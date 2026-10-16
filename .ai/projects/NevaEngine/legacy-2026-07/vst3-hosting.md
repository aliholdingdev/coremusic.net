---
type: technical-spec
project: Neva Engine
module: vst3-hosting
version: 1.0.0
date: 2026-06-02
status: draft
language: tr-TR
---

# Neva Engine — VST3 Plugin Hosting

## 1. Genel Bakış

Neva Engine, Steinberg VST3 standardını tam olarak destekleyen, JUCE
AudioPluginHost mimarisini referans alan, sandbox korumalı, çok formatlı
bir plugin hosting sistemidir.

### Tasarım Hedefleri

| Hedef | Açıklama |
|-------|----------|
| Format desteği | VST3 (birincil), VST2, AU, LV2 |
| Stabilite | Plugin crash'i motoru etkilemez |
| Performans | Sandbox IPC overhead < 1 ms |
| Esneklik | Sınırsız plugin zinciri, drag-drop sıralama |
| Parametre | MIDI CC mapping, otomasyon, preset yönetimi |
| GUI | Native plugin arayüzü hosting |

### Desteklenen Formatlar

| Format | Platform | Durum | Öncelik |
|--------|----------|-------|---------|
| VST3 | Win/Mac/Linux | Birincil | 1 |
| VST2 | Win/Mac/Linux | Legacy (sonlandırılıyor) | 2 |
| AU (AudioUnit) | macOS/iOS | macOS native | 2 |
| LV2 | Linux | Linux native | 3 |
| CLAP | Win/Mac/Linux | Yeni standart (gözlem) | 4 |
| AAX | Win/Mac | Pro Tools (düşük öncelik) | 5 |
| LADSPA | Linux | Legacy Linux | 5 |

---

## 2. VST3 SDK Entegrasyonu

### 2.1 Steinberg VST3 SDK

- Versiyon: VST 3.7.8+
- Lisans: GPLv3 (Neva Engine uyumlu) veya Steinberg Proprietary
- Modüller: `pluginterfaces/`, `public.sdk/`, `base/`
- JUCE wrapper: `juce_audio_plugin_client` → alternatif olarak doğrudan SDK

### 2.2 VST3 Bileşenleri

| Bileşen | Arayüz | Açıklama |
|---------|--------|----------|
| IComponent | Temel plugin arayüzü | Ses işleme, parametreler |
| IEditController | UI kontrol arayüzü | Parametre değişiklikleri, state |
| IAudioProcessor | Ses işleme | `process()` callback |
| IConnectionPoint | Bağlantı noktası | Component ↔ Controller iletişim |
| IUnitInfo | Birim bilgisi | Program list, unit yapısı |
| IProgramListData | Program verisi | Preset verisi |
| IMidiMapping | MIDI eşleme | MIDI CC → parametre |
| IContextMenu | Bağlam menüsü | Sağ tık menüsü |

### 2.3 VST3 Plugin Yaşam Döngüsü

```
IPluginFactory::createInstance()
    ↓
IComponent::initialize()
    ↓
IAudioProcessor::setupProcessing()
    ↓
IAudioProcessor::setBusArrangements()
    ↓
IComponent::setActive(true)
    ↓
IAudioProcessor::process()  ← Ana ses işleme döngüsü
    ↓
IComponent::setActive(false)
    ↓
IComponent::terminate()
```

---

## 3. Plugin Tarama Sistemi

### 3.1 Tarama Stratejisi

```cpp
class PluginScanner {
public:
    struct PluginInfo {
        std::string name;
        std::string vendor;
        std::string version;
        std::string category;       // "EQ", "Compressor", "Reverb", vb.
        PluginFormat format;
        std::string filePath;
        int numInputs;
        int numOutputs;
        int numParameters;
        bool hasEditor;
        bool isInstrument;
        uint64_t latencySamples;
        std::vector<std::string> tags;
    };

    std::vector<PluginInfo> scanDirectory(const std::string& path);
    std::vector<PluginInfo> scanAll(const std::vector<std::string>& paths);
    void rescanChanged();
};
```

### 3.2 Tarama Yolları

| Platform | Varsayılan Yol |
|----------|---------------|
| Windows | `C:\Program Files\Common Files\VST3\` |
| macOS | `/Library/Audio/Plug-Ins/VST3/` |
| macOS (user) | `~/Library/Audio/Plug-Ins/VST3/` |
| Linux | `/usr/lib/vst3/`, `~/.vst3/` |
| Custom | `neva-engine.json` içinde tanımlı |

### 3.3 Kara Liste Sistemi

```json
{
  "plugins": {
    "blacklist": [
      {"name": "unstable_beta_plugin", "reason": "Crash raporu: 3/5 test"},
      {"path": "*/malware_scanner.vst3", "reason": "İmza doğrulama başarısız"},
      {"vendor": "UnknownVendor_0xDEAD", "reason": "Şüpheli kaynak"}
    ]
  }
}
```

---

## 4. Plugin Sandbox (İzole Process)

### 4.1 Sandbox Mimarisi

```
┌──────────────────────────────────────┐
│  Ana Engine Process (neva-engine)    │
│                                      │
│  ┌────────────────────────────┐      │
│  │ PluginHostProxy            │      │
│  │ (Plugin'ları temsil eder)  │      │
│  └──────────┬─────────────────┘      │
│             │ IPC (Shared Memory)    │
│             │ + Pipe (kontrol)       │
├─────────────┼────────────────────────┤
│             ▼                        │
│  ┌────────────────────────────┐      │
│  │ Sandbox Process            │      │
│  │ (Her plugin için ayrı)     │      │
│  │                            │      │
│  │  · seccomp/AppContainer    │      │
│  │  · düşük integrity level   │      │
│  │  · kısıtlı dosya erişimi  │      │
│  │  · kısıtlı ağ erişimi     │      │
│  │  · CPU/Memory limit        │      │
│  └────────────────────────────┘      │
└──────────────────────────────────────┘
```

### 4.2 Sandbox Özellikleri

| Özellik | Windows | macOS | Linux |
|---------|---------|-------|-------|
| Process izolasyonu | AppContainer | Sandbox | seccomp + namespace |
| Dosya erişimi | Sadece plugin dizini | Sadece plugin dizini | Sadece plugin dizini |
| Ağ erişimi | Yasak | Yasak | Yasak |
| CPU limiti | Job Object | — | cgroups v2 |
| Bellek limiti | Job Object | — | cgroups v2 |
| Crash koruması | Otomatik yeniden başlatma | Otomatik yeniden başlatma | Otomatik yeniden başlatma |

### 4.3 IPC Protokolü

| Kanal | Mekanizma | Veri |
|-------|-----------|------|
| Ses buffer'ları | Shared Memory (Ring Buffer) | Float32, N kanal × M sample |
| Parametre değişikliği | Pipe / Socket | JSON (küçük payload) |
| GUI render | Shared Memory + Event | Framebuffer (GPU paylaşımı) |
| Preset verisi | Pipe | Binary blob |
| Durum sorgulama | Pipe | JSON |

---

## 5. Plugin Zinciri

### 5.1 Zincir Yapısı

Her bus, sınırsız uzunlukta bir plugin zincirine sahip olabilir:

```
Bus Sinyali
    │
    ▼
┌──────────────┐
│ Plugin 1     │  (Örn: FabFilter Pro-Q3)
├──────────────┤
│ Plugin 2     │  (Örn: ValhallaRoom)
├──────────────┤
│ Plugin 3     │  (Örn: iZotope Ozone)
├──────────────┤
│    ...       │
├──────────────┤
│ Plugin N     │
└──────┬───────┘
       │
       ▼
Bus Çıkışı
```

### 5.2 Zincir Yönetimi

```cpp
class PluginChain {
    struct PluginSlot {
        PluginProxy* plugin;
        bool enabled;
        float wetDryMix;        // 0.0 (dry) ... 1.0 (wet)
        float inputGain;        // -24 ... +24 dB
        float outputGain;       // -24 ... +24 dB
        int latencySamples;     // Plugin gecikmesi
    };

    std::vector<PluginSlot> slots;

public:
    void addPlugin(const std::string& pluginId, int position = -1);
    void removePlugin(int position);
    void movePlugin(int from, int to);
    void bypassPlugin(int position, bool bypass);
    void processBlock(float* buffer, int numSamples, int numChannels);
    int getTotalLatency() const;  // Tüm plugin'lerin toplam gecikmesi
};
```

### 5.3 Drag-Drop Sıralama

```
┌────────────────────────────────────────────┐
│  Plugin Zinciri GUI                        │
│                                            │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐ │
│  │ Pro-Q3   │→│ CLA-76   │→│ Valhalla │ │ │
│  │ EQ       │  │ Comp     │  │ Reverb   │ │ │
│  └──────────┘  └──────────┘  └──────────┘ │
│       ↕              ↕              ↕       │
│    Sürükle-bırak ile sıralama değiştirme   │
└────────────────────────────────────────────┘
```

---

## 6. Parametre Yönetimi

### 6.1 Plugin Parametreleri

```cpp
struct PluginParameter {
    int id;
    std::string name;
    std::string unit;           // "dB", "ms", "Hz", "%"
    float defaultValue;
    float minValue;
    float maxValue;
    float stepSize;             // 0 = sürekli
    bool isAutomatable;
    bool isHidden;
    std::string groupName;      // Parametre grubu
};
```

### 6.2 MIDI CC Mapping

```json
{
  "mappings": [
    {
      "pluginId": "fabfilter_proq3",
      "paramId": 5,
      "paramName": "Band 1 Gain",
      "midiChannel": 0,
      "midiCC": 20,
      "midiMin": 0,
      "midiMax": 127,
      "paramMin": -18.0,
      "paramMax": 18.0,
      "curve": "linear"
    },
    {
      "pluginId": "valhallaroom",
      "paramId": 2,
      "paramName": "Decay",
      "midiChannel": 0,
      "midiCC": 21,
      "midiMin": 0,
      "midiMax": 127,
      "paramMin": 0.1,
      "paramMax": 100.0,
      "curve": "logarithmic"
    }
  ]
}
```

---

## 7. Preset Yönetimi

### 7.1 Preset Formatları

| Format | Dosya | Açıklama |
|--------|-------|----------|
| VST3 Preset | `.vstpreset` | VST3 standart preset |
| JUCE Preset | `.xml` | JUCE ValueTree XML |
| FXP/FXB | `.fxp` / `.fxb` | VST2 formatı (legacy) |
| AU Preset | `.aupreset` | macOS AU formatı |
| Neva Preset | `.nvaplug` | Dahili format |

### 7.2 Preset Tarayıcı

```
┌────────────────────────────────────────────┐
│  Preset Browser                             │
│                                             │
│  ┌─ Factory Presets ─────────────────────┐ │
│  │  · Clean Guitar                       │ │
│  │  · Warm Vocal                         │ │
│  │  · Punchy Drums                       │ │
│  │  · ...                                │ │
│  └───────────────────────────────────────┘ │
│                                             │
│  ┌─ User Presets ────────────────────────┐ │
│  │  · Benim EQ Ayarlarım                  │ │
│  │  · Düğün Salonu Reverb                 │ │
│  │  · Podcast Compressor                  │ │
│  └───────────────────────────────────────┘ │
│                                             │
│  ┌─ Community Presets (online) ──────────┐ │
│  │  · Popüler presetler                   │ │
│  └───────────────────────────────────────┘ │
└────────────────────────────────────────────┘
```

### 7.3 Preset API

```json
// Plugin preset listesi
{"method": "plugin.listPresets", "params": {"pluginId": "proq3"}, "id": 1}

// Preset yükle
{"method": "plugin.loadPreset", "params": {"pluginId": "proq3", "presetName": "Warm Vocal"}, "id": 2}

// Preset kaydet
{"method": "plugin.savePreset", "params": {
    "pluginId": "proq3",
    "presetName": "Benim Ayarlar",
    "category": "User/Vocal"
}, "id": 3}

// Plugin state al/kaydet (full state dump)
{"method": "plugin.getState", "params": {"pluginId": "proq3"}, "id": 4}
{"method": "plugin.setState", "params": {"pluginId": "proq3", "state": "base64..."}, "id": 5}
```

---

## 8. Plugin GUI Hosting

### 8.1 GUI Modları

| Mod | Açıklama | Avantaj |
|-----|----------|---------|
| Native GUI | Plugin'in kendi arayüzü | Tam plugin deneyimi |
| JUCE Generic UI | JUCE'nin otomatik UI'si | Hafif, tutarlı |
| Hybrid | Native + JUCE overlay | Özel kontroller ekleme |

### 8.2 Pencere Yönetimi

```cpp
class PluginWindowManager {
public:
    // Plugin editor'ü aç
    juce::AudioProcessorEditor* openEditor(const std::string& pluginId);

    // Pencere konumlandırma
    void setEditorPosition(const std::string& pluginId, int x, int y, int w, int h);

    // Pencereyi her zaman üstte tut
    void setAlwaysOnTop(const std::string& pluginId, bool alwaysOnTop);

    // Tüm pencereleri kapat
    void closeAllEditors();

    // Pencere düzenini kaydet/yükle
    void saveLayout(const std::string& path);
    void loadLayout(const std::string& path);
};
```

### 8.3 Plugin GUI IPC (Sandbox modunda)

```
Sandbox Process            Ana Process
     │                         │
     │  Plugin GUI render      │
     │  (OpenGL/DirectX)       │
     │──────────Framebuffer────►│  Shared GPU texture
     │                         │
     │                         │  Kullanıcı input
     │◄─────Mouse/Keyboard─────│  (tıklama, scroll, tuş)
     │                         │
```

---

## 9. Plugin Bridge (32-bit → 64-bit)

### 9.1 Bridge Mimarisi

```
┌──────────────────────────────────────────────┐
│  64-bit Ana Process                          │
│                                              │
│  ┌────────────────────────────┐              │
│  │ PluginBridgeClient         │              │
│  │ (64-bit tarafı)            │              │
│  └──────────┬─────────────────┘              │
│             │ IPC (Shared Memory + Pipe)     │
├─────────────┼────────────────────────────────┤
│             ▼                                │
│  ┌────────────────────────────┐              │
│  │ PluginBridgeServer.exe     │              │
│  │ (32-bit process)           │              │
│  │                            │              │
│  │  32-bit VST2/VST3 Plugin  │              │
│  └────────────────────────────┘              │
└──────────────────────────────────────────────┘
```

### 9.2 Bridge Özellikleri

- Otomatik algılama: 32-bit plugin tespit edilirse bridge devreye girer
- Şeffaf: Kullanıcı fark etmez, normal plugin gibi görünür
- IPC overhead: < 0.5 ms (shared memory audio)
- Limit: En fazla 4 bridge process aynı anda

---

## 10. Plugin Güvenlik

### 10.1 Güvenlik Katmanları

| Katman | Kontrol |
|--------|---------|
| İmza doğrulama | Authenticode (Win), Code Signing (Mac) |
| Hash kontrolü | SHA-256 dosya hash |
| Reputation check | VirusTotal API entegrasyonu (ops.) |
| Sandbox | İzole process, sınırlı yetki |
| Kara liste | Manuel/otomatik engelleme |
| Kaynak kontrolü | Bilinen vendor listesi |
| CPU limit | Plugin başına max %25 CPU |
| Bellek limit | Plugin başına max 2 GB |

### 10.2 Güvenlik Politikası

```json
{
  "pluginSecurity": {
    "signatureRequired": false,
    "sandboxMode": "strict",
    "untrustedVendorMode": "sandbox",
    "maxCPUPercent": 25,
    "maxMemoryMB": 2048,
    "autoBlacklist": {
      "enabled": true,
      "crashThreshold": 3,
      "crashWindow": 300
    },
    "reputationCheck": {
      "enabled": false,
      "apiKey": ""
    }
  }
}
```

---

## 11. Önerilen Plugin'ler

### 11.1 Kategori Bazında Öneriler

| Kategori | Plugin | Vendor | Format | Neden |
|----------|--------|--------|--------|-------|
| EQ | Pro-Q 3 | FabFilter | VST3/VST2/AU | Endüstri standardı |
| EQ | Nova | TDR | VST3/VST2/AU | Ücretsiz, paralel EQ |
| Compressor | Pro-C 2 | FabFilter | VST3/VST2/AU | Temiz, modern |
| Compressor | Kotelnikov | TDR | VST3/VST2/AU | Ücretsiz, mastering |
| Reverb | ValhallaRoom | ValhallaDSP | VST3/VST2/AU | Profesyonel, hafif |
| Reverb | ValhallaVintageVerb | ValhallaDSP | VST3/VST2/AU | Karakter reverb |
| Delay | ValhallaDelay | ValhallaDSP | VST3/VST2/AU | Yaratıcı delay |
| Mastering | Ozone 11 | iZotope | VST3/VST2/AU | Tam mastering paketi |
| Kanal şeridi | SSL Native | Waves | VST3/VST2/AU | SSL konsol sesi |
| Saturator | Saturn 2 | FabFilter | VST3/VST2/AU | Distortion/renklendirme |
| Limiter | Pro-L 2 | FabFilter | VST3/VST2/AU | True peak limiter |
| Gate | Pro-G | FabFilter | VST3/VST2/AU | Profesyonel gate |
| De-esser | Pro-DS | FabFilter | VST3/VST2/AU | Hassas de-essing |
| Analyzer | SPAN | Voxengo | VST3/VST2/AU | Ücretsiz spektrum |
| Synth | Vital | Vital Audio | VST3/VST2/AU | Ücretsiz, modern |

---

## 12. Plugin Durum İzleme

### 12.1 Sağlık Metrikleri

```json
{
  "pluginHealth": {
    "pluginId": "proq3",
    "status": "healthy",
    "crashCount": 0,
    "avgCPUMs": 0.8,
    "maxCPUMs": 2.3,
    "memoryMB": 45,
    "latencySamples": 0,
    "lastCrash": null,
    "uptimeSeconds": 86400
  }
}
```

### 12.2 Otomatik İyileştirme

| Durum | Aksiyon |
|-------|---------|
| Plugin crash | Otomatik yeniden başlat, 3 crash → bypass |
| CPU spike (>%50) | Uyarı logla, 10 sn sürekli → bypass |
| Bellek sızıntısı | Periyodik restart (saat başı) |
| GUI yanıtsız | GUI thread restart, ses işleme devam |
| Preset bozulması | Otomatik backup'tan geri yükle |

---

## 13. Yapılandırma

```json
{
  "plugins": {
    "scanPaths": [
      "C:/Program Files/Common Files/VST3",
      "C:/Program Files/Steinberg/VSTPlugins",
      "/Library/Audio/Plug-Ins/VST3",
      "~/.vst3"
    ],
    "autoScanOnStartup": true,
    "autoScanInterval": 3600,
    "sandboxEnabled": true,
    "bridgeEnabled": true,
    "maxBridgeInstances": 4,
    "blacklist": [],
    "guiMode": "native",
    "defaultPresetPath": "./presets/plugins/",
    "latencyCompensation": true
  }
}
```

---

*Neva Engine v1.0.0 — VST3 Plugin Hosting Spesifikasyonu*
*CoreMusic Ekosistemi · GPLv3 · C++20 · JUCE 8.x*
