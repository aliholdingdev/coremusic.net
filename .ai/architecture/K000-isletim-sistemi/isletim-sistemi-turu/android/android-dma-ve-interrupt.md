---
title: "Android DMA ve Kesme - AAudio MMAP/NOIRQ, dmabuf, Fast Mixer SCHED_FIFO, MmapStreamInterface"
type: architecture-deep-dive
node_id: ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/android/android-dma-ve-interrupt.md"
node_type: architecture
status: draft
date: 2026-10-14
parent_id: K000-isletim-sistemi/isletim-sistemi-turu/android
siblings: [android-anayasa, android-kernel, android-ses-stacki, android-dma-ve-interrupt]
children: []
depends_on:
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/android/android-ses-stacki.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/linux/linux-dma-ve-interrupt.md"
evidence:
  - "source.android.com/docs/core/audio/aaudio — AAudio MMAP: EXCLUSIVE'te uygulamanın ALSA sürücüsüyle paylaşılan mmap buffer'a doğrudan yazar; NOIRQ modu; 'anon_inode:dmabuf'; 'AUDIO_MMAP_APPLICATION_SHAREABLE' (fetch: 2026-10-14)"
  - "source.android.com/docs/core/audio/latency/design — Fast Mixer SCHED_FIFO, 2–5 ms periyot; düşük gecikme tasarımı (fetch: 2026-10-14)"
  - "android.googlesource.com/platform/frameworks/av/+/master/include/media/MmapStreamInterface.h — createMmapBuffer / getMmapPosition arayüzü (fetch: 2026-10-14)"
validation: "TRUTH MODE: iddia → resmi URL + fetch tarihi; SoC'ye özel ALSA/ASoC sürücüsü DMA kanal ve IRQ detayları cihaza özgüdür → bu turda DOĞRULANMADI (⚠️); kod uydurma yasak (repo 0 .cpp/.h)"
open_questions:
  - "SoC'ye özel ASoC/ALSA sürücüsünde DMA kanalı ve IRQ numarası — cihaza özgü, genel belge YOK (⚠️ UNKNOWN)"
  - "NOIRQ modunda uyanışın kesme yerine ne ile tetiklendiğinin tam mekanizması (event/poll yüzeyi) — resmi belgede kaba anlatım var, ince mekanizma DOĞRULANMADI (⚠️)"
  - "dmabuf import/export akışının uygulama–sürücü arası tam protokolü (fd paylaşımı) — bu turda derin fetch YOK (⚠️)"
group: mimari
---

# android-dma-ve-interrupt — Android DMA ve Kesme Mimarisi (Teknoloji: Android)

> **Eksen:** `K000-isletim-sistemi / isletim-sistemi-turu / android` · **Grup:** `mimari` · **Durum:** `draft`
> **Amaç:** Android'de düşük gecikmeli ses yolunun DMA yüzeyini (AAudio MMAP/NOIRQ, dmabuf) ve
> kesme yerine geçen uyanış modelini resmi Android kaynaklarıyla belgelemek; Linux çekirdek
> DMA/IRQ katmanına (K000/linux) olan bağımlılığı işaretlemek.
> **Konu sırası:** (1) sınır → (2) standart yol vs MMAP → (3) MMAP DMA → (4) NOIRQ → (5) Fast Mixer
> → (6) kesme yüzeyi → (7) katman sınırı → (8) açık konular → (9) FAQ → (10) bağımlılık →
> (11) RED TEAM → (12) fetch günlüğü.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-14 · **Mod:** TRUTH MODE / RED TEAM

---

## 0. Bu Dosyanın Kapsamı (Boundary)

- ✅ **Yapılır:** AAudio MMAP/NOIRQ ve dmabuf'un resmi Android tanımı; Fast Mixer zamanlama
  modeli; Linux çekirdek DMA/IRQ katmanına referans.
- ❌ **Yapılmaz:** SoC'ye özel ASoC sürücüsü register/DMA kanal detayı (→ açık madde), JNI/HAL kodu
  (repo 0 `.cpp/.h`), OEM cihaz ölçümü, AAudio API kullanım rehberi derinliği.

---

## 1. İki Ses Yolu (Resmi Karşılaştırma)

| # | Yol | DMA'ya Yaklaşım | Kaynak |
|---|---|---|---|
| 1 | Standart yol (AudioTrack → AudioFlinger → HAL) | Tamponlar AudioFlinger'da kopyalanır; DMA'ya HAL/sürücü katmanında dokunulur | source.android.com ✅ 2026-10-14 |
| 2 | AAudio MMAP (EXCLUSIVE) | Uygulama, ALSA sürücüsüyle **paylaşılan mmap buffer'a doğrudan yazar** — kopya yok | source.android.com ✅ 2026-10-14 |
| 3 | AAudio MMAP (SHAREABLE) | `AUDIO_MMAP_APPLICATION_SHAREABLE` — tampon paylaşılabilir | source.android.com ✅ 2026-10-14 |
| 4 | Exclusive olmayan (MIX?) | Standart yola düşer — MMAP yok | source.android.com ✅ 2026-10-14 (AAudio sayfası) |

---

## 2. MMAP DMA Yüzeyi (Resmi Tanımlar)

| # | Öğe | Resmi İfade | Kaynak |
|---|---|---|---|
| 1 | mmap buffer | Uygulama ile ALSA sürücüsü arasında paylaşılan bellek; doğrudan yazma | source.android.com ✅ 2026-10-14 |
| 2 | dmabuf | `anon_inode:dmabuf` — buffer Linux dmabuf üzerinden paylaşılır | source.android.com ✅ 2026-10-14 |
| 3 | AUDIO_MMAP_APPLICATION_SHAREABLE | Paylaşılabilir MMAP tamponu seçeneği | source.android.com ✅ 2026-10-14 |
| 4 | MmapStreamInterface | HAL arayüzü: `createMmapBuffer` / `getMmapPosition` | android.googlesource.com ✅ 2026-10-14 |
| 5 | konum (position) | Uygulama, DMA ilerleyişini `getMmapPosition` ile okur (kopyasız) | android.googlesource.com ✅ 2026-10-14 |

**Sonuç:** Android MMAP'ta DMA tamponu, uygulamanın **bellek haritasındadır**; uygulama DMA verisine
`mmap` üzerinden, sürücü `dmabuf` üzerinden **aynı fiziksel belleğe** erişir. Kopya adımı yoktur —
gecikmenin bir kısmını budayan tasarım budur.

---

## 3. NOIRQ — Kesme Yerine Ne Var?

| # | Kavram | Resmi İfade | Kaynak |
|---|---|---|---|
| 1 | NOIRQ | DMA tamponu kesme yerine **paylaşılan konum (position) okunarak** izlenir | source.android.com ✅ 2026-10-14 |
| 2 | Amaç | Kesme trafiğini azaltıp uyanış gecikmesini düşürmek | source.android.com ✅ 2026-10-14 (latency/design) |
| 3 | Konum okuma | `getMmapPosition` ile uygulama periyodu takip eder | android.googlesource.com ✅ 2026-10-14 |
| 4 | Uyanış mekanizması (ince) | Resmi belgede tam mekanizma (event/poll) **ayrıntılanmamış** | ⚠️ VERIFICATION REQUIRED |

> **Dürüstlük notu:** "NOIRQ = kesme yok" **iddiası dikkatli kullanılmalıdır**: resmi belge NOIRQ'un
> kesme bağımlılığını azalttığını söyler; alttaki ALSA/ALSA-timer/IRQ katmanının tamamen kesmesiz
> olduğu **bu turda kanıtlanmadı** → `⚠️ VERIFICATION REQUIRED`.

---

## 4. Fast Mixer — Zamanlama Modeli (Ses Gerçek Zamanlı Kısıtı)

| # | Öğe | Resmi İfade | Kaynak |
|---|---|---|---|
| 1 | Fast Mixer önceliği | `SCHED_FIFO` (gerçek zamanlı kuyruk) | source.android.com ✅ 2026-10-14 |
| 2 | Periyot | 2–5 ms | source.android.com ✅ 2026-10-14 |
| 3 | Amaç | Gecikmeyi düşürmek (low-latency design) | source.android.com ✅ 2026-10-14 |
| 4 | Kapsam | Uygulama ↔ Fast Mixer arası "fast path" — DMA bu yolun **altındadır** | source.android.com ✅ 2026-10-14 |

---

## 5. Kesme Yüzeyi — Ne Biliniyor / Ne Bilinmiyor

| # | Öğe | Durum | Kaynak |
|---|---|---|---|
| 1 | Altta Linux çekirdeği IRQ/DMAEngine katmanı vardır | Linux K000 dosyasında belgelendi | `../linux/linux-dma-ve-interrupt.md` ✅ (dahili) |
| 2 | SoC'ye özel ASoC sürücüsünün DMA kanalı / IRQ | Cihaza özgü — genel belge **YOK** | ⚠️ UNKNOWN |
| 3 | MMAP + NOIRQ'ta kesme tamamen devre dışı mı? | Resmi belge "azaltır" der; "tamamen yok" **kanıtlanmadı** | ⚠️ VERIFICATION REQUIRED |
| 4 | dmabuf fd paylaşım protokolü (uygulama↔HAL↔sürücü) | Derin fetch bu turda yapılmadı | ⚠️ UNKNOWN |

---

## 6. Katman Sınırı

| Katman | DMA'ya Dokunur mu? | Kanıt |
|---|---|---|
| Uygulama (AAudio / AudioTrack) | ⚠️ MMAP'ta **paylaşılan tampona doğrudan yazar** (fiziksel DMA belleği) | source.android.com ✅ 2026-10-14 |
| AudioFlinger / Fast Mixer | ✅ Fast path — `SCHED_FIFO`, 2–5 ms | source.android.com ✅ 2026-10-14 |
| HAL (MmapStreamInterface) | ✅ `createMmapBuffer` / `getMmapPosition` | android.googlesource.com ✅ 2026-10-14 |
| ALSA / ASoC sürücüsü (Linux çekirdek) | ✅ DMA kanalı + IRQ | ⚠️ SoC'ye özel detay UNKNOWN |
| Donanım (ses DSP/codec) | ✅ Fiziksel DMA | ⚠️ SoC'ye özel |

**Fark (macOS/Windows'a kıyasla):** Android MMAP, uygulamayı **fiziksel DMA tamponuyla** yüzleştirir;
macOS'ta uygulama bunu göremez. Bu yüzden Android'de "tampon hizası/boyutu" bir **uygulama
kararıdır** — CoreMusic ölçüm tasarımı (512 örnek) için doğrudan etkilidir.

---

## 7. Açık Konular (Q listesi)

| # | Soru | Durum |
|---|---|---|
| Q1 | SoC'ye özel DMA kanal/IRQ numaraları? | ⚠️ UNKNOWN (cihaza özgü) |
| Q2 | NOIRQ'ta uyanış mekanizmasının ince mekanizması? | ⚠️ VERIFICATION REQUIRED |
| Q3 | dmabuf fd protokolünün tam şeması? | ⚠️ UNKNOWN |
| Q4 | 2–5 ms periyodun CoreMusic 512/48k (≈10.7 ms) ile ilişkisi? | Yorum — sayısal kıyas ölçüm dosyasına ait |

---

## 8. Sıkça Sorulanlar (FAQ)

**S: Android'de uygulama DMA'ya dokunabilir mi?**
H: MMAP/EXCLUSIVE'te uygulama, sürücüyle paylaşılan mmap tamponuna doğrudan yazar — yani DMA
belleğinin **bir görünümüne** dokunur. Standart yolda dokunmaz (kopya AudioFlinger'dadır).

**S: NOIRQ = kesme yok mu?**
H: Hayır denemez. Resmi belge, kesme bağımlılığını azalttığını söyler; alt katmanın tamamen kesmesiz
olduğu kanıtlanmadı → `⚠️ VERIFICATION REQUIRED`.

**S: Fast Mixer ile MMAP aynı şey mi?**
H: Değildir. Fast Mixer AudioFlinger içindeki hızlı karıştırıcı yolu (`SCHED_FIFO`, 2–5 ms);
MMAP ise uygulama–sürücü arası kopyasız tampon yoludur.

---

## 9. Bağımlılıklar

| Bağımlılık | Dosya |
|---|---|
| Alt çekirdek DMA/IRQ | `../linux/linux-dma-ve-interrupt.md` |
| Android ses stacki | `android-ses-stacki.md` |
| Kardeş kıyas | `../windows/windows-dma-ve-interrupt.md` · `../macos/macos-dma-ve-interrupt.md` |

---

## 10. RED TEAM

| # | İddia | Sorgu | Sonuç |
|---|---|---|---|
| 1 | "Android'de DMA tamamen gizlidir" | MMAP buffer paylaşılan fiziksel DMA belleği mi? | **RED** — MMAP'ta görünür/kopyasız |
| 2 | "NOIRQ kesmeyi tamamen kaldırır" | Resmi belge "tamamen yok" diyor mu? | **RED/UNKNOWN** — "azaltır" der; kanıt yok |
| 3 | "Fast Mixer DMA'yı yönetir" | DMA hangi katmanda? | **RED** — DMA altta ALSA/ASoC; Fast Mixer karıştırma/kuyruk katmanıdır |
| 4 | "Her Android cihazda MMAP EXCLUSIVE vardır" | Zorunlu mu? | **RED** — donanım/sürücüye bağlıdır |

---

## 11. Fetch Günlüğü

| Tarih | Kaynak | Durum |
|---|---|---|
| 2026-10-14 | source.android.com/docs/core/audio/aaudio | ✅ fetch |
| 2026-10-14 | source.android.com/docs/core/audio/latency/design | ✅ fetch |
| 2026-10-14 | android.googlesource.com/.../MmapStreamInterface.h | ✅ fetch |
| 2026-10-14 | SoC'ye özel ASoC sürücü detayı | ❌ fetch yok → ⚠️ |
| 2026-10-14 | dmabuf protokol derinlik sayfası | ❌ fetch yok → ⚠️ |

---

**Authority:** Bayram Ali / Vault Steward · Red Team · Human Mode · Truth Mode
