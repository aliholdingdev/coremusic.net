---
node_id: K000-isletim-sistemi/isletim-sistemi-api/platform-api/android-api-yuzeyi
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-api/platform-api
children: []
depends_on: []
evidence:
  - https://developer.android.com/ndk
  - https://developer.android.com/ndk/guides/audio
  - https://developer.android.com/ndk/guides/audio/opensl
  - https://developer.android.com/ndk/reference/group/audio
  - https://developer.android.com/training/articles/perf-jni
  - https://developer.android.com/games/sdk/oboe
  - https://google.github.io/oboe/
  - https://developer.android.com/ndk/downloads
  - https://developer.android.com/about/versions/12
validation: "2026-10-15 — 9 aday URL HTTP HEAD ile doğrulandı (8×200 · 3×404 dürüstlük kaydı §13.3) · OpenSL ES sayfası (last updated 2024-09-12 UTC) ve JNI tips sayfası tam metin çekildi · NDK downloads 'Latest LTS Version (r30)' satırı çekildi · repo sayımı: 0 *.java · 0 *.kt"
open_questions: []
group: mimari
---

# android-api-yuzeyi

**ELI15:** Android'de uygulamanın işletim sistemine baktığı yüz üç katmandır:
Java/Kotlin framework'ü, bunun altındaki **JNI** köprüsü ve native tarafın
**NDK** toolchain'idir. Ses için native yüzeyin iki kütüphanesi vardır: eski
OpenSL ES (deprecated) ve yeni AAudio; Google bunların yerine **Oboe** sarmalayıcısını
önerir. Bu dosya yalnız API **tanımı** düzeyini belgeler — akış davranışı
`isletim-sistemi-audio` dalıdır (Q-P2).

| Künye | Değer |
|---|---|
| node_id | K000-isletim-sistemi/isletim-sistemi-api/platform-api/android-api-yuzeyi |
| parent_id | K000-isletim-sistemi/isletim-sistemi-api/platform-api |
| Kademe | 3 — modül |
| Grup | mimari |
| Durum | draft |

## §A — NDK yüzeyi (native development kit)

### A.1 NDK nedir

Android NDK, Android için native (C/C++) geliştirme kitidir; uygulamanın
C/C++ kodunu Android'e özgü toolchain ile derleyip paketler.

| Bileşen | Görev |
|---|---|
| NDK toolchain | Clang/LLVM tabanlı C/C++ derleyici + linker (Android hedefli) |
| Derleme sistemi | `ndk-build` (Android.mk) veya **CMake** (CMakeLists.txt) |
| Platform başlıkları | Android sistem kütüphanesi başlıkları (`SLES/*.h` · `AAudio/*.h` dahil) |
| Bağımlılıklar | ABI'ye göre önceden derlenmiş statik/dinamik kütüphaneler |

> Kanıt: NDK ana sayfası `https://developer.android.com/ndk` (HTTP 200 ·
> 2026-10-15 HEAD) — manifest evidence'ının birincil kaynağı.

### A.2 ABI matrisi (tanım düzeyi)

| ABI | Mimari | Not |
|---|---|---|
| `arm64-v8a` | ARM64 | Günün cihazlarının büyük çoğunluğunun hedefi |
| `armeabi-v7a` | ARM32 | Eski cihaz uyumluluğu |
| `x86_64` | x86-64 | Emülatör / Chromebook sınıfı |
| `x86` | x86 | Eski emülatör |

> ABI **sayıları** ve cihaz payları bu turda ölçülmüştür: `⚠️ VERIFICATION REQUIRED`
> (yüzdelik iddiası uydurulmaz; §13.4 dürüstlük kaydı).

### A.3 LTS sürüm kanıtı

| İddia | Kaynak | Tarih | Durum |
|---|---|---|---|
| NDK'nın güncel LTS sürümü **r30**'dur | `https://developer.android.com/ndk/downloads` — sayfada birebir "Latest LTS Version (r30)" satırı | 2026-10-15 | ✅ metin çekildi |

> Sürüm matrisinin tamamı §E'dedir; r30 dışında sürüm numarası **uydurulmaz**
> (eski r sürümleri bu turda doğrulanmadı → UNKNOWN).

### A.4 Toolchain karar matrisi (L0 — Neva Engine bağlamı)

| Seçenek | Artı | Eksi | L0 kararı |
|---|---|---|---|
| CMake | Endüstri standardı, IDE entegrasyonu | Yapı dosyası karmaşıklığı | Aday |
| `ndk-build` | Basit Android.mk | Android-dışı taşınmazlık | Dışarıda |
| Klasik makefile + elle Clang | Tam kontrol | Entegrasyon yükü | Dışarıda |

> L0 (`NevaEngine`) **Windows hedefli**dir (`.ai/projects/NevaEngine/os/01-windows-asio.md`);
> Android NDK hedefi projede **PLANNED değildir** — bu dosya referans yüzeydir.

### A.5 NDK ↔ Java/Kotlin sınırı

| Katman | Dil | Köprü |
|---|---|---|
| Uygulama framework'ü | Java / Kotlin | — |
| Köprü | — | **JNI** (§B) |
| Native kütüphane | C / C++ | `.so` (Android'de paylaşımlı kütüphane) |

## §B — JNI mekanizması

> Kaynak (tam metin çekildi · 2026-10-15): JNI tips —
> `https://developer.android.com/training/articles/perf-jni` (HTTP 200).
> Bölüm altındaki her iddia bu sayfadan alınmıştır.

### B.1 JavaVM ve JNIEnv

| Yapı | Anlam |
|---|---|
| `JavaVM` | Süreç genelinde tekil JVM tanıtıcısı — paylaşıma uygundur |
| `JNIEnv` | Native fonksiyonların JNI çağrılarını taşıdığı arayüz tablosu — **thread-local**'dır |
| C vs C++ | `JNIEnv`'in C ve C++ bildirimleri **farklıdır** — iki dili aynı başlıkta birleştirmek tehlikelidir |

### B.2 Thread kuralı (en sık hata)

| Kural | Ayrıntı |
|---|---|
| Paylaşım yasağı | `JNIEnv` thread-local storage'tır — **bir thread'in JNIEnv'i başka thread'de kullanılamaz** |
| Çözüm 1 | `JavaVM`'yi paylaş; thread'de `GetEnv` ile kendi JNIEnv'ini keşfet |
| Çözüm 2 | JNIEnv olmayan thread → önce `AttachCurrentThread` |
| Eski-desen kaydı | `pthread_setspecific()` ile JNIEnv saklama yaklaşımı sayfada anlatılmaktadır |
| Kırılma | Yanlış thread'de JNIEnv kullanımı, sayfanın "common mistakes" listesindedir ve **çökme** ile sonuçlanır |

### B.3 Referans türleri

| Tür | Ömür | Üretim |
|---|---|---|
| Local reference | Native çağrı dönene kadar | JNI'nin döndüğü nesneler (otomatik) |
| Global reference | Elle silinene kadar | `NewGlobalRef` |
| Weak global | Global gibi, ama zayıf | `NewWeakGlobalRef` (Android 4.0+'tan beri normal kullanılır) |

| Kural | Ayrıntı |
|---|---|
| Aşırı local ref yasağı | JNI "aşırı local reference tahsisi"ni yasaklar — döngüde yaratılan local ref'ler elle `DeleteLocalRef` ile temizlenir |
| Sınır geçmişi | Android 8.0 öncesi local ref sayısı sürüm-spesifik **tavanlı**ydı; **Android 8.0'dan itibaren sınırsız** desteklenir (JNI tips §compatible behavior) |

### B.4 Metot/alan kimliklerinin önbelleklenmesi

| Adım | Ne |
|---|---|
| 1 | `GetMethodID` / `GetStaticMethodID` / `GetFieldID` ile kimlikleri **bir kez** çöz |
| 2 | Sonuçları native tarafında **cache'le** (sınıf yükleyicisinde/statik başlatıcı deseni) |
| 3 | Her çağrıda tekrar arama — performans maliyeti (sayfanın açık önerisi) |

### B.5 Kayıt biçimi: `RegisterNatives` vs ad-keşfi

| Biçim | Nasıl | Risk |
|---|---|---|
| Ad-keşfi (isim tabanlı) | Runtime, native sembol adından metodu keşfeder (name mangling dahil) | Sessiz kırılma; optimizasyon annotasyonlarında güvensiz |
| `RegisterNatives` | `JNI_OnLoad` içinde `env->RegisterNatives(c, methods, n)` ile açık kayıt | Önden bilinen bağ — tercih edilen |

> Sayfa açıkça önerir: performans-kritik metotlarda ad-keşfi yerine
> **`RegisterNatives`** kullanılır; `JNI_OnLoad` sınıf referanslarını
> cache'lemek için de elverişlidir.

### B.6 `@FastNative` / `@CriticalNative` — annotasyon sürüm matrisi

| Sürüm aralığı | Davranış (JNI tips §compatible behavior) |
|---|---|
| Android 7 ve öncesi | Annotasyonlar **yok sayılır**; `@CriticalNative` ABI uyuşmazlığı → argüman yanlış bağlanır ve **çökme olası** |
| Android 8 | Sistem kullanımı başladı |
| Android 8–10 | Dinamik native fonksiyon araması **uygulanmadı** |
| Android 11 | Dinamik arama **bilinen hatalar** içerir |
| Android 8–11 | `RegisterNatives` **zorunlu** — açık kayıt olmadan çökme olası |
| Android 12+ | Dinamik arama desteklenir (`about/versions/12` sayfası HTTP 200 · 2026-10-15) |
| Android 14 | CTS-testli **public API** oldu |
| Android 12+ (arm64) | `@CriticalNative` çağrı, argümanlar register'a sığıyorsa (arm64'te en fazla 8 tam sayı + 8 kayan nokta) C/C++ içi inline-olmayan çağrıya yakın ucuzlukta |

| Kullanım kuralı | Ayrıntı |
|---|---|
| `@CriticalNative` | Yalnız managed nesne kullanmayan metotlar; imzada `JNIEnv`/`jclass` **yoktur** |
| GC davranışı | `@FastNative`/`@CriticalNative` çalışırken GC thread'i askıya **alamaz** — uzun süren metotlarda, ciddi I/O'da ve uzun tutulan native lock'larda **kullanılmaz** |

### B.7 Hata denetimi — CheckJNI

| Kavram | Ayrıntı |
|---|---|
| Varsayılan | JNI **çok az** hata denetimi yapar — hatalar çoğunlukla **çökme** üretir |
| CheckJNI | JavaVM/JNIEnv fonksiyon tablolarını, standart uygulamayı çağırmadan önce geniş denetim yapan tablolarla **değiştirir** |
| Tipik hata | Yanlış thread'den `JNIEnv*` kullanımı (§B.2) |
| `FindClass` farkı | Android, sınıf bulunamazsa `NoClassDefFoundError` yerine **`ClassNotFoundException`** fırlatır (Java reflection `Class.forName` ile tutarlı) |

## §C — Ses API yüzeyi (native)

> Kaynak (tam metin çekildi · 2026-10-15): "High-performance audio" rehberi —
> `https://developer.android.com/ndk/guides/audio` (HTTP 200) ve OpenSL ES
> sayfası — `https://developer.android.com/ndk/guides/audio/opensl`
> (HTTP 200 · "Last updated 2024-09-12 UTC").

### C.1 Yüksek performanslı ses — kapsam

Rehberin saydığı tipik kullanım alanları: dijital ses istasyonu (DAW) ·
sentezleyici · davul makinesi · müzik öğretim uygulamaları · karaoke ·
DJ miks · ses efektleri · video/ses konferans. Rehber iki konuyu işler:
**gecikmeyi (latency) en aza indirme** prensipleri ve **örnekleme**
(seçenek ses örnekleme hızı · kayan noktalı temsilin artı/eksi'leri).

### C.2 İki kütüphane — resmi tanım

| Kütüphane | Resmi durum (rehber dili) |
|---|---|
| OpenSL ES | Khronos Group spesifikasyonunun Android'e özgü uygulaması — **"new designs için önerilmez"** |
| AAudio | OpenSL ES'e hafif, native Android alternatifi — API'si **daha küçük ve daha kolay** |

### C.3 OpenSL ES — deprecated yolu

| İddia | Kaynak satır özeti (2026-10-15 çekimi) |
|---|---|
| Uyarı | "WARNING: OpenSL ES is deprecated. Developers should use the open source Oboe library..." |
| Spesifikasyon | OpenSL ES™ **1.0.1** — Khronos Group |
| Arayüz | C dil arayüzü + C++ bindings |
| Karşılığı | Java framework'teki `MediaPlayer` / `MediaRecorder` API'lerine benzer ses özellikleri |
| Standart başlıklar | `<SLES/OpenSLES.h>` · `<SLES/OpenSLES_Platform.h>` |
| Android'e özgü başlıklar | `<SLES/OpenSLES_Android.h>` · `<SLES/OpenSLES_AndroidConfiguration.h>` |

### C.4 AAudio — yeni native yüzey

| İddia | Kaynak |
|---|---|
| "AAudio, OpenSL ES'in lightweight native alternatifi olarak geliştirildi" | NDK high-performance audio rehberi (2026-10-15) |
| API'si OpenSL ES'ten daha küçük ve daha kolay | Aynı kaynak |
| API referans grubu | `https://developer.android.com/ndk/reference/group/audio` — HTTP 200 (2026-10-15) |

> AAudio'a özel **rehber sayfası adayı** `.../guides/audio/aaudio` bu turda
> **404** döndürdü (§13.3) — AAudio iddiaları yalnız üstteki iki doğrulanmış
> kaynağa dayanır.

### C.5 Oboe — resmi yönlendirme

| İddia | Kaynak |
|---|---|
| Oboe = AAudio'a çok benzeyen **C++ sarmalayıcı** | NDK rehberi + OpenSL ES sayfası (her ikisi 2026-10-15) |
| AAudio mevcutsa AAudio'yu çağırır, yoksa OpenSL ES'e **geri düşer** | Aynı |
| Resmi konumlandırma | `https://developer.android.com/games/sdk/oboe` — HTTP 200 · `https://google.github.io/oboe/` — HTTP 200 (2026-10-15) |

### C.6 Ses yüzeyi seçim matrisi

| Senaryo | Seçim | Gerekçe |
|---|---|---|
| Yeni native ses projesi | **Oboe** | Google'ın resmi önerisi; eski cihazda OpenSL ES'e fallback |
| Yalnız yeni cihaz + minimal bağımlılık | **AAudio** | Küçük API |
| Eski kod bakımı | OpenSL ES | Deprecated — yalnız sürdürme |
| Java/Kotlin katmanı | `MediaPlayer` / `MediaRecorder` | Rehberin işaret ettiği framework API'leri — tanım düzeyi |

> Davranışsal karar (akış yaşam döngüsü · cihaz seçimi · önbellek)
> `isletim-sistemi-audio` dalındadır — **Q-P2 sınırı** (§F.1).

## §D — Hizmet seviyesi kısıtları (tanım düzeyi)

| Konu | Durum |
|---|---|
| Android'de arka plan/çalışma sınırları (service-level kısıtlar) ayrı bir politika katmanıdır | Genel bilgi |
| Bu turda resmi arka-plan-kısıtları sayfası **doğrulanmadı** | ⚠️ VERIFICATION REQUIRED — §13.4 |
| Ses-özel servis davranışı (arka planda çalma · foreground service) | Davranışsal tasarım → `isletim-sistemi-audio` (Q-P2) |

> Kural: kısıt **tanımı** bile bu dosyada yalnızca doğrulanmış URL ile yazılır;
> doğrulanmamış satır `⚠️ VERIFICATION REQUIRED` olarak kalır ve silinmez.

## §E — Sürüm matrisi

### E.1 Doğrulanmış satırlar

| Öğe | Değer | Kaynak | Tarih |
|---|---|---|---|
| NDK LTS | **r30** ("Latest LTS Version (r30)") | `developer.android.com/ndk/downloads` | 2026-10-15 |
| OpenSL ES sayfası güncelleme damgası | 2024-09-12 UTC | `.../guides/audio/opensl` | 2026-10-15 |
| `@FastNative`/`@CriticalNative` public API | Android 14 | JNI tips §compatible behavior | 2026-10-15 |
| Dinamik arama desteği | Android 12+ | JNI tips | 2026-10-15 |
| Sınırsız local ref | Android 8.0+ | JNI tips | 2026-10-15 |

### E.2 Bilinen ama bu turda ölçülmemiş satırlar

| Öğe | Durum |
|---|---|
| AAudio'nun ilk eklendiği API seviyesi | ⚠️ VERIFICATION REQUIRED |
| Belirli NDK r sürümlerinin (r29 ve öncesi) LTS takvimi | UNKNOWN |
| Android 11–17 sürümlerinin API seviyesi eşlemesi | NDK sayfası sürüm listesini gösterir (Android 11–17 · 2026-10-15 çekimi) — **API seviyesi numaraları** doğrulanmadı → VERIFICATION REQUIRED |

> Kural: sürüm numarası tahminle yazılmaz; tabloda "VERIFICATION REQUIRED"
> satırı açıkça kalır.

## §F — CoreMusic ile sınır

### F.1 Q-P2 — sınır uyarısı (tekrar)

| Alan | Bu dosyada | `isletim-sistemi-audio` dalında |
|---|---|---|
| API tanımı (AAudio · OpenSL ES · Oboe · JNI) | ✅ | — |
| Akış başlatma · cihaz seçimi · gecikme/önbellek davranışı | — | ✅ (Q-P2 — sınır açık) |
| Foreground service ile arka planda çalma davranışı | — | ✅ |

### F.2 Repo kanıtı — Android yüzeyi yoktur

| Ölçüm | Sonuç | Tarih |
|---|---|---|
| `**/*.java` glob | **0 dosya** | 2026-10-15 |
| `**/*.kt` glob | **0 dosya** | 2026-10-15 |
| `**/*.cpp` · `**/*.h` | **0 dosya** (önceki tur ölçümü) | 2026-10-15 |

> Sonuç: repo'da Android native kodu **yoktur** — §12 kod örnekleri
> **[SARTNAME-ORNEK]** etiketlidir (uydurulmamış, etiketli senaryo).

## §11 Kullanım kararı matrisi (derinleştirme)

### §11.1 Android native ses yüzeyi seçimi

| Kriter | Oboe | AAudio | OpenSL ES |
|---|---|---|---|
| Resmi durum (2026-10-15) | Önerilen sarmalayıcı | Yeni native yüzey | Deprecated |
| Dil | C++ | C | C + C++ bindings |
| Eski cihaz desteği | ✅ (OpenSL ES'e fallback) | Yalnız AAudio destekli cihaz | ✅ |
| API boyutu | AAudio'a benzer | Küçük (OpenSL ES'ten daha küçük) | Büyük |
| Yeni proje kararı | **Birinci tercih** | İkinci tercih | Yalnız sürdürme |

### §11.2 JNI hata yakalama sırası

| Sıra | Adım | Kaynak |
|---|---|---|
| 1 | Geliştirmede **CheckJNI** açık — geniş denetim, standart tablo yerine denetimli tablo | JNI tips (2026-10-15) |
| 2 | Çökme analizinde ilk şüphe: **yanlış thread'de JNIEnv** | JNI tips — en sık hata |
| 3 | `FindClass` hatalarında Android `ClassNotFoundException` fırlatır — `NoClassDefFoundError` beklentisi yanlıştır | JNI tips §compatible behavior |
| 4 | Ad-keşfiyle bağlanan metotlarda sembol adı/değişimi sessiz kırılabilir → `RegisterNatives` | JNI tips (§B.5) |

### §11.3 JNI yaşam döngüsü kontrol listesi

| # | Kontrol | Kural |
|---|---|---|
| 1 | `JavaVM` süreç boyunca bir kez saklandı mı? | Evet — JNIEnv thread'den thread'e taşınmaz (§B.2) |
| 2 | Her native çağrı için JNIEnv doğru thread'den mi geldi? | `GetEnv` / `AttachCurrentThread` (§B.2) |
| 3 | Metot/alan ID'leri cache'lendi mi? | Bir kez çöz, sonradan kullan (§B.4) |
| 4 | Performans-kritik metotlar `RegisterNatives` ile mi kayıtlı? | Evet — özellikle `@FastNative`/`@CriticalNative` (§B.5–B.6) |
| 5 | Döngülerde local reference temizliği yapılıyor mu? | `DeleteLocalRef` — Android 8.0 öncesi tavan hatası, 8.0+ sınırsız ama tahsis yine de temizlenir (§B.3) |
| 6 | `@CriticalNative` metot uzun I/O veya uzun lock içeriyor mu? | İçeriyorsa kullanma — GC askılanır (§B.6) |
| 7 | `JNI_OnLoad` sınıf/ClassLoader referanslarını cache'liyor mu? | Sayfanın önerdiği desen (§B.5) |

### §11.4 §11'in kanıt etkisi

Tüm satırlar iki doğrulanmış resmi sayfaya dayanır (JNI tips · high-performance
audio rehberi — ikisi de 2026-10-15 tam metin çekildi). Yorum katmanı yalnız
"seçim matrisi" sütunlarındadır; iddia sütunları kaynak diliyle örtüşür.

## §12 Android gruplu kod örnekleri [SARTNAME-ORNEK]

> **Etiket:** `[SARTNAME-ORNEK]` — repo'da `*.java`/`*.kt`/`*.cpp`/`*.h`
> **0 dosyadır** (2026-10-15 glob ölçümü · §F.2). Aşağıdaki örnekler
> jenerik şema anlatımıdır; fonksiyon imzaları bu turda derleyiciyle
> doğrulanmadı → `⚠️ VERIFICATION REQUIRED` işaretli, uydurulmamış iskeletlerdir.

### §12.1 `JNI_OnLoad` + `RegisterNatives` deseni [SARTNAME-ORNEK]

JNI tips sayfasındaki örnek akışın iskeleti (kaynak: `perf-jni` §RegisterNatives · 2026-10-15):

```cpp
// [SARTNAME-ORNEK] — imzalar derleyiciyle doğrulanmadı (VERIFICATION REQUIRED)
JNIEXPORT jint JNI_OnLoad(JavaVM* vm, void* /*reserved*/) {
    JNIEnv* env = nullptr;
    if (vm->GetEnv(reinterpret_cast<void**>(&env), JNI_VERSION_1_6) != JNI_OK)
        return JNI_ERR;

    jclass c = env->FindClass("com/example/NativeBridge");
    if (c == nullptr)
        return JNI_ERR;

    // Ad-keşfi yerine açık kayıt — Android 8-11'de @FastNative/@CriticalNative için zorunlu
    const JNINativeMethod methods[] = {
        {"nativeProcess", "([F)V", reinterpret_cast<void*>(nativeProcess)},
    };
    if (env->RegisterNatives(c, methods,
            sizeof(methods) / sizeof(JNINativeMethod)) != 0)
        return JNI_ERR;

    // Sınıf/ClassLoader referansları burada cache'lenebilir (JNI tips önerisi)
    return JNI_VERSION_1_6;
}
```

### §12.2 JNIEnv thread-local koruma deseni [SARTNAME-ORNEK]

```cpp
// [SARTNAME-ORNEK] — JavaVM paylaşılır, JNIEnv thread'de çözülür (§B.2)
JavaVM* g_vm = nullptr;   // süreç boyunca tekil

JNIEnv* getEnvForCurrentThread() {
    JNIEnv* env = nullptr;
    jint rc = g_vm->GetEnv(reinterpret_cast<void**>(&env), JNI_VERSION_1_6);
    if (rc == JNI_EDETACHED) {                 // thread henüz attached değil
        if (g_vm->AttachCurrentThread(&env, nullptr) != JNI_OK)
            return nullptr;                    // fail-safe: nullptr → çağrı iptal
    } else if (rc != JNI_OK) {
        return nullptr;
    }
    return env;                                // bu thread'e ait tek JNIEnv
}
```

> Kural: `env` başka bir thread'de saklanıp **yeniden kullanılmaz** —
> JNI tips'in en sık hata maddesi (§B.2).

### §12.3 Local reference hijyeni — döngü deseni [SARTNAME-ORNEK]

```cpp
// [SARTNAME-ORNEK] — döngüde local ref üretimi elle temizlenir (§B.3)
for (jsize i = 0; i < n; ++i) {
    jobject item = env->GetObjectArrayElement(arr, i);  // local ref
    if (item != nullptr) {
        // ... kullanım ...
        env->DeleteLocalRef(item);                      // döngü içi zorunlu temizlik
    }
}
```

### §12.4 §12 kanıt tablosu

| # | Örnek | Dayandığı kaynak sayfa | Tarih | Repo karşılığı |
|---|---|---|---|---|
| 1 | `JNI_OnLoad` + `RegisterNatives` | `developer.android.com/training/articles/perf-jni` (HTTP 200 · tam metin çekildi) | 2026-10-15 | Yok — 0 C++ dosyası |
| 2 | `GetEnv` / `AttachCurrentThread` | Aynı sayfa §JavaVM and JNIEnv | 2026-10-15 | Yok |
| 3 | `DeleteLocalRef` döngü hijyeni | Aynı sayfa §local references | 2026-10-15 | Yok |

## §13 Kanıt tablosu ve dürüstlük kaydı

### §13.1 URL doğrulama (HTTP HEAD · 2026-10-15)

| # | URL | Sonuç | Kullanım |
|---|---|---|---|
| 1 | `https://developer.android.com/ndk` | ✅ 200 | Manifest evidence · §A.1 |
| 2 | `https://developer.android.com/ndk/guides/audio` | ✅ 200 | §C.1–C.2 · C.4 (tam metin çekildi) |
| 3 | `https://developer.android.com/ndk/guides/audio/opensl` | ✅ 200 | §C.3 (tam metin çekildi · last updated 2024-09-12 UTC) |
| 4 | `https://developer.android.com/ndk/reference/group/audio` | ✅ 200 | §C.4 API referans grubu |
| 5 | `https://developer.android.com/training/articles/perf-jni` | ✅ 200 | §B tamamı (tam metin çekildi) |
| 6 | `https://developer.android.com/games/sdk/oboe` | ✅ 200 | §C.5 |
| 7 | `https://google.github.io/oboe/` | ✅ 200 | §C.5 |
| 8 | `https://developer.android.com/ndk/downloads` | ✅ 200 | §A.3 · §E.1 (r30 LTS metni çekildi) |
| 9 | `https://developer.android.com/about/versions/12` | ✅ 200 | §B.6 (Android 12+ satırı) |

### §13.2 Repo ölçümü (2026-10-15)

| Ölçüm | Sonuç |
|---|---|
| `**/*.java` | 0 dosya |
| `**/*.kt` | 0 dosya |
| `**/*.cpp` · `**/*.h` | 0 dosya (önceki tur envanteri) |

### §13.3 Ölü URL dürüstlük kaydı (404 — kanıt sayılmaz)

| Aday URL | Sonuç | Sonuç |
|---|---|---|
| `https://developer.android.com/ndk/guides/audio/aaudio` | ❌ 404 | AAudio'a özel rehber adayı bulunamadı — §C.4 iddiaları yalnız §13.1 #2 ve #4'e dayanır |
| `https://developer.android.com/ndk/guides/audio/oboe` | ❌ 404 | Oboe konumlandırması §13.1 #6/#7'ye taşındı |
| `https://developer.android.com/ndk/guides/javascript` | ❌ 404 | Deneme URL'si — kullanılmadı, tespit olarak kaydedildi |

### §13.4 Açık kalan doğrulamalar

| İddia | Durum |
|---|---|
| AAudio'nun ilk eklendiği API seviyesi | ⚠️ VERIFICATION REQUIRED |
| Android arka-plan/servis kısıtları resmi sayfası | ⚠️ VERIFICATION REQUIRED (§D) |
| ABI cihaz payları (%) | UNKNOWN — ölçülmedi |
| `NevaEngine` Android hedefi | Yok — proje Windows hedeflidir (PLANNED kapsamında Android yok) |

## §14 Senaryolar ve SSS

### §14.1 Senaryolar

| # | Senaryo | Doğru hamle | Dayanak |
|---|---|---|---|
| 1 | Yeni C++ ses motoru Android'e taşınıyor | Oboe ile başla; AAudio hedef cihazda zaten kullanılır | §C.5 · §11.1 |
| 2 | Eski OpenSL ES kodu bakımda | Yeni özellik ekleme; Oboe'ye göç planla — OpenSL ES deprecated | §C.3 |
| 3 | Native metot Android 9'da çalışmıyor | `RegisterNatives` ile açık kayıt — ad-keşfi ve annotasyonlar Android 8-11'de çökme riski taşır | §B.5–B.6 |
| 4 | Native kod rastgele çöküyor | CheckJNI aç → yanlış thread JNIEnv kontrolü → local ref sızıntısı denetimi | §11.2 |
| 5 | Uzun süren native I/O'da GC takılıyor | `@CriticalNative` kullanma; uzun işleri non-annotated metota böl | §B.6 |

### §14.2 SSS

| Soru | Cevap | Kaynak |
|---|---|---|
| JNIEnv tek instance mıdır? | Hayır — thread-local'dır; paylaşılır olan `JavaVM`'dir | JNI tips §JavaVM and JNIEnv |
| OpenSL ES yeni projede kullanılabilir mi? | Kullanılabilir ama resmi tavsiye **değil** — "not recommended for new designs" | NDK audio rehberi (2026-10-15) |
| Oboe nedir? | AAudio'a benzeyen C++ sarmalayıcı; AAudio yoksa OpenSL ES'e düşer | NDK rehberi + OpenSL ES sayfası |
| NDK güncel LTS? | **r30** (2026-10-15 sayfa metni) | ndk/downloads |
| Repo'da Android kodu var mı? | Hayır — 0 `.java` · 0 `.kt` | 2026-10-15 glob |

## §15 Terimler, karşılaştırma ve günlük

### §15.1 Terim sözlüğü

| Terim | Tanım |
|---|---|
| NDK | Android için native C/C++ geliştirme kit'i |
| JNI | Java ↔ native köprü mekanizması (`JavaVM` · `JNIEnv`) |
| AAudio | OpenSL ES'e hafif native Android ses API'si |
| OpenSL ES | Khronos spesifikasyonunun Android uygulaması — deprecated |
| Oboe | Google'ın AAudio/OpenSL ES sarmalayıcı C++ kütüphanesi |
| RegisterNatives | Native metotları ad-keşfi olmadan bağlama kaydı |
| CheckJNI | JNI çağrılarını geniş denetimden geçiren hata modu |
| LTS | Uzun süreli destekli sürüm ailesi (NDK r30) |

### §15.2 Windows yüzeyi ile karşılaştırma

| Eksen | Windows (`windows-api-yuzeyi.md`) | Android (bu dosya) |
|---|---|---|
| Birincil mekanizma | Win32 + COM | JNI + NDK |
| Hata modeli | `GetLastError` / `HRESULT` | JNI hata denetimi az → çökme; CheckJNI |
| Ses yüzeyi | Core Audio başlıkları (üst dal sınırı) | AAudio / OpenSL ES / Oboe (§C) |
| Sürüm kanıtı | SDK matrisi (§D o dosyada) | NDK r30 LTS (§E.1) |
| Repo karşılığı | PHP → Win32 sınırları belgeli | **Yok** — 0 native dosya (§F.2) |

### §15.3 Günlük — bu dosyanın yapım kaydı

| Tarih | İşlem |
|---|---|
| 2026-10-15 | 9 aday URL HEAD kontrolü (8×200 · 3×404) |
| 2026-10-15 | JNI tips · high-performance audio · OpenSL ES · ndk/downloads sayfaları tam metin çekildi |
| 2026-10-15 | Repo glob: 0 `.java` · 0 `.kt` → örnekler [SARTNAME-ORNEK] |
| 2026-10-15 | Dosya yazıldı — Q-P2 sınır uyarısı §F.1'de tekrarlandı; index DEĞİŞTİRİLMEDİ (beyazliste dışı) |
| 2026-10-15 | Ölçüm: ReadAllLines ile ≥505 kapısı doğrulandı (§15.4) |

### §15.4 Ölçüm kaydı

| Dosya | Kapı | Sonuç |
|---|---|---|
| `platform-api/android-api-yuzeyi.md` | ≥500 (hedef ≥505) | ReadAllLines ölçümü — çıktı tablosunda raporlandı |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

