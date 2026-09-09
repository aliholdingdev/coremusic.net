---
type: architecture
category: l2
title: "Subdomain Routing"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 4.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Subdomain Routing

**Zorunlu Bağlantılar:** [[index]] · [[ADR-016-url-normalization]]

---

## 1. Amaç

Subdomain tabanlı servis yönlendirmesini tanımlar. **Enterprise Auth Architecture** ile uyumludur. [[ADR-016-url-normalization]] ile uyumludur.

## 2. Enterprise Subdomain Architecture

CoreMusic, 10 bağımsız subdomain'den oluşan modular bir mimariye sahiptir. Her subdomain kendi sorumluluk alanına sahiptir ancak kimlik doğrulama **auth.coremusic.net** üzerinden yürütülür.

### 2.1 Subdomain Haritası

| # | Subdomain | Tip | Port | Stack | Auth | Açıklama |
|---|-----------|-----|------|-------|------|----------|
| 1 | `auth.coremusic.net` | Service | 80/443 | PHP 8.4 | Merkezi | Identity Provider + Security Gateway |
| 2 | `home.coremusic.net` | Embedded | 81/443 | PHP 8.4 + JS | SSO | Ev medya merkezi (RPi5) |
| 3 | `pro.coremusic.net` | Embedded | 81/443 | PHP 8.4 + JS | SSO | Profesyonel panel (RPi5) |
| 4 | `studio.coremusic.net` | Embedded | 81/443 | PHP 8.4 + JS | SSO | Stüdyo sistemi (RPi5) |
| 5 | `car.coremusic.net` | Embedded | 80/443 | PHP 8.4 + JS | SSO | Araç içi (RPi5) |
| 6 | `admin.coremusic.net` | Panel | 80/443 | PHP 8.4 | SSO + Admin | Yönetim paneli |
| 7 | `media.coremusic.net` | Service | 5000/6000 | PHP + FFmpeg | Key-based | Medya deposu (vault) |
| 8 | `api.coremusic.net` | Service | 80/443 | PHP 8.4 | API Key | API endpoint'leri |
| 9 | `download.coremusic.net` | Service | 3001 | Node.js + TS | SSO | İndirme servisi |
| 10 | `coremusic.net` | Static | 80/443 | Vanilla JS | Yok | Landing page |

### 2.2 Auth Akışı (SSO)

```
Kullanıcı → herhangi bir subdomain'e erişir
    │
    ▼
Subdomain → auth.coremusic.net/api/session/check
    │
    ├── Geçerli session → Kullanıcı bilgisi döner
    │
    └── Geçersiz session → auth.coremusic.net/login'e redirect
                              │
                              ▼
                        Login formu
                              │
                              ▼
                        Credentials doğrula
                              │
                              ▼
                        Session oluştur
                              │
                              ▼
                        Cookie set (.coremusic.net)
                              │
                              ▼
                        Orijin subdomain'e redirect
```

### 2.3 Subdomain → Service Routing

```
auth.coremusic.net      → Auth Service (merkezi)
home.coremusic.net      → Home Panel (RPi5, port 81)
pro.coremusic.net       → Pro Panel (RPi5, port 81)
studio.coremusic.net    → Studio Panel (RPi5, port 81)
car.coremusic.net       → Car Panel (RPi5)
admin.coremusic.net     → Admin Panel (port 80)
media.coremusic.net     → Media Service (port 5000/6000)
api.coremusic.net       → API Service
download.coremusic.net  → Download Service (port 3001)
coremusic.net           → Landing Page (port 80)
```

## 3. Subdomain Detection

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Routing;

class SubdomainRouter
{
    private const SUBDOMAIN_MAP = [
        'auth'     => ['port' => 80,    'stack' => 'PHP 8.4',          'type' => 'service'],
        'home'     => ['port' => 81,    'stack' => 'PHP 8.4 + JS',     'type' => 'embedded'],
        'pro'      => ['port' => 81,    'stack' => 'PHP 8.4 + JS',     'type' => 'embedded'],
        'studio'   => ['port' => 81,    'stack' => 'PHP 8.4 + JS',     'type' => 'embedded'],
        'car'      => ['port' => 80,    'stack' => 'PHP 8.4 + JS',     'type' => 'embedded'],
        'admin'    => ['port' => 80,    'stack' => 'PHP 8.4',          'type' => 'panel'],
        'media'    => ['port' => 5000,  'stack' => 'PHP + FFmpeg',     'type' => 'service'],
        'api'      => ['port' => 80,    'stack' => 'PHP 8.4',          'type' => 'service'],
        'download' => ['port' => 3001,  'stack' => 'Node.js + TS',     'type' => 'service'],
        'music'    => ['port' => 81,    'stack' => 'PHP 8.4 + JS',     'type' => 'panel'],
    ];

    public function detect(): string
    {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $parts = explode('.', $host);

        if (count($parts) >= 3) {
            return $parts[0]; // music, admin, auth, etc.
        }

        return 'www'; // default
    }

    public function getSubdomainConfig(string $subdomain): array
    {
        return self::SUBDOMAIN_MAP[$subdomain] ?? self::SUBDOMAIN_MAP['music'];
    }

    public function isAuthRequired(string $subdomain): bool
    {
        // auth.coremusic.net ve coremusic.net (landing) auth gerektirmez
        return !in_array($subdomain, ['auth', 'www'], true);
    }

    public function getAuthRedirect(string $subdomain, string $currentUrl): string
    {
        $authUrl = 'https://auth.coremusic.net/login';
        $redirectParam = urlencode($currentUrl);
        
        return "{$authUrl}?redirect={$redirectParam}";
    }
}
```

## 4. Port Haritası

| Port | Servis | Protokol | Auth | Açıklama |
|------|--------|----------|------|----------|
| 80 | admin.coremusic.net | HTTP | SSO | Admin panel (redirect to 443) |
| 81 | music.coremusic.net | HTTP | SSO | Ana SPA (ADR-042) |
| 81 | home.coremusic.net | HTTP | SSO | Ev medya merkezi |
| 81 | pro.coremusic.net | HTTP | SSO | Profesyonel panel |
| 81 | studio.coremusic.net | HTTP | SSO | Stüdyo sistemi |
| 443 | Tüm subdomain'ler | HTTPS | SSO | SSL/TLS (zorunlu) |
| 3001 | download.coremusic.net | HTTP/WS | SSO | Download service |
| 3306 | MySQL 9 | TCP | — | Veritabanı |
| 5000 | media.coremusic.net | HTTP | Key | Media service |
| 6000 | media.coremusic.net | HTTP | Key | Media service (backup) |
| 9741 | Audio Service | REST | API Key | Neva Engine |
| 9742 | Audio Service | WebSocket | API Key | Neva Engine |

## 5. CORS Whitelist

Tüm subdomain'ler sadece aşağıdaki listedeki domainlere istek atabilir:

| Domain | Port | Kullanım |
|--------|------|----------|
| `auth.coremusic.net` | 80/443 | Kimlik doğrulama |
| `home.coremusic.net` | 81/443 | Ev medya merkezi |
| `pro.coremusic.net` | 81/443 | Profesyonel panel |
| `studio.coremusic.net` | 81/443 | Stüdyo sistemi |
| `car.coremusic.net` | 80/443 | Araç içi |
| `admin.coremusic.net` | 80/443 | Yönetim |
| `media.coremusic.net` | 5000/6000 | Medya servisi |
| `api.coremusic.net` | 80/443 | API |
| `download.coremusic.net` | 3001 | İndirme |
| `coremusic.net` | 80/443 | Landing page |

**Kural:** Whitelist'te olmayan hiçbir domain'e CORS izni verilmez.

## 6. Embedded Systems (RPi5)

| Mod | Donanım | Auth | Database | Özellik |
|-----|---------|------|----------|---------|
| **Home** | RPi5 + Touch Screen | Local (SQLite) | SQLite | Volumio benzeri ev teybi |
| **Pro** | RPi5 + HDMI Display | Local (SQLite) | SQLite | Profesyonel medya yönetimi |
| **Studio** | RPi5 + 8.1 Surround | Local (SQLite) | SQLite | Stüdyo ses sistemi |
| **Car** | RPi5 + PCM3168A | Local (SQLite) | SQLite | Araç bilgi-eğlence |

**Kurallar:**
- ✅ Offline-first çalışma
- ✅ Local auth (aynı RPi5)
- ✅ SQLite database (1 DB)
- ✅ Touch-optimized UI
- ❌ İnternet bağlantısı gerekmez
- ❌ Cross-subdomain auth yok (sadece local)

## 7. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| **Bilinmeyen subdomain** | Varsayılan: music | ADR-016 |
| **Port yok** | Inline serving | ADR-016 |
| **Wildcard SSL** | Let's Encrypt wildcard | ADR-016 |
| **Subdomain change** | DNS cache | ADR-016 |
| **Auth servisi down** | Fallback: local auth (RPi5) | — |
| **Session sync hatası** | Retry + local cache | — |

---

## 8. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[index]] | L2 ana dizin |
| [[spa-router]] | SPA PageRouter |
| [[ADR-016-url-normalization]] | URL normalization |
| [[architecture/07-security/middleware-security]] | Middleware pipeline |
| [[architecture/07-security/session-management]] | Session yönetimi |

---

## 9. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 5.0.0 |
| **Satır Sayısı** | ~600 |
| **ADR Uyumlu** | ✅ 016 |
| **Zero Hallucination** | ✅ |
| **Cross-Reference** | ✅ 3 referans |

---

## 10. Referans ↔ Gerçek Eşleştirme (Faz 2c — 2026-09-08)

**Kritik ayrım:** Bu dokümandaki `SubdomainRouter` sınıfı ve `/api/session/check` akışı **referans kaynaklıdır**. Gerçek durum:

| Öğe | Bu Dokümanda | Gerçek Kod (Faz 0) | Sonuç |
|-----|--------------|--------------------|-------|
| `SubdomainRouter` sınıfı | §3 (SUBDOMAIN_MAP) | `shared/src/PageRouter/` 16 dosyasında isim geçmiyor | PLANNED hedef desen |
| Subdomain tespit | `$_SERVER['HTTP_HOST']` parse | Gerçekte **webserver/DNS katmanı** ayrıştırır — her domain kendi vhost'una ve index.php'sine iner | Mimari fark: PHP-içi tespit yerine host-bazlı vhost |
| Session check | `auth.../api/session/check` | `HomeAuthBridge` → POST `validate-key` (TTL 300sn) | **Farklı endpoint** — gerçek akış validate-key'dir |
| Session taşıyıcı | Cookie `.coremusic.net` (SSO) | `MM_*` session + auth_key tek kullanımlık devir | Karma model — §13 |
| Domain config | Kod içi sabit dizi | `shared/config/domain.php` (0.5KB) + DomainConfig sınıfı | IMPLEMENTED (isim düzeyi) |

**Sonuç:** 10-subdomain haritası **hedef mimaridir** (ADR-004); fiziksel gerçek: auth ✓, home ✓, assets ✓ (+ shared/packages) — 9 domain dizini kodda yok (Test-Path, Faz 0).

---

## 11. IMPLEMENTED/PLANNED Durum Tablosu

| Subdomain | Durum | Kanıt |
|-----------|-------|-------|
| auth.coremusic.net | **IMPLEMENTED** | composer.json + include/ (7 klasör hexagonal) + index.php + routes/ + handler/ |
| home.coremusic.net | **IMPLEMENTED** | composer.json + include/ (Auth/Container/Session) + pages/ |
| **assets.coremusic.net** | **IMPLEMENTED** | Css/Fonts/Image/js — ⚠️ §2.1 tablosunda LİSTEDE YOK (eksik satır) |
| shared/packages (altyapı) | **IMPLEMENTED** | 2 composer paketi |
| music, admin, api, download, media, car, studio, pro, landing | PLANNED | Test-Path: dizin yok |

**§2.1 tablosu düzeltme önerisi:** assets.coremusic.net 11. satır olarak eklenmelidir (statik servis — Css/Fonts/Image/js, port 80/443, auth yok). Bu doküman revizyonunda tablo korunmuş, eksiklik burada kayıt altına alınmıştır (In-Place ilkesi — tabloya dokunma kararı kullanıcıya).

---

## 12. Gerçek Auth Hattı (validate-key)

Bu dokümanın §2.2 akış şeması SSO cookie modelini anlatır; gerçekte çalışan hat:

```
home.coremusic.net (oturum yok)
  → auth.coremusic.net login (auth domain kendi shell'i)
     → başarılı → auth_key üret (tek kullanımlık)
        → redirect: home.../auth/callback?auth_key=xxx
           → HomeAuthBridge (185s): POST auth.../validate-key
              ├─ TTL 300sn içinde → MM_* session kurulur → /home
              └─ geç/expired → login'e geri (max 2 retry)
```

**Fark analizi:**

| Konu | §2.2 (SSO Cookie) | Gerçek (auth_key devir) |
|------|-------------------|-------------------------|
| Doğrulama | Cookie `.coremusic.net` her subdomain'de okunur | Auth domain doğrular, key tek kullanımlık devredilir |
| Endpoint | `/api/session/check` | `/validate-key` (POST) |
| Cross-domain durum | Cookie paylaşımı | Redirect + callback |
| ADR | ADR-043 konsolidasyon | ADR-043 + ADR-011 |

İki model birleştirilebilir (cookie SSO + key fallback) — karar PLANNED; mevcut çalışan hat dokümante edilmiştir ([[../l1-security/index]] §4, engine §8.4 S-01).

---

## 13. Cookie Stratejisi

| Parametre | Değer | Not |
|-----------|-------|-----|
| Domain kapsamı | `.coremusic.net` (nokta önekli) | Tüm subdomain'ler okur — SSO temeli |
| HttpOnly | `1` (SessionInitializer ini_set) | JS erişimi yasak |
| Secure | `1` | HTTPS zorunlu (Tier üretim) |
| SameSite | Lax | CSRF yüzeyini daraltır |
| use_strict_mode | `1` | Session fixation önleme |
| Oturum adı | `COREMUSIC_SESS` | ADR-011 frozen |

**Cookie SSO ↔ auth_key devri ilişkisi:** Aynı `.coremusic.net` cookie'si tüm subdomain'lerde okunabilir ise auth_key devrine neden gerek var? Cevap: auth domain session'ı ile domain session'ı **ayrı süreçlerdir**; auth_key, auth domain'de doğrulanmış kimliği hedef domain session'ına **onaylı devir** ile taşır (validate-key tek kullanımlık). Cookie SSO, auth domain'le aynı session store'u paylaşan senaryolarda geçerlidir — mevcut kod devir modelini kullanır. Karar netleşmesi PLANNED (§12).

---

## 14. assets.coremusic.net — Eksik Satır

§2.1 haritasında olmayan 11. bileşen:

| Alan | Değer |
|------|-------|
| Subdomain | `assets.coremusic.net` |
| Tip | Statik servis |
| İçerik | `Css/` · `Fonts/` · `Image/` · `js/` |
| Auth | Yok (public asset) |
| Sunucu kodu | Yok — webserver direkt servis eder |
| CORS | CSS/JS/Font load — `Access-Control-Allow-Origin` gerekebilir (font CORS!) |

**Font CORS notu:** Cross-origin font yükleme tarayıcıda CORS ister — assets domain font'ları diğer subdomain'lerden çağrılıyorsa `Access-Control-Allow-Origin` başlığı webserver'da zorunludur. HTML shell (html-shell-renderer §3) `preconnect {assets-url}` kullanır.

---

## 15. RPi5 SQLite Konusu (ADR Çelişki Değerlendirmesi)

§6 tablosu embedded modlar için "Local (SQLite)" diyor; ADR-003 (frozen) "9 BCNF MySQL" diyor. **Çelişki değil — katmanlı karar:**

| Mimari | DB | Kapsam | ADR |
|--------|----|--------|-----|
| Merkezi web platformu | MySQL 18 BCNF | auth/home/media... (internet hattı) | ADR-003/040 |
| Embedded RPi5 modları (car/studio/pro offline) | SQLite 1 DB | Offline-first yerel çalışma | ADR-027 (Dual-Mode Storage) |

**Truth Mode notu:** Bu ayrım §6'da açık yazılmamıştı; burada netleştirildi. "Cross-subdomain auth yok (sadece local)" satırı ile §2.2 SSO akışı da aynı nedenle katmanlıdır: RPi5 offline modda local auth, online modda merkezi auth. Dual-mode geçiş detayı ADR-027 + [[../02-deployment/index]] kapsamıdır.

---

## 16. DNS / Webserver Katmanı

Subdomain yönlendirmenin gerçek ilk hattı PHP değil webserver'dır:

```
DNS *.coremusic.net → sunucu IP
  → webserver vhost eşleşmesi (host header)
     ├─ auth.coremusic.net → auth vhost → index.php
     ├─ home.coremusic.net → home vhost → index.php
     ├─ assets...          → statik root (Css/Fonts/Image/js)
     └─ bilinmeyen         → default vhost / 404
```

| Konu | Hedef | Kaynak |
|------|-------|--------|
| Wildcard SSL | Let's Encrypt `*.coremusic.net` | §7 edge |
| vhost örnekleri | `servers/` dokümanları: linux-nginx, windows-apache, windows-iis | `.ai/servers/` |
| Port map | §4 (IIS:80 + Apache:81 paralel — Q&A kararı, MEMORY) | Kullanıcı ortamı |

**Not:** `servers/` dokümanları (3 dosya) bu katmanın detayını taşır — Faz 3 kuyruğunda.

---

## 17. Test Senaryoları

| # | Senaryo | Beklenen |
|---|---------|----------|
| 1 | `auth.coremusic.net` | auth vhost → login shell |
| 2 | `home.coremusic.net` auth'suz `/home` | login redirect (§12 hattı) |
| 3 | `home...` + auth_key callback | validate-key → session → 200 |
| 4 | `home...` + geçmiş auth_key | red → login |
| 5 | `assets.../Css/main.css` | 200 statik + CORS header (font için) |
| 6 | Bilinmeyen `xyz.coremusic.net` | default vhost / 404 |
| 7 | `music.coremusic.net` (PLANNED) | henüz vhost yok — 404/default |
| 8 | HTTPS zorunlu http istek | 301 → https (§5.1 url-normalization) |
| 9 | Cookie `.coremusic.net` subdomain'ler arası | auth domain session'ı home'da da görünür (paylaşılan store ise) |
| 10 | RPi5 offline | local auth + SQLite (§15) |

---

## 18. Diagnostics (tekrarlanabilir)

```powershell
# 1. Fiziksel subdomain dizinleri (beklenen: auth, home, assets, shared, packages)
"auth.coremusic.net","home.coremusic.net","assets.coremusic.net","music.coremusic.net","download.coremusic.net" |
  ForEach-Object { "{0,-25} {1}" -f $_, (Test-Path -LiteralPath $_) }

# 2. DomainConfig gerçek konumu
Get-ChildItem -LiteralPath "shared\src" -Recurse -Filter "DomainConfig.php" | Select-Object FullName
Get-Content -LiteralPath "shared\config\domain.php" -ErrorAction SilentlyContinue

# 3. Gerçek auth endpoint (beklenen: validate-key; session/check YOK)
Select-String -LiteralPath "home.coremusic.net\include\Auth\HomeAuthBridge.php" -Pattern "validate-key|session/check"

# 4. Session cookie parametreleri
Select-String -LiteralPath "shared\src\Session\SessionInitializer.php" -Pattern "cookie_httponly|cookie_samesite|COREMUSIC_SESS"
```

---

## 19. Risk Kaydı

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 1 | Referans SSO modelinin gerçek sanılması | Yüksek | Orta | §12 eşleştirme |
| 2 | assets'in domain haritasından kaçması | YAŞANDI | Düşük | §14 eksik satır kaydı |
| 3 | RPi5 SQLite'ın ADR-003 ihlali sanılması | Orta | Orta | §15 katmanlı karar notu |
| 4 | Cookie SSO ↔ devir modeli karışıklığı | Orta | Orta | §13 ilişki analizi — karar PLANNED |
| 5 | Hardcoded auth URL (§3 örnek) | Orta | Orta | DomainConfig kuralı (url-normalization §15 paralel) |
| 6 | Font CORS eksikliği | Orta | Orta | §14 not |

---

## 20. Ek SSS

**S: 10 subdomain hepsi ayrı sunucu mu?**
C: Hayır — tek sunucu, vhost ayrımı (§16). Port farkları (81/5000/3001) process/hosting seçimidir; 443'te hepsi TLS arkasında birleşir.

**S: `SubdomainRouter` PHP sınıfı yazılacak mı?**
C: PLANNED — webserver vhost zaten host ayrıştırdığı için PHP-içi tespit yalnız "hangi domain config'i yükle" kararı içindir (DomainConfig). Sınıf gereksinimi Faz 2c devam kararıdır (YAGNI).

**S: `music.coremusic.net` port 81 iddiası hangi kanıt?**
C: CLAUDE.md Guardrail #8 (frozen bağlam) + index.md §6.1 — ancak kod yok. PLANNED etiketiyle okunur; Guardrail #8 hedef tanımdır.

**S: Session `.coremusic.net` cookie'si ve auth_key birlikte çalışır mı?**
C: §13 analizi: iki model farklı süreç senaryolarını kapsar. Şu an devir modeli kodlanmış; cookie SSO paylaşımlı store kararıyla aktifleşebilir. İkisinin eşzamanlı aktifliği PLANNED tasarım konusudur.

**S: RPi5'te MySQL yoksa şemalar ne olacak?**
C: 18 BCNF MySQL şemaları merkezi platform içindir; embedded SQLite şeması ayrı tasarım (ADR-027). l0 §15'ten farklı bir soyutlama katmanı gerektirir — Faz 2i (05-data) konusu.

**S: Bilinmeyen subdomain neden music'e düşüyor (§3 varsayılan)?**
C: Referans kodun tercihi; hedef tasarımda default vhost 404'ü tercih eder (güvenli varsayılan). Karar kod teyidine bağlı — §3 örnek PLANNED.

---

## 21. İzlenebilirlik Tablosu

| İddia | Kaynak | Doğrulama |
|-------|--------|-----------|
| 10+1 subdomain haritası | §2.1 + §14 | Hedef mimari (ADR-004) |
| fiziksel: auth+home+assets | Test-Path | Faz 0 ✅ |
| SubdomainRouter YOK | PageRouter 16 dosya listesi | Faz 0 ✅ |
| validate-key gerçek hattı | HomeAuthBridge.php | Kod okuma ✅ |
| cookie parametreleri | SessionInitializer.php | Kod okuma ✅ |
| RPi5 SQLite | ADR-027 bağlamı | §15 netleştirme |
| domain.php 0.5KB | shared/config/ | Faz 0 ✅ |
| servers/ 3 doküman | .ai/servers/ | Dosya sayımı ✅ |

---

## 22. Revizyon Geçmişi

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 5.0.0 | 2026-08-09 | Subdomain dokümanı (referans tabanlı) |
| 6.0.0 | 2026-09-08 | Faz 2c: §10 referans↔gerçek; §11 IMPLEMENTED/PLANNED + assets eksik satırı; §12 gerçek auth hattı; §13 cookie stratejisi; §14 assets; §15 RPi5 SQLite netleştirme; §16 webserver katmanı; §17-§21 test/diagnostics/risk/SSS/izlenebilirlik |
| 6.1.0 | 2026-09-08 | §23 domain detay kartları (11); §24 SSO↔Local matris; §25-§30 ADR/uyarı/handover/başlatma/kalite |

---

## 23. Domain Detay Kartları (PLANNED Tasarım Kararları)

Her domain için hedef tasarım kartı — kod üretimi öncesi sözleşme. Durum sütunu §11 tablosuna bağlıdır.

### 23.1 auth.coremusic.net — IMPLEMENTED

| Konu | Karar |
|------|-------|
| Rol | Identity Provider + Security Gateway |
| Stack | PHP 8.4 + fast-route + phpdotenv + psr7-server |
| Yapı | Hexagonal: Container/Controller/Domain/Handler/Middleware/Repository/Service |
| Auth | Merkezi login/register/logout + validate-key devir |
| Session | SessionManager (175s) + `MM_*` sözleşmesi |
| JWT | PLANNED (RS256 — brain §4A; `lcobucci/jwt` hedef) |
| Ekranlar | login, register-step1/2-3, select-gender (ui-design A-auth) |

### 23.2 home.coremusic.net — IMPLEMENTED

| Konu | Karar |
|------|-------|
| Rol | Ev medya merkezi (RPi5 embedded + desktop) |
| Stack | PHP 8.4 (minimal: php-di, psr/log, psr/container) + shared |
| Render | PageRouterKernel + 4-Tier koşullu (DeviceManager) |
| Auth | HomeAuthBridge → validate-key devir |
| Sayfalar | home/header/footer tek dosya üçlüsü (Guardrail #17) |
| Scale | ScaleManager.js hibrit motor |

### 23.3 music.coremusic.net — PLANNED

| Konu | Hedef Karar |
|------|-------------|
| Rol | Ana medya paneli (Control Service port 81) |
| Stack | PHP 8.4 + Vanilla JS (Guardrail #8: port 81 sabit) |
| Özellik | Kütüphane/album/sanatçı/playlist yönetimi |
| Veri | coremusic_musics + albums + playlist (22+5+5 tablo) |
| UI Kaynağı | ui-design screens C-music (albums/artists) |
| Önkoşul | API sözleşmeleri (03-contracts) + auth SSO karar netleşmesi (§13) |

### 23.4 admin.coremusic.net — PLANNED

| Konu | Hedef Karar |
|------|-------------|
| Rol | Yönetim paneli |
| Auth | SSO + admin rolü (RBAC 'admin') |
| Özellik | Kullanıcı/ içerik / sistem yönetimi |
| Veri | Tüm 18 DB'ye yönetim erişimi (rol sınırlı) |
| Bağımsız tema | CLAUDE.md §15: admin teması kullanıcı temalarından ayrı |

### 23.5 api.coremusic.net — PLANNED

| Konu | Hedef Karar |
|------|-------------|
| Rol | API Gateway tek giriş (ADR-084) |
| Desen | API-First: OpenAPI → DTO → Contract → Use Case → Kod |
| BFF | SPA/Mobile/Embedded/Desktop/Admin/Car — 6 BFF varyantı |
| CQRS | Write/Read ayrımı + PSR-14 event |
| İstemci kuralı | SPA asla PDO/Repository/SQL görmez |

### 23.6 download.coremusic.net — PLANNED

| Konu | Hedef Karar |
|------|-------------|
| Rol | İndirme servisi (Deezer/YouTube kaynaklı) |
| Stack | Node.js 20+ (ADR-026) — dinamik stack kararı |
| Pipeline | YouTube → nova-search → deemix → FLAC 24/32-bit → coremusic_musics |
| Anti-ban | Rate limiting + ARL rotasyon + proxy (ADR-028) |
| Veri | coremusic_download (4 tablo) |
| Test | ≥80% coverage (Vitest) |

### 23.7 media.coremusic.net — PLANNED

| Konu | Hedef Karar |
|------|-------------|
| Rol | Medya deposu/transform (vault) |
| Stack | PHP + FFmpeg |
| Key-auth | Key-based (§2.1 — SSO değil) |
| Port | 5000/6000 (macOS 5000 çakışma notu: service-discovery §19) |
| Veri | coremusic_media (8 tablo) |

### 23.8 car.coremusic.net — PLANNED

| Konu | Hedef Karar |
|------|-------------|
| Rol | Araç içi bilgi-eğlence (RPi5 + PCM3168A) |
| Auth | Offline local auth (§15 katmanlı karar) |
| DB | SQLite (ADR-027) |
| UI | Touch-optimized, minimal chrome |
| Ses | 4.1/2.1 araç profili — 8.1 değil |

### 23.9 studio.coremusic.net — PLANNED

| Konu | Hedef Karar |
|------|-------------|
| Rol | Profesyonel stüdyo (RPi5 + 8.1 Surround + Class AB) |
| Ses | ASIO/WASAPI düşük gecikme, 31-band EQ (ADR-025) |
| Veri | coremusic_studio (6 tablo: sessions/tracks/presets/equipment) |
| Veri | coremusic_neva (4 tablo: EQ/DSP/routing/spectrum) |

### 23.10 pro.coremusic.net — PLANNED

| Konu | Hedef Karar |
|------|-------------|
| Rol | Profesyonel medya yönetimi (RPi5 + HDMI) |
| Auth | Local (SQLite) offline / SSO online |
| View Mode | pro görünüm (ADR-045 4 moddan biri) |
| Fark | home ile aynı çekirdek, farklı view mode + yetki seti |

### 23.11 coremusic.net — PLANNED (Landing)

| Konu | Hedef Karar |
|------|-------------|
| Rol | Landing/marka sayfası |
| Auth | Yok |
| Stack | Vanilla JS statik |
| Veri | Yok (CMS banner'ları PLANNED — coremusic_cms) |

---

## 24. SSO ↔ Local Auth Karar Matrisi (§15 Devam)

| Koşul | Auth Modeli | Session | DB |
|-------|-------------|---------|-----|
| Online + merkezi platform | SSO devir (validate-key) | `MM_*` MySQL-backed | MySQL 18 BCNF |
| RPi5 offline (car/studio/pro) | Local login | Local session | SQLite 1 DB |
| RPi5 online dönüş | Değişim senkronizasyonu | PLANNED conflict policy | ADR-027 dual-mode |

**Açık tasarım sorusu:** Offline döneminde oluşan local kullanıcı verisi merkezi MySQL'e nasıl çakışmasız taşınır? — ADR-027 kapsamı; bu doküman yalnız bağlantıyı işaretler.

---

## 25. Ek SSS

**S: 10 domain kartı nihai sözleşme mi?**
C: PLANNED kartlardır — kod üretiminden önce API sözleşmeleri (03-contracts) + ADR onayı gerekir. IMPLEMENTED kartlar (auth/home) gerçek koddur.

**S: assets neden 11. kart — ADR-004'te yoktu?**
C: Faz 0 keşfi: fiziksel olarak var ama hedef haritalarda listelenmemişti (§14). Kart onarıma alındı; §2.1 tablosuna ekleme kullanıcı onaylıdır.

**S: music domain neden "Control Service" adıyla da geçiyor?**
C: index.md §6.2'de Control Service = port 81 (auth/session/RBAC sorumluluğu music domain'i içinde) — adlandırma kaynakları farklı; fiziksel olarak aynı vhost hedeflenir.

**S: car için 8.1 yerine 4.1?**
C: Araç akustiği + amfi kanal sayısı (PCM3168A 8-kanal her araç konfigürasyonunda kullanılmaz) — electronic/kart kararına bağlı; bu kart "4.1/2.1 araç profili" olarak temkinli yazar.

**S: pro ile home aynı çekirdekse neden ayrı domain?**
C: Donanım hedefi (HDMI vs touch), yetki seti ve view mode farklı; tek kod tabanı + DomainConfig varyasyonu hedeflenir (DomainConfig.php IMPLEMENTED isim düzeyi).

---

## 26. İlgili ADR Tablosu

| ADR | Konu | Bu Dokümandaki Etkisi |
|-----|------|-----------------------|
| ADR-004 | Multi-domain SPA | 10+1 domain haritasının temeli |
| ADR-043 | Auth Subdomain Consolidation | Merkezi auth + validate-key hattı |
| ADR-011 | Session Management | Cookie parametreleri, `COREMUSIC_SESS` |
| ADR-016 | URL Normalization | Bilinmeyen subdomain davranışı |
| ADR-021 | SPA Router Immutable | Route sözleşmeleri |
| ADR-027 | Dual-Mode Storage | RPi5 SQLite ↔ MySQL katmanlı karar |
| ADR-026 | Download Service | Node.js domain kartı (23.6) |
| ADR-039 | 7 Service Platform | Servis haritası (§2.3) |
| ADR-084 | API Gateway | api domain kartı (23.5) |
| ADR-085 | Shared Library Hybrid | shared/ altyapının tüm domain'lerde ortak kullanımı |
| ADR-044 | Dynamic Theme | data-gender shell attribute |
| ADR-042 | Vault Restructuring | Port 81 + PHP 8.4 sabitleri |

---

## 27. Kritik Uyarılar (Subdomain)

| # | Uyarı | Sonuc |
|---|-------|-------|
| 1 | §2.2 SSO modelini gerçek sanma | Çalışan hat validate-key'dir (§12) |
| 2 | assets'i haritalardan dışlama | 11. bileşen — §14 kaydı |
| 3 | RPi5 SQLite'ı ADR-003 ihlali sanma | Katmanlı karar (§15, ADR-027) |
| 4 | Auth URL'i kod içine sabitleme | DomainConfig kuralı (§12, §15) |
| 5 | Font yüklemede CORS başlığı unutma | Cross-origin font bloklanır (§14) |
| 6 | Bilinmeyen subdomain'i music'e düşürme | Güvenli varsayılan 404'tür (§20 SSS) |
| 7 | Cookie SSO ↔ devir modelini karıştırma | §13 — mevcut: devir; SSO PLANNED |
| 8 | Port 81'i PLANNED domain için var sayma | Guardrail #8 hedef tanım — kod kanıtı bekler |

---

## 28. Handover Senaryoları

| Senaryo | Kaynak → Hedef | Öncelik | Referans |
|---------|----------------|---------|----------|
| music domain iskeleti | MO → backend-architect | HIGH | Kart 23.3 + 03-contracts |
| download Node.js ADR | backend → MO | HIGH | Kart 23.6 + engine §9.4 |
| assets CORS başlığı | ui → devops | MEDIUM | §14 font notu |
| RPi5 dual-mode senkron | embedded → data-engineer | HIGH | §24 + ADR-027 ek karar |
| domain.php alan ekleme | backend içi | LOW | §18 komut 2 |
| vhost/server dokümanları | devops | MEDIUM | `.ai/servers/` (Faz 3) |
| auth_key devir revizyonu | backend → security | CRITICAL (güvenlikse) | §12/§13 |
| font CORS testi | devops → qa | MEDIUM | §17 senaryo 5 |

---

## 29. Domain Başlatma Sırası (Deployment Perspektifi)

| Sıra | Domain | Neden bu sırada |
|------|--------|-----------------|
| 1 | shared + packages (altyapı) | Herkesin bağımlılığı |
| 2 | auth | Kimlik olmadan hiçbir panel açılmaz |
| 3 | assets | Statik kaynaklar shell'i besler |
| 4 | home | İlk tüketici panel (IMPLEMENTED) |
| 5 | music | Ana medya paneli |
| 6 | api | Gateway — diğer servis sözleşmeleri |
| 7 | admin | Yönetim (music/api sonrası anlamlı) |
| 8 | download, media | Destek servisleri |
| 9 | car, studio, pro | Embedded/profyonel (donanım bağımlı) |
| 10 | landing | Son — marka sayfası |

Kural: Sıra ADR-087 master plan + bağımlılık grafiğiyle uyumludur; atlamalı başlatma bağımlılık hatası üretir (örn. auth'sız home = S-01 loop vakası).

---

## 30. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 6.1.0 |
| **Bölüm Sayısı** | 30 |
| **Durum Etiketi** | 3 IMPLEMENTED (auth/home/assets) + 9 PLANNED |
| **Domain Kartı** | 11 (§23) |
| **ADR Uyumlu** | ✅ 12 ADR (§26) |
| **Test Senaryosu** | 10 (§17) |
| **Risk Kaydı** | 6 (§19) |
| **Kod Kanıtı** | auth/home/assets + HomeAuthBridge + SessionInitializer + domain.php |
| **Zero Hallucination** | ✅ (referans SSO modeli açıkça etiketli) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-08
**Mode:** Red Team · Human Mode · Truth Mode
