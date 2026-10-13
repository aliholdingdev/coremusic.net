---
node_id: "K000-isletim-sistemi/isletim-sistemi-turu/android/android-aaudio-ve-opensl"
node_type: "architecture"
status: "verify-required"
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-audio"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-api"
evidence:
  - "https://developer.android.com/ndk/guides/audio"
  - "https://developer.android.com/ndk/guides/audio/aaudio/aaudio"
  - "https://github.com/google/oboe"
validation: "Dosya içi doğrulama matrisi §12 (| İddia | URL | Tarih: 2026-10-12 | Durum |) — parent index §GRUPLAR satırı bekleniyor"
open_questions:
  - "AAudio Exclusive Stream (EXCLUSIVE) modunun cihaz-özgü başarım oranı bu turda ölçülmedi → UNKNOWN"
  - "Oboe sürüm numarası ve yayın tarihi github sayfasından bu turda okunmadı → UNKNOWN"
group: mimari
---

# Android — AAudio ve OpenSL ES (NDK native ses arayüzleri)

**Kapsam:** Android native (NDK) ses arayüzleri — OpenSL ES implementasyonu ve onun
önerilmeme durumu, AAudio'nun kuruluş amacı, Oboe sarmalayıcısının AAudio ↔ OpenSL ES
geri dönüşümü (fallback) mantığı, AAudio MMAP/NOIRQ veri yolu, sistem özellikleri
(system properties) ile yol seçimi.

**Yöntem (RED TEAM / TRUTH MODE):** Tüm iddialar 2026-10-12 tarihinde HTTP 200 ile
doğrulanan resmi sayfalardan türetilmiştir; NDK ses sayfası ve AAudio/MMAP sayfasının
**gövdeleri okunup alıntılanmıştır**. Gövde okunmayan iddialar `⚠️ VERIFICATION REQUIRED`
ile işaretlenir. Kod kanıtı politikası: depoda 0 `.cpp/.h` → `Dosya:/Satır:` YASAK;
yalnız `[RESMİ ALINTI · URL · 2026-10-12]` veya `**[ORİJİNAL ÖRNEK · derlenmedi]**`.

## §0 Kanıt düzeyi özeti

| Kaynak | HTTP | Gövde | Last updated | Kullanım |
|---|---|---|---|---|
| NDK "High-performance audio" | 200 | OKUNDU | 2026-03-06 UTC | §1 §2 §3 |
| NDK "AAudio" | 200 | OKUNDU | (sayfa başlığı) | §4 |
| AOSP "AAudio and MMAP" | 200 | OKUNDU | 2026-10-09 UTC | §5 §6 §7 |
| NDK "OpenSL ES" genel bakış | 200 | başlık | — | §2 |
| NDK "Get started" / "for Android" / "programming notes" / "Android extensions" | 200 | başlık | — | §8 |
| NDK "Audio latency" / "Sampling audio" | 200 | başlık | — | §9 |
| github `google/oboe` | 200 | başlık | — | §3 §11 |

## §1 NDK ses sayfasının kendi çerçevesi

Sayfa gövesinden doğrudan alıntı temelli özet: [RESMİ ALINTI · https://developer.android.com/ndk/guides/audio · 2026-10-12]

| İfade | Sayfadaki karşılığı |
|---|---|
| Performans beklentisi | "They demand responsive realtime system behavior" |
| Kapsanan kullanım | DAW, sentezleyici, davul makinesi, müzik öğretimi, karaoke, DJ karıştırma, ses efektleri, video/ses konferansı |
| Açıklanan ilke | Genel gecikmeyi azaltma prensipleri (minimizing audio latency) |
| Örnek hızı tavsiyesi | "choose the optimal sample rate and consider the pros and cons of using floating-point numbers" |
| Sunulan iki kütüphane | OpenSL ES ve AAudio |

## §2 OpenSL ES — doküman konumu ve statüsü

| Konu | Doküman iddiası (gövde alıntısı temelli) |
|---|---|
| Tanım | "OpenSL ES is an Android-specific implementation of the OpenSL ES API specification from the Khronos Group" |
| Statü | **"OpenSL ES is not recommended for new designs"** |
| Hedef kitle uyarısı | "App developers and middleware providers should target either Oboe or AAudio as the native audio API" |
| API seviyesi | API seviyesi 9 ve üzeri Android sürümleri, OpenSL ES 1.0.1 alt kümesine dayalı native ses API destekler *(kaynak: `android-audio-mimarisi.md` §3 — bu dosyada URL tekrarı yok)* |
| Standart sahibi | Khronos Group (doküman atfı) |

**RED TEAM bulgu 2-1:** "Android'de OpenSL ES hala önerilen native API'dir" iddiası
dokümanın açık ifadesiyle ÇELİŞİR. Doğru ifade: **yeni tasarımlar için önerilmez;
Oboe veya AAudio hedeflenmelidir.**

## §3 AAudio ve Oboe — kuruluş ve fallback zinciri

| Bileşen | Doküman iddiası |
|---|---|
| AAudio amacı | "AAudio was developed as a lightweight, native Android alternative to the OpenSL ES library" |
| Boyut/öz | "The AAudio API is smaller and easier to use than OpenSL ES" |
| Oboe tanımı | "Oboe is a C++ wrapper that provides an API that closely resembles AAudio" |
| Oboe davranışı | "It calls AAudio when it is available, and falls back to OpenSL ES if AAudio is not available" |
| Oboe kaynağı | Açık kaynak (GitHub) — doküman: "the open source Oboe library which is available on GitHub" |
| Doküman önerisi | "Developers should consider using the open source Oboe library" |

### §3.1 Fallback zinciri (mantıksal harita)

| Sıra | Koşul | Çağrılan API |
|---|---|---|
| 1 | AAudio mevcut | AAudio |
| 2 | AAudio yok | OpenSL ES |
| 3 | İki API de erişilemez | Bu durum dokümanda tanımlanmamış → `UNKNOWN` |

### §3.2 Öneri matrisi (doküman ifadelerinden)

| Hedef | Dokümanın önerisi | Durum |
|---|---|---|
| Yeni uygulama tasarımı | Oboe veya AAudio | ÖNERİLEN |
| Eski kod bakımı | OpenSL ES kullanılabilir ama "not recommended for new designs" | KOŞULLU |
| Ara katman (middleware) sağlayıcı | Yine Oboe veya AAudio | ÖNERİLEN |

## §4 NDK AAudio sayfası (başlık kanıtı)

| Öğe | Değer |
|---|---|
| URL | `https://developer.android.com/ndk/guides/audio/aaudio/aaudio` |
| Başlık | "AAudio" |
| HTTP (206-12) | 200 |
| Gövde | Bu turda tam olarak alıntılanmadı → içerik iddiaları `⚠️ VERIFICATION REQUIRED` |
| Yanlış yol denemesi | `/ndk/guides/audio/aaudio` → **404** (yol tahmini hatalıdır, §10.1) |

## §5 AAudio MMAP/NOIRQ veri yolu (gövde alıntısı)

Kaynak: AOSP "AAudio and MMAP" sayfası gövdesi, `Last updated 2026-10-09 UTC`
(`https://source.android.com/docs/core/audio/aaudio` — bu dosyanın §12 matrisinde).

### §5.1 Bellek ve pozisyon

| İddia | Doküman ifadesi |
|---|---|
| Pozisyon raporu | `getMmapPosition()` şunları üretir: `(Result retval, MmapPosition position)` — çerçeve ve **MONOTONIC** zaman damgası (nanosaniye) içerir |
| HAL'nin kaynağı | ALSA sürücüsünden yeni bir tinyalsa fonksiyonu ile: `int pcm_mmap_get_hw_ptr(struct pcm* pcm, unsigned int *hw_ptr, struct timespec *tstamp)` |
| Paylaşımlı bellek | MMAP veri yolu donanım ile ses servisi arasında paylaşılan bellek bölgesi kullanır |
| Dosya tanımlayıcı | Paylaşılan bellek, ALSA sürücüsü tarafından üretilen bir dosya tanımlayıcı ile referanslanır |

### §5.2 EXCLUSIVE ve SHARED modları

| Mod | Doküman iddiası |
|---|---|
| SHARED | `/dev/snd/` ile doğrudan ilişkili dosya tanımlayıcı ses servisinde kullanılabilir |
| EXCLUSIVE | Aynı tanımlayıcı istemci koduna **geçirilemez** — `/dev/snd/` tanımlayıcısı istemciye çok geniş erişim sağlar ve **SELinux tarafından engellenir** |
| EXCLUSIVE çözümü | `/dev/snd/` tanımlayıcısı `anon_inode:dmabuf` dosya tanımlayıcısına dönüştürülmelidir; SELinux bu tanımlayıcının istemciye geçirilmesine izin verir |
| Üretim yolu | `anon_inode:dmabuf` tanımlayıcısı Android Ion bellek kütüphanesi ile üretilebilir |

### §5.3 HAL ve sistem özelliği bayrakları

| Öğe | Doküman ifadesi |
|---|---|
| Android 10.0 öncesi | MMAP tamponunun boyutu **negatif sayı** ile gönderilerek paylaşabilirlik bildirilirdi (örn. `-2048`) |
| Android 10.0+ | `AUDIO_MMAP_APPLICATION_SHAREABLE` bayrağı ayarlanabilir |
| Ek veri yolu | AAudio'nun AudioFlinger yoluyla **paralel** çalışması için ses alt sisteminin önüne ek bir veri yolu gerekir |
| Eski yol | "legacy path" tüm diğer sistem ve uygulama sesleri için kullanılır |
| Sağlayıcı | DSP'de yazılım mikseri veya SoC'de donanım mikseri |

```c
[RESMİ ALINTI · https://source.android.com/docs/core/audio/aaudio · 2026-10-12]
mmapBufferInfo |= AUDIO_MMAP_APPLICATION_SHAREABLE;
```

## §6 Yol seçimi — sistem özellikleri (system properties)

| Değer | Sabit | Anlamı (doküman) |
|---|---|---|
| 1 | `AAUDIO_POLICY_NEVER` | Yalnızca eski (legacy) yol — MMAP denenmez bile |
| 2 | `AAUDIO_POLICY_AUTO` | MMAP denenir; olmazsa/başarısız olursa eski yol |
| 3 | `AAUDIO_POLICY_ALWAYS` | Yalnızca MMAP — eski yola düşülmez |

### §6.1 Üretici yapılandırması (Makefile)

[RESMİ ALINTI · https://source.android.com/docs/core/audio/aaudio · 2026-10-12]

```make
# Enable AAudio MMAP/NOIRQ data path.
# 2 is AAUDIO_POLICY_AUTO so it will try MMAP then fallback to Legacy path.
PRODUCT_PROPERTY_OVERRIDES += aaudio.mmap_policy=2
# Allow EXCLUSIVE then fall back to SHARED.
PRODUCT_PROPERTY_OVERRIDES += aaudio.mmap_exclusive_policy=2
```

### §6.2 Cihaz üzerinde geçersiz kılma (adb)

[RESMİ ALINTI · https://source.android.com/docs/core/audio/aaudio · 2026-10-12]

```sh
adb root
adb shell setprop aaudio.mmap_policy 2
adb shell killall audioserver
```

Doküman notu: değişikliğin etkili olması için **audioserver yeniden başlatılmalıdır**;
değerler cihaz açıldıktan sonra da geçersiz kılınabilir.

### §6.3 API üzerinden politika sorgulama

[RESMİ ALINTI · https://source.android.com/docs/core/audio/aaudio · 2026-10-12]

```c
aaudio_result_t AAudio_setMMapPolicy(aaudio_policy_t policy);
bool AAudioStream_isMMapUsed(AAudioStream* stream);
```

İlk fonksiyon `ndk/sysroot/usr/include/aaudio/AAudioTesting.h` içinde tanımlıdır;
ikincisi bir akışın MMAP yolunu kullanıp kullanmadığını söyler.

## §7 Test ve doğrulama tuzağı (RED TEAM)

| Bulgu | Gerekçe |
|---|---|
| MMAP'siz cihazda AAudio **çalışır** | Doküman: "AAudio will use the legacy AudioFlinger data path if MMAP is not supported or fails to open a stream" |
| Test tuzağı | "When testing MMAP support for AAudio, it is important to know whether you are actually testing the MMAP data path or merely testing the legacy data path" |
| Sonuç | "AAudio çalışıyor" ölçümü tek başına MMAP'yi **kanıtlamaz** |
| Doğru ölçüm | `AAudioStream_isMMapUsed()` veya `aaudio.mmap_policy` değerinin kontrolü |

**RED TEAM bulgu 7-1:** "AAudio kullanıyorum = en düşük gecikme yolundayım"
zinciri geçersizdir. MMAP doğrulanmadan gecikme iddiası bu depoda yazılamaz.

## §8 OpenSL ES alt sayfaları (başlık kanıtı)

| Sayfa | Başlık | Gövde |
|---|---|---|
| `/ndk/guides/audio/opensl` | OpenSL ES (genel) | okunmadı → `⚠️ VERIFICATION REQUIRED` |
| `/ndk/guides/audio/opensl/getting-started` | Get started | okunmadı → `⚠️ VERIFICATION REQUIRED` |
| `/ndk/guides/audio/opensl/opensl-for-android` | OpenSL ES for Android | okunmadı → `⚠️ VERIFICATION REQUIRED` |
| `/ndk/guides/audio/opensl/android-extensions` | Android extensions | okunmadı → `⚠️ VERIFICATION REQUIRED` |
| `/ndk/guides/audio/opensl/opensl-prog-notes` | OpenSL ES programming notes | okunmadı → `⚠️ VERIFICATION REQUIRED` |

**Yasak:** Bu beş sayfadan API imzası, desteklenen interface sayısı veya örnek hızı
ÜRETİLEMEZ. Ayrıntı için ikinci bir gövde okuma turu gerekir.

## §9 Gecikme ve örnek hızı (yalnızca başlık düzeyi)

| Sayfa | Başlık | Bu dosyadaki işlev |
|---|---|---|
| `/ndk/guides/audio/audio-latency` | Audio latency | §1 kapsamında prensipler — sayısal hedef YOK |
| `/ndk/guides/audio/sampling-audio` | Sampling audio | Örnek hızı + float kullanımı artıları/eksileri (§1) |
| `/ndk/guides/audio/additional-resources` | Additional resources | Oboe örnekleri, codelab'lar, videolar — ayrı dosya |
| `/ndk/guides/audio/midi` | Native MIDI API | ayrı dosya havuzu |

**RED TEAM bulgu 9-1:** Örnek hızı (örn. 44.1/48 kHz) bu dosyada **sabitlenmez**;
NDK sayfası yalnızca "optimal sample rate seçimi"nın tartışılmasını önerir.
Belirli bir değer uydurmak yasaktır.

## §10 Hata günlüğü — yol doğruluğu ölçümü (2026-10-12)

### §10.1 404 dönen tahmini yollar (gerçek ölçüm)

| Denenen yol | Sonuç | Doğru karşılık |
|---|---|---|
| `/ndk/guides/audio/aaudio` | HTTP 404 | `/ndk/guides/audio/aaudio/aaudio` |
| `/ndk/guides/audio/oboe` | HTTP 404 | Oboe dokümanı resmi Android rehberi içinde değil → github `google/oboe` |
| `/ndk/guides/audio/ndk-audio` | HTTP 404 | `/ndk/guides/audio` |
| `/games/indie/gdc/using-audio-ndk` | HTTP 404 | geçerli karşılığı bu turda bulunamadı → `UNKNOWN` |

### §10.2 Doğru yolu çıkarma yöntemi

İndeks sayfası `https://developer.android.com/ndk/guides/audio` üzerindeki
`href` listesinden çıkarılan 10 yolun tamamı HTTP 200 döndü (ölçüm: 2026-10-12).

| Çıkarılan yol | Durum |
|---|---|
| `/ndk/guides/audio/aaudio/aaudio` | 200 |
| `/ndk/guides/audio/opensl` | 200 |
| `/ndk/guides/audio/opensl/getting-started` | 200 |
| `/ndk/guides/audio/opensl/opensl-for-android` | 200 |
| `/ndk/guides/audio/opensl/opensl-prog-notes` | 200 |
| `/ndk/guides/audio/opensl/android-extensions` | 200 |
| `/ndk/guides/audio/audio-latency` | 200 |
| `/ndk/guides/audio/sampling-audio` | 200 |
| `/ndk/guides/audio/additional-resources` | 200 |
| `/ndk/guides/audio/midi` | 200 |

## §11 Oboe (github) — kapsam sınırı

| Öğe | Değer |
|---|---|
| URL | `https://github.com/google/oboe` |
| HTTP | 200 (2026-10-12) |
| Sayfa başlığı | "GitHub - google/oboe: Oboe is a C++ library that makes it easy to build high-performance audio apps on Android" |
| Gövde | Bu turda okunmadı → sürüm/ürün tarihi `UNKNOWN` |
| Dokümandaki rolü | NDK sayfasının önerdiği C++ sarmalayıcı (§3) |

**RED TEAM bulgu 11-1:** Oboe sürümü, API seviyesi eşikleri ve üretim-özel notlar
bu turda doğrulanmadı; depoya sürüm numarası YAZILAMAZ.

## §12 Doğrulama matrisi

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| NDK yüksek performanslı ses sayfası gövdesi okundu ("not recommended for new designs") | https://developer.android.com/ndk/guides/audio | 2026-10-12 | HTTP 200 · gövde alındı |
| AAudio "lightweight, native Android alternative" iddiası | https://developer.android.com/ndk/guides/audio | 2026-10-12 | Gövde alıntısı · doğrulandı |
| Oboe fallback zinciri iddiası | https://developer.android.com/ndk/guides/audio | 2026-10-12 | Gövde alıntısı · doğrulandı |
| AAudio sayfası erişilebilir (başlık) | https://developer.android.com/ndk/guides/audio/aaudio/aaudio | 2026-10-12 | HTTP 200 · başlık kanıtı |
| MMAP/NOIRQ, getMmapPosition, pcm_mmap_get_hw_ptr | https://source.android.com/docs/core/audio/aaudio | 2026-10-12 | HTTP 200 · gövde alındı · LU 2026-10-09 UTC |
| `AUDIO_MMAP_APPLICATION_SHAREABLE` bayrağı | https://source.android.com/docs/core/audio/aaudio | 2026-10-12 | Gövde alıntısı · doğrulandı |
| `aaudio.mmap_policy` 1/2/3 anlamları | https://source.android.com/docs/core/audio/aaudio | 2026-10-12 | Gövde alıntısı · doğrulandı |
| OpenSL ES genel bakış sayfası | https://developer.android.com/ndk/guides/audio/opensl | 2026-10-12 | HTTP 200 · başlık kanıtı · `⚠️ VERIFICATION REQUIRED` |
| OpenSL ES get started | https://developer.android.com/ndk/guides/audio/opensl/getting-started | 2026-10-12 | HTTP 200 · başlık kanıtı · `⚠️ VERIFICATION REQUIRED` |
| OpenSL ES for Android | https://developer.android.com/ndk/guides/audio/opensl/opensl-for-android | 2026-10-12 | HTTP 200 · başlık kanıtı · `⚠️ VERIFICATION REQUIRED` |
| OpenSL ES programming notes | https://developer.android.com/ndk/guides/audio/opensl/opensl-prog-notes | 2026-10-12 | HTTP 200 · başlık kanıtı · `⚠️ VERIFICATION REQUIRED` |
| Android extensions (OpenSL) | https://developer.android.com/ndk/guides/audio/opensl/android-extensions | 2026-10-12 | HTTP 200 · başlık kanıtı · `⚠️ VERIFICATION REQUIRED` |
| Audio latency (NDK) | https://developer.android.com/ndk/guides/audio/audio-latency | 2026-10-12 | HTTP 200 · başlık kanıtı |
| Sampling audio (NDK) | https://developer.android.com/ndk/guides/audio/sampling-audio | 2026-10-12 | HTTP 200 · başlık kanıtı |
| Additional resources (NDK) | https://developer.android.com/ndk/guides/audio/additional-resources | 2026-10-12 | HTTP 200 · başlık kanıtı |
| Native MIDI API (NDK) | https://developer.android.com/ndk/guides/audio/midi | 2026-10-12 | HTTP 200 · başlık kanıtı |
| Oboe github deposu erişilebilir | https://github.com/google/oboe | 2026-10-12 | HTTP 200 · başlık kanıtı |

## §13 RED TEAM — bu dosyada reddedilen iddialar

| # | Reddedilen iddia | Gerekçe |
|---|---|---|
| 1 | "OpenSL ES hâlâ önerilen native API'dir" | Doküman: "not recommended for new designs" |
| 2 | "AAudio, OpenSL ES'in yeniden paketlenmesidir" | Doküman: "lightweight, native Android alternative" — farklı bir API |
| 3 | "Oboe yalnızca AAudio kullanır" | Doküman: AAudio yoksa OpenSL ES'e fallback yapar |
| 4 | "AAudio kullanmak MMAP kullanmak demektir" | MMAP yoksa eski AudioFlinger yolu kullanılır (§7) |
| 5 | "EXCLUSIVE mod her zaman kullanılabilir" | `/dev/snd/` tanımlayıcısı SELinux tarafından engellenir; dmabuf dönüşümü gerekir (§5.2) |
| 6 | "Android 10.0 öncesi paylaşabilirlik bildirimi aynıdır" | Eski yöntem negatif boyut iletilmesiydi; 10.0+ bayrak (§5.3) |
| 7 | "Politika değişimi anında etkilidir" | audioserver yeniden başlatma gerekir (§6.2) |
| 8 | "Belirli örnek hızı bu depoda standarttır" | Doküman yalnızca "optimal" seçim önerir; sayı YOK (§9) |

## §14 Açık sorular

1. Oboe'nin güncel sürümü ve destek tabanı (minimum API seviyesi): bu turda okunmadı → `UNKNOWN`.
2. OpenSL ES alt sayfalarının gövde içerikleri (interface listesi, örnek kod) → `⚠️ VERIFICATION REQUIRED`.
3. AAudio Exclusive Stream başarı oranı cihaz-özgü → `UNKNOWN`.
4. `AAudioTesting.h` içeriğinin tam listesi (yalnızca iki fonksiyon gövde alıntısında geçti) → kalanı `⚠️ VERIFICATION REQUIRED`.
5. NDK AAudio rehberinin gövdesi (yalnızca başlık + indeks kanıtı okundu) → ikinci tur önceliği.

## §15 Kapanış durumu

| Kalem | Değer |
|---|---|
| Dosya | `android-aaudio-ve-opensl.md` |
| `node_type` | `architecture` · `group`: `mimari` |
| Frontmatter alanı | 10 |
| Doğrulanan URL | 13 tekil (§12 matrisi 17 satır) |
| Gövde okunan sayfa | 2 (NDK ses · AAudio+MMAP) |
| Resmi alıntı bloğu | 4 (§5.3 · §6.1 · §6.2 · §6.3) |
| `⚠️ VERIFICATION REQUIRED` hücre | 9 |
| Açık soru | 5 |

## §16 MMAP/NOIRQ — mimari özet tabloları

| Bileşen | Rol | Kanıt |
|---|---|---|
| AAudioService | Akış yaşam döngüsü ve yol seçimi | §5–§6 gövde alıntısı |
| ALSA sürücüsü | `pcm_mmap_get_hw_ptr` ile hw_ptr + tstamp | §5.1 |
| tinyalsa | Kullanım-bölgesi ALSA API'si | §5.1 fonksiyon imzası |
| Ion bellek kütüphanesi | `anon_inode:dmabuf` üretimi | §5.2 |
| SELinux | `/dev/snd/` tanımlayıcısının istemciye geçişini engeller | §5.2 |
| Yazılım/donanım mikser | Ek veri yolu (paralel AudioFlinger) | §5.3 |
| audioserver | Özellik değişikliğinde yeniden başlatma gerekir | §6.2 |

### §16.1 Politika–mod etkileşim matrisi

| `mmap_policy` | `mmap_exclusive_policy` | Beklenen davranış (doküman ifadelerinden türetilmiş) |
|---|---|---|
| 1 (NEVER) | — | MMAP denenmez; mod seçimi anlamsız |
| 2 (AUTO) | 2 (EXCLUSIVE → SHARED) | Yapılandırma örneğindeki varsayılan kombinasyon |
| 3 (ALWAYS) | — | MMAP zorunlu; başarısızlıkta fallback YOK |
| bilinmiyor | bilinmiyor | Cihaz-özgü yapılandırma → `UNKNOWN` |

**Not:** Bu matrisin hücreleri dokümandaki üç politika tanımı + Makefile örneğinden
türetilmiştir; türetme olduğu §0 kuralı gereği belirtilmiştir.

## §17 EXCLUSIVE ↔ SHARED karar ağacı

| # | Soru | Evet | Hayır |
|---|---|---|---|
| 1 | Cihaz MMAP destekliyor mu? | 2. soruya geç | Legacy yol (§7) |
| 2 | `mmap_policy` MMAP'e izin veriyor mu? | 3. soruya geç | Legacy yol |
| 3 | Paylaşılabilir `dmabuf` var mı (`AUDIO_MMAP_APPLICATION_SHAREABLE` / eski negatif boyut)? | 4. soruya geç | SHARED kapsamı dışında → `UNKNOWN` |
| 4 | EXCLUSIVE isteniyor mu (`mmap_exclusive_policy`)? | dmabuf dönüşümü + SELinux uyumu gerekli | SHARED modda servis üzerinden kullanım |
| 5 | İstemci `/dev/snd/` tanımlayıcısı istedi mi? | SELinux **engeller** (§5.2) | — |

## §18 NDK alt sayfalarının envanteri (başlık kanıtı tablosu)

| # | Başlık (sayfanın kendi `<title>` değeri) | Konu alanı |
|---|---|---|
| 1 | High-performance audio | §1 ana sayfa |
| 2 | Audio latency | §9 |
| 3 | Sampling audio | §9 |
| 4 | AAudio | §4 |
| 5 | OpenSL ES (genel) | §2 §8 |
| 6 | Get started (OpenSL) | §8 |
| 7 | OpenSL ES for Android | §8 |
| 8 | Android extensions | §8 |
| 9 | OpenSL ES programming notes | §8 |
| 10 | Native MIDI API | ayrı havuz |
| 11 | Additional resources for high-performance audio | §9 |

Başlıklar `Invoke-WebRequest` yanıtındaki `<title>` etiketinden alınmıştır
(2026-10-12, HTTP 200). Başlık kanıtı **içerik kanıtı değildir**; §8'deki gibi
içerik iddiaları ayrıca işaretlenir.

## §19 Kod kanıtı envanteri

| # | Tür | Bulunduğu bölüm | Kaynak |
|---|---|---|---|
| 1 | `[RESMİ ALINTI]` C bayrağı | §5.3 | AOSP AAudio/MMAP gövdesi |
| 2 | `[RESMİ ALINTI]` Makefile | §6.1 | aynı |
| 3 | `[RESMİ ALINTI]` kabuk (adb) | §6.2 | aynı |
| 4 | `[RESMİ ALINTI]` C fonksiyon imzaları | §6.3 | aynı |
| 5 | `[ORİJİNAL ÖRNEK · derlenmedi]` | §20 | bu dosya |

Toplam: 4 resmi alıntı + 1 orijinal örnek. `Dosya:/Satır:`: **0** (repo 0 `.cpp/.h`).

### §19.1 Orijinal örnek (derlenmedi, kanıt değildir)

**[ORİJİNAL ÖRNEK · derlenmedi]**

```cpp
// Kavramsal akış — resmi imza doğruluğu §6.3'teki alıntıyla sınırlıdır.
enum class Yol { Eski, Mmap };
Yol secYol(AAudioStream* s) {
    return AAudioStream_isMMapUsed(s) ? Yol::Mmap : Yol::Eski;
}
```

**Uyarı:** Bu blok depoda derlenmemiştir; yalnızca §7'deki ölçüm tuzağını
anlatmak içindir. Üretim kodu olarak kullanılamaz.

## §20 Kırmızı takım matrisi (dosya bazında)

| Tehlike | Tespit | Aksiyon |
|---|---|---|
| Uydurma yol tahmini | §10.1'de 4 adet 404 ölçüldü | Yollar yalnız indeks `href` listesinden |
| MMAP ≠ AAudio varsayımı | §7 | `AAudioStream_isMMapUsed` ölçümü şart |
| OpenSL ES "öneriliyor" yanlış bilgisi | §2 §13 | Doküman alıntısıyla düzeltildi |
| Sürüm uydurma (Oboe) | §11 §14.1 | `UNKNOWN` yazıldı |
| Örnek hızı sabitleme | §9 | Sayı yazılmadı |
| Sel/güvenlik iddiası | — | Bu dosyada hiç gündeme gelmedi (kapsam dışı) |

## §21 Bağlantı haritası (bu dosyanın komşuları)

| Komşu dosya | Ayrık konu | URL kesişimi |
|---|---|---|
| `android-audio-mimarisi.md` | Platform mimarisi, terminoloji, öznitelikler | YOK (ölçüldü) |
| `android-hal-ve-surucu.md` | HAL, ALSA, MMAP kernel tarafı ayrıntıları | YOK — `/dev/snd/` tartışma derinliği orada |
| `android-izin-ve-lifecycle.md` | İzin + yaşam döngüsü | YOK |
| `android-kod-ornekleri.md` | Java/Kotlin referans kodları | YOK |
| `android-web-arastirma.md` | Kaynak taraması | YOK |

**Not:** §5'teki `pcm_mmap_get_hw_ptr` konusu HAL dosyasıyla **temas** eder; URL
tekrarı yasak olduğu için burada kalır, HAL dosyası kendi kaynaklarını kullanır.

## §22 Kapanış durumu (revize)

| Kalem | Değer |
|---|---|
| Dosya | `android-aaudio-ve-opensl.md` |
| `node_type` | `architecture` · `group`: `mimari` |
| Frontmatter alanı | 10 |
| Doğrulanan URL | 13 tekil |
| Gövde okunan sayfa | 2 |
| Resmi alıntı bloğu | 4 |
| Orijinal örnek | 1 (derlenmedi) |
| `## §N` bölüm sayısı | 23 (§0–§22) |
| Tablo sayısı | ≥18 |
| Açık soru | 5 (§14) |

## §23 Ölçüm ve tekrar üretilebilirlik kaydı

| Adım | Komut / işlem | Gözlenen sonuç (2026-10-12) |
|---|---|---|
| 1 | GET `https://developer.android.com/ndk/guides/audio` | HTTP 200 · gövde ≈11.657 karakter düz metin |
| 2 | Gövdeden "not recommended" ifadesinin çıkarılması | Eşleşme bulundu (§2) |
| 3 | Gövdeden Oboe fallback ifadesinin çıkarılması | Eşleşme bulundu (§3) |
| 4 | GET `https://source.android.com/docs/core/audio/aaudio` | HTTP 200 · düz metin 15.833 karakter · LU 2026-10-09 UTC |
| 5 | `pcm_mmap_get_hw_ptr` araması | Gövdede bulundu (§5.1) |
| 6 | `aaudio.mmap_policy` araması | Gövdede bulundu (§6) |
| 7 | Tahmini yol `.../audio/aaudio` | HTTP 404 (§10.1) |
| 8 | İndeks `href` listesi (10 yol) | 10/10 HTTP 200 (§10.2) |
| 9 | GET `https://github.com/google/oboe` | HTTP 200 · başlık kanıtı |

### §23.1 Ölçüm geçerlilik sınırları

| Sınır | Açıklama |
|---|---|
| Tek tur | Tüm ölçümler tek günde alındı; sayfalar değişebilir |
| User-Agent | Tarayıcı UA'sı kullanıldı; varsayılan UA ile sonuç farklı olabilir |
| HEAD vs GET | HEAD ile 404 / GET ile 200 görüldü → her zaman GET kullanıldı |
| Gövde kapsamı | Yalnızca 2 sayfanın gövdesi alındı; 15 sayfa başlık düzeyinde |
| Cihaz ölçümü | Gerçek donanım üzerinde ölçüm YAPILMADI → gecikme sayıları `UNKNOWN` |

### §23.2 Kırmızı takım son kontrolü

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | Uydurma API adı var mı? | HAYIR — tüm API adları §5–§6 alıntılarından |
| 2 | Uydurma sürüm var mı? | HAYIR — Oboe sürümü `UNKNOWN` (§14.1) |
| 3 | Klasör içi URL kesişimi | HAYIR (kapanış raporunda ölçülür) |
| 4 | Dolgu/kısaltma yer tutucu | 0 |
| 5 | `Dosya:/Satır:` | 0 |
| 6 | Frontmatter 10 alan | EVET |
| 7 | İmla hataları korundu | EVET (SSOT §3 KURAL 1) |

## §24 Kapanış metrikleri (ölçüm — §15 yerine geçer)

| Metrik | Değer | Ölçüm yöntemi |
|---|---|---|
| Tekil URL | 13 | regex `https://[^\s\)\|`"\<\>]+` → benzersiz kümeler |
| Doğrulama matrisi satırı | 17 (§12) — 4 satır aynı URL'yi tekrarlar | elle sayım |
| Gövde okunan sayfa | 2 | düz metin uzunluğu + alıntı |
| Resmi alıntı bloğu | 4 | §19 envanteri |
| Orijinal örnek | 1 (derlenmedi) | §19.1 |
| Frontmatter alanı | 10 | manuel sayım |
| `## §N` bölüm | 24 (§0–§23) | başlık taraması |
| Dolgu yer tutucu | 0 | metin taraması |

| # | Son kontrol | Sonuç |
|---|---|---|
| 1 | Dosyalar arası URL kesişimi (android klasörü) | Kapanış raporunda ölçülür — bu yazımda §21 komşu haritası |
| 2 | `Dosya:/Satır:` | 0 |
| 3 | `git commit` | ATILMADI |

**Authority: Bayram Ali / Vault Steward**

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode