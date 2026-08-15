# 🎵 CoreMusic

**Kurumsal Seviyede Dijital Medya Yönetim Platformu**

[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES2022-F7DF1E?style=flat&logo=javascript&logoColor=black)](https://tc39.es/ecma262/)
[![C++](https://img.shields.io/badge/C++-20-00599C?style=flat&logo=cplusplus&logoColor=white)](https://isocpp.org/)
[![MySQL](https://img.shields.io/badge/MySQL-9-4479A1?style=flat&logo=mysql&logoColor=white)](https://dev.mysql.com/)
[![License](https://img.shields.io/badge/License-Proprietary-red)](#lisans)
[![Status](https://img.shields.io/badge/Status-Aktif%20Geliştirme-brightgreen)](#proje-durumu)

---

## 📋 İçindekiler

- [Proje Tanımı](#proje-tanımı)
- [Vizyon](#vizyon)
- [Temel Özellikler](#temel-özellikler)
- [Teknoloji Yığını](#teknoloji-yığını)
- [Mimari Yapı](#mimari-yapı)
- [Servis Haritası](#servis-haritası)
- [Veritabanı Yapısı](#veritabanı-yapısı)
- [Donanım Desteği](#donanım-desteği)
- [Kurulum Rehberi](#kurulum-rehberi)
- [Geliştirme Ortamı](#geliştirme-ortamı)
- [Katkı Rehberi](#katkı-rehberi)
- [Yol Haritası](#yol-haritası)
- [Lisans](#lisans)
- [İletişim](#iletişim)

---

## 🎯 Proje Tanımı

**CoreMusic**, bireysel kullanıcılar, profesyonel müzik üreticileri, stüdyolar, araç içi bilgi-eğlence ve ev medya merkezleri için tasarlanmış **kurumsal seviyede dijital medya yönetim platformudur**.

Platform yalnızca bir müzik oynatıcı değildir. Müzik indirme, yönetme, arşivleme, profesyonel ses yönetimi ve çoklu cihaz senkronizasyonu gibi kapsamlı yeteneklere sahiptir.

### Hedef Kullanıcılar

| Kullanıcı Grubu | Kullanım Senaryosu |
|-----------------|-------------------|
| 🎧 Bireysel Kullanıcılar | Kişisel müzik kütüphane yönetimi, çevrimdışı dinleme |
| 🎛️ Profesyonel Üreticiler | Stüdyo kalitesinde ses, 31-band EQ, DSP zinciri |
| 🏢 Stüdyolar | 8.1 surround ses, çoklu oda senkronizasyonu |
| 🚗 Araç İçi | CarPlay/Android Auto entegrasyonu,低 gecikmeli ses |
| 🏠 Ev Medya | NAS entegrasyonu, multi-room ses, ev otomasyonu |

---

## 🔭 Vizyon

CoreMusic, müzik dinleme ve yönetme deneyimini **katmanlı, modüler ve uzatılabilir** bir yapıyla yeniden tanımlamayı hedefler:

- **Faz 1 (MVP):** Mevcut PC/laptop'larda temel medya platformu
- **Faz 2 (Premium):** CoreMusic Audio donanım entegrasyonu (PCM3168A, XMOS XU316)
- **Faz 3 (Professional):** Tam entegre stüdyo ve araç içi sistemler

---

## ✨ Temel Özellikler

### 🎵 Medya Yönetimi
- Otomatik müzik indirme (YouTube, Deezer → FLAC/MP3)
- Kapsamlı müzik kütüphane yönetimi (sanatçı, albüm, tür, sözler)
- Metadata ve kapak görseli otomatik çekme
- Akıllı çalma listeleri (AI destekli öneriler)

### 🔊 Profesyonel Ses
- 31-band parametrik EQ
- DSP zinciri: EQ → Compressor → Limiter → Reverb
- 8.1 surround ses desteği (7.1 + LFE)
- ASIO/WASAPI düşük gecikmeli ses çıkışı

### 🖥️ Çoklu Panel
- 10 bağımsız web paneli (music, admin, download, media, auth, home, car, studio, pro, landing)
- Her panel için özel optimizasyon
- Responsive tasarım (masaüstü, tablet, mobil)

### 🤖 Yapay Zeka
- AI destekli müzik öneri sistemi
- Otomatik EQ optimizasyonu
- Akıllı ses analizi
- Otonom indirme kuyruğu

### 🔒 Güvenlik
- OWASP Top 10:2025 uyumluluğu
- AES-256-GCM şifreleme
- Argon2id parola hashing
- CSRF/Koruma, CSP nonce-based
- Rol bazlı erişim kontrolü (RBAC)

---

## 🛠️ Teknoloji Yığını

### Backend

| Teknoloji | Versiyon | Kullanım |
|-----------|----------|----------|
| PHP | 8.4+ | API, middleware, routing (strict_types) |
| MySQL/MariaDB | 9.x | 11 BCNF izole veritabanı |
| PDO | — | Ham SQL, prepared statement (ORM yasak) |
| APCu | — | Önbellek, rate limiting |
| Redis | — | Oturum depolama, pub/sub |
| Node.js | LTS | Download Service (TypeScript) |

### Frontend

| Teknoloji | Versiyon | Kullanım |
|-----------|----------|----------|
| Vanilla JS | ES6+ (ES2022) | SPA, DOM manipulation (Framework YASAK) |
| ITCSS | 7-layer | CSS mimarisi |
| BEM/BEMIT | — | CSS metodolojisi |
| TrustedTypes | — | XSS koruması |
| DOMParser | — | innerHTML yerine güvenli HTML işleme |

### Ses & Donanım

| Teknoloji | Versiyon | Kullanım |
|-----------|----------|----------|
| C++ | 20 | Audio Engine, DSP, Mixer |
| JUCE | 9.0.0 | Cross-platform ses framework'ü |
| ASIO SDK | 2.3.4 | Düşük gecikmeli ses sürücüsü |
| XMOS XU316 | — | USB Audio Class 2.0, zero-latency DSP |
| PCM3168A | — | 6-giriş/8-çıkış codec (24-bit) |
| AK4458 | — | Opsiyonel 8-kanal high-end DAC |

### Altyapı

| Teknoloji | Kullanım |
|-----------|----------|
| Docker | Containerizasyon |
| GitHub Actions | CI/CD pipeline |
| GitLeaks | Secret tarama |
| FFmpeg | Medya işleme |

---

## 🏗️ Mimari Yapı

### 4 Katmanlı Mimari (L0-L3)

```
┌─────────────────────────────────────────────────────────┐
│  L3 — Presentation  (Frontend, UI, DOM)                │
│  └── Vanilla JS, ITCSS, TrustedTypes, Web Audio        │
├─────────────────────────────────────────────────────────┤
│  L2 — Routing       (Router, middleware, dispatch)      │
│  └── PHP 8.4 PageRouter, SPA, Subdomain Routing        │
├─────────────────────────────────────────────────────────┤
│  L1 — Security      (Session, Auth, CSRF, CSP)         │
│  └── Middleware Pipeline, Argon2id, AES-256-GCM        │
├─────────────────────────────────────────────────────────┤
│  L0 — Infrastructure (Database, cache, fs)              │
│  └── PDO MySQL, APCu, Redis, Shared Memory             │
└─────────────────────────────────────────────────────────┘
```

**Bağımlılık Kuralları:**
- ✅ İzinli: L3→L2, L2→L1, L1→L0
- ❌ Yasak: L0→L2/L3, L1→L3, L3→L0 (Layer Violation → derhal revert)

### Middleware Pipeline (Sıra Değişmez)

```
SessionManager → BypassAuth → RateLimiter → Auth → SecurityHeaders → Csrf
```

| # | Middleware | Görev |
|---|-----------|-------|
| 1 | SessionManager | Session başlatır, CSP nonce üretir |
| 2 | BypassAuth | Test bypass (prod'da devre dışı) |
| 3 | RateLimiter | APCu tabanlı, 60 req/60s |
| 4 | Auth | Auth bilgisi inject, RBAC kontrolü |
| 5 | SecurityHeaders | CSP strict-dynamic, X-Frame-Options, HSTS |
| 6 | Csrf | `csrf_token` doğrulama (POST/PUT/DELETE) |

---

## 🗺️ Servis Haritası

### 10 Web Paneli

| # | Panel | Port | Stack |
|---|-------|------|-------|
| 1 | music.coremusic.net | 81 | PHP 8.4 + Vanilla JS |
| 2 | admin.coremusic.net | 80 | PHP 8.4 |
| 3 | download.coremusic.net | 3001 | Node.js + TypeScript |
| 4 | media.coremusic.net | 5000/6000 | PHP + FFmpeg |
| 5 | auth.coremusic.net | — | PHP 8.4 |
| 6 | home.coremusic.net | — | Vanilla JS |
| 7 | car.coremusic.net | — | Vanilla JS |
| 8 | studio.coremusic.net | — | Vanilla JS |
| 9 | pro.coremusic.net | — | Vanilla JS |
| 10 | coremusic.net | — | Vanilla JS (Landing) |

### 7 Backend Servisi

| # | Servis | Port | Protocol | Stack |
|---|--------|------|----------|-------|
| 1 | Control Service | 81 | HTTP | PHP 8.4 (Auth, Session, RBAC) |
| 2 | Media Service | 5000/6000 | HTTP | PHP + FFmpeg (Library, Metadata) |
| 3 | Audio Service | 9741/9742 | REST/WS | C++20 JUCE (Player, DSP, Mixer) |
| 4 | Device Service | — | BLE/WiFi/USB | C++20 (Bluetooth, WiFi, USB) |
| 5 | Network Audio | — | WebRTC/P2P | C++20 (Streaming, Multi-room) |
| 6 | AI Service | — | Internal | PHP + Python (Recommendations) |
| 7 | Download Service | 3001 | HTTP/WS | Node.js + TypeScript |

### Port Haritası

| Port | Servis | Protokol |
|------|--------|----------|
| 80 | admin.coremusic.net | HTTP |
| 81 | music.coremusic.net (Control) | HTTP |
| 3001 | download.coremusic.net | HTTP/WS |
| 3306 | MySQL 9 BCNF DB | TCP |
| 5000/6000 | media.coremusic.net | HTTP |
| 9741 | Audio Service (REST) | HTTP |
| 9742 | Audio Service (WebSocket) | WS |

---

## 🗄️ Veritabanı Yapısı

**11 BCNF İzole Veritabanı** (ADR-040)

| # | Veritabanı | Amaç | Tablo |
|---|------------|------|-------|
| 1 | coremusic_auth | Users, roles, sessions, tokens, API keys | 12 |
| 2 | coremusic_user | Profiles, preferences, history, favorites | 7 |
| 3 | coremusic_musics | Songs, artists, genres, lyrics, files | 12 |
| 4 | coremusic_albums | Album collections | 5 |
| 5 | coremusic_playlist | User and AI playlists | 5 |
| 6 | coremusic_catalog | Reference data (genres, instruments, moods) | 8 |
| 7 | coremusic_logs | Audit trail, analytics, error logs | 13 |
| 8 | coremusic_media | Device sync, media metadata | 8 |
| 9 | coremusic_system | Settings, config, cache, EQ presets | 13 |
| 10 | coremusic_social | Comments, shares, activity, rooms | 9 |
| 11 | coremusic_wireless | WiFi + Bluetooth networks | 5 |

**Toplam:** 17 veritabanı, ~100+ tablo, BCNF normalizasyonu

**Veritabanı Kuralları:**
- ❌ ORM yasak (Eloquent, Doctrine)
- ❌ `SELECT *` yasak (açık sütun listesi zorunlu)
- ✅ Prepared statement zorunlu
- ✅ Soft delete (`is_deleted = 0`)
- ✅ snake_case naming

---

## 🔧 Donanım Desteği

### Ses Donanımı

| Bileşen | Özellik | Durum |
|---------|---------|-------|
| XMOS XU316 | USB Audio Class 2.0, zero-latency DSP | ✅ Destekli |
| PCM3168A | 6-in/8-out codec, 24-bit, DAC 192kHz | ✅ Ana seçim |
| AK4458 | 8-kanal high-end DAC, 32-bit, 768kHz | ✅ Opsiyonel |
| PCM5122 | 2-kanal DAC | ❌ REDDEDİLMİŞ (H001) |

### Amplifikatör

| Sınıf | Güç | Kullanım |
|-------|-----|----------|
| Class AB | 100W @ 8Ω | Stüdyo, Pro, Araç |
| Class D | Değişken | Düşük güç, yüksek verim |

### Çevre Birimleri

- 🖥️ Masaüstü PC/Laptop
- 📱 Raspberry Pi 5 (ARM64)
- 🚗 Araç içi bilgi-eğlence sistemleri
- 📦 NAS cihazları (Synology, QNAP)

---

## 🚀 Kurulum Rehberi

### Ön Gereksinimler

```bash
# PHP 8.4+
php -v

# MySQL 9+
mysql --version

# Node.js LTS (Download Service için)
node -v
npm -v

# Composer
composer -V

# Git
git --version
```

### Kurulum Adımları

```bash
# 1. Depoyu klonlayın
git clone https://github.com/coremusic/coremusic.net.git
cd coremusic.net

# 2. PHP bağımlılıklarını kurun
composer install

# 3. JS bağımlılıklarını kurun (Download Service)
cd download-service
npm install
cd ..

# 4. Ortam değişkenlerini yapılandırın
cp .env.example .env
# .env dosyasını düzenleyin

# 5. Veritabanlarını oluşturun
mysql -u root -p < .sql/IMPORT_MANIFEST.sql

# 6. Composer senkronizasyonu
composer sync
```

### Geliştirme Sunucusu

```bash
# PHP geliştirme sunucusu (port 81)
php -S localhost:81 -t public/

# Download Service (port 3001)
cd download-service
npm run dev
```

### Docker Kurulumu (Opsiyonel)

```bash
docker-compose up -d
```

---

## 💻 Geliştirme Ortamı

### Editör Önerileri

| Editör | Eklenti |
|--------|---------|
| VS Code | PHP Intelephense, ESLint, Prettier |
| PhpStorm | PHP 8.4, Docker, Database Tools |
| Vim/Neovim | LSP, Treesitter |

### Kod Standartları

| Dil | Standart |
|-----|----------|
| PHP | PSR-12, strict_types, constructor injection |
| JavaScript | ES6+, const/let, async/await, no var |
| CSS | ITCSS 7-layer, BEM/BEMIT |
| C++ | C++20, noexcept, constexpr, alignas(64) |

### Testler

```bash
# PHP testleri
vendor/bin/phpunit

# JS testleri
npm test

# E2E testleri
npx playwright test
```

---

## 🤝 Katkı Rehberi

### Dal (Branch) Stratejisi

```
main ← producción
├── develop ← geliştirme
│   ├── feature/özellik-adı
│   ├── bugfix/hata-düzeltme
│   └── hotfix/acil-düzeltme
```

### Katkı Adımları

1. **Fork** yapın
2. **Branch** oluşturun (`feature/ozellik-adi`)
3. **Değişiklikleri** yapın
4. **Testleri** çalıştırın
5. **Commit** atın (Conventional Commits)
6. **Pull Request** açın

### Commit Formatı

```
<type>(<scope>): <description>

[optional body]

[optional footer]
```

**Türler:** `feat`, `fix`, `docs`, `style`, `refactor`, `test`, `chore`

### Code Review Kontrol Listesi

- [ ] Kod PSR-12 standartlarına uygun mu?
- [ ] Test coverage %80'in üzerinde mi?
- [ ] Güvenlik kontrolü yapıldı mı (OWASP)?
- [ ] Dokümantasyon güncellendi mi?
- [ ] Breaking change varsa ADR oluşturuldu mu?

---

## 🗺️ Yol Haritası

### 2026

| Çeyrek | Hedef |
|--------|-------|
| Q3 | MVP lansmanı, temel medya yönetimi |
| Q4 | Premium özellikler, AI öneri sistemi |

### 2027

| Çeyrek | Hedef |
|--------|-------|
| Q1 | CoreMusic Audio donanım entegrasyonu |
| Q2 | Profesyonel stüdyo modu |
| Q3 | Araç içi bilgi-eğlence sistemi |
| Q4 | Multi-room ses, NAS desteği |

### 2028

| Çeyrek | Hedef |
|--------|-------|
| Q1-Q2 | Uluslararası lansman |
| Q3-Q4 | Kurumsal çözümler |

---

## 📊 Proje İstatistikleri

| Metrik | Değer |
|--------|-------|
| Vault Dosyası | 529+ |
| ADR Sayısı | 72 |
| Veritabanı | 11 BCNF |
| Web Paneli | 10 |
| Backend Servisi | 7 |
| Platform Desteği | 5 (Windows, Linux, macOS, RPi, ReactOS) |
| Audio Division | 5 |
| Donanım Ailesi | 3 |

---

## 📜 Lisans

Bu proje **kapalı kaynak** (proprietary) lisansla yayınlanmaktadır.

Telif hakkı © 2026 CoreMusic. Tüm hakları saklıdır.

Detaylar için [Lisans Sözleşmesi](LICENSE) dosyasına bakın.

---

## 🤖 AI Engineering

CoreMusic, yapay zeka destekli geliştirme için aşağıdaki kaynakları içerir:

### Skills (10)

| Skill | Amaç | Yol |
|-------|------|-----|
| ui-code-generator | UI/CSS kod üretimi, responsive | [[.opencode/skills/ui-code-generator/SKILL.md]] |
| ui-analyzer | UI analizi, tasarım değerlendirme | [[.opencode/skills/ui-analyzer/SKILL.md]] |
| skill-maker | Skill oluşturma, template sistemi | [[.opencode/skills/skill-maker/SKILL.md]] |
| red-team-truth-mode | Güvenlik testi, adversarial analiz | [[.opencode/skills/red-team-truth-mode/SKILL.md]] |
| prompt-maker | Prompt mühendisliği, AI talimat | [[.opencode/skills/prompt-maker/SKILL.md]] |
| composer-sync | Composer dependency yönetimi | [[.opencode/skills/composer-sync/SKILL.md]] |
| agent-orchestrator | Agent görev dağıtımı | [[.opencode/skills/agent-orchestrator/SKILL.md]] |
| human-mode | İnsan modu, onay süreçleri | [[.opencode/skills/human-mode/SKILL.md]] |
| hallucination-control | Halüsinasyon kontrolü | [[.opencode/skills/hallucination-control/SKILL.md]] |
| database-normalize-maker | BCNF normalizasyonu | [[.opencode/skills/database-normalize-maker/SKILL.md]] |

### Templates (25)

| Kategori | Adet | Yol |
|----------|------|-----|
| ADR | 6 | [[.ai/.templates/adr/]] |
| Backend | 2 | [[.ai/.templates/backend/]] |
| Frontend | 2 | [[.ai/.templates/frontend/]] |
| Testing | 2 | [[.ai/.templates/testing/]] |
| Infrastructure | 3 | [[.ai/.templates/infrastructure/]] |
| Documentation | 3 | [[.ai/.templates/documentation/]] |
| Hardware | 3 | [[.ai/.templates/hardware/]] |
| Query | 1 | [[.ai/.templates/query/]] |
| Other | 3 | [[.ai/.templates/other/]] |

Detaylar: [[.ai/.templates/index.md]]

### AI Configuration

| Dosya | Amaç | Yol |
|-------|------|-----|
| CLAUDE.md | AI anayasası, guardrails | [[CLAUDE.md]] |
| AGENTS.md | Agent kayıt defteri | [[AGENTS.md]] |
| WORKFLOW.md | İş akışı tanımları | [[WORKFLOW.md]] |

---

## 📬 İletişim

| Kanal | Link |
|-------|------|
| 🌐 Web | [coremusic.net](https://coremusic.net) |
| 📧 E-Mail | info@coremusic.net |
| 🐛 Bug Raporu | [GitHub Issues](https://github.com/coremusic/coremusic.net/issues) |
| 💬 Tartışma | [GitHub Discussions](https://github.com/coremusic/coremusic.net/discussions) |

---

## 🙏 Teşekkürler

CoreMusic, aşağıdaki açık kaynak projelerinin katkılarıyla mümkün olmuştur:

- [PHP](https://php.net) — Web programlama dili
- [MySQL](https://mysql.com) — Veritabanı yönetim sistemi
- [JUCE](https://juce.com) — Cross-platform ses framework'ü
- [ASIO SDK](https://steinberg.net) — Düşük gecikmeli ses sürücüsü
- [FFmpeg](https://ffmpeg.org) — Medya işleme aracı
- [Node.js](https://nodejs.org) — JavaScript çalışma zamanı

---

**CoreMusic** — *Müzik deneyimini yeniden tanımlıyoruz.* 🎵

---

> 💡 **Not:** Bu proje aktif geliştirme aşamasındadır. API ve özellikler değişebilir.
> Güncel durum için [CHANGELOG](CHANGELOG.md) dosyasını takip edin.
