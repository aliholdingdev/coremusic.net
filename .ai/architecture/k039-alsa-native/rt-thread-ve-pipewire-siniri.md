---
title: "ALSA Native — RT Thread Kuralları ve PipeWire Sınırı"
type: architecture
category: audio
status: active
version: 1.0.0
date: 2026-10-06
related_adrs: [D01]
---

# RT Thread ve PipeWire Sınırı (k039)

> Bu dosya k039 kapsamındaki iki daraltılmış konuyu tek yerde toplar:
> (1) gerçek zamanlı (RT) thread disiplini, (2) ALSA ile PipeWire/PulseAudio
> sınırı. Kanıt yoksa `⚠️ VERIFICATION REQUIRED` yazılır; sayı/spin/us uydurulmaz.

---

## 1. Kapsam ve Kapsam Dışı

| Konu | Bu dosyada | Not |
|---|---|---|
| ALSA RT thread kuralları (öncelik, stack, kilit) | ✅ | Bölüm 4–5 |
| PipeWire/PulseAudio ↔ uygulama sınırı | ✅ | Bölüm 6 |
| buffer/period hesabı | ❌ | → `alsa-pcm-device.md` §6–§6.4 |
| mmap / RT patch | ❌ | → `alsa-pcm-device.md` §4–§5 |
| hw/plughw seçim | ❌ | → `alsa-pcm-device.md` §2.2 |
| Latency zinciri bütçesi | ❌ | → `[[../k032-latency-optimization/latency-optimization]]` |

---

## 2. Dosya Haritası

| Dosya | İçerik |
|---|---|
| `rt-thread-ve-pipewire-siniri.md` (bu dosya) | RT thread kuralları, PipeWire sınırı |
| `alsa-pcm-device.md` | hw/plughw, PCM akışı, mmap, buffer/period, PCM state |
| `index.md` | kimlik, bağımlılık, dosya indeksi, izlenebilirlik |

---

## 3. Kimlik Bloğu (bu dosya)

| Alan | Değer |
|---|---|
| Layer ID | `k039-alsa-native/rt-thread-ve-pipewire-siniri` |
| Purpose | ALSA sürücü-öncesi katmanda RT thread disiplinini ve PipeWire/PulseAudio sınırını tanımlamak |
| Dependencies Allowed | `.ai/architecture/k018`, `k025`, `k020`, `k016`, `k030`–`k033` |
| Dependencies Forbidden | k040–k053 (diskte YOK), doğrudan `snd_pcm_*` çağrıları (repo'da 0 eşleşme) |
| Runtime | Linux ALSA user-space (libsndfile haricinde repo kodu yok — §8) |
| Failure Mode | Öncelik tersine dönüşü (priority inversion), RT thread'de bloklayıcı I/O |
| Observability | `⚠️ VERIFICATION REQUIRED` — bu repo'da RT ölçüm altyapısı yok |

---

## 4. RT Thread Kuralları (kanıtlı)

### 4.1 Kaynaklarda ne var

**a) Sistem çağrısı seviyesinde öncelik (kanıt: `cross-platform-api.md` L57–L62):**

```c
pthread_attr_setschedpolicy(&attr, SCHED_FIFO);   /* Gerçek zamanlı */
attr.schedparam.sched_priority = 50;
pthread_attr_setscope(&attr, PTHREAD_SCOPE_SYSTEM);
pthread_create(&tid, &attr, rt_thread_func, NULL);
```

**b) RT thread stack boyutu (kanıt: `cross-platform-api.md` L63):**

```c
pthread_attr_setstacksize(&attr, 1024 * 1024);    /* 1 MB */
```

**c) Kilit hiyerarşisi — öncelik tersine dönüşüne karşı (kanıt: `cross-platform-api.md` L64–L75):**

```
Device Lock  >  Stream Lock  >  Ring Buffer Lock
   (en dış)                                              (en iç)
```

Kaynakta verilen hata örneği: iki thread farklı sırayla `pthread_mutex_lock`
çağırırsa kilitlenme (deadlock) oluşur; hiyerarşi bu sırayı zorunlu kılar.

> ⚠️ VERIFICATION REQUIRED: kaynak koddaki `/* Normal öncelik */` yorumu
> `SCHED_RR` yanında geçiyor; SCHED_RR gerçekte gerçek zamanlı bir politikadır.
> Bu dosyada yorumu tekrarlamıyoruz, sadece çağrı mekanizmasını aktarıyoruz.
> Hangi öncelik değerinin bu projede kullanılacağı: **[UNKNOWN]**.

**d) Öncelik hiyerarşisi şeması (kanıt: `cross-platform-api.md` L78–L83):**

```
┌─────────────────────────────────────┐
│  Gerçek Zamanlı Sistem (SCHED_FIFO)│  ← En yüksek
├─────────────────────────────────────┤
│  Yüksek Öncelikli İşler            │
├─────────────────────────────────────┤
│  Normal İşler (SCHED_OTHER)         │
├─────────────────────────────────────┤
│  Boşta Kalma İşleri                │  ← En düşük
└─────────────────────────────────────┘
```

**e) Bekleme stratejisi (kanıt: `cross-platform-api.md` L85–L93):**

| Kullanım Yeri | Bekleme Mekanizması | Neden |
|---|---|---|
| Thread'ler arası veri | `sem_wait` | Düşük gecikme |
| Worker havuzu | `pthread_cond_wait` | Esnek uyanma |
| Timeout bekleme | `sem_timedwait` | Zaman sınırı |

### 4.2 Bu dosyanın kural listesi (kanıtlanan kaynaklara dayalı)

| # | Kural | Kanıt |
|---|---|---|
| R1 | RT thread `pthread_attr_setschedpolicy(..., SCHED_FIFO)` ile oluşturulmalı | `cross-platform-api.md` L57–L61 |
| R2 | RT thread stack ≥ 1 MB (`pthread_attr_setstacksize`) | `cross-platform-api.md` L63 |
| R3 | Kilit sırası zorunlu: Device → Stream → Ring Buffer | `cross-platform-api.md` L64–L75 |
| R4 | Beklemede `sem_wait` / `pthread_cond_wait` / `sem_timedwait` | `cross-platform-api.md` L85–L93 |
| R5 | Sistem servisi systemd ile RT yetkisi verilerek çalıştırılmalı | `linux-core.md` L146–L152 (aşağıda §4.3) |
| R6 | Uygulama içi öncelikli thread havuzu düşük → yüksek sırayla tüketilmeli | `threading-model.md` L161–L177 |

### 4.3 systemd servis öncelikleri (kanıt: `linux-core.md` L146–L152)

```ini
[Service]
Nice=-20
CPUSchedulingPolicy=realtime
LimitRTPRIO=99
LimitRTTIME=infinity
Group=audio
```

⚠️ VERIFICATION REQUIRED: bu birim dosyasının adı ve hangi servise ait olduğu
kaynakta geçmiyor; ALSA-related bir servise ait olduğu **[UNKNOWN]**. Değerler
(99, -20, infinity) kaynaktan aynen alınmıştır, yorumlanmamıştır.

### 4.4 Sistem çağrıları — kaynak davranış tablosu (kanıt: `cross-platform-api.md` L160–L169)

| Çağrı | Davranış | Block Mu? |
|---|---|---|
| `read()` / `write()` | Veri hazır olana kadar bekleme | ✅ Block |
| `poll()` / `select()` | Belirli olayları bekleme | ✅ Block |
| `futex` (FUTEX_WAIT) | Kilit bekleme (düşük gecikme) | ✅ Block |
| `epoll_wait` | I/O olaylarını bekleme | ✅ Block |

> RT thread içinde bu çağrıların hangisinin kullanılacağı proje kararına bağlı:
> **[UNKNOWN]** (repo'da kod yok — §8).

---

## 5. Öncelik Tersine Dönüşü (priority inversion) — kaynak anlatımı

Kanıt: `cross-platform-api.md` L70–L75.

| Thread | Öncelik (kaynaktaki örnekte) | Durum |
|---|---|---|
| Düşük öncelikli | `prio 10` | Kilit 1'i tutuyor |
| Yüksek öncelikli | `prio 50` | Kilit 2'i bekliyor |
| Orta öncelikli | `prio 30` | CPU'yu alıkoyuyor |

Sonuç (kaynak metni): yüksek öncelikli thread, düşük öncelikli serbest
olana kadar beklemek **zorunda** kalır.

Çözüm (kaynak metni): **kilit hiyerarşisi** + **öncelik mirası (priority
inheritance)** kullanılması. `priority inheritance` mekanizmasının bu projede
açık olup olmadığı: **[UNKNOWN]**.

---

## 6. PipeWire / PulseAudio Sınırı

### 6.1 Kaynak durum (kanıt: `k2-surucu/pipewire-modern.md`)

| Bilgi | Değer | Satır |
|---|---|---|
| PipeWire sürümü | 0.3.48+ | L12 |
| SPA havuzu | 6 modül (audio, video, codec, dsp, plugin, support) | L12 |
| Bağımlılıklar | gstreamer, gst-plugins-base, gst-plugins-good, pulseaudio-server, dbus, rtkit | L12 |
| WirePlumber | `wireplumber.service` (oturum politika yöneticisi) | L46 |
| PulseAudio köprüsü | `pipewire-pulse.socket` → `pipewire-pulse.service` | L48 |
| ALSA yapılandırması | `alsa.conf.d/99-pipewire-default.conf` — varsayılan `!default` PipeWire'e yönlenir | L50 |
| Paket adı | `libpipewire-0.3` | L243 |
| Kurulum durumu | K028'de DAĞITIM ADIMI OLARAK EKLENDİ (paket dosyası bu görevde) | L245 |

### 6.2 PipeWire'in ALSA katmanındaki konumu (kanıt: `linux-core.md` L576–L578)

```
Uygulama → PipeWire / PulseAudio → ALSA → Donanım
Uygulama → ALSA (doğrudan) → Donanım
```

Alternatif katmanlar: ALSA, PulseAudio-PipeWire.

### 6.3 Sınır kuralı (bu dosya kararı, kaynaklara dayalı)

| # | Kural | Gerekçe | Kanıt |
|---|---|---|---|
| B1 | Uygulama ya **doğrudan ALSA**'ya ya da **PipeWire'e** bağlanır; ikisi aynı PCM üzerinde eşzamanlı açılmaz | Çift-open aynı cihaz üzerinde **[UNKNOWN]** davranış üretir; kaynaklarda eşzamanlı kullanım tanımı yok | `linux-core.md` L576–L578 |
| B2 | `!default` varsayılanı PipeWire'e yönleniyorsa, ham `hw:` erişimi için cihaz seçimi açıkça yapılmalı | `99-pipewire-default.conf` varsayılanı değiştirir | `pipewire-modern.md` L50 |
| B3 | RT öncelik verme yetkisi systemd (`LimitRTPRIO=99`) veya yetkili servis üzerinden sağlanmalı | Uygulama içi tek başına yeterli değil | `linux-core.md` L146–L152 |
| B4 | Session politikası WirePlumber'da; uygulama politika dayatmaz | `wireplumber.service` oturum politika yöneticisidir | `pipewire-modern.md` L46 |

⚠️ VERIFICATION REQUIRED: `hw:` doğrudan erişimin PipeWire ile eşzamanlı
kullanımındaki davranış (kilitlenme / EBUSY / paylaşımlı erişim) bu depoda
kanıtlanamıyor — **[UNKNOWN]**.

---

## 7. Bağımlılıklar

### 7.1 İzin verilen (diskte var — doğrulandı)

| Hedef | Neden | Kanıt |
|---|---|---|
| `[[../k025-threading-model/gercek-zamanli-zamanlama]]` | RT zamanlama | diskte var |
| `[[../k018-dma-kesinti-yonetimi/irq-kesinti-yoneticisi]]` | IRQ/DMA | diskte var |
| `[[../k020-linux-core/alsa-native]]` | Kernel ALSA | diskte var |
| `[[../k016-linux-ses/alsa-native]]` | Linux ses katmanı | diskte var |
| `[[../k031-buffer-management/kilitsiz-kuyruklar]]` | lock-free kuyruk | diskte var |
| `[[../k032-latency-optimization/latency-optimization]]` | bütçe | diskte var |
| `[[../k033-platform-ses-suruculeri/index]]` | sürücü öncesi katman | diskte var |

### 7.2 Yasak

`k040`–`k053` — bu klasörler `.ai/architecture/` altında diskte YOK (2026-10-06
`Get-ChildItem` taraması). Eski wiki-linkler `../k04x-...` →
`../k03x-...` olarak taşındı; taşıma kaydı: `index.md` §19.6.

---

## 8. Bilinmeyenler / Doğrulanamayanlar (bu dosya)

| # | Konu | Durum |
|---|---|---|
| U1 | proje-spesifik RT öncelik değeri (hangi prio, hangi politika) | **[UNKNOWN]** |
| U2 | `99-pipewire-default.conf` içeriği (sadece dosya adı ve amacı var) | **[UNKNOWN]** |
| U3 | `hw:` + PipeWire eşzamanlı erişim davranışı | **[UNKNOWN]** |
| U4 | systemd RT biriminin servis adı | **[UNKNOWN]** |
| U5 | priority inheritance'ın bu projede açık olup olmadığı | **[UNKNOWN]** |
| U6 | `snd_pcm_*` API çağrısı içeren kod | **[UNKNOWN]** — repo genelinde 0 eşleşme |
| V1 | `cross-platform-api.md` içindeki `SCHED_RR /* Normal öncelik */` yorumu | ⚠️ VERIFICATION REQUIRED |

---

## 9. Zaman Çizelgesi (bu dosya)

| Tarih | Değişiklik | Kayıt |
|---|---|---|
| 2026-10-06 | Oluşturuldu (v1.0.0) — k039 kapsam daraltma: RT + PipeWire sınırı; kanıt: `_backup\arch-2026-10-06_1057` (`cross-platform-api.md`, `linux-core.md`, `threading-model.md`, `pipewire-modern.md`) | index.md §19.6 |

---

## 10. Bağlantı Bütünlüğü (bu dosya — 2026-10-06)

| Bağlantı | Hedef diskte var mı |
|---|---|
| `[[../k025-threading-model/gercek-zamanli-zamanlama]]` | ✅ |
| `[[../k018-dma-kesinti-yonetimi/irq-kesinti-yoneticisi]]` | ✅ |
| `[[../k020-linux-core/alsa-native]]` | ✅ |
| `[[../k016-linux-ses/alsa-native]]` | ✅ |
| `[[../k031-buffer-management/kilitsiz-kuyruklar]]` | ✅ |
| `[[../k032-latency-optimization/latency-optimization]]` | ✅ |
| `[[../k033-platform-ses-suruculeri/index]]` | ✅ |
| `k040`–`k053` referansı | ❌ bilinçli olarak hiç yok |
