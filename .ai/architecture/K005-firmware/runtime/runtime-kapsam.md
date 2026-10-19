---
node_id: K005-firmware-runtime-kapsam
node_type: kapsam
status: active
parent_id: K005-firmware-runtime
children:
  - K005-firmware-runtime-anayasa
  - K005-firmware-runtime-surucu
  - K005-firmware-runtime-ornekler
  - K005-firmware-runtime-dogrulama
depends_on:
  - K005-firmware-boot-kapsam
  - K005-firmware-firmware-kapsam
evidence:
  - .ai/architecture/K005-firmware/runtime/runtime-anayasa.md
  - .ai/architecture/K005-firmware/boot/boot-kapsam.md
  - .ai/electronic/firmware/index.md
validation: olcum ReadAllLines
open_questions:
  - K005 depoda 0 cpp/h -> kod iddiasi yok
group: mimari
---

# K005 · Runtime Kapsam (Neva Engine çalışma zamanı)

> **Kapsam dosyası (boot-kapsam kalıbı):** çalışma zamanı katmanının sınırı, bileşenleri,
> arayüzleri, bağımlılık grafiği, durum matrisi ve riskleri. Örnek kod **yok** — erişilemeyen
> kaynağa `⚠️ VERIFICATION REQUIRED` yazılır.

## §A Kapsam Sınırı

### A.1 Ne kapsar

1. **Session yaşam döngüsü** — başlat, oynat, duraklat, durdur, kapat (runtime-anayasa §2).
2. **Ses akışı yolu (hot path)** — ASIO callback → DSP zinciri → 8.1 kanal çıkışı (runtime-surucu).
3. **DSP modülleri** — EQ, dinamik, upmix, canal yönetimi (runtime-ornekler).
4. **RTOS/örtü katmanı** — görev önceliği, kuyruk, statik bellek (runtime-anayasa §9–§10).
5. **Donanım sürücü soyutlaması** — I2S/TDM, USB audio, cihaz entegrasyonu.
6. **Çalışma zamanı doğrulama** — overrun, kilit, taşma senaryoları (runtime-dogrulama).

### A.2 Kapsam dışarı

1. **Boot öncesi akış** —.power-on, ön-yükleyici → `boot/` alt dalı (K005-firmware-boot).
2. **Süreç/güncelleme akışı** — OTA paketi, imza zinciri → `update/` alt dalı.
3. **Güvenlik politikası** — güvenli önyükleme, anahtar saklama → `security/` alt dalı.
4. **Teşhis/ölçüm altyapısı** — log, metrik → `diagnostics/` alt dalı.
5. **Güç/termal koruma** — röle, fan → `power-control/` alt dalı.
6. **Test stratejisi tanımı** — plan/kapılar → `testing/` alt dalı (runtime burada yalnız
   çalıştırılabilir doğrulama senaryosu üretir).

### A.3 Sınır çizelgesi

| Konu | Sahibi | Runtime'ın rolü |
|---|---|---|
| Session başlatma | runtime | Sahibi — kaynağa bağlanır |
| ASIO callback | runtime | Sahibi — tek Exclusive Lock sahibi |
| DSP zinciri | runtime | Sahibi — zincir sırası |
| OTA uygulama | update | TETİKLENİR (runtime yalnız bayrak okur) |
| Güvenli boot | boot | Bitmiş kabul edilir; runtime doğrular |
| Teşhis logu | diagnostics | Yazar (kilitli kuyruk) |
| DC/termal röle | power-control | Eşik aşımında KORUMA çağırır |
| Test kapısı | testing | Senaryo çalıştırır, kararı vermez |

## §B Bileşen Envanteri

### B.1 Çekirdek bileşenler

| # | Bileşen | Sorumluluk | Durum |
|---|---|---|---|
| 1 | `RuntimeKernel` | Oturum yaşam döngüsü | vault md — kod YOK |
| 2 | `AudioEngine` | ASIO/WASAPI başlatma + callback | vault md — kod YOK |
| 3 | `DspChain` | Modül zinciri, örnek işleme | vault md — kod YOK |
| 4 | `BufferPool` | Statik tampon, önceden ayırma | vault md — kod YOK |
| 5 | `RtosBridge` | Görev/kuyruk/kesme soyutlama | vault md — kod YOK |
| 6 | `DriverLayer` | I2S/TDM, USB, cihaz soyutlama | vault md — kod YOK |
| 7 | `ClockDomain` | Örnekleme saati, kilitleme | vault md — kod YOK |
| 8 | `FaultHandler` | Overrun/taşma/kanal hata tepkisi | vault md — kod YOK |

### B.2 Durum kodu (kod bulunabilirliği)

1. **Depo kanıtı (2026-10-15, glob):** `*.cpp` 0 · `*.h` 0 → hiçbir bileşenin kaynak dosyası
   depoda **yok**.
2. **Vault kanıtı:** her bileşen için yalnız Markdown şartname/analiz dosyaları var
   (bkz. `.ai/architecture/K005-firmware/runtime/index.md` §ÇOCUKLAR).
3. **Sonuç:** bu envanter bir **tasarım envanteridir**; üretim kodu envanteri değildir.
   Aksini iddia etmek zero-hallucination ihlali olur.

## §C Arayüzler

### C.1 Dahili arayüzler (sözleşme seviyesi)

| Arayüz | Yön | Sözleşme |
|---|---|---|
| Session ↔ Engine | engine'e | başlat/durdur, hata kodu döner |
| Engine ↔ DspChain | zincire | blok blok örnek, 512 örnek varsayılan |
| DspChain ↔ BufferPool | havuza | statik tahsis, çalışma anında allocate YOK |
| Runtime ↔ RtosBridge | örtüye | görev oluştur/yayınla (statik) |
| Runtime ↔ DriverLayer | sürücüye | open/start/stop/close, noexcept |
| Runtime ↔ Diagnostics | teşhise | lock-free kuyruğa yaz |
| Runtime ↔ PowerControl | korumaya | eşik olayı (bayrak) |
| Runtime ↔ Update | güncellemeye | "update bekliyor" bayrağı oku |

### C.2 Dış bağımlılık arayüzleri

1. **Donanım** — ASIO SDK (sürüm kanıtı yok → `⚠️ VERIFICATION REQUIRED`), I2S/TDM DMA,
   USB ses sınıfı.
2. **İşletim sistemi** — Windows (WASAPI/ASIO), bare-metal XMOS (XU316), FreeRTOS
   (STM32/ESP32) — `.ai/electronic/firmware/index.md` §9 cihaz tablosu.
3. **Bellek** — statik/önceden ayrılmış; serbest çalışma zamanı tahsisi **yasak**
   (firmware index §5: "static allocation, heap yasak").

### C.3 Hata sözleşmesi

| Hata sınıfı | Davranış |
|---|---|
| Callback overrun | Bayrak + sıfırlı çıkış (taşma senaryosu) |
| Kanal sayısı uyuşmazlığı | 8.1'e kadar düşürme veya reddetme (şartnameye göre) |
| Saat kilitlenemedi | Durdur + teşhise rapor |
| BufferPool tükenme | Yeni oturumu REDDET (çalışma anı tahsis YOK) |
| Kilitli yol tespidi | Rapor et, ses yolunu kilitleme |

## §D Bağımlılık Grafiği

```text
RuntimeKernel
├── AudioEngine ── DriverLayer ── (I2S/TDM · USB · cihaz)
│        └── ClockDomain
├── DspChain ── BufferPool (statik)
├── RtosBridge ── (FreeRTOS / bare-metal örtü)
├── FaultHandler ── Diagnostics (lock-free kuyruk)
├── (bayrak) ── PowerControl (koruma)
└── (bayrak) ── Update (OTA)
```

### D.1 Katman kuralı

1. **Yukarı bakış yasak** — DriverLayer, RuntimeKernel'i tanımaz.
2. **Aşağı bakış tek yönlü** — RuntimeKernel tüm alt bileşenlere bakar.
3. **Yanal bağımlılık** — yalnız bayrak/kuyruk üzerinden (payload taşımaz).

## §G Durum Matrisi

| Durum | Aşamaya giriş | Çıkış koşulu | Hata |
|---|---|---|---|
| `IDLE` | ilk açılış / oturum kapanışı | session_start | — |
| `OPENING` | engine.open() | cihaz+saat hazır | cihaz bulunamadı |
| `PRIMED` | buffer havuzu tahsisi | ilk callback bekleniyor | havuz tükenme |
| `RUNNING` | ilk callback | session_stop | overrun bayrağı |
| `DEGRADED` | overrun/saat kaybı | toparlanma veya stop | teşhise rapor |
| `PROTECT` | koruma eşiği (DC/termal) | eşik düşüşü | röle olayı |
| `UPDATING` | update bayrağı set | güncelleme bitişi | doğrulama hatası |
| `CLOSING` | session_stop | driver.close() | kilit bırakma hatası |

### G.1 Geçiş kuralları

1. `RUNNING → DEGRADED` yalnız overrun/saat olayıyla; otomatik `RUNNING`'e dönüş **kullanıcı
   onayı** olmadan yapılmaz (güvenli taraf).
2. `* → PROTECT` her durumdan mümkündür ve **en yüksek önceliktir**.
3. `PROTECT → UPDATING` geçişi **yasaktır** (koruma açıkken güncelleme başlamaz).

## §N Normlar (kapsam düzeyi)

| # | Norm | Kaynak | Kapsamdaki yeri |
|---|---|---|---|
| 1 | 512 örnek / 48 kHz | sartname (ölçüm PLANNED) | §B havuz boyutu |
| 2 | 32-bit float veri | ADR-017 | §C sözleşmeler |
| 3 | Lock-free / noexcept / sıfır tahsis | CoreMusic kural seti | §H yasaklar |
| 4 | alignas(64) | CoreMusic kural seti | §B tampon hizası |
| 5 | Heap yasak (statik tahsis) | firmware index §5 | §B.2, §D.1 |
| 6 | 32 görev üst sınırı | firmware index §5 | §R kaynak |

## §H Yasaklar (kapsam içi)

1. **Ses yolunda `malloc/new/delete`** → yasak (kural 1).
2. **Ses yolunda `mutex/lock_guard/unique_lock`** → yasak (kural 2).
3. **Audio callback'te `noexcept` eksikliği** → yasak (kural 3).
4. **Çalışma anında tampon tahsisi** → yasak (firmware index §5).
5. **PCM5122** → mutlak yasak; yalnız PCM3168A (ADR-038 H001).
6. **Depo kodu varmış gibi yazılmak** → yasak (0 cpp/h, 2026-10-15).

## §O Riskler

| # | Risk | Etki | Önlem |
|---|---|---|---|
| 1 | ASIO Exclusive Lock çakışması (tek uygulama) | başlatma reddi | Tek sahiplik + kullanıcı bilgisi |
| 2 | Callback overrun | ses kesintisi/taşma | Sıfırlı çıkış + DEGRADED |
| 3 | Serbest tahsis sızıntısı | jitter/taşma | Statik havuz + tükenmede oturum reddi |
| 4 | 8.1 kanal eşleşmezliği | kanal kayması | Açılışta doğrulama, reddetme |
| 5 | Saat/DAE uyuşmazlığı | frekans kayması | Kilitleme başarısızsa durdur |
| 6 | Kilitli yol (yanlış API) | gecikme | Statik/desen denetimi + gözden geçirme |
| 7 | Kod bulunmamasına rağmen "çalışıyor" raporu | yanlış güven | 0 cpp/h beyanı zorunlu |

## §R Kaynaklar (salt vault)

| # | Dosya | Rol |
|---|---|---|
| 1 | `.ai/architecture/K005-firmware/runtime/runtime-anayasa.md` | Bu dalın anayasası |
| 2 | `.ai/architecture/K005-firmware/runtime/runtime-surucu.md` | Sürücü/hot-path |
| 3 | `.ai/architecture/K005-firmware/runtime/runtime-ornekler.md` | Örnek şablonlar (md) |
| 4 | `.ai/architecture/K005-firmware/boot/boot-kapsam.md` | Kalıp (bu dosyanın şablonu) |
| 5 | `.ai/electronic/firmware/index.md` | RTOS/cihaz/güncelleme gerçekleri |
| 6 | `.ai/electronic/firmware/rtos.md` | Görev/kuyruk derinliği |
| 7 | `.ai/architecture/K005-firmware/firmware-kapsam.md` | K005 üst kapsam |

## §U Güncelleme

| Tarih | Değişiklik |
|---|---|
| 2026-10-15 | İlk üretim — boot-kapsam kalıbında runtime kapsamı; 0 cpp/h dürüstlük beyanı |

## §E Bileşen Sözleşmeleri (derinleştirme eki)

### E.1 RuntimeKernel — oturum yaşam döngüsü ayrıntısı

| Çağrı | Ön koşul | Sonuç | Hata yolu |
|---|---|---|---|
| `create(config)` | — | Statik yapılar yerleşir, havuz tahsis edilir | Havuz boyutu geçersizse reddet |
| `open(device)` | create başarılı | Cihaz Exclusive Lock alınır | Lock reddi → temiz çıkış |
| `start()` | open başarılı | İlk callback kuyruğa alınır | Saat yoksa reddet |
| `stop()` | start yapıldı | Callback durur, tamponlar sıfırlanır | Durdurma zaman aşımı → DEGRADED |
| `close()` | stop yapıldı | Lock bırakılır, kaynak geri verilir | Bırakma hatası → rapor |
| `destroy()` | close yapıldı | Statik alan sıfırlanır | — |

1. **Sıra zorunlu:** create → open → start → stop → close → destroy; sıra dışı çağrı reddedilir.
2. **Yeniden giriş:** `open()` açıkken tekrar çağrısı hata kodu döndürür (sessiz başarısızlık yok).
3. **Koruma öncelikli:** her aşamada PROTECT bayrağı okunabilir; set ise ilerleme durdurulur.

### E.2 AudioEngine — ASIO/WASAPI başlatma akışı

```text
engine.open():
  1. sürücü listesini oku (salt okunur)
  2. hedef cihazı seç → Exclusive Lock dene
  3. lock REDDEDİLİRSE → kullanıcıya tek net hata (kural 9: tek uygulama)
  4. lock ALINIRSA → sample rate / buffer size eşleştir (512 / 48 kHz varsayılan)
  5. callback imzasını noexcept olarak kaydet
  6. clock kilidi doğrula → başarısızsa geri al + reddet
```

1. **Eşleşme tablosu** (açılışta doğrulanır, uyuşmazlık = reddetme):

| Parametre | İstenen | Uyuşmazlık tepkisi |
|---|---|---|
| Örnekleme | 48000 Hz | Reddet (yanlışlıkla dönüştürme YOK) |
| Blok | 512 örnek | Reddet veya şartnamedeki alternatife düş (ikinci seçenek PLANNED) |
| Kanal | 8.1'e kadar | Fazla kanalı reddet, eksik kanalı bilgi ver |
| Format | 32-bit float | Başka format → reddet |

### E.3 DspChain — zincir sırası sözleşmesi

| # | Aşama | Yön | Girdi/çıktı | Not |
|---|---|---|---|---|
| 1 | Giriş kazancı | mono-blok | float → float | Taşma koruması ilk sırada |
| 2 | EQ (bantlar) | kanal başına | float → float | Sıra değişimi yalnız oturum dışı |
| 3 | Dinamik (sıkıştırma/sınırlayıcı) | kanal başına | float → float | Ölçüm bloğu sabit |
| 4 | Upmix/downmix | kanal matrisi | N kanal → 8.1 | Matris sabit, çalışma anı tahsis yok |
| 5 | Çıkış kazancı + DC filtresi | kanal başına | float → float | DC koruma ile uyumlu |
| 6 | Kanal yönlendirme | — | zincir → sürücü | Kanal sırası korunur |

1. **Değişmezlik:** 1→2→3→4→5→6 sırası oturum içinde **değiştirilemez** (oturum dışı yeniden
   kurulur) — pop/klik ve gecikme bütçesi bu sıraya bağlıdır.
2. **Durumlu modüller** (dinamik, filtre) oturum durdurulurken **sıfırlanır**; yeniden
   başlatmada bayrak artefaktı üretilmez.

### E.4 BufferPool — statik yerleşim

| Bölme | Boyut (mantıksal) | Hizalama | Kim kullanır |
|---|---|---|---|
| Girdi tamponu | blok × kanal × float | alignas(64) | Engine → zincir |
| Ara tampon | zincir aşamaları arası | alignas(64) | DspChain |
| Çıktı tamponu | blok × 8.1 × float | alignas(64) | Zincir → sürücü |
| Ölçüm penceresi | dinamik ölçüm bloğu | alignas(64) | Dinamik aşama |
| Teşhis kuyruğu | sabit kapasite | alignas(64) | Teşhis yazıcı |

1. **Tahsis anı:** yalnızca `create()`; çalışma anında (callback) tahsis **yasak**.
2. **Tükenme davranışı:** havuz yetersizse `open()` reddedilir — çalışma anı sessiz taşma yok.
3. **Kapasite kaydı:** her bölmenin kapasitesi teşhise yazılır (denetlenebilir iz).

## §F Arayüz Ayrıntıları (derinleştirme eki)

### F.1 DriverLayer — soyutlama tablosu

| İşlem | Anlam | noexcept | Geri dönüş |
|---|---|---|---|
| `open(config)` | Cihazı aç, kilidi al | Evet | durum kodu |
| `start(callback)` | Akışı başlat | Evet | durum kodu |
| `stop()` | Akışı durdur | Evet | durum kodu |
| `close()` | Kilidi bırak | Evet | durum kodu |
| `channels()` | Kanal sayısı sor | Evet | sayı |
| `latency()` | Gecikme (örnek) sor | Evet | örnek |

1. **Sürücü aileleri:** I2S/TDM (DAC çıkışı), USB ses sınıfı, cihaz entegrasyonu — her aile için
   aynı alt arayüz, farklı taşıyıcı.
2. **Yasak:** arayüzde `std::string`/`std::vector` gibi çalışma anı tahsis yapan dönüş tipleri
   **kullanılmaz**; durum kodu + sabit tampon.

### F.2 RtosBridge — görev/öncelik haritası

| # | Görev | Öncelik (yüksek = üstün) | Tahsis | Not |
|---|---|---|---|---|
| 1 | Koruma/fault | 1 (en yüksek) | statik | PROTECT bayrağı tarar |
| 2 | Ses callback | 2 | statik (ASIO/RTOS) | Hot path |
| 3 | Teşhis boşaltma | 3 | statik | Kuyruğu diske/pipe'a |
| 4 | UI/kontrol | 4 | statik | Parametre yayını (atomik) |
| 5 | Bekleyen güncelleme | 5 (en düşük) | statik | Yalnız boşta çalışır |

1. **Sınır:** firmware index §5 → en fazla 32 görev, statik kayıt, dinamik yığın yok.
2. **Bekleyen kesme/iletişim:** yalnız lock-free kuyruk veya atomik bayrak (kural 2).

### F.3 Teşhis el sıkışması (runtime → diagnostics)

| Alan | Tür | Anlam |
|---|---|---|
| seq | atomik sayaç | Yazılan olay sırası |
| overrun | atomik sayaç | Overrun birikimi |
| xrun | atomik sayaç | Donanım taşması |
| protect | atomik bayrak | PROTECT durumu |
| last_error | atomik kod | Son hata kodu |

1. **Dolgu davranışı:** kuyruk doluysa **sayac artar, ses kesilmez** (teşhis ses yoluna göre
   ikincildir).
2. **Bileşen yönü:** teşhis yalnızca yazar; runtime teşhis kuyruğunu **bloke ederek okumaz**.

## §I Gecikme Bütçesi (mantıksal dağılım)

| Aşama | Bütçe (mantıksal) | Ölçüm | Aşım tepkisi |
|---|---|---|---|
| Sürücü girişi → tampon | blok gecikmesi içinde | PLANNED | xrun sayacı |
| Zincir işleme (1..5) | blok süresinin altında | PLANNED | overrun |
| Çıkış → DAC | donanım tamponu | PLANNED | xrun sayacı |
| **Toplam** | şartname gecikme hedefi | ⚠️ VERIFICATION REQUIRED (ölçüm yok) | — |

1. **Bütçe kuralı:** toplam gecikme blok/bütünleşik hedefi aşarsa DEGRADED bayrağı.
2. **Dürüstlük:** bu tablodaki "aşama" satırları **mantıksal dağılımdır**; sayısal ölçüm
   **yapılmamıştır** (0 test çalıştırıldı).

## §J Durum Makinesi Geçiş Tablosu (tam)

| # | Kaynak | Olay | Hedef | Eylem |
|---|---|---|---|---|
| 1 | IDLE | session_start | OPENING | create+open sırası |
| 2 | OPENING | lock+clock tamam | PRIMED | Havuz doğrula |
| 3 | OPENING | lock/clock/hata | IDLE | Geri al + hata kodu |
| 4 | PRIMED | ilk callback | RUNNING | Zincir etkin |
| 5 | PRIMED | timeout | IDLE | Temiz kapanış |
| 6 | RUNNING | overrun/saat kaybı | DEGRADED | Sıfırlı çıkış |
| 7 | RUNNING | protect set | PROTECT | Koruma çağır |
| 8 | RUNNING | session_stop | CLOSING | Kontrollü durdur |
| 9 | DEGRADED | kullanıcı onayı | RUNNING | Sıfırla + yeniden başlat |
| 10 | DEGRADED | session_stop | CLOSING | Temiz kapanış |
| 11 | PROTECT | eşik düşüşü | IDLE (önce CLOSING) | Kontrollü kapanış |
| 12 | PROTECT | protect set | PROTECT | İkinci koruma olayı say |
| 13 | UPDATING | doğrulama tamam | IDLE | Otomatik başlatma YOK |
| 14 | CLOSING | close tamam | IDLE | Lock bırakıldı |

1. **Yasak geçiş:** `PROTECT → UPDATING` yok (O.1 kuralı) — koruma açıkken güncelleme başlamaz.
2. **Geri düşüş:** `RUNNING → DEGRADED` için otomatik `RUNNING` dönüşü **yoktur** (elle/onay).

## §K Parametre Yayıncısı (oturum dışı ↔ çalışma anı)

| Parametre | Yol | Anlık mı? | Not |
|---|---|---|---|
| Çıkış kazancı | atomik float | Evet (blok sınırı) | Kademeli olabilir (PLANNED) |
| EQ bant merkezi/gain | çift tampon yayın | Blok sınırında | Sıra değişmez |
| Sınırlayıcı eşiği | atomik float | Blok sınırında | — |
| Kanal modu (8.1) | oturum dışı | Hayır | Değişimi = yeniden başlatma |
| Blok boyutu | oturum dışı | Hayır | Değişimi = yeniden başlatma |

1. **Kural:** çalışma anında değişen tekil değerler atomik/çift-tampon ile yayılır; **kilit yok**.
2. **Yapısal değişiklik** (kanal, blok) oturum dışındadır — çalışma anında uygulanmaz.

## §M Uyum Matrisi (kural → runtime karşılığı)

| Kural | Runtime karşılığı | Denetim |
|---|---|---|
| Kural 1 (0 tahsis) | BufferPool statik; E.4 | Statik denetim (kod gelince) |
| Kural 2 (lock-free) | F.2/F.3 atomik+kuyruk | Statik denetim |
| Kural 3 (noexcept) | F.1 tüm işlemler noexcept | Derleme zamanı (kod gelince) |
| Kural 4 (constexpr) | Sabit tablolar (zincir matrisi, kanal haritası) | Statik denetim |
| Kural 5 (alignas 64) | E.4 tüm bölmler | Çalışma zamanı doğrulama (DV-RT-25) |
| Kural 7 (32-bit float) | §C.2/C.3, E.3 | Açılışta format reddi |
| Kural 8 (512/48k) | E.2 eşleşme tablosu | Açılışta doğrulama |
| Kural 9 (tek Exclusive Lock) | E.2 adım 2–3 | DV-RT-04 |
| Kural 10 (DC >0.5V röle) | E.3 aşama 5 + PROTECT | DV-RT-31 |

## §P Bilinen Boşluklar (açık sorular genişletmesi)

| # | Boşluk | Etkisi | Kapanış koşulu |
|---|---|---|---|
| 1 | Depoda 0 cpp/h | Hiçbir sözleşme kodla doğrulanamaz | İlk gerçek kaynak eklendiğinde |
| 2 | ASIO SDK/JUCE sürüm kanıtdışı | Sürüm uyumsuzluk riski | `⚠️ VERIFICATION REQUIRED` kapanışı |
| 3 | Ölçüm yapılmamış (PLANNED) | Gecikme/overrun sayıları yok | DV-RT-11/24 çalıştırıldığında |
| 4 | Gerçek DAC/amp hedefi belirsiz | Çıkış zinciri sonu doğrulanamaz | Hedef donanım netleştiğinde |
| 5 | Bit derinliği reddi mi düşüş mü belirsiz | E.2 satırı iki olasılıklı | Şartname kararı gerekiyor |

## §Q Cihaz ve Bağlantı Kapsamı (derinleştirme eki)

### Q.1 Hedef cihaz haritası (salt vault kaynağı)

| Cihaz | Rol | RTOS/çekirdek | Runtime'ın ilgisi |
|---|---|---|---|
| XMOS XU316 | DSP/ses işlemci | bare-metal | Zincir + TDM çıkışı |
| STM32 sınıfı | kontrol/güç | FreeRTOS | Görev haritası (F.2) |
| ESP32 sınıfı | bağlantı/kontrol | FreeRTOS | Görev haritası (F.2) |
| Ana bilgisayar (Windows) | oynatıcı | ASIO/WASAPI | Engine (E.2) |

1. **Kaynak:** `.ai/electronic/firmware/index.md` §9 cihaz tablosu — rol/RTOS eşlemesi buradan
   alınmıştır; yeni cihaz iddiası **eklenmemiştir**.
2. **İkili davranış:** aynı runtime sözleşmesi iki ailede farklı taşıyıcıda uygulanır
   (bare-metal örtü vs FreeRTOS örtü); sözleşme (§C) değişmez.

### Q.2 Bağlantı/arayüz envanteri (kapsam seviyesi)

| Arayüz | Yön | Runtime katkısı | Sahip dal |
|---|---|---|---|
| I2S/TDM | çıkış | Kanal/saat senkronu | runtime (DriverLayer) |
| USB ses sınıfı | çıkış (anahtar) | Cihaz listesi + lock | runtime (E.2) |
| Denetim kanalı (kontrol) | çift yönlü | Parametre yayını (§K) | runtime |
| Teşhis çıkışı | çıkış | Lock-free kuyruk (F.3) | diagnostics |
| Güç koruma girişi | giriş | PROTECT bayrağı (E.1) | power-control |
| Güncelleme bayrağı | giriş | UPDATING kapısı (J-13) | update |

### Q.3 Kapsam içi ölçek varsayımları (şartnameden, ölçülmemiş)

| Varsayılan | Değer | Kaynak durumu |
|---|---|---|
| Blok | 512 örnek | sartname — ölçüm PLANNED |
| Örnekleme | 48 kHz | sartname — ölçüm PLANNED |
| Kanal üst sınırı | 8.1 | K005 kapsamı |
| Kanal tipi | 32-bit float | ADR-017 |
| Görev üst sınırı | 32 | firmware index §5 |
| Bellek | statik (heap yasak) | firmware index §5 |

1. **Dürüstlük:** bu tablo yalnızca **mevcut vault şartnamesinin tekrarıdır**; bağımsız
   ölçüm veya doğrulanmış performans sayısı **içermez**.

## §S Çıkış ve Sonuçlar (kapsam düzeyi)

| Çıkış | Biçim | Sahibi | Kapsamdaki yeri |
|---|---|---|---|
| 8.1 kanal ses akışı | 32-bit float örnek | runtime | §E.3/E.4 |
| Durum bayrakları | atomik | runtime | §J |
| Teşhis olayları | lock-free kuyruk | runtime → diagnostics | F.3 |
| Hata kodları | sabit enum | runtime | §C.3 |
| Koruma tetikleri | bayrak/olay | runtime → power-control | §J-7 |

## §T Kapsam Dışı İddia Yasakları (dürüstlük kilidi)

1. **"Çalışıyor/geçti" iddiası yok** — 0 test çalıştırıldı; tüm durumlar `PLANNED`.
2. **Kod iddiası yok** — 2026-10-15: depoda `*.cpp` 0 · `*.h` 0.
3. **URL/sürüm/repo iddiası yok** — bu dosya yalnız yerel vault dosyalarına atıf yapar.
4. **Ölçülmüş sayı yok** — gecikme/overrun/bellek rakamları yalnız eşik (kural) seviyesindedir;
   gerçek ölçüm sonuçları `⚠️ VERIFICATION REQUIRED`.
5. **Kaynak erişilemiyorsa** dosya/üretim yok sayılır; tahminle doldurulmaz.

## §V Kapsam-İçi Sözlük (derinleştirme eki)

| Terim | Kapsamdaki anlamı |
|---|---|
| Hot path | Ses callback'inin içindeki yol; tahsis/kilit yasak bölge |
| Blok | Bir callback'te işlenen örnek sayısı (varsayılan 512) |
| Xrun | Donanım tampon taşması (eksik/fazla veri) |
| Overrun | İşleme süresinin blok süresini aşması |
| Exclusive Lock | ASIO cihazının tek uygulamaya özel devralınması (kural 9) |
| Statik tahsis | Çalışma anı değil, kurulum anında ayırma (heap yasak) |
| PROTECT | DC/termal koruma durumu; en yüksek öncelik |
| DEGRADED | Ses sürmeye devam eden hatalı durum (overrun/saat kaybı) |
| Yayın (publish) | Değeri atomik/çift-tampon ile çalışmadayken değiştirme |
| Zincir | DSP aşamalarının sabit sırası (E.3) |
| Havuz | Önceden ayrılmış, sabit kapasiteli tampon bölmleri (E.4) |
| Örtü | RTOS/bare-metal üzerindeki ortak soyutlama (RtosBridge) |

## §W Komşu Dal Eşgüdümü (kapsam düzeyi)

| Komşu dal | Eşgüdüm noktası | Kapsam açısından ne değişir |
|---|---|---|
| boot/ | Güvenli boot biter → runtime başlar | Boot sonucu bir bayrak okunur |
| update/ | UPDATING kapısı (§J-13) | Update çalışırken runtime yalnız boşaltır |
| security/ | Anahtar/imi doğrulama bayrağı | Başarısızlık → oturum reddi |
| diagnostics/ | Kuyruk (F.3) | Kural: teşhis ses yolunu bloke etmez |
| power-control/ | PROTECT (§J-7) | En yüksek öncelik, her durumdan geçer |
| testing/ | DV-RT serisi senaryoları | Runtime senaryo çalıştırır, kapı kararını testing verir |

1. **Tek sahiplik kuralı:** her eşgüdüm noktasının **tek sahibi** dal vardır; runtime bu
   tabloda yalnızca kendi satırlarını uygular.
2. **Bayrak istisnası:** eşgüdüm payload değil, yalnız bayrak/sayaç taşır (§D.1-3).

## §X Doğrulama İlişkisi (runtime-dogrulama bağlantısı)

| Kapsamdaki iddia | Karşılık gelen senaryo | Durum |
|---|---|---|
| §E.1 sıralı yaşam döngüsü | DV-RT-01/02 | PLANNED |
| §E.2 lock/uyuşmazlık reddi | DV-RT-03/04 | PLANNED |
| §E.3 zincirde pop yok | DV-RT-14 | PLANNED |
| §E.4 statik havuz/tükenme | DV-RT-23/24 | PLANNED |
| §F.2 görev öncelikleri | DV-RT-12 | PLANNED |
| §J koruma/geçiş yasakları | DV-RT-31/32 | PLANNED |
| §M yasak denetimleri | DV-RT-21/22 | PLANNED (araç yok) |

1. **İlişki yönü:** bu dosya **ne iddia eder**, doğrulama dosyası **nasıl sınanır** — ikisi
   birbirini tamamlar; hiçbiri tek başına "kanıt" sayılmaz.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
