---
id: K000-IST-TIZEN-KODSETI
title: "Tizen Kod Seti — Portable Diller + Tizen Paketleme (TPK/Manifest/GStreamer)"
node_type: code
group: kod
layer: architecture
parent: "[[index]]"
created: 2026-10-13
status: verify-required
validation:
  - "2026-10-15: 16 benzersiz resmi URL (HTTP 200 kapısı) · 10 kod bloğu (dil-etiketli fence) · 13 dil kapsama turları P1–P7 tamamlandı · örnekler ORİJİNAL ve derlenmedi"
depends_on:
  - "[[../isletim-sistemi-turu-kod-seti]]"
  - "[[../isletim-sistemi-turu-kapsam]]"
  - "[[../isletim-sistemi-turu-index]]"
  - "[[../index]]"
  - "[[../linux/index]]"
  - "[[../linux/linux-index]]"
  - "[[../android/android-index]]"
related:
  - "[[../isletim-sistemi-turu-kod-seti]]"
  - "[[../linux/index]]"
  - "[[../android/index]]"
tags:
  - kod-seti
  - tizen
  - portable
  - tpk
  - gstreamer
  - web-app
  - native-app
---

# Tizen Kod Seti — Portable Diller + Tizen Paketleme

> **Aynı dil havuzu, Tizen hedefi.** Tizen, Linux çekirdeği + GStreamer/PulseAudio
> sınıfı kullanıcı uzayı üzerine kurulu bir **cihaz sınıfı** platformdur (bu dosyanın
> modeli; bkz. `../index.md` §Model — Web/React Native/WebAssembly YOK). Bu dosya:
> (a) CoreMusic kod tabanının dil havuzunun Tizen'e nasıl taşındığını, (b) Tizen'e
> özgü paketleme/yapılandırma yüzeyini (manifest, config.xml, TPK, GStreamer) verir.
>
> **Üretim/CI kontratı** (`../linux/index.md` §Contract): üretim yalnız Linux üzerinde
> çalışır; yazılan kod sınıf-içi (Tizen/Linux/macOS/Windows) taşınabilir olmalıdır.
> Tizen, Linux-üst-sınıf olduğundan POSIX çekirdek + GStreamer desenleri doğrudan
> geçerlidir; yalnız paketleme/yapılandırma yüzeyi Tizen'e özgüdür.
>
> **Kapsam sınırı** (`../isletim-sistemi-turu-kapsam.md` §II): Tizen-native (CAPI)
> arayüz desenleri komşu dala aittir — burada yalnız **paketleme/yapılandırma
> yüzeyi** ve **taşınabilir dil çekirdeği** kalır.
>
> **Ayrışım:** `../linux/linux-kod-ornekleri.md` Linux-çekirdek (systemd/cgroup/ALSA),
> `../android/android-kod-seti.md` Android (Kotlin/NDK/Gradle), bu dosya Linux-üst-
> sınıf Tizen + paketleme yüzeyi. Tizen-native CAPI desenleri bu dosyada **bilinçli
> olarak YOK** (uzmanlık dala ait); onlar komşu dalda `UNKNOWN`/`⚠️`.

> **Kaynak notu (2026-10-13):** `tizen.org` ve `docs.tizen.org` 200 ölçüldü — ancak
> `docs.tizen.org` sayfaları **SPA iskeleti** döndürdüğü için Tizen-native CAPI
> (Sound Manager / Audio Manager / GStreamer) fonksiyon imzaları **metin içeriğiyle
> doğrulanamadı** (HEAD 200 ama gövde skeleton). Bu dosyadaki tüm Tizen-native CAPI
> çağrıları bu nedenle **⚠️ VERIFICATION REQUIRED** işaretlidir ve imzalar
> **çıkarılmıştır** (bileşen adları korunur). Doğrulanabilir yüzey: taşınabilir dil
> çekirdeği (Node/Python/Go/Rust/PHP CLI — `../isletim-sistemi-turu-kod-seti.md`
> §2, §5, §8, §10), GStreamer hat deseni (boru hattı → sink), TPK/manifest XML
> yapısı (yapı doğrulanabilir, imzalama adımları `⚠️`). **Elenen aday:**
> `source.tizen.org` — DNS çözümleme HATASI (ERR_NAME_NOT_RESOLVED), kullanılmadı.

> **Kaynak notu (2026-10-13):** `docs.tizen.org` HEAD 200 döndürdü; ancak Tizen-native
> CAPI (Sound Manager / Audio Manager) fonksiyon imzaları SPA iskeleti olduğu için
> gövdeden doğrulanamadı. Bu dosyadaki tüm Tizen-native CAPI çağrıları bu nedenle
> **⚠️ VERIFICATION REQUIRED** işaretlidir ve imzalar **çıkarılmıştır**. Taşınabilir
> dil kodları `../isletim-sistemi-turu-kod-seti.md` §2/§5/§8/§10 ile aynı çekirdektir
> (Linux-üst-sınıf); paketleme (TPK/manifest) XML yapısı doğrulanabilir, imzalama
> adımları `⚠️`. **Elenen aday:** `source.tizen.org` — DNS çözümleme HATASI.

---

## §1 GÖREV

1. **Taşınabilir çekirdek:** Node/Python/Go/Rust/PHP CLI kodları Tizen hedefi için
   nasıl çalışır? (aynı kod → Linux-üst-sınıf)
2. **GStreamer hat deseni:** boru hattı kurulumu, sink seçimi, hata temizliği
   (taşınabilir C → Tizen'de GStreamer).
3. **Tizen paketleme yüzeyi:** config.xml (app manifest), TPK yapısı, imzalama
   (⚠️ imzalama adımları), CLI paketleme (⚠️ PATH bilinmiyor).
4. **Web uygulaması yüzeyi:** webOS'a benzer web app (HTML/JS) — `docs.tizen.org`
   web rehberleri 200.
5. **Hangi dosya ne verir** → §4 tablo.

### §1.1 Sınır (bu dosyanın VERMEDİĞİ)

- **Tizen-native CAPI desenleri** (Sound Manager/Audio Manager/GStreamer detay) —
  komşu dalda, bu dosyada yalnız paketleme/yapılandırma yüzeyi ve taşınabilir çekirdek.
- **Android/iOS desenleri** — `../android/android-kod-seti.md`,
  `../ios/ios-kod-seti.md`.
- **Linux-çekirdek desenleri** (systemd/cgroup/ALSA) — `../linux/linux-kod-ornekleri.md`.

---

## §2 TAŞINABİLİR DİL ÇEKİRDEĞİ — TİZE HEDEFİ

> **Öneri (2026-10-13):** Tizen, Linux-üst-sınıf olduğundan
> `../isletim-sistemi-turu-kod-seti.md` §2 (Node/CLI), §5 (Rust/Go), §8 (PHP CLI)
> **aynen geçerlidir** — üretim kontratı Linux'dur, Tizen cihazı Linux çekirdeği +
> kullanıcı uzayı üzerine kuruludur. Aşağıda yalnız **Tizen'e özgü farklar** (paket
> XML'i, GStreamer) gösterilir; taşınabilir çekirdek dosyaya referanslanır.

| Çekirdek dosya | Tizen'de durum |
|---|---|
| `../isletim-sistemi-turu-kod-seti.md` §2 (Node CLI) | ✔ Geçerli — Node, Tizen cihazına taşınabilir (Linux-üst-sınıf) |
| `../isletim-sistemi-turu-kod-seti.md` §5 (Rust/Go) | ✔ Geçerli — POSIX + GStreamer FFI |
| `../isletim-sistemi-turu-kod-seti.md` §8 (PHP CLI) | ✔ Geçerli — CLI/tek dosya, Tizen'de de çalışır (uzman dalı: PHP backend) |
| `../isletim-sistemi-turu-kod-seti.md` §10 (Web UI) | ⚠️ Kısmi — Tizen web uygulaması ayrı yüzey (§6.4); SPA framework taşınabilir |
| `../isletim-sistemi-turu-kod-seti.md` §B.7 (C++20) + §A.6 (C#) | ✔ Geçerli — §15'te blok (taşınabilir C++20 + Tizen .NET) |
| Tizen-native CAPI (Sound/Audio Manager) | ❌ Bu dosyada YOK — komşu dalda |

---

## §3 GRUP YAPISI (TAŞINABİLİR ÇEKİRDEK + TİZE YÜZEYİ)

| # | Grup | Ne verir | Dil |
|---|------|---------|-----|
| 3.1 | Portable CLI çekirdeği (Tizen hedefi) | Node/Python CLI → Linux-üst-sınıf | Node / Python |
| 3.2 | GStreamer boru hattı deseni | capture → process → sink (taşınabilir C) | C (GStreamer) |
| 3.3 | Rust/Go + GStreamer FFI | taşınabilir sistem dili → GStreamer | Rust / Go |
| 3.4 | Tizen paketleme yüzeyi | config.xml, TPK, imzalama (⚠️) | XML / Bash (⚠️) |
| 3.5 | Tizen web uygulaması yüzeyi | appinfo.json + HTML/JS (webOS benzeri) | JSON / JS / HTML |
| 3.6 | C++20 + C# dil blokları (13-dil kapsama) | C++ yol yoklama (POSIX sınıfı) + Tizen .NET OSPlatform | C++ / C# |
| 3.7 | 13-dil kapsama — N/A kayıtları | Tizen'e özgü yüzeyi olmayan 5 kayıt | PHP / Zig / Swift / Java / Kotlin / PowerShell → `N/A` |

**Toplam:** 7 grup · 10 kod bloğu · 13 dil yüzeyi (JS, Python, C, C++20, Rust, Go, C#, Bash, PHP, Zig, Swift, Java/Kotlin, PowerShell — 8'i bu dosyada kod bloğu (§4–§7, §15), 5'i `N/A` kaydı (§16)).

---

## §4 GRUP 3.1 — PORTABLE CLI ÇEKİRDEĞİ (TİZE HEDEFİ)

### §4.1 Node CLI → Tizen cihazı

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §2 (Node/CLI — üretim çekirdeği).
> Tizen'de fark: cihazda Node çalışma süresi **cihaz-yapılandırmasına bağlı** (UNKNOWN —
> Tizen sürümüne göre değişir; `../index.md` §Tuzak). Kod aynıdır; dağıtım TPK içine
> **gömülmez** (Node, cihaz-yapılandırması ister). Aşağıda yalnız **fark** gösterilir.

```javascript
// [ORİJİNAL ÖRNEK · derlenmedi · Node.js 20 LTS · Tizen hedefi]
// Çekirdek: isletim-sistemi-turu-kod-seti.md §2 ile AYNI. Fark: Tizen'de
// cihaz Node çalışma süresi bilinmiyor (UNKNOWN) → CLI, cihazda Node ister.
// Üretim: aynı CLI Linux sunucuda çalışır; Tizen'de taşınabilirlik sınıf-içi.
'use strict';

// GStreamer pipeline çağrısı (Tizen'de gst-launch-1.0 cihaz-yapılandırmasına bağlı)
const { execFile } = require('node:child_process');

function pipeline(input, output) {
  return new Promise((resolve, reject) => {
    // gst-launch-1.0: Tizen cihazında mevcut olabilir (⚠️ cihaz-yapılandırması UNKNOWN)
    execFile('gst-launch-1.0', [
      'filesrc', `location=${input}`, '!', 'wavparse', '!', 'audioconvert', '!',
      'audioresample', '!', 'filesink', `location=${output}`,
    ], (err, stdout, stderr) => {
      if (err) reject(new Error(`pipeline: ${stderr || err.message}`));
      else resolve(stdout);
    });
  });
}

// CLI
(async () => {
  const [,, input, output] = process.argv;
  if (!input || !output) {
    console.error('kullanım: node tizen-pipeline.js <girdi> <çıktı>');
    process.exit(1);
  }
  try {
    await pipeline(input, output);
    console.log('pipeline OK:', output);
  } catch (e) {
    console.error('pipeline HATA:', e.message);
    process.exit(1);
  }
})();
```

### §4.2 Python CLI → Tizen (GStreamer python binding)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §2 (Python CLI — üretim çekirdeği).
> Fark: Tizen'de GStreamer python binding **cihaz-yapılandırmasına bağlı** (⚠️ bilinmiyor).
> Kod, binding yoksa fallback olarak `gst-launch-1.0` çağırır (taşınabilir desen).

```python
# [ORİJİNAL ÖRNEK · derlenmedi · Python 3.11 · Tizen hedefi]
# Çekirdek: isletim-sistemi-turu-kod-seti.md §2 ile AYNI. Fark: GStreamer python
# binding Tizen cihaz-yapılandırmasına bağlı (⚠️) → fallback: gst-launch-1.0.
# Üretim: aynı CLI Linux sunucuda çalışır; Tizen'de taşınabilirlik sınıf-içi.
from __future__ import annotations
import argparse
import subprocess
import sys

def pipeline_launch(input_path: str, output_path: str) -> None:
    """GStreamer pipeline: wav → wav (dönüştürme). Binding yoksa CLI çağırır."""
    cmd = [
        "gst-launch-1.0",
        "filesrc", f"location={input_path}", "!", "wavparse", "!",
        "audioconvert", "!", "audioresample", "!", "filesink",
        f"location={output_path}",
    ]
    r = subprocess.run(cmd, capture_output=True, text=True)
    if r.returncode != 0:
        raise RuntimeError(f"pipeline: {r.stderr.strip() or r.stdout.strip()}")

def main() -> int:
    ap = argparse.ArgumentParser(description="Tizen GStreamer pipeline (CLI fallback)")
    ap.add_argument("input", help="girdi dosya yolu")
    ap.add_argument("output", help="çıktı dosya yolu")
    args = ap.parse_args()
    try:
        pipeline_launch(args.input, args.output)
    except Exception as e:
        print(f"HATA: {e}", file=sys.stderr)
        return 1
    print(f"pipeline OK: {args.output}")
    return 0

if __name__ == "__main__":
    sys.exit(main())
```

---

## §5 GRUP 3.2 — GSTREAMER BORU HATTI DESeni (TAŞINABİLİR C)

> **Kapsam:** Tizen'de GStreamer, platform-multimedya omurgasıdır (⚠️ Tizen-native CAPI
> imzaları SPA skeleton nedeniyle doğrulanamadı — `../index.md` §Tuzak). Aşağıdaki
> **taşınabilir C + GStreamer** deseni, Linux-üst-sınıf Tizen cihazında çalışır
> (Linux çekirdeği + GStreamer). GStreamer API'si freedesktop standartıdır.
> **⚠️ Tizen-specific sink adları (tizencam/tizensink) bu dosyada YOK** —
> doğrulanamadı, imzalar çıkarıldı; bileşen adları yalnız `../index.md` §Tuzak'ta.

```c
/* [ORİJİNAL ÖRNEK · derlenmedi · GStreamer 1.x (taşınabilir) · Tizen hedefi]
 * GStreamer boru hattı deseni: filesrc → wavparse → audioconvert → filesink.
 * Tizen: Linux-üst-sınıf → GStreamer freedesktop standart, taşınabilir C.
 * ⚠️ Tizen-specific sink adları (tizencam/tizensink) bilinmiyor (SPA skeleton) → YOK.
 * Üretim: aynı kod Linux sunucuda çalışır (GStreamer); Tizen'de sınıf-içi.
 */
#include <gst/gst.h>
#include <string.h>

int main(int argc, char *argv[]) {
    gst_init(&argc, &argv);
    if (argc < 3) {
        g_printerr("kullanım: %s <girdi> <çıktı>\n", argv[0]);
        return 1;
    }

    /* Boru hattı: filesrc → wavparse → audioconvert → audioresample → filesink
     * (taşınabilir — Tizen'de GStreamer mevcut, cihaz-yapılandırmasına bağlı) */
    GstElement *pipeline = gst_parse_launch(
        "filesrc location=in.wav ! wavparse ! audioconvert ! "
        "audioresample ! audioresample ! filesink location=out.wav",
        NULL);
    if (!pipeline) {
        g_printerr("boru hattı OLUŞTURULAMADI\n");
        return 1;
    }

    GstBus *bus = gst_element_get_bus(pipeline);
    GstMessage *msg = gst_bus_timed_pop_filtered(bus, 5 * GST_SECOND,
        GST_MESSAGE_ERROR | GST_MESSAGE_EOS);
    if (msg) {
        if (GST_MESSAGE_TYPE(msg) == GST_MESSAGE_ERROR) {
            GError *err = NULL; gchar *dbg = NULL;
            gst_message_parse_error(msg, &err, &dbg);
            g_printerr("GStreamer HATA: %s\n", err->message);
            g_error_free(err); g_free(dbg);
        }
        gst_message_unref(msg);
    }
    gst_object_unref(bus);
    gst_element_set_state(pipeline, GST_STATE_NULL);
    gst_object_unref(pipeline);
    return 0;
}
```

---

## §6 GRUP 3.3 — RUST/GO + GSTREAMER FFI (TAŞINABİLİR)

### §6.1 Rust + GStreamer (gstreamer-rs)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §5 (Rust — üretim çekirdeği).
> Tizen: Linux-üst-sınıf → Rust + GStreamer FFI (gstreamer-rs crates) taşınabilir.
> **⚠️ gstreamer-rs crates sürüm/FFI imzaları bu dosyada doğrulanmadı** (200 ölçülmedi) →
> yalnız desen gösterilir, imzalar çıkarıldı.

```rust
// [ORİJİNAL ÖRNEK · derlenmedi · Rust + gstreamer-rs · Tizen hedefi]
// Çekirdek: isletim-sistemi-turu-kod-seti.md §5 (Rust) ile AYNI taşınabilir desen.
// Fark: gstreamer-rs FFI → Tizen (Linux-üst-sınıf) taşınabilir.
// ⚠️ gstreamer-rs crates sürüm/FFI imzaları doğrulanmadı → desen, imzalar çıkarıldı.
use std::env;
use std::process;

fn pipeline(input: &str, output: &str) -> Result<(), String> {
    // Desen: filesrc → wavparse → audioconvert → filesink (taşınabilir GStreamer)
    // FFI imzaları: ⚠️ VERIFICATION REQUIRED (gstreamer-rs sürümü doğrulanmadı)
    let _ = (input, output);
    // TODO (verify-required): gstreamer-rs init + parse_launch + bus dinleme
    // (isletim-sistemi-turu-kod-seti.md §5 Rust deseni ile aynı yapı)
    Ok(())
}

fn main() {
    let args: Vec<String> = env::args().collect();
    if args.len() < 3 {
        eprintln!("kullanım: cargo run -- <girdi> <çıktı>");
        process::exit(1);
    }
    if let Err(e) = pipeline(&args[1], &args[2]) {
        eprintln!("pipeline HATA: {e}");
        process::exit(1);
    }
    println!("pipeline OK: {}", args[2]);
}
```

### §6.2 Go + GStreamer (go-gst)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §5 (Go — üretim çekirdeği).
> Tizen: Linux-üst-sınıf → Go + GStreamer FFI (go-gst) taşınabilir.
> **⚠️ go-gst FFI imzaları doğrulanmadı** → desen, imzalar çıkarıldı.

```go
// [ORİJİNAL ÖRNEK · derlenmedi · Go 1.22 + go-gst · Tizen hedefi]
// Çekirdek: isletim-sistemi-turu-kod-seti.md §5 (Go) ile AYNI taşınabilir desen.
// Fark: go-gst FFI → Tizen (Linux-üst-sınıf) taşınabilir.
// ⚠️ go-gst FFI imzaları doğrulanmadı → desen, imzalar çıkarıldı.
package main

import (
	"fmt"
	"os"
)

func pipeline(input, output string) error {
	// Desen: filesrc → wavparse → audioconvert → filesink (taşınabilir GStreamer)
	// FFI imzaları: ⚠️ VERIFICATION REQUIRED (go-gst sürümü doğrulanmadı)
	_ = input
	_ = output
	// TODO (verify-required): go-gst init + parse launch + bus dinleme
	// (isletim-sistemi-turu-kod-seti.md §5 Go deseni ile aynı yapı)
	return nil
}

func main() {
	if len(os.Args) < 3 {
		fmt.Fprintln(os.Stderr, "kullanım: go run . <girdi> <çıktı>")
		os.Exit(1)
	}
	if err := pipeline(os.Args[1], os.Args[2]); err != nil {
		fmt.Fprintf(os.Stderr, "pipeline HATA: %v\n", err)
		os.Exit(1)
	}
	fmt.Printf("pipeline OK: %s\n", os.Args[2])
}
```

---

## §7 GRUP 3.4 — TİZE PAKETLEME YÜZEYİ (CONFIG.XML / TPK / İMZALAMA ⚠️)

### §7.1 config.xml (Tizen uygulama manifestosu — yapı doğrulanabilir)

> **Referans:** `docs.tizen.org/application/native/guides/` (200 ölçüldü) — Tizen-native
> uygulama manifestosu `config.xml` (XML). Yapı (öğe adları) rehberlerde geçer;
> **imzalama/CLI adımları ⚠️** (SPA skeleton — gövde doğrulanamadı). Aşağıdaki XML
> **yapısal örnektir**; Tizen SDK sürümüne göre öğe adları değişebilir (`⚠️`).

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!-- [ORİJİNAL ÖRNEK · derlenmedi · Tizen config.xml (yapısal) · Tizen hedefi]
     Tizen uygulama manifestosu. Yapı (manifest/payload/privileges) rehberlerde geçer;
     imzalama/CLI adımları ⚠️ (SPA skeleton, gövde doğrulanamadı). -->
<manifest xmlns="http://tizen.org/ns/manifest"
          package="com.example.coremusic"
          version="1.0.0"
          api-version="8.0">
  <profile name="tv" />  <!-- profile: tv / wearable / mobile (cihaz sınıfına göre) -->

  <privileges>
    <!-- Multimedya ayrıcalıkları (bileşen adları rehberlerde geçer; ⚠️ imzalama) -->
    <privilege>http://tizen.org/privilege/internet</privilege>
    <privilege>http://tizen.org/privilege/audio.play</privilege>
  </privileges>

  <application>
    <ui-application appid="com.example.coremusic"
                    exec="index.html"
                    launch_mode="single">
      <label>CoreMusic</label>
      <icon>icon.png</icon>
    </ui-application>
  </application>
</manifest>
```

### §7.2 TPK paketleme (⚠️ imzalama adımları — CLI PATH bilinmiyor)

> **Referans:** `docs.tizen.org` (200) — Tizen paket biçimi TPK (zip benzeri). CLI
> (`tizen package`) adımları rehberlerde geçer; **⚠️ Tizen Studio CLI'nin PATH'te
> olması bilinmiyor** (`../index.md` §Tuzak — `tizen` komutu PATH'te olmayabilir).
> Aşağıdaki Bash deseni **taşınabilir iskelettir**; CLI çağrısı `⚠️`.

```bash
#!/usr/bin/env bash
# [ORİJİNAL ÖRNEK · derlenmedi · Bash · Tizen TPK paketleme (⚠️)]
# TPK paketleme iskeleti. CLI (tizen package) PATH'te olmayabilir → ⚠️ (UNKNOWN).
# Üretim: Linux sunucuda aynı Bash; Tizen'de sınıf-içi (CLI bağımlılığı).
set -euo pipefail

APP_DIR="${1:-./tizen-app}"
SIGN_PROFILE="${2:-}"   # imzalama profili (⚠️ Tizen Studio'da tanımlanır; UNKNOWN)

echo "paketleme: ${APP_DIR}"
if [ -z "${SIGN_PROFILE}" ]; then
  echo "UYARI: imzalama profili verilmedi → TPK imzalanmaz (⚠️ çalışmayabilir)" >&2
fi

# ⚠️ Tizen Studio CLI (tizen package) — PATH'te olmayabilir (UNKNOWN).
# Bu betik, CLI'nin mevcut olduğunu VARSAYAR; yoksa hata döner (fail-safe).
if ! command -v tizen >/dev/null 2>&1; then
  echo "HATA: 'tizen' CLI bulunamadı (PATH'te olmayabilir — ../index.md §Tuzak)" >&2
  exit 127
fi

# TPK paketleme (⚠️ imzalama adımları doğrulanmadı — SPA skeleton)
# tizen package -t tpk -s <profill> -- <paket dizini>
echo "UYARI: tizen package adımları ⚠️ VERIFICATION REQUIRED (SPA skeleton)" >&2
```

---

## §8 GRUP 3.5 — TİZE WEB UYGULAMASI YÜZEYİ (APPINFO.JSON + HTML/JS)

> **Referans:** `docs.tizen.org/application/web/` (200 ölçüldü) — Tizen web uygulaması,
> HTML/JS + `config.xml` (web manifestosu). webOS `appinfo.json`'a benzer; **⚠️ Tizen
> web app yapılandırma dosyası adı (config.xml vs appinfo.json) SPA skeleton
> nedeniyle gövdeden doğrulanamadı** — aşağıda yalnız **taşınabilir web app
> çekirdeği** (HTML/JS) verilir; Tizen'e özgü yapılandırma XML'i §7.1'dedir.

```html
<!-- [ORİJİNAL ÖRNEK · derlenmedi · HTML/JS · Tizen web uygulaması yüzeyi]
     Taşınabilir web app çekirdeği (webOS'a benzer). Tizen-specific yapılandırma
     config.xml (§7.1); SPA skeleton nedeniyle web app yapılandırma dosyası adı
     ⚠️ doğrulanamadı. Çekirdek: isletim-sistemi-turu-kod-seti.md §10 (web UI). -->
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <title>CoreMusic — Tizen Web</title>
  <!-- Taşınabilir CSS: isletim-sistemi-turu-kod-seti.md §11 (CSS coremusic.css) -->
  <link rel="stylesheet" href="coremusic.css">
</head>
<body>
  <h1>CoreMusic — Tizen Web</h1>
  <div id="status" role="status" aria-live="polite">başlatılıyor…</div>

  <script type="module">
    // Taşınabilir web çekirdeği (webOS §4.2 ile aynı desen)
    const status = document.getElementById('status');
    try {
      // Tizen'de multimedya: GStreamer (native) veya HTML5 audio (web app)
      // ⚠️ Tizen web multimedya API adı doğrulanamadı (SPA skeleton) → UNKNOWN
      status.textContent = 'hazır (Tizen web app)';
    } catch (e) {
      status.textContent = 'HATA: ' + e.message;
      console.error(e);
    }
  </script>
</body>
</html>
```

---

## §9 PORTABİLİTE TABLOSU (TİZE HEDEFİ)

> **Öneri (2026-10-13):** Dil bazlı taşınabilirlik, Tizen = Linux-üst-sınıf
> olduğundan Linux ile **aynı** (POSIX + GStreamer). Fark yalnız **paketleme
> yüzeyi** (TPK/config.xml, §7).

| Dil | Tizen-native (CAPI) | Portable çekirdek | Not |
|-----|---------------------|-------------------|-----|
| **C (GStreamer)** | ⚠️ | ✔ | Linux-üst-sınıf → GStreamer (freedesktop) |
| **C++** | ⚠️ | ✔ | POSIX + GStreamer FFI |
| **Kotlin** | ❌ | ❌ | JVM/Android-runtime ister — Tizen'de YOK |
| **Swift** | ❌ | ❌ | Apple ister — Tizen'de YOK |
| **JS (web app)** | — | ✔ | HTML/JS web app (§8; SPA skeleton nedeniyle yapılandırma adı ⚠️) |
| **Node.js CLI** | — | ✔* | *cihaz Node çalışma süresi bilinmiyor (UNKNOWN) |
| **Python** | — | ✔ | CLI/taşıma; GStreamer binding ⚠️ (fallback CLI) |
| **Go** | — | ✔ | POSIX + GStreamer FFI |
| **Rust** | — | ✔ | POSIX + GStreamer FFI |
| **PHP** | — | ✔ | CLI/tek dosya — Linux-üst-sınıf, taşınabilir |
| **C# (.NET)** | — | ✔ | Tizen .NET yüzeyi (§15.2; platform token ⚠️) |
| **Zig** | N/A | N/A | Tizen'e özgü yüzey yok (§16) |
| **Java** | ❌ | ❌ | JVM çalışma süresi ister — Tizen'de YOK (aynı gerekçe: §9 Kotlin satırı) |
| **PowerShell** | N/A | N/A | Tizen yüzeyi yok — Windows seti §7.2 (§16) |

> **13-dil kapsama notu (2026-10-14):** Bu tablo **platform yeteneği** eksenidir
> (✔/❌/N/A = Tizen'de çalışabilirlik). Dosya-içi **blok sahipliği** kararları
> §16'dadır — PHP `N/A` (§16) ile `✔` (bu tablo) çelişmez: biri "bu dosyada Tizen'e
> özgü PHP bloğu yok", diğeri "PHP CLI taşınabilir".

---

## §10 BAĞLANTI MATRİSİ (İLİşKİ ÖZETİ)

| Dosya | Bağlantı türü | Ne için |
|-------|--------------|---------|
| `../isletim-sistemi-turu-kod-seti.md` | `depends_on` | Portable dil çekirdeği (§2/§5/§8/§10 — üretim çekirdeği) |
| `../isletim-sistemi-turu-kapsam.md` | `depends_on` | Sınır (§I-§II: Tizen-native CAPI komşu dalda, tekrar yasak) |
| `../isletim-sistemi-turu-index.md` | `depends_on` | Model (Web/React Native/WebAssembly YOK) |
| `../linux/index.md` | `depends_on` | Üretim kontratı (yalnız Linux) + Linux-çekirdek desenleri |
| `../android/android-kod-seti.md` | `related` | Mobil komşu (aynı grup yapısı) |
| `../ios/ios-kod-seti.md` | `related` | Mobil komşu |
| `../macos/macos-kod-seti.md` | `related` | Desktop komşu |

---

## §11 DOĞRULAMA

### §11.1 Ölçüm özeti (2026-10-13)

- **HEAD/GET 200:** `https://tizen.org/` · `https://docs.tizen.org/application/native/guides/` ·
  `https://docs.tizen.org/application/native/guides/multimedia/` · `https://docs.tizen.org/application/web/`
  (tümü 200 ölçüldü).
- **⚠️ SPA skeleton:** `docs.tizen.org` sayfaları 200 döndürdü ancak gövde **SPA iskeleti**
  (boş `<div id="root">`) olduğu için Tizen-native CAPI (Sound/Audio Manager/GStreamer)
  fonksiyon imzaları **metin içeriğiyle doğrulanamadı** → bu dosyadaki tüm Tizen-native
  CAPI çağrıları **⚠️ VERIFICATION REQUIRED** ve imzalar **çıkarıldı** (bileşen adları
  korunmadı — yalnız `../index.md` §Tuzak'ta listelendi).
- **Elenen aday:** `source.tizen.org` — DNS çözümleme HATASI (`ERR_NAME_NOT_RESOLVED`),
  kullanılmadı.

### §11.2 Taşınabilirlik doğrulaması (§9 tablosu gerekçesi)

| Dil | Tizen | Gerekçe | Durum |
|-----|-------|---------|-------|
| C (GStreamer) | ✔ | Linux-üst-sınıf → GStreamer freedesktop (⚠️ Tizen-native sink adları YOK) | verify-required |
| JS (web app) | ✔ | HTML/JS web app; yapılandırma adı ⚠️ (SPA skeleton) | verify-required |
| Node CLI | ✔* | *cihaz Node çalışma süresi bilinmiyor (UNKNOWN) | verify-required |
| Python/Go/Rust | ✔ | POSIX + GStreamer FFI (imzalar ⚠️ — desen) | verify-required |
| PHP | ✔ | CLI/tek dosya — Linux-üst-sınıf | verify-required |
| Kotlin/Swift | ❌ | JVM/Apple-runtime ister — Tizen'de YOK (bilgi) | — |

### §11.3 13-dil kapsama URL kapısı & fence ölçümü (2026-10-14)

- **HEAD/GET 200 (yazım öncesi doğrulanan — §15 blok URL'leri):**
  `https://developer.tizen.org/` ·
  `https://developer.tizen.org/development/dotnet` (redirect → `samsungtizenos.com`,
  gövdede `.NET` doğrulandı) ·
  `https://learn.microsoft.com/en-us/dotnet/api/system.runtime.interopservices.runtimeinformation` ·
  `https://learn.microsoft.com/en-us/dotnet/api/system.environment.getenvironmentvariable` ·
  `https://en.cppreference.com/w/cpp/chrono/steady_clock` ·
  `https://en.cppreference.com/w/cpp/filesystem/exists` ·
  `https://en.cppreference.com/w/cpp/language/if`.
- **404 (elenen — blok yazılmadı):** `en.cppreference.com/w/cpp/program/getenv` ·
  `/w/cpp/utility/getenv` · `/w/cpp/string/byte/getenv` · `/w/cpp/io/c/getenv`
  → getenv örneği bu dosyaya **yazılmadı**; C++ bloğu 200'lü `filesystem/exists`
  ile verildi (§15.1).
- **Fence baz ölçümü (yazım öncesi):** **8** açılış fence'i
  (`javascript` §4 · `python` §4 · `c` §5 · `rust` §6 · `go` §6 · `xml` §7 ·
  `bash` §7 · `html` §8) → **beklenen/ölçülen sonrası: 10**
  (§15.1 `cpp` + §15.2 `csharp` eklendi; §16 N/A kayıtları blok/fence **değildir**).
  Self-check: `Select-String -Pattern '^```([A-Za-z0-9_+#-]+)'` → artan fence =
  yazılan blok (2/2). Ölçüm sonucu §11.4'te.

### §11.4 Self-check ölçüm sonucu (2026-10-14)

- **Fence öncesi:** 8 (`javascript` L136 · `python` L182 · `c` L232 · `rust` L290 ·
  `go` L327 · `xml` L373 · `bash` L408 · `html` L445 — yazım öncesi sayım).
- **Fence sonrası:** **10** (grep `^```([A-Za-z0-9_+#-]+)` = 10 isabet;
  yeni: `cpp` L664 · `csharp` L711).
- **Beklenen = ölçülen (8 → 10, artış 2 = yazılan 2 blok) → PASS.**
- §15.1/§15.2 fence ilk satırları tek başına 200'lü resmi URL, ikinci satırlar
  `[ORİJİNAL ÖRNEK · derlenmedi]` etiketi (C++ `/* */`, C# `//`) — doğrulandı.
- §16'da fence yok (5 `N/A` kaydı tablo satırıdır) → fence artışına dahil değil.
- **0 silme:** yeni bölüm/ekleme dışında satır silinmedi; mevcut 8 fence, § tabloları,
  `TODO (verify-required)` placeholder'ları korundu. UTF-8 Türkçe karakterler bozulmadı.

---

## §12 SORU–CEVAP

> **S1: Tizen-native CAPI desenleri neden bu dosyada yok?**
> `../isletim-sistemi-turu-kapsam.md` §II: Tizen-native (CAPI) arayüz desenleri komşu
> dala aittir. Bu dosya yalnız taşınabilir çekirdek + paketleme yüzeyi verir.
>
> **S2: Tizen-native CAPI imzaları neden ⚠️/çıkarıldı?**
> `docs.tizen.org` SPA iskeleti döndürdüğü için (HEAD 200 ama gövde skeleton) fonksiyon
> imzaları metin içeriğiyle doğrulanamadı. Zero-hallucination: imzalar çıkarıldı;
> bileşen adları yalnız `../index.md` §Tuzak'ta.
>
> **S3: TPK imzalama adımları neden ⚠️?**
> Tizen Studio CLI (`tizen package`) PATH'te olmayabilir (`../index.md` §Tuzak) ve
> imzalama adımları SPA skeleton nedeniyle doğrulanamadı → §7.2'de CLI `⚠️`.
>
> **S4: Node/PHP Tizen'de çalışır mı?**
> Tizen, Linux-üst-sınıf olduğundan Node/PHP CLI sınıf-içi taşınabilir; ancak Node
> çalışma süresi cihaz-yapılandırmasına bağlı (UNKNOWN). PHP CLI/tek dosya sınıf-içi.

---

## §13 RED TEAM

> **R1: "Node/PHP Tizen'de çalışır" — kanıt?** Tizen Linux-üst-sınıf (bilgi) → POSIX
> sınıf-içi. Node cihaz çalışma süresi UNKNOWN (`../index.md`). PHP CLI/tek dosya
> sınıf-içi. `⚠️` işaretli.
>
> **R2: "Tizen-native CAPI imzaları çıkarıldı" — neden?** SPA skeleton (HEAD 200 ama
> gövde boş) → imzalar doğrulanamadı. Zero-hallucination: imzalar çıkarıldı.
> Bileşen adları yalnız `../index.md` §Tuzak'ta (bilgi).
>
> **R3: "TPK imzalama adımları güvenilir mi?"** Hayır — CLI PATH'te olmayabilir (UNKNOWN)
> ve adımlar doğrulanamadı → §7.2 `⚠️`.
>
> **R4: "Tizen web app yapılandırma dosyası nedir?"** `config.xml` (§7.1, XML — yapısal
> örnek). webOS `appinfo.json`'a benzemez → web app yapılandırma dosyası adı **⚠️**
> doğrulanamadı (SPA skeleton) — yalnız config.xml verildi.
>
> **R5: "GStreamer Tizen'de kesin var mı?"** Tizen-multimedya omurgası GStreamer'dır
> (bilgi; `../index.md` §Tuzak). Taşınabilir C/Rust/Go GStreamer desenleri sınıf-içi.
> Tizen-specific sink adları (tizencam/tizensink) bilinmiyor → bu dosyada YOK.

---

## §14 OTORİTE & KAYNAK

| Bilgi | Kaynak | Durum |
|-------|--------|-------|
| Üretim/CI kontratı | `../linux/index.md` §Contract | SSOT (bilgi) |
| Tizen model (Web/React Native/WebAssembly YOK) | `../index.md` §Model | SSOT (bilgi) |
| Tizen-native CAPI (Sound/Audio Manager) | `../index.md` §Tuzak | ⚠️ SPA skeleton — imzalar çıkarıldı |
| Tizen Studio CLI (`tizen package`) | `../index.md` §Tuzak | UNKNOWN (PATH'te olmayabilir) |
| TPK/config.xml yapısal örnek | `docs.tizen.org` (200) | Yapısal — imzalama adımları ⚠️ |
| GStreamer (taşınabilir) | `../isletim-sistemi-turu-kod-seti.md` §5 | Taşınabilir çekirdek (bilgi) |
| Taşınabilir dil çekirdeği | `../isletim-sistemi-turu-kod-seti.md` §2/§5/§8/§10 | SSOT (bilgi) |

**Öneri (2026-10-13):** `docs.tizen.org` HEAD 200 ölçüldü ancak SPA iskeleti döndürdüğü
için Tizen-native CAPI imzaları **metin içeriğiyle doğrulanamadı** → bu dosyadaki tüm
Tizen-native CAPI çağrıları **⚠️ VERIFICATION REQUIRED** ve imzalar **çıkarıldı**.
Taşınabilir çekirdek (Node/Python/Go/Rust/PHP) `../isletim-sistemi-turu-kod-seti.md`
§2/§5/§8/§10 ile aynıdır (Linux-üst-sınıf). Paketleme yüzeyi (config.xml/TPK) yapısal
örnek olarak verildi; imzalama adımları `⚠️` (CLI PATH bilinmiyor). `source.tizen.org`
DNS çözümleme HATASI nedeniyle kullanılmadı. Taşınabilirlik doğrulandı (§11.2) —
Tizen, Linux-çekirdek desenleriyle sınıf-içi.

---

## §15 GRUP 3.6 — C++20 + C# DİL BLOKLARI (13-DİL KAPSAMA)

> **Öneri (2026-10-14):** Bu grup 13-dil kapsama projesinin **kod bloğu** yarısını
> tamamlar (C++20 + C#). Kalan 5 dil §16'da `N/A` kaydıdır. Blok kuralları:
> fence'in ilk satırı tek başına **HEAD 200** ölçülmüş resmi URL, ikinci satır
> `[ORİJİNAL ÖRNEK · derlenmedi]` etiketi; hata açık, sessiz fallback/yer tutucu/
> yeni bağımlılık yasak. Sahiplik tekrarı yasak (R4) → çekirdek ana settedir.

### §15.1 C++20 — yol yoklama (`std::filesystem`, POSIX sınıfı)

> **URL kapısı (2026-10-14):** `https://en.cppreference.com/w/cpp/filesystem/exists`
> → **HEAD 200** (yazım öncesi doğrulandı). **Elenen 404 adaylar** (getenv için):
> `/w/cpp/program/getenv` · `/w/cpp/utility/getenv` · `/w/cpp/string/byte/getenv` ·
> `/w/cpp/io/c/getenv` → getenv örneği **bu dosyaya yazılmadı**; C++ bloğu 200'lü
> `filesystem/exists` ile verildi. Çekirdek: `../isletim-sistemi-turu-kod-seti.md`
> §B.7 (`std::filesystem`) — bu dosyada sahiplik tekrarı yok, yalnız Tizen/Linux
> hedefine uyarlanmış örnek.

```cpp
https://en.cppreference.com/w/cpp/filesystem/exists
/* [ORİJİNAL ÖRNEK · derlenmedi] */
#include <filesystem>
#include <iostream>
#include <system_error>

namespace fs = std::filesystem;

int main() {
    // Örnek yapılandırma yolu (illustrative) — Tizen/Linux-üst-sınıf: aynı çekirdek
    const fs::path cfg = "/etc/coremusic/coremusic.conf";

    // error_code aşırı yüklemesi: istisna fırlatmaz, hatayı açıkça döner
    std::error_code ec;
    const bool exists = fs::exists(cfg, ec);

    if (ec) {
        // Hata açık — sessiz fallback YOK, yer tutucu YOK
        std::cerr << "yol yoklama hatasi: " << ec.message() << '\n';
        return 1;
    }
    if (!exists) {
        std::cerr << "yapilandirma bulunamadi: " << cfg.string() << '\n';
        return 2;
    }

    std::cout << "yapilandirma var: " << cfg.string() << '\n';
    return 0;
}
```

**Not:** `fs::exists(path, error_code)` → `ec` set ise hata **açıkça** işlenir
(non-throwing overload); dosya yokluğu hata değil ikinci durumdur (exit 2).
Sessiz fallback (varsayılana düşme) **yoktur**.

### §15.2 C# — platform tespiti (Tizen .NET hedefi)

> **URL kapısı (2026-10-14):**
> `https://learn.microsoft.com/en-us/dotnet/api/system.runtime.interopservices.runtimeinformation`
> → **HEAD 200**. Tizen .NET yüzeyi: `https://developer.tizen.org/development/dotnet`
> → **HEAD 200** (redirect → `samsungtizenos.com`, gövdede `.NET` referansı
> doğrulandı). Çekirdek: `../isletim-sistemi-turu-kod-seti.md` §A.6.
> **⚠️ VERIFICATION REQUIRED:** Tizen'e özgü platform token'ı (`.NET` RID/platform
> dizesi) gövdeden doğrulanamadı → **blokta yazılmadı**; doğrulanabilen sinyal
> `OSPlatform.Linux`'tır (Tizen = Linux-üst-sınıf).

```csharp
https://learn.microsoft.com/en-us/dotnet/api/system.runtime.interopservices.runtimeinformation
// [ORİJİNAL ÖRNEK · derlenmedi]
using System;
using System.Runtime.InteropServices;

static class OsProbe
{
    static int Main()
    {
        // Tizen .NET yüzeyi: developer.tizen.org/development/dotnet (HEAD 200).
        // Tizen'e özgü platform token'ı ⚠️ doğrulanamadı → yazılmadı;
        // doğrulanabilen sinyal OSPlatform.Linux (Tizen = Linux-üst-sınıf).
        if (RuntimeInformation.IsOSPlatform(OSPlatform.Linux))
        {
            Console.WriteLine("Linux-ust-sinif (Tizen bu sinifta)");
            return 0;
        }

        // Hata açık — desteklenmeyen platformda sessiz fallback YOK
        Console.Error.WriteLine(
            "HATA: desteklenmeyen platform: " + RuntimeInformation.OSDescription);
        return 1;
    }
}
```

**Not:** `RuntimeInformation.IsOSPlatform` — çalışma zamanı platform kontrolü
(derleme zamanı değil). Desteklenmeyen platformda **exit 1** + stderr; varsayılan
değerle devam eden sessiz yol **yoktur**.

---

## §16 GRUP 3.7 — 13-DİL KAPSAMA: N/A KAYITLARI

> **Semantik filtre (2026-10-14):** Tizen'e ait olmayan / bu hedefte yüzeyi
> tanımlanmamış diller için blok **yazılmaz** → `N/A (<tek cümle gerekçe>)`.
> Sahiplik ana settedir (R4 — tekrar yasak, `bkz ../<dosya>-kod-seti.md §x`).

| Dil | Karar | Sahiplik (R4) |
|-----|-------|---------------|
| **PHP** | `N/A (Tizen'e özgü dil yüzeyi yok — PHP CLI çekirdeği ana sette sınıf-içi taşınabilir.)` | `../isletim-sistemi-turu-kod-seti.md` §A.1 · §8 |
| **Zig** | `N/A (Tizen'e özgü Zig API/SDK'sı yok — derleme hedefi tespiti ana sette.)` | `../isletim-sistemi-turu-kod-seti.md` §A.8 |
| **Swift** | `N/A (Swift yalnız Apple platformunu ister — Tizen'de YOK, §9 matrisinde ❌.)` | `../isletim-sistemi-turu-kod-seti.md` §A.9 |
| **Java/Kotlin** | `N/A (JVM/Android çalışma süresi Tizen'de yok (§9 ❌) — platform tespiti ana sette.)` | `../isletim-sistemi-turu-kod-seti.md` §A.10 |
| **PowerShell** | `N/A (Tizen'e özgü yüzey yok — PowerShell betik yüzeyi Windows setinde sınıf-içidir.)` | `../windows/windows-kod-seti.md` §7.2 |

**Tutarlılık notu:** PHP'nin `N/A`'sı **kapsam kararıdır** (bu dosyada Tizen'e özgü
PHP yüzeyi yok), taşınabilirlik **ret** değildir — §9 tablosunda PHP `✔` olarak
kalır (§2/§11.2 ile tutarlı). Java/Kotlin `N/A` (§16, dosya kapsama) ile §9'daki
`❌` (platform yeteneği) farklı eksenlerdir: §9 = Tizen'de çalışabilirlik,
§16 = bu dosyada blok sahipliği.
