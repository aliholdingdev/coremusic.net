---
node_id: K000-isletim-sistemi/electroncis/electroncis-kapsam
node_type: module
status: draft
parent_id: K000-isletim-sistemi/electroncis
children: []
depends_on:
  - K002-electroncis
  - K001-donanım
  - K004-dsp
  - K005-firmware
  - K000-isletim-sistemi/isletim-sistemi-audio
  - K000-isletim-sistemi/isletim-sistemi-kernel
  - K000-isletim-sistemi/os-donanim-tespiti
evidence:
  - ".ai/architecture/K000-isletim-sistemi/os-master.md §3 · §8 (Q1 — kapanan soru)"
  - ".ai/architecture/K002-electroncis/index.md (glob 2026-10-14 — taşınma hedefi)"
  - ".ai/electronic/drivers/audio-drivers.md (709 satır · okundu 2026-10-14 · atıf)"
  - ".ai/electronic/firmware/rtos.md (541 satır · okundu 2026-10-14 · atıf)"
  - "Vault Steward kararı 2026-10-14: electroncis = OS ile alakalı elektronik arayüz; tam elektronik → K002-electroncis"
validation: "2026-10-14 · Q1 kapandı (Vault Steward) · §A-§I yeniden yazıldı · children = 2 yeni alt-dal · Eski alt-dallara dokunulmadı"
open_questions:
  - "Q-E1: K002-altyapi ID GAP (os-master FM K002-altyapi diyor, diskte K002-electroncis var) çözülmedi — registry §6 sahibi"
  - "Q-E2: eski devre-tasarimi/guc-ve-koruma içeriklerinin K002-electroncis'e taşınma durumu ayrı süreç"
  - "Q-E3: status: draft → verified geçişi hangi kanıtla kapanır? → UNKNOWN (Vault Steward)"
group: mimari
---

# electroncis — Kapsam

> **Kapsam durumu: KARARLI (Vault Steward Q1 kapandı, 2026-10-14).** Bu dosya tahmin
> üretmez; yalnız karar, sınır ve açık soru taşır. Yazım `electroncis` (v2 §3 KURAL 1)
> DOKUNULMAZ. Bu tur: kök 4 dosya yeniden yazıldı + 2 yeni alt-dal açıldı
> (`donanim-arayuz-protokolleri` · `os-guc-elektronigi`); eski `devre-tasarimi`/`guc-ve-koruma`
> içerikleri **bu yazımın beyazlistesi dışındadır** (ayrı süreç silinecek).

## §0 ELI15

Bir işletim sistemi "ses çal" dediğinde aslında ne olur? Uygulama API'ye (WASAPI/ALSA) yazar,
API sürücüyü çağırır, sürücü **donanım register'larını** yazar: I2S veri hattını ve saatlerini
kurar, codec'e I2C/SPI ile register yazar, USB cihazını enumerer eder. Ayrı bir "güç düğmesi
basmak" da aynı şeyin tersidir: OS ACPI üzerinden "uyu" der, güç devresi rail'leri kapatır,
wake kaynağı gelince açar. `electroncis` bu iki yüzeyi — **ses/veri arayüzü protokolleri** ve
**OS gözünden güç elektroniği** — kapsar. Devrenin kendisi (şema, topoloji, eşik hesabı) ise
`K002-electroncis`'tir.

## §A — Kapsam karar durumu

### §A.1 Q1 karar kaydı (kapanan soru)

| # | Soru | Karar | Tarih | Sahip |
|---|---|---|---|---|
| A1 | electroncis neyi temsil ediyor? | **OS ile alakalı elektronik arayüz** — OS'in donanıma register/protokol seviyesinde yüzleştiği yüzey | 2026-10-14 | Vault Steward |
| A2 | Tam elektronik (devre tasarımı/güç devresi) kime ait? | **`K002-electroncis`** (fark analizi tamamlandı, taşınma ayrı ajan) | 2026-10-14 | Vault Steward |
| A3 | "donanımın kendisi" mi, "donanıma giden OS gözü" mü? | **OS gözü** — donanımın kendisi K002-electroncis/K001 | 2026-10-14 | Vault Steward |
| A4 | `status` ne olur? | `draft` — kapsam kararlı; `verified` ayrı kapı (Q-E3) | 2026-10-14 | bu dosya |
| A5 | Yazım düzeltmesi (`electronics`)? | HAYIR — `electroncis` dokunulmaz | v2 §3 KURAL 1 | anayasa |

### §A.2 Kapsam İÇİNDE ne var

| # | Öğe | Kapsam durumu | Dayanak |
|---|---|---|---|
| B1 | OS↔cihaz register/protokol arayüzü (I2S/TDM/PCM · MCLK/BCLK/FS) | ANA KAPSAM | `donanim-arayuz-protokolleri/` |
| B2 | I2C/SPI ile codec/ADC/DAC register kontrolü (OS gözü) | ANA KAPSAM | `donanim-arayuz-protokolleri/` |
| B3 | USB Audio Class enumerasyon/endpoint (OS tarafı) | ANA KAPSAM | `donanim-arayuz-protokolleri/` |
| B4 | Hotplug/mechanical detect pini (OS olay modeline giren arayüz) | ANA KAPSAM | `donanim-arayuz-protokolleri/` |
| B5 | Clock ağacının OS tarafından yönetimi | ANA KAPSAM | `donanim-arayuz-protokolleri/` |
| B6 | ACPI güç durumları (S0–S5 · D0–D3) ↔ gerçek güç devresi sinyalleri | ANA KAPSAM | `os-guc-elektronigi/` |
| B7 | PME/wake mantığı (wake-on-LAN/USB, wakeup kaynakları) | ANA KAPSAM | `os-guc-elektronigi/` |
| B8 | Termal okuma zinciri (sensor register → OS thermal zone → throttle/fan) | ANA KAPSAM | `os-guc-elektronigi/` |
| B9 | OS'nin koruma eşiğiyle ilişkisi (critical shutdown) | ANA KAPSAM | `os-guc-elektronigi/` |
| B10 | Repo'daki gerçek PHP/JS OS-yüzeyi örnekleri (satır-doğrulamalı) | destek kanıt | `electroncis-ornekler.md` |

### §A.3 Kapsam DIŞI (nereye gider)

| # | Kapsam dışı alan | Nereye gider | Dayanak |
|---|---|---|---|
| C1 | Devre tasarımı (şema · topoloji · güç devresi) | `K002-electroncis` | Vault Steward kararı 2026-10-14 |
| C2 | PCB/kart/fiziksel tasarım | `K001-donanım` | görev manifesti · `os-master.md` §2 |
| C3 | DSP algoritması (EQ · reverb · crossover) | `K004-dsp` | görev manifesti |
| C4 | Firmware (boot · RTOS · OTA) | `K005-firmware` | görev manifesti |
| C5 | Ses API'si (WASAPI · ALSA · CoreAudio · ASIO) | `isletim-sistemi-audio` | komşu sınır |
| C6 | Sürücü kodu (ring-0 C/C++) | `isletim-sistemi-kernel` | komşu sınır |
| C7 | Donanım tespiti/enumerasyon (ACPI/USB taraması) | `os-donanim-tespiti` | komşu sınır |
| C8 | K002 kök K-node içeriği (fark analizi + taşıma) | `K002-electroncis` | Vault Steward kararı |
| C9 | `.ai/electronic/**` vault içeriğinin derinleştirilmesi | vault (3 ajan) | görev manifesti |
| C10 | K000 kök dosyaları (index · os-master · os-bagimliliklar · os-dogrulama) | K000-kök ajanının beyazlistesi | görev manifesti |

## §B — Alt-dal envanteri (2 · Kademe-3)

### §B.1 Alt-dal genel bakış

| # | Alt-dal | node_type | Dosya | Kademe | Grup ağırlığı |
|---|---|---|---|---|---|
| D1 | `donanim-arayuz-protokolleri` | subdomain | 4 | Kademe-3 | mimari |
| D2 | `os-guc-elektronigi` | subdomain | 4 | Kademe-3 | mimari |
| D3 | Toplam (alt-dal) | — | 8 | Kademe-3/4 | — |
| D4 | Kök dosyalar (bu tur yeniden yazım) | subdomain + module | 4 | Kademe-2 | mimari · kod · kanit |

### §B.2 `donanim-arayuz-protokolleri/` dosya envanteri

| # | Dosya | node_id sonu | group | Hedef satır |
|---|---|---|---|---|
| E1 | `index.md` | `/donanim-arayuz-protokolleri` | mimari | 40–100 |
| E2 | `donanim-arayuz-protokolleri-anayasa.md` | `/donanim-arayuz-protokolleri-anayasa` | mimari | ≥250 (hedef ≥350) |
| E3 | `donanim-arayuz-protokolleri-ornekler.md` | `/donanim-arayuz-protokolleri-ornekler` | kod | ≥250 |
| E4 | `donanim-arayuz-protokolleri-dogrulama.md` | `/donanim-arayuz-protokolleri-dogrulama` | kanit | ≥250 |

### §B.3 `os-guc-elektronigi/` dosya envanteri

| # | Dosya | node_id sonu | group | Hedef satır |
|---|---|---|---|---|
| F1 | `index.md` | `/os-guc-elektronigi` | mimari | 40–100 |
| F2 | `os-guc-elektronigi-anayasa.md` | `/os-guc-elektronigi-anayasa` | mimari | ≥250 (hedef ≥350) |
| F3 | `os-guc-elektronigi-ornekler.md` | `/os-guc-elektronigi-ornekler` | kod | ≥250 |
| F4 | `os-guc-elektronigi-dogrulama.md` | `/os-guc-elektronigi-dogrulama` | kanit | ≥250 |

### §B.4 Sorumluluk dağılımı

| # | Konu | Sahip alt-dal | Komşu K-node |
|---|---|---|---|
| G1 | I2S/TDM/PCM veri hattı + saat timing'i | `donanim-arayuz-protokolleri` | `K002-electroncis` (devre) |
| G2 | I2C/SPI register kontrol yolu | `donanim-arayuz-protokolleri` | `K002-electroncis` |
| G3 | USB Audio Class (OS tarafı) | `donanim-arayuz-protokolleri` | `os-donanim-tespiti` (enumerasyon) |
| G4 | Clock ağacı yönetimi | `donanim-arayuz-protokolleri` | `K005-firmware` |
| G5 | ACPI S0–S5 ↔ güç devresi sinyalleri | `os-guc-elektronigi` | `K002-electroncis/guc-yonetimi` |
| G6 | PME/wake mantığı | `os-guc-elektronigi` | `os-donanim-tespiti` |
| G7 | Termal zincir (sensor → throttle → shutdown) | `os-guc-elektronigi` | `K002-electroncis` (sensör devresi) |
| G8 | Sürücü kodu | — (kapsam dışı) | `isletim-sistemi-kernel` |
| G9 | Ses API'si | — (kapsam dışı) | `isletim-sistemi-audio` |

## §C — Bağımlılık grafiği

### §C.1 Dış bağımlılıklar (bu düğüm → dış)

| # | Hedef | Bağımlılık türü | Yön | Durum |
|---|---|---|---|---|
| H1 | `K002-electroncis` | sınır (devre tasarımı) + taşınma hedefi | electroncis → K002 | taslak |
| H2 | `K001-donanım` | sınır (PCB · fiziksel) | electroncis → K001 | taslak |
| H3 | `K004-dsp` | sınır (dijital algoritma) | electroncis → K004 | taslak |
| H4 | `K005-firmware` | sınır (firmware · clock kaynağı) | electroncis → K005 | taslak |
| H5 | `isletim-sistemi-audio` | sınır (ses API'si — üst katman) | electroncis → audio dalı | taslak |
| H6 | `isletim-sistemi-kernel` | sınır (sürücü kodu — uygulayıcı) | electroncis → kernel dalı | taslak |
| H7 | `os-donanim-tespiti` | sınır (tespit/enumerasyon — süreç) | electroncis → tespit dalı | taslak |
| H8 | `.ai/electronic/**` | kaynak atıf (içerik kopyalanmaz) | electroncis → vault | atıf |

### §C.2 Gelen bağımlılıklar (dış → bu düğüm)

| # | Kaynak | Ne getirir | Yön | Durum |
|---|---|---|---|---|
| I1 | K000 kök (`os-master.md`) | çocuk satırı + Q1 kaydı | K000 → electroncis | doğrulandı |
| I2 | `isletim-sistemi-audio` | API'nin altındaki arayüz gereksinimi | audio → electroncis | taslak |
| I3 | `os-donanim-tespiti` | tespit edilen cihazla konuşulan protokol | tespit → electroncis | taslak |
| I4 | `K002-electroncis` | devre pin/sinyal listesi (arayüz girdisi) | K002 → electroncis | ⚠️ taşınma sürüyor |

### §C.3 Bağımlılık kuralları

| # | Kural | Uygulama | İhlal durumu |
|---|---|---|---|
| J1 | K-node ↔ vault: tek yönlü atıf | `.ai/electronic` yalnız okunur, kopyalanmaz | şema bozulması |
| J2 | `depends_on` yalnız gerçek K-node/komşu yol | uydurma ID yasak | registry bozulması |
| J3 | `evidence` disk kanıtı ister | her iddia dosya yolu/URL | kanıtsız iddia yasak |
| J4 | `validation` tarih + ölçüm ister | 2026-10-14 | tarihsiz validation yasak |
| J5 | Alt-dal → kök: `parent_id` | `/electroncis` | `parent:` anahtarı yasak |

## §D — Sınır komşuları (ayrım kuralları)

### §D.1 Komşu ↔ ayrım tablosu

| # | Komşu | Sınır konusu | Ayrım kuralı | Etiket |
|---|---|---|---|---|
| K1 | `isletim-sistemi-audio` | ses API'si (WASAPI/ALSA/CoreAudio) | API = üst katman; bu dal = API'nin altındaki register arayüzü | taslak |
| K2 | `isletim-sistemi-kernel` | sürücü modeli (kod) | bu dal = protokol sözleşmesi; kod/kernel yaşam döngüsü orada | taslak |
| K3 | `os-donanim-tespiti` | tespit/enumerasyon (ACPI/USB taraması) | tespit = süreç; bu dal = tespit edilenle konuşulan protokol | taslak |
| K4 | `K002-electroncis` | devrenin kendisi (şema/pin/bileşen) | devre tasarımı orada; bu dal = OS gözü | taşınma sürüyor |
| K5 | `K002-electroncis/guc-yonetimi` | güç devresi tasarımı (regüle/topoloji/eşik) | eşik hesabı orada; bu dal = OS'nin eşiği **okuma/kullanma** biçimi | taşınma sürüyor |
| K6 | `K001-donanım` | PCB/kart/mekanik | fiziksel tasarım orada | taslak |
| K7 | `K004-dsp` | dijital algoritma | algoritma orada; bu dal = algoritmanın çalıştığı veri hattının protokolü | taslak |
| K8 | `K005-firmware` | firmware boot/RTOS/OTA | firmware orada; bu dal = firmware'nin arkasındaki clock/register sözleşmesi | taslak |

### §D.2 Sınır ihlali belirtileri

| # | Belirti | Örnek | Sonuç |
|---|---|---|---|
| L1 | Şema/topoloji tartışması bu düğümde | buck/boost · regulator hesabı | `K002-electroncis`'e taşı |
| L2 | PCB yerleşim/iz tartışması bu düğümde | iz genişliği · katman sayısı | `K001-donanım`'a taşı |
| L3 | DSP kodu (biquad/FFT) bu düğümde | filtre implementasyonu | `K004-dsp`'e taşı |
| L4 | OTA/boot kodu bu düğümde | bootloader · flash yazma | `K005-firmware`'e taşı |
| L5 | WASAPI/ASIO buffer tartışması bu düğümde | exclusive lock · mix | `isletim-sistemi-audio`'ya taşı |
| L6 | Sürücü API (ioctl/IRP) tartışması bu düğümde | Linux/Windows driver kodu | `isletim-sistemi-kernel`'e taşı |
| L7 | Keşif/VID-PID tespit süreci bu düğümde | bus taraması · devnode | `os-donanim-tespiti`'ne taşı |

## §E — Bilinen GAP: K002-altyapi ID (korunuyor — çözülmüyor)

| # | İddia | Kanıt | Durum |
|---|---|---|---|
| M1 | `os-master.md` FM `depends_on: [K002-altyapi]` diyor | `os-master.md` satır 7 (2026-10-14 grep) | doğrulandı |
| M2 | Diskte `K002-altyapi/` dizini YOK | glob `K002-*/index.md` → yalnız `K002-electroncis` | doğrulandı |
| M3 | Diskte `K002-electroncis/` var (6 md) | glob 2026-10-14 | doğrulandı |
| M4 | Bu GAP bu yazımda çözüldü | HAYIR — bilinen GAP olarak kaydedildi | kayıt sahibi: registry §6 |

> **Not (dürüstlük):** `K002-altyapi` vs `K002-electroncis` ID uyuşmazlığı bu dosyanın
> çözüm alanının dışındadır; yalnızca korunur. Çözüm registry (§6) sahibine aittir.

## §F — K-node ↔ `.ai/electronic` ayrımı (atıf kuralı)

| # | Kriter | K-node (`electroncis/`) | Vault (`.ai/electronic/`) |
|---|---|---|---|
| N1 | Şema ailesi | v2 (10 alan + `group`) | `type` · `category` · `title` + `[[wikilink]]` |
| N2 | Amaç | mimari envanter + sınır + kanıt | derin teknik içerik |
| N3 | Kopyalama | YASAK (yalnız atıf) | YASAK (dokunulmaz) |
| N4 | Atıf kaynakları (okundu) | bu tur | `drivers/audio-drivers.md` (709 sat) · `firmware/rtos.md` (541 sat) |
| N5 | Karıştırılırsa ne olur? | şema bozulur → onarım gerekir | wikilink zinciri bozulur |

## §G — Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| O1 | K002-altyapi ID GAP bu dalı etkiliyor mu? (Q-E1) | ⚠️ VERIFICATION REQUIRED | registry §6 |
| O2 | Eski `devre-tasarimi`/`guc-ve-koruma` silinmesi ne zaman biter? (Q-E2) | süreç | başka ajan |
| O3 | `status: draft` → `verified` hangi kanıtla kapanır? (Q-E3) | UNKNOWN | Vault Steward |
| O4 | `K002-electroncis` taşınması tamamlanınca sınır komşu tablosu güncellenir mi? | UNKNOWN | Vault Steward |
| O5 | `.ai/electronic` senkronu ne sıklıkla? | UNKNOWN | vault-sync |
| O6 | Hedef platform seti (Win32/POSIX/ikisi) bu dal için K000 Q4 ile aynı mı? | ⚠️ VERIFICATION REQUIRED | Vault Steward |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
