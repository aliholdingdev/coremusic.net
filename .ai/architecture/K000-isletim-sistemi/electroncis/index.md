---
node_id: K000-isletim-sistemi/electroncis
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi
children: [donanim-arayuz-protokolleri, os-guc-elektronigi]
depends_on:
  - K002-electroncis
  - K000-isletim-sistemi/isletim-sistemi-audio
  - K000-isletim-sistemi/isletim-sistemi-kernel
  - K000-isletim-sistemi/os-donanim-tespiti
evidence:
  - ".ai/architecture/K000-isletim-sistemi/os-master.md §3 · §8 (çocuk satırı + Q1)"
  - ".ai/architecture/K002-electroncis/ (6 md — glob 2026-10-14)"
  - ".ai/architecture/K000-isletim-sistemi/os-master.md satır 7 (depends_on: [K002-altyapi] — bilinen ID GAP)"
  - ".ai/electronic/drivers/audio-drivers.md (709 satır · okundu 2026-10-14 · yalnız atıf)"
  - ".ai/electronic/firmware/rtos.md (541 satır · okundu 2026-10-14 · yalnız atıf)"
  - "Vault Steward kararı 2026-10-14: Q1 kapandı — electroncis = OS ile alakalı elektronik arayüz"
validation: "2026-10-14 · Q1 kapandı (Vault Steward) · kök index yeniden yazıldı · children = 2 yeni alt-dal · status: draft"
open_questions:
  - "K002-altyapi ID GAP: os-master.md FM 'depends_on: [K002-altyapi]' diyor; diskte K002-electroncis/ var → registry §6 sahibi"
  - "Eski alt-dallar (devre-tasarimi · guc-ve-koruma) başka ajan tarafından silinecek — bu yazım onlara dokunmadı"
  - "status: draft → verified geçişi hangi kapı ile kapanır? → UNKNOWN (Vault Steward)"
group: mimari
---

# electroncis — Kademe-2 Kayıt (OS ↔ Elektronik Arayüzü)

> **Yazım dokunulmaz:** `electroncis` (v2 §3 KURAL 1 — `electronics` DEĞİL, düzeltilmez).
> **Q1 KAPANDI (Vault Steward, 2026-10-14):** kapsam tanımlıdır — eski "UNKNOWN kapsam"
> satırları bu yeniden yazımla kaldırıldı.

**ELI15:** İşletim sistemi, elektronik donanıma **kendi gözünden** bakar: ses verisini
I2S/TDM saatleriyle taşır, codec'e I2C/SPI üzerinden register yazar, USB ses cihazını
protokol üzerinden enumerer eder, uyku komutunu ACPI üzerinden güç devresine iletir,
sıcaklığı sensör register'ından okur. Bu dal OS'nin **donanıma giden gözünü**
belgeler — devrenin kendisi değil (devre tasarımı `K002-electroncis`'e aittir).

| Öğe | Değer |
|---|---|
| parent_id | K000-isletim-sistemi |
| Kademe | 2 · subdomain (alt-dallar Kademe-3 · içerik Kademe-4) |
| Master referansı | `os-master.md` §3 · §8 |
| Alt kademe | `children: 2 alt-dal` |
| Durum | `draft` (kapsam kararlı — Q1 kapandı) |

## §1 Kapsam durumu

| # | Alan | Değer | Etiket |
|---|---|---|---|
| 1 | Bu dal neyi kapsıyor? | OS ↔ donanım elektronik **arayüzü** — register/protokol seviyesi | Vault Steward kararı 2026-10-14 |
| 2 | K000 içindeki işlevi | OS'nin elektronik donanımla yüzleştiği yüzeyin envanteri | kararlı |
| 3 | Tam elektronik (devre tasarımı/güç devresi) | KAPSAM DIŞI → `K002-electroncis` (taşınma ayrı süreç) | kararlı |
| 4 | Alt-dal 1: `donanim-arayuz-protokolleri` | OS↔cihaz register/protokol arayüzü (I2S/TDM · I2C/SPI · UAC · clock) | `draft` (Kademe-3) |
| 5 | Alt-dal 2: `os-guc-elektronigi` | OS gözünden güç elektroniği (ACPI S0–S5 · wake · termal zincir) | `draft` (Kademe-3) |

## §2 Sınır komşuları

| Komşu | İlişki | Ayrım kuralı |
|---|---|---|
| `isletim-sistemi-audio` | ses API'si (WASAPI/ALSA/CoreAudio) | bu dal = API'nin **altındaki** register arayüzü |
| `isletim-sistemi-kernel` | sürücü modeli (kod) | bu dal = donanımın **protokol sözleşmesi**, sürücü kodu değil |
| `../os-donanim-tespiti` | donanım tespiti/enumerasyon (ACPI/USB taraması) | bu dal = tespit edilenle **konuşulan** protokol |
| `K002-electroncis` | devrenin kendisi (şema/pin/bileşen) | bu dal = OS gözü; devre tasarımı orada |
| `K003-audio` · `K004-dsp` · `K005-firmware` | ses/DSP/firmware K-node'ları | sınır: API/algoritma/firmware ≠ arayüz protokolü |
| `K001-donanım` | fiziksel donanım (PCB · kart · kasa) | fiziksel tasarım orada |

## §3 Disk kanıtları

| # | İddia | Dosya : konum |
|---|---|---|
| D1 | Çocuk satırı + Q1 açık sorusu (eski tur) | `os-master.md` §3 · §8 |
| D2 | `K002-electroncis/` diskte var (6 md) | `.ai/architecture/K002-electroncis/` (glob 2026-10-14) |
| D3 | FM `depends_on: [K002-altyapi]` (bilinen GAP) | `os-master.md` satır 7 |
| D4 | Vault atıf kaynakları okundu | `.ai/electronic/drivers/audio-drivers.md` · `firmware/rtos.md` (2026-10-14) |
| D5 | Repo'da ACPI/termal/I2S/USB-audio kod izi YOK | PHP/JS grep = 0 isabet (2026-10-14 · `-dogrulama.md` §6) |

## §4 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q1 | K002-altyapi ID GAP bu dalı etkiliyor mu? | ⚠️ VERIFICATION REQUIRED | registry §6 |
| Q2 | Eski `devre-tasarimi`/`guc-ve-koruma` silinmesi ne zaman tamamlanır? | süreç | başka ajan |
| Q3 | `status: draft` → `verified` geçişi hangi kapı ile kapanır? | UNKNOWN | Vault Steward |

## İlişkili dosyalar

- `electroncis-kapsam.md` — kapsam İÇİNDE/DIŞI + bağımlılık grafiği + GAP kaydı
- `electroncis-dogrulama.md` — web/disk kanıt protokolü (draft)
- `electroncis-ornekler.md` — şartname/pseudocode + gerçek repo yüzeyi (satır-doğrulamalı)
- `donanim-arayuz-protokolleri/index.md` · `os-guc-elektronigi/index.md` — Kademe-3 indeksleri
- `../os-master.md` · `../os-dogrulama.md`

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `donanim-arayuz-protokolleri/` | OS↔cihaz register/protokol arayüzü | Kademe-3 |
| mimari | `os-guc-elektronigi/` | OS gözünden güç elektroniği (ACPI/wake/termal) | Kademe-3 |

> Kök `index.md` (Kademe-2) · `electroncis-kapsam.md` (mimari) · `electroncis-ornekler.md` (kod) ·
> `electroncis-dogrulama.md` (kanit) bu tablonun üst kataloğudur; Kademe-4 içerik dosyaları (6 adet)
> kendi alt-dal `index.md` §GRUPLAR tablolarında listelenir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
