---
node_id: "K008-device-management-bilesenler"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-device-management-kapsam"
  - "K008-device-management-ornekler"
evidence:
  - "shared/src/Device/ glob (2026-10-15): 4 dosya — DeviceDetector · DeviceManager · DeviceCssMap · DeviceRenderer"
  - ".ai/.sql/mysql/coremusic_media.sql satır 18–113 · coremusic_system.sql satır 138–159"
  - "domain-master.md satır 95 · §3.4 satır 126"
validation: "2026-10-15 — §1 mevcut kademe (1 kod + 6 tablo + 1 workflow) + §2 altı PLANNED satır"
open_questions:
  - "aggregate + invariant → ADR-097 K4"
  - "device fingerprint stratejisi → belge yok → ⚠️"
group: mimari
---

# device-management — Bileşenler (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> §1 mevcut olanı, §2 ise **PLANNED** kademe-3 planını taşır. Disk'te karşılığı
> olmayan sınıf/dosya adı "var" gibi sunulmaz.

## §1 MEVCUT KADEME

| # | Bileşen | Kanıt | Durum |
|---|---|---|---|
| 1 | `DeviceDetector` — UA/viewport/X-Device-Type → 7 tip | `shared/src/Device/DeviceDetector.php` satır 49–72 | ✅ DISKTE (içerik okundu) |
| 2 | `DeviceManager` · `DeviceCssMap` · `DeviceRenderer` | `shared/src/Device/` glob (2026-10-15) | ✅ dosya var — içerik ⚠️ okunmadı |
| 3 | `devices.config.js` — HOME/AUTH/VIEW CSS haritası + ALL | `assets.coremusic.net/js/devices.config.js` satır 1–43 | ✅ DISKTE |
| 4 | `devices` + `device_types` — kayıt ve tip modeli | `.ai/.sql/mysql/coremusic_media.sql` satır 68–113 | ✅ DISKTE |
| 5 | `device_playlists` + `device_tracks` + `device_sync_history` — çevrimdışı senkron | aynı dosya satır 18–66 | ✅ DISKTE |
| 6 | `system_bluetooth_devices` — BT kayıt | `.ai/.sql/mysql/coremusic_system.sql` satır 138–159 | ✅ DISKTE |
| 7 | `AIWorkflow::autoEQWorkflow` — cihaza özel EQ akışı | `shared/src/AI/AIWorkflow.php` satır 147–174 | ✅ DISKTE |
| 8 | Domain event (outbound) | — | ❌ YOK — master §3.4 satır 126 |
| 9 | Cihaz kayıt/senkron sunucu servisi (PHP) | — | ⚠️ VERIFICATION REQUIRED (D-DM16) |

## §2 KADEME-3 PLANI (PLANNED — ADR-097 K4 bekliyor)

| # | Bileşen (aday) | Sorumluluk | Dayanak | Durum |
|---|---|---|---|---|
| 1 | Device (aggregate — ad master satır 95) | cihaz kaydı + hedef seçimi yaşam döngüsü | [[device-management-kapsam]] §3 satır 2 | ⚠️ PLANNED |
| 2 | Domain event (`DeviceRegistered` / `DeviceSynced`) | senkron + audit akışı | master §3.4 satır 126 boşluğu | ⚠️ PLANNED — needs ADR |
| 3 | Senkron servisi | `device_playlists` → `device_tracks` akışının sahibi | 3 tablo var, akış kanıtlanmadı | ⚠️ PLANNED — sahibi UNKNOWN |
| 4 | DLNA/UPnP MediaRenderer keşfi | ağdaki cihazları bulma | [[device-management-web-arastirma]] W-2…W-8 | ⚠️ PLANNED — diskte kod YOK |
| 5 | Fingerprint stratejisi | `device_fingerprint` alanının üretilmesi/teşhisi | satır 95 (alan var, strateji belgesi yok) | ⚠️ PLANNED |
| 6 | Enum birleştirme kararı | `device_types.platform` vs `system_bluetooth_devices.device_type` | [[device-management-dogrulama]] §2 | ⏸ BEKLEMEDE — data-engine kararı |

**Yasak:** §2 adları **tasarım adayıdır**; disk'te karşılığı doğrulanmadan "mevcut"
gibi raporlanmaz. Geçiş: kod diske yazıldı + ADR-097 K4 kilitlendi → `active`.
`AIWorkflow` üzerinde K008 kaynaklı değişiklik yetkisi yoktur (bkz recommendation
§4.3 — uygulama katmanı).

**Kardeş dosyalar:** [[device-management-kapsam]] · [[device-management-dogrulama]] · [[device-management-web-arastirma]] · [[device-management-ornekler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
