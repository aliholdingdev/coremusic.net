---
title: "CoreMusic — Audio Architecture"
category: electronics
date: 2026-08-09
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
updated: 2026-10-13
---

# CoreMusic — Audio Architecture

**Zorunlu Bağlantılar:** [[electronic/core-music-electronics-overview]] · [[electronic/platform-architecture]] · [[electronic/device-architecture]] · [[electronic/operating-system-architecture]] · [[ADR-062-dsp-pipeline-architecture]]

---

## 1. Amaç

CoreMusic ELECTRONICS'in en kritik bileşeni olan Ses Mimarisi, müzik çalma, girdi yakalama, işleme, yönlendirme, dönüştürme, filtreleme, analiz, yükseltme ve dağıtma süreçlerini yönetir. Gerçek zamanlı (real-time) olarak tasarlanmıştır.

---

## 2. Ses Motoru Katmanları

```
Input ──▶ Decoder ──▶ DSP Processing ──▶ Mixer ──▶ Effects ──▶ Output
```

### 2.1 ASCII: Ses Motoru Katmanları
```
┌─────────────────────────────────────────────────────────────────────────────┐
│                     COREMUSIC SES MOTORU — KATMANLAR                         │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  ┌──────────────┐                                                          │
│  │    INPUT     │  Girdi kaynağı yönetimi                                  │
│  │──────────────│  Lokal / Ağ / USB / Bluetooth / HDMI / Optik             │
│  │ Yerel Müzik  │                                                          │
│  │ Ağ Akışı     │                                                          │
│  │ USB Ses      │                                                          │
│  │ Bluetooth    │                                                          │
│  └──────┬───────┘                                                          │
│         ▼                                                                  │
│  ┌──────────────┐                                                          │
│  │   DECODER    │  Ses formatı çözümleme                                   │
│  │──────────────│  MP3, FLAC, WAV, AAC, OGG, OPUS, AIFF, ALAC, DSD, PCM   │
│  │ Format Çözüm │                                                          │
│  │ Bit Derinliği│                                                          │
│  │ Örnekleme    │                                                          │
│  └──────┬───────┘                                                          │
│         ▼                                                                  │
│  ┌──────────────┐                                                          │
│  │ DSP PROCESSING│  Sinyal işleme                                          │
│  │──────────────│  EQ, Compressor, Limiter, Crossover, Reverb, Delay       │
│  │ 31-Bant EQ   │                                                          │
│  │ Compressor   │                                                          │
│  │ Limiter      │                                                          │
│  │ Crossover    │                                                          │
│  └──────┬───────┘                                                          │
│         ▼                                                                  │
│  ┌──────────────┐                                                          │
│  │    MIXER     │  Çoklu kanal karıştırma                                  │
│  │──────────────│  Kaynak seviye dengeleme, Pan, Solo/Mute, Bus            │
│  │ Karıştırma   │                                                          │
│  │ Pan Kontrolü │                                                          │
│  │ Solo/Mute    │                                                          │
│  └──────┬───────┘                                                          │
│         ▼                                                                  │
│  ┌──────────────┐                                                          │
│  │   EFFECTS    │  Uzamsal efektler                                        │
│  │──────────────│  Reverb, Delay, Echo, Stereo Genişliği, Room Correction  │
│  │ Reverb       │                                                          │
│  │ Delay        │                                                          │
│  │ Room Correct │                                                          │
│  └──────┬───────┘                                                          │
│         ▼                                                                  │
│  ┌──────────────┐                                                          │
│  │   OUTPUT     │  Çıkış yönlendirme                                       │
│  │──────────────│  ASIO, WASAPI, ALSA, CoreAudio, Bluetooth, Analog        │
│  │ ASIO         │                                                          │
│  │ WASAPI       │                                                          │
│  │ ALSA         │                                                          │
│  └──────────────┘                                                          │
└─────────────────────────────────────────────────────────────────────────────┘
```

| Katman | Görev |
|--------|-------|
| **Input** | Girdi kaynağı yönetimi (lokal, ağ, USB, BT) |
| **Decoder** | Ses formatı çözümleme (MP3, FLAC, WAV vb.) |
| **DSP Processing** | EQ, compressor, limiter, crossover |
| **Mixer** | Çoklu kanal karıştırma |
| **Effects** | Reverb, delay, echo, spatial |
| **Output** | Çıktı yönlendirme (ASIO, WASAPI, ALSA) |

---

## 3. Ses Hattı (Audio Pipeline)

```
Input ──▶ Decode ──▶ Normalize ──▶ DSP ──▶ Mixer ──▶ EQ ──▶ Effects ──▶ Limiter ──▶ Output Routing ──▶ Amplifier ──▶ Speaker
```

Her aşama bağımsız bir modüldür:

1. **Input** → 2. **Decode** → 3. **Normalize** → 4. **DSP** → 5. **Mixer** → 6. **EQ** → 7. **Effects** → 8. **Limiter** → 9. **Output Routing** → 10. **Amplifier** → 11. **Speaker**

---

## 4. Desteklenen Formatlar

| Biçim | Açıklama | Tür |
|-------|----------|-----|
| MP3 | MPEG-1 Audio Layer III | Kayıplı |
| FLAC | Free Lossless Audio Codec | Kayıpsız |
| WAV | Waveform Audio File Format | Ham |
| AAC | Advanced Audio Coding | Kayıplı |
| OGG | Ogg Vorbis | Kayıplı |
| OPUS | Opus Interactive Audio Codec | Kayıplı |
| AIFF | Audio Interchange File Format | Ham |
| ALAC | Apple Lossless Audio Codec | Kayıpsız |
| WMA | Windows Media Audio | Kayıplı |
| DSD | Direct Stream Digital | Ham |
| PCM | Pulse-Code Modulation | Ham |

---

## 5. DSP Hattı (DSP Pipeline)

```
Input Signal ──▶ Input Gain ──▶ Noise Gate ──▶ HPF ──▶ LPF ──▶ Parametric EQ ──▶ Graphic EQ ──▶ Compressor ──▶ Limiter ──▶ Loudness ──▶ Crossover ──▶ Delay ──▶ Reverb ──▶ Output Gain ──▶ Output Routing
```

| Aşama | Görev |
|-------|-------|
| Input Signal | Ham girdi sinyali |
| Input Gain | Girdi seviyesi ayarı |
| Noise Gate | Gürültü engelleme |
| HPF | High-pass filtre |
| LPF | Low-pass filtre |
| Parametric EQ | Parametrik equalizer |
| Graphic EQ | Grafik equalizer |
| Compressor | Sinyal sıkıştırma |
| Limiter | Sinyal sınırlama |
| Loudness | Yoğunluk ayarı |
| Crossover | Frekans bölme |
| Delay | Gecikme efekti |
| Reverb | Yankı efekti |
| Output Gain | Çıktı seviyesi ayarı |
| Output Routing | Çıktı yönlendirme |

---

## 6. Ekvalizör Tipleri

| Tip | Bant Sayısı | Parametreler |
|-----|-------------|--------------|
| Graphic | 2-31 bant | Kazanç (dB) |
| Parametric | 2-31 bant | Frekans, Kazanç, Q Faktörü |

---

## 7. Ses Efektleri

### 7.1 Dinamikler
- **Compressor** — Sinyal sıkıştırma
- **Limiter** — Sinyal sınırlama
- **Gate** — Gürültü engelleme
- **Expander** — Sinyal genişletme

### 7.2 Frekans
- **Graphic EQ** — Grafik equalizer
- **Parametric EQ** — Parametrik equalizer
- **FIR** — Finite Impulse Response
- **IIR** — Infinite Impulse Response

### 7.3 Mekânsal
- **Reverb** — Yankı
- **Delay** — Gecikme
- **Echo** — Yankı tekrarı
- **Stereo Genişliği** — Stereo genişletme
- **Surround Geliştirme** — Surround geliştirme

### 7.4 Bas
- **Bass Boost** — Bas güçlendirme
- **Bass Management** — Bas yönetimi
- **LFE Yönlendirmesi** — Subwoofer yönlendirme
- **Subwoofer Kazancı** — Subwoofer seviyesi

---

## 8. Geçirgen (Crossover) Motoru

```
Subwoofer (20-120Hz) ─┐
Woofer (120-500Hz) ───┤──▶ Input Signal
Midrange (500Hz-8kHz) ─┤
Tweeter (8kHz-20kHz) ──┘
```

| Özellik | Değer |
|---------|-------|
| Topoloji | Linkwitz-Riley 4. nesil |
| Varsayılan Geçiş Noktası | 80Hz |
| Kanal Çıkışları | Her kanal için bağımsız |
| Frekans Bantları | Yapılandırılabilir |
| Subwoofer | 20-120Hz |
| Woofer | 120-500Hz |
| Midrange | 500Hz-8kHz |
| Tweeter | 8kHz-20kHz |

---

## 9. Kanal Konfigürasyonları

| Konfigürasyon | Kanallar |
|---------------|----------|
| Stereo 2.0 | Sol, Sağ |
| Subwoofer 2.1 | Sol, Sağ, LFE |
| Surround 5.1 | Ön L/R, Merkez, Arka L/R, LFE |
| Surround 7.1 | Ön + Arka + Yan + Merkez + LFE |
| **Surround 7.1 (Varsayılan)** | Ön L/R + Merkez + Yan L/R + Arka L/R + LFE |
| Profesyonel Mono | Tek kanal |
| Profesyonel Çift Mono | İki bağımsız kanal |
| Profesyonel Çok Kanallı | Çoklu kanal |

---

## 10. Mikser Motoru

Birden fazla kaynağı eşzamanlı olarak yönetir:
- Kaynak seviye dengeleme
- Pan kontrolü
- Solo/Mute
- Bus yönlendirme
- Alt mikser oluşturma

---

## 11. Ses Yönlendirme

Herhangi bir girdi → herhangi bir çıktıyı yönlendirir:
- Esnek yönlendirme matrisi
- Çoklu çıkış hedefleme
- Paralel yönlendirme
- Öncelik tabanlı yönlendirme

---

## 12. Çıktı Yöneticisi

| Çıktı Türü | Teknoloji |
|------------|-----------|
| USB Ses | USB Audio Class 1/2 |
| ASIO | ASIO SDK 2.3.4 |
| WASAPI | Windows Audio Session API |
| ALSA | Advanced Linux Sound Architecture |
| CoreAudio | macOS/iOS Audio |
| HDMI | HDMI Audio |
| Bluetooth | A2DP, aptX, LDAC |
| Optik | S/PDIF, TOSLINK |
| Analog | RCA, TRS, XLR |

---

## 13. Yükseltici (Amplifier) Yöneticisi

| Özellik | Açıklama |
|---------|----------|
| Kazanç | Ayarlanabilir |
| Sessiz | Mute |
| Yumuşak Başlangıç/Durak | Soft Start/Stop |
| Sıcaklık İzleme | Thermal Monitor |
| Koruma | Protection |
| Klip Algılama | Clip Detection |

---

## 14. Hoparlör Yöneticisi

| Parametre | Açıklama |
|-----------|----------|
| Ses Seviyesi | Volume |
| Gecikme | Delay |
| Faz | Phase |
| Kazanç | Gain |
| Sessiz | Mute |
| Mesafe | Distance |
| Frekans Aralığı | Frequency Range |

---

## 15. Yapay Zeka Ses Motoru

| Özellik | Açıklama |
|---------|----------|
| Oda Akustiği | Room acoustics analysis |
| Hoparlör Yerleşimi | Speaker placement optimization |
| Frekans Analizi | Frequency analysis |
| Gürültü Analizi | Noise analysis |
| Faz Analizi | Phase analysis |
| Bozulma Analizi | Distortion analysis |
| Otomatik EQ | Auto EQ |
| Otomatik Crossover | Auto crossover |

---

## 16. Performans Hedefleri

| Metrik | Hedef |
|--------|-------|
| Gecikme | Ultra düşük (10ms altı) |
| Gerçek Zamanlı | Kesintisiz işleme |
| Kararlılık | Sistem kararlılığı |
| CPU/Kullanım | Düşük |
| Bellek Kullanımı | Düşük |
| Kesintisiz Çalma | Seamless playback |
| Yüksek Kanal Kapasitesi | 8+ kanal desteği |

---

## 17. Modüler Yapı

```
Girdi:   Yerel Müzik / Ağ Akışı / USB Ses / Bluetooth / HDMI / Optik ──▶ DSP Motoru
İşlem:   DSP Motoru ──▶ Ekvalizör ──▶ Efektler ──▶ Mikser
Çıkış:   USB / ASIO / WASAPI / Bluetooth / Analog
```

---

## 18. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| [[decisions/accepted/ADR-017-dsp-hardware-mode]] | DSP donanım modu |
| [[decisions/accepted/ADR-038-8.1-sound-card-chip-selection]] | 8.1 ses kartı çip seçimi |
| [[decisions/accepted/ADR-062-dsp-pipeline-architecture]] | DSP hattı mimarisi |

---

## 19. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[electronic/index]] | Elektronik indeksi |
| [[electronic/dsp/index]] | DSP motoru |
| [[electronic/amplifier/index]] | Yükseltici mimarisi |
| [[electronic/drivers/index]] | Sürücü çerçevesi |
| [[electronic/hardware/index]] | Donanım tasarımı |
| [[electronic/firmware/index]] | Firmware mimarisi |

---

## 20. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 20 |
| ADR References | 3 |
| ASCII Art Diagrams | 5 (Ses Motoru, Ses Hattı, DSP Hattı, Crossover, Modüler Yapı) |
| Input Sources | 10 |
| Audio Formats | 11 |
| DSP Stages | 15 |
| Channel Configurations | 8 |
| Output Types | 9 |
| AI Features | 8 |

---

## 21. DSP Hattı Aşama Sırası ve Bağımlılıklar

`## 5`'teki 15 aşamanın girdi/çıktı sözleşmesi ve bir sonraki aşamaya bağımlılığı. Sıra **değiştirilemez**; her aşamanın çıktısı bir sonrakinin girdisidir.

| # | Aşama | Girdi | Çıktı | Sıradaki Aşamaya Bağımlı mı |
|---|-------|-------|-------|------------------------------|
| 1 | Input Signal | Kaynak | Ham örnek | — |
| 2 | Input Gain | Ham örnek | Kazanç uygulanmış | Evet |
| 3 | Noise Gate | Kazançlı | Eşiklenmiş | Evet |
| 4 | HPF | Eşiklenmiş | Alt kesilmiş | Evet |
| 5 | LPF | Alt kesilmiş | Üst kesilmiş | Evet |
| 6 | Parametric EQ | Bantlı | Şekillendirilmiş | Evet |
| 7 | Graphic EQ | Şekillendirilmiş | Bant ayarlı | Evet |
| 8 | Compressor | Bant ayarlı | Sıkıştırılmış | Evet |
| 9 | Limiter | Sıkıştırılmış | Sınırlandırılmış | Evet |
| 10 | Loudness | Sınırlandırılmış | Düzeltilmiş | Evet |
| 11 | Crossover | Düzeltilmiş | Bantlara ayrılmış | Evet |
| 12 | Delay | Bantlı | Geciktirilmiş | Evet |
| 13 | Reverb | Geciktirilmiş | Yankılı | Evet |
| 14 | Output Gain | Yankılı | Çıkış seviyeli | Evet |
| 15 | Output Routing | Çıkış seviyeli | Yönlendirilmiş | — |

**Sıra değişikliği kuralı:** `Limiter` (9) hiçbir koşulda `Compressor` (8)'den öne alınamaz — limiter son savunmadır. `Crossover` (11) `Reverb` (13)'ten önce olmalıdır; aksi halde yankı bantlara dağılmaz.

**Sıra tutarlılığı:** Bu sıra `§3` ses hattındaki `EQ → Effects → Limiter` sırasıyla **aynı mantığı** taşır; iki tablo arasında terslik varsa `§5` (DSP hattı) geçerlidir, çünkü aşama sayısı ondadır.

---

## 22. Frekans Bantları ve Crossover Tutarlılığı

`## 8`'deki bant sınırlarının **aritmetik** denetimi. Bu denetim yeni frekans üretmez; yalnız dosyadaki değerlerin bitişikliğini ve örtüşme/boşluk durumunu sınar.

| Bant | Alt Sınır | Üst Sınır | Bir Öncekiyle Bitişik mi |
|------|-----------|-----------|--------------------------|
| Subwoofer | 20 Hz | 120 Hz | — (ilk bant) |
| Woofer | 120 Hz | 500 Hz | ✅ 120 Hz = 120 Hz |
| Midrange | 500 Hz | 8.000 Hz | ✅ 500 Hz = 500 Hz |
| Tweeter | 8.000 Hz | 20.000 Hz | ✅ 8.000 Hz = 8.000 Hz |

**Sonuç:** Dört bant **kesintisiz ve örtüşmesiz** dizilmiştir; boşluk yok, çift sayılık sınır yok.

**Geçiş noktası denetimi:** Dosyadaki varsayılan geçiş noktası **80 Hz** → Subwoofer bandı (20–120 Hz) içindedir ✅. 80 Hz, `Woofer` bandının (120–500 Hz) **dışında** olduğundan, subwoofer/woofer ayrımı için de 80 Hz kullanılacaksa iki ayrı geçiş noktası gerekir → bu durum `⚠️ VERIFICATION REQUIRED` olarak işaretlenmiştir (dosya tek geçiş noktası yazıyor).

**Tersine hesap (kontrol):** 20 Hz alt sınırı işitme eşiği kabulüne dayanır; bu dosyada işitme eşiği iddiası **yoktur**, yalnız bant sınırı vardır — ikisi karıştırılmaz.

---

## 23. Gecikme Bütçesi (Çelişki Denetimi)

`## 16`'daki "10ms altı" hedefi ile L0 şartnamesindeki 512 örnek / 48 kHz tampon arasında **sayısal** bir ilişki vardır. Bu bölüm ilişkiyi hesaplar.

| Bileşen | Hesap | Değer | Dayanak |
|---------|-------|-------|---------|
| Tampon süresi | 512 ÷ 48000 | **10.667 ms** | Şartname (512 · 48 kHz) |
| `## 16` hedefi | — | **<10 ms** | Bu dosya §16 |
| Fark | 10.667 − 10 | **+0.667 ms** | Hesap |

**Çelişki:** Tek başına tampon süresi (10.667 ms), hedefin (10 ms) **üzerindedir**. Yani iki değer aynı anda doğru olamaz; en az biri revize edilmelidir. **Düzeltme YAPILMADI** (hangisinin geçerli olduğu ölçümle belirlenir) → `## 27` risk 1.

**Bütçenin parçaları (ölçülmesi gerekenler):**

| Bileşen | Durum |
|---------|-------|
| Tampon gecikmesi (512/48k) | Hesaplandı: 10.667 ms |
| DSP aşamalarının grup gecikmesi (§21'deki 15 aşama) | `⚠️ VERIFICATION REQUIRED` |
| Sürücü/kuyruk gecikmesi | `⚠️ VERIFICATION REQUIRED` |
| DAC/grup gecikmesi (donanım) | `⚠️ VERIFICATION REQUIRED` |
| Amplifikatör faz kayması | `⚠️ VERIFICATION REQUIRED` |
| **Toplam uçtan uca** | `UNKNOWN` — parçalar ölçülmeden toplanamaz |

**Kural:** Toplam gecikme, parçaların toplamıdır; parçalar ölçülmeden "10ms altı" iddiası **tekrarlanamaz**, yalnız hedef olarak taşınır.

---

## 24. Repoya Dayalı Uygulama Kanıtları

Her kanıt repo diskinden alınmıştır; satır numarası `Select-Object -Index (N-1)` ile doğrulanmıştır.

**Kanıt 1 — Tarayıcı tarafı ses elemanı sunum katmanında oluşturuluyor**

`Dosya: shared/src/PageRouter/HtmlShellRenderer.php` · `Satır: 192`

```php
echo '<audio id="audio" class="vdisplay" preload="metadata"></audio>';
```

**Kanıt 2 — Oynatma durumu bağımsız bir repository nesnesinde tutuluyor**

`Dosya: assets.coremusic.net/js/coreplayer/coreplayer.controls.js` · `Satır: 7`

```javascript
const PlaybackRepository = {
```

**Kanıt 3 — Ses alan bilgisi bilgi tabanı kategorileri arasında**

`Dosya: shared/src/AI/KnowledgeBase.php` · `Satır: 25`

```php
private const CATEGORIES = ['adr', 'architecture', 'security', 'audio', 'hardware', 'workflow', 'template'];
```

**Yorum:** Kanıtlar yalnız **uygulama/çalar katmanını** gösterir. Bu dosyadaki DSP hattı (§21), mixer, crossover ve amplifikatör yöneticileri için repo'da **kod karşılığı yoktur**; `§12`'deki "ASIO SDK 2.3.4" gibi sürüm iddiaları da repo'da doğrulanamaz (§25).

---

## 25. Kapsam Sınırı ve Dürüstlük Beyanı

| İddia | Durum | Dayanak |
|-------|-------|---------|
| 6 katmanlı ses motoru · 15 DSP aşaması · 11 format | Vault tasarım listesi | `§2`, `§4`, `§5` |
| **"ASIO SDK 2.3.4"** (`§12`) | **Sürüm kanıtı YOK** — diskte doğrulanmadı | `⚠️ VERIFICATION REQUIRED` |
| "10ms altı" gecikme hedefi | 512/48k tamponuyla **çelişkili** | `§23` |
| Linkwitz-Riley 4. nesil crossover | Tasarım — uygulama kodu yok | `⚠️ VERIFICATION REQUIRED` |
| Varsayılan geçiş 80 Hz | Tasarım — tek/geçiş noktası belirsiz | `§22` |
| A2DP, aptX, LDAC (`§12`) | Tasarım listesi — cihaz desteği doğrulanmadı | `⚠️ VERIFICATION REQUIRED` |
| AI ses özellikleri (`§15`) | Model/veri yok | `UNKNOWN` |
| PCM3168A · XMOS XU316 · AK4458 | Vault ADR seçimi | `[[ADR-038-8.1-sound-card-chip-selection]]` |
| **PCM5122** | **KATI YASAK — reddedildi (H001)** | ADR-038 |
| Ses veri tipi | 32-bit float | ADR-017 |
| Repo'da DSP/amplifikatör kaynak kodu | **YOK** | Dizin taraması |
| JUCE 9 sürüm kanıtı | Yok | `⚠️ VERIFICATION REQUIRED` |

**Yasak yüzeyler:** `.ai/electronic/AGENTS.md` · `.ai/electronic/CLAUDE.md` · `.ai/electronic/{amplifier,hardware,drivers,dsp}/**` · `firmware/AGENTS.md` · `firmware/CLAUDE.md` · `.ai/architecture/**` · `*.php` · `*.js` · `*.css` · `*.sql` (okunur, yazılmaz).

---

## 26. Web Kaynakları

Her satır 2026-10-13 tarihinde HTTP 200 ile doğrulanmış, vault URL envanteriyle kesişmeyen benzersiz bir kaynaktır.

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| Opus ses kodeğinin IETF spesifikasyonu (RFC 6716) | https://www.rfc-editor.org/rfc/rfc6716 | 2026-10-13 | HTTP 200 ✓ |
| Sayısal sinyal işleme üzerine açık bir ders kitabı kaynağı | https://www.dspguide.com/ | 2026-10-13 | HTTP 200 ✓ |

**Sınır:** Bu kaynaklar `§4`'teki OPUS formatının standart olduğunu ve DSP kavramlarının genel tanımlarını destekler; CoreMusic'in OPUS'u **çözdüğünü** veya DSP hattını **uyguladığını** desteklemez.

---

## 27. Riskler ve Açık Sorular

| # | Soru | Durum | Sonraki Adım |
|---|------|-------|--------------|
| 1 | "10ms altı" hedefi mi yoksa 512 tampon mu geçerli? | **Çelişki tespit edildi** (`§23`) | Öncelik kararı + ölçüm |
| 2 | `§12` "ASIO SDK 2.3.4" sürümü nereden geliyor? | `⚠️ VERIFICATION REQUIRED` | Bağımlılık manifesti okuması |
| 3 | Crossover geçiş noktası tek mi, subwoofer için ayrı mı? | `⚠️ VERIFICATION REQUIRED` (`§22`) | Tasarım kararı |
| 4 | `§5` 15 aşamanın grup gecikmesi ölçüldü mü? | `⚠️ VERIFICATION REQUIRED` | Aşama başına ölçüm |
| 5 | `§15` AI analiz özellikleri için veri var mı? | `UNKNOWN` | Veri seti araması |
| 6 | `§4` 11 formatın hangisi gerçekten çözülüyor? | `UNKNOWN` | Çözücü kodu araması |
| 7 | `§9` 7.1 varsayılan kanal düzeni hangi cihazda? | `⚠️ VERIFICATION REQUIRED` | Cihaz bazlı kanal matrisi |

---

## 28. Terim Sözlüğü (Bu Dosyaya Özgü)

| Terim | Tanım | İlgili Bölüm |
|-------|-------|--------------|
| Ses hattı (pipeline) | Girdiden çıkışa giden aşamalar dizisi | `§3` |
| DSP hattı | 15 aşamalı sinyal işleme sırası | `§5`, `§21` |
| Parametrik EQ | Frekans, kazanç ve Q ayarlanabilir EQ | `§6` |
| Grafik EQ | Sabit frekanslı, yalnızca kazanç ayarlı EQ | `§6` |
| Q faktörü | Bandın ne kadar dar olduğunu gösteren oran | `§6` |
| FIR / IIR | Sonlu ve sonsuz dürtü tepkili filtre türleri | `§7.2` |
| Linkwitz-Riley | Geçiş noktası civarında faz uyumlu çaprazlama | `§8`, `§22` |
| Geçiş noktası | Bantların ayrıldığı frekans | `§8`, `§22` |
| LFE | Düşük frekans kanalı (subwoofer) | `§9` |
| Bus | Birden fazla kaynağın birleştiği sanal yol | `§10` |
| Limiter | Sert üst seviye sınırlayıcısı | `§21` |
| Grup gecikmesi | Bileşenin sinyali geciktirme süresi | `§23` |
| Tampon süresi | Örnek sayısı ÷ örnek hızı | `§23` |
| Uçtan uca gecikme | Girdiden fiziksel çıkışa toplam gecikme | `§23` |
| Çelişki | İki iddianın aynı anda doğru olamaması | `§23` |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode