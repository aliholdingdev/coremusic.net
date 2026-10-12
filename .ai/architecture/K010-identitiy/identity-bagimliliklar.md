---
node_id: K010
status: draft
depends_on: []
---

# K010 identity — bağımlılıklar (Ç-10)

> **Nitelik:** TASARIM İDDİASI — kanıt temelli değil. Hiçbir ok doğrulanmamıştır; doğrulanmayan her satır `⚠️ VERIFICATION REQUIRED» işaretlidir.

## Ç-10.1 Düğümler arası (v2 §2.10)
| # | Kaynak | Hedef | Tür | Nitelik |
|---|---|---|---|---|
| 1 | user-identity | account | require | TASARIM İDDİASI |
| 2 | account | credentials | require | TASARIM İDDİASI |
| 3 | account | session | require | TASARIM İDDİASI |
| 4 | session | tokens | provide | TASARIM İDDİASI (yön: tokens→session da savunulabilir — ⚠️ VERIFICATION REQUIRED) |
| 5 | account | roles | require | TASARIM İDDİASI |
| 6 | roles | permissions | require | TASARIM İDDİASI |
| 7 | device | device-identity | require | TASARIM İDDİASI (K004 komşuluğu) |
| 8 | credentials | identity-integration | require | TASARIM İDDİASI |
| 9 | tokens | identity-integration | require | TASARIM İDDİASI (2 web kaynağı ile destekli — §Ç-10.3) |

## Ç-10.2 Domenler arası (üçgen, 2026-10-12)
| Bağ | Not |
|---|---|
| K010 → K009 | token imzalama/oturum denetimi ortak — sınır tartışmalı (ADR-097 G2) · TASARIM İDDİASI |
| K009 → K010 | auth guard identity modeline bakar · TASARIM İDDİASI |
| K010 → K008 | account/roles birer domain modeli olabilir · TASARIM İDDİASI |
| K010 → K013 | OAuth/sınırlar dokümanı (PKCE) — web kanıtlı sınır · doğrulanmış (2026-10-12, URL §Ç-10.3) |

## Ç-10.3 Doğrulanmış dış bağımlılıklar (web, 2026-10-12)
| Kaynak | URL | Ne kanıtlıyor |
|---|---|---|
| Spotify PKCE flow | https://developer.spotify.com/documentation/web-api/tutorials/code-pkce-flow | code_verifier + S256 challenge + state; secret-less client; token endpoint `https://accounts.spotify.com/api/token» yalnız PKCE ile |
| Spotify authorization | https://developer.spotify.com/documentation/web-api/concepts/authorization | PKCE'nin yetkilendirme kavramı içindeki yeri |
| Apple developer token | https://developer.apple.com/documentation/applemusicapi/generating-developer-tokens | ES256 · 10 karakter kid · MusicKit private key · platform otomasyonu |
| Apple WWDC 10148 | https://developer.apple.com/videos/play/wwdc2022/10148/ | token üretimi anlatımı (incelendi) |

**Kapsam notu:** her iki sağlayıcı da K009/K010/K013 sınırında — hangi katmanın sahip olduğu ADR-097 G2.

## Ç-10.4 ⚠️ VERIFICATION REQUIRED
1. §Ç-10.1'deki 9 ok tamamı TASARIM İDDİASI (hiçbiri ölçülmüş değil).
2. session okunun yönü (3/4) — net değil.
3. CoreMusic'in OAuth rolü (sağlayıcı/istemci) — UNKNOWN.
4. K010'un dış kimlik sağlayıcı listesi — Spotify/Apple doğrulandı; diğerleri UNKNOWN.

## Ç-10.6 Ok tiplerinin açıklaması

| Tür | Anlamı | Örnek |
|---|---|---|
| require | kaynak, hedefin arayüzüne ihtiyaç duyar | account → credentials |
| provide | kaynak, hedefin beklediği arayüzü sağlar | session → tokens (yön tartışmalı) |
| exclude | birlikte kurulu olamaz | (bu domende yok) |

**Nitelik sütunu kuralı (v2 §4.1):** `TASARIM İDDİASI» = ok mimari tasarımdan çıkarılmış, ölçülmüş değil. `doğrulanmış» = tarih + URL/kanıt var. Bu belgede 9 iç okun tamamı TASARIM İDDİASI; tek doğrulanmış çapraz-domen K010→K013 (4 URL, §Ç-10.3).

## Ç-10.7 Bilinen belirsizlikler (bội-oku / çift yorum)

1. **#4 yönü:** session → tokens (provide) mi, tokens → session (require) mı? İkisi de savunulabilir: refresh token oturumu sürer (tokens→session), oturum token üretir (session→tokens). v2 §2.10 bu ayrımı yapmıyor → ⚠️ VERIFICATION REQUIRED.
2. **#7 dış düğüm:** device düğümü K004'te — v2 K004 içeriği bu görev kapsamında okunmadı (UNKNOWN). Ok, komşuluk varsayımıyla yazıldı.
3. **roles → permissions yönü:** tersi (permissions→roles) de RBAC literatüründe var. v2 satır 344-345 sırası esas alındı (roles önce) — TASARIM İDDİASI.
4. **account ↔ user-identity ayrımı:** ikisi birleştirilebilir (tek User aggregate). v2 iki satır ayrı tutmuş (339, 340) → ayrı düğüm kabul edildi.

## Ç-10.8 Sayaçlar (son)

9 iç ok · 6 çapraz-domen (1 doğrulanmış, 5 TASARIM İDDİASI) · 4 dış URL · 5 ⚠️ VERIFICATION REQUIRED · 4 bilinen belirsizlik · 0 doğrulanmış iç bağımlılık · dolgu 0

## Ç-10.9 Doğrulama adımları (bu belge nasıl test edilir)

1. **Adım 1 — ok listesi:** §Ç-10.1'deki 9 satırın tamamı v2 §2.10 (339-347) içinde karşılık buluyor mu? → evet, 9/9 (2026-10-12).
2. **Adım 2 — çapraz:** §Ç-10.2'deki 6 çapraz-domen bağı, ilgili domen master'larında da geçiyor mu? → K009/K013 tarafı bu görevde yazıldı (security-bagimliliklar.md), K006/K007/K008 batch'lerinde geçiyor → çapraz okundu.
3. **Adım 3 — dış URL:** §Ç-10.3'teki 4 URL, iddialarıyla birebir örtüşüyor mu? → evet (G-10.2).
4. **Adım 4 — belirsizlik:** §Ç-10.4'teki 5 satır, belgede UNKNOWN/⚠️ işaretiyle mi duruyor? → evet, hiçbiri kesin yazılmadı.
5. **Geçme kriteri:** 4 adım da evet → Ç-10 GECTİ. Aksi → belge revize.

## Ç-10.10 Kapsam dışı (bu belgeye YAZILMAYAN)

- **kademe-3 düğümler** (ADR-097 K4) — UNKNOWN, listelenmedi.
- **K004/K006/K007/K008 domen detayları** — yalnız çapraz-ok var, içeriği bu görevde doğrulanmadı.
- **runtime kod bağımlılıkları** (PHP namespace, import) — A4 aşaması, bu belge kapsamı DIŞI.
- **dolgu / tahmini ok** — `...», `TBD», `yaklaşık» yok (dolgu YASAK).
