---
title: "K001 HARDWARE — Katman Index"
type: index
category: architecture
version: "1.0.0"
status: draft
authority: "SSOT: .ai/architecture/K001-donanim/index.md — K-space band-1 katmanı (ADR-096)"
updated: 2026-10-08
tier: 3
domain: K001-donanim
ssot: true
risk: medium
owner: audio-hw
depends-on: [".ai/architecture/00-kspace-anayasa.md"]
---

# K001 HARDWARE — Katman Index

**Künye**

| Alan | Değer |
|---|---|
| Kategori | architecture (katman `index.md`) |
| Durum | draft (FM `status: draft` · katman durumu = PROPOSED — R16.2, 👤 onayı bekliyor) |
| Tarih | 2026-10-08 |
| Yazar | Claude — Band-1 üretim ajanı (F1 KAPI 9) |
| Onay | Vault Steward (Kapı 10) — **BEKLİYOR**, bu dosya vault'a taşınmadı |
| Bant | Bant 1 — `K000-K020 EXISTING FOUNDATION` (21 kart) |
| Uçak | **SOFTWARE** (EK A §A.0) — *GÖREV 07 notu: donanım-yazılım kesişimi; fiziksel üretim K016-K020'de* |
| Teatral epitet | «DEMİRHANE» — yalnız sıfat, K-ID'yi ezmez (R2.3) |
| Sahip (owner) | `audio-hw` (Audio Hardware Engineer — AGENTS.md §4 madde 9) |
| Üretim yeri | staging (`b1-K001-donanim.md`) → hedef `.ai/architecture/K001-donanim/index.md` |

> **Durum etiketleri (F1 §5.4):** `[CURRENT]` (repo kanıtlı) · `[TARGET]`
> (onaylı hedef) · `[PROPOSED]` (bu kart) · `[PLANNED]`/`[DESIGN]` (kanıtsız) ·
> `[VERIFY REQUIRED]`. Hedef ≠ kanıt ayrı yazılır (H10).

---

#### §1 Genel Bakış

K001, CoreMusic ses donanımının **tasarım ve arayüz katmanıdır**: ses DSP'si
(XMOS XU316), codec/DAC/ADC (PCM3168A · AK4458), amplifikatör arayüzü,
konnektörler, güç arayüzleri ve sensörler (EK A §A.1 K001 kartı). Uçak
ayrımında (ADR-096 §2.2 · GÖREV 07) bu katman **SOFTWARE uçağındadır** —
sebebi donanım-yazılım kesişiminin burada tanımlanmasıdır; **fiziksel
üretim/PCB/BOM/termal işleri K016-K020'de** kalır. İzinli alt katmanı
yalnız K000'dir.

### 1.1 Kapsam Dışı (K001 ne YAPMAZ)

| # | Kapsam dışı | Asıl sahip | Kaynak |
|---|---|---|---|
| 1 | İşletim sistemi runtime / süreç / bellek | K000 | EK A §A.1 K000 kartı |
| 2 | Sürücü protokolleri (ASIO/WASAPI/ALSA/I2S/USB/BT/DLNA) | K002 | EK A §A.1 K002 kartı |
| 3 | DSP zinciri / EQ / reverb yazılımı | K003 | EK A §A.1 K003 kartı |
| 4 | PCB yerleşimi, termal, güç dönüşümü devresi, BOM/üretim | K016-K020 (PHYSICAL uçak) | F1 §8.5 · ADR-096 §2.2 |
| 5 | Kimlik/kripto/denetim kararı | K006 | `.ai/CLAUDE.md` §5 K6 |
| 6 | Ağ protokolleri | K014 | EK A §A.1 K014 kartı |

---

## §2 Özet Satır — 16 Alan (R4.1 · F1 §10.2 kontratı)

| K-ID | KANONİK_AD | TEATRAL_EPİTET | DOMAIN | RUNTIME | SORUMLULUK | GİRDİ | ÇIKTI | İZİNLİ_BAGIMLILIK | YASAK_BAGIMLILIK | DATA_BOUNDARY | SECURITY_BOUNDARY | FAILURE_MODE | OBSERVABILITY | TEST | KANIT |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| K001 | HARDWARE | «DEMİRHANE» | HARDWARE-INTERFACE | embedded · desktop · edge | Audio Hardware · XMOS XU316 · PCM3168A · AK4458 · Amplifier · Connectors · Power Interfaces · Sensors (EK A, 8 madde) | dijital ses akışı (I2S/TDM) · kontrol/komut arayüzü · güç/ölçüm sensörü okuması · arayüz pin tanımı | analog çıkış sinyali (DAC/amfi) · dönüş sinyali (ADC) · sensör/telemetri okuması · arayüz durumu | K000 (kök alt katman) + port/adapter | üst katmana (K002-K020) doğrudan erişim · geri çağrı (H20) · veri paylaşımı (H19) · PHYSICAL uçak işlerine (K016-K020) karışma | çekirdek veri sınırı (EK A) — yazılım katmanlarıyla paylaşılan tablo YOK; donanım tanımı ayrı envanterde | security=ORTA (EK A) · imzalı firmware/asset (F1 SEC15) · yetki kararı üretmez | fail-over (EK A) · kanal arızasında devre dışı/enable pin ile izolasyon | sinyal/ölçüm telemetrisi · sıcaklık/akım sensör okuması · kanal durumu [PLANNED — metrik adları K012 ile] | sinyal bütünlüğü · koruma eşiği (DC offset/OVP/OCP) · kanal izolasyonu testi [PLANNED] | `.ai/architecture/00-kspace-anayasa.md` satır 74-77 · `.ai/CLAUDE.md` §5 K1 satırı (120) + §5 K1-K20 bileşen tablosu · `--/architecture/k16-class-ab/bom-classab.md` (e695f47 rename-artefaktı — V2 KAPSAM DIŞI; BOM = K016/K020 envanterinde yeniden üretilecek ⚠️) · ADR-038/061/063/064/089/090 · web: ⚠️ VERIFICATION REQUIRED |

### 2.1 Alan bazlı değer gerekçesi (R4.1 kontratının açılımı)

| # | Alan | Bu karttaki değer | Gerekçe (kaynak) |
|---|---|---|---|
| 1 | K-ID | `K001` | EK A §A.0 — kimlik asla değişmez (R2.3) |
| 2 | KANONİK_AD | `HARDWARE` | EK A §A.0 kanonik ad sütunu |
| 3 | TEATRAL_EPİTET | «DEMİRHANE» | EK A §A.0 epitet sütunu |
| 4 | DOMAIN | `HARDWARE-INTERFACE` | katman donanım-yazılım kesişimidir (GÖREV 07) |
| 5 | RUNTIME | embedded · desktop · edge | donanımın bağlandığı çalışma zamanları (§14 deployment modları) |
| 6 | SORUMLULUK | 8 madde | EK A §A.1 K001 Sorumluluk satırı — birebir |
| 7 | GİRDİ | dijital ses akışı · komut · sensör okuması · pin tanımı | Sorumluluk maddelerinden türetildi (tasarım) |
| 8 | ÇIKTI | analog sinyal · ADC dönüşü · telemetri · arayüz durumu | aynı türetme |
| 9 | İZİNLİ_BAGIMLILIK | K000 | EK A satır 76 "izinli=K000-K000 (alt katmanlar)" |
| 10 | YASAK_BAGIMLILIK | K002-K020 doğrudan · H20 · H19 | EK A satır 76 "yasak=üst katmana doğrudan erişim" |
| 11 | DATA_BOUNDARY | çekirdek veri sınırı | EK A satır 76 "data=çekirdek veri sınırı" |
| 12 | SECURITY_BOUNDARY | ORTA · SEC15 | EK A satır 76 "security=ORTA" + F1 §9.3 SEC15 |
| 13 | FAILURE_MODE | fail-over | EK A satır 76 "failure=fail-over" |
| 14 | OBSERVABILITY | telemetri · sensör · kanal durumu | EK A'da alan yok → tasarım [PLANNED], owner K012 |
| 15 | TEST | sinyal bütünlüğü · koruma eşiği · izolasyon | donanım test tipi; kapsam hedefi §17 (test yok → [PLANNED]) |
| 16 | KANIT | anayasa + ADR + repo (BOM) + `⚠️` (web) | R9.5 3'lü format |

---

## §3 Tam Kimlik Kartı — EK C (20 alan · R4.2/R4.3)

```yaml
K-ID:               K001
KANONİK_AD:         HARDWARE
TEATRAL_EPİTET:     «DEMİRHANE»
DOMAIN:             HARDWARE-INTERFACE
SUBDOMAIN:          audio-hardware                    # EK A: XMOS · codec/DAC · amplifikatör
BOUNDED_CONTEXT:    hardware-arayuz                   # EK A: konnektör · güç arayüzü · sensör
RUNTIME:            embedded · desktop · edge
SORUMLULUK:         Audio Hardware · XMOS XU316 · PCM3168A · AK4458 · Amplifier ·
                    Connectors · Power Interfaces · Sensors
GIRDI:              dijital ses akışı (I2S/TDM) · kontrol/komut arayüzü · güç ve
                    ölçüm sensörü okumaları · arayüz pin/şema tanımı
CIKTI:              analog çıkış sinyali (DAC/amfi çıkışı) · ADC dönüş sinyali ·
                    sensör/telemetri okuması · arayüz/kanal durumu
IZINLI_BAGIMLILIK:  [K000]                             # EK A: izinli=K000-K000
YASAK_BAGIMLILIK:   [K002..K020 doğrudan erişim, geriye çağrı H20, veri paylaşımı H19,
                    K016..K020 fiziksel üretim işlerine karışma]
DATA_BOUNDARY:      çekirdek veri sınırı — yazılım katmanlarıyla paylaşılan tablo YOK;
                    donanım/konfigürasyon envanteri ayrıdır (YARGI 2)
SECURITY_BOUNDARY:  security=ORTA (EK A) · OWASP A08:2025 Software/Data Integrity
                    arayüzü — firmware/asset imzalı, doğrulanmadan çalıştırılmaz
                    (F1 §9.3 SEC15) · yetki kararı üretmez (K006)
FAILURE_MODE:       fail-over (EK A) — kanal arızasında enable pin ile izolasyon ve
                    devre dışı bırakma; koruma eşikleri K016/K017'nin uygulamasıdır
OBSERVABILITY:      sinyal/ölçüm telemetrisi · sıcaklık/akım sensör okuması ·
                    kanal durumu [PLANNED — K012 ile birlikte tanımlanacak]
TEST:               sinyal bütünlüğü · koruma eşiği (DC offset / OVP / OCP) ·
                    kanal izolasyonu testi [PLANNED]
KANIT:              .ai/architecture/00-kspace-anayasa.md satır 74-77 |
                    .ai/CLAUDE.md §5 K1 satırı (satır 120) + §5 K16-K20 + H1-H5 |
                    --/architecture/k16-class-ab/bom-classab.md (e695f47 artefaktı — V2 dışı ⚠️) |
                    ADR-038 · ADR-061 · ADR-063 · ADR-064 · ADR-089 · ADR-090 |
                    web: ⚠️ VERIFICATION REQUIRED
kanit-tarihi:       2026-10-08
kart-durumu:        PROPOSED (R16.2 — 👤 onayı bekliyor)
```

**Kart kalite kapıları (R4.4):**

| Kapı | Sonuç |
|---|---|
| (a) Her alan dolu | GEÇTİ — 20/20 (18 EK C + `kanit-tarihi` (R9.3) + `kart-durumu` (R16.2)) |
| (b) IZINLI ∩ YASAK = ∅ | GEÇTİ — IZINLI = {K000}; YASAK = {K002..K020, H20, H19} → kesişim ∅ |
| (c) KANIT `⚠️` ise research kapısı | KISMİ — web ayağı `⚠️`; R14 kapısında doldurulacak |
| (d) Aynı veri sınırı iki katman paylaşırsa YARGI ihlali | GEÇTİ — çekirdek veri sınırı K001'e münhasır |

**Epitet kalite notu:** «DEMİRHANE» EK A §A.0'dan alınmıştır (R8.1 — çelişkide
EK A kazanır); epitet kimliği ezmez (R2.3).

---

## §4 Sorumluluk Derinliği

### 4.1 EK A kartı Sorumluluk maddeleri (00-kspace-anayasa.md §A.1 K001)

| # | Kalem (EK A) | Ne yapar | Durum | Kanıt |
|---|---|---|---|---|
| 1 | Audio Hardware | Ses donanımı tümünün arayüz tanımı (giriş/çıkış/kontrol) | DESIGN | anayasa satır 75 (Sorumluluk) |
| 2 | XMOS XU316 | USB Audio SoC — dijital ses köprüsü | TARGET | `.ai/CLAUDE.md` §5 K1 satırı (satır 120) · §24 Web Verification tablosu (XMOS XU316) |
| 3 | PCM3168A | Codec (ADC+DAC) — 8 kanal | TARGET | §5 K1 satırı · §24 (PCM316A/TI) · §22 "PCM5122 yasak → PCM3168A/AK4458" |
| 4 | AK4458 | 32-bit 8 kanal DAC | TARGET | §5 K1 satırı · §24 (AK4458/ AKM) |
| 5 | Amplifier | 8× Class AB amplifikatör arayüzü (üretim ayrı: K016) | TARGET | §5 K1 satırı "8×Class AB" · ADR-089 · ADR-090 |
| 6 | Connectors | 14 konnektör arayüzü | TARGET | §5 K1 satırı "14 konnektör" |
| 7 | Power Interfaces | Güç arayüzü tanımı (üretim/devre ayrı: K017) | TARGET | §5 K1 satırı "Boost" · ADR-089 (±35V boost) |
| 8 | Sensors | Sıcaklık/akım/gerilim sensör arayüzleri (okuma yolu) | PLANNED | `⚠️` — sensör envanteri dokümanı repo'da yok |

### 4.2 Anayasa §5 K1 satırındaki gerçek kapsam (`.ai/CLAUDE.md` §5, satır 120)

**Satır:** `K1 Donanım | XMOS XU316, PCM3168A, AK4458, 8×Class AB, 14 konnektör, Boost | 120 | DC-Only + Class AB; Sinyal zincirine parazit yasak`

| Alt kapsam | Ne kapsar | Durum | Kanıt |
|---|---|---|---|
| XMOS XU316 | USB Audio SoC | TARGET | §5 satır 120 · §24 |
| PCM3168A | Codec (ADC+DAC) | TARGET | §5 satır 120 · §24 |
| AK4458 | 32-bit 8ch DAC | TARGET | §5 satır 120 · §24 |
| 8×Class AB | amplifikatör kanalları | TARGET | §5 satır 120 · ADR-089/ADR-090 |
| 14 konnektör | arayüz/konnektör seti | TARGET | §5 satır 120 |
| Boost | yükseltici güç arayüzü | TARGET | §5 satır 120 · ADR-089 (LM5122 ×2) |
| **Hard Guardrail** | "DC-Only + Class AB; Sinyal zincirine parazit yasak" | BAĞLAYICI | §5 satır 120 |
| "120 bileşen" | §5 sayım sütunu | **HEDEF** | H10: hedef ≠ kanıt |

### 4.3 Bileşen / kalem envanteri (derinlik)

| Kalem | Ne yapar | Durum | Kanıt |
|---|---|---|---|
| USB ses köprüsü (XU316) | Host ↔ DSP arası dijital ses + kontrol | TARGET | §5 K1 · §24 XMOS XU316 |
| Codec dönüştürücü (PCM3168A) | ADC/DAC dönüşümü, 8 kanal | TARGET | §5 K1 · §24 PCM3168A |
| Çok kanallı DAC (AK4458) | 32-bit 8 kanal çıkış | TARGET | §5 K1 · §24 AK4458 |
| Amplifikatör arayüzü | Kanal başına giriş/enable/geri besleme tanımı | TARGET | §5 K1 · ADR-089 · ADR-090 |
| Konnektör seti (14) | Sinyal/güç/veri konnektörleri | TARGET | §5 K1 |
| Güç arayüzü | ±35V ray bağlantı tanımı (üretim K017) | TARGET | ADR-089 · §5 K17 satırı |
| Sensör arayüzü | Sıcaklık/akım/gerilim okuma yolu | PLANNED | `⚠️` |
| I2S/TDM pin tanımı | Seri ses veri yolu pin/saat tanımı | DESIGN | §5 K19 "50Ω I2S impedans" (hedef) · `⚠️` |
| DC-offset koruma el sıkışması | >0.5V DC'de koruma tetiği (devre K016'da) | TARGET | `.ai/CLAUDE.md` §23 Uyarı 7 |
| Kanal izolasyonu | Tek kanal bağımsızlık + enable pin | TARGET | §5 K16 "Tek kanal bağımsız, enable pinli" |
| EMI/EMC arayüz şartı | PCB/şartname arayüzü (uygulama K019) | DESIGN | §5 K19 · ADR-063 |
| BOM bağlantısı | Üretim listesi referansı | CURRENT (disk) | `--/architecture/k16-class-ab/bom-classab.md` (e695f47 rename-artefaktı — V2 dışı ⚠️; ls 2026-10-08) |
| Sürücü bağlantısı | K002'ye arayüz sağlamak | DESIGN | F1 §8.5 kesişim kuralı |
| Telemetri çıkışı | Ölçüm verisinin K012'ye aktarımı | PLANNED | `⚠️` |

**Sayım disiplini:** §4.3'te 14 kalem listelenmiştir; §5'in "120 bileşen" hedefi
yle **karıştırılmaz** (H10).

### 4.4 EK A K001 bloğunun birebir alıntısı (kaynak metin)

```text
### K001 - HARDWARE «DEMİRHANE» Bant: K000-K020
- Sorumluluk: Audio Hardware · XMOS XU316 · PCM3168A · AK4458 · Amplifier · Connectors · Power Interfaces · Sensors
- Sınır: data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=K000-K000 (alt katmanlar) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault
```

*Kaynak:* `.ai/architecture/00-kspace-anayasa.md` satır 74-77. Alıntı
değiştirilmemiştir (In-Place · H4/R7).

### 4.5 EK A Sınır satırının madde madde açılımı

| Sınır maddesi | Değer | Bu karttaki karşılığı | Açıldığı bölüm |
|---|---|---|---|
| `data` | çekirdek veri sınırı | donanım/konfigürasyon envanteri ayrı; yazılım DB'siyle paylaşılan tablo yok | §5.3 |
| `security` | ORTA | firmware/asset imzası (SEC15); yetki K006'da | §5.4 |
| `failure` | fail-over | kanal arızasında izolasyon/devre dışı; koruma eşikleri K016/K017 | §5.5 |
| `izinli` | K000-K000 | yalnız K000 (kök) | §5.1 |
| `yasak` | üst katmana doğrudan erişim (yalnız port/adapter) | K002-K020 + H20 + H19 | §5.2 |
| `Kanıt` (EK A) | kaynak prompt K0–K20 bloğu · .ai/ vault | web ayağı bu kartta ayrı `⚠️` | §7 |

### 4.6 Uçak ayrımı NOTU (GÖREV 07 — bu kartın en kritik sınırı)

| Soru | Yanıt | Kaynak |
|---|---|---|
| K001 hangi uçakta? | **SOFTWARE** | EK A §A.0 satır 46 · ADR-096 §2.2 "Uçak ayrımı (F1 GÖREV 07)" |
| Neden? | Donanım-yazılım **kesişimi** yalnızca driver/API sınırında tanımlanır | ADR-096 §2.2 |
| Fiziksel üretim nerede? | **K016-K020** (AMPLIFIER · POWER · THERMAL · PCB · MANUFACTURING — PHYSICAL) | EK A §A.0 satır 61-65 · F1 §8.5 |
| Kesişim kim? | YALNIZ K2 (K002 sürücü) + K9/K15 API sınırı | F1 §8.5 |
| Yasak | Web/UI katmanının doğrudan donanım katmanına erişimi | F1 §8.5 |
| Bu karta etkisi | K001 **tasarım/arayüz** katmanıdır; fabrikasyon/PCB/BOM işi bu kartın içinde değildir | §1.1 madde 4 |

### 4.7 Kritik Bileşen tablosu ↔ K001 kapsamı (`.ai/CLAUDE.md` §5 — H1-H5)

| Kod | Bileşen | Kapsam | Bu karttaki yeri |
|---|---|---|---|
| H1 | Class AB Amplifikatör | 50W/kanal, 8 kanal modüler, MJL21194/MJL21193 | arayüz = K001 · devre/üretim = K016 |
| H2 | Güç Kaynağı | ±40V Push-Pull / LM5122, 12-24V DC giriş | güç arayüzü = K001 · devre = K017 |
| H3 | Termal Tasarım | Fischer heatsink + fan + thermal cutoff | sensör arayüzü = K001 · tasarım = K018 |
| H4 | PCB Tasarım | 6-layer, 90Ω USB / 50Ω I2S impedans | şartname arayüzü = K001 · yerleşim = K019 |
| H5 | BOM & Üretim | 639 bileşen (8 kanal) — `bom-classab.md`, disk doğrulandı 2026-09-26 | referans = K001 · üretim = K020 |

### 4.8 K001 ↔ K016-K020 sınır ayrımı (fiziksel uçak çakışması önleme)

| İş | K001 (bu kart) | K016-K020 (fiziksel uçak) | Ayırıcı kural |
|---|---|---|---|
| Amplifikatör | kanal arayüzü, enable/geri besleme tanımı | MJL21194/93 devresi, PCB yerleşimi, THD doğrulaması | F1 §8.5 uçak ayrımı |
| Güç | ± ray bağlantı/ölçüm arayüzü | LM5122 devresi, OR-ing, koruma (UVP/OVP/OCP/OTP) | ADR-089 · §5 K17 |
| Termal | sensör **okuma arayüzü** | heatsink/fan yerleşimi, thermal cutoff (KSD301) | §5 K18 |
| PCB | I2S/USB impedans **şartnamesi** | 6-layer stackup, ENIG, thermal vias (K019) | §5 K19 |
| BOM/üretim | BOM'a **referans** verir | 1,775 satır üretim listesi, tedarik, kalibrasyon (K020) | §5 K20 |
| Kod numarası | K001 | K016 · K017 · K018 · K019 · K020 | EK A §A.0 (satır 61-65 PHYSICAL) |

---

## §5 Bağımlılık & Sınır

### 5.1 İZİNLİ bağımlılıklar (EK A: izinli = K000-K000 · R6.1)

| Hedef | Tür | Aralık / not |
|---|---|---|
| K000 | alt katman | EK A satır 76: "izinli=K000-K000 (alt katmanlar)" — tek izinli |
| K000 port/adapter | port/adapter | donanım arayüzü K000'in cihaz soyutlamasına port/adapter ile bağlanır |
| K002 (gelen yön) | gelen çağrı | K002 aşağı iner → K001; K001 **geri çağırmaz** (H20) |

### 5.2 YASAK bağımlılıklar (R6.1 · F1 H19/H20)

| Yasak | Kaynak | Sonuç |
|---|---|---|
| K002-K020'ye doğrudan erişim | EK A satır 76 | Layer Violation → derhal revert + log CRITICAL |
| Geri çağrı (H20) | F1 §5.1 H20 · R6.1 | yalnız port/adapter ile |
| Doğrudan veri paylaşımı (H19) | F1 §5.1 H19 · YARGI 2 | data boundary ihlali |
| K016-K020 fiziksel işlerine karışma | ADR-096 §2.2 uçak ayrımı | bu kart yalnız arayüz/tasarım |
| Web/UI → doğrudan donanım erişimi | F1 §8.5 | erişim yalnız K002 üzerinden |
| Sinyal zincirine parazit | `.ai/CLAUDE.md` §5 K1 guardrail | donanım-katman ihlali |

**Olay (event) yukarı serbest:** R6.2 — K001'den K002/K003'e cihaz olayları
(bağlantı/kopma, koruma eşiği tetiği) event ile yayılabilir; senkron çağrı
yukarı yasaktır.

### 5.3 DATA_BOUNDARY (YARGI 2)

| Konu | Kural |
|---|---|
| Sınır adı | çekirdek veri sınırı (EK A satır 76) |
| K001'in verisi | pin/şema tanımı, kanal konfigürasyonu, sensör kalibrasyonu — yazılım DB'siyle paylaşılmaz |
| Üst katman verisi | K005 tabloları K001'e okunmaz/yazdırılmaz |
| Paylaşılan tablo | YOK |
| Kalıcılık | donanım envanteri/şema dosyaları ayrıdır; DB'ye yazım yok |

### 5.4 SECURITY_BOUNDARY (EK A: security=ORTA)

| Konu | Değer |
|---|---|
| Seviye | ORTA (EK A satır 76) |
| OWASP eşlemesi | A08:2025 Software/Data Integrity — firmware/asset imza doğrulaması |
| İlgili kural | F1 SEC15: firmware/asset güncellemesi imzalı; doğrulanmadan çalıştırılmaz |
| İlgili kural | F1 SEC16: cihaz-yazılım sınırında **yalnız K002** geçiş yapar |
| İlgili kural | F1 SEC17: telemetri/gizli veri cihazdan çıkarılırken kullanıcı onayı + redaksiyon |
| Devre dışı bırakılamaz | K006 bypass'ı yok; yetki kararı K001 üretmez |

### 5.5 FAILURE_MODE (EK A: fail-over)

| Senaryo | Davranış | Durum |
|---|---|---|
| Kanal arızası | enable pin ile kanalı izole etme (fail-over) | TARGET (§5 K16 guardrail) |
| DC offset > 0.5V | koruma tetiği/devre dışı bırakma | TARGET (`.ai/CLAUDE.md` §23 Uyarı 7) |
| Aşırı akım/gerilim (OVP/OCP) | koruma eşiği — uygulama K017 | TARGET (§5 K17 guardrail) |
| Aşırı sıcaklık | termal koruma — uygulama K018 | TARGET (§5 K18 guardrail) |
| fail-open | YOK — fail-open yalnız belgelenmiş ve ADR'li (F1 SEC14) | — |

### 5.6 Observability / Test sınırı

Log/metrik/trace **üretim kuralları K012**'dedir (R7.1); K001 yalnız ölçüm
arayüzünü sağlar. Test stratejisi K013 CI/CD + donanım test prosedürleri;
yazılım testleri §17 hedeflerine bağlıdır.

### 5.7 Port/Adapter deseni (F1 §8.2 — K001'e uygulaması)

| Port tipi | Yön | Örnek (tasarım) | Adapter | Durum |
|---|---|---|---|---|
| Inbound | K002 → K001 | sürücü komut/akış portu | K002 adapter'ı | DESIGN |
| Outbound | K001 → K000 | cihaz/IO erişim portu | K000 port/adapter | DESIGN |
| Kural | — | adapter değişse de K001 sözleşmesi değişmez (F1 §8.2) | — | bağlayıcı |
| Kural | — | K001 doğrudan DB'ye bağlanamaz (H19) | — | bağlayıcı |

### 5.8 Sınır ihlali denetim ve yaptırım akışı (R6.5/R6.6 · anayasa §5.1)

| Adım | Aksiyon | Kaynak |
|---|---|---|
| 1 | İhlal tespiti: K001 → K003/K005 gibi doğrudan üst erişim | R6.1 · §5.2 |
| 2 | Derhal revert | `.ai/CLAUDE.md` §5.1 |
| 3 | `log.md`'ye CRITICAL girişi | anayasa §5.1 |
| 4 | 👤 bilgi | R6.5 |
| 5 | Döngü varsa `dep-check` exit 1 → en zayıf kenar ADR'a | R6.6 |
| 6 | Kardeş ilişki `refers-to` (düz metin K-ID) | R6.4 · §8.1 |

---

## §6 Girdi-Çıktı · Runtime · Observability-Test

### 6.1 Girdi (GIRDI alanının açılımı)

| Girdi | Kaynak | Biçim | Durum |
|---|---|---|---|
| Dijital ses akışı | K002 sürücü katmanı | I2S/TDM | DESIGN |
| Kontrol/komut arayüzü | K002/K003 | register/komut | DESIGN |
| Güç/ölçüm sensörü okuması | donanım | analog/gerilim okuması | PLANNED (`⚠️`) |
| Arayüz pin/şema tanımı | tasarım envanteri | doküman | DESIGN |
| USB ses çerçevesi | host (K000 üzerinden) | USB Audio sınıfı | TARGET (XMOS XU316) |

### 6.2 Çıktı (ÇIKTI alanının açılımı)

| Çıktı | Tüketen katman | Durum |
|---|---|---|
| Analog çıkış (DAC/amfi) | hoparlör/kullanıcı (donanım ucu) | TARGET |
| ADC dönüş sinyali | K003 (giriş işleme) | DESIGN |
| Sensör/telemetri | K012 (gözlem) | PLANNED |
| Arayüz/kanal durumu | K002 (durum raporu) · olay ile yukarı | DESIGN |

### 6.3 Runtime

| Alan | Değer |
|---|---|
| Runtime sınıfları | embedded · desktop · edge (EK C) |
| Uçak | **SOFTWARE** (GÖREV 07 kesişim) — fiziksel uçak K016-K020 |
| Deployment modları (ilgili) | DAC Control System (XMOS XU316 + PCM3168A) · Professional Studio · Car Audio — `.ai/CLAUDE.md` §14 [TARGET] |
| Platform tier'ları | Tier 1-5 (§13) — donanım arayüzü tier'lara bağlı değil, sürücü katmanına (K002) bağlıdır |

### 6.4 Observability

| Alan | İçerik | Durum |
|---|---|---|
| Log | — (K000/K012 taşıyıcısı) | owner K012 |
| Metric | sinyal/ölçüm telemetrisi · sıcaklık/akım/gerilim · kanal durumu | PLANNED — `⚠️` |
| Trace | donanım olay zinciri (olay yukarı serbest) | PLANNED |
| Health | kanal enable/durum | DESIGN |
| Alert | K012 üzerinden | PLANNED |

### 6.5 Test

| Test tipi | Kapsam | Kabul kriteri | Durum |
|---|---|---|---|
| Sinyal bütünlüğü | DAC/amfi çıkış yolu | bozulma/parazit şartı | PLANNED (`⚠️` — eşik değeri yok) |
| Koruma eşiği | DC offset · OVP · OCP | eşikte tetiklenme | PLANNED |
| Kanal izolasyonu | Tek kanal bağımsızlık + enable | izolasyon sağlanması | PLANNED |
| Firmware imza | SEC15 | imza doğrulaması geçerli | PLANNED |
| Hedef kapsam | `.ai/CLAUDE.md` §17 ≥80% | ≥80% | hedef |

### 6.6 F1 §8.4 YARGI'larının K001'e uygulaması

| Yargı | Kural | K001 uygulaması | Durum |
|---|---|---|---|
| YARGI 1 | yalnız alt katman tanınır | yalnız K000 | GEÇERLİ (EK A) |
| YARGI 2 | DATA BOUNDARY, tablo paylaşımı yasak | çekirdek veri sınırı | GEÇERLİ |
| YARGI 3 | SECURITY BOUNDARY | ORTA + A08 + SEC15/16/17 | GEÇERLİ |
| YARGI 4 | OBSERVABILITY alanı | telemetri/sensör/kanal [PLANNED] | TASARIM |
| YARGI 5 | FAILURE_MODE tanımlı | fail-over | GEÇERLİ |
| YARGI 6 | EVENT BOUNDARY tercih | cihaz olayları yukarı serbest | GEÇERLİ (R6.2) |

### 6.7 Bilinen açık maddeler ve riskler (K001'e özgü)

| # | Açık madde | Etki | Aksiyon |
|---|---|---|---|
| 1 | Web ayağı boş (EK B'de K001'e doğrudan kaynak yok) | `⚠️` zorunlu | R14 research + EK B'ye `#NN` ekle |
| 2 | Sensör envanteri dokümanı repo'da yok | §4.1/§4.3 madde PLANNED | donanım envanteri üretimi (K020 ile) |
| 3 | `bom-classab.md` yolu `--/architecture/…` altında (repo kökünde `--/` — e695f47 rename-artefaktı: peer scratch klasörüne taşınmış eski ağaç) | yol V2-dışı → `⚠️` | temizlik/kapsam kararı Vault Steward (J4: yalnız `.ai/**`) |
| 4 | "120 bileşen" hedefi sayım'a bağlı değil | hedef/kanıt karışması | H10: ayrı raporla |

---

## §7 Kanıt Kaynakları (R9 — 3'lü kanıt)

| # | Tür | Kaynak (tam yollar) | İçerik | Durum |
|---|---|---|---|---|
| 1 | Anayasa/defter | `.ai/architecture/00-kspace-anayasa.md` satır 74-77 | K001 kartı: Sorumluluk 8 madde · Sınır · Kanıt satırı | GEÇERLİ |
| 2 | Anayasa | `.ai/architecture/00-kspace-anayasa.md` satır 46 (§A.0) | K001 · HARDWARE · «DEMİRHANE» · SOFTWARE | GEÇERLİ |
| 3 | Anayasa (vault) | `.ai/CLAUDE.md` §5 K1 satırı (satır 120) · §5 K16-K20 · H1-H5 · §24 | donanım kapsamı + guardrail + bileşen sayıları + teknoloji doğrulama tablosu | GEÇERLİ |
| 4 | Anayasa (vault) | `.ai/CLAUDE.md` §19 · §22 · §23 Uyarı 7 | ses donanım standartları · PCM5122 yasağı · DC offset koruma | GEÇERLİ |
| 5 | Repo (dosya yolu) | `--/architecture/k16-class-ab/bom-classab.md` (e695f47 rename-artefaktı — repo kökü `--/` scratch; V2 KAPSAM DIŞI) | Class AB BOM (ls ile 2026-10-08 doğrulandı) | GEÇERLİ ama V2-dışı · yol notu `⚠️` · yeniden üretim K016/K020 |
| 6 | Repo (dosya yolu) | `.ai/CLAUDE.md` §5 H5 satırı: "bom-classab.md (disk doğrulandı 2026-09-26)" | BOM diskinde referansı | GEÇERLİ |
| 7 | ADR (`ls .ai/.decisions/accepted/`) | `ADR-038-8-1-sound-card-chip-selection.md` | 8.1 ses kartı çip seçimi (PCM3168A + XMOS XU316) | GEÇERLİ |
| 8 | ADR (`ls`) | `ADR-061-electronics-architecture.md` · `ADR-063-hardware-design-standards.md` · `ADR-064-electronics-platform-architecture.md` | elektronik mimari · tasarım standartları · platform mimarisi | GEÇERLİ |
| 9 | ADR (`ls`) | `ADR-089-classab-24v.md` · `ADR-090-channel-variant-product-family.md` | Class AB + 6S LiPo + ±35V · kanal varyant ürün ailesi | GEÇERLİ |
| 10 | Karar | `.ai/.decisions/accepted/ADR-096-kspace-5000-boundary-model.md` §2.2 (satır 117) | "SOFTWARE PLANE = K000→K15+ (K001 HARDWARE dahil — donanım-yazılım kesişimi) · PHYSICAL PLANE = K016→K020" | GEÇERLİ |
| 11 | Spec (arşiv) | `.ai/prompts/2026-10-08-master-prompt-v2.2.0-f1.md` §8.5 (satır 658-665) | uçak ayrımı · kesişim yalnız K2 · web→donanım yasağı | GEÇERLİ |
| 12 | Kural | `.ai/architecture/rules.md` R2/R3/R4/R6/R9 | isimlendirme · iskelet · kayıt formatı · yön · kanıt | GEÇERLİ |
| 13 | Repo (grep) | 2026-10-08: `git ls-files '*.cpp' = 0` · donanım kod dosyası **yok** | K001 için kod kanıtı yok → durumlar DESIGN/PLANNED/TARGET | `⚠️` |
| 14 | Web (EK B) | F1 EK B defteri (satır 7409-7508) | K001'e doğrudan numaralı kaynak **yok** | ⚠️ VERIFICATION REQUIRED |

**3'lü kanıt dengesi (R9.2):** repo ayağı 1 (BOM dosyası, anlamlı ama tek) ·
ADR ayağı 6 · web ayağı 0 → **2/3 kısmi → `⚠️ VERIFICATION REQUIRED`**.
R14 research kapısında K001'e özgü web turu (XMOS/PCM3168A/AK4458 üretici
kaynakları) EK B'ye eklenmelidir.

### 7.1 Kanıt dengesi özeti (R9.2/R9.3)

| Ayağı | Durum | Sayı | Sonuç |
|---|---|---|---|
| Repo (dosya yolu + satır) | KISMİ | 1 (BOM) | donanım kodu yok → kalan maddeler PLANNED |
| ADR (`ls` ile görülen adlar) | VAR | 6 | kapsam/standart kanıtı |
| Web (URL + tarih) | YOK | 0 | `⚠️` zorunlu |
| Anayasa/kural/karar | VAR | 6 kayıt | kartın taşıyıcısı |
| `kanit-tarihi` | 2026-10-08 | — | R9.3 dolu |
| `STALE` (R11) | gerekmedi | — | tüm kanıtlar 2026-10-08 |

### 7.2 R14 research boşluğu (K001'e özgü açık sorular — EK B'ye taşınacak)

| # | Açık soru | İlgili alan | Beklenen kaynak tipi |
|---|---|---|---|
| 1 | XMOS XU316'ya ait üretici dokümantasyonu | §4.1 madde 2 | üretici (xmos.com) + tarih |
| 2 | PCM3168A kanal/konvertör spesifikasyonu | §4.1 madde 3 | üretici (ti.com) + tarih |
| 3 | AK4458 çözünürlük/kanal spesifikasyonu | §4.1 madde 4 | üretici (akm.com) + tarih |
| 4 | Sensör envanteri (hangi sensör, hangi arayüz) | §4.1 madde 8 | tasarım envanteri — repo'da YOK |
| 5 | 14 konnektörün listesi | §4.2 | tasarım envanteri — repo'da YOK |
| 6 | BOM dosyasının geçerli yolu (`--/…` rename-artefaktı — V2 dışı) | §7 no. 5 | Vault Steward: `--/` temizlik/kapsam kararı (J4) + K016/K020 envanterinde yeniden üretim |

> Bu sorular **uydurulmaz**; cevap bulunana kadar ilgili maddeler
> `⚠️ VERIFICATION REQUIRED` / `PLANNED` kalır (H15 · R9.2).

---

## §8 İlişki & Değişiklik

### 8.1 Kardeş ve bant ilişkileri (düz metin K-ID — wiki-link YASAK)

| Yön | K-ID | İlişki |
|---|---|---|
| Alt katman | K000 | tek izinli bağımlılık (`depends-on` alt katman) |
| Üst komşu (doğrudan) | K002 | sürücü — K001'e port/adapter ile iner |
| Band-1 kardeşler | K003 · K004 · K005 · K006 · K007 · K008 · K009 · K010 · K011 · K012 · K013 · K014 · K015 | `refers-to` (R6.4) — `depends-on` DEĞİL |
| PHYSICAL uçak kardeşleri | K016 · K017 · K018 · K019 · K020 | uygulama/üretim bu kartın arayüzünü kullanır (§4.7) |
| Kesişim partneri | K002 | F1 §8.5: donanım-yazılım kesişimi yalnız K002 |

### 8.2 İzinli wiki-linkler (yalnız mevcut kontrol-plane dosyaları)

- [[architecture/00-kspace-anayasa]] — EK A (K001 kartı kaynağı)
- [[architecture/rules]] — R2/R3/R4/R6/R9
- [[architecture/00-master-index]] — giriş navigasyonu

>Kardeş K-ID'lere wiki-link **yasaktır** (hedefler henüz yok; link-check ihlali).

### 8.3 Geçmiş

| Tarih | Değişiklik | Yapan |
|---|---|---|
| 2026-10-08 | İlk üretim — band-1 K001 `index.md` (staging) | Vault Steward (üretim ajanı) |

### 8.4 Dosya yerleşim planı (ADR-096 §2.2 dizin deseni · R3 iskelet)

| Dosya | Rol | Zorunluluk |
|---|---|---|
| `.ai/architecture/K001-donanim/index.md` | bu dosyanın vault'taki hedefi | zorunlu (R3.1) |
| `.ai/architecture/K001-donanim/*.md` (derinleşme) | yalnız gerçek karmaşıklıkta | koşullu (R3.1 · F2 §42) |
| `b1-K001-donanim.md` (staging) | üretim kopyası — vault'a taşınmadı | bu görevin çıktısı |
| Dizin adı | `K001-donanim` (R2.2: 3 haneli K-ID + tire + Türkçe ad) | değişmez (R2.4) |
| Tek dev md | yasak (R3.2) | bağlayıcı |
| min-500 | üretim dosyası ≥500 satır; şişirme yasak (R3.3 · H1) | bağlayıcı |

---

**Authority:** SSOT hedefi `.ai/architecture/K001-donanim/index.md` — bu kopya şimdilik **staging**'dedir.
**Last Updated:** 2026-10-08
**Mode:** Red Team · Human Mode · Truth Mode
