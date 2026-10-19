---
node_id: K0100-identitiy
node_type: domain
status: verify-required
parent_id: K000-K5000-Architecture-Registry
children:
  - kullanici-modeli
  - rol-ve-yetki
  - oturum-yonetimi
  - dogrulama-servisleri
  - sso-entegrasyonu
  - mfa-ve-sertifika
  - oidc
  - oauth2
  - jwt
depends_on:
  - K009-security
  - K008-domain
  - K007-data
evidence:
  - "shared/src/Security/JwtService.php L24 RS256 · L51 jti · L89-109 fail-closed validate — 2026-10-12"
  - "shared/src/Session/SessionConfig.php L16-20 MAX_LIFETIME 1800 · IDLE_TIMEOUT 3600 — 2026-10-12"
  - "shared/src/Api/Middleware/AuthenticationMiddleware.php L33-38 PUBLIC_ROUTE_PATTERNS — 2026-10-12"
  - "shared/src/OAuth/OAuthManager.php L157-183 sha256(state) tek kullanımlık — 2026-10-12"
validation: "identity-dogrulama.md · identity-web-arastirma.md (8 tekil URL) · ADR-097 (draft) bekliyor"
group: mimari
open_questions:
  - "Session paradoxu: MAX_LIFETIME 1800 < IDLE_TIMEOUT 3600 — hangisi kazanır? (verify-required)"
  - "K009 ↔ K010 sınırı: session/tokens hangi düğümün malı? (ADR-097 G2)"
  - "OAuth tarafı: CoreMusic sağlayıcı mı, istemci mi (PKCE) — kısmen kanıtlandı"
  - "MFA: repo'da implementasyon YOK — tamamı PLANNED (verify-required)"
  - "Eski 9 kademe-2 klasörü (user-identity … identity-integration) diskte duruyor — silme yetkisi orkestratörde"
---

# K0100-identitiy — kimlik (identity) düğümü dizini

- **Kaynak:** v2 SSOT `coremusic-katmanli-mimari-agaci-v2.md` §2.10 (satır 331-350) + §3 + §4.2/§4.3 · **Ölçüm:** 2026-10-12
- **Yazım kilidi:** `identitiy` yazımı kaynağa sadık korunur (v2 §2.10) — hiçbir dosyada düzeltilmez
- **Kapsam sınırı:** kimlik · kullanıcı · rol · yetki · oturum · token · credential · SSO · MFA. Middleware, CSRF/CSP, rate limit → **K009-security** (bağımlılık). Kademe: 1 (kök) → 2 (6 çocuk) → 3 (3 teknoloji klasörü). Grup ekseni `mimari`/`kod`/`kanit` (ADR-097 §5.5 · registry §8.1). status `verify-required`. Yazım kilidi: `identitiy` asla düzeltilmez (ölçüm 2026-10-12).

## §2 Kapsam Özet

| İçerik | Dışerik |
|---|---|
| Kullanıcı kimliği, hesap, rol, permission, session, token, credential, SSO bağlantısı, MFA tasarımı | Rate limit, CSP nonce üretimi politikası, encryption altyapısı (→ K009-security) |
| JWT issuance/validation (kod kanıtlı), OAuth state/nonce (kod kanıtlı), RBAC kontrol akışı (kod kanıtlı) | Session dosya sistemi şeması, DB migration içeriği (→ K007-data) |
| OIDC/OAuth2/JWT teknoloji derinlemesine (kademe-3) | Event fethi, domain event sahipliği (→ K008-domain) |

## §3 Kademe-2 Çocuk Ağacı (6 düğüm)

| # | Düğüm | v2 §2.10 karşılığı | Dizin |
|---|---|---|---|
| 1 | kullanici-modeli | user-identity + account | `kullanici-modeli/index.md` |
| 2 | rol-ve-yetki | roles + permissions | `rol-ve-yetki/index.md` |
| 3 | oturum-yonetimi | session + tokens | `oturum-yonetimi/index.md` |
| 4 | dogrulama-servisleri | credentials | `dogrulama-servisleri/index.md` |
| 5 | sso-entegrasyonu | identity-integration | `sso-entegrasyonu/index.md` |
| 6 | mfa-ve-sertifika | device-identity + yeni MFA başlığı | `mfa-ve-sertifika/index.md` |

> Eski 9 kademe-2 klasörü (`user-identity/` … `identity-integration/`) diskte durur; bu yeniden yazım onları **silmez** (silme kararı orkestratöre ait — §7/4).

## §4 Kademe-3 Teknoloji Klasörleri (3)

| # | Klasör | Dosya | Seçim gerekçesi |
|---|---|---|---|
| 1 | `oidc/` | 10 role + index | SSO oturum paylaşımı (auth.coremusic.net → web), `response_type='session'` (K11) |
| 2 | `oauth2/` | 10 role + index | 10 provider + PKCE state (K8/K9) — repo'daki en zengin kanıt |
| 3 | `jwt/` | 10 role + index | `JwtService` RS256 + jti revocation (K1) — API auth çekirdeği |

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` · `identity-master.md` · `identity-kapsam.md` · `identity-bagimliliklar.md` · `identity-bilesenler.md` · `identity-kararlar.md` · `identity-evrensel-dil.md` | künye, kapsamlı anlatı, sınır, bağımlılık, bileşen, karar, sözlük | 1 |
| kod | `identity-kod-ornekleri.md` · `identity-test-stratejisi.md` | gerçek repo kod alıntısı, test senaryo matrisi | 1 |
| kanit | `identity-dogrulama.md` · `identity-web-arastirma.md` · `identity-riskler.md` | iddia→URL→tarih→durum, risk defteri | 1 |
| mimari | `kullanici-modeli/` · `rol-ve-yetki/` · `oturum-yonetimi/` · `dogrulama-servisleri/` · `sso-entegrasyonu/` · `mfa-ve-sertifika/` (her biri index + 5 MD) | kademe-2 derinlemesine (6 birim) | 2 |
| mimari | `oidc/` · `oauth2/` · `jwt/` (her biri 10 rol + index) | kademe-3 teknoloji derinliği | 3 |

## §6 Bağımlılık Özeti

| Hedef | Tür | Kanıt durumu |
|---|---|---|
| `K009-security` | dış düğüm | kod kanıtlı (middleware, encryption K009 tarafında) |
| `K008-domain` | dış düğüm | olay/yönetim sınırı — `⚠️ VERIFICATION REQUIRED` |
| `K007-data` | dış düğüm | `oauth_connections` / `oauth_states` migration var (K23) |
| `K0100-identitiy/oidc` · `/oauth2` · `/jwt` | iç mimari düğüm | ADR-097 §5.5 kural 5 (depends_on iç+dış) |

## §7 Açık Sorular

1. **Session paradoxu:** `MAX_LIFETIME=1800` < `IDLE_TIMEOUT=3600` — mutlak üst sınır idle'ı ezer; davranış `identity-dogrulama.md` §4'te kanıtsız → `⚠️ VERIFICATION REQUIRED`
2. **Sınır (ADR-097 G2):** session ve token düğümü K010'da mı, K009'da mı — karar ADR-097'de bekliyor
3. **MFA:** repo'da hiçbir MFA/sertifika implementasyonu bulunamadı → `mfa-ve-sertifika/` tamamı `PLANNED`
4. **Legacy dizinler:** 9 eski kademe-2 klasörü ne zaman kaldırılacak (bu revizyon silmez)
5. **`identity-master.md` status:** içeriği doğrulanmış kanıtlara dayanır; ADR-097 onayı öncesi `verify-required`

**Doğrulama:** frontmatter 10 alan (`group` 9.) · `## §GRUPLAR` var · `TBD`/`...` yok · tekil URL (kök web-arastirma 8 URL) · gerçek kod referansı (`Dosya:`/`Satır:`) → ✅ · kök 12 dosya · kademe-2 6×6 · kademe-3 3×(10+1) → ✅ ölçüm §master.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
