---
node_id: "K008-device-management-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-domain/domain-master"
evidence:
  - "domain-master.md §3.1 satır 95 — Cihaz Yönetimi | device-management | Device (çalma hedefi) | — (kanıt AŞAĞIDA §3.4)"
  - "domain-master.md §3.4 satır 126 — device-management domain event dosyası GÖRÜLMEDİ (keşif 2026-10-12) ⚠️"
  - ".ai/.sql/mysql/coremusic_media.sql satır 18–113 (5 tablo) · coremusic_system.sql satır 138–159"
  - "shared/src/Device/DeviceDetector.php satır 49–72 · assets.coremusic.net/js/devices.config.js satır 1–43"
validation: "2026-07-15 — kapsam sınırı 6 tablo + 2 kod dosyası + master kanıtıyla çizildi"
open_questions:
  - "aggregate adı Device (çalma hedefi) → ADR-097 K4'te kilitlenmedi → taslak"
  - "domain event → §3.4 boşluğu (master satır 126) → ADR bekliyor"
  - "cihaz kayıt/senkron sunucu akışı (PHP) → bu oturumda tarama yapılmadı → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# device-management — Kapsam (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> `device-management`, **cihazın kendisini** yönetir: tespit (7 tip), kayıt
> (`devices` + `device_types`), çevrimdışı senkron (3 tablo), Bluetooth kayıt
> (`system_bluetooth_devices`) ve çalma hedefi (`is_primary`). Cihaza özel ses
> profili `AIWorkflow::autoEQWorkflow` içinden akar.

## §1 ROL (ELI15)

Kullanıcı CoreMusic'i farklı cihazlarda kullanır: telefon, tablet, gömülü
ekran (kiosk), laptop, desktop, 4K TV, 4K monitor. Sunucu bu cihazı **User-Agent
+ viewport + `X-Device-Type` başlığıyla** tespit eder (`DeviceDetector::detect`,
satır 57–72) ve **aynı 7'li liste** istemci tarafında CSS haritasını besler
(`devices.config.js` satır 43 — `ALL`). Cihaz kayıtları `devices` tablosunda
tutulur; her cihaz bir `device_types` satırına bağlanır. **Çevrimdışı erişim**
için playlist/track senkronu `device_playlists` + `device_tracks` + `device_sync_history`
tablolarında izlenir. Eşleştirilmiş Bluetooth cihazları ayrı
`system_bluetooth_devices` tablosundadır. `autoEQWorkflow` oda profili +
`device_id` ile cihaza özel EQ eğrisi üretir (satır 147–174).

## §2 KAPSAM SINIRI

| # | Bu bağlamın İÇİNDE | Bu bağlamın DIŞINDA | Sınır komşusu |
|---|---|---|---|
| 1 | Cihaz kaydı + tip modeli (`devices`, `device_types`) | Medya dosyası/variant (transcode) | `media` |
| 2 | Çevrimdışı senkron izleme (`device_playlists`, `device_tracks`, `device_sync_history`) | Çalma listesi CRUD | `playlist` |
| 3 | Çalma hedefi seçimi (`devices.is_primary` satır 101) | Oynatma oturumu/durumu | `playback` |
| 4 | Cihaza özel EQ isteği (`autoEQWorkflow` satır 147–174) | EQ eğrisinin DSP'de uygulanması | Neva Engine / `media` |
| 5 | Bluetooth cihaz kaydı (`system_bluetooth_devices`) | Bluetooth stack/protokol | OS (Windows/macOS) |
| 6 | — | Ağ cihazı keşfi (DLNA/UPnP MediaRenderer) | **DISKTE YOK** — yalnız web referansı (W-2…W-8) |

## §3 DDD MODEL NOTU (taslak — ADR-097 K4)

| # | Kalem | Ad | Kanıt |
|---|---|---|---|
| 1 | Bounded Context (aday) | Cihaz Yönetimi (`device-management`) | ✅ domain-master §3.1 satır 95 |
| 2 | Aggregate (aday) | Device (çalma hedefi) | ⚠️ TASARIM İDDİASI — ADR-097 K4 |
| 3 | Entity / kayıt | `devices` satırı · `device_types` satırı · 3 senkron tablosu · BT tablosu | ✅ coremusic_media.sql 18–113 · coremusic_system.sql 138–159 |
| 4 | Value Object (aday) | `platform` enum (satır 72) · `sync_status` enum'ları (satır 22, 36–37, 58) · `file_format` enum (satır 57) · `codec` enum (satır 144) | ✅ şema |
| 5 | Ubiquitous language | cihaz = kullanıcıya bağlı makine · senkron = çevrimdışı erişim · hedef = `is_primary` · eşleşme = `device_uuid`/`mac_address` | ✅ şema + kod |
| 6 | Domain event | — | ❌ YOK — master §3.4 satır 126 (2026-10-12 keşfi) |
| 7 | Invariant | `device_uuid` UNIQUE (satır 109) · `mac_address` UNIQUE (satır 156) · (device,playlist) UNIQUE (satır 28) · (device,music) UNIQUE (satır 63) | ✅ KOD KANITLI |

## §4 DISK KANITLARI

| Dosya | Satır | Ne |
|---|---|---|
| `.ai/architecture/K008-domain/domain-master.md` | 95 · 126 | aggregate adayı · event boşluğu |
| `.ai/.sql/mysql/coremusic_media.sql` | 18–113 | `device_playlists` · `device_sync_history` · `device_tracks` · `device_types` · `devices` |
| `.ai/.sql/mysql/coremusic_system.sql` | 138–159 | `system_bluetooth_devices` |
| `shared/src/Device/DeviceDetector.php` | 49–72 | `detect()` — UA/viewport/X-Device-Type → 7 tip |
| `assets.coremusic.net/js/devices.config.js` | 1–43 | CSS haritası SSOT — HOME/AUTH/VIEW + ALL |
| `shared/src/AI/AIWorkflow.php` | 147–174 | `autoEQWorkflow` — device_id + oda profili + cache |

**Kardeş dosyalar:** [[device-management-dogrulama]] · [[device-management-web-arastirma]] · [[device-management-ornekler]] · [[device-management-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
