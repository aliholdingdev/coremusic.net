---
title: "CoreMusic — Css/ Geliştirme Süreci"
type: guide
category: frontend
date: 2026-10-03
updated: 2026-10-03
version: 1.1.0
status: active
authority: "SSOT (klasör kökü) — adım kaynağı: css-template.md §5"
docType: workflow
---

# CoreMusic — Css/ Geliştirme Süreci

**docType:** workflow · **Katman:** L3 sunum (ITCSS) · **Sorumlu:** UI Designer (uygulama), MO (commit)

**Zorunlu Bağlantılar:** [[../../.ai/.templates/index]] · [[../../.ai/.templates/frontend/css-template]] · [[../../.ai/.templates/frontend/context-template]] · [[CONTEXT]] · [[CLAUDE]] · [[AGENTS]] · [[../../AGENTS.md]] · [[../../WORKFLOW]]

---

## 1. Amaç

Bu doküman `assets.coremusic.net/Css/` üzerinde bir CSS görevinin **adım adım akışını** tanımlar: not/mockup okumaktan commit'e kadar kapılar, şablon doldurma ve cihaz dosyası eşzamanlılık kuralı. Her adımın **gerekçesi ve atlama sonucu** §5.1 tablosundadır (context-template §3.5 — 4 soru kuralı). Adım kaynağı [[../../.ai/.templates/frontend/css-template]] §5; bu dosya css-klasöre özgü genişletmedir.

| Kapı | Yer | Açılmazsa |
|------|-----|-----------|
| Onay / APPROVAL | Kök AGENTS §1 (ASKING QUESTIONS kapıları) | Implementation başlamaz |
| Mockup Before Frontend | css-template §2 ön koşul | **DUR** — görsel okunamıyorsa kod yazılmaz |
| Guardrail #16 (şablon) | css-template §6 | Dosya reddedilir |
| QA denetimi | [[AGENTS]] §3.1 | Handover döngüsü — teslim sayılmaz |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Tek CSS görevinin uçtan uca akışı | Genel vault süreçleri → [[../../WORKFLOW]] |
| Cihaz dosyası ekleme/değişiklik eşzamanlılığı | JS modül akışı → [[js-template]] |
| Commit sorumluluğu (subagent atmaz) | Deploy/CI → DevOps |
| Doğrulama kapıları (guardrail + tarayıcı testi) | Veri/SQL görevleri |

- **Ön koşul:** repo kökünde `notes.md` ve `.ai/ui-design/` görseli erişilebilir.
- **Çıktı:** katmanına uygun `.css` (iskelet veya içerikli) + doğrulama raporu.

---

## 3. Mimari

### 3.1 Katman Seçim Tablosu (css-template §3.1 — karar anı)

| Soru | Cevap | Katman |
|------|-------|--------|
| Sadece `--token: değer` mi? | Evet | `01_Abstracts/` (`a-…-tokens.css`, cihaz dosyası seç) |
| Bare reset / yapısal iskelet mi? | Evet | `02_Base/` |
| Grid/header/footer/sidebar düzeni mi? | Evet | `03_Layout/` |
| Birçok sayfada tekrar eden parça mı (buton, kart, form, menü)? | Evet | `04_Components/` (`c-*.css`) |
| Tek bir PHP sayfasına özgü mü (`pages/**/*.php`)? | Evet | `05_Pages/` (`p-*.css`) |
| Tek amaclı sıfır mantık sınıf mı (`.is-hidden`)? | Evet | `06_Utilities/` (`u-*.css`) |
| Tekrarlanabilir yardımcı desen/makro mu? | Evet | `10_Helpers/` (`h-*.css`) |
| Cihaz kırılım davranışı mı (hover/touch/scrollbar)? | Evet | `08_Devices/` — **yalnız import + davranış** |
| Görünüm modu mu (home/pro/studio/car)? | Evet | `09_ViewModes/` (`v-*.css`) |
| 3. taraf kod mu? | — | `07_Vendors/` — **YAZMAZSIN** |

### 3.2 Şablon Seçim Tablosu (Guardrail #16 — css-template §5.3)

| Görev | Şablon bölümü |
|-------|---------------|
| Token dosyası | css-template §3.3 (`width` cihaz genişliği placeholder'ı, medya, `:root`) |
| Bileşen CSS'i | §3.4 (`block` placeholder'ı — BEM iskeleti) |
| Cihaz dosyası | §3.5 (`device` placeholder'ı — import zinciri + davranış) |
| Auth paketi | §3.6 (`auth-bundled` grup sırası) |
| Tema override | §3.7 (`[data-theme]` jeton override) |
| Context dokümanı | [[../../.ai/.templates/frontend/context-template]] (§3.4 kalıp + §3.5 5-sütun amaç alanı) |

### 3.3 Cihaz Eşzamanlılık Şeması (KRİTİK)

```
Css/08_Devices/d-{yeni}.css  ÜRET
        │
        ├─→  assets.coremusic.net/js/devices.config.js   (aynı görevde GÜNCELLE)
        └─→  shared/src/.../DeviceCssMap.php             (aynı görevde GÜNCELLE)
```

| Dosya | Ne eşleşir | Eşleşmezse |
|-------|------------|------------|
| `Css/08_Devices/d-*.css` | cihaz dosyası kendisi | — |
| `js/devices.config.js` | breakpoint → css yolu (frontend loader) | dosya var, **sessizce yüklenmez** |
| `DeviceCssMap.php` | cihaz → css yolu (server render) | sunucu **yanlış/eksik yolu** basar |

> Kural (css-template §3.5/§4.2): **device CSS ekleme/değişikliği → üçü birden**; dosya adı değişirse tüm `@import` + iki map + PHP docblock yorumları da güncellenir. Taşınan dosyalar (`_home-components` gibi) için `@import` sahipleri de güncellenir.

### 3.4 Kapı / Engel Tablosu (süreç içinde karşılaşılan durumlar)

| Durum | Aksiyon | Ref |
|-------|---------|-----|
| Mockup/ASCII okunamıyor | **DUR** + bildir — kod yazılmaz | Mockup Before Frontend |
| Vault ↔ disk çelişkisi | **disk kazanır** + `⚠️ VERIFICATION REQUIRED` | css-template §4.2 |
| Bilinmeyen dosya/seçici/API | `UNKNOWN` / `⚠️` yaz — tahmin yok | Zero-Hallucination |
| Guardrail #1–#6 ihlali (kodda) | revert + düzelt | css-template §4.1 |
| Aynı hatada **3 başarısız düzeltme** | **DUR** — şüpheli varsayımı söyle, 1 kısa soru sor | Kök AGENTS §5.5 |
| Kırık `@import` tespiti | RAPORLA (CONTEXT §3.4); 08_Devices salt okunur — kendin düzeltme | Bu doküman §4 #8 |

---

## 4. Kurallar

| # | Kapı | Kural | Neden bu kural | Ref |
|---|------|-------|----------------|-----|
| 1 | Görev başı | Kök `notes.md` okunur; uygulanan CSS notu `✓` imzalanır | Not unutulursa kullanıcı kararı kaybolur; `✓` uygulanmayan notu ayırır | css-template §4.2 |
| 2 | Görsel | `.ai/ui-design/` ilgili PNG okunmadan kod yazılamaz; okunamıyorsa DUR | Tahmini UI = israf + revizyon | Mockup Before Frontend |
| 3 | Şablon | Yeni `.css` ilgili css-template §3.x'ten üretilir | Tutarlı iskelet + guardrail'lerin gömülü olması | Guardrail #16 |
| 4 | Yazım | [[CLAUDE]] §4.1 — 10 guardrail 10/10 | İhlal = revert (gerekçeler: [[CLAUDE]] §4.1 "Neden var" sütunu) | css-template §4.1 |
| 5 | Taşıma | Dosya taşınır(sa): içerik BİREBİR + tüm `@import` + map dosyaları güncellenir | Yarı taşıma = sessiz kırık zincir | css-template §4.2 |
| 6 | Test | Değişiklik sonrası **gerçek sayfada** tarayıcı testi zorunlu | CSS spec: kırık import/eksik dosya **sessiz atlanır** — konsol/Network kanıtı şart | Kök AGENTS §7.7 |
| 7 | Commit | **Subagent commit ATMAZ** — orkestratöre aittir | Tek commit kalemi + audit trail | [[../../AGENTS.md]] §13.9 #5 |
| 8 | Kırık import | Yalnız **raporla** (CONTEXT §3.4) — `08_Devices/` salt okunur, yeni dosya yetkisi görev kapsamındadır | Kural değişmezliği: taşıma/yeni iskelet dışında dosya içi değişiklik YOK | Bu doküman §3.4 |

---

## 5. Workflow

```
NOTES.MD OKU → MOCKUP OKU → KATMAN SEÇ (§3.1) → ŞABLON KOPYALA (§3.2)
→ PLACEHOLDER DOLDUR → GUARDRAIL #16 DOĞRULA (css-template §6, 10/10)
→ [cihaz dosyası ise: devices.config.js + DeviceCssMap.php EŞZAMANLI]
→ TARAYICI TESTİ → RAPOR → COMMIT (MO)
```

1. **NOTES OKU:** kök `notes.md` → CSS ilgili notlar iş listesine.
2. **MOCKUP OKU:** `.ai/ui-design/00-mockup-index` → ekran spec + `.ai/.png/` (PNG > ASCII art). Okunamıyorsa **DUR + bildir**.
3. **KATMAN SEÇ:** §3.1 tablosu; şüphe varsa MO'ya sor (component mi sayfa mı ayrımı).
4. **ŞABLON KOPYALA:** §3.2 — Guardrail #16; şablonsuz dosya üretilmez.
5. **PLACEHOLDER DOLDUR:** `block`, `device`, `width`, `page`, `v` (css-template §5.4) — teslimde placeholder sözdizimi kalamaz.
6. **GUARDRAIL #16 DOĞRULA:** [[CLAUDE]] §4.1 (10/10) + css-template §6 kontrol listesi.
7. **CIHAZ DOSYASI İSE:** `08_Devices/d-*.css` üretildiyse/müdüyse → `js/devices.config.js` **+** `DeviceCssMap.php` aynı görevde (§3.3); ikisinden biri eksikse görev **TAMAMLANMIŞ SAYILMAZ**.
8. **TARAYICI TESTİ:** gerçek sayfada eleman/layout doğrulaması (Chrome DevTools MCP); kırık `@import`/eksik yüklenme raporlanır.
9. **RAPOR:** değişen/yeni dosya yolları, taşınan satır sayısı, doğrulama sonucu, bulgular (`⚠️ VERIFICATION REQUIRED` dahil).
10. **COMMIT:** MO atar — subagent atmaz.

### 5.1 Adım Gerekçeleri — Neden bu adım / Atlarsa ne olur (context-template §3.5)

| # | Adım | Neden bu adım? | Atlarsa ne olur? |
|---|------|----------------|------------------|
| 1 | NOTES OKU | Kullanıcı kararları yalnız `notes.md`'de; görev başı okunmazsa uygulanmamış karar kalır | Eski davranış sürer, karar kaybolur; `✓` imzası da atılmaz → tekrar iş |
| 2 | MOCKUP OKU | PNG gerçeği koddan önce gelir (PNG > ASCII > Inventory) | Uydurma UI → revert + revizyon döngüsü; DUR kapısı atlanırsa QA reddi |
| 3 | KATMAN SEÇ | Yanlış katman = yanlış ev (05'e component, 08'e yerleşim) | Guardrail #5/#8 ihlali → dosya taşınır, tekrar work; drift |
| 4 | ŞABLON KOPYALA | Guardrail #16: iskelet guardrail'leri ve § sırasını gömülü taşır | Şablonsuz dosya = reddedilir; standardsiz isim/katman sapması |
| 5 | PLACEHOLDER DOLDUR | Yarım şablon yarım davranış üretir | `{{`/`block` ham kalır → geçersiz CSS/red; §6 #3 ihlali |
| 6 | GUARDRAIL #16 DOĞRULA | 10 guardrailin toplu son kapısı — revert'ı en ucuz an | hardcoded/`!important`/inline kaçar → teknik borç + CSP riski |
| 7 | CİHAZ EŞZAMANLIK | 3 sistem (CSS, JS loader, PHP map) tek gerçek paylaşır | Dosya var ama **sessiz yüklenmez** ya da **çift yüklenir**; kullanıcı cihazında stil yok |
| 8 | TARAYICI TESTİ | CSS spec hataları konsolda değil **Network/sessiz atlamada** gizlenir | Kırık zincir üretimde kalır; QA kapısı RED |
| 9 | RAPOR | Zero-Hallucination kanıt zinciri — sonraki görev bu rapora güvenir | Sonraki görev yanlış envanterle başlar; uydurma/eksik bulgu zinciri kırılır |
| 10 | COMMIT (MO) | Tek kalem = temiz revert + audit (`log.md`) | Dağınık/eksik commit → geri alma imkânsız, çakışma |

**Adım eli10/eli15 blokları (§5.1'deki 10 adımın — neden bu adım, atlarsan ne olur):**

**1 — NOTES.MD OKU**
> **eli10 (basit):** İşe başlamadan önce not defteri okunur — içinde kullanıcının özel istekleri vardır.
> **eli15 (detay):** Bu adım var çünkü kararlar genelde oraya yazılır, akılda kalmaz. Atlarsan eski/yanlış istekle çalışılır, ayrıca uygulanan nota "✓" imzası atılmaz ve iş tekrar istenir. Dosya okunur, karar listeye alınır, sonra imzalanır.

**2 — MOCKUP OKU**
> **eli10 (basit):** Kod yazmadan önce ekranın resmine bakılır — tahmin değil, gerçeğe göre hareket edilir.
> **eli15 (detay):** Bu adım var çünkü görsiz kod = yanlış ürün, sonra hepsi silinir. Atlarsan "DUR" kapısı ihlal edilir, çıkan iş beğenilmez ve iş iki katına çıkar. PNG resim okunur, ölçüler not edilir, öyle kod yazılır.

**3 — KATMAN SEÇ**
> **eli10 (basit):** Yazılacak şey doğru kutuya konur: değer mi, buton mu, tek sayfa mı?
> **eli15 (detay):** Bu adım var çünkü yanlış kutuya yazılan şey sonra taşınmak zorunda kalır. Atlarsan guardrail ihlali (component 05'e, yerleşim 08'e) olur, kod geri alınır. Ayrım tablosuna bakılır, şüphe varsa yöneticiye sorulur.

**4 — ŞABLON KOPYALA**
> **eli10 (basit):** Dosya boş sayfadan değil, hazır kalıptan başlar — kurallar zaten içinde gömülüdür.
> **eli15 (detay):** Bu adım var çünkü kalıp 10 guardraili ve doğru bölüm sırasını taşır. Atlarsan şablonsuz dosya reddedilir (Guardrail #16), standart dışı isim/katman sapması çıkar. İlgili kalıp açılır, olduğu gibi kopyalanır.

**5 — PLACEHOLDER DOLDUR**
> **eli10 (basit):** Kalıptaki boşluklar gerçek adlarla doldurulur (parça adı, cihaz adı, tarih…).
> **eli15 (detay):** Bu adım var çünkü yarım kalıp yarım iş üretir. Atlarsan dosyada boşluk işaretleri kalır, CSS geçersiz olur ya da doküman reddedilir. Her boşluk gerçek değerle değiştirilir, sonra "boşluk kaldı mı" taranır.

**6 — GUARDRAIL #16 DOĞRULA**
> **eli10 (basit):** Son kontrol: on kurala uyuldu mu, dosya gerçekten hazır mı diye bakılır.
> **eli15 (detay):** Bu adım var çünkü hata ne kadar erken yakalanırsa düzeltmesi o kadar ucuz. Atlarsan yasaklı şeyler (ham ölçü, `!important`, inline stil) sızmış kalır ve kod revert edilir. Kontrol listesi 10/10 taranır, tek eksik varsa iş teslim edilmez.

**7 — CİHAZ EŞZAMANLILIĞI**
> **eli10 (basit):** Cihaz dosyası eklenince iki harita dosyası da hemen güncellenir — üçü bir takım.
> **eli15 (detay):** Bu adım var çünkü CSS'i bulan üç sistem var (tarayıcı yükleyicisi, JS haritası, sunucu haritası); biri unutulursa dosya var ama yüklenmez. Atlarsan kullanıcı cihazında stil görünmez ya da dosya iki kez yüklenir. Aynı görevde üçü birden güncellenir, biri eksikse iş bitmiş sayılmaz.

**8 — TARAYICI TESTİ**
> **eli10 (basit):** İş biterse gerçek sayfada gözle kontrol edilir — sadece dosyaya bakmak yetmez.
> **eli15 (detay):** Bu adım var çünkü kopuk bağlantılar (kırık import) sessizce atlanır, hata konsolda değil kullanıcıda görünür. Atlarsan üretimde bozuk sayfa kalır. Sayfa açılır, eleman/düzen kanıtla doğrulanır, kırık varsa rapora yazılır.

**9 — RAPOR**
> **eli10 (basit):** Ne değişti, ne buldun kısaca yazılır — sonraki iş bu yazılanlara güvenir.
> **eli15 (detay):** Bu adım var çünkü bilgi yazılmazsa kaybolur ve sonraki görev yanlış bilgiyle başlar. Atlarsan denetim zinciri kopar, uydurma/eksik rapor riski doğar. Dosya yolları, taşımalar, doğrulama sonucu ve bulgular listelenir.

**10 — COMMIT (MO)**
> **eli10 (basit):** Kaydetme işini tek kişi yapar — dağınık kalsın diye değil, düzenli dursun diye.
> **eli15 (detay):** Bu adım var çünkü tek kalemden kayıt temiz geri alma ve denetim izi sağlar. Atlarsan dağınık/eksik commit oluşur, hata geri alınamaz. Alt görev (subagent) hiç commit atmaz, raporu üstüne bırakır.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: workflow` |
| 2 | Bölüm | §1–§7, ≤3 başlık seviyesi |
| 3 | Akış | §5 zinciri 10 adım: notes → mockup → katman → şablon → placeholder → guardrail → (eşzamanlılık) → tarayıcı → rapor → commit |
| 4 | **Gerekçe** | §5.1 tablosu 10 satır — her adımda "neden + atlarsa" (context-template §3.5) |
| 5 | Katman seçimi | §3.1 karar tablosu 10 satır |
| 6 | Eşzamanlılık | `devices.config.js` + `DeviceCssMap.php` kuralı §3.3 + §5.7'de mevcut |
| 7 | Commit | "subagent atmaz" §4 #7 + §5.10'da mevcut |
| 8 | Placeholder | Üretilen `.css`/dokümanda placeholder sözdizimi kalmadı (şablon dosyaları hariç) |
| 9 | Halüsinasyon | Diskte olmayan dosya/adım iddia edilmedi |
| 10 | **eli10 + eli15 bloğu** | §5.1'deki 10 adım maddesinin altında `> **eli10 (basit):**` + `> **eli15 (detay):**` blokları var mı; "neden bu adım / atlarsan ne olur" eli15 içinde geçiyor mu |

---

## 7. Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| CSS ana şablon | [[../../.ai/.templates/frontend/css-template]] | §5 akış + §3.x şablonlar |
| Context şablonu | [[../../.ai/.templates/frontend/context-template]] | §3.5 4-soruluk amaç alanı |
| Kural özeti (klasör) | [[CLAUDE]] | 10 guardrail + gerekçe sütunu |
| Envanter (klasör) | [[CONTEXT]] | Disk sayıları + kırık import listesi |
| Rolleri (klasör) | [[AGENTS]] | Kim denetler, ne zaman devreye girer |
| Global workflow | [[../../WORKFLOW]] | Genel süreç kapıları |
| Agent registry | [[../../AGENTS.md]] | Routing + commit kuralı |
| Template registry | [[../../.ai/.templates/index]] | Guardrail #16 envanteri |
| JS şablonu | [[../../.ai/.templates/frontend/js-template]] | devices.config.js katman hizası |
| Mockup indeksi | [[../../.ai/ui-design/01-mockup-index]] | Adım 2 girdisi |
| Notlar | `notes.md` (repo kökü) | Adım 1 girdisi |

---

**Version:** 1.1.0 · **Last Updated:** 2026-10-03 · **docType:** workflow
