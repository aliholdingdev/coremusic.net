---
title: "Adım 11 — Dropdown Menu (DB Bağlantılı, Rol Bazlı) Raporu"
type: report
category: backend-rebuild
step: 11
version: 1.0.0
date: 2026-10-22
status: complete
gates: KAPI-E
---

# Adım 11 — Dropdown Menu · DB Bağlantılı (SON ADIM) · Rapor

> Prompt: `coremusic-backend-step11-dropdown.md` (KATMAN UI 2/2 · SON) · Master §4-§8 bağlayıcı
> **KAPI-E** (UI katmanı kapanışı): Header + dropdown JS fetch ile veri çekiyor · browser konsol hatası yok · mockup gate geçti

## §1 Menü Şeması + Rol Matrisi (§7 çıktı)

`GET /api/v1/user/header-data` → `data.menu`:

```json
[{ "id": "settings", "label": "Ayarlar", "route": "/ayarlar", "roles": ["user","admin"] },
 { "id": "history",  "label": "Geçmiş",  "route": "/history", "roles": ["user","admin"] },
 { "id": "admin",    "label": "Admin Panel", "route": "https://admin.coremusic.net/", "roles": ["admin"] }]
```

| Rol | Yanıttaki menü | Admin ogesi |
|---|---|---|
| anonim (null) | `[]` (hata değil) | — |
| `user` | 2 öğe (settings, history) | **YANIT GÖVDESİNDE YOK** |
| `admin` | 3 öğe | VAR |

- **Hard Rule 2 — sunucu filtresi** (`UserHeaderService::menuForRole`): yetkisiz öğe istemciye hiç gönderilir; client'ta "gizleme" yok. JS yalnız geleni basar: API'den gelmeyen öğe DOM'a hiç yazılmaz.
- Route'lar gerçek disk yüzeyinden: `/ayarlar`+`/history` = `header.php` dropdown (L99-L100) · `admin.coremusic.net` = PNG `admin-1920` + T17 admin spec'leri (prompt §3 "admin link'i rol koşul").
- ARIA/klavye zaten DOM'da (`role=menu`/`aria-expanded`/`tabindex` · Enter/Space toggle · Escape kapat · link click→kapat) — bu adım JS testleriyle kanıtlandı.

## §2 Değişen Dosyalar (§7.2 kanıt — 8 dosya)

| Dosya | Δ | Ne |
|---|---|---|
| `api.../Service/UserHeaderService.php` | +46 | `MENU_ITEMS` const + `menuForRole()` sunucu filtresi + iki yanıt dalına `menu` alanı |
| `api.../Service/IUserHeaderService.php` | +8/-3 | phpdoc şemasına `menu` alanı |
| `api.../tests/Unit/HistoryAndStreamTest.php` | +66 | +3 test (anonim `[]` · user'da admin YOK · admin 3+şema) |
| `assets.../js/header-manager.js` | +85 | `#renderMenu` (idempotent, textContent, SPA kuralları) · `#resetMenu` (§9) · fetch hata yollarına reset bağlandı |
| `assets.../Css/03_Layout/_header.css` | +30 | `.header-dropdown-menu` + **`a[hidden]{display:none}`** (a{display:flex} UA hidden'ı override eder — kritik fix) + truncate + touch 24px |
| `assets.../tests/components/header-manager.spec.js` | +~150 | +6 test |
| `.ai/reports/backend-step11-curl-pc.ps1` | yeni | sunucu-PC curl (dinamik rol matrisi) |
| `.ai/reports/backend-step11-dropdown.md` | yeni | bu rapor |

## §3 Hard Rules Karşılığı

| Kural | Durum |
|---|---|
| Vanilla ES6+ · `var`/`eval`/`innerHTML` yasak · strict | ✓ (`#private` metot · replaceChildren · node --check temiz) |
| **Rol bazlı menü: sunucu filtresi** | ✓ `menuForRole` — test: user rolünde yanıtta admin id **YOK** |
| **SPA: `data-route` + delegate — full-reload YASAK** | ✓ same-origin linkte `data-no-spa` YOK → mevcut `RouterEventManager` document-delegate `preventDefault` + `onNavigate` (kendi delegate'İMİZ YOK — çift tetikleme riski sıfır). Cross-origin admin → `data-no-spa`+`rel=external` (pushState cross-origin SecurityError koruması). Logout `data-no-spa` KORUNDU (PHP `/logout` oturum kapanışı — server-side zorunlu). |
| XSS: textContent · ikon sabit harita | ✓ testte payload img OLUŞTURMADI (ikon eklenmedi — öğe metni label) |
| Touch ≥24×24px · focus outline 3px (`--cm-*`) | ✓ `.header-dropdown-menu a{min-height:24px}` + mevcut dropdown `:focus-visible` mirası (WCAG) |
| Mockup gate | ✓ `flow/navigation/01-spa-routing.md` + `02-header-nav.md` + T17 header — yerleşim DEĞİŞMEDİ (dropdown içeriği kod-kanıtlı; playlist deseniyle aynı) |

## §4 Testler (§7.4 rubrik öncesi kanıt)

| Suite | Önce | Şimdi |
|---|---|---|
| `header-manager.spec.js` | 6 | **12/12** (+6: admin 3 link+statik gizli · user'da admin link DOM'dA YOK · SPA data-route/data-no-spa ayrımı · anonim `[]` yalnız logout · 500→eski menü gizlenir+statik geri · XSS) |
| JS tam suite | 184 | **190/190** |
| `HistoryAndStreamTest` | 14 | **17/17** |
| PHP api suite | 107 | **110/110** (368 assertion) |
| `php -l` (3 PHP dosyası) | — | **0 hata** |

## §5 Canlı Kanıt Planı — ⚠️ VERIFICATION REQUIRED (server PC)

`.ai/reports/backend-step11-curl-pc.ps1` (parse 0 · ASCII 0; `CM_TEST_EMAIL`/`CM_TEST_PASSWORD` gerekli — rol yanıtta okunur, hesap rolü sabitlenmez):

1. anonim → `menu []` · 2. login → 3. **rol matrisi dinamik**: user→2 / admin→3 + admin id var/yok · 4. route eşleşmesi (`/ayarlar`, `/history`, admin URL) · 5. şema alanları · 6. auth'siz 401.

Browser (KAPI-E — §5.4/§5.6 kabul):
- **2 sayfa geçişi** dropdown'dan: Ayarlar → Geçmiş; network'te XHR yalnız (tam sayfa reload YOK) + **konsol temiz**.
- **Rol A/B**: admin hesapta "Admin Panel" görünür, user hesapta görünmez.
- Klavye: Enter açar · Escape kapatır · oklarla menü gezilir (aria-expanded true/false).

## §6 Rubrik (§12.3 — master §7.4)

| Kriter | Puan | Gerekçe |
|---|---|---|
| Completeness | 14/15 | 6 adım tamam; canlı browser kanıtı server PC'de (⚠️) |
| Consistency | 10/10 | Adım 10 deseni (idempotent/fail-safe/BEM) birebir sürdürüldü |
| Production-Ready | 14/15 | 190+110 yeşil, php -l 0, curl script hazır — canlı yok |
| **Security** | **19/20** | sunucu rol filtresi (gövdede yetkisiz oge YOK) · XSS textContent · GET uç CSRF'siz (Adım 08) · fail-safe reset |
| Scalability | 9/10 | menü 1 satır const; rol matrisi data-driven |
| Clarity | 9/10 | rapor + kod yorumu |
| Depth (WCAG) | 9/10 | ARIA zemin+test; canlı klavye kanıtı bekliyor |
| Documentation | 9/10 | bu rapor + curl script |
| **Toplam** | **93/100** ✓ ≥85 · **Security 95** ✓ ≥90 | **GECTI** |

## §7 Kalan ⚠️ / UNKNOWN (gizlenmez)

1. ⚠️ Curl script canlı çalıştırma (server PC) — §5.
2. ⚠️ Browser KAPI-E: 2 sayfa geçişi XHR + konsol temiz + rol A/B + klavye.
3. ⚠️ `https://admin.coremusic.net/` kök URL'si — PNG/spec kanıtı var, canlı tıklama doğrulanmadı (rota sunucu yanıtında; tıklama cross-origin tam sayfa geçişi olur).
4. ⚠️ Test hesabının rolü bilinmiyor — script rolü yanıtta okur (user ise2, admin ise3 bekler).

## §8 KAPI-E + Kapanış (master §4/§7)

- **KAPI-E koşulu kod tarafı:** header+dropdown JS fetch veri çekiyor ✓ (Adım 10+11) · mockup gate ✓ (flow navigation + T17 header okundu, yerleşim değişmedi) · **browser konsol: ⚠️ server PC'de doğrulanacak** → kapı, §5 kanıtı geldiğinde tam kapanır.
- **Plan tamamlandı:** Adım 01→11 (DB 1-3 · API 4 · Backend 5 · Entegrasyon 6-9 · UI 10-11).
- **vault-sync önerisi:** `.ai/log.md` append (Adım 10+11 kaydı) · `.ai/reports/backend-step09..11` indekslendi · master §7 raporu bu dosyadır.
