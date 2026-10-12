---
node_id: K010
layer: identity
status: draft
registry: ".ai/architecture/index.md §5 satır 10"
spec: "v2 coremusic-katmanli-mimari-agaci-v2.md §2.10 (satır 331-350) + §3/§4"
depends_on: []
---

# K010 — identitiy (kimlik)

- **Kaynak:** v2 §2.10 satır 331-350 (2026-10-12)
- **Sorumlu dosyalar:** `identity-master.md» · `identity-bagimliliklar.md» · `identity-dogrulama.md»
- **Yazım notu:** `identitiy» yazımı kaynağa sadık korunur (v2 satır 350) — adlarda ve prose'de değiştirilmez
- **Depends_on:** `[]» — TASARIM İDDİASI (security/token sınırı: bkz `security-master.md» §4)

## Çocuk ağacı (kademe-2, 9 düğüm)

| # | Düğüm | v2 satır | Dosya |
|---|---|---|---|
| 1 | user-identity | 339 | user-identity/index.md |
| 2 | account | 340 | account/index.md |
| 3 | credentials | 341 | credentials/index.md |
| 4 | session | 342 | session/index.md |
| 5 | tokens | 343 | tokens/index.md |
| 6 | roles | 344 | roles/index.md |
| 7 | permissions | 345 | permissions/index.md |
| 8 | device-identity | 346 | device-identity/index.md |
| 9 | identity-integration | 347 | identity-integration/index.md |

## Kanıt (web, 2026-10-12)

- **Spotify PKCE:** S256 code_challenge + code_verifier + state · secret-less client · token endpoint `https://accounts.spotify.com/api/token» yalnız PKCE akışında → K009/K010/K013 sınırı
- **Apple MusicKit developer token:** ES256 · 10 karakter `kid» · MusicKit private key · Apple platformlarında otomatik, diğerlerinde self-sign → K009/K010 sınırı
- **YouTube Music:** resmi public API yok → TOS riski (K009 risk defteri), ⚠️ URL EKSİK

## Açık sorular

1. **Rol:** §4.2 alanı v2'de yok → ⚠️ VERIFICATION REQUIRED
2. **Kademe-3:** ADR-097 K4 kararı bekliyor (UNKNOWN)
3. **Sınır:** K009 ↔ K010 (session/tokens hangi katmanda?) → ADR-097 G2
4. **OAuth tarafı:** CoreMusic sağlayıcı mı, istemci mi (PKCE)?
5. **Doğrulanmış iç bağımlılık:** 0 — tüm oklar TASARIM İDDİASI

## İlişkili dosyalar

- **Doğrulama:** `identity-dogrulama.md» (G-10, 9/9 düğüm + 4 URL)
- **Bağımlılık:** `identity-bagimliliklar.md» (Ç-10, 9 ok + 6 çapraz-domen)
- **Master:** `identity-master.md» (§0-§12, 9/9 kapsamlı)
