---
title: "Neva Engine — Kullanıcı Gereksinimleri & FxSound/VoiceMeeter Farkları"
type: doc
category: audio-engine
version: 1.0.0
status: planlama
authority: "SSOT: kullanıcı direktifi 2026-10-14 · 04-reverse-voicemeeter-fxsound · 05-neva-mixer-web-pcm-plani"
updated: 2026-10-14
---

# Neva Engine — Gereksinimler ve Rakip Farkları (A-Takımı taahhüdü)

**Zorunlu Bağlantılar:** [[05-neva-mixer-web-pcm-plani]] · [[04-reverse-voicemeeter-fxsound]] · [[02-dsp-zincir-mimari]] · [[00-sdk-lib-envanter]] · [[01-gelistirme-plani]]

## §1 Kullanıcı Gereksinim Listesi (2026-10-14 direktifi — ham metinden çıkarım)

| # | Gereksinim | Teknik karşılık | Durum |
|---|-----------|----------------|-------|
| R1 | **FxSound ve VoiceMeeter'dan farklı olacak** — taklit değil, daha profesyonel | Clean-room tasarım; RE raporu yalnızca özellik envanteri (04 §kural) | BAĞLAYICI |
| R2 | **10 cihaza ses çıkışı** — A1, A5, A10 … etiketli çıkışlarla (VoiceMeeter A1/A2/A3 mantığı ×10) | Multi-out bus mimarisi: **10 fiziksel çıkış aynı anda**, her biri ayrı cihaza routing (aux-style çıkış yönetimi) | ✅ ONAYLI (Q1, 2026-10-14) |
| R3 | **Sıfır gecikme ile ses karıştırma (mix)** | RT zincirinde lock-free summing; hedef ASIO <10 ms roundtrip (vault spec) — "sıfır" = algılanamaz seviye, fiziksel 0 ms mümkün değil | PLANLI |
| R4 | **32-bit float** | Float32 PCM pipeline (zaten vault spec — CLAUDE §19/§24) | BAĞLAYICI |
| R5 | **Cihaza göre çalışacak + otomatik tanıyacak** | Otomatik device enumeration/hot-plug: MMDevice (Win), udev/PipeWire (Linux), CoreAudio listener (macOS); profil başına EQ/kalibrasyon atama | ✅ ONAYLI (Q3: hot-plug + otomatik profil ikisi de) |
| R6 | **Samsung Galaxy J7 2016'daki "geniş alan konser salonu" efekti gibi web'de doğal geniş sahne efekti** — hoparlörde reverb FX ile aynı hissi vererek PCM/DSP'e uygulanacak | Concert-hall reverb + geniş stereo sahne (width/ambience) DSP modülü; hedef: telefondaki SoundAlive Concert Hall hissinin eşdeğeri. **GENİŞLETME (Freelancer doc v1, 2026-10-15):** psikoakustik mekan preset'leri — **Düğün Salonu · Konser Alanı & Arena · Canlı Stüdyo · Kulüp** (aynı reverb motoru, 4 hazır mekan) | ✅ ONAYLI kapsam (Q2: hoparlör + kulaklık ikisi) · ⚠️ kanıt: bkz §2 |
| R7 | **Ev ses sistemlerinde tek tuşla aynı efekti almak** | Preset sistemi (tek dokunuş → hazır FX seti); 05 §2 preset JSON sözleşmesi ile aynı | PLANLI |
| R8 | **Dinamik güçlendirme (dynamic boost) ≠ Bas güçlendirme (bass boost)** — ikisi AYRI kontroller olacak; dynamic boost bası da güçlendirecek ama kendi mantığıyla | 2 ayrı DSP modülü: (a) bass boost = shelf/peak alt-bant; (b) dynamic boost = çok bantlı dinamik işlem + alt-bant katılımı — FxSound'dan farklı algoritma | BAĞLAYICI |
| R9 | **FX → PCM → DSP zinciri** efekt uygulama hattı | 02-dsp-zincir-mimari 8 aşamalı zincir ile birebir uyumlu | PLANLI |
| R16 | **EQ ÇİFT FORM (tur 2):** dinleme sırasında ses güzelleştirme — **hem parametrik EQ hem grafik EQ**, 2 modda multi uyumlu çalışacak, **tam kontrol** | Aynı biquad motorunun 2 arayüz formu: (a) parametrik (her bant: frekans Q kazanç — 02 §EQ biquad) · (b) grafik (31 bant slider — `legacy-2026-07/equalizer-31band`). Mod geçişi canlı (restart YOK); bantlar senkron (aynı motor, 2 görünüm); cihaz/profil başına EQ formu hatırlanır (06 R5 profil sistemiyle uyumlu) | BAĞLAYICI (tur 2) |
| R18 | **CİHAZ BAZLI OTOMATİK VARSAYILAN (tur 4, 2026-10-15):** global sabit varsayılan (örn. 44100 Hz / 512 smp) **YOK** — varsayılan değerler **cihaza göre otomatik tespit** edilir; yeni cihaz bağlandığında veya mevcut cihaz değiştiğinde ayarlar o cihazın yeteneğine göre **otomatik** güncellenir | Cihaz algılama + per-device profil (R5 profil sistemiyle entegre). Em-saller: VP ASIO sürücüsü SR/latency'i engine'den shared-memory'den okur (04 §8.3) · FxSound per-device `device_configs` + `form_factor` (04 §7.7). Neva: OS device-change callback (WASAPI/CoreAudio/AAudio) → capability sorgu (SR/buffer/kanal) → cihaz-hafızası profil yükleme; kayıt: 09 doc §9 | BAĞLAYICI (tur 4) |
| R17 | **MOBILE DSP (tur 3):** hem **Android'de hem iOS'ta ses DSP** — mobil sadece kumanda değil, telefonda yerel işlenir; PC DSP'nin tamamı mobilde de çalışır | Aynı C++ çekirdek 2 platforma derlenir: **Android** = NDK (CMake) + AAudio/Oboe (SL ES fallback); **iOS** = Xcode (C++/ObjC++) + CoreAudio/AVAudioEngine. DSP zinciri (EQ çift-form · FX · compressor · limiter) birebir aynı. min OS: Android API 26+ ⚠️ VERIFICATION REQUIRED · iOS 15+ ⚠️ | BAĞLAYICI (tur 3) |

## §2 Kanıt Durumu — J7 "Konser Salonu" Efekti

| İddia | Kanıt | Not |
|-------|-------|-----|
| Samsung telefonlarda SoundAlive paketi "Concert Hall" reverb içerir | ✅ Samsung topluluk yanıtı (r2.community.samsung.com/t5/Galaxy-J — SoundAlive "3d, bass, clarity and Concert Hall effect") + AndroidHeadlines Note 7 incelemesi ("concert hall reverb") | 2 kaynak, 2026-10-14 websearch |
| Galaxy **J7 2016**'da bizzat bu efekt menüsü | ⚠️ VERIFICATION REQUIRED | J7-özel ekran/kaynak bulunamadı; efekt sınıfı J serisinde mevcut, J7 2016 birebir doğrulanmadı |
| Kullanıcı "webde" ifadesi | YORUM: "web arayüzünde ayarlanabilir" olarak okundu (Neva Mixer web UI) — ya da tarayıcıda çalışan sürüm | ⚠️ Q2 |

## §3 Açık Sorular → CEVAPLANDI (kullanıcı onayı 2026-10-14)

| Q | Soru | Cevap |
|---|------|-------|
| Q1 | "A1, A5, A10 … 10 çıkış" = 10 fiziksel çıkış mı, sanal bus mı? | ✅ **10 fiziksel cihaz aynı anda** — cihaza göre aux-style çıkış yönetimi (routing: her A-çıkışı ayrı cihaza) |
| Q2 | J7 efekti kapsam: hoparlör mü, kulaklık mı, "webde" ne? | ✅ **İkisi de** (hoparlör + kulaklık) — web UI'dan kontrol |
| Q3 | Otomatik tanıma = hot-plug mı, profil mi? | ✅ **İkisi de** — hem hot-plug otomatik tespit hem cihaza özel otomatik profil yükleme |

> Gate kapandı: R2/R5/R6 implementation'a açıktır (05 §3.1-§3.2'ye işlendi).

## §4 FxSound / VoiceMeeter Fark Matrisi (Ne YAPILMAYACAK + Ne EKLENECEK)

| Yön | VoiceMeeter Potato | FxSound | **Neva (fark)** |
|-----|-------------------|---------|-----------------|
| Çıkış sayısı | 3 fiziksel (A1-A3) | 1 çıkış (varsayılan cihaz) | **10 çıkış hedefi (R2; Q1 kapandı 2026-10-15 — Win7 modern + XP legacy)** |
| Gecikme | Düşük (WDM/ks bind) | Orta (WASAPI shared) | ASIO/WASAPI-exclusive <10 ms (R3) |
| Ses kalitesi | Float32 iç, cihaza göre bit | DSP sonrası shared | **Bit-perfect 32-bit float uçtan uca (R4)** |
| EQ | Strip 10 bant + bus 15 bant | 10 bant graphic | 31 bant parametrik + grafik (02 §EQ) |
| Cihaz yönetimi | Manuel strip atama | MMNotification hot-plug | **Otomatik tanıma + profil atama (R5)** |
| Reverb/sahne | Yok (bus reverb yok) | Ambience/Surround (0-10) | **Konser-salonu sahne modülü (R6) — ayrı FX rack'te** |
| Dynamic vs Bass | Yok ayrı | DynamicBoost + BassBoost ayrı 0-10 slider | **Ayrı modüller, dynamic basa da dokunur ama ayrı algoritma (R8) — FxSound mantığı kopyalanmaz** |
| Preset | Macro Editor (karmaşık) | Preset | **Tek tuş ev-modu preset (R7)** |
| Arayüz | Win32 native | Win32 native | **Web UI (yerel WS) + native çekirdek (05 §2)** |
| Lisans/kod | Kapalı | Kapalı | Clean-room C++20 (kod kopyası YASAK) |

## §5 Bağlayıcı Kurallar

1. Clean-room: VoiceMeeter/FxSound koduna erişim YOK — yalnız 04'teki string-kanıtlı özellik envanteri.
2. "Sıfır gecikme" pazarlama değil mühendislik hedefi: ölçüm (roundtrip buffer 64/128/256) olmadan "0 ms" iddiası yazılmaz.
3. R8'deki ayrı-modül kuralı: dynamic boost ve bass boost **asla tek slider'da birleştirilmez** (kullanıcı kuralı).
4. Açık sorular (§3) implementation gate'dir — Q1 netleşmeden output matrix kodu yazılmaz.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
