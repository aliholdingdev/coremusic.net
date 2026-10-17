---
node_id: audio-kapsam
node_type: module
status: draft
parent_id: K001-donanım/donanaım-type/audio
children: []
depends_on:
  - K001-donanım/donanaım-type
  - K001-donanım/donanaım-type/audio/index
evidence:
  - "K001-donanım/donanaım-type/audio/cipler/ — 3 dosya (ak4458-dac · pcm3168a-codec · tpa3255-amplifier, 2026-10-13 sayıldı)"
  - ".ai/.decisions/accepted/ADR-038-8-1-sound-card-chip-selection.md (accepted, 2026-09-26, TEYİT)"
  - ".ai/.decisions/accepted/ADR-017-dsp-hardware-mode.md · ADR-063-hardware-design-standards.md (varlıkları doğrulandı 2026-10-13)"
  - "https://www.ti.com/product/PCM3168A (HTTP 200, 2026-10-13, HEAD)"
  - "https://www.akm.com/us/en/products/audio/audio-dac/ak4458vn/ (HTTP 200, 2026-10-13, HEAD)"
validation: "draft — 2026-10-13 yazıldı · ≥500 satır ölçümü yazım sonrası · fiziksel envanter ve ölçüm YOK (tüm S1 kapıları açık)"
open_questions:
  - "Q-AK1: Fiziksel ses donanımı envanteri (hangi DAC/amfi kartı takılı) bilinmiyor — UNKNOWN (K001 Q4)"
  - "Q-AK2: ADR-038 çekirdeği (PCM3168A + XU316) için prototip/ölçüm yok — kod 0, entegrasyon PLANNED"
  - "Q-AK3: Amfi topolojisi Class AB sabit; TPA3255 (Class D) yalnız referans kaydıdır — BOM kararı YOK"
group: mimari
---

# audio-kapsam — Ses Donanımı Kapsamı (K001 Donanım Türü Envanteri)

> **Yazım notu:** `donanaım-type` yazımı v2 kaynağına sadıktır — düzeltme yasak
> (v2 §3 KURAL 1; `donanaım-type/index.md` yazım notu). Bu dosya `audio/`
> alt-konunun **kapsam** belgesidir: ses donanımının K001 envanterindeki yeri,
> çekirdek bileşenler, sinyal zincirinin donanım tarafı ve komşu düğümlerle sınır.

## §1 ELI15

Ses donanımı, dijital olarak duran sıfır/birleri **duyulabilir titreşime**
çeviren fiziksel parçalar bütünüdür: bir **çevirici** (DAC — dijitalden
analoğa), varsa bir **toplayıcı** (ADC — analogdan dijitale), bir **sürücü
ses kartı/denetleyici** (USB'den gelen akışı I2S/TDM'a çeviren mikrodenetleyici)
ve bir **güçlendirici** (hoparlöre enerji veren amfi). Bu dosya bu parçaların
**sınıf/envanter** açısından listesini çıkarır, rollerini ayırır ve **kimin
sahip olduğu** sorusunu yanıtlar: hangi soru K001'e (donanım türü), hangisi
K003'e (ses işlemesi) aittir.

> ⚠️ VERIFICATION REQUIRED: Bu alt-konunun tanımı v2 §2.2'deki **adından**
> türetilmiştir; envanter (hangi çipin/kartın fiziksel olarak elde olduğu)
> bilinmiyor (K001 Q4 → `donanaım-type-kapsam.md` §6 Q-T1). Bu dosyadaki
> **sayısal performans değerleri** okunan cip dosyalarından **aktarımdır**;
> bu dosya ölçüm yapmaz, ölçüm `audio-dogrulama.md` kapılarında tanımlıdır.
> Uydurma rakam yazılmaz.

## §2 Kapsam — İÇİNDE NE var

| # | Kapsam İÇİ | Durum |
|---|---|---|
| A1 | Ses donanımı sınıfının K001 envanterindeki yeri (tür listesi) | ✅ mimari tanım |
| A2 | Çekirdek üç cip dosyasının özeti (AK4458 · PCM3168A · TPA3255) | ✅ aktarım + atıf |
| A3 | ADR-038 ses kartı çekirdeğinin **kayıtlı karar** olarak durumu | ✅ ADR diskten okundu |
| A4 | Donanım tarafı sinyal zinciri (USB → I2S/TDM → DAC → amfi → hoparlör) | ✅ sınır tarifi |
| A5 | K001 (donanım türü) ↔ K003 (ses işlemesi) sınırı | ✅ mimari sınır |
| A6 | Etiket disiplini: IMPLEMENTED / DOCUMENTED / PLANNED / PROPOSED / UNKNOWN | ✅ kural tablosu |
| A7 | Amplifikatör topolojisi durumu (Class AB sabit · TPA3255 Class D referans) | ⚠️ çelişki kayıtlı, karar YOK |
| A8 | PCM5122 yasağı ve alternatif envanteri | ✅ red kaydı (H001) |
| A9 | Ölçüm/kabul kapılarının **tanımı** (uygulaması `audio-dogrulama.md`) | ✅ tanım |
| A10 | Riskler ve uygulama sırası (envanter + prototip bekleyen işler) | ✅ plan |

## §3 Kapsam — DIŞINDA NE var (sınır komşuları)

| # | Dışında | Nereye gider | Dayanak |
|---|---|---|---|
| B1 | Sesin **üretilmesi/işlenmesi** (DSP, miks, yönlendirme, gerçek zamanlı) | K003-audio | `donanım-anayasa.md` §2 · K003 `audio-master.md` |
| B2 | ASIO/WASAPI backend, sürücü, cihaz kaybı, tampon stratejisi | K003-audio (`backend/`, `driver/`, `output/`) | K003 `backend/asio/*` (19 dosya) |
| B3 | DSP donanım modu ve hard-RT kısıtları | ADR-017 (accepted) | dosya varlığı doğrulandı 2026-10-13 |
| B4 | Donanım tasarım standartları | ADR-063 (accepted) | dosya varlığı doğrulandı 2026-10-13 |
| B5 | Depolama (kütüphane nerede durur) | `hdd-kapsam.md` · `ssd-kapsam.md` | kardeş alt-konular |
| B6 | Dosya sistemi / sürücü katmanı | K000-isletim-sistemi | `donanaım-type-kapsam.md` §3 |
| B7 | BOM/satın alma kararı, tedarik | K001 envanter (Q4) + owner onayı | bu dosya karar üretmez |
| B8 | PCB/şema/üretim çizimi | `.ai/electronic/**` (owner onayı) | cip dosyaları §Authority |
| B9 | Güç kaynağı ağacı (rail tasarımı) | K001 güç kaynağı envanteri + ADR-063 | envanter UNKNOWN |
| B10 | Kablosuz/ağ üzerinden ses dağıtımı | K010-network · K014-media | komşu K düğümleri |

## §4 Ses Donanımı Sınıfı — K001 Tür Envanteri

> Bu bölüm **tür** listesidir: "CoreMusic ses sisteminde hangi sınıftan
> donanım bulunabilir" sorusunun cevabı. **Fiziksel olarak elde var mı** ayrı
> sorudur → Q-AK1 (UNKNOWN).

| # | Tür | Görev | Vault'taki karşılığı | Etiket |
|---|---|---|---|---|
| 1 | USB ses kartı denetleyicisi | USB UAC2.0 ↔ I2S/TDM çevirisi | ADR-038: XMOS XU316 | DOCUMENTED (karar) · PLANNED (kod) |
| 2 | Codec (ADC + DAC tek çip) | 6-in / 8-out konvertör | `cipler/pcm3168a-codec.md` | DOCUMENTED (datasheet) · PLANNED (entegrasyon) |
| 3 | Çok-kanallı DAC (ayrı çip) | 8 kanal diferansiyel analog çıkış | `cipler/ak4458-dac.md` | DOCUMENTED (datasheet) · PROPOSED (alternatif) |
| 4 | Güçlendirici (amfi) | Hoparlöre güç aktarımı | `cipler/tpa3255-amplifier.md` (Class D referans) | DOCUMENTED · PROPOSED (BOM YOK) |
| 5 | Amplifikatör topolojisi (sabit) | Class AB zinciri (K16) | kök `CLAUDE.md` §Sabitler · `.ai/CLAUDE.md` §5 K16 | sabit kural (kod kanıtı YOK → PLANNED) |
| 6 | Giriş yolu (mikrofon/hat) | ADC kanalları | PCM3168A 6-in (kullanım planı yok) | PROPOSED |
| 7 | Hoparlör/kulak yükü | Akustik dönüşüm | vault'ta dosya YOK | UNKNOWN |
| 8 | Clock/jitter kaynağı | Sistem saati ağacı | ADR-038 §2.2-c | DOCUMENTED · sayısal bütçe ⚠️ |
| 9 | Güç kaynağı (rail) | ±35V / 5V / 3.3V vs. | K001 güç envanteri | UNKNOWN |
| 10 | Kablo/bağlantı (I2S, analog, hoparlör) | Sinyal taşıma | K19 50Ω I2S kuralı (skill atfı) | PROPOSED |

**Okuma kuralı:** "DOCUMENTED" = vault'ta okunan belge/ADR var; "PLANNED" =
kod/firmware henüz yok; "PROPOSED" = öneri/envanter kaydı, karar verilmedi;
"UNKNOWN" = veri hiç yok. **Belgede geçen ≠ fiziksel olarak üretilmiş.**

## §5 Çekirdek Üç Cip (okunan dosya özeti)

> Kaynak: `audio/cipler/*.md` — bu dosya üçüne de **sadece atıf yapar**; tekrar
> hesap üretmez. Rakam cip dosyasından **aktarılmıştır**, bu dosyanın ölçümü
> değildir.

### §5.1 PCM3168A (TI Burr-Brown) — seçili codec

| Alan | Değer (cipler/pcm3168a-codec.md) | Kaynak etiketi |
|---|---|---|
| Tip | Codec — 6-in / 8-out, ΔΣ ADC + ΔΣ DAC tek çip | DOCUMENTED |
| Çözünürlük / örnekleme | 24-bit · ADC 8–96 kHz · DAC 8–192 kHz (192 yalnız DAC) | DOCUMENTED |
| DAC SNR / THD+N | 112 dB (EIAJ A-wtd) · −94 dB @48k | DOCUMENTED (datasheet §8) |
| ADC SNR / THD+N | 107 dB diferansiyel · −93 dB | DOCUMENTED (datasheet §8) |
| Çıkış | Diferansiyel 8 VPP | DOCUMENTED |
| Formatlar | I2S · LJ · RJ · DSP · TDM | DOCUMENTED |
| Paket / güç | HTQFP-64 · 5V analog + 3.3V dijital | DOCUMENTED |
| CoreMusic durumu | ADR-038 ses kartı çekirdeği (kayıtlı karar) | DOCUMENTED (ADR) · PLANNED (kod) |
| Ölçüm durumu | Prototip yok → dosya "PLANNED" yazar | PLANNED |

### §5.2 AK4458 (AKM VELVET SOUND) — alternatif DAC

| Alan | Değer (cipler/ak4458-dac.md) | Kaynak etiketi |
|---|---|---|
| Tip | Sadece DAC — 8 kanal (4 diferansiyel çift) | DOCUMENTED |
| Çözünürlük / tepe | 32-bit · 768 kHz · DSD 11.2 MHz | DOCUMENTED |
| SNR / THD+N (AVDD=5V) | 115 dB · −107 dB @44.1k | DOCUMENTED (datasheet §8) |
| Çıkış | Diferansiyel ±2.8 Vpp tip. | DOCUMENTED |
| Paket | 48-pin QFN | DOCUMENTED |
| CoreMusic durumu | "opsiyonel/alternatif" (brain §8 atfı; BOM kararı YOK) | PROPOSED |
| ADC | **Yok** — giriş yolu için ayrı çip gerekir | DOCUMENTED (eksiklik) |
| Ölçüm durumu | Prototip yok → PLANNED | PLANNED |

### §5.3 TPA3255 (TI PurePath Ultra-HD) — Class D referans kaydı

| Alan | Değer (cipler/tpa3255-amplifier.md) | Kaynak etiketi |
|---|---|---|
| Tip | **Class-D** analog girişli amfi (AD-mode) | DOCUMENTED |
| Güç | 315W stereo / 600W mono (10% THD+N) | DOCUMENTED (ti.com) |
| PVDD | 18–53.5 V (RL=4Ω) önerilen çalışma | DOCUMENTED (datasheet §7.2) |
| Paket / sıcaklık | HTSSOP-44 · 0…70 °C | DOCUMENTED |
| Vault statüsü | **Salt referans/alternatif teknoloji envanteri** — karar YOK, BOM YOK | PROPOSED |
| Topoloji çelişkisi | Class D = yasak (kök `CLAUDE.md` §Sabitler · ADR-089 §3.1-A1 · skill H001) | çelişki kayıtlı |
| Ölçüm durumu | Prototip yok → PLANNED | PLANNED |

**§5 uyarısı (bağlayıcı):** TPA3255'in Class D olarak envantere kaydı,
**mevcut Class AB kuralını otomatik olarak onaylamaz ve gevşetmez.** Belgedeki
durum aynen budur: kural Class AB'dir, bu çip Class D'dir ve yalnız referans
olarak durur; topolojiyi değiştirmek **ADR + owner onayı** ister
(`tpa3255-amplifier.md` §9).

## §6 ADR-038 — Ses Kartı Çekirdeği (KAYITLI KARAR, yeni seçim DEĞİL)

> ⚠️ **Okuma uyarısı:** ADR-038, 2026-09-26'da **kabul edilmiş** ve kendi
> metninde **"TEYİT"** türünde bir karardır (brain §8'deki mevcut seçimin
> gerekçe + entegrasyon + ölçüm planıyla kayda bağlanması). Bu dosya ADR-038'i
> **yeni bir seçim olarak sunmaz**; yalnızca **vault'ta kayıtlı, kabul edilmiş
> kararı** aktarır. Yeni bir çip seçimi ancak **yeni ADR** ile değişir.

| Alan | ADR-038 kaydı |
|---|---|
| Dosya | `.ai/.decisions/accepted/ADR-038-8-1-sound-card-chip-selection.md` |
| Durum | `accepted` · debate ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL) · frozen YOK |
| Tarih | 2026-09-26 (updated 2026-09-29) |
| Karar türü | **TEYİT** — mevcut seçimi gerekçelendirir, yeni seçim yapmaz |
| Çekirdek | **PCM3168A (6-in/8-out) + XMOS XU316 (UAC2.0, I2S/TDM master)** |
| Reddedilen | PCM5122 ailesi (H001 — 2 kanal) · XMOS referans kartının 4×PCM5122 dizilimi (kopyalanmadı) |
| Saat ağacı | Tek kaynak: XU316 → PCM3168A sistem saati (128–768 fS) + I2S/TDM master |
| Ölçüm planı | M1–M6 (AES17-2020 · IEC 61606-1 · EIAJ CP-2404) — `audio-dogrulama.md` §3 |
| Kod durumu | `*.cpp / *.h / *.xc` = **0** → entegrasyon ve ölçüm **PLANNED** (ADR §1.1-B) |
| Jitter bütçesi | "<1 ns RMS" hedefi dış kaynaklı; CoreMusic'e özel sayısal paylar ⚠️ VERIFICATION REQUIRED |

**İlişkili ADR'ler (yalnız varlık iddiası):** `ADR-017-dsp-hardware-mode.md` ve
`ADR-063-hardware-design-standards.md` dosyaları `.ai/.decisions/accepted/`
altında **mevcuttur** (2026-10-13 glob ile doğrulandı). Bu dosya iki ADR'nin
içeriği hakkında **iddia kurmaz**; içerikleri yalnızca ihtiyaç anında
`@` ile okunur.

## §7 Donanım Tarafı Sinyal Zinciri (sınır tarifi)

> Bu tablo **sahiplik** tablosu değildir — her aşamanın sahibi yazılır;
> K003'e devredilen hiçbir adım burada tekrar tanımlanmaz.

| # | Aşama | Sahip | Etiket |
|---|---|---|---|
| 1 | USB host ↔ UAC2.0 asenkron akış | K001 (XU316 donanımı) + K003 (backend) | DOCUMENTED / PLANNED |
| 2 | I2S/TDM üretimi (BCLK, LRCK, SDATA 8 slot) | K001 (donanım saat ağacı) · sürücü K003 | DOCUMENTED |
| 3 | Sistem saati (128–768 fS) ve jitter bütçesi | K001 (ADR-038 §2.2-c) | DOCUMENTED · sayı ⚠️ |
| 4 | Konvertör (DAC 8-out / ADC 6-in) | K001 `[[pcm3168a-codec]]` | DOCUMENTED |
| 5 | Analog seviye/empedans eşleştirmesi | K001 (tasarım YOK — owner onayı) | PROPOSED |
| 6 | Güçlendirici (Class AB zinciri) | K001 topoloji sabiti · ayrı kayıt | sabit kural |
| 7 | DSP/zaman alanı, miks, yönlendirme | **K003-audio** (engine/mixing/routing) | PLANNED |
| 8 | Çıkış kuyruğu (ASIO/WASAPI, 512 örnek @48k) | K003 `[[output-kapsam]]` | PLANNED |
| 9 | Hoparlör → kulak | fiziksel | UNKNOWN (envanter) |

**Kural:** 1–6 ve 9. aşamalar K001 donanım türü kapsamındadır; 7–8. aşamalar
K003 ses işlemesi kapsamındadır. **Çakışma yazmak yasaktır** (§9).

## §8 Etiket Disiplini (belgede geçen ≠ üretilmiş)

| # | Etiket | Anlamı | Bu ağlarda geçerli örnek |
|---|---|---|---|
| 1 | IMPLEMENTED | Kod/fiziksel kanıt diskte vardır | Vault dosyaları (md) — sayım kanıtlı |
| 2 | DOCUMENTED | Belge/ADR/datasheet okunmuştur; üretim yok | 3 cip dosyası · ADR-038 |
| 3 | PLANNED | Karar/spec var, kod/prototip yok | XMOS firmware (kod 0) · ölçüm M1–M6 |
| 4 | PROPOSED | Öneri/envanter kaydı; karar verilmedi | AK4458 alternatifi · TPA3255 referansı |
| 5 | UNKNOWN | Veri hiç yok | Fiziksel envanter (Q-AK1) · hoparlör yükü |

**Kapı kuralı:** Bir satırda tek etiket bulunur; karışık durumda **en düşük**
etiket yazılır (ör. "spec IMPLEMENTED + kod 0 → PLANNED"). "Üretildi/sahte"
iddiası yalnız IMPLEMENTED ile yazılır ve **dosya yolu + sayı** ister
(`audio-dogrulama.md` §4).

## §9 K001 ↔ K003 Sınırı (çakışma yazma kuralı)

| # | Soru | Sahip | K001'in yazabileceği | K001'in YAZAMAYACAĞI |
|---|---|---|---|---|
| 1 | Hangi çip sınıfı envanterde? | K001 | tür + datasheet özeti | çipin DSP davranışı |
| 2 | I2S/TDM saatini kim üretir? | K001 (donanım) | master/slave rolü | sürücü kodu/registry |
| 3 | Tampon boyu ne? | K003 | yalnız atıf (512/48k) | tampon stratejisi tasarımı |
| 4 | ASIO/WASAPI davranışı | K003 (`backend/asio`) | — | hiçbir iddia |
| 5 | Kanal eşlemesi (8 out) | K001 karar · K003 tüketim | ADR-038 satırı | eşlemenin uygulaması |
| 6 | DSP zinciri katsayıları | K003 + K004-dsp | — | — |
| 7 | Gecikme bütçesi (ms) | K003 (ölçüm) | donanım katkısı tanımı | "gecikme şudur" kararı |
| 8 | Cihaz kaybı/hot-swap | K003 `device/` + `driver/` | — | — |
| 9 | Termal/EMI/PCB kuralı | ADR-063 + `.ai/electronic/**` | atıf | tasarım |
| 10 | Örnekleme oranı desteği | K001 (datasheet) | çipin sınırı (ör. ADC 96k) | sistemin hangi fs'de çalışacağı |

**Tespit (envanter):** K003-audio altında **12 alt dizin / 68 md** vardır
(`backend` · `device` · `diagnostics` · `driver` · `engine` · `input` ·
`mixing` · `output` · `realtime` · `routing` · `synchronization` · `testing`).
K001 bu dosyalara **içerik üretmez**; yalnız sınır satırında listeler.

## §10 Amplifikatör Topolojisi — Belgedeki Durum (olduğu gibi)

| # | Öğe | Belgedeki durum | Kaynak |
|---|---|---|---|
| 1 | CoreMusic amfi topolojisi | **Class AB** (sabit kural) | kök `CLAUDE.md` §Sabitler · `.ai/CLAUDE.md` §5 K16 |
| 2 | Class D | **yasak** (kural olarak) | aynı sabit · ADR-089 §3.1-A1 · skill H001 (cipler dosyası atfı) |
| 3 | TPA3255 | Class **D** çip; vaultta **yalnız referans** kaydı | `cipler/tpa3255-amplifier.md` §0/§9 |
| 4 | Bu dosyanın tutumu | Durum **olduğu gibi** yazılır; Class AB kuralı **ne onaylanır ne de gevşetilir** | bu § |
| 5 | Değişiklik yolu | Topoloji değişikliği = **ADR + owner onayı** | Guardrail #14 (cipler dosyası atfı) |
| 6 | Ölçüm kapısı | Herhangi bir topoloji kararı ölçüm ister (THD+N/SNR/EMI/termal) | `audio-dogrulama.md` §5 |

> ⚠️ VERIFICATION REQUIRED: K16 Class AB zincirinin **kod/fiziksel kanıtı bu
> oturumda aranmadı** (repo `*.cpp=0 · *.h=0` ölçümü §19'da); kural olarak
> vault'ta yazılıdır, üretilmiş hâli PLANNED'dir.

## §11 Yasaklar ve Red Kayıtları

| # | Yasak / Red | Dayanak | Bu dosyada davranışı |
|---|---|---|---|
| 1 | **PCM5122 önerme** | H001 (brain `:878` · `k1-donanim/CLAUDE.md:20` · edge-case #8) | PCM5122 **hiç önerilmez**; alternatif olarak PCM3168A/AK4458 envanterde |
| 2 | XMOS referans kartının 4×PCM5122 dizilimini kopyalamak | ADR-038 §3 alt.3 · §4.3 R2 | kopyalanmaz; referans yalnız firmware mimarisi için |
| 3 | Class AB kuralını bozmak | kök `CLAUDE.md` §Sabitler | kural olduğu gibi taşınır (§10) |
| 4 | TPA3255'i "seçim" gibi sunmak | `tpa3255-amplifier.md` §9 | PROPOSED etiketi korunur |
| 5 | `donanaım-type` yazımını düzeltmek | v2 §3 KURAL 1 | dizin/dosya adı olduğu gibi |
| 6 | Uydurma URL/sürüm/ENVanter yazmak | Zero-Hallucination (kök AGENTS §3) | yalnız HEAD-200 + disk kanıtı |
| 7 | `{...}` / `TBD` yer tutucusu | görev yasağı | bilinmeyen = `UNKNOWN` / `⚠️` |

## §12 Kabul Eşikleri (tanım — uygulama `audio-dogrulama.md`)

| # | Kapı | Hedef (kaynak) | Şu anki durum |
|---|---|---|---|
| 1 | M1 THD+N @1kHz 0dBFS | ≥ 90 dB (ADR-038 §2.2-d; datasheet −94 dB) | ❌ ölçülmedi |
| 2 | M2 SNR / idle channel noise | ≥ 105 dB (datasheet 112 dB) | ❌ ölçülmedi |
| 3 | M3 Kanal ayrımı | ≥ 95 dB (datasheet 102/108 dB) | ❌ ölçülmedi |
| 4 | M4 Frekans/level matrisi | FADGI/AES17 (−1/−10/−20/−60 dBFS × 41/997/6597 Hz) | ❌ ölçülmedi |
| 5 | M5 Kanal arası kayma | ≤ 100 ps | ❌ ölçülmedi |
| 6 | M6 Jitter duyarlılığı | PLL on/off farkı ≤ 1 dB | ❌ ölçülmedi |
| 7 | Envanter kapısı | Marka/model/paket listesi | ❌ UNKNOWN (Q-AK1) |
| 8 | Etiket kapısı | Her satırda tek etiket | ✅ bu dosyada uygulandı |

## §13 Riskler

| # | Risk | Etki | Olasılık | Azaltma |
|---|---|---|---|---|
| 1 | Envanter yokken karar vermek (BOM) | kritik | **aktif** | Q-AK1 kapatılmadan satın alma yok |
| 2 | "Belgede var" → "üretildi" sanılması | yüksek | yüksek | §8 etiket disiplini + `audio-dogrulama.md` §4 |
| 3 | ADR-038'in yeni seçim sanılması | orta | orta | §6 TEYİT uyarısı |
| 4 | TPA3255'in BOM'a sessizce girmesi | kritik | düşük-orta | §11/4 · PROPOSED etiketi |
| 5 | K001↔K003 sınırının yazım sırasında kayması | orta | orta | §9 tablosu — çakışma yazma yasağı |
| 6 | Sayısal jitter bütçesinin uydurulması | yüksek | orta | yalnız ölçümle (M6) kapanır |
| 7 | ADC 96 kHz sınırının göz ardı edilmesi | orta | orta | §5/1 · ADR-038 §4.2 |
| 8 | Ölçüm kapılarının "geçti" sanılması | kritik | yüksek | S1 kanıtsız "GEÇTİ" yazılmaz |
| 9 | Çip datasheet rakamının sistem ölçümü sanılması | yüksek | yüksek | datasheet ≠ ölçüm (§19.5) |

## §14 Uygulama Sırası (checklist)

| # | Adım | Çıktı | Kapı |
|---|---|---|---|
| 1 | Fiziksel envanteri çıkar (çip/kart/paket) | liste | Q-AK1 |
| 2 | Etiket tablosunu her dosyaya uygula | etiket sütunu | §8 |
| 3 | K001↔K003 sınırını gözden geçir | sınır tablosu | §9 |
| 4 | ADR-038'in TEYİT olduğunu her özetle tekrarla | not | §6 |
| 5 | Class AB / Class D durumunu olduğu gibi yaz | kayıt | §10 |
| 6 | Ölçüm tezgâhını kur (AES17/IEC 61606/EIAJ) | tezgâh | `audio-dogrulama.md` §6 |
| 7 | M1–M6 ölçümünü çalıştır | ölçüm raporu | `audio-dogrulama.md` §3 |
| 8 | Web kaynaklarını HEAD ile doğrula | URL tablosu | `audio-dogrulama.md` §7 |
| 9 | Uygulama örneklerini şablonla | örnek defteri | `audio-ornekler.md` |
| 10 | Sahiplik kararı (BOM/topoloji) → owner + ADR | karar kaydı | Guardrail #14 |

## §15 Sık Yapılan Hatalar

| # | Hata | Belirti | Doğrusu |
|---|---|---|---|
| 1 | ADR-038'i "yeni seçim" diye sunmak | Karar yeniden tartışılır | TEYİT + tarih (2026-09-26) |
| 2 | Datasheet SNR'ını sistem SNR'ı sanmak | Ölçümden önce "112 dB" demek | §19.5 — datasheet ≠ ölçüm |
| 3 | PCM5122 önermek | H001 ihlali | PCM3168A/AK4458 envanteri |
| 4 | TPA3255'i BOM'a yazmak | Class D yasağı ihlali | PROPOSED · ADR + onay |
| 5 | K003 konusunu bu dizinde tekrar yazmak | Çifte sahiplik | §9 — sınır yeterli |
| 6 | IMPLEMENTED etiketini spec'e yapıştırmak | Sahte kanıt | kod 0 ise PLANNED |
| 7 | `{...}`/`TBD` bırakmak | Okunamaz tablo | `UNKNOWN` / `⚠️` |
| 8 | Yazımı düzeltmek ("donanım-type") | 489+ wiki-link kırılır | yazım korunur |
| 9 | Envanter çıkmadan satın alma | Uyumsuz parça | Q-AK1 |

## §16 Terim Sözlüğü

| # | Terim | Tanım |
|---|---|---|
| 1 | DAC | Dijital → analog çevirici |
| 2 | ADC | Analog → dijital çevirici |
| 3 | Codec | ADC + DAC tek çipte (PCM3168A) |
| 4 | UAC2.0 | USB Audio Class 2.0 (asenkron akış) |
| 5 | I2S | 3 hatlı ses veri arayüzü (BCLK/LRCK/SDATA) |
| 6 | TDM | Zaman bölüşümlü çok-kanal taşıma (tek hatta 8 slot) |
| 7 | BCLK / LRCK | Bit saati / kare (kanal) saati |
| 8 | MCLK / SCKI | Master clock / sistem clock girişi |
| 9 | Jitter | Saat kenarının zaman dalgalanması |
| 10 | SNR | Sinyal-gürültü oranı (dB) |
| 11 | THD+N | Bozulma + gürültü (sinyale göre) |
| 12 | DNR / dinamik aralık | En yüksek–en düşük seviye farkı |
| 13 | BTL / SE / PBTL | Dengeli / tek uçlu / paralel dengeli çıkış |
| 14 | VPP | Tepe-tepe voltaj |
| 15 | FSR | Tam ölçek (full scale reference) |
| 16 | Class AB | Doğrusal (yarım akım) amfi topolojisi — K001 sabiti |
| 17 | Class D | Anahtarlanmış amfi topolojisi — vaultta yasak/referans |
| 18 | H001 | PCM5122 ailesi yasağı (edge-case) |
| 19 | IMPLEMENTED / PLANNED | Kod kanıtlı / spec-only ayrımı (§8) |
| 20 | PROPOSED | Öneri — karar verilmedi |
| 21 | UNKNOWN | Veri hiç yok |
| 22 | ADR | Architecture Decision Record (karar kaydı) |
| 23 | TEYİT | Mevcut kararı gerekçelendiren ADR türü (ADR-038) |
| 24 | Frozen | Değiştirilemez ADR (ADR-001…036) |

## §17 Kapsam Dışı (özet)

| # | Konu | Sahip |
|---|---|---|
| 1 | DSP/zaman alanı/miks/yönlendirme | K003-audio |
| 2 | ASIO/WASAPI/sürücü/cihaz kaybı | K003-audio (`backend`, `driver`, `device`) |
| 3 | Dosya sistemi / depolama | K000 · `hdd-*` · `ssd-*` |
| 4 | BOM/satın alma | owner + K001 envanter (Q4) |
| 5 | PCB/şema/üretim | `.ai/electronic/**` (onaylı) |
| 6 | Donanım tasarım standartları içeriği | ADR-063 (okunmadı — varlık iddiası) |
| 7 | DSP donanım modu içeriği | ADR-017 (okunmadı — varlık iddiası) |
| 8 | Doğrulama uygulaması | `audio-dogrulama.md` |
| 9 | Uygulama örnekleri | `audio-ornekler.md` |

## §18 Kaynakça (bu dosyanın kaynakları)

| # | Kaynak | Tarih | Durum |
|---|---|---|---|
| 1 | `audio/cipler/pcm3168a-codec.md` (500 satır) | 2026-10-13 okundu | ✅ disk kanıtı |
| 2 | `audio/cipler/ak4458-dac.md` (541 satır) | 2026-10-13 okundu | ✅ disk kanıtı |
| 3 | `audio/cipler/tpa3255-amplifier.md` (586 satır) | 2026-10-13 okundu | ✅ disk kanıtı |
| 4 | ADR-038 (accepted, 2026-09-26) | 2026-10-13 okundu | ✅ disk kanıtı |
| 5 | ADR-017 · ADR-063 dosya varlıkları | 2026-10-13 glob | ✅ yalnız varlık iddiası |
| 6 | https://www.ti.com/product/PCM3168A | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| 7 | https://www.akm.com/us/en/products/audio/audio-dac/ak4458vn/ | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| 8 | https://www.xmos.com/ | 2026-10-13 | ❌ HTTP 406 → **kullanılmadı** |
| 9 | `donanaım-type/index.md` (72 satır) · `audio/index.md` (47 satır) | 2026-10-13 okundu | ✅ disk kanıtı |
| 10 | K003-audio envanteri: 12 dizin / 68 md | 2026-10-13 sayıldı | ✅ ölçüm |

**Uydurma kaynak yok:** 2 web URL'si bu oturumda HEAD ile 200 döndürülerek
doğrulanmıştır; 406 döndüren URL **kullanılmamıştır**; diğer satırlar disk
dosyası referansıdır. **Yeni envanter kesişimi: 0** (bu dosya envantere yeni
fiziksel parça iddiası eklemez).

## §19 Çapraz Referanslar ve Bütçe

### §19.1 Çapraz referanslar

| # | Hedef | İlişki |
|---|---|---|
| 1 | `[[audio/index]]` | Bu konunun künyesi |
| 2 | `[[donanaım-type/index]]` | Üst künye · children + §GRUPLAR |
| 3 | `[[donanaım-type-kapsam]]` | Üst kapsam · envanter UNKNOWN |
| 4 | `[[hdd-kapsam]]` · `[[ssd-kapsam]]` | Kardeş sınıflar (depolama) |
| 5 | `[[pcm3168a-codec]]` | Seçili codec (ADR-038 çekirdeği) |
| 6 | `[[ak4458-dac]]` | Alternatif DAC (PROPOSED) |
| 7 | `[[tpa3255-amplifier]]` | Class D referans kaydı (PROPOSED) |
| 8 | `audio-dogrulama.md` | Ölçüm kapıları M1–M6 |
| 9 | `audio-ornekler.md` | Uygulama şablonları |
| 10 | ADR-038 · ADR-017 · ADR-063 | Karar/standart kayıtları |

### §19.2 Kanal/bütçe aritmetiği (türetilmiş — ölçüm değil)

| # | Hesap | İşlem | Sonuç |
|---|---|---|---|
| 1 | PCM3168A toplam kanal | 6-in + 8-out | 14 kanal (tek çip) |
| 2 | 8.1 surround hedefi için çıkış | 7 + 1 | 8 out — tek çip yeterli |
| 3 | DAC SNR marjı (K16 hedefi >105 dB) | 112 − 105 | 7 dB tip. marj (datasheet) |
| 4 | AK4458 SNR marjı (aynı hedef) | 115 − 105 | 10 dB tip. marj (datasheet) |
| 5 | ADC tarafı 192 kHz desteği | 96 kHz sınır | çıkış 192k / giriş 96k |
| 6 | Çip başına PCM5122 ile 8 out | 8 ÷ 2 | 4 çip (H001 yasağı nedeniyle önerilmez) |

**Uyarı:** 3–4. satırlar **datasheet tip. değerleri** üzerindendir — sistem
ölçümü değildir (`§12` M1–M2 kapıları açık).

### §19.3 Bağımlılık haritası

| # | Bu konu | Bağımlı olduğu | Yer |
|---|---|---|---|
| 1 | audio-kapsam | Donanım envanteri (UNKNOWN) | K001 Q4 |
| 2 | audio-kapsam | Ses kartı çekirdek kararı | ADR-038 (TEYİT) |
| 3 | audio-kapsam | Topoloji sabiti | kök `CLAUDE.md` §Sabitler |
| 4 | audio-kapsam | K003 sınırı | K003 index + 12 alt dizin |
| 5 | audio-dogrulama | Bu dosyanın §12 kapıları | `audio-dogrulama.md` |
| 6 | audio-ornekler | Bu dosyanın §7 zinciri | `audio-ornekler.md` |

### §19.4 Doğrulama kapısına devir

| # | Bu dosyadaki iddia | Kanıt tipi | Gittiği dosya | Seviye |
|---|---|---|---|---|
| 1 | 3 cip datasheet özeti | aktarım (okuma) | `audio-dogrulama.md` §2 | S4 ✅ |
| 2 | ADR-038 çekirdek/ölçüm planı | ADR disk kanıtı | `audio-dogrulama.md` §3 | S5 ✅ |
| 3 | M1–M6 eşikleri | ADR tarifi | `audio-dogrulama.md` §3 | S5 ✅ |
| 4 | Gerçek ölçüm sonuçları | **ölçüm gerekli** | `audio-dogrulama.md` §5 | S1 → ❌ açık |
| 5 | Fiziksel envanter | insan girdisi | `audio-dogrulama.md` §6 | ❌ UNKNOWN |
| 6 | 2 web URL | HEAD 200 | `audio-dogrulama.md` §7 | S3 ✅ |
| 7 | K001/K003 sınırı | vault kuralı | bu dosya §9 | S4 ✅ |
| 8 | Class AB / Class D durumu | sabit kural + cip dosyası | bu dosya §10 | S5 ✅ |

### §19.5 Datasheet ≠ sistem ölçümü (ayırıcı tablo)

| # | Değer | Nereden geldi | Ne değildir |
|---|---|---|---|
| 1 | PCM3168A SNR 112 dB | TI datasheet §8 (EIAJ A-wtd) | CoreMusic sisteminin SNR'ı **değil** |
| 2 | PCM3168A THD+N −94 dB | TI datasheet §8 @48k | Ölçüm tezgâhı sonucu **değil** |
| 3 | AK4458 SNR 115 dB | AKM datasheet §8 (AVDD=5V) | Sistem seviyesi **değil** |
| 4 | TPA3255 SNR 112 dB | TI datasheet §7.6 (BTL) | Class AB zincirinin sonucu **değil** |
| 5 | Jitter hedefi <1 ns RMS | ADR-038 §2.2-c (dış kaynak) | Ölçülmüş bütçe **değil** — ⚠️ |
| 6 | K16 hedef SNR >105 dB | vault hedefi (skill atfı) | Gerçekleşmiş değer **değil** — PLANNED |
| 7 | 512 örnek ≈ 10,67 ms | aritmetik (512 ÷ 48000) | Ölçüm değildir — S2 |

**Kural:** Yukarıdaki 1–6. satırların **hiçbiri** "geçti" olarak yazılamaz;
"GEÇTİ" yalnız `audio-dogrulama.md` §5'te **S1 kanıtı** ile yazılır.

### §19.6 Açık sorular tablosu (kapıya bağlı)

| # | Soru | Etiket | Kapı |
|---|---|---|---|
| 1 | Fiziksel ses donanımı envanteri nedir? | UNKNOWN | Q-AK1 · `audio-dogrulama.md` §6 |
| 2 | ADR-038 çekirdeği prototipte çalışıyor mu? | PLANNED | `audio-dogrulama.md` §3 |
| 3 | Topoloji Class AB sabit mi kalacak? | sabit kural · değişiklik ADR ister | §10 · Guardrail #14 |
| 4 | TPA3255 BOM'a girer mi? | PROPOSED | owner + ölçüm (§12) |
| 5 | AK4458 kullanılır mı (8+ kanal)? | PROPOSED | ADR-038 §3 alt.2 |
| 6 | ADC 6 kanal hangi kaynaklara bağlanır? | UNKNOWN | K001/K003 sınırı (§9) |
| 7 | Sayısal jitter payları nedir? | ⚠️ VERIFICATION REQUIRED | M6 ölçümü |
| 8 | Güç rail'i (5V/3.3V/±35V) nasıl türetilir? | UNKNOWN | K001 güç envanteri |

### §19.7 Karar matrisi (satın alma / entegrasyon öncesi)

| # | Soru | İyi cevap | Kötü cevap |
|---|---|---|---|
| 1 | Karar vault'ta mı? | ADR numarası + tarih | "internette öyle yazıyordu" |
| 2 | Etiket nedir? | §8'den tek etiket | "hazır" / "yapıldı" |
| 3 | Ölçüm var mı? | M1–M6 kayıt no | datasheet rakamı |
| 4 | Envanter biliniyor mu? | marka/model/paket | UNKNOWN → DUR |
| 5 | Kime ait? | K001 mi K003 mü (§9) | iki dosyada da yazmak |
| 6 | Yasağa dokunuldu mu? | PCM5122 yok · Class AB bozulmadı | sessiz alternatif ekleme |
| 7 | Yeni çip gerekirse? | yeni ADR + owner | bu dosyayı düzenlemek |
| 8 | Belirsizlik nasıl yazıldı? | `UNKNOWN` / `⚠️` | `{...}` / `TBD` |

### §19.8 Konu → dosya indeksi (hızlı erişim)

| # | Soru | Dosya |
|---|---|---|
| 1 | Hangi çipler envanterde? | `cipler/*.md` (3 dosya) |
| 2 | Ses kartı çekirdeği neydi? | ADR-038 (`.ai/.decisions/accepted/`) |
| 3 | Topoloji kuralı nedir? | kök `CLAUDE.md` §Sabitler · `.ai/CLAUDE.md` §5 K16 |
| 4 | K003 sınırı nerede biter? | bu dosya §9 · K003 `audio-master.md` |
| 5 | Ölçüm kapıları ne? | `audio-dogrulama.md` §3 · §12 (bu dosya) |
| 6 | Uygulama şablonları nerede? | `audio-ornekler.md` |
| 7 | Üst kapsam sınırı? | `donanaım-type-kapsam.md` |
| 8 | Kardeş sınıflar? | `hdd-kapsam.md` · `ssd-kapsam.md` |
| 9 | ADR-017/063 içeriği? | `.ai/.decisions/accepted/` (içerik okunmadı) |
| 10 | Standart ölçüm tarifi? | AES17-2020 · IEC 61606-1 · EIAJ CP-2404 (ADR-038 §2.2-d) |

---

## §20 Dürüstlük Bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez. Eklenen bölümler yalnız okunan disk
dosyaları (3 cip · ADR-038 · index dosyaları) ve 2 adet HEAD-200 web URL'si
içerir; ADR-017 ve ADR-063 hakkında **yalnız varlık iddiası** kurulmuştur.
Doğrulanamayan her iddia `⚠️ VERIFICATION REQUIRED` veya `UNKNOWN` ile
işaretlidir; `IMPLEMENTED / DOCUMENTED / PLANNED / PROPOSED / UNKNOWN`
etiketleri §8'de tanımlandığı gibi uygulanmıştır. Belgede geçen hiçbir değer
"fiziksel olarak üretildi" diye sunulmamıştır; `{...}` ve `TBD` yer tutucusu
kullanılmamıştır. Ölçüm iddiası yalnız `audio-dogrulama.md` §5'te S1 kanıtı
ile yazılabilir.

---

## §21 OS × DİL ERİŞİM MATRİSİ (ses)

> Bu bölüm `donanım-platformlararasi-kod-ornekleri.md` §PLATFORM MATRİSİ (P1–P6 · G1–G11)
> üzerine kuruludur; **yeni URL eklenmemiştir.** Hücreler "OS algılama → ses aygıtı erişim
> yolu (§P5)" biçimindedir; doğrulanmamış dil-OS bağları hücre içinde `⚠️` ile işaretlidir.
> Bölüm yalnız erişim yolu tanımlar — ölçüm/kanıt iddiası taşımaz (§20 · §8 geçerlidir).

| Dil | Windows | macOS (Darwin) | Linux |
|---|---|---|---|
| JavaScript (Node.js) | `process.platform` (§G1) → WASAPI ⚠️ (K000/K003 sahipliği, §P5) | `process.platform` (§G1) → CoreAudio (§P5) | `process.platform` (§G1) → ALSA (§P5) — Node sarmalayıcı adı yok ⚠️ |
| C | `#if defined(_WIN32)` (§G2) → WASAPI (COM) ⚠️ (§P5) | `__APPLE__` ⚠️ (§G2) → CoreAudio (§P5) | `__linux__` (§G2) → ALSA kütüphanesi (§P5) |
| Python | `sys.platform` (§G3) → `ctypes` + WASAPI ⚠️ (URL'siz) | `sys.platform` (§G3) → CoreAudio ⚠️ (dil bağı URL'siz) | `sys.platform` (§G3) → ALSA ⚠️ (dil bağı URL'siz) |
| Go | `runtime.GOOS` (§G4) → WASAPI ⚠️ (sarmalayıcı yok) | `runtime.GOOS` (§G4) → CoreAudio ⚠️ (sarmalayıcı yok) | `runtime.GOOS` (§G4) → ALSA ⚠️ (sarmalayıcı yok) |
| Rust | `cfg!` (§G5) → WASAPI ⚠️ (sarmalayıcı ADR bekliyor §G5.3) | `cfg!` (§G5) → CoreAudio ⚠️ (§G5.3) | `cfg!` (§G5) → ALSA ⚠️ (§G5.3) |
| C# (.NET) | `RuntimeInformation` (§G6) → WASAPI P/Invoke/COM ⚠️ (§P5) | `RuntimeInformation` (§G6) → CoreAudio P/Invoke ⚠️ | `RuntimeInformation` (§G6) → ALSA P/Invoke ⚠️ |
| PHP | `PHP_OS_FAMILY` (§G7) → PHP'de ses API'si **yok** → çocuk süreç ⚠️ | aynı (CoreAudio'ya doğrudan erişim yok) ⚠️ | aynı (ALSA'ya doğrudan erişim yok) ⚠️ |
| Bash | — (çocuk süreç: PowerShell ⚠️) | `uname -s` ⚠️ MINGW (§G8.1) → CoreAudio aracı doğrulanmadı ⚠️ | `uname -s` (§G8.1) → ALSA araç/dosya erişimi ⚠️ (yol doğrulanmadı) |
| PowerShell | `[RuntimeInformation]::IsOSPlatform` (§G8.2) → WASAPI ⚠️ (§P5) | — | — |
| Java | `System.getProperty("os.name")` (§G8.3) → JNA/FFM ⚠️ (URL'siz, §K3) | aynı ⚠️ (§G8.3) | aynı ⚠️ (§G8.3) |
| Swift | `#if os(Windows)` (§G9) → fiziksel yol yazılmadı ⚠️ (rapor §4.11) | `#if os(macOS)` (§G9) → CoreAudio ⚠️ (dil-köprüsü rapor §4.11) | `#if os(Linux)` (§G9) → ALSA ⚠️ (dil-köprüsü rapor §4.11) |
| Kotlin | Kotlin/Native POSIX / JVM `os.name` (§G10) → JNA/FFM ⚠️ | aynı ⚠️ (§G10) | aynı ⚠️ (§G10) |
| C++ | `#if defined(_WIN32)` (§G11) → WASAPI (COM) ⚠️ — G2 Win32 yolları | `__APPLE__` ⚠️ (§G11) → CoreAudio (§P5) | `__linux__` (§G11) → ALSA kütüphanesi (§P5) |

**Öneri (repo dili PHP/JS):** Ses aygıtı tespiti repo katmanında PHP/JS'ten **doğrudan**
API ile yapılmaz; JS/Node ve PHP çocuk süreç (PowerShell / `system_profiler` / ALSA
aracı ⚠️) ile listeler ve sonucu K001'e taşır. Doğrudan erişim Çİ/C++ katmanındadır:
Windows WASAPI (K000 sahipliği), macOS CoreAudio, Linux ALSA (§P5).

**Risk/uyumluluk (korunan ⚠️ kayıtları):**
- WASAPI **sahipliği** bölünmüştür: sürücü/düşük seviye K000-isletim-sistemi, backend
  K003 (§3 · B2) — bu bölüm yalnız **çağrı şablonunu** satar (§P5 sınır notu).
- `__APPLE__` ön-tanımlı makrosu için doğrulanmış URL yok (§K4/R4); ortak ⚠️ kayıtları —
  macOS `sysctl -n hw.memsize` 404 (R1) ve `getifaddrs` 404 (R8) — silinmez; ses erişimi
  bellek/ağ yolunu kullanmaz, kayıt korunur.
- Dil-OS ses bağları (Python/Go/Rust/C#/Java/Swift/Kotlin sarmalayıcıları) doğrulanmadı
  (§G8.4 · §G9 · §G10 · §K3 JNA/FFM) → ⚠️ VERIFICATION REQUIRED; uydurma crate/paket
  adı yazılmaz.
- Git Bash/MSYS2 `uname -s` MINGW öneki doğrulanamadı (§G8.1) → ⚠️ VERIFICATION REQUIRED.
- Aşama/gecikme/ses kartı envanteri ölçümü bu matristen **üretilmez** (§8 etiket
  disiplini · S1 yalnız `audio-dogrulama.md` §5).

Kapsamlı şablonlar: `donanım-platformlararasi-kod-ornekleri.md` §G1–§G11 · tam matris: `raporlar/K001-donanım-os-dil-matrisi.md`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
