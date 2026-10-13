---
title: "K003-audio — Master"
type: architecture-master
node_id: K003-audio
node_type: domain
status: draft
date: 2026-10-12
updated: 2026-10-13
source: "v2 §2.3 (coremusic-katmanli-mimari-agaci-v2.md) · registry index.md §5"
parent_id: "K000–K5000 Architecture Registry"
children: [engine, device, driver, backend, input, output, routing, mixing, realtime, synchronization, diagnostics, testing]
depends_on: [K000-isletim-sistemi, K001-donanım, K005-firmware]
evidence: ["v2 §2.3 mini-ağaç — 12 çocuk adı (2026-10-12 okundu, birebir)", "registry index.md §5 satır 3 — K003-audio · audio-master.md · draft", "shared/src/AI/AIEngine.php:300-339 — ffmpeg metadata + ISO 31-band EQ", "2026-10-13: 5 resmi URL HEAD200+fetch → §10–§11 ekleri"]
validation: "draft — D-kriterleri audio-dogrulama.md §4; çocuk kanıtları <çocuk>-dogrulama.md §1"
open_questions: ["Q5 engine ↔ backend çocuk sınırı (pull model'de veri sahipliği) ⚠️", "Q6 input/output ↔ device/driver sorumluluk ayrımı ⚠️", "ASIO GPLv3 lisans etkisi · ASIO SDK 2.3.4 sürüm teyidi ⚠️ · K002 ID GAP"]
group: mimari
---

# audio-master — K003 Ses Motoru Anayasası

## §1 Rol (ELI15)

**Sesin üretilip hoparlöre/kulaklığı gittiği motor.**

K003, ses verisinin kaynaktan çıkış noktasına kadarki **aktarma ve zamanlama**
sorumluluğunu taşır. Bir medya oynatıcı düşünelim: dosya okunur, belleğe alınır,
kanallara ayrılır, doğru cihaza gönderilir ve tüm bunlar **zamanında** yapılır.
Bu adımların hangisinin nerede durduğu bu alanın 12 çocuğunda tanımlıdır.

Basit benzetme: K003 bir **su boru hattı**dır. Suyu üreten K001 (kaynak/fiziksel
parça), suyun tadını ayarlayan K004 (DSP), hattın döşenmesine izin veren zemin
K000 (OS), çeşmenin içindeki küçük mekanizma K005'tir (firmware). Boru hattı
kendi başına iyi çalışmalı: **sızdırmamalı** (veri kaybı / xrun), **basınç
dalgalanmamalı** (zamanlama jitter'ı), **yanlış çeşmeye çıkmamalı** (yanlış routing).

## §2 Kapsam / Sınır

### §2.1 Ne K003'ün işidir

| Alan | Kapsam içi |
|---|---|
| akış | Ses verisinin sırayla işlenmesi ve taşınması |
| cihaz | Hangi çıkış/giriş cihazının seçileceği ve değiştirilebileceği |
| sürücü | Sürücü katmanıyla tek noktadan konuşma |
| backend | Buffer, kuyruk ve çalışma zamanı altyapısı |
| yön | Kanal yönlendirme (routing) ve karıştırma (mixing) |
| zaman | Realtime çalışma kuralları ve clock senkronizasyonu |
| sağlık | diagnostics + testing ile ölçüm ve doğrulama |

### §2.2 Ne DEĞİL (sınır)

| Konu | Sahibi | Gerekçe |
|---|---|---|
| EQ / bass / tiz / reverb / crossover DSP zinciri | K004-dsp | Sinyal şekillendirme ayrı alan |
| DAC/ADC, amplifikatör, PCB tasarımı | K001-donanım | Fiziksel tasarım ayrı alan |
| Boot, register yazımı, cihaz içi güncelleme | K005-firmware | Donanım içi yazılım ayrı alan |
| Ses API'si, sürücü yükleme, OS kaynak yönetimi | K000-isletim-sistemi | Zemin katmanı |
| Uygulama arayüzü (oynat/duraklat butonu) | uygulama katmanı | K003 motoru, UI'ı bilmez |

**Sınır kuralı:** K003 içinden K004'e sinyal **gönderilir**, K004'ün içine
**sokulmaz**; DSP parametresi veri olarak taşınır, DSP hesabı K003'ün realtime
döngüsüne **kopyalanmaz** (aksi halde aynı matematik iki yerde yaşar →
`audio-bagimliliklar.md` §4/7 riski).

## §3 Kademe-2 çocuk tablosu (v2 §2.3 — birebir, 12 çocuk)

| # | Çocuk | Kapsam (1 satır) | Üst düğüm | Durum |
|---|---|---|---|---|
| 1 | engine | Zinciri çalıştıran ana döngü ve yaşam döngüsü | K003-audio | draft |
| 2 | device | Cihaz envanteri, seçimi ve değişimi | K003-audio | draft |
| 3 | driver | Sürücü yüzeyiyle tek noktadan etkileşim | K003-audio | draft |
| 4 | backend | Buffer/kuyruk/çalışma zamanı altyapısı | K003-audio | draft |
| 5 | input | Kaynak girişi (dosya, mikrofon, akış) | K003-audio | draft |
| 6 | output | Çıkış tarafı (hoparlör, kulaklık, akış) | K003-audio | draft |
| 7 | routing | Kanal/akış yönlendirme kuralları | K003-audio | draft |
| 8 | mixing | Çoklu kaynağı tek akışta birleştirme | K003-audio | draft |
| 9 | realtime | Zaman baskısı ve lock-free çalışma kuralları | K003-audio | draft |
| 10 | synchronization | Clock/epoch uyumu, drift telafisi | K003-audio | draft |
| 11 | diagnostics | Log, ölçüm, hata sınıflandırma | K003-audio | draft |
| 12 | testing | Test senaryoları ve kabul kriterleri | K003-audio | draft |

> Çocuk tanımları yalnız addan türetilmiştir (v2 §3 KURAL 3) → her satır
> ⚠️ VERIFICATION REQUIRED taşır; içerik iddiası sayılmaz. Derin kapsam her
> çocuğun `<çocuk>-kapsam.md` dosyasındadır.

## §4 Referans mimari (resmi platform davranışı — doğrulanmış)

Bu bölüm, CoreMusic K003 tasarımının **dayanacağı** resmi platform gerçeklerini
taşıır. Bunlar CoreMusic kodu değildir; CoreMusic'in kendi engine'i bunları
referans alarak kendi durum kümesini ve döngüsünü tanımlayacaktır.

| # | Platform gerçeği | Kaynak (URL) | K003'e etkisi |
|---|---|---|---|
| A1 | Core Audio'da ses akışı **pull model** ile yürür: host, grafın son düğümünün render metodunu çağırarak veriyi "çekecektir"; her düğüm ihtiyacı olduğunda kendinden öncekine render ile sorar | https://developer.apple.com/library/archive/documentation/MusicAudio/Conceptual/AudioUnitDevelopmentFundamentals/AudioUnitDevelopmentFundamentals.html | engine, zinciri **pull** olarak kurgulamalı (push değil) |
| A2 | Bir ses birimi (audio unit) render çağrısında iki şeyden birini yapar: uygulamadan veriyi render callback ile alır ya da bağlandığı diğer birimden kendi render metodunu çağırarak alır | https://developer.apple.com/library/archive/technotes/tn2091/_index.html | engine ↔ backend sınırı (Q5): veri sahipliği netleşmeli |
| A3 | Audio Unit Processing Graph (AUGraph) ses işleme zincirini oluşturur; AUGraphNewNode ile düğüm eklenir, bir graf output unit ile biter, AUGraphInitialize bellek ayırır, AUGraphStart işlemi başlatır | https://developer.apple.com/library/archive/documentation/MusicAudio/Conceptual/CoreAudioOverview/ARoadmaptoCommonTasks/ARoadmaptoCommonTasks.html | engine'in yaşam döngüsü: create → addNode → initialize (bellek) → start |
| A4 | `AudioUnitRender` her render zaman damgasında `inTimeStamp.sampleTime` monoton artar: sonraki = önceki + inNumberFrames | https://developer.apple.com/documentation/audiotoolbox/audiounitrender(_:_:_:_:_:_:) | realtime/synchronization: sample-time monotonluğu kural olmalı |
| A5 | Bir efekt birimi DSP çekirdeğini `Process` metodunda tanımlar; `Reset` birimi temizler (ör. reverbdecay'i yeni oynatma noktasında sıfırlar) | https://developer.apple.com/library/archive/documentation/MusicAudio/Conceptual/AudioUnitProgrammingGuide/TheAudioUnit/TheAudioUnit.html | K004 düğümlerinin K003'e eklenme sözleşmesi: Process + Reset |
| A6 | `auval` CLI, bir audio unit'ı plug-in API + temel işlev (kanal konfigürasyonu, instantiate süresi, render yeteneği) üzerinden doğrular | https://developer.apple.com/library/archive/documentation/MusicAudio/Conceptual/AudioUnitDevelopmentFundamentals/AudioUnitDevelopmentFundamentals.html | testing çocuğunun doğrulama aracı referansı |

## §5 Teknoloji kanıtları (yalnızca doğrulanmış web bulguları)

| # | Bulgu | Kanıt (URL) | Erişim | Durum |
|---|---|---|---|---|
| T1 | Steinberg ASIO SDK açık kaynak ilan edilmiş (GPLv3) | https://www.steinberg.net/developers/asiosdk-open | 2026-10-12 | ✅ doğrulandı |
| T2 | JUCE deposu erişilebilir — cross-platform C++ framework (VST/VST3/AU/AUv3/LV2/AAX) | https://github.com/juce-framework/JUCE | 2026-10-12 | ✅ doğrulandı |
| T3 | JUCE 9, 2026-07-21 "JUCE 9 is Available Now" duyurusu | https://forum.juce.com/t/juce-9-is-available-now/69175 | 2026-10-12 | ✅ doğrulandı |
| T4 | NAudio deposu + ASIO tartışma kategorisi mevcut | https://github.com/naudio/NAudio/discussions/categories/general | 2026-10-12 | ✅ kategori doğrulandı |
| T5 | NAudio + ASIO (2026-09-28) kesin thread başlığı | aday: discussions/1091 · /1250 · issues/1249 · Docs/AsioPlayback.md | 2026-10-12 | ⚠️ VERIFY REQUIRED |
| T6 | WASAPI ana sayfası (Windows Core Audio) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2026-10-12 | ✅ doğrulandı |
| T7 | WASAPI exclusive mode streams belgesi | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2026-10-12 | ✅ doğrulandı |
| T8 | ASIO cihaz kaybında WASAPI fallback kuralı | .ai/AGENTS.md §17 #6 (dosya okundu) | 2026-10-12 | ✅ iç kanıt |
| T9 | Realtime: lock-free callback, buffer'lar baştan allocate | ~/.config/opencode/AGENTS.md §5 (dosya okundu) | 2026-10-12 | ✅ iç kanıt |
| T10 | JUCE 9 / ASIO SDK 2.3.4 hedef sürümleri (kurulu değil — hedef) | .ai/AGENTS.md §4 (dosya okundu) | 2026-10-12 | ✅ iç kanıt [TASARIM] |
| T11 | ASIO SDK sürüm numarası (2.3.4) bağımsız web teyidi | — | 2026-10-12 | ⚠️ VERIFY REQUIRED |
| T12 | NAudio sürüm/paket bilgisi | — | 2026-10-12 | **UNKNOWN** |
| T13 | ALSA PCM: hw_params (format/rate/channels/buffer) + sw_params (avail_min/period_event) ayrımı; beş access modu (MMAP_INTERLEAVED · MMAP_NONINTERLEAVED · MMAP_COMPLEX · RW_INTERLEAVED · RW_NONINTERLEAVED) | https://alsa-project.org/alsa-doc/alsa-lib/pcm.html | 2026-10-12 | ✅ doğrulandı |
| T14 | ALSA plug plugin: format/channels/rate otomatik dönüşümü (slave belirtilir) | https://alsa-project.org/alsa-doc/alsa-lib/pcm_plugins.html | 2026-10-12 | ✅ doğrulandı |
| T15 | PipeWire graph scheduling: driver node döngüyü başlatır; required/pending sayaçları ile node tetiklenir; async node 1 quantum latency ekler | https://docs.pipewire.org/page_scheduling.html | 2026-10-12 | ✅ doğrulandı |
| T16 | PipeWire latency: port bazlı Latency nesnesi (direction + min/max); linked node latency'leri toplanır, mismatch tespit edilir | https://docs.pipewire.org/page_latency.html | 2026-10-12 | ✅ doğrulandı |
| T17 | PipeWire media graph = nodes + ports + links; node = medya işleme elemanı, link iki portu bağlar | https://docs.pipewire.org/devel/page_objects_design.html | 2026-10-12 | ✅ doğrulandı |

**T5/T11/T12 yasağı:** doğrulanmamış satırlara sürüm/thread/paket adı
**uydurulmaz**; kanıt gelene kadar ⚠️ veya UNKNOWN olarak kalır. Sayısal
iddialar (yıldız, sürüm) erişim tarihiyle yazılır.

### §5.1 Kanıt durumuna göre teknoloji kararı matrisi

| Teknoloji | Kanıt | K003 kararı | Not |
|---|---|---|---|
| ASIO SDK (Steinberg) | ✅ açık kaynak (T1) | Windows düşük-gecikme adayı | GPLv3 lisans etkisi → Q1 ⚠️ |
| WASAPI (Microsoft) | ✅ docs (T6, T7) | Windows fallback/alternatif | exclusive ↔ shared kip tercihi |
| JUCE 9 | ✅ repo + duyuru (T2, T3) | Aday cross-platform framework | APv2 API kararsızlığı → arayüz uyarısı |
| NAudio | ✅ repo (T4) | .NET sarmalayıcı adayı | sürüm/paket → T12 UNKNOWN |
| ALSA (Linux) | ✅ docs (T13, T14) | Linux backend referansı | hw/sw params ayrımı şablon |
| PipeWire (Linux) | ✅ docs (T15–T17) | Linux graph/latency referansı | driver/quantum modeli şablon |
| JACK | ✅ (graf/realtime) | Linux pro-audio referansı | process callback modeli |

**Kural:** ✅ olmayan satır teknoloji seçimi olarak kullanılamaz; yalnız ad
olarak anılır. Karar verilen her satır kanıtıyla §5'e işlenir ve dogrulama §1'e
düşer — sessiz güncelleme yasak.

## §6 Bağımlılık özeti

| Yön | Bağımlılık | Tür | Etiket |
|---|---|---|---|
| K003 → K000-isletim-sistemi | Ses API'si, sürücü yükleme, kaynak yönetimi | çalışma zamanı | [TASARIM] |
| K003 → K001-donanım | DAC/ADC, ses kartı, I2S/TDM hattı | fiziksel | [TASARIM] |
| K003 → K005-firmware | Donanımın hazır hale gelmesi (boot sonrası) | zamanlama | [TASARIM] |
| K004-dsp → K003 | DSP, akışın üstünde çalışır (graph node) | çalışma zamanı | [TASARIM] |
| K003 → K002 | **ID GAP** — registry'de K002 kimliği belirsiz | — | ⚠️ registry §6 |

Bağımlılık yönü: zemin → donanım → motor → DSP → firmware akışına göre K003
**üçüncü katmandır**. Geriye (K000/K001'e) yalnızca arayüz yüzeyinden bağımlıdır;
üst katmanlar K003'ü çağıran taraftır (used_by). Ayrıntı: `audio-bagimliliklar.md`.

## §7 Doğrulama özeti

| Kriter | Tanım | Şu an |
|---|---|---|
| D1 | v2 §2.3 mini-ağaç birebirliği (12 çocuk) | ✅ 2026-10-12 |
| D2 | 12 çocuk klasöründe index.md (8 zorunlu alan) | ✅ 2026-10-12 |
| D3 | master + bagimliliklar + dogrulama dosyaları mevcut | ✅ 2026-10-12 |
| D4 | açık ⚠️ / UNKNOWN satırı kalmadı | ❌ T5, T11 ⚠️ + T12 UNKNOWN |
| D5 | açık sorular kapandı | ❌ (Q1, Q2, Q4, Q5, Q6) |
| D6 | en az bir [TASARIM] → [KANIT] geçişi | ❌ 0 (repo'da C++ motor kodu yok) |
| D7 | status: verified yazımı | ❌ D1–D7 ✅ olunca |

**Geçiş kuralı:** D1–D7 tamamı ✅ → index.md status: draft → verified. Kısmi
geçiş yoktur; D4'te tek bir açık ⚠️ kalırsa verified yazılmaz.

## §8 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q1 | ASIO SDK GPLv3 lisansı CoreMusic'e nasıl etki eder? | ⚠️ VERIFY REQUIRED | legal-advisor + embedded |
| Q2 | NAudio + ASIO tartışması (2026-09-28) tam hangi thread? | ⚠️ VERIFY REQUIRED | researcher |
| Q4 | ASIO SDK 2.3.4 hedefi güncel sürümlerle uyumlu mu? | ⚠️ VERIFY REQUIRED | embedded-engineer |
| Q5 | engine ↔ backend çocuk sınırı nedir? (pull model'de veri sahipliği) | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q6 | input/output ↔ device/driver sorumluluk ayrımı? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q7 | CoreMusic'in kendi yaşam döngüsü durum kümesi nedir? (AAudio 5 durum: Open/Started/Paused/Flushed/Stopped şablon alınır mı?) | ⚠️ VERIFICATION REQUIRED | embedded-engineer |

## §9 Kaynaklar

| Kaynak | İlgili bölüm | Bağlantı |
|---|---|---|
| v2 §2.3 — K003-audio/ mini-ağaç | §1, §2, §3 | coremusic-katmanli-mimari-agaci-v2.md (G: Drive · okundu 2026-10-12) |
| v2 §3 — adlandırma kuralları (KURAL 1–3) | §2, §3 | aynı dosya |
| v2 §4.2/§4.3 — 8 zorunlu alan + K003 şablonu | §7 | aynı dosya |
| v2 §4.5 — path-ID (Yöntem A/B) | §3 çocuk adresleri | aynı dosya |
| ADR-097 K2 — multi-MD şeması ve rol adları | §1, §3 | .ai/.decisions/draft/ADR-097-…md |
| Core Audio — pull model + render döngüsü | §4 A1–A6 | developer.apple.com (TN2091 · AudioUnitDevelopmentFundamentals · CoreAudioOverview · AudioUnitProgrammingGuide · AudioUnitRender) |
| Steinberg ASIO SDK (açık kaynak) | §5 T1 · §8 Q1 | https://www.steinberg.net/developers/asiosdk-open |
| JUCE deposu + JUCE 9 duyurusu | §5 T2, T3 | https://github.com/juce-framework/JUCE · https://forum.juce.com/t/juce-9-is-available-now/69175 |
| NAudio discussions (ASIO başlıkları) | §5 T4–T5 · §8 Q2 | https://github.com/naudio/NAudio/discussions/categories/general |
| WASAPI (Windows Core Audio) + exclusive mode | §5 T6, T7 | learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi · .../exclusive-mode-streams |
| ALSA PCM + plugins | §5 T13, T14 | alsa-project.org/alsa-doc/alsa-lib/pcm.html · pcm_plugins.html |
| PipeWire scheduling/latency/objects | §5 T15–T17 | docs.pipewire.org/page_scheduling.html · page_latency.html · devel/page_objects_design.html |
| Repo ses kanıtı (ffmpeg metadata + ISO 31-band EQ) | §7 D6 | shared/src/AI/AIEngine.php:300-339 |
| Agent registry (JUCE 9, ASIO SDK 2.3.4; ASIO→WASAPI fallback) | §5 T8, T10 | .ai/AGENTS.md §4 · §17 |
| Global mühendislik standartları (lock-free RT, buffer ön-allocate) | §5 T9 | ~/.config/opencode/AGENTS.md §5 |

## §10 Mimari harita (tam ağaç — wiki-link)

§3'teki 12 çocuk tablosunun **dosya düzeyinde** karşılığı. Her satır bir
`<çocuk>/<çocuk>-kapsam.md` + `<çocuk>/<çocuk>-ornekler.md` çiftine işaret
eder; durum sütunu o dalın `status` alanını yansıtır (hepsi 2026-10-12'de
`draft`).

| # | Çocuk | Kapsam dosyası | Örnek dosyası | Doğrulama dosyası | Durum |
|---|---|---|---|---|---|
| 1 | engine | `engine/engine-kapsam.md` | `engine/engine-ornekler.md` | `engine/engine-dogrulama.md` | draft |
| 2 | device | `device/device-kapsam.md` | `device/device-ornekler.md` | `device/device-dogrulama.md` | draft |
| 3 | driver | `driver/driver-kapsam.md` | `driver/driver-ornekler.md` | `driver/driver-dogrulama.md` | draft |
| 4 | backend | `backend/backend-kapsam.md` | `backend/backend-ornekler.md` | `backend/backend-dogrulama.md` | draft |
| 5 | input | `input/input-kapsam.md` | `input/input-ornekler.md` | `input/input-dogrulama.md` | draft |
| 6 | output | `output/output-kapsam.md` | `output/output-ornekler.md` | `output/output-dogrulama.md` | draft |
| 7 | routing | `routing/routing-kapsam.md` | `routing/routing-ornekler.md` | `routing/routing-dogrulama.md` | draft |
| 8 | mixing | `mixing/mixing-kapsam.md` | `mixing/mixing-ornekler.md` | `mixing/mixing-dogrulama.md` | draft |
| 9 | realtime | `realtime/realtime-kapsam.md` | `realtime/realtime-ornekler.md` | `realtime/realtime-dogrulama.md` | draft |
| 10 | synchronization | `synchronization/synchronization-kapsam.md` | `synchronization/synchronization-ornekler.md` | `synchronization/synchronization-dogrulama.md` | draft |
| 11 | diagnostics | `diagnostics/diagnostics-kapsam.md` | `diagnostics/diagnostics-ornekler.md` | `diagnostics/diagnostics-dogrulama.md` | draft |
| 12 | testing | `testing/testing-kapsam.md` | `testing/testing-ornekler.md` | `testing/testing-dogrulama.md` | draft |

**Bu dosyanın kendisi** (kök): `audio-master.md` (anayasa) +
`audio-bagimliliklar.md` (bağımlılıklar) + `audio-dogrulama.md`
(doğrulama tablosu).

### §10.1 12 çocuk — hızlı işlev özeti (1 satır)

| Çocuk | 1 satır |
|---|---|
| engine | Zinciri yürüten ana döngü + yaşam döngüsü (pull — §4 A1) |
| device | Cihaz envanteri/seçimi/değişimi (Q6 ile kilitli) |
| driver | Sürücü yüzeyiyle tek noktadan etkileşim |
| backend | Buffer/kuyruk/çalışma zamanı altyapısı (Q5 ile kilitli) |
| input | Kaynak girişi (dosya, mikrofon, akış) |
| output | Çıkış ucu (hoparlör/kulaklık/akış) — pull çağrısının hedefi (§4 A1) |
| routing | Kaynak→hedef eşleme matrisi + port/link (§5 T17) |
| mixing | Çoklu kaynağı birleştirme (gain/toplama) — routing ile sınır (R2) |
| realtime | RT disiplini: lock-free, ön-atama, yasak kataloğu (§5 T9) |
| synchronization | Clock/epoch uyumu, drift telafisi (§4 A4 monotonluk) |
| diagnostics | Log, ölçüm, hata sınıflandırma (ölçer — TS1) |
| testing | Test senaryoları + kabul kriterleri (karar verir — §4 A6) |

## §11 Kapılar (validation gates — D1–D7 + çocuk kapıları)

§7'deki D1–D7 özetinin **genişletilmiş** hâli: her D-kriterinin arkasındaki
çocuk kapıları. **Kapıların tamamı bu dosyada AÇIK kalır** (sayı uydurma
yasak).

### §11.1 D-kriter ↔ çocuk kapı eşlemesi

| D-kriter | Çocuk kapıları | Şu an |
|---|---|---|
| D1 (v2 §2.3 birebirliği) | — (master düzeyi) | ✅ |
| D2 (12 index.md) | — (master düzeyi) | ✅ |
| D3 (master/bagimliliklar/dogrulama) | — (master düzeyi) | ✅ |
| D4 (açık ⚠️/UNKNOWN kalmadı) | Tüm çocuk kapıları | ❌ |
| D5 (açık sorular kapandı) | Q1 · Q2 · Q4 · Q5 · Q6 · Q7 + çocuk R1–R6'lar | ❌ |
| D6 (ilk [TASARIM] → [KANIT]) | realtime RT-G5 · testing TS-G2 | ❌ (repo'da C++ yok) |
| D7 (status: verified) | D1–D7 tamamı ✅ | ❌ |

### §11.2 Çocuk kapıları özeti (dosya adlarıyla)

| Çocuk | Kapı adları | Kapı durumu |
|---|---|---|
| output | O-G1 · O-G2 · O-G3 · O-G4 · O-G5 | hepsi ❌ (`output/output-kapsam.md` §13) |
| realtime | RT-G1 · RT-G2 · RT-G3 · RT-G4 · RT-G5 | hepsi ❌ (`realtime/realtime-kapsam.md` §12) |
| routing | RO-G1 · RO-G2 · RO-G3 · RO-G4 · RO-G5 · RO-G6 | hepsi ❌ (`routing/routing-kapsam.md` §12) |
| synchronization | SY-G1 · SY-G2 · SY-G3 · SY-G4 · SY-G5 · SY-G6 | hepsi ❌ (`synchronization/synchronization-kapsam.md` §12) |
| testing | TS-G1 · TS-G2 · TS-G3 · TS-G4 · TS-G5 · TS-G6 | hepsi ❌ (`testing/testing-kapsam.md` §12) |
| bağımlılıklar | BK-1 · BK-2 · BK-3 · BK-4 · BK-5 | hepsi ❌ (`audio-bagimliliklar.md` §12) |
| engine · device · driver · backend · input · mixing · diagnostics | — (bu revizyon kapsamı dışında) | ⚠️ ilgili dosyalara bak |

**Kapı kuralı:** bir kapı **yalnız** sahibinin dosyasında açılır (ölçüm
`diagnostics`/`testing`de, karar ADR'de); **bu dosyada hiçbir kapı
açılmaz**. Kısmi geçiş yoktur (§7 kapanış kuralı).

## §12 Bağımlılık & risk özeti (çapraz referans)

§6'daki bağımlılık tablosunun **riskli** hâli: `audio-bagimliliklar.md` §8
(RK-1…RK-8) bu dosyadan bağımsızdır ama kaynak **bu dosyanın §5/§8
açık sorularıdır**. En kritik üç risk:

| Risk | Kaynak (bu dosya) | Etki | Sahip |
|---|---|---|---|
| RK-1 (Q5 — veri sahipliği) | §8 Q5 · §4 A2 | HIGH | Vault Steward |
| RK-2 (Q6 — cihaz ayrımı) | §8 Q6 · §3 satır 6/7 | HIGH | Vault Steward |
| RK-3 (Q7 — ASIO lisans) | §8 Q1 · §5 T1 | HIGH | legal-advisor + embedded |

**Ders:** bu üç riskin **hiçbiri teknik değil, karar eksikliğidir**; kod
yazılmadan **önce** kapatılmalı (vault §1 workflow: APPROVAL → IMPLEMENTATION).

## §13 Açık soru izleme tablosu (Q-kapanış planı)

§8'deki soruların **izleme** tablosu: kapanış yolu ve bağımlı dosyalar.

| # | Soru | Kapanış yolu | Bağımlı dosyalar | Durum |
|---|---|---|---|---|
| Q1 | ASIO GPLv3 etkisi | legal-advisor incelemesi → ADR | audio-bagimliliklar §8 RK-3 | ❌ açık |
| Q2 | NAudio + ASIO thread (2026-09-28) | researcher URL doğrulaması → §5 T5 | audio-master §5 | ❌ açık |
| Q4 | ASIO SDK 2.3.4 uyumu | embedded-engineer sürüm teyidi → §5 T11 | audio-master §5 | ❌ açık |
| Q5 | engine ↔ backend sınırı | Vault Steward ADR | output · realtime · backend (Q5 kilitli) | ❌ açık |
| Q6 | input/output ↔ device/driver | Vault Steward ADR | output · routing · device (Q6 kilitli) | ❌ açık |
| Q7 | Yaşam döngüsü durum kümesi | embedded-engineer şablon kararı (AAudio 5 durum şablon mu?) | engine (master §8) | ❌ açık |

**Plan kuralı:** Q1–Q4 **araştırma** ile, Q5–Q6 **karar (ADR)** ile, Q7
**tasarım** ile kapanır; kapanma öncesi `status: verified` yazılmaz (§7 D7).

## §14 Çocuk dosyaları — derinleştirme kaydı (2026-10-13)

§10'daki 12 çocuktan **beşi** bu revizyonda derinleştirildi (500+ satır
hedefi). Derinleştirme, mevcut bölümlere **dokunmadan** yeni §numaralarla
yapıldı; her dosyanın sonunda dürüstlük bloğu + tek Authority satırı vardır.

| # | Çocuk | Kapsam satır (yaklaşık) | Örnek satır (yaklaşık) | Yeni bölümler |
|---|---|---|---|---|
| 1 | output | ≥500 | ≥500 | §6–§13 (eşleme · WASAPI · Core Audio · ALSA · hız geçişi · çoklu çıkış · underrun · kapılar) |
| 2 | realtime | ≥500 | ≥500 | §6–§12 · §5–§10 (kısıt kataloğu · bütçe · vardiya · inversion · yasaklar) |
| 3 | routing | ≥500 | ≥500 | §6–§12 · §5–§10 (matris · patching · bus · click-free · hata modları) |
| 4 | synchronization | ≥500 | ≥500 | §6–§12 · §5–§10 (clock · jitter buffer · master/slave · telafi · epoch) |
| 5 | testing | ≥500 | ≥500 | §6–§12 · §5–§10 (piramit · SNR/THD · PASS/FAIL · regresyon · auval) |

**Derinleştirme kuralı:** eklenen bölümler **şablon/niyet** statüsündedir;
`[KANIT]` etiketi **almaz** (repo'da C++ yok — D6). Eşik/ölçüm kapıları
**açık** kalır; sayı uydurulmaz → UNKNOWN. `engine · device · driver ·
backend · input · mixing · diagnostics` bu revizyonun **kapsamı dışındadır**.

## §15 Kaynak genişletme (doğrulanmış çerçeveler)

§4–§5'teki URL'lerin **dosya-bazlı** özeti (aynı kaynaklar, yeni iddia yok).
Bu tablo §9'daki kaynak listesinin **derinlemesine**idir; her satır bir
**çerçeve**dir, CoreMusic kodu değildir.

| # | Çerçeve | Kullanıldığı dosya | Ne verdiği |
|---|---|---|---|
| 1 | Core Audio pull + render (A1–A4) | output · realtime · synchronization · testing | Pull-model, monoton sampleTime, AUGraph yaşam döngüsü, auval |
| 2 | WASAPI exclusive/shared (T6–T7) | output | Kip karşılaştırması, akış kurulumu |
| 3 | ASIO açık kaynak + fallback (T1, T8) | driver · device | Windows düşük-gecikme, cihaz kaybı kuralı |
| 4 | ALSA PCM + plug (T13–T14) | output · routing · realtime | hw/sw params, periyot/buffer, kanal dönüşümü |
| 5 | PipeWire scheduling/latency/objects (T15–T17) | realtime · routing · synchronization · testing | Sayaç modeli, latency mismatch, node/port/link |
| 6 | JUCE 9 (T2–T3) | driver · engine | Aday framework (APv2 kararsızlığı — §5.1) |
| 7 | Global RT kuralı (T9 — `.config` AGENTS) | realtime | Lock-free + ön-atama (bağlayıcı kural) |

**Kural:** bu tablo **yalnız mevcut doğrulanmış çerçeve** taşır; yeni URL,
sürüm veya API adı **eklenmez** (uydurma yasağı). Yeni kanıt → `audio-
dogrulama.md` §1'e düşer, bu dosyaya sessizce yazılmaz.

---

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya
referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan
iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu
dosyada AÇIK kalır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
