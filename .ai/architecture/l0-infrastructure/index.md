---
type: architecture
category: l0
title: "L0 — Infrastructure Layer"
date: 2026-08-08
updated: 2026-09-08
status: active
version: 5.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# L0 — Infrastructure Layer

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[index.md]] · [[brain.md]]

**Diğer Katmanlar:** [[architecture/l1-security]] · [[architecture/l2-routing]] · [[architecture/l3-presentation]]

---

## 1. Amaç

L0, CoreMusic platformunun **altyapı katmanıdır**. Veritabanı, cache, dosya sistemi, credential vault ve servisler arası iletişim bu katmanda yönetilir. Tüm L1–L3 katmanları L0'a bağımlıdır — L0 ise yukarı katmanlara bağımlı değildir.

**Katman Bağımlılık Kuralı:**
```
✅ L3 → L2 → L1 → L0
❌ L0 → L1/L2/L3
```

*Kaynak: [[CLAUDE.md]] §5, [[ADR-042-vault-restructuring-2026-08-03]]*

Bu sürüm (v5.0.0) Faz 2a vault revizyonu ile güncellendi (2026-09-08):
1. Redis iddiası IMPLEMENTED/PLANNED etiketiyle netleştirildi (kod taraması: Redis adapter YOK).
2. Gerçek kod karşılıkları eklendi (§14-§17) — `shared/src/` sınıf taraması Faz 0.
3. Diagnostics komut seti (§18) ve izlenebilirlik tablosu (§20) eklendi.

---

## 2. Kapsam

| Kapsam | Durum | Kapsam Dışı |
|--------|-------|-------------|
| 18 BCNF MySQL veritabanı (ADR-040) | **IMPLEMENTED (şema)** — kod: `shared/src/Database/DatabaseManager.php` | Frontend UI kodu |
| Multi-tier cache — APCu + Memory | **IMPLEMENTED** — kod: `shared/src/Cache/CacheManager.php` | SPA routing |
| Redis dağıtık cache | **PLANNED** — kodda adapter yok | Middleware pipeline |
| Dosya sistemi yönetimi (PSR-17) | **IMPLEMENTED (doküman)** | Güvenlik politikası tasarımı |
| Credential vault (AES-256-GCM — ADR-022) | **IMPLEMENTED (doküman/şema)** | Deployment süreçleri |
| Modüler paket altyapısı (ADR-085) | **IMPLEMENTED** — 2 composer paketi: `coremusic/shared-infrastructure` + `coremusic/shared` | İş mantığı |
| Event bus altyapısı (PSR-14 — ADR-086) | **PLANNED** — bağımlılık composer'da var (`symfony/event-dispatcher ^7.0`), bus kodu yok | — |
| Servisler arası iletişim (IPC — ADR-032) | **PLANNED** — kodda IPC modülü yok | — |

---

## 3. Bileşenler

| Bileşen | Dosya | Amaç |
|---------|-------|------|
| **Database** | [[database]] | 18 BCNF MySQL veritabanı (ADR-040), PDO, prepared statement, migration |
| **Cache** | [[cache]] | Multi-tier cache: APCu → Memory (Redis PLANNED), namespace isolation (ADR-007) |
| **Filesystem** | [[filesystem]] | Medya dosyaları, upload yönetimi, disk I/O, PSR-17 stream |
| **Credential Vault** | [[credential-vault]] | AES-256-GCM şifreleme (ADR-022), API key, token yönetimi |
| **Modüler Paketler** | — | İki PSR-4 kökü geçiş aşaması: `CoreMusic\` + `CoreMusic\Shared\` (ADR-085) |
| **Event Bus** | — | PSR-14 Event Dispatcher altyapısı (ADR-086) — PLANNED |
| **IPC** | — | Servisler arası iletişim, JSON/msgpack (ADR-032) — PLANNED |

---

## 4. Tech Stack

| Teknoloji | Versiyon | Kullanım | Durum | Kaynak |
|-----------|---------|----------|-------|--------|
| MySQL | 9+ | Veritabanı (18 BCNF şema) | ŞEMA IMPLEMENTED | dev.mysql.com |
| InnoDB | — | Storage engine | ŞEMA IMPLEMENTED | dev.mysql.com |
| PHP PDO | 8.4+ | DB abstraction | **IMPLEMENTED** — `DatabaseManager` | php.net |
| APCu | 5.1+ | In-memory cache | **IMPLEMENTED** — `ApcuAdapter` | pecl.php.net |
| Memory Adapter | PHP native | APCu yoksa fallback | **IMPLEMENTED** — `MemoryAdapter` | shared/src/Cache |
| Redis | 7+ | Distributed cache | **PLANNED** — kodda adapter yok | redis.io |
| OpenSSL | 3.x | AES-256-GCM | DOKÜMAN (kod karşılığı DOĞRULAMA GEREKLİ) | openssl.org |

*Kaynak: MySQL 9.7 Reference Manual (dev.mysql.com), PHP 8.4 Manual (php.net) — 2026-08-08'de doğrulandı; kod tarafı Faz 0 taraması 2026-09-08.*

---

## 5. Sorumluluk Matrisi

| Bileşen | Veri Türü | Öncelik | SLA | Kod Kanıtı |
|---------|-----------|---------|-----|------------|
| Database | Transactional veri | CRITICAL | 99.9% uptime | `DatabaseManager` (PDO) |
| Cache | Hot data, session | HIGH | <5ms read | `CacheManager` + adapter'lar |
| Filesystem | Medya, dosya | MEDIUM | <100ms write | Doküman (kod karşılığı bekliyor) |
| Credential Vault | Secret, key | CRITICAL | 100% integrity | Şema + doküman |

---

## 6. Port Haritası

| Port | Servis | Protokol | Durum |
|------|--------|----------|-------|
| 81 | Control Service (PHP 8.4) | HTTP | PLANNED (music domain kodu yok) |
| 5000/6000 | Media Service | HTTP | PLANNED |
| 9741 | Audio Service (REST) | HTTP | PLANNED |
| 9742 | Audio Service (WebSocket) | WS | PLANNED |
| 3001 | Download Service | HTTP/WS | PLANNED |
| 3306 | MySQL 9 | TCP | ŞEMA HAZIR — sunucu kurulumu kullanıcı ortamına bağlı |
| 6379 | Redis | TCP | **PLANNED** (kodda Redis yok) |

*Kaynak: [[ADR-042-vault-restructuring-2026-08-03]] · Durum sütunu Faz 0 (2026-09-08).*

---

## 7. Hard Guardrails

| # | Kural | Kaynak |
|---|-------|--------|
| 1 | ORM yasak — sadece PDO prepared statement | [[ADR-002-pdo-mandatory-no-orm]] |
| 2 | SELECT * yasak — açık sütun listesi zorunlu | [[ADR-040-database-authority]] |
| 3 | Hard delete yasak — soft delete zorunlu (`is_deleted = 0`) | [[ADR-040-database-authority]] |
| 4 | AES-256-GCM — credential şifreleme standartı (96-bit IV, 16-byte tag) | [[ADR-022-database-hardened-security]] |
| 5 | Argon2id — password hashing standartı (64MB/4/2 threads) | [[ADR-022-database-hardened-security]] |
| 6 | Cache namespace — her servis ayrı namespace (ADR-007) | [[ADR-007-cache-namespace]] |
| 7 | BCNF normalizasyon — 18 BCNF DB zorunlu | [[ADR-040-database-authority]] |
| 8 | Hardcoded secret yasak — credential vault kullan | [[ADR-034-credential-vault-normalization]] |
| 9 | Circular dependency yasak — shared/ namespace bağımsız | [[ADR-085-modular-composer-packages]] |
| 10 | PSR-14 event bus — servisler arası doğrudan çağrı yasak | [[ADR-086-event-driven-architecture]] |
| 11 | Node/C# tarafında da ORM yok — stack Genel ilkesi | [[engine.md]] §9.5.4 |

---

## 8. Edge Cases

| Durum | Çözüm | ADR | Kod Karşılığı |
|-------|-------|-----|---------------|
| Cache Stampede | Mutex ile single load | [[cache]] | PLANNED (Mutex sınıfı yok) |
| DB Connection Loss | Retry + failover | [[database]] | `DatabaseManager` PDO EXCEPTION modu |
| File Upload Attack | MIME + extension check | [[filesystem]] | Doküman aşaması |
| Race Condition | DB transaction + row lock | InnoDB | Şema düzeyi |
| Credential Leak | Vault + redaction | [[credential-vault]] | `[REDACTED]` politikası (MEMORY §12) |

---

## 9. Test Kapsama Hedefleri

| Modül | Minimum | Hedef | Framework | Mevcut |
|-------|---------|-------|-----------|--------|
| Database | ≥80% | ≥90% | PHPUnit 11 / 10.5 | require-dev tanımlı |
| Cache | ≥80% | ≥90% | PHPUnit 11 / 10.5 | require-dev tanımlı |
| Filesystem | ≥80% | ≥90% | PHPUnit 11 / 10.5 | require-dev tanımlı |
| Credential Vault | ≥90% | ≥95% | PHPUnit 11 / 10.5 | require-dev tanımlı |

*Not: `coremusic/shared-infrastructure` PHPUnit ^10.5, `coremusic/shared` ^11.0 kullanır — sürüm farkı ADR-085 birleşim kapsamında çözülecek ([[engine.md]] §9.5.1).*

---

## 10. İlgili Dosyalar

| Dosya | Amaç | Satır (Faz 2a) |
|-------|------|----------------|
| [[database]] | 18 BCNF veritabanı, PDO, repository pattern | 650 ✅ |
| [[cache]] | Multi-tier cache, APCu, namespace | 663 ✅ |
| [[filesystem]] | Dosya yönetimi, upload, disk I/O | 706 ✅ |
| [[credential-vault]] | AES-256-GCM, Argon2id, secret yönetimi | 641 ✅ |
| [[l1-security]] | Security middleware, session, CSRF, CSP | Katman index |
| [[architecture/05-data/database_master]] | Database master dokümanı | 407 |

---

## 11. Çapraz Referanslar

| Bu Dosyadan | Hedef | İlişki |
|-------------|-------|--------|
| § 6 Port | [[ADR-042-vault-restructuring-2026-08-03]] | Port mapping |
| § 7 Guardrails | [[ADR-002-pdo-mandatory-no-orm]] | ORM yasağı |
| § 7 Guardrails | [[ADR-040-database-authority]] | DB otoritesi |
| § 7 Guardrails | [[ADR-022-database-hardened-security]] | Güvenlik |
| § 7 Guardrails | [[ADR-034-credential-vault-normalization]] | Credential vault |
| § 3 Bileşenler | [[ADR-007-cache-namespace]] | Cache standardı |
| § 14-17 | `shared/src/**` | Gerçek kod karşılıkları |

---

## 12. Sözlük

| Terim | Tanım |
|-------|-------|
| **BCNF** | Boyce-Codd Normal Form — 18 BCNF DB için zorunlu normalizasyon |
| **PDO** | PHP Data Objects — veritabanı erişim soyutlama katmanı |
| **APCu** | APC User Cache — PHP in-memory önbellek |
| **Redis** | Remote Dictionary Server — dağıtık önbellek (PLANNED) |
| **AES-256-GCM** | Advanced Encryption Standard, 256-bit, Galois/Counter Mode |
| **Argon2id** | Şifreleme algoritması (64MB/4/2) |
| **IPC** | Inter-Process Communication — servisler arası iletişim |
| **Soft Delete** | Kayıt silmek yerine `is_deleted = 1` ile işaretlemek |
| **Prepared Statement** | SQL injection önleme amaçlı parametreli sorgu |
| **Cache Stampede** | Yüksek eşzamanlı cache miss yükü |
| **Namespace** | Cache anahtarlarının servise göre ayrılması |
| **Adapter** | Cache backend soyutlaması — `ApcuAdapter`, `MemoryAdapter` |
| **Singleton** | Tek örnek deseni — `CacheManager` gerçek kod deseni |

---

## 13. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 5.0.0 |
| **Status** | Red Team · Human Mode · Truth Mode verified |
| **Bileşen Sayısı** | 4 dokümante + 3 PLANNED (Event Bus, IPC, Redis) |
| **ADR Uyumlu** | ✅ 002, 007, 022, 034, 040, 042, 085, 086 |
| **Kod Doğrulama** | ✅ Faz 0 (2026-09-08) — CacheManager, DatabaseManager |
| **Cross-Reference** | ✅ Doğrulandı |

---

## 14. Kod Karşılıkları (Faz 0 Doğrulaması — 2026-09-08)

L0 dokümanındaki bileşenlerin `shared/src/` içindeki gerçek karşılıkları:

| Bileşen | Gerçek Sınıf | Dosya | Desen | Not |
|---------|--------------|-------|-------|-----|
| Cache | `CacheManager` | `shared/src/Cache/CacheManager.php` | Singleton, adapter seçici | ~25 satır; `apcu_fetch` varlığı sınanır |
| Cache Adapter | `ApcuAdapter` | `shared/src/Cache/` | PSR-16 uyumlu | APCu mevcutsa seçilir |
| Cache Adapter | `MemoryAdapter` | `shared/src/Cache/` | PSR-16 uyumlu | APCu yoksa (tipik Windows dev) fallback |
| Page Cache | `PageCacheAdapter` | `shared/src/Cache/` | `PageCacheInterface` implements | PageRouter kurucu param 7 ile enjekte |
| Database | `DatabaseManager` | `shared/src/Database/DatabaseManager.php` | `IDatabaseManager` implements | PDO MySQL |
| Rate Limit Cache | `CacheRateLimiter` | `shared/src/Security/CacheRateLimiter.php` | `IRateLimiter` implements | 41 satır; `rl:` prefix tüketir |
| Cache Namespace | `rl:` prefix | `RateLimiterMiddleware` sabitleri | — | ADR-007 uygulaması |

**Redis uyarısı:** Yukarıdaki tabloda Redis adapter YOKTUR — `[[cache]]` dokümanındaki Redis bölümleri hedef mimari (PLANNED) olarak okunmalıdır. Bu ayrım Truth Mode gereğidir; sessiz "IMPLEMENTED" varsayımı yasaktır.

---

## 15. Cache Adapter Zinciri (Gerçek Kod Akışı)

`CacheManager`'ın çalışma anı davranışı (kod okumasından):

```
CacheManager::getInstance()
  → extension_loaded / function_exists('apcu_fetch') kontrolü
      ├─ APCu mevcut → ApcuAdapter örnekle  → PSR-16 CacheInterface
      └─ APCu yok    → MemoryAdapter örnekle → PSR-16 CacheInterface (in-process dizi)
```

**Tüketim noktaları (Faz 0 doğrulaması):**

| Tüketici | Kullanım | Anahtar Öneki |
|----------|----------|---------------|
| `RateLimiterMiddleware` | Pencere sayacı (60 istek/60 sn) | `rl:` |
| `PageCacheAdapter` | Sayfa yanıtı önbelleği | PageRouter enjeksiyonu |
| `psr/cache ^3.0` | Genel CacheInterface sözleşmesi | Servis namespace'i (ADR-007) |

**Sınır:** `MemoryAdapter` process-local'dır — çoklu worker ortamında paylaşılmaz. Bu, üretimde APCu/Redis zorunluluğunun teknik gerekçesidir; doküman diliyle "Redis PLANNED" ifadesinin arkasındaki neden budur.

---

## 16. DatabaseManager PDO Parametreleri

`shared/src/Database/DatabaseManager.php` bağlantı kurulumu (kod okumasından):

| Parametre | Değer | Anlamı |
|-----------|-------|--------|
| `PDO::ATTR_ERRMODE` | `ERRMODE_EXCEPTION` | Hata sessiz geçilmez — exception fırlatır |
| `PDO::ATTR_EMULATE_PREPARES` | `false` | Gerçek prepared statement (SQL injection koruması) |
| Driver | `mysql:host=...;charset=utf8mb4` | 18 BCNF şema hedefi |

**18 BCNF şema kaynakları:** `.ai/.sql/mysql/*.sql` (18 dosya, 156 tablo) — master doküman [[architecture/05-data/database_master]] (407 satır). Migration klasörü: `shared/database/migrations/`.

**Repository deseni:** Veri erişimi `IDatabaseManager` sözleşmesi üzerinden; somut repository sınıfları domain servislerinde (auth: `include/Repository/`). ORM YASAK (ADR-002) — bu kural Node.js (ADR-026) ve C# (gelecek) tarafına da genellenir ([[engine.md]] §9.5.4).

---

## 17. PLANNED Bileşen Durum Tablosu

| Bileşen | ADR | Beklenen Konum | Giriş Önkoşulu |
|---------|-----|----------------|----------------|
| Redis Adapter | ADR-007/013 | `shared/src/Cache/RedisAdapter.php` | `predis/predis` composer bağımlılığı + ADR onayı |
| Event Bus | ADR-086 | `shared/src/Events/` (klasör mevcut, bus sınıfı yok) | `symfony/event-dispatcher` zaten require'da |
| IPC | ADR-032 | `shared/src/Ipc/` (klasör yok) | ADR-032 kapsam onayı |
| Credential Vault Kodu | ADR-034 | `shared/src/Security/` | Şifreleme sınıfı tasarım onayı |

Kural: Bu tablodaki hiçbir bileşen "IMPLEMENTED" yazılamaz; kod geldiğinde satır güncellenir ve `log.md`'ye kayıt düşülür.

---

## 18. L0 Diagnostics Komutları (tekrarlanabilir)

```powershell
# 1. L0 kod varlığı
"Cache","Database","Security" | ForEach-Object {
  Get-ChildItem -LiteralPath "shared\src\$_" -Filter "*.php" -ErrorAction SilentlyContinue |
    Select-Object Name
}

# 2. CacheManager adapter zinciri doğrulama
Select-String -LiteralPath "shared\src\Cache\CacheManager.php" -Pattern "Apcu|Memory"

# 3. PDO parametreleri doğrulama
Select-String -LiteralPath "shared\src\Database\DatabaseManager.php" -Pattern "ERRMODE|EMULATE_PREPARES"

# 4. 18 şema sayımı (beklenen: 18)
(Get-ChildItem -LiteralPath ".ai\.sql\mysql" -Filter "*.sql").Count

# 5. Redis iddia kontrolü (beklenen: 0 sonuç → PLANNED doğru)
Get-ChildItem -LiteralPath "shared\src\Cache" -Filter "*Redis*" -ErrorAction SilentlyContinue

# 6. composer bağımlılık kanıtı
Select-String -LiteralPath "shared\composer.json" -Pattern "psr/cache|event-dispatcher|php-di"
```

Bu komutlar Faz 0'da (2026-09-08) fiilen çalıştırılmıştır; §14-§17 tabloları bu çıktıdan üretilmiştir.

---

## 19. Sık Sorulan Sorular

**S: Cache hangi backend'i kullanıyor?**
C: Çalışma ortamına göre: APCu varsa `ApcuAdapter`, yoksa `MemoryAdapter`. Redis PLANNED'tir — §14 tablosu kanıttır.

**S: Redis'i şimdi ekleyebilir miyim?**
C: Giriş önkoşulları §17 tablosundadır: ADR onayı + `predis/predis` bağımlılığı + `RedisAdapter` sınıfı. plansız bağımlılık yasaktır ([[CLAUDE.md]] Hard Guardrails).

**S: APCu olmayan ortamda ne olur?**
C: `MemoryAdapter` devreye girer — tek süreç içinde çalışır; çoklu worker'da cache paylaşılmaz. Üretim dağıtımında APCu (veya gelecekte Redis) zorunlu.

**S: 18 veritabanı gerçekten var mı?**
C: Şema düzeyinde EVET — 18 .sql dosyası, 156 tablo (`.ai/.sql/mysql/`). Çalışan DB bağlantısı `DatabaseManager` ile kurulur; sunucu kurulumu ortam bağımlısıdır.

**S: ORM neden kesin yasak?**
C: ADR-002 (frozen) — PDO prepared statement zorunlu. Bu yalnız PHP kuralı değildir; Node.js/C# tarafına da ORM yasağı genellemesi [[engine.md]] §9.5.4'te sabittir.

**S: Credential vault kodda nerede?**
C: Doküman + şema düzeyi IMPLEMENTED; şifreleme sınıfı kodu `shared/src/Security/` altında beklemektedir (§17). ADR-034 kapsam onayı sonrası üretilir.

**S: PSR-14 event bus composer'da var ama neden PLANNED?**
C: Bağımlılık (`symfony/event-dispatcher ^7.0`) require listesinde; ancak bus sınıfı/abonelik yapısı kodda henüz yoktur. Bağımlılık varlığı tek başına IMPLEMENTED kanıtı değildir — sınıf kanıtı gerekir.

**S: Event Bus'a ilk event nasıl eklenir?**
C: ADR-086 kapsamında domain event sözleşmesi yazılır ([[ROLE.md]] §20.4 pattern) → `shared/src/Events/` altına sınıf → PSR-14 dispatcher'a kayıt → test. plansız event sınıfı yasaktır.

---

## 20. İzlenebilirlik Tablosu

| İddia (bu dosya) | Kaynak | Doğrulama |
|------------------|--------|-----------|
| CacheManager APCu→Memory | `shared/src/Cache/CacheManager.php` | Kod okuma (Faz 0) |
| DatabaseManager PDO parametreleri | `shared/src/Database/DatabaseManager.php` | Kod okuma |
| `rl:` prefix + 60/60sn | `shared/src/Middleware/RateLimiterMiddleware.php` | Kod okuma (93 satır) |
| `CacheRateLimiter` IRateLimiter | `shared/src/Security/CacheRateLimiter.php` | Kod okuma (41 satır) |
| PageCacheAdapter → PageRouter param 7 | `shared/src/PageRouter/PageRouter.php` | Kurucu imza okuma |
| 18 şema / 156 tablo | `.ai/.sql/mysql/` + [[architecture/05-data/database_master]] | Dosya sayımı |
| 2 composer paketi | `shared/composer.json`, `packages/shared/composer.json` | Okuma |
| Redis YOK | `shared/src/Cache/` glob | Test-Path / glob |

---

## 21. Revizyon Geçmişi

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 4.0.0 | 2026-08-13 | Katman index yapısı |
| 5.0.0 | 2026-09-08 | Faz 2a: Redis PLANNED etiketi; §14-§18 kod karşılıkları/diagnostics; §19 SSS; §20 izlenebilirlik |

## 22. Bileşen Derin Özetleri (alt dosya haritaları)

L0'ın dört büyük dokümanının bölüm haritası — hangi detayın nerede olduğunu bulmak için:

### 22.1 [[cache]] (663 satır)

| Bölüm | Konu |
|-------|------|
| Multi-tier mimari | APCu → Memory zinciri, seçim mantığı |
| Namespace isolation | ADR-007 standartı, `rl:` gibi önekler |
| Stampede koruması | Mutex/single-load hedefi |
| TTL politikaları | Katman bazlı ömür |
| PLANNED: Redis | Dağıtık cache geçiş şeması |

### 22.2 [[database]] (650 satır)

| Bölüm | Konu |
|-------|------|
| 18 BCNF şema haritası | DB başına tablo sayısı (156 toplam) |
| PDO kuralları | Prepared statement, EMULATE_PREPARES=false |
| Repository pattern | IDatabaseManager sözleşmesi |
| Migration stratejisi | Forward-only, versioned (ADR-014) |
| Soft delete | `is_deleted` standardı |

### 22.3 [[filesystem]] (706 satır)

| Bölüm | Konu |
|-------|------|
| Medya dizin düzeni | Upload/asset ayrımı |
| PSR-17 stream | Dosya I/O soyutlaması |
| Upload güvenliği | MIME + extension doğrulama |
| Dual-mode storage | ADR-027 hibrit depolama |
| Kuota/disk politikaları | Boyut sınırları |

### 22.4 [[credential-vault]] (641 satır)

| Bölüm | Konu |
|-------|------|
| AES-256-GCM şema | 96-bit IV, 16-byte tag, 256-bit key |
| Argon2id parametreleri | 64MB / 4 iterasyon / 2 thread |
| Secret yaşam döngüsü | Üretim → depolama → rotasyon |
| ADR-034 normalizasyon | vault şema yapısı |
| Redaction politikası | Log'larda `[REDACTED]` |

---

## 23. Yedekleme ve Kurtarma (L0 Perspektifi)

| Kaynak | Yöntem | Sıklık | Hedef Süre |
|--------|--------|--------|------------|
| 18 BCNF şema | Git (.ai/.sql/mysql/) | Her commit | <1 dk |
| DB verisi | mysqldump (kullanıcı ortamı) | Ortama bağlı | Ortam hedefi |
| Migration durumu | `coremusic_patch` DB şeması | Her migration | — |
| Credential vault | Şifreli yedek (kod üretimi PLANNED) | — | — |

**Kural:** Şema dosyaları Git'tedir; veri yedekleme politikası deployment ortamı kararıdır ([[../02-deployment/index]]).

---

## 24. Performans Hedefleri (L0)

| İşlem | Hedef | Ölçüm Yöntemi | Durum |
|-------|-------|---------------|-------|
| Cache read (APCu) | <5ms | Benchmark | PLANNED (benchmark kodu yok) |
| DB prepared query | <100ms (tipik) | PHPUnit entegrasyon testi | PLANNED |
| Upload write | <100ms | Integration | PLANNED |
| ADR-006 genel | TTFB <200ms, API <100ms | ADR-006 | Hedef tanımı |

Benchmark uydurma YASAKTır ([[CLAUDE.md]] §8.1) — ölçüm altyapısı kurulmadan "hedefe ulaşıldı" yazılamaz.

---

## 25. Veri Akışı (L0 Perspektifi)

Bir web isteğinin L0 bileşenlerinden geçiş hattı (kod kanıtlı hatlar düz, PLANNED hatlar kesikli):

```
Controller (L2 PageRouter dispatch)
   │
   ├─► Repository (domain katmanı, auth: include/Repository/)
   │      └─► IDatabaseManager ──► DatabaseManager (PDO, prepared)
   │                                └─► MySQL 9 (18 BCNF şema)          [ŞEMA HAZIR]
   │
   ├─► CacheManager (Singleton) ──► ApcuAdapter | MemoryAdapter         [IMPLEMENTED]
   │      ├─ rl: pencere sayacı (RateLimiterMiddleware tüketimi)
   │      └─ PageCacheAdapter (PageRouter ctor param 7)
   │
   ├─► SecurityHeaders/Csrf (L1) ──► nonce + token üretim/teslim          [IMPLEMENTED]
   │
   ┄┄► CredentialVault ┄┄► AES-256-GCM                                   [PLANNED]
   ┄┄► Event Bus (PSR-14) ┄┄► dispatcher                                 [PLANNED]
   ┄┄► IPC ┄┄► JSON/msgpack                                              [PLANNED]
   ┄┄► Redis ┄┄► RedisAdapter                                            [PLANNED]
```

Okuma kuralı: Kesikli hatlar üzerinde hiçbir kod iddiası yapılamaz; hat devreye alındığında bu şema güncellenir ve `log.md`'ye kayıt düşülür.

---

## 26. 18 BCNF Veritabanı Listesi (Kanıtlı)

Kaynak: `.ai/.sql/mysql/` (18 .sql dosyası) + [[architecture/05-data/database_master]] (407 satır):

| # | Veritabanı | Tablo | Amaç Özeti |
|---|------------|-------|------------|
| 1 | coremusic_auth | 13 | Users, roles, sessions, tokens, credential vault, API keys |
| 2 | coremusic_user | 7 | Profiller, tercihler, geçmiş, favoriler |
| 3 | coremusic_musics | 22 | Songs, artists, genres, lyrics, files, podcast, video, radio |
| 4 | coremusic_albums | 5 | Albüm koleksiyonları, diskler, istatistik |
| 5 | coremusic_playlist | 5 | Kullanıcı + AI playlist, işbirlikçi, takipçi |
| 6 | coremusic_catalog | 8 | Referans veri (tür, rol, enstrüman, mood) |
| 7 | coremusic_logs | 22 | Audit trail, analitik, hata, performans |
| 8 | coremusic_media | 8 | Cihaz senkron, metadata, erişim kontrolü |
| 9 | coremusic_system | 17 | Ayarlar, config, cache, EQ, dosya yöneticisi, i18n |
| 10 | coremusic_social | 9 | Yorum, paylaşım, aktivite, dinleme odaları |
| 11 | coremusic_wireless | 5 | WiFi + Bluetooth ağları |
| 12 | coremusic_ai | 6 | Tercih profilleri, öneriler |
| 13 | coremusic_api | 4 | API key, rate limit, çağrı logları, webhook |
| 14 | coremusic_cms | 8 | Sayfalar, blog, etiket, banner, SSS |
| 15 | coremusic_download | 4 | İndirme kuyruğu, geçmiş, cache, kaynak API |
| 16 | coremusic_neva | 4 | EQ preset, DSP ayarları, routing matrisi |
| 17 | coremusic_studio | 6 | Stüdyo oturumları, parçalar, ekipman |
| 18 | coremusic_patch | 3 | Şema sürümleri, migration log, yamalar |
| | **TOPLAM** | **156** | — |

---

## 27. Bileşen İlişki Matrisi

| Bileşen | Tüketen | Sağlanan Sözleşme | Not |
|---------|---------|-------------------|-----|
| `DatabaseManager` | Domain repository'ler (auth öncelikli) | `IDatabaseManager` | ORM yasak |
| `CacheManager` | RateLimiter, PageCache, gelecek servisler | PSR-16 CacheInterface | Singleton |
| `PageCacheAdapter` | PageRouter (ctor param 7) | `PageCacheInterface` | Sayfa yanıtı önbelleği |
| `CacheRateLimiter` | RateLimiterMiddleware | `IRateLimiter` | 41 satır |
| `SessionInitializer` | Tüm giriş noktaları | `ensureStarted(): void` | `COREMUSIC_SESS` — kopya sorunu var |
| `HomeAuthBridge` | home domain | — | validate-key POST (L1/L2 sınırları) |

**İlişki kuralı:** L0 sınıfları yukarı katman sınıflarını import edemez (Layer Violation — derhal revert). Bağımlılık yönü her zaman yukarıdan L0'a doğrudur.

---

## 28. Yapılandırma Kaynakları

| Kaynak | Konum | L0 Kullanımı |
|--------|-------|--------------|
| Composer manifest | `shared/composer.json` | psr/cache, event-dispatcher, php-di bağımlılıkları |
| `.env` | Proje kökü (vlucas/phpdotenv — auth'ta) | DB DSN, secret referansları (değerler vault'a) |
| Domain config | `shared/config/domain.php` (0.5KB) | Domain sabitleri — LSP bulgusuyla ilişkili |
| Route config | `shared/config/routes.php` (3.3KB) | L2 tüketir, L0 değil |
| OAuth platform config | `shared/config/oauth-platforms.php` (10.8KB) | `shared/src/OAuth/` tüketir |

**LSP bulgusu:** `SESSION_NAME`, `PAGES_PATH`, `TRUSTED_PROXIES` sabitleri tanımsız görünüyor — büyük olasılıkla config katmanından yüklenmeli; çözüm kod onayı bekliyor ([[engine.md]] §8.1 #7).

---

## 29. Yeni L0 Bileşeni Ekleme Prosedürü

1. **İhtiyaç analizi:** Hangi L1-L3 bileşeni neyi tüketecek? (Tüketici yoksa bileşen yazılmaz — YAGNI.)
2. **ADR kontrolü:** Konu bir ADR kapsamında mı (Redis→ADR-007/013, IPC→ADR-032)? Kapsamda değilse önce ADR draft.
3. **Sözleşme:** `shared/src/Interfaces/` altında interface yaz (`IXxx`), PSR uyumlu.
4. **Implementasyon:** `final class` + `declare(strict_types=1)`, constructor injection, framework yok.
5. **Test:** PHPUnit (paket sürümüne göre ^10.5/^11.0), coverage ≥80%.
6. **Statik analiz:** PHPStan level 5 (`composer stan`).
7. **Doküman:** Bu index §14 tablosuna satır + ilgili alt dosya; IMPLEMENTED etiketi kod kanıtıyla.
8. **Audit:** `log.md` append + faz kontrol listesi ([[engine.md]] §12.6).

Yasak kısayollar: interface'siz sınıf, plansız composer bağımlılığı, test'siz merge, kanıtsız IMPLEMENT etiketi.

---

## 30. Anti-Patternler (L0)

| ❌ Yasak | Neden | ✅ Doğru |
|----------|-------|----------|
| ORM import (Eloquent/Doctrine) | ADR-002 | PDO prepared + repository |
| `SELECT *` | Sütun kayması, injection yüzeyi | Açık sütun listesi |
| `DELETE FROM` hard delete | Veri kaybı | `is_deleted = 1` soft delete |
| Secret'ı config/comment'e yazmak | Sızıntı | Credential vault + `[REDACTED]` |
| "Redis entegre edildi" (kod yokken) | Truth Mode ihlali | PLANNED etiketi + ADR |
| Singleton içinden yukarı katman import | Layer violation | Bağımlılık enjeksiyonu aşağı-yönlü |
| APCu olmayan ortamda Redis varsayımı | Çalışma zamanı hatası | Adapter zinciri + ortam tespiti |
| Test'siz adapter | Regresyon riski | PHPUnit + PHPStan geçişi |

---

## 31. Ek SSS

**S: CacheManager neden Singleton?**
C: Adapter seçimi tek sefer yapılır ve tüm tüketici aynı backend'i görsün. Çoklu örnek, rate limit sayacının ikiye bölünmesi gibi tutarsızlıklar üretir.

**S: PageCache neden PageRouter'a enjekte ediliyor?**
C: Sayfa önbelleği dispatch kararıyla iç içedir (render önce/sonra). Kurucu param 7 ile geçmesi, PageRouter'ın cache'e doğrudan bağımlılık yazmasını engelleyen sözleşme tasarımıdır (DIP).

**S: 156 tablonun kaçı kodda kullanılıyor?**
C: DOĞRULAMA GEREKLİ — şema tam, kod kısmi (auth+home). Kullanım haritası Faz 2i (05-data) kapsamında çıkarılacaktır.

**S: Migration forward-only ne demek?**
C: ADR-014: geri alınabilir migration yoktur; hata durumunda ileri düzeltme migration'ı yazılır. `coremusic_patch` DB'si sürüm izini tutar.

**S: L0'a test nasıl yazılır?**
C: PDO/Cache mock'lanabilir (PSR sözleşmeleri sayesinde). Entegrasyon testi gerçek MySQL ister; ortam kurulumu deployment kararına bağlıdır.

**S: Neden iki composer paketi var?**
C: ADR-085 geçişi: `coremusic/shared-infrastructure` (ana) + `coremusic/shared` (uuid/sodium taşıyan yeni kök). Birleşim senaryosu [[ROLE.md]] §11.2'de planlıdır.

## 32. L0 Görev Örnekleri (Senaryo Kütüphanesi)

### 32.1 Senaryo — Yeni Cache Tüketici Modülü

```
GÖREV: Ayarlar servisi için yapılandırma önbelleği.
ADIM 1: Tüketici analizi — SystemController config okumayı N kez yapıyor (N>1 meşru).
ADIM 2: ADR-007 namespace: anahtar öneki "sys:config:" tanımlanır.
ADIM 3: Kod: CacheManager singleton'dan instance → CacheInterface get/set.
ADIM 4: TTL kararı: config değişimi nadir → 300s TTL.
ADIM 5: Test: hit/miss/invalidation üçlüsü PHPUnit.
ADIM 6: Doküman: cache.md bölüm + bu index §15 tüketim tablosuna satır.
YASAK: Namespace'siz anahtar, TTL'siz set, test'siz merge.
```

### 32.2 Senaryo — Migration Ekleme

```
GÖREV: coremusic_system'e yeni tablo (user_widgets).
ADIM 1: BCNF denetimi — anahtar bağımlılıkları tek aday anahtara mı? (ADR-040)
ADIM 2: Migration dosyası: shared/database/migrations/ — forward-only, versioned.
ADIM 3: coremusic_patch'e sürüm kaydı.
ADIM 4: Şema .sql güncelle: .ai/.sql/mysql/coremusic_system.sql (kanonik kaynak).
ADIM 5: database_master.md (407 satır) tablo sayımı güncelle (17→18).
ADIM 6: Repository + test; SELECT * yok, soft delete kolonu dahil.
KURAL: Geri alınabilir migration yazılmaz (ADR-014) — hata → ileri düzeltme migration.
```

### 32.3 Senaryo — APCu Olmayan Ortam Tespiti

```
BELİRTİ: Rate limit sayaçları worker'lar arasında tutarsız.
ADIM 1: CacheManager zinciri oku → MemoryAdapter devrede (APCu yok) → process-local.
ADIM 2: Kök neden: Windows dev ortamı / APCu eklenti kurulu değil.
ADIM 3: Seçenekler: (a) APCu kur (üretim uyumlu), (b) Redis PLANNED'i hızlandır (ADR gerekli).
ADIM 4: Karar kaydı: log.md + memory; sessiz "çalışıyor gibi" kabulü yok.
DERS: MemoryAdapter fallback'i güvenlik sayacı için üretim uygunluğu DEĞİLDİR.
```

---

## 33. Araçlar ve Scriptler

| Araç | Konum | Kullanım |
|------|-------|----------|
| PHPStan | `composer stan` (level 5) | Statik analiz — L0 sınıfları dahil |
| PHPUnit | `composer test` (paket sürümüne göre) | Birim/entegrasyon |
| Test-Path taraması | engine §7.4 komutları | Kod-doküman cross-check |
| LSP taraması | Editör entegre | Tanımsız sembol yakalama (3 bilinen bulgu) |
| vault-integrity-check.ps1 | KAYIP — yeniden üretim bekliyor | Vault sağlığı |

---

## 34. Referans Sınıf İmzaları (kod okumasından)

```php
// shared/src/Cache/CacheManager.php — Singleton, adapter seçici
final class CacheManager
{
    public static function getInstance(): CacheManager;   // APCu var→Apcu, yok→Memory
    // PSR-16 CacheInterface yüzeyini tüketicilere sunar
}

// shared/src/Database/DatabaseManager.php — PDO MySQL
final class DatabaseManager implements IDatabaseManager
{
    // ERRMODE_EXCEPTION + EMULATE_PREPARES=false + utf8mb4
}

// shared/src/Cache/ — PageCache
final class PageCacheAdapter implements PageCacheInterface
{
    // PageRouter ctor param 7 — sayfa yanıtı önbelleği
}

// shared/src/Security/CacheRateLimiter.php — 41 satır
final class CacheRateLimiter implements IRateLimiter
{
    // "rl:" namespace; 60 istek/60 sn pencere tüketimi
}
```

Not: İmzalar kod okumasından özetlenmiştir; kesin parametre listeleri için dosyalar esastır. Bu bölüm "örnek alma" değil "konum bulma" amaçlıdır ([[WORKFLOW.md]] §8.1C — kod kopyalanmaz).

---

## 35. Risk Kaydı (L0)

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 1 | MemoryAdapter ile üretim deploy | Orta | Yüksek (sayaç/cache bölünmesi) | §19 SSS + deployment checklist |
| 2 | Redis'i plansız ekleme | Düşük | Yüksek | §29 prosedür + ADR kapısı |
| 3 | Migration geri alınabilir yazma | Düşük | Yüksek | ADR-014 forward-only kuralı |
| 4 | Secret'ı .env comment'ine yazma | Orta | Kritik | §30 anti-pattern + redaction taraması |
| 5 | Şema-kod sapması | Orta | Orta | §18 komut 4 (18 sayım) + Faz 2i |
| 6 | Singleton test izolasyonu zorluğu | Orta | Düşük | PSR sözleşme mock'ları |

---

## 36. Ek SSS

**S: `IDatabaseManager` neden interface?**
C: Domain katmanı somut PDO sınıfını bilmemeli (DIP) — testlerde mock, üretimde gerçek bağlantı. Interface `shared/src/Interfaces/` altındadır.

**S: utf8mb4 neden şart?**
C: Türkçe/emoji içerik ve 4-byte UTF-8 karakterler; MySQL utf8 (3-byte) veri kaybına yol açar. 18 şema dosyalarında charset tanımı kanoniktir.

**S: Cache invalidation stratejisi ne?**
C: Yazımda ilgili namespace anahtarları silinir; TTL üst sınırdır. Tag-based invalidation PLANNED — mevcut kodda düz silme.

**S: L0 içinde loglama var mı?**
C: PSR-3 (`psr/log ^3.0`) bağımlılığı hazır; Monolog entegrasyonu ve deep-logging akışı [[../07-security/deep-logging-system]] (874 satır) dokümanındadır — kod karşılığı Faz 2f kapsamı.

**S: Neden bu dosya PLANNED'leri bu kadar vurguluyor?**
C: Faz 0 bulgusu: vault-kod sapmasının en büyük kaynağı "dokümanda var, kodda yok" satırlarıydı. Etiket disiplini bu sapmayı kalıcı kapatır.

---

---
