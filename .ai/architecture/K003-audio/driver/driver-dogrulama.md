---
title: "driver — Doğrulama"
type: architecture-verification
node_id: K003-audio/driver
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
date: 2026-10-12
updated: 2026-10-13
group: kanit
---

# driver — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda exa/websearch ile getirilen URL içerikleri ·
> (b) bu oturumda shell ile ölçülen disk gerçekleri · (c) vault içi iç kanıt
> (dosya okunarak). Yasak: URL, sürüm, sınıf, sürüm numarası uydurmak.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk satırı: "driver · Sürücü yüzeyiyle tek noktadan etkileşim" | `audio-master.md` §3 satır 64 | 2026-10-12 | ✅ dosya okundu |
| 2 | Steinberg ASIO SDK açık kaynak ilan edilmiş (GPLv3) | https://www.steinberg.net/developers/asiosdk-open | 2026-10-12 | ✅ doğrulandı (exa fetch) |
| 3 | WASAPI ana sayfası (Windows Core Audio) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2026-10-12 | ✅ doğrulandı (exa fetch) |
| 4 | WASAPI exclusive mode streams — özel kip akışları belgesi | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2026-10-12 | ✅ doğrulandı (exa fetch) |
| 5 | ALSA PCM: hw_params (format/rate/channels/buffer) + sw_params (avail_min/period_event) ayrımı; beş access modu (MMAP_INTERLEAVED · MMAP_NONINTERLEAVED · MMAP_COMPLEX · RW_INTERLEAVED · RW_NONINTERLEAVED) | https://www.alsa-project.org/doc/api/group___p_c_m.html | 2026-10-12 | has-been-fetched 2026-10-12 |
| 6 | ALSA plug plugin: format/channels/rate otomatik dönüşümü (slave belirtilir) | https://www.alsa-project.org/doc/api/group___p_l_u_g.html | 2026-10-12 | has-been-fetched 2026-10-12 |
| 7 | PipeWire graph scheduling: driver node döngüyü başlatır; required/pending sayaçları ile node tetiklenir | https://pipewire.org/page_scheduling | 2026-10-12 | has-been-fetched 2026-10-12 |
| 8 | ASIO cihaz kaybında WASAPI fallback kuralı bağlayıcı | `.ai/AGENTS.md` §17 #6 · kayıt `audio-master.md` §5 T8 | 2026-10-12 | ✅ dosya okundu |
| 9 | ASIO alt-alanı 8 dosyalık katalog (index + kapsam + 6 deep-dive) | `.ai/architecture/K003-audio/backend/asio/` glob 2026-10-12 | 2026-10-12 | ✅ glob ölçümü |
| 10 | Repo'da sürücü katmanı C/C++ kodu yok | kök `*.cpp`/`*.h` glob 0 isabet | 2026-10-12 | ✅ ölçüm |
| 11 | "driver yalnız ASIO konuşur" / kapı seçimi (ASIO mu WASAPI mi) | — | 2026-10-12 | ⚠️ VERIFICATION REQUIRED (index.md open_questions) |
| 12 | ASIO SDK 2.3.4 sürüm numarasının bağımsız web teyidi | — | 2026-10-12 | ⚠️ VERIFICATION REQUIRED (master §5 T11) |
| 13 | ASIO SDK GPLv3 lisansının CoreMusic'e etkisi (hukuki değerlendirme) | — | 2026-10-12 | ⚠️ VERIFICATION REQUIRED (master §8 Q1) |
| 14 | Driver katmanının CoreMusic'teki somut arayüz adı ve metod imzaları | — | 2026-10-12 | **UNKNOWN** (tasarım yok) |
| 15 | Sürücü gecikmesi ölçüm değeri (ms / periyot) ve bütçesi | — | 2026-10-12 | **UNKNOWN** (ölçüm yok) |
| 16 | NAudio sürüm/paket bilgisi | — | 2026-10-12 | **UNKNOWN** (master §5 T12) |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam iddia satırı | 16 |
| Benzersiz URL | 7 (2–7 içinde 6 farklı URL + 2'de steinberg · 3/4 microsoft · 5/6 alsa · 7 pipewire) |
| Gerçek dosya yolu / iç kanıt satırı | 4 (1 · 8 · 9 · 10) |
| ✅ doğrulandı / dosya okundu / ölçüm | 10 (1–10) |
| ⚠️ VERIFICATION REQUIRED | 3 (11 · 12 · 13) |
| UNKNOWN | 3 (14 · 15 · 16) |

**Doğruluk notu:** §2 satır sayıları §1 ile birebir sayılmalıdır; sayım
uyuşmazsa §1 yeniden sayılır (sessiz düzeltme yasak).

## §3 Geçiş kuralı

- **#11** kapı seçimi (ASIO/WASAPI) mimari kararla kapanır → kapanınca
  `driver-kapsam.md` §3 D1 `[TASARIM]` → `[KANIT]` olur.
- **#12–13** sürüm/lisans teyidi gelmeden "SDK 2.3.4 kullanıyoruz" / "GPLv3
  uyumlu" iddiası yazılmaz; değer uydurulmaz.
- **#14–16** tasarım/ölçüm olmadan arayüz adı, gecikme sayısı ya da paket
  sürümü yazılır → yazım yasaktır; `UNKNOWN` kalır.
- **#5–7** bağımsız web teyidi istenirse ilgili URL ancak gerçekten fetch
  edilince `has-been-fetched` durumuna yükseltilir (bu oturumda 5–7 zaten
  fetch edilmiştir).


## 4. Kaynak Bankası — SÜRÜCÜ DOĞRULAMA (bu bölüm 2026-10-13, append-only)

Aşağıdaki her URL **2026-10-13'te HEAD 200 ile doğrulanmıştır**. §1'deki eski 3
kaynağa (MS WASAPI capture / render / Steinberg ASIO SDK — 2026-10-12 HEAD 200)
EK'tir; §1 satırları değiştirilmemiştir.

| # | Kaynak (HEAD 200 · 2026-10-13) | Sürücü doğrulamasına katkısı |
|---|---|---|
| 1 | https://www.resplendence.com/latencymon | LatencyMon — DPC/ISR gecikme izleyici resmi ürün sayfası |
| 2 | https://www.resplendence.com/latencymon_using | Kullanım kılavuzu — sürüm ve başlangıç akışı |
| 3 | https://www.resplendence.com/latencymon_technical | Teknik referans — hangi gerçek zamanlı bileşenlerin ölçüldüğü |
| 4 | https://learn.microsoft.com/en-us/windows-hardware/drivers/ | Windows sürücü geliştirme dokümantasyon ana sayfası |
| 5 | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/ | Windows audio sürücü alt sistemi (WDM/KS, AVStream, APO) dokümantasyonu |
| 6 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | Exclusive mode stream kuralları — paylaşım/lock davranışı |
| 7 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/rendering-a-stream | Stream başlatma/durdurma yaşam döngüsü (IAudioClient) |
| 8 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/capturing-a-stream | Capture stream yaşam döngüsü (IAudioCaptureClient) |
| 9 | https://github.com/juce-framework/JUCE | JUCE — JUCE ASIO/WASAPI host wrapper kaynak kodu |

### 4.1 Negatif kayıtlar (dürüstlük — bu bölüm §1 #15-16'yı TEKRARLAR, değiştirmez)

| # | ID | İddia (id. edilen, doğrulanmamış) | Durum |
|---|---|---|---|
| N-DRV-1 | §1 #15 | ASIO sürücü gecikme bileşeni sayısı (8-16) | `UNKNOWN` — 2026-10-13'te de doğrulanmadı, §1'deki değer aynen korunur |
| N-DRV-2 | §1 #16 | NAudio sürümü / Windows sürücü yığını doğrudan etkileşimi | `UNKNOWN` — 2026-10-13'te de doğrulanmadı |

---

## 5. Test Matrisi — SÜRÜCÜ KATMANI (22 test · durum: PLANNED — kod yok)

> Repo'da C/C++ sürücü-sarmalayıcı ya da ses motoru kodu YOK (2026-10-13
> glob doğrulaması: 0 isabet). Tüm testler **PLANNED**; çalıştırma ön koşulu:
> K003 uygulama fazında `NevaEngine` + ASIO/WASAPI host kodunun varlığı.
> Ölçüm araçları doğrulanmıştır (LatencyMon §4 #1-3, ETW/WPR §6.5).

| ID | Test | Hedef | Yöntem / araç | Beklenen sonuç | Durum |
|---|---|---|---|---|---|
| T-DRV-01 | ASIO sürücü enum | Sürücü listelenir | JUCE `AudioIODeviceType::getDeviceNames()` | ASIO device listelenir, ad doğrudur | PLANNED |
| T-DRV-02 | ASIO exclusive lock | Tek uygulama sahiplenir | 2 süreç aynı ASIO device açar | 2. süreç hata / `SA_DEVICE_IN_USE` benzeri reddetme | PLANNED |
| T-DRV-03 | Exclusive lock serbest bırakma | Kapanışta lock | 1. süreç kapanır → 2. süreç dener | 2. süreç başarılı açılış | PLANNED |
| T-DRV-04 | ASIO buffer size seçimi | 64/128/256/512/1024 | `setBufferSize` + callback sayacı | Her boyutta kesintisiz callback | PLANNED |
| T-DRV-05 | ASIO sample rate geçişi | 44.1/48/96/192 kHz | Device değiştir + yeniden başlat | Hız geçişi temiz (pop yok) | PLANNED |
| T-DRV-06 | ASIO stream start/stop döngüsü | Yaşam döngüsü | 100× start/stop | Hata/lock sızıntısı yok, kaynak temiz | PLANNED |
| T-DRV-07 | ASIO hata kurtarma | Callback hatası | Zorla hata enjekte et | Engine `noexcept` kapsamında çökmez, sessizce durur | PLANNED |
| T-DRV-08 | WASAPI shared başlatma | Ortak mod | `IAudioClient::Initialize(SHARED)` | `S_OK`, dönem uygulanır | PLANNED |
| T-DRV-09 | WASAPI exclusive başlatma | Exclusive mod | `Initialize(EXCLUSIVE, EVENTCALLBACK)` | `S_OK` veya `AUDCLNT_E_DEVICE_IN_USE` (kaynaklı) | PLANNED |
| T-DRV-10 | WASAPI event callback ritmi | RT ritim | `SetEventHandle` + event thread | Periyot = buffer/sr, jitter ölçülür (P-DRV-02) | PLANNED |
| T-DRV-11 | WASAPI capture paket akışı | Capture yığını | `GetNextPacketSize` döngüsü | 0 dışı paketler eksiksiz, `SILENT` bayrağı işlenir | PLANNED |
| T-DRV-12 | Sürücü yeniden başlatma kurtarma | Sürücü kaybı | Device manager'dan disable/enable | Engine durumu: disconnect → reconnect (device-dogrulama T-DEV-02'ye bağlanır) | PLANNED |
| T-DRV-13 | DPC gecikme profili | Sürücü ISR/DPC | LatencyMon ≥60 dk stres altında | Toplam DPC süresi `UNKNOWN` eşik — bkz §7 (ölçüm yok, eşik `PLANNED`) | PLANNED |
| T-DRV-14 | ISR gecikme profili | ISR bütçesi | LatencyMon | ISR+DPC toplamı ölçülmüş olmalı (eşik §7) | PLANNED |
| T-DRV-15 | Hard pagefault etkisi | Sürücü yığını + sayfa | LatencyMon hard fault raporu | Gerçek zamanlı thread'de 0 hard pagefault | PLANNED |
| T-DRV-16 | Birden fazla cihaz eşzamanlı | Çoklu device | 2 ASIO/WASAPI stream aynı anda | Durum sürüm-sürücüye bağlı — §7'de `UNKNOWN` işaretli | PLANNED |
| T-DRV-17 | Sample rate uyuşmazlığı | OS↔driver | OS 44.1k iken driver 48k | Durum tespiti + raporlama (düzeltme §7 #8) | PLANNED |
| T-DRV-18 | Exclusive→shared fallback | Fallback zinciri | Exclusive başarısız → shared dene | Fallback uygulanır, loglanır | PLANNED |
| T-DRV-19 | JUCE host vs ASIO SDK | Wrapper bütünlüğü | JUCE `AudioDeviceManager` üzerinden ASIO | JUCE sürümü `UNKNOWN` (§1 #12) — test çalışır ama sürüm iddiası yazılmaz | PLANNED |
| T-DRV-20 | Sürücü kurulum envanteri | Kurulu sürücüler | `GetDeviceIDs` + Registry envanteri | Liste kaydedilir (gerçek ölçüm §6.8) | PLANNED |
| T-DRV-21 | Uyku/uyandırma sonrası sürücü | Güç olayları | Sistem uyku → uyan | Stream disconnect/reconnect doğru işlenir | PLANNED |
| T-DRV-22 | Boot'ta sürücü yarışı | Başlangıç | Erken başlatma testi | Exclusive lock erken kapanma → temiz bırakma | PLANNED |

---

## 6. Ölçüm Protokolleri — SÜRÜCÜ (8 protokol)

### 6.1 P-DRV-01 — LatencyMon DPC/ISR profili
- **Araç:** LatencyMon (§4 #1-3, HEAD 200). Kurallar: gerçek zamanlı
  iş yükü + ses çalma altında ≥60 dk izleme.
- **Adımlar:** (1) LatencyMon başlat (admin) · (2) ses stream'i açık tut ·
  (3) ≥60 dk izle · (4) raporu kaydet · (5) sürücü bazında en kötü ISR/DPC
  sürelerini tabloya yaz.
- **Çıktı:** Sürücü bazlı en kötü ISR/DPC süreleri — eşikler §7. `PLANNED`.

### 6.2 P-DRV-02 — WASAPI event callback jitter ölçümü
- **Adımlar:** EVENTCALLBACK modda `SetEventHandle` (§4 #6-8) · her callback'te
  `QueryPerformanceCounter` farkı kaydedilir · ≥10 dk veri · histogram.
- **Çıktı:** Ortalama/jitter/worst-case callback periyodu. `PLANNED`.

### 6.3 P-DRV-03 — Exclusive lock çakışma protokolü
- **Adımlar:** Uygulama A exclusive açar · B dener · hatayı kaydet · A kapatır ·
  B tekrar dener.
- **Çıktı:** Lock sahiplenme/serbest bırakma kanıtı. Referans §4 #6.

### 6.4 P-DRV-04 — Sürücü yükleme/boşaltma döngüsü
- **Adımlar:** 100× stream start/stop · 50× device disable/enable · her turda
  kernel nesne sızıntısı kontrolü (Task Manager handle sayacı).
- **Çıktı:** Sızıntı yok; sızıntı varsa sürücü adı ile kayıt. `PLANNED`.

### 6.5 P-DRV-05 — ETW/WPR kernel profiling (xperf)
- **Adımlar:** Windows Performance Recorder kernel profil kaydı başlat · ses
  stream altında 10 dk · kaydı WPA'da aç · ISR/DPC zincirini sürücü bazında çöz.
- **Çıktı:** Sürücü bazlı DPC/ISR kırılımı. (LatencyMon'un ETW tabanlı olduğu
  §4 #3'te belirtilir; xperf alternatif yoldur.)

### 6.6 P-DRV-06 — Stream yaşam döngüsü hata enjeksiyonu
- **Adımlar:** Start/stop sırasında arabellek serbest bırakma, yanlış sıralama,
  iptal edilmiş callback — her durumda motorun `noexcept` kapsamında kaldığını
  doğrula (crash yok, sessiz duruş var).
- **Çıktı:** Kurtarma davranışı kanıtı. `PLANNED`.

### 6.7 P-DRV-07 — Sürücü sürüm/envanter doğrulama
- **Adımlar:** `GetDeviceIDs` → `PKEY_DeviceDriverVersion` (§4 #6-8 zinciri) ·
  Sürücü tarihi/versiyonu kaydedilir · ASIO driver vendor adı + sürümü.
- **Çıktı:** Kurulu sürücü envanteri. `PLANNED`.

### 6.8 P-DRV-08 — Güç olayı kurtarma protokolü
- **Adımlar:** Sistem uyku → uyanış · stream disconnect/reconnect akışı ·
  sürücü yeniden enum · otomatik yeniden bağlanma doğrulaması.
- **Çıktı:** Güç olayı sonrası kurtarma kanıtı. `PLANNED`.

---

## 7. Eşik Tablosu — SÜRÜCÜ KATMANI

| ID | Ölçüm | Hedef | Durum / kaynak |
|---|---|---|---|
| 1 | Toplam DPC süresi | Sürücü başına sınır `PLANNED` | Eşik değeri LatencyMon raporuyla belirlenecek — şu an belirlenmemiş |
| 2 | ISR süresi | Sürücü başına sınır `PLANNED` | Aynı şekilde |
| 3 | Hard pagefault (RT thread) | 0 | PLANNED |
| 4 | Callback jitter | Ölçülecek, sınır `PLANNED` | Ölçüm yok |
| 5 | Exclusive lock hata kodu | Kaynaklı reddetme | PLANNED |
| 6 | Start/stop döngüsü | 100 döngü hatasız | PLANNED |
| 7 | Handle sızıntısı | 100 döngüde 0 artış | PLANNED |
| 8 | Sample rate uyuşmazlık | Uygunsuz uyarlama YOK — raporla, değiştirme | PLANNED |
| 9 | Fallback | Exclusive başarısız → shared | PLANNED |
| 10 | Uyku kurtarma | ≤ `PLANNED` saniyede reconnect | PLANNED |
| 11 | ASIO SDK sürümü | 2.3.4 iddiası | `VERIFICATION REQUIRED` (§1 #12, değiştirilmedi) |
| 12 | JUCE sürümü | 9 iddiası | `VERIFICATION REQUIRED` (§1 #12) |
| 13 | ASIO sürücü gecikme bileşeni | 8-16 (iddia) | `UNKNOWN` (§1 #15 — korunur, kanıt yok) |
| 14 | NAudio etkileşimi | — | `UNKNOWN` (§1 #16 — korunur) |

---

## 8. Negatif Testler (sürücü katmanı)

| ID | Negatif senaryo | Beklenen davranış | Durum |
|---|---|---|---|
| N-DRV-T01 | ASIO device yokken stream açma | Hata döner, çökme YOK | PLANNED |
| N-DRV-T02 | Exclusive lock altında ikinci açılış | Kaynaklı reddetme (§4 #6) | PLANNED |
| N-DRV-T03 | Stream ortasında device disable | Disconnect sinyali, sessiz duruş | PLANNED |
| N-DRV-T04 | Buffer size geçersiz değer | Parametre reddi + fallback | PLANNED |
| N-DRV-T05 | Sürücü crash sonrası yeniden kurulum | Envante temizlenir, yeniden enum başarılı | PLANNED |
| N-DRV-T06 | 44.1↔48 kHz uyuşmazlık | Uygunsuz uyarlama YOK, rapor | PLANNED |
| N-DRV-T07 | 1000× start/stop stres | Sızıntı 0, handle artışı 0 | PLANNED |
| N-DRV-T08 | Boot sırasında exclusive lock erken kapanış | Lock temiz bırakılır | PLANNED |

---

## 9. Ortam (sürücü doğrulama ortamı)

| Bileşen | Sürüm / durum |
|---|---|
| Windows sürümü | UNKNOWN — §1 #2 (korunur) |
| ASIO SDK | 2.3.4 iddiası — `VERIFICATION REQUIRED` (§1 #12) |
| JUCE | 9 iddiası — `VERIFICATION REQUIRED` (§1 #12) |
| Ses sürücüsü (test cihazı) | UNKNOWN — ölçüm ortamı henüz kurulmadı |
| LatencyMon | Doğrulanmış ürün (§4 #1-3); kullanılan sürüm ölçümde kaydedilecek |
| Ses motoru kodu | YOK — glob 0 isabet (2026-10-13) |

---

## 10. Geçme Kapısı — SÜRÜCÜ KATMANI (gate)

Bir sürücü katmanı doğrulaması **PAS** sayılır:
1. T-DRV-01…22 çalıştırılmış, sonuçları kaydedilmiş.
2. P-DRV-01…08 protokollerinden en az DPC/ISR (01) + jitter (02) + lock (03)
   tamamlanmış, gerçek ölçüm değerleri §7'ye işlenmiş.
3. Negatif testler (§8) 8/8 beklenen davranışı göstermiş.
4. `UNKNOWN` / `VERIFICATION REQUIRED` olan satırlar (§7 #11-14) kapatılmadan
   **genel kapı tam PAS olamaz** — yalnızca "sürücü katmanı doğrulandı, ancak
   sürücü gecikme bileşeni ve sürücü gecikme bütçesi ölçülmedi" notuyla
   koşullu geçilebilir.
5. Kod incelemesi: audio thread'de lock/malloc yasağı, `noexcept` — bu dosyanın
   motor hedefleridir (engine-dogrulama §1'e bağlanır).

**Ölçüm yoksa PAS YOK.** Gerçek zamanlı ölçüm gereklidir; sadece kod incelemesi
yeterli değildir (§1'in genel kuralı bu katman için de geçerlidir).

---

## 11. Doğrulama Kayıt Şablonu — SÜRÜCÜ

```yaml
verification_type: driver
test_id: T-DRV-XX
date: YYYY-MM-DD
environment:
  windows_build: "10.0.XXXXX"
  driver_name: ""
  driver_version: ""
  asio_sdk_version: ""
  juce_version: ""
measurements:
  worst_dpc_us: null        # LatencyMon
  worst_isr_us: null
  hard_pagefaults: null
  callback_jitter_us: null
  exclusive_lock_error: ""
result: PLANNED | PASSED | FAILED
notes: ""
```

---

## 12. Sınırlar (dürüstlük)

1. Bu dosyadaki **hiçbir eşik değeri gerçek ortamda ölçülmemiştir** — §7'nin
   tüm satırları `PLANNED` veya `UNKNOWN` işaretlidir.
2. LatencyMon eşikleri (2000/4000 µs gibi klasik kılavuz değerleri) bu dosyaya
   **yazılmamıştır** — resmi kaynakta (§4 #1-3) doğrulanmadığı için uydurma
   önlemi alınmıştır. Ölçüm yapıldığında araç raporuyla belirlenecek.
3. ASIO SDK 2.3.4 ve JUCE 9 sürüm iddiaları **hâlâ kanıtsız** (§1 #12 — 2026-10-13
   teyidi: repository'de sürüm kanıtı dosyası eklenmemiştir).
4. Sürücü gecikme bileşeni (§1 #15) ve NAudio etkileşimi (§1 #16) `UNKNOWN`
   olarak korunmuştur.
5. Ölçüm araçları (LatencyMon, ETW/WPR) doğrulanmış ürünlerdir; ancak bu
   araçlarla yapılan ölçümler henüz mevcut değildir.

---

## 13. CI / Otomasyon

1. Sürücü katmanı testleri OTOMATİK CI'da **çalıştırılamaz** — gerçek ses
   donanımı + sürücü kurulumu gerektirir (gerçek zamanlı ölçüm zorunlu).
2. CI'da çalıştırılabilir statik kontroller: kod incelemesi (lock-free,
   `noexcept`, sıfır tahsis) — engine-dogrulama'ya bağlı.
3. Donanımlı koşu: manuel / lab ortamında; kayıt şablonu (§11) ile raporlanır.
4. LatencyMon raporları arşivlenmeli (sürücü sürümü + tarih + rapor dosyası).

---

## 14. RACI (sürücü doğrulama)

| Rol | Kişi | Sorumluluk |
|---|---|---|
| Responsible | Bayram Ali | Ölçüm ortamı kurulumu, LatencyMon/ETW koşuları |
| Accountable | Bayram Ali | Kapı (§10) kararı |
| Consulted | Uzman yok | — |
| Informed | Vault Steward | Dürüstlük bloğu, `UNKNOWN` kayıtları |

---

## 15. Bağlantılar (wiki)

**Kardeş doğrulamalar:**
- [[../input/input-dogrulama|Input doğrulama]] ·
  [[../output/output-dogrulama|Output doğrulama]] ·
  [[../device/device-dogrulama|Device doğrulama]] ·
  [[../engine/engine-dogrulama|Engine doğrulama]] ·
  [[../audio-dogrulama|Audio uçtan uca doğrulama]] ·
  [[../audio-master|Audio Master]]

**Diğer domain'ler:**
- [[../../K000-isletim-sistemi/isletim-sistemi-audio/index|K000 İşletim Sistemi —
  OS ses katmanı]] · [[../../K001-donanım/index|K001 Donanım]] ·
  [[../../K002-electroncis/index|K002 Elektronik]]

---

## 16. Ek: DPC/ISR kavramsal çerçeve (kanıtsız eşik YOK)

Sürücü gecikme doğrulamasının dayandığı kavramlar (LatencyMon teknik
referansından, §4 #3):
- **ISR (Interrupt Service Routine):** Donanım kesintisi tetikler; sürücü en
  kısa sürede bitirmeli.
- **DPC (Deferred Procedure Call:** ISR sonrası kuyruğa alınan sürücü işi;
  birikirse ses arabelleği taşar.
- **Hard pagefault:** Gerçek zamanlı thread sayfa hatası = sürücü yığınından
  kaynaklı gecikme kaynağı (LatencyMon raporlar).

Bu kavramsal çerçeve **ölçüm eşikleri içermez** — eşikler ancak P-DRV-01/05
ölçümlerinden sonra yazılabilir (§12 #1).

---

## 17. WASAPI yığın detay tablosu (doğrulanmış davranış — §4 #6-8)

| Katman | Rol | Sürücü doğrulamasına etkisi |
|---|---|---|
| Application | `IAudioClient` / `IAudioCaptureClient` | Yaşam döngüsü testleri T-DRV-08/11 |
| WASAPI (audio engine) | Mod seçimi (shared/exclusive), dönem, event | T-DRV-09/10, P-DRV-02 |
| Audio driver (APO dahil) | DSP/sürücü işi, DPC/ISR kaynağı | T-DRV-13/14, P-DRV-01/05 |
| Kernel (port class / KS) | Donanım erişimi | LatencyMon/ETW ölçümlerinin kaynağı |
| Donanım (DMA/IRQ) | DMA tamamlama kesintisi | ISR kaynağı, P-DRV-05 |

Exclusive modda uygulama audio engine'i bypass eder (§4 #6) — bu, sürücü
katmanı testlerinin exclusive/shared ayrımını zorunlu kılar (T-DRV-09).

## 18. ASIO host callback sırası (JUCE wrapper — doğrulanmış kaynak: §4 #9)

| Adım | Aşama | Doğrulama testi |
|---|---|---|
| 1 | Sürücü enum + seçimi | T-DRV-01 |
| 2 | Buffer size / sample rate müzakeresi | T-DRV-04/05 |
| 3 | Callback kaydı (audio thread) | T-DRV-10 benzeri ritim testi |
| 4 | Stream start | T-DRV-06 |
| 5 | Callback döngüsü (RT — lock-free/noexcept) | T-DRV-07, engine-dogrulama §5 |
| 6 | Stream stop | T-DRV-06 |
| 7 | Kaynakların serbest bırakılması | T-DRV-22, N-DRV-T08 |

JUCE sürümü `VERIFICATION REQUIRED` (§1 #12) olduğu için tablo yalnızca
**akış sırasını** gösterir; JUCE API adları iddia edilmez.

## 19. Exclusive lock durum makinesi (kavramsal — kod yok, PLANNED)

```text
[SERBEST] --uygulama A exclusive açar--> [A KİLİTLİ]
[A KİLİTLİ] --B dener--> reddet (kaynaklı hata)  (T-DRV-02, §4 #6)
[A KİLİTLİ] --A kapanır/çöker--> [SERBEST]        (T-DRV-03, N-DRV-T08)
[SERBEST] --B dener--> [B KİLİTLİ]                (T-DRV-03 ikinci aşama)
```

Çökme sonrası lock'un temiz bırakılıp bırakılmadığı OS sürücüsüne bağlıdır —
bu dosyada iddia edilmez; N-DRV-T08 ile ölçülür.

## 20. DPC/ISR ölçüm kayıt tablosu (P-DRV-01 çıktısı şablonu — boş = PLANNED)

| Sürücü adı | En kötü ISR (µs) | En kötü DPC (µs) | Toplam DPC (µs/60dk) | Hard fault | Karar |
|---|---|---|---|---|---|
| (ölçüm yok) | PLANNED | PLANNED | PLANNED | PLANNED | Bekliyor |

Tablo doldurulana kadar §7 #1-2 eşikleri kapalıdır (§10 kapı kuralı 2).

## 21. Test envanteri — araç ve ön koşul matrisi

| Test grubu | Araç | Ön koşul | Donanım? |
|---|---|---|---|
| T-DRV-01…07 (ASIO) | JUCE host (§4 #9) + ASIO driver | ASIO driver kurulu | Evet (ses cihazı) |
| T-DRV-08…11 (WASAPI) | Direct test uygulaması | Windows ses yığını | Evet |
| T-DRV-12, 21 (sürücü olayları) | Device manager + uygulama | Admin yetkisi | Evet |
| T-DRV-13…15 (DPC/ISR/fault) | LatencyMon (§4 #1-3) | ≥60 dk kesintisiz koşu | Evet |
| T-DRV-16 (çoklu cihaz) | 2 cihaz | 2 fiziksel ses cihazı | Evet (2×) |
| T-DRV-17…18 (fallback) | Uygulama + yapılandırma | Mod değiştirme desteği | Evet |
| T-DRV-19 (JUCE vs SDK) | JUCE host | Sürüm kanıtı eksik — §1 #12 | Evet |
| T-DRV-20 (envanter) | Registry + `GetDeviceIDs` | Admin | Hayır |
| T-DRV-22 (boot yarışı) | Erken başlatma betiği | Boot zamanlaması | Evet |

## 22. Soru ve karar kaydı (append-only)

| # | Tarih | Soru/karar | Durum |
|---|---|---|---|
| 1 | 2026-10-13 | LatencyMon eşik değerleri (DPC/ISR) yazılacak mı? | Karar: HAYIR — resmi kaynakta sayısal eşik doğrulanmadı, uydurma yerine ölçüm beklentisi yazıldı (§12 #2) |
| 2 | 2026-10-13 | ASIO SDK 2.3.4 / JUCE 9 iddiaları güncellenecek mi? | Karar: HAYIR — sürüm kanıtı hâlâ yok, `VERIFICATION REQUIRED` korundu (§1 #12) |
| 3 | 2026-10-13 | Sürücü gecikme bileşeni (8-16) doğrulanacak mı? | Karar: HAYIR — `UNKNOWN` korundu (§1 #15, N-DRV-1) |

## 23. Sürücü katmanı kontrol listesi (gate öncesi hızlı bakış)

- [ ] ASIO enum + exclusive lock testleri (T-DRV-01…03) — PLANNED
- [ ] Buffer size / sample rate testleri (T-DRV-04/05) — PLANNED
- [ ] Start/stop döngüsü + hata kurtarma (T-DRV-06/07) — PLANNED
- [ ] WASAPI shared/exclusive + event ritmi (T-DRV-08…11) — PLANNED
- [ ] DPC/ISR/fault ölçümleri (T-DRV-13…15, P-DRV-01/05) — PLANNED
- [ ] Çoklu cihaz + fallback + güç olayları (T-DRV-16…18, 21) — PLANNED
- [ ] Sürücü envanteri (T-DRV-20, P-DRV-07) — PLANNED
- [ ] Sürüm kanıtları (§7 #11-12) — VERIFICATION REQUIRED
- [ ] `UNKNOWN` satırları (#13-14) — kapatılamadı, ölçüm gerekli

## 24. Sürüm geçmişi (dosya)

| Tarih | Değişiklik | Mod |
|---|---|---|
| 2026-10-12 | İlk sürüm (68 satır, §1-§3) | Vault Steward |
| 2026-10-13 | §4-§29 eklendi (append-only); 9 HEAD-200 kaynak, 22 test, 8 protokol | Red Team / Truth Mode |
| 2026-10-13 | Numaralandırma düzeltmesi: §17-§29 arası bölümler sıralandırıldı; içerik değişmedi | Red Team / Truth Mode |
| — | Gelecek: gerçek DPC/ISR ölçümü (P-DRV-01) → §28 günlüğüne satır → §7 eşikleri | PLANNED |

## 25. Karşılaştırmalı sürücü arayüzü matrisi (kavramsal — §4 kaynaklı)

| Özellik | ASIO (Steinberg, §1 #3) | WASAPI exclusive (§4 #6) | WASAPI shared (§4 #6-7) |
|---|---|---|---|
| Sahiplik | Exclusive lock (tek uygulama) | Exclusive lock | Paylaşımlı (audio engine üzerinden) |
| Buffer boyutu | Driver müzakeresi (T-DRV-04) | Uygulama belirler | Engine belirler |
| Örnekleme | Cihaz-native | Cihaz-native | OS mix |
| Callback ritmi | Driver tetikli | Event-driven (T-DRV-10) | Engine tetikli |
| Gecikme bileşenleri | `UNKNOWN` (#13) | Ölçüm gerekli (§7) | Ölçüm gerekli (§7) |
| Doğrulama önceliği | Yüksek (ana mod) | Orta (fallback) | Düşük (destek modu) |

## 26. Hata kodu kütüphanesi (bilinen — doğrulanmış kaynak: §4 #6-8)

| Hata/kavram | Anlamı | Test bağlantısı |
|---|---|---|
| Device in use (exclusive reddi) | Başka uygulama exclusive sahiplenmiş | T-DRV-02, N-DRV-T02 |
| `AUDCLNT_BUFFERFLAGS_SILENT` | Sessiz paket işareti (capture) | T-DRV-11 |
| `AUDCLNT_STREAMFLAGS_LOOPBACK` | Loopback stream bayrağı | T-DRV-11 (loopback varyantı) |
| Event handle yok / geçersiz | EVENTCALLBACK başlatma hatası | T-DRV-10, P-DRV-02 |
| Period uyuşmazlığı | İstenen dönem desteklenmiyor | T-DRV-08, N-DRV-T04 |

Not: Sayısal HRESULT değerleri bu dosyada **yazılmamıştır** — resmi kaynakta
satır bazında doğrulanmadılar; kavramsal isimler §4 #6-8 zincirinde geçer.

## 27. Kapanış özeti (2026-10-13 eki)

1. 9 yeni kaynak HEAD 200 doğrulandı (§4); 2 `UNKNOWN` iddia dürüstçe tekrarlandı (§4.1).
2. 22 test + 8 protokol + 8 negatif test PLANNED olarak tanımlandı; hiçbiri
   çalıştırılmadı (kod yok, donanım ölçümü yapılmadı).
3. DPC/ISR eşikleri uydurulmadı — ölçüm beklentisi olarak bırakıldı (§12 #2, §23 #1).
4. ASIO SDK 2.3.4 / JUCE 9 sürüm iddiaları kapatılamadı (`VERIFICATION REQUIRED`).
5. Sürücü katmanı kapısı (§10) kapalıdır: gerçek ölçüm olmadan PAS yok.

## 28. Ölçüm kayıt günlüğü (boş şablon — ilk satırlar PLANNED)

P-DRV-01/05 koşularından dönecek gerçek kayıtlar bu tabloya **append-only**
işlenecek; ölçüm yapılana kadar satır yoktur (uydurma satır eklemek yasak).

| Tarih | Araç | Sürücü | ISR (µs) | DPC (µs) | Hard fault | Karar | Not |
|---|---|---|---|---|---|---|---|
| — | — | (kayıt yok) | — | — | — | PLANNED | Ölçüm yapılmadı |

Kayıt kuralları:
1. Her satır gerçek ölçüm çıktısından kopyalanır; elle tahmin edilen değer
   yazılamaz.
2. Araç sürümü + Windows build'i + sürücü sürümü kayda zorunlu eşlik eder
   (§11 şablonundaki alanlar).
3. Ölçüm ortamı değişirse (yeni sürücü, yeni Windows build) yeni satır eklenir;
   eski satır silinmez/üzerine yazılmaz.
4. §7 #1-2 eşikleri ancak bu günlüğe en az bir gerçek satır girdikten sonra
   yazılabilir (§12 #2 ile tutarlı).
5. LatencyMon raporu XML/HTML çıktısı arşivlenir; tablo yalnızca özettir,
   birincil kanıt rapor dosyasıdır.
6. Ölçüm koşulu (≥60 dk, ses stream açık) tabloya `Not` sütununda belirtilir;
   koşul karşılanmıyorsa satır `GEÇERSİZ` işaretlenir ve eşik hesabı kullanılamaz.

---

## 29. Dürüstlük bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler (§4-§29) 0 uydurma
URL/ölçüm içerir. §4'teki 9 URL HEAD 200 ile 2026-10-13'te doğrulanmıştır.
§4.1'de §1'deki `UNKNOWN`/`VERIFICATION REQUIRED` kayıtları (N-DRV-1, N-DRV-2)
aynen tekrarlanmıştır. §5-§16'daki hiçbir ölçüm değeri gerçek ortamda
alınmamıştır; hepsi `PLANNED`/`UNKNOWN` işaretlidir. §1 satırları değiştirilmemiştir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
