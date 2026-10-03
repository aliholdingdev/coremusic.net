---
title: "CoreMusic — assets.coremusic.net İş Akışı"
type: docs
category: frontend
docType: workflow
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/ + kök AGENTS.md"
---

# assets.coremusic.net — WORKFLOW.md

**docType:** workflow · **Klasör:** `assets.coremusic.net/` · **Sorumlu:** MO (workflow)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[CONTEXT.md]] · [[../AGENTS.md]]

---

## 1. Amaç

Bu doküman `assets.coremusic.net/` üzerinde bir değişikliğin **adım adım akışını** (adım → çıktı → neden → atlarsan ne olur) ve **kapılarını** tanımlar. Asset servisi tüm subdomainlerin görünüm/behavior kaynağını beslediğinden hata sitenin tamamına yayılır; kapılar (keşif → mockup → şablon → uygulama → senkron → test → rapor) zorunludur.

| Karar | Kaynak (disk) |
|-------|---------------|
| Test altyapısı: Vitest + Playwright | `vitest.config.js`, `playwright.config.ts` (ikisi de VAR) |
| Test dosyaları `tests/` altında (5 dosya) | `tests/` dosya listesi |
| Şablon zorunluluğu: `css-template` + `js-template` | [[../.ai/.templates/frontend/css-template]] · [[../.ai/.templates/frontend/js-template]] |
| Mockup Before Frontend | [[../.ai/ui-design/01-mockup-index]] (VAR) |
| Cihaz CSS eşzamanlılığı: `js/devices.config.js` + `DeviceCssMap.php` | assets `AGENTS.md` §4 Zorunlu 4 |
| Commit subagent'a ait değil | Kök [[../AGENTS.md]] §7/§8 — "commit (subagent ATMAYACAK)" |

> VERIFICATION REQUIRED: `assets.coremusic.net/` kökünde **`package.json` YOK** ve `node_modules/` YOK (kök dizin listesi: `Css/`, `Fonts/`, `Image/`, `js/`, `tests/` + 6 kök dosya) — yani `vitest`/`playwright` komutunun bu klasörden nasıl koştuğu **UNKNOWN**; test komutu yolu göreve başlarken doğrulanmalıdır. Ayrıca `tests/` kapsamı şimdilik `player-info` eksenlidir; modül kapsamı UNKNOWN. `.ai/.rules/error-recovery.md` **diskte YOK** — hata kurtarma adımı kök `AGENTS.md` §7 metnine dayanır.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `assets.coremusic.net/` içinde CSS/JS/font/görsel/test değişiklik akışı | `Css/` katman içi üretim ayrıntısı → `Css/WORKFLOW.MD` |
| Kapılar: keşif → mockup → şablon → uygulama → senkron → test → rapor | Deploy/CI → DevOps |
| Cihaz haritası eşzamanlılığı (`devices.config.js` + `DeviceCssMap.php`) | PHP sayfa sunumu → `home/` `auth/` `api/` |
| Commit kuralı (subagent atmaz) | Vault `.ai/` içi doküman akışı |

- **Kullananlar:** UI Designer (birincil), Security Engineer (CSP/vendor), QA Engineer (Vitest/Playwright), MO (kapı + rapor).
- **Ön koşul:** [[CONTEXT.md]] (envanter) + [[CLAUDE.md]] / [[AGENTS.md]] (kural) okunmuş; görünüm işi ise mockup okunmuş.
- **Dokunulmaz yüzeyler:** `Css/07_Vendors/` (33 dosya) · `Fonts/` · `Image/` (yalnız onaylı ekleme/silme).

---

## 3. Mimari

### 3.1 Adım Tablosu (adım → çıktı → neden → atlarsan ne olur)

| # | Adım | Çıktı | Neden | Atlarsan ne olur | eli10 | eli15 |
|---|------|-------|-------|------------------|-------|-------|
| 1 | Görev tanımını netleştir (prompt-maker / soru kapısı) | Onaylı görev özeti | Yanlış iş yapmayı önler | İstenmeyen değişiklik, revert | Önce ne istendiğini anlamak; yanlış anlarsan tüm iş boşa gider. | Kapı (kök `AGENTS.md` §1) onaysız başlamaz. Tanım okunmadan başlarsan beklenti ile kod ayrışır. Çıktı, ölçülebilir özet olur. Atlarsan yanlış iş üretimi ve zaman kaybı. |
| 2 | Klasör kurallarını oku: [[CLAUDE.md]] + [[AGENTS.md]] + [[CONTEXT.md]] (+ katman işi ise [[Css/CONTEXT.md]]) | Kural + envanter + §3.6 çelişki listesi | Klasör-specific yasaklar orada | Yasak ihlali (vendor, inline, rename) | Bu klasörün özel kurallarını öğrenmek. | Kurallar klasör dosyalarında tutulur çünkü genel kural her detayı taşıyamaz. Çelişki listesi okununca eski sayılarla iş yapılmaz (ör. `main.css` YOK). Envanter hangi katmanın kaç dosya olduğunu baştan gösterir. Atlarsan yanlış katman/yanlış sayı. |
| 3 | **Mockup kapısı:** [[../.ai/ui-design/01-mockup-index]] + ilgili PNG (PNG > ASCII > Inventory > Tokens) | Okunmuş mockup | Görsel kanıt zorunlu | Kanonik olmayan UI, revert | Ekranın gerçek ölçüsünü resimden görmek. | Kök `AGENTS.md` §7 + assets `AGENTS.md` §4 Zorunlu 1 bu adımı şart koşar. Görsel okunamıyorsa DUR ve bildir; tahmini değer yazılmaz. Kapı, kod ile görseli karşılaştırır. Atlarsan tasarım sapması. |
| 4 | **Şablon kapısı:** görev tipine göre `css-template.md` veya `js-template.md` | Okunmuş şablon | Guardrail #16 — şablonsuz dosya üretilmez | Standartsız dosya, vault reddi | Dosyayı hazır kalıba göre açmak. | CSS katman dosyası `css-template` §3.1'den, JS modülü `js-template`'ten türetilir. Şablon okunmadan üretilen dosya revizyonda elenir. Şablon, katman sırasını ve guardrail'ları hazır getirir. Atlarsan reddedilen dosya + ikinci iş. |
| 5 | Hedef dosyayı 1. kez oku + disk kanıtı topla | Okunmuş dosya, import zinciri | Anti-overthink: ilk okumadan KARAR VER | Aynı dosya 2. kez okunur, token israfı | Dosyayı bir kez okuyup karar vermek. | Anti-waste kuralı (kök `AGENTS.md` §5) ikinci okumayı yasaklar. İlk okuma import zincirini ve mevcut kuralları verir. Karar ilk okumadan verilir. Atlarsan tekrarlı okuma ve gecikme. |
| 6 | Değişikliği yap — hedefe kilitli: `Css/<katman>/` veya `js/<modül>/` | Kod diff'i | Katman/IRCSS sınırı + BEM | Katman ihlali, kapsam genişlemesi | İstenen yeri değiştirmek, başka yere dokunmamak. | Katman seçimi `css-template` §3.1'e göredir (token → 01, yerleşim → 03, bileşen → 04, sayfa → 05, cihaz → 08). BEM `block__element--modifier` zorunlu. Kapsam genişlerse çakışma doğar. Atlarsan katman ihlali + revert. |
| 7 | Yeni cihaz dosyası ekleme/değişikliği → `js/devices.config.js` + `shared/src/Device/DeviceCssMap.php` **EŞZAMANLI** | İki harita da güncel | Cihaz → css yolu tek eşleme | Çift/eksik CSS yüklenmesi, bozuk cihaz görünümü | İki haritayı birlikte güncellemek. | assets `AGENTS.md` §4 Zorunlu 4 bu adımı şart koşar. Tek taraf değişirse cihaz yükleyici yanlış dosyayı ister. Değişiklik diff'inde iki dosya birlikte görünmelidir. Atlarsan bozuk cihaz görünümü. |
| 8 | Token ekleme/değiştirme yalnız `Css/01_Abstracts/` içinde; ham hex/px katmana yazılmaz | Token satırı | Token SSOT (ADR-001/045) | Tutarsız değer, çoklu kaynak | Değeri tek sözlüğe yazmak. | assets `AGENTS.md` §4 Zorunlu 2 bu kuralı koyar. Katmana gömülürse değer dağılır ve değişince her yerde tarama gerekir. Token mockup ölçüsüne dayanır. Atlarsan tutarsızlık + drift. |
| 9 | Test ekle/koru: `tests/` altında (Vitest birim / Playwright uç) + `mocks/` | Yeni/güncel test + mock | Regresyon kapısı | Sessiz kırılma, bozuk arayüz | Kodun doğru kaldığını kanıtlayan soru kâğıdı. | Mevcut desen: `tests/components/*.spec.js`, `tests/mocks/*.mock.js`, `tests/e2e/*.spec.ts`. Mock, gerçek servise dokunmaz. Yeni özellik = yeni test. Atlarsan sessiz kırılma. |
| 10 | Test kapısı: Vitest + Playwright (`vitest.config.js`, `playwright.config.ts` üzerinden) | Yeşil kapı | Regresyon kapısı | Hatalı değişiklik git'e girer | Sınavı çalıştırıp geçmek. | Yapılandırma dosyaları diskte VAR; ancak `package.json` YOK (§1 notu) → komut yolu görev başında doğrulanır. Kapıdan geçmeyen iş bitmiş sayılmaz. Kırmızıda kök `AGENTS.md` §7: kurala dön → yeniden yaz → tekrar kontrol. |
| 11 | UI etkisi varsa browser testi (gerçek sayfa + mockup karşılaştırma) | Ekran doğrulaması | Yerleşim kırığını yakala | Bozuk layout site geneline yayılır | Değişiklikten sonra sayfayı tarayıcıda kontrol etmek. | Kök `AGENTS.md` §7 #7 zorunlu kılar; DOM/layout iddiası kod ile kanıtlanamaz. Katman değişimi tüm subdomainleri etkilediği için kapı önemlidir. Atlarsan gizli yerleşim hatası. |
| 12 | Rapor yaz; **commit ATMAZ** | Rapor | Yetki sınırı | Yetkisiz tarih, revert riski | İş bitince haber vermek; commit'i başkasının atması. | Subagent commit atmaz (kök `AGENTS.md` §7/§8); commit yetkisi orkestratördedir. Tarih tek elden yazılır. Rapor, kapıyı orkestratöre teslim eder. Atlarsan iş görünmez kalır ya da düzensiz commit oluşur. |

**eli10 / eli15 blokları (§3.1'deki 12 adımın karşılığı):**

**Adım 1 — Görev tanımı**
> **eli10 (basit):** Önce ne istendiğini anlamak.
> **eli15 (detay):** Kapı onaysız başlamaz. Tanım okunmadan kod yazılırsa beklenti ile sistem ayrışır. Çıktı ölçülebilir özettir. Atlarsan yanlış iş üretirsin.

**Adım 2 — Klasör kuralları**
> **eli10 (basit):** Bu klasörün kendi kurallarını ve bilinen farklarını öğrenmek.
> **eli15 (detay):** Kurallar ayrı dosyadır. Çelişki listesi okununca eski sayılarla iş yapılmaz. Envanter katman sınırlarını baştan gösterir. Atlarsan yanlış katman ve yanlış sayı.

**Adım 3 — Mockup kapısı**
> **eli10 (basit):** Ekranın gerçek ölçüsünü resimden görmek.
> **eli15 (detay):** Sıra PNG > ASCII > Inventory > Tokens'tır. Görsel okunamıyorsa DUR ve bildir; tahmin yazılmaz. Kapı kod ile görseli karşılaştırır. Atlarsan tasarım sapması ve revert.

**Adım 4 — Şablon kapısı**
> **eli10 (basit):** Dosyayı hazır kalıba göre açmak.
> **eli15 (detay):** Guardrail #16 şablonsuz dosyayı reddeder. CSS `css-template`, JS `js-template` ile açılır. Şablon katman sırasını hazır getirir. Atlarsan standartsız dosya + ikinci iş.

**Adım 5 — 1. kez okuma**
> **eli10 (basit):** Dosyayı bir kez okuyup karar vermek.
> **eli15 (detay):** Anti-waste kuralı ikinci okumayı yasaklar. İlk okuma import zincirini verir. Karar ilk okumadan verilince akış hızlanır. Atlarsan token israfı ve gecikme.

**Adım 6 — Uygulama**
> **eli10 (basit):** Yalnız hedef katmanda ve doğru dosyada değişiklik.
> **eli15 (detay):** Katman seçimi şablona göredir (token → 01, yerleşim → 03 …). BEM zorunludur. Kapsam genişlerse çakışma doğar. Atlarsan katman ihlali + revert.

**Adım 7 — Cihaz senkronu**
> **eli10 (basit):** Yeni cihaz ayarını iki harita dosyasında birlikte güncellemek.
> **eli15 (detay):** `devices.config.js` frontend'i, `DeviceCssMap.php` backend'i tutar. Tek taraf değişirse yükleyici yanlış dosyayı ister. İkisi tek diff'te görünmelidir. Atlarsan bozuk cihaz görünümü.

**Adım 8 — Token**
> **eli10 (basit):** Değeri tek sözlüğe yazmak.
> **eli15 (detay):** Ham değer katmana gömülmez; yalnız `01_Abstracts` içinde token olur. Böylece değer tek yerden değişir. Token mockup ölçüsüne dayanır. Atlarsan tutarsızlık + drift.

**Adım 9 — Test**
> **eli10 (basit):** Kodun doğru kaldığını kanıtlayan sorular yazmak.
> **eli15 (detay):** Mevcut desen `tests/{components,mocks,e2e}`. Mock gerçek servise dokunmaz. Yeni özellik = yeni test. Atlarsan sessiz kırılma.

**Adım 10 — Test kapısı**
> **eli10 (basit):** Sınavı çalıştırıp geçmek.
> **eli15 (detay):** Yapılandırma dosyaları diskte VAR, `package.json` YOK → komut yolu görev başında doğrulanır (§1 notu). Kapıdan geçmeyen iş bitmez. Kırmızıda kök `AGENTS.md` §7 işletilir. Atlarsan kırık değişiklik git'e girer.

**Adım 11 — Browser testi**
> **eli10 (basit):** Değişikliği gerçek sayfada görmek.
> **eli15 (detay):** DOM/layout iddiası kod ile kanıtlanamaz. Mockup ile karşılaştırma zorunludur. Katman değişimi site genelini etkiler. Atlarsan gizli yerleşim hatası.

**Adım 12 — Rapor**
> **eli10 (basit):** İş bitince haber vermek; commit'i başkasına bırakmak.
> **eli15 (detay):** Commit yetkisi orkestratördedir; tarih tek elden yazılır. Rapor kapıyı teslim eder. Dağınık commit iz sürmeyi bozar. Atlarsan iş görünmez kalır.

### 3.2 Kapılar (Gate)

```text
[K1 KEŞİF: kural + envanter] ─▶ [K2 MOCKUP: PNG okundu] ─▶ [K3 ŞABLON: css|js-template okundu]
  ──onay──▶ [K4 UYGULAMA: Css/<katman> | js/<modül>] ─ (cihaz ise K5 SENKRON: 2 harita)
  ─▶ [K6 TEST: Vitest + Playwright yeşil] ─ (UI ise K7 BROWSER)]
  ─▶ [K8 RAPOR → ORKESTRATÖR → COMMIT (MO)]
```

| Kapı | Koşul | Red durumunda |
|------|-------|---------------|
| K1 Keşif | CLAUDE + AGENTS + CONTEXT okundu; 1. okumadan karar | Görev uygulamaya alınmaz |
| K2 Mockup | `.ai/ui-design/` görseli okundu (görünüm işi ise) | DUR + bildir; tahmini değer yazılmaz |
| K3 Şablon | İlgili şablon (`css-template`/`js-template`) okundu | Dosya üretilmez (Guardrail #16) |
| K4 Uygulama | Diff yalnız hedef katman/modül; BEM + token kuralı uygun; `07_Vendors`/`Fonts`/`Image`'e onaysız dokunulmadı | Kapsam daraltılır / revert |
| K5 Senkron | Cihaz dosyası değiştiyse `devices.config.js` + `DeviceCssMap.php` birlikte | Eşzamanlılık sağlanır, tekrar denenir |
| K6 Test | Vitest + Playwright yeşil (`vitest.config.js`, `playwright.config.ts`); komut yolu doğrulandı | Kök `AGENTS.md` §7 → kurala dön → yeniden yaz → tekrar kontrol (3 başarısız → DUR + 1 soru) |
| K7 Browser | Gerçek sayfa + mockup karşılaştırması geçti | Düzelt, tekrar test |
| K8 Rapor | Rapor tam; **commit ORKESTRATÖRDE** | Subagent commit atmaz |

**eli10 / eli15 blokları (§3.2 — 8 kapı):**

**K1 Keşif**
> **eli10 (basit):** Kural ve envanter okunmadan başlamamak.
> **eli15 (detay):** Kapı, yanlış katman ve eski sayılarla işi engeller. Redde görev başlamaz. Eksik okuma kapsam hatası doğurur.

**K2 Mockup**
> **eli10 (basit):** Resim okunmadan görünüm kodu yazmamak.
> **eli15 (detay):** Görsel okunamıyorsa DUR ve bildir. Tahmini ölçü yazılmaz. Kapı, kod ile tasarımı karşılaştırır. Atlarsan tasarım sapması.

**K3 Şablon**
> **eli10 (basit):** Dosyayı şablona göre açmak.
> **eli15 (detay):** Guardrail #16 şablonsuz üretimi reddeder. Şablon katman sırasını getirir. Okunmadan üretilen dosya elenir. Redde dosya yazılmaz.

**K4 Uygulama**
> **eli10 (basit):** Yalnız hedef katmanda değişiklik; dokunulmazlara dokunmamak.
> **eli15 (detay):** `07_Vendors`, `Fonts`, `Image` onaysız değişmez. BEM ve token kuralı zorunludur. Redde genişletilmiş kapsam geri alınır.

**K5 Senkron**
> **eli10 (basit):** Cihaz ayarını iki haritada birlikte tutmak.
> **eli15 (detay):** Tek taraf değişirse yükleyici yanlış dosyayı ister. Kapı, iki dosyanın birlikte değiştiğini doğrular. Redde eşzamanlılık sağlanır. Atlarsan bozuk cihaz görünümü.

**K6 Test**
> **eli10 (basit):** Sınav yeşil olmadan iş bitmez.
> **eli15 (detay):** Kapı regresyonu yakalar. Komut yolu `package.json` yokluğu nedeniyle görev başında doğrulanır. Kırmızıda kurala dönülür. 3 başarısız düzeltmede durulur ve tek soru sorulur.

**K7 Browser**
> **eli10 (basit):** Değişikliğin gerçek ekranda doğru olduğunu görmek.
> **eli15 (detay):** Kod ile yerleşim kanıtlanamaz. Mockup karşılaştırması zorunludur. Redde düzeltip tekrar test edilir. Atlarsan site geneline yayılan hata.

**K8 Rapor**
> **eli10 (basit):** İşi haber vermek; commit'i orkestratöre bırakmak.
> **eli15 (detay):** Yetki sınırı burada kapanır. Tarih tek elden yazılır. Rapor olmadan iş görünmezdir. Subagent commit atmaz.

### 3.3 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur |
|---|---------|----------|---------------|
| 1 | **Bakım kolaylığı** | Diff küçük, güvenli | Her şey değişir, inceleme kör olur |
| 2 | **Tek sorumluluk** | Adımın tek sahibi | Sahipsiz adım |
| 3 | **Token tek kaynak** | Ölçü/renk `01_Abstracts`'te | Her dosyada ayrı değer → tutarsızlık |
| 4 | **Cihaz izolasyonu** | Telefon ayarı masaüstünü bozmaz | Fark her yere sıçrar, drift |
| 5 | **Vendor karantinası** | `07_Vendors/` + `vendor/` ayrı | Güncelleme bizim kodu bozar |

> **eli10 (basit):** Adımlar ve kapılar ayrı kutularda; bir kutu değişince diğeri bozulmasın.
> **eli15 (detay):** Süreç ayrı dosyadır çünkü kapılar (mockup, şablon, senkron, test, rapor) koddan farklı hızda değişir; kod içine gömülse kapı unutulur. Okunması, işe sırayla başlamayı sağlar. Yazması, tekrarlanabilir ve denetlenebilir akış bırakır. Bölünmezse "hangi kapı kaldı" sorusu cevapsız kalır.

### 3.4 Dosya Tipi → Şablon → Kapı Eşlemesi

| # | Dosya/değişiklik | Şablon | Zorunlu adımlar | Kapılar | eli10 | eli15 |
|---|------------------|--------|-----------------|---------|-------|-------|
| 1 | `Css/01_Abstracts/token` ekleme/değiştirme | `css-template` | Adım 1-6 + **Adım 8 (token)** | K1-K4, K6, K7 | Değer tek sözlüğe girer; başka katmana ham değer yazılmaz. | Token mockup ölçüsüne dayanır (PNG > ASCII sırası). Ham hex/px katman dosyasına girerse dağılır ve değişince her yerde tarama gerekir. Kapı, token ↔ mockup eşleşmesini de kontrol eder. |
| 2 | `Css/03_Layout/` · `04_Components/` · `05_Pages/` değişikliği | `css-template` | Adım 1-6 + Adım 11 | K1-K4, K6, K7 | Yerleşim/parça/sayfa katmanı seçimi şablona göredir. | Katman ayrımı zorunludur (component → 04, PHP sayfası → 05 — Guardrail #8). BEM `block__element--modifier` şarttır. Yanlış kataman yazımı revert ile sonuçlanır. |
| 3 | `Css/08_Devices/` cihaz dosyası ekleme/değişikliği | `css-template` | Adım 1-6 + **Adım 7 (senkron)** | K1-K5, K6, K7 | Cihaz ayarı iki harita dosyasıyla birlikte değişir. | `js/devices.config.js` (frontend) ile `shared/src/Device/DeviceCssMap.php` (backend) eşzamanlı güncellenir; tek taraf değişirse yükleyici yanlış dosyayı ister. Cihaz katmanı yalnız import + davranış taşır, yerleşim taşımaz. |
| 4 | `Css/07_Vendors/` değişikliği | — (RED) | Adım 1-2 + Security bilgilendirme | K1 reddi | Başkalarının yazdığı kutuya elle girilmez. | 33 dosya (17 `.css` + 16 `.map`) salt okunurdur; yalnız onaylı sürüm yükseltmesi Security onayıyla yapılır. Elle değişiklik güncellemede kaybolur ve güvenlik yamalarını şaşırır. Kapı baştan engeller. |
| 5 | `js/<modül>/*.js` değişikliği | `js-template` | Adım 1-6 + Adım 9-10 | K1-K4, K6, K7 | Davranış kodu kendi kalıbıyla açılır. | Vanilla ES6+ zorunlu; `var`, `eval`, `innerHTML` yasaktır (DOMParser + TrustedTypes). Modül klasörü (`core/`, `managers/`, `router/` …) yerel `CLAUDE.md` notuna bağlıdır. Yeni modül = yeni test. |
| 6 | `js/devices.config.js` değişikliği | `js-template` | Adım 1-6 + **Adım 7 (senkron)** | K1-K5, K6 | Frontend haritası backend haritasıyla birlikte değişir. | Bu dosya cihaz → css yolunun frontend kaynağıdır; `DeviceCssMap.php` ile ikizdir. İkisi tek diff'te görünmelidir. Tek başına değişirse tüm cihazlarda yükleme bozulur. |
| 7 | `Fonts/` · `Image/` ekleme/silme/rename | — (onaylı) | Adım 1-2 + onay | K1 + onay | Binary varlıklar referansla bağlıdır — onaysız değişmez. | Silme/rename mevcut `@font-face` ve görsel referanslarını kırar. Yalnız onaylı ekleme/kaldırma yapılır; diff, hangi referansın etkilendiğini raporlar. Redde dosya geri alınır. |
| 8 | `tests/**` değişikliği | — | Adım 1-2 + Adım 4 + Adım 9-10 | K1, K2, K6 | Test dosyası kendi kapısından geçer. | Mevcut desen `tests/{components,mocks,e2e}` korunur. Mock, gerçek servise dokunmaz. Yeni özellik = yeni test. Kapı yeşil kalır. |
| 9 | Yeni `.css` / `.js` dosyası | `css-template` / `js-template` | Adım 1-2 + **Adım 4 (şablon)** + Adım 6 | K1, K3, K4, K6 | Yeni dosya her zaman hazır kalıpla açılır. | Guardrail #16 şablonsuz dosyayı reddeder. Doğru katman/modül şablonla birlikte seçilir; yanlış yere açılan dosya import zincirine girmez. Atlarsan reddedilen dosya + ikinci iş. |

**eli10 / eli15 blokları (§3.4 — 9 dosya tipi):**

**1 · Token**
> **eli10 (basit):** Değer tek sözlüğe girer, başka katmana ham değer yazılmaz.
> **eli15 (detay):** Token mockup ölçüsüne dayanır. Ham değer dağılırsa değişince her yerde tarama gerekir. Kapı token ↔ mockup eşleşmesini kontrol eder. Diğer katmanlarda yalnız `var()` okunur.

**2 · Layout / Component / Page**
> **eli10 (basit):** Yerleşim, parça ve sayfa katmanları ayrıdır.
> **eli15 (detay):** Katman ayrımı Guardrail #8'dir. BEM zorunludur. Yanlış kataman revert ile sonuçlanır. Kapılar sırayla geçilir.

**3 · Device**
> **eli10 (basit):** Cihaz ayarı iki haritayla birlikte değişir.
> **eli15 (detay):** Frontend ve backend haritası ikizdir. Tek taraf değişirse yükleyici yanlış dosyayı ister. Cihaz katmanı yalnız import + davranış taşır. Kapı senkronu doğrular.

**4 · Vendors**
> **eli10 (basit):** Başkalarının yazdığı kutuya elle girilmez.
> **eli15 (detay):** 33 dosya salt okunurdur. Yalnız onaylı sürüm yükseltmesi Security ile yapılır. Elle değişiklik kaybolur ve güvenlik yamalarını şaşırır. Kapı baştan engeller.

**5 · JS modülü**
> **eli10 (basit):** Davranış kodu kendi kalıbıyla açılır.
> **eli15 (detay):** Vanilla ES6+ zorunlu; `var`/`eval`/`innerHTML` yasaktır. Klasör notu (`CLAUDE.md`) okunur. Yeni modül = yeni test. Kapı regresyonu yakalar.

**6 · Cihaz yapılandırması**
> **eli10 (basit):** Frontend haritası backend haritasıyla birlikte değişir.
> **eli15 (detay):** İki dosya ikizdir ve tek diff'te görünmelidir. Tek başına değişirse tüm cihazlarda yükleme bozulur. Kapı ikisini birlikte ister. Kural ADR/AGENTS §4 ile bağlanır.

**7 · Font / Image**
> **eli10 (basit):** Binary varlıklar onaysız değişmez.
> **eli15 (detay):** Silme/rename mevcut referansları kırar. Yalnız onaylı ekleme/kaldırma yapılır. Diff etkilenen referansı raporlar. Redde dosya geri alınır.

**8 · Test**
> **eli10 (basit):** Test dosyası kendi kapısından geçer.
> **eli15 (detay):** Desen korunur (`tests/{components,mocks,e2e}`). Mock gerçek servise dokunmaz. Yeni özellik = yeni test. Kapı yeşil kalır.

**9 · Yeni dosya**
> **eli10 (basit):** Yeni dosya her zaman hazır kalıpla açılır.
> **eli15 (detay):** Şablon okunmadan üretim yapılmaz. Doğru katman/modül birlikte seçilir. Şablonsuz dosya revizyonda elenir. Atlarsan ikinci iş.

### 3.5 Sık Yapılan Hatalar

| # | Hata | Doğrusu |
|---|------|---------|
| 1 | `main.css`'e import isteği | `main.css` YOK — giriş `Css/08_Devices/d-*.css` + `Css/auth-bundled.css` (CONTEXT §3.6) |
| 2 | Mockup okumadan katman dosyası düzenlemek | Adım 3 → PNG > ASCII > Inventory > Tokens |
| 3 | Cihaz dosyasını tek başına eklemek | Adım 7 → `devices.config.js` + `DeviceCssMap.php` birlikte |
| 4 | Ham hex/px'i katman dosyasına yazmak | Adım 8 → yalnız `01_Abstracts` içinde `--token` |
| 5 | `07_Vendors` içinde düzeltme yapmak | RED — salt okunur; sürüm işi Security onaylı |
| 6 | Aynı dosyayı 2. kez okumak ("emin olmak") | Adım 5 → ilk okumadan karar (kök `AGENTS.md` §5) |
| 7 | Subagent'ın commit atması | Adım 12 → rapor; commit orkestratörde |

**eli10 / eli15 (§3.5 bloğu):**

> **eli10 (basit):** En sık yapılan yedi hata ve doğrusu — hepsi yukarıdaki adımlara çıkar.
> **eli15 (detay):** Bu tablo kapıların neden var olduğunu örneklerle gösterir; içerdiği sayısal iddialar `CONTEXT.md` §3.6'daki disk ölçümüne bağlıdır. İçine yalnız tekrar eden gerçek hatalar girer. Okunması, işe başlarken aynı tuzağa düşmeyi engeller. Yeni hata türü görülünce tek satır eklenir; eski satırlar silinmez. 3 başarısız düzeltmede durulur ve tek soru sorulur.

---

## 4. Kurallar

| # | Kural | Neden var |
|---|-------|-----------|
| 1 | Faz 1 keşif onaysız kod yok · aynı dosya 2. kez okunmaz · 3 başarısız → DUR + 1 soru | Anti-overthink (kök `AGENTS.md` §4/§5) |
| 2 | Mockup okunmadan UI kodu yazılmaz (PNG > ASCII > Inventory > Tokens) | Kanonik görünüm (assets `AGENTS.md` §4 Zorunlu 1) |
| 3 | Yeni `.css`/`.js` dosyası şablondan türetilir (Guardrail #16) | Vault standardı |
| 4 | Framework/jQuery/React/Vue · inline style/script · `var`/`eval`/`innerHTML` yasak | ADR-001 + CSP (assets `AGENTS.md` §4) |
| 5 | Token yalnız `01_Abstracts/`; `07_Vendors` salt okunur; Font/Image onaysız değişmez | SSOT + karantina (assets `AGENTS.md` §4) |
| 6 | Cihaz dosyası ekleme/değişikliği → `devices.config.js` + `DeviceCssMap.php` eşzamanlı | Çift/eksik yüklenme (assets `AGENTS.md` §4 Zorunlu 4) |
| 7 | Hata → kök `AGENTS.md` §7 kurala dön → yeniden yaz → tekrar kontrol | Kör düzeltme döngüsü durur (`.ai/.rules/error-recovery.md` diskte YOK — §1 notu) |
| 8 | UI etkisi → browser testi (gerçek sayfa + mockup) | Görsel kanıt zorunlu (kök `AGENTS.md` §7) |
| 9 | **Commit subagent ATMAZ** | Tarih/entegrasyon orkestratörde (kök `AGENTS.md` §7/§8) |

> **eli10 (basit):** Bu kurallar görünümün şablona ve mockup'a bağlı, güvenli ve sırayla üretilmesini, commit'in tek elden atılmasını sağlar.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü süreç ile kod farklı hızda değişir; kod içine gömülse revizyonda kaybolur. Okunmaları, kapıların önceden bilinmesini sağlar. Yazmaları, denetimin tekrarlanmasını garantiler. Kural değişince (ADR/kök `AGENTS.md` revizyonu) yalnız bu tablo güncellenir.

---

## 5. Workflow

```text
GÖREV → [K1: CLAUDE+AGENTS+CONTEXT] → [K2: MOCKUP (PNG)] → [K3: ŞABLON (css|js-template)]
  → [K4: Css/<katman> | js/<modül> diff] → (cihaz ise [K5: devices.config.js + DeviceCssMap.php])
  → [K6: Vitest + Playwright] → (UI ise [K7: BROWSER — mockup karşılaştırma])]
  → [K8: RAPOR — commit yok] → ORKESTRATÖR → COMMIT (MO)
```

Adım detayı §3.1 · kapı detayı §3.2 · katman bazlı üretim `Css/WORKFLOW.MD` §5.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: workflow` |
| 2 | Bölüm sırası | §1–§7 sabit |
| 3 | Adım tablosu | Her adımda "Çıktı" + "Neden" + "Atlarsan ne olur" dolu (12 satır) |
| 4 | Commit kuralı | §3.1 #12 + §4 #9 "ATMAZ" ifadesi mevcut |
| 5 | Kapılar | K1–K8 tablosu + zincirle uyumlu |
| 6 | Placeholder | Dosyada doldurulmamış şablon değişkeni kalmadı (arama deseni: iki parantez + harf) |
| 7 | Wiki-link | Wiki-link biçimi (çift köşeli parantez) kullanıldı; hedefler diskte var (olmayanlar §1 notunda) |
| 8 | eli10 + eli15 | §3.1 (12) + §3.2 (8) + §3.3 + §4 bloklarında etiketli blok |
| 9 | Disk kanıtı | `vitest.config.js` + `playwright.config.ts` VAR · `package.json` YOK — doğrulandı |
| 10 | Dokunulmaz | `CLAUDE.md` / `AGENTS.md` / `Css/*` dokümanları + kaynak kod değiştirilmedi |
| 11 | Emoji | Dekoratif emoji yok |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör kuralları | [[CLAUDE.md]] | Uygulama kuralları (MEVCUT) |
| Klasör rolleri | [[AGENTS.md]] | Routing + yasaklar (MEVCUT) |
| Klasör context | [[CONTEXT.md]] | Envanter + §3.6 çelişki kaydı |
| Katman context | [[Css/CONTEXT.md]] | 11 katman + import zinciri (klasör içi SSOT) |
| Katman süreç | `Css/WORKFLOW.MD` | Katman üretim akışı (MEVCUT) |
| Kök master kurallar | [[../AGENTS.md]] | §1 kapı · §4 keşif · §5 anti-overthink · §7 loop |
| CSS şablonu | [[../.ai/.templates/frontend/css-template]] | Katman dosyası (Guardrail #16) |
| JS şablonu | [[../.ai/.templates/frontend/js-template]] | Modül dosyası (Guardrail #16) |
| Mockup indeksi | [[../.ai/ui-design/01-mockup-index]] | Mockup Before Frontend |
| Cihaz haritası | `../shared/src/Device/DeviceCssMap.php` | Eşzamanlılık hedefi (K5) |
| Cihaz yapılandırması | `assets.coremusic.net/js/devices.config.js` | Eşzmanlılık hedefi (K5) |
| Test yapılandırması | `assets.coremusic.net/vitest.config.js` · `playwright.config.ts` | K6 kapısı kanıtı |
| shared süreç | [[../shared/WORKFLOW.md]] | Ortak katman akışı |
| auth süreç | [[../auth.coremusic.net/WORKFLOW.md]] | `auth-bundled.css` tüketicisinin akışı |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-03 · **docType:** workflow
