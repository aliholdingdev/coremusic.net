---
title: "ASIO Hata Yönetimi"
type: architecture
category: architecture
version: 1.0.0
status: draft
authority: "SSOT — .ai/architecture"
updated: 2026-10-06
---

# K036.7 — ASIO Hata Yönetimi (ASIOError_* · ErrorChain · Kurtarma · Eskalasyon)

> **Hub:** [[index]] · **İlgili:** [[asio-device-lifecycle]] (K036.4) · [[asio-exclusive-mode]] (K036.1) ·
> [[asio-sdk-entegrasyon]] (K036.6)
>
> **Kapsam:** `ASIOError_*` kodları, katmanlı `ErrorChain` + retry, Edge Case #6 fallback,
> eskalasyon yolları. Kod implementasyonu repo'da YOK (bkz. [[index]] §1.2).

---

## §1 Backup Kanıt Kaynakları (birincil içerik kaynağı)

| # | Backup dosyası | Kanıt satırları | Kullanılan içerik |
|---|---|---|---|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` | L105–L113 | `ASIOError_*` hata durumları + tepki stratejileri |
| 2 | `_backup/.../k2-surucu/driver-stack-mimari.md` | L212–L260 | `ErrorChain` — bildirim + üst katmana iletim + retry mantığı |
| 3 | `_backup/.../k2-surucu/driver-stack-mimari.md` | L239–L246 | `ErrorCode` enum: 1001–1006 (`ERR_DEVICE_NOT_FOUND` … `ERR_TIMEOUT`) |
| 4 | `_backup/.../k2-surucu/index.md` | L52–L53 | Hata toleransı: donanım kopunca K3 çökmez, graceful degradation |
| 5 | `_backup/.../k2-surucu/index.md` | L63–L71 | Hedef metrikler (kurtarma < 100ms — driver-stack L360) |
| 6 | `_backup/.../k2-surucu/driver-stack-mimari.md` | L116–L133 | ASIO yoksa → WASAPI (`createAutoDetect`) |
| 7 | `_backup/.../k12-izleme/README.md` | L72 | İzleme metriği: "ASIO buffer underrun · Sayı" |
| 8 | `.ai/AGENTS.md` | §17 Edge Case #6 | Cihaz/hat kopması → WASAPI fallback zorunlu |
| 9 | `.ai/AGENTS.md` | §10.1 | Hata eskalasyon yolu |
| 10 | `.ai/ecosystem/asio-wasapi-rehber.md` | §5.1 | Fallback zinciri (ASIO → WASAPI Shared) |

---

## §2 `ASIOError_*` Kodları ve Tepkiler

Kaynak: `asio-drivers.md` L105–L113 (backup birebir):

| Hata | Backup tepkisi | Sınıf | Sonraki adım |
|---|---|---|---|
| `ASIOError_InvalidMode` | Exclusive mode kullanılamıyorsa **Shared mode'a geç** | geçiş | [[asio-exclusive-mode]] §6; WASAPI Shared ise: `[[../k037-wasapi-exclusive/wasapi-exclusive-shared]]` |
| `ASIOError_BufferSize` | Buffer boyutu donanım tarafından desteklenmiyorsa | yeniden yapılandırma | [[asio-buffer-callback]] §6 (buffer yeniden kurulumu) |
| `ASIOError_HardwareFailure` | Donanım hatası → **K3'ü durdur** | kritik | [[asio-device-lifecycle]] §3.3 (ERROR durumu) |
| `ASIOError_UnableToStart` | Başlatma hatası → **3 yeniden deneme** | retry | [[asio-device-lifecycle]] §3.3 |

```
ASIOError akışı:

  driver.start() / callback
        │
        ├─ InvalidMode ────────→ Exclusive→Shared geçişi (lock bırakılır! bkz. exclusive-mode §6)
        ├─ BufferSize ─────────→ buffer'ı min sınıra çek, yeniden init (CONFIGURED'e dön)
        ├─ UnableToStart ──────→ retry ×3 ── başarısız ─┐
        └─ HardwareFailure ────→ K3'ü durdur ───────────┤
                                                        ▼
                                                   ERROR durumu
                                                        │
                                          retry/kurtarma başarısızsa
                                                        ▼
                                          WASAPI fallback (Edge Case #6)
```

---

## §3 Katmanlı ErrorChain (backup iskeleti)

Kaynak: `driver-stack-mimari.md` L216–L259.

### §3.1 `ErrorCode` enum (backup L239–L246)

| Kod | Ad | ASIO karşılığı (eşleme) |
|---|---|---|
| 1001 | `ERR_DEVICE_NOT_FOUND` | cihaz listelenemiyor / unplugged → fallback tetikler |
| 1002 | `ERR_BUFFER_OVERFLOW` | `ASIOError_BufferSize` ailesi · underrun/overrun sayacı |
| 1003 | `ERR_FORMAT_NOT_SUPPORTED` | SR/canal/bit-depth reddi |
| 1004 | `ERR_PERMISSION_DENIED` | Exclusive lock başka uygulamada (Sözleşme #2 ihlali) |
| 1005 | `ERR_HARDWARE_FAILURE` | `ASIOError_HardwareFailure` |
| 1006 | `ERR_TIMEOUT` | başlatma zaman aşımı |

⚠️ VERIFICATION REQUIRED: 1:1 eşleme tablosu bu dosyada **yorumlanmıştır**; backup'ta
`ASIOError_*` ↔ `ERR_*` eşlemesi açıkça yoktur (iki ayrı dosyada bağımsız tanımlar).

### §3.2 Akış (backup L218–L258)

```
reportError(layer, code, msg):
  1. ErrorEntry kaydet (layer, code, message, timestamp)
  2. notifyUpperLayer(entry)          → K3'e hata bildirimi
  3. shouldRetry(code)?               → ERR_TIMEOUT ve ERR_BUFFER_OVERFLOW: retryOperation()
     diğer kodlar: retry YOK, doğrudan üst katmana bildir
```

Katman bilgisi (backup L220): hata hangi katmanda (K2 HAL / K2 driver / K2 platform) üretildi
kayda girilir — ASIO yolunda `layer` = K2 driver (ASIO adapter).

### §3.3 `ASIOError_*` vs `ERR_*` nasıl birleşir?

```
ASIO callback / init (backup asio-drivers.md L105-L113)
        │  özel tepki (InvalidMode / BufferSize / retry / durdur)
        ▼
ErrorChain::reportError(K2_DRIVER, koddan_eşlenen_ERR_*, msg)
        │
        ├─ retry kararı (L255-L258)
        └─ notifyUpperLayer → K3 (k2 index.md L52-L53: K3 çökmez)
```

---

## §4 Retry ve Kurtarma Politikası

| Politika | Değer | Kanıt |
|---|---|---|
| Başlatma yeniden deneme | 3 | asio-drivers.md L112 |
| Retry'lı hata kodları | `ERR_TIMEOUT`, `ERR_BUFFER_OVERFLOW` | driver-stack-mimari.md L255–L258 |
| Hata kurtarma süresi hedefi | < 100ms | driver-stack-mimari.md L360 |
| Driver değişim (fallback) süresi hedefi | < 10ms | driver-stack-mimari.md L359 |
| Graceful degradation | K3 çökmez | k2 index.md L52–L53 |

Süreler hedeftir (backup "Hedef" sütunu); ölçüm yöntemi ⚠️ VERIFICATION REQUIRED
(backup'ta ölçüm prosedürü yok).

---

## §5 Edge Case #6 — Cihaz/Hat Kopması (zorunlu fallback)

Kaynak: `.ai/AGENTS.md` §17 Edge Case #6 (zorunlu WASAPI fallback) + backup fallback factory.

```
Belirti                          Tepki zinciri
──────────────────────────────────────────────────────────────
Driver listesinde cihaz yok      ERR_DEVICE_NOT_FOUND(1001)
  (unplug / sürücü kaybı)           → retry/kurtarma
                                    → başarısız → WASAPI fallback

Exclusive lock aniden kaybedildi ERR_PERMISSION_DENIED(1004)
  (diğer uygulama çöktü/sızdı)    → [[asio-exclusive-mode]] §8 tespit akışı
                                    → kurtarılamazsa WASAPI fallback

Callback underrun sayacı        ERR_BUFFER_OVERFLOW(1002) + izleme
yükseliyor                         → k12 metriği: "ASIO buffer underrun"
                                    → buffer düşür / retry → düşmezse fallback
```

Kullanıcıya bildirim ve geri dönüş (yeniden ASIO denemesi) davranışı:
⚠️ VERIFICATION REQUIRED — backup'ta otomatik yeniden-ASIO politikası tanımlı değil.

---

## §6 Eskalasyon Yolu

Kaynak: `.ai/AGENTS.md` §10.1 (hatta hat → role → Expert).

```
Seviye 0: ASIOError tepkisi (§2)                    — otomatik, RT dışı yönetim thread'i
Seviye 1: ErrorChain retry (§3.2, 3 deneme)         — otomatik
Seviye 2: WASAPI fallback (Edge Case #6)            — otomatik, zorunlu
Seviye 3: K3'e bildirim + kullanıcıya durum raporu  — notifyUpperLayer (backup L251-L253)
Seviye 4: Tekrarlayan/çözülemeyen → eskalasyon      — .ai/AGENTS.md §10.1
          (role → Expert: embedded-engineer / windows-software-engineer)
```

Seviye 4 içeriği bu dokümanın dışındadır; vault otoritesi: `@.ai/.rules/error-recovery.md`
(istek anında okunur).

---

## §7 Hata → Konu Dosyası Yönlendirme Tablosu

| Hata / durum | İlgili topic |
|---|---|
| `ASIOError_InvalidMode`, lock kaybı | [[asio-exclusive-mode]] |
| `ASIOError_BufferSize`, underrun/overrun | [[asio-buffer-callback]] |
| Buffer değişiminde latency etkisi | [[asio-latency-hesap]] |
| Retry sonrası durum geçişleri | [[asio-device-lifecycle]] |
| Callback içinde hata yakalama (noexcept!) | [[asio-thread-model]] |
| SDK init/com hatası | [[asio-sdk-entegrasyon]] |
| Fallback hedefi | `[[../k037-wasapi-exclusive/index]]` |

---

## §8 Wiki-Bağlantılar

- Hub: [[index]]
- Tüm K036.1–K036.6 topic dosyaları §7'de listelendi.

---

## §9 Backup Kanıt Özeti (bu dosyaya ait)

```
backup kanıtı: k2-surucu/asio-drivers.md         L105-L113 (ASIOError_* kodları + tepkiler)
backup kanıtı: k2-surucu/driver-stack-mimari.md  L212-L260 (ErrorChain + ERR_* 1001-1006 + retry)
backup kanıtı: k2-surucu/driver-stack-mimari.md  L116-L133 (ASIO→WASAPI createAutoDetect)
backup kanıtı: k2-surucu/driver-stack-mimari.md  L354-L362 (kurtarma <100ms, değişim <10ms)
backup kanıtı: k2-surucu/index.md                L52-L53   (graceful degradation — K3 çökmez)
backup kanıtı: k12-izleme/README.md              L72       (ASIO buffer underrun metriği)
kanıt: .ai/AGENTS.md §17 Edge Case #6 (→ WASAPI fallback zorunlu)
kanıt: .ai/AGENTS.md §10.1 (eskalasyon yolu)
kanıt: .ai/ecosystem/asio-wasapi-rehber.md §5.1 (fallback zinciri)
```

**Çelişki raporu:** Backup içi çelişki YOK. Vault ile uyum: `.ai/AGENTS.md` §17 Edge Case #6,
backup `createAutoDetect` (L120–L123) ile aynı kararı verir.

---

*Backmatter: `version: 1.0.0` · `status: draft` · `updated: 2026-10-06` · `authority: "SSOT — .ai/architecture"`*
