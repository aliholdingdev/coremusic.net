---
title: "output — Doğrulama"
type: architecture-verification
node_id: K003-audio/output
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
date: 2026-10-12
updated: 2026-10-13
group: kanit
---

# output — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda exa/websearch ile getirilen URL içerikleri ·
> (b) bu oturumda shell ile ölçülen disk gerçekleri · (c) vault içi iç kanıt
> (dosya okunarak). Yasak: URL, sürüm, sınıf, sürüm numarası uydurmak.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk satırı: "output · Çıkış tarafı (hoparlör, kulaklık, akış)" | `audio-master.md` §3 satır 67 | 2026-10-12 | ✅ dosya okundu |
| 2 | Çıkış unit'leri veriye isteğe bağlı **pull** eder; kullanıcı çekirdekten iş istediğinde çekirdek çıkış unit'ine iş ister | https://developer.apple.com/documentation/coreaudio/audio_unit_programming_guide/audio_units_in_cocoa | 2026-10-12 | has-been-fetched 2026-10-12 |
| 3 | `AudioUnitRender` her render zaman damgasında `inTimeStamp.sampleTime` monoton artar: sonraki = önceki + inNumberFrames | https://developer.apple.com/documentation/coreaudio/audiounit/1571111-audiounitrender | 2026-10-12 | has-been-fetched 2026-10-12 |
| 4 | Core Audio C API ses denetleyicisi sürücülerini, ses formatlarını, codec'leri, sesunları ve tüm API'leri kapsar | https://developer.apple.com/documentation/coreaudio/core_audio_overview/core_audio_api_reference | 2026-10-12 | has-been-fetched 2026-10-12 |
| 5 | WASAPI (Windows Core Audio) ana sayfası — Windows çıkış arayüzünün resmi kaynağı | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2026-10-12 | ✅ doğrulandı (exa fetch) |
| 6 | WASAPI exclusive mode streams — özel kip akışları belgesi | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2026-10-12 | ✅ doğrulandı (exa fetch) |
| 7 | ALSA PCM hw_params/sw_params + beş access modu (playback write yolu) | https://www.alsa-project.org/doc/api/group___p_c_m.html | 2026-10-12 | has-been-fetched 2026-10-12 |
| 8 | Açık soru Q6: input/output ↔ device/driver sorumluluk ayrımı | `audio-master.md` §8 Q6 | 2026-10-12 | ✅ dosya okundu |
| 9 | ASIO cihaz kaybında WASAPI fallback kuralı | `.ai/AGENTS.md` §17 #6 · `audio-master.md` §5 T8 | 2026-10-12 | ✅ dosya okundu |
| 10 | Repo'da playback (çıkış) kodu yok | kök `*.cpp`/`*.h` glob 0 isabet | 2026-10-12 | ✅ ölçüm |
| 11 | "output cihazı seçmez" — Q6 kapanmadan kesin sınır değildir | — | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 12 | Çoklu çıkış (hoparlör + kulaklık aynı anda) desteği | — | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 13 | Playback gecikme bütçesi (ms) | — | 2026-10-12 | **UNKNOWN** (ölçüm yok) |
| 14 | Underrun/xrun anında çıkış davranışı (sessizlik mi tekrar mı) | — | 2026-10-12 | **UNKNOWN** (tasarım yok) |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam iddia satırı | 14 |
| Benzersiz URL | 5 (2 · 3 · 4 · 5/6 ayrı · 7) |
| Gerçek dosya yolu / iç kanıt satırı | 4 (1 · 8 · 9 · 10) |
| ✅ doğrulandı / dosya okundu / ölçüm | 10 (1 · 5 · 6 · 8 · 9 · 10 → 2/3/4/7 dahil has-been-fetched) |
| ⚠️ VERIFICATION REQUIRED | 2 (11 · 12) |
| UNKNOWN | 2 (13 · 14) |

**Doğruluk notu:** §2 satır sayıları §1 ile birebir sayılmalıdır; sayım
uyuşmazsa §1 yeniden sayılır (sessiz düzeltme yasak).

## §3 Geçiş kuralı

- **#11 (Q6)** üst kararla kapanır → kapanınca `output-kapsam.md` §2 satır 1 ve
  §3 O1 satırı `[TASARIM]` → `[KANIT]` olur.
- **#12** çoklu çıkış kararı embedded-engineer onayıyla kapanır.
- **#13–14** ölçüm/tasarım olmadan ms değeri ya da underrun davranışı iddiası
  yazılmaz → `UNKNOWN` kalır.
- **#2–4 · 7** bağımsız teyit istenirse URL ancak gerçekten fetch edilince
  `✅ doğrulandı` durumuna yükseltilir.

---

## §4 Kanıt bankası — bu oturumda doğrulanan resmi kaynaklar (2026-10-13)

> **Yöntem:** `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`.
> Yalnızca **HTTP 200** giren URL'ler tabloya girer; 404 ölçümleri §4.1'dedir.

| # | Kaynak | URL | HEAD | Bu dosyadaki kullanımı |
|---|---|---|---|---|
| S1 | Microsoft Learn — Rendering a Stream | `https://learn.microsoft.com/en-us/windows/win32/coreaudio/rendering-a-stream` | 200 | §5 T-OUT-01 · §6.1 |
| S2 | Microsoft Learn — About WASAPI | `https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi` | 200 | §5 T-OUT-02 · §17 |
| S3 | Microsoft Learn — Exclusive Mode Streams | `https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams` | 200 | §5 T-OUT-16 · §10 K3 |
| S4 | Microsoft Learn — Loopback Recording | `https://learn.microsoft.com/en-us/windows/win32/coreaudio/loopback-recording` | 200 | §5 T-OUT-11 · §6.6 |
| S5 | TI — PCM3168A ürün sayfası (fetch) | `https://www.ti.com/product/PCM3168A` | 200 | §7 E6–E11 (DAC gerçek değerleri) |
| S6 | TI — PCM3168A datasheet PDF | `https://www.ti.com/lit/ds/symlink/pcm3168a.pdf` | 200 | §6.3 (tam test koşulları PDF'ten okunacak) |
| S7 | TI — TPA3255 ürün sayfası (fetch) | `https://www.ti.com/product/TPA3255` | 200 | §7 E12–E20 (amplifier gerçek değerleri) |
| S8 | TI — TPA3255 datasheet PDF | `https://www.ti.com/lit/ds/symlink/tpa3255.pdf` | 200 | §6.4 (koruma/tünel testleri PDF'ten) |
| S9 | Room EQ Wizard (ölçüm yazılımı) | `https://www.roomeqwizard.com/` | 200 | §6.7 · §9 (ölçüm aracı adayı) |
| S10 | Microsoft Learn — Windows Hardware drivers (audio) | `https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/` | 200 | §13 CI/driver bağımlılığı |
| S11 | Apple — Core Audio | `https://developer.apple.com/documentation/coreaudio` | 200 | §17 referans platform |
| S12 | Steinberg — ASIO SDK açık kaynak duyurusu | `https://www.steinberg.net/developers/asiosdk-open` | 200 | §5 T-OUT-17 (ASIO çıkış yolu) |

### §4.1 Negatif ölçüm — bu domainin eski iddia URL'leri (2026-10-13)

| URL | Eski iddia | 2026-10-13 GET | Sonuç |
|---|---|---|---|
| `https://www.alsa-project.org/doc/api/group___p_c_m.html` | §1 #7 `has-been-fetched 2026-10-12` (playback write yolu) | 404 | URL bugün erişilemez; 2026-10-12 kaydı silinmez, **bugünkü bağımsız teyit 404** — yeniden kaynak gerekir |
| `https://pipewire.org/page_latency` | (K003 genel dosyası #A-14…A-17) | 404 | bu dosyada kullanılmaz; genel dosyanın işi |

**Sonuç:** bu oturumda 12 URL HEAD 200 · 1 URL GET 404 (ALSA playback sayfası).

## §5 Çıkış (playback) test matrisi

> **Durum sözlüğü:** `PLANNED` = tasarlandı, çalıştırılmadı (repo'da playback kodu yok — §1 #10) ·
> `BLOCKED` = ön koşul (tasarım/karar) yok · `REFERENCE` = yalnız referans.

| ID | Test | Ön koşul | Yöntem (özet) | Geçme kriteri | Durum |
|---|---|---|---|---|---|
| T-OUT-01 | WASAPI render başlatma (shared) | Windows ortamı | S1: `IAudioClient::Initialize` → `IAudioRenderClient::GetBuffer/ReleaseBuffer` → 60 s akış | 60 s kesintisiz · `GetCurrentPadding` düzenli · boşluk yok | PLANNED |
| T-OUT-02 | Event-driven render tetikleme | S2 | `AUDCLNT_STREAMFLAGS_EVENTCALLBACK` + `SetEventHandle` | her period olay gelir · kaçan olay 0 | PLANNED |
| T-OUT-03 | DAC doğrulama (PCM3168A) | donanım hazır | §6.3 protokolü · 1 kHz THD+N + SNR | CoreMusic kuralı: SNR > 100 dB · THD+N < 0.01 % @1 kHz · ölçüm yok | PLANNED |
| T-OUT-04 | Çıkış DC offset doğrulama | yük bağlı / boş | §6.5 protokolü · DC ölçümü | kural: DC > 0.5 V → koruma rölesi zorunlu · eşik tasarımı açık | PLANNED |
| T-OUT-05 | Jitter / clock doğrulama | — | §6.6 protokolü (yöntem seçimi açık) | hedef jitter değeri **UNKNOWN** (ölçüm yok) | BLOCKED |
| T-OUT-06 | Frekans tepkisi (20 Hz–20 kHz) | sinyal kaynağı | §16 tarama planı | sapma eşiği tasarlanmadı → UNKNOWN | PLANNED |
| T-OUT-07 | Kademeli genlik / lineerlik | — | §6.2 (merdiven sinyali) | headroom payı UNKNOWN | PLANNED |
| T-OUT-08 | Click/pop (açma-kapama) | amplifier (S7: TPA3255 "click and pop free" iddiası) | tekrarlı start/stop | TPA3255 iddiası donanım referansı; CoreMusic ölçümü yok | PLANNED |
| T-OUT-09 | Mute / soft-mute davranışı | — | dijital attenuation → sessizlik | sessizlik zemini eşiği UNKNOWN | PLANNED |
| T-OUT-10 | Çoklu çıkış (hoparlör + kulaklık) | — | iki render akışı | §1 #12 **⚠️** — kapsam kararı yok | BLOCKED |
| T-OUT-11 | Loopback ile çıkış doğrulama | S4 | çalınanı `eRender`+`AUDCLNT_STREAMFLAGS_LOOPBACK` ile kaydet | sinyal bütünlüğü · kesinti yok | PLANNED |
| T-OUT-12 | Örnekleme hızı değişimi (48↔96/192 kHz) | DAC 192 kHz'e kadar (S5) | rate değiştir → ses doğrulama | dönüşüm stratejisi UNKNOWN | BLOCKED |
| T-OUT-13 | Bit derinliği/format dönüşümü | float32 çekirdek (ADR-017) | float32 → cihaz formatı | dönüşüm kaybı ölçülmeli · eşik yok | PLANNED |
| T-OUT-14 | Uzun süre dayanıklılık (≥30 dk playback) | T-OUT-01 | sürekli akış + kesinti sayacı | kesinti/xrun = 0 | PLANNED |
| T-OUT-15 | Cihaz kaybı anında çıkış | takılabilir cihaz | çalarken fiş çekme | hata raporu · sessizlik · fallback (`.ai/AGENTS.md` §17 #6) | BLOCKED (tasarım yok) |
| T-OUT-16 | Exclusive kip render | S3 | exclusive stream | ikinci uygulama reddedilir (ASIO exclusive tekil kuralı) | PLANNED |
| T-OUT-17 | ASIO çıkış yolu | S12 | ASIO bufferSwitch çıktısı | ASIO SDK sürüm/lisans kapıları (#12/#13) açık → BLOCKED | BLOCKED |
| T-OUT-18 | Amplifier koruma durumları | S7 | UV/OTP/kısa devre/clip hata raporu | TPA3255 self-protection iddiası (donanım); yazılım tepkisi tasarlanmadı | BLOCKED |
| T-OUT-19 | Kanal ayrımı / faz | ≥2 kanal | işaretli sinyal testi | tolerans tasarlanmadı → UNKNOWN | PLANNED |
| T-OUT-20 | Sessizlik üretimi (dijital 0) | — | tam sessiz akış | DC/gürültü zemini ölçülür · eşik UNKNOWN | PLANNED |
| T-OUT-21 | Sistem karışımına etki (shared kip) | S2 | başka uygulamalarla eşzamanlı | paylaşım davranışı tanımı yok → REFERENCE | BLOCKED |
| T-OUT-22 | Çıkış→giriş round-trip (körleme) | kayıt cihazı | `[[../audio-dogrulama|K003 genel]]` §6 E2E | zincir bütçesi UNKNOWN | PLANNED |

## §6 Ölçüm protokolleri (adım adım — hiçbiri henüz çalıştırılmadı)

### §6.1 P-OUT-1 · WASAPI render yaşam döngüsü protokolü (S1)

1. `IMMDeviceEnumerator` → `GetDefaultAudioEndpoint(eRender, …)` → `IMMDevice::Activate(IAudioClient)`.
2. `IAudioClient::Initialize` (shared kip; süre parametresi seçimi PLANNED).
3. `IAudioClient::GetBufferSize` ile **gerçek** tampon boyutu okunur (istenen ≠ alınan olabilir — S1).
4. `IAudioRenderClient::GetBuffer` → veri yaz → `ReleaseBuffer` döngüsü (S1'e göre paket kuyruğa eklenir).
5. `IAudioClient::GetCurrentPadding` ile kuyruktaki veri miktarı izlenir; taşıma izin verilmez.
6. ≥ 60 s çalıştırılır; kaçırılan dönem / boşluk sayacı kaydedilir (sayaç tasarımı `UNKNOWN`).
7. `IAudioClient::Stop` → `Reset` → Release zinciri (hata yolları §8'de).
8. Sonuç §11 şablonuna işlenir; durum `PLANNED`.

### §6.2 P-OUT-2 · Kademeli genlik / lineerlik protokolü

1. 1 kHz sinyal, −60 dBFS'ten başlar; 1 dB adımlarla yukarı.
2. Her adımda ≥ 1 s kayıt + tepe/THD+N analizi.
3. İlk "ölçüsel sapma" noktası kaydedilir (sapma tanımı araç ile birlikte tanımlanacak — araç `UNKNOWN`).
4. Sonuç iki değer olarak raporlanır: **ölçülen lineerlik sınırı** ve **tasarlanan headroom payı**.
5. Tasarlanan headroom **yok** → `UNKNOWN` (uydurma yasak).
6. DAC referansı (S5): diferansiyel çıkış **8 V PP** (PCM3168A tipik) — donanım referansı.
7. Amplifier referansı (S7): TPA3255 "<0.01 % THD+N to clipping" iddiası — donanım referansı.
8. Durum `PLANNED`.

### §6.3 P-OUT-3 · DAC THD+N / SNR doğrulama protokolü (PCM3168A referanslı)

1. **Kaynak (S5, bu oturumda fetch edildi):** PCM3168A DAC tarafı gerçek tipik değerler:
   - DAC THD+N **−94 dB** (fS = 48 kHz) · DAC SNR **112 dB** · DAC dinamik aralık **112 dB**
   - DAC örnekleme hızı **8 kHz – 192 kHz** · diferansiyel gerilim çıkışı **8 V PP**
   - Fazürtükleme (oversampling) **4×/8×** · bant dalgalanması **±0.0018 dB** · durdurma bandı **−75 dB**
   - De-emphasis: 32/44.1/48 kHz (DAC) · 8 DAC kanalı (6-in/8-out codec)
2. Bu değerler TI'nin tipikleridir — CoreMusic ölçümü **değildir**.
3. Sinyal: 1 kHz · bilinen genlik · ≥ 10 s · float32 çekirdek (ADR-017).
4. Analiz: THD+N + SNR; pencereleme/analiz yöntemi `UNKNOWN` (araç seçilmedi).
5. Karar: ölçülen değer §7'deki CoreMusic kural eşiği (SNR > 100 dB · THD+N < 0.01 % @1 kHz) ile
   **yan yana** raporlanır; TI değeri referanstır, geçme kriteri değildir.
6. Tekrar ≥ 3; medyan + dağılım §11 şablonunda.
7. Datasheet (S6) HEAD 200 ölçüldü; PDF içeriği okunmadı → test koşulları `⚠️ VERIFICATION REQUIRED`.
8. Durum `PLANNED`.

### §6.4 P-OUT-4 · Amplifier zincir doğrulama protokolü (TPA3255 referanslı)

1. **Kaynak (S7, bu oturumda fetch edildi):** TPA3255 gerçek değerler:
   - Mimarisi: **Class-D** · ses girişi **analog** · en fazla **4 hoparlör kanalı**
   - Çıkış gücü @10 % THD+N: **315 W stereo 4 Ω BTL** · **185 W stereo 8 Ω BTL** · **600 W mono 2 Ω PBTL**
   - Çıkış gücü @1 % THD+N: **260 W stereo 4 Ω** · **150 W stereo 8 Ω** · **480 W mono 2 Ω**
   - THD+N **0.006 % @ 1 W / 4 Ω / 1 kHz** · kesme noktasına kadar **< 0.01 %**
   - SNR **> 111 dB (A-weighted)** · çıkış gürültüsü **< 85 µV (A-weighted)** · PSRR **> 65 dB (BTL, 1 kHz)**
   - Sinyal bant genişliği **100 kHz'e kadar** · verim **%90 (4 Ω)**
   - Güç kaynağı **18 V – 53.5 V** · analog besleme **10.8 V – 13.2 V** · çalışma sıcaklığı **0–70 °C**
   - Kendi koruması: **düşük gerilim · aşırı sıcaklık · kesme · kısa devre** (hata raporlamalı)
   - "Click and pop free startup and stop" iddiası (donanım)
2. Protokol: amplifier beslemesi sabitlenir → T-OUT-03/06/08 testleri bu zincir üzerinden tekrarlanır.
3. Beklenen (CoreMusic kuralı): zincir SNR > 100 dB · THD+N < 0.01 % @ 1 kHz — **ölçülmedi**.
4. Koruma testleri (T-OUT-18): UV/OTP/short senaryoları donanım masası gerektirir → `BLOCKED`.
5. Ölçüm noktası (DAC çıkışı mı, amplifier çıkışı mı) **tasarlanmadı** → `UNKNOWN`.
6. Yükleme empedansı/konfigürasyon (BTL/PBTL) seçimi `UNKNOWN` (tasarım yok).
7. Termal ölçüm yöntemi `UNKNOWN`.
8. Durum `PLANNED` / `BLOCKED`.

### §6.5 P-OUT-5 · Çıkış DC offset ve koruma rölesi protokolü

1. Kural (CoreMusic): **DC offset > 0.5 V → koruma rölesi zorunlu** (kural seti; donanım kuralı).
2. Ölçüm: sessizlik üret (T-OUT-20) → çıkış DC'sini ölç (yüklü ve yüksüz ayrı ayrı).
3. Ölçüm noktası (DAC mi, amplifier mi) tasarlanmadı → karar verilemez (`UNKNOWN`).
4. Röle tepki süresi/zamanlaması tasarlanmadı → `UNKNOWN`.
5. Röle donanım envanteri bu domainde yok → `[[../../K002-electroncis/index|K002-electroncis]]` bağımlılığı.
6. Sonuç: DC değeri + röle karar durumu birlikte yazılır; röle `UNKNOWN` ise karar `—` kalır.
7. PCM3168A DAC "Zero Flag" özelliği (S5) ayrı bir gözlem noktasıdır — röle ile karıştırılmaz.
8. Durum `PLANNED` (bloklu kısım BLOCKED).

### §6.6 P-OUT-6 · Loopback ile çıkış doğrulama protokolü (S4)

1. S4: loopback için `GetDefaultAudioEndpoint` `eRender` ile alınır; `IAudioClient::Initialize`
   ikinci parametresi `AUDCLNT_STREAMFLAGS_LOOPBACK` yapılır; sonra `IAudioClient::GetService`
   ile `IAudioCaptureClient` alınır.
2. Uygulama: bilinen sinyal çalınır → loopback kaydedilir → iki kayıt karşılaştırılır.
3. Amaç: donanım gerekmeden yazılım çıktısının bütünlüğünü doğrulamak.
4. S4 notu: loopback, donanım loopback cihazı olmasa da çalışır; Terminal Services oturumları dahil
   tüm oturumların karışımını yakalar.
5. Körleme korelasyon eşiği tasarlanmadı → `UNKNOWN`.
6. Bu test donanım THD+N'nin yerine geçmez (yalnız bütünlük) — §6.3/§6.4'ten ayrı raporlanır.
7. Q6 kararı (§1 #11/#8) açıksa sonuç `input` sınırına taşınmaz; K003 genel raporunda toplanır.
8. Durum `PLANNED`.

### §6.7 P-OUT-7 · Frekans tepkisi ölçüm protokolü (S9 adayı araç)

1. Tarama: 20 Hz – 20 kHz log-sweep · sabit genlik · ≥ 10 s.
2. Aday araç: Room EQ Wizard (S9, HEAD 200) — **araç seçimi henüz onaylanmadı** → `UNKNOWN`.
3. Ölçüm noktası: DAC çıkışı / amplifier çıkışı (seçim `UNKNOWN`).
4. Karşılaştırma: PCM3168A bant dalgalanması **±0.0018 dB** (DAC filtresi, S5) — referans.
5. Bant genişliği referansı: TPA3255 **100 kHz'e kadar** sinyal bant genişliği (S7) — referans.
6. Sapma eşiği (CoreMusic): tasarlanmadı → `UNKNOWN`; sayı uydurulmaz.
7. Tekrar ≥ 3 · ortam gürültüsü kaydedilir (§9).
8. Durum `PLANNED`.

### §6.8 P-OUT-8 · Jitter ölçüm protokolü (yöntem seçimi açık)

1. **Gerçek:** jitter ölçüm yöntemi (körleme analizi, spektrum, sayaç) bu oturumda **seçilmedi**.
2. Ölçüm noktası (DAC clock mu, dijital giriş mi) `UNKNOWN`.
3. Hedef jitter değeri **UNKNOWN** — `[[../audio-dogrulama|K003 genel doğrulama]]` §3 UNKNOWN tablosuyla aynı kural: sayı uydurulmaz.
4. Gerekli ekipman envanteri `UNKNOWN` (donanım masası yok).
5. Referans platform notu: Core Audio/ASIO dokümantasyonu jitter'a değinir mi → ayrıca fetch gerekir.
6. Bu protokol kapanmadan "jitter düşük/id" iddiası yazılamaz.
7. Bağımlılık: K001 donanım domaini (clock tasarımı) → `[[../../K001-donanım/index|K001-donanım]]`.
8. Durum `BLOCKED` (yöntem/ekipman yok).

## §7 Eşik tablosu (eşik · kaynak · durum)

| # | Eşik | Değer | Kaynak | Durum |
|---|---|---|---|---|
| E1 | Çekirdek biçimi | 32-bit float (ADR-017) | CoreMusic kural seti | ✅ kural |
| E2 | Örnekleme/tampon | 48 kHz · 512 | CoreMusic sartname (ölçüm PLANNED) | ✅ kural |
| E3 | Sistem SNR hedefi | > 100 dB | CoreMusic standardı | ✅ kural |
| E4 | Sistem THD+N hedefi | < 0.01 % @ 1 kHz | CoreMusic standardı | ✅ kural |
| E5 | DC koruma rölesi | > 0.5 V → röle | CoreMusic kural seti | ✅ kural |
| E6 | DAC THD+N (tipik) | −94 dB @ 48 kHz | S5 (TI PCM3168A) | ✅ fetch 2026-10-13 · referans |
| E7 | DAC SNR (tipik) | 112 dB | S5 | ✅ fetch 2026-10-13 · referans |
| E8 | DAC dinamik aralık | 112 dB | S5 | ✅ fetch 2026-10-13 · referans |
| E9 | DAC örnekleme aralığı | 8 kHz – 192 kHz | S5 | ✅ fetch 2026-10-13 |
| E10 | DAC diferansiyel çıkış | 8 V PP | S5 | ✅ fetch 2026-10-13 |
| E11 | DAC filtresi bant dalgalanması | ±0.0018 dB (4×/8× OS) | S5 | ✅ fetch 2026-10-13 · referans |
| E12 | Amplifier THD+N | 0.006 % @ 1 W/4 Ω/1 kHz · <0.01 % kesmeye kadar | S7 (TPA3255) | ✅ fetch 2026-10-13 · referans |
| E13 | Amplifier SNR | > 111 dB (A-weighted) | S7 | ✅ fetch 2026-10-13 · referans |
| E14 | Amplifier çıkış gürültüsü | < 85 µV (A-weighted) | S7 | ✅ fetch 2026-10-13 · referans |
| E15 | Amplifier PSRR | > 65 dB (BTL, 1 kHz) | S7 | ✅ fetch 2026-10-13 · referans |
| E16 | Amplifier güç | 315 W stereo 4 Ω BTL @10 % THD+N | S7 | ✅ fetch 2026-10-13 · referans |
| E17 | Amplifier besleme | 18–53.5 V (güç) · 10.8–13.2 V (analog) | S7 | ✅ fetch 2026-10-13 |
| E18 | Headroom payı (çıkış) | — | — | **UNKNOWN** |
| E19 | Playback gecikme bütçesi | — | — | **UNKNOWN** (§1 #13) |
| E20 | Underrun/xrun davranışı | — | — | **UNKNOWN** (§1 #14) |
| E21 | Jitter hedefi | — | — | **UNKNOWN** |
| E22 | Çoklu çıkış desteği | — | — | **⚠️** (§1 #12) |

**Kural:** `UNKNOWN` satırına sayı yazmak yasaktır.

## §8 Negatif test matrisi

| ID | Senaryo | Beklenen davranış | Bağımlılık | Durum |
|---|---|---|---|---|
| N-OUT-01 | Render cihazı yok | açık hata · sessiz çökme yok | S2 | BLOCKED |
| N-OUT-02 | Çalarken cihaz çıkarılır | hata + fallback (`.ai/AGENTS.md` §17 #6) | Q6/#11 ⚠️ | BLOCKED |
| N-OUT-03 | Underrun (veri yetiştirilemezse) | davranış tanımsız (sessizlik mi tekrar mı) | §1 #14 UNKNOWN | BLOCKED |
| N-OUT-04 | Exclusive hakkı dolu | ikinci istemci reddedilir mi? tanımsız | S3 | BLOCKED |
| N-OUT-05 | DC > 0.5 V | koruma rölesi (kural) · röle envanteri yok | K002 | BLOCKED |
| N-OUT-06 | Kısa devre / aşırı sıcaklık | TPA3255 kendi koruması (S7) · yazılım tepkisi tanımsız | S7 | BLOCKED |
| N-OUT-07 | Kesme (clip) anı | donanım iddiası (<0.01 % THD+N) · yazılım davranışı yok | S7 | PLANNED |
| N-OUT-08 | 48 kHz dışı cihaz | dönüşüm stratejisi tanımsız | §5 T-OUT-12 | BLOCKED |
| N-OUT-09 | Sessizlik üretilirken DC | offset ölçümü (P-OUT-5) | — | PLANNED |
| N-OUT-10 | Başka uygulama çalarken (shared) | karışım davranışı tanımsız | S2 | REFERENCE |

## §9 Ölçüm ortamı ve tekrarlanabilirlik

1. Donanım masası/envanteri `UNKNOWN` (bu domainde yok).
2. Ölçüm noktası (DAC/amp) seçimi `UNKNOWN` — §6.4/§6.7.
3. OS + sürücü sürümü kaydı zorunlu (§11 şablonu).
4. Güç kaynağı/ yük empedansı kaydı zorunlu (S7 besleme aralıkları §7 E17).
5. Termal koşul (0–70 °C çalışma aralığı, S7) ölçümle birlikte kaydedilir.
6. Ortam gürültüsü zemin ölçümü (sessizlik kaydı) her oturumda yapılır.
7. Tekrar sayısı ≥ 3 · medyan raporlanır.
8. Ölçüm yazılımı sürümü kaydedilir (S9 adayı — seçim `UNKNOWN`).
9. §9 dışı koşulda ölçüm "geçersiz" işaretlenir.
10. Yüklü/yüksüz (loaded/unloaded) durumlar ayrı raporlanır.

## §10 Geçme kriterleri (kapı — `draft` → `verified`)

| # | Kapı | Kriter | Durum |
|---|---|---|---|
| K1 | URL bankası | §1 + §4 satırları fetch/HEAD ile doğrulanmış olsun | 🔄 (ALSA URL §4.1'de 404) |
| K2 | Açık sorular | #11 (Q6) ve #12 (çoklu çıkış) kapalı olsun | ❌ açık |
| K3 | Exclusive/fallback | #9 fallback kuralı zincirleme doğrulanmış olsun | 🔄 (kural var, test yok) |
| K4 | Test matrisi | T-OUT-01/03/04/14 çalıştırılmış olsun | ❌ |
| K5 | Protokoller | P-OUT-1…P-OUT-3 sonuçları §11'de olsun | ❌ |
| K6 | Eşikler | E18–E21 kapanmış veya gerekçeli `UNKNOWN` bırakılmış olsun | 🔄 |
| K7 | Koruma | Röle/koruma zinciri (E5) için sahip atanmış olsun | ❌ |
| K8 | Tutarlılık | §2 sayımı hâlâ §1 ile birebir olsun | ✅ (§1 değişmedi) |

**Kapı sonucu:** 8 kriterin **1'i** tam · **3'ü** kısmi · **4'ü** açık → `output` `verified` **OLAMAZ**.

## §11 Kanıt kayıt şablonu

| Tarih | Test ID | Protokol | Ölçüm noktası | OS/sürücü | Ölçüm değeri | Tekrar | Karar | Not |
|---|---|---|---|---|---|---|---|---|
| — | T-OUT-01 | P-OUT-1 | — | — | **henüz yok** | — | — | PLANNED |
| — | T-OUT-03 | P-OUT-3 | DAC çıkışı | — | **henüz yok** | — | — | PLANNED |
| — | T-OUT-04 | P-OUT-5 | — | — | **henüz yok** | — | — | PLANNED |
| — | T-OUT-05 | P-OUT-8 | — | — | **henüz yok** | — | — | BLOCKED |
| — | T-OUT-06 | P-OUT-7 | — | — | **henüz yok** | — | — | PLANNED |

**Doldurma kuralı:** ölçüm yapılmadan değer yazılmaz; boş hücre `—` kalır, `0` yazılmaz.

## §12 Bilinen sınırlar ve riskler

1. Repo'da playback kodu yok (§1 #10) → tüm testler kod olmadan `PLANNED`.
2. Q6 + çoklu çıkış (#12) açık → sınır testleri sahipsiz.
3. §4.1'de ALSA playback URL'i 404 → iddia yeniden kaynaklanmalı.
4. Ölçüm noktası (DAC vs amp) seçilmedi → tüm zincir ölçümleri bloklu.
5. Röle donanım envanteri bu domainde yok (K002 bağımlılığı).
6. TI değerleri referanstır; CoreMusic ölçümü ile karıştırılırsa sahte kanıt oluşur.
7. Jitter protokolü metodolojisiz → "düşük jitter" iddiası yasak.
8. Playback bütçesi (#13) sayıya dönüştürülmeden gerçek zamanlı iddia yazılamaz.
9. PCM5122 **yasaktır** (ADR-038 H001); test donanımında PCM5122 görülürse test geçersiz sayılır.
10. ASIO çıkış yolu (#12/#13 kapıları açık) → T-OUT-17 BLOCKED.

## §13 Otomasyon (CI) matrisi

| Test | Koda bağlanabilir mi? | Engel |
|---|---|---|
| T-OUT-01/02 (render yaşam döngüsü) | evet (kod varsa) | kod yok (§1 #10) |
| T-OUT-03 (THD+N) | hayır | donanım/ölçüm noktası gerekir |
| T-OUT-04 (DC) | kısmen | röle/ölçüm noktası gerekir |
| T-OUT-11 (loopback) | evet | donanım gerekmez — CI adayı |
| T-OUT-14 (dayanıklılık) | evet | çalışma süresi politikası |
| T-OUT-16 (exclusive) | kısmen | çift istemci senaryosu |

**Sonuç:** CI kapısı bu domainde **kurulamaz** (kod yok); en iyi CI adayı T-OUT-11'dir.

## §14 Sorumluluk matrisi

| Alan | Birincil | Onay |
|---|---|---|
| Q6 sınır kararı | Master Orchestrator | Vault Steward |
| Çoklu çıkış kararı (#12) | embedded-engineer | audio-hardware-engineer |
| DAC doğrulama (P-OUT-3/5) | audio-hardware-engineer | qa-engineer |
| Amplifier zinciri (P-OUT-4) | audio-hardware-engineer | — |
| Jitter protokolü (P-OUT-8) | dsp-firmware-engineer | embedded-engineer |
| Röle/koruma | `[[../../K002-electroncis/index|K002-electroncis]]` sahibi | audio-hardware-engineer |
| Ölçüm yazılımı seçimi (S9) | qa-engineer | — |
| Kapı kapanışı (§10) | qa-engineer | Vault Steward |

## §15 Bağlantılar

| Bağlantı | Hedef | İlişki |
|---|---|---|
| Üst anayasa | `[[../audio-master|audio-master]]` | §1 iddialarının kaynağı |
| Domain indeksi | `[[../index|K003-audio index]]` | registry |
| Kardeş — giriş | `[[../input/input-dogrulama|input doğrulama]]` | round-trip/DC bütçesi |
| Kardeş — sürücü | `[[../driver/driver-dogrulama|driver doğrulama]]` | DPC/latency bağımlılığı |
| Kardeş — cihaz | `[[../device/device-dogrulama|device doğrulama]]` | enum/hot-swap |
| Kardeş — motor | `[[../engine/engine-dogrulama|engine doğrulama]]` | pull-model/underrun |
| Genel zincir | `[[../audio-dogrulama|K003 genel doğrulama]]` | E2E kapı |
| Alt alan kapsamı | `[[output-kapsam|output-kapsam]]` · `[[output-ornekler|output-ornekler]]` | tasarım/örnek |
| Üst domain — OS ses | `[[../../K000-isletim-sistemi/isletim-sistemi-audio/index|K000-isletim-sistemi-audio]]` | WASAPI/ASIO katmanı |
| Üst domain — donanım | `[[../../K001-donanım/index|K001-donanım]]` | DAC/amplifier/clock |
| Üst domain — elektronik | `[[../../K002-electroncis/index|K002-electroncis]]` | koruma devresi / röle |

## §16 Frekans tarama planı (çıkış düzgünlüğü)

| # | Frekans | Genlik | Beklenen (kaynak) | Durum |
|---|---|---|---|---|
| F1 | 20 Hz | −12 dBFS | — | PLANNED |
| F2 | 50 Hz | −12 dBFS | — | PLANNED |
| F3 | 100 Hz | −12 dBFS | — | PLANNED |
| F4 | 1 kHz | −12 dBFS | THD+N/SNR referans noktası (§6.3) | PLANNED |
| F5 | 5 kHz | −12 dBFS | — | PLANNED |
| F6 | 10 kHz | −12 dBFS | — | PLANNED |
| F7 | 20 kHz | −12 dBFS | DAC bant sınırı referansı | PLANNED |
| F8 | 16 kHz (Nyquist'e yakın) | −12 dBFS | filtre kesim davranışı (PCM3168A: ±0.0018 dB bant, S5) | PLANNED |

**Karar kuralı:** sapma eşiği tasarlanmadı → `UNKNOWN`; sayı uydurulmaz.

## §17 Jitter / clock değerlendirme matrisi (durum tespiti)

| Konu | Durum | Not |
|---|---|---|
| Ölçüm yöntemi seçimi | **UNKNOWN** | körleme/spektum/sayaç — seçilmedi |
| Ölçüm noktası | **UNKNOWN** | DAC clock · dijital giriş · amplifler çıkışı — seçili değil |
| Hedef değer | **UNKNOWN** | sayı yok (K003 genel dosyası UNKNOWN kuralı ile aynı) |
| Platform dokümanı | REFERENCE | Core Audio (S11) · WASAPI (S2) · ASIO (S12) — jitter için ayrıca fetch gerekir |
| Donanım bağımlılığı | `[[../../K001-donanım/index|K001-donanım]]` | clock tasarımı bu domainin dışında |

## §18 Test sinyali kütüphanesi (çıkış — tasarım, PLANNED)

| ID | Sinyal | Amaç | Durum |
|---|---|---|---|
| SIG-1 | 1 kHz sinüs | THD+N/SNR (§6.3) | PLANNED |
| SIG-2 | 20 Hz–20 kHz sweep | tepki (§16) | PLANNED |
| SIG-3 | DC (0 Hz) | offset (§6.5) | PLANNED |
| SIG-4 | Sessizlik | zemin gürültüsü | PLANNED |
| SIG-5 | Kademeli merdiven (1 dB) | lineerlik (§6.2) | PLANNED |
| SIG-6 | Çift ton | IMD | PLANNED |
| SIG-7 | Darbe | faz/gecikme | PLANNED |
| SIG-8 | Beyaz gürültü | geniş bant zemin | PLANNED |

## §19 Kabul senaryoları (Given / When / Then — hepsi PLANNED)

| ID | Given | When | Then | Durum |
|---|---|---|---|---|
| A-OUT-01 | DAC zinciri hazır · 1 kHz −12 dBFS | §6.3 ölçümü | THD+N/SNR kaynakla yan yana raporlanır | PLANNED |
| A-OUT-02 | sessizlik akışı | §6.5 DC ölçümü | DC ≤ röle eşiği (0.5 V kuralı) · aksi halde röle kararı | PLANNED |
| A-OUT-03 | sweep sinyali | §6.7 | tepki raporu · sapma eşiği yok → karar `—` | PLANNED |
| A-OUT-04 | render açık | cihaz fişi çekil | hata + fallback (BLOCKED — tasarım yok) | BLOCKED |
| A-OUT-05 | veri akışı durdurulur | underrun tetiklenir | davranış tanımsız (#14) → karar yok | BLOCKED |
| A-OUT-06 | iki uygulama | exclusive açılmaya çalışılır | ikincisi reddedilir (tasarım yok) | BLOCKED |
| A-OUT-07 | ≥30 dk playback | kesinti sayacı | kesinti/xrun = 0 | PLANNED |
| A-OUT-08 | loopback kaydı | sinyal çal | korelasyon doğrulanır (§6.6) | PLANNED |
| A-OUT-09 | PCM5122 görüldü (test masasında) | envanter kontrolü | test geçersiz (ADR-038 H001 kuralı) | ✅ kural |
| A-OUT-10 | 48 kHz/512/float32 yapılandırma | her test | E1/E2 kurallarına uygunluk §11'de işaretlenir | PLANNED |

## §20 Anti-pattern denetimi

| # | Anti-pattern | Denetim | Durum |
|---|---|---|---|
| AP-1 | TI tipiklerini ölçüm gibi sunmak | §7 "referans" etiketi zorunlu | ✅ |
| AP-2 | Çalıştırılmamış testi geçti saymak | §5 durum sütunu | ✅ |
| AP-3 | 404 URL'i kanıt taşımak | §4.1 | ✅ |
| AP-4 | Eşik uydurmak | §7 kuralı + §3 | ✅ |
| AP-5 | Loopback'i THD+N ölçümü sanmak | §6.6 madde 6 | ✅ |
| AP-6 | DC rölesi kuralını ölçüm sanmak | §6.5 madde 1 (kural ≠ ölçüm) | ✅ |
| AP-7 | Jitter iddiası metodolojisiz | §17 tamamı UNKNOWN | ✅ |
| AP-8 | PCM5122'yi test donanımına sokmak | A-OUT-09 | ✅ |

## §21 Özet ve sonraki adım

| Metrik | Değer |
|---|---|
| §4 HEAD 200 kaynak | 12 |
| §4.1 GET 404 ölçüm | 1 |
| §5 test satırı | 22 (0 çalıştırılmış) |
| §6 protokol | 8 (0 çalıştırılmış) |
| §7 eşik | 22 (5 kural · 12 fetch referans · 4 UNKNOWN · 1 ⚠️) |
| §8 negatif test | 10 |
| §10 kapı | 8 kriter · 1 tam · 3 kısmi · 4 açık |
| §11 ölçülen değer | **0** |

**Sonraki adım (tek):** ölçüm noktası (DAC çıkışı vs amplifier çıkışı) kararını
audio-hardware-engineer'a kapatırmak — kapanınca P-OUT-3/4/5/7 çalıştırılabilir.

## §22 Ölçüm verisi doğrulama kuralları

| # | Kural | Uygulama |
|---|---|---|
| V1 | Değer birimi açık (dB · dBFS · µs · ms · V · W · Ω) | birimsiz değer reddedilir |
| V2 | Ölçüm koşulu (fs · bit · kanal · kip · yük) satıra eşlik eder | koşulsuz değer reddedilir |
| V3 | Tek değer değil: min/medyan/max + tekrar sayısı | tek değer reddedilir |
| V4 | Araç adı + sürümü kaydedilir (`UNKNOWN` ise ölçüm geçersiz) | araçsız ölçüm geçersiz |
| V5 | Donanım referansı (TI) ile ölçüm aynı hücrede karıştırılmaz | sütun ayrımı zorunlu |
| V6 | 404 URL'den türetilmiş değer girilmez | §4.1 kuralı |
| V7 | `UNKNOWN` eşik varsa "geçti/geçmedi" kararı yazılmaz | karar satırı `—` kalır |
| V8 | Ölçüm noktası (DAC/amp) her satırda belirtilir | noktasız ölçüm sayılmaz |
| V9 | Besleme/ yük koşulu (S7 aralıkları) kaydedilir | koşulsuz güç ölçümü sayılmaz |
| V10 | Her ölçüm ISO tarihsiz girilmez | §11 şablonu |

## §23 Kardeş dosya bağımlılık matrisi

| Beklenen karar | Sahibi | Bu dosyadaki etkisi | Bloklanan satırlar |
|---|---|---|---|
| Q6 input/output ↔ device/driver | Vault Steward | sınır testleri sahipsiz | T-OUT-15 · N-OUT-02 · §10 K2 |
| Çoklu çıkış (#12) | embedded-engineer + audio-hw | hoparlör+kulaklık kapsamı yok | T-OUT-10 |
| Ölçüm noktası (DAC/amp) | audio-hardware-engineer | tüm zincir ölçümleri bloklu | §6.3/§6.4/§6.7 · §9 |
| Röle/koruma envanteri | `[[../../K002-electroncis/index|K002-electroncis]]` | DC protokolü bloklu | §6.5 · N-OUT-05 |
| Clock/jitter tasarımı | `[[../../K001-donanım/index|K001-donanım]]` + dsp-fw | jitter protokolü METHODSIZ | §6.8 · §17 |
| Playback gecikme bütçesi | embedded-engineer + qa | geçme kriteri yok | §7 E19 · §10 K6 |
| Underrun davranışı (#14) | embedded-engineer | tasarım yok | §7 E20 · N-OUT-03 |
| ASIO sürüm/lisans kapıları | `[[../driver/driver-dogrulama|driver]]` | T-OUT-17 BLOCKED | §5 T-OUT-17 |
| DPC/ISR ortam sağlığı | `[[../driver/driver-dogrulama|driver]]` | ölçümler driver'a bağımlı | §13 |

## §24 Sürüm ve revizyon kaydı

| Tarih | Değişiklik | Yapan | Satır etkisi |
|---|---|---|---|
| 2026-10-12 | §1–§3 ilk kanıt tablosu (14 iddia · 5 URL · 4 iç kanıt) | Vault oturumu | 64 satır |
| 2026-10-13 | §4–§24 derinleştirme: 12 HEAD 200 kaynak · 404 negatif ölçüm · 22 test matrisi · 8 protokol · DAC/amp gerçek değerleri · kapı/şablon bölümleri | vault derinleştirme oturumu (append-only) | +396 satır |

**Revizyon kuralı:** bu dosya append-only'dir; §1–§3 satırları değiştirilmez, §2 sayımları sabittir.

## §25 Eksik kanıt özeti (bu dosyanın açık borçları)

| Borç | Tür | Kapanışı |
|---|---|---|
| Playback gecikme bütçesi (ms) | ölçüm + tasarım | P-OUT-1 çalıştırılır → §7 E19 |
| Underrun/xrun davranışı | tasarım | embedded-engineer kararı → §7 E20 |
| Jitter hedefi ve yöntemi | metodoloji | §6.8/§17 kapanışı |
| Ölçüm noktası (DAC vs amp) | tasarım | audio-hardware-engineer kararı |
| Çoklu çıkış kapsamı | kapsam kararı | §1 #12 kapanışı |
| ALSA playback URL yenileme | kaynak | §4.1 → yeni URL fetch |

---

> **Dürüstlük bloğu:** Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler 0 uydurma URL/ölçüm içerir.
> §4'teki 12 URL bu oturumda `Invoke-WebRequest -Method Head` ile **HTTP 200** ölçülmüştür (2026-10-13);
> §4.1'deki URL **404** ölçülmüştür. §5–§21'teki hiçbir test çalıştırılmamıştır, hiçbir ölçüm değeri
> üretilmemiştir; tüm sonuç satırları `PLANNED` / `BLOCKED` / `UNKNOWN` işaretlidir. §7'deki TI değerleri
> (PCM3168A · TPA3255) ilgili ürün sayfalarından **fetch edilmiş referans**tır, CoreMusic ölçümü değildir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
