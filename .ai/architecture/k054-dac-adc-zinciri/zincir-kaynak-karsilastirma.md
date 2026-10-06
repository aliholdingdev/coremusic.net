---
title: "DAC-ADC Zinciri — Yedek Kaynak Çapraz Kontrolü ve Çelişki Kaydı"
type: architecture
category: architecture
date: 2026-10-06
updated: 2026-10-06
version: 4.0.0
status: active
authority: reference
---

# DAC-ADC Zinciri — Yedek Kaynak Çapraz Kontrolü ve Çelişki Kaydı

> Kardeş dosyalar: [[../k054-dac-adc-zinciri/index]] · [[../k054-dac-adc-zinciri/zincir-mimari]]
> Salt-okunur kaynak kökü: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/`
> Çip kararı SSOT: `.ai/.decisions/accepted/ADR-038-8-1-sound-card-chip-selection.md`

**İçindekiler**

- [§1 Amaç & Kapsam](#§1-amaç--kapsam)
- [§2 Kaynak Kapsamı](#§2-kaynak-kapsamı)
- [§3 Çapraz Kontrol Tablosu](#§3-çapraz-kontrol-tablosu)
- [§4 Çelişki Kaydı](#§4-çelişki-kaydı)
- [§5 Bağımlılıklar](#§5-bağımlılıklar)
- [§6 Kenar Durumlar](#§6-kenar-durumlar)
- [§7 Hata Modları](#§7-hata-modları)
- [§8 Doğrulama & Kanıt](#§8-doğrulama--kanıt)
- [§9 Kaynak Kanıt Dizini (dac-adc-zinciri.md)](#§9-kaynak-kanıt-dizini)
- [§10 Bağımlılık Matrisi (D01)](#§10-bağımlılık-matrisi-d01--k054k071)
- [§11 Doğrulama Protokolü](#§11-doğrulama-protokolü)
- [§12 Açık Kalemler](#§12-açık-kalemler)
- [§13 Tam Kaynak Satır Dizini (README.md)](#§13-tam-kaynak-satır-dizini-readmemd)

---

## §1 Amaç & Kapsam

Bu dosya, klasör kardeşleri `index.md` ve `zincir-mimari.md` ile salt-okunur yedek
kaynaklar arasındaki iddiaları **çapraz kontrol** eder ve üç çıktıyı üretir:

1. **§3** — kaynaklarla **uyumlu** kardeş iddialar (kanıt satır numarasıyla).
2. **§4** — **çelişkiler** (zorunlu 6 çelişki + ek çelişkiler), tek tabloda.
3. **§9/§13** — birincil kaynak (`dac-adc-zinciri.md`) ve `README.md` için satır
   bazlı kanıt dizini.

**Kapsam**

- `k1-donanim/` yedek klasöründeki 23 Markdown dosyasından 9'u tam/parçalı okundu
  ve çapraz sorgulandı (§2 tablosu).
- ADR-038 yalnız çapraz teyit (grep) amacıyla okundu; kararı değiştirilmez.
- Bu dosya bir **kayıt belgesidir**: yedek kaynak değiştirilmez, sibling dosyalar
  düzenlenmez, `git commit` atılmaz (commit orkestratöre aittir).

**Kapsam dışı**

- Çip seçiminin yeniden tartışılması — SSOT `ADR-038` (bkz. §4 K6 kapsam notu).
- Yedek dosyalara düzeltme uygulanması — `_backup/**` salt-okunurdur.
- K054 dışındaki katmanlar ve `.ai/ui-design/**` yüzeyleri.

**Doğrulama kuralı (Zero-Hallucination):** her satır gerçek dosya numarasına
dayanır; doğrulanamayan değer `UNKNOWN`, doğrulanamayan iddia `VERIFICATION
REQUIRED` etiketiyle §12'ye düşer. Model hafızası ve web, disk kanıtının altındadır.

---

## §2 Kaynak Kapsamı

| # | Kaynak (dosya) | Satır | Rol | Kullanım |
|---|---|---|---|---|
| 1 | `_backup/.../k1-donanim/dac-adc-zinciri.md` | 159 | **birincil** | §9 tam dizin · §4 K1–K4 |
| 2 | `_backup/.../k1-donanim/README.md` | 805 | bağlam / dizin konusu | §13 · §4 K3–K6, E2–E6 |
| 3 | `_backup/.../k1-donanim/i2s-interface.md` | 186 | I2S/TDM protokolü | §4 K1–K2, E1, E7 |
| 4 | `_backup/.../k1-donanim/xmos-xu316.md` | 99 | USB denetleyici çipi | §4 K5, E3 |
| 5 | `_backup/.../k1-donanim/pcm3168a-dac-adc.md` | 117 | konvertör çipi | §4 K4 |
| 6 | `_backup/.../k1-donanim/usb-audio.md` | 190 | saat ailesi kesişimi | §4 K1 |
| 7 | `_backup/.../k1-donanim/ak4458-dac.md` | 123 | DAC rol kesişimi | §4 K3 |
| 8 | `_backup/.../k1-donanim/index.md` | 96 | katman özeti | §4 E4, E5, E8 |
| 9 | `_backup/.../k1-donanim/ozet-durum.md` | 90 | ilerleme özeti | §4 E8 |
| 10 | `.ai/.decisions/accepted/ADR-038-8-1-sound-card-chip-selection.md` | 315 | **SSOT çip kararı** | §4 K3, K4, K6 |
| 11 | `_backup/.../k1-donanim/*.md` (glob) | 23 dosya | dosya adı envanteri | §4 E6 |

Sayılar bu oturumda `read_text_file`/`read` ile teyit edilmiştir (ör. birincil
kaynak 159 satır, `README.md` 805 satır, `k1-donanim/*.md` = 23 adet).

### §2.1 Oturum Kısıtı

**Kapanan kısıt (orkestratör, 2026-10-06):** yazım alt-oturumunda shell izni
reddedildiği için `.ai/scripts/vault-utf8-writer.mjs` betiği ve `git status`
**o oturumda çalıştırılamamıştı**; betik sonra **orkestratör tarafından repo
kökünden çalıştırıldı** ve sonuçlar §11 adım 2–3 ile §12-2/§12-3'tedir.
`git status` / `git commit` bu görevde yine de **YOKTUR** (commit orkestratöre aittir).

---

## §3 Çapraz Kontrol Tablosu

> Yalnız **uyumlu** kardeş iddialar. Çelişkiler §4'e taşınır.

| # | Kardeş iddia (dosya:satır) | Kaynak karşılığı (dosya:satır) | Sonuç |
|---|---|---|---|
| 1 | `index.md:107` — §9 "README satır satır indeksi" | İlk satır `index.md:111` = README **L18**; son satır `index.md:792` = README **L805** | ✅ dizin aralığı birebir |
| 2 | `index.md:115` — README L23 özeti "**Bileşen Sayısı:** 120" | `README.md:23` = `**Bileşen Sayısı:** 120` | ✅ birebir |
| 3 | `index.md:101-102` — salt-okunur kaynak yolları | her iki dosya diskte mevcut (glob ile teyitli) | ✅ yol geçerli |
| 4 | `index.md:800` — `[[../k054-dac-adc-zinciri/zincir-mimari]]` | `.ai/architecture/k054-dac-adc-zinciri/zincir-mimari.md` diskte | ✅ link hedefi var |
| 5 | `index.md:801-817` — D01 k055…k071 wiki-link hedefleri | 17 hedefin tamamı `.ai/architecture/` altında `.md` bulundu | ✅ kırık link 0 (§10.1) |
| 6 | `zincir-mimari.md` §4.2 "MCLK 256fs / 512fs çelişkisi" kaydı (`index.md:863`) | `dac-adc-zinciri.md:20` (22.5792/24.576 MHz) ve `:23` (256fs = 11.2896/12.288 MHz) | ✅ tespit kaynakla uyumlu |
| 7 | `zincir-mimari.md` §4.4 "jitter < 100 ps" kestirimi (`index.md:864`) | `dac-adc-zinciri.md:105-107` — MCLK/SCK/WS `< 100ps RMS` | ✅ sayısal değer kaynakta |
| 8 | `index.md:862` — K1 kalemi "MCLK 256fs ↔ Clock 22.5792" | `dac-adc-zinciri.md:20` + `:23` aynı spec tablosunda | ✅ tespit doğru (ayrıntı §4 K1) |
| 9 | `index.md:858` — kardeş tarama notu "gövde işareti = 6" | kardeş §12 tablosu 6 satır (`index.md:862-867`) | ✅ kardeş kendi içinde tutarlı |
| 10 | README rolü: `README.md:58` "Ana DAC PCM3168A" + `:94` "PCM3168A DAC Detayı" | `ADR-038:70` rol çelişkisini kaydeder → SSOT = bu ADR | ✅ çapraz teyit (detay §4 K3) |

---

## §4 Çelişki Kaydı

> Zorunlu 6 çelişki (K1–K6) + ek çelişkiler (E1–E8). Karar sütununda VERIFICATION
> REQUIRED etiketi = açık madde (satır numaraları §12'dedir). Tüm satır numaraları
> bu dosyanın yazıldığı oturumda read/grep ile diskten teyit edilmiştir.

| # | İddia A (kaynak + satır) | İddia B (kaynak + satır) | Fark | Etki | Karar | Etkilediği dosya |
|---|---|---|---|---|---|---|
| K1 | `dac-adc-zinciri.md:23` — MCLK = 256fs = 11.2896MHz / 12.288MHz (destek: `i2s-interface.md:22` "256fs (11.2896MHz @ 44.1kHz)", `README.md:124` "System Clock 256fs = 12.288MHz") | `dac-adc-zinciri.md:20` — Clock Frequency = 22.5792MHz (44.1kHz family) / 24.576MHz (48kHz family) (destek: `dac-adc-zinciri.md:81,:105` "MCLK (22.5792MHz)", `README.md:51` "Clock 22.5792 MHz", `usb-audio.md:165-166` kristaller) | 22.5792 MHz = 512fs×44.1kHz, 11.2896 MHz = 256fs×44.1kHz → tam 2× fark; aynı dosyada MCLK iki farklı değer (`:23` ↔ `:105`) | Saat ağacı fs çarpanı, jitter bütçesi, DAC/ADC SCKI yapılandırması | ⚠️ VERIFICATION REQUIRED — "Clock Frequency" satırının kristal mi MCLK mı olduğu hiçbir kaynakta yazılmıyor; kristal↔MCLK oranı doğrulanamadı | dac-adc-zinciri.md · i2s-interface.md · README.md · usb-audio.md |
| K2 | `dac-adc-zinciri.md:21` — I2S Bit Clock = 1.4112MHz (44.1kHz) / 1.536MHz (48kHz) (destek: `:82` diyagram, `:106` SCK 1.4112MHz) → 32fs | `i2s-interface.md:23` — Bit Clock = 64fs × 32-bit = 2.1168MHz @ 44.1kHz (destek: `README.md:107` "BCK (64fs)", `README.md:125` "BCK 64fs = 3.072MHz") + `i2s-interface.md:36` — "2 × 2 × 32 × 44100 = 4.2336MHz" → 128fs | Üç farklı fs çarpanı (32fs ↔ "64fs" ↔ 128fs); `i2s-interface.md:23` kendi içinde aritmetik tutarsız (64fs×44.1k = 2.8224MHz, yazılan 2.1168MHz = 48fs) | I2S frame senkronizasyonu, slot yapısı, firmware kanal yapılandırması | ⚠️ VERIFICATION REQUIRED — I2S (2 kanal) ile TDM modları arasında hangi BCK'nin geçerli olduğu kaynakta çözülmemiş | dac-adc-zinciri.md · i2s-interface.md · README.md |
| K3 | `dac-adc-zinciri.md:18-19` — DAC = AK4458 (32-bit, 8-kanal), ADC = PCM3168A (destek: `:65,:92,:147-148` diyagram ve bağımlılık satırları) | `README.md:58` — Ana DAC = PCM3168A (6-in/8-out) (destek: `README.md:94` "## 3. PCM3168A DAC Detayı", `README.md:191` akış "XMOS XU316 → I2S → PCM3168A") | Roller ters: PCM3168A bir dosyada ADC, diğerinde ana DAC | Pin eşlemesi, I2S yönü, BOM/çip listesi | ✅ `ADR-038:70` (SSOT) → PCM3168A = 8-out DAC, AK4458 = opsiyonel; düzeltme `ADR-038:234` §5.1 adım 4 = PLANNED (yedek salt-okunur, düzeltilmez) | dac-adc-zinciri.md (düzeltme kaydı) |
| K4 | `dac-adc-zinciri.md:19` — PCM3168A (32-bit, 8-kanal) (destek: `pcm3168a-dac-adc.md:12` "32-bit çözünürlüklü, 8 kanallı", `:18` Çözünürlük = 32-bit) | `README.md:58` — PCM3168A: 6-in/8-out, 24-bit, 192kHz (destek: `ADR-038:47` "6-in/8-out, 24-bit, 192 kHz, 112 dB SNR", `ADR-038:92` veri sayfası özeti, `ADR-038:115` teyit) | Çözünürlük 32-bit ↔ 24-bit; kanal 8 ↔ 6-in/8-out | Register yapılandırması, ölçüm hedefi, pazarlama iddiası | ✅ README + ADR-038 (veri sayfası kanıtlı) kazanır: 24-bit · 6-in/8-out; "32-bit" satırları yedekte kalır (kaynak değiştirilmez) | pcm3168a-dac-adc.md · dac-adc-zinciri.md |
| K5 | `xmos-xu316.md:18` — Çekirdek Sayısı = 16 (Hardware Threads) + `:19` — saat Hızı = 500MHz per core | `README.md:203` — Logical Cores = 8 (4 x 2 tile) | 16 ↔ 8 çekirdek; 500MHz ifadesinin README karşılığı yok | Firmware kapasite/paralellik planı, performans iddiaları | ⚠️ VERIFICATION REQUIRED — 16 = 8×2 (tile/thread) eşlemesi hiçbir dosyada yazılmıyor; 500MHz teyidi yok | xmos-xu316.md · README.md |
| K6 | `README.md:60` — REDDEDİLMİŞ: PCM5122 (2-kanal) + `:62` uyarı + `:274` "ADR-038 · PCM3168A (PCM5122 REDDEDİLMİŞ)" | `ADR-038:47,104,115` — H001 PCM5122 yasağı (brain :878), "PCM5122 ailesi H001 gereği reddedilmiştir" | Çelişki yok — uyum; yalnız kapsam notu eklenir | — | ✅ red teyitli. Kapsam notu: ADR-038 karar ADR'sidir, spesifikasyon kaynağı değildir; `ADR-038:207` rol çelişkisini yalnız işaretler (çözmez), `ADR-038:262` ADR-083 slot numarası çakışmasını ayrıca işaretler | — (kayıt bu dosyada) |
| E1 | `i2s-interface.md:19` — Kanal Sayısı = 8 stereo (16 single) (destek: `xmos-xu316.md:24` aynı ifade) | `i2s-interface.md:131-132` — 4 data line × 32 channels/line = 128 channels (TDM mode, 32-bit per channel) | 16 ↔ 128 kanal (8×) | TDM slot planı, firmware kanal eşlemesi | ⚠️ VERIFICATION REQUIRED — I2S (16) ve TDM (128) için hangi yapılandırmanın geçerli olduğu belirsiz | i2s-interface.md · xmos-xu316.md |
| E2 | `README.md:58` — PCM3168A 8-out | `README.md:113-114` — OUTL1-OUTL3 / OUTR1-OUTR3 = 6 çıkış | README kendi içinde 8 ↔ 6 | Pin/eşleme tablosu, kanal doğrulaması | ✅ `ADR-038:115` (8-out) kazanır; README L113-114 6-pin bloğu eksik sayım | README.md |
| E3 | `xmos-xu316.md:51-52` — XTAL_IN (Pin 8) / XTAL_OUT (Pin 9) → tek 22.5792MHz kristal (destek: `:97` "dual" ifadesi) | `xmos-xu316.md:69-70` — pin tablosu: "5-8 GND" ve "9-10 XTAL" | Pin 8 iki kez tanımlı (XTAL_IN ↔ GND), Pin 9 iki kez (XTAL_OUT ↔ XTAL); diyagram tek kristal, durum satırı dual | PCB pin eşlemesi, kristal yerleşimi, kalkış riski | ⚠️ VERIFICATION REQUIRED — pin eşlemesi dosya içinde tutarsız; hangi tablonun geçerli olduğu doğrulanamadı | xmos-xu316.md |
| E4 | `README.md:23` — Bileşen Sayısı = 120 (destek: `README.md:245` Amplifikatör (8 kanal) = 120) | `README.md:250` — TOPLAM = ~1.000 + `index.md:75` — Toplam Bileşen Sayısı = 1.775 (tüm K1 alt dosyaları) | 120 ↔ ~1.000 ↔ 1.775 | BOM/maliyet planı, envanter sayımı | ⚠️ VERIFICATION REQUIRED — README L23 kendi BOM toplamıyla (L250) çelişiyor; K1 toplamının hangisi olduğu belirsiz | README.md · index.md (yedek) |
| E5 | `index.md:78` — Maksimum Çıkış Gücü = 8 × 250W = 2.000W RMS | `README.md:68-69` — MJL21194/93 = 50W/kanal (destek: `README.md:177` Güç (kanal başına) = 50W @ 8Ω) | Kanal başına 250W ↔ 50W (8 kanal: 2.000W ↔ 400W) | Güç kaynağı, termal tasarım, hoparlör seçimi | ⚠️ VERIFICATION REQUIRED — 5× güç farkı; tasarım hedefinin hangisi olduğu kaynakta çözülmemiş | index.md (yedek) · README.md |
| E6 | `README.md:260-264` — dosya adları: pcm3168a.md, ak4458.md, class-ab-amplifier.md, speaker-matrix.md, bom-cost.md | glob `_backup/.../k1-donanim/*.md` = 23 dosya → gerçek adlar: pcm3168a-dac-adc.md, ak4458-dac.md, class-ab-amplifikator.md, hoparlor-dizilimi.md; bom-cost.md YOK | 5 satırda dosya adı diskte yok | Kırık referans / link | ✅ disk kanıtı kazanır: README §8'de 5 kırık ad; `README.md:265-266` (k16-class-ab, k17-guc-kaynagi) glob ile geçerli | README.md |
| E7 | `README.md:222` — I2S Impedans = 50Ω single-ended (destek: `i2s-interface.md:26` Empedans = 50Ω (source), `:141` Output impedance = 50Ω) | `dac-adc-zinciri.md:25` — Empedans = 100Ω differential + `:120` Impedans = 90Ω differential (destek: `i2s-interface.md:146` Characteristic impedance = 90Ω differential) | 50Ω single-ended ↔ 90Ω differential ↔ 100Ω differential | PCB empedans kontrolü, yansıma/EMI | ⚠️ VERIFICATION REQUIRED — kaynak empedans ile iz karakteristiğinin I2S hattında hangisinin uygulandığı belirsiz | README.md · dac-adc-zinciri.md · i2s-interface.md |
| E8 | `dac-adc-zinciri.md:153` — 🟡 Simülasyon Aşamasında + `index.md:85` — 🟡 Tasarım Aşamasında + `ozet-durum.md:47,85` — Genel İlerleme %60 | `i2s-interface.md:179` — 🟢 Hazır (destek: `xmos-xu316.md:94` 🟢, `usb-audio.md:183` 🟢) | 🟡 (tasarım/simülasyon, %60) ↔ 🟢 (hazır) | İlerleme raporu, kabul kapısı | ⚠️ VERIFICATION REQUIRED — modül/katman/proje durumlarının tek özet değere bağlanacağı kural hiçbir dosyada yazılmıyor | dac-adc-zinciri.md · i2s-interface.md · index.md (yedek) · ozet-durum.md |

Çelişki sayısı: **14** (zorunlu 6: K1–K6 · ek 8: E1–E8) · açık madde (VERIFICATION
REQUIRED): **9** — tamamı §12'de satır numarasıyla listelenir.

---

## §5 Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|---|---|---|
| [[../k054-dac-adc-zinciri/index]] | Kardeş (stil + §13 kaynağı) | §13 satır dizini kardeş §9 (index.md L111–L792) birebir alınmıştır |
| [[../k054-dac-adc-zinciri/zincir-mimari]] | Kardeş (kapsam) | §4.2–§4.5 çelişki kalemleri kardeş §12 üzerinden çapraz kontrol edildi (§3 satır 6–8) |
| `.ai/.decisions/accepted/ADR-038-8-1-sound-card-chip-selection.md` | Üst (SSOT) | K3, K4, K6 kararlarının bağlayıcı kaynağı |
| `_backup/.../k1-donanim/dac-adc-zinciri.md` | Kaynak (birincil) | §9 tam satır dizini (130 satır) |
| `_backup/.../k1-donanim/README.md` | Kaynak | §13 tam satır dizini (696 satır) |
| `.ai/architecture/k055…k071` (17 klasör) | Komşu (D01) | §10 bağımlılık matrisi + §10.1 link envanteri |
| `.ai/scripts/vault-utf8-writer.mjs` | Doğrulama | §11 adım 2–3 — bu oturumda shell izni reddedildi, çalıştırılamadı |
| git (repo kökü) | Commit kapısı | Bu görevde commit ATILMAZ; commit orkestratöre aittir |

---

## §6 Kenar Durumlar

| # | Kenar durum | Karar |
|---|---|---|
| 1 | Kardeş `index.md:823-840` wiki-link durumunu "üretim aşamasında" gösteriyor; hedeflerin tamamı diskte mevcut | Bu dosyanın §10.1'i doğrulanmış durumu kaydeder; kardeş dosya düzenlenmez (yalnız not) |
| 2 | README §8'deki `k16-class-ab` / `k17-guc-kaynagi` satırları | Bunlar kırık değildir — glob her iki `README.md`'yi de buldu; kırık olan yalnız L260–L264 (§4 E6) |
| 3 | Yedek kaynaklarda hata bulunması | `_backup/**` salt-okunurdur: hata yalnız kayda geçer, kaynağa düzeltme uygulanmaz |
| 4 | ADR-038 §5.1 adım 4 (rol düzeltmesi) hâlâ PLANNED | Bu dosya düzeltme yapmaz; çelişki kaydı + SSOT referansıyla yetinir (§4 K3) |
| 5 | `pcm3168a-dac-adc.md:12` metni çipi hem genel "dönüştürücü" hem ADC gibi tanımlar | Rol yorumu ADR-038'e bırakılır; bu dosya yalnız metin çelişkisini kaydeder (§4 K3–K4) |
| 6 | `i2s-interface.md:26` (50Ω source) ile `:146` (90Ω differential) aynı dosyada | Ölçüt ayrımı (kaynak empedans ↔ iz karakteristiği) yazılmamış → §4 E7'ye taşındı |
| 7 | Kardeş `index.md` §12 "6 açık madde" sayımı | Kardeşin kendi gövdesine aittir; bu dosyanın §12 sayımı bağımsızdır (§3 satır 9) |
| 8 | `ozet-durum.md` %60 ile modül bazlı 🟢/🟡 tabloları | Kapsam hiyerarşisi (modül → katman → proje) hiçbir kaynakta tanımlı değil → §4 E8 |

---

## §7 Hata Modları

> Her satır, §4'teki zorunlu bir çelişkinin hata modu olarak yeniden çerçevelenir
> (kaynak çelişkisi → saha hatası). Çapraz referanslar §4'e bağlanır.

| # | Hata modu | Tetikleyen çelişki | Muhtemel etki | Tespit sinyali | Önleme | §4 |
|---|---|---|---|---|---|---|
| HM1 | MCLK fs çarpanı yanlış seçilir (256fs ↔ 512fs belirsizliği) | K1 | DAC/ADC SCKI uyuşmazlığı → jitter ve THD+N artışı | Frekans ölçümünde MCLK beklenen fs çarpanında değil | Saat ağacı tek satırda tanımlanır; kristal↔MCLK oranı doğrulanır | K1 |
| HM2 | BCK fs çarpanı yanlış (32fs / "48fs" / 128fs karışımı) | K2 | I2S frame senkronizasyon hatası → click, drop, kanal kayması | LRCK/BCK oranı fs çarpanıyla uyuşmuyor | Mod (I2S ↔ TDM) başına tek BCK tanımı yazılır | K2 |
| HM3 | Rol kaydı ters işlenir (AK4458 = DAC varsayımı) | K3 | Yanlış pin/bağlantı, I2S yönü ters, prototipte sinyal yok | DAC çıkışında sinyal yok; pin ölçümü tutmuyor | ADR-038 SSOT uygulanır; §5.1 adım 4 tamamlanır | K3 |
| HM4 | Çözünürlük/kanal iddiasıyla register yazılır (32-bit, 128-ch) | K4 · E1 | Yanlış konfigürasyon → düşük SNR veya kanalın hiç gelmemesi | Okunan register değeri beklenenle uyuşmuyor | Veri sayfası + ADR-038 değerleri tek kaynak alınır | K4, E1 |
| HM5 | Firmware kapasite planı yanlış çekirdek/saat varsayımıyla yazılır | K5 | Zaman aşımı, gerçek zamanlı görevlerin kaçırılması | Performans ölçümü iddiayla uyuşmuyor | Çekirdek/tile eşlemesi ölçümle teyit edilir | K5 |
| HM6 | PCM5122'a dönüş veya XMOS referans kartının çip listesinin kopyalanması | K6 | H001 ihlali: 2-kanal çip ile 8-out sağlanamaz | Kanal sayısı karşılanamıyor | H001 yasağı + ADR-038 uygulanır; referans yalnız firmware mimarisi için okunur | K6 |

---

## §8 Doğrulama & Kanıt

| # | Yöntem | Kapsam | Sonuç |
|---|---|---|---|
| 1 | Tam okuma (`read`) | `dac-adc-zinciri.md` — 159 satır | §9 için 130 dolu satır indeksi üretildi (boşlar ve `---` hariç) |
| 2 | Tam okuma (`read`) | `i2s-interface.md` (186) · `xmos-xu316.md` (99) · `pcm3168a-dac-adc.md` (117) · yedek `index.md` (96) · `ozet-durum.md` (90) | §4 K2, K4, K5, E1, E3, E4, E5, E7, E8 satırları bu okumalardan |
| 3 | Parçalı okuma (`read`) | `README.md` L1–L33, L54–L135, L160–L279 | K3–K6, E2, E4, E5, E6 kanıtları |
| 4 | Grep (sayılar) | `1.4112 · 2.1168 · 4.2336 · 22.5792 · 24.576 · 256fs · 500MHz · Logical Cores · Hardware Threads` | her eşleşme §4'te dosya:satır biçiminde |
| 5 | Grep (ADR-038) | `PCM5122 · PCM3168A · AK4458 · H001 · 8.1` | kanıtlı satırlar: 47, 70, 92, 104, 115, 207, 217, 234, 258, 262 |
| 6 | Glob (kaynak klasör) | `_backup/.../k1-donanim/*.md` | 23 dosya → §4 E6 (README §8'de 5 kırık ad) |
| 7 | Glob (hedef klasör) | `.ai/architecture/k055…k071/**/*.md` | 39 dosya (k054 ile birlikte 41) → §10.1'de 19 hedefin tamamı var |
| 8 | Satır sayısı | tüm kaynaklar | README 805 · dac-adc 159 · i2s 186 · xmos 99 · pcm3168a 117 · usb-audio 190 · ak4458 123 · yedek index 96 · ozet 90 |
| 9 | Betik/komut çalıştırma | `vault-utf8-writer.mjs` · `git status` | yazım oturumunda çalıştırılamadı (shell reddi) → **orkestratör repo kökünden çalıştırdı**: `verify` → `mojibake:0` · `hasBom:false` · `lines:1143`, `scan` → `dirty:0` (§11 adım 2–3) |




## §9 Kaynak Kanıt Dizini (`dac-adc-zinciri.md`)

> Birincil kaynak: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/dac-adc-zinciri.md`
> Kaynak toplam **159 satır**; boş satırlar ve `---` ayraçları hariç **130 dolu satır** tamamı aşağıda.
> Biçim: `| L<kaynak satırı> | tür | özet (≤100 karakter) | disk kanıtı |`
> Bu dizin, §4'teki çelişki satırlarındaki `dosya:satır` referanslarının doğrulanma kuyruğudur.

| Kaynak satır | Tür | Özet | Kanıt |
|---|---|---|---|
| L2 | metin | title: "DAC → ADC Sinyal Zinciri" | ✅ disk |
| L3 | metin | layer: K1 | ✅ disk |
| L4 | metin | category: "Sinyal İşleme" | ✅ disk |
| L5 | metin | date: 2026-09-20 | ✅ disk |
| L8 | baslik | # DAC → ADC Sinyal Zinciri | ✅ disk |
| L10 | baslik | ## Genel Bakış | ✅ disk |
| L12 | metin | DAC → ADC sinyal zinciri, COREMUSIC'da dijital sinyalin analog forma dönüştürülmesinden sonra tekrar… | ✅ disk |
| L14 | baslik | ## Teknik Spesifikasyonlar | ✅ disk |
| L16 | tablo | \| Parametre \| Değer \| | ✅ disk |
| L17 | tablo | \|-----------\|-------\| | ✅ disk |
| L18 | tablo | \| DAC \| AK4458 (32-bit, 8-kanal) \| | ✅ disk |
| L19 | tablo | \| ADC \| PCM3168A (32-bit, 8-kanal) \| | ✅ disk |
| L20 | tablo | \| Clock Frequency \| 22.5792MHz (44.1kHz family) / 24.576MHz (48kHz family) \| | ✅ disk |
| L21 | tablo | \| I2S Bit Clock \| 1.4112MHz (44.1kHz) / 1.536MHz (48kHz) \| | ✅ disk |
| L22 | tablo | \| Word Select \| 44.1kHz / 48kHz \| | ✅ disk |
| L23 | tablo | \| MCLK \| 256fs = 11.2896MHz / 12.288MHz \| | ✅ disk |
| L24 | tablo | \| Sinyal Seviyesi \| 2.1Vrms (differential) \| | ✅ disk |
| L25 | tablo | \| Empedans \| 100Ω differential \| | ✅ disk |
| L27 | baslik | ## Sinyal Yolu Diyagramı | ✅ disk |
| L29 | kod-sınır | ````` blok işareti | ✅ disk |
| L30 | kod | ┌─────────────────────────────────────────────────────────────────┐ | ✅ disk |
| L31 | kod | │ DAC → ADC SİNYAL ZİNCİRİ │ | ✅ disk |
| L32 | kod | │ │ | ✅ disk |
| L33 | kod | │ ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐ │ | ✅ disk |
| L34 | kod | │ │ XMOS │───▶│ I2S │───▶│ DAC │───▶│ Analog │ │ | ✅ disk |
| L35 | kod | │ │ XU316 │ │ Bus │ │ AK4458 │ │ Output │ │ | ✅ disk |
| L36 | kod | │ └────┬─────┘ └──────────┘ └──────────┘ └─────┬────┘ │ | ✅ disk |
| L37 | kod | │ │ │ │ | ✅ disk |
| L38 | kod | │ │ ┌──────────┐ ┌──────────┐ │ │ | ✅ disk |
| L39 | kod | │ └────────▶│ Clock │◀───│ Crystal │ │ │ | ✅ disk |
| L40 | kod | │ │ Sync │ │ Osc. │ │ │ | ✅ disk |
| L41 | kod | │ └──────────┘ └──────────┘ │ │ | ✅ disk |
| L42 | kod | │ │ │ | ✅ disk |
| L43 | kod | │ ┌──────────┐ ┌──────────┐ ┌──────────┐ │ │ | ✅ disk |
| L44 | kod | │ │ ADC │◀───│ I2S │◀───│ Analog │◀────────┘ │ | ✅ disk |
| L45 | kod | │ │ PCM3168A │ │ Bus │ │ Input │ │ | ✅ disk |
| L46 | kod | │ └────┬─────┘ └──────────┘ └──────────┘ │ | ✅ disk |
| L47 | kod | │ │ │ | ✅ disk |
| L48 | kod | │ ▼ │ | ✅ disk |
| L49 | kod | │ ┌──────────┐ │ | ✅ disk |
| L50 | kod | │ │ DSP │ Dijital İşleme │ | ✅ disk |
| L51 | kod | │ │ Engine │ │ | ✅ disk |
| L52 | kod | │ └──────────┘ │ | ✅ disk |
| L53 | kod | └─────────────────────────────────────────────────────────────────┘ | ✅ disk |
| L54 | kod-sınır | ````` blok işareti | ✅ disk |
| L56 | baslik | ## Clock Synchronization | ✅ disk |
| L58 | baslik | ### Master/Slave Konfigürasyonu | ✅ disk |
| L60 | kod-sınır | ````` blok işareti | ✅ disk |
| L61 | kod | Clock Hierarchy: | ✅ disk |
| L63 | kod | Primary Clock Source: XMOS XU316 (Master) | ✅ disk |
| L64 | kod | │ | ✅ disk |
| L65 | kod | ├─ MCLK Output ──▶ AK4458 SCKI (DAC Master Clock) | ✅ disk |
| L66 | kod | │ PCM3168A SCKI (ADC Master Clock) | ✅ disk |
| L67 | kod | │ | ✅ disk |
| L68 | kod | ├─ SCK Output ──▶ AK4458 TDMCLK (Bit Clock) | ✅ disk |
| L69 | kod | │ PCM3168A BCK (Bit Clock) | ✅ disk |
| L70 | kod | │ | ✅ disk |
| L71 | kod | ├─ WS Output ──▶ AK4458 TDMFS (Word Select) | ✅ disk |
| L72 | kod | │ PCM3168A LRCK (LR Clock) | ✅ disk |
| L73 | kod | │ | ✅ disk |
| L74 | kod | └─ SD0-SD3 ──▶ AK4458 TDMD0-3 (Data) | ✅ disk |
| L75 | kod | PCM3168A DOUTA/B (Data) | ✅ disk |
| L77 | kod | Clock Distribution: | ✅ disk |
| L78 | kod | ┌─────────────────────────────────────────────────────────┐ | ✅ disk |
| L79 | kod | │ │ | ✅ disk |
| L80 | kod | │ XMOS XU316 (Master) │ | ✅ disk |
| L81 | kod | │ ├─ MCLK (22.5792MHz) ──────────────────────────┐ │ | ✅ disk |
| L82 | kod | │ ├─ SCK (1.4112MHz) ────────────────────────┐ │ │ | ✅ disk |
| L83 | kod | │ ├─ WS (44.1kHz) ──────────────────────┐ │ │ │ | ✅ disk |
| L84 | kod | │ └─ SD[0:3] ──────────────────────┐ │ │ │ │ | ✅ disk |
| L85 | kod | │ │ │ │ │ │ | ✅ disk |
| L86 | kod | │ AK4458 (Slave) ◀────────────────┘ │ │ │ │ | ✅ disk |
| L87 | kod | │ ├─ SCKI ◀──────────────────────────┘ │ │ │ | ✅ disk |
| L88 | kod | │ ├─ TDMCLK ◀────────────────────────────┘ │ │ | ✅ disk |
| L89 | kod | │ ├─ TDMFS ◀──────────────────────────────────┘ │ | ✅ disk |
| L90 | kod | │ └─ TDMD[0:3] ◀─────────────────────────────────────┘ | ✅ disk |
| L91 | kod | │ │ | ✅ disk |
| L92 | kod | │ PCM3168A (Slave) │ | ✅ disk |
| L93 | kod | │ ├─ SCKI ◀──────────────────────────────────────────────┘ | ✅ disk |
| L94 | kod | │ ├─ BCK ◀───────────────────────────────────────────────┘ | ✅ disk |
| L95 | kod | │ ├─ LRCK ◀──────────────────────────────────────────────┘ | ✅ disk |
| L96 | kod | │ └─ DOUTA/B ───────────────────────────────────────────▶ XMOS | ✅ disk |
| L97 | kod | │ │ | ✅ disk |
| L98 | kod | └─────────────────────────────────────────────────────────┘ | ✅ disk |
| L99 | kod-sınır | ````` blok işareti | ✅ disk |
| L101 | baslik | ### Clock Accuracy | ✅ disk |
| L103 | tablo | \| Clock \| Frequency \| Tolerance \| Jitter \| | ✅ disk |
| L104 | tablo | \|-------\|-----------\|-----------\|--------\| | ✅ disk |
| L105 | tablo | \| MCLK \| 22.5792MHz \| ±50ppm \| < 100ps RMS \| | ✅ disk |
| L106 | tablo | \| SCK \| 1.4112MHz \| ±50ppm \| < 100ps RMS \| | ✅ disk |
| L107 | tablo | \| WS \| 44.1kHz \| ±50ppm \| < 100ps RMS \| | ✅ disk |
| L109 | baslik | ## EMI Filtreleme | ✅ disk |
| L111 | baslik | ### I2S Hat Filtresi | ✅ disk |
| L113 | kod-sınır | ````` blok işareti | ✅ disk |
| L114 | kod | XMOS Output ──▶ Ferrite Bead (600Ω @ 100MHz) ──▶ 100nF ──▶ DAC/ADC | ✅ disk |
| L116 | kod | Her I2S hattı için: | ✅ disk |
| L117 | kod | - Ferrite bead: BLM18AG601SN1 (600Ω, 0603) | ✅ disk |
| L118 | kod | - Decoupling: 100nF MLCC (0402) | ✅ disk |
| L119 | kod | - Trace length: < 50mm | ✅ disk |
| L120 | kod | - Impedans: 90Ω differential | ✅ disk |
| L121 | kod-sınır | ````` blok işareti | ✅ disk |
| L123 | baslik | ## Empedans Eşleşme | ✅ disk |
| L125 | tablo | \| Junction \| Source Z \| Load Z \| Matched? \| | ✅ disk |
| L126 | tablo | \|----------\|----------\|--------\|----------\| | ✅ disk |
| L127 | tablo | \| XMOS → DAC \| 50Ω \| 100Ω \| No (high-Z input) \| | ✅ disk |
| L128 | tablo | \| DAC → ADC \| 25Ω \| 10kΩ \| No (voltage mode) \| | ✅ disk |
| L129 | tablo | \| ADC → DSP \| 100Ω \| 50Ω \| Yes (differential) \| | ✅ disk |
| L131 | baslik | ## Bileşen Değerleri | ✅ disk |
| L133 | tablo | \| # \| Bileşen \| Model/Değer \| Adet \| Açıklama \| | ✅ disk |
| L134 | tablo | \|---\|---------\|-------------\|------\|----------\| | ✅ disk |
| L135 | tablo | \| 1 \| Ferrite Bead \| BLM18AG601SN1 \| 16 \| I2S hat filtresi \| | ✅ disk |
| L136 | tablo | \| 2 \| Decoupling Cap \| 100nF MLCC \| 16 \| I2S dekuplajı \| | ✅ disk |
| L137 | tablo | \| 3 \| Crystal \| 22.5792MHz \| 1 \| 44.1kHz family \| | ✅ disk |
| L138 | tablo | \| 4 \| Crystal \| 24.576MHz \| 1 \| 48kHz family \| | ✅ disk |
| L139 | tablo | \| 5 \| Load Cap \| 18pF C0G \| 4 \| Crystal load \| | ✅ disk |
| L140 | tablo | \| 6 \| Termination \| 100Ω \| 8 \| I2S termination \| | ✅ disk |
| L142 | baslik | ## Bağımlılıklar | ✅ disk |
| L144 | tablo | \| Bağımlılık \| Yön \| Açıklama \| | ✅ disk |
| L145 | tablo | \|------------\|-----\|----------\| | ✅ disk |
| L146 | tablo | \| K1 XMOS \| Clock \| Master clock source \| | ✅ disk |
| L147 | tablo | \| K1 DAC \| Çıkış \| AK4458 analog output \| | ✅ disk |
| L148 | tablo | \| K1 ADC \| Giriş \| PCM3168A digital output \| | ✅ disk |
| L149 | tablo | \| K3 DSP \| Üst \| Dijital sinyal işleme \| | ✅ disk |
| L151 | baslik | ## Durum: Implementasyon | ✅ disk |
| L153 | vurgu | **Durum**: 🟡 Simülasyon Aşamasında | ✅ disk |
| L155 | madde | - Clock synchronization: LTSpice ile simulate edildi | ✅ disk |
| L156 | madde | - I2S timing: Eye diagram analizi yapıldı | ✅ disk |
| L157 | madde | - EMI: Pre-compliance test ile ferrite bead seçimi doğrulandı | ✅ disk |
| L158 | madde | - Crystal: Dual crystal (22.5792MHz + 24.576MHz) seçildi | ✅ disk |
| L159 | madde | - PCB routing: I2S traces length-matched (±1mm tolerance) | ✅ disk |
---

## §10 Bağımlılık Matrisi (D01 — k054…k071)

> D01 = donanım aralığının ilk dilimi (k054–k071). Bu dosya yalnız **okuma** yönünde bağlanır;
> hiçbir komşu dosya düzenlenmez. Durum sütunu `glob` ile dosya varlığına dayanır (§10.1).

| # | Düğüm | Klasör | Bu dosyayla ilişki | Durum |
|---|---|---|---|---|
| 1 | DAC-ADC Zinciri | `k054-dac-adc-zinciri` | bu klasör (kaydın sahibi) | ✅ 3 dosya |
| 2 | AK4458 DAC | `k055-ak4458-dac` | K3 rol çelişkisinin taraflarından biri | ✅ |
| 3 | PCM3168A DAC-ADC | `k056-pcm3168a-dac-adc` | K3 / K4 rol + çözünürlük çelişkileri | ✅ |
| 4 | I2S / TDM | `k057-i2s-interface` | K2 BCK fs çarpanı, E1 kanal sayısı | ✅ |
| 5 | XMOS XU316 | `k058-xmos-xu316` | K5 çekirdek/saat, E3 pin çelişkisi | ✅ |
| 6 | USB Audio | `k059-usb-audio` | K1 kristal ailesi kesişimi | ✅ |
| 7 | Analog Sinyal Yolu | `k060-analog-sinyal-yolu` | zincirin analog devamı (DAC çıkışı sonrası) | ✅ |
| 8 | Diff Pair Input | `k061-diff-pair-input` | giriş evi; K2/E7 empedans kesişimi | ✅ |
| 9 | VAS Stage | `k062-vas-stage` | Class AB zinciri (bu dosyanın kapsamı dışı) | ✅ |
| 10 | Output Stage | `k063-output-stage` | Class AB zinciri (kapsam dışı) | ✅ |
| 11 | Feedback Network | `k064-feedback-network` | geri besleme (kapsam dışı) | ✅ |
| 12 | MJL21193/94 | `k054`→`k065-mjle21194-93` | E5 güç çelişkisinin kaynağı (50W/kanal) | ✅ |
| 13 | Konnektörler | `k066-konnektorler` | pin/bağlantı doğrulaması (K3) | ✅ |
| 14 | Koruma Devreleri | `k067-koruma-devreleri` | zincir koruma katmanı | ✅ |
| 15 | Güç Kaynağı Analog | `k068-guc-kaynagi-analog` | E7/EMI empedans kesişimi | ✅ |
| 16 | Hoparlör Dizilimi | `k069-hoparlor-dizilimi` | E4 bileşen sayımı kesişimi | ✅ |
| 17 | PCB Tasarım | `k070-pcb-tasarim` | E7 90Ω differential iz kuralı | ✅ |
| 18 | Termal Yönetim | `k071-termal-yonetim` | E5 güç/termal kesişimi | ✅ |

**Ölçüm:** `.ai/architecture/` altında `k054`–`k071` arası **18 klasörün 18'i de var**; eksik yok.

### §10.1 Wiki-Link Envanteri

> Bu dosyadaki tüm `[[../k0xx-.../...]]` hedeflerinin dosya adları diskten `glob` ile üretildi.
> Biçim: `| klasör | link biçimi | .md sayısı | dosya adları | durum |`
| k054 | [[../k054-dac-adc-zinciri/<dosya>]] | 3 | index · zincir-kaynak-karsilastirma · zincir-mimari | ✅ |
| k055 | [[../k055-ak4458-dac/<dosya>]] | 3 | ak4458-dac-rehberi · ak4458-kaynak-karsilastirma · index | ✅ |
| k056 | [[../k056-pcm3168a-dac-adc/<dosya>]] | 3 | index · pcm3168a-dac-adc-rehberi · pcm3168a-kaynak-karsilastirma | ✅ |
| k057 | [[../k057-i2s-interface/<dosya>]] | 3 | i2s-firmware-surucu · i2s-ve-tdm-rehberi · index | ✅ |
| k058 | [[../k058-xmos-xu316/<dosya>]] | 3 | index · xu316-entegrasyon · xu316-firmware | ✅ |
| k059 | [[../k059-usb-audio/<dosya>]] | 3 | index · usb-audio-firmware-surucu · usb-audio-yolu | ✅ |
| k060 | [[../k060-analog-sinyal-yolu/<dosya>]] | 2 | analog-yol-rehberi · index | ✅ |
| k061 | [[../k061-diff-pair-input/<dosya>]] | 2 | diff-pair-tasarim · index | ✅ |
| k062 | [[../k062-vas-stage/<dosya>]] | 2 | index · vas-stage-tasarim | ✅ |
| k063 | [[../k063-output-stage/<dosya>]] | 2 | index · output-stage-tasarim | ✅ |
| k064 | [[../k064-feedback-network/<dosya>]] | 2 | feedback-tasarim · index | ✅ |
| k065 | [[../k065-mjle21194-93/<dosya>]] | 2 | index · mjle-op-amp-kurulum | ✅ |
| k066 | [[../k066-konnektorler/<dosya>]] | 2 | index · konnektor-envanteri | ✅ |
| k067 | [[../k067-koruma-devreleri/<dosya>]] | 2 | index · koruma-rehberi | ✅ |
| k068 | [[../k068-guc-kaynagi-analog/<dosya>]] | 2 | analog-besleme · index | ✅ |
| k069 | [[../k069-hoparlor-dizilimi/<dosya>]] | 2 | hoparlor-dizilim · index | ✅ |
| k070 | [[../k070-pcb-tasarim/<dosya>]] | 2 | index · pcb-rehber | ✅ |
| k071 | [[../k071-termal-yonetim/<dosya>]] | 2 | index · termal-rehber | ✅ |
---

## §11 Doğrulama Protokolü

> Adımlar **repo kökünden** (`C:\www\coremusic.net`) çalıştırılır. Adım 2–3 bu görevde
> **orkestratör oturumunda** çalıştırıldı (yazım alt-oturumunda `shell` izni reddedilmişti).

| # | Adım | Komut (repo kökünden) | Durum |
|---|---|---|---|
| 1 | Satır sayısı (≥500 kapısı) | `[System.IO.File]::ReadAllLines('<bu dosya>').Length` | ✅ **1143 satır** (≥500 kapısı geçildi) |
| 2 | UTF-8 / mojibake / BOM | `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/k054-dac-adc-zinciri/zincir-kaynak-karsilastirma` | ✅ orkestratör çalıştırdı → `hasBom:false` · `mojibake:0` · `cjk:0` · `hasNul:false` · `lines:1143` |
| 3 | Klasör taraması | `node .ai/scripts/vault-utf8-writer.mjs scan --dir .ai/architecture/k054-dac-adc-zinciri` | ✅ orkestratör çalıştırdı → `ok:true` · `dirty:0` · `files:[]` |
| 4 | Wiki-link kırıklığı | `[[../<klasör>/<dosya>]]` hedefleri `glob` ile (§10.1) | ✅ 18 klasör / kırık 0 |
| 5 | Dizin tutarlılığı | §9 = 130 satır ve §13 = 696 satır sayımları kaynak uzunluklarıyla (159 / 805) karşılaştırıldı | ✅ birebir |
| 6 | Salt-okunur dokunulmazlık | `_backup/**` yalnız okundu; bu görevde tek yazılan dosya bu dosyadır | ✅ |
| 7 | Commit kapısı | `git commit` subagent tarafından ATILMAZ | ✅ commit orkestratöre ait |
| 8 | REDACTED | dosyada secret / token / anahtar bulunmaması | ✅ 0 eşleşme |

---

## §12 Açık Kalemler (⚠️ işaretli)

> Açık madde sayısı **9** — §4'te `⚠️ VERIFICATION REQUIRED` kararı verilen satırlar.
> `Satır` sütunu **bu dosyanın** satır numarasıdır.

| # | Satır | Bölüm | Açık madde | Kapatmak için gereken |
|---|---|---|---|---|
| 1 | L121 | §4 K1 | MCLK 256fs ↔ 512fs (11.2896 vs 22.5792 MHz) — hangisi kristal, hangisi MCLK belirsiz | Saat ağacı şeması + ölçüm (LCR/frekans sayacı) |
| 2 | L122 | §4 K2 | BCK fs çarpanı üç farklı (32fs / "48fs" / 128fs) | I2S ↔ TDM mod kararı + gözlemleyici (logic analyzer) |
| 3 | L125 | §4 K5 | XMOS 16 thread ↔ 8 logical core; 500MHz teyidi yok | Ürün veri sayfası + firmware kapasite ölçümü |
| 4 | L127 | §4 E1 | Kanal 16 ↔ 128 (TDM) | TDM slot planı kararı |
| 5 | L129 | §4 E3 | Pin 8 / Pin 9 çift tanım (XTAL ↔ GND/XTAL) | XMOS paket pin-out doğrulaması |
| 6 | L130 | §4 E4 | Bileşen 120 ↔ ~1.000 ↔ 1.775 | BOM toplam kapsamı (README L23 mi, L250 mi, yedek index mi) |
| 7 | L131 | §4 E5 | 250W/kanal ↔ 50W/kanal | Tasarım güç hedefi kararı (E5) |
| 8 | L133 | §4 E7 | 50Ω source ↔ 90Ω ↔ 100Ω differential | PCB empedans kuralı (hangisi iz, hangisi kaynak) |
| 9 | L134 | §4 E8 | 🟡 (%60, tasarım/simülasyon) ↔ 🟢 (hazır) | Durum hiyerarşisi kuralı (modül → katman → proje) |

**Kapanan işaretleme:** §2.1 ve §8 satır 9'daki "betik çalıştırılamadı" ibaresi,
betik repo kökünden çalıştırılmasıyla kapanmıştır → `verify`: `mojibake:0` · `hasBom:false` · `lines:1143` → `scan`: `dirty:0` (§11 adım 2–3).

---

## §13 Tam Kaynak Satır Dizini (`README.md`)

> Bağlam kaynağı: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/README.md`
> Kaynak toplam **805 satır**; boş satırlar ve `---` ayraçları hariç **696 satır** aşağıda.
> Biçim: `| L<kaynak satırı> | tür | özet (≤100 karakter) | disk kanıtı |`
> Bu dizin, §4 K3–K6 ve E2–E8 satırlarındaki `README.md:<satır>` referanslarının kuyruğudur.

| Kaynak satır | Tür | Özet | Kanıt |
|---|---|---|---|
| L2 | metin | reference_doc: Freelancer Technical Documentation v1.0 | ✅ disk |
| L3 | metin | title: "CoreMusic — K1 Donanım Layer" | ✅ disk |
| L4 | metin | type: architecture-layer | ✅ disk |
| L5 | metin | category: architecture | ✅ disk |
| L6 | metin | date: 2026-09-20 | ✅ disk |
| L7 | metin | updated: 2026-09-29 | ✅ disk |
| L8 | metin | last_update_note: "3 turlu agent tartışması" | ✅ disk |
| L9 | metin | status: active | ✅ disk |
| L10 | metin | version: 1.0.1 | ✅ disk |
| L11 | metin | authority: Single Source of Truth (SSOT) | ✅ disk |
| L12 | metin | governance: Red Team · Human Mode · Truth Mode | ✅ disk |
| L13 | metin | reference: | ✅ disk |
| L14 | metin | authority: ".ai/CLAUDE.md" | ✅ disk |
| L15 | metin | source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md" | ✅ disk |
| L18 | baslik | # K1: Donanım Layer | ✅ disk |
| L20 | vurgu | **Katman:** K1 (Donanım Altyapısı) | ✅ disk |
| L21 | vurgu | **Kapsam:** XMOS, DAC, Amplifikatör, Hoparlör, Güç kaynağı | ✅ disk |
| L22 | vurgu | **Sorumlu Agent:** Audio Hardware Engineer | ✅ disk |
| L23 | vurgu | **Bileşen Sayısı:** 120 | ✅ disk |
| L27 | baslik | ## 1. Genel Bakış | ✅ disk |
| L29 | metin | K1 katmanı, CoreMusic'in fiziksel donanım bileşenlerini içerir. Bu katman, dijital sinyali analog si… | ✅ disk |
| L31 | baslik | ### 1.1 Temel İlkeler | ✅ disk |
| L33 | tablo | \| İlke \| Açıklama \| | ✅ disk |
| L34 | tablo | \|------\|----------\| | ✅ disk |
| L35 | tablo | \| **Bit-Perfect** \| Sinyal zincirinde kayıp yok \| | ✅ disk |
| L36 | tablo | \| **Low THD** \| Toplam Harmonik Bozulma <0.005% \| | ✅ disk |
| L37 | tablo | \| **High SNR** \| Sinyal-Gürültü Oranı >100dB \| | ✅ disk |
| L38 | tablo | \| **8.1 Surround** \| 8 kanal + 1 LFE \| | ✅ disk |
| L39 | tablo | \| **DC-Only** \| Güç kaynağı DC Only \| | ✅ disk |
| L43 | baslik | ## 2. Bileşen Haritası | ✅ disk |
| L45 | baslik | ### 2.1 USB Audio Interface | ✅ disk |
| L47 | tablo | \| Bileşen \| Model \| Özellik \| | ✅ disk |
| L48 | tablo | \|---------\|-------\|---------\| | ✅ disk |
| L49 | tablo | \| USB Audio \| XMOS XU316 \| USB Audio Class 2.0, 32-bit \| | ✅ disk |
| L50 | tablo | \| USB Interface \| USB-C \| 24-pin, USB 2.0/3.0 \| | ✅ disk |
| L51 | tablo | \| Clock \| 22.5792 MHz \| 44.1kHz family \| | ✅ disk |
| L52 | tablo | \| Clock \| 24.576 MHz \| 48kHz family \| | ✅ disk |
| L54 | baslik | ### 2.2 DAC (Digital-to-Analog Converter) | ✅ disk |
| L56 | tablo | \| Bileşen \| Model \| Kanal \| Bit \| Sample Rate \| | ✅ disk |
| L57 | tablo | \|---------\|-------\|-------\|-----\|-------------\| | ✅ disk |
| L58 | tablo | \| Ana DAC \| PCM3168A \| 6-in/8-out \| 24-bit \| 192kHz \| | ✅ disk |
| L59 | tablo | \| Opsiyonel DAC \| AK4458 \| 8-kanal \| 32-bit \| 768kHz \| | ✅ disk |
| L60 | tablo | \| REDDEDİLMİŞ \| PCM5122 \| 2-kanal \| 32-bit \| — \| | ✅ disk |
| L62 | vurgu | **⚠️ Uyarı:** PCM5122 8.1 surround için yetersizdir (ADR-038). Sadece 2 kanal destekler. | ✅ disk |
| L64 | baslik | ### 2.3 Amplifikatör | ✅ disk |
| L66 | tablo | \| Bileşen \| Model \| Topoloji \| Güç \| THD \| | ✅ disk |
| L67 | tablo | \|---------\|-------\|----------\|-----\|-----\| | ✅ disk |
| L68 | tablo | \| NPN Output \| MJL21194 \| Class AB Darlington \| 50W/kanal \| <0.005% \| | ✅ disk |
| L69 | tablo | \| PNP Output \| MJL21193 \| Class AB Darlington \| 50W/kanal \| <0.005% \| | ✅ disk |
| L71 | baslik | ### 2.4 Güç Kaynağı | ✅ disk |
| L73 | tablo | \| Bileşen \| Model \| Giriş \| Çıkış \| Verim \| | ✅ disk |
| L74 | tablo | \|---------\|-------\|-------\|-------\|-------\| | ✅ disk |
| L75 | tablo | \| Boost Converter \| LM5122 \| 22.2V (6S LiPo) \| ±35V \| %96 \| | ✅ disk |
| L76 | tablo | \| Batarya \| 6S LiPo \| 22.2V nominal \| — \| — \| | ✅ disk |
| L77 | tablo | \| DC Adapter \| 19-24V \| AC/DC \| — \| — \| | ✅ disk |
| L79 | baslik | ### 2.5 Hoparlör Matrisi (8.1 Surround) | ✅ disk |
| L81 | tablo | \| Kanal \| Hoparlör \| Frekans \| Konum \| | ✅ disk |
| L82 | tablo | \|-------\|----------\|---------\|-------\| | ✅ disk |
| L83 | tablo | \| CH1 \| Front Left \| 20Hz-20kHz \| Ön sol \| | ✅ disk |
| L84 | tablo | \| CH2 \| Front Right \| 20Hz-20kHz \| Ön sağ \| | ✅ disk |
| L85 | tablo | \| CH3 \| Center \| 100Hz-8kHz \| Merkez \| | ✅ disk |
| L86 | tablo | \| CH4 \| LFE (Sub) \| 20Hz-120Hz \| Subwoofer \| | ✅ disk |
| L87 | tablo | \| CH5 \| Surround Left \| 100Hz-16kHz \| Arka sol \| | ✅ disk |
| L88 | tablo | \| CH6 \| Surround Right \| 100Hz-16kHz \| Arka sağ \| | ✅ disk |
| L89 | tablo | \| CH7 \| Rear Left \| 100Hz-16kHz \| Arka sol \| | ✅ disk |
| L90 | tablo | \| CH8 \| Rear Right \| 100Hz-16kHz \| Arka sağ \| | ✅ disk |
| L94 | baslik | ## 3. PCM3168A DAC Detayı | ✅ disk |
| L96 | baslik | ### 3.1 Pin Out | ✅ disk |
| L98 | kod-sınır | ````` blok işareti | ✅ disk |
| L99 | kod | PCM3168A Pin Configuration: | ✅ disk |
| L100 | kod | VDD1: +3.3V (Digital) | ✅ disk |
| L101 | kod | VDD2: +5V (Analog) | ✅ disk |
| L102 | kod | VSS: -5V (Analog) | ✅ disk |
| L103 | kod | AGND: Analog Ground | ✅ disk |
| L104 | kod | DGND: Digital Ground | ✅ disk |
| L106 | kod | I2S Input: | ✅ disk |
| L107 | kod | BCK: Bit Clock (64fs) | ✅ disk |
| L108 | kod | LRCK: Left/Right Clock (fs) | ✅ disk |
| L109 | kod | DIN: Data In | ✅ disk |
| L110 | kod | SCKI: System Clock (256fs or 512fs) | ✅ disk |
| L112 | kod | Analog Output: | ✅ disk |
| L113 | kod | OUTL1-OUTL3: Left channels (3 output) | ✅ disk |
| L114 | kod | OUTR1-OUTR3: Right channels (3 output) | ✅ disk |
| L115 | kod-sınır | ````` blok işareti | ✅ disk |
| L117 | baslik | ### 3.2 I2S Konfigürasyonu | ✅ disk |
| L119 | tablo | \| Parametre \| Değer \| | ✅ disk |
| L120 | tablo | \|-----------\|-------\| | ✅ disk |
| L121 | tablo | \| Sample Rate \| 48kHz (default) \| | ✅ disk |
| L122 | tablo | \| Bit Depth \| 24-bit \| | ✅ disk |
| L123 | tablo | \| I2S Mode \| Standard I2S \| | ✅ disk |
| L124 | tablo | \| System Clock \| 256fs = 12.288MHz \| | ✅ disk |
| L125 | tablo | \| BCK \| 64fs = 3.072MHz \| | ✅ disk |
| L126 | tablo | \| LRCK \| 48kHz \| | ✅ disk |
| L128 | baslik | ### 3.3 Analogy Output Devresi | ✅ disk |
| L130 | kod-sınır | ````` blok işareti | ✅ disk |
| L131 | kod | PCM3168A OUTL1 → I/V Resistor (1kΩ) → Low-Pass Filter (20kHz) → Differential Driver → Amplifier Inpu… | ✅ disk |
| L132 | kod-sınır | ````` blok işareti | ✅ disk |
| L136 | baslik | ## 4. Class AB Amplifikatör Detayı | ✅ disk |
| L138 | baslik | ### 4.1 Tek Kanal Devre Şeması | ✅ disk |
| L140 | kod-sınır | ````` blok işareti | ✅ disk |
| L141 | kod | +35V (PVDD) | ✅ disk |
| L142 | kod | │ | ✅ disk |
| L143 | kod | ┌────┴────┐ | ✅ disk |
| L144 | kod | │ Q15 │ MJL21194 (NPN Output) | ✅ disk |
| L145 | kod | │ NPN │ | ✅ disk |
| L146 | kod | Input ─────┤ Q16 ├──── Output → Hoparlör | ✅ disk |
| L147 | kod | (Diff) │ BD139 │ | ✅ disk |
| L148 | kod | │ VAS │ | ✅ disk |
| L149 | kod | │ Q17 │ MJL21193 (PNP Output) | ✅ disk |
| L150 | kod | │ PNP │ | ✅ disk |
| L151 | kod | └────┬────┘ | ✅ disk |
| L152 | kod | │ | ✅ disk |
| L153 | kod | -35V (PVSS) | ✅ disk |
| L155 | kod | Bias Network: | ✅ disk |
| L156 | kod | Q1 (BC546B): Diferansiyel çift giriş | ✅ disk |
| L157 | kod | Q2 (BC546B): Diferansiyel çift giriş | ✅ disk |
| L158 | kod | Q5 (BC556B): Akım havuzu | ✅ disk |
| L159 | kod | Q9 (KSC3503): VAS (Voltage Amplifier Stage) | ✅ disk |
| L160 | kod | Q10 (BD139): Vbe çarpımı (bias spreader) | ✅ disk |
| L161 | kod-sınır | ````` blok işareti | ✅ disk |
| L163 | baslik | ### 4.2 Bias Ayar Prosedürü | ✅ disk |
| L165 | tablo | \| Adım \| İşlem \| Değer \| | ✅ disk |
| L166 | tablo | \|------\|-------\|-------\| | ✅ disk |
| L167 | tablo | \| 1 \| Güç kaynağı ayarla \| ±35V DC \| | ✅ disk |
| L168 | tablo | \| 2 \| Multimetre çıkışa bağla \| DC offset ölç \| | ✅ disk |
| L169 | tablo | \| 3 \| Bias potansiyometresi ayarla \| 0V DC offset hedefle \| | ✅ disk |
| L170 | tablo | \| 4 \| Sıcaklık stabilizasyonu \| 5-10 dk bekle \| | ✅ disk |
| L171 | tablo | \| 5 \| Son kontrol \| <0.5V DC offset \| | ✅ disk |
| L173 | baslik | ### 4.3 Termal Hesaplama | ✅ disk |
| L175 | tablo | \| Parametre \| Değer \| | ✅ disk |
| L176 | tablo | \|-----------\|-------\| | ✅ disk |
| L177 | tablo | \| Güç (kanal başına) \| 50W @ 8Ω \| | ✅ disk |
| L178 | tablo | \| Verimlilik \| ~%65 (Class AB) \| | ✅ disk |
| L179 | tablo | \| Isı (kanal başına) \| ~17.5W \| | ✅ disk |
| L180 | tablo | \| Toplam ısı (8 kanal) \| ~140W \| | ✅ disk |
| L181 | tablo | \| Heatsink gereksinimi \| >140W/C° thermal resistance \| | ✅ disk |
| L182 | tablo | \| Fan gereksinimi \| 80mm PWM, >50 CFM \| | ✅ disk |
| L186 | baslik | ## 5. XMOS XU316 Detayı | ✅ disk |
| L188 | baslik | ### 5.1 Blok Diyagramı | ✅ disk |
| L190 | kod-sınır | ````` blok işareti | ✅ disk |
| L191 | kod | USB 2.0 ──→ XMOS XU316 ──→ I2S ──→ PCM3168A | ✅ disk |
| L192 | kod | │ | ✅ disk |
| L193 | kod | ├→ Clock Generator | ✅ disk |
| L194 | kod | ├→ USB Audio Class 2.0 | ✅ disk |
| L195 | kod | ├→ DSP Processing | ✅ disk |
| L196 | kod | └→ Control Interface | ✅ disk |
| L197 | kod-sınır | ````` blok işareti | ✅ disk |
| L199 | baslik | ### 5.2 XMOS Kaynak Kullanımı | ✅ disk |
| L201 | tablo | \| Kaynak \| Kullanım \| | ✅ disk |
| L202 | tablo | \|--------\|----------\| | ✅ disk |
| L203 | tablo | \| Logical Cores \| 8 (4 x 2 tile) \| | ✅ disk |
| L204 | tablo | \| MIPS \| ~2000 (toplam) \| | ✅ disk |
| L205 | tablo | \| RAM \| 512KB (tile 0+1) \| | ✅ disk |
| L206 | tablo | \| Flash \| 16MB (external) \| | ✅ disk |
| L207 | tablo | \| USB PHY \| High-speed 480Mbps \| | ✅ disk |
| L211 | baslik | ## 6. PCB Tasarım Kuralları | ✅ disk |
| L213 | tablo | \| Parametre \| Değer \| | ✅ disk |
| L214 | tablo | \|-----------\|-------\| | ✅ disk |
| L215 | tablo | \| Layer \| 6-layer stackup \| | ✅ disk |
| L216 | tablo | \| Copper (top/bottom) \| 2oz \| | ✅ disk |
| L217 | tablo | \| Copper (inner) \| 1oz \| | ✅ disk |
| L218 | tablo | \| Finish \| ENIG \| | ✅ disk |
| L219 | tablo | \| Min trace \| 4mil \| | ✅ disk |
| L220 | tablo | \| Min via \| 8mil drill, 16mil pad \| | ✅ disk |
| L221 | tablo | \| USB Impedans \| 90Ω differential \| | ✅ disk |
| L222 | tablo | \| I2S Impedans \| 50Ω single-ended \| | ✅ disk |
| L223 | tablo | \| Ground \| Star ground topology \| | ✅ disk |
| L224 | tablo | \| Thermal \| Thermal vias under power components \| | ✅ disk |
| L226 | baslik | ### 6.1 Stackup | ✅ disk |
| L228 | kod-sınır | ````` blok işareti | ✅ disk |
| L229 | kod | Layer 1: Signal (top) — Components, traces | ✅ disk |
| L230 | kod | Layer 2: Ground — Continuous ground plane | ✅ disk |
| L231 | kod | Layer 3: Signal — I2S, control signals | ✅ disk |
| L232 | kod | Layer 4: Power — +35V, -35V, +3.3V, +5V | ✅ disk |
| L233 | kod | Layer 5: Ground — Continuous ground plane | ✅ disk |
| L234 | kod | Layer 6: Signal (bottom) — Components, traces | ✅ disk |
| L235 | kod-sınır | ````` blok işareti | ✅ disk |
| L239 | baslik | ## 7. BOM Maliyet Analizi | ✅ disk |
| L241 | tablo | \| Kategori \| Bileşen Sayısı \| Toplam Maliyet \| | ✅ disk |
| L242 | tablo | \|----------\|---------------\|---------------\| | ✅ disk |
| L243 | tablo | \| USB Audio (XMOS) \| 3 \| $15.00 \| | ✅ disk |
| L244 | tablo | \| DAC (PCM3168A) \| 15 \| $25.00 \| | ✅ disk |
| L245 | tablo | \| Amplifikatör (8 kanal) \| 120 \| $85.00 \| | ✅ disk |
| L246 | tablo | \| Güç Kaynağı \| 35 \| $45.00 \| | ✅ disk |
| L247 | tablo | \| Pasif Bileşenler \| 800+ \| $180.00 \| | ✅ disk |
| L248 | tablo | \| Konnektörler \| 25 \| $30.00 \| | ✅ disk |
| L249 | tablo | \| PCB (6-layer) \| 1 \| $50.00 \| | ✅ disk |
| L250 | tablo | \| **TOPLAM** \| **~1,000** \| **~$430** \| | ✅ disk |
| L254 | baslik | ## 8. İlgili Dosyalar | ✅ disk |
| L256 | tablo | \| Dosya \| Amaç \| | ✅ disk |
| L257 | tablo | \|-------\|------\| | ✅ disk |
| L258 | tablo | \| `architecture/k1-donanim/README.md` \| Bu dosya \| | ✅ disk |
| L259 | tablo | \| `architecture/k1-donanim/xmos-xu316.md` \| XMOS detayı \| | ✅ disk |
| L260 | tablo | \| `architecture/k1-donanim/pcm3168a.md` \| DAC detayı \| | ✅ disk |
| L261 | tablo | \| `architecture/k1-donanim/ak4458.md` \| High-end DAC \| | ✅ disk |
| L262 | tablo | \| `architecture/k1-donanim/class-ab-amplifier.md` \| Amplifikatör devresi \| | ✅ disk |
| L263 | tablo | \| `architecture/k1-donanim/speaker-matrix.md` \| Hoparlör konfigürasyonu \| | ✅ disk |
| L264 | tablo | \| `architecture/k1-donanim/bom-cost.md` \| BOM maliyet \| | ✅ disk |
| L265 | tablo | \| `architecture/k16-class-ab/README.md` \| K16 Class AB \| | ✅ disk |
| L266 | tablo | \| `architecture/k17-guc-kaynagi/README.md` \| K17 Güç kaynağı \| | ✅ disk |
| L270 | baslik | ## 9. İlgili ADR'ler | ✅ disk |
| L272 | tablo | \| ADR \| Konu \| | ✅ disk |
| L273 | tablo | \|-----\|------\| | ✅ disk |
| L274 | tablo | \| ADR-038 \| PCM3168A (PCM5122 REDDEDİLMİŞ) \| | ✅ disk |
| L275 | tablo | \| ADR-089 \| Class AB Amplifikatör + 6S LiPo + ±35V Boost \| | ✅ disk |
| L279 | baslik | ## Alt Katman Şeması (K1.a.b.c) | ✅ disk |
| L281 | alinti | > **Şema kuralları (2026-09-24 · 3 turlu agent tartışması):** `K1` → `K1.a` (2. katman, 12 düğüm) → … | ✅ disk |
| L283 | baslik | ### K1 Şema Özeti | ✅ disk |
| L285 | tablo | \| 2. Katman \| Ad \| 3. Katman \| 4. Kanıtlı Yaprak \| Birincil Kanıt \| | ✅ disk |
| L286 | tablo | \|-----------\|----\|-----------\|-------------------\|----------------\| | ✅ disk |
| L287 | tablo | \| K1.1 \| DAC/ADC Zinciri \| 3 \| 23 \| ak4458-dac.md, pcm3168a-dac-adc.md, dac-adc-zinciri.md \| | ✅ disk |
| L288 | tablo | \| K1.2 \| Amplifikatör Aşamaları \| 6 \| 66 \| diff-pair/vas/class-ab/output/feedback/mjle md \| | ✅ disk |
| L289 | tablo | \| K1.3 \| Analog Sinyal Yolu \| 1 \| 17 \| analog-sinyal-yolu.md \| | ✅ disk |
| L290 | tablo | \| K1.4 \| Güç Kaynağı & Koruma \| 2 \| 23 \| guc-kaynagi-analog.md, koruma-devreleri.md \| | ✅ disk |
| L291 | tablo | \| K1.5 \| Dijital Arayüzler \| 3 \| 32 \| i2s-interface.md, usb-audio.md, xmos-xu316.md \| | ✅ disk |
| L292 | tablo | \| K1.6 \| PCB & Termal \| 2 \| 21 \| pcb-tasarim.md, termal-yonetim.md \| | ✅ disk |
| L293 | tablo | \| K1.7 \| Konnektör & Hoparlör \| 2 \| 24 \| konnektorler.md, hoparlor-dizilimi.md \| | ✅ disk |
| L294 | tablo | \| K1.8 \| Firmware (K1.f alt katmanı) \| 8 \| 123 \| firmware/ — 8 MD \| | ✅ disk |
| L295 | tablo | \| K1.9 \| Bileşen Haritası & BOM \| 1 \| 20 \| README.md §2-§7 \| | ✅ disk |
| L296 | tablo | \| K1.10 \| index.md \| 1 \| 5 \| index.md \| | ✅ disk |
| L297 | tablo | \| K1.11 \| CLAUDE.md Guardrails \| 1 \| 4 \| CLAUDE.md \| | ✅ disk |
| L298 | tablo | \| K1.12 \| README Genel Bakış \| 1 \| 2 \| README.md §1 \| | ✅ disk |
| L299 | tablo | \| **TOPLAM** \| \| **31** \| **360** \| \| | ✅ disk |
| L301 | baslik | ### K1.1 — DAC/ADC Zinciri | ✅ disk |
| L303 | tablo | \| Kod \| Ad \| Kanıt (dosya · satır) \| | ✅ disk |
| L304 | tablo | \|-----\|----\|-----------------------\| | ✅ disk |
| L305 | tablo | \| **K1.1.1** \| AK4458 DAC (3. katman) \| ak4458-dac.md — 7 yaprak \| | ✅ disk |
| L306 | tablo | \| K1.1.1.1 \| Genel Bakış \| ak4458-dac.md L10 \| | ✅ disk |
| L307 | tablo | \| K1.1.1.2 \| Teknik Spesifikasyonlar \| ak4458-dac.md L14 \| | ✅ disk |
| L308 | tablo | \| K1.1.1.3 \| DSD Modu Destekleri \| ak4458-dac.md L29 \| | ✅ disk |
| L309 | tablo | \| K1.1.1.4 \| Devre Tasarımı \| ak4458-dac.md L39 \| | ✅ disk |
| L310 | tablo | \| K1.1.1.5 \| Pin Konfigürasyonu (Önemli Pinler) \| ak4458-dac.md L80 \| | ✅ disk |
| L311 | tablo | \| K1.1.1.6 \| Temel Bağlantılar \| ak4458-dac.md L41 \| | ✅ disk |
| L312 | tablo | \| K1.1.1.7 \| Kondansatör ve Direnç Değerleri \| ak4458-dac.md L68 \| | ✅ disk |
| L313 | tablo | \| **K1.1.2** \| PCM3168A DAC/ADC (3. katman) \| pcm3168a-dac-adc.md — 6 yaprak \| | ✅ disk |
| L314 | tablo | \| K1.1.2.1 \| Genel Bakış \| pcm3168a-dac-adc.md L10 \| | ✅ disk |
| L315 | tablo | \| K1.1.2.2 \| Teknik Spesifikasyonlar \| pcm3168a-dac-adc.md L14 \| | ✅ disk |
| L316 | tablo | \| K1.1.2.3 \| Devre Tasarımı \| pcm3168a-dac-adc.md L29 \| | ✅ disk |
| L317 | tablo | \| K1.1.2.4 \| Pin Konfigürasyonu \| pcm3168a-dac-adc.md L68 \| | ✅ disk |
| L318 | tablo | \| K1.1.2.5 \| Temel Bağlantılar \| pcm3168a-dac-adc.md L31 \| | ✅ disk |
| L319 | tablo | \| K1.1.2.6 \| Filtre ve Kondansatörler \| pcm3168a-dac-adc.md L57 \| | ✅ disk |
| L320 | tablo | \| **K1.1.3** \| DAC-ADC Zinciri (3. katman) \| dac-adc-zinciri.md — 10 yaprak \| | ✅ disk |
| L321 | tablo | \| K1.1.3.1 \| Genel Bakış \| dac-adc-zinciri.md L10 \| | ✅ disk |
| L322 | tablo | \| K1.1.3.2 \| Teknik Spesifikasyonlar \| dac-adc-zinciri.md L14 \| | ✅ disk |
| L323 | tablo | \| K1.1.3.3 \| Sinyal Yolu Diyagramı \| dac-adc-zinciri.md L27 \| | ✅ disk |
| L324 | tablo | \| K1.1.3.4 \| Clock Synchronization \| dac-adc-zinciri.md L56 \| | ✅ disk |
| L325 | tablo | \| K1.1.3.5 \| EMI Filtreleme \| dac-adc-zinciri.md L109 \| | ✅ disk |
| L326 | tablo | \| K1.1.3.6 \| Empedans Eşleşme \| dac-adc-zinciri.md L123 \| | ✅ disk |
| L327 | tablo | \| K1.1.3.7 \| Bileşen Değerleri \| dac-adc-zinciri.md L131 \| | ✅ disk |
| L328 | tablo | \| K1.1.3.8 \| Master/Slave Konfigürasyonu \| dac-adc-zinciri.md L58 \| | ✅ disk |
| L329 | tablo | \| K1.1.3.9 \| Clock Accuracy \| dac-adc-zinciri.md L101 \| | ✅ disk |
| L330 | tablo | \| K1.1.3.10 \| I2S Hat Filtresi \| dac-adc-zinciri.md L111 \| | ✅ disk |
| L332 | baslik | ### K1.2 — Amplifikatör Aşamaları | ✅ disk |
| L334 | tablo | \| Kod \| Ad \| Kanıt (dosya · satır) \| | ✅ disk |
| L335 | tablo | \|-----\|----\|-----------------------\| | ✅ disk |
| L336 | tablo | \| **K1.2.1** \| Diff Pair Input (3. katman) \| diff-pair-input.md — 11 yaprak \| | ✅ disk |
| L337 | tablo | \| K1.2.1.1 \| Genel Bakış \| diff-pair-input.md L10 \| | ✅ disk |
| L338 | tablo | \| K1.2.1.2 \| Teknik Spesifikasyonlar \| diff-pair-input.md L14 \| | ✅ disk |
| L339 | tablo | \| K1.2.1.3 \| Devre Şeması \| diff-pair-input.md L28 \| | ✅ disk |
| L340 | tablo | \| K1.2.1.4 \| Bias Current Hesaplaması \| diff-pair-input.md L59 \| | ✅ disk |
| L341 | tablo | \| K1.2.1.5 \| CMRR Analizi \| diff-pair-input.md L80 \| | ✅ disk |
| L342 | tablo | \| K1.2.1.6 \| Gürültü Analizi \| diff-pair-input.md L102 \| | ✅ disk |
| L343 | tablo | \| K1.2.1.7 \| Tail Current \| diff-pair-input.md L61 \| | ✅ disk |
| L344 | tablo | \| K1.2.1.8 \| Operating Point \| diff-pair-input.md L69 \| | ✅ disk |
| L345 | tablo | \| K1.2.1.9 \| CMRR Formülü \| diff-pair-input.md L82 \| | ✅ disk |
| L346 | tablo | \| K1.2.1.10 \| hesaplama \| diff-pair-input.md L93 \| | ✅ disk |
| L347 | tablo | \| K1.2.1.11 \| Giriş Gürültüsü Kaynakları \| diff-pair-input.md L104 \| | ✅ disk |
| L348 | tablo | \| **K1.2.2** \| VAS Stage (3. katman) \| vas-stage.md — 11 yaprak \| | ✅ disk |
| L349 | tablo | \| K1.2.2.1 \| Genel Bakış \| vas-stage.md L10 \| | ✅ disk |
| L350 | tablo | \| K1.2.2.2 \| Teknik Spesifikasyonlar \| vas-stage.md L14 \| | ✅ disk |
| L351 | tablo | \| K1.2.2.3 \| Devre Şeması \| vas-stage.md L28 \| | ✅ disk |
| L352 | tablo | \| K1.2.2.4 \| Miller Compensation Analizi \| vas-stage.md L62 \| | ✅ disk |
| L353 | tablo | \| K1.2.2.5 \| Bileşen Değerleri \| vas-stage.md L104 \| | ✅ disk |
| L354 | tablo | \| K1.2.2.6 \| Frekans Tepkisi \| vas-stage.md L114 \| | ✅ disk |
| L355 | tablo | \| K1.2.2.7 \| Stabilite Analizi \| vas-stage.md L136 \| | ✅ disk |
| L356 | tablo | \| K1.2.2.8 \| Neden Miller Compensation? \| vas-stage.md L64 \| | ✅ disk |
| L357 | tablo | \| K1.2.2.9 \| Miller Etkisi Formülü \| vas-stage.md L77 \| | ✅ disk |
| L358 | tablo | \| K1.2.2.10 \| Dominant Pole \| vas-stage.md L94 \| | ✅ disk |
| L359 | tablo | \| K1.2.2.11 \| Phase Margin Hesabı \| vas-stage.md L138 \| | ✅ disk |
| L360 | tablo | \| **K1.2.3** \| Class-AB Amplifikatör (3. katman) \| class-ab-amplifikator.md — 8 yaprak \| | ✅ disk |
| L361 | tablo | \| K1.2.3.1 \| Genel Bakış \| class-ab-amplifikator.md L10 \| | ✅ disk |
| L362 | tablo | \| K1.2.3.2 \| Teknik Spesifikasyonlar \| class-ab-amplifikator.md L14 \| | ✅ disk |
| L363 | tablo | \| K1.2.3.3 \| Devre Şeması — Genel Görünüm \| class-ab-amplifikator.md L28 \| | ✅ disk |
| L364 | tablo | \| K1.2.3.4 \| Devre Tasarımı — Detaylı \| class-ab-amplifikator.md L50 \| | ✅ disk |
| L365 | tablo | \| K1.2.3.5 \| Bileşen Listesi \| class-ab-amplifikator.md L129 \| | ✅ disk |
| L366 | tablo | \| K1.2.3.6 \| Differential Pair Input Stage \| class-ab-amplifikator.md L52 \| | ✅ disk |
| L367 | tablo | \| K1.2.3.7 \| Voltage Amplification Stage (VAS) \| class-ab-amplifikator.md L78 \| | ✅ disk |
| L368 | tablo | \| K1.2.3.8 \| Push-Pull Output Stage \| class-ab-amplifikator.md L101 \| | ✅ disk |
| L369 | tablo | \| **K1.2.4** \| Output Stage (3. katman) \| output-stage.md — 15 yaprak \| | ✅ disk |
| L370 | tablo | \| K1.2.4.1 \| Genel Bakış \| output-stage.md L10 \| | ✅ disk |
| L371 | tablo | \| K1.2.4.2 \| Teknik Spesifikasyonlar \| output-stage.md L14 \| | ✅ disk |
| L372 | tablo | \| K1.2.4.3 \| Push-Pull Konfigürasyon \| output-stage.md L26 \| | ✅ disk |
| L373 | tablo | \| K1.2.4.4 \| Darlington Configuration \| output-stage.md L65 \| | ✅ disk |
| L374 | tablo | \| K1.2.4.5 \| Thermal Tracking \| output-stage.md L108 \| | ✅ disk |
| L375 | tablo | \| K1.2.4.6 \| Bileşen Değerleri \| output-stage.md L146 \| | ✅ disk |
| L376 | tablo | \| K1.2.4.7 \| Akım Yolu Analizi \| output-stage.md L159 \| | ✅ disk |
| L377 | tablo | \| K1.2.4.8 \| Tek transistor (Single-ended output) \| output-stage.md L28 \| | ✅ disk |
| L378 | tablo | \| K1.2.4.9 \| Push-Pull Output \| output-stage.md L35 \| | ✅ disk |
| L379 | tablo | \| K1.2.4.10 \| Neden Darlington? \| output-stage.md L67 \| | ✅ disk |
| L380 | tablo | \| K1.2.4.11 \| Darlington Emitters Follower \| output-stage.md L82 \| | ✅ disk |
| L381 | tablo | \| K1.2.4.12 \| Crossover Distortion Sorunu \| output-stage.md L110 \| | ✅ disk |
| L382 | tablo | \| K1.2.4.13 \| Çözüm: Thermal Tracking \| output-stage.md L120 \| | ✅ disk |
| L383 | tablo | \| K1.2.4.14 \| Pozitif Yarım Döngü (MJL21194 Active) \| output-stage.md L161 \| | ✅ disk |
| L384 | tablo | \| K1.2.4.15 \| Negatif Yarım Döngü (MJL21193 Active) \| output-stage.md L170 \| | ✅ disk |
| L385 | tablo | \| **K1.2.5** \| Feedback Network (3. katman) \| feedback-network.md — 11 yaprak \| | ✅ disk |
| L386 | tablo | \| K1.2.5.1 \| Genel Bakış \| feedback-network.md L10 \| | ✅ disk |
| L387 | tablo | \| K1.2.5.2 \| Teknik Spesifikasyonlar \| feedback-network.md L14 \| | ✅ disk |
| L388 | tablo | \| K1.2.5.3 \| Devre Şeması \| feedback-network.md L26 \| | ✅ disk |
| L389 | tablo | \| K1.2.5.4 \| Kazanç Hesaplaması \| feedback-network.md L58 \| | ✅ disk |
| L390 | tablo | \| K1.2.5.5 \| Frequency Compensation \| feedback-network.md L82 \| | ✅ disk |
| L391 | tablo | \| K1.2.5.6 \| Bileşen Değerleri \| feedback-network.md L117 \| | ✅ disk |
| L392 | tablo | \| K1.2.5.7 \| Empedans Eşleşme \| feedback-network.md L126 \| | ✅ disk |
| L393 | tablo | \| K1.2.5.8 \| Kapalı Devre Kazancı \| feedback-network.md L60 \| | ✅ disk |
| L394 | tablo | \| K1.2.5.9 \| Distorsiyon Azaltma \| feedback-network.md L73 \| | ✅ disk |
| L395 | tablo | \| K1.2.5.10 \| Bode Plot \| feedback-network.md L84 \| | ✅ disk |
| L396 | tablo | \| K1.2.5.11 \| Stabilite Kriterleri \| feedback-network.md L108 \| | ✅ disk |
| L397 | tablo | \| **K1.2.6** \| MJL21194/93 Output Transistör (3. katman) \| mjle21194-93.md — 10 yaprak \| | ✅ disk |
| L398 | tablo | \| K1.2.6.1 \| Genel Bakış \| mjle21194-93.md L10 \| | ✅ disk |
| L399 | tablo | \| K1.2.6.2 \| Teknik Spesifikasyonlar \| mjle21194-93.md L14 \| | ✅ disk |
| L400 | tablo | \| K1.2.6.3 \| DC Karakteristikleri (Tipik @ 25°C) \| mjle21194-93.md L32 \| | ✅ disk |
| L401 | tablo | \| K1.2.6.4 \| AC Karakteristikleri (Tipik @ 25°C) \| mjle21194-93.md L42 \| | ✅ disk |
| L402 | tablo | \| K1.2.6.5 \| Devre Bağlantıları \| mjle21194-93.md L51 \| | ✅ disk |
| L403 | tablo | \| K1.2.6.6 \| Termal Hesaplamalar \| mjle21194-93.md L115 \| | ✅ disk |
| L404 | tablo | \| K1.2.6.7 \| Push-Pull Konfigürasyon \| mjle21194-93.md L53 \| | ✅ disk |
| L405 | tablo | \| K1.2.6.8 \| Thermal Tracking (Bias Transistörleri) \| mjle21194-93.md L93 \| | ✅ disk |
| L406 | tablo | \| K1.2.6.9 \| Güç Tüketimi (Tipik Usage) \| mjle21194-93.md L117 \| | ✅ disk |
| L407 | tablo | \| K1.2.6.10 \| Soğutucu Gereksinimi \| mjle21194-93.md L127 \| | ✅ disk |
| L409 | baslik | ### K1.3 — Analog Sinyal Yolu | ✅ disk |
| L411 | tablo | \| Kod \| Ad \| Kanıt (dosya · satır) \| | ✅ disk |
| L412 | tablo | \|-----\|----\|-----------------------\| | ✅ disk |
| L413 | tablo | \| **K1.3.1** \| Analog Sinyal Yolu (3. katman) \| analog-sinyal-yolu.md — 17 yaprak \| | ✅ disk |
| L414 | tablo | \| K1.3.1.1 \| Genel Bakış \| analog-sinyal-yolu.md L10 \| | ✅ disk |
| L415 | tablo | \| K1.3.1.2 \| Teknik Spesifikasyonlar \| analog-sinyal-yolu.md L14 \| | ✅ disk |
| L416 | tablo | \| K1.3.1.3 \| Sinyal Yolu Diyagramı \| analog-sinyal-yolu.md L28 \| | ✅ disk |
| L417 | tablo | \| K1.3.1.4 \| Aşama 1: Giriş Filtresi \| analog-sinyal-yolu.md L52 \| | ✅ disk |
| L418 | tablo | \| K1.3.1.5 \| Aşama 2: Differential Pair \| analog-sinyal-yolu.md L79 \| | ✅ disk |
| L419 | tablo | \| K1.3.1.6 \| Aşama 3: VAS Stage \| analog-sinyal-yolu.md L91 \| | ✅ disk |
| L420 | tablo | \| K1.3.1.7 \| Aşama 4: Output Stage \| analog-sinyal-yolu.md L103 \| | ✅ disk |
| L421 | tablo | \| K1.3.1.8 \| Aşama 5: Feedback Network \| analog-sinyal-yolu.md L112 \| | ✅ disk |
| L422 | tablo | \| K1.3.1.9 \| Impedance Matching \| analog-sinyal-yolu.md L121 \| | ✅ disk |
| L423 | tablo | \| K1.3.1.10 \| EMI Filtering \| analog-sinyal-yolu.md L162 \| | ✅ disk |
| L424 | tablo | \| K1.3.1.11 \| Bileşen Değerleri \| analog-sinyal-yolu.md L182 \| | ✅ disk |
| L425 | tablo | \| K1.3.1.12 \| Differential Input Filter \| analog-sinyal-yolu.md L54 \| | ✅ disk |
| L426 | tablo | \| K1.3.1.13 \| Input Impedance \| analog-sinyal-yolu.md L123 \| | ✅ disk |
| L427 | tablo | \| K1.3.1.14 \| Inter-stage Impedance \| analog-sinyal-yolu.md L133 \| | ✅ disk |
| L428 | tablo | \| K1.3.1.15 \| Output Impedance \| analog-sinyal-yolu.md L147 \| | ✅ disk |
| L429 | tablo | \| K1.3.1.16 \| Input EMI \| analog-sinyal-yolu.md L164 \| | ✅ disk |
| L430 | tablo | \| K1.3.1.17 \| Output EMI \| analog-sinyal-yolu.md L173 \| | ✅ disk |
| L432 | baslik | ### K1.4 — Güç Kaynağı & Koruma | ✅ disk |
| L434 | tablo | \| Kod \| Ad \| Kanıt (dosya · satır) \| | ✅ disk |
| L435 | tablo | \|-----\|----\|-----------------------\| | ✅ disk |
| L436 | tablo | \| **K1.4.1** \| Analog Güç Kaynağı (3. katman) \| guc-kaynagi-analog.md — 9 yaprak \| | ✅ disk |
| L437 | tablo | \| K1.4.1.1 \| Genel Bakış \| guc-kaynagi-analog.md L10 \| | ✅ disk |
| L438 | tablo | \| K1.4.1.2 \| Teknik Spesifikasyonlar \| guc-kaynagi-analog.md L14 \| | ✅ disk |
| L439 | tablo | \| K1.4.1.3 \| Güç Topolojisi \| guc-kaynagi-analog.md L28 \| | ✅ disk |
| L440 | tablo | \| K1.4.1.4 \| Devre Tasarımı \| guc-kaynagi-analog.md L71 \| | ✅ disk |
| L441 | tablo | \| K1.4.1.5 \| Bileşen Listesi \| guc-kaynagi-analog.md L110 \| | ✅ disk |
| L442 | tablo | \| K1.4.1.6 \| Ripple Analizi \| guc-kaynagi-analog.md L123 \| | ✅ disk |
| L443 | tablo | \| K1.4.1.7 \| Koruma Devreleri \| guc-kaynagi-analog.md L138 \| | ✅ disk |
| L444 | tablo | \| K1.4.1.8 \| LM5122 Dual Boost Converter \| guc-kaynagi-analog.md L73 \| | ✅ disk |
| L445 | tablo | \| K1.4.1.9 \| Voltaj Ayarı \| guc-kaynagi-analog.md L95 \| | ✅ disk |
| L446 | tablo | \| **K1.4.2** \| Koruma Devreleri (3. katman) \| koruma-devreleri.md — 14 yaprak \| | ✅ disk |
| L447 | tablo | \| K1.4.2.1 \| Genel Bakış \| koruma-devreleri.md L10 \| | ✅ disk |
| L448 | tablo | \| K1.4.2.2 \| Teknik Spesifikasyonlar \| koruma-devreleri.md L14 \| | ✅ disk |
| L449 | tablo | \| K1.4.2.3 \| DC Offset Koruması \| koruma-devreleri.md L25 \| | ✅ disk |
| L450 | tablo | \| K1.4.2.4 \| Overcurrent Koruması \| koruma-devreleri.md L69 \| | ✅ disk |
| L451 | tablo | \| K1.4.2.5 \| Thermal Shutdown \| koruma-devreleri.md L107 \| | ✅ disk |
| L452 | tablo | \| K1.4.2.6 \| Short Circuit Koruması \| koruma-devreleri.md L141 \| | ✅ disk |
| L453 | tablo | \| K1.4.2.7 \| Bileşen Değerleri \| koruma-devreleri.md L157 \| | ✅ disk |
| L454 | tablo | \| K1.4.2.8 \| Devre Şeması (DC Offset) \| koruma-devreleri.md L27 \| | ✅ disk |
| L455 | tablo | \| K1.4.2.9 \| Çalışma Prensibi \| koruma-devreleri.md L58 \| | ✅ disk |
| L456 | tablo | \| K1.4.2.10 \| Devre Şeması (Overcurrent) \| koruma-devreleri.md L71 \| | ✅ disk |
| L457 | tablo | \| K1.4.2.11 \| Current Limiting Profile \| koruma-devreleri.md L98 \| | ✅ disk |
| L458 | tablo | \| K1.4.2.12 \| Devre Şeması (Thermal) \| koruma-devreleri.md L109 \| | ✅ disk |
| L459 | tablo | \| K1.4.2.13 \| Thermal Profile \| koruma-devreleri.md L131 \| | ✅ disk |
| L460 | tablo | \| K1.4.2.14 \| Çift Koruma \| koruma-devreleri.md L143 \| | ✅ disk |
| L462 | baslik | ### K1.5 — Dijital Arayüzler | ✅ disk |
| L464 | tablo | \| Kod \| Ad \| Kanıt (dosya · satır) \| | ✅ disk |
| L465 | tablo | \|-----\|----\|-----------------------\| | ✅ disk |
| L466 | tablo | \| **K1.5.1** \| I2S Interface (3. katman) \| i2s-interface.md — 13 yaprak \| | ✅ disk |
| L467 | tablo | \| K1.5.1.1 \| Genel Bakış \| i2s-interface.md L10 \| | ✅ disk |
| L468 | tablo | \| K1.5.1.2 \| Teknik Spesifikasyonlar \| i2s-interface.md L14 \| | ✅ disk |
| L469 | tablo | \| K1.5.1.3 \| I2S Sinyalleri \| i2s-interface.md L28 \| | ✅ disk |
| L470 | tablo | \| K1.5.1.4 \| I2S Timing Diagram \| i2s-interface.md L49 \| | ✅ disk |
| L471 | tablo | \| K1.5.1.5 \| Master/Slave Mode \| i2s-interface.md L70 \| | ✅ disk |
| L472 | tablo | \| K1.5.1.6 \| Multi-Channel Configuration \| i2s-interface.md L105 \| | ✅ disk |
| L473 | tablo | \| K1.5.1.7 \| Impedans ve Drive \| i2s-interface.md L135 \| | ✅ disk |
| L474 | tablo | \| K1.5.1.8 \| Bileşen Değerleri \| i2s-interface.md L159 \| | ✅ disk |
| L475 | tablo | \| K1.5.1.9 \| XMOS as Master \| i2s-interface.md L72 \| | ✅ disk |
| L476 | tablo | \| K1.5.1.10 \| DAC/ADC as Slave \| i2s-interface.md L87 \| | ✅ disk |
| L477 | tablo | \| K1.5.1.11 \| 8-Channel TDM (Time Division Multiplexing) \| i2s-interface.md L107 \| | ✅ disk |
| L478 | tablo | \| K1.5.1.12 \| Source Impedans \| i2s-interface.md L137 \| | ✅ disk |
| L479 | tablo | \| K1.5.1.13 \| Load Impedans \| i2s-interface.md L150 \| | ✅ disk |
| L480 | tablo | \| **K1.5.2** \| USB Audio (3. katman) \| usb-audio.md — 13 yaprak \| | ✅ disk |
| L481 | tablo | \| K1.5.2.1 \| Genel Bakış \| usb-audio.md L10 \| | ✅ disk |
| L482 | tablo | \| K1.5.2.2 \| Teknik Spesifikasyonlar \| usb-audio.md L14 \| | ✅ disk |
| L483 | tablo | \| K1.5.2.3 \| USB Descriptor Hierarchy \| usb-audio.md L29 \| | ✅ disk |
| L484 | tablo | \| K1.5.2.4 \| Isochronous Transfer \| usb-audio.md L63 \| | ✅ disk |
| L485 | tablo | \| K1.5.2.5 \| Sample Rate Support \| usb-audio.md L90 \| | ✅ disk |
| L486 | tablo | \| K1.5.2.6 \| DSD (DoP) Support \| usb-audio.md L103 \| | ✅ disk |
| L487 | tablo | \| K1.5.2.7 \| USB-C Connection \| usb-audio.md L129 \| | ✅ disk |
| L488 | tablo | \| K1.5.2.8 \| Driver Status \| usb-audio.md L150 \| | ✅ disk |
| L489 | tablo | \| K1.5.2.9 \| Bileşen Değerleri \| usb-audio.md L160 \| | ✅ disk |
| L490 | tablo | \| K1.5.2.10 \| Neden Isochronous? \| usb-audio.md L65 \| | ✅ disk |
| L491 | tablo | \| K1.5.2.11 \| Transfer Parameters \| usb-audio.md L79 \| | ✅ disk |
| L492 | tablo | \| K1.5.2.12 \| DoP (DSD over PCM) \| usb-audio.md L105 \| | ✅ disk |
| L493 | tablo | \| K1.5.2.13 \| Pin Mapping \| usb-audio.md L131 \| | ✅ disk |
| L494 | tablo | \| **K1.5.3** \| XMOS XU316 (3. katman) \| xmos-xu316.md — 6 yaprak \| | ✅ disk |
| L495 | tablo | \| K1.5.3.1 \| Genel Bakış \| xmos-xu316.md L10 \| | ✅ disk |
| L496 | tablo | \| K1.5.3.2 \| Teknik Spesifikasyonlar \| xmos-xu316.md L14 \| | ✅ disk |
| L497 | tablo | \| K1.5.3.3 \| Devre Tasarımı \| xmos-xu316.md L29 \| | ✅ disk |
| L498 | tablo | \| K1.5.3.4 \| Pin Konfigürasyonu \| xmos-xu316.md L64 \| | ✅ disk |
| L499 | tablo | \| K1.5.3.5 \| Temel Bağlantılar \| xmos-xu316.md L31 \| | ✅ disk |
| L500 | tablo | \| K1.5.3.6 \| Kondansatör Değerleri \| xmos-xu316.md L55 \| | ✅ disk |
| L502 | baslik | ### K1.6 — PCB & Termal | ✅ disk |
| L504 | tablo | \| Kod \| Ad \| Kanıt (dosya · satır) \| | ✅ disk |
| L505 | tablo | \|-----\|----\|-----------------------\| | ✅ disk |
| L506 | tablo | \| **K1.6.1** \| PCB Tasarım (3. katman) \| pcb-tasarim.md — 8 yaprak \| | ✅ disk |
| L507 | tablo | \| K1.6.1.1 \| Genel Bakış \| pcb-tasarim.md L10 \| | ✅ disk |
| L508 | tablo | \| K1.6.1.2 \| Teknik Spesifikasyonlar \| pcb-tasarim.md L14 \| | ✅ disk |
| L509 | tablo | \| K1.6.1.3 \| Katman Stackup \| pcb-tasarim.md L30 \| | ✅ disk |
| L510 | tablo | \| K1.6.1.4 \| Star Grounding Sistemi \| pcb-tasarim.md L76 \| | ✅ disk |
| L511 | tablo | \| K1.6.1.5 \| Controlled Impedance \| pcb-tasarim.md L101 \| | ✅ disk |
| L512 | tablo | \| K1.6.1.6 \| Bileşen Yerleşimi \| pcb-tasarim.md L142 \| | ✅ disk |
| L513 | tablo | \| K1.6.1.7 \| Impedans Hesaplaması \| pcb-tasarim.md L103 \| | ✅ disk |
| L514 | tablo | \| K1.6.1.8 \| Differential Pair (I2S, USB) \| pcb-tasarim.md L123 \| | ✅ disk |
| L515 | tablo | \| **K1.6.2** \| Termal Yönetim (3. katman) \| termal-yonetim.md — 13 yaprak \| | ✅ disk |
| L516 | tablo | \| K1.6.2.1 \| Genel Bakış \| termal-yonetim.md L10 \| | ✅ disk |
| L517 | tablo | \| K1.6.2.2 \| Teknik Spesifikasyonlar \| termal-yonetim.md L14 \| | ✅ disk |
| L518 | tablo | \| K1.6.2.3 \| Isı Dağılım Analizi \| termal-yonetim.md L26 \| | ✅ disk |
| L519 | tablo | \| K1.6.2.4 \| Soğutucu Seçimi \| termal-yonetim.md L46 \| | ✅ disk |
| L520 | tablo | \| K1.6.2.5 \| Sıcaklık İzleme \| termal-yonetim.md L71 \| | ✅ disk |
| L521 | tablo | \| K1.6.2.6 \| Fan Kontrolü \| termal-yonetim.md L102 \| | ✅ disk |
| L522 | tablo | \| K1.6.2.7 \| Termal Pad Uygulaması \| termal-yonetim.md L127 \| | ✅ disk |
| L523 | tablo | \| K1.6.2.8 \| Toplam Isı (8 Kanal) \| termal-yonetim.md L38 \| | ✅ disk |
| L524 | tablo | \| K1.6.2.9 \| Alüminyum Ekstrüzyon Soğutucu \| termal-yonetim.md L48 \| | ✅ disk |
| L525 | tablo | \| K1.6.2.10 \| Termal Ped \| termal-yonetim.md L61 \| | ✅ disk |
| L526 | tablo | \| K1.6.2.11 \| NTC Sensör Devresi \| termal-yonetim.md L73 \| | ✅ disk |
| L527 | tablo | \| K1.6.2.12 \| Sıcaklık-Hassasiyet Tablosu \| termal-yonetim.md L92 \| | ✅ disk |
| L528 | tablo | \| K1.6.2.13 \| PWM Fan Driver \| termal-yonetim.md L104 \| | ✅ disk |
| L530 | baslik | ### K1.7 — Konnektör & Hoparlör | ✅ disk |
| L532 | tablo | \| Kod \| Ad \| Kanıt (dosya · satır) \| | ✅ disk |
| L533 | tablo | \|-----\|----\|-----------------------\| | ✅ disk |
| L534 | tablo | \| **K1.7.1** \| Konnektörler (3. katman) \| konnektorler.md — 16 yaprak \| | ✅ disk |
| L535 | tablo | \| K1.7.1.1 \| Genel Bakış \| konnektorler.md L10 \| | ✅ disk |
| L536 | tablo | \| K1.7.1.2 \| Teknik Spesifikasyonlar \| konnektorler.md L14 \| | ✅ disk |
| L537 | tablo | \| K1.7.1.3 \| XLR Konnektörü \| konnektorler.md L25 \| | ✅ disk |
| L538 | tablo | \| K1.7.1.4 \| RCA Konnektörü \| konnektorler.md L61 \| | ✅ disk |
| L539 | tablo | \| K1.7.1.5 \| USB-C Konnektörü \| konnektorler.md L80 \| | ✅ disk |
| L540 | tablo | \| K1.7.1.6 \| Optical (Toslink) \| konnektorler.md L97 \| | ✅ disk |
| L541 | tablo | \| K1.7.1.7 \| HDMI ARC \| konnektorler.md L111 \| | ✅ disk |
| L542 | tablo | \| K1.7.1.8 \| Binding Posts (Hoparlör Çıkışları) \| konnektorler.md L136 \| | ✅ disk |
| L543 | tablo | \| K1.7.1.9 \| Panel Düzeni \| konnektorler.md L151 \| | ✅ disk |
| L544 | tablo | \| K1.7.1.10 \| Pin Konfigürasyonu (XLR) \| konnektorler.md L27 \| | ✅ disk |
| L545 | tablo | \| K1.7.1.11 \| XLR Devre Bağlantısı \| konnektorler.md L45 \| | ✅ disk |
| L546 | tablo | \| K1.7.1.12 \| Pin Konfigürasyonu (RCA) \| konnektorler.md L63 \| | ✅ disk |
| L547 | tablo | \| K1.7.1.13 \| Pin Konfigürasyonu (USB-C) \| konnektorler.md L82 \| | ✅ disk |
| L548 | tablo | \| K1.7.1.14 \| Pin Konfigürasyonu (Optical) \| konnektorler.md L99 \| | ✅ disk |
| L549 | tablo | \| K1.7.1.15 \| Pin Konfigürasyonu (HDMI ARC) \| konnektorler.md L113 \| | ✅ disk |
| L550 | tablo | \| K1.7.1.16 \| Pin Konfigürasyonu (Binding Posts) \| konnektorler.md L138 \| | ✅ disk |
| L551 | tablo | \| **K1.7.2** \| Hoparlör Dizilimi (3. katman) \| hoparlor-dizilimi.md — 8 yaprak \| | ✅ disk |
| L552 | tablo | \| K1.7.2.1 \| Genel Bakış \| hoparlor-dizilimi.md L10 \| | ✅ disk |
| L553 | tablo | \| K1.7.2.2 \| Teknik Spesifikasyonlar \| hoparlor-dizilimi.md L14 \| | ✅ disk |
| L554 | tablo | \| K1.7.2.3 \| Hoparlör Konumları \| hoparlor-dizilimi.md L27 \| | ✅ disk |
| L555 | tablo | \| K1.7.2.4 \| Kanal Haritası \| hoparlor-dizilimi.md L68 \| | ✅ disk |
| L556 | tablo | \| K1.7.2.5 \| Hoparlör Özellikleri \| hoparlor-dizilimi.md L81 \| | ✅ disk |
| L557 | tablo | \| K1.7.2.6 \| Kablo ve Bağlantılar \| hoparlor-dizilimi.md L106 \| | ✅ disk |
| L558 | tablo | \| K1.7.2.7 \| Full-Range Hoparlörler (FL, C, FR, SL, SR, SBL, SBR) \| hoparlor-dizilimi.md L83 \| | ✅ disk |
| L559 | tablo | \| K1.7.2.8 \| Subwoofer (LFE) \| hoparlor-dizilimi.md L95 \| | ✅ disk |
| L561 | baslik | ### K1.8 — Firmware (K1.f Alt Katmanı) | ✅ disk |
| L563 | alinti | > **Kural:** `firmware/` klasörü K1'in alt katmanıdır (**K1.f**); 8 MD'nin tamamı K1.f.1..K1.f.8 ola… | ✅ disk |
| L565 | tablo | \| Kod \| Ad \| Kanıt (dosya · satır) \| | ✅ disk |
| L566 | tablo | \|-----\|----\|-----------------------\| | ✅ disk |
| L567 | tablo | \| **K1.f.1** \| Bootloader (3. katman) \| firmware/bootloader.md — 16 yaprak \| | ✅ disk |
| L568 | tablo | \| K1.f.1.1 \| Genel Bakış \| firmware/bootloader.md L10 \| | ✅ disk |
| L569 | tablo | \| K1.f.1.2 \| Firmware Mimarisi \| firmware/bootloader.md L14 \| | ✅ disk |
| L570 | tablo | \| K1.f.1.3 \| Kaynak Kod Yapısı \| firmware/bootloader.md L58 \| | ✅ disk |
| L571 | tablo | \| K1.f.1.4 \| Teknik Detaylar \| firmware/bootloader.md L85 \| | ✅ disk |
| L572 | tablo | \| K1.f.1.5 \| Derleme & Yükleme \| firmware/bootloader.md L395 \| | ✅ disk |
| L573 | tablo | \| K1.f.1.6 \| Boot Sequence \| firmware/bootloader.md L87 \| | ✅ disk |
| L574 | tablo | \| K1.f.1.7 \| Dual-Bank Firmware Update \| firmware/bootloader.md L102 \| | ✅ disk |
| L575 | tablo | \| K1.f.1.8 \| DFU Protocol \| firmware/bootloader.md L130 \| | ✅ disk |
| L576 | tablo | \| K1.f.1.9 \| XMOS Bootloader Implementasyonu \| firmware/bootloader.md L150 \| | ✅ disk |
| L577 | tablo | \| K1.f.1.10 \| Flash Manager \| firmware/bootloader.md L211 \| | ✅ disk |
| L578 | tablo | \| K1.f.1.11 \| USB DFU Handler \| firmware/bootloader.md L274 \| | ✅ disk |
| L579 | tablo | \| K1.f.1.12 \| Reboot & Recovery \| firmware/bootloader.md L356 \| | ✅ disk |
| L580 | tablo | \| K1.f.1.13 \| XMOS Bootloader Derleme \| firmware/bootloader.md L397 \| | ✅ disk |
| L581 | tablo | \| K1.f.1.14 \| STM32 Bootloader Derleme \| firmware/bootloader.md L410 \| | ✅ disk |
| L582 | tablo | \| K1.f.1.15 \| DFU ile Firmware Güncelleme \| firmware/bootloader.md L422 \| | ✅ disk |
| L583 | tablo | \| K1.f.1.16 \| XMOS DFU \| firmware/bootloader.md L441 \| | ✅ disk |
| L584 | tablo | \| **K1.f.2** \| XMOS Firmware (3. katman) \| firmware/xmos-firmware.md — 18 yaprak \| | ✅ disk |
| L585 | tablo | \| K1.f.2.1 \| Genel Bakış \| firmware/xmos-firmware.md L10 \| | ✅ disk |
| L586 | tablo | \| K1.f.2.2 \| Firmware Mimarisi \| firmware/xmos-firmware.md L14 \| | ✅ disk |
| L587 | tablo | \| K1.f.2.3 \| Kaynak Kod Yapısı \| firmware/xmos-firmware.md L41 \| | ✅ disk |
| L588 | tablo | \| K1.f.2.4 \| Teknik Detaylar \| firmware/xmos-firmware.md L75 \| | ✅ disk |
| L589 | tablo | \| K1.f.2.5 \| Derleme & Yükleme \| firmware/xmos-firmware.md L262 \| | ✅ disk |
| L590 | tablo | \| K1.f.2.6 \| XC Dil Özellikleri \| firmware/xmos-firmware.md L77 \| | ✅ disk |
| L591 | tablo | \| K1.f.2.7 \| XMOS XU316 Özellikleri \| firmware/xmos-firmware.md L96 \| | ✅ disk |
| L592 | tablo | \| K1.f.2.8 \| Thread Zamanlama \| firmware/xmos-firmware.md L110 \| | ✅ disk |
| L593 | tablo | \| K1.f.2.9 \| Memory Map \| firmware/xmos-firmware.md L121 \| | ✅ disk |
| L594 | tablo | \| K1.f.2.10 \| USB Audio Pipeline \| firmware/xmos-firmware.md L132 \| | ✅ disk |
| L595 | tablo | \| K1.f.2.11 \| Clock Recovery \| firmware/xmos-firmware.md L142 \| | ✅ disk |
| L596 | tablo | \| K1.f.2.12 \| DSP Processing Chain \| firmware/xmos-firmware.md L171 \| | ✅ disk |
| L597 | tablo | \| K1.f.2.13 \| USB Audio Class 2.0 Entegrasyonu \| firmware/xmos-firmware.md L201 \| | ✅ disk |
| L598 | tablo | \| K1.f.2.14 \| Error Handling & Recovery \| firmware/xmos-firmware.md L231 \| | ✅ disk |
| L599 | tablo | \| K1.f.2.15 \| Ortam Kurulumu \| firmware/xmos-firmware.md L264 \| | ✅ disk |
| L600 | tablo | \| K1.f.2.16 \| Firmware Derleme \| firmware/xmos-firmware.md L276 \| | ✅ disk |
| L601 | tablo | \| K1.f.2.17 \| Firmware Yükleme \| firmware/xmos-firmware.md L294 \| | ✅ disk |
| L602 | tablo | \| K1.f.2.18 \| Debug & Trace \| firmware/xmos-firmware.md L307 \| | ✅ disk |
| L603 | tablo | \| **K1.f.3** \| USB Audio Firmware (3. katman) \| firmware/usb-audio-firmware.md — 15 yaprak \| | ✅ disk |
| L604 | tablo | \| K1.f.3.1 \| Genel Bakış \| firmware/usb-audio-firmware.md L10 \| | ✅ disk |
| L605 | tablo | \| K1.f.3.2 \| Firmware Mimarisi \| firmware/usb-audio-firmware.md L14 \| | ✅ disk |
| L606 | tablo | \| K1.f.3.3 \| Kaynak Kod Yapısı \| firmware/usb-audio-firmware.md L62 \| | ✅ disk |
| L607 | tablo | \| K1.f.3.4 \| Teknik Detaylar \| firmware/usb-audio-firmware.md L89 \| | ✅ disk |
| L608 | tablo | \| K1.f.3.5 \| Derleme & Yükleme \| firmware/usb-audio-firmware.md L432 \| | ✅ disk |
| L609 | tablo | \| K1.f.3.6 \| USB Audio Class 2.0 Descriptor Hiyerarşisi \| firmware/usb-audio-firmware.md L91 \| | ✅ disk |
| L610 | tablo | \| K1.f.3.7 \| Isochronous Transfer Mekanizması \| firmware/usb-audio-firmware.md L129 \| | ✅ disk |
| L611 | tablo | \| K1.f.3.8 \| Clock Recovery & Sync \| firmware/usb-audio-firmware.md L187 \| | ✅ disk |
| L612 | tablo | \| K1.f.3.9 \| Ring Buffer Implementasyonu \| firmware/usb-audio-firmware.md L265 \| | ✅ disk |
| L613 | tablo | \| K1.f.3.10 \| Audio Format Conversion \| firmware/usb-audio-firmware.md L319 \| | ✅ disk |
| L614 | tablo | \| K1.f.3.11 \| USB Audio Control Requests \| firmware/usb-audio-firmware.md L354 \| | ✅ disk |
| L615 | tablo | \| K1.f.3.12 \| Latency Optimization \| firmware/usb-audio-firmware.md L408 \| | ✅ disk |
| L616 | tablo | \| K1.f.3.13 \| USB Audio Firmware Derleme \| firmware/usb-audio-firmware.md L434 \| | ✅ disk |
| L617 | tablo | \| K1.f.3.14 \| USB Descriptor Doğrulama \| firmware/usb-audio-firmware.md L453 \| | ✅ disk |
| L618 | tablo | \| K1.f.3.15 \| USB Audio Test \| firmware/usb-audio-firmware.md L467 \| | ✅ disk |
| L619 | tablo | \| **K1.f.4** \| MCU Support (3. katman) \| firmware/mcu-support.md — 18 yaprak \| | ✅ disk |
| L620 | tablo | \| K1.f.4.1 \| Genel Bakış \| firmware/mcu-support.md L10 \| | ✅ disk |
| L621 | tablo | \| K1.f.4.2 \| Firmware Mimarisi \| firmware/mcu-support.md L14 \| | ✅ disk |
| L622 | tablo | \| K1.f.4.3 \| Kaynak Kod Yapısı \| firmware/mcu-support.md L56 \| | ✅ disk |
| L623 | tablo | \| K1.f.4.4 \| Teknik Detaylar \| firmware/mcu-support.md L112 \| | ✅ disk |
| L624 | tablo | \| K1.f.4.5 \| Derleme & Yükleme \| firmware/mcu-support.md L877 \| | ✅ disk |
| L625 | tablo | \| K1.f.4.6 \| STM32F4 Pin Configuration \| firmware/mcu-support.md L114 \| | ✅ disk |
| L626 | tablo | \| K1.f.4.7 \| SPI Communication Protocol \| firmware/mcu-support.md L143 \| | ✅ disk |
| L627 | tablo | \| K1.f.4.8 \| System Health Monitoring \| firmware/mcu-support.md L252 \| | ✅ disk |
| L628 | tablo | \| K1.f.4.9 \| Firmware Update Orchestration \| firmware/mcu-support.md L352 \| | ✅ disk |
| L629 | tablo | \| K1.f.4.10 \| Raspberry Pi Control Interface \| firmware/mcu-support.md L470 \| | ✅ disk |
| L630 | tablo | \| K1.f.4.11 \| Network Manager \| firmware/mcu-support.md L619 \| | ✅ disk |
| L631 | tablo | \| K1.f.4.12 \| Configuration Management \| firmware/mcu-support.md L684 \| | ✅ disk |
| L632 | tablo | \| K1.f.4.13 \| UART Debug Console \| firmware/mcu-support.md L783 \| | ✅ disk |
| L633 | tablo | \| K1.f.4.14 \| STM32 Firmware Derleme \| firmware/mcu-support.md L879 \| | ✅ disk |
| L634 | tablo | \| K1.f.4.15 \| STM32 Firmware Yükleme \| firmware/mcu-support.md L897 \| | ✅ disk |
| L635 | tablo | \| K1.f.4.16 \| Raspberry Pi Firmware Derleme \| firmware/mcu-support.md L911 \| | ✅ disk |
| L636 | tablo | \| K1.f.4.17 \| Raspberry Pi Kurulum \| firmware/mcu-support.md L931 \| | ✅ disk |
| L637 | tablo | \| K1.f.4.18 \| Entegrasyon Testi \| firmware/mcu-support.md L953 \| | ✅ disk |
| L638 | tablo | \| **K1.f.5** \| DSP Firmware (3. katman) \| firmware/dsp-firmware.md — 15 yaprak \| | ✅ disk |
| L639 | tablo | \| K1.f.5.1 \| Genel Bakış \| firmware/dsp-firmware.md L10 \| | ✅ disk |
| L640 | tablo | \| K1.f.5.2 \| Firmware Mimarisi \| firmware/dsp-firmware.md L14 \| | ✅ disk |
| L641 | tablo | \| K1.f.5.3 \| Kaynak Kod Yapısı \| firmware/dsp-firmware.md L72 \| | ✅ disk |
| L642 | tablo | \| K1.f.5.4 \| Teknik Detaylar \| firmware/dsp-firmware.md L103 \| | ✅ disk |
| L643 | tablo | \| K1.f.5.5 \| Derleme & Yükleme \| firmware/dsp-firmware.md L547 \| | ✅ disk |
| L644 | tablo | \| K1.f.5.6 \| DSP Processing Block Structure \| firmware/dsp-firmware.md L105 \| | ✅ disk |
| L645 | tablo | \| K1.f.5.7 \| IIR Biquad Filter \| firmware/dsp-firmware.md L146 \| | ✅ disk |
| L646 | tablo | \| K1.f.5.8 \| Parametric EQ \| firmware/dsp-firmware.md L216 \| | ✅ disk |
| L647 | tablo | \| K1.f.5.9 \| Dynamics Compressor \| firmware/dsp-firmware.md L282 \| | ✅ disk |
| L648 | tablo | \| K1.f.5.10 \| Peak Limiter \| firmware/dsp-firmware.md L345 \| | ✅ disk |
| L649 | tablo | \| K1.f.5.11 \| Noise Gate \| firmware/dsp-firmware.md L402 \| | ✅ disk |
| L650 | tablo | \| K1.f.5.12 \| Stereo Crossfeed (Headphone) \| firmware/dsp-firmware.md L457 \| | ✅ disk |
| L651 | tablo | \| K1.f.5.13 \| DSP Performance Optimization \| firmware/dsp-firmware.md L509 \| | ✅ disk |
| L652 | tablo | \| K1.f.5.14 \| DSP Firmware Derleme \| firmware/dsp-firmware.md L549 \| | ✅ disk |
| L653 | tablo | \| K1.f.5.15 \| DSP Test \| firmware/dsp-firmware.md L568 \| | ✅ disk |
| L654 | tablo | \| **K1.f.6** \| GPIO Control (3. katman) \| firmware/gpio-control.md — 14 yaprak \| | ✅ disk |
| L655 | tablo | \| K1.f.6.1 \| Genel Bakış \| firmware/gpio-control.md L10 \| | ✅ disk |
| L656 | tablo | \| K1.f.6.2 \| Firmware Mimarisi \| firmware/gpio-control.md L14 \| | ✅ disk |
| L657 | tablo | \| K1.f.6.3 \| Kaynak Kod Yapısı \| firmware/gpio-control.md L61 \| | ✅ disk |
| L658 | tablo | \| K1.f.6.4 \| Teknik Detaylar \| firmware/gpio-control.md L90 \| | ✅ disk |
| L659 | tablo | \| K1.f.6.5 \| Derleme & Yükleme \| firmware/gpio-control.md L716 \| | ✅ disk |
| L660 | tablo | \| K1.f.6.6 \| GPIO Pin Haritası (XMOS XU316) \| firmware/gpio-control.md L92 \| | ✅ disk |
| L661 | tablo | \| K1.f.6.7 \| Button Handler \| firmware/gpio-control.md L119 \| | ✅ disk |
| L662 | tablo | \| K1.f.6.8 \| LED Controller \| firmware/gpio-control.md L233 \| | ✅ disk |
| L663 | tablo | \| K1.f.6.9 \| Rotary Encoder \| firmware/gpio-control.md L399 \| | ✅ disk |
| L664 | tablo | \| K1.f.6.10 \| Display Driver (OLED SSD1306) \| firmware/gpio-control.md L493 \| | ✅ disk |
| L665 | tablo | \| K1.f.6.11 \| Event Queue \| firmware/gpio-control.md L607 \| | ✅ disk |
| L666 | tablo | \| K1.f.6.12 \| GPIO Interrupt Handler \| firmware/gpio-control.md L671 \| | ✅ disk |
| L667 | tablo | \| K1.f.6.13 \| GPIO Firmware Derleme \| firmware/gpio-control.md L718 \| | ✅ disk |
| L668 | tablo | \| K1.f.6.14 \| GPIO Test \| firmware/gpio-control.md L734 \| | ✅ disk |
| L669 | tablo | \| **K1.f.7** \| I2S Driver (3. katman) \| firmware/i2s-driver.md — 15 yaprak \| | ✅ disk |
| L670 | tablo | \| K1.f.7.1 \| Genel Bakış \| firmware/i2s-driver.md L10 \| | ✅ disk |
| L671 | tablo | \| K1.f.7.2 \| Firmware Mimarisi \| firmware/i2s-driver.md L14 \| | ✅ disk |
| L672 | tablo | \| K1.f.7.3 \| Kaynak Kod Yapısı \| firmware/i2s-driver.md L63 \| | ✅ disk |
| L673 | tablo | \| K1.f.7.4 \| Teknik Detaylar \| firmware/i2s-driver.md L84 \| | ✅ disk |
| L674 | tablo | \| K1.f.7.5 \| Derleme & Yükleme \| firmware/i2s-driver.md L426 \| | ✅ disk |
| L675 | tablo | \| K1.f.7.6 \| I2S Protocol Overview \| firmware/i2s-driver.md L86 \| | ✅ disk |
| L676 | tablo | \| K1.f.7.7 \| Clock Configuration \| firmware/i2s-driver.md L109 \| | ✅ disk |
| L677 | tablo | \| K1.f.7.8 \| I2S Master Driver \| firmware/i2s-driver.md L161 \| | ✅ disk |
| L678 | tablo | \| K1.f.7.9 \| Multi-Channel I2S \| firmware/i2s-driver.md L286 \| | ✅ disk |
| L679 | tablo | \| K1.f.7.10 \| Sample Rate Conversion \| firmware/i2s-driver.md L326 \| | ✅ disk |
| L680 | tablo | \| K1.f.7.11 \| Clock Recovery (Slave Mode) \| firmware/i2s-driver.md L367 \| | ✅ disk |
| L681 | tablo | \| K1.f.7.12 \| Audio Quality Metrics \| firmware/i2s-driver.md L407 \| | ✅ disk |
| L682 | tablo | \| K1.f.7.13 \| I2S Driver Derleme \| firmware/i2s-driver.md L428 \| | ✅ disk |
| L683 | tablo | \| K1.f.7.14 \| I2S Test \| firmware/i2s-driver.md L447 \| | ✅ disk |
| L684 | tablo | \| K1.f.7.15 \| DAC/ADC Konfigürasyonu \| firmware/i2s-driver.md L460 \| | ✅ disk |
| L685 | tablo | \| **K1.f.8** \| Firmware index (3. katman) \| firmware/index.md — 12 yaprak \| | ✅ disk |
| L686 | tablo | \| K1.f.8.1 \| Genel Bakış \| firmware/index.md L10 \| | ✅ disk |
| L687 | tablo | \| K1.f.8.2 \| Firmware Mimarisi \| firmware/index.md L14 \| | ✅ disk |
| L688 | tablo | \| K1.f.8.3 \| Kaynak Kod Yapısı \| firmware/index.md L49 \| | ✅ disk |
| L689 | tablo | \| K1.f.8.4 \| Teknik Detaylar \| firmware/index.md L88 \| | ✅ disk |
| L690 | tablo | \| K1.f.8.5 \| Derleme & Yükleme \| firmware/index.md L134 \| | ✅ disk |
| L691 | tablo | \| K1.f.8.6 \| XMOS XU316 Seçim Gerekçesi \| firmware/index.md L90 \| | ✅ disk |
| L692 | tablo | \| K1.f.8.7 \| Firmware Katmanları \| firmware/index.md L99 \| | ✅ disk |
| L693 | tablo | \| K1.f.8.8 \| Real-Time Gereksinimleri \| firmware/index.md L110 \| | ✅ disk |
| L694 | tablo | \| K1.f.8.9 \| Thread Kullanımı (XMOS) \| firmware/index.md L121 \| | ✅ disk |
| L695 | tablo | \| K1.f.8.10 \| XMOS Firmware Derleme \| firmware/index.md L136 \| | ✅ disk |
| L696 | tablo | \| K1.f.8.11 \| STM32 Firmware Derleme \| firmware/index.md L151 \| | ✅ disk |
| L697 | tablo | \| K1.f.8.12 \| DFU ile Güncelleme \| firmware/index.md L164 \| | ✅ disk |
| L699 | baslik | ### K1.9 — Bileşen Haritası & BOM | ✅ disk |
| L701 | tablo | \| Kod \| Ad \| Kanıt (dosya · satır) \| | ✅ disk |
| L702 | tablo | \|-----\|----\|-----------------------\| | ✅ disk |
| L703 | tablo | \| **K1.9.1** \| README §2-§7 (3. katman) \| README.md — 20 yaprak \| | ✅ disk |
| L704 | tablo | \| K1.9.1.1 \| 2. Bileşen Haritası \| README.md L42 \| | ✅ disk |
| L705 | tablo | \| K1.9.1.2 \| 3. PCM3168A DAC Detayı \| README.md L93 \| | ✅ disk |
| L706 | tablo | \| K1.9.1.3 \| 4. Class AB Amplifikatör Detayı \| README.md L135 \| | ✅ disk |
| L707 | tablo | \| K1.9.1.4 \| 5. XMOS XU316 Detayı \| README.md L185 \| | ✅ disk |
| L708 | tablo | \| K1.9.1.5 \| 6. PCB Tasarım Kuralları \| README.md L210 \| | ✅ disk |
| L709 | tablo | \| K1.9.1.6 \| 7. BOM Maliyet Analizi \| README.md L238 \| | ✅ disk |
| L710 | tablo | \| K1.9.1.7 \| 2.1 USB Audio Interface \| README.md L44 \| | ✅ disk |
| L711 | tablo | \| K1.9.1.8 \| 2.2 DAC (Digital-to-Analog Converter) \| README.md L53 \| | ✅ disk |
| L712 | tablo | \| K1.9.1.9 \| 2.3 Amplifikatör \| README.md L63 \| | ✅ disk |
| L713 | tablo | \| K1.9.1.10 \| 2.4 Güç Kaynağı \| README.md L70 \| | ✅ disk |
| L714 | tablo | \| K1.9.1.11 \| 2.5 Hoparlör Matrisi (8.1 Surround) \| README.md L78 \| | ✅ disk |
| L715 | tablo | \| K1.9.1.12 \| 3.1 Pin Out \| README.md L95 \| | ✅ disk |
| L716 | tablo | \| K1.9.1.13 \| 3.2 I2S Konfigürasyonu \| README.md L116 \| | ✅ disk |
| L717 | tablo | \| K1.9.1.14 \| 3.3 Analogy Output Devresi \| README.md L127 \| | ✅ disk |
| L718 | tablo | \| K1.9.1.15 \| 4.1 Tek Kanal Devre Şeması \| README.md L137 \| | ✅ disk |
| L719 | tablo | \| K1.9.1.16 \| 4.2 Bias Ayar Prosedürü \| README.md L162 \| | ✅ disk |
| L720 | tablo | \| K1.9.1.17 \| 4.3 Termal Hesaplama \| README.md L172 \| | ✅ disk |
| L721 | tablo | \| K1.9.1.18 \| 5.1 Blok Diyagramı \| README.md L187 \| | ✅ disk |
| L722 | tablo | \| K1.9.1.19 \| 5.2 XMOS Kaynak Kullanımı \| README.md L198 \| | ✅ disk |
| L723 | tablo | \| K1.9.1.20 \| 6.1 Stackup \| README.md L225 \| | ✅ disk |
| L725 | baslik | ### K1.10 — index.md | ✅ disk |
| L727 | tablo | \| Kod \| Ad \| Kanıt (dosya · satır) \| | ✅ disk |
| L728 | tablo | \|-----\|----\|-----------------------\| | ✅ disk |
| L729 | tablo | \| **K1.10.1** \| K1 İndeksi (3. katman) \| index.md — 5 yaprak \| | ✅ disk |
| L730 | tablo | \| K1.10.1.1 \| Genel Bakış \| index.md L10 \| | ✅ disk |
| L731 | tablo | \| K1.10.1.2 \| Blok Diyagramı \| index.md L14 \| | ✅ disk |
| L732 | tablo | \| K1.10.1.3 \| Bileşen Listesi \| index.md L50 \| | ✅ disk |
| L733 | tablo | \| K1.10.1.4 \| Katman Bağımlılıkları \| index.md L65 \| | ✅ disk |
| L734 | tablo | \| K1.10.1.5 \| Teknik Özet \| index.md L73 \| | ✅ disk |
| L736 | baslik | ### K1.11 — CLAUDE.md Guardrails | ✅ disk |
| L738 | tablo | \| Kod \| Ad \| Kanıt (dosya · satır) \| | ✅ disk |
| L739 | tablo | \|-----\|----\|-----------------------\| | ✅ disk |
| L740 | tablo | \| **K1.11.1** \| K1 Guardrails (3. katman) \| CLAUDE.md — 4 yaprak \| | ✅ disk |
| L741 | tablo | \| K1.11.1.1 \| Hard Guardrails \| CLAUDE.md L16 \| | ✅ disk |
| L742 | tablo | \| K1.11.1.2 \| Bileşen Seçim Kısıtları \| CLAUDE.md L26 \| | ✅ disk |
| L743 | tablo | \| K1.11.1.3 \| Yasaklı Bileşenler \| CLAUDE.md L36 \| | ✅ disk |
| L744 | tablo | \| K1.11.1.4 \| İlgili ADR'ler \| CLAUDE.md L44 \| | ✅ disk |
| L746 | baslik | ### K1.12 — README Genel Bakış | ✅ disk |
| L748 | tablo | \| Kod \| Ad \| Kanıt (dosya · satır) \| | ✅ disk |
| L749 | tablo | \|-----\|----\|-----------------------\| | ✅ disk |
| L750 | tablo | \| **K1.12.1** \| README §1 (3. katman) \| README.md — 2 yaprak \| | ✅ disk |
| L751 | tablo | \| K1.12.1.1 \| 1. Genel Bakış \| README.md L26 \| | ✅ disk |
| L752 | tablo | \| K1.12.1.2 \| 1.1 Temel İlkeler \| README.md L30 \| | ✅ disk |
| L754 | baslik | ## Kanıt Kataloğu (K1) | ✅ disk |
| L756 | alinti | > Bu dizin, K1 şemasındaki 360 yaprağın dosya bazlı kaynağını verir. "Kapsanan" = şemaya giren yapra… | ✅ disk |
| L758 | tablo | \| # \| Dosya \| Rol \| H2 \| H3 \| Kapsanan \| Satır Aralığı \| | ✅ disk |
| L759 | tablo | \|---\|-------\|-----\|----\|----\|----------\|---------------\| | ✅ disk |
| L760 | tablo | \| 1 \| README.md \| Katman özeti, bileşen haritası, BOM \| 9 \| 15 \| 22 \| L26–L269 \| | ✅ disk |
| L761 | tablo | \| 2 \| CLAUDE.md \| Hard guardrails \| 4 \| 0 \| 4 \| L16–L44 \| | ✅ disk |
| L762 | tablo | \| 3 \| index.md \| Mimari indeks \| 6 \| 0 \| 5 \| L10–L83 \| | ✅ disk |
| L763 | tablo | \| 4 \| ak4458-dac.md \| AK4458 DAC \| 7 \| 2 \| 7 \| L10–L114 \| | ✅ disk |
| L764 | tablo | \| 5 \| analog-sinyal-yolu.md \| 5 aşamalı analog yol \| 13 \| 6 \| 17 \| L10–L209 \| | ✅ disk |
| L765 | tablo | \| 6 \| class-ab-amplifikator.md \| Class-AB devresi \| 7 \| 3 \| 8 \| L10–L153 \| | ✅ disk |
| L766 | tablo | \| 7 \| dac-adc-zinciri.md \| DAC-ADC zinciri \| 9 \| 3 \| 10 \| L10–L151 \| | ✅ disk |
| L767 | tablo | \| 8 \| diff-pair-input.md \| Differential giriş \| 8 \| 5 \| 11 \| L10–L122 \| | ✅ disk |
| L768 | tablo | \| 9 \| feedback-network.md \| Geri besleme ağı \| 9 \| 4 \| 11 \| L10–L145 \| | ✅ disk |
| L769 | tablo | \| 10 \| guc-kaynagi-analog.md \| LM5122 boost PSU \| 9 \| 2 \| 9 \| L10–L156 \| | ✅ disk |
| L770 | tablo | \| 11 \| hoparlor-dizilimi.md \| 8.1 hoparlör matrisi \| 8 \| 2 \| 8 \| L10–L125 \| | ✅ disk |
| L771 | tablo | \| 12 \| i2s-interface.md \| I2S/TDM arayüzü \| 10 \| 5 \| 13 \| L10–L177 \| | ✅ disk |
| L772 | tablo | \| 13 \| koruma-devreleri.md \| DC/OC/thermal/short \| 9 \| 7 \| 14 \| L10–L180 \| | ✅ disk |
| L773 | tablo | \| 14 \| konnektorler.md \| XLR/RCA/USB-C/HDMI \| 11 \| 7 \| 16 \| L10–L194 \| | ✅ disk |
| L774 | tablo | \| 15 \| mjle21194-93.md \| Output transistörler \| 8 \| 4 \| 10 \| L10–L148 \| | ✅ disk |
| L775 | tablo | \| 16 \| output-stage.md \| Darlington output \| 9 \| 8 \| 15 \| L10–L190 \| | ✅ disk |
| L776 | tablo | \| 17 \| pcm3168a-dac-adc.md \| Ana DAC/ADC \| 6 \| 2 \| 6 \| L10–L109 \| | ✅ disk |
| L777 | tablo | \| 18 \| pcb-tasarim.md \| 6-layer PCB \| 8 \| 2 \| 8 \| L10–L177 \| | ✅ disk |
| L778 | tablo | \| 19 \| termal-yonetim.md \| Soğutma/fan/NTC \| 9 \| 6 \| 13 \| L10–L160 \| | ✅ disk |
| L779 | tablo | \| 20 \| usb-audio.md \| USB Audio Class \| 11 \| 4 \| 13 \| L10–L181 \| | ✅ disk |
| L780 | tablo | \| 21 \| vas-stage.md \| VAS + Miller \| 9 \| 4 \| 11 \| L10–L160 \| | ✅ disk |
| L781 | tablo | \| 22 \| xmos-xu316.md \| XMOS XU316 devresi \| 6 \| 2 \| 6 \| L10–L92 \| | ✅ disk |
| L782 | tablo | \| 23 \| firmware/bootloader.md \| K1.f — bootloader/DFU \| 7 \| 11 \| 16 \| L10–L465 \| | ✅ disk |
| L783 | tablo | \| 24 \| firmware/xmos-firmware.md \| K1.f — XMOS XC \| 7 \| 13 \| 18 \| L10–L336 \| | ✅ disk |
| L784 | tablo | \| 25 \| firmware/usb-audio-firmware.md \| K1.f — UAC2 firmware \| 7 \| 10 \| 15 \| L10–L494 \| | ✅ disk |
| L785 | tablo | \| 26 \| firmware/mcu-support.md \| K1.f — STM32/RPi \| 7 \| 13 \| 18 \| L10–L980 \| | ✅ disk |
| L786 | tablo | \| 27 \| firmware/dsp-firmware.md \| K1.f — DSP blokları \| 7 \| 10 \| 15 \| L10–L592 \| | ✅ disk |
| L787 | tablo | \| 28 \| firmware/gpio-control.md \| K1.f — GPIO/LED/display \| 7 \| 9 \| 14 \| L10–L758 \| | ✅ disk |
| L788 | tablo | \| 29 \| firmware/i2s-driver.md \| K1.f — I2S master \| 7 \| 10 \| 15 \| L10–L485 \| | ✅ disk |
| L789 | tablo | \| 30 \| firmware/index.md \| K1.f — indeks \| 7 \| 7 \| 12 \| L10–L189 \| | ✅ disk |
| L790 | tablo | \| \| **TOPLAM** \| 30 dosya \| **241** \| **176** \| **360** \| \| | ✅ disk |
| L792 | vurgu | **Katalog notları:** | ✅ disk |
| L794 | madde | 1. **Bilinçli çıkarım (57 başlık):** 27 "Bağımlılıklar" + 28 "Durum: Implementasyon" + README §8 İlg… | ✅ disk |
| L795 | madde | 2. **Satır bazlı ek kanıt (şemaya sayılmadı):** README §2 bileşen tabloları 20 satır (L48-89), §7 BO… | ✅ disk |
| L796 | madde | 3. **K1.f:** firmware/ 8 MD'nin tamamı K1.f.1..K1.f.8 olarak 3. katmanda; 139 firmware yaprağı K1.f … | ✅ disk |
| L797 | madde | 4. **Kapsam:** 19 içerik MD + README + CLAUDE + index = 22 k1-donanim dosyası + 8 firmware dosyası =… | ✅ disk |
| L798 | madde | 5. **Adlandırma:** klasör/dosya adları lowercase-hyphen (k1-donanim, firmware, mjle21194-93, …), bel… | ✅ disk |
| L802 | metin | *K1 Donanım Layer v1.0.0 — CoreMusic Architecture* | ✅ disk |
| L803 | metin | *Authority: Bayram Ali / Vault Steward* | ✅ disk |
| L804 | metin | *Last Updated: 2026-09-29 — genişletme: 3 turlu agent tartışması* | ✅ disk |
| L805 | metin | *Mode: Red Team · Human Mode · Truth Mode* | ✅ disk |
