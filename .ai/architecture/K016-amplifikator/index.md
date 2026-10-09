---
title: "K016 AMPLIFIER «ZAR» — Katman Index"
type: index
category: architecture
version: 1.0.0
status: draft
authority: "SSOT: .ai/architecture/K016-amplifikator/index.md — K-space band-1 katmanı (ADR-096)"
updated: 2026-10-08
tier: 3
domain: amplifikator
ssot: true
risk: medium
owner: audio-hw
depends-on: [".ai/architecture/00-kspace-anayasa.md"]
---

# K016 AMPLIFIER «ZAR» — Katman Index

> **Authority:** Bu dosya K016 katmanının tek SSOT'udur (R7.2). Üst otorite:
> `00-kspace-anayasa.md` §A.1 K016 kartı > `.ai/CLAUDE.md` §5/§23 > `rules.md` > bu dosya (R8.1).
> Kurucu karar: `ADR-096-kspace-5000-boundary-model.md` §2 (hibrit kayıt · dizin deseni · çift uçak)
> + donanım kararı: `ADR-089-classab-24v` (accepted).
> **Durum:** `draft` — bant onayı Kapı 10'da 👤 (R10). Vault'a yazılmadı (staging).
> **Uçak:** PHYSICAL PLANE (anayasa §A.0: `K016 · AMPLIFIER · «ZAR» · PHYSICAL` · ADR-096 §2.2
> — PHYSICAL = K016→K020).

## Künye

| Alan | Değer |
|---|---|
| K-ID | K016 |
| Kanonik Ad | AMPLIFIER |
| Teatral Epitet | «ZAR» |
| Bant | Bant 1 — K000-K020 EXISTING FOUNDATION |
| Uçak | PHYSICAL (ADR-096 §2.2 — SOFTWARE PLANE K000→K15+; bu katman fiziksel düzlemde) |
| Dizin deseni | `.ai/architecture/K016-amplifikator/index.md` (R2.2 — dizin henüz üretilmedi, ls 2026-10-08) |
| Tier / Domain | 3 / amplifikator |
| Owner (`.ai/AGENTS.md` §4 registry) | audio-hw (Audio Hardware Engineer — `audio-hw`, AGENTS.md L91) |
| Risk | medium — ses-güç yüzeyi (DC offset koruma rölesi, gain hataları) yüksek etkili ama anayasa §23 #7 gibi spesifik koruma zorunlulukları tanımlı; risk:high eşiği K017 (güç kaynağı) için ayrılmıştır (gerekçe §4.5) |
| Depends-on | `.ai/architecture/00-kspace-anayasa.md` |
| Bağlı ADR'ler (ls-verified: `.ai/.decisions/accepted/`) | ADR-038 · ADR-061 · ADR-063 · ADR-064 · ADR-089 · ADR-090 · ADR-096 |
| Kanıt tarihi | 2026-10-08 |
| Bant-kardeş üretimi | band-1 seti (K014-K020) — 7 dosya, staging |
| Şablon/format otoritesi | R3/R4 (iskelet + min-500 + 16 özet + 20 alan EK C) — `rules.md` |

#### §1 Genel Bakış

K016 AMPLIFIER «ZAR», CoreMusic'in güç-amplifikatör katmanıdır: Class AB topolojisi, MJL21194/93
Darlington çıkış çifti, 8 kanal modüler yapı, 50W/kanal ve THD <0.005% hedefi anayasa §5 K16
satırında bağlayıcıdır; kısıt satırı "Tek kanal bağımsız, enable pinli"tir. Bu katman **PHYSICAL
PLANE**'dedir (ADR-096 §2.2 GÖREV 07): yazılım katmanlarıyla (özellikle K015 MEDIA) **senkron
bağlantısı YOKTUR** — erişim yalnız sürücü/API/olay sınırından ve fiziksel arayüzden (DAC/ADC,
giriş-çıkış, PCB) yapılır. Repo durumu: `find` ile `*.cpp/*.c/*.ino/CMakeLists/platformio` →
**0 sonuç** (2026-10-08) — yani bu katmanda henüz uygulama kodu yoktur; tüm kalemler
DESIGN/PLANNED seviyesindedir (R16.3: IMPLEMENTED iddiası repo dosya yolu ister).

---

## §2 Özet Satır — 16 Alan (R4.1 / F1 §10.2 kontratı)

| K-ID | KANONİK_AD | TEATRAL_EPİTET | DOMAIN | RUNTIME | SORUMLULUK | GİRDİ | ÇIKTI | İZİNLİ_BAGIMLILIK | YASAK_BAGIMLILIK | DATA_BOUNDARY | SECURITY_BOUNDARY | FAILURE_MODE | OBSERVABILITY | TEST | KANIT |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| K016 | AMPLIFIER | «ZAR» | amplifikator | Class AB analog zincir + STM32/RP2040 MCU telemetri (ADR-089 hibrit MCU); firmware repo'su YOK (find 0, 2026-10-08) | Class AB · DAC Output · ADC Input · Speaker Out · Subwoofer · Multi-Channel · Gain · Protection (EK A §A.1 · "Class D" kalemi ADR-089 ile reddedildi — §4.1.2) · 120 bileşen (anayasa §5 K16) | DAC çıkışı (PCM3168A/AK4458 — K001) · ±35V güç barı (K017) · termal durum (K018) · enable/gain komutları (olay-sınırı) | hoparlör/subwoofer analog çıkışı (8 kanal) · koruma durumu (DC offset rölesi) · telemetri/geri-bildirim olayı | K000-K015 (EK A aralık) + port/adapter — **PHYSICAL uç: K015/K009 yazılımına yalnız sürücü/API/olay sınırı, senkron yok** (ADR-096 §2.2) | K016 → K017-K020 sağ/üst erişim (H20) · **K015 medya yazılımı ile SENKRON bağ** (çift uçak ihlali) · H19 veri paylaşımı · K003/K001 sinyal/veri kaynaklarına doğrudan veri yazımı · Class D topolojisi (ADR-089 + anayasa §5 "Class D yasak") · PCM5122 kullanımı (anayasa §21/#22 H001 REJECT) | fiziksel-kalibrasyon/telemetri kaydı (gain, bias, DC offset, sıcaklık okuması); K015/K005 iş verisi PAYLAŞMAZ (H19) — ölçüm verisi üretim testinde K020'ye ait | DC offset >0.5V → koruma rölesi (anayasa §23 #7 zorunlu) · termal aşırılık → K018 cutoff · giriş-üç-kaçak/parazit yasağı (§5 K1 kısıtı "sinyal zincirine parazit yasak") · ASIO exclusive-lock yalnız oynatma-yazılımı (§23 #6 — K002/K015 sınırı) | telemetri olayları (MCU → sürücü/API → K012); üretim-test ölçümleri K020'ye devredilir; canlı metrik altyapısı PLANNED | THD/N · gain eşleşmesi · kanal izolasyonu · DC offset koruma · enable-kesme testleri = **hedef test tanımı** (yazılmadı; "yapıldı" DEĞİL) · hedef ≥80% (anayasa §17) | vault: `.ai/CLAUDE.md §5 K16 + §23 #7` + `00-kspace-anayasa.md §A.1 K016` + `brain.md §5/§8` (H1 bileşen listesi) · ADR: ADR-038/061/063/064/089/090 (accepted/ ls) · repo: `*.cpp/*.c/CMakeLists` → 0 (ls 2026-10-08) · web: ⚠️ VERIFICATION REQUIRED (R9) |

---

## §3 Tam Kimlik Kartı (EK C — 20 alan · R4.3)

| # | Alan | Değer |
|---|---|---|
| 1 | K-ID | K016 |
| 2 | KANONİK_AD | AMPLIFIER |
| 3 | TEATRAL_EPİTET | «ZAR» (yalnız sıfat — K-ID'yi ezmez, R2.3) |
| 4 | DOMAIN | amplifikator |
| 5 | SUBDOMAIN | class-ab-topology · channel-modularity · dac-adc-interface · gain-structure · speaker-subwoofer-out · protection · mcu-telemetry · product-variants |
| 6 | BOUNDED_CONTEXT | Analog Güç Yükseltme — sinyali güçlendirir ve korur; medya işi yapmaz, ağ/aşağı-yazılım kararı vermez, veri saklamaz |
| 7 | RUNTIME | Class AB analog devre (transistör zinciri — brain.md §5 H1 listesi) · STM32/RP2040 MCU telemetri (ADR-089 "hibrit MCU") · firmware (C++20) HEDEF — repo'da dosya YOK (find 0, 2026-10-08) |
| 8 | SORUMLULUK | Class AB (Class D RED — §4.1.2) · DAC Output · ADC Input · Speaker Out · Subwoofer · Multi-Channel (8 modüler) · Gain · Protection (EK A §A.1 K016) · 120 bileşen (anayasa §5 K16) |
| 9 | GIRDI | DAC analog çıkışı (K001 PCM3168A 6-in/8-out · AK4458 opsiyonel 8-kanal — brain.md §8) · ±35V DC güç barı (K017) · termal durum/gereksinim (K018) · enable/gain komutu (sürücü/API/olay üzerinden) |
| 10 | CIKTI | hoparlör + subwoofer analog çıkışı (8 kanal · 50W/kanal @ 8Ω) · koruma durumu (röle/açma) · telemetri olayı (bias, DC offset, akım, sıcaklık) |
| 11 | IZINLI_BAGIMLILIK | K000-K015 aralık (EK A §A.1 "izinli=K000-K015") + port/adapter; somut uçlar: K001 (DAC/ADC/konnektör), K002 (sürücü/enable-telemetri API'si), K017 (güç barı), K018 (termal), K019 (PCB), K003/K015 yalnız Olay/driver-API ile |
| 12 | YASAK_BAGIMLILIK | K016 → K017-K020 sağ/üst katmanlara doğrudan erişim (H20) · **K015/K009/K010 yazılımı ile SENKRON BAĞ** (yalnız sürücü/API/olay — ADR-096 §2.2 GÖREV 07) · H19 veri paylaşımı · K005'e veri yazımı · Class D topoloji · PCM5122 (§21/#22) · sinyal zincirine parazit (§5 K1 kısıtı) |
| 13 | DATA_BOUNDARY | Yalnız fiziksel/telemetri ölçümü: bias · DC offset · akım · sıcaklık · gain ayarı. İş/katalog/medya verisi PAYLAŞMAZ; üretim-test kayıtları K020'ye (ölçüm/seri no), canlı telemetri K012'ye (olay) devredilir; kalıcı DB yazımı K016'da YOK |
| 14 | SECURITY_BOUNDARY | donanım-güvenlik: DC offset >0.5V koruma rölesi zorunlu (anayasa §23 #7) · termal koruma K018 üzerinden · giriş/çıkış kısa-devre (OCP) K017 üzerinden · firmware-erişim/enable komutu sürücü-API sınırında K002/K006 ile hizalı · sinyal/veri bütünlüğü (parazit yasağı, §5 K1) |
| 15 | FAILURE_MODE | fail-safe (fiziksel): DC offset aşımında röle ile çıkış kesme (§23 #7) · termal aşırılıkta K018 cutoff/devre kesme · güç-barı kaybında/K017 OCP'de sessiz-kapanma · kanal-bağımsız enable: hatalı kanal kendi başına kapatılır (§5 K16 kısıtı) · PCM5122 benzeri yanlış-bileşen seçimi → H001 REJECT (§22) |
| 16 | OBSERVABILITY | MCU telemetri olayları (bias/DC offset/akım/sıcaklık → sürücü/API → K012) · üretim-test ölçümleri (K020 AES17 hattı) · canlı metrik/izleme altyapısı PLANNED (K012 K12 satırı) |
| 17 | TEST | **Hedef test tanımları (yazılı değil):** THD/THD+N ölçümü @1W (hedef <0.005%) · SNR ölçümü · 8 kanal gain eşleşmesi · kanal izolasyonu · DC offset koruma rölesi tetikleme · enable-pin kesme · termal-soğuk/sıcak başlangıç · 8Ω yük altında 50W sürme · ADR-090 varyantları (mono…8+1). Durum: TANIMLI, YAZILMADI (repo test altyapısı da yok) |
| 18 | KANIT | vault: `.ai/CLAUDE.md §5 K16 satırı (120 · MJL21194/93 · 50W · 8 kanal · THD<0.005% · tek kanal bağımsız, enable pinli)` + §23 #7 (DC offset rölesi) + §5 H1 satırı (STM32/RP2040 MCU) · `00-kspace-anayasa.md §A.1 K016` · `brain.md §5 (H1 bileşen listesi)` + `§8 (Class AB Amp satırı)` · ADR: ADR-038/061/063/064/089/090 (accepted/ ls) · repo: `*.cpp/*.c/*.ino/CMakeLists.txt/platformio.ini` → 0 (find 2026-10-08) · web: ⚠️ VERIFICATION REQUIRED (R9) |
| 19 | KANIT_TARIHI | 2026-10-08 (vault read + accepted/ ls + repo find) |
| 20 | EPİTET_KALİTE_NOTU | «ZAR» — zar/at metaforu: kader-sınırlı güç hamlesi (topoloji-kesin, sonuç-ölçülebilir); 1 epitet, K-ID'nin yanında (R2.3); EK A §A.0 anahtar satırı: `K016 · AMPLIFIER · «ZAR» · PHYSICAL` |

**R4.4 kart kapıları:**
(a) 20 alanın tamamı dolu — GEÇTİ · (b) IZINLI ∩ YASAK = ∅ — GEÇTİ (IZINLI'daki K015 kaydı
**sürücü/API/olay taşıyıcısıdır**; YASAK'taki K015 kaydı **senkron doğrudan bağı** tikseder —
koşul farklı (taşıyıcı ≠ senkron bağ) → kesişim ∅; H20 aralığı K017-K020 IZINLI aralığının dışında) ·
(c) KANIT 3'lü format — GEÇTİ (vault/ADR | repo (0) | web ⚠️; repo 0 = "yok" kanıtı, R16.3 gereği
IMPLEMENTED yazılmadı) · (d) veri sınırı tek katmana ait (ölçüm/telemetri; üretim kaydı K020'ye,
iş verisi K005'e) — GEÇTİ.

---

## §4 Sorumluluk Derinliği

### §4.1 EK A §A.1 K016 Sorumluluk Maddeleri (anayasa kartı — ana kaynak)

Kaynak: `00-kspace-anayasa.md §A.1` — "Sorumluluk: Class AB · Class D · DAC Output · ADC Input ·
Speaker Out · Subwoofer · Multi-Channel · Gain · Protection".

#### §4.1.1 Kalem tablosu (durum: repo kodu yok → DESIGN/PLANNED — H1)

| Kalem | Ne yapar | Durum | Kanıt |
|---|---|---|---|
| Class AB | asıl topoloji: Darlington çıkış çiftiyle çalışma noktası korumalı yükseltme | DESIGN (karar accepted) | ADR-089 (accepted/ ls) · `.ai/CLAUDE.md §5 K16` · §5 başlık "Class AB Amplifikatör" |
| Class D | (EK A'da adı geçiyor) ana-yasa ve ADR-089 **REDDEDİLMİŞ** topolojidir | REDDEDİLDİ — §4.1.2 | `.ai/CLAUDE.md` (mimari referans: "Amplifikatör topolojisi Class AB (Class D yasak)") · ADR-089 §1.2 topoloji karar maddesi |
| DAC Output | DAC çıkışının amplifikatör girişine sürülmesi | DESIGN | brain.md §8 (PCM3168A 6-in/8-out · AK4458 8-kanal) · ADR-038 (accepted/ ls) |
| ADC Input | geri-besleme/ölçüm giriş yolu | DESIGN | `.ai/CLAUDE.md §5 K1` (PCM3168A ADC 24-bit/96kHz — brain.md §8) |
| Speaker Out | hoparlör çıkışı (50W/kanal @ 8Ω) | DESIGN | `.ai/CLAUDE.md §5 K16` · ADR-089 başlığı (8×50W) |
| Subwoofer | LFE/alt-kanal çıkışı (8.1 — §9 K015/komşu kurgu) | DESIGN | `.ai/CLAUDE.md §9` (8.1 Surround · LFE) · brain.md §9 (8.1 kanal tanımı) |
| Multi-Channel | 8 kanal modüler yapı | DESIGN | `.ai/CLAUDE.md §5 K16` (8 kanal modüler) · ADR-090 (kanal varyantları) |
| Gain | kazanç yapısı/aşamaları | DESIGN (tanım yok → ⚠️) | `00-kspace-anayasa.md §A.1 K016` (Gain maddesi) · somut kazanç değeri ⚠️ |
| Protection | DC offset rölesi · enable-pin kesme · termal/akım korumaları | DESIGN (zorunluluk anayasada) | `.ai/CLAUDE.md §23 #7` · §5 K16 kısıtı (enable pinli) · ADR-089 §1.3 |

#### §4.1.2 Topoloji Çelişkisi Kaydı (Contradiction Gate — anayasa §7 #12 / R8)

| Kaynak | İddia | Durum |
|---|---|---|
| `00-kspace-anayasa.md §A.1 K016` (EK A) | "Sorumluluk: Class AB · **Class D** · …" | EK A kartında Class D adı geçiyor |
| `.ai/CLAUDE.md` (mimari referans · v4.0.0) | "Amplifikatör topolojisi **Class AB (Class D yasak)**" | yasak hükmü |
| ADR-089 §1.2 (accepted/ ls) | topoloji kararı 3 seçenek (AB · D · DC) → karar AB lehine | karar kaynağı |
| **Çözüm (bu dosyada)** | EK A'daki "Class D" kalemi = **tarihsel/envanter adıdır; fiilen REDDEDİLDİ** — uygulanabilir topoloji yalnız Class AB | 👤 onayı ile kesinleştirilir (R8.2 — çelişki defterine not) |

### §4.2 Anayasa §5 K-Matrix Satırı (K16) — 120 bileşen hattının açılımı

Kaynak: `.ai/CLAUDE.md §5` K0-K20 tablosu satırı: **K16 Class AB Amplifikatör | MJL21194/93
Darlington, 50W/kanal, 8 kanal modüler, THD <0.005% | 120 bileşen | Tek kanal bağımsız, enable pinli.**

| Kalem (§5 satırı) | Ne yapar | Durum | Kanıt |
|---|---|---|---|
| MJL21194/93 Darlington | NPN/PNP çift-çıkış aşaması (TO-264) | DESIGN (bileşen seçimi kararlı) | `.ai/CLAUDE.md §5 K16` + §5 H1 · brain.md §5 (Q15 MJL21194 output NPN · Q17 MJL21193 output PNP) · ADR-089 |
| 50W/kanal | kanal başına güç hedefi (@8Ω) | DESIGN (hedef) | `.ai/CLAUDE.md §5 K16` · ADR-089 başlığı (8×50W) |
| 8 kanal modüler | kanal başına bağımsız kart/PCB | DESIGN | `.ai/CLAUDE.md §5 K16` · ADR-089 §1.1 (PROJECTS aktarımı: "her kanal bağımsız PCB") |
| THD <0.005% | bozulma ölçütü | DESIGN (hedef ölçüm — §6.4) | `.ai/CLAUDE.md §5 K16` (§23 #7 ile birlikte ADR-089 §1.1 kaynak zinciri) |
| Kısıt: "Tek kanal bağımsız, enable pinli" | kanal-bağımsızlık + hardware enable | BAĞLAYICI (anayasa kısıtı) | `.ai/CLAUDE.md §5 K16` |
| "120 bileşen" sayımı | §5 K16 bileşen sayısı (envanter — HEDEF) | HEDEF · alt-döküm ⚠️ | `.ai/CLAUDE.md §5 K16 satırı` · H10: hedef ≠ kanıt (repo 0 dosya; brain.md H1 listesi 7 kalem + ADR-089 devresi) |

### §4.3 Alt-Sistem Bazlı Derinlik (10 kalem)

#### §4.3.1 Topoloji · Class AB (Darlington)

| Boyut | İçerik |
|---|---|
| Kural | Class AB: çıkış aşamasında çalışma noktası bias ile korunur (crossover bozulma düşük tutulur) |
| Bileşenler (brain.md §5 H1) | Q1/Q2 BC546B diferansiyel çift · Q5 BC556B akım havuzu · Q9 KSC3503 VAS · Q10 BD139 Vbe çarpımı · Q15 MJL21194 (output NPN) · Q17 MJL21193 (output PNP) |
| Durum | DESIGN — devre şeması kararında (ADR-089 accepted); repo'da devre dosyası/firmware yok |
| Edge case | bias drift (sıcaklık) · crossover bozulması · çift-taraflı asimetri |
| Ölçüm hedefi | THD <0.005% (§5 K16) · THD+N <0.01% (brain.md §8 — farklı metrik, §4.3.9) |
| Kanıt | brain.md §5 (H1 listesi) · `.ai/CLAUDE.md §5 K16` · ADR-089 (accepted/ ls) |

#### §4.3.2 Topoloji Reddi · Class D ve PCM5122

| Boyut | İçerik |
|---|---|
| Kural | topoloji yalnız Class AB; Class D yasak; PCM5122 (8.1 için 2 kanal) yasaklı desen |
| Red nedenleri | anayasa: "Class D yasak" · ADR-089 §1.2 topoloji kararı (AB vs D vs DC → AB) · §21/§22: PCM5122 → "H001 REJECT" (yalnız PCM3168A/AK4458) |
| Durum | REDDEDİLDİ (karar) — uygulama zaten yok (repo 0) |
| Edge case | gelecekte D-sınıfı teklifi → ADR'siz karar YASAK (H08/R10); Frozen ADR düzenlenmez |
| Kanıt | `.ai/CLAUDE.md §21` + §22 + §23 #4 · ADR-089 (accepted/ ls) · ADR-038 (accepted/ ls) |

#### §4.3.3 Kanal Mimarisi · 8 Modüler + Enable

| Boyut | İçerik |
|---|---|
| Kural | her kanal bağımsız kart/PCB; her kanal bağımsız enable ile açılıp kapanır (§5 K16 kısıtı) |
| Durum | DESIGN (8 bağımsız kart = ADR-089 §1.1 · PROJECTS aktarımı) |
| Edge case | tek kanal arızasının gruba etkisi (izolasyon) · enable sırası (açılış/kapanış) · kanal-eşleşmeyen gain |
| Etkileşim | 8.1 kurgusunda 8 kanal + LFE (brain.md §9); varyantlar ADR-090 |
| Kanıt | `.ai/CLAUDE.md §5 K16` · ADR-090 (accepted/ ls) · brain.md §9 |

#### §4.3.4 Giriş/Çıkış · DAC Output / ADC Input

| Boyut | İçerik |
|---|---|
| Kural | DAC çıkışı amplifikatör girişine; ADC geri-besleme/ölçüm için (K001 bileşenleri) |
| Bileşenler | PCM3168A (6-in/8-out codec, 24-bit, DAC 192kHz/ADC 96kHz, SNR 112dB DAC — brain.md §8) · AK4458 (opsiyonel 8-kanal 32-bit/768kHz) |
| Durum | DESIGN (bileşen seçimi ADR-038 kararı) |
| Edge case | seviye uyumu (line-level ↔ güç aşaması) · impedance eşleşmesi · kanal-sayı uyumu (8-out) |
| Kanıt | ADR-038-8-1-sound-card-chip-selection (accepted/ ls) · brain.md §8 · `.ai/CLAUDE.md §5 K1` |

#### §4.3.5 Çıkış · Speaker Out / Subwoofer

| Boyut | İçerik |
|---|---|
| Kural | hoparlör çıkışı 50W/kanal @ 8Ω; LFE/subwoofer ayrı kanal (8.1) |
| Durum | DESIGN (hedef) |
| Edge case | 4Ω yük (güç artışı/termal yük — K018) · kısa devre (K017 OCP) · subwoofer crossover (K003 DSP işi — §9 Linkwitz-Riley 80Hz) |
| Sınır | crossover/filtre K003'ün; K016 yalnız yükseltir |
| Kanıt | `.ai/CLAUDE.md §5 K16` + §9 · brain.md §9 (bass management 80Hz) |

#### §4.3.6 Kazanç · Gain Yapısı

| Boyut | İçerik |
|---|---|
| Kural | giriş/ ara- aşama kazançları; hedef SNR ile uyumlu |
| Durum | DESIGN — **somut kazanç değeri/ayar tanımı vault'ta yok → ⚠️** (EK A "Gain" maddesi tek başına) |
| Edge case | kazanç-farkı (kanallar arası eşleşme) · tam-kuvvet/distorsiyon eşiği |
| Ölçüm hedefi | kanal gain eşleşmesi testi (§6.4) |
| Kanıt | `00-kspace-anayasa.md §A.1 K016` · ⚠️ VERIFICATION REQUIRED (değer için research/A-DR kapısı) |

#### §4.3.7 Koruma · DC Offset Rölesi + Enable

| Boyut | İçerik |
|---|---|
| Kural | **Class AB amfide >0.5V DC offset → koruma rölesi** (anayasa §23 #7 — bağlayıcı uyarı) |
| Durum | DESIGN (zorunluluk anayasada; devre/firmware uygulaması yok) |
| Edge case | röle-kaynaştırma (welding) · açılma gecikmesi (hoparlör koruması vs. pop) · yanlış tetik (titreşim) |
| İlişki | enable-pinli kanal kesme (§5 K16 kısıtı) ile iki kademeli koruma: röle (analog) + enable (MCU) |
| Kanıt | `.ai/CLAUDE.md §23 #7` · ADR-089 §1.1 (PROJECTS: "KSD301 + DC offset koruma rölesi") |

#### §4.3.8 Kontrol · Hibrit MCU Telemetri (STM32/RP2040)

| Boyut | İçerik |
|---|---|
| Kural | hibrit MCU zinciri: analog zincir + STM32/RP2040 ile telemetri/enable (ADR-089 "Hibrit MCU") |
| Durum | DESIGN — firmware (C++20) repo'da YOK (find `*.cpp/*.c/*.ino` → 0, 2026-10-08) |
| Edge case | MCU-kaybında analog zincirin davranışı (fail-safe mantığı ⚠️) · telemetri-gecikmesi · ADC-kanal çarpışması (ölçüm MUX) |
| Sınır | telemetri olayı yukarı (K012); komut aşağı yalnız sürücü/API (K002) |
| Kanıt | `.ai/CLAUDE.md §5 H1` (C++20, STM32/RP2040 MCU) · ADR-089 başlığı · repo find 0 |

#### §4.3.9 Ölçütler · THD / SNR (ayrı ölçütler — ⚠️ uzlaştırma)

| Kaynak | Ölçüt | Not |
|---|---|---|
| `.ai/CLAUDE.md §5 K16` | THD <0.005% | anayasa satırı — bağlayıcı |
| `brain.md §8` (Class AB Amp satırı) | THD+N <0.01% · SNR >100dB · 50W @ 8Ω · ±35V DC | farklı metrik (THD+N vs THD) |
| ADR-089 başlığı | 120dB+ hedefi | §1.2: "120dB+ hedefinin ne anlama geldiği" — ADR içinde tanımlanacak |
| ADR-089 §1.1 (PROJECTS L305-311 aktarımı) | SNR >105dB · "Durum: TASARIM AŞAMASINDA" | proje tablosu aktarımı |
| **Kayıt notu** | üç ölçüt (0.005% · 0.01% · 100/105/120dB) ayrı kaydedildi; **uzlaştırma ⚠️ (Vault Steward)** — sayılar birleştirilmedi (H10) | `rules.md R9.4` |

#### §4.3.10 Ürün Ailesi · Kanal Varyantları (ADR-090)

| Boyut | İçerik |
|---|---|
| Kural | varyant seti: mono · 2 · 2+1 · 4 · 5 · 6 · 7 · 8 · 7+1 · 8+1 (ADR-090 başlığı) |
| Durum | DESIGN (karar accepted) |
| Edge case | varyant-başına BOM/maliyet farkı (K020) · firmware-konfigürasyon ayrımı · stok/SKU |
| İlişki | K020 üretim/BOM varyant yönetimi; K017 güç-barı ortaklığı |
| Kanıt | ADR-090-channel-variant-product-family (accepted/ ls) |

### §4.4 Kapsam Dışı / Sınır Tanımı (K016'un YAPMADIĞI)

| Aday konu | Neden K016 değil | Asıl sahip | Kanıt |
|---|---|---|---|
| EQ/reverb/limiter/crossover (DSP) | sinyal işleme | K003 AUDIO ENGINE | `.ai/CLAUDE.md §5 K3` + §19 |
| DAC/ADC çipi kendisi (donanım) | donanım-alt-sistem | K001 HARDWARE | `.ai/CLAUDE.md §5 K1` |
| Güç barı üretimi (±35V) | güç kaynağı katmanı | K017 POWER | `.ai/CLAUDE.md §5 K17` |
| Soğutma/heatsink/fan | termal katman | K018 THERMAL | `.ai/CLAUDE.md §5 K18` |
| PCB dizaynı/üretimi | PCB katmanı | K019 PCB | `.ai/CLAUDE.md §5 K19` |
| BOM/montaj/seri test | üretim katmanı | K020 MANUFACTURING | `.ai/CLAUDE.md §5 K20` |
| ASIO/WASAPI oynatma + exclusive lock | sürücü/oynatma yazılımı | K002/K015 | `.ai/CLAUDE.md §23 #6` |

### §4.5 Durum Özeti & Risk Gerekçesi (H1 — hedef ≠ kanıt)

| Ölçüm | Değer |
|---|---|
| Anayasa §5 K16 hedefi | 120 bileşen (envanter sayımı — HEDEF) |
| Repo kanıtı (find 2026-10-08) | `*.cpp/*.c/*.ino` + `CMakeLists.txt` + `platformio.ini` → **0** (firmware/devre-yazılımı yok) |
| Karar kanıtı | ADR-089 (accepted) · ADR-090 (accepted) · ADR-038/061/063/064 (accepted) — ls 2026-10-08 |
| Durum dağılımı | DESIGN: 8 kalem (topoloji · kanal · giriş/çıkış · çıkış · koruma · MCU · ölçütler · varyant) · PLANNED: 1 (gain değeri — ⚠️) · REDDEDİLDİ: 1 (Class D) · IMPLEMENTED: 0 (kod yok) |
| Ölçüm kayıtları | brain.md §5 "Electronics Registry" yolları (`electronics/*.md`) **diskte YOK** (ls: `.ai/electronics/` yok, 2026-10-08) → ölü atıf, §7.1 G6 |
| Risk gerekçesi (medium) | anayasa §23 #7 gibi tanımlı koruma var, güç-barı riski K017'de; K016 doğrudan kullanıcı-güvenlik bypass'ı üretmez → medium (K017 high ile ayrışır) |
| Web research | 0 URL bu dosyada → ⚠️ (R9 3'lü eksik; F1 EK B kapısı) |

### §4.6 K016 ADR Haritası (`.ai/.decisions/accepted/` ls-verified 2026-10-08)

| ADR | Karar özeti (dosya başlığı) | K016 etkisi |
|---|---|---|
| ADR-038-8-1-sound-card-chip-selection | 8.1 ses kartı çipi seçimi | DAC/ADC giriş-çıkış bileşenleri (PCM3168A/AK4458) |
| ADR-061-electronics-architecture | Electronics Architecture (L6) — katman tanımı, kart/modül hiyerarşisi, bölüm sınırı, bileşen seçim politikası | K016'nın elektronik-katman içindeki yeri + bileşen seçim politikası |
| ADR-063-hardware-design-standards | PCB kural seti · sinyal bütünlüğü & EMC · bileşen/kart kalitesi · dokümantasyon · AES17 fabrika test hizası | ölçüm/test disiplini (§6.4) + K019/K020 ile ortak |
| ADR-064-electronics-platform-architecture | Electronics Platform (L0-L6 · 5 cihaz sınıfı · cihaz↔servis eşleme) | platform-sınıfı eşlemesi (K016'nın hangi cihaz sınıfında) |
| ADR-089-classab-24v | Class AB Amplifikatör Sistemi (8×50W, 120dB+, 12–24V boost, hibrit MCU) | **ana karar**: topoloji + güç + MCU |
| ADR-090-channel-variant-product-family | Kanal varyant ürün ailesi (mono…8+1) | ürün ailesi (§4.3.10) |
| ADR-096-kspace-5000-boundary-model | K-Space V2 · dizin deseni · çift uçak (§2.2) | PHYSICAL plane kuralı + format kaynağı |

### §4.7 K016 Arayüz Sözleşmeleri (komşularla sınır — çift uçak)

| Komşu | Arayüz | K016'nın verdiği | K016'nın beklediği | Kanıt |
|---|---|---|---|---|
| K001 HARDWARE | fiziksel arayüz (DAC/ADC, konnektör, hoparlör) | analog giriş/çıkış yükü | PCM3168A/AK4458 seviyeleri (line-level) | `.ai/CLAUDE.md §5 K1` · ADR-038 |
| K002 DRIVERS | sürücü/API (enable · telemetri) | durum/ölçüm olayları | komut kanalı (enable/gain) — senkron K015'e değil | ADR-096 §2.2 (yalnız driver/API) |
| K003 AUDIO ENGINE | talep/olay (crossover · seviye) | yükseltilmiş sinyal (fiziksel) | DSP çıktısı (bass management 80Hz) | brain.md §9 · `.ai/CLAUDE.md §19` |
| K015 MEDIA | **olay/sürücü-API yalnız (senkron YOK)** | oynatma-durumu geri bildirimi | enable/ölçüm olayları | ADR-096 §2.2 · R6.2 |
| K017 POWER | güç barı (±35V DC) | güç tüketimi/çıkış-akımı olayı | temiz, korumalı bar (UVP/OVP/OCP/OTP) | `.ai/CLAUDE.md §5 K17` |
| K018 THERMAL | termal durum/sınır | ısı üretimi verisi (W) | heatsink/fan + cutoff (KSD301) | `.ai/CLAUDE.md §5 K18` |
| K019 PCB | kart geometrisi/örtü | yerleşim/iş akımı gereksinimleri | 6-layer, 2oz, star ground, thermal vias | `.ai/CLAUDE.md §5 K19` |
| K020 MANUFACTURING | üretim test/ölçüm | ölçüm değerleri (THD/DC offset) | AES17 üretim-test hattı + BOM | ADR-063 (accepted/ ls) |

---

## §5 Bağımlılık & Sınır

### §5.1 İZİNLİ Bağımlılıklar (EK A aralık — alt katmanlar + port/adapter)

| Hedef | Yön | Gerekçe | Kanıt |
|---|---|---|---|
| K016 → K001 HARDWARE | aşağı | DAC/ADC/konnektör/sensör arayüzü | `00-kspace-anayasa.md §A.1 K016 "izinli=K000-K015"` · ADR-096 §2.2 |
| K016 → K002 DRIVERS | aşağı (sürücü/API ucu) | enable/telemetri komut kanalı | ADR-096 §2.2 (yalnız driver/API sınırı) |
| K016 → K000 OS | aşağı | MCU/runtime ortamı (telemetri süreçleri) | anayasa §A.1 K000 |
| K016 → K003/K015 | aşağı (olay/sürücü-API) | talep/geri-bildirim — **senkron bağ YOK** | ADR-096 §2.2 (çift uçak — GÖREV 07) |
| K016 ← K017/K018/K019 (fiziksel besleme yönü) | karşı-yöne gelen güç/termal/PCB hizmeti | K016 bu katmanların çıktısını TÜKETİR (±35V bar · soğutma · kart); K016'nın onlara erişimi/çağrısı H20 ile YASAK'tır (§5.2) — bağımlılık yazımı EK A aralığı (K016 izinli = K000-K015) ile sınırlıdır | `.ai/CLAUDE.md §5 K17/K18/K19` · `00-kspace-anayasa.md §A.1 K016` · `rules.md R6.1` |
| port/adapter | yan | EK A istisnası (R6.3) | `00-kspace-anayasa.md §A.1 K016` |

**Çift uçak notu (ADR-096 §2.2 · GÖREV 07):** K016 PHYSICAL PLANE'dedir. K015 MEDIA (yazılım)
ile ilişkisi yalnız sürücü/API/olay üzerinden kurulur; **senkron doğrudan bağlantı (fonksiyon
çağrısı, veri paylaşımı) yasaktır** — ihlalde R6.5 (revert + CRITICAL log).

### §5.2 YASAK Bağımlılıklar

| Yasak | Gerekçe | Kanıt |
|---|---|---|
| K016 → K017-K020 doğrudan erişim / geri çağrı (H20) | klasik yön (üst/sağ katmana erişim yok) | `rules.md R6.1` · `ADR-096 §2` |
| K015/K009/K010 ile SENKRON BAĞ | çift uçak ihlali (PHYSICAL ↔ SOFTWARE senkron yasak) | `ADR-096 §2.2` · `rules.md R6.1` |
| H19 doğrudan veri paylaşımı | veri sınırı ihlali (R4.4d) | `rules.md R6.1` · `ADR-096 §2` |
| Class D topolojisi | anayasa: "Class D yasak"; ADR-089 AB kararı | `.ai/CLAUDE.md` (mimari referans) · ADR-089 |
| PCM5122 kullanımı | §21 yasaklı desen · §22 H001 REJECT | `.ai/CLAUDE.md §21` + §22 |
| Sinyal zincirine parazit | §5 K1 kısıtı (DC-Only + Class AB) | `.ai/CLAUDE.md §5 K1` |
| K005'e veri yazımı / iş verisi saklama | veri sınırı: K016 yalnız ölçüm/telemetri | EK A K016 · H19 |
| `SELECT *` / ORM / framework (yazılım tarafında) | ADR-001/002 mutlak yasakları | `rules.md R17` |

### §5.3 Boundary Matrisi

| Boundary | K016 Tanımı | Komşu sahip |
|---|---|---|
| DATA_BOUNDARY | fiziksel ölçüm/telemetri (bias · DC offset · akım · sıcaklık · gain); DB yazımı yok | K020 (üretim-test kaydı) · K012 (olay) · K005 (iş verisi) |
| SECURITY_BOUNDARY | DC offset >0.5V rölesi zorunlu (§23 #7) · enable-kesme · parazit yasağı (§5 K1) · PCM5122/Class D reddi | K017 (OCP/UVP/OVP) · K018 (termal cutoff) · K006 (komut-erişimi) |
| FAILURE_MODE | fail-safe fiziksel: röle ile DC kesme · kanal-bağımsız enable · güç/termal kaybında sessiz-kapanma | K017/K018 (kaynak korumaları) · K012 (olay) |
| RUNTIME boundary | analog zincir + MCU telemetri süreçleri; firmware repo'su yok | K000 (runtime) · K002 (sürücü yaşam döngüsü) |
| MEASUREMENT boundary | THD/SNR/gain ölçümleri üretim-testine (K020) ait; saha ölçümü AES17 | K020 (test) · K013 (test otomasyonu) |
| Olay (event) yukarı serbest | telemetri/koruma olayları yukarı; senkron geri çağrı yasak | K008 (Event Bus) · K012 (izleme) |

**Sınır ihlali prosedürü:** tespit → derhal revert + `log.md` CRITICAL + 👤 bilgi (R6.5 · anayasa §5.1).

### §5.4 Olay (Event) Akışı — yukarı serbest, aşağı senkron yasak (R6.2)

```text
K012 OBSERVABILITY  ← (olay yayını, yukarı SERBEST)  ←  K016 telemetri/koruma olayları
      ↑                                                        │
      │ (okuma)                                      [senkron K015/K009'a YASAK]
      └────────── K008 SERVICES (Event Bus) ──────────┘
                        │
  K016 yalnız alttan beslenir: K001 sinyal · K017 güç · K018 termal · K002 sürücü-API  (aşağı ↓ izinli)
```

| Akış | Yön | Kural | Kanıt |
|---|---|---|---|
| K016 → K012 telemetri/koruma olayı | yukarı | olay yayını yukarı serbest (R6.2) | `rules.md R6.2` |
| K016 → K015/K009 senkron çağrı | — | YASAK (çift uçak + H20) | `ADR-096 §2.2` · `rules.md R6.1` |
| K003 → K016 çıkış talebi (olay) | aşağı | talep olayı iner; yükseltme fizikseldir | `.ai/CLAUDE.md §5 K3` |
| K017 → K016 güç barı | aşağı | ±35V DC | `.ai/CLAUDE.md §5 K17` |
| K018 → K016 termal kesme | aşağı | cutoff sinyali fizikseldir | `.ai/CLAUDE.md §5 K18` |

### §5.5 Kademe / Zıplama Notu (R6.3 — 9 kademeli hiyerarşi)

| Kademe | K016 karşılığı | Not |
|---|---|---|
| K-Layer → Domain → Subdomain | Bant 1 / K016 / class-ab · channels · gain · protection · telemetry | SUBDOMAIN §3/5 |
| Module → Component | kanal modülü (bağımsız kart) · koruma modülü (röle/enable) · MCU telemetri modülü | hedef (devre seviyesi) |
| Service | (donanım katmanı — servis değil; sürücü-API K002'de) | çift uçak |
| Adapter | sürücü/API adaptörü (enable/telemetri) · üretim-test adaptörü (K020) | port/adapter sıçraması |
| Döngü toleransı | sıfır (R6.6) | `rules.md R6.6` |

### §5.6 K016 Risk Güvenlik Notları (anayasa §23 hizası)

| Risk | Etki | Azaltma | Kanıt |
|---|---|---|---|
| DC offset hoparlöre ulaşma | hoparlör/donanım hasarı | **>0.5V → koruma rölesi** (zorunlu) | `.ai/CLAUDE.md §23 #7` |
| Yanlış topoloji/bileşen (Class D · PCM5122) | sistem hatası (H001 REJECT) | anayasa yasağı + ADR-089 | `.ai/CLAUDE.md §21/#22` · ADR-089 |
| ASIO exclusive lock (oynatma) | kesinti/kilit çakışması | oynatma katmanı politikası (K002/K015) | `.ai/CLAUDE.md §23 #6` |
| Ölçüt ikilemi (THD 0.005% vs THD+N 0.01%; SNR 100/105/120dB) | yanlış kabul kriteri | ayrı kayıt + ⚠️ uzlaştırma (§4.3.9) | `rules.md R9.4` |
| Sinyal zincirine parazit (güç/RF) | gürültü/ses kalitesi bozulması | §5 K1 kısıtı + K019 star ground | `.ai/CLAUDE.md §5 K1/K19` |

---

## §6 Girdi-Çıktı · Runtime · Observability-Test

### §6.1 Girdi → Çıktı Haritası

| Aşama | Girdi | Çıktı / Devir | Sınır notu |
|---|---|---|---|
| Giriş (DAC→amp) | PCM3168A/AK4458 line-level | yükseltilmiş kanal sinyali | K001 arayüzü |
| Bias/çalışma noktası | bias devresi + sıcaklık | distorsiyon-düşük çıkış | §4.3.1 |
| Çıkış aşaması | MJL21194/93 çifti | 50W/kanal @ 8Ω | §5 K16 |
| Enable/kesme | MCU/sürücü komutu | kanal açma/kapama | §5 K16 kısıtı |
| Koruma | DC offset/akım/termal ölçümü | röle açma / enable kesme | §23 #7 |
| Telemetri | ölçüm ADC'leri | olay (yukarı) | K012 |
| Subwoofer/çıkış | LFE kanal girdisi | subwoofer çıkışı | §9 (8.1) |

### §6.2 Runtime

| Öğe | Değer | Kanıt |
|---|---|---|
| Topoloji | Class AB (Darlington çıkış) | `.ai/CLAUDE.md §5 K16` · ADR-089 |
| Güç | ±35V DC bar (K017'den) · 6S LiPo/12-24V giriş zinciri | `.ai/CLAUDE.md §5 K17` · ADR-089 başlığı |
| Kontrol | STM32/RP2040 MCU (hibrit) — firmware C++20 HEDEF | `.ai/CLAUDE.md §5 H1` · repo find 0 |
| Kanal | 8 modüler · varyantlar mono…8+1 (ADR-090) | ADR-090 (accepted/ ls) |
| Çıkış sınıfı | 50W/kanal @ 8Ω · THD <0.005% hedef | `.ai/CLAUDE.md §5 K16` |

### §6.3 Observability

| Sinyal | Kaynak | Durum |
|---|---|---|
| Telemetri (bias/DC offset/akım/sıcaklık) | MCU → sürücü/API → K012 | PLANNED (firmware yok) |
| Koruma olayı (röle/enable) | koruma devresi + MCU | PLANNED |
| Üretim-test ölçümleri | AES17 hattı (ADR-063) → K020 kaydı | PLANNED (K020) |
| Canlı metrik/izleme | Prometheus/Grafana (§5 K12) | PLANNED |
| Saha arıza kaydı | field service (K020) | PLANNED |

### §6.4 Test (Hedef Test Tanımları — "yapıldı" DEĞİL)

| # | Test | Ölçüt/.acceptance | Durum |
|---|---|---|---|
| 1 | THD ölçümü @1W | <0.005% (§5 K16) | TANIMLI, YAZILMADI |
| 2 | THD+N ölçümü | <0.01% (brain.md §8 — ⚠️ uzlaştırma §4.3.9) | TANIMLI, YAZILMADI |
| 3 | SNR ölçümü | 100/105/120dB adayları (§4.3.9 — ⚠️) | TANIMLI, YAZILMADI |
| 4 | Güç çıkışı | 50W/kanal @ 8Ω | TANIMLI, YAZILMADI |
| 5 | 8 kanal gain eşleşmesi | kanallar arası sapma eşiği ⚠️ (değer yok) | TANIMLI (ölçüt eksik) |
| 6 | Kanal izolasyonu | hatalı kanalın komşuya sızıntısı ⚠️ | TANIMLI (ölçüt eksik) |
| 7 | DC offset koruma rölesi | >0.5V → röle açar (§23 #7) | TANIMLI, YAZILMADI |
| 8 | Enable-kesme | komutla kanal kapanır (§5 K16 kısıtı) | TANIMLI, YAZILMADI |
| 9 | Termal-soğuk/sıcak başlangıç | bias drift sınırı ⚠️ | TANIMLI (ölçüt eksik) |
| 10 | Varyant kapsamı | ADR-090 seti (mono…8+1) | TANIMLI, YAZILMADI |

### §6.5 Failure Mode Senaryoları (failure=fail-safe fiziksel)

| # | Senaryo | K016 davranışı | Sistem etkisi | Kanıt |
|---|---|---|---|---|
| 1 | DC offset >0.5V | koruma rölesi açar → çıkış kesilir | hoparlör korunur | `.ai/CLAUDE.md §23 #7` |
| 2 | Tek kanal arızası | o kanal enable kapanır (kanal-bağımsız) | diğer kanallar devam | `.ai/CLAUDE.md §5 K16 kısıtı` |
| 3 | Güç barı kaybı (K017 OCP/UVP) | güç yok → sessiz-kapanma | çıkış kesilir (pop riski ⚠️) | `.ai/CLAUDE.md §5 K17` |
| 4 | Termal aşırılık | K018 cutoff sinyali → kesme | kanal/grup kapanır | `.ai/CLAUDE.md §5 K18` |
| 5 | Yanlış bileşen (PCM5122) | tasarım-red (H001 REJECT) | ürün reddi | `.ai/CLAUDE.md §22` |
| 6 | MCU kilitlenmesi | koruma zincirinin davranışi ⚠️ (fail-safe tasarımı tanımsız) | belirsiz | ⚠️ (firmware yok) |
| 7 | Termal-soğuk açılış | bias drift → bozulma artışı riski | kalite kaybı | ⚠️ (ölçüt yok §4.3.9) |
| 8 | 4Ω yük (beklenmedik) | akım/termal yük artışı → K017/K018 korumaları | kısıtlama/kesme | ⚠️ (yük-sınırı vault'ta yok) |

### §6.6 Uyum & Kapı Bağlantısı (R11)

| Kapı/Kural | K016 durumu |
|---|---|
| KAPI 1 vault oku | TAM — anayasa §A.1 K016 + §5/§21/§22/§23 + brain.md + rules.md + ADR-096 + ADR-089 başlık/§1 okundu |
| KAPI 9 hallucination damgası | `⚠️` ayağlar §7.1'de (gain değeri · ölçüt uzlaştırması · MCU fail-safe · 4Ω sınırı · electronics/* ölü yolları · web kaynağı) |
| KAPI 10 kullanıcı onayı | BEKLİYOR — `status: draft`, bant onayı 👤 (R10) |
| Guardrail #3 (Zero-Hallucination) | Uygun — repo 0 sonucu "yok" olarak yazıldı; IMPLEMENTED iddiası yok |
| R16.3 (durum etiketi) | Uygun — tüm kalemler DESIGN/PLANNED/RED (repo dosya yolu kanıtı yok) |
| H10 (hedef ≠ kanıt) | Uygun — 120 bileşen hedefi ile repo 0 / brain listesi ayrı satırlarda |
| R8 (çelişki) | 2 açık kayıt: Class D (EK A vs yasak — §4.1.2) · ölçütler (§4.3.9) → 👤 |

### §6.7 Kabul Kriterleri (katman `ACTIVE` eşiği — R16.2 + R4.4)

| # | Kriter | Ölçüt | Mevcut |
|---|---|---|---|
| K1 | Özet satır 16 alan | tam dolu · İZİNLİ∩YASAK=∅ | dolu (§2) · kesişim yok (gerekçe §3b) |
| K2 | EK C 20 alan kart | tam dolu · KANIT 3'lü | dolu (§3) |
| K3 | Çift uçak notu | PHYSICAL + senkron-yasak yazılı | dolu (§1/§5.1/§5.4) |
| K4 | Veri sınırı | ölçüm/telemetri; DB yazımı yok | dolu (§3/13 · §5.3) |
| K5 | Topoloji kararı | Class AB + Class D/PCM5122 reddi | dolu (§4.1.2 · §5.2) |
| K6 | Web research (R9 3'lü) | iddiaların ≥%80'i kaynaklı | değil → ⚠️ (G6, F1 EK B kapısı) |
| K7 | Durum geçişi | draft → ACTIVE = 👤 + kanıt | bekliyor (Kapı 10) |

---

## §7 Kanıt Kaynakları

| # | Kaynak | Tür (R9.3-lü) | Kullanım |
|---|---|---|---|
| 1 | `00-kspace-anayasa.md §A.1 K016 - AMPLIFIER «ZAR»` (+ §A.0 anahtar satırı: PHYSICAL) | dosya yolu (vault read 2026-10-08) | Sorumluluk/Sınır/Kanıt — ana kaynak |
| 2 | `.ai/CLAUDE.md §5` (K16 + H1 satırları · K17/K18/K19 komşu satırlar) · `§21/#22/#23` (yasaklar + DC offset) · `§9` (8.1) · `§19` (standartlar) | dosya yolu (vault read 2026-10-08) | K-matrix + koruma + yasaklar |
| 3 | `brain.md §5` (Electronics Registry + H1 bileşen listesi) · `§8` (Class AB Amp · PCM3168A/AK4458/PCM5122) · `§9` (8.1 kanal) | dosya yolu (vault read 2026-10-08) | bileşen/ölçüt detayı |
| 4 | repo find: `*.cpp/*.c/*.ino` + `CMakeLists.txt` + `platformio.ini` → 0 | repo grep/find (2026-10-08) | IMPLEMENTED iddiasının yokluğu |
| 5 | `.ai/.decisions/accepted/` ls (2026-10-08): ADR-038 · ADR-061 · ADR-063 · ADR-064 · ADR-089 · ADR-090 · ADR-096 | ADR (ls teyitli) | karar atıfları |
| 6 | `rules.md R2/R3/R4/R6/R8/R9/R10/R16.3` · `ADR-096 §2.4/§2.5 + §2.2 (çift uçak)` | dosya yolu (vault read) | format + yön + çelişki prosedürü |
| 7 | F1 (`2026-10-08-master-prompt-v2.2.0-f1.md`) EK B research defteri (37 URL, 2026-10-08) | URL (vault arşivi) | web research üssü |

### §7.1 Açık Kanıt Boşlukları (⚠️ defteri — R4.4c / R9.2)

| # | Boşluk | Etkilenen alan | Kapanış yolu |
|---|---|---|---|
| G1 | Gain değeri/ayar tanımı vault'ta yok | EK C 9 (GIRDI) · §4.3.6 | devre/ölçüm kaynağı + 👤 karar |
| G2 | Ölçüt uzlaştırması (THD/THD+N · SNR 100/105/120dB) | §4.3.9 · §6.4 #1-3 | Vault Steward kararı (H10/R9.4) |
| G3 | MCU fail-safe davranışı tanımsız (firmware yok) | §6.5 #6 · EK C 15 | firmware tasarımı + ADR |
| G4 | 4Ω/kritik yük sınırı vault'ta yok | §6.5 #8 | güç/termal hesabı + ölçüt |
| G5 | brain.md "Electronics Registry" yolları (`electronics/*.md`) diskte YOK | §4.5 · KANIT zinciri | dosyaların geri gelmesi veya atıfın güncellenmesi |
| G6 | Web kanıtı (URL+tarih) — topoloji/bileşen/ölçüt iddiaları | KANIT web ayağı | F1 EK B research kapısı (R14) |
| G7 | Class D (EK A) ↔ "Class D yasak" çelişkisinin resmî kaydı | §4.1.2 | R8.2: çelişki defteri + 👤 onayı |
| G8 | EK A'nın "Class D" maddesinin anayasa-ekinde düzeltilmesi | anayasa (kapsam dışı — dokunulmaz bu görevde) | Vault Steward onayıyla anayasa güncellemesi |

**Kural hatası:** Bu boşluklar dosyayı geçersiz kılmaz (R4.4c: `⚠️` R14'te doldurulur); ancak
KAPI 9/10'dan önce kapatılmadan katman `ACTIVE` olamaz (R16.2).

---

## §8 İlişki & Değişiklik

**Kardeş katmanlar (Bant 1 · K000-K020 — düz metin, wiki-link YASAK, R2.4):**
Alt (izinli): K000 OS «TEMEL TAŞI» · K001 HARDWARE «DEMİRHANE» · K002 DRIVERS «KANAT» ·
K003 AUDIO ENGINE «ÇEKİRDEK» · K004 AI «MIKNATIS» · K005 DATA «FENER» · K006 SECURITY «ÇELİK KAPI» ·
K007 MIDDLEWARE «SUR» · K008 SERVICES «KULE» · K009 API «HÜCRE» · K010 APPLICATION «DÜĞÜM» ·
K011 UX «OMURGA» · K012 OBSERVABILITY «AYNA» · K013 CI/CD «PUSULA» · K014 NETWORK «ÇARK» ·
K015 MEDIA «MÜHÜR» (senkron bağ YOK — sürücü/API/olay, çift uçak).
Sağ/üst (H20 yasak yönü): K017 POWER «KANTAR» · K018 THERMAL «MEZİT» · K019 PCB «ALEV» ·
K020 MANUFACTURING «BUZUL».
İlişki türü: kardeş katmanlar arası yalnız `refers-to` (doküman linki), `depends-on` DEĞİL (R6.4).

**İzinli wiki-linkler:** [[architecture/00-kspace-anayasa]] · [[architecture/rules]] · [[architecture/00-master-index]]

**Bant-1 üretim durumu (2026-10-08 · staging, vault'a yazılmadı):**

| Dosya | Katman | Durum |
|---|---|---|
| b1-K014-ag.md | K014 NETWORK «ÇARK» | band-1 setinin parçası |
| b1-K015-medya.md | K015 MEDIA «MÜHÜR» | band-1 setinin parçası |
| b1-K016-amplifikator.md | K016 AMPLIFIER «ZAR» | bu dosya (draft) |
| b1-K017-guc-kaynagi.md | K017 POWER «KANTAR» | band-1 setinin parçası |
| b1-K018-termal.md | K018 THERMAL «MEZİT» | band-1 setinin parçası |
| b1-K019-pcb.md | K019 PCB «ALEV» | band-1 setinin parçası |
| b1-K020-uretim.md | K020 MANUFACTURING «BUZUL» | band-1 setinin parçası |

**Geçmiş:** 2026-10-08 ilk üretim (bant-1 · staging) | Vault Steward.
