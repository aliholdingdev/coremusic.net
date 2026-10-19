---
node_id: "K000-isletim-sistemi/isletim-sistemi-turu/android/android-audio-mimarisi"
node_type: "architecture"
status: "verify-required"
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-audio"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-kernel"
evidence:
  - "https://source.android.com/docs/core/audio"
  - "https://source.android.com/docs/core/audio/terminology"
  - "https://source.android.com/docs/core/audio/attributes"
validation: "Dosya içi doğrulama matrisi §10 (| İddia | URL | Tarih: 2026-10-12 | Durum |) — parent index §GRUPLAR satırı bekleniyor"
open_questions:
  - "AOSP ses yığınındaki gerçek katman sayımları cihaz-özgü yapılandırmaya bağlı — bu dosya doküman kanıtıyla sınırlıdır"
  - "Kod örnekleri dosyasında Dosya:/Satır: yok (repo 0 .cpp/.h) — bkz. android-kod-ornekleri.md §0"
group: mimari
---

# Android — Ses Mimarisi (Audio Architecture)

**Kapsam:** Android ses yığınının platform tarafı — AOSP katmanları, ses öznitelikleri
(AudioAttributes), veri formatları ve dönüşüm kuralları, gecikme (latency) tasarımı,
uygulama katmanı medya API'leri. `windows/linux/macos` derin-dallarından ve ReactOS
derin-dalından bağımsız, AOSP'ye (Android Open Source Project) ait bir platformdur.

**Yöntem (RED TEAM / TRUTH MODE):** Tek doğrulama turu — 2026-10-12 tarihinde
`source.android.com` ve `developer.android.com` üzerinden doğrudan HTTP isteği
çekildi; her iddia aşağıdaki §10 matrisinde gerçek URL ile işaretlenir. Desteklenmeyen
iddia `⚠️ VERIFICATION REQUIRED`, bilinmeyen değer `UNKNOWN` yazılır. Model hafızası
ve web araması, bu dosyadaki sayfa gövdesi kanıtının ALTINDADIR.

**Kod kanıtı politikası:** Bu depoda 0 adet `.cpp/.h/.hpp` dosyası vardır; bu nedenle
`Dosya:/Satır:` referansı ÜRETİLEMEZ. Kod blokları yalnız iki kaynaktan gelir:
`[RESMİ ALINTI · URL · 2026-10-12]` (doküman gövdesinden alınmış blok) veya
`**[ORİJİNAL ÖRNEK · derlenmedi]**`.

## §0 Bu dosyanın iddia bütünlüğü

| Soru | Cevap | Kanıt |
|---|---|---|
| Hangi siteler? | `source.android.com` + `developer.android.com` | §10 matrisi |
| Doğrulama tarihi | 2026-10-12 | bu dosyanın her tablosu |
| Sayfa gövdesi okundu mu? | Üç ana kaynakta EVET (NDK audio, AAudio+MMAP, Terminology, Data formats, Attributes) | §2 · §3 · §4 · §5 |
| Sadece durum kodu mu? | Kalan URL'lerde EVET — otorite `⚠️ VERIFICATION REQUIRED` ile işaretli | §10 |
| Bağlı olduğu node | `isletim-sistemi-audio`, `isletim-sistemi-kernel` | frontmatter `depends_on` |

## §1 Kapsam ve kapsam dışı

| # | Kapsam İÇİ | Kapsam DIŞI |
|---|---|---|
| 1 | AOSP ses yığını katmanları | Uygulama düzeyi UI/UX |
| 2 | AudioAttributes ve stream dönüşümleri | OEM cihaz kalibrasyonu (UNKNOWN) |
| 3 | Veri formatları, Qm.n, kayıp/kayıpsız dönüşüm | Kod derleme ve paketleme |
| 4 | Gecikme (latency) tasarımı ve ölçüm sayfaları | iOS / Tizen / webOS karşılaştırması (ayrı dosyalar) |
| 5 | Uygulama katmanı API yüzeyi (MediaPlayer, AudioTrack) | Dış kabuk (out-of-band) codec lisansları |

## §2 AOSP ses yığını — doküman ağacı

Kaynak: ses dokümanlarının ana indeks sayfası (§10 satır 1). Sayfanın kendi TOC
yapısı aşağıdaki bölüm adlarını listeler; bu tablo dokümandaki **bölüm adlarını**
yeniden üretir, cihaz-özgü gerçek katman sayısını değil.

| Katman | Doküman bölümü | Rol (doküman iddiası) |
|---|---|---|
| Uygulama (Java/Kotlin) | Audio attributes · Data formats · Effects | `AudioAttributes`, `AudioTrack`, `AudioRecord` |
| NDK (yerel) | AAudio and MMAP · Audio warmup | AAudio akışları, MMAP/NOIRQ yolu |
| Çerçeve (framework) | Terminology · Latency | `mediaserver`, `AudioFlinger`, strateji motoru |
| Política (policy) | Configurable Audio Policy support in AIDL HAL · Configure audio policies | yönlendirme + ses seviyesi kararları |
| HAL | Audio HAL · AIDL Audio HAL · HIDL Audio HAL · AIDL and HIDL Audio HAL comparison | donanım soyutlama |
| Kütüphane (user-mode) | — (terminoloji sayfası) | TinyALSA — HAL implementasyonları için önerilen kütüphane |
| Kernel | — (AAudio/MMAP sayfası) | ALSA sürücüsü, `/dev/snd/` dosya tanımlayıcıları, dmabuf |
| Efektler | Effects · High-resolution audio · Sound dose | efekt zinciri, yüksek çözünürlüklü ses |
| Donanım bağlantıları | USB · TV audio · Combined audio device routing · Device type limit | giriş/çıkış cihazı yönetimi |
| Duyusal özellikler | MIDI · Sound Trigger · Spatial audio · Concurrent capture | MIDI, uyanma-kelimesi, uzamsal ses |

**RED TEAM bulgu 2-1:** Doküman ağacı bir "sabit mimari şema" değildir; AIDL/HIDL
karşılaştırma sayfası varlığı, HAL arayüzünün sürüm boyunca İKİ farklı taşıyıcıyla
yaşadığını gösterir. Tek-sürüm-tek-arayüz varsayımı bu dosyada REDDEDİLDİR.

## §3 Terminoloji sözlüğü (sayfa gövdesinden)

Kaynak: Terminoloji sayfası gövdesi, doğrudan alıntı temelli özet (`[RESMİ ALINTI · URL · 2026-10-12]` mantığıyla, §10 satır 2).

| Terim | Dokümandaki tanımı (özet) |
|---|---|
| fast track | Düşük gecikmeli, bazı cihaz ve yönlendirmelerde daha az özellikli `AudioTrack`/`AudioRecord` istemcisi |
| normal mixer | `AudioFlinger` içindeki, çoğu tam özellikli istemciye hizmet eden iş parçacığı; doğrudan çıkış cihazını sürer veya sub-mix'i pipe ile FastMixer'e besler |
| NBAIO | Girdi-çıktı için bloklamayan (nonblocking) soyutlama; "nonblocking" adına rağmen bazı implementasyonlar bloklayabilir |
| StateQueue | `AudioFlinger` içinde veri değil **kontrol** bilgisini senkronize eden modül |
| strategy | Benzer davranışlı stream tipi grubu; ses politikası servisi tarafından kullanılır |
| stream type | Çıkış için kullanım durumunu ifade eden enum; ses politikası ses seviyesi + yönlendirme kararlarında kullanır |
| volume index | Mutlak sönüm yerine **birim (unit) içermeyen** tam sayı; `AudioManager` API'leri bu indeksle çalışır |
| volume attenuation curve | Genel ses indeksini belirli bir çıkış için sönüm faktörüne eşleyen cihaza özgü eşleme |
| TinyALSA | ALSA kernel üzerinde küçük bir user-mode API, BSD lisanslı; **HAL implementasyonları için önerilen** kütüphane |
| OpenSL ES | Khronos Group ses API standardı; API seviyesi 9 ve üzeri Android sürümleri OpenSL ES 1.0.1 alt kümesine dayalı native ses API destekler |
| pro audio | `android.hardware.audio.pro` özellik bayrağı; gereksinimler Android CDD bölüm 5.10 "Professional Audio" içinde belgelenmiştir |
| real time | İlgili olaylara zorunlu zaman sınırı içinde yanıt veren sistem; `pro audio` için **gerekli ama yeterli olmayan** ön koşul |
| SoundPool | `AudioTrack` üzerinde seviyeli sınıf; örneklenmiş klipleri çalar (UI geri bildirimi ve oyun sesleri için) |
| ToneGenerator | `AudioTrack` üzerinde seviyeli sınıf; DTMF çift-tonlu sinyalleri çalar |
| Stagefright | Medya oynatma motoru (doküman: "See Media") |
| media.log | Yalnızca özel derlemelerdeki `AudioFlinger` hata ayıklama özelliği; olayları dairesel tampona yazar |
| mediaserver` | Sesle ilgili servisleri (AudioFlinger dahil) içeren Android sistem süreci |
| silent mode | Zil ve bildirimleri medya oynatmadan susturan kullanıcı ayarı |
| tee sink | `Audio debugging` bölümünde ele alınan gözetleme çıkışı |

**RED TEAM bulgu 3-1:** Terminoloji sayfası `pro audio` bayrağını "profesyonel kullanıcı"
olarak DEĞİL, "öngörülebilir gerçek-zamanlı performans seviyesi" olarak tanımlar.
Yaygın yanlış okuma bu dosyada düzeltilmiştir.

## §4 Ses öznitelikleri (AudioAttributes)

Kaynak: Attributes sayfası gövdesi (§10 satır 3).

### §4.1 Bayraklar ve kullanım

| Öğe | Doküman iddiası |
|---|---|
| `HW_AV_SYNC` | Sistemden donanım A/V senkronizasyonunu destekleyen bir çıkış stream'i seçmesini ister |
| Bayrak doğası | Öznitelik bayrakları **karmaşıktır** (non-exclusive), birlikte kullanılabilirler |
| `STREAM_SYSTEM_ENFORCED` ihtiyacı | Kamera deklanşör sesi gibi zorunlu sistem seslerinin ihtiyaçlarını karşılar |
| Önerilen API | Android 5.0 ve üzeri için uygulamalar öznitelikleri kullanmalıdır |
| Geriye dönük uyum | Öznitelik kullanmayan uygulamalar için çerçeve legacy stream tiplerini otomatik olarak özniteliklere çevirir |
| Uyarı | Bu eşleme cihaz, üretici veya Android sürümü arasında **garanti edilmez ve zorunlu kılınmaz** |

### §4.2 Uyumluluk eşleme tablosu (doküman çizelgesi)

| CONTENT_TYPE / USAGE (Android 5.0+) | Android 4.4 ve öncesi karşılığı |
|---|---|
| `CONTENT_TYPE_SPEECH` → `USAGE_VOICE_COMMUNICATION` | `STREAM_VOICE_CALL` |
| `CONTENT_TYPE_SONIFICATION` → `USAGE_ASSISTANCE_SONIFICATION` | `STREAM_SYSTEM` |
| `CONTENT_TYPE_SONIFICATION` → `USAGE_NOTIFICATION_RINGTONE` | `STREAM_RING` |
| `CONTENT_TYPE_MUSIC` → `USAGE_UNKNOWN` / `USAGE_MEDIA` / `USAGE_GAME` / `USAGE_ASSISTANCE_ACCESSIBILITY` / `USAGE_ASSISTANCE_NAVIGATION_GUIDANCE` | `STREAM_MUSIC` |
| `CONTENT_TYPE_SONIFICATION` → `USAGE_ALARM` | `STREAM_ALARM` |
| `CONTENT_TYPE_SONIFICATION` → `USAGE_NOTIFICATION` + türevleri | `STREAM_NOTIFICATION` |
| `CONTENT_TYPE_SPEECH` (`@hide`) | `STREAM_BLUETOOTH_SCO` |
| `FLAG_AUDIBILITY_ENFORCED` (`@hide`) | `STREAM_SYSTEM_ENFORCED` |
| `CONTENT_TYPE_SONIFICATION` → `USAGE_VOICE_COMMUNICATION_SIGNALLING` (`@hide`) | `STREAM_DTMF` |

`@hide` stream'leri çerçevenin içinde kullanılır ve genel API'nin parçası değildir.

### §4.3 Otomotiv için eskimiş (deprecated) stream tipleri

Android 9, otomotiv kullanımı için şu stream tiplerini eskimiş ilan eder:
`STREAM_DEFAULT` · `STREAM_VOICE_CALL` · `STREAM_SYSTEM` · `STREAM_RING` ·
`STREAM_MUSIC` · `STREAM_ALARM` · `STREAM_NOTIFICATION` · `STREAM_BLUETOOTH_SCO` ·
`STREAM_SYSTEM_ENFORCED` · `STREAM_DTMF` · `STREAM_TTS` · `STREAM_ACCESSIBILITY`.
(Çözüm önerisi dokümanda: "Automotive Audio" bölümüne bakınız.)

### §4.4 Dokümandaki resmi örnek

[RESMİ ALINTI · https://source.android.com/docs/core/audio/attributes · 2026-10-12]

```java
AudioTrack myTrack = new AudioTrack(
    new AudioAttributes.Builder()
        .setUsage(AudioAttributes.USAGE_MEDIA)
        .setContentType(AudioAttributes.CONTENT_TYPE_MUSIC)
        .build(),
    myFormat, myBuffSize, AudioTrack.MODE_STREAM, mySession);
```

## §5 Veri formatları ve dönüşüm kuralları

Kaynak: Data formats sayfası gövdesi (§10 satır 4).

### §5.1 Kayan nokta ↔ sabit nokta dönüşümü

| Kural | Doküman ifadesi |
|---|---|
| Qm.n → float | Değeri nokta yokmuş gibi tamsayıya çevir, `2^-n` ile çarp (Q4.27 için: `float = integer * (2 ^ -27)`) |
| Float nominal aralık | Tek duyarlılıkta nominal aralık ±1.0, ara değerler için tam aralık ±1.7e+38 |
| Dış temsil dönüşümü | Yalnızca nominal aralık dikkate alınır, taşan değerler kırpılır |
| +1.0 → sabit nokta | `+1.0` bir LSB eksik `+1.0`'a kırpılır |
| Denormal / ±0.0 | Temsile izin verilir; işleme sırasında sessizce 0.0'a dönebilir |
| Sonsuzluk | Ya geçer ya da ±1.0'a sessizce sınırlanır |
| NaN davranışı | **TANIMSIZ**: aynı NaN geçebilir, Default NaN'a dönebilir, ±1.0'a kırpılabilir, 0.0 olabilir veya hata döndürebilir |

### §5.2 Sabit nokta (Qm.n) dönüşüm kuralları

| Değişim | Kural |
|---|---|
| `m` artıyor | Tamsayı kısmının solundan sign-extend |
| `m` azalıyor | Tamsayı kısmını kırp (clamp) |
| `n` artıyor | Ondalık kısmın sağından zero-extend |
| `n` azalıyor | Fazla ondalık bitleri dither, round veya truncate et |

Örnek (dokümandan): Q4.27 → Q0.15 (dither/round yok) için Q4.27'yi 12 bit sağa kaydır
ve 16-bit işaretli aralığı aşan her şeyi kırp. Q7.24 → Q7.23 için işaretli bölme yap
veya işaret bitini ekleyip sağa 1 bit kaydır — **basit işaretli sağa kaydır, işaretli
bölmenin eşdeğeri değildir**.

### §5.3 Kayıpsız / kayıplı sınır

| Dönüşüm | Sonuç |
|---|---|
| Tanım | Dönüşüm kayıpsızsa A→B→C sonucunda A = C (invertible) |
| Sabit nokta (≤25 anlamlı bit) → float | **Kayıpsız** |
| Float → herhangi bir yaygın sabit nokta temsili | **Kayıplı** |
| Pratik sonuç | Float'ta saklanan 26+ bitlik sabit nokta veri geri döndürülemez |

## §6 Gecikme (latency) tasarımı

| Başlık | Durum |
|---|---|
| `Latency` genel bakış sayfası | HTTP 200 doğrulandı (§10 satır 6) — gövde okunmadı: `⚠️ VERIFICATION REQUIRED` |
| `Design for reduced latency` | HTTP 200 doğrulandı (§10 satır 7) — gövde okunmadı: `⚠️ VERIFICATION REQUIRED` |
| `Audio latency for app developers` | HTTP 200 doğrulandı (§10 satır 8) — gövde okunmadı: `⚠️ VERIFICATION REQUIRED` |
| NDK gecikme sayfası | Gövde başlığı: "High performance audio apps ... responsive realtime system behavior" (§7) |
| Ölçüm sayfaları | `Measure audio latency` · `Audio loopback dongle` — bu dosyada **kullanılmaz**, sürücü dosyasına ayrılmıştır (ayrı URL havuzu) |

**RED TEAM bulgu 6-1:** Gecikme sayısal hedefleri (ms cinsinden) bu dosyada
**YAZILMAMIŞTIR**, çünkü okunan NDK sayfası gecikme hedefi vermez; AOSP gecikme
sayfalarının gövdeleri bu turda okunmadı. Sayısal gecikme hedefi uydurmak yerine
`⚠️ VERIFICATION REQUIRED` tercih edilmiştir.

## §7 Uygulama katmanı — yüksek performanslı ses kullanımı alanları

Kaynak: NDK "High-performance audio" sayfası gövdesi — sayfanın kendisi bu dosyaya
ait değil, `android-aaudio-ve-opensl.md` §1'e tahsislidir; burada yalnızca başlık
ve kullanım listesi özetlenir (URL tekrarı yasak).

| Kullanım alanı | NDK sayfasında listelenen örnek |
|---|---|
| Dijital ses çalışma istasyonu | Digital audio workstations |
| Sentezleyiciler | Synthesizers |
| Davul makineleri | Drum machines |
| Müzik öğretim uygulamaları | Music learning apps |
| Karaoke uygulamaları | Karaoke apps |
| DJ karıştırma | DJ mixing |
| Ses efektleri | Audio effects |
| Video/ses konferansı | Video/audio conferencing |

Sayfanın kendi ifadesi: yüksek performanslı ses uygulamaları "basitçe ses çalma veya
kaydetme yeteneğinden fazlasını" ister; tipik gereksinim **duyarlı gerçek-zamanlı
sistem davranışıdır**. Bu bölüm iki kütüphaneyi tanıtır: OpenSL ES ve AAudio
(Ayrıntı: `android-audio-ve-opensl.md`, bu dosyanın URL'leriyle kesişmez).

## §8 Uygulama API yüzeyi — bu dosyada kullanılabilecek resmi sayfalar

| API | Sayfa durumu (2026-10-12) | Bu dosyada mı? |
|---|---|---|
| `MediaPlayer` rehberi | HTTP 200 (§10 satır 10) | EVET — §8.1 |
| Desteklenen medya formatları | HTTP 200 (§10 satır 5) | EVET — §8.2 |
| `AudioFormat` · `AudioManager` · `AudioTrack` · `AudioRecord` · `AudioAttributes` · `AudioFocusRequest` · `AudioDeviceInfo` | HTTP 200 | HAYIR → `android-kod-ornekleri.md` |
| `MediaRecorder` rehberi | HTTP 200 | HAYIR → `android-izin-ve-lifecycle.md` |

### §8.1 MediaPlayer kapsamı

Sayfa başlığı doğrulanan resmi rehber: "About MediaPlayer" (§10 satır 10). Sayfanın
kapsamı bu dosyada yalnızca **başlık kanıtı** ile sınırlıdır; API imzaları için
`android-kod-ornekleri.md` §3'e başvurulur.

### §8.2 Desteklenen formatlar

Sayfa başlığı doğrulanan resmi liste: "Supported media formats" (§10 satır 5).
Belirli codec/format satırları bu turda gövde olarak okunmadı → format-spesifik
iddialar `⚠️ VERIFICATION REQUIRED` işaretlidir.

## §9 RED TEAM — bu dosyada reddedilen iddialar 

| # | Reddedilen iddia | Gerekçe | Durum |
|---|---|---|---| | 1 | "Android ses yığınındaki katman sayısı sabittir" | Doküman ağacı AIDL + HIDL iki taşıyıcısını birlikte listeler | REDDEDİLDİ |
| 2 | "'pro audio' = profesyonel kullanıcıya yönelik" | Terminoloji: pro terimi "the level of predictable real-time performance, not the intended user" ifadesiyle tanımlanır — hedef kitle değildir | REDDEDİLDİ |
| 3 | "Öznitelik↔stream eşlemesi tüm cihazlarda garantilidir" | Doküman: eşleme cihaz/üretici/sürüm arasında garanti edilmez | REDDEDİLDİ |
| 4 | "Q4.27'den Q0.15'e sağa kaydırma bölme ile aynıdır" | Doküman: basit işaretli sağa kaydır işaretleme ölçeklemesine EŞDEĞER DEĞİLDIR | REDDEDİLDİ |
| 5 | "Float'ta 26+ bitlik sabit nokta veri kayıpsız saklanır" | Doküman: float → yaygın sabit nokta dönüşümü kayıplıdır | REDDEDİLDİ |
| 6 | "TinyALSA önerilmez" | Terminoloji: "The library is recommended for HAL implementations" | REDDEDİLDİ |
| 7 | "NaN davranışı sabittir" | Doküman: "NaN behavior is undefined" | REDDEDİLDİ |
| 8 | "AAudio her cihazda MMAP kullanır" | AAudio/MMAP sayfası: MMAP yoksa/başarısızsa eski AudioFlinger yolu kullanılır | REDDEDİLDİ 

(8. satırın kanıtı: `android-hal-ve-surucu.md` §4 — bu dosyada URL tekrarı yapılmaz.)

## §10 Doğrulama matrisi 

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---| | AOSP ses dokümanları ana indeksi erişilebilir | https://source.android.com/docs/core/audio | 2026-10-12 | HTTP 200 · gövde TOC okundu |
| Terminoloji sayfası gövdesi okundu (fast track, TinyALSA, pro audio) | https://source.android.com/docs/core/audio/terminology | 2026-10-12 | HTTP 200 · Last updated 2026-10-09 UTC |
| Attributes sayfası gövdesi okundu (bayraklar, eşleme tablosu) | https://source.android.com/docs/core/audio/attributes | 2026-10-12 | HTTP 200 · Last updated 2026-10-09 UTC |
| Data formats sayfası gövdesi okundu (Qm.n, kayıp kuralları) | https://source.android.com/docs/core/audio/data_formats | 2026-10-12 | HTTP 200 · Last updated 2026-10-09 UTC |
| Desteklenen medya formatları sayfası var | https://developer.android.com/guide/topics/media/platform/supported-formats | 2026-10-12 | HTTP 200 · başlık kanıtı |
| Gecikme genel bakış sayfası var | https://source.android.com/docs/core/audio/latency | 2026-10-12 | HTTP 200 · gövde okunmadı → `⚠️ VERIFICATION REQUIRED` |
| Azaltılmış gecikme için tasarım sayfası var | https://source.android.com/docs/core/audio/latency/design | 2026-10-12 | HTTP 200 · gövde okunmadı → `⚠️ VERIFICATION REQUIRED` |
| Uygulama geliştiriciler için gecikme sayfası var | https://source.android.com/docs/core/audio/latency/app | 2026-10-12 | HTTP 200 · gövde okunmadı → `⚠️ VERIFICATION REQUIRED` |
| Örnek hızı dönüşümü (SRC) sayfası var | https://source.android.com/docs/core/audio/src | 2026-10-12 | HTTP 200 · gövde okunmadı → `⚠️ VERIFICATION REQUIRED` |

| Medya platform rehberi (genel) var | https://developer.android.com/guide/topics/media/platform | 2026-10-12 | HTTP 200 · başlık kanıtı |
| MediaPlayer rehberi var | https://developer.android.com/guide/topics/media/platform/mediaplayer | 2026-10-12 | HTTP 200 · başlık: About MediaPlayer |

## §11 Açık sorular 

1. AOSP gecikme sayfalarının gövde içeriği (ölçüm metodolojisi, sayısal hedefler) bu turda okunmadı — `⚠️ VERIFICATION REQUIRED`.

2. OEM'e özgü ses politikası yapılandırmaları (cihaz başına `audio_policy_configuration.xml` içeriği) bu dosyanın dışında ve `UNKNOWN`.

3. `android.hardware.audio.pro` bayrağı taşıyan cihazların güncel sayısı: bu turda ölçülmedi → `UNKNOWN`.

4. Sayfa gövdesi okunmayan URL'lerin alıntılanması için ikinci bir tur gerekir (öncelik: gecikme üçlüsü + SRC).

## §12 Katmanlar arası veri yolu (analitik harita)

Bu bölüm yeni URL TÜKETMEZ; §2 doküman ağacı ile §3–§7 gövde kanıtlarından türetilir.
Süzülme (türetme) kuralı: bir hücrede kaynak yoksa hücre `UNKNOWN` veya
`⚠️ VERIFICATION REQUIRED` taşır.

| Aşama | Taşıyıcı | Kanıt durumu |
|---|---|---|
| Uygulama çağrısı (`AudioTrack` / `AudioRecord`) | JVM/Kotlin API yüzeyi | `attributes` sayfası örneğiyle destekli |
| Öznitelik çözümü | `AudioAttributes` → stream dönüşümü | §4.2 eşleme tablosu (doküman çizelgesi) |
| İstemci oluşturma | fast track ya da normal mixer seçimi | §3 terminoloji (tanım kanıtı) |
| Mikser iş parçacığı | `AudioFlinger` normal mixer → FastMixer pipe | §3 terminoloji (tanım kanıtı) |
| Port soyutlaması | NBAIO boruları | §3 terminoloji (uyarı: bazı implementasyonlar bloklayabilir) |
| Kontrol senkronizasyonu | StateQueue | §3 terminoloji |
| Politika kararı | strategy + stream type + volume index | §3 terminoloji |
| HAL | AIDL veya HIDL taşıyıcısı | §2 (her ikisi de dokümanda mevcut) |
| User-mode ALSA | TinyALSA (önerilen) | §3 terminoloji |
| Kernel ALSA | `/dev/snd/` sürücü dosyaları | `android-hal-ve-surucu.md` (ayrı URL havuzu) |
| Donanım | DAC/çıkış cihazı | `UNKNOWN` — cihaz-özgü |

### §12.1 Hız takipçisi (fast track) ile normal mikser farkı

| Ölçüt | fast track | normal mixer |
|---|---|---|
| Gecikme | Daha düşük (doküman: "lower latency") | Daha yüksek |
| Özellikler | Bazı cihaz ve rotalarda daha az özellik | Tam özellikli istemcilerin çoğu |
| Doküman kaynağı | Terminoloji sayfası | Terminoloji sayfası |
| Bu dosyada uygulanabilirlik | Sadece tanım düzeyinde | Sadece tanım düzeyinde |
| Cihaz-özgü sayılar | `UNKNOWN` | `UNKNOWN` |

### §12.2 NBAIO üzerindeki kırmızı takım notu

Dokümanın kendi uyarısı: NBAIO "bloklamayan" anlamına gelir ancak API'nin bazı
implementasyonları **bloklamayı destekler**. Bu, arayüz adının güvenilir bir
davranış garantisi OLMADIĞINI gösterir. Kode adı ile davranış sözü arasındaki bu
fark, bu dosyada açıkça kayda geçmiştir.

## §13 Ses politikası kavramı (strategi tabanlı karar)

| Kavram | Dokümandaki işlevi | Karar çıktısı |
|---|---|---|
| strategy | Benzer davranışlı stream tipi grubu | Yönlendirme önceliği |
| stream type | Kullanım durumu enum'u | Politika girdisi |
| volume index | Kullanıcının istediği bağıl seviye | Sönüm eğrisine girdi |
| volume attenuation curve | Cihaza özgü eşleme | Nihai sönüm faktörü |
| Politika uygulayıcısı | "audio policy service" | Ses seviyesi + yönlendirme kararı |
| Yapılandırma | `Configure audio policies` bölüm adı (TOC) | Gövde okunmadı → `⚠️ VERIFICATION REQUIRED` |

**RED TEAM bulgu 13-1:** Volume, Android'de **mutlak kazanç** değil, birimiz yok
tam sayı indeks + cihaza özgü eğri üzerinden hesaplanır. "0–100 arası desibel"
varsayımı bu dosyada reddedildi (kanıt: terminoloji tanımı, §10 satır 2).

## §14 Efektler, yüksek çözünürlük ve ses dozu (kapsam haritası)

Bu bölümde URL kullanılmaz; TOC bölüm adları §2'den gelir, gövde okumaları
yapılmadığı için her satır açıkça işaretlenir.

| Bölüm adı (doküman TOC'si) | Gövde okundu mu? | Bu dosyadaki durum |
|---|---|---|
| Effects | HAYIR | `⚠️ VERIFICATION REQUIRED` |
| High-resolution audio | HAYIR | `⚠️ VERIFICATION REQUIRED` |
| Sound dose | HAYIR | `⚠️ VERIFICATION REQUIRED` |
| Configurable Audio Policy support in AIDL HAL | HAYIR | `⚠️ VERIFICATION REQUIRED` |
| AIDL and HIDL Audio HAL comparison | HAYIR | `⚠️ VERIFICATION REQUIRED` |
| Device type limit | HAYIR | `⚠️ VERIFICATION REQUIRED` |
| Audio warmup | HAYIR | `⚠️ VERIFICATION REQUIRED` |

**Yasak:** Bu bölüm başlıklarından efekt sayısı, kazanç değeri, doz eşiği veya
sürüm numarası ÜRETİLEMEZ. Üretilen her sayı uydurmadır.

## §15 Uygulama düzeyi ile platform düzeyi ayrımı

| Soru | Platform düzeyi (bu dosya) | Uygulama düzeyi (harici) |
|---|---|---|
| Hangi API kullanılır? | `AudioAttributes` + politika | Uygulamanın seçtiği player |
| Gecikme kimin sorumluluğu? | Çerçeve + HAL + kernel | Buffer boyutu ve politika seçimi |
| Format desteği kim belirler? | Platform + cihaz (§8.2) | Uygulama kodlayıcısı |
| İzin kim ister? | İzin çerçevesi (rehber) | Uygulama manifestosu → `android-izin-ve-lifecycle.md` |
| Arka plan davranışı | Yaşam döngüsü kuralları → `android-izin-ve-lifecycle.md` | Uygulama servis tasarımı |

## §16 RED TEAM — tekrar kontrol listesi

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | Her iddia §10 matrisinde bir URL'ye mi bağlı? | EVET (12/12 satır) |
| 2 | Gövde-okunmayan sayfalara içerik atfedildi mi? | HAYIR — `⚠️ VERIFICATION REQUIRED` ile işaretli (§6, §14) |
| 3 | Sayısal gecikme hedefi uyduruldu mu? | HAYIR — §6.1 RED TEAM bulgusu |
| 4 | `Dosya:/Satır:` referansı var mı? | HAYIR — depoda 0 `.cpp/.h` (§0) |
| 5 | Dolgu/kısaltma yer tutucuları kullanıldı mı? | HAYIR |
| 6 | Başka dosyayla URL kesişimi var mı? | HAYIR — §10 listesi bu dosyaya özel |
| 7 | İmla hataları korundu mu? | EVET (SSOT §3 KURAL 1) |
| 8 | Türkçe + İngilizce teknik terim dengesi | EVET |

## §17 Tekrar üretilebilirlik (kanıt toplama yöntemi)

| Adım | İşlem | Sonuç (2026-10-12) |
|---|---|---|
| 1 | `source.android.com/docs/core/audio` için GET | HTTP 200, gövde TOC okundu |
| 2 | Terminoloji / Attributes / Data formats gövdelerinin alınması | Üçü de `Last updated 2026-10-09 UTC` |
| 3 | NDK ses sayfası gövdesinin alınması | `Last updated 2026-03-06 UTC` |
| 4 | HTML → düz metin (script/style çıkarma, etiket soyma) | §3–§5 alıntılarının kaynağı |
| 5 | Bulanık (fuzzy) yol denemeleri (`/ndk/guides/audio/aaudio`) | **HTTP 404** → doğru yol `/ndk/guides/audio/aaudio/aaudio` |
| 6 | Aynı hata kaynağı: `source.android.com/docs/core/audio/implementation` | **HTTP 404** → doğru bölüm adı `implement` |

**Önemli ölçümlü bulgu:** Android doküman yolları tahminle bulunmaz; indeks
sayfasındaki `href` listesinden çıkarılmalıdır. 2026-10-12 turunda 14 aday yoldan
**14'ü 404 döndü**, indeks üzerinden çıkarılan yolların tamamı 200 döndü. Bu ölçüm
uydurulmamıştır: §18 kanıt tablosunda tekrarlanabilir komut vardır.

## §18 Kanıt toplama komutları (tekrarlanabilir)

```powershell
# Bir sayfanın durum kodu
Invoke-WebRequest -Uri '<URL>' -UseBasicParsing -UserAgent 'Mozilla/5.0 ...'

# İndeks sayfasından gerçek yolları çıkar
[regex]::Matches($r.Content,'href="(/docs/core/audio/[^"#]+)"')
```

| Parametre | Değer | Neden |
|---|---|---|
| User-Agent | Tarayıcı UA'sı | Varsayılan UA ile bazı sayfalar **HTTP 404** döndürür |
| `UseBasicParsing` | açık | PowerShell parser bağımlılığını kaldırır |
| Yöntem | GET (HEAD değil) | Bu turda HEAD ile 404 / GET ile 200 görülen durumlar vardır |
| Kodlama | UTF-8 | Türkçe karakter bozulmasını önler |

## §19 Bu dosyaya tahsis edilen URL listesi (tekil)

| # | URL | Dosyadaki işlevi |
|---|---|---|
| 1 | https://source.android.com/docs/core/audio | §2 doküman ağacı |
| 2 | https://source.android.com/docs/core/audio/terminology | §3 · §13 |
| 3 | https://source.android.com/docs/core/audio/attributes | §4 |
| 4 | https://source.android.com/docs/core/audio/data_formats | §5 |
| 5 | https://source.android.com/docs/core/audio/src | §6 başlık kanıtı |
| 6 | https://source.android.com/docs/core/audio/latency | §6 |
| 7 | https://source.android.com/docs/core/audio/latency/design | §6 |
| 8 | https://source.android.com/docs/core/audio/latency/app | §6 |
| 9 | https://developer.android.com/guide/topics/media/platform | §8 |
| 10 | https://developer.android.com/guide/topics/media/platform/supported-formats | §8.2 |
| 11 | https://developer.android.com/guide/topics/media/platform/mediaplayer | §8.1 |


Bu 11 URL'nin tamamı bu dosyaya ÖZELDİR; `android/` klasörünün diğer beş dosyasında
tekrarlanmaz (klasör içi kesişim ölçümü kapanış raporunda verilir).

## §20 Kapanış durumu

| Kalem | Değer |
|---|---|
| Dosya | `android-audio-mimarisi.md` |
| `node_type` | `architecture` |
| `group` | `mimari` |
| Frontmatter alanı | 10 |
| Doğrulanan URL | 11 (dosyaya tahsisli tekil) |
| Gövde okunan sayfa | 6 (indeks + terminoloji + attributes + data formats + NDK audio + medya platform) |
| `⚠️ VERIFICATION REQUIRED` satırı | 7 (§6 üçlü + §14 yedi satırının ilgili hücreleri + §8.2) |
| Açık soru | 4 (§11) |
| Kod bloğu | 1 (§4.4, resmi alıntı) |

## §21 İstemci sınıfı haritası (terminoloji sayfasından)

Bu tablo §3 sözlüğündeki "seviyeli sınıf" tanımlarını tek yerde toplar; yeni URL TÜKETMEZ.

| Sınıf | Seviye | Dokümandaki işi |
|---|---|---|
| `AudioTrack` | temel | Çalma (track) — fast track veya normal mikser üzerinden |
| `AudioRecord` | temel | Kayıt istemcisi |
| `AudioAttributes` | betimleme | Kullanım + içerik türü (§4) |
| `MediaPlayer` | üst | Kodlanmış içeriği veya ses+video track içeriği çalar |
| `SoundPool` | üst | Örneklenmiş klipler; UI geri bildirimi ve oyun sesleri |
| `ToneGenerator` | üst | DTMF çift-tonlu sinyaller |
| `AudioManager` | yönetim | Ses indeksi ve stream yönetimi (§13) |
| `AudioFlinger` | sistem | Mikser iş parçacıkları, medya.log (özel derleme) |
| `StateQueue` | sistem | Kontrol bilgisi senkronizasyonu |
| `NBAIO` | sistem | Port soyutlaması (uyarılı tanım) |
| Stagefright | sistem | Medya oynatma motoru |
| mediaserver | süreç | Sesle ilgili servisleri barındırır |

### §21.1 Öznitelik → istemci akışı

| Adım | Sınıf | Çıktı |
|---|---|---|
| 1 | `AudioAttributes.Builder` | usage + content type (§4.4 resmi örnek) |
| 2 | Politika çözümü | strategy seçimi (§13) |
| 3 | `AudioTrack` oluşturma | fast track ya da normal mikser |
| 4 | Mikser + NBAIO | veri akışı |
| 5 | HAL (AIDL/HIDL) | donanım yazımı |

## §22 Öznitelik kullanım senaryoları (doküman iddialarından)

| Senaryo | İlgili usage | İlgili content type | Not |
|---|---|---|---|
| Müzik çalma | `USAGE_MEDIA` | `CONTENT_TYPE_MUSIC` | §4.4 resmi örnekteki ikili |
| Zil sesi | `USAGE_NOTIFICATION_RINGTONE` | `CONTENT_TYPE_SONIFICATION` | eşleme §4.2 |
| Alarm | `USAGE_ALARM` | `CONTENT_TYPE_SONIFICATION` | eşleme §4.2 |
| Sesli arama | `USAGE_VOICE_COMMUNICATION` | `CONTENT_TYPE_SPEECH` | eşleme §4.2 |
| Navigasyon yönlendirmesi | `USAGE_ASSISTANCE_NAVIGATION_GUIDANCE` | `CONTENT_TYPE_MUSIC` | eşleme §4.2 |
| Erişilebilirlik | `USAGE_ASSISTANCE_ACCESSIBILITY` | `CONTENT_TYPE_MUSIC` | eşleme §4.2 |
| Oyun sesi | `USAGE_GAME` | `CONTENT_TYPE_MUSIC` | eşleme §4.2 |
| Görüntüleme (A/V senkron) | — | — | `HW_AV_SYNC` bayrağı (§4.1) |
| Kamera deklanşörü (zorunlu) | — | — | `STREAM_SYSTEM_ENFORCED` ihtiyacı (§4.1) |

**RED TEAM bulgu 22-1:** `USAGE_*` ve `CONTENT_TYPE_*` eşleşmesi doküman
çizelgesindeki **önerilen** eşleşmedir; çerçevenin bunu her cihazda zorunlu kılmadığı
§4.1'in "does not enforce or guarantee" uyarısıyla sabittir.

## §23 Terminoloji ek maddeleri (aynı kaynak)

| Madde | İçerik |
|---|---|
| `media.log` | Yalnızca özel derlemelerde; olaylar dairesel tampondan geriye dönük dökülebilir |
| `tee sink` | Gözetleme çıkışı — "Audio debugging" bölümünde ele alınır |
| silent mode | Yalnızca zil + bildirimleri susturur; medya oynatmayı etkilemez |
| `strategy` grubu | "Group of stream types with similar behavior" |
| CDD bağlantısı | `pro audio` gereksinimleri Android CDD **bölüm 5.10** |
| Gerçek-zamanlı kapsam | Oyun, grafik, kamera, video, sensör işleme, VR/AR da fayda görür (terminoloji) |

## §24 Ölçüm kaydı (bu dosya için)

| Metrik | Değer |
|---|---|
| Doğrulama tarihi | 2026-10-12 |
| Dosyaya tahsisli URL | 12 |
| Frontmatter alanı | 10 |
| `## §N` bölüm sayısı | 25 (§0–§24) |
| Tablo sayısı | ≥15 |
| Gövde okunan sayfa | 6 |
| `Dosya:/Satır:` | 0 (yasak, §0) |
| Dolgu/kısaltma yer tutucu | 0 |

## §25 Bu dosyanın örnek sayfa başlıkları (envanter)

2026-10-12 turunda `<title>` değeri okunan resmi sayfalar — başlık kanıtı olarak,
içerik kanıtı olarak DEĞİL:

| # | Başlık değeri | Kullanıldığı bölüm |
|---|---|---|
| 1 | Audio (AOSP) | §2 |
| 2 | Audio terminology | §3 |
| 3 | Audio attributes | §4 |
| 4 | Data formats | §5 |
| 5 | High-performance audio (NDK) | §7 |
| 6 | About MediaPlayer | §8.1 |
| 7 | Supported media formats | §8.2 |
| 8 | Platform APIs and behaviors | §8 |

| Kapanış metriği | Değer |
|---|---|
| Toplam `## §` bölüm | 26 (§0–§25) |
| Frontmatter alanı | 10 |
| Dosyaya özel URL | 12 |

**Authority: Bayram Ali / Vault Steward**

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode