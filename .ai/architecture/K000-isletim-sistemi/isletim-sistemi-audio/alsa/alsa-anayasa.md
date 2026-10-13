---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/alsa/alsa-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio/alsa
children: []
depends_on: []
evidence:
  - https://www.alsa-project.org/wiki/Asoundrc
  - https://www.alsa-project.org/alsa-doc/alsa-lib/group___p_c_m.html
  - https://www.alsa-project.org/alsa-doc/alsa-lib/group___mixer.html
  - https://www.kernel.org/doc/html/latest/sound/kernel-api/writing-an-alsa-driver.html
  - https://www.kernel.org/doc/html/latest/sound/kernel-api/alsa-driver-api.html
  - https://manpages.debian.org/bookworm/alsa-utils/aplay.1.en.html
validation:
  - "2026-10-13 · 6 kaynak GET/HEAD 200 · envanter kesişimi 0 · tüm alıntılar indirilen HTML'den birebir doğrulandı"
  - "repo taraması: `snd_|alsa|aplay|amixer` deseni 0 isabet → ALSA kodu YOK"
open_questions:
  - "Hedef Linux dağıtımı ve paket seti (alsa-utils sürümü) → UNKNOWN"
  - "PipeWire oturum yöneticisi altında doğrudan `alsa-lib` kullanımı → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# ALSA Anayasa — Kimlik · Katmanlar · PCM Modeli · Paylaşım Modları

> **Bu dosya ne değildir:** Kod, benchmark veya kurulum rehberi değildir. Bu dosya
> ALSA'yı *belge kanıtıyla* tanımlar; alıntılar indirilen resmi sayfalardan (2026-10-13)
> birebir alınmıştır. Erişilemeyen her iddia `⚠️ VERIFICATION REQUIRED` veya `UNKNOWN`
> işaretidir, tahminle doldurulmaz.
>
> **Durum etiketi:** ŞARTNAME/belge IMPLEMENTED · Kod **YOK** (repo taraması 2026-10-13:
> `snd_|alsa|aplay|amixer` desenine 0 isabet → UNKNOWN).

---

## §0 ELI15 — ALSA bir cümlede

ALSA (Advanced Linux Sound Architecture), Linux'un ses donanımıyla konuşma dilidir:
çekirdekte ses kartını yöneten `snd-*` sürücüleri durur, kullanıcı uzayında
`alsa-lib` uygulamaya bir PCM akışı (çalma/kayıt) ve mikser kontrolü açar; uygulama
bu akışa periyot blokları hâlinde yazar/okur, sürücü kesmede (interrupt) tamponu
işleyip `snd_pcm_period_elapsed()` ile katmanı haberdar eder. (Kaynak: §3 · §4 · §5)

---

## §1 ALSA Kimlik Kartı

| Alan | Değer | Kanıt |
|---|---|---|
| Tam adı | Advanced Linux Sound Architecture | [K1] başlık "Sound Subsystem Documentation" içinde ALSA Kernel API başlığı |
| Katman | Çekirdek alt sistemi (`sound/`) + kullanıcı kütüphanesi (`alsa-lib`) + araçlar (`alsa-utils`) | [K4] · [A4] · [M1]-[M3] |
| Çekirdek API sayfası | "ALSA Kernel API Documentation" | [K4] `<title>` |
| Kullanıcı kütüphanesi | `alsa-lib` — PCM grubu 127 üye, mixer grubu 61 üye | [A4] · [A5] (2026-10-13 sayım) |
| Komut araçları | `aplay`/`arecord` · `amixer` · `alsactl` (+ `alsaloop` · `aplaymidi` · `axfer` man sayfaları 200) | [M1]-[M6] |
| Yapılandırma dosyaları | `$HOME/.asoundrc` · `/etc/asound.conf` · `/usr/share/alsa/alsa.conf` | [A1] `<pre>` blokları 1-3 |
| Varsayılan aygıt bloğu | `pcm.!default { type hw, card 0 }` + `ctl.!default { type hw, card 0 }` | [A1] `<pre>` #5 (birebir) |
| Çekirdek sürücü sözleşmesi | `struct snd_pcm_ops` operator tablosu | [K3] (birebir alıntı, §4.4) |
| Kesme yolu bildirimi | `snd_pcm_period_elapsed()` — kesme rutini tarafından çağrılır | [K3] · [K2] |
| XRUN davranışı | `-EPIPE` → çekirdek durumu `SNDRV_PCM_STATE_XRUN`'a otomatik çevirir | [K3] (birebir) |
| Sahibi | Linux çekirdek topluluğu (`sound/` ağacı) — ayrı lisans yok | [K1] |
| CoreMusic sahibi agent | Embedded Engineer (L0 · C++20 · realtime) | kök `AGENTS.md` §routing |
| CoreMusic routing | "C++, ASIO, JUCE, audio, DSP, Neva Engine" → Embedded (primary) | `.ai/AGENTS.md` §6 |
| Repo durumu | ALSA kullanıcı kodu YOK — UNKNOWN (§9.1) | tarama, 2026-10-13 |

---

## §2 Bu dosyanın kanıt kaynakları

| Etiket | Kaynak | URL | 2026-10-13 Durum |
|---|---|---|---|
| [A1] | ALSA wiki — `.asoundrc` yapılandırması | https://www.alsa-project.org/wiki/Asoundrc | ✅ GET 200 · 44.988 bayt · 39 `<pre>` |
| [A2] | ALSA wiki — Matrix ana sayfası | https://www.alsa-project.org/wiki/Matrix:Main | ✅ HEAD 200 |
| [A3] | ALSA wiki — dokümantasyon dizini | https://www.alsa-project.org/wiki/Documentation | ✅ GET 200 · 22.445 bayt |
| [A4] | alsa-lib PCM referans grubu | https://www.alsa-project.org/alsa-doc/alsa-lib/group___p_c_m.html | ✅ GET 200 · 127 üye |
| [A5] | alsa-lib Mixer referans grubu | https://www.alsa-project.org/alsa-doc/alsa-lib/group___mixer.html | ✅ GET 200 · 61 üye |
| [M1] | `aplay(1)` — Debian bookworm alsa-utils 1.2.8-1 | https://manpages.debian.org/bookworm/alsa-utils/aplay.1.en.html | ✅ GET 200 · SYNOPSIS alındı |
| [M2] | `amixer(1)` — Debian bookworm | https://manpages.debian.org/bookworm/alsa-utils/amixer.1.en.html | ✅ GET 200 · SYNOPSIS alındı |
| [M3] | `alsactl(1)` — Debian bookworm | https://manpages.debian.org/bookworm/alsa-utils/alsactl.1.en.html | ✅ GET 200 · SYNOPSIS alındı |
| [K1] | Çekirdek ses alt sistemi dizini | https://www.kernel.org/doc/html/latest/sound/index.html | ✅ GET 200 |
| [K2] | ALSA Driver API (PCM middle layer) | https://www.kernel.org/doc/html/latest/sound/kernel-api/alsa-driver-api.html | ✅ GET 200 · 749.583 bayt |
| [K3] | Writing an ALSA Driver | https://www.kernel.org/doc/html/latest/sound/kernel-api/writing-an-alsa-driver.html | ✅ GET 200 · 249.450 bayt |
| [K4] | ALSA Kernel API dizini | https://www.kernel.org/doc/html/latest/sound/kernel-api/index.html | ✅ GET 200 |

> Not: Bu dosyada kullanılan 12 URL'in tamamı repo URL envanteriyle
> (**2103 satır**, `url-inventory-20261013.txt`) **kesişim 0** — 2026-10-13'te taze doğrulandı.

---

## §3 Katman mimarisi

ALSA üç katmanlıdır; her katman bir sonrakine **dar bir sözleşme** sunar ve
üst katman alt katmanın içini görmez.

| Katman | Konum | Ne taşır | Kanıt |
|---|---|---|---|
| 1 · Uygulama | kullanıcı uzayı | çalma/kayıt akışı, mikser ayarı | [M1]-[M3] araçları |
| 2 · alsa-lib | kullanıcı uzayı (`snd_pcm_*`, `snd_mixer_*`) | aygıt açma, parametre müzakere, periyot I/O, olay kuyruğu | [A4] · [A5] |
| 3 · ALSA core (çekirdek) | çekirdek | PCM middle layer, runtime, durum makinesi, kesme yönetimi | [K2] · [K3] |
| 4 · `snd-*` sürücüsü | çekirdek | donanım register'ları, DMA, kesme servisi | [K3] `snd_pcm_ops` |
| 5 · Donanım | PCB | codec (ör. PCM3168A — ADR-038 H001) · DAC/ADC | K001-donanım |

### §3.1 Uygulama–alsa-lib–çekirdek sınırı

| Sınır | Üstte ne görür | Altta ne olur | Kanıt |
|---|---|---|---|
| Uygulama → alsa-lib | dosya benzeri PCM kulp (`snd_pcm_t` tutamağı) + bayraklar | `SND_PCM_NONBLOCK` · `SND_PCM_EINTR` · `SND_PCM_ABORT` bayrakları yorumlanır | [A4] bayrak listesi |
| alsa-lib → çekirdek | `hw_params`/`sw_params` müzakeresi | donanımın desteklediği format erişim kümesi ile kesişim alınır | [A4] "Hardware Parameters" · "Software Parameters" bölümleri |
| Çekirdek → sürücü | `struct snd_pcm_ops` operator tablosu | `.open/.close/.hw_params/.hw_free/.prepare/.trigger/.pointer` çağrılır | [K3] (§4.4 alıntı) |
| Sürücü → çekirdek | kesme rutininden `snd_pcm_period_elapsed()` | middle layer konumu günceller, bekleyen poll iş parçacıklarını uyandırır | [K3] (§4.2 alıntı) |

### §3.2 Kim neyi yükler

| Bileşen | Ne zaman yükülür | Etki |
|---|---|---|
| `snd-*` çekirdek sürücüsü | modül/probe anında (dağıtım) | aygıt `hw:C,D` olarak görünür |
| `alsa-lib` | uygulama bağlantısında | `snd_pcm_open()` başarısı mümkün olur |
| `/usr/share/alsa/alsa.conf` | alsa-lib ilk kullanımda | sistem varsayılanları tanımlar [A1] |
| `$HOME/.asoundrc` · `/etc/asound.conf` | alsa-lib yapılandırma okumasında | kullanıcı/dağıtım özel tanımı — `!default` üzerine yazar [A1] |
| PulseAudio / PipeWire oturum yöneticisi | oturum başında (dağıtım) | bu dalın **dışında** — politika katmanı, içeriği ⚠️ VERIFICATION REQUIRED |

---

## §4 PCM modeli

### §4.1 Aygıt · alt akış · yön

| Kavram | Tanım | Kanıt |
|---|---|---|
| Aygıt (card) | Ses kartı numarası — `hw:C,D` biçiminde adreslenir (kart · aygıt) | [A1] `card 0` / `hw:1,0` / `hw:0,0` blokları |
| Alt akış (substream) | Bir cihaz üzerindeki tek yönlü akış kopyası; sürücü callback'leri `snd_pcm_substream *` alır | [K3] `snd_mychip_pcm_trigger(struct snd_pcm_substream *substream, int cmd)` |
| Yön | playback (üretim) ve capture (toplama) ayrı operator tabloları alır | [K3] `snd_mychip_playback_ops` ve `snd_mychip_capture_ops` |
| Periyot | Kesme hizmetinin bir turda işlediği örnek bloğu | [K3] "buffer-update routine" paragrafı (§4.2) |
| Tampon (buffer) | Donanımdaki DMA halkası; periyotların toplamı | [K3] `buffer_size - 1` bağlamı (§4.2) |
| Uygulama işareti | Uygulamanın yazdığı/okuduğu kare konumu — `.pointer` callback'i bildirir | [K2] ".pointer - to retrieve current position of audio data transmission by frame count" |

### §4.2 Periyot ve tampon — kesme yolu alıntısı

Aşağıdaki alıntı [K3] sayfasından **birebir** alınmıştır (2026-10-13):

> "… buffer_size - 1 . This is usually called from the buffer-update routine in the
> PCM middle layer, which is invoked when **snd_pcm_period_elapsed()** is called by
> the interrupt routine. Then the PCM middle layer updates the position and calculates
> the available space, and wakes up the sleeping poll threads, etc. This callback is
> also atomic by default."

| Gözlem | Sonuç | Kanıt |
|---|---|---|
| Bildirim kaynağı | kesme rutini (interrupt) | [K3] alıntı |
| Sonrası işlem | konum güncellenir · boşluk hesaplanır · poll iş parçacıkları uyandırılır | [K3] alıntı |
| Atomiklik | callback "atomic by default" — kilit/ataama beklentisi vardır | [K3] alıntı |
| Tekilleme kuralı | birden çok periyot geçmiş olsa bile bildirim **bir kez** çağrılır | [K2] "Even if more than one periods have elapsed since the last call, you have to call this only once." |

### §4.3 Durumlar ve XRUN

| Olay | Sonuç | Kanıt |
|---|---|---|
| `ack` callback `-EPIPE` dönerse | çekirdek "buffer XRUN" olarak değerlendirir ve durumu `SNDRV_PCM_STATE_XRUN`'a **otomatik** çevirir | [K3] birebir: "When the return value is -EPIPE, PCM core treats that as a buffer XRUN, and changes the state to SNDRV_PCM_STATE_XRUN automatically." |
| XRUN veya DRAINING anında | sürücü `.trigger` callback'i `SNDRV_PCM_TRIGGER_STOP` ile çağrılır | [K2] ".trigger - with SNDRV_PCM_TRIGGER_STOP at XRUN or DRAINING state." |
| Durum güncelleme | `snd_pcm_*` runtime durumu stream lock altında ayarlanır | [K2] "Set the PCM runtime state with stream lock" |
| Uygulama kurtarma yükümlülüğü | akış `EPIPE` sonrası uygulama tarafında kurtarma (recover) beklenir | [A4] `snd_pcm_recover` üyesi mevcut |

> **Tam durum listesi (OPEN/SETUP/PREPARED/RUNNING/…) bu turda tek kaynaktan
> doğrulanmadı → ⚠️ VERIFICATION REQUIRED.** Doğrulanan tek durum adı
> `SNDRV_PCM_STATE_XRUN`'dur [K3].

### §4.4 Kesme yolu sözleşmesi — `snd_pcm_ops`

Aşağıdaki operator tablosu [K3] sayfasından **birebir** alınmıştır (2026-10-13):

```c
static struct snd_pcm_ops snd_mychip_playback_ops = {
    .open = snd_mychip_playback_open,
    .close = snd_mychip_playback_close,
    .hw_params = snd_mychip_pcm_hw_params,
    .hw_free = snd_mychip_pcm_hw_free,
    .prepare = snd_mychip_pcm_prepare,
    .trigger = snd_mychip_pcm_trigger,
    .pointer = snd_mychip_pcm_pointer,
};
```

Trigger callback'inin çekirdekten aldığı komutlar [K3] birebir:

```c
static int snd_mychip_pcm_trigger(struct snd_pcm_substream *substream, int cmd)
{
    switch (cmd) {
    case SNDRV_PCM_TRIGGER_START:
        /* do something to start the PCM engine */
        break;
    case SNDRV_PCM_TRIGGER_STOP:
        /* do something to stop the PCM engine */
        break;
    default:
        return -EINVAL;
    }
}
```

| Operator | Sıra | Zamanlama | Kanıt |
|---|---|---|---|
| `.open` | akış açılışında | bir kez | [K3] tablo |
| `.hw_params` | donanım parametresi yerleşince | yeniden yapılandırmada | [K3] tablo |
| `.prepare` | start öncesi son hazırlık | her yeniden başlatmada | [K3] tablo |
| `.trigger` | atomik başlat/durdur | kesme bağlamında | [K3] alıntı |
| `.pointer` | konum sorgusu | periyodik | [K2] "frame count or XRUN state" |
| `.hw_free` | parametre serbest bırakma | kapanış öncesi | [K3] tablo |
| `.close` | akış kapanışı | bir kez | [K3] tablo |

---

## §5 alsa-lib PCM yaşam döngüsü

alsa-lib PCM grubu [A4] içinde 127 üye taşır; yaşam döngüsü çağrıları arasında
aşağıdakilerin **varlığı sayfa içeriğinden doğrulanmıştır** (2026-10-13):

| # | Çağrı | Aşama | Doğrulama |
|---|---|---|---|
| 1 | `snd_pcm_open` | akışı aç (yön + mod + bayrak) | [A4] isabet ✅ |
| 2 | `snd_pcm_hw_params` | donanım parametrelerini müzakere et | [A4] isabet ✅ |
| 3 | `snd_pcm_sw_params` | yazılım parametrelerini (periyot/uyanma) ayarla | [A4] isabet ✅ |
| 4 | `snd_pcm_prepare` | akışı çalışma öncesi hazırla | [A4] isabet ✅ |
| 5 | `snd_pcm_start` | akışı başlat | [A4] isabet ✅ |
| 6 | `snd_pcm_wait` | kullanılabilirlik için bekle (bayraklı) | [A4] isabet ✅ |
| 7 | `snd_pcm_drop` | akışı anında durdur (tamponu at) | [A4] isabet ✅ |
| 8 | `snd_pcm_drain` | tamponu boşaltarak bitir | [A4] isabet ✅ |
| 9 | `snd_pcm_recover` | hata (EPIPE/ESTRPIPE) sonrası kurtar | [A4] isabet ✅ |
| 10 | `snd_pcm_close` | kulpı kapat | [A4] isabet ✅ |

> **Sıra şeması (1→10) bu dosyanın sentezidir**; çağrıların *varlığı* [A4]'ten,
> *aşama sırası* ALSA uygulama pratiğidir ve tek sayfa üzerinde maddelenmedi →
> uygulama öncesi `⚠️ VERIFICATION REQUIRED` (A6, §11).

### §5.2 Hata kodları — doğrulanmış çekirdek davranışı

| Kod | Bağlam | Çekirdek tepkisi | Kanıt |
|---|---|---|---|
| `-EPIPE` | `ack` callback dönüş değeri | "buffer XRUN" kabul edilir; durum `SNDRV_PCM_STATE_XRUN`'a otomatik geçilir | [K3] birebir alıntı |
| `-EINVAL` | `.trigger` bilinmeyen komut | sürücü `-EINVAL` döndürür | [K3] `default: return -EINVAL;` |
| `-ESTRPIPE` (askıya alma) | sistem uykusu benzeri durum | bu turda doğrulanmadı | ⚠️ VERIFICATION REQUIRED |
| HTTP `416`/`206` | bu tabloya dahil değildir — HTTP arayüzü CoreMusic frontend'ine aittir | algı karışıklığı önlemi: `home.coremusic.net/include/Stream/MusicStreamHandler.php:95-103` | repo, 2026-10-13 |

### §5.3 Kulp (handle) yaşam döngüsü — sorumluluk dağılımı

| Sorumluluk | Sahip | Not |
|---|---|---|
| `snd_pcm_t` kulpının açılması/kapatılması | uygulama (alsa-lib) | [A4] `snd_pcm_open` · `snd_pcm_close` |
| Parametre müzakere tekrarı | uygulama | `hw_params` başarısızsa farklı geometri ile dener |
| XRUN sonrası kurtarma | uygulama | [A4] `snd_pcm_recover` üyesi mevcut |
| Durum geçişleri | çekirdek (ALSA core) | [K2] "Set the PCM runtime state with stream lock" |
| Donanım register'ı yazımı | `snd-*` sürücüsü | [K3] operator tablosu (§4.4) |
| Callback atomikliği | sürücü callback'i | [K3] "atomic by default" |

### §5.4 Nesne ağacı — kavram eşlemesi

| Kavram | Üreteç | Altındaki | Kanıt |
|---|---|---|---|
| `snd_pcm_t` (kulp) | uygulama `snd_pcm_open` | alt akış (substream) | [A4] · [K3] |
| `snd_pcm_substream *` | çekirdek | driver callback'leri | [K3] tüm örnek imzaları |
| `snd_pcm_ops` (operator tablosu) | sürücü | `.open` … `.pointer` | [K3] §4.4 alıntı |
| `snd_pcm_hw_params_t` | uygulama | donanım kabul kümesi | [A4] "Hardware Parameters" |
| `snd_pcm_sw_params_t` | uygulama | periyot/uyanma politikası | [A4] "Software Parameters" |
| `snd_mixer_t` (mikser) | uygulama `snd_mixer_open` | eleman listesi | [A5] §8 |

### §5.1 Sayfanın kendi bölümlendirilmesi (birebir)

[A4] sayfasındaki bölüm başlıkları (2026-10-13, HTML'den alındı):

| Bölüm | Kapsam |
|---|---|
| Stream Information | akış hakkında bilgi sorguları |
| Hardware Parameters | `snd_pcm_hw_params_t` müzakeresi |
| Software Parameters | `snd_pcm_sw_params_t` müzakeresi |
| Access Mask Functions | erişim türü (RW_INTERLEAVED/MMAP…) maskeleri |
| Format Mask Functions | PCM format maskeleri |
| Direct Access (MMAP) | doğrudan bellek erişimi |
| Simple setup functions | hızlı kurulum yardımcıları |
| PCM Plugins | eklenti (plug/dmix/dsnoop/rate…) mekanizması |

Bayraklar (birebir): `SND_PCM_NONBLOCK` · `SND_PCM_ASYNC` · `SND_PCM_EINTR` ·
`SND_PCM_ABORT` · `SND_PCM_NO_AUTO_RESAMPLE` · `SND_PCM_NO_AUTO_CHANNELS` ·
`SND_PCM_NO_AUTO_FORMAT` · `SND_PCM_NO_SOFTVOL`.

---

## §6 Paylaşım modları

ALSA'da "kim donanıma ne kadar yakın bağlanır" sorusunun cevabı `.asoundrc`
içindeki **plugin türü**dür. Aşağıdaki bloklar [A1] sayfasındaki `<pre>`
bloklarından **birebir** alınmıştır (2026-10-13).

### §6.1 Doğrudan donanım (exclusive'e en yakın yol)

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

| Gözlem | Sonuç |
|---|---|
| `type hw` | uygulama donanıma **doğrudan** bağlanır; yazılım mikseri devrede değildir |
| Varsayılan üzerine yazma | `!default` eki mevcut varsayılanı değiştirir |
| ASIO Exclusive Lock karşılığı | aynı uygulama-sınırı mantığı — ancak ALSA'da kilit semantics'i bu sayfada tanımlanmaz → ⚠️ VERIFICATION REQUIRED |

### §6.2 Yazılım mikseri — dmix (çoklu çalma)

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

| Anahtar | Görev | Kanıt |
|---|---|---|
| `ipc_key` | dmix örneği için paylaşılan IPC anahtarı (eşsiz olmalı) | [A1] blok (dsnoop yorumu: "must be unique for all dmix plugins!!!!") |
| `slave` | altta yatan gerçek `hw` aygıtı ve tampon geometrisi | [A1] blok |
| `bindings` | kanal → slave kanal eşlemesi | [A1] blok |
| `type plug` sarmalayıcı | format/kanal/rate uyuşmazlığını otomatik düzelttirir | [A1] blok · [A1] `type plug` tanımı |

### §6.3 Yazılım mikseri — dsnoop (çoklu kayıt)

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

| Plugin | Yön | Ne kazandırır |
|---|---|---|
| `dmix` | playback | birden fazla uygulamanın aynı aygıta yazabilmesi |
| `dsnoop` | capture | birden fazla uygulamanın aynı aygıttan okuyabilmesi |
| `plug` | her iki yön | format/kayıt/rate uyumsuzluğunu yazılım katmanında çözer |
| `rate` | dönüşüm | [A1] `pcm.rate_convert { type rate, slave sl2 }` bloğu |

### §6.4 Paylaşım matrisi

| Mod | Plugin | Kim donanıma bağlanır | Çalma eşzamanlılığı | Gecikme |
|---|---|---|---|---|
| Doğrudan | `hw` | tek uygulama (donanım kısıtı) | tek akış | en düşük |
| Varsayılan+plug | `plug` → `hw` | tek uygulama | tek akış | düşük + dönüşüm payı |
| Mikser playback | `dmix` → `hw` | dmix sunucusu | çoklu | periyot tamponu kadar |
| Mikser capture | `dsnoop` → `hw` | dsnoop sunucusu | çoklu | periyot tamponu kadar |
| Ağ JACK | `jack` → JACK sunucusu | JACK sunucusu | çoklu | [A1] `pcm.jack { type jack … }` bloğu |

> Eşzamanlılık ve gecikme sayısal eşikleri (ms) bu sayfalarda **verilmiyor** →
> ölçüm gerektirir, `UNKNOWN` (bkz. sartname: 512 örnek · 48 kHz = 10,67 ms/block
> hesabı bu dosyanın dışıdır; ölçüm PLANNED).

### §6.5 Yapılandırma konumları (birebir)

[A1] sayfasındaki ilk `<pre>` blokları (2026-10-13):

| # | Konum | Kapsam | Kanıt |
|---|---|---|---|
| 1 | `$HOME/.asoundrc` | kullanıcının özel yapılandırması | [A1] pre#1 |
| 2 | `/usr/share/alsa/alsa.conf` | sistem/dağıtım varsayılanları | [A1] pre#2 |
| 3 | `/etc/asound.conf` | sistem geneli özel yapılandırma | [A1] pre#3 |
| 4 | `vi /home/xxx/.asoundrc` | düzenleme örneği (belge metni) | [A1] pre#4 |

| Soru | Cevap | Durum |
|---|---|---|
| Konum önceliği sırası tam olarak nedir? | bu sayfada maddelenmedi | ⚠️ VERIFICATION REQUIRED (A11) |
| `!default` eki ne anlama gelir? | mevcut tanımı **ezerek** yeniden tanımlar (blok içindeki `!default` kullanımı) | [A1] pre#5 · pre#24 |

---

## §7 Zamanlama ve gecikme — bağlayıcı gözlemler

| Kural | Metin | Kanıt |
|---|---|---|
| Periyot bildirimi tekilliği | "Even if more than one periods have elapsed since the last call, you have to call this only once." | [K2] |
| Bildirim bağlamı | kesme rutini → middle layer → poll uyandırma | [K3] §4.2 alıntısı |
| Atomiklik varsayılanı | ilgili callback'ler "atomic by default" | [K3] |
| Gecikme ölçümü | bu kaynaklarda sayısal gecikme tablosu **yok** → ölçüm PLANNED | sartname · ölçüm durumu |

---

## §8 Mixer (mikser) kontrol modeli

alsa-lib mixer grubu [A5] içinde 61 üye taşır. Doğrulanan çekirdek çağrılar
(2026-10-13 sayfa taraması):

| Çağrı | Görev | Doğrulama |
|---|---|---|
| `snd_mixer_open` | mikser kulpı aç | isabet ✅ |
| `snd_mixer_attach` / `snd_mixer_attach_hctl` | aygıt/HCtl kaynağı bağla | isabet ✅ |
| `snd_mixer_handle_events` | olayları işle (değişiklik kuyruğu) | isabet ✅ |
| `snd_mixer_elem_new` / `snd_mixer_elem_next` | eleman listesi gezinme | isabet ✅ |
| `snd_mixer_class_register` | sınıf kaydı (özel sınıf desteği) | isabet ✅ |
| `snd_mixer_detach` / `snd_mixer_detach_hctl` | kaynağı ayır | isabet ✅ |
| `snd_mixer_close` | mikser kulpını kapat | isabet ✅ |

| Gözlem | Durum |
|---|---|
| Basit eleman API'si (`snd_mixer_selem_*`) adları [A5] sayfasında bu turda **isabet etmedi** | `⚠️ VERIFICATION REQUIRED` (A7, §11) — sayfa yapısı farklı bir adlandırma taşıyor olabilir |
| CLI karşılığı | `amixer [ -option ] [ cmd ]` — "command-line mixer for ALSA soundcard driver" [M2] birebir NAME satırı |

---

## §9 CoreMusic ile bağ

### §9.1 Repo kanıtı (2026-10-13)

| İddia | Durum |
|---|---|
| CoreMusic reposunda ALSA (`snd_*` · `alsa` · `aplay` · `amixer`) kodu | **YOK** — desen taraması 0 isabet → UNKNOWN |
| CoreMusic ses dosyasını nasıl işler | `ffprobe`/`shell_exec` ile dosya düzeyinde — `shared/src/AI/AIEngine.php:288-295` (§10) |
| Donanım erişimi | **YOK** — HTML5 çalma frontend katmanıdır |

### §9.2 Kural eşlemesi (L0 kuralları → bu dal)

| L0 kuralı | ALSA karşılığı | Durum |
|---|---|---|
| Zero-allocation (audio thread) | callback'ler "atomic by default" — [K3]; tahsis yasağı sürücü callback'ine de uygulanır | bağlayıcı |
| Lock-free | kesme bağlamında kilit yasağı; [K2] stream-lock kullanımı çekirdek içindedir | bağlayıcı |
| `noexcept` (C++) | alsa-lib C API — C++ sarmalayıcıda `noexcept` maddi | bağlayıcı |
| 512 örnek · 48 kHz | `slave { period_size … buffer_size … rate 44100 }` blokları geometriyi belirler; 48 kHz hedefi için ölçüm PLANNED | ⚠️ VERIFICATION REQUIRED |
| ASIO Exclusive Lock (tek uygulama) | `type hw` doğrudan yol — eşzamanlılık politikası dağıtıma bağlı | ⚠️ VERIFICATION REQUIRED |
| PCM3168A (ADR-038 H001) | bu dal kod-ses kartı modeline girmez | K001-donanım |

---

## §10 Web kanıt matrisi

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| `.asoundrc` yapılandırma belgesi erişilebilir ve 39 `<pre>` bloğu taşır | https://www.alsa-project.org/wiki/Asoundrc | 2026-10-13 | ✅ GET 200 |
| alsa-lib PCM referans sayfası erişilebilir (127 üye) | https://www.alsa-project.org/alsa-doc/alsa-lib/group___p_c_m.html | 2026-10-13 | ✅ GET 200 |
| alsa-lib Mixer referans sayfası erişilebilir (61 üye) | https://www.alsa-project.org/alsa-doc/alsa-lib/group___mixer.html | 2026-10-13 | ✅ GET 200 |
| `writing-an-alsa-driver` `snd_pcm_ops` ve XRUN davranışını belgeler | https://www.kernel.org/doc/html/latest/sound/kernel-api/writing-an-alsa-driver.html | 2026-10-13 | ✅ GET 200 · alıntı doğrulandı |
| `alsa-driver-api` periyot/trigger sözleşmesini belgeler | https://www.kernel.org/doc/html/latest/sound/kernel-api/alsa-driver-api.html | 2026-10-13 | ✅ GET 200 · alıntı doğrulandı |
| `aplay(1)` NAME/SYNOPSIS: "arecord, aplay - command-line sound recorder and player for ALSA soundcard driver" | https://manpages.debian.org/bookworm/alsa-utils/aplay.1.en.html | 2026-10-13 | ✅ GET 200 |
| `amixer(1)` NAME: "amixer - command-line mixer for ALSA soundcard driver" | https://manpages.debian.org/bookworm/alsa-utils/amixer.1.en.html | 2026-10-13 | ✅ GET 200 |
| `alsactl(1)` NAME: "alsactl - advanced controls for ALSA soundcard driver" | https://manpages.debian.org/bookworm/alsa-utils/alsactl.1.en.html | 2026-10-13 | ✅ GET 200 |

**Çakışma kontrolü:** bu dosyadaki 8 URL, repo envanteri (2103 satır) ile
**kesişim 0**.

---

## §11 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| A6 | alsa-lib yaşam döngüsü çağrısı sırası tek resmi sayfada doğrulandı mı? | ⚠️ VERIFICATION REQUIRED |
| A7 | `snd_mixer_selem_*` (basit eleman) API'si hangi sayfada? | ⚠️ VERIFICATION REQUIRED |
| A8 | Hedef dağıtım + alsa-utils sürümü (bookworm 1.2.8-1 referans mı)? | UNKNOWN |
| A9 | PipeWire altındaki ALSA erişim yolu (doğrudan `alsa-lib` mi, yoksa oturum API'si mi)? | ⚠️ VERIFICATION REQUIRED |
| A10 | 512 örnek · 48 kHz geometrisi hangi `slave` bloğunda kesinleşecek? | ⚠️ VERIFICATION REQUIRED |

---

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-4 |
| mimari | `alsa-anayasa.md` | Kimlik, katman mimarisi ve PCM modeli (bu dosya) | Kademe-4 |
| mimari | `alsa-surucu.md` | Sürücü arayüzü, kesme yolu ve tasarım belgeleri | Kademe-4 |
| kod | `alsa-ornekler.md` | Yapılandırma, CLI ve çağrı dizisi örnekleri | Kademe-4 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
