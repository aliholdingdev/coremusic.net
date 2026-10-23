---
title: "CoreMusic — admin.coremusic.net Agent Talimatları"
type: docs
category: admin
docType: agents
date: 2026-10-23
updated: 2026-10-23
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/AGENTS.md + kök AGENTS.md"
---

# admin.coremusic.net — AGENTS.md

**docType:** agents · **Klasör:** `admin.coremusic.net/` · **Sorumlu:** MO (registry)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[CONTEXT.md]] · [[WORKFLOW.md]] · [[PRD.md]] · [[../AGENTS.md]]

---

## 1. Amaç

Bu doküman `admin.coremusic.net/` klasöründe **kimin hangi dosyaya dokunacağını** (routing) ve rol sınırlarını tanımlar. Yönetim paneli server-rendered yüzeydir; auth sözleşmesi (ADR-043) ve shared route yasağı burada en sık ihlal edilen sınırlardır.

| Karar | Kaynak (disk) |
|-------|---------------|
| Domain sınırları → dosya tipi eşlemesi | [[../.ai/AGENTS.md]] §5 Domain Boundaries |
| PHP yüzeyi → Backend Architect | [[../.ai/AGENTS.md]] §6 (PHP, routing keyword grubu) |
| Auth bridge/CSRF/session → Security Engineer | [[../.ai/AGENTS.md]] §6 (auth, session, CSRF keyword grubu) |
| Test dosyaları → QA Engineer | [[../.ai/AGENTS.md]] §5 (`tests/**/*.php`) |
| Rewrite/başlık → DevOps Engineer | `.htaccess` / `web.config` sunucu yüzeyi |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `admin.coremusic.net/` içi dosya → rol eşlemesi | `../auth.coremusic.net/` formları → [[../auth.coremusic.net/AGENTS.md]] (ADR-043 sahibi orası) |
| Yetki sınırları + handover tetikleyicileri | `../shared/src/` → [[../shared/AGENTS.md]] |
| Bu klasörde kime ne zaman başvurulacağı | `../assets.coremusic.net/` CSS/JS → [[../assets.coremusic.net/AGENTS.md]] |

- **Kullananlar:** MO (routing), atanan agent (yürütme).
- **Ön koşul:** [[CONTEXT.md]] (envanter) okunmuş; hedef dosya diskte mevcut.

---

## 3. Mimari

### 3.1 Rol Tablosu (bu klasörde)

| # | Rol / Agent | Bu klasördeki işi | Dokunabildiği dosyalar | Dokunamadığı | Ne zaman devreye girer | eli10 | eli15 |
|---|-------------|-------------------|------------------------|--------------|------------------------|-------|-------|
| 1 | **Backend Architect** | Panel sayfaları, route, bootstrap, redirector | `pages/**`, `include/Routing/**`, `config/routes.php`, `config/bootstrap.php`, `config/constants.php`, `autoload.php`, `index.php` | `include/Auth/AdminAuthBridge.php` (Security ortaklığı), `../shared/config/routes.php` (YASAK), auth formları | Yeni admin sayfası, rota, boot-öncesi kapı gerektiğinde | Panelin masa düzenini kuran usta. | Backend bu klasörün sahibidir çünkü route tablosu ve sayfa üretimi A2 katmanıdır. Yeni sayfa eklerken route + `pages/admin-*.php` + test üçlüsünü birlikte düşünür (ADR-058 testleri auth klasöründedir). `shared/config/routes.php` asla değiştirilemez (dosya yorumu — `routes.php:8`). Aşırı sınır (auth/CSRF) Security'e handover yapar. |
| 2 | **Security Engineer** | Auth bridge, CSRF, session, başlıklar, `/dev-login` denetimi | `include/Auth/AdminAuthBridge.php`, güvenlik satırları (`bootstrap.php` session/cookie), `.htaccess`/`web.config` başlık blokları | `pages/` iş içeriği, `config/routes.php` | Auth akışı, CSRF, session, debug kapısı değişince | Kimlik köprüsünün ve oturumun denetçisi. | Security bu yüzeye A1 sınırından girer; korunan şey auth_key doğrulama + session fixation savunması + debug backdoor koşuludur (`/dev-login` yalnız DEBUG — `bootstrap.php:91-105`). Değişiklik denetimi `log.md`'ye yazılır; onaysız auth satırı değişmez. |
| 3 | **QA Engineer** | Test yazımı/koruma | `tests/**`, `phpunit.xml` | Üretim kodu (`pages/`, `include/`, `config/`) | Kod değişti, kapsam açığı görüldü | Panelin soru kâğıtlarını yazan kişi. | QA yalnız test yüzeyine girer; üretim koduna dokunmaz çünkü test ile kod aynı elde değişirse test kodu taklit eder. `AdminMarkupContractTest` auth formlarının markup sözleşmesini korur (ADR-058) — bu test kırmızı ise kapı auth klasörüne handover ister. |
| 4 | **Data Engineer** | — (şema/shared üzerinden) | Bu klasörde dosyası yok; sorgu/şema ihtiyacı `shared/src/Database/` üzerinden | `admin/**` dosyaları | Yeni sorgu/şema değişikliği gerektiğinde | DB işi bu kapıdan değil, ortak kapıdan yürür. | Panel şu an tek sorgu kullanır (`SELECT COUNT(*) FROM admin_users` — `admin-dashboard.php:36`); yeni sorgu eklenirse Data Engineer `../shared` içinde çalışır. Sınır, `coremusic_auth` şemasına dağınık erişim doğmasını engeller. |
| 5 | **DevOps Engineer** | Sunucu/rewrite yapılandırması, vendor junction | `web.config`, `.htaccess`, `composer.json`/`composer.lock`, `.gitignore` | Uygulama kodu | Sunucu/deploy kuralı değişince; bağımlılık eklenince | IIS/Apache kapı yönlendirmesini ayarlayan kişi. | `web.config`/`.htaccess` sunucu yüzeyidir; erişim kapatma (config/include/pages → 403) ve güvenlik başlıkları buradadır. `vendor/` junction'dır (`../shared`) — elle oynanmaz, yalnız `composer install`. |
| 6 | **MO (Master Orchestrator)** | Routing + bu doküman güncellemesi | `CONTEXT/CLAUDE/AGENTS/WORKFLOW/PRD.md` | Üretim kodu | Görev dağıtımı, doküman senkronu | Kimin nereye gideceğini ayarlayan koordinatör. | MO bu dokümanların sahibidir; routing değişince (§3.2) tek o günceller. Kod katmanına girmez, kapıları (onay/rapor) işletir. Commit yetkisi de ondadır. |

### 3.2 Dosya → Birincil Rol Eşlemesi (hızlı routing)

| Dosya / Desen | Birincil rol | İkincil / denetim |
|---------------|--------------|-------------------|
| `pages/admin-*.php`, `pages/layout.php` | Backend Architect | QA (markup testi) · Security (escaping `$h` zorunlu) |
| `include/Routing/*.php` | Backend Architect | QA (redirect testleri) |
| `include/Auth/AdminAuthBridge.php` | Security Engineer | Backend Architect (entegrasyon) |
| `config/routes.php` | Backend Architect | MO (envanter) — `shared/config/routes.php` YASAK |
| `config/bootstrap.php`, `config/constants.php` | Backend Architect | Security (session/debug satırları) |
| `config/.env.example` | Security Engineer | DevOps |
| `tests/**`, `phpunit.xml` | QA Engineer | — |
| `web.config`, `.htaccess` | DevOps Engineer | Security (başlık/yasak blokları) |
| `composer.json` / `composer.lock` | DevOps Engineer | Backend Architect |
| `CONTEXT/CLAUDE/AGENTS/WORKFLOW/PRD.md` | MO (vault-updater) | — |

### 3.3 Handover Tetikleyicileri (bu klasörden çıkarken)

| Tetikleyici | Kaynak rol | Hedef rol | Öncelik |
|-------------|-----------|-----------|---------|
| Auth bridge/validate-key akışında güvenlik açığı | Backend | Security | CRITICAL |
| CSRF token akışında boşluk (formda token yok) | Backend | Security | CRITICAL |
| Yeni sayfa = yeni sorgu/şema ihtiyacı | Backend | Data (`../shared`) | HIGH |
| Markup sözleşmesi testi kırmızı (ADR-058) | QA | Security (auth formları) | HIGH |
| Rewrite/403 kuralı kırığı | Backend | DevOps | MEDIUM |
| `/dev-login` davranışı değişiklik isteği | Backend | Security | HIGH |

> Kaynak: [[../.ai/AGENTS.md]] §9.3 Handover Scenarios; bu tablo klasöre uyarlanmış hâlidir.

### 3.4 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur |
|---|---------|----------|---------------|
| 1 | Bakım kolaylığı | Diff küçük | Her şey değişir, inceleme kör olur |
| 2 | Tek sorumluluk | Her rolün tek alanı | Kim ne yaptı belirsiz |
| 3 | Auth/form ayrımı (ADR-043) | Form auth'da, panel burada | Çifte auth akışı |
| 4 | Test/kod ayrımı | Test yalnız `include/` kapsar | Test kodu taklit eder |
| 5 | Vendor karantinası | `vendor/` junction ayrı | Güncelleme kodu bozar |

> **eli10 (basit):** Roller ayrılmış ki kimin neye dokunacağı baştan belli olsun; karışınca herkes her yere girer.
> **eli15 (detay):** Rol/sorumluluk ayrı dosyadır çünkü yetki, kodla aynı anda değişmez; görev dağılımı ayrı hızda revize edilir. Okunması, işe başlamadan sınırı görmeyi sağlar (layer violation önlenir). Yazılması, denetim ve eskalasyonu mümkün kılar.

---

## 4. Kurallar

| # | Kural | Neden var | Kaynak |
|---|-------|-----------|--------|
| 1 | Domain boundary: her rol yalnız dosya tipine dokunur | Layer violation → revert | [[../.ai/AGENTS.md]] §18 |
| 2 | `shared/config/routes.php` DEĞİŞTİRİLMEZ | Ortak route tablosu tek kaynak (PRD-PLT-005) | `config/routes.php:8` dosya yorumu |
| 3 | Auth formları bu klasöre taşınmaz | ADR-043: formlar `auth.coremusic.net/admin/` | ADR-043 |
| 4 | `.env`/secret okunup vault'a/chat'e yazılmaz | Secret guardrail'ı | Kök `AGENTS.md` §3 |
| 5 | `/dev-login` yalnız DEBUG_MODE | Debug backdoor production'a sızamaz | `bootstrap.php:91-105` |
| 6 | Commit subagent ATMAZ | Yetki orkestratörde | Kök `AGENTS.md` §7/§8 |

> **eli10 (basit):** Kurallar herkesin kendi masasında kalmasını, kapı değişikliklerinin onayla olmasını ve sırların yazılmamasını söylüyor.
> **eli15 (detay):** Kurallar ayrı yazıldı ki yetki metni kod içine gömülmesin; rol değişince kod değil, bu tablo değişir. Okunması, görev başlarken sınır ihlalini engeller.

---

## 5. Workflow

```text
GÖREV → MO routing (§3.2) → rol atanır → CONTEXT + CLAUDE okunur
  → uygulama (pages/include/config) → güvenlik yüzeyi ise Security handover (§3.3)
  → TEST (phpunit yeşil) → DOĞRULA → RAPOR → MO → COMMIT: MO/ataması
```

| # | Adım | Neden | Atlarsan ne olur |
|---|------|-------|------------------|
| 1 | §3.2 eşlemesinden rolü bul | Doğru yüzey, doğru el | Layer violation |
| 2 | [[CLAUDE.md]] §4 kuralları oku | ADR-043/058 + shared yasağı | Sözleşme ihlali |
| 3 | Handover tablosu §3.3 | Auth/CSRF/sorgu sınırı | Yetkisiz değişiklik |
| 4 | Test kapısı (QA) | Sözleşme sabitlenir | Sessiz kırılma |
| 5 | Rapor + commit MO'da | Tarih tek elden | Düzensiz git geçmişi |

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 alan + `docType: agents` |
| 2 | Bölüm sırası | §1–§7 |
| 3 | Rol tablosu | §3.1 = 6 satır, her satırda eli10 + eli15 |
| 4 | Dosya eşlemesi | §3.2 her satırında birincil rol var |
| 5 | Placeholder | `{{` kalmadı |
| 6 | Wiki-link | `[[...]]`, hedefler diskte var |
| 7 | Disk kanıtı | Dosya listesi gerçek (`include/` 3 dosya, `config/` 4, `pages/` 6, `tests/` 7) |
| 8 | Uydurma rol/ad | Yalnız [[../.ai/AGENTS.md]] §4'teki 11 agent adı |
| 9 | Emoji | Yok (yalnız `⚠️` işareti gereken yerde) |
| 10 | Dokunulmaz | Üretim kodu + kardeş dokümanlar değişmedi |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör context | [[CONTEXT.md]] | Envanter + auth akışı |
| Klasör kuralları | [[CLAUDE.md]] | Kural listesi |
| Klasör süreç | [[WORKFLOW.md]] | Adım/kapı |
| Klasör gereksinimleri | [[PRD.md]] | PRD-ADM-001..007 |
| Kök agent registry | [[../AGENTS.md]] | §6.1 persona hiyerarşisi |
| Vault agent registry (SSOT) | [[../.ai/AGENTS.md]] | §5/§6/§9 |
| Auth komşusu | [[../auth.coremusic.net/AGENTS.md]] | ADR-043/058 sahipliği |
| shared rolleri | [[../shared/AGENTS.md]] | Ortak katman |
| Template kaynağı | `.ai/.templates/frontend/context-template.md` | İskelet (Guardrail #16) |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-23
