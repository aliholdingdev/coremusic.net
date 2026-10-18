---
node_id: electroncis-web-arastirma
node_type: subdomain
status: draft
parent_id: K002-electroncis
children: []
depends_on: [K000-isletim-sistemi, K001-donanım, K004-dsp, K005-firmware]
evidence:
  - "webfetch doğrulama — ti.com/product/PCM3168A · ti.com/product/INA219 · ti.com/product/UCC27524 · ti.com/product/TPS54202 · electronics-tutorials.ws/amplifier/class-ab-amplifier.html · 2026-10-18"
  - "webfetch doğrulama — analog.com LT1166 datasheet · xmos.com XU316 · itu.int/rec/R-REC-BS.1770 · github.com/Signalsmith-Audio/dsp · 2026-10-18"
  - ".ai/architecture/K002-electroncis/electroncis-dogrulama.md §5 (2026-10-13 HTTP 200 envanteri — bu dosya onu GENİŞLETİR, tekilleştirmez) · 2026-10-18"
validation: "Her URL üretim öncesi webfetch ile doğrulanır; erişilemeyen kaynak silinmez, '⚠️ DEAD?' etiketiyle tabloda kalır; iddia-URL eşlemesi her satırda zorunlu."
open_questions:
  - "⚠️ ti.com/product/TPS542022 → HTTP 404 (2026-10-18): böyle bir TI parça numarası YOK; gerçek parça TPS54202 (4.5–28V, 2A, ACTIVE). Vault'ta TPS542022 geçmiyor — yanlış numara yalnız bu turda üretildi ve elendi."
  - "⚠️ learn.microsoft.com/en-us/windows/win32/coreaudio/apis → HTTP 404 (2026-10-18) — K000 elektronik kesişiminde WASAPI referansı için yeni Microsoft URL'i bu turda doğrulanmadı (K000 os-web-arastirma.md'ye işlendi)."
  - "⚠️ CISPR 32 / IEC 61000-4-x standart metinleri iec.ch ve jedec.org bu ortamdan erişilemedi (2026-10-13 elenme kaydı, dogrulama §4) — standart numaraları yalnız ADR-063 §2(b) metnine dayanır."
group: mimari
---

# K002-electroncis — Web Araştırması (Doğrulanmış Kaynaklar)

> **Kural:** Her kaynak üretim öncesi `webfetch` ile doğrulanır. Erişilemeyen
> kaynak **silinmez** — `⚠️ DEAD?` etiketiyle tabloda kalır. Bu dosya
> `electroncis-dogrulama.md` §5'in (2026-10-13) **genişletmesidir**; aynı URL'i
> iki dosyada farklı tarih/iddia ile taşımamak için buradaki taze tarih (2026-10-18)
> geçerlidir.

## §1 ELI15

Elektronik kararlar (çip seçimi, amfi topolojisi, standart uyumu) "güvenilir
kaynak" ister: üreticinin kendi datasheet'i, resmi standardın kendisi, doğrulanmış
açık kaynak kütüphane. Bu dosya K002'deki her önemli iddianın arkasındaki URL'i,
erişim tarihini ve durumunu tablo tablo tutar. Ölü link gizlenmez — "şu an
erişilemiyor" yazılır, çünkü yarın o karar tapuya dayanak olacak.

## §2 Doğrulanmış Kaynaklar (erişim: 2026-10-18)

### §2.1 Üretici Sayfaları (TI — ses/güç/sensör)

| # | İddia (kısa Türkçe) | URL | Erişim | Durum |
|---|---|---|---|---|
| 1 | PCM3168A 8-kanal 110 dB ses ADC/DAC — ADR-038 H001 DAC kilidinin dayanağı | https://www.ti.com/product/PCM3168A | 2026-10-18 | ✅ HTTP 200 |
| 2 | INA219 — 26 V'a kadar I2C akım/güç monitörü, 12 bit, 16 adres (master §3 sensör arayüzü) | https://www.ti.com/product/INA219 | 2026-10-18 | ✅ HTTP 200 |
| 3 | UCC27524 — 5 A/5 A dual low-side gate driver, 4.5–18 V VDD, UVLO 4 V (GVDD sürücü sınıfı) | https://www.ti.com/product/UCC27524 | 2026-10-18 | ✅ HTTP 200 |
| 4 | TPS54202 — 4.5–28 V giriş, 2 A senkron buck, 500 kHz, spread-spectrum EMI (güç yönetimi adayı) | https://www.ti.com/product/TPS54202 | 2026-10-18 | ✅ HTTP 200 |

### §2.2 Referans Tasarım & Eğitim Kaynakları

| # | İddia (kısa Türkçe) | URL | Erişim | Durum |
|---|---|---|---|---|
| 5 | Class AB amfi: bias, crossover distorsiyonu, diode/thermal kompanzasyon (ADR-089 topoloji temeli) | https://www.electronics-tutorials.ws/amplifier/class-ab-amplifier.html | 2026-10-18 | ✅ HTTP 200 |
| 6 | LT1166 — otomatik bias kontrol IC'si (Class AB quiescent akım sabitleme) | https://www.analog.com/media/en/technical-documentation/data-sheets/1166fa.pdf | 2026-10-18 | ✅ HTTP 200 |

### §2.3 Donanım Platformu & DSP Ekosistemi (K004 sınırı)

| # | İddia (kısa Türkçe) | URL | Erişim | Durum |
|---|---|---|---|---|
| 7 | XMOS XU316 — 6 çekirdekli xCORE DSP; ADR-017/038 USB ses platformu | https://www.xmos.com/xu316/ | 2026-10-18 | ✅ HTTP 200 |
| 8 | XMOS USB çok-kanallı ses referans uygulaması (BCLK/MCLK ağacı) | https://www.xmos.com/application/usb-multichannel-audio/ | 2026-10-18 | ✅ HTTP 200 |
| 9 | Signalsmith-Audio DSP — açık kaynak header-only C++ DSP (yanlış-loudness, VS3) | https://github.com/Signalsmith-Audio/dsp | 2026-10-18 | ✅ HTTP 200 |
| 10 | ITU-R BS.1770 — loudness ölçüm standardı (EBU R128 / libebur128 dayanağı) | https://www.itu.int/rec/R-REC-BS.1770 | 2026-10-18 | ✅ HTTP 200 |

## §3 Ulaşılamayan / Elelenen Kaynaklar (dürüstlük kaydı)

| # | URL | İddia | Erişim | Durum | Karar |
|---|---|---|---|---|---|
| 1 | https://www.ti.com/product/TPS542022 | "TPS542022 buck converter" | 2026-10-18 | ⚠️ HTTP 404 | Böyle parça numarası yok — **TPS54202** (satır §2.1/4) kullanıldı |
| 2 | https://www.learn.microsoft.com/.../coreaudio/apis | WASAPI/CoreAudio API listesi | 2026-10-18 | ⚠️ HTTP 404 | K000 dosyasına işlendi; K002 kapsamı değil |
| 3 | st.com · jedec.org · iec.ch ürün/standart sayfaları | MCU/standart doğrulama | 2026-10-13 | ⚠️ 403/timeout | `electroncis-dogrulama.md` §4 elenme kaydı geçerli |

## §4 İddia → Kaynak Eşlemesi (kritik K002 iddiaları)

| # | İddia (vault) | Kaynak satırı | ADR bağı |
|---|---|---|---|
| 1 | DAC yalnız PCM3168A (PCM5122 yasak) | §2.1 #1 | ADR-038 H001 |
| 2 | SNR >100 dB · THD+N <0.01 % @1W | §2.1 #1 (datasheet spec) + ADR-063 §2(f) | ADR-063 |
| 3 | Class AB bias / crossover kontrolü | §2.2 #5–6 | ADR-089 |
| 4 | PVDD Class D 18–53.5 V (datasheet kazanır) | §2.1 #3 (gate driver PVDD uyumu) | KD-E06 |
| 5 | Sensör arayüzü INA219/ADS1115 | §2.1 #2 | — |
| 6 | Loudness ölçüm (LUFS) yaklaşımı | §2.3 #10 | ADR-017 (32-bit float zincir) |
| 7 | Fabrika testi AES17 kapıları | §2.2 #5–6 + ADR-063 §2(f) | ADR-063 |
| 8 | USB çok-kanallı ses platformu | §2.3 #7–8 | ADR-017 · ADR-038 |

### §4.1 Eşlemesi Olmayan İddialar (açık alan)

| # | İddia | Durum | Not |
|---|---|---|---|
| 1 | EMC standart eşikleri (CISPR 32 / IEC 61000-4-x) | ⚠️ VERIFICATION REQUIRED | Standart metinleri bu ortamdan erişilemedi (§3 #3) |
| 2 | IPC-6012/A-610 Class 2 kabul kriterleri | ⚠️ VERIFICATION REQUIRED | ADR-063 §2(b) metnine dayanır; birincil standart metni doğrulanmadı |
| 3 | ADS1115 datasheet doğrulaması | ⚠️ bu turda yapılmadı | INA219 doğrulandı (§2.1 #2); ADS1115 yalnız vault içi referans |

## §5 Doğrulama Yöntemi (tekrarlanabilir kayıt)

| # | Adım | Araç | Not |
|---|---|---|---|
| 1 | URL'i `webfetch` ile çağır | webfetch (markdown) | HEAD değil içerik GET — anti-bot 403'ü ayırt eder |
| 2 | Durum kodu + anahtar iddia metnini doğrula | manuel okuma | Örn. INA219 "26 V, 12-bit I2C" metni sayfada görüldü |
| 3 | Üretici sayfasında datasheet linkini teyit et | sayfa içi link | Örn. ti.com/lit/gpn/INA219 PDF linki mevcut |
| 4 | Sonucu bu dosyanın §2/§3 tablosuna işle | tablo | Tarih + durum her satırda zorunlu |
| 5 | 404/timeout → §3'e `⚠️ DEAD?`/`elenen` olarak yaz | tablo | Silme yasağı — denetim izi |

**2026-10-18 tur kaydı:** §2.1–§2.3'teki 10 kaynak bu yöntemle doğrulandı;
§3'teki 3 kaynak bu turda elendi (TPS542022 yanlış parça numarası, MS CoreAudio
404, 2026-10-13'te elenenlerin tekrarı). Toplam: **10 ✅ / 3 ⚠️**.

## §6 Kullanım Kuralları

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Yeni kaynak eklemeden önce webfetch ile doğrula | Zero-hallucination |
| 2 | Datasheet iddiası yalnız üretici alan adından (ti.com, analog.com, xmos.com) | Birincil kaynak |
| 3 | Standart numarası (CISPR/IEC/IPC) metin doğrulaması olmadan "uygunuz" denmez | `⚠️ VERIFICATION REQUIRED` |
| 4 | Dead link tabloda kalır, silinmez | Denetim izi |
| 5 | K004-dsp ortak kaynakları (Signalsmith, BS.1770) tek dosyada münhasır | Çift sayım yasağı |
| 6 | Doğrulama tarihi geçmiş 6 ayı aşırsa URL yeniden çağrılmalı | Bağlantı çürümesi |
| 7 | Parça numarası iddiası ürün sayfası HTTP 200 ile kanıtlanır, forum/blog ile değil | Birincil kaynak |

## §6 Çapraz Referanslar

| Hedef | İlişki |
|---|---|
| `electroncis-dogrulama.md` §4–§5 | 2026-10-13 ilk doğrulama turu (bu dosya genişletme) |
| `electroncis-kararlar.md` §2 | ADR-017/038/062/063/064/089/090 — bu dosyadaki kaynakların karar karşılığı |
| `electroncis-kod-ornekleri.md` | §2'deki bileşenlerin (INA219, UCC27524) kod örnekleri |
| `.ai/.decisions/accepted/ADR-063-hardware-design-standards.md` §2(b) | EMC/standart matrisi — dayanak yalnız ADR metni |
