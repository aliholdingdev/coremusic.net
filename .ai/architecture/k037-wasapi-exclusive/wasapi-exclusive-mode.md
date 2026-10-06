---
title: "K037.2 — WASAPI Exclusive Mode: IAudioClient, Bit-Perfect ve Tek Sahip Kilidi"
type: architecture
category: "K-Modül — Sürücü / D01"
version: 4.0.0
status: active
authority: "SSOT — .ai/architecture"
updated: 2026-10-06
---

# K037.2 — WASAPI Exclusive Mode

**Bağlantılar:** [[index]] · [[wasapi-exclusive-shared]] · [[wasapi-shared-mode]] · [[wasapi-buffer-latency]] · [[wasapi-format-negotiation]] · [[wasapi-hata-kodlari]] · [[wasapi-audio-session]] · [[wasapi-device-hotplug]] · [[../k036-asio-drivers/asio-exclusive-mode]]

---

## §1 Genel Bakış

Exclusive mode, uygulamanın ses uç noktasını (endpoint) **tek başına** kullanması ve Windows ses motorunun (miksaj + efekt zinciri) bu akışın yoluna girmemesidir. Kaynak dokümanın ifadesiyle: "Doğrudan HW erişimi · Mix organizer yok · Tek uygulama · Bit-perfect output · Düşük latency · Donanım formatı".

> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L16–L31 (WASAPI Working Modes diyagramı).

Bu doküman exclusive mode'un dört çekirdek sorusunu yanıtlar:

1. **Sahiplik:** Cihazı kim, hangi koşulda tek başına alır, kilidi ne zaman kaybeder? (§3, §7)
2. **Arayüz:** `IAudioClient` ile açılış/hayat döngüsü nasıl kurulur? (§4)
3. **Bit-perfect:** Dönüşümsüz bit aktarımı hangi koşullarda korunur, ne bozar? (§6)
4. **Öncelik:** Exclusive mode ASIO ile karşılaştırıldığında nerede durur? (§10)

### §1.1 Durum

| Alan | Durum | Kanıt |
|------|-------|-------|
| Tasarım dokümanı (bu dosya) | 🟢 Üretildi (2026-10-06) | Bu dosya |
| Kaynak içerik | 🟢 `k2-surucu/wasapi-exclusive.md` L33–L130 | Okundu |
| Kod implementasyonu | 🔴 Repo'da WASAPI kodu yok | index.md §1.3 · ⚠️ VERIFICATION REQUIRED |
| Donanım ölçümü | 🔴 Yok | ⚠️ VERIFICATION REQUIRED |

---

## §2 Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Exclusive sahiplik modeli, tek uygulama kilidi | ASIO exclusive lock → `../k036-asio-drivers/asio-exclusive-mode` (aynı kavram, farklı API) |
| `IAudioClient` exclusive açılış akışı | Shared miksaj DSP'si → [[wasapi-shared-mode]] |
| Bit-perfect koşulları ve doğrulaması | Format pazarlık ayrıntısı → [[wasapi-format-negotiation]] |
| Exclusive buffer/period seçimi | Buffer formülü ve period negotiation → [[wasapi-buffer-latency]] |
| Exclusive'e özgü AUDCLNT hata yolları | Tam hata kodu tablosu → [[wasapi-hata-kodlari]] |
| Cihaz kaybında exclusive kilit düşüşü | Endpoint notification akışı → [[wasapi-device-hotplug]] |

---

## §3 Sahiplik Modeli

### §3.1 Tek sahip kuralı

Exclusive mode'da bir uç noktayı **aynı anda tek bir `IAudioClient` akışı** işgal eder. Başka bir uygulama aynı cihazı exclusive ile açmak isterse açılış reddedilir; kaynak dokümanda bu durumun kodu `0x8889000A` (`AUDCLNT_E_DEVICE_IN_USE`) olarak verilir.

Kanıt: `wasapi-exclusive.md` L124–L130 · Öncelik bağlamı: `_backup/.../k2-surucu/CLAUDE.md` L27–L34 (ASIO #1, WASAPI Exclusive #2, WASAPI Shared #6).

### §3.2 Sahiplik durum makinesi

```
        ┌─────────────┐
        │  AVAILABLE  │  cihaz boş, exclusive denenebilir
        └──────┬──────┘
               │ Initialize(AUDCLNT exclusive) başarılı
               ▼
        ┌─────────────┐   ikinci süreç dener
        │  OWNED      │ ───────────────────────▶ 0x8889000A (REJECTED)
        │ (tek sahip) │                            └─▶ bkz. [[wasapi-shared-mode]]
        └──────┬──────┘
               │ Start() → akış canlı
               ▼
        ┌─────────────┐
        │  RUNNING    │ ── Stop() ──▶ RELEASED ──▶ AVAILABLE
        └──────┬──────┘
               │ cihaz kaybı / process crash
               ▼
        ┌─────────────┐
        │  INVALIDATED│ ── UID ile yeniden enumeration ──▶ AVAILABLE
        └─────────────┘   (bkz. [[wasapi-device-hotplug]])
```

> Durum adları mimari tasarım notudur; API'de resmi enum karşılıkları yoktur (tasarım soyutlaması).

### §3.3 Exclusive ↔ Shared sahiplik karşılaştırması

| Özellik | Exclusive | Shared |
|---------|-----------|--------|
| Sahiplik | Tek uygulama | Çoklu uygulama |
| Mix organizer | Devrede değil | Devrede |
| DSP | Donanım / yok | Windows |
| Latency | 1–3 ms | 10–40 ms |
| Bit-perfect | Evet | Hayır |
| CPU | Düşük | Yüksek |

Kanıt: `wasapi-exclusive.md` L16–L31 ( Working Modes diyagramı ) + L53–L59 (avantaj tablosu).

### §3.4 ASIO ile sahiplik kıyası

| Boyut | WASAPI Exclusive | ASIO |
|-------|------------------|------|
| Sahiplik uygulaması | Windows ses servisi tarafından zorlanır | Sürücü/SDK tarafından zorlanır |
| İkinci açılış | `0x8889000A` ile reddedilir | Exclusive lock ile reddedilir (guardrail #1) |
| Öncelik sırası (k2) | #2 | #1 |
| Bit-perfect | Evet | Evet |
| Round-trip hedefi | 3 ms (kaynak L181) | 1.34 ms hedef / 1.33 ms gerçek (k036 kanıtı) |

Kaynaklar: `_backup/.../k2-surucu/CLAUDE.md` L27–L34 · `wasapi-exclusive.md` L177–L183 · `../k036-asio-drivers/index` §4.1.

---

## §4 Arayüz Katmanı (resmi WASAPI arayüzleri)

> ⚠️ **Kaynak türü uyarısı:** Bu bölümdeki arayüz ve method adları **standart / official Microsoft WASAPI dokümanı** bilgisidir; CoreMusic repo'sunda karşılık gelen kod **yoktur** (index.md §1.3). Kod iddiası olarak değil, **tasarım referansı** olarak okunur. Uygulama kodu üretilinceye kadar tüm davranış iddiaları `⚠️ VERIFICATION REQUIRED` statüsündedir.

### §4.1 `IAudioClient` yaşam döngüsü

Kaynak doküman ana kontrol noktasını şu şekilde tanımlar: "**IAudioClient arayüzü**: Ana kontrol noktası — `Initialize()`: Exclusive mode ile başlatma · `GetBufferDuration()`: Buffer süresi sorgusu · `Start()` / `Stop()`: Akış kontrolü".

Kanıt: `wasapi-exclusive.md` L37–L40.

| Aşama | Method (official docs) | Exclusive notu |
|-------|------------------------|----------------|
| 1 Aktivasyon | `IMMDevice::Activate(IAudioClient)` | Cihaz handle'ı exclusive isteğiyle alınır |
| 2 Başlatma | `IAudioClient::Initialize(...)` | Exclusive akış bayrağı ile çağrılır |
| 3 Sorgu | `GetBufferSize` / `GetDevicePeriod` / `GetMixFormat` | Period ve format kararı burada |
| 4 Kaynak | `GetService(IAudioRenderClient veya IAudioCaptureClient)` | Exclusive'te format sabitlenir |
| 5 Olay | `SetEventHandle` | Event-driven mod (bkz. [[wasapi-buffer-latency]]) |
| 6 Çalıştırma | `Start()` | Akış ve zamanlama başlar |
| 7 Durdurma | `Stop()` → `Reset()` | Buffer temizliği |
| 8 Serbest bırakma | COM referans sayımı (`Release`) | §4.4 |

> `Initialize()` ve `Start()` çağrılarının exclusive'e özgü parametreleri için official doküman esastır; bu dosyada parametre hex/değeri **üretilmez**.

### §4.2 Çıkış ve giriş arayüzleri

Kaynak doküman:

- **`IAudioCaptureClient`** (giriş): `GetBuffer()` ham veri erişimi · `ReleaseBuffer()` buffer'ı serbest bırakma · `GetNextPacketSize()` bir sonraki paket boyutu.
- **`IAudioRenderClient`** (çıkış): `GetBuffer()` yazma alanı alma · `ReleaseBuffer()` veriyi donanıma iletme.

Kanıt: `wasapi-exclusive.md` L42–L49.

| Arayüz | Yön | Method | Görev |
|--------|-----|--------|-------|
| `IAudioRenderClient` | Uygulama → cihaz | `GetBuffer` | Yazılabilir alan al |
| `IAudioRenderClient` | Uygulama → cihaz | `ReleaseBuffer` | Dolu bloğu motora ilet |
| `IAudioCaptureClient` | Cihaz → uygulama | `GetBuffer` | Dolu bloğu oku |
| `IAudioCaptureClient` | Cihaz → uygulama | `ReleaseBuffer` | Okuma bloğunu serbest bırak |
| `IAudioCaptureClient` | Cihaz → uygulama | `GetNextPacketSize` | Sıradaki paket boyutu (döngü kontrolü) |

### §4.3 Exclusive açılış akış diyagramı

```
IMMDeviceEnumerator ──▶ GetEndpoint (varsayılan çıkış)
        │
        ▼
IMMDevice::Activate ──▶ IAudioClient
        │
        ▼
IsFormatSupported (exclusive) ── reddi ──▶ [[wasapi-format-negotiation]] §5
        │ kabul
        ▼
IAudioClient::Initialize (exclusive + format + period)
        │
        ├─ 0x8889000A ──▶ ikinci sahip var ──▶ [[wasapi-shared-mode]]
        ├─ 0x8889000E ──▶ yetki/bayrak ──▶ [[wasapi-hata-kodlari]]
        ├─ 0x88890008 ──▶ format ──▶ [[wasapi-format-negotiation]]
        └─ 0x88890018 ──▶ buffer ──▶ [[wasapi-buffer-latency]]
        │ başarılı
        ▼
GetService(IAudioRenderClient) → SetEventHandle (event-driven)
        │
        ▼
Start() ──▶ RUNNING ──▶ bit-perfect doğrula (§6.2)
```

### §4.4 COM referans sayımı kuralları

| # | Kural | İhlal sonucu |
|---|-------|-------------|
| 1 | Her başarılı `QueryInterface`/`Activate` → dengeli `Release` | Cihaz kilidi sızar, exclusive kalıcı işgal |
| 2 | Akış `Stop()` sonrası `Release` sırası: render/capture → audioClient → endpoint | Pop/klik ve kilit sızıntısı |
| 3 | Çökme/exception yolunda da `Release` (RAII/`wil::com_ptr` benzeri sarmalayıcı) | Uygulama kapandıktan sonra cihaz "kullanımda" kalır |
| 4 | Event handle ve thread temizliği `Release`'ten önce | Zamanlanmış callback ölü handle'a yazar |

> COM referans sayımı kuralı kök `AGENTS.md` § "COM reference counting mandatory" ile bağlayıcıdır; method adları official docs, sıralama mimari tasarım notudur.

---

## §5 Stream Flags (akış bayrakları)

### §5.1 Official bayrak seti (tasarım referansı)

| Bayrak (official docs) | Amaç | Exclusive ilişkisi |
|------------------------|------|--------------------|
| `AUDCLNT_STREAMFLAGS_SHARED` | Shared akış talebi | Exclusive'de **kullanılmaz** |
| `AUDCLNT_STREAMFLAGS_LOOPBACK` | Çıkışın geri okunması (loopback kayıt) | Shared/loopback senaryosu |
| `AUDCLNT_STREAMFLAGS_EVENTCALLBACK` | Event-driven çalışma | Exclusive düşük gecikmede standart tercih |
| `AUDCLNT_STREAMFLAGS_NOPERSIST` | Oturum ses seviyesinin diske yazılmaması | Bit-perfect / kiosk senaryosu |
| `AUDCLNT_STREAMFLAGS_RATEADJUST` | Örnekleme hızı uyarlama | Exclusive'te **dikkatli**: dönüşüm bit-perfect'i bozar |
| `AUDCLNT_STREAMFLAGS_AUTOCONVERTPCM` | Otomatik format dönüştürme | Exclusive'te **kullanılmaz** (dönüşüm yasak) |
| `AUDCLNT_SRC_DEFAULT_QUALITY` | Kaynak dönüşüm kalitesi | Shared'e ait |

> ⚠️ VERIFICATION REQUIRED: Bayrak **hex değerleri** bu dosyada yazılmaz; Windows SDK `audclnt.h` ile doğrulanır. Repo'da header referansı yoktur.

### §5.2 `AUDCLNT_STREAMFLAGS_EOFIL` doğrulaması

Görev metninde geçen **`AUDCLNT_STREAMFLAGS_EOFIL`** bayrağı, bilinen official WASAPI stream-flag setinde (§5.1) **yer almamaktadır**.

| İddia | Durum |
|-------|-------|
| `AUDCLNT_STREAMFLAGS_EOFIL` official bir bayrak adıdır | ⚠️ VERIFICATION REQUIRED — `audclnt.h` içinde doğrulanamadı |
| Repo'da bu bayrağı kullanan kod | 🔴 Yok (index.md §1.3) |
| Olası kastedilen bayraklar | `AUDCLNT_STREAMFLAGS_LOOPBACK` · `AUDCLNT_STREAMFLAGS_NOPERSIST` · `AUDCLNT_STREAMFLAGS_EVENTCALLBACK` (ad benzerliği spekülasyondur — **kullanıcı onayı gerekir**) |

**Karar:** Bayrak adı doğrulanana kadar bu dosyada **kullanılmaz**; exclusive açılış akışında (§4.3) yalnız §5.1'deki resmi bayraklar referans alınır.

---

## §6 Bit-Perfect Zinciri

### §6.1 Bit-perfect koşulları

| # | Koşul | Kaynak / Dayanak |
|---|-------|------------------|
| 1 | Exclusive mode aktif | `wasapi-exclusive.md` L53–L59 |
| 2 | Format cihazın donanım formatıyla birebir | L29 "Donanım formatı" |
| 3 | Mix organizer / efekt zinciri devrede değil | L24–L25 |
| 4 | Örnekleme hızı uygulama ile cihazda aynı | k2 guardrail #4 (CLAUDE L18–L24) |
| 5 | Otomatik dönüştürme bayrakları kapalı | §5.1 (`RATEADJUST`/`AUTOCONVERTPCM` yasak) |
| 6 | Endpoint volume 0 dB (kısma yok) | bkz. [[wasapi-audio-session]] §5 |

### §6.2 Bit-perfect doğrulama adımları

1. Exclusive akışta bilinen bir bit deseni üret (ör. 0xAA/0x55 ardışık örnekler).
2. Çıkışı loopback veya donanım ölçümüyle geri oku.
3. Gönderilen ile okunan bit desenini birebir karşılaştır.
4. Uyuşmazlık varsa §6.3 tablosundaki bozanları sırayla elele.
5. Sonucu ölçüm protokolüne işle (index.md §14).

> ⚠️ VERIFICATION REQUIRED: Ölçüm altyapısı repo'da yoktur; adımlar tasarım prosedürüdür.

### §6.3 Bit-perfect'i bozan etkenler

| # | Etken | Sonuç | Önlem |
|---|-------|-------|-------|
| 1 | Shared modda çalışma | Windows miksajı + DSP | Exclusive yolu seç |
| 2 | SR uyuşmazlığı | Resample → pitch/bit kaybı | Açılışta reddet (guardrail #4) |
| 3 | Otomatik format dönüştürme bayrağı | Uygulama katmanında dönüşüm | Bayrakları §5.1'e göre seç |
| 4 | Endpoint volume < 0 dB | Yazılım kısma (bit değişimi) | Volume 0 dB sabitle |
| 5 | Ortak paylaşımlı format (16/48 varsayılanı) | Bit derinliği düşer | Exclusive'te donanım formatı |
| 6 | Loopback/dönüşüm senaryosu | Karışım kaynağı | Loopback'i ayrı akışta tut |

> 4. madde byte düzeyinde doğrulama gerektirir → `⚠️ VERIFICATION REQUIRED` (donanım ölçümü yok).

---

## §7 Tek Sahip Kilidi — Olaylar ve Kayıp

### §7.1 Kilidi veren olaylar

| Olay | Sonuç |
|------|-------|
| Exclusive `Initialize` başarı | Kilidin alınması |
| `Start()` | Akışın canlanması (kilip sürücüsü) |
| İkinci sürecin exclusive denemesi | `0x8889000A` → reddi (§3.1) |

### §7.2 Kilidi kaybeden olaylar

| Olay | Davranış | Yönlendirme |
|------|----------|-------------|
| `Stop()` + `Release()` | Kilit temiz bırakılır | §4.4 |
| Cihaz fiziksel olarak çıkar | Akış hata verir — cihaz-geçersizleme kodu backup'ta **YOK** (`⚠️ VERIFICATION REQUIRED`; en yakın backup kodu `0x8889000A` = `DEVICE_IN_USE`, L126) | [[wasapi-device-hotplug]] |
| Uygulama crash | OS handle temizliği kilit bırakır; gecikme riski | COM/RAII kuralı §4.4 |
| Windows ses servisi yeniden başlatma | Oturum ve akış koptu | [[wasapi-audio-session]] §7 |
| Yetki/privilej düşüşü | `0x8889000E` sınıfı red | [[wasapi-hata-kodlari]] |

### §7.3 İkinci uygulama senaryosu (adım adım)

| Adım | Olay | Sistem yanıtı | Kullanıcıya |
|------|------|---------------|-------------|
| 1 | Uygulama B exclusive dener | Sahiplik pazarlığı | — |
| 2 | Cihaz A tarafından işgal | `0x8889000A` | "Cihaz başka bir uygulama tarafından kullanılıyor" |
| 3 | Mod düşürme kararı | Shared'a geç | "Paylaşımlı modda çalışıyor" |
| 4 | Latency etkisi | 3 ms → 30 ms | Gösterge güncellemesi |

Kaynak: `wasapi-exclusive.md` L124–L130 + L177–L183.

---

## §8 Exclusive Açılış Prosedürü

| # | Adım | Girdi | Çıktı | Hata yolu |
|---|------|-------|-------|-----------|
| 1 | Cihaz listesi tara | — | Endpoint listesi | Cihaz yok → dur |
| 2 | Varsayılan çıkış seç | Rol (console/multimedia) | Cihaz UID | Yok → [[wasapi-device-hotplug]] |
| 3 | Format sabitle | SR / bit / kanal | `WAVEFORMATEX` | Uyuşmazlık → [[wasapi-format-negotiation]] |
| 4 | Period seç | Hedef latency | Buffer süresi | Red → [[wasapi-buffer-latency]] |
| 5 | Exclusive Initialize | Bayrak + format + süre | `IAudioClient` | §11 hata kodları |
| 6 | Render/capture servisi al | — | `IAudioRenderClient` | COM hatası → §4.4 |
| 7 | Event handle kur | — | Event | Kurulum hatası → dur |
| 8 | Start | — | RUNNING | Hata → §11 |
| 9 | Bit-perfect doğrula | Bit deseni | ✓/✗ | ✗ → §6.3 |
| 10 | Ölçüm al | Round-trip | ≤3 ms hedefi | Sapma → index.md §14 |

Kaynak bağı: adım 3–4 sayısal hedefleri `wasapi-exclusive.md` L113–L121 (3–6 ms buffer, `SetEventHandle`, `THREAD_PRIORITY_TIME_CRITICAL`, CPU affinity).

### §8.1 Düşük gecikme ayarları (kaynak tablosu)

| # | Ayar | Kaynak |
|---|------|--------|
| 1 | Buffer süresi 3–6 ms arası (donanıma bağlı) | `wasapi-exclusive.md` L117 |
| 2 | `IAudioClient::SetEventHandle()` ile kesme zamanlaması | L118 |
| 3 | `THREAD_PRIORITY_TIME_CRITICAL` ile yüksek öncelik | L119 |
| 4 | Ses thread'ini belirli CPU çekirdeğine ata (affinity) | L120 |

---

## §9 Guardrails (bağlayıcı)

| # | Kural | İhlal sonucu | Kaynak |
|---|-------|-------------|--------|
| 1 | ASIO Exclusive Lock — tek uygulama | Sürücü çökmesi | `k2-surucu/CLAUDE.md` L18–L24 |
| 2 | Audio thread blocking yasak | Ses takılması | aynı |
| 3 | Buffer underrun koruması zorunlu | Crackling | aynı |
| 4 | Sample rate mismatch önlem | Pitch shift | aynı |

> Guardrail #1 ASIO'ya aittir; WASAPI Exclusive'in paralel kuralı §3.1'deki tek sahip modelidir (aynı prensip, farklı API).

---

## §10 Performans ve Öncelik

| Metrik | Exclusive | Shared | Kaynak |
|--------|-----------|--------|-------|
| Input Latency | 1.5 ms | 15 ms | `wasapi-exclusive.md` L179 |
| Output Latency | 1.5 ms | 15 ms | L180 |
| Round-trip | 3 ms | 30 ms | L181 |
| CPU (boşta) | %0.5 | %2 | L182 |
| Bit-perfect | Evet | Hayır | L183 |

Öncelik sırası (k2): `1 ASIO · 2 WASAPI Exclusive · 3 CoreAudio · 4 ALSA · 5 PipeWire · 6 WASAPI Shared` — Kanıt: `_backup/.../k2-surucu/CLAUDE.md` L27–L34.

---

## §11 Hata Yolları (özet → tam tablo)

| Kod | Ad | İlk müdahale |
|-----|----|--------------|
| 0x8889000A | DEVICE_IN_USE | Shared'a geç |
| 0x88890008 | UNSUPPORTED_FORMAT | Format pazarlığı |
| 0x8889000E | EXCLUSIVE_MODE_NOT_ALLOWED | Yetki/bayrak kontrolü |
| 0x88890018 | BUFFER_SIZE_ERROR | Period yeniden seç |

Tam tablo + sınıflandırma: [[wasapi-hata-kodlari]] · Kanıt: `wasapi-exclusive.md` L124–L130.

---

## §12 Kenar Durumlar

| # | Kenar durum | Beklenen davranış |
|---|------------|-------------------|
| 1 | Exclusive açıkken ikinci uygulama | `0x8889000A` + Shared öner |
| 2 | Exclusive yetkisiz süreçte | `0x8889000E` → yetki denetle |
| 3 | Desteklenmeyen format | `0x88890008` → format listesi |
| 4 | Buffer boyutu geçersiz | `0x88890018` → period yeniden seç |
| 5 | Cihaz aniden çıkar | Kilit düşer, uygulama çökmez → [[wasapi-device-hotplug]] |
| 6 | Uygulama askıya alınmış | Akış durur; dönüşte `Reset` + yeniden `Start` |
| 7 | Windows ses servisi yeniden başlar | Oturum koptu → [[wasapi-audio-session]] §7 |
| 8 | Exclusive açılışta loopback istenirse | Bayrak seti çakışması → §5.1, official doküman |

---

## §13 Test Matrisi (EX-serisi)

| ID | Test | Beklenen | Durum |
|----|------|----------|-------|
| EX-W01 | Exclusive açılış | Başarılı, bit-perfect | ⚠️ kod yok |
| EX-W02 | İkinci süreç exclusive dener | `0x8889000A` | ⚠️ kod yok |
| EX-W03 | Bit deseni round-trip | Birebir eşleşme | ⚠️ ölçüm yok |
| EX-W04 | SR uyuşmazlığı ile açılış | Reddet (guardrail #4) | ⚠️ kod yok |
| EX-W05 | Otomatik dönüştürme bayrağı ile açılış | Bayrak reddi veya bit bozulması tespiti | ⚠️ kod yok |
| EX-W06 | Cihaz çıkar → kilit düşüşü | Uygulama çökmez, kurtarma | ⚠️ kod yok |
| EX-W07 | Event-driven callback jitter | ≤ period bütçesi | ⚠️ ölçüm yok |
| EX-W08 | `Release` sonrası ikinci açılış | Başarılı (sızıntı yok) | ⚠️ kod yok |

---

## §14 Bağımlılıklar

| Bağımlılık | Yön | Not |
|-----------|-----|-----|
| Windows SDK | Alt | `wasapi-exclusive.md` L185–L192 |
| K1 Windows Core | Alt | thread/bellek/event |
| K2 buffer yönetimi | Alt | period/buffer boyutu |
| K3 Engine | Üst | ses motoru |
| ASIO (K036) | Yatay | #1 öncelik; exclusive başarısızsa alternatif değil, **üst tercih** |

---

## §15 Kanıt Satırları

| # | İddia | Kaynak | Satır |
|---|-------|--------|-------|
| 1 | Exclusive mode tanımı (doğrudan HW, mix yok, tek uygulama) | `_backup/.../k2-surucu/wasapi-exclusive.md` | L16–L31 |
| 2 | `IAudioClient` kontrol methodları | aynı dosya | L37–L40 |
| 3 | `IAudioCaptureClient` methodları | aynı dosya | L42–L45 |
| 4 | `IAudioRenderClient` methodları | aynı dosya | L47–L49 |
| 5 | Exclusive/Shared avantaj tablosu (1–3 ms vs 10–40 ms) | aynı dosya | L53–L59 |
| 6 | Hata kodu `0x8889000A` | aynı dosya | L124–L130 |
| 7 | Hata kodu `0x8889000E` | aynı dosya | L124–L130 |
| 8 | Düşük gecikme ayarları (3–6 ms, SetEventHandle, öncelik, affinity) | aynı dosya | L113–L121 |
| 9 | Performans metrikleri (3 ms round-trip) | aynı dosya | L177–L183 |
| 10 | Öncelik sırası #2 WASAPI Exclusive | `_backup/.../k2-surucu/CLAUDE.md` | L27–L34 |
| 11 | Guardrails (blocking/underrun/SR) | aynı dosya | L18–L24 |
| 12 | ASIO round-trip 1.34/1.33 ms | `_backup/.../k2-surucu/asio-drivers.md` (k036 §7 kanıtı) | L160 |

> §4, §5 ve §6'daki **arayüz/method/bayrak adları** official Microsoft WASAPI dokümanı bilgisidir — repo kanıtı **değildir**; hex/değer üretilmemiştir.

---

## §16 Wiki-Bağlantılar

| Hedef | Bağlantı | İlişki |
|-------|----------|--------|
| Hub | [[index]] | K037 indeksi |
| Mod karşılaştırması | [[wasapi-exclusive-shared]] | Üst mod rehberi |
| Shared mod | [[wasapi-shared-mode]] | Exclusive reddi hedefi |
| Buffer/latency | [[wasapi-buffer-latency]] | Period seçimi |
| Format | [[wasapi-format-negotiation]] | §4.3 red yolları |
| Oturum | [[wasapi-audio-session]] | Metering/volume |
| Hata kodları | [[wasapi-hata-kodlari]] | §11 tam tablo |
| Hotplug | [[wasapi-device-hotplug]] | §7.2 kilip kaybı |
| ASIO exclusive | [[../k036-asio-drivers/asio-exclusive-mode]] | Aynı kavram, #1 öncelik |
| ASIO indeksi | [[../k036-asio-drivers/index]] | Komşu modül |

---

## §17 Açık Konular

| # | Konu | Durum |
|---|------|-------|
| 1 | WASAPI kod implementasyonu | ⚠️ VERIFICATION REQUIRED |
| 2 | `AUDCLNT_STREAMFLAGS_EOFIL` bayrak adının kaynağı | ⚠️ VERIFICATION REQUIRED — official set'te yok; kullanıcı onayı |
| 3 | Bayrak hex değerleri (`audclnt.h`) | ⚠️ VERIFICATION REQUIRED |
| 4 | Exclusive round-trip gerçek ölçümü | ⚠️ VERIFICATION REQUIRED |
| 5 | Bit-perfect doğrulama altyapısı (loopback/ölçüm) | ⚠️ VERIFICATION REQUIRED |
| 6 | Period sayısal değeri (kaynakta yok) | ⚠️ VERIFICATION REQUIRED |

---

## §18 Kontrol Listesi

- [x] Frontmatter 7 alan · `version: 4.0.0` · `updated: 2026-10-06`
- [x] Wiki-link yalnız mevcut disk hedeflerine (iç dosyalar + k036)
- [x] Kanıt = gerçek dosya yolu + satır aralığı
- [x] Official doküman bilgisi resmi kaynak olarak işaretlendi
- [x] Doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED`
- [x] Uydurma COM arayüzü/sürüm/hex değeri yok
- [x] `git commit` atılmadı

---

## §19 Değişiklik Geçmişi (append-only)

| Tarih | Sürüm | Değişiklik | Yazar |
|-------|-------|-----------|-------|
| 2026-10-06 | 4.0.0 | İlk üretim — Exclusive mode konu dosyası (multi-md yapılandırması) | Vault Documentation Specialist |
