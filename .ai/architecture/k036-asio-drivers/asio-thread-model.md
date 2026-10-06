---
title: "ASIO Thread Modeli"
type: architecture
category: architecture
version: 1.0.0
status: draft
authority: "SSOT — .ai/architecture"
updated: 2026-10-06
---

# K036.5 — ASIO Thread Modeli (RT Thread · Lock-Free · Sıfır Tahsis)

> **Hub:** [[index]] · **İlgili:** [[asio-buffer-callback]] (K036.2) · [[asio-exclusive-mode]] (K036.1) ·
> [[asio-device-lifecycle]] (K036.4)
>
> **Kapsam:** ASIO callback'in çalıştırtıldığı gerçek zamanlı (RT) thread, uygulama thread'inden
> ayrımı, senkronizasyon primitifleri yasağı, sıfır tahsis/lock-free kuralları, Windows thread
> yapılandırması. Kod implementasyonu repo'da YOK (bkz. [[index]] §1.2).

---

## §1 Backup Kanıt Kaynakları (birincil içerik kaynağı)

| # | Backup dosyası | Kanıt satırları | Kullanılan içerik |
|---|---|---|---|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | L238–L247 | Thread öncelik hiyerarşisi — "Audio Processing / TIME_CRITICAL (15) / Dedicated / ASIO callback" |
| 2 | `_backup/.../k0-isletim-sistemi/README.md` | L249–L284 | `LockFreeQueue` — `noexcept push`, `alignas(64)` head/tail, pre-allocated node |
| 3 | `_backup/.../k0-isletim-sistemi/README.md` | L230–L233 | Bellek kuralları: Stack allocation ✅ (64KB limit) · TLS ✅ (pre-allocated) · `alignas(64)` ✅ zorunlu (false sharing) |
| 4 | `_backup/.../k0-isletim-sistemi/windows-api.md` | L152–L173 | `CreateThread` + `SetThreadPriority(TIME_CRITICAL)` + `SetThreadAffinityMask` + `SetThreadDescription` |
| 5 | `_backup/.../k0-isletim-sistemi/windows-api.md` | L175–L185 | Senkronizasyon primitif tablosu: Critical Section/SRW Lock ❌ YASAK · Event/Semaphore ⚠️ sınırlı · Interlocked/`atomic<>` ✅ |
| 6 | `_backup/.../k0-isletim-sistemi/windows-api.md` | L190–L204 | Large page allocation (`VirtualAlloc` + `MEM_LARGE_PAGES`) |
| 7 | `_backup/.../k2-surucu/index.md` | L43–L44 | RT güvenlik: "bellek ayırma yasak, kilitlenme (blocking) yasak, sistem çağrısı yasak" |
| 8 | `_backup/.../k2-surucu/latency-optimization.md` | L85–L133 | `RealTimeScheduler`: `pthread_setschedparam(SCHED_FIFO)` · `setaffinity` · `mlockall` · pre-fault (POSIX tarafı) |
| 9 | `_backup/.../k2-surucu/buffer-management.md` | L36–L98, L178–L226 | Lock-free ring buffer + SPSC queue (`memory_order_acquire/release`) |
| 10 | `_backup/.../k3-ses-motoru/README.md` | L37 | "Noexcept — ASIO callback noexcept zorunlu" |
| 11 | `.ai/AGENTS.md` | §16 Embedded standardı | Zero-allocation · lock-free · noexcept (kural listesi) |
| 12 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/CLAUDE.md` | L21 | Kesme bağlamı kuralları (guardrail) |

---

## §2 Thread Sınıflandırması

### §2.1 Backup öncelik tablosu (Windows karşılığı ile)

Kaynak: `k0-isletim-sistemi/README.md` L240–L247.

| Thread | Backup öncelik | Windows karşılığı | CPU Core | Kullanım |
|---|---|---|---|---|
| **Audio Processing** | TIME_CRITICAL (15) | `THREAD_PRIORITY_TIME_CRITICAL` | **Dedicated** | **ASIO callback (bufferSwitch)** |
| DSP Processing | HIGHEST (10) | `THREAD_PRIORITY_HIGHEST` | Dedicated | EQ, reverb, compressor |
| Network I/O | ABOVE_NORMAL (8) | `ABOVE_NORMAL_PRIORITY_CLASS` | Shared | Streaming, DLNA |
| UI Rendering | NORMAL (0) | Normal | Shared | Arayüz |
| Background Tasks | BELOW_NORMAL (-1) | `BELOW_NORMAL_PRIORITY_CLASS` | Shared | İndirme, indeksleme |
| Idle | LOWEST (-2) | `THREAD_PRIORITY_LOWEST` | Shared | Cleanup, GC |

Windows thread kurulumu (backup `windows-api.md` L154–L173):

```
CreateThread(..., AudioThreadProc, ...)
  → SetThreadPriority(hThread, THREAD_PRIORITY_TIME_CRITICAL)
  → SetThreadAffinityMask(hThread, 1 << audioCoreIndex)   // dedicated çekirdek
  → SetThreadDescription(hThread, L"Audio Processing Thread")
```

### §2.2 Kim hangi thread'de çalışır?

```
  UYGULAMA THREAD'İ (main / UI / servis)          ASIO RT THREAD'İ (driver sahiplenir)
  ┌───────────────────────────────────┐           ┌────────────────────────────────────┐
  │ init / configure / start          │  komut    │ bufferSwitch(index, directProcess) │
  │ SR & buffer seçimi                │──────────→│   → lock-free oku (input ring)      │
  │ exclusive lock sahiplenme         │ (atomic   │   → NevaEngine process (noexcept)   │
  │ hata kurtarma / fallback kararı   │  bayrak)  │   → lock-free yaz (output ring)     │
  │ log, UI güncelleme                │◄──────────│   → sayaçlar (atomic counter)       │
  └───────────────────────────────────┘  atomic   └────────────────────────────────────┘
                                               TIME_CRITICAL + dedicated core
```

⚠️ VERIFICATION REQUIRED: ASIO spesifikasyonunda `bufferSwitch` çağrısının hangi thread'de
yapıldığı (driver'ın kendi thread'i mi, uygulama çağırdığı thread mi) backup'ta yoktur;
backup yalnızca "Audio Processing thread = ASIO callback" öncelik atamasını verir
(`k0 README.md` L242).

---

## §3 RT Thread Yasakları ve İzinli Olanlar

### §3.1 Kural tablosu (birleşik kanıt)

| İşlem | RT thread'de | Kanıt |
|---|---|---|
| Bellek ayırma (`new`/`malloc`/`delete`) | ❌ YASAK | k2 index.md L44; `.ai/AGENTS.md` §16.1 |
| Mutex / lock_guard / unique_lock | ❌ YASAK | `.ai/AGENTS.md` §16.2 |
| Critical Section, SRW Lock (Windows) | ❌ YASAK | windows-api.md L179–L180 |
| Event / Semaphore bekleme | ⚠️ Sınırlı | windows-api.md L181–L182 |
| `Interlocked*` / `std::atomic<>` | ✅ İzinli | windows-api.md L183–L184 |
| Stack allocation | ✅ İzinli (64KB limit) | k0 README L230 |
| Thread-local storage | ✅ İzinli (pre-allocated) | k0 README L231 |
| `alignas(64)` | ✅ Zorunlu (false sharing) | k0 README L232; `.ai/AGENTS.md` §16.5 |
| Sistem çağrısı | ❌ YASAK | k2 index.md L44 |
| Blocking I/O | ❌ YASAK | k2 index.md L44 |
| `noexcept` olmayan callback | ❌ YASAK | k3 README L37; `.ai/AGENTS.md` §16.3 |

### §3.2 RT güvenlik özeti (backup cümlesiyle)

> "Tüm K2 kodu, kesme.Context içinde çalışabilir: bellek ayırma yasak, kilitlenme (blocking)
> yasak, sistem çağrısı yasak." — `k2-surucu/index.md` L44

---

## §4 Lock-Free Veri Yapısı Kuralları

### §4.1 SPSC ring buffer (ASIO girdi/çıktı yolu)

Backup kanıtı: `buffer-management.md` L36–L98. Çekirdek prensip (kod backup'tan aynen taşınır,
bu dosyada özet iskelet):

```
write (tek producer — uygulama tarafı ya da callback):
  currentWrite = writeIndex.load(relaxed)
  currentRead  = readIndex.load(acquire)       ← kilit yerine acquire okuma
  if (count > capacity - (currentWrite - currentRead)) return false
  veriyi kopyala
  writeIndex.store(currentWrite + count, release)

read (tek consumer):
  mirror işlem; readIndex.store(..., release)
```

### §4.2 SPSC kuyruk (SPSCQueue) — backup L178–L226

Tek eleman push/pop; dolu/boş durumu iki atomik işaretçi ile — `mutex` yok.
ASIO tarafında kullanılacak sahne (parametre, komut) için uygundur.

### §4.3 Kuyruk kuralı (k0 lock-free queue)

Backup `k0 README.md` L255–L281:

- `push` → `noexcept`
- node **pre-allocated** (RT içinde `getNode()` tahsis yapmaz — düğüm havuzu önceden doldurulur)
- `_head` / `_tail` → `alignas(64)` (false sharing önlenir)

⚠️ VERIFICATION REQUIRED: düğüm havuzunun RT dışı thread'de doldurulmasına dair akış
(explicit pool refill döngüsü) backup'ta tanımlı değil; yalnız "Pre-allocated" yorumu var (L258).

### §4.4 Double buffering alternatifi — ve mutex yasağı çelişkisi

Backup `buffer-management.md` L127–L176'daki `DoubleBuffer` sınıfı `std::mutex` +
`lock_guard` **kullanır**. Bu, RT thread yasağıyla (`.ai/AGENTS.md` §16.2) çelişir.

| | Backup DoubleBuffer (L127–L176) | Lock-free SPSC (L36–L98) |
|---|---|---|
| Senkronizasyon | `std::mutex` ❌ RT'de yasak | atomik işaretçiler ✅ |
| Kullanım yeri | RT dışı / kavramsal taslak | **ASIO callback yolu (tercih edilen)** |

**Çelişki kararı:** RT yolu lock-free SPSC'dir; backup DoubleBuffer kodu RT-dışı taslak olarak
korunur, callback'e sokulmaz. ⚠️ VERIFICATION REQUIRED (backup bu ayrımı yapmıyor).

---

## §5 Bellek ve CPU Ayarları

| Ayar | Backup değeri | Kaynak |
|---|---|---|
| Bellek kilidi (POSIX) | `mlockall(MCL_CURRENT \| MCL_FUTURE)` | latency-optimization.md L113–L116 |
| Stack pre-fault (POSIX) | 8MB, sayfa sayfa dokunma | latency-optimization.md L118–L131 |
| Large page (Windows) | `VirtualAlloc` + `MEM_LARGE_PAGES` | windows-api.md L192–L204 |
| CPU affinity (Windows) | `SetThreadAffinityMask(1 << audioCoreIndex)` | windows-api.md L169 |
| Öncelik (Windows) | `THREAD_PRIORITY_TIME_CRITICAL` | windows-api.md L166 |
| Hedef CPU (RT) | < %5 | latency-optimization.md L367 |

⚠️ VERIFICATION REQUIRED: Windows'ta `SetProcessWorkingSetSize` / locked-page limit ayarı
backup'ta geçmiyor (yalnız large page alloc var).

---

## §6 Uygulama ↔ RT İletişim Desenleri

```
Uygulama thread                    RT (bufferSwitch)
     │                                   │
     │  1. atomic komut bayrağı yaz      │
     │     (parametre pre-set edilir)    │
     │──────────────────────────────────→│
     │                                   │  2. callback başında bayrağı oku
     │                                   │     (memory_order_acquire)
     │  3. atomic sayaçları oku          │  4. işi bitir, sayacı artır
     │◄──────────────────────────────────│     (memory_order_release)
     │                                   │
  Yasak: RT→uygulama için mutex, queue'da blocking pop,
         log write, dosya/registry erişimi (sistem çağrısı)
```

İzinli geçiş araçları: `std::atomic<>` bayrak/sayaç, SPSC lock-free kuyruk (tamponlanmış
mesaj), pre-allocated çift tampon.

---

## §7 Performans Hedefleri

| Metrik | Hedef | Kanıt |
|---|---|---|
| RT CPU kullanımı | < %5 | latency-optimization.md L367 |
| Jitter | < 10μs | latency-optimization.md L366 |
| Bellek kilidi | %100 | latency-optimization.md L368 |
| Underrun oranı | < %0.01 | latency-optimization.md L369 |
| Write/Read latency (buffer) | < 1μs | buffer-management.md L361–L362 |

---

## §8 Wiki-Bağlantılar

- Hub: [[index]]
- Callback sözleşmesi: [[asio-buffer-callback]]
- Buffer seçimi ve latency etkisi: [[asio-latency-hesap]]
- Exclusive lock (RT thread'in sahiplendiği kaynak): [[asio-exclusive-mode]]
- Durum makinesi: [[asio-device-lifecycle]]
- Hata durumunda RT davranışı: [[asio-hata-yonetimi]]
- SDK callback sınıfları: [[asio-sdk-entegrasyon]]

---

## §9 Backup Kanıt Özeti (bu dosyaya ait)

```
backup kanıtı: k0-isletim-sistemi/README.md   L238-L247  (thread öncelik hiyerarşisi — ASIO callback TIME_CRITICAL)
backup kanıtı: k0-isletim-sistemi/README.md   L249-L284  (LockFreeQueue, noexcept, alignas(64))
backup kanıtı: k0-isletim-sistemi/README.md   L230-L233  (stack/TLS/alignas bellek kuralları)
backup kanıtı: k0-isletim-sistemi/windows-api.md L152-L173 (CreateThread + öncelik + affinity)
backup kanıtı: k0-isletim-sistemi/windows-api.md L175-L185 (senkronizasyon primitif yasağı tablosu)
backup kanıtı: k0-isletim-sistemi/windows-api.md L190-L204 (large page alloc)
backup kanıtı: k2-surucu/index.md             L43-L44    (RT güvenlik: tahsis/blocking/syscall yasak)
backup kanıtı: k2-surucu/latency-optimization.md L85-L133 (RT scheduling, mlockall, pre-fault)
backup kanıtı: k2-surucu/buffer-management.md L36-L98, L178-L226 (lock-free ring + SPSC)
backup kanıtı: k2-surucu/buffer-management.md L127-L176 (DoubleBuffer mutex'li — RT-dışı, çelişki §4.4)
backup kanıtı: k3-ses-motoru/README.md        L37        (ASIO callback noexcept zorunlu)
backup kanıtı: k2-surucu/CLAUDE.md            L21        (guardrail: kesme bağlamı)
kanıt: .ai/AGENTS.md §16 (Embedded: zero-allocation / lock-free / noexcept / alignas(64))
```

**Çelişki raporu:** Backup `buffer-management.md` DoubleBuffer kodu mutex kullanır (L140, L149, L159);
`.ai/AGENTS.md` §16.2 mutex'i RT thread'de yasaklar. Karar: §4.4 — mutex'li taslak RT-dışı sayılır.

---

*Backmatter: `version: 1.0.0` · `status: draft` · `updated: 2026-10-06` · `authority: "SSOT — .ai/architecture"`*
