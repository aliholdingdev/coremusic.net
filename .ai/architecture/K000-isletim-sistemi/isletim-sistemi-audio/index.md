---
node_id: K000-isletim-sistemi/isletim-sistemi-audio
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi
children: [alsa, coreaudio, aaudio, reactos-audio, tizen-audio, wasapi, webos-audio]
depends_on: []
evidence:
  - https://learn.microsoft.com/en-us/windows/win32/coreaudio/about-the-windows-core-audio-apis
  - https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams
  - https://www.alsa-project.org/wiki/Main_Page
  - https://developer.apple.com/documentation/audiotoolbox
validation: "2026-10-12 · 4 resmi URL HTTP 200 · 2026-10-13 · children 7 deep-dala genişletildi (alsa + coreaudio bu turda açıldı) · 2026-10-13 · 7/7 deep-dal diskte (reactos-audio + tizen-audio + webos-audio bu turda 4'er dosya; aaudio daha önce açılmıştı — eski '0 dosya' kaydı bayatmıştı)"
open_questions:
  - "✅ KAPANDI (2026-10-13): aaudio/tizen-audio/webos-audio/reactos-audio diskte — 4+4+4+4 dosya ölçüldü; yeni açık: 3 yeni dalın derin iddiaları gövde-GET ile teyit bekliyor → ⚠️ VERIFICATION REQUIRED (bkz. ilgili dal index §4)"
  - "CoreMusic'in gerçek ses donanımına erişimi YOK (ffmpeg ile WAV) — donanım yolu UNKNOWN"
group: mimari
---

# isletim-sistemi-audio — Kademe-2 Kayıt

**ELI15:** İşletim sisteminin **ses donanımına erişim kapısı** — ses kartını kimin, hangi API ile çağıracağını OS belirler; bu dal o API'lerin mimari tarifidir.

| Öğe | Değer |
|---|---|
| parent_id | K000-isletim-sistemi |
| Kademe | 3 · subdomain |
| Master referansı | `os-master.md` §3.8 · §4 |
| Alt kademe | `children:` 7 deep-dal — **7/7 diskte**: wasapi 13 · alsa 4 · coreaudio 4 · aaudio 4 · reactos-audio 4 · tizen-audio 4 · webos-audio 4 |
| Durum | `draft` (7 platform da diskte; 3 yeni dalın gövde-GET teyidi açık — §5 Q-NEW) |

## §1 Platform ses API'leri

| Platform | API | Deep-dal | Durum |
|---|---|---|---|
| Windows | Windows Core Audio (WASAPI dahil) | `wasapi/` | ✅ diskte 13 dosya |
| Linux | ALSA (kernel `snd-*` + `alsa-lib`) | `alsa/` | ✅ bu turda açıldı |
| macOS / iOS | CoreAudio + AudioToolbox + AVAudioSession | `coreaudio/` | ✅ bu turda açıldı |
| Android | AAudio | `aaudio/` | ✅ diskte 4 dosya |
| Tizen | Tizen ses API'si | `tizen-audio/` | ✅ diskte 4 dosya (2026-10-13) |
| webOS | webOS ses API'si | `webos-audio/` | ✅ diskte 4 dosya (2026-10-13) |
| ReactOS | Win32-uyumlu ses yolu | `reactos-audio/` | ✅ diskte 4 dosya (2026-10-13) |

Platform detayları kendi deep-dalındadır — bu dosya üst haritayı taşır, dal içeriğini **kopyalamaz**. Web kanıtı (2026-10-12 · HTTP 200): `about-the-windows-core-audio-apis` · `exclusive-mode-streams` · ALSA `Main_Page` · Apple `audiotoolbox`.

## §2 Kritik kanıt boşluğu (CoreMusic özel)

| # | İddia | Durum |
|---|---|---|
| 1 | CoreMusic, ses donanımına **doğrudan** erişir | **HAYIR** — repo'da kanıt yok |
| 2 | CoreMusic ses dosyasını **ffmpeg/ffprobe** ile işler | ✅ `shared/src/AI/AIEngine.php:288-295` |
| 3 | `ApcuAdapter`/`AIEngine` dışındaki ses donanımı kodu | **YOK** — ⚠️ VERIFICATION REQUIRED |

**Sonuç:** CoreMusic bir **ses donanımı sürücüsü değildir**, sesi dosya düzeyinde işler (HTML5 çalma frontend katmanıdır); deep-dal dokümantasyonu hedeflenen **gelecek** yüzeyi (Neva Engine) tarif eder.

## §3 Sınır komşuları

| Dışında olan | Nereye gider |
|---|---|
| DSP zinciri, EQ/reverb algoritması | K004-dsp |
| Ses motorunun C++ kodu (Neva Engine) | K003-audio · ROLE §11 `embedded` |
| Donanım kartı devresi (DAC/ADC) · sürücü kurulumu/paketleme | K001-donanım · `electroncis` (Q1) |
| Web'de çalma UI'ı | frontend katmanı |

## §4 Deep-dallar

| Deep-dal | Dosya | Durum |
|---|---|---|
| `wasapi/` | 13 dosya · 5.740 sat | ✅ mevcut — **dokunulmadı** |
| `alsa/` + `coreaudio/` | 4 + 4 dosya | ✅ bir önceki turda oluşturuldu |
| `aaudio/` | 4 dosya | ✅ mevcut (önceki turda açılmış — eski "0 dosya" kaydı bayatmıştı) |
| `reactos-audio/` · `tizen-audio/` · `webos-audio/` | 4 + 4 + 4 dosya | ✅ **bu turda oluşturuldu** (2026-10-13 · web kanıtı doğrulandı: GitHub README · docs.tizen.org imzaları · webOSTV Luna sayfaları) |

## §5 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q-NEW | 3 yeni dalda (reactos/tizen/webos) gövde-GET teyidi: docs.tizen.org tam sayfa GET'i · ReactOS wiki bot-duvarı · webOS `getVolume/setVolume` olası metotları | ⚠️ VERIFICATION REQUIRED |
| Q-A2 | CoreMusic'in donanım yolu ne zaman eklenecek (Neva Engine)? | ⚠️ VERIFICATION REQUIRED |
| Q-A3 | ASIO karşılaştırması `wasapi/` içinde mi, ayrı child mı? | ⚠️ VERIFICATION REQUIRED |
| Q-A4 | Hedef platform seti (Q4) — ses önceliği hangi OS? | UNKNOWN |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**. Kardeş kayıtlar: `isletim-sistemi-audio-{kapsam,dogrulama,ornekler}.md` · üst kayıt `../os-master.md` §3.8.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `isletim-sistemi-audio-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| kod | `isletim-sistemi-audio-ornekler.md` | Uygulama örnekleri ve kalıp gösterimi | Kademe-2 |
| kanit | `isletim-sistemi-audio-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-2 |
| mimari | `alsa/index.md` | Linux ALSA deep-dal künyesi | Kademe-3 |
| mimari | `coreaudio/index.md` | macOS/iOS CoreAudio deep-dal künyesi | Kademe-3 |
| mimari | `aaudio/index.md` | Android AAudio deep-dal künyesi | Kademe-3 |
| mimari | `reactos-audio/index.md` | ReactOS ses deep-dal künyesi | Kademe-3 |
| mimari | `tizen-audio/index.md` | Tizen ses deep-dal künyesi | Kademe-3 |
| mimari | `wasapi/index.md` | Windows deep-dal künyesi | Kademe-3 |
| mimari | `webos-audio/index.md` | webOS ses deep-dal künyesi | Kademe-3 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
