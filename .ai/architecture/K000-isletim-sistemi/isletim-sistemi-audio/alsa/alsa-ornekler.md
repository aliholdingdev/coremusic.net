---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/alsa/alsa-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio/alsa
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-audio/alsa/alsa-anayasa
  - K000-isletim-sistemi/isletim-sistemi-audio/alsa/alsa-surucu
evidence:
  - https://www.alsa-project.org/wiki/Asoundrc
  - https://manpages.debian.org/bookworm/alsa-utils/aplay.1.en.html
  - https://manpages.debian.org/bookworm/alsa-utils/amixer.1.en.html
  - https://manpages.debian.org/bookworm/alsa-utils/alsactl.1.en.html
  - https://www.alsa-project.org/alsa-doc/alsa-lib/group___p_c_m.html
validation:
  - "2026-10-13 · 6 kaynak GET 200 · envanter kesişimi 0 · yapılandırma blokları 39 `<pre>` arasından birebir"
  - "repo kanıtı: `shared/src/AI/AIEngine.php` ve `home.coremusic.net/include/Stream/MusicStreamHandler.php` satırları `Select-Object -Index` ile okundu"
open_questions:
  - "Projenin hedef `asound.conf`'u henüz yazılmadı → PLANNED"
  - "48 kHz geometrisi için `slave` bloğu doğrulanmadı → ⚠️ VERIFICATION REQUIRED"
group: kod
---

# ALSA Örnekler — Yapılandırma · CLI · Çağrı Dizisi · Repo Kanıtı

> **Bu dosya ne değildir:** Üretim yapılandırması veya çalıştırılabilir kurulum
> paketi değildir. Buradaki bloklar **belgelerden birebir alınmış örneklerdir**;
> CoreMusic'e ait oldukları iddia edilmez. Repo'ya ait satırlar ise gerçek
> dosyadan okunmuştur ve kaynak dosyayla eşleşir.

---

## §0 ELI15 — bu dosya ne taşır

ALSA'da "örnek" üç katmanda yaşar: (1) `.asoundrc` yapılandırma blokları —
hangi aygıtın varsayılan olduğu, kimin mikserlediği; (2) komut satırı —
`aplay`/`amixer`/`alsactl` ile hızlı deneme; (3) kütüphane çağrı dizisi —
uygulama kodunun izlediği `open → hw_params → … → close` yolu. Bu dosya üçünü
de örnekler + doğrulanmış repo kanıtıyla taşır. (Kaynak: §2 · §3 · §4 · §5)

---

## §1 Repo kanıtı — CoreMusic ALSA kullanmıyor (2026-10-13)

### §1.1 Tarama sonucu

| Desen | Kapsam | İsabet |
|---|---|---|
| `snd_` · `alsa` · `aplay` · `amixer` · `alsactl` | repo PHP/JS/CSS/SQL kaynakları | **0** → "Repo'da ALSA kodu YOK" |

### §1.2 CoreMusic sesi nasıl işliyor — gerçek satırlar

`Dosya: shared/src/AI/AIEngine.php` (satır 288-295, `Select-Object -Index 287..294`):

```php
    private function extractMetadata(string $filePath): array
    {
        $cmd = sprintf(
            'ffprobe -v quiet -print_format json -show_format -show_streams "%s"',
            escapeshellarg($filePath)
        );

        $output = shell_exec($cmd);
```

| Gözlem | Sonuç |
|---|---|
| Ses kaynağı | dosya (ffprobe ile metadata) — donanım erişimi **yok** |
| Komut | `ffprobe … -show_format -show_streams` |
| Güvenlik | `escapeshellarg($filePath)` ile argüman kaçışlı |

`Dosya: shared/src/AI/AIEngine.php` (satır 102-104, `Select-Object -Index 101..103`):

```php
            'sample_rate'   => $metadata['sample_rate'] ?? 44100,
            'bit_depth'     => $metadata['bit_depth'] ?? 16,
            'channels'      => $metadata['channels'] ?? 2,
```

| Varsayılan | Değer | Not |
|---|---|---|
| `sample_rate` | 44100 | ffprobe gelmezse düşülen değer |
| `bit_depth` | 16 | ffprobe gelmezse düşülen değer |
| `channels` | 2 | ffprobe gelmezse düşülen değer |

> **Çarpışma notu:** sartname hedefi 48 kHz iken repo varsayılanı 44100'dür —
> bu bir **gözlem**, düzeltme değil (kod değişikliği bu görevin dışıdır).

`Dosya: home.coremusic.net/include/Stream/MusicStreamHandler.php` (satır 46,
`Select-Object -Index 45`):

```php
    private const CHUNK_BYTES = 1048576; // 1 MiB
```

`Dosya: home.coremusic.net/include/Stream/MusicStreamHandler.php` (satır 91-106,
`Select-Object -Index 90..105`):

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

| Satır | Ne yapar | İlgisi |
|---|---|---|
| 46 | 1 MiB'lık chunk sabiti | akış kesme birimi |
| 91-106 | HTTP Range (416/206/200) | tarayıcıya parça parça teslim |
| 310-329 | `emitBaseHeaders` — `Accept-Ranges: bytes` · `Content-Length` · `ETag` · `nosniff` | başlık yüzeyi |
| 331-334 | `sendBody` — `fopen($path, 'rb')` | dosyadan gövde okuma |

`Dosya: home.coremusic.net/include/Stream/MusicStreamHandler.php` (satır 310-329,
`Select-Object -Index 309..328` — özet):

| Başlık | Değer |
|---|---|
| `Accept-Ranges` | `bytes` |
| `Cache-Control` | `private, max-age=0, must-revalidate` |
| `X-Content-Type-Options` | `nosniff` |
| `Content-Type` | mime (parametre) |

**Sonuç:** CoreMusic'in ses yolu **dosya → HTTP Range → tarayıcı (HTML5 `<audio>`)**;
ALSA/CoreAudio/WASAPI hiçbirinde doğrudan kullanılmıyor → bu dal dokümantasyonu
**gelecek** yüzeyi (Neva Engine) tarif eder.

---

## §2 CLI örnekleri (belgelerden)

### §2.1 Man sayfaları — NAME/SYNOPSIS (birebir)

| Araç | NAME satırı (birebir) | SYNOPSIS (birebir) | Kaynak |
|---|---|---|---|
| `aplay`/`arecord` | "arecord, aplay - command-line sound recorder and player for ALSA soundcard driver" | `arecord [ flags ] [filename]` · `aplay [ flags ] [filename [filename]] ...` | [M1] |
| `amixer` | "amixer - command-line mixer for ALSA soundcard driver" | `amixer [ -option ] [ cmd ]` | [M2] |
| `alsactl` | "alsactl - advanced controls for ALSA soundcard driver" | `alsactl [ options ] [ store \| restore \| init ] <card # or id or device>` · `alsactl monitor <card # or id>` | [M3] |

> Sürüm bağlamı: Debian bookworm `alsa-utils` **1.2.8-1** (sayfanın "other versions"
> satırı — 2026-10-13).

### §2.2 Belgedeki çalıştırma örnekleri (birebir, [A1] `<pre>` blokları)

```
aplay -D default test.wav
```

```
aplay -D hw:0,0 test.wav
```

```
aplay -D"plug:'dmix:RATE=44100'" test.wav
```

```
aplay -Dplug:\'dmix:SLAVE=\"hw:1,0\",RATE=44100\' test.wav
```

| Örnek | Ne dener | Katman |
|---|---|---|
| `-D default` | yapılandırılmış varsayılan PCM | anayasa §6 |
| `-D hw:0,0` | doğrudan kart 0 · aygıt 0 | exclusive'e yakın yol |
| `plug:'dmix:RATE=44100'` | plug üzerinden dmix + oran zorlama | yazılım mikseri |
| `SLAVE="hw:1,0",RATE=48000` | slave aygıt + 48 kHz (birebir satır) | [A1] pre#28 |

### §2.3 Teshis komutları (araç varlığı doğrulanmış; örnek satır bu turda yok)

| Amaç | Araç | Kanıt |
|---|---|---|
| Çalma/kayıt | `aplay` · `arecord` | [M1] NAME ✅ |
| Mikser okuma/yazma | `amixer` | [M2] NAME ✅ |
| Kalıcı ayar (store/restore/init) | `alsactl` | [M3] SYNOPSIS ✅ |
| Döngü/loopback | `alsaloop` | man sayfası HEAD 200 ✅ (içerik okunmadı → ⚠️) |
| MIDI kaydı | `aplaymidi` | man sayfası HEAD 200 ✅ (içerik okunmadı → ⚠️) |
| Aktarım aracı | `axfer` | man sayfası HEAD 200 ✅ (içerik okunmadı → ⚠️) |

### §2.4 Araç–görev eşlemesi (bu dalın okuma kılavuzu)

| Görev | Birincil araç | İkincil | Derinlik |
|---|---|---|---|
| Hızlı deneme (dosya çal) | `aplay` | — | [M1] |
| Ses seviyesi/MUX ayarı | `amixer` | `alsactl` (kalıcı) | [M2] · [M3] |
| Kart ayarını kalıcılaştırma | `alsactl store` | `alsactl restore` | [M3] SYNOPSIS |
| Hata ayıklama (proc) | `/proc/asound` | tracepoint | [D1] · [D6] (surucu §8.2) |
| Yapılandırma denemesi | `aplay -D <tanım>` | `.asoundrc` düzenlemesi | §3 |

---

## §3 `.asoundrc` yapılandırma blokları (birebir, [A1])

### §3.1 Dosya konumları

```
$HOME/.asoundrc
```

```
/usr/share/alsa/alsa.conf
```

```
/etc/asound.conf
```

### §3.2 Varsayılan aygıt — doğrudan donanım

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

| Anahtar | Anlam |
|---|---|
| `pcm.!default` | varsayılan **çalma/kayıt** tanımı (ezerek yeniden tanımlama) |
| `ctl.!default` | varsayılan **mikser kontrol** tanımı |
| `type hw` | doğrudan donanım — yazılım mikseri yok |
| `card 0` | birinci ses kartı |

### §3.3 `plug` — format uyumu katmanı

```
type plug             	# Format adjusted PCM
slave STR               # Slave name (see pcm_slave)
route_policy STR 	# route policy for automatic ttable generation
```

| `route_policy` değerleri (birebir) | Anlam |
|---|---|
| `default` | "copy policy, except for mono capture - sum" |
| `average` | "result is average of input channels" |
| `copy` | "only first channels are copied to destination" |
| `duplicate` | "duplicate first set of channels" |

### §3.4 Oran dönüştürme

```
pcm_slave.sl2 {
	pcm "hw:1,0"
	rate 48000
}

pcm.rate_convert {
	type rate
	slave sl2
}
```

### §3.5 Karmaşık dönüşüm (plug + slave)

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

### §3.6 dmix — çoklu çalma mikseri

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
```

### §3.7 dsnoop — çoklu kayıt mikseri

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

### §3.8 JACK köprüsü

```
pcm.jackplug {
	type plug
	slave { pcm "jack" }
}

pcm.jack {
	type jack
	playback_ports {
		0 alsa_pcm:playback_1
		1 alsa_pcm:playback_2
	}
	capture_ports {
		0 alsa_pcm:capture_1
		1 alsa_pcm:capture_2
	}
}
```

### §3.9 Çoklu aygıt (multi) — dört kanallık sanal cihaz

```
pcm.multi {
        type multi;
        slaves.a.pcm "hw:0,0";
        slaves.a.channels 2;
        slaves.b.pcm "hw:1,0";
        slaves.b.channels 2;
        bindings.0.slave a;
        bindings.0.channel 0;
        bindings.1.slave a;
        bindings.1.channel 1;
        bindings.2.slave b;
        bindings.2.channel 0;
        bindings.3.slave b;
        bindings.3.channel 1;
}
```

> [A1] blok yorumu (birebir): "create a virtual four-channel device with two
> sound devices" — "two interleaved stereo streams in different memory locations".

### §3.10 Blok matrisi — hangi örnek hangi soruyu yanıtlar

| # | Blok | Soru | Bölüm |
|---|---|---|---|
| 1 | konumlar | nereye yazılır? | §3.1 |
| 2 | `!default` + `hw` | doğrudan donanım nasıl? | §3.2 |
| 3 | `type plug` | format uyuşmazlığı nasıl çözülür? | §3.3 |
| 4 | `rate_convert` | oran dönüşümü nasıl? | §3.4 |
| 5 | `complex_convert` | format+kanal+oran birlikte? | §3.5 |
| 6 | `dmix` | iki uygulama aynı anda nasıl çalar? | §3.6 |
| 7 | `dsnoop` | iki uygulama aynı anda nasıl kaydeder? | §3.7 |
| 8 | `jack` | JACK'e nasıl bağlanır? | §3.8 |
| 9 | `multi` | iki kart tek cihaz gibi nasıl? | §3.9 |
| 10 | CLI örnekleri | nasıl denenir? | §2.2 |

---

## §4 alsa-lib çağrı dizisi (uygulama örneği iskeleti)

> **Dürüstlük notu:** aşağıdaki iskelet, çağrı adlarının **varlığı** [A4]'ten
> doğrulanmış, sıralaması ise anayasa §5'teki sentezdir; çalıştırılmadı →
> `⚠️ VERIFICATION REQUIRED` (A6).

| # | Adım | Çağrı | Dönüş/beğeni |
|---|---|---|---|
| 1 | aç | `snd_pcm_open` | kulp (`snd_pcm_t`) |
| 2 | donanım parametresi | `snd_pcm_hw_params` | başarısızsa geometriyi değiştirip tekrar |
| 3 | yazılım parametresi | `snd_pcm_sw_params` | periyot/uyanma politikası |
| 4 | hazırla | `snd_pcm_prepare` | — |
| 5 | başlat | `snd_pcm_start` | — |
| 6 | I/O döngüsü | `snd_pcm_wait` + okuma/yazma | XRUN → `snd_pcm_recover` |
| 7 | drenaj (bitiş) | `snd_pcm_drain` | tamponun boşalması |
| 8 | anlık durdurma | `snd_pcm_drop` | tampon atılır |
| 9 | kurtarma | `snd_pcm_recover` | hata sonrası |
| 10 | kapat | `snd_pcm_close` | kulp serbest |

| Bayrak (birebir) | Ne yapar |
|---|---|
| `SND_PCM_NONBLOCK` | bloklama yok — `EAGAIN` benzeri davranış |
| `SND_PCM_EINTR` | sinyal kesmesinde çağrı kesilebilir |
| `SND_PCM_ABORT` | hata durumunda kulpı kullanılamaz yap |
| `SND_PCM_NO_AUTO_RESAMPLE` | otomatik örnekleme dönüşümü kapalı |
| `SND_PCM_NO_AUTO_CHANNELS` | otomatik kanal dönüşümü kapalı |
| `SND_PCM_NO_AUTO_FORMAT` | otomatik format dönüşümü kapalı |
| `SND_PCM_NO_SOFTVOL` | yazılım ses seviyesi kapalı |

---

## §5 Anti-pattern'ler (belge gözleminden)

| # | Anti-pattern | Sonuç | Dayanak |
|---|---|---|---|
| 1 | `type hw` ile `!default` yapıp mikser beklemek | ses seviyesi kontrolleri görünmeyebilir (donanım kontrolü dışarıda) | [A1] §3.2 · [M2] |
| 2 | `ipc_key`'i kopyalayarak ikinci dmix kurmak | çakışma riski — belge "must be unique for all dmix plugins!!!!" der | [A1] §3.7 |
| 3 | Oran/kanal uyuşmazlığını `plug` olmadan `hw:` üstünden zorlamak | `hw_params` reddi olası (müzakere başarısız) | [A4] hw_params üyeliği |
| 4 | XRUN sonrası `recover` çağırmamak | akış `SNDRV_PCM_STATE_XRUN`'da kalır | [K3] (surucu §6) |
| 5 | Periyot bildirimini uygulama tarafında çoğaltmak | "call this only once" kuralı ihlali | [K2] (surucu §5.2) |
| 6 | CoreMusic varsayılanını (44100) sartname (48000) sanmak | ölçüm/uyum hatası | repo §1.2 · sartname |

---

## §6 Eşik ve ölçüm tablosu

| Büyüklük | Hesap | Değer | Not |
|---|---|---|---|
| Blok süresi | 512 / 48000 | **10,667 ms** | sartname: 512 örnek · 48 kHz (sabit hesap) |
| 1 saniyedeki blok sayısı | 48000 / 512 | **93,75** | tam bölünme yok — sayaç/tasarım dikkati |
| `period_size` örneği (dmix bloğu) | [A1] | 1024 örnek | belge örneği, sartname değil |
| `buffer_size` örneği (dmix bloğu) | [A1] | 4096 örnek | belge örneği |
| Örnek oranı (belge örneği) | [A1] dmix slave | 44100 Hz | repo varsayılanıyla aynı, sartname değil |

> **Ölçüm durumu:** gerçek cihaz gecikmesi/XRUN sayımı **PLANNED** — sayılar
> yalnız sabit hesap ve belge değerleridir, ölçüm iddiası taşımaz.

### §6.1 Ölçüm kapıları (bu turda çalıştırılmadı)

| # | Kapı | Yöntem | Kabul |
|---|---|---|---|
| 1 | Blok süresi doğrulama | `aplay` ile 48 kHz/512 kaydı + `alsactl monitor` | 10,667 ms ± ölçüm hatası |
| 2 | XRUN sayacı | `CONFIG_SND_DEBUG` + tracepoint | test boyunca 0 XRUN |
| 3 | Eşzamanlılık | ikinci `hw:` istemcisi | beklenen hata/izin davranışı |
| 4 | 44100 → 48000 uyumu | `complex_convert` benzeri blok | `hw_params` kabulü |
| 5 | Kalıcılık | `alsactl store` → reboot → `restore` | ayarlar korunur |

---

## §7 Web kanıt matrisi

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| `.asoundrc` blokları (bu dosya §3 tamamı) | https://www.alsa-project.org/wiki/Asoundrc | 2026-10-13 | ✅ GET 200 · 39 `<pre>` |
| `aplay` NAME/SYNOPSIS | https://manpages.debian.org/bookworm/alsa-utils/aplay.1.en.html | 2026-10-13 | ✅ GET 200 |
| `amixer` NAME/SYNOPSIS | https://manpages.debian.org/bookworm/alsa-utils/amixer.1.en.html | 2026-10-13 | ✅ GET 200 |
| `alsactl` NAME/SYNOPSIS | https://manpages.debian.org/bookworm/alsa-utils/alsactl.1.en.html | 2026-10-13 | ✅ GET 200 |
| alsa-lib PCM fonksiyon adları + bayraklar | https://www.alsa-project.org/alsa-doc/alsa-lib/group___p_c_m.html | 2026-10-13 | ✅ GET 200 |
| `alsaloop`/`aplaymidi`/`axfer` man sayfaları | `https://manpages.debian.org/bookworm/alsa-utils/{alsaloop,aplaymidi,axfer}.1.en.html` | 2026-10-13 | ✅ HEAD 200 · içerik okunmadı ⚠️ |

**Çakışma kontrolü:** §2-§7'deki URL'ler repo envanteri (2103 satır) ile
**kesişim 0**.

---

## §8 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| O1 | Projenin hedef `asound.conf` bloğu hangi geometriyle (512/48k) yazılacak? | PLANNED |
| O2 | `hw` (exclusive) mi `dmix` (paylaşımlı) mı? — ASIO Exclusive Lock karşılığı | ⚠️ VERIFICATION REQUIRED |
| O3 | 44100 varsayılanı ile 48000 sartname çelişkisi ne zaman kapatılacak? | ⚠️ VERIFICATION REQUIRED |
| O4 | `alsaloop`/`aplaymidi`/`axfer` içerikleri hangi turda doğrulanacak? | ⚠️ VERIFICATION REQUIRED |

---

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-4 |
| mimari | `alsa-anayasa.md` | Kimlik, katman mimarisi ve PCM modeli | Kademe-4 |
| mimari | `alsa-surucu.md` | Sürücü arayüzü, kesme yolu ve tasarım belgeleri | Kademe-4 |
| kod | `alsa-ornekler.md` | Yapılandırma, CLI ve çağrı dizisi örnekleri (bu dosya) | Kademe-4 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
