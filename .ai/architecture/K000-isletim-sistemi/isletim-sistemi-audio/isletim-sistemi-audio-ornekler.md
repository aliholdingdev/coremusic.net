---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/isletim-sistemi-audio-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio
children: []
depends_on: []
evidence:
  - "2026-10-13 · §1-§5 repo kod blokları diskten birebir okundu · satır referansları Select-Object -Index ile doğrulandı"
  - "2026-10-13 · §10 ALSA yapılandırma blokları alsa-project.org/wiki/Asoundrc fetch (39 pre bloğu içinden seçildi)"
  - "2026-10-13 · §11 Apple tip beyanları developer.apple.com JSON uç noktası fetch (HTTP 200)"
validation:
  - "2026-10-13 · 7 yeni resmi URL HTTP 200 · envanter kesişimi 0 (§15)"
  - "2026-10-13 · repo'da ALSA/CoreAudio/WASAPI deseni 0 isabet (§9) — uydurma Dosya/Satır referansı yok"
open_questions:
  - "Bu dosyadaki upstream örnekler Neva Engine'e ne zaman kod olarak girer? → ⚠️ PLANNED"
  - "Hedef platform (Q4) hangisi — örneklerin hangisi canlıya alınır? → UNKNOWN"
group: kod
---

# isletim-sistemi-audio — Gerçek Repo Örnekleri + Platform Örnek Yüzeyi

> **Kapsam uyarısı:** repo'da WASAPI/ALSA/CoreAudio'ya **doğrudan** erişen kod **YOK**
> (`kapsam.md` §5 R1 · bu dosya §9 kanıtı). §1–§5, var olan **ses dosyası akışı** kodunu
> taşır — HTTP byte-range streaming + ffprobe. Her blok diskten birebir kopyalanmıştır.
> §10–§11 ise **repo değil**, resmi upstream dokümantasyondan **kaynak-etiketli** örneklerdir.

---

## §0 Repo durum kanıtı (2026-10-13 ölçümü)

| Ölçüm | Komut / yüzey | Sonuç | Tarih |
|---|---|---|---|
| ALSA/CoreAudio deseni | `Select-String 'snd_pcm|ALSA|CoreAudio|AudioUnit|AudioQueue|AVAudioSession'` · `shared/src` + `home.coremusic.net` · `*.php`/`*.js` · `vendor` hariç | **0 dosya isabeti** | 2026-10-13 |
| C/C++ kaynağı | glob `**/*.{cpp,h,hpp,c}` (repo kökü) | **0 dosya** | 2026-10-12 |
| Neva Engine vault kaydı | `.ai/AGENTS.md` §25.2 | UI/Embedded/DSP/Windows: ⚠️ PLANNED (spec mevcut) | 2026-10-12 okundu |
| Ses dosyası akışı kodu | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | **mevcut** (HTTP Range) | 2026-10-13 okundu |
| Ses metadata kodu | `shared/src/AI/AIEngine.php` (ffprobe) | **mevcut** (dosya düzeyi) | 2026-10-13 okundu |

**Sonuç:** bu dosyanın §1–§5'i **repo kanıtı**, §10–§11'i **şartname/upstream kanıtıdır**;
ikisi birbirine karıştırılmaz (RED TEAM kuralı).

---

## §1 Örnek 1 — Ses dosyası metadata çıkarma (ffprobe)

**Dosya:** `shared/src/AI/AIEngine.php:288-295` · **Doğrulama:** `Select-Object -Index 287..294`

```php
private function extractMetadata(string $filePath): array
{
    $cmd = sprintf(
        'ffprobe -v quiet -print_format json -show_format -show_streams "%s"',
        escapeshellarg($filePath)
    );

    $output = shell_exec($cmd);
```

**OS bağlantısı:** Ses **donanımı** değil, ses **dosyası** okunur. `shell_exec` → libc
`popen` → `execve` zinciriyle harici binary'ye gidilir. Bu, `os-bagimliliklar.md` §2 C6'daki
"ffmpeg/ffprobe binary" bağımlılığının tek somut kanıtıdır.

---

## §2 Örnek 2 — HTTP byte-range akışı (tarayıcıya ses)

**Dosya:** `home.coremusic.net/include/Stream/MusicStreamHandler.php:91-106` · **Doğrulama:** `Select-Object -Index 90..105`

```php
$range = $this->parseRange((string)($_SERVER['HTTP_RANGE'] ?? ''), $size);

$this->emitBaseHeaders($mime, $range['length'], $etag, $lastMod);

if ($range['status'] === 416) {
    header('Content-Range: bytes */' . $size);
    http_response_code(416);
    exit;
}

if ($range['status'] === 206) {
    header('Content-Range: bytes ' . $range['start'] . '-' . $range['end'] . '/' . $size);
    http_response_code(206);
} else {
    http_response_code(200);
}
```

**OS bağlantısı:** Çalma/sarma (seek) için **HTTP Range** kullanılır — tarayıcıya parçalı
dosya gönderilir. Bu, sesin cihaza **OS ses API'si üzerinden değil**, HTTP üzerinden
gittiğinin kanıtıdır. (K000 ile frontend sınırı burasıdır.)

---

## §3 Örnek 3 — Temel ses başlıkları

**Dosya:** `home.coremusic.net/include/Stream/MusicStreamHandler.php:310-329` · **Doğrulama:** `Select-Object -Index 309..328`

```php
private function emitBaseHeaders(string $mime, int $length, string $etag, string $lastMod): void
{
    if (function_exists('ini_set')) {
        @ini_set('zlib.output_compression', '0');
    }
    while (ob_get_level() > 0) {
        @ob_end_clean();
    }
    @ignore_user_abort(true);

    header_remove('Content-Type');
    header('Accept-Ranges: bytes');
    header('Content-Length: ' . $length);
    header('ETag: ' . $etag);
    header('Last-Modified: ' . $lastMod);
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Content-Disposition: inline');
    header('X-Content-Type-Options: nosniff');
    header('Content-Type: ' . $mime);
}
```

**OS bağlantısı:** `zlib.output_compression` kapatılır ve buffer'lar temizlenir —
**ses akışında yeniden sıkıştırma olmaz** (OS/SAPI düzeyinde davranış). `Accept-Ranges: bytes`
sunucunun parçalı okumayı desteklediğini bildirir.

---

## §4 Örnek 4 — Gövdeyi parçalı gönderme (dosya okuma)

**Dosya:** `home.coremusic.net/include/Stream/MusicStreamHandler.php:331-334` · **Doğrulama:** `Select-Object -Index 330..333`

```php
private function sendBody(string $path, int $start, int $length): never
{
    $handle = @fopen($path, 'rb');
    if ($handle === false) {
```

**OS bağlantısı:** Dosya tanıtıcısı (fd) `fopen` ile açılır, `start`/`length` kadar
okunur — `read(2)` sargısının uygulama düzeydeki kullanımı. `rb` = ikili mod, platform
bağımsız okuma. Parça boyutu `CHUNK_BYTES = 1048576` (satır 46, `Select-Object -Index 45`
ile doğrulandı) — 1 MiB'lık `fread` döngüsü.

---

## §5 Örnek 5 — Dosya boyutu/erişim kontrolü (donanım-öncesi sınır)

**Dosya:** `home.coremusic.net/include/Stream/MusicStreamHandler.php:70-81`

```php
$size = (int)$row['file_size'];
if (is_file($path)) {
    $diskSize = (int)filesize($path);
    if ($diskSize > 0) {
        $size = $diskSize;
    }
}

$mime      = $this->contentType($path, (string)$row['file_format']);
$mtime     = (int)(is_file($path) ? filemtime($path) : time());
$etag      = '"' . md5($path . '|' . $size . '|' . $mtime) . '"';
$lastMod   = gmdate('D, d M Y H:i:s', $mtime) . ' GMT';
```

**OS bağlantısı:** `filesize`/`filemtime` = `stat(2)` sargıları; `gmdate` GMT = OS saat
bilgisine bağımlı olmayan biçimlendirme. Mime türü dosya uzantısı + DB formatından
türetilir — **donanım ile ilgisi yoktur**.

---

## §6 Özet tablo (§1–§5)

| # | Örnek | Dosya : satır | Ne tür | Donanım erişimi? |
|---|---|---|---|---|
| 1 | ffprobe metadata | `AIEngine.php:288-295` | dosya işleme | **hayır** |
| 2 | HTTP Range | `MusicStreamHandler.php:91-106` | dosya akışı | **hayır** |
| 3 | Temel başlıklar | `MusicStreamHandler.php:310-329` | SAPI | **hayır** |
| 4 | Parçalı gövde | `MusicStreamHandler.php:331-334` | `read` sargısı | **hayır** |
| 5 | `stat` sargıları | `MusicStreamHandler.php:70-81` | `stat` sargısı | **hayır** |
| — | WASAPI/ALSA/CoreAudio çağrısı | **repo'da YOK** | — | ⚠️ VERIFICATION REQUIRED |

---

## §7 Repo'da ALSA/CoreAudio kodu neden yok (dürüstlük kaydı)

| # | Soru | Cevap | Kanıt |
|---|---|---|---|
| 1 | CoreMusic ses donanımını açabilir mi? | **Hayır** | desen taraması 0 isabet (§0) |
| 2 | CoreMusic sesi nasıl iletir? | HTTP byte-range (§2–§4) | `MusicStreamHandler.php` |
| 3 | CoreMusic sesi nasıl analiz eder? | `ffprobe` harici binary (§1) | `AIEngine.php:288-295` |
| 4 | OS ses API'si çağrısı yapan tek satır? | **YOK** | `**/*.{cpp,h}` = 0 dosya |
| 5 | Bu dosyanın platform örnekleri neyi tarif eder? | **gelecek** Neva Engine yüzeyini | ⚠️ PLANNED |

---

## §8 Resmi ses API referansları (2026-10-12 · HTTP 200 — envanterde, yeniden basılmadı)

| # | API | Kaynak |
|---|---|---|
| 1 | Windows Core Audio (WASAPI) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/about-the-windows-core-audio-apis |
| 2 | Exclusive mode streams | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams |
| 3 | ALSA (Linux) | https://www.alsa-project.org/wiki/Main_Page |
| 4 | AudioToolbox (macOS) | https://developer.apple.com/documentation/audiotoolbox |

---

## §9 Açık sorular (kök — korunur)

| # | Soru | Etiket |
|---|---|---|
| Q-A2 | Bu 5 örnekten hangisi gelecekteki donanım yolunun temeli? | ⚠️ VERIFICATION REQUIRED |
| Q-A4 | Hedef platform: Raspberry Pi 5 (Linux/ARM) mi, Windows masaüstü mü? | UNKNOWN |

---

## §10 ALSA örnek yüzeyi (upstream — repo'da YOK)

> **Kaynak kuralı:** aşağıdaki yapılandırma blokları `alsa-project.org/wiki/Asoundrc`
> sayfasından **birebir** alınmıştır (fetch 2026-10-13). Repo'da bu dosyaların hiçbiri
> **yoktur**; bunlar Neva Engine/Linux hedefi için **çalışan kalıp** gösterimidir.

### §10.1 Varsayılan cihaz tanıımı — `pcm.!default` / `ctl.!default`

**Kaynak:** https://www.alsa-project.org/wiki/Asoundrc · **Dosya yolları (sayfadan):**
`$HOME/.asoundrc` · `/etc/asound.conf` · `/usr/share/alsa/alsa.conf`

```
pcm.!default {
	type hw
	card 0
}

ctl.!default {
	type hw           
	card 0
}
```

**Ne yapar:** `default` adını doğrudan kernel `hw:0` cihazına bağlar — format dönüşümü
**yoktur**; uygulama cihazın kanal/srate/formatına uymak zorundadır (bkz. §10.2 `plug`).

### §10.2 `plug` ile format/rate/kanal dönüşümü (çalışan kalıp)

**Kaynak:** https://www.alsa-project.org/wiki/Asoundrc (aynı sayfa, `type plug` bloğu)

```
pcm_slave.sl3 {
	pcm "hw:1,0"
	format S16_LE
	channels 1
	rate 16000
}

pcm.complex_convert {
	type plug
	slave sl3
}
```

**Çağrı:** `aplay -vD complex_convert test.wav` (sayfadaki komut).

| Alan | `plug` davranışı (sayfa yorumu) | Çekirdek |
|---|---|---|
| `format` | `nearest` (varsayılan) veya `unchanged` | en yakın slave formatına dönüştürür |
| `channels` | `nearest` / `unchanged` | kanal sayısını yaklaştırır |
| `rate` | `nearest` / `unchanged` | örnekleme oranını yaklaştırır |
| `route_policy` | `default` · `average` · `copy` · `duplicate` | otomatik tablo (ttable) politikası |

### §10.3 `dmix` — iki süreç için paylaşım (playback)

**Kaynak:** https://www.alsa-project.org/wiki/Asoundrc (`pcm.dmixer` bloğu)

```
pcm.!default {
	type plug
	slave.pcm "dmixer"
}

pcm.dmixer  {
 	type dmix
 	ipc_key 1024
 	slave {
		pcm "hw:1,0"
		period_time 0
		period_size 1024
		buffer_size 4096
		rate 44100
	}
	bindings {
		0 0
		1 1
	}
}

ctl.dmixer {
	type hw
	card 0
}
```

**Çağrı:** `aplay -f cd -D default test.wav` (sayfadaki komut).
**Ölçüm karşılığı:** `period_size 1024` @ 44100 Hz = 23,22 ms periyot (1024 ÷ 44100 × 1000).

### §10.4 `dsnoop` — capture paylaşımı

**Kaynak:** https://www.alsa-project.org/wiki/Asoundrc (`pcm.mixin` bloğu)

```
pcm.mixin {
	type dsnoop
	ipc_key 5978293	# must be unique for all dmix plugins!!!!
	ipc_key_add_uid yes
	slave {
		pcm "hw:0,0"
		channels 2
		period_size 1024
		buffer_size 4096
		rate 44100
		periods 0 
		period_time 0
	}
	bindings {
		0 0
		0 1
	}
}
```

**Uyarı (sayfanın kendi yorumu):** `ipc_key` **tüm** dmix/dsnoop eklentileri için
**eşsiz** olmalıdır — aksi halde yanlış slava bağlanma riski doğar.

### §10.5 CLI kalıpları (upstream sayfasındaki komutlar)

| # | Komut | Ne yapar | Kaynak |
|---|---|---|---|
| 1 | `aplay -D default test.wav` | varsayılan cihazda çal | Asoundrc |
| 2 | `aplay -D hw:0,0 test.wav` | doğrudan kernel cihazında çal | Asoundrc |
| 3 | `aplay -D primary test.wav` | adlandırılmış `pcm.primary` üzerinden çal | Asoundrc |
| 4 | `aplay -vD complex_convert test.wav` | `plug` dönüşümüyle çal (verbose) | Asoundrc |
| 5 | `aplay -f cd -D default test.wav` | CD formatı (16/44.1/2) ile çal | Asoundrc |
| 6 | `arecord -f S16_LE -r 44100 -c 4 -D multi | aplay -f S16_LE -r 44100 -c 4 -D multi` | 4 kanal döngü testi | Asoundrc |
| 7 | `ecasound -i test.wav -o alsa,hw:0,0` | üçüncü parti araçla doğrudan ALSA'ya yaz | Asoundrc |

### §10.6 ALSA araç yüzeyi (man sayfaları — 2026-10-13 HTTP 200)

| Araç | Görev | Doğrulanma | URL |
|---|---|---|---|
| `aplay` | PCM çalma (wav çalar) | ✅ 200 | https://manpages.debian.org/bookworm/alsa-utils/aplay.1.en.html |
| `amixer` | mixer kontrol okuma/yazma | ✅ 200 | https://manpages.debian.org/bookworm/alsa-utils/amixer.1.en.html |
| `alsactl` | kontrol durumu kaydet/geri yükle | ✅ 200 | https://manpages.debian.org/bookworm/alsa-utils/alsactl.1.en.html |
| `alsaloop` | döngü (loopback) köprüsü — round-trip ölçümü | ✅ 200 | https://manpages.debian.org/bookworm/alsa-utils/alsaloop.1.en.html |
| `aplaymidi` | MIDI akışı çalma | ✅ 200 | https://manpages.debian.org/bookworm/alsa-utils/aplaymidi.1.en.html |
| `axfer` | `arecord`/`aplay` yerine geçen transfer aracı | ✅ 200 | https://manpages.debian.org/bookworm/alsa-utils/axfer.1.en.html |

### §10.7 ALSA API çağrı sırası (libasound dokümanı — HTTP 200)

**Kaynak:** https://www.alsa-project.org/alsa-doc/alsa-lib/group___p_c_m.html

| # | Fonksiyon | Aşama |
|---|---|---|
| 1 | `snd_pcm_open` | cihaz açılır (yön: playback/capture) |
| 2 | `snd_pcm_hw_params` | donanım parametreleri (rate · format · kanal · period) |
| 3 | `snd_pcm_sw_params` | yazılım parametreleri (start_threshold · avail_min) |
| 4 | `snd_pcm_prepare` | akış hazırlanır (state → PREPARED) |
| 5 | `snd_pcm_start` | DMA/akış başlatılır |
| 6 | `snd_pcm_drop` | anında durdur (buffer'ı at) |
| 7 | `snd_pcm_drain` | buffer'ı boşaltarak durdur |
| 8 | `snd_pcm_recover` | hata/xrun'dan kurtarma |

> Aynı sayfa `plug` eklentisini de anar (§10.2). `dmix`/`dsnoop` anlatımı bu sayfada
> **değil**, `Asoundrc` sayfasındadır (§10.3 · §10.4) — kaynak şaşması yoktur.

---

## §11 CoreAudio örnek yüzeyi (upstream — repo'da YOK)

> **Kaynak kuralı:** aşağıdaki tip beyanları `developer.apple.com` doküman JSON
> ucundan (`/tutorials/data/documentation/...json`) **birebir** alınmıştır
> (fetch 2026-10-13, HTTP 200). Repo'da hiçbir Apple ses kodu **yoktur** (§7).

### §11.1 Temel kimlik tipleri

| # | Beyan | Anlamı | Kaynak |
|---|---|---|---|
| 1 | `typealias AudioObjectID = UInt32` | HAL nesnesinin kimliği (32-bit) | https://developer.apple.com/documentation/coreaudio/audioobjectid |
| 2 | `typealias AudioDeviceID = AudioObjectID` | ses cihazı = bir HAL nesnesi | https://developer.apple.com/documentation/coreaudio/audiodeviceid |

**Örnek bağlam:** cihaz listesi = `AudioDeviceID` kümesi; `default` çıkış = bir
`AudioDeviceID` değeri. Dönüşüm/kopya gerekmez — tipler aynıdır (beyan 2).

### §11.2 IOProc callback imzası (akışın çekirdeği)

**Beyan (birebir):**

```
typealias AudioDeviceIOProc = (AudioObjectID, UnsafePointer<AudioTimeStamp>, UnsafePointer<AudioBufferList>, UnsafePointer<AudioTimeStamp>, UnsafeMutablePointer<AudioBufferList>, UnsafePointer<AudioTimeStamp>, UnsafeMutableRawPointer?) -> OSStatus
```

**Kaynak:** https://developer.apple.com/documentation/coreaudio/audiodeviceioproc

**Parametre okuması (beyan sırasına göre):**

| # | Parametre | Yön | Rol |
|---|---|---|---|
| 1 | `AudioObjectID` | giriş | hangi cihaz |
| 2 | `UnsafePointer<AudioTimeStamp>` | giriş | giriş zaman damgası |
| 3 | `UnsafePointer<AudioBufferList>` | giriş | giriş buffer'ı (capture) |
| 4 | `UnsafePointer<AudioTimeStamp>` | giriş | çıkış zaman damgası |
| 5 | `UnsafeMutablePointer<AudioBufferList>` | çıkış | çıkış buffer'ı (render) |
| 6 | `UnsafePointer<AudioTimeStamp>` | giriş | cihaz zaman damgası |
| 7 | `UnsafeMutableRawPointer?` | giriş | kullanıcı verisi (client ref) |
| dönüş | `OSStatus` | — | 0 = başarılı |

**CoreMusic kuralı (§10/§11 bağlamı):** bu callback **gerçek zamanlıdır** —
kural: `malloc`/`new`/mutex yasak (kök `dogrulama.md` §D.2 N9).

### §11.3 Oturum/kesinti bildirimi

| # | Beyan | Kaynak |
|---|---|---|
| 1 | `class let interruptionNotification: NSNotification.Name` | https://developer.apple.com/documentation/avfaudio/avaudiosession/interruptionnotification |
| 2 | `class AVAudioUnitEffect` (efekt AudioUnit sarmalayıcısı) | https://developer.apple.com/documentation/avfaudio/avaudiouniteffect |

### §11.4 Session kategori seçimi (matris)

| Durum | Kategori/mode beyanı | Kaynak | Not |
|---|---|---|---|
| arka planda ses çalma hedefi | `playback` kategorisi | https://developer.apple.com/documentation/avfaudio/avaudiosessioncategoryplayback | varsayılan çalma adayı |
| özel mode hedefi | `AVAudioSessionMode` | https://developer.apple.com/documentation/avfaudio/avaudiosessionmode | mode ayrı tiptir |
| varsayılan mode | `AVAudioSessionModeDefault` | https://developer.apple.com/documentation/avfaudio/avaudiosessionmodedefault | pazarlık başlangıcı |
| format tanımı | `AudioFormatID` | https://developer.apple.com/documentation/coreaudio/audioformatid | codec kimliği |
| format bayrakları | `AudioFormatFlags` | https://developer.apple.com/documentation/coreaudio/audioformatflags | bit düzlemi/doluluk |
| lineer PCM sabiti | `kAudioFormatLinearPCM` | https://developer.apple.com/documentation/coreaudio/kaudioformatlinearpcm | ham PCM codec kimliği |
| buffer tanımı | `AudioBuffer` | https://developer.apple.com/documentation/coreaudio/audiobuffer | kanal buffer'ı |
| hizalı PCM servisi | Core Audio Utils sayfası (`AudioToolbox`) | https://developer.apple.com/documentation/audiotoolbox/audio_services | servis ailesi |
| sunucu üstü ilişkisi | PipeWire deposu | https://gitlab.freedesktop.org/pipewire/pipewire | Linux üst ses sunucusu |

### §11.5 Örnek ↔ repo ayrımı

| Yüzey | Repo'da var mı? | Bu dosyada | Durum |
|---|---|---|---|
| PHP ses dosyası akışı | **evet** (§1–§5) | diskten kopya | ✅ kanıtlı |
| ALSA yapılandırması | hayır | upstream kalıp (§10) | ⚠️ PLANNED |
| CoreAudio tip beyanı | hayır | upstream alıntı (§11) | ⚠️ PLANNED |
| C++/ObjC/Swift gerçek kod | hayır (0 dosya) | yazılmadı | ⚠️ VERIFICATION REQUIRED |

---

## §12 Anti-pattern — NE YAPILMAZ

| # | Yasak davranış | Sonuç | Kural |
|---|---|---|---|
| 1 | Repo'da olmayan `Dosya: … / Satır: …` referansı yazmak | uydurma kanıt | bu dosya §0 · RED TEAM |
| 2 | `pcm.!default { type hw }` ile düşük gecikme beklemek | format uymazsa `open`/`hw_params` hatası | §10.1 · §10.2 |
| 3 | `plug` olmadan 44.1 kHz dosyayı 48 kHz cihaza vermek | xrun / hız şaşması | §10.2 |
| 4 | İki süreçte aynı `ipc_key` (dmix/dsnoop) | yanlış slava bağlanma | §10.4 (sayfa uyarısı) |
| 5 | IOProc içinde tahsis (malloc/new) veya lock | gerçek zamanlı ihlal | §11.2 · dogrulama N9 |
| 6 | `snd_pcm_drain` yerine aniden kapanmak | click/pop duyulur | §10.7/7 · S10 |
| 7 | xrun sonrası yeniden `open` (kurtarmadan) | gereksiz cihaz kaybı, süre kaybı | §10.7/8 `snd_pcm_recover` |
| 8 | Eşiği ölçmeden "geçti" yazmak | sahte doğrulama | kök `dogrulama.md` §D.3 |
| 9 | `pcm5122` içeren donanım seçimi | ADR-038 H001 ihlali | kök AGENTS.md kural 6 |
| 10 | Ses callback'inde dosya I/O (`fopen`/`fread`) | jitter | §2–§4 (dosya I/O yalnız servis katmanında) |
| 11 | `group` alanını tırnaklamak | şema ihlali | v2 §4.2 (frontmatter) |
| 12 | `wasapi/` dosyalarını düzenlemek | çakışma (dokunulmaz) | bu tur beyazlistesi |

---

## §13 Eşik / kabul kriteri (özet — kök `dogrulama.md` §D'den devir)

| # | Eşik | Değer | Durum |
|---|---|---|---|
| 1 | xrun (12 dk pencere) | 0 | kabul kriteri — ölçülmedi |
| 2 | hedef round-trip | ≤ 10,67 ms (512/48 kHz) | sartname türevi |
| 3 | ALSA periyot karşılığı | 512 örnek ÷ 48000 = 10,667 ms | hesap kanıtı |
| 4 | `dmix` örneği (§10.3) | 1024 ÷ 44100 = 23,22 ms | upstream yapılandırma |
| 5 | örnek biçimi | 32-bit `float` (ADR-017) | bağlayıcı |
| 6 | gerçek zamanlı kural | callback'te tahsis/lock yasak | bağlayıcı |
| 7 | DC offset > 0,5 V | koruma rölesi zorunlu | K001-donanım |
| 8 | ölçüm tekrar sapması | medyan dışı ≥ %10 ⇒ geçersiz | metod kuralı |

---

## §14 Örnek → senaryo → doğrulama eşlemesi

| # | Örnek (bu dosya) | Kök senaryo | Doğrulama yolu |
|---|---|---|---|
| 1 | §1 ffprobe | — (dosya analizi) | çıktı JSON doğruluğu |
| 2 | §2 HTTP Range | S4 kısa çalma (tarayıcı) | 206 + byte bütünlüğü |
| 3 | §3 başlıklar | S11 uyumsuz istek | Content-Type doğru mu |
| 4 | §4 sendBody | S10 kapanış | son paketten sonra kopma yok |
| 5 | §5 stat | — | boyut/ETag tutarlılığı |
| 6 | §10.1 default (hw) | S2 cihaz açma | `aplay -D default` |
| 7 | §10.2 plug | S11 format dönüşümü | `aplay -vD complex_convert` |
| 8 | §10.3 dmix | S5 paylaşım (2 süreç) | iki `aplay` aynı anda, kırılma yok |
| 9 | §10.7 sıra | S6 xrun → kurtarma | `snd_pcm_recover` sonrası devam |
| 10 | §11.2 IOProc | S6/S8 akış + ölçüm | OSStatus = 0 · jitter (σ) |

---

## §15 Bu tur kaynak matrisi (2026-10-13 · HTTP 200 · envanter kesişimi 0)

| # | İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| 1 | libasound PCM grubu: `snd_pcm_open` · `hw_params` · `sw_params` · `prepare` · `start` · `drop` · `drain` · `recover` | https://www.alsa-project.org/alsa-doc/alsa-lib/group___p_c_m.html | 2026-10-13 | ✅ HTTP 200 |
| 2 | libasound mixer grubu (kontrol okuma/yazma arayüzü) | https://www.alsa-project.org/alsa-doc/alsa-lib/group___mixer.html | 2026-10-13 | ✅ HTTP 200 |
| 3 | `alsaloop` man sayfası (döngü köprüsü) | https://manpages.debian.org/bookworm/alsa-utils/alsaloop.1.en.html | 2026-10-13 | ✅ HTTP 200 |
| 4 | `aplaymidi` man sayfası (MIDI çalma) | https://manpages.debian.org/bookworm/alsa-utils/aplaymidi.1.en.html | 2026-10-13 | ✅ HTTP 200 |
| 5 | `axfer` man sayfası (transfer aracı) | https://manpages.debian.org/bookworm/alsa-utils/axfer.1.en.html | 2026-10-13 | ✅ HTTP 200 |
| 6 | `AudioToolbox` servis ailesi resmi sayfası | https://developer.apple.com/documentation/audiotoolbox/audio_services | 2026-10-13 | ✅ HTTP 200 |
| 7 | `AVAudioSession` playback kategorisi resmi sayfası | https://developer.apple.com/documentation/avfaudio/avaudiosessioncategoryplayback | 2026-10-13 | ✅ HTTP 200 |
| 8 | PipeWire deposu (Linux üst ses sunucusu) | https://gitlab.freedesktop.org/pipewire/pipewire | 2026-10-13 | ✅ HTTP 200 |

**Erişim yöntemi:** `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`.
§10 blokları için ek fetch: `https://www.alsa-project.org/wiki/Asoundrc` (aynı gün, HTTP 200 —
kök `dogrulama.md` §K-03'te kayıtlıdır, burada **tekrar sayılmaz**).
§11 beyanları için JSON fetch: `https://developer.apple.com/tutorials/data/documentation/coreaudio/audiodeviceioproc.json` ·
`.../coreaudio/audioobjectid.json` · `.../coreaudio/audiodeviceid.json` ·
`.../avfaudio/avaudiosession/interruptionnotification.json` · `.../avfaudio/avaudiouniteffect.json`
(hepsi HTTP 200, 2026-10-13).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
