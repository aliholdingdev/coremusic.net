---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/alsa/alsa-surucu
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio/alsa
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-audio/alsa/alsa-anayasa
evidence:
  - https://www.kernel.org/doc/html/latest/sound/kernel-api/writing-an-alsa-driver.html
  - https://www.kernel.org/doc/html/latest/sound/kernel-api/alsa-driver-api.html
  - https://www.kernel.org/doc/html/latest/sound/designs/procfile.html
  - https://www.kernel.org/doc/html/latest/sound/designs/tracepoints.html
  - https://www.kernel.org/doc/html/latest/sound/designs/oss-emulation.html
  - https://www.kernel.org/doc/html/latest/sound/cards/index.html
validation:
  - "2026-10-13 · 16 çekirdek dokümanı GET 200 · envanter kesişimi 0 · alıntılar indirilen HTML'den birebir"
  - "repo taraması: çekirdek ALSA sürücü deseni (`snd_pcm_ops|module_snd`) 0 isabet → sürücü kodu YOK"
open_questions:
  - "CoreMusic/ Neva Engine hedefli donanım kartı için hangi `snd-*` sürücüsü kullanılır? → UNKNOWN"
  - "Alsa tasarım belgelerinden hangileri fiilen proje kapsamında? → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# ALSA Sürücü — `snd_pcm_ops` · Kesme Yolu · Tasarım Belgeleri

> **Bu dosya ne değildir:** Sürücü kodu veya patch değildir. Bu dosya Linux çekirdek
> ALSA sürücü sözleşmesini *belge kanıtıyla* tarif eder; alıntılar 2026-10-13'te
> indirilen HTML'den birebir alınmıştır. Doğrulanmayan her satır `⚠️ VERIFICATION
> REQUIRED` veya `UNKNOWN` işaretlidir.
>
> **Durum etiketi:** ŞARTNAME/belge IMPLEMENTED · Çekirdek sürücü kodu çekirdek
> ağacındadır (dağıtım işi) · CoreMusic'te sürücü kodu **YOK** (tarama 0 isabet).

---

## §0 ELI15 — ALSA sürücüsü bir cümlede

ALSA sürücüsü, ses kartının kesmesini (interrupt) ve DMA tamponunu yöneten
çekirdek tarafındaki parçadır: çekirdek sürücüye `snd_pcm_ops` içindeki
callback'leri çağırır (aç · parametre hazırla · başlat/durdur · konum bildir),
sürücü her periyot sonunda `snd_pcm_period_elapsed()` ile "tampon güncellendi"
der; çekirdek bunun üzerine konumu hesaplar ve bekleyen uygulamaları uyandırır.
(Kaynak: §4 · §5)

---

## §1 Dosya kimliği

| Alan | Değer |
|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-audio/alsa/alsa-surucu` |
| Kademe | 4 · deep-dal içeriği (module) |
| Üst dosya | `alsa-anayasa.md` (katmanlar · PCM modeli) |
| Kardeş dosya | `alsa-ornekler.md` (yapılandırma · CLI · repo kanıtı) |
| Durum | `draft` |
| Konu | Linux çekirdek ALSA sürücü sözleşmesi + tasarım belgeleri (designs/*) |
| Repo durumu | Sürücü kodu YOK → UNKNOWN (§8.1) |

---

## §2 Kanıt kaynakları

| Etiket | Kaynak | URL | 2026-10-13 |
|---|---|---|---|
| [K3] | Writing an ALSA Driver | https://www.kernel.org/doc/html/latest/sound/kernel-api/writing-an-alsa-driver.html | ✅ GET 200 · 249.450 bayt |
| [K2] | ALSA Driver API | https://www.kernel.org/doc/html/latest/sound/kernel-api/alsa-driver-api.html | ✅ GET 200 · 749.583 bayt |
| [K4] | ALSA Kernel API dizini | https://www.kernel.org/doc/html/latest/sound/kernel-api/index.html | ✅ GET 200 |
| [K1] | Sound Subsystem Documentation | https://www.kernel.org/doc/html/latest/sound/index.html | ✅ GET 200 |
| [D1] | Proc Files of ALSA Drivers | https://www.kernel.org/doc/html/latest/sound/designs/procfile.html | ✅ GET 200 |
| [D2] | ALSA PCM channel-mapping API | https://www.kernel.org/doc/html/latest/sound/designs/channel-mapping-api.html | ✅ GET 200 |
| [D3] | Standard ALSA Control Names | https://www.kernel.org/doc/html/latest/sound/designs/control-names.html | ✅ GET 200 |
| [D4] | ALSA Jack Controls | https://www.kernel.org/doc/html/latest/sound/designs/jack-controls.html | ✅ GET 200 |
| [D5] | Notes on Kernel OSS-Emulation | https://www.kernel.org/doc/html/latest/sound/designs/oss-emulation.html | ✅ GET 200 |
| [D6] | Tracepoints in ALSA | https://www.kernel.org/doc/html/latest/sound/designs/tracepoints.html | ✅ GET 200 |
| [D7] | Notes on Power-Saving Mode | https://www.kernel.org/doc/html/latest/sound/designs/powersave.html | ✅ GET 200 |
| [D8] | ALSA Co-processor Acceleration API | https://www.kernel.org/doc/html/latest/sound/designs/compress-accel.html | ✅ GET 200 |
| [D9] | ALSA Compress-Offload API | https://www.kernel.org/doc/html/latest/sound/designs/compress-offload.html | ✅ GET 200 |
| [D10] | ALSA Jack Software Injection | https://www.kernel.org/doc/html/latest/sound/designs/jack-injection.html | ✅ GET 200 |
| [D11] | MIDI 2.0 on Linux | https://www.kernel.org/doc/html/latest/sound/designs/midi-2.0.html | ✅ GET 200 |
| [D12] | OSS Sequencer Emulation on ALSA | https://www.kernel.org/doc/html/latest/sound/designs/seq-oss.html | ✅ GET 200 |
| [C1] | Card-Specific Information | https://www.kernel.org/doc/html/latest/sound/cards/index.html | ✅ GET 200 |

> 17 kaynak, repo URL envanteri (2103 satır) ile **kesişim 0** — 2026-10-13 taze doğrulama.

---

## §3 Sürücünün konumu

| Katman | Kim çağırır | Ne bilir | Bilmediği |
|---|---|---|---|
| Uygulama | — | — | sürücüyü hiç tanımaz (alsa-lib üzerinden) |
| alsa-lib | uygulama | PCM kulpı + bayraklar | donanım register'ı |
| ALSA core (PCM middle layer) | çekirdek | durum makinesi, runtime, periyot hesabı | hangi çip |
| **`snd-*` sürücüsü (bu dosya)** | ALSA core | `snd_pcm_ops` callback'leri + kesme + DMA | uygulama politikası |
| Donanım | sürücü | register'ları | — |

**Kanıt:** operator tablosunun adı ve callback kümesi [K3] (§4.1 alıntı); periyot
bildiriminin "buffer-update routine in the PCM middle layer" olduğu [K3] (§5.1).

### §3.1 Doğrulanmış belge dizini (URL yapısından)

| Dizin | Sayfa | Başlık (doğrulanmış) |
|---|---|---|
| `sound/` | `index.html` | Sound Subsystem Documentation |
| `sound/kernel-api/` | `index.html` | ALSA Kernel API Documentation |
| `sound/kernel-api/` | `writing-an-alsa-driver.html` | Writing an ALSA Driver |
| `sound/kernel-api/` | `alsa-driver-api.html` | ALSA Driver API |
| `sound/designs/` | 12 sayfa (§8.1) | tasarım belgeleri |
| `sound/cards/` | `index.html` | Card-Specific Information |

### §3.2 Belge–yaşam döngüsü eşlemesi

| Yaşam aşaması | İlgili belge | Etiket |
|---|---|---|
| Sürücü yazımı (operator tablosu) | Writing an ALSA Driver | [K3] |
| API referansı (callback listesi) | ALSA Driver API | [K2] |
| Teşhis (çalışma zamanı) | Proc Files · Tracepoints | [D1] · [D6] |
| Kontrol adlandırma standardı | Standard ALSA Control Names | [D3] |
| Kanal topolojisi | channel-mapping API | [D2] |
| Eski uyumluluk | OSS-Emulation · seq-oss | [D5] · [D12] |
| Güç yönetimi | Power-Saving Mode | [D7] |
| Sıkıştırılmış ses | Compress-Offload · Co-processor Acceleration | [D9] · [D8] |
| Giriş/jack testi | Jack Controls · Jack Software Injection | [D4] · [D10] |
| MIDI evrimi | MIDI 2.0 on Linux | [D11] |

---

## §4 `snd_pcm_ops` sözleşmesi

### §4.1 Playback operator tablosu (birebir, [K3])

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

Capture tarafı aynı kümeyle ayrı tablo kurar: `snd_mychip_capture_ops`
(`.open = snd_mychip_capture_open` · `.close = snd_mychip_capture_close` başlar) [K3].

### §4.2 Callback görev tablosu

| Callback | Görev | Bağlam | Kanıt |
|---|---|---|---|
| `.open` | alt akışı aç, runtime'ı kur | çağrı bağlamı | [K3] tablo |
| `.hw_params` | donanım parametresi yerleşince kurulum | çağrı bağlamı | [K3] tablo |
| `.hw_free` | parametreleri serbest bırak | çağrı bağlamı | [K3] tablo |
| `.prepare` | DMA/hazırlık — start öncesi son adım | çağrı bağlamı | [K3] tablo |
| `.trigger` | atomik başlat/durdur (`START`/`STOP`) | atomik bağlam | [K3] switch alıntısı |
| `.pointer` | kare cinsinden mevcut konumu döndür | atomik bağlam | [K2] "frame count or XRUN state" |
| `.ack` | uygulama işareti (appl_ptr) takibi | — | [K3] `-EPIPE` bağlamı (§6.1) |
| `.page` | mmap sayfa hatası adresi (isteğe bağlı) | — | [K3] "mmap calls this callback" |
| `.mmap` | mmap davranışı (isteğe bağlı) | — | [K3] "optional callback" |
| `ioctl` yardımcısı | genel PCM ioctl'ları işler | — | [K2] "Processes the generic ioctl commands for PCM. Can be passed as the ioctl callback for PCM ops." |

### §4.3 `.pointer` ve `.trigger` — kesin metin ([K2])

> "Developer should pay enough attention that some callbacks in snd_pcm_ops are
> done by the call of function:
> **.pointer** - to retrieve current position of audio data transmission by frame
> count or XRUN state.
> **.trigger** - with `SNDRV_PCM_TRIGGER_STOP` at XRUN or DRAINING state.
> **.get_time_info** - to retrieve audio time stamp if needed."

| Gözlem | Sonuç |
|---|---|
| `.pointer` çağrılmazsa | konum bilinmez → uygulama beklemesi kör kalır |
| `.trigger` XRUN/DRAINING'de | `SNDRV_PCM_TRIGGER_STOP` ile durdurma yükümlülüğü sürücüde |
| `.get_time_info` | isteğe bağlı zaman damgası kaynağı (zaman damgası ihtiyacı olan uygulamalar için) |

### §4.4 Trigger switch (birebir, [K3])

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

| Komut | Sürücü sorumluluğu | Hatalı komut |
|---|---|---|
| `SNDRV_PCM_TRIGGER_START` | PCM motorunu başlat | — |
| `SNDRV_PCM_TRIGGER_STOP` | PCM motorunu durdur | — |
| tanınmayan | — | `-EINVAL` döner |

### §4.5 Açma ve hazırlama callback'leri ([K3] — yer tutucular kısaltıldı)

> **Aktarım notu:** sayfadaki "the hardware-specific codes will be here" yer
> tutucusu (`....`) bloklardan **çıkarıldı**; kalan satırlar birebirdir.

```c
static int snd_mychip_playback_open(struct snd_pcm_substream *substream)
{
    struct mychip *chip = snd_pcm_substream_chip(substream);
    struct snd_pcm_runtime *runtime = substream->runtime;
    runtime->hw = snd_mychip_playback_hw;
    /* more hardware-initialization will be done here */
    return 0;
}
```

```c
static int snd_mychip_pcm_prepare(struct snd_pcm_substream *substream)
{
    struct mychip *chip = snd_pcm_substream_chip(substream);
    struct snd_pcm_runtime *runtime = substream->runtime;
    /* set up the hardware with the current configuration */
    mychip_set_sample_format(chip, runtime->format);
    mychip_set_sample_rate(chip, runtime->rate);
    mychip_set_channels(chip, runtime->channels);
    mychip_set_dma_setup(chip, runtime->dma_addr,
                         chip->buffer_size, chip->period_size);
    return 0;
}
```

| Gözlem | Sonuç | Kanıt |
|---|---|---|
| `runtime->hw` ataması | açıklayıcı (hw limits) tablo `open` içinde tanıtılır | [K3] alıntı |
| hw limit örneği | `.period_bytes_max = 32768` · `.periods_min = 1` · `.periods_max = 1024` (sayfanın `snd_mychip_playback_hw` örneği) | [K3] alıntı |
| `runtime` erişimi | `format` · `rate` · `channels` · `dma_addr` prepare'da okunur | [K3] alıntı |
| DMA kurulumu | `buffer_size` ve `period_size` chip katmanına iletilir | [K3] alıntı |
| `snd_pcm_substream_chip` | substream → özel chip verisi köprüsü (örnek kodda her callback'te) | [K3] alıntı |
| Şablondaki `....` işaretleri | sayfanın "the hardware-specific codes will be here" yer tutucusudur — gerçek kodda donanıma özgü gövde vardır | [K3] metni |

---

## §5 Kesme yolu (interrupt path)

### §5.1 Bildirim ve sonrası (birebir, [K3])

> "… buffer_size - 1 . This is usually called from the buffer-update routine in the
> PCM middle layer, which is invoked when **snd_pcm_period_elapsed()** is called by
> the interrupt routine. Then the PCM middle layer updates the position and
> calculates the available space, and wakes up the sleeping poll threads, etc.
> This callback is also atomic by default."

### §5.2 Tekilleme kuralı (birebir, [K2])

> "Even if more than one periods have elapsed since the last call, you have to
> call this only once."

### §5.3 Kilitli varyant (birebir, [K2])

Sayfa `snd_pcm_period_elapsed_under_stream_lock(struct snd_pcm_substream *)`
fonksiyonunu da listeler; "stream lock should be acquired in advance" uyarısıyla
verilir. (Alıntı bağlamı: 2026-10-13 HTML taraması.)

### §5.4 Adım adım akış

| # | Adım | Kim | Kanıt |
|---|---|---|---|
| 1 | donanım kesmesi gelir | sürücü kesme rutini | [K3] "called by the interrupt routine" |
| 2 | sürücü `snd_pcm_period_elapsed()` çağırır | sürücü | [K3] |
| 3 | middle layer konumu günceller | çekirdek | [K3] alıntı |
| 4 | kullanılabilir alan hesaplanır | çekirdek | [K3] alıntı |
| 5 | bekleyen poll iş parçacıkları uyandırılır | çekirdek | [K3] alıntı |
| 6 | uygulama `read`/`write` ile döner | uygulama | [A4] (anayasa §5) |
| 7 | XRUN olursa `.trigger STOP` çağrılır | çekirdek → sürücü | [K2] (§4.3) |

---

## §6 XRUN ve hata yolu

### §6.1 `-EPIPE` → otomatik XRUN (birebir, [K3])

> "When the return value is `-EPIPE`, PCM core treats that as a buffer XRUN, and
> changes the state to `SNDRV_PCM_STATE_XRUN` automatically."

### §6.2 Kök neden sınıflandırması

| Neden | Mekanizma | Sahip | Durum |
|---|---|---|---|
| Uygulama geç yazdı/okudu | tampon taşması → `EPIPE` | uygulama zamanlaması | [K3] davranış ✅ |
| DMA/pointer takibi | `.pointer` + `ack` callback'leri | sürücü + çekirdek | [K3] ✅ |
| DRAINING anı | `.trigger STOP` çağrılır | çekirdek → sürücü | [K2] ✅ |
| Sayısal eşik (kaç örnekte XRUN?) | — | ölçüm | PLANNED (512 örnek · 48 kHz testi) |

### §6.3 Kurtarma sorumluluğu

| Katman | Ne yapar | Kanıt |
|---|---|---|
| Çekirdek | durumu `SNDRV_PCM_STATE_XRUN`'a geçirir | [K3] |
| alsa-lib | `snd_pcm_recover` çağrısı kullanılabilir | [A4] isabet (anayasa §5) |
| Uygulama | recover sonrası akışı sürdürür veya akışı yeniden kurar | sentez — uygulama kararı |

---

## §7 Atomiklik ve bağlam kuralları

| Kural | Metin | Kanıt |
|---|---|---|
| Callback atomikliği varsayılanıdır | "This callback is also atomic by default." | [K3] (§5.1) |
| `ack`/pointer callback atomik | sayfa callback'i "rarely used" dışında atomik varsayılır | [K3] |
| Runtime durum değişimi | "Set the PCM runtime state with stream lock" | [K2] |
| Uygulama kuralı (CoreMusic L0) | callback içinde tahsis/kilit yasak — çekirdek sözleşmesiyle uyumlu | L0 kuralları |

---

## §8 Tasarım belgeleri (designs/*)

Bu bölümdeki 12 belge, başlıkları + giriş metinleri 2026-10-13'te GET 200 ile
doğrulanmıştır; her satırın **kendi metni** kaynak sayfadan alınmıştır.

### §8.1 Belge envanteri

| # | Belge (resmi `<title>`) | Giriş metni (birebir özet) | Etiket |
|---|---|---|---|
| 1 | Proc Files of ALSA Drivers | "ALSA has its own proc tree, `/proc/asound`. Many useful information are found in this tree." | [D1] |
| 2 | ALSA PCM channel-mapping API | "The channel mapping API allows user to query the possible channel maps and the current channel map, also optionally to modify the channel map of the current stream." | [D2] |
| 3 | Standard ALSA Control Names | "This document describes standard names of mixer controls." | [D3] |
| 4 | ALSA Jack Controls | "ALSA uses kcontrols to export audio controls (switch, volume, Mux, ...) to user space." | [D4] |
| 5 | Notes on Kernel OSS-Emulation | "ALSA provides a powerful OSS emulation on the kernel … `snd-pcm-oss`, `snd-mixer-oss` and `snd-seq-oss`." | [D5] |
| 6 | Tracepoints in ALSA | "ALSA PCM core registers `snd_pcm` subsystem to kernel tracepoint system … two categories of tracepoints; for state of PCM buffer and for processing of PCM hardware parameters." | [D6] |
| 7 | Notes on Power-Saving Mode | "AC97 and HD-audio drivers have the automatic power-saving mode … `CONFIG_SND_AC97_POWER_SAVE` and `CONFIG_SND_HDA_POWER_SAVE`." | [D7] |
| 8 | ALSA Co-processor Acceleration API | "There is a requirement to expose the audio hardware that accelerates various tasks for user space such as sample rate converters, compressed stream decoders, etc." | [D8] |
| 9 | ALSA Compress-Offload API | "Since its early days, the ALSA API was defined with PCM support or constant bitrates payloads such as IEC61937 in mind." | [D9] |
| 10 | ALSA Jack Software Injection | "… users could inject plugin or plugout events to the audio jacks through debugfs interface …" | [D10] |
| 11 | MIDI 2.0 on Linux | "MIDI 2.0 is an extended protocol for providing higher resolutions and more fine controls over the legacy MIDI 1.0." | [D11] |
| 12 | OSS Sequencer Emulation on ALSA | "This directory contains the OSS sequencer emulation driver on ALSA." | [D12] |

### §8.2 Debug ve teşhis yüzeyleri

| Yüzey | Ne gösterir | Kanıt |
|---|---|---|
| `/proc/asound` | ALSA proc ağacı — hata ayıklama dosyaları | [D1] birebir |
| `/proc/asound/card*` | karta özgü dosyalar; bazı sürücüler register dump üretir | [D1] "shows the register dump" |
| Tracepoint'ler | PCM tampon durumu + hw parametre işleme izleri | [D6] birebir |
| Aktivasyon | `CONFIG_SND_DEBUG`; XRUN izleri için ayrıca `SND_PCM_XRUN_DEBUG` | [D6] birebir |

### §8.3 Kanal eşleme (channel map)

| Kavram | Tanım | Kanıt |
|---|---|---|
| Channel map | "an array of position for each PCM channel" | [D2] birebir |
| Stereo örneği | `{ front_left, front_right }` | [D2] birebir |
| 4.0 örneği | `{ front left, front right, rear left, rear right }` | [D2] birebir |
| 8.1 hedef (CoreMusic) | bu belgede 8.1 eşlemesi **geçmiyor** → eşleme uygulama/ölçüm işi | ⚠️ VERIFICATION REQUIRED |

### §8.4 JACK ve kcontrol köprüsü

| Gözlem | Metin | Kanıt |
|---|---|---|
| Kcontrol dışa açılımı | "ALSA uses kcontrols to export audio controls (switch, volume, Mux, ...) to user space." | [D4] |
| Örnek senaryo | "userspace applications like pulseaudio can switch off headphones and switch on speakers when no headphones are plugged in." | [D4] |
| Eski kod sınırlaması | "The old ALSA jack code only created input devices for each registered jack." + non-root okunabilirlik kısıtı | [D4] |
| Yazılım enjeksiyonu | `sw_inject_enable` · `jackin_inject` · `status` debugfs anahtarları | [D10] |

### §8.5 Sıkıştırılmış ses ve DSP görevleri

| Belge | Ne getirir | CoreMusic ilgisi | Durum |
|---|---|---|---|
| Compress-Offload [D9] | sıkıştırılmış akış için API — "frames" argümanlarının kısıtını belgeler | Neva Engine'in PCM zinciri **dışında** | kapsam dışı (Q2) |
| Co-processor Acceleration [D8] | hızlandırıcı görevler (örnekleme hızı dönüştürme · çözücü) için API uzantısı | DSP donanım yüzeyi K001/K004 komşusu | ⚠️ VERIFICATION REQUIRED |
| IEC61937 | sabit bit hızlı akış taşıma biçimi — API'nin başlangıç varsayımı | Dolby/DTS ham akış konusu | yalnız [D9] metninde geçiyor |

### §8.6 Uyumluluk ve eski arayüzler

| Belge | Ne | Kanıt |
|---|---|---|
| OSS-Emulation [D5] | `snd-pcm-oss` · `snd-mixer-oss` · `snd-seq-oss` eklenti çekirdek modülleri; "loaded automatically when the corresponding service is called" (alias `sound-service-x-y`) | [D5] birebir |
| OSS Sequencer [D12] | `/dev/sequencer` ve `/dev/music` cihaz emülasyonu; "still in the development state" | [D12] birebir |
| MIDI 2.0 [D11] | UMP kapsayıcı biçimi — "32bit aligned"; "up to 16 'UMP Groups', where each UMP Group contain up to 16 MIDI channels" | [D11] birebir |
| Kart-dözel bilgiler | "Card-Specific Information" başlıklı bölüm | [C1] `<title>` |

### §8.7 `/proc/asound` bölüm başlıkları (birebir, [D1])

Sayfanın H2 başlıkları (2026-10-13 HTML taraması):

| # | Başlık | Konu |
|---|---|---|
| 1 | General | genel proc dosyaları |
| 2 | Global Information | global durum |
| 3 | Card Specific Files | karta özgü dosyalar |
| 4 | PCM Proc Files | PCM akış dosyaları |
| 5 | AC97 Codec Information | AC97 codec bilgisi |
| 6 | USB Audio Streams | USB ses akışları |
| 7 | HD-Audio Codecs | HD-Audio codec listesi |
| 8 | Sequencer Information | sequencer durumu |
| 9 | Help For Debugging? | hata ayıklama yardımı |

> Bu başlıkların her birinin içeriği tek tek okunmadı — yalnız bölüm dizini
> doğrulanmıştır. Derin okuma için [D1] kaynağı esastır (adres: bu dosya §2).

---

## §9 CoreMusic ile bağ

### §9.1 Repo kanıtı (2026-10-13)

| İddia | Durum |
|---|---|
| CoreMusic reposunda çekirdek ALSA sürücü deseni (`snd_pcm_ops` · `module_snd`) | **0 isabet** → YOK · UNKNOWN |
| Çekirdek sürücü geliştirme hedefte mi? | **HAYIR** — dağıtım/donanım işi; K001-donanım komşusu |

### §9.2 Kural eşlemesi

| L0 kuralı | Sürücü yüzeyindeki karşılığı | Durum |
|---|---|---|
| Zero-allocation (audio thread) | kesme bağlamı atomiktir; tahsis yasağı [K3] "atomic by default" ile örtüşür | bağlayıcı |
| Lock-free | `.trigger`/`.pointer` kilitli çağrılmaz; durum geçişi çekirdek içi stream-lock ile | bağlayıcı |
| `noexcept` (C++) | çekirdek yüzeyi C'dir — C++ tarafı (Neva Engine) yalnız kullanıcı uzayında | bağlayıcı |
| 512 örnek · 48 kHz | periyot/tampon geometrisi `.hw_params` ile belirlenir; sayısal hedef ölçüm bekler | PLANNED |
| ASIO Exclusive Lock | tek uygulama donanım hakimiyeti — ALSA'da `type hw` yolu (anayasa §6.1) | ⚠️ VERIFICATION REQUIRED |
| DC offset koruması (>0.5V → röle) | donanım katmanı — bu dalın dışı | K001-donanım |

### §9.3 Sürücü-ölçüm kapıları (PLANNED)

| # | Kapı | Kabul kriteri | Durum |
|---|---|---|---|
| 1 | Periyot/tampon geometrisi seçimi | 512 örnek · 48 kHz (sartname) cihazda kabul ediliyor mu? | PLANNED |
| 2 | XRUN toleransı | test sırasında `SNDRV_PCM_STATE_XRUN` sayımı = 0 | PLANNED |
| 3 | Kesme gecikmesi | periyot → uygulama uyanma ölçümleri (tracepoint ile) | PLANNED |
| 4 | Tek uygulama denemesi | ikinci `hw:` istemcisi beklenen hatayı alıyor mu? (ASIO Exclusive Lock mantığı) | PLANNED |
| 5 | 8.1 kanal haritası | channel-map çıktısı hedef topolojiyle eşleşiyor mu? | ⚠️ VERIFICATION REQUIRED |

> Bu kapıların hiçbiri bu turda çalıştırılmadı (kod/ortam yok). Ölçüm oturumu
> ayrı bir görevde planlanır; sayı uydurulmaz.

---

## §10 Web kanıt matrisi

### §10.1 Doğrulama yöntemi (bu tur)

| Adım | İşlem | Sonuç |
|---|---|---|
| 1 | 17 URL `Invoke-WebRequest` HEAD/GET ile sorgulandı (timeout 20-60 sn) | tamamı HTTP 200 |
| 2 | HTML yerel diske indirildi (`Temp\opencode\*.html`) | alıntılar dosyadan çıkarıldı |
| 3 | Alıntılar regex ile metinden birebir alındı (elle yazılmadı) | §4-§8 alıntıları |
| 4 | Repo URL envanteri (2103 satır) ile kesişim sayıldı | **0** |
| 5 | Repo kod taraması (`snd_pcm_ops` · `module_snd`) | **0 isabet** |

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| `snd_pcm_ops` operator tablosu ve trigger switch resmi örnek sayfada | https://www.kernel.org/doc/html/latest/sound/kernel-api/writing-an-alsa-driver.html | 2026-10-13 | ✅ GET 200 · alıntı doğrulandı |
| `.pointer`/`.trigger` XRUN-DRAINING sözleşmesi + periyot tekilliği kuralı | https://www.kernel.org/doc/html/latest/sound/kernel-api/alsa-driver-api.html | 2026-10-13 | ✅ GET 200 · alıntı doğrulandı |
| `/proc/asound` teşhis ağacı belgesi | https://www.kernel.org/doc/html/latest/sound/designs/procfile.html | 2026-10-13 | ✅ GET 200 · giriş metni alındı |
| Tracepoint aktivasyon konfigürasyonları | https://www.kernel.org/doc/html/latest/sound/designs/tracepoints.html | 2026-10-13 | ✅ GET 200 · giriş metni alındı |
| OSS emülasyon modülleri (`snd-*-oss`) | https://www.kernel.org/doc/html/latest/sound/designs/oss-emulation.html | 2026-10-13 | ✅ GET 200 · giriş metni alındı |
| Jack kcontrol dışa açılımı | https://www.kernel.org/doc/html/latest/sound/designs/jack-controls.html | 2026-10-13 | ✅ GET 200 · giriş metni alındı |
| MIDI 2.0 UMP tanımı | https://www.kernel.org/doc/html/latest/sound/designs/midi-2.0.html | 2026-10-13 | ✅ GET 200 · giriş metni alındı |
| Kart-dözel bilgi bölümü dizini | https://www.kernel.org/doc/html/latest/sound/cards/index.html | 2026-10-13 | ✅ GET 200 · `<title>` alındı |

**Çakışma kontrolü:** bu dosyadaki URL'ler repo envanteri (2103 satır) ile
**kesişim 0**.

---

## §11 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| S1 | Hedef donanım kartı için hangi `snd-*` sürücüsü/paketi kullanılacak? | UNKNOWN |
| S2 | `snd_pcm_period_elapsed_under_stream_lock` hangi sürücü senaryosunda gerekli? | ⚠️ VERIFICATION REQUIRED |
| S3 | 8.1 kanal eşlemesi (CoreMusic hedefi) ALSA channel-map'te nasıl tanımlanacak? | ⚠️ VERIFICATION REQUIRED |
| S4 | Tracepoint teşhisi (`CONFIG_SND_DEBUG`) üretim görüntüsünde açık mı? | UNKNOWN |
| S5 | Compress-Offload/Co-processor API'leri Neva Engine kapsamına giriyor mu? | ⚠️ VERIFICATION REQUIRED |

### §11.1 Okuma sırası (bu deep-dal içinde)

| Sıra | Dosya | Ne için okunur |
|---|---|---|
| 1 | `index.md` | dal künyesi ve kanıt özeti |
| 2 | `alsa-anayasa.md` | katmanlar · PCM modeli · paylaşım modları |
| 3 | `alsa-surucu.md` (bu dosya) | sürdürücü sözleşmesi · kesme yolu · designs/* |
| 4 | `alsa-ornekler.md` | `.asoundrc` blokları · CLI · repo kanıtı |

---

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-4 |
| mimari | `alsa-anayasa.md` | Kimlik, katman mimarisi ve PCM modeli | Kademe-4 |
| mimari | `alsa-surucu.md` | Sürücü arayüzü, kesme yolu ve tasarım belgeleri (bu dosya) | Kademe-4 |
| kod | `alsa-ornekler.md` | Yapılandırma, CLI ve çağrı dizisi örnekleri | Kademe-4 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
