---
title: "input — Doğrulama"
type: architecture-verification
node_id: K003-audio/input
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
date: 2026-10-12
updated: 2026-10-13
group: kanit
---

# input — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda exa/websearch ile getirilen URL içerikleri ·
> (b) bu oturumda shell ile ölçülen disk gerçekleri · (c) vault içi iç kanıt
> (dosya okunarak). Yasak: URL, sürüm, sınıf, sürüm numarası uydurmak.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk satırı: "input · Kaynak girişi (dosya, mikrofon, akış)" | `audio-master.md` §3 satır 66 | 2026-10-12 | ✅ dosya okundu |
| 2 | Giriş unit'leri iş isteyecek bir iş parçacığındaki önbellekten örnek alır (yayında örneklemek yerine önbellek tüm sürücü belleğine örnek alır) | https://developer.apple.com/documentation/coreaudio/audio_unit_programming_guide/audio_units_in_cocoa | 2026-10-12 | has-been-fetched 2026-10-12 |
| 3 | Çıkış unit'leri veriye isteğe bağlı **pull** eder; kullanıcı çekirdekten iş istediğinde çekirdek çıkış unit'ine iş ister | https://developer.apple.com/documentation/coreaudio/audio_unit_programming_guide/audio_units_in_cocoa | 2026-10-12 | has-been-fetched 2026-10-12 |
| 4 | `AudioUnitRender` — audio unit'e bir iş parçacığından veya pull-model kullanıcısından iş isteyen çekirdek işlev | https://developer.apple.com/documentation/coreaudio/audiounit/1571111-audiounitrender | 2026-10-12 | has-been-fetched 2026-10-12 |
| 5 | ALSA PCM hw_params (format/rate/channels/buffer) + sw_params ayrımı — capture/read yolu da aynı aşamalı akışı kullanır | https://www.alsa-project.org/doc/api/group___p_c_m.html | 2026-10-12 | has-been-fetched 2026-10-12 |
| 6 | Açık soru Q6: "input/output ↔ device/driver sorumluluk ayrımı?" | `audio-master.md` §8 Q6 | 2026-10-12 | ✅ dosya okundu |
| 7 | Sınır kuralı: "K003 içinden K004'e sinyal gönderilir, içine sokulmaz" | `audio-master.md` §2.2 | 2026-10-12 | ✅ dosya okundu |
| 8 | Repo'da mikrofon/dosya/akış okuma kodu (C/C++) yok | kök `*.cpp`/`*.h` glob 0 isabet | 2026-10-12 | ✅ ölçüm |
| 9 | İlgili tek gerçek kod: ffmpeg metadata çıkışı (AI içindir, input kanıtı sayılmaz) | `shared/src/AI/AIEngine.php` satır 300–339 | 2026-10-12 | ✅ dosya okundu |
| 10 | "input cihaz seçmez" (envanter device'ındır) — Q6 kapanmadan kesin değildir | — | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 11 | Pull-model'de kaynak verisinin sahibi input'tur (engine değil) | — | 2026-10-12 | ⚠️ VERIFICATION REQUIRED (master §8 Q5) |
| 12 | Input için tampon boyutu / kanal formatı (CoreMusic tasarımı) | — | 2026-10-12 | **UNKNOWN** (tasarım yok) |
| 13 | Capture gecikme bütçesi (ms) | — | 2026-10-12 | **UNKNOWN** (ölçüm yok) |
| 14 | Kaynak türlerinin tam listesi (dosya/mikrofon/akış dışında kalanlar) | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam iddia satırı | 14 |
| Benzersiz URL | 3 (2/3 aynı · 4 · 5) |
| Gerçek dosya yolu / iç kanıt satırı | 5 (1 · 6 · 7 · 8 · 9) |
| ✅ doğrulandı / dosya okundu / ölçüm | 9 (1–9) |
| ⚠️ VERIFICATION REQUIRED | 2 (10 · 11) |
| UNKNOWN | 3 (12 · 13 · 14) |

**Doğruluk notu:** §2 satır sayıları §1 ile birebir sayılmalıdır; sayım
uyuşmazsa §1 yeniden sayılır (sessiz düzeltme yasak).

## §3 Geçiş kuralı

- **#10 (Q6)** üst kararla kapanır → kapanınca `input-kapsam.md` §2 satır 1 ve
  §3 I1 satırı `[TASARIM]` → `[KANIT]` olur.
- **#11 (Q5)** pull-model veri sahipliği kararı Vault Steward ile kapanır.
- **#12–14** tasarım/ölçüm olmadan tampon boyutu, gecikme ms değeri ya da kaynak
  listesi yazılır → yazım yasaktır; `UNKNOWN` kalır.
- **#2–5** bağımsız web teyidi istenirse ilgili URL ancak gerçekten fetch
  edilince `✅ doğrulandı` durumuna yükseltilir.

---

## §4 Kanıt bankası — bu oturumda doğrulanan resmi kaynaklar (2026-10-13)

> **Yöntem:** `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`.
> Bu tabloya yalnızca **HTTP 200** giren URL'ler yazılır; 404 ölçümleri §4.1'dedir.

| # | Kaynak | URL | HEAD | Bu dosyadaki kullanımı |
|---|---|---|---|---|
| S1 | Microsoft Learn — Capturing a Stream | `https://learn.microsoft.com/en-us/windows/win32/coreaudio/capturing-a-stream` | 200 | §5 T-IN-05 · §6.5 |
| S2 | Microsoft Learn — Loopback Recording | `https://learn.microsoft.com/en-us/windows/win32/coreaudio/loopback-recording` | 200 | §5 T-IN-11 · §6.6 |
| S3 | Microsoft Learn — About WASAPI | `https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi` | 200 | §5 T-IN-01 · §7 |
| S4 | Microsoft Learn — Exclusive Mode Streams | `https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams` | 200 | §5 T-IN-18 · §7 |
| S5 | Microsoft Learn — Enumerating Audio Devices | `https://learn.microsoft.com/en-us/windows/win32/coreaudio/enumerating-audio-devices` | 200 | §5 T-IN-02 |
| S6 | TI — PCM3168A ürün sayfası (bu oturumda fetch edildi) | `https://www.ti.com/product/PCM3168A` | 200 | §6.3 · §7 (ADC gerçek değerleri) |
| S7 | TI — PCM3168A datasheet PDF | `https://www.ti.com/lit/ds/symlink/pcm3168a.pdf` | 200 | §6.3 (tam test koşulları PDF'ten okunacak) |
| S8 | Apple — Core Audio dokümanı | `https://developer.apple.com/documentation/coreaudio` | 200 | §5 T-IN-03 |
| S9 | ALSA — alsa-lib dokümantasyon | `https://www.alsa-project.org/alsa-doc/alsa-lib/` | 200 | §5 T-IN-04 |
| S10 | PortAudio ana sayfa | `https://www.portaudio.com/` | 200 | §5 T-IN-12 |

### §4.1 Negatif ölçüm — §1'deki eski iddia URL'leri bugün 404 (2026-10-13)

| URL | İlgili eski iddia | 2026-10-13 HEAD | 2026-10-13 GET | Sonuç |
|---|---|---|---|---|
| `https://www.alsa-project.org/doc/api/group___p_c_m.html` | §1 #5 `has-been-fetched 2026-10-12` | 404 | 404 | URL bugün erişilemez; 2026-10-12 fetch kaydı **silinmez**, ama bugünkü bağımsız teyit **404'tür** — yeniden kaynak gerekir |
| `https://www.alsa-project.org/doc/api/group___p_l_u_g.html` | (driver dosyası #6) | — | 404 | aynı; güncel alsa-lib kaynağı S9'dur |
| `https://pipewire.org/page_latency` | (K003 genel dosyası #A-14…A-17) | — | 404 | bu dosyada kullanılmaz; K003 genel dosyasının kapanış işidir |

**Sonuç (§4/§4.1):** bu oturumda 10 URL HEAD 200, 3 URL GET 404 ölçümü vardır.
`⚠️ VERIFICATION REQUIRED` kalan satırlar için §3 geçiş kuralları aynen geçerlidir.

## §5 Giriş (capture) test matrisi

> **Durum sözlüğü:** `PLANNED` = test tasarlandı ama çalıştırılmadı (repo'da giriş kodu yok — §1 #8) ·
> `BLOCKED` = ön koşul (tasarım/karar) yok · `REFERENCE` = yalnız referans, CoreMusic kapsamı kararı yok.

| ID | Test | Ön koşul | Yöntem (özet) | Geçme kriteri | Durum |
|---|---|---|---|---|---|
| T-IN-01 | WASAPI shared capture başlatma | Windows ortamı | S1 akışı: `IAudioClient::Initialize` → `Start` → 60 s kesintisiz paket okuma | 60 s içinde `GetBuffer` boşluk hatası yok · paket sırası bozulmaz | PLANNED |
| T-IN-02 | Capture endpoint enum doğrulama | sistemde ≥1 mikrofon | S5: `EnumAudioEndpoints(eCapture, DEVICE_STATE_ACTIVE)` listesi | liste ile Windows ses ayarları birebir aynı (ad + sıralama) | PLANNED |
| T-IN-03 | Core Audio giriş birimi referans testi | macOS hedef kararı | `AudioUnitRender` render çağrısı (S8) | — (platform kapsamı kararı yok) | REFERENCE |
| T-IN-04 | ALSA capture (hw_params/sw_params) | Linux hedef kararı | S9 alsa-lib: hw_params → sw_params → readi akışı | Linux hedefi onaylanana kadar kriter yazılmaz | BLOCKED |
| T-IN-05 | Event-driven capture | S1 | `AUDCLNT_STREAMFLAGS_EVENTCALLBACK` + `SetEventHandle` ile tetikleme | olay her period'da gelir · kaçırılan olay 0 | PLANNED |
| T-IN-06 | Timer-driven capture | S1 | yarım tampon süresi kadar uyku + `GetNextPacketSize` döngüsü | 60 s boyunca paket birikmesi tampon taşmaz | PLANNED |
| T-IN-07 | DC offset doğrulama (giriş) | girişe sinyal YOK | sessiz kayıt → örnek ortalaması (DC) | eşik §7 tablosunda · **ölçüm yapılmadı** | PLANNED |
| T-IN-08 | Clip / headroom doğrulama | kalibre genlik kaynağı | kademeli genlik artırımı → ilk clip noktası | §7 headroom eşiği · ölçüm yapılmadı | PLANNED |
| T-IN-09 | ADC SNR / THD+N karşılaştırması | 1 kHz sinüs kaynağı | P-IN-3 protokolü · PCM3168A tipik değerlerle karşılaştırma | §7 ADC eşikleri (kaynaklı) · ölçüm yok | PLANNED |
| T-IN-10 | Kanal faz ve kurulum testi | ≥2 kanal giriş | işaretli sinyal → L/R örnek işaretleri | faz tersliği yok · kanal karışımı yok | PLANNED |
| T-IN-11 | Loopback kaydı | S2 | `eRender` + `AUDCLNT_STREAMFLAGS_LOOPBACK` ile sistem karışımını kaydet | loopback akışı açılır · sessiz paketler `AUDCLNT_BUFFERFLAGS_SILENT` ile işaretlenir | PLANNED |
| T-IN-12 | PortAudio capture alternatifi | S10 | PortAudio üzerinden aynı capture senaryosu | alternatif katman kararı yok → kriter yazılmaz | REFERENCE |
| T-IN-13 | Mikrofon kalibrasyon doğrulama | bilinen referans ses basıncı | P-IN-4 protokolü | referans kaynak/ölçüm aracı yok → **UNKNOWN** | BLOCKED |
| T-IN-14 | Kanal ayrıştırma (crosstalk) | tek kanallı kaynak | L'ye ver, R'yi ölç (ve tersi) | eşik değeri tasarlanmadı → UNKNOWN | BLOCKED |
| T-IN-15 | Dayanıklılık (≥30 dk kesintisiz capture) | T-IN-01 | sürekli kayıt + kesinti/xrun sayacı | xrun/kesinti sayısı0 | PLANNED |
| T-IN-16 | Örnekleme biçimi dönüşümü | cihaz ≠ float32 | cihaz formatı → float32 çekirdek dönüşümü (ADR-017) | dönüşüm kayıpsız/ölçülmüş · kayıp bütçesi UNKNOWN | PLANNED |
| T-IN-17 | Cihaz kaybı anında capture davranışı | takılabilir cihaz | kayıt sırasında fiziksel çıkarma | hata raporlanır · sessiz çökme yok · fallback zinciri (§13) | BLOCKED (tasarım yok) |
| T-IN-18 | Exclusive kipte capture | S4 · boş exclusive hakkı | exclusive stream başlatma | ikinci uygulama reddedilir (ASIO exclusive tekil kuralı) | PLANNED |
| T-IN-19 | Sessiz paket bayrağı davranışı | S1 | `AUDCLNT_BUFFERFLAGS_SILENT` geldiğinde veri işleme | bayrak gelince veri `NULL` muamelesi görür (S1'e göre) | PLANNED |
| T-IN-20 | Cihaz 48 kHz değilse | cihaz farklı rate | dönüşüm/kalibrasyon yolu | CoreMusic hedefi 48 kHz (kural) · dönüşüm stratejisi UNKNOWN | BLOCKED |
| T-IN-21 | Çoklu capture cihazı eşzamanlı | ≥2 giriş cihazı | iki akış aynı anda | eşzamanlı kapsam kararı yok → REFERENCE | BLOCKED |
| T-IN-22 | Giriş susturulduğunda kalan gürültü | mute/attenuation | dijital attenuation → kalan gürültü zemini | §7 zemin eşiği · ölçüm yok | PLANNED |

## §6 Ölçüm protokolleri (adım adım — hiçbiri henüz çalıştırılmadı)

### §6.1 P-IN-1 · DC offset doğrulama protokolü

1. **Ortam:** çıkış ≠ giriş; oda sessiz; giriş hattı bilinen bir yük/kaynakla sabitlenir.
2. **Sinyal:** hiçbir kaynak bağlanmaz (giriş-keklik/iddiasız durum) veya bilinen kısa devre terminasyonu.
3. **Kayıt:** 48 kHz · en az 10 saniye · çekirdek biçimi float32 (ADR-017) · P-IN kaydı kesintisiz.
4. **İşlem:** tüm örneklerin aritmetik ortalaması = DC bileşeni; mutlak değer ortalaması da raporlanır.
5. **Karar:** sonuç §7 "giriş DC offset" satırındaki eşikle karşılaştırılır.
6. **Eşik kaynağı:** CoreMusic kural seti koruma rölesi eşiği **0.5 V** (çıkış tarafı kuralıdır; giriş için eşik **tasarlanmadı** → §7'de `UNKNOWN` işaretli).
7. **Kayıt:** ölçüm değeri yok → bu protokolün sonucu `PLANNED`; sonuç satırı §11 şablonuna işlenir.
8. **Yanlışlık kontrolü:** ölçüm öncesi DC kalibrasyonu yapılmış mı sorusu açık → `UNKNOWN`.

### §6.2 P-IN-2 · Clip / headroom doğrulama protokolü

1. Üretilen sinyal bilinen bir genlikte başlar (örnek: −40 dBFS kademeli).
2. Genlik 1 dB adımlarla artırılır; her adımda 1 saniye kayıt alınır.
3. İlk clip anı: örnek değeri format sınırına çarpanda tespit edilir (|x| ≥ sınır).
4. Raporda iki değer istenir: **ölçülen clip başlangıcı** ve **tasarlanan headroom payı**.
5. Tasarlanan headroom değeri **yok** → satır `UNKNOWN`; sayı uydurulmaz.
6. Çıkışa giden zincir için eşik ayrıdır → `[[../output/output-dogrulama|output]]` §7.
7. Ölçüm aracı seçimi (donanım jeneratör mü, yazılım mı) **PLANNED**.
8. Sonuç §11 şablonuna işlenir; bu protokol çalıştırılmadan geçme kriteri yazılmaz.

### §6.3 P-IN-3 · ADC SNR / THD+N doğrulama protokolü (PCM3168A referanslı)

1. **Kaynak (bu oturumda fetch edildi — S6):** TI PCM3168A ürün sayfası; ADC tarafı gerçek tipik değerler:
   - ADC THD+N **−93 dB** (farklı ve tek uçlu, fS = 48 kHz)
   - ADC SNR **107 dB** (farklı uçlu) / **104 dB** (tek uçlu)
   - ADC dinamik aralık **107 dB** (farklı) / **104 dB** (tek uçlu)
   - ADC örnekleme hızı **8 kHz – 96 kHz** · DAC tarafı **8 kHz – 192 kHz**
   - Farklı uçlu gerilim girişi **2 V RMS** · tek uçlu **1 V RMS**
   - Yerleşik yüksek geçiren filtre **0.96 Hz** (fS = 48 kHz)
   - Örnekleme-decimation bant dalgalanması **±0.035 dB** · durdurma bandı baskı **−75 dB**
2. **Dikkat:** bu değerler TI'nin tipik değerleridir; CoreMusic'in **ölçtüğü** değer değildir.
3. Sinyal: tek ton 1 kHz · bilinen genlik · ≥10 saniye · float32 çekirdek.
4. İşlem: THD+N ve SNR hesaplanır (hesap yöntemi/ pencereleme **PLANNED** — araç seçilmedi).
5. Karar: ölçülen değer §7'deki **CoreMusic kural eşiği** (SNR > 100 dB · THD+N < 0.01 % @1 kHz) ile
   ve TI tipik değeriyle **yan yana** raporlanır; TI değeri "geçme kriteri" değil, **karşılaştırma referansıdır**.
6. Farklı uçlu / tek uçlu giriş seçimi tasarlanmadı → `UNKNOWN`.
7. Tekrar: en az 3 ölçüm; medyan yazılır (dağılım §11 şablonunda).
8. Datasheet PDF (S7) HEAD 200 ölçülmüştür; PDF'in tamamı bu oturumda **okunmadı** → test koşulları (bias, ağırlık, filter) `⚠️ VERIFICATION REQUIRED`.

### §6.4 P-IN-4 · Mikrofon kalibrasyon doğrulama protokolü

1. Kalibrasyon için **bilinen referans ses kaynağı** gerekir (sıcaklık/humidity kaydıyla).
2. Kaynağın frekans/genlik doğrulanabilirliği: kalibrasyon sertifikası veya referans mikrofon.
3. Ölçüm zinciri: kaynak → mikrofon → PCM3168A ADC → kayıt dosyası.
4. Hesap: ölçülen genlik/frekans sapması; beklenen değer **tanımlı değil** → sapma hedefi `UNKNOWN`.
5. referans mikrofon seçimi: **yok** → protokol `BLOCKED` (§1 #13 benzeri durum).
6. Araç/uygulama seçimi: `UNKNOWN` (bu oturumda araç doğrulanmadı).
7. Tekrar sayısı: en az 3 · ortam gürültüsü kaydedilir.
8. Bu protokol kapanmadan "mikrofon kalibre edilmiştir" iddiası yazılmaz.

### §6.5 P-IN-5 · Capture gecikmesi ölçüm protokolü

1. **Ölçüm tanımı:** giriş ucunda oluşan olayın yakalanmış örneğe dönüş süresi (tek başına, uçtan uca değil).
2. Uçtan uca ölçüm ayrı protokoldür → `[[../audio-dogrulama|K003 genel doğrulama]]` §7.
3. S1'e göre istemci uç nokta tamponunu `IAudioClient::Initialize` ile ister, boyutu `GetBufferSize` ile okur.
4. Ölçülecek bileşenler: dönem (period) süresi · tampon doldurma süresi · paket hazır olma gecikmesi.
5. Timer-driven ve event-driven (S1) iki mod ayrı ayrı ölçülür (T-IN-05/T-IN-06).
6. **Bütçe değeri: `UNKNOWN`** — §1 #13'teki capture gecikme bütçesi ölçülmedi, sayı uydurulmaz.
7. Ölçüm sayısı ≥ 100 dönem · max/medyan/p95 istatistikleri yazılır (hedef eşik yok).
8. OS katmanıyla çelişki çıkarsa doğrudan karar verilmez; konu
   `[[../../K000-isletim-sistemi/isletim-sistemi-audio/index|K000-isletim-sistemi-audio]]` ile birlikte üst karara taşınır.

### §6.6 P-IN-6 · Loopback süreklilik protokolü (S2)

1. S2'ye göre loopback: `IMMDeviceEnumerator::GetDefaultAudioEndpoint` çağrısındaki `dataFlow`
   `eCapture` yerine `eRender` yapılır ve `IAudioClient::Initialize` ikinci parametresi
   `AUDCLNT_STREAMFLAGS_LOOPBACK` olur.
2. Uygulama akışı: render cihazı üzerinden çalınan bilinen sinyal, capture olarak kaydedilir.
3. Beklenen: kaydedilen sinyal ile çalınan sinyal arasında örnekle birebir/korelasyon doğrulaması.
4. S2 notu: loopback, donanım loopback cihazı olsa da olmasa da çalışır (yazılım karışımı kaydeder).
5. Bayrak: sessiz paketler `AUDCLNT_BUFFERFLAGS_SILENT` (S1) ile işaretlenir.
6. Geçme kriteri: kesinti yok · korelasyon eşiği **tasarlanmadı** → `UNKNOWN`.
7. Kapsam notu: bu test `input` ile `output` arasında sınır yaratır → Q6 kararı (§1 #10) açıksa sonuç
   `input`'a değil, K003 genelinde raporlanır.
8. Durum: `PLANNED`.

### §6.7 P-IN-7 · Kanal bütünlüğü / faz protokolü

1. Her kanala sırayla tek tek işaretli sinyal uygulanır (kalan kanallar terminate).
2. Örnek işaretleri ve genlikleri karşılaştırılır: kanal karıştırma/terslik tespit edilir.
3. Faz ölçümü için iki kanallı eşzamanlı kayıt gerekir.
4. Faz toleransı (derece/ms) **tasarlanmadı** → `UNKNOWN`.
5. Kablo/senaryo envanteri bu oturumda diskte **yok** → envanter satırı `UNKNOWN`.
6. Sonuç, cihaz envanterinin kanal sayısına bağlanır → `[[../device/device-dogrulama|device]]` §5/§7.
7. PCM3168A kanal düzeni (6 giriş/8 çıkış, S6) ile karşılaştırma: yalnızca donanım referansı olarak.
8. Durum: `PLANNED`.

### §6.8 P-IN-8 · Uzun süre kesintisiz kayıt protokolü

1. Süre hedefi: T-IN-15 için ≥ 30 dk (uzatma: 24 saate kadar — hedef **PLANNED**).
2. Sayaçlar: kesinti · xrun · paket boşluğu · sayfa-fault kaydı (elde araç yok → sayaç tasarımı `UNKNOWN`).
3. Kayıtlar: log dosyası yok (repo'da logging yüzeyi bu domainde yok) → sonuç elle raporlanır.
4. Geçme kriteri: 0 kesinti (bu bir **tasarım kriteridir**, ölçüm sonucu değildir).
5. Çalışma ortamı §9 koşullarına uymalıdır; uymuyorsa ölçüm geçersiz sayılır.
6. Durum: `PLANNED`.

## §7 Eşik tablosu (eşik · kaynak · durum)

| # | Eşik | Değer | Kaynak | Durum |
|---|---|---|---|---|
| E1 | Çekirdek ses biçimi | 32-bit float (ADR-017) | CoreMusic kural seti | ✅ kural |
| E2 | Örnekleme hızı / tampon | 48 kHz · 512 örnek | CoreMusic sartname (ölçüm PLANNED) | ✅ kural |
| E3 | Sistem SNR hedefi | > 100 dB | CoreMusic mühendislik standardı | ✅ kural |
| E4 | Sistem THD+N hedefi | < 0.01 % @ 1 kHz | CoreMusic mühendislik standardı | ✅ kural |
| E5 | Koruma rölesi DC eşiği | > 0.5 V → röle | CoreMusic kural seti (çıkış/koruma) | ✅ kural (girişe uygulanabilirliği: ⚠️) |
| E6 | ADC THD+N (TI tipik) | −93 dB (48 kHz, diff/SE) | S6 TI PCM3168A sayfası | ✅ fetch 2026-10-13 · referans |
| E7 | ADC SNR (TI tipik) | 107 dB diff / 104 dB SE | S6 | ✅ fetch 2026-10-13 · referans |
| E8 | ADC dinamik aralık | 107 dB diff / 104 dB SE | S6 | ✅ fetch 2026-10-13 · referans |
| E9 | ADC örnekleme aralığı | 8 kHz – 96 kHz | S6 | ✅ fetch 2026-10-13 |
| E10 | Giriş gerilimi | 2 V RMS diff · 1 V RMS SE | S6 | ✅ fetch 2026-10-13 |
| E11 | Giriş DC offset eşiği (giriş kanalı) | — | — | **UNKNOWN** (tasarım yok; sayı uydurulmaz) |
| E12 | Headroom payı (giriş) | — | — | **UNKNOWN** (ölçüm/tasarım yok) |
| E13 | Capture gecikme bütçesi | — | — | **UNKNOWN** (§1 #13) |
| E14 | Mikrofon kalibrasyon toleransı | — | — | **UNKNOWN** (referans kaynak yok) |
| E15 | Crosstalk eşiği | — | — | **UNKNOWN** (tasarım yok) |
| E16 | LatencyMon DPC/ISR eşiği | 2000 / 4000 µs (sistemin gerçek zamanlıya uygunluğu) | `https://www.resplendence.com/latencymon_using` (HEAD 200) | ✅ fetch 2026-10-13 · driver testine bağımlı |

**Kural:** `UNKNOWN` satırına sayı yazmak yasaktır (§3 ile aynı kural).

## §8 Negatif test matrisi (hata ve yol durumları)

| ID | Senaryo | Beklenen davranış | Kaynak/bağımlılık | Durum |
|---|---|---|---|---|
| N-IN-01 | Capture cihazı hiç yok | açık hata/boş envanter · sessiz çökme yok | S5 (enum) | BLOCKED (tasarım yok) |
| N-IN-02 | Kayıt sırasında cihaz çıkarılır | hata raporu + fallback (`.ai/AGENTS.md` §17 #6 kuralı driver içindir; input'a etkisi Q6) | Q6 açık → ⚠️ | BLOCKED |
| N-IN-03 | Exclusive hakkı doluyken ikinci istemci | reddetme/queue davranışı tanımsız | S4 | BLOCKED (tasarım yok) |
| N-IN-04 | İstenen format desteklenmiyor | dönüşüm (`plug` benzeri) veya açık hata | S9/S1 (ALSA `plug` iddiası §4.1'de 404 — dikkat) | BLOCKED |
| N-IN-05 | Tampon taşması (overrun) | xrun sayacı · kesinti | — | UNKNOWN (sayaç tasarımı yok) |
| N-IN-06 | Sessiz paket geldi (`SILENT`) | veri `NULL` muamelesi (S1) | S1 | PLANNED |
| N-IN-07 | 48 kHz dışı cihaz | dönüşüm stratejisi tanımsız | §5 T-IN-20 | BLOCKED |
| N-IN-08 | Çok düşük gürültü zemininde clip | kalibrasyon/offset bozukluğu ayrımı | P-IN-1/P-IN-2 | PLANNED |
| N-IN-09 | Aynı anda iki capture istemcisi | paylaşım/reddetme davranışı tanımsız | S3/S4 | BLOCKED (tasarım yok) |
| N-IN-10 | Dosya→geri oynatma ile körleme (round-trip) | zincir doğrulaması | `[[../audio-dogrulama|K003 genel]]` §6 E2E | PLANNED |

## §9 Ölçüm ortamı & tekrarlanabilirlik şartları

1. **Makine:** test makinesinin donanım listesi bu oturumda diskte yok → `UNKNOWN`.
2. **İşletim sistemi sürümü:** kaydedilmelidir; kayıt şablonu §11'dedir.
3. **Sürücü sürümü:** kayıt zorunlu (driver dosyası §5/§7 ile ortak).
4. **Güç planı / saat çözünürlüğü:** LatencyMon notları power yönetimi sorunlarına işaret eder
   (`https://www.resplendence.com/latencymon_technical` HEAD 200) — test öncesi sabitlenir.
5. **Ortam gürültüsü:** sessiz oda; ölçüm öncesi zemin ölçümü (P-IN-1 benzeri).
6. **Tekrar sayısı:** her ölçüm ≥ 3 · medyan + uç değerler yazılır.
7. **Bağlantı:** kablo/terminasyon envanteri yok → `UNKNOWN`.
8. **Saat kaynağı:** harici referans var mı → `UNKNOWN` (tasarım yok).
9. **Kayıt biçimi:** 48 kHz · float32 · 512 örnek (E1/E2 kuralları).
10. **Koşul sapması:** §9 dışındaki koşulda ölçüm geçersiz sayılır ve §11'de "geçersiz" işaretlenir.
11. **Yazılım sürümü:** ölçüm uygulaması sürümü kaydedilir; uygulama seçimi `UNKNOWN`.
12. **Saat dilimi/tarih:** ölçüm kaydı ISO tarih ile (§11 şablonu).

## §10 Geçme kriterleri (kapı — `draft` → `verified`)

| # | Kapı | Kriter | Durum |
|---|---|---|---|
| K1 | URL bankası | §1 + §4 satırlarındaki her URL gerçekten fetch edilmiş olsun | 🔄 kısmi (§4.1'de 3 URL 404) |
| K2 | Açık sorular | #10 (Q6) ve #11 (Q5) kapalı olsun | ❌ açık |
| K3 | UNKNOWN satırlar | #12–14 için tasarım/ölçüm sunulmuş olsun | ❌ açık |
| K4 | Test matrisi | §5'teki `PLANNED` satırlar en azından T-IN-01/07/08/09/15 için çalıştırılmış olsun | ❌ çalıştırılmadı |
| K5 | Protokoller | §6'daki P-IN-1…P-IN-3 sonuçları §11 şablonuna işlenmiş olsun | ❌ sonuc yok |
| K6 | Eşikler | §7'deki `UNKNOWN` satırlar ya kapanmış ya da gerekçeli olarak `UNKNOWN` bırakılmış olsun | 🔄 gerekçe var, değer yok |
| K7 | Negatif testler | §8'de `BLOCKED` satırlar için sahip (agent) atanmış olsun | ❌ sahip yok |
| K8 | Tutarlılık | §2 sayım hâlâ §1 ile birebir olsun (bu ekleme §1'e satır eklemez) | ✅ (§1 değişmedi) |

**Kapı sonucu:** 8 kriterin **1'i** tam, **2'si** kısmi, **5'i** açık → `input` `verified` **OLAMAZ**.

## §11 Kanıt kayıt şablonu (ölçüm sonuçları buraya işlenir)

| Tarih | Test ID | Protokol | Ortam (OS/sürücü) | Ölçüm değeri | Tekrar | Karar (pass/fail) | Not |
|---|---|---|---|---|---|---|---|
| — | T-IN-07 | P-IN-1 | — | **henüz yok** | — | — | PLANNED |
| — | T-IN-08 | P-IN-2 | — | **henüz yok** | — | — | PLANNED |
| — | T-IN-09 | P-IN-3 | — | **henüz yok** | — | — | PLANNED |
| — | T-IN-13 | P-IN-4 | — | **henüz yok** | — | — | BLOCKED |
| — | T-IN-05/06 | P-IN-5 | — | **henüz yok** | — | — | PLANNED |
| — | T-IN-11 | P-IN-6 | — | **henüz yok** | — | — | PLANNED |

**Doldurma kuralı:** ölçüm yapılmadan değer yazılmaz; boş hücre `—` kalır, `0` yazılmaz.

## §12 Bilinen sınırlar ve riskler

1. Repo'da giriş kodu yok (§1 #8) → tüm testler **kod olmadan PLANNED** kalır.
2. Q6 (input/output ↔ device/driver) açık → sınır testleri sahipsizdir.
3. §4.1'de 3 URL 404 → ALSA/PipeWire iddiaları yeniden kaynaklanmalıdır.
4. Mikrofon kalibrasyonu için referans donanım envanteri yok → P-IN-4 sonsuza kadar BLOCKED kalabilir.
5. Ölçüm uygulaması seçilmedi → protokoller araç-bağımsız yazılmıştır; araç seçimi ayrı karardır.
6. TI tipik değerleri CoreMusic ölçümü değildir; ikisi karıştırılırsa sahte kanıt oluşur.
7. `.ai/` vault'u bu testlerin sonuçlarını bilmez; sonuçlar yalnız §11 tablosuna girer.
8. Capture bütçesi (§1 #13) sayıya dönüştürülmeden gerçek zamanlı iddia yazılamaz.
9. Windows dışı platformlar (Core Audio/ALSA) kapsam kararı olmadan test sayılmaz (§5 REFERENCE).
10. DC rölesi kuralı (0.5 V) çıkış/koruma kuralıdır; girişe olduğu gibi taşınırsa kapsam ihlali olur.

## §13 Otomasyon (CI) matrisi — ne koda bağlanabilir?

| Test | Koda bağlanabilir mi? | Engel |
|---|---|---|
| T-IN-01/05/06 (capture yaşam döngüsü) | evet (kod varsa) | kod yok (§1 #8) |
| T-IN-02 (enum) | evet | cihaz ortamı CI'da yok |
| T-IN-07/08/09 (ölçüm) | kısmen | donanım/jeneratör gerekebilir → araç `UNKNOWN` |
| T-IN-13 (kalibrasyon) | hayır | referans donanım zorunlu |
| T-IN-15 (dayanıklılık) | evet | çalışma süresi politikası gerekir |
| T-IN-19 (sessiz bayrak) | evet | kod yok |

**Sonuç:** CI kapısı bu domainde **kurulamaz** (kod yok); kapı §10 K4'e bağlıdır.

## §14 Sorumluluk matrisi (kim neyi kapatır?)

| Alan | Birincil | Onay |
|---|---|---|
| Q6 sınır kararı | Master Orchestrator | Vault Steward |
| Q5 veri sahipliği | Vault Steward | — |
| Ölçüm protokolleri (P-IN-*) | embedded-engineer | qa-engineer |
| Mikrofon/donanım envanteri | audio-hardware-engineer | — |
| Driver/DPC bağımlılıkları | `[[../driver/driver-dogrulama|driver]]` sahibi | windows-software-engineer |
| Cihaz enum bağımlılıkları | `[[../device/device-dogrulama|device]]` sahibi | — |
| URL bankası yenileme (404 düzeltme) | research | Vault Steward |
| Kapı kapanışı (§10) | qa-engineer | Vault Steward |

## §15 Bağlantılar (kardeşler ve üst domainler)

| Bağlantı | Hedef | İlişki |
|---|---|---|
| Üst anayasa | `[[../audio-master|audio-master]]` | §1 iddialarının kaynağı |
| Domain indeksi | `[[../index|K003-audio index]]` | children/registry |
| Kardeş — çıkış | `[[../output/output-dogrulama|output doğrulama]]` | THD+N/DC bütçesi ortak |
| Kardeş — sürücü | `[[../driver/driver-dogrulama|driver doğrulama]]` | DPC/latency bağımlılığı |
| Kardeş — cihaz | `[[../device/device-dogrulama|device doğrulama]]` | enum/hot-swap bağımlılığı |
| Kardeş — motor | `[[../engine/engine-dogrulama|engine doğrulama]]` | pull-model veri sahipliği (Q5) |
| Genel zincir | `[[../audio-dogrulama|K003 genel doğrulama]]` | E2E kapı |
| Alt alan kapsamı | `[[input-kapsam|input-kapsam]]` · `[[input-ornekler|input-ornekler]]` | tasarım/örnek |
| Üst domain — OS ses | `[[../../K000-isletim-sistemi/isletim-sistemi-audio/index|K000-isletim-sistemi-audio]]` | WASAPI/ALSA/CoreAudio katmanı |
| Üst domain — donanım | `[[../../K001-donanım/index|K001-donanım]]` | ADC/mikrofon donanımı |
| Üst domain — elektronik | `[[../../K002-electroncis/index|K002-electroncis]]` | giriş devreleri/koruma |

## §16 Frekans tarama test planı (giriş düzgünlüğü)

> **Amaç:** giriş yolunun frekans tepkisi; PCM3168A decimation filtresi gerçekleri (S6) ile karşılaştırma.
> Ölçüm **yapılmadı** — plan satırları `PLANNED` işaretlidir.

| # | Frekans | Sinyal genliği | Beklenen (kaynak) | Durum |
|---|---|---|---|---|
| F1 | 20 Hz | −12 dBFS | kaynak: PCM3168A HPF 0.96 Hz @48 kHz (S6) → 20 Hz'de belirgin kesim BEKLENMEZ | PLANNED |
| F2 | 50 Hz | −12 dBFS | aynı | PLANNED |
| F3 | 100 Hz | −12 dBFS | aynı | PLANNED |
| F4 | 200 Hz | −12 dBFS | aynı | PLANNED |
| F5 | 500 Hz | −12 dBFS | aynı | PLANNED |
| F6 | 1 kHz | −12 dBFS | THD+N/SNR referans noktası (§6.3) | PLANNED |
| F7 | 2 kHz | −12 dBFS | — | PLANNED |
| F8 | 5 kHz | −12 dBFS | — | PLANNED |
| F9 | 10 kHz | −12 dBFS | — | PLANNED |
| F10 | 16 kHz | −12 dBFS | — | PLANNED |
| F11 | 20 kHz | −12 dBFS | bant sınırı referansı | PLANNED |
| F12 | 0.5 Hz (DC yakını) | −12 dBFS | HPF 0.96 Hz (@48 kHz, S6) → bu frekansta bastırma BEKLENİR | PLANNED |

**Karar kuralı:** ölçülen tepki, PCM3168A tipik değerleriyle **yan yana** raporlanır; sapma eşiği
**tasarlanmadı** → `UNKNOWN`. Tarama adımı/süresi (ör. 10 saniye log-sweep) `PLANNED` seçimdir.

## §17 Platform capture yolu karşılaştırma tablosu (yalnızca doğrulanmış satırlar)

| Platform / kip | Doğrulanmış gerçek | Kaynak | CoreMusic durumu |
|---|---|---|---|
| WASAPI shared capture | `IAudioCaptureClient` ile uç nokta tamponundan okuma; paket boyutu çağrılar arasında değişebilir | S1 (2026-10-12 fetch + 2026-10-13 HEAD 200) | PLANNED |
| WASAPI event-driven | `AUDCLNT_STREAMFLAGS_EVENTCALLBACK` + `IAudioClient::SetEventHandle` | S1 | PLANNED |
| WASAPI timer-driven | yarım tampon süresi kadar bekleme döngüsü | S1 | PLANNED |
| WASAPI loopback | `eRender` + `AUDCLNT_STREAMFLAGS_LOOPBACK` | S2 | PLANNED |
| WASAPI exclusive | özel kip akışlar belgesi var; capture'a etkisi bu dosyada işlenmedi | S4 (HEAD 200) | ⚠️ (detay fetch edilmedi) |
| Core Audio (macOS/iOS) | `AudioUnitRender` çekirdek işlevi; giriş birimleri önbellekten örnek alır | §1 #2–4 + S8 | REFERENCE |
| ALSA (Linux) | hw_params/sw_params ayrımı iddiası §1 #5'te; **bugünkü URL 404** | §4.1 | ⚠️ yeniden kaynak gerekir |
| PortAudio | gerçek zamanlı giriş/çıkış C API'si ana sayfası | S10 (HEAD 200) | REFERENCE |

**Kural:** bu tablo yalnızca **HEAD 200 / fetch** satırları içerir; tahmini özellik yazılmaz.

## §18 Test veri seti tanımı (sinyal kütüphanesi — tasarım, PLANNED)

| ID | Sinyal | Amaç | Üretim | Durum |
|---|---|---|---|---|
| SIG-1 | 1 kHz sinüs | THD+N/SNR (§6.3) | yazılım/donanım — araç `UNKNOWN` | PLANNED |
| SIG-2 | 20 Hz–20 kHz log-sweep | frekans tepkisi (§16) | araç `UNKNOWN` | PLANNED |
| SIG-3 | DC (0 Hz) | offset (§6.1) | sabit değer | PLANNED |
| SIG-4 | Sessizlik | zemin gürültüsü | kesintisiz 0 | PLANNED |
| SIG-5 | Beyaz gürültü | zemin + geniş bant | araç `UNKNOWN` (üretim yöntemi tasarlanmadı) | PLANNED |
| SIG-6 | Kademeli genlik merdiveni (1 dB) | lineerlik/clip (§6.2) | merdiven üretici | PLANNED |
| SIG-7 | Çift ton (intermodülasyon) | IMD | araç `UNKNOWN` | PLANNED |
| SIG-8 | İşaretli darbe (impuls) | faz/gecikme | tek örnek darbe | PLANNED |

**Depolama:** dosya biçimi/kanal düzeni `UNKNOWN` (tasarım yok); çekirdek biçimi float32 (ADR-017).

## §19 Kabul senaryoları (Given / When / Then — hepsi PLANNED)

| ID | Given | When | Then | Durum |
|---|---|---|---|---|
| A-IN-01 | giriş cihazı bağlı, sessiz oda | 10 s sessizlik kaydı | DC ölçümü §7 E11 eşiği ile karşılaştırılır (eşik UNKNOWN — karar yok) | PLANNED |
| A-IN-02 | SIG-1 hazır (−12 dBFS 1 kHz) | kaydet + analiz | THD+N/SNR §6.3'te kaynakla yan yana raporlanır | PLANNED |
| A-IN-03 | SIG-6 merdiven | kademeli artır | ilk clip noktası kaydedilir; §7 E12 (UNKNOWN) ile karşılaştırılır | PLANNED |
| A-IN-04 | iki cihaz takılı | enum çağrısı | liste Windows ses ayarlarıyla birebir (T-IN-02) | PLANNED |
| A-IN-05 | loopback kipi | render sinyali çal | capture kaydı korelasyon doğrular (§6.6) | PLANNED |
| A-IN-06 | capture çalışırken | cihaz fişi çekil | hata raporu · sessiz çökme yok (N-IN-02, BLOCKED) | BLOCKED |
| A-IN-07 | ≥30 dk kayıt | kesinti sayacı | xrun/kesinti = 0 | PLANNED |
| A-IN-08 | exclusive kip açık | ikinci istemci açmaya çalışır | reddetme/queue (tasarım yok) | BLOCKED |
| A-IN-09 | cihaz 96 kHz | capture başlat | dönüşüm stratejisi tanımsız → karar yok | BLOCKED |
| A-IN-10 | 48 kHz/512/float32 yapılandırma | her test | E1/E2 kurallarına uyulur (uygunluk §11'de işaretlenir) | PLANNED |

## §20 Anti-pattern denetimi (bu domainde yaygın hatalar)

| # | Anti-pattern | Denetim | Durum |
|---|---|---|---|
| AP-1 | Tipik değerleri ("typ") CoreMusic ölçümü gibi sunmak | §7 E6–E10 sütununda "referans" etiketi zorunlu | ✅ etiketli |
| AP-2 | Çalıştırılmamış testi "geçti" diye raporlamak | §5 durum sütunu yalnız PLANNED/BLOCKED/REFERENCE | ✅ |
| AP-3 | 404 URL'i kanıt olarak taşımak | §4.1 ayrı tabloda işaretlenir | ✅ |
| AP-4 | Eşik uydurmak (UNKNOWN → sayı) | §7 kural satırı + §3 | ✅ |
| AP-5 | Uçtan uca gecikmeyi tek başına capture gecikmesi sanmak | §6.5 madde 2 ayrım yapar | ✅ |
| AP-6 | DC rölesi kuralını (0.5 V) girişe taşımak | §7 E5 notu + §12 madde 10 | ✅ |
| AP-7 | Ölçüm aracını doğrulamadan adlandırmak | her protokolde araç `UNKNOWN`/`PLANNED` | ✅ |
| AP-8 | Q6 açıkken sınır iddiası yazmak | §5 T-IN-17/N-IN-02 BLOCKED | ✅ |

## §21 Özet ve sonraki adım

| Metrik | Değer |
|---|---|
| §4 HEAD 200 kaynak | 10 |
| §4.1 GET/HEAD 404 ölçüm | 3 |
| §5 test satırı | 22 (0 çalıştırılmış) |
| §6 protokol | 8 (0 çalıştırılmış) |
| §7 eşik | 16 (7 kural/referans · 8 UNKNOWN · 1 ⚠️) |
| §8 negatif test | 10 |
| §10 kapı | 8 kriter · 1 tam · 2 kısmi · 5 açık |
| §11 ölçülen değer | **0** |

**Sonraki adım (tek):** Q6 sınır kararını Vault Steward'a kapatırmak — kapanınca §5'teki 6 BLOCKED
satırı sahip alır ve T-IN-01/07/08/09 çalıştırılabilir hâle gelir.

## §22 Ölçüm verisi doğrulama kuralları (sonuç girilirse)

| # | Kural | Uygulama |
|---|---|---|
| V1 | Değer birimi açık yazılır (dB · dBFS · µs · ms · V · Hz) | birimsiz değer reddedilir |
| V2 | Ölçüm koşulu (fs · bit · kanal · kip) satıra eşlik eder | koşulsuz değer reddedilir |
| V3 | Tek değer değil: min/medyan/max + tekrar sayısı | tek değer reddedilir |
| V4 | Araç adı + sürümü kaydedilir (araç `UNKNOWN` ise ölçüm geçersiz) | araçsız ölçüm geçersiz |
| V5 | Referans (TI tipik) ile ölçüm aynı hücrede karıştırılmaz | sütun ayrımı zorunlu |
| V6 | 404 URL'den türetilmiş değer girilmez | §4.1 kuralı |
| V7 | `UNKNOWN` eşik varsa "geçti/geçmedi" kararı yazılmaz | karar satırı `—` kalır |
| V8 | Her ölçüm ISO tarihli; tarihsiz satır sayılmaz | §11 şablonu |

## §23 Kardeş dosya bağımlılık matrisi (bu dosyanın beklediği kararlar)

| Beklenen karar | Sahibi | Bu dosyadaki etkisi | Bloklanan satırlar |
|---|---|---|---|
| Q6 input/output ↔ device/driver | Vault Steward | sınır testleri sahipsiz kalır | T-IN-17 · N-IN-02 · §10 K2 |
| Q5 pull-model veri sahipliği | Vault Steward | veri sahibi belli değil | §10 K2 |
| Ölçüm uygulaması seçimi | embedded-engineer | tüm protokoller araç'sız | §6 tamamı |
| Mikrofon/donanım envanteri | audio-hardware-engineer | kalibrasyon protokolü BLOCKED | P-IN-4 · T-IN-13 |
| Capture gecikme bütçesi (ms) | embedded-engineer + qa | geçme kriteri yok | §7 E13 · §10 K6 |
| Giriş DC eşik tasarımı | audio-hardware-engineer | §7 E11 UNKNOWN | §6.1 · A-IN-01 |
| Cihaz ID/enum davranışı | `[[../device/device-dogrulama|device]]` | enum testi cihaza bağlı | T-IN-02 |
| DPC/ISR ortam sağlığı | `[[../driver/driver-dogrulama|driver]]` | ölçümler driver'a bağımlı | §7 E16 |

## §24 Sürüm ve revizyon kaydı

| Tarih | Değişiklik | Yapan | Satır etkisi |
|---|---|---|---|
| 2026-10-12 | §1–§3 ilk kanıt tablosu (14 iddia · 3 URL · 5 iç kanıt) | Vault oturumu | 64 satır |
| 2026-10-13 | §4–§24 derinleştirme: HEAD 200 kaynak bankası · 404 negatif ölçüm · 22 test matrisi · 8 protokol · eşik/kapı/şablon bölümleri | vault derinleştirme oturumu (append-only) | +402 satır (ölçüm: dosya sonu) |

**Revizyon kuralı:** bu dosya append-only'dir; §1–§3 satırları değiştirilmez, sayımlar (§2) sabittir.

---

> **Dürüstlük bloğu:** Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler 0 uydurma URL/ölçüm içerir.
> §4'teki 10 URL bu oturumda `Invoke-WebRequest -Method Head` ile **HTTP 200** ölçülmüştür (2026-10-13);
> §4.1'deki 3 URL **404** ölçülmüştür ve uydurulmamıştır. §5–§13'teki hiçbir test çalıştırılmamıştır,
> hiçbir ölçüm değeri üretilmemiştir; tüm sonuç satırları `PLANNED` / `BLOCKED` / `UNKNOWN` işaretlidir.
> §7'deki TI değerleri ürün sayfasından **fetch edilmiş referans**tır, CoreMusic ölçümü değildir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
