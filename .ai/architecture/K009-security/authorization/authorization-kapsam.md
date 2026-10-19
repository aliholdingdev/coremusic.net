---
node_id: K009-security/authorization/kapsam
node_type: module
status: verify-required
parent_id: K009-security/authorization
children: []
depends_on:
  - K009-security/authorization
evidence:
  - "PermissionMiddleware.php satır 17-63 (rol + permission kontrolü)"
  - "OriginCheckMiddleware.php satır 34-121 (origin allowlist, fail-closed)"
  - "CorsMiddleware.php satır 48-142 (header yayımı, wildcard yok)"
  - "Api/Middleware/AuthorizationMiddleware.php satır 25-110 (public rota + 401/403)"
validation:
  - "2026-10-12 — her §1-§8 iddiası satır aralığıyla dosyaya bağlandı"
open_questions:
  - "Rol matrisinin tek kaynağı (config vs route meta) ADR-097 K4'te kesinleşmedi"
group: mimari
---

# authorization-kapsam.md — Yetki Kontrolü Kapsamı

## §1 Sınır Tanımı

authorization, **doğrulanmış kimliğin** bir kaynak/eklem üzerinde işlem yapmasına
izin verip vermediğini kararlaşturan tüm kontrolleri kapsar. Girdi: kimlik
(authentication çıktısı). Çıktı: geç / 401 / 403 / origin reddi.

## §2 İki Ayrı Yetki Motoru (kanıt)

| Motor | Dosya | Pipeline | Karar |
|---|---|---|---|
| Web (sayfa) | PermissionMiddleware.php | #9 | role eşitliği + permission üyeliği (satır 31, 39) |
| API | Api/AuthorizationMiddleware.php | ADR-084 | in_array strict ile role/permission (satır 64, 77) |

Ortak özellik: **ikisi de strict karşılaştırma** kullanır (=== / in_array(..., true));
tip gizleme (juggling) yoktur.

## §3 Web Yetki Akışı (PermissionMiddleware)

~~~text
Request
  → [9] PermissionMiddleware
      ├─ _auth['userId'] boş     → $next()  (satır 23-25: yetkisiz değil, kimliksiz)
      ├─ requiredRole var, uyuşmuyor → 403 role_insufficient (satır 30-33)
      ├─ requiredPermission yok listede → 403 permission_denied (satır 36-41)
      └─ uygun → $next() (satır 44)
~~~

**Kritik nüans (satır 23-25):** kimlik bilgisi yoksa middleware **sessizce geçer** —
çünkü koruma AuthMiddleware'in işidir. Bu, "izin ver" değil "benim alanım değil"
demektir; zincirde AuthMiddleware'in ÖNÜNDE olması gerekir (frozen sıra, satır 13).

## §4 API Yetki Akışı (AuthorizationMiddleware)

1. 404/405 ise RBAC **uygulanmaz** (satır 29-34) — handler çalışmayacak.
2. Public rota ise auth zorunlu değildir (satır 38-40); yoksa login/register
   401 ile kilitlenirdi.
3. _auth_user null → 401 UNAUTHORIZED (satır 43-50).
4. requiredRole uyuşmuyor → 403 FORBIDDEN (satır 61-71).
5. requiredPermission yok → 403 FORBIDDEN (satır 74-84).

Public rota tespiti iki katmandır: route['public'] bayrağı, ardından
PUBLIC_ROUTE_PATTERNS üzerinde str_starts_with (satır 94-110).

## §5 401 ile 403 Ayrımı (kural)

| Durum | Kod | Anlamı |
|---|---|---|
| Kimlik yok / token geçersiz | 401 | authentication/children'ı |
| Kimlik var, yetki yok | 403 | bu düğüm |
| Origin izinsiz | 403 origin_not_allowed | OriginCheck satır 54-60 |

Kural: 401 → "giriş yap", 403 → "bu senin işin değil"; 403'te kullanıcı adı
veya rol listesi sızdırılmaz (yanıt yalnız error kodu üretir, satır 56-62).

## §6 Kaynak (Origin) Politikası — İki Middleware

**OriginCheck (Pipeline #1):**
- Origin başlığı yoksa devam (satır 39-41) — same-origin istekler etkilenmez.
- İzin verilmiyorsa 403 + securityEvent('origin_blocked') (satır 47-60).
- Suffix eşleşmesi bilinçli KAPALI (satır 72-76): apex'e bağlı her alt alan adı
  credentialed origin olarak reflekt edilemezdi (subdomain takeover riski).
- '*.example.com' biçiminde AÇIKÇA yazılıyorsa alt alan kabulü vardır (satır 95-102).
- Production'da dev fallback YOKTUR → fail-closed (satır 108-111).

**Cors (Pipeline #2):**
- OriginCheck geçmişse başlıklar yayılır; allowlist dışına başlık verilmez
  (satır 63-67).
- ACAO = isteğin origin'i (wildcard '*' YOK, satır 63-64, 70).
- allow_credentials varsayılan true (satır 44) → wildcard'la birlikte KULLANILAMAZ.
- Preflight OPTIONS → 204 halt (satır 84-93).
- allowed_headers varsayılanı Authorization ve X-Api-Key içerir (satır 37-43);
  düşürülürse tarayıcıdan API istekleri 401 döner.

## §7 Kapsam Dışı (bu düğüm DEĞİL)

1. Parola/argon2id/JWT → authentication/
2. Session sabitleme, regenerate → session-security/
3. Sıklık/brute-force → rate-limiting/
4. CSRF token üretimi → K009 kök kod-ornekleri.md
5. CORS başlık şifreleme/tls → encryption/

## §8 Kabul Kriterleri

1. Her 403/401 yolu bir satır aralığıyla kanıtlanmış olmalı (§2-§5).
2. Fail-closed davranışı her yüzeyde doğrulanmalı: origin (satır 108-111),
   public rota yalnız route['public'] veya açık pattern (satır 94-110).
3. Loglama zorunlu: authz_denied (Permission satır 50-54) ve origin_blocked
   (OriginCheck satır 48-52) — sessiz reddi yasak (P5-32).
4. Tahmin/dolgu yasak: kanıtı olmayan iddia UNKNOWN işaretlenir.

## §9 İlişkili Düğümler ve Devir

| Konu | Giden düğüm | Gerekçe |
|---|---|---|
| 401 kaynakları (token süresi, kimlik yok) | authentication/ | kimlik doğrulama sorunu |
| Cookie bayrakları, session sabitleme | session-security/ | oturum güvenliği |
| 429/brute-force | rate-limiting/ | sıklık kontrolü |
| Parola sızıntısı | secrets/ | anahtar/pepper yönetimi |
| RBAC reddinin loglanması | audit (dalga-3) | olay kaydı akışı |

Devir kuralı: bu düğüm yalnız **karar** verir (geç / reddet); neden
reddedildiğinin kaydı Permission satır 50-54 ve OriginCheck satır 48-52 ile
audit'e hazır üretilir.

## §10 Özet Karar Tablosu

| # | Karar | Kanıt | Durum |
|---|---|---|---|
| A-01 | RBAC: role eşitliği + permission üyeliği | Permission satır 31, 39 | aktif |
| A-02 | Kimlik yoksa Permission pas geçer | Permission satır 23-25 | aktif |
| A-03 | API'de public rota auth'suz | ApiAuth satır 38-40 | aktif |
| A-04 | 404/405'te RBAC uygulanmaz | ApiAuth satır 29-34 | aktif |
| A-05 | Origin allowlist, wildcard yok | Cors satır 63-64 | aktif |
| A-06 | Suffix eşleşmesi kapalı | OriginCheck satır 72-76 | aktif |
| A-07 | Prod'da dev fallback yok (fail-closed) | OriginCheck satır 108-111 | aktif |
| A-08 | Reddi loglamak zorunlu (P5-32) | Permission satır 49-54 | aktif |

## §11 YETKİLENDİRME HATA SINIFLARI VE FAIL-CLOSED MATRİSİ (2026-10-18 — APPEND-ONLY)

> §1-§10 silinmez; A-01..A-08 tablosunun derinlik zeminidir. Yeni kod kanıtı yok — sentez.

### 11.1 401 vs 403 vs 404 (yanlış seçim sızıntı yaratır)

| Kod | Ne zaman | Ne sızdırmaz | K009 kanıtı |
|---|---|---|---|
| 401 | kimlik kanıtı YOK/geçersiz (token süresi, cookie yok) | "bu kaynak var" bilgisi | authentication devri |
| 403 | kimlik VAR ama yetki YOK | hangi rolün geçtiği bilgisi (yanıt gövdesi genel) | Permission satır 49-54 (log + reddet) |
| 404 | var olmayan KAYNAK | varlığı bilinen kaynağın yok sayılması (IDOR gizleme) | A-04: 404/405'te RBAC uygulanmaz |
| 405 | yanlış HTTP metodu | — (Allow header) | route katmanı |

| Kural | Açıklama |
|---|---|
| Sızıntı önleme | yetkisiz kullanıcıya 403 + 404 AYRI davranış gösterirse IDOR keşfi kolaylaşır; A-04 kararı bu yüzden önemli |
| Gövde sızıntısı | 403 gövdesinde "role: admin gerekli" yazmak yetki haritası sızdırır — genel mesaj |
| Log zorunluluğu | her reddetme olayı üretilir (P5-32); sessiz reddetme A09 ihlali |

### 11.2 IDOR / Nesne-Erişim Matrisi (A01 derinliği)

| Yüzey | Saldırı | Savunma | Durum |
|---|---|---|---|
| `/user?id=123` → `?id=124` | IDOR (düz nesne referansı) | rol/permission + sahiplik denetimi | Permission üyeliği A-01; sahiplik (owner) denetimi kanıtı YOK ⚠️ |
| UUID ile tahmin | zayıf random | UUIDv7 özel implementasyonu | §7 istisna 4 ⚠️ |
| Bulk endpoint (çoklu ID) | parça başına yetki atlanır | parça bazında denetim | kanıt yok ⚠️ |
| API key → nesne | anahtarın kapsamı dışına erişim | key scope tanımı | ApiKeyRepository·171; scope kanıtı ⚠️ |

### 11.3 Origin/CORS Fail-Closed Matrisi (A-05..A-07 derinliği)

| Durum | Davranış | Neden fail-safe |
|---|---|---|
| Origin allowlist'te | geç | — |
| Origin yoksa (same-origin GET) | geç (gerekli davranış) | same-origin'de Origin bazen yok |
| Origin allowlist'te DEĞİL | reddet + origin_blocked log (OriginCheck·48-52) | A-05 |
| suffix eşleşmesi (ör. evil-origin.com ile trusted.com) | KAPALI (·72-76) | suffix = bypass |
| prod'da dev fallback | YOK (·108-111) | A-07; fallback = serbest CORS |
| Origin doğrulanamıyor (parse hatası) | reddet | fail-open yasak (kapsam §21) |

### 11.4 Yetki Karar Adımları (RBAC sorgu zinciri)

| Adım | Kontrol | Aşama |
|---|---|---|
| 1 | Route public mi? (satır 94-110) | evet → authsuz geç (A-03) |
| 2 | Kimlik var mı? | yok → 401 (authentication devri) |
| 3 | Rol eşitliği (satır 31) | uyuşmazlık → 403 + log |
| 4 | Permission üyeliği (satır 39) | üye değil → 403 + log |
| 5 | Nesne sahipliği (owner) | ⚠️ kanıt yok — dalga-3 |
| 6 | Karar + olay | reddetme her durumda olay üretir (A-08) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**
