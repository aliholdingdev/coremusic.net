---
type: architecture
category: l0
title: "L0 — Infrastructure Layer"
date: 2026-08-08
updated: 2026-08-13
status: active
version: 4.0.0
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

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 18 BCNF MySQL veritabanı (ADR-040) | Frontend UI kodu |
| Multi-tier cache (APCu, Redis, File) | SPA routing |
| Dosya sistemi yönetimi (PSR-17) | Middleware pipeline |
| Credential vault (AES-256-GCM — ADR-022) | Güvenlik politikası tasarımı |
| Servisler arası iletişim (IPC — ADR-032) | Deployment süreçleri |
| Modüler paket altyapısı (coremusic/* — ADR-085) | İş mantığı |
| Event bus altyapısı (PSR-14 — ADR-086) | — |

---

## 3. Bileşenler

| Bileşen | Dosya | Amaç |
|---------|-------|------|
| **Database** | [[database]] | 18 BCNF MySQL veritabanı (ADR-040), PDO, prepared statement, migration |
| **Cache** | [[cache]] | Multi-tier cache: APCu → Redis → File, namespace isolation (ADR-007) |
| **Filesystem** | [[filesystem]] | Medya dosyaları, upload yönetimi, disk I/O, PSR-17 stream |
| **Credential Vault** | [[credential-vault]] | AES-256-GCM şifreleme (ADR-022), API key, token yönetimi |
| **Modüler Paketler** | — | tek shared/ + PSR-4 namespace (ADR-085 v3.0), circular dependency yasak |
| **Event Bus** | — | PSR-14 Event Dispatcher altyapısı (ADR-086) |
| **IPC** | — | Servisler arası iletişim, JSON/msgpack (ADR-032) |

---

## 4. Tech Stack

| Teknoloji | Versiyon | Kullanım | Kaynak |
|-----------|---------|----------|--------|
| MySQL | 9+ | Veritabanı | dev.mysql.com |
| InnoDB | — | Storage engine | dev.mysql.com |
| PHP PDO | 8.4+ | DB abstraction | php.net |
| APCu | 5.1+ | In-memory cache | pecl.php.net |
| Redis | 7+ | Distributed cache | redis.io |
| OpenSSL | 3.x | AES-256-GCM | openssl.org |

*Kaynak: MySQL 9.7 Reference Manual (dev.mysql.com), PHP 8.4 Manual (php.net) — 2026-08-08'da doğrulandı*

---

## 5. Sorumluluk Matrisi

| Bileşen | Veri Türü | Öncelik | SLA |
|---------|-----------|---------|-----|
| Database | Transactional veri | CRITICAL | 99.9% uptime |
| Cache | Hot data, session | HIGH | <5ms read |
| Filesystem | Medya, dosya | MEDIUM | <100ms write |
| Credential Vault | Secret, key | CRITICAL | 100% integrity |

---

## 6. Port Haritası

| Port | Servis | Protokol |
|------|--------|----------|
| 81 | Control Service (PHP 8.4) | HTTP |
| 5000/6000 | Media Service | HTTP |
| 9741 | Audio Service (REST) | HTTP |
| 9742 | Audio Service (WebSocket) | WS |
| 3001 | Download Service | HTTP/WS |
| 3306 | MySQL 9 | TCP |
| 6379 | Redis | TCP |

*Kaynak: [[ADR-042-vault-restructuring-2026-08-03]]*

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

---

## 8. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| Cache Stampede | Mutex ile single load | [[cache]] |
| DB Connection Loss | Retry + failover | [[database]] |
| File Upload Attack | MIME + extension check | [[filesystem]] |
| Race Condition | DB transaction + row lock | InnoDB |
| Credential Leak | Vault + redaction | [[credential-vault]] |

---

## 9. Test Kapsama Hedefleri

| Modül | Minimum | Hedef | Framework |
|-------|---------|-------|-----------|
| Database | ≥80% | ≥90% | PHPUnit 11 |
| Cache | ≥80% | ≥90% | PHPUnit 11 |
| Filesystem | ≥80% | ≥90% | PHPUnit 11 |
| Credential Vault | ≥90% | ≥95% | PHPUnit 11 |

---

## 10. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[database]] | 18 BCNF veritabanı, PDO, repository pattern |
| [[cache]] | Multi-tier cache, APCu, Redis, namespace |
| [[filesystem]] | Dosya yönetimi, upload, disk I/O |
| [[credential-vault]] | AES-256-GCM, Argon2id, secret yönetimi |
| [[l1-security]] | Security middleware, session, CSRF, CSP |
| [[architecture/05-data/database_master]] | Database master dokümanı |

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

---

## 12. Sözlük

| Terim | Tanım |
|-------|-------|
| **BCNF** | Boyce-Codd Normal Form — 18 BCNF DB için zorunlu normalizasyon |
| **PDO** | PHP Data Objects — veritabanı erişim soyutlama katmanı |
| **APCu** | APC User Cache — PHP in-memory önbellek |
| **Redis** | Remote Dictionary Server — dağıtık önbellek |
| **AES-256-GCM** | Advanced Encryption Standard, 256-bit, Galois/Counter Mode |
| **Argon2id** | Şifreleme algoritması (64MB/4/2) |
| **IPC** | Inter-Process Communication — servisler arası iletişim |
| **Soft Delete** | Kayıt silmek yerine `is_deleted = 1` ile işaretlemek |
| **Prepared Statement** | SQL injection önleme amaçlı parametreli sorgu |
| **Cache Stampede** | Yüksek eşzamanlı cache miss yükü |
| **Namespace** | Cache anahtarlarının servise göre ayrılması |

---

## 13. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 4.0.0 |
| **Status** | Red Team · Human Mode · Truth Mode verified |
| **Bileşen Sayısı** | 4 (Database, Cache, Filesystem, Credential Vault) |
| **ADR Uyumlu** | ✅ 002, 007, 022, 034, 040, 042 |
| **Cross-Reference** | ✅ Doğrulandı |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
