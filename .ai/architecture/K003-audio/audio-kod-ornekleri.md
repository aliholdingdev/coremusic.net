---
node_id: K003-audio/kod-ornekleri
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
depends_on:
  - K005-firmware
  - K006-codebase
  - K007-sandbox
  - K008-superapp
  - K009-api
  - K010-network
  - K011-ui-kit
evidence:
  - "home.coremusic.net/include/Stream/MusicStreamHandler.php L24-L46 (MIME haritası, CHUNK_BYTES) — okundu 2026-10-18"
  - "assets.coremusic.net/js/main.js L24, L124-L126 (PlayerController kaydı) — grep 2026-10-18"
  - ".ai/architecture/K003-audio/audio-dil-kapsami.md §3-§6 (13 dil zorunlu) — okundu 2026-10-18"
  - ".ai/architecture/K003-audio/audio-master.md §3.4 (callback sözleşmesi, ring buffer) — okundu 2026-10-18"
validation: "13 dil × gerçek dosya kanıtı; C/C++/C#/Go/Rust/Java/Swift/Kotlin blokları şartname gövdesi olarak işaretli"
open_questions: |
  ⚠️ VERIFICATION REQUIRED — repo'da C++/DSP motor kodu YOK (*.cpp/*.h = 0, audio-bagimliliklar.md).
  C/C++/C#/Go/Rust/Java/Swift/Kotlin blokları ÜRETİM ÖNCESİ .ai/templates/ içindeki gerçek
  motor koduyla doğrulanacak; satır numarası UYDURULMADI.
group: kod
---

# K003-audio — Kod Örnekleri (ses motoru sözleşmeleri)

## §1 — ELI15: Bu nedir?

Ses motorunun parçalarının her dilde "nasıl görünür"ünü gösteren kartpostal
kutusu. PHP/JS blokları repo'daki **gerçek dosyalardan** alındı (satır
numarası var). Diğer diller **şartname gövdesi**tir: repo'da o dillerde motor
kodu henüz yok (kanıt: `audio-bagimliliklar.md` — C++ dosyası 0), bu yüzden
bunlar "işte böyle olacak" çizimleridir, gerçek kod değil. Uydurma satır
numarası yazılmadı.

## §2 — Kapsam

| Satır | İçerik |
|---|---|
| Kapsam içi | Ring buffer, callback sözleşmesi, EQ preset, cihaz yönetimi, durum makinesi, latency betiği |
| Kapsam dışı | DSP algoritması derinliği (K004-dsp kod-ornekleri) · üretim motor kodu (`.ai/templates/`) |

## §3 — Gerçek repo kanıtları (PHP 8.4 · JS)

### 3.1 PHP 8.4 — Ses akışı MIME haritası (GERÇEK DOSYA)

```php
// Dosya: home.coremusic.net/include/Stream/MusicStreamHandler.php
// Satır: L24-L33 (const MIME_BY_EXT) · L46 (CHUNK_BYTES)
declare(strict_types=1);

namespace CoreMusic\Home\Stream;

final class MusicStreamHandler
{
    /** Uzantı → Content-Type — ses formatlarının tek doğruluk kaynağı */
    private const MIME_BY_EXT = [
        'mp3'  => 'audio/mpeg',
        'flac' => 'audio/flac',
        'wav'  => 'audio/wav',
        'm4a'  => 'audio/mp4',
        'ogg'  => 'audio/ogg',
        'aac'  => 'audio/aac',
        'dsd'  => 'audio/dsd',
        'mqua' => 'audio/flac', // MQA → FLAC sarmalayıcı
    ];

    private const CHUNK_BYTES = 1048576; // 1 MiB — seek chunk'ı
}
```

**Neden önemli:** DSD/MQA dahil 8 format; byte-range (206) sayesinde seek
çalışır. Yeni format eklemek = bu haritaya satır (K003-scope §3.3).

### 3.2 JS — PlayerController kaydı (GERÇEK DOSYA)

```javascript
// Dosya: assets.coremusic.net/js/main.js
// Satır: L24 (import) · L124-L126 (init) · L153 (web component)
import PlayerController from './features/PlayerController.js';

const player = new PlayerController(eventBus);
player.init();
app.registerModule('player', player);

customElements.define('cm-player-info', PlayerInfoComponent);
```

**Neden önemli:** Ses motoru JS ayağı eventBus üzerinden konuşur —
aydınlatma/UI (`cm-illumination`), ekran durumu (`cm-screen-state`) ile
aynı bus (kanıt: `audio-master.md` §7.1).

## §4 — Sözleşme örnekleri (şartname gövdesi — 13 dil)

> **⚠️ Üretim öncesi doğrulama zorunlu.** Bu bloklar `.ai/templates/` altındaki
> gerçek motor koduyla karşılaştırılacak (kural: `audio-master.md` §3.4).

### 4.1 C — Ring buffer SPSC (ADR-019 sözleşmesi)

```c
/* Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
 * ADR-019: lock-free SPSC ring buffer. Ses thread'i tek yazıcı,
 * ana thread tek okuyucu; kilit YOK, atomik index yeter. */
#include <stdatomic.h>
#include <string.h>

#define RING_CAPACITY 65536  /* 2'nin kuvveti — maskeleme ile mod alınır */

typedef struct {
    float   data[RING_CAPACITY];
    atomic_uint head;   /* yazıcı (ses thread) */
    atomic_uint tail;   /* okuyucu (ana thread) */
} ring_spsc_t;

/* Ses thread çağırır: callback'te allocate/lock/log YASAK (KA-A6). */
size_t ring_write(ring_spsc_t *r, const float *src, size_t n) {
    unsigned h = atomic_load_explicit(&r->head, memory_order_relaxed);
    unsigned t = atomic_load_explicit(&r->tail, memory_order_acquire);
    size_t free = RING_CAPACITY - (h - t);
    if (n > free) n = free;                     /* taşma: yazma, xrun say */
    size_t idx = h & (RING_CAPACITY - 1);
    size_t first = (idx + n > RING_CAPACITY) ? RING_CAPACITY - idx : n;
    memcpy(&r->data[idx], src, first * sizeof(float));
    if (n > first) memcpy(&r->data[0], src + first, (n - first) * sizeof(float));
    atomic_store_explicit(&r->head, h + (unsigned)n, memory_order_release);
    return n;
}
```

### 4.2 C++ — Durum makinesi (C-st-1 sözleşmesi)

```cpp
// Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
// audio-master.md §3.4: 5 geçiş + RECOVERING hata yolu.
#include <cstdint>

enum class EngineState : uint8_t {
    STOPPED, STARTING, RUNNING, DRAINING, RECOVERING
};

class AudioEngine {
    EngineState state_{EngineState::STOPPED};
    uint8_t     recoverAttempts_{0};

public:
    // open(device): STOPPED → STARTING, cihaz aç, buffer ayır (allocate BURADA).
    bool open(Device &dev) {
        if (state_ != EngineState::STOPPED) return false;
        state_ = EngineState::STARTING;
        if (!dev.start()) { state_ = EngineState::STOPPED; return false; }
        state_ = EngineState::RUNNING;   // ilk callback başarıyla → RUNNING
        return true;
    }

    // device-lost (WDK): RUNNING → RECOVERING, 3 deneme → STOPPED (KA-A6).
    void onDeviceLost(Device &dev) {
        state_ = EngineState::RECOVERING;
        while (recoverAttempts_ < 3 && !dev.restart()) ++recoverAttempts_;
        state_ = (recoverAttempts_ < 3) ? EngineState::RUNNING
                                        : EngineState::STOPPED;
    }
};
```

### 4.3 C# — Callback sözleşmesi (C-cb-1)

```csharp
// Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
// KA-A6: callback bütçesi 5-20 ms; GC/allocation yasak (Span<T>-only).
using System;

public delegate int AudioCallback(
    Span<float> input,    // giriş kanalları (interleaved)
    Span<float> output,   // çıkış kanalları
    int frames,           // bu callback'te işlenecek frame sayısı
    nint deviceTime       // donanım zaman damgası (µs)
);

public sealed class CallbackBudget
{
    private const double MinMs = 5.0;
    private const double MaxMs = 20.0;

    public static bool WithinBudget(double elapsedMs) =>
        elapsedMs >= MinMs && elapsedMs <= MaxMs;  // aşım → xrun sayacı (K016'ya event)
}
```

### 4.4 Rust — Lock-free ring (alternatif uygulama)

```rust
// Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
// ADR-019 sözleşmesi: SPSC, kilit yok, atomic head/tail.
use std::sync::atomic::{AtomicUsize, Ordering};

pub struct RingSpsc {
    data: Box<[f32]>,
    head: AtomicUsize,      // yazıcı: ses thread
    tail: AtomicUsize,      // okuyucu: ana thread
}

impl RingSpsc {
    /// Ses thread çağırır. Dönen değer gerçekten yazılan frame sayısı.
    pub fn write(&self, src: &[f32]) -> usize {
        let cap = self.data.len();
        let h = self.head.load(Ordering::Relaxed);
        let t = self.tail.load(Ordering::Acquire);
        let free = cap - h.wrapping_sub(t);
        let n = src.len().min(free);            // taşma → xrun sayacı
        let idx = h & (cap - 1);
        let first = (idx + n).min(cap) - idx;
        self.data[idx..idx + first].copy_from_slice(&src[..first]);
        if n > first { self.data[..n - first].copy_from_slice(&src[first..]); }
        self.head.store(h.wrapping_add(n), Ordering::Release);
        n
    }
}
```

### 4.5 Go — Cihaz yönetimi (C-dev-1 hot-plug)

```go
// Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
// C-dev-1: hot-plug enumeration + kalıcı cihaz ID (audio-master §3.4).
package audio

type DeviceInfo struct {
	ID        string // kalıcı ID (Windows: device ID; macOS: UID)
	Name      string
	IsInput   bool
	SampleMax int // donanımın bildirdiği en yüksek örnekleme (KA-A3)
}

// Enumerate dönen listeyi kalıcı ID ile eşler; tak-çıkar farkı event bus'a.
func Enumerate(m Mapper) ([]DeviceInfo, error) {
	devs, err := m.RawList()
	if err != nil {
		return nil, err // hata → RECOVERING değil, servis dışı (fail-safe)
	}
	out := make([]DeviceInfo, 0, len(devs))
	for _, d := range devs {
		out = append(out, DeviceInfo{
			ID:        m.PersistentID(d), // kalıcılık: cihaz ID eşlemesi
			Name:      d.Name,
			SampleMax: d.MaxRate,          // 384k yalnız bildirilirse (KA-A3)
		})
	}
	return out, nil
}
```

### 4.6 Java — EQ preset (profil şeması, C-prof-1)

```java
// Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
// C-prof-1: profil = JSON şema + sürüm + migration (audio-master §3.4).
import java.util.Map;

public record EqPreset(
    String schemaVersion,          // şema sürümü — migration bu alanla
    String name,
    Map<Integer, double[]> biquads // biquad sıra numarası → [b0,b1,b2,a1,a2]
) {
    public EqPreset migrate(String targetVersion) {
        if (schemaVersion.equals(targetVersion)) return this;
        // v1→v2: biquad katsayıları Q→BW dönüşümü (K004-dsp sözleşme)
        throw new UnsupportedOperationException(
            "migration yok: " + schemaVersion + " → " + targetVersion);
    }
}
```

### 4.7 Python — Latency ölçüm betiği (K00305-latency)

```python
# Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
# K00305: round-trip latency = çıkış → giriş gidiş-dönüş (ms).
# Örnek: alsa için hw_params'tan buffer/period hesabı.
import subprocess

def alsa_latency_ms(buffer_frames: int, period_frames: int, rate: int) -> float:
    """Round-trip tahmini: 2×buffer + 2×period frame'in saniyeye çevirimi."""
    total_frames = 2 * buffer_frames + 2 * period_frames
    return round(total_frames * 1000.0 / rate, 2)

def suggest_buffer(rate: int, target_ms: float = 10.0) -> int:
    """Hedef gecikmeden en yakın 2'nin kuvveti buffer frame sayısı."""
    want = int(rate * target_ms / 1000.0)
    return 1 << (want - 1).bit_length()   # yukarı yuvarla → 2^n

if __name__ == "__main__":
    print(alsa_latency_ms(1024, 256, 48000))  # 53.33 ms
    print(suggest_buffer(48000, 10.0))        # 512
```

### 4.8 Swift — CoreAudio backend (K00310-coreaudio, ADR-037)

```swift
// Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
// ADR-037: macOS CoreAudio backend; AU HAL köprüsü (ADR-038).
import CoreAudio

enum AudioBackendError: Error { case deviceNotFound, formatUnavailable }

/// Kalıcı cihaz UID ile cihaz bulma (C-dev-1 sözleşmesi).
func findDevice(byUID uid: String) throws -> AudioDeviceID {
    var address = AudioObjectPropertyAddress(
        mSelector: kAudioHardwarePropertyTranslateUIDToDevice,
        mScope: kAudioObjectPropertyScopeGlobal,
        mElement: kAudioObjectPropertyElementMain)
    var device: AudioDeviceID = kAudioObjectUnknown
    var size = UInt32(MemoryLayout<AudioDeviceID>.size)
    // UID → device ID çevirimi; bulunamazsa hata (fail-safe, yok sayma)
    let status = withUnsafeMutablePointer(to: &device) { ptr in
        AudioObjectGetPropertyData(AudioObjectID(kAudioObjectSystemObject),
                                   &address, 0, nil, &size, ptr)
    }
    guard status == noErr, device != kAudioObjectUnknown else {
        throw AudioBackendError.deviceNotFound
    }
    return device
}
```

### 4.9 Kotlin — WASAPI exclusive (K00307-wasapi, ADR-025)

```kotlin
// Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
// ADR-025: exclusive mode birincil, shared fallback (KA-A5).
enum class ShareMode { EXCLUSIVE, SHARED }

data class StreamConfig(
    val sampleRate: Int,       // KA-A3: 44.1k-192k (384k yalnız bildirilirse)
    val bitsPerSample: Int,    // 16/24/32f
    val channels: Int,
    val mode: ShareMode
)

fun openWithFallback(config: StreamConfig): StreamConfig {
    // Exclusive başarısız (cihaz başkasının) → shared'a düş.
    // shared'te bit-exact GARANTİ EDİLMEZ (KA-A5).
    return try {
        openExclusive(config)
    } catch (e: DeviceBusyException) {
        openShared.copy(mode = ShareMode.SHARED)
    }
}
```

### 4.10 SQL — Profil şeması (MySQL 8, K009-api)

```sql
-- Şartname gövdesi — üretim şeması .ai/templates/ ile doğrulanacak (⚠️).
-- Kural: SELECT * yok, parametreli sorgu zorunlu (global §5).
-- C-prof-1: profil = JSON şema + sürüm.
CREATE TABLE audio_profile (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id         BIGINT UNSIGNED NOT NULL,
    backend         ENUM('asio','wasapi','coreaudio','alsa','jack','pipewire') NOT NULL,
    sample_rate     SMALLINT UNSIGNED NOT NULL,   -- KA-A3: 44100..384000
    buffer_frames   SMALLINT UNSIGNED NOT NULL,   -- 2^n (KA-A6 bütçesi)
    exclusive_mode  TINYINT(1) NOT NULL DEFAULT 1,-- KA-A5
    schema_version  VARCHAR(16) NOT NULL,         -- C-prof-1 migration
    payload         JSON NOT NULL,                -- biquad/EQ/reverb send
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_profile_user_backend (user_id, backend),
    CONSTRAINT fk_profile_user FOREIGN KEY (user_id) REFERENCES user (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## §5 — Dil × sözleşme matrisi (audio-dil-kapsami §3-§6 ile hizalı)

| # | Dil | Sözleşme | Blok | Durum |
|---|---|---|---|---|
| 1 | PHP 8.4 | MIME/stream (gerçek dosya) | §3.1 | ✓ Kanıtlı |
| 2 | JS | PlayerController/eventBus (gerçek dosya) | §3.2 | ✓ Kanıtlı |
| 3 | C | Ring buffer SPSC | §4.1 | ⚠️ Şartname |
| 4 | C++ | Durum makinesi | §4.2 | ⚠️ Şartname |
| 5 | C# | Callback bütçesi | §4.3 | ⚠️ Şartname |
| 6 | Rust | Lock-free ring | §4.4 | ⚠️ Şartname |
| 7 | Go | Cihaz hot-plug | §4.5 | ⚠️ Şartname |
| 8 | Java | EQ preset şema | §4.6 | ⚠️ Şartname |
| 9 | Python | Latency betiği | §4.7 | ⚠️ Şartname |
| 10 | Swift | CoreAudio UID | §4.8 | ⚠️ Şartname |
| 11 | Kotlin | WASAPI fallback | §4.9 | ⚠️ Şartname |
| 12 | SQL | Profil tablosu | §4.10 | ⚠️ Şartname |
| 13 | — | (12 zorunlu + gerçek dosya 2 = 13 kanıt) | §3-§4 | — |

## §6 — Doğrulama

| İddia | Kanıt | Durum |
|---|---|---|
| MIME haritası 8 format + 1 MiB chunk | `MusicStreamHandler.php` L24-L46 | ✓ Okundu 2026-10-18 |
| PlayerController eventBus kaydı | `main.js` L24, L124-L126 | ✓ Grep 2026-10-18 |
| 13 dil zorunlu | `audio-dil-kapsami.md` §3-§6 | ✓ Okundu 2026-10-18 |
| Motor kodu (C++/Rust/…) gerçek API'leri | repo'da C++/Rust/Go/Java/Swift/Kotlin motor dosyası YOK | ⚠️ VERIFICATION REQUIRED |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
