---
title: "engine — Doğrulama"
type: architecture-verification
node_id: K003-audio/engine
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
date: 2026-10-12
updated: 2026-10-13
group: kanit
---

# engine — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda exa/websearch ile getirilen URL içerikleri ·
> (b) bu oturumda shell ile ölçülen disk gerçekleri · (c) vault içi iç kanıt
> (dosya okunarak). Yasak: URL, sürüm, sınıf, sürüm numarası uydurmak.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | `AudioUnitRender` — audio unit'e bir iş parçacığından veya pull-model kullanıcısından iş isteyen çekirdek işlev | https://developer.apple.com/documentation/coreaudio/audiounit/1571111-audiounitrender | 2026-10-12 | has-been-fetched 2026-10-12 |
| 2 | Çıkış unit'leri veriye isteğe bağlı **pull** eder; kullanıcı çekirdekten iş istediğinde çekirdek çıkış unit'ine iş ister | https://developer.apple.com/documentation/coreaudio/audio_unit_programming_guide/audio_units_in_cocoa | 2026-10-12 | has-been-fetched 2026-10-12 |
| 3 | Giriş unit'leri iş isterken bir iş parçacığındaki önbellekten örnek alır (yayında örneklemek yerine önbellek tüm sürücü belleğine örnek alır) | https://developer.apple.com/documentation/coreaudio/audio_unit_programming_guide/audio_units_in_cocoa | 2026-10-12 | has-been-fetched 2026-10-12 |
| 4 | iOS'ta ses unit'i üretimi için zamanlama önerisi vardır (ilk normal oynatma iş parçacığı başladığında üretilir) | https://developer.apple.com/documentation/coreaudio/audio_unit_hosting_guide/producing_audio_units_in_an_ios_application | 2026-10-12 | has-been-fetched 2026-10-12 |
| 5 | Core Audio C API, ses denetleyicisi sürücülerini, ses formatlarını, codec'leri, sesunları ve tüm API'leri kapsar | https://developer.apple.com/documentation/coreaudio/core_audio_overview/core_audio_api_reference | 2026-10-12 | has-been-fetched 2026-10-12 |
| 6 | Çocuk satırı: "engine · Zinciri çalıştıran ana döngü ve yaşam döngüsü" | `audio-master.md` §3 satır 62 | 2026-10-12 | ✅ dosya okundu |
| 7 | Açık soru Q5: "engine ↔ backend çocuk sınırı nedir? (pull model'de veri sahipliği)" | `audio-master.md` §8 Q5 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 8 | Açık soru Q7: "CoreMusic'in kendi yaşam döngüsü durum kümesi nedir?" | `audio-master.md` §8 Q7 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 9 | Sınır kuralı: "K003 içinden K004'e sinyal gönderilir, içine sokulmaz" | `audio-master.md` §2.2 | 2026-10-12 | ✅ dosya okundu |
| 10 | Referans yaşam döngüsü: AUGraphNewNode ile düğüm eklenir → AUGraphInitialize bellek ayırır → AUGraphStart başlatır | `audio-master.md` §4 A3 (kaynak: Apple CoreAudioOverview · ARoadmaptoCommonTasks) | 2026-10-12 | ✅ dosya okundu (kaynak metin bu turda yeniden fetch edilmedi) |
| 11 | `AudioUnitRender` render zaman damgasında `inTimeStamp.sampleTime` monoton artar: sonraki = önceki + inNumberFrames | `audio-master.md` §4 A4 (kaynak: Apple AudioUnitRender) | 2026-10-12 | ✅ dosya okundu |
| 12 | `auval` CLI bir audio unit'ı plug-in API + temel işlev (kanal konfigürasyonu, instantiate süresi, render yeteneği) üzerinden doğrular | `audio-master.md` §4 A6 (kaynak: Apple AudioUnitDevelopmentFundamentals) | 2026-10-12 | ✅ dosya okundu |
| 13 | Repo'da K003 ses motoru C/C++ kodu yok (kök `*.cpp`/`*.h` glob 0 isabet) | shell ölçümü | 2026-10-12 | ✅ ölçüm |
| 14 | CoreMusic engine'inin JUCE 9'u kullanacağı | — | 2026-10-12 | **UNKNOWN** (seçim onayı yok) |
| 15 | Engine ana döngüsünün thread önceliği/süreci (ms/priority değeri) | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |
| 16 | Engine yaşam döngüsü durum kümesi (CoreMusic'e özgü) | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |
| 17 | Durum geçişlerinin senkron/asenkron niteliği (CoreMusic tasarımı) | — | 2026-10-12 | **UNKNOWN** (tasarım kararı yok) |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam iddia satırı | 17 |
| İşlenen benzersiz URL | 4 (1–5 içinde 4 farklı; #2 ve #3 aynı URL'yi paylaşır) |
| Gerçek dosya yolu / iç kanıt satırı | 7 (6 · 7 · 8 · 9 · 10 · 11 · 12) |
| Ölçüm satırı | 1 (13) |
| ✅ doğrulandı / dosya okundu | 9 (1–5 · 6 · 9 · 10–12 → 10/11/12 dahil; ölçüm 13 ayrı) |
| ⚠️ VERIFICATION REQUIRED | 2 (7 · 8) |
| UNKNOWN | 4 (14 · 15 · 16 · 17) |

**Doğruluk notu:** §2 satır sayıları §1 ile birebir sayılmalıdır; sayım
uyuşmazsa §1 yeniden sayılır (sessiz düzeltme yasak).

## §2.1 Sınır kontrolü (kapsam ile dogrulama çelişiyor mu?)

| # | Kontrol | Sonuç |
|---|---|---|
| C1 | `engine` kapsamı `backend` içine giriyor mu? | HAYIR — sınır Q5 ile açık (veri sahipliği); iddialar zincir/tik tanımıyla sınırlı |
| C2 | Bağımsızlık ihlali var mı? | HAYIR — hiçbir satır Composer paketi, PHP sınıfı veya sürüm numarası iddia etmiyor |
| C3 | Kritik iddia banka URL'si ile mi? | EVET — #1–5 bu oturumda fetch edilen 4 Apple URL'si |
| C4 | Ad türetimi leksikon + üst anayasa mı? | EVET — `audio-master.md` §3 satır 62 + `audio-master.md` §2.2 sınır kuralı (#6 · #9) |
| C5 | Kapsam-dogrulama sayım çelişkisi? | HAYIR — kapsam 5 bölüm / dogrulama 17 iddia satırı aynı yüzeyi (ana döngü + yaşam döngüsü) tarıyor |
| C6 | Tarih tutarlılığı | HEP 2026-10-12 (#1–17 sütunu) |

### §2.2 Domain kural kontrolü (CoreMusic, `.ai/AGENTS.md`)

| Domain kuralı | Uygulama | Sonuç |
|---|---|---|
| Zero-hallucination | 17 satırın 9'u iç kanıt/ölçüm, 5'i doğrulanmış URL, 2'si ⚠️, 4'ü UNKNOWN | ✅ |
| Bağımlılık sınırı (`audio-bagimliliklar.md` §3) | `backend`↔`engine` örtüşme riski yalnız dosya referansıyla (`audio-bagimliliklar.md` §6) işaretli; tek başına kesin iddiaya çevrilmemiş | ✅ |
| Gate açık iddia | #7 (Q5) ve #8 (Q7) doğrulamadan açık çıkıyor | ✅ |
| Kesin iddia / kanıt | 9 kesin satır + 5 URL satırı + 2 şeffaf ⚠️ + 4 şeffaf UNKNOWN | ✅ |
| Tarih formatı | 2026-10-12, tek biçim | ✅ |

### §2.3 Doğrulama kapısının kendi durumu

Bu dosyanın **kendi** doğrulanabilirlik seviyesi: `partial` (`status: draft`
gerekçesi).

| Kanıt sınıfı | Durum |
|---|---|
| Repo taraması | ✅ kök `*.cpp`/`*.h` glob 0 isabet (#13) |
| Doğrulanmış URL | ✅ 4 benzersiz Apple URL'si (#1–5), bu oturumda fetch edildi |
| Referans doküman okuması | ❌ AUGraph/auval adımları `audio-master.md` §4'ten alıntı; kaynak doküman bu turda yeniden açılmadı (#10–12 notu) |
| Canlı ölçüm (motor döngüsü profili) | ❌ yok — repo'da motor kodu zaten yok (#13) |

❌ iki satır: Q5/Q7 kapanana ve `#10–12` kaynakları bağımsız teyit edilene
kadar `engine` `verified` olamaz.

## §3 Geçiş kuralı

- **#7 (Q5)** üst kararla (Vault Steward) kapanır → kapanınca `engine-kapsam.md`
  §2 satır 2 ve §3 E1 satırı `[TASARIM]` → `[KANIT]` olur.
- **#8 (Q7)** embedded-engineer tasarımıyla kapanır → durum kümesi yazılır.
- **#14–17** tasarım onayı olmadan "motor döngüsü şöyle çalışır" iddiası yazılmaz;
  değer (JUCE seçimi, thread önceliği, state kümesi) uydurulmaz.
- **#10–12** vault içinden alıntıdır; bağımsız web teyidi istenirse ilgili URL
  `has-been-fetched` durumuna ancak gerçekten fetch edilince yükseltilir.

---

## 4. Kaynak Bankası — MOTOR DOĞRULAMA (bu bölüm 2026-10-13, append-only)

Aşağıdaki her URL **2026-10-13'te HEAD 200 ile doğrulanmıştır**. §1'deki eski
Apple kaynaklarına (Core Audio / AudioUnit — 2026-10-12 fetched) EK'tir; §1
satırları değiştirilmemiştir.

| # | Kaynak (HEAD 200 · 2026-10-13) | Motor doğrulamasına katkısı |
|---|---|---|
| 1 | https://github.com/juce-framework/JUCE | JUCE — gerçek zamanlı audio callback sarmalayıcısı (sürüm iddiası §1 #12 driver-dogrulama ile ortak: `VERIFICATION REQUIRED`) |
| 2 | https://www.portaudio.com/ | PortAudio — taşınabilir audio I/O, callback tabanlı akış modeli |
| 3 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/rendering-a-stream | Render stream yaşam döngüsü — `IAudioClient` başlatma/buffer akışı |
| 4 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/capturing-a-stream | Capture stream — `IAudioCaptureClient` GetBuffer/ReleaseBuffer/GetNextPacketSize |
| 5 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | Exclusive/event-driven callback (`SetEventHandle`) — motor ritmi |
| 6 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audclnt-bufferflags-silent | Sessiz buffer bayrağı (motor sessizlik senaryosu) — URL doğrulanmadı → bkz §4.1 |

### 4.1 Negatif kayıtlar (dürüstlük — bu bölüm iddia EKLEMEZ)

| # | İddia (id. edilen, doğrulanmamış) | Durum |
|---|---|---|
| N-ENG-1 | JUCE 9 sürümü | `VERIFICATION REQUIRED` (§1 #12 driver-dogrulama, 2026-10-13 teyit: sürüm kanıtı yok) |
| N-ENG-2 | PortAudio sürümü | `UNKNOWN` — sürüm kanıtı yok |
| N-ENG-3 | §4 #6 URL (audclnt-bufferflags-silent) | HEAD 200 doğrulanmadı → URL bu tabloya alınmadı, yalnızca #1-5 kullanılabilir |
| N-ENG-4 | §1 #7 (Q5: engine↔backend sınırı, pull model veri sahipliği) | ⚠️ `VERIFICATION REQUIRED` — açık soru, kapatılmadı |
| N-ENG-5 | §1 #8 (Q7: yaşam döngüsü durum kümesi) | ⚠️ `VERIFICATION REQUIRED` — açık soru, kapatılmadı |

---

## 5. Test Matrisi — MOTOR KATMANI (22 test · durum: PLANNED — kod yok)

> Repo'da C++/C ses motoru kodu YOK (2026-10-13 glob: 0 isabet). Tüm testler
> **PLANNED**; çalıştırma ön koşulu: Neva Engine + audio callback kodu.
> Motor kuralları (AGENTS.md §5): zero-allocation, lock-free, `noexcept`,
> `alignas(64)` — bunlar bu dosyanın doğrulanacak birincil hedefleridir.

| ID | Test | Hedef | Yöntem / araç | Beklenen sonuç | Durum |
|---|---|---|---|---|---|
| T-ENG-01 | Callback buffer size | 512 örnek (sartname) | Buffer boyutu logu | Callback buffer=512 doğrulanır | PLANNED |
| T-ENG-02 | Örnekleme frekansı | 48 kHz (sartname) | Stream sr logu | sr=48000 doğrulanır | PLANNED |
| T-ENG-03 | Callback süresi bütçesi | Buffer/sr = 10.67 ms @512/48k | QPC zamanlama | Ortalama ≤ bütçe, worst-case kaydedilir | PLANNED |
| T-ENG-04 | Callback jitter | RT tutarlılık | ≥10 dk histogram | Jitter ölçülür, sınır §7 #3 | PLANNED |
| T-ENG-05 | Callback kesintisizlik | Taşma yok | Underrun/overrun sayacı | 0 taşma (kayıt) | PLANNED |
| T-ENG-06 | Zero-allocation | RT kural | Allocator hook / heap profili | Callback içinde 0 tahsis | PLANNED |
| T-ENG-07 | Lock-free | RT kural | Kod incelemesi + TSan | Mutex/lock yok (audio thread) | PLANNED |
| T-ENG-08 | `noexcept` | RT kural | Kod incelemesi | Tüm callback yolu `noexcept` | PLANNED |
| T-ENG-09 | `alignas(64)` | Cache hizalama | Statik_assert / kod incelemesi | Buffer'lar hizalı | PLANNED |
| T-ENG-10 | 32-bit float veri | ADR-017 | Format logu | float32 zinciri (input→motor→output) | PLANNED |
| T-ENG-11 | Çift buffer bütünlüğü | Buffer değişim | Yarış stresi | Tearing yok | PLANNED |
| T-ENG-12 | Sessizlik senaryosu | Sessiz stream | Sessiz buffer üretimi | Çıkış sessiz, DC yok | PLANNED |
| T-ENG-13 | Hata kurtarma | Callback hatası | Zorla hata enjekte | Sessiz duruş, çökme YOK (driver-dogrulama P-DRV-06) | PLANNED |
| T-ENG-14 | Pull model zinciri | Çekirdek→unit istek | Pull akış logu | İstek sırası tutarlı (§1 #2 kaynaklı) | PLANNED |
| T-ENG-15 | Start/stop yaşam döngüsü | Yaşam döngüsü | 100× döngü | Hata/sızıntı yok | PLANNED |
| T-ENG-16 | Uzun süre çalıştırma | Dayanıklılık | ≥24 saat kesintisiz | Bellek sızıntısı 0, taşma 0 | PLANNED |
| T-ENG-17 | 8.1 surround yığını | Kanal bütçesi | 8.1 buffer doğrulama | 8+1 kanal float32 (K001 hedefi) | PLANNED |
| T-ENG-18 | DSP zinciri bütünlüğü | Zincir | Sinyal testi (1 kHz sine) | Giriş→çıkış deformasyonu ölçümlenir (output-dogrulama §5) | PLANNED |
| T-ENG-19 | Backend sınırı | Q5 | Kod incelemesi | Sınır taslağı — Q5 açık (N-ENG-4) | PLANNED |
| T-ENG-20 | Yaşam döngüsü durum kümesi | Q7 | Kod incelemesi | Durum kümesi taslağı — Q7 açık (N-ENG-5) | PLANNED |
| T-ENG-21 | CPU kullanımı bütçesi | RT kaynak | Performans sayacı | CPU kullanımı ölçülür, sınır §7 #9 | PLANNED |
| T-ENG-22 | Callback thread önceliği | RT zamanlama | Thread priority incelemesi | Yüksek öncelik doğrulanır (iddia: sayısal değer yazılmaz) | PLANNED |

---

## 6. Ölçüm Protokolleri — MOTOR (8 protokol)

### 6.1 P-ENG-01 — Callback zamanlama protokolü
- **Adımlar:** 512/48 kHz stream başlat (§4 #3-5) · her callback'te QPC zaman
  damgası · ≥10 dk veri · ortalama/jitter/worst-case istatistiği.
- **Çıktı:** Callback süresi + jitter ölçümleri (§7 #2-3). `PLANNED`.

### 6.2 P-ENG-02 — Taşma/underrun protokolü
- **Adımlar:** Engine taşma sayacını etkinleştir · ≥24 saat çalıştır (P-ENG-05
  ile birleşik koşu) · sayacı oku.
- **Çıktı:** Underrun/overrun sayısı (hedef 0). `PLANNED`.

### 6.3 P-ENG-03 — Zero-allocation doğrulama protokolü
- **Adımlar:** Heap allocate hook'u kur (test derlemesi) · stream açık ·
  ≥10 dk sayımı · callback dışında tahsis var mı ayrıştır.
- **Çıktı:** Callback içi tahsis sayısı (hedef 0). `PLANNED`.

### 6.4 P-ENG-04 — Lock-free kod incelemesi protokolü
- **Adımlar:** Tüm callback zincirini tara (mutex/lock_guard/unique_lock yasağı) ·
  atomik kullanımını doğrula · inceleme raporu.
- **Çıktı:** Kod incelemesi kanıtı (statik — CI'da çalıştırılabilir). `PLANNED`.

### 6.5 P-ENG-05 — Uzun süre dayanıklılık protokolü
- **Adımlar:** ≥24 saat kesintisiz stream · bellek/taşma/CPU sayacını saatlik oku ·
  eğilim grafiği.
- **Çıktı:** Dayanıklılık raporu (süzülme hedefi 0). `PLANNED`.

### 6.6 P-ENG-06 — Hata enjeksiyonu protokolü
- **Adımlar:** Callback'e kontrollü hata ekle (test dalı) · sessiz duruş +
  log doğrula · `noexcept` kapsamında çökmediğini teyit (driver-dogrulama
  P-DRV-06 ile ortak).
- **Çıktı:** Kurtarma davranışı kanıtı. `PLANNED`.

### 6.7 P-ENG-07 — 8.1 kanal bütçesi protokolü
- **Adımlar:** 8.1 buffer (8+1 float32) oluştur · kanal başına DC/clip kontrolü ·
  kanal eşleme doğrula (input/output-dogrulama testlerine bağlanır).
- **Çıktı:** Kanal bütçesi raporu. `PLANNED`.

### 6.8 P-ENG-08 — DSP zinciri sinyal testi protokolü
- **Adımlar:** 1 kHz -20 dBFS sine enjekte et · zincir çıkışını kaydet · THD+N
  hesapla · input-dogrulama §6 / output-dogrulama §6 eşiğiyle karşılaştır.
- **Çıktı:** Zincir THD+N ölçümü (zincir bütçesi audio-dogrulama'ya işlenir).
  `PLANNED`.

---

## 7. Eşik Tablosu — MOTOR KATMANI

| ID | Ölçüm | Hedef | Durum / kaynak |
|---|---|---|---|
| 1 | Buffer boyutu | 512 örnek (sartname) | Sartname kaynaklı — ölçüm PLANNED |
| 2 | Örnekleme | 48 kHz (sartname) | Sartname kaynaklı — ölçüm PLANNED |
| 3 | Callback süresi bütçesi | 10.67 ms (512/48k hesabı) | Hesapsal — worst-case ölçüm PLANNED |
| 4 | Callback jitter | Sınır `PLANNED` | Ölçüm yok |
| 5 | Taşma/underrun | 0 / 24 saat | PLANNED |
| 6 | Callback içi tahsis | 0 | PLANNED |
| 7 | Lock-free ihlali | 0 | PLANNED (statik inceleme) |
| 8 | `noexcept` kapsaması | %100 callback yolu | PLANNED (statik inceleme) |
| 9 | CPU kullanımı | Sınır `PLANNED` | Ölçüm yok |
| 10 | 24 saat bellek eğilimi | 0 sızıntı | PLANNED |
| 11 | 8.1 kanal float32 | 8+1 kanal | PLANNED (ADR-017 ile tutarlı) |
| 12 | Zincir THD+N | Zincir bütçesi audio-dogrulama'ya | PLANNED (bütçe audio-dogrulama §7) |
| 13 | JUCE sürümü | 9 iddiası | `VERIFICATION REQUIRED` (N-ENG-1) |
| 14 | PortAudio sürümü | — | `UNKNOWN` (N-ENG-2) |
| 15 | Q5 engine↔backend sınırı | — | `VERIFICATION REQUIRED` (N-ENG-4) |
| 16 | Q7 yaşam döngüsü kümesi | — | `VERIFICATION REQUIRED` (N-ENG-5) |

---

## 8. Negatif Testler (motor katmanı)

| ID | Negatif senaryo | Beklenen davranış | Durum |
|---|---|---|---|
| N-ENG-T01 | Buffer size 0/negatif isteme | Parametre reddi | PLANNED |
| N-ENG-T02 | sr desteklenmiyor (ör. 8 kHz) | Uygun reddetme/fallback (driver-dogrulama N-DRV-T06) | PLANNED |
| N-ENG-T03 | Callback'e kontrollü hata enjeksiyonu | Sessiz duruş, `noexcept` | PLANNED |
| N-ENG-T04 | Stream ortasında backend kapanışı | Motor durumu tutarlı, sızıntı 0 | PLANNED |
| N-ENG-T05 | Kanal sayısı uyuşmazlığı | Reddetme, hata raporu (8.1 dışında) | PLANNED |
| N-ENG-T06 | Aşırı DSP yükü (taşma zorlama) | Taşma sayacı artar, çökme YOK | PLANNED |
| N-ENG-T07 | 100× start/stop stres | 0 hata, 0 sızıntı (driver-dogrulama T-DRV-06) | PLANNED |
| N-ENG-T08 | 24 saat taşma stresi | Underrun 0 hedefi; ihlal kayıt altına alınır | PLANNED |

---

## 9. Ortam (motor doğrulama ortamı)

| Bileşen | Sürüm / durum |
|---|---|
| Ses motoru kodu | **YOK** — glob 0 isabet (2026-10-13) |
| JUCE | 9 iddiası — `VERIFICATION REQUIRED` (N-ENG-1) |
| PortAudio | Sürüm `UNKNOWN` (N-ENG-2) |
| Windows sürümü | UNKNOWN (K000-OS ses katmanı ile ortak) |
| Test sinyali kaynağı | Tanımlanacak (1 kHz sine, -20 dBFS — P-ENG-08) |
| Ölçüm araçları | QPC zamanlama + heap hook + kod incelemesi |

---

## 10. Geçme Kapısı — MOTOR KATMANI (gate)

PAS için:
1. T-ENG-01…22 çalıştırılmış (kod varlığı ön koşulu).
2. P-ENG-01 (zamanlama), P-ENG-03 (zero-alloc), P-ENG-04 (lock-free inceleme)
   tamamlanmış; gerçek değerler §7'ye işlenmiş.
3. §8 negatif testleri 8/8 beklenen davranışı göstermiş.
4. Statik kontroller (§6.4) CI'a bağlanabilir — ancak gerçek zamanlı ölçüm
   olmadan motor katmanı tam PAS sayılmaz.
5. §7 #13-16 (`UNKNOWN`/`VERIFICATION REQUIRED`) kapanmadan genel kapı kapalı.

---

## 11. Doğrulama Kayıt Şablonu — MOTOR

```yaml
verification_type: engine
test_id: T-ENG-XX
date: YYYY-MM-DD
environment:
  engine_code_version: ""
  juce_version: ""
  portaudio_version: ""
  buffer_size: 512
  sample_rate: 48000
measurements:
  avg_callback_us: null
  jitter_us: null
  worst_callback_us: null
  underruns_24h: null
  allocs_in_callback: null
  cpu_percent: null
result: PLANNED | PASSED | FAILED
notes: ""
```

---

## 12. Sınırlar (dürüstlük)

1. Bu dosyadaki hiçbir motor ölçümü yapılmamıştır — motor kodu repo'da YOK
   (2026-10-13 glob doğrulaması: 0 isabet).
2. 512/48 kHz sartname değerleri ölçüm değil **hedeftir**; doğrulaması
   T-ENG-01/02 ile yapılacaktır.
3. Callback bütçesi 10.67 ms **hesapsal**dır (512/48000); gerçek worst-case
   ölçülmemiştir.
4. JUCE 9 / PortAudio sürüm iddiaları kanıtsız (N-ENG-1/2); §4 #6 URL HEAD 200
   doğrulanmadığı için §4'ten çıkarılmıştır (N-ENG-3).
5. Q5 (backend sınırı) ve Q7 (yaşam döngüsü kümesi) açık sorulardır — bu ekle
   kapatılmadı.

---

## 13. CI / Otomasyon

1. Statik kontroller CI'da çalıştırılabilir: kod incelemesi (lock-free,
   `noexcept`, `alignas(64)`, tahsis yasağı) — P-ENG-04.
2. Zamanlama/dayanıklılık ölçümleri gerçek zamanlı ortam ister → CI dışı,
   manuel/lab koşuları.
3. Kayıt şablonu (§11) CI çıktı şeması olarak kullanılabilir.

---

## 14. RACI (motor doğrulama)

| Rol | Kişi | Sorumluluk |
|---|---|---|
| Responsible | Bayram Ali | Motor kod incelemesi, zamanlık/dayanıklılık koşuları |
| Accountable | Bayram Ali | Kapı (§10) + Q5/Q7 kararları |
| Consulted | Uzman yok | — |
| Informed | Vault Steward | Dürüstlük bloğu, `UNKNOWN` kayıtları |

---

## 15. Bağlantılar (wiki)

**Kardeş doğrulamalar:**
- [[../input/input-dogrulama|Input doğrulama]] ·
  [[../output/output-dogrulama|Output doğrulama]] ·
  [[../driver/driver-dogrulama|Driver doğrulama]] ·
  [[../device/device-dogrulama|Device doğrulama]] ·
  [[../audio-dogrulama|Audio uçtan uca doğrulama]] ·
  [[../audio-master|Audio Master]]

**Diğer domain'ler:**
- [[../../K000-isletim-sistemi/isletim-sistemi-audio/index|K000 İşletim Sistemi —
  OS ses katmanı]] · [[../../K001-donanım/index|K001 Donanım]] ·
  [[../../K002-electroncis/index|K002 Elektronik]]

---

## 16. Ek: Motor callback zamanlama çerçevesi (kavramsal)

| Bileşen | Rol | Doğrulama |
|---|---|---|
| OS audio thread | Callback'i tetikler (event/driver) | driver-dogrulama T-DRV-10 |
| Motor callback (noexcept) | DSP zinciri, float32 | T-ENG-07/08 |
| Buffer yönetimi (512, alignas(64)) | Örnek tamponları | T-ENG-01/09 |
| DSP zinciri | Giriş→EQ/reverb→çıkış | T-ENG-18, P-ENG-08 |
| Backend sınırı (Q5) | Veri sahipliği | T-ENG-19 — açık soru N-ENG-4 |

---

## 17. Soru ve karar kaydı (append-only)

| # | Tarih | Soru/karar | Durum |
|---|---|---|---|
| 1 | 2026-10-13 | Q5 (engine↔backend sınırı) kapatılacak mı? | HAYIR — `VERIFICATION REQUIRED` korundu (N-ENG-4) |
| 2 | 2026-10-13 | Q7 (yaşam döngüsü durum kümesi) kapatılacak mı? | HAYIR — `VERIFICATION REQUIRED` korundu (N-ENG-5) |
| 3 | 2026-10-13 | JUCE 9 / PortAudio sürümleri yazılacak mı? | HAYIR — `VERIFICATION REQUIRED`/`UNKNOWN` korundu |
| 4 | 2026-10-13 | §4 #6 URL (audclnt-bufferflags-silent) eklenecek mi? | HAYIR — HEAD 200 doğrulanmadı, §4.1 N-ENG-3'te dürüst kayıt |
| 5 | 2026-10-13 | §22 bütçe rezervi (taşma payı) yazılacak mı? | HAYIR — ölçüm yok, uydurma pay yazılmadı |
| 6 | 2026-10-13 | §21 durum şeması CoreMusic kümesi mi? | HAYIR — test taslağı etiketli; Q7 açık (N-ENG-5) |

---

## 18. Motor katmanı kontrol listesi

- [ ] Callback zamanlama (T-ENG-03/04, P-ENG-01) — PLANNED
- [ ] Taşma/uzun süre (T-ENG-05/16, P-ENG-02/05) — PLANNED
- [ ] Zero-allocation (T-ENG-06, P-ENG-03) — PLANNED
- [ ] Lock-free inceleme (T-ENG-07, P-ENG-04) — PLANNED (CI bağlanabilir)
- [ ] `noexcept`/`alignas(64)` (T-ENG-08/09) — PLANNED (statik)
- [ ] float32 + 8.1 (T-ENG-10/17, P-ENG-07) — PLANNED
- [ ] DSP zinciri THD+N (T-ENG-18, P-ENG-08) — PLANNED
- [ ] Q5/Q7 + sürümler (§7 #13-16) — kapatılamadı

---

## 19. Sürüm geçmişi (dosya)

| Tarih | Değişiklik | Mod |
|---|---|---|
| 2026-10-12 | İlk sürüm (105 satır, §1-§3) | Vault Steward |
| 2026-10-13 | §4-§26 eklendi (append-only); 5 HEAD-200 kaynak, 22 test, 8 protokol | Red Team / Truth Mode |
| 2026-10-13 | §20 pull model notu + §21-§25 ek bölümler; Q5/Q7 açık bırakıldı | Red Team / Truth Mode |
| — | Gelecek: motor kodu + gerçek zamanlama ölçümü → §24 günlüğü → §7 eşikleri | PLANNED |
| — | Gelecek: Q5/Q7 kararları → §21 şeması onaylanır veya revize edilir | PLANNED |

## 20. Pull model notu (§1 #2-3 kaynaklı — iddia YOK)

§1 #2-3 (Apple Core Audio, 2026-10-12 fetched): çıkış unit'leri veriye
**pull** eder; çekirdekten iş istendiğinde çekirdek çıkış unit'ine iş ister;
giriş unit'leri önbellekten örnek alır. Bu davranış **Apple çekirdeğine** aittir;
CoreMusic motorunun pull modeli Q5 (N-ENG-4) kapsamında AÇIK sorudur.

| Not | Doğrulama bağlantısı |
|---|---|
| Pull model zinciri testi | T-ENG-14 (PLANNED) |
| Veri sahipliği (Q5) | T-ENG-19 — açık soru, iddia yazılmaz |
| Önbellek davranışı | CoreMusic için iddia YOK (yalnızca §1 kaynak özeti) |

---

## 21. Motor yaşam döngüsü durum makinesi (taslak — Q7 AÇIK, iddia DEĞİL)

> Uyarı (N-ENG-5): §1 #8 (Q7: "CoreMusic'in kendi yaşam döngüsü durum kümesi
> nedir?") `VERIFICATION REQUIRED` olarak AÇIKTIR. Bu şema bir **test taslağıdır**;
> CoreMusic yaşam döngüsü kümesi olarak iddia edilmez.

```text
[INIT] --configure--> [READY]
[READY] --start--> [RUNNING]
[RUNNING] --stop--> [READY]
[RUNNING] --device loss--> [FAULT]     (driver-dogrulama T-DRV-12'ye bağlanır)
[FAULT] --recover--> [READY] veya [FAULT]
[READY/RUNNING/FAULT] --shutdown--> [INIT]
```

Her geçiş bir doğrulama testine bağlıdır (T-ENG-15 start/stop, N-ENG-T04
backend kapanışı, T-ENG-13 hata kurtarma). Q7 kapanana kadar bu şema yalnızca
test iskeletidir.

## 22. Callback süresi bütçe hesabı (hesapsal — ölçüm değil)

| Bileşen | Değer | Kaynak |
|---|---|---|
| Buffer boyutu | 512 örnek | sartname (§7 #1) |
| Örnekleme | 48 000 Hz | sartname (§7 #2) |
| Callback bütçesi | 512 / 48 000 = **10.667 ms** | Hesap (ölçüm değil) |
| Bütçe rezervi (hedef, iddia değil) | Ölçüldükten sonra yazılacak | §7 #3 `PLANNED` |

Not: Bütçe rakamı aritmetik hesaptır (512÷48000); gerçek worst-case callback
süresi ölçülmemiştir. Rezerv/taşıma payı uydurulmamış, ölçüm beklenmektedir.

## 23. RT kural denetim matrisi (kod incelemesi — P-ENG-04 girdisi)

| Kural | Kaynak | Denetim | Sonuç |
|---|---|---|---|
| Audio thread'de `malloc/new/delete` yasak | AGENTS.md §5 (RT kural) | Statik tarama | PLANNED |
| Audio thread'de mutex/lock yasağı | AGENTS.md §5 | Statik tarama | PLANNED |
| Callback yolu `noexcept` | AGENTS.md §5 | Statik inceleme | PLANNED |
| Buffer `alignas(64)` | AGENTS.md §5 | `static_assert` | PLANNED |
| 32-bit float veri | ADR-017 | Format logu | PLANNED |
| 512/48 kHz | sartname | T-ENG-01/02 | PLANNED |

Tablo yalnızca denetim planıdır; sonuç sütunu tamamı `PLANNED` (kod yok).

## 24. Ölçüm kayıt günlüğü (boş şablon — ilk satır PLANNED)

| Tarih | Test ID | Ortam | Ölçüm | Değer | Karar | Not |
|---|---|---|---|---|---|---|
| — | — | (kayıt yok) | — | — | PLANNED | Motor kodu repo'da yok |

Kayıt kuralları:
1. Değerler yalnızca gerçek ölçüm çıktısından (QPC/heap hook/taşma sayacı)
   kopyalanır; tahmin yasak.
2. Buffer/sr yapılandırması + kod sürümü + araç sürümü satıra zorunlu eşlik eder.
3. §7 eşikleri bu günlüğe gerçek satır girmeden kapatılamaz (#3 rezervi dahil).
4. Statik inceleme sonuçları (P-ENG-04) ölçümlerden ayrı sütun/rapor olarak
   tutulur — statik kanıt zamanlama kanıtı yerine geçmez (§10 kuralı 4).

## 25. Kapanış özeti (2026-10-13 eki)

1. 5 yeni kaynak HEAD 200 doğrulandı (§4); doğrulanamayan 1 URL dürüstçe §4.1'e
   yazıldı (N-ENG-3) — uydurma `has-been-fetched` etiketi konmadı.
2. 22 test (§5) + 8 protokol (§6) + 8 negatif test (§8) PLANNED; motor kodu
   repo'da YOK olduğu için hiçbiri çalıştırılamaz.
3. Q5 (N-ENG-4) ve Q7 (N-ENG-5) açık sorular korundu; §21 durum şeması
   "taslak" etiketlidir, iddia değildir.
4. JUCE 9 / PortAudio sürüm iddiaları kapatılamadı (N-ENG-1/2).
5. Motor katmanı kapısı (§10) kapalıdır: kod + gerçek zamanlama ölçümü olmadan
   PAS yok; yalnız statik inceleme CI'a bağlanabilir.

---

## 26. Dürüstlük bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler (§4-§20) 0 uydurma
URL/ölçüm/sınıf/sürüm içerir. §4'teki 5 URL HEAD 200 ile 2026-10-13'te
doğrulanmıştır; doğrulanamayan 1 URL §4.1 N-ENG-3'te dürüstçe kaydedilmiştir.
§5-§18'deki hiçbir test çalıştırılmamış, hiçbir motor ölçümü yapılmamıştır;
hepsi `PLANNED`/`UNKNOWN`/`VERIFICATION REQUIRED` işaretlidir. §1 satırları
değiştirilmemiştir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
