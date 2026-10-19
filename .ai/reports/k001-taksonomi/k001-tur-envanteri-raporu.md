---
title: "K001 Tür Envanteri Raporu — types (10 bağlam · 25 alan şeması)"
type: report
node_id: reports/k001-taksonomi/k001-tur-envanteri-raporu
status: active
evidence:
  - ".ai/.decisions/draft/ADR-102-tur-taksonomi-genislemesi-5-dal.md §4"
  - ".ai/architecture/K001-donanım/donanaım-type/index.md (§GRUPLAR, children)"
  - ".ai/architecture/K001-donanım/donanım-devices/donanım-devices-kapsam.md (26 alanlık şema — devices dalı)"
  - "ADR-062 (donanım tipi sınıflandırma) · ADR-063 (donanım-envanter)"
validation: "PROPOSED — APPROVAL REQUIRED (ADR-102 draft durumunda)"
open_questions:
  - "Q-T1: 25 alan şeması prompt'tan türetildi; devices dalındaki 26 alan şemasıyla eşleştirme PROPOSED"
  - "Q-T2: Tüm tür envanteri verisi UNKNOWN — fiziksel envanter YOK (K001 Q4)"
group: rapor
date: 2026-10-13
updated: 2026-10-13
---

# K001 Tür Envanteri Raporu (types)

## §1 Amaç

`donanaım-type/` altındaki **sınıf/tür** envanterini tanımlar: *neyin* kategorisi.
`donanım-devices/`'ın karşıtıdır — o *birim* (hangi tekil cihazın) envanteridir (ADR-063).

## §2 Bağlam Haritası (5 mevcut + 4 PROPOSED = 9 · + gpu keşfi = 10)

> **Düzeltme (2026-10-13, bu oturum):** İlk yazım "3 mevcut + 5 PROPOSED = 8"
> derdi; `power/` diskte mevcut (4 dosya, önceki oturum, Vault Steward onayı) —
> 9 bağlam olarak hizalandı (zero-hallucination: disk > taslak).
> **Keşif 2 (22:07 doğrulama):** `gpu/` paralel Vault Steward oturumu tarafından
> açıldı (index · kapsam · dogrulama; `gpu-ornekler.md` 22:49'da tamamlandı) → **10. dal**,
> ADR-102 kapsamı DIŞI (bkz. `gpu/index.md` Q-GI1). gpu satırı altta eklenmiştir.

| # | Bağlam | Durum | Kapsam (özet) | K001'e AİT DEĞİL |
|---|---|---|---|---|
| 1 | `audio/` | aktif | DAC/ADC · amfi · ses kartı sınıfı (PCM3168A, AK4458, TPA3255 — ADR-038/089) | DSP algoritması (K004) · firmware (K005) |
| 2 | `hdd/` | aktif | Mekanik disk sınıfı (kapasite · SMART · 3-2-1) | Depolama protokolü (K000) |
| 3 | `ssd/` | aktif | Katı hal disk sınıfı (NVMe/SATA) | Depolama protokolü (K000) |
| 4 | `display/` | **proposed (4/4)** | Panel (LCD/OLED) · GPU çıkışı · DP/HDMI · dokunmatik · ekran kontrolcüsü | GPU hesap çekirdeği (K004) · güç kaynağı (K002) |
| 5 | `power/` | **proposed (4/4 — önceki oturum)** | Güç kaynağı **sınıf/tip** envanteri (P-T01…P-T13) | Devre/PCB/BOM/güç hesabı (K002-electroncis) |
| 6 | `cables/` | **PROPOSED** | Fiziksel kablo + konnektör sınıfı (HDMI/DP/USB/ethernet/analogue) | PCB yolu (K002) · sinyal bütünlüğü ölçümü (K002) |
| 7 | `network/` | **PROPOSED** | Ağ PHY/kart sınıfı (Ethernet · Wi-Fi · BT) | Ağ protokolü (K000) · driver (K000) |
| 8 | `memory/` | **PROPOSED** | Bellek yongası sınıfı (DDR/SO-DIMM · EEPROM · flash) | Bellek yönetim algoritması (K000) |
| 9 | `cpu/` | **PROPOSED** | İşlemci sınıfı (CPU/SoC · XMOS XU316 — ADR-038) | DSP algoritması (K004) · firmware (K005) |
| 10 | `gpu/` | **proposed (4/4 — paralel oturum 21:52–22:49, ADR-102 DIŞI)** | GPU hesaplama/render sınıfı (Vulkan/CUDA/OpenCL repo ölçümü=0) · display ile sınır gpu-kapsam §0'da | Panel/UI gecikmesi (display dalı) · DSP (K004) |

## §3 25 Alanlı Tür Şeması (PROPOSED)

Her tür kaydı şu alanlarla tanımlanır. **Tüm envanter alanları şu an UNKNOWN** —
fiziksel envanter yok (anayasa §10/4: "Envanter: bilinen modeller gerçek mi?").

| # | Alan | Tip | Açıklama | Örnek (audio — ADR-038 kanıtlı) |
|---|---|---|---|---|
| 1 | `tur_id` | string | Benzersiz tür kimliği | `audio-dac-pcm3168a` |
| 2 | `tur_adi` | string | Görünen ad | PCM3168A Codec |
| 3 | `baglam` | enum | Bağlam (10 bağlamdan biri) | `audio` |
| 4 | `alt_kategori` | string | Alt sınıflandırma | DAC/ADC codec |
| 5 | `arayuz` | string | Bağlantı arayüzü | I2S/TDM |
| 6 | `teknoloji` | string | Temel teknoloji | ΔΣ codec |
| 7 | `uretici` | string | Üretici (UNKNOWN ise) | UNKNOWN |
| 8 | `model` | string | Model (UNKNOWN ise) | PCM3168A |
| 9 | `surum` | string | Donanım sürümü | UNKNOWN |
| 10 | `fiziksel_envanter` | bool | Fiziksel olarak mevcut mu | false |
| 11 | `envanter_kaynak` | string | Envanter kaynak dosyası | UNKNOWN (K001 Q4) |
| 12 | `adr_referans` | list | İlişkili ADR'ler | [ADR-038] |
| 13 | `dosya_yolu` | string | Vault dal dosyası | `donanaım-type/audio/cipler/pcm3168a-codec.md` |
| 14 | `k001_sahiplik` | enum | K001 sahiplik düzeyi | `sinif` (sınıf — birim değil) |
| 15 | `k002_crossref` | string | K002-electroncis referansı | `K002-electroncis` (güç/PCB) |
| 16 | `k003_crossref` | string | K003-audio referansı | `K003-audio` (ses işleme) |
| 17 | `k004_crossref` | string | K004-dsp referansı | `K004-dsp` (DSP algoritması) |
| 18 | `k005_crossref` | string | K005-firmware referansı | `K005-firmware` |
| 19 | `k000_crossref` | string | K000-isletim-sistemi referansı | `K000-isletim-sistemi` (sürücü) |
| 20 | `kanit_seviyesi` | enum | S1/S2/S3 kanıt seviyesi | S1 (ADR kanıtlı) |
| 21 | `url_kaynak` | list | Web kaynak URL'leri (erişim tarihli) | [https://www.ti.com/ (UNKNOWN — doğrulanmadı)] |
| 22 | `erisim_tarihi` | date | URL erişim tarihi | UNKNOWN |
| 23 | `durum` | enum | draft/proposed/active | active |
| 24 | `open_questions` | list | Açık sorular | [Q-AK1: envanter bilinmiyor] |
| 25 | `validation_notu` | string | Doğrulama notu | ADR-038 kanıtlı, envanter UNKNOWN |

> **Şema notu (Q-T1):** Prompt'taki 25 alan, `donanım-devices-kapsam.md`'deki
> **26 alanlık cihaz şemasıyla** farklıdır — bu kasıtlıdır: tür (sınıf) ≠ birim
> (cihaz). Eşleştirme Vault Steward kararıdır (PROPOSED).

## §4 Envanter Kapıları (tümü AÇIK)

| Kapı | Durum | Neden |
|---|---|---|
| S1: ADR kanıtı | audio (PCM3168A/AK4458/TPA3255) için KAPALI (kanıt var) | ADR-038/089 |
| S1: ADR kanıtı | display/cables/network/memory/cpu için **AÇIK** | ADR-102 draft |
| S2: Web URL | display için KAPALI (6 URL HTTP 200, 2026-10-13) | display-kapsam.md evidence |
| S2: Web URL | cables/network/memory/cpu için subagent doğruluyor | bu oturum |
| S3: Fiziksel ölçüm | **Tümü AÇIK** | Fiziksel envanter YOK (K001 Q4) |

## §5 Sınır Kuralları (ADR-102 §4.1)

| Kural | Kaynak |
|---|---|
| Power/PCB/BOM → `K002-electroncis` (cross-ref) | ADR-064 · anayasa §2 |
| DSP algoritması → `K004-dsp` | anayasa §9 |
| Firmware → `K005-firmware` | anayasa §9 |
| Ağ protokolü/driver → `K000-isletim-sistemi` | anayasa §9 |
| `donanaım-type` yazımı korunur (düzeltme yasak) | v2 §3 KURAL 1 |

**Authority:** Bayram Ali / Vault Steward
