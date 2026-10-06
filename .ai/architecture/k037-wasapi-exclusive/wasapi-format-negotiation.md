---
title: "K037.5 — WASAPI Format Negotiation: WAVEFORMATEX, GetMixFormat, IsFormatSupported"
type: architecture
category: "K-Modül — Sürücü / D01"
version: 4.0.0
status: active
authority: "SSOT — .ai/architecture"
updated: 2026-10-06
---

# WASAPI Format Negotiation

> **Kapsam:** Exclusive/Shared mod ayrımı olmadan, akışın açılabilmesi için format
> anlaşması (WAVEFORMATEX → GetMixFormat → IsFormatSupported → Initialize).
> Mod bazlı davranışlar → [[wasapi-exclusive-mode]] · [[wasapi-shared-mode]].
>
> **Kanıt kuralı:** Bu dosyadaki her teknik iddia birincil backup kaynağı
> `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` ve
> ek backup dosyalarıyla ilişkilidir (satır no §8'de). Backup'ta olmayan her
> değer `⚠️ VERIFICATION REQUIRED` veya `[UNKNOWN]` işaretlidir.
> **Repo'da WASAPI implementasyon kodu YOKTUR** → kod iddiası = kanıt yok.

---

## §1 Neden Format Anlaşması Zorunlu

WASAPI'de akış `IAudioClient::Initialize` ile açılır; bu çağrıya bir
`WAVEFORMATEX` verilir. Backup birincil kaynağı, arayüz ve akış şemasını
L37–L49'da tanımlar:

- `IAudioClient` — akış yaşam döngüsü (Initialize / Start / Stop / Start)
- `IAudioRenderClient` — çıkış buffer'ı
- `IAudioCaptureClient` — giriş buffer'ı
- `IAudioSessionControl` · `IAudioSessionManager` · `IAudioSessionMeter` — oturum katmanı

**Backup kanıtı:** `wasapi-exclusive.md L37–L49`.

Format uyarlaması olmadan `Initialize` başarısız olur ve tüm zincir
(buffer → session → render) açılmaz. Bu yüzden format negotitation
`[[wasapi-buffer-latency]]` ve `[[wasapi-audio-session]]` ön koşuludur.

---

## §2 WAVEFORMATEX — Backup Format Tablosu

Birincil backup kaynağı format tablosunu L99–L111'de verir:

| Biçim | Örnek Formatlar | Backup satır |
|---|---|---|
| PCM 16-bit | `44100 Hz · 16-bit · 2 ch (CD)` | `wasapi-exclusive.md L99–L103` |
| PCM 24-bit | `48000 Hz · 24-bit · 2 ch` | `wasapi-exclusive.md L104–L107` |
| PCM 32-bit float | `96000 Hz · 32-bit · 2 ch (Yüksek Kalite)` | `wasapi-exclusive.md L108–L111` |

**Backup kanıtı:** `wasapi-exclusive.md L99–L111` (üç format satırı).

Ek backup (README mod tablosu) format derinliğini mod bazında verir:

| Mod | Format (backup) |
|---|---|
| Shared Mode | PCM, 16/24-bit |
| Exclusive Mode | PCM, 16/24/32-bit |

**Backup kanıtı:** `_backup/arch-2026-10-06_1057/architecture/k2-surucu/README.md L116–L122`.

### §2.1 Alan Bazlı Şema (backup sınırları içinde)

Backup üç formatı örnek değerlerle verir; alan adlarını backup'tan birebir
üretmek mümkün değildir → alan listesi `⚠️ VERIFICATION REQUIRED` kabul edilir
ve yalnız **değerler** tabloya taşınır:

```
WAVEFORMATEX (şema alan adları ⚠️ VERIFICATION REQUIRED — repo'da kod yok)
┌────────────────┬──────────────────┬──────────────────┬──────────────────┐
│ Değer          │ PCM 16-bit       │ PCM 24-bit       │ PCM 32-bit float │
├────────────────┼──────────────────┼──────────────────┼──────────────────┤
│ Örnekleme hızı │ 44100 Hz         │ 48000 Hz         │ 96000 Hz         │
│ Bit derinliği  │ 16-bit           │ 24-bit           │ 32-bit           │
│ Kanal          │ 2 (stereo)       │ 2 (stereo)       │ 2 (stereo)       │
│ Kullanım       │ CD kalitesi      │ stüdyo           │ yüksek kalite    │
└────────────────┴──────────────────┴──────────────────┴──────────────────┘
```

**Backup kanıtı:** `wasapi-exclusive.md L99–L111`.

> ⚠️ **VERIFICATION REQUIRED:** `WcFormatTag` · `nChannels` · `nSamplesPerSec`
> · `nAvgBytesPerSec` · `nBlockAlign` · `wBitsPerSample` · `cbSize` alan
> adları ve 32-bit float için `WAVE_FORMAT_IEEE_FLOAT` sabiti backup'ta
> GEÇMEZ → uydurulmadı. Official Microsoft Core Audio dokümantasyonundan
> doğrulanmadan kullanılmamalıdır.

---

## §3 Format Karar Akışı (backup akışına dayalı)

Backup, `GetMixFormat` → `Initialize` sırasını kod örneği olarak verir
(`k0-isletim-sistemi/windows-api.md`):

```
IMMDeviceEnumerator (CoCreateInstance MMDeviceEnumerator)
        │
        ▼
GetDefaultAudioEndpoint(eRender, eConsole) ─→ IMMDevice
        │
        ▼
IMMDevice::Activate(IAudioClient) ─→ IAudioClient
        │
        ▼
IAudioClient::GetMixFormat(&pwfx) ─→ endpoint'in karışım formatı
        │
        ├── İstenen format == mix format? ── EVET ──→ Initialize(hedef)
        │
        └── HAYIR → mod kuralı:
                 Exclusive → desteklenen formatı kendin ver (bit-perfect)
                 Shared    → engine karıştırır; sen mix formata uy (§4)
        │
        ▼
IAudioClient::Initialize(sharemode, flags, hnsBuffer, 0, pwfx, NULL)
        │
        ▼
GetService(IAudioRenderClient) → buffer
```

**Backup kanıtı:**
- Endpoint activasyon + `GetMixFormat` + `Initialize` + `GetService`:
  `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md L103–L146`
- `AUDCLNT_SHAREMODE_EXCLUSIVE` / `AUDCLNT_SHAREMODE_SHARED` seçim parametresi:
  `windows-api.md L132–L134` · `k0-isletim-sistemi/windows-core.md L218–L221`
- Buffer süresi örneği (`10 * 1000 * 10` → 100 ms backup yorumu):
  `windows-api.md L135`
- COM init (`CoInitializeEx COINIT_MULTITHREADED`): `windows-api.md L241–L249`

### §3.1 Paylaşımlı Mod Üstünlüğü

Backup README mod tablosu:
- **Bit-perfect: Shared = Hayır · Exclusive = Evet**
- **Erişim: Shared = Tüm uygulamalar · Exclusive = Tek uygulama**

→ Shared modda uygulamanın seçtiği format garanti değildir; engine son
sözlüktür. Exclusive modda uygulama donanımın doğrudan sahibidir
([[wasapi-exclusive-mode]] §Ownership).

**Backup kanıtı:** `k2-surucu/README.md L116–L122`.

---

## §4 Mod Bazlı Format Sorumluluğu

| Sorumluluk | Exclusive | Shared | Backup |
|---|---|---|---|
| Formatı kim seçer | Uygulama (bit-perfect hedef) | Sistem mix formatı | `README.md L116–L122` · `windows-api.md L130` |
| Desteklenmeyen format | `Initialize` başarısız → fallback zinciri | Engine dönüştürür/ karıştırır | `wasapi-exclusive.md L124–L130` (hata kodları) |
| Bit derinliği | 16/24/32-bit | 16/24-bit | `README.md L119` |
| Örnekleme hızı değişikliği | Exclusive sahibi belirler | Endpoint mix rate | `windows-api.md L84` (ASIO tablosu: 44100–192000, ⚠️ ASIO'ya ait) |

**Desteklenmeyen format → hata zinciri (backup L124–L130):**

| Kod | Anlam (backup) |
|---|---|
| `0x8889000A` | `AUDCLNT_E_DEVICE_IN_USE` — cihaz başka süreçte kullanımda → Shared mode'a geç |
| `0x88890008` | `AUDCLNT_E_UNSUPPORTED_FORMAT` — desteklenmeyen format → formatı değiştir |
| `0x8889000E` | `AUDCLNT_E_EXCLUSIVE_MODE_NOT_ALLOWED` — yetki kontrolü |
| `0x88890018` | `AUDCLNT_E_BUFFER_SIZE_ERROR` — buffer boyutunu ayarla |

→ **Format başarısızlığının somut işareti `0x88890008`**
(`AUDCLNT_E_UNSUPPORTED_FORMAT`). Ayrıntı → [[wasapi-hata-kodlari]].

**Backup kanıtı:** `wasapi-exclusive.md L124–L130`.

---

## §5 Fallback Zinciri (format başarısızsa)

Backup, `0x8889000A` ve `0x88890008` hatalarına karşılık "fallback zinciri"
tanımlar (L124–L130 açıklaması). Zincirin backup'taki somut adımları:

1. **Hedef formatla dene** (PCM 32-bit float / 96 kHz hedefi — `L108–L111`)
2. **`0x88890008` (UNSUPPORTED_FORMAT) alırsan** → bir üst/sade biçimlere düş:
   32-bit float → 24-bit (`L104–L107`) → 16-bit (`L99–L103`)
3. **`0x8889000A` (DEVICE_IN_USE) alırsan** → format değil, **lock** sorunu:
   başka süreç cihazı tutuyor → Shared fallback ([[wasapi-device-hotplug]] §4)

> ⚠️ **VERIFICATION REQUIRED:** Zincirin adım sırası (32→24→16) backup'ta
> **açıkça yazılmamıştır**; backup yalnız "fallback zinciri" ifadesini ve iki
> hata kodunu verir. Sıra, README format tablosundaki (`L119`) derinlik
> sırasından türetilmiştir — implementasyon öncesi doğrulanmalıdır.

**Backup kanıtı:** `wasapi-exclusive.md L124–L130` · `README.md L119`.

---

## §6 `AUDCLNT_STREAMFLAGS_EVENTCALLBACK` ve Format

Backup'ta `Initialize` çağrı imzası iki yerde geçer; her ikisinde de bayrak
`AUDCLNT_STREAMFLAGS_EVENTCALLBACK`'tir:

```c
pAudioClient->Initialize(
    AUDCLNT_SHAREMODE_EXCLUSIVE,      // veya AUDCLNT_SHAREMODE_SHARED
    AUDCLNT_STREAMFLAGS_EVENTCALLBACK,
    bufferDuration, 0, &format, NULL);
```

**Backup kanıtı:** `windows-core.md L218–L221` · `windows-api.md L132–L139` ·
`00-enterprise-index.md L188` (araştırma satırı: `AUDCLNT_SHAREMODE_EXCLUSIVE`
+ `AUDCLNT_STREAMFLAGS_EVENTCALLBACK` · `SetEventHandle` · MMCSS "Pro Audio").

→ Format parametresi (`&format` / `pwfx`) bayraktan **bağımsız** 6. argümandır;
event bayrağı yalnız senkronizasyon yöntemini belirler
([[wasapi-buffer-latency]] §Event-driven).

> ⚠️ **VERIFICATION REQUIRED:** `AUDCLNT_STREAMFLAGS_EOFIL` ifadesi
> (görev metninde geçti) backup'ta **GEÇMEZ** → gerçek bir bayrak olarak
> kabul edilmedi; karar: kullanma (ayrıntı → [[wasapi-exclusive-mode]] §5.2).

---

## §7 Çapraz Bağlantılar

| Konu | Dosya |
|---|---|
| Exclusive mod sahipliği + bit-perfect | [[wasapi-exclusive-mode]] |
| Shared mod engine + format sorumluluğu | [[wasapi-shared-mode]] |
| Buffer / period / latency hesabı | [[wasapi-buffer-latency]] |
| Oturum katmanı (format oturumu etkiler mi) | [[wasapi-audio-session]] |
| Hata kodları (`0x88890008` vb.) | [[wasapi-hata-kodlari]] |
| Cihaz kaybında format geçersizleşmesi | [[wasapi-device-hotplug]] |
| Mod karşılaştırması (hub) | [[wasapi-exclusive-shared]] |
| Üst hub | [[index]] |

---

## §8 Kanıt Kaynakları (backup satır haritası)

| # | İddia | Backup dosyası | Satır |
|---|---|---|---|
| 1 | IAudioClient / Render / Capture / Session arayüzleri | `k2-surucu/wasapi-exclusive.md` | L37–L49 |
| 2 | Format tablosu (16/24/32-bit · 44.1/48/96 kHz) | `k2-surucu/wasapi-exclusive.md` | L99–L111 |
| 3 | Hata kodları + fallback zinciri | `k2-surucu/wasapi-exclusive.md` | L124–L130 |
| 4 | Mod tablosu (format · bit-perfect · erişim) | `k2-surucu/README.md` | L112–L132 |
| 5 | Endpoint → GetMixFormat → Initialize akışı | `k0-isletim-sistemi/windows-api.md` | L103–L146 |
| 6 | COM init | `k0-isletim-sistemi/windows-api.md` | L238–L249 |
| 7 | Initialize + SetEventHandle + padding döngüsü | `k0-isletim-sistemi/windows-core.md` | L205–L235 |
| 8 | Araştırma satırı (bayrak + MMCSS + IAudioClient3) | `00-enterprise-index.md` | L188 |
| 9 | ASIO buffer/rate aralığı (yalnız karşılaştırma) | `k0-isletim-sistemi/windows-api.md` | L79–L87 |

> **Repo kanıtı:** `C:\www\coremusic.net\.ai\architecture\k037-wasapi-exclusive\index.md`
> §1.3 — repo'da WASAPI implementasyon kodu yoktur; bu dosya yalnız
> backup dokümanı + official-doc etiketli bilgi içerir.

---

## §9 Değişiklik Günlüğü

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 4.0.0 | 2026-10-06 | İlk üretim — multi-md yapılandırması (format negotiation) |
