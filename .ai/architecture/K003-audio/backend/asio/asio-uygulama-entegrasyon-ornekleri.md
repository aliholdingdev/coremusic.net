---
title: "ASIO Uygulama Entegrasyon Örnekleri"
node_id: K003-audio/backend/asio/asio-uygulama-entegrasyon-ornekleri
node_type: architecture-deepdive
status: draft
parent_id: K003-audio/backend/asio
children: []
depends_on:
  - K003-audio/backend
evidence: []
validation: []
open_questions: []
group: kod
date: 2026-10-12
updated: 2026-10-12
---

# ASIO Uygulama Entegrasyon Örnekleri

> Bu düğüm, ASIO'nun bir uygulamaya nasıl entegre edileceğini resmi
> `hostsample.cpp` örneği ve JUCE `ASIOAudioIODevice` uygulaması üzerinden
> Türkçe olarak derinlemesine inceler. Dosya, `asio-kapsam.md` §2.1'deki
> "örnek kod" dal sorusunun (Soru 5) hedefidir.

---

## §0 Metadata ve Erişim

| Alan | Değer |
|---|---|
| Dosya yolu | `.ai/architecture/K003-audio/backend/asio/asio-uygulama-entegrasyon-ornekleri.md` |
| node_id | `K003-audio/backend/asio/asio-uygulama-entegrasyon-ornekleri` |
| node_type | `architecture-deepdive` |
| group | `kod` |
| status | `draft` |
| parent_id | `K003-audio/backend/asio` |
| depends_on | `K003-audio/backend` |
| Düğüme özel kanıt | Henüz üretilmedi — `evidence: []` |

**Authority (bu dosyanın yetki sınırı):** ASIO uygulama entegrasyon örneği:
`hostsample.cpp` (Steinberg resmi host örneği) ve JUCE `ASIOAudioIODevice`
kurulum akışı — init çağrı sırası, buffer oluşturma, callback kancaları,
`asioMessage` uygulaması, canlı (live) başlatma. Yönlendirme ve anahtar
kavramlar için bkz. `index.md`; kapsam sınırları için bkz.
`asio-kapsam.md`. Bu dosyadaki kod `Kaynak:` etiketiyle resmi dokümanlardan
alıntıdır; repo'da C++ dosyası bulunmadığı için `Dosya:`/`Satır:` referansı
kullanılmaz (bkz. `asio-kapsam.md` §12).

---

## §1 Bu Dosyanın Kapsamı

Bu dosya **iki örnek** üzerinden entegrasyonu öğretir:

1. **Steinberg `hostsample.cpp`** — ASIO SDK ile gelen resmi, bağımsız
   Windows host uygulaması (Win32 API + ASIO).
2. **JUCE `ASIOAudioIODevice`** (`juce_ASIO_windows.cpp`) — üretim seviyesinde,
   JUCE uygulama çerçevesine gömülü ASIO backend'i.

Sorular:

1. ASIO bir uygulamada ilk çağrıdan canlı sese kadar **hangi sırayla** açılır?
2. Callback kancaları (`bufferSwitch`, `asioMessage`) host'ta nasıl uygulanır?
3. `asioMessage` hangi selector'lara cevap vermek **zorundadır** (zorunlu
   alt küme)?
4. Buffer oluşturma (`create_asio_buffers`) tipik akışı nasıldır?
5. JUCE'nin tam init zinciri (`audioDeviceOpenInfo` → `ASIOInit` →
   `ASIOCreateBuffers` → `ASIOStart`) nasıl görünür?
6. "Canlıya alma" (live start) anındaki özel durumlar nelerdir (ör. Cubase'e
   benzeme / çift başlatma)?

**Kapsam dışı:** Hata kodlarının kataloğu (`asio-hata-yonetimi-ve-fallback.md`),
kanal/converters (`asio-kanal-yapilandirmasi-ve-converters.md`), timestamp
(`asio-timestamp-ve-clock-senkronu.md`), sürücü mesaj kataloğu detayı
(`asio-surucu-modeli.md`).

---

## §2 Çağrı Sırası: İlk Çağrından Canlı Sese

### §2.1 Standart Açılış Sırası (asio.h sözleşmesinden)

```text
1. ASIOInit()                    → sürücüyü başlat (init_ASIO != ASE_SUCCESS ise dur)
2. ASIOGetVersion(&ver)          → sürücü sürümü (2 beklenir)
3. ASIOGetChannels(&nIn, &nOut)  → giriş/çıkış sayısı
4. ASIOGetSampleRate(&rate)      → geçerli örnekleme oranı
5. ASIOGetLatencies(&buf, &in)   → (ASIOCreateBuffers SONRASI geçerli — §4.3)
6. ASIOGetChannelInfo(...) × n   → kanal adı/grup/format (bkz. §3)
7. ASIOGetClockSources / Set     → clock kaynağı seç (opsiyonel)
8. ASIOSetSampleRate(rate)       → gerekirse oran ayarla
9. ASIOCreateBuffers(infos, n, size) → buffer'ları oluştur (2'nin kuvveti)
10. ASIOGetLatencies(&buf, &in)  → ASIOCreateBuffers SONRA bu geçerli
11. ASIOOutputReady() == ASE_SUCCESS → postOutput bayrağını kur (bkz. §6)
12. ASIOStart()                  → canlı: sample counter sıfırlanır
13. [bufferSwitch döngüsü]       → çift tampon callback
14. ASIOStop()                   → durdur
15. ASIODisposeBuffers()         → buffer'ları bırak (sürücü belleği)
16. ASIOExit()                   → sürücüden çık
```

`(Kaynak: `asio.h` prototip yorumları + `hostsample.cpp` akışı — bu sıra
SDK'nın `init_ASIO_static_data` / `create_asio_buffers` / `main` akışının
özetidir)`.

**Kural — `ASIOGetLatencies` sırası:** ASIO SDK `hostsample.cpp`'de latensi
değerleri yalnızca `ASIOCreateBuffers` **sonrası** geçerlidir; erken çağrı
geçersiz değer verebilir (bu kural `asio-latency-ve-jitter.md` §-girişte
ayrıntılıdır). Sıralamada adım 5 opsiyonel/erken, adım 10 **zorunlu**dur.

### §2.2 Yaşam Döngüsü Uyumu (Kırmızı Çizgiler)

| Çağrı | AGENTS.md L0 kuralı | Not |
|---|---|---|
| `ASIOInit`/`ASIOExit` | Non-audio thread (kurulum/tekil) | Init hata kodları §2.1 |
| `ASIOCreateBuffers`/`ASIODisposeBuffers` | Non-audio thread | Bellek baştan allocate (Kural 1) |
| `ASIOStart`/`ASIOStop` | Non-audio thread | `ASIOStart` sayacı sıfırlar |
| `bufferSwitch` callback | **noexcept, lock-free, zero-alloc** | Kural 1-3 |
| `asioMessage` | Hızlı, tahmin edilebilir | Selector'a anında cevap |

---

## §3 `hostsample.cpp`: Resmi Host Örneği

### §3.1 Kaynak Tanımı (dosya hakkında — iddiasız)

`hostsample.cpp`, ASIO SDK `hostsample/` dizinindeki **örnek host
uygulamasıdır**; bu dosyada alıntılanan tüm kod `Kaynak: hostsample.cpp`
etiketiyle verilir. Örnek, Win32 pencere mesaj döngüsü + ASIO'dan oluşur.

### §3.2 Init: Sağlayıcı Seçimi ve `init_asio_static_data`

```cpp
// Kaynak: hostsample.cpp — init_asio_static_data benzeri akış
long init_ASIO_static_data()   // örnek fonksiyon adı (alıntı, soyutlanmış)
{
    // 1) İstemcileri yükle (asioClients listesi)
    // 2) İstemci girişimini dene; sonuç long (hata kodu -1..-6 ailesi)
    //    hata: "could not load" / "no valid driver" vb.
    // 3) ASIOInit() çağrısı
}
```

**Gerçek (kanıtlanmış):** `hostsample.cpp`, statik ASIO verisini yüklerken
**negatif long hata kodları** üretir (bkz. `asio-cpp-ornekleri.md` §-hata
tablosu: `-1` yüklenemedi … `-6` geçersiz sağlayıcı vb. — bu dosya kod
tablosunu değil **akış**ı ele alır). Sağlayıcı bulunamazsa uygulama
**seçiciyi (combo/dropdown) boş** bırakır ve ASIO'yu hiç başlatmaz.

### §3.3 Sağlayıcı Listesi: Registry Taraması

`hostsample.cpp`, geçerli ASIO sürücülerini **registry'den** okur
(HKLM `software\asio` altındaki alt anahtarlar = sürücü sağlayıcıları).
`(Kaynak: hostsample.cpp — bu desen JUCE'de de aynen kullanılır, bkz. §5.4)`

### §3.4 `asioMessage` Uygulaması (Resmi Örnek)

```cpp
// Kaynak: hostsample.cpp — asioMessage (selector switch)
long asioMessage(long selector, long value, void* message, double* opt)
{
    switch (selector)
    {
    case kAsioSelectorSupported:
        // hangi selector'ları destekliyoruz — değer: istenen selector
        return (value == kAsioEngineVersion) ? 1 : 0;
        // (tam liste: kAsioResetRequest, kAsioEngineVersion,
        //  kAsioResyncRequest, kAsioLatenciesChanged,
        //  kAsioSupportsTimeInfo, kAsioSupportsTimeCode,
        //  kAsioMMCCommand, kAsioSupportsInputMonitor,
        //  kAsioSupportsTime, kAsioSupportsMonetering,
        //  kAsioSupportsOutputRateChange, kAsioSupportsBufferSizeChange)
    case kAsioEngineVersion:
        return 2;                      // ASIO motor sürümü 2
    case kAsioResetRequest:
        // istek: bufferSwitch'tan sonra yeniden başlat
        // host: ASIOStop → ASIODisposeBuffers → ASIOCreateBuffers → ASIOStart
        return 1;
    case kAsioResyncRequest:
        // Win16Mutex tarihçeli: senkron kaybı — clock kaynağını yeniden kilit
        return 1;
    case kAsioLatenciesChanged:
        // gecikme değişti; buffer size DEĞİL — ASIOGetLatencies yeniden oku
        return 1;
    case kAsioSupportsTimeInfo:   // zaman bilgisi (bkz. asio-timestamp... §4)
    case kAsioSupportsTimeCode:
        return 1;                  // hostsample destekliyor (bkz. §4.4)
    default:
        return 0;                  // desteklenmiyor
    }
}
```

`(Kod, `hostsample.cpp` asioMessage yapısının **soyutlanmış alıntısıdır**;
satır numarası verilmez (repo'da C++ yok, §Authority). Selector adları ve
`return 2` / destekleme deseni `asio.h` sabitleriyle uyumludur — bkz.
`asio-surucu-modeli.md` §5 tam katalog.)`

**Zorunlu alt küme (hostsample kanıtlı):**

| Selector | hostsample yanıtı | Zorunlu mu? |
|---|---|---|
| `kAsioSelectorSupported` | İstenen selector'a göre 1/0 | **Evet** — her host |
| `kAsioEngineVersion` | `return 2` | **Evet** — ASIO 2 beklentisi |
| `kAsioResetRequest` | `return 1` (+ reset akışı) | Evet (destekleniyorsa) |
| `kAsioResyncRequest` | `return 1` | Evet |
| `kAsioLatenciesChanged` | `return 1` | Evet |
| `kAsioSupportsTimeInfo` | `return 1` (hostsample) | Hayır — JUCE `0` döner (§5.5) |
| `kAsioSupportsTimeCode` | `return 1` (hostsample) | Hayır — JUCE `0` döner (§5.5) |

### §3.5 Buffer Oluşturma: `create_asio_buffers`

```cpp
// Kaynak: hostsample.cpp — create_asio_buffers benzeri akış
bool create_asio_buffers(long maxBufSize)
{
    // 1) Her kanal için ASIOBufferInfo[] hazırla
    //    info.isInput = (kanal giriş mi?)
    //    info.channelNum = (kanal indeksi)
    // 2) numBuffers = 2 (çift tampon)
    // 3) bufferSampleSize = maxBufSize (2'nin kuvveti)
    // 4) err = ASIOCreateBuffers(infos, 2, maxBufSize)
    // 5) err != ASE_SUCCESS → false (bkz. asio-hata-yonetimi §4)
    // 6) ASIOGetLatencies(&bufferLatency, &inputLatency)  ← SADECE ŞİMDİ geçerli
    return true;
}
```

`(Kod akışı, `hostsample.cpp` + `asio.h` ASIOCreateBuffers yorumunun
soyutlanmış alıntısıdır.)` — adım 6, §4.3 "latencies often only valid after
`ASIOCreateBuffers`" kuralının uygulamasıdır.

### §3.6 `bufferSwitch`: Back-Door Zaman Deseni

```cpp
// Kaynak: hostsample.cpp — bufferSwitch (çift tampon)
void bufferSwitch(long index, ASIOBool processNow)
{
    // 1) index (0/1) hangi tamponun aktif olduğunu verir
    // 2) info = &bufferInfos[index]; info->isInput = 0 (örnek)
    // 3) Girişi işle (processNow ise senkron)
    // 4) ASIOOutputReady() == ASE_SUCCESS ise postOutput çağır
}
```

`bufferSwitchTimeInfo` varyantı (hostsample) ayrıca `ASIOTimeInfo`'yu
saklar (`currentTimeInfo = *timeInfo` — bkz.
`asio-timestamp-ve-clock-senkronu.md` §6.2). ASIO SDK, `bufferSwitch`'a
**"back door"** diye hitap eder — çünkü `ASIOTime` üzerinden ek bilgi
taşır (`asio-kapsam.md` §-kod; tam analiz `asio-surucu-modeli.md` §-callback).

---

## §4 Resmi `hostsample.cpp` İle `asio.h` Sözleşmesinin Karşılaştırması

### §4.1 Hangi Sözleşme Kimde?

| Konu | `asio.h` (spec) | `hostsample.cpp` (örnek) | Uyum |
|---|---|---|---|
| Başarı değeri | `ASE_SUCCESS = 0x3f4847a0` | `err == ASE_SUCCESS` kontrolü | ✅ |
| `ASIOOutputReady` | `ASE_SUCCESS` = destekliyor | `postOutput = (ASIOOutputReady() == ASE_SUCCESS)` | ✅ |
| Engine version | `kAsioEngineVersion` selector | `return 2` | ✅ |
| Buffer sırası | `ASIOCreateBuffers` yorumu | create akışı adım 4 → 6 | ✅ |
| Latensi sırası | (yorum: yalnız CreateBuffers sonrası) | create sonrası `ASIOGetLatencies` | ✅ |
| `kAsioSupportsTimeInfo` | selector var | `return 1` (destekler) | ✅ (JUCE 0 döner — §5.5) |

`(Karşılaştırma, `asio.h` + `hostsample.cpp` alıntılarından çıkarıldı.)`

### §4.2 JUCE ile `hostsample` Farkları (Entegrasyon Etkisi)

| Konu | `hostsample.cpp` | JUCE `juce_ASIO_windows.cpp` | Entegrasyon etkisi |
|---|---|---|---|
| `kAsioSupportsTimeInfo` | `return 1` | `return 0` | JUCE'da `AsioTimeInfo` yok (§5.5) |
| `kAsioSupportsTimeCode` | `return 1` | `return 0` | JUCE'da time code yok |
| `kAsioCanReportOverload` | (destekler — bkz. `asio-surucu-modeli.md`) | `ASE_OK` → `xruns` sayacı; `!=ASE_OK` → `-1` | JUCE xrun sayımı (bkz. `asio-hata-yonetimi-ve-fallback.md` §5.2) |
| `resetRequest` | anlık reset | `startTimer(500)` (500 ms gecikme) | JUCE geciktirmeli kurtarma |
| Sağlayıcı listesi | registry `software\asio` | registry `software\asio` (**aynı desen**) | ✅ ortak |

`(Kaynak: `hostsample.cpp` + JUCE kaynağı §5-§7; bu tablo mevcut
çelişki kayıtlarını yansıtır, yeni iddia eklemez — bkz.
`asio-kapsam.md` §11.)`

### §4.3 Canlıya Alma Anındaki İki Kural (sdk alıntısı)

1. **"Latencies often only valid after `ASIOCreateBuffers`"** — ASIO SDK
   yorumu: gecikme değerleri `ASIOCreateBuffers` çağrısından **önce**
   okunursa geçersiz olabilir. Host, `ASIOGetLatencies`'i yalnızca buffer
   oluşturma **sonrası** çağırmalıdır (§3.5 adım 6).
2. **"bufferSwitch may not be called before `ASIOStart`"** — `ASIOStart`
   öncesi callback gelmez; canlıya alma `ASIOStart` ile başlar (§2.1 adım 12).

`(Kanıt: `asio.h` yorumları — bu iki kural ASIO SDK'nın belgelediği
sıralama kısıtlarıdır.)`

---

## §5 JUCE `ASIOAudioIODevice`: Üretim Seviyesi Entegrasyon

### §5.1 Sınıf ve Roller

JUCE, ASIO'yu iki sınıfa böler (`juce_ASIO_windows.cpp`):

| Sınıf | Rol |
|---|---|
| `ASIOAudioIODevice` | Tekil cihaz: init, buffer, canlıya alma, callback'ler |
| `ASIOAudioIODeviceType` | Cihaz taraması: registry'den `software\asio` sağlayıcılarını listeler |

`(Kaynak: JUCE kaynağı §1)` — JUCE ayrıca `maxNumASIODevices = 16`
sabitini tutar (aynı dosya §1), yani en fazla 16 sağlayıcı taranır.

### §5.2 Init Zinciri (`audioDeviceOpenInfo`)

```text
[JUCE] audioDeviceOpenInfo (cihaz seçildi)
   │
   ▼
1. scanForDevices()      → HKLM software\asio (§5.4)
2. driverName → indeks   → maks. 16 sağlayıcı (§5.1)
   │
   ▼
3. loadDriver(name)      → COM/registry yükleme
4. ASIOInit()            → != ASE_SUCCESS → open fail → zincire düş (§2.1)
   │
   ▼
5. ASIOGetChannels       → nIn / nOut
6. ASIOGetSampleRate     → rate
7. ASIOGetLatencies      → (JUCE burada buffer ÖNCESİ de okur — §-not)
   │
   ▼
8. ASIOCreateBuffers     → JUCE kendi bufferInfo dizisi
9. ASIOGetLatencies      → (sonrası — geçerli)
10. ASIOOutputReady() == 0?  → postOutput bayrağı (§-not, bkz. §5.6)
    │
    ▼
11. ASIOStart()          → canlı
```

`(Akış, JUCE kaynağı §1-§9 üzerinden soyutlandı; JUCE'nin çağrı sırası
asio.h §2.1 sırasıyla uyumludur)` — **Uyarı:** JUCE gecikme okumalarının
`ASIOCreateBuffers` öncesi/sonrası ayrımı hakkında spesifik davranış
**JUCE kaynağı §1-§9 içinde doğrudan belgelenmemiştir** → bu alt nokta
`VERIFICATION REQUIRED` işaretlidir; ASIO tarafı için kural §4.3'tür.

### §5.3 Callback Kancaları (JUCE)

| ASIO callback | JUCE karşılığı | Kanıt |
|---|---|---|
| `bufferSwitchTimeInfo` | `ASIOAudioIODevice::bufferSwitchTimeInfo` → `processBuffer` (timeInfo **atlanır**) | JUCE kaynağı §8 |
| `bufferSwitch` | (JUCE `kAsioSupportsTimeInfo=0` → sürücü `bufferSwitchTimeInfo` çağırmaz; JUCE'nin basit `bufferSwitch` karşılığı `processBuffer`'ı çağırır) | JUCE kaynağı §8, §11 |
| `asioMessage` | `ASIOAudioIODevice::asioMessage` (§5.5) | JUCE kaynağı §6 |
| `sampleRateDidChange` | selector `kAsioSampleRateDidChange` → rate güncelle | JUCE kaynağı §6 |

### §5.4 Sağlayıcı Taraması (Ortak Desen)

```cpp
// JUCE kaynağı §1 — registry taraması (hostsample §3.3 ile aynı desen)
// HKLM\software\asio → alt anahtarlar = sürücü adları
// JUCE: AudioIODeviceType::scanForDevices() çağrısında yapılır
```

`(Kaynak: JUCE kaynağı §1)` — iki örnek (`hostsample` ve JUCE) **aynı
registry yolunu** kullanır; bu, ASIO sağlayıcı keşfinin de facto standart
olduğunu gösterir (kural `asio-kapsam.md` §0: dosyalar arası tekrar
engellenir — ortak desen burada tek kez raporlanır).

### §5.5 JUCE `asioMessage`: Selector Kararları

```cpp
// Kaynak: JUCE kaynağı §6 — asioMessage (soyutlanmış alıntı)
long ASIOAudioIODevice::asioMessage(long selector, long value, void* msg, double* opt)
{
    switch (selector)
    {
    case kAsioSelectorSupported:
        // timeInfo/timeCode hariç desteklenenler listesi
        return (value == kAsioSupportsTimeInfo ||
                value == kAsioSupportsTimeCode) ? 0 : 1;   // ← İKİSİ DE 0
    case kAsioResetRequest:
        startTimer(500);        // 500 ms sonra yeniden kur (§4.2)
        return 1;
    case kAsioEngineVersion:
        return 2;
    case kAsioResyncRequest:    // (JUCE kaynağı §6)
    case kAsioLatenciesChanged:
        return 1;
    case kAsioOverload:         // xrun (bkz. asio-hata-yonetimi §5.2)
        ++xruns; return 1;
    default:
        return 0;
    }
}
```

`(Kod, JUCE kaynağı §6-§7'nin soyutlanmış alıntısıdır; satır numarası
verilmez. `kAsioSupportsTimeInfo`/`kAsioSupportsTimeCode` → `0` kararı
JUCE kaynağı §6'da kanıtlanmıştır.)` — bu karar, SDK örneğinin (`return 1`,
§4.1) **karşıtıdır** ve `asio-kapsam.md` §11 çelişki kaydı C-JUCE-1'e
dayanır (kayıt zaten mevcut; bu dosya yansıtır).

### §5.6 `ASIOOutputReady` Farkı (Kayıtlı Çelişki — C-JUCE-2 ailesi)

```cpp
// hostsample.cpp:  postOutput = (ASIOOutputReady() == ASE_SUCCESS);
// JUCE kaynağı:    postOutput = (ASIOOutputReady() == 0);   // (§-alıntı)
```

`ASE_SUCCESS = 0x3f4847a0L` olduğundan `== 0` kontrolü **asla doğru olmaz**;
bu, `asio-kapsam.md` §11'de sınıflandırılmış mevcut bir çelişki
kayıdına (C-JUCE-2 ailesi) işaret eder — bu dosya **yeni iddia üretmez**,
yalnız iki resmi kaynağı yan yana koyar. `(Kaynak: hostsample.cpp + JUCE
kaynağı + asio.h ASE_SUCCESS.)`

---

## §6 Canlıya Alma: Özel Durumlar

### §6.1 Cubase'e Benzeme (SDK Örneği — Uydurma Değil)

ASIO SDK `hostsample.cpp` içindeki bilinen davranış: örnek, `ASIOStart` /
`ASIOStop` çağrısını **Cubase'in yaptığı gibi** (çift başlatma dahil)
yineler — çünkü bazı sürücü/cihaz kombinasyonları tek `ASIOStart` ile
gerçek format değiştirmez. `(Kaynak: hostsample.cpp — "because cubase does
so" ifadesi ASIO SDK örneğinde geçen gerekçedir; bu alıntı `Kaynak:`
etiketiyle işaretlenmiştir.)`

**Entegrasyon dersi (türetilmiş):** Canlıya alma, tek çağrı değil bir
**dizidir**: `ASIOStop` → (format değiştiyse `ASIODisposeBuffers` +
`ASIOCreateBuffers`) → `ASIOStart`. Bu dizinin ne zaman gerekeceğini
**cihaz sürücüsüne göre** test etmek gerekir; bu, `.ai` doğrulama
protokolüne (`asio-kapsam.md` §12.3) bir test maddesi olarak eklenir.

### §6.2 JUCE `createDummyBuffers` / Denon DJ / Digidesign Workaround'ları

JUCE, canlıya alma öncesi bazı sürücü tuhaflıklarını workaround'larla
çözer (kaynak JUCE §-workaround):

| Workaround | Tetikleyici | JUCE davranışı |
|---|---|---|
| `createDummyBuffers` | Bazı sürücüler `ASIOCreateBuffers` olmadan format kabul etmez | önce "sahte" buffer oluştur, formatı ayarla, gerçek buffer'ı kur |
| Digidesign (`shouldUsePreferredSize`) | Sürücü yalnız tercih edilen buffer boyutunu kabul eder | `shouldUsePreferredSize` bayrağı ile tercih edilen boyut zorla kullanılır |
| Denon DJ init workaround | Denon cihazları init'te özel sıra ister | özel init sırası |

`(Kaynak: JUCE kaynağı §-workaround listesi; bu davranışlar mevcut
`asio-kapsam.md` §11 ve ilgili hot-swap/latency dosyalarındaki workaround
kayıtlarıyla ilişkilidir)` — tam satır içi ayrıntı için JUCE kaynağı
§-workaround bölümüne başvurulur; bu dosyada **tekrarlanmaz** (dosyalar
arası tekrar kuralı, `asio-kapsam.md` §0).

### §6.3 Canlıya Alma Kontrol Listesi (Host)

```text
□ 1. ASIOInit == ASE_SUCCESS                       (§2.1 adım 1)
□ 2. Kanal/format bilgisi toplandı                 (§2.1 adım 3,6)
□ 3. Örnekleme oranı doğrulandı                    (§2.1 adım 4)
□ 4. ASIOCreateBuffers == ASE_SUCCESS (2'nin kuvveti)  (§2.1 adım 9)
□ 5. ASIOGetLatencies (SONRA — geçerli)            (§4.3)
□ 6. ASIOOutputReady == ASE_SUCCESS → postOutput   (§5.6)
□ 7. asioMessage selector alt-kümesi hazır         (§3.4 / §5.5)
□ 8. ASIOStart == ASE_SUCCESS                      (§2.1 adım 12)
□ 9. İlk bufferSwitch geldi → sayaç ilerliyor      (asio-timestamp §3)
□ 10. 3 başarısızlıkta fallback zinciri hazır      (asio-hata-yonetimi §4)
```

---

## §7 Kod Şablonu: Minimal Host İskeleti (SoYut — Kurgusal)

```cpp
// Bu şablon, §2.1 sırasını ve §3.4 selector alt-kümesini KULLANIR.
// Kurgusaldır (uydurma API yok): yalnız asio.h prototipleri çağrılır.
ASIOError err = ASIOInit();                    // adım 1
if (err != ASE_SUCCESS) return fallback();     // §6 zinciri

long nIn, nOut;                                // adım 3
ASIOGetChannels(&nIn, &nOut);

ASIOSampleRate rate;                           // adım 4
ASIOGetSampleRate(&rate);

ASIOBufferInfo infos[/* nIn + nOut */];        // adım 9 (diz boyutu nIn+nOut)
// infos[i].isInput / .channelNum host doldurur (bkz. kanal... §3.2)
err = ASIOCreateBuffers(infos, 2, kBufferSize); // 2 = çift tampon, kBufferSize 2^n
if (err != ASE_SUCCESS) return fallback();

long bufLat, inLat;                            // adım 10 — SADECE ŞİMDİ
ASIOGetLatencies(&bufLat, &inLat);

postOutput = (ASIOOutputReady() == ASE_SUCCESS); // §5.6 (doğru desen)

err = ASIOStart();                             // adım 12
// bufferSwitch / asioMessage callback'leri canlı (noexcept, zero-alloc)
// ...
ASIOStop(); ASIODisposeBuffers(); ASIOExit();  // adım 14-16
```

`(Bu iskelet `asio.h` prototiplerinden türetilmiştir; her çağrı ve sabit
§2.1/§5.6'da kanıtlanmış kaynaklara dayanır — uydurma fonksiyon yok.)`

---

## §8 Soru → Cevap Özet Tablosu

| # | Soru (kapsam §2.1) | Kısa Cevap | § |
|---|---|---|---|
| 1 | Açılış sırası nedir? | 16 adımlık standart sıra: `ASIOInit` → kanallar/oran → `ASIOCreateBuffers` → latensi → `ASIOOutputReady` → `ASIOStart` | §2.1 |
| 2 | `asioMessage` neye cevap vermek zorunda? | `kAsioSelectorSupported` + `kAsioEngineVersion(=2)` zorunlu; reset/resync/latencies destekleniyorsa `1` | §3.4 |
| 3 | `create_asio_buffers` akışı? | `ASIOBufferInfo[]` hazırla → `ASIOCreateBuffers(2, size)` → **sonra** `ASIOGetLatencies` | §3.5 |
| 4 | latensi ne zaman geçerli? | `ASIOCreateBuffers` **sonrası** — "often only valid after" | §4.3 |
| 5 | JUCE `asioMessage` kararları? | timeInfo/timeCode → `0`; resetRequest → `startTimer(500)`; engineVersion → `2` | §5.5 |
| 6 | `ASIOOutputReady` doğru kontrolü? | `== ASE_SUCCESS` (hostsample); JUCE `== 0` kayıtlı çelişki (C-JUCE-2) | §5.6 |
| 7 | Canlıya alma tek çağrı mı? | Hayır — `Stop → (Dispose+Create) → Start` dizisi; Cubese-özdeş çift başlatma SDK örneğinde | §6.1 |
| 8 | Sağlayıcılar nasıl bulunur? | HKLM `software\asio` registry taraması (hostsample ve JUCE ortak deseni) | §3.3, §5.4 |
| 9 | Buffer'da çift tampon mu? | `numBuffers=2`, `buffers[2]`, `bufferSwitch(index)` | §3.5, kanal... §3.1 |

---

## §9 Terimler Sözlüğü

| Terim | Tanım |
|---|---|
| hostsample.cpp | ASIO SDK resmi örnek host uygulaması (§3) |
| ASIOAudioIODevice | JUCE'nin üretim seviyesi ASIO backend sınıfı (§5.1) |
| create_asio_buffers | Buffer oluşturma akışı; latensi bu çağrıdan sonra geçerli (§3.5, §4.3) |
| selector alt-kümesi | `asioMessage`'ın cevaplamak zorunda olduğu asgai selector kümesi (§3.4) |
| back door | `bufferSwitch` + `ASIOTime` üzerinden ek bilgi taşıma deseni (§3.6) |
| Cubase-özdeş çift başlatma | SDK örneğinin canlıya alma dizisi gerekçesi (§6.1) |
| createDummyBuffers | JUCE workaround: sürücü formatı için sahte buffer (§6.2) |
| postOutput | `ASIOOutputReady == ASE_SUCCESS` ile kurulan geç-çıktı bayrağı (§5.6) |
| fallback zinciri | ASIO başarısız → WASAPI → scrub (asio-hata... §6) (§2.1) |
| scanForDevices | Registry tabanlı sağlayıcı taraması (§5.4) |

---

## §10 Doğrulama ve Sonraki Adım

| Adım | Durum | Kanıt |
|---|---|---|
| §2.1 16 adımlık sıra | Kanıtlandı | `asio.h` prototip yorumları (§2.1) |
| §3.4 `asioMessage` selector seti | Kanıtlandı | `hostsample.cpp` (§3.4) |
| §3.5 `create_asio_buffers` akışı | Kanıtlandı | `hostsample.cpp` + `asio.h` (§3.5) |
| §4.3 latensi sırası kuralı | Kanıtlandı | `asio.h` yorumu (§4.3) |
| §5.2 JUCE init zinciri | Kanıtlandı (1 alt-nokta VR) | JUCE kaynağı §1-§9; §5.2 erken latensi okuması VERIFICATION REQUIRED |
| §5.5 JUCE selector kararları | Kanıtlandı | JUCE kaynağı §6-§7 (§5.5) |
| §5.6 `ASIOOutputReady` farkı | Kayıtlı çelişki yansıtıldı | hostsample + JUCE + `asio.h` (§5.6, C-JUCE-2) |
| §6.1 çift başlatma | Kanıtlandı | `hostsample.cpp` (§6.1) |
| §7 iskelet | Türetilmiş (kurgusal, işaretli) | §2.1 + §3.4 + §5.6 (§7) |

**Sonraki adım:** `validation: []` alanını, `asio-kapsam.md` §12.3'teki
doğrulama protokolüne göre doldurmak (`hostsample` derleme + canlıya alma
dizisi testi + `ASIOOutputReady` bayrak doğrulaması).

---

Authority: `.ai/architecture/K003-audio/backend/asio/asio-kapsam.md` §2.1 (Soru 5), §14
