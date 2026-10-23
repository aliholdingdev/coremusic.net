---
title: "CoreMusic — admin.coremusic.net Klasör Kuralları"
type: docs
category: admin
docType: claude
date: 2026-10-23
updated: 2026-10-23
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/ + kök AGENTS.md"
---

# admin.coremusic.net — CLAUDE.md

**docType:** claude · **Klasör:** `admin.coremusic.net/` · **Sorumlu:** Security + Backend

**Zorunlu Bağlantılar:** [[CONTEXT.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[PRD.md]] · [[../AGENTS.md]]

---

## 1. Amaç

Bu dosya `admin.coremusic.net/` klasöründe çalışırken uyulacak kuralları ve **her kuralın neden var olduğunu** tanımlar. Yönetim paneli auth sözleşmesine (ADR-043) ve shared route yasağına dayanır; kural ihlali doğrudan güvenlik açığına (yetkisiz panel erişimi, CSRF, sızıntı) dönüşür.

| Karar | Kaynak (disk) |
|-------|---------------|
| Vanilla PHP 8.4 + `strict_types` | `composer.json:16` + tüm dosya başlıkları |
| Auth formları auth subdomain'de (ADR-043) | `AuthAliasRedirector.php:29-34` |
| Session fixation savunması | `AdminAuthBridge.php:83` (`session_regenerate_id(true)`) |
| Çıktı escaping tek sarmalayıcı | `pages/layout.php:27` (`$h` — ENT_QUOTES\|ENT_SUBSTITUTE, UTF-8) |
| Erişim kapatma + başlıklar | `.htaccess:9-16,18-36`, `web.config:5-45` |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `index.php`, `autoload.php`, `config/`, `include/`, `pages/`, `tests/` değişiklik kuralları | `../shared/src/` kendi kuralları → [[../shared/CLAUDE.md]] |
| Auth bridge + CSRF + session kuralları | `../auth.coremusic.net/` formları/ kodu (SSOT orası) |
| Bu klasörde yazma/yasak listesi | Frontend CSS/JS (`../assets.coremusic.net/`), CI/CD (DevOps) |

- **Kullananlar:** Backend Architect, Security Engineer, QA Engineer, DevOps Engineer.
- **Ön koşul:** Klasör `CONTEXT.md`'si okunmuş; her kuralın diske dayanan kaynağı tabloda.

---

## 3. Mimari

### 3.1 Yüzey Envanteri (kuralların koruduğu dosyalar — disk ölçümü 2026-10-23)

| Dosya/Katman | Ne için kullanılır | Neyden oluşur | Neden var | Ne zaman düzenlenir | eli10 | eli15 |
|--------------|--------------------|---------------|-----------|---------------------|-------|-------|
| `index.php` | Boot girişi | autoload → constants → RuntimeBootstrap → bootstrap | Tek kapı | Boot sırası ADR ile değişince | Uygulamanın ön kapısı. | Tek dosyadır ki boot sırası tek yerde okunsun; sıralama kırılırsa hiç sınıf yüklenmez. Rutin özellik eklemesi buraya değil `pages/`/`include/`'a gider. |
| `config/bootstrap.php` | Kernel öncesi kapılar + PageRouterKernel | health, dev-login, alias, root redirect, auth callback | Kapılar tek yerde | Yeni kernel-öncesi rota gerektiğinde | Motorun ilk çalıştığı masa. | Ayrıldı çünkü kernel öncesi davranış (auth callback dâhil) route tablosundan farklı hızda değişir. `/dev-login` satırları yalnız DEBUG'da anlam taşır — production koşulu Security teyidindedir. |
| `config/routes.php` | Admin-local SPA route tablosu | SpaRoute dizisi, requiresAuth işaretleri | Adres tek kaynakta | Yeni sayfa eklenince | Panelin adres defteri. | `shared/config/routes.php` DEĞİŞTİRİLMEZ (dosya yorumu:8); yalnızca bu klasörün local rotaları burada durur. Çifte kaynak oluşursa iki gerçek davranış doğar. |
| `include/Auth/AdminAuthBridge.php` | auth_key doğrulama + session kurma | cURL POST, retry/timeout, regenerate | Auth kapısı tek sınıfta | Auth akışı ADR ile değişince | Kimlik köprüsünün tek bekçisi. | Ayrıldı çünkü doğrulama (harici validate-key) ile oturum kurma aynı gövdede test edilebilir kalmalı; parçalanırsa fixation savunması unutulabilir. Yalnız Security onayıyla değişir. |
| `include/Routing/*.php` | I/O-sız yönlendiriciler | static, saf fonksiyonlar | Test edilebilir yönlendirme | Alias/route değişince | Kapıların ok tabelaları. | I/O-sız yazıldı ki PHPUnit Redirect testleri ağ olmadan koşsun. İş mantığı taşırsa test ağı ister ve körleşir. |
| `pages/layout.php` + `admin-*.php` | Sunucu taraflı sayfa üretimi | `$content` + layout require + `$h` escaping | Sayfalar tek şablonda | Yeni sayfa eklenince | Panelin vitrini. | Her sayfa kendi `$content`'ini üretip layout'a teslim eder; escaping tek yerde (`$h`) olunca XSS yüzeyi daralır. Ham `echo` + kullanıcı verisi birleşmez. |
| `tests/Unit/*` (6 test) | Sözleşme testleri | Bridge, redirect, markup, route, viewmode | Sözleşme test ile kilitli | Kod değişince | Panelin soru kâğıtları. | Test yalnız `include/`'ı kapsar (`phpunit.xml`); markup testi auth formlarının sözleşmesini de korur (ADR-058). Test kalkarsa sessiz kırılma fark edilmez. |

### 3.2 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur |
|---|---------|----------|---------------|
| 1 | Bakım kolaylığı | Diff küçük | Her şey değişir, inceleme kör olur |
| 2 | Tek sorumluluk | Dosyanın tek sahibi | Kimin ne işi belli değil |
| 3 | Auth/form ayrımı (ADR-043) | Form auth'da, panel burada | Çifte auth akışı |
| 4 | Test/kod ayrımı | Test yalnız `include/` | Test kodu taklit eder |
| 5 | Vendor karantinası | `vendor/` junction | Güncelleme kodu bozar |

> **eli10 (basit):** Kutular ayrı ki birini değiştirirken diğeri bozulmasın; auth ile panel zaten farklı klasörlerde.
> **eli15 (detay):** Route, kod, sayfa, test ayrı dosyalardır; çünkü adres en sık değişir, kod daha seyrek, test en son. Ayrı olunca adres eklerken test ve kod değişmez, inceleme küçük kalır.

---

## 4. Kurallar (her biri: neden var → kaynak)

| # | Kural | Korunan yüzey | Neden var (gerekçe) | Kaynak (disk) |
|---|-------|---------------|---------------------|---------------|
| 1 | **`strict_types=1` her PHP dosyasında** | Tip güvenliği | Session/pepper işlemi tip kaymasında sessizce yanlış sonuç üretir | Tüm `.php` başlıkları |
| 2 | **Framework/ORM YOK; raw PDO yalnız shared üzerinden** | Bağımlılık + injection yüzeyi | ADR-002/003; panel sorgusu dâhil | `bootstrap.php:9` "no ORM" yorumu |
| 3 | **`shared/config/routes.php` DEĞİŞTİRİLMEZ** | Ortak route tek kaynak | Panel kendi local tablosunu kullanır | `config/routes.php:8` |
| 4 | **Auth formları bu klasöre taşınmaz** (ADR-043) | Tek kimlik akışı (PRD-PLT-004) | Formlar `auth.coremusic.net/admin/`'dedir | ADR-043 + `AuthAliasRedirector.php:29-34` |
| 5 | **CSRF token her state-changing formda zorunlu** | CSRF yüzeyi | `$_SESSION['csrf_token']` gizli input deseni | `admin-settings.php:14,20`, `admin-song-add.php:15,32`, `admin-playlist.php:15` |
| 6 | **Tüm çıktı `$h` (htmlspecialchars ENT_QUOTES\|ENT_SUBSTITUTE, UTF-8) ile geçer** | XSS yüzeyi | Tek sarmalayıcı = tek savunma hattı | `pages/layout.php:27` |
| 7 | **`/dev-login` yalnız `DEBUG_MODE`** | Yetki | Debug backdoor production'a sızarsa panel açık demektir | `bootstrap.php:91-105` |
| 8 | **`AdminAuthBridge` yalnız Security onayıyla değişir** | Auth doğrulama + session fixation savunması | validate-key retry/timeout + `session_regenerate_id(true)` | `AdminAuthBridge.php:26-28,83` |
| 9 | **Secret yalnız `.env`** (`.env.example` placeholder) | Anahtar sızıntısı | `APP_PEPPER` gibi sabitler vault'a/md'ye yazılmaz | `config/.env.example` (CHANGE_ME) |
| 10 | **Erişim kapatma korunur**: `config|include|pages`, non-index `.php`, `.env/.md/.log/...` → 403 | Bilgi sızıntısı | Sunucu kuralı uygulama kodundan ayrı güvenlik katmanıdır | `.htaccess:18-36`, `web.config:20-31` |
| 11 | **Yeni PHP dosyası `php-template.md`'den türetilir** (Guardrail #16) | Vault standardı | Tutarlı iskelet | `.ai/.templates/backend/php-template.md` |
| 12 | **Commit subagent ATMAZ** | Yetki | Tarih/entegrasyon orkestratörde | Kök `AGENTS.md` §7/§8 |

> **eli10 (basit):** On iki kural: sıkı tip, framework yok, ortak rota dokunulmaz, auth auth'da, token zorunlu, çıkış temiz, debug kapalı, sır yazılmaz, kapılar kapalı, şablon zorunlu, commit başkasında.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü her biri farklı bir kaybı (açık panel, sızıntı, çifte akış, kırık sözleşme) önler. Okunması, işe başlamadan hattı gösterir. Kural kalkarsa panel güvenliği formaliteye döner.

---

## 5. Workflow (özet — tam akış [[WORKFLOW.md]])

```text
GÖREV → CONTEXT+CLAUDE oku → hedef dosyayı 1. kez oku → DEĞİŞ (pages/include/config)
  → auth/CSRF yüzeyi ise SECURITY handover → phpunit yeşil → RAPOR → COMMIT (MO)
```

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 alan + `docType: claude` |
| 2 | Bölüm sırası | §1–§6 |
| 3 | Kural kaynakları | Her kuralın disk kanıtı tabloda |
| 4 | Placeholder | `{{` kalmadı |
| 5 | Wiki-link | `[[...]]`, hedefler diskte var |
| 6 | Disk kanıtı | Envanter 2026-10-23 ölçümüyle uyumlu |
| 7 | Emoji | Yok |
| 8 | Dokunulmaz | Kod + kardeş dokümanlar değişmedi |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör context | [[CONTEXT.md]] | Envanter + auth akışı |
| Klasör rolleri | [[AGENTS.md]] | Routing |
| Klasör süreci | [[WORKFLOW.md]] | Adım/kapı |
| Klasör gereksinimleri | [[PRD.md]] | PRD-ADM-001..007 |
| Kök master kurallar | [[../AGENTS.md]] | §3 Zero-Hallucination |
| Vault anayasası | [[../.ai/CLAUDE.md]] | Hard Guardrails |
| Auth komşusu | [[../auth.coremusic.net/CLAUDE.md]] | ADR-043 yüzeyi |
| shared kuralları | [[../shared/CLAUDE.md]] | Ortak katman kuralları |
| PHP şablonu | `.ai/.templates/backend/php-template.md` | Guardrail #16 |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-23
