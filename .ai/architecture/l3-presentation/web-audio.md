---
type: architecture
category: l3
title: "Web Audio API"
date: 2026-08-08
updated: 2026-09-08
status: active
version: 5.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Web Audio API

**Zorunlu Bağlantılar:** [[index]] · [[ADR-001-vanilla-js-itcss]] · [[ADR-025-professional-eq-system]]

---

## 1. Amaç

Web Audio API kullanımını ve ses oynatma mekanizmasını tanımlar.

**Faz 2d dürüstlük notu (2026-09-08):** Bu dosyadaki `AudioManager` sınıfı **hedef desendir**. Gerçek üretim oynatması HTML5 `<audio>` elementi üzerinden yürür (kanıt: html-shell-renderer §3 — `<audio controls id="audio" class="vdisplay">` shell'de sabit kalıcı oynatma elemanı). Web Audio API (`AudioContext`) entegrasyonu — özellikle EQ/analyser node'ları — **PLANNED**'tir.

---

## 2. Audio Context (Hedef Desen)

```javascript
class AudioManager {
    #context = null;
    #gainNode = null;
    #source = null;

    constructor() {
        this.#context = new (window.AudioContext || window.webkitAudioContext)();
        this.#gainNode = this.#context.createGain();
        this.#gainNode.connect(this.#context.destination);
    }

    async loadTrack(url) {
        const response = await fetch(url);
        const arrayBuffer = await response.arrayBuffer();
        const audioBuffer = await this.#context.decodeAudioData(arrayBuffer);
        return audioBuffer;
    }

    play(buffer) {
        this.#source = this.#context.createBufferSource();
        this.#source.buffer = buffer;
        this.#source.connect(this.#gainNode);
        this.#source.start();
    }

    setVolume(value) {
        this.#gainNode.gain.value = value;
    }

    stop() {
        if (this.#source) {
            this.#source.stop();
        }
    }
}
```

---

## 3. Gerçek Kod Durumu (Faz 0/2d — 2026-09-08)

| Öğe | Durum | Kanıt |
|-----|-------|-------|
| HTML5 `<audio>` oynatma | **IMPLEMENTED** | Shell `<audio id="audio" class="vdisplay">` (kalıcı — DOM patch'e dokunmaz) |
| `PlayerController.js` state machine | brain §18B dokümanı | STOPPED/PLAYING/PAUSED — dosya varlığı DOĞRULAMA GEREKLİ |
| `AudioContext` / AudioManager | **PLANNED** | js/ glob'unda audio context kullanımı görülmedi |
| 31-band EQ (web tarafı) | **PLANNED** | ADR-025; gerçek DSP hedefi C++ Audio Service (9741) |
| Footer player | **IMPLEMENTED (spec + partial kod)** | footer.php v8+ 9 icon + seek slider; ADR-018 vaporwave |
| Volume kontrol | `showVolume()` toggle IMPLEMENTED (DeviceManager) | brain §18B; AudioContext gain yok |

**Sonuç:** Oynatma hattı `<audio>` + PlayerController state machine dokümanı; Web Audio API katmanı (analyser/EQ/gain graph) PLANNED. İkisi birbirinin alternatifi değil — `<audio>` kalıcı oynatma çatıları, Web Audio API onun üzerine graph ekler (MediaElementSource ile).

---

## 4. Oynatma Hattı (Gerçek — Kanıtlı)

```
<footer.php> footer player (IMPLEMENTED)
  → <audio id="audio"> element (shell'de kalıcı — §1 kanıt)
     → PlayerController state machine (STOPPED/PLAYING/PAUSED — brain §18B)
        → src değişimi / play()/pause() çağrıları
  → seek slider (showFooterSeekSlider — phone hariç)
  → volume toggle (showVolume — phone/embedded hariç)
```

**Hedef uzatma (Web Audio API):**

```
<audio> element
  → MediaElementAudioSourceNode
     → EQ node'ları (31-band BiquadFilter zinciri — PLANNED)
        → analyser node (spectrum — coremusic_neva veri hedefi)
           → GainNode
              → destination
```

Bu uzatma yapılana dek DSP/EQ hedefleri C++ Audio Service (9741, PLANNED) tarafında yürütülür — iki yoldan hangisi önce devreye alınırsa web EQ önceliği azalır (tekrar önleme).

---

## 5. Features (Hedefler)

| Özellik | Değer | Kaynak | Durum |
|---------|-------|--------|-------|
| Format | FLAC 24/32-bit | ADR-026/003 | PLANNED (media pipeline) |
| Sample Rate | 48kHz standart | brain §19 | Hedef tanım |
| Channels | 2.0 → 8.1 | brain §9 | Hedef (C++ tarafı birincil) |
| Latency | <10ms (ASIO), <20ms (WASAPI) | brain §19 | **Native hedeflerdir** — web latency farklı kategoridir |
| EQ | 31-band parametric | ADR-025 | PLANNED (web), C++ hedef |
| Reverb Modları | Konser/Düğün/Oda/Stüdyo | brain §19 | PLANNED |

**Latency dürüstlük notu:** `<10ms` değerleri C++ ASIO/WASAPI hedefleridir (brain §19). Web Audio API ve `<audio>` elementi bu gecikme sınıfına ulaşamaz — dokümanlar arasında hedef karıştırılmamalıdır. Web tarafı latency hedefi ayrıca tanımlanmalıdır (DOĞRULAMA GEREKLİ).

---

## 6. Edge Cases

| Durum | Çözüm |
|-------|-------|
| **Context suspended** | User gesture ile resume (autoplay politikaları) |
| **Buffer underrun** | Fade-out + restart (brain §19 Edge: 50ms sessizlik) |
| **Format desteği** | Decode fallback (FLAC desteği tarayıcıya bağlı — MP3 fallback) |
| **Mobile restriction** | User interaction sonrası play |
| **Navigasyonda kesinti** | `<audio>` shell'de kalıcı — DOM patch'e dokunmaz (§3) |
| **Çoklu tab oynatma** | Tek instance ilkesi — ikinci tab oynatırsa ilki durmalı (uygulama kuralı PLANNED) |
| **Autoplay yasağı** | Welcome popup etkileşimi ilk gesture sağlar (RPi5 akışı) |

---

## 7. Autoplay ve Kullanıcı Etkileşimi

| Ortam | Kural |
|-------|-------|
| Tarayıcı genel | Autoplay ses engelli — ilk user gesture sonrası `play()` çağrılmalı |
| RPi5 embedded | Welcome popup (shouldRenderWelcomePopup — sadece embedded) etkileşimli açılış gesture sağlar |
| Muted autoplay | İzinli olabilir — ancak müzik platformu için anlamsız; kullanılmaz |

Kural: Oynatma akışı her zaman kullanıcı etkileşimiyle başlar — sessiz autoplay bağımlılık kurulmaz.

---

## 8. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[index]] | L3 ana dizin |
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS |
| [[ADR-018-footer-player-vaporwave]] | Footer player (IMPLEMENTED spec) |
| [[ADR-025-professional-eq-system]] | EQ system (31-band) |
| [[../06-audio/index]] | Audio service hattı (C++ 9741) |
| [[ADR-017-dsp-hardware-mode]] | DSP donanım modu |

---

## 9. Diagnostics (tekrarlanabilir)

```powershell
# 1. Shell'de kalıcı audio element
Select-String -LiteralPath "shared\src\PageRouter\HtmlShellRenderer.php" -Pattern "audio"

# 2. PlayerController varlığı (DOĞRULAMA GEREKLİ çözümü)
Get-ChildItem -LiteralPath "assets.coremusic.net\js" -Recurse -Filter "PlayerController.js" -ErrorAction SilentlyContinue

# 3. AudioContext kullanımı (beklenen: 0 → PLANNED doğru)
Get-ChildItem -LiteralPath "assets.coremusic.net\js" -Recurse -Include "*.js" | Select-String -Pattern "AudioContext" -ErrorAction SilentlyContinue

# 4. Footer player kod karşılığı
Select-String -LiteralPath "home.coremusic.net\footer.php" -Pattern "audio|seek" -ErrorAction SilentlyContinue

# 5. 31-band EQ dokümanı
Get-ChildItem -LiteralPath ".ai\electronic\dsp" -Filter "equalizer*" | Select-Object Name
```

---

## 10. Sık Sorulan Sorular

**S: Web Audio API kullanılıyor mu?**
C: Hayır (Faz 0 kanıtı: js/ içinde AudioContext geçmiyor — §9 komut 3). Oynatma `<audio>` elementiyle yapılıyor. API katmanı PLANNED'tir.

**S: Oynatma neden `<audio>` elementiyle — Web Audio API daha üstün değil mi?**
C: `<audio>` basit, kesintisiz (shell'de kalıcı) ve yeterlidir; EQ/analiz gerektiğinde MediaElementSource ile üstüne graph bağlanır. İki model rekabet değil katmanlı ilişkidir (§4).

**S: 8.1 surround web'de olur mu?**
C: Pratikte hayır — 8.1 hedefi C++ Audio Service + donanım (PCM3168A) hattıdır (brain §9). Web oynatma stereo/2.0 sınırlıdır. Karıştırılmaması için §5 tablo ayrımı yapıldı.

**S: `<10ms` latency web'e uygulanır mı?**
C: Uygulanmaz — o değerler ASIO/WASAPI native hedefleridir (§5 dürüstlük notu). Web latency hedefi ayrı tanımlanmalı; mevcut doküman iddiası yoktur.

**S: EQ slider'ları frontend'de nerede?**
C: PLANNED — 31-band EQ UI'si ADR-025 + coremusic_neva şemasına bağlı; C++ Audio Service REST (9741) üzerinden ayar uygular. Web Audio API EQ alternatifidir ama kararlaştırılmadı.

**S: Audio elementi neden shell'de gizli (vdisplay)?**
C: Navigasyonda müzik kesintisiz devam etsin diye — DOM patch container'ı değiştirir, audio'ya dokunmaz (html-shell §19 SSS paralel).

## 14. PlayerController State Machine (Hedef Desen)

brain §18B dokümanındaki state machine — dosya varlığı DOĞRULAMA GEREKLİ (Faz 2d glob görevi):

| Mevcut Durum | Olay | Sonraki Durum | Yan Etki |
|--------------|------|---------------|----------|
| STOPPED | play(track) | PLAYING | audio.src set + play() |
| PLAYING | pause() | PAUSED | — |
| PAUSED | play() | PLAYING | kaldığı yerden |
| PLAYING/PAUSED | stop() | STOPPED | src temizle |
| PLAYING | track bitti (ended) | STOPPED veya next | playlist sırası |
| PLAYING | hata (error event) | STOPPED | fallback format denemesi |

State event'leri EventBus üzerinden yayınlanır (UI güncellemeleri: footer seek, play icon).

---

## 15. Footer Player Bileşenleri (IMPLEMENTED spec)

MEMORY 2026-09-04 kaydıyla: footer.php v8.0.0 — 9 utility icon + seek slider:

| Bileşen | Görünürlük | Kaynak |
|---------|------------|--------|
| Seek slider | `!isPhone()` | DeviceManager.showFooterSeekSlider |
| 9 utility icon | `!isPhone()` | showUtilityIcons (repeat, shuffle, EQ, vb.) |
| Phone kompakt player | isPhone | footer.php Phone bloğu |
| Vaporwave stil | Tüm tier | ADR-018 (FROZEN) |

Tam davranış matrisi: footer.php dokümantasyonu (3-Zone × 4-Tier — MEMORY 2026-09-05).

---

## 16. Format Zinciri

```
Kaynak: FLAC 24/32-bit (download hattı — ADR-026, PLANNED)
  → tarayıcı FLAC desteği var → doğrudan oynat
  → destek yok → MP3 320kbps fallback (download hattı üretir)
  → decode başarısız → error event → PlayerController STOPPED + kullanıcı bildirimi
```

Web Audio API decodeAudioData da aynı fallback zincirini kullanır (hedef desen). Tarayıcı codec desteği CanUse genel bilgisi — ortam testi önerilir.

---

## 17. ADR-025 EQ — Web Karşılığı Senaryosu (PLANNED)

```
31-band parametric EQ hedefi iki yoldan biriyle:
  YOL A (C++ Audio Service — 9741 REST):
    UI slider → API → C++ DSP zinciri → donanım çıktı
    Avantaj: gerçek 8.1 + native latency; Dezavantaj: servis bağımlılığı
  YOL B (Web Audio API):
    MediaElementSource → 31 × BiquadFilterNode → gain → destination
    Avantaj: servis bağımsız; Dezavantaj: CPU, stereo sınırı
Karar: ADR-025 kapsamında değerlendirilecek — bu dosya yalnız B'yi tanımlar.
```

İki yol aynı anda aktif olamaz (çift DSP) — seçim kararı PLANNED; coremusic_neva şeması (EQ preset saklama) her iki yola ortaktır.

---

## 18. C++ Audio Service Köprüsü (PLANNED)

| Konu | Hedef |
|------|-------|
| Endpoint | `http://localhost:9741` REST (service-discovery §2) |
| İşler | play/pause/seek/volume/EQ preset/DSP parametreleri |
| Kimlik | API Key (subdomain-routing §4 tablosu) |
| JS köprüsü | PlayerController → fetch(9741) — CORS whitelist |
| Fallback | Servis down → HTML5 audio local playback (degraded mode — service-discovery §11) |

Bu köprü devreye alındığında web oynatma "hizmetli" rolüne düşer; yerel `<audio>` degraded modun çatısıdır.

---

## 19. Ek SSS

**S: `webkitAudioContext` fallback gerekli mi?**
C: Safari eski sürümler için örnek kodda var; hedef platform seti (Tier 1-4) modern tarayıcı — modern standard `window.AudioContext` yeterli. Kaldırma kararı ADR-001 ek kaydı.

**S: Volume slider nerede — AudioContext gain mi, audio.volume mu?**
C: Şu an `<audio>.volume` (HTML5 property) yeterli — Web Audio gain node PLANNED. gain, EQ graph devreye alınca zorunlu olur.

**S: Şarkı bittiğinde sonraki parça?**
C: PlayerController `ended` olayı → playlist sırası (WidgetManager/playlist modülü). Otomatik devam politikası UI ayarıdır.

**S: Hata logları nereye?**
C: PSR-3/JS log seviyesi (RouterConfig.logLevel: 'info') — js-module-architecture kapsamı.

**S: FLAC şarkı metadata'sı (kapak) nereden?**
C: coremusic_musics DB + media pipeline (PLANNED) — bu dosya kapsamı dışı (media.coremusic.net kartı).

---

## 11. İzlenebilirlik Tablosu

| İddia | Kaynak | Doğrulama |
|-------|--------|-----------|
| `<audio>` element shell'de | HtmlShellRenderer §3 | Kod okuma ✅ |
| AudioManager hedef desen | Bu dosya §2 | PLANNED etiketi |
| PlayerController state machine | brain §18B | Doküman — dosya DOĞRULAMA GEREKLİ |
| AudioContext kullanılmıyor | js/ grep | Faz 2d taraması ✅ |
| 31-band EQ ADR-025 | decisions/accepted | ✅ |
| native latency hedefleri | brain §19 | ✅ (web'e taşınmadı) |
| footer player 9 icon + seek | MEMORY 2026-09-04 | ✅ |

---

## 12. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 5.0.0 |
| **Bölüm Sayısı** | 12 |
| **Durum Etiketi** | `<audio>` IMPLEMENTED · Web Audio API PLANNED · EQ C++ hedefli |
| **ADR Uyumlu** | ✅ 001, 018, 025 (+017/026 çapraz) |
| **Test Senaryosu** | — (öneri: §10 edge listesi test iskeletine dönüşebilir) |
| **Zero Hallucination** | ✅ (latency karışımı açık notlandı) |

---

## 13. Revizyon Geçmişi

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 4.0.0 | 2026-08-08 | İlk doküman |
| 5.0.0 | 2026-09-08 | Faz 2d: §1/§3 dürüst durum ayrımı (`<audio>` vs AudioContext); §4 gerçek hattı + hedef graph; §5 latency karışımı notu; §7 autoplay; §9 diagnostics; §10 SSS; §11 izlenebilirlik |

---

## 22. Audio Element Event Referansı

| Event | Zaman | PlayerController Kullanımı |
|-------|-------|---------------------------|
| `play` | oynatma başladı | state → PLAYING |
| `pause` | duraklatıldı | state → PAUSED |
| `ended` | parça bitti | state → STOPPED veya sonraki parça |
| `timeupdate` | ~250ms aralıkla | seek slider pozisyonu + süre göstergesi |
| `loadedmetadata` | metadata hazır | toplam süre set |
| `durationchange` | süre değişti | UI güncelle |
| `volumechange` | ses değişti | volume ikon durumu (muted/resim) |
| `error` | yükleme/decode hatası | fallback format + STOPPED |
| `waiting` / `stalled` | tampon | loading göstergesi |
| `canplay` | oynatmaya hazır | aria-busy=false |

Tüm handler'lar EventBus'a yayınlar — UI bileşenleri doğrudan audio'ya bağlanmaz (tek kaynak ilkesi).

---

## 23. Seek Slider Davranışı

| Aşama | Davranış |
|-------|----------|
| Görselleştirme | `timeupdate` → slider.value = currentTime/duration (Math.floor — jittersız) |
| Kullanıcı sürükleme | `input` event → görsel önizleme (audio.seeking sırasında çalmaya dokunmaz) |
| Bırakma | `change` event → `audio.currentTime = oranı × duration` |
| seeking durumu | `seeking` event → loading göstergesi; `seeked` → kaldırma |
| Phone'da | seek slider gizli (showFooterSeekSlider=false) — sadece ileri/geri ikonları |

**Kural:** Seek sonrası state değişmez (PAUSED kalır PAUSED) — yalnız pozisyon değişir.

---

## 24. Volume Davranışı

| Öğe | Değer |
|-----|-------|
| Aralık | 0.0 – 1.0 (audio.volume) |
| UI | volume ikonu + hover slider (footer) |
| Muted | `audio.muted` — volumechange event ikon günceller |
| Görünürlük | `showVolume()` — phone/embedded gizli (DeviceManager) |
| Kalıcılık | localStorage volume tercihi — DİKKAT: auth için localStorage yasak (CLAUDE §21); volume tercih storage kararları PLANNED (session/cookie) |

Web Audio gain node devreye girince volume audio.volume yerine gain.value'ya taşınır (çift kontrol çakışmasını önlemek için tek nokta).

---

## 25. Preload Politikası

| Değer | Davranış | Tercih |
|-------|----------|--------|
| `none` | Hiçbir şey yüklemez | Önerilen başlangıç — trafik tasarrufu (Offline-First uyum) |
| `metadata` | Süre/süreler | Alternatif — playlist süre gösterimi için |
| `auto` | Tüm dosya | Trafik savurgan — kullanılmaz |

Ruler: RPi5 embedded (bandwidth kısıtlı) → `none` + ilk play'de yükleme. Değer shell/JS kararı — PlayerController sorumluluğu.

---

## 26. Parça Değişimi Akışı (Uçtan Uca)

```
Kullanıcı Track Row (C13) çal'a tıklar
  → CardManager event delegation → EventBus 'track:select'
     → PlayerController: STOPPED → src set → play()
        → state PLAYING → EventBus 'player:state'
           → footer ikon güncelle (pause görünür)
           → Media Card/C13 aktif satır stili (C13 playing variant)
Track bitti (ended)
  → STOPPED → playlist sırası (WidgetManager) → sonraki track select
Navigasyon (SPA)
  → audio element shell'de kalır → oynatma kesintisiz (§3)
```

---

## 27. MediaError Kodları

| code | Anlam | PlayerController Tepkisi |
|------|-------|--------------------------|
| 1 MEDIA_ERR_ABORTED | yükleme iptal | sessiz toparla |
| 2 MEDIA_ERR_NETWORK | ağ hatası | retry (offline-first queue) |
| 3 MEDIA_ERR_DECODE | decode hatası | fallback format dene |
| 4 MEDIA_ERR_SRC_NOT_SUPPORTED | codec yok | MP3 fallback |

Hata log'u RouterConfig.logLevel'e göre; kullanıcıya yerelleştirilmiş mesaj (i18n PLANNED).

---

## 28. Senaryo — Hatalı Format Fallback

```
Girdi: track.flac — tarayıcı decode edemiyor (eski tarayıcı)
  audio.error → code 4 (SRC_NOT_SUPPORTED)
  PlayerController:
    1. STOPPED + EventBus 'player:error' {code:4}
    2. Alternatif kaynak var mı? (track.mp3 — download hattı üretir)
       ├─ var → src değiştir → tekrar play() → PLAYING
       └─ yok → kullanıcı bildirimi (i18n mesaj) → STOPPED kalır
  log: PSR-3/JS warn seviyesi
```

---

## 29. Ek SSS

**S: `timeupdate` neden 250ms?**
C: Tarayıcı standart aralığı ~250ms'dir (spesifikasyon garanti etmez ama pratik bu). Frame hassasiyetli görselleştirme için requestAnimationFrame alternatifi PLANNED.

**S: Slider sürüklerken ses takılıyor mu?**
C: Hayır — input sırasında currentTime set edilmez (yalnız görsel); change'de tek atım set edilir. Sürekli seek decode maliyeti üretir.

**S: Telefonlarda arka plan oynatma?**
C: PWA/Web Audio MediaSession API PLANNED (kilit ekranı metadata + kontrol). Mevcut: tarayıcı native davranışı.

**S: İki tabda parça çalarsa?**
C: Şu an çift oynatma mümkün (§6 edge) — tek instance kilidi (BroadcastChannel/storage event) PLANNED uygulama kuralı.

**S: `stalled` ile `waiting` farkı?**
C: `stalled` ağ verisi gelmiyor; `waiting` sonraki frame bekleniyor. İkisi de loading göstergesi tetikler; çözüm aynı — tampon.

**S: Volume tercihi nerede saklanır?**
C: PLANNED — localStorage auth-dışı kullanım için serbest ama tutarlılık için cookie/session kararı alınmalı (§24 not).

---

## 30. Diagnostics Ek

```powershell
# 1. audio element event bağları (JS tarafı)
Get-ChildItem -LiteralPath "assets.coremusic.net\js" -Recurse -Include "*.js" |
  Select-String -Pattern "timeupdate|seeked|ended" -ErrorAction SilentlyContinue

# 2. footer seek slider kodu
Select-String -LiteralPath "home.coremusic.net\footer.php" -Pattern "seek|slider" -ErrorAction SilentlyContinue

# 3. MediaSession (beklenen: 0 → PLANNED)
Get-ChildItem -LiteralPath "assets.coremusic.net\js" -Recurse -Include "*.js" |
  Select-String -Pattern "mediaSession" -ErrorAction SilentlyContinue
```

---

## 31. İzlenebilirlik Ek

| İddia | Kaynak | Doğrulama |
|-------|--------|-----------|
| Event tablosu | HTML5 media standardı | Genel standard — ortam testi |
| seek gizli phone | DeviceManager.showFooterSeekSlider | brain §18B ✅ |
| 9 icon | MEMORY 2026-09-04 | ✅ |
| MediaSession yok | js/ grep bekliyor | §30 komut 3 |
| error kodları | HTML5 MediaError standardı | Genel standard |
| localStorage auth yasağı | CLAUDE.md §21 | ✅ |

---

## 32. Kalite Raporu (Final)

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 5.1.0 |
| **Bölüm Sayısı** | 32 |
| **Event Referansı** | 10 event (§22) |
| **Senaryo** | 2 uçtan uca (§26/§28) |
| **Risk/Etiket** | PLANNED ayrımları net (§3/§5) |
| **Zero Hallucination** | ✅ — latency/native ayrımı, MediaSession bekliyor |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-08
**Mode:** Red Team · Human Mode · Truth Mode
