---
title: "CoreMusic — auth.coremusic.net Agent Talimatları"
type: agent-registry
folder: "auth.coremusic.net"
category: domain
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# auth.coremusic.net — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[./CLAUDE.md]] · [[../.ai/architecture/08-auth/index.md]]

## 1. Amaç

Kimlik doğrulama servisi (ADR-043: Auth Subdomain Consolidation). Hexagonal/DDD katmanlı PHP 8.4 uygulaması; login, register, şifre sıfırlama ve gender seçim akışlarını yürütür.

## 2. İçerik Envanteri

| Yol | Amaç |
|-----|------|
| `index.php` | Front controller |
| `autoload.php`, `composer.json`, `composer.lock` | PSR-4 autoloading |
| `phpunit.xml`, `.phpunit.result.cache` | Test altyapısı |
| `.htaccess`, `web.config` | Apache/IIS rewrite kuralları |
| `config/.env.example` | Ortam değişkeni şablonu |
| `config/app.php`, `config/constants.php`, `config/cors.php` | Uygulama + CORS ayarları |
| `include/Container/AuthContainer.php` | DI konteyneri |
| `include/Controller/AuthController.php` | HTTP girişi |
| `include/Domain/DTO/` | AuthResponse, LoginRequest, RegisterRequest |
| `include/Domain/Entity/User.php` | User aggregate |
| `include/Domain/ValueObject/` | Email, Gender, Password, UserId |
| `include/Handler/` | AuthKeyRedirect, AuthPost, AutoRedirect handler'ları |
| `include/Middleware/` | Pipeline, OriginCheck, RateLimit, SecurityHeaders, Session + MiddlewareInterface |
| `include/Repository/UserRepository.php` | Kalıcılık (PDO) |
| `include/Service/` | AuthService, SessionManager |
| `pages/` | login, register, forgot-password, reset-password, select-gender, set-gender, logout |
| `tests/` | bootstrap + Unit (DTO, Entity, ValueObject testleri) |

## 3. Agent Sorumlulukları

| Agent | Görev |
|-------|-------|
| Backend Architect | Hexagonal akış: Controller → Handler → Service → Repository |
| Security Engineer | ADR-010 CSRF, ADR-011 Session, ADR-013 Rate Limiting uyumu |
| QA Engineer | PHPUnit 10 testleri; ValueObject kapsamı genişletilirken mevcut testler korunur |

## 4. Kurallar

### Zorunlu
1. ORM yasak — Repository yalnızca PDO prepared statement (ADR-002)
2. Yeni PHP dosyası `[[../.ai/.templates/backend/php-template.md]]`'den türetilir
3. Argon2id şifre hash'i; `Password` ValueObject dışında hash üretimi yasak
4. Test ekleme → `tests/Unit` hiyerarşisine, `phpunit.xml` suite yapısına uygun
5. Yeni middleware → `MiddlewareInterface` implementasyonu + Pipeline kaydı

### Yasak
1. `config/.env` içeriğini okuyup vault'a veya chat'e yazmak (Guardrail: Secret Yok)
2. Domain katmanından framework/superglobal erişimi
3. Session/cookie değerlerini log'a yazmak
4. `pages/*.php` içine iş mantığı (business logic) gömmek — mantık Service'te

## 5. İlgili Kaynaklar

| Kaynak | Yol |
|--------|-----|
| Auth mimarisi | [[../.ai/architecture/08-auth/auth-domain.md]] |
| Auth akışı | [[../.ai/architecture/08-auth/auth-flow.md]] |
| Ortak middleware | [[../shared/src/Middleware/AGENTS.md]] |
| PHP şablonu | [[../.ai/.templates/backend/php-template.md]] |
| DB şema | [[../.ai/.sql/mysql/coremusic_auth.sql]] |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
