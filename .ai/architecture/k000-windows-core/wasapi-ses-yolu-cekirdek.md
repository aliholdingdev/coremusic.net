---
title: "WASAPI Ses Yolu Çekirdeği — MMDevice Keşfi, IAudioClient Oturumu ve Fallback Zinciri"
type: architecture
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "K000 Windows Core — SSOT: .ai/architecture/k000-windows-core/"
updated: 2026-10-06
---

# WASAPI Ses Yolu Çekirdeği — MMDevice Keşfi, IAudioClient Oturumu ve Fallback Zinciri

> **K numarası:** K000 — Windows Core (işletim sistemi yüzeyi). Bu belge WASAPI
> (Windows Audio Session API) yolunun **kavramsal çekirdeğini** K0 (platform) ve K2
> (sürücü) sınırında tarif eder. Depoda C++ uygulama kodu **yoktur** (`*.cpp` / `*.h`
> sayısı 0); bu nedenle tüm kod blokları **kavramsal iskelettir** ve üzerinde etiket
> taşır. Ölçülmüş gecikme/latency rakamı bu depoda **üretilmez**.

---

## §1 Kapsam ve Bağlam

### §1.1 Dosya İlişkileri

| Dosya | İlişki |
|---|---|
| [[index]] | K000 klasör indeksi — bu dosyanın üst dizini |
| [[windows-api-yuzeyi]] | Win32/COM API yüzeyi — bu dosyadaki `CoCreateInstance`/`Activate` çağrılarının yüzey referansı |
| [[windows-core-mimari]] | K0 çekirdek mimarisi — WASAPI'nin oturduğu platform katmanı |
| [[win32-olay-dongusu-ve-mesaj-kuyrugu]] | Olay döngüsü — event-driven WASAPI tesliminin Win32 olay kuyruğu ile ilişkisi |

### §1.2 Kapsam Sınırı

| Kapsar | Kapsamaz |
|---|---|
| MMDevice keşif zinciri (`IMMDeviceEnumerator` → `IMMDevice` → `IAudioClient`) | **K1 Donanım** — DAC/ADC, devre, sınıf-D/amf → `[KAPSAM DIŞI]` |
| Shared / exclusive mod ayrımı ve fallback zinciri | **K3 Ses Motoru** DSP algoritmaları → `[KAPSAM DIŞI]` |
| `IAudioClient::Initialize` oturum yaşam döngüsü (kavramsal) | **K5 Veri** / **K12 İzleme** ölçüm altyapısı → `[KAPSAM DIŞI]` (bkz. §14) |
| ASIO → WASAPI yedek ilişkisi (yalnız bağlantı) | ASIO callback ayrıntısı → [[asio-cekirdek-entegrasyonu]] |
| K0 ↔ K2 bağımlılık sınırı (matris kanıtlı) | Gerçek cihaz üzerinde ölçüm → `[NOT PROVIDED]` |

---

## §2 Gömülü Kaynaklar

| # | Dosya | Bu belgede kullanımı |
|---|---|---|
| 1 | `_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\windows-api.md` | §2 WASAPI Entegrasyonu — arayüz adları, `Initialize` çağrısı, mod tablosu |
| 2 | `_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\windows-core.md` | WASAPI kullanım örneği (L213–215) + platform sürümü iddiası (L376) |
| 3 | `_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\README.md` | Tier 1 platform tablosu (L45) · `initializeWASAPI`/`shutdownWASAPI` imzaları (L94–L96) |
| 4 | `_backup\arch-2026-10-06_1057\architecture\katman-baglilik-matrisi.md` | K0 kök · K2 → K1 · K3 → K2 bağımlılık ve yasakları |
| 5 | `.ai\ecosystem\asio-wasapi-rehber.md` | Exclusive/shared eşitlesmesi (§3.2), fallback zinciri (§5.1), callback sözleşmesi (§5.4), 64-bit kısıtı (§3.3) |
| 6 | `.ai\CLAUDE.md` | Latency hedefi satırı (L429) · WASAPI fallback (L692) · terim (L861) |
| 7 | `.ai\brain.md` | K2 tanımı (L229) · ASIO device loss → WASAPI fallback (L873) |
| 8 | `.ai\AGENTS.md` | §17 madde 6 — ASIO device loss → WASAPI fallback |
| 9 | `_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\system-calls.md` | §2 Windows NT API — servis çağrısı bağlamı |

---

## §3 K0 ↔ K2 ↔ K3 Konumu (Matris Kanıtlı)

`katman-baglilik-matrisi.md` kanonik kaynaktır (MEVCUT PROJE GERÇEĞİ):

```text
K1 Donanım  ← (bağımlılık)  K2 Sürücü  ← (bağımlılık)  K3 Ses Motoru
     ↑                            ↑
     └──── K2 → K1 bağımlılığı ───┘   K3 → K2 bağımlılığı

K0 (kök) — tüm katmanların platform kökü
```

| İddia | Değer | Etiket |
|---|---|---|
| K0 kök katmandır; WASAPI bu katmanın platform yüzeyidir | matris K0 satırı | **MEVCUT PROJE GERÇEĞİ** |
| K2 Sürücü → K1 Donanım bağımlıdır | matris `K2 → K1` | **MEVCUT PROJE GERÇEĞİ** |
| K3 → K2 bağımlıdır; K3, K2'nin callback'ine teslim olur | matris `K3 → K2` | **MEVCUT PROJE GERÇEĞİ** |
| WASAPI, K2'nin **ikinci halkasıdır** (ASIO birinci) | `asio-wasapi-rehber.md` §5.1 | **MEVCUT PROJE GERÇEĞİ** |
| K12 → K0 bağımlılığı **yasak**; K6 → K0 bağımlılığı **yasak** | matris yasak sütunu | **MEVCUT PROJE GERÇEĞİ** |

> **WASAPI'nin katmanı:** WASAPI bir **API'dir**, sürücü değil; buna rağmen CoreMusic
> sözlüğünde K2 kapsamına girer (`brain.md` L229: `| **K2** | Sürücü | ASIO, WASAPI,
> ALSA, PipeWire, I2S |`). Gerekçe: K2, donanım-yazılım köprüsüdür ve WASAPI bu
> köprüyü Windows ses motoru üzerinden kurar. **Çelişki değil, kapsam genişletmesi**
> olarak kaydedilir.

---

## §4 Kavramsal Zincir (Uçtan Uca)

```text
[Çekirdek K3 ses motoru]  (bu belge kapsamı DIŞI)
         │  process(io, channels, n)  ← ortak K2→K3 arayüzü (asio-wasapi-rehber §5.4)
         ▼
[K2 sürücü sözleşmesi — WASAPI halkası]
   CoCreateInstance(MMDeviceEnumerator)
     → IMMDeviceEnumerator::GetDefaultAudioEndpoint(eRender, eConsole)
       → IMMDevice
         → IMMDevice::Activate(IAudioClient)
           → IAudioClient::Initialize(shared | exclusive, flags, süre, format)
             → IAudioClient::GetService(IAudioRenderClient)
               → olay tetiklenir → GetBuffer / ReleaseBuffer
         ▼
[Windows ses motoru (shared)] veya [doğrudan sürücü (exclusive)]
         ▼
[K1 Donanım]  ← KAPSAM DIŞI (donanım devresi, DAC/ADC)
```

**Zaman çizelgesi etiketi:** Yukarıdaki oklar **çağrı sırasıdır**, **süre/id** iddiası
değildir. Hiçbir ok üzerinde ms/µs değeri yazılmamıştır; süre iddiası bu belgede
`[NOT PROVIDED]` olarak taşınır.

---

## §5 MMDevice Keşif Katmanı

### §5.1 Arayüz Zinciri (Depo Kanıtı)

`windows-api.md` §2.2 (MEVCUT PROJE GERÇEĞİ — kod parçası depoda mevcut):

```cpp
// KAVRAMSAL İSKELET — kaynak: _backup/.../k0-isletim-sistemi/windows-api.md §2.2
// Depoda derlenebilir C++ dosyası YOKTUR (*.cpp/*.h = 0).
IMMDeviceEnumerator* pEnumerator = nullptr;
IMMDevice*            pDevice    = nullptr;
IAudioClient*         pAudioClient = nullptr;
IAudioRenderClient*   pRenderClient = nullptr;

hr = CoCreateInstance(
    __uuidof(MMDeviceEnumerator),
    NULL, CLSCTX_ALL,
    __uuidof(IMMDeviceEnumerator),
    (void**)&pEnumerator);

hr = pEnumerator->GetDefaultAudioEndpoint(
    eRender, eConsole, &pDevice);

hr = pDevice->Activate(
    __uuidof(IAudioClient),
    CLSCTX_ALL, NULL,
    (void**)&pAudioClient);
```

### §5.2 Adım Tablosu

| # | Çağrı | Dönüş | Etiket |
|---|---|---|---|
| 1 | `CoInitializeEx` | COM başlatma | **MEVCUT PROJE GERÇEĞİ** (`README.md` L119) |
| 2 | `CoCreateInstance(__uuidof(MMDeviceEnumerator), …)` | `IMMDeviceEnumerator*` | **MEVCUT PROJE GERÇEĞİ** |
| 3 | `GetDefaultAudioEndpoint(eRender, eConsole, &pDevice)` | `IMMDevice*` | **MEVCUT PROJE GERÇEĞİ** |
| 4 | `Activate(__uuidof(IAudioClient), …)` | `IAudioClient*` | **MEVCUT PROJE GERÇEĞİ** |
| 5 | `GetMixFormat(&pwfx)` | `WAVEFORMATEX*` | **MEVCUT PROJE GERÇEĞİ** |
| 6 | `Initialize(...)` | `HRESULT` | **MEVCUT PROJE GERÇEĞİ** |
| 7 | `GetService(__uuidof(IAudioRenderClient), …)` | `IAudioRenderClient*` | **MEVCUT PROJE GERÇEĞİ** |

### §5.3 Endpoint Keşfi ve Değişimi

- **Varsayılan endpoint:** `eRender` (çıkış) + `eConsole` (oturum) çifti depoda
  kanıtlıdır (windows-api.md L118–L120).
- **Cihaz değişimi / tak-çalıştır:** `IMMNotificationClient` benzeri bildirim
  arayüzü **bu depoda geçmiyor** → `⚠️ VERIFICATION REQUIRED` (kanıt yok; uydurulmadı).
- **Çoklu cihaz seçimi:** yalnızca varsayılan endpoint kodu mevcuttur; enumerated
  liste seçimi `[NOT PROVIDED]`.

---

## §6 IAudioClient Oturum Yaşam Döngüsü

### §6.1 Aşamalar

```text
ADAY OTURUM (kavramsal)
  Activate ──> Initialize ──> [Start] ──> olay döngüsü ──> [Stop] ──> Release
                              ▲                              │
                              └──────── Reset (yeniden başlat) ──────┘
```

| Aşama | Arayüz/uygulama | Etiket |
|---|---|---|
| Oluşturma | `IMMDevice::Activate` | **MEVCUT PROJE GERÇEĞİ** |
| Yapılandırma | `IAudioClient::Initialize` | **MEVCUT PROJE GERÇEĞİ** |
| Başlatma (`Start`) | `IAudioClient` üyesi | `⚠️ VERIFICATION REQUIRED` — çağrı adı depoda geçmiyor |
| Durdurma (`Stop`) | `IAudioClient` üyesi | `⚠️ VERIFICATION REQUIRED` — çağrı adı depoda geçmiyor |
| Serbest bırakma | `Release` (COM) | **MEVCUT PROJE GERÇEĞİ** (`README.md` shutdown akışı) |

> **Kural:** `Start`/`Stop` çağrısı gerçek COM yaşam döngüsü gereğidir, ancak
> **bu depoda hiçbir yerde yazılmamıştır**; bu nedenle tabloda işaretle taşınır,
> sessizce "bilinen" sayılmaz.

### §6.2 `Initialize` Çağrısı (Kaynak Birebir)

`windows-api.md` L132–L139 (MEVCUT PROJE GERÇEĞİ — metin birebir):

```cpp
hr = pAudioClient->Initialize(
    AUDCLNT_SHAREMODE_EXCLUSIVE,  // or AUDCLNT_SHAREMODE_SHARED
    AUDCLNT_STREAMFLAGS_EVENTCALLBACK,
    10 * 1000 * 10,  // 100ms buffer   ← ⚠️ bkz. §8.1 ÇELİŞKİ notu
    0,
    pwfx,
    NULL);
```

**Parametre okuması:**

| Parametre | Değer | Anlam |
|---|---|---|
| 1. | `AUDCLNT_SHAREMODE_EXCLUSIVE` | Cihazı tek istemciye devral |
| 2. | `AUDCLNT_STREAMFLAGS_EVENTCALLBACK` | Olay tabanlı teslim etkin |
| 3. | `10 * 1000 * 10` | Referans süre (`hns` = 100 ns birimli) — **yorum satırı ile aritmetik uyumsuz, §8.1** |
| 4. | `0` | Gecikme (period) — `0` = varsayılan |
| 5. | `pwfx` | `GetMixFormat` çıktısı |
| 6. | `NULL` | Olay kolu (aleyhine doldurulmamış) |

---

## §7 Shared vs Exclusive Mod

### §7.1 Karşılaştırma (Kaynak: `asio-wasapi-rehber.md` §3.2 — exa doğrulaması 2026-09-24)

| Kriter | WASAPI Exclusive | WASAPI Shared |
|---|---|---|
| Cihaz erişimi | Uygulama devralır (tek istemci) | Sistem mix'i (çok istemci) |
| Sistem mix'i | Yok | Var |
| Çakışma riski | Diğer uygulamalar susturulur | Yok |
| CoreMusic rolü | **2. tercih / fallback** | **3. tercih (uyumluluk)** |

Etiket: **MEVCUT PROJE GERÇEĞİ** — `.ai/ecosystem/asio-wasapi-rehber.md` §3.2; belge
kendisi "Doğrulanmış olgu (2026-09-24 exa)" etiketini taşır (ARAŞTIRMA REFERANSI
temelli proje kararı).

### §7.2 Mod Seçimi Hiyerarşisi (§5.1 zinciri)

```text
1) ASIO dene (64-bit sürücü)  ──ok──> ASIO yolu (en düşük gecikme)
   │ yok / 32-bit sürücü
2) WASAPI exclusive           ──ok──> mix yok, cihaz kilitli
   │ paylaşımlı kullanım gerekiyorsa
3) WASAPI shared              ──> uyumluluk yolu
```

- **Kaynak:** `asio-wasapi-rehber.md` §5.1 (MEVCUT PROJE GERÇEĞİ).
- **Çapraz kanıt:** `.ai/AGENTS.md` §17 madde 6 — `ASIO device loss → WASAPI fallback`.
- **Çapraz kanıt:** `.ai/brain.md` L873 — `ASIO Device Loss | USB kopması | WASAPI fallback → Null Output`.
- **Çapraz kanıt:** `.ai/CLAUDE.md` L692 — `USB cihaz çıkarma | WASAPI fallback | [[ADR-017-dsp-hardware-mode]]`.

### §7.3 64-Bit Kısıtının WASAPI'ye Etkisi

`asio-wasapi-rehber.md` §3.3 (MEVCUT PROJE GERÇEĞİ): Windows'un evrensel yerleşik
ASIO desteği **yalnız 64-bit ASIO sürücülerini** işletir; 32-bit ASIO sürücüsü
desteklenmez. Sonuç: 32-bit ASIO sürücülü eski donanımda zincir **2. halkaya
(WASAPI exclusive) düşer**. CoreMusic K0 hedefi bu nedenle 64-bit'tir
(`§5.3 ADR şablonu` — durum: **ÖNERİ**, henüz onaylı ADR değil).

---

## §8 Buffer Zamanlama ve 100ns Birimi

### §8.1 Birim Okuması

WASAPI `Initialize`'ın 3. parametresi **100 nanosaniyelik (`hns`) tam sayı birimli**
referans süredir (bu birimlendirme, kaynak kodun `10 * 1000 * 10` ifadesinin
yorumuyla birlikte okunarak çıkarılabilir; **birebir belge tanımı bu depoda yok** →
`⚠️ VERIFICATION REQUIRED`).

| İfade | Aritmetik | Yorum satırı iddiası | Durum |
|---|---|---|---|
| `10 * 1000 * 10` | 100.000 | `// 100ms buffer` | **UYUMSUZ** — 100.000 × 100 ns = 10 ms, 100 ms değil |

> Bu uyumsuzluk `## ÇELİŞKİ / DOĞRULAMA` bölümünde 1 numaralı madde olarak kayıtlıdır.
> **Bu belge düzeltmez** — kaynak dosya salt-okunurdur; yalnızca raporlar.

### §8.2 Süre → Örnek Sayısı Dönüşümü (Kavramsal)

```text
süre(sn)  = hns × 100e-9
örnek sayısı = süre(sn) × örnekleme hızı (Hz)

ÖRNEK HESAP (kavramsal, ölçüm DEĞİL):
  48.000 Hz + 10 ms  → 480 örnek
  48.000 Hz + 100 ms → 4.800 örnek
```

| Değer | Durum |
|---|---|
| Örnekleme hızı (varsayılan) | `[NOT PROVIDED]` — depoda sabit yok |
| Hedef buffer boyutu | `[NOT PROVIDED]` — `asio-wasapi-rehber.md` §3.8 madde 3: "p95 gecikme hedefi (ms) henüz yok" |
| Ölçülmüş gecikme | `[NOT PROVIDED]` — gerçek ölçüm yok |

### §8.3 Hedef mi, Ölçüm mü?

`.ai/CLAUDE.md` L429: `| Latency Hedefi | <10ms (ASIO), <20ms (WASAPI) |` →
**hedeftir, ölçüm değildir** (MEVCUT PROJE GERÇEĞİ, etiket: hedef).
`asio-wasapi-rehber.md` §6.4 ise ölçümü "gerçek donanımda gecikme ölçümü" olarak
**bekleyen adım** ilan eder. İkisi birlikte okunduğunda: **hedef var, ölçüm yok.**

---

## §9 Event-Driven Teslim

### §9.1 Olay Kolu

```cpp
// KAVRAMSAL — kaynak: windows-api.md Initialize bayrağı
AUDCLNT_STREAMFLAGS_EVENTCALLBACK   // olay tabanlı teslim etkin
```

| Öğe | Depo Kanıtı | Etiket |
|---|---|---|
| `AUDCLNT_STREAMFLAGS_EVENTCALLBACK` bayrağı | `windows-api.md` L134 | **MEVCUT PROJE GERÇEĞİ** |
| `IAudioClient event` → K2 callback | `asio-wasapi-rehber.md` §5.4 | **MEVCUT PROJE GERÇEĞİ** |
| `SetEventHandle` çağrısı | — | `⚠️ VERIFICATION REQUIRED` — depoda geçmiyor |
| `GetCurrentPadding` çağrısı | — | `⚠️ VERIFICATION REQUIRED` — depoda geçmiyor |

### §9.2 Win32 Olay Kuyruğu ile İlişki

WASAPI event-driven teslimi, [[win32-olay-dongusu-ve-mesaj-kuyrugu]] içinde anlatılan
**olay nesnesi + bekleyen thread** modeline yaslanır. İki önemli sınır:

1. **Kuyruk bağımlılığı:** `WM_*` mesaj kuyruğu (UI thread) ile ses olayı aynı
   mekanizma değildir; ses callback'i UI mesaj döngüsünü **beklemez**.
2. **Kanıt sınırı:** Bu ilişkinin bu depoda **çözümlenmiş kodu yoktur** →
   `⚠️ VERIFICATION REQUIRED`.

### §9.3 Çekirdek Zamanlama Sorumlulukları

| Sorumluluk | Katman | Etiket |
|---|---|---|
| Olay nesnesini tetikleme | Windows ses motoru (OS) | ARAŞTIRMA REFERANSI (Microsoft CoreAudio — `asio-wasapi-rehber.md` §7 madde 2) |
| Olayı bekleyip buffer doldurma | K2 | **MEVCUT PROJE GERÇEĞİ** (§5.4 sözleşme) |
| DSP işini yapma | K3 | `[KAPSAM DIŞI]` |
| xrun (underflow/overflow) sayacı | K2 → K14 metriği | **MEVCUT PROJE GERÇEĞİ** (`asio-wasapi-rehber.md` §3.4, §5.1) |

> **K14 notu:** `asio-wasapi-rehber.md` xrun sayacını **K14**'e verir; buna karşılık
> `katman-baglilik-matrisi.md` içinde K14 → K0 bağımlılığı **taşımaz** ve K12 → K0
> yasaktır. Metrik yolları bu belgenin **kapsamı dışındadır** → `[KAPSAM DIŞI]`
> (detay: [[windows-performans-ve-gozlemlenebilirlik]]).

---

## §10 Format Müzakeresi

### §10.1 Adımlar

| # | Adım | Arayüz | Etiket |
|---|---|---|---|
| 1 | Karışım formatını al | `IAudioClient::GetMixFormat` | **MEVCUT PROJE GERÇEĞİ** |
| 2 | Formatı sorgula | `IsFormatSupported` | **MEVCUT PROJE GERÇEĞİ** (`asio-wasapi-rehber.md` §2.1) |
| 3 | Formatı `Initialize`'a ver | `WAVEFORMATEX*` | **MEVCUT PROJE GERÇEĞİ** |

### §10.2 Format Tablosu (Kaynak: `windows-api.md` §1)

| Alan | CoreMusic hedefi | ASIO | DirectSound |
|---|---|---|---|
| Bit Derinliği | 32-bit float | 16 | 32 |
| Kanal | 8 (8.1) | 1 | 16 |
| Gecikme (iddia) | ~10.67ms | ~1.33ms | ~21.33ms |

> **⚠️ VERIFICATION REQUIRED:** Bu satırlar **eski belge iddiasıdır** — gerçek donanım
> üzerinde **ölçülmemiştir**. Değerler bu belgede hedef/iddia olarak taşınır; kesin
> değer olarak kullanılmaz. `asio-wasapi-rehber.md` §6.2 risk satırı da aynı uyarıyı
> taşır: "Gecikme hedefi ölçülmeden yazılır → Orta".

---

## §11 Render Besleme Döngüsü (Kavramsal)

```cpp
// KAVRAMSAL İSKELET — depoda derlenebilir karşılığı YOK
// Kaynak: windows-api.md §2.2 GetService(IAudioRenderClient)
//
// olay geldiğinde:
//   pAudioClient->GetBuffer(frameCount, &pData);      // ⚠️ VERIFICATION REQUIRED
//   doldur(pData);                                    // K3 teslimi (kapsam dışı)
//   pAudioClient->ReleaseBuffer(frameCount, 0);       // ⚠️ VERIFICATION REQUIRED
```

| Çağrı | Durum |
|---|---|
| `GetService(__uuidof(IAudioRenderClient), …)` | **MEVCUT PROJE GERÇEĞİ** |
| `GetBuffer` / `ReleaseBuffer` | `⚠️ VERIFICATION REQUIRED` — bu depoda geçmiyor, iskelet yalnız şemadır |
| Buffer doldurma işi | `[KAPSAM DIŞI]` (K3) |

**Kural:** Yukarıdaki `⚠️` işaretli satırlar **kullanıma hazır kod değildir**; şema
amacıyla buradadırlar ve gerçekleştirme öncesi doğrulanmalıdır.

---

## §12 Cihaz Kaybı ve Hata Yolu

| Senaryo | Eylem | Kaynak | Etiket |
|---|---|---|---|
| ASIO cihaz kaybı (USB kopması) | WASAPI fallback → Null Output | `.ai/brain.md` L873 | **MEVCUT PROJE GERÇEĞİ** |
| ASIO hiç yok / 32-bit sürücü | WASAPI exclusive → shared | `asio-wasapi-rehber.md` §5.1 | **MEVCUT PROJE GERÇEĞİ** |
| WASAPI `Initialize` başarısız (`HRESULT`) | Hata kodu okuma + üst katmana bildirim | — | `⚠️ VERIFICATION REQUIRED` — işlenme kodu yok |
| Endpoint kaybolma (cihaz çıkarıldı) | Yeniden keşif döngüsü | — | `[NOT PROVIDED]` |
| **Fallback zinciri şemasının tamamı** | — | `asio-wasapi-rehber.md` §2.2: "exclusive/shared fallback zinciri şeması **yok**" | **eksik (belge kendi beyanı)** |

---

## §13 Gömülü Doğrulanabilir İddialar Tablosu

| # | İddia | Kanıt (dosya + satır) | Etiket |
|---|---|---|---|
| 1 | `initializeWASAPI()` / `shutdownWASAPI()` statik imzaları tanımlanmıştır | `README.md` L94–L96 | **MEVCUT PROJE GERÇEĞİ** |
| 2 | `CoInitializeEx` COM başlatması "WASAPI için" olarak anılır | `README.md` L119 | **MEVCUT PROJE GERÇEĞİ** |
| 3 | WASAPI örneği `IAudioClient` + `IMMDeviceEnumerator` kullanır | `windows-core.md` L213–L215 | **MEVCUT PROJE GERÇEĞİ** |
| 4 | Mod tablosu: Shared ~15ms · Exclusive ~3ms · Loopback ~15ms | `windows-api.md` L95–L99 | **ESKİ BELGE İDDİASI — doğrulanmadı** |
| 5 | CoreMusic K2 sıralaması: ASIO → WASAPI exclusive → WASAPI shared | `asio-wasapi-rehber.md` §5.1 | **MEVCUT PROJE GERÇEĞİ** |
| 6 | Latency hedefi `<20ms (WASAPI)` | `.ai/CLAUDE.md` L429 | **MEVCUT PROJE GERÇEĞİ (hedef)** |
| 7 | WASAPI, Windows ses oturum yönetimidir | `.ai/CLAUDE.md` L861 | **MEVCUT PROJE GERÇEĞİ** |
| 8 | K2 tanımı: `ASIO, WASAPI, ALSA, PipeWire, I2S` | `.ai/brain.md` L229 | **MEVCUT PROJE GERÇEĞİ** |
| 9 | P95 gecikme hedefi henüz yok | `asio-wasapi-rehber.md` §3.8 madde 3 | **MEVCUT PROJE GERÇEĞİ (eksik beyanı)** |
| 10 | Bu depoda derlenebilir WASAPI kodu var | `*.cpp`/`*.h` sayısı 0 | **OLUMSUZ — kanıt yok** |

---

## §14 Bağlantı Haritası (Kapsam Ayrımı)

| Konu | Giden dosya | Kapsam |
|---|---|---|
| ASIO callback yaşam döngüsü | [[asio-cekirdek-entegrasyonu]] | K000 |
| Win32 olay kuyruğu, `WM_*` döngüsü | [[win32-olay-dongusu-ve-mesaj-kuyrugu]] | K000 |
| Token/ACL/privilege kısıtları (`SeLockMemoryPrivilege`) | [[windows-guvenlik-ve-olcullu-kisitlar]] | K000 |
| ETW, QPC, xrun metrik yolları | [[windows-performans-ve-gozlemlenebilirlik]] | K000 |
| DAC/ADC, sınıf-D, XMOS | `[KAPSAM DIŞI]` → K1 | K1 |
| 18 DB / PHP pipeline | `[KAPSAM DIŞI]` → K5, K8 | K5/K8 |
| K12 → K0 bağımlılık yasağı (metrik export) | `[KAPSAM DIŞI]` → K12 | K12 |

---

## §15 Gerçekleştirme Kontrol Listesi (Kavramsal)

> **Uyarı:** Bu liste **kod tarifi değildir**; her satır `⚠️ VERIFICATION REQUIRED`
> işaretli maddeler içerir ve gerçek donanım üzerinde doğrulanmak zorundadır.

| # | Kontrol | Beklenen durum | Kanıt durumu |
|---|---|---|---|
| 1 | COM başlatması yapıldı mı? | `CoInitializeEx` çağrısı var | **MEVCUT PROJE GERÇEĞİ** (`README.md` L119) |
| 2 | Varsayılan endpoint alındı mı? | `GetDefaultAudioEndpoint(eRender, eConsole)` | **MEVCUT PROJE GERÇEĞİ** |
| 3 | `Activate` ile `IAudioClient` alındı mı? | işaretçi geçerli | **MEVCUT PROJE GERÇEĞİ** |
| 4 | `GetMixFormat` okundu mu? | `WAVEFORMATEX*` dolu | **MEVCUT PROJE GERÇEĞİ** |
| 5 | Mod seçimi bilinçli mi? (shared/exclusive) | Karar belgede yazılı | **MEVCUT PROJE GERÇEĞİ** (§7.2) |
| 6 | Buffer süresi doğru yorumlandı mı? | `hns` birimi + §8.1 çelişkisi çözüldü | `⚠️ VERIFICATION REQUIRED` |
| 7 | Olay bayrağı etkin mi? | `AUDCLNT_STREAMFLAGS_EVENTCALLBACK` | **MEVCUT PROJE GERÇEĞİ** |
| 8 | `Start`/`Stop` yaşam döngüsü yazılı mı? | Çağrı adları doğrulanmış | `⚠️ VERIFICATION REQUIRED` |
| 9 | Fallback zinciri test edildi mi? | ASIO yokken exclusive devrede | `⚠️ VERIFICATION REQUIRED` |
| 10 | Ölçüm yapıldı mı? | Gerçek donanımda p95 gecikme | `[NOT PROVIDED]` |

**KPI:** 10 maddenin 10'u `MEVCUT PROJE GERÇEĞİ` ise gerçekleştirme hazırdır;
`⚠️` maddeleri açıkken üretim kodu olarak sunulmaz.

---

## §16 Sık Yapılan Hatalar (Depo Kanıtlı)

| # | Hata | Sonuç | Kanıt |
|---|---|---|---|
| 1 | `10 * 1000 * 10` ifadesini 100 ms sanmak | 10 kat kısa buffer → xrun riski | `windows-api.md` L135 (§8.1) |
| 2 | Exclusive modda paylaşımı yok saymak | Diğer uygulamalar susturulur | `asio-wasapi-rehber.md` §3.2 |
| 3 | 32-bit hedefte ASIO beklemek | Evrensel ASIO yolu kapalı → exclusive'e düşer | `asio-wasapi-rehber.md` §3.3 |
| 4 | Callback içinde bloklama/tahsis | Gerçek zamanlı sözleşme ihlali | `asio-wasapi-rehber.md` §5.4 ("bloklama YOK") |
| 5 | Ölçüm olmadan gecikme iddiası yazmak | Halüsinasyon | `asio-wasapi-rehber.md` §6.2 risk satırı |

---

## §17 İddia → Kanıt Eşiği

```text
İddia yazılacak
  → dosya yolu + satır var mı?
      EVET  → etiket: MEVCUT PROJE GERÇEĞİ
      HAYIR → dış kaynak (exa/Microsoft) ile doğrulandı mı?
                  EVET  → etiket: ARAŞTIRMA REFERANSI (+ tarih)
                  HAYIR → [NOT PROVIDED] veya ⚠️ VERIFICATION REQUIRED
  → sayısal değer (ms/µs/sürüm) mi?
      EVET  → ölçüm yoksa ESKİ BELGE İDDİASI olarak işaretle
```

**Bu belgede üretilen yeni sayısal değer yoktur.** Tüm sayısal satırlar kaynak
belgelerden **olduğu gibi** alınmış ve etiketlenmiştir.

---

## ÇELİŞKİ / DOĞRULAMA

> **Kural:** Bu bölümde en fazla **5** madde taşınır (dosya toplamı). Her madde
> iki kaynağın gerçek satırını gösterir; **düzeltme yapılmaz**, yalnızca raporlanır.

**1 — `Initialize` buffer süresi aritmetik uyumsuzluğu (2 madde → 1 kayıt)**

| Kaynak | Satır | İddia |
|---|---|---|
| `_backup\...\k0-isletim-sistemi\windows-api.md` | L135 | `10 * 1000 * 10,  // 100ms buffer` |
| Aynı satırın aritmetiği | — | `100.000 × 100 ns = 10 ms` (≠ 100 ms) |

> **Etki:** Örnek kod/iskelet kopyalanırsa buffer süresi 10 kat kısa olur.
> **Durum:** `⚠️ VERIFICATION REQUIRED` — hangisinin kastedildiği (10 ms mi, 100 ms mi)
> depoda çözülemiyor. Bu belge düzeltmez.

**2 — Desteklenen Windows sürümü aralığı**

| Kaynak | Satır | İddia |
|---|---|---|
| `_backup\...\k0-isletim-sistemi\README.md` | L45 | `Tier 1 | Windows (XP-11, Server 2012 R2+)` |
| `.ai\CLAUDE.md` | L347 | `Tier 1 (Primary) | Windows (XP-11, Server 2012 R2+)` |
| `_backup\...\k0-isletim-sistemi\windows-core.md` | L376 | `Windows 10/11 (1809 ve üzeri)` |

> **Etki:** WASAPI sürüm desteği bu iki aralıkta çelişir (XP ↔ 1809).
> **Durum:** `⚠️ VERIFICATION REQUIRED` — hangi sürümün hedef olduğu bir karar
> (ADR) ile sabitlenmemiş. Bu belge her iki iddiayı da etiketli taşır.

---

## Kaynaklar

Okunan gerçek dosya yolları (bu belgeye gömülü kanıt):

1. `C:\www\coremusic.net\_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\windows-api.md`
2. `C:\www\coremusic.net\_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\windows-core.md`
3. `C:\www\coremusic.net\_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\README.md`
4. `C:\www\coremusic.net\_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\system-calls.md`
5. `C:\www\coremusic.net\_backup\arch-2026-10-06_1057\architecture\katman-baglilik-matrisi.md`
6. `C:\www\coremusic.net\.ai\ecosystem\asio-wasapi-rehber.md`
7. `C:\www\coremusic.net\.ai\CLAUDE.md`
8. `C:\www\coremusic.net\.ai\brain.md`
9. `C:\www\coremusic.net\.ai\AGENTS.md`
10. `C:\www\coremusic.net\.ai\architecture\k000-windows-core\index.md` (salt-okunur stil referansı)
11. `C:\www\coremusic.net\.ai\architecture\k000-windows-core\windows-api-yuzeyi.md` (salt-okunur stil referansı)
12. `C:\www\coremusic.net\.ai\architecture\k000-windows-core\windows-core-mimari.md` (salt-okunur stil referansı)

**Doğrulama durumu:** Bu belgede **yeni sürüm/ölçüm/latency rakamı üretilmemiştir**.
Depoda `*.cpp`/`*.h` sayısı 0 olduğundan tüm kod blokları **kavramsal iskelettir**;
gerçekleştirme öncesi `⚠️ VERIFICATION REQUIRED` işaretli satırlar doğrulanmalıdır.
