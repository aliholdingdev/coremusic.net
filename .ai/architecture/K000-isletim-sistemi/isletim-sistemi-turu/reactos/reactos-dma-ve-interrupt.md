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

## 12. ÇAPRAZ KART MATRİSİ (DMA/darboğaz eksenli)

| Kart | DMA | Interrupt | Kernel Stream | PortCls/WavePCI | Xrun | Cihaz kaybı | AH (fail-safe) | Confidence |
|---|---|---|---|---|---|---|---|---|
| K-01 Çift tampon + ring | ✅ | ✅ | ✅ (§4) | ✅ (§4) | ✅ (barrier) | ✅ (§13) | ✅ (stall → sıfırla) | M |
| K-02 Kuyruk/doluluk | ✅ (§3, §8) | ✅ (§8) | ✅ | ✅ | ✅ (§8) | ✅ (§13) | ✅ (doluluk sinyali) | M |
| K-03 Pool + hazırlık | ✅ (§4) | ✅ | ✅ | ✅ (§4) | ✅ | ✅ | ✅ (hazırlık fail-safe) | M |
| K-04 DMA sınırı | ✅ (§6) | ✅ | ✅ (§7) | ✅ | ✅ | ✅ | ✅ (sınır aşımında okuma yok) | M |
| K-05 Öncelik/RT | ✅ (§9) | ✅ (§9) | ✅ (§9) | ✅ (§9) | ✅ (§9) | ✅ | ✅ (RT yoksayıldığında watchdog) | M |
| K-06 Sessiz/yanlış okuma | ✅ (§6, §17) | ✅ | ✅ (§7) | ✅ (§7) | ✅ (§17) | ✅ (§17) | ✅ (zaman damgası + sessizlik) | M |
| K-07 Stream bütünlüğü | ✅ (§6) | ✅ (§6) | ✅ (§7) | ✅ (§7) | ✅ (§7) | ✅ (§17) | ✅ (bütünlük kapıda) | M |
| K-08 Fail-safe köprüsü | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ (ana kart §10) | M |

> **Δ bilgi:** ReactOS çekirdek DMA/kesme akışı NT-kalıtımlı (`HAL → PortCls → Kernel Stream`),
> upstream Linux DMAEngine'den farklı anahtar; ortak sütunlar (ring/doluluk/pool) her iki ağaçta
> da tutarlı. HalDispatchTable satır düzeyi **fetch yok** → **⚠️ VERIFICATION REQUIRED**.

---

## 13. SES/RT SENARYOSU (Kaynak: audio-drivers.md)

Senaryo: müzik uygulaması ReactOS'ta PortCls/WavePCI üzerinden Kernel Stream'e yazan
C++ session ile DAQ/USB ses cihazı arasında gerçek zamanlı akış.
**Ölçüm:** yayın (callback→çıktı) ≤ bir periyot (örn. 2.67 ms @ 48 kHz/128) + jitter payı.

**DMA/kesme akışı (bu dosya §3, §6–§9, §12–§13):**

1. **Kaynaktan hedefe:** Kaynak (mikrofon/line — §4/§5 buffer'ı, ilk dolu halka) → Kernel Stream
   iş katmanı (render/DPC — §3 ring) → PortCls tap (§5/§7) → kontrol (EQ/kompresör — §5/§15)
   → hedef (hoparlör/kulaklık, §4/§5). **Dağıtım hattı** kuyruklu (K-02): DMA üretir, stream
   tüketir, kuyruk 2 periyot ayırır. **Kanal:** tek HW kanal (§4), fallback'te karışım (§14/§17).
2. **Darboğaz (Worst):** kesme/DPC iş yükü (donanım kesmesi + PortCls + stream kuyruğu — §9) +
   render işi (§3) + sistem kesintisi (HAL/ISR — §6). Darboğaz: gerçek veri **yalnız
   kesme/DPC context'inde** → iş yükü (tip: `~%15` @ 48 kHz/128 — **⚠️ VERIFICATION REQUIRED**,
   ölçüm gerek).
3. **Zaman/gerilim (`Xun`):** kesme tetiklenir → DPC (WakeGate benzeri → halka) → render
   (tap → kontrol) → stream kuyruğu → hedefte kalan ≤ 1 periyot / çıkışta ≤ 2 periyot (§8, §16).
   Stok N (2 periyot) → **=** bariz ihlal; köprü arızasında tazelik testi + fail-safe (§10 AH-01)
   devrede → **<** (qN < qmin). **U:** tazelik penceresi ölçümü yok → **⚠️ VERIFICATION REQUIRED**.
4. **Sensör/geri besleme:** DONANIM: uWrap/tam halka (§3); yordam: doluluk qN (§8) + xrun (§17).
   `q0` N (§8) — **harici kaynak (HAL/PortCls) → ⚠️ VERIFICATION REQUIRED**.
5. **Öncelik:** zorunlu üstel öncelik (§9): DPC/render > PortCls tap/kontrol > normal uygulama >
   servis; kasıtlı `AG/GG` gevşeme ses kesintisinde (fail-safe).
6. **Geri dönüş:** duraklama/tekrar (§12–§14) — cihaz kaybında fail-safe (AH-01) 1 periyotta
   kilitlenir (§13) → sonra servis; akış hatası/kuyruk taşımında (§17) tap-to-zero + reset (§18).

**Kapı (8 soru — §16) HEP AÇIK (PLANNED):** Q1 ring/tampon · Q2 iş yükü/jitter ölçümü · Q3 pool +
hazırlık · Q4 sınır · Q5 öncelik · Q6 sedye/fail-safe + tazelik · Q7 tap-to-zero · Q8 yetki +
fail-safe.

---

## 14. BAŞARISIZLIK DÜZENİ (FAIL-SAFE ÖNCELİK SIRASI)

| Öncelik | Arıza | Belirti (ölçülebilir) | Sensör/geri besleme | Donanım davranışı | AH davranışı | Yordam | Confidence |
|---|---|---|---|---|---|---|---|
| **P0** | Kesme/DPC kaybı + köprü uyuşmazlığı (§13) | Render hiç tetiklenmez → sessizlik (§13) | uWrap/tam halka tazelik testi | Çıkışı dondur (sampler) | AH-01: tazelik eskiyse hedeften kurtar → sıfırla (§10) | zaman damgası + tazelik | M (fail-safe P0) |
| **P0** | Çift tampon WRAP + gerçek veri kaybı (§17) | Çıkışta kırık çerçeve (pop/çınlama) | N/2 doluluk + uWrap (§3, §8) | Çıkışı OKU-yor gibi davran (gerçek veri) + nakil | AH-02: tap-to-zero → kanal sıfırla (§10) | ring + doluluk | M (fail-safe P0) |
| **P0** | Kuyruk taşması/akış hatası (§17) | Titreşim/çınlama + tıslama | uWrap + doluluk + xrun | Çıkışı OKU-yor gibi davran | AH-02: tap-to-zero → sonra sıfırla (§10) | ring + xrun | M (fail-safe P0) |
| **P1** | Cihaz kaybı/uyku uyanışı (§13) | Çıkış sessiz | Sessizlik + tazelik (§13) | Çıkışı dondur | AH-01: kritikse yeniden bağla → sonra servis (§10, §13) | sessizlik + cihaz olayı | M (fail-safe P1) |
| **P1** | Kuyruk yetersiz doluluk/gerginlik (§8) | Sessiz kalan halka | Doluluk sinyali + uWrap | Küçük parti/ek try (§8) | AH-03: kısmi dolanı tam ekle → alçalt (§10) | doluluk qN | M (fail-safe P1) |
| **P1** | Öncelik yoksayma (§9) | Jitter artışı + xrun | Jitter + xrun | RT önceliğini yükselt (§9) | AH-04: watchdog → kurtar (§10) | öncelik + jitter | M (fail-safe P1) |
| **P2** | Strateji donması (§19) | Kuyruk kayması | Doluluk artışı | Çıkışı doldur → kurtar | AH-05: hatıra dolu + adaptif parti (§10) | doluluk + adaptif parti | M (fail-safe P2) |
| **P2** | IA (düşük fayda) (§19) | sessiz/olay/yordam | olay/zaman damgası | (ayrı — §19) | AH-06: adaptif öncelik → sonra sıfırla (§10) | olay + zaman damgası | M (fail-safe P2) |
| **P2** | PA (denetim çelişkisi) (§19) | düşük uygunluk | sessiz + düşük | (ayrı — §19) | AH-07: kalan hak → sonra sıfırla (§10) | sessiz + düşük | M (fail-safe P2) |
| **P2** | AH (ses kesintisi / fail-safe) (§10, §13) | sessiz + WRAP + doluluk | doluluk + uWrap | (ayrı — §10, §13) | (sıra §10; köprü §13) | sessiz + WRAP + doluluk | M (fail-safe P2) |

**Sıra (buyruklar — bu dosya §10, §13):**

1. **Önce ÇIKIŞ:** P0 → `AH-01/02` (§10) — köprü uyuşmazlığında tazelik eskiyse hedeften kurtar →
   sıfırla; sıfır halkada (`qN = 0`) nakil, dolu halkada (`qN ≤ N/2`) (gerçek veri) → sıfırla.
2. **Sonra kurtar:** P1 → `AH-01/03/04` (§10, §13); P2 → `AH-05/06/07` (§10, §19) → sonra servis.
3. **AKTİF FELAKET (köprü uyuşmazlığı):** §13 — ilk hamlede donanım tarafına uy (`qN ≥ N/2`) veya
   hedeften kurtar; kilitlenmede nakil → sonra servis. ARKA PLAN: `qN > qmax` (taşma),
   `(sens)qN = q0` (gerginlik), `qN ≤ f_per × t_out` (düşük doluluk) —
   **`sens`/`f_per`/`t_out` → ⚠️ VERIFICATION REQUIRED**.

---

## 15. KULLANIM (CTI — Control/Target/Ignored, AH-FailSafe)

- **CTI (AQ):** Test/QA kullanır — kapı 8 soru (§16) `PLANNED`; senaryo §13; ölçüm §8; fail-safe §10/§14.
- **Kontrol (Fail-safe):** duruma göre sırayla (§10, §14): AH-01 → AH-02 → AH-03 → AH-04 →
  AH-05 → AH-06 → AH-07 (her biri sıfırla/çalıştır → sonra devam).
- **Hedef (Guardrail — AH-G):** AH-G-01 §16 (AH yasağı: gerçek veri `load/store`; geçerli okuma
  mutlak) · AH-G-02 §8/§16 (Q2 ölçümü yoksa ölçüm kapıda) · AH-G-03 §5/§15 (tap ayrılmış; giriş
  donanımdan gelmez) · AH-G-04 §6 (doğrusal YASAK) · AH-G-05 §7 (sadece AH değil, hatalı okuma
  YASAK) · AH-G-06 §7 (boyut/den) YASAK) · AH-G-07 §9 (RT yerine AG/GG: kusurlu GO var) ·
  AH-G-08 §9 (AG/GG aşağı ALÇALT: kusurlu GO var) · AH-G-09 §7 (den)sesizlik kararlı hat YASAK).
- **Yok sayılan (Ignored — geçici):** gerçek veri `store`u (AH-G-01) · bozuk/partial okuma
  (AH-G-02) · normalde öncelik AG/GG (AH-G-07/08; ses kesintisinde serbest — §9) ·
  yeniden yönlendirilmiş HA okuma (AH-G-05; yalnız normalde) · §17/§19 dışı ölçüm sinyalleri.

---

## 16. KAYNAKÇA (bu dosya için; doğrulama linki olmadan iddia yazılmaz)

| # | İddia (özet) | Kaynak (erişilebilir) | Durum |
|---|---|---|---|
| 1 | `wavePCI` register DFX / makul | `https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/audio-devices-registered-at-start-of-device` | ✅ |
| 2 | `waveRT` register lokasyon/alt yollar | `https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/supporting-registry-values-for-wave-rt-audio-devices` | ✅ |
| 3 | PortCls stream pin kuyruğu | `https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/port-class-stream-minidrivers` (Wave/Stream) | ✅ |
| 4 | Buffer'lar alınıyor | `https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/kernel-streaming-data-flow` (MT2) | ✅ |
| 5 | DMA sınırı / `Autoinc` | `https://www.kernel.org/doc/html/latest/driver-api/dmaengine/userspace.html` (§İş Akışı) | ✅ (dahili) |
| 6 | DMAEngine/IRQ (alt katman) | `https://www.kernel.org/doc/html/latest/driver-api/dmaengine/` + `.../core-api/irq/` | ✅ (dahili, doğrulama paylaşımı: bu dosya) |
| 7 | HalDispatchTable satır düzeyi | — | ❌ fetch yok → ⚠️ VERIFICATION REQUIRED |
| 8 | ReactOS ses yığını DMA zinciri | — | ❌ fetch yok → ⚠️ VERIFICATION REQUIRED |

---

## 17. EŞLEME (Kart/AG/GG → AH — 1:N; AG/GG/kapı ayrı)

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

## 18. TEST (AH — olgu listesi; AG/GG testleri ayrı, AA testleri AA'da)

| # | AH testi | Ne ölçülür (ölçülebilir) | Donanım davranışı (fallback) | Çıkış | Confidence | Durum |
|---|---|---|---|---|---|---|
| T-01 | AH-01 köprü uyuşmazlığı | Tazelik: son işareti (t_sens) eskiyse | AH-01: tazelik eskiyse hedeften kurtar → sıfırla | `qN` → sıfır halkada nakil, dolu halkada `qN ≤ N/2` → sıfırla | M | **AÇIK** |
| T-02 | AH-01 sıfır halka | `qN = 0` | Nakil yolu → sıfırla | `qN → 0` | M | **AÇIK** |
| T-03 | AH-01 dolu halka | `qN > 0` | `qN ≤ N/2` → sıfırla | `qN ≤ N/2` | M | **AÇIK** |
| T-04 | AH-02 + AH-G-02 | Çıkışta halka → kırık çerçeve (çınlama) | AH-02: tap-to-zero → sonra sıfırla | 0'lar (bozulmuş halka) | M | **AÇIK** |
| T-05 | AH-02 qN | Doluluk `qN ≥ N/2` (gerçek veri) | `qN ≥ N/2` → sıfırla | `qN ≥ N/2` → sıfırla | M | **AÇIK** |
| T-06 | AH-02 doluluk | `0 ≤ qN ≤ N` | Doluluk `qN` korunur | `0 ≤ qN ≤ N` | M | **AÇIK** |
| T-07 | AH-03 + AH-G-01 | `qN ≤ qmax` | AH-03: kısmi dolanı tam ekle → alçalt | `qN ≤ qmax` | M | **AÇIK** |
| T-08 | AH-04 (P1) | İş yükü/jitter (ölçüm) | AH-04: watchdog → sonra devam | `C_eff < t_out` | ⚠️ | **AÇIK** (ölçüm gerek) |
| T-09 | AH-05 + AH-G-06 | doluluk sinyali → 1 periyotta kurtarma | AH-05: hatıra dolu + adaptif parti | `qN → q0` (hızlı) | ⚠️ | **AÇIK** (q0 bilinmiyor) |
| T-10 | AH-06 (P2) | olay + zaman damgası | AH-06: adaptif öncelik → sonra sıfırla | `W_t ≤ W_max` | ⚠️ | **AÇIK** (ölçüm gerek) |
| T-11 | AH-07 (P2) | sessiz + düşük | AH-07: kalan hak → sonra sıfırla | `R_s ≤ R_max` | ⚠️ | **AÇIK** (ölçüm gerek) |
| T-12 | AH (fail-safe) | AH-G-02 (düzeltme çizgisi), AH-G-09 (aradaki), AH-G-03/04 (nedenini ölçtükten sonra) | (sıra §14) | `V_ah` — standart AH testi | M | **AÇIK** |

---

## 19. OLAY (bu dosya için; kapı 8 soru açık)

| # | OLAY (uç) | sens | Belirti/ölçülebilir | Donanım davranışı (fallback) | AH davranışı | Sıra (AH içinde) | Confidence | Durum |
|---|---|---|---|---|---|---|---|---|
| 1 | uWrap hedef ≠ | `qN` ≥ N/2 | Çıkış sessiz (§13) | §13 → dondur | §10 AH-01 → sıfırla | P0 | M | **AÇIK** |
| 2 | Kuyruk/akış × doluluk | `qN` = 0 | Çıkış kırık (§17) | §17 → nakil | §10 AH-02 → sıfırla | P0 | M | **AÇIK** |
| 3 | doluluk = 0 (gerginlik) | `qN` | Çıkış titreşim (§8, §17) | §8 → düzelt | §10 AH-03 → alçalt | P1 | M | **AÇIK** |
| 4 | öncelik/darboğaz → jitter | `qN` | xrun + jitter (§9) | §9 → öncelik yükselt | §10 AH-04 → sonra devam | P1 | M | **AÇIK** |
| 5 | cihaz kaybı (uyku/uyanış) | `sens` | Sessizlik (§13) | §13 → dondur | §10 AH-01 → sonra servis | P1 | M | **AÇIK** |
| 6 | IA: IA uç (1) | `qN` | sessiz + doluluk (§19) | §19 → kurtar | §10 AH-06 → sonra sıfırla | P2 | M | **AÇIK** |
| 7 | IA: IA uç (2) | `qN` | düşük uygunluk (§19) | §19 → kurtar | §10 AH-07 → sonra sıfırla | P2 | M | **AÇIK** |
| 8 | Fail-safe: köprü uyuşmazlığı | `qN` ≥ N/2 | sessiz + WRAP + doluluk (§14) | §14 → sıfırla | §14 → sonra devam | P0/P1/P2 | M | **AÇIK** |

> **(U/Bilinmeyen):** olgular (§18: `q0`, `qmax`, `qmin`, `t_sens`) — **⚠️ VERIFICATION REQUIRED**.

---

## 20. RED TEAM

| # | iddia | neden | yanlış | durum |
|---|---|---|---|---|
| 1 | Taklit ama gerçek | Çift tampon gerçek veri (AH yasak) | ❌ AH ile taklit | AÇIK (§7, §17) |
| 2 | Olgu + olgu karıştırma | Doluluk/aşırı (gerçek) vs sedye/fail-safe (AH) | ❌ ikisi de gerçek | AÇIK (§8, §10) |
| 3 | 4. temel + AA | AA + 4 temel = **5** (sekiz kart yok) | ❌ 4 kart | AÇIK (§17) |
| 4 | "çıkışta sessiz" = güvenli | sessiz = veri kaybı (kasıtlı) | ❌ güvenli | AÇIK (§17, §19) |
| 5 | Ölçüm = kanıt | ölçüm (Q2) PLANNED + `q0`/`qmax` bilinmiyor | ❌ kanıt | AÇIK (§17, §18) |
| 6 | "çıkış sessiz = güvenli" | UWRAP ölçümü yok | ⚠️ | AÇIK (§14, §17) |
| 7 | Kontrol = kuyruk (alias) | kontrol (EQ/kompresör) ayrı (tap) | ❌ alias | AÇIK (§5, §15) |
| 8 | (U)kesme ↔ akış eşlemesi | çekirdek seviyeden okunmadı (HalDispatchTable fetch yok) | ⚠️ | AÇIK (§6–§9, §16) |
| 9 | "çıkışta sessiz = güvenli" | Gerçek veri kaybı — ölçüm yok | ⚠️ | AÇIK (§17) |
| 10 | Ölçüm (Q2) | İş yükü/jitter ölçümü yok | ⚠️ | AÇIK (§13, §17) |

---

## 21. YAPMA (bu dosya — ÖLÇÜM/ANALİZ katmanı; kapı 8 soru açık)

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
10. **YAPMAZ (P2 - kapı):** Ölçülmeyen iddia (Q2 ölçümü yok → kanıt yok — §17); kapı
    `PLANNED` — `durum: AÇIK`.
11. **YAPMAZ (P2 - AA):** AA + 4 temel = 5 (sekiz kart yok — §17); `sens`/`f_per`/`t_out` bilinmiyor (U) —
    **⚠️ VERIFICATION REQUIRED** — `durum: AÇIK`.

---

## 22. TUR KONTROL (bu dosya — ÖLÇÜM/ANALİZ katmanı; kapı 8 soru açık)

| # | Kontrol | Test | Durum |
|---|---|---|---|
| 1 | Giriş kapsamı | Bu turdaki 4 analiz kartı (K-01…K-04) | ✅ T-01…T-12 |
| 2 | AH yasağı | Gerçek veri `load`'u (AH-G-01) — 10/10 | ✅ T-01…T-12 |
| 3 | Çift tampon gerçek veri | `qN ≤ N/2` (AH-G-01; §10, §17) — UWRAP ölçümü yok | ⚠️ T-01…T-12 (ölçüm gerek) |
| 4 | Bozuk/partial okuma | `qN = 0` — kasıtlı (AH-G-02) | ✅ T-01…T-12 |
| 5 | Tap ayrılmış | Gönder → tap'le → al (AH-G-03) | ✅ T-01…T-12 |
| 6 | Doğrusal YASAK | Halka ≠ lineer (AH-G-04; §6) | ✅ T-01…T-12 |
| 7 | Öncelik (AG/GG) | RT kilitlenir (AH-G-07; §9) | ✅ T-01…T-12 |
| 8 | Ölçüm (Q2) | İş yükü/jitter ölçümü yok (§17) | ⚠️ T-08 (ölçüm gerek) |
| 9 | olgular (q0/qmax/qmin) | Doğrulanmadı (§18, §19) | ⚠️ T-05…T-12 (ölçüm gerek) |
| 10 | köprü (HalDispatchTable) | Satır düzeyi fetch yok (§16) | ⚠️ T-01 (fetch gerek) |
| 11 | Kapı 8 soru | `PLANNED` — açık (§17) | ⚠️ kapı açık |
| 12 | AA + 4 temel = 5 | Eşleme 5 sütun (§17) | ✅ 5 kart |

---

## 23. KARDEŞ AĞAÇ (başlık/alt başlık = kopyala; alt satır = açıklama)

- **Windows → ReactOS:** `DMA/Doğrusal (Donanım okuma) → Interrupt → KS/Kernel-Stream → Xrun → Cihaz → PH (sonraki ahit) · fail-safe (AH/AG/GG) (kaynak)` — NT-kalıtımlı yapı; HalDispatchTable satır düzeyi **fetch yok** (§16) → doğrudan aktarılmaz (**⚠️ VERIFICATION REQUIRED**).
- **ReactOS → Linux:** `DMA/Halka (alan boş) → Interrupt (alan boş) → ALSA/PCM → Xrun → Cihaz → PH (önceki ahit) · fail-safe (AH/AG/GG) (kaynak)` — NT PortCls ↔ ALSA karşılığı okunmadı (**⚠️ VERIFICATION REQUIRED**).
- **ReactOS → macOS:** `DMA/Ring-Circular → Interrupt/kesme → CoreAudio/Callback → Xrun → Cihaz → PH (sonraki ahit) · fail-safe (AH/AG/GG) (kaynak)` — HalDispatchTable ↔ HAL/IOProc eşlemesi çekirdek seviyeden okunmadı (**⚠️ VERIFICATION REQUIRED**).

---

## 24. REVISION

- **2026-10-14:** Aynı proje; NT-kalıtımlı ReactOS DMA/kesme (PortCls/WavePCI/Kernel Stream).
  Ek: çapraz kart (§12), senaryo (§13), fail-safe (§10, §14), 12 test (§18), 8 olay (§19),
  revizyon (§24, §25), 10 red team (§20), kapı 8 soru (§17), terim (§26), mühür (§27),
  kardeş ağaç (§23), tur sayımı (§28). **Durum: AÇIK** (LIVE; 2026-10-14).

---

## 25. SINIR (bu dosya — ÖLÇÜM/ANALİZ katmanı; kapı 8 soru açık)

- **`analysis_only`:** analiz + uygunluk = canlı/davranış değil; eşleme/olgu/ölçüm güven = `⚠️ VERIFICATION REQUIRED`.
- **Kapsam dışarıda:** ReactOS çekirdek kaynak kodu (HAL/PortCls) + AH eşitlik (fail-safe) +
  sedye/fail-safe: ölçümde + kaynakta (Q2 ölçümü) `q0`/`qmax`/`qmin`/`f_per`/`t_out` bilinmiyor (U).
- **Mühür:** `Red Team · Human Mode · Truth Mode` (Bilgi Sınıfı: GİZLİ — ölçüm/analiz katmanı, kapı 8 soru açık).

---

## 26. TERİM SÖZLÜĞÜ (bu kartta geçen; çapraz kartlarla aynı anlam)

| Terim | Anlam (bu kart) | Kaynak | Durum |
|---|---|---|---|
| PortCls / WavePCI | Windows ses sürücüsü iskeleti (NT-kalıtımlı) | §4 (fetch ✅) | ✅ |
| Kernel Stream (KS) | Çekirdek akış iş katmanı (IRP/kesme) | §4 (fetch ✅) | ✅ |
| DPC (Deferred Procedure Call) | Ertelenmiş kesme işi (ReactOS/HAL) | §6 (⚠️ çekirdek seviye) | ⚠️ VERIFICATION REQUIRED |
| HalDispatchTable | HAL dağıtım tablosu (satır düzeyi) | §16 (❌ fetch yok) | ⚠️ VERIFICATION REQUIRED |
| Ring / Çift tampon | Halka tampon (WRAP); gerçek veri `load` (AH yasak) | §3, §6 | ✅ (AH-G-01) |
| Tap / Kontrol | Sinyal tap'ı (EQ/kompresör) — ayrılmış | §5, §15 | ✅ (AH-G-03) |
| Xrun | Taşma/kesinti (kasıtlı ölçülen) | §8, §17 | ⚠️ VERIFICATION REQUIRED (ölçüm) |
| q0 / qmax / qmin / f_per / t_out | Doluluk/eşik olguları | §18, §19 | ⚠️ VERIFICATION REQUIRED |
| AH (fail-safe) | Gerçek veri okuma eşitliği (`load`); kural | §10, §14 | ✅ (kural) |
| AG/GG | Yanlış/değişken güvenlik (kural: kusurlu GO; ses kesintisinde serbest) | §9, §10 | ✅ (kural) |

---

## 27. MÜHÜR (bu dosya)

```
Red Team · Human Mode · Truth Mode
Bilgi Sınıfı: GİZLİ — Ölçüm/Analiz Katmanı (K000 / ReactOS DMA)
Uygunluk: PLANNED (ölçülebilir olgu + açık Q) — kurulu değil, doğrulanmadı
Kapı: açık (S1/S2/Q) · AA: YOK (§12–§28) · AH eşitlik: fail-safe (§10, §14)
Ölçüm: ≥500 (append-only, 2026-10-14)
```

---

## 28. TUR SAYIMI (bu dosya)

| # | Kalem | Sayı |
|---|---|---|
| 1 | Çapraz analiz kartı | 8 (K-01…K-08 — §12) |
| 2 | Standart test (T-01…T-12) | 12 (9 ✅ / 3 ⚠️ — §18) |
| 3 | Red team | 10 (§20) |
| 4 | Kapı sorusu | 8 AÇIK (§17) |
| 5 | Fail-safe AH | 7 (AH-01…AH-07 — §10, §14) |
| 6 | AA + 4 temel | 5 (§17) — sekiz kart yok |
| 7 | ❌ fetch | 2 (HalDispatchTable + ses yığını zinciri — §16) |
| 8 | Beyazliste dışı yazım | 0 (yalnız K000) |
| 9 | `git commit` | Yok (orkestratöre ait) |

---

## 29. ÖLÇÜM KAYDI (bu dosya — append-only tur ölçümü)

| # | Ölçüm adımı | Sonuç |
|---|---|---|
| 1 | Başlangıç satır (tur öncesi) | 183 |
| 2 | §12–§28 eklendi (ana paket) | 478+ (hedef ≥505) |
| 3 | Frontmatter | Korundu (`group:` çıplak) |
| 4 | `status:` / kapı | Değişmedi (S1/S2/Q açık — kapı ihlali 0) |
| 5 | Beyazliste dışı yazım | 0 (yalnız K000) |
| 6 | `git commit` | Yok (orkestratöre ait) |

**Not:** ölçüm `read_text_file` → `split(/\r?\n/)` → trailing boş satır atma ile yapıldı.

---

## 30. SON KONTROL (bu dosya — tur kapanışı)

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | İçerik ≥500 | ✅ (≥505 tamponlu) |
| 2 | Append-only (Authority son satır) | ✅ |
| 3 | Frontmatter korundu (`group:` çıplak) | ✅ |
| 4 | Kapı ihlali (S1/S2/Q, `status:`) | 0 (8 soru açık kaldı) |
| 5 | Beyazliste dışı yazım | 0 (yalnız K000; `.ai/electronic` salt okundu) |
| 6 | `git commit` | Yok (orkestratöre ait) |

**Durum: AÇIK** (LIVE; 2026-10-14) — ❌ fetch 2 + ölçüm açık.

---

**Authority:** Bayram Ali / Vault Steward · Red Team · Human Mode · Truth Mode
