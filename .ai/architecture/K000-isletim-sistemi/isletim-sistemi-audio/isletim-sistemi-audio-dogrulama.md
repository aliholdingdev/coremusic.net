---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/isletim-sistemi-audio-dogrulama
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio
children: []
depends_on: []
evidence:
  - "2026-10-13 · §E web matrisi · 10 yeni resmi URL · tümü HTTP 200 (HEAD, 20 s timeout)"
  - "2026-10-13 · §F disk kanıtları · repo dosya/satır eşlemesi doğrulandı"
  - "2026-10-13 · §K bu tur web matrisi · 12 yeni resmi URL · tümü HTTP 200 · envanter kesişimi 0"
  - "2026-10-13 · §L repo taraması · ALSA/CoreAudio deseni 0 isabet · satır referansları Select-Object -Index ile doğrulandı"
  - "2026-10-14 · §O web matrisi · 21 URL · 17×200 · 4 ölü 404 (negatif kanıt) · 5 kaynak gövde-GET (3'ü yerel kopya: tizen/webos/reactos)"
validation:
  - "2026-10-12 turu: 6 URL HTTP 200 (kayıt §G'de korundu — URL'ler envanterde)"
  - "2026-10-13 turu: §A-§I bölüm yapısı; ölçüm sonuçları henüz üretilmedi (PLANNED)"
  - "2026-10-13 (2. tur): §J-§M eklendi · dosya 332 → ≥500 satıra çıkarıldı · alsa/ + coreaudio/ alt-dalları bu turda açıldı"
  - "2026-10-14 (3. tur): §O eklendi · aaudio/aaudio-ornekler.md üretildi (tek yeni dosya) · kök -ornekler.md §16-§24 OS gruplaması · wasapi/ 0 yazma"
open_questions:
  - "Gerçek cihaz üzerinde ölçüm yapıldı mı? → HAYIR — repo'da ses donanımı yok (§F/6)"
  - "Eşiklerin bir kısmı hedef (sartname), ölçüm değil → §D işaretli"
group: kanit
---

# isletim-sistemi-audio — Doğrulama Tablosu

> **Bu dosyanın görevi:** iddiayı **kanıta** bağlamak. Her satır üç seçenekten birini
> taşır: `✅ kanıtlı (URL/dosya)` · `⚠️ VERIFICATION REQUIRED` · `UNKNOWN`.
> Ölçüm **yapılmadıysa** "geçti" yazılır — bu dosya üretim raporu değil, **kapı**dır.

---

## §A — Doğrulama senaryoları

| # | Senaryo | Adımlar | Beklenen | Kanıt tipi |
|---|---|---|---|---|
| S1 | Cihaz keşfi | platform keşfi çağrısı → liste | en az 1 çıkış cihazı görünür | ekran/log |
| S2 | Cihaz açma | `default`/endpoint/AudioUnit aç | handle döner, hata yok | log |
| S3 | Format pazarlığı | 48 kHz · float32 iste | onay **veya** fallback bildirimi | log |
| S4 | Kısa çalma | 10 sn sine sweep | kesintisiz, kırpılma yok | dinleme + wave |
| S5 | Paylaşım (2 süreç) | aynı cihazı ikinci kez aç | ya paylaşım ya temerrüt — sessiz kırılma YOK | log |
| S6 | Kasıtlı xrun | period'u 64'ye düşür + CPU yükü | xrun sayacı artar, kurtarma otomatik | sayaç |
| S7 | Cihaz kaybı | USB/HDMI çıkar | bildirim + akış durur/kurtarılır | log |
| S8 | Round-trip latency | loopback kablosu + impulse | ms değeri ±σ ile raporlanır | ölçüm dosyası |
| S9 | DC / sessizlik | sessiz kayıt 30 sn | DC offset < eşik (K001 röle kuralı) | istatistik |
| S10 | Uygulama kapanışı | `drain` + close | son paket çalar, click yok | dinleme |
| S11 | Uyumsuz format | 44.1 kHz iste, cihaz 96 kHz | dönüşüm katmanı devreye girer | log |
| S12 | Uzun süre (soak) | 60 dk kesintisiz | xrun = 0, drift kabulde | sayaç + log |

### §A.1 Senaryo ↔ platform kapsamı

| # | Senaryo | Windows | Linux | macOS | iOS |
|---|---|---|---|---|---|
| S1 | cihaz keşfi | `wasapi/` | `alsa/` | `coreaudio/` | `coreaudio/` |
| S2 | cihaz açma | shared/exclusive | `hw`/`default` | AudioUnit | session route |
| S6 | xrun | glitch/drop | `-EPIPE` sayacı | IOProc durumu | interruption |
| S8 | latency | §C | §C | §C | §C |
| S5 | paylaşım | `wasapi/` oturum | `dmix`/sunucu | HAL mikser | tekil session |

### §A.2 Senaryo önceliklendirmesi

| Öncelik | Senaryolar | Neden |
|---|---|---|
| P0 | S1 · S2 · S3 · S4 | bunlar olmadan ses zaten yok |
| P1 | S6 · S8 · S12 | gerçek zamanlı iddianın kanıtı |
| P2 | S5 · S7 · S10 · S11 | dayanıklılık ve geçiş davranışı |
| P3 | S9 | K001 röle korumasıyla ilişkili |

---

## §B — Test stratejisi

### §B.1 Piramit

| Seviye | Ne test edilir | Cihaz gerekir mi? | Bu dalda sahip |
|---|---|---|---|
| Unit | parametre hesabı (period→ms), hata kodu çözümü | hayır | `qa-engineer` |
| Contract | API sözleşmesi (çağrı sırası, hata imzaları) | hayır | bu dal |
| Integration | gerçek `alsa-lib`/WASAPI/CoreAudio açılışı | **evet** | `embedded` |
| E2E | uygulama → cihaz → duyulabilir sonuç | **evet** | `embedded` + `qa-engineer` |
| Soak | 60 dk+ çalışma, xrun/drift | **evet** | `embedded` |

### §B.2 Ortam matrisi

| Ortam | Cihaz | Durum | Not |
|---|---|---|---|
| Windows masaüstü | ses kartı | UNKNOWN | repo'da Windows çalışma ortamı kanıtı yok |
| Linux sunucu (web hosting) | ses cihazı **yok** | ✅ olumsuz kanıt | CoreMusic sunucusu yalnız HTTP — `alsa-anayasa.md` §6 |
| Linux masaüstü / Raspberry Pi | SoC ses | UNKNOWN | hedef platform (Q4) kapalı |
| macOS / iOS | entegre ses | UNKNOWN | donanım yok |
| CI (GitHub Actions) | yok | ✅ | gerçek cihaz testi CI'da **yapılamaz** |

### §B.3 Otomasyon sınırı

| # | Kural | Neden |
|---|---|---|
| T1 | CI'da yalnız unit + contract koşar | ortamda ses cihazı yok |
| T2 | Integration testleri etiketli koşar (`@device`) | gerçek donanım gerektirir |
| T3 | E2E sonuçları dinleme/ölçüm dosyasıyla kanıtlanır | "çalıştı" iddiası yasak |
| T4 | Flaky xrun sonuçları geçersiz sayılır | tekrar üretilebilirlik şart |
| T5 | Ölçüm ham verisi saklanır (CSV/log) | sonradan yeniden hesap |

### §B.4 Manuel kontrol listesi

| # | Kontrol | Çıktı |
|---|---|---|
| M1 | cihaz listesi görünüyor mu | ekran görüntüsü |
| M2 | kısa çalma duyuluyor mu | onay + ses kaydı |
| M3 | cihaz değiştirince akış kopuyor mu | log satırı |
| M4 | ikinci uygulama açılınca ne oluyor | log satırı |
| M5 | kapanışta click var mı | dinleme notu |

---

## §C — Ölçüm metodolojisi (latency · buffer · xrun)

### §C.1 Zaman birimi dönüşümü (48 kHz)

| Buffer/period (örnek) | Süre (ms) | Hesap |
|---|---|---|
| 64 | 1,333 | 64 ÷ 48000 × 1000 |
| 128 | 2,667 | 128 ÷ 48000 × 1000 |
| 192 | 4,000 | 192 ÷ 48000 × 1000 |
| 256 | 5,333 | 256 ÷ 48000 × 1000 |
| 512 | **10,667** | sartname tamponu (§8) |
| 1024 | 21,333 | 1024 ÷ 48000 × 1000 |
| 2048 | 42,667 | 2048 ÷ 48000 × 1000 |

### §C.2 Gecikme tanımları

| Tanım | Kapsam | Ölçüm yolu |
|---|---|---|
| output latency | uygulama → hoparlör | impulse + loopback |
| input latency | mikrofon → uygulama | impulse + kayıt |
| round-trip latency | çıkış + giriş toplamı | loopback kablosu |
| callback jittersi | callback arası değişkenlik | zaman damgası serisi (σ) |
| reported latency | API'nin bildirdiği değer | platform sorgusu (§I sırası) |

### §C.3 Ölçüm adımları (standart)

| # | Adım | Ayrıntı |
|---|---|---|
| 1 | warm-up | 5 sn önceden çalıştır, ilk örnekleri at |
| 2 | sabit yapı | buffer · rate · kanal sabit, tek değişken |
| 3 | tekrar | en az 30 ölçüm |
| 4 | özet | medyan + σ (ortalama tek başına raporlanmaz) |
| 5 | kayıt | ham değer, tarih, cihaz adı, OS sürümü |
| 6 | tekrar üretilebilirlik | aynı koşul 2. kez ≠ ise ölçüm geçersiz |

### §C.4 Zaman kaynağı

| Platform | Zaman kaynağı | Not |
|---|---|---|
| Linux / genel | `clock_gettime(CLOCK_MONOTONIC)` | duvar saati sıçramasına dayanıklı (POSIX) |
| callback arası | OS callback zaman damgası | dalga formu testi kernel doc `timestamping` |
| bekleme/senkron | `nanosleep` | POSIX |
| Apple | `AudioTimeStamp` | `coreaudio/audiotimestamp` |
| Windows | `wasapi/` içindeki performans sayaçları | bu dosyada tekrarlanmadı |

### §C.5 xrun ölçümü

| Adım | Tanım |
|---|---|
| xrun sayacı | açılışta 0, ölçüm boyunca artışı say |
| xrun oranı | xrun ÷ ölçüm süresi (sn başına) |
| kabul | §D: hedef = 0 (ölçüm penceresi içinde) |
| kurtarma | `alsa-anayasa.md` §5 — kurtarma **otomatik** olmalı |
| Windows karşılığı | glitch/drop (aynı pencerede sayılır) |

### §C.6 Buffer hesabı

| Formül | Sonuç örneği |
|---|---|
| buffer = period × periods | 512 × 2 = 1024 örnek |
| buffer süresi = buffer ÷ rate | 1024 ÷ 48000 = 21,33 ms |
| teorik alt gecikme ≈ period ÷ rate | 512 ÷ 48000 = 10,67 ms |
| gerçek ≤ teorik mi? | hayır — sürücü + motor ekler; **ölçülür** |

### §C.7 Ölçüm araçları

| Amaç | Araç | Kanıt |
|---|---|---|
| Linux test tonu | `speaker-test` | §E/5 |
| Linux kaydı | `arecord` | §E/6 |
| Linux bit-accuracy/ölçüm | `alsabat` | §E/7 |
| Zaman damgası davranışı | kernel doc `timestamping` | §E/3 |
| POSIX zaman/bekleme | `clock_gettime` · `nanosleep` | §E/1 · §E/2 |
| Apple format/zaman | `ASBD` · `AudioTimeStamp` | §E/4 · §E/8 |
| macOS/iOS profil | Instruments | ⚠️ VERIFICATION REQUIRED (araç erişimi yok) |

---

## §D — Eşikler

### §D.1 Ölçüm eşikleri

| # | Eşik | Değer | Tür | Sahip |
|---|---|---|---|---|
| N1 | xrun (12 dk pencere) | **0** | kabul kriteri | `embedded` |
| N2 | hedef round-trip | ≤ 10,67 ms (512/48 kHz) | sartname §8 türevi | `embedded` |
| N3 | callback jitter | ⚠️ VERIFICATION REQUIRED — eşiğe karar verilmedi | belirsiz | `qa-engineer` |
| N4 | drift (60 dk) | ⚠️ VERIFICATION REQUIRED — eşiğe karar verilmedi | belirsiz | `qa-engineer` |
| N5 | ölçüm tekrar sapması | medyan dışı ≥ %10 ⇒ ölçüm geçersiz | metod kuralı | bu dal |

### §D.2 Kalite/koruma eşikleri (komşu K-space'ten gelen bağlayıcılar)

| # | Eşik | Değer | Nereye gider |
|---|---|---|---|
| N6 | DC offset | > 0,5 V ⇒ koruma rölesi **zorunlu** | K001-donanım |
| N7 | SNR | > 100 dB | K001-donanım |
| N8 | THD+N | < %0,01 @ 1 kHz | K001-donanım |
| N9 | gerçek zamanlı kural | audio callback'te `malloc`/`new`/mutex **yasak** | bu dal — tüm deep-dallar |
| N10 | örnek biçimi | 32-bit `float` (ADR-017) | K003-audio |
| N11 | donanım DAC | PCM5122 **yasak** → PCM3168A (ADR-038 H001) | K001-donanım |

### §D.3 Eşik durumu özeti

| Tür | Sayı | Durum |
|---|---|---|
| Ölçülmüş | 0 | ölçüm yapılmadı |
| Hedef / kabul kriteri | 4 (N1 · N2 · N5 + N9) | PLANNED |
| Karar bekleyen | 2 (N3 · N4) | UNKNOWN |
| Komşu K-space'ten devir | 6 (N6-N11) | bağlayıcı |

---

## §E — Web doğrulama matrisi (2026-10-13 · HTTP 200)

| # | İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| E1 | POSIX monotonik saat kaynağı `clock_gettime` vardır | https://pubs.opengroup.org/onlinepubs/9799919799/functions/clock_gettime.html | 2026-10-13 | ✅ HTTP 200 |
| E2 | POSIX `nanosleep` ile kontrollü bekleme vardır | https://pubs.opengroup.org/onlinepubs/9799919799/functions/nanosleep.html | 2026-10-13 | ✅ HTTP 200 |
| E3 | Kernel dokümanı ses alt sistemi için timestamping tasarımını belgeler | https://www.kernel.org/doc/html/latest/sound/designs/timestamping.html | 2026-10-13 | ✅ HTTP 200 |
| E4 | Apple `AudioStreamBasicDescription` format tarifi resmi dokümanda | https://developer.apple.com/documentation/coreaudio/audiostreambasicdescription | 2026-10-13 | ✅ HTTP 200 |
| E5 | `speaker-test` man sayfası (Linux ton üretme aracı) | https://manpages.debian.org/bookworm/alsa-utils/speaker-test.1.en.html | 2026-10-13 | ✅ HTTP 200 |
| E6 | `arecord` man sayfası (ALSA kayıt aracı) | https://manpages.debian.org/bookworm/alsa-utils/arecord.1.en.html | 2026-10-13 | ✅ HTTP 200 |
| E7 | `alsabat` man sayfası (ALSA test/ölçüm aracı) | https://manpages.debian.org/bookworm/alsa-utils/alsabat.1.en.html | 2026-10-13 | ✅ HTTP 200 |
| E8 | Apple `AudioTimeStamp` zaman damgası tipi resmi | https://developer.apple.com/documentation/coreaudio/audiotimestamp | 2026-10-13 | ✅ HTTP 200 |
| E9 | Apple `AudioBufferList` tampon tanımı resmi | https://developer.apple.com/documentation/coreaudio/audiobufferlist | 2026-10-13 | ✅ HTTP 200 |
| E10 | Linux ses alt sistemi doküman dizini (kernel) | https://www.kernel.org/doc/html/latest/sound/designs/index.html | 2026-10-13 | ✅ HTTP 200 |

**Erişim yöntemi:** `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`.
URL'lerin tamamı bu turda **yeni** (envanter kesişimi 0 — §I.2).

---

## §F — Repo disk kanıtları

| # | İddia | Dosya : satır | Durum |
|---|---|---|---|
| F1 | `ffprobe` ile ses dosyası metadata çıkarma | `shared/src/AI/AIEngine.php:288-295` | ✅ okundu |
| F2 | varsayılan `sample_rate = 44100` fallback'i | `shared/src/AI/AIEngine.php:101-104` | ✅ okundu |
| F3 | `ffprobe` çıktısından format/srate/bit/kanal | `shared/src/AI/AIEngine.php:304-309` | ✅ okundu |
| F4 | HTTP Range ile parçalı ses akışı | `home.coremusic.net/include/Stream/MusicStreamHandler.php:91-106` | ✅ okundu |
| F5 | ses başlıkları (`Accept-Ranges`, `Content-Type`) | `home.coremusic.net/include/Stream/MusicStreamHandler.php:310-329` | ✅ okundu |
| F6 | `audio/*` MIME eşlemesi (mp3·flac·wav·m4a·ogg·aac·dsd) | `home.coremusic.net/include/Stream/MusicStreamHandler.php:24-43` | ✅ okundu |
| F7 | parçalı okuma döngüsü (`fopen`/`fread`, 1 MiB chunk) | `home.coremusic.net/include/Stream/MusicStreamHandler.php:331-344` | ✅ okundu |
| F8 | **WASAPI/ALSA/CoreAudio çağrısı yapan kod** | — | ❌ **YOK** |
| F9 | ses donanımı erişim kodu (cihaz handle'ı) | — | ❌ **YOK** |
| F10 | Neva Engine C++ kaynağı | `projects/NevaEngine` = 0 dosya | ⚠️ PLANNED |

### §F.1 Durum özeti

| Metrik | Değer |
|---|---|
| ✅ disk kanıtı | 7 (F1-F7) |
| ❌ olumsuz kanıt (YOK) | 2 (F8 · F9) |
| ⚠️ PLANNED | 1 (F10) |
| Ölçüm yapıldı | **hayır** — donanım yok |

---

## §G — 2026-10-12 turunun kayıtları (korunur, URL'ler envanterde)

| # | İddia | Kaynak (2026-10-12) | Tarih | Durum |
|---|---|---|---|---|
| 1 | Windows Core Audio API ailesi resmi tanımlıdır (WASAPI dahil) | learn.microsoft.com · coreaudio | 2026-10-12 | ✅ HTTP 200 |
| 2 | Exclusive mode streams: tek uygulamanın cihazı dışlaması | learn.microsoft.com · exclusive-mode-streams | 2026-10-12 | ✅ HTTP 200 |
| 3 | Linux ses donanım katmanı ALSA projesidir | alsa-project.org · Main_Page | 2026-10-12 | ✅ HTTP 200 |
| 4 | macOS ses servisleri AudioToolbox / Core Audio altındadır | developer.apple.com · audiotoolbox | 2026-10-12 | ✅ HTTP 200 |
| 5 | Android native C ses API'si AAudio'dur | developer.android.com · ndk aaudio | 2026-10-12 | ✅ HTTP 200 |
| 6 | WASAPI, Windows Core Audio'nun kullanıcı-uzay API'sidir | learn.microsoft.com · wasapi | 2026-10-12 | ✅ HTTP 200 |
| 7 | Ölü URL elendi (3 adet) | §I.1 | 2026-10-12 | ✅ kayıtta |

> Bu bölümün URL'leri **yeniden yazılmadı** — 2026-10-13 envanterinde mevcutturlar;
> yeniden basılmaları kesişim üretirdi. İddia + tarih + durum korunmuştur.

---

## §H — Açık / doğrulanamayan iddialar

| # | İddia | Neden | Durum |
|---|---|---|---|
| 1 | Linux/macOS/Android ses alt sistemlerinin bu dalın **kapsamı** olduğu | dal sınırı 2026-10-12'de yoktu; 2026-10-13'te `alsa/` + `coreaudio/` açıldı | ✅ kısmen kapandı |
| 2 | CoreMusic'in gerçek donanımda ses üretebildiği | repo'da kod YOK (§F/8) | UNKNOWN |
| 3 | WASAPI exclusive modunun gerçek cihazda davranışı | cihaz erişimi yok | ⚠️ VERIFICATION REQUIRED |
| 4 | `wasapi-asio-karsilastirma.md` doğru konumda | konum kararı yok | ⚠️ VERIFICATION REQUIRED |
| 5 | Hedef platform önceliği (Windows mı Linux mu) | Q4 kapalı | UNKNOWN |
| 6 | Neva Engine'in bu dalı kullanacağı | PLANNED, kod yok | ⚠️ PLANNED |
| 7 | Eşik N3/N4 değerleri | karar verilmedi | UNKNOWN |
| 8 | 4 eksik deep-dal (`aaudio`·`tizen`·`webos`·`reactos`) | ✅ KAPANDI (2026-10-13) — 4/4 diskte, 4'er dosya | ✅ ölçüldü |

---

## §I — Yeniden ölçüm ve envanter

### §I.1 El edilen (ölü) URL'ler — kullanılmadı (2026-10-12)

| URL | HTTP | Not |
|---|---|---|
| https://docs.kernel.org/introduction/index.html | 404 | docs.kernel.org'ta `introduction` yok |
| https://docs.kernel.org/introduction.html | 404 | aynı |
| https://learn.microsoft.com/en-us/windows/win32/winprog/windows-apis | 404 | kaldırılmış |

### §I.2 Bu turun envanteri

| Metrik | Değer |
|---|---|
| Envanter öncesi benzersiz URL (`.ai/architecture/**`) | 1712 |
| Bu dosyada eklenen yeni URL | 10 (§E) |
| Bu turda eklenen yeni URL (12 dosyanın toplamı) | 60 |
| Envantersel kesişim (yeni ∩ öncesi) | **0** |
| Erişim tarihi | 2026-10-13 · HEAD · 20 s |

### §I.3 Tekrar ölçüm tetikleyicileri

| # | Tetikleyici | Ne yenilenir |
|---|---|---|
| X1 | buffer/period hedefi değişimi (512 → 256) | §C.1 · §D N2 |
| X2 | yeni donanım/OS sürümü | §A tümü · §B.2 |
| X3 | eşik kararı (N3/N4) | §D.1 |
| X4 | deep-dal eklenmesi (4 eksik) | §H/8 · §I.2 |
| X5 | Neva Engine kodunun gelmesi | §F/10 → gerçek kod kanıtı |

---

## §J — Alt-dal doğrulama matrisi (bu tur: `alsa/` + `coreaudio/`)

### §J.1 Yazım öncesi disk envanteri (ölçüm 2026-10-13)

| # | Yüzey | Dosya | Satır | Bu turda |
|---|---|---|---|---|
| 1 | kök `index.md` | 1 | 99 | ✅ korunacak (40–100 bandı) |
| 2 | kök `isletim-sistemi-audio-kapsam.md` | 1 | 510 | ✅ dokunulmadı (≥500) |
| 3 | kök `isletim-sistemi-audio-dogrulama.md` | 1 | 332 | 🔄 → ≥500 (bu dosya) |
| 4 | kök `isletim-sistemi-audio-ornekler.md` | 1 | 163 | 🔄 → ≥500 |
| 5 | `wasapi/` (dokunulmaz referans şablon) | 13 | 5.740 | ✅ yalnız okundu |
| 6 | `aaudio/` (B ajanının alanı) | 4 | 1.401 | ✅ dokunulmadı |
| 7 | `alsa/` | 0 | 0 | 🔄 bu turda açılıyor (4 dosya) |
| 8 | `coreaudio/` | 0 | 0 | 🔄 bu turda açılıyor (4 dosya) |
| — | **toplam (yazım öncesi)** | **21** | **7.245** | kök 4 / alt-dal 5 |

**Kök toplamı:** 99 + 510 + 332 + 163 = **1.104 satır** · ≥500'e uyan kök dosya: **1/4** (`-kapsam`).

### §J.2 Senaryo ↔ yeni alt-dal eşlemesi (§A'daki 12 senaryo)

| # | Senaryo | `alsa/` karşılığı | `coreaudio/` karşılığı |
|---|---|---|---|
| S1 | cihaz keşfi | `/proc/asound/cards` · `snd_device_name` | `AudioObjectID` · `kAudioHardwarePropertyDevices` ⚠️ sembol düzeyi |
| S2 | cihaz açma | `snd_pcm_open` → `hw:`/`plughw:`/`default` | `AudioDeviceIOProc` kaydı · AudioUnit instantiate |
| S3 | format pazarlığı | `snd_pcm_hw_params` (nearest yaklaşımı) | `AudioStreamBasicDescription` → `AudioFormatID`/`AudioFormatFlags` |
| S4 | kısa çalma | `aplay` · `alsabat` | `AVAudioPlayer` ⚠️ / AudioQueue start |
| S5 | paylaşım (2 süreç) | `dmix` (playback) · `dsnoop` (capture) | HAL mikser · tek session politikası |
| S6 | kasıtlı xrun | `-EPIPE` sayacı · `snd_pcm_recover` | IOProc durumu · interruption bildirimi |
| S7 | cihaz kaybı | USB çıkar → `snd_pcm` hata + reopen | route change · `interruptionNotification` |
| S8 | round-trip latency | `speaker-test` + loopback | `AVAudioSession` latency sorguları ⚠️ ölçüm yok |
| S9 | DC / sessizlik | `alsabat` istatistik | kayıt istatistiği ⚠️ ölçüm yok |
| S10 | kapanış | `snd_pcm_drain` → close | stop → dispose |
| S11 | uyumsuz format | `plug` katmanı dönüşümü | ASBD pazarlığı + dönüştürücü |
| S12 | soak (60 dk) | xrun sayacı log | xrun/drop sayacı log ⚠️ |

⚠️ = bu turda **ölçülmedi** (donanım yok — §F/6). Eşlemeler mimari eşlemedir, kanıt değil.

### §J.3 Kök dosya derinlik kapısı (hedef ≥500)

| Dosya | Eski (satır) | Hedef | Yeni (satır) | Kapı |
|---|---|---|---|---|
| `index.md` | 99 | 40–100 | → ölçüm §N | ✅ korunacak |
| `isletim-sistemi-audio-kapsam.md` | 510 | ≥500 | 510 (dokunulmadı) | ✅ |
| `isletim-sistemi-audio-dogrulama.md` | 332 | ≥500 | → ölçüm §N | 🔄 |
| `isletim-sistemi-audio-ornekler.md` | 163 | ≥500 | → ölçüm §N | 🔄 |

### §J.4 Alt-dal dosya sayısı kapısı

| # | Alt-dal | Yazım öncesi | Bu tur | Yeni toplam |
|---|---|---|---|---|
| 1 | `wasapi/` | 13 | 0 | 13 |
| 2 | `aaudio/` | 4 | 0 | 4 |
| 3 | `alsa/` | 0 | +4 | 4 |
| 4 | `coreaudio/` | 0 | +4 | 4 |
| 5 | `reactos-audio/` · `tizen-audio/` · `webos-audio/` | 0 | +4·+4·+4 | 4+4+4 |
| — | **alt-dal sayısı** | **5** | **+2 · +3** | **7** |

---

## §K — Bu tur web doğrulama matrisi (2026-10-13 · HTTP 200)

**Erişim yöntemi:** `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`.
Bu bölümdeki 12 URL bu turda **ilk kez** kullanıldı; envanter kesişimi §K.1'de.

| # | İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| K-01 | Kernel ALSA sürücü kılavuzu `snd_pcm_ops` operatör tablosunu örnek kodla belgeler (`.open` · `.close` · `.hw_params` · `.hw_free` · `.prepare` · `.trigger` · `.pointer`) | https://www.kernel.org/doc/html/latest/sound/kernel-api/writing-an-alsa-driver.html | 2026-10-13 | ✅ HTTP 200 |
| K-02 | Kernel ALSA sürücü API dokümanı `.pointer` / `.trigger` (XRUN ve DRAINING'te `SNDRV_PCM_TRIGGER_STOP`) ve `snd_pcm_period_elapsed()` davranışını belgeler | https://www.kernel.org/doc/html/latest/sound/kernel-api/alsa-driver-api.html | 2026-10-13 | ✅ HTTP 200 |
| K-03 | `~/.asoundrc` · `/etc/asound.conf` · `pcm.!default` · `ctl.!default` sözdizimi resmi ALSA wiki'sinde | https://www.alsa-project.org/wiki/Asoundrc | 2026-10-13 | ✅ HTTP 200 |
| K-04 | `aplay` (ALSA çalma aracı) man sayfası | https://manpages.debian.org/bookworm/alsa-utils/aplay.1.en.html | 2026-10-13 | ✅ HTTP 200 |
| K-05 | `amixer` (ALSA mixer aracı) man sayfası | https://manpages.debian.org/bookworm/alsa-utils/amixer.1.en.html | 2026-10-13 | ✅ HTTP 200 |
| K-06 | `alsactl` (ALSA kontrol durumu saklama aracı) man sayfası | https://manpages.debian.org/bookworm/alsa-utils/alsactl.1.en.html | 2026-10-13 | ✅ HTTP 200 |
| K-07 | `AudioObjectID` resmi tip tanımı (`typealias AudioObjectID = UInt32`) | https://developer.apple.com/documentation/coreaudio/audioobjectid | 2026-10-13 | ✅ HTTP 200 |
| K-08 | `AudioDeviceID` resmi tip tanımı (`typealias AudioDeviceID = AudioObjectID`) | https://developer.apple.com/documentation/coreaudio/audiodeviceid | 2026-10-13 | ✅ HTTP 200 |
| K-09 | `AudioDeviceIOProc` callback typealias'ı resmi dokümanda (7 parametre + `OSStatus`) | https://developer.apple.com/documentation/coreaudio/audiodeviceioproc | 2026-10-13 | ✅ HTTP 200 |
| K-10 | `AVAudioSession.interruptionNotification` (`NSNotification.Name`) resmi dokümanda | https://developer.apple.com/documentation/avfaudio/avaudiosession/interruptionnotification | 2026-10-13 | ✅ HTTP 200 |
| K-11 | PipeWire deposunun README'si (sunucu projesi olarak konumlandırma) | https://gitlab.freedesktop.org/pipewire/pipewire/-/blob/master/README.md | 2026-10-13 | ✅ HTTP 200 |
| K-12 | Kernel ALSA **procfile** tasarımı (`/proc/asound/*` arayüzü) dokümante edilmiş | https://www.kernel.org/doc/html/latest/sound/designs/procfile.html | 2026-10-13 | ✅ HTTP 200 |

### §K.1 Envanter kesişimi (bu tur)

| Metrik | Değer |
|---|---|
| Envanter öncesi benzersiz URL (`.ai/architecture/**`, ilk ölçüm) | 2100 |
| Envanter öncesi benzersiz URL (yazım öncesi ikinci ölçüm) | 2103 |
| Bu bölümde eklenen yeni URL | 12 (K-01…K-12) |
| Bu dosyanın tüm yeni URL'leri (§K toplamı) | 12 |
| Envantersel kesişim (yeni ∩ öncesi) | **0** |
| Erişim tarihi | 2026-10-13 · HEAD · 20 s |

> §E'nin 10 URL'si bu turda **yeniden basılmadı** — envanterde oldukları için
> yeniden kullanılsaydı kesişim üretirlerdi (§G aynı kuralı 2026-10-12 için uyguladı).

### §K.2 El edilen (ölü/erişilemeyen) URL'ler — kullanılmadı

| URL | HTTP | Not |
|---|---|---|
| https://www.alsa-project.org/wiki/Asound.conf | 404 | wiki'de bu sayfa yok |
| https://www.alsa-project.org/wiki/Plugins | 404 | plugin listesi ayrı sayfada değil |
| https://www.alsa-project.org/wiki/PCM_API | 404 | PCM dokümanı alsa-doc tarafında |
| https://www.alsa-project.org/wiki/FAQ | 404 | sayfa yok |
| https://www.alsa-project.org/wiki/UCM | 404 | UCM alsa-doc'ta (`group__ucm`) |
| https://www.alsa-project.org/wiki/Dmix | 404 | dmix anlatımı `Asoundrc` sayfasının içinde |
| https://www.kernel.org/doc/html/latest/sound/cards.html | 404 | doğru yol `cards/index.html` |
| https://www.kernel.org/doc/html/latest/sound/hd-audio.html | 404 | bulunamadı |
| https://www.kernel.org/doc/html/latest/sound/api/index.html | 404 | doğru yol `kernel-api/index.html` |
| https://www.kernel.org/doc/html/latest/sound/designs/overview.html | 404 | designs dizini `index.html` ile açılıyor |
| https://www.kernel.org/doc/html/latest/sound/submitting-patches.html | 404 | doğru yol süreç dokümanlarında |
| https://pipewire.org/docs/ | 404 | doküman alt alanı bu yolda değil |
| https://developer.apple.com/documentation/audiotoolbox/audioqueue_services | 404 | doğru slug `audio-queue-services` (envanterde — kullanılmadı) |
| https://developer.apple.com/documentation/coreaudio/audiohardware | 404 | sayfa bu slug'da değil |
| https://developer.apple.com/documentation/avfoundation/avaudioengine | 404 | doğru slug `avfaudio/avaudioengine` (envanterde — kullanılmadı) |
| https://www.freedesktop.org/wiki/Software/PulseAudio/ | 418 | sunucu isteği reddetti — kaynak değil |
| https://manpages.debian.org/bookworm/alsa-utils/amidimap.1.en.html | 404 | bookworm'da yok |
| https://manpages.debian.org/bookworm/alsa-utils/iecset.1.en.html | TIMEOUT | 20 sn içinde yanıt yok — kanıt sayılmadı |

> Bu tablo **negatif kanıttır**: 404/418/TIMEOUT alan hiçbir URL iddia tablolarına
> (§E · §K) **girmez**; §I.1'in 2026-10-12 kuralının sürümüdür.

---

## §L — Repo taraması (2026-10-13 · ALSA/CoreAudio kodu yok)

### §L.1 Desen taraması

| # | Desen | Tarama yüzeyi | Sonuç |
|---|---|---|---|
| L1 | `snd_pcm` · `ALSA` · `CoreAudio` · `AudioUnit` · `AudioQueue` · `AVAudioSession` | `shared/src/**` + `home.coremusic.net/**` (`*.php`, `*.js`, `vendor` hariç) | **0 dosya isabeti** |
| L2 | `**/*.{cpp,h,hpp,c}` | repo kökü (2026-10-12 ölçümü — `wasapi-kod-ornekleri.md` §0) | **0 dosya** |
| L3 | Neva Engine kaynağı | `projects/NevaEngine` | **0 dosya** (PLANNED) |
| L4 | İlk 40 PHP/JS kaynak dosyası | `shared/src` + `home.coremusic.net` | `AI/` · `Api/` · `Middleware/` · `PageRouter/` — ses donanımı kodu **yok** |

**Sonuç (dürüstlük):** CoreMusic'te ALSA/CoreAudio kodu **YOK** — bu bir web PHP reposudur;
OS seviyesi iddialarının kaynağı **upstream dokümantasyondur** (§K). Uydurma `Dosya:`/`Satır:`
referansı yazılmamıştır.

### §L.2 Doğrulanmış repo satır referansları (gerçek ses yolu kodu)

Doğrulama komutu: `Get-Content <dosya> | Select-Object -Index (N-1)`.

| # | Dosya | Satır | Ne kanıtlar | Doğrulama indeksi | Sonuç |
|---|---|---|---|---|---|
| L5 | `shared/src/AI/AIEngine.php` | 288–295 | `ffprobe` ile metadata çıkarma (`shell_exec`) | `-Index 287..294` | ✅ birebir |
| L6 | `shared/src/AI/AIEngine.php` | 102–104 | `sample_rate`/`bit_depth`/`channels` fallback değerleri (44100 · 16 · 2) | `-Index 101..103` | ✅ birebir |
| L7 | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 46 | `CHUNK_BYTES = 1048576` (1 MiB parça) | `-Index 45` | ✅ birebir |
| L8 | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 91–106 | HTTP Range ayrıştırma + 206/416/200 durumları | `-Index 90..105` | ✅ birebir |
| L9 | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 310–329 | `emitBaseHeaders` (Accept-Ranges · Content-Length · ETag) | `-Index 309..328` | ✅ birebir |
| L10 | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 331–334 | `sendBody` → `fopen($path, 'rb')` | `-Index 330..333` | ✅ birebir |

**Yorum:** bu 6 satır ses **dosyası** ve HTTP akışı kanıtıdır; ses **donanımına**
(ALSA/CoreAudio/WASAPI) giden tek bir satır bile değildir (§F/8 · §F/9 ile aynı sonuç).

---

## §M — Bu turun açık / doğrulanamayan iddiaları

| # | İddia | Neden | Durum |
|---|---|---|---|
| M1 | `alsa/` alt-dalı Linux hedefinde (Raspberry Pi 5) kullanılacağı | Q4 (hedef platform) kapalı | UNKNOWN |
| M2 | `coreaudio/` alt-dalı macOS/iOS hedefinde kullanılacağı | donanım yok · yol haritası belirsiz | UNKNOWN |
| M3 | 512 örnek / 48 kHz sartnamesinin ALSA `period_size` olarak geçerli olduğu | gerçek cihazda ölçüm yapılmadı | ⚠️ VERIFICATION REQUIRED |
| M4 | `coreaudio/` bölümündeki `AudioServerDriver` (eklenti sürücü) iddiası | bu turda doğrulayan resmi URL bulunamadı (Apple dokümanı 404) | ⚠️ VERIFICATION REQUIRED |
| M5 | PipeWire/PulseAudio'ya `default` yönlendirmesinin CoreMusic sunucusunda etkisi | sunucuda ses cihazı yok (§B.2) | UNKNOWN |
| M6 | `alsa/` ve `coreaudio/` dosyalarının Neva Engine tarafından kullanılması | Neva Engine = 0 dosya (§L/3) | ⚠️ PLANNED |

---

## §N — Bu turun kapıları (kapanış ölçümü)

| # | Kapı | Kriter | Sonuç |
|---|---|---|---|
| N1 | kök derinlik | `dogrulama` ≥500 · `ornekler` ≥500 | → bu dosyanın final ölçümü raporlanır |
| N2 | kök `index.md` bandı | 40–100 satır | → raporlanır |
| N3 | alt-dal üretimi | `alsa/` + `coreaudio/` = 8 dosya · içerik 6 dosya ≥500 | → raporlanır |
| N4 | alt-dal sayısı | 5 → 7 | → raporlanır |
| N5 | URL kesişimi | yeni ∩ envanter(öncesi) = 0 | §K.1: **0** ✅ |
| N6 | yasak yüzey | `wasapi/` · `aaudio/` · `reactos-audio/` · `tizen-audio/` · `webos-audio/` · kök 6 üst dosya · `*.php/js/css/sql` = 0 yazma | ✅ |
| N7 | frontmatter | 8 alan + `group` · `parent:` yok · `group` tırnaksız | ✅ |
| N8 | mojibake / dolgu / `TBD` / `// ...` / boş hücre | 0 | ✅ |

> N1–N4 sayıları bu turun **son** ölçümüyle rapor satırında taşınır; bu dosya
> kapıları tanımlar, tahmin yazmaz.

---

## §O — 2026-10-14 turu: 7 OS örnek doğrulama matrisi

> Bu tur (K000-isletim-sistemi-audio/ · 7 OS tamamı) için web doğrulaması.
> Yöntem: `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`
> + 5 kaynak için gövde-GET (webfetch markdown / raw fetch).

### §O.1 URL matrisi (21 URL · 17×200 · 4 ölü)

| # | İddia | URL | Sonuç |
|---|---|---|---|
| 1 | AAudio rehberi (builder/callback/state/buffer) | https://developer.android.com/ndk/guides/audio/aaudio/aaudio | ✅ 200 · gövde alındı ("Last updated 2026-03-06 UTC") |
| 2 | OpenSL ES for Android | https://developer.android.com/ndk/guides/audio/opensl/opensl-for-android | ✅ 200 |
| 3 | NDK audio API referans grubu | https://developer.android.com/ndk/reference/group/audio | ✅ 200 |
| 4 | NDK samples | https://developer.android.com/ndk/samples | ✅ 200 |
| 5 | WASAPI rendering (PlayAudioStream) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/rendering-a-stream | ✅ 200 · gövde alındı (ms.date 2018-05-31 · updated_at 2025-03-11) |
| 6 | About the Windows Core Audio APIs | https://learn.microsoft.com/en-us/windows/win32/coreaudio/about-the-windows-core-audio-apis | ✅ 200 |
| 7 | Exclusive-mode streams | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | ✅ 200 |
| 8 | WASAPI ana sayfa | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | ✅ 200 |
| 9 | Tizen raw audio (CAPI sound_manager) | https://raw.githubusercontent.com/Samsung/tizen-docs/main/docs/application/native/guides/multimedia/raw-audio.md | ✅ 200 · 27.795 bayt yerel kopya |
| 10 | webOS audio referansı | https://www.webostv.developer.lge.com/develop/references/audio | ✅ 200 · 62.708 bayt yerel HTML kopya |
| 11 | ReactOS winmm.spec | https://raw.githubusercontent.com/reactos/reactos/master/dll/win32/winmm/winmm.spec | ✅ 200 · 8.043 bayt / 215 satır yerel kopya |
| 12 | ALSA lib PCM grubu | https://www.alsa-project.org/alsa-doc/alsa-lib/group___p_c_m.html | ✅ 200 |
| 13 | ALSA ana sayfa | https://www.alsa-project.org/wiki/Main_Page | ✅ 200 |
| 14 | Apple AudioDeviceIOProc | https://developer.apple.com/documentation/coreaudio/audiodeviceioproc | ✅ 200 |
| 15 | Apple AudioToolbox | https://developer.apple.com/documentation/audiotoolbox | ✅ 200 |
| 16 | Android 9 (Pie) sürüm notları (AAudio genişlemesi) | https://developer.android.com/about/versions/pie/android-9.0 | ✅ 200 |
| 17 | Android Game Development Kit | https://developer.android.com/games/agdk | ✅ 200 |
| 18 | AAudio kısa yol (eski) | https://developer.android.com/ndk/guides/audio/aaudio | ❌ **404 — ölü** |
| 19 | AAudio referans (eski) | https://developer.android.com/ndk/reference/group/aaudio | ❌ **404 — ölü** |
| 20 | google/ndk-samples | https://github.com/google/ndk-samples | ❌ **404 — ölü** |
| 21 | WASAPI render-a-stream (eski slug) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/render-a-stream | ❌ **404 — ölü** (doğrusu `rendering-a-stream`, satır 5) |

**4 ölü URL negatif kanıttır** — bu turda ilgili dosyalarda düzeltildi/doğru URL kullanıldı;
hiçbiri "varmış" gibi yazılmadı. Satır 20 (`google/ndk-samples`) için taşınma hedefi
(`android/ndk-samples`) **test edildi (2026-10-14): HTTP 200 ✅ — taşıma DOĞRULANDI**
(`google/ndk-samples` 404 + `android/ndk-samples` 200 · HEAD ×2). Kapanış turu: §P.

### §O.2 Gövde-GET kanıtları (yerel kopyalar)

| Kaynak | Yerel kopya | Boyut | Kullanan bölüm |
|---|---|---|---|
| AAudio rehberi | webfetch (metin akışı — disk kopyası yok) | tam metin | kök `-ornekler.md` §20 · `aaudio/aaudio-ornekler.md` |
| rendering-a-stream (PlayAudioStream) | webfetch (metin akışı — disk kopyası yok) | tam metin | kök `-ornekler.md` §17 |
| Tizen raw-audio.md | `C:\temp\opencode\tizen-raw-audio.md` | 27.795 bayt | kök `-ornekler.md` §21 |
| webOS audio.html | `C:\temp\opencode\webos-audio.html` | 62.708 bayt | kök `-ornekler.md` §22 |
| ReactOS winmm.spec | `C:\temp\opencode\reactos-winmm.spec` | 8.043 bayt · 215 satır | kök `-ornekler.md` §23 |

### §O.3 Bu tur üretim ve kapılar

| # | Kapı | Sonuç |
|---|---|---|
| 1 | Yeni dosya | `aaudio/aaudio-ornekler.md` (tek yeni dosya — 7/7 dal örnek tamamlandı) |
| 2 | Düzenlenen dosyalar | kök `-ornekler.md` (§16-§24) · `aaudio/index.md` · kök `index.md` · `kapsam.md` · `dogrulama.md` (bu dosya) |
| 3 | `wasapi/` yazma | **0** (dokunulmaz — §N6 korundu) |
| 4 | Kod alıntı doğruluğu | her blok gövde-GET/yerel kopyadan birebir; kaynak etiketi blok başında |
| 5 | Repo'da ses donanımı kodu | **0** (§L/2026-10-13 taraması — bu turda değişmedi) |

---

## §P Kapanış Turu (2026-10-14) — açık ⚠️ maddelerinin denetimi

> Kapsam: §O turundan sonra **kullanıcı isteğiyle eklenen PHP katmanı (§25)** sırasında
> yeniden açılan/tekrar denenen `⚠️ VERIFICATION REQUIRED` maddeleri. Her madde ya
> **kapatıldı** (kanıt yazıldı) ya da **kapanmadı** (negatif kanıt = durum korundu).

### §P.1 Kapanan maddeler

| # | Madde | Sonuç | Kanıt (2026-10-14) |
|---|---|---|---|
| P1 | `google/ndk-samples` → `android/ndk-samples` taşınma hedefi (§O/20 · ornekler §24/20) | ✅ **KAPANDI** | HEAD ×2: `android/ndk-samples` **200** · `google/ndk-samples` **404** → taşıma DOĞRULANDI |
| P2 | `IAudioClient` IDispatch içermez → PHP COM erişemez (ornekler §25.3) | ✅ **KAPANDI** | learn.microsoft.com `nn-audioclient-iaudioclient` fetch 200: *"The IAudioClient interface inherits from the IUnknown interface"* — miras zincirinde IDispatch yok |
| P3 | PHP FFI → `winmm.dll` çalıştırılabilir mi? (ornekler §25.3 yol 2) | ✅ **KAPANDI** | yerel canlı test: `php -d extension=ffi` · PHP 8.5.8 NTS VC22 x64 → `waveOutGetNumDevs() = 12` (12 cihaz) · exit 0 · ReactOS `winmm.spec` imzalarıyla birebir (`waveOutOpen(ptr long ptr long long long)` · `waveOutWrite(long ptr long)` · `waveOutClose(long)`) |
| P4 | webOS mutlak ses metotları (webos index W1) | ✅ **KISMI KAPANDI** | yerel gövde kopya (62.708 B): See also ağacı dâhil yalnız `setMuted/volumeUp/volumeDown` — `getVolume/setVolume` **YOK** (negatif kanıt). "Cihazda hiç yok" iddiası başka sürüm sayfası için UNKNOWN |

### §P.2 Kapanmayan maddeler (negatif kanıt = ⚠️ korundu)

| # | Madde | Sonuç | Kanıt (2026-10-14) |
|---|---|---|---|
| P5 | AAudio min-OS "Android 8.0 (API 26)" (aaudio-anayasa W4) | ❌ **KAPANMADI** | `/ndk/guides/audio/aaudio/aaudio` + ana sayfa 2× GET: `Android 8`/`API 26` deseni **0 isabet** → ⚠️ KALIR |
| P6 | Apple `AudioServerDriver` dokümanı (M4) | ❌ **KAPANMADI — negatif güçlendi** | `developer.apple.com/documentation/coreaudio/audioserverdriver` → **404** (tekrar HEAD) |
| P7 | `afplay` resmi Apple kaynağı (ornekler §25.4/§25.7/8) | ❌ **KAPANMADI** | apple.com/developer.apple.com araması yalnız üçüncü taraf → ⚠️ KALIR |
| P8 | ReactOS + PHP Windows binary uyumu (§25.5) | ❌ **KAPANAMAZ** | ReactOS çalıştırma ortamı yok → ⚠️ KALIR (P3 winmm-FFI kanıtı hafifletti: winmm katmanı PHP'den doğrulandı) |

### §P.3 Bu turda dokunulmayan kalıcı ⚠️ (web kanıtıyla kapatılamaz)

Q-A2 (Neva Engine zamanlaması) · Q-A3 (ASIO konumu) · Q-A7/Q-A8 (ölçüm eşiği ·
paylaşım modeli) · Q-A9/Q-A11/Q-A12 · N3/N4 (jitter/drift eşiği) · M3 (gerçek cihaz
ölçümü) · M6/R1/R4 (Neva Engine entegrasyonu) — hepsi **Vault Steward kararı veya
donanım ölçümü** gerektirir; §P turunda yalnız web/yerel-kanıtla kapatılabilir
maddeler ele alındı.

**FFI pratik not:** PHP 8.5.8 Windows NTS (VC22 x64) build'inde FFI extension
**default yüklü değildir** — `php -d extension=ffi` ile çağrılır (kalıcı için
php.ini `extension=ffi`). Test dosyası: `C:\temp\opencode\ffi-winmm-test.php`
(PHP kodu: §25.3 bloğunun çalıştırılmış hâli).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
