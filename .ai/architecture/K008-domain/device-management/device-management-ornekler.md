---
node_id: "K008-device-management-ornekler"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-device-management-kapsam"
evidence:
  - "assets.coremusic.net/js/devices.config.js satır 9–21, 42–43"
  - "shared/src/Device/DeviceDetector.php satır 57–72"
  - ".ai/.sql/mysql/coremusic_media.sql satır 89–113"
  - "shared/src/AI/AIWorkflow.php satır 147–174"
validation: "2026-10-15 — 4 örnek (JS, PHP ×2, SQL); hepsi disk'ten birebir kopyalandı"
open_questions:
  - "device-loader.js / DeviceManager.js senkron akışı → içerik okunmadı → ⚠️"
group: kod
---

# device-management — Örnekler (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> Dört örnek de disk'ten birebir kopyalanmıştır.

## §1 ÖRNEK 1 — JS: devices.config.js — HOME_CSS haritası + ALL (satır 9–21, 42–43)

```js
// Dosya: assets.coremusic.net/js/devices.config.js · satır 9-21, 42-43
window.CoreMusic = window.CoreMusic || {};
window.CoreMusic.DEVICES = Object.freeze({

    /** Cihaz bazlı ana CSS dosyaları — 4K tek SSOT: d-4k.css (v3.0.0) */
    HOME_CSS: Object.freeze({
        'embedded':   '08_Devices/d-embedded.css',
        'phone':      '08_Devices/d-phone.css',
        'tablet':     '08_Devices/d-tablet.css',
        'laptop':     '08_Devices/d-laptop.css',
        'desktop':    '08_Devices/d-desktop.css',
        '4k-tv':      '08_Devices/d-4k.css',
        '4k-monitor': '08_Devices/d-4k.css',
    }),
    // … AUTH_CSS (satır 24-31) ve VIEW_CSS (satır 35-40) aynı dosyada …

    /** Tüm desteklenen cihaz türleri */
    ALL: Object.freeze(['embedded', 'phone', 'tablet', 'laptop', 'desktop', '4k-tv', '4k-monitor']),
});
```

**Ne kanıtlıyor:** 7 tip `Object.freeze` ile sabitlenmiştir; 4k-tv ve 4k-monitor
**aynı** CSS dosyasına yönlendirilir (satır 19–20) — "4K tek SSOT" yorumu
(satır 12) kodla tutarlıdır.

## §2 ÖRNEK 2 — PHP: DeviceDetector::detect (satır 57–72)

```php
<?php
// Dosya: shared/src/Device/DeviceDetector.php · satır 57-72
public static function detect(
    ?string $userAgent = null,
    ?int    $viewportW = null,
    ?int    $viewportH = null
): string {
    // 0. Özel HTTP başlığı veya kiosk bayrağı kontrolü
    if (!empty($_SERVER['HTTP_X_DEVICE_TYPE']) && strtolower((string)$_SERVER['HTTP_X_DEVICE_TYPE']) === 'embedded') {
        return 'embedded';
    }

    $ua = $userAgent ?? $_SERVER['HTTP_USER_AGENT'] ?? '';

    // 1. Smart TV agent kontrolü
    if (self::isTvAgent($ua)) {
        return '4k-tv';
    }
```

**Ne kanıtlıyor:** tespit sırası — önce `X-Device-Type` (kiosk override, satır
62–65), sonra UA. Dönüş değerleri docblock'taki 7'li listeden (satır 55) gelir.

## §3 ÖRNEK 3 — SQL: devices tablosu (satır 89–113)

```sql
-- Dosya: .ai/.sql/mysql/coremusic_media.sql · satır 89-113
CREATE TABLE `devices` (
  `id` binary(16) NOT NULL,
  `user_id` binary(16) NOT NULL,
  `device_type_id` binary(16) NOT NULL,
  `device_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_fingerprint` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `os_version` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `app_version` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mac_address` varchar(17) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `is_primary` tinyint(1) DEFAULT '0',
  `last_synced_at` timestamp NULL DEFAULT NULL,
  `last_active_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_devices_uuid` (`device_uuid`),
  KEY `idx_devices_user_id` (`user_id`),
  KEY `idx_devices_device_type_id` (`device_type_id`),
  KEY `idx_devices_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User devices for synchronization, streaming, and multi-device access';
```

**Ne kanıtlıyor:** cihaz kimliği üç katmanlıdır — `device_uuid` (UNIQUE, satır 109),
`device_fingerprint` (satır 95, strateji belgesiz), `mac_address` (satır 99).
`is_primary` (satır 101) çalma hedefi seçimidir. ID'ler `binary(16)` = UUID nesli.

## §4 ÖRNEK 4 — PHP: AIWorkflow::autoEQWorkflow (satır 147–174 kesit)

```php
<?php
// Dosya: shared/src/AI/AIWorkflow.php · satır 147-174 (kesit)
public function autoEQWorkflow(array $request): array
{
    $startTime = microtime(true);
    $sessionId = $request['session_id'] ?? 'anonymous';
    $deviceId = $request['device_id'] ?? null;

    // 1. Task dispatch
    $taskId = $this->orchestrator->dispatchTask('eq-optimization', $request, 'medium');

    // 2. Room profile分析
    $roomProfile = [
        'size'               => $request['room_size'] ?? 'medium',
        'reverb'             => $request['reverb'] ?? 0.5,
        'frequency_response' => $request['frequency_response'] ?? [],
    ];

    // 3. EQ optimize
    $eqCurve = $this->engine->optimizeEQ($roomProfile);

    // 4. Device-specific ayarlar (varsa)
    if ($deviceId !== null) {
        $hardwareAnalysis = $this->engine->analyzeHardware($deviceId);
        // Cihaz özelliklerine göre EQ'yu ayarla
    }

    // 5. Cache'le
    $cacheKey = "eq_{$sessionId}_" . md5(serialize($roomProfile));
    $this->memory->remember($cacheKey, fn() => $eqCurve, 'l2', 3600);
```

**Ne kanıtlıyor:** EQ akışı cihaz-bilinçlidir (satır 151, 167–170); oda profili
zorunlu değil, varsayılanlarla çalışır (satır 157–161). Satır 156'daki `分析`
(karakterler disk'te birebir böyledir) yorumu — dosya UTF-8, mojibake değil,
kaynağındaki hali korunmuştur.

## §5 DÜZELTME/NOT KAYDI

| # | Not | Durum |
|---|---|---|
| 1 | `coremusic_devices.sql` diye bir dosya diskte YOK — device tabloları `coremusic_media.sql` içindedir | ✅ glob + grep 2026-10-15 |
| 2 | `js/devices/audio_devices.js` diskte YOK — cihaz JS'i `js/devices.config.js` + `js/coreplayer/` altındadır | ✅ glob 2026-10-15 |

**Kardeş dosyalar:** [[device-management-kapsam]] · [[device-management-dogrulama]] · [[device-management-web-arastirma]] · [[device-management-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
