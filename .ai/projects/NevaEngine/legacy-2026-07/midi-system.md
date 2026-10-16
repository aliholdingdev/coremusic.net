---
type: technical-spec
project: Neva Engine
module: midi-system
version: 1.0.0
date: 2026-06-02
status: draft
language: tr-TR
---

# Neva Engine — MIDI Sistemi

## 1. Genel Bakış

Neva Engine MIDI sistemi, donanım controller'lar, sanal enstrümanlar, ağ MIDI
cihazları ve otomasyon sistemleri ile tam entegrasyon sağlayan, MIDI 1.0 ve
MIDI 2.0 standartlarını destekleyen kapsamlı bir kontrol altyapısıdır.

### Tasarım Hedefleri

| Hedef | Açıklama |
|-------|----------|
| Evrensel bağlantı | USB, Bluetooth, Ağ, Sanal MIDI |
| MIDI Learn | Her parametre otomatik eşlenebilir |
| Çift yönlü | Motor kontrolü + feedback (motorlu fader) |
| MIDI 2.0 hazır | Yüksek çözünürlük, property exchange |
| Düşük jitter | < 1 ms MIDI zamanlama |

### MIDI Mesaj Türleri

| Tip | Açıklama | Kullanım |
|-----|----------|----------|
| Note On/Off | Nota başlangıç/bitiş | Transport, trigger |
| Control Change (CC) | Sürekli kontrol (0-127) | Fader, knob |
| Program Change | Program/sahne değişimi | Scene recall |
| Pitch Bend | Pitch kaydırma | Pitch wheel |
| Channel Pressure | Kanal basıncı | Aftertouch |
| SysEx | Sistem özel mesajı | Cihaz konfigürasyonu |
| MTC (MIDI Time Code) | Zaman kodu | Sync |
| MIDI Clock | Tempo clock'u | BPM sync |
| MMC (MIDI Machine Ctrl) | Transport kontrolü | Play/Stop/Record |
| NRPN/RPN | Yüksek çözünürlük CC | 14-bit parametre |

---

## 2. MIDI Giriş Kaynakları

### 2.1 Bağlantı Türleri

| Kaynak | Protokol | Gecikme | Kullanım |
|--------|----------|---------|----------|
| USB MIDI | USB 2.0 Class Compliant | < 1 ms | Donanım controller |
| Bluetooth MIDI | BLE MIDI 1.0 | 10-20 ms | Kablosuz controller |
| Virtual MIDI Port | loopMIDI / IAC | < 1 ms | DAW ↔ Neva |
| RTP-MIDI (AppleMIDI) | RFC 6295, UDP | 1-5 ms | Ağ MIDI |
| 5-pin DIN MIDI | MIDI 1.0 | < 1 ms | Legacy donanım |
| MIDI over USB (host) | USB Host | < 1 ms | USB-MIDI cihazlar |

### 2.2 Cihaz Keşfi

```cpp
class MidiDeviceManager {
public:
    struct MidiDevice {
        std::string name;
        std::string identifier;
        MidiDeviceType type;    // USB, BLE, Virtual, Network, DIN
        bool isInput;
        bool isOutput;
        std::string manufacturer;
        bool connected;
    };

    std::vector<MidiDevice> scanDevices();
    void openDevice(const std::string& identifier);
    void closeDevice(const std::string& identifier);
    void setMidiInputCallback(MidiInputCallback callback);

    using MidiInputCallback = std::function<void(const MidiMessage&)>;
};
```

### 2.3 MIDI Mesaj Yapısı

```cpp
struct MidiMessage {
    uint64_t timestamp;         // Mikrosaniye (motor zamanı)
    uint8_t status;             // Status byte (0x80-0xFF)
    uint8_t channel;            // MIDI kanalı (0-15)
    std::vector<uint8_t> data;  // 1-2 byte (CC: [number, value])

    enum Type {
        NOTE_OFF = 0x80,
        NOTE_ON = 0x90,
        CONTROL_CHANGE = 0xB0,
        PROGRAM_CHANGE = 0xC0,
        PITCH_BEND = 0xE0,
        SYSEX = 0xF0,
        MTC_QUARTER_FRAME = 0xF1,
        SONG_POSITION = 0xF2,
        SONG_SELECT = 0xF3,
        CLOCK = 0xF8,
        START = 0xFA,
        CONTINUE = 0xFB,
        STOP = 0xFC,
        ACTIVE_SENSING = 0xFE,
        RESET = 0xFF,
    };
};
```

---

## 3. MIDI Learn Sistemi

### 3.1 MIDI Learn Akışı

```
Kullanıcı: "MIDI Learn" moduna gir
    ↓
GUI: "Hangi parametreyi eşlemek istersiniz?"
    ↓
Kullanıcı: EQ Band 5 Gain'e tıkla (GUI)
    ↓
GUI: "Şimdi controller'daki istediğiniz düğmeyi çevirin..."
    ↓
Kullanıcı: MIDI controller'da knob çevirir
    ↓
Motor: CC#20, Ch1 → EQ Band 5 Gain olarak eşle
    ↓
GUI: "Eşleme tamam: CC#20 → EQ Band 5 Gain"
```

### 3.2 Mapping Yapısı

```cpp
struct MidiMapping {
    std::string targetId;           // "eq.band.5.gain"
    MidiMessageType sourceType;     // CC, Note, PitchBend, NRPN
    int sourceChannel;              // 0-15
    int sourceNumber;               // CC numarası, Note numarası
    float inMin, inMax;             // MIDI değer aralığı (CC: 0-127)
    float outMin, outMax;           // Parametre değer aralığı
    MappingCurve curve;             // Linear, Log, Exp, Custom
    bool feedbackEnabled;           // Motorlu fader geri bildirimi
    float smoothing;                // Düzgünleştirme (ms)
};

enum class MappingCurve {
    LINEAR,
    LOGARITHMIC,
    EXPONENTIAL,
    S_CURVE,
    CUSTOM
};
```

### 3.3 Mapping Eğrileri

```
Değer Dönüşümü:

Linear:    y = (x - inMin)/(inMax - inMin) × (outMax - outMin) + outMin

Log:       y = outMin + log(x/inMax) / log(base) × (outMax - outMin)

Exp:       y = outMin + (exp(x/inMax × k) - 1) / (exp(k) - 1) × (outMax - outMin)

S-Curve:   y = smoothstep(x/inMax) × (outMax - outMin) + outMin
```

### 3.4 Eşlenebilir Parametreler

| Kategori | Parametre | Aralık |
|----------|----------|--------|
| EQ Band Gain | eq.band.{1-33}.gain | -18.0 ... +18.0 dB |
| EQ Band Freq | eq.band.{1-33}.frequency | 20 ... 20000 Hz |
| EQ Band Q | eq.band.{1-33}.q | 0.1 ... 10.0 |
| Bus Volume | bus.{id}.volume | -∞ ... +12 dB |
| Bus Pan | bus.{id}.pan | -1.0 ... 1.0 |
| Bus Mute | bus.{id}.mute | 0 / 1 |
| FX Send | bus.{id}.send.{fxId} | -∞ ... 0 dB |
| Plugin Param | plugin.{id}.param.{n} | Plugin tanımlı |
| Transport | transport.{play,stop,rec} | 0 / 1 |
| Scene Recall | scene.load.{name} | 0 / 1 (trigger) |
| Master Volume | master.volume | -∞ ... +6 dB |

---

## 4. Mapping Profilleri

### 4.1 Profil Sistemi

```json
{
  "profile": {
    "name": "Akai MPK Mini",
    "deviceMatch": "AKAI MPK mini",
    "version": "1.0",
    "mappings": [
      {
        "target": "eq.band.10.gain",
        "source": "cc",
        "channel": 0,
        "ccNumber": 20,
        "knobLabel": "K1",
        "inRange": [0, 127],
        "outRange": [-18.0, 18.0],
        "curve": "linear"
      },
      {
        "target": "bus.master.volume",
        "source": "cc",
        "channel": 0,
        "ccNumber": 7,
        "inRange": [0, 127],
        "outRange": [-70.0, 6.0],
        "curve": "logarithmic"
      },
      {
        "target": "transport.play",
        "source": "note",
        "channel": 0,
        "noteNumber": 36,
        "mode": "toggle"
      },
      {
        "target": "transport.stop",
        "source": "note",
        "channel": 0,
        "noteNumber": 37,
        "mode": "trigger"
      }
    ]
  }
}
```

### 4.2 Desteklenen Controller Profilleri

| Controller | Tip | Mapping Sayısı | Özellikler |
|-----------|-----|---------------|-----------|
| Akai MPK Mini Mk3 | Klavye + Pad + Knob | 40 | Transport, pad, knob |
| Novation Launchpad X | Pad grid | 64 | Grid, session view |
| Behringer X-Touch | MCU kontrol yüzeyi | 120 | Motorlu fader, scribble strip |
| Korg nanoKONTROL 2 | Kompakt kontrol | 46 | Fader, knob, transport |
| M-Audio Oxygen Pro | Klavye + kontrol | 80 | Pad, fader, knob |
| Arturia KeyLab Essential | Klavye + kontrol | 72 | Knob, fader, pad |
| Ableton Push 2 | Grid controller | 190 | Tam entegrasyon |
| Generic MIDI Mixer | Evrensel | 16 | Fader, mute, pan |

---

## 5. Transport Kontrolü

### 5.1 Transport Komutları

| Komut | MIDI | Açıklama |
|-------|------|----------|
| Play | MMC Play, Note, CC | Oynatmayı başlat |
| Stop | MMC Stop, Note, CC | Oynatmayı durdur |
| Pause | MMC Pause | Duraklat |
| Record | MMC Record | Kaydı başlat |
| Rewind | MMC Rewind | Geri sar |
| Fast Forward | MMC Fast Forward | İleri sar |
| Next Track | Note, CC | Sonraki parça |
| Previous Track | Note, CC | Önceki parça |
| Loop Toggle | Note, CC | Döngü aç/kapat |
| Jog Wheel | CC (göreli) | Jog/shuttle |

### 5.2 MMC (MIDI Machine Control)

```
MMC Komut Formatı:
F0 7F <deviceID> 06 <command> F7

Komutlar:
01 = Stop
02 = Play
03 = Deferred Play
04 = Fast Forward
05 = Rewind
06 = Record Strobe
07 = Record Exit
08 = Record Pause
09 = Pause
0A = Eject
0B = Chase
0D = MMC Reset
```

---

## 6. Scene/Snapshot Recall

### 6.1 MIDI ile Scene Değiştirme

```json
{
  "sceneMapping": [
    {
      "scene": "Düğün Salonu Canlı",
      "trigger": {"type": "programChange", "channel": 0, "program": 1}
    },
    {
      "scene": "Stüdyo Kayıt",
      "trigger": {"type": "programChange", "channel": 0, "program": 2}
    },
    {
      "scene": "Podcast",
      "trigger": {"type": "programChange", "channel": 0, "program": 3}
    },
    {
      "scene": "DJ Mix",
      "trigger": {"type": "note", "channel": 0, "noteNumber": 48, "mode": "trigger"}
    }
  ]
}
```

### 6.2 Scene Geçiş Efektleri

| Geçiş Tipi | Süre | Açıklama |
|------------|------|----------|
| Instant | 0 ms | Anında değişim |
| Crossfade | 100-5000 ms | Ses seviyesi crossfade |
| Morph | 500-5000 ms | Tüm parametreler smooth morph |
| Trigger | — | MIDI notası ile tetikleme |

---

## 7. MIDI Clock Sync

### 7.1 Master/Slave Modları

```
Master Modu (Neva Engine clock kaynağı):
Neva Engine ──MIDI Clock──► Harici synth/sampler/DJ ekipmanı

Slave Modu (harici cihaz clock kaynağı):
Harici cihaz ──MIDI Clock──► Neva Engine
    │
    ▼
BPM Detection → Neva Engine internal tempo
    │
    ▼
Efekt senkronizasyonu: Delay time, LFO rate, arpeggiator
```

### 7.2 MIDI Clock Yapısı

```
MIDI Clock: 24 PPQN (Pulse Per Quarter Note)

1 ölçü (4/4) = 4 vuruş = 96 clock pulse

Örnek: 120 BPM
- 1 vuruş = 500 ms
- 1 clock pulse = 500/24 = 20.83 ms

Start  → Sayaç sıfırlanır, clock başlar
Stop   → Clock durur, pozisyon korunur
Continue→ Clock kaldığı yerden devam eder
```

### 7.3 BPM Detection (Ses Analizi)

```cpp
class BPMDetector {
public:
    struct BPMResult {
        float bpm;
        float confidence;       // 0.0 - 1.0
        float beatStrength;     // Ortalama vuruş şiddeti
        std::vector<float> beatPositions;  // Vuruş zamanları (saniye)
    };

    BPMResult detectBPM(const float* audio, int numSamples, float sampleRate);

private:
    // Adımlar:
    // 1. Filter bank (6 band: sub-bass, bass, low-mid, mid, high-mid, high)
    // 2. Her band için onset detection (spectral flux)
    // 3. Autocorrelation → tempo adayları
    // 4. Comb filter → beat tracking
    // 5. Best candidate seçimi (histogram)
};
```

---

## 8. MIDI 2.0 Hazırlığı

### 8.1 MIDI 2.0 Özellikleri

| Özellik | MIDI 1.0 | MIDI 2.0 |
|---------|----------|----------|
| Çözünürlük | 7-bit (0-127) | 32-bit |
| CC sayısı | 128 | 32,768+ |
| Nota hızı | 7-bit | 16-bit |
| Pitch bend | 14-bit | 32-bit |
| Per-note express. | Yok (kanal tabanlı) | Var (nota başına) |
| Bidirectional | Sınırlı (sysex) | Tam (property exchange) |
| Profile config | Yok | Var (auto-config) |
| Jitter azaltma | Yok | JR Timestamps |

### 8.2 MIDI 2.0 UMP (Universal MIDI Packet)

```
UMP Formatı (32-bit kelimeler):

┌────────────┬────────────────────────────┐
│ Message    │                            │
│ Type (4b)  │  Data (28b × N words)      │
├────────────┼────────────────────────────┤
│ 0x0 Utility│ JR Clock, System Real-Time │
│ 0x1 System │ MIDI 1.0 uyumlu            │
│ 0x2 MIDI 1.0│ Kanal ses mesajları (eski) │
│ 0x3 Data   │ SysEx (64-bit gruplar)     │
│ 0x4 MIDI 2.0│ Yeni kanal ses mesajları   │
│ 0x5-0xF    │ Rezerve                     │
└────────────┴────────────────────────────┘
```

### 8.3 MIDI 2.0 Geçiş Stratejisi

| Faz | Süre | Kapsam |
|-----|------|--------|
| F1 | 0 ay | MIDI 1.0 tam destek |
| F2 | 6 ay | MIDI 1.0 + UMP altyapısı |
| F3 | 12 ay | MIDI 2.0 temel mesajlar (CC, Note) |
| F4 | 18 ay | MIDI 2.0 property exchange |
| F5 | 24 ay | MIDI 2.0 tam profil desteği |

---

## 9. MIDI → OSC Bridge (Opsiyonel)

### 9.1 Bridge Mimarisi

```
MIDI Controller (CC#20, Value: 64)
    │
    ▼
MIDI → OSC Dönüştürücü
    │
    ▼
OSC Mesajı: /eq/band/5/gain 0.5
    │
    ▼
Neva Engine veya Harici OSC Client (TouchOSC, Lemur)
```

### 9.2 OSC Adres Formatı

| MIDI Kaynağı | OSC Adresi |
|-------------|-----------|
| CC 7, Channel 0 | `/midi/cc/0/7` |
| Note On 60, Ch 0 | `/midi/note/0/60` 1.0 |
| Note Off 60, Ch 0 | `/midi/note/0/60` 0.0 |
| Program Change 5 | `/midi/pc/0/5` |
| Pitch Bend | `/midi/pitch/0` -1.0...1.0 |

### 9.3 OSC Konfigürasyonu

```json
{
  "osc": {
    "enabled": false,
    "listenPort": 9743,
    "sendPort": 9744,
    "sendHost": "127.0.0.1",
    "addressPrefix": "/neva",
    "midiToOsc": true,
    "oscToMidi": false
  }
}
```

---

## 10. MIDI Routing

### 10.1 MIDI Yönlendirme Matrisi

```
MIDI Giriş Cihazları:
┌─────────────┐
│ USB MIDI 1  │──┐
└─────────────┘  │
┌─────────────┐  │    ┌──────────────┐    ┌──────────────┐
│ BLE MIDI 1  │──┼───►│ MIDI Router  │───►│ MIDI Output  │
└─────────────┘  │    │              │    └──────────────┘
┌─────────────┐  │    │ · Filtreleme │
│ Network MIDI│──┤    │ · Dönüştürme │    ┌──────────────┐
└─────────────┘  │    │ · Kanal map  │───►│ VST3 Synth   │
┌─────────────┐  │    │ · CC remap   │    └──────────────┘
│ Sanal MIDI  │──┘    └──────────────┘
└─────────────┘
```

### 10.2 MIDI Filtreleme

```json
{
  "midiFilters": [
    {
      "name": "Sadece CC",
      "passTypes": ["controlChange"],
      "blockTypes": ["noteOn", "noteOff", "programChange"]
    },
    {
      "name": "Kanal 1 sadece",
      "channelFilter": [0],
      "restrictOthers": true
    },
    {
      "name": "CC Remap",
      "remapRules": [
        {"fromCC": 20, "toCC": 7},
        {"fromCC": 21, "toCC": 10}
      ]
    }
  ]
}
```

---

## 11. MIDI API

### 11.1 WebSocket JSON-RPC

```json
// MIDI Learn başlat
{"method": "midi.learnStart", "params": {
    "target": "eq.band.10.gain"
}, "id": 1}

// MIDI Learn durdur
{"method": "midi.learnStop", "params": {}, "id": 2}

// Mapping listesi
{"method": "midi.listMappings", "params": {}, "id": 3}

// Mapping ekle (manuel)
{"method": "midi.addMapping", "params": {
    "target": "bus.master.volume",
    "sourceType": "cc",
    "channel": 0,
    "ccNumber": 7,
    "inRange": [0, 127],
    "outRange": [-70, 6],
    "curve": "logarithmic"
}, "id": 4}

// Mapping sil
{"method": "midi.removeMapping", "params": {"target": "bus.master.volume"}, "id": 5}

// Profil yükle
{"method": "midi.loadProfile", "params": {"name": "akai_mpk_mini"}, "id": 6}

// Profil kaydet
{"method": "midi.saveProfile", "params": {"name": "benim_controller"}, "id": 7}

// MIDI saat sync
{"method": "midi.setClockMode", "params": {"mode": "master"}, "id": 8}

// MIDI cihaz listesi
{"method": "midi.listDevices", "params": {}, "id": 9}

// MIDI mesaj gönder (çıkış)
{"method": "midi.sendMessage", "params": {
    "type": "cc",
    "channel": 0,
    "number": 7,
    "value": 100
}, "id": 10}
```

### 11.2 MIDI Olay Bildirimi (Event Stream)

```json
// Server → Client: MIDI olayı
{
  "jsonrpc": "2.0",
  "method": "midi.event",
  "params": {
    "type": "cc",
    "channel": 0,
    "number": 20,
    "value": 64,
    "deviceName": "AKAI MPK mini",
    "timestamp": 1723456789000
  }
}
```

---

## 12. Zamanlama ve Performans

### 12.1 MIDI Zamanlama Hassasiyeti

| Kaynak | Jitter | Gecikme | Not |
|-------|--------|---------|-----|
| USB MIDI | < 0.1 ms | < 1 ms | En iyi |
| 5-pin DIN | < 0.05 ms | < 1 ms | Donanım seviyesi |
| BLE MIDI | 1-3 ms | 10-20 ms | Kablosuz |
| RTP-MIDI | 0.1-1 ms | 1-5 ms | Ağ kalitesine bağlı |
| Virtual Port | < 0.1 ms | < 1 ms | Aynı bilgisayar |

### 12.2 MIDI Buffer Stratejisi

- MIDI callback: Gerçek zamanlı, audio thread benzeri öncelik
- MIDI buffer: 256 mesaj kapasiteli, lock-free SPSC
- İşleme: Audio callback başında toplu MIDI işleme
- Timestamp: `juce::Time::getHighResolutionTicks()` (mikrosaniye)

---

## 13. Yapılandırma

```json
{
  "midi": {
    "enabled": true,
    "inputDevices": ["auto"],
    "outputDevices": ["auto"],
    "defaultProfile": "none",
    "learnMode": false,
    "clock": {
      "mode": "off",
      "sendMMC": true,
      "bpmDetectionEnabled": false
    },
    "midi2": {
      "enabled": false,
      "umpSupported": false
    },
    "osc": {
      "enabled": false,
      "listenPort": 9743,
      "sendPort": 9744
    },
    "filters": [],
    "feedbackToMotorizedFaders": true
  }
}
```

---

## 14. Gelecek Genişletmeler

| Özellik | Açıklama |
|---------|----------|
| MIDI 2.0 tam profil | Otomatik cihaz tanıma ve konfigürasyon |
| MPE (MIDI Poly. Exp.) | Nota başına bağımsız kontrol |
| Ableton Link | Ağ üzerinden tempo sync |
| DMX Bridge (MIDI→DMX) | Işık kontrolü entegrasyonu |
| Lua scripting | Özel MIDI işleme scriptleri |
| MIDI kayıt/playback | MIDI sequencer, düzenleme |
| OSC tam destek | TouchOSC, Lemur template |
| Web MIDI API | Tarayıcıdan doğrudan MIDI kontrolü |

---

*Neva Engine v1.0.0 — MIDI Sistemi Spesifikasyonu*
*CoreMusic Ekosistemi · GPLv3 · C++20 · JUCE 8.x*
