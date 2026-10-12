---
node_id: K010
status: draft
spec: "v2 coremusic-katmanli-mimari-agaci-v2.md §2.10 (satır 331-350)"
depends_on: []
---

# K010 identity — master (v2 §2.10)

**Kapsam (v2 §2.10, satır 331-350):** identitiy katmanı 9 alt alan: user-identity, account, credentials, session, tokens, roles, permissions, device-identity, identity-integration. Yazım `identitiy» kaynağa sadık (satır 350). Bu belge §0-§12 9 alanı kapsar; her başlıkta v2 §2.10 satır aralığı.

## §0 Şablon (v2 §4.2 — 8 alan)
Her düğüm için: node_id · node_type · parent_id · status · layer · depends_on · evidence · validation (+ open_questions listesi). Kademe-2 `index.md» dosyaları bu şemayı uygular (§4.3).

---

## 1. user-identity (satır 339) — TASARIM İDDİASI

**Kapsam:** Kullanıcı kimliğinin çekirdek tanımı — kimlik id'si, profil bağının sahipliği, identified vs anonymous ayrımının kaynağı. KVKK/GDPR bağlamında burası kişisel verinin başlangıç düğümü (GDPR / md.kvkk.gov.tr — kanıt, 2026-10-12).

- **depends_on:** `[]» (çekirdek, bağımlılığı yok)
- **Rol:** K010'un root düğümü — diğer 8 alt alan buradan türetilir
- **Sınır:** K007 (user verisi saklama) ↔ K010 (kimlik tanımı) — hangisi id sahibi? ⚠️ VERIFICATION REQUIRED

## 2. account (satır 340)

**Kapsam:** Hesap yaşam döngüsü — oluşturma, e-posta doğrulama, askıya alma, silme (GDPR unutulma hakkı bu düğümde başlar). identified-user-id'nin operasyonel evi.

- **depends_on:** [user-identity] (TASARIM İDDİASI)
- **Risk:** Hesap silme ↔ KVKK — ADR-097 K4'te saklı

## 3. credentials (satır 341)

**Kapsam:** Parola / API-anahtar saklama **arayüzü** — bu katman hash algoritması seçmez (OWASP 2025 M5: parola hash'i storage katmanının işi, K007'de pgcrypto). K010 yalnız `credential sakla / doğrula» kontratını tanımlar.

- **depends_on:** [account] (TASARIM İDDİASI)
- **Çakışma:** K007 pgcrypto (hash üretimi) ↔ K010 credentials (arayüz) — parola hash'i hangisi? ADR-097 K4 kararı
- **Sızıntı riski:** credential sızıntısı → K009 risk defteri (incident-coordinator 1277-1285 / 3294-3300)

## 4. session (satır 342) — EN TARTIŞMALI DÜĞÜM

**Kapsam:** Oturum tanımı, yaşam döngüsü, yenileme, sonlandırma.

- **depends_on:** [account, tokens?] (yön tartışmalı)
- **Sınır çatışması:** v2 §2.9 guvenlik-temel sürümünde 313-315 (session-anonymous-id), 317 (session 600 sn idempotency — bu satır K013'e ait), 325 (token verify session-state). Yani **session üç katmanın sınırında:** K009 (denetim), K010 (kimlik), K013 (idempotency).
- **Karar bekleyen:** ADR-097 G2 (session sürüklenmeli mi?)

## 5. tokens (satır 343) — EN ÇOK KANITLANAN DÜĞÜM

**Kapsam:** Token türleri (access / refresh / API-key) ve imzalama sözleşmeleri.

**Web kanıtı (2026-10-12):**

| Sağlayıcı | Mekanizma | Kapsam sınırı |
|---|---|---|
| Spotify | PKCE — code_verifier + S256 code_challenge + state · secret-less client · `https://accounts.spotify.com/api/token» yalnız PKCE flow'da | K010 tokens + K013 OAuth |
| Apple | MusicKit developer token — ES256 · 10 karakter `kid» · MusicKit private key · platform otomasyonu / self-sign | K009 imza doğrulama + K010 token sahipliği |

- **depends_on:** [session] (TASARIM İDDİASI — yön tartışmalı)
- **Sınır:** token sahibi mi (K010) doğrulayıcısı mı (K009)? Her ikisi de kısmen — ADR-097 G2

## 6. roles (satır 344) + 7. permissions (satır 345)

**Kapsam:** RBAC modeli — rol tanımları (roles) ve izin matrisi (permissions).

- **depends_on:** roles → permissions, account → roles (TASARIM İDDİASI)
- **Çakışma riski:** K009'da authorization-guard ve tenant satırları var (314, 324-326) — isimler birleşmeli mi? ADR-097 G2
- **Sınır:** izin kontrolü gerçekten guard'da (K009) — burada model tanımlanır, denetim K009

## 8. device-identity (satır 346)

**Kapsam:** Cihaz kimliği (embedded / RPi5 bağlamı, K004 ile komşu) — anonim-id ↔ identified-id köprüsü. (stream-anonymous-id, K010, 600 sn sınırlı iz — v2 §2.9 satır 318.)

- **depends_on:** [device(K004), account] (TASARIM İDDİASI)

## 9. identity-integration (satır 347)

**Kapsam:** Harici kimlik sağlayıcı entegrasyonu (OAuth 2.0 / OIDC).

- **Doğrulanmış:** Spotify PKCE (2 URL) + Apple MusicKit (2 URL) — §5'teki tablo
- **Doğrulanmadı:** YouTube Music resmi public API yok → TOS riski (K009 risk defteri, ⚠️ URL EKSİK)
- **depends_on:** [credentials, tokens] (TASARIM İDDİASI)
- **Açık soru:** CoreMusic sağlayıcı mı, istemci mi? (K013 sınırı)

---

## §10 Ağ özeti (v2 §2.10, 9/9)

- 9 alt alan · 9 bağımlılık oku (1 yön tartışmalı) · **0 doğrulanmış iç bağımlılık** (TASARIM İDDİASI)
- En riskli düğüm: **session** (K009/K013 sınırı, G2 kararı bekliyor)
- En çok kanıtlanan düğüm: **tokens** (4 web URL'si, §5)
- KVKK kaynak düğümü: **user-identity**
- Sürükleme adayı: session, roles, permissions
- Dış bağımlılık: Spotify (PKCE) + Apple (ES256) — 2 doğrulanmış sağlayıcı

## §11 Açık sorular (K010)

1. session kimin? (K009 sürümü session satırları 313-315 / 317) — ADR-097 G2.
2. OAuth tarafı: CoreMusic sağlayıcı mı, istemci mi (PKCE)? — K013 sınırı.
3. roles/permissions ↔ K009 authorization-guard isim birleşimi.
4. identity-integration dış sağlayıcı listesi: Spotify/Apple doğrulandı; diğerleri UNKNOWN.
5. credentials ↔ K007 pgcrypto: parola hash'i kimin işi? (ADR-097 K4)
6. user-identity ↔ K007 user verisi: id sahibi kim?

## §12 Bağlantılar (v2 §3.1 okuma düzeni)

- **K006 ↔ K010:** embed / risks → user-identity (anonim/identik ayrımı) — TASARIM İDDİASI
- **K007 ↔ K010:** user / account verisi saklama — TASARIM İDDİASI
- **K008 ↔ K010:** account / roles domain modeli olabilir — TASARIM İDDİASI
- **K009 ↔ K010:** token imzalama / oturum denetimi ortak — en aktif sınır
- **K013 ↔ K010:** OAuth / PKCE sınırlar dokümanı — 4 web URL'si ortak

## §13 Katman sözleşmesi (v2 §3 — K010'un yeri)

| Katman | K010'da ne var | Ne YOK |
|---|---|---|
| Presentation | login/register ekranlarının ViewModel kontratı (adı) | HTML/form kodu |
| Application | authenticate / register / rotate-token use-case adları | iş kuralı detayı |
| Domain | user, account, credential, session, token, role, permission modelleri | framework |
| Infrastructure | hash üretimi (K007/pgcrypto), token imzası (K009), sağlayıcı adapter'ları | — |

**Dependency Rule notu:** K010 domain modelleri dışarı bakmaz; sağlayıcı adapter'ları (Spotify/Apple) en dışta ve yalnız identity-integration'a bağlı.

## §14 KVKK / GDPR okuması (2026-10-12, bu domen için)

1. **user-identity** = kişisel veri kaynağı → aydınlatma metni buradan başlar (KVKK md.kvkk.gov.tr / GDPR).
2. **account silme** = unutulma hakkı → account düğümünde yaşam döngüsü kancası zorunlu.
3. **credentials** = yüksek riskli veri → yalnız hash (OWASP 2025 M5), plaintext yasak.
4. **device-identity** = davranış verisi köprüsü → K006 privacy-by-design ile eşleştirilmeli (anonim id 600 sn).
5. **tokens** = erişim izni → en kısa ömür + rotasyon (PKCE örneği: code_verifier tek kullanımlık).

## §15 OWASP 2025 kesişimi (K009↔K010)

| OWASP | K010 düğümü | K009 tarafı |
|---|---|---|
| M01 Broken Access Control | roles / permissions | authorization-guard |
| M02 Cryptographic Failure | credentials (hash arayüzü) | crypto-verification |
| M07 Identification & Authentication Failures | session / tokens | session-anonymous-id, token verify |
| M05 Injection (parola politikası) | account (policy) | — |

## §16 Risk defteri (K010)

| # | Risk | Etki | Durum |
|---|---|---|---|
| R1 | session üç katman sınırında (K009/K010/K013) | tasarım karmaşası | ADR-097 G2 bekliyor |
| R2 | credentials ↔ K007 pgcrypto sahiplik belirsiz | hash yanlış yerde üretilebilir | ADR-097 K4 bekliyor |
| R3 | YouTube Music resmi API yok | TOS ihlali riski | URL EKSİK, K009 defteri |
| R4 | OAuth tarafı belirsiz (sağlayıcı/istemci) | yanlış entegrasyon | UNKNOWN, K013 |
| R5 | roles/permissions ↔ authorization-guard isim çakışması | çift model | ADR-097 G2 |
| R6 | 0 doğrulanmış iç bağımlılık | tüm ağ TASARIM İDDİASI | A4 kod incelemesi |

## §17 Doğrulama bağları

- **G-10** (`identity-dogrulama.md»): 9/9 düğüm · 4 URL · 7 sorgu · 6 aşama
- **Ç-10** (`identity-bagimliliklar.md»): 9 iç ok · 6 çapraz-domen · 5 ⚠️ VERIFICATION REQUIRED
- **Çocuklar:** 9 × `{alt-alan}/index.md» (20-60 satır bütçesi, §4.3 şeması)
