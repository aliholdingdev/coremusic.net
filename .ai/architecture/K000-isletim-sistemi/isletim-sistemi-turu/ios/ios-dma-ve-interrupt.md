---
title: "iOS DMA ve Kesme - CoreAudio Soyutlama Sınırı, IOKit Paylaşımı, Uygulama Yüzeyi"
type: architecture-deep-dive
node_id: ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/ios/ios-dma-ve-interrupt.md"
node_type: architecture
status: draft
date: 2026-10-14
parent_id: K000-isletim-sistemi/isletim-sistemi-turu/ios
siblings: [ios-anayasa, ios-kernel, ios-ses-stacki, ios-dma-ve-interrupt]
children: []
depends_on:
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/ios/ios-ses-stacki.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/macos/macos-dma-ve-interrupt.md"
evidence:
  - "developer.apple.com/documentation/driverkit/iodmacommand — IODMACommand (XNU/Darwin IOKit çekirdeği) · iOS da Darwin tabanlıdır (fetch: 2026-10-14)"
  - "developer.apple.com/documentation/driverkit/iobuffermemorydescriptor — IOBufferMemoryDescriptor (fetch: 2026-10-14)"
  - "developer.apple.com/library/archive/documentation/DeviceDrivers/Conceptual/IOKitFundamentals/DataMgmt/DataMgmt.html — prepare/complete akışı (fetch: 2026-10-14)"
validation: "TRUTH MODE: Darwin paylaşımlı IOKit API'si macOS kaynağından doğrulandı; iOS'ta ses yığınının (CoreAudio HAL → donanım) DMA yüzeyi AYRI olarak DOĞRULANMADI → ⚠️; iOS özel sürücü/AVAudioSession sınırları bu turda fetch EDİLMEDİ (⚠️); kod uydurma yasak (repo 0 .cpp/.h)"
open_questions:
  - "iOS CoreAudio HAL ↔ donanım DMA zinciri — ayrı iOS kaynak bu turda fetch EDİLMEDİ (⚠️ VERIFICATION REQUIRED)"
  - "iOS kesme (interrupt) yüzeyi ve zamanlayıcı → callback zinciri DOĞRULANMADI (⚠️ UNKNOWN)"
  - "Apple silicon iOS tarafında ses coprocessörü (AOP) DMA akışı — resmi belge YOK (⚠️ UNKNOWN)"
group: mimari
---

# ios-dma-ve-interrupt — iOS DMA ve Kesme Mimarisi (Teknoloji: iOS)

> **Eksen:** `K000-isletim-sistemi / isletim-sistemi-turu / ios` · **Grup:** `mimari` · **Durum:** `draft`
> **Amaç:** iOS'ta DMA'nın **görünmezleştirildiği** katman sınırını veriye dayalı çizmek: Darwin/IOKit
> çekirdek API'si (macOS ile paylaşılan) ile iOS uygulama katmanı arasındaki sınırı; neden
> iOS'ta DMA'ya dokunulmadığını — dokunulamayacağını — belgelemek.
> **Konu sırası:** (1) sınır → (2) Darwin paylaşımlı IOKit → (3) iOS katman ayrımı → (4) kesme
> yüzeyi → (5) ne bilinmiyor → (6) FAQ → (7) bağımlılık → (8) RED TEAM → (9) fetch günlüğü.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-14 · **Mod:** TRUTH MODE / RED TEAM

---

## 0. Bu Dosyanın Kapsamı (Boundary)

- ✅ **Yapılır:** Darwin/IOKit'in paylaşıldığı çekirdek DMA API'sinin macOS kaynaklı doğrulanması;
  iOS katman sınırının (uygulama → CoreAudio → sürücü → IOKit) veriye dayalı çizilmesi;
  "iOS'ta DMA gizlidir" iddiasının sınırı.
- ❌ **Yapılmaz:** iOS CoreAudio HAL iç DMA akışının doğrulanması (→ açık madde); AVAudioSession API
  derinliği; AOP/co-processor iç akışı; sürücü kodu (repo 0 `.cpp/.h`).

---

## 1. Darwin Paylaşımı — Neden macOS Kanıtı iOS İçin Geçerli (Kısmen)

| # | Öğe | İfade | Kaynak |
|---|---|---|---|
| 1 | Darwin/XNU | iOS, Darwin tabanlı çekirdek paylaşır (IOKit ailesi çekirdektedir) | genel bilgi · ⚠️ iOS'a özel doğrulama bu turda YOK |
| 2 | IODMACommand | `iokit/Kernel/IODMACommand.cpp` — Darwin kaynaklı | github.com/apple/darwin-xnu ✅ 2026-10-14 |
| 3 | prepare/complete | IOKit Fundamentals "Managing Data" akışı | developer.apple.com ✅ 2026-10-14 |
| 4 | iOS'ta erişim | Kullanıcı-alanı uygulama IOKit API'lerini **çağıramaz** (sürücü/çekirdek katmanı) | ⚠️ genel mimari — iOS özel sayfa bu turda fetch YOK |

> **Sınır notu:** macOS kanıtı, iOS **için** yalnızca çekirdek API'sinin *varlığını* destekler;
> iOS ses zincirinin (CoreAudio HAL → donanım) aynı akışı kullandığı **ayrıca** doğrulanmalıdır →
> `⚠️ VERIFICATION REQUIRED`.

---

## 2. iOS Katman Sınırı (Ne Görünür, Ne Gizli)

| Katman | DMA Görünür mü? | Kanıt |
|---|---|---|
| Uygulama (AVAudioEngine / AudioUnit / AVAudioSession) | ❌ Hayır — tampon + örnek kopyası | ⚠️ API yüzeyi genel okuma |
| CoreAudio HAL | ❌ Uygulamaya DMA açmaz | ⚠️ UNKNOWN — iOS özel fetch YOK |
| Ses sürücüsü (Apple dahili) | ✅ IOKit prepare/complete | macOS kaynaklı ✅ 2026-10-14 (paylaşım varsayımı ⚠️) |
| IOKit çekirdek | ✅ IODMACommand / IOMemoryDescriptor | darwin-xnu ✅ 2026-10-14 |
| Donanım | ✅ Fiziksel DMA | ⚠️ donanım özel |

**Sonuç:** iOS'ta DMA, uygulamanın **erişim alanı dışındadır**. Bu bir **özellik**tir (pil/istikrar/
güvenlik), eksiklik değil. CoreMusic gibi bir motor iOS'ta yalnızca tampon ve örnek kopyası
yüzeyinde çalışır — ASIO/WASAPI benzeri doğrudan donanım yolu **yoktur**.

---

## 3. Kesme Yüzeyi — Ne Biliniyor / Ne Bilinmiyor

| # | Öğe | Durum | Kaynak |
|---|---|---|---|
| 1 | Callback tetikleme mekanizması (timer vs donanım kesmesi) | iOS özelinde **doğrulanmadı** | ⚠️ UNKNOWN |
| 2 | AVAudioSession kesinti/rotasyon geri çağırı | API yüzeyi var; DMA ile ilişkisi **doğrulanmadı** | ⚠️ UNKNOWN |
| 3 | Darwin IOKit kesme aileleri | Çekirdekte mevcut (macos dosyasındaki durumla aynı) | ⚠️ bu turda API fetch YOK |
| 4 | AOP ses kesmesi/coprocessör | Resmi belge **YOK** | ⚠️ UNKNOWN |

---

## 4. macOS ile Kıyas (Ne Aynı, Ne Aynı Değil)

| Boyut | macOS | iOS | Ayırım |
|---|---|---|---|
| Çekirdek DMA API | IOKit IODMACommand ✅ 2026-10-14 | Darwin paylaşımı — iOS özel doğrulama ⚠️ | Çekirdek aynı aile · iOS kanıtı eksik |
| Uygulama → DMA sınırı | Kapalı (sürücü katmanı) | Kapalı (aynı prensip) | ✅ prensip aynı |
| Dış donanım/ses yolu | USB/Thunderbolt CoreAudio | Dahili + AVAudioSession yönetimi | 🔴 farklı |
| Kesme zinciri | ⚠️ (macos dosyası Q1) | ⚠️ (bu dosya Q1) | Her ikisi de AÇIK |

---

## 5. Açık Konular (Q listesi)

| # | Soru | Durum |
|---|---|---|
| Q1 | iOS CoreAudio HAL → donanım DMA zinciri? | ⚠️ VERIFICATION REQUIRED |
| Q2 | Callback tetikleme mekanizması (kesme vs timer)? | ⚠️ UNKNOWN |
| Q3 | AOP ses coprocessörü DMA akışı? | ⚠️ UNKNOWN |
| Q4 | iOS IOKit API kapsamının iOS sürümüne göre değişimi? | ⚠️ UNKNOWN |

---

## 6. Sıkça Sorulanlar (FAQ)

**S: iOS'ta DMA'ya kodumuzdan dokunabilir miyiz?**
H: Hayır. IOKit DMA API'leri sürücü/çekirdek katmanındadır; kullanıcı-alanı uygulaması erişemez.
Uygulamanın yüzeyi tampon + örnek kopyadır.

**S: macOS dosyasındaki kanıtlar iOS için de geçerli mi?**
H: **Kısmen.** Çekirdek API'nin varlığı evet; iOS ses zincirinin aynı akışı kullandığı ayrı
doğrulanmalıdır → `⚠️ VERIFICATION REQUIRED`.

**S: Peki CoreMusic iOS'ta nasıl ölçüm yapar?**
H: Yalnızca uygulama katmanı ölçümü (tampon süresi, callback aralığı) — DMA/kesme ölçümü bu
platformda **yapılamaz**. Ölçüm tasarımı buna göre sınırlandırılmalıdır.

---

## 7. Bağımlılıklar

| Bağımlılık | Dosya |
|---|---|
| macOS DMA (kanıt kaynağı) | `../macos/macos-dma-ve-interrupt.md` |
| iOS ses stacki | `ios-ses-stacki.md` |
| Kardeş kıyas | `../android/android-dma-ve-interrupt.md` (MMAP'ta tam ters durum) |

---

## 8. RED TEAM

| # | İddia | Sorgu | Sonuç |
|---|---|---|---|
| 1 | "iOS'ta uygulama DMA'ya dokunabilir" | Uygulama alanından IOKit çağrısı mümkün mü? | **RED** — hayır |
| 2 | "macOS kanıtı iOS için birebir geçerlidir" | iOS özel doğrulama var mı? | **RED** — yok → ⚠️ |
| 3 | "iOS kesmesi donanım kesmesidir" | Mekanizma doğrulandı mı? | **RED/UNKNOWN** — hayır |
| 4 | "DMA gizli olduğu için gecikme önemsizdir" | Gecikme DMA'dan bağımsız mı? | **RED** — DMA/uyanış alt sınırı her platformda vardır (K000/linux §1) |

---

## 9. Fetch Günlüğü

| Tarih | Kaynak | Durum |
|---|---|---|
| 2026-10-14 | developer.apple.com/documentation/driverkit/iodmacommand | ✅ fetch |
| 2026-10-14 | developer.apple.com/documentation/driverkit/iobuffermemorydescriptor | ✅ fetch |
| 2026-10-14 | developer.apple.com/library/archive/.../DataMgmt.html | ✅ fetch |
| 2026-10-14 | iOS CoreAudio HAL DMA iç akışı | ❌ fetch yok → ⚠️ |
| 2026-10-14 | iOS kesme/callback mekanizması | ❌ fetch yok → ⚠️ |

---

**Authority:** Bayram Ali / Vault Steward · Red Team · Human Mode · Truth Mode
