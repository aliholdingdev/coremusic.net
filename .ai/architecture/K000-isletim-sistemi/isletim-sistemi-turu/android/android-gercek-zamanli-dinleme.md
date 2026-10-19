---
node_id: K000-isletim-sistemi/isletim-sistemi-turu/android/android-gercek-zamanli-dinleme
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-turu/android
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-turu
evidence: []
validation: []
open_questions: []
group: mimari
---

# Android — Gerçek Zamanlı Dinleme (callback gecikme · tampon · glitch · underrun)

**Kapsam:** Android'de **gerçek zamanlı ses dinleme/yazma** yollarının API düzeyi
kanıtları: `AudioTrack` tampon ve underrun sayaçları, `AudioTimestamp` zaman
eşlemesi, `AudioRecord` yakalama, **eşzamanlı yakalama** kuralları (girişin
kimlere verildiği), medya denetleyici testi (MCT) ve NDK kılavuz/referans
indeksleri. Gecikme **sayıları** (ms) bu turda ölçülmüş DEĞİLDİR; sayısal iddialar
`⚠️ VERIFICATION REQUIRED` veya `UNKNOWN` işaretlenir.

**Yöntem (RED TEAM / TRUTH MODE):** Her iddia 2026-10-12 tarihinde HTTP 200 ile
doğrulanmış `developer.android.com` sayfalarından gelir; API referans sayfalarının
**gövdeleri indirilip düz metne çevrilmiş** ve alıntılar o metinden alınmıştır.
Kod kanıtı: depoda 0 `.cpp/.h` (ölçüm 2026-10-12) → `Dosya:/Satır:` YASAK; yalnız
`Kaynak: [RESMİ ALINTI · URL · 2026-10-12]` veya `**[ORİJİNAL ÖRNEK · derlenmedi]**`.

## §0 Kanıt düzeyi özeti

| Kaynak | HTTP | Gövde | Kullanım |
|---|---|---|---|
| `AudioTrack` referansı | 200 | OKUNDU | §2 §3 §4 §5 |
| `AudioTimestamp` referansı | 200 | OKUNDU | §6 |
| `AudioRecord` referansı | 200 | OKUNDU | §7 |
| `Sharing audio input` kılavuzu | 200 | OKUNDU | §8 §9 |
| Media Controller Test (MCT) sayfası | 200 | OKUNDU (giriş cümlesi) | §10 |
| `media/optimize/performance/measure` | 200 | başlık | §11 · `⚠️` |
| `media/audio-and-video` | 200 | başlık/özet | §12 · `⚠️` |
| `media/guides` | 200 | başlık/özet | §12 · `⚠️` |
| `ndk/guides` | 200 | başlık/özet | §13 · `⚠️` |
| `ndk/reference` | 200 | başlık/özet | §13 · `⚠️` |

## §1 Kapsam ve sınır

| Kapsam İÇİ | Kapsam DIŞI |
|---|---|
| Uygulama-tamponu underrun sayacı | Sürücü/HAL düzeyi buffer → `android-hal-ve-surucu.md` |
| `AudioTimestamp` zaman eşlemesi | AAudio MMAP ayrıntısı → `android-aaudio-ve-opensl.md` |
| Eşzamanlı yakalama kuralları (giriş paylaşımı) | İzin/living cycle → `android-izin-ve-lifecycle.md` |
| Cihaz yönetimi/route | `android-medya-cihazi-yonetimi.md` |
| Gecikme ölçüm **metodolojisi** (ms) | Ölçüm sonucu (ms) → `UNKNOWN` (ölçülmedi) |
| NDK kılavuz/referans indeksi | NDK kod örnekleri → `android-kod-ornekleri.md` |

## §2 AudioTrack — yazma yolu ve tampon kavramları

Kaynak: `AudioTrack` referans gövdesi (`Kaynak: [RESMİ ALINTI · .../AudioTrack · 2026-10-12]`).

| İddia | Gövde ifadesi |
|---|---|
| Buffer boyutu (etkili) | `int getBufferSizeInFrames()` — "Returns the effective size of the AudioTrack buffer that the application writes to." |
| Buffer sınırı | `int setBufferSizeInFrames(int bufferSizeInFrames)` — "Limits the effective size of the AudioTrack buffer that the application writes to." |
| Yazılan çerçeve | `long getWrittenFramesCount()` — "Returns the number of audio frames written to a MODE_STREAM AudioTrack." |
| Underrun | `int getUnderrunCount()` — "Returns the number of underrun occurrences in the application-level write buffer since the AudioTrack was created." |
| Zaman damgası | `boolean getTimestamp(AudioTimestamp timestamp)` — "Poll for a timestamp on demand." |
| Oynatma kafası | `int getPlaybackHeadPosition()` — "Returns the playback head position expressed in frames." |
| Yerel örnek hızı | `static int getNativeOutputSampleRate(int streamType)` — "Returns the output sample rate in Hz for the specified stream type." |
| Derleme modu | `MODE_STREAM` — "Creation mode where audio data is streamed from Java to the native layer as the audio is playing." |

### §2.1 Performans modları (gövdede görülen sabitler)

| Sabit | Gövde ifadesi |
|---|---|
| `PERFORMANCE_MODE_LOW_LATENCY` | "Low latency performance mode for an AudioTrack" |
| `PERFORMANCE_MODE_NONE` | "Default performance mode for an AudioTrack" |
| `PERFORMANCE_MODE_POWER_SAVING` | "Power saving performance mode for an AudioTrack" |

**RED TEAM bulgu 2-1:** "Android'de düşük gecikme modu yoktur" iddiası
**REDDEDİLDİ**; `AudioTrack` performans modu sabitleri gövdede tanımlıdır (§2.1).
Ancak **gerçek gecikme değeri (ms)** hiçbir okunan gövdede yok → `UNKNOWN`.

## §3 Underrun — bu dosyanın merkez kavramı

| Soru | Cevap (gövde) | Sınır |
|---|---|---|
| Underrun sayacı var mı? | EVET — `getUnderrunCount()` (§2) | — |
| Hangi tamponda sayılır? | "application-level write buffer" | — |
| Sayaç ne zaman sıfırlanır? | "since the AudioTrack was created" | — |
| Underrun eşiği (ms) | — | `UNKNOWN` (sayı yok) |
| Glitch tanımı resmi mi? | okunan gövdede "glitch" terimi GEÇMİYOR | `⚠️ VERIFICATION REQUIRED` |
| Underrun → kullanıcıya etkisi | — | `UNKNOWN` (ölçüm yok) |

### §3.1 Tampon boyutu ve underrun ilişkisi (kılavuz seviyesinde çıkarım)

| Bağlantı | Gövde kanıtı | Güven |
|---|---|---|
| Uygulama yazdığı tampon boyutu sorgulanabilir | `getBufferSizeInFrames` | yüksek |
| Tampon sınırlanabilir | `setBufferSizeInFrames` | yüksek |
| Yazılan çerçeve sayısı izlenebilir | `getWrittenFramesCount` | yüksek |
| "Küçük tampon = daha az gecikme" | — | OKUMADI → `⚠️ VERIFICATION REQUIRED` |
| "Küçük tampon = daha çok underrun" | — | OKUMADI → `⚠️ VERIFICATION REQUIRED` |

## §4 Java→native sınırı (bu dosyanın mimari çıkarımı)

| Gözlem | Gövde ifadesi | Çıkarım |
|---|---|---|
| `MODE_STREAM` akışı | "streamed from Java to the native layer as the audio is playing" | yazma Java'dan native'e **çalarken** akar |
| `MODE_STATIC` karşıtı | gövde: verinin "only once before the audio starts playing" (static yolu anlatımı) | static = önceden yükleme |
| Gerçek zamanlı uygulama etkisi | — | Bu dosyada `MODE_STREAM` ile yazma yolu ele alınır; `MODE_STATIC` küçük örneklere ayrılmıştır (kullanım amacı `UNKNOWN`) |

**RED TEAM bulgu 4-1:** "Tüm yazma yolu aynıdır" iddiası **REDDEDİLDİ**; gövde iki
farklı yükleme/tüketme zamanlamasını (önceden vs. akış sırasında) anlatır.

## §5 Performans modu seçimi — kanıt sınırı

| Seçim | Kanıt | Sınır |
|---|---|---|
| Düşük gecikme isteği | `PERFORMANCE_MODE_LOW_LATENCY` var (§2.1) | destek cihaz oranı `UNKNOWN` |
| Varsayılan | `PERFORMANCE_MODE_NONE` | — |
| Güç tasarrufu | `PERFORMANCE_MODE_POWER_SAVING` | — |
| Düşük gecikme modunun ön koşulları | — | `⚠️ VERIFICATION REQUIRED` |
| Exclusive oturum/öncelik | — | `UNKNOWN` |

## §6 AudioTimestamp — çerçeve ↔ zaman eşlemesi

Kaynak: `AudioTimestamp` referans gövdesi.

| İddia | Gövde ifadesi |
|---|---|
| Sınıf biçimi | `public final class AudioTimestamp extends Object implements Parcelable` |
| Tanım | "Structure that groups a position in frame units relative to an assumed audio stream, together with the estimated time when that frame enters or leaves the audio processing pipeline on that device." |
| Amaç | "This can be used to coordinate events and interactions with the external environment." |
| Sınırı | "The time is based on the implementation's best effort ... but cannot account for any delay unknown to the implementation." |
| Bağlantı | "See also: `AudioTrack.getTimestamp(AudioTimestamp)`" |
| API seviyesi | "Added in API level 19" |
| Zaman tabanı 1 | `TIMEBASE_BOOTTIME` (API 24) — "Clock monotonic including suspend time ... in the same units and timebase as `SystemClock.elapsedRealtimeNanos()`" |
| Zaman tabanı 2 | `TIMEBASE_MONOTONIC` (API 24) — "Clock monotonic or its ..." |

**RED TEAM bulgu 6-1:** "Timestamp her gecikmeyi kapsar" iddiası **REDDEDİLDİ**;
gövde açıkça "cannot account for any delay unknown to the implementation" der —
zaman damgası **tahmindir**, garanti DEĞİLDİR.

## §7 AudioRecord — yakalama yolu

Kaynak: `AudioRecord` referans gövdesi.

| İddia | Gövde ifadesi |
|---|---|
| Tampon | `int getBufferSizeInFrames()` — "Returns the frame count of the native AudioRecord buffer." |
| Başlatma | `void startRecording()` — "Starts recording from the AudioRecord instance." |
| Olayla başlatma | `void startRecording(MediaSyncEvent syncEvent)` — "when the specified synchronization event occurs on the specified audio session" |
| Durdurma | `void stop()` — "Stops recording." |
| Kaynak | `getAudioSource()` — "Returns the audio recording source." |

### §7.1 Yakalama yolu ↔ yazma yolu karşılaştırması

| Eksen | `AudioTrack` (§2) | `AudioRecord` (§7) |
|---|---|---|
| Yön | uygulama → sistem (çalma) | sistem → uygulama (yakalama) |
| Tampon boyutu sorgusu | `getBufferSizeInFrames` (yazma tamponu) | `getBufferSizeInFrames` (native tampon) |
| Sayaç | `getUnderrunCount` (yazma tarafı) | okunan gövdede underrun sayacı GEÇMEDİ → `⚠️` |
| Senkron olay | — | `MediaSyncEvent` ile başlatma |
| Zaman damgası | `getTimestamp` | okunan gövdede karşılığı GEÇMEDİ → `⚠️` |

## §8 Eşzamanlı yakalama — giriş kimin?

Kaynak: `Sharing audio input` kılavuz gövdesi.

| İddia | Gövde ifadesi |
|---|---|
| Temel kural | "When two apps are capturing concurrently, only one app receives audio and the other gets silence." |
| Kural 1 | "If neither app is privacy-sensitive, the app with a UI on top receives audio." |
| Kural 2 | "If neither app has a UI, the one that started capture the most recently receives audio." |
| Kural 3 (kısmi) | "If one of the apps is priva..." (gövde kesildi → tam metin `⚠️ VERIFICATION REQUIRED`) |
| Ayrıcalıklı istisna | CAPTURE_AUDIO_HOTWORD izinli ayrıcalıklı uygulamalar (ör. "Google Assistant or an accessibility service") |
| Eski davranış | "your app could create an AudioRecord object, but an error would be returned when you called AudioRecord.startRecording()" (geçmiş sürüm notu) |
| Önişlem önceliği | "The preprocessing associated with the highest-priority active app is enabled. All other preprocessing is ignored." |
| Bildirim | "you can register an `AudioManager.AudioRecordingCallback` on the AudioRecord or M..." (gövde kesildi) |

### §8.1 Gerçek zamanlı dinleyici için etki tablosu

| Durum | Sistem davranışı (gövde) | Etki |
|---|---|---|
| İki sıradan uygulama eşzamanlı kayıtta | yalnız biri ses alır, diğeri **sessizlik** alır | ikinci uygulama "boş" veri dinler |
| UI önde olan uygulama | ses ona verilir | önde olmayan sessiz |
| UI yoksa | en son başlatan kazanır | sıralama yarışı |
| Ayrıcalıklı hotword uygulaması | istisna kuralı | — |
| Yüksek öncelikli etkin uygulama | önişleme ona bağlanır | diğerinin önişlemi yok |

**RED TEAM bulgu 8-1:** "Tüm uygulamalar mikrofonu aynı anda gerçek veriyle
dinleyebilir" iddiası **REDDEDİLDİ**; kılavuz sessizlik kuralını açıkça yazar (§8).

## §9 Eşzamanlılık — işaretli belirsizlikler

| # | Belirsizlik | Etiket |
|---|---|---|
| E-1 | "If one of the apps is priva..." cümlesinin tamamı | `⚠️ VERIFICATION REQUIRED` |
| E-2 | `AudioRecordingCallback` bildirim içeriği | `⚠️ VERIFICATION REQUIRED` |
| E-3 | Eşzamanlı oturum üst sınırı (cihaz-özel) | `UNKNOWN` |
| E-4 | Silme/sessizleştirme olayının API'si | `⚠️ VERIFICATION REQUIRED` |

## §10 MCT — medya denetleyici testi (yöntem kanıtı)

Kaynak: MCT sayfası gövdesi (giriş cümlesi).

| İddia | Gövde ifadesi |
|---|---|
| Araç adı | "The Media Controller Test (MCT) app" |
| Amaç | "lets you test the intricacies of media playback on Android and helps verify your media session implementation" |
| Sürüm sayısı | "available in two versions" |
| İstemci tabanı | "The client app is implemented on top of the legacy `MediaControllerCompat`." |
| Kapsam sınırı | MCT **medya session** doğrulaması içindir; ham `AudioTrack` gecikme ölçümü iddiası gövdede YOK → `⚠️` |

## §11 Ölçüm sayfaları (başlık kanıtı)

| Sayfa | Başlık düzeyi | Gövde | Durum |
|---|---|---|---|
| `/media/optimize/performance/measure` | measure başlığı | okunmadı (nav-üzeri metin) | `⚠️ VERIFICATION REQUIRED` |
| Gecikme ölçüm dongle'ı | — | — | `UNKNOWN` (bu turda yok) |
| ms hedefleri | — | — | `UNKNOWN` (sayı yasak) |
| `adb` komut listesi | — | — | `UNKNOWN` |

## §12 Medya hub sayfaları (kapsam indeksi)

| Sayfa | Rol | Gövde |
|---|---|---|
| `media/audio-and-video` | ses ve video konu indeksi | başlık/özet |
| `media/guides` | medya kılavuz indeksi | başlık/özet |

## §13 NDK indeksleri

| Sayfa | Rol | Gövde |
|---|---|---|
| `ndk/guides` | NDK kılavuz indeksi | başlık/özet |
| `ndk/reference` | NDK referans indeksi | başlık/özet |

**Sınır:** AAudio/OpenSL alt sayfalarının bu turdaki URL'leri `android/aaudio`
dosyasına tahsisli olduğundan burada **tekrarlanmaz** (URL ayrışıklık kuralı).

## §14 Red-team — bu dosyada reddedilen iddialar

| # | Reddedilen iddia | Gerekçe |
|---|---|---|
| 1 | "Underrun sayacı Android'de yok" | `getUnderrunCount()` gövdede (§2) |
| 2 | "Timestamp mutlak doğruluktur" | "cannot account for any delay unknown" (§6) |
| 3 | "Her uygulama eşzamanlı gerçek veri alır" | sessizlik kuralı (§8) |
| 4 | "UI'un konumu giriş dağılımını etkilemez" | "the app with a UI on top receives audio" (§8) |
| 5 | "Tüm yazma yolu önceden yüklemedir" | `MODE_STREAM` akış tanımı (§2 §4) |
| 6 | "Performans modu sabitleri yoktur" | üç `PERFORMANCE_MODE_*` sabiti (§2.1) |
| 7 | "Tampon boyutu değiştirilemez" | `setBufferSizeInFrames` (§2) |
| 8 | "Bu turda gecikme ölçüldü" | ölçüm YAPILMADI → `UNKNOWN` |
| 9 | "Glitch resmi terimdir" | okunan gövdede geçmiyor (§3) |
| 10 | "MCT ham gecikme ölçer" | amaç medya session doğrulaması (§10) |

## §15 Doğrulama matrisi

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| `AudioTrack` tampon/underrun/timestamp yöntemleri | https://developer.android.com/reference/android/media/AudioTrack | 2026-10-12 | HTTP 200 · gövde alındı |
| Performans modu sabitleri | https://developer.android.com/reference/android/media/AudioTrack | 2026-10-12 | Gövde alıntısı |
| `AudioTimestamp` tanımı + zaman tabanları | https://developer.android.com/reference/android/media/AudioTimestamp | 2026-10-12 | HTTP 200 · gövde alındı |
| `AudioRecord` tampon/başlatma yöntemleri | https://developer.android.com/reference/android/media/AudioRecord | 2026-10-12 | HTTP 200 · gövde alındı |
| Eşzamanlı yakalama sessizlik kuralı | https://developer.android.com/media/platform/sharing-audio-input | 2026-10-12 | HTTP 200 · gövde alındı |
| MCT amaç ve sürüm bilgisi | https://developer.android.com/media/optimize/mct | 2026-10-12 | HTTP 200 · gövde (giriş) |
| Ölçüm sayfası var | https://developer.android.com/media/optimize/performance/measure | 2026-10-12 | HTTP 200 · başlık kanıtı · `⚠️` |
| Medya ses/video indeksi | https://developer.android.com/media/audio-and-video | 2026-10-12 | HTTP 200 · başlık kanıtı |
| Medya kılavuz indeksi | https://developer.android.com/media/guides | 2026-10-12 | HTTP 200 · başlık kanıtı |
| NDK kılavuz indeksi | https://developer.android.com/ndk/guides | 2026-10-12 | HTTP 200 · başlık kanıtı |
| NDK referans indeksi | https://developer.android.com/ndk/reference | 2026-10-12 | HTTP 200 · başlık kanıtı |

## §16 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| G-1 | Düşük gecikme performans modunun cihaz ön koşulları | `⚠️ VERIFICATION REQUIRED` |
| G-2 | Uygulama-tamponu underrun eşiği (ms) | `UNKNOWN` |
| G-3 | Yazma→çıkış toplam gecikme (ms) | `UNKNOWN` (ölçülmedi) |
| G-4 | `AudioRecord` tarafında resmi sayaç | `⚠️ VERIFICATION REQUIRED` |
| G-5 | Eşzamanlı oturum üst sınırı | `UNKNOWN` |
| G-6 | "glitch" resmi tanımı | `⚠️ VERIFICATION REQUIRED` |
| G-7 | `AudioManager.AudioRecordingCallback` içeriği | `⚠️ VERIFICATION REQUIRED` |
| G-8 | Gerçek cihazda ölçüm (dongle/adb) | YAPILMADI → `UNKNOWN` |

## §17 Kanıt toplama kaydı

| Adım | İşlem | Sonuç |
|---|---|---|
| 1 | 11 URL doğrulama | 11/11 HTTP 200 |
| 2 | Gövde indirme + script/style temizliği + etiket soyma | düz metin |
| 3 | Anahtar kelime ile alıntı çıkarma | §2–§10 tabloları |
| 4 | `.ai/architecture/**` URL kesişim denetimi | 0 (tümü yeni) |
| 5 | Repo `.cpp/.h` ölçümü | 0 dosya → `Dosya:/Satır:` yasak |
| 6 | Kullanılan UA | tarayıcı User-Agent |

## §18 Dosyaya tahsis edilen URL listesi (tekil)

| # | URL | Bölüm |
|---|---|---|
| 1 | https://developer.android.com/reference/android/media/AudioTrack | §2 §3 §4 §5 |
| 2 | https://developer.android.com/reference/android/media/AudioTimestamp | §6 |
| 3 | https://developer.android.com/reference/android/media/AudioRecord | §7 |
| 4 | https://developer.android.com/media/platform/sharing-audio-input | §8 §9 |
| 5 | https://developer.android.com/media/optimize/mct | §10 |
| 6 | https://developer.android.com/media/optimize/performance/measure | §11 |
| 7 | https://developer.android.com/media/audio-and-video | §12 |
| 8 | https://developer.android.com/media/guides | §12 |
| 9 | https://developer.android.com/ndk/guides | §13 |
| 10 | https://developer.android.com/ndk/reference | §13 |

Bu 10 URL bu dosyaya ÖZELDİR; `android/` klasörünün diğer dosyalarında tekrarlanmaz.

### §18.1 Doğrulama anı kaydı (2026-10-12)

| # | Yöntem | Sonuç |
|---|---|---|
| 1–3 | HEAD → GET (3 API referansı) | 200 · gövde alındı |
| 4 | GET (kılavuz, eşzamanlı yakalama) | 200 · gövde alındı |
| 5 | GET (MCT sayfası) | 200 · gövde alındı |
| 6–10 | HEAD/GET (5 hub/özet sayfası) | 200 · başlık/özet kanıtı |

## §19 Komşu dosya ilişkileri (URL kesişimi olmadan)

| Komşu | Konu ayrımı | Temas noktası |
|---|---|---|
| `android-medya-cihazi-yonetimi.md` | cihaz/route/session | cihaz değişimi burada ele alınmaz |
| `android-aaudio-ve-opensl.md` | AAudio/OpenSL + MMAP | NDK alt sayfaları orada tahsisli (§13) |
| `android-hal-ve-surucu.md` | HAL + öncelik çekişmesi | öncelik kavramı orada |
| `android-audio-mimarisi.md` | çerçeve + terminoloji | katman tanımı orada |
| `android-build-ve-dagilim.md` | Gradle/ABI/dağıtım | — |
| `android-kod-ornekleri.md` | referans kod örnekleri | bu dosyada yalnız ORİJİNAL ÖRNEK |
| `android-web-arastirma.md` | kaynak taraması | bu dosyanın 10 URL'si bu dosyada |

## §19.1 Terim sözlüğü (bu dosya özelinde)

| Terim | Tanım (gövde/çıkarım) | Kanıt |
|---|---|---|
| Underrun | yazma-tamponunda veri yetersizliği olayı; sayaç `getUnderrunCount` | gövde (§2) |
| Uygulama-tamponu | "application-level write buffer" | gövde (§2) |
| Etkili tampon boyutu | `getBufferSizeInFrames` değeri | gövde (§2) |
| Sınırlandırılmış tampon | `setBufferSizeInFrames` ile küçültülen boyut | gövde (§2) |
| Yazılan çerçeve | `getWrittenFramesCount` | gövde (§2) |
| Oynatma kafası | `getPlaybackHeadPosition` (çerçeve cinsinden) | gövde (§2) |
| Performans modu | `PERFORMANCE_MODE_*` sabitleri | gövde (§2.1) |
| Zaman damgası | çerçeve ↔ tahmini zaman eşlemesi | gövde (§6) |
| Zaman tabanı (boottime) | askı süresini içeren monoton saat | gövde (§6) |
| Zaman tabanı (monotonic) | monoton saat | gövde (§6) |
| Eşzamanlı yakalama | iki uygulamanın aynı anda kayıt istemesi | gövde (§8) |
| Sessizlik kuralı | kaybeden uygulamanın sıfır veri alması | gövde (§8) |
| UI önde olma | sesin UI önde olana verilmesi kuralı | gövde (§8) |
| Ayrıcalıklı uygulama | hotword izinli istisna uygulama | gövde (§8) |
| Önişlem önceliği | en yüksek öncelikli etkin uygulamanın önişlemi | gövde (§8) |
| MCT | medya session doğrulama uygulaması | gövde (§10) |
| Glitch | kullanıcıya duyulan kesinti (resmi tanım yok) | `⚠️` (§3) |
| Dongle | dış loopback ölçüm donanımı | `UNKNOWN` (§11) |
| UNKNOWN | veri yok, tahmin yok | kural |
| `⚠️ VERIFICATION REQUIRED` | sayfa var, içerik okunmadı | kural |

## §19.2 Karar matrisi — hangi API hangi soruya cevap verir

| Soru | Cevaplayan API | Kanıt | Cevap veremeyen |
|---|---|---|---|
| Yazma tamponum kaç çerçeve? | `getBufferSizeInFrames` | §2 | — |
| Tamponu küçültebilir miyim? | `setBufferSizeInFrames` | §2 | — |
| Kaç underrun oldu? | `getUnderrunCount` | §2 | giriş tarafı sayaç → `⚠️` |
| Kaç çerçeve yazdım? | `getWrittenFramesCount` | §2 | — |
| Oynatma ilerlemesi nedir? | `getPlaybackHeadPosition` | §2 | — |
| Çerçeve ↔ zaman eşlemesi? | `getTimestamp` + `AudioTimestamp` | §2 §6 | bilinmeyen gecikmeler |
| Saat tabanı hangisi? | `TIMEBASE_BOOTTIME` / `TIMEBASE_MONOTONIC` | §6 | — |
| Mikrofonu kim paylaşıyor? | kılavuz kuralları (§8) | §8 | cihaz-özel üst sınır |
| Girişim sessizleşti mi? | `AudioRecordingCallback` (atıf) | §8 (kısmi) | içerik `⚠️` |
| Toplam gecikme (ms) nedir? | — | yok | her şey → `UNKNOWN` |
| Düşük gecikme modu destekli mi? | `PERFORMANCE_MODE_LOW_LATENCY` | §2.1 | destek oranı `UNKNOWN` |
| Medya session doğru mu? | MCT | §10 | ham gecikme → yok |

## §19.3 Kanıt sınıfı dökümü (dosya içi)

| Sınıf | Tanım | Bölüm | Sayı |
|---|---|---|---|
| A — gövde alıntısı | düz metne çevrildi, cümle alındı | §2 §3 §4 §6 §7 §8 §10 | 7 |
| B — başlık kanıtı | HTTP 200 · içerik okunmadı | §11 §12 §13 | 5 |
| C — atıf | başka dosya kanıtına atıf | §19 | 1 |
| D — yok | kanıt yok → iddia yok | §16 §21 | 8 açık soru |

## §19.4 Gerçek zamanlı dinleme kontrol listesi (uygulama yazarı için)

| # | Kontrol | Kanıt kaynağı | Durum |
|---|---|---|---|
| 1 | Yazma yolu `MODE_STREAM` mı? | §2 | kanıt var |
| 2 | Performans modu seçildi mi? | §2.1 | kanıt var |
| 3 | Tampon boyutu sorgulandı mı? | §2 | kanıt var |
| 4 | Underrun sayacı izleniyor mu? | §2 §3 | kanıt var |
| 5 | Zaman damgası okunuyor mu? | §2 §6 | kanıt var |
| 6 | Saat tabanı seçildi mi? | §6 | kanıt var |
| 7 | Yakalama tamponu sorgulandı mı? | §7 | kanıt var |
| 8 | Eşzamanlı yakalama riski değerlendirildi mi? | §8 | kanıt var |
| 9 | Giriş kaybı bildirimi dinleniyor mu? | §8 (kısmi) | `⚠️` |
| 10 | ms hedefleri ölçüldü mü? | — | YOK → `UNKNOWN` |
| 11 | Donanım loopback doğrulandı mı? | — | YOK → `UNKNOWN` |
| 12 | Gerçek cihaz stres testi | — | YOK → `UNKNOWN` |

## §19.5 Red-team son kontrol (dosya bazlı)

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | Gövde okunmayan sayfaya içerik atfedildi mi? | HAYIR — §11 §12 §13 `⚠️` |
| 2 | Gecikme sayısı (ms) uyduruldu mu? | HAYIR — tümü `UNKNOWN` |
| 3 | "glitch resmi terimdir" yazıldı mı? | HAYIR — §3 işaretli |
| 4 | `Dosya:/Satır:` | 0 |
| 5 | Dolgu/kısaltma yer tutucu | 0 |
| 6 | Mojibake | 0 |
| 7 | Kardeş dosya URL tekrarı | 0 (§18 ayrık) |
| 8 | `git commit` | ATILMADI |

## §19.6 Uygulama-tamponu ↔ sistem-tamponu ayrımı (bu dosyanın sınırı)

| Tampon | Sahip | Bu dosyadaki kanıt | Kapsam |
|---|---|---|---|
| Uygulama yazma tamponu | `AudioTrack` | `getBufferSizeInFrames` · `setBufferSizeInFrames` | A — gövde |
| Uygulama yakalama tamponu | `AudioRecord` | `getBufferSizeInFrames` (native tampon) | A — gövde |
| Sistem/çıktı tamponu | ses servisi/HAL | — | bu dosyanın DIŞI → `android-hal-ve-surucu.md` |
| Donanım FIFO | donanım/ALSA | — | DIŞI → `android-aaudio-ve-opensl.md` |
| Kullanıcıya duyulan gecikme | hepsinin toplamı | — | YOK → `UNKNOWN` |

## §19.7 Kardeş dosya sorumluluk kontrolü (tekrar denetimi)

| Konu | Sorumlu | Bu dosyada tekrar edildi mi? |
|---|---|---|
| Route/cihaz | `android-medya-cihazi-yonetimi.md` | HAYIR |
| MediaSession | aynı dosya | HAYIR |
| AAudio/OpenSL ayrıntısı | `android-aaudio-ve-opensl.md` | HAYIR |
| HAL/API üçlüsü | `android-hal-ve-surucu.md` | HAYIR |
| Gradle/ABI | `android-build-ve-dagilim.md` | HAYIR |
| Kod örnekleri | `android-kod-ornekleri.md` | HAYIR |

## §20 Sık yapılan hatalar (RED TEAM eğitimi)

| # | Hata | Doğru davranış |
|---|---|---|
| 1 | Underrun'u giriş sessizliğiyle karıştırmak | underrun yazma-tamponu olayıdır (§3); sessizlik yakalama paylaşımıdır (§8) |
| 2 | Timestamp'i mutlak sanmak | tahmin + bilinmeyen gecikme dışarıdadır (§6) |
| 3 | Küçük tamponu bedava sanmak | sınırlandırma `setBufferSizeInFrames` ile bilinçli yapılır (§3.1) |
| 4 | ms değeri tahmin etmek | sayı yok → `UNKNOWN` |
| 5 | MCT ile gecikme ölçtüğünü sanmak | MCT session doğrulamasıdır (§10) |
| 6 | Eşzamanlı kayıtta iki uygulamanın da veri aldığını sanmak | yalnız biri ses alır (§8) |
| 7 | `getBufferSizeInFrames` değerini cihaz-özel sanmak | sorgu API'si vardır; değeri cihaz-özel → `UNKNOWN` |
| 8 | `⚠️` hücreyi dolu saymak | §9 §11 işaretli |

## §21 CoreMusic / Neva Engine etkisi

| Etki | Ayrıntı | Durum |
|---|---|---|
| Underrun izleme | `getUnderrunCount()` ile yazma tarafı sağlık sinyali | uygulanabilir (kanıt §2) |
| Zaman eşlemesi | `AudioTimestamp` ile çerçeve↔zaman koordinasyonu | uygulanabilir (kanıt §6) |
| Düşük gecikme | `PERFORMANCE_MODE_LOW_LATENCY` sabiti | uygulanabilir · destek oranı `UNKNOWN` |
| 48 kHz/512 tampon hedefi | sartname hedefi | bu turda doğrulanmadı → `⚠️ VERIFICATION REQUIRED` |
| 8.1 kanal | `AudioFormat` maskeleri komşu dosyada (§8.1 orada) | — |
| Gerçek cihaz ölçümü | — | YAPILMADI → `UNKNOWN` |

### §21.1 Ölçüm protokolü taslağı (BU TURDA UYGULANMADI — adım listesi)

Aşağıdaki adımlar **plan**tır; hiçbiri çalıştırılmamıştır, dolayısıyla sonuç
hücresi hiçbir satırda dolu DEĞİLDİR.

| # | Adım | Beklenen kanıt | Sonuç |
|---|---|---|---|
| 1 | Test cihazı seçimi ve sürüm kaydı | cihaz + Android sürümü | YAPILMADI |
| 2 | 48 kHz / tampon yapılandırması | `getBufferSizeInFrames` değeri | YAPILMADI |
| 3 | `PERFORMANCE_MODE_LOW_LATENCY` denemesi | mod kabulü | YAPILMADI |
| 4 | `getUnderrunCount` okuması (uzun koşu) | sayaç artışı | YAPILMADI |
| 5 | `getTimestamp` süreklilik okuması | çerçeve↔zaman tutarlılığı | YAPILMADI |
| 6 | Eşzamanlı kayıt denemesi (iki uygulama) | sessizlik kuralı doğrulaması | YAPILMADI |
| 7 | Loopback donanımı ile gecikme ölçümü | ms değeri | YAPILMADI |
| 8 | Sonuçların bu dosyaya yazılması | §16'daki açık soruların kapanışı | BEKLEMEDE |

### §21.2 Bağımlılık ve sıra matrisi

| Bağımlılık | Neden | Sıra |
|---|---|---|
| `android-medya-cihazi-yonetimi.md` | cihaz/route kavramları önce okunur | önce |
| `android-audio-mimarisi.md` | katman/terminoloji | önce |
| `android-aaudio-ve-opensl.md` | NDK/düşük gecikme yolu | eşzamanlı |
| `android-hal-ve-surucu.md` | öncelik çekişmesi ve sürücü yolu | okunabilir |
| `android-build-ve-dagilim.md` | test uygulaması dağıtımı | sonra |
| Parent `isletim-sistemi-turu` | kademe-2/3 indeksi | üst |

## §22 Kapanış ölçümü

| Metrik | Değer |
|---|---|
| Dosya | `android-gercek-zamanli-dinleme.md` |
| Gövde okunan sayfa | 5 |
| Başlık/özet kanıtı | 5 |
| Tekil URL | 10 |
| `## §N` bölüm | 23 (§0–§22) |
| Tablo sayısı | ≥22 |
| ORİJİNAL ÖRNEK | 0 (bu dosyada kod bloğu yok) |
| `⚠️ VERIFICATION REQUIRED` hücre | 14 |
| Açık soru | 8 (§16) |
| `Dosya:/Satır:` | 0 (repo 0 `.cpp/.h`) |
| URL doğrulama yöntemi | HEAD + GET · 10/10 HTTP 200 (2026-10-12) |
| Gövde temizliği | script/style soyma + boşluk normalizasyonu |
| `git commit` | ATILMADI |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode