---
node_id: K009-security/audit/audit-bilesenler
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on:
  - K009-security/audit/audit-kapsam
evidence: "B-A01..B-A09 bileşen envanteri (Dosya·Satır, diskten 2026-10-15)"
validation: "Her bileşen dosya + satır + rol kanıtı; eksikler ⚠️"
open_questions: "K017 tüketici bileşeni ⚠️ · secret_access üretici ⚠️ · log rotation bileşeni ⚠️"
group: mimari
---

# K009 · audit · Bileşenler

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · parent: K009-security/audit · rol: denetim izi bileşen envanteri.

## §1 Bileşen Envanteri

| # | Bileşen | Dosya | Satır | Rol |
|---|---|---|---|---|
| B-A01 | Redactor | shared/src/Log/Redactor.php | 25-106 | tek maskeleme noktası |
| B-A02 | FileHandler | shared/src/Log/FileHandler.php | 30-253 | PSR-3 yazıcı + olay kayıtları + ikiz stream |
| B-A03 | LoggerFactory | shared/src/Log/LoggerFactory.php | 11-34 | singleton FileHandler (repo kökü) |
| B-A04 | StructuredLogger | shared/src/PageRouter/StructuredLogger.php | 7-81 | JSON normalize + error_log |
| B-A05 | UserLoggedInEvent | shared/src/Events/Domain/UserLoggedInEvent.php | 20-86 | login olayı (userId/ip/ua/ts) |
| B-A06 | UserLoggedOutEvent | shared/src/Events/Domain/UserLoggedOutEvent.php | 20-76 | logout olayı (userId/sessionId/ts) |
| B-A07 | SecurityHelper::logTestBypass | shared/src/Security/SecurityHelper.php | 42-55 | bypass audit kaydı |
| B-A08 | SecurityHelper::isTestBypassActive | shared/src/Security/SecurityHelper.php | 23-30 | prod fail-closed anahtarı |
| B-A09 | security stream dosyası | coremusic_php_security.log (üretim) | — | audit tail'i (FileHandler.php·233-235) |

## §2 Bileşen Rol Detayları

| Bileşen | Girdi | Çıktı | Kanıt |
|---|---|---|---|
| Redactor | ham satır | maskeli satır | `Redactor.php·65-93` |
| FileHandler | message + context | 1-4 dosya satırı | `FileHandler.php·207-246` |
| StructuredLogger | module+event+extra | `[CoreMusic] {json}` | `StructuredLogger.php·61-72` |
| UserLoggedInEvent | userId/ip/ua | payload + occurredOn | `UserLoggedInEvent.php·26-60` |
| UserLoggedOutEvent | userId/sessionId | payload + occurredOn | `UserLoggedOutEvent.php·26-58` |
| logTestBypass | context/file/line | TEST_BYPASS satırı | `SecurityHelper.php·47-53` |

## §3 Akış Bileşen Sırası (surucu §3 ile)

| Sıra | Bileşen | Durum |
|---|---|---|
| 1 | olay üretici (Event/Middleware/Security) | ✅ §1 |
| 2 | normalize (StructuredLogger / FileHandler satır kurulumu) | ✅ §1 |
| 3 | maskele (Redactor) | ✅ §1 |
| 4 | yaz (appendToFile / logTestBypass) | ✅ §1 |
| 5 | tüket (K017) | ⚠️ bileşen YOK (U-1) |

## §4 Bağımlılık Grafiği

| Bileşen | Üstünde | Altında |
|---|---|---|
| Redactor | — | FileHandler, StructuredLogger |
| FileHandler | LoggerFactory, middleware çağıranlar | 4 log dosyası |
| StructuredLogger | PageRouterKernel (çağıran okunmadı ⚠️) | error_log |
| Events | authentication/Domain | dispatcher (akış ⚠️) |
| SecurityHelper | BypassAuthMiddleware (authentication §7) | coremusic_php_errors.log |

## §5 Eksik Bileşenler

| Eksik | Kanıt | Durum |
|---|---|---|
| K017 tüketici (tail/SIEM aktarım) | — | ⚠️ YOK (surucu §14) |
| secret_access olay üretici | — | ⚠️ YOK (anayasa U-3) |
| Log rotation/retention | — | ⚠️ YOK (anayasa U-1) |
| Kayıt imza/zoetluk bileşeni | — | ⚠️ YOK (anayasa U-7) |
| KVKK silme rotini | — | ⚠️ YOK (anayasa U-2) |
| sessionId key güncellemesi (Redactor) | `Redactor.php·31` listesinde yok | ⚠️ (AD-16) |

## §6 Dosya Hedefleri (Bileşen × Akış)

| Dosya | Yazan bileşen | Koşul | Kanıt |
|---|---|---|---|
| coremusic_php_errors.log | FileHandler | her zaman | `FileHandler.php·228` |
| coremusic_php_security.log | FileHandler | securityStream | `FileHandler.php·233-235` |
| coremusic_php_warnings.log | FileHandler | debug mode | `FileHandler.php·238-241` |
| coremusic_php_info.log | FileHandler | debug mode | `FileHandler.php·242-244` |
| coremusic_php_errors.log (TEST_BYPASS) | SecurityHelper | bypass aktif | `SecurityHelper.php·46` |

## §7 Sınır (K009-Dışı Tüketici/Üretici)

| Düğüm | Bileşen İlişkisi | Kanıt |
|---|---|---|
| authorization | authz_denied olayını tetikler | olay adı `FileHandler.php·179` (üretici kod ⚠️) |
| authentication | bypass log akışını tetikler | authentication-ornekler §7 |
| secrets | secret_access kaynağı | ⚠️ kanıt yok |
| K016-observability | security.log tüketir | ⚠️ kanıt yok |
| K000-isletim-sistemi | dosya izinleri/OS journal | sınır referansı (index §4) |

## §8 Bileşen × Olay Matrisi

| Bileşen | Ürettiği olay | Doğrudan Çağrı | Kanıt |
|---|---|---|---|
| UserLoggedInEvent | user.logged_in | authentication/controller | `UserLoggedInEvent.php·39` |
| UserLoggedOutEvent | user.logged_out | authentication/controller | `UserLoggedOutEvent.php·38` |
| FileHandler::authEvent | [AUTH] login_* / register_* / logout / rate_limited | auth akışı | `FileHandler.php·139-163` |
| FileHandler::securityEvent | [SECURITY] authz_denied / csrf_invalid / origin_blocked / rate_limited / bypass_activated | middleware katmanı | `FileHandler.php·169-184` |
| SecurityHelper::logTestBypass | [TEST_BYPASS] | BypassAuthMiddleware | `SecurityHelper.php·42-55` |
| StructuredLogger | JSON modül olayları | PageRouterKernel | `StructuredLogger.php·55-73` |
| Redactor | (üretmez — dönüştürür) | tüm yazıcılar | `Redactor.php·65` |

## §9 Bileşen Riskleri

| # | Bileşen | Risk | Savunma/Durum |
|---|---|---|---|
| 1 | logTestBypass | Redactor'dan geçmez (doğrudan yazım) | içerik sır içermiyor (context/file/line) — ⚠️ not |
| 2 | StructuredLogger | error_log hedefi PHP konfigürasyonuna bağlı | ⚠️ hedef bilinmiyor |
| 3 | FileHandler | `@` suppress sessiz kayıp | ⚠️ U-6 (anayasa H-02) |
| 4 | Redactor | camelCase anahtarlar eşleşmez | ⚠️ AD-16 |
| 5 | LoggerFactory | repo köküne yazım | kanonik hedef (FileHandler.php·19-25) |
| 6 | Events | payload Redactor'dan geçer mi? | ⚠️ KANIT YOK |

## §10 Bileşen Tetikleyicileri

| Tetikleyici | Etkilenen Bileşen | Aksiyon |
|---|---|---|
| Yeni hassas alan | B-A01 | SENSITIVE_KEYS güncelle |
| Yeni güvenlik olayı | B-A02 | securityEvent match'e ekle |
| K017 entegrasyonu | B-A09 | tüketici bileşeni envantere ekle |
| secret_access eklenmesi | B-A02 | yeni event + stream |
| Log rotation ADR | B-A02/B-A03 | retention bileşeni ekle |


## §10.1 Bileşen Kabul Kriterleri (her bileşen için)

| Kriter | Beklenen | Kanıt |
|---|---|---|
| Dosya + satır çifti | envanterde her B-Axx için mevcut | §1 tablosu |
| Redaction geçişi | hassas alan üreten bileşen Redactor ile eşleşmeli | `Redactor.php·31` |
| Olay adı disiplini | nokta-lı kanonik ad (user.logged_in) | `UserLoggedInEvent.php·39` |
| Üretici-çıktı eşlemesi | her olay bir dosya/stream hedefine yazmalı | `FileHandler.php·207-246` |
| Sınırda tüketici | K009 dışı her ilişki açıkça işaretli | §7 tablosu |

Bir bileşen bu beş kriterden birini karşılamıyorsa §9 risk tablosuna veya §5 eksik
envanterine yazılır; sessizce atlanmaz. Kabul kriterleri denetim sırasında dosya +
satır kanıtıyla tek tek sınanır.

## §11 Son Durum

| Alan | Değer |
|---|---|
| Dosya | audit-bilesenler.md · group: mimari · parent K009-security |
| Bileşen | 9 envanter + 6 eksik (işaretli) |
| Satır | ≥150 |
| git commit | ATILMADI (subagent yasağı) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
