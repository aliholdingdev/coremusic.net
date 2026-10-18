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

## §5 AÇIK SORULAR (R-DM1…R-DM5)

| # | Soru | Kanıt / işaret | Kapanış için gereken |
|---|---|---|---|
| R-DM1 | `devices.device_uuid` (s.94, varchar 255) ile `device_types.id` (s.69, binary(16)) neden farklı tip ailelerinde? | coremusic_media.sql s.94 vs s.69 | Kimlik stratejisi kararı — varchar UUID vs binary(16) |
| R-DM2 | `system_bluetooth_devices` (coremusic_system) ile `devices` (coremusic_media) arasındaki ilişki nedir? | iki ayrı şema · FK **YOK** | Eşleme kuralı — mac_address mi, uuid mi? |
| R-DM3 | Senkron motoru (device_tracks → cihaz) hangi servis? | 3 senkron tablosu var; motor kodu kanıtlanmadı | Backend taraması → ⚠️ VERIFICATION REQUIRED |
| R-DM4 | `DeviceDetector` (PHP) ile `devices` tablosu bağlantılı mı? | DeviceDetector s.49–72 UA/viewport → 7 tip; tablo `device_types` platform enum 7 | Tespit edilen tip DB'de kayıt açıyor mu? — tarama |
| R-DM5 | `has_screen`/`has_audio_output`/`has_bluetooth`/`has_wifi` (s.73–76) bayrakları kim tarafından güncellenir? | `device_types` sabit tablo (tanım) | Bayraklar statik tanım mı, dinamik mi — tasarım sorusu |

> `status: draft` ve §3 yasağı bu eklemle **kapanmaz**; kapanış ADR-097 K4'e bağlıdır.

## §6 ŞEMA KANITLARI: CİHAZ ÇEKİRDEĞİ

> **Kaynak:** `.ai/.sql/mysql/coremusic_media.sql` (device_* 5 tablo) +
> `.ai/.sql/mysql/coremusic_system.sql` (bluetooth) + kod kanıtları (§4 tablosu).

### §6.1 Çekirdek tablo envanteri

| Tablo | Satır | Rol | Kanıt notu |
|---|---|---|---|
| `devices` | s.89–113 | Kullanıcı cihazı | `uk_devices_uuid` (s.109) |
| `device_types` | s.68–87 | Cihaz tipi tanımı | `uk_device_types_name` (s.84) |
| `device_tracks` | s.51–66 | Cihaz↔parça senkron | UNIQUE (device,music) (s.63) |
| `device_playlists` | s.18–31 | Cihaz↔playlist senkron | UNIQUE (device,playlist) (s.28) |
| `device_sync_history` | s.33–49 | Senkron geçmişi (hata/ayırt etme) | 4 dizin (s.45–48) |
| `system_bluetooth_devices` | s.138–160 | BT cihaz kaydı (system şeması) | `uk_bt_devices_mac` (s.156) |

### §6.2 `devices` — kimlik ve durum kanıtları

| Kolon | Satır | Tip / değer | Kanıt notu |
|---|---|---|---|
| `device_uuid` | s.94 | varchar(255) NOT NULL | UNIQUE (s.109) — tip ailesi R-DM1 |
| `device_fingerprint` | s.95 | varchar(255) NULL | Parmak izi — UNIQUE **YOK** |
| `mac_address` | s.99 | varchar(17) NULL | UNIQUE **YOK** (devices'ta!) |
| `is_primary` | s.101 | tinyint(1) default 0 | Hedef cihaz — tekil control **YOK** |
| `is_active` | s.100 | tinyint(1) default 1 | Aktiflik |
| `device_type_id` | s.92 | binary(16) NOT NULL | FK **YOK** (s.89–113'te CONSTRAINT yok) |

### §6.3 Senkron tabloları — UNIQUE kanıtları

| Tablo | UNIQUE | Satır | Enum kanıtı |
|---|---|---|---|
| `device_playlists` | (device_id,playlist_id) | s.28 | `sync_status` enum 4 (synced/pending/failed/disabled) s.22 |
| `device_tracks` | (device_id,music_id) | s.63 | `sync_status` enum 4 (synced/pending/failed/deleted) s.58 · `file_format` enum 5 s.57 |
| `device_sync_history` | — (UNIQUE yok) | s.33–49 | `sync_type` enum 4 (full/incremental/playlist/metadata) s.36 · `sync_status` enum 4 (started/completed/failed/cancelled) s.37 |

> **Senkron enum dilleri çelişkili:** `sync_status` üç tabloda üç farklı küme
> (§6.3) — `disabled` vs `deleted` vs `cancelled`. Tek bir domain dili
> **yoktur**; VO `from()` fabrikası hangi kümeyle kurulacak — açık soru (§12 R-DM8).

### §6.4 `system_bluetooth_devices` — BT kanıtları

| Kolon | Satır | Tip / değer | Kanıt notu |
|---|---|---|---|
| `mac_address` | s.142 | varchar(17) NOT NULL | UNIQUE (s.156) — BT'de tekil |
| `codec` | s.144 | enum 7 (sbc/aac/aptx/aptx_hd/ldac/lhdc/other) default aac | Ses codec kümesi |
| `bluetooth_version` | s.143 | varchar(10) default '5.0' | Serbest metin — enum değil |
| `device_type` | s.141 | enum 7 (speaker/headphone/car/phone/tablet/laptop/other) | BT tip kümesi |
| `is_paired/is_connected/is_trusted/auto_connect` | s.145–148 | tinyint(1) NOT NULL | 4 bayrak |
| `metadata` | s.150 | json NULL | Ek veri |

## §7 INVARIANT ADAYLARI (SQL KANITLI — KİLİTLENMEMİŞ ÖNERİ)

| # | SQL kanıtı (satır) | Ne garanti eder | Invariant adayı (kanıt DEĞİL) |
|---|---|---|---|
| DM-P1 | s.109 `uk_devices_uuid` | Aynı device_uuid iki kayıtta olamaz | Cihaz kaydı global tekil |
| DM-P2 | s.84 `uk_device_types_name` | Tip adı tekil | Aynı tip adı iki kez tanımlanamaz |
| DM-P3 | s.28 UNIQUE (device,playlist) | Cihaz başına playlist tekil | Çift senkron satırı ret |
| DM-P4 | s.63 UNIQUE (device,music) | Cihaz başına parça tekil | Çift senkron satırı ret |
| DM-P5 | s.156 `uk_bt_devices_mac` | BT cihazı MAC'te tekil | Aynı MAC iki kez kaydedilemez |
| DM-P6 | s.101 `is_primary` default 0 · UNIQUE **YOK** | Bayrak var ama tekil DB'de **yok** | Kullanıcı başına en fazla 1 primary — DB güvencesi YOK, domain kontrolü önerisi |
| DM-P7 | s.92 `device_type_id` NOT NULL · FK **YOK** | Tip zorunlu ama bütünlük DB'de yok | Uygulama doğrulamalı — DB güvencesi YOK |
| DM-P8 | s.99 `mac_address` varchar(17) NULL · UNIQUE **YOK** (devices) | MAC opsiyonel + tekrar serbest | BT tablosunda tekil (DM-P5); `devices`'ta **değil** — bilinçli fark mı? (R-DM6) |
| DM-P9 | s.72 `platform` enum 7 | Platform kapalı küme | Geçersiz platform yazılamaz |
| DM-P10 | s.144 `codec` enum 7 | Codec kapalı küme | Geçersiz codec yazılamaz |
| DM-P11 | s.36–37 iki enum (sync_type 4 · sync_status 4) | Senkron kaydı tip+durum kapalı küme | Geçersiz kombinasyon yazılamaz — ama geçiş matrisi DB'de YOK |
| DM-P12 | s.141 `device_type` enum 7 (BT) ↔ s.72 `platform` enum 7 (device_types) | İki ayrı "tip" dili | İki enum eşleşmiyor (speaker vs windows) — eşleme tablosu YOK, çelişki §12 R-DM7 |

### §7.1 Uyumsuzluk / açık noktalar (kanıt + öneri ayrımı)

| İddia | Kanıt | Durum |
|---|---|---|
| `devices.device_uuid` binary(16) | s.94 varchar(255) | ⚠️ TİP AİLESİ FARKLI (R-DM1) |
| BT ↔ devices ilişki | iki şema · FK yok | ⚠️ EŞLEME YOK (R-DM2) |
| `is_primary` tekil | s.101 · UNIQUE yok | ⚠️ DB güvencesi YOK (DM-P6) |
| `sync_status` tek dil | §6.3 üç farklı enum kümesi | ⚠️ ÇELİŞKİLİ DİL |
| BT `device_type` ↔ `platform` | s.141 vs s.72 — 7'ye 7 ama farklı değerler | ⚠️ EŞLEŞMEYEN KÜMELER (DM-P12) |

## §8 VALUE OBJECT ADAYLARI (ÖNERİ — DEPODA VO YOK)

> **TASARIM ÖNERİSİ — kanıt DEĞİL.** Adaylar şema kolonlarından türetilmiştir.

| # | VO | Dayanak | Değer kümesi / biçim | Önerilen davranış |
|---|---|---|---|---|
| V-DM1 | `Platform` | s.72 enum(7) | windows/linux/macos/android/ios/rpi/other | `from(string)` fabrika |
| V-DM2 | `DeviceUuid` | s.94 varchar(255) | UUID biçim | Biçim doğrulama — tip ailesi R-DM1 |
| V-DM3 | `MacAddress` | s.99 varchar(17) · s.142 | `AA:BB:CC:DD:EE:FF` | Biçim doğrulama (17 karakter) |
| V-DM4 | `SyncStatus` | §6.3 üç küme | ⚠️ TEK VO KURULAMAZ — dil çelişkili | Üç ayrı VO mu, birleşik küme mi — §12 R-DM8 |
| V-DM5 | `BtCodec` | s.144 enum(7) | sbc/aac/aptx/aptx_hd/ldac/lhdc/other | `from(string)` + `isLossy()` (sbc/aac lossy — öneri, kanıt DEĞİL) |
| V-DM6 | `BtDeviceType` | s.141 enum(7) | speaker/headphone/car/phone/tablet/laptop/other | `from(string)` |
| V-DM7 | `SyncType` | s.36 enum(4) | full/incremental/playlist/metadata | `from(string)` |

### §8.1 VO test edilebilirlik notları

| VO | Karakteristik test fikri (öneri) |
|---|---|
| V-DM1 | 8. değer `from()` ret |
| V-DM3 | `GG:HH:...` (geçersiz hex) ret · 16 karakter ret |
| V-DM5 | 8. değer ret |
| V-DM4 | `from('deleted')` — device_tracks kümesinde geçerli, device_playlists kümesinde **geçersiz**: çelişki testi |

## §9 DOMAIN OLAY ENVANTERİ

### §9.1 Var olan olaylar — **YOK**

`Events/Domain/` 10 dosyanın hiçbiri cihaz olayı değildir (master §3.4 satır 126
kaydı: event boşluğu). `device-management`'in 2026-10-12 keşfinde domain event'i
**0**'dır (§3 satır 6).

### §9.2 Eksik olay adayları (öneri — uydurma YASAK)

| # | Önerilen olay | Tetikleyici kanıtı | Mevcut durum |
|---|---|---|---|
| E-DM1 | `device.registered` | `devices` INSERT (s.89–113) | ❌ DOSYA YOK |
| E-DM2 | `device.primary_changed` | `is_primary` (s.101) değişimi | ❌ DOSYA YOK |
| E-DM3 | `sync.completed` | `device_sync_history.sync_status='completed'` (s.37) | ❌ DOSYA YOK |
| E-DM4 | `sync.failed` | s.37 + `error_message` (s.42) | ❌ DOSYA YOK |
| E-DM5 | `bluetooth.paired` | `is_paired` (s.145) değişimi | ❌ DOSYA YOK |
| E-DM6 | `bluetooth.connected` | `is_connected` (s.146) değişimi | ❌ DOSYA YOK |

> **Kural:** E-DM1…E-DM6, `domain-bilesenler.md` §12 protokolüne tabidir.

## §10 REPOSITORY SÖZLEŞMESİ ÖNERİSİ (TASARIM ÖNERİSİ — kanıt DEĞİL)

| # | Yöntem (öneri) | Dönüş | Bağlı invariant / kanıt |
|---|---|---|---|
| RM-DM1 | `registerDevice(string $userId, array $data): array` | cihaz | DM-P1 |
| RM-DM2 | `findByUuid(string $uuid): ?array` | cihaz veya null | DM-P1 |
| RM-DM3 | `setPrimary(string $userId, string $deviceId): void` | — | DM-P6 (tekil domain kontrolü) |
| RM-DM4 | `syncTracks(string $deviceId, array $musicIds): array` | senkron sonucu | DM-P4 |
| RM-DM5 | `syncHistory(string $deviceId, int $page): array` | sayfa | s.45–48 dizinler |
| RM-DM6 | `pairBluetooth(string $mac, string $codec): array` | BT kaydı | DM-P5, V-DM5 |
| RM-DM7 | `listByUser(string $userId): array` | cihaz listesi | s.110 dizin |
| RM-DM8 | `markSyncResult(string $historyId, string $status, ?string $error): void` | — | DM-P11 geçiş matrisi (öneri) |

## §11 KOMŞU SINIR DETAYI (§2 GENİŞLETMESİ)

| Komşu | Yön | Alan/kanıt | Ayrım kuralı |
|---|---|---|---|
| user | device ← user | `user_id` (s.91) | Kimlik user'de |
| playback | device ↔ playback | Çalma hedefi seçimi (anlık) | Keşif/eşleşme burada; anlık seçim playback |
| media | device → media | `file_path/file_size/file_format` (s.55–57) — dosya bilgisi | Dosyanın kendisi media'nın |
| system (K005) | device ↔ system | `system_bluetooth_devices` iki şema arası | Eşleme YOK (R-DM2) |
| discovery | device ← discovery | `DeviceDetector` UA tespiti | Tespit → keşif girdisi olabilir (R-DM4) |
| AI (autoEQ) | device ← AI | AIWorkflow s.147–174 device_id + oda profili | EQ profili cihaza bağlı |

## §12 AÇIK SORULAR (§5 GENİŞLETME — R-DM6…R-DM10)

| # | Soru | Kanıt / işaret | Kapanış için gereken |
|---|---|---|---|
| R-DM6 | `devices.mac_address` (s.99) neden UNIQUE değil, BT tablosunda ise UNIQUE (s.156)? | iki tablo | Bilinçli fark mı — tasarım niyeti |
| R-DM7 | BT `device_type` (s.141) ↔ `platform` (s.72) eşlemesi? | 7'ye 7 farklı küme | Eşleme tablosu tasarımı |
| R-DM8 | `sync_status` üç küme (§6.3) nasıl birleştirilir? | 3 enum farklı | Tek VO kararı — ADR |
| R-DM9 | `device_fingerprint` (s.95) UNIQUE değil — parmak izi çakışması nasıl çözülür? | s.95 | Çakışma stratejisi |
| R-DM10 | `device_sync_history` neden UNIQUE'siz? (diğer ikisinde var) | s.33–49 | Geçmiş tablosu olması kasıtlı — onay |

## §13 KARAKTERİSTİK TEST SENARYOLARI (ÖNERİ)

| # | Senaryo | Beklenen | Bağlı kural |
|---|---|---|---|
| DM-T1 | Aynı device_uuid ile ikinci kayıt | UNIQUE ret | DM-P1 |
| DM-T2 | platform='web' (8. değer) | VO ret | V-DM1 |
| DM-T3 | `from('deleted')` sync_status (playlist tablosu) | VO ret (küme dışı) | V-DM4, R-DM8 |
| DM-T4 | Aynı (device,playlist) ikinci senkron | UNIQUE ret | DM-P3 |
| DM-T5 | BT MAC tekrar kaydı | UNIQUE ret | DM-P5 |
| DM-T6 | Aynı kullanıcıda ikinci `is_primary=1` | Domain ret (DB retmez!) | DM-P6 |
| DM-T7 | `device_type_id` var olmayan tip | DB ret **DEĞİL** (FK yok!) — uygulama ret | DM-P7 |

## §14 ÖLÇÜM VE KAPANIŞ (BU TUR)

| # | Öğe | Değer |
|---|---|---|
| 1 | Dosya | `device-management-kapsam.md` |
| 2 | Başlangıç satırı | 81 |
| 3 | Hedef | ≥500 |
| 4 | Kapsam | §5–§28 |
| 5 | Yasak ihlali | 0 |

## §15 BC ETKİLEŞİM MATRİSİ (YÖN + NİYET)

| # | Komşu BC | Yön | Niyet |
|---|---|---|---|
| 1 | user | ← | Cihaz-sahibi ilişkisi |
| 2 | playback | ↔ | Çalma hedefi |
| 3 | media | → | Dosya senkronu |
| 4 | discovery | ↔ | UA tespiti (R-DM4) |
| 5 | system | ↔ | BT cihazları (R-DM2) |
| 6 | AI | ← | autoEQ device_id (AIWorkflow s.147–174) |

## §16 ÖRNEK ANLATIM (TUTARLILIK OKUMASI)

Kullanıcı yeni telefon kaydeder → `devices` INSERT (`device_uuid` UNIQUE —
DM-P1). `DeviceDetector` UA'dan tip çıkarır (s.49–72, 7 tip) — fakat tespit ile
DB kaydı arasındaki bağlantı **kanıtlanmadı** (R-DM4). Kullanıcı playlist'ini
senkronlar → `device_playlists` satırı (`sync_status='pending'`, UNIQUE — DM-P3);
senkron biter → `device_sync_history` INSERT (`completed`, E-DM3 olayı **yok**).
Bluetooth kulaklık eşleşir → `system_bluetooth_devices` (MAC UNIQUE — DM-P5);
`is_paired=1` (s.145); `bluetooth.paired` olayı **yok** (E-DM5). BT kaydı ile
`devices` kaydı **eşleşmez** — iki şema arası eşleme YOK (R-DM2).

## §17 OKUMA YOLU VE KOMŞU DOSYA DAVET LİSTESİ

### §17.1 Bu dosyadan okuma sırası

1. §2–§3 · 2. §6–§7 (şema + invariant) · 3. §7.1 + §12 (çelişkiler + açık sorular)

### §17.2 Çağrılan komşu dosyalar

[[playback-kapsam]] (hedef seçimi) · [[media-kapsam]] (dosya bilgisi) ·
[[discovery-kapsam]] (UA tespiti R-DM4) · [[domain-master]] (§3.1 satır 95 · §3.4 satır 126)

## §18 OKUYUCU UYARILARI VE ÖLÇÜM YÖNTEMİ

- `status: draft` — `Device` aggregate'i kilitlenmemiştir (ADR-097 K4).
- §7/§8/§10/§13 **öneridir, kanıt değildir**.
- Ölçüm `[System.IO.File]::ReadAllLines` ile; `Measure-Object -Line` YASAK.

## §19 KARAR MATRİSİ (BU DOSYA ÖLÇÜSÜNDE)

| # | Karar | Durum |
|---|---|---|
| 1 | Aggregate `Device` | ⚠️ TASARIM İDDİASI — master §3.1 satır 95 |
| 2 | Domain event | ❌ 0 (§9.1) |
| 3 | `sync_status` dili | ⚠️ ÇELİŞKİLİ — 3 küme (R-DM8) |
| 4 | BT ↔ devices eşlemesi | ⚠️ YOK (R-DM2) |
| 5 | `is_primary` tekil | ⚠️ DB güvencesi YOK (DM-P6) |

## §20 ATIF VE KAYNAK ÇİZELGESİ

| İddia | Kaynak | Tür |
|---|---|---|
| 5 senkron/cihaz tablosu | `coremusic_media.sql` s.18–113 | ✅ disk kanıtı |
| BT cihaz tablosu | `coremusic_system.sql` s.138–160 | ✅ disk kanıtı |
| UA → 7 tip | `DeviceDetector.php` s.49–72 | ✅ disk kanıtı (§4) |
| CSS haritası SSOT | `devices.config.js` s.1–43 | ✅ disk kanıtı (§4) |
| autoEQ device_id | `AIWorkflow.php` s.147–174 | ✅ disk kanıtı (§4) |
| Device aggregate | `domain-master.md` satır 95 | ⚠️ TASARIM İDDİASI |
| Event boşluğu | master §3.4 satır 126 | ✅ negatif kanıt |
| Repository/VO/invariant | Bu dosya §7–§10, §13 | 🔶 TASARIM ÖNERİSİ |

## §21 TUR KAYDI (2026-10-17)

| # | Öğe | Değer |
|---|---|---|
| 1 | Eklenen bölümler | §5–§21 |
| 2 | Invariant adayı | DM-P1…DM-P12 |
| 3 | VO adayı | V-DM1…V-DM7 |
| 4 | Olay adayı | E-DM1…E-DM6 |
| 5 | Açık soru | R-DM1…R-DM10 |
| 6 | En kritik bulgu | `sync_status` üç çelişkili enum kümesi (§6.3) + BT↔devices eşleme yok (R-DM2) |

## §22 SENKRON DURUM MAKİNESİ (ŞEMA KANITLI — GEÇİŞ MATRİSİ ÖNERİ)

> §6.3'teki üç `sync_status` kümesi ayrı ayrı kanıttır; buradaki **geçiş sırası**
> birleştirilmiş **öneridir** (tek VO kurulamaz — R-DM8).

### §22.1 `device_tracks.sync_status` (s.58: synced/pending/failed/deleted)

| # | Geçiş | Tetikleyici kanıtı | Durum |
|---|---|---|---|
| 1 | (yeni satır) → `pending` | s.58 default 'pending' | ✅ şema |
| 2 | `pending` → `synced` | `synced_at` (s.59) set edilir | ✅ kolon kanıtı |
| 3 | `pending` → `failed` | — (hata kolonu device_tracks'ta YOK) | ⚠️ hata alanı YOK — history'de (s.42) |
| 4 | `synced` → `deleted` | soft-silme mi hard mı — `is_deleted` YOK | ⚠️ belirsiz |

### §22.2 `device_sync_history.sync_status` (s.37: started/completed/failed/cancelled)

| # | Geçiş | Tetikleyici kanıtı | Durum |
|---|---|---|---|
| 1 | (INSERT) → `started` | s.37 NOT NULL — başlangıç | ✅ şema |
| 2 | `started` → `completed` | `tracks_synced` (s.38) · `duration_ms` (s.41) | ✅ kolon kanıtı |
| 3 | `started` → `failed` | `error_message` (s.42) · `tracks_failed` (s.39) | ✅ kolon kanıtı |
| 4 | `started` → `cancelled` | — (iptal eden alan YOK) | ⚠️ iptal mekanizması kanıtsız |

### §22.3 `device_playlists.sync_status` (s.22: synced/pending/failed/disabled)

| # | Geçiş | Tetikleyici kanıtı | Durum |
|---|---|---|---|
| 1 | (yeni satır) → `pending` | s.22 default 'pending' | ✅ şema |
| 2 | `pending` → `synced` | `last_synced_at` (s.24) | ✅ kolon kanıtı |
| 3 | `pending` → `failed` | — (hata kolonu YOK) | ⚠️ |
| 4 | herhangi → `disabled` | kullanıcı senkronu kapatır (varsayım) | ⚠️ TETİKLEYİCİ KANITSIZ |

> **Birleştirme notu (R-DM8):** üç küme ortak çekirdek = `pending · synced ·
> failed`; farklar `disabled` (playlist) · `deleted` (tracks) · `cancelled`
> (history) · `started` (history) — tek VO kurulursa hangi alt-küme hangi
> bağlamda geçerli, karar ADR-097 K4'tedir.

## §23 KOD KANITLARI DETAYI (§4 TABLOSU GENİŞLETME)

### §23.1 `DeviceDetector.php` s.49–72 (7 tip)

| # | Kanıt | Satır | Not |
|---|---|---|---|
| 1 | `detect()` yöntemi | s.49 | UA/viewport/X-Device-Type üç sinyal |
| 2 | 7 tip çıktısı | s.49–72 | `device_types.platform` enum 7 ile (s.72) sayısal uyum — **değer uyumu kanıtlanmadı** (R-DM4) |

> **Sayısal uyum tesadüf mü, eşleme mi?** PHP 7 tip ↔ SQL enum 7 — değerlerin
> birebir eşleşip eşleşmediği bu turda okunmadı → ⚠️ VERIFICATION REQUIRED.

### §23.2 `devices.config.js` s.1–43 (CSS haritası SSOT)

| # | Kanıt | Satır | Not |
|---|---|---|---|
| 1 | HOME/AUTH/VIEW + ALL grupları | s.1–43 | Frontend cihaz haritası — DB `devices` ile ilişkisi kanıtsız |
| 2 | Bağımlılık | — | JS haritası statik; DB kaydı dinamik — iki ayrı "cihaz" dili |

### §23.3 `AIWorkflow.php` s.147–174 (autoEQ)

| # | Kanıt | Satır | Not |
|---|---|---|---|
| 1 | `autoEQWorkflow` | s.147–174 | device_id + oda profili + cache |
| 2 | device_id kullanımı | s.147–174 | AI ↔ cihaz bağımlılığı — eq profili cihaz bazlı |

## §24 AKIŞ SENARYOLARI (TASARIM ÖNERİSİ — kanıt DEĞİL)

### §24.1 Cihaz kaydı

`RM-DM1 registerDevice` → `V-DM2 DeviceUuid.from` (biçim ret) → `uk_devices_uuid`
çakışması ret (DM-P1) → `device.registered` olayı **yok** (E-DM1).

### §24.2 Primary değişimi

`RM-DM3 setPrimary` → domain kontrolü: kullanıcıda zaten primary varsa önce
kaldır (DM-P6 — DB retmez!) → `device.primary_changed` olayı **yok** (E-DM2).

### §24.3 Parça senkronu

`RM-DM4 syncTracks` → `device_tracks` satırları `pending` (s.58) → senkron
motoru (**kanıtsız**, R-DM3) → `synced` + `synced_at` → `device_sync_history`
INSERT `started` → `completed` (E-DM3 olayı **yok**).

### §24.4 BT eşleştirme

`RM-DM6 pairBluetooth` → MAC biçimi doğrulama (V-DM3) → `uk_bt_devices_mac`
çakışması ret (DM-P5) → `is_paired=1` (s.145) → `bluetooth.paired` olayı **yok**
(E-DM5) → `devices` tablosunda karşılık **açılmaz** (eşleme yok — R-DM2).

## §25 SONRAKİ TUR İŞ KALEMLERİ

1. R-DM2 (BT ↔ devices eşlemesi) — kritik boşluk, sahiplik kararı.
2. R-DM8 (`sync_status` üç küme) — tek VO kararı ADR.
3. R-DM4 (DeviceDetector 7 tip ↔ platform enum değer uyumu) — tarama.
4. Senkron motoru kodu (R-DM3) — backend taraması.
5. §14 senaryolarının `test-writer` kapsamına devri.

## §26 TUR SONU KONTROL LİSTESİ

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | Authority eski satır | ✅ `2026-10-15` aynen duruyor |
| 2 | Yeni Authority | ✅ `2026-10-17` append |
| 3 | Frontmatter | ✅ korundu |
| 4 | Bölüm silme | ❌ YOK — §1–§4 duruyor, §5–§26 append |
| 5 | Yer tutucu | 0 |
| 6 | Uydurma satır numarası | 0 — tüm `s.NN` okunan SQL'den |
| 7 | Numara çakışması | ✅ düzeltildi (repository = RM-DM*, açık soru = R-DM*) |
| 8 | Ölçüm | `ReadAllLines().Length` |

## §27 ÇAPRAZ REFERANS VE BAĞIMLILIK MATRİSİ

| # | Bu dosya referansı | Hedef | İlişki |
|---|---|---|---|
| 1 | §6.2 `device_uuid` (s.94) | `RM-DM2 findByUuid` | Kimlik okuma yolu |
| 2 | §6.3 üç enum kümesi | R-DM8 · §22 durum makinesi | Çelişki → karar zinciri |
| 3 | §6.4 `codec` enum (s.144) | V-DM5 · RM-DM6 | BT codec VO + eşleştirme |
| 4 | §7 DM-P6 (`is_primary`) | RM-DM3 · DM-T6 | Tekil kontrol — domain'de |
| 5 | §7 DM-P7 (`device_type_id` FK YOK) | RM-DM1 · DM-T7 | Uygulama doğrulaması zorunlu |
| 6 | §9.2 E-DM3/E-DM4 (sync olayları) | §22.2 durum geçişleri | Olay tetikleyicileri = geçişler |
| 7 | §23.1 (7 tip sayısal uyum) | R-DM4 | Doğrulanmadı → tarama |
| 8 | §23.3 (autoEQ) | AIWorkflow s.147–174 | AI ↔ cihaz bağımlılığı |
| 9 | master §3.1 satır 95 | `Device` aggregate adayı | ⚠️ ADR-097 K4 |
| 10 | master §3.4 satır 126 | Event boşluğu kaydı | ✅ negatif kanıt zinciri |

### §27.1 K008 içi okuma daveti

| Dosya | Neden okunmalı |
|---|---|
| [[playback-kapsam]] §11 | Hedef cihaz seçimi sınırı (çift yönlü) |
| [[media-kapsam]] §6 | `file_format` enum 5 (s.57) — media ile aynı küme |
| [[discovery-kapsam]] §4 | `DeviceDetector` → keşif girdisi (R-DM4) |
| [[domain-bilesenler]] §14 | Olay→BC çizelgesi: E-DM1…E-DM6 orada konumlanmalı |
| K005/system vault | `system_bluetooth_devices` şema sahibi — eşleme kararı (R-DM2) |

> **Bu dosyanın doğruluk sözü:** §6/§7/§22/§23'teki her `s.NN` `coremusic_media.sql`
> ve `coremusic_system.sql` okumasından gelir; §7/§8/§10/§13/§22 geçiş/VO/olay
> listeleri **öneridir** ve ADR-097 K4'e bağlıdır.

## §28 TERİM SÖZLÜĞÜ (BU DALIN UBİQUITOUS DİLİ)

| Terim | Şema/kod karşılığı | Kanıt |
|---|---|---|
| Cihaz | `devices` satırı | s.89–113 |
| Cihaz tipi | `device_types` satırı | s.68–87 |
| Platform | `device_types.platform` enum 7 | s.72 |
| Hedef cihaz | `devices.is_primary=1` | s.101 |
| Cihaz UUID | `devices.device_uuid` (varchar 255, UNIQUE) | s.94 · s.109 |
| Parmak izi | `devices.device_fingerprint` (UNIQUE yok) | s.95 |
| Senkron (parça) | `device_tracks` satırı + `sync_status` | s.51–66 |
| Senkron (playlist) | `device_playlists` satırı + `sync_status` | s.18–31 |
| Senkron geçmişi | `device_sync_history` satırı | s.33–49 |
| Senkron tipi | `sync_type` enum 4 | s.36 |
| BT cihazı | `system_bluetooth_devices` satırı | s.138–160 |
| BT MAC | `mac_address` varchar(17) UNIQUE | s.142 · s.156 |
| BT codec | `codec` enum 7 | s.144 |
| Eşleştirme | `is_paired` | s.145 |
| Otomatik bağlantı | `auto_connect` default 1 | s.148 |
| UA tespiti | `DeviceDetector::detect()` | s.49–72 |
| Oda profili (EQ) | `AIWorkflow::autoEQWorkflow` | s.147–174 |

---

**Kardeş dosyalar:** [[device-management-dogrulama]] · [[device-management-web-arastirma]] · [[device-management-ornekler]] · [[device-management-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
**Authority:** Bayram Ali / Vault Steward · 2026-10-17 (K008-domain derinleştirme — §5–§21 eklendi; senkron enum çelişkisi + BT eşleme boşluğu kaydedildi)
