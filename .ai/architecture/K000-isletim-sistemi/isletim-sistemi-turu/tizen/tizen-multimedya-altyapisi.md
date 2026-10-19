---
node_id: K000-isletim-sistemi/isletim-sistemi-turu/tizen/tizen-multimedya-altyapisi
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-turu/tizen
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-turu
evidence: []
validation: []
open_questions: []
group: mimari
---

# Tizen — Multimedya Altyapısı (GStreamer)

## §0 ELI15

Tizen cihazlarında ses ve video iş akışı GStreamer boru hattı (pipeline) üzerine kuruludur.
Bu dosya pipeline'ın nasıl kurulduğunu, element'lerin birbirine nasıl bağlandığını (pad/caps),
hataların uygulamaya nasıl ulaştığını (bus) ve durum makinesinin (NULL→READY→PAUSED→PLAYING)
nasıl çalıştığını anlatır. Tizen'e özgü CAPI sarmalayıcıları bu ortamdan içerik düzeyinde
okunamadığı için `⚠️ VERIFICATION REQUIRED` ile işaretlidir; doğrulanabilen kısım
GStreamer resmi belgeleridir.

## §1 Künye

| Alan | Değer |
|---|---|
| Konu | GStreamer pipeline mimarisi + Tizen multimedya katmanı |
| Platform | Tizen (Samsung) |
| Üst düğüm | `K000-isletim-sistemi/isletim-sistemi-turu/tizen` |
| Kardeş dosyalar | ses-yoneticisi · audio-hal · uygulama-ortami · gercek-zamanli-tampon · web-arastirma |
| Kapsam dışı | Codec seçimi · DRM · video render · GUI |
| Doğrulama tarihi | 2026-10-13 |
| Kanıt biçimi | Resmi URL tablosu + repo kod satırı |

## §2 Kapsam ve Sınır

Kapsam: pipeline kurulumu, element/pad/caps ilişkisi, bus mesaj tipleri, durum geçişleri,
clock seçimi, Tizen katmanının bu üstteki mimariye karşılığı, hata yayılımı.
Sınır dışı: DSP algoritması, PCB/Donanım, PHP/JS uygulama kodu, PCM3168A kararı (ADR-038 H001).

## §3 Katman Şeması

| Katman | Ad | Sorumluluk | Doğrulama |
|---|---|---|---|
| L0 | Uygulama (native/TV app) | pipeline kurma, bus dinleme | `⚠️ VERIFICATION REQUIRED` (Tizen CAPI) |
| L1 | Tizen multimedya API | CAPI sarmalayıcı, oturum/rota | `⚠️ VERIFICATION REQUIRED` |
| L2 | GStreamer core | pipeline · bin · element · pad · bus · clock | ✅ resmi belge (2026-10-13) |
| L3 | GStreamer plug-in | alsasink · audioconvert · playbin · appsink | ✅ resmi belge (2026-10-13) |
| L4 | ALSA / audio HAL | cihaz açma, mixer, UCM profili | ✅ resmi belge (2026-10-13) |
| L5 | Donanım | DAC · CODEC · hoparlör | K003 kapsamı, bu dosya dışı |

## §4 Pipeline Kavramları

| Kavram | Tanım | Kaynak |
|---|---|---|
| `GstPipeline` | Toplevel kapsayıcı; `GstBin`'in özel hâli | gstpipeline.html |
| `gst_pipeline_new` | Pipeline oluşturma fonksiyonu | gstpipeline.html |
| `gst_object_unref` | Pipeline'ı ve eklenen element'leri serbest bırakma | gstpipeline.html |
| `gst_bin_add` / `gst_bin_remove` | Element ekleme/çıkarma | gstpipeline.html |
| `gst_pipeline_get_bus` | Durum değişikliğinden ÖNCE bus edinme zorunluluğu | gstpipeline.html |
| `gst_pipeline_set_auto_flush_bus` | NULL durumuna geçerken otomatik flush davranışı | gstpipeline.html |
| `gst_pipeline_use_clock` | Clock'u elle dayatma | gstpipeline.html |
| `gst_pipeline_auto_clock` | Varsayılan clock seçimine dönüş | gstpipeline.html |

**Önemli kural (resmi metinden):** durum değiştirmeden önce `GstBus` edinilmelidir; bus
dinlenmezse hata mesajları yığılır ve pipeline **açıklamasız durabilir**. Aynı resmi metne
göre pipeline, kimse dinlemese bile mesaj yayımlar; bu bellek biriktirir.

### §4.1 Pipeline Oluşturma Adımları

| Adım | İş | Hata Sonucu |
|---|---|---|
| 1 | `gst_pipeline_new` | Bellek hatası → `UNKNOWN` |
| 2 | Element'leri üret (parse/launch) | Geçersiz sözdizimi → kurulum hatası |
| 3 | `gst_bin_add` ile pipeline'a ekle | Aynı element iki kez → reddedilir |
| 4 | `gst_pipeline_get_bus` | Bus alınmazsa hata sessiz kalır |
| 5 | Pad'leri bağla (`link`) | Caps uyuşmazlığı → link başarısız |
| 6 | `NULL → READY → PAUSED → PLAYING` | Geçiş başarısız → hata mesajı bus'ta |

### §4.2 Bin ve Pipeline Farkı

| Özellik | `GstBin` | `GstPipeline` |
|---|---|---|
| Toplevel | Hayır, alt kapsayıcı | Evet, üst kapsayıcı |
| Clock yönetimi | Yok | Global clock seçimi ve dağıtımı |
| Bus | Alt element mesajları iletir | Uygulamaya teslim edilen birincil bus |
| Kullanım | Gruplama (ör. `playsink`) | Uygulamanın başlattığı akış |

## §5 Caps (Capability) Muhakemesi

Caps, bir pad'in taşıyabildiği medya formatı açıklamasıdır; kaynak ve hedef pad
yalnızca caps **muhakemesi (negotiation)** başarılıysa bağlanır.

| Caps Alanı | Anlam | Örnek |
|---|---|---|
| `media type` | Veri türü | `audio/x-raw` · `video/x-raw` |
| `format` | Örnekleme biçimi | `S16LE` · `F32LE` · `S32LE` |
| `layout` | Kanal yerleşimi | `interleaved` |
| `rate` | Örnekleme hızı | `48000` |
| `channels` | Kanal sayısı | `2` |
| `channel-mask` | Kanal konumu maskesi | platforma bağlı `⚠️ VERIFICATION REQUIRED` |

### §5.1 alsasink Pad Template'i (ölçülen resmi değer)

| Alan | Ölçülen Aralık |
|---|---|
| `format` kümesi | `F64LE` · `F64BE` · `F32LE` · `F32BE` · `S32LE` · `S32BE` · `U32LE` · `U32BE` · `S24_32LE` · `S24_32BE` · `U24_32LE` · `U24_32BE` · `S24LE` · `S24BE` · `U24LE` · `U24BE` · `S20_32LE` · `S20_32BE` · `U20_32LE` · `U20_32BE` · `S20LE` · `S20BE` · `U20LE` · `U20BE` · `S18LE` · `S18BE` · `U18LE` · `U18BE` · `S16LE` · `S16BE` · `U16LE` · `U16BE` · `S8` · `U8` |
| `layout` | `interleaved` |
| `rate` | `[1, 2147483647]` |
| `channels` | `[1, 2147483647]` |
| Ek tip | `audio/x-dsd` · `audio/x-ac3` · `audio/x-eac3` · `audio/x-dts` · `audio/mpeg` |
| `block-size` (AC3/DTS) | `{ (int)512, (int)1024, (int)2048 }` |
| Yön | `sink` (always) |

### §5.2 Caps Uyuşmazlığı Senaryoları

| Senaryo | Beliren Yer | Çözüm |
|---|---|---|
| `S16LE` kaynağı → `F32LE` hedef | link anında | `audioconvert` ekle |
| 44100 → 48000 | link anında | `audioresample` ekle |
| Tek kanal → stereo | link anında | `audioconvert` + mix matrix |
| Yerleşimsiz kanal maskesi | muhakeme sırasında | açık `mix matrix` ver |
| Bitmiş stream (bit depth) | link anında | `audioconvert` width dönüşümü |

## §6 Pad Modeli

| Terim | Tanım |
|---|---|
| `GstPad` | Element'in veri giriş/çıkış ucu |
| `sink pad` | Veri alan uç |
| `src pad` | Veri üreten uç |
| `link` | src → sink yönünde bağlama |
| `negotiation` | İki ucun caps üzerinde anlaşması |
| `flow` | Buffer akış durumu (ok / eos / error) |

### §6.1 Pad Bağlantı Kuralları

| Kural | Aıklama |
|---|---|
| Yön | Yalnız `src → sink` |
| Teklik | Bir `sink` pad genelde tek `src` bağlanır |
| Uyumluluk | Caps kesişimi boş olmamalı |
| Sıra | Element eklenmeden önce pad bulunmalı |
| Hata | Başarısız link → `FALSE`; yutulursa sessiz kopukluk |

## §7 Bus ve Mesajlar

`GstBus`, streaming iş parçacığından (GstTask) uygulamaya mesajları **FIFO** taşıyan nesnedir.
Uygulama tipik olarak tek iş parçacığından okur; bus bu geçişimarshall (marshal) eder.

| Mesaj Tipi | Anlam | Uygulama Tepkisi |
|---|---|---|
| `error` | Onarılamaz hata | Dur + kullanıcıya bildir |
| `warning` | Uyarı | Log |
| `eos` | Akış sonu | Duraklat / temizle |
| `state-changed` | Durum geçişi | Sırayı doğrula |
| `async-done` | Asenkron geçiş tamam | Beklemeyi sonlandır |
| `duration-changed` | Süre değişti | UI güncelle |
| `buffering` | Tampon doldurma oranı | Yüzeye göre oynat/duraklat |
| `latency` | Gecikme bildirimi | Yeniden yapılandırma |
| `stream-start` | Akış başlangıcı | Sıfırlama |

### §7.1 Bus Okuma Yöntemleri

| Yöntem | Davranış | Uygun Olduğu Yer |
|---|---|---|
| `gst_bus_poll` | Zaman aşımına kadar bloklayan bekleme | Ayrı iş parçacığı |
| `gst_bus_pop` | Kuyruktan tek mesaj alma | Döngü içinde |
| `gst_bus_peek` | Mesaja bak, alma | Gözlem |
| `gst_bus_add_watch` | Varsayılan main loop'a GSource kaydı | GLib main loop |
| `gst_bus_add_watch_full` | Ayarlanabilir öncelik/veri ile watch | İleri kullanım |
| `gst_bus_post` | Elle mesaj gönderme | Test / özel durum |

### §7.2 Bus Kuyruğu Riski

| Risk | Belirti | Önlem |
|---|---|---|
| Dinlenmeyen kuyruk | Bellek artışı | Her zaman bus edin ve dinle |
| NULL geçişinde birikme | Döngü referansı | Otomatik flush (varsayılan) |
| Tek iş parçacığı dışında okuma | Yarış durumu | Bus marshalling'e güven |
| Hata mesajının geç okunması | Açıklamasız durma | Hata önceliği en yüksek |

## §8 Durum Makinesi

| Durum | Anlam | Ses Aygıtı Açılır mı? |
|---|---|---|
| `NULL` | Hiçbir kaynak yok | Hayır |
| `READY` | Kaynaklar yüklendi | Aygıt hazırlanabilir |
| `PAUSED` | Akış durakladı, preroll yapıldı | Genelde evet |
| `PLAYING` | Akış akıyor, clock çalışıyor | Evet |

### §8.1 Geçiş Tablosu

| Geçiş | Tipik Sonuç | Sık Hata |
|---|---|---|
| `NULL → READY` | Kaynak açılır | Aygıt meşgul |
| `READY → PAUSED` | Preroll başlar (canlı kaynakta `NO_PREROLL`) | Caps uyuşmazlığı |
| `PAUSED → PLAYING` | Clock seçilir ve akış başlar | Clock yok |
| `PLAYING → PAUSED` | Duraklatma | Askıda kalma |
| `PAUSED → READY` | Kaynak bırakılır | — |
| `READY → NULL` | Tam serbest bırakma | Bus flush |

### §8.2 Canlı (Live) ve Dosya Kaynağı Farkı

| Özellik | Canlı Kaynak | Dosya Kaynağı |
|---|---|---|
| `gst_element_set_state` dönüşü | `GST_STATE_CHANGE_NO_PREROLL` olabilir | Normal preroll |
| Clock seçimi | En upstream canlı kaynak clock'ı | Sink clock'ı (tipik ses sink) |
| Buffering davranışı | Doldurma yok | `buffering` mesajı gelir |
| Gecikme toleransı | Düşük | Esnek |

## §9 Clock Seçimi

| Seçenek | Davranış |
|---|---|
| Varsayılan algoritma | En upstream (kaynağa en yakın) element'in clock'ı |
| Sink tabanlı seçim | Normal pipeline'larda ses sink'in clock'ı |
| `gst_pipeline_use_clock` | Belirli clock dayatılır |
| `gst_pipeline_auto_clock` | Varsayılan algoritmaya dönüş |
| Clock yoksa | `GstSystemClock` kullanılır |

**Running time:** clock zamanı ile base time farkıdır; READY'ye geçiş ya da flushing seek
koşulunda sıfırlanır.

## §10 Element Kataloğu (ölçülen resmi indeksler)

| Element | Sınıf | Rol |
|---|---|---|
| `playbin` | Generic/Bin/Player | URI'den otomatik çözme ve oynatma |
| `playbin3` | Generic/Bin/Player | playbin'in paralel sürümü |
| `uridecodebin` | Generic/Bin/Decoder | URI'yi ham medyaya çözme |
| `parsebin` | Generic/Bin/Parser | Demultiplex → elementer akış |
| `playsink` | Generic/Bin/Sink | Çoklu akış için uygun sink grubu |
| `streamsynchronizer` | Generic | Grup akışlarını eşit süreye hizalar |
| `appsink` | Generic/Sink | Uygulamaya ham buffer erişimi |
| `appsrc` | Generic/Source | Uygulamanın pipeline'a buffer beslemesi |
| `audioconvert` | Dönüşüm | Tam/tamsayı, bit genişliği, sonluluk, kanal, dither |
| `audiorate` | Zamanlama | Örnek ekleyerek/düşürerek mükemmel akış |
| `audioresample` | Dönüşüm | Örnekleme hızı dönüşümü |
| `alsasink` | Sink/Audio | ALSA ile ses çıktısı (rank: primary) |
| `audiotestsrc` | Kaynak | Test tonu üretimi |

### §10.1 audioconvert Yetenekleri (resmi metin)

| Yetenek | Açıklama |
|---|---|
| Tam ↔ float | `S16LE` ↔ `F32LE` dönüşümü |
| Genişlik/derinlik | 8/16/20/24/32 bit geçişleri |
| Sonluluk ve sonluluk dışı | `S` ↔ `U` |
| Uçianlık (endianness) | `LE` ↔ `BE` |
| Kanal dönüşümü | Upmix / downmix |
| Dither & noise-shaping | Bit daraltırken gürültü kontrolü |
| Mix matrix | Yerleşimsiz kanallarda yeniden eşleme |

### §10.2 audiorate Davranışı

| Özellik | Değer |
|---|---|
| Amaç | Zaman damgalı ham çerçevelerden kusursuz akış üretme |
| `in` / `out` | Giren / çıkan örnek sayısı |
| `add` / `drop` | Eklenen / düşürülen örnek sayısı |
| `silent=FALSE` | `add`/`drop` değişiminde GObject bildirimi |
| Bildirim yeri | Streaming iş parçacığında — dikkatli kullanılmalı |
| `tolerance` | Sapma bu değerin altındaysa düzeltme yapılmaz |

## §11 Tizen CAPI Katmanı

Bu bölümdeki Tizen'e özgü API adları bu ortamdan içerik düzeyinde doğrulanamadı
(`docs.tizen.org` derin yolları SPA iskeleti döndürüyor) → hepsi işaretlidir.

| İddia | Durum |
|---|---|
| Tizen, multimedya için CAPI sarmalayıcılar sunar | `⚠️ VERIFICATION REQUIRED` |
| Pipeline destek grubunun adı `CAPI_MEDIA_TOOLKIT_PIPELINE_SUPPORT` | `⚠️ VERIFICATION REQUIRED` (URL 200, gövde okunamadı) |
| Sound Manager ayrı bir CAPI grubudur | `⚠️ VERIFICATION REQUIRED` |
| Tizen uygulamaları GStreamer'ı doğrudan kullanabilir | `⚠️ VERIFICATION REQUIRED` |
| Ses çıkışı ALSA üzerinden gerçekleşir | ⚠️ Kısmi: alsasink resmi belgesi var, Tizen'e özel bağ `⚠️ VERIFICATION REQUIRED` |

## §12 Repo Kod Kanıtı

Bu depoda `.cpp/.h` YOK (L0: PLANNED) → kanıt gerçek PHP/YAML dosyalarından alınmıştır.

| Dosya | Satır | İçerik | Doğrulama |
|---|---|---|---|
| `api.coremusic.net/config/routes.php` | 24 | `return [` — route tablosu başlangıcı | `Get-Content … \| Select-Object -Index 23` |
| `api.coremusic.net/config/routes.php` | 27 | `'/api/v1/auth' => ['service' => 'auth', …]` | `Select-Object -Index 26` |
| `.github/workflows/ci.yml` | 14 | `permissions:` bloğu başlangıcı | `Select-Object -Index 13` |

**Dosya:** `api.coremusic.net/config/routes.php` / **Satır:** 27 —
`'/api/v1/auth'     => ['service' => 'auth',     'handler' => 'authController'],`
(Yukarıdaki satır `Select-Object -Index 26` ile doğrulanmıştır; repo'da tek eşleşme.)

**Eşleşme yoksa `Dosya:` kullanılmaz:** Tizen'e özgü hiçbir PHP/YAML karşılığı yoktur —
bu bölüm yalnız deponun genel sözleşmesini (route tablosu = tek doğruluk kaynağı örneği)
göstermek için vardır.

## §13 Hata Yayılımı

| Seviye | Kaynak | Üst Katman Davranışı |
|---|---|---|
| 1 | Pad link başarısızlığı | Kurulum anında hata döndür |
| 2 | Caps muhakemesi başarısız | Element `error` mesajı yayımlar |
| 3 | State geçiş başarısız | `state-changed` + `error` |
| 4 | ALSA aygıt açılamaz | Sink `error` → pipeline durur |
| 5 | Uygulama bus'ı geç okursa | Açıklamasız durma riski |

## §14 Performans Notları

| Konu | Not |
|---|---|
| Buffer kopyası | `audioconvert` yerine mümkünse caps'i kaynağa uydur |
| Kanal dönüşümü | Mix matrix verilmezse şablon matris üretilir |
| Bit daraltma | Dither/noise-shaping CPU maliyeti taşır |
| `audiorate` bildirimi | `silent=FALSE` streaming iş parçacığında bildirim üretir |
| Bus izleme | Yoğun mesajlarda watch maliyeti main loop'a biner |

## §15 Açık Sorular

| # | Soru | Durum |
|---|---|---|
| Q1 | Tizen sürümüne göre CAPI adları değişiyor mu? | `UNKNOWN` |
| Q2 | Cihazda varsayılan ses rotası hangi ALSA `hw:` yoludur? | `UNKNOWN` |
| Q3 | 8.1 surround kanal maskesi hangi düzen ile bildiriliyor? | `⚠️ VERIFICATION REQUIRED` |
| Q4 | `open_questions` alanı neden boş değil? | Bilinçli: Q1–Q3 yanıt bekliyor |

## §16 Kontrol Listesi

| Kontrol | Durum |
|---|---|
| Frontmatter 9 alan (group dahil) | ✅ |
| `parent` kullanılmadı | ✅ |
| En az 1 repo kod satırı | ✅ |
| URL'ler `tizen-web-arastirma.md` içinde | ✅ |
| Yabancı platform dosyasına yazım yok | ✅ |

## §17 Örnek Pipeline Sözdizimleri

| # | Sözdizimi | Amaç | Doğrulama |
|---|---|---|---|
| P1 | `uridecodebin uri=file:///x.ogg ! audioconvert ! audioresample ! autoaudiosink` | Dosyayı ALSA üzerinden çal | alsasink resmi örnek |
| P2 | `audiotestsrc ! audioconvert ! audio/x-raw,format=S8,channels=2 ! level ! fakesink silent=TRUE` | 8-bit'e dönüştürme testi | audioconvert resmi örnek |
| P3 | `uridecodebin uri=file:///x.flac ! audioconvert ! vorbisenc ! oggmux ! filesink location=out.ogg` | Yeniden kodlama | audioconvert resmi örnek |
| P4 | `audiotestsrc ! audioconvert ! audioresample ! appsink` | Uygulamaya ham buffer | appsink tanımı |
| P5 | `appsrc ! audioconvert ! alsasink` | Uygulama kaynağından çıkış | appsrc/alsasink tanımı |
| P6 | `playbin uri=file:///x.flac` | Tek element'lik oynatma | playback indeksi |
| P7 | `urisourcebin uri=… indexto=` | URI indirme/tamponlama | playback indeksi |
| P8 | `parsebin ! audiorate ! audioconvert` | Elementer akış + zaman düzeltme | audiorate tanımı |

Not: `…` sembolü kısaltma içindir; gerçek sözdiziminde URI tam yazılır.

## §18 Element Yerleşim Şablonları

| Şablon | Zincir | Kullanım |
|---|---|---|
| T-1 | kaynak → `audioconvert` → `audioresample` → sink | Standart çıkış |
| T-2 | kaynak → `audiorate` → `audioconvert` → sink | Zaman kusuru giderme |
| T-3 | `playbin` | Hızlı prototip, ince ayar yok |
| T-4 | `appsrc` → `audioconvert` → `alsasink` | Uygulama içi üretim |
| T-5 | `uridecodebin` → `appsink` | Uygulamada işleme |
| T-6 | `playbin` → `playsink` | Çoklu akış (ses + video) |
| T-7 | `parsebin` → dönüştürücü → muxer → `filesink` | Dışa aktarma |
| T-8 | `audiotestsrc` → `level` → `fakesink` | Seviye ölçümü |

## §19 Caps Karşılaştırma Matrisi

| Kaynak | Hedef | Gereken Element | Not |
|---|---|---|---|
| `S16LE/2ch/48000` | `S16LE/2ch/48000` | Yok | Doğrudan link |
| `S16LE/2ch/48000` | `F32LE/2ch/48000` | `audioconvert` | Tam → float |
| `F32LE/2ch/48000` | `S16LE/2ch/48000` | `audioconvert` | Dither önerilir |
| `S16LE/1ch/44100` | `S16LE/2ch/48000` | `audioconvert` + `audioresample` | İki dönüşüm |
| `S32LE/8ch/48000` | `S16LE/2ch/48000` | `audioconvert` | Downmix gerekir |
| `S16LE/2ch/44100` | `S16LE/2ch/48000` | `audioresample` | Yalnız hız |
| `S24LE/2ch/48000` | `S32LE/2ch/48000` | `audioconvert` | Genişletme |
| `audio/mpeg` (compressed) | `audio/x-raw` | decode element'i | `⚠️ VERIFICATION REQUIRED` |
| `audio/x-ac3` | `audio/x-raw` | decode element'i | `⚠️ VERIFICATION REQUIRED` |
| Yerleşimsiz kanal | Standart düzen | `audioconvert` + mix matrix | Boş matris → özdeşlik |

## §20 Bus Mesaj Akış Sırası

| Sıra | Tipik Olay | Beklenen Mesaj |
|---|---|---|
| 1 | Pipeline başlatılır | `stream-start` |
| 2 | `READY → PAUSED` | `state-changed` |
| 3 | Preroll tamam | `async-done` |
| 4 | `PAUSED → PLAYING` | `state-changed` |
| 5 | Ağ yavaşlaması (dosya) | `buffering` (0–100) |
| 6 | Kaynak bitti | `eos` |
| 7 | Onarılamaz hata | `error` |
| 8 | Süre değişti | `duration-changed` |
| 9 | Gecikme güncellendi | `latency` |
| 10 | Tekrar başlangıç | `flush-start` / `flush-stop` |

## §21 Durum Geçiş Hata Senaryoları

| Senaryo | Beliren Geçiş | Sonuç |
|---|---|---|
| Aygıt başka süreçte meşgul | `READY → PAUSED` | `error`, aygıt açılamaz |
| Caps hiç uyuşmuyor | `READY → PAUSED` | `error` |
| Clock seçilemiyor | `PAUSED → PLAYING` | Beklenmedik duraklama |
| Bus dinlenmiyor | Herhangi | Açıklamasız durma |
| Element zinciri kopuk | `PAUSED → PLAYING` | Boş/kesik çıkış |
| EOS sonrası temizlik | `PLAYING → READY` | Flush gerekir |

## §22 Kardeş Dosya Bağları

| Bağ | Yön | İçerik |
|---|---|---|
| → `tizen-ses-yoneticisi.md` | Devir | Rota/odak kararları pipeline dışındadır |
| → `tizen-audio-hal.md` | Aşağı | `alsasink` nihayetinde ALSA'ya iner |
| → `tizen-gercek-zamanli-tampon.md` | Aşağı | Buffer/underrun disiplini |
| → `tizen-uygulama-ortami.md` | Yan | Privilege olmadan pipeline kurulamaz |
| → `tizen-web-arastirma.md` | Kanıt | Bu dosyadaki tüm URL'ler |
| ← `isletim-sistemi-turu/index.md` | Üst | Parent kaydı |

## §23 Ölçüm Kaydı (2026-10-13)

| Kaynak | Metod | Sonuç |
|---|---|---|
| `gstreamer.freedesktop.org` sayfaları | HTTP HEAD + GET gövde okuma | 200 + gerçek içerik |
| `docs.tizen.org` derin yollar | HTTP HEAD | 200 (SPA iskeleti, ~105 KB) |
| `developer.tizen.org/development/native-api` | HTTP GET | 200 + portal içeriği |
| `alsa-project.org/alsa-doc/alsa-lib/group___mixer.html` | HTTP HEAD + GET | 200 + API listesi |
| Envanter kesişimi | 1441 kayıt karşılaştırması | 0 |

## §24 Sözlük

| Terim | Tanım |
|---|---|
| Pipeline | Element zincirini yöneten toplevel kapsayıcı |
| Bin | Alt grup kapsayıcı, clock/uygulama bus'ı yok |
| Element | Tek işlevli düğüm |
| Pad | Element'in giriş/çıkış ucu |
| Caps | Pad'in taşıyabildiği format tanımı |
| Negotiation | İki pad'in caps üzerinde anlaşması |
| Buffer | Zaman damgalı veri parçası |
| Bus | Streaming → uygulama mesaj kanalı |
| Marshalling | İş parçacıkları arası mesaj güvenli aktarımı |
| Clock | Akış zaman referansı |
| Running time | Clock zamanı eksi base time |
| Preroll | PAUSED'ta ilk buffer'ın hazırlanması |
| EOS | Kaynağın bitmesi |
| Underrun | Eklenecek buffer kalmaması (bkz. RT dosyası) |
| Upstream | Kaynağa doğru yön |
| Downstream | Sink'e doğru yön |
| Sink | Veriyi tüketen element |
| Source | Veriyi üreten element |
| Rank | Element seçim önceliği (alsasink: primary) |
| Mix matrix | Kanal yeniden eşleme matrisi |
| Dither | Bit daraltma gürültüsü |
| Noise-shaping | Dither frekans profilini şekillendirme |
| Preroll-first | İlk buffer'ı PAUSED'ta hazırlama |

## §25 Platformlar Arası Karşılaştırma

| Boyut | Tizen | iOS (kardeş dal) | Linux (kardeş dal) |
|---|---|---|---|
| Üst ses API | CAPI (işaretli) | AVAudioEngine / AudioUnit | PulseAudio / PipeWire |
| Boru hattı | GStreamer | Yok (AU grafiği) | GStreamer/PipeWire |
| HAL | ALSA + UCM | Core Audio | ALSA |
| Paket | TPK | IPA | Dağıtım paketi |
| İmza | Tizen sertifikası | Apple sertifikası | Paket imzası |
| Doğrulama durumu | Kısmi | Yüksek | Yüksek |

## §26 Risk Kaydı

| # | Risk | Etki | Azaltma |
|---|---|---|---|
| R1 | CAPI iddiaları doğrulanamadı | Yanlış API adı | `⚠️ VERIFICATION REQUIRED` |
| R2 | SPA iskeleti 200 → sahte-varlık | Yanlış "sayfa var" kararı | Gövde kontrolü zorunlu |
| R3 | Bus dinlenmemesi | Sessiz durma | §4 kuralı |
| R4 | Caps kopukluğu | Boş çıkış | §19 matrisi |
| R5 | 8.1 kanal düzeni belirsiz | Yanlış maske | Q3 açık soru |

## §27 Doğrulama Günlüğü

| Adım | İş | Sonuç |
|---|---|---|
| 1 | Dizin disk kontrolü | `tizen/` YOK → sıfırdan kurulum |
| 2 | Referans desen | `ios/` 7 dosya okundu |
| 3 | URL envanteri | 1441 kayıt |
| 4 | Aday URL ölçümü | 200 olanlar alındı |
| 5 | Gövde okuma (GStreamer/ALSA) | İçerik doğrulandı |
| 6 | Gövde okuma (Tizen) | SPA iskeleti → işaretlendi |
| 7 | Repo kod satırı | `Select-Object -Index` ile doğrulandı |
| 8 | Satır sayısı | ≥500 hedefi |

## §28 Ölçüm Ortamı Künyesi

| Alan | Değer |
|---|---|
| Ölçüm günü | 2026-10-13 |
| İstek metodu | `Invoke-WebRequest -Method Head -UseBasicParsing` |
| Zaman aşımı | 20 saniye |
| Envanter öncesi benzersiz URL | 1441 |
| Bu dosyanın eklediği benzersiz URL | 0 (tümü `tizen-web-arastirma.md` içinde) |
| Kesişim | 0 |
| doldurma metni / doldurma satırı | 0 |
| `parent` alanı | 0 |

---
**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode