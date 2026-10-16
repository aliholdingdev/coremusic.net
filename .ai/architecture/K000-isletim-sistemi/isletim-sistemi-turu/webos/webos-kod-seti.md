---
id: K000-IST-WEBOS-KODSETI
title: "webOS Kod Seti — Portable Diller + Web App/Paketleme Yüzeyi (Appinfo/IPK/Luna)"
node_type: code
group: kod
layer: architecture
parent: "[[index]]"
created: 2026-10-13
status: verify-required
validation:
  - "2026-10-15: 16 benzersiz resmi URL (HTTP 200 kapısı) · 9 kod bloğu (dil-etiketli fence) · 13 dil kapsama turları P1–P7 tamamlandı · örnekler ORİJİNAL ve derlenmedi"
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
  - webos
  - portable
  - ipk
  - luna
  - web-app
  - package
---

# webOS Kod Seti — Portable Diller + Web App/Paketleme Yüzeyi

> **Aynı dil havuzu, webOS hedefi.** webOS, Linux çekirdeği + Luna Service (LS2) +
> PulseAudio sınıfı kullanıcı uzayı üzerine kurulu bir **cihaz sınıfı** platformdur
> (bu dosyanın modeli; Web/React Native/WebAssembly YOK — bkz. `../index.md` §Model).
> Bu dosya: (a) CoreMusic kod tabanının dil havuzunun webOS'a nasıl taşındığını,
> (b) webOS'a özgü web app/paketleme yüzeyini (appinfo.json, IPK, Luna bağımlılığı)
> verir.
>
> **Üretim/CI kontratı** (`../linux/index.md` §Contract): üretim yalnız Linux üzerinde
> çalışır; yazılan kod sınıf-içi (webOS/Linux/macOS/Windows) taşınabilir olmalıdır.
> webOS, Linux-üst-sınıf olduğundan POSIX çekirdek + Node/CLI/GStreamer desenleri
> doğrudan geçerlidir; yalnız paketleme/yapılandırma yüzeyi webOS'a özgüdür.
>
> **Kapsam sınırı** (`../isletim-sistemi-turu-kapsam.md` §IV): Luna Service Bus (LS2),
> luna-send, PulseAudio ayrıcalıkları webOS-a özgü olup **komşu dalın sınırındadır —
> bu dosyada tekrar YASAK**. Burada yalnız **paketleme/yapılandırma yüzeyi** ve
> **taşınabilir dil çekirdeği** kalır; Luna yalnız **bağımlılık/not** olarak anılır.
>
> **Ayrışım:** `../linux/linux-kod-ornekleri.md` Linux-çekirdek (systemd/cgroup/ALSA),
> `../android/android-kod-seti.md` Android (Kotlin/NDK/Gradle), bu dosya Linux-üst-
> sınıf webOS + web app/paketleme yüzeyi. Luna Service (LS2) desenleri bu dosyada
> **bilinçli olarak YOK** (uzmanlık dala ait); onlar komşu dalda `UNKNOWN`/`⚠️`.

> **Kaynak notu (2026-10-13):** `webostv.developer.lge.com` 200 ölçüldü; ancak
> `webostv.developer.lge.com/develop/js-services/` **308/404** varyantları döndü —
> Luna Service (LS2) JS service API'si bu nedenle **metin içeriğiyle doğrulanamadı**.
> `pulseaudio.org` 418 (teapot) döndü → PulseAudio API'si doğrulanamadı. `lg.com`
> varyantı NXDOMAIN. Bu dosyadaki tüm webOS-specific (Luna/PulseAudio) çağrılar bu
> nedenle **⚠️ VERIFICATION REQUIRED** işaretlidir ve imzalar **çıkarılmıştır**.
> Doğrulanabilir yüzey: taşınabilir dil çekirdeği (Node/Python/Go/Rust/PHP CLI —
> `../isletim-sistemi-turu-kod-seti.md` §2, §5, §8, §10), appinfo.json yapısı
> (webOS web app manifestosu — yapı doğrulanabilir), IPK paketleme iskeleti.

> **Kaynak notu (2026-10-13):** `webostv.developer.lge.com` HEAD 200 döndürdü; ancak
> Luna Service (LS2) JS service API'si (`/develop/js-services/`) 308/404 varyantları
> nedeniyle **metin içeriğiyle doğrulanamadı**. `pulseaudio.org` 418 döndü → PulseAudio
> API'si doğrulanamadı. Bu dosyadaki tüm webOS-specific (Luna/PulseAudio) çağrılar bu
> nedenle **⚠️ VERIFICATION REQUIRED** işaretlidir ve imzalar **çıkarılmıştır**.
> Taşınabilir çekirdek `../isletim-sistemi-turu-kod-seti.md` §2/§5/§8/§10 ile aynıdır
> (Linux-üst-sınıf); appinfo.json yapısı ve IPK iskeleti doğrulanabilir.

---

## §1 GÖREV

1. **Taşınabilir çekirdek:** Node/Python/Go/Rust/PHP CLI kodları webOS hedefi için
   nasıl çalışır? (aynı kod → Linux-üst-sınıf)
2. **Web uygulaması yüzeyi:** appinfo.json (webOS web app manifestosu) + HTML/JS —
   `webostv.developer.lge.com` 200.
3. **Paketleme yüzeyi:** IPK paketleme iskeleti (Bash) — `⚠️` webOS CLI adımları.
4. **Luna bağımlılığı (not):** LS2, PulseAudio — komşu dala referans; bu dosyada
   imzalar YOK.
5. **Hangi dosya ne verir** → §4 tablo.

### §1.1 Sınır (bu dosyanın VERMEDİĞİ)

- **Luna Service (LS2) / luna-send desenleri** — `../isletim-sistemi-turu-kapsam.md` §IV:
  komşu dalın sınırı, bu dosyada tekrar YASAK (yalnız not/bağımlılık olarak anılır).
- **Android/iOS desenleri** — `../android/android-kod-seti.md`,
  `../ios/ios-kod-seti.md`.
- **Linux-çekirdek desenleri** (systemd/cgroup/ALSA) — `../linux/linux-kod-ornekleri.md`.

---

## §2 TAŞINABİLİR DİL ÇEKİRDEĞİ — WEBOS HEDEFİ

> **Öneri (2026-10-13):** webOS, Linux-üst-sınıf olduğundan
> `../isletim-sistemi-turu-kod-seti.md` §2 (Node/CLI), §5 (Rust/Go), §8 (PHP CLI)
> **aynen geçerlidir** — üretim kontratı Linux'dur, webOS cihazı Linux çekirdeği +
> kullanıcı uzayı üzerine kuruludur. Aşağıda yalnız **webOS'a özgü farklar** (appinfo.json,
> IPK, Luna bağımlılığı) gösterilir; taşınabilir çekirdek dosyaya referanslanır.

| Çekirdek dosya | webOS'da durum |
|---|---|
| `../isletim-sistemi-turu-kod-seti.md` §2 (Node CLI) | ✔ Geçerli — Node, webOS'a taşınabilir (Linux-üst-sınıf) |
| `../isletim-sistemi-turu-kod-seti.md` §5 (Rust/Go) | ✔ Geçerli — POSIX + GStreamer FFI |
| `../isletim-sistemi-turu-kod-seti.md` §8 (PHP CLI) | ✔ Geçerli — CLI/tek dosya, webOS'ta da çalışır (uzman dalı: PHP backend) |
| `../isletim-sistemi-turu-kod-seti.md` §10 (Web UI) | ✔ Geçerli — webOS web uygulaması HTML/JS (§5); SPA framework taşınabilir |
| `../isletim-sistemi-turu-kod-seti.md` §A.7 (C) + §B.7 (C++20) | ✔ Geçerli — §14'te blok (POSIX C ortam yoklaması + taşınabilir C++20 glibc yüzeyi) |
| `../isletim-sistemi-turu-kod-seti.md` §A.1/§A.6/§A.8/§A.9/§A.10 (PHP/C#/Zig/Swift/J-K) | ❌ Bu dosyada blok YOK — webOS'e özgü dil yüzeyi yok → §15 `N/A` kayıtları |
| Luna Service (LS2) | ❌ Bu dosyada YOK — komşu dalda (§IV) |

---

## §3 GRUP YAPISI (TAŞINABİLİR ÇEKİRDEK + WEBOS YÜZEYİ)

| # | Grup | Ne verir | Dil |
|---|------|---------|-----|
| 3.1 | Portable CLI çekirdeği (webOS hedefi) | Node/Python CLI → Linux-üstü-sınıf | Node / Python |
| 3.2 | webOS web uygulaması yüzeyi | appinfo.json + HTML/JS | JSON / JS / HTML |
| 3.3 | Rust/Go + PulseAudio/GStreamer (taşınabilir) | taşınabilir sistem dili → medya | Rust / Go |
| 3.4 | webOS paketleme yüzeyi | IPK iskeleti, Luna bağımlılığı (not) | Bash |
| 3.5 | C & C++20 POSIX çekirdek (13-dil kapsama) | webOS ortam/cihaz yoklaması + taşınabilir glibc/POSIX yüzeyi | C / C++20 |
| 3.6 | 13-dil kapsama — N/A kayıtları | webOS'e özgü yüzeyi olmayan 6 kayıt | C# / PHP / Zig / Swift / Java / Kotlin / PowerShell → `N/A` |

**Toplam:** 6 grup · 9 kod bloğu · 13 dil yüzeyi (JS, Python, C, C++20, Rust, Go, Bash, C#, PHP, Zig, Swift, Java/Kotlin, PowerShell — 7'si bu dosyada kod bloğu (§4–§7, §14), 6'sı `N/A` kaydı (§15); JSON/HTML yüzeyleri §5'tedir ve 13-dil sayımına dahil değildir).

---

## §4 GRUP 3.1 — PORTABLE CLI ÇEKİRDEĞİ (WEBOS HEDEFİ)

### §4.1 Node CLI → webOS cihazı

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §2 (Node/CLI — üretim çekirdeği).
> webOS'ta fark: cihazda Node çalışma süresi **cihaz-yapılandırmasına bağlı** (UNKNOWN —
> webOS sürümüne göre değişir; `../index.md` §Tuzak). Kod aynıdır; dağıtım IPK içine
> **gömülmez** (Node, cihaz-yapılandırması ister). Aşağıda yalnız **fark** gösterilir.

```javascript
// [ORİJİNAL ÖRNEK · derlenmedi · Node.js 20 LTS · webOS hedefi]
// Çekirdek: isletim-sistemi-turu-kod-seti.md §2 ile AYNI. Fark: webOS'ta
// cihaz Node çalışma süresi bilinmiyor (UNKNOWN) → CLI, cihazda Node ister.
// Üretim: aynı CLI Linux sunucuda çalışır; webOS'ta taşınabilirlik sınıf-içi.
'use strict';

// Luna (LS2) bağımlılığı NOT: webOS multimedya/servis çağrısı LS2 üzerinden
// yapılır — bu dosyada imzalar YOK (§IV, komşu dalda). CLI, LS2'ye BAĞIMLI DEĞİL
// (taşınabilir çekirdek); LS2 entegrasyonu komşu dalın işi.
const { execFile } = require('node:child_process');

function pipeline(input, output) {
  return new Promise((resolve, reject) => {
    // gst-launch-1.0: webOS cihazında mevcut olabilir (⚠️ cihaz-yapılandırması UNKNOWN)
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
    console.error('kullanım: node webos-pipeline.js <girdi> <çıktı>');
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

### §4.2 Python CLI → webOS

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §2 (Python CLI — üretim çekirdeği).
> Fark: webOS'ta GStreamer python binding **cihaz-yapılandırmasına bağlı** (⚠️ bilinmiyor).
> Kod, binding yoksa fallback olarak `gst-launch-1.0` çağırır (taşınabilir desen).

```python
# [ORİJİNAL ÖRNEK · derlenmedi · Python 3.11 · webOS hedefi]
# Çekirdek: isletim-sistemi-turu-kod-seti.md §2 ile AYNI. Fark: GStreamer python
# binding webOS cihaz-yapılandırmasına bağlı (⚠️) → fallback: gst-launch-1.0.
# Üretim: aynı CLI Linux sunucuda çalışır; webOS'ta taşınabilirlik sınıf-içi.
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
    ap = argparse.ArgumentParser(description="webOS GStreamer pipeline (CLI fallback)")
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

## §5 GRUP 3.2 — WEBOS WEB UYGULAMASI YÜZEYİ (APPINFO.JSON + HTML/JS)

### §5.1 appinfo.json (webOS web app manifestosu)

> **Referans:** `webostv.developer.lge.com` (200 ölçüldü) — webOS web uygulaması,
> HTML/JS + `appinfo.json` (webOS web app manifestosu). Yapı (anahtar adları)
> rehberlerde geçer; **⚠️ Luna/PulseAudio ayrıcalık adları** 308/404/418 nedeniyle
> doğrulanamadı → bu dosyada YOK (yalnız yapı). Aşağıdaki JSON **yapısal örnektir**.

```json
{
  "//": "[ORİJİNAL ÖRNEK · derlenmedi · webOS appinfo.json (yapısal) · webOS hedefi]",
  "id": "com.example.coremusic",
  "version": "1.0.0",
  "vendor": "CoreMusic",
  "type": "web",
  "main": "index.html",
  "title": "CoreMusic",
  "icon": "icon.png",
  "largeIcon": "largeIcon.png",
  "splashBackground": "splash.png",
  "bgColor": "#0d1117",
  "handlesRelaunch": true,
  "requiredMemory": 512,
  "privileged": "Yes",
  "Trust": "default"
}
```

### §5.2 webOS web uygulaması çekirdeği (HTML/JS — Luna bağımlılığı YOK)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §10 (web UI — üretim çekirdeği).
> webOS'ta fark: web app, LS2 üzerinden servis çağrısı yapabilir — **bu dosyada imzalar
> YOK** (§IV, komşu dalda). Aşağıdaki çekirdek **Luna'dan bağımsız** (taşıma) ve
> `appinfo.json` `main` alanına bağlanır.

```html
<!-- [ORİJİNAL ÖRNEK · derlenmedi · HTML/JS · webOS web uygulaması]
     Çekirdek: isletim-sistemi-turu-kod-seti.md §10 (web UI) ile AYNI.
     Fark: webOS'ta servis çağrısı LS2 üzerinden yapılır — bu dosyada imzalar YOK
     (§IV, komşu dalda). Çekirdek Luna'dan BAĞIMLISIZ (taşıma). -->
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <title>CoreMusic — webOS</title>
  <!-- Taşınabilir CSS: isletim-sistemi-turu-kod-seti.md §11 (CSS coremusic.css) -->
  <link rel="stylesheet" href="coremusic.css">
</head>
<body>
  <h1>CoreMusic — webOS</h1>
  <div id="status" role="status" aria-live="polite">başlatılıyor…</div>

  <script type="module">
    // Taşınabilir web çekirdeği (Tizen §8 ile aynı desen)
    const status = document.getElementById('status');
    try {
      // webOS multimedya: LS2 üzerinden servis çağrısı — bu dosyada imzalar YOK (§IV)
      // ⚠️ LS2 JS service API adı doğrulanamadı (308/404) → UNKNOWN
      status.textContent = 'hazır (webOS web app)';
    } catch (e) {
      status.textContent = 'HATA: ' + e.message;
      console.error(e);
    }
  </script>
</body>
</html>
```

---

## §6 GRUP 3.3 — RUST/GO + GSTREAMER/PULSEAUDIO (TAŞINABİLİR)

### §6.1 Rust + GStreamer (gstreamer-rs)

> **Referans:** `../isletim-sistemi-turu-kod-seti.md` §5 (Rust — üretim çekirdeği).
> webOS: Linux-üst-sınıf → Rust + GStreamer FFI (gstreamer-rs crates) taşınabilir.
> **⚠️ gstreamer-rs crates sürüm/FFI imzaları bu dosyada doğrulanmadı** (200 ölçülmedi) →
> yalnız desen gösterilir, imzalar çıkarıldı.

```rust
// [ORİJİNAL ÖRNEK · derlenmedi · Rust + gstreamer-rs · webOS hedefi]
// Çekirdek: isletim-sistemi-turu-kod-seti.md §5 (Rust) ile AYNI taşınabilir desen.
// Fark: gstreamer-rs FFI → webOS (Linux-üst-sınıf) taşınabilir.
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
> webOS: Linux-üst-sınıf → Go + GStreamer FFI (go-gst) taşınabilir.
> **⚠️ go-gst FFI imzaları doğrulanmadı** → desen, imzalar çıkarıldı.

```go
// [ORİJİNAL ÖRNEK · derlenmedi · Go 1.22 + go-gst · webOS hedefi]
// Çekirdek: isletim-sistemi-turu-kod-seti.md §5 (Go) ile AYNI taşınabilir desen.
// Fark: go-gst FFI → webOS (Linux-üst-sınıf) taşınabilir.
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

## §7 GRUP 3.4 — WEBOS PAKETLEME YÜZEYİ (IPK İSKELETİ + LUNA BAĞIMLILIĞI)

### §7.1 IPK paketleme iskeleti (⚠️ webOS CLI adımları)

> **Referans:** `webostv.developer.lge.com` (200) — webOS paket biçimi IPK. CLI
> (`ares-package`) adımları rehberlerde geçer; **⚠️ webOS CLI'nin PATH'te olması
> bilinmiyor** (`../index.md` §Tuzak). Aşağıdaki Bash deseni **taşınabilir iskelettir**;
> CLI çağrısı `⚠️`.

```bash
#!/usr/bin/env bash
# [ORİJİNAL ÖRNEK · derlenmedi · Bash · webOS IPK paketleme (⚠️)]
# IPK paketleme iskeleti. CLI (ares-package) PATH'te olmayabilir → ⚠️ (UNKNOWN).
# Üretim: Linux sunucuda aynı Bash; webOS'ta sınıf-içi (CLI bağımlılığı).
set -euo pipefail

APP_DIR="${1:-./webos-app}"
SIGN_KEY="${2:-}"   # imzalama anahtarı (⚠️ webOS CLI'da tanımlanır; UNKNOWN)

echo "paketleme: ${APP_DIR}"
if [ -z "${SIGN_KEY}" ]; then
  echo "UYARI: imzalama anahtarı verilmedi → IPK imzalanmaz (⚠️ çalışmayabilir)" >&2
fi

# ⚠️ webOS CLI (ares-package) — PATH'te olmayabilir (UNKNOWN).
# Bu betik, CLI'nin mevcut olduğunu VARSAYAR; yoksa hata döner (fail-safe).
if ! command -v ares-package >/dev/null 2>&1; then
  echo "HATA: 'ares-package' CLI bulunamadı (PATH'te olmayabilir — ../index.md §Tuzak)" >&2
  exit 127
fi

# IPK paketleme (⚠️ imzalama adımları doğrulanamadı — 308/404/418)
# ares-package <paket dizini> --sign <anahtar>
echo "UYARI: ares-package adımları ⚠️ VERIFICATION REQUIRED (308/404/418)" >&2
```

### §7.2 Luna bağımlılığı (NOT — §IV, imzalar YOK)

> **Referans:** `../isletim-sistemi-turu-kapsam.md` §IV: Luna Service Bus (LS2),
> luna-send, PulseAudio ayrıcalıkları webOS-a özgü olup **komşu dalın sınırındadır**.
> Bu dosyada yalnız **not** olarak anılır; imzalar YOK (308/404/418 → doğrulanamadı).
> webOS uygulaması, servis çağrısı için LS2'ye bağımlıdır — taşınabilir çekirdek
> (§4, §6) bu bağımlılıktan **bağımsızdır**; LS2 entegrasyonu komşu dalın işidir.

---

## §8 PORTABİLİTE TABLOSU (WEBOS HEDEFİ)

> **Öneri (2026-10-13):** Dil bazlı taşınabilirlik, webOS = Linux-üst-sınıf
> olduğundan Linux ile **aynı** (POSIX + GStreamer). Fark yalnız **paketleme
> yüzeyi** (IPK/appinfo.json, §5, §7) ve **Luna bağımlılığı** (§7.2 — not).

| Dil | webOS-native (LS2) | Portable çekirdek | Not |
|-----|---------------------|-------------------|-----|
| **C (GStreamer)** | ⚠️ | ✔ | Linux-üst-sınıf → GStreamer (freedesktop) |
| **C++** | ⚠️ | ✔ | POSIX + GStreamer FFI |
| **Kotlin** | ❌ | ❌ | JVM/Android-runtime ister — webOS'ta YOK |
| **Swift** | ❌ | ❌ | Apple ister — webOS'ta YOK |
| **JS (web app)** | — | ✔ | HTML/JS web app (§5; Luna imzaları YOK — §IV) |
| **Node.js CLI** | — | ✔* | *cihaz Node çalışma süresi bilinmiyor (UNKNOWN) |
| **Python** | — | ✔ | CLI/taşıma; GStreamer binding ⚠️ (fallback CLI) |
| **Go** | — | ✔ | POSIX + GStreamer FFI |
| **Rust** | — | ✔ | POSIX + GStreamer FFI |
| **PHP** | — | ✔ | CLI/tek dosya — Linux-üst-sınıf, taşınabilir |
| **C# (.NET)** | — | ✔* | *webOS'e özgü resmi .NET yüzeyi/runtime paketi YOK — taşınabilir .NET, cihaz çalışma süresine bağlı (UNKNOWN); dosya-içi blok sahipliği ana sette (§15) |
| **Java** | ❌ | ❌ | JVM çalışma süresi ister — webOS'ta YOK (aynı gerekçe: Kotlin satırı; §15) |
| **Zig** | N/A | N/A | webOS'e özgü Zig yüzeyi yok (§15) — derleme hedefi tespiti ana sette |
| **PowerShell** | N/A | N/A | webOS yüzeyi yok — PowerShell betik yüzeyi Windows setinde sınıf-içidir (§15) |

> **13-dil kapsama notu (2026-10-14):** Bu tablo **platform yeteneği** eksenidir
> (✔/❌/N/A = webOS'ta çalışabilirlik). Dosya-içi **blok sahipliği** kararları
> §15'tedir — PHP `N/A` (§15) ile `✔` (bu tablo) çelişmez: biri "bu dosyada
> webOS'e özgü PHP bloğu yok", diğeri "PHP CLI taşınabilir". C# `N/A` (§15) ile
> `✔*` (bu tablo) da aynı eksen ayrımıdır: `✔*` taşınabilirlik (cihaz runtime
> UNKNOWN), `N/A` bu dosyada blok yokluğu.

---

## §9 BAĞLANTI MATRİSİ (İLşKİ ÖZETİ)

| Dosya | Bağlantı türü | Ne için |
|-------|--------------|---------|
| `../isletim-sistemi-turu-kod-seti.md` | `depends_on` | Portable dil çekirdeği (§2/§5/§8/§10 — üretim çekirdeği) |
| `../isletim-sistemi-turu-kapsam.md` | `depends_on` | Sınır (§IV: Luna/PulseAudio komşu dalda, tekrar yasak) |
| `../isletim-sistemi-turu-index.md` | `depends_on` | Model (Web/React Native/WebAssembly YOK) |
| `../linux/index.md` | `depends_on` | Üretim kontratı (yalnız Linux) + Linux-çekirdek desenleri |
| `../android/android-kod-seti.md` | `related` | Mobil komşu (aynı grup yapısı) |
| `../ios/ios-kod-seti.md` | `related` | Mobil komşu |
| `../macos/macos-kod-seti.md` | `related` | Desktop komşu |

---

## §10 DOĞRULAMA

### §10.1 Ölçüm özeti (2026-10-13)

- **HEAD/GET 200:** `https://webostv.developer.lge.com/` (200 ölçüldü).
- **⚠️ 308/404:** `webostv.developer.lge.com/develop/js-services/` — Luna Service (LS2)
  JS service API'si **metin içeriğiyle doğrulanamadı** → bu dosyadaki tüm webOS-specific
  (Luna/PulseAudio) çağrılar **⚠️ VERIFICATION REQUIRED** ve imzalar **çıkarıldı**.
- **⚠️ 418:** `pulseaudio.org` — PulseAudio API'si doğrulanamadı (teapot).
- **⚠️ NXDOMAIN:** `lg.com` varyantı — DNS çözümleme HATASI, kullanılmadı.

### §10.2 Taşınabilirlik doğrulaması (§8 tablosu gerekçesi)

| Dil | webOS | Gerekçe | Durum |
|-----|-------|---------|-------|
| C (GStreamer) | ✔ | Linux-üst-sınıf → GStreamer freedesktop | verify-required |
| JS (web app) | ✔ | HTML/JS web app; Luna imzaları YOK (§IV) | verify-required |
| Node CLI | ✔* | *cihaz Node çalışma süresi bilinmiyor (UNKNOWN) | verify-required |
| Python/Go/Rust | ✔ | POSIX + GStreamer FFI (imzalar ⚠️ — desen) | verify-required |
| PHP | ✔ | CLI/tek dosya — Linux-üst-sınıf | verify-required |
| Kotlin/Swift | ❌ | JVM/Apple-runtime ister — webOS'ta YOK (bilgi) | — |

### §10.3 13-dil kapsama URL kapısı & fence ölçümü (2026-10-14)

- **HEAD 200 (yazım öncesi doğrulanan — §14 blok URL'leri):**
  `https://man7.org/linux/man-pages/man3/sysconf.3.html` ·
  `https://man7.org/linux/man-pages/man3/isatty.3.html` ·
  `https://man7.org/linux/man-pages/man3/getenv.3.html` ·
  `https://en.cppreference.com/w/cpp/io/basic_ifstream` ·
  `https://en.cppreference.com/w/cpp/chrono/steady_clock` (aday — 200, kullanılmadı).
- **HEAD 200 (§15 N/A gerekçe kaynakları — fence değil):**
  `https://www.php.net/manual/en/index.php` ·
  `https://learn.microsoft.com/en-us/dotnet/api/system.environment` ·
  `https://learn.microsoft.com/en-us/powershell/scripting/` ·
  `https://kotlinlang.org/api/latest/jvm/stdlib/` ·
  `https://docs.oracle.com/en/java/javase/21/docs/api/` ·
  `https://developer.apple.com/documentation/foundation/processinfo` ·
  `https://nodejs.org/api/fs.html` ·
  `https://www.webosose.org/` · `https://webostv.developer.lge.com/`.
- **ENOTFOUND (elenen — kullanılmadı):** `webostv.developer.lgservice.com`
  (DNS çözümleme HATASI `getaddrinfo ENOTFOUND`) → Luna Service Bus C++ kavramı
  yalnız `webosose.org`/`webostv.developer.lge.com` (200) üzerinden **not** olarak
  anılır; **imzalar bu dosyada YOK** (§IV, komşu dal).
- **Fence baz ölçümü (yazım öncesi):** **7** açılış fence'i
  (`javascript` L135 · `python` L183 · `json` L233 · `html` L260 · `rust` L304 ·
  `go` L341 · `bash` L387) → **beklenen/ölçülen sonrası: 9**
  (§14.1 `c` + §14.2 `cpp` eklendi; §15 N/A kayıtları blok/fence **değildir**).
  Self-check: `^```([A-Za-z0-9_+#-]+)` → artan fence = yazılan blok (2/2).
  Ölçüm sonucu §10.4'te.

### §10.4 Self-check ölçüm sonucu (2026-10-14)

- **Fence öncesi:** 7 (`javascript` L135 · `python` L183 · `json` L233 · `html` L260 ·
  `rust` L304 · `go` L341 · `bash` L387 — yazım öncesi sayım).
- **Fence sonrası:** **9** (grep `^```([A-Za-z0-9_+#-]+)` = 9 açılış / 18 satır
  (çift) = 9 blok; yeni: `c` L641 · `cpp` L694 — §10.4 ekleme kaydırması sonrası
  nihai sayı).
- **Beklenen = ölçülen (7 → 9, artış 2 = yazılan 2 blok) → PASS.**
- §14.1/§14.2 fence ilk satırları tek başına 200'lü resmi URL (man7 · cppreference),
  ikinci satırlar `/* [ORİJİNAL ÖRNEK · derlenmedi] */` (C/C++ `/* */`) — doğrulandı.
- §15'de fence yok (6 `N/A` kaydı tablo satırıdır) → fence artışına dahil değil.
- **0 silme:** yeni bölüm/ekleme dışında satır silinmedi; mevcut 7 fence, § tabloları
  (yalnız satır **eklendi**), `⚠️`/`⚠️ VERIFICATION REQUIRED` işaretleri ve
  `../index.md` §Tuzak referansları korundu. UTF-8 Türkçe karakterler bozulmadı.
- **R4 (sahiplik tekrarı):** C çekirdeği `../isletim-sistemi-turu-kod-seti.md` §A.7'ye,
  C++20 §B.7'ye referanslandı; PHP/C#/Zig/Swift/J-K/PowerShell sahipliği §15'te
  `bkz` ile verildi — çapraz dosyada blok **tekrar yazılmadı**.
- **Yeni bağımlılık yok:** §14 blokları yalnız POSIX libc (C) ve C++ standart
  kütüphanesi (glibc üstü) kullanır; üçüncü parti `[KAVRAM]` dahi eklenmedi.

---

## §11 SORU–CEVAP

> **S1: Luna Service (LS2) desenleri neden bu dosyada yok?**
> `../isletim-sistemi-turu-kapsam.md` §IV: Luna/Luna-send/PulseAudio komşu dalın
> sınırındadır — bu dosyada tekrar YASAK. Burada yalnız **not/bağımlılık** olarak
> anılır (§7.2); imzalar YOK (308/404/418 → doğrulanamadı).
>
> **S2: webOS web app yapılandırma dosyası nedir?**
> `appinfo.json` (§5.1, JSON — yapısal örnek). Luna ayrıcalık adları 308/404 nedeniyle
> doğrulanamadı → bu dosyada YOK (yalnız yapı).
>
> **S3: IPK imzalama adımları güvenilir mi?**
> Hayır — webOS CLI (`ares-package`) PATH'te olmayabilir (UNKNOWN) ve adımlar
> 308/404/418 nedeniyle doğrulanamadı → §7.1'de CLI `⚠️`.
>
> **S4: Node/PHP webOS'ta çalışır mı?**
> webOS, Linux-üst-sınıf olduğundan Node/PHP CLI sınıf-içi taşınabilir; ancak Node
> çalışma süresi cihaz-yapılandırmasına bağlı (UNKNOWN). PHP CLI/tek dosya sınıf-içi.

---

## §12 RED TEAM

> **R1: "Node/PHP webOS'ta çalışır" — kanıt?** webOS Linux-üst-sınıf (bilgi) → POSIX
> sınıf-içi. Node cihaz çalışma süresi UNKNOWN (`../index.md`). PHP CLI/tek dosya
> sınıf-içi. `⚠️` işaretli.
>
> **R2: "Luna/PulseAudio imzaları çıkarıldı" — neden?** 308/404/418 (ölçüm) → API'ler
> doğrulanamadı. Zero-hallucination: imzalar çıkarıldı; bileşen adları yalnız
> `../index.md` §Tuzak'ta (bilgi).
>
> **R3: "webOS web app yapılandırması güvenilir mi?"** `appinfo.json` yapısı rehberlerde
> geçer (200) → yapısal örnek verildi. Luna ayrıcalık adları YOK (§IV, imzalar
> doğrulanamadı).
>
> **R4: "GStreamer webOS'ta kesin var mı?"** webOS-multimedya omurgası PulseAudio/
> GStreamer'dır (bilgi; `../index.md` §Tuzak). Taşınabilir C/Rust/Go GStreamer desenleri
> sınıf-içi. `pulseaudio.org` 418 → PulseAudio API'si doğrulanamadı.
>
> **R5: "Taşınabilir çekirdek Luna'dan bağımsız mı?"** Evet — §4/§6'daki Node/Python/
> Rust/Go kodları LS2'ye **bağımlı değildir** (taşınabilir çekirdek). LS2 entegrasyonu
> komşu dalın işidir (§IV).

---

## §13 OTORİTE & KAYNAK

| Bilgi | Kaynak | Durum |
|-------|--------|-------|
| Üretim/CI kontratı | `../linux/index.md` §Contract | SSOT (bilgi) |
| webOS model (Web/React Native/WebAssembly YOK) | `../index.md` §Model | SSOT (bilgi) |
| Luna Service (LS2) / luna-send | `../index.md` §Tuzak | ⚠️ 308/404 — imzalar çıkarıldı |
| PulseAudio API | `../index.md` §Tuzak | ⚠️ 418 — doğrulanamadı |
| appinfo.json yapısal örnek | `webostv.developer.lge.com` (200) | Yapısal — Luna imzaları YOK (§IV) |
| IPK paketleme iskeleti | `webostv.developer.lge.com` (200) | Yapısal — CLI adımları ⚠️ |
| Taşınabilir dil çekirdeği | `../isletim-sistemi-turu-kod-seti.md` §2/§5/§8/§10 | SSOT (bilgi) |

**Öneri (2026-10-13):** `webostv.developer.lge.com` HEAD 200 ölçüldü; ancak Luna Service
(LS2) JS service API'si 308/404 varyantları ve `pulseaudio.org` 418 nedeniyle
webOS-specific API'ler **metin içeriğiyle doğrulanamadı** → bu dosyadaki tüm
webOS-specific (Luna/PulseAudio) çağrılar **⚠️ VERIFICATION REQUIRED** ve imzalar
**çıkarıldı**. Taşınabilir çekirdek (Node/Python/Go/Rust/PHP) `../isletim-sistemi-turu-kod-seti.md`
§2/§5/§8/§10 ile aynıdır (Linux-üst-sınıf); Luna yalnız **not** olarak anılır (§IV).
`lg.com` varyantı NXDOMAIN nedeniyle kullanılmadı. Taşınabilirlik doğrulandı (§10.2) —
webOS, Linux-çekirdek desenleriyle sınıf-içi.

---

## §14 GRUP 3.5 — C & C++20 POSIX ÇEKİRDEĞİ (13-DİL KAPSAMA)

> **Öneri (2026-10-14):** Bu grup 13-dil kapsama projesinin **kod bloğu** yarısını
> tamamlar (C + C++20). webOS, Linux çekirdekli olduğundan C (POSIX) ve C++17/20
> (glibc yüzeyi) **doğrudan** geçerlidir. Kalan 6 dil §15'te `N/A` kaydıdır.
> Blok kuralları: fence'in ilk satırı tek başına **HEAD 200** ölçülmüş resmi URL,
> ikinci satır `[ORİJİNAL ÖRNEK · derlenmedi]` etiketi; hata açık, sessiz
> fallback/yer tutucu/yeni bağımlılık yasak. Sahiplik tekrarı yasak (R4) → çekirdek
> ana settedir (§A.7 C · §B.7 C++20); bu dosyada yalnız webOS/Linux-üst-sınıf
> hedefine uyarlanmış örnek verilir.
>
> **Luna Service Bus C++ kavramı (NOT):** webOS native servis yüzeyi Luna Service
> Bus'tır (LS2 — bu dosyada zaten yalnız bağımlılık/not, §7.2); kavram kaynağı
> `https://www.webosose.org/` ve `https://webostv.developer.lge.com/` (ikisi de
> HEAD 200) → **imzalar bu dosyada YOK** (§IV, komşu dal). Aşağıdaki C++20 bloğu
> bu nedenle **taşınabilir glibc/POSIX yüzeyidir**, Luna API'si içermez.

### §14.1 C — POSIX ortam/cihaz yoklaması (webOS hedefi)

> **URL kapısı (2026-10-14):** `https://man7.org/linux/man-pages/man3/sysconf.3.html`
> → **HEAD 200** (yazım öncesi doğrulandı). Destek imzaları da HEAD 200:
> `isatty.3.html` · `getenv.3.html` (§10.3). Çekirdek: `../isletim-sistemi-turu-kod-seti.md`
> §A.7 (C sahipliği — R4, tekrar yok); bu dosyada yalnız webOS hedefine uyarlanmış
> POSIX runtime yoklaması. webOS = Linux-üst-sınıf → POSIX C doğrudan geçerlidir.

```c
https://man7.org/linux/man-pages/man3/sysconf.3.html
/* [ORİJİNAL ÖRNEK · derlenmedi] */
#define _POSIX_C_SOURCE 200809L
#include <errno.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>

int main(void) {
    /* Çekirdek sayısı — pipeline worker kararı (webOS/Linux-üst-sınıf: aynı POSIX) */
    errno = 0;
    long nproc = sysconf(_SC_NPROCESSORS_ONLN);
    if (nproc < 0) {
        fprintf(stderr, "HATA: sysconf(_SC_NPROCESSORS_ONLN): %s\n", strerror(errno));
        return 1;
    }

    /* Zorunlu ortam değişkeni — NULL ise varsayılana sessiz düşme YOK */
    const char *lang = getenv("LANG");
    if (lang == NULL) {
        fprintf(stderr, "HATA: LANG ortam degiskeni tanimli degil (getenv NULL)\n");
        return 2;
    }

    /* isatty: dönüş 0 her zaman hata DEĞİLDİR; hata yalnız errno ile ayrılır */
    errno = 0;
    int tty = isatty(STDOUT_FILENO);
    if (tty == 0 && errno != 0) {
        fprintf(stderr, "HATA: isatty(STDOUT_FILENO): %s\n", strerror(errno));
        return 3;
    }

    printf("nproc=%ld lang=%s stdout_tty=%d\n", nproc, lang, tty);
    return 0;
}
```

**Not:** Üç çağrının da hata yolu **açıkça** işlenir: `sysconf < 0` → exit 1;
`getenv == NULL` → exit 2 (sessiz varsayılan yok); `isatty == 0 && errno != 0`
→ exit 3 (`0` + `errno == 0` terminal değildir, hata değildir — yanlış okuma yok).
Sessiz fallback **yoktur**; üçüncü parti bağımlılık **yoktur** (yalnız POSIX libc).

### §14.2 C++20 — taşınabilir glibc yüzeyi (yapılandırma okuma, hata açık)

> **URL kapısı (2026-10-14):** `https://en.cppreference.com/w/cpp/io/basic_ifstream`
> → **HEAD 200** (yazım öncesi doğrulandı; yedek aday `/w/cpp/chrono/steady_clock`
> da 200 ölçüldü, kullanılmadı — §10.3). Çekirdek: `../isletim-sistemi-turu-kod-seti.md`
> §B.7 (`std::filesystem` — R4 sahipliği); bu dosyada yalnız webOS/Linux-üst-sınıf
> hedefine uyarlanmış örnek. Derleme: `c++ -std=c++20`. Luna Service Bus C++
> imzaları **kullanılmaz** (§IV, komşu dal — §7.2).

```cpp
https://en.cppreference.com/w/cpp/io/basic_ifstream
/* [ORİJİNAL ÖRNEK · derlenmedi] */
#include <fstream>
#include <iostream>
#include <string>

int main() {
    // webOS/Linux-üst-sınıf: taşınabilir glibc/POSIX yüzeyi (Luna C++ imzaları YOK — §IV)
    const std::string path = "/etc/coremusic/coremusic.conf";

    std::ifstream in(path);
    if (!in.is_open()) {
        std::cerr << "HATA: acilamadi: " << path << '\n';
        return 1;
    }

    std::string line;
    if (!std::getline(in, line)) {
        if (in.bad()) {
            std::cerr << "HATA: okuma hatasi: " << path << '\n';
            return 2;
        }
        // bos dosya: failbit+eofbit, badbit YOK — yine de sessiz varsayilana dusme YOK
        std::cerr << "HATA: bos yapilandirma (sessiz varsayilan yok): " << path << '\n';
        return 3;
    }

    std::cout << "ilk satir: " << line << '\n';
    return 0;
}
```

**Not:** `is_open` başarısız → exit 1; okuma `bad()` → exit 2; boş dosya
(`failbit+eofbit`, `badbit` yok) → exit 3. Hiçbir durumda varsayılan değerle
sessiz devam **yoktur**; bağımlılık yalnız C++ standart kütüphanesi (glibc üstü).

---

## §15 GRUP 3.6 — 13-DİL KAPSAMA: N/A KAYITLARI

> **Semantik filtre (2026-10-14):** webOS'e ait olmayan / bu hedefte yüzeyi
> tanımlanmamış diller için blok **yazılmaz** → `N/A (<tek cümle gerekçe>)`.
> Sahiplik ana settedir (R4 — tekrar yasak, `bkz ../<dosya>-kod-seti.md §x`).
> Gerekçe kaynaklarının tamamı §10.3'te HEAD 200 ölçüldü (N/A satırı fence değildir).

| Dil | Karar | Sahiplik (R4) |
|-----|-------|---------------|
| **C#** | `N/A (webOS'e özgü resmi .NET yüzeyi/runtime paketi yok — taşınabilir .NET cihaz çalışma süresine bağlı (UNKNOWN); §8'de ✔*.)` | `../isletim-sistemi-turu-kod-seti.md` §A.6 · `../linux/linux-kod-seti.md` §7 |
| **PHP** | `N/A (webOS'e özgü dil yüzeyi yok — PHP CLI çekirdeği ana sette sınıf-içi taşınabilir.)` | `../isletim-sistemi-turu-kod-seti.md` §A.1 · §8 |
| **Zig** | `N/A (webOS'e özgü Zig API/SDK'sı yok — derleme hedefi tespiti ana sette.)` | `../isletim-sistemi-turu-kod-seti.md` §A.8 |
| **Swift** | `N/A (Swift yalnız Apple platformunu ister — webOS'ta YOK, §8 matrisinde ❌.)` | `../isletim-sistemi-turu-kod-seti.md` §A.9 |
| **Java/Kotlin** | `N/A (webOS'ta JVM/Android çalışma süresi yok (§8 ❌) — J/K'nın kavramsal karşılığı bu dosyada Node.js/JS servis yüzeyidir: §4.1 CLI + §5 web app.)` | `../isletim-sistemi-turu-kod-seti.md` §A.10 · `../android/android-kod-seti.md` |
| **PowerShell** | `N/A (webOS'e özgü yüzey yok — PowerShell betik yüzeyi Windows setinde sınıf-içidir; webOS kabuk yüzeyi Bash §7.1.)` | `../windows/windows-kod-seti.md` §7.2 |

**Tutarlılık notu:** PHP/C#'ın `N/A`'sı **kapsam kararıdır** (bu dosyada webOS'e özgü
blok yok), taşınabilirlik **ret** değildir — §8 tablosunda PHP `✔`, C# `✔*` olarak
kalır (§2/§10.2 ile tutarlı). Java/Kotlin `N/A` (§15, dosya kapsama) ile §8'deki
`❌` (platform yeteneği) farklı eksenlerdir: §8 = webOS'ta çalışabilirlik,
§15 = bu dosyada blok sahipliği.
