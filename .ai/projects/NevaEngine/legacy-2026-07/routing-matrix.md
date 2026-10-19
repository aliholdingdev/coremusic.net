---
type: technical-spec
project: Neva Engine
module: routing-matrix
version: 1.0.0
date: 2026-06-02
status: draft
language: tr-TR
---

# Neva Engine — Ses Routing Matris Sistemi

## 1. Genel Bakış

Neva Engine routing matrisi, fiziksel bir ses mikserini dijital ortamda
modelleyen, N giriş kanalını M çıkış kanalına yönlendirebilen, sınırsız
sanal bus ve efekt zinciri desteği sunan profesyonel bir ses yönlendirme
sistemidir.

### Konsept

```
┌───────────┐     ┌───────────────────────┐     ┌───────────┐
│ N Giriş   │ ──► │    Routing Matrix     │ ──► │ M Çıkış   │
│ Kanalı    │     │    (N×M Bağlantı)     │     │ Kanalı    │
└───────────┘     │                       │     └───────────┘
                  │  ┌─────────────────┐  │
                  │  │  Sanal Bus'lar  │  │
                  │  │  (Sınırsız)     │  │
                  │  │  · Mic Bus      │  │
                  │  │  · Apollo Bus   │  │
                  │  │  · Master Bus   │  │
                  │  │  · FX Send/Ret  │  │
                  │  └─────────────────┘  │
                  └───────────────────────┘
```

### Hedefler

| Hedef | Açıklama |
|-------|----------|
| N×M esneklik | Her giriş her çıkışa yönlendirilebilir |
| Sanal bus | Sınırsız sanal ses yolu |
| Bağımsız zincir | Her bus kendi EQ/Comp/VST3 zincirine sahip |
| Zero-latency | ASIO Direct Monitoring desteği |
| Scene yönetimi | Tüm routing kaydedilebilir/çağrılabilir |
| Görsel akış | Grid tablo + drag-drop bağlantı + sinyal akış diyagramı |

---

## 2. Giriş Kaynakları

### 2.1 Kaynak Tipleri

| Kaynak | Açıklama | Kanal Sayısı |
|--------|----------|-------------|
| Fiziksel Ses Kartı | ASIO/WASAPI girişleri | 1-64 (donanıma bağlı) |
| Sanal Input | Loopback, WASAPI loopback | 1-8 |
| System Audio | Windows ses çıkış yakalama | 2 |
| VST3 Output | Plugin çıkış sinyali | 1-16 |
| CoreMusic Player | Web player ses akışı | 2 |
| Network Stream | Ağdan gelen ses (AES67) | 1-64 |
| File Playback | Dosyadan oynatma | 1-8 |
| Test Tone | Test sinyali (sine, noise) | 1 |

### 2.2 Giriş Konfigürasyonu

```json
{
  "inputs": [
    {
      "id": "phys_mic_1",
      "name": "Mikrofon 1",
      "type": "physical",
      "device": "Focusrite USB ASIO",
      "channels": [0],
      "gain": 0.0,
      "phantom": false,
      "phaseInvert": false
    },
    {
      "id": "loopback_system",
      "name": "Sistem Sesi",
      "type": "loopback",
      "channels": [0, 1],
      "gain": -6.0
    },
    {
      "id": "player_stream",
      "name": "CoreMusic Player",
      "type": "ipc",
      "channels": [0, 1],
      "bufferMs": 100
    }
  ]
}
```

---

## 3. Bus Sistemi

### 3.1 Bus Tipleri

| Bus Tipi | Amaç | Varsayılan Kanal |
|----------|------|-----------------|
| Mic Bus | Mikrofon sinyali işleme | 1 (mono) |
| Apollo Bus | Playback efekti zinciri | 2 (stereo) |
| Master Bus | Son çıkış toplaması | 2 (stereo) |
| Submix Bus | Alt grup toplaması | 2 (stereo) |
| FX Send Bus | Paralel efekt gönderimi | 2 (stereo) |
| FX Return Bus | Efekt dönüşü toplaması | 2 (stereo) |
| Cue Bus | Kulaklık monitor çıkışı | 2 (stereo) |
| Record Bus | Kayıt sinyali | 2 (stereo) |

### 3.2 Bus Zinciri

Her bus bağımsız bir sinyal işleme zincirine sahiptir:

```
Bus Giriş Sinyali
    │
    ▼
┌──────────────┐
│ Pre-Gain     │  (-60 ... +12 dB)
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ EQ (31-band) │  (Parametrik equalizer)
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ Gate         │  (Noise gate)
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ Compressor   │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ VST3 Zinciri │  (Sınırsız plugin)
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ FX Send      │  (Reverb, Delay send)
├──────────────┤
│ Spatializer  │  (Binaural, room)
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ Fader + Pan  │  (Seviye + stereo pozisyon)
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ Mute + Solo  │
└──────┬───────┘
       │
       ▼
Bus Çıkış Sinyali → Routing Matrix → Çıkış Kanalı
```

### 3.3 Apollo Bus — Özel Efekt Zinciri

Apollo Bus, mikrofon sinyalinden bağımsız olarak sadece playback (müzik)
sesine efekt uygulamak için tasarlanmış özel bir bus'tır.

```
                      ┌─────────────────┐
Player Sesi ──────►  │   Apollo Bus    │
(Müzik)              │                 │
                      │  · EQ           │
                      │  · Reverb       │  (Özel efekt)
                      │  · Delay        │
                      │  · Chorus       │
                      │  · Spatializer  │
                      └────────┬────────┘
                               │
                               ▼
                      ┌─────────────────┐
                      │   Master Bus    │  ← Mikrofon + Apollo
                      │   (Toplama)     │
                      └─────────────────┘
```

**Apollo Bus Kullanım Senaryoları:**
- Düğün salonu: Müziğe reverb + oda simülasyonu ekle, mikrofon kuru kalsın
- Podcast: Arka plan müziğine sidechain compressor
- DJ performans: Müziğe özel EQ ve efekt zinciri

---

## 4. N×M Routing Matrix

### 4.1 Matris Yapısı

```
            Çıkış Kanalları
            Ch1  Ch2  Ch3  ...  ChM
          ┌────┬────┬────┬────┬────┐
Giriş 1   │ 0  │-3  │-∞  │    │ 0  │
          ├────┼────┼────┼────┼────┤
Giriş 2   │-∞  │ 0  │    │    │-6  │
          ├────┼────┼────┼────┼────┤
Giriş 3   │    │    │ 0  │    │    │
          ├────┼────┼────┼────┼────┤
  ...     │    │    │    │    │    │
          ├────┼────┼────┼────┼────┤
Giriş N   │    │    │    │    │ 0  │
          └────┴────┴────┴────┴────┘

Değerler: dB (-∞ = kapalı, 0 = birlik, +12 = maksimum)
```

### 4.2 Routing Bağlantısı

```cpp
struct RoutingConnection {
    std::string inputBusId;     // Kaynak bus ID
    int inputChannel;           // Kaynak kanal indeksi
    std::string outputBusId;    // Hedef bus ID
    int outputChannel;          // Hedef kanal indeksi
    float gain;                 // dB (-70 ... +12)
    bool muted;                 // Susturulmuş
    bool solo;                  // Solo aktif
    float pan;                  // -1.0 (sol) ... 1.0 (sağ)
};

class RoutingMatrix {
    std::vector<RoutingConnection> connections;
    std::unordered_map<std::string, Bus> buses;

public:
    void addConnection(const RoutingConnection& conn);
    void removeConnection(const std::string& inputBus, const std::string& outputBus);
    void setGain(const std::string& inputBus, const std::string& outputBus, float gainDb);
    std::vector<RoutingConnection> getConnectionsForOutput(const std::string& outputBus);
};
```

### 4.3 Matris API

```json
// Tüm bağlantıları al
{"method": "routing.getMatrix", "params": {}, "id": 1}

// Bağlantı ekle/güncelle
{"method": "routing.setConnection", "params": {
    "inputBus": "mic_1",
    "inputChannel": 0,
    "outputBus": "master",
    "outputChannel": 0,
    "gain": 0.0,
    "pan": 0.0
}, "id": 2}

// Bağlantı kaldır
{"method": "routing.removeConnection", "params": {
    "inputBus": "mic_1", "outputBus": "master"
}, "id": 3}

// Bus oluştur
{"method": "routing.createBus", "params": {
    "id": "custom_fx",
    "name": "Özel Efekt",
    "type": "fxSend",
    "channels": 2
}, "id": 4}

// Bus sil
{"method": "routing.deleteBus", "params": {"id": "custom_fx"}, "id": 5}
```

---

## 5. Send/Return (FX) Sistemi

### 5.1 Paralel Efekt Gönderimi

```
Bus Sinyali ──────► Bus Çıkışı (%100 dry)
    │
    │  Send Level (-∞ ... 0 dB)
    ▼
┌──────────────┐
│ FX Send Bus  │
│  · Reverb    │
│  · Delay     │
└──────┬───────┘
       │
       │  Return Level (-∞ ... +6 dB)
       ▼
┌──────────────┐
│ Master Bus   │  (Dry + Wet toplama)
└──────────────┘
```

### 5.2 Send Konfigürasyonu

```json
{
  "fxSends": [
    {
      "id": "reverb_send",
      "type": "reverb",
      "plugin": "ValhallaRoom",
      "prePostFader": "post",
      "channels": 2
    },
    {
      "id": "delay_send",
      "type": "delay",
      "plugin": "EchoBoy",
      "prePostFader": "post",
      "channels": 2
    }
  ]
}
```

### 5.3 Bus Başına Send Seviyeleri

```json
{
  "busSends": {
    "mic_1": [
      {"sendId": "reverb_send", "level": -12.0},
      {"sendId": "delay_send", "level": -30.0}
    ],
    "mic_2": [
      {"sendId": "reverb_send", "level": -10.0},
      {"sendId": "delay_send", "level": -25.0}
    ]
  }
}
```

---

## 6. Scene / Snapshot Yönetimi

### 6.1 Scene Tanımı

Scene, tüm routing konfigürasyonunun (bus yapısı, bağlantılar, efekt zincirleri,
seviyeler) bir anlık görüntüsüdür. Scene'ler arası geçiş smooth crossfade ile
yapılır.

### 6.2 Scene Dosya Formatı (JSON)

```json
{
  "scene": {
    "name": "Düğün Salonu Canlı",
    "description": "3 mikrofon + playback + reverb",
    "version": "1.0",
    "created": "2026-06-02T10:30:00Z",
    "buses": [
      {
        "id": "mic_1", "name": "Mikrofon 1", "type": "mic",
        "channels": 1, "gain": 3.0, "muted": false,
        "eqPreset": "Vokal Boost",
        "fxSends": [{"sendId": "reverb_send", "level": -8.0}]
      },
      {
        "id": "mic_2", "name": "Mikrofon 2", "type": "mic",
        "channels": 1, "gain": 2.5, "muted": false,
        "eqPreset": "Vokal Boost",
        "fxSends": [{"sendId": "reverb_send", "level": -10.0}]
      },
      {
        "id": "apollo", "name": "Apollo Bus", "type": "apollo",
        "channels": 2, "gain": 0.0, "muted": false,
        "eqPreset": "Düğün Salonu",
        "spatialPreset": "Düğün Salonu (20x15x6m)"
      },
      {
        "id": "master", "name": "Master", "type": "master",
        "channels": 2, "gain": 0.0, "muted": false,
        "limiterEnabled": true, "limiterCeiling": -1.0
      }
    ],
    "connections": [
      {"from": "mic_1", "fromCh": 0, "to": "master", "toCh": 0, "gain": 0, "pan": -0.3},
      {"from": "mic_2", "fromCh": 0, "to": "master", "toCh": 1, "gain": 0, "pan": 0.3},
      {"from": "apollo", "fromCh": 0, "to": "master", "toCh": 0, "gain": 0, "pan": -1.0},
      {"from": "apollo", "fromCh": 1, "to": "master", "toCh": 1, "gain": 0, "pan": 1.0}
    ],
    "fxSends": [
      {
        "id": "reverb_send", "type": "reverb",
        "pluginPreset": "Düğün Salonu Reverb",
        "returnLevel": -2.0
      }
    ]
  }
}
```

### 6.3 Scene Yönetim API'si

```json
// Scene listesi
{"method": "routing.listScenes", "params": {}, "id": 1}

// Scene kaydet
{"method": "routing.saveScene", "params": {"name": "YeniScene"}, "id": 2}

// Scene yükle (smooth transition)
{"method": "routing.loadScene", "params": {
    "name": "Düğün Salonu Canlı",
    "transitionMs": 500
}, "id": 3}

// Scene sil
{"method": "routing.deleteScene", "params": {"name": "EskiScene"}, "id": 4}

// Scene export/import
{"method": "routing.exportScene", "params": {"name": "Scene", "path": "export.json"}, "id": 5}
{"method": "routing.importScene", "params": {"path": "import.json"}, "id": 6}
```

### 6.4 Scene Geçişi (Smooth Transition)

```
Scene A ────────┬─────── Scene B
                │
                ▼
        crossfade (ayarlanabilir 100-2000 ms)
                │
                ▼
        Parametre interpolasyonu:
        · Gain: dB → linear → crossfade
        · EQ band: smooth coefficient morphing
        · FX send: linear crossfade
        · Plugin: bypass → yeni plugin → unbypass
```

---

## 7. Zero-Latency Monitoring

### 7.1 ASIO Direct Monitoring (ADM)

ASIO sürücüsü tarafından sağlanan donanım seviyesinde giriş izleme.
Giriş sesi DSP zincirinden geçmeden doğrudan çıkışa yönlendirilir.
Gecikme: sıfır (donanım seviyesi).

```cpp
class DirectMonitor {
    bool asioDirectMonitoringEnabled = true;

    void enableDirectMonitor(int inputChannel, int outputChannel) {
        if (asioDriver) {
            asioDriver->enableDirectMonitor(inputChannel, outputChannel, true);
        }
    }

    void disableDirectMonitor() {
        if (asioDriver) {
            asioDriver->disableDirectMonitor();
        }
    }
};
```

### 7.2 ADM vs Yazılım Monitor

| Mod | Latency | DSP Efekt | Kullanım |
|-----|---------|-----------|----------|
| ADM | 0 ms | Yok | Kayıt sırasında kuru izleme |
| Yazılım | Buffer × 2 | Var | Efektli izleme |
| Hibrit | ADM + yazılım | Kısmi | Düşük latency + efekt |

---

## 8. Görsel Routing Arayüzü

### 8.1 Grid Tablo (N×M Matris)

```
             Çıkış: Master L  Master R  Cue L    Cue R
Giriş:
Mic 1        [  0.0 dB  ]  [  -∞   ]  [-6.0 ]  [  -∞   ]
Mic 2        [   -∞    ]  [  0.0  ]  [-6.0 ]  [  -∞   ]
Apollo L     [  0.0 dB ]  [  -∞   ]  [ 0.0 ]  [  -∞   ]
Apollo R     [   -∞    ]  [ 0.0 dB]  [  -∞  ]  [  0.0  ]
```

### 8.2 Drag-Drop Bağlantı

- Giriş bus'ından çıkış bus'ına sürükle-bırak
- Otomatik kanal eşleştirme (mono → stereo sol/sağ)
- Bağlantı koparma: sağ tık veya X butonu
- Görsel kablo çizimi: bağlı bus'lar arası renkli çizgiler

### 8.3 Sinyal Akış Diyagramı

```
                                 ┌──────────┐
                    ┌───────────►│  Cue Bus │──► Kulaklık
                    │            └──────────┘
┌──────┐   ┌────────┴───────┐
│ Mic1 │──►│                │   ┌──────────┐
└──────┘   │                │──►│ FX Send  │──► Reverb
┌──────┐   │  Routing       │   └────┬─────┘
│ Mic2 │──►│  Matrix        │        │
└──────┘   │                │   ┌────┴─────┐
┌──────┐   │                │──►│  Master  │──► Hoparlör
│Apollo│──►│                │   └──────────┘
└──────┘   └────────────────┘
```

---

## 9. VoiceMeeter Benzeri Ses Kartı Taklidi

### 9.1 Sanal Ses Kartı Modu

Neva Engine, Windows'ta sanal bir ses kartı olarak görünebilir:

```
┌────────────────────────────────────────────────┐
│              Windows Ses Sistemi                │
│                                                 │
│  Uygulama 1 (Chrome) ──┐                       │
│  Uygulama 2 (OBS)    ──┤                       │
│  Uygulama 3 (Discord) ─┤                       │
│                         ├──► Neva Engine        │
│  Mikrofon Girişi     ──┤    (Sanal Ses Kartı)  │
│  Line In             ──┤                       │
│                         │    · Routing          │
│                         │    · EQ/Comp          │
│                         │    · FX               │
│                         │                       │
│                         └──► Fiziksel Çıkış     │
│                              (Hoparlör/Kulaklık)│
└────────────────────────────────────────────────┘
```

### 9.2 Sanal Cihaz Özellikleri

| Özellik | Açıklama |
|---------|----------|
| Sanal Input | Windows'a sanal mikrofon olarak görünür |
| Sanal Output | Windows'a sanal hoparlör olarak görünür |
| Loopback | Sistem sesini yakala (WASAPI loopback) |
| Multi-app | Birden fazla uygulama aynı anda bağlanabilir |
| Sample Rate | 44.1/48/96 kHz |
| Buffer | 128-1024 sample |

---

## 10. Master Bus İşleme

### 10.1 Master Zinciri

```
Tüm Bus Toplaması
    │
    ▼
┌──────────────┐
│ Master EQ    │  (5-band parametrik + Low/High Shelf)
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ Multiband    │  (3-band: Low/Mid/High)
│ Compressor   │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ Stereo Imager│  (Width, balance, M/S)
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ True Peak    │  (ITU-R BS.1770-4)
│ Limiter      │  Ceiling: -1.0 dBTP
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ Loudness     │  (EBU R128, -23 LUFS hedef)
│ Normalizer   │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ Dither       │  (TPDF, noise shaping)
│ (Float→Int)  │
└──────┬───────┘
       │
       ▼
Fiziksel Çıkış
```

### 10.2 Master Ölçüm

| Metre | Birim | Hedef |
|-------|-------|-------|
| Peak | dBFS | < -1.0 |
| True Peak | dBTP | < -1.0 |
| RMS | dBFS | -18 ... -12 |
| LUFS (M) | LUFS | -23 (±1) EBU |
| LUFS (S) | LUFS | -18 ... -14 (stream) |
| Dynamic Range | dB | > 8 |
| LRA (Loudness Range) | LU | 5-15 |
| Phase Correlation | -1 ... 1 | > 0 (stereo) |
| Spectrum | FFT | Görsel |

---

## 11. Yapılandırma

### 11.1 Routing Yapılandırması (neva-engine.json)

```json
{
  "routing": {
    "defaultScene": "Default",
    "autoSaveScene": true,
    "sceneTransitionMs": 500,
    "directMonitor": {
      "enabled": true,
      "mode": "auto"
    },
    "virtualDevice": {
      "enabled": false,
      "inputName": "Neva Engine Input",
      "outputName": "Neva Engine Output",
      "sampleRate": 48000,
      "bufferSize": 256
    },
    "masterBus": {
      "limiterEnabled": true,
      "limiterCeiling": -1.0,
      "loudnessTarget": -23,
      "ditherEnabled": true,
      "ditherType": "tpdf"
    }
  }
}
```

---

## 12. Performans ve Sınırlamalar

### 12.1 Kapasite

| Metrik | Sınır |
|--------|-------|
| Toplam bus | Sınırsız (bellek sınırlı) |
| Bus başına kanal | 1-64 |
| Toplam FX send | 16 |
| Bus başına send | 8 |
| Scene depolama | Sınırsız (disk sınırlı) |
| Crosspoint (bağlantı) | Sınırsız |

### 12.2 CPU Maliyeti

| Operasyon | CPU (Ryzen 7) |
|-----------|---------------|
| Matris yönlendirme (32×32) | < 0.5% |
| Bus işleme (8 bus, full chain) | < 10% |
| Scene geçişi (500 ms) | +%2 (geçiş süresince) |
| Master işleme | < 3% |
| Toplam (tipik) | < 15% |

---

## 13. Gelecek Genişletmeler

| Özellik | Açıklama |
|---------|----------|
| OSC remote control | iPad/tablet ile uzaktan routing kontrolü |
| NDI streaming | Ağ üzerinden ses/routing paylaşımı |
| Dante Virtual Soundcard | Audinate Dante entegrasyonu |
| OSC/MIDI scene recall | Donanım controller ile scene değiştirme |
| Collaborative routing | Birden fazla Neva Engine arası routing |
| Auto-routing | AI destekli otomatik routing önerisi |

---

*Neva Engine v1.0.0 — Routing Matris Spesifikasyonu*
*CoreMusic Ekosistemi · GPLv3 · C++20 · JUCE 8.x*
