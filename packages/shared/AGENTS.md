---
title: "CoreMusic — packages/shared Agent Talimatları"
type: agent-registry
folder: "packages/shared"
category: shared
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# packages/shared — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[../shared/AGENTS.md]] · [[./CLAUDE.md]]

## 1. Amaç

Alt domainler arası paylaşılan sözleşme (contract) ve value katmanı paketi. Interface + DTO + Enum + Event odaklıdır; iş mantığı barındırmaz.

## 2. İçerik Envanteri

| Yol | Amaç |
|-----|------|
| `composer.json`, `phpunit.xml`, `README.md` | Paket tanımı + test config |
| `src/Contract/Http/` | MiddlewareInterface, RequestInterface, ResponseInterface |
| `src/Contract/Repository/` | MediaRepositoryInterface, PlaylistRepositoryInterface, UserRepositoryInterface |
| `src/Contract/Service/` | AuthServiceInterface, MediaServiceInterface |
| `src/DTO/Auth/` | LoginRequest, LoginResponse, TokenPair |
| `src/DTO/Media/` | MediaFileDTO, MediaUploadDTO |
| `src/DTO/Playlist/` | PlaylistDTO, PlaylistItemDTO |
| `src/DTO/User/` | UserDTO, UserProfileDTO |
| `src/Enum/` | DeviceType, MediaType, PermissionAction, UserRole |
| `src/Event/` | MediaUploaded, PlaylistCreated, UserLoggedIn, UserLoggedOut |
| `src/Exception/` | Authentication, Authorization, NotFound, Validation |
| `src/Helper/` | ArrayHelper, DateTimeHelper, StringHelper |
| `src/Http/` | Headers, StatusCode |
| `src/Security/` | Cipher, Hasher, TokenGenerator |
| `src/Validation/` | EmailRule, PasswordRule, ValidationResult |
| `src/ValueObject/` | Email, MediaId, PlaylistId, UserId |
| `tests/Unit/` | StringHelperTest, EmailTest |

## 3. Agent Sorumlulukları

| Agent | Görev |
|-------|-------|
| Backend Architect | Contract değişiklikleri (breaking change → ADR) |
| QA Engineer | Helper/ValueObject test kapsamı genişletme |

## 4. Kurallar

### Zorunlu
1. Contract (interface) değişikliği breaking ise → ADR + versiyon notu
2. ValueObject değişmez (immutable); tüm state constructor'da
3. Her yeni helper → test ile birlikte
4. PSR-4 namespace `CoreMusic\Shared\*` çizgisinde (composer.json doğrulanır)

### Yasak
1. Bu pakette DB bağlantısı, dosya sistemi I/O, HTTP çağrısı (yan etkili I/O yasak — saf katman)
2. Subdomain'e özel konfig okuma
3. ORM (ADR-002)

## 5. İlgili Kaynaklar

| Kaynak | Yol |
|--------|-----|
| Kök ikiliği | [[../shared/AGENTS.md]] |
| PHP şablonu | [[../../.ai/.templates/backend/php-template.md]] |
| PHPUnit şablonu | [[../../.ai/.templates/testing/phpunit-template.md]] |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
