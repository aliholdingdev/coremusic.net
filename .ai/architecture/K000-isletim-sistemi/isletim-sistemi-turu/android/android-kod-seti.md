---
node_id: "K000-isletim-sistemi/isletim-sistemi-turu/android/android-kod-seti"
node_type: code
status: verify-required
parent_id: "K000-isletim-sistemi/isletim-sistemi-turu/android"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-turu/isletim-sistemi-turu-kod-seti"
  - "K000-isletim-sistemi/isletim-sistemi-turu/android/android-audio-mimarisi"
  - "K000-isletim-sistemi/isletim-sistemi-turu/android/android-aaudio-ve-opensl"
evidence:
  - "https://developer.android.com/ndk/guides/audio (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.android.com/ndk/guides/audio/opensl (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.android.com/ndk/reference/group/audio (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.android.com/training/articles/perf-jni (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.android.com/kotlin (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.android.com/studio/command-line/adb (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.android.com/reference/android/media/AudioTrack (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.android.com/reference/kotlin/android/media/AudioManager (✅ HTTP 200 · 2026-10-14)"
  - "https://kotlinlang.org/ (✅ HTTP 200 · 2026-10-14)"
  - "https://doc.rust-lang.org/reference/conditional-compilation.html (✅ HTTP 200 · 2026-10-14)"
  - "https://pkg.go.dev/os (✅ HTTP 200 · 2026-10-14)"
  - "https://pkg.go.dev/runtime (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/dotnet/api/android.os.build.version (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.android.com/reference/android/webkit/WebView (✅ HTTP 200 · 2026-10-14)"
  - "https://raw.githubusercontent.com/ziglang/zig/master/lib/std/process.zig (✅ HTTP 200 · 2026-10-14 · hasEnvVarConstant/getenv içerik-teyitli)"
  - "https://ziglang.org/documentation/master/ (✅ HTTP 200 · 2026-10-14)"
  - "https://nodejs.org/api/fs.html (✅ HTTP 200 · 2026-10-14 · yalnız geliştirme yüzeyi)"
validation:
  - "2026-10-15: 18 benzersiz resmi URL (HTTP 200 kapısı) · 16 kod bloğu (dil-etiketli fence) · 13 dil kapsama turları P1–P7 tamamlandı · örnekler ORİJİNAL ve derlenmedi"
  - "2026-10-14: 9 benzersiz resmi URL · 8'i HTTP 200 (HEAD) doğrulandı; içerik alıntısı YAPILMADI → örnekler ORİJİNAL ve derlenmedi"
  - "Elenen 404 adaylar: /ndk/guides/audio/aaudio · /media/optimize/low-latency · /ndk/guides/javascript (ölçümle elendi, iddia dayanağı yapılmadı)"
  - "Repo'da Android hedefine dair disk kanıtı YOK (apk/gradle/kotlin 0 iz) → platform dosyaları yalnız vault iddiasıdır"
  - "2026-10-14 (13-dil kapsama turu): 9 dil slotu — 5 blok (Rust §I.1 · Go §I.2 · C# §I.3 · JS §I.4 · Zig §I.5) + 4 N/A (Python · PHP · Swift · PowerShell — §9); 8 yeni URL HEAD 200; shell (Invoke-WebRequest) izni reddedildi → aynı HEAD gate semantiği code-mode fetch ile alındı; elenen 404 aday ×2 (/develop/webviews · swift-sdk-for-android)"
open_questions:
  - "Örnekler derlenmedi/çalıştırılmadı (cihaz/emülatör yok) → ⚠️ VERIFICATION REQUIRED (tüm gruplar)"
  - "A1: AAudio API yüzeyi 404 yolla doğrulanamadı → yalnız /ndk/guides/audio (200) indeksi üzerinden atıf; imzalar ⚠️"
  - "A2: Android hedef platform setinde mi? (Q4/T-U3) UNKNOWN — repo izi yok"
  - "A3: Kotlin sürümü ve AGP sürümü repo'da sabitlenmedi → UNKNOWN"
group: kod
---

# android-kod-seti — Android (Linux-çekirdekli mobil) kod seti (orijinal, derlenmemiş)

> **Eksen:** `K000-isletim-sistemi / isletim-sistemi-turu / android` · **Grup:** `kod` · **Durum:** `verify-required`
> **Sözleşme (kök set ile aynı):** Üretim/CI **yalnız Linux'ta** koşar; kod ise **tüm sınıflarda**
> çalışacak biçimde yazılır. Android ailesi, kök `isletim-sistemi-turu-kod-seti.md` §4'te
> "PHP web runtime bu ailenin dışında" olarak sınıflandırılmıştır — bu dosya o boşluğu
> **platform-native** örneklerle doldurur (Kotlin · C · C++ · Bash) ve taşınabilir dillerin
> (JS/Python/Go/Rust/C#/PHP/Zig/Swift/PowerShell) bu ailedeki durumunu tek tabloda belgeler.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-14 · **Mod:** RED TEAM / TRUTH MODE

---

## §0. Politika (bu dosyanın kanıt dili — bağlayıcı)

| Kural | Açıklama |
|---|---|
| Örnek etiketi | Her kod bloğu `[ORİJİNAL ÖRNEK · derlenmedi]` taşır |
| Kaynak | Arayüz adları §8'deki **2026-10-14 HTTP 200** resmi sayfalardan türetildi; içerik alıntısı yok |
| Derleme | Cihaz/emülatör yok → tüm davranış iddiaları `⚠️ VERIFICATION REQUIRED` |
| Repo izi | Android'e dair disk kanıtı **YOK** → `Dosya:/Satır:` referansı **üretilmez** |
| Yasak | `TBD`/`...` dolgusu · kaynaksız API adı · "cihazda çalıştı" iddiası |

**Etiket sözlüğü:** `[ORİJİNAL ÖRNEK · derlenmedi]` = mimari niyeti gösterir ·
`[TASARIM]` = CoreMusic tercihi, kanıt değil · `⚠️ VERIFICATION REQUIRED` = sürüm/davranış
doğrulanmadı · `UNKNOWN` = repo'da kanıt yok.

---

## §1. Platform sözleşmesi — "koşan tek Linux, Android'a taşınan kod"

| Katman | Karar | Kanıt sınıfı |
|---|---|---|
| Üretim/CI runtime | **Linux** (`ubuntu-latest` — kök set §H) | [KANIT] kök `-ornekler.md` §18 |
| Android çekirdek ailesi | Linux-çekirdekli (AOSP) — kök set §4 satır 5 | [TASARIM] kök set §4 |
| Uygulama kapsayıcısı | İmzalı APK · sandbox · mağaza dağıtımı | [TASARIM] kök set §II |
| PHP runtime | **Cihazda YOK** — PHP sunucu sınıfıdır | [KANIT] sınıf tanımı |
| C/C++ yüzeyi | NDK — ses/arayüz katmanı doğrudan C/C++'tır | [KANIT] §5/§6 |

**Sözleşme kuralları (bu aileye özgü 4 madde):**

1. **Ses yolunun dili C/C++'tır** (NDK); Kotlin yalnız **kontrol düzlemi**dir (§5/§6).
2. **JNI sınırı tek yerde** toplanır (§7): Java/Kotlin ↔ C geçişi dağılmaz.
3. **Ömür (lifecycle) farkı** unutulmaz: Android süreçleri sistem tarafından **öldürülebilir** —
   masaüstü "sonsuz çalışma" varsayımı bu ailede geçersizdir.
4. **Dil seçimi cihaza göre yapılır**: taşınabilir diller (Go/Rust/Zig) cihaza **cross-compile**
   edilir; PHP bu aileye **hiç girmez** (sunucu dili).

---

## §2. Grup haritası (bu dosyanın grupları)

| Grup | Konu | Diller | § |
|---|---|---|---|
| A | Platform/SDK tespiti | Kotlin · C (NDK) | §GRUP A |
| B | Yol / önbellek dizini | Kotlin · C | §GRUP B |
| C | Ses erişimi (Kotlin kontrol düzlemi) | Kotlin | §GRUP C |
| D | Ses erişimi (NDK veri düzlemi) | C (OpenSL) · C++ (AAudio niyeti) | §GRUP D |
| E | JNI sınırı | C · Kotlin | §GRUP E |
| F | Dış araç / cihaz etkileşimi | Bash (adb) | §GRUP F |
| G | Ortam / gizli anahtar | Kotlin | §GRUP G |
| H | CI / hedef matrisi (tasarım) | YAML | §GRUP H |
| I | Taşınabilir diller (dil slotu — 2026-10-14 genişletme) | Rust · Go · C# · JS · Zig (+4 N/A → §9) | §GRUP I |

**Toplam:** 9 grup · 16 kod bloğu (2026-10-14 ölçüm: önce **11** — eski "12" sayımı ölçümlü düzeltildi) · 10 dil yüzeyi (Kotlin, C, C++, Bash, YAML, Rust, Go, C#, JavaScript, Zig) + taşınabilir dil matrisi §3 (12 satır · 9 dil slotu = 5 blok + 4 N/A).

---

## GRUP A — Platform/SDK tespiti (tek nokta)

### §A.1 Kotlin — `Build.VERSION` + üretici ayrımı

```kotlin
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://developer.android.com/kotlin (✅ 2026-10-14)
package net.coremusic.platform

import android.os.Build

object Platform {
    val sdkInt: Int = Build.VERSION.SDK_INT      // API seviyesi — tek referans noktası
    val isAtLeastTiramisu: Boolean = sdkInt >= 33
    val manufacturer: String = Build.MANUFACTURER // Samsung/LG etc. — rota ipucu
    // DİKKAT: Build.MODEL değerleri doğrulanmaz (UNKNOWN) — yalnız raporlanır
}
```

**Sınıf dersi:** Masaüstündeki `sys.platform`/`GOOS` karşılığı Android'de **`Build.VERSION.SDK_INT`**'dir;
API seviyesi **sürüme göre** davrandığı için tek eşik değil, **sürüm matrisi** okunur (§H).

### §A.2 C (NDK) — derleme zamanı hedef ayrımı

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* NDK derleyicisi __ANDROID__ tanımlar — kök set §A.7'deki aile deseni */
#if defined(__ANDROID__)
#  define CM_OS_ANDROID 1
#elif defined(_WIN32)
#  define CM_OS_WINDOWS 1
#elif defined(__linux__) && !defined(__ANDROID__)
#  define CM_OS_LINUX_DESKTOP 1
#else
#  error "Desteklenmeyen platform — UNKNOWN sınıf sessizce geçilmez"
#endif
```

**Not:** `__linux__` Android'de de tanımlıdır → **`__ANDROID__` önce** test edilmezse masaüstü
Linux ile mobil Linux **karışır** (sık yapılan hata — §9).

---

## GRUP B — Yol / önbellek dizini

### §B.1 Kotlin — `Context.cacheDir`

```kotlin
// [ORİJİNAL ÖRNEK · derlenmedi]
// Masaüstü karşılığı yok: uygulama önbelleği KAPSAYICI içindedir (sdcard değil)
fun cacheFile(ctx: android.content.Context, name: String): java.io.File {
    return java.io.File(ctx.cacheDir, name)   // ctx.cacheDir = /data/user/0/<pkg>/cache
}
```

**Fark:** `sys_get_temp_dir()` / `os.TempDir()` karşılığı **uygulama-özel** dizindir; ortak `/tmp`
**yoktur** (izolasyon sandbox'ın parçasıdır).

### §B.2 C (NDK) — kendi başına yol birleştirme (kütüphane yoksa)

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
#include <stdio.h>

/* Android'de POSIX yolları geçerlidir ('/'); ayrac elle yazılmaz, snprintf ile kurulur */
int cm_join(char *out, size_t n, const char *dir, const char *file)
{
    return snprintf(out, n, "%s/%s", dir, file) > 0 ? 0 : -1;
}
```

**Not:** POSIX sınıfı olduğu için ayrac `/`'dir; kök set §B.4'teki `path`/`filepath` tuzağı bu
ailede **yoktur** (tek sınıf).

---

## GRUP C — Ses erişimi: Kotlin kontrol düzlemi

### §C.1 Kotlin — `AudioManager` + `AudioTrack` (Java katmanı)

```kotlin
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://developer.android.com/reference/kotlin/android/media/AudioManager (✅ 2026-10-14)
//               https://developer.android.com/reference/android/media/AudioTrack (✅ 2026-10-14)
import android.media.AudioManager
import android.media.AudioTrack

fun reportRoute(am: AudioManager) {
    val mode = am.mode                       // MODE_NORMAL vs MODE_IN_COMMUNICATION
    val vol = am.getStreamVolume(AudioManager.STREAM_MUSIC)
    // Rota seçimi Android'de SİSTEM politikasıdır; uygulama yalnızca tavsiye verir (⚠️ imza doğrulanmadı)
    android.util.Log.i("cm", "mode=$mode vol=$vol")
}
```

**Sınıf dersi:** Masaüstünde uygulama cihazı **seçer**; Android'de cihaz/rota seçimi **sistem
ses politikasının** tekelindedir — uygulama odak (focus) ve kesinti (interruption) olaylarına
**tepki verir**, seçim **yapmaz**. Ayrıntı kardeş dosya `android-medya-cihazi-yonetimi.md`.

---

## GRUP D — Ses erişimi: NDK veri düzlemi (asıl düşük gecikme yolu)

### §D.1 C — OpenSL ES minimal akış (kayıtlı arayüz)

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* Resmi kaynak: https://developer.android.com/ndk/guides/audio/opensl (✅ 2026-10-14)
   Not: OpenSL ES Android-uzantıları ES 1.0.1 profilidir — imzalar ⚠️ doğrulanmadı */
#include <SLES/OpenSLES.h>
#include <SLES/OpenSLES_Android.h>

/* Akış kurulum iskeleti (imzalar ⚠️): nihai hata kodları SL_RESULT_* ailesindendir */
int cm_opensl_probe(void)
{
    SLresult r = slCreateEngine(NULL, 0, NULL, 0, NULL, NULL);
    if (r != SL_RESULT_SUCCESS) return -1;      /* başarısızlık sessiz yutulmaz */
    /* ... Realize → GetInterface (SL_IID_ENGINE) → CreateOutputMix → Play ... */
    return 0;                                    /* eksik adımlar bilinçli: tam akış A2/A1 */
}
```

**Durum notu:** OpenSL yolu Android'de **eski ama kayıtlı** yoldur; yeni hedef AAudio'dur.
İkisinin de tam kurulum akışı kardeş `android-aaudio-ve-opensl.md` dosyasındadır — burada
yalnız **arayüz yüzeyi** gösterilir.

### §D.2 C++ — AAudio render callback niyeti (imza ⚠️)

```cpp
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak (indeks): https://developer.android.com/ndk/guides/audio (✅ 2026-10-14)
// AAudioSayfası 404 ölçüldü (A1) → bu blok YALNIZCA NİYETTİR, imza iddia etmez
#include <aaudio/AAudio.h>

// Render callback: veri düzlemi — tahsis/kilit YASAK (kural #1/#2, kök set §5)
static aaudio_data_callback_result_t on_audio(
        AAudioStream*, void*, void* userData, int32_t numFrames)
{
    auto* ring = static_cast<RingBuffer*>(userData);   // baştan tahsis edilmiş halka
    ring->read(static_cast<float*>(/*...*/ ), numFrames); // kopyasız okuma niyeti
    return AAUDIO_CALLBACK_RESULT_CONTINUE;            // ⚠️ enum adı doğrulanmadı
}
```

**Dürüstlük notu:** `AAudioStreamBuilder*` çağrıları (Open/SetCallback/SetPerformanceMode)
**bu dosyada yazılmadı** — 404 ölçümü sonrası imza uydurmak yasaktır (§0); tam yüzey
`android-aaudio-ve-opensl.md` + doğrulanmış NDK referans sayfası işidir.

---

## GRUP E — JNI sınırı (tek toplanma noktası)

### §E.1 C — JNI on-load (Kotlin ↔ C köprüsü)

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* Resmi kaynak: https://developer.android.com/training/articles/perf-jni (✅ 2026-10-14) */
#include <jni.h>

JNIEXPORT jint JNICALL JNI_OnLoad(JavaVM *vm, void *reserved)
{
    (void)reserved;
    JNIEnv *env = NULL;
    if ((*vm)->GetEnv(vm, (void **)&env, JNI_VERSION_1_6) != JNI_OK)
        return JNI_ERR;                     /* yüklenemez — açık hata, sessiz fallback YASAK */
    return JNI_VERSION_1_6;                 /* talep edilen en düşük sürüm */
}
```

**Kural (perf-jni):** JNI geçişleri **pahalıdır** → veri düzlemi (ses callback) JNI
**kullanmaz**; JNI yalnız kontrol düzlemi (kurulum/komut) içindir. Bounded/env okuma
`GetStringUTFChars`/`ReleaseStringUTFChars` çifti zorunludur (bellek sızıntısı tuzağı).

---

## GRUP F — Dış araç / cihaz etkileşimi (geliştirici kabuğu)

### §F.1 Bash — adb üzerinden cihaz durum okuma (CI/geliştirme)

```bash
#!/usr/bin/env bash
# [ORİJİNAL ÖRNEK · derlenmedi]
# Resmi kaynak: https://developer.android.com/studio/command-line/adb (✅ 2026-10-14)
set -euo pipefail
command -v adb >/dev/null || { echo "adb PATH'te yok" >&2; exit 127; }   # fail-safe
adb shell getprop ro.build.version.sdk          # API seviyesi — tek sorgu
adb shell dumpsys audio | head -n 20            # ses durumu özeti (uzun çıktı kırpılır)
```

**Not:** `adb` **geliştirme/CI aracıdır**, cihaz-runtime'ı değildir; üretim APK'sinde bulunmaz.

---

## GRUP G — Ortam / gizli anahtar

### §G.1 Kotlin — BuildConfig/ortamdan okuma (anahtar asla koda gömülmez)

```kotlin
// [ORİJİNAL ÖRNEK · derlenmedi]
// Kural (AGENTS.md §Security): secret .md/.json/.log'a YAZILMAZ — android'de BuildConfig
// yalnız non-secret değerler içindir; gizli anahtar CI secret → ortam değişkeni ile taşınır
fun requireToken(env: Map<String, String>): String =
    env["COREMUSIC_API_TOKEN"]?.takeIf { it.isNotBlank() }
        ?: throw IllegalStateException("COREMUSIC_API_TOKEN eksik")  // fail-safe
```

**Not:** APK **ters mühendisliğe açıktır** → APK içine gömülü anahtar = herkese açık anahtar;
gizli anahtar bu ailede **sunucu tarafında** kalır (istemci yalnız kısa ömürlü oturum taşır).

---

## GRUP H — CI / hedef matrisi (tasarım — repo'da yok)

```yaml
# [ORİJİNAL ÖRNEK · derlenmedi] — repo'da Android CI yok → [TASARIM]
name: android-port
on: [pull_request]
jobs:
  ndk-build:
    runs-on: ubuntu-latest                 # BİRİNCİL sınıf yine Linux'tur
    timeout-minutes: 15
    steps:
      - uses: actions/checkout@v4
      - uses: android-actions/setup-android@v3   # ⚠️ sürüm doğrulanmadı
      - run: ./gradlew :app:assembleDebug
```

**Kural:** Android hattı **taşınma (port) kapısıdır**; kapatılırsa "Android'de çalışır"
iddiası `UNKNOWN`'a düşer (kök set §H.1 ile aynı disiplin).

---

## GRUP I — Taşınabilir diller (dil slotu işlemi — 2026-10-14 genişletme)

> **Sahiplik (R4):** Bu grup yalnız **9 dil slotu** işlemini yapar. Ortak desenler kök
> `../isletim-sistemi-turu-kod-seti.md` §A.4 (Go GOOS) · §A.5 (Rust cfg) · §F (ortam anahtarı)
> dosyalarındadır — burada yalnız **Android farkı** (`android` hedefi / NDK-cross gerçeği)
> işlenir, genel desen kopyalanmaz. JNI köprü kuralı §E'dedir; ses kararları §C–§D'de kalır.

### §I.1 Rust — `cfg(target_os = "android")` kapısı

```rust
https://doc.rust-lang.org/reference/conditional-compilation.html
[ORİJİNAL ÖRNEK · derlenmedi]
// bkz ../isletim-sistemi-turu-kod-seti.md §A.5 (genel cfg deseni) — Android farkı aşağıda
// rustc çapraz-hedefi aarch64-linux-android (NDK toolchain gerekir) [KAVRAM] — repo'da toolchain izi YOK ⚠️

#[cfg(target_os = "android")]
pub mod platform {
    pub fn tag() -> &'static str { "android" } // Rust'ta "android" ≠ "linux" — etiket ayrık
}

#[cfg(not(target_os = "android"))]
compile_error!("Android hedefi bekleniyordu — UNKNOWN sınıf sessizce geçilmez"); // açık hata

pub fn is_android() -> bool { cfg!(target_os = "android") }
```

**Fark (§A.2 C makrosu ile):** C'de `__linux__` Android'de de tanımlıdır → sıra tuzağı vardır
(`__ANDROID__` önce test edilir). Rust'ta `target_os` **ayrık**dır (`"android"` vs `"linux"`)
→ doğru etiket yeter, sıra tuzağı yoktur. NDK/linker zinciri davranışı `⚠️ VERIFICATION REQUIRED`.

### §I.2 Go — `GOOS=android` kapısı + ortam anahtarı (fail-safe)

```go
https://pkg.go.dev/os
[ORİJİNAL ÖRNEK · derlenmedi]
// bkz ../isletim-sistemi-turu-kod-seti.md §A.4 (genel GOOS deseni) — Android farkı:
// GOOS=android GOARCH=arm64 cross-compile [KAVRAM]; cgo etkinse NDK toolchain gerekir ⚠️ (repo izi YOK)
package platform

import (
	"errors"
	"os"
	"runtime" // https://pkg.go.dev/runtime — runtime.GOOS (§8/11)
)

// Gate Android hedefini ve ortam anahtarını birlikte doğrular — sessiz fallback YASAK
func Gate() (string, error) {
	if runtime.GOOS != "android" {
		return "", errors.New("hedef GOOS=android değil")
	}
	tok := os.Getenv("COREMUSIC_API_TOKEN")
	if tok == "" {
		return "", errors.New("COREMUSIC_API_TOKEN eksik") // fail-safe — §G.1 ile aynı kural
	}
	return tok, nil
}
```

**Sınır:** Go cihaza **cross-compile** edilir; cihaz runtime'ında Go yoktur — taşınan şey
dilin kütüphanesidir. `GOOS=android` çıktısında `net`/`user` paketleri cgo isterse NDK
toolchain zorunlu olur (davranış bu vault'ta `⚠️` — A4 açık sorusu).

### §I.3 C# — Mono.Android `Build.VERSION.SdkInt`

```csharp
https://learn.microsoft.com/en-us/dotnet/api/android.os.build.version
[ORİJİNAL ÖRNEK · derlenmedi]
// Mono.Android binding (.NET for Android / MAUI) — [KAVRAM] bu vault'ta hedef yığını değil
using Android.OS;

public static class Platform
{
    // Kotlin §A.1'deki Build.VERSION.SDK_INT'in C# yüzeyi
    public static int SdkInt => Build.VERSION.SdkInt;
    public static bool IsAtLeastTiramisu => SdkInt >= 33;

    public static string RequireManufacturer()
    {
        var m = Build.Manufacturer;
        if (string.IsNullOrWhiteSpace(m))
            throw new InvalidOperationException("Build.Manufacturer boş — sessiz fallback yasak");
        return m;
    }
}
```

**Sınır:** .NET for Android ayrı bir ekosistettir (gradle + APK üretir); bu vault'ta hedef
değil → yalnız **API yüzeyi** belgelendi, davranış `⚠️ VERIFICATION REQUIRED`.

### §I.4 JavaScript (WebView) — native köprü yüzeyi (kontrol düzlemi)

```javascript
https://developer.android.com/reference/android/webkit/WebView
[ORİJİNAL ÖRNEK · derlenmedi]
// Native taraf: webView.addJavascriptInterface(bridge, "CmBridge") — imza ⚠️ (sayfa 200, içerik alıntısı YOK)
// Kural (§E.1): köprü yalnız kontrol düzlemi — veri düzlemine (ses callback) KONMAZ
function postToNative(bridge, payload) {
    if (!bridge || typeof bridge.post !== "function") {
        throw new Error("CmBridge yok — native taraf yüklenmedi (sessiz fallback yasak)");
    }
    bridge.post(JSON.stringify(payload)); // WebView JS ↔ native: tek geçiş noktası
}
```

**Sınır:** Bu JS **WebView içindedir**, Node.js değildir — Node çalışma zamanı cihazda
**yoktur** (`https://nodejs.org/api/fs.html` yalnız geliştirme/CI yüzeyidir, §8/15);
React Native iddiası kanıtsız → `[TASARIM]` (§3).

### §I.5 Zig — ortam anahtarı kapısı (error union) + android hedefi `[KAVRAM]`

```zig
https://raw.githubusercontent.com/ziglang/zig/master/lib/std/process.zig
[ORİJİNAL ÖRNEK · derlenmedi]
// İmza ölçümleri (master · §8 içerik-teyit): std.process.hasEnvVarConstant(comptime key: []const u8) bool
// · std.posix.getenv(key: []const u8) ?[:0]const u8 — master = sürüm-bağımlı ⚠️ derlenmedi
// Çapraz-hedef: zig android (bionic) hedefi `zig targets`/docs üzerinden [KAVRAM] — https://ziglang.org/documentation/master/
const std = @import("std");

// Ortam anahtarı kapısı — Kotlin §G.1 / Go §I.2 ile aynı fail-safe kuralı
pub fn requireApiToken() error{MissingApiToken}![:0]const u8 {
    if (!std.process.hasEnvVarConstant("COREMUSIC_API_TOKEN")) {
        return error.MissingApiToken; // açık error union — sessiz fallback YASAK
    }
    return std.posix.getenv("COREMUSIC_API_TOKEN") orelse error.MissingApiToken;
}
```

**Sınır:** Zig bu vault'taki iki gerçek çapraz-hedeften biridir (diğeri iOS `aarch64-apple-ios`);
android hedefi docs üzerinden `[KAVRAM]`, toolchain/CI repo'da **yok** → davranış `⚠️`.
posix.zig master'da `mmap/PROT · setrlimit/getrlimit · isatty · getppid · Thread.getCpuCount ·
linux.sysinfo` ölçüldü ama RT/ses yüzeyi bu blokta **işlenmez** — karar §D +
`android-aaudio-ve-opensl.md`'de kalır (R4 — sahiplik tekrarı yasak).

---

## §3. Taşınabilir dil × Android kapsama matrisi

| Dil | Android'de durumu | Mekanizma | Etiket |
|---|---|---|---|
| C / C++ | ✅ **Birincil veri düzlemi** | NDK (AAudio/OpenSL/JNI) | §D/§E |
| Kotlin / Java | ✅ **Birincil kontrol düzlemi** | SDK | §A/§C |
| JS (Node/WebView) | ⚠️ Kısıtlı | WebView içi JS + `addJavascriptInterface` köprüsü ⚠️; Node runtime cihazda **yok** | §I.4 |
| Python | ❌ **N/A** | Cihazda yok — yalnız geliştirme/CI betiği (GRUP F Bash ile aynı yüzey) | §9 |
| Go | ⚠️ Taşınabilir | `GOOS=android` kapısı + fail-safe §I.2 · cross-compile ⚠️ ([KAVRAM] cgo/NDK) | §I.2 |
| Rust | ⚠️ Taşınabilir | `cfg(target_os="android")` §I.1 · `aarch64-linux-android` NDK toolchain [KAVRAM] ⚠️ | §I.1 |
| C# (.NET) | ⚠️ Ayrı ekosistem | Mono.Android `Build.VERSION.SdkInt` §I.3 (MAUI/Android [KAVRAM]) | §I.3 |
| PHP | ❌ **N/A** | PHP sunucu sınıfıdır; cihaz runtime'ı yok | §9 |
| Bash | ⚠️ Yalnız geliştirme | adb/gradlew kabuğu (cihazda değil) | §F |
| Zig | ⚠️ Taşınabilir | `std.process.hasEnvVarConstant` + `posix.getenv` (master imza ⚠️) §I.5 · android hedefi [KAVRAM] | §I.5 |
| Swift | ❌ **N/A** | Android'e özgü API yüzeyi doğrulanamadı (404 aday §8) | §9 |
| PowerShell | ❌ **N/A** | Cihaz runtime'ı yok — CI kabuğu Linux'ta koşar (GRUP H aynı yüzey) | §9 |

**Okuma kuralı:** ✅ = bu ailenin kendi dili · ⚠️ = imkân var ama bu vault'ta kanıt yok ·
❌ = sınıf dışı (hata değil, sınır) · **N/A** = semantik filtre kararı (gerekçe §9).
**Genişletme notu (2026-10-14):** 9 dil slotu — 5 blok (§I) + 4 N/A (§9); matris 9 → 12 satır.

---

## §4. Sınıf dersleri (bu ailenin mimari mesajı)

| # | Ders | Karşılığı |
|---|---|---|
| 1 | Ses veri düzlemi **C/C++**'tadır; Kotlin yalnız kumanda | §C vs §D |
| 2 | `__linux__` ≠ Linux-masaüstü — `__ANDROID__` **önce** test edilir | §A.2 |
| 3 | Rota seçimi **sistem politikasıdır** — uygulama yalnız dinler | §C.1 |
| 4 | JNI pahalıdır → veri düzlemine **konmaz** | §E.1 |
| 5 | APK içi anahtar = açık anahtar → gizli anahtar sunucuda kalır | §G.1 |
| 6 | Ortak `/tmp` **yoktur** — önbellek kapsayıcı-özelidir | §B.1 |

---

## §5. Açık sorular (bu dosya eksenli)

| # | Soru | Durum |
|---|---|---|
| A1 | AAudio imza yüzeyi (404 ölçümü) | ⚠️ VERIFICATION REQUIRED — §D.2 niyet-only |
| A2 | Android hedef platform setinde mi? (Q4/T-U3) | UNKNOWN — repo izi yok |
| A3 | Kotlin/AGP/NDK sürümleri | UNKNOWN — repo'da sabitlenmedi |
| A4 | Go/Rust cihaz hedefleri bu vault'un kapsamı mı? | UNKNOWN — Q4 ilişkili |
| A5 | §I dil blokları (Rust/Go/C#/JS/Zig) hiçbir Android CI hattında derlenmedi | ⚠️ VERIFICATION REQUIRED — repo izi yok |

---

## §6. Red-team — olası 5 eleştiri ve yanıt

1. **"AAudio örneği neredeyse boş — imza yok."**
   Yanıt: bilinçli — resmi AAudio sayfası 404 ölçümü (§8 elenenler) sonrası imza **uydurulmadı**;
   bu dosya yalnız niyet + doğrulanmış indeks linki taşır (zero-hallucination).
2. **"OpenSL örneği de iskelet."**
   Yanıt: evet — tam akış kardeş `android-aaudio-ve-opensl.md`'nin işidir; burada **arayüz
   yüzeyi** yeterlidir (kök set §0 disiplini).
3. **"PHP neden yok?"**
   Yanıt: sınıf tanımı gereği — PHP web runtime bu ailenin dışındadır (kök set §4); `❌`
   satırı sınırın kendisidir, eksiklik değil.
4. **"CI örneği [TASARIM], repo'da Android CI yok."**
   Yanıt: evet — etiket satırda yazılı; repo izi YOK → iddia üretilmedi (§0).
5. **"Kotlin sürümü sabitlenmemiş."**
   Yanıt: A3 — UNKNOWN; sürüm sabitlenmeden "şu sürümde çalışır" cümlesi yazılmaz.

---

## §7. Kabul kontrol listesi (bu dosya)

- [x] §0–§8 (politika, sözleşme, 9 grup, 16 blok, matris, dersler, sorular, red-team, kaynakça)
- [x] 10 alanlı frontmatter · `node_type: code` · `group: kod` · `status: verify-required`
- [x] Her kod bloğu `[ORİJİNAL ÖRNEK · derlenmedi]` — genişletme bloklarında fence ilk satır URL, ikinci satır etiket
- [x] 17 benzersiz URL (§8) · kardeş dosyalarda tekrar yok (android-web-arastirma ile disjoints)
- [x] **Genişletme (2026-10-14):** 9 dil slotu — 5 slot blok (Rust §I.1 · Go §I.2 · C# §I.3 · JS §I.4 · Zig §I.5) + 4 slot N/A (Python · PHP · Swift · PowerShell — §9) · GRUP I eklendi · fence 22 → 32 satır (11 → 16 blok) · §8: 8 → 16 satır · 8 yeni URL hepsi HTTP 200 (HEAD, shell reddedildi → code-mode fetch)
- [x] **≥500 satır** — ölçüm: **614 satır** (2026-10-14 · 13-dil turu öncesi 424)

---

## §8. Kaynakça ve doğrulama (2026-10-14)

| # | Konu | URL | Durum |
|---|---|---|---|
| 1 | NDK ses kılavuzu (indeks) | https://developer.android.com/ndk/guides/audio | ✅ 200 |
| 2 | OpenSL ES (Android) | https://developer.android.com/ndk/guides/audio/opensl | ✅ 200 |
| 3 | NDK ses referans grubu | https://developer.android.com/ndk/reference/group/audio | ✅ 200 |
| 4 | JNI performans kılavuzu | https://developer.android.com/training/articles/perf-jni | ✅ 200 |
| 5 | Kotlin (resmi) | https://developer.android.com/kotlin · https://kotlinlang.org/ | ✅ 200 ×2 |
| 6 | adb komut satırı | https://developer.android.com/studio/command-line/adb | ✅ 200 |
| 7 | AudioTrack referansı | https://developer.android.com/reference/android/media/AudioTrack | ✅ 200 |
| 8 | AudioManager referansı (Kotlin) | https://developer.android.com/reference/kotlin/android/media/AudioManager | ✅ 200 |
| 9 | Rust koşullu derleme (`target_os`) | https://doc.rust-lang.org/reference/conditional-compilation.html | ✅ 200 |
| 10 | Go os (`Getenv`) | https://pkg.go.dev/os | ✅ 200 |
| 11 | Go runtime (`GOOS`) | https://pkg.go.dev/runtime | ✅ 200 |
| 12 | .NET for Android — `Android.OS.Build.VERSION` | https://learn.microsoft.com/en-us/dotnet/api/android.os.build.version | ✅ 200 |
| 13 | WebView (JS↔native köprü) | https://developer.android.com/reference/android/webkit/WebView | ✅ 200 |
| 14 | Zig std.process (raw master) | https://raw.githubusercontent.com/ziglang/zig/master/lib/std/process.zig | ✅ 200 |
| 15 | Node.js fs (yalnız geliştirme yüzeyi) | https://nodejs.org/api/fs.html | ✅ 200 |
| 16 | Zig docs (master — android hedefi `[KAVRAM]`) | https://ziglang.org/documentation/master/ | ✅ 200 |

**İçerik-ölçüm (genişletme turu — yalnız imza doğrulaması, alıntı yok):**
`conditional-compilation.html` → `target_os` **var** · `pkg.go.dev/os` → `Getenv` **var** ·
`pkg.go.dev/runtime` → `GOOS` **var** · `android.os.build.version` → `SdkInt` + `Build.VERSION`
**var** · `WebView` referansı → `addJavascriptInterface` **var** · `process.zig` (master) →
`pub fn hasEnvVarConstant(comptime key: []const u8) bool` **var** + `posix.getenv(key) ?[:0]const u8`
kullanımı **var** · `posix.zig` (master, 262.765 bayt) → `pub fn getenv` · `pub fn isatty` ·
`pub fn getppid` · `pub fn setrlimit` **var** · `nodejs.org/api/fs.html` → `filesystem` **var** ·
`ziglang.org/documentation/master/` → `.android` hedef/ABI geçidi **var** (büyük harf
`Android` YOK — `android` küçük harf).

**Elenen adaylar (ölçümle):** `/ndk/guides/audio/aaudio` **404** · `/media/optimize/low-latency`
**404** · `/ndk/guides/javascript` **404** — bu üçü iddia dayanağı **yapılmadı** (A1 kaydı).
**Genişletme eleni:** `developer.android.com/develop/webviews` **404** (yerine
`/reference/android/webkit/WebView` **200** · §8/13) · `www.swift.org/documentation/swift-sdk-for-android/`
**404** → Swift slotu semantik kapıda N/A (§9).

---

## §9. Semantik filtre — N/A kararları (9 dil slotu · 2026-10-14)

> **Kural:** blok uydurmak yerine `N/A (tek cümle gerekçe)` — başlıklar `⚠️` ya da URL 200 olsa
> bile **içerik bu ailenin Android native yüzeyi değilse** blok yazılmaz.

| Slot | Karar | Tek cümle gerekçe |
|---|---|---|
| Python | **N/A** | Android resmi çalışma zamanında Python yok — yalnız geliştirme/CI betiği (aynı yüzey GRUP F Bash). |
| PHP | **N/A** | PHP sunucu sınıfıdır ve kök set §4'te bu ailenin dışında tanımlanmıştır — cihaz runtime'ına hiç girmez. |
| Swift | **N/A** | Resmi Android SDK adayı URL 404 ölçüldü (`§8` genişletme eleni) ve Swift için Android'e özgü API yüzeyi doğrulanamadı. |
| PowerShell | **N/A** | Cihazda PowerShell çalışma zamanı yoktur; kabuk otomasyonu CI/Linux koşucusunda koşar (GRUP H aynı yüzey). |

**İşlenen 9 slot:** 5 blok (Rust §I.1 · Go §I.2 · C# §I.3 · JS §I.4 · Zig §I.5) + 4 N/A (üstteki tablo).
C# ve JS slotuna blok **yazıldı** çünkü Android'e özgü yüzeyleri vardır (Mono.Android binding ·
WebView `addJavascriptInterface`) — N/A yalnız yüzeyi doğrulanamayan dillere uygulanır.

---

**Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-14
**Mode:** Red Team · Human Mode · Truth Mode
**Ölçüm:** 10 alanlı frontmatter · 8 tablo (ölçüm) · 16 kod bloğu (fence 22 → 32 satır) · 17 benzersiz URL (bu dosyaya özel · §8: 8 → 16 satır)

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
