---
type: technical-spec
project: Neva Engine
component: eq-plugin-hosting
updated: 2026-06-03
refs:
  - JUCE VST3 Hosting: https://github.com/juce-framework/JUCE
  - DPF Plugin Formats: https://github.com/DISTRHO/DPF
  - CLAP Standard: https://cleveraudio.org/
---

# Neva Engine — Plugin Hosting (VST3/LV2/CLAP)

> JUCE tabanlı plugin host. Her strip'te ve master bus'ta ayrı plugin slot'ları.
> Send/Return loop üzerinde efekt plugin'leri.

---

## 1. Plugin Format Desteği

| Format | Platform | Open Source | Durum |
|--------|----------|-----------|-------|
| **VST3** | Win/Mac/Linux | ✗ (Steinberg SDK) | Birincil |
| **LV2** | Win/Mac/Linux | ✅ (açık kaynak) | Desteklenir |
| **CLAP** | Win/Mac/Linux | ✅ (açık kaynak) | Desteklenir |
| **AU** | macOS only | ✗ (Apple) | macOS'te desteklenir |
| **LADSPA** | Linux | ✅ | Temel destek |

---

## 2. Plugin Slot Mimarisi

```
┌───────────────────────────────────────────────────────┐
│  STRIP 1 — Plugin Chain                               │
│  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐     │
│  │Plugin A │→│Plugin B │→│Plugin C │→│Plugin D │     │
│  │(VST3)   │ │(LV2)    │ │(CLAP)   │ │(VST3)   │     │
│  └─────────┘ └─────────┘ └─────────┘ └─────────┘     │
│  Max 8 plugin per strip                                │
├───────────────────────────────────────────────────────┤
│  MASTER BUS — Final Processing                         │
│  ┌─────────┐ ┌─────────┐                               │
│  │Plugin E │→│Plugin F │  (Mastering chain)            │
│  │(VST3)   │ │(VST3)   │                               │
│  └─────────┘ └─────────┘                               │
├───────────────────────────────────────────────────────┤
│  SEND/RETURN LOOP                                      │
│  ┌─────────┐    ┌─────────┐                            │
│  │ Send    │→──→│Plugin G │→──→ Return (Reverb/Delay) │
│  │ (Bus 1) │    │(VST3)   │                            │
│  └─────────┘    └─────────┘                            │
└───────────────────────────────────────────────────────┘
```

---

## 3. Plugin Scanner

```cpp
class PluginScanner {
    std::vector<fs::path> scanPaths = {
        "C:\\Program Files\\Common Files\\VST3\\",      // Windows VST3
        "/Library/Audio/Plug-Ins/VST3/",                 // macOS VST3
        "/usr/lib/vst3/",                                // Linux VST3
        "/usr/lib/lv2/",                                 // Linux LV2
        "/usr/lib/clap/",                                // Linux CLAP
        "~/.vst3/",                                      // User VST3
        "~/.lv2/",                                       // User LV2
        "~/.clap/",                                      // User CLAP
    };

    struct PluginInfo {
        std::string name;
        std::string vendor;
        std::string format;   // "VST3", "LV2", "CLAP"
        std::string path;
        int numInputs;
        int numOutputs;
        std::string category; // "EQ", "Compressor", "Reverb", "Delay", "Other"
        bool hasEditor;       // Custom UI support
    };

    std::vector<PluginInfo> scanAll() {
        std::vector<PluginInfo> plugins;
        for (auto& path : scanPaths) {
            if (fs::exists(path)) {
                auto found = scanDirectory(path);
                plugins.insert(plugins.end(), found.begin(), found.end());
            }
        }
        return plugins;
    }
};
```

---

## 4. Plugin Host (JUCE)

```cpp
class PluginHost {
    juce::AudioPluginFormatManager formatManager;
    juce::KnownPluginList knownPlugins;

    void initialize() {
        // Register all formats
        formatManager.addDefaultFormats(); // VST3, AU, LV2

        // CLAP (JUCE 8.x native support)
        // formatManager.addFormat(new CLAPPluginFormat());

        // Scan
        juce::PluginDirectoryScanner scanner(knownPlugins,
            formatManager, searchPaths, true, deadMansPedalFile);
        while (scanner.scanNextFile()) {}

        knownPlugins.sort(juce::KnownPluginList::sortByManufacturer);
    }

    std::unique_ptr<juce::AudioPluginInstance> loadPlugin(
        const PluginInfo& info, double sampleRate, int blockSize
    ) {
        juce::PluginDescription desc;
        desc.name = info.name;
        desc.fileOrIdentifier = info.path;
        desc.pluginFormatName = info.format;

        auto instance = formatManager.createPluginInstance(
            desc, sampleRate, blockSize, errorMsg
        );
        return instance;
    }
};
```

---

## 5. Per-Strip Plugin Chain

```cpp
class StripPluginChain {
    static constexpr int MAX_PLUGINS = 8;

    struct PluginSlot {
        std::unique_ptr<juce::AudioPluginInstance> instance;
        bool enabled = true;
        bool hasCustomUI = false;
    };

    std::array<PluginSlot, MAX_PLUGINS> slots;
    std::array<bool, MAX_PLUGINS> bypass;

    void processBlock(juce::AudioBuffer<float>& buffer) {
        for (int i = 0; i < MAX_PLUGINS; ++i) {
            if (slots[i].instance && !bypass[i] && slots[i].enabled) {
                juce::MidiBuffer midi; // boş
                slots[i].instance->processBlock(buffer, midi);
            }
        }
    }

    void loadPlugin(int slotIndex, const PluginInfo& info) {
        slots[slotIndex].instance = pluginHost.loadPlugin(info, sampleRate, blockSize);
        slots[slotIndex].instance->prepareToPlay(sampleRate, blockSize);
    }
};
```

---

## 6. Send/Return Loop

```cpp
class SendReturnBus {
    static constexpr int NUM_BUSES = 4;

    struct Bus {
        float sendLevel;    // -60 to 0 dB
        float returnLevel;  // -60 to 0 dB
        bool preFader;      // Pre/post fader send
        std::unique_ptr<juce::AudioPluginInstance> fxPlugin;
    };

    Bus buses[NUM_BUSES];

    // Bus 1: Reverb
    // Bus 2: Delay
    // Bus 3: FX (user plugin)
    // Bus 4: FX (user plugin)

    void processSend(int stripIndex, juce::AudioBuffer<float>& buffer) {
        for (int b = 0; b < NUM_BUSES; ++b) {
            if (buses[b].fxPlugin) {
                auto sendBuffer = buffer;
                sendBuffer.applyGain(std::pow(10.0f, buses[b].sendLevel / 20.0f));
                buses[b].fxPlugin->processBlock(sendBuffer, juce::MidiBuffer());
                // Mix return
            }
        }
    }
};
```

---

*Neva Engine Plugin Hosting v1 — VST3/LV2/CLAP · 2026-06-03*
