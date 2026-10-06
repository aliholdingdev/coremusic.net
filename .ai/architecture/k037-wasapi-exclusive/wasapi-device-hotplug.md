---
title: "K037.8 — WASAPI Cihaz Hotplug: Lock Çatışması, Yeniden Enumerasyon, ASIO↔WASAPI Fallback"
type: architecture
category: "K-Modül — Sürücü / D01"
version: 4.0.0
status: active
authority: "SSOT — .ai/architecture"
updated: 2026-10-06
---

# WASAPI Cihaz Hotplug (Device Change)

> **Kapsam:** Cihaz takma/çıkartma, varsayılan endpoint değişimi,
> `0x8889000A` kurtarma, yeniden enumerasyon ve ASIO↔WASAPI fallback.
>
> **Kanıt kuralı:** Her iddia §7'de backup satır haritasına bağlıdır.
> Backup'ta olmayan `⚠️ VERIFICATION REQUIRED`. **Repo'da WASAPI implementasyon
> kodu YOKTUR** (`index.md` §1.3).

---

## §1 Hotplug'un Tetiklediği Backup Olayları

Backup'ta cihaz değişimi doğrudan şu verilerla geçer:

| Olay | Backup kanıtı |
|---|---|
| Lock çatışması (cihaz başka süreçte kullanımda) `0x8889000A` = `AUDCLNT_E_DEVICE_IN_USE` → "Shared mode'a geç" | `k2-surucu/wasapi-exclusive.md L124–L130` (satır L126) |
| **Cihaz-geçersizleme (cihazın sökülmesi) hata kodu** | `⚠️ VERIFICATION REQUIRED` — backup tablosunda **YOK** (yalnız 4 kod var: L126–L129) |
| Cihaz envanteri: WMI `SELECT * FROM Win32_SoundDevice` | `k0-isletim-sistemi/windows-core.md L187–L203` |
| Varsayılan endpoint seçimi: `GetDefaultAudioEndpoint(eRender, eConsole)` | `k0-isletim-sistemi/windows-api.md L118–L120` |
| Cihaz ayarı saklama: Registry `SOFTWARE\CoreMusic\AudioDevices` | `k0-isletim-sistemi/windows-core.md L115–L121` |

→ Hotplug = **endpoint kaybı + yeniden seçimi + yeniden Initialize**.
(Cihaz-kaldırma anının kendi hata kodu backup'ta geçmez — uydurulmadı.)

---

## §2 Kurtarma Akışı

```
CIHAZ ÇIKARTILDI / BAĞLANTI KOPTU
        │
        ▼
Akış hatası — cihaz-kaldırma kodu backup'ta YOK
  (⚠️ VERIFICATION REQUIRED — hex uydurulmadı)
        │
        ▼
Cihaz envanterini tazele: WMI Win32_SoundDevice ← windows-core.md L187–L203
        │
        ▼
Yeni varsayılan endpoint bul: GetDefaultAudioEndpoint ← windows-api.md L118–L120
        │
        ▼
Registry'den tercih edilen cihaz ayarını oku     ← windows-core.md L115–L121
        │
        ▼
Activate(IAudioClient) → GetMixFormat → Initialize  ← windows-api.md L122–L139
        │
        ├── 0x8889000A (DEVICE_IN_USE) → başka süreç tutuyor → Shared'a geç
        │   (L126 · fallback: §4 zinciri)
        ├── 0x88890008 (UNSUPPORTED_FORMAT) → format fallback
        │   ([[wasapi-format-negotiation]] §5 · L127)
        ├── 0x8889000E (EXCLUSIVE_MODE_NOT_ALLOWED) → yetki kontrolü (L128)
        └── 0x88890018 (BUFFER_SIZE_ERROR) → buffer boyutunu ayarla (L129)
        │
        ▼
Akışı yeniden başlat (event handle yeniden bağla:
  SetEventHandle)                              ← windows-core.md L224–L225
```

**Backup kanıtı:** Akış şeması §7'deki satır haritasının birleşimidir;
**hex uydurma yoktur** — yalnız backup'ın 4 kodu kullanılmıştır
(`wasapi-exclusive.md L126–L129`). Cihaz-kaldırma semptomunun **kendi kodu
backup'ta yoktur** → `⚠️ VERIFICATION REQUIRED` olarak bırakıldı.

> ⚠️ **VERIFICATION REQUIRED:** Backup'ta **endpoint change notification
> arayüzü adı** (örn. bir callback/registration mekanizması) **GEÇMEZ**;
> yalnız `IAudioSessionEvent` / `IAudioSessionNotification` oturum arayüzleri
> geçer (`wasapi-exclusive.md L88–L97`). Bildirim mekanizmasının resmi adı
> official dokümantasyonla doğrulanmalıdır — burada **uydurulmadı**.

---

## §3 Değişim Senaryoları

| # | Senaryo | Backup dayanağı | Davranış |
|---|---|---|---|
| 1 | USB DAC çıkartıldı | Cihaz-kaldırma kodu backup'ta YOK (`⚠️ VERIFICATION REQUIRED`); envanter `windows-core L187–L203` | §2 kurtarma akışı (yeniden enum) |
| 2 | Yeniden takınca başka süreç tutuyor | `0x8889000A` DEVICE_IN_USE (`L126`) | Shared mode'a geç (§4 zinciri) |
| 2 | Varsayılan cihaz değişti (kullanıcı) | `GetDefaultAudioEndpoint` (windows-api L118–L120) | Sonraki akışta yeni endpoint seçilir; mevcut stream **etkisi ⚠️ VERIFICATION REQUIRED** |
| 3 | Aynı cihaz takıldı | Registry ayarları (windows-core L115–L121) | Tercih edilen cihaz ayarı geri uygulanır |
| 4 | Cihaz envanter kontrolü | WMI `Win32_SoundDevice` (windows-core L187–L203) | Enumeration raporu |

---

## §4 ASIO ↔ WASAPI Fallback Önceliği

Backup öncelik sırası (k2-surucu CLAUDE.md):

| Öncelik | API | Backup |
|---|---|---|
| **#1** | ASIO (birincil · exclusive lock) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/CLAUDE.md L27–L34` |
| **#2** | WASAPI Exclusive | `CLAUDE.md L27–L34` |
| **#6** | WASAPI Shared | `CLAUDE.md L27–L34` |

Ayrıca 00-enterprise-index araştırma satırı:
`AUDCLNT_SHAREMODE_EXCLUSIVE` + `AUDCLNT_STREAMFLAGS_EVENTCALLBACK` ·
`SetEventHandle` · MMCSS **"Pro Audio"** · IAudioClient3 · WaveRT > WaveCyclic
(`00-enterprise-index.md L188`).

**Fallback zinciri (hotplug sırasında):**

```
ASIO (#1) başarısız/uygunsuz
   │  (ASIO exclusive lock: yalnız 1 uygulama — bkz. k036)
   ▼
WASAPI Exclusive (#2)   ← bit-perfect, ~3ms (README L116–L122 · windows-api L95–L99)
   │  (cihaz format desteklemiyorsa / paylaşım gerekiyorsa)
   ▼
WASAPI Shared (#6)      ← ~15ms, engine karıştırır (README L116–L122)
```

**Backup kanıtı:** `k2-surucu/CLAUDE.md L27–L34` · `README.md L116–L122` ·
`windows-api.md L95–L99` · `00-enterprise-index.md L188`.

> **Disk bağımlılığı (dış wiki-link):** ASIO ayrıntısı →
> `[[../k036-asio-drivers/index]]` · `[[../k036-asio-drivers/asio-exclusive-mode]]`
> (her ikisi de diskte mevcut). CoreAudio karşılaştırması →
> `[[../k038-core-audio-macos/index]]` (diskte mevcut).

---

## §5 Hotplug'ta Buffer/Session Etkisi

| Katman | Etki | Backup |
|---|---|---|
| Buffer | Endpoint buffer süresi yeniden negotiate edilir → `0x88890018` (BUFFER_SIZE_ERROR) riski | `wasapi-exclusive.md L61–L87` (buffer) · L129 (kod) |
| Session | Oturum stream'i yeniden kurulur → metering/volume geçici olarak geçersiz | `wasapi-exclusive.md L88–L97` · L124–L130 |
| Thread | `WaitForSingleObject(hEvent)` döngüsü yeni handle ile devam | `windows-core.md L223–L234` |

→ Ayrıntı: [[wasapi-buffer-latency]] · [[wasapi-audio-session]] ·
[[wasapi-hata-kodlari]] §6.

---

## §6 Bilinmeyenler (boş bırakılan alanlar)

| Alan | Durum |
|---|---|
| Endpoint change notification resmi arayüz adı | `⚠️ VERIFICATION REQUIRED` (backup'ta yok) |
| `IMMDeviceEnumerator` üzerinden device-state callback kayıt API'si | `⚠️ VERIFICATION REQUIRED` |
| Hotplug tepki süresi hedefi (ms) | `[UNKNOWN]` (backup'ta yok) |
| Uygulama-tray hotplug bildirimi davranışı | `[UNKNOWN]` |

---

## §7 Kanıt Kaynakları (backup satır haritası)

| # | İddia | Backup dosyası | Satır |
|---|---|---|---|
| 1 | 4 hata kodu (`DEVICE_IN_USE`/`UNSUPPORTED_FORMAT`/`EXCLUSIVE_MODE_NOT_ALLOWED`/`BUFFER_SIZE_ERROR`) | `k2-surucu/wasapi-exclusive.md` | L124–L130 (satır L126–L129) |
| 2 | WMI `Win32_SoundDevice` sorgusu | `k0-isletim-sistemi/windows-core.md` | L182–L203 |
| 3 | Registry ses cihazı ayarları | `k0-isletim-sistemi/windows-core.md` | L115–L121 |
| 4 | `GetDefaultAudioEndpoint(eRender, eConsole)` | `k0-isletim-sistemi/windows-api.md` | L118–L120 |
| 5 | Activate + GetMixFormat + Initialize | `k0-isletim-sistemi/windows-api.md` | L122–L146 |
| 6 | SetEventHandle + event loop | `k0-isletim-sistemi/windows-core.md` | L223–L234 |
| 7 | Öncelik #1 ASIO · #2 WASAPI Exc · #6 Shared | `k2-surucu/CLAUDE.md` | L27–L34 |
| 8 | Mod tablosu (latency · bit-perfect) | `k2-surucu/README.md` | L112–L122 |
| 9 | Araştırma satırı (bayrak · MMCSS · WaveRT) | `00-enterprise-index.md` | L188 |
| 10 | Oturum arayüzleri (`IAudioSessionEvent` vb.) | `k2-surucu/wasapi-exclusive.md` | L88–L97 |

---

## §8 Çapraz Bağlantılar

| Konu | Dosya |
|---|---|
| Hata kodları | [[wasapi-hata-kodlari]] |
| Format fallback | [[wasapi-format-negotiation]] |
| Mod sahipliği | [[wasapi-exclusive-mode]] · [[wasapi-shared-mode]] |
| Buffer | [[wasapi-buffer-latency]] |
| Oturum | [[wasapi-audio-session]] |
| Mod karşılaştırması (hub) | [[wasapi-exclusive-shared]] |
| Üst hub | [[index]] |
| ASIO (dış) | `[[../k036-asio-drivers/index]]` · `[[../k036-asio-drivers/asio-exclusive-mode]]` |
| CoreAudio (dış) | `[[../k038-core-audio-macos/index]]` |

---

## §9 Değişiklik Günlüğü

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 4.0.0 | 2026-10-06 | İlk üretim — multi-md yapılandırması (device hotplug) |
| 4.0.0 | 2026-10-06 | Çelişki düzeltmesi — `0x8889000A` backup'a hizalandı (`DEVICE_IN_USE`, L126; backup kazandı); cihaz-kaldırma kodu `⚠️ VERIFICATION REQUIRED` olarak işaretlendi |
