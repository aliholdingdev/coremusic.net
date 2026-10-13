---
node_id: K000-isletim-sistemi/electroncis/electroncis-kapsam
node_type: module
status: verify-required
parent_id: K000-isletim-sistemi/electroncis
children: []
depends_on:
  - K001-donanım
  - K004-dsp
  - K005-firmware
evidence:
  - ".ai/architecture/K000-isletim-sistemi/os-master.md §3 · §8"
  - ".ai/architecture/K000-isletim-sistemi/os-bagimliliklar.md §5"
  - ".ai/electronic/** (60 md · 10.323 sat · ölçüm 2026-10-13)"
validation: "2026-10-13 · §A-§H yazıldı · children 2 alt-dal disk kanıtlı"
open_questions:
  - "Q-E1: electroncis siniri K001 ile kapanacak mi? → ⚠️ VERIFICATION REQUIRED"
  - "Q-E4: K002 ID GAP bu dali etkiliyor mu? → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# electroncis — Kapsam

> **Kapsam durumu: `UNKNOWN` (Vault Steward Q1).** Bu dosya tahmin üretmez; yalnız kanıt,
> sınır kararı ve açık soru taşır. Yazım `electroncis` (v2 §3 KURAL 1) DOKUNULMAZ.
> Bu tur: 2 alt-dal açıldı (`devre-tasarimi` · `guc-ve-koruma`) — K-node yalnız bu ikisini kapsar.

## §A — Kapsam

### §A.1 Bu turda açılan yüzey

| # | Yüzey | Kademe | Dosya | Durum |
|---|---|---|---|---|
| A1 | `electroncis/` kökü | Kademe-2 | index + 3 içerik dosyası | `verify-required` |
| A2 | `devre-tasarimi/` | Kademe-3 | index + 3 içerik dosyası | `draft` |
| A3 | `guc-ve-koruma/` | Kademe-3 | index + 3 içerik dosyası | `draft` |
| A4 | Toplam dosya (bu tur) | Kademe-2/3/4 | 12 | ölçüm 2026-10-13 |
| A5 | Kapsam tanımı (v2 §2.1) | — | YOK | ⚠️ VERIFICATION REQUIRED |
| A6 | Alt-dal sayısı | Kademe-3 | 2 | disk kanıtlı |

### §A.2 Kapsam İÇİNDE ne var

| # | Öğe | Kapsam durumu | Dayanak |
|---|---|---|---|
| B1 | Devre tasarımı (şema → simülasyon → PCB girişi) | BU TURDA AÇILDI | `devre-tasarimi/` |
| B2 | Güç kaynakları ve koruma devreleri | BU TURDA AÇILDI | `guc-ve-koruma/` |
| B3 | Analog/dijital devre temelleri (bileşen, op-amp, ADC/DAC) | alt-dal içeriği | `devre-tasarimi/analog-ve-dijital-devre-temelleri.md` |
| B4 | Doğrulama ve ölçüm planı (THD · SNR · eşik) | alt-dal içeriği | `devre-simulasyonu-ve-dogrulama.md` |
| B5 | Koruma eşikleri (akım · gerilim · termal · ESD) | alt-dal içeriği | `guc-ve-koruma/koruma-devreleri-ve-esikler.md` |
| B6 | OS ↔ donanım sürücü arayüzü kavramı | **komşu kavram** — kapsam DEĞİL | kök `index.md` §3 |
| B7 | Kapsam içeriği (Q1) | **UNKNOWN** | `os-master.md` §8 |
| B8 | K000 içindeki işlev (Q1) | **UNKNOWN** | `os-master.md` §3 |

### §A.3 Kapsam DIŞI (nereye gider)

| # | Kapsam dışı alan | Nereye gider | Dayanak |
|---|---|---|---|
| C1 | DSP algoritması (EQ · reverb · crossover) | K004-dsp | görev manifesti · `os-master.md` §2 |
| C2 | Firmware (boot · RTOS · OTA) | K005-firmware | görev manifesti · `os-master.md` §2 |
| C3 | Donanım/PCB fiziksel tasarımı | K001-donanım | görev manifesti · `os-master.md` §2 |
| C4 | Ses API'si (ASIO · WASAPI · JACK) | K003-audio · `-audio` dalı | görev manifesti |
| C5 | Sürücü ve modül modeli (Linux/Windows) | `K000\isletim-sistemi-kernel\surucu-ve-modul-modeli` | görev manifesti |
| C6 | Ses motoru kodu (C++20 · JUCE) | K003-audio / K004-dsp | repo taraması (aşağıda §A.5) |
| C7 | Web/uç nokta PHP kodu | ilgili K-düğümleri | repo taraması |
| C8 | Güç dağıtımı teslim/tesisat (bina) | kapsam dışı — K001 bile değil | K-node kararı |
| C9 | `.ai/electronic/**` içeriğinin derinleştirilmesi | 3 ayrı ajan (bu K-node değil) | görev manifesti |
| C10 | K000 kök dosyaları (index · os-master · os-bagimliliklar · os-dogrulama) | K000-kök ajanının beyazlistesi | görev manifesti |

### §A.4 Kapsam karar durumu

| # | Soru | Karar | Tarih |
|---|---|---|---|
| D1 | Bu K-node hangi alt-dalları kapsar? | 2 alt-dal: `devre-tasarimi` · `guc-ve-koruma` | 2026-10-13 |
| D2 | `electronics` yazım düzeltmesi yapılır mı? | HAYIR — `electroncis` dokunulmaz | v2 §3 KURAL 1 |
| D3 | K001 sınırı kapatıldı mı? | HAYIR — Q1 açık | 2026-10-13 |
| D4 | `.ai/electronic` içeriği bu dosyaya kopyalanır mı? | HAYIR — yalnız atıf | 2026-10-13 |
| D5 | Kapsam UNKNOWN → verified geçişi | YALNIZ Q-E1 kapandığında | 2026-10-13 |

### §A.5 Repo taraması (kapsam sınırı kanıtı)

| # | Tarama | Sonuç | Tarih |
|---|---|---|---|
| E1 | `*.cpp` / `*.c` / `*.h` (vendor/node_modules hariç) | **0 dosya** — devre seviyesi kod YOK | 2026-10-13 |
| E2 | `NevaEngine/` kaynak ağacı | PLANNED (sartname §8) | 2026-10-13 |
| E3 | PHP/JS/CSS/SQL yüzeyi | K-node KAPSAM DIŞI (§A.3 C7) | 2026-10-13 |
| E4 | Devre seviyesi kod kanıtı | `Repo'da devre seviyesi kod YOK — kaynak: .ai/electronic/** (60 dosya)` | 2026-10-13 |

## §B — 2 alt-dal envanteri

### §B.1 Alt-dal genel bakış

| # | Alt-dal | node_type | Dosya | Kademe | Grup ağırlığı |
|---|---|---|---|---|---|
| F1 | `devre-tasarimi` | subdomain | 4 | Kademe-3 | mimari |
| F2 | `guc-ve-koruma` | subdomain | 4 | Kademe-3 | mimari |
| F3 | Toplam | — | 8 | Kademe-3/4 | — |
| F4 | Kök dosyalar (bu tur yeniden yazım) | subdomain + module | 4 | Kademe-2 | mimari · kod · kanit |

### §B.2 `devre-tasarimi/` dosya envanteri

| # | Dosya | node_id sonu | group | Hedef satır |
|---|---|---|---|---|
| G1 | `index.md` | `/devre-tasarimi` | mimari | 40–100 |
| G2 | `devre-tasarimi-anayasa.md` | `/devre-tasarimi-anayasa` | mimari | ≥500 |
| G3 | `analog-ve-dijital-devre-temelleri.md` | `/analog-ve-dijital-devre-temelleri` | mimari | ≥500 |
| G4 | `devre-simulasyonu-ve-dogrulama.md` | `/devre-simulasyonu-ve-dogrulama` | mimari | ≥500 |

### §B.3 `guc-ve-koruma/` dosya envanteri

| # | Dosya | node_id sonu | group | Hedef satır |
|---|---|---|---|---|
| H1 | `index.md` | `/guc-ve-koruma` | mimari | 40–100 |
| H2 | `guc-ve-koruma-anayasa.md` | `/guc-ve-koruma-anayasa` | mimari | ≥500 |
| H3 | `guc-kaynaklari-ve-regulatorler.md` | `/guc-kaynaklari-ve-regulatorler` | mimari | ≥500 |
| H4 | `koruma-devreleri-ve-esikler.md` | `/koruma-devreleri-ve-esikler` | mimari | ≥500 |

### §B.4 Sorumluluk dağılımı

| # | Konu | Sahip alt-dal | Komşu K-node |
|---|---|---|---|
| I1 | Şema tasarımı | `devre-tasarimi` | K001-donanım |
| I2 | Simülasyon + Monte Carlo | `devre-tasarimi` | — |
| I3 | Buck/boost topolojileri | `guc-ve-koruma` | — |
| I4 | Aşırı akım/gerilim eşiği | `guc-ve-koruma` | K005-firmware (koruma döngüsü) |
| I5 | PCB yerleşim + ısıl tasarım | — (kapsam dışı) | K001-donanım |
| I6 | Filtre algoritması (dijital) | — (kapsam dışı) | K004-dsp |
| I7 | Sürücü katmanı | — (kapsam dışı) | `isletim-sistemi-kernel` |
| I8 | Ölçüm metodu (THD/SNR) | `devre-tasarimi` | — |

## §C — Elektronik mimari katmanları (devre → kart → cihaz → ekosistem)

### §C.1 Katman tanımı

| # | Katman | Kapsadığı | Bu K-node içinde mi? | Sınır |
|---|---|---|---|---|
| J1 | **Devre (circuit)** | bileşen · topoloji · şema · hesap | EVET — 2 alt-dal | — |
| J2 | **Kart (board)** | PCB yerleşim · iz · katman · montaj | HAYIR | K001-donanım |
| J3 | **Cihaz (device)** | gövde · soğutma · arayüz · bağlantı | HAYIR | K001-donanım |
| J4 | **Ekosistem (ecosystem)** | sürücü · firmware · API · ağ | HAYIR | K000-kernel · K003 · K005 |

### §C.2 Katman × alt-dal matrisi

| # | Katman öğesi | `devre-tasarimi` | `guc-ve-koruma` | Dışarıda |
|---|---|---|---|---|
| K1 | Besleme devresi (AC→DC) | ortak | ANA SAHİP | — |
| K2 | Amplifikatör devresi | ANA SAHİP | koruma katmanı | K004-dsp (algoritma) |
| K3 | Filtre devresi (analog) | ANA SAHİP | — | K004-dsp (dijital) |
| K4 | Koruma devresi (akım/gerilim) | destek | ANA SAHİP | K005-firmware |
| K5 | Termal tasarım | destek | destek | K001-donanım |
| K6 | PCB yerleşimi | — | — | K001-donanım |
| K7 | Sürücü/firmware | — | — | kernel · K005 |
| K8 | Ölçüm ve doğrulama | ANA SAHİP | ANA SAHİP | — |

### §C.3 Katmanlar arası tipik zincir

| # | Adım | Girdi | Çıktı | Sahip |
|---|---|---|---|---|
| L1 | Şebeke/DC giriş | 230 V AC / 12–24 V DC | ham DC | `guc-ve-koruma` |
| L2 | Filtreleme + regüle | ham DC | temiz DC | `guc-ve-koruma` |
| L3 | Koruma katmanı | temiz DC | korumalı DC | `guc-ve-koruma` |
| L4 | Sinyal zinciri | analog/dijital giriş | işlenmiş analog | `devre-tasarimi` |
| L5 | Dönüştürücü (ADC/DAC) | analog | dijital | `devre-tasarimi` |
| L6 | Dijital işleme | dijital örnek | dijital örnek | K004-dsp |
| L7 | Çıkış sürücüsü | düşük seviye | yük sürücüsü | `devre-tasarimi` |
| L8 | Ölçüm doğrulama | fiziksel çıktı | rapor | 2 alt-dal ortak |

### §C.4 Katman riskleri

| # | Risk | Katman | Etki | İşaret |
|---|---|---|---|---|
| M1 | Devre hatası → kart revizyonu | devre → kart | maliyet + süre | yüksek |
| M2 | Koruma eşiği yanlış → hasar | devre | donanım kaybı | kritik |
| M3 | Isıl tasarım yetersiz → drift | kart/cihaz | performans | yüksek |
| M4 | Sürücü uyumsuz → cihaz görünmez | ekosistem | işlev kaybı | orta |
| M5 | Ölçüm metodu yanlış → yanlış onay | devre | yanlış güven | kritik |

## §D — Bağımlılık grafiği (electroncis ↔ K001/K004/K005/.ai/electronic)

### §D.1 Dış bağımlılıklar (bu düğüm → dış)

| # | Hedef | Bağımlılık türü | Yön | Durum |
|---|---|---|---|---|
| N1 | K001-donanım | sınır (PCB · kart · cihaz) | electroncis → K001 | ⚠️ sınır boşluğu |
| N2 | K004-dsp | sınır (dijital filtre/algoritma) | electroncis → K004 | taslak |
| N3 | K005-firmware | sınır (koruma döngüsü · OTA) | electroncis → K005 | taslak |
| N4 | K003-audio · `-audio` | sınır (ses API'si) | electroncis → K003 | taslak |
| N5 | `isletim-sistemi-kernel` | sınır (sürücü modeli) | electroncis → K000-kernel | taslak |
| N6 | `.ai/electronic/**` | kaynak atıf (içerik kopyalanmaz) | electroncis → vault | atıf |

### §D.2 Gelen bağımlılıklar (dış → bu düğüm)

| # | Kaynak | Ne getirir | Yön | Durum |
|---|---|---|---|---|
| O1 | K000 kök (`os-master.md`) | çocuk satırı + Q1 | K000 → electroncis | doğrulandı |
| O2 | K001-donanım | kart/PCB tasarım girdisi | K001 → electroncis | ⚠️ VERIFY |
| O3 | K005-firmware | koruma döngüsü gereksinimi | K005 → electroncis | taslak |
| O4 | K004-dsp | filtre kesim frekansı gereksinimi | K004 → electroncis | taslak |

### §D.3 Bağımlılık yoğunluğu

| # | Kenar | Sayı | Not |
|---|---|---|---|
| P1 | electroncis → dış K-node | 5 | hepsi `border` tipinde |
| P2 | dış → electroncis | 4 | hiçbiri `must` değil |
| P3 | electroncis → `.ai/electronic` | 1 | tek yönlü atıf |
| P4 | iç bağımlılık (alt-dal → kök) | 6 | `depends_on: []` dolu değil |

## §E — Sınır komşuları

### §E.1 Sınır komşu listesi

| # | Komşu | Sınır konusu | Ayrım kuralı | Etiket |
|---|---|---|---|---|
| Q1 | K001-donanım | PCB · kart · mekanik · ısıl | şema sonu = PCB girişi | ⚠️ VERIFY |
| Q2 | K004-dsp | dijital filtre · EQ · dinamik | analog devre sonu = DSP girişi | taslak |
| Q3 | K005-firmware | boot · RTOS · koruma döngüsü | devre eşiği = firmware gözetimi | taslak |
| Q4 | K003-audio / `-audio` | ses API'si · ASIO/WASAPI | devre = fiziksel, API = yazılım | taslak |
| Q5 | `isletim-sistemi-kernel` | sürücü · modül | devre ≠ sürücü | taslak |
| Q6 | K002-altyapi | ID GAP | Q1c | ⚠️ VERIFY |

### §E.2 Bilinen sınır boşlukları

| # | Boşluk | Kaynak | Kapanma koşulu |
|---|---|---|---|
| R1 | electroncis ↔ K001 sınırı tanımsız | `os-bagimliliklar.md` §5 (Q1) | Vault Steward kararı |
| R2 | electroncis ↔ K005 sınırı tanımsız | `os-master.md` §8 | Q1 kararı |
| R3 | K002-altyapi ID GAP etkisi bilinmiyor | registry §6 | registry düzeltmesi |
| R4 | Alt-dal ↔ K001 PCB girişi net değil | bu tur | Vault Steward |

### §E.3 Sınır ihlali belirtileri

| # | Belirti | Örnek | Sonuç |
|---|---|---|---|
| S1 | PCB yerleşim tartışması bu düğümde | iz genişliği · katman sayısı | K001'e taşı |
| S2 | C++ DSP kodu bu düğümde | biquad · FFT | K004'e taşı |
| S3 | OTA/boot kodu bu düğümde | flash · bootloader | K005'e taşı |
| S4 | ASIO/WASAPI tartışması bu düğümde | buffer · exclusive lock | K003'e taşı |
| S5 | Sürücü API tartışması bu düğümde | ioctl · IRP | kernel dalına taşı |

## §F — Açık sorular

| # | Soru | Etiket | Sahip | Hedef |
|---|---|---|---|---|
| T1 | electroncis neyi temsil ediyor? (Q1) | ⚠️ VERIFICATION REQUIRED | Vault Steward | K000 kök |
| T2 | Dal elektronik donanımı (K001) mi, OS sürücü katmanı mı? | UNKNOWN | Vault Steward | K000 kök |
| T3 | K001 ↔ electroncis sınır boşluğu ne zaman kapanır? | ⚠️ VERIFICATION REQUIRED | Vault Steward | K000 kök |
| T4 | 2 alt-dal K001'in içine giriyor mu (PCB girişi)? | ⚠️ VERIFICATION REQUIRED | Vault Steward | bu dosya |
| T5 | K002-altyapi ID GAP bu dalı etkiliyor mu? | ⚠️ VERIFICATION REQUIRED | registry §6 | os-dogrulama |
| T6 | `.ai/electronic` 60 dosyası hangi sıklıkla senkronlanır? | UNKNOWN | Vault Steward | vault-sync |
| T7 | K004-dsp sınırında ortak dosya doğar mı? | UNKNOWN | Vault Steward | K004 |
| T8 | `status: verify-required` → `verified` geçişi Q1'e mi bağlı? | ⚠️ VERIFICATION REQUIRED | Vault Steward | bu dosya |

**Vault Steward kararı bekliyor (Q1).** Karar gelmeden bu dosyada kapsam satırı `UNKNOWN`
olarak kalır; `group: mimari` ve `parent_id` değişmez.

## §G — K-node ↔ .ai/electronic ayrım tablosu (hangisi ne, neden ayrı)

| # | Kriter | K-node (`electroncis/`) | Vault (`.ai/electronic/`) |
|---|---|---|---|
| U1 | Şema ailesi | v2 §4.2 (8 alan + `group`) | `type` · `category` · `title` + `[[wikilink]]` |
| U2 | ID biçimi | `node_id` (yol, uzantısız) | dosya adı |
| U3 | Link biçimi | düz yol referansı | `[[wikilink]]` |
| U4 | Amaç | mimari envanter + sınır + kanıt | derin teknik içerik |
| U5 | `group` alanı | VAR (9. alan) | YOK |
| U6 | `children` alanı | VAR (hiyerarşi) | YOK (yalnız klasör yapısı) |
| U7 | Kapsam sorusu | Q1 (Vault Steward) | yok — içerik odaklı |
| U8 | Sahip | Vault Steward (K-kayıt) | 3 derinleştirme ajanı |
| U9 | Kopyalama | YASAK (yalnız atıf) | YASAK (dokunulmaz) |
| U10 | Dosya sayısı (ölçüm) | 12 (bu tur) | 60 |
| U11 | Satır sayısı (ölçüm) | bu turda ölçüldü | 10.323 |
| U12 | Alt klasör | 2 (`devre-tasarimi` · `guc-ve-koruma`) | 5 (`amplifier` · `drivers` · `dsp` · `firmware` · `hardware`) |
| U13 | Neden ayrı? | K-node = mimari karar yüzeyi | vault = bilgi bankası |
| U14 | Karıştırılırsa ne olur? | şema bozulur → onarım gerekir | wikilink zinciri bozulur |

## §H — Kaynak envanteri (60 dosyalık `.ai/electronic` yapısı)

### §H.1 Dizin kırılımı (ölçüm 2026-10-13)

| # | Dizin | Dosya | İçerik dosyası (yaklaşık satır) | Not |
|---|---|---|---|---|
| V1 | `.ai/electronic/` kök | 17 | 15 içerik + AGENTS + CLAUDE | genel mimari |
| V2 | `amplifier/` | 8 | 6 içerik + AGENTS + CLAUDE | sınıf AB/D · güç · koruma · termal |
| V3 | `drivers/` | 10 | 8 içerik + AGENTS + CLAUDE | ses · USB · BT · WiFi · ağ |
| V4 | `dsp/` | 10 | 8 içerik + AGENTS + CLAUDE | EQ · filtre · dinamik · crossover |
| V5 | `firmware/` | 7 | 5 içerik + AGENTS + CLAUDE | boot · RTOS · OTA |
| V6 | `hardware/` | 8 | 6 içerik + AGENTS + CLAUDE | PCB · arayüz · SNR/THD ölçüm |
| V7 | **Toplam** | **60** | **50 içerik + 10 yönetim** | 10.323 satır |

### §H.2 Kök içerik dosyaları (15)

| # | Dosya | Satır | Konu | Bu K-node ile ilişki |
|---|---|---|---|---|
| W1 | `index.md` | 143 | vault indeksi | atıf |
| W2 | `core-music-electronics-overview.md` | 222 | elektronik genel bakış | uzak |
| W3 | `amplifier-architecture.md` | 313 | amplifikatör mimarisi | `devre-tasarimi` ile ilişkili |
| W4 | `audio-architecture.md` | 303 | ses mimarisi | K003 komşusu |
| W5 | `device-architecture.md` | 264 | cihaz mimarisi | K001 komşusu |
| W6 | `device-ecosystem.md` | 300 | cihaz ekosistemi | ekosistem katmanı |
| W7 | `development-workflow.md` | 501 | geliştirme akışı | süreç |
| W8 | `driver-framework.md` | 457 | sürücü çerçevesi | kernel dalı komşusu |
| W9 | `dsp-engine-architecture.md` | 425 | DSP motoru | K004 komşusu |
| W10 | `firmware-architecture.md` | 351 | firmware mimarisi | K005 komşusu |
| W11 | `hardware-design.md` | 471 | donanım tasarımı | K001 komşusu |
| W12 | `operating-system-architecture.md` | 231 | OS mimarisi | K000 kökü |
| W13 | `platform-architecture.md` | 211 | platform mimarisi | K000 kökü |
| W14 | `service-architecture.md` | 328 | servis mimarisi | uzak |
| W15 | `software-architecture.md` | 194 | yazılım mimarisi | uzak |

### §H.3 Alt klasör içerik dosyaları (35 + 10 yönetim)

| # | Dizin | Dosyalar (satır) | Konu |
|---|---|---|---|
| X1 | `amplifier/` | `index`(220) · `class-ab`(93) · `class-d`(97) · `power-supply`(215) · `protection`(53) · `thermal`(59) | güçlendirici |
| X2 | `drivers/` | `index`(142) · `audio-drivers`(91) · `bluetooth-drivers`(72) · `embedded-drivers`(62) · `network-drivers`(48) · `usb-drivers`(69) · `wifi-drivers`(70) · `device-integration`(54) | sürücüler |
| X3 | `dsp/` | `index`(139) · `dsp-pipeline`(137) · `effects`(133) · `equalizer`(104) · `dynamics`(92) · `loudness`(90) · `crossover`(78) · `filters`(78) | DSP |
| X4 | `firmware/` | `index`(152) · `boot-sequence`(62) · `dsp-firmware`(55) · `ota-update`(54) · `rtos`(49) | firmware |
| X5 | `hardware/` | `index`(152) · `pcb-design`(65) · `audio-interface`(66) · `snr-thd-measurement`(62) · `frequency-response`(51) · `amplifier-pcb`(50) | donanım |
| X6 | yönetim dosyaları | her klasörde `AGENTS.md` + `CLAUDE.md` (5×2 + kök 2 = 12) | vault kuralları |

### §H.4 Bu K-node'un `.ai/electronic` ile kesişimi

| # | Kesişim konusu | İlgili dosya (vault) | İlgili dosya (K-node) | Aksiyon |
|---|---|---|---|---|
| Y1 | Güç kaynağı topolojisi | `amplifier/power-supply.md` | `guc-ve-koruma/guc-kaynaklari-ve-regulatorler.md` | yalnız atıf |
| Y2 | Koruma devresi | `amplifier/protection.md` | `guc-ve-koruma/koruma-devreleri-ve-esikler.md` | yalnız atıf |
| Y3 | Termal | `amplifier/thermal.md` | `guc-ve-koruma-anayasa.md` §5 | yalnız atıf |
| Y4 | SNR/THD ölçüm | `hardware/snr-thd-measurement.md` | `analog-ve-dijital-devre-temelleri.md` §5 | yalnız atıf |
| Y5 | PCB | `hardware/pcb-design.md` | KAPSAM DIŞI (K001) | sınır |
| Y6 | DSP filtreleri | `dsp/filters.md` | KAPSAM DIŞI (K004) | sınır |
| Y7 | Firmware koruma döngüsü | `firmware/*` | KAPSAM DIŞI (K005) | sınır |
| Y8 | Sürücü | `driver-framework.md` | KAPSAM DIŞI (kernel) | sınır |

### §A.6 Kapsam beyanı taslağı (Q1 kararı öncesi — BAĞLAYICI DEĞİL)

| # | Beyan | Durum | Kanıt |
|---|---|---|---|
| Z1 | `electroncis` = elektronik devre tasarımı + güç/koruma yüzeyi (K000 içi) | taslak | bu tur açılan 2 alt-dal |
| Z2 | `electroncis` = OS sürücü katmanı | REDDEDİLDİ (aday) | sürücü → `isletim-sistemi-kernel` (§A.3 C5) |
| Z3 | `electroncis` = K001 donanımın tekrarı | REDDEDİLDİ (aday) | PCB/kart KAPSAM DIŞI (§A.3 C3) |
| Z4 | `electroncis` = `.ai/electronic` içeriğinin K-node karşılığı | KISMEN | yalnız atıf (§G U9) |
| Z5 | Beyan 1-4 bağlayıcı değil | ⚠️ VERIFICATION REQUIRED | Q1 açık |
| Z6 | `status` bu beyanlarla `verified` olmaz | KURAL | §A.4 D5 |

### §A.7 Reddedilen kapsam adayları

| # | Aday kapsam | Neden reddedildi | Yeniden açılış koşulu |
|---|---|---|---|
| AA1 | Tam donanım kapsamı (K001 ile aynı) | çift kayıt → çakışma | Vault Steward kararı |
| AA2 | DSP algoritma kapsamı | K004'e ayrılmış | manifesto |
| AA3 | Firmware kapsamı | K005'e ayrılmış | manifesto |
| AA4 | Ses API kapsamı | K003'e ayrılmış | manifesto |
| AA5 | Sürücü modeli kapsamı | kernel dalına ayrılmış | manifesto |
| AA6 | PCB yerleşim kapsamı | K001'e ayrılmış | manifesto |

### §C.5 Katman × ekipman/ölçüm matrisi

| # | Katman | Tipik ekipman | Tipik ölçüm | Bu K-node |
|---|---|---|---|---|
| AB1 | Devre | osiloskop · fonksiyon jeneratörü | dalga biçimi · frekans | EVET |
| AB2 | Devre | güç kaynağı · yük | verim · ripple | EVET |
| AB3 | Devre | analyzer (THD/SNR) | THD+N · SNR | EVET |
| AB4 | Kart | AOI · test jig | süreklilik · iz direnci | HAYIR (K001) |
| AB5 | Cihaz | termal kamera · chamber | sıcaklık · drift | HAYIR (K001) |
| AB6 | Ekosistem | host PC · sürücü logu | latency · hata sayacı | HAYIR (K000-kernel/K003) |

### §C.6 Devre tasarımı girdi-çıktı envanteri

| # | Girdi | Kaynak | Çıktı | Sahip alt-dal |
|---|---|---|---|---|
| AC1 | Gereksinim (bant · kazanç · seviye) | K003/K004 | şartname parçası | `devre-tasarimi` |
| AC2 | Besleme gerilimi | sistem | güç ağacı | `guc-ve-koruma` |
| AC3 | Koruma eşiği | güvenlik | eşik tablosu | `guc-ve-koruma` |
| AC4 | Bileşen modelleri | üretici datasheet | simülasyon netlisti | `devre-tasarimi` |
| AC5 | Tolerans seti | üretim | Monte Carlo girdisi | `devre-tasarimi` |
| AC6 | Ölçüm planı | doğrulama | test prosedürü | iki alt-dal |
| AC7 | PCB tasarım girdisi | bu K-node | şema çıktısı | → K001 sınırı |
| AC8 | Firmware gereksinimi | K005 | koruma parametresi | → K005 sınırı |

### §D.4 Bağımlılık kuralları

| # | Kural | Uygulama | İhlal durumu |
|---|---|---|---|
| AD1 | K-node ↔ vault: tek yönlü atıf | `.ai/electronic` okunmaz/yazılmaz | şema bozulması |
| AD2 | K-node ↔ K-node: `depends_on` ile | 5 dış bağımlılık | — |
| AD3 | `evidence` alanı disk kanıtı ister | her iddia dosya yolu | kanıtsız iddia yasak |
| AD4 | `open_questions` alanı açık soruyu taşır | Q1 · Q1c | — |
| AD5 | `validation` alanı tarih + ölçüm ister | 2026-10-13 | tarihsiz validation yasak |
| AD6 | Alt-dal → kök: `parent_id` | `/electroncis` | `parent:` anahtarı yasak |

### §E.4 Sınır komşu ↔ dosya eşlemesi

| # | Komşu | Bu düğümdeki karşılığı | Ayrım satırı |
|---|---|---|---|
| AE1 | K001-donanım | yok (kapsam dışı) | şema teslimi biter |
| AE2 | K004-dsp | analog filtre tasarımı | dijitalleşmeden önce |
| AE3 | K005-firmware | koruma eşik değerleri | çalışma anı değil tasarım anı |
| AE4 | K003-audio | ADC/DAC zinciri seçimi | API katmanı değil |
| AE5 | kernel dalı | sürücü modeli | elektronik ≠ sürücü |
| AE6 | K002-altyapi | ID GAP etkisi | registry §6 |
| AE7 | `.ai/electronic` | atıf satırı | içerik kopyalanmaz |
| AE8 | K001 termal | ısıl hesap girişi | mekanik tasarım K001 |

### §F.2 Ek açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| AF1 | Alt-dal dosya adları Türkçe olmak zorunda mı? | UNKNOWN | Vault Steward |
| AF2 | `group: mimari` konu dosyaları için doğru mu? | UNKNOWN | registry §8.1 |
| AF3 | `node_type: module` Kademe-4 için geçerli mi? | ⚠️ VERIFICATION REQUIRED | v2 §4.2 |
| AF4 | `children` listesi Kademe-4'te neden `[]`? | bilinçli — dosya yaprak | — |
| AF5 | `.ai/electronic` senkronu ne sıklıkla? | UNKNOWN | vault-sync |
| AF6 | Ölü URL listesi güncellenmeli mi? | ⚠️ VERIFICATION REQUIRED | dogrulama dosyası |
| AF7 | K001 termal sınırı hangi belgeyle kapanır? | UNKNOWN | Vault Steward |
| AF8 | K004 filtre sınırı hangi belgeyle kapanır? | UNKNOWN | Vault Steward |
| AF9 | Web doğrulama tarihi her turda yenilenir mi? | UNKNOWN | Vault Steward |
| AF10 | Repo'da C/C++ yokluğu kalıcı mı? | UNKNOWN | PLANNED yüzeyi |
| AF11 | 12 dosyalık tur sonrası log kaydı gerekir mi? | ⚠️ VERIFICATION REQUIRED | WORKFLOW |
| AF12 | `verify-required` → `verified` tek Q1'e bağlı mı? | ⚠️ VERIFICATION REQUIRED | Vault Steward |

### §G.2 Alan-bazlı şema karşılaştırması

| # | Alan | K-node v2 §4.2 | Vault `.ai/electronic` |
|---|---|---|---|
| AG1 | `node_id` | VAR (zorunlu) | YOK |
| AG2 | `node_type` | VAR (module/subdomain) | YOK |
| AG3 | `status` | VAR (draft/verify-required) | `status` benzeri alan kısıtlı |
| AG4 | `parent_id` | VAR (hiyerarşi) | klasör yolu |
| AG5 | `children` | VAR (liste) | YOK |
| AG6 | `depends_on` | VAR (liste) | YOK |
| AG7 | `evidence` | VAR (liste) | kaynak linki (wikilink) |
| AG8 | `validation` | VAR (liste/tarih) | YOK |
| AG9 | `open_questions` | VAR (liste) | YOK |
| AG10 | `group` | VAR (9. alan, tırnaksız) | YOK |
| AG11 | `title` | YOK (başlık H1'de) | VAR |
| AG12 | `type` / `category` | YOK | VAR |

### §H.5 Envanter ölçüm metodu

| # | Adım | Komut/yanıt | Sonuç |
|---|---|---|---|
| AH1 | Dosya sayımı | `Get-ChildItem -Recurse -Filter *.md` | 60 |
| AH2 | Satır sayımı | `Get-Content \| Measure-Object -Line` | 10.323 |
| AH3 | Dizin sayısı | kök + 5 alt klasör | 6 |
| AH4 | URL taraması | regex `https?://` | 0 (vault'ta URL yok) |
| AH5 | Mimari envanter URL | aynı regex, `.ai/architecture/**` | 1.854 |
| AH6 | Kesişim | vault ∩ mimari | 0 |
| AH7 | Yeni URL (bu tur) | 15 adet, tümü HTTP 200 | kesişim 0 |
| AH8 | Tarih | 2026-10-13 | — |

### §H.6 Satır yoğunluğu (kök 15 dosya)

| # | Dosya | Satır | Yoğunluk | Not |
|---|---|---|---|---|
| AI1 | `development-workflow.md` | 501 | yüksek | en uzun kök dosya |
| AI2 | `hardware-design.md` | 471 | yüksek | K001 sınırında |
| AI3 | `driver-framework.md` | 457 | yüksek | kernel sınırında |
| AI4 | `dsp-engine-architecture.md` | 425 | yüksek | K004 sınırında |
| AI5 | `firmware-architecture.md` | 351 | orta-yüksek | K005 sınırında |
| AI6 | `service-architecture.md` | 328 | orta | uzak |
| AI7 | `amplifier-architecture.md` | 313 | orta | `devre-tasarimi` ile ilişkili |
| AI8 | `device-ecosystem.md` | 300 | orta | ekosistem |
| AI9 | `audio-architecture.md` | 303 | orta | K003 sınırında |
| AI10 | `device-architecture.md` | 264 | orta | K001 sınırında |
| AI11 | `operating-system-architecture.md` | 231 | orta | K000 kökü |
| AI12 | `core-music-electronics-overview.md` | 222 | orta | genel |
| AI13 | `platform-architecture.md` | 211 | orta | K000 kökü |
| AI14 | `software-architecture.md` | 194 | düşük-orta | uzak |
| AI15 | `index.md` | 143 | düşük | indeks |

### §H.7 Kök 15 dosyanın konuya göre dağılımı (atıf haritası)

| # | Konu kümesi | Vault dosyası | Bu K-node karşılığı | İlişki tipi |
|---|---|---|---|---|
| AJ1 | Elektronik genel | `core-music-electronics-overview.md` | kök `index.md` | atıf |
| AJ2 | Güçlendirici + koruma + termal | `amplifier-architecture.md` · `amplifier/*` | `guc-ve-koruma/` | atıf |
| AJ3 | Devre temelleri | `audio-architecture.md` (kısmen) | `analog-ve-dijital-devre-temelleri.md` | atıf |
| AJ4 | PCB · kart | `hardware-design.md` · `hardware/*` | yok (K001) | sınır |
| AJ5 | DSP | `dsp-engine-architecture.md` · `dsp/*` | yok (K004) | sınır |
| AJ6 | Firmware | `firmware-architecture.md` · `firmware/*` | yok (K005) | sınır |
| AJ7 | Sürücü | `driver-framework.md` · `drivers/*` | yok (kernel) | sınır |
| AJ8 | OS/platform | `operating-system-architecture.md` · `platform-architecture.md` | K000 kökü | üst düğüm |
| AJ9 | Yazılım/servis | `software-architecture.md` · `service-architecture.md` | yok | uzak |
| AJ10 | Cihaz ekosistemi | `device-ecosystem.md` · `device-architecture.md` | yok (K001) | sınır |
| AJ11 | Geliştirme akışı | `development-workflow.md` | yok (WORKFLOW) | süreç |
| AJ12 | Ölçüm (SNR/THD) | `hardware/snr-thd-measurement.md` | `devre-simulasyonu-ve-dogrulama.md` | atıf |

### §B.5 Önerilen okuma sırası (bu K-node içi)

| # | Sıra | Dosya | Amaç | Hedef okuyucu |
|---|---|---|---|---|
| AK1 | 1 | kök `index.md` | katalog + çocuklar | tüm ajanlar |
| AK2 | 2 | `electroncis-kapsam.md` | sınır + kapsam dışı | Vault Steward |
| AK3 | 3 | `electroncis-dogrulama.md` | kanıt durumu | QA/kanıt |
| AK4 | 4 | `electroncis-ornekler.md` | kod örnekleri | geliştirici |
| AK5 | 5 | `devre-tasarimi/index.md` | alt-dal kataloğu | elektronik ajan |
| AK6 | 6 | `devre-tasarimi-anayasa.md` | akış + katmanlar | elektronik ajan |
| AK7 | 7 | `analog-ve-dijital-devre-temelleri.md` | temeller | elektronik ajan |
| AK8 | 8 | `devre-simulasyonu-ve-dogrulama.md` | simülasyon + ölçüm | elektronik ajan |
| AK9 | 9 | `guc-ve-koruma/index.md` | alt-dal kataloğu | güç ajanı |
| AK10 | 10 | `guc-ve-koruma-anayasa.md` | güç mimarisi | güç ajanı |
| AK11 | 11 | `guc-kaynaklari-ve-regulatorler.md` | topolojiler | güç ajanı |
| AK12 | 12 | `koruma-devreleri-ve-esikler.md` | eşikler | güç ajanı |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
