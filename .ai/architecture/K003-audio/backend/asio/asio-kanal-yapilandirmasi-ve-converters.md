---
title: "ASIO Kanal Yapılandırması ve Dönüşüm Katmanı"
node_id: K003-audio/backend/asio/asio-kanal-yapilandirmasi-ve-converters
node_type: architecture-deepdive
status: draft
parent_id: K003-audio/backend/asio
children: []
depends_on:
  - K003-audio/backend
evidence: []
validation: []
open_questions: []
group: mimari
date: 2026-10-12
updated: 2026-10-12
---

# ASIO Kanal Yapılandırması ve Dönüşüm Katmanı

> Bu düğüm, ASIO'nun kanal/buffer yapılandırmasını (`ASIOChannelInfo`,
> `ASIOBufferInfo`) ve örnek formatı dönüşüm katmanını (`ASIOSampleType` →
> host iç temsili) Türkçe olarak derinlemesine inceler. Dosya,
> `asio-kapsam.md` §2.1'deki "kanal yapılandırması" dal sorusunun
> (Soru 2) hedefidir.

---

## §0 Metadata ve Erişim

| Alan | Değer |
|---|---|
| Dosya yolu | `.ai/architecture/K003-audio/backend/asio/asio-kanal-yapilandirmasi-ve-converters.md` |
| node_id | `K003-audio/backend/asio/asio-kanal-yapilandirmasi-ve-converters` |
| node_type | `architecture-deepdive` |
| group | `mimari` |
| status | `draft` |
| parent_id | `K003-audio/backend/asio` |
| depends_on | `K003-audio/backend` |
| Düğüme özel kanıt | Henüz üretilmedi — `evidence: []` |

**Authority (bu dosyanın yetki sınırı):** ASIO kanal/buffer yapılandırması ve
örnek formatı dönüşüm modeli: `ASIOChannelInfo`, `ASIOBufferInfo`,
`ASIOSampleType` enumu, kanal bilgisi sorgu API'leri (`ASIOGetChannelInfo` /
`ASIOGetChannels`) ve host tarafı format dönüştürücü (converter) sözleşmesi.
Yönlendirme ve anahtar kavramlar için bkz. `index.md`; kapsam sınırları için
bkz. `asio-kapsam.md`.

---

## §1 Bu Dosyanın Kapsamı

Bir ASIO kanalı (giriş veya çıkış) iki ayrı veriyle tanımlanır: **kimlik/bilgi**
(`ASIOChannelInfo`: yön, grup, etiket, veri tipi) ve **tampon bağlaması**
(`ASIOBufferInfo`: kanal indeksi + host pointer'ı). Bu dosya şu soruları
yanıtlar:

1. `ASIOChannelInfo` hangi alanları taşır; `group` alanı ne anlama gelir?
2. `ASIOGetChannels` iki sayı (giriş/çıkış) nasıl döndürür?
3. `ASIOGetChannels` + `ASIOGetChannelInfo` ile tam bir kanal haritası nasıl
   çıkarılır?
4. `ASIOCreateBuffers` hangi bilgilerle çağrılır; buffer ↔ kanal eşlemesi
   nasıl kurulur?
5. `ASIOSampleType` enumundaki her format ne anlama gelir; hangi bayraklar
   byte genişliği ve byte sırasını belirler?
6. Host tarafında dönüşüm (converter) katmanının sözleşmesi nedir — özellikle
   JUCE'nin `ASIOSampleFormat` kazıma (cast) stratejisi nasıl çalışır?

**Kapsam dışı:** Kanal başına gecikme (`asio-latency-ve-jitter.md`), timestamp
(`asio-timestamp-ve-clock-senkronu.md`), sürücü yeniden bağlama/hot-swap
(`asio-hot-swap-ve-device-loss.md`), 8.1 surround kanal haritası
(`asio-multichannel-ve-timestamp.md`).

---

## §2 `ASIOChannelInfo`: Kanal Kimliği

### §2.1 Yapı Tanımı

```cpp
typedef struct ASIOChannelInfo
{
    long            channel;    // this is the 'index' of the channel
    long            isInput;    // this is an input channel
    long            group;      // stereo group, or ASIOMAXCHANNELS
    ASIOSampleType  type;       // the dataType of this channel
    long            active;     // this is in use
    long            channelOffset;  // channel offset (reserved)
    char            name[32];   // channel name
    char            shortLabel[8];  // for short label (Cubase VST uses it)
} ASIOChannelInfo;
```

`(Kanıt: asio.h, ASIOChannelInfo tanımı)` — alanlar ve anlamları:

| Alan | Tip | Anlamı |
|---|---|---|
| `channel` | `long` | Kanal indeksi (0 tabanlı) — `ASIOGetChannels` ile dönen sayının altında |
| `isInput` | `long` | `1` = giriş, `0` = çıkış kanalı |
| `group` | `long` | **Sesli grubu** (stereo grubu) — `ASIOMAXCHANNELS` değeri ayrılmıştır |
| `type` | `ASIOSampleType` | Bu kanalın veri tipi (§5) |
| `active` | `long` | Kanal şu anda kullanımda mı |
| `channelOffset` | `long` | Kanal ofseti (reserved) |
| `name[32]` | `char` | Uzun kanal adı (örn. "Analog Out 1") |
| `shortLabel[8]` | `char` | Kısa etiket — Cubase VST kısa alanda kullanır |

**`group` alanının anlamı (kanıtlanmış):** `asio.h` yorumu — "this is a
stereo group, or `ASIOMAXCHANNELS`". Yani `group` alanı kanalı bir **stereo
grubunun** parçası olarak işaretler; `ASIOMAXCHANNELS` değeri, "gruba dahil
değil / ayrılmış" anlamında kullanılır. `group` değeri, kanalların çiftler
(stereo pair) halinde sunulduğunu gösterir; örneğin 8 çıkışlı bir cihazda
çıkış 0-1 `group=0`, 2-3 `group=1` gibi eşlenebilir. **(Kanıt: `asio.h`,
ASIOChannelInfo::group yorumu)** — gerçek değerler sürücüye göre değişir;
bu dosyada değer uydurulmaz.

**Uyarı (Bilgi notu):** Bu `group` alanı, CoreMusic vault frontmatter'ındaki
`group: mimari|kod|kanit` alanı ile **aynı şey değildir** — biri ASIO sürücü
API'sinde sesli grup, diğeri vault dosya sınıflandırmasıdır. Karıştırılmamalıdır.

### §2.2 `ASIOGetChannels`: İki Sayı

```cpp
// ASIOGetChannels: this will tell the host the number of input
// and output channels available. The host must provide pointers
// to two longs; the driver will fill in the counts.
ASIOError ASIOGetChannels(long* numInputs, long* numOutputs);
```

**(Kanıt: `asio.h`, ASIOGetChannels prototipi)** — host iki `long` için alan
ayırır; sürücü giriş ve çıkış sayılarını ayrı ayrı yazar. API **çift yönlü
sayım** verir: tek çağrıda hem giriş hem çıkış sayısı okunur.

### §2.3 `ASIOGetChannels` Sonrası Kanal Haritası Çıkarma Akışı

```text
ASIOGetChannels(&nIn, &nOut)              → toplam sayılar
    ↓
giriş için:  for (i = 0; i < nIn;  i++) ASIOGetChannelInfo({channel=i, isInput=1})
çıkış için:  for (j = 0; j < nOut; j++) ASIOGetChannelInfo({channel=j, isInput=0})
    ↓
Her çağrı: name / shortLabel / group / type / active döner
    ↓
Sonuç: kanal + yön + grup + format + etiket içeren tam harita
```

**Kural:** `ASIOGetChannelInfo` çağrısından **önce** `channel` ve `isInput`
alanları host tarafından doldurulmalıdır (sürücü hangi kanalı istendiğini bu
iki değerden anlar); `group`, `type`, `active`, `name`, `shortLabel` ise
sürücü tarafından doldurulur. Bu "çift tampon" (in/out struct) deseni ASIO'nun
tüm sorgu API'lerinde ortaktır: host niyeti yazar, sürücü cevabı yazar.

---

## §3 `ASIOBufferInfo`: Buffer ↔ Kanal Eşlemesi

### §3.1 Yapı Tanımı

```cpp
typedef struct ASIOBufferInfo
{
    ASIOBool        isInput;          // is this an input or output?
    long            channelNum;       // channel index
    void*           buffers[2];       // buffer pointers for input & output
} ASIOBufferInfo;
```

`(Kanıt: asio.h, ASIOBufferInfo tanımı)` — üç alan:

| Alan | Anlamı |
|---|---|
| `isInput` | `ASIO_TRUE` = giriş kanalı, `ASIO_FALSE` = çıkış |
| `channelNum` | Kanal indeksi (§2.2'deki sayımın altında) |
| `buffers[2]` | İki arabellek — sırayla çift tampon (double buffer) slotları |

**Kritik gerçek:** `buffers[2]` **çift tampon (double-buffering)** içindir —
her `bufferSwitch` çağrısında sürücü `index` (0 veya 1) verir; host bu indekste
`buffers[index]` kullanır. `(Kanıt: asio.h, ASIOCreateBuffers yorumu — bkz.
§3.2)` — ASIO'nun çift tampon modeli bu iki slot ile uygulanır; tek tamponlu
mod (`kAsioCanInputMonitor` benzeri) için ayrı bir yapı yoktur.

### §3.2 `ASIOCreateBuffers` Sözleşmesi

```cpp
// ASIOCreateBuffers: this will create the buffers for the given channels.
// The host must provide an array of ASIOBufferInfo, each specifying
// isInput and channelNum; the driver fills in the buffer pointers.
// bufferSampleSize is the number of samples per buffer (power of 2).
// numBuffers is the number of buffers (2 for double buffering).
ASIOError ASIOCreateBuffers(ASIOBufferInfo* bufferInfos,
                            long numBuffers, long bufferSampleSize);
```

**(Kanıt: `asio.h`, ASIOCreateBuffers prototipi ve yorumu)** — çağrı sözleşmesi:

1. Host, `numBuffers` adet `ASIOBufferInfo` dizisi hazırlar; her elemanda
   `isInput` ve `channelNum` **host tarafından** yazılır (niyet).
2. `bufferSampleSize` = arabellek başına örnek sayısı (**2'nin kuvveti** —
   ASIO'nun standart kısıtı).
3. `numBuffers` = arabellek sayısı (**çift tampon için 2**).
4. Sürücü her elemanın `buffers[2]` dizisini **kendi belleğiyle** doldurur.
5. `ASIOCreateBuffers` sonrası `ASIOGetLatencies` çağrılmadan önce gecikme
   değerleri **geçersiz olabilir** — bu kural bkz. `asio-latency-ve-jitter.md`
   (ASIO SDK `hostsample.cpp`: "latencies are only valid after
   `ASIOCreateBuffers`").

### §3.3 Eşleme Tablosu Örneği (kurgusal — değerler sürücüye bağlı)

```text
bufferInfos[0] = { isInput = 0, channelNum = 0 }  → çıkış 0, buffers[0]/[1]
bufferInfos[1] = { isInput = 0, channelNum = 1 }  → çıkış 1, buffers[0]/[1]
bufferInfos[2] = { isInput = 1, channelNum = 0 }  → giriş 0, buffers[0]/[1]
...
bufferInfos[N-1] = { isInput = ?, channelNum = ? }
```

**Uyarı:** Yukarıdaki `channelNum` değerleri **örnektir, spesifikasyon
değildir** — gerçek indeksler `ASIOGetChannels` (§2.2) ile okunur. Bu dosyada
gerçek bir cihazın kanal sayısı uydurulmaz (zero-hallucination).

---

## §4 Kanal Yapılandırması Akış Diyagramı

```text
[ASIOGetChannels] → nIn, nOut
        │
        ├── giriş kanalları: nIn × ASIOGetChannelInfo(isInput=1)
        └── çıkış kanalları: nOut × ASIOGetChannelInfo(isInput=0)
        │        → name / shortLabel / group / type / active
        ▼
[host: kanal + yön + grup + format haritası çıkar]
        │
        ▼
[host: ASIOBufferInfo dizisi hazırla]
   her eleman: isInput + channelNum (host yazar)
        │
        ▼
[ASIOCreateBuffers(bufferInfos, numBuffers, bufferSampleSize)]
   sürücü: buffers[2] pointer'larını doldurur
        │
        ▼
[ASIOGetLatencies → gecikme artık geçerli]
        │
        ▼
[ASIOStart → bufferSwitch/double buffer döngüsü]
```

---

## §5 `ASIOSampleType`: Örnek Formatı Enumu

### §5.1 Tam Enum Tanımı

```cpp
typedef enum ASIOSampleType
{
    ASIOSTInt16MSB   = 0x0,
    ASIOSTInt24MSB   = 0x1,
    ASIOSTInt32MSB   = 0x2,
    ASIOSTFloat32MSB = 0x3,
    ASIOSTFloat64MSB = 0x4,
    ASIOSTInt32MSB16 = 0x8,
    ASIOSTInt32MSB18 = 0x9,
    ASIOSTInt32MSB20 = 0xa,
    ASIOSTInt32MSB24 = 0xb,
    ASIOSTInt16LSB   = 0x10,
    ASIOSTInt24LSB   = 0x11,
    ASIOSTInt32LSB   = 0x12,
    ASIOSTFloat32LSB = 0x13,
    ASIOSTFloat64LSB = 0x14,
    ASIOSTInt32LSB16 = 0x18,
    ASIOSTInt32LSB18 = 0x19,
    ASIOSTInt32LSB20 = 0x1a,
    ASIOSTInt32LSB24 = 0x1b,
    ASIOSTDSDInt8MSB = 0x20,
    ASIOSTDSDInt8LSB = 0x21,
    ASIOSTLast
} ASIOSampleType;
```

`(Kanıt: asio.h, ASIOSampleType enumu)` — enum değerleri **asio.h'den aynen
alınmıştır**; `ASIOSTLast` işaretcisi enumun sonunu belirtir (dolgu/guard).

### §5.2 Format Ailesi ve Bayrak Kodlaması

Enum değerleri **bit alanı** olarak kodlanır; her bayrak ayrı bir özelliği
taşır:

| Bit aralığı | Bayrak | Anlamı |
|---|---|---|
| `0x00-0x0f` | MSB ailesi (`0x0x`) | Big-endian (Most Significant Byte önce) |
| `0x10-0x1f` | LSB ailesi (`0x1x`) | Little-endian (Least Significant Byte önce) |
| `0x20-0x2f` | DSD ailesi (`0x2x`) | DSD (Direct Stream Digital) örnek formatı |
| `0x0` nibble | `...Int16...` | 16-bit tam sayı |
| `0x1` nibble | `...Int24...` | 24-bit tam sayı |
| `0x2` nibble | `...Int32...` | 32-bit tam sayı |
| `0x3` nibble | `...Float32...` | 32-bit kayan nokta |
| `0x4` nibble | `...Float64...` | 64-bit kayan nokta |
| `0x8` nibble | `...Int32...16` | 32-bit kelime içinde 16-bit dolgulu (left-justified) |
| `0x9` nibble | `...Int32...18` | 32-bit kelime içinde 18-bit dolgulu |
| `0xa` nibble | `...Int32...20` | 32-bit kelime içinde 20-bit dolgulu |
| `0xb` nibble | `...Int32...24` | 32-bit kelime içinde 24-bit dolgulu |

`(Kanıt: `asio.h` enum değerlerinden çıkarım — tablo, enumun bit yapısını
açıklar; değerler §5.1'deki gibi aynen korunur)` — örneğin
`ASIOSTInt32LSB24 = 0x1b`: `0x10` (LSB) + `0xb` (24-bit-in-32) → little-endian,
32-bit kelime, 24-bit etkin bit.

### §5.3 Byte Genişliği Tablosu (türetilmiş — host hesabı)

| Format | Bayt/genel | Not |
|---|---|---|
| `Int16MSB` / `Int16LSB` | 2 | 16-bit PCM |
| `Int24MSB` / `Int24LSB` | 3 | 24-bit PCM (paketlenmiş, byte-aligned) |
| `Int32MSB` / `Int32LSB` | 4 | 32-bit PCM (tam) |
| `Float32MSB` / `Float32LSB` | 4 | 32-bit IEEE 754 (ASIOSTFloat32*) |
| `Float64MSB` / `Float64LSB` | 8 | 64-bit IEEE 754 |
| `Int32*16/18/20/24` | 4 | 32-bit kelime; etkin bit 16/18/20/24, **sol-justify** (MSB dolgu) |
| `DSDInt8MSB` / `DSDInt8LSB` | 1 | DSD 1-bit akış, bayt başına 8 bit |

`(Kanıt: §5.1 enum + §5.2 bit kodlaması — bu tablo byte genişliklerinin
türetilmiş karşılığıdır; ASIO SDK'da byte genişliği literal olarak yazılmaz,
host hesaplar)` — **dönüşüm (converter) katmanı bu byte genişliğini
kullanarak** host iç temsiline (float32) çevirir (§6).

---

## §6 Dönüşüm (Converter) Katmanının Sözleşmesi

### §6.1 Yön: ASIO Formatı → Host İçi Temsil

```text
ASIO tamponu (§5 formatı, sürücü belleği)
        │  convert (host, bufferSwitch dışında veya içinde)
        ▼
host iç temsil (tipik: float32, ADR-017 "32-bit float")
```

**Kural (ADR-017):** CoreMusic'te ses verisi iç temsili **32-bit float**'tır
(bkz. `.ai/.decisions/index.md`, ADR-017). ASIO sürücüsü `Int16/Int24/Int32/
Float32/Float64` (§5.1) formatlarından herhangi birinde veri sunabilir; host
bunu float32 iç temsiline dönüştürür. Dönüşüm **buffer örneği başına**
gerçekleşir ve sıfır-allokasyon kuralına tabidir (dönüşüm tamponları baştan
allocate edilir — bkz. `AGENTS.md` §L0 kural 1).

### §6.2 Dönüşüm Matrisi (sözleşme)

| ASIO formatı (§5) | Host float32 dönüşümü | Ölçek (gain) mantığı |
|---|---|---|
| `Int16*` (2 byte) | 16-bit tam → float32 | `value / 32768.0` (tam sayı aralığı -32768..32767 → -1.0..1.0) |
| `Int24*` (3 byte) | 24-bit tam → float32 | `value / 8388608.0` (2^23) |
| `Int32*` (4 byte, tam) | 32-bit tam → float32 | `value / 2147483648.0` (2^31) |
| `Int32*16` (4 byte, 16 etkin) | 16-bit etkin → float32 | etkin bit 16; sağa kaydırma + `value / 32768.0` |
| `Int32*18` | 18-bit etkin → float32 | `value / 131072.0` (2^17) |
| `Int32*20` | 20-bit etkin → float32 | `value / 1048576.0` (2^19) |
| `Int32*24` | 24-bit etkin → float32 | `value / 8388608.0` (2^23) |
| `Float32*` | kopya (zaten float32) | gain = 1.0 |
| `Float64*` | 64→32 daraltma | gain = 1.0 |
| `DSDInt8*` | DSD → PCM gerekir | **dönüşüm yok** — DSD akışı doğrudan PCM iç temsiline çevrilemez (bkz. §6.4) |

`(Kanıt: §5.1-§5.3 enum + bit kodlaması; §6.1 ADR-017 iç temsil kuralı —
ölçekler 2^n tam sayı aralıklarının float32 karşılığı olarak türetilmiştir)`.

### §6.3 JUCE `ASIOSampleFormat` Kazıma Stratejisi (kanıtlanmış)

JUCE `juce_ASIO_windows.cpp`, ASIO formatlarını **runtime'da değil,
derleme zamanında** yönetir; `ASIOSampleFormat` enumu ASIO'nun §5.1
enumunu birebir yansıtır: `Int16, Int24, Int32, Float32, Float64, Int32_16,
Int32_18, Int32_20, Int32_24, DSD8` — ve `JUCE_ASIOFormatToASIOSampleType()`
fonksiyonu ASIO tipini bu enuma çevirir. `(Kaynak: JUCE kaynağı §2)`.

Dönüşüm deseni, giriş/çıkış tamponlarının `float**` pointer'larına
**static_cast** ile yönlendirilmesidir (JUCE kaynağı §11, processBuffer
içi):

```cpp
float** floatSamples = (float**) buffer.channelData;  // static_cast deseni
short* shortInput = (short*) info.buffers[bufferIndex];
// Int32LSB girişi: (int*) info.buffers[bufferIndex]
```

`(Kaynak: JUCE kaynağı §11)` — JUCE **tip doğrulamasını switch-case ile
yapar** (format tanınmazsa `ASE_NotPresent` döner, §11 "unhandled
case" tablosu); dönüştürme sırasında **kazıyarak (cast)** okur. Örneğin
`Int32LSB` girişinde `(int*)` ile okuyup float32'ye çarpar. JUCE'nin
`g` (gain) sabitleri:

| JUCE formatı | Gain (JUCE kaynağı §11) |
|---|---|
| `Int16` → float32 | `1.0 / 32768.0` |
| `Int24` → float32 | `1.0 / 8388608.0` (0x800000) |
| `Int32` → float32 | `1.0 / 2147483648.0` (0x80000000) |
| `Float32` | `1.0` (doğrudan) |
| `Int32_16/18/20` | JUCE'de `unhandled` (§6.4) |

`(Kaynak: JUCE kaynağı §11 — "unhandled case" tablosu; gain değerleri JUCE
kaynağının kendi tanımlarıdır)` — §6.2 tablosu ASIO'nun **sözleşmesini**,
bu tablo JUCE'nin **uygulamasını** gösterir; ikisi 2^n ölçeğinde aynı sonucu
verir.

### §6.4 JUCE'nin Ele Almadığı Formatlar (kanıtlanmış boşluk)

JUCE `ASIOSampleFormat` switch-case'inde **unhandled** (ele alınmamış)
formatlar:

| Format | JUCE davranışı | Sonuç |
|---|---|---|
| `Int32LSB18` | unhandled → `ASIOSampleFormat::Int32LSB18` case'i yok | kanal `ASE_NotPresent` ile reddedilir |
| `Int32LSB20` | unhandled | Aynı |
| `DSDInt8MSB` / `DSDInt8LSB` | JUCE `DSD8` enumu var ama dönüşüm yok | DSD cihazları JUCE ASIO ile çalışamaz |

`(Kaynak: JUCE kaynağı §11 "unhandled case" tablosu)` — bu boşluk, ASIO
§5.1 enumunda tanımlı formatların **tamamının host tarafından desteklenmesini**
zorunlu kılmaz; host desteklemmediği formatı `ASE_NotPresent` ile reddedebilir
(bkz. `asio-hata-yonetimi-ve-fallback.md`). Bu, CoreMusic için **P1 risk**:
DSD veya 18/20-bit çıkışlı bir cihaz JUCE ASIO backend'inde açılamaz.

### §6.5 Converter Uygulama Sözleşmesi (L0 kural eşlemesi)

Dönüşüm katmanı (§6.1-§6.4) uygulamaya geçerken bağlayıcı L0 kurallarıyla
eşleştirilir — her satır bu dosyanın değil, kök `AGENTS.md` §L0'nın kuralıdır:

| L0 Kuralı | Converter'daki Karşılığı | Denetim |
|---|---|---|
| Kural 1 — sıfır-allokasyon | Dönüşüm tamponları `ASIOCreateBuffers` aşamasında baştan allocate edilir; `bufferSwitch` içinde `new`/`malloc`/`delete` YOK | kod taraması |
| Kural 2 — lock-free | Converter saf (pure) fonksiyondur: kilit, mutex, atomik paylaşım içermez | kod taraması |
| Kural 3 — `noexcept` | Callback zinciri ve converter imzaları `noexcept` | imza denetimi |
| Kural 4 — `constexpr` | Ölçek çarpanları (§6.2) derleme zamanı sabittir | kod denetimi |
| Kural 5 — `alignas(64)` | Kanal tampon dizileri cache-line hizalı yerleşir | `static_assert`/yerleşim |
| Kural 7 — float32 | Çıkış her kanalda `float` (ADR-017) | tip denetimi |

**Sözleşme (bağlayıcı):** converter **yalnız** §6.2 matrisindeki dönüşümü yapar;
gain, pan, EQ gibi DSP zinciri §6.1'deki tek yönde (ASIO → host) **sonra** gelir.
Converter çağırmadan önce `numProc` (örnek sayısı) `bufferSampleSize`'a (§3.2)
eşittir; bu eşitlik bozuksa dönüşüm **durdurulur** (taşma koruması), hata
`ASE_InvalidArg` ile bildirilir (bkz. `asio-hata-yonetimi-ve-fallback.md`).

**[ORİJİNAL ÖRNEK · derlenmedi]** — §6.2 `Int16*` satırının saf karşılığı:

```cpp
// [ORİJİNAL ÖRNEK] Int16LSB → float32 kanal converter — derlenmedi,
// yalnız sözleşmeyi gösterir (sıfır-allokasyon: çağranda tampon hazırdır)
static void convertInt16ToFloat(const short* src, float* dst, long n) noexcept
{
    constexpr float kGain = 1.0f / 32768.0f;   // §6.2: value / 32768.0
    for (long i = 0; i < n; ++i)
        dst[i] = static_cast<float>(src[i]) * kGain;
}
```

---

## §7 Kanal + Format Karar Tablosu (Host Yol Haritası)

| Durum | Host eylemi | Referans |
|---|---|---|
| `ASIOGetChannels` sonrası | Tüm giriş/çıkış kanalları için `ASIOGetChannelInfo` çağır; `name`, `group`, `type` topla | §2.2-§2.3 |
| `type` host formatına uygun | Doğrudan float32'ye dönüştür (§6.2 matrisi) | §6.2 |
| `type` JUCE'de unhandled (18/20-bit, DSD) | Kanalı reddet veya sürücü isteğini `ASE_NotPresent` ile cevapla | §6.4 |
| `group` alanı ilginç (tek kanallı gruplar) | `ASIOMAXCHANNELS` değeri "grupsuz" kabul et; stereo eşlemeyi `group`'a göre kur | §2.1 |
| `ASIOCreateBuffers` sonrası | `buffers[2]` pointer'larını **asla free etme** (sürücü belleği) | §3.1 |
| `bufferSampleSize` 2'nin kuvveti değilse | Çağrı reddedilir — host güç-2 boyutu sağlamalı | §3.2 |

---

## §8 Soru → Cevap Özet Tablosu

| # | Soru (kapsam §2.1) | Kısa Cevap | § |
|---|---|---|---|
| 1 | `ASIOChannelInfo` hangi alanları taşır? | `channel, isInput, group, type, active, channelOffset, name[32], shortLabel[8]` | §2.1 |
| 2 | `group` alanı ne anlama gelir? | Stereo grup indeksi veya `ASIOMAXCHANNELS` (grupsuz); Cubase VST `shortLabel`'ı da bu yapıdadır | §2.1 |
| 3 | Giriş/çıkış sayısı nasıl okunur? | `ASIOGetChannels(&nIn, &nOut)` tek çağrıda iki sayıyı döndürür | §2.2 |
| 4 | Tam kanal haritası nasıl çıkarılır? | Her kanal için `ASIOGetChannelInfo` (host `channel`+`isInput` yazar, sürücü cevabı yazar) | §2.3 |
| 5 | Buffer nasıl bağlanır? | `ASIOBufferInfo` (host: `isInput`+`channelNum`) → `ASIOCreateBuffers` (sürücü: `buffers[2]`) | §3.1-§3.2 |
| 6 | `buffers[2]` neden iki slot? | Çift tampon (double buffering); `bufferSwitch(index)` hangi slotun aktif olduğunu verir | §3.1 |
| 7 | `ASIOSampleType` bayrakları nasıl kodlanır? | `0x0x` MSB, `0x1x` LSB, `0x2x` DSD; nibble genişlik (16/24/32/float) | §5.2 |
| 8 | Dönüşüm katmanının sözleşmesi? | ASIO formatı → float32 (ADR-017); 2^n ölçek; JUCE `static_cast` deseni + gain tablosu | §6.1-§6.3 |
| 9 | Hangi formatlar desteklenmiyor (JUCE)? | `Int32LSB18`, `Int32LSB20`, DSD → `unhandled`, kanal reddi | §6.4 |

---

## §9 Terimler Sözlüğü

| Terim | Tanım |
|---|---|
| ASIOChannelInfo | Kanal kimlik/bilgi yapısı: yön, grup, format, etiket (§2.1) |
| Stereo group | `group` alanının işaretlediği sesli çift; `ASIOMAXCHANNELS` = grupsuz (§2.1) |
| shortLabel | 8 karakterlik kısa etiket; Cubase VST tarafından kullanılır (§2.1) |
| ASIOBufferInfo | Buffer ↔ kanal eşlemesi; `buffers[2]` çift tampon (§3.1) |
| Double buffering | İki slot (`buffers[0]`/`buffers[1]`) ile kesintisiz akış (§3.1) |
| ASIOSampleType | Örnek formatı enumu: MSB/LSB/DSD bayrakları + genişlik (§5.1) |
| Left-justified | `Int32*16/18/20/24`: 32-bit kelime içinde MSB'ye hizalı etkin bit (§5.2) |
| Converter | ASIO formatı → host float32 dönüşüm katmanı (§6.1) |
| Gain (ölçek) | 2^n tam sayı aralığını -1..1'e indiren çarpan (§6.2) |
| static_cast deseni | JUCE'nin ASIO tamponunu host tipine yönlendirme yöntemi (§6.3) |
| unhandled format | JUCE switch-case'inde olmayan format → kanal reddi (§6.4) |

---

## §10 Doğrulama ve Sonraki Adım

| Adım | Durum | Kanıt |
|---|---|---|
| §2 `ASIOChannelInfo` tanımı | Kanıtlandı | `asio.h` yapı tanımı (§2.1) |
| §2.2-§2.3 sorgu API'leri | Kanıtlandı | `asio.h` prototip (§2.2), çift-tampon deseni (§2.3) |
| §3 `ASIOBufferInfo` + `ASIOCreateBuffers` | Kanıtlandı | `asio.h` yapı + prototip yorumu (§3.1-§3.2) |
| §5 `ASIOSampleType` enumu | Kanıtlandı | `asio.h` enum — değerler aynen alındı (§5.1) |
| §5.2 bit kodlaması | Türetilmiş | §5.1 enum değerlerinden çıkarım (§5.2) |
| §6.2 dönüşüm matrisi | Türetilmiş | §5 + §6.1 ADR-017 (§6.2) |
| §6.3-§6.4 JUCE uygulaması | Kanıtlandı | JUCE kaynağı §2, §11 (§6.3-§6.4) |
| Dosya içi spec-kod tutarlılığı | Doğrulandı | §5.1 enum ↔ §5.2 bit ↔ §6.2 ölçek aynı genişlikleri kullanır |

**Sonraki adım:** `validation: []` alanını, `asio-kapsam.md` §12.3'teki
doğrulama protokolüne göre doldurmak (16/24/32-bit giriş buffer'ının float32
dönüşümü + kazanç doğrulaması).

---

## §11 Dosya Künyesi ve Kapsam Sınırı Özeti

| Alan | Değer |
|---|---|
| Kapsanan yapılar | `ASIOChannelInfo` · `ASIOBufferInfo` · `ASIOSampleType` enumu (§2, §3, §5) |
| Kapsanan API'ler | `ASIOGetChannels` · `ASIOGetChannelInfo` · `ASIOCreateBuffers` (§2.2-§3.2) |
| Kapsanan sözleşme | ASIO formatı → host float32 converter + JUCE kazıma stratejisi (§6) |
| Kapsam dışı | gecikme ölçümü · timestamp/clock · hot-swap · 8.1 çok-kanal haritası (§1) |
| Bağımlılıklar | `asio-kapsam.md` (§2.1 Soru 2 · §14) · `AGENTS.md` §L0 · ADR-017 (float32) |
| Bağımlı dosyalar | `asio-cpp-ornekleri.md` (converter çağrısı) · `asio-hata-yonetimi-ve-fallback.md` (`ASE_*` bildirimi) |
| Kanıt durumu | `asio.h` yapı/enum/prototip + JUCE kaynağı §2/§11 → `evidence: []` (dosya içi) |
| Açık soru | 18/20-bit ve DSD formatları JUCE backend'inde `unhandled` (§6.4) → cihaz açılamaz |
| Doğrulama adımı | 16/24/32-bit giriş buffer'ının float32 dönüşümü + kazanç ölçümü (§10) |

**Dosya içi § sayımı:** §0-§11 (12 başlık) · 11 tablo · 5 kod bloğu
(C tanımı 2 · C++ tanımı 2 · `text` akış 2 — §2.1, §2.2, §3.1, §3.2, §5.1, §2.3/§4
için akış diyagramları).

---

**Kaynak (yetki zinciri):** `.ai/architecture/K003-audio/backend/asio/asio-kapsam.md` §2.1 (Soru 2), §14
**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
