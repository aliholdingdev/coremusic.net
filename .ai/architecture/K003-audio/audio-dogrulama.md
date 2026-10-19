---
title: "K003-audio — Doğrulama Kayıtları"
type: architecture-verification
node_id: K003-audio
node_type: domain
status: draft
date: 2026-10-12
updated: 2026-10-14
parent_id: "K000–K5000 Architecture Registry"
children:
  - engine
  - backend
  - device
  - driver
  - input
  - output
  - routing
  - mixing
  - realtime
  - synchronization
  - diagnostics
  - testing
depends_on:
  - "K001-donanım"
  - "K007-data"
evidence: "exa/websearch ile 2026-10-12 çekilmiş URL listesi (oturum içi URL bankası)"
validation: "kanıt-hiyerarşisi: has-been-fetched 2026-10-12 > ⚠️ VERIFICATION REQUIRED > UNKNOWN"
open_questions:
  - "⚠️ satırları: sistem tarafından has-been-fetched edilmedi → köprü agent ile tamamlanacak (v2 §4.5b)"
  - "UNKNOWN satırları: sürüm/ölçüm değeri kanıtlanana kadar sayı yazılmaz"
group: kanit
---

# K003-audio · Doğrulama Kayıtları (2026-10-12)

> Format (v2 §4.3): her satır `iddia · URL · 2026-10-12 · durum`.
> **durum sözlüğü:** `has-been-fetched 2026-10-12` = bu oturumda fetch edilerek kanıtlandı ·
> `⚠️ VERIFICATION REQUIRED` = URL/format biliniyor ama bu oturumda fetch edilmedi ·
> `UNKNOWN` = sürüm/ölçüm/repodan gelmeyen sayısal değer yok → sayı UYDURULMAZ.
> Zero-hallucination: fetch edilmeyen URL'e sürüm, tarih, star, API uydurulmaz.

## 1. Bu oturumda FETCH EDİLMİŞ kayıtlar (kanıt değeri: en yüksek)

### 1a. Apple Core Audio

| # | iddia | URL | tarih | durum |
|---|---|---|---|---|
| A-01 | Core Audio, Mac ve iOS'taki ses erişimi ve işleme teknolojisi ailesidir. | https://developer.apple.com/documentation/coreaudio | 2026-10-12 | has-been-fetched 2026-10-12 |
| A-02 | `AudioUnitRender` — audio unit'e bir iş parçacığından veya pull-model kullanıcısından iş isteyen çekirdek işlev. | https://developer.apple.com/documentation/coreaudio/audiounit/1571111-audiounitrender | 2026-10-12 | has-been-fetched 2026-10-12 |
| A-03 | Çıkış unit'leri veriye isteğe bağlı olarak pull eder; kullanıcı çekirdekten iş istediğinde çekirdek çıkış unit'ine iş ister. | https://developer.apple.com/documentation/coreaudio/audio_unit_programming_guide/audio_units_in_cocoa | 2026-10-12 | has-been-fetched 2026-10-12 |
| A-04 | Giriş unit'leri iş isterken bir iş parçacığındaki önbellekten örnek alır (yayında örneklemek yerine önbellek tüm sürücü belleğine örnek alır). | https://developer.apple.com/documentation/coreaudio/audio_unit_programming_guide/audio_units_in_cocoa | 2026-10-12 | has-been-fetched 2026-10-12 |
| A-05 | Çıkış unit'leri kuyruğa alınmış istekleri işlerken programınıza iş ister ve iş parçacığını bloke eder. | https://developer.apple.com/documentation/coreaudio/audio_unit_programming_guide/audio_units_in_cocoa | 2026-10-12 | has-been-fetched 2026-10-12 |
| A-06 | iOS'ta ses unit'i üretimi için hazır bir zamanlama tasarlanmıştır (öneri: ilk normal oynatma iş parçacığı başladığında üretilir). | https://developer.apple.com/documentation/coreaudio/audio_unit_hosting_guide/producing_audio_units_in_an_ios_application | 2026-10-12 | has-been-fetched 2026-10-12 |
| A-07 | Core Audio C API, ses denetleyicisi sürücülerini, ses formatlarını, codec'leri, ses uygulama hoparlörlerini, ağ yönlendirmesini, mikrofonları, sesunları ve tüm API'leri kapsar. | https://developer.apple.com/documentation/coreaudio/core_audio_overview/core_audio_api_reference | 2026-10-12 | has-been-fetched 2026-10-12 |
| A-08 | Apple, Core Audio API'nin beta sürümleri için kullanıma hazır şablonlar yayınlar (sample code listesi çekildi). | https://developer.apple.com/documentation/coreaudio/audio_unit_development_fundamentals/using_apple_s_sample_code | 2026-10-12 | has-been-fetched 2026-10-12 |

### 1b. ALSA

| # | iddia | URL | tarih | durum |
|---|---|---|---|---|
| A-09 | `snd_pcm_hw_params` — ses aygıtının donanım parametrelerini ayarlar: bit hızı, örnekleme hızı, kanal sayısı, periodo boyutu, tampon boyutu, boşa çıkarma süresi. | https://www.alsa-project.org/doc/api/group___p_c_m.html | 2026-10-12 | has-been-fetched 2026-10-12 |
| A-10 | `snd_pcm_sw_params` — yazılım parametrelerini ayarlar (START_THRESHOLD, AVAIL_MIN); hw_params'tan bağımsız ayrı yapı. | https://www.alsa-project.org/doc/api/group___p_c_m.html | 2026-10-12 | has-been-fetched 2026-10-12 |
| A-11 | pcm.c örnek kodu: `snd_pcm_hw_params_any` ile erişim modları — `SNDRV_PCM_ACCESS_MMAP_INTERLEAVED` / `MMAP_NONINTERLEAVED` / `RW_INTERLEAVED` / `RW_NONINTERLEAVED` (5 erişim modu). | https://www.alsa-project.org/doc/api/group___p_c_m.html | 2026-10-12 | has-been-fetched 2026-10-12 |
| A-12 | ALSA `hw_params` / `sw_params` / `start` / `writei/area` 4 kavramı ve her değişiklik başına çağrı dizisi belgelendi. | https://www.alsa-project.org/doc/techs/pcm.html | 2026-10-12 | has-been-fetched 2026-10-12 |
| A-13 | Eksik kanal/format desteği `plug` plugin ile yazılım katmanında çözülür. | https://www.alsa-project.org/doc/api/group___p_l_u_g.html | 2026-10-12 | has-been-fetched 2026-10-12 |

### 1c. PipeWire

| # | iddia | URL | tarih | durum |
|---|---|---|---|---|
| A-14 | PipeWire'in tasarım hedefi: canlı ses, düşük gecikme, düşük CPU. | https://pipewire.org/page_latency | 2026-10-12 | has-been-fetched 2026-10-12 |
| A-15 | Düşük gecikmeli programlama: kuyruklar mainloop'ta değil thread olarak çalışır; period zamanı dolduğunda uyanır (thread-per-graph mantığı). | https://pipewire.org/page_scheduling | 2026-10-12 | has-been-fetched 2026-10-12 |
| A-16 | driver.node: `sched.max = quantum`, `node.latency = buffer/quantum` — sürücü düğümü diğer tüm düğümleri eşzamanlı yürütür. | https://pipewire.org/page_latency | 2026-10-12 | has-been-fetched 2026-10-12 |
| A-17 | Toplam gecikme = her düğüm için buffer–quantum toplamı; bağlantılar 1–2 buffer–quantum ekler. | https://pipewire.org/page_latency | 2026-10-12 | has-been-fetched 2026-10-12 |
| A-18 | Nesne modeli: `graph.nodes` / `graph.ports` / `graph.links` — özellik listeleri ve eşleşme tablosu çekildi. | https://pipewire.org/page_objects_design | 2026-10-12 | has-been-fetched 2026-10-12 |
| A-19 | PipeWire JACK yapılandırması `jack.conf` içinde belgelendi (pages dizininden çekilen yapılandırma sayfası). | https://pipewire.org/docs (pages/ altında jack.conf sayfası) | 2026-10-12 | has-been-fetched 2026-10-12 |

## 2. ⚠️ VERIFICATION REQUIRED (URL/format biliniyor, bu oturumda fetch EDİLMEDİ)

> Bu satırlar içerikte markalanmıştır; köprü agent `durum` alanını `has-been-fetched` veya `UNKNOWN` ile değiştirir.

| # | iddia | URL | tarih | durum |
|---|---|---|---|---|
| V-01 | TN2091 — Core Audio sürücü/alt sistem teknik notu (URL oturum bankasında, metin ayrıntısı doğrulanmadı). | https://developer.apple.com/library/archive/technotes/tn2091/_index.html | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| V-02 | `auval` — audio unit çalışma zamanı doğrulama aracı (Apple'ın doğrulama aracı). | https://developer.apple.com/documentation/coreaudio/validating_audio_units | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| V-03 | `AudioUnitProperties.h` — local/remote audio unit ayrımı ve AUBase yapıları. | https://developer.apple.com/documentation/coreaudio/audiounit_properties_h | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| V-04 | AUGraph lifecycle (create → addNode → initialize → start) + render callback örneği. | https://developer.apple.com/library/archive/samplecode/CaptureTheBeat/Introduction/Intro.html | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| V-05 | `AVAudioEngine` — ses işleyicilerini grafikte bağlayan Objective-C/Swift API. | https://developer.apple.com/documentation/avfoundation/avaudioengine | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| V-06 | ALSA plugin mimarisi diyagramı (hw → plugins → plugin çekirdek mimarisi). | https://www.alsa-project.org/doc/techs/pcm.html | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| V-07 | PortAudio — gerçek zamanlı giriş/çıkış C API'si. | https://www.portaudio.com/docs/v19.7.0/docs-api/basic_io.html | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| V-08 | JUCE `AudioDeviceManager` + `LockFreeAudioFifo` (realtime FIFO örneği). | https://juce.com/doc/ (audio module docs) | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| V-09 | ASIO SDK — GitHub'da açık kaynak (v2 §2.3 "ASIO SDK 2.3.4" kaynağı: 2026-09-28 issue/thread). | https://github.com/steinbergmedia/asio-sdk | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| V-10 | WASAPI shared/exclusive mode ve düşük gecikme davranışı. | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| V-11 | JACK "How JACK works" teknik referans bölümü. | https://jackaudio.io/ | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| V-12 | ALSA `pcm.html` - `hw_params`/`sw_params` teknik dokümanı ayrıntıları (access/format/subformat/params). | https://www.alsa-project.org/doc/techs/pcm.html | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |

## 3. UNKNOWN (bu domainin kalan bilinmeyenleri — sayı uydurulmaz)

| id | bilinmeyen | neden UNKNOWN |
|---|---|---|
| A-U1 | ASIO SDK sürümü | v2 §2.3 "ASIO SDK 2.3.4" = sürüm iddiası; URL/commit bu oturumda doğrulanmadı |
| A-U2 | ASIO / CoreAudio SDK yıldız sayısı, son commit tarihi | GitHub API bu oturumda çekilmedi |
| A-U3 | CoreAudioType.h header'daki frame işaretçileri tam listesi | header fetch edilmedi |
| A-U4 | ASIO↔WASAPI↔CoreAudio↔ALSA↔JACK↔PipeWire kompozit eşik oranı (K001) | eşik iddiası yok |
| A-U5 | ASIO alt-tamamlama asahi/rt/preempt/alt-kernel varyantlarının zincir derinliği | repo'da kernel kaynağı yok |
| A-U6 | RT jitter hedefi (≤1 ms) ve latency budget | sayısal hedef henüz kanıtlanmadı |
| A-U7 | CoreAudio framework sürümü / macOS sürümü bağlaması | apple doc sürüm bağlaması fetch edilmedi |

## 4. Validation özeti

- Bu domainde toplam doğrulama satırı: **19 has-been-fetched** + **12 ⚠️** + **7 UNKNOWN**.
- `K003-audio/index.md` `validation` alanı bu dosyayı işaret eder.
- Kanıt hiyerarşisi: `has-been-fetched 2026-10-12` > `⚠️ VERIFICATION REQUIRED` > `UNKNOWN` (v2 §4.5b).
- Açık soru (index Q5/Q6) ile bu dosyadaki ⚠️ satırları kilitlidir: `durum` alanı güncellenmeden köprü aşaması kapanmaz.

---

## 5. Uçtan Uca (E2E) Test Matrisi — ZİNCİR (20 test · PLANNED)

> Bu bölüm 2026-10-13'te, append-only eklendi. Zincir = input → driver →
> engine → mixing/routing → output → cihaz (K001 DAC/amp). Repo'da ses motoru
> kodu YOK (2026-10-13 glob: 0 isabet) ve donanım ölçümü yapılmadı → tüm
> testler PLANNED. Alt dosyalar: [[input/input-dogrulama|input]],
| [[driver/driver-dogrulama|driver]], [[device/device-dogrulama|device]],
| [[engine/engine-dogrulama|engine]], [[output/output-dogrulama|output]].

| ID | Test | Zincir segmenti | Yöntem | Beklenen | Durum |
|---|---|---|---|---|---|
| E2E-01 | Loopback gürültü tabanı | input→output | Cihaz girişi çıkışına loopback, sessiz kayıt | Gürültü tabanı ölçülür (eşik output §7) | PLANNED |
| E2E-02 | Round-trip gecikme | tüm zincir | Click/impuls loopback, gecikme ölç | Toplam round-trip ölçülür | PLANNED |
| E2E-03 | Zincir THD+N bütçesi | input→DAC→amp | 1 kHz sine uçtan uca | Bütçe audio §7'de | PLANNED |
| E2E-04 | Zincir DC offset | input→output | DC ölçüm uçtan uca | <0.5V eşik (K003 kural #10) | PLANNED |
| E2E-05 | 8.1 uçtan uca | engine→output | 8.1 sinyal doğrulama | 8+1 kanal float32 (ADR-017) | PLANNED |
| E2E-06 | Örnekleme bütünlüğü | tüm zincir | 48 kHz stream doğrulama | sr=48000 uçtan uca | PLANNED |
| E2E-07 | Buffer bütünlüğü | engine | 512 örnek doğrulama | Buffer=512 uçtan uca | PLANNED |
| E2E-08 | Exclusive lock bütünlüğü | driver | ASIO lock yaşam döngüsü | Temiz lock/serbest bırakma | PLANNED |
| E2E-09 | Hot-swap zincir etkisi | device→output | Cihaz çıkar/tak | Sessiz duruş + reconnect | PLANNED |
| E2E-10 | Fallback zinciri | driver→device | ASIO→WASAPI düşüş | Fallback uygulanır (R4) | PLANNED |
| E2E-11 | Dış kaynak→çıkış | input→output | Dış kaynak besleme, çıkış doğrulama | Sinyal bozulmadan çıkışta | PLANNED |
| E2E-12 | Sessizlik bütünlüğü | tüm zincir | Sessiz stream | Çıkış sessiz, DC yok | PLANNED |
| E2E-13 | 24 saat dayanıklılık | tüm zincir | Kesintisiz stream | 0 taşma, 0 sızıntı | PLANNED |
| E2E-14 | CPU bütçesi | engine | CPU kullanımı | Ölçüm, sınır §7 #7 | PLANNED |
| E2E-15 | Jitter bütçesi | engine→driver | Callback jitter | Ölçüm, sınır §7 #6 | PLANNED |
| E2E-16 | DSP zinciri THD+N | engine | Sinyal testi | engine-dogrulama P-ENG-08 | PLANNED |
| E2E-17 | Miks bütünlüğü | mixing | Çoklu kaynak miks | Kanal bütçesi + seviye | PLANNED |
| E2E-18 | Eşzamanlı stream | device→output | 2 cihaz eşzamanlı | Durum UNKNOWN (§1 #13) | PLANNED |
| E2E-19 | Güç olayı kurtarma | tüm zincir | Uyku→uyanış | Reconnect + enum güncel | PLANNED |
| E2E-20 | Uçtan uca THD+N bütçesi | input→DAC→amp | 1 kHz sine uçtan uca | Bütçe §7 #3 | PLANNED |

---

## 6. Ölçüm Protokolleri — E2E (6 protokol)

### 6.1 P-E2E-1 — Loopback gecikme protokolü
- **Adımlar:** Dış loopback kablosu · click enjekte et (motor) · kaydı geri
  al · gecikme (ms) hesapla · round-trip = kayıt gecikmesi + motor gecikmesi.
- **Çıktı:** Toplam round-trip gecikme (eşik §7 #2). `PLANNED`.

### 6.2 P-E2E-2 — Zincir THD+N bütçesi protokolü
- **Adımlar:** 1 kHz -20 dBFS sine enjekte · uçtan uca kaydet · THD+N hesapla ·
  bütçe tablosu (§7 #3).
- **Çıktı:** Zincir THD+N ölçümü + bütçe dağılımı. `PLANNED`.

### 6.3 P-E2E-3 — Gürültü tabanı protokolü
- **Adımlar:** Sessiz stream · uçtan uca kayıt · gürültü tabanı (dBFS) hesapla ·
  DC offset ölç.
- **Çıktı:** Gürültü + DC ölçümleri (DC <0.5V kuralı). `PLANNED`.

### 6.4 P-E2E-4 — 24 saat dayanıklılık protokolü
- **Adımlar:** ≥24 saat kesintisiz stream · saatlik taşma/bellek/CPU oku ·
  eğilim grafiği.
- **Çıktı:** Dayanıklılık raporu (hedef 0 taşma/sızıntı). `PLANNED`.

### 6.5 P-E2E-5 — Fallback zincir protokolü
- **Adımlar:** ASIO cihazı kaybettir · WASAPI'ye düş · tekrar ASIO dene ·
  karar kaydı (R4 girdisi).
- **Çıktı:** Zincir geri düşme kanıtı (device-dogrulama P-DEV-06'ya bağlanır).
  `PLANNED`.

### 6.6 P-E2E-6 — Eşzamanlı stream protokolü
- **Adımlar:** 2 cihazda eşzamanlı stream · durum doğrula · taşma/jitter kaydet.
- **Çıktı:** Eşzamanlı davranış kanıtı (§7 #9 `UNKNOWN`). `PLANNED`.

---

## 7. Zincir Eşik Bütçesi Tablosu (E2E)

| ID | Ölçüm | Hedef | Durum / kaynak |
|---|---|---|---|
| 1 | Round-trip gecikme | Ölçülecek, sınır belirlenecek | PLANNED |
| 2 | Gürültü tabanı | Ölçülecek, sınır belirlenecek | PLANNED (output §7'ye bağlanır) |
| 3 | Zincir THD+N bütçesi | Bütçe = input + DAC + amp THD+N toplamı | PLANNED (bütçe hesabı ölçüm sonrası) |
| 4 | DC offset | < 0.5 V (K003 koruma kuralı #10) | PLANNED |
| 5 | Taşma (24 saat) | 0 | PLANNED |
| 6 | Jitter | Ölçülecek, sınır belirlenecek | PLANNED |
| 7 | CPU kullanımı | Ölçülecek, sınır belirlenecek | PLANNED |
| 8 | 8.1 kanal float32 | 8+1 kanal (ADR-017) | PLANNED |
| 9 | Eşzamanlı stream | Durum sürücüye bağlı | `UNKNOWN` (§1 #13 ile tutarlı) |
| 10 | Fallback | Uygulanır (R4) | PLANNED (device-dogrulama §23 taslağı) |
| 11 | Hot-swap zincir etkisi | Sessiz duruş + reconnect | PLANNED |
| 12 | 24 saat bellek eğilimi | 0 sızıntı | PLANNED |
| 13 | Örnekleme bütünlüğü | 48 kHz uçtan uca | PLANNED (sartname) |
| 14 | Buffer bütünlüğü | 512 örnek uçtan uca | PLANNED (sartname) |

---

## 8. Zincir Negatif Testleri

| ID | Negatif senaryo | Beklenen davranış | Durum |
|---|---|---|---|
| E2E-N01 | Zincir ortasında cihaz kaybı | Sessiz duruş, çökme YOK | PLANNED |
| E2E-N02 | Eşik aşımı (DC >0.5V) | Koruma devreye girer (relay) | PLANNED |
| E2E-N03 | Fallback bulunamaz | Düzgün hata raporu, duruş | PLANNED |
| E2E-N04 | 24 saat taşma stresi | Taşma sayacı artar, çökme YOK | PLANNED |
| E2E-N05 | Kanal uyuşmazlığı (8.1 dışı) | Reddetme + rapor | PLANNED |
| E2E-N06 | Uyku→uyanış bozuk kurtarma | Enum + stream yeniden kurulur | PLANNED |
| E2E-N07 | Loopback kablosu çıkarılırsa | Sessiz kayıt, çökme YOK | PLANNED |
| E2E-N08 | Aşırı DSP yükü (taşma zorlama) | Taşma sayacı, sessiz duruş | PLANNED |

---

## 9. Entegre Geçme Kapısı — UÇTAN UCA (gate)

E2E PAS için:
1. E2E-01…20 çalıştırılmış (kod + donanım ön koşulu).
2. P-E2E-1 (round-trip), P-E2E-2 (THD+N bütçesi), P-E2E-3 (gürültü/DC),
   P-E2E-4 (24 saat) tamamlanmış; gerçek değerler §7'ye işlenmiş.
3. §8 negatif testleri 8/8 beklenen davranışı göstermiş (özellikle E2E-N02 DC
   koruma).
4. Alt kapılar (input/output/driver/device/engine §10'ları) en azından koşullu
   PAS'ta — E2E genel PAS için alt kapıların tamamı gerekli.
5. §7 #9 (`UNKNOWN`) kapanmadan genel PAS yok — "eşzamanlı stream durumu
   sürücüye bağlı, ölçülmedi" koşullu notu.

**Ölçüm yoksa PAS YOK.** Uçtan uca zincir ancak gerçek donanım + ölçümle
doğrulanır; statik inceleme yetersizdir.

---

## 10. E2E Kayıt Şablonu

```yaml
verification_type: e2e-chain
test_id: E2E-XX
date: YYYY-MM-DD
environment:
  os_build: ""
  driver: ""
  input_device: ""
  output_device: ""
  buffer_size: 512
  sample_rate: 48000
measurements:
  roundtrip_ms: null
  thd_n_percent: null
  noise_floor_dbfs: null
  dc_offset_v: null
  underruns_24h: null
  jitter_us: null
  cpu_percent: null
result: PLANNED | PASSED | FAILED
notes: ""
```

---

## 11. Sınırlar (dürüstlük)

1. Bu dosyadaki §5-§10 (2026-10-13 eki) **0 uydurma ölçüm/URL içerir**; §1-§4
   (2026-10-12) değiştirilmemiştir.
2. Tüm E2E ölçümleri `PLANNED` — kod yok, donanım ölçümü yapılmadı.
3. §7 #9 eşzamanlı stream `UNKNOWN` (§1 #13 ile tutarlı); #3 THD+N bütçesi
   ancak alt ölçümlerden sonra hesaplanabilir.
4. DC <0.5V kuralı K003 kural #10'dan (koruma relay'i) alınmıştır; ölçüm
   PLANNED.
5. §6 protokolleri gerçek donanım + loopback kablosu gerektirir; laboratuvar
   ortamı henüz kurulmadı.

---

## 12. Bağlantılar (wiki)

**Alt doğrulamalar:** [[input/input-dogrulama|input]] · [[output/output-dogrulama|output]] · [[driver/driver-dogrulama|driver]] · [[device/device-dogrulama|device]] · [[engine/engine-dogrulama|engine]] · [[audio-master|Audio Master]]

**Diğer domain'ler:** [[../K000-isletim-sistemi/isletim-sistemi-audio/index|K000 OS ses katmanı]] · [[../K001-donanım/index|K001 Donanım]] · [[../K002-electroncis/index|K002 Elektronik]]

---

## 13. Dürüstlük bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler (§5-§13) 0 uydurma
URL/ölçüm içerir. §1-§4'teki 19 `has-been-fetched` + 12 `⚠️` + 7 `UNKNOWN`
sayıları değiştirilmemiştir. §5-§10'daki hiçbir test çalıştırılmamış, hiçbir
zincir ölçümü yapılmamıştır; hepsi `PLANNED`/`UNKNOWN` işaretlidir. Alt
doğrulama dosyalarındaki `UNKNOWN`/`VERIFICATION REQUIRED` kayıtları bu
dosyada kapatılmamıştır.

## 14. Zincir segment bütçe dağılımı (hesapsal iskelet — ölçüm yok)

THD+N bütçesi (§7 #3) ancak alt segment ölçümleri geldikten sonra
dağıtılabilir. Bu tablo **iskelettir**; boş hücreler uydurulmaz:

| Segment | THD+N (dB/%) | Ölçüm kaynağı | Durum |
|---|---|---|---|
| Input (ADC, mikrofon yolu) | — | [[input/input-dogrulama|input]] §7 | PLANNED |
| Motor DSP zinciri | — | engine-dogrulama P-ENG-08 | PLANNED |
| DAC (PCM3168A referans) | -94 dB (TI datasheet referansı) | output-dogrulama §1 (referans, CoreMusic ölçümü DEĞİL) | Referans |
| Amplifikatör (TPA3255 referans) | 0.006% @1W/4Ω (TI) | output-dogrulama §1 (referans) | Referans |
| Uçtan uca ölçülmüş | — | P-E2E-2 | PLANNED |

Not: DAC/amp satırları **TI datasheet referans değerleridir** — CoreMusic
ölçümü değildir (output-dogrulama dürüstlük kuralları ile tutarlı). Zincir
bütçesi ancak her segmentin CoreMusic ölçümüyle kapanır.

## 15. Round-trip gecikme iskeleti (hesapsal — ölçüm yok)

| Bileşen | Değer | Kaynak |
|---|---|---|
| OS/driver gecikmesi | `UNKNOWN` | driver-dogrulama #13 (sürücü gecikme bileşeni `UNKNOWN`) |
| Motor callback bütçesi (512/48k) | 10.667 ms (hesap) | engine-dogrulama §22 |
| Codec/amp gecikmesi | `UNKNOWN` | output-dogrulama (ölçüm PLANNED) |
| Round-trip ölçülmüş | — | P-E2E-1 |

Uydurma toplam yazılmadı: `UNKNOWN` + hesapsal + `UNKNOWN` ≠ sayısal toplam.

## 16. Test envanteri — araç ve ön koşul matrisi (E2E)

| Test grubu | Araç | Ön koşul | Donanım? |
|---|---|---|---|
| E2E-01, 03, 04, 11, 20 (loopback/sinyal) | Dış loopback kablosu + sinyal kaynağı | Fiziksel loopback yolu | Evet |
| E2E-02 (round-trip) | Click/impuls üreteci + kayıt | Loopback + motor kodu | Evet |
| E2E-05, 08-10 (8.1/lock/hot-swap/fallback) | Motor + cihaz | 8.1 destekli cihaz + USB hot-swap | Evet (çoklu) |
| E2E-06, 07 (sr/buffer) | Motor logu | Motor kodu | Hayır (kod yeterli) |
| E2E-12 (sessizlik) | Motor + kayıt | Loopback | Evet |
| E2E-13, 14, 15 (dayanıklılık/CPU/jitter) | Sayaçlar + QPC | ≥24 saat koşu | Evet |
| E2E-16 (DSP THD+N) | Sinyal testi | Motor + loopback | Evet |
| E2E-17 (miks) | Çoklu kaynak | Mixing kodu | Hayır (kod yeterli) |
| E2E-18 (eşzamanlı) | 2 cihaz | 2 fiziksel cihaz | Evet (2×) |
| E2E-19 (güç olayı) | Uyku/uyanış | Admin + donanım | Evet |

## 17. Soru ve karar kaydı (append-only)

| # | Tarih | Soru/karar | Durum |
|---|---|---|---|
| 1 | 2026-10-13 | §1-§4 (19 fetched + 12 ⚠️ + 7 UNKNOWN) değiştirilecek mi? | HAYIR — sayılar aynen korundu, yalnız §5+ eklendi |
| 2 | 2026-10-13 | Zincir THD+N bütçesi sayısal olarak yazılacak mı? | HAYIR — alt segment ölçümleri yok, §14 iskelet boş bırakıldı |
| 3 | 2026-10-13 | Round-trip gecikme toplamı yazılacak mı? | HAYIR — `UNKNOWN` + hesapsal bileşenler toplanmaz (§15) |
| 4 | 2026-10-13 | Eşzamanlı stream durumu (§7 #9) | `UNKNOWN` korundu (§1 #13 ile tutarlı) |
| 5 | 2026-10-13 | DC <0.5V eşiği kaynaklı mı? | Evet — K003 kural #10 (koruma relay'i); ölçüm PLANNED |
| 6 | 2026-10-14 | ASIO GPLv3 lisans etkisi (Q1) | **KAPANDI** — GPLv3 açık varyant kararı (NevaEngine `01-gelistirme-plani.md` §7 karar #1 · §28 #3) |
| 7 | 2026-10-14 | ASIO SDK sürüm daralması: "2.3.x" (NevaEnvanter) vs "2.3.4" (v2 §2.3) | A-U1/§2 #V-09 **UNKNOWN/⚠️ KORUNDU** — bağımsız teyit yok; daralma yazıldı, sessiz hizalama YASAK (§28 #7) |

## 18. E2E kontrol listesi (gate öncesi hızlı bakış)

- [ ] Loopback gürültü/DC (E2E-01/04, P-E2E-3) — PLANNED
- [ ] Round-trip gecikme (E2E-02, P-E2E-1) — PLANNED
- [ ] Zincir THD+N bütçesi (E2E-03/16/20, P-E2E-2) — PLANNED
- [ ] 8.1 + float32 + sr/buffer bütünlüğü (E2E-05-07) — PLANNED
- [ ] Lock/hot-swap/fallback zinciri (E2E-08-10, P-E2E-5) — PLANNED + R4 açık
- [ ] 24 saat dayanıklılık + CPU/jitter (E2E-13-15, P-E2E-4) — PLANNED
- [ ] Eşzamanlı stream (E2E-18, P-E2E-6) — `UNKNOWN` (#9)
- [ ] Güç olayı kurtarma (E2E-19) — PLANNED
- [ ] Negatif testler 8/8 (§8) — PLANNED
- [ ] Alt kapılar (5 dosya §10'ları) — tamamı kapalı

## 19. Sürüm geçmişi (dosya)

| Tarih | Değişiklik | Mod |
|---|---|---|
| 2026-10-12 | İlk sürüm (111 satır, §1-§4); 19 fetched + 12 ⚠️ + 7 UNKNOWN | Vault Steward |
| 2026-10-13 | Frontmatter: `type`/`date`/`updated` alanları eklendi; §1-§4 içerik değiştirilmedi | Red Team / Truth Mode |
| 2026-10-13 | §5-§19 eklendi (append-only); 20 E2E test, 6 protokol, 14 zincir eşiği | Red Team / Truth Mode |
| 2026-10-14 | §28 eklendi (append-only): NevaEngine vault iç kanıtı + Q1 kararı + sürüm daralması kaydı; §17'ye #6–#7 | Red Team / Truth Mode |
| — | Gelecek: gerçek E2E ölçümü (loopback donanımı) → §7 zincir bütçesi | PLANNED |

## 20. Dürüstlük bloğu (2026-10-13 eki)

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler (§5-§19) 0 uydurma
URL/ölçüm içerir. §1-§4'teki 19 `has-been-fetched` + 12 `⚠️` + 7 `UNKNOWN`
sayıları aynen korunmuş, durum alanları değiştirilmemiştir. §14'teki DAC/amp
satırları **TI datasheet referans değerleridir** (CoreMusic ölçümü değildir);
zincir bütçesi boş bırakılmıştır. §15'te `UNKNOWN` bileşenler toplanmamıştır.
§5-§18'deki 20 E2E testinin hiçbiri çalıştırılmamıştır; hiçbiri çalıştırılamaz —
repo'da ses motoru kodu YOK (2026-10-13 glob: 0 isabet) ve loopback donanımı
bağlanmamıştır.

## 21. Zincir bağlantı şeması (kavramsal — iddia YOK)

```text
[Dış kaynak / mikrofon]
      │  (analog)
      ▼
[input] ADC yolu — clip/DC/mikrofon kalibrasyon (input-dogrulama §6)
      │  (float32)
      ▼
[driver] OS sürücü yüzeyi — lock, DPC/ISR, stream lifecycle (driver-dogrulama §5)
      │  (float32, 512 örnek)
      ▼
[engine] Motor callback — RT kuralları, DSP zinciri (engine-dogrulama §5)
      │  (float32, 8.1 kanal)
      ▼
[mixing/routing] Kanal eşleme — 8.1 bütçesi (E2E-17)
      │
      ▼
[output] DAC (PCM3168A) → amplifikatör (TPA3255) — THD+N/jitter (output-dogrulama §6)
      │  (analog)
      ▼
[cihaz] Hoparlör/kulaklık (device-dogrulama §5)
```

Şema zincirin **test planı iskeletidir**; hiçbir ok üstünde sayısal değer yoktur
(ölçüm olmadan sayı yazılmaz).

## 22. Protokol detayları — P-E2E-2 THD+N bütçe hesabı (iskelet)

Bütçe hesabı ancak her segmentin CoreMusic ölçümüyle yapılır. Hesap şeması:

```text
Zincir THD+N ≈ toplam bozulma (kabaca; kesin formül ölçüm sonrası)
  = f(ADC segmenti, DSP segmenti, DAC segmenti, amp segmenti)
```

| Adım | İş | Durum |
|---|---|---|
| 1 | ADC segment THD+N ölç (input-dogrulama §6) | PLANNED |
| 2 | DSP segment THD+N ölç (engine P-ENG-08) | PLANNED |
| 3 | DAC+amp segment THD+N ölç (output-dogrulama §6) | PLANNED |
| 4 | Uçtan uca ölç (P-E2E-2) — bütçeyi doğrula | PLANNED |
| 5 | Aşım varsa segment bazında kalem | PLANNED |

Formül/dağılım modeli ölçümden sonra seçilecek; şimdiden sayısal dağıtım
yazılmaz (uydurma yasağı).

## 23. E2E RACI

| Rol | Kişi | Sorumluluk |
|---|---|---|
| Responsible | Bayram Ali | Loopback donanımı kurulumu, tüm E2E koşuları |
| Accountable | Bayram Ali | Entegre kapı (§9) kararı |
| Consulted | Uzman yok | — |
| Informed | Vault Steward | Dürüstlük blokları (§13, §20), `UNKNOWN` kayıtları |

## 24. CI / Otomasyon (E2E)

1. E2E testleri gerçek donanım (loopback kablosu, 2 cihaz, 8.1 çıkış) ister →
   otomatik CI'da **çalıştırılamaz**.
2. CI'da çalıştırılabilir: şablon doğrulaması (§10 YAML şeması), statik
   zincir incelemesi (format/buffer kontrolleri).
3. Donanımlı koşu manuel/lab ortamında; kayıtlar §10 şablonu + ham ölçüm
   dosyalarıyla arşivlenir.

## 25. Negatif test detayları — E2E-N02 DC koruma (kritik)

K003 kural #10: DC offset > 0.5 V → koruma relay'i zorunlu. E2E-N02 akışı:

| Adım | İş | Beklenen |
|---|---|---|
| 1 | Kontrollü DC kaynakla çıkışa >0.5 V DC enjekte | Ölçüm sistemi DC'yi görür |
| 2 | Koruma eşiğini doğrula | Relay devreye girer |
| 3 | DC kaldır | Sistem normale döner |
| 4 | Tekrarla (5×) | Tutarlı davranış |

Durum: `PLANNED` — koruma devresi donanım tarafında K002'de tanımlıdır
(elle tutulur kanıt: K003 kural #10); ölçüm bu dosyanın zincir kapısıdır.

## 26. Kapanış özeti (2026-10-13 eki)

1. Frontmatter alanları eklendi (`type`, `date`, `updated`); §1-§4 satırları
   değiştirilmedi — 19 fetched + 12 ⚠️ + 7 UNKNOWN sayıları aynen duruyor.
2. 20 E2E test + 6 protokol + 14 zincir eşiği + 8 negatif test PLANNED olarak
   tanımlandı; repo'da motor kodu YOK + donanım yok → hiçbiri çalıştırılamaz.
3. Zincir THD+N bütçesi ve round-trip toplamı uydurulmadı — §14-§15 iskelet
   boş bırakıldı; `UNKNOWN` bileşenler toplanmadı.
4. DC <0.5V koruma eşiği (K003 #10) zincir negatif testine (E2E-N02) bağlandı.
5. Entegre kapı (§9) kapalıdır; alt kapıların (5 dosya) tamamı da kapalı —
   zincir PAS'ı ancak alt kapılar + gerçek E2E ölçümüyle kapanır.

---

## 27. Dürüstlük bloğu (nihai — 2026-10-13)

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler (§5-§27) 0 uydurma
URL/ölçüm/formül içerir. §1-§4 değişmemiştir. §14'teki DAC/amp satırları TI
datasheet referansıdır; bütçe hücreleri BOŞ bırakılmıştır. §22'de sayısal
dağıtım formülü yazılmamıştır. §25 DC akışı kural #10'dan gelir, ölçüm PLANNED'dir.
Hiçbir E2E testi çalıştırılmamıştır; durumları `PLANNED`/`UNKNOWN` işaretlidir.

Ek dürüstlük kayıtları (2026-10-13):

1. §1-§4'teki 38 satır (19 + 12 + 7) sayısal özeti değiştirilmemiştir; bu ekle
   **yeni fetched/⚠️/UNKNOWN satır eklenmemiştir** — §5-§27 tamamı PLANNED
   test/protokol bölümleridir, kanıt satırı değildir.
2. §12'deki wiki bağlantıları göreli yollarla verilmiştir; hedef dosyalar
   varlıklarıyla bağlanmıştır (2026-10-13 disk doğrulaması: input/output/driver/
   device/engine dogrulama dosyaları mevcuttur).
3. Bu dosya K003-audio alanının **ana doğrulama kaydıdır**; alt dosyalardaki
   `UNKNOWN`/`VERIFICATION REQUIRED` kayıtları bu dosyada kapatılmaz, yalnızca
   §7/#9 gibi zincir eşiklerine yansıtılır.
4. Gelecek adım: loopback donanımı + motor kodu → P-E2E-1/2/3 ölçümleri →
   §7 zincir bütçesi kapatılır; bu satır append-only güncellenecektir.

---

## 28. 2026-10-14 eki — NevaEngine vault iç kanıtı + Q1 kararı (append-only)

> Bu ek, 2026-10-14 NevaEngine vault turunun (`.ai/projects/NevaEngine/` 00–06 + os/01–04
> okundu) K003-audio ana doğrulama kaydına yansımasıdır. §1–§4 satırları ve 38 satır
> sayısal özeti (19 + 12 + 7) **değiştirilmemiştir**; aşağıda K003'e ait 8 iç kanıt
> satırı **vault-içi dosya okuma** kanıtı olarak eklenir (URL fetch DEĞİLDİR, status
> sözlüğü §1'deki gibi uygulanır: `iç kanıt (dosya okundu)`).

### 28.1 Eklenen iç kanıt satırları (K003 payı)

| # | İddia | Dosya | Tarih | Durum |
|---|---|---|---|---|
| E-1 | JUCE 9 dual lisans: AGPLv3 + Commercial (repo LICENSE.md kanıtlı) | `.ai/projects/NevaEngine/00-sdk-lib-envanter.md` §1 #1 | 2026-10-14 | iç kanıt (dosya okundu) |
| E-2 | ASIO SDK açık-kaynak sayfası: GPLv3 açık varyant + tescilli lisans seçeneği | `00-sdk-lib-envanter.md` §1 · steinberg.net/developers/asiosdk-open (NevaEngine oturum fetch'i) | 2026-10-14 | iç kanıt — master §5 T1 ile aynı çerçeve |
| E-3 | **Q1 KARARI:** ASIO SDK GPLv3 açık varyant kabul edildi — ürün GPLv3 modeline hizalanır | `01-gelistirme-plani.md` §7 karar #1 · `00-sdk-lib-envanter.md` §4 | 2026-10-14 | karar kaydı (kullanıcı direktifi) → §17 #6 |
| E-4 | JUCE = AGPLv3 uyum kararı · kod yolu = `neva.engine/` kök + `vendor/` | `01-gelistirme-plani.md` §7 karar #2–#3 | 2026-10-14 | karar kaydı |
| E-5 | Platform backend entegrasyon örnekleri (ASIO · WASAPI · ALSA/PipeWire · CoreAudio — tümü tahsis-yok deseni, status: planlama) | `.ai/projects/NevaEngine/os/01–04.md` | 2026-10-14 | iç kanıt [TASARIM] — `[KANIT]` DEĞİL |
| E-6 | `neva.engine/` diskte YOK (Faz 0 ⛔ BAŞLAMADI — clone iptal, kalıntı 0) · repo `*.cpp`/`*.h` = 0 | `01-gelistirme-plani.md` §1 Faz 0 durum notu · master §19 | 2026-10-14 | negatif ölçüm — **D6 değişmez** |
| E-7 | ASIO SDK sürüm **numarası**: NevaEnvanter "2.3.x" ↔ v2 §2.3 "2.3.4" | iki vault kaydı karşılaştırması | 2026-10-14 | ⚠️ A-U1/§2 #V-09 **KORUNUR** — bağımsız teyit yok; daralma yazıldı (sessiz hizalama YASAK) → §17 #7 |
| E-8 | backend kök üçlüsü üretildi (kapsam/örnekler/doğrulama — 30 satır kanıt) | `.ai/architecture/K003-audio/backend/backend-dogrulama.md` §1 | 2026-10-14 | iç kanıt (dosya okundu) |

### 28.2 Kapanmayan kapılar (aynen korunur)

| # | Kapı | Neden kapanmadı |
|---|---|---|
| 1 | §2 #V-09 (ASIO SDK GitHub repo) | E-3 lisans **seçimidir**; github.com/steinbergmedia/asio-sdk fetch'i hâlâ yapılmamıştır → ⚠️ |
| 2 | A-U1 (sürüm 2.3.4) | E-7'deki daralma bağımsız teyitsiz → `UNKNOWN` |
| 3 | D6 ([TASARIM] → [KANIT]) | E-6: repo 0 `.cpp/.h` → geçiş imkânsız |
| 4 | §9 entegre kapı + §18 liste | E2E testleri PLANNED; motor kodu + loopback donanımı yok |

### 28.3 Dürüstlük bloğu (2026-10-14 eki)

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen 8 satır **vault-içi dosya okuma**
kanıtıdır (exa/websearch fetch'i değildir); §1–§4'ün 38 satır özeti (19 + 12 + 7)
aynen korunmuştur. Q1 kararı vault karar kaydıdır; formel ADR değildir — kapıların
hiçbiri bu ekle AÇIKTAN KAPANMAMIŞTIR (yalnız §17 #6 soru-karar satırı "KAPANDI"
olarak işaretlenmiştir, kaynak E-3). E-5 örnekleri `[TASARIM]` etiketlidir; E-6
negatif ölçüm D6'nın neden hâlâ kapalı olduğunu belgeler. `index.md` aynı turda
hizalandı (frontmatter open_questions + §1.5 21→19 sayım düzeltmesi + §4 D4/D5
+ Authority tarihi) — ayrı bir `index.md` turu değildir, kayıt buradadır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
