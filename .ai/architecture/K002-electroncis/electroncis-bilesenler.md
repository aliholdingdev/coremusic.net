---
node_id: electroncis-bilesenler
node_type: subdomain
status: draft
parent_id: K002-electroncis
children: []
depends_on: [K000-isletim-sistemi, K001-donanım, K005-firmware]
evidence:
  - ".ai/architecture/K002-electroncis/electroncis-master.md §2 (5 katman), §3 (alt-bileşenler), §4 (arayüzler) · 2026-10-18"
  - "disk ölçümü: K002 root 6 md + 5 alt dizin (devre-ve-analog · guc-yonetimi · dijital-ve-mikroislemci · sensor-ve-akteyici · imalat-ve-test — her biri index+anayasa+ornekler+dogrulama) · 2026-10-18"
validation: "Her bileşen master §2/§3/§4 ile birebir; repo'da elektronik kodu YOK (§9 #7) — kanıt sadece vault md ve datasheet URL'leridir."
open_questions:
  - "⚠️ K002 root'unda `electroncis-bilesenler.md` hariç dosyalar §GRUPLAR'da listelenmeli — K002 index.md bu oturumda yazılmadı (S2-B yetkisi), grup kaydı eksik olabilir → VERIFICATION REQUIRED."
  - "⚠️ 5 alt dizinin anayasa dosyaları kendi iç tutarlılığını taşır; aralarında sınır çelişkisi taranmadı (bu görevin kapsamı dışı) → UNKNOWN."
group: mimari
---

# K002-electroncis — Bileşenler (Katman · Alt-Bileşen · Arayüz)

## §1 ELI15

Bu domain beş katman: **devre → kart → cihaz → ekosistem → üretim**.
Bileşenler bu katmanların içindeki parçalardır. K002 **kod üretmez**
(register/boot kodu K005'te), yalnız **şartname + şema + test eşiği** üretir.

## §2 Katmanlar (master §2)

| # | Katman | Ne içerir | Sınır |
|---|---|---|---|
| 1 | Devre (Circuit) | analog/dijital devre, topoloji, hesap | PCB tasarımı K002 |
| 2 | Kart (PCB) | layout, iz, GND bölme, DRC | kural seti ADR-063 §2(a) |
| 3 | Cihaz (Device) | tam cihaz, koruma, boot bütçesi | firmware kodu K005 |
| 4 | Ekosistem | USB/BT/WiFi/Ethernet arayüzleri | komşu: K000/K010/K014 |
| 5 | Üretim (Manufacturing) | BOM, AOI/ICT/FCT, EMC | AES17 fabrika testi (ADR-063 §2e) |

## §3 Alt Dizin Bileşenleri (disk ölçümü 2026-10-18)

| Alt dizin | Sahip olduğu | Dosyalar |
|---|---|---|
| `devre-ve-analog/` | direnç/kondansatör/op-amp/transistör sınıfı, sinyal zinciri | index · anayasa · ornekler · dogrulama |
| `guc-yonetimi/` | boost/LDO/PWM, EMI filtresi, akım sınırı | index · anayasa · ornekler · dogrulama |
| `dijital-ve-mikroislemci/` | MCU/saat/PLL, register-map şartnamesi | index · anayasa · ornekler · dogrulama |
| `sensor-ve-akteyici/` | INA219/ADS1115 · DRV8825/DRV8874 arayüzleri | index · anayasa · ornekler · dogrulama |
| `imalat-ve-test/` | DRC/AOI/ICT/FCT, üretim test sırası | index · anayasa · ornekler · dogrulama |

Root bileşenleri: `electroncis-master.md` (anayasa) · `-kapsam` · `-bagimliliklar` · `-dogrulama` · `-ornekler` · `index.md` (+ bu dosyanın da aralarında olduğu 7 yeni `-` dosyası).

## §4 Alt-Bileşen Sınıflandırması (master §3.1)

| # | Sınıf | Örnek | Kritik parametre | Alt-konu |
|---|---|---|---|---|
| 1 | Direnç | metal film 1% | TCR, gürültü, parasitik L | devre-ve-analog |
| 2 | Kondansatör | MLCC X7R | ESR, DC bias kaybı, piezo | devre-ve-analog |
| 3 | Bobin | toroid choke | SRF, doygunluk akımı | guc-yonetimi |
| 4 | Op-amp | OPA1612, NE5532 | GBW, gürültü, THD+N | devre-ve-analog |
| 5 | Güç transistörü | NPN/PNP çift | SOA, hFE, termal | devre-ve-analog |
| 6 | PWM kontrolcü | UC3843, TPS40210 | anahtarlama frekansı | guc-yonetimi |
| 7 | LDO | LM1085, TPS7A24 | dropout, PSRR, gürültü | guc-yonetimi |
| 8 | Sensör arayüzü | INA219, ADS1115 | doğruluk, drift | sensor-ve-akteyici |
| 9 | Aktüatör sürücü | DRV8825, DRV8874 | akım sınırı, mikro adım | sensor-ve-akteyici |
| 10 | Referans | shunt, bandgap | ppm/°C drift | devre-ve-analog |

### §4.1 Ses Sinyal Zinciri (master §3.2 — 7 aşama)

Giriş (RCA/XLR 47kΩ) → differential pair (BJT, CMRR) → VAS → driver
(Darlington/bootstrap) → complementary push-pull → negatif geri besleme →
DC offset rölesi (koruma).

### §4.2 Güç Zinciri (master §3.3 — 6 aşama)

DC 12-24V → boost (12V→51V Class D / ±42V Class AB) → EMI LC filtre →
gate drive GVDD 12V → smoothing 10,000µF → over-voltage >±48V koruma.

## §5 Arayüz Bileşenleri (master §4)

| Grup | Arayüzler | Sınır |
|---|---|---|
| Dahili (kart içi) | I2S (3.072MHz BCLK, 8-kanal) · TDM · SPI (DAC config) · I2C (100/400kHz) · GPIO · PLL clock (jitter <1ps hedef) | K002 şartnamesi |
| Dış (cihaz-ösü) | USB Audio 2.0 · BT 5.0+ · WiFi · Ethernet · analog giriş/çıkış | komşu K'lar |
| Yazılım | Register map (K002 şartname) · Boot sequence (kod K005) · HAL API (K000) · DSP zinciri (K004) | **kod bu domainde YOK** |

## §6 Bağımlılık Yönü

```text
K000-isletim-sistemi ← K005-firmware ← K002 (şartname akışı)
        ↑                    ↑
   HAL referansı        boot/register kodu
K001-donanım (parça envanteri) ← K002 (tasarlanan kart)
K004-dsp (yalnız referans) · K002-altyapi ⚠️ (ölü referans — diskte YOK)
```

## §7 Kanıt

- `electroncis-master.md` §2 (satır 52) · §3 (138) · §4 (181) · §9 (350)
- disk glob 2026-10-18: 5 alt dizin × 4 dosya + root 6 md
- `electroncis-kararlar.md` §2 (ADR bağları)

## §8 ⚠️ Bilinen Bozuk Referanslar (salt rapor — DÜZELTME YASAK)

- `K002-altyapi` · `K002-data` — ölü K referansları (diskte yok).
- `K000-isletim-sistemi/electroncis/**` (4 dosya) — master §9 #2'de "2 alt-dal — ayrı ajan" notu var; bu oturumda dokunulmadı.
