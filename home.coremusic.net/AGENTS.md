---
title: "CoreMusic — home.coremusic.net Agent Talimatları"
type: agent-registry
folder: "home.coremusic.net"
category: domain
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# home.coremusic.net — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[./CLAUDE.md]] · [[../.ai/subdomains/home.coremusic.net/index.md]]

## 1. Amaç

Home Media Center — Raspberry Pi 5 üzerinde çalışan ana medya paneli. `auth.coremusic.net` ile bridge üzerinden kimlik doğrulama, `assets.coremusic.net` ile statik varlık paylaşımı yapar.

## 2. İçerik Envanteri

| Yol | Amaç |
|-----|------|
| `index.php` | Front controller |
| `header.php`, `footer.php` | Ortak HTML kabuk parçaları |
| `autoload.php`, `composer.json`, `composer.lock` | PSR-4 autoloading |
| `.htaccess`, `web.config` | Rewrite kuralları |
| `config/.env` | Ortam değişkenleri (SALT — içerik vault'a yazılmaz) |
| `config/app.php`, `bootstrap.php`, `config.php`, `constants.php` | Uygulama konfigürasyonu + bootstrap zinciri |
| `include/Auth/HomeAuthBridge.php` | auth.coremusic.net köprüsü |
| `include/Container/HomeContainer.php` | DI konteyneri |
| `include/Session/HomeSessionManager.php` | Oturum yönetimi |
| `pages/` | home, player, ayarlar, health, redirect, auth_callback |

## 3. Agent Sorumlulukları

| Agent | Görev |
|-------|-------|
| Backend Architect | Bridge akışı: `redirect.php` → auth servisi → `auth_callback.php` → oturum kurulumu |
| UI Designer | `pages/*.php` view'ları kanonik mockup'lara (`.ai/.png/home-1024/`) sadık kodlanır |
| QA Engineer | `health.php` sağlık kontrolü davranışı korunur |

## 4. Kurallar

### Zorunlu
1. Header/footer değişikliği — `home-1024` PNG mockup'larıyla karşılaştırıldıktan sonra (Header 60px y:0-60, İçerik 450px, Footer 90px kanonik referans)
2. Oturum işlemleri yalnızca `HomeSessionManager` üzerinden
3. Kimlik doğrulama akışı `HomeAuthBridge` dışına taşınmaz
4. Yeni sayfa → `pages/` + gerekiyorsa config bootstrap zincirine kayıt

### Yasak
1. `config/.env` içeriğini dokümante etmek/kopyalamak (Guardrail: Secret Yok)
2. `auth_callback.php` içinde token/oturum mantığını yeniden icat etmek
3. `vendor/` klasörüne elle müdahale
4. Mockup'ta olmayan UI ögesi eklemek (Guardrail #11)

## 5. İlgili Kaynaklar

| Kaynak | Yol |
|--------|-----|
| Subdomain vault kaydı | [[../.ai/subdomains/home.coremusic.net/index.md]] |
| Home mockup'ları | [[../.ai/.png/home-1024/]] (12 ekran) |
| PNG analizleri | [[../.ai/.png-analysis/B-home/]] |
| Flow tanımları | [[../.ai/ui-design/flow/music/01-playback.md]] |
| Home mimarisi | [[../.ai/architecture/08-auth/auth-cross-domain.md]] |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
