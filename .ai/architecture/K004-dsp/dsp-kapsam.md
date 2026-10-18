---
node_id: K004-dsp/dsp-kapsam
node_type: subdomain
status: draft
parent_id: K004-dsp
children: []
depends_on: [K003-audio, K000-isletim-sistemi, K001-donanım, K005-firmware]
evidence:
  - ".ai/architecture/K004-dsp/dsp-master.md §A.1-§A.7 (ELI15, 12 çocuk, tier-3) · 2026-10-18"
  - ".ai/architecture/K004-dsp/index.md §4 (köK 12 dosya listesi — bu dosya o listenin üyesi) · 2026-10-18"
  - ".ai/.decisions/accepted/ADR-017-dsp-hardware-mode.md (zincir host'ta; XMOS yalnız hard-RT + I2S/TDM — Q5 kapandı 2026-10-15) · 2026-10-18"
validation: "12 çocuk v2 §2.4 ve disk klasör sayısıyla birebir; kapsam dışı sahipleri master §A.6'dan alınır; 'gerçek zamanlı' iddiası latency bütçesi UNKNOWN iken yazılmaz (T8)."
open_questions:
  - "⚠️ Latency bütçesi (T8) UNKNOWN — 'gerçek zamanlı' iddiası bu sayı gelene kadar yazılmaz; Q1-Q4 açık (bkz. dsp-kararlar.md §3)."
  - "⚠️ Q3 (dynamics ↔ compressor/limiter hiyerarşi mi kardeş mi?) çözülmediği için kapsam tekrarı riski R1 açık."
  - "⚠️ Q4 (measurement ↔ testing ayrımı) çözülmediği için iki çocuk arasındaki sorumluluk sınırı çizgisi Vault Steward kararı bekliyor."
group: mimari
---

# K004-dsp — Kapsam (Kök · Kademe-1)

> **Node:** `K004-dsp/dsp-kapsam` · **Type:** subdomain · **Status:** draft · **Group:** mimari
> **Üst düğüm:** `K004-dsp` (domain) · **Disk durumu:** depoda C++/DSP motoru **YOKTUR**
> (master §C.7: 0 satır) — bu dosya şartnamedir, çalışan kod değildir.

## §1 ELI15

**Sesin şekillendirildiği yer — bass, tiz, ekolayzır, oda hissi.**

K004, sesi **değiştiren** katmandır: ham akış K003'ten gelir, burada işlemden geçer
ve tekrar akışa döner. ELI15 benzetme: K004 bir stüdyo miksaj masasıdır — fader'lar,
ekolayzır, kompresör ve oda simülasyonu üstüne oturur; masanın kendisi müziği
üretmez, yalnızca duyulanı şekillendirir (master §A.1).

İki temel özellik:

1. **Graf düğümü olarak yaşar.** Kendi thread'i / clock'ı iddia etmez; K003'ün
   realtime döngüsüne bir işleyici (node) olarak eklenir (master §A.6 sınır kuralı).
2. **Durum taşır.** Filtre bellekleri, kompresör envelope'u, reverb geçmişi =
   örnek başına güncellenen durum → sıfırlama/başlatma semantiği `pipeline`
   çocuğunun sorumluluğudur (master §A.1).

**Hesap nerede? (Q5 KAPANDI 2026-10-15 — ADR-017):** Zincir hesabı **HOST'ta**
(NevaEngine/JUCE, K003 üstünde K004 düğümü). XMOS XU316 yalnız **hard-RT zamanlama
+ I2S/TDM veri yolu** taşır — DSP algoritması donanım içinde DEĞİLDİR.

## §2 Kapsam İçi (5 grup — master §A.6 birebir)

| # | Grup | Çocuklar | Kapsam cümlesi |
|---|---|---|---|
| 1 | pipeline | pipeline | İşlem basamaklarının sırası ve akış grafiği |
| 2 | frekans | equalizer · filters · crossover | Frekans bantlarına göre seviye/filtre/ayrım |
| 3 | dinamik | dynamics · compressor · limiter | Eşik üstü seviye kontrolü ve güvenlik |
| 4 | uzamsal | reverb · delay · spatial-audio | Yansıma, gecikme ve konum hissi |
| 5 | ölçüm | measurement · testing | Tepki ölçümü ve bozulma kontrolü |

**Toplam: 12 Kademe-2 çocuk** (v2 §2.4 birebir; diskte 12 klasör doğrulandı —
2026-10-13). Sayım notu master §A.2'de: `index.md` §9.6'daki "13 alt-modül" ifadesi
kök üç dosyanın ayrı öğe sayılmasından gelir; **çocuk sayısı 12'dir**.

## §3 Kapsam Dışı (sahibiyle birlikte — master §A.6)

| # | Konu | Sahibi | Gerekçe |
|---|---|---|---|
| 1 | Sesin taşınması, cihaz seçimi, zamanlama | `K003-audio` | Taşıyıcı ayrı alan |
| 2 | DAC/ADC, amplifikatör devresi, PCB | `K001-donanım` | Fiziksel tasarım |
| 3 | Cihaz içindeki kontrol/komut yazılımı | `K005-firmware` | Donanım içi yazılım |
| 4 | OS kaynağı, sürücü, thread yönetimi | `K000-isletim-sistemi` | Zemin katmanı |
| 5 | Kullanıcının EQ preset arayüzü | uygulama katmanı | K004 motoru, UI'ı bilmez |
| 6 | Ses kaynağı metadata (ffmpeg) / BI ölçüm | `K007-data` | Veri katmanı (registry: K002-data yazımı imla-korunur — master §A.6) |
| 7 | Elektronik devre tasarımı | `K002-electroncis` | Devre ≠ algoritma |

**Sınır kuralı:** K004, K003'ün realtime döngüsüne **graf düğümü olarak** eklenir;
kendi başına ayrı bir zaman çizgisi (thread/clock) iddia etmez. İddia ederse bu
K003 ile çakışma → risk satırı olur, sessizce çözülmez (master §A.6).

## §4 Bağımlılık Yönü (A1 dizisi)

| # | Yön | Bağımlılık | Tür | Etiket |
|---|---|---|---|---|
| 1 | K004 → K003-audio | Akış kaynağı ve graph'a ekleme noktası | çalışma zamanı | [TASARIM] |
| 2 | K004 → K000-isletim-sistemi | CPU, thread, bellek kaynağı | çalışma zamanı | [TASARIM] |
| 3 | K004 → K001-donanım | Nihai çıkış amplifikatöre/hoparlöre iner | fiziksel | [TASARIM] |
| 4 | K004 → K005-firmware | Donanımda yalnız zamanlama/I2S-TDM (XMOS) — zincir host'ta | — | [TASARIM] (ADR-017) |

A1 dizisi: **K000 → K001 → K003 → K004 → K005.** K004 **dördüncü katmandır** ve
K003'ün üstünde yaşar. Ters yön (K003'ün K004'ü bilmesi) sınır ihlalidir: K003
yalnızca soyut bir işlem noktası tanır (master §B.5).

## §5 Tier-3 Derin Dal Planı (7 dal × 8 dosya — ADR-097 §5.5)

| # | Tier-3 dalı | Üst çocuk | 8 dosyalık rol seti |
|---|---|---|---|
| 1 | `equalizer/eq` | equalizer | anayasa · filtre-topolojileri · coefficient-hesaplari · kod-ornekleri · gercek-zaman-ve-performans · test · dogrulama · bilesenler |
| 2 | `pipeline/resampler` | pipeline | anayasa · nesnel-kesme-ve-aliasing · interpolasyon · kod-ornekleri · performans · test · dogrulama · bilesenler |
| 3 | `measurement/fft-analiz` | measurement | anayasa · pencil-function · windowing · spektrum · kod-ornekleri · test · dogrulama · bilesenler |
| 4 | `measurement/loudness` | measurement | anayasa · itu-r-bs1770 · k-weighting · integrasyon-ve-lufs · kod-ornekleri · test · dogrulama · bilesenler |
| 5 | `reverb/reverb` | reverb | anayasa · fdn-topolojileri · odme-aglari · yutma-filtreleri · kod-ornekleri · test · dogrulama · bilesenler |
| 6 | `compressor/compressor` | compressor | anayasa · ozellik-tanima · attack-release · knee-ve-ratio · kod-ornekleri · test · dogrulama · bilesenler |
| 7 | `limiter/limiter` | limiter | anayasa · lookahead · gain-riding · true-peak · kod-ornekleri · test · dogrulama · bilesenler |

**Kod olmayan 6 çocuk:** filters · dynamics · delay · crossover · spatial-audio ·
testing → tier-3 yok (kapsam tek dosyada yeterli; derinleştirme = YAGNI —
master §A.5). Tier-3 dosya adı `<teknoloji>-<rol>` öneki taşır (soyut ad YASAK).

## §6 Ölçüm Kapıları (tek kaynak — master §C.5)

| # | Eşik | Değer | Kaynak |
|---|---|---|---|
| 1 | SNR | > 100 dB | `.config/opencode/AGENTS.md` §5 (okundu 2026-10-12) |
| 2 | THD+N | < 0.01 % @ 1 kHz | aynı dosya |
| 3 | Buffer / örneklem | 512 · 48 kHz | sartname (ölçüm PLANNED) |
| 4 | DC offset koruma | > 0.5 V → koruma rölesi | ADR-038 H001 |
| 5 | Ses veri biçimi | 32-bit float | ADR-017 |
| 6 | Latency bütçesi | — | **UNKNOWN — sayı uydurulmaz (T8)** |

## §7 Kabul Kriterleri (kapsam dosyasının kendi kapısı)

| # | Kriter | Ölçüm |
|---|---|---|
| 1 | 12 çocuk klasörü diskte mevcut ve index.md taşıyor | 2026-10-13 disk ölçümü ✅ |
| 2 | Kapsam dışı hiçbir konu bu dosyada "bizimiz" yapılmamış | §3 tablosu ile çelişki 0 |
| 3 | "Gerçek zamanlı" iddiası T8 UNKNOWN korunarak yazılmamış | open_questions #1 |
| 4 | Zincir-hesap-yeri iddiası ADR-017 ile uyumlu | §1 Q5 notu |
| 5 | Sayım çelişkisi (12↔13) iki kayıt birlikte taşınarak belgelenmiş | master §A.2 atfı |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
