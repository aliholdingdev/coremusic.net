---
node_id: K009-security
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - authentication
  - authorization
  - policies
  - session-security
  - secrets
  - encryption
  - input-validation
  - rate-limiting
  - audit
  - threat-model
  - security-testing
depends_on:
  - K010-identitiy
  - K014-network
  - K000-isletim-sistemi
evidence:
  - "v2 §2.9 — coremusic-katmanli-mimari-agaci-v2.md satır 310–329"
  - ".ai/architecture/index.md §5 satır 9 (master = security-master.md)"
  - "https://developer.spotify.com/documentation/web-api/tutorials/code-pkce-flow (2026-10-12)"
  - "https://developer.apple.com/documentation/applemusicapi/generating-developer-tokens (2026-10-12)"
validation:
  - "2026-10-12 — children listesi v2 mini-ağaçı ile karşılaştırıldı: 11/11 birebir"
  - "2026-10-12 — Spotify PKCE ve Apple developer-token dokümanları açıldı, iddialar karşılaştırıldı"
open_questions:
  - "YouTube Music resmi public API yok (TOS riski) → entegrasyon stratejisi → ⚠️ VERIFICATION REQUIRED"
  - "K009 ↔ K010 sınırı (session-security vs session/tokens/credentials) → döngü riski → ⚠️ VERIFICATION REQUIRED"
  - "Çocukların rol tanımları v2'de yok → ⚠️ VERIFICATION REQUIRED (ADR-097 K4)"
---

# K009-security — Domain Künyesi

## ELI15 rol (v2 §2.9 — birebir)

> **Kapıdaki bekçi — şifre, yetki, şifreleme, saldırı koruması.**

## Çocuk kataloğu (kademe-2)

v2 §2.9 bu çocuklara **yalnızca ad verir**; hiçbiri için rol tanımı yok → hepsi `⚠️ VERIFICATION REQUIRED».

| # | Çocuk | v2 satır | Durum |
|---|---|---|---|
| 1 | authentication | 318 | draft |
| 2 | authorization | 319 | draft |
| 3 | policies | 320 | draft |
| 4 | session-security | 321 | draft |
| 5 | secrets | 322 | draft |
| 6 | encryption | 323 | draft |
| 7 | input-validation | 324 | draft |
| 8 | rate-limiting | 325 | draft |
| 9 | audit | 326 | draft |
| 10 | threat-model | 327 | draft |
| 11 | security-testing | 328 | draft |

Her çocuğun kendi `index.md»'si bu klasörde vardır (kademe-2 künyesi, `children: []»).

## Dosyalar (çoklu-MD şeması — ADR-097 K2)

| Dosya | Rol |
|---|---|
| `index.md» | bu künye (8 zorunlu alan) |
| `security-master.md» | anayasa — v2'deki ad, dokunulmaz |
| `security-bagimliliklar.md» | depends_on / used_by / entegrasyon sınırı |
| `security-dogrulama.md» | ölçütler · açık sorular · web-kanıt kayıtları |

## Durum

| Alan | Değer |
|---|---|
| status | draft (registry §5 satır 9) |
| Web kanıtı | ✅ var — Spotify PKCE · Apple MusicKit · YouTube Music (master §4) |
| K013 sınırı | Spotify akışı K009/K010/K013'e bölünür (manifest kaydı) |
| Açık sorular | 3 · master §7 ve -dogrulama §3 |

> **Etiket notu:** frontmatter `depends_on» satırları **TASARIM İDDİASI**dır — `verified» değildir.
