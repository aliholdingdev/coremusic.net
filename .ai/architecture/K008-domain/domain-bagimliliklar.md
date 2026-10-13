---
node_id: K008-domain/bagimliliklar
node_type: root
status: verified
parent_id: K008-domain
children: []
depends_on:
  - K008-domain/domain-master.md
  - K008-domain/domain-kapsam.md
evidence:
  - shared/composer.json
  - shared/src/Events/EventDispatcher.php
  - shared/src/Log/FileHandler.php
validation: 2026-10-12 — composer.json require bloğu ve Psr\ kullanım taraması okundu (doğrudan disk kanıtı)
open_questions:
  - "Domain katmanının DI (php-di) ile ilişkisi kod kanıtıyla henüz kapatılmadı — VERIFICATION REQUIRED"
group: mimari
---

# K008-domain — Bağımlılıklar (Dependency Axis)

> **Authority: Bayram Ali / Vault Steward.** ADR-097 §5.5'in **ikinci ekseni**:
> K008-domain neye bağımlıdır, ne K008'e bağımlıdır, hangisi yasaktır.
> Bu dosya yalnız **kanıtlanmış** bağımlılıkları listeler; kanıtsız satır yazılmaz
> (master §6-C4 kuralı).

## §1 BAĞIMLILIK ÖZETİ

| Yön | Tür | Durum |
|---|---|---|
| Dışarıdan içeri | PHP 8.4+, PSR arayüzleri (event-dispatcher, log, cache, container) | Kanıtlı (§3) |
| İçeri dışarı | Domain event yayını (PSR-14 dispatcher) | Kanıtlı (§3.2) |
| Yasak | PDO/MySQL, HTTP, UI, dosya sistemi, kuyruk seçimi | Kural (§4) |
| Belirsiz | DI container, validation kütüphanesi | `⚠️ VERIFICATION REQUIRED` (§5) |

## §2 DİSTRİBÜSYON BİRİMİ KİMLİĞİ

| Alan | Değer (disk kanıtı) |
|---|---|
| Paket adı | `coremusic/shared-infrastructure` |
| Sürüm | `2.0.0` |
| Lisans | `proprietary` |
| Tip | `library` |
| Min PHP | `>=8.4` |
| Namespace (PSR-4) | `CoreMusic\` → `src/` |
| Test namespace | `CoreMusic\Test\` → `tests/` |
| Dosya | `shared/composer.json` (require bloğu, 2026-10-12 okundu) |

## §3 KANITLANMIŞ BAĞIMLILIKLAR

### §3.1 PSR arayüzleri (composer require)

| Paket | Sürüm | K008-domain ile ilişki | Kanıt |
|---|---|---|---|
| `psr/event-dispatcher` | `^1.0` | Domain event yayınının sözleşmesi (master §3.3) | `shared/composer.json` require |
| `psr/log` | `^3.0` | Domain servislerinin loglama arayüzü | `shared/composer.json` require |
| `psr/cache` | `^3.0` | Önbellek arayüzü (domain katmanı doğrudan kullanmaz — `⚠️` §5) | `shared/composer.json` require |
| `psr/container` | `^2.0` | Bağımlılık çözümleme arayüzü | `shared/composer.json` require |
| `symfony/event-dispatcher` | `^7.0` | Dispatcher'ın uygulama katmanındaki sağlayıcı | `shared/composer.json` require |

### §3.2 PSR-14 kullanım kanıtı (kod)

| İddia | Dosya | Satır | Durum |
|---|---|---|---|
| Dispatcher sınıfı PSR-14 `EventDispatcherInterface` kullanır | `shared/src/Events/EventDispatcher.php` | s.20 (sınıf) | Doğrulandı |
| `dispatch()` yöntemi event yayınlar | `shared/src/Events/EventDispatcher.php` | s.28 (yöntem) | Doğrulandı |
| PSR-3 logger kullanımı kodda geçer | `shared/src/Log/FileHandler.php` | `Psr\` referansı | Doğrulandı |
| PSR-4 autoload `CoreMusic\` altında | `shared/composer.json` | `autoload.psr-4` | Doğrulandı |

### §3.3 Geliştirme bağımlılıkları (test/dogrulama ile ilişkili)

| Paket | Sürüm | K008 bağlantısı | Kanıt |
|---|---|---|---|
| `phpunit/phpunit` | `^10.5` | `domain-test-stratejisi.md` §2 aracı | composer.json require-dev |
| `phpstan/phpstan` | `^1.10` | Statik analiz — `scripts.stan` = `phpstan analyse src --level=5` | composer.json scripts |
| `ext-pdo`, `ext-json`, `ext-mbstring`, `ext-apcu` | `*` | **Domain katmanı PDO kullanmaz** — uzantılar altyapı katmanına ait | composer.json require |

## §4 YASAK BAĞIMLILIKLAR (domain purity — bağlayıcı)

| # | Yasak | Gerekçe | Kaynak |
|---|---|---|---|
| Y1 | Domain sınıfının `PDO` / `mysqli` çağırması | Persistence bilgisi domain'e sızarsa BC sınırı çöker | DDD — master §3.2-A5 |
| Y2 | Domain'in `$_GET`/`$_POST`/`Response` bilmesi | HTTP domain'in dalıdır, sahibi değildir | N2 (kapsam §2) |
| Y3 | Domain'in dosya yolu / dizin yazması | Altyapı (media kökü) sahipliğindedir | N1 |
| Y4 | Domain'in UI string'i (ekran metni) tutması | Ubiquitous dil İngilizce teknik terimdir; yerelleştirme UI katmanında | `domain-evrensel-dil.md` §2 |
| Y5 | Domain'in kuyruk/broker seçimi | Seçim K006/iş katmanı | N6 |
| Y6 | Domain'in şifreleme/Argon2id çağırması | security katmanı sahipliğinde | N3 |

**Kural:** Bu dosyada yasak ihlali görülürse doğrudan düzeltilmez; `domain-riskler.md`
girdisi (R-satırı) + orkestratör raporu üretilir.

## §5 KANIT BEKLEYEN BAĞIMLILIKLAR (`⚠️ VERIFICATION REQUIRED`)

| # | İddia | Neden kanıtlanmadı | Nasıl kapatılır |
|---|---|---|---|
| V1 | Domain sınıfları `php-di` container'ı biliyor | Tarama yalnız `Psr\` desenini yaptı | `new Container` / `ContainerInterface` taraması |
| V2 | `respect/validation` domain kurallarında kullanılıyor | Kod okunmadı | `shared/src/` içinde `Respect\` taraması |
| V3 | Domain → DTO bağımlılığı sınır ihlali mi? | `MusicResponse.php`/`PlaylistResponse.php` domain event/DTO bağlantısı okunmadı | Dosya başlıklarının namespace kontrolü |
| V4 | `psr/cache` domain'de kullanılıyor mu? | Kod taraması yapılmadı | `CacheItemPoolInterface` araması |
| V5 | 10 çocuk config karşılığı (3 çocuk) | `domain.php` 7 satırda kaldı | `shared/config/domain.php` tekrar okunup §3-kapsam tablosuna işlenir |

## §6 BAĞIMLILIK YÖNÜ ŞEMASI

```text
[Dış: PHP 8.4 / PSR-14 / PSR-3]  --arayüz-->  [K008-domain: event sözleşmesi, dil, sınır]
        |                                                |
        | composer: psr/event-dispatcher ^1.0            | domain event yayını
        | composer: psr/log ^3.0                         v
        +---------------------------------->  [shared/src/Events/EventDispatcher.php (uygulama)]

[YASAK yönleri]  domain -X-> PDO/MySQL (.ai/.sql/mysql)
                 domain -X-> HTTP/Response (api katmanı)
                 domain -X-> CSS/UI (frontend)
```

## §7 DEĞİŞİKLİK PROTOKOLÜ

| Durum | Eylem |
|---|---|
| Yeni bağımlılık iddiası | Önce kanıt (Dosya/Satır) → sonra bu dosyaya satır |
| Kaldırılan bağımlılık | Satır silinmez, `Durum` sütunu `kaldırıldı (tarih)` olur |
| Yasak ihlali | Risk kaydı + rapor; K008 agent'ı kodu değiştirmez |
| Sürüm yükseltmesi | K016 sahipliğinde; bu dosya yalnız ilişkisini günceller |

---

*Authority: Bayram Ali / Vault Steward · K008-domain bağımlılıklar · 2026-10-12 · composer.json + kod taraması kanıtlı*
