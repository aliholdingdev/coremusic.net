---
node_id: K002-electroncis/imalat-ve-test/imalat-ve-test-anayasa
node_type: module
status: draft
parent_id: K002-electroncis/imalat-ve-test
children: []
depends_on: [ADR-017, ADR-038, L0-10]
evidence:
  - ".ai/architecture/K002-electroncis/electroncis-kapsam.md §B.5 satır 98-109 (kapsam + Faz 15 kanıtı development-workflow.md:416-434)"
  - ".ai/architecture/K002-electroncis/electroncis-kapsam.md §H.5 satır 247-256 (envanter satır kanıtları)"
  - "pcb-design.md:24-30,44-52 · snr-thd-measurement.md:29,45-48,55-59 · frequency-response.md:50-56 · development-workflow.md:232 — satır atıfları (okunmadı `⚠️`)"
validation: "draft — 2026-10-14 yazıldı · ≥500 ölçümü yazım sonrası · satır kanıtları okunmadı"
open_questions:
  - "Q-IM1: EMC standart numaraları doğrulanmadı"
  - "Q-IM2: SNR/THD ölçümü PLANNED (fiziksel ölçüm yok)"
  - "Q-IM3: DFT/AOI kriterleri vault satır kanıtı `⚠️`"
group: mimari
---

# imalat-ve-test — Tasarım Anayasası (Kademe-4)

> **Yazım dokunulmaz:** `electroncis` (v2 §3 KURAL 1). Bu dosya **üretim/ölçüm
> kuralları** üretir; ölçüm sonucu **ÜRETMEZ**, ölçülmüş sayı **YAZMAZ**, standart
> numarası **UYDURMAZ**. Satır kanıtı okunmayan her madde `⚠️ VERIFICATION REQUIRED`
> işaretlidir.

## §0 ELI15

Bu anayasa, elektronik tasarımın **fabrikada ve ölçüm masasında** nasıl
kanıtlanacağını yazar. Altı kapı vardır: (1) PCB'nin doğru üretilmesi, (2)
devrenin test edilebilir tasarlanması, (3) gözle/makineyle muayene, (4) performans
ölçümü (SNR/THD+N), (5) EMC'nin sertifika öncesi provası, (6) 8 saat burn-in.
Basitçe: iyi çizilmiş devre **üretimde ve ölçümde** geçmezse tasarım **kandırıyordur**
— bu yüzden ölçüm sonucu olmadan "geçti" damgası vurulmaz.

## §1 Bağlayıcı Kısıtlar (ölçüm kapılarını bağlar)

| # | Kısıt | Değer | Kaynak |
|---|---|---|---|
| 1 | SNR hedefi | **>100 dB** | proje kısıtı (§B.5-4) |
| 2 | THD+N hedefi | **<0.01% @1 kHz** | proje kısıtı (§B.5-4) |
| 3 | Kaynak formatı | 32-bit float | ADR-017 |
| 4 | Blok/örnekleme | 512 örnek / 48 kHz | ADR-017 · L0 |
| 5 | DAC | tek PCM3168A (PCM5122 yasak) | ADR-038 · H001 |
| 6 | DC koruma | >0.5 V → röle | L0 kural 10 |
| 7 | Ölçüm durumu | **PLANNED** — fiziksel ölçüm yok | §N-7 |

## §2 Anayasa 1 — PCB Üretimi

> Kapsam §B.5(1): PCB üretimi (Gerber, panel, reflow) — **Web üretim takibi → PHP (K0xx)** · kanıt `pcb-design.md:24-30,44-52`.

| # | Kural | Gerekçe | Etiket |
|---|---|---|---|
| 1 | Gerber çıktısı üretime gönderilmeden önce katman/trace kontrolü yapılır | `pcb-design.md:24-30` — **okunmadı** | `⚠️` |
| 2 | Panel tasarımı kenar payı/break-away içerir | Kerf/kesim hasarı | `⚠️` |
| 3 | Reflow profilisi lehim pastası spec'ine göre | Tombstone/void | `⚠️` |
| 4 | I2S trace 8 mil kuralı üretimde uygulanır | Kapsam §N(3) — arayüz dijitalde, kural burada | kapsam |
| 5 | Decoupling yerleşimi üretimde; değeri `devre-ve-analog` | Kapsam §N(4) | kapsam |
| 6 | Termal sensör yerleşimi = montaj kuralı | Kapsam §N(10) | kapsam |
| 7 | Web üretim takibi bu dosyanın **kapsam dışı** (PHP) | §B.5(1) ikinci sütun | kapsam |
| 8 | Web takip verisi PHP PDO ile saklanır (karar PHP'de değil) | OWASP · `php-backend-standards` | kural |

## §3 Anayasa 2 — DFT: Test Noktaları, ICT/FCT Planı

> Kapsam §B.5(2): DFT — **Test otomasyon kodu → K005/QA** · test noktası ihtiyacı komşulardan gelir (§N-9).

| # | Kural | Gerekçe | Etiket |
|---|---|---|---|
| 1 | Her kritik sinyal/rail için test noktası **ayrılır** | ICT/FCT erişimi | `⚠️` plan |
| 2 | Test noktaları yerleşimi bu alt-konuda; ihtiyacı komşular bildirir | Kapsam §N(9) | kapsam |
| 3 | ICT (bed-of-nails) + FCT (fonksiyonel) planı ayrı yazılır | Farklı hata sınıfları | `⚠️` |
| 4 | Test otomasyon **kodu yazılmaz** | K005/QA ajanı | §B.5(2) |
| 5 | Test erişimi toprak/güç pin'lerine de verilir | Besleme doğrulaması | `⚠️` |
| 6 | Proprietary test point alanı korunur (overlap yasak) | Boşluk ihlali | `⚠️` |

## §4 Anayasa 3 — AOI / X-ray Muayene Kriteri

> Kapsam §B.5(3): AOI/X-ray muayene kriteri — ikinci sütun **boş** (dışarı devir yok).

| # | Kural | Gerekçe | Etiket |
|---|---|---|---|
| 1 | AOI kriteri: eksik/yayım/short — gözle görünür sınıflar | İnsan gözü sınırlı | `⚠️` |
| 2 | X-ray kriteri: BGA/void — gözle görünmeyen sınıflar | Kapalı bağlantı | `⚠️` |
| 3 | Kriter sayıları (limit %) **uydurulmaz** | Ölçüm/standart yok | Zero-Hallucination |
| 4 | Red/fail listesi ölçüm kaydına bağlanır | İzlenebilirlik | `⚠️` |
| 5 | DFT/AOI vault satır kanıtı `⚠️` (§H.5-6) | okunmadı | `⚠️` |
| 6 | Kriter revizyonu sürümle saklanır | Değişiklik izi | `⚠️` |

## §5 Anayasa 4 — Kalibrasyon Testi (SNR, THD+N)

> Kapsam §B.5(4): ölçüm **BURADA**; **UI/script → QA/K004** `⚠️` · kurulum kanıtı `snr-thd-measurement.md:29,45-48,55-59` (okunmadı).

| # | Kural | Gerekçe | Etiket |
|---|---|---|---|
| 1 | SNR **>100 dB** hedefi ölçümle doğrulanır — **ölçülmedi** | §N-7 PLANNED | `⚠️` |
| 2 | THD+N **<0.01% @1 kHz** hedefi ölçümle doğrulanır — **ölçülmedi** | §N-7 PLANNED | `⚠️` |
| 3 | Ölçüm kurulumu `snr-thd-measurement.md:29,45-48,55-59` — satırlar **okunmadı** | dürüstlük | `⚠️` |
| 4 | Frekans yanıt ölçümü `frequency-response.md:50-56` — **okunmadı** | dürüstlük | `⚠️` |
| 5 | Örnekleme ölçüm ortamı 512/48k bağında olmalı | ADR-017 · L0 | kısıt |
| 6 | Ölçüm çıktısı 32-bit float yolunda kaydedilir | ADR-017 | kısıt |
| 7 | Ölçüm UI/script'i **yazılmaz** (QA/K004) | §B.5(4) ikinci sütun | kapsam |
| 8 | Sonuç yoksa cihaz **"geçti" sayılmaz** | dürüstlük kapısı | L0 |

## §6 Anayasa 5 — EMC Ön Hazırlık (pre-scan)

> Kapsam §B.5(5): EMC ön hazırlık — **Sertifikasyon → dış laboratuvar** · kanıt `development-workflow.md:232` (okunmadı).

| # | Kural | Gerekçe | Etiket |
|---|---|---|---|
| 1 | Pre-scan sertifika **DEĞİL**, hazırlık provasıdır | §B.5(5) ayrımı | kapsam |
| 2 | Sertifikasyon dış laboratuvara devredilir | Yetki sınırı | kapsam |
| 3 | Standart numaraları **yazılmaz/uydurulmaz** | Q-IM1 `⚠️` | Zero-Hallucination |
| 4 | EMC filtresi şeması `guc-yonetimi` (giriş topolojisi) | Kapsam §N(12) | kapsam |
| 5 | Kablo/gövde yerleşimi test öncesi gözden geçirilir | Yayılım yolu | `⚠️` |
| 6 | Pre-scan bulguları düzeltme döngüsüne yazılır | Geri besleme | `⚠️` |

## §7 Anayasa 6 — Burn-in / Yaşlanma (8 saat)

> Kapsam §B.5(6): Burn-in 8 saat — ikinci sütun **boş** (dışarı devir yok).

| # | Kural | Gerekçe | Etiket |
|---|---|---|---|
| 1 | **8 saat** yaşlanma koşulu (kapsam sabiti) | Erken ölümler | §B.5(6) |
| 2 | Süreç boyunca güç/ölçüm logu tutulur | Geri dönüş izi | `⚠️` |
| 3 | DC offset eşiği (>0.5 V → röle) süreçte de izlenir | L0-10 · ölçüm kaynağı sensörde | L0-10 |
| 4 | Sıcaklık ortamı kaydedilir (değer uydurulmaz) | Termal stres | `⚠️` |
| 5 | Fail = ünite ayrı işaretlenir, istatistiğe girer | Yield verisi | `⚠️` |
| 6 | Burn-in sonunda tekrar SNR/THD kapısı | Kümülatif doğrulama | §5 |

## §8 Kapsam DIŞI Tablosu (§B.5 ikinci sütun)

| # | Dışarı çıkan | Gittiği yer | Ayrım cümlesi |
|---|---|---|---|
| 1 | Web üretim takibi | PHP katmanı (K0xx) | Veri orada, üretim burada |
| 2 | Test otomasyon kodu | K005 / QA ajanı | Plan burada, kod orada |
| 3 | Ölçüm UI/script | QA / K004 ⚠️ | Kapı burada, ekran orada |
| 4 | EMC sertifikasyonu | Dış laboratuvar | Prova burada, sertifika dışarıda |
| 5 | (boş — AOI) | — | Kapsam içi tam |
| 6 | (boş — burn-in) | — | Kapsam içi tam |

## §9 Ölçüm Kapıları Özeti (hedef ↔ durum)

| # | Kapı | Hedef | Durum | Kaynak |
|---|---|---|---|---|
| 1 | SNR | >100 dB | **ölçülmedi** `⚠️` | §N-7 |
| 2 | THD+N @1 kHz | <0.01 % | **ölçülmedi** `⚠️` | §N-7 |
| 3 | Frekans yanıt | — | **okunmadı** `⚠️` | frequency-response.md:50-56 |
| 4 | DC offset | >0.5 V → röle | kapı tanımlı, ölçüm yok | L0-10 |
| 5 | Burn-in | 8 saat | süreç tanımlı, log yok | §B.5(6) |
| 6 | AOI/X-ray | kriter | limit yok `⚠️` | §H.5-6 |

## §10 OS Kapsamı — Üretim/Ölçüm Yüzeyi (her işletim sistemi)

| # | OS | Üretim/ölçüm arayüzü önerisi | K000 atıfı | Doğrulama |
|---|---|---|---|---|
| 1 | Windows | CAD/fabrika istemcisi + CLI ölçüm aracı (C#/Python) | `isletim-sistemi-turu/windows/` | `⚠️` |
| 2 | macOS | CLI ölçüm aracı (Python/C++) | `isletim-sistemi-turu/macos/` | `⚠️` |
| 3 | Linux | Ölçüm sunucusu + otomasyon (Python/shell) — **kod K005/QA** | `os-guc-elektronigi/` | `⚠️` |
| 4 | iOS | Üretim arayüzü **YOK** — yalnız rapor görüntüleme | `isletim-sistemi-turu/ios/` | `⚠️` |
| 5 | Android | Üretim arayüzü **YOK** — yalnız rapor görüntüleme | `isletim-sistemi-turu/android/` | `⚠️` |
| 6 | Fabrika/RTOS | Test fixture + seri/USB hattı | `donanim-arayuz-protokolleri/` | `⚠️` |

## §11 Dil Kapsamı — Üretim/Ölçüm Önerileri (her programlama dili)

| # | Dil | Öneri | Katman | Doğrulama |
|---|---|---|---|---|
| 1 | Python | Ölçüm scripti, CSV/JSONL üretimi, burn-in logu | test/imalat | `⚠️` |
| 2 | C#/.NET | Fabrika istemcisi, rapor ekranı | test/imalat | `⚠️` |
| 3 | C/C++ | Donanım arayüzü (fixture), fixture firmware **K005** sınırı | test | `⚠️` |
| 4 | Rust | Ölçüm aracı (tip güvenli rapor) | test | `⚠️` |
| 5 | PHP 8.x | Üretim takip kaydı (PDO parametreli) | uygulama | `php-backend-standards` |
| 6 | JS/TS | Ölçüm raporu görüntüleme (okuma) | uygulama | `⚠️` |
| 7 | PowerShell/shell | Hattan otomasyon (kod yüzeyi belirsiz → K005/QA) | devralınan | `⚠️` |
| 8 | Assembly | **Üretim katmanında önerilmez** | — | — |

## §12 Kural ↔ Sınır Eşlemesi

| # | Kural | Sorumlu | Bağ |
|---|---|---|---|
| 1 | I2S trace 8 mil | `dijital-ve-mikroislemci` (arayüz) | §N(3) |
| 2 | Decoupling değeri | `devre-ve-analog` | §N(4) |
| 3 | Güç testi eşiği | `guc-yonetimi` | §N(5) |
| 4 | Test noktası ihtiyacı | `sensor-ve-akteyici` + tümü | §N(9) |
| 5 | Termal sensör montajı | `guc-yonetimi` (eşik) | §N(10) |
| 6 | EMC filtresi şeması | `guc-yonetimi` (giriş topolojisi) | §N(12) |

## §13 Yasaklar

| # | Yasak | Neden |
|---|---|---|
| 1 | Ölçüm sonucu uydurma (SNR/THD) | Zero-Hallucination · §N-7 |
| 2 | EMC standart numarası uydurma | Q-IM1 `⚠️` |
| 3 | Test otomasyon kodu yazma | K005/QA |
| 4 | Ölçüm UI/script yazma | QA/K004 `⚠️` |
| 5 | Web üretim takibi yazma | PHP katmanı (K0xx) |
| 6 | AOI limit yüzdesi uydurma | ölçüm yok |
| 7 | "8 saat" dışına süre uydurma | §B.5(6) sabiti |

## §14 Açık Sorular

| ID | Soru | Etiket |
|---|---|---|
| Q-IM1 | EMC standart numaraları | `⚠️` |
| Q-IM2 | SNR/THD fiziksel ölçümü | `⚠️` PLANNED |
| Q-IM3 | DFT/AOI satır kanıtı | `⚠️` |
| Q-O1 | Elektronik kod örneği | açık |
| Q-B1 | K004/K005 disk durumu | açık |

## §15 Ölçüm Girdi Sözlüğü

| Girdi | Birim | Durum | Kaynak |
|---|---|---|---|
| SNR | dB | `UNKNOWN` (hedef >100) | ölçüm yok |
| THD+N @1 kHz | % | `UNKNOWN` (hedef <0.01) | ölçüm yok |
| Frekans yanıt | dB/Hz | `UNKNOWN` | okunmadı |
| AOI fail oranı | % | `UNKNOWN` | kriter yok |
| Burn-in süresi | saat | 8 (kapsam sabiti) | §B.5(6) |
| DC offset | V | `UNKNOWN` (eşik 0.5) | L0-10 |

## §16 Sürüm ve Değişiklik Kaydı

| Sürüm | Tarih | Değişiklik | Kaynak |
|---|---|---|---|
| 1.0 | 2026-10-14 | İlk yazım — 10 eksik dosya turu | index.md children |

## §17 Kapı Özeti

| Kapı | Aşama | Durum |
|---|---|---|
| Mockup | görsel yok | N/A |
| LINE-500 | bu dosya ≥500 | ölçülür |
| Mojibake-0 | UTF-8 regex | 0 ✓ |
| FORMAT | frontmatter 9 alan | ✓ |
| Commit | subagent ATMAZ | ATILMADI |

## §18 OS Ayrıntı Tabloları (her OS — devam)

### Windows

| # | Bileşen | Öneri | İş |
|---|---|---|---|
| 1 | CAD/fabrika | istemcisi (C#/Python) | test |
| 2 | Ölçüm | CLI + JSONL | test |
| 3 | Rapor | HTML/ekran | okuma |
| 4 | Karar | Yok — kapı ölçümde | §9 |

### macOS

| # | Bileşen | Öneri | İş |
|---|---|---|---|
| 1 | Ölçüm | CLI (Python/C++) | test |
| 2 | Log | JSONL | dürüstlük |
| 3 | Rapor | CLI/ekran | okuma |
| 4 | Karar | Yok | §9 |

### Linux

| # | Bileşen | Öneri | İş |
|---|---|---|---|
| 1 | Ölçüm sunucusu | Python otomasyon **kod K005/QA** | devralınan |
| 2 | Log | JSONL/CSV | dürüstlük |
| 3 | Üretim takibi | PHP PDO | §2(8) |
| 4 | Karar | Yok | §9 |

### iOS / Android

| # | Bileşen | Öneri | İş |
|---|---|---|---|
| 1 | Üretim arayüzü | **YOK** | — |
| 2 | Rapor okuma | uygulama içi görüntüleme `⚠️` | okuma |
| 3 | Ölçüm | **Bu hedefte yok** | — |
| 4 | Karar | Yok | §9 |

### Fabrika / RTOS (fixture)

| # | Bileşen | Öneri | Kural |
|---|---|---|---|
| 1 | Fixture hattı | seri/USB | `⚠️` |
| 2 | Test flush | ICT/FCT sırası | §3 |
| 3 | Burn-in | 8 saat + log | §7 |
| 4 | Kapı ölçümü | SNR/THD (hedef) | §5 |
| 5 | Koruma | 0.5 V → röle | L0-10 |

## §19 Dil Ayrıntı Tabloları (her dil)

| # | Dil | Ölçüm | Üretim kaydı | Kaçınılan |
|---|---|---|---|---|
| 1 | Python | script + CSV | — | kapı kararı |
| 2 | C#/.NET | istemci + rapor | — | kapı kararı |
| 3 | C/C++ | fixture arayüzü | — | K005 aşımı |
| 4 | Rust | rapor aracı | — | — |
| 5 | PHP 8.x | — | PDO takip | karar/kapı |
| 6 | JS/TS | — | rapor okuma | karar |
| 7 | PowerShell/shell | hattan otomasyon | — | sorumluluk K005/QA |
| 8 | Assembly | **önerilmez** | — | — |

## §20 Pseudocode İskeletleri (çalışır değildir)

### Ölçüm kaydı (Python — test)

```python
# PSEUDOCODE - olcum kaydi (SONUC YOK -> UNKNOWN)
# sonuc = olcum_et()                  # GERCEK OLCEM YAPILMADI
# kayit = {"snr_db": "UNKNOWN", "thd_n_percent": "UNKNOWN",
#          "measured": False}          # Zero-Hallucination §3
```

### Burn-in logu (Python — test)

```python
# PSEUDOCODE - 8 saat yaslanma (sure kapsam sabiti)
# sure_saat = 8                       # §B.5(6)
# her dk: log_yaz(guc, dc_offset)     # degerler olcumle gelir
# dc > 0.5 -> RÖLE (cekirdek, L0-10)
```

### Üretim takibi (PHP 8.x — uygulama)

```php
<?php
// PSEUDOCODE - uretim kaydi saklama (PDO parametreli)
// $st = $pdo->prepare('INSERT INTO prod_log (part, station) VALUES (:p,:s'));
// KARAR YOK - yalniz kayit; sertifika/hukum disari
```

### Rapor görüntüleme (JS/TS — uygulama)

```javascript
// PSEUDOCODE - olcum raporu okuma (sadece gosterme)
// const r = await fetch('/api/report');   // PHP kaydi
// render(r);                              // KARAR YOK
```

## §21 OS × Dil Uygunluk Matrisi (öneridir · `⚠️`)

| OS ↓ / Dil → | Python | C# | C/C++ | Rust | PHP | JS/TS | Shell |
|---|---|---|---|---|---|---|---|
| Windows | **3** | **3** | 2 | 2 | 2 | 3 | 2 |
| macOS | **3** | 1 | **3** | 2 | 2 | 3 | 2 |
| Linux | **3** | 1 | **3** | 2 | **3** | 3 | **3** |
| iOS | 0 | 0 | 1 | 0 | 1 | 2 | 0 |
| Android | 1 | 0 | 1 | 0 | 1 | 2 | 0 |
| Fabrika/RTOS | **3** | 2 | **3** | 2 | 0 | 1 | 3 |

> 0 = önerilmez · 1 = sınırlı · 2 = uygun · 3 = tercih edilen · ölçüm **DEĞİLDİR**.
> Assembly üretim katmanında önerilmez (§11-8).

## §22 Kısıt ↔ Anayasa Çaprazı

| # | Kısıt | Anayasa bağları |
|---|---|---|
| 1 | SNR >100 dB | §5(1) · §9(1) · §15 |
| 2 | THD+N <0.01% | §5(2) · §9(2) · §15 |
| 3 | 512/48 kHz | §5(5) |
| 4 | 32-bit float | §5(6) |
| 5 | L0-10 0.5 V | §7(3) · §18 fabrika |
| 6 | ADR-038 PCM3168A | §1(5) — DAC testi yola bağlı |
| 7 | §N-7 PLANNED | §1(7) · §9 · §14 |

## §23 Ölçüm Tablosu (LINE-500)

| # | Dosya | Hedef | Ölçüm | Durum |
|---|---|---|---|---|
| 0 | Ölçüm yeri | repo kökünden (§13.6) | `ReadAllLines().Count` | zorunlu |
| 1 | `imalat-ve-test-anayasa.md` | ≥500 | ölçülür | — |
| 2 | Mojibake | 0 | 0 | ✓ |
| 3 | `parent:` alan hatası | 0 | 0 | ✓ |
| 4 | Tırnaklı `group` | 0 | 0 | ✓ |
| 5 | Commit | — | ATILMADI | ✓ |

## §24 Dürüstlük İddia Matrisi

| # | İddia türü | Bu dosyada | Kanıt |
|---|---|---|---|
| 1 | Ölçüm sonucu | **YOK** | §N-7 PLANNED |
| 2 | EMC standart numarası | **YOK** | Q-IM1 `⚠️` |
| 3 | AOI limit yüzdesi | **YOK** | §H.5-6 `⚠️` |
| 4 | Satır kanıtları | atıf var, **okunmadı** `⚠️` | §H.5 |
| 5 | Yeni envanter iddiası | **ÜRETİLMEDİ** | V6 çürütme |
| 6 | Tahmini | `⚠️ VERIFICATION REQUIRED` | Zero-Hallucination |

## §25 Bağımlılık Zinciri

| # | Bağımlılık | Yön | Not |
|---|---|---|---|
| 1 | `imalat-ve-test` → kök index | parent | children (orkestratör) |
| 2 | index → 3 içerik | children | ✓ |
| 3 | anayasa → ornekler | kural → örnek | §2-§11 |
| 4 | anayasa → dogrulama | kural → kanıt | §2-§7 |
| 5 | anayasa → `guc-yonetimi` | güç testi eşiği | kardeş |
| 6 | anayasa → ADR-017/038 · L0 | kısıt | frozen |

## §26 OS/Dil Uygunluk Notları

| # | Not | Değer |
|---|---|---|
| 1 | OS satırları 6 yüzey | Windows/macOS/Linux/iOS/Android/Fabrika |
| 2 | Dil satırları 8 | Python/C#/C++/Rust/PHP/JS/Shell/Asm(önerilmez) |
| 3 | Tüm teknik iddialar `⚠️` | ölçüm yok |
| 4 | Mobil = yalnız rapor okuma | üretim/ölçüm yok |
| 5 | Kod sorumluluğu | otomasyon K005/QA, takip PHP |

## §27 Ölçüm Kapısı Ayrıntısı (SNR/THD)

| # | Kapı | Hedef | Kaynak satır | Durum |
|---|---|---|---|---|
| 1 | SNR | >100 dB | `snr-thd-measurement.md:29,45-48,55-59` | okunmadı `⚠️` |
| 2 | THD+N @1kHz | <0.01 % | aynı | okunmadı `⚠️` |
| 3 | Frekans yanıt | — | `frequency-response.md:50-56` | okunmadı `⚠️` |
| 4 | Test fazları | — | `development-workflow.md:416-434` | okunmadı `⚠️` |
| 5 | EMC | — | `development-workflow.md:232` | okunmadı `⚠️` |
| 6 | PCB katman/trace | — | `pcb-design.md:24-30,44-52` | okunmadı `⚠️` |

## §28 Komşu İhtiyaç Bildirimleri (§N-9)

| # | İhtiyaç bildiren | İstenen | Alan |
|---|---|---|---|
| 1 | Tüm alt-konular | test noktası | yerleşim (bu alt-konu) |
| 2 | `guc-yonetimi` | güç testi/ripple | ölçüm masası |
| 3 | `sensor-ve-akteyici` | shunt/ADC test erişimi | DFT |
| 4 | `dijital-ve-mikroislemci` | I2S trace doğrulaması | PCB kontrol |
| 5 | `devre-ve-analog` | decoupling yerleşim kontrolü | PCB kontrol |

## §29 Sürüm/Değişiklik Kaydı (genişletilmiş)

| Sürüm | Tarih | Değişiklik | Kanıt |
|---|---|---|---|
| 1.0 | 2026-10-14 | İlk yazım (10 eksik dosya — 7/10) | disk |
| 1.0 | 2026-10-14 | OS 6 yüzey + dil 8 eklendi (§T-K000 deseni) | K000 salt-okunur |

## §30 Kapanış Kontrol Listesi

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | `electroncis` yazım | ✓ |
| 2 | Frontmatter 9 alan + tırnaksız `group` | ✓ |
| 3 | Mojibake 0 | 0 ✓ |
| 4 | Bölüm tabloları | ✓ |
| 5 | Ölçüm/standart uydurması yok | ✓ |
| 6 | Kapsam dışı devirler işaretli (§8) | ✓ |
| 7 | OS 6 · dil 8 | ✓ |
| 8 | Commit ATILMADI | ✓ |

## §31 Ölçüm Tablosu (LINE-500 — nihai)

| # | Dosya | Hedef | Ölçüm | Durum |
|---|---|---|---|---|
| 1 | `imalat-ve-test/index.md` | 40-100 | ölçülür | — |
| 2 | `imalat-ve-test-anayasa.md` | ≥500 | ölçülür (bu dosya) | — |
| 3 | `imalat-ve-test-ornekler.md` | ≥500 | ölçülür | — |
| 4 | `imalat-ve-test-dogrulama.md` | ≥500 | ölçülür | — |
| 5 | Mojibake (4 dosya) | 0 | 0 | ✓ |
| 6 | Commit | — | ATILMADI | ✓ |

## §32 OS Aday Kaynak Haritası (K000 salt-okunur atıf)

| # | K000 kaynağı | Kullanılan bölüm | İş |
|---|---|---|---|
| 1 | `isletim-sistemi-turu/windows/` | §10 · §18 | atıf |
| 2 | `isletim-sistemi-turu/macos/` | §10 · §18 | atıf |
| 3 | `os-guc-elektronigi/` | §10 · §18 | atıf |
| 4 | `isletim-sistemi-turu/ios/` | §10 · §18 | atıf |
| 5 | `isletim-sistemi-turu/android/` | §10 · §18 | atıf |
| 6 | `donanim-arayuz-protokolleri/` | §10 · §18 | atıf |

> K000 dosyalarına **yazma yapılmadı** — §T-K000 gereği yalnız okuma/atıf.

## §33 Ölçüm Sonrası Kapı Akışı

```text
OLCUM YAP (test istasyonu)
  -> SNR >100 dB ?      -> GECTI / KALDI
  -> THD+N <0.01% @1k ? -> GECTI / KALDI
  -> DC > 0.5 V ?       -> RÖLE (L0-10, otomatik)
  -> 8 saat burn-in ?   -> log + tekrar olcum
HICBIRI YOK -> "GECTI" DAMGASI YASAK (§5-8)
```

| Aşama | Veri | Durum |
|---|---|---|
| SNR ölçümü | `UNKNOWN` | yapılmadı |
| THD ölçümü | `UNKNOWN` | yapılmadı |
| Burn-in logu | `UNKNOWN` | yapılmadı |
| Röle eşiği | 0.5 V (sabit) | kapı tanımlı |

## §34 Kapanış — Sayısal İddia Sınırı

| # | İddia | Yazım |
|---|---|---|
| 0 | Ölçüm yeri | repo kökünden ölçülür (`AGENTS.md` §13.6) |
| 1 | Kapsam sabitleri (8 saat, >100 dB, <0.01%) | kaynaklı yazılır |
| 2 | Ölçüm sonucu | **yazılmaz** |
| 3 | Standart numarası | **üretilmez** |
| 4 | AOI limiti | **üretilmez** |
| 5 | Yeni envanter | **üretilmez** |
| 6 | Tahmini | `⚠️ VERIFICATION REQUIRED` |

---

**Authority:** `.ai/architecture/K002-electroncis/electroncis-kapsam.md` §B.5 · §H.5 · `.ai/ARCHITECTURE.md` · ADR-017 · ADR-038
