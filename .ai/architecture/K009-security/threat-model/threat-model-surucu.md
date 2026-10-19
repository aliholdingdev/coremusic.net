---
node_id: "K009-security/threat-model/threat-model-surucu"
node_type: "module"
status: "draft"
parent_id: "K009-security"
children: []
depends_on: []
evidence:
  - ".ai/architecture/K009-security/threat-model/threat-model-anayasa.md (§3 sınıf · §4 skor · §5 zincir · §8 ADR kapısı)"
  - ".ai/architecture/K009-security/security-riskler.md:36-49 (R-01..R-14 — gerçek risk kayıtları)"
  - ".ai/.decisions/accepted/ADR-010-csrf-protection-strategy.md (Senaryo 1 kararı)"
  - ".ai/.decisions/accepted/ADR-008-bypass-auth-middleware.md (Senaryo 2 kapsam kuralı)"
  - ".ai/.decisions/accepted/ADR-013-rate-limiting-apcu.md (Senaryo 3 kararı)"
validation: "Draft — 4 senaryo gerçek Dosya·Satır + gerçek R/ADR ile sürülüyor; 5 dal · 3 bulgu bağı · 11 hata kolu + kurtarma adımları · 18 satır kanıt konsolidasyonu anayasa §5/§6/§8 ile hizalı"
open_questions: []
group: "mimari"
---

# K009 · threat-model · Sürücü

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Sürücü: Tehdit → Risk → Kontrol Akışı, Senaryolar ve Hata Kolları

---

## §1 SÜRÜCÜNÜN GÖREVİ

Anayasa **neyin** kurallar olduğunu söyler (sınıf, skor, zincir, kapı); bu dosya **nasıl sürülüğünü** gösterir:
bir tetikleyici geldiğinde elde anayasa adımlarıyla yapılacak işin sırası, üç gerçek senaryoda bu akışın kanıtlanmış
hâli ve akışın herhangi bir adımında koptuğunda gidilecek hata kolu.

**Sahiplik:** akışı security-engineer sürer; onay Vault Steward'dır (anayasa §7). Bu dosya kontrol **uygulamaz** —
uygulayan K009 çocuklarıdır (authentication, session-security, rate-limiting, ...); sürücü yalnızca sırayı ve
karar noktalarını bağlar.

---

## §2 ANA AKIŞ (7 adım — her tetikleyicide aynı sıra)

```text
[Tetikleyici: anayasa §7]
  → A1 Yüzey tespiti     : hangi Dosya/endpoint/config değişiyor?
  → A2 Sınıf atama       : §3 (S/T/R/I/D/E) veya §3.2 (7-12)
  → A3 Varlık + etki     : §11 varlık envanteri + §4.1 puan (1-5)
  → A4 Olasılık + kanıt  : §4.2 puan (1-5); kanıtsız olasılık ≥3 (§4 kuralı)
  → A5 Skor + bant       : etki × olasılık → §4.4 bant (P0/P1/P2/İzle)
  → A6 Kontrol ataması   : §13 eşleme → K009 çocuğu + mekanizma
  → A7 Kanıt + durum     : Dosya·Satır (Z-5) → AÇIK / AZALTILMIŞ / KABUL / ⚠️
```

### §2a Adım detayları ve kapılar

| Adım | Soru | Çıktı | Kapı (cevap yoksa) |
|---|---|---|---|
| A1 | Değişen yüzey hangi Dosya? | Dosya·Satır listesi | Yüzey yoksa tehdit yok → akış biter |
| A2 | Sınıf: STRIDE mi, §3.2 mi? | S/T/R/I/D/E veya 7-12 | Sınıf belirsiz → **DUR** (hata kolu H-1) |
| A3 | Hangi varlık (V-x) etkileniyor? | V-x + etki puanı | Varlık yoksa etki uydurulur → **BLOCK** (H-3) |
| A4 | Olasılık kanıta bağlı mı? | puan + kanıt durumu | Kanıtsızsa puan asla <3 (§4) |
| A5 | Skor hangi bantta? | 1-25 → bant | 15-25 (P0) → §8 ADR kapısı zorunlu (T-07) |
| A6 | §13 eşlemesinde çocuğu var mı? | çocuk adı + mekanizma | Çocuk yoksa → §8 seçenekleri (H-4) |
| A7 | Kanıt Dosya·Satır mı? | durum satırı | Değilse `⚠️ KANIT YOK`; AZALTILMIŞ **yazılamaz** (Z-5) |

### §2b Akış sonrası (kontrol uygulandıktan sonra)

| # | İşlem | Yer |
|---|---|---|
| B-1 | Kontrol uygulanır | K009 çocuğu (bu dosya DEĞİL) |
| B-2 | Kanıt satırı güncellenir + doğrulama satırı açılır | `threat-model-dogrulama.md` AD-xx |
| B-3 | Durum `AÇIK` → `AZALTILMIŞ` (yalnız kanıtla) | anayasa Z-5 / T-04 |
| B-4 | Kabul/devret/azalt kararı yazılır | §8 + ADR (kabul ise) |
| B-5 | Tetikleyici tekrarlı mı kontrol edilir | anayasa §7 (T-12: kabullenme = durum, kapanış değil) |

### §2c Akışın beş dalı (ayrışma A6-A7'dedir)

Aynı akış A1-A5 adımlarını her senaryoda aynı sürdürür; ayrışma A6-A7'de olur. Beş dal ve vault'taki karşılıkları:

| # | Dal | Koşul | A6-A7 çıktısı | Bu vault'taki örnek | Zorunlu alan |
|---|---|---|---|---|---|
| D1 | Azaltma | kontrol var + kanıt Dosya·Satır | `AZALTILMIŞ` | S1 (Ör-03 · AD-01), S2 (Ör-04 · AD-04) | doğrulama AD-xx |
| D2 | Kabul + ADR | dayanıklılık eksiği **bilinçli** | `KABUL` + ADR dosyası | S3b → ADR-013 (R-13) | ADR adı + R satırı |
| D3 | Kanıt yok | kontrol kodda var ama gözlem/günlük yok | `⚠️ KANIT YOK` | S6 → B-1 (`OriginCheckMiddleware.php:43`) | bulgu B-xx |
| D4 | Kapı | skor 15-25 veya fail-open "geçici" diye | **BLOCK — üst onay + ADR** | H-6 / H-7 (T-07) | üst onay kaydı |
| D5 | Devir | yüzey K009 dışı (K010/K000/K014) | **devir** — Vault Steward onayı | H-8 (T-08) | devir kaydı |

Dallar sabit değildir; geçişler §2b adımlarından geçilmeden yapılamaz:

- D3 → D1: gözlem kanıtı (ör. `securityEvent`) eklenip Dosya·Satır okunduğunda — B-1 için §8'deki "azalt" seçeneği tam olarak budur.
- D2 → A1: tetikleyici tekrarladığında (anayasa T-12) — kabul durumu yeniden sürülür, kalıcı kapanış değildir.
- Her geçişte kanıt satırı güncellenir; "durum değişti" cümlesi tek başına geçerli bir geçiş değildir.

**Dal kanıtları:**

- **D1 → S1:** `CsrfMiddleware.php:42` (`hash_equals`) + `:49-63` (403 `csrf_invalid` + securityEvent) okundu → Ör-03 · AD-01 → `AZALTILMIŞ`.
- **D2 → S3b:** `RateLimiterMiddleware.php:66-68` (genel yollar fail-open) + `ADR-013-rate-limiting-apcu` kararı → `KABUL`, kapı geçilmiş.
- **D3 → S6:** `OriginCheckMiddleware.php:43` ihlalde 403 döner ama satırda log/securityEvent **yok** → gözlem üretilmiyor → `⚠️ KANIT YOK`. "Kontrol var" cümlesi D1'e gerekçe olmaz (anayasa Z-5 / T-04).

### §2d Sürüş öncesi ön kontrol (pre-flight — A1'den önce)

1. Tetikleyici anayasa §7'ye göre yazıldı mı? — yoksa akış başlamaz (T-01).
2. Hedef dosya diskte mevcut mu ve yüzey K009'da mı? — değilse H-8 devri.
3. İlgili risk satırı (R-xx) var mı? — yoksa §8'de "kayıt aç" seçeneği zorunlu (uydurma R numarası yazılmaz).
4. ADR adı diskte gerçek mi (ADR-008/010/011/012/013/014/020/022/024)? — uydurma ADR adı T-11 / Y-03'tür.
5. Kanıt Dosya·Satır bugün okunabilir durumda mı (okuma: 2026-10-15)? — okunmayan dosya kanıt sayılmaz (Z-5).

"Evetsiz" madde varsa akış A1'de durur; A6'ya (kontrol ataması) ilerlenmez. Pre-flight sonucu senaryo tablosuna ek satır olarak yazılmaz — yazılan tek şey varsa `⚠️` işaretidir.

---

## §3 SENARYO 1 — CSRF (sınıf T · R-04/R-14 · ADR-010)

> Senaryo: saldırgan üçüncü bir sayfadan state-changing POST gönderir; token'ı yoktur veya tahmin etmeye çalışır.

| Adım | Uygulama | Kanıt |
|---|---|---|
| A1 yüzey | `shared/src/Middleware/CsrfMiddleware.php` + state-changing POST uçları | Dosya mevcut (2026-10-15 okundu) |
| A2 sınıf | T (Tampering — CSRF) + E (yetki artışı potansiyeli) | anayasa §3 satır 2 |
| A3 varlık/etki | V-5 CSRF token (session) · etki **4** (state-changing istek sahte oturumla) | anayasa §4.1, §11 |
| A4 olasılık | **4** — varsayılan tarayıcı davranışı cross-origin form POST'a izin verir; kanıt: risk defteri R-04 | security-riskler.md:39 |
| A5 skor | 4 × 4 = **16 → Kritik / P0** (kontrol öncesi) | anayasa §8.2 skor kalıbı |
| A6 kontrol | `session-security` + ADR-010: double-submit token + safe-method ayrımı + hash_equals | anayasa §13, ADR-010 |
| A7 kanıt | `CsrfMiddleware.php:24` (safe-method dışı her şey doğrulanır) · `:42` (`hash_equals` — sabit-zamanlı) · `:49-63` (ihlal → 403 `csrf_invalid` + securityEvent) · bypass köprüsü `:15-17` **kaldırıldı** | ornekler Ör-03, dogrulama AD-01 |
| durum | **AZALTILMIŞ** (R-04) — kanıt Dosya·Satır ✓ | security-riskler.md:39 |

**Sürüş notları:**

- Aynı akış R-14'ü de kapatır (CSRF bypass + rol karşılaştırma zaafı — `CsrfMiddleware·12-17` bypass kaldırma; `PermissionMiddleware·15-45` strict in_array; security-riskler.md:49).
- ADR-010 katman-2 (SameSite) uygulaması **Lax** olarak kanıtlandı (Session/CLAUDE.md:49) — bu, "Strict" beklentisi varsa bir **fark kaydıdır**: dogrulama §4 B-3'e bağlanır, sessizce geçilmez.
- OAuth callback/CSRF-Nonce açık sorusu ayrı kayıtta durur (threat-model/index.md §5); o soru kapanana kadar bu senaryo "tam kapandı" olarak işaretlenmez.

---

## §4 SENARYO 2 — BypassAuth (sınıf S+E · R-01 · ADR-008)

> Senaryo: debug/development bypass kapısı prod'a açık gelirse veya whitelist yanlış URL ile auth'ı atlar.

| Adım | Uygulama | Kanıt |
|---|---|---|
| A1 yüzey | `shared/src/Middleware/BypassAuthMiddleware.php` + bypass flag/config | Dosya mevcut (2026-10-15 okundu) |
| A2 sınıf | S (Spoofing — sahte kimlik) + E (Elevation) | anayasa §3 satır 1, 6 |
| A3 varlık/etki | V-6 bypass credential sabitleri · etki **5** (kimlik tamamen atlanır → tüm oturumlar) | anayasa §5.1, §11 |
| A4 olasılık | **4** (config hatası varsayılanı) — kanıt: R-01 **AÇIK (test yok)** | security-riskler.md:36 |
| A5 skor | 5 × 4 = **20 → Kritik / P0** | anayasa §5.1 zincir örneği |
| A6 kontrol | `authentication` çocuğu: fail-closed credential + kod inceleme; ADR-008 kapsam kuralı | anayasa §13, ADR-008 |
| A7 kanıt | `BypassAuthMiddleware.php:29-31` (3 sabitten biri boşsa `[]` — reddet) · `:42-44` (header yoksa "dev bypass yok") · `:49-54` (her kullanımda securityEvent) · canlı log: `shared/logs/app.log` `DEV BACC 006` (2026-10-09) | ornekler Ör-04, dogrulama AD-04/AD-09 |
| durum | **KISMİ — R-01 hâlâ AÇIK** (test yok); fail-closed kodu var ama kapsam genişlemesi yasağı ADR-008 ile korunuyor | security-riskler.md:36 |

**Sürüş notları:**

- Kapsam genişletme yasağı bağlayıcıdır: `shared/AGENTS.md` §4 Yasak-2 — "BypassAuthMiddleware kapsam genişletme (ADR-008 kapsamı ADR ile değişir)"; anayasa §8.1 bu değişikliği **ADR zorunlu** sınıfına koyar.
- Bileşen envanteri `K009-security/authentication/authentication-bilesenler.md`'de bağlanır (bilesenler D-06).
- Kanıt canlıdır (log satırı) — ama "test yok" açık kalır: test yazımı `security-testing` çocuğunun işidir; bu senaryo onu **tetikler**, kendisi kapatmaz.

---

## §5 SENARYO 3 — Rate-limit yoksayma / fail-open (sınıf D · R-12/R-13 · ADR-013)

> Senaryo: (a) saldırgan auth uçlarını brute-force'lar; (b) rate-limiter servisi (APCu cache) çökerse istekler
> kontrolsüz geçer.

| Adım | Uygulama | Kanıt |
|---|---|---|
| A1 yüzey | `shared/src/Middleware/RateLimiterMiddleware.php` + auth uçları (`/api/auth/login`, `/api/auth/otp`, ...) | Dosya mevcut (2026-10-15 okundu) |
| A2 sınıf | D (Denial of Service — brute-force/credential stuffing) | anayasa §3 satır 5 |
| A3 varlık/etki | V-2 parola hash/pepper · etki **4-5** (hesap ele geçirme zinciri) | anayasa §11 |
| A4 olasılık | (a) **4** — standart araçla denenebilir (R-12 kanıtlanmış: 60/60 + fail-closed); (b) cache çöküşü olasılığı **3** (R-13, KABUL) | security-riskler.md:47-48 |
| A5 skor | (a) 4 × 4 = **16 → P0** (kontrol öncesi); (b) bilinçli kabul — §8.1 ADR zorunlu | anayasa §4.4, §8.1 |
| A6 kontrol | `rate-limiting` çocuğu: 60 istek/60 sn + **AUTH_FAIL_CLOSED_PATHS** (ADR-013 §5.4 şart 1a) | anayasa §13, ADR-013 |
| A7 kanıt | `RateLimiterMiddleware.php:20-30` (`AUTH_FAIL_CLOSED_PATHS` listesi) · `:53-65` (cache çözülemezse auth yollarında **503** — istek geçmez) · `:86-99` (ihlal → 429 + `RATE 005` securityEvent) · genel yollar `:66-68` fail-open (ADR-013 §3 — hizmet sürekliliği) | ornekler Ör-05, dogrulama AD-05 |
| durum | (a) **AZALTILMIŞ** (R-12) · (b) **KABUL (bilinçli)** — gerekçe: hizmet sürekliliği; ADR-013 | security-riskler.md:47-48 |

**Sürüş notları:**

- R-13'ün "kabul" olması kapanış değildir (anayasa T-12): §7 tetikleyici 7 (config değişikliği — bypass/allowlist/proxy) gerçekleşirse senaryo **yeniden sürülür**.
- Genel (auth-dışı) yolların fail-open'ı bilinçli ve kayıtlıdır; auth yolları asla fail-open olamaz — bu çizgi ADR-013 §5.4'te yazılıdır ve anayasa §8.1'de "fail-open bilinçliyse ADR" maddesine bağlanır.

---

## §6 SENARYO 4 — OriginCheck fail-open dalı (sınıf 10 · R bağı YOK → §8)

> Senaryo: origin header'ı gelmediğinde ve dev-fallback flag'i açıksa istek origin kontrolünden geçer.
> Bu senaryo, akışın "kontrol var ama kanıt/RC eksik" dalını gösterir.

| Adım | Uygulama | Kanıt |
|---|---|---|
| A1 yüzey | `shared/src/Middleware/OriginCheckMiddleware.php:36-43` (empty-origin + flag dali) | ornekler Ör-01 |
| A2 sınıf | 10 (Yanlış yapılandırma — misconfiguration) + D/E etkisi | anayasa §3.2 satır 10 |
| A3 etki | 5'e kadar (auth'lu uçlara geçiş) | anayasa §4.1 |
| A4 olasılık | flag prod'da default kapalı → olasılık **2**; kanıt: `:109-111` "prod'da fallback asla açık gelmez" | dogrulama AD-02 |
| A5 skor | 5 × 2 = **10 → Yüksek / P1** (prod varsayımıyla) | anayasa §4.4 |
| A6 kontrol | env-gated flag (`origin_check_dev_fallback`) + prod default false | ornekler Ör-01 |
| A7 kanıt | `:36-43` fail-open dali · `:109-111` prod kapalı; **ama** origin-mismatch 403'ünde log/securityEvent **YOK** (`:43`) | dogrulama §4 B-1 |
| durum | **⚠️ KANIT YOK (kısmi)** — kontrol mekanizması var, gözlemlenebilirlik eksik; R kaydı YOK → anayasa §8: azalt (securityEvent ekle) veya kayıt aç | dogrulama §4 B-1 |

### §6a Doğrulama defterindeki üç bulgu bu akışa nasıl bağlanır

`threat-model-dogrulama.md` §4'teki B-1/B-2/B-3 bulguları akış dışında işlemez; her biri A3-A7 adımlarına girer:

| Bulgu | Kanıt | İlgili senaryo | Akışta nereye girer | Çıktı |
|---|---|---|---|---|
| B-1 origin-mismatch 403 günlüksüz | `OriginCheckMiddleware.php:43` (403 döner; satırda log/securityEvent yok) | S6 | A7 (gözlem kanıtı) | `⚠️ KANIT YOK` + §8: azalt (securityEvent ekle) veya R kaydı aç |
| B-2 HSTS başlığı yok | `SecurityHeadersMiddleware.php:26-41` (10 başlık: X-Content-Type, X-Frame, X-XSS, Referrer-Policy, CSP, Require-Trusted-Types, CORP, Permissions-Policy, X-Permitted + `:38-41` yalnız HTTPS'te COOP/COEP — `Strict-Transport-Security` **yok**) · `shared/src/Middleware/CLAUDE.md:42` "PLANNED" | S4 (başlık eksiği sınıfı) | A3-A4 (etki/olasılık) | risk defterinde ayrı R satırı **YOK** → §8: kayıt aç veya devret; B-2 kaydı açık kalır |
| B-3 SameSite Lax/ADR farkı | `shared/src/Session/CLAUDE.md:49` + `shared/src/Middleware/CLAUDE.md:102` = **Lax** (ADR-010 katman-2) | S1 (katman-2 savunma) | A6-A7 (kontrol tanımı notu) | fark kaydı — `AZALTILMIŞ` bozulmaz; metinde "Lax" yazılır, "Strict" yazılmaz |

**B-2 akıştaki yeri ayrı dikkat ister:** sorun "eksik kanıt" değil, **eksik kontrol**dür — başlık kodda üretilmiyor (dosyada 10 başlık var, HSTS yoktur). Bu yüzden B-2 için D3 (kanıt yok) değil, kontrol ataması (A6) gerekir: atama yoksa durum `⚠️` değil `AÇIK` kalır. Kanıtı eksik olan şey kontrolün kendisidir; "başlık yok" cümlesi Dosya·Satır kanıtıyla (`.php:26-41`) **desteklenmiştir**, yani bulgu kesindir.

**B-3 skoru değiştirmez:** Lax, Strict'ten daha gevşek bir katman-2'dir ama ADR-010'un seçtiği ve kodda kanıtlanan değerdir. İtiraz (Strict isteği) varsa bu bir skor/olabilirlik revizyonu (T-03) değil, **ADR revizyonudur** — kapı ADR-010'dur; anayasa §8.1'den geçilmeden "uygulama değiştirildi" yazılamaz.

---

## §7 HATA KOLLARI (akış nerede koparsa nereye)

| # | Kopma noktası | Hata kolu | Yaptırım |
|---|---|---|---|
| H-1 | Sınıf belirsiz (STRIDE mi, §3.2 mi?) | **DUR** — anayasa §10 soru 2'ye dön; sınıf yazılmadan skor yazılmaz | vault review BLOCK |
| H-2 | Tetikleyici yok ama yeni yüzey var | §7 zorunlu — modele alınmadan üretemez (T-01) | kod incelemesinde RED |
| H-3 | Varlık/etki uyduruluyor | §11 envanterine dön; yoksa yeni V-x aç | anayasa Z-2 (uydurma yasak) |
| H-4 | Kontrol çocuğu yok (§13'te eşleşme boş) | §8 seçenekleri: azalt / devret / **kabul (ADR)** / yok et | T-05: sessiz geçilmez |
| H-5 | Kanıt Dosya·Satır değil (test/doküman iddiası) | `⚠️ KANIT YOK` yaz; AZALTILMIŞ **yazılamaz** | Z-5 / T-04 / Y-02 |
| H-6 | Skor 15-25 ve kapatılmak isteniyor | §8.1: **üst onay + ADR** zorunlu | T-07 / Y-04 |
| H-7 | Fail-open "geçici" diye kayıt dışı | Y-05 — bilinçliyse ADR (ör. R-13 → ADR-013) | BLOCK |
| H-8 | Karar K009 dışında domene yazılıyor (K010/K000/K014) | T-08: devir — Vault Steward onayı | Layer violation → revert |
| H-9 | Skorla oynama (olasılığı düşürme) | T-03: skor değişikliği ayrı karar; gerekçesiz revizyon RED | revizyon RED |
| H-10 | Aynı tetikleyici tekrar gerçekleşti | §7 + T-12: kabullenme yeniden açılır; durum `KABUL` → yeniden değerlendirilir | gözden geçirme zorunlu |
| H-11 | Uydurma Dosya·Satır / CVE / URL tespit edildi | T-11 + Y-03: kayıt silinir, doğrulanmış kanıtla yeniden yazılır | vault kalite kapısı |

### §7a Hata kolu → doğru dosya eşlemesi

| Kopma | Git |
|---|---|
| Sınıf/skor/ADR kapısı tartışması | `threat-model-anayasa.md` §3-§8 |
| Risk kaydı/defter güncellemesi | `security-riskler.md` (append — R-15+ için) |
| Kontrol uygulaması | ilgili K009 çocuğu (authentication, rate-limiting, session-security, ...) |
| Kanıt/doğrulama satırı | `threat-model-dogrulama.md` AD-xx |
| Yüzey kanıtı ekleme | `threat-model-ornekler.md` Ör-xx |
| Kapsam sınırı tartışması | `threat-model-kapsam.md` §3 (S-x matrisi) |

### §7b Her kopmada zorunlu kurtarma adımı (sıra değişmez)

| Kopma | 1. adım | 2. adım | Sorumlu |
|---|---|---|---|
| H-1 | anayasa §3'e dön, sınıfı yeniden yaz | adımları baştan sür | security-engineer |
| H-2 | tetikleyiciyi anayasa §7 tanımına göre yaz | yazılmadan yüzeyi modele alma | security-engineer |
| H-3 | anayasa §11 varlık envanterini oku | eş yoksa yeni V-x aç (uydurma yok) | security-engineer |
| H-4 | §8 seçeneklerinden birini seç (azalt / devret / kabul / yok et) | kabulde ADR zorunlu | security-engineer + Vault Steward |
| H-5 | `⚠️ KANIT YOK` yaz | kanıtı Dosya·Satır'a çevir, sonra yeniden durumla | security-engineer |
| H-6 | skoru değiştirme — bırak | üst onay + ADR başlat | Vault Steward |
| H-7 | fail-open'ı ADR'ye taşı | ADR yoksa kayıt BLOCK | Vault Steward |
| H-8 | yüzeyi sahibi domene devret | onay bekle (T-08) | Vault Steward |
| H-9 | skor değişikliğini ayrı karar yap | gerekçe + kanıt yaz | security-engineer |
| H-10 | tetikleyiciyi anayasa §7'ye işle | durumu `KABUL`'den yeniden A1'e al | security-engineer |
| H-11 | kaydı sil | doğrulanmış kanıtla yeniden yaz | Vault Steward |

Kurtarma adımı atlanıp doğrudan durum satırına geçilemez: §7'nin kolu zaten "X yapılmadan Y yazılamaz" anlamındadır. Aynı kol **üç kez** üst üste koparsa akış durur — dördüncü turda şüpheli varsayım yazılmaz, üst karar istenir (anayasa T-11 ile uyumlu: uydurma kanıt tespit edilirse kayıt silinir, tahminle onarılmaz).

---

## §8 SÜRÜM NOTLARI

| Alan | Değer |
|---|---|
| Kapsam | 4 senaryo (3 kabul edilmiş kontrol + 1 kısmi/bulgu) + 11 hata kolu + 5 dal + 3 bulgu bağı + 18 kanıt satırı |
| Bağımlılık | anayasa (kurallar) → surucu (akış) → ornekler/dogrulama (kanıt) |
| Değişmez | kontrol uygulaması bu dosyada yapılmaz; kararlar §8 ADR kapısından geçer |
| Revizyon | yeni senaryo = yeni Ör-xx + AD-xx ile birlikte |
| Ek yapı | §2c beş dal · §2d pre-flight · §6a üç bulgu bağı · §7b kurtarma adımları · §9 durum-karar · §10 kanıt konsolidasyonu (18 satır) |

## §9 DURUM → KARAR KISAYOLU (tek satırda)

| Durum | Karar | Zorunlu alan | Yasak |
|---|---|---|---|
| `AÇIK` | kontrol yok → azalt / devret / kabul / yok et seç | A6 ataması | olasılığı düşürerek skoru kapatmak (T-03) |
| `AZALTILMIŞ` | kanıt + AD-xx var | Dosya·Satır + doğrulama satırı | kanıtsız kapatma (Z-5 / T-04) |
| `KABUL` | dayanıklılık eksiği bilinçli | ADR dosya adı + R satırı | ADR'siz kabul (T-07 / H-7) |
| `⚠️ KANIT YOK` | gözlem ekle → sonra durumla | bulgu B-xx | "kontrol var" cümlesiyle kapatmak |
| `KISMİ` | açık kalan kalem görünür kalsın | hangi kısım açık | bütünü azaltılmış göstermek |
| `DEVİR` | K009 dışı yüzey | Vault Steward onayı | kendi başına karar (T-08) |

Bu tablo anayasa §8-§12 ile çelişirse **anayasa kazanır** — kısayol yalnız hatırlatmadır. Her durum satırı üç soruyla sınanır: (1) kanıt Dosya·Satır mı? (2) kapı (ADR) gerekiyorsa geçildi mi? (3) doğrulama defterine AD-xx açıldı mı? Üçü de "evet" olmadan durum satırı değiştirilemez.

**Bu vault'taki karşılıkları:** R-01 `AÇIK` (test yok — security-riskler.md:36) · R-06 `KISMİ` · R-12 `AZALTILMIŞ` (60/60 + fail-closed) · R-13 `KABUL` → ADR-013 · B-1 `⚠️ KANIT YOK` · B-3 fark kaydı (senaryoya bağlı, durum değiştirmez).

### §9a Durum satırı yazım şablonu (kopyala-kullan — alan sırası değişmez)

```text
[durum] sınıf=<S|T|R|I|D|E|7..12> risk=<R-xx | yok> skor=<etki>×<olasılık>=<1-25>/<bant>
        kanıt=<dosya: satır | ⚠️ KANIT YOK> doğrulama=<AD-xx | —> adr=<ADR-xxx | —>
        karar=<azalt | devret | kabul | yok et> gerekçe=<tek cümle, kanıta dayalı>
```

Doldurulmuş örnek 1 — S3b (kabul + ADR):

```text
[KABUL]  sınıf=D risk=R-13 skor=5×3=15/P0 (kabul öncesi)
         kanıt=RateLimiterMiddleware: 66-68 doğrulama=AD-05 adr=ADR-013-rate-limiting-apcu
         karar=kabul gerekçe=genel yollar fail-open, hizmet sürekliliği için bilinçli kabul
```

Doldurulmuş örnek 2 — S6 (kanıt yok + kapı):

```text
[⚠️ KANIT YOK] sınıf=10 risk=yok skor=5×2=10/P1
         kanıt=OriginCheckMiddleware: 36-43 (403 var, log yok) doğrulama=B-1 adr=—
         karar=azalt gerekçe=ihlal satırına securityEvent eklenmeli; eklenince AZALTILMIŞ
```

Şablon kuralı: alan eksik olamaz — veri yoksa `—` ya da `yok` yazılır, satır kısaltılmaz. Şablondaki `skor` alanı anayasa §4.4 bantlarıyla, `kanıt` alanı §10 konsolidasyonuyla, `doğrulama` alanı `threat-model-dogrulama` AD-xx defteriyle tutarlı olmak zorundadır; üçünden biri çelişirse satır geçersiz sayılır ve §7b kurtarma adımı işletilir.

---

## §10 KANIT KONSOLİDASYONU (bu dosyada kullanılan Dosya·Satır)

Senaryo ve bulgularda geçen tüm kanıt satırları tek yerde — çelişki aranacaksa burası okunur (hepsi 2026-10-15 disk okumasıdır):

| # | Dosya | Satır | Ne gösterir | Nerede |
|---|---|---|---|---|
| 1 | `shared/src/Middleware/CsrfMiddleware.php` | 15-17 | bypass route listesi boş (`[]`) | S1 |
| 2 | `shared/src/Middleware/CsrfMiddleware.php` | 24 | safe-method ayrımı (GET/HEAD/OPTIONS) | S1 |
| 3 | `shared/src/Middleware/CsrfMiddleware.php` | 42 | `hash_equals` sabit-zamanlı karşılaştırma | S1 |
| 4 | `shared/src/Middleware/CsrfMiddleware.php` | 49-63 | ihlal → 403 `csrf_invalid` + securityEvent | S1 |
| 5 | `shared/src/Middleware/BypassAuthMiddleware.php` | 29-31 | credential'tan biri boşsa `[]` — reddet | S2 |
| 6 | `shared/src/Middleware/BypassAuthMiddleware.php` | 42-44 | header yoksa "dev bypass yok" | S2 |
| 7 | `shared/src/Middleware/BypassAuthMiddleware.php` | 49-54 | her kullanımda securityEvent | S2 |
| 8 | `shared/src/Middleware/RateLimiterMiddleware.php` | 20-30 | `AUTH_FAIL_CLOSED_PATHS` listesi | S3a |
| 9 | `shared/src/Middleware/RateLimiterMiddleware.php` | 53-65 | cache çözülemezse auth yolunda 503 | S3a |
| 10 | `shared/src/Middleware/RateLimiterMiddleware.php` | 66-68 | genel yollar fail-open | S3b |
| 11 | `shared/src/Middleware/RateLimiterMiddleware.php` | 86-99 | ihlal → 429 + `RATE 005` securityEvent | S3a |
| 12 | `shared/src/Middleware/OriginCheckMiddleware.php` | 36-43 | empty-origin + flag dali; `:43` 403 (log yok) | S6 · B-1 |
| 13 | `shared/src/Middleware/OriginCheckMiddleware.php` | 109-111 | prod'da fallback asla açık gelmez | S6 |
| 14 | `shared/src/Middleware/SecurityHeadersMiddleware.php` | 17-19 | nonce üretimi (`bin2hex(random_bytes(32))`) | B-2 bağlamı |
| 15 | `shared/src/Middleware/SecurityHeadersMiddleware.php` | 26-41 | 10 başlık — HSTS yok | S4 · B-2 |
| 16 | `shared/src/Middleware/SecurityHeadersMiddleware.php` | 38-41 | COOP/COEP yalnız HTTPS'te | S4 · B-2 |
| 17 | `shared/logs/app.log` | satır kanıtı | `DEV BACC 006` (2026-10-09) | S2 canlı log |
| 18 | `shared/logs/app.log` | satır kanıtı | `RATE 005` (2026-10-09) | S3a canlı log |

Kural: bu tabloya yeni satır yalnız Dosya·Satır okunarak eklenir; "benzer bir yerde vardı" ifadesi satır değildir (anayasa Z-5). Tablo ile senaryo tabloları çelişirse **senaryo tablosu değil, bu konsolidasyon tablosu** yeniden okunur ve senaryo düzeltilir — kanıt değişmez, yorum değişir.

---

## §11 TUR-2 KANIT TAZELEME KAYDI (2026-10-15 — sibling dosyaların ortak okuması)

Senaryolar §10'daki kanıtla sürülür; bu turda sibling genişletme için aşağıdaki ek yüzeyler **aynı gün** diskten okundu.
Kural: aşağıdaki satırlar bu dosyanın §10 konsolidasyonuna **ek** kaynaktır; senaryo tablosundaki eski satırlarla çelişirse
okuma tekrarlanır (§10 son kuralı geçerlidir).

| # | Dosya | Okundu (2026-10-15) | Ne alındı | Kullanan sibling |
|---|---|---|---|---|
| 1 | `shared/src/Middleware/PermissionMiddleware.php` | `:11-14` docblock (frozen sıra) · `:23-25` (auth yoksa geç) · `:30-42` (role/permission strict) · `:47-62` (403 + `securityEvent('authz_denied')`) | yetki aşımı yüzeyi + gözlem kanıtı | ornekler Ör-07 · dogrulama AD-12 |
| 2 | `shared/src/Middleware/ValidationMiddleware.php` | `:22-24` (safe-method atlama) · `:26-31` (`required_fields` boşsa geç) · `:41-69` (kurallar) · `:72-80` (422 halt) | doğrulama yüzeyi + boşluk kanıtı | ornekler Ör-08 · dogrulama AD-13 |
| 3 | `shared/src/Middleware/AuthMiddleware.php` | `:11-13` (`_auth` init) · `:18-21` (session anahtarları) · `:23-31` (userId string + RBAC payload) | kimlik aktarımı zinciri | ornekler Ör-10 |
| 4 | `shared/src/Middleware/SessionManagerMiddleware.php` | `:17-21` (`ensureStarted` · `startOrExtend(nonce)` · `_session` yazımı) | oturum → request köprüsü | ornekler Ör-10 · dogrulama AD-08 |
| 5 | `shared/src/Middleware/SecurityHeadersMiddleware.php` | `:17-19` (nonce) · `:26-41` (9 başlık + HTTPS koşullu COOP/COEP) · `:54-56` (strict-dynamic) · `:71-73` (frame-ancestors/form-action) | başlık set'i + HSTS yokluğu (B-2) | ornekler Ör-09 · dogrulama AD-14 |
| 6 | `shared/src/Middleware/MiddlewarePipeline.php` | `:17-21` (`pipe`) · `:32-34` (reverse sarma) · `:41-48` (sınıf adı + `timing()`) | sıra + gözlem mekanizması | ornekler Ör-11 · dogrulama AD-15 |
| 7 | `shared/tests/Middleware/MiddlewarePipelineTest.php` | `:190-199` (`1_OriginCheck … 10_Validation` frozen sıra kaydı) | sıra regresyon testi | ornekler Ör-11 · dogrulama AD-15 |
| 8 | `shared/src/Log/Redactor.php` | `:31` (hassas anahtar listesi) · `:40/:49/:56` (desenler) · `:65-93` (`redact()` akışı) | sır maskeleme (R-08) | ornekler Ör-12 · dogrulama AD-17 |
| 9 | `shared/tests/Unit/Log/LoggerRedactionTest.php` | `:122-138` (64-char hash maskelenir, UUID korunur — `:127`, `:134`, `:137`) | maskeleme test kanıtı | ornekler Ör-12 · dogrulama AD-17 |
| 10 | `shared/src/Middleware/` (glob) | 11 dosya / `IMiddleware` sınıfı 10 — `LoggingMiddleware.php` ve `ApiVersionMiddleware.php` **YOK** (0 glob isabeti) | envanter düzeltme | bilesenler §2a · dogrulama AD-16 |

| Tazeleme Kuralı | |
|---|---|
| Ne zaman yeniden okunur | kanıt satırı `⚠️` ise · senaryo yeni yüzeye değiyorsa · anayasa §7 tetikleyicisi gerçekleştiyse |
| Okuma olmadan satır | yalnız `⚠️ VERIFICATION REQUIRED` yazılır; eski satır silinmez (anayasa T-11 / Y-03) |
| §10 ↔ §11 çelişkisi | ikisi de yeniden okunur; çelişki Vault Steward kararı olmadan "düzeltmez" |
| Sınır | bu tur okumaları K009 içindedir; K010/K000/K014 yüzeyine karar yazılmaz (T-08 / H-8) |

## § BAĞLANTILAR

- [[threat-model-anayasa]] · [[threat-model-ornekler]] · [[threat-model-kapsam]] · [[threat-model-dogrulama]] · [[threat-model-bilesenler]] · [[threat-model-web-arastirma]] · [[index]]
- [[ADR-008-bypass-auth-middleware]] · [[ADR-010-csrf-protection-strategy]] · [[ADR-013-rate-limiting-apcu]] · [[security-riskler]] · [[security-master]]

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
