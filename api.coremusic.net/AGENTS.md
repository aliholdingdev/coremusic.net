---
title: "CoreMusic — api.coremusic.net Agent Talimatları"
type: docs
category: api
docType: agents
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/ + kök AGENTS.md"
---

# api.coremusic.net — AGENTS.md

**docType:** agents · **Klasör:** `api.coremusic.net/` · **Sorumlu:** MO (registry)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[CONTEXT.md]] · [[WORKFLOW.md]] · [[../AGENTS.md]]

---

## 1. Amaç

Bu doküman `api.coremusic.net/` klasöründe **kimin hangi dosyaya dokunacağını** (routing) ve rol sınırlarını tanımlar. API JSON-only_gateway olduğundan changes burada sözleşme kırıcıdır; hangi agent'ın hangi yüzeye gireceği önceden bellidir.

| Karar | Kaynak (disk) |
|-------|---------------|
| Domain sınırları → dosya tipi eşlemesi | [[../AGENTS.md]] §5 Domain Boundaries |
| Routing: API/endpoint/PHP → Backend Architect | [[../AGENTS.md]] §6 |
| Auth/CSRF/rate limit → Security Engineer | [[../AGENTS.md]] §6 |
| Test dosyaları → QA Engineer | [[../AGENTS.md]] §5 (`tests/**/*.php`) |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `api.coremusic.net/` içi dosya → rol eşlemesi | `shared/src/` → [[../shared/AGENTS.md]] (MEVCUT) |
| Yetki sınırları + handover tetikleyicileri | `auth.coremusic.net/include/` (SSOT orası) |
| Bu klasörde kime ne zaman başvurulacağı | Vault `.ai/` (MO) |

- **Kullananlar:** MO (routing), atanan agent (yürütme).
- **Ön koşul:** [[CONTEXT.md]] (envanter) okunmuş; hedef dosya diskte mevcut.

---

## 3. Mimari

### 3.1 Rol Tablosu (bu klasörde)

| # | Rol / Agent | Bu klasördeki işi | Dokunabildiği dosyalar | Dokunamadığı | Ne zaman devreye girer | eli10 | eli15 |
|---|-------------|-------------------|------------------------|--------------|------------------------|-------|-------|
| 1 | **Backend Architect** | Gateway, route, controller, container | `index.php` (sıra/sözleşme ADR'siz değişmez), `include/**`, `config/routes.php`, `config/app.php`, `config/constants.php` | `config/cors.php` (Security onayı), `shared/**`, `auth/**` | Yeni uç, route, controller, iş akışı gerektiğinde | API'nin kapı ve masa düzenini kuran usta. | Backend bu klasörün sahibidir çünkü JSON gateway ve route tablosu A2 (K8-K9) katmanıdır. `index.php` içindeki middleware sırası ADR-020 ile sabit olduğundan tek başına değiştirilemez. Yeni uç eklerken route + controller + test üçlüsünü birlikte düşünür. Aşırı sınırı Security'e handover yapar. |
| 2 | **Security Engineer** | CORS, middleware sırası, rate limit, hata sızıntısı denetimi | `config/cors.php`, `index.php` (yalnız güvenlik satırları — ADR ile), RateLimit/Authentication çağrıları | `include/` iş mantığı, `config/routes.php` | Güvenlik kuralı değişince, elyüzeyi denetlenince | Güvenlik kapılarının ve cevap başlıklarının denetçisi. | Security bu yüzeye A1 (K6-K7) sınırından girer; korunan şey hata sızıntısı + kapı sırasıdır. CORS izni ya da başlık değişikliği tek başına onun kararındır. Rate limit/AKS düzeltmelerinde Backend'e handover (§5.3). Denetim sonucu `log.md`'ye yazılır. |
| 3 | **QA Engineer** | Test yazımı/koruma | `tests/**`, `phpunit.xml` | Üretim kodu (`index.php`, `include/`, `config/`) | Kod değişti, kapsam açığı görüldü | API'nin soru kâğıtlarını yazan kişi. | QA bu klasörde yalnız test yüzeyine girer; üretim koduna dokunmaz çünkü test ile kod aynı elde değişirse test kodu taklit eder. `SmokeTest`, `RouteConfigTest`, `AuthControllerTest` mevcut ve korunur. Yeni uç geldiğinde test kapısı (composer test) onun sorumluluğunda yeşil kalır. |
| 4 | **Data Engineer** | — (şema/shared üzerinden) | Bu klasörde dosyası yok; sorgu/şema ihtiyacı `shared/src/Database/` üzerinden | `api/**` dosyaları | Sorgu/şema değişikliği gerektiğinde | Veritabanı işi bu kapıdan değil, ortak kapıdan yürür. | Bu klasörde PHP ile sorgu yazılmaz; DB erişimi `shared` katmanındadır (ADR-002/003). Data Engineer gerektiğinde `../shared` içinde çalışır, api köküne girmez. Sınır, ikinci bağlantı/pseudo-repo doğmasını engeller. |
| 5 | **DevOps Engineer** | Sunucu/rewrite yapılandırması | `web.config` (IIS rewrite) | Uygulama kodu | Sunucu/部署 kuralı değişince | IIS'in kapı yönlendirmesini ayarlayan kişi. | `web.config` sunucu yüzeyidir, uygulama kodundan ayrı hızda değişir. Deployment/IIS kuralı değişince o girer. Kod değişikliğinde çağrılması gerekmez. |
| 6 | **MO (Master Orchestrator)** | Routing + bu doküman güncellemesi | `CONTEXT/CLAUDE/AGENTS/WORKFLOW.md` | Üretim kodu | Görev dağıtımı, doküman senkronu | Kimin nereye gideceğini ayarlayan koordinatör. | MO bu dokümanların sahibidir; routing değişince (§6) tek o günceller. Kod katmanına girmez, kapıları (onay/rapor) işletir. Commit yetkisi de ondadır. |

### 3.2 Dosya → Birincil Rol Eşlemesi (hızlı routing)

| Dosya / Desen | Birincil rol | İkincil / denetim |
|---------------|--------------|-------------------|
| `index.php` (middleware, sıra, hata sözleşmesi) | Backend Architect | Security Engineer (ADR-020) |
| `include/Controller/*`, `include/Container/*` | Backend Architect | QA Engineer (test) |
| `config/routes.php` | Backend Architect | MO (envanter) |
| `config/cors.php` | Security Engineer | Backend Architect |
| `config/constants.php`, `config/app.php` | Backend Architect | Security (secret yok) |
| `tests/**`, `phpunit.xml` | QA Engineer | — |
| `web.config` | DevOps Engineer | — |
| `composer.json` / `composer.lock` | DevOps Engineer + Backend Architect | — |
| `CONTEXT/CLAUDE/AGENTS/WORKFLOW.md` | MO (vault-updater) | — |

### 3.3 Handover Tetikleyicileri (bu klasörden çıkarken)

| Tetikleyici | Kaynak rol | Hedef rol | Öncelik |
|-------------|-----------|-----------|---------|
| Route/controller'da güvenlik açığı tespiti | Backend | Security | CRITICAL |
| `config/cors.php` değişiklik isteği | Backend | Security | HIGH |
| Middleware sırası değişiklik talebi | Backend/Security | MO → ADR | HIGH |
| Yeni uç = şema/sorgu ihtiyacı | Backend | Data (`../shared`) | HIGH |
| Test kapısı (`composer test`) kırmızı | QA | Backend | MEDIUM |
| IIS rewrite kırığı | Backend | DevOps | MEDIUM |

> Kaynak: [[../AGENTS.md]] §9.3 Handover Scenarios (senaryo tablosu); bu tablo klasöre uyarlanmış hâlidir.

### 3.4 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur |
|---|---------|----------|---------------|
| 1 | **Bakım kolaylığı** | Diff küçük | Her şey değişir, inceleme kör olur |
| 2 | **Tek sorumluluk** | Her rolün tek alanı | Kim ne yaptı belirsiz |
| 3 | **Token tek kaynak** | Ortak değer tek yerde | Tutarsızlık + tarama |
| 4 | **Cihaz izolasyonu** | Cihaz farkı ayrı | Yayılır, drift |
| 5 | **Vendor karantinası** | `vendor/` ayrı | Güncelleme kodu bozar |

> **eli10 (basit):** Roller ayrılmış ki kimin neye dokunacağı baştan belli olsun; karışınca herkes her yere girer.
> **eli15 (detay):** Rol/isorumluluk ayrı dosyadır çünkü yetki, kodla aynı anda değişmez; görev dağılımı ayrı hızda revize edilir. Okunması, işe başlamadan sınırı görmeyi sağlar (layer violation önlenir). Yazılması, denetim ve eskalasyonu mümkün kılar. Tek dosyada toplansaydı "bunu kim yazdı" sorusu cevapsız kalırdı.

---

## 4. Kurallar

| # | Kural | Neden var |
|---|-------|-----------|
| 1 | Domain boundary: her rol yalnız dosya tipine dokunur | Layer violation → revert ([[../AGENTS.md]] §18) |
| 2 | Yeni PHP dosyası `php-template.md`'den türetilir (Guardrail #16) | Vault standardı |
| 3 | `.env`/secret okunup vault'a/chat'e yazılmaz | Secret yok guardrail'ı |
| 4 | Sıra/sözleşme değişikliği → ADR + MO | ADR-020 sabit karar |
| 5 | Commit subagent ATMAZ (§5.3) | Yetki orkestratörde |

> **eli10 (basit):** Kurallar herkesin kendi masasında kalmasını ve büyük kapı değişikliklerinin onayla olmasını söylüyor.
> **eli15 (detay):** Kurallar ayrı yazıldı ki yetki metni kod içine gömülmesin; rol değişince kod değil, bu tablo değişir. Okunması, görev başlarken sınır ihlalini engeller. Yazılması, MO'nun routing'i uygulayabilmesini sağlar. Kural ihlalinde revert + log işleri ([[../AGENTS.md]] §17) işletilir.

---

## 5. Workflow

```text
GÖREV → MO routing (§3.2) → rol atanır → CONTEXT + kurallar okunur
  → uygulama → test (QA) → DOĞRULA → RAPOR → MO
  → (gerekirse handover §3.3) → COMMIT: MO/ataması
```

| # | Adım | Neden | Atlarsan ne olur |
|---|------|-------|------------------|
| 1 | §3.2 eşlemesinden rolü bul | Doğru yüzey, doğru el | Layer violation |
| 2 | Handover tablosu §3.3 | Güvenlik/sıra/şema sınırı | Yetkisiz değişiklik |
| 3 | Test kapısı (QA) | Sözleşme sabitlenir | Sessiz kırılma |
| 4 | Rapor + commit MO'da | Tarih tek elden | Düzensiz git geçmişi |

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 alan + `docType: agents` |
| 2 | Bölüm sırası | §1–§7 |
| 3 | Rol tablosu | §3.1 = 6 satır, her satırda eli10 + eli15 |
| 4 | Dosya eşlemesi | §3.2 her satırda birincil rol var |
| 5 | Placeholder | `{{` kalmadı |
| 6 | Wiki-link | `[[...]]`, hedefler diskte var |
| 7 | Disk kanıtı | Dosya listesi gerçek (`include/` 2 dosya, `config/` 4, `tests/` 4) |
| 8 | Dokunulmaz | Üretim kodu + mevcut doküman değişmedi |
| 9 | Emoji | Yalnız `[[...]]` |
| 10 | Uydurma rol/ad | Yalnız [[../AGENTS.md]] §4'teki 11 agent adı |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör context | [[CONTEXT.md]] | Envanter + akış |
| Klasör kuralları | [[CLAUDE.md]] | Kural listesi |
| Klasör süreç | [[WORKFLOW.md]] | Adım/kapı |
| Kök agent registry | [[../AGENTS.md]] | §5/§6/§9 (SSOT) |
| Vault agent registry | [[../.ai/AGENTS.md]] | Agent profilleri |
| shared rolleri | [[../shared/AGENTS.md]] | Ortak katman (MEVCUT) |
| Template kaynağı | `../.ai/.templates/frontend/context-template.md` | İskelet (Guardrail #16) |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-03
