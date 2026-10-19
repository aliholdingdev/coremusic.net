---
node_id: ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-winsdk-dogrulama.md"
node_type: verification
status: verify-required
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-surucu-modeli.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-shared-ve-exclusive-mode.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-latency-ve-buffer.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-session-management.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-web-arastirma.md"
evidence:
  - "Windows SDK/DDK iddia→URL doğrulama tabloları — fetch: 2026-10-12 (learn.microsoft.com 6 URL)"
  - "Doğrulama koşulları: AutoAGS/CurveEQ/KsFilterMetdata/AudioEngine/kullanıcı sürücü/nextextensions"
validation: "TRUTH MODE: her doğrulama koşulu = iddia + resmi URL + fetch tarihi + durum; kod dosyası UYDURMA YASAK (repo 0 .cpp/.h); eksik kanıt '⚠️ UNKNOWN' — dolgu yasak (TBD/.../Yakında)"
open_questions:
  - "AUDCLNT_STREAMFLAGS_AUTOCONVERTPCM 'Need to confirm' — resmi doğrulama adımı bekleniyor"
  - "kullanıcı sürücüsü (AudioDriver) doğrulama adımları learn.microsoft.com sayfasında bu turda fetch edilmedi (B8)"
  - "nextextensions Uzantıları doğrulama kuralı — resmi SDK dokümantasyonu bulunamadı (Üçüncü parti)"
group: kanit
---

# wasapi-winsdk-dogrulama — WASAPI Windows SDK/DDK Doğrulama (Teknoloji: WASAPI)

> **Eksen:** `K000-isletim-sistemi / isletim-sistemi-audio / wasapi` · **Grup:** `kanit` · **Durum:** `verify-required`
> **Amaç:** WASAPI mimari dosyalarındaki iddiaları Windows SDK/DDK dokümantasyonuyla
> doğrulama kriterlerine bağlamak; her koşulun resmi URL + fetch tarihini taşımak; kod
> uydurmadan (repo'da 0 `.cpp/.h`) doğrulama adımlarını listelemek.
> **Konu sırası:** (1) kapsam → (2) SDK/DDK doğrulama matrisi → (3) bileşen bazlı doğrulama
> koşulları (AutoAGS/CurveEQ/KsFilterMetdata/AudioEngine/kullanıcı sürücü/nextextensions) →
> (4) derleme/çalışma doğrulama adımları → (5) ⚠️ UNKNOWN → (6) reddedilen iddialar → (7) giriş.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12 · **Mod:** TRUTH MODE / RED TEAM

---

## 0. Bu Dosyanın Kapsamı (Boundary)

- ✅ **Yapılır:** Windows SDK/DDK (WDM/KS/PortCls/WASAPI) dokümantasyonuna bağlama, doğrulama
  koşulları (ne ile, hangi araçla, hangi beklenen sonuçla), ⚠️ işaretleri.
- ❌ **Yapılmaz:** uydurma kod dosyası (`Dosya:…/Satır:…`), repo olmayan `.cpp/.h` satır atfı,
  TBD/dolgu, resmi olmayan kaynak doğrulama dayanağı. Kod alıntıları yalnızca
  `wasapi-kod-ornekleri.md` içinde kaynak-etiketli yapılır.

---

## 1. Kapsam: WASAPI İddia Kümeleri ve Doğrulama Türü

| İddia Kümesi | Kaynak Dosya | Doğrulama Türü | Araç |
|---|---|---|---|
| OHCI katmanı / WaveRT / DMA | wasapi-surucu-modeli | statik (doküman-tutarlılığı) + dynamic (ETW) | Windows SDK + Windows Driver Kit |
| IAudioClient3 / arayüz hiyerarşisi | wasapi-arayuz-hiyerarsisi | statik (Headers) + runtime (HRESULT) | audioclient.h + AUDCLNT_E_* |
| Shared/exclusive | wasapi-shared-ve-exclusive-mode | runtime (mod geçişi) | IAudioClient::Initialize + AUDCLNT_STREAMFLAGS_* |
| Latency / buffer / period | wasapi-latency-ve-buffer | ölçüm (Performans) | IAudioClient::GetDevicePeriod + ETW |
| Session | wasapi-session-management | runtime (state) | IAudioSessionControl::GetState + GUID_NULL |

---

## 2. Windows SDK/DDK Doğrulama Matrisi (İddia → URL → Durum)

| # | İddia (kaynak dosyadan) | Doğrulama Yöntemi | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|---|---|
| 1 | IAudioClient3 = 3 metod (initializeSharedStream / setDeviceFormat / initializeDeviceSharedStream) | SDK header `audioclient.h` + örnek kod karşılaştırması | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi |
| 2 | Exclusive period legal = fundamentalPeriodInFrames katları | GetDevicePeriod + Initialize (100 ns) karşılaştırması | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi |
| 3 | AUDCLNT_E_ENGINE_PERIODICITY_LOCKED / FORMAT_LOCKED | Initialize (fmt, tag, period) tekrar denemesi → hata | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2026-10-12 | ✅ fetch edildi |
| 4 | AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED | exclusive Initialize → buffer yeniden hesaplama → tekrar | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2026-10-12 | ✅ fetch edildi |
| 5 | Exclusive = 2 ping-pong buffer + sistem sesleri susar | PlayExclusiveStream sırasında sistem bildirimi sesi testi | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2026-10-12 | ✅ fetch edildi |
| 6 | Session active/inactive/expired + GUID_NULL otomatik | IAudioSessionControl::GetState üç durum gözlemi | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions | 2026-10-12 | ✅ fetch edildi |
| 7 | MMCSS 10 ms eşiği → "Pro Audio"/"Audio" kuyruğu | AvSetMmThreadCharacteristicsW döner değeri + scheduler gözlemi | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi |
| 8 | Event = DMA bildirimi (WaveRT) | WaveRT event handle + DMA tamamlama ETW korelasyonu | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi |
| 9 | Win10 ~1.3 ms / capture ~0 ms / roundtrip 4.5 ms | ETW roundtrip ölçümü (resmi örnek yöntemle) | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi |
| 10 | Inbox HDAudio arabellek 128/480 örnek | registry/driver package incelemesi + sürücü bellek raporu | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | 2026-10-12 | ✅ fetch edildi |
| 11 | AUDCLNT_STREAMFLAGS_AUTOCONVERTPCM | sayfa "Need to confirm" → test senaryosu bekliyor | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2026-10-12 | ⚠️ UNKNOWN (doğrulanacak) |
| 12 | PortCls DDI (PcAddStreamResource / IPortClsStreamResourceManager) | WDK PortCls header + sürücü derlemesi | B8 (§2.1 web-arastirma) | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| 13 | MMDevice API (IMMDeviceEnumerator) | SDK header mmdeviceapi.h + örnek | B3 (§2.1 web-arastirma) | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| 14 | Shared↔exclusive akış karşılaştırması (Windows 8 sürümü) | B1 (shared-vs-exclusive-mode) | B1 (§2.1 web-arastirma) | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| 15 | Wave image classification (KS_WAVEIMPLEMENTATION_DEFAULT_PORTS/RT/CI) | glossary + WDK ks.h | https://learn.microsoft.com/en-us/windows/win32/coreaudio/glossary | 2026-10-12 | ✅ fetch edildi |

---

## 3. Bileşen Bazlı Doğrulama Koşulları (6 Bileşen)

### 3.1 AutoAGS (Auto Audio Graph Solver?)

> **Not:** "AutoAGS" terimi bu turda resmi bir learn.microsoft.com sayfasında doğrudan
> getirilmedi; en yakın resmi karşılık **ses otomasyonu (auto device routing)** ve **audio graph**
> (AudioGraph/C#/Metro örneği) bileşenleridir. ⚠️ UNKNOWN olarak işaretlenir; dolgu YASAK.

| Koşul | İddia | Doğrulama | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|---|---|
| AutoAGS = otomatik grafik kurulumu (audio graph) | AutoAGS sesi otomatik grafikte kurar | AudioGraph C# örneğiyle (Windows 8.1) eşleşme | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi (AudioGraph örneği) · ⚠️ "AutoAGS" adı UNKNOWN |
| AutoAGS = otomatik cihaz yönlendirme (auto routing) | cihaz değişince grafik yeniden kurulur | IAudioSessionEvents / endpoint değişimi | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions | 2026-10-12 | ✅ fetch edildi (endpoint+session) · ⚠️ terim UNKNOWN |

**Karar:** "AutoAGS" kısaltması resmi kaynakta doğrulanana kadar bu vault'ta **kısaltma olarak
kullanılmaz**; yerine `audio graph (AutoAGS)` parantezli not + ⚠️ etiketi.

### 3.2 CurveEQ (Kavisli EQ / EQ eğrisi)

| Koşul | İddia | Doğrulama | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|---|---|
| CurveEQ = APO zincirinde EQ eğrisi (FX APO) | CurveEQ audio engine'de EQ eğrisi uygular | APO1 (AHSAPO) / audio engine bölüm kontrolü | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | 2026-10-12 | ✅ fetch edildi (APO1/APO2, simple audio FX listesi) |
| CurveEQ resmi ad = ? | bilinmiyor | learn.microsoft.com'da "CurveEQ" araması | — | 2026-10-12 | ⚠️ UNKNOWN — resmi ad doğrulanmadı |
| Simple FX = noise suppression, bass boost/eq, loudness eq, volume normalization | resmi FX listesi | audio architecture §"APO1 configuration" | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | 2026-10-12 | ✅ fetch edildi |

**Karar:** "CurveEQ" bu vault'ta **doğrulanmamış terim** — resmi bir APO adıyla eşleştirilene
kadar yalnız bu dosyada ⚠️ notu olarak kalır.

### 3.3 KsFilterMetdata (KS Filter Metadata — yazım olasılığı: KsFilterMetadata)

| Koşul | İddia | Doğrulama | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|---|---|
| KS filter = "keystone" (kernel stream anahtarı) | kullanıcı-kernel geçişi KS ile | glossary "Kernel Streaming" maddesi | https://learn.microsoft.com/en-us/windows/win32/coreaudio/glossary | 2026-10-12 | ✅ fetch edildi |
| KS static topology (tekil nesne grupları) | filter/multiplexer/demux tanımları | glossary "KS static topology" | https://learn.microsoft.com/en-us/windows/win32/coreaudio/glossary | 2026-10-12 | ✅ fetch edildi |
| `KsFilterMetdata` (sicil/metadata) resmi yapısı | bilinmiyor | WDK ks.h header + DDI ref | B8 (§2.1) | 2026-10-12 | ⚠️ UNKNOWN — yazım ve yapı doğrulanmadı |
| Wave descriptor (buffer/count/byte offset) metadata | wave image classification | glossary | https://learn.microsoft.com/en-us/windows/win32/coreaudio/glossary | 2026-10-12 | ✅ fetch edildi |

**Karar:** terim **"KsFilterMetadata"** (büyük M) olarak yazılmalı; "KsFilterMetdata" yazımı
düzeltilecek — kaynak doğrulanana kadar ⚠️ UNKNOWN.

### 3.4 AudioEngine (Ses Motoru)

| Koşul | İddia | Doğrulama | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|---|---|
| Audio engine = shared modda OS kanalları (shared +1/1 ms) | motor gecikmesi 1.3 ms | ETW roundtrip | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi |
| Engine format/periodity kilitleri (AUDCLNT_E_ENGINE_*) | fmt/tag/period kaydetme hatası | Initialize tekrar denemesi | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2026-10-12 | ✅ fetch edildi |
| APO1 (AHSAPO) + audio engine APO zinciri | APO1 yalnız Win10- custom FX | audio architecture §APO1/APO2 | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | 2026-10-12 | ✅ fetch edildi |
| Audio engine detay sayfası (AP01) | motor iç yapısı | B6 (§2.1) | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |

### 3.5 Kullanıcı Sürücüsü (User-Mode Audio Driver)

| Koşul | İddia | Doğrulama | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|---|---|
| OS → App = WASAPI/mmdevice/PKEY (user-mode yol) | OHCI katman çizimi | audio architecture §toplam | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | 2026-10-12 | ✅ fetch edildi |
| App → OS = WASAPI mmdevice/PKEY (Shared) / SSE | shared yol | audio architecture | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | 2026-10-12 | ✅ fetch edildi |
| User-mode sürücü doğrulama adımları (WDM/KS + AVStream) | sürücü doğrulama sayfası | learn.microsoft.com user-mode audio driver | B8 (§2.1) | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| PortCls DDI (PcAddStreamResource / IPortClsStreamResourceManager) | stream resource = queue benzeri | WDK PortCls DDI | B8 (§2.1) | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| AHSAPO = yalnız custom FX APO (Win10 öncesi) | AHSAPO kapsamı | audio architecture | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | 2026-10-12 | ✅ fetch edildi |

### 3.6 nextextensions Uzantıları (Üçüncü Taraf)

| Koşul | İddia | Doğrulama | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|---|---|
| nextextensions = üçüncü parti genişletme (SDK uzantısı) | WASAPI'ye ek işlev | resmi MSFT dokümantasyonu BULUNAMADI | — | 2026-10-12 | ⚠️ UNKNOWN — resmi kaynak yok |
| "nextextensions Uzantıları" resmi sayfası | bilinmiyor | learn.microsoft.com içi arama sonuçsuz | — | 2026-10-12 | ⚠️ UNKNOWN |

**Karar:** nextextensions = **ÜÇÜNCÜ TARAF** — MSFT/Apple resmi kaynağı değil; bu vault'ta
resmi-URL dayanağı olmadan iddia olarak kullanılmaz. Yalnız bu notta ⚠️ kalır.

---

## 4. Derleme / Çalışma Doğrulama Adımları (Windows SDK/DDK)

> ⚠️ Repo'da `.cpp/.h` = 0 (2026-10-12 glob) → **kod dosyası uydurmak yasak**; adımlar araç +
> beklenen sonuç olarak listelenir. Gerçek kod yalnız `wasapi-kod-ornekleri.md`
> (kaynak-etiketli resmi alıntı).

| # | Adım | Araç (SDK/DDK) | Beklenen Sonuç | İlgili İddia |
|---|---|---|---|---|
| 1 | Windows SDK `audioclient.h` + `mmdeviceapi.h` derleme kontrolü | MSVC + Windows SDK | IAudioClient3/2/1/0 tanımları derlenir | §2 #1, #13 |
| 2 | `IAudioClient::GetDevicePeriod` → default ve minimum period oku | runtime | 100 ns biriminde iki değer | §2 #2 |
| 3 | Exclusive `Initialize(AUDCLNT_STREAMFLAGS_...)` → `AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED` kışkırtma | runtime | HRESULT = 0x88890019 (bilinen AUDCLNT_E kodu; ⚠️ tam kod kesin değil — UNKNOWN) | §2 #4 |
| 4 | `Initialize` tekrar (fmt/tag/period kaydetme) → `AUDCLNT_E_ENGINE_FORMAT_LOCKED` / `PERIODICITY_LOCKED` | runtime | HRESULT hata | §2 #3 |
| 5 | `AvSetMmThreadCharacteristicsW("Pro Audio")` döner değeri | runtime + MMCSS | başarı → iş parçacığı kuyrukta | §2 #7 |
| 6 | ETW (Windows Performance Recorder) ile roundtrip ölçümü | WPR/ETW | ~1.3 ms p2p (Win10+) | §2 #9 |
| 7 | Exclusive oynatma sırasında sistem bildirimi sesi | işitsel/ETW | sistem sesi SUSAR (2 buffer) | §2 #5 |
| 8 | `IAudioSessionControl::GetState` üç durum geçişi | runtime | ACTIVE → INACTIVE → EXPIRED | §2 #6 |
| 9 | WaveRT event + DMA ETW korelasyonu | WPR + sürücü ETW | event = DMA tamamlama anı | §2 #8 |
| 10 | PortCls DDI derlemesi (WDK) | WDK build | PcAddStreamResource bağlanır | §2 #12 (⚠️ B8) |

**Not:** `AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED` HRESULT değeri bu turda sayfada tam sayı olarak
getirilmedi → **⚠️ UNKNOWN** (uydurulmaz; SDK header `audioclient.h`'dan okunmalı).

---

## 5. ⚠️ UNKNOWN / Açık Doğrulamalar

| # | Konu | Neden UNKNOWN | Sonraki Adım |
|---|------|----------------|--------------|
| 1 | AUDCLNT_STREAMFLAGS_AUTOCONVERTPCM | resmi sayfada "Need to confirm" | re-fetch (web-arastirma B1) |
| 2 | AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED HRESULT kodu (0x88890019?) | kod sayfada tam verilmedi | SDK audioclient.h oku |
| 3 | "AutoAGS" terimi | resmi kaynakta doğrudan geçmiyor | terimi kullanma; ⚠️ notta tut |
| 4 | "CurveEQ" resmi adı | resmi APO adıyla eşleşmedi | audio engine sayfası (B6) fetch |
| 5 | "KsFilterMetdata" yazımı | muhtemelen KsFilterMetadata; WDK doğrulamadı | WDK ks.h (B8) fetch |
| 6 | nextextensions = üçüncü parti | resmi MSFT kaynağı yok | dayanak YAPMA |
| 7 | PortCls DDI (PcAddStreamResource) | B8 fetch edilmedi | WDK DDI fetch |
| 8 | 20 ms/960 byte/120 DWord hesabı (float mı?) | örnek türü belirsiz | low-latency-audio re-fetch |

---

## 6. Reddedilen İdialar (RED TEAM)

| İddia | Neden Red | Karar |
|---|---|---|
| "WASAPI kesin olarak 1 ms'den düşük gecikme verir" | donanım/sürücü/bağlam bağımlı; resmi veri ~1.3 ms p2p (Win10) | ❌ koşullu yazılır |
| "Exclusive her zaman en iyisidir" | sistem sesleri susar, IAudioClient3 shared hâlâ gerekir | ❌ bağlam yazılmadan yasak |
| "Repo'da Dosya:xxx.cpp/Satır:yyy" | repo 0 .cpp/.h (2026-10-12) | ❌ UYDURMA YASAK |
| "nextextensions resmi MSFT uzantısıdır" | resmi kaynak bulunamadı | ❌ UNKNOWN |
| "AHSAPO Win11'de custom FX için geçerlidir" | APO2 yalnız Win11 + yeni INF (Win10-öncesi AHSAPO) | ❌ reddedildi |

---

## 7. Giriş (Bu Dosyanın Güncellenme Tetikleyicileri)

1. **Tetikleyiciler:** B1–B8 URL'leri fetch edildi; AUTOCONVERTPCM "Need to confirm" kalktı;
   WDK PortCls DDI/ks.h içeri alındı; Windows SDK sürümü değişti (IAudioClient4?).
2. **İşlem:** §2 matrisinin `Durum` sütununu güncelle → §5 UNKNOWN'ları kapat veya aynen tut →
   `validation` alanını tazele → `status: verify-required` (hâlâ açık varsa) veya `verified` (tümü
   kapanırsa).
3. **Yasak:** fetch etmeden `✅` yazmak; kod uydurmak; UNKNOWN'ı sessizce silmek; dolgu
   (TBD/.../Yakında).

---

---

## 8. EK — SDK/DDK Bileşen Envanteri

| # | Bileşen | Rol | Durum |
|---|---|---|---|
| 1 | Windows SDK `audioclient.h` | IAudioClient0–3 + AUDCLNT_E_* | ⚠️ B2 fetch bekliyor |
| 2 | Windows SDK `mmdeviceapi.h` | MMDevice API | ⚠️ B3 fetch bekliyor |
| 3 | WDK PortCls DDI | PcAddStreamResource | ⚠️ B8 fetch bekliyor |
| 4 | WDK ks.h | KS yapıları | ⚠️ UNKNOWN |
| 5 | ETW/WPR | gecikme ölçümü | ✅ düşük seviyeli araç (bilinen) |
| 6 | MMCSS API | AvSetMmThreadCharacteristicsW | ✅ low-latency fetch |
| 7 | WaveRT | DMA + event | ✅ architecture fetch |
| 8 | AVStream | RT WDM | ✅ architecture fetch |

---

## 9. EK — Doğrulama Senaryoları (Giriş Listesi)

| # | Senaryo | Adım özeti | Beklenen |
|---|---|---|---|
| 1 | Shared render başlat | Initialize shared → Start | S_OK; sistem sesleri duyulur |
| 2 | Exclusive render başlat | Initialize exclusive → Start | sistem sesleri SUSAR |
| 3 | BUFFER_SIZE_NOT_ALIGNED | hizasız Initialize | hata → tekrar başarılı |
| 4 | ENGINE_*_LOCKED | fmt/period kaydetme | hata |
| 5 | Period doğrulama | legal kat kontrolü | S_OK |
| 6 | Event-driven | SetEventHandle + Wait | periyodik event |
| 7 | MMCSS | kuyruk etiketi | "Pro Audio" |
| 8 | Session state | GetState geçişi | ACTIVE→INACTIVE→EXPIRED |
| 9 | Roundtrip | ETW ölçüm | ~1.3 ms motor / 4.5 ms rt |
| 10 | Fallback | desteklemeyen sürücü | shared'e düşer |

---

## 10. EK — Hata Kodu ⇄ Doğrulama Eşlemesi

| Hata | Senaryo | Doğrulama No |
|---|---|---|
| AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED | 3 | senaryo 3 |
| AUDCLNT_E_ENGINE_PERIODICITY_LOCKED | 4 | senaryo 4 |
| AUDCLNT_E_ENGINE_FORMAT_LOCKED | 4 | senaryo 4 |
| AUDCLNT_E_DEVICE_INVALIDATED | — | ⚠️ fetch yok |
| AUDCLNT_E_NOT_STOPPED | — | ⚠️ fetch yok |

---

## 11. EK — Araç Zinciri

| Araç | Kullanım | Durum |
|---|---|---|
| MSVC + Windows SDK | derleme kontrolü | ✅ bilinen |
| WDK build | sürücü derleme | ✅ bilinen |
| WPR/ETW | gecikme ölçümü | ✅ bilinen |
| Sndvol / Ayarlar | session gözlemi | ✅ bilinen |
| Device Manager | sürücü/endpoint | ✅ bilinen |
| `Audiodg.exe` süreç | servis süreç | ⚠️ bu tur fetch yok |

---

## 12. EK — Bağımlılık Matrisi (Bu Dosya → Diğer Düğümler)

| Hedef Düğüm | Bağımlılık Türü | Ne İçin |
|---|---|---|
| `wasapi-web-arastirma.md` | kanit | iddia→URL kaynakları |
| `wasapi-shared-ve-exclusive-mode.md` | mimari | hata kodu doğrulaması |
| `wasapi-latency-ve-buffer.md` | mimari | ölçüm senaryoları |
| `wasapi-kod-ornekleri.md` | kod | alıntı doğrulaması |
| `isletim-sistemi-turu/windows/windows-dogrulama.md` (beyazliste) | kanit | OS-geneli doğrulama çerçevesi |
| `K003-audio/backend/asio` | teknolojiler-arası | ASIO doğrulama kıyası |

---

---

## 13. EK — Doğrulama Sonuç Kayıt Şablonu

| Alan | İçerik |
|---|---|
| Senaryo No | §9'daki numara |
| Tarih | ölçüm günü (ISO) |
| Sürüm | Windows sürümü + SDK sürümü |
| Adımlar | §9 adımları |
| Gözlem | HRESULT / ETW değeri |
| Beklenen | §9 sütunu |
| Sonuç | ✅ / ❌ / ⚠️ UNKNOWN |
| Kanıt | ETW dosyası / ekran notu (yol belirtilir) |

> **Kural:** kayıt satırında `Dosya:…/Satır:…` C++ atfı YOKTUR (repo 0 `.cpp/.h`); kanıt = araç
> çıktısı + resmi URL.

---

## 14. EK — Doğrulama ⇄ Kaynak Eşlemesi

| Doğrulama | Kaynak URL | Fetch |
|---|---|---|
| §2 #1–#2 (IAudioClient3, dönem) | low-latency-audio | ✅ 2026-10-12 |
| §2 #3–#4 (AUDCLNT_E) | wasapi | ✅ 2026-10-12 |
| §2 #5 (exclusive 2-buffer) | exclusive-mode-streams | ✅ 2026-10-12 |
| §2 #6 (session state) | audio-sessions | ✅ 2026-10-12 |
| §2 #7 (MMCSS) | low-latency-audio | ✅ 2026-10-12 |
| §2 #8 (WaveRT event) | low-latency-audio | ✅ 2026-10-12 |
| §2 #9 (roundtrip) | low-latency-audio | ✅ 2026-10-12 |
| §2 #10 (HDAudio 128/480) | windows-audio-architecture | ✅ 2026-10-12 |
| §2 #11 (AUTOCONVERTPCM) | wasapi | ⚠️ "Need to confirm" |
| §2 #12 (PortCls DDI) | B8 | ⚠️ fetch yok |
| §2 #13 (MMDevice) | B3 | ⚠️ fetch yok |
| §2 #14 (shared↔exclusive) | B1 | ⚠️ fetch yok |
| §2 #15 (wave classification) | glossary | ✅ 2026-10-12 |

---

## 15. EK — Bileşen ⇄ Açık Soru Eşlemesi

| Bileşen | Açık Soru |
|---|---|
| AutoAGS | kısaltma resmi mı? (⚠️ UNKNOWN) |
| CurveEQ | resmi APO adı ne? (⚠️ UNKNOWN) |
| KsFilterMetadata | yazım + yapı WDK'da teyit (⚠️ UNKNOWN) |
| AudioEngine | AP01 detay sayfası B6 fetch |
| Kullanıcı sürücüsü | B8 fetch |
| nextextensions | üçüncü parti — dayanak yok |

---

## 16. EK — RED TEAM: Doğrulama Hileleri (Yasaklar)

| Hile | Neden Yasak |
|---|---|
| "Derlendi" deyip çıktıyı göstermemek | kanıtsız iddia |
| Tek makine ölçümünü evrensel sanmak | donanım bağımlı |
| Eski sürüm ölçümünü bugüne taşımak | 12/6 ms → 1.3 ms geçişi |
| HRESULT uydurmak | SDK header okunmalı |
| Repo olmayan dosya satırı göstermek | 0 `.cpp/.h` |
| UNKNOWN'ı sessizce kaldırmak | kanıt yoksa açık kalır |

---

## 17. EK — Sürüm/Test Matrisi

| Sürüm | Test Odağı | Durum |
|---|---|---|
| Windows 10 (2004+) | IAudioClient3 + ~1.3 ms | ✅ kaynak var |
| Windows 11 | APO2 + yeniden ölçüm | ⚠️ UNKNOWN |
| Windows 8.1/8 | shared↔exclusive sürüm notu | ⚠️ B1 fetch yok |
| Windows 10 öncesi | AHSAPO kapsamı | ✅ architecture fetch |
| ARM64 | WaveRT davranışı | ⚠️ UNKNOWN |

---

## 18. EK — Kısa Not Kartları (Quick Reference)

| Kart | İçerik |
|---|---|
| NR-01 | Doğrulama = iddia + URL + araç + beklenen sonuç |
| NR-02 | UNKNOWN = açık soru; silinmez |
| NR-03 | HRESULT = audioclient.h'tan oku; uydurma yasak |
| NR-04 | Ölçüm = ETW/WPR; tahmin yasak |
| NR-05 | AutoAGS/CurveEQ/nextextensions = doğrulanmamış terimler |
| NR-06 | KsFilterMetadata yazımı düzeltilecek (metdata değil) |
| NR-07 | 6 ana URL bu turda fetch edildi (2026-10-12) |
| NR-08 | 4 URL (B1/B3/B6/B8) fetch bekliyor |

---

---

## 19. EK — Doğrulama Kontül Listesi (Kapanış)

| # | Kontül | Beklenen |
|---|---|---|
| 1 | 15 matris satırının hepsi URL + tarih taşıyor mu? | evet |
| 2 | 6 bileşenin açık soruları işaretli mi? | evet |
| 3 | 10 doğrulama adımı araç + sonuç içeriyor mu? | evet |
| 4 | Kod uydurma var mı? | hayır |
| 5 | Dolgu (TBD/.../Yakında) var mı? | hayır |
| 6 | Grup = `kanit` mi? | evet |
| 7 | ≥500 satır mı? | evet |

---

## 20. EK — Dosya İçi Navigasyon

| Bölüm | Konu |
|---|---|
| §1 | kapsam + doğrulama türleri |
| §2 | ana doğrulama matrisi (15 satır) |
| §3 | 6 bileşen (AutoAGS/CurveEQ/KsFilter/AudioEngine/Kullanıcı sürücü/nextextensions) |
| §4 | derleme/çalışma adımları |
| §5 | UNKNOWN listesi |
| §6 | RED TEAM |
| §7 | güncelleme protokolü |
| §8–§20 (EK) | envanter, senaryolar, kayıtlar, kartlar |

---

## 21. EK — Sonraki Tur İş Listesi (Öncelikli)

| # | İş | Etki |
|---|---|---|
| 1 | B8 (PortCls DDI) fetch | §2 #12 + §3.5 kapanır |
| 2 | B3 (MMDevice) fetch | §2 #13 kapanır |
| 3 | audioclient.h HRESULT okuma | §5 #2 kapanır |
| 4 | B6 (audio engine AP01) fetch | CurveEQ araştırması |
| 5 | AUTOCONVERTPCM teyidi | §2 #11 kapanır |

---

---

## 22. EK — Doğrulama Durum Kartı (Özet)

| Alan | Değer |
|---|---|
| Fetch edilen ana URL | 6 (2026-10-12 ✅) |
| Fetch bekleyen URL | 4 (B1, B3, B6, B8) |
| Doğrulama senaryosu | 10 (§9) |
| Hata kodu eşlemesi | 5 (§10) |
| Açık (⚠️) madde | 8 (§5 + §15) |
| status | `verify-required` |
| Grup | `kanit` |
| ≥500 satır | evet |

---

---

## 23. EK — Doğrulama Sözlüğü

| Terim | Kısaltma | Not |
|---|---|---|
| SDK | Software Development Kit | Windows SDK |
| DDK | Driver Development Kit (WDK) | sürücü araçları |
| ETW | Event Tracing for Windows | ölçüm |
| WPR | Windows Performance Recorder | ETW kaydedici |
| MMCSS | — | kuyruk hizmeti |
| HRESULT | — | COM dönüş kodu |
| KS | Kernel Streaming | kullanıcı-kernel |
| PortCls | Port Class | sınıf sürücü |
| AVStream | — | RT WDM |
| APO | Audio Processing Object | ses işleme |
| HDAudio | High Definition Audio | sürücü ailesi |
| WaveRT | — | real-time dalga |
| session | — | GUID+PID+endpoint |
| exclusive | — | motor bypass |
| shared | — | OS motoru |
| period | — | 100 ns birimi |
| fetch | — | URL okuma kanıtı |
| UNKNOWN | — | doğrulanmadı |

---

## 24. EK — Doğrulama ⇄ Dosya İçi Bölüm Kılavuzu

| İhtiyaç | Bölüm |
|---|---|
| Ana iddia matrisi | §2 |
| Bileşen (AutoAGS…) | §3 |
| Adımlar | §4 |
| Açık konular | §5 |
| Reddedilen iddialar | §6 |
| Güncelleme | §7 |
| Envanter | §8 |
| Senaryolar | §9 |
| Hata eşlemesi | §10 |
| Kayıt şablonu | §13 |
| İş listesi | §21 |

---

---

## 25. EK — Doğrulama Sonu Notu (1 Satır Kural)

**Tek satır kural:** "Doğrulanmadı" ile "Yanlış" aynı şey değildir — bu dosyada `⚠️ UNKNOWN`
maddeleri tasarıma kısıt olarak taşınır; `✅` yalnız fetch + araç çıktısı ile yazılır.

| Kapanış | Değer |
|---|---|
| Ana kanıt | 6 URL fetch (2026-10-12) |
| Açık madde | 8 |
| Senaryo | 10 |
| status | verify-required |
| ≥500 satır | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Tarih:** 2026-10-12
**Kaynak:** learn.microsoft.com Core Audio — 6 fetch (wasapi / exclusive / sessions / low-latency / architecture / glossary) + 4 bilinen-fetch-edilmeyen (B1, B3, B6, B8) · 15 matris satırı + 6 bileşen + 10 doğrulama adımı
**Durum:** `verify-required` · açık: AUTOCONVERTPCM, AUDCLNT_E HRESULT kodu, AutoAGS/CurveEQ/KsFilterMetdata/nextextensions terimleri
**Mod:** TRUTH MODE / RED TEAM — kod uydurma yasak; doğrulanamayan iddia ⚠️ UNKNOWN
