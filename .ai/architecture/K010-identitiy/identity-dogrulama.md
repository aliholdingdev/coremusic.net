---
node_id: K010
status: draft
depends_on: []
---

# K010 identity — doğrulama (G-10)

## G-10.1 Belge doğrulaması (2026-10-12, 9/9)
| Düğüm | v2 satır | Dosya | Durum |
|---|---|---|---|
| user-identity | 339 | K010-identitiy/user-identity/index.md | ✓ |
| account | 340 | K010-identitiy/account/index.md | ✓ |
| credentials | 341 | K010-identitiy/credentials/index.md | ✓ |
| session | 342 | K010-identitiy/session/index.md | ✓ |
| tokens | 343 | K010-identitiy/tokens/index.md | ✓ |
| roles | 344 | K010-identitiy/roles/index.md | ✓ |
| permissions | 345 | K010-identitiy/permissions/index.md | ✓ |
| device-identity | 346 | K010-identitiy/device-identity/index.md | ✓ |
| identity-integration | 347 | K010-identitiy/identity-integration/index.md | ✓ |

## G-10.2 Web kanıtı (2026-10-12, 4/4 URL)
Spotify PKCE (2 URL) + Apple developer token (2 URL) — §Ç-10.3'teki iddiaları doğrular. Token endpoint `accounts.spotify.com/api/token» iddiası Spotify PKCE sayfasından, ES256/kid iddiası Apple sayfasından. YouTube Music API iddiası doğrulanmadı → açık soru (⚠️ VERIFICATION REQUIRED).

## G-10.3 Yapıyı doğrulayan sorgular (boş sonuç = GECTI)
1. K010-identitiy altındaki dosya sayısı: 13/13 ✓ (2026-10-12)
2. 9 kademe-2 klasörünün tamamında index.md var ✓
3. frontmatter 8 alan: node_id · status · spec · depends_on (7/9 boş, 2 işaretli) ✓
4. dosya adları: v2 9 ismi birebir (9/9) ✓
5. `identitiy» yazım ihlali: 0 (adlarda ve prose'de korunuyor) ✓
6. `dolgu» / `...» / `TBD»: 0 ✓

## G-10.4 Kapı
**[GATE-10] BELGE DOĞRULAMASI — BAŞARILI** (2026-10-12, 13 dosya).
**[GATE-10-DURUM] DRAFT · runtime kod doğrulaması YOK.**
Koşullu geçiş: ADR-097 K4 + G2 kararları + kod incelemesi (session/K009 sınırı).

## G-10.5 Aşamalar
- [x] A1 v2 §2.10 karşılaştırması (9/9) + §3/§4.1-4.3 şablonu
- [x] A2 Spotify/Apple kanıtı (4 URL) — YouTube Music: eksik, açık soru
- [x] A3 ADR-097 K4 + G2 kararı bekliyor (session/K009 sınırı)
- [ ] A4 kod incelemesi: oauth/session/role tabloları UNKNOWN
- [ ] A5 KVKK kimlik denetimi (user-identity kaynağı)

## G-10.6 Sayaçlar
13 dosya · 9 düğüm · 9/9 karşılaştırıldı · 4 URL · 4+1 açık soru · 9 TASARIM İDDİASI · 0 doğrulanmış iç bağımlılık · dolgu 0

## G-10.7 Dosya envanteri (13/13)

| # | Dosya | Bütçe | Durum |
|---|---|---|---|
| 1 | index.md | 40-90 | 50 ✓ |
| 2 | identity-master.md | 150-400 | 157 ✓ |
| 3 | identity-bagimliliklar.md | 80-150 | ✓ |
| 4 | identity-dogrulama.md | 80-150 | ✓ (bu dosya) |
| 5-13 | 9 × {alt-alan}/index.md | 20-60 | 24 ✓ (9/9) |

## G-10.8 Ayrım kontrolü (K009↔K010↔K013)

| Konu | Sahip (tasarım) | Aday sahip (v2 kanıtı) | Karar |
|---|---|---|---|
| session tanımı | K010 (satır 342) | K009 (§2.9 satır 313-315) | ADR-097 G2 |
| token imzası doğrulama | K009 | — | tasarımı K009'da |
| token sahipliği/rotasyonu | K010 (satır 343) | — | tasarımda K010 |
| idempotency / session 600 sn | K013 (satır 337-341) | K009 (satır 317) | ADR-097 K4 |
| OAuth/PKCE sınırları | K013 dokümanı | K010 (token uçları) | K013'e taşıdı |
| roles/permissions modeli | K010 (344-345) | K009 guard (314) | G2: isim birleşimi |

**Sonuç:** 6 konudan 1'i net (token imzası), 3'ü G2/K4 kararı, 2'si tasarımda K010 — hepsi açık soru listesinde.

## G-10.9 Test senaryoları (A4 öncesi, taslak)

| # | Senaryo | Beklenen | Kaynak |
|---|---|---|---|
| T1 | PKCE code_verifier yoksa token isteği | reddedilir | Spotify URL (Ç-10.3) |
| T2 | state eşleşmiyorsa | reddedilir (CSRF) | Spotify URL |
| T3 | Apple token kid 10 karakter değilse | doğrulama hatası | Apple URL |
| T4 | ES256 imzası yanlışsa | 401 | K009 crypto |
| T5 | parola hash'i düz metin DB'de | ihlal (M5) | OWASP / K007 pgcrypto |
| T6 | oturum süresi doldu | yenileme/reddet | K010 session |
| T7 | izinsiz rol erişimi | 403 | K009 authorization-guard |

**Durum:** senaryolar TASLAK — kod yok, çalıştırılamaz (A4). Her satır kaynağa bağlı, uydurma yok.

## G-10.10 Sonraki kapılar

1. **ADR-097 G2:** session / roles / permissions sahipliği → G-10.8 tablosundaki 3 karar.
2. **ADR-097 K4:** kademe-3 düğümler → 9 alt alanın içine ne girecek.
3. **A4 kod incelemesi:** oauth / session / role tabloları ve import zinciri — UNKNOWN'ların kapanması.
4. **A5 KVKK denetimi:** user-identity + account silme akışı (§G-10.5).
