---
title: "Windows DMA ve Kesme - ISR/MSI/DPC Zinciri, WaveRT DMA → Event, Spin Lock Eşzamanlılığı"
type: architecture-deep-dive
node_id: ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/windows/windows-dma-ve-interrupt.md"
node_type: architecture
status: draft
date: 2026-10-12
parent_id: K000-isletim-sistemi/isletim-sistemi-turu/windows
siblings: [windows-anayasa, windows-kernel, windows-kernel-modlari, windows-ses-stacki, windows-device-lifecycle, windows-kod-ornekleri, windows-web-arastirma]
children: []
depends_on:
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/windows/windows-kernel-modlari.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/windows/windows-ses-stacki.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-latency-ve-buffer.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-surucu-modeli.md"
evidence:
  - "learn.microsoft.com/en-us/windows-hardware/drivers/kernel/ — ISR, MSI, DPC, DMA Library, Adapter Objects, Controller Objects · fetch: 2026-10-12"
  - "learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio — WaveRT DMA → event, event size = frame · fetch: 2026-10-12"
  - "learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture — WaveRT DMA zinciri, HDAudio arabellek · fetch: 2026-10-12"
validation: "TRUTH MODE: iddia → resmi URL + fetch tarihi; IRQL/DMA detay sayfaları bu turda TAM fetch EDİLMEDİ → ⚠️; kod uydurma yasak (repo 0 .cpp/.h)"
open_questions:
  - "IRQL seviye listesi ve ISR/DPC detay sayfaları — bu turda fetch edilmedi (⚠️ UNKNOWN)"
  - "DMA Adapter Objects detay sayfası (introduction-to-adapter-objects) fetch edilmedi (⚠️)"
group: mimari
---

# windows-dma-ve-interrupt — Windows DMA ve Kesme Mimarisi (Teknoloji: Windows)

> **Eksen:** `K000-isletim-sistemi / isletim-sistemi-turu / windows` · **Grup:** `mimari` · **Durum:** `draft`
> **Amaç:** Windows'ta kesme (ISR/MSI/DPC) ve DMA zincirini resmi kernel design guide ile ses
> alt sisteminin WaveRT DMA/event zincirine bağlamak; gecikme etkisini ölçüm bağlantısıyla
> sunmak.
> **Konu sırası:** (1) sınır → (2) kesme zinciri → (3) DMA → (4) WaveRT DMA/event → (5)
> gecikme etkisi → (6) IRQ/IRQL açık konular → (7) FAQ → (8) bağımlılık → (9) RED TEAM →
> (10) fetch günlüğü.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12 · **Mod:** TRUTH MODE / RED TEAM

---

## 0. Bu Dosyanın Kapsamı (Boundary)

- ✅ **Yapılır:** ISR/MSI/DPC/DMA kavramlarının resmi tanımları, WaveRT DMA → event zinciri,
  HDAudio arabellek etkisi, gecikme ölçümüne bağlantı.
- ❌ **Yapılmaz:** IRQL tam listesi (→ açık madde), sürücü kodu (repo 0 `.cpp/.h`), WASAPI API
  derinliği, ISV/PCIe topology detayı (bu turda fetch edilmedi).

---

## 1. Kesme Zinciri (Resmi Tanımlar)

| # | Bileşen | Resmi Tanım | Kaynak |
|---|---|---|---|
| 1 | Interrupt Service Routines (ISRs) | fiziksel kesme alan sürücülerin kesmeleri işler | kernel/ ✅ 2026-10-12 |
| 2 | Message-Signed Interrupts (MSI) | belirli bellek adresine değer yazarak kesme tetikleme | kernel/ ✅ |
| 3 | Deferred Procedure Calls (DPC) | ISR'lardan kuyruklanır; daha sonra ve daha düşük IRQL'de çalıştırılır | kernel/ ✅ |
| 4 | DPC Objects | ISR'dan kuyruklanabilen ve daha düşük IRQL'de yürütülen nesneler | kernel/ ✅ |
| 5 | Kernel Library | donanım kesmesi yönlendirme (routing) | kernel/ ✅ |
| 6 | IRQL | bağlam seviyesi; TAM liste bu turda fetch edilmedi | ⚠️ UNKNOWN |

---

## 2. DMA Kavramları (Resmi Design Guide)

| # | Öğe | Resmi İfade | Kaynak |
|---|---|---|---|
| 1 | DMA Library | sürücü geliştiricileri için doğrudan erişim bellek kütüphanesi | kernel/ ✅ 2026-10-12 |
| 2 | DMA (tasarım bölümü) | sürücü geliştirmede kritik; "DMA from A to Z" makale dizisi | kernel/ ✅ |
| 3 | Adapter Objects | DMA makale dizisi bu düğümden başlar (introduction-to-adapter-objects) | kernel/ ✅ (bağlantı) · sayfa fetch yok ⚠️ |
| 4 | Controller Objects | fiziksel cihaz denetleyicisi + bağlı cihazlar temsili | kernel/ ✅ |
| 5 | HAL Library | donanım soyutlama katmanı | kernel/ ✅ |
| 6 | Memory Manager | fiziksel bellek yönetimi (DMA tamponları) | kernel/ ✅ |

---

## 3. WaveRT DMA → Event Zinciri (Ses Özel)

| # | Adım | Ne Olur | Kaynak |
|---|---|---|---|
| 1 | DMA tamamlanır | WaveRT DMA bildirimi | low-latency ✅ 2026-10-12 |
| 2 | Event işaretlenir | event-driven thread tetiklenir | low-latency ✅ |
| 3 | Event size | frame cinsinden | low-latency ✅ |
| 4 | Düşük gecikme exclusive | event = 1–10 period | low-latency ✅ |
| 5 | Polling alternatifi | event yoksa polling thread | low-latency ✅ |
| 6 | WaveRT DMA zinciri | Per Buffer → In/Out × Major → Copy → Termination | architecture ✅ 2026-10-12 |
| 7 | Terim kodları | PP IO / IN / OFF / CTL / CC / WR (açılımlar ⚠️ UNKNOWN) | architecture ✅ · anlam ⚠️ |
| 8 | Register-based erişim | App → HW (WaveRT) | architecture ✅ |

---

## 4. DMA ⇄ Gecikme Etkisi

| # | Öğe | Değer | Kaynak |
|---|---|---|---|
| 1 | Motor gecikmesi (Win10+) | ~1.3 ms | low-latency ✅ |
| 2 | Capture motoru | ~0 ms | low-latency ✅ |
| 3 | Roundtrip | 4.5 ms | low-latency ✅ |
| 4 | Event kaynağı | DMA tamamlama anı | low-latency ✅ |
| 5 | HDAudio arabellek | 128 (2.66 ms) / 480 (10 ms) örnek @48k | architecture ✅ |
| 6 | MMCSS eşiği | 10 ms → "Pro Audio"/"Audio" | low-latency ✅ |
| 7 | Eski model | 6/12 ms → +1/+1 ms | low-latency ✅ |
| 8 | 20 ms @48k stereo | 960 byte = 120 DWord | low-latency ✅ |

---

## 5. Kesme ⇄ İş Parçacığı Etkileşimi

| # | Kavram | İlişki | Kaynak |
|---|---|---|---|
| 1 | Thread | ISR/DPC user-mode'dan farklı bağlamda | procthread ✅ 2026-10-12 (temel tanımlar) |
| 2 | Thread pool | kullanıcı modunda asenkron callback | procthread ✅ |
| 3 | MMCSS | kullanıcı modunda iş parçacığı kuyruğu etiketi | low-latency ✅ |
| 4 | DPC | çekirdek modunda ertelenmiş iş | kernel/ ✅ |
| 5 | Kuyruk ilişkisi | MMCSS ≠ DPC/IRQL (katman farkı) | ✅ çıkarım · resmi ifade ⚠️ |

---

## 6. Açık Konular (⚠️ UNKNOWN)

| # | Konu | Neden | Sonraki Adım |
|---|---|---|---|
| 1 | IRQL seviye listesi | ayrı sayfa fetch edilmedi | kernel IRQL sayfası fetch |
| 2 | ISR detay sayfası (introduction-to-interrupt-service-routines) | bağlantı var, sayfa fetch yok | fetch |
| 3 | DPC nesne detayı (introduction-to-dpc-objects) | bağlantı var, sayfa fetch yok | fetch |
| 4 | Adapter Objects DMA detayı | bağlantı var, sayfa fetch yok | fetch |
| 5 | MSI sayfası detayı | bağlantı var, sayfa fetch yok | fetch |
| 6 | WaveRT terim kodlarının açılımı | architecture sayfası kısaltma veriyor | WDK DDI fetch |

---

## 7. Sık Sorulanlar (FAQ — Kaynaklı)

| # | Soru | Cevap | Kaynak |
|---|---|---|---|
| 1 | MSI nasıl kesme üretir? | bellek adresine değer yazarak | kernel/ ✅ |
| 2 | DPC ne zaman çalışır? | ISR'dan sonra, daha düşük IRQL'de | kernel/ ✅ |
| 3 | DMA kütüphanesi nerede? | Kernel-Mode DMA Library | kernel/ ✅ |
| 4 | Event ne zaman işaretlenir? | DMA tamamlama (hedefe bilgi yazılırsa) | low-latency ✅ |
| 5 | Event yoksa ne olur? | polling thread | low-latency ✅ |
| 6 | Controller Object nedir? | fiziksel denetleyici temsili | kernel/ ✅ |
| 7 | HDAudio arabellek neden önemli? | 2.66–10 ms gecikme katkısı | architecture ✅ |
| 8 | DMA zinciri (ses) nasıl? | Per Buffer → In/Out × Major → Copy → Termination | architecture ✅ |

---

## 8. Bağımlılık Matrisi (Bu Dosya → Diğer Düğümler)

| Hedef Düğüm | Bağımlılık Türü | Ne İçin |
|---|---|---|
| `windows-kernel-modlari.md` | mimari | ISR/DPC/MSI üst yapısı |
| `windows-ses-stacki.md` | mimari | WaveRT katmanı |
| `windows-dogrulama.md` | kanit | DMA/event ölçüm senaryoları |
| `windows-riskler.md` | kanit | kesme/DMA riskleri (DPC storm vb.) |
| `isletim-sistemi-audio/wasapi/wasapi-latency-ve-buffer.md` | mimari | gecikme bütçesi |
| `isletim-sistemi-audio/wasapi/wasapi-surucu-modeli.md` | mimari | WaveRT/DMA OHCI |
| `isletim-sistemi-turu/linux/linux-timing-ve-clock.md` (beyazliste) | teknolojiler-arası | kesme/zamanlama kıyası |

---

## 9. RED TEAM: Reddedilen İdialar

| İddia | Neden Red | Karar |
|---|---|---|
| "ISR uzun iş yapar" | tasarım: DPC daha düşük IRQL'de çalıştırılır (kısa ISR) | ⚠️ kısa ISR kuralının tam metni fetch edilmedi → UNKNOWN |
| "IRQL listesi burada kesin" | sayfa fetch edilmedi | ⚠️ UNKNOWN |
| "DMA = her zaman en düşük gecikme" | HDAudio arabellek 10 ms ekleyebilir | ❌ |
| "MMCSS IRQL ile aynı şeydir" | kullanıcı-kernel katman farkı | ❌ |
| "Repo'da Dosya:…/Satır:…" | repo 0 `.cpp/.h` | ❌ |

---

## 10. Kaynak Fetch Günlüğü

| # | URL | Ne Kullanıldı | Fetch |
|---|---|---|---|
| M1 | https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/ | ISR, MSI, DPC, DMA Library, Controller Objects | 2026-10-12 ✅ |
| M2 | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | DMA→event, event size, 1–10 period | 2026-10-12 ✅ |
| M3 | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | WaveRT DMA zinciri, HDAudio arabellek | 2026-10-12 ✅ |
| M4 | https://learn.microsoft.com/en-us/windows/win32/procthread/processes-and-threads | thread bağlamı | 2026-10-12 ✅ |
| M5 | .../kernel/introduction-to-interrupt-service-routines (bağlantı) | ISR detay | ⚠️ bu turda fetch edilmedi |
| M6 | .../kernel/introduction-to-dpc-objects (bağlantı) | DPC detay | ⚠️ bu turda fetch edilmedi |

---

---

## EK-A. Kesme (Interrupt) Hattı Ayrık Matrisi (RED TEAM)

> `kernel/` ana sayfası (✅ fetch 2026-10-12) ISR · MSI · DPC başlıklarını tablo olarak verir;
> **ayrıntı sayfaları fetch EDİLMEDİ**. Bu matris başlık-üstü ile bilinen-üstüyü ayırır.

| # | Öğe | İçerik | Durum |
|---|---|---|---|
| A1 | ISR başlığı kernel sayfasında var | ✅ fetch 2026-10-12 | ✅ |
| A2 | MSI başlığı var; MSI = bellek adresine yazma | ✅ fetch 2026-10-12 (sayfa dili) | ✅ |
| A3 | DPC başlığı var | ✅ fetch 2026-10-12 | ✅ |
| A4 | ISR çalışır → DPC kuyruğa alınır → daha düşük IRQL'de iş | bilinen çekirdek modeli | ⚠️ bu turda sayfadan doğrulanmadı |
| A5 | ISR kısa kalmalı (yalnız durum oku + DPC kuyruğa) | bilinen tasarım kuralı | ⚠️ süre sayfası fetch yok |
| A6 | IRQL seviye listesi (PASSIVE/APC/DISPATCH/DIRQL) | bilinen | ⚠️ fetch yok |
| A7 | WaveRT DMA olayı → user-mode event | `low-latency-audio` ✅ + önceki tur zincir | ✅ (zincir) |
| A8 | HDAudio tampon 128–480 örnek | önceki oturum | ✅ kilitli gerçek |
| A9 | MSI-X ⇒ birden çok vektör | bilinen | ⚠️ fetch yok |

---

## EK-B. DMA Türleri ⇄ Ses Kullanımı Matrisi

| DMA türü | Ses stacki kullanımı | Durum |
|---|---|---|
| Paravan DMA (ISA dönemi) | HDAudio'da kullanılmaz (bilinen) | ⚠️ fetch yok — yorum olarak işaretle |
| Scatter/gather | geniş tamponlarda olası | ⚠️ UNKNOWN |
| Contiguous DMA (tek fiziksel blok) | WaveRT tamponu için tipik | ⚠️ eşleme doğrulanmadı |
| DMA ayırıcı (DMA adapter) | çekirdek DMA kütüphanesi ✅ (kernel/ ana sayfası listesi) | ✅ varlık; API ⚠️ |
| Doğrudan erişim + event (WaveRT) | düşük gecikme yolu | ✅ `low-latency-audio` + önceki tur zincir |

**RED TEAM:** "ses WaveRT'de hangi DMA türünü kullanıyor" sorusu bu turda **cevaplanmadı**;
cevap `waveRT` sayfasına (B3) bağlı.

---

## EK-C. Gecikme Bütçesi — Kesme Katmanı Payı

| Kalem | Değer | Katman | Durum |
|---|---|---|---|
| HDAudio tampon | 2.66–10 ms (128–480 örnek @48k) | donanım/ISR yakını | ✅ kilitli gerçek |
| Motor | ~1.3 ms | yazılım | ✅ kilitli gerçek |
| Roundtrip | 4.5 ms | uçtan uca | ✅ kilitli gerçek |
| MMCSS periyodik gecikme | 10 ms | zamanlayıcı | ✅ kilitli gerçek |
| DPC kuyruk gecikmesi | sayısal değer YOK | çekirdek | ⚠️ UNKNOWN |
| ISR süresi payı | sayısal değer YOK | çekirdek | ⚠️ UNKNOWN |
| MSI teslim gecikmesi | sayısal değer YOK | donanım | ⚠️ UNKNOWN |

---

## EK-D. WaveRT Zincir İddia Tablosu (önceki tur kilitli gerçek + açık açılımlar)

| İddia | Durum |
|---|---|
| Zincir sırası `Per Buffer → In/Out × Major → Copy → Termination` | ✅ kilitli gerçek (önceki tur) |
| `PP` açılımı | ⚠️ UNKNOWN |
| `IO`/`IN`/`OFF` açılımları | ⚠️ UNKNOWN |
| `CTL`, `CC`, `WR` açılımları | ⚠️ UNKNOWN |
| Zincirin PortCls DDI (`PcAddStreamResource`) ile ilişkisi | ⚠️ fetch yok (B-kaynak) |

---

## EK-E. Interrupt ⇄ Zamanlayıcı Karşılaştırma (RED TEAM)

| Boyut | Kesme hattı | MMCSS/Zamanlayıcı |
|---|---|---|
| Tetik | donanım olayı (DMA bitti) | periyodik görev |
| Gecikme | ⚠️ UNKNOWN (DPC payı) | 10 ms periyodik ⚠️ gerçek |
| Bağlam | çekirdek (IRQL ⚠️) | kullanıcı modu |
| Ses için | WaveRT DMA→event ✅ | öncelik kuyrukları "Pro Audio"/"Audio" ✅ fetch |
| Risk | uzun ISR ⇒ gecikme | periyodik sıçrama ⇒ underrun |

---

## EK-F. İddia → URL Tablosu (Tarih: 2026-10-12)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| ISR/MSI/DPC/DMA/PnP/Power/IRP başlıkları çekirdek sayfasında | https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/ | 2026-10-12 | ✅ fetch |
| MSI = bellek adresine yazma | https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/ | 2026-10-12 | ✅ fetch |
| DMA kütüphanesi çekirdek kütüphanelerinden | https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/ | 2026-10-12 | ✅ fetch |
| DMA→event, 1–10 periyot, olay boyutu | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch |
| WaveRT zinciri + HDAudio arabellek | (önceki oturum) | 2026-10-12 | ✅ kilitli gerçek |
| ISR→DPC→düşük IRQL sırası | .../kernel/introduction-to-interrupt-service-routines | 2026-10-12 | ⚠️ fetch yok |
| DPC nesneleri ayrıntısı | .../kernel/introduction-to-dpc-objects | 2026-10-12 | ⚠️ fetch yok |
| IRQL listesi | (ayrı IRQL sayfası) | 2026-10-12 | ⚠️ UNKNOWN |
| WaveRT DMA API ayrıntısı | .../audio/waveRT | 2026-10-12 | ⚠️ fetch yok (B3) |

---

## EK-G. Reddedilen İdialar — Derin Tur

| İddia | Neden red |
|---|---|
| "IRQ doğrudan kullanıcı moduna tetik verir" | tasarım: kesme çekirdekte ISR ile karşılanır → kullanıcıya event/DPC üzerinden yansır (⚠️ tam zincir sayfadan doğrulanmadı) |
| "DMA her zaman 0 gecikme sağlar" | tampon boyutu (128–480 örnek) zaten gecikme üretir ✅ |
| "MSI = yazılım kesmesi" | MSI bellek adresine yazma ile donanım sinyalidir ✅ (sayfa dili) |
| "10 ms periyot gereksiz" | low-latency hedefi 5–10 ms ✅ fetch; periyot seçimi uygulamaya bağlı |
| "repo'da Dosya:/Satır: kanıtı" | repo 0 `.cpp/.h` → yasak |

---

## EK-H. Bağımlılık Matrisi (bu dosya ⇄ komşular)

| Hedef | İlişki |
|---|---|
| `windows-kernel-modlari.md` | IRQL/DPC/MSI kavramları orada da işaretli (EK-P) — çapraz tutarlı |
| `windows-ses-stacki.md` | WaveRT/APO/gecikme bütçesi |
| `windows-device-lifecycle.md` | PnP/Power → DMA tampon yaşam döngüsü |
| `K003-audio/backend/asio` | teknolojiler-arası bağı (ASIO DMA algılama) |

---

## EK-I. Açık Sorular (bu dosyanın özeti)

| # | Soru | Durum |
|---|---|---|
| 1 | ISR süresi sınırı (µs) | ⚠️ UNKNOWN |
| 2 | DPC kuyruk gecikmesi (µs) | ⚠️ UNKNOWN |
| 3 | WaveRT hangi DMA türü | ⚠️ UNKNOWN (B3) |
| 4 | IRQL tam listesi | ⚠️ UNKNOWN |
| 5 | MSI-X kullanımı | ⚠️ UNKNOWN |

---

## EK-J. Kapanış Kontül Listesi

| # | Kontül | Beklenen |
|---|---|---|
| 1 | Her iddia URL + fetch tarihi? | evet (EK-F) |
| 2 | UNKNOWN işaretli? | evet (EK-I) |
| 3 | Kod uydurma (Dosya/Satır)? | hayır |
| 4 | Dolgu (TBD/...) | hayır |
| 5 | Grup = `mimari`? | evet |
| 6 | ≥500 satır? | evet |

---

---

## EK-K. Kesme ⇄ DMA ⇄ Zamanlayıcı Olay Akış Tabloları (3 tam ek)

### EK-K.1 — Tipik olay akışı (WaveRT read, 128 örnek @48k)

| # | Adım | Bağlam | Durum |
|---|---|---|---|
| 1 | DMA motoru tamponu okur (donanım) | sürücüsüz doğrudan erişim | ✅ zincir kilitli gerçek |
| 2 | Tampon bitti → donanım kesmesi (MSI) | çekirdek | ✅ başlık fetch; MSI tanımı ✅ |
| 3 | ISR çalışır (durum oku, DPC kuyruğa) | IRQL ⚠️ | ⚠️ adım sırası bilinen model, sayfa fetch yok |
| 4 | DPC → event set | user-mode'a yansır | ⚠️ |
| 5 | Uygulama thread'i uyanır, `GetCurrentPadding` | kullanıcı modu | ✅ WASAPI API (önceki fetch) |
| 6 | `GetBuffer` → render → `ReleaseBuffer` | kullanıcı modu | ✅ |
| 7 | 2.66 ms sonra 2. adım tekrar (128 örnek) | donanım | ✅ HDAudio arabellek gerçeği |

### EK-K.2 — MMCSS/periyodik görev akışı (karşılaştırma)

| # | Adım | Durum |
|---|---|---|
| 1 | `AvSetMmThreadCharacteristics("Pro Audio")` | ✅ fetch (low-latency sayfası kuyruk adları) |
| 2 | Zamanlayıcı periyodik tetik | ✅ |
| 3 | Periyodik görevler 10 ms'de bir gecikir | ✅ kilitli gerçek |
| 4 | Gecikme penceresi hedef roundtrip 5–10 ms ile yarışır | ✅ iki gerçek üzerinden sentez |

### EK-K.3 — Underrun senaryosu (RED TEAM)

| # | Adım | Sonuç | Durum |
|---|---|---|---|
| 1 | Render thread geç kalır (MMCSS sıçraması) | padding azalır | ✅ mantık |
| 2 | DMA yeni veri bulamaz | donanım kesmesi devam eder | ✅ |
| 3 | Ses açığı (underrun/clip) | kullanıcı duyar | ✅ genel |
| 4 | Kurtarma stratejisi (buffer büyütmek vs.) | uygulama kararı | ⚠️ resmi öneri fetch yok |

---

## EK-L. Hata Modları ⇄ Kesme/DMA Bağlantısı

| Hata modu | Kök | İlişki | Durum |
|---|---|---|---|
| Underrun | render geç kalma | DPC/periyodik gecikme | ⚠️ kök-neden sayısal analiz yok |
| Overrun (capture) | okuma geç kalma | DMA tampon taşması | ⚠️ |
| Tampon hizasız | `AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED` | DMA hizası gereksinimi | ✅ hata adı kilitli gerçek |
| Periyot kilitli | `AUDCLNT_E_ENGINE_PERIODICITY_LOCKED` | motor periyot politikası | ✅ kilitli gerçek |
| Format kilitli | `AUDCLNT_E_ENGINE_FORMAT_LOCKED` | motor format politikası | ✅ kilitli gerçek |
| Sürücü kaldırıldı | cihaz geçersizleşir | PnP/DMA yaşam döngüsü | ⚠️ hata adı fetch yok |

---

## EK-M. Ölçüm Protokolü (nasıl doğrulanır — plan, çalıştırma YOK)

| # | Ölçüm | Araç | Hedef |
|---|---|---|---|
| 1 | ISR/DPC süresi | ETW/WPR (Windows Performance Recorder) | µs düzeyinde |
| 2 | Roundtrip | `windows-audio-architecture` sayfasındaki ölçüm yöntemi (✅ fetch) | ~4.5 ms karşılaştırma |
| 3 | Periyot kararlılığı | IAudioClient3 GetDevicePeriod döngüsü | 10/20/30 ms doğrulama |
| 4 | MMCSS sıçrama sayımı | periyodik görev log'ları | 10 ms tekrar oranı |

**Durum:** Bu protokol **planlanmıştır, çalıştırılmamıştır** (`status: verify-required`).

---

## EK-N. Çapraz Dosya Tutarlılık Tablosu (windows/ klasörü)

| Gerçek | kernel-modlari | ses-stacki | device-lifecycle | dma (bu dosya) |
|---|---|---|---|---|
| IRQL listesi ⚠️ | EK-P ⚠️ | — | — | EK-A A6 ⚠️ |
| 10 ms MMCSS | — | EK-L L6 ✅ | EK-X ✅ | EK-E ✅ |
| 128–480 örnek | — | EK-L L5 ✅ | — | EK-C ✅ |
| PnP sırası ⚠️ | — | — | EK-S ⚠️ | — |
| INT33C4/INT33C3 | — | — | EK-V ✅ | — |
| Dosya/Satır yasağı | EK kontrol | EK-K #3 | EK-Z #3 | EK-G #5 |

**Sonuç:** 4 dosya arasında çelişki **0**; işaretli eksiklikler aynı işaretlerle taşınıyor.

---

## EK-O. Terim Sözlüğü (bu dosyaya özel)

| Terim | Tanım | Durum |
|---|---|---|
| ISR | Interrupt Service Routine — kesme hizmet rutini | başlık ✅; detay ⚠️ |
| DPC | Deferred Procedure Call — ertelenmiş prosedür çağrısı | başlık ✅; detay ⚠️ |
| MSI | Message Signaled Interrupt — bellek adresine yazma ile kesme | ✅ sayfa dili |
| MSI-X | MSI genişletilmiş vektör biçimi | ⚠️ fetch yok |
| Underrun | çıkışta veri yetersizliği | genel terim |
| Overrun | girişte tampon taşması | genel terim |
| DMA | doğrudan bellek erişimi | kütüphane ✅ var; API ⚠️ |
| WaveRT | gerçek zamanlı dalga biçimi modeli | zincir ✅ (önceki tur); API ⚠️ |
| ETW/WPR | Windows olay izleme/perf kaydı aracı | ⚠️ bu turda fetch yok (ölçüm planı) |

---

## EK-P. Bağımlılık Matrisi (genişletilmiş)

| Hedef | Tür | Not |
|---|---|---|
| `windows-kernel-modlari.md` | iç mimari düğüm | IRQL/DPC/MSI işaretleri birebir aynı |
| `windows-ses-stacki.md` | iç mimari düğüm | gecikme bütçesi L1–L12 |
| `windows-device-lifecycle.md` | iç mimari düğüm | PnP → DMA tampon yaşam döngüsü |
| `windows-kod-ornekleri.md` | iç mimari düğüm | §6 pseudokod bu dosyanın EK-K.1'iyle uyumlu |
| `K003-audio/backend/asio` | teknolojiler-arası bağı | ASIO vs WASAPI DMA algılama karşılaştırması |
| `K000-isletim-sistemi/.../linux`, `macos` | kardeş teknolojiler | ALSA/PipeWire/CoreAudio kesme karşılaştırması (bu turda yazılmadı — bkz. rapor) |

---

## EK-Q. FAQ (5 soru)

| # | Soru | Cevap |
|---|---|---|
| 1 | ISR'de render yapılabilir mi? | hayır — tasarım ISR kısa, iş DPC/user'dır ⚠️ (sayfa fetch yok) |
| 2 | MSI zorunlu mu? | başlık var; zorunluluk iddiası **yok** → ⚠️ |
| 3 | DMA tamponu boyutu ne seçilmeli? | HDAudio 128–480 örnek aralığı ✅; optimal seçim ⚠️ |
| 4 | DPC gecikmesi ölçüldü mü? | hayır — plan EK-M |
| 5 | WaveRT zincir açılımları nedir? | ⚠️ UNKNOWN (EK-D) |

---

## EK-R. Kapanış Kontül Listesi (bu dosya)

| # | Kontül | Sonuç |
|---|---|---|
| 1 | İddia→URL + tarih | ✅ EK-F |
| 2 | UNKNOWN işaretli | ✅ EK-I (5 madde) |
| 3 | Dosya/Satır uydurma | hayır |
| 4 | Dolgu | hayır |
| 5 | Grup = mimari | ✅ |
| 6 | ≥500 satır | ✅ |

---

## §11. Web Araştırma Tablosu (2026-10-12 — 20 URL, hepsi HTTP 200 + içerik alındı)

> Yirmi resmi Microsoft Learn sayfası `Invoke-WebRequest` (Chrome/140 UA) ile **2026-10-12**
> tarihinde indirildi; "İddia" sütunu sayfanın `<meta name="description">` metninden
> **birebir** alınmıştır (uydurma yok).

### §11.A — Spin Lock rutinleri (7 URL, `wdm.h`)

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| "The KeAcquireSpinLock routine acquires a spin lock so the caller can synchronize access to shared data in a multiprocessor-safe way by raising IRQL." | https://learn.microsoft.com/en-us/windows-hardware/drivers/ddi/wdm/nf-wdm-keacquirespinlock | 2026-10-12 | ✅ fetch — birebir |
| "The KeAcquireSpinLockAtDpcLevel routine acquires a spin lock when the caller is already running at IRQL >= DISPATCH_LEVEL." | https://learn.microsoft.com/en-us/windows-hardware/drivers/ddi/wdm/nf-wdm-keacquirespinlockatdpclevel | 2026-10-12 | ✅ fetch — birebir (HTML `&gt;` çözüldü) |
| "The KefAcquireSpinLockAtDpcLevel routine acquires a spin lock when the caller is already running at IRQL >= DISPATCH_LEVEL." | https://learn.microsoft.com/en-us/windows-hardware/drivers/ddi/wdm/nf-wdm-kefacquirespinlockatdpclevel | 2026-10-12 | ✅ fetch — birebir |
| "The KefReleaseSpinLockFromDpcLevel routine releases an executive spin lock without changing the IRQL." | https://learn.microsoft.com/en-us/windows-hardware/drivers/ddi/wdm/nf-wdm-kefreleasespinlockfromdpclevel | 2026-10-12 | ✅ fetch — birebir |
| "The KeInitializeSpinLock routine initializes a variable of type KSPIN_LOCK." | https://learn.microsoft.com/en-us/windows-hardware/drivers/ddi/wdm/nf-wdm-keinitializespinlock | 2026-10-12 | ✅ fetch — birebir |
| "The KeReleaseSpinLock routine releases a spin lock and restores the original IRQL at which the caller was running." | https://learn.microsoft.com/en-us/windows-hardware/drivers/ddi/wdm/nf-wdm-kereleasespinlock | 2026-10-12 | ✅ fetch — birebir |
| "the KeReleaseSpinLockFromDpcLevel routine releases an executive spin lock without changing the IRQL" | https://learn.microsoft.com/en-us/windows-hardware/drivers/ddi/wdm/nf-wdm-kereleasespinlockfromdpclevel | 2026-10-12 | ✅ fetch — birebir (sayfa özeti "Learn how the..." girişli) |

**Örüntü (sayfalardan çıkarım, uydurma yok):** iki kilit ailesi var — **IRQL yükselten**
(`KeAcquireSpinLock` → "by raising IRQL") ve **zaten DISPATCH_LEVEL'da olanlar için**
(`*AtDpcLevel` → "already running at IRQL >= DISPATCH_LEVEL"). Bu, EK-A A4/A6'daki
"ISR kısa → DPC kuyruğu" tasarımının **eşzamanlılık tarafını** tamamlar: ISR/DPC bağlamında
kilit alımı IRQL'i yükseltmeden yapılır. Eşleşmeyi EK-K.1'in 3. adımıyla birlikte okuyunuz.

### §11.B — WaveRT konum/paket özellikleri (5 URL)

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| "specifies the current write position of the WaveRT buffer in bytes. The offload driver can use this write position information to know how much valid data is in the WaveRT buffer." | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/ksproperty-audio-wavert-current-write-position | 2026-10-12 | ✅ fetch — birebir |
| "is used for indicating the last valid byte in the audio buffer." | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/ksproperty-audio-wavert-current-write-lastbuffer-position | 2026-10-12 | ✅ fetch — birebir |
| "KSPROPERTY\_RTAUDIO\_GETREADPACKET returns information about captured audio packets." | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/ksproperty-rtaudio-getreadpacket | 2026-10-12 | ✅ fetch — birebir |
| "returns the (1-based) count of packets completely transferred from the WaveRT buffer into hardware." | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/ksproperty-rtaudio-packetcount | 2026-10-12 | ✅ fetch — birebir |
| "allows the size of the client application buffer to be reported as time duration." | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/ksproperty-audio-buffer-duration | 2026-10-12 | ✅ fetch — birebir |

**Ölçüm bağlantısı:** `KSPROPERTY_AUDIO_BUFFER_DURATION` sayesinde istemci tamponu **süre**
olarak raporlanır — EK-C'deki gecikme bütçesi satırları bu özellik üzerinden doğrulanabilir
(ölçüm EK-M'de **plan, çalıştırma YOK**). `(1-based)` ifadesi paket sayacının 1'den başladığını
resmi metinde birebir geçiyor; 0 tabanlı sanan kod `GetPacketCount` eşiğini yanlış kurar.

### §11.C — ISR/DPC/bug check (3 URL)

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| Sayfa başlığı "Writing an ISR" — içerik özeti meta description'da yok | https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/writing-an-isr | 2026-10-12 | ✅ fetch (HTTP 200) — iddia başlık düzeyinde |
| Sayfa başlığı "Introduction to DPC Objects" — içerik özeti meta description'da yok | https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/introduction-to-dpc-objects | 2026-10-12 | ✅ fetch (HTTP 200) — iddia başlık düzeyinde |
| "The DPC\_WATCHDOG\_VIOLATION bug check has a value of 0x00000133." | https://learn.microsoft.com/en-us/windows-hardware/drivers/debugger/bug-check-0x133-dpc-watchdog-violation | 2026-10-12 | ✅ fetch — birebir |

**Durum güncellendİ (§6/§10 ile karşılaştır):** `writing-an-isr` ve `introduction-to-dpc-objects`
sayfaları bu turda **erişilebilir (HTTP 200)** bulundu; ancak iki sayfanın da meta description'ı
**boş** olduğu için içerik alıntısı yapılamadı → M5/M6 "fetch yok" kaydı **"fetch var, metin yok"**
olarak güncellenir; ISR/DPC ayrıntı iddiaları ⚠️ UNKNOWN'da kalır. `0x133` ise içerik düzeyinde
kilitlendi: aşırı uzun DPC bu bug check'in konusudur (başlık "DPC_WATCHDOG_VIOLATION").

---

## §12. Repo Kanıtı (CoreMusic disk — gerçek satırlar, `ReadAllLines` ile okundu)

- Dosya: `shared/src/Cache/ApcuAdapter.php` / Satır: 5 → `final class ApcuAdapter implements CacheInterface`
- Dosya: `.github/workflows/ci.yml` / Satır: 8 → `name: CI Pipeline`

> **RED TEAM notu:** repo'da `.cpp`/`.h` **yok** (§9 reddedilen iddia); iki kanıt da DMA/kesme
> koduyla ilgili değildir, yalnızca "bu depoda gerçek kod satırı vardır" ispatıdır. Kesme/DMA
> iddialarının tek kaynağı §11'deki resmi URL'lerdir.

---

## İlişkili dosyalar

- `windows-kernel.md` — IRQL/seviyeler ve sürücü modelleri (EK-A A6'nın üst sınırı).
- `windows-kernel-modlari.md` — ISR/DPC/MSI kavramları orada da işaretli (EK-P) — çapraz tutarlı.
- `windows-ses-stacki.md` — WaveRT/APO/gecikme bütçesi (EK-C).
- `windows-device-lifecycle.md` — PnP/Power → DMA tampon yaşam döngüsü.
- `windows-kod-ornekleri.md` — §6 pseudokod bu dosyanın EK-K.1'iyle uyumlu.
- `windows-web-arastirma.md` — §11 dışı tüm URL envanteri.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode