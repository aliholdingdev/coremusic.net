---
node_id: K000-isletim-sistemi/electroncis/donanim-arayuz-protokolleri
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/electroncis
children: [donanim-arayuz-protokolleri/-anayasa.md, donanim-arayuz-protokolleri/-ornekler.md, donanim-arayuz-protokolleri/-dogrulama.md]
depends_on: [K002-electroncis, K001-donanım, K004-dsp, K000-isletim-sistemi/isletim-sistemi-audio, K000-isletim-sistemi/os-donanim-tespiti]
evidence:
  - "https://docs.kernel.org/driver-api/i2c.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/driver-api/usb/index.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/driver-api/gpio/index.html (200 OK · 2026-10-14)"
  - "https://www.usb.org/ (200 OK · 2026-10-14)"
  - "C:\www\coremusic.net\.ai\electronic\drivers\audio-drivers.md (709 satır · salt-okunur atıf)"
validation: "2026-10-14 · yazıldı (Vault Steward Q1 kararı) · kök: electroncis/index.md children[0] · red-team turu bekliyor"
open_questions:
  - "⚠️ VERIFICATION REQUIRED: cihaz bazlı register/ID listesi (PCI/ACPI/I2C slave) bu dalda yok — os-donanim-tespiti / batı@CoreMusic envanterine devredildi."
  - "clocksource/clockevents 404 kaydı korunuyor: timer alt-sistemi bu dalın parçası DEĞİLDİR (yalnız ses/clock ağacı işlenir)."
group: mimari
---

# donanim-arayuz-protokolleri — alt düğüm index

> **`electroncis` yazım hatası dokunulmaz** (v2 §3 KURAL 1) — üst düğüm adı bu imla ile
> yazılır; burada da aynı imla korunur.

## §0 ELI15

`donanim-arayuz-protokolleri`, OS'in donanımı **register/protokol seviyesinde**
 yüzleştiği arayüzlerin vault kaydıdır: I2S/TDM ses hattı, I2C/SPI register erişimi,
 USB (UAC dahil) enumerasyon, GPIO haberleşmesi/hotplug ve clock ağacı. Testi OS
 tarafından basitçe yapılır: **"OS kodu bu cihazla konuşuyor mu?"** Konuşma şekli
 sürücü/protokol-standardı ise burada; tam elektronik devre, eşik, PCB ise
 `K002-electroncis` içindedir.

## §1 Kapsam — İÇİNDE / DIŞI

| İÇİNDE (OS gözü) | DIŞI (K002 / K001 / K004) |
|---|---|
| I2S/TDM/PCM veri hattı: OS ses yığınının gördüğü slot/kanal/(master–slave) | Devre tasarımı, empedans, seviye çevirici |
| MCLK/BCLK/FS oranı: OS clock ağacı yapılandırması | Osilatör/sağdırıcı tolerans hesabı |
| I2C/SPI: OS driver'ın register okuma/yazma sırası | Slave register haritası tasarımı, bus topolojisi |
| USB UAC: enumerasyon, descriptor, endpoint eşleştirme | USB fiziksel katman, ESD/EMI |
| GPIO: hotplug/haberleşme pin-i yönetimi (OS konfigürasyonu) | Pin çevresi devresi, pull-up hesabı |
| Sürücü↔donanım keşfi (probe), tanılama | firmware yükleme / RTOS içi (`.ai/electronic/firmware/`) |

## §2 Dosyalar

| Dosya | Ne verir | Zemin |
|---|---|---|
| `-anayasa.md` | protokol kuralları, register erişim disiplini, clock/ses hattı, USB/GPIO OS davranışları | mimari · ≥350 |
| `-ornekler.md` | şartname/pseudocode örnekler (repo'da C/C++ = 0) | kod · ≥250 |
| `-dogrulama.md` | web matrisi, grep-0, şema regex denetimi | kanit · ≥250 |

## §3 Sınır komşuları

| Komşu | Ne alır / ne verir |
|---|---|
| `electroncis/os-guc-elektronigi` | güç durumu ↔ arayüz yaşam döngüsü (suspend'te bus kapanır) |
| `isletim-sistemi-audio` | ses hattı üst katmanı (PCM/akış) — bu dal alt katman (protokol) |
| `os-donanim-tespiti` | hangi cihaz var (ID/envanter) — bu dal nasıl konuşulur |
| `K002-electroncis` | tam elektronik devre/register haritası — sınır, yazma yok |
| `K004-dsp` | sinyal zinciri (ses formatı dönüşümü) — OS değil |

## §4 Açık sorular

1. Cihaz register envanteri (`⚠️ VERIFICATION REQUIRED`) hangi node'a yazılacak?
2. timer/clocksource konusu 404 kaydıyla bu dalın dışında — gereksizse vault
   §6 sahibi resen düşsün.
3. `K002-altyapi` ID GAP'i (üst düğüm `os-master.md` satır 7) bu dalın
   `depends_on`'unu etkiler mi? (şu an: HAYIR — uydurma ID yazılmadı)

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
