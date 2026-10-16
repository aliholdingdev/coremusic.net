---
node_id: "K008-media-bilesenler"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-domain/media-kapsam.md"
evidence:
  - ".ai/.sql/mysql/coremusic_media.sql satır 18–165 (device_* + media_access/audit/metadata) — 2026-10-15 okundu"
  - "shared/src/Events/Domain/MediaAccessedEvent.php satır 20 (tek media event)"
  - "media-kapsam.md §2/§3 — sınır ve model notları"
validation: "2026-10-15 — kademe-3 planı; şema satır kanıtları coremusic_media.sql"
open_questions:
  - "`MediaTranscodedEvent` diskte YOK (Events/Domain/ 9 dosya, 2026-10-15) → transcode olayı adayı ⚠️ VERIFICATION REQUIRED"
  - "Kademe-3 adları ADR-097 K4 bekliyor"
group: mimari
---

# media — Bileşenler (K008 · kademe-3 planı)

> **Authority: Bayram Ali / Vault Steward.** `media` BC'sinin kademe-3 **taslak**
> planı. Kanıt iddiası yapmaz; kanıt [[media-dogrulama]] ve
> [[media-kapsam]] içindedir.

## §1 KADeme-3 ALT DÜĞÜM ADAYLARI

| # | Alt düğüm (aday) | Kapsamı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `media-varlık` | `media_metadata` / `media_access` varlık dili | `coremusic_media.sql` satır 115 (`media_access`), 150 (`media_metadata`) | ⏳ ADR-097 K4 |
| 2 | `media-erişim` | `MediaAccessedEvent` — erişim olayı | `Events/Domain/MediaAccessedEvent.php` satır 20 | ✅ dosya kanıtlı |
| 3 | `media-transcode` | transcode davranışı + olay adayı | `MediaTranscodedEvent` Events/Domain/ içinde **YOK** (2026-10-15) | ⚠️ VERIFICATION REQUIRED |
| 4 | `media-denetim` | `media_audit` kaydı | `coremusic_media.sql` satır 132 | ⏳ ADR-097 K4 |
| 5 | `media-format` | codec/format sözlüğü (`file_format` enum mp3/flac/wav/aac/ogg) | `coremusic_media.sql` satır 57 | ✅ şema kanıtlı — web: [[media-web-arastirma]] M-1…M-11 |

## §2 SINIR KOMŞULARI

| Komşu | Yön | Ne geçmez | Kanıt |
|---|---|---|---|
| `music` | media → music | katalog dili kopyalanmaz | `media-kapsam.md` §2 |
| `device-management` | media → device | senkron durumu cihaz tablolarında | `coremusic_media.sql` satır 18–66 (`device_*`) |
| `playback` | media → playback | oynatma durumu burada **tutulmaz** | `media-kapsam.md` §2 |

## §3 OLGUNLUK MATRİSİ

| Bileşen | Kod | Şema | Test | Olgunluk |
|---|---|---|---|---|
| Erişim olayı | ✅ `MediaAccessedEvent` | — | ⚠️ test kanıtı → VERIFICATION REQUIRED | Orta |
| Şema (5 device tablosu + 3 media tablosu) | — | ✅ satır 18–165 | — | Yüksek |
| Transcode olayı | ❌ | — | — | Yok |
| Cihaz oynatma hedefi | ❌ (bkz `device-management` çocuğu) | — | — | Yok (sınır dışı) |

**Kardeş dosyalar:** [[media-kapsam]] · [[media-dogrulama]] · [[media-web-arastirma]] · [[media-ornekler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
