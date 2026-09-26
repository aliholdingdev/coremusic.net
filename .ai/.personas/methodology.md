---
title: "CoreMusic — Persona Test Metodolojisi (Seviye 1-4 · PREPARE → EXECUTE → REPORT · ADR-023 Coverage Gate)"
type: methodology
category: personas
version: 1.0.0
status: active
authority: reference
updated: 2026-09-26
---

# CoreMusic — Persona Test Metodolojisi

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[personas/research-bank]] · [[.templates/index]]

---

| Alan | Değer |
|------|-------|
| Dosya Adı | `methodology.md` |
| Dosya Yolu | `.ai/.personas/methodology.md` |
| Dosya Tipi | Test metodolojisi — persona test seviyeleri, test akışı, başarı eşikleri, ADR-023 gate'i; **karar DEĞİLDİR** (ADR değil) |
| Hedef Kitle | QA Engineer (persona-test), UI Designer (erişilebilirlik), DevOps Engineer (gate), persona üreten AI ajanları |
| Yapı | H1 + Zorunlu Bağlantılar + §1 Amaç → §7 Referanslar (7 bölüm + 7 alanlı frontmatter) |
| Test Seviyesi | **4** — Seviye 1 AI Rol Testi · Seviye 2 Browser MCP Canlı Test · Seviye 3 Playwright E2E · Seviye 4 Rapor |
| Test Akışı | **PREPARE → EXECUTE (COLLECT) → REPORT** (§3.1) |
| Veri Kaynağı | `[[personas/research-bank]]` paketleri **P5 · P6 · P7 · P8** — 27 satır taşındı (§6.2) |
| Karar Dayanağı | `[[ADR-023-persona-driven-testing]]` — 20 persona test matrisi · %90 satır+şart · kritik yollar %100 branş · PR'da gate |
| Eski İskelet | `coremusic.net.old/.ai/personas/methodology.md` (110 satır, **salt okunur**) — iskelet alındı, genişletildi ve research-bank ile doğrulandı |
| Zorunlu Blok | §4.0 Şablon-Önce Kural Bloğu (silinemez) |
| Kayıt Kuralı | Değişiklik Geçmişi **append-only** — mevcut satıra dokunulmaz |
| Dil | Türkçe (ç ğ ı İ ö ş ü doğru; mojibake YASAK); dosya/klasör adı ASCII |
| Authority | `reference` — bu dosya yalnızca **anlatır**; bağlayıcı karar ADR-023'tedir (SSOT self-claim yasak) |
| Governance | Red Team · Human Mode · Truth Mode |
| Versiyon | 1.0.0 (ilk üretim — 2026-09-26) |
| Son Kontrol | 2026-09-26 |

---

## §1 Amaç

Bu dosya, CoreMusic'in **68 kişilik persona envanterini** (`[[personas/index]]` — 6 grup) ve **`[[ADR-023-persona-driven-testing]]` kararını** aynı test hattında birleştiren **test metodolojisidir**: her persona hangi seviyede (AI rolü / canlı tarayıcı / E2E otomasyon / rapor) nasıl test edilir, akışın adımları nelerdir, başarı eşikleri hangi doğrulanmış kaynaklardan gelir ve test çıktısı ADR-023'ün coverage kapısına nasıl girer — hepsi burada tanımlıdır. Persona *içeriği* `[[personas/persona-template]]`'ten, *verisi* `[[personas/research-bank]]`'ten, *eşiği* buradan okunur; **üçü karıştırılmaz**.

| Boyut | Değer |
|-------|-------|
| Ne taşır | 4 test seviyesi, PREPARE → EXECUTE → REPORT akışı, seviye/persona-alanı eşleşmesi, emülasyon ayarları (viewport · tema · network · geolocation), doğrulanmış başarı eşikleri (P8/P5/P6/P7), ADR-023 gate uygulaması |
| Ne taşımaz | Persona profili (→ `[[personas/persona-template]]`), araştırma bulgularının tamamı (→ `[[personas/research-bank]]`), mood küme adları (→ `[[personas/mood-taxonomy]]`), senaryo eşlemesi (→ `[[personas/test-scenarios-mapping]]`), karar (→ `[[ADR-023-persona-driven-testing]]`) |
| Neden var | Eski vault iskeleti (110 satır) 4 seviyeyi ve akışı tanımlıyordu ama **kaynak/eşik/coverage bağı yoktu**; ADR-023 ise eşiği ve gate'i tanımlarken **seviye uygulamasını** tanımlamıyordu → bu dosya iki ucu bağlar |
| Kim okumalı | Test çalıştıran/yazan her ajan (QA Engineer öncelikli); denetimde Vault Steward |
| Ne zaman yazıldı | 2026-09-26 — research-bank (P1-P8) ve ADR-023 sonrası veri temeliyle |
| Kanıt zinciri | `research-bank` (veri) → `persona-template` (alan + etiket) → persona dosyası → **bu dosya (test)** → `ADR-023` (gate) |

### §1.1 Bu Dosya Ne İçin — Ne İçin Değil

| İhtiyaç | Doğru Dosya | Bu Dosya Kullanılmaz |
|---------|-------------|----------------------|
| Persona test seviyeleri ve akışı | ✅ Bu dosya | — |
| Başarı eşikleri (LCP/INP/CLS/WCAG) | ✅ Bu dosya (research-bank'tan taşınmış) | Yeni web araştırması yapılmaz |
| Persona profili / alan havuzu | `[[personas/persona-template]]` | ❌ |
| Gerçek-dünya veri + kaynak durumu | `[[personas/research-bank]]` | ❌ |
| Coverage eşiği / PR gate kararı | `[[ADR-023-persona-driven-testing]]` | ❌ (burada yalnız **uygulanır**) |
| PHPUnit/Playwright test kodu | `[[.templates/testing/phpunit-template]]` + kod ağacı | ❌ |

**Ayırıcı test:** "Bu içerik bir test **adımı/eşiği/akışı** mı?" → Evet ise bu dosya. "Bir **karar** mı?" → ADR. "Bir **kişi** mi?" → persona-template.

---

## §2 Kapsam

### §2.1 Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 4 test seviyesi tanımı (araç · çıktı · sorumlu) | Persona dosyasının kendisi (→ `[[personas/persona-template]]`) |
| PREPARE → EXECUTE → REPORT akışı + 10+ EXECUTE adımı | Mood taksonomisi ve UI etkileri (→ `[[personas/mood-taxonomy]]`) |
| Emülasyon ayarları: viewport · tema · network · geolocation | 6 senaryo × grup eşleme matrisi (→ `[[personas/test-scenarios-mapping]]`) |
| Doğrulanmış başarı eşikleri + Lighthouse ağırlıkları (research-bank P8/P5/P6/P7) | Araştırma süreci ve kaynak taraması (→ `[[personas/research-bank]]`) |
| ADR-023 gate'inin seviye bazlı uygulanışı (%90 / %100 branş / PR) | Gate'in kendisi, CI iş akışı değişikliği, eşiği değiştirmek (→ ADR-023, frozen YOK ama değişiklik = yeni ADR) |
| Rapor formatı ve teslim çıktıları | Test kodu (PHPUnit/Playwright dosyaları) — kod ayrı domaindir |

*Alt konular:* seviye tanımı → akış → persona-alanı eşleşmesi → emülasyon → eşikler → gate → doğrulama.
Kapsam dışı için: persona → `[[personas/persona-template]]`, veri → `[[personas/research-bank]]`, karar → `[[ADR-023-persona-driven-testing]]`.

### §2.2 Dört Test Seviyesi Tablosu

| Seviye | Araç | Çıktı | ADR-023 Coverage Katkısı | Sorumlu |
|--------|------|-------|--------------------------|---------|
| **Seviye 1 — AI Rol Testi** | LLM + persona dosyasındaki **AI Rol Kartı** (`Sen ...'sin` paragrafı) | Davranış notları, karar tutarlılık listesi, **senaryo taslağı** (happy / error / boundary) | Matris satırının (20 persona) **birim + entegrasyon senaryosu taslağı** buradan çıkar; `@group persona-NN` etiketinin kaynağı | QA Engineer (persona-test) |
| **Seviye 2 — Browser MCP Canlı Test** | Chrome DevTools MCP / CDP: `Emulation.setDeviceMetricsOverride`, `Network.emulateNetworkConditions`, konsol, trace, screenshot, Lighthouse audit | Canlı test raporu (markdown), screenshot seti, performans metrikleri (FCP/LCP/TBT/CLS/SI), Lighthouse skorları, konsol log'ları | Testin **gerçek davranış kanıtı**; bulgu → yeni birim/entegrasyon testi talebi (PR'daki coverage'ı üreten iş akışı) | QA Engineer + UI Designer (a11y) |
| **Seviye 3 — Playwright E2E Otomasyon** | Playwright: `playwright.devices`, `page.setViewportSize()`, `use: { offline: true }`, CDP ağ emülasyonu; headless/headed; CI | Spec dosyası (`*.spec.*`), CI çıktısı, regression raporu | **Doğrudan coverage üretir**: PR'daki coverage gate'i bu testlerin satır+şart katkısıyla ölçülür; kritik akış E2E şartı (ADR-023 §5.4/2) | QA Engineer + DevOps Engineer |
| **Seviye 4 — Rapor (COLLECT + REPORT)** | Markdown + tablo/CSV (Excel hedefi); `vault-utf8-writer` ile vault'a yazım | Test raporu + öneri/aksiyon listesi + özet tablo (tüm personalar tek tabloda) | Gate **girdisi ve kanıtı**: hangi persona satırı kapandı, kalan boşluklar (`⚠️ VERIFICATION REQUIRED`) | QA Engineer + MO (log/vault kaydı) |

> **Kural:** Seviye 1 tek başına **yeterli değildir** (kağıt testi); ADR-023'ün coverage'ı Seviye 3'te ölçülür. Seviye 2, Seviye 3'ün senaryosunu **canlı doğrular**. Seviye 4 olmadan satır **kapanmaz**.

### §2.3 Hedef Kitle

| Kitle | Bu Dosyayı Nasıl Kullanır |
|-------|---------------------------|
| QA Engineer | Seviye 1-3'ü sırayla çalıştırır; eşikleri §3.4'ten okur; gate'i §5.3'e göre raporlar |
| UI Designer | Seviye 2'de a11y adımlarını (WCAG §3.4.2) denetler |
| DevOps Engineer | Seviye 3 çıktısını `ci.yml` coverage adımına bağlar (ADR-023 §5.1/5) |
| Persona üreten ajan | Test Adımları tablosunu (persona-template §3.5.10) bu seviye eşlemesiyle yazar |
| Vault Steward | §6.1 kontrol listesi + §6.2/§6.3 metrikleriyle denetler |

### §2.4 Kapsam Dışı İstisnalar

| Durum | Ne Yapılır |
|-------|-----------|
| Persona'da olmayan bir alan testte isteniyor (ör. cihaz spec'i eksik) | `[[personas/research-bank]]` P3'e bak; yoksa `⚠️ VERIFICATION REQUIRED` — uydurma spec yazılmaz |
| Test eşiği research-bank'ta yok | DUR → yalnız `⚠️ DERIVED` + dayanak satırıyla yazılır (X-10 kuralı) |
| Yeni seviye (Seviye 5) eklenmek isteniyor | Bu dosya **In-Place** genişletilir (dosya adı değişmez); seviye = strateji kararıysa **yeni ADR** (`ADR-088+`) |
| ADR-023 eşiği değişmek isteniyor | Bu dosyada değiştirilemez → ADR-023 Arch Lead onayı + `log.md` append ya da yeni ADR |
| Test raporu `.ai/` dışında üretilmek isteniyor | DUR → MO onayı; rapor yolu §5.4'te `⚠️ VERIFICATION REQUIRED` olarak işaretlidir |

---

## §3 Mimari — Test Akışı ve Eşleşmeler

### §3.0 Dizin ve Bağımlılık Ağacı

```text
.ai/.personas/
├── index.md                    → 68 persona / 6 grup kataloğu (DOKUNULMAZ)
├── mood-taxonomy.md            → mood küme adları (DOKUNULMAZ)
├── test-scenarios-mapping.md   → senaryo × grup matrisi (DOKUNULMAZ)
├── research-bank.md            → P1-P8 veri (66 bulgu) — bu dosyanın veri kaynağı (DOKUNULMADI)
└── methodology.md              → BU DOSYA (test seviyeleri + akış + eşikler + gate)
    ├── §1 Amaç
    ├── §2 Kapsam → 4 seviye tablosu
    ├── §3 Mimari → akış · eşleşme · emülasyon · eşikler
    ├── §4 Kurallar → Şablon-Önce + kaynak etiketleme + ≥500
    ├── §5 Workflow → seviye adımları + ADR-023 gate
    ├── §6 Doğrulama → kontrol + taşınan bulgu metriği + EXCLUDED atıf
    └── §7 Referanslar → wiki-link + Değişiklik Geçmişi

Bağımlılık zinciri:
research-bank (veri) → persona-template (alan+etiket) → persona dosyası → methodology (bu dosya: test) → ADR-023 (gate)
```

| Varlık | Rol | Sorumlu |
|--------|-----|---------|
| `persona-template` §3.5 (11 alan) | Test girdisinin şekli (neyi test edeceğiz) | QA Engineer + UX Researcher |
| `research-bank` P5/P6/P7/P8 | Test girdisinin değeri (hangi eşik geçerli) | research-agent → okuyan: QA |
| `methodology` (bu dosya) | Test süreci (nasıl, hangi seviyede, hangi akışla) | QA Engineer |
| `ADR-023` | Test kapısı (kaç %, nerede merge block) | Vault Steward + DevOps |
| `test-scenarios-mapping` | Senaryo × grup önceliği | QA Engineer |

### §3.1 Test Akışı — PREPARE → EXECUTE → REPORT

```text
1. PREPARE
   ├── Persona dosyasını oku (11 alanın hepsi dolu mu? §3.2)
   ├── AI Rol Kartı'nı hazırla (Seviye 1 girdisi; `{{...}}` kalmamış olmalı)
   ├── Cihaz / viewport ayarla (P3 spec + ⚠️ DERIVED viewport + §3.3.1 breakpoint)
   ├── Tema uygula (light / dark — Playwright `colorScheme`, CDP emülasyon)
   ├── Network throttling ayarla (P8: 150 ms · 1.6 Mbps / 750 Kbps ya da persona hızı)
   └── Geolocation ayarla (persona şehir/semt verisi — §3.3.4, API adı ⚠️ VERIFICATION REQUIRED)

2. EXECUTE  (≥10 adım — §3.1.2; her adımda COLLECT çalışır)
   ├── Adım 1: Sayfa yükleme → FCP, LCP, TBT, CLS ölç
   ├── Adım 2: Login / kayıt akışı (KVKK yaş sınırı — §3.4.3)
   ├── Adım 3: Home page → görsel/animasyon kontrolü
   ├── Adım 4: Müzik araması → persona DNA'sına göre sonuç
   ├── Adım 5: SPA navigasyon → sayfalar arası geçiş süresi
   ├── Adım 6: Player kontrol → play / pause / skip / volume
   ├── Adım 7: Playlist işlemleri → oluştur / düzenle / sil
   ├── Adım 8: Profil & settings → tercihlerin kalıcılığı
   ├── Adım 9: Hata sayfaları → 404 / 403 / 500
   └── Adım 10: Erişilebilirlik → Lighthouse a11y audit (WCAG §3.4.2)

3. COLLECT  (EXECUTE ile eşzamanlı)
   ├── Screenshot (her adım)
   ├── Console log'ları (hata/uyarı → 0 hedef)
   ├── Performans metrikleri (FCP, LCP, CLS, TBT, SI)
   ├── Lighthouse skorları (Performance, A11y, Best Practices, SEO)
   └── Network waterfall

4. REPORT
   ├── Markdown raporu (biçim: §5.4)
   ├── Özet tablo / CSV (tüm personalar tek tabloda)
   ├── Öneri / aksyon listesi
   └── log.md append + ADR-023 matrisinde satır durumu
```

#### §3.1.1 PREPARE — Hazırlık Adımları

| # | Adım | Kaynak | Çıktı |
|---|------|--------|-------|
| P1 | Persona dosyasını oku; 11/11 alan dolu mu denetle | `[[personas/persona-template]]` §3.5 | Test girdisi listesi |
| P2 | AI Rol Kartı'nı al; `{{...}}` yer tutucusu kalmamış mı bak | persona-template §3.5.9 | Seviye 1 prompt'u |
| P3 | Cihaz spec'ini al → viewport'u türet | research-bank P3 (`⚠️ DERIVED`) | Viewport W×H + DPR |
| P4 | Tema seç (light/dark) | persona Cihaz & Teknoloji satırı | Tema değeri |
| P5 | Ağ koşulu seç (persona hızı ya da P8 mobil preset) | research-bank P8.3 | Throttling profili |
| P6 | Konum/izin senaryosu belirle | persona Kimlik Kartı (şehir/semt) | Geolocation senaryosu |
| P7 | Temiz profil / temiz veri başlangıcı | test senaryosu | Deterministik koşu |

#### §3.1.2 EXECUTE — Zorunlu Adımlar (≥10)

| # | Adım | Beklenen | Doğrulama yöntemi |
|---|------|----------|-------------------|
| 1 | Sayfa yükleme | LCP ≤ **2500 ms** (§3.4.1), mood'a uygun içerik üstte | Trace + screenshot |
| 2 | Login / kayıt akışı | Türkçe karakterli ad kabul; 16 yaş altı → veli onayı akışı (§3.4.3) | Manuel + form assertion |
| 3 | Home page | Görsel/animasyon hatası yok, konsol temiz | Console + screenshot |
| 4 | Müzik araması | Persona DNA'sına göre sonuç (tür/sanatçı) | İçerik assertion |
| 5 | SPA navigasyon | Sayfalar arası geçiş akıcı, müzik kesintisiz | Trace + console |
| 6 | Player kontrol | play/pause/skip/volume + klavye erişimi | Erişilebilirlik audit |
| 7 | Playlist işlemleri | Oluştur / düzenle / sil; yetki hataları anlamlı | DOM assertion |
| 8 | Profil & settings | Tercihler kalıcı (yeniden yükleyince korunur) | Storage kontrolü |
| 9 | Hata sayfaları (404/403/500) | Türkçe mesaj + dönüş yolu | Manuel + screenshot |
| 10 | Erişilebilirlik audit | WCAG 2.2 AA eşikleri (§3.4.2) | Lighthouse / axe |
| 11 | Bağlantı yavaşlatma | Throttling altında hata yok (§3.3.3) | Ağ emülasyonu + trace |

*Asgari 10 satır; satır 11 şarta bağlıdır. Her satır 3 sütun zorunludur: Adım · Beklenen · Doğrulama yöntemi.*

#### §3.1.3 REPORT — Çıktılar

| Çıktı | İçerik | Yer |
|-------|--------|-----|
| Test raporu (markdown) | Seviye, cihaz, viewport, tema, network, adım tablosu, metrikler, Lighthouse, konsol, öneriler | `.ai/reports/` — yol onayı `⚠️ VERIFICATION REQUIRED` (§5.4) |
| Özet tablo (CSV/Excel) | Tüm personalar tek tabloda: persona · seviye · durum · LCP · a11y · hata | rapor eki |
| ADR-023 matris satırı | 20 satırdan hangisi kapandı (birim + entegrasyon + H/E/B) | `log.md` append + matris notu |
| Öneri/aksiyon listesi | Yeni test talebi, persona düzeltme, gate boşluğu | rapor bölümü |

### §3.2 Persona Dosyası Alanlarıyla Eşleşme

| # | persona-template Alanı (§3.5) | Testte Kullanımı | Seviye |
|---|-------------------------------|------------------|--------|
| 1 | Kimlik Kartı | Yaş → KVKK/onay akışı senaryosu; şehir/semt → konum/yerellik adımı | 1 · 2 |
| 2 | Fiziksel Özet (5-8 satır) | UI'ı etkileyen kısıt (gözlük → font, parmak → dokunma hedefi) | 2 |
| 3 | Big Five (OCEAN) | Karar/tarz tutarlılığı — puan **kurgusal + ⚠️ DERIVED yöntem** (§3.4.4) | 1 |
| 4 | Mood Profili | Karşılama/öneri sırası, boş durum metni beklentisi | 1 · 2 |
| 5 | Müzik DNA'sı | Arama/keşif senaryosunun beklenen sonuçları (tür/sanatçı) | 1 · 2 · 3 |
| 6 | Cihaz & Teknoloji | **Viewport, DPR, OS, tarayıcı, internet hızı, tema** → emülasyon girdisi (§3.3) | 2 · 3 |
| 7 | Erişilebilirlik / Kısıt | WCAG kriterleri ve eşikler (§3.4.2) → audit kapsamı | 2 · 3 |
| 8 | Kişilik & Davranış | Sabır eşiği, hata tepkisi → bekleme/hata adımlarının yorumu | 1 |
| 9 | AI Rol Kartı | **Seviye 1'in prompt'u** — birebir gömülür | 1 |
| 10 | Test Adımları (≥10) | EXECUTE adım listesinin çekirdeği (§3.1.2 ile eşleştirilir) | 1 · 2 · 3 |
| 11 | Kaynak & Doğrulama | Testte kullanılacak her iddianın etiketi; etiketsiz iddia test edilmez | Hepsi |

> **Eşleşme kuralı:** bir persona alanı **boşsa** o adım `yok` olarak yazılır ve atlanmaz; **etiketsizse** (§4.5) o adım çalıştırılmaz, `⚠️ VERIFICATION REQUIRED` ile raporlanır.

### §3.3 Emülasyon Ayarları (viewport · tema · network · geolocation)

#### §3.3.1 Viewport

| Kural | Değer | Kaynak / Durum |
|-------|-------|----------------|
| Spec → CSS viewport | Çözünürlük ÷ DPR (ör. 1080×2340 ÷ 3 → **≈ 360×780**; ÷ 2.625 → **≈ 412×915**) | research-bank P3 — **`⚠️ DERIVED`** |
| Seviye 2 ayarı | CDP `Emulation.setDeviceMetricsOverride` (persona viewport + DPR) | research-bank P3 · persona-template §3.5.6 |
| Seviye 3 ayarı | `playwright.devices` registry ya da `page.setViewportSize()` | research-bank P8 — `VERIFIED` |
| Test breakpoint'leri | 320×568 · 375×667 · 375×812 · 414×896 · 768×1024 · 1280×720 · 1600×1200 · 1920×1080 · 2560×1440 | research-bank P8.5 — `VERIFIED` |
| EXCLUDED cihazlar | Redmi Note 12 · iPhone 11/12 · Moto G spec'leri **doğrulanmadı** → bu cihazlarla test önce ek tur (X-1) | research-bank §6.4 |

#### §3.3.2 Tema

| Ayar | Değer | Kaynak |
|------|-------|--------|
| Kaynak satır | persona "Cihaz & Teknoloji → Tema" (`light` / `dark`) | persona-template §3.5.6 |
| Seviye 3 uygulaması | Playwright `colorScheme` alanı (`playwright.devices` alan listesi içinde) | research-bank P8 — `VERIFIED` |
| Doğrulama | Her iki temada da adımlar 1-10 tekrarlanır; konsol hatası 0 | §3.1.2 |

#### §3.3.3 Network (Throttling)

| Ayar | Değer | Kaynak / Durum |
|-------|-------|----------------|
| Mobil laboratuvar preset | Gecikme **150 ms** · indirme **1.6 Mbps** · yükleme **750 Kbps** · paket kaybı yok | research-bank P8.3 — `VERIFIED` |
| Karşılık gelen bağlantı | ~%85 persentil mobil bağlantı; Lighthouse'ta **"Slow 4G"** (eski adı "Fast 3G") | research-bank P8.3 — `VERIFIED` |
| DevTools preset'leri | Slow 3G · Fast 3G · Slow 4G · Fast 4G (+ özel profil) | research-bank P8.3 — `VERIFIED` |
| Seviye 3 ağ emülasyonu | CDP `Network.emulateNetworkConditions` — örnek: `downloadThroughput: 500*1024/8`, `uploadThroughput` aynı, `latency: 400` | research-bank P8.4 — `VERIFIED` |
| Offline | Playwright `use: { offline: true }` | research-bank P8.4 — `VERIFIED` |
| Persona hızı | persona satırındaki hız (ör. 35 Mbps ADSL) ile preset **farklıysa** özel profil kullanılır | persona verisi + `⚠️ DERIVED` (X-10) |
| Uyarı | Ağ simülasyonu **simülasyondur**; raporda "gerçek 4G hızı" denmez → `⚠️ VERIFICATION REQUIRED` | research-bank P8.7 |

#### §3.3.4 Geolocation

| Kural | Değer | Durum |
|-------|-------|-------|
| Girdi | persona Kimlik Kartı'ndaki şehir/ilçe/semt (kurgusal) | persona-template §3.5.1 |
| Senaryo | Konum izni istenen adımlar (yerellik/keşif) — izin ver / reddet iki yol da test edilir | bu dosya §3.1.2 |
| Teknik API | CDP geolocation emülasyon API'sinin adı **bu turda araştırılmadı** | **`⚠️ VERIFICATION REQUIRED`** (research-bank P8'de yok) |
| Kişisel veri | Konum gerçek kullanıcı verisi değildir; gerçek konum/log **yazılmaz** (KVKK §4.7) | `Kaynak: kurgusal (persona verisi)` |

### §3.4 Başarı Eşikleri (research-bank'tan taşındı)

#### §3.4.1 Core Web Vitals (75. yüzdelik — `VERIFIED`)

| Metrik | İyi | Kötü | Kaynak |
|--------|-----|------|--------|
| **LCP** | ≤ **2500 ms** | > 4000 ms | research-bank P8 — `VERIFIED` |
| **INP** | ≤ **200 ms** | > 500 ms | research-bank P8 — `VERIFIED` |
| **CLS** | ≤ **0.1** | > 0.25 | research-bank P8 — `VERIFIED` |
| **FCP** | iyi eşiği **1 saniye** (Lighthouse v8 score eğrisi buna hizalı) | — | research-bank P8 — `VERIFIED` |
| **TBT** | **> 50 ms** long task main thread'i bloklar | — | research-bank P8 — `VERIFIED` |

**Lighthouse v8 performans skoru ağırlıkları (`VERIFIED`):** LCP **25** · TBT **30** · CLS **15** · FCP **10** · Speed Index **10** · TTI **10**.

| Uyarı | Kural |
|-------|-------|
| Sürüm duyarlılığı | Skor formülü **sürüm duyarlıdır**; v8 baz alınmıştır → sürüm yazılmadan "skor = X" iddiası yapılmaz (P8.7) |
| Yüzdelik | Eşikler **75. yüzdelik** alanınadır; persona'ya özel hedef = `⚠️ DERIVED` + dayanak (X-10) |

#### §3.4.2 WCAG 2.2 AA (Seviye 2/3 audit — `VERIFIED`)

| Kriter | Başlık | Eşik | Not |
|--------|--------|------|-----|
| 1.4.3 | Contrast (Minimum) | Metin ≥ **4.5:1**; büyük metin ≥ 3:1 | AA |
| 1.4.11 | Non-text Contrast | UI bileşenleri/grafikler ≥ **3:1** | AA |
| 2.4.7 | Focus Visible | AA | Odak görünür olmalı |
| 2.4.11 | Focus Not Obscured (Minimum) | AA | **WCAG 2.2 YENİ** |
| 2.5.7 | Dragging Movements | AA | **WCAG 2.2 YENİ** |
| 2.5.8 | Target Size (Minimum) | İşaretçi hedefi ≥ **24×24 CSS px** (5 istisna) | **WCAG 2.2 YENİ** |
| 3.3.8 | Accessible Authentication (Minimum) | AA | **WCAG 2.2 YENİ** |

*Uyarı (P5.5): bu turda tek tek açılmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı) test metnine numara olarak girerse `⚠️ VERIFICATION REQUIRED` (X-7). **AAA, CoreMusic hedefi değildir** → "AAA uyumlu" iddiası yazılmaz. Renk kontrastı **hesaplanacak** değerdir (`⚠️ DERIVED`); eşikler VERIFIED'tir.*

#### §3.4.3 KVKK / Yaş Sınırı (Seviye 1-2 akış kuralı)

| Rejim / Madde | Değer | Kaynak |
|---------------|-------|--------|
| KVKK 6698 m.5/1 | "Kişisel veriler ilgili kişinin açık rızası olmaksızın işlenemez." | research-bank P6 — `VERIFIED` |
| TMK m.11 (erginlik) | **18 yaş** | research-bank P6 — `VERIFIED` |
| COPPA (ABD) | rıza yaşı **13** | research-bank P6 — `VERIFIED` |
| GDPR (AB) m.8 | **16** (üye devletler **13'e** kadar indirebilir) | research-bank P6 — `VERIFIED` |
| **CoreMusic sonucu** | **16 yaş altı persona → veli/onay akışı test edilir** (`veli_onayı_gerekli: true`) | research-bank P6 — **`⚠️ DERIVED`** (politika sonucu; karar kalıcıysa `ADR-088+`) |

*Yasak ifade: "KVKK 16 diyor" — bu GDPR m.8 değeridir, **yanlıştır** (P6.5). Türkiye'de çocuk için özel rıza yaşı düzenlemesi **yoktur**.*

#### §3.4.4 Big Five (Seviye 1 tutarlılık ölçütü)

| Kural | Değer | Durum |
|-------|-------|-------|
| Ölçek künyesi | **IPIP-NEO-120** (Johnson 2014) — 120 madde · 5 domain · 30 facet · facet başına 4 madde | research-bank P7 — `VERIFIED` |
| Boyutlar | O/C/E/A/N (5 boyut) | research-bank P7 — `VERIFIED` |
| Cronbach alfa | Openness **0.81** · Conscientiousness **0.90** · Extraversion **0.89** · Agreeableness **0.86** · Neuroticism **0.90** | research-bank P7 — `VERIFIED` |
| Puanlama | 0-100 normalize = **resmi ölçek değil** → persona puanı kurgusal + **`⚠️ DERIVED`** yöntem notu | research-bank P7 — **`⚠️ DERIVED`** |
| Test kullanımı | Seviye 1'de **davranış tutarlılığı** ölçülür; puan "bilimsel sonuç" diye sunulmaz | bu dosya |

#### §3.4.5 Eski Vault İç Hedefleri (uyumlu, ama doğrulanmadı)

> Eski `methodology.md`'deki hedefler P8 eşikleriyle **çelişmez (daha sıkıdır)** ama research-bank'ta **yer almaz** → **`⚠️ DERIVED`** (X-10: persona testine özel hedef = türetilmiş; dayanak P8).

| Metrik | İç Hedef | Kritik Eşik | P8 Doğrulanmış Eşik |
|--------|----------|-------------|---------------------|
| FCP | < 1.0 s | < 1.5 s | iyi 1 s (`VERIFIED`) |
| LCP | < 1.5 s | < 2.5 s | ≤ 2500 ms iyi / > 4000 ms kötü (`VERIFIED`) |
| CLS | < 0.05 | < 0.1 | ≤ 0.1 / > 0.25 (`VERIFIED`) |
| TBT | < 100 ms | < 300 ms | > 50 ms long task (`VERIFIED`) |
| SI | < 1.5 s | < 3.0 s | Lighthouse v8 ağırlığı 10 (`VERIFIED`) |
| Lighthouse Performance | 95+ | 80+ | ağırlıklar §3.4.1 (`VERIFIED`) |
| Lighthouse A11y | 95+ | 90+ | WCAG §3.4.2 (`VERIFIED`) |
| Console Errors | 0 | 0 | — (test kuralı) |
| SPA navigasyon süresi | < 100 ms | < 300 ms | — (`⚠️ DERIVED`, P8'de yok) |

**Öncelik:** raporda **P8 eşiği esastır**; iç hedef yalnızca dahili uyarı olarak kullanılır ve `⚠️ DERIVED` etiketi taşır.

### §3.5 Anti-Pattern Tablosu

| # | Anti-Pattern | Neden Kötü | Doğrusu |
|---|--------------|-----------|---------|
| 1 | Seviye 1'i tek başına "test tamamlandı" saymak | Coverage üretmez (ADR-023 gate'siz) | Seviye 3 + Seviye 4 zorunlu |
| 2 | Eşiği araştırıp yeni sayı eklemek | Hallüsinasyon (ADR-005) | research-bank P5/P6/P7/P8'den al; yoksa `⚠️ DERIVED`/`⚠️ VERIFICATION REQUIRED` |
| 3 | Viewport'u "üretici viewport" diye sunmak | Türetilmiş değer | `⚠️ DERIVED` + çözünürlük÷DPR dayanağı (P3) |
| 4 | Konum/API adlarını uydurmak (geolocation CDP methodu) | Doğrulanmamış teknik iddia | `⚠️ VERIFICATION REQUIRED` (§3.3.4) |
| 5 | Persona alanını atlayarak test etmek | Yanlış senaryo | 11 alan eşleşmesi (§3.2); boş alan `yok` |
| 6 | Throttling'i "gerçek 4G" diye raporlamak | Simülasyon ≠ ölçüm (P8.7) | "simüle edilmiş Slow 4G (150 ms · 1.6/750)" yaz |
| 7 | EXCLUDED iddiayı test sonucuna veri yapmak | Kırık veri (X-1..X-10) | §6.3 listesi: iddia yazılmaz ya da `⚠️ VERIFICATION REQUIRED` |
| 8 | Raporu eski vault yoluna yazmak | Kırık yol (eski `.ai/sessions/` bu vault'ta YOK) | §5.4 — yol MO onaylı, `⚠️ VERIFICATION REQUIRED` |
| 9 | Dosya adını `-v2` yapmak / 500 altı yazmak | In-Place + derinlik ihlali | §4.6 · §4.8 |
| 10 | PowerShell ile yazmak | Windows-1254/mojibake | `vault-utf8-writer` (§4.3) |

---

## §4 Kurallar

### §4.0 ZORUNLU: ŞABLON ÖNCE (Template-First)

**Herhangi bir dosyayı yazmadan ÖNCE `.ai/.templates/` dizinindeki ilgili şablonları oku:**

1. Uygun şablonu `[[.templates/index]]` (`.ai/.templates/index.md`) §7.1 tablolarından seç.
2. Şablonu oku; `{{VARIABLE}}` alanlarını doldur, gereksiz bölümleri kaldır.
3. **Şablon varsa ona göre yaz.**
4. **Şablon yoksa** standart 8-bölüm formatına göre yaz ve `⚠️ VERIFICATION REQUIRED` notuyla `log.md`'ye şablon eksiğini bildir.
5. Şablon okunmadan yazılan dosya **geçersizdir** (Guardrail #16): revert edilir, `log.md`'ye ERROR girilir.

| Durum | Aksiyon |
|-------|---------|
| `.ai/.templates/` altında ilgili şablon VAR | Şablonu oku → ona göre yaz |
| Şablon YOK | Standart formata göre yaz + `log.md`'ye "şablon eksiği" kaydı |
| Şablon okundu ama çelişiyor | DUR → `[[../CLAUDE.md]]` §2.1 SSOT öncelik sırası |
| Vault erişilemiyor | `⚠️ VERIFICATION REQUIRED` → yazım durdurulur, kullanıcıya sor |

### §4.1 Bağlayıcı Kurallar

| # | Kural | Uygulama | İhlal Sonucu |
|---|-------|----------|--------------|
| 1 | Template Mandatory (Guardrail #16) | Bu dosya docs-md iskeletinden üretilir | Dosya geçersiz, revert |
| 2 | Şablon Önce | Yazmadan `.ai/.templates/` okunur | ERROR log, yazıma devam yok |
| 3 | SSOT | Test verisi research-bank'te; karar ADR-023'te; **burada yalnız süreç** | Çakışan sayı silinir |
| 4 | Zero Hallucination (**ADR-005**) | Etiketsiz/kanıtsız iddia yazılmaz; verilmeyen eklenmez | İddia silinir + `log.md` ERROR |
| 5 | Kaynak Etiketleme | Her eşik/iddia research-bank durumuyla etiketlenir (§4.5) | Etiketsiz satır test edilemez sayılır |
| 6 | In-Place Refactoring | Dosya adı/yolu onaysız DEĞİŞTİRİLMEZ (`methodology-v2.md` üretilmez) | Dosya geri yüklenir |
| 7 | Frozen ADR 001-037 | Okunur, referans edilir; değiştirilmez | revert + log ERROR |
| 8 | Append-only log | Değişiklikler `log.md`'ye **eklenir** | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya `repair` ile onarılır |
| 10 | Dil / Mojibake | Türkçe; `Ã-` dizileri ve U+FFFD yasak (§4.3) | `verify` → `repair` |
| 11 | REDACTED / KVKK | Secret, gerçek kullanıcı/çocuk verisi yazılmaz (§4.7) | Sızıntı sayılır |
| 12 | Domain boundary | Dosya QA Engineer (test) + MO (vault) alanıdır | Layer violation → revert |

### §4.2 Frontmatter — 7 Zorunlu Alan

| Alan | Zorunlu | Kural | Bu Dosyadaki Değer |
|------|---------|-------|--------------------|
| `title` | ✅ | Tırnak içinde, `CoreMusic — ...` | `"CoreMusic — Persona Test Metodolojisi (...)"` |
| `type` | ✅ | Test metodolojisi tipi | `methodology` |
| `category` | ✅ | Kategori | `personas` |
| `version` | ✅ | Semver | `1.0.0` |
| `status` | ✅ | `active` / `draft` / `deprecated` | `active` |
| `authority` | ✅ | **`reference`** (SSOT self-claim yasak) | `reference` |
| `updated` | ✅ | `YYYY-MM-DD` | `2026-09-26` |

### §4.3 Dil, Kod Adı ve Mojibake (UTF-8)

| Kural | Detay |
|-------|-------|
| Ana dil | Türkçe — ç ğ ı İ ö ş ü doğru yazılır |
| Dosya/klasör adı | ASCII: `methodology.md` |
| Teknik terim | Gerektiği yerde İngilizce kalır (viewport, throttling, Lighthouse, branch coverage) |
| Mojibake yasak | `Ã-` ile başlayan diziler ve U+FFFD yer tutucuları `verify` ile tespit edilir |
| Yazım aracı | `node .ai/scripts/vault-utf8-writer.mjs` (`write` / `append` / `insert-before-marker`) |
| PowerShell yazım yasağı | `Set-Content`, `Out-File`, `Add-Content`, `echo >` kullanılmaz (Windows-1254/BOM/UTF-16 bozar) |
| log.md | **Yalnız `append` modu** — bayt-seviyesi, mevcut içeriğe dokunmaz |
| Doğrulama | Yazım sonrası `verify --file` → `mojibake: 0`, `hasBom: false` |

### §4.4 Wiki-Link ve Bağlantı Formatı

| ✅ Doğru | ❌ Yanlış |
|----------|-----------|
| `[[personas/index]]` | `[index](index.md)` |
| `[[personas/research-bank]]` | `[rb](C:\www\coremusic.net\.ai\.personas\research-bank.md)` |
| `[[ADR-023-persona-driven-testing]]` | `https://iç-sistem/adr-023` |
| `[[.templates/index]]` | `[reg](../.templates/index.md)` |
| Harici URL düz metin | `https://...` (wiki-link yapılmaz) |

**Göreli yol kuralı:** bağlantı dosyanın kendi dizininden göreli yazılır; hedef diskte yoksa §7.1'de `📋 planlanan` işaretlenir + `log.md`'ye kaydedilir.

### §4.5 Kaynak Etiketleme — Research-Bank Durum Aktarımı (ADR-005)

**Test metnindeki her gerçek-dünya iddiası research-bank'tan gelir; durumu da taşınır. Etiketsiz iddia test edilemez.**

| # | Banka Durumu | Bu Dosyadaki Karşılığı | Testte Uygulaması |
|---|--------------|------------------------|-------------------|
| 1 | `VERIFIED` | §3.4'teki P5/P6/P7/P8 satırları | Eşik doğrudan hedef yazılır → `Kaynak: research-bank P8 + [K1] + [K2]` |
| 2 | `SINGLE-SOURCE ⚠️` | Tek kaynaklı iddia (ör. X-3) | `⚠️ VERIFICATION REQUIRED` + 1 kaynak — test sonucuna **veri** yapılmaz |
| 3 | `CONFLICT ⚠️` | İki değerli alan (P1 nüfus) | İki değer de yazılır + `⚠️ VERIFICATION REQUIRED`; tek değer seçilecekse kaynağıyla |
| 4 | `DERIVED ⚠️` | viewport · 0-100 Big Five · 16 yaş veli onayı · iç hedefler | `⚠️ DERIVED` + dayanak satırı birlikte yazılır |
| 5 | `⚠️ VERIFICATION REQUIRED` | EXCLUDED (X-1 … X-10) | İddia yazılmaz ya da 1 kaynakla işaretli yazılır (§6.3) |

| ✅ Doğru | ❌ Yanlış (Hallüsinasyon) |
|----------|---------------------------|
| `LCP ≤ 2500 ms (research-bank P8 — VERIFIED)` | `LCP 1800 ms` (kaynaksız hedef) |
| `viewport ≈ 360×780 (dpr 3) — ⚠️ DERIVED` | `viewport = 360×780` (türetilmiş gibi sunum) |
| `16 yaş altı → veli onayı — ⚠️ DERIVED (P6)` | `KVKK 16 diyor` (yanlış atıf) |
| `geolocation emülasyon API'si ⚠️ VERIFICATION REQUIRED` | Uydurma CDP method adı |
| `Kaynak: research-bank P5 (W3C Rec + Quick Ref)` | `Kaynak: internet` · `Kaynak: biliniyor` |

### §4.6 Derinlik Standardı (500+)

| Kural | Değer |
|-------|-------|
| Bu dosya | ≥ 500 satır (ham ölçüm — `vault-utf8-writer verify` → `lines`) |
| Üretilen test raporu | Kural değil; ama §5.4 şablonu dolu doldurulur |
| Kapsam dışı | `session-log-template.md` (144 satır — bu dosyayla ilgisi yok) |
| İhlal | 500 altındaysa dosya tamamlanmış sayılmaz |

### §4.7 REDACTED, KVKK ve Sır Politikası

| Durum | Aksiyon |
|-------|---------|
| API key, token, parola, `.env` değeri | Test raporuna/vault'a yazılmaz; `[REDACTED]` |
| Gerçek kullanıcı verisi (ad, e-posta, telefon, konum) | Kurguya çevrilir ya da `[REDACTED]` |
| 16 yaş altı persona (KVKK m.5/1 · P6) | Kurgusal ve test amaçlı olduğu belirtilir; **gerçek çocuk verisi asla** |
| Sağlık/kısıt verisi (6698 m.6 — özel nitelikli) | `Kaynak: kurgusal (persona verisi)` + KVKK notu |
| Log'a sızan secret | `log.md` girişinde `[REDACTED]` |

### §4.8 In-Place, Frozen ADR ve Dosya Adı Koruması

| Kural | Uygulama |
|-------|----------|
| Dosya adı değişmez | `methodology.md` → `methodology-v2.md` üretilmez |
| Kırık link güncelleme | `[[personas/methodology]]`'yi işaretleyen `📋 planlanan` satırları (index · mood-taxonomy · research-bank §7.1) **bu dosyanın sorumluluğu değildir** — güncellemek için MO onayı gerekir |
| Frozen ADR 001-037 | Okunur, referans edilir; değiştirilmez |
| ADR-023 değişikliği | Bu dosyada yapılmaz; yeni ADR `ADR-088+` |
| Silinmezlik | Eski içerik düzeltilir, `log.md`'ye kaydedilir |

### §4.9 Yasak / Doğru Tablosu

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| `INP 180 ms — iyi` (kaynaksız) | `INP ≤ 200 ms iyi / > 500 ms kötü (P8 — VERIFIED)` |
| `Lighthouse skoru 95 şart` (sürüm belirsiz) | `Lighthouse v8 ağırlıkları: LCP 25 · TBT 30 · CLS 15 · FCP 10 · SI 10 · TTI 10` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16 (13'e inebilir) · TMK m.11 = 18 · P6: 16 altı = veli onayı ⚠️ DERIVED` |
| `persona Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + yöntem ⚠️ DERIVED (IPIP-NEO-120)` |
| `coverage %80 yeter` (AGENTS.md eski eşik) | `ADR-023: %90 satır + şart; kritik yollar %100 branş` |
| `Seviye 2 testi geçti, E2E gerekmez` | `Seviye 3 coverage üretir; Seviye 1 tek başına yeterli değildir` |
| Raporu `.ai/sessions/` yazmak (eski vault) | §5.4 — yol `⚠️ VERIFICATION REQUIRED`, MO onayıyla |

---

## §5 Workflow

### §5.1 Genel Üretim Akışı (her persona için)

| Adım | Aksiyon | Çıktı | Süre |
|------|---------|-------|------|
| 1 | `[[personas/index]]` + persona dosyası + `[[personas/test-scenarios-mapping]]` oku | Persona + öncelik senaryosu | 5 dk |
| 2 | `[[personas/research-bank]]` P5/P6/P7/P8 + bu dosya §3.3-§3.4 oku | Eşik + emülasyon seti | 5 dk |
| 3 | **PREPARE** (§3.1.1 P1-P7) | Hazır ortam (viewport · tema · network · konum) | 5 dk |
| 4 | **Seviye 1** — AI Rol Kartı ile rol testi | Davranış notları + senaryo taslağı (H/E/B) | 10 dk |
| 5 | **Seviye 2** — Browser MCP canlı test (≥10 adım) | Screenshot + metrik + konsol + Lighthouse | 25 dk |
| 6 | **Seviye 3** — Playwright ile senaryoyu otomatikleştir | `*.spec` + `@group persona-NN` | 30 dk |
| 7 | **COLLECT** (§3.1.3 çıktısı) | Ham veri paketi | 5 dk |
| 8 | **Seviye 4 / REPORT** — rapor + özet tablo + öneri listesi | Rapor dosyası + CSV | 15 dk |
| 9 | ADR-023 matrisinde satır durumunu işaretle (birim + entegrasyon + H/E/B) | Matris güncellemesi | 5 dk |
| 10 | `vault-utf8-writer verify` + `log.md` **append** | UTF-8 raporu + audit trail | 2 dk |

```text
OKU (persona + research-bank + bu dosya) → PREPARE → SEVIYE 1 → SEVIYE 2 → SEVIYE 3 → COLLECT → REPORT → MATRİS → UTF-8 VERIFY → LOG APPEND
```

### §5.2 Seviye Bazlı Üretim Adımları

#### Seviye 1 — AI Rol Testi

| # | Adım | Çıktı |
|---|------|-------|
| 1 | AI Rol Kartı'nı persona dosyasından al (§3.5.9), `{{...}}` yoksa başlat | Prompt bloğu |
| 2 | Rolü test et: müzik seçimi · öneri beklentisi · sabır/hata tepkisi (§3.2 satır 3-8) | Davranış gözlemi |
| 3 | Her gözlemi **kaynak etiketiyle** yaz (kurgusal persona verisi) | Etiketli not |
| 4 | 3 yol üret: **happy / error / boundary** (ADR-023 §2.2a zorunluluğu) | Senaryo taslağı |
| 5 | Taslağı `test-scenarios-mapping` önceliğiyle hizala | Senaryo önceliği |

#### Seviye 2 — Browser MCP Canlı Test

| # | Adım | Çıktı |
|---|------|-------|
| 1 | PREPARE ayarlarını uygula (viewport · tema · network · konum — §3.3) | Emüle ortam |
| 2 | ≥10 adımı (§3.1.2) sırayla çalıştır; her adımda screenshot + konsol | Adım kanıtı |
| 3 | Performans metriklerini topla (FCP · LCP · TBT · CLS · SI) | Metrik tablosu |
| 4 | Lighthouse audit çalıştır (Performance · A11y · Best Practices · SEO) | Skorlar |
| 5 | Eşiklerle karşılaştır (§3.4.1/§3.4.2) → geçen/geçmeyen işaretle | Durum sütunu |
| 6 | Aday test taleplerini üret (Seviye 3 girdisi) | Test talebi listesi |

#### Seviye 3 — Playwright E2E Otomasyon

| # | Adım | Çıktı |
|---|------|-------|
| 1 | Emülasyonu kur: `playwright.devices` **veya** `page.setViewportSize()`; `colorScheme`; `offline`/CDP ağ | Test context |
| 2 | Senaryoyu spec'e çevir (Adım · Beklenen · Doğrulama yöntemi 3 sütunu) | `*.spec` dosyası |
| 3 | Her testi `@group persona-NN` ile etiketle (ADR-023 §5.1/3) | Grup etiketi |
| 4 | Üç yolu kodla: happy · error · boundary | 3 test yolu |
| 5 | Yerelde çalıştır → CI'a bağla (ADR-023 §5.4/2 — kritik akış E2E şartı) | CI çıktısı |
| 6 | Kritik yollar için branş katkısını izle (`include/**`, `Api/**`, `Middleware/**`) | Gate girdisi |

#### Seviye 4 — Rapor (COLLECT + REPORT)

| # | Adım | Çıktı |
|---|------|-------|
| 1 | Toplanan veriyi paketle (screenshot · konsol · metrik · Lighthouse · waterfall) | Ham veri |
| 2 | §5.4 şablonuyla markdown raporu yaz | Rapor dosyası |
| 3 | Özet tabloyu üret (tüm personalar tek tabloda — CSV/Excel) | Özet |
| 4 | Öneri/aksiyon listesini yaz (yeni test · persona düzeltme · gate boşluğu) | Aksiyonlar |
| 5 | `log.md` append + ADR-023 matrisinde satır durumu | Audit + matris |

### §5.3 ADR-023 Coverage Gate (bağlayıcı)

> Bu bölüm ADR-023'ü **yeniden yorumlamaz** — yalnızca test hattına nasıl uygulanacağını yazar. Eşik/A gate kararı sahibi `[[ADR-023-persona-driven-testing]]`'tir.

| # | Gate Kuralı | Değer | Durum (ADR-023) |
|---|-------------|-------|-----------------|
| 1 | Genel coverage | **%90 satır + %90 şart (branch)** — `shared` + `auth` | Karar: **accepted** (AGENTS.md §16 %80'den sıkılaştırma) |
| 2 | Kritik yollar | **%100 branş** — `auth.coremusic.net/include/**`, `shared/src/Api/**`, `shared/src/Middleware/**` | Karar: accepted; payment kapsamı **`⚠️ VERIFICATION REQUIRED`** (kod yok) |
| 3 | PR kapısı | `ci.yml` coverage raporu + eşik → **eşik altındaysa PR merge EDİLMEZ** | **PLANNED** (gate bugün yok) |
| 4 | Kademeli eşik | baseline → **%70 → %80 → %90** | Karar: accepted (ilk gün pipeline kırılmasın) |
| 5 | Mutation | kritik dizinlerde Infection `--min-msi` **%65 → %80** | Karar: accepted |
| 6 | Flaky koruması | retry **2** + quarantine listesi + `failOnRisky/failOnWarning` | Karar: accepted |
| 7 | Matris | **20 persona** × (1 birim + 1 entegrasyon) × **H/E/B** = 40 senaryo | Debate: ✅ 18/2/0 KABUL |
| 8 | Eşik bugünkü değeri | Coverage raporu **0** → `⚠️ VERIFICATION REQUIRED`; baseline ölçümü ilk adım | PLANNED |
| 9 | JS/E2E | Dosya **0** → Seviye 3 bu boşluğu kapatır (kritik akış E2E: footer player ADR-018 + API ADR-020) | PLANNED |
| 10 | Test yazım standardı | `#[Test]`, `#[DataProvider]` (static provider); PHPUnit `^12` **ayrı adım** | Karar: accepted |

**Gate'e giren bu metodolojiden çıkan işler:**

| Seviye | Gate'e Katkısı | Kanıt |
|--------|----------------|-------|
| 1 | Senaryo taslağı → sonradan yazılan birim/entegrasyon testi | `@group persona-NN` senaryosu |
| 2 | Bulgu → test talebi (boşluğu kapatır) | Rapor "öneri" listesi |
| 3 | **Doğrudan satır + şart üretir**; kritik dizinlerde branş | CI coverage çıktısı |
| 4 | Matris kapanışı + kalan boşluk listesi | `log.md` append |

### §5.4 Rapor Formatı

**Dosya yolu:** eski vault `.ai/sessions/persona-test-{ad}-{tarih}.md` idi; bu vault'ta `sessions/` dizini **YOK** → hedef yol **`⚠️ VERIFICATION REQUIRED`** (MO onayı gerekir; geçici aday `.ai/reports/persona-test-*.md` — `.ai/reports/` dizini vault'ta mevcuttur).

```markdown
# Persona Test Raporu: {Ad Soyad}
**Tarih:** {YYYY-MM-DD HH:mm}   **Seviye:** {1 / 2 / 3}   **Persona:** {[[personas/index]] satırı}
**Cihaz:** {model}   **Viewport:** {g}x{y} (DPR {d})   **Tema:** {light/dark}
**Network:** {preset — P8: 150 ms · 1.6/750 Mbps/Kbps}   **Konum senaryosu:** {şehir/semt — kurgusal}

## Test Sonuçları
| Adım | Başlık | Durum | Süre | Not |

## Performans Metrikleri (research-bank P8 — VERIFIED eşikler)
| Metrik | Değer | İyi | Kötü | Durum |

## WCAG 2.2 AA (research-bank P5)
| Kriter | Eşik | Sonuç |

## Lighthouse Skorları (v8 ağırlıkları)
| Kategori | Skor | Hedef |

## Ekran Görüntüleri / Console Logları
[screenshot referansı · hata/uyarı — hedef 0]

## ADR-023 Matris Etkisi
| Matris satırı | Birim (H/E/B) | Entegrasyon (H/E/B) | Durum |

## Öneriler
[Yeni test talebi · persona düzeltme · gate boşluğu — etiketli]
```

**Excel/CSV raporu:** özet sayfa (tüm personalar) + persona başına detay; **bu vault'ta `rapor-sablonlari/` dizini YOK** → şablon yolu `⚠️ VERIFICATION REQUIRED` (eski vault `personas/rapor-sablonlari/test-sonuc.xlsx` salt okunur referanstır).

### §5.5 Hata Modları

| Hata | Belirti | Düzeltme |
|------|---------|----------|
| Persona alanı eksik | §3.2 eşleşmesinde satır `yok` | Alanı `yok` diye işaretle; adımı atla, **atlamayı raporla** |
| Eşik kaynaksız | §6.1 "kaynak etiketi" düşer | research-bank P5/P6/P7/P8'den al; yoksa `⚠️ DERIVED`/`⚠️ VERIFICATION REQUIRED` |
| Viewport türetilmiş gibi sunuluyor | `⚠️ DERIVED` etiketi yok | P3 dayanağıyla birlikte işaretle |
| EXCLUDED iddia test verisi olmuş | §6.3 listesi ile rapor uyuşmuyor | İddiyi sil + `log.md` ERROR |
| Konsol hatası raporlanmamış | Konsol 0 değilken "geçti" | Adım `error` işaretle; hata raporda tam metin |
| Mojibake | `verify.mojibake > 0` | `vault-utf8-writer repair` |
| Eşik çelişkisi (P8 vs iç hedef) | İki farklı sayı | P8 esas; iç hedef `⚠️ DERIVED` (§3.4.5) |
| Gate PLANNED sanılıp "gate geçildi" denmiş | Coverage raporu 0 | `⚠️ VERIFICATION REQUIRED` + baseline ölçümü (§5.3/8) |
| Başka dosyaya yazım | `index.md`/`research-bank.md` kirlenmiş | revert + `log.md` ERROR (domain boundary) |

### §5.6 Bitiş Koşulları

- [ ] Dosya hedef yolda: `.ai/.personas/methodology.md`, adı ASCII, `.md` uzantılı
- [ ] `vault-utf8-writer verify` temiz (BOM yok, mojibake 0, `lines ≥ 500`)
- [ ] `log.md` **append** edildi (geçmiş satıra dokunulmadı)
- [ ] Wiki-link'ler `[[...]]` formatında; hedefi olmayanlar §7.1'de durum sütunlu
- [ ] ADR-023 gate değerleri ADR metniyle birebir (§5.3)

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`)
- [ ] `type: methodology` · `category: personas` · `version: 1.0.0` · `status: active` · `updated: 2026-09-26`
- [ ] Tek H1 var (`# `) + Zorunlu Bağlantılar satırı (≥ 1 wiki-link)
- [ ] §1-§7 başlıkları eksiksiz (8 bölüm iskeleti)
- [ ] §2.2'de **4 test seviyesi** tablosu var ve 5 sütunu taşıyor (Seviye · Araç · Çıktı · ADR-023 coverage katkısı · Sorumlu)
- [ ] §3.1 akışı **PREPARE → EXECUTE → REPORT** (COLLECT dahil) ve ≥ 10 EXECUTE adımı var
- [ ] §3.2'de persona-template **11 alan** eşleşmesi 11/11 dolu
- [ ] §3.3'te **viewport · tema · network · geolocation** başlıkları ayrı ayrı var
- [ ] §3.4 eşikleri research-bank ile **birebir** (LCP 2500/4000 · INP 200/500 · CLS 0.1/0.25 · FCP 1s · TBT 50ms · Lighthouse v8 25/30/15/10/10/10 · throttling 150ms + 1.6/750)
- [ ] §3.4.2 WCAG 2.2 AA: 1.4.3 (4.5:1) · 1.4.11 (3:1) · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 (24×24) · 3.3.8
- [ ] §3.4.3 KVKK/TMKG/COPPA/GDPR satırları + "16 yaş altı = veli onayı **⚠️ DERIVED**"
- [ ] §3.4.4 IPIP-NEO-120 + alfa (0.81/0.90/0.89/0.86/0.90) + "0-100 normalize **⚠️ DERIVED**"
- [ ] Her gerçek-dünya satırında research-bank durum etiketi var (§4.5 — ADR-005)
- [ ] EXCLUDED iddialar §6.3'te listeli ve testte nasıl davranılacağı yazılı
- [ ] §4.0 Şablon-Önce bloğu birebir mevcut ve §4'ün ilk maddesi
- [ ] §4'te numaralı kurallar + Yasak/Doğru tablosu (§4.9) var
- [ ] §5.3'te ADR-023 gate değerleri var: %90 satır+şart · kritik %100 branş · PR'da merge block · kademeli baseline→70→80→90
- [ ] §7.1 wiki-link tablosu ≥ 8 hedef; §7.2 Değişiklik Geçmişi append-only
- [ ] Mojibake yok (`verify` → mojibake 0, BOM false); dosya derinliği ≥ 500 satır
- [ ] Verilmeyen hiçbir sayı eklenmedi; REDACTED/KVKK ihlali yok; başka `.personas` dosyasına yazılmadı

### §6.2 Taşınan Araştırma Bulguları Metriği

| Paket | Konu | Taşınan satır | `VERIFIED` | `⚠️` etiketli satır | Uyarı notu |
|-------|------|---------------|------------|----------------------|------------|
| P8 | Test Metodolojisi (eşik · Lighthouse · throttling · emülasyon · breakpoint) | 11 | **11** | 0 | 4 (§3.4.1 · §3.3.3) |
| P5 | WCAG 2.2 AA kriterleri | 7 | **7** | 0 | 2 (§3.4.2 — eksik kriter + AAA) |
| P6 | KVKK / Yaş sınırı | 5 | **4** | **1** (`⚠️ DERIVED` veli onayı) | 3 (§3.4.3 · §4.7) |
| P7 | Big Five (IPIP-NEO) | 4 | **3** | **1** (`⚠️ DERIVED` 0-100) | 2 (§3.4.4) |
| Eski vault | İç hedef tablosu (research-bank'ta YOK) | 9 | 0 | **1** (`⚠️ DERIVED` — X-10) | 1 (§3.4.5) |
| **TOPLAM** | — | **36** | **25** | **3** | **12** |

**Doğrulama notları:**

- research-bank'ta toplam **66 bulgu / 58 `VERIFIED`** vardır (§6.2 bankanın kendisi); bu dosya **yalnız testle ilgili 25 `VERIFIED`** satırı taşımıştır — P1-P4 (demografi, okul, cihaz spec, müzik) **persona içeriğidir, test eşiği değildir** → taşınmadı.
- **3 `⚠️` etiketi** zorunludur (P6 veli onayı · P7 0-100 normalize · iç hedefler X-10); ayrıca **12 uyarı notu** (bankanın `⚠️` blokları) korunmuştur.
- Taşınan `⚠️`/`VERIFIED` değerleri **bankanın etiketiyle aynıdır**; yeniden adlandırma/`VERIFIED`'a yükseltme **yasaktır** (§4.5).

### §6.3 EXCLUDED Listesine Atıf (research-bank §6.4)

| # | İddia | Bu Metodolojide Ne Yapılır |
|---|-------|-----------------------------|
| X-1 | Redmi Note 12 · iPhone 11/12 · Moto G spec'leri | Bu cihazlarla Seviye 2/3 **önce ek tur** → viewport yoksa test `⚠️ VERIFICATION REQUIRED` ile raporlanır |
| X-2 | Şarkı bazlı BPM değerleri | Test adımında **parça BPM'i hedeflenmez**; tür aralığı (P4 `VERIFIED`) kullanılır |
| X-3 | Ezhel — Spotify 2018-2021 TR en çok dinlenen | Test beklentisine **veri yapılmaz**; kullanılırsa `⚠️ VERIFICATION REQUIRED` + 1 kaynak |
| X-4 | 50-59 / 60-69 / 70+ yaş grupları | Persona yaşı bu aradaysa **yaş-bazlı akış (KVKK) verisi yazılmaz** → `⚠️ VERIFICATION REQUIRED` |
| X-5 | Güzel sanatlar / spor liseleri süre-kontenjan | Persona "okul" alanı test verisi olarak **kullanılmaz** (yalnız kurgusal kimlik) |
| X-6 | Üniversite/bölüm adları | Aynısı — test adımı üretilmez |
| X-7 | WCAG AA kriterlerinin tamamı (1.4.5, 2.4.3, 3.3.x eksiksiz) | A11y auditte **yalnız §3.4.2 listesi** gerekçe sayılır; numara fazlası eklenirse `⚠️ VERIFICATION REQUIRED` |
| X-8 | KVKK idari para cezası tutarları | Rapora/akışa **konu olmaz** |
| X-9 | IPIP-NEO Türkçe geçerlilik çalışması | "Türkçe geçerli" iddiası yazılmaz → `⚠️ VERIFICATION REQUIRED` |
| X-10 | Persona testlerine özel LCP/INP hedefleri | **Yalnız `⚠️ DERIVED` + P8 dayanağı** ile yazılır (§3.4.5 uygulaması) |

*Bu liste araştırma bankasının SSOT'udur; bu dosya yalnızca **test davranışına çevirir**. Yeni EXCLUDED eklemek research-bank'te yapılır (append-only).*

### §6.4 Quality Report (Bu Dosyanın Kendisi)

| Metrik | Değer |
|--------|-------|
| Versiyon | 1.0.0 |
| Durum | Red Team · Human Mode · Truth Mode verified |
| Bölüm | 7 (H1 + §1-§7) + frontmatter (7 alan) |
| Test seviyesi | 4 (Seviye 1-4) |
| Akış aşaması | 3 + COLLECT (PREPARE → EXECUTE → REPORT) |
| Zorunlu blok | 1 (§4.0 Şablon-Önce — silinemez) |
| EXECUTE adımı | 11 (asgari 10) |
| Persona alanı eşleşmesi | 11/11 |
| Taşınan `VERIFIED` bulgu | 25 (P8 11 · P5 7 · P6 4 · P7 3) |
| Taşınan `⚠️` etiketi | 3 (`⚠️ DERIVED`) + 12 uyarı notu |
| EXCLUDED atfı | 10/10 (X-1 … X-10) |
| ADR-023 gate satırı | 10 (§5.3) |
| Anti-pattern | 10 (§3.5) |
| Wiki-link (§7.1) | 8 hedef |
| Derinlik | ≥ 500 satır (`verify` → `lines`) |
| Kayıt | `log.md` append-only |

---

## §7 Referanslar

### §7.1 Wiki-link Referansları

| Wiki-link | İlişki | Durum / Gerçek hedef |
|-----------|--------|----------------------|
| `[[personas/index]]` | Persona kataloğu — 68 persona / 6 grup (testin girdisi) | ✅ `.ai/.personas/index.md` |
| `[[personas/persona-template]]` | Persona şablonu — 11 alan + AI Rol Kartı + Test Adımları (§3.2 kaynağı) | ✅ `.ai/.templates/personas/persona-template.md` |
| `[[personas/research-bank]]` | Araştırma bankası — P5/P6/P7/P8 eşikleri + EXCLUDED (§6.3 kaynağı) | ✅ `.ai/.personas/research-bank.md` |
| `[[personas/mood-taxonomy]]` | Mood küme adları — Seviye 1/2 beklentileri | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Senaryo × grup matrisi — Seviye 1 önceliği | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı — 20 matris · %90 · %100 branş · PR gate | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |
| `[[ADR-005-ultrathink-protocol]]` | Zero-hallucination — §4.1/§4.5 dayanağı | ✅ `.ai/.decisions/accepted/ADR-005-ultrathink-protocol.md` |
| `[[.templates/index]]` | Şablon registry'si (Guardrail #16) | ✅ `.ai/.templates/index.md` |

*Bu dosyaya bakan `📋 planlanan` satırları (`index.md`, `mood-taxonomy.md`, `research-bank.md` §7.1, `persona-template.md` §7.1) **başka dosyalardır** — güncellemesi MO'ya aittir (§4.8).*

### §7.2 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — eski vault `methodology.md` (110 satır) iskeleti alınıp genişletildi: 4 test seviyesi tablosu, PREPARE → EXECUTE → REPORT akışı, 11/11 persona-alanı eşleşmesi, emülasyon ayarları (viewport · tema · network · geolocation), research-bank P5/P6/P7/P8'ten 25 `VERIFIED` + 3 `⚠️ DERIVED` eşik, ADR-023 coverage gate bölümü, EXCLUDED (X-1…X-10) atfı | methodology-agent (vault-updater) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
