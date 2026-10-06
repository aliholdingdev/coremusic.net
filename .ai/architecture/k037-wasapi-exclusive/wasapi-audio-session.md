---
title: "K037.6 — WASAPI Audio Session: SessionControl, Manager, Meter, Oturum Yaşam Döngüsü"
type: architecture
category: "K-Modül — Sürücü / D01"
version: 4.0.0
status: active
authority: "SSOT — .ai/architecture"
updated: 2026-10-06
---

# WASAPI Audio Session

> **Kapsam:** WASAPI'nin oturum katmanı — `IAudioSessionControl` ·
> `IAudioSessionManager` · `IAudioSessionMeter` · endpoint volume ilişkisi.
> Mod davranışı → [[wasapi-exclusive-mode]] · [[wasapi-shared-mode]].
>
> **Kanıt kuralı:** Her iddia §7'de backup satır haritasına bağlıdır.
> Backup'ta olmayan `⚠️ VERIFICATION REQUIRED`. **Repo'da WASAPI implementasyon
> kodu YOKTUR** (`index.md` §1.3) → kod iddiası kanıtsızdır.

---

## §1 Oturum Nedir (backup tanımı)

Birincil backup kaynağı, WASAPI arayüz setini L37–L49'da sayarken oturum
nesnelerini de sayar:

- `IAudioSessionControl` — oturum denetimi (ad/estado/ses seviyesi alanı)
- `IAudioSessionManager` — oturum yöneticisi
- `IAudioSessionMeter` — oturum ölçümü (metering)
- `IAudioSessionEvent` — oturum olayları (eklenti/diskonet)

**Backup kanıtı:** `wasapi-exclusive.md L88–L97`.

README akış diyagramı oturumun yerini verir:

```
Application → Audio Client → Audio Session → Audio Engine → Hardware
                                    ↑
                              Event Callback
                                    ↓
                            Buffer Switch Event
```

**Backup kanıtı:** `k2-surucu/README.md L124–L132`.

→ **Audio Session, uygulama ile audio engine arasındadır.** Buffer switch
event'i session üzerinden yükselir; bu nedenle oturum katmanı latency
zincirinin bileşenidir ([[wasapi-buffer-latency]]).

---

## §2 Arayüz Kümesi ve Sorumluluklar

| Arayüz | Sorumluluk | Backup |
|---|---|---|
| `IAudioSessionControl` | Oturum denetimi (kontrol) | `wasapi-exclusive.md L88–L97` |
| `IAudioSessionManager` | Oturum yönetimi | `wasapi-exclusive.md L88–L97` |
| `IAudioSessionMeter` | Oturum ölçümü (metering) | `wasapi-exclusive.md L88–L97` |
| `IAudioSessionEvent` | Oturum olayları | `wasapi-exclusive.md L88–L97` |
| `IAudioEndpointVolume` | Endpoint (cihaz) ses seviyesi | `wasapi-exclusive.md L88–L97` |
| `IAudioSessionNotification` | Yeni oturum bildirimi | `wasapi-exclusive.md L88–L97` |

> ⚠️ **VERIFICATION REQUIRED:** Her arayüzün tek tek metod imzaları
> (`GetVolume` · `SetVolume` · `GetState` vb.) backup'ta **GEÇMEZ**; backup
> yalnız arayüz adlarını L88–L97 listeler. Metod adları official Microsoft
> dokümantasyonuyla doğrulanmadan kullanılmamalıdır.

---

## §3 Mod Etkisi (Exclusive vs Shared)

Backup README mod tablosundan oturum açısından sonuç:

| Boyut | Shared | Exclusive | Backup |
|---|---|---|---|
| Oturum sayısı | Tüm uygulamaların oturumları engine'de karışır | Tek uygulamanın oturumu | `README.md L116–L122` ("Erişim") |
| Bit-perfect | Hayır (engine müdahale eder) | Evet (engine dışı yol) | `README.md L116–L122` |
| Bit derinliği | 16/24-bit | 16/24/32-bit | `README.md L119` |
| Gecikme | ~15ms | ~3ms | `README.md L118` |

**Backup kanıtı:** `k2-surucu/README.md L112–L122` ·
`k0-isletim-sistemi/windows-api.md L95–L99` (Shared ~15ms · Exclusive ~3ms ·
Loopback ~15ms).

### §3.1 Çıkarım (etiketli)

- Shared modda **diğer uygulamaların oturumları** senin metering'ini ve
  latency'sini etkiler (engine tüm oturumları toplar).
- Exclusive modda **tek oturum** varsın; IAudioSessionMeter yalnız kendi
  akışını ölçer.

> Bu iki satır backup'ın "Tüm uygulamalar / Tek uygulama" +
> "~15ms / ~3ms" verilerinden **çıkarılmıştır** (etiket: çıkarım).
> ⚠️ VERIFICATION REQUIRED: ölçüm değeri olarak kullanılmadan önce
> implementasyonla doğrulanmalıdır.

---

## §4 Metering ve Real-Time Kuralı

Backup, real-time audio thread senkronizasyonunu kod örneğiyle verir:

```c
while (running) {
    WaitForSingleObject(hEvent, INFINITE);   // event-driven
    pAudioClient->GetCurrentPadding(&padding);
    if (padding < bufferFrameCount) {
        ProcessAudioData();                   // RT bölge
    }
}
```

**Backup kanıtı:** `k0-isletim-sistemi/windows-core.md L223–L234`.

RT thread kural tablosu (backup, senkron primitiflerin audio thread'de
kullanımını sınırlar):

| Primitif | Audio thread | Backup |
|---|---|---|
| Critical Section | ❌ YASAK | `k0-isletim-sistemi/windows-api.md L175–L184` |
| SRW Lock | ❌ YASAK | `windows-api.md L175–L184` |
| Event | ⚠️ Sınırlı | `windows-api.md L175–L184` |
| Semaphore | ⚠️ Sınırlı | `windows-api.md L175–L184` |
| Interlocked* | ✅ | `windows-api.md L175–L184` |
| atomic<> | ✅ | `windows-api.md L175–L184` |

→ **Metering okuması RT thread'de lock ile yapılamaz** (Critical Section /
SRW Lock yasak). Ölçüm değeri Interlocked/atomic ile publish edilmelidir.

**Backup kanıtı:** `windows-api.md L175–L184`.

> ⚠️ VERIFICATION REQUIRED: `IAudioSessionMeter`'ın kendi okuma API'si
> (senkron mu asenkron mu) backup'ta geçmez → lock yasağı burada yalnız
> **uygulamanın kendi metering yayını** için geçerlidir.

---

## §5 Endpoint Volume ve Bit-Perfect Uyarısı

- `IAudioEndpointVolume` (backup L88–L97) cihaz seviyesini denetler.
- Backup mod tablosu: Shared modda **Bit-perfect = Hayır**
  (`README.md L116–L122`).

→ Endpoint volume değişimi, exclusive bit-perfect yolunu
**dijital domain'de** etkiler mi: ⚠️ **VERIFICATION REQUIRED**
(backup'ta volume → bit-perfect etkisi **geçmez**). Uygulama kararı:
bit-perfect modda volume rampalamayı uygulama içinde yap, endpoint
seviyesini 0 dB'de sabitle → *karar, kanıt eksikliği nedeniyle öneridir,
ADR gerektirir*.

**Backup kanıtı:** `wasapi-exclusive.md L88–L97` (endpoint volume arayüzü) ·
`README.md L116–L122` (bit-perfect satırı).

---

## §6 Oturum Yaşam Döngüsü ve Cihaz Değişimi

Backup hata kodları, oturum/akış hayatını cihaz olaylarıyla bağlar:

| Kod | Anlam (backup) | Oturum etkisi |
|---|---|---|
| `0x8889000A` | `AUDCLNT_E_DEVICE_IN_USE` — cihaz başka süreçte kullanımda | Exclusive oturum açılamaz → Shared fallback |
| `0x8889000E` | `AUDCLNT_E_EXCLUSIVE_MODE_NOT_ALLOWED` — yetki reddi | Oturum exclusive yolunda açılamaz |
| `0x88890018` | `AUDCLNT_E_BUFFER_SIZE_ERROR` — buffer boyutu hatalı | Oturum stream'i periyodu geçersiz → yeniden negotiate |

**Backup kanıtı:** `wasapi-exclusive.md L124–L130`.

→ Cihaz takma/çıkartma senaryosu → [[wasapi-device-hotplug]];
lock/yetki/buffer hataları → [[wasapi-hata-kodlari]].

Ek backup: WMI ile ses cihazı sorgusu (`SELECT * FROM Win32_SoundDevice`) —
cihaz envanteri hotplug kontrolünde kullanılabilir.

**Backup kanıtı:** `k0-isletim-sistemi/windows-core.md L182–L203`.

---

## §7 Çapraz Bağlantılar

| Konu | Dosya |
|---|---|
| Mod sahipliği | [[wasapi-exclusive-mode]] · [[wasapi-shared-mode]] |
| Buffer/latency (session zincirdedir) | [[wasapi-buffer-latency]] |
| Format | [[wasapi-format-negotiation]] |
| Hata kodları | [[wasapi-hata-kodlari]] |
| Cihaz değişimi | [[wasapi-device-hotplug]] |
| Mod karşılaştırması (hub) | [[wasapi-exclusive-shared]] |
| Üst hub | [[index]] |

---

## §8 Kanıt Kaynakları (backup satır haritası)

| # | İddia | Backup dosyası | Satır |
|---|---|---|---|
| 1 | Session arayüz kümesi | `k2-surucu/wasapi-exclusive.md` | L88–L97 |
| 2 | Akış diyagramı (Application→…→Hardware) | `k2-surucu/README.md` | L124–L132 |
| 3 | Mod tablosu (erişim · bit-perfect · latency) | `k2-surucu/README.md` | L112–L122 |
| 4 | Mod latency tablosu (15/3/15ms + Loopback) | `k0-isletim-sistemi/windows-api.md` | L91–L99 |
| 5 | Event loop (SetEventHandle/GetCurrentPadding) | `k0-isletim-sistemi/windows-core.md` | L205–L235 |
| 6 | RT senkron primitif kural tablosu | `k0-isletim-sistemi/windows-api.md` | L150–L184 |
| 7 | Hata kodları (4 adet) | `k2-surucu/wasapi-exclusive.md` | L124–L130 |
| 8 | WMI `Win32_SoundDevice` sorgusu | `k0-isletim-sistemi/windows-core.md` | L182–L203 |
| 9 | MMCSS "Pro Audio" araştırma satırı | `00-enterprise-index.md` | L188 |

---

## §9 Değişiklik Günlüğü

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 4.0.0 | 2026-10-06 | İlk üretim — multi-md yapılandırması (audio session) |
