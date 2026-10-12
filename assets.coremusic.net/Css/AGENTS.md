---
title: "CoreMusic — Css/ Rolleri & Routing"
type: docs
category: frontend
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "SSOT (klasör kökü) — routing anahtarı: .ai/AGENTS.md §6"
docType: agents
---

# CoreMusic — Css/ Rolleri & Routing

**docType:** agents · **Katman:** L3 sunum (ITCSS) · **Sorumlu:** MO (routing), UI Designer (birincil uygulama)

**Zorunlu Bağlantılar:** [[../../.ai/.templates/index]] · [[../../.ai/.templates/frontend/css-template]] · [[../../AGENTS.md]] · [[CONTEXT]] · [[CLAUDE]] · [[WORKFLOW]]

---

## 1. Amaç

Bu doküman `assets.coremusic.net/Css/` klasöründe **kimin ne yaptığını** tanımlar: birincil/ikincil sorumlular, katman bazlı sorumluluk, keyword routing ve domain okuması. Global routing SSOT'u [[../../AGENTS.md]] §6'dır; bu dosya **css'e özgü daraltmadır** — çelişkide kök registry kazanır.

| Karar | Karşılığı |
|-------|-----------|
| `*.css` (ITCSS layers) → UI Designer (birincil) | [[../../AGENTS.md]] §5 domain boundary |
| QA: WCAG/responsive doğrulama | §2 rol tablosu |
| Backend: inline style denetimi (CSP) | §2 rol tablosu |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `Css/**/*.css` üretimi, denetimi, taşınması | JS routing/SPA → UI Designer + Backend (ADR-083) |
| CSS görevi çıktısının QA/Security elinden geçişi | SQL/şema → Data Engineer |
| Katman bazlı sorumluluk matrisi | CI/CD, deploy → DevOps |
| CSS-specific keyword routing | Genel vault dokümanı yazımı → MO |

- **Ön koşul:** Mockup Before Frontend — CSS görevine başlayan agent `.ai/ui-design/` görselini okur.
- **Commit kuralı:** subagent **commit ATMAZ** — orkestratöre aittir.

---

## 3. Mimari

### 3.1 Rol Tablosu — Kim ne yapar

| Agent | Kod | **Ne için (işlev)** | **Ne zaman devreye girer (tetikleyici)** | Ne yapar | Ne YAPMAZ |
|-------|-----|----------------------|-------------------------------------------|----------|-----------|
| **UI Designer** | `ui` | CSS/ITCSS üretiminin **tek sahibi** — sunum katmanı sorumluluğu tek elde toplansın (domain boundary) | Görev onayı + `.ai/ui-design/` mockup'ı okunabilir olduğunda; Guardrail #16 şablonu elde | CSS yazar/taşır/ıskeletler; token üretir (01); BEM uygular; import zincirini kurar | Commit atmaz; ADR üretmez; mockupsuz kod yazmaz |
| **QA Engineer** | `qa` | UI çıktısının **bağımsız doğrulaması** — erişilebilirlik ve responsive garantisi üretimden önce | UI teslim ettiğinde (zorunlu son kapı); WCAG/resize/taşma bulgusu geldiğinde | WCAG 2.2 AA (kontrast ≥4.5:1, hedef ≥48px); tarayıcı testi; katman/şablon kontrolü (css-template §6) | Kodu kendisi düzeltmez — bulguyu UI Designer'a handover eder |
| **Backend Architect** | `backend` | CSP uyumunun **PHP kaynağından** denetimi — inline style'ın CSS dosyası dışında da üretilmesini engeller | PHP sayfa/şablon değiştiğinde; `pages/**/*.php` ↔ `05_Pages/` eşleşmesi sorgulandığında; inline style bildirimi geldiğinde | PHP içinde **inline style** tespiti (Guardrail #3 / css-template §4.1 #3); sayfa↔dosya eşleşme denetimi | CSS dosyasına yazmaz |
| **Security Engineer** | `security` | **Vendor/CSP sınırı** — üçüncü taraf sızıntısı ve politika kırılmasının güvenlik kaynağı | Vendor ekleme/güncelleme talebinde; CSP politikası değiştiğinde; `07_Vendors` dışı kod şüphesinde | CSP nonce uyumsuzluğu (inline style/script); `07_Vendors` dışına vendor sızması denetimi | BEM/katman denetimi yapmaz |
| **MO** | `mo` | Routing + **kapanış sorumluluğu** — tek commit kalemi, audit trail | Görev başında (keyword routing §4.1); tüm aşamalar bittiğinde (kapanış/commit); lock çatışmasında | Routing, envanter/CONTEXT güncellemesi, context lock, **commit** | CSS kodu yazmaz |

**Rol eli10/eli15 blokları (§3.1'deki 5 rolün — ne işe yarar, neden okur, ne zaman devreye girer):**

**UI Designer (`ui`)**
> **eli10 (basit):** Ekranların görünümünü yazan ana kişi — buton, renk, düzen onun işi.
> **eli15 (detay):** Bu rol tek tutuldu çünkü görünüm kodu dağınık yazılınca kimin ne yaptığı kaybolur. Şablon ve ekran resmi (mockup) hazır olduğunda devreye girer, o yüzden önce ona görev düşer. Dosyaları okur ki tekrar etmesin, yazar ki tek yerden yönetilsin. Commit atmez, işi öyle teslim eder ki hatası geri alınabilsin.

**QA Engineer (`qa`)**
> **eli10 (basit):** Yazılanı gözden geçiren, bozuk ya da kullanışsız yeri bulan denetçi.
> **eli15 (detay):** Ayrı rol çünkü yapan ile kontrol eden aynı olursa hata kaçar. UI teslim ettiğinde ya da erişilebilirlik/telefon görünümü şüphesinde devreye girer. Dosyaları okur ama düzeltmez — bulduğu sorunu yazana geri gönderir. Öyle ki kontrol bağımsız kalsın.

**Backend Architect (`backend`)**
> **eli10 (basit):** PHP tarafına gizlice stil (inline style) karıştırılmadığını denetleyen kişi.
> **eli15 (detay):** Sorun bazen CSS dosyasında değil sayfayı üreten kodda başlar; o yüzden backend denetimi şart. PHP sayfası değiştiğinde ya da "stil nereden geliyor" şüphesinde devreye girer. CSS dosyalarını okur, eşleşmeyi kontrol eder ama CSS'e dokunmaz. Sınır net kalsın diye.

**Security Engineer (`security`)**
> **eli10 (basit):** Güvenlik açıkları ve dışarıdan gelen kütüphane denetimini yapan uzman.
> **eli15 (detay):** Dış kod (vendor) ve güvenlik politikası (CSP) kimseye sorulmadan değişirse açık doğar; o yüzden ayrı rol. Vendor ekleme/güncelleme ya da güvenlik şüphesi anında devreye girer. `07_Vendors` dosyalarını ve stil kaynaklarını okur, sıradan tasarım işine karışmaz. Güvenlik sınırı bağımsız kalsın diye.

**Master Orchestrator (`mo`)**
> **eli10 (basit):** Kimin ne yapacağını ayarlayan, iş bitince tek elden kaydeden yönetici.
> **eli15 (detay):** Görev dağıtımı ve kayıt (commit) tek elden olmalı ki dağınık değişiklik bir araya gelmesin. Görev başında (routing) ve iş bittiğinde (kapanış) devreye girer, ayrıca kilit çatışmasında arabuluculuk yapar. Diğerlerinin dosyalarını okur, kod yazmaz. Tek kalemden geçmişi tutmak için.

### 3.2 Katman Bazlı Sorumluluk Tablosu

| Katman | Yazım | Denetim | Not |
|--------|-------|---------|-----|
| `01_Abstracts/` | UI Designer | QA (token↔mockup eşleşmesi) | Sadece `--token: değer`; cihaz ayrımı zorunlu |
| `02_Base/` | UI Designer | QA | Reset + yapısal iskelet |
| `03_Layout/` | UI Designer | QA (responsive) | Yerleşimin tek evi (cihaz katmanına yazılmaz) |
| `04_Components/` | UI Designer | QA (BEM) | Figma component karşılığı; `_home-components` dahil |
| `05_Pages/` | UI Designer | Backend (inline) + QA | PHP sayfası karşılığı `p-*.css` |
| `06_Utilities/` | UI Designer | QA | Sıfır mantık sınıf |
| `07_Vendors/` | **KIMSE** | Security | Salt okunur — 33 dosya (17 .css + 16 .map) |
| `08_Devices/` | UI Designer | QA (45-tier) + DevOps | Import + davranış; **eşzamanlı:** `devices.config.js` + `DeviceCssMap.php` |
| `09_ViewModes/` | UI Designer | QA | iskelet dosyalar (v-home/pro/studio/car) |
| `10_Helpers/` | UI Designer | QA | iskelet (h-ellipsis) |
| `11_OAuth/` | UI Designer | Security | oauth.css |
| Kök `auth-bundled.css` | UI Designer | Security + Backend | Tek auth girişi; 07_Vendors içermez |

### 3.3 Handover Senaryoları (css'e özgü — [[../../AGENTS.md]] §9.3)

| Kaynak | Hedef | Tetikleyici | Öncelik |
|--------|-------|-------------|---------|
| UI | QA | CSS değişikliği tamam → tarayıcı/WCAG testi | MEDIUM |
| QA | UI | WCAG/hedef boyut ihlali tespiti | MEDIUM |
| Backend | UI | PHP'de inline style tespiti (Guardrail #3) | HIGH |
| Security | UI | CSP nonce kırıcı inline/`!important` kalıbı | HIGH |
| UI | MO | Katman ihlali / ssot çelişkisi | LOW |

### 3.4 Senaryo → Agent → Handover Matrisi

| # | Senaryo (tetikleyen) | Birincil | Handover / not |
|---|----------------------|----------|----------------|
| 1 | Yeni `.css` dosyası (guardrail #16) | UI Designer | Şablon okunmadıysa MO'ya DUR raporu |
| 2 | `08_Devices/` cihaz dosyası ekleme | UI Designer | `devices.config.js` + `DeviceCssMap.php` eşzamanlı — DevOps bilgilendirir (CI kırılmasın) |
| 3 | Taşma (dosya adı/yolu değişimi) | UI Designer | Tüm `@import` + map + PHP docblock — sonra QA link/asset testi |
| 4 | Token ekleme/değiştirme | UI Designer | QA: token↔mockup eşleşmesi (PNG > ASCII) |
| 5 | Boş iskeletin doldurulması (09/10) | UI Designer | Mockup Before Frontend — görsel yoksa iskelet kalır |
| 6 | WCAG bulgusu (kontrast/hedef boyut) | QA Engineer | → UI Designer (MEDIUM) |
| 7 | PHP'de inline style | Backend Architect | → UI Designer (HIGH, Guardrail #3) |
| 8 | CSP nonce kırıcı inline/`!important` kalıbı | Security Engineer | → UI Designer (HIGH) |
| 9 | Vault ↔ disk çelişkisi | İlk farkeden | `⚠️ VERIFICATION REQUIRED` + MO; disk kazanır |
| 10 | `main.css`'e import isteği | UI Designer | RED — `main.css` YOK (§4 #8); giriş `08_Devices/*` + `auth-bundled` |
| 11 | `07_Vendors` değişiklik talebi | Security Engineer | RED — salt okunur (§4 #1) |
| 12 | Repo genelinde CSS revizyonu / branch birleştirme | MO | Context lock + queue; en esik kilidi MO kırar |

### 3.5 Escalasyon (css'e özgü örnekler — [[../../AGENTS.md]] §10)

| Durum | Seviye 1 | Seviye 2 | Seviye 3 |
|-------|----------|----------|----------|
| Guardrail #1-#6 ihlali tekrar tekrar | UI Designer düzeltir | MO revert eder | — |
| Katman ihlali (05'e component, 08'e yerleşim) | UI Designer taşır | MO revert + log ERROR | — |
| Mockup/ASCII çelişkisi | UI Designer DUR | MO | İnsan |
| Token SSOT belirsizliği (Figma boş — örn. 3840/TV) | UI Designer `UNKNOWN` yazar | MO türetme kuralını sorar | İnsan |

### 3.6 Context Lock — Eşzamanlı Erişim

| Kural | Değer |
|-------|-------|
| Kilitleme süresi | max 30 sn |
| Öncelik | CRITICAL > HIGH > MEDIUM > LOW |
| Deadlock | MO en eski kilidi kırar |
| Logging | acquire/release → `log.md` |

---

## 4. Kurallar

| # | Kural | Ref |
|---|-------|-----|
| 1 | `*.css` dosyası başka agent tarafından **yazılamaz** — yalnız UI Designer; diğerleri denetler, raporlar | [[../../AGENTS.md]] §5 |
| 2 | Guardrail #16: css katman dosyası [[../../.ai/.templates/frontend/css-template]]'ten üretilir | §13.5 |
| 3 | Domain boundary ihlali → revert + MO | [[../../AGENTS.md]] §18 |
| 4 | `⚠️ VERIFICATION REQUIRED` etiketi bilinmeyen API/seçici/dosya için zorunlu | Zero-Hallucination |
| 5 | Commit subagent'ta DEĞİL — orkestratör | [[../../AGENTS.md]] §13.9 #5 |
| 6 | `.ai/` kural dosyaları yalnız ihtiyaç anında `@` ile okunur (boot'ta toplu okuma yasak) | Kök AGENTS §9 |

### 4.1 CSS-Specific Keyword Routing (kök §6 satırının daraltması)

| Keyword'ler | Birincil | İkincil |
|-------------|----------|---------|
| CSS, ITCSS, BEM, token, responsive, mockup, ui-design, glassmorphism, design, frontend, accessibility, screen-spec, 45-tier, device-matrix, c01-c16 | **UI Designer** | **QA Engineer** |
| inline style, CSP nonce, `style=""` | Backend Architect | Security Engineer |
| `07_Vendors`, bootstrap sürümü, vendor sızması | Security Engineer | UI Designer |
| WCAG, kontrast, touch target, tarayıcı testi | QA Engineer | UI Designer |

### 4.2 §24.3 Domain Okuması — CSS Satırları (kök registry §24.3 Frontend satırının css özeli)

| # | Zorunlu Okuma | Ne için |
|---|---------------|---------|
| 1 | `.ai/ui-design/01-mockup-index.md` + `.ai/.png/` ilgili PNG | Mockup Before Frontend — PNG > ASCII > Inventory > Tokens sırası |
| 2 | `.ai/.templates/frontend/css-template.md` (Guardrail #16 zorunlu) | Katman sırası, §4.1 10 guardrail, §3 şablonları |
| 3 | `.ai/ui-design/tokens/design-tokens-master.md` + katman `CONTEXT.md`/`CLAUDE.md` | Token SSOT + klasör envanteri/çelişki kaydı |

> Ek (görev tipine göre): bileşen görevi → `.ai/ui-design/02-component-inventory.md` · cihaz görevi → `.ai/ui-design/reference/04-verification` + `10-device-specific-guidelines` · WCAG görevi → `.ai/ui-design/04-accessibility-gaps.md`.

---

## 5. Workflow

```
MO ROUTING (keyword → §4.1) → AGENT ATAMA → NOTES/MOCKUP OKU
→ KATMAN SEÇ → ŞABLON KOPYALA → ÜRET → QA DENETİMİ (WCAG/BEM/katman)
→ HANDOVER (varsa) → MO COMMIT
```

1. **Routing:** keyword §4.1 tablosuna eşlenir; `*.css` → UI Designer.
2. **Pre-flight:** mockup okundu mu? şablon (Guardrail #16) okundu mu? — yoksa DUR.
3. **Üretim:** UI Designer [[CLAUDE]] §4.1 10/10 uygular.
4. **Denetim:** QA (WCAG + responsive + css-template §6), Backend (inline), Security (CSP/vendors).
5. **Handover:** §3.3 tablosuna göre; onaysız tamamlanmaz.
6. **Commit:** MO — subagent atmaz.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: agents` |
| 2 | Bölüm | §1–§7, ≤3 başlık seviyesi |
| 3 | Roller | UI=birincil, QA=WCAG/responsive, Backend=inline — 3 satır eksiksiz |
| 4 | Katman matrisi | 12 satır (11 katman + kök); her satırda yazım + denetim |
| 5 | Routing | CSS keyword satırı kök §6 ile uyumlu |
| 6 | §24.3 | CSS-specific 3 satır mevcut |
| 7 | Commit | "subagent atmaz" ifadesi mevcut |
| 8 | Halüsinasyon | Diskte olmayan rol/agent/dosya iddia edilmedi |
| 9 | **eli10 + eli15 bloğu** | §3.1'deki 5 rol maddesinin her birinde `> **eli10 (basit):**` + `> **eli15 (detay):**` bloğu var mı; "neden okur / ne zaman devreye girer" eli15 içinde geçiyor mu |

---

## 7. Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Agent registry (SSOT) | [[../../AGENTS.md]] | §5 domain · §6 routing · §9 handover · §24.3 okuma |
| CSS ana şablon | [[../../.ai/.templates/frontend/css-template]] | Guardrail #16 + §4.1 |
| Klasör envanteri | [[CONTEXT]] | Disk sayıları |
| Kural özeti | [[CLAUDE]] | 10 hard guardrail |
| Süreç | [[WORKFLOW]] | Adım akışı |
| Agent profilleri | [[../../.ai/.agents/AGENTS]] | Profil indeksi |
| UI Designer profili | [[../../.ai/.agents/ui-designer]] | Rol detayı |
| QA profili | [[../../.ai/.agents/qa-engineer]] | WCAG test sorumluluğu |
| Template registry | [[../../.ai/.templates/index]] | Şablon kaydı |

---

**Version:** 1.0.0 · **Last Updated:** 2026-10-03 · **docType:** agents
