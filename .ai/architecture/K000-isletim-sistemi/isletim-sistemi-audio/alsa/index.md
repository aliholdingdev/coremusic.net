---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/alsa
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio
children:
  - K000-isletim-sistemi/isletim-sistemi-audio/alsa/alsa-anayasa
  - K000-isletim-sistemi/isletim-sistemi-audio/alsa/alsa-surucu
  - K000-isletim-sistemi/isletim-sistemi-audio/alsa/alsa-ornekler
depends_on: []
evidence:
  - https://www.alsa-project.org/wiki/Asoundrc
  - https://www.alsa-project.org/alsa-doc/alsa-lib/group___p_c_m.html
  - https://www.kernel.org/doc/html/latest/sound/kernel-api/writing-an-alsa-driver.html
validation:
  - "2026-10-13 · 30 fresh URL HTTP 200 (HEAD/GET) · envanter kesişimi 0 · repo taraması ALSA deseni 0 isabet"
open_questions:
  - "CoreMusic reposunda ALSA/`snd_*` kodu YOK → UNKNOWN (bkz. `alsa-ornekler.md` §1)"
  - "PipeWire GitLab GET bot-challenge döndü → README/`pipewire.conf` içeriği doğrulanamadı ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# alsa — Deep-Dal İndeksi (Kademe-4)

**ELI15:** ALSA, Linux'un **ses donanımıyla konuşma dilidir** — çekirdek tarafında
`snd-*` sürücüleri ses kartını yönetir, kullanıcı tarafında `alsa-lib` uygulamaya
PCM akışı (çalma/kayıt) ve mikser kontrolü açar; dağıtımlar gelen arayüz olarak
`aplay`/`amixer`/`alsactl` araçlarını taşır.

| Öğe | Değer |
|---|---|
| parent_id | `K000-isletim-sistemi/isletim-sistemi-audio` |
| Kademe | 4 · deep-dal |
| Durum | `draft` |
| Platform | Linux (çekirdek ALSA + alsa-lib + alsa-utils) |
| Repo durumu | CoreMusic'te ALSA kodu YOK → UNKNOWN (§4 Q1) |

## §1 Kanıtlanmış çerçeve

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| `.asoundrc` sayfası kullanıcı yapılandırmasını (varsayılan PCM/mikser blokları) belgeler | https://www.alsa-project.org/wiki/Asoundrc | 2026-10-13 | ✅ GET 200 · 44.988 bayt · 39 `<pre>` bloğu |
| alsa-lib PCM referans sayfası PCM arayüzünü (açma · hw/sw parametre · çalıştırma · kurtarma) tek grup halinde toplar | https://www.alsa-project.org/alsa-doc/alsa-lib/group___p_c_m.html | 2026-10-13 | ✅ GET 200 · 274.184 bayt · 127 üye |
| `writing-an-alsa-driver` sayfası `snd_pcm_ops` operator tablosunu örnek kodla tanımlar | https://www.kernel.org/doc/html/latest/sound/kernel-api/writing-an-alsa-driver.html | 2026-10-13 | ✅ GET 200 · `struct snd_pcm_ops` alıntısı doğrulandı |
| Linux çekirdek ses alt sistemi dokümantasyonu ana sayfası ALSA sürücü/design başlıklarını indeksler | https://www.kernel.org/doc/html/latest/sound/index.html | 2026-10-13 | ✅ GET 200 · `<title>Sound Subsystem Documentation` |
| Debian bookworm `alsa-utils` man sayfaları (`aplay` · `amixer` · `alsactl`) komut sözleşmelerini taşıyor | https://manpages.debian.org/bookworm/alsa-utils/aplay.1.en.html | 2026-10-13 | ✅ GET 200 · SYNOPSIS alındı |

## §2 Bu deep-dalın dosya envanteri (disk, 2026-10-13)

| # | Dosya | İçerik yüzeyi | Bu turda |
|---|---|---|---|
| 1 | `index.md` (bu dosya) | deep-dal indeksi | **oluşturuldu** |
| 2 | `alsa-anayasa.md` | Kimlik · katman mimarisi · PCM modeli · paylaşım modları | **oluşturuldu** |
| 3 | `alsa-surucu.md` | `snd_pcm_ops` · kesme yolu · tasarım belgeleri (designs/*) | **oluşturuldu** |
| 4 | `alsa-ornekler.md` | `.asoundrc` blokları · CLI · alsa-lib çağrı dizisi · repo kanıtı | **oluşturuldu** |

> Kural: bu 4 dosyanın yüzeyi bu deep-dalın **kendi beyazlistesidir**; kök
> `isletim-sistemi-audio/*.md`, `wasapi/`, `coreaudio/`, `aaudio/` dosyalarına
> bu turda **dokunulmadı** (başka ajanların yüzeyi).

## §3 Bu deep-dalın üst sınırları

| Dışında olan | Nereye gider |
|---|---|
| PulseAudio / PipeWire oturum yöneticisi politikası | bu dal §3 · yalnız üst-bağlam, PipeWire içeriği ⚠️ VERIFICATION REQUIRED |
| Windows ses yolu (WASAPI · shared/exclusive) | `../wasapi/` |
| macOS/iOS ses yolu (CoreAudio · AVAudioSession) | `../coreaudio/` |
| DSP zinciri, EQ/reverb algoritması | K004-dsp |
| Ses motorunun C++ kodu (Neva Engine) | K003-audio |
| Ses kartı devresi (DAC/ADC) · amplifikatör | K001-donanım · `electroncis` (Q1) |
| Web'de çalma UI'ı | frontend katmanı |

## §4 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| A1 | CoreMusic reposunda ALSA kodu var mı (desen taraması 0 isabet)? | UNKNOWN |
| A2 | Hedef Linux dağıtımı ve `asound.conf` dağıtım politikası ne olacak? | ⚠️ VERIFICATION REQUIRED |
| A3 | Neva Engine'in ALSA üzerinden 512 örnek · 48 kHz hedefi (sartname) hangi cihazda ölçülecek? | ⚠️ VERIFICATION REQUIRED |
| A4 | PipeWire altında `alsa-lib` doğrudan kullanımı mı, yoksa oturum yöneticisi API'si mi? | ⚠️ VERIFICATION REQUIRED |
| A5 | `dmix`/`dsnoop` yazılım mikseri mi, yoksa exclusive `hw:` erişimi mi (ASIO Exclusive Lock karşılığı)? | ⚠️ VERIFICATION REQUIRED |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-4 |
| mimari | `alsa-anayasa.md` | Kimlik, katman mimarisi ve PCM modeli | Kademe-4 |
| mimari | `alsa-surucu.md` | Sürücü arayüzü, kesme yolu ve tasarım belgeleri | Kademe-4 |
| kod | `alsa-ornekler.md` | Yapılandırma, CLI ve çağrı dizisi örnekleri | Kademe-4 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
