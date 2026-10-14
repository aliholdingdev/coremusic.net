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
  - "2026-10-14 · §17 Windows PlayAudioStream kod bloğu learn.microsoft.com/coreaudio/rendering-a-stream gövde-fetch (birebir alıntı)"
  - "2026-10-14 · §20 AAudio builder/callback blokları developer.android.com/ndk/guides/audio/aaudio/aaudio gövde-fetch (birebir alıntı)"
  - "2026-10-14 · §21 Tizen audio_out_* blokları raw.githubusercontent.com/Samsung/tizen-docs/raw-audio.md yerel kopya (C:\\temp\\opencode\\tizen-raw-audio.md)"
  - "2026-10-14 · §22 webOS Luna JS blokları webostv.developer.lge.com/develop/references/audio yerel HTML kopya (C:\\temp\\opencode\\webos-audio.html)"
  - "2026-10-14 · §23 ReactOS winmm.spec export satırları raw.githubusercontent.com/reactos/reactos/master yerel kopya (C:\\temp\\opencode\\reactos-winmm.spec)"
  - "2026-10-14 · §25 PHP×OS bölümü: PHP FFI/COM/exec sayfaları HEAD 200 · refs.audio.php 404 (negatif kanıt) · ALSA pcm.h fetch (FFI imzaları birebir)"
validation:
  - "2026-10-13 · 7 yeni resmi URL HTTP 200 · envanter kesişimi 0 (§15)"
  - "2026-10-13 · repo'da ALSA/CoreAudio/WASAPI deseni 0 isabet (§9) — uydurma Dosya/Satır referansı yok"
  - "2026-10-14 · §24 matrisi: 21 URL HEAD/GET · 17×200 · 4 ölü URL (404) negatif kanıt olarak kaydedildi"
  - "2026-10-14 · §16-§23 OS gruplaması 7/7 tamamlandı — aaudio dalı örnek dosyası bu turda üretildi (aaudio-ornekler.md)"
  - "2026-10-14 · §25 PHP×OS katmanı eklendi (kullanıcı isteği: kod örneklerinde PHP) — §25.7 PHP doğrulama matrisi 9 satır"
  - "2026-10-14 · kapanış turu: §25.3 winmm FFI canlı test (PHP 8.5.8 → 12 cihaz, exit 0) · IAudioClient IUnknown mirası (learn.microsoft.com 200) · ndk-samples taşıma 200/404 · §25.7 matris 9→15 satır · §24/20 taşınma ⚠️ kapatıldı"
  - "2026-10-14 · §26 Geniş Dil Seti (JS·C·Python·Go·Rust·C#) eklendi — 12 URL doğrulama: 9×200 (W3C Web Audio · pyalsaaudio · sounddevice · portaudio×2 · oto · cpal×2 · NAudio · SoundPlayer) + 2×404 negatif kanıt (nodejs/api/audio · pythonhosted/sounddevice) + 4 README fetch (cpal backend matrisi · oto ALSA fallback · NAudio WASAPI · portaudio cgo)"
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

## §16 OS Gruplaması — 7 İşletim Sistemi Örnek Yüzeyi (2026-10-14)

> **Kapsam:** K000-isletim-sistemi/isletim-sistemi-audio/ altındaki 7 OS dalı,
> her biri kendi örneği + kod alıntısı ile gruplanır. Repo'da ses donanımı kodu
> YOK olduğundan (§0) **her blok upstream/resmi kaynak alıntısıdır** — "hedef yüzey".
> Dal-dosya kanıtı: her OS dalında `-ornekler.md` dosyası mevcuttur (aaudio bu turda üretildi).

| # | OS | API Yüzeyi | Dal | Örnek dosyası | Bu dosyada |
|---|---|---|---|---|---|
| 1 | Windows | WASAPI (IAudioClient/IAudioRenderClient) | `wasapi/` (13 dosya, dokunulmaz) | `wasapi/wasapi-kod-ornekleri.md` | §17 alıntı |
| 2 | Linux | ALSA (libasound PCM) | `alsa/` (4 dosya) | §10 yapılandırma blokları (bu dosya) | §18 pointer |
| 3 | macOS + iOS | CoreAudio / AudioToolbox / AVAudioSession | `coreaudio/` (4 dosya) | §11 tip beyanları (bu dosya) | §19 pointer |
| 4 | Android | AAudio (NDK C API) | `aaudio/` (5 dosya) | `aaudio/aaudio-ornekler.md` | §20 alıntı |
| 5 | Tizen | CAPI sound_manager (audio_out_*) | `tizen/` (4 dosya) | `tizen/tizen-ornekler.md` | §21 alıntı |
| 6 | webOS | Luna bus (luna://com.webos.audio) | `webos/` (4 dosya) | `webos/webos-ornekler.md` | §22 alıntı |
| 7 | ReactOS | Win32 uyumluluk (winmm.dll export) | `reactos/` (4 dosya) | `reactos/reactos-ornekler.md` | §23 alıntı |

> **PHP notu (2026-10-14):** CoreMusic'in dili PHP olduğundan her OS'un PHP karşılığı
> §25'tedir (erişim yolları matrisi · ALSA-FFI kalıbı · COM sınırı · süreç yolu).
> **Geniş dil seti:** JS · C · Python · Go · Rust · C# erişim gerçeklikleri §26'dadır
> (Web Audio · upstream C pointer'ı · pyalsaaudio/sounddevice · portaudio/oto · cpal · NAudio/SoundPlayer — 12 URL doğrulamalı).

---

## §17 Windows / WASAPI — PlayAudioStream (Microsoft resmi örneği)

**Kaynak:** https://learn.microsoft.com/en-us/windows/win32/coreaudio/rendering-a-stream ·
gövde-fetch 2026-10-14 (HTTP 200). `original_content_git_url`:
`github.com/MicrosoftDocs/win32-pr/blob/live/desktop-src/CoreAudio/rendering-a-stream.md`.
Sayfa "Rendering a Stream — Win32 apps" başlıklıdır; kod bloğu sayfanın tamamıdır.
`wasapi/` dalı bu turda dokunulmaz olduğundan alıntı buraya konur; dal dosyaları
`wasapi/wasapi-kod-ornekleri.md` içinde ayrı alıntılar taşır (2026-10-12 fetch).

```c
HRESULT PlayAudioStream(MyAudioSource *pMySource)
{
    HRESULT hr;
    REFERENCE_TIME hnsRequestedDuration = REFTIMES_PER_SEC;
    REFERENCE_TIME hnsActualDuration;
    IMMDeviceEnumerator *pEnumerator = NULL;
    IMMDevice *pDevice = NULL;
    IAudioClient *pAudioClient = NULL;
    IAudioRenderClient *pRenderClient = NULL;
    WAVEFORMATEX *pwfx = NULL;
    UINT32 bufferFrameCount;
    UINT32 numFramesAvailable;
    UINT32 numFramesPadding;
    BYTE *pData;
    DWORD flags = 0;

    hr = CoCreateInstance(
           CLSID_MMDeviceEnumerator, NULL,
           CLSCTX_ALL, IID_IMMDeviceEnumerator,
           (void**)&pEnumerator);
    EXIT_ON_ERROR(hr)

    hr = pEnumerator->GetDefaultAudioEndpoint(
                        eRender, eConsole, &pDevice);
    EXIT_ON_ERROR(hr)

    hr = pDevice->Activate(
                    IID_IAudioClient, CLSCTX_ALL,
                    NULL, (void**)&pAudioClient);
    EXIT_ON_ERROR(hr)

    hr = pAudioClient->GetMixFormat(&pwfx);
    EXIT_ON_ERROR(hr)

    hr = pAudioClient->Initialize(
                         AUDCLNT_SHAREMODE_SHARED,
                         0,
                         hnsRequestedDuration,
                         0,
                         pwfx,
                         NULL);
    EXIT_ON_ERROR(hr)

    // Tell the audio source which format to use.
    hr = pMySource->SetFormat(pwfx);
    EXIT_ON_ERROR(hr)

    // Get the actual size of the allocated buffer.
    hr = pAudioClient->GetBufferSize(&bufferFrameCount);
    EXIT_ON_ERROR(hr)

    hr = pAudioClient->GetService(
                         IID_IAudioRenderClient,
                         (void**)&pRenderClient);
    EXIT_ON_ERROR(hr)

    // Grab the entire buffer for the initial fill operation.
    hr = pRenderClient->GetBuffer(bufferFrameCount, &pData);
    EXIT_ON_ERROR(hr)

    // Load the initial data into the shared buffer.
    hr = pMySource->LoadData(bufferFrameCount, pData, &flags);
    EXIT_ON_ERROR(hr)

    hr = pRenderClient->ReleaseBuffer(bufferFrameCount, flags);
    EXIT_ON_ERROR(hr)

    // Calculate the actual duration of the allocated buffer.
    hnsActualDuration = (double)REFTIMES_PER_SEC *
                        bufferFrameCount / pwfx->nSamplesPerSec;

    hr = pAudioClient->Start();  // Start playing.
    EXIT_ON_ERROR(hr)

    // Each loop fills about half of the shared buffer.
    while (flags != AUDCLNT_BUFFERFLAGS_SILENT)
    {
        // Sleep for half the buffer duration.
        Sleep((DWORD)(hnsActualDuration/REFTIMES_PER_MILLISEC/2));

        // See how much buffer space is available.
        hr = pAudioClient->GetCurrentPadding(&numFramesPadding);
        EXIT_ON_ERROR(hr)

        numFramesAvailable = bufferFrameCount - numFramesPadding;

        // Grab all the available space in the shared buffer.
        hr = pRenderClient->GetBuffer(numFramesAvailable, &pData);
        EXIT_ON_ERROR(hr)

        // Get next 1/2-second of data from the audio source.
        hr = pMySource->LoadData(numFramesAvailable, pData, &flags);
        EXIT_ON_ERROR(hr)

        hr = pRenderClient->ReleaseBuffer(numFramesAvailable, flags);
        EXIT_ON_ERROR(hr)
    }

    // Wait for last data in buffer to play before stopping.
    Sleep((DWORD)(hnsActualDuration/REFTIMES_PER_MILLISEC/2));

    hr = pAudioClient->Stop();  // Stop playing.
    EXIT_ON_ERROR(hr)

Exit:
    CoTaskMemFree(pwfx);
    SAFE_RELEASE(pEnumerator)
    SAFE_RELEASE(pDevice)
    SAFE_RELEASE(pAudioClient)
    SAFE_RELEASE(pRenderClient)

    return hr;
}
```

**OS eşlemesi:** shared-mode endpoint buffer döngüsü — `GetCurrentPadding` →
`GetBuffer` → `LoadData` → `ReleaseBuffer`. Repo yüzeyine (PHP/JS) karşılığı yoktur;
Neva Engine WASAPI hedefidir (`.ai/AGENTS.md` §25.2: Windows SW ⚠️ PLANNED).

---

## §18 Linux / ALSA — Pointer

ALSA örnekleri bu dosyanın **§10**'undadır (2026-10-13, alsa-project.org/wiki/Asoundrc
gövde-fetch): §10.1 default/hw · §10.2 plug dönüşümü · §10.3 dmix/dsnoop paylaşım ·
§10.7 kurtarma sırası (`snd_pcm_recover`). Ek dal kanıtı: `alsa/` 4 dosya ·
`alsa-project.org/alsa-doc/alsa-lib/group___p_c_m.html` (§15 satır 1, HTTP 200).
Bu bölümde alıntı **tekrarlanmaz** (SSOT dedup kuralı).

---

## §19 macOS / iOS / CoreAudio — Pointer

CoreAudio örnekleri bu dosyanın **§11**'indedir (2026-10-13, developer.apple.com JSON
uç noktası fetch): §11.2 `AudioDeviceIOProc` beyanı + gerçek zamanlı kural
(IOProc içinde tahsis/lock yasak — §12 madde 5). Ek dal kanıtı: `coreaudio/` 4 dosya ·
`developer.apple.com/documentation/coreaudio/audiodeviceioproc` ve
`.../audiotoolbox` (§O matrisi, HTTP 200). Bu bölümde alıntı **tekrarlanmaz**.

---

## §20 Android / AAudio — Builder + Data Callback (NDK resmi rehberi)

**Kaynak:** https://developer.android.com/ndk/guides/audio/aaudio/aaudio ·
gövde-fetch 2026-10-14 (HTTP 200 · "Last updated 2026-03-06 UTC").
Dal dosyası: `aaudio/aaudio-ornekler.md` (bu turda üretildi — §5 builder · §7 callback
aynı kaynaktan birebir alıntı).

Builder (rehber "Creating an audio stream" §1-3):

```cpp
AAudioStreamBuilder *builder;
aaudio_result_t result = AAudio_createStreamBuilder(&builder);
```

```cpp
AAudioStreamBuilder_setDeviceId(builder, deviceId);
AAudioStreamBuilder_setDirection(builder, direction);
AAudioStreamBuilder_setSharingMode(builder, mode);
AAudioStreamBuilder_setSampleRate(builder, sampleRate);
AAudioStreamBuilder_setChannelCount(builder, channelCount);
AAudioStreamBuilder_setFormat(builder, format);
AAudioStreamBuilder_setBufferCapacityInFrames(builder, frames);
```

```cpp
AAudioStream *stream;
result = AAudioStreamBuilder_openStream(builder, &stream);
```

Data callback prototipi + kayıt (rehber "Using a high priority callback"):

```cpp
typedef aaudio_data_callback_result_t (*AAudioStream_dataCallback)(
        AAudioStream *stream,
        void *userData,
        void *audioData,
        int32_t numFrames);
```

```cpp
AAudioStreamBuilder_setDataCallback(builder, myCallback, myUserData);
```

```cpp
aaudio_data_callback_result_t myCallback(
        AAudioStream *stream,
        void *userData,
        void *audioData,
        int32_t numFrames) {
    int64_t timeout = 0;

    // Write samples directly into the audioData array.
    generateSineWave(static_cast<float *>(audioData), numFrames);
    return AAUDIO_CALLBACK_RESULT_CONTINUE;
}
```

Düşük gecikme kombinasyonu (rehber "Setting performance mode"):

```cpp
// Create a stream builder
AAudioStreamBuilder *streamBuilder;
AAudio_createStreamBuilder(&streamBuilder);
AAudioStreamBuilder_setDataCallback(streamBuilder, dataCallback, nullptr);
AAudioStreamBuilder_setPerformanceMode(streamBuilder, AAUDIO_PERFORMANCE_MODE_LOW_LATENCY);

// Use it to create the stream
AAudioStream *stream;
AAudioStreamBuilder_openStream(streamBuilder, &stream);
```

**OS eşlemesi:** AAudio C API — repo'da karşılığı yok (§0: C/C++ 0 dosya).
Rehber notu: Oboe kütüphanesi AAudio varsa AAudio, yoksa OpenSL ES'e düşer.

---

## §21 Tizen / CAPI — Ses Çıkış Sırası (Samsung resmi dokümanı)

**Kaynak:** raw.githubusercontent.com/Samsung/tizen-docs/.../raw-audio.md ·
yerel kopya `C:\temp\opencode\tizen-raw-audio.md` (27.795 bayt · 2026-10-14 indirildi,
HTTP 200). Dal: `tizen/` 4 dosya. Alıntı "Managing Synchronous Playback" ve
başlangıç bölümünden (satır ~104-175).

Başlangıç — cihaz oluşturma:

```c
#include <sound_manager.h>
```

```c
/* Define the sample rate for recording audio */
#define SAMPLE_RATE 44100

/* Declare the variable used for checking function results */
audio_io_error_e error_code;

/* Initialize the audio input device */
audio_in_h input;

error_code = audio_in_create(SAMPLE_RATE, AUDIO_CHANNEL_MONO, AUDIO_SAMPLE_TYPE_S16_LE, &input);

/* Initialize the audio output device */
audio_out_h output;

error_code = audio_out_create_new(SAMPLE_RATE, AUDIO_CHANNEL_MONO, AUDIO_SAMPLE_TYPE_S16_LE, &output);
```

Senkron çalma — hazırla / yaz / durdur:

```c
/* Prepare the audio output device (starts the hardware playback process) */
error_code = audio_out_prepare(output);
```

```c
/* Copy the audio data from the local buffer to the internal output buffer */
int bytes_number = audio_out_write(output, buffer, buffer_size);
```

```c
/* Stop the hardware playback process */
error_code = audio_out_unprepare(output);
```

Doküman sırası: `audio_out_create_new` → `audio_out_prepare` → `audio_out_write`
→ `audio_out_unprepare` → `audio_out_destroy`. Senkron arayüz bloklayıcıdır;
doküman açıkça ayrı thread önerir (`ecore_thread_run(synchronous_playback, ...)`).

---

## §22 webOS — Luna Servis Ses Çağrıları (LG resmi dokümanı)

**Kaynak:** webostv.developer.lge.com/develop/references/audio · yerel HTML kopya
`C:\temp\opencode\webos-audio.html` (62.708 bayt · 2026-10-14 indirildi, HTTP 200).
Dal: `webos/` 4 dosya. Alıntı volumeDown/volumeUp "Example" JS bloklarıdır
(HTML entity `&#x27;` → `'` olarak çözüldü; gerisi birebir).

volumeDown örneği:

```javascript
// One-time call
var request = webOS.service.request('luna://com.webos.audio', {
    method: 'volumeDown',
    onSuccess: function (inResponse) {
        console.log('The volume is decreased by 1.');
        // To-Do something
    },
	onFailure: function (inError) {
		console.log('Failed to decrease volume by 1.');
		console.log('[' + inError.errorCode + ']: ' + inError.errorText);
		// To-Do something
		return;
	}
});
```

volumeUp örneği:

```javascript
// One-time call
var request = webOS.service.request('luna://com.webos.audio', {
  method: 'volumeUp',
  onComplete: function (inResponse) {
    console.log('The volume is increased by 1.');
    // To-Do something
  },
  onFailure: function (inError) {
```

**OS eşlemesi:** ses donanımı kodu değil, TV ses servisi RPC'sidir
(`luna://com.webos.audio`). CoreMusic TV hedefinde uzaktan kumanda ses
eşleme senaryosuna karşılık gelir (K000 kapsam notu).

---

## §23 ReactOS — winmm.dll Export Listesi (uyumluluk kanıtı)

**Kaynak:** raw.githubusercontent.com/reactos/reactos/master/dll/win32/winmm/winmm.spec ·
yerel kopya `C:\temp\opencode\reactos-winmm.spec` (8.043 bayt · 215 satır ·
2026-10-14 indirildi, HTTP 200). Dal: `reactos/` 4 dosya.
Bu `.spec` dosyası ReactOS'un `winmm.dll` için export tablosudur — Windows MMAPI
uyumluluğunun kaynağı-kanıtı (uyumluluk = ReactOS'un hedeflediği Win32 API yüzeyi).

Seçili export satırları (dosyadan birebir):

```
 @ stdcall PlaySound(ptr long long) PlaySoundA
 @ stdcall PlaySoundA(ptr long long)
 @ stdcall PlaySoundW(ptr long long)
```

```
 @ stdcall mciSendCommandA(long long long long)
 @ stdcall mciSendCommandW(long long long long)
 @ stdcall mciSendStringA(str ptr long long)
 @ stdcall mciSendStringW(wstr ptr long long)
```

```
 @ stdcall sndPlaySoundA(ptr long)
 @ stdcall sndPlaySoundW(ptr long)
```

```
 @ stdcall waveOutOpen(ptr long ptr long long long)
 @ stdcall waveOutPrepareHeader(long ptr long)
 @ stdcall waveOutWrite(long ptr long)
 @ stdcall waveOutUnprepareHeader(long ptr long)
 @ stdcall waveOutReset(long)
 @ stdcall waveOutClose(long)
 @ stdcall waveOutPause(long)
 @ stdcall waveOutRestart(long)
 @ stdcall waveOutGetPosition(long ptr long)
 @ stdcall waveOutGetVolume(long ptr)
 @ stdcall waveOutSetVolume(long long)
```

**OS eşlemesi:** ReactOS, Windows MMAPI'nin (`winmm.dll`) açık kaynak yeniden
uygulamasıdır; §17'deki WASAPI hedefinin altında yatan legacy katman burada
export düzeyinde kanıtlanır. `waveOut*` klasik (WASAPI öncesi) dal; modern
düşük gecikme yolu §17'deki `IAudioClient` akışıdır.

---

## §24 Bu Tur Kaynak Matrisi (2026-10-14 · HEAD/GET · 7 OS doğrulama turu)

| # | İddia | URL | Yöntem | Sonuç |
|---|---|---|---|---|
| 1 | AAudio rehberi (builder/callback/state) | developer.android.com/ndk/guides/audio/aaudio/aaudio | HEAD + gövde GET | ✅ 200 · tam metin alındı |
| 2 | OpenSL ES for Android | developer.android.com/ndk/guides/audio/opensl/opensl-for-android | HEAD | ✅ 200 |
| 3 | NDK audio API referans grubu | developer.android.com/ndk/reference/group/audio | HEAD | ✅ 200 |
| 4 | NDK samples sayfası | developer.android.com/ndk/samples | HEAD | ✅ 200 |
| 5 | WASAPI rendering (PlayAudioStream) | learn.microsoft.com/.../coreaudio/rendering-a-stream | HEAD + gövde GET | ✅ 200 · tam metin alındı |
| 6 | Core Audio API genel bakış | learn.microsoft.com/.../about-the-windows-core-audio-apis | HEAD | ✅ 200 |
| 7 | Exclusive-mode streams | learn.microsoft.com/.../coreaudio/exclusive-mode-streams | HEAD | ✅ 200 |
| 8 | WASAPI ana sayfa | learn.microsoft.com/.../coreaudio/wasapi | HEAD | ✅ 200 |
| 9 | Tizen raw-audio dokümanı | raw.githubusercontent.com/Samsung/tizen-docs/.../raw-audio.md | HEAD + gövde GET | ✅ 200 · 27,8 KB yerel kopya |
| 10 | webOS audio API referansı | webostv.developer.lge.com/develop/references/audio | HEAD + gövde GET | ✅ 200 · 62,7 KB yerel kopya |
| 11 | ReactOS winmm.spec | raw.githubusercontent.com/reactos/reactos/master/dll/win32/winmm/winmm.spec | HEAD + gövde GET | ✅ 200 · 8,0 KB yerel kopya |
| 12 | ALSA lib PCM grubu | alsa-project.org/alsa-doc/alsa-lib/group___p_c_m.html | HEAD | ✅ 200 |
| 13 | ALSA ana sayfa | alsa-project.org/wiki/Main_Page | HEAD | ✅ 200 |
| 14 | Apple AudioDeviceIOProc | developer.apple.com/documentation/coreaudio/audiodeviceioproc | HEAD | ✅ 200 |
| 15 | Apple AudioToolbox | developer.apple.com/documentation/coreaudio/audiotoolbox | HEAD | ✅ 200 |
| 16 | Android Pie sürüm notları | developer.android.com/about/versions/pie/android-9.0 | HEAD | ✅ 200 |
| 17 | Android Game Development Kit | developer.android.com/games/agdk | HEAD | ✅ 200 |
| 18 | AAudio kısa yol (eski) | developer.android.com/ndk/guides/audio/aaudio | HEAD | ❌ **404 — ölü** |
| 19 | AAudio ref (eski) | developer.android.com/ndk/reference/group/aaudio | HEAD | ❌ **404 — ölü** |
| 20 | google/ndk-samples (eski repo) | github.com/google/ndk-samples | HEAD | ❌ **404 — ölü** · taşıma **DOĞRULANDI (2026-10-14):** github.com/android/ndk-samples HTTP 200 ✅ (§25.7/10) |
| 21 | WASAPI render-a-stream (eski slug) | learn.microsoft.com/.../coreaudio/render-a-stream | HEAD | ❌ **404 — ölü** (doğrusu `rendering-a-stream`, satır 5) |

**Özet:** 21 URL · **17 × HTTP 200** · **4 × 404 (ölü URL — negatif kanıt)**.
Erişim yöntemi: `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`
· gövde alımları: webfetch (markdown, 2026-10-14). Yerel kaynak kopyaları:
`C:\temp\opencode\{tizen-raw-audio.md · webos-audio.html · reactos-winmm.spec}`.

---

## §25 PHP × OS — CoreMusic'in diliyle ses katmanı (2026-10-14)

> **Premis:** CoreMusic PHP 8.x (`strict_types=1` · PDO zorunlu). Bu bölüm her OS'de
> PHP'nin ses katmanına **gerçekten** nasıl erişebileceğini tanımlar. Repo kanıtı
> (§0): PHP kodu ses **dosyasını** işler (ffprobe §1 · HTTP Range §2–§4); ses
> **donanımına** PHP'den erişim repo'da **yoktur**. Bu bölümün kod blokları
> **hedef yüzey kalıplarıdır** — repo kodu değildir, her biri kaynak-etiketlidir.

### §25.1 PHP erişim yolları matrisi

| # | Yol | PHP mekanizması | OS kapsamı | Kanıt |
|---|---|---|---|---|
| 1 | Harici süreç | `shell_exec` / `proc_open` → CLI aracı (ffprobe · aplay · afplay) | hepsi (PHP olan her yer) | repo §1 (`shell_exec`) · php.net 200 |
| 2 | HTTP streaming | PHP byte-range → tarayıcı `<audio>` → OS ses yığını | **hepsi** (tarayıcı PHP'nin yerine ses donanımını sürer) | repo §2–§4 |
| 3 | FFI (PHP 7.4+) | `FFI::cdef` → doğrudan C kütüphanesi (libasound · winmm) | Linux · Windows | php.net 200 · pcm.h (aşağıda) |
| 4 | COM otomasyonu | `new COM(...)` → IDispatch tabanlı COM | yalnız Windows | php.net 200 · ⚠️ WASAPI sınırı §25.3 |
| 5 | ffmpeg köprüsü | `shell_exec('ffmpeg ...')` → OS ses yığını | hepsi | repo §1 (ffprobe aynı kalıp) |

**PHP'de yerleşik (native) ses donanımı API'si YOKTUR.** PHP manual'de ses uzantıları
kategori sayfası **404'tür** (negatif kanıt, 2026-10-14: `php.net/manual/en/refs.audio.php`).
Yukarıdaki 5 yol PHP'nin OS sesine **tek** erişimleridir.

### §25.2 PHP + Linux/ALSA — FFI ile libasound (en derin yol)

**Kanal:** PHP FFI (php.net/manual/en/book.ffi.php · HTTP 200 · setup
php.net/manual/en/ffi.setup.php · HTTP 200 — 2026-10-14).
**İmza kaynağı (birebir):** alsa-lib `include/pcm.h`
(raw.githubusercontent.com/alsa-project/alsa-lib/master/include/pcm.h · 2026-10-14 fetch):

```c
int snd_pcm_open(snd_pcm_t **pcm, const char *name,
                 snd_pcm_stream_t stream, int mode);
int snd_pcm_close(snd_pcm_t *pcm);
int snd_pcm_set_params(snd_pcm_t *pcm,
                       snd_pcm_format_t format,
                       snd_pcm_access_t access,
                       unsigned int channels,
                       unsigned int rate,
                       int soft_resample,
                       unsigned int latency);
snd_pcm_sframes_t snd_pcm_writei(snd_pcm_t *pcm, const void *buffer, snd_pcm_uframes_t size);
int snd_pcm_recover(snd_pcm_t *pcm, int err, int silent);
```

Enum sabitleri (aynı header): `SND_PCM_STREAM_PLAYBACK = 0` ·
`SND_PCM_ACCESS_RW_INTERLEAVED = 3` · `SND_PCM_FORMAT_FLOAT_LE = 14` (ADR-017
32-bit float ile aynı format).

PHP kalıbı (hedef yüzey — **çalıştırılmadı**, ⚠️ PLANNED):

```php
<?php
declare(strict_types=1);

$ffi = FFI::cdef(
    'typedef struct _snd_pcm snd_pcm_t;
     int snd_pcm_open(snd_pcm_t **pcm, const char *name, int stream, int mode);
     int snd_pcm_close(snd_pcm_t *pcm);
     int snd_pcm_set_params(snd_pcm_t *pcm, int format, int access,
         unsigned int channels, unsigned int rate, int soft_resample,
         unsigned int latency);
     long snd_pcm_writei(snd_pcm_t *pcm, const void *buffer, unsigned long size);
     int snd_pcm_recover(snd_pcm_t *pcm, int err, int silent);',
    'libasound.so.2'
);

$pcm = FFI::new('snd_pcm_t*');
$err = $ffi->snd_pcm_open(FFI::addr($pcm), 'default', 0 /* SND_PCM_STREAM_PLAYBACK */, 0);
if ($err < 0) {
    exit(1); // negatif değer = hata kodu (pcm.h sözleşmesi)
}

// SND_PCM_FORMAT_FLOAT_LE=14 · SND_PCM_ACCESS_RW_INTERLEAVED=3 · 48 kHz · stereo
$err = $ffi->snd_pcm_set_params($pcm, 14, 3, 2, 48000, 0, 10667 /* µs ≈ 512/48k */);
if ($err < 0) {
    $ffi->snd_pcm_close($pcm);
    exit(1);
}

$frames = 512;
$buf = FFI::new("float[" . ($frames * 2) . "]"); // interleaved stereo float32
// ... $buf doldurulur (DDS/dosya/sentez — §2-§4 I/O ile aynı kural: I/O yalnız servis katmanında)
$n = $ffi->snd_pcm_writei($pcm, $buf, $frames);
if ($n < 0) {
    $ffi->snd_pcm_recover($pcm, $n, 0); // §10.7/8 kuralı: yeniden open DEĞİL recover
}

$ffi->snd_pcm_close($pcm);
```

**Sınır:** bu kalıp Linux'tur (`.so`); macOS `.dylib` / Windows `.dll` farklı kütüphane
adı ister. Gerçek zamanlı callback PHP'den **yapılamaz** — PHP userland'de OS callback
seviyesinde ses thread'i yoktur (AAudio `dataCallback` §20 · CoreAudio IOProc §11.2
karşılığı PHP'de **yoktur**).

### §25.3 PHP + Windows/WASAPI — COM sınırı ve winmm alternatifi

**COM yolu (sınırlı):** PHP `COM` sınıfı (php.net/manual/en/book.com.php · HTTP 200)
yalnızca **IDispatch** tabanlı COM nesnelerini otomatize eder. WASAPI'nin birincil
arayüzleri `IAudioClient` / `IAudioRenderClient` ise **saf COM vtable (IUnknown)**
arayüzleridir (§17 kaynakçası). **KANIT (2026-10-14):** learn.microsoft.com
`/en-us/windows/win32/api/audioclient/nn-audioclient-iaudioclient` (HTTP 200 fetch):
*"The IAudioClient interface inherits from the IUnknown interface"* — miras
zincirinde IDispatch **yok**; PHP COM (yalnız IDispatch otomasyonu) erişemez.
Sonuç: **PHP COM ile
WASAPI doğrudan kullanılamaz** → PHP'nin Windows sesi pratikte iki yolla alınır:

1. **Harici süreç:** `shell_exec('ffmpeg -f dshow -i audio="..." out.wav')` (girdi) ve
   `shell_exec('ffplay -nodisp out.wav')` (çıkış) — repo §1 kalıbının birebir devamı.
2. **FFI + winmm (eski dal) — ✅ ÇALIŞTIRILDI (2026-10-14):** ReactOS
   `winmm.spec` export'ları (§23) PHP FFI'dan canlı çağrıldı —
   `php -d extension=ffi C:\temp\opencode\ffi-winmm-test.php`
   (PHP 8.5.8 NTS VC22 x64): `waveOutGetNumDevs() = 12` → **12 ses çıkış cihazı
   sayıldı** · çıkış kodu 0. Not: FFI bu Windows NTS build'inde **default yüklü
   değildir**; `php -d extension=ffi` veya php.ini `extension=ffi` gerekir:

```php
$ffi = FFI::cdef(
    'unsigned int waveOutOpen(void **phwo, unsigned int uDeviceID,
         void *pwfx, unsigned int dwCallback, unsigned int dwInstance,
         unsigned int dwFlags);
     unsigned int waveOutWrite(void *hwo, void *pwh, unsigned int cbwh);
     unsigned int waveOutClose(void *hwo);',
    'winmm.dll'
);
```

> İmzalar §23'teki `winmm.spec` satırlarından türetildi
> (`waveOutOpen(ptr long ptr long long long)` · `waveOutWrite(ptr long long)` ·
> `waveOutClose(long)`). `waveOut` WASAPI öncesi klasik daldır; düşük gecikme hedefi
> (§17) için PHP'de **tek doğrudan yol yoktur**.

### §25.4 PHP + macOS/iOS — CoreAudio karşılığı yok, süreç yolu var

PHP'nin CoreAudio'ya (`.dylib` FFI dahil) doğrudan örneği **bu turda doğrulanamadı**
(⚠️ VERIFICATION REQUIRED). Pratik yol: `shell_exec('afplay /path/file.mp3')` —
`afplay` macOS yerleşik CLI çalarıdır; **resmi Apple dokümanı bu turda bulunamadı**
(2026-10-14 websearch: yalnız üçüncü taraf kaynaklar) → ⚠️ VERIFICATION REQUIRED.
iOS'ta PHP runtime **yoktur** — sunucu PHP + tarayıcı (§25.1 yol 2).

### §25.5 PHP + Android / Tizen / webOS / ReactOS

| OS | PHP var mı? | PHP ses yolu | Kanıt |
|---|---|---|---|
| Android | cihazda PHP runtime yok (resmi Android dağıtımında PHP yok) | sunucu PHP → HTTP → tarayıcı/WebView (§25.1 yol 2) | §20 · ⚠️ cihazda PHP iddiası genel bilgi — resmi kaynak bu turda çekilmedi |
| Tizen | Tizen uygulama katmanı C/JS (§21 `sound_manager` C API'si) | sunucu PHP → Luna/JS değil, doğrudan cihazda PHP **yok** → HTTP | §21 |
| webOS | webOS uygulama katmanı JS + Luna bus (§22) | sunucu PHP → Luna çağıran JS'e payload | §22 |
| ReactOS | Windows PE uyumluluğu → resmi PHP Windows binary'leri teorik çalışır | §25.3 winmm/FFI veya ffmpeg süreci | §23 · ⚠️ ReactOS+PHP bu turda test edilmedi |

### §25.6 PHP anti-pattern — NE YAPILMAZ

| # | Yasak | Sonuç | Kural |
|---|---|---|---|
| 1 | Ses callback'i beklemek (PHP'de OS callback thread'i) | PHP'de böyle API yok | §25.2 sınırı |
| 2 | `shell_exec` ile `pcm5122` / belirli DAC seçimi | ADR-038 H001 ihlali | kök AGENTS.md kural 6 |
| 3 | `unserialize(user_input)` — ses metadata yolunda dahi | güvenlik ihlali | global AGENTS.md §5 |
| 4 | FFI hata kodunu (`<0`) yok sayıp devam | sessiz xrun / cihaz bozulması | §25.2 · §10.7 |
| 5 | PHP'de gerçek zamanlı eşik (≤10,67 ms) iddiası | PHP GC/tick gerçek zamanlı garantisi vermez | §13 eşik 2 · §25.2 sınırı |

### §25.7 Bu tur PHP doğrulama kaydı (2026-10-14)

| # | İddia | URL | Sonuç |
|---|---|---|---|
| 1 | PHP FFI kitabı (7.4+) | https://www.php.net/manual/en/book.ffi.php | ✅ 200 |
| 2 | PHP FFI kurulumu | https://www.php.net/manual/en/ffi.setup.php | ✅ 200 |
| 3 | PHP COM sınıfı (Windows) | https://www.php.net/manual/en/book.com.php | ✅ 200 |
| 4 | `shell_exec` | https://www.php.net/manual/en/function.shell-exec.php | ✅ 200 |
| 5 | `exec` | https://www.php.net/manual/en/function.exec.php | ✅ 200 |
| 6 | `aplay` (alsa-utils man) | https://manpages.debian.org/bookworm/alsa-utils/aplay.1.en.html | ✅ 200 |
| 7 | PHP ses uzantıları kategorisi | https://www.php.net/manual/en/refs.audio.php | ❌ **404 — negatif kanıt** (PHP'de ses uzantısı kategorisi YOK) |
| 8 | `afplay` resmi kaynağı | apple.com / developer.apple.com | ❌ **bulunamadı** (yalnız üçüncü taraf) → ⚠️ |
| 9 | ALSA C imzaları (FFI için) | raw.githubusercontent.com/alsa-project/alsa-lib/master/include/pcm.h | ✅ fetch · `snd_pcm_open/close/set_params/writei/recover` birebir alındı |
| 10 | `google/ndk-samples` → `android/ndk-samples` taşıma | github.com (HEAD ×2) | ✅ `android/ndk-samples` **200** · `google/ndk-samples` **404** → **taşıma DOĞRULANDI** |
| 11 | IAudioClient IUnknown mirası (§25.3) | learn.microsoft.com/.../audioclient/nn-audioclient-iaudioclient | ✅ 200 fetch · "inherits from the IUnknown interface" — COM sınırı kanıtı |
| 12 | PHP FFI → winmm.dll canlı testi (§25.3 yol 2) | yerel: `php -d extension=ffi` · PHP 8.5.8 NTS VC22 x64 | ✅ `waveOutGetNumDevs() = 12` · ReactOS spec imzalarıyla birebir · exit 0 |
| 13 | webOS ses metot envanteri (§22/W1) | yerel gövde kopya `webos-audio.html` (62.708 B) | ✅ yalnız `setMuted/volumeUp/volumeDown` (See also ağacı dâhil) — `getVolume/setVolume` **YOK** (negatif kanıt) |
| 14 | AAudio min-OS (API 26) iddiası | /ndk/guides/audio/aaudio/aaudio + ana sayfa | ❌ 2× GET · `Android 8`/`API 26` deseni **0 isabet** → anayasa ⚠️ KALIR (§P5) |
| 15 | Apple `AudioServerDriver` dokümanı (M4) | developer.apple.com/documentation/coreaudio/audioserverdriver | ❌ **404** (tekrar HEAD 2026-10-14) → negatif kanıt güçlendi |

---

## §26 Geniş Dil Seti — JS · C · Python · Go · Rust · C# (2026-10-14)

> **Premis:** PHP (§25) dışında kalan 6 dilin OS ses katmanına erişim gerçeği. CoreMusic
> repo'su bu dillerin **hiçbirini** kullanmaz (repo: PHP 8.x + Vanilla JS + MySQL);
> bu bölüm **hedef yüzey / upstream** etiketli referanstır. Her iddia resmi URL ile
> doğrulanmıştır. Erişim yolları §25.1'in 5 yoluyla (süreç · HTTP · FFI · COM · araç)
> aynı kategorilere oturur.

### §26.1 Dil × OS erişim yolları matrisi

| Dil | Birincil yol | OS kapsamı | Kanıt durumu |
|---|---|---|---|
| **JS** (tarayıcı) | Web Audio API → `AudioContext`/`<audio>` → OS ses yığını | **hepsi** (tarayıcı işletir) | ✅ W3C TR 200 |
| **JS** (Node.js) | yerleşik ses API'si **YOK** → native addon (binding) veya süreç | addon olan her yer | ✅ negatif (nodejs.org/api/audio.html **404**) |
| **C** | OS'un kendi API'si — bu dosyadaki §16–§23 tüm upstream alıntıları zaten C | 7/7 | ✅ §17–§23 (birebir alıntı) |
| **Python** | pyalsaaudio (ALSA doğrudan) · sounddevice (PortAudio) | Linux · çapraz | ✅ 2×200 |
| **Go** | portaudio (cgo + PortAudio) · oto (cgo'suz, PulseAudio→ALSA fallback) | Linux · Windows · macOS · Android · iOS | ✅ 4×200 |
| **Rust** | cpal (platform backend: AAudio · ALSA · CoreAudio · WASAPI) | 6+ OS | ✅ 2×200 · README matrisi |
| **C#** | NAudio (Windows: WASAPI/DirectSound) · SoundPlayer (winmm.dll) | Windows ağırlıklı | ✅ 2×200 · README |

### §26.2 JavaScript — Web Audio API + Node.js sınırı

**Tarayıcı JS — Web Audio API (tek resmi JS ses yüzeyi):**
Kanal: W3C Candidate Recommendation — https://www.w3.org/TR/webaudio/ (HTTP 200 · 2026-10-14).
7 OS'un hepsinde **aynı API**; OS ses donanımını **tarayıcı işletir** (PHP §25.1 yol 2
ile aynı katman). Sınıf/sözleşme: `AudioContext` · `createBufferSource` ·
`createOscillator` · `createDynamicsCompressor` — tarayıcı-özel, OS-düz API **değil**.

**Node.js — yerleşik ses API'si YOK:**
Kanıt: https://nodejs.org/api/audio.html → **HTTP 404** (negatif kanıt · 2026-10-14) —
Node.js API referansında "audio" başlığı **yoktur**. Node'da ses için iki gerçek yol:
(a) native addon (binding → PortAudio/ALSA/winmm — PHP FFI §25.2/§25.3 ile **aynı
sınırlar**), (b) `child_process.exec` ile CLI çalar (`aplay`/`afplay` — PHP
`shell_exec` §25.1 yol 1 ile birebir aynı kalıp). **webOS notu:** cihaz JS'i Luna Bus
kullanır (§22) — bu tarayıcı JS'i değil, cihaz-özel Web Runtime'dır.

### §26.3 C — upstream'in kendisi (yeni doğrulama gerekmez)

C, 7 OS'un **native** dilidir; bu dosyadaki tüm upstream alıntılar zaten C kodudur:
Windows/WASAPI `IAudioClient`/`IAudioRenderClient` (§17) · Linux/ALSA
`snd_pcm_open`/`snd_pcm_writei`/`snd_pcm_recover` (§10 · §18 · §25.2 pcm.h) ·
macOS/iOS CoreAudio `AudioDeviceIOProc`/`AudioObjectID` (§11 · §19) ·
Android/AAudio `AAudioStreamBuilder`/`AAudioStream_write` (§20) ·
Tizen `audio_out_create_new`/`audio_out_write` (§21) ·
ReactOS `waveOutOpen`/`waveOutWrite` (§23 winmm.spec).
C satırı = kaynak satırıdır; §26'da kopyalama yok, **pointer** var.

### §26.4 Python — iki gerçek yol

**1. pyalsaaudio — ALSA'ya doğrudan Python binding'i:**
https://larsimmisch.github.io/pyalsaaudio/ (HTTP 200 · 2026-10-14) · PyPI `pyalsaaudio`.
Sınıf: `alsaaudio.PCM` · `pcm.write(bytes)` — ALSA `snd_pcm_writei` sarmalı.
**Sınır: yalnız Linux** (bağımlılık: `libasound` — PHP FFI §25.2 ile aynı .so).

**2. sounddevice — PortAudio binding'i (çapraz-platform):**
https://python-sounddevice.readthedocs.io/ (HTTP 200 · 2026-10-14) · PyPI `sounddevice`.
PortAudio arkasında ALSA/CoreAudio/WASAPI'yi **PortAudio yönetir** (dil değil
kütüphane OS seçer). API: `sd.play(data, samplerate)` · `sd.rec(...)` · `OutputStream`.
**Negatif kanıt:** eski konum `pythonhosted.org/sounddevice/` → **404**.

### §26.5 Go — cgo PortAudio veya cgo'suz oto

**1. gordonklaus/portaudio — cgo binding:**
https://github.com/gordonklaus/portaudio (HTTP 200 · 2026-10-14) ·
https://pkg.go.dev/github.com/gordonklaus/portaudio (HTTP 200 · 2026-10-14).
README (birebir): *"This package provides an interface to the PortAudio audio I/O
library"* · **cgo zorunlu** · build için PortAudio development headers/libs gerekli.
API: `portaudio.OpenDefaultStream`.

**2. hajimehoshi/oto — cgo'suz, saf Go (dinamik yükleme):**
https://github.com/hajimehoshi/oto (HTTP 200 · 2026-10-14).
README (birebir): *"When no PulseAudio server is reachable, Oto falls back to ALSA.
This fallback also requires no Cgo: `libasound.so.2` is loaded dynamically at runtime,
so no ALSA development headers are needed."* — Go'nun kendi FFI'siyle PHP §25.2
`FFI::cdef('libasound.so.2')` kalıbının **aynı mekanizması**. Windows tarafı
DirectSound/waveOut = PHP winmm §25.3 ile aynı DLL katmanı. API: `oto.NewContext` ·
`oto.NewPlayer`.

### §26.6 Rust — cpal (platform backend matrisi, README birebir)

**cpal — RustAudio (tek çapraz-platform crate):**
https://docs.rs/cpal (HTTP 200 · 2026-10-14) · https://github.com/RustAudio/cpal
(HTTP 200 · 2026-10-14 · README fetch). README platform tablosu (birebir alıntı):

| OS | cpal birincil backend | README alternatifleri |
|---|---|---|
| Android | **AAudio** | — |
| Linux | **ALSA** | JACK · PipeWire · PulseAudio |
| BSD | ALSA | JACK · PipeWire · PulseAudio |
| macOS · iOS · tvOS | **CoreAudio** | macOS: JACK |
| Windows | **WASAPI** (README: DirectSound ile birlikte listeli) | — |

API: `cpal::default_host()` · `device.default_output_config()` · `build_output_stream`.
Windows WASAPI backend'i = NAudio/C# ile aynı API yüzeyi, PHP FFI §25.3 ile aynı
COM sınırı (IAudioClient saf vtable).

### §26.7 C# — NAudio (WASAPI/DirectSound) + SoundPlayer (winmm)

**1. NAudio — Windows .NET ses kütüphanesi:**
https://github.com/naudio/NAudio (HTTP 200 · 2026-10-14 · README fetch).
README (birebir): *"DirectSound · WASAPI · Record system audio with WASAPI Capture"*.
Sınıflar: `NAudio.Wave.WaveOutEvent` · `NAudio.Wasapi.WasapiOut`. **COM farkı:**
C# COM'a `[ComImport]` vtable-binding ile girer (IDispatch **değil**) → §25.3'te PHP
COM'un erişemediği `IAudioClient`'e C#'tan **doğrudan erişim vardır** — dil farkı
OS API derinliğini belirler.

**2. `System.Media.SoundPlayer` — .NET yerleşik (winmm.dll):**
https://learn.microsoft.com/en-us/dotnet/api/system.media.soundplayer (HTTP 200 · 2026-10-14).
`SoundPlayer.Play()` / `PlaySync()` arka planda `winmm.dll` `waveOut*` çağırır
(ReactOS `winmm.spec` §23 export'ları = PHP FFI §25.3 çağrısı ile **aynı**).
**Sınır:** yalnız Windows · yalnız WAV · gerçek zamanlı callback **yok**.

### §26.8 Anti-pattern — geniş dil seti

| # | Yasak | Sonuç | Kural |
|---|---|---|---|
| 1 | Node.js'te yerleşik ses API'si beklemek | 404 negatif kanıtla **yok** | §26.2 |
| 2 | Go'da cgo olmadan PortAudio binding'i | cgo PortAudio için **zorunlu** | §26.5/1 |
| 3 | Python `sounddevice`'ı süreç döngüsüyle gerçek zamanlı sanmak | PHP §25.6/5 ile aynı GC/tick sınırı | §26.4/2 |
| 4 | C#'ta `SoundPlayer` ile ≤10,67 ms hedefi | winmm tek seferlik yükleme, callback yok | §26.7/2 · §25.6/5 |
| 5 | Tarayıcı JS'i ile donanım-düz ses beklemek | Web Audio API OS sesini **çağırır**, işletmez | §26.2 |
| 6 | Rust `cpal` stream'ini lock (Mutex) ile beslemek | callback real-time — §eşik 10,67 ms ihlali | §26.6 · kök AGENTS.md §5 |

### §26.9 Geniş dil seti doğrulama kaydı (2026-10-14)

| # | Dil | İddia | URL | Sonuç |
|---|---|---|---|---|
| 1 | JS | Web Audio API (W3C) | https://www.w3.org/TR/webaudio/ | ✅ 200 |
| 2 | JS | Node.js'te yerleşik ses API'si | https://nodejs.org/api/audio.html | ❌ **404 — negatif kanıt** |
| 3 | Python | pyalsaaudio (ALSA binding) | https://larsimmisch.github.io/pyalsaaudio/ | ✅ 200 |
| 4 | Python | sounddevice (PortAudio) docs | https://python-sounddevice.readthedocs.io/ | ✅ 200 |
| 5 | Python | sounddevice (eski PyPI konumu) | https://pythonhosted.org/sounddevice/ | ❌ **404 — negatif kanıt** |
| 6 | Go | gordonklaus/portaudio (cgo) | https://github.com/gordonklaus/portaudio | ✅ 200 · README fetch |
| 7 | Go | pkg.go.dev dokümanı | https://pkg.go.dev/github.com/gordonklaus/portaudio | ✅ 200 |
| 8 | Go | hajimehoshi/oto (cgo'suz ALSA fallback) | https://github.com/hajimehoshi/oto | ✅ 200 · README fetch |
| 9 | Rust | docs.rs/cpal | https://docs.rs/cpal | ✅ 200 |
| 10 | Rust | RustAudio/cpal (backend matrisi) | https://github.com/RustAudio/cpal | ✅ 200 · README fetch |
| 11 | C# | NAudio (WASAPI/DirectSound) | https://github.com/naudio/NAudio | ✅ 200 · README fetch |
| 12 | C# | SoundPlayer (winmm.dll) | https://learn.microsoft.com/en-us/dotnet/api/system.media.soundplayer | ✅ 200 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
