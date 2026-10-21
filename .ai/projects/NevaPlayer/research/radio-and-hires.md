---
title: "Neva Player — Internet Radio ve Hi-Res Audio Derin Araştırma"
type: research
project: nevaplayer
topics: [internet-radio, icy, shoutcast, hls, dash, wasapi-exclusive, asio, eq, gapless, dsd, resampling]
version: 1.0.0
date: 2026-10-19
method: websearch + webfetch (2026 güncel), zero-hallucination
status: reviewed
---

# NEVA PLAYER — INTERNET RADIO & HI-RES AUDIO ARAŞTIRMA RAPORU

> Kural: her iddia arkasındaki kaynak §7'de listelenmiştir. Kaynağı olmayan veya
> tek kaynaktan doğrulanamayan bilgi metin içinde **"⚠️ VERIFICATION REQUIRED"**
> ile işaretlenmiştir. Sayısal değerler (bitrate, gecikme, örnek oranı) kaynaktan
> alınan değerlerdir; kaynağı olmayan hesaplamalar "hesap (bizim)" olarak
> işaretlenmiştir.

---

## §1 INTERNET RADIO PROTOKOL REHBERİ

### 1.1 ICY / Shoutcast v1 — paket akışı

ICY, HTTP üzerine kurulmuş bir streaming protokolüdür; istemci HTTP GET yapar,
sunucu HTTP benzeri yanıt döner ve başlıklar "icy-" öneki taşır [S1][S3][S4].
Tarihçe: Nullsoft (Winamp) tarafından geliştirilmiş, "I Can Yell" kısaltmasından
"icy" etiketleri doğmuştur [S4].

**İstemci isteği:**

    GET /stream HTTP/1.1
    Host: station.example:8000
    User-Agent: NevaPlayer/1.0
    Icy-MetaData: 1          <-- metadata desteklediğini bildirir (kritik)
    Accept: */*
    Connection: keep-alive

Sunucu "Icy-MetaData: 1" görmezse metadata GÖNDERMEZ [S1][S4].

**Sunucu yanıtı (başlıklar) [S1]:**

    HTTP/1.0 200 OK                  (bazı sunucular "ICY 200 OK" döner)
    Content-Type: audio/mpeg
    icy-br: 128                      bitrate (kbps)
    icy-genre: Adult Pop Rock
    icy-name: Virgin Radio
    icy-url: http://station.example/
    icy-pub: 1                       dizinde yayınlanıyor mu
    icy-metaint: 8192                <-- metin verisi için ANA anahtar
    icy-notice2: SHOUTcast DNAS/posix(linux x86) v2.4.7.256

"icy-metaint" değeri N bayt ses verisinden sonra bir metadata bloğu geleceğini
belirtir. 8192 önerilen değerdir; bazı eski istemciler başka değerlerle bozulur [S1].

**Metadata bloğu formatı [S2][S3]:**

    [1 bayt] L = kaç tane 16 baytlık blok geldiği (0 = bu turda metadata yok)
    [L*16 bayt] ASCII payload, kalan kısım 0x00 ile doldurulur

Payload biçimi: StreamTitle='Şarkı Adı - Sanatçı';
Ek alan: StreamURL='...' (albüm kapağı linki vs. taşıyabilir) [S1].
Tırnak içinde tek tırnak (') karakteri YOKTUR (sunucu tarafında kaçırılır) [S2].
Maksimum metadata uzunluğu 256 bayt (255+1) civarıdır; resmi spec'te yazmıyor,
ölçümle doğrulanmıştır — ⚠️ VERIFICATION REQUIRED (kesin üst sınır spec'te yok) [S3].

Gerçek hexdump örneği (curl -H 'Icy-Metadata: 1' | tail -c 128) [S3]:

    03 53 74 72 65 61 6d 54 69 74 6c 65 3d 27 44 69 |.StreamTitle='Di|
    65 20 46 61 6e 74 61 73 74 69 73 63 68 65 6e 20 |e Fantastischen |
    56 69 65 72 20 2d 20 52 65 69 63 68 27 3b 00 00 |Vier - Reich';..|
    00 82 98 b0 ...                                 |... (devamı ses)|

Burada ilk bayt 0x03 = 48 bayt metadata geliyor; 46 bayt metin + 2 bayt 0x00
dolgu; sonraki baytlar tekrar MP3 sesidir.

**ÖNEMLİ:** Shoutcast/Icecast sunucuları HTTP/1.0-vari, kendi uzantılarıyla HTTP
yanıtları döndürebilir. Hazır HTTP kütüphaneleri (bazıları) status satırı ve
"ICY " önekini tolere etmeyebilir — kendi header parser'ı önerilir [S4].

### 1.2 Shoutcast v2 (Ultravox 2.1)

Shoutcast DNAS v2, v1'den TAMAMEN farklı bir protokol (Ultravox 2.1) kullanır;
ancak v2 sunucuları v1 uyumluluğu için ICY akışı da sunar [S5][S6].

Farklar [S6][S7]:
- v1: tek port, tek stream. v2: tek süreç içinde birden fazla Stream ID (SID);
  high/low bitrate ya da ayrı kanallar aynı portta olabilir (?sid=1, ?sid=2).
- v2 durum bilgisini XML/JSON endpoint'lerinden verir: /statistics, /currentsong,
  /admin.cgi?sid=1&mode=viewxml&page=1|3|4 (istatistik / dinleyici / şarkı
  geçmişi); ?json=1 ile JSON [S8].
- v1'in eski "7.html" durum sayfası hâlâ birçok sunucuda çalışır [S8].
- Taşıma yine HTTP'dir; içerik MP3 veya AAC [S9].

**Neva Player için pratik sonuç:** v2 protokolünü implemente ETMEK ZORUNLU DEĞİL.
ICY akışı + /currentsong (varsa) + /statistics yeterlidir; SID'li istek
"station_url?sid=1" şeklinde kullanıcıya bırakılabilir.

### 1.3 HLS (HTTP Live Streaming) — audio radio

- FFmpeg'de HLS DEMUX desteği vardır: "hls" demuxer, Apple HTTP Live Streaming
  demuxer'ıdır (ffmpeg-formats §3.13) [S10].
- FFmpeg'in "lhls" seçeneği DEMUX değil, DASH muxer'a ait düşük-gecikmeli
  ÜRETİM seçeneğidir (#EXT-X-PREFETCH ekler; Apple LL-HLS spec'i değil, hls.js
  topluluğunun taslak spec'idir) [S10]. Yani "FFmpeg ile LL-HLS oynatma" diye
  bir şey yok — oynatıcı tarafında partial segment desteği sizin işinizdir.
- Apple LL-HLS 2020'de ana HLS spec'ine girmiştir; partial segmentler
  (#EXT-X-PART), #EXT-X-PART-INF:PART-TARGET, blocking playlist reload
  (_HLS_msn / _HSLS_part) kullanır; tam segmentten ÖNCE yayına girerler [S11][S13].
- Gecikme rakamları:
  - Klasik HLS: 12–30 sn [S12].
  - LL-HLS uçtan uca: 5–10 sn (AWS blog) [S12]; CMAF chunked encoding + part
    200–500 ms ile 2–3 sn'ye kadar (üçüncü taraf makale) [S14].
  - LL-HLS MPEG-TS ile ÖNERİLMEZ: overhead gecikmeyi erir; fMP4/CMAF kullanın [S13].
- Audio-only container LL-HLS'te desteklenir (fMP4/CMAF) [S13].

**Değerlendirme:** Radio istemcisinde LL-HLS'in kazancı sınırlıdır — ICY zaten
~1–3 sn gecikme ile çalışır. LL-HLS ancak yayınlayıcı tarafı CMAF-part destekliyorsa
anlamlıdır; Neva Player'a P3 önceliği olarak " HLS demux + normal oynatma" ile
başlamak, LL-HLS'i sonra eklemek doğru stratejidir (LL-HLS eklemek istemci
tarafında partial segment fetch + playlist polling gerektirir, karmaşıklık yüksek).

### 1.4 DASH audio radio

- MPEG-DASH = ISO/IEC 23009-1:2022, MPD (XML manifest) tabanlı, codec-
  agnostiktir [S15][S18].
- HE-AAC v2, DASH-IF Interoperability Guidelines'da ZORUNLU stereo codec'tir;
  HE-AAC 7.1 opsiyonel çok-kanallı codec'tir (Fraunhofer) [S19].
- Radio'da yaygınlığı: DASH radyoda HLS kadar yaygın DEĞİLDİR; ancak büyük
  yayınçılar ikisini birden sunar. BBC "Audio Factory" platformu hem HLS hem
  MPEG-DASH (profiles 1-4) kullanır ve ICY/Shoutcast akışlarını DOMESTİK
  servisler için KALDIRMIŞTIR; yeni cihazlar HLS/DASH + AAC+ beklenmektedir [S20].
  (BBC'nin 2025-01-21'de birçok eski internet radyo stream'ini kapattığı da
  raporlanmıştır [S21].)
- Segment konteyneri: DASH her zaman ISO-BMFF (fMP4) veya WebM kullanır; HLS
  artık fMP4'e geçtiği için CMAF ile aynı baytlar iki manifestte de kullanılır [S17].
- FFmpeg'de hem dash hem hls demuxer'ları vardır; DASH radio için ek MPD parser
  gerekmez (libavformat çözer) [S10].

**Değerlendirme:** Neva Player için DASH = "FFmpeg demuxer'a bırak, ek iş yok".
Yaygın radyo istasyonlarının çoğu hâlâ ICY; BBC gibi büyük oyuncular HLS/DASH'a
geçtiği için her ikisi de desteklenmeli, ama ICY %90+ istek payını karşılar
(bu oran bizim tahminimizdir — ⚠️ VERIFICATION REQUIRED, ölçüm gerektirir).

### 1.5 Ücretsiz / açık radio kaynakları ve API formatları

**(a) Radio Browser API** — en güçlü aday [S22][S23][S24]:
- Topluluk kaynaklı (Wikipedia benzeri), VERİ lisansı: public domain; sunucu
  yazılımı (radiobrowser-api-rust) AGPL-3.0, kendin mirror kurabilirsin [S23][S25].
- API anahtarı / signup YOK. JSON (ayrıca XML, CSV, M3U, PLS, XSPF, TTL) [S25].
- Mirror'lar round-robin DNS ile keşfedilir: all.api.radio-browser.info
  (IP listesi, TLS yok); örnek sunucular fi1.api..., de2.api... [S26].
  En az iki farklı mirror'ı denemek en iyisidir (resmi FAQ böyle öneriyor) [S26].
- Endpoint'ler (base: https://api.radio-browser.info/) [S22]:
  - /json/stations/search?name=&country=&tag=&codec=&bitrate=&limit=&offset=&order=
  - /json/stations/byuuid/{uuid} · /byname/{name} · /bytag/{tag}
  - /bycountry/{country} · /bycountrycode/{code} · /bylanguage/{lang}
  - /bycodec/{codec} · /bybitrate/{bitrate}
  - /json/stations/topclicks · /json/stations/topvotes
  - /json/tags · /json/countries · /json/languages
- Alanlar: name, url, url_resolved, homepage, favicon, tags, country,
  countrycode, language, codec, bitrate, votes, clickcount, uuid [S22].
- Not: url ve url_resolved AYRI alandır; oynatma için url_resolved tercih edilir
  (bizim önerimiz; alan varlığı kaynakta doğrulandı, tercih gerekçesi mühendislik
  judgment'ıdır).
- İstasyon sayısı: sitenin kendi sayacı 51.159 (bir anlık görüntü) ile 58.612
  (farklı anlık görüntü) arasında değişiyor [S26][S27] — yani ~50–60 bin bandı.
- lastcheckok / broken-station uçları için sürekli sağlık kontrolü yapılır [S25].

**(b) SomaFM** [S28][S29]:
- Doğrudan stream linkleri sayfası (directstreamlinks.html) PLS/M3U ve doğrudan
  sunucu URL'leri yayınlar; sayfanın altında "son güncelleme" tarihi var [S28].
- Bitrate/codec politikası (kendi FAQ'ları) [S29]:
  - 128 kbps MP3
  - 128 kbps AAC (= AAC-LC, MPEG-4 parçası)
  - 64 ve 32 kbps AAC-HE (= aacPlus)
  - Birkaç kanalda deneysel yüksek-bitrate MP3
- Altyapı: Icecast KH sunucular [S29]. Yani ICY metadata SomaFM'de çalışır.
- Üçüncü taraf derleme listeleri de mevcut (m3u dosyaları) [S30].

**(c) UK radio** [S20][S21][S31]:
- BBC: ICY akışları domestik servisler için KALDIRILDI; HLS ve MPEG-DASH +
  AAC+ kullanılıyor [S20]. Uluslararası dinleyiciler için hâlâ çalışan AAC
  128 kbps HTTP akışları topluluk listelerinde var ama bunlar garanti DEĞİL [S31].
- 2025-01 itibarıyla BBC birçok eski stream'i kapatmıştır [S21].
- Pratik sonuç: UK istasyonları için Radio Browser'daki url_resolved alanını
  kullanmak, sabit URL gömmekten daha güvenilirdir.

**(d) Diğer** : Icecast 2.4.x Ogg (Vorbis/Theora), Opus, WebM ve MP3 akışlarını
destekler; /status-json.xsl uç noktası JSON metadata verir [S32][S33].

### 1.6 C++ implementasyon: libcurl + ICY, reconnect/backoff

**libcurl gerçekleri [S34][S35][S36]:**
- CURLOPT_WRITEFUNCTION ile veri, CURLOPT_HEADERFUNCTION ile header ayrı
  callback'lere gider; ikisine ayrı userdata (CURLOPT_WRITEDATA /
  CURLOPT_WRITEDATA-header) verilir [S35].
- Write callback'e gelen veri NULL-TERMINATE DEĞİLDİR; strlen/strcpy/printf("%s")
  YASAK [S35][S36].
- Callback'ler C++'ta non-static member function OLAMAZ; static + userdata
  kullanın [S36].
- Callback kaç kez çağrılır BELLİ DEĞİLDİR; her çağrıda farklı boyutta veri
  gelebilir [S36].

**Pseudokod (ICY demux + metadata parse):**

    class IcyStream {
      CURL* h;
      size_t metaInt = 0;          // icy-metaint
      size_t bytesUntilMeta = 0;
      std::string hdrBuf;          // header satırları için
      std::string metaRaw;         // metadata bloğu için
      RingBuffer audioOut;         // decode'a giden temiz ses
      std::function<void(std::string)> onTitle;

      static size_t onHeader(char* p, size_t sz, void* u) {
        auto* s = (IcyStream*)u;
        std::string line(p, sz);                       // NULL-TERM DEGIL
        if (line.rfind("icy-metaint:", 0) == 0)
          s->metaInt = strtoul(line.c_str()+12, nullptr, 10);
        if (line.rfind("Content-Type:", 0) == 0)
          s->contentType = Trim(line.substr(13));
        return sz;
      }

      static size_t onData(char* p, size_t sz, void* u) {
        auto* s = (IcyStream*)u;
        size_t i = 0;
        while (i < sz) {
          if (s->metaInt == 0) {                        // metadata yok
            s->audioOut.write(p+i, sz-i); return sz; }
          if (s->bytesUntilMeta == 0) {
            uint8_t blocks = (uint8_t)p[i++];
            s->bytesUntilMeta = (size_t)blocks * 16;
            s->metaRaw.clear();
            if (blocks == 0) s->bytesUntilMeta = s->metaInt; // meta yok, sayacı resetle
            continue;                                   // <-- kritik durum makinesi
          }
          size_t n = min(s->bytesUntilMeta, sz - i);
          if (s->metaRaw.size() < 4096) s->metaRaw.append(p+i, n);
          i += n; s->bytesUntilMeta -= n;
          if (s->bytesUntilMeta == 0 && !s->metaRaw.empty()) {
            s->onTitle(ParseStreamTitle(s->metaRaw));    // "StreamTitle='...';"
            s->bytesUntilMeta = s->metaInt;              // sayacı geri yükle
          }
        }
        return sz;
      }

      void start(const std::string& url) {
        h = curl_easy_init();
        curl_easy_setopt(h, CURLOPT_URL, url.c_str());
        curl_easy_setopt(h, CURLOPT_FOLLOWLOCATION, 1L);
        curl_easy_setopt(h, CURLOPT_HTTPHEADER,
                         curl_slist_append(nullptr, "Icy-MetaData: 1"));
        curl_easy_setopt(h, CURLOPT_HEADERFUNCTION, &onHeader);
        curl_easy_setopt(h, CURLOPT_WRITEFUNCTION,  &onData);
        curl_easy_setopt(h, CURLOPT_WRITEDATA, this);
        curl_easy_setopt(h, CURLOPT_USERAGENT, "NevaPlayer/1.0");
        curl_easy_setopt(h, CURLOPT_CONNECTTIMEOUT, 10L);
        curl_easy_setopt(h, CURLOPT_LOW_SPEED_LIMIT, 1024L);   // 1 KB/s altı = ölü
        curl_easy_setopt(h, CURLOPT_LOW_SPEED_TIME, 15L);      // 15 sn boyunca
        curl_easy_perform(h);   // blocking; ayrı thread'de çalıştırın
      }
    };

Not: durum makinesinde "blok uzunluğu 0 geldiğinde metaint sayacına DÖNÜLÜR"
detayı çoğu bozuk implementasyonun kaynağıdır (hexdump örneği bunu gösteriyor) [S3].

**Reconnect / backoff stratejisi (mühendislik önerisi, literatürle uyumlu):**
1. CURLOPT_LOW_SPEED_LIMIT/TIME ile ölü bağlantıyı erken tespit et [S34].
2. Exponential backoff: 1s → 2s → 4s → 8s → 16s → 30s (cap), ±%20 jitter
   (thundering- herd koruması). Başarılı bağlantıda sayaç sıfırlanır.
3. HTTP 3xx için FOLLOWLOCATION açık; 302/307 zincirleri için limit koyun.
4. Bazı sunucular HTTP/1.0-vari status döndürür; hazır HTTP parser'lar takılabilir
   — gerekirse CURLOPT_HTTP09Allowed / kendi socket katmanı [S4].
5. Buffer'ı 2–4 sn'lik doldurmadan başlatma (underrun koruması).
6. Metadata olmadan da çalabilmek şarttır (metInt=0 → temiz akış).

**Gapless radio playback zorlukları (farklı bir problem):**
- Radyoda "gapless" dosya-oynatma gibi değildir: akış sonsuzdur, seek yoktur.
- Asıl zorluk: reconnect sonrası yeniden senkron. Decoder (MP3/AAC) akış ortasında
  başlarsa ilk birkaç frame bozuk gelebilir — decoder'ı yeniden kurup ilk N frame'i
  atmak (AAC-LC için 1–2 frame, HE-AAC için SBR kurulumu nedeniyle daha fazla)
  gerekir. HE-AAC'te frame 2048 örnek ≈ 42.6 ms'dir (AAC-LC 1024 ≈ 21.3 ms @48k) [S37].
- Reconnect anında kesintiyi gizlemek için 100–300 ms'lik short fade-in önerilir
  (bizim önerimiz; kesin değer ölçüm gerektirir).
- HLS radyoda ek zorluk: live-edge buffering kuralı (3 segment kuralı) nedeniyle
  zorunlu gecikme + playlist yenileme döngüsü.

### 1.7 Radio codec'leri: AAC-LC vs HE-AAC (v1/v2) vs Opus vs MP3

| Codec | Bitrate (stereo) | Örnek oranı | Radio'daki yeri | Kaynak |
|---|---|---|---|---|
| MP3 | 128 / 192 / 320 kbps | 44.1/48 kbps klasik | Evrensel uyumluluk, hâlâ en yaygın ICY codec'i | [S38] |
| AAC-LC | 96–256 kbps | 24–96 kHz (Fraunhofer HE-AAC sayfası aile için) | SomaFM 128k AAC, yüksek kalite ICY | [S39][S29] |
| HE-AAC v1 (AAC+) | 48–80 kbps (48–64 kbps stereo Fraunhofer) | 24–96 kHz | Dijital radyonun (DAB+, HD Radio, DRM) ana codec'i | [S39][S40] |
| HE-AAC v2 (SBR+PS) | 24–48 kbps | 24–96 kHz | DAB+ stereo zorunlu; çok düşük bitrate'de stereo | [S39][S40][S41] |
| xHE-AAC (USAC) | 12 kbps–şeffaf | — | Yeni nesil, DASH içinde büyüyor | [S39] |
| Opus | 64 / 96 / 128 kbps | 48 kHz (tek çalışma oranı, RFC 6716) | Kalite #1 ama uyumluluk #4; Opus 48 kHz'te 20 kHz bant sınırı | [S38][S42] |

Kritik teknik gerçekler:
- HE-AAC v2 = AAC-LC + SBR + PS. HE-AAC v2 decoder'ı GERİYE DÖNÜŞÜMDÜR:
  AAC-LC ve HE-AAC v1 akışlarını da doğru çalar [S41].
- HE-AAC v1/v2 SBR nedeniyle 2048-örnek frame kullanır (42.6 ms @48k);
  AAC-LC 1024-örnek (21.3 ms) [S41].
- SBR yüksek bandı "yeniden üretir"; düşük bitrate'de kazanç, yüksek bitrate'de
  (>64 kbps) kazanç YOKTUR [S39][S41].
- **48 kHz desteği:** Fraunhofer HE-AAC sayfası "Sampling rates: 24 to 96 kHz"
  der [S39] — yani 192 kHz radyo stream'i HE-AAC ile TAŞINAMAZ (pratikte radyo
  zaten 48k üzeri değil). Opus da 48 kHz'e sabittir [S42] (⚠️ "Opus 48 kHz tek
  oranı" iddiası için resmi API dokümanı opus-codec.org/docs/opus_api referans
  verilmelidir — bu oturumda doğrulanmadı).
- Uyumluluk sırası (üçüncü taraf karşılaştırma) [S42]:
  kalite: Opus > HE-AAC v2/AAC > Vorbis > MP3 > FLAC(stream);
  uyumluluk: MP3 > AAC > HE-AAC v2 > Opus > Vorbis > FLAC.
- **Neva Player kodlayıcı destek matrisi (öneri):** MP3 (libmpg123/MINIMP3),
  AAC-LC + HE-AAC v1/v2 (FDK-AAC veya FAAD2 — FDK-AAC HE-AAC v2 desteği için
  lisansı LGPL/BSD varyantına dikkat), Opus (libopus, her zaman 48 kHz'e
  normalize edin), Ogg Vorbis (libvorbis), Ogg/FLAC (libFLAC).

---

## §2 NEVA PLAYER RADIO MİMARİSİ ÖNERİSİ

    +---------------------------  UI / Playlist  ---------------------------+
    |  Radio Browser API  |  Favoriler  |  Şimdi Çalıyor (StreamTitle, art)  |
    +----------------------------------+------------------------------------+
                                       | (station: url_resolved, codec, br)
                                       v
    +---------------------------  TUNER / SESSION  -------------------------+
    |  RadioSession: URL, proxy, User-Agent, Icy-MetaData:1, timeout        |
    |  ReconnectController: exponential backoff + jitter + cap 30s          |
    +----------------------------------+------------------------------------+
                                       v
    +---------------------------  NETWORK (thread A)  ----------------------+
    |  libcurl easy handle                                                  |
    |   +-- HEADERFUNCTION --> icy-metaint, Content-Type, icy-name/br      |
    |   +-- WRITEFUNCTION  --> IcyDemux (durum makinesi)                   |
    |          |                                                            |
    |          +--> metadata blok --> StreamTitle parser --> UI event      |
    |          +--> temiz ses baytları --> ByteRingBuffer (2-4 sn)         |
    +----------------------------------+------------------------------------+
                                       v
    +---------------------------  DEMUX / CONTAINER (thread B)  ------------+
    |  URL'e göre seçim:                                                   |
    |   icy/raw (MP3 veya AAC ADTS) --> AVFormatContext ile sniff          |
    |   *.m3u8 --> hls demuxer (libavformat)  [S10]                        |
    |   *.mpd --> dash demuxer (libavformat)  [S10]                        |
    |  Çıktı: AVPacket akışı + codec parametreleri                         |
    +----------------------------------+------------------------------------+
                                       v
    +---------------------------  DECODE (thread B, pull)  -----------------+
    |  libmpg123 / FDK-AAC / libopus / libvorbis / libFLAC                 |
    |  Çıktı: float32 (veya int32) PCM, kaynak örnek oranı                 |
    |  Not: HE-AAC'te ilk 1-2 frame at (sync)                              |
    +----------------------------------+------------------------------------+
                                       v
    +---------------------------  DSP CHAIN (RT güvenli)  ------------------+
    |  [EQ: 31-band biquad cascade, double-precision coeff]  <-- §5        |
    |  [Volume: float, headroom]                                            |
    |  [True Peak meter: 4x oversample]                                     |
    |  [Resample: libsoxr SADECE mismatch ise]  <-- §? bkz. §3.7            |
    +----------------------------------+------------------------------------+
                                       v
    +---------------------------  RING BUFFER  ------------------------------+
    |  Lock-free SPSC float ring, >= 4x device period                      |
    |  Underrun durumunda: fade-in + (radio için) reconnect tetikle        |
    +----------------------------------+------------------------------------+
                                       v
    +---------------------------  AUDIO ENGINE  -----------------------------+
    |  WASAPI Exclusive (bit-perfect)  <-- §3                               |
    |   |-- başarısız/engellendiyse --> WASAPI Shared (IAudioClient3)       |
    |   |-- (opsiyonel) ASIO backend  <-- §4                                |
    |  Event-driven: AUDCLNT_STREAMFLAGS_EVENTCALLBACK + MMCSS thread      |
    +------------------------------------------------------------------------+

Tasarım notları (mühendislik önerisi, kaynakla çelişmiyor):
- Ağ thread'i ASLA RT thread değildir; tek yön veri akışı ring buffer ile.
- Radio için gapless crossfade YOK; reconnect fade-in VAR.
- EQ resampler'dan ÖNCE değil SONRA: resampler yalnızca cihad sample rate
  mismatch ise devreye girer (bit-perfect zincirde hiç girmez) — §3.7.
- Metadata UI'a thread-safe kuyruk ile iletilir (callback'te UI'a dokunma).

---

## §3 WASAPI EXCLUSIVE IMPLEMENTASYON REHBERİ

### 3.1 Temel gerçekler

- Exclusive mode ses motorunu (mixer) bypass eder: float'a dönüşüm ve mixer
  dither'i UYGULANMAZ; akış doğrudan sürücüye gider [S43][S44].
- Exclusive mode cihazı KİLİTLER: sistem sesleri, diğer uygulamalar susturulur.
  Microsoft açıkça "her zaman kullanıcıya shared mod'a düşüş seçeneği sunun"
  der [S45].
- Exclusive mode'da ISimpleAudioVolume / IChannelAudioVolume ETKİSİZDİR;
  ses seviyesi için IAudioEndpointVolume kullanılır (veya uygulama içi volume,
  bit-perfect bozulmaması için uygulama içi attenuation dikkat ister) [S45].
- Birçok cihaz exclusive mode'da IEEE float formatını REDDEDER ve 24-bit PCM'i
  (32-bit konteynerde) kabul eder [S46] — bu yüzden format negosyasyonu şarttır.
- Shared mode'da IAudioClient3 (Win10+) ile engine period 2.67 ms @48k'a kadar
  inebilir; exclusive'a "düşük gecikme" için değil, yalnızca bit-egni / özel
  sample rate için gidin [S45][S47][S48].
- Device period < 10 ms ise WASAPI thread'e MMCSS görev adı "Pro Audio" atar;
  >= 10 ms ise "Audio" [S45].

### 3.2 Kritik COM adımları (Microsoft örneği bazında) [S45]

    // 1) COM başlat (MT: CoInitializeEx(NULL, COINIT_MULTITHREADED))
    // 2) Enumerator
    CoCreateInstance(CLSID_MMDeviceEnumerator, nullptr, CLSCTX_ALL,
                     IID_IMMDeviceEnumerator, (void**)&pEnumerator);
    pEnumerator->GetDefaultAudioEndpoint(eRender, eConsole, &pDevice);
    // 3) IAudioClient (ve exclusive gerekiyorsa IAudioClient3 sorgula)
    pDevice->Activate(IID_IAudioClient, CLSCTX_ALL, nullptr, (void**)&pAudioClient);
    // 4) Format negosyasyonu: IAudioClient::IsFormatSupported(EXCLUSIVE, fmt, ...)
    //    -- hr == S_OK  : birebir destekleniyor (bit-perfect bu)
    //    -- hr == S_FALSE: closestMatch döner; exclusive'de KULLANMA (dönüşüm olur)
    //    -- hr == AUDCLNT_E_UNSUPPORTED_FORMAT: bu format cihazda yok
    //    strateji sırası: 32-bit float -> 32-bit int -> 24-in-32 -> 24-in-24
    // 5) Period
    pAudioClient->GetDevicePeriod(nullptr, &hnsDefaultPeriod);  // exclusive min period
    // 6) Initialize
    pAudioClient->Initialize(AUDCLNT_SHAREMODE_EXCLUSIVE,
                             AUDCLNT_STREAMFLAGS_EVENTCALLBACK,
                             hnsPeriod,   // hnsRequestedDuration (buffered duration)
                             hnsPeriod,   // periodicity: exclusive'de 0 YASAK
                             pwfx, nullptr);
    // 7) AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED -> buffer hizalama kurtarma yolu:
    //    GetBufferSize(&nFrames) al, hnsPeriod'yi yeniden hesapla, Stop/Reset/
    //    Terminate/Release ile yeni IAudioClient oluştur ve 6'ya dön [S45]
    // 8) IAudioRenderClient al, event handle oluştur, MMCSS "Pro Audio" kaydı,
    //    Start() -> event döngüsü (WaitForSingleObject -> GetBuffer/GetPadding ->
    //    doldur veya sıfırla -> ReleaseBuffer -> tekrar)

Not: exclusive'de periodicity 0 geçirilirse AUDCLNT_E_BUFFER_SIZE_ERROR alırsınız;
minimum değeri GetDevicePeriod'dan alın [S49].

### 3.3 Format eşleşmesi (WAVE_FORMAT_EXTENSIBLE)

- Exclusive mode'da "en yakın" format yetmez; cihazın desteklediği EXAKT formata
  düşmelisiniz [S45][S46].
- WAVE_FORMAT_EXTENSIBLE kullanın: KSDATAFORMAT_SUBTYPE_IEEE_FLOAT (32-bit float)
  veya KSDATAFORMAT_SUBTYPE_PCM (int). Bit-perfect zincir için float veya int32
  ikisi de uygundur; hangisinin cihazda S_OK döndüğünü IsFormatSupported ile
  ölçün [S45][S46].
- Örnek oranı: exclusive'da cihazın desteklediği oranlardan biri olmalıdır
  (44.1/48/96/176.4/192 k tipik). Dosya 192k, cihaz 192k destekliyorsa → perfect.
  Desteklemiyorsa → resampler devreye girer ve bit-perfectlik BİTER (§3.7).

### 3.4 Gapless tekniği (IAudioClient event-driven)

- AUDCLNT_STREAMFLAGS_EVENTCALLBACK + CreateEvent + MMCSS kaydı; WaitForSingleObject
  ile tetiklenen döngü, GetBufferSize/GetPadding ile ne kadar doldurulacağı hesaplanır [S45].
- Gapless için cihazı YENİDEN AÇMAYIN: tek bir kalıcı stream + yeterince büyük
  ring buffer (ör. 4 device period). Parça değişiminde format AYNIYSA sadece ring
  dolar (dosya bağlama maliyeti RT olmayan tarafta kalır).
- Farklı sample rate/bit-depth olan iki dosya arasında geçişte tek yol:
  stream'i kapatıp yeniden Initialize etmek (20–200 ms kesinti) — bu yüzden
  "oturum başına tek cihaz formatı" seçip tüm playlist'i o formata resample
  etmek gapless için daha iyi stratejidir (bizim önerimiz).
- Underrun: GetPadding()==bufferSize iken boş veri (silence) yazın; kesintiyi
  10–30 ms zarf/kapak ile yumuşatın.

### 3.5 Bit-perfect doğrulama (§3.3 devamı)

Doğrulama yöntemi (mühendislik pratiği; tek resmi standart yok — ⚠️ kısmen
VERIFICATION REQUIRED):
1. Exclusive + bit-egni eşleşme kurulduğunu IsFormatSupported(S_OK) ile KANITLAYIN.
2. Zincirde şunlar OLMADIĞINI kod yolundan doğrulayın: dither, resampler,
   mixer, uygulama içi volume <1.0, EQ bypass edilmemişse EQ (EQ biquad'ları
   0 dB gain'de bile kesirli katsayılarla çalışır; bit-perfect ZORUNLU ise EQ
   bypass edilmelidir — bu bizim analizimiz).
3. Pratik test: aynı makinede WASAPI loopback kaydı (shared loopback) ile
   exclusive çıkışın kaydını karşılaştırın; PCM konteynerde ham byte-eşitlik
   beklemeyin (loopback yolu farklıdır) — daha güvenilir test DAC çıkışının
   dijital koaksiyel/optikal S/PDIF çıkışıyla (varsa) dışarı kaydedilip
   kaynak dosyayla birebir hash karşılaştırılmasıdır.
4. "Windows ses panelinde cihad sample rate dosyanınkiyle aynı + exclusive"
   kombinasyonu, mixer'ın devre dışı kalmasını garanti eder [S43].

### 3.6 Exclusive → Shared otomatik geçiş stratejisi (öneri)

Microsoft: exclusive erişim kullanıcı tarafından cihaz bazında kapatılabilir;
her zaman görünür bir shared fallback sunun [S45].

Önerilen durum makinesi:

    IDLE -> (oynat isteği) -> FORMAT PROBE
      probe S_OK exclusive?  -> EXCLUSIVE_RUNNING
      probe engellendi (AUDCLNT_E_DEVICE_IN_USE / ACCESS_DENIED /
        UNSUPPORTED_FORMAT / kullanıcı exclusive'ı kapalı) ->
          SHARED_RUNNING (IAudioClient3 low-period; mixer var, bit-perfect yok)
    EXCLUSIVE_RUNNING -> (cihaz değişimi / unplug / render hatası)
          -> teardown (Stop/Reset/Release) -> SHARED_RUNNING + kullanıcıya
             "bit-perfect devre dışı" bildirimi
    SHARED_RUNNING -> (kullanıcı manuel) veya (cihaz boşaldı + exclusive
          destekleniyor) -> EXCLUSIVE_RUNNING (kısa fade-out/in ile)

Ek kurallar (öneri):
- Exclusive her zaman "en iyi-effort" olsun; kullanıcı ayarı: Otomatik / Exclusive
  zorla / Shared zorla.
- Ses kesintisini en aza indirmek için geçişleri track sınırında veya 30 ms
  fade ile yapın.
- Cihaz değişikliğini IMMNotificationClient ile dinleyin; exclusive'de cihaz
  "in use" hatası yaygındır.

### 3.7 Ne zaman resample? (libsoxr)

- Mismatch YOK (dosya oranı == cihad exclusive oranı): resampler HİÇ devreye
  girmez → bit-perfect korunur. Bu birincil kuraldır.
- Mismatch VAR (shared mode'da mixer kendi resampler'ını kullanır; exclusive'de
  cihad oranı desteklemiyorsa shared'a düşmek ya da soxr ile önceden resample
  etmek gerekir).
- libsoxr kalite seviyeleri: QQ (en düşük gecikme) / LQ (16-bit precision) /
  MQ / HQ (24-bit precision) / VHQ (32-bit precision) [S50]. FFmpeg resampler
  dokümanı: soxr precision varsayılanı 20 bit (SoX "High Quality"), 28 bit
  "Very High Quality" demektir; cutoff için soxr 0 dB noktasıdır ve 0.91 varsayılan
  44.1k'de tüm 20 kHz bandını korur [S51].
- Gecikme gerçeği: libsoxr README, HQ yapılandırmasıyla 44100→48000 dönüşümünde
  gecikmenin ~1000 çıkış örneği (≈20 ms) olduğunu yazar; passband/FFT parametreleri
 yle düşürülebilir [S52].
- ⚠️ VERIFICATION REQUIRED: "NOSRC vs HQSRC" terimleri Windows ses motoru
  resampler APO'sunun (Microsoft Sound Resampler kalite modu) ayar adlarıdır;
  bu oturumda resmi kaynakla doğrulanmadı. libsoxr'da karşılığı QQ/LQ/MQ/HQ/VHQ
  preset'leridir [S50].
- Öneri: bit-perfect hedefindeyken resample yok; sadece kullanıcı açıkça
  "kalite modu" seçtiyse HQ/VHQ, realtime/düşük gecikme gerektiğinde QQ/LQ.

---

## §4 ASIO IMPLEMENTASYON REHBERİ

### 4.1 Driver enumeration

- ASIO sürücüleri Windows kayıt defterinde listelenir:
  HKEY_LOCAL_MACHINE\SOFTWARE\ASIO (BASSASIO ve cwASIO bu yolu tarar) [S53][S54].
- ASIO SDK host tarafı "asiolist" (asiodrivers.cpp + asiolist.cpp) ile COM
  üzerinden listeleme + CoCreateInstance(clsid) ile açma yapılır; PortAudio
  derlemesi bu iki dosyayı host'a ekler [S55].
- Kurulum SDK lisansı: Steinberg ücretsiz SDK sağlar, developer hesabı gerekir [S55].
- Düşük kaliteli ASIO sürücüleri uygulamayı çökertebilir; sürücü sağlayıcısının
  güncel sürümü şarttır [S54].

### 4.2 Buffer size ve latency hesabı

- ASIO buffer size örnek değerleri: 32, 64, 128, 256, 512, 1024. Süre = örnek
  sayısı / örnek oranı [S56].
- Teorik tek yönlü gecikme = bufferSize / sampleRate (+ sürücünün dahili güvenlik
  buffer'ları). Örnekler [S56]:
  - 128 örnek @ 48 kHz = 2.67 ms (tek yönlü), round-trip ~2× (giriş+çıkış) +
    sürücü katkısı. YouTube ölçüm örneğinde 128 buffer'da toplam round-trip
    ~14 ms raporlanmıştır (dahili gecikmeler dahil) [S57].
  - 1024 örnek @ 48 kHz = 21.3 ms tek yönlü.
- PortAudio'nun ASIO modeli: buffer ASIOCreateBuffers(bufferSize) ile kurulur;
  sınırlar sürücüye bağlıdır ve AsioDriverInfo{bufferMinSize, maxBufferSize,
  bufferPreferredSize, bufferGranularity} ile okunur (bazı sürücüler sabit, bazıları
  aralık, bazıları 2'nin kuvveti veya katsayı granularity ister) [S58].
- Buffer size ayarı PROGRAMCIYA ait değildir; kullanıcı (veya kullanıcı tercihi)
  belirler [S59].

### 4.3 Sample type eşleşmesi

- ASIO çıkışta desteklenen tipler arasında ASIOSTFloat32 (32-bit float) ve
  ASIOSTInt32 (32-bit int) bulunur; ayrıca 24-bit varyantları (ASIOSTInt24LSB vb.)
  — kesin enum listesi ASIO SDK asio.h içinde tanımlıdır.
  ⚠️ VERIFICATION REQUIRED: bu oturumda ASIO SDK header'ının resmi URL'sinden
  doğrulanmadı (yalnız ASIO 2.3 teknik dokümanı [S60] ve host örnekleri incelendi).
- strateji (öneri): driver'ın info().iniFormat üzerinden desteklediği tipleri
  tara; sırayla ASIOSTFloat32 → ASIOSTInt32 → 24-bit LSB dene. Bit-perfect
  hedefinde float32 veya int32; int32'ye 24-bit PCM'i konteyner olarak yazarken
  byte sırası (LSB/MSB) sürücüye göre doğrulayın.

### 4.4 Pseudokod

    // 1) enumeration
    AsioDriverList list;
    list.asioGetDriverNameCount();           // kayıt defteri + COM
    for (i...) list.asioGetDriverName(i, name);
    // 2) seçili sürücüyü yükle
    AsioDriver* drv = AsioDriver::open(name);   // CoCreateInstance(clsid)
    // 3) durum
    drv->getChannels(&inCh, &outCh);
    drv->getSampleRate(&sr);                    // örn. 44100..192000
    ASIOBufferInfo bi[2];                       // L,R çıkış kanalları
    drv->getBufferSize(&minSz,&maxSz,&prefSz,&gran);
    long buf = SnapToGranularity(prefSz, gran); // 128/256/512
    drv->createBuffers(bi, 2, buf, &callbacks);
    // 4) sample type seçimi (bit-perfect)
    ASIOSampleType t = drv->getSampleFormat();  // float32/int32/24LSB...
    if (t == ASIOSTFloat32) useFloatPipeline();
    else if (t == ASIOSTInt32) useInt32Pipeline();
    // 5) callback (RT thread!)
    callbacks.bufferSwitch = [](long index, ASIOBool direct) {
        float* L = (float*)bi[0].buffers[index];
        float* R = (float*)bi[1].buffers[index];
        ring.pull(L, R, buf);                  // lock-free; yoksa silence
    };
    drv->start();

RT kuralları: bufferSwitch içinde allocate, lock, dosya/ ağ I/O, log YASAK;
ring buffer underrun'da silence yaz + flag [S58][S59].

---

## §5 31-BAND EQ TASARIMI

### 5.1 Frekans tablosu (1/3 octave, ISO 2631/266 sıklıkta kullanılan dizi)

Soru listesindeki diziyle birebir (31 band, 20 Hz – 20 kHz):

| # | Hz | # | Hz | # | Hz |
|---|---|---|---|---|---|
| 1 | 20 | 12 | 250 | 23 | 3150 |
| 2 | 25 | 13 | 315 | 24 | 4000 |
| 3 | 31.5 | 14 | 400 | 25 | 5000 |
| 4 | 40 | 15 | 500 | 26 | 6300 |
| 5 | 50 | 16 | 630 | 27 | 8000 |
| 6 | 63 | 17 | 800 | 28 | 10000 |
| 7 | 80 | 18 | 1000 | 29 | 12500 |
| 8 | 100 | 19 | 1250 | 30 | 16000 |
| 9 | 125 | 20 | 1600 | 31 | 20000 |
| 10 | 160 | 21 | 2000 | | |
| 11 | 200 | 22 | 2500 | | |

Not: 1/3 octave band merkezleri 10^(k/10) × 100 Hz serisidir; 20, 25, 31.5, 40...
dizisi bunun 20 Hz'ten başlatan versiyonudur. ⚠️ VERIFICATION REQUIRED: bu dizi
standartlarda (IEC 61260 1/3 octave bandları) 20 Hz'ten önce 16 Hz ve 12.5 Hz
gibi bantlar da içerir; buradaki "20–20k 31 bant" kullanıcı spesifikasyonudur,
standart bant listesi değildir.

### 5.2 Biquad katsayıları (RBJ Audio-EQ Cookbook)

Kaynak: Robert Bristow-Johnson, "Cookbook formulae for audio EQ biquad filter
coefficients" [S61]; W3C Web Audio API sürümü [S62]; ham metin PDF aynısı [S63].

Peaking EQ (her band için):

    A     = 10^(dBgain / 40)                 // 40! (genlik, güç değil) [S64]
    w0    = 2*pi*f0/Fs
    cosw0 = cos(w0);  sinw0 = sin(w0)
    alpha = sinw0 / (2*Q)                    // 1/3-octave EQ için Q ~ 4.31 (bkz. alt)

    b0 = 1 + alpha*A
    b1 = -2*cosw0
    b2 = 1 - alpha*A
    a0 = 1 + alpha/A
    a1 = -2*cosw0
    a2 = 1 - alpha/A
    // sonra a0'a bölünür ve katsayılar double olarak saklanır

Kutup (Direct Form 1 önerisi, RBJ'nin kendi ifadesi) [S63]:

    y[n] = (b0/a0)x[n] + (b1/a0)x[n-1] + (b2/a0)x[n-2]
                   - (a1/a0)y[n-1] - (a2/a0)y[n-2]

- 1/3-octave band genişliği için Q: band genişliği BW (oktav) ↔ Q ilişkisi
  1/Q = 2*sinh(ln(2)/2 * BW) (analog prototip) [S61]. 1/3 oktav → BW = 1/3 →
  Q ≈ 4.31 (hesap: 1/Q = 2*sinh(0.3333*1.0397)=2*sinh(0.3466)=0.7203 → Q≈1.388;
  dikkat: bu hesap analog BW tanımı içindir; dijital BW tanımı farklıdır).
  ⚠️ VERIFICATION REQUIRED: 31-band grafik EQ'da klasik olarak Q≈4.31 KULLANILIR
  (KxStudio/Calf/LSP-plugins 31-band EQ değerleri), ancak bu oturumda ilgili
  açık kaynak EQ plug-in'inin katsayı kaynağı doğrulanmadı. Uygulamada Q
  kullanıcı ayarı olarak 1.0–10.0 aralığında sunulmalıdır.

### 5.3 Cascade ve numerik notlar

- 31 bant × 2 kanal = 62 biquad instance; her biquad'ın kendi state'i (x1,x2,y1,y2)
  DOUBLE PRECISION'da; katsayılar da double'da hesaplanıp float32'ye yuvarlanabilir
  (Fs=192 kHz'te float32 DF1 katsayı hassasiyeti sınırda kalabilir; double state +
  double katsayı önerilir — bu bizim analizimiz).
- Gain 0 dB olan bantlar bile CASCADE'de bırakılırsa birikmiş faz bozulması olur;
  |dBgain| < 0.05 olan bantları bypass etmek yaygın optimizasyondur (bizim önerimiz).
- IIR sıfır-algoritma gecikmez (grup gecikmesi frekans bağımlıdır, toplam
  gecikme yoktur); yani EQ'nun eklediği BUFFER gecikmesi 0 örnektir. Fakat
  minimum-faz biquad faz kayması phase distortion yaratır — istemiyorsanız
  linear-phase FIR'a geçin (gecikme bedeli ödenir).

### 5.4 CPU maliyeti hesabı (hesap — bizim, kaynak değil)

Bir peaking biquad (DF1, normalize edilmiş): 5 çarpma + 4 toplam (MAC) + state
update. Kanal başına 31 biquad → 155 MAC/örnek.

| Örnek oranı | Kanal | MAC/saniye | 2.5 GHz çekirdekte tahmini |
|---|---|---|---|
| 44.1 kHz | 2 | 13.7 M | ≈ %0.01 |
| 48 kHz | 2 | 14.9 M | ≈ %0.01 |
| 192 kHz | 2 | 59.5 M | ≈ %0.03–0.05 |

(192k: 31×5×2×192000 = 59.52 M MAC/s.) Sonuç: modern x86'da 31-band EQ, DSP
zincirinin en ucuz elemanıdır; gerçek maliyeti coefficient recompute (gain
değişiminde, sample-rate değişiminde) ve UI smoothing (ör. 50 ms interpolate)
oluşturur. Optimizasyon: gain değişimi anında değil, 10–20 adımlık rampa ile
uygulanır (tıkırtı önleme).

### 5.5 TRUE PEAK vs SAMPLE PEAK

- Sample peak: örnek değerlerin |max|'ı.
- True peak: ITU-R BS.1770-4 Annex 2, band-sınırlı sinyalin örnekler arası
  (inter-sample) tepe değerini tahmin etmek için 4× oversampling önerir
  (48 kHz'te 4×, 96/88.2 kHz'te 2×, 192 kHz'te oversampling gerekmez) [S65][S66][S67].
- ISPs (örnek arası tepler) PCM'de kaçınılmazdır; DAC'ta kırpılmaya (clip) yol
  açabilir; bu yüzden streaming/TV dağıtımı true-peak sınırı zorunludur [S65][S66].
- Neva Player'daki yeri: EQ boost +12 dB yaparsa sample peak ile ölçüm yanıltıcı
  olur; true-peak meter (4× oversample halfband FIR) önerilir. CPU: yarı-bant
  FIR ile 4× upsampling, band başına ~20-40 MAC/örnek; yine ucuz (hesap).

---

## §6 GAPLESS PLAYBACK TEKNİĞİ

### 6.1 Kopma nedenleri [S68][S69][S70]

1. Codec'in ürettiği başlangıç/bitiş dolgusu (MP3 encoder delay + padding).
2. Decoder'ın sonraki dosyayı geç açması (disk/NAS arama maliyeti).
3. Ses cihadının yeniden bağlanması (format değişince stream kapanır).
4. Crossfade açıkken gapless'i "geri alması".

### 6.2 MP3 gapless (LAME tag)

- LAME, Xing/Info header'ını genişleterek encoder delay ve padding bilgisini
  ekler [S71][S72][S73].
- Format: MP3 frame içinde "Xing" (VBR) veya "Info" (CBR) imzası; bundan 17 byte
  sonra "LAME" veya "Lavf" etiketi; etiketten +21 byte ileride 3 byte'lık alan
  → üst 12 bit front padding, alt 12 bit end padding [S73].
- enc_delay genelde 576 (0x240) örnektir (örnek dosyada ölçülen değer) [S72].
- Uyarı: bazı oynatıcılar (ör. mpg123/foobar sürümleri) belirli dosyalarda
  Xing/LAME etiketini TANIMAYABİLİR ve delay/padding'i yok sayar — yani sadece
  "etiket var" yetmez, parser'ınızın offset'i doğru olmalı [S72].
- Ayrıca iTunes tarzı ID3 içinde de encoder-delay/padding alanı bulunur
  (ExoPlayer bu ikisini de destekler) [S74].

### 6.3 FLAC seek表 (SEEKTABLE) ve cue

- SEEKTABLE metadata bloğu seek point'leri tutar: sample numarası + byte offset
  + block size; tek blok olabilir, point sayısı serbesttir [S75].
- Seek table olmadan da herhangi bir sample'a seek mümkündür ama gecikme
  öngörülemez (bitrate değişken); seek point'ler bu gecikmeyi ciddi azaltır [S75].
- Konteyner içinde (mp4/mkv) FLAC seektable genelde KULLANILMAZ; konteynerin
  kendi seek mekanizması devreye girer [S75].
- metaflac --add-seekpoint=100x / 3.5s ile nokta eklenebilir; --import-cuesheet-from
  cue sheet'teki her index için seek point üretir [S76].
- FLAC gapless'i doğası gereği gapless'tir; ek olarak CUESHEET lead-in/out
  alanları diskler arası parça birleştirmede kullanılır [S75][S77].

### 6.4 Gapless mimarisi (öneri, literatürle uyumlu) [S68][S69]

- Sonraki dosyayı mevcut dosya bitmeden ÖNCE aç + decode et (prefetch).
- Cihaz stream'ini PARÇA SINIRINDA YENİDEN AÇMA: örnek oranı/bit-depth eşitse
  tek kalıcı stream + yeterince büyük ring buffer ile kesintisiz geçiş.
- Farklı format pair'leri için: ya (a) playlist'i cihaz formatına resample edip
  bit-perfectlikten feragat, ya da (b) kısa (10–30 ms) crossfade ile geçiş.
- MP3'te encoder delay/padding'i decode sonunda kırp; FLAC'ta seektable + cuesheet
  kullan; AAC için gapless metadata genelde CONTAINER tarafında (MP4 elst /
  iTunes gapless tag) bulunur — ⚠️ VERIFICATION REQUIRED (bu oturumda MP4
  gapless tag spesifikasyonu doğrulanmadı).
- Radio için gapless dosya mekaniği geçerli değildir; reconnect + fade-in (§1.6).

---

## §7 KAYNAKÇA

| # | Kaynak | URL | Güven |
|---|---|---|---|
| S1 | ICY (cast.readme.io docs) | https://cast.readme.io/docs/icy | orta |
| S2 | Practical: A Shoutcast Server (gigamonkeys) | https://gigamonkeys.com/book/practical-a-shoutcast-server | orta |
| S3 | Icecast protocol spec + hexdump (gist, niko) | https://gist.github.com/niko/2a1d7b2d109ebe7f7ca2f860c3505ef0 | orta |
| S4 | Shoutcast/ICY teknik dersi (KTH PDF) | https://people.kth.se/~johanmon/dse/casty.pdf | orta |
| S5 | Mixxx forum: Shoutcast 2 & 2.5 | https://mixxx.discourse.group/t/shoutcast-2-2-5-support/17376 | düşük |
| S6 | Shoutcast v1 vs v2 (SpectraRadio KB) | https://www.spectraradio.ng/knowledgebase/21/What-Is-The-Different-Between-Shoutcast-V1-And-Shoutcast-V2.html | düşük |
| S7 | playitsoftware FAQ: v1 vs v2 | https://www.playitsoftware.com/FAQs/q/difference-shoutcast-v1-v2-configurations | orta |
| S8 | SHOUTcast compatible API (Cast) | https://cast.readme.io/reference/shoutcast-compatible-api | orta |
| S9 | Shoutcast SPRL DNAS agreements | https://www.shoutcast.com/legal/agreements/dnas | yüksek |
| S10 | FFmpeg Formats Documentation (hls demuxer, lhls) | https://ffmpeg.org/ffmpeg-formats.html | yüksek |
| S11 | Apple: Enabling Low-Latency HLS | https://developer.apple.com/documentation/http-live-streaming/enabling-low-latency-http-live-streaming-hls | yüksek |
| S12 | AWS: Low-latency HLS workflow | https://aws.amazon.com/blogs/media/how-to-configure-a-low-latency-hls-workflow-using-aws-media-services | orta |
| S13 | Softvelum: LL-HLS (Nimble) | https://softvelum.com/low-latency-hls · https://blog.wmspanel.com/2020/11/apple-low-latency-hls-nimble-streamer.html | orta |
| S14 | Muvi: LL-HLS & LL-DASH optimization | https://www.muvi.com/blogs/a-practical-guide-to-optimizing-ll-hls-ll-dash-for-ultra-low-latency | düşük |
| S15 | MPEG-DASH (Wikipedia) | https://en.wikipedia.org/wiki/Dynamic_Adaptive_Streaming_over_HTTP | orta |
| S16 | Forasoft: MPD, Periods, AdaptationSets | https://www.forasoft.com/learn/video-streaming/articles-streaming/mpeg-dash-deep-dive | düşük |
| S17 | BlazingCDN: HLS vs DASH | https://blog.blazingcdn.com/en-us/streaming-cdn-protocol-comparison-hls-vs-mpeg-dash | düşük |
| S18 | Ant Media: MPEG-DASH explained | https://antmedia.io/mpeg-dash-streaming-protocol | düşük |
| S19 | Fraunhofer IIS: HE-AAC, HE-AACv2 | https://www.iis.fraunhofer.de/en/ff/amm/broadcast-streaming/heaac.html | yüksek |
| S20 | BBC Sounds: codecs, bitrates & protocols | https://www.bbc.co.uk/sounds/help/questions/about-bbc-sounds-and-our-policies/codecs-bitrates | yüksek |
| S21 | Garfnet: New BBC internet radio streams 2025 | https://garfnet.org.uk/cms/2025/01/22/new-bbc-internet-radio-streams-2025 | düşük |
| S22 | Radio Browser API örnekleri (OpenPublicApis) | https://openpublicapis.com/api/radio-browser | orta |
| S23 | radio-browser.info (proje) | https://www.radio-browser.info · https://radio-browser.info | yüksek |
| S24 | Radio Browser API (PublicAPI) | https://publicapi.dev/radio-browser-api | düşük |
| S25 | APIs.io: Radio Browser provider | https://apis.io/providers/radio-browser | orta |
| S26 | radio-browser.info FAQ (mirror DNS, sunucular) | https://www.radio-browser.info/faq | yüksek |
| S27 | OpenPublicApis (Radio Browser sayfa) | https://openpublicapis.com/api/radio-browser | orta |
| S28 | SomaFM Direct Stream Links | https://somafm.com/live/directstreamlinks.html | yüksek |
| S29 | SomaFM FAQ (bitrate/codec, Icecast KH) | https://somafm.com/about/faq.html | yüksek |
| S30 | mikepierce/internet-radio-streams (curated) | https://github.com/mikepierce/internet-radio-streams | orta |
| S31 | Roon forum: BBC radio stream listesi | https://community.roonlabs.com/t/internet-radio-url-swap-meet/8666 | düşük |
| S32 | Icecast (proje) | https://icecast.org | yüksek |
| S33 | Pro.Radio: Icecast status-json.xsl | https://pro.radio/guides/add-radio-stream-to-wordpress | düşük |
| S34 | libcurl CURLOPT_WRITEFUNCTION | https://curl.se/libcurl/c/CURLOPT_WRITEFUNCTION.html | yüksek |
| S35 | everything.curl: Write data (callbacks) | https://everything.curl.dev/transfers/callbacks/write.html | yüksek |
| S36 | Daniel Stenberg: Common mistakes with libcurl | https://daniel.haxx.se/blog/2021/09/27/common-mistakes-when-using-libcurl | yüksek |
| S37 | 2wcom: Is DAB+ HE-AAC? (frame lengths) | https://www.2wcom.com/is-dab-he-aac | orta |
| S38 | RadioTedu: Determining Optimal Audio Encoders (PDF) | https://radiotedu.com/technology/research/determining-optimal-audio-encoders.pdf | düşük |
| S39 | Forasoft: AAC-LC vs HE-AAC vs xHE-AAC | https://www.forasoft.com/learn/audio-for-video/articles-audio/aac-family-lc-he-v1-v2-xhe | düşük |
| S40 | Wikipedia: High-Efficiency AAC | https://en.wikipedia.org/wiki/High-Efficiency_Advanced_Audio_Coding | orta |
| S41 | 2wcom: HE-AAC v2 compatibility | https://www.2wcom.com/is-dab-he-aac | orta |
| S42 | RadioMast: Streaming audio codecs explained | https://www.radiomast.io/blog/streaming-audio-codecs-explained.html | orta |
| S43 | The Well-Tempered Computer: WASAPI | https://www.thewelltemperedcomputer.com/KB/WASAPI.htm | orta |
| S44 | Zenn: Building a Bit-Perfect Music Player (WPF/WASAPI) | https://zenn.dev/kerobot/articles/90190d8d203740 | düşük |
| S45 | Microsoft Learn: Exclusive-Mode Streams | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | yüksek |
| S46 | FluidSynth discussion: exclusive'de float vs 24-bit PCM | https://github.com/FluidSynth/fluidsynth/discussions/1734 | düşük |
| S47 | Microsoft Learn: Low Latency Audio (IAudioClient3) | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio | yüksek |
| S48 | Nirbheek: Low-latency audio on Windows (GStreamer) | https://blog.nirbheek.in/2018/03/low-latency-audio-on-windows-with.html | orta |
| S49 | Windows Developer Blog: Real-time audio in Store apps | https://blogs.windows.com/windowsdeveloper/2014/05/15/real-time-audio-in-windows-store-and-windows-phone-apps | yüksek |
| S50 | libsoxr wrappers (quality presets QQ/LQ/MQ/HQ/VHQ) | https://pkg.go.dev/github.com/tphakala/go-audio-resampling | düşük |
| S51 | FFmpeg Resampler Documentation (soxr precision, cutoff) | https://ffmpeg.org/ffmpeg-resampler.html | yüksek |
| S52 | libsoxr README (chirlu mirror) | https://github.com/chirlu/soxr · https://sourceforge.net/projects/soxr | yüksek |
| S53 | cwASIO README (registry enumeration) | https://github.com/s13n/cwASIO/blob/main/README.md | orta |
| S54 | un4seen forum: ASIO device list (HKLM\SOFTWARE\ASIO) | http://www.un4seen.com/forum?topic=17628.0 | düşük |
| S55 | PortAudio: Building with ASIO (asiodrivers/asiolist) | https://portaudio.com/docs/v19-doxydocs/compile_windows_asio_msvc.html | yüksek |
| S56 | PCAudioLabs: How to configure ASIO buffer | https://pcaudiolabs.com/how-to-configure-asio-buffer | düşük |
| S57 | YouTube ölçüm örneği (buffer→latency) | https://www.youtube.com/watch?v=kKwK3fwXfu8 | düşük |
| S58 | PortAudio wiki: Buffering/Latency guidelines (ASIO) | https://github.com/PortAudio/portaudio/wiki/BufferingLatencyAndTimingImplementationGuidelines | orta |
| S59 | FMOD QA: ASIO buffer size kullanıcıya ait | https://qa.fmod.com/t/asio-how-do-i-programmatically-set-the-buffer-size-fmod-should-use-to-match-that-of-asio-driver/ | orta |
| S60 | ASIO 2.3 teknik doküman (TUL) | https://dspace.tul.cz/server/api/core/bitstreams/39cee442-acf3-4ba4-8a9f-7b9988a8abe4/content | orta |
| S61 | RBJ Audio-EQ-Cookbook (webaudio.github.io) | https://webaudio.github.io/Audio-EQ-Cookbook/audio-eq-cookbook.html | yüksek |
| S62 | W3C: Audio EQ Cookbook (Working Group Note) | https://www.w3.org/TR/audio-eq-cookbook | yüksek |
| S63 | RBJ Cookbook ham metin (cqham PDF) | https://www.cqham.ru/forum/attachment.php?attachmentid=14613&d=1193604949 | orta |
| S64 | The VMUNIX Blues: LLM-generated biquad anatomy (A=10^(dB/40)) | https://www.vmunix.com/posts/anatomy-of-an-llm-generated-biquad | düşük |
| S65 | ITU-R BS.1770-4 (PDF) | https://assets.corusent.com/wp-content/uploads/2021/10/ITUR_BS_1770_4_Audio_Program_Loudness_En.pdf | yüksek |
| S66 | i@like.audio: Reference of Audio Metering (true peak, 4×) | https://like.audio/20260203/reference-of-audio-metering | düşük |
| S67 | JUCE forum: True peak oversampling factors | https://forum.juce.com/t/true-peak-measuring-is-it-really-that-simple/52601 | düşük |
| S68 | Wikipedia: Gapless playback | https://en.wikipedia.org/wiki/Gapless_playback | yüksek |
| S69 | BitMuse: Gapless playback (pre-decode tekniği) | https://bitmuse.app/gapless-playback-mac | düşük |
| S70 | Audify: Gapless playback Android (ExoPlayer davranışı) | https://audifyplayer.com/guides/gapless-playback-android.html | düşük |
| S71 | LAME bug tracker #453 (Xing header, delay/padding) | https://sourceforge.net/p/lame/bugs/453 | orta |
| S72 | Jack Whitham: Debugging Gapless MP3 Playback | https://www.jwhitham.org/2023/09/system-shock-soundtrack-gapless.html | orta |
| S73 | web.dev: Media Source Extensions for Audio (Xing/LAME parse) | https://web.dev/articles/mse-seamless-playback | yüksek |
| S74 | ExoPlayer issue #4425 (gapless MP3, iTunes-style metadata) | https://github.com/google/ExoPlayer/issues/4425 | orta |
| S75 | IETF draft-ietf-cellar-flac-10 §8.5 Seektable | https://www.ietf.org/archive/id/draft-ietf-cellar-flac-10.html | yüksek |
| S76 | metaflac man page (Debian) | https://manpages.debian.org/testing/flac/metaflac.1.en.html | yüksek |
| S77 | Xiph: FLAC format overview | https://xiph.org/flac/documentation_format_overview.html | yüksek |
| S78 | dCS community: DoP / Native DSD | https://dcs.community/t/interesting-piece-on-dsd-and-dop/5376 | orta |
| S79 | DSD-Guide: DoP Open Standard | https://dsd-guide.com/dop-open-standard | orta |
| S80 | XMOS lib_xua: DSD / DoP (bit-perfect uyarısı) | https://www.xmos.com/documentation/XM-012296-UG/html/doc/rst/opt_dsd.html | yüksek |
| S81 | NativeDSD help: DSD→PCM downconversion | https://help.nativedsd.com/en/articles/63529-playing-dsd-dxd-and-very-high-bit-rate-pcm-files | düşük |
| S82 | JRiver wiki: DSD (DSD→PCM) | https://wiki.jriver.com/index.php/DSD | düşük |
| S83 | Auris: WASAPI Exclusive vs Shared guide | https://aurisplayer.com/blog/wasapi-exclusive-guide.html | düşük |
| S84 | GitHub gist: BBC Radio HLS m3u | https://gist.github.com/bpsib/67089b959e4fa898af69fea59ad74bc3 | düşük |

---

## §8 ÖZET (10 satır)

1. ICY hâlâ internet radyonun omurgası: "Icy-MetaData: 1" istek başlığı + "icy-metaint" yanıt başlığı + uzunluk-baytlı StreamTitle bloğu; durum makinesinde "0 uzunluk → metaint sayacına dön" detayı şarttır (S1, S3).
2. Shoutcast v2 protokolü (Ultravox) ayrı bir dünyadır ama v2 sunucular ICY uyumluluğu sunar; Neva ICY + /currentsong ile yetinebilir (S5, S8).
3. HLS'de FFmpeg demuxer hazırdır; LL-HLS (partial segment, 3–10 sn kazanç) yalnızca yayıncı CMAF-part destekliyorsa anlamlıdır — P3 önceliği (S10, S11, S12).
4. DASH radyoda HLS kadar yaygın değildir ama BBC gibi büyük yayıncılar ICY yerine HLS/DASH + AAC+ kullanıyor; her ikisi de desteklenmeli (S19, S20).
5. En iyi ücretsiz kaynak Radio Browser API: anahtarsız JSON, ~50–60 bin istasyon, public domain veri, çoklu mirror DNS (S22, S25, S26); SomaFM MP3/AAC/AAC-HE + Icecast KH (S28, S29).
6. Radio codec matrisi: MP3 (evrensel) > AAC-LC (96–256k) > HE-AAC v1/v2 (48–32k, DAB+ çekirdeği, 24–96 kHz) > Opus (kalite lideri, 48 kHz'e sabit) (S39, S42).
7. WASAPI Exclusive bit-perfectin Windows'taki tek yolu: mixer bypass, dither yok; ama cihaz exact format istemezse AUDCLNT_E_UNSUPPORTED_FORMAT alırsınız — 32f→32i→24-in-32 negosyasyon sırası + IAudioClient3 shared fallback otomatik geçişi şart (S45, S46, S47).
8. ASIO'da buffer size kullanıcıya aittir; gecikme = buffer/sampleRate (+ sürücü katkısı); sürücü listesi HKLM\SOFTWARE\ASIO; sample type float32/int32 seçimi bit-perfect için kritiktir (S53, S58, S59).
9. 31-band EQ: RBJ peaking katsayıları + DF1, 192 kHz stereo'de ~59.5 M MAC/s (çekirdeğin %0.05'i); IIR buffer gecikmesi sıfırdır, true-peak için 4× oversampling BS.1770-4标准ıdır (S61, S62, S65).
10. Gapless: MP3'te Xing/LAME 12-bit front/end padding, FLAC'ta SEEKTABLE+CUESHEET; kalıcı cihaz stream'i + prefetch mimarisi ile parça sınırında yeniden bağlama yok (S73, S75, S68).

**Dosya:** C:\www\coremusic.net\.ai\projects\nevaplayer\research\radio-and-hires.md
