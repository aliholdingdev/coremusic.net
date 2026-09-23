---
title: "CoreMusic — auth.coremusic.net/include/Repository Bağlam"
type: context
folder: "auth.coremusic.net/include/Repository"
category: layer0-infrastructure
date: 2026-09-21
updated: 2026-09-21
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
---

# Repository — CLAUDE.md (Detaylı)

**Zorunlu Bağlantılar:** · [[../CLAUDE.md]] · [[../../shared/src/Database/CLAUDE.md]]

---

## 1. Bağlam

Kalıcılık katmanı. UserRepository PDO prepared statement ile `coremusic_auth` veritabanına erişir. **ORM yasak** (ADR-002).

---

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Toplam dosya | 1 PHP dosyası |
| DB | `coremusic_auth` (13 tablo) |
| ADR | ADR-002 (PDO mandatory) |

### 2.1 Dosya Envanteri

| Dosya | Amaç |
|-------|------|
| `UserRepository.php` | User CRUD işlemleri |

---

## 3. UserRepository Metotları

| Metot | SQL | Dönüş |
|-------|-----|-------|
| `findById(UserId)` | `SELECT id, email, name FROM users WHERE id = :id AND is_deleted = 0` | User entity |
| `findByEmail(string)` | `SELECT id, email, name, password_hash FROM users WHERE email = :email AND is_deleted = 0` | User entity |
| `create(array)` | `INSERT INTO users (id, email, name, password_hash, created_at) VALUES (...)` | User entity |
| `update(User)` | `UPDATE users SET name = :name, updated_at = :now WHERE id = :id` | bool |
| `softDelete(UserId)` | `UPDATE users SET is_deleted = 1, deleted_at = :now WHERE id = :id` | bool |

---

## 4. Yasaklar

| # | Yasak | Neden |
|---|-------|-------|
| 1 | ORM (Eloquent, Doctrine) | ADR-002 yasağı |
| 2 | `SELECT *` | SQL injection riski |
| 3 | Hard delete | Soft delete zorunlu |
| 4 | Prepared statement olmadan sorgu | SQL injection |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
