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

**Authority:** Bayram Ali / Vault Steward · Red Team · Human Mode · Truth Mode
