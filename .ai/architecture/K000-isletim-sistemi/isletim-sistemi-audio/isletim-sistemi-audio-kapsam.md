---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/isletim-sistemi-audio-kapsam
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio
children: []
depends_on: []
evidence:
  - "disk: isletim-sistemi-audio/ — 4 kök kayıt + 3 deep-dal (wasapi · alsa · coreaudio), 2026-10-13"
  - "shared/src/AI/AIEngine.php:288-295 (ffprobe — dosya düzeyinde ses, donanım değil)"
validation:
  - "2026-10-13 · §A-§F bölüm yapısı A görev tanımına göre yazıldı"
  - "2026-10-13 · kapsam dışı komşuları §E'de5 komşu ile sabitlendi"
open_questions:
  - "aaudio/tizen-audio/webos-audio/reactos-audio envanteri bu dosyada boş — B ajanı dolduracak"
  - "Hedef platform önceliği (Q4) hâlâ UNKNOWN"
group: mimari
---

# isletim-sistemi-audio — Kapsam

## §0 Rol (ELI15)

İşletim sisteminin **ses donanımına erişim kapısı**: ses kartı ham PCM üretir, OS bunu
**paylaştırır** (birden çok uygulama aynı anda çalamaz), **zamanlar** (buffer/latency) ve
**güvenli kılar** (cihazı tek bir süreç yutamaz). Bu dal, o API'lerin mimari tarifidir.

`os-master.md` §3.8 ELI15'i: "Ses donanımına erişim — bu, CoreMusic'in **tek gerçek
donanım-yüzeyidir**". Bu kayıt, o tanımın **kapsam sözleşmesidir**: ne bu dalın içindedir,
ne komşuya devredilmiştir, ne de henüz kanıtlanmıştır.

---

## §A — Kapsam

### §A.1 Kapsamın İÇİNDE ne var

| # | İçinde olan yüzey | Dayanak | Durum |
|---|---|---|---|
| A1 | Windows Core Audio API ailesi (WASAPI dahil) | `wasapi/` · kanıt 2026-10-12 | ✅ kanıtlı |
| A2 | Exclusive mode streams (dışlama modu — tek süreç cihazı alır) | `wasapi/` | ✅ kanıtlı |
| A3 | Linux ses katmanı ALSA (donanım ↔ kullanıcı) | `alsa/` · 2026-10-13 | ✅ bu turda açıldı |
| A4 | macOS / iOS Core Audio + AudioToolbox + AVAudioSession | `coreaudio/` · 2026-10-13 | ✅ bu turda açıldı |
| A5 | Android native C ses API'si AAudio | `aaudio/` | ⚠️ B ajanı — diskte yok |
| A6 | Paylaşma modları: shared vs exclusive — latency/tradeoff | `wasapi/` + `alsa/` + `coreaudio/` | ✅ üç dalda da var |
| A7 | OS ↔ ses donanımı entegrasyon sınırı | `os-bagimliliklar.md` §2 C7 | ✅ referans |
| A8 | Deep-dal sahipliği: `wasapi/` · `alsa/` · `coreaudio/` | disk glob, 2026-10-13 | ✅ disk |
| A9 | Ses akış zamanlaması: period, buffer, xrun, callback | `-dogrulama.md` §C | ✅ tanımlı |
| A10 | Platformlar arası karşılaştırma yüzeyi | `-ornekler.md` §D | ✅ tanımlı |

### §A.2 Kapsamın DIŞINDA ne var (bağlayıcı)

| # | Dışında olan | Nereye gider | Neden bu dalda değil |
|---|---|---|---|
| B1 | DSP zinciri (EQ, reverb, gain staging, crossover) | **K004-dsp** | algoritma katmanı, OS API'si değil |
| B2 | Ses motorunun C++ kodu (Neva Engine) | **K003-audio** · ROLE §11 `embedded` | uygulama kodu, işletim sistemi değil |
| B3 | Cihaz sürücüsü ve kart devresi (DAC/ADC/PCB) | **K001-donanım** · `electroncis` | donanım tasarımı, yazılım arayüzü değil |
| B4 | Ses codec'leri ve sıkıştırma (MP3/FLAC/Opus) | **K003-audio** | biçim/kodlama, taşıma katmanı değil |
| B5 | Web'de çalma (HTML5 `<audio>` · Web Audio API) | frontend katmanı | tarayıcı, OS ses API'sini gizler |
| B6 | ffmpeg/ffprobe dosya dönüştürme iş mantığı | `AIEngine` (K001+ uygulama katmanı) | dosya işleme ≠ canlı ses yolu |
| B7 | Core / kernel / API katmanlarının geneli | `isletim-sistemi-core` · `-kernel` · `-api` | bu dal yalnız **ses** yüzeyini alır |
| B8 | Sesle ilgili güvenlik (yetki, session) | K006-K007 güvenlik katmanı | cross-cutting, ses API'sine özgü değil |

### §A.3 Kapsam ilkeleri (bu dalda geçerli kurallar)

| # | İlke | Uygulama |
|---|---|---|
| P1 | Dal, **API sözleşmelerini** belgeler; çalışan sürücü kodu üretmez | doküman = şartname, kod ≠ bu dal |
| P2 | Her iddia ya URL, ya disk dosyası, ya `UNKNOWN` taşır | `-dogrulama.md` §E/§F kapısı |
| P3 | Platform detayı kendi deep-dalına iner, kökte kopyalanmaz | kök `index.md` §1 yalnız harita |
| P4 | Repo'da karşılığı olmayan yüzey "hedef mimari" etiketi taşır | `status: draft` asla `verified` olmaz |
| P5 | Donanım-yazılım sınırı (PCM3168A · AK4458 · ADR-038) bu dalda değil | K001-donanım'a devredilir |

---

## §B — 7 alt-dal envanteri

| # | Alt-dal (`children:`) | Platform | Dosya (disk) | Sahip | Durum 2026-10-13 |
|---|---|---|---|---|---|
| 1 | `wasapi/` | Windows | 13 dosya · 5.740 sat | Win-SW ajanı | ✅ mevcut — dokunulmadı |
| 2 | `alsa/` | Linux | 4 dosya | bu ajan | ✅ bu turda açıldı |
| 3 | `coreaudio/` | macOS / iOS | 4 dosya | bu ajan | ✅ bu turda açıldı |
| 4 | `aaudio/` | Android | 0 | B ajanı | ⚠️ diskte YOK |
| 5 | `tizen-audio/` | Tizen | 0 | B ajanı | ⚠️ diskte YOK |
| 6 | `webos-audio/` | webOS | 0 | B ajanı | ⚠️ diskte YOK |
| 7 | `reactos-audio/` | ReactOS | 0 | B ajanı | ⚠️ diskte YOK |

### §B.1 Derin dal dosya yapısı (bu turda açılan 2 dal)

| Dal | Dosya | Yüzey |
|---|---|---|
| `alsa/` | `index.md` | deep-dal künyesi, 4 çocuk |
| `alsa/` | `alsa-anayasa.md` | kimlik · kernel/`alsa-lib` · shared vs exclusive · period/buffer · xrun |
| `alsa/` | `alsa-pcm-ve-cihaz-yonetimi.md` | PCM akışı · cihaz açma · negociation · mmap · envanter · hata |
| `alsa/` | `alsa-ve-yigin-katmanlari.md` | ALSA ↔ PulseAudio ↔ PipeWire ↔ JACK · düşük gecikme |
| `coreaudio/` | `index.md` | deep-dal künyesi, 4 çocuk |
| `coreaudio/` | `coreaudio-anayasa.md` | aile · HAL property modeli · callback · ASBD · macOS vs iOS |
| `coreaudio/` | `coreaudio-callback-ve-buffer.md` | render callback · ring buffer · real-time kısıtlar · latency |
| `coreaudio/` | `coreaudio-platform-farklari.md` | macOS HAL · iOS AVAudioSession · Catalyst · matris |

### §B.2 Envantersel dürüstlük

| # | İddia | Durum |
|---|---|---|
| C1 | `children` satırı 7 dal gösterir | ✅ — ancak 4'ü diskte yok |
| C2 | 4 eksik dal bu dosyada içeriğiyle anlatıldı | **HAYIR** — yalnız envanter satırı var |
| C3 | Eksik dal içerikleri B ajanının beyazlistesindedir | ✅ görev sözleşmesi |
| C4 | `wasapi/` dosyaları bu turda değiştirildi | **HAYIR** — okundu, yazılmadı |

---

## §C — Arayüz katmanları

**Genel zincir:** `uygulama → OS ses API → OS ses motoru/çakıştırıcı → sürücü → donanım`.

| # | Katman | Sorumluluk | Bu dalda mı? |
|---|---|---|---|
| L1 | Uygulama (Neva Engine · tarayıcı · DAW) | zamanlama, DSP, format seçimi | hayır (K003) |
| L2 | OS ses API (WASAPI · ALSA-lib · CoreAudio) | cihaz seçimi, format pazarlığı, callback | **EVET** |
| L3 | OS ses motoru / mikser (shared mod) | karıştırma, session ses seviyesi, efekt | **EVET** |
| L4 | Sürücü katmanı (PortCls · `snd-*` · CoreAudio HAL plug-in) | DMA, interrupt, donanım register'ı | sınır — yalnız sözleşme |
| L5 | Donanım (DAC/ADC · ses kartı · HDMI) | analog dönüşüm | hayır (K001) |

### §C.1 L1 — Uygulama katmanı (bu dalın dışında, sınırı)

| Alan | Değer |
|---|---|
| Ne üretir | PCM blokları ya da dosya |
| Ne ister | cihaz açma, format onayı, buffer süresi, callback çağıracak zaman |
| Bu dalın rolü | yalnızca **istediği sözleşmeyi** tarif eder |
| Komşu | K003-audio (Neva Engine · PLANNED) |

### §C.2 L2 — OS ses API katmanı (dalın çekirdeği)

| Platform | API yüzeyi | Cihaz seçimi | Paylaşım | Deep-dal |
|---|---|---|---|---|
| Windows | WASAPI + MMDevice API | `IMMDeviceEnumerator` | shared / exclusive | `wasapi/` |
| Linux | `snd_pcm_open` (alsa-lib) | `hw:X,Y` · `plughw:X,Y` · `default` | `hw` · `dmix` · sunucu üzerinden | `alsa/` |
| macOS | `AudioObject` property · `AudioUnit` | `AudioDeviceID` | varsayılan paylaşımlı | `coreaudio/` |
| iOS | `AVAudioSession` + `RemoteIO` | session route | tek aktif uygulama | `coreaudio/` |

### §C.3 L3 — OS ses motoru / mikser katmanı

| Platform | Motor | Ne yapar | Paylaşım modeli |
|---|---|---|---|
| Windows | audio engine (`Audiodg.dll` — `wasapi/` kaydı) | oturum bazlı karıştırma, ses efekti | shared mod varsayılan |
| Linux | `dmix` plug-in **veya** PulseAudio/PipeWire | yazılım karıştırma | ALSA `hw` genelde tek açık |
| macOS | Core Audio HAL + system mixing | cihaz örnekleme oranı üzerinden karıştırma | varsayılan paylaşımlı |
| iOS | session tekil | kayıt/çalma arasında geçiş, interruption | uygulama başına tek session |

### §C.4 L4 — Sürücü katmanı (sınır: yalnızca davranış sözleşmesi)

| Platform | Sürücü yüzeyi | Bu dalda kaydedilen |
|---|---|---|
| Windows | PortCls · WaveRT miniport | yalnızca "callback zamanlaması sürücüden gelir" |
| Linux | kernel `snd-*` (`pcm_native.c`) | yalnızca `-EPIPE`/`-ESTRPIPE` semantiği |
| macOS | CoreAudio HAL plug-in / AudioServerPlugIn | yalnızca IOProc zamanlaması |
| Donanım register'ı, DMA descriptor, interrupt önceliği | — | **YOK** → K001-donanım / `-kernel` |

### §C.5 L5 — Donanım katmanı (kesin çıkış)

| Öğe | Nereye |
|---|---|
| DAC/ADC seçimi (PCM3168A · AK4458), clock, jitter | K001-donanım |
| Class AB güç amplifier, termal, PSU | K001-donanım · `electroncis` |
| I2S / TDM / S/PDIF fiziksel bağlantı | K005-firmware · `dsp-fw` |

### §C.6 Katmanlar arası taşınan veri tipleri

| # | Tip | L1 → L2 | L2 → L3/L4 | Bu dalda tanımı |
|---|---|---|---|---|
| T1 | PCM örneği | `float32` blok (ADR-017) | sürücünün beklediği wire formatı | `-ornekler.md` §A |
| T2 | Format tarifi | kanal · rate · bit derinliği | `mix format` (Windows) · `ASBD` (Apple) · `hw_params` (ALSA) | deep-dal dosyaları |
| T3 | Zaman bilgisi | hedef gecikme | callback zaman damgası (dakiklık) | `-dogrulama.md` §C |
| T4 | Cihaz tanımı | kullanıcı seçimi | endpoint/device id · `hw:X,Y` · `AudioDeviceID` | §B/§I |
| T5 | Durum | başlat/durdur | PREPARED · RUNNING · XRUN · SUSPENDED | `alsa-anayasa.md` §5 |
| T6 | Hata | - | `-EPIPE` · `AUDCLNT_*` HRESULT · `OSStatus` | `-dogrulama.md` §A/§D |
| T7 | Ses seviyesi | uygulama ayarı | session/master volume | `wasapi/` (Windows) |
| T8 | Olay | cihaz tak/çıkar | device/state notification | deep-dal dosyaları |

---

## §D — Bağımlılık grafiği

### §D.1 Komşu→dal bağımlılığı (kim bu dala ihtiyaç duyar)

| # | Bağımlı | Bu daldan istediği | Tipi | Zorunlu mu |
|---|---|---|---|---|
| D1 | K003-audio (Neva Engine) | cihaz açma + callback sözleşmesi | tasarım | evet (PLANNED) |
| D2 | K004-dsp | gerçek zamanlı örnek akışı | veri | hayır — ölçüm için |
| D3 | `isletim-sistemi-core` | ses alt sistemini başlatma sırası | sıralama | evet |
| D4 | `isletim-sistemi-kernel` | sürücü yükleme (`snd-*`, PortCls) | altyapı | evet |
| D5 | `isletim-sistemi-api` | cihaz seçimi/sağlık sunumu | sarmalayıcı | hayır |
| D6 | frontend | tarayıcı `<audio>` → OS API'nin arkası | sınır | hayır |

### §D.2 Dal→bağımlılık (bu dalın ihtiyacı)

| # | Bağımlılık | Ne getirir | Durum |
|---|---|---|---|
| E1 | `isletim-sistemi-kernel` | ses sürücüsü altyapısı | komşu dal |
| E2 | `isletim-sistemi-core` | başlatma ve cihaz keşfi sırası | komşu dal |
| E3 | K001-donanım | fiziksel DAC/ADC · clock | komşu K-space |
| E4 | K004-dsp | beklenen buffer/latency hedefi (512 · 48 kHz) | sartname §8 |

### §D.3 Yön şeması

```text
        [K001-donanım] ──(fiziksel PCM)──► [L4 sürücü] ──► [L5 DAC/ADC]
                                                    ▲
                                                    │ yalnız sözleşme
   [K003-audio · Neva Engine]                       │
        │ istek: cihaz + format + callback          │
        ▼                                           │
   [L2 OS ses API] ──► [L3 motor/mikser] ───────────┘
        ▲
        │
   [frontend · HTML5 audio]        [K004-dsp] ──(512/48k hedefi)──► [L1 uygulama]
```

### §D.4 Bağımlılık tipi sözlüğü

| Tip | Tanım | Örnek |
|---|---|---|
| tasarım | diğer dal bu dalın sözleşmesini kullanacak | D1 |
| veri | örnek akışı / ölçülmüş değer akar | D2 |
| sıralama | başlatma sırası bozulursa cihaz açılmaz | D3 |
| altyapı | modül/sürücü yüklenmeden API çağrısı yoktur | D4 |
| sınır | iki katmanın bitiştiği, tek yönlü veri | D6 |
| sartname | hedef değer (ölçülmemiş) | E4 |

### §D.5 Bağımlılık riskleri

| # | Risk | Tetikleyen | Etki | Azaltma |
|---|---|---|---|---|
| R1 | kernel ses modülü yok | minimal sunucu imajı | `snd_pcm_open` başarısız | `-kernel` envanteri + `alsa-anayasa.md` §6 |
| R2 | cihaz tek açık (exclusive) | ikinci süreç | açamama / glitch | paylaşım modeli kararı (§F Q-A8) |
| R3 | format pazarlığı başarız | sürücü desteklemiyor | `plughw`/mix-format fallback |
| R4 | xrun tekrarı | buffer küçük · CPU yükü | ses atlama | `-dogrulama.md` §D eşikleri |
| R5 | cihaz kaybı (tak-çıkar) | USB/HDMI değişimi | akış kapanır | bildirim + yeniden bağlanma |
| R6 | ölçüm tutarsızlığı | iki farklı araç | yanlış gecikme kararı | tek metodoloji (§E.5) |
| R7 | `children` ≠ disk | 4 eksik dal | indeks yanıltıcı | §F Q-A9 + orkestratör |

---

## §E — Sınır komşuları

### §E.1 `isletim-sistemi-api`

| Alan | Değer |
|---|---|
| İlişki | API dalı **genel** servis/kimlik katmanıdır; bu dal yalnız **ses** cihaz yolunu alır |
| Paylaşılan veri | cihaz listesi, sağlık durumu |
| Ayrım | "HTTP endpoint" vs "ses cihazı handle'ı" |
| Çakışma riski | cihaz sağlık metriği iki yerde tanımlanabilir → sahiplik: cihaz = bu dal, sunum = `-api` |
| Sahip | `-api` ajanları ×2 |

### §E.2 `isletim-sistemi-kernel`

| Alan | Değer |
|---|---|
| İlişki | kernel dalı modül/sürücü yüklemesini, bu dal **ses API sözleşmesini** alır |
| Paylaşılan veri | `snd-*` · PortCls modül adları |
| Ayrım | "modül yüklendi mi" (kernel) vs "PCM akışı nasıl açılır" (bu dal) |
| Çakışma riski | xrun/latency ölçümü kernel metrikleriyle karışabilir → bu dal yalnız kullanıcı-uzay ölçümü |
| Sahip | `-kernel` ajanı |

### §E.3 `isletim-sistemi-core`

| Alan | Değer |
|---|---|
| İlişki | core dalı sistem başlatma sırasını tutar; ses alt sistemi bir **adımdır** |
| Paylaşılan veri | başlatma sırası, bağımlılık listesi |
| Ayrım | "ne zaman başlar" (core) vs "nasıl çağrılır" (bu dal) |
| Çakışma riski | `children` listeleri çakışabilir → kök `index.md` orkestratöre ait |
| Sahip | `-core` ajanları ×2 |

### §E.4 `K003-audio`

| Alan | Değer |
|---|---|
| İlişki | Neva Engine bu dalın **ilk müşterisidir**; callback/buffer sözleşmesini kullanır |
| Paylaşılan veri | 512 örnek · 48 kHz · float32 (ADR-017 · sartname §8) |
| Ayrım | DSP/algoritma ve C++ kodu K003'te; OS çağrısı burada |
| Çakışma riski | ring buffer tasarımı iki yerde başlayabilir → sahiplik: uygulama buffer'ı K003, OS buffer'ı bu dal |
| Sahip | `embedded` ajanı (Expert #4) |

### §E.5 `K004-dsp`

| Alan | Değer |
|---|---|
| İlişki | DSP zinciri bu dalın ürettiği **zamanlı örnek akışını** tüketir |
| Paylaşılan veri | gecikme bütçesi (hedef) |
| Ayrım | filtre/reverb matematiği (K004) vs cihaz zamanlaması (bu dal) |
| Çakışma riski | latency ölçümü iki raporda farklı çıkabilir → ölçüm metodolojisi bu dalın `-dogrulama.md` §C'sinde sabit |
| Sahip | DSP-FW ajanı |

### §E.6 Genel sınır özeti

| # | Komşu | Ayrım cümlesi | Bozulursa |
|---|---|---|---|
| S1 | `-api` | cihaz sahipliği ≠ sunum | çift yazım |
| S2 | `-kernel` | kullanıcı-uzay ≠ çekirdek | yanlış xrun yorumu |
| S3 | `-core` | sıra ≠ çağrı | başlangıç yarışı |
| S4 | K003-audio | OS yolu ≠ DSP kodu | katman ihlali (§18 W6) |
| S5 | K004-dsp | zamanlama ≠ matematik | gecikme bütçesi tutarsız |

### §E.7 Handover tetikleyicileri (kim kime devreder)

| # | Durum | Kaynak | Hedef | Öncelik |
|---|---|---|---|---|
| H1 | modül/sürücü eksiği | bu dal | `-kernel` | HIGH |
| H2 | cihaz devresi/ölçüm sorunu | bu dal | K001-donanım · `audio-hw` | HIGH |
| H3 | callback buffer tasarım kararı | bu dal | K003-audio · `embedded` | HIGH |
| H4 | gecikme bütçesi ihlali | bu dal | K004-dsp · `dsp-fw` | MEDIUM |
| H5 | güvenlik/oturum yetkisi | bu dal | `security-engineer` | CRITICAL |
| H6 | eksik deep-dal üretimi | bu dal | B ajanı | MEDIUM |

---

## §F — Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q-A1 | Kalan 4 deep-dal (`aaudio` · `tizen-audio` · `webos-audio` · `reactos-audio`) ne zaman açılıyor? | ⚠️ VERIFICATION REQUIRED | B ajanı |
| Q-A2 | CoreMusic'in gerçek donanım yolu ne zaman var olacak (R1 → çalışır hâle gelecek mi)? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q-A3 | `wasapi-asio-karsilastirma.md` neden `wasapi/` altında, ayrı child mı olmalı? | ⚠️ VERIFICATION REQUIRED | Win-SW ajanı |
| Q-A4 | Hedef platform seti (Q4) — ses önceliği Windows mu, Linux (RPi) mu? | UNKNOWN | Vault Steward |
| Q-A5 | Linux sunucuda (web hosting) gerçek ses cihazı var mı? | UNKNOWN → `alsa-anayasa.md` §6 |
| Q-A6 | macOS/iOS hedefi var mı, yoksa yalnız referans mı? | UNKNOWN | Vault Steward |
| Q-A7 | Gecikme bütçesi hedefi (512/48 kHz ≈ 10,67 ms) ölçüldü mü? | ⚠️ PLANNED | `embedded` |
| Q-A8 | Paylaşım modeli tercihi: OS mikser mi, exclusive mi? | ⚠️ VERIFICATION REQUIRED | `embedded` |
| Q-A9 | `children` listesi disk gerçekliğiyle ne zaman hizalanır (4 eksik dal)? | ⚠️ VERIFICATION REQUIRED | orkestratör |
| Q-A10 | Ölçüm aracı seti (alsabat · pw-top · Instruments) hangi ortamda çalışacak? | UNKNOWN | `qa-engineer` |
| Q-A11 | Reverb/EQ gibi DSP etkileri OS motoruna mı, uygulamaya mı taşınacak? | ⚠️ VERIFICATION REQUIRED | K004-dsp |
| Q-A12 | Donanım koruma eşiği (DC offset > 0.5 V → röle) hangi katmanda denetlenir? | ⚠️ VERIFICATION REQUIRED | K001-donanım |

---

## §G — Repo ile sınır (kritik dürüstlük — §5 kaydının korunması)

| # | İddia | Kanıt | Durum |
|---|---|---|---|
| R1 | Repo'da doğrudan ses donanımı erişimi (WASAPI/ALSA/CoreAudio çağrısı) | **kod yok** | ⚠️ VERIFICATION REQUIRED |
| R2 | Repo'da ses **dosyası** işleme var (`ffprobe`) | `shared/src/AI/AIEngine.php:288-295` | ✅ okundu |
| R3 | Repo'da HTML5 ses oynatma UI'ı | frontend — K000 dışı | `[TASARIM]` |
| R4 | Neva Engine (C++ ses motoru) hedefi | ROLE §11 `embedded` — PLANNED | ⚠️ PLANNED |
| R5 | HTTP ile ses akışı (tarayıcıya byte-range) | `home.coremusic.net/include/Stream/MusicStreamHandler.php:91-106` | ✅ okundu |

> **Bu dalın mevcut CoreMusic ile ilişkisi:** `R2` + `R5` = bugün var olan, `R4` = hedeflenen,
> `R1` = **yok**. Dolayısıyla bu dal bugün bir **hedef mimari** dokümanıdır; çalışan ses
> donanımı kodu taşımaz. Bu cümle kasıtlıdır, uydurma değildir.

---

## §H — Alt ayrım noktaları ve disk kanıtları

### §H.1 Ayrım noktaları

| # | Ayrım | Ayıran şey | Durum |
|---|---|---|---|
| C1 | shared ↔ exclusive mod | birden çok süreç vs tek süreç | ✅ kanıtlı (wasapi · alsa · coreaudio) |
| C2 | OS API ↔ donanım sürücüsü | sözleşme vs gerçek cihaz | `[TASARIM]` |
| C3 | Web sesi ↔ OS sesi | tarayıcı `<audio>` vs doğrudan cihaz | `[TASARIM]` |
| C4 | Bu dal ↔ `wasapi/` deep-dal | üst harita vs Windows detayı | ✅ disk |
| C5 | Dosya düzeyinde ses (ffmpeg) ↔ canlı ses | `AIEngine` ≠ WASAPI | ✅ §G R2 |

### §H.2 Disk kanıtları

| # | İddia | Gerçek dosya yolu |
|---|---|---|
| E1 | Ses API'si tablosu | `os-master.md` §3.8 |
| E2 | C7 bağımlılığı: "Ses donanımı erişimi (WASAPI/ALSA/CoreAudio) — [BİLİNMEYEN]" | `os-bagimliliklar.md` §2 C7 |
| E3 | `ffprobe` ile ses dosyası işleme | `shared/src/AI/AIEngine.php:288-295` |
| E4 | HTTP byte-range ses akışı | `home.coremusic.net/include/Stream/MusicStreamHandler.php:91-106` |
| E5 | Deep-dal envanteri: 3 dal / 21 dosya | disk glob `isletim-sistemi-audio/**` · 2026-10-13 |
| E6 | Çocuk açık sorusu: "Linux/macOS/Android ses alt sistemleri kanıtlanmadı" | `isletim-sistemi-audio/index.md` (2026-10-12 kaydı) |

---

## §I — İlk çağrı sırası (L2 katmanı, platform bazlı)

> Bu tablo **sözleşme özeti**dir; adım numaraları sıralı zorunluluktur, argümanlar
> deep-dal dosyalarında açılır. Android/AAudio · Tizen · webOS · ReactOS bu tabloya
> **girmez** (B ajanının yüzeyi).

### §I.1 Windows — WASAPI shared render

| Adım | Çağrı | Ne çözer |
|---|---|---|
| 1 | `CoInitializeEx` | COM apartmanı hazırlanır |
| 2 | `IMMDeviceEnumerator::GetDefaultAudioEndpoint` | varsayılan çıkış cihazı seçilir |
| 3 | `IMMDevice::Activate` → `IAudioClient` | cihaz handle'ı alınır |
| 4 | `IAudioClient::GetMixFormat` | motorun karışım formatı okunur |
| 5 | `IAudioClient::Initialize` (shared) | buffer süresi + oturum açılır |
| 6 | `IAudioClient::GetService` → `IAudioRenderClient` | doldurulacak render buffer'ı |
| 7 | `IAudioClient::SetEventHandle` + `Start` | callback/olay döngüsü başlar |

### §I.2 Linux — ALSA playback

| Adım | Çağrı | Ne çözer |
|---|---|---|
| 1 | `snd_pcm_open("default" \| "hw:0,0", PLAYBACK)` | cihaz + yön seçilir |
| 2 | `snd_pcm_set_params` **veya** `hw_params` + `sw_params` | format · kanal · rate · period/buffer |
| 3 | `snd_pcm_prepare` | durum PREPARED'a geçer |
| 4 | `snd_pcm_writei` **veya** mmap `begin/commit` | örnekler iletilir |
| 5 | `-EPIPE`/`-ESTRPIPE` → `prepare`/`resume` | xrun kurtarma |
| 6 | `snd_pcm_drain` · `snd_pcm_close` | kapanış |

### §I.3 macOS — Core Audio HAL + AudioUnit

| Adım | Çağrı | Ne çözer |
|---|---|---|
| 1 | `AudioObjectGetPropertyData` (system → devices) | cihaz listesi |
| 2 | `AudioComponentFindNext` + `AudioComponentInstanceNew` | AudioUnit (output) kurulur |
| 3 | `AudioUnitSetProperty` (stream format = ASBD) | format pazarlığı |
| 4 | `AudioUnitSetProperty` (render callback) | callback bağlanır |
| 5 | `AudioUnitInitialize` + `AudioOutputUnitStart` | IO akışı başlar |
| 6 | `AudioOutputUnitStop` + instance dispose | kapanış |

### §I.4 iOS — AVAudioSession + engine

| Adım | Çağrı | Ne çözer |
|---|---|---|
| 1 | `AVAudioSession.sharedInstance()` | oturum tekil nesnesi |
| 2 | `setCategory` (playback / record) | politika (sessiz anahtar, arka plan) |
| 3 | `setActive(true)` | route cihaza bağlanır |
| 4 | `AVAudioEngine attach/connect` **veya** RemoteIO AU kurulumu | graf kurulur |
| 5 | play / record döngüsü | akış |
| 6 | interruption + routeChange bildirimleri | kopma/kurtarma |

### §I.5 Sıra karşılaştırması

| Platform | Cihaz seçimi | Format kararı | Akış başlatma | Kurtarma |
|---|---|---|---|---|
| Windows | adım 2-3 | adım 4-5 (motor mix formatı) | adım 7 | session/device events |
| Linux | adım 1 | adım 2 (uygulama ister, `plughw` çevirir) | adım 3-4 | adım 5 (xrun) |
| macOS | adım 1 | adım 3 (uygulama ASBD verir) | adım 5 | device/IOProc durumu |
| iOS | oturum route | engine/remoteIO varsayılanı | adım 4-5 | interruption bildirimi |

---

## §J — Kapsam karar ağacı (hangi soru hangi dala)

| # | Soru | Cevap veren dal |
|---|---|---|
| J1 | "Bu çağrı OS ses API'si mi?" | bu dal (`index.md` §1) |
| J2 | "WASAPI exclusive modda ne olur?" | `wasapi/` |
| J3 | "ALSA'da xrun nasıl kurtarılır?" | `alsa/alsa-anayasa.md` §5 |
| J4 | "iOS interruption'da akış ne yapar?" | `coreaudio/coreaudio-platform-farklari.md` §2 |
| J5 | "Bu cihaz sürücüsü hangi register'ı yazar?" | K001-donanım · `-kernel` (bu dal YOK) |
| J6 | "Reverb algoritması nerede?" | K004-dsp |
| J7 | "Neva Engine buffer kaç örnek?" | K003-audio · sartname §8 (512 · 48 kHz) |
| J8 | "Tarayıcı sesi nasıl çalar?" | frontend (K000 dışı) |
| J9 | "PCM3168A mı PCM5122 mi?" | K001-donanım — **PCM5122 yasak (ADR-038 H001)** |
| J10 | "Ölçüm sonucu nereye yazılır?" | `-dogrulama.md` §C/§D |

---

## §K — Kapsam dışı maddelerin geri dönüş koşulları

| # | Dışarıda olan | Geri gelir mi? | Koşul |
|---|---|---|---|
| K1 | DSP zinciri (K004) | hayır | kalıcı sınır |
| K2 | Ses motoru kodu (K003) | hayır | kalıcı sınır |
| K3 | DAC/ADC devresi (K001) | hayır | kalıcı sınır |
| K4 | Cihaz sürücüsü **kodu** | hayır | yalnız davranış sözleşmesi kalır |
| K5 | Ses codec/format (K003) | hayır | kalıcı sınır |
| K6 | Tarayıcı sesi | hayır | frontend |
| K7 | Kernel modül listesi | kısmen | `-kernel` yalnız modül adı paylaşır |
| K8 | Cihaz sağlık sunumu | kısmen | `-api` sunar, veri bu dalda |

---

## §L — Kapsam sözlüğü

| Terim | Bu bağlamdaki anlamı |
|---|---|
| ses yolu | uygulamadan donanıma kadar PCM'in geçtiği zincir |
| paylaşma modu | aynı cihazı birden çok sürecin kullanıp kullanamayacağı |
| period | kesinti/transfer birimi — callback bu ritimde gelir |
| buffer | dönemsel PCM saklama alanı — gecikmenin ana bileşeni |
| xrun | ALSA'da taşma/eksiklik durumu (`-EPIPE`) — Windows karşılığı glitch/drop |
| callback | OS'in uygulamayı örnek istemek için çağırdığı geri çağırı |
| mix format | Windows'ta motorun karışım formatı (uygulama ona uyar) |
| ASBD | Apple'ın format tarifi (`AudioStreamBasicDescription`) |
| route | iOS'ta sesin hangi giriş/çıkış üzerinden gittiği |
| locked stream | yalnız tek sürecin açık tuttuğu cihaz yolu (exclusive'e benzer) |

---

## §M — Kapsam kabul kriterleri (bu dalın "bitti" tanımı)

| # | Kriter | Ölçüm | Durum |
|---|---|---|---|
| 1 | `children` = 7 dal ve her dal için `index.md` | disk glob | ⚠️ 4 eksik |
| 2 | Her content dosyası ≥500 satır | satır sayacı | ✅ (bu tur) |
| 3 | Her iddia URL/dosya/`UNKNOWN` taşır | `-dogrulama.md` §E/§F | ✅ |
| 4 | Kapsam dışı listesi komşu ajanlarla çakışmıyor | §E imza satırları | ✅ |
| 5 | Repo'da ses donanımı kodu yokluğu açıkça yazıldı | §G R1 | ✅ |

---

## §N — Değişiklik kaydı

| Tarih | Değişiklik | Yapan |
|---|---|---|
| 2026-10-12 | İlk kayıt (100 satır · `wasapi/` tek deep-dal) | Vault Steward |
| 2026-10-13 | §A-§N yapısına genişletildi; `alsa/` + `coreaudio/` envantere eklendi; `children` 7'ye çıktı | bu ajan |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
