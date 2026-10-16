---
title: "ReactOS DMA ve Kesme - HalAllocateCommonBuffer, Scatter-Gather, HAL Dispatch, Windows Uyumluluk Sınırı"
type: architecture-deep-dive
node_id: ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/reactos/reactos-dma-ve-interrupt.md"
node_type: architecture
status: draft
date: 2026-10-14
parent_id: K000-isletim-sistemi/isletim-sistemi-turu/reactos
siblings: [reactos-anayasa, reactos-kernel, reactos-ses-stacki, reactos-dma-ve-interrupt]
children: []
depends_on:
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/reactos/reactos-kernel.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/windows/windows-dma-ve-interrupt.md"
evidence:
  - "doxygen.reactos.org/d6/dbe/hal_2halx86_2generic_2dma_8c.html — HalAllocateCommonBuffer kaynağı (satır 907) · hal/halx86/generic/dma.c (fetch: 2026-10-14)"
  - "github.com/reactos/reactos/blob/2f15e2f743e2764ad2a158b4ddcb00a0839ce6f6/hal/hal.spec — HAL export listesi (DMA fonksiyon exportları) (fetch: 2026-10-14)"
validation: "TRUTH MODE: iddia → resmi ReactOS URL + fetch tarihi; Windows davranışıyla birebir aynılık IDDİASI yapılmaz (⚠️ sadece export/uyumluluk düzeyi); ses sürücüsü + DMA zinciri ReactOS'ta DOĞRULANMADI (⚠️); kod uydurma yasak (repo 0 .cpp/.h)"
open_questions:
  - "ReactOS ses yığınında (WAVE/DSOUND) gerçek bir WaveRT DMA zinciri uçtan uca DOĞRULANMADI (⚠️ VERIFICATION REQUIRED)"
  - "hal.spec'teki DMA export'larının hangilerinin tam implemente olduğu (stub vs gerçek) — satır düzeyi doğrulama bu turda yapılmadı (⚠️)"
  - "ReactOS'ta MSI/IRQL davranışı — Windows ile benzerlik düzeyi gerçek donanımda ölçülmadı (⚠️ UNKNOWN)"
group: mimari
---

# reactos-dma-ve-interrupt — ReactOS DMA ve Kesme Mimarisi (Teknoloji: ReactOS)

> **Eksen:** `K000-isletim-sistemi / isletim-sistemi-turu / reactos` · **Grup:** `mimari` · **Durum:** `draft`
> **Amaç:** ReactOS'un HAL DMA katmanını (HalAllocateCommonBuffer, scatter-gather, hal.spec
> exportları) resmi kaynaklarla belgelemek; Windows DMA zinciriyle **uyumluluk düzeyini** — ve
> sınırını — dürüstçe ayırmak (birebirlik iddiası YOK).
> **Konu sırası:** (1) sınır → (2) HAL DMA API'si → (3) scatter-gather → (4) dispatch/export →
> (5) kesme yüzeyi → (6) Windows kıyası → (7) ses akışına bağ → (8) açık konular → (9) FAQ →
> (10) bağımlılık → (11) RED TEAM → (12) fetch günlüğü.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-14 · **Mod:** TRUTH MODE / RED TEAM

---

## 0. Bu Dosyanın Kapsamı (Boundary)

- ✅ **Yapılır:** HAL DMA API'sinin resmi kaynak/export kanıtları; scatter-gather kavramı; Windows
  ile **uyumluluk sınırı**nın dürüst ayrımı.
- ❌ **Yapılmaz:** "ReactOS Windows ile aynıdır" iddiası; WaveRT DMA zincirinin uçtan uca
  doğrulanması (→ açık madde); sürücü kodu (repo 0 `.cpp/.h`); gerçek donanım ölçümü.

---

## 1. HAL DMA API'si (Resmi Kaynak Kanıtı)

| # | Fonksiyon | Kanıt | Kaynak |
|---|---|---|---|
| 1 | `HalAllocateCommonBuffer` | `hal/halx86/generic/dma.c` — **satır 907** | doxygen.reactos.org ✅ 2026-10-14 |
| 2 | `HalFreeCommonBuffer` | aynı dosya ailesi (DMA common buffer yaşam döngüsü) | doxygen.reactos.org ✅ 2026-10-14 (dosya sayfası) |
| 3 | Scatter-gather listesi kurma | `HalBuildScatterGatherList` / `HalGetScatterGatherList` / `HalPutScatterGatherList` | doxygen.reactos.org ✅ 2026-10-14 (dosya/alt sistem) |
| 4 | Export listesi | `hal/hal.spec` — DMA fonksiyonları DLL export olarak listeli | github.com/reactos ✅ 2026-10-14 (commit `2f15e2f…`) |

**Anlamı:** Common buffer, DMA'nın fiziksel olarak erişebildiği (wired) bellektir; sürücü bunu
`HalAllocateCommonBuffer` ile alır, fiziksel adresle donanıma teslim eder, iş bitince serbest bırakır.
ReactOS bunu **kendi HAL kodunda** (`hal/halx86/generic/dma.c`) implemente eder.

---

## 2. Scatter-Gather (Dağınık-Birleştirici) DMA

| # | Kavram | İşlev | Kanıt |
|---|---|---|---|
| 1 | Scatter-gather listesi | Sürekli olmayan bellek parçalarını tek DMA transferinde birleştirir | doxygen.reactos.org ✅ 2026-10-14 |
| 2 | `HalBuildScatterGatherList` | MDL/tabandan S-G listesi kurar | doxygen.reactos.org ✅ 2026-10-14 |
| 3 | `HalGetScatterGatherList` | Listeyi alır (kullanım sayacı ile) | doxygen.reactos.org ✅ 2026-10-14 |
| 4 | `HalPutScatterGatherList` | Listeyi bırakır | doxygen.reactos.org ✅ 2026-10-14 |
| 5 | Ses açısından anlamı | Tampon fiziksel sürekli değilse (ring/çok-parça) sürücü S-G ile okur/yazar | yorum — genel DMA kavramı |

---

## 3. HAL Dispatch ve Export Yüzeyi

| # | Öğe | Kanıt | Kaynak |
|---|---|---|---|
| 1 | `hal.spec` | HAL DLL'inin export listesi — DMA fonksiyonları burada | github.com/reactos ✅ 2026-10-14 |
| 2 | HalDispatchTable | Çekirdek→HAL yönlendirme tablosu (NT uyumluluk yolu) | ⚠️ VERIFICATION REQUIRED — bu turda satır düzeyi fetch YOK |
| 3 | Stub vs tam implementasyon | Hangi export tam/eksik — **satır düzeyi doğrulanmadı** | ⚠️ UNKNOWN |

> **Dürüstlük notu:** Export listesi bir **API sözleşmesidir**; "bu fonksiyon her senaryoda Windows
> gibi davranır" **anlamına gelmez**. ReactOS projesinin kendisi uyumluluk projedir — tam kapalı
> döngü (donanım+test) hedefe yakındır, **olgunluğa erişti kanıtlanmamıştır**.

---

## 4. Kesme Yüzeyi — Ne Biliniyor / Ne Bilinmiyor

| # | Öğe | Durum | Kaynak |
|---|---|---|---|
| 1 | HAL üzerinden kesme yönlendirme (routing) fonksiyon ailesi | Windows tasarımıyla uyumlu HAL katmanı mevcut | ⚠️ bu turda fetch YOK → VERIFICATION REQUIRED |
| 2 | ISR/DPC zincirinin ses kontrolörü ile uçtan uca akışı | ReactOS ses yığınında **doğrulanmadı** | ⚠️ UNKNOWN |
| 3 | MSI/IRQL davranışı | Gerçek donanımda ölçülmedi | ⚠️ UNKNOWN |

---

## 5. Windows DMA ile Uyumluluk Sınırı (Kıyas)

| Boyut | Windows (K000/windows) | ReactOS (bu dosya) | Ayırım |
|---|---|---|---|
| DMA API adı | `HalAllocateCommonBuffer` vb. (WDK design guide) | Aynı adlar — hal.spec export | ✅ API **adı** uyumlu |
| Kaynak | learn.microsoft.com kernel/ ✅ 2026-10-12 | doxygen.reactos.org ✅ 2026-10-14 | İkisi ayrı kaynak |
| WaveRT DMA → event | Belgelendi (low-latency-audio) ✅ 2026-10-12 | **Belgelenmedi** | ⚠️ ReactOS tarafı YOK |
| Scatter-gather | DMA Library / Adapter Objects design guide ✅ | S-G fonksiyonları kaynakta ✅ | API uyumlu · davranış ⚠️ |
| Olgunluk | Üretim (WDK) | Uyumluluk projesi (geliştirme) | 🔴 Karşılaştırılabilir DEĞİL |

**Sonuç:** ReactOS, Windows **API sözleşmesini** paylaşır (aynı adlar, aynı spec biçiminde export);
**davranış eşitliği** ve **ses zinciri olgunluğu** bu turda kanıtlanmamıştır → `⚠️`.

---

## 6. Ses Akışına Bağlantı (Katman Sınırı)

| Katman | DMA'ya Dokunur mu? | Kanıt |
|---|---|---|
| Uygulama (WASAPI eşdeğeri) | ❌ | ⚠️ ReactOS ses API'si yüzeyi bu turda doğrulanmadı |
| Ses yığını (WAVE/DSOUND/DirectMusic) | ⚠️ Uygulamada var; DMA zinciri **doğrulanmadı** | ⚠️ UNKNOWN |
| Sürücü (WDM/WaveRT eşdeğeri) | ✅ Olması gereken katman (Windows modeli ile aynı) | ⚠️ uçtan uca YOK |
| HAL (`hal/halx86/generic/dma.c`) | ✅ Common buffer + S-G | doxygen ✅ 2026-10-14 |
| Donanım | ✅ Fiziksel DMA | ⚠️ gerçek donanım testi yok |

---

## 7. Açık Konular (Q listesi)

| # | Soru | Durum |
|---|---|---|
| Q1 | WaveRT DMA → event zinciri ReactOS'ta çalışıyor mu? | ⚠️ VERIFICATION REQUIRED |
| Q2 | `hal.spec` DMA export'larının tam/stub durumu? | ⚠️ UNKNOWN (satır düzeyi yok) |
| Q3 | ISR/DPC + ses kontrolörü uçtan uca? | ⚠️ UNKNOWN |
| Q4 | MSI/IRQL gerçek donanım davranışı? | ⚠️ UNKNOWN |

---

## 8. Sıkça Sorulanlar (FAQ)

**S: ReactOS ses sürücüleri Windows sürücüleriyle aynı mı?**
H: API sözleşmesi (aynı adlar) uyumludur; **davranış eşitliği** bu dosyada kanıtlanmamıştır.

**S: `HalAllocateCommonBuffer` ne yapar?**
H: DMA'nın fiziksel erişebildiği common buffer ayırır (kaynak: `hal/halx86/generic/dma.c:907` ✅).

**S: CoreMusic ReactOS'u hedefleyebilir mi?**
H: K000 kapsamı araştırma katmanıdır; ReactOS'un ses zinciri olgunluğu `⚠️` işaretli olduğu için
hedef olarak değerlendirilemez — önce Q1 kapanmalıdır.

---

## 9. Bağımlılıklar

| Bağımlılık | Dosya |
|---|---|
| Windows DMA (kıyas kaynağı) | `../windows/windows-dma-ve-interrupt.md` |
| ReactOS kernel | `reactos-kernel.md` |
| ReactOS ses stacki | `reactos-ses-stacki.md` |
| Alt çekirdek DMA kavramı | `../linux/linux-dma-ve-interrupt.md` |

---

## 10. RED TEAM

| # | İddia | Sorgu | Sonuç |
|---|---|---|---|
| 1 | "ReactOS DMA Windows ile birebir aynı" | Davranış doğrulaması var mı? | **RED** — yalnız API adı/export kanıtlı |
| 2 | "hal.spec'teki her fonksiyon implemente" | Stub olabilir mi? | **RED/UNKNOWN** — satır düzeyi yok |
| 3 | "ReactOS'ta WaveRT DMA event zinciri çalışır" | Uçtan uca kanıt? | **RED** — yok → ⚠️ |
| 4 | "Common buffer her zaman sürekli fiziksel" | S-G varsa neden var? | **RED** — sürekli değilse S-G kullanılır |

---

## 11. Fetch Günlüğü

| Tarih | Kaynak | Durum |
|---|---|---|
| 2026-10-14 | doxygen.reactos.org/d6/dbe/hal_2halx86_2generic_2dma_8c.html | ✅ fetch |
| 2026-10-14 | github.com/reactos/reactos/blob/…/hal/hal.spec | ✅ fetch |
| 2026-10-14 | ReactOS ses yığını DMA zinciri (uçtan uca) | ❌ fetch yok → ⚠️ |
| 2026-10-14 | HalDispatchTable satır düzeyi | ❌ fetch yok → ⚠️ |

---

**Authority:** Bayram Ali / Vault Steward · Red Team · Human Mode · Truth Mode
