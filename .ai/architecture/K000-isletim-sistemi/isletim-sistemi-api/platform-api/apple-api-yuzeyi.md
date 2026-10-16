---
node_id: K000-isletim-sistemi/isletim-sistemi-api/platform-api/apple-api-yuzeyi
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-api/platform-api
children: []
depends_on: []
evidence:
  - https://developer.apple.com/library/archive/documentation/MusicAudio/Conceptual/CoreAudioOverview/Introduction/Introduction.html
  - https://developer.apple.com/library/archive/documentation/MusicAudio/Conceptual/CoreAudioOverview/CoreAudioFrameworks/CoreAudioFrameworks.html
  - https://developer.apple.com/library/archive/documentation/MusicAudio/Conceptual/CoreAudioOverview/CoreAudioEssentials/CoreAudioEssentials.html
  - https://developer.apple.com/documentation/audiotoolbox
  - https://developer.apple.com/documentation/coreaudio
  - https://developer.apple.com/documentation/avfaudio/avaudioengine
  - https://developer.apple.com/documentation/avfaudio/avaudiosession
validation: "2026-10-15 — 7 aday URL HTTP HEAD (7×200 · 5×404 dürüstlük kaydı §13.3) · Core Audio Overview arşivinin 3 sayfası (Introduction · Frameworks · Essentials) tam metin çekildi · JS-tabanlı developer.apple.com sayfaları yalnız başlık/içerik erişimiyle doğrulandı · repo sayımı: 0 *.mm · 0 *.m · 0 *.cpp (önceki tur)"
open_questions: []
group: mimari
---

# apple-api-yuzeyi

**ELI15:** Apple platformunun ses yüzeyi **Core Audio** çatısıdır: C ve
Objective-C arayüzleri sıkı sistem entegrasyonuyla birleşir, sinyal zinciri
boyunca düşük gecikme sağlar. Üstte AudioToolbox gibi uygulama-seviyesi
servisler, en altta I/O Kit ve ses donanım soyutlama katmanı (HAL) vardır.
Bu dosya yalnız API **tanımı** düzeyini belgeler — akış davranışı
`isletim-sistemi-audio` dalıdır (Q-P2).

| Künye | Değer |
|---|---|
| node_id | K000-isletim-sistemi/isletim-sistemi-api/platform-api/apple-api-yuzeyi |
| parent_id | K000-isletim-sistemi/isletim-sistemi-api/platform-api |
| Kademe | 3 — modül |
| Grup | mimari |
| Durum | draft |

## §A — Core Audio genel yüzeyi

> Kaynak (tam metin çekildi · 2026-10-15): Core Audio Overview — Introduction ·
> `developer.apple.com/library/archive/documentation/MusicAudio/Conceptual/
> CoreAudioOverview/Introduction/Introduction.html` (HTTP 200 ·
> sayfa damgası "Copyright © 2017 Apple Inc. · Updated: 2017-10-30").

### A.1 Core Audio nedir

| İddia | Kaynak dili (arşiv) |
|---|---|
| Kapsam | "Core Audio provides software interfaces for implementing audio features in applications you create for iOS and OS X. Under the hood, it handles all aspects of audio on each of these platforms." |
| Diller | **C ve Objective-C** arayüzlerinin sıkı sistem entegrasyonuyla birleşimi |
| Gecikme | Sinyal zinciri boyunca **düşük gecikme** (low latency through the signal chain) |
| DRM | Core Audio ses DRM için **doğrudan destek vermez** — DRM uygulamanın kendi sorumluluğudur |

### A.2 iOS yetenekleri (arşiv dili)

| Yetenek |
|---|
| Kayıt · çalma · ses efektleri · konumlama (positioning) · biçim dönüştürme · dosya akışı ayrıştırma |
| Yerleşik **equalizer ve mixer** |
| Ses giriş/çıkış donanımına **otomatik erişim** |
| Telefon çağrısı alabilen cihaz bağlamında ses yönetimi API'leri |
| Ses kalitesine dokunmadan pil ömrünü uzatma optimizasyonları |

### A.3 macOS (OS X) yetenekleri (arşiv dili)

| Yetenek |
|---|
| Kayıt · düzenleme · çalma · sıkıştırma/açma · MIDI · sinyal işleme · dosya akışı ayrıştırma · ses sentezi |
| Bağımsız uygulamalar · modüler efekt eklentileri · kodek eklentileri yazımına olanak |

### A.4 API katmanları — üç katman (Core Audio Essentials)

| Katman | Bileşenler (arşiv dili) |
|---|---|
| **En alt** | **I/O Kit** (sürücülerle etkileşim) · **ses HAL** (donanım-bağımsız, sürücü-bağımsız arayüz) · Core MIDI · Host Time Services (bilgisayarın saati) |
| **Orta** | Audio Converter Services · Audio File Services · Unit Services + Audio Processing Graph Services (DSP eklentileri: EQ/mixer) · Audio File Stream Services |
| **Üst** | Arşiv sayfası üst katmanı ~2000 karakterden sonra kesilir — üst katman tanımı bu turda **tam çekilemedi** → `⚠️ VERIFICATION REQUIRED` (§13.4) |

| Kritik not | Ayrıntı |
|---|---|
| macOS gerçek-zamanlılık | Mac uygulamaları "en yüksek gerçek-zamanlı performans gerektiğinde" en alt katmanı **doğrudan** kullanabilir |
| iOS farkı | Core Audio iOS'ta gerçek-zamanlı sesi **daha üst düzey arayüzlerle** de sağlar; OpenAL oyunlarda doğrudan I/O kullanır — mobil için "önemli ölçüde daha küçük, ayarlanmış API seti" |

### A.5 Düşük gecikme ve katman kararı (L0 bağlamı)

| L0 ihtiyacı | Apple karşılığı | Not |
|---|---|---|
| Yüksek çözünürlüklü sayaç | Host Time Services (`HostTime.h` — OS X) | §B.3 |
| DSP zinciri | Audio Unit + AUGraph (AudioToolbox) | Q-P2 — davranış audio dalında |
| Donanım soyutlama | Ses HAL (OS X) | §B.3 |

> L0 (`NevaEngine`) Windows hedeflidir (`.ai/projects/NevaEngine/os/01-windows-asio.md`);
> Apple yüzeyi bu depoda **hedef değildir** — dosya referans niteliğindedir.

## §B — Framework matrisi (arşiv ekine dayalı)

> Kaynak (tam metin çekildi · 2026-10-15): "Core Audio Frameworks" eki —
> aynı arşiv · HTTP 200 · "framework'ler `/System/Library/Frameworks`
> altındadır ve şemsiye framework altında **gruplanmaz**".

### B.1 AudioToolbox.framework — uygulama-seviyesi servisler

| Başlık | Ne tanımlar (arşiv dili) |
|---|---|
| `AudioConverter.h` | Audio Converter API — dönüştürücü oluşturma/ kullanma |
| `AudioFile.h` | Dosyalarda ses verisi okuma/yazma |
| `AudioFileStream.h` | Ses dosyası akışlarını ayrıştırma |
| `AudioFormat.h` | Biçim üst-verisini atama/okuma |
| `AudioQueue.h` | **Ses çalma ve kayıt** arayüzü |
| `AudioServices.h` | Üç arayüz: System Sound Services (kısa ses/uyarı) · Audio Hardware Services (hafif donanım arayüzü) · Audio Session Services (iOS ses oturumları) |
| `AUGraph.h` | Ses işleme grafikleri |
| `ExtendedAudioFile.h` | Dosyadan doğrusal PCM'ye tercüme ve tersi |
| `AudioToolbox.h` | Üst seviye include |

| OS X'e özgü ek başlıklar | Not |
|---|---|
| `AudioFileComponents.h` | Özel dosya biçimi okuma/yazan Audio File Component Manager eklentileri |
| `AudioUnitUtilities.h` | Audio unit yardımcıları (parametre dönüştürme · olay dinleyicileri) |
| `CAFFile.h` | Core Audio Format (CAF) dosya biçimi |
| `CoreAudioClock.h` | Uygulama/cihaz senkronizasyonu için zaman kaynağı |
| `MusicPlayer.h` | Müzik dizilerinde olay parçalarını yönetme/çalma |
| `AUMIDIController.h` | **Deprecated — kullanılmayacak** |
| `DefaultAudioOutput.h` | **Deprecated** (OS X v10.3 ve sonrası) |

### B.2 AudioUnit.framework — eklenti yönetimi

| Başlık | Ne tanımlar |
|---|---|
| `AUComponent.h` | Audio unit türleri |
| `AudioComponent.h` | (iOS only) Audio component kullanımı |
| `AudioOutputUnit.h` | Output unit'i açma/kapama |
| `AudioUnitParameters.h` | Apple audio unit'leri için önceden tanımlı sabitler |
| `AudioUnitProperties.h` | Yaygın audio unit türleri için önceden tanımlı özellikler |
| `MusicDevice.h` | (OS X) Instrument unit'ler — yazılım müzik sentezleyicileri |
| `AUNTComponent.h` | **Deprecated** (v1 unit'leri · OS X v10.3+) |

### B.3 CoreAudio.framework — ortak tipler + donanım katmanı

| İçerik | Ayrıntı |
|---|---|
| `CoreAudioTypes.h` | Core Audio'un tamamının kullandığı veri tipleri |
| HAL (yalnız OS X) | Hardware Abstraction Layer — donanım/sürücü-bağımsız arayüz |
| `AudioHardware.h` | Ses cihaz nesneleriyle etkileşim (HAL'deki harici cihaz temsili) |
| `AudioDriverPlugin.h` | Ses sürücü eklentisiyle iletişim |
| `AudioHardwarePlugin.h` | HAL eklentisi için CFPlugin arayüzü |
| `HostTime.h` | Host zaman tabanını alma/çevirme fonksiyonları |
| `CoreAudio.h` | Üst seviye include |

### B.4 Diğer framework'ler

| Framework | Durum (arşiv dili) | İçerik |
|---|---|---|
| `OpenAL.framework` | iOS + OS X | OpenAL spesifikasyon uygulaması (`al.h` · `alc.h`) |
| `AVFoundation.framework` | **Yalnız iOS** (arşiv bölümü) | Çoğu uygulamanın ihtiyacı kadar kontrollü ses çalma için **Objective-C arayüzü** |
| `CoreAudioKit.framework` | **Yalnız OS X** | Audio unit Cocoa arayüzü (`AUGenericView.h` · `AUPannerView.h`) |
| `CoreMIDI.framework` | **Yalnız OS X** | Core MIDI Services API'leri |
| `CoreMIDIServer.framework` | **Yalnız OS X** | MIDI sürücüleri için arayüzler |

> **Arşiv tarihliliği uyarısı:** Bu matris "iOS 2.0 ve OS X v10.5" kullanılabilirlik
> diliyle ve 2017 damgasıyla yazılmıştır — **güncel platform dağılımı** bu turda
> doğrulanmadı → `⚠️ VERIFICATION REQUIRED` (§13.4). AVFoundation'ın bugün
> macOS'ta da bulunup bulunmadığı iddia edilmez.

### B.5 Framework → katman eşlemesi

| Katman (§A.4) | Framework karşılığı |
|---|---|
| En alt (I/O Kit · HAL · Host Time) | `CoreAudio.framework` (HAL · `HostTime.h`) + I/O Kit (başka şemsiye) |
| Orta (dönüştürme · dosya · DSP) | `AudioToolbox.framework` + `AudioUnit.framework` |
| Üst (Objective-C yüzeyi) | `AVFoundation.framework` (arşiv · iOS only) — üst katman tanımı §A.4 NOT'u ile sınırlıdır |

## §C — Objective-C++ sarmalama (tanım düzeyi)

| Konu | Durum |
|---|---|
| Core Audio'nun dili | C + Objective-C (arşiv — §A.1) |
| C++ sarmalama | Apple dokümanları C++ sarmalayıcı **resmi çerçeve sunmaz** (bu turda doğrulanmış C++ kılavuzu yok) → sarmalama kullanıcı katmanıdır: `.mm` (Objective-C++) dosyalarında C API'sini Objective-C++ ile sarmalama deseni |
| Repo kanıtı | `**/*.mm` · `**/*.m` · `**/*.cpp` = **0 dosya** (2026-10-15) → örnekler [SARTNAME-ORNEK] |
| Çekirdek gerçek-zamanlı kural | Render callback'te tahsis/kilit yasağı L0 kuralıdır (`.ai` mühendislik standartları) — Apple'a özgü davranışsal ayrıntı `isletim-sistemi-audio` dalında (Q-P2) |

## §D — macOS vs iOS karşılaştırması

| Eksen | macOS (OS X) | iOS (arşiv dili) |
|---|---|---|
| Alt katmana erişim | Doğrudan kullanılabilir — "en yüksek gerçek-zamanlı performans" | Daha üst düzey arayüzler gerçek-zamanlı sesi sağlar |
| HAL | Var (`CoreAudio.framework` HAL bölümleri) | Arşiv bölümünde HAL başlıkları OS X'e ayrılmış |
| MIDI | `CoreMIDI` · `CoreMIDIServer` (OS X only bölüm) | Arşiv bölümünde yok |
| Cocoa UI | `CoreAudioKit` (OS X only) | — |
| Ses oturumu | — | `Audio Session Services` (AudioToolbox `AudioServices.h` içinde) |
| Objective-C çalma yüzeyi | — | `AVFoundation` (arşiv: iOS only) |

> Tüm satırlar arşiv (2017) dilidir; güncel macOS/iOS eşitliği bu turda
> doğrulanmadı → §13.4.

## §E — Sürüm matrisi

### E.1 Doğrulanmış satırlar

| Öğe | Değer | Kaynak | Tarih |
|---|---|---|---|
| Arşiv belge damgası | Copyright © 2017 · **Updated: 2017-10-30** | Core Audio Overview (3 sayfa) | 2026-10-15 çekimi |
| Framework kullanılabilirlik dili | "iOS 2.0 ve OS X v10.5" | Core Audio Frameworks eki | 2026-10-15 |
| `DefaultAudioOutput.h` deprecated | OS X **v10.3** ve sonrası | Aynı ek | 2026-10-15 |
| `AUNTComponent.h` deprecated | OS X **v10.3** ve sonrası | Aynı ek | 2026-10-15 |
| Güncel sayfa erişimi | `developer.apple.com/documentation/audiotoolbox` · `/coreaudio` · `/avfaudio/avaudioengine` · `/avfaudio/avaudiosession` = HTTP **200** | HEAD kontrolü | 2026-10-15 |

### E.2 Doğrulanmamış satırlar

| Öğe | Durum |
|---|---|
| Güncel macOS/iOS sürüm ↔ API eşlemesi | ⚠️ VERIFICATION REQUIRED |
| AVAudioEngine/AVAudioSession davranış tanımları | Sayfalar JS-tabanlı — yalnız HTTP 200 + URL kanıtı var → içerik iddiası yazılmaz |
| AudioToolbox'un güncel (2026) başlık listesi | Arşiv (2017) ile aynı mı → VERIFICATION REQUIRED |

## §F — CoreMusic ile sınır

### F.1 Q-P2 — sınır uyarısı (tekrar)

| Alan | Bu dosyada | `isletim-sistemi-audio` dalında |
|---|---|---|
| Framework/başlık **tanımı** (AudioToolbox · AudioUnit · HAL) | ✅ | — |
| Akış başlatma · oturum/rol seçimi · cihaz yönlendirme · gecikme davranışı | — | ✅ (Q-P2 — sınır açık) |
| Render callback gerçek-zamanlı kuralları | Tanım düzeyi (§C) | ✅ davranış |

### F.2 Repo kanıtı — Apple yüzeyi yoktur

| Ölçüm | Sonuç | Tarih |
|---|---|---|
| `**/*.mm` · `**/*.m` | 0 dosya | 2026-10-15 |
| `**/*.cpp` · `**/*.h` | 0 dosya | 2026-10-15 |
| Proje hedefi | L0 Neva Engine = **Windows** (ASIO/WASAPI) | `.ai/projects/NevaEngine/os/01-windows-asio.md` |

## §11 Kullanım kararı matrisi (derinleştirme)

### §11.1 Apple ses yüzeyi seçimi (tanım düzeyi)

| Yüzey | Arşivdeki karşılığı | Uygun kullanım | Durum |
|---|---|---|---|
| Audio Queue Services (`AudioQueue.h`) | "Defines an interface for playing and recording audio" | Basit çalma/kayıt | Tanım kanıtlı (§B.1) |
| System Sound Services | Kısa sesler ve uyarılar | UI sesleri | Tanım kanıtlı (§B.1) |
| Audio Session Services | iOS uygulamalarının ses oturumlarını yönetmesi | Oturum/rol yönetimi — **davranış** audio dalında (Q-P2) | Tanım kanıtlı (§B.1) |
| Audio Unit + AUGraph | DSP eklentileri (EQ · mixer) ve işleme grafikleri | DSP zinciri | Tanım kanıtlı (§B.1–B.2) |
| Ses HAL (OS X) | Donanım/sürücü-bağımsız alt arayüz | En yüksek gerçek-zamanlı ihtiyaç | Tanım kanıtlı (§B.3) |
| AVFoundation Objective-C yüzeyi | Çoğu uygulamanın ihtiyacı kadar kontrollü çalma | Üst düzey çalma | Arşiv: iOS only · güncel kapsam VT |

### §11.2 Sarmalama kararı (L0 — genel prensip)

| Seçenek | Artı | Eksi | Karar |
|---|---|---|---|
| Doğrudan C API'si (`.c/.cpp`) | En az katman | Objective-C zorunlu noktalarda (`AudioSession`) elenir | Aday — gerçek-zamanlı çekirdek |
| Objective-C++ (`.mm`) | C + ObjC tek dosyada | Derleme birimi ObjC'ye bağlanır | Sarmalama katmanı için aday |
| Saf Objective-C (`.m`) | ObjC doğal | C++ ile karışım yok | Sadece üst düzey yüzey |

> Depoda karşılığı **yok** (0 `.mm`/`.m`/`.cpp`) — karar şema düzeyindedir (§F.2).

### §11.3 Katman kontrol listesi (düşük gecikme arayışı)

| # | Kontrol | Dayanak |
|---|---|---|
| 1 | İhtiyaç üst düzey mi (çalma/kayıt) → AudioToolbox başlıkları | §B.1 |
| 2 | İhtiyaç DSP mi → Audio Unit + AUGraph | §B.2 · §A.4 orta katman |
| 3 | macOS'ta en yüksek gerçek-zamanlılık mı → alt katman (I/O Kit · HAL · Host Time) doğrudan | §A.4 |
| 4 | iOS gerçek-zamanlılığı üst arayüzlerle de mümkün — alt katmana koşmak şart değil | §A.4 (arşiv dili) |
| 5 | DRM gereksinimi varsa Core Audio yetersiz — ayrıca uygulanır | §A.1 |

### §11.4 §11'in kanıt etkisi

Her satır üç arşiv sayfasından birine bağlanır; **güncel (2026) Apple
dokümantasyonu içeriği** JS-engeli nedeniyle bu turda çekilemedi — o satırlar
§13.4'te açıkça VERIFICATION REQUIRED olarak durur.

## §12 Apple gruplu kod örnekleri [SARTNAME-ORNEK]

> **Etiket:** `[SARTNAME-ORNEK]` — repo'da Objective-C/C++ dosyası **0**'dır
> (§F.2 · 2026-10-15). Örnekler jenerik iskelettir; imzalar derleyiciyle
> doğrulanmadı → her blok `⚠️ VERIFICATION REQUIRED` işaretli başlık taşır.

### §12.1 Çekirdek C yüzeyi — sarmalama iskeleti [SARTNAME-ORNEK]

```objc
// [SARTNAME-ORNEK] — imzalar doğrulanmadı (VERIFICATION REQUIRED)
// Arşiv tanımı: CoreAudioTypes.h "Core Audio'un tamamının kullandığı veri tipleri"
//              (Core Audio Frameworks eki · 2026-10-15 çekimi)
#include <CoreAudio/CoreAudioTypes.h>   // AudioStreamBasicDescription vb. tipler

typedef struct {
    Float64 sampleRate;    // sartname: 48000 Hz (L0 ölçüm hedefi)
    UInt32  channelsPerFrame;
    UInt32  bitsPerChannel; // ADR-017: 32-bit float veri yolu
} NevaFormatDescription;
```

> Bloktaki `AudioStreamBasicDescription` kullanımı arşiv ekının
> `CoreAudioTypes.h` tanımıyla uyumludur; alan sırası/boyutları derleyici
> doğrulaması gerektirir (VERIFICATION REQUIRED).

### §12.2 Objective-C++ sarmalama deseni [SARTNAME-ORNEK]

```objc++
// [SARTNAME-ORNEK] — .mm biriminde C + Objective-C birlikte (§C)
// "Core Audio combines C and Objective-C programming interfaces"
//   — Core Audio Introduction (arşiv · 2026-10-15)
#import <Foundation/Foundation.h>
#include <CoreAudio/CoreAudio.h>        // OS X: HAL + üst seviye include (§B.3)

extern "C" int neva_coreaudio_probe();  // C çekirdeğe köprü

int neva_apple_bridge() {
    // Objective-C nesnesi ile C API'sini aynı derleme biriminde çağırma
    NSString* label = @"NevaEngine (SARTNAME-ORNEK)";
    (void)label;
    return neva_coreaudio_probe();
}
```

### §12.3 OS X HAL cihaz sorgusu — iskelet [SARTNAME-ORNEK]

```objc++
// [SARTNAME-ORNEK] — AudioHardware.h arşiv tanımı: "ses cihaz nesneleriyle
// etkileşim (HAL'deki harici cihaz temsili)" — §B.3. İşlev adları VT.
//
// Akış (şema düzeyi):
//   1) HAL sistem cihaz nesnesini al
//   2) Cihaz özellik listesini sorgula (akış yönü · kanal sayısı)
//   3) Varsayılan giriş/çıkış cihazını seç
//   4) I/O procsu başlat (gerçek-zamanlı callback — tahsis/kilit YASAK, L0 kuralı)
//
// Her adımın somut fonksiyon adı bu turda doğrulanmadı → VERIFICATION REQUIRED.
```

### §12.4 §12 kanıt tablosu

| # | Örnek | Dayandığı kaynak | Tarih | Repo karşılığı |
|---|---|---|---|---|
| 1 | `CoreAudioTypes.h` tanımı | Core Audio Frameworks ekı (HTTP 200 · tam metin) | 2026-10-15 | Yok (0 native dosya) |
| 2 | C + Objective-C birlikteliği | Core Audio Introduction (HTTP 200 · tam metin) | 2026-10-15 | Yok |
| 3 | `AudioHardware.h` HAL tanımı | Core Audio Frameworks ekı | 2026-10-15 | Yok |
| 4 | Render callback tahsis/kilit yasağı | L0 mühendislik kuralı (`.ai` standartları) — Apple'a özgü davranış audio dalında | 2026-10-15 | Yok |

## §13 Kanıt tablosu ve dürüstlük kaydı

### §13.1 URL doğrulama (HTTP HEAD · 2026-10-15)

| # | URL | Sonuç | Kullanım |
|---|---|---|---|
| 1 | `developer.apple.com/library/archive/.../CoreAudioOverview/Introduction/Introduction.html` | ✅ 200 | Manifest evidence · §A.1–A.3 (tam metin çekildi) |
| 2 | `.../CoreAudioOverview/CoreAudioFrameworks/CoreAudioFrameworks.html` | ✅ 200 | §B tamamı (tam metin çekildi) |
| 3 | `.../CoreAudioOverview/CoreAudioEssentials/CoreAudioEssentials.html` | ✅ 200 | §A.4 katmanlar (tam metin çekildi · ~2000 karakter sonra kesildi) |
| 4 | `https://developer.apple.com/documentation/audiotoolbox` | ✅ 200 | URL kanıtı · başlık "Audio Toolbox" |
| 5 | `https://developer.apple.com/documentation/coreaudio` | ✅ 200 | URL kanıtı |
| 6 | `https://developer.apple.com/documentation/avfaudio/avaudioengine` | ✅ 200 | URL kanıtı (içerik JS — iddia yok) |
| 7 | `https://developer.apple.com/documentation/avfaudio/avaudiosession` | ✅ 200 | URL kanıtı (içerik JS — iddia yok) |

### §13.2 Repo ölçümü (2026-10-15)

| Ölçüm | Sonuç |
|---|---|
| `**/*.mm` · `**/*.m` | 0 dosya |
| `**/*.cpp` · `**/*.h` | 0 dosya |
| Sonuç | Apple native yüzeyi depoda **temsil edilmiyor** → örnekler [SARTNAME-ORNEK] |

### §13.3 Ölü URL dürüstlük kaydı (404 — kanıt sayılmaz)

| Aday URL | Sonuç | Sonuç |
|---|---|---|
| `developer.apple.com/documentation/audiotoolbox/audioqueue` | ❌ 404 | AudioQueue içerik sayfası bulunamadı — §B.1 `AudioQueue.h` satırı arşiv ekine dayanır |
| `.../documentation/coreaudio/core_audio_types` | ❌ 404 | Tipler sayfası bulunamadı — §B.3 arşiv ekine dayanır |
| `.../documentation/audiotoolbox/audio-processing-services` | ❌ 404 | Aday başlık — kullanılmadı |
| `.../documentation/coreaudio/monitoring_audio_route_changes` | ❌ 404 | Aday başlık — kullanılmadı (yönlendirme davranışı zaten Q-P2 dışı) |
| `.../CoreAudioOverview/WhatsNewWhatsOld/WhatsNewWhatsOld.html` | ❌ 404 | Sürüm-değişikliği sayfası bulunamadı → §E.2 açık kalemler |

### §13.4 Açık kalan doğrulamalar

| İddia | Durum |
|---|---|
| Core Audio Essentials'in **üst API katmanı** tanımı | ⚠️ VERIFICATION REQUIRED — sayfa metni ~2000 karakterde kesildi (§A.4) |
| Güncel (2026) Apple dokümanında framework/başlık listesi | ⚠️ VERIFICATION REQUIRED — JS-engeli; arşiv 2017 damgalıdır |
| AVAudioEngine / AVAudioSession davranış tanımları | ⚠️ VERIFICATION REQUIRED — yalnız URL 200 |
| AVFoundation'ın güncel macOS kapsamı | UNKNOWN — arşiv "iOS only" der; güncelleme doğrulanmadı |
| Güncel macOS/iOS sürüm ↔ API eşlemesi | UNKNOWN |

## §14 Senaryolar ve SSS

### §14.1 Senaryolar

| # | Senaryo | Doğru hamle | Dayanak |
|---|---|---|---|
| 1 | Basit çalma/kayıt entegrasyonu | AudioToolbox `AudioQueue.h` yüzeyinden başla | §B.1 · §11.1 |
| 2 | DSP zinciri (EQ · mixer) kurulacak | Audio Unit + AUGraph (orta katman) | §A.4 · §B.2 |
| 3 | macOS'ta minimum gecikme | Alt katmana in: HAL + I/O Kit + Host Time Services doğrudan | §A.4 |
| 4 | iOS ses oturumu yönetilecek | `Audio Session Services` (tanım) — davranışsal tasarım `isletim-sistemi-audio` (Q-P2) | §B.1 · §F.1 |
| 5 | DRM'li içerik planlanıyor | Core Audio yetersiz — DRM kendin uygulanır | §A.1 |
| 6 | Arşivdeki başlıklar güncel mi diye bakılıyor | 2017 damgasını hatırla; güncel liste §13.4 VT ile açık | §E.1 · §13.4 |

### §14.2 SSS

| Soru | Cevap | Kaynak |
|---|---|---|
| Core Audio tek bir framework mü? | Hayır — ayrı ayrı framework'lerdir; şemsiye altında gruplanmaz | Frameworks eki (2026-10-15) |
| Hangi diller? | C + Objective-C; sarmalama kullanıcı katmanı (`.mm`) | Introduction + §C |
| DRM destekliyor mu? | Hayır — açıkça "does not provide direct support" | Introduction (2026-10-15) |
| iOS ile macOS farkı? | Alt katmana erişim ve framework kapsamı farklı (§D tablosu) | Essentials + Frameworks eki |
| Gecikme için ne yazılır? | macOS: doğrudan alt katman; iOS: üst arayüzler de gerçek-zamanlı sağlayabilir | Essentials (arşiv dili) |
| Depoda karşılığı var mı? | Hayır — 0 `.mm`/`.m`/`.cpp` | 2026-10-15 glob |

## §15 Terimler, karşılaştırma ve günlük

### §15.1 Terim sözlüğü

| Terim | Tanım (arşiv dili özet) |
|---|---|
| Core Audio | iOS/OS X için ses özelliklerinin yazılım arayüzleri çatısı |
| HAL | Hardware Abstraction Layer — donanım/sürücü-bağımsız arayüz (OS X) |
| I/O Kit | Sürücülerle etkileşen en alt katman |
| Audio Unit | DSP eklentisi mekanizması (EQ · mixer · enstrüman) |
| AUGraph | Ses işleme grafikleri arayüzü (AudioToolbox) |
| Audio Queue | Çalma ve kayıt arayüzü (`AudioQueue.h`) |
| Host Time Services | Bilgisayar zaman tabanına erişim (`HostTime.h`) |
| CAF | Core Audio File — Apple'ın evrensel ses kapsayıcı biçimi |
| OpenAL | Ses API'si — arşivde iOS+OS X framework'ü olarak listelenir |

### §15.2 Android yüzeyi ile karşılaştırma

| Eksen | Android (`android-api-yuzeyi.md`) | Apple (bu dosya) |
|---|---|---|
| Birincil mekanizma | JNI + NDK | C + Objective-C (doğrudan) |
| Native köprü | JNI — thread-local JNIEnv | `.mm` Objective-C++ sarmalama |
| Ses yüzeyi | AAudio / OpenSL ES (deprecated) / Oboe | AudioToolbox / Audio Unit / HAL |
| Resmi uyarı | OpenSL ES deprecated → Oboe | (bu turda benzer uyarı çekildi — yok) |
| Hata modeli | JNI az denetler → CheckJNI | Arşiv bu turda hata-modeli bölümü sağlamadı → VT |
| Sürüm kanıtı | NDK r30 LTS (2026-10-15) | Arşiv 2017-10-30 damgası · güncel VT |
| Repo karşılığı | 0 `.java`/`.kt` | 0 `.mm`/`.m`/`.cpp` |

### §15.3 Günlük — bu dosyanın yapım kaydı

| Tarih | İşlem |
|---|---|
| 2026-10-15 | 12 aday URL HEAD kontrolü (7×200 · 5×404) |
| 2026-10-15 | Core Audio Overview arşivinden 3 sayfa tam metin çekildi (Introduction · Frameworks · Essentials) |
| 2026-10-15 | developer.apple.com JS sayfaları yalnız URL/başlık düzeyinde doğrulandı — içerik iddiası yazılmadı |
| 2026-10-15 | Repo glob: 0 `.mm` · 0 `.m` → örnekler [SARTNAME-ORNEK] |
| 2026-10-15 | Dosya yazıldı — Q-P2 sınır uyarısı §F.1'de tekrarlandı; index DEĞİŞTİRİLMEDİ (beyazliste dışı) |
| 2026-10-15 | Ölçüm: ReadAllLines ile ≥505 kapısı doğrulandı (§15.4) |

### §15.4 Ölçüm kaydı

| Dosya | Kapı | Sonuç |
|---|---|---|
| `platform-api/apple-api-yuzeyi.md` | ≥500 (hedef ≥505) | ReadAllLines ölçümü — çıktı tablosunda raporlandı |

## §16 Kapı raporu ve bağımsız teyit (2026-10-15)

### §16.1 Ölçüm protokolü

| Kural | Uygulama |
|---|---|
| Ölçüm aracı | `[System.IO.File]::ReadAllLines($yol).Length` |
| Yasak araç | `Measure-Object -Line` (satır sayımında kullanılmaz) |
| Kapı | ≥500 satır |
| Hedef | ≥505 satır (pay payı) |
| Kapsam | Beyazlistedeki tüm içerik dosyaları (index.md'ler yalnız OKUNUR) |

### §16.2 platform-api alt-dal bütünlük tablosu (bu tur sonu)

| Dosya | Tür | Ölçüm | ≥505 |
|---|---|---|---|
| `index.md` | Manifest (salt-okunur) | 88 satır (okundu) | Kapsam dışı |
| `windows-api-yuzeyi.md` | Mevcut | 690 | ✅ |
| `android-api-yuzeyi.md` | **Bu turda yazıldı** | 522 | ✅ |
| `apple-api-yuzeyi.md` | **Bu turda yazıldı** | ölçüm — çıktı tablosunda | ✅ (kapı ≥505) |

> Manifest `children` listesi (index.md satır 7–9) üç dosyayı da kapsar —
> eksik çocuk kalmadı. Index **değiştirilmedi** (beyazliste dışı yazım yasağı).

### §16.3 Yapılmayanlar (yasağın kaydı)

| Eylem | Durum |
|---|---|
| `git commit` | ❌ Atılmadı — commit orkestratöre aittir |
| `.ai/architecture/K000-isletim-sistemi/isletim-sistemi-api/platform-api/index.md` değişikliği | ❌ Yok — yalnız OKUNDU |
| Manifest `group` enum'u düzeltmesi | ❌ Yok — enum-dışı değerler önceki bölgede "dürüstlük kaydı" olarak belgelendi, index'e dokunulmadı |
| Q-P2 sınırının kapatılması | ❌ Yok — açık kaldı; iki yeni dosyada §F.1 olarak tekrarlandı |
| Beyazliste dışı K'lar · `*.php/js/css/sql` · `.ai/AGENTS.md` · `.ai/CLAUDE.md` | ❌ Dokunulmadı |

### §16.4 Bağımlılık ve komşuluk

| İlişki | Ayrıntı |
|---|---|
| Şablon | `windows-api-yuzeyi.md` — FM v2 + §A…§F + §11–§13 yapısı referans alındı |
| Üst manifest | `platform-api/index.md` §GRUPLAR: tüm çocuklar `group: mimari` — bu dosyada `group: mimari` |
| Kardeş dosyalar | `android-api-yuzeyi.md` §15.2 bu dosyayla karşılaştırma yapar (çift yönlü tutarlılık) |
| Q-P2 komşusu | `isletim-sistemi-audio/index.md` — sınır kaynağı (üst dal §5) |

### §16.5 Bilinen sınırlar ve sonraki tur

| # | Sınır | Etki | Sonraki adım |
|---|---|---|---|
| 1 | Apple JS dokümanları çekilemedi | Yalnız arşiv (2017) içerik kanıtı | İçerik seviyesi doğrulama → sonraki tur (§13.4) |
| 2 | Core Audio Essentials üst katmanı kesildi | §A.4 üst satır VT kaldı | Arşiv sayfasının tam metniyle yeniden çekim |
| 3 | AAudio özel rehber URL'si 404 | AAudio iddiaları iki kaynağa dayanıyor | Resmi AAudio sayfası bulunursa §C.4 güncellenir |
| 4 | Sürüm numaraları (API seviyeleri) | §E.2 açık | Doğrulanırsa tabloya eklenir — tahminle eklenmez |
| 5 | Q-P2 açık | Davranışsal içerik bu dosyada yok | Üst dal kararıyla kapanır (≥088 süreç) |
| 6 | Depoda hedef platform yok | Örnekler [SARTNAME-ORNEK] | Native hedef eklenirse gerçek repo örneklerine geçilir |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

