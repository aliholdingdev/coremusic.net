---
title: "Neva Engine — Neva Mixer/Enhancer Ürün Planı (Web UI + Native C++)"
type: doc
category: audio-engine
version: 1.0.0
status: planlama
authority: "SSOT: 02-dsp-zincir-mimari · 04-reverse-voicemeeter-fxsound · CLAUDE §19"
updated: 2026-10-14
---

# Neva Mixer — Ürün Planı (VoiceMeeter + FxSound sınıfı, daha profesyonel)

**Zorunlu Bağlantılar:** [[04-reverse-voicemeeter-fxsound]] · [[02-dsp-zincir-mimari]] · [[01-gelistirme-plani]] · [[03-isletim-sistemi-kod-ornekleri]] · [[brain.md]] §7

## §1 Ürün Tanımı (eli10)

**Neva Mixer**, sistemin sesini yakalayan, 31 bant EQ + dinamik + FX işleyen ve
hoparlöre/kulaklığa geri gönderen bir **masaüstü ses kontrol merkezi**:
- **Yerel çekirdek:** C++20 · tek process · kilitlenmez (lock-free) RT audio thread
  (Windows WASAPI/ASIO · Linux ALSA/PipeWire · macOS CoreAudio — bkz `os/`)
- **Web arayüzü:** Aynı makinede çalışan yerel HTTP/WebSocket sunucusu (tarayıcıda
  `http://127.0.0.1:port`) — PCM'i tarayıcıya değil, **çekirdeğe** işlettirir; tarayıcı
  yalnız kontrol + görselleştirme (RTA/LUFS meter WebSocket akışı) yapar
- **Ek web uygulaması:** Dosya tabanlı PCM/EQ editörü — seçili WAV/FLAC'ı Web Audio API
  (Float32 + OfflineAudioContext) ile tarayıcıda işler, kaydeder (sunucuya yüklenmez, gizlilik)

## §2 Mimari (3 katman)

> **Control plane netleşti (Q2, 2026-10-15):** Neva kurulumunda **yerel PHP sunucusu** çalışır:
> (1) Neva'nın kendi PHP paneli (web mixer UI backend'i) · (2) **diğer uygulamalar için özel API
> sistem sunucusu** (entegrasyon yüzeyi — 3. parti uygulamalar PHP REST/WS API ile EQ/FX/preset/
> çıkış kontrolü yapar). CoreMusic Control Service (port 81) = ikinci halka (uzaktan erişim).
> Katmanlar: [1] Web UI (PHP+JS) / [2] C# Windows app / [3] mobil native (Swift/Kotlin) /
> [4] entegrasyon API (PHP) — hepsi aynı yerel C++ çekirdeğe WS/REST JSON ile bağlanır.

```
┌─────────────────────────────────────────────────────────┐
│ Layer 3: Web UI (vanilla JS + canvas WebGL RTA)         │
│  - mixer view (strip/bus fader, EQ curve, meter)        │
│  - file editor (Web Audio API, Float32 PCM)             │
├─────────────────────────────────────────────────────────┤
│ Layer 2: Control Plane (C++ embedded server)            │
│  - WebSocket JSON: param set/get, meter push (30fps)    │
│  - MCP-uyumlu tool şeması (FxSound fxmcp ilhamı)        │
│  - preset: JSON save/load/import/export                 │
├─────────────────────────────────────────────────────────┤
│ Layer 1: NevaEngineCore (C++20 RT, noexcpt/zero-alloc)  │
│  - capture (loopback/ALSA/Pulse) → process → render     │
│  - 02-dsp-zincir 8 aşama + FX rack                      │
│  - strip×bus matrix (8×8, 8.1 dahil)                    │
└─────────────────────────────────────────────────────────┘
```

**Bağımlılık kontrolü:** Layer 1 → yalnız OS audio API + vendor lib (JUCE/miniaudio…).
Layer 2 → `cpp-httplib` **veya** JUCE `EmbeddedWebServer` (karar: Faz 0 spike —
⚠️ JUCE 9'da embedded web sunucusu var mı doğrulanacak; yoksa cpp-httplib MIT).
Layer 3 → framework yok (vanilla JS, proje standardı: CoreMusic SPA ile tutarlı).

## §3 Ses İşleme Özellikleri (RE envanterinden — clean-room)

### §3.1 Mixer (VoiceMeeter Potato seviyesi + üstü)
- 8 strip (4 physical + 4 virtual) / 8 bus · her strip: gain, L/R pan, mute, solo, label, comp satırı
- **10 fiziksel çıkış (A1–A10)** — her çıkış ayrı cihaza (hoparlör + kulaklık + TV + amfi … aynı anda);
  aux-style çıkış yönetimi (kullanıcı onayı 2026-10-14, 06 §3-Q1)
- **Otomatik cihaz yönetimi:** hot-plug tespiti + cihaza özel otomatik profil yükleme
  (06 §3-Q3 — ikisi de) · cihaza göre EQ/kalibrasyon atama (06 R5)
- Bus modları: mono/mute/center-only/LFE-only/rear-only/stereo-rep + **8.1 matrix**
- Composite/monitor sync delay (ms) — VoiceMeeter `MaxMonDelayMS` karşılığı

### §3.2 FX Rack (FxSound 7 efekt karşılığı, 0–10 skala korunur — ad taklidi YASAK, karşılık = matris)
| Neva FX | Eşdeğer | Algoritma kaynağı |
|---|---|---|
| Bass Boost | FxSound Bass Boost | low-shelf + transients (Airwindows referans) |
| Ambience | FxSound Ambience | kısa convolution/Allpass reverb (4 mod) |
| Surround | FxSound Surround | mid/side width + upmix (BUS_REARONLY mantığı) |
| Fidelity | FxSound Fidelity | harmonic enhance (Airwindows `Harmonics` stil, MIT) |
| Dynamic Boost | FxSound Dynamic Boost | multiband comp (2-way LR4 crossover) |
| Clarity | FxSound Clarity | transient/tone split |
| Headphone EQ | FxSound Headphone | HRTF başlangıç ⚠️ (sonraki faz, VERIFICATION REQUIRED) |
| **Concert Hall** (yeni, 06 R6) | J7 SoundAlive "Concert Hall" sınıfı — **hoparlör + kulaklık ikisinde de** (Q2 onayı) | geniş sahne reverb:early-reflection + width; hedef telefondaki "geniş alan konser salonu" hissi (kanıt: 06 §2) |
- **Dynamic Boost ≠ Bass Boost (06 R8 — bağlayıcı):** iki AYRI modül/slider; dynamic boost multiband
  olarak bass bandına da dokunur ama bass boost'ın shelf'i KULLANMAZ — FxSound'dan farklı algoritma
- **Tek tuş ev preset (06 R7):** preset.load tek dokunuş → EQ + FX + çıkış routing tek paket
- Bypass: tek `on` anahtarı (WSS `fx.bypass`)

### §3.3 VoiceMeeter'in olmayıp Neva'da olacak (profesyonel artı)
- **True-peak limiter + LUFS** ölçüm/normalizasyon (libebur128) — VoiceMeeter'da yok
- **31 bant parametrik EQ** (VoiceMeeter 6 bant / bus 15 bant grafik üstü)
- **Açık kaynak lisans** + taşınabilirlik (Linux/macOS — VoiceMeeter Windows-only)
- Web UI + MCP-uyumlu API (FxSound fxmcp tek rakip)

## §4 Control Plane Sözleşmesi (WebSocket JSON — özet)

```jsonc
// istem → çekirdek (param set)
{ "id": 1, "op": "set", "path": "strip[0].gain_db", "value": -6.0 }
{ "id": 2, "op": "set", "path": "strip[0].eq.band[4].gain_db", "value": 3.5 }
{ "id": 3, "op": "set", "path": "fx.surround", "value": 7 }        // 0-10
// çekirdek → istem (meter push, 30fps)
{ "ev": "meter", "t": 1234.5, "strip": [{"pk":[..],"rms":[..]}], "lufs": -14.2, "tp": -1.1 }
// preset
{ "op": "preset.save", "name": "gece" } / { "op": "preset.load", "name": "gece" }
```
- Senkron (request/response) + asenkron (meter) tek WS kanalı · OHLC benzeri id korelasyonu
- MCP tool şeması birebir aynı path'lerle (`set_effect`, `set_eq_band`, `save_preset`) →
  gelecekte `fxmcp` gibi LLM erişimi bedava

## §5 Web Dosya Editörü (PCM/Web Audio — ayrı modül)

- **Yol A (tarayıcı-içi):** `<audio>` → `decodeAudioData` (Float32) → OfflineAudioContext
  ile biquad chain (EQ) + DynamicsCompressor → `AudioBuffer → WAV encode` → indir.
  Sınırlama: dosya tarayıcıdan çıkmaz · büyük dosya (>.gitattributes) için slice-okuma yok
  (⚠️ planda: `decodeAudioData` 200MB üstü riskli — kabul <100MB dosya)
- **Yol B (yerel çekirdek):** Aynı EQ DSP'si C++'ta (`neva.engine/core/eq`), web UI'dan
  sürülen; PCM dosyasını sunucu tarafında işler (uzun dosyalar için)
- Öncelik: **Yol A hemen** (sıfır backend), Yol B Faz 5

## §6 Faz Planı (01-gelistirme-plani'ne ek katman)

| Faz | İş | Çıktı | Süre |
|---|---|---|---|
| **F6.1** | Control plane spike: cpp-httplib vs JUCE embedded WS | ADR + mini PoC | 1 gün |
| **F6.2** | Mixer çekirdek: 8×8 matrix + fader/pan/mute/solo (ses geçişi) | WASAPI loopback→render döngüsü | 2 gün |
| **F6.2b** | **10 çıkış routing (A1–A10) + hot-plug tespit + otomatik profil** (06 R2/R5 — Q1/Q3 onaylı) | çıkış matrix API + device manager | 2 gün |
| **F6.3** | Web mixer UI: canvas fader + EQ curve + WS meter | tarayıcıdan kontrol | 2 gün |
| **F6.4** | FX rack (7 efekt) + preset JSON | fx.* API | 2 gün |
| **F6.5** | Web dosya editörü (Yol A — Web Audio) | bağımsız sayfa | 1 gün |
| **F7** | sv1 sanal sürücü araştırması (Windows ATKS/WDM-capis) + VBAN-eşdeğer ağ | ⚠️ PLANNED | — |

**Öncelik sırası:** F6.2 (ses gerçekten çalıyor) → F6.2b (10 çıkış + otomatik cihaz) → F6.3 (görünür kontrol) → F6.4 → F6.5.

## §7 Riskler
1. **Loopback gecikmesi** (WASAPI loopback monitor için yavaştır): kabul <40ms; ASIO/exclusive
   yolu cihaz-ağır kullanıcı için Faz 1'de (os/01) — VoiceMeeter'ın kendi sanal sürücüsü gibi
   gerçek sanal sürücü **F7**'ye ertelendi (sürücü yazımı tek başına projedir).
2. **FxSound rekabet hattı:** efekt adları/marka taklidi YASAK — algoritma clean-room,
   UI kendi tasarımı (`.ai/ui-design` mockup gate geçerli: web mixer UI için mockup ZORUNLU).
3. **JUCE AGPLv3:** dağıtım açık kaynak olacak (karar 2026-10-14) — CoreMusic repo lisansı
   ile uyumlu; kapalı dağıtım gerekirse lisans yeniden gözden geçirilir.
4. **Ghidra yok:** RE yalnız string katmanı — algoritma detayı istenirse Ghidra 12.1.x
   kurulumu (JDK 21 mevcut ✔, GHIDRA_INSTALL_DIR tanımsız ✗).

**Authority:** Architect (L1) · Embedded Engineer (L1) · Statü: PLANNING
