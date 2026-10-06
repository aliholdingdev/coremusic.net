---
title: "K038.2 — CoreAudio Düşük Gecikme Yolu ve Gerçek-Zamanlı Thread Kuralları"
type: architecture
category: "K-Modül — Sürücü / D01"
version: 1.0.0
status: active
authority: "SSOT — .ai/architecture"
updated: 2026-10-06
---

# K038.2 — Düşük Gecikme Yolu (CoreAudio)

> **Bağlantılar:** [[index]] · [[coreaudio-hal]] · [[../k036-asio-drivers/asio-latency-hesap]] · [[../k037-wasapi-exclusive/wasapi-buffer-latency]] · [[../k039-alsa-native/alsa-pcm-device]] · [[../k032-latency-optimization/latency-optimization]] · [[../k031-buffer-management/kilitsiz-kuyruklar]] · [[../k025-threading-model/gercek-zamanli-zamanlama]]
>
> **Kaynak:** Bu dosyadaki her sayısal satır `_backup/arch-2026-10-06_1057/architecture/` altındaki K-A/K-B/K-D/K-E/K-F dosyalarından gelir (envanter: [[index]] §0). Kaynakta olmayan hiçbir ms/sample değeri üretilmedi; hesapla bulunan değerler **"türetme"** etiketi taşır.

---

## §1 Layer Kimliği (Layer Model)

| Alan | Değer |
|------|-------|
| **Layer ID** | K038.2 |
| **Layer Name** | CoreAudio Düşük Gecikme Yolu |
| **Domain** | D01 — Sürücü / ses yolları (macOS) |
| **Purpose** | CoreAudio yolu boyunca uçtan uca gecikme bütçesini, gerçek-zamanlı (RT) thread yasaklarını ve exclusive/shared durumunu tek yerde tanımlamak |
| **Responsibility** | Gecikme hesabı · RT yasak listesi · gözlemlenebilirlik · çelişki kaydı |
| **Inputs** | Buffer boyutu (kare) · örnekleme hızı (SR) · cihaz property okumaları · guardrail metni (K-B) |
| **Outputs** | Hesap tabloları · yasak listesi · ölçüm protokolü · `⚠️ VERIFICATION REQUIRED` işaretleri |
| **Dependencies (Allowed)** | `CoreAudio.framework` / `AudioToolbox.framework` (K-A L276–L282) · K1 macOS Core (GCD/XPC/IOKit) · K031 buffer · K032 latency · K025 RT zamanlama |
| **Dependencies (Forbidden)** | ASIO/WASAPI/ALSA API'lerine doğrudan çağrı (platform yolları ayrık) · kaynağı olmayan sayısal iddia · `.ai/architecture/` dışı yazım |
| **Runtime** | macOS · gerçek zamanlı audio thread (render callback dönemi) |
| **Owner** | `embedded-engineer` — dayanak: `.ai/AGENTS.md §6` keyword routing ("C++, ASIO, JUCE, audio, DSP … → Embedded Engineer") |
| **Technology** | CoreAudio · AudioUnit (HALOutput) · `AudioStreamBasicDescription` · `AudioTimeStamp` |
| **Security Boundary** | Uygulama süreci içi; donanım erişimi HAL Plugin'e kadar — sandbox/IOKit yetkileri `../k021-macos-core/macos-core` kapsamında (bu dosyanın dışı) |
| **Data Boundary** | Ses buffer'ı (float, `ioData->mBuffers[0].mData`) süreç içi; disk/ağa yazılmaz |
| **Failure Mode** | Underrun (kopuk/tıslama) · SR mismatch (pitch) · clock drift · cihaz kaybı — bkz. §6 |
| **Observability** | `getLatency()` (`K-A L241`) · `isRunning()` (`L233`) · nominal SR property (`L175–L181`) · callback sayacı/ölçüm → §7 |
| **Tests** | §7 test matrisi (tamamı `⚠️ kod yok` — implementasyon bekliyor) |
| **Documentation** | [[index]] (kapsam/kanıt) · [[coreaudio-hal]] (API/adımlar) · bu dosya (bütçe + kurallar) |

---

## §2 Kaynak Gecikme Hesabı (K-B — birebir)

`_backup/arch-2026-10-06_1057/architecture/k2-surucu/CLAUDE.md L36–L43`:

```
Buffer: 512 samples @ 48kHz = 10.67ms (tek yön)
Buffer: 256 samples @ 48kHz = 5.33ms (tek yön)
Buffer: 128 samples @ 48kHz = 2.67ms (tek yön)
Toplam: Çift yön = ~2x tek yön gecikme
```

| Buffer (sample) | SR | Tek yön (kaynak) | Çift yön (kaynak kuralı: ~2×) |
|-----------------|-----|------------------|-------------------------------|
| 512 | 48 kHz | 10.67ms | ~21.3ms (türetme: 10.67×2) |
| 256 | 48 kHz | 5.33ms | ~10.7ms (türetme: 5.33×2) |
| 128 | 48 kHz | 2.67ms | ~5.3ms (türetme: 2.67×2) |

**Türetme kaydı (üretilen tek sayılar bunlardır):**

| Türetme | Hesap | Dayanak |
|---------|-------|---------|
| 128 @ 96 kHz tek yön | 128 ÷ 96000 = **1.33ms** | SR değeri K-A `L95` (örnek ASBD 96000) + K-B formülü |
| 256 @ 96 kHz tek yön | 256 ÷ 96000 = **2.67ms** | aynı |
| 128 @ 44.1 kHz tek yön | 128 ÷ 44100 = **2.90ms** | SR alt sınır K-A `L274` (44.1k) |

> Kural: `ms = sample / SR`. K-B'deki üç satır da bu kuralı doğrular (512/48000=10.67 ✓ · 256/48000=5.33 ✓ · 128/48000=2.67 ✓).

---

## §3 Gerçek-Zamanlı Thread Yasakları

### §3.1 Guardrail tabanı (K-B `L18–L24` — birebir)

| # | Kural | İhlal sonucu | CoreAudio yolu kapsamı |
|---|-------|-------------|------------------------|
| 1 | ASIO Exclusive Lock — tek uygulama | Sürücü çökmesi | **Uygulanmaz** (ASIO'ya özgü) |
| 2 | Audio thread blocking yasak | Ses takılması | **Uygulanır** — render callback thread'i |
| 3 | Buffer underrun koruması zorunlu | Crackling | **Uygulanır** — callback bütçesi aşılırsa |
| 4 | Sample rate mismatch önlem | Pitch shift | **Uygulanır** — ASBD ↔ cihaz SR denetimi |

### §3.2 Callback yasakları (türetme — kaynakta doğrudan yazılmamıştır)

| # | Yasak | Dayanak maddesi | Not |
|---|-------|-----------------|-----|
| Y1 | Mutex/lock beklemesi (lock alınması) | K-B madde 2 (blocking) | Türetme |
| Y2 | Dosya/sockets/głóbal IO erişimi | K-B madde 2 (blocking) | Türetme |
| Y3 | `malloc`/`new`/`delete` (bellek ayırma) | K-B madde 2 + 3 (blocking + underrun) | Türetme |
| Y4 | Callback süresinin dönem (period) süresini aşması | K-B madde 3 (underrun) | Türetme |
| Y5 | SR uyuşmazlığını gizleyen format dönüşümü | K-B madde 4 | Türetme |
| Y6 | Çağrı bloğunda senkron log yazımı | K-B madde 2 (blocking) | Türetme |

> **Sınır:** Bu altı satır guardrail'den **türetilmiştir**; `core-audio-macos.md` içinde benzer bir yasak listesi **yoktur**. `⚠️ VERIFICATION REQUIRED` — uygulama düzeyinde doğrulama kod ile yapılacaktır.

---

## §4 Exclusive vs Shared Karşılaştırması (kanıtlı sınırlarla)

| Yol | Mod | Kanıt | Dosya |
|-----|-----|-------|-------|
| ASIO (K036) | Exclusive / tek uygulama kilidi | K-B madde 1 | `../k036-asio-drivers/asio-exclusive-mode` |
| WASAPI Exclusive (K037) | Latency 1–3ms · bit-perfect ✓ · multi-app ✗ | K-E `L53–L59` | `../k037-wasapi-exclusive/wasapi-exclusive-shared` |
| WASAPI Shared (K037) | Latency 10–40ms · multi-app ✓ | K-E `L53–L59` | aynı |
| ALSA (K039) | MMAP 1ms/0.8ms · RW 5ms/4.2ms | K-F `L222–L230` | `../k039-alsa-native/alsa-pcm-device` |
| **CoreAudio (K038)** | hog mode / exclusive sahiplik kanıtı **yok** | — | `⚠️ VERIFICATION REQUIRED` (kaynaklarda `hog` geçmiyor) |

**Sonuç:** CoreAudio'un "exclusive" karşılığı bu vault'ta kanıtlanamadı; uygulama `shared` varsayımıyla mı yoksa başka bir mekanizmayla mı çalıştığı `[UNKNOWN]`.

---

## §5 Uçtan Uca Zincir (CoreAudio düşük gecikme yolu)

| # | Adım | İşlem | Kanıt |
|---|------|-------|-------|
| 1 | Cihaz seçimi | `listDevices()` → UID → `setOutputDevice` | K-A `L44–L65`, `L220–L223` |
| 2 | SR doğrulama | `getSupportedSampleRates(deviceId)` | K-A `L244–L245` |
| 3 | Buffer seçimi | `setBufferSize(frames)` (örnek: 256) | K-A `L227`, `L256–L257` |
| 4 | Format sabitleme | ASBD (`mSampleRate=96000` örneği) | K-A `L94–L111` |
| 5 | Callback kaydı | `registerRenderCallback` / `SetInputCallback` | K-A `L136–L145`, `L236–L237` |
| 6 | Başlatma | `start()` → `AudioOutputUnitStart` | K-A `L231`, `L171` |
| 7 | Dönemsel veri | `renderCallback` → `engine->process(buffer, frames)` | K-A `L120–L133` |
| 8 | Gecikme okuma | `getLatency()` | K-A `L241` |
| 9 | Döngü ölçümü | round-trip → hedef 1ms / kaynak "Gerçek" 0.8ms | K-A `L266–L274` |

**Zincir maliyeti (türetme):** toplam gecikme ≈ (uygulama buffer) + (cihaz period) + (çift yön dönüşüm) — kaynaklarda **bileşen bazlı ölçüm yok**, yalnız uçtan uca iddia var (`⚠️ VERIFICATION REQUIRED`).

---

## §6 Failure Mode tablosu

| # | Failure | Belirti | Kök neden | Azaltma | Yönlendirme |
|---|---------|---------|-----------|---------|-------------|
| F1 | Underrun | Kopuk/tıslama | Callback bütçesi aşıldı (Y4) | Buffer/period artışı · lock-free yol | `../k031-buffer-management/kilitsiz-kuyruklar` |
| F2 | SR mismatch | Pitch/kısa-oran hatası | Uygulama ↔ cihaz SR farkı | K-B madde 4 reddi | [[coreaudio-hal]] §5.4 |
| F3 | Clock drift | Zamanlama kayması | Nominal SR değişimi | Property yeniden okuma | [[coreaudio-hal]] §7 |
| F4 | Cihaz kaybı | Akış kesilir | USB/transport çıkar | UID ile yeniden bağlama | `../k034-usb-audio/usb-hotplug-enumerasyon` |
| F5 | Hesap↔iddia çelişkisi | Beklenen gecikme aşıldı | §8'deki uyumsuzluk | Ölçüm kapısı | §8 |

---

## §7 Observability & Tests

### §7.1 Gözlemlenebilirlik

| Sinyal | Kaynak | Durum |
|--------|--------|-------|
| `getLatency()` | K-A `L241` | API var (tasarım) · gerçek değer `⚠️ VERIFICATION REQUIRED` |
| `isRunning()` | K-A `L233` | API var · gözlem kanıtı yok |
| Nominal SR property | K-A `L175–L181` | API var · drift olay kanıtı yok |
| Callback dönem sayacı / callback süresi | — | `[UNKNOWN]` — kaynakta ölçüm mekanizması yok |

### §7.2 Test matrisi

| ID | Test | Beklenen | Durum |
|----|------|----------|-------|
| D1 | 128 @ 48k hesabı | 2.67ms (K-B ile birebir) | ✅ hesap doğrulandı (bu revizyon) |
| D2 | 128 @ 96k hesabı | 1.33ms (türetme) | ✅ hesap doğrulandı (bu revizyon) |
| D3 | Round-trip ölçüm | 0.8ms (kaynak iddiası) | ⚠️ ölçüm yok |
| D4 | Callback bütçesi içinde bitiş | periyot < buffer süresi | ⚠️ kod yok |
| D5 | Callback'te lock/IO/allocate yok | 0 ihlal | ⚠️ kod yok |
| D6 | Buffer 256 @ 96k ≈ 2.67ms tek yön | hesapla uyum | ✅ hesap (türetme) |

---

## §8 Çelişki Kaydı (açık — kapatılmadı)

| # | Çelişki | Kanıt A | Kanıt B | Durum |
|---|---------|---------|---------|-------|
| C1 | CoreAudio `Gerçek latency = 0.8ms` (çift yön varsayımıyla) | K-A `L268–L270` | K-B `L41`: 128 sample @48kHz = **2.67ms tek yön** → ~5.3ms çift yön; K-A'daki "Buffer Gerçek = 128" ile birlikte **0.8ms ile örtüşmüyor** | `⚠️ VERIFICATION REQUIRED` — SR 48k mı 96k mı belirsiz; 96k'da bile 128 sample = 1.33ms tek yön (türetme) > 0.8ms |
| C2 | Buffer aralığı "64–256" iken örnek kullanım `setBufferSize(256)` ve "Gerçek 128" | K-A `L269`, `L257` | K-A `L269` (Gerçek 128) | Tutarlı ama hangi koşulda 128 ölçüldüğü `[UNKNOWN]` |
| C3 | Metal GPU `<1ms` hedefi | K-C `L531` ("Gerçek" = Belirlenecek) | K-A performans tablosunda Metal satırı yok | `⚠️ VERIFICATION REQUIRED` |

**Kapanış koşulu:** gerçek donanım üzerinde round-trip ölçümü (ölçüm protokolü → [[index]] §14 M1/M9) + SR/buffer koşulunun kayda geçmesi.

---

## §9 Kanıt Satırları (bu dosya)

| # | İddia | Kaynak dosya | Satır |
|---|-------|--------------|-------|
| 1 | Buffer latency hesabı 512/256/128 @48k + çift yön kuralı | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/CLAUDE.md` | L36–L43 |
| 2 | Hard guardrail 4 madde | aynı | L18–L24 |
| 3 | Performans tablosu (1ms/0.8ms · 64–256/128 · 44.1k–384k · 128 kanal) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` | L266–L274 |
| 4 | Örnek ASBD SR = 96000 | aynı | L94–L104 |
| 5 | `setBufferSize(256)` kullanım örneği | aynı | L256–L257 |
| 6 | `getLatency()` / `isRunning()` | aynı | L241 / L233 |
| 7 | SR aralığı okuma | aynı | L244–L247 |
| 8 | Callback gövdesi `engine->process` | aynı | L120–L133 |
| 9 | WASAPI Exclusive/Shared latency 1–3ms / 10–40ms | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` | L53–L59 |
| 10 | ALSA MMAP/RW 1ms/0.8ms · 5ms/4.2ms | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` | L222–L230 |
| 11 | ASIO round-trip 1.34/1.33ms | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` | L156–L163 |
| 12 | Owner routing (embedded-engineer) | `.ai/AGENTS.md` | §6 |
| 13 | ms = sample/SR türetmeleri (1.33/2.67/2.90) | hesap — K-B formülü + K-A SR değerleri | §2 |
| 14 | CoreMIDI/AVAudioEngine kanıtsızlığı | yedek geneli tarama (0 eşleşme) | [[index]] §1.3 |

---

## §10 Risk & Açık Konu

| # | Risk | Olasılık | Etki | Azaltma |
|---|------|---------|------|---------|
| D-R1 | C1 çelişkisi yanlış latency beklentisi doğurur | Yüksek | Yüksek | Ölçüm kapısı (§8) |
| D-R2 | RT yasakları yalnız kâğıtta | Yüksek | Yüksek | CA-17/D5 testleri (kod bekleniyor) |
| D-R3 | hog mode varsayımı | Orta | Orta | `⚠️ VERIFICATION REQUIRED` (§4) |
| D-R4 | Komşu linklerde eksik/eksik güncellenmiş hedef | Orta | Düşük | [[index]] §19.2 raporu |

---

## §11 Kaynak Düzeyi Sözlük

| Terim | Tanım | Kanıt |
|-------|-------|-------|
| Tek yön / çift yön | Yalnız çıkış ↔ giriş+çıkış toplamı | K-B `L42` |
| Period | Bir callback çağrısındaki kare sayısı | K-A `L124` (`inNumberFrames`) |
| Underrun | Donanımın beklediği sürede veri hazır olmaması | K-B madde 3 |
| Türetme | Kaynak sayılarından hesapla üretilen değer | §2 tablosu |
| hog mode | macOS cihaz sahiplik kilidi | `⚠️ VERIFICATION REQUIRED` (kanıt yok) |

---

## §12 Değişiklik Geçmişi (append-only)

| Tarih | Sürüm | Değişiklik | Yazar |
|-------|-------|-----------|-------|
| 2026-10-06 | 1.0.0 | İlk üretim — Layer modeli, gecikme hesabı, RT yasakları, exclusive/shared, çelişki kaydı (C1–C3) | Vault Documentation Specialist |
