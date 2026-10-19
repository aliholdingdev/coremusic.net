---
node_id: "K008-device-management-dogrulama"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-device-management-kapsam"
  - "K008-device-management-web-arastirma"
evidence:
  - "satır D-DM1…D-DM16 — 2026-10-15 kaydı"
  - "repo kanıtları: coremusic_media.sql 18–113 · coremusic_system.sql 138–159 · DeviceDetector.php 49–72 · devices.config.js 1–43 · AIWorkflow.php 147–174"
validation: "2026-10-15 — 16 iddia: 12 Dosya/Satır, 1 web pointer, 2 tasarım/negatif ⚠️, 1 tarama boşluğu ⚠️"
open_questions:
  - "cihaz kayıt/senkron sunucu akışı (PHP) → tarama yapılmadı → ⚠️ VERIFICATION REQUIRED"
  - "DLNA/UPnP keşfi → diskte kod yok → PLANNED (W-2…W-8)"
group: kanit
---

# device-management — Doğrulama Defteri (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> Web kanıtları URL ile değil [[device-management-web-arastirma]] §1 pointer'ı ile verilir (C4).
> Durumlar: ✅ doğrulandı · ⚠️ VERIFICATION REQUIRED · **UNKNOWN**.

## §1 İDDİA TABLOSU

| # | İddia | Repo kanıtı (Dosya/Satır) | Web kanıtı | Tür | Durum |
|---|-------|---------------------------|-----------|-----|-------|
| D-DM1 | aggregate adayı `Device (çalma hedefi)` — master §3.1 satır 95 | domain-master.md satır 95 | — | tasarım | ⚠️ ADR-097 K4 |
| D-DM2 | device-management **domain event dosyası GÖRÜLMEDİ** (negatif kanıt, keşif 2026-10-12) | domain-master.md §3.4 satır 126 | — | negatif | ⚠️ |
| D-DM3 | `DeviceDetector::detect()` — UA + viewport + `X-Device-Type` → 7 tip (`phone\|tablet\|embedded\|laptop\|desktop\|4k-tv\|4k-monitor`); kiosk başlığı satır 62–65; Smart TV satır 69–72 | `shared/src/Device/DeviceDetector.php` satır 49–72 | — | T4 | ✅ |
| D-DM4 | `devices.config.js` `ALL` = 7 tip — DeviceDetector satır 55 ile **aynı liste** | `assets.coremusic.net/js/devices.config.js` satır 43 | — | T4 | ✅ |
| D-DM5 | 4k-tv ve 4k-monitor **aynı** `d-4k.css` dosyasına gider ("4K tek SSOT") | aynı dosya satır 12, 19–20 | — | T4 | ✅ |
| D-DM6 | AUTH_CSS 7 cihaz varyantı (`d-auth-embedded` … `d-auth-4k-monitor`) | aynı dosya satır 24–31 | — | T4 | ✅ |
| D-DM7 | VIEW_CSS 4 görünüm: home/pro/studio/car | aynı dosya satır 35–39 | — | T4 | ✅ |
| D-DM8 | `devices` — `binary(16)` ID'ler, `device_uuid` UNIQUE (satır 109), `is_primary` (satır 101), fingerprint/ip/mac (satır 95–99), COMMENT "synchronization, streaming, and multi-device access" (satır 113) | `.ai/.sql/mysql/coremusic_media.sql` satır 89–113 | — | T4 | ✅ |
| D-DM9 | `device_types` — `platform` enum **7 değer** (satır 72), `has_screen/audio_output/bluetooth/wifi` (satır 73–76), UNIQUE name (satır 84), soft delete (satır 81–82) | aynı dosya satır 68–87 | — | T4 | ✅ |
| D-DM10 | `device_playlists` — `sync_status` enum 4 değer (satır 22), UNIQUE (device,playlist) (satır 28), COMMENT "offline access" (satır 31) | aynı dosya satır 18–31 | — | T4 | ✅ |
| D-DM11 | `device_sync_history` — `sync_type` enum 4 değer (satır 36), tracks_synced/failed/bytes/duration_ms (satır 38–41), COMMENT "debugging and analytics" (satır 49) | aynı dosya satır 33–49 | — | T4 | ✅ |
| D-DM12 | `device_tracks` — `file_format` enum 5 değer (satır 57), UNIQUE (device,music) (satır 63), COMMENT "offline playback" (satır 66) | aynı dosya satır 51–66 | — | T4 | ✅ |
| D-DM13 | `system_bluetooth_devices` — `device_type` enum 7 değer (satır 141), `codec` enum 7 değer (satır 144), `mac_address` UNIQUE (satır 156), `id` comment "UUID v7" (satır 139) | `.ai/.sql/mysql/coremusic_system.sql` satır 138–159 | — | T4 | ✅ |
| D-DM14 | `autoEQWorkflow` — `device_id` istekten (satır 151), `dispatchTask('eq-optimization', …)` (satır 154), oda profili (satır 157–161), `optimizeEQ` (satır 164), `analyzeHardware($deviceId)` (satır 167–170), L2 cache 3600 (satır 173–174) | `shared/src/AI/AIWorkflow.php` satır 147–174 | — | T4 | ✅ |
| D-DM15 | ağ cihazı interop referansları: DNS-SD · DLNA · UPnP · MS-DLNHND · OCF MediaRenderer · CTA | — | [[device-management-web-arastirma]] §1 W-1…W-9 | T3 | ✅ HTTP 200 · 2026-10-15 |
| D-DM16 | cihaz kayıt/senkron **sunucu akışı** (PHP servis/controller) — bu oturumda tarama yapılmadı | — | — | tarama | ⚠️ VERIFICATION REQUIRED |

## §2 NOTLAR

- D-DM2 + D-DM16 birlikte okunmalı: event yok + akış kanıtlanmadı → `device-management`
  **alan olarak** kanıtlanmıştır (tablo + tespit + EQ), **akış olarak** değil.
- D-DM8…D-DM13'teki tüm ID'ler `binary(16)`'dır — bu, bu oturumda
  `int unsigned` bulunan [[recommendation-dogrulama]] D-RC9'un **karşı kutbundadır**
  (iki farklı şema nesli).
- D-DM9 `device_types.platform` (windows/linux/macos/android/ios/rpi/other) ile
  D-DM13 `system_bluetooth_devices.device_type` (speaker/headphone/car/phone/tablet/laptop/other)
  **farklı enum kümeleridir** — aynı "cihaz tipi" kavramının iki ayrı modeli;
  birleştirme kararı verilmedi.
- `shared/src/Device/` dizininde 4 dosya var (glob 2026-10-15):
  `DeviceDetector` · `DeviceManager` · `DeviceCssMap` · `DeviceRenderer` —
  yalnız `DeviceDetector` içeriği bu oturumda okundu.

**Kardeş dosyalar:** [[device-management-kapsam]] · [[device-management-web-arastirma]] · [[device-management-ornekler]] · [[device-management-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
