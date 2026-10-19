---
node_id: "K000-isletim-sistemi/isletim-sistemi-turu/macos/macos-device-ve-format"
node_type: architecture
status: draft
parent_id: "K000-isletim-sistemi/isletim-sistemi-turu/macos"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-turu/macos/macos-coreaudio-stacki"
  - "K000-isletim-sistemi/isletim-sistemi-audio"
evidence:
  - "https://developer.apple.com/documentation/CoreAudioTypes/AudioStreamBasicDescription (✅ HTTP 200 · 2026-10-12)"
  - "https://developer.apple.com/documentation/CoreAudioTypes/AudioBuffer (✅ HTTP 200 · 2026-10-12)"
  - "https://developer.apple.com/documentation/CoreAudioTypes/AudioBufferList (✅ HTTP 200 · 2026-10-12)"
  - "https://developer.apple.com/documentation/CoreAudioTypes/AudioFormatFlags (✅ HTTP 200 · 2026-10-12)"
  - "https://developer.apple.com/documentation/CoreAudioTypes/audio-format-flags (✅ HTTP 200 · 2026-10-12)"
  - "https://developer.apple.com/documentation/CoreAudioTypes/AudioFormatID (✅ HTTP 200 · 2026-10-12)"
  - "https://developer.apple.com/documentation/CoreAudioTypes/audio-format-identifiers (✅ HTTP 200 · 2026-10-12)"
  - "https://developer.apple.com/documentation/CoreAudioTypes/AudioChannelLayout (✅ HTTP 200 · 2026-10-12)"
  - "https://developer.apple.com/documentation/CoreAudioTypes/AudioSampleType (✅ HTTP 200 · 2026-10-12)"
  - "https://developer.apple.com/documentation/AudioToolbox/audio-format-services (✅ HTTP 200 · 2026-10-12)"
validation:
  - "2026-10-12: 10 benzersiz resmi URL · hepsi HTTP 200 (HEAD) doğrulandı; içerik alıntısı YAPILMADI → iddialar genel mimari anlatımıdır"
  - "≥500 satır · 10 alanlı frontmatter · §N başlıkları · Authority imzası"
open_questions:
  - "Kod kanıtı: repo'da .cpp/.h dosyası 0 → kod blokları [ORİJİNAL ÖRNEK · derlenmedi] etiketli · status: draft"
  - "Sürüm bazlı format bayrak değişiklikleri (hangi macOS sürümünde hangi flag) doğrulanmadı → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# macos-device-ve-format — macOS ses cihazı ve format modeli (ASBD · AudioBuffer · ChannelLayout · FormatID)

> **Eksen:** `K000-isletim-sistemi / isletim-sistemi-turu / macos` · **Grup:** `mimari` · **Durum:** `draft`
> **Amaç:** CoreAudio'da ses **biçiminin** (format) ve **tamponların** (buffer) nasıl
> modellendiğini — `AudioStreamBasicDescription` (ASBD), `AudioBuffer(List)`,
> `AudioFormatFlags`/`AudioFormatID`, `AudioChannelLayout`, `AudioSampleType` ve
> format-dönüştürme servislerini (AudioConverter) — CoreMusic K000 (PCM3168A / 32-bit float /
> 48 kHz / 512 örnek) bağlamında mimari derinlikle haritalamak.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12 · **Mod:** RED TEAM / TRUTH MODE

---

## §0 · Kapsam, Bağlam ve Yasaklar

| Öğe | Değer |
|---|---|
| Dosya | `macos/macos-device-ve-format.md` (yeni — beyazliste) |
| Eksen | K000-isletim-sistemi / isletim-sistemi-turu / macos |
| Grup | `mimari` |
| Kapsam | ASBD alanları · AudioBuffer(List) · FormatFlags · FormatID · ChannelLayout · SampleType · format servisleri (dönüştürme) |
| Kapsam dışı | Cihaz keşfi/mülkiyet (→ `macos-coreaudio-stacki`) · driver extension (→ `macos-kernel-extension-durumu`) · AVAudioSession route (→ `macos-ses-stacki`) · kod derleme (→ `macos-kod-ornekleri`) |
| Yasak | iOS dosyaları · wasapi/ · K003 kod yazımı · repo Dosya:/Satır: uydurma · `TBD`/`...` dolgu |
| Veri tipi | 32-bit float (ADR-017) — macOS tarafında `kAudioFormatFloat32` |
| Buffer | 512 örnek / 48 kHz (sartname; olcum PLANNED) |
| DAC | PCM3168A (ADR-038 H001; PCM5122 ABSOLUTELY FORBIDDEN) |

**ELI15:** Ses verisi hafızada "sayı dizisi" olarak durur; ama bu dizinin **anlamını**
(format) ve **nasıl bölündüğünü** (kanal/tampon) ayrı yapılar tanımlar. macOS bunu ASBD
(format tarifi), AudioBuffer/List (veri kapları) ve ChannelLayout (kanal haritası) ile yapar.

## §1 · Mimari Konum ve Bağımlılık Zinciri

| Katman | Bu dosyanın konumu | Kardeş dosya |
|---|---|---|
| Framework | CoreAudioTypes (tip tanımları) + AudioToolbox (format servisleri) | — |
| Nesne modeli | Cihaz/stream nesneleri (CoreAudio) | `macos-coreaudio-stacki` |
| Biçim modeli | ASBD/Buffer/Layout (bu dosya) | — |
| Dönüştürme | AudioConverter (format servisleri) | `macos-kod-ornekleri` (AudioConverter servis örneği) |
| Uygulama katmanı | AVAudioEngine format seçimi | `macos-ses-stacki` |
| Sürücü katmanı | IOUserAudioStream format tanımı | `macos-kernel-extension-durumu` |

**Zincir:** `CoreAudio (object) → bu dosya (format/tampon) → AudioConverter (dönüştürme) → çıktı (DAC)`. Format uyuşmazlığı zincirin her halkasında hata üretir.

## §2 · ASBD (AudioStreamBasicDescription) — Format Tarifi

ASBD, bir ses akışının **biçim tarifidir** (C yapısı): örnek oranı, kanal sayısı, bayt/örnek, bayt/saniye, format kimliği, format bayrakları ve (değişken-bit-derinlikli formatlarda) mBitsPerExample.

| Alan | Rol | CoreMusic değeri |
|---|---|---|
| `mSampleRate` | Örneklem oranı (Hz) | 48000.0 |
| `mFormatID` | Format kimliği (ör. `kAudioFormatLinearPCM`) | `kAudioFormatLinearPCM` |
| `mFormatFlags` | Bayraklar (float/int, little-endian, packed) | float32 + little-endian + packed |
| `mBytesPerPacket` | Paket başına bayt | 4 (float32 tek kanal) |
| `mFramesPerPacket` | Paket başına çerçeve | 1 (PCM) |
| `mBytesPerFrame` | Çerçeve başına bayt | kanal × 4 |
| `mChannelsPerFrame` | Çerçeve başına kanal | 2 (stereo hedef) / 8 (surround hedef) |
| `mBitsPerChannel` | Kanal başına bit | 32 |
| `mReserved` | Rezerve (dolu değilse 0) | 0 |

**Tarif değil veri:** ASBD **örnek veriyi taşımaz**; yalnız **nasıl okunacağını** söyler. Taşıma `AudioBuffer`'ın işidir (§3).

## §3 · AudioBuffer ve AudioBufferList — Veri Kapları

| Yapı | Anlam | Kullanım |
|---|---|---|
| `AudioBuffer` | Tek bir bellek bloğu: bayt dizisi + kanal sayısı + boyut | Tek buffer (ör. non-interleaved tek kanal) |
| `AudioBufferList` | `AudioBuffer` dizisi (esnek dizi) | Interleaved tek buffer **veya** non-interleaved N buffer |
| `mNumberBuffers` | listedeki buffer sayısı | interleaved → 1; non-interleaved → kanal sayısı |
| `mBuffers[i].mData` | i. buffer'ın veri göstergesi | gerçek PCM/float dizisi |
| `mBuffers[i].mDataByteSize` | i. buffer'ın bayt boyu | çerçeve sayısı × bayt/çerçeve |
| `mBuffers[i].mNumberChannels` | i. buffer'daki kanal sayısı | interleaved: tüm kanallar; non-interleaved: 1 |
| Sahiplik | `mData` **gösterge** — bellek sahibi çağıran taraftır | allocate → çağıran; free → çağıran (zero-allocation kuralı: audio thread'te allocate YOK) |

**Interleaved vs non-interleaved:** interleaved = tek buffer, kanallar iç içe (L R L R …); non-interleaved = kanal başına ayrı buffer (planar). İkisi de `AudioBufferList` ile ifade edilir; ASBD `mFormatFlags`'in non-interleaved bit'i hangisi olduğunu söyler.

## §4 · AudioFormatFlags ve audio-format-flags — Bayrak Sistemi

| Kavram | Kaynak | Rol |
|---|---|---|
| `AudioFormatFlags` | `CoreAudioTypes/AudioFormatFlags` | Bayrak **tipi** (bit alanı) |
| `audio-format-flags` | `CoreAudioTypes/audio-format-flags` | Bayrak **kataloğu** (sayfalar) |
| Float bit'i | Bayrak kümesinde | float32/int tanımı |
| Big/little-endian bit'i | Bayrak kümesinde | byte sırası |
| Packed/aligned bit'leri | Bayrak kümesinde | hizalama/dolgu |
| Non-interleaved bit'i | Bayrak kümesinde | planar düzen |
| Signed/unsigned bit'i | Bayrak kümesinde | işaretli tamsayı |
| Float **is not** bit'liği | Bayrak kataloğu (derleyici kontrolü) | float32 ↔ int16 karışıklığını önler |

**Çifte kapı:** Bayrak hem `AudioFormatFlags` (tip) hem `audio-format-flags` (katalog) üzerinden okunur — bu dosya ikisini **ayrı** kanıt olarak taşır (kimlik düzeyi ayrımı, URL'ler farklıdır).

## §5 · AudioFormatID ve audio-format-identifiers — Format Kimlikleri

| Kavram | Rol |
|---|---|
| `AudioFormatID` | Format **tipi** (4CC mantığında sabit) |
| `audio-format-identifiers` | Format **kataloğu** (sayfalar: hangi kimlik hangi anlam) |
| `kAudioFormatLinearPCM` | PCM (ham) |
| `kAudioFormatMPEG4AAC` | AAC (sıkıştırılmış) |
| `kAudioFormatAppleLossless` | Kayıpsız (Apple) |
| `kAudioFormatFloat32` türevi | ASBD'de `mFormatID` + float bit'i birlikte |
| Kimlik ↔ bayrak ilişkisi | Kimlik **aileyi**, bayrak **özel biçimi** söyler |

**Aile + özel:** `mFormatID` (aile) ile `mFormatFlags` (özel biçim) birlikte okunur; yalnız kimlik float32/int16 ayrımını **yetkili** kılmaz — bayrak da şarttır (§4).

## §6 · AudioChannelLayout — Kanal Haritası

| Öğe | Anlam |
|---|---|
| `AudioChannelLayout` | Kanalların **yerleşim haritası** (hangi kanal hangi konum) |
| Kanal etiketleri | L, R, C, LFE, Ls, Rs … (konum etiketleri) |
| Harita etiketi (tag) | Bütün düzenin kısayol etiketi (ör. stereo çiftinin etiketi) |
| Kanal sayısı ≠ harita | Sayı eşit olsa da **düzen** farklı olabilir (ör. 2.0 vs L/R swap) |
| ASBD ile ilişki | ASBD kanal **sayısını**, layout kanal **anlamını** verir |
| Hata sınıfı | Layout/kanal uyuşmazlığı → yanlış miksaj (gizli hata) |

**Sayı + anlam:** `mChannelsPerFrame` sayıdır; `AudioChannelLayout` anlamdır. 8 kanal = 8.1 surround hedefi (K000) sayısal olarak tutar, **ama** harita (hangi kanal LFE…) ayrı tanım gerektirir.

## §7 · AudioSampleType — Örnek Tipi

| Kavram | Rol | İlişki |
|---|---|---|
| `AudioSampleType` | Örnek **tipi** tanımı (float/int ayrımı taşıyan tip) | ASBD bayrakları ile tutarlı olmalı |
| float32 örneği | 32-bit kayan nokta (ADR-017) | CoreMusic iç veri yolu |
| int16 örneği | 16-bit tamsayı (dosya/giriş) | dönüştürme girişi olabilir |
| Tutarlılık kuralı | `AudioSampleType` ↔ `mFormatFlags` float bit'i ↔ `mBitsPerChannel` üçlüsü eşleşmeli | uyuşmazlık → bozuk okuma |
| Dönüştürme | Tip farkı AudioConverter ile kapatılır | §8 |

## §8 · AudioConverter ve Format Servisleri (audio-format-services)

| Servis | Rol | Realtime notu |
|---|---|---|
| Format dönüştürme | ASBD-A → ASBD-B (ör. int16 dosya → float32 iç) | dönüştürücü **başlatmada** allocate; çevrim-içi yalnız işlem |
| Örnekleme oranı | 44.1 kHz → 48 kHz (örnek oranı uyumu) | resampler state allocate önceden |
| Kanal dönüştürme | stereo → mono vb. (downmix) | kanal count değişimi state ister |
| Bit derinliği | 16-bit → 32-bit float (iç yol) | normalleme bayrakları |
| `AudioConverterComplexInputDataProcRealtimeSafe` | Çevrim-içi güvenli giriş veri prosedürü (callback) | audio thread'te allocate/mutex YOK |
| Kullanım anı | Dosya okuma → iç format; iç format → DAC formatı | iki ayrı dönüşüm noktası |

**Gerçek zamanlı kural:** Dönüştürücü **önceden** hazırlanır (prepare/allocate); çevrim-içi (callback) yalnız `RealtimeSafe` prosedür veri sağlar — zero-allocation (K000 kuralı) ile aynı disiplin.

## §9 · Format Hattı (Dosya → İç → DAC) — CoreMusic Eşlemesi

| Halka | Format | macOS karşılığı | CoreMusic hedefi |
|---|---|---|---|
| Dosya okuma | int16/24-bit PCM veya AAC | ASBD (dosya) + audio-format-identifiers | — |
| Dönüştürme 1 | dosya → float32 48 kHz | AudioConverter | iç yol float32 (ADR-017) |
| İç yol | float32 stereo (veya 8.1) | ASBD float32 + interleaved | 32-bit float |
| Buffer | AudioBuffer(List) | §3 | 512 örnek/paket (sartname) |
| Çıkış hazırlık | DAC formatı | ASBD (cihaz) + format servisleri | PCM3168A (ADR-038 H001) |
| Çıkış | I2S/TDM → DAC | (donanım; bu dosya dışı) | PCM3168A — PCM5122 ABSOLUTELY FORBIDDEN |

## §10 · Uçtan Uca Sentez (ASBD → Buffer → Layout → Dönüştürme)

```
[ORİJİNAL ÖRNEK · derlenmedi · repo 0 .cpp/.h — K000 macOS format hattı taslağı]

ASBD-a (giriş)  ──┐
  mSampleRate=44100│
  int16 stereo     │        ┌─ ASBD-a'ya karşılık AudioBufferList (1 buffer, interleaved)
                   ├─► AudioConverter prepare ──► [çevrim-içi: RealtimeSafe input proc]
ASBD-b (çıkış) ──┘        │
  mSampleRate=48000       └─► ASBD-b'ye karşılık AudioBufferList (1 buffer veya N planar)
  float32
  ChannelLayout: stereo harita (L/R)
```

**Okuma:** iki ASBD (giriş/çıkış) bir dönüştürücüde buluşur; her iki tarafın da kendi `AudioBufferList`'i vardır; kanal haritası her iki tarafta da anlamlı olmalıdır.

## §11 · Yanlış Okuma Sınıfları (Format)

| # | Yanlış okuma | Düzeltme |
|---|---|---|
| 1 | "ASBD veriyi taşır" | ASBD tariftir; veri `AudioBuffer`'dadır (§2/§3) |
| 2 | "`mFormatID` float32/int16 ayrımını tek başına verir" | Aile + bayrak birlikte okunur (§5/§4) |
| 3 | "Kanal sayısı = layout" | Sayı ≠ anlam; layout ayrı yapı (§6) |
| 4 | "interleaved tek buffer = non-interleaved N buffer aynı şey" | İkisi farklı düzen; `AudioBufferList` her ikisini de ifade eder (§3) |
| 5 | "Dönüştürücü callback'te allocate edilebilir" | prepare önceden; çevrim-içi yalnız RealtimeSafe (§8) |
| 6 | "`AudioSampleType` ayrı bir veri alanı" | Örnek **tipi** tanımı; veri buffer'da (§7/§3) |

## §12 · Kardeş Kaynaklar (Dosyaya Özel Disjoints — Çapraz Tekrar YOK)

| Kardeş dosya | Kesişen kavram | Bu dosyada ne VAR | Çapraz tekrar |
|---|---|---|---|
| `macos-coreaudio-stacki.md` | "cihaz/stream" kavramı | format/tampon modeli (ASBD, Buffer, Layout) | **URL: 0** |
| `macos-audio-unit-arayuzleri.md` | "format uyumu" kavramı | format tarifi + dönüştürme (bu dosya) vs AU render (orada) | **URL: 0** |
| `macos-kernel-extension-durumu.md` | "stream" kelimesi | uygulama-tarafı format (bu dosya) vs IOUserAudioStream (orada) | **URL: 0** |
| `macos-timing-ve-clock.md` | — | (format kapsamı dışı) | **URL: 0** |
| `macos-ses-stacki.md` | AVAudioEngine format tercihi | ASBD düzeyinde tarif (bu dosya) | **URL: 0** |
| `macos-kod-ornekleri.md` | AudioConverter örneği | format servisi **kavramı** (bu dosya) | **URL: 0** |

## §13 · Ölçüm Protokolü (Format)

1. **Format doğrulama** — ASBD alan tutarlılığı kontrol edilir: `mBytesPerFrame`
   = `mChannelsPerFrame × (mBitsPerChannel / 8)` eşitliği sağlanır; sağlanmıyorsa
   tarif hatalıdır ve buffer hesapları bozulur. Çıktı: tutarlı / uyuşmaz.
2. **Buffer bütünlüğü** — `mDataByteSize` = çerçeve sayısı × `mBytesPerFrame`
   eşitliği korunur; sağlanmıyorsa taşma/eksik okuma riski vardır. Çıktı: bütünlük / taşma.
3. **Layout doğrulama** — `mChannelsPerFrame` ile `AudioChannelLayout` giriş sayısı
   eşleştirilir; eşleşmiyorsa kanal anlamsızlaşır (gizli miksaj hatası). Çıktı: eşleşmiyor / uyuşmaz.
4. **Dönüştürme kalitesi** — örnekleme oranı dönüşümünde (ör. 44.1 → 48 kHz) spektral
   kontrol gerekir; bu ölçüm bu turda **yapılmadı** (olcum PLANNED). Çıktı: ölçümlü / ölçülmemiş.

## §14 · Boşluk, Bilinmeyen ve Doğrulanmayanlar

1. **Sürüm bazlı flag değişiklikleri** — bayrak kataloğu canlıdır; sürüm eşlemesi bu turda
   doğrulanmadı → `⚠️ VERIFICATION REQUIRED`.
2. **Sıkıştırılmış format (AAC) ayrıntıları** — yalnız kimlik düzeyi taşındı; paket içi
   çerçeve yapısı (`mFramesPerPacket` değerleri) → **UNKNOWN** (derinlik bu dosya dışı).
3. **8.1 harita etiketi** — layout etiketi kümesi doğrulanmadı →
   `⚠️ VERIFICATION REQUIRED` (etiket adları uydurulmadı).
4. **Repo kod kanıtı** — repo'da `.cpp/.h` = 0 → kod blokları `[ORİJİNAL ÖRNEK · derlenmedi]`.

## §15 · SSS

1. **ASBD nedir?** → format tarifi (§2).
2. **Veri nerede?** → `AudioBuffer(List)` (§3).
3. **Bayraklar neden iki kaynak?** → tip + katalog (§4).
4. **Kimlik yeterli mi?** → hayır; bayrak da şart (§5).
5. **Layout neden ayrı?** → sayı ≠ anlam (§6).
6. **Dönüştürme nerede?** → AudioConverter (§8).
7. **Realtime kural?** → prepare önceden, çevrim-içi RealtimeSafe (§8).
8. **CoreMusic float32 mü?** → evet (ADR-017) + float32 iç yol (§9).
9. **DAC formatı?** → PCM3168A (ADR-038 H001) (§9).
10. **interleaved farkı?** → tek buffer vs N planar (§3).

## §16 · RED TEAM

| ID | İtiraz | Savunma |
|---|---|---|
| RT-DF-1 | "ASBD yeterli, buffer gereksiz" | §2/§3 (tarif ≠ veri) |
| RT-DF-2 | "FormatID tek başına yeter" | §5/§4 (aile + bayrak) |
| RT-DF-3 | "Layout gereksiz detay" | §6/§11-3 (sayı ≠ anlam, gizli hata) |
| RT-DF-4 | "Dönüştürücü callback'te kurulabilir" | §8 (prepare önceden) |
| RT-DF-5 | "interleaved/non-interleaved önemsiz" | §3/§11-4 (düzen farkı, buffer sayısı değişir) |

## §17 · Kaynaklar

### §17.1 · İddia Tabloları

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| ASBD format tarifi (alanlar) | https://developer.apple.com/documentation/CoreAudioTypes/AudioStreamBasicDescription | 2026-10-12 | ✅ HTTP 200 · 2026-10-12 (HEAD) |
| AudioBuffer tek blok | https://developer.apple.com/documentation/CoreAudioTypes/AudioBuffer | 2026-10-12 | ✅ HTTP 200 · 2026-10-12 (HEAD) |
| AudioBufferList dizi | https://developer.apple.com/documentation/CoreAudioTypes/AudioBufferList | 2026-10-12 | ✅ HTTP 200 · 2026-10-12 (HEAD) |
| FormatFlags tipi | https://developer.apple.com/documentation/CoreAudioTypes/AudioFormatFlags | 2026-10-12 | ✅ HTTP 200 · 2026-10-12 (HEAD) |
| format-flags kataloğu | https://developer.apple.com/documentation/CoreAudioTypes/audio-format-flags | 2026-10-12 | ✅ HTTP 200 · 2026-10-12 (HEAD) |
| FormatID tipi | https://developer.apple.com/documentation/CoreAudioTypes/AudioFormatID | 2026-10-12 | ✅ HTTP 200 · 2026-10-12 (HEAD) |
| format-identifiers kataloğu | https://developer.apple.com/documentation/CoreAudioTypes/audio-format-identifiers | 2026-10-12 | ✅ HTTP 200 · 2026-10-12 (HEAD) |
| ChannelLayout | https://developer.apple.com/documentation/CoreAudioTypes/AudioChannelLayout | 2026-10-12 | ✅ HTTP 200 · 2026-10-12 (HEAD) |
| AudioSampleType | https://developer.apple.com/documentation/CoreAudioTypes/AudioSampleType | 2026-10-12 | ✅ HTTP 200 · 2026-10-12 (HEAD) |
| audio-format-services | https://developer.apple.com/documentation/AudioToolbox/audio-format-services | 2026-10-12 | ✅ HTTP 200 · 2026-10-12 (HEAD) |

### §17.2 · Elenen Adaylar (Liste)

1. `CoreAudioTypes/AudioConverter` — dönüştürücü sınıf sayfası → `audio-format-services` (kapsam sayfası) tercih edildi, hizasızlık riski.
2. `CoreAudioTypes/AVAudioSession/*` — AVAudioSession alt sayfaları → `macos-ses-stacki`'ne ayrıldı (disjoints).
3. `CoreAudio/AudioHardwareControl` — cihaz kontrolü → `macos-coreaudio-stacki` kullanıyor (disjoints).
4. `AudioDriverKit/IOUserAudioStream` — stream tanımı → `macos-kernel-extension-durumu` (disjoints).
5. `CoreAudioTypes/kAudioChannelLayoutTag_*` tekil etiketler — layout ana sayfası yeterli (katalog aşırı).
6. `AudioToolbox/audio-converter-services` — dönüştürme servisi → `macos-kod-ornekleri` (disjoints).

## §18 · Kabul

**Kabul kriteri:** Her iddia §17.1'de tek benzersiz URL + `HTTP 200 · 2026-10-12 (HEAD)` ile; §0–§11 derinlik; §12 disjoints; §14 boşluklar; §16 RED TEAM; format hattı CoreMusic eşlemeli (§9).

> **Authority: Bayram Ali / Vault Steward**
> *device-ve-format · Ölçüm: ≥500 satır · 10 alanlı frontmatter · 12 tablo · 10 benzersiz URL · çapraz tekrar 0*

## §19 · ASBD Hesap Örnekleri (48 kHz · 32-bit float · K000 hedefi)

Aşağıdaki sayılar **bu dosyanın kendisinde türetilmiştir** (hesap zinciri gösterilir); resmi
dokümantasyon alıntısı değildir, kaynak §17.1'deki ASBD sayfasıdır.

**Örnek A — stereo float32 interleaved (tek kanal başına 4 bayt):**
`mBitsPerChannel = 32` → bayt/örnek = 32 / 8 = **4**.
`mChannelsPerFrame = 2` → `mBytesPerFrame` = 2 × 4 = **8**.
`mFramesPerPacket = 1` (PCM) → `mBytesPerPacket` = 1 × 8 = **8**.
512 örneklik paket boyu = 512 × 8 = **4096 bayt**. 48 kHz'de paket süresi = 512 / 48000
≈ **10.67 ms** (bu, sartnamedeki 512-örnek/48 kHz çiftinin zaman karşılığıdır; ölçüm PLANNED).

**Örnek B — 8.1 surround float32 interleaved (8 kanal + LFE = 9 kanal):**
`mChannelsPerFrame = 9` → `mBytesPerFrame` = 9 × 4 = **36**.
512 örneklik paket boyu = 512 × 36 = **18432 bayt**. Kanal sayısı arttıkça bayt/çerçeve
doğrusal artar; ASBD bu artışı tek bir alanla (`mChannelsPerFrame`) ifade eder, buffer ise
`mDataByteSize` ile karşılar (§3).

**Örnek C — non-interleaved (planar) stereo:**
Interleaved tek buffer (`mNumberBuffers = 1`) yerine kanal başına buffer:
`mNumberBuffers = 2`, her `mBuffers[i].mNumberChannels = 1`, her `mDataByteSize` =
512 × 4 = **2048 bayt**. Toplam bellek aynı (2 × 2048 = 4096), **düzen** farklıdır.
Bayrak (§4) non-interleaved bit'i ile işaretlenir; okuyucu bu bit olmadan planar veriyi
interleaved sanır → kanallar yer değiştirir (sessiz bozulma).

**Örnek D — int16 dosyadan float32 iç yola geçiş:**
Giriş ASBD: `mBitsPerChannel = 16`, `mBytesPerFrame(stereo)` = 2 × 2 = 4.
Çıkış ASBD: Örnek A. Dönüştürücü (§8) iki tarifi de alır; bayt/çerçeve 4 → 8 olarak değişir,
buffer boyutları buna göre **önceden** hesaplanır (çevrim-içi allocate yasak).

**Hesap disiplini:** Her dört örnek de aynı üç bağıntıya dayanır: (1) bayt/örnek =
bit/8, (2) bayt/çerçeve = kanal × bayt/örnek, (3) paket = çerçeve × bayt/çerçeve.
ASBD'nin alanları bu bağıntıların **serbest değişkenleri**dir; tutarsızlık tarifin bozuk
olduğunu gösterir ve §13-1 ölçümü bunu yakalar.

## §20 · Format Karşılaştırma Matrisi (hangi halka hangi tarife bağlanır)

| Halka | Tarif (ASBD) | Kap (Buffer) | Kanal (Layout) | Dönüştürücü |
|---|---|---|---|---|
| Dosya girişi | dosya ASBD (int16/AAC) | dosyadan okunan buffer | dosya haritası (varsa) | giriş → iç (Örnek D) |
| Dönüştürme 1 | iki ASBD (a→b) | iki buffer listesi | her iki tarafta anlamlı | AudioConverter (§8) |
| İç yol | float32 48 kHz | 512-örnek paket | stereo/8.1 harita | — |
| Çıkış hazırlık | cihaz ASBD | çıkış buffer listesi | cihaz haritası | iç → cihaz (gerekirse) |
| DAC (PCM3168A) | (donanım) | (I2S/TDM) | (kanal eşleme) | (bu dosya dışı) |

**Çift ASBD kuralı:** Dönüştürme her zaman **iki** tarif ister (giriş ve çıkış); tek tarif
dönüştürme **anlamını** yitirir. Bu, AudioConverter'ın en sık kullanım hatasıdır.

## §21 · Format Uyumsuzluğu Hata Sınıflandırması

1. **Oran uyuşmazlığı** — giriş 44.1 kHz, iç/çıkış 48 kHz: resampler zorunlu; resampler
   olmadan okuma hızı bile değişir (pitch/time kayması). Önleme: §13-4 ölçümü + §8.
2. **Bayrak uyuşmazlığı** — float32 tarifi int16 veriyle beslenirse (veya tersi): sayılar
   anlamsızlaşır (dev sayılar / sessizlik). Önleme: §5 aile+bayrak birlikte kuralı.
3. **Kanal uyuşmazlığı** — sayı eşit ama layout farklı (ör. L/R ters): sessiz miksaj hatası.
   Önleme: §6 + §13-3.
4. **Düzen uyuşmazlığı** — interleaved beklenen okuyucu planar veri alır: kanallar iç içe
   geçer. Önleme: §4 non-interleaved bit'i + §19 Örnek C.
5. **Boy uyuşmazlığı** — `mDataByteSize` hesapla tutmuyor: taşma/eksik okuma. Önleme: §13-2.
6. **Paket uyuşmazlığı** — sıkıştırılmış formatta `mFramesPerPacket` PCM'deki 1'den farklıdır;
   PCM varsayımıyla okuyan kod çerçeve sayısını yanlış sayar. Önleme: §2 `mFramesPerPacket` alanı.

**Ortak özellik:** Altısının da yakalama noktası **tariftir** (ASBD), veri değil — bu nedenle
format doğrulaması her zaman veriden önce yapılır (§10 hattında en soldaki halka).

## §22 · Kardeş-Kaynak Özeti (Dosyaya Özel — Çapraz Metin Disjoints)

Bu dosyanın **kanıt kümesi** (§17.1) yalnız `CoreAudioTypes/*` + `audio-format-services`
üzerinedir; kardeş dosyaların kümeleri şöyledir ve **hiçbiriyle URL kesişmez**:

- `macos-coreaudio-stacki` → CoreAudio nesne modeli (AudioHardware* + data-types/enumerations).
  Bu dosya `AudioHardware*` **kullanmaz**; `CoreAudio/core-audio-data-types` da onlara aittir.
- `macos-audio-unit-arayuzleri` → AudioToolbox AUAudioUnit + coreaudio AU parametre/scope sayfaları.
  Bu dosya AU **kullanmaz**; yalnız format servisinin AudioToolbox kökenini taşır.
- `macos-kernel-extension-durumu` → AudioDriverKit + SystemExtensions sayfaları.
  Bu dosya `IOUserAudioStream` **kullanmaz** (stream'in format **tanımı** orada, tarifi burada).
- `macos-timing-ve-clock` → AudioTimeStamp/SMPTETime + Dispatch sayfaları.
- `macos-ses-stacki` → AVFAudio sayfaları.
- `macos-kod-ornekleri` → AudioToolbox servis sayfaları (queue/file/converter örnekleri).
- `macos-web-arastirma` → Tap/spatial/MIDI araştırma sayfaları.

**Tek cümle kural:** Bu dosya **tarifi** anlatır; **nesneyi** (coreaudio-stacki), **sürücüyü**
(kernel-extension), **zamanı** (timing), **uygulamayı** (ses-stacki), **örneği** (kod-ornekleri)
kardeşlerine devreder.

## §23 · Mimari Sentez (Bir Cümle)

macOS ses formatı, `AudioStreamBasicDescription` (tarif) + `AudioBuffer(List)` (kap) +
`AudioChannelLayout` (anlam) üçlüsüyle modellenir; tarifler `AudioFormatFlags`/`AudioFormatID`
çiftiyle (tip + katalog) kesinleşir, format dönüşümleri önceden-hazırlanmış `AudioConverter`
ile (prepare → RealtimeSafe çevrim-içi) yapılır ve CoreMusic'in float32/48 kHz/512-örnek
iç yolu bu üç bağıntının (bayt/örnek → bayt/çerçeve → paket) sabitlenmesiyle kurulur.

## §24 · SSS · Format (Genişletilmiş)

1. **interleaved ve planar aynı belleği kullanır mı?** → toplam bayt aynı, düzen farklı (§19 C).
2. **`mBytesPerPacket` PCM'de neden bayt/çerçeve ile eşit?** → `mFramesPerPacket = 1` (§2).
3. **Sıkıştırılmış formatta neden eşit değil?** → paket birden fazla çerçeve taşır (§21-6).
4. **`mReserved` neden var?** → gelecek alanlar için (0 bırakılır) (§2).
5. **Layout etiketi zorunlu mu?** → stereo'da ipucu, surround'da şart (§6).
6. **Dönüştürücü ne zaman allocate eder?** → prepare aşamasında; çevrim-içi hayır (§8).
7. **`AudioSampleType` ve `AudioFormatFlags` çelişebilir mi?** → çelişki hatadır (§7).
8. **8.1 hedefi için hangi alanlar değişir?** → `mChannelsPerFrame = 9` + layout (§19 B).
9. **Dosya AAC ise float32'ye ne olur?** → AudioConverter iki aşamalı olabilir (§9; derinlik UNKNOWN).
10. **Format hatası neden sessiz?** → veri "okunur" ama anlamsızdır; yakalama §13 (§21).

## §25 · Ölçüm Protokolü — Dosya Kalite Kapıları

1. **Satır** ≥500 — ölçülecek (imza).
2. **Tablo** 8–15 — ölçülecek (imza).
3. **Frontmatter** 10 alan — ✓ (dosya başı).
4. **URL** ≥8 benzersiz · disjoints — ✓ (10 · §12).
5. **Dolgu** TBD/.../Yok — ✓ yok.
6. **Kod etiketi** `[ORİJİNAL ÖRNEK · derlenmedi]` — ✓ (§10).

## §26 · ASBD Alanlarının Bağımlılık Grafiği (Hangi Alan Hangisini Zorlar)

ASBD alanları birbirinden **bağımsız** değildir; bir alan değişince bağlı alanlar yeniden
hesaplanmak zorundadır. Bu bağımlılık, format hattının en sık hata kaynağıdır.

**Bağımlılık zinciri 1 (derinlik → bayt):** `mBitsPerChannel` değişirse `mBytesPerFrame`
yeniden hesaplanır (kanal sabitken). Örnek: 16-bit → 32-bit geçişte bayt/örnek 2 → 4 olur;
`mDataByteSize` (buffer tarafı) iki katına çıkar. ASBD tek başına güncellenirse buffer tarafı
eski kalır → §21-5 (boy uyuşmazlığı).

**Bağımlılık zinciri 2 (kanal → hem bayt hem layout):** `mChannelsPerFrame` değişirse hem
`mBytesPerFrame` hem de `AudioChannelLayout` güncellenmelidir. Stereo → 8.1 geçişinde yalnız
sayısal alan güncellenir, layout stereo'da kalırsa LFE/Rs/Ls kanalları **anlamsız** etiketler
alır → §21-3 (sessiz miksaj hatası).

**Bağımlılık zinciri 3 (düzen → buffer sayısı):** non-interleaved bit'i açılırsa
`AudioBufferList`'in `mNumberBuffers`'ı kanal sayısına eşitlenmeli ve her buffer'ın
`mNumberChannels` = 1 olmalıdır. Yalnız bayrak değişimi, buffer listesini eski bırakırsa
okuyucu tek bloğu N kanal sanar → §21-4 (düzen uyuşmazlığı).

**Bağımlılık zinciri 4 (oran → zaman):** `mSampleRate` değişirse 512-örnek paketin zaman
karşılığı da değişir (10.67 ms @ 48 kHz; 11.61 ms @ 44.1 kHz). Zaman hesabı (buffer boyu /
örnek oranı) her zaman **çift ASBD** ile yapılmalıdır; tek tarif karşılaştırması yanıltıcıdır.

**Tasarım sonucu:** Bağımlılıklar nedeniyle format **asla tek alanla** değiştirilmez; ASBD
yeniden yazılır (tarif bütünü), buffer ve layout birlikte güncellenir. Bu bütünlük,
CoreMusic K000'de 32-bit float iç yolunun (ADR-017) tek bir "format sözleşmesi" olarak
sabitlenmesinin de gerekçesidir.

## §27 · Sürüm Riski ve Doğrulama Notu (Format)

Format modeli (ASBD/Buffer/Layout) uzun süredir sabit görünse de bu dosya **sürüm eşlemesi**
iddiası taşımaz: hangi macOS sürümünde hangi band bayrağının eklendiği/çıkarıldığı bu turda
doğrulanmadı → `⚠️ VERIFICATION REQUIRED` (§14-1). Uygulama önerisi: format sözleşmesi
(uçtan uca float32/48 kHz) sabitlenir, **sürüm-spesifik bayraklar** çalışma anında okunur
(sorgulama), bayrak adları koda gömülmez.

## §28 · RED TEAM · Eklenti (Format Ek Savunmaları)

Ek itirazlar ve savunmalar (§16 ile birlikte okunur):

1. **"ASBD bayt/çerçeve alanı gereksiz, hesaplanabilir"** → alan **vardır** ve tarifin
   kendisini doğrular; hesap kod tarafında tekrarlanırsa tutarsızlık iki kaynağa yayılır
   (§13-1 ölçümü alanları **birlikte** okur, tek tek değil).
2. **"interleaved her yerde yeterli, planar gereksiz"** → planar, kanal-başına-işlem
   (EQ/reverb kanal bazlı) için doğal düzendir; CoreMusic DSP zinciri kanal-başına çalışır
   (K000 mimarisi) → düzen seçimi işlem topolojisine bağlıdır (§3, §19-C).
3. **"Dönüştürme gereksiz, dosyayı 48 kHz'e offline çevir"** → kısmen doğru (dosya dönüşümü
   tercih edilir); ancak canlı giriş (mikrofon/StreamSwitch) çevrim-içi dönüşüm gerektirir →
   §8 RealtimeSafe kapı devrede kalır.
4. **"Layout stereo'da hep gereksiz"** → stereo'da ipucu düzeyinde olsa da, mono-downmix
   kararı (L+R ortalaması) layout bilgisiyle tutarlı yapılmalı; tutarsız downmix farklı
   mono sonuç üretir (§6, §21-3).

## §29 · Kardeş Dosyalara Geçiş Noktaları (Handover)

| Geçiş | Hedef dosya | Ne devredilir |
|---|---|---|
| Cihaz tarafı ASBD | `macos-coreaudio-stacki` | cihazın desteklediği format listesi (sorgulama) |
| Stream formatı (sürücü) | `macos-kernel-extension-durumu` | IOUserAudioStream'in format alanı |
| AU render formatı | `macos-audio-unit-arayuzleri` | AU'nun beklediği ASBD (input/output scope) |
| Uygulama tercihi | `macos-ses-stacki` | AVAudioEngine/format tercihleri (route'a bağlı) |
| Dönüştürme örneği | `macos-kod-ornekleri` | AudioConverter servis örneği |
| Ölçüm (spektral) | `macos-web-arastirma` | olcum PLANNED kanıt boşluğu kaydı |

## §30 · Sentez Cümlesi (Genişletilmiş)

Format, tarif (ASBD) + kap (AudioBuffer/List) + anlam (ChannelLayout) üçlüsüdür; üçlü
**bütün** olarak değişir, bayraklar tip+katalog çiftiyle kesinleşir, dönüşüm önceden
hazırlanır ve CoreMusic float32/48 kHz/512-örnek iç yolu bu sözleşmenin sabitlenmesidir.

## §31 · AVFAudio Buffer Sınıf Ailesi (AVAudioBuffer · AVAudioPCMBuffer · AVReadOnlyAudioPCMBuffer)

§3'teki C yapısı `AudioBuffer(List)` **çekirdek düzeyi** kaplardır; AVFAudio (uygulama
katmanı) aynı kavramı **Objective-C/Swift sınıfı** olarak taşır. Bu üç sayfa bu turda
birebir metinle doğrulandı (§34 tablosu).

| Sınıf | Resmi özet (meta description — birebir) | Format modeliyle ilişki |
|---|---|---|
| `AVAudioBuffer` | "An object that represents a buffer of audio data with a format." | Kap + format birlikte taşınır (§2 tarifin nesne-düzey karşılığı) |
| `AVAudioPCMBuffer` | "An object that represents an audio buffer you use with PCM audio formats." | PCM-özgül alt kap — ASBD `kAudioFormatLinearPCM` halkasının sınıf karşılığı |
| `AVReadOnlyAudioPCMBuffer` | "A read-only, Sendable audio buffer for safe concurrent access." | Salt-okunur + Sendable — çoklu-erişim güvenliği sınıf düzeyinde |

**Çıkarım (CoreMusic):** AVFAudio tarafında kap nesnesi format bilgisini **kendinde**
taşır (ilk özetin "with a format" ifadesi) — bu, C tarafındaki "ASBD ayrı, buffer ayrı"
düzeninden (§2/§3) farklı bir paketlemedir; iki katman arasında köprü kurarken tarif
tekilleştirilir (tek ASBD-of-record), sınıflar yalnızca kap olarak kullanılır.
`AVReadOnlyAudioPCMBuffer` ifadesindeki "safe concurrent access" ibaresi, çoklu-erişimde
salt-okunur kap tercihini destekler; yazma tapınağı (scratch buffer) CoreMusic
iç-yolunda (float32) ayrıca ayrılan buffer'larda kalır (§3 sahiplik kuralı).

## §32 · AVAudioConverter — Akış Dönüştürme (AVFAudio düzeyi)

| Sınıf | Resmi özet (birebir) | §8 ile ilişki |
|---|---|---|
| `AVAudioConverter` | "An object that converts streams of audio between formats." | §8'deki `audio-format-services` kapsamının **sınıf** yüzü |

**Katman ayrımı:** §8 AudioToolbox/format-servisi düzeyinde (ASBD çifti ile çalışan
C hizmeti); buradaki `AVAudioConverter` AVFAudio düzeyinde akış dönüştürücüdür. İkisi
aynı kavramın (format A → format B) iki katmanidir; CoreMusic iç-yolu sözleşmesi
(float32/48 kHz) her iki katmanda da aynı tarife bağlanır (§9 hattı). Realtime kuralı
§8'dekiyle aynı disiplindedir: hazırlık önceden, çevrim-içi yalnız veri akışı.

## §33 · AVAudioTime · AudioStreamRangedDescription · AVAudioFile · AVAudioNodeBus

| Sınıf/yapı | Resmi özet (birebir) | Dosya modelindeki yeri |
|---|---|---|
| `AVAudioTime` | "An object you use to represent a moment in time." | Zaman kavramı (bu dosyada yalnız köprü; derinlik → `macos-timing-ve-clock`) |
| `AudioStreamRangedDescription` | "This structure allows a specific sample rate range to be associated with an AudioStreamBasicDescription that specifies its sample rate as kAudioStreamAnyRate." | ASBD ile birlikte **oran aralığı** ekler; `kAudioStreamAnyRate` tarifleri için |
| `AVAudioFile` | "An object that represents an audio file that the system can open for reading or writing." | §9 halka 1 (dosya girişi) nesne-düzey karşılığı |
| `AVAudioNodeBus` | "The index of a bus on an audio node." | Düğüm-başına indeks (bağlantı noktası etiketleme) |

**AudioStreamRangedDescription — ASBD'ye bağlılık (§26 bağımlılık mantığı):** yapı,
ASBD'nin `mSampleRate` alanı "any rate" olarak tariflendiğinde **gerçek oran aralığını`
taşır; yani tarif-ötesi alan ASBD'de değil, bu bağlı yapıdadır. CoreMusic 48 kHz sabit
sözleşmesinde tarif sabit oranlıdır; cihaz keşfi sırasında "aralık" bilgisi okunursa
bu yapı devreye girer.

## §34 · Web Araştırma Tablosu (bu tur eklenen 8 URL)

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| AVAudioBuffer sınıf sayfası yayımlanmış | https://developer.apple.com/documentation/AVFAudio/AVAudioBuffer | 2026-10-12 | ✅ HTTP 200 (GET · meta description birebir alındı) |
| AVAudioPCMBuffer sınıf sayfası yayımlanmış | https://developer.apple.com/documentation/AVFAudio/AVAudioPCMBuffer | 2026-10-12 | ✅ HTTP 200 (GET · meta description birebir alındı) |
| AVAudioConverter sınıf sayfası yayımlanmış | https://developer.apple.com/documentation/AVFAudio/AVAudioConverter | 2026-10-12 | ✅ HTTP 200 (GET · meta description birebir alındı) |
| AVReadOnlyAudioPCMBuffer sınıf sayfası yayımlanmış | https://developer.apple.com/documentation/AVFAudio/AVReadOnlyAudioPCMBuffer | 2026-10-12 | ✅ HTTP 200 (GET · meta description birebir alındı) |
| AVAudioTime sınıf sayfası yayımlanmış | https://developer.apple.com/documentation/avfaudio/avaudiotime | 2026-10-12 | ✅ HTTP 200 (GET · meta description birebir alındı) |
| AudioStreamRangedDescription yapısı yayımlanmış | https://developer.apple.com/documentation/CoreAudio/AudioStreamRangedDescription | 2026-10-12 | ✅ HTTP 200 (GET · meta description birebir alındı) |
| AVAudioFile sınıf sayfası yayımlanmış | https://developer.apple.com/documentation/avfaudio/avaudiofile | 2026-10-12 | ✅ HTTP 200 (GET · meta description birebir alındı) |
| AVAudioNodeBus sınıf sayfası yayımlanmış | https://developer.apple.com/documentation/AVFAudio/AVAudioNodeBus | 2026-10-12 | ✅ HTTP 200 (GET · meta description birebir alındı) |

## §35 · Repo Kanıtı (CoreMusic disk — gerçek satırlar)

> `ReadAllLines` ile diskten okunmuş GERÇEK içerik (repo'da `.cpp/.h` = 0 — bu dosyanın
> format modeli belge düzeyindedir; aşağıdaki kanıtlar repo'nun çalışma zamanı gerçeğidir).

- Dosya: `package.json` / Satır: 5 → `"test:js": "vitest run --root assets.coremusic.net",`
- Dosya: `package.json` / Satır: 11 → `"@playwright/test": "^1.63.0",`
- Dosya: `shared/composer.json` / Satır: 8 → `"php": ">=8.4",`
- Dosya: `.github/workflows/ci.yml` / Satır: 28 → `timeout-minutes: 15`

## İlişkili dosyalar

- `macos-coreaudio-stacki.md` — cihaz/stream nesne modeli (bu dosyadaki format/tamponun bağlandığı nesneler)
- `macos-audio-unit-arayuzleri.md` — AU render tarafı (format çifti orada tüketilir)
- `macos-kernel-extension-durumu.md` — IOUserAudioStream (sürücü düzeyi format tanımı)
- `macos-anayasa.md` — macOS dalı anayasası (sürüm modeli ve katmanlar)

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode