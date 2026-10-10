---
type: architecture
category: layer-topic
title: "K000 · İşletim Sistemi Türü — Tizen (Smart TV Ses Yolu)"
date: 2026-10-10
status: active
version: 1.0.0
authority: SSOT
---

# Tizen — Ses Yolu ve Zemin

> Kaynak: Tizen Docs (Media Playback · Native Player API · Audio Output · Sound Manager) · Samsung Smart TV Web Device API — 2026-10-10 erişimli.
> Vault: `[[../os-master]]` · `brain.md:594` (User-Agent "Tizen/webOS/SmartTV" → `4k-tv` cihaz sınıfı)

## §1 Çekirdek Gerçekler (web-doğrulanmış)

| Konu | Gerçek | Kaynak |
|---|---|---|
| Üst API | **Player API** (`player_create` → `player_prepare` → `player_start`); dosya/bellek/akış (HTTP/RTSP) | Tizen Docs — Media Playback |
| Ham PCM | **Audio Output API** (`audio_out_create_new` → `audio_out_write`); mono/stereo; 5.5+'tan 8/16/24/32-bit; 5.0+'tan 192 kHz'e kadar | Tizen Native API — Audio Output |
| Codec seçimi | Yazılım/hardware codec ayrımı `player_set_audio_codec_type()`; **audio offload** donanımda decode (güç tasarrufu, PCM efekt kapalı) | Tizen Native API — Player |
| Sistem sesi | **Sound Manager** — ses seviyesi, stream politikası, cihaz bağlantı odaklı; yalnız asenkron (callback) | Tizen Native API — Sound Manager |
| Web yüzeyi | Samsung Smart TV **Web Device API** (`Tizen.TV.Multimedia`, `Tizen.TV.Player`/`ESPlayer`, `TVAudioControl`) | Samsung Developer |
| Cihaz bağımlılığı | Desteklenen format ve buffer boyutu cihaza bağlı (`audio_in_get_buffer_size()` sound server önerisi; TV/IoT farklı) | Tizen Docs — Raw Audio |

## §2 CoreMusic Karşılığı
- Vault hedefi **web tarayıcısı** üzerinden: brain.md UA eşlemesi Tizen → `4k-tv` cihaz sınıfı
  (AI: `brain.md:593-594`). Yani CoreMusic TV'de **Tizen web browser** üzerinden çalışır.
- Native Tizen uygulaması (Player/Audio Output API) vault'ta **kararlı değil** → UNKNOWN.
- `4k-tv` token katmanı boş (`tokens-tv.json` 0 byte — AGENTS §24.3) → tasarım verisi `⚠️ VERIFICATION REQUIRED`.

## §3 Durum
**PLANNED (web hedefi) / UNKNOWN (native)** — repo'da Tizen paketi/dosyası YOK.

## §4 Bilinmeyenler
- Native Tizen hedefi var mı → kanıt yok → `⚠️ VERIFICATION REQUIRED`.
- TV tarayıcısında Web AudioWorklet desteği → doğrulanmadı → UNKNOWN.
