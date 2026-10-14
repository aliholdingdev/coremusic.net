---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/reactos-audio
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio
children:
  - K000-isletim-sistemi/isletim-sistemi-audio/reactos-audio/reactos-audio-anayasa
  - K000-isletim-sistemi/isletim-sistemi-audio/reactos-audio/reactos-audio-surucu
  - K000-isletim-sistemi/isletim-sistemi-audio/reactos-audio/reactos-audio-ornekler
depends_on: []
evidence:
  - https://github.com/reactos/reactos/tree/master/drivers/multimedia
  - https://github.com/reactos/reactos/tree/master/drivers/multimedia/audio
  - https://github.com/reactos/reactos/tree/master/dll/win32/winmm
  - https://github.com/reactos/reactos/tree/master/drivers
validation:
  - "2026-10-13 · 4 GitHub URL GET 200 · gerçek HTML içerik · drivers/multimedia/README gövdesi alındı (KS.SYS/SYSAUDIO.SYS/PORTCLS.SYS/WDMAUD tanıkları) · isletim-sistemi-audio/ içi URL kesişimi 0"
open_questions:
  - "ReactOS ses yığını bileşenlerinin çoğu README'de 'stub for future implementation' — üretim olgunluğu UNKNOWN"
  - "CoreMusic/ReactOS ilişkisi yok (repo'da ReactOS hedefi 0 isabet) → UNKNOWN"
  - "Wiki bot-duvarı (Anubis) sürdüğünden wiki içeriği hâlâ okunamadı ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# reactos-audio — Deep-Dal İndeksi (Kademe-4)

**ELI15:** ReactOS, Windows'un ses yığınını **açık kaynaktan yeniden kurmaya çalışan** bir
işletim sistemi — uygulama `WINMM.DLL`'i çağırır, o da çekirdeğe (`WDMAUD` → `KS.SYS`)
ulaşır; depodaki README bu zinciri ad ad anlatır ama bileşenlerin çoğu henüz **taslak
(stub)**tır; yani "mimari çizilmiş, şehir henüz tam bitmemiş" durumdadır.

| Öğe | Değer |
|---|---|
| parent_id | `K000-isletim-sistemi/isletim-sistemi-audio` |
| Kademe | 4 · deep-dal |
| Durum | `draft` |
| Platform | ReactOS (Win32-uyumlu açık kaynak OS) |
| Repo durumu | CoreMusic'te ReactOS hedefi YOK → UNKNOWN (§4 Q3) |

## §1 Kanıtlanmış çerçeve

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| `drivers/multimedia/` dizini ses yığını bileşenlerini (KS.SYS · SYSAUDIO.SYS · PORTCLS.SYS · WDMAUD.DLL/WDMAUD.SYS) README metniyle tanımlar ve "much of this code exists only as stubs" der | https://github.com/reactos/reactos/tree/master/drivers/multimedia | 2026-10-13 | ✅ GET 200 · README gövdesi alındı · Andrew Greenwood · 3 Aralık 2006 |
| Aynı README opsiyonel sürücüleri `sndblst` (Sound Blaster) ve `mpu401` (MPU-401 MIDI) olarak sayar | https://github.com/reactos/reactos/tree/master/drivers/multimedia | 2026-10-13 | ✅ README "Optional drivers" bölümü |
| `drivers/multimedia/audio/` altında `mpu401_nt4` · `sb16_nt4.old` · `sndblst.old` · `sndblst_sys` · `sound` alt dizinleri diskte mevcuttur | https://github.com/reactos/reactos/tree/master/drivers/multimedia/audio | 2026-10-13 | ✅ GET 200 · 5 dizin sayıldı |
| `dll/win32/winmm/` altında WINMM uygulaması (`winmm.c` · `driver.c` · `mci.c` · `mmio.c` · `playsound.c` · `time.c` · `joystick.c` · `lolvldrv.c` · `registry.c` · `midimap/`) bulunur | https://github.com/reactos/reactos/tree/master/dll/win32/winmm | 2026-10-13 | ✅ GET 200 · 13 dosya + 2 dizin sayıldı |
| `drivers/` üst dizininde ses'e adanmış ayrı bir dizin yoktur; ses `multimedia/` altındadır (22 alt dizin listelendi) | https://github.com/reactos/reactos/tree/master/drivers | 2026-10-13 | ✅ GET 200 · dizin listesi alındı |

## §2 Bu deep-dalın dosya envanteri (disk, 2026-10-13)

| # | Dosya | İçerik yüzeyi | Bu turda |
|---|---|---|---|
| 1 | `index.md` (bu dosya) | deep-dal indeksi | **oluşturuldu** |
| 2 | `reactos-audio-anayasa.md` | Kimlik · Win32-uyumlu ses yığını katmanları · stub gerçeği | **oluşturuldu** |
| 3 | `reactos-audio-surucu.md` | `drivers/multimedia` yapısı · README bileşen tanıkları · opsiyonel sürücüler | **oluşturuldu** |
| 4 | `reactos-audio-ornekler.md` | `winmm/` dosya envanteri · çağrı akışı · negatif kanıt sınırı | **oluşturuldu** |

> Kural: bu 4 dosyanın yüzeyi bu deep-dalın **kendi beyazlistesidir**; kök
> `isletim-sistemi-audio/*.md`, `wasapi/`, `alsa/`, `coreaudio/`, `aaudio/`,
> `tizen-audio/`, `webos-audio/` dosyalarına bu turda **dokunulmadı**.

## §3 Bu deep-dalın üst sınırları

| Dışında olan | Nereye gider |
|---|---|
| Windows'un kendi ses yığını (WASAPI · shared/exclusive) | `../wasapi/` |
| ReactOS çekirdek mimarisi geneli (NT uyumluluğu) | `../../isletim-sistemi-turu/reactos/reactos-cekirdek-mimarisi.md` |
| ReactOS Win32 uyumluluk katmanı | `../../isletim-sistemi-turu/reactos/reactos-win32-uyumlulugu.md` |
| ReactOS sürüm/wiki/SSS kanıtları (bot-duvarı kaydı) | `../../isletim-sistemi-turu/reactos/reactos-ses-stacki.md` |
| DSP zinciri, EQ/reverb algoritması | K004-dsp |
| Ses motorunun C++ kodu (Neva Engine) | K003-audio |
| Ses kartı devresi (DAC/ADC) | K001-donanım |

## §4 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| R1 | `drivers/multimedia` README'sindeki bileşenlerin (KS/SYSAUDIO/PORTCLS/WDMAUD) hangileri üretim seviyesinde çalışıyor? | ⚠️ VERIFICATION REQUIRED (README: "stubs") |
| R2 | `sndblst_sys`/`mpu401_nt4` sürücüleri gerçek donanımda test edildi mi? | UNKNOWN |
| R3 | CoreMusic/Neva Engine yol haritasında ReactOS hedefi var mı? (repo 0 isabet) | UNKNOWN |
| R4 | Wiki'de ses sayfası bot-duvarı arkasından okunabilir hale geldi mi? | ⚠️ VERIFICATION REQUIRED |
| R5 | 48 kHz · 512 örnek ölçüm hedefi ReactOS'ta ölçülebilir mi? (bkz. üst dal §9) | ⚠️ VERIFICATION REQUIRED |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-4 |
| mimari | `reactos-audio-anayasa.md` | Kimlik, katman yığını ve stub gerçeği | Kademe-4 |
| mimari | `reactos-audio-surucu.md` | Sürücü ağacı, README tanıkları, opsiyonel sürücüler | Kademe-4 |
| kod | `reactos-audio-ornekler.md` | WINMM dosya envanteri ve çağrı akışı | Kademe-4 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
