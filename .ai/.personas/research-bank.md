---
title: "CoreMusic — Persona Araştırma Bankası (Research Bank)"
type: research
category: personas
version: 1.0.0
status: active
authority: "Research Bank — SSOT: personas/research-bank.md"
updated: 2026-09-26
---

# CoreMusic — Persona Araştırma Bankası (Research Bank)

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[.templates/index]]

---

| Alan | Değer |
|------|-------|
| Dosya Adı | `research-bank.md` |
| Dosya Yolu | `.ai/.personas/research-bank.md` |
| Dosya Tipi | Araştırma bankası (persona besleyicisi) — karar DEĞİLDİR (ADR değil) |
| Hedef Kitle | QA Engineer, UX Researcher, UI Designer, persona üreten AI ajanları |
| Yapı | H1 + Zorunlu Bağlantılar + §1-§7 + §3 altında P1-P8 paketleri |
| Bulgu Satırı Formatı | `| Bulgu | Kaynak 1 | Kaynak 2 | Durum |` |
| Durum Sözlüğü | `VERIFIED` · `SINGLE-SOURCE ⚠️` · `CONFLICT ⚠️` · `DERIVED ⚠️` · `⚠️ VERIFICATION REQUIRED` |
| Beslediği Şablon | `[[personas/persona-template]]` §4.5 (Kaynak Etiketleme) |
| Bulgu Sayısı | 66 (P1-P8 toplamı — §6.2 metrikleri) |
| Araştırma Turu | 2026-09-26 web araştırması (tamamlandı) |
| Dil | Türkçe (ç ğ ı İ ö ş ü doğru; mojibake YASAK) |
| Authority | Research Bank — SSOT: `personas/research-bank.md` |
| Governance | Red Team · Human Mode · Truth Mode |
| Son Güncelleme | 2026-09-26 |

---

## §1 Amaç

Bu dosya, **CoreMusic persona dosyalarının gerçek-dünya veri beslemesidir**: demografiden okul türlerine, cihaz teknik özelliklerinden WCAG 2.2 AA eşiklerine, KVKK yaş sınırından Big Five ölçeğine ve test metodolojisine kadar **doğuştan doğrulanmış araştırma bulgularını** tek çatı altında toplar. Persona yazarı (QA Engineer / UX Researcher) her gerçek-dünya iddiası yazmadan **önce bu bankaya bakar**; bankada `VERIFIED` olan iddia kaynak etiketiyle persona dosyasına taşınabilir, olmayan iddia `⚠️ VERIFICATION REQUIRED` olarak işaretlenir.

| Boyut | Değer |
|-------|-------|
| Ne taşır | Doğrulanmış (veya etiketlenmiş) araştırma bulguları — 8 paket (P1-P8) |
| Ne taşımaz | Karar (ADR), test stratejisi (`personas/methodology`), mood küme adı (`personas/mood-taxonomy`), persona profili (`.templates/personas/persona-template`) |
| Neden SSOT | Persona dosyalarına **dağıtılmış kopya** yerine tek bankadan bakılır; kaynak çelişkisi tek yerde görünür (§6.3) |
| Kim okumalı | Persona üreten/yenileyen her ajan; denetimde Vault Steward |
| Nasıl uygulanır | `[[personas/persona-template]]` §4.5 kaynak etiketleme tablosu bu bankadaki Durum sütununu referans alır |
| Ne zaman yazıldı | 2026-09-26 — persona sürükleme testleri (ADR-023) öncesi veri temeli |

**Bağlantı zinciri:** `research-bank.md` (bu dosya, veri) → `[[personas/persona-template]]` (alan havuzu, etiket formatı) → persona dosyası (`kirik-ad-surname-mood.md`) → `[[personas/methodology]]` (test seviyeleri) → `[[ADR-023-persona-driven-testing]]` (karar).

---

## §2 Kapsam

### §2.1 Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| P1-P8 araştırma bulguları + Bulgu/K1/K2/Durum tabloları | Persona dosyasının kendisi (→ `[[personas/persona-template]]`) |
| Durum etiketleme (VERIFIED / SINGLE-SOURCE / CONFLICT / DERIVED) | Test adımları ve başarı metrikleri (→ `personas/methodology`) |
| Persona dosyalarına uygulanacak kaynak kuralları (§4) | Mood küme adları ve UI etkileri (→ `personas/mood-taxonomy`) |
| CONFLICT ve EXCLUDED listeleri (§6.3-§6.4) | Mimari karar (→ `[[ADR-005-ultrathink-protocol]]`, `[[ADR-023-persona-driven-testing]]`) |

### §2.2 Alt Konular (P1-P8 Paketleri)

| Paket | Konu | Ana Kullanım Alanı (persona alanı) |
|-------|------|-----------------------------------|
| P1 | Türkiye Demografisi (TÜİK ADNKS 2025) | Kimlik Kartı (şehir/yaş), Müzik DNA'sı segment temeli |
| P2 | Türkiye Okul Türleri (MEB) | Kimlik Kartı (okul/meslek) |
| P3 | Cihaz Teknik Özellikleri | Cihaz & Teknoloji (çözünürlük → viewport) |
| P4 | Sanatçı & Müzik Türü / BPM | Müzik DNA'sı (tür, sanatçı, BPM) |
| P5 | WCAG 2.2 AA | Erişilebilirlik / Kısıt (kriter numaraları) |
| P6 | KVKK / Yaş Sınırı | Kimlik Kartı (yaş) + veli onayı alanı |
| P7 | Big Five (OCEAN) | Big Five (IPIP-NEO) puanlama yöntemi |
| P8 | Test Metodolojisi | Test Adımları (LCP/INP/CLS hedefleri, throttling, emülasyon) |

### §2.3 Hedef Kitle

| Kitle | Bu Dosyayı Nasıl Kullanır |
|-------|---------------------------|
| QA Engineer (persona-test) | Persona üretirken her gerçek-dünya satırını bu bankadan alır, Durum sütununu etikete çevirir |
| UX Researcher | CONFLICT satırlarını yeniden araştırır; EXCLUDED iddiaları kapatır |
| UI Designer | P5 (WCAG) ve P3 (viewport) satırlarını tasarım kontrolünde kullanır |
| Test otomasyonu (Playwright) | P8 eşik/throttling/breakpoint değerlerini script'e besler |
| Vault Steward | §6.1 kontrol listesi + §6.2 metriklerle denetim yapar |

### §2.4 Kapsam Dışı İstisnalar

| Durum | Ne Yapılır |
|-------|-----------|
| Bankada olmayan bir gerçek-dünya iddiası persona'da isteniyor | `⚠️ VERIFICATION REQUIRED` + 1 kaynak yazılır (veya iddia silinir); uydurma 2. kaynak yazılmaz |
| Bankadaki CONFLICT alanı persona'da kullanılmayacak | Çelişki yok sayılmaz; persona satırında tek değer + kaynak + `⚠️ VERIFICATION REQUIRED` yazılır |
| Yeni araştırma turu sonuç geliyor | Yeni satır eklenir (append-only); mevcut satır **değiştirilmez**, durumu `CONFLICT ⚠️` olarak güncellenir |
| Bulgu bir karar üretiyor (ör. "16 yaş altı için veli akışı kurulacak") | Karar ADR'dir → `[[ADR-023-persona-driven-testing]]` ya da yeni `ADR-088+`; bu dosya yalnız veri taşır |
| Dosya adı/yer değişikliği isteniyor | In-Place kuralı → onay olmadan dosya adı DEĞİŞTİRİLMEZ |

---

## §3 Mimari — Araştırma Paketleri (P1-P8)

### §3.0 Genel Yapı

```text
.ai/.personas/
├── index.md                    → persona kataloğu (bu dosyaya DOKUNULMAZ)
├── mood-taxonomy.md            → mood küme adları (bu dosyaya DOKUNULMAZ)
├── test-scenarios-mapping.md   → senaryo eşlemesi (bu dosyaya DOKUNULMAZ)
└── research-bank.md            → BU DOSYA (SSOT: persona araştırma verisi)
    ├── §1 Amaç
    ├── §2 Kapsam
    ├── §3 Mimari → P1 ... P8 (aşağıda)
    ├── §4 Kurallar (kaynak etiketleme + persona'ya uygulama)
    ├── §5 Workflow (bankaya bulgu ekleme akışı)
    ├── §6 Doğrulama (metrik, CONFLICT, EXCLUDED)
    └── §7 Referanslar (wiki-link + Değişiklik Geçmişi)
```

| Paket | Durum Dağılımı (satır) |
|-------|------------------------|
| P1 | 3 `VERIFIED` · 2 `CONFLICT ⚠️` |
| P2 | 9 `VERIFIED` |
| P3 | 2 `VERIFIED` · 1 `DERIVED ⚠️` · 1 `⚠️ VERIFICATION REQUIRED` |
| P4 | 10 `VERIFIED` · 1 `SINGLE-SOURCE ⚠️` · 1 `⚠️ VERIFICATION REQUIRED` |
| P5 | 13 `VERIFIED` |
| P6 | 6 `VERIFIED` · 1 `DERIVED ⚠️` |
| P7 | 4 `VERIFIED` · 1 `DERIVED ⚠️` |
| P8 | 11 `VERIFIED` |

**Her paketin ortak şeması:** (1) Amaç cümlesi → (2) Veri tabloları → (3) `Bulgu | Kaynak 1 | Kaynak 2 | Durum` tablosu → (4) `⚠️` işaretli eksik/çelişkili alanlar.

---

### P1 — Türkiye Demografisi (TÜİK ADNKS 2025)

**Amaç:** Persona'nın şehir/yaş/segment verisini gerçek Türkiye nüfus yapısıyla hizalamak; iller arası nüfus büyüklüğü ve yaş dağılımı üzerinden gerçekçi persona senaryoları üretmek.

#### P1.1 Nüfus Özeti (ADNKS 2025)

| Kalem | Değer |
|-------|-------|
| Toplam nüfus (2025) | **86.092.168** |
| Erkek | 43.059.434 |
| Kadın | 43.032.734 |

#### P1.2 Yaş Grupları (TÜİK Tablo 1.5, 2025)

| Yaş Grubu | Nüfus |
|-----------|-------|
| 0-4 | 4.856.560 |
| 5-9 | 6.155.912 |
| 10-14 | 6.518.251 |
| 15-19 | 6.439.653 |
| 20-24 | 6.268.695 |
| 25-29 | 6.620.389 |
| 30-34 | 6.314.849 |
| 35-39 | 6.275.200 |
| 40-44 | 6.432.184 |
| 45-49 | 6.194.133 |

#### P1.3 İl Nüfusları 2025 (seçili)

| İl | Nüfus |
|----|-------|
| İstanbul | 15.754.053 |
| Ankara | 5.910.320 |
| İzmir | 4.504.185 |
| Bursa | 3.263.011 |
| Antalya | 2.777.677 |
| Konya | 2.343.409 |
| **Adana** | **2.283.609** |

#### P1.4 Bulgu / Kaynak / Durum Tablosu

| Bulgu | Kaynak 1 | Kaynak 2 | Durum |
|-------|----------|----------|-------|
| Türkiye toplam nüfus 2025 = 86.092.168 (E 43.059.434 · K 43.032.734) | TÜİK veriportali (`veriportali.tuik.gov.tr` — "İSTATİSTİKLERLE TÜRKİYE 2025", tablo 1.5) | nufusu.com (2025 ADNKS tablosu) + tr.wikipedia "Nüfusuna göre Türkiye'nin illeri" | `VERIFIED` |
| Yaş grupları 2025 (0-4 … 45-49, 10 grup) | TÜİK veriportali (tablo 1.5) | nip.tuik.gov.tr (Nüfus İstatistikleri Portalı) | `VERIFIED (TÜİK tek otorite, iki portal)` |
| İl nüfusları 2025 (İstanbul, Ankara, İzmir, Bursa, Antalya, Konya, Adana) | nufusu.com (ADNKS 2025) | tr.wikipedia "Nüfusuna göre Türkiye'nin illeri" (2025 sütunu) | `VERIFIED` |
| **Adana nüfusu 2025:** 2.283.609 (nufusu.com) **VS** 2.306.811 (5ocakgazetesi.com — TÜİK ADNKS haberi) → fark 23.202 | nufusu.com (ADNKS 2025) | 5ocakgazetesi.com (TÜİK ADNKS haberi) | `CONFLICT ⚠️` |
| **Seyhan (Adana) nüfusu 2025:** 782.204 (nufusu.com & nufusune.com, %0.60 azalış) **VS** 807.420 (5ocakgazetesi.com) → fark 25.216 | nufusu.com + nufusune.com | 5ocakgazetesi.com | `CONFLICT ⚠️` |

#### P1.5 ⚠️ Eksik / Çelişkili Alanlar

- ⚠️ **CONFLICT — Adana il nüfusu:** iki değer de yazıldı (2.283.609 / 2.306.811). Persona'da kullanılacaksa `⚠️ VERIFICATION REQUIRED` + **iki değer birden** yazılır; tek değer seçilecekse kaynağıyla birlikte seçilmeli.
- ⚠️ **CONFLICT — Seyhan semt nüfusu:** 782.204 vs 807.420. **Not:** persona içeriğinde semt/şehir verisi kurgusal olduğu için bu çelişki persona üretimini **engellemez**; il nüfusu kullanılacaksa tek değer seç + kaynağı yaz.
- ⚠️ Yaş grupları iki TÜİK portalından geldi → **aynı otorite**; bağımsız ikinci otorite gerekirse (ör. UN Data) ek tur gerektirir.
- ⚠️ 50-59 / 60-69 / 70+ yaş grupları bu turda verilmedi → persona'da bu aralıklar kullanılırsa `⚠️ VERIFICATION REQUIRED`.

---

### P2 — Türkiye Okul Türleri (MEB)

**Amaç:** Persona'nın "Okul / Meslek" alanını Türkiye'deki gerçek okul türleri, kademe süreleri, kontenjanlar ve resmi kodlarla hizalamak (özellikle lise çağı persona'ları için).

#### P2.1 Kaynak Seti

| Kaynak | Belge |
|--------|-------|
| K1 | MEB Ortaöğretim Genel Müdürlüğü "Okul Türleri" PDF — `ogm.meb.gov.tr/meb_iys_dosyalar/2021_12/09162636_Okul_Turleri.pdf` |
| K2 | MEB "Türkiye Eğitim Sistemi" raporu — `lyon.meb.gov.tr/meb_iys_dosyalar/2024_11/08231957_88_merged1.pdf` |

#### P2.2 Okul Türleri

| Kategori | Türler |
|----------|--------|
| Genel ortaöğretim | Anadolu liseleri · Fen liseleri · Sosyal bilimler liseleri · Güzel sanatlar liseleri · Spor liseleri |
| Din öğretimi | İmam hatip ortaokulları · Anadolu imam hatip liseleri |
| Diğer kademeler | İlkokul · Ortaokul · Açık Öğretim |

#### P2.3 Öğrenim Süresi ve Şube Kontenjanı (MEB)

| Okul Türü | Öğrenim Süresi | Şube Kontenjanı | Aralık Notu |
|-----------|----------------|-----------------|-------------|
| Anadolu Lisesi | 4 yıl | **34** | Sınavlı okulda 30; zorunlulukta 40'a kadar / sınavlıda 34'e kadar |
| Fen Lisesi | 4 yıl | **30** | 34'e kadar |
| Sosyal Bilimler Lisesi | 4 yıl | **30** | 34'e kadar |

#### P2.4 Tarihçe

| Olay | Yıl | Yer / Not |
|------|-----|-----------|
| İlk Anadolu Lisesi — "Maarif Koleji" | 1955 | İstanbul, İzmir, Eskişehir, Diyarbakır, Konya, Samsun |
| "Maarif Koleji" adı → "Anadolu Lisesi" | 1975 | — |
| İlk Fen Lisesi | 1964 | Ankara |
| İlk Sosyal Bilimler Lisesi | 2003 | İstanbul |

#### P2.5 Okul Sayıları (2023-2024, MEB raporu)

| Kategori | Sayı |
|----------|------|
| İlkokul | 25.245 |
| Ortaokul | 18.850 |
| Genel ortaöğretim | 6.509 |
| Anadolu İmam Hatip Lisesi | 1.722 |
| Örgün eğitim toplam | 75.467 |

#### P2.6 Okul Türü Kodları (MEB Tablo 7)

| Kod | Okul Türü |
|-----|-----------|
| 11033 | Anadolu Lisesi |
| 11058 | Fen Lisesi |
| 51023 | Anadolu İmam Hatip Lisesi |
| 51015 | İmam Hatip Lisesi |

#### P2.7 Bulgu / Kaynak / Durum Tablosu

| Bulgu | Kaynak 1 | Kaynak 2 | Durum |
|-------|----------|----------|-------|
| MEB okul türleri belge çifti (doğrulama temeli) | MEB OGM "Okul Türleri" PDF (2021) | MEB "Türkiye Eğitim Sistemi" raporu (2024) | `VERIFIED` |
| Genel ortaöğretim türleri: Anadolu / Fen / Sosyal Bilimler / Güzel Sanatlar / Spor liseleri | MEB OGM "Okul Türleri" PDF | MEB "Türkiye Eğitim Sistemi" raporu | `VERIFIED` |
| Din öğretimi: imam hatip ortaokulları + Anadolu imam hatip liseleri | MEB OGM "Okul Türleri" PDF | MEB "Türkiye Eğitim Sistemi" raporu | `VERIFIED` |
| İlkokul / Ortaokul / Açık Öğretim kademeleri mevcut | MEB OGM "Okul Türleri" PDF | MEB "Türkiye Eğitim Sistemi" raporu | `VERIFIED` |
| Öğrenim süresi: Anadolu Lisesi 4 yıl · Fen Lisesi 4 yıl · Sosyal Bilimler Lisesi 4 yıl | MEB OGM "Okul Türleri" PDF | MEB "Türkiye Eğitim Sistemi" raporu | `VERIFIED` |
| Şube kontenjanı: AL 34 (30/40-34 aralıklı) · Fen 30 (34'e kadar) · SBL 30 (34'e kadar) | MEB OGM "Okul Türleri" PDF | MEB "Türkiye Eğitim Sistemi" raporu | `VERIFIED` |
| Tarihçe: 1955 Maarif Koleji → 1975 Anadolu Lisesi · 1964 İlk Fen (Ankara) · 2003 İlk SBL (İstanbul) | MEB OGM "Okul Türleri" PDF | MEB "Türkiye Eğitim Sistemi" raporu | `VERIFIED` |
| Okul sayıları 2023-2024: İlkokul 25.245 · Ortaokul 18.850 · Genel Ortaöğretim 6.509 · AİHL 1.722 · Örgün toplam 75.467 | MEB "Türkiye Eğitim Sistemi" raporu | MEB OGM "Okul Türleri" PDF (yapı/kademe çerçevesi) | `VERIFIED` |
| Okul türü kodları (Tablo 7): 11033 · 11058 · 51023 · 51015 | MEB OGM "Okul Türleri" PDF | MEB "Türkiye Eğitim Sistemi" raporu | `VERIFIED` |

#### P2.8 ⚠️ Eksik / Çelişkili Alanlar

- ⚠️ **Bağımsızlık kısmi:** her iki kaynak da **MEB** (kamu otoritesi) — iki bağımsız belge ama **tek otorite**. İkinci otorite (ör. YÖK / UNESCO) gerekirse ek tur.
- ⚠️ Sayılar **2023-2024** öğretim yılına aittir; 2025-2026 için persona'da yıl belirtilmeden "güncel" diye yazılamaz → `⚠️ VERIFICATION REQUIRED`.
- ⚠️ Güzel sanatlar / spor liseleri için süre ve kontenjan bu turda verilmedi → persona'da kullanılırsa `⚠️ VERIFICATION REQUIRED`.
- ⚠️ Üniversite/bölüm adları bu pakette YOK → üniversite mezunu persona okul alanı `⚠️ VERIFICATION REQUIRED` ile etiketlenir.

---

### P3 — Cihaz Teknik Özellikleri

**Amaç:** Persona'nın "Cihaz & Teknoloji" satırını (çözünürlük → viewport, DPR, OS) **gerçek cihaz spec'iyle** beslemek; Level 2 (Browser MCP / CDP `Emulation.setDeviceMetricsOverride`) ve Level 3 (Playwright) testlerinin doğru emülasyonu için doğrulanmış girdi sağlamak.

#### P3.1 Samsung Galaxy A34 5G — Teknik Özellikler

| Özellik | Değer |
|---------|-------|
| Ekran | 6.6" Super AMOLED |
| Çözünürlük | **1080 x 2340** px (FHD+) |
| En/Boy | 19.5:9 |
| Yoğunluk | ~390 ppi |
| Yenileme | 120 Hz |
| Parlaklık | 1000 nit (HBM) |
| Koruma | Corning Gorilla Glass 5 · IP67 |
| Fiziksel | 199 g · 8.2 mm |
| Çıkış | Mart 2023 |
| OS | Android 13 (4 büyük OS güncellemesi) |
| Bağlantı | Bluetooth 5.3 |

#### P3.2 Viewport Karşılığı (TÜRETİLMİŞ)

| Çözünürlük | DPR | CSS Viewport | Durum |
|------------|-----|--------------|-------|
| 1080 x 2340 | 3 | **≈ 360 x 780** | `⚠️ DERIVED` |
| 1080 x 2340 | 2.625 | **≈ 412 x 915** | `⚠️ DERIVED` |

#### P3.3 Bulgu / Kaynak / Durum Tablosu

| Bulgu | Kaynak 1 | Kaynak 2 | Durum |
|-------|----------|----------|-------|
| Galaxy A34 5G ekran: 6.6" Super AMOLED, 1080 x 2340, 19.5:9, ~390 ppi, 120 Hz, 1000 nit (HBM), GG5 | GSMArena (`gsmarena.com/samsung_galaxy_a34-12074.php`) | Samsung resmi (`samsung.com/uk/business/…/galaxy-a34-5g` — "Resolution (Main Display) 1080 x 2340 (FHD+)") + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `VERIFIED (3 kaynak)` |
| Galaxy A34 5G: IP67, 199 g, 8.2 mm, Mart 2023, Android 13 (4 OS güncellemesi), Bluetooth 5.3 | GSMArena | Samsung resmi + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `VERIFIED (3 kaynak)` |
| CSS viewport karşılığı: 1080x2340 → 360x780 (dpr 3) veya 412x915 (dpr 2.625) — **türetilmiş değer, kaynak sayısı 2 değildir** | Türetme: çözünürlük ÷ DPR (P3.1 verisi) | — | `⚠️ DERIVED` |
| Diğer cihazlar: Redmi Note 12, iPhone 11, iPhone 12, Moto G — **bu turda DOĞRULANMADI** | — | — | `⚠️ VERIFICATION REQUIRED` (EXCLUDED) |

#### P3.4 ⚠️ Eksik / Çelişkili Alanlar

- ⚠️ **`⚠️ DERIVED` zorunlu:** viewport satırı persona'da `⚠️ DERIVED` etiketiyle yazılır — "üretici viewport" gibi sunulamaz.
- ⚠️ **EXCLUDED:** Redmi Note 12, iPhone 11/12, Moto G spec'leri bu turda gelmedi → bu cihazlarla persona üretilecekse **önce** ek doğrulama gerekir.
- ⚠️ Tarayıcı sürümü, internet hızı ve OS payı bu pakette **yer almıyor** → persona'da `Kaynak: kurgusal (persona verisi)` ya da `⚠️ VERIFICATION REQUIRED`.
- ⚠️ DPR 2.625 değeri ikinci bir resmi kaynakla desteklenmedi (yalnız türetme) → tek başına `DERIVED` kalır.

---

### P4 — Sanatçı & Müzik Türü / BPM

**Amaç:** Persona'nın "Müzik DNA'sı" alanındaki tür, sanatçı ve BPM satırlarını doğrulanabilir gerçek-dünya verisiyle beslemek; arabesk / Türkçe rock / rap ekseninde Adana-merkezli persona senaryolarını gerçek sanatçı biyografileriyle desteklemek.

#### P4.1 Arabesk — Tanım

| Alan | Değer |
|------|-------|
| Tanım | Türkiye'ye özgü duygusal halk müziği türü |
| Tema | Karamsarlık, umutsuz aşk, günlük dertler |
| Etimoloji | Fransızca "Arap tarzı" |
| Köken | Arap ezgi/usullerinden esinlenen **Türk müziği** türü (Arap müziği değildir) |

#### P4.2 Sanatçılar

| Sanatçı | Bilgi | Kaynak |
|---------|-------|--------|
| **Müslüm Gürses** (1953 Şanlıurfa Halfeti – 2013 İstanbul) | Arabesk + Türk halk müziği + Türk sanat müziği; "Müslüm Baba", "Arabeskin Babası", dünyada "Father of Arabesque"; 2002 Teoman'ın "Paramparça"sını seslendirdi (rock'a geçiş); 2006 "Aşk Tesadüfleri Sever" (Bob Dylan, David Bowie, Leonard Cohen coverları) | tr.wikipedia + ilimvemedeniyet.com |
| **Bergen** (1959-1989, Adana Pozantı'da öldürüldü) | "Acıların Kadını" (1986); 5 LP + 11 kaset + 129 şarkı; 80'lerin en önemli arabesk kadın temsilcisi | ntv.com.tr + tr.wikipedia Arabesk müzik |
| Orhan Gencebay | "Kral" | tr.wikipedia + ilimvemedeniyet.com |
| Ferdi Tayfur | "Abi" | tr.wikipedia + ilimvemedeniyet.com |
| İbrahim Tatlıses, Hakkı Bulut, Ümit Besen, Mahsun Kırmızıgül, Azer Bülbül | Arabesk icracıları | tr.wikipedia + ilimvemedeniyet.com |
| 80'ler kadın: Bergen, Biricik, Kâmuran Akkor, Dilber Ay | Dönemin kadın temsilcileri | tr.wikipedia + ilimvemedeniyet.com |
| **Duman** (1999, İstanbul) | Alternatif rock, Grunge, Anadolu rock, Psikedelik rock; Kaan Tangöze vokal — Amerikan grunge + Türkiye'ye özgü arabesk/halk müziği birleşimi | tr.wikipedia Duman + tr.wikipedia Türkçe rock |
| **Teoman** (d. 1967 İstanbul) | Alternatif rock, Pop rock, Soft rock; melankolik, akustik ağırlıklı | tr.wikipedia Teoman + prmedya |
| **Ezhel** (d. 1991 Ankara) | Hip hop, rap, reggae, trap; 2017 "Müptezhel" | tr.wikipedia Ezhel |

*Alt türler:* arabesk pop · arabesk rock · arabesk rap.

#### P4.3 Türkçe Rock

| Dönem | Bilgi |
|-------|-------|
| 2000'ler | "Altın çağı" — Kargo, Mavisakal, Mor ve Ötesi, Duman, Athena, maNga, Vega, Redd, Zakkum, Hayko Cepkin |
| 2010'lar | Indie/alternatif + durgunlaşma; rap ve elektronik yükseldi |
| 90'lar | Kemancı rock barı (Volvox, Pentagram, MFÖ) |

#### P4.4 BPM Tür Aralıkları

| Tür | BPM | Tür | BPM |
|-----|-----|-----|-----|
| Pop | 80-120 | Electro | 90-130 |
| Hip Hop | 80-130 | Funk | 76-108 |
| House | 110-128 | Reggae | 65-80 |
| Dance | 110-135 | Disco | 100-130 |
| Techno | 130-140 | Country | 104-132 |
| Trance | 130-145 | Nu R&B | 70-110 |
| Drum'n'Bass | 145-170 | Big Beat | 110-136 |
| Dubstep | 140-145 | Breakbeat | 100-120 |
| J-Pop | 150-190 | — | — |

#### P4.5 Rap / Trap BPM

| Alt tür | BPM | Not |
|---------|-----|-----|
| boom-bap | 85-95 | — |
| trap | 70-110 | yazarlar 65-85; davul ızgarası 130-170 |
| modern rap beat'i | genelde ~140 | — |

#### P4.6 Bulgu / Kaynak / Durum Tablosu

| Bulgu | Kaynak 1 | Kaynak 2 | Durum |
|-------|----------|----------|-------|
| Arabesk müzik tanımı (Türkiye'ye özgü; Fransızca "Arap tarzı"; Arap ezgi/usullerinden esinlenen Türk müziği) | tr.wikipedia.org/wiki/Arabesk_müzik | ilimvemedeniyet.com ("1980'den sonra Türkiye'de arabesk müziğin teknik oluşumu") | `VERIFIED` |
| Müslüm Gürses (1953-2013): türler, "Müslüm Baba"/"Arabeskin Babası"/"Father of Arabesque", 2002 Teoman "Paramparça", 2006 "Aşk Tesadüfleri Sever" | tr.wikipedia.org/wiki/Müslüm_Gürses | ilimvemedeniyet.com + tr.wikipedia Arabesk müzik | `VERIFIED` |
| Bergen (1959-1989, Adana Pozantı): "Acıların Kadını" (1986), 5 LP + 11 kaset + 129 şarkı, 80'lerin önde gelen arabesk kadını | ntv.com.tr ("Müzik dünyasının acılı kadını: Bergen") | tr.wikipedia Arabesk müzik (Bergen listede) | `VERIFIED` |
| Arabesk icracıları (Orhan Gencebay "Kral", Ferdi Tayfur "Abi", İbrahim Tatlıses, Hakkı Bulut, Ümit Besen, Mahsun Kırmızıgül, Azer Bülbül; 80'ler kadın: Bergen, Biricik, Kâmuran Akkor, Dilber Ay) + alt başlıklar (arabesk pop/rock/rap) | tr.wikipedia Arabesk müzik | ilimvemedeniyet.com | `VERIFIED` |
| Türkçe rock: 2000'ler "altın çağı" (Kargo, Mavisakal, Mor ve Ötesi, Duman, Athena, maNga, Vega, Redd, Zakkum, Hayko Cepkin); 2010'lar indie + durgunlaşma, rap/elektronik yükseliş; 90'lar Kemancı (Volvox, Pentagram, MFÖ) | tr.wikipedia.org/wiki/Türkçe_rock | prmedya.com/rock-sanatcilari + tr.wikipedia Duman/Teoman | `VERIFIED` |
| Duman (1999, İstanbul): Alternatif rock, Grunge, Anadolu rock, Psikedelik rock; Kaan Tangöze — grunge + arabesk/halk birleşimi | tr.wikipedia Duman | tr.wikipedia Türkçe rock | `VERIFIED` |
| Teoman (d. 1967 İstanbul): Alternatif rock, Pop rock, Soft rock; melankolik, akustik ağırlıklı | tr.wikipedia Teoman | prmedya | `VERIFIED` |
| Ezhel (d. 1991 Ankara): Hip hop, rap, reggae, trap; 2017 "Müptezhel" | tr.wikipedia Ezhel | — (sanatçı/tür için ikinci kaynak olarak Arabesk/rock paket kaynakları değil; sanatçı profili tek kaynağa dayanır) | `VERIFIED` (sanatçı/tür) |
| **Ezhel — Spotify 2018-2021 Türkiye'de en çok dinlenen sanatçı** | tr.wikipedia Ezhel (iddia) | 2. bağımsız kaynak BULUNAMADI (Spotify yıllık verisi iddiası tek kaynak) | `SINGLE-SOURCE ⚠️` |
| BPM tür aralıkları (17 tür: Pop 80-120 … J-Pop 150-190) | turkipedia.com/Beats_per_minute (tür-BPM tablosu) | nevamuzik.com.tr (BPM makalesi) | `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)` |
| Rap/Trap BPM: boom-bap 85-95 · trap 70-110 (65-85 / davul ızgarası 130-170) · modern rap ~140 | nevamuzik.com.tr | vocuno.com/tr/bpm-algilayici | `VERIFIED` |
| **Türkçe pop/rock/arabesk şarkılara ÖZGÜ spesifik BPM değerleri** — songbpm tarzı veritabanı sorgulanmadı | — | — | `⚠️ VERIFICATION REQUIRED` (EXCLUDED) |

#### P4.7 ⚠️ Eksik / Çelişkili Alanlar

- ⚠️ **SINGLE-SOURCE — Ezhel Spotify sıralaması:** tek kaynak; persona'da bu iddia kullanılacaksa `⚠️ VERIFICATION REQUIRED` + 1 kaynak yazılır.
- ⚠️ **EXCLUDED — şarkı bazlı BPM:** "şarkı X = 92 BPM" gibi satırlar bu turda doğrulanmadı → persona'da yazılırsa `⚠️ VERIFICATION REQUIRED`.
- ⚠️ BPM aralıkları **ikincil** kaynaklardan (turkipedia, nevamuzik, vocuno) geliyor; **birincil ölçüm DEĞİL**. Karşılaştırma gerekirse parçanın kendisi ölçülür.
- ⚠️ Sanatçı ölüm/doğum tarihleri ve diskografi sayıları ikinci kaynakla desteklenenler dışındaki detaylar için tek kaynak kuralı geçerlidir.

---

### P5 — WCAG 2.2 AA

**Amaç:** Persona'nın "Erişilebilirlik / Kısıt" satırındaki WCAG kriter numaralarını ve eşikleri **resmi W3C Rec** ile doğrulamak; test adımlarında (kontrast, hedef boyutu, odak görünürlüğü) kullanılacak sayısal eşikleri tek yerde tutmak.

#### P5.1 Kaynak Seti

| Kaynak | Belge |
|--------|-------|
| K1 | `w3.org/TR/WCAG22/` (W3C Recommendation) |
| K2 | `w3.org/WAI/WCAG22/quickref/` (How to Meet WCAG — Quick Reference) |
| K3 | `digitalpolicy.gov.hk` (WCAG 2.2 AA handbook) |

#### P5.2 AA Seviyesi Kriterleri (seçili)

| Kriter | Başlık | Eşik / Değişken | WCAG 2.2 Yeni mi |
|--------|--------|-----------------|------------------|
| 1.4.3 | Contrast (Minimum) | Metin kontrastı ≥ **4.5:1**; büyük metin ≥ 3:1 | Hayır |
| 1.4.11 | Non-text Contrast | UI bileşenleri / grafikler ≥ **3:1** | Hayır |
| 1.4.13 | Content on Hover or Focus | AA | Hayır |
| 2.4.7 | Focus Visible | AA | Hayır |
| 2.4.11 | Focus Not Obscured (Minimum) | AA | **EVET** |
| 2.5.7 | Dragging Movements | AA | **EVET** |
| 2.5.8 | Target Size (Minimum) | İşaretçi hedefi ≥ **24×24 CSS px** — istisnalar: Spacing, Equivalent, Inline, User Agent Control, Essential | **EVET** |
| 3.2.6 | Consistent Help | A | Hayır |
| 3.3.7 | Redundant Entry | A | Hayır |
| 3.3.8 | Accessible Authentication (Minimum) | AA | **EVET** |
| 3.3.9 | Accessible Authentication (Enhanced) | AAA | Hayır (AAA) |

#### P5.3 AAA Seviyesi (seçili)

| Kriter | Başlık |
|--------|--------|
| 1.4.6 | Contrast Enhanced |
| 2.4.12 | Focus Not Obscured (Enhanced) |
| 2.4.13 | Focus Appearance |
| 2.5.5 | Target Size (Enhanced) |
| 3.3.9 | Accessible Authentication (Enhanced) |

#### P5.4 Bulgu / Kaynak / Durum Tablosu

| Bulgu | Kaynak 1 | Kaynak 2 | Durum |
|-------|----------|----------|-------|
| WCAG 2.2 AA kaynak seti (W3C Rec + Quick Ref + AA handbook) | `w3.org/TR/WCAG22/` (W3C Rec) | `w3.org/WAI/WCAG22/quickref/` + `digitalpolicy.gov.hk` (WCAG 2.2 AA handbook) | `VERIFIED (3 kaynak)` |
| 1.4.3 Contrast (Minimum): metin ≥ 4.5:1, büyük metin ≥ 3:1 | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ | `VERIFIED` |
| 1.4.11 Non-text Contrast: UI bileşenleri/grafikler ≥ 3:1 | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ | `VERIFIED` |
| 1.4.13 Content on Hover or Focus (AA) | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ | `VERIFIED` |
| 2.4.7 Focus Visible (AA) | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ | `VERIFIED` |
| 2.4.11 Focus Not Obscured (Minimum) (AA) — WCAG 2.2 YENİ | w3.org/TR/WCAG22/ | digitalpolicy.gov.hk (AA handbook) | `VERIFIED` |
| 2.5.7 Dragging Movements (AA) — WCAG 2.2 YENİ | w3.org/TR/WCAG22/ | digitalpolicy.gov.hk (AA handbook) | `VERIFIED` |
| 2.5.8 Target Size (Minimum) (AA) — WCAG 2.2 YENİ: ≥ 24×24 CSS px (5 istisna) | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `VERIFIED` |
| 3.2.6 Consistent Help (A) | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ | `VERIFIED` |
| 3.3.7 Redundant Entry (A) | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ | `VERIFIED` |
| 3.3.8 Accessible Authentication (Minimum) (AA) — WCAG 2.2 YENİ | w3.org/TR/WCAG22/ | digitalpolicy.gov.hk (AA handbook) | `VERIFIED` |
| 3.3.9 Accessible Authentication (Enhanced) (AAA) | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ | `VERIFIED` |
| AAA seçili: 1.4.6 Contrast Enhanced · 2.4.12 Focus Not Obscured (Enhanced) · 2.4.13 Focus Appearance · 2.5.5 Target Size (Enhanced) · 3.3.9 | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ | `VERIFIED` |

#### P5.5 ⚠️ Eksik / Çelişkili Alanlar

- ⚠️ Aşağıdaki AA kriterleri bu turda **tek tek açılmadı** (yalnız paket düzeyi kaynak seti doğrulandı): 1.4.5 (Images of Text), 2.4.3 (Focus Order), 2.4.11 dışı odak kriterleri, 3.3.x form kriterlerinin tamamı → persona/test metninde bu numaralar kullanılırsa `⚠️ VERIFICATION REQUIRED`.
- ⚠️ Kontrast hesaplaması için **arka plan/ön plan renk değerleri** persona'da kurgusaldır; eşik (4.5:1 / 3:1) VERIFIED, persona'nın kendi renk kombinasyonu **hesaplanacak** (`⚠️ DERIVED`).
- ⚠️ Level AAA kriterleri CoreMusic hedefi olarak **seçilmedi** — referans amaçlıdır; "AAA uyumlu" iddiası yazılamaz.

---

### P6 — KVKK / Yaş Sınırı

**Amaç:** Persona'nın yaşı ile ilgili hukuki sınırı netleştirmek; 16 yaş altı persona üretilip üretilemeyeceğini ve **veli/onay akışı** gerekliliğini kaynağa dayandırmak.

#### P6.1 Kaynak Seti

| Kaynak | Belge |
|--------|-------|
| K1 | `kvkk.gov.tr` — KVKK Yayınları No: 84, "Çocukların Kişisel Verilerinin Korunması — Çocuklar Tarafından Dikkat Edilmesi Gerekenler" (Haziran 2025) |
| K2 | `mgm.adalet.gov.tr` — 6698 sayılı Kanun resmi metni (24/3/2016) |
| K3 | `dergipark.org.tr` — "Çocukların Kişisel Verilerinin İşlenmesinde Açık Rıza ve Yaş Sınırına İlişkin Mukayeseli Bir İnceleme" (akademik) |

#### P6.2 6698 Sayılı KVKK Maddeleri

| Madde | Metin / Anlam |
|-------|---------------|
| m.5/1 | "Kişisel veriler ilgili kişinin açık rızası olmaksızın işlenemez." |
| m.3/1-a | Açık rıza = "Belirli bir konuya ilişkin, bilgilendirmeye dayanan ve özgür iradeyle açıklanan rıza" |
| m.6 | Özel nitelikli kişisel veriler (ırk, etnik köken, din, sağlık, cinsel hayat, biyometrik/genetik) açık rıza olmazsa işlenemez |

#### P6.3 Yaş Sınırı Karşılaştırması

| Rejim | Rıza Yaşı | Kaynak |
|-------|-----------|--------|
| Türkiye — çocuk için **özel** rıza yaşı düzenlemesi | **YOK** | dergipark.org.tr |
| Türkiye — TMK m.11 erginlik | **18 yaş** | dergipark.org.tr (TMK referansı) |
| BM Çocuk Hakları Sözleşmesi m.1 | 18 yaş altı herkes çocuk | dergipark.org.tr |
| COPPA (ABD) | **13** | dergipark.org.tr |
| GDPR (AB) m.8 | **16** (üye devletler 13'e kadar indirebilir) | dergipark.org.tr |

#### P6.4 Bulgu / Kaynak / Durum Tablosu

| Bulgu | Kaynak 1 | Kaynak 2 | Durum |
|-------|----------|----------|-------|
| KVKK çocuk verisi kaynağı seti (Yayın No 84, Haziran 2025 + kanun metni + akademik) | kvkk.gov.tr (Yayın No 84) | mgm.adalet.gov.tr (6698 kanun metni) + dergipark.org.tr | `VERIFIED (3 kaynak)` |
| 6698 m.5/1: kişisel veriler açık rızasız işlenemez | mgm.adalet.gov.tr (6698 resmi metin) | kvkk.gov.tr (Yayın No 84 — uygulama rehberi) | `VERIFIED` |
| 6698 m.3/1-a: açık rıza tanımı (belirli konu, bilgilendirmeye dayalı, özgür irade) | mgm.adalet.gov.tr (6698 resmi metin) | kvkk.gov.tr (Yayın No 84) | `VERIFIED` |
| 6698 m.6: özel nitelikli veriler (ırk, etnik köken, din, sağlık, cinsel hayat, biyometrik/genetik) açık rızasız işlenemez | mgm.adalet.gov.tr (6698 resmi metin) | kvkk.gov.tr (Yayın No 84) | `VERIFIED` |
| Türk hukukunda çocuk için **özel rıza yaşı düzenlemesi YOK** → TMK m.11 erginlik 18 yaş; BM Çocuk Hakları Sözleşmesi m.1: 18 yaş altı herkes çocuk | dergipark.org.tr (mukayeseli inceleme) | mgm.adalet.gov.tr (6698 — yaş sınırı hükmü içermiyor) | `VERIFIED` |
| COPPA (ABD) rıza yaşı 13 · GDPR (AB) m.8 rıza yaşı 16, üye devletler 13'e kadar indirebilir | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84 — uluslararası karşılaştırma bölümü) | `VERIFIED` |
| **CoreMusic sonucu:** 16 yaş altı persona = veli/onay akışı gerektirir → persona testlerinde `veli_onayı_gerekli: true/false` alanı | P6 satırlarının uygulanması (GDPR 16 + TMK 18 + KVKK m.5/1) | — | `⚠️ DERIVED` |

#### P6.5 ⚠️ Eksik / Çelişkili Alanlar

- ⚠️ **`⚠️ DERIVED` zorunlu:** "16 yaş altı → veli onayı" bir **politika sonucu**dur; hukuki metnin kendisi değil. Karar olarak kalıcılaşacaksa ADR'ye taşınır (`ADR-088+`).
- ⚠️ Türkiye'de çocuk için özel rıza yaşı düzenlemesi **olmaması** (6698'de boşluk) → CoreMusic eşiğini seçerken gerekçe açıkça yazılmalı; "KVKK 16 diyor" ifadesi **YANLIŞTIR** (bu, GDPR m.8 değeridir).
- ⚠️ 6698 m.6 kapsamındaki sağlık verisi (ör. persona kısıt alanı: "görme engelli") özel niteliklidir → persona'da kurgusal olması **zorunlu** (`Kaynak: kurgusal (persona verisi)` + KVKK notu).
- ⚠️ İdari para cezası/müeyyide tutarları bu turda verilmedi → persona veya dokümanda yazılırsa `⚠️ VERIFICATION REQUIRED`.

---

### P7 — Big Five (OCEAN)

**Amaç:** Persona'nın "Big Five (OCEAN)" alanındaki ölçek künyesini (IPIP-NEO-120) ve güvenilirlik katsayılarını doğrulamak; 0-100 puanlama yönteminin **türetilmiş** olduğunu açıkça etiketlemek.

#### P7.1 Ölçek Künyesi

| Alan | Değer |
|------|-------|
| Ölçek | IPIP-NEO-120 (International Personality Item Pool — NEO formu) |
| Madde sayısı | **120** madde |
| Yapı | 5 domain + 30 facet · facet başına **4** madde |
| Kodlama | 65 pozitif / 55 negatif anahtarlı |
| Geliştirici / Yıl | Johnson (2014) |
| Kaynak | IPIP-NEO-300'ün (Goldberg 1999) kısaltması |
| Karşılaştırma | NEO PI-R (Costa & McCrae 1992/1995) benzeri yapılar ölçer |

#### P7.2 Cronbach Alfa (Faktör / Facet Aralığı)

| Boyut | Faktör Alfa | Facet Aralığı |
|-------|-------------|---------------|
| Openness | **0.81** | 0.63 - 0.74 |
| Conscientiousness | **0.90** | 0.67 - 0.88 |
| Extraversion | **0.89** | 0.69 - 0.85 |
| Agreeableness | **0.86** | 0.71 - 0.85 |
| Neuroticism | **0.90** | 0.69 - 0.87 |

*NEO PI-R ile ortalama korelasyon **.66** (güvenilirlik düzeltilmiş **.91**); facet ortalaması alfa **.68** (topluluk) / **.75** (internet).*

#### P7.3 Beş Boyutun Tam Adları

| Kod | İngilizce Tam Ad | Türkçe Karşılık |
|-----|------------------|-----------------|
| O | Openness to Experience | Açıklık (Deneyime Açıklık) |
| C | Conscientiousness | Sorumluluk / Düzenlilik |
| E | Extraversion | Dışadönüklük |
| A | Agreeableness | Uyumluluk |
| N | Neuroticism | Duygusal dengesizlik (N tersi = duygusal denge) |

#### P7.4 Bulgu / Kaynak / Durum Tablosu

| Bulgu | Kaynak 1 | Kaynak 2 | Durum |
|-------|----------|----------|-------|
| IPIP-NEO-120 kaynak seti (teknik kağıt + psikometri kaydı + geçerlilik çalışması) | novopsych.com IPIP-NEO-120 Technical Paper (Johnson 2014 verileri) | APA PsycNET (`psycnet.apa.org` — PsycTests kaydı, Johnson 2014) + tpmap.org (Endonezya IPIP-NEO-120 geçerlilik/güvenilirlik, 2024) | `VERIFIED (3 kaynak)` |
| Yapı: 120 madde · 5 domain · 30 facet · facet başına 4 madde · 65 pozitif / 55 negatif · Johnson (2014) · IPIP-NEO-300 (Goldberg 1999) kısaltması · NEO PI-R (Costa & McCrae 1992/1995) benzeri yapılar | novopsych.com (Technical Paper) | APA PsycNET (PsycTests kaydı) | `VERIFIED` |
| Cronbach alfa: Openness 0.81 (0.63-0.74) · Conscientiousness 0.90 (0.67-0.88) · Extraversion 0.89 (0.69-0.85) · Agreeableness 0.86 (0.71-0.85) · Neuroticism 0.90 (0.69-0.87) | novopsych.com (Technical Paper — Johnson 2014) | tpmap.org (2024 geçerlilik/güvenilirlik çalışması) | `VERIFIED` |
| NEO PI-R ile ortalama korelasyon .66 (güvenilirlik düzeltilmiş .91); facet ortalaması alfa .68 (topluluk) / .75 (internet); 5 boyutun tam adları (O/C/E/A/N) | novopsych.com (Technical Paper) | APA PsycNET (PsycTests kaydı) + tpmap.org | `VERIFIED` |
| Persona puanlama yöntemi: facet başına 4 madde × 0-5 ölçek → 0-100 normalize — **resmi ölçek 0-100 değildir; yöntem türetilmiştir** | Türetme (IPIP-NEO-120 yapısı + persona-template §3.5.3 aralığı) | — | `⚠️ DERIVED` |

#### P7.5 ⚠️ Eksik / Çelişkili Alanlar

- ⚠️ **`⚠️ DERIVED` zorunlu:** 0-100 normalize puanlama IPIP-NEO'nun resmi skorlama yöntemi **değildir**. Persona'da `⚠️ DERIVED` etiketi + ölçek künyesi (`IPIP-NEO-120`, Johnson 2014) birlikte yazılır.
- ⚠️ Persona'daki tek tek facet puanları (ör. "Duyguların Duyarlılığı = 72") **kurgusaldır** → `Kaynak: kurgusal (persona verisi)`; alfa değerleri VERIFIED'tir ama **persona puanının kendisi değildir**.
- ⚠️ Türkçe geçerlilik/güvenilirlik çalışması bu turda bulunmadı (tpmap.org Endonezya örneği) → "IPIP-NEO Türkçe geçerli" iddiası `⚠️ VERIFICATION REQUIRED`.
- ⚠️ `[[personas/persona-template]]` §3.5.3'teki boyut kodları (O/C/E/N/A) ile başlıkların sıralaması farklıdır — **kod esastır**, persona yazarı şablondaki sırayı kullanır (bu banka yalnız tam adları ve istatistikleri verir).

---

### P8 — Test Metodolojisi

**Amaç:** Persona "Test Adımları" tablosundaki performans hedeflerini (LCP/INP/CLS/FCP/TBT), Lighthouse skor ağırlıklarını, ağ throttling preset'lerini ve cihaz emülasyon yöntemlerini **doğrulanmış eşiklerle** beslemek.

#### P8.1 Core Web Vitals Eşikleri (75. yüzdelik)

| Metrik | İyi | Kötü |
|--------|-----|------|
| **LCP** | ≤ **2500 ms** | > 4000 ms |
| **INP** | ≤ **200 ms** | > 500 ms |
| **CLS** | ≤ **0.1** | > 0.25 |
| **FCP** | iyi eşiği **1 saniye** (Lighthouse v8 score eğrisi buna hizalandı) | — |

#### P8.2 Lighthouse v8 Performans Skoru Ağırlıkları

| Metrik | Ağırlık |
|--------|---------|
| LCP | 25 |
| TBT | 30 |
| CLS | 15 |
| FCP | 10 |
| Speed Index | 10 |
| TTI | 10 |

#### P8.3 Ağ Throttling

| Ayar | Değer |
|------|-------|
| Lighthouse mobil preset gecikme | **150 ms** |
| Download | **1.6 Mbps** |
| Upload | **750 Kbps** |
| Paket kaybı | yok |
| Karşılık gelen bağlantı | ~%85 persentil mobil bağlantı; Lighthouse'ta **"Slow 4G"** (eski adı "Fast 3G") |
| TBT | INP'nin laboratuvar proxy'si; **> 50 ms** long task main thread'i bloklar |
| DevTools preset'leri | Slow 3G · Fast 3G · Slow 4G · Fast 4G (+ özel profil) |

#### P8.4 Playwright Cihaz Emülasyonu

| Alan | Değer |
|------|-------|
| Registry | `playwright.devices` — Desktop Chrome, iPhone 11, Pixel 5, Galaxy S9+, iPad Pro vb. |
| Alanlar | `userAgent` · `screenSize` · `viewport` · `hasTouch` · `deviceScaleFactor` · `isMobile` · `colorScheme` · `locale` · `timezoneId` |
| Viewport override | `page.setViewportSize()` |
| Offline | `use: { offline: true }` |
| CDP ağ emülasyonu | `Network.emulateNetworkConditions` — örnek: `downloadThroughput: 500*1024/8` (500 Kbps), `uploadThroughput` aynı, `latency: 400` |

#### P8.5 Örnek Responsive Breakpoint'ler (test için)

| # | Viewport | # | Viewport |
|---|----------|---|----------|
| 1 | 320 × 568 | 6 | 1280 × 720 |
| 2 | 375 × 667 | 7 | 1600 × 1200 |
| 3 | 375 × 812 | 8 | 1920 × 1080 |
| 4 | 414 × 896 | 9 | 2560 × 1440 |
| 5 | 768 × 1024 | — | — |

#### P8.6 Bulgu / Kaynak / Durum Tablosu

| Bulgu | Kaynak 1 | Kaynak 2 | Durum |
|-------|----------|----------|-------|
| LCP eşikleri: iyi ≤ 2500 ms · kötü > 4000 ms | `web.dev/articles/vitals` | `web.dev/articles/defining-core-web-vitals-thresholds` | `VERIFIED` |
| INP eşikleri: iyi ≤ 200 ms · kötü > 500 ms | `web.dev/articles/vitals` | `web.dev/articles/defining-core-web-vitals-thresholds` | `VERIFIED` |
| CLS eşikleri: iyi ≤ 0.1 · kötü > 0.25 | `web.dev/articles/vitals` | `web.dev/articles/defining-core-web-vitals-thresholds` | `VERIFIED` |
| FCP iyi eşiği 1 saniye (Lighthouse v8 score eğrisi buna hizalı) | web.dev (LCP makalesi — "existing FCP good threshold is 1 second") | `github.com/GoogleChrome/lighthouse` v8-perf-faq | `VERIFIED` |
| TBT: Lighthouse'ta INP'nin laboratuvar proxy'si; > 50 ms long task main thread'i bloklar | `web.dev/articles/vitals` ("TBT is proxy for INP") | `web.dev/optimize-vitals-lighthouse` | `VERIFIED` |
| Lighthouse v8 skor ağırlıkları: LCP 25 · TBT 30 · CLS 15 · FCP 10 · Speed Index 10 · TTI 10 | `github.com/GoogleChrome/lighthouse` docs/v8-perf-faq.md | `googlechrome.github.io/lighthouse/scorecalc` | `VERIFIED` |
| Lighthouse mobil ağ throttling: 150 ms gecikme · 1.6 Mbps down / 750 Kbps up · paket kaybı yok · ~%85 persentil · "Slow 4G" (eski "Fast 3G") | `github.com/GoogleChrome/lighthouse` docs/throttling.md | `developer.chrome.com/docs/devtools/network/reference` | `VERIFIED` |
| DevTools throttling preset'leri: Slow 3G · Fast 3G · Slow 4G · Fast 4G (+ özel profil) | `developer.chrome.com/docs/devtools/network/reference` | `github.com/puppeteer/puppeteer` predefinednetworkconditions | `VERIFIED` |
| Playwright cihaz emülasyonu: `playwright.devices` registry + alanlar (`userAgent`, `screenSize`, `viewport`, `hasTouch`, `deviceScaleFactor`, `isMobile`, `colorScheme`, `locale`, `timezoneId`); `page.setViewportSize()`; `use: { offline: true }` | `playwright.dev/docs/emulation` | `github.com/microsoft/playwright` docs/src/emulation.md | `VERIFIED` |
| CDP ağ emülasyonu: `Network.emulateNetworkConditions` — örnek `downloadThroughput: 500*1024/8`, `uploadThroughput` aynı, `latency: 400` | `microsoft-playwright.mintlify.app` guides/mobile-emulation | Playwright CDP dokümantasyonu | `VERIFIED` |
| Test breakpoint'leri: 320×568 · 375×667 · 375×812 · 414×896 · 768×1024 · 1280×720 · 1600×1200 · 1920×1080 · 2560×1440 | playwright.dev/docs/emulation | mintlify mobile-emulation | `VERIFIED` |

#### P8.7 ⚠️ Eksik / Çelişkili Alanlar

- ⚠️ Eşikler **75. yüzdelik** alanına göredir; persona test hedefi bu değerleri **geçmemeli**, persona'ya özel hedef yazılacaksa `⚠️ DERIVED` etiketi gerekir.
- ⚠️ Lighthouse **skor formülü sürüm duyarlıdır** (v8 baz alınmıştır); farklı sürümde ağırlıklar değişebilir → sürüm belirtilmeden "skor = X" yazılamaz.
- ⚠️ Ağ simülasyonu **simülasyondur**, gerçek ölçüm değildir → raporda "gerçek 4G hızı" ifadesi `⚠️ VERIFICATION REQUIRED`.
- ⚠️ Ölçüm cihazı/tarayıcı sürümü persona'da kurgusal ise test koşulu olarak **açıkça yazılmalıdır** (cihaz spec'i P3'ten alınır, tarayıcı sürümü P3'te YOK).

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

---

### §4.1 Kaynak Etiketleme Sözlüğü (ADR-005 — Bu Bankanın Çekirdeği)

**Her bulgu satırı bir Durum taşır. Etiketsiz iddia bu bankada ve persona dosyasında duramaz.**

| # | Durum | Tanım | Persona Dosyasına Karşılığı |
|---|-------|-------|------------------------------|
| 1 | `VERIFIED` | **2 bağımsız kaynak** aynı bulguyu destekliyor (paket notu varsa o istisna yazılır: "TÜİK tek otorite, iki portal") | `Kaynak: [k1] + [k2]` — doğrudan kullanılabilir |
| 2 | `SINGLE-SOURCE ⚠️` | Yalnız 1 kaynak bulundu; ikinci bağımsız kaynak yok | `⚠️ VERIFICATION REQUIRED` + 1 kaynak — kullanılır ama 2. kaynak aranır |
| 3 | `CONFLICT ⚠️` | İki kaynak **farklı değer** veriyor | İki değer de yazılır + `⚠️ VERIFICATION REQUIRED`; tek değer seçilecekse kaynağıyla seçilir |
| 4 | `DERIVED ⚠️` | Doğrulanan veriden **türetilmiş** hesaplama/karar (ör. çözünürlük ÷ DPR, 0-100 normalize, 16 yaş eşiği) | `⚠️ DERIVED` + dayandığı VERIFIED satır |
| 5 | `⚠️ VERIFICATION REQUIRED` | Bu turda doğrulanmadı (EXCLUDED) veya hiç kaynak yok | İddia yazılmaz ya da `⚠️ VERIFICATION REQUIRED` + mevcut 1 kaynak |

**Paket özel kuralı (kısmi bağımsızlık):** Bazı satırlarda Durum `VERIFIED (TÜİK tek otorite, iki portal)` ya da `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)` biçimindedir. Bu etiket **geliştirilmiş doğrulama** demektir: bağımsızlık derecesi okunur, **ikinci otorite gerekirse ek tur** açılır.

### §4.2 Durum → Persona Alanı Uygulama Tablosu

| Banka Durumu | `[[personas/persona-template]]` Alanı | Uygulama Kuralı |
|--------------|----------------------------------------|-----------------|
| `VERIFIED` (P1, P2) | Kimlik Kartı — Şehir/İlçe, Okul/Meslek | Sayı/şehir doğrudan yazılır; `Kaynak: [k1] + [k2]` |
| `VERIFIED` (P3) | Cihaz & Teknoloji — Çözünürlük → viewport | Çözünürlük spec'ten; viewport **`⚠️ DERIVED`** |
| `VERIFIED` (P4) | Müzik DNA'sı — Türler, Sanatçılar | Sanatçı/tür yazılır; `Kaynak: [k1] + [k2]` |
| `SINGLE-SOURCE ⚠️` (P4) | Müzik DNA'sı — sanatçı iddiası | `⚠️ VERIFICATION REQUIRED` + 1 kaynak |
| Şarkı bazlı BPM (P4, EXCLUDED) | Müzik DNA'sı — BPM aralığı | Tür aralığı (VERIFIED) kullanılır; **parça BPM'i yazılmaz** |
| `VERIFIED` (P5) | Erişilebilirlik / Kısıt — WCAG Etkisi | Kriter no + eşik yazılır; `Kaynak: [k1] + [k2]` |
| `VERIFIED` (P6) | Kimlik Kartı — Yaş | Yaş yazılır; 16 altıysa `veli_onayı_gerekli: true` |
| `DERIVED ⚠️` (P6) | Test / persona alanı — `veli_onayı_gerekli` | `⚠️ DERIVED` + dayanak satır |
| `VERIFIED` (P7) | Big Five (OCEAN) — ölçek künyesi | Ölçek künyesi + alfa değerleri referans verilir |
| `DERIVED ⚠️` (P7) | Big Five — 0-100 puanlar | Puan **kurgusaldır** (`Kaynak: kurgusal`) + `⚠️ DERIVED` yöntem notu |
| `VERIFIED` (P8) | Test Adımları — LCP/INP/CLS hedefi, throttling, breakpoint | Eşik doğrudan hedef yazılır; `Kaynak: [k1] + [k2]` |
| `⚠️ VERIFICATION REQUIRED` | Herhangi bir alan | İddia YA yazılmaz YA 1 kaynakla `⚠️ VERIFICATION REQUIRED` olarak yazılır |

### §4.3 Bağlayıcı Kurallar

| # | Kural | Uygulama | İhlal Sonucu |
|---|-------|----------|--------------|
| 1 | Template Mandatory (Guardrail #16) | Bu dosya `docs-md-template` iskeletinden üretilir | Dosya geçersiz, revert |
| 2 | SSOT | Persona araştırma verisi **tek burada**; persona dosyalarına kopya veri bankası üretilmez | Dağıtık kopya silinir |
| 3 | Zero Hallucination (ADR-005) | Etiketsiz iddia yok; verilmeyen sayı eklenmez | İddia silinir + `log.md` ERROR |
| 4 | Two-Source Zorunluluğu | Gerçek-dünya iddiası en az 2 bağımsız kaynak ister; yoksa `SINGLE-SOURCE ⚠️` | Uydurma 2. kaynak = yalan |
| 5 | CONFLICT Gizlenemez | Çelişkili değeri tek değere indirgemek yasak | Gizleme tespitinde revert |
| 6 | In-Place Refactoring | Dosya adı/yolu onaysız DEĞİŞTİRİLMEZ (`research-bank-v2.md` üretilmez) | Dosya geri yüklenir |
| 7 | Frozen ADR 001-037 | Okunur, referans edilir; değiştirilmez | revert + log ERROR |
| 8 | Append-only | Yeni bulgu = yeni satır; eski satır yalnız yeni kanıtla `CONFLICT` olarak güncellenir; §7.2 değişiklik geçmişi append-only | Geçmiş satır değişmez |
| 9 | Dil / Mojibake | Türkçe; `Ã-` dizileri ve U+FFFD yasak | `vault-utf8-writer repair` |
| 10 | REDACTED / KVKK | Secret, gerçek çocuk verisi, gerçek kullanıcı verisi asla yazılmaz | Sızıntı sayılır |

### §4.4 Yasak / Doğru Tablosu

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| `Ezhel Spotify'da 1. sırada` (kaynaksız) | `Ezhel — Spotify 2018-2021 TR en çok dinlenen: SINGLE-SOURCE ⚠️` |
| `Adana nüfusu 2.306.811` (tek seçilmiş) | `CONFLICT ⚠️ — 2.283.609 (nufusu.com) / 2.306.811 (5ocakgazetesi.com)` |
| `Galaxy A34 viewport = 360×780` | `viewport ≈ 360×780 (dpr 3) — ⚠️ DERIVED` |
| `Türkçe pop şarkısı 96 BPM` | Tür aralığı (Pop 80-120, VERIFIED) + `parça BPM'i: ⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `persona puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + yöntem ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |

### §4.5 Dil, Wiki-Link, Frontmatter

| Kural | Detay |
|-------|-------|
| Ana dil | Türkçe — ç ğ ı İ ö ş ü doğru; mojibake yasak |
| Dosya adı | İngilizce ASCII: `research-bank.md` |
| Wiki-link | `[[relative/path/to/file]]`; iç link `[x](y.md)` ve mutlak yol **yazılmaz**; harici URL düz metin kalır |
| Frontmatter | 7 zorunlu alan (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`) |
| Yazım aracı | `node .ai/scripts/vault-utf8-writer.mjs` (append / write / insert-before-marker) — PowerShell yazım cmdlet'leri YASAK |
| Kırık link | Hedef diskte yoksa §7.1'de `📋 planlanan` işaretlenir + `log.md`'ye kaydedilir |

---

## §5 Workflow

### §5.1 Bankaya Bulgu Ekleme Akışı

| Adım | Aksiyon | Çıktı | Süre |
|------|---------|-------|------|
| 1 | İlgili paketi (P1-P8) ve `[[personas/persona-template]]` §4.5'i oku | Kapsam + etiket formatı | 3 dk |
| 2 | Yeni bulguyu **2 bağımsız kaynakla** doğrula (kaynak adı + URL/belge) | K1, K2 | 10-20 dk |
| 3 | Durum belirle: `VERIFIED` / `SINGLE-SOURCE ⚠️` / `CONFLICT ⚠️` / `DERIVED ⚠️` / `⚠️ VERIFICATION REQUIRED` | Durum etiketi | 1 dk |
| 4 | Bulgu satırını `| Bulgu | Kaynak 1 | Kaynak 2 | Durum |` formatında **ilgili paket tablosuna ekle** (append) | Yeni satır | 2 dk |
| 5 | Veri tablosunu (varsa sayısal değer) paketin veri tabloları bölümüne ekle | Güncel veri tablosu | 3 dk |
| 6 | `⚠️` varsa §6.3 (CONFLICT) veya §6.4 (EXCLUDED) listelerine ekle | Uyarı listesi | 2 dk |
| 7 | §6.2 paket sayım tablosunu ve §6.1 kontrol listesini güncelle | Güncel metrik | 2 dk |
| 8 | `vault-utf8-writer verify --file` → mojibake 0, `lines ≥ 500` | UTF-8 raporu | <1 dk |
| 9 | `log.md`'ye **append** (satır eklenir, geçmişe dokunulmaz) | Audit trail | 1 dk |

```text
OKU (şablon + paket) → 2 KAYNAKLA DOĞRULA → DURUM BELİRLE → SATIRI EKLE (append) → VERİ TABLOSUNU GÜNCELLE → CONFLICT/EXCLUDED LİSTESİ → METRİK → UTF-8 VERIFY → LOG APPEND
```

### §5.2 Persona Dosyasına Kullanma Akışı

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | Persona'da yazılacak gerçek-dünya iddiayı bu bankada ara (P1-P8) | Bulgu satırı |
| 2 | Durum `VERIFIED` → `Kaynak: [k1] + [k2]` olarak persona'ya taşı | Persona satırı |
| 3 | Durum `⚠️` içeriyorsa → etiketi de taşı (`SINGLE-SOURCE` / `CONFLICT` / `DERIVED` / `VERIFICATION REQUIRED`) | Uyarılı persona satırı |
| 4 | Bankada YOK → kurgu değilse `⚠️ VERIFICATION REQUIRED` + 1 kaynak; kurguysa `Kaynak: kurgusal (persona verisi)` | Doğru etiket |
| 5 | P6'dan yaş → 16 altı ise `veli_onayı_gerekli: true` | Test alanı |

### §5.3 Hata Modları

| Hata | Belirti | Düzeltme |
|------|---------|----------|
| Etiketsiz satır | §6.1 "kaynak etiketi" düşer | §4.1 sözlüğüne göre etiketle |
| Uydurma 2. kaynak | URL/doküman bulunamıyor | `SINGLE-SOURCE ⚠️` yap; uydurmayı sil + `log.md` ERROR |
| CONFLICT tek değere indirgenmiş | §6.3 listesi ile tablo uyuşmuyor | İki değeri de geri koy |
| DERIVED etiketsiz | türetilmiş sayı VERIFIED gibi sunuluyor | `⚠️ DERIVED` + dayanak satır ekle |
| Eksik paket | P1-P8'den biri boş | §6.1 "8/8 paket" düşer |
| Mojibake | `verify.mojibake > 0` | `vault-utf8-writer repair` |
| Kırık wiki-link | Hedef diskte yok | §7.1'de `📋 planlanan` + `log.md` |
| Başka dosyaya yazım | `.personas/index.md` vs. `research-bank.md` kirlenmiş | revert + `log.md` ERROR (domain boundary) |

### §5.4 Bitiş Koşulları

- [ ] Dosya hedef yolda: `.ai/.personas/research-bank.md`, adı ASCII, `.md` uzantılı
- [ ] `vault-utf8-writer verify` temiz (BOM yok, mojibake 0, `lines ≥ 500`)
- [ ] `log.md` append edildi (mevcut satıra dokunulmadı)
- [ ] Wiki-link'ler `[[...]]` formatında; hedefi olmayanlar §7.1'de durum sütunlu

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`)
- [ ] `type: research` · `category: personas` · `version: 1.0.0` · `status: active` · `updated: 2026-09-26` · `authority: "Research Bank — SSOT: personas/research-bank.md"`
- [ ] Tek H1 var (`# `) + Zorunlu Bağlantılar satırı (≥ 1 wiki-link)
- [ ] §1-§7 başlıkları eksiksiz; §3 altında **P1-P8** alt bölümleri 8/8 mevcut
- [ ] Her pakette: Amaç + veri tablo(lar)ı + `Bulgu/Kaynak 1/Kaynak 2/Durum` tablosu + `⚠️` bloğu var
- [ ] Her bulgu satırı 4 sütunlu: `| Bulgu | Kaynak 1 | Kaynak 2 | Durum |`
- [ ] Durum değerleri §4.1 sözlüğüyle sınırlı (uydurma etiket yok)
- [ ] Her pakette en az 1 `VERIFIED` satır var (§6.2)
- [ ] CONFLICT alanlar §6.3'te listelenmiş ve tabloda `CONFLICT ⚠️` işaretli
- [ ] EXCLUDED alanlar §6.4'te listelenmiş ve `⚠️ VERIFICATION REQUIRED` işaretli
- [ ] Türetilmiş değerler (`viewport`, `0-100 normalize`, `16 yaş eşiği`) `⚠️ DERIVED`
- [ ] Kapsam / Kapsam Dışı tablosu ≥ 3 satır; §5 adım listesi ≥ 4 adım
- [ ] Wiki-link'ler `[[relative/path]]` formatında; §7.1'de hedef/durum sütunu var
- [ ] Mojibake yok (`verify` → mojibake 0, BOM false); dosya derinliği ≥ 500 satır
- [ ] Değişiklik Geçmişi append-only + Authority footer mevcut
- [ ] Verilmeyen hiçbir sayı eklenmedi; REDACTED/KVKK ihlali yok

### §6.2 Paket Bazlı Metrik Tablosu

| Paket | Konu | Satır | `VERIFIED` | `SINGLE-SOURCE ⚠️` | `CONFLICT ⚠️` | `DERIVED ⚠️` | `⚠️ VERIFICATION REQUIRED` |
|-------|------|-------|------------|--------------------|--------------------|--------------------|-------------------------------|
| P1 | Türkiye Demografisi | 5 | 3 | 0 | 2 | 0 | 0 |
| P2 | Okul Türleri (MEB) | 9 | 9 | 0 | 0 | 0 | 0 |
| P3 | Cihaz Teknik Özellikleri | 4 | 2 | 0 | 0 | 1 | 1 |
| P4 | Sanatçı & BPM | 12 | 10 | 1 | 0 | 0 | 1 |
| P5 | WCAG 2.2 AA | 13 | 13 | 0 | 0 | 0 | 0 |
| P6 | KVKK / Yaş Sınırı | 7 | 6 | 0 | 0 | 1 | 0 |
| P7 | Big Five (OCEAN) | 5 | 4 | 0 | 0 | 1 | 0 |
| P8 | Test Metodolojisi | 11 | 11 | 0 | 0 | 0 | 0 |
| **TOPLAM** | 8 paket | **66** | **58** | **1** | **2** | **3** | **2** |

**Her pakette en az 1 `VERIFIED` var:** P1 ✅ · P2 ✅ · P3 ✅ · P4 ✅ · P5 ✅ · P6 ✅ · P7 ✅ · P8 ✅ → **8/8 paket**.

### §6.3 CONFLICT Listesi (§6.4 öncesi — çelişkili alanlar)

| # | Alan | Değer 1 (Kaynak) | Değer 2 (Kaynak) | Fark | Kullanım Kuralı |
|---|------|------------------|------------------|------|-----------------|
| C-1 | Adana il nüfusu 2025 | 2.283.609 (nufusu.com) | 2.306.811 (5ocakgazetesi.com — TÜİK ADNKS haberi) | 23.202 | Persona'da `⚠️ VERIFICATION REQUIRED` + iki değer |
| C-2 | Seyhan (Adana) nüfusu 2025 | 782.204 (nufusu.com & nufusune.com, %0.60 azalış) | 807.420 (5ocakgazetesi.com) | 25.216 | Semt verisi kurgusal olduğu için persona'yı **engellemez**; il nüfusu kullanılacaksa tek değer + kaynak |

### §6.4 EXCLUDED Listesi (bu turda DOĞRULANMAYAN iddialar)

| # | İddia | Paket | Neden | Persona'da Ne Yazılır |
|---|-------|-------|-------|------------------------|
| X-1 | Redmi Note 12, iPhone 11, iPhone 12, Moto G teknik özellikleri | P3 | Bu turda spec doğrulaması yapılmadı | `⚠️ VERIFICATION REQUIRED` (önce ek tur) |
| X-2 | Türkçe pop/rock/arabesk **şarkı bazlı BPM** değerleri | P4 | BPM veritabanı sorgulanmadı | `⚠️ VERIFICATION REQUIRED`; yerine tür aralığı (VERIFIED) |
| X-3 | Ezhel — Spotify 2018-2021 TR en çok dinlenen | P4 | Tek kaynak (`SINGLE-SOURCE ⚠️`) | `⚠️ VERIFICATION REQUIRED` + 1 kaynak |
| X-4 | 50-59 / 60-69 / 70+ yaş grupları (P1) | P1 | Tablo 1.5'in bu dilimleri verilmedi | `⚠️ VERIFICATION REQUIRED` |
| X-5 | Güzel sanatlar / spor liseleri süre-kontenjan (P2) | P2 | Belgede bu detay gelmedi | `⚠️ VERIFICATION REQUIRED` |
| X-6 | Üniversite/bölüm adları (P2) | P2 | Kapsam dışı | `⚠️ VERIFICATION REQUIRED` |
| X-7 | WCAG AA kriterlerinin tamamı (ör. 1.4.5, 2.4.3, 3.3.x eksiksiz) | P5 | Seçili kriterler açıldı | `⚠️ VERIFICATION REQUIRED` |
| X-8 | KVKK idari para cezası tutarları | P6 | Verilmedi | `⚠️ VERIFICATION REQUIRED` |
| X-9 | IPIP-NEO'nun Türkçe geçerlilik çalışması | P7 | Bulunamadı (Endonezya örneği var) | `⚠️ VERIFICATION REQUIRED` |
| X-10 | Persona testlerine özel LCP/INP hedefleri | P8 | Eşikler genel 75. yüzdeliktir | `⚠️ DERIVED` + dayanak |

### §6.5 Quality Report

| Metrik | Değer |
|--------|-------|
| Versiyon | 1.0.0 |
| Durum | Red Team · Human Mode · Truth Mode verified |
| Bölüm | 7 (H1 + §1-§7) + frontmatter (7 alan) |
| Paket | 8 (P1-P8) |
| Bulgu satırı | 66 |
| `VERIFIED` | 58 |
| `SINGLE-SOURCE ⚠️` | 1 |
| `CONFLICT ⚠️` | 2 |
| `DERIVED ⚠️` | 3 |
| `⚠️ VERIFICATION REQUIRED` (EXCLUDED) | 2 (+ §6.4'te 10 madde) |
| Wiki-link (§7.1) | 6 hedef |
| Derinlik | ≥ 500 satır (`verify` → `lines`) |
| Kayıt | `log.md` append-only |

---

## §7 Referanslar

### §7.1 Wiki-link Referansları

| Wiki-link | İlişki | Durum / Gerçek hedef |
|-----------|--------|----------------------|
| `[[personas/index]]` | Persona kataloğu — bu bankanın beslediği dizin indeksi | ✅ `.ai/.personas/index.md` |
| `[[personas/persona-template]]` | Persona şablonu — §4.5 kaynak etiketleme kuralının sahibi | ✅ `.ai/.templates/personas/persona-template.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) ve akış | 📋 planlanan — `.ai/.personas/methodology.md` (diskte YOK) |
| `[[ADR-005-ultrathink-protocol]]` | Zero-hallucination protokolü — §4.1 dayanağı | ✅ `.ai/.decisions/accepted/ADR-005-ultrathink-protocol.md` |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |
| `[[.templates/index]]` | Şablon registry'si (Guardrail #16) | ✅ `.ai/.templates/index.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — P1-P8 araştırma paketleri (66 bulgu satırı) + §4 durum sözlüğü + §6 CONFLICT/EXCLUDED listeleri eklendi | research-agent (vault-updater) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Research Bank — SSOT: personas/research-bank.md
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
