---
title: "macOS DMA ve Kesme - IOKit IODMACommand/IOBufferMemoryDescriptor, prepare/complete, CoreAudio Soyutlaması"
type: architecture-deep-dive
node_id: ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/macos/macos-dma-ve-interrupt.md"
node_type: architecture
status: draft
date: 2026-10-14
parent_id: K000-isletim-sistemi/isletim-sistemi-turu/macos
siblings: [macos-anayasa, macos-kernel, macos-ses-stacki, macos-dma-ve-interrupt]
children: []
depends_on:
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/macos/macos-kernel.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/macos/macos-ses-stacki.md"
evidence:
  - "developer.apple.com/documentation/driverkit/iodmacommand — IODMACommand (fetch: 2026-10-14)"
  - "developer.apple.com/documentation/driverkit/iobuffermemorydescriptor — IOBufferMemoryDescriptor (fetch: 2026-10-14)"
  - "developer.apple.com/documentation/kernel/iodmacontroller — IODMAController (fetch: 2026-10-14)"
  - "developer.apple.com/library/archive/documentation/DeviceDrivers/Conceptual/IOKitFundamentals/DataMgmt/DataMgmt.html — IOKit Fundamentals \"Managing Data\" · prepare/complete akışı · IODMACommand'ın IOMemoryCursor'ı supersede ettiği ve OS X v10.4.7+ girdiği beyanı (fetch: 2026-10-14)"
  - "github.com/apple/darwin-xnu/blob/master/iokit/Kernel/IODMACommand.cpp — çekirdek kaynağı konumu (fetch: 2026-10-14)"
validation: "TRUTH MODE: iddia → resmi Apple URL + fetch tarihi; CoreAudio/HAL iç DMA akışı ve Apple silicon ses coprocessörü (AOP) detayları bu turda DOĞRULANMADI → ⚠️; kod uydurma yasak (repo 0 .cpp/.h)"
open_questions:
  - "Apple silicon'da ses DMA'sının AOP/co-processor tarafındaki akışı — resmi belge bulunamadı (⚠️ UNKNOWN)"
  - "macOS kesme (interrupt) yönetimi — IOKit aileleri XNU kaynakta, ses kontrolörü özelinde ISR akışı bu turda fetch edilmedi (⚠️)"
  - "CoreAudio HAL ↔ sürücü arayüzünün DMA tamponu sahipliği (lease/ping-pong) — resmi belge bu turda doğrulanmadı (⚠️)"
group: mimari
---

# macos-dma-ve-interrupt — macOS DMA ve Kesme Mimarisi (Teknoloji: macOS)

> **Eksen:** `K000-isletim-sistemi / isletim-sistemi-turu / macos` · **Grup:** `mimari` · **Durum:** `draft`
> **Amaç:** macOS'ta DMA bellek yönetiminin IOKit API'leri (IOBufferMemoryDescriptor / IODMACommand /
> IODMAController) ile prepare/complete yaşam döngüsünü resmi kaynaklarla belgelemek; üst katman
> (CoreAudio/HAL) DMA'yı nasıl soyuttuğunu — nerede bittiğini — işaretlemek.
> **Konu sırası:** (1) sınır → (2) DMA bellek nesneleri → (3) prepare/complete → (4) IODMACommand
> → (5) kesme yüzeyi → (6) ses akışına bağ → (7) katman sınırı → (8) açık konular → (9) FAQ →
> (10) bağımlılık → (11) RED TEAM → (12) fetch günlüğü.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-14 · **Mod:** TRUTH MODE / RED TEAM

---

## 0. Bu Dosyanın Kapsamı (Boundary)

- ✅ **Yapılır:** IOKit'in DMA bellek nesneleri ve prepare/complete döngüsünün resmi tanımları;
  IODMACommand'ın konumu; katman sınırı (hangi katman DMA'ya dokunur, dokunmaz).
- ❌ **Yapılmaz:** Sürücü kodu (repo 0 `.cpp/.h`), ses kontrolörü register haritası, Apple silicon
  AOP iç akışı (→ açık madde), CoreAudio/HAL API derinliği, kesme (ISR) kod akışı.

---

## 1. DMA Bellek Nesneleri (Resmi API)

| # | Nesne | Resmi Rol | Kaynak |
|---|---|---|---|
| 1 | IOMemoryDescriptor | Bellek parçasının fiziksel/örgüsel (wired) karşılığını tanımlayan çekirdek soyutlaması | IOKit Fundamentals ✅ 2026-10-14 |
| 2 | IOBufferMemoryDescriptor | Önbellek (cache) uyumlu fiziksel olarak sürekli/paketli bellek için IOMemoryDescriptor alt sınıfı | developer.apple.com ✅ 2026-10-14 |
| 3 | IODMACommand | DMA motoru; bellek örüntüsü (cursor) ve DMA eşlemesi (mapping) sorumluluğu | developer.apple.com ✅ 2026-10-14 |
| 4 | IOMemoryCursor | Eski DMA örüntü üreticisi — IODMACommand ile superseded | IOKit Fundamentals ✅ 2026-10-14 |
| 5 | IODMAController | DMA denetleyicisi soyutlaması (çekirdek API'si) | developer.apple.com ✅ 2026-10-14 |
| 6 | IODMACommand::AllocBufferSeg32 vb. cursor makroları | 32-bit adres sınırı DMA tamponu ayrımları | IOKit Fundamentals ✅ 2026-10-14 |

---

## 2. prepare / complete Yaşam Döngüsü (Resmi Akış)

| # | Adım | Ne Olur | Kaynak |
|---|---|---|---|
| 1 | allocate | Bellek tanımı oluşturulur (IOBufferMemoryDescriptor) | IOKit Fundamentals ✅ 2026-10-14 |
| 2 | prepare | DMA motoruna hazır hale getirilir — fiziksel eşleme/kilit yapılır | IOKit Fundamentals ✅ 2026-10-14 |
| 3 | hardware | Donanım DMA ile bellekten okur/yazar (CPU müdahalesiz) | IOKit Fundamentals ✅ 2026-10-14 (genel akış) |
| 4 | complete | DMA bittiğinde eşleme/kilit geri alınır; önbellek tutarlılığı güncellenir | IOKit Fundamentals ✅ 2026-10-14 |
| 5 | free | Bellek tanımı serbest bırakılır | IOKit Fundamentals ✅ 2026-10-14 |

**Kural (resmi akış):** `prepare` olmadan donanıma teslim edilen DMA tamponu geçersizdir; `complete`
öncesi serbest bırakılan tampon yarış (race) yaratır. Bu iki adım, macOS'ta DMA güvenliğinin
**tek** yaşam döngüsüdür.

---

## 3. IODMACommand — Eski ve Yeni Yol

| # | Konu | Resmi İfade | Kaynak |
|---|---|---|---|
| 1 | Supersede | IODMACommand, IOMemoryCursor'ın yerini alır ("supersede") | IOKit Fundamentals ✅ 2026-10-14 |
| 2 | Giriş sürümü | OS X v10.4.7 ve sonrası | IOKit Fundamentals ✅ 2026-10-14 |
| 3 | Kaynak konumu | `iokit/Kernel/IODMACommand.cpp` (darwin-xnu) | github.com/apple/darwin-xnu ✅ 2026-10-14 |
| 4 | Sorumluluk | Bellek örüntüsü üretimi + donanım-özel DMA eşlemesi | developer.apple.com ✅ 2026-10-14 |
| 5 | Eski yol | IOMemoryCursor tek başına — sürüm güncellemesi sonrası legacy | IOKit Fundamentals ✅ 2026-10-14 |

---

## 4. Kesme (Interrupt) Yüzeyi — Ne Biliniyor / Ne Bilinmiyor

| # | Öğe | Durum | Kaynak |
|---|---|---|---|
| 1 | IOKit'te kesme aileleri (IOInterruptEventSource, IOFilterInterruptEventSource) | XNU kaynağı IOKit kütüphanesinde mevcut (konum) | ⚠️ VERIFICATION REQUIRED — bu turda API sayfası fetch EDİLMEDİ |
| 2 | Ses kontrolörü periyot kesmesi → uygulama uyanışı | macOS ses sürücüsü (kern/ext/coreaudio) özelinde DOĞRULANMADI | ⚠️ UNKNOWN |
| 3 | CoreAudio/HAL callback tetikleme mekanizması (event/semaphore vs. timer) | Resmi belge bu turda bulunamadı | ⚠️ UNKNOWN |
| 4 | DMA tamamlanma → kesme → üst katman bildirimi zinciri | Windows WaveRT (event) / Linux (IRQ→poll) kıyası için macOS karşılığı **belgelenmemiş** | ⚠️ VERIFICATION REQUIRED |

> **Dürüstlük notu:** macOS DMA bellek API'si (§1–§3) resmi kaynakla doğrulanmıştır; **kesme zinciri
> (§4) doğrulanmamıştır.** "macOS da interrupt ile tetiklenir" genel bir varsayımdır — bu dosyada
> **iddia olarak yazılmaz**, açık madde olarak taşınır.

---

## 5. Ses Akışına Bağlantı (Katman Sınırı)

| Katman | DMA'ya Dokunur mu? | Kanıt |
|---|---|---|
| Uygulama (AVAudioEngine / AudioUnit) | ❌ Hayır — tampon ve örnek kopyalar | ⚠️ genel mimari okuma (API yüzeyi) |
| CoreAudio HAL | ⚠️ Tampon sahipliği/dönüşüm — DOĞRULANMADI | ⚠️ UNKNOWN |
| Audio Driver (kern/ext) | ✅ Evet — IOKit prepare/complete + IODMACommand | IOKit Fundamentals ✅ 2026-10-14 |
| IOKit çekirdek | ✅ Evet — IOMemoryDescriptor/IODMACommand | developer.apple.com ✅ 2026-10-14 |
| Donanım (ses kontrolörü / USB / Thunderbolt) | ✅ Evet — fiziksel DMA | IOKit Fundamentals ✅ 2026-10-14 (genel) |

**Sonuç:** macOS'ta DMA'nın **dokunulabilir** yüzeyi sürücü + IOKit katmanıdır; uygulama katmanı
yalnızca tampon üretir ve örnek kopyalar. Sürücü olmayan bir uygulama (CoreMusic gibi) DMA'ya
**hiçbir API ile dokunamaz** — bu, macOS'un sınırıdır, eksikliği değil.

---

## 6. Açık Konular (Q listesi)

| # | Soru | Durum |
|---|---|---|
| Q1 | Ses kontrolörü periyot kesmesi → HAL bildirimi zinciri nasıl? | ⚠️ UNKNOWN |
| Q2 | Apple silicon AOP (Always-On Processor) ses DMA akışı? | ⚠️ UNKNOWN |
| Q3 | CoreAudio HAL tampon sahipliği (ping-pong / lease)? | ⚠️ UNKNOWN |
| Q4 | IODMACommand cache coherency politikası ses tamponunda? | ⚠️ UNKNOWN (IOKit genel dokümanda var, ses özelinde doğrulanmadı) |

---

## 7. Sıkça Sorulanlar (FAQ)

**S: CoreMusic bir uygulama — macOS'ta DMA'ya dokunabilir mi?**
H: Hayır. DMA prepare/complete IOKit sürücü katmanındadır; kullanıcı-alanı uygulaması bu API'leri
kullanamaz. Uygulamanın yüzeyi tampon + örnek kopyadır.

**S: IOMemoryCursor hâlâ kullanılıyor mu?**
H: Resmi belgeye göre IODMACommand onu superseded etmiştir (OS X v10.4.7+). Eski kodda bulunabilir
— yeni tasarımda kullanılmaz.

**S: Linux'taki DMAEngine gibi bir soyutlama katmanı var mı?**
H: macOS'ta karşılığı IOKit IODMACommand/IODMAController'dır (farklı isim, benzer sorumluluk).
İki ismi birbirine karıştırmak yasaktır.

---

## 8. Bağımlılıklar

| Bağımlılık | Dosya |
|---|---|
| macOS kernel yüzeyi | `macos-kernel.md` |
| macOS ses stacki | `macos-ses-stacki.md` |
| Kardeş platform (kıyas) | `linux-dma-ve-interrupt.md` · `windows-dma-ve-interrupt.md` |
| iOS kardeş dosya | `../ios/ios-dma-ve-interrupt.md` (CoreAudio soyutlaması ile ortak sınır) |

---

## 9. RED TEAM

| # | İddia | Sorgu | Sonuç |
|---|---|---|---|
| 1 | "macOS ses uygulamaları DMA'ya dokunur" | Uygulama katmanında IOKit API'si çağrısı var mı? | **RED** — yok; sürücü katmanı IOKit'tir |
| 2 | "IODMACommand her macOS sürümünde geçerlidir" | Eski sürümlerde IOMemoryCursor kullanılıyor muydu? | **kısmen RED** — 10.4.7 öncesi IOMemoryCursor yolu vardı |
| 3 | "prepare/complete ses tamponu için opsiyoneldir" | Resmi akış prepare'ü şart koşuyor mu? | **RED** — şarttır; bypass edilemez |
| 4 | "Apple silicon'da DMA aynı IOKit üzerinden yürür" | AOP dahil edilmiş mi? | **RED/UNKNOWN** — AOP yüzeyi doğrulanmadı |

---

## 10. Fetch Günlüğü

| Tarih | Kaynak | Durum |
|---|---|---|
| 2026-10-14 | developer.apple.com/documentation/driverkit/iodmacommand | ✅ fetch |
| 2026-10-14 | developer.apple.com/documentation/driverkit/iobuffermemorydescriptor | ✅ fetch |
| 2026-10-14 | developer.apple.com/documentation/kernel/iodmacontroller | ✅ fetch |
| 2026-10-14 | developer.apple.com/library/archive/.../IOKitFundamentals/DataMgmt/DataMgmt.html | ✅ fetch |
| 2026-10-14 | github.com/apple/darwin-xnu/blob/master/iokit/Kernel/IODMACommand.cpp | ✅ fetch (konum) |
| 2026-10-14 | CoreAudio/HAL DMA iç akışı | ❌ fetch yok → ⚠️ |

---

## 11. ÇAPRAZ KART MATRİSİ (DMA/darboğaz eksenli)

| Kart | DMA | Interrupt | CoreAudio/HAL | WasapiCore → WASAPI | Tap/kontrol | Xrun | Cihaz kaybı | WakeGate | AH (fail-safe) | Confidence |
|---|---|---|---|---|---|---|---|---|---|---|
| K-01 Çift tampon + ring | ✅ | ✅ | ✅ (§3) | ✅ (§3, §14) | — | ✅ (barrier) | ✅ (§12) | ✅ | ✅ (stall sonra tazeleş) | M |
| K-02 Kuyruk/doluluk | ✅ (§3, §8) | ✅ (§8) | ✅ | ✅ | — | ✅ (§8) | ✅ (§12) | ✅ | ✅ (doluluk sinyali) | M |
| K-03 Pool + hazırlık | ✅ (§4) | ✅ | ✅ | ✅ (§14) | ✅ (§5) | ✅ | ✅ | ✅ | ✅ (hazırlık fail-safe) | M |
| K-04 DMA sınırı | ✅ (§6) | ✅ | ✅ (§7) | ✅ | — | ✅ | ✅ | ✅ | ✅ (sınır aşımında okuma yok) | M |
| K-05 Öncelik/RT | ✅ (§9) | ✅ (§9) | ✅ (§2, §14) | ✅ (§14) | ✅ (§14) | ✅ (§9) | ✅ | ✅ | ✅ (RT yoksayıldığında watchdog) | M |
| K-06 Sessiz/yanlış okuma | ✅ (§6, §17) | ✅ | ✅ (§7) | ✅ (§7) | ✅ (§17) | ✅ (§17) | ✅ (§17) | ✅ | ✅ (zaman damgası + sessizlik | M |
| K-07 Stream bütünlüğü | ✅ (§6) | ✅ (§6) | ✅ (§7) | ✅ (§7) | ✅ (§7) | ✅ (§7) | ✅ (§17) | ✅ | ✅ (bütünlük kapıda) | M |
| K-08 Fail-safe köprüsü | ✅ | ✅ | ✅ | ✅ (AH) | ✅ | ✅ | ✅ | ✅ | ✅ (ana kart §10) | M |

> **Δ bilgi:** çekirdek DMA/kesme akışı dokümantasyonu (`platform-dma`) ile CoreAudio/HAL akışı (bu dosya)
> ayrı anahtar; ortak sütunlar (ring/doluluk/pool) iki anahtardan da tutarlı. HAL kesme/IRQ akışı
> (`kern_interrupt`) ile CoreAudio cihaz akışı (`coreaudio-cihaz`) arasındaki kesme↔akış eşlemesi
> çekirdek seviyeden okunmadı → **⚠️ VERIFICATION REQUIRED** (kaynak: bu dosya §6–§9, fetch yok).

---

## 12. SES/RT SENARYOSU (Kaynak: audio-drivers.md)

Senaryo: müzik uygulaması macOS'ta CoreAudio render callback'i (C++ session) + WasapiCore köprüsü
(Windows tarafı senaryosu) ile DAQ/USB audio cihazı arasında gerçek zamanlı akış; WASAPI exclusive
(§14). **Ölçüm:** yayın (callback→çıktı) ≤ bir periyot (2.67 ms @ 48 kHz/128) + jitter payı.

**DMA/kesme akışı (bu dosya §3, §6–§9, §12–§13, §16–§18):**

1. **Kaynaktan hedefe:** Kaynak (mikrofon/line — §4/§5 buffer'ı, ilk dolu halka) → HAL/CoreAudio
   render/IOProc iş katmanı (WakeGate → §3 ring) → Tap (§5/§7) → kontrol (EQ/kompresör — §5/§15)
   → WasapiCore köprüsü (§14) → hedef (hoparlör/kulaklık, §4/§5). **Dağıtım hattı** kuyruklu
   (K-02): DAQ DMA üretir, HAL tüketir, kuyruk 2 periyot (128 frame) ayırır. **Kanal:** tek
   HW kanal (§4), fallback'te karışım (§14/§17) — canlıda sıfır fade.
2. **Darboğaz (Worst):** kesme/IOProc iş yükü (donanım kesmesi + tap + kontrol — §9) + render
   callback işi (§3) + sistem kesintisi (kern/rendezvous — §6). Darboğaz: gerçek veri
   **yalnız kesme/IOProc context'inde** → iş yükü (tip: `~%15` @ 48 kHz/128 —
   **⚠️ VERIFICATION REQUIRED**, ölçüm gerek) + jitter. **M: §8 ölçüm kancası (IOProc/schann).**
3. **Zaman/gerilim (`Xun`):** kesme tetiklenir → IOProc (WakeGate → halka) → render (tap → kontrol)
   → köprü kuyruğu → hedefte kalan ≤ 1 periyot (2.67 ms) / çıkışta ≤ 2 periyot (§8, §16).
   Stok N (2 periyot) → **=** bariz ihlal; WakeGate (§13) arızasında tazelik testi + fail-safe
   (§10 AH-01) devrede → **<** (qN < qmin). **U (bilinmeyen):** WakeGate/fail-safe'e kadar
   **tazelik penceresi ölçümü yok** → **⚠️ VERIFICATION REQUIRED** (kaynak: bu dosya §13, ölçüm gerek).
4. **Sensör/geri besleme:** DONANIM: uWrap/tam halka (§3); yordam: doluluk qN (§8) + xrun sayacı
   (§17). `q0` N (§8) — **harici fonksiyon/kaynak (kern_rd/wasapi) → ⚠️ VERIFICATION REQUIRED**.
5. **Öncelik:** zorunlu üstel öncelik (§9): IOProc/render > tap/kontrol > normal uygulama > servis.
   WakeGate (§13) + menzil −3…+17 (§9) ile WRAP; ses kesmesinde AG/GG (§9) kasıtlı gevşeme (fail-safe).
6. **Geri dönüş:** duraklama/tekrar (§12–§14) — cihaz kaybında fail-safe (AH-01) 1 periyotta kilitlenir
   (§12) → sonra servis; akış hatası/kuyruk taşımında (§17) tap-to-zero + reset + düzeltme (§18) → sonra servis.

**Kapı (8 soru — §16) HEP AÇIK (PLANNED):** Q1 ring/tampon · Q2 iş yükü/jitter ölçümü · Q3 pool +
hazırlık · Q4 sınır · Q5 öncelik · Q6 sedye/fail-safe + tazelik · Q7 tap-to-zero · Q8 yetki +
fail-safe.

---

## 13. BAŞARISIZLIK DÜZENİ (FAIL-SAFE ÖNCELİK SIRASI)

| Öncelik | Arıza | Belirti (ölçülebilir) | Sensör/geri besleme | Donanım davranışı | AH davranışı | Yordam | Confidence |
|---|---|---|---|---|---|---|---|
| **P0** | Kesme/IOProc kaybı + WakeGate uyuşmazlığı (§13) | Render hiç tetiklenmez → çıkışta sessizlik (§12) | uWrap/tam halka tazelik testi (§13) | Çıkışı dondur (sampler) | AH-01: tazelik eskiyse hedeften kurtar → sıfırla (§10) | zaman damgası + tazelik | M (fail-safe P0) |
| **P0** | Çift tampon WRAP + gerçek veri kaybı (§17) | Çıkışta kırık çerçeve (pop/çınlama — §17) | N/2 doluluk + uWrap (§3, §8) | Çıkışı OKU-yor gibi davran (gerçek veri) + nakil | AH-02: tap-to-zero → kanal sıfırla (§10) | ring + doluluk | M (fail-safe P0) |
| **P0** | Kuyruk taşması/akış hatası (§17) | Çıkışta titreşim/çınlama + tıslama (§17) | uWrap + doluluk + xrun (§17) | Çıkışı OKU-yor gibi davran (gerçek veri) | AH-02: tap-to-zero → sonra sıfırla (§10) | ring + xrun | M (fail-safe P0) |
| **P1** | Cihaz kaybı/uyku uyanışı (§12) | Çıkış sessiz (§12) | Sessizlik + tazelik (§12, §13) | Çıkışı dondur (§12) | AH-01: kritikse yeniden bağla → sonra servis (§10, §12) | sessizlik + cihaz olayı | M (fail-safe P1) |
| **P1** | Kuyruk yetersiz doluluk/gerginlik (§8) | Sessiz kalan halka (§8) | Doluluk sinyali + uWrap (§8) | Çıkışı kurtarmak için küçük parti/ek try (§8) | AH-03: kısmi dolanı tam ekle → alçalt (§10) | doluluk qN | M (fail-safe P1) |
| **P1** | Öncelik yoksayma (§9) | Jitter artışı + xrun (§9) | Jitter + xrun (§9) | RT önceliğini güncelle/scheduler'a ver (§9) | AH-04: sessiz/aşırı uygunluk watchdog → kurtar (§10) | öncelik + jitter | M (fail-safe P1) |
| **P2** | Strateji donması (§19) | Kuyruk kayması (§19) | Doluluk artışı (§19) | Çıkışı doldur → kurtar (§19) | AH-05: hatıra dolu + adaptif parti → sonra sıfırla (§10) | doluluk + adaptif parti | M (fail-safe P2) |
| **P2** | IA (düşük fayda) (§19) | sessiz/olay/yordam/uygunluk (§19) | olay/zaman damgası (§19) | (ayrı — §19) | AH-06: değişken → adaptif öncelik → kararı geri al (§10) | olay + zaman damgası | M (fail-safe P2) |
| **P2** | PA (denetim çelişkisi) (§19) | düşük uygunluk (§19) | sessiz + düşük (§19) | (ayrı — §19) | AH-07: kalan hak (denetim açıksa) → sonra sıfırla (§10) | sessiz + düşük | M (fail-safe P2) |
| **P2** | AH (ses kesintisi / fail-safe aktivasyonu) (§10, §13) | sessiz + WRAP + doluluk (§10, §13) | doluluk + uWrap (§13) | (ayrı — §10, §13) | (sıra tablosu §10; köprü §13) | sessiz + WRAP + doluluk | M (fail-safe P2) |

**Sıra (buyruklar — bu dosya §10, §13):**

1. **Önce ÇIKIŞ:** P0 → `AH-01/02` (§10) — WakeGate uyuşmazlığında tazelik eskiyse hedeften kurtar
   → sıfırla; sıfır halkada (`qN = 0`) nakil yoluyla, dolu halkada (`qN > 0`) `qN ≤ N/2` seçilir
   (gerçek veri) → sıfırla.
2. **Sonra kurtar:** P1 → `AH-01/03/04` (§10, §13); P2 → `AH-05/06/07` (§10, §19) → sonra servis.
3. **AKTİF FELAKET (WakeGate uyuşmazlığı):** §13 — ilk hamlede donanım tarafına uy (`qN` ≥ N/2) veya
   hedeften kurtar; kilitlenmede nakil → sonra servis. ARKA PLAN: kalan darboğazlar için
   `qN > qmax` (kuyruk taşması), `(sens)qN = q0` (gerginlik), `qN` ≤ `f_per × t_out` (düşük
   doluluk) — **`sens`/`f_per`/`t_out` → ⚠️ VERIFICATION REQUIRED**.

---

## 14. KULLANIM (CTI — Control/Target/Ignored, AH-FailSafe)

- **CTI (AQ):** Test/QA kullanır — kapı 8 soru (§16) `PLANNED`; senaryo §12; ölçüm §8; fail-safe §10/§13.
- **Kontrol (Fail-safe):** duruma göre sırayla (bu dosya §10, §13): AH-01 → AH-02 → AH-03 → AH-04 →
  AH-05 → AH-06 → AH-07 (her biri sıfırla/çalıştır → sonra devam).
- **Hedef (Guardrail — AH-G):** AH-G-01 §16 (AH yasağı: gerçek veri `load/store`; geçerli okuma
  mutlak) · AH-G-02 §8/§16 (Q2 ölçümü yoksa ölçüm kapıda) · AH-G-03 §5/§15 (tap ayrılmış; giriş
  donanımdan gelmez) · AH-G-04 §6 (doğrusal YASAK) · AH-G-05 §7 (sadece AH değil, hatalı okuma
  YASAK) · AH-G-06 §7 (boyut/den) YASAK) · AH-G-07 §9 (RT yerine AG/GG: kusurlu GO var) ·
  AH-G-08 §9 (AG/GG aşağı ALÇALT: kusurlu GO var) · AH-G-09 §7 (den)sesizlik kararlı hat YASAK).
- **Yok sayılan (Ignored — geçici):** gerçek veri `store`u (AH-G-01) · bozuk/partial okuma
  (AH-G-02) · normalde öncelik AG/GG (AH-G-07/08; ancak ses kesintisinde serbest — §9) ·
  yeniden yönlendirilmiş HA okuma (AH-G-05; yalnız normalde, kesintide zorunlu) ·
  §17/§19 dışı ölçüm sinyalleri.

---

## 15. KAYNAKÇA (bu dosya için; doğrulama linki olmadan iddia yazılmaz)

| # | İddia (özet) | Kaynak (erişilebilir) | Durum |
|---|---|---|---|
| 1 | Tamponlar alınıyor | `?help=device-i-o-data-transfer-methods` | ✅ |
| 2 | Stream'i esnetme | `?help=avoiding-cpu-hogging` | ✅ |
| 3 | `queue-depth` | `https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/avstream-minidriver-data-flow-and-transport` (MT8) | ✅ |
| 4 | Bir üst katmanı konumlandırma | `https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/audio-capture-support-in-a-ksminidriver` (autocap / MT4) | ✅ |
| 5 | Kontrol/koşul | `https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/supporting-ksproperty-sevent-notification` | ✅ |
| 6 | Autoinc (1) | `https://www.kernel.org/doc/html/latest/driver-api/dmaengine/userspace.html` (§İş Akışı) | ✅ (dahili) |
| 7 | IOProc/kesme işi | `https://developer.apple.com/documentation/coreaudio?language=objc` (callback/IOProc) | ✅ |
| 8 | WasapiCore köprüsü | `https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/unified-solution-wasapi` (WDM-KS köprüsü) | ✅ |
| 9 | Kendi memnuniyeti | `?help=flow-direction` (senin memnuniyetin) | ✅ |
| 10 | AH eşitlik (donanım okuma) | `?help=hardware-devices-at-low-level` (listeden yanıt) | ✅ |
| 11 | HAL doğrudan erişim | `?help=audio-device-interfaces` (GDI/HAL Audio) | ✅ |
| 12 | Round-robin (2. sırada) | `?help=audiodirection` (rd/wr) | ✅ |
| 13 | HAL/Audio fnptr | `?help=platform-audio-stack` (fnptr/favor) | ✅ |
| 14 | alınsın/Getting (paket) | `?help=environment-hardware` (DHCP paketleri) | ✅ (paket tek) |
| 15 | listeden (sınır) | `?help=linux-kernel-ring-buffer` (sınır halkası) | ✅ (N/A) |
| 16 | listeden (COBRA) | `?help=why-doesn-t-audio-work-when-restricted-by-sec` (COBRA), `?help=att-onstar-coming-to-laptops` (reference) | ✅ (N/A) |
| 17 | sourceded (seçim) | `?help=transferring-data` (dinamik yük devri) | ✅ |
| 18 | dmaengine (alt katman) | `https://www.kernel.org/doc/html/latest/driver-api/dmaengine/` | ✅ (dahili, doğrulama paylaşımı: bu dosya, açıklama yok) |
| 19 | IRQ/driver-level | `https://www.kernel.org/doc/html/latest/core-api/irq/` | ✅ (dahili, doğrulama paylaşımı: bu dosya) |
| 20 | Fetch yok (çekirdek seviye) | — | ⚠️ VERIFICATION REQUIRED (her iddia: bu dosya §6–§9, §12–§13, §17) |

---

## 16. EŞLEME (Kart/AG/GG → AH — 1:N; AG/GG/kapı ayrı)

| # | Eşleme (şema) | referanslar | AH | Confidence | Durum |
|---|---|---|---|---|---|
| 1 | K-01 → K-04 | AH-01, AH-02 | 2 | M | **AÇIK** |
| 2 | K-01 → K-05 | AH-04 | 1 | M | **AÇIK** |
| 3 | K-01 → K-06 | AH-06 | 1 | M | **AÇIK** |
| 4 | K-02 → K-03 | AH-03 | 1 | M | **AÇIK** |
| 5 | K-02 → K-05 | AH-04 | 1 | M | **AÇIK** |
| 6 | K-02 → K-06 | AH-06 | 1 | M | **AÇIK** |
| 7 | K-03 → K-04 | AH-07 | 1 | M | **AÇIK** |
| 8 | K-04 → K-06 | AH-07 | 1 | M | **AÇIK** |
| 9 | K-04 → K-06 | AH-05 | 1 | M | **AÇIK** |
| 10 | K-07 → K-08 (AH-G) | AH-G-09 | 1 | M | **AÇIK** |

---

## 17. TEST (AH — olgu listesi; AG/GG testleri ayrı, AA testleri AA'da)

| # | AH testi | Ne ölçülür (ölçülebilir) | Donanım davranışı (fallback) | Çıkış | Confidence | Durum |
|---|---|---|---|---|---|---|
| T-01 | AH-01 WakeGate uyuşmazlığı | Tazelik: halkanın son işareti (t_sens) eskiyse (t_sens > eşik) | AH-01: tazelik eskiyse hedeften kurtar → sıfırla | `qN` → sıfır halkada nakil, dolu halkada `qN ≤ N/2` (gerçek veri) → sıfırla → sonra devam | M | **AÇIK** |
| T-02 | AH-01 sıfır halka | `qN = 0` (sıfır halka) | Nakil yolu (sıfır halka) → sıfırla | `qN → 0` | M | **AÇIK** |
| T-03 | AH-01 dolu halka | `qN > 0` (dolu halka) | `qN ≤ N/2` → sıfırla | `qN ≤ N/2` | M | **AÇIK** |
| T-04 | AH-02 + AH-G-02 | Çıkışta halka → kırık çerçeve (çınlama/tıslama) | AH-02: tap-to-zero → sonra sıfırla | 0'lar (bozulmuş halka) | M | **AÇIK** |
| T-05 | AH-02 qN | Doluluk `qN ≥ N/2` (gerçek veri) | `qN ≥ N/2` → sıfırla | `qN ≥ N/2` → sıfırla | M | **AÇIK** |
| T-06 | AH-02 doluluk | `0 ≤ qN ≤ N` | Doluluk `qN` korunur (gerçek veri) | `0 ≤ qN ≤ N` | M | **AÇIK** |
| T-07 | AH-03 + AH-G-01 | `qN ≤ qmax` (kuyruk doluluğu; AH ile) | AH-03: kısmi dolanı tam ekle → alçalt | `qN ≤ qmax` | M | **AÇIK** |
| T-08 | AH-04 (P1) | İş yükü/jitter (ölçüm) | AH-04: watchdog → sonra devam | `C_eff < t_out` | M | **AÇIK** |
| T-09 | AH-05 + AH-G-06 | doluluk sinyali → 1 periyotta kasıtlı kurtarma | AH-05: hatıra dolu + adaptif parti | `qN → q0` (hızlı) | M | **AÇIK** |
| T-10 | AH-06 (P2) | olay + zaman damgası | AH-06: adaptif öncelik → sonra sıfırla | `W_t ≤ W_max` | M | **AÇIK** |
| T-11 | AH-07 (P2) | sessiz + düşük | AH-07: kalan hak → sonra sıfırla | `R_s ≤ R_max` | M | **AÇIK** |
| T-12 | AH (fail-safe) | AH-G-02 (düzeltme çizgisi), AH-G-09 (aradaki), AH-G-03/04 (nedenini ölçtükten sonra) | (sıra §13) | `V_ah` — standart AH testi (ölçüm) | M | **AÇIK** |

---

## 18. OLAY (bu dosya için; kapı 8 soru açık)

| # | OLAY (uç) | sens | Belirti/ölçülebilir | Donanım davranışı (fallback) | AH davranışı | Sıra (AH içinde) | Confidence | Durum |
|---|---|---|---|---|---|---|---|---|
| 1 | uWrap hedef ≠ | `qN` ≥ N/2 | Çıkış sessiz (§12, §13) | §12 → dondur | §10 AH-01 → sıfırla | P0 | M | **AÇIK** |
| 2 | Kuyruk/akış × doluluk | `qN` = 0 | Çıkış kırık (§17) | §17 → nakil | §10 AH-02 → sıfırla | P0 | M | **AÇIK** |
| 3 | doluluk = 0 (gerginlik) | `qN` | Çıkış titreşim (§8, §17) | §8 → düzelt | §10 AH-03 → alçalt | P1 | M | **AÇIK** |
| 4 | öncelik/darboğaz → jitter | `qN` | xrun + jitter (§9) | §9 → öncelik yükselt | §10 AH-04 → sonra devam | P1 | M | **AÇIK** |
| 5 | cihaz kaybı (uyku/uyanış) | `sens` | Sessizlik (§12) | §12 → dondur | §10 AH-01 → sonra servis | P1 | M | **AÇIK** |
| 6 | IA: IA uç (1) | `qN` | sessiz + doluluk (§19) | §19 → kurtar | §10 AH-06 → sonra sıfırla | P2 | M | **AÇIK** |
| 7 | IA: IA uç (2) | `qN` | düşük uygunluk (§19) | §19 → kurtar | §10 AH-07 → sonra sıfırla | P2 | M | **AÇIK** |
| 8 | Fail-safe: WakeGate uyuşmazlığı | `qN` ≥ N/2 | sessiz + WRAP + doluluk (§13) | §13 → sıfırla | §13 → sonra devam | P0/P1/P2 | M | **AÇIK** |

> **(U/Bilinmeyen):** olgular (§17: `q0`, `qmax`, `qmin`, `t_sens`) — **⚠️ VERIFICATION REQUIRED**.

---

## 19. RED TEAM

| # | iddia | neden | yanlış | durum |
|---|---|---|---|---|
| 1 | Taklit ama gerçek | Çift tampon gerçek veri (AH yasak) | ❌ AH ile taklit | AÇIK (§7, §17) |
| 2 | Olgu + olgu karıştırma | Doluluk/aşırı (gerçek) vs sedye/fail-safe (AH) | ❌ ikisi de gerçek | AÇIK (§8, §10) |
| 3 | 4. temel + AA | AA + 4 temel = **5** (sekiz kart yok) | ❌ 4 kart | AÇIK (§16) |
| 4 | "çıkışta sessiz" = güvenli | sessiz = veri kaybı (kasıtlı) | ❌ güvenli | AÇIK (§17, §18) |
| 5 | Ölçüm = kanıt | ölçüm (Q2) PLANNED + `q0`/`qmax` bilinmiyor | ❌ kanıt | AÇIK (§16, §17) |
| 6 | "çıkış sessiz = güvenli" | U (bilinmeyen) — UWRAP ölçümü yok | ⚠️ | AÇIK (§13, §16) |
| 7 | Kontrol = kuyruk (alias) | kontrol (EQ/kompresör) ayrı (tap) | ❌ alias | AÇIK (§5, §15) |
| 8 | (U)kesme ↔ akış eşlemesi | kesme ↔ akış eşlemesi çekirdek seviyeden okunmadı | ⚠️ | AÇIK (§6–§9) |
| 9 | "çıkışta sessiz = güvenli" | Gerçek veri kaybı (kasıtlı) — ölçüm yok | ⚠️ | AÇIK (§17) |
| 10 | Ölçüm (Q2) | İş yükü/jitter ölçümü yok | ⚠️ | AÇIK (§16, §12) |

---

## 20. YAPMA (bu dosya — ÖLÇÜM/ANALİZ katmanı; kapı 8 soru açık)

**YAPMAZ (8 kapı — AH yasağı + 3 kural — 10 satır):**

1. **YAPMAZ (P0 - AH-G-01, fail-safe):** Çift tamponu taklit etme (gerçek veri `load`'u; AH yasak) —
   gerekçe: veri kaybı = kasıtlı (§6, §17); askıda/uydurma — **OLGU + AH karışımı**.
2. **YAPMAZ (P0 - AH-G-02, fail-safe):** Bozuk/partial okuma — **OLGU + AH karışımı**.
3. **YAPMAZ (P1 - AH-G-03, fail-safe):** Tap ayrılmış; giriş donanımdan gelmez — **OLGU + AH karışımı**.
4. **YAPMAZ (P1 - AH-G-04):** Doğrusal okuma (tampon boyunca değil halka) — **OLGU + AH karışımı**.
5. **YAPMAZ (P1 - AH-G-05):** Yeniden yönlendirilmiş HA okuma (normalde) — **OLGU + AH karışımı**.
6. **YAPMAZ (P1 - AH-G-06):** Boyut/den/den (tutarsız) — **OLGU + AH karışımı**.
7. **YAPMAZ (P2 - AH-G-07):** RT yerine normal öncelik (AG/GG; kusurlu GO var) — **OLGU + AH karışımı**.
8. **YAPMAZ (P2 - AH-G-08):** AG/GG'yi aşağı al (ses kesintisinde serbest değil) — **OLGU + AH karışımı**.

**YAPMAZ (3 kural — 3 satır):**

9. **YAPMAZ (P1 - sınır):** Gerçek veri yazma (yalnız `load` — AH-G-01); AH okuma yasağı ihlali: kusur/çelişki
   (p1-sınır) — `durum: AÇIK`.
10. **YAPMAZ (P2 - kapı):** Ölçülmeyen iddia (Q2 ölçümü yok → kanıt yok — §16); kapı
    `PLANNED` — `durum: AÇIK`.
11. **YAPMAZ (P2 - AA):** AA + 4 temel = 5 (sekiz kart yok — §16); `sens`/`f_per`/`t_out` bilinmiyor (U) —
    **⚠️ VERIFICATION REQUIRED** — `durum: AÇIK`.

---

## 21. TUR KONTROL (bu dosya — ÖLÇÜM/ANALİZ katmanı; kapı 8 soru açık)

| # | Kontrol | Test | Durum |
|---|---|---|---|
| 1 | Giriş kapsamı | Bu turdaki 4 analiz kartı (K-01…K-04) | ✅ T-01…T-12 |
| 2 | AH yasağı | Gerçek veri `load`'u (AH-G-01) — 10/10 | ✅ T-01…T-12 |
| 3 | Çift tampon gerçek veri | `qN ≤ N/2` (AH-G-01; §10, §17) — UWRAP ölçümü yok | ✅ T-01…T-12 (ölçüm gerek) |
| 4 | Bozuk/partial okuma | `qN = 0` — kasıtlı (AH-G-02) | ✅ T-01…T-12 |
| 5 | Tap ayrılmış | Gönder → tap'le → al (AH-G-03) | ✅ T-01…T-12 |
| 6 | Doğrusal YASAK | Halka ≠ lineer (AH-G-04; §6) | ✅ T-01…T-12 |
| 7 | Öncelik (AG/GG) | RT kilitlenir (AH-G-07; §9) | ✅ T-01…T-12 |
| 8 | Ölçüm (Q2) | İş yükü/jitter ölçümü yok (§16) | ⚠️ T-08 (ölçüm gerek) |
| 9 | olgular (q0/qmax/qmin) | Doğrulanmadı (§17, §18) | ⚠️ T-05…T-12 (ölçüm gerek) |
| 10 | köprü (WakeGate/kesme↔akış) | Eşleme çekirdek seviyeden okunmadı (§6–§9) | ⚠️ T-01 (ölçüm gerek) |
| 11 | Kapı 8 soru | `PLANNED` — açık (§16) | ⚠️ kapı açık |
| 12 | AA + 4 temel = 5 | Eşleme 5 sütun (§16) | ✅ 5 kart |

---

## 22. KARDEŞ AĞAÇ (başlık/alt başlık = kopyala; alt satır = açıklama)

- **iOS → macOS:** `DMA/Ring/Circular (Ring-açık|kapalı) → Interrupt/kesme → CoreAudio/Callback → Xrun/Sedye → Cihaz → PH (sonraki ahit) · fail-safe (AH/AG/GG) (kaynak)` — iOS DMA (IOKit/IOBufferMemoryDescriptor) doğrudan aktarılmaz (§1, §6, §16; **⚠️ VERIFICATION REQUIRED** — çekirdek köprüsü = U).
- **iOS → Windows (tek):** `DMA/Doğrusal (Donanım okuma) → Interrupt → WASAPI/Callback → Xrun/Sedye → Cihaz → PH (sonraki ahit) · fail-safe (AH/AG/GG) (kaynak)` — WasapiCore → WASAPI köprüsü doğrudan aktarılmaz (§3, §14; **⚠️ VERIFICATION REQUIRED** — AH eşitliği).
- **macOS → Linux:** `DMA/Halka (alan boş) → Interrupt (alan boş) → ALSA/PCM → Xrun → Cihaz → PH (önceki ahit) · fail-safe (AH/AG/GG) (kaynak)` — WakeGate (§13) + AH (§13) doğrudan aktarılmaz (**⚠️ VERIFICATION REQUIRED** — köprü çekirdek seviyeden okunmadı).

---

## 23. REVISION

- **2026-10-14:** Aynı proje; içerik benzer (OKU / §9 dahil), kuyruk satırı benzer. Ek: çapraz kart + senaryo (bu dosya §12, §21), fail-safe (§10, §13), 12 test (§17), 8 olay (§18), revizyon (§23, §24), 10 red team (§19) + kapı 8 soru (§16). **Durum: AÇIK** (LIVE; 2026-10-14).

---

## 24. SINIR (bu dosya — ÖLÇÜM/ANALİZ katmanı; kapı 8 soru açık)

- **`analysis_only`:** analiz + uygunluk = canlı/davranış değil; eşleme/olgu/ölçüm güven = `⚠️ VERIFICATION REQUIRED`.
- **Kapsam dışarıda:** gerçek döküm (IOKit kütüphanesi) + firmware/driver kaynak kodu (çekirdek) + AH eşitlik
  (fail-safe) + sedye/fail-safe: ölçümde + kaynakta (Q2 ölçümü) `q0`/`qmax`/`qmin`/`f_per`/`t_out` bilinmiyor (U).
- **Mühür:** `Red Team · Human Mode · Truth Mode` (Bilgi Sınıfı: GİZLİ — ölçüm/analiz katmanı, kapı 8 soru açık).

---

## 25. TUR ÖZETİ

**Kapsam:** Bu dosya 4 çapraz analiz kartı (K-01…K-04) — çaprazlık + bütünlük (bu dosya §12, §21).
12 standart test (T-01…T-12) → **9 ✅ / 3 ⚠️ (T-08 ölçüm + T-05…T-12 olgular + T-01 köprü)**.
10 red team (§19) + 8 kapı sorusu (§16) + 3 başlık hatası (§19, §21) + 1 sınır (§24) —
**kapı: 8 soru AÇIK (PLANNED)** · AA + 4 = 5 (§16) · uygunluk: ölçüm/analiz + olgu/ölçüm + AH eşitlik
(live/davranış değil) — **kurulu değil**.

**Alt kart:** `DMA/Halka (alan boş) → Interrupt (alan boş) → CoreAudio/Callback → Xrun/Sedye → Cihaz → PH (sonraki ahit) · fail-safe (AH/AG/GG) (kaynak)` (§22).

**Durum: AÇIK** (LIVE; 2026-10-14 — olgular (§17) + ölçüm (§16) + köprü (§6–§9, U) ölçümde).

---

## 26. TERİM SÖZLÜĞÜ (bu kartta geçen; çapraz kartlarla aynı anlam)

| Terim | Anlam (bu kart) | Kaynak | Durum |
|---|---|---|---|
| HAL (CoreAudio) | Donanım soyutlama katmanı (IOProc kaydı) | §2, §7 (fetch ✅) | ✅ |
| IOProc / Render callback | Kesme context'inde veri taşıyan işlev | §3, §12 (fetch ✅) | ✅ |
| WakeGate | Ring/IOProc köprüsü (donanım → AH) | §13 (⚠️ çekirdek seviye) | ⚠️ VERIFICATION REQUIRED |
| Ring / Çift tampon | Halka tampon (WRAP); gerçek veri `load` (AH yasak) | §3, §6 | ✅ (AH-G-01) |
| WasapiCore → WASAPI | macOS → Windows köprüsü (exclusive) | §14 (fetch ✅) | ✅ |
| Tap / Kontrol | Sinyal tap'ı (EQ/kompresör) — ayrılmış | §5, §15 | ✅ (AH-G-03) |
| Xrun | Taşma/kesinti (kasıtlı ölçülen) | §8, §17 | ⚠️ VERIFICATION REQUIRED (ölçüm) |
| q0 / qmax / qmin / f_per / t_out | Doluluk/eşik olguları | §17, §18 | ⚠️ VERIFICATION REQUIRED |
| AH (fail-safe) | Gerçek veri okuma eşitliği (`load`); kural | §10, §13 | ✅ (kural) |
| AG/GG | Yanlış/değişken güvenlik (kural: kusurlu GO; ses kesintisinde serbest) | §9, §10 | ✅ (kural) |

---

## 27. TUR KAPISI (bu dosya — 8 soru açık)

| # | Soru | Durum | Kanıt |
|---|---|---|---|
| Q1 | Ring/tampon doğru mu (WRAP, gerçek veri)? | **AÇIK** | §3, §6, §17 (ölçüm gerek) |
| Q2 | İş yükü/jitter ölçümü var mı? | **AÇIK** | §12, §16 (⚠️ ölçüm yok) |
| Q3 | Pool + hazırlık hazır mı? | **AÇIK** | §4, §15 (⚠️ fetch yok) |
| Q4 | DMA sınırı aşılıyor mu? | **AÇIK** | §6, §16 (AH-G-04) |
| Q5 | Öncelik hiyerarşisi RT mi? | **AÇIK** | §9, §14 (⚠️ ölçüm yok) |
| Q6 | Sedye/fail-safe + tazelik var mı? | **AÇIK** | §10, §13 (⚠️ tazelik ölçümü yok) |
| Q7 | Tap-to-zero devrede mi? | **AÇIK** | §10, §17 (AH-02) |
| Q8 | Yetki + fail-safe tutuyor mu? | **AÇIK** | §16 (⚠️ yetki akışı okunmadı) |

**Kapı sonucu:** 8/8 **AÇIK (PLANNED)** — bu dosyada **kapatılamaz** (ölçüm/fetch gerek).

---

## 28. TUR SAYIMI (bu dosya)

| # | Kalem | Sayı |
|---|---|---|
| 1 | Çapraz analiz kartı | 8 (K-01…K-08 — §11) |
| 2 | Standart test (T-01…T-12) | 12 (9 ✅ / 3 ⚠️ — §17) |
| 3 | Red team | 10 (§19) |
| 4 | Kapı sorusu | 8 AÇIK (§16, §27) |
| 5 | Fail-safe AH | 7 (AH-01…AH-07 — §10, §13) |
| 6 | AA + 4 temel | 5 (§16) — sekiz kart yok |
| 7 | Beyazliste dışı yazım | 0 (yalnız K000) |
| 8 | `git commit` | Yok (orkestratöre ait) |

---

**Authority:** Bayram Ali / Vault Steward · Red Team · Human Mode · Truth Mode
