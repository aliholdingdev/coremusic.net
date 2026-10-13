---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/aaudio/aaudio-stream-yasam-dongusu
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio/aaudio
children: []
depends_on: []
evidence:
  - https://developer.android.com/reference/android/media/AudioPlaybackCaptureConfiguration
  - https://developer.android.com/ndk/downloads
  - https://github.com/android/ndk-samples
validation:
  - "2026-10-13 · 3 resmi URL HEAD 200 · envanter kesişimi 0 · 500+ satır kontrolü yapıldı"
open_questions:
  - "Burst/capacity gerçek değerleri cihaz ölçümü olmadan yazılmaz → UNKNOWN (PLANNED)"
  - "CoreMusic Android hedefi yok (repo 0 kod) → UNKNOWN"
group: mimari
---

# AAudio Stream Yaşam Döngüsü — Build · Open · Start · Callback · Kapanma

> **Bu dosya ne değildir:** Çalışan kod veya benchmark raporu değildir. Bu dosya
> bir `AAudioStream`'in yaşam döngüsünü **sözleşme düzeyinde** tarif eder;
> sayısal değerler (burst · capacity · gecikme) ancak ölçümle yazılabilir —
> ölçüm yoksa hücre `UNKNOWN` veya `PLANNED` olarak kalır.
>
> **Durum etiketi:** ŞARTNAME PLANNED · Kod YOK (repo taraması 2026-10-13: 0 isabet).

---

## §0 ELI15 — Stream yaşam döngüsü bir cümlede

Bir AAudio akışı **kurulur → açılır → başlatılır → callback içinde veri
değiştirilir → durdurulur → kapatılır**; arada bir cihaz koparsa sistem
hata geri çağrısıyla haber verir ve uygulamanın akışı **yeniden açması**
gerekir — eski akış ölüdür, "devam ettirilemez". (Kaynak: §1 · §4)

---

## §1 Build · Open · Start

### §1.1 Üç fazın sorumluluğu

| Faz | Ne yapılır | Hangi thread | Tahsis |
|---|---|---|---|
| Build (kurma) | Yön · cihaz · format · mod · tampon tercihleri yazılır | Kontrol/UI thread | İzinli (RT değil) |
| Open (açma) | `AAudioStream` oluşturulur, sistem kaynak bağlar | Kontrol/UI thread | İzinli (RT değil) |
| Start (başlatma) | Zamanlayıcı devreye girer, callback akar | Ses zamanlayıcısı | Tahsis yasak |
| Running | Blok alışverişi | Ses callback'i | Tahsis yasak |
| Stop/Pause | Zamanlayıcı durur | Kontrol thread | RT dışı |
| Close | Kaynak serbest | Kontrol thread | RT dışı |

### §1.2 Build ayarları envanteri

| # | Ayar | Ne belirler | Yan etki |
|---|---|---|---|
| 1 | Direction | Çıkış mı giriş mi | Cihaz listesi değişir |
| 2 | DeviceId | Hangi cihaz | Cihaz kopunca geçersiz |
| 3 | Format | Örnek biçimi | Dönüşüm yükü |
| 4 | Sample rate | Örnek oranı | Cihaza uymazsa ret/fallback |
| 5 | Channel count | Kanal sayısı | Çoğu cihaz sınırlı |
| 6 | PerformanceMode | Gecikme/güç önceliği | §5 anayasası |
| 7 | SharingMode | Paylaşımlı/münhasır | Üretici izni |
| 8 | Capacity | Azami tampon | Bellek + gecikme |
| 9 | SessionId | Efekt/mikser eşleşmesi | Efekt zinciri (bu dal dışı) |
| 10 | Callback takma | Data + error callback | Callbacksiz mod da mümkün |

### §1.3 Açılış (open) sonuçları

| Sonuç | Anlam | Uygulama karşılığı |
|---|---|---|
| Başarılı | Akış açık | Sıradaki: start veya callback bekleme |
| Cihaz bulunamadı | deviceId geçersiz/kopuk | Yeniden enumerasyon |
| Format reddedildi | İstenen biçim desteklenmiyor | Fallback biçim |
| Mod reddedildi | İstenen paylaşım verilmedi | Shared'a düş |
| Örnek oranı reddedildi | Cihaz uymuyor | Cihazın oranını al |
| Ekipman yok | Yön için cihaz yok | Yön değiştir/vazgeç |
| İzin sorunu | Erişim kısıtı | Uygulama ayarı |

### §1.4 Başlatma ve ilk callback

| # | Gözlem | Beklenen | Sapma durumu |
|---|---|---|---|
| 1 | Start sonrası ilk callback | İlk blok zamanında gelir | Gecikme → zamanlama sorunu |
| 2 | İlk blok içeriği | Uygulamanın doldurduğu değer | Sessizlik/kırpılma |
| 3 | Süreklilik | Blok aralıkları stabil | Titreme → ölç |
| 4 | Direction doğrulaması | Çıkışta yazma, girişte okuma | Yön karışıklığı |
| 5 | Timestamp ilk okuma | Tutarlı zaman | Uyuşmazlık → işaretle |

---

### §1.5 Kurulum hata kataloğu

| # | Hata yüzeyi | Muhtemel kök neden | Karar |
|---|---|---|---|
| 1 | Open ret — cihaz yok | deviceId yanlış/kopuk | Yeniden enumerasyon |
| 2 | Open ret — yön yok | Cihazda giriş yok | Yön değiştir |
| 3 | Open ret — format | İstenen biçim desteklenmiyor | Fallback biçim |
| 4 | Open ret — oran | 44.1/48/96 uyuşmazlığı | Cihaz oranını benimse |
| 5 | Open ret — kanal | Kanal düzeni desteklenmiyor | Kanal düşür |
| 6 | Open ret — paylaşım | Exclusive verilmedi | Shared'a düş |
| 7 | Open ret — kapasite | İstenen buffer büyük | Kapasiteyi düşür |
| 8 | Start ret | Akış durumu uygun değil | Durum makinesini kontrol |
| 9 | Anlık ret (çalışırken) | Geçersiz durum geçişi | §1.1 sırasına dön |
| 10 | Error callback (çalışırken) | Cihaz kopması | §4.3 reconnect |
| 11 | Sessizlik (hata yok) | Callback yanlış takılı | Callback doğrulama |
| 12 | Ret — ekipman izni | Uygulama izinleri | Kullanıcı ayarı |

### §1.6 Thread ve izin matrisi

| İşlem | Kontrol thread | Ses callback | RT dışı uzak iş |
|---|---|---|---|
| Build | İzinli | Yasak | Evet |
| Open | İzinli | Yasak | Evet |
| Start/Stop | İzinli | Yasak | Gerekirse |
| Veri alışverişi | Kısıtlı | Tek yetkili | Hayır |
| Log yazma | İzinli | Yasak | Evet |
| Kapat | İzinli | Yasak (önce bırak) | Evet |

### §1.7 Yaşam döngüsü zaman çizelgesi (kavramsal)

| An | Olay | Uygulama |
|---|---|---|
| T0 | Builder ayarları | Yapılandırma |
| T1 | Open çağrısı | Kaynak bağlama |
| T2 | Açık + bekliyor | Callback takılı |
| T3 | Start | İlk callback kuyruğu |
| T4 | Kararlı akış | Ölçüm penceresi |
| T5 | Cihaz olayı | Error callback |
| T6 | Yeniden build | Eski stream kapalı |
| T7 | Yeni start | Fade-in |
| T8 | Stop | Akış durur |
| T9 | Close | Kaynak serbest |

### §1.8 Açılış öncesi kontrol listesi

| # | Kontrol | Kabul |
|---|---|---|
| K1 | Yön (input/output) netleşti | Yön yazılı |
| K2 | Cihaz kimliği taze | Enumerasyon güncel |
| K3 | Biçim/örnek oranı kararlı | Karar yazılı |
| K4 | Performans modu seçildi | §5 anayasasına uygun |
| K5 | Paylaşım tercihi + fallback hazır | §4 |
| K6 | Callback/heartbeat takma sırası | §1.1 |
| K7 | RT kuralları gözden geçirildi | §2.3 |
| K8 | Ölçüm penceresi planlandı | §6.1 |

## §2 Callback ve Format

### §2.1 Callback rolleri

| Callback | Tetikleyici | Yükü | Zorunluluk |
|---|---|---|---|
| Data callback | Sistem veri ister/verir | Blok doldurma/boşaltma | Yaygın |
| Error callback | Akış koptu/hata | Yeniden kurma kararı | Kopmaya karşı kritik |
| Callbacksiz (polling) | Uygulama ister | Manuel kuyruk yönetimi | Alternatif yol |

### §2.2 Format matrisi

| Biçim | Kullanım | Not | Kanıt |
|---|---|---|---|
| Float32 | DSP/çalma hattı | CoreMusic ADR-017 ile uyumlu (iddia) | ⚠️ cihaz ölçümü PLANNED |
| Int16 | Uyumluluk/yaygın | Eski cihazlarda güvenli | ⚠️ |
| Diğer bit derinlikleri | Sınırlı/koşullu | Builder reddedebilir | ⚠️ VERIFICATION REQUIRED |
| Örnek oranı | Cihaz tarafından belirlenir | 48 kHz hedefi sartnameden | sartname |
| Kanal | Cihaz düzenine bağlı | 8.1 taşınabilirliği sınırlı | UNKNOWN |

### §2.3 Callback gövde sözleşmesi (bağlayıcı)

| # | Kural | Gerekçe |
|---|---|---|
| C1 | Tahsis yasak (`new` · `malloc` · kap-container) | RT gecikme bütçesi |
| C2 | Kilitleme yasak (mutex · lock_guard · unique_lock) | Öncelik tersine çevirme |
| C3 | Dosya/ağ E/S yasak | Belirsiz süre |
| C4 | İşlev `noexcept` niteliğinde | İstisna ses yolunu durdurur |
| C5 | Buffer baştan allocate | Koşul-tahsisi yasak |
| C6 | En kısa sürer: < burst aralığı | Underrun önleme |
| C7 | Sayaçlar atomik | Yarış yok |
| C8 | Hata koduyla dönülür | İstisna yok |

### §2.4 Blok boyutu davranışı

| Gözlem | Ne anlama gelir | Aksiyon |
|---|---|---|
| `numFrames` her çağrıda aynı | Donanım sabit burst kullanıyor | Planlanabilir |
| `numFrames` değişiyor | Sistem yeniden bölüyor | Esnek kapasite |
| Burst < hedef buffer | Cihaz küçük istiyor | 512 örneğe ulaşmayabilir |
| Burst > hedef buffer | Cihaz büyük istiyor | Callback süresi artar |
| Blok taşması | Uygulama doldurmadı | Xrun → §5 |

---

### §2.5 Format dönüşüm sorumluluğu

| Durum | Kim dönüştürür | Uygulamaya düşen |
|---|---|---|
| Cihaz float istemiyor | Dönüşüm katmanı | Gerekirse Int16 işle |
| DSP hattı float (ADR-017) | Yok (doğal) | Doğrulama |
| Kanal düzeni farklı | Yönlendirme | Kanal haritası |
| Örnek oranı farklı | Resampler (kimi cihaz) | Kabullen/fallback |
| Bit derinliği sınırlı | Codec | Format kararı |

### §2.6 Callback dışı (polling) yolu

| # | Adım | Not |
|---|---|---|
| 1 | Akış callback'siz açılır | Yapılandırma seçimi |
| 2 | Uygulama kendi kuyruğunu okur | Zamanlama uygulamada |
| 3 | Boşluk sorgusu yapılır | Doluluk takibi |
| 4 | Uygunsa blok yazılır | Underrun riski artabilir |
| 5 | Zaman çizelgesi uygulamada | RT kuralları değişmez |

### §2.7 Callback telemetrisi (ölçüm için sayaçlar)

| Sayaç | Tip | Amaç | Erişim |
|---|---|---|---|
| Çağrı sayısı | atomik sayaç | Süreklilik | RT dışı okur |
| Toplam örnek | atomik sayaç | Throughput | RT dışı okur |
| En uzun callback | atomik maks | Tepe yük | RT dışı okur |
| Boş/geliş bağlantısı | olay sayacı | Xrun erken uyarı | RT dışı okur |
| Son hata kodu | atomik | Teşhis | Error callback |
| Kopma sayısı | olay sayacı | Bağlantı sağlığı | Error callback |

> Telemetri **sayaçlardan** ibarettir: string/fstream/lock kullanılmaz; okuma
> RT dışı thread'de, yazım atomiktir.

## §3 Buffer Sizing (Tampon Boyutlandırma)

### §3.1 Kavramlar

| Kavram | Tanım | Ölçüm |
|---|---|---|
| Burst | Tek seferde taşınan örnek sayısı | `getFramesPerBurst` benzeri sorgu |
| Capacity | Akışın tutabildiği azami örnek | Kapasite sorgusu |
| Yazılan çerçeve | Uygulamanın gönderdiği toplam | Sayaç |
| Okunan çerçeve | Cihazın tükettiği toplam | Sayaç |
| Doluluk | Aradaki fark | Taşma/eksik uyarısı |
| Gecikme | Doluluk + donanım kuyruğu | Timestamp ile |

### §3.2 Boyut karar matrisi

| Hedef | Yönelim | Sonuç | Uyarı |
|---|---|---|---|
| Düşük gecikme | Küçük buffer | Underrun riski ↑ | Ölçüm şart |
| Güç tasarrufu | Büyük buffer | Gecikme ↑ | Pil testi |
| Sartname (512) | Orta yol | Cihaza bağlı | ⚠️ doğrulanmadı |
| Çalma hattı sürüklenme | Kapasite geniş | Daha az kopma | Bellek maliyeti |
| Canlı girdi | Simetrik plan | Yön başına ayrı plan | Çift yol |

### §3.3 Boyutlandırma denetim listesi

| # | Kontrol | Kabul | Red |
|---|---|---|---|
| 1 | Burst okundu mu | Kayıt var | Ölçüm yok |
| 2 | Capacity ≥ beklenen | Yeterli | Artır/fallback |
| 3 | Callback süresi < burst aralığı | Altında | Optimize et |
| 4 | Doluluk sınırı izleniyor mu | Atomik sayaç var | Ekle |
| 5 | Gecikme hedefle karşılaştırıldı | Bütçe içinde | Revizyon talebi |
| 6 | Kopmada yeniden boyutlandırma | Plan var | §4'e başvur |

---

### §3.4 Senaryo bazlı tampon planları

| Senaryo | Hedef | Burst etkisi | Risk | Onay |
|---|---|---|---|---|
| Canlı enstrüman | En küçük gecikme | Burst'a eşit/çift | Underrun | Ölçüm |
| Müzik çalma | Akıcılık | Büyük buffer kabul | Gecikme | Ölçüm |
| Kayıt (girdi) | Süreklilik | Simetrik plan | Taşma | Ölçüm |
| Oyun sesi | Tepki | Küçük/orta | Underrun | Ölçüm |
| Arka plan akışı | Güç | Büyük buffer | Gecikme | Batarya |
| Ölçüm testi | Tekrarlanabilirlik | Sabit | Sapma | Sabitleme |

### §3.5 Sartname hizası (512 örnek · 48 kHz)

| Parametre | Sartname | AAudio gerçeği | Hizalama |
|---|---|---|---|
| Örnek oranı | 48 kHz | Cihaza bağlı | Doğrula |
| Buffer | 512 örnek | Burst/capacity sınırlı | Doğrula |
| Biçim | float32 (ADR-017) | Float32 desteklenir | Doğrula |
| Kanal | 8.1 | Cihaz düzenine bağlı | Kısıt olası |
| Karar | Sartname sabit mi, cihaza esnek mi? | — | Üst karar |

### §3.6 Hesaplama çerçevesi (kavramsal — sayısal değer ölçümle gelir)

| Büyüklik | İlişki | Not |
|---|---|---|
| Bir blok süresi | burst ÷ örnek oranı | Ölçümle |
| Bir gecikme payı | doluluk ÷ örnek oranı | Ölçümle |
| Sartname bloğu | 512 örnek ÷ 48 kHz | Hedef, garanti değil |
| Callback bütçesi | blok süresi − işlem süresi | Ölçülen fark |
| Bellek yükü | capacity × kanal × bit derinliği | Kapasite ile |
| Yeniden deneme aralığı | backoff politikası | Sabit karar |

> Formüller **kavramsaldır**; sayısal sonuç yalnız cihaz ölçümünden yazılır
> (§6.4). Tabloya uydurma sayı girilmez.

## §4 Kapanma ve Hata Geri Çağrıları

### §4.1 Kapanma sırası

| # | Adım | Not |
|---|---|---|
| 1 | Akışın kapanmak üzere olduğu bilgisi | RT dışı thread |
| 2 | Zamanlayıcıyı durdur | Callback akışı kesilir |
| 3 | Callback'leri bırak | Kapanıştan sonra çağrılmaz |
| 4 | Akışı kapat | Kaynak serbest |
| 5 | Durum makinesini sıfırla | Yeni open için hazır |

### §4.2 Hata geri çağrısı senaryoları

| Senaryo | Belirti | Karar |
|---|---|---|
| Cihaz kopması | Error callback | Yeniden open |
| Kalıcı geçersizlik | Tekrarlı ret | Kullanıcıya bildir |
| Geçici meşguliyet | Ret → bekleyip dene | Sınırlı tekrar |
| Format değişimi | Akış geçersiz | Yeniden build |
| Biçim/düzen değişimi (çıkış yönü) | Uygulama yüzeyi | Çıkış yeniden kurulur |
| Uygulama askıya alma | Pause/Close | Yaşam döngüsüne uy |

### §4.3 Yeniden bağlanma (reconnect) sözleşmesi

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Eski akış asla yeniden kullanılmaz | Geçersiz durum |
| 2 | Yeniden build ile başlar | Cihaz listesi değişmiş olabilir |
| 3 | DeviceId yeniden çözümlenir | Eski kimlik ölü |
| 4 | Sesli geçiş için kısma/fade uygulanır | Tıklama önleme |
| 5 | Yeniden deneme sınırı vardır | Sonsuz döngü yok |
| 6 | Olay log'a yazılır (RT dışı) | Teşhis |

---

### §4.4 Kapanış ve askıya alma

| Durum | Zorunlu sıra | Yaygın hata |
|---|---|---|
| Normal kapanış | Stop → callback bırak → close | Close öncesi callback bırakılmaz |
| Askıya alma (uygulama arka plan) | Durum politikasına göre stop/close | Açık stream bırakılır |
| Cihaz kopması | Error callback → close → rebuild | Eski stream ile devam |
| Uygulama çökmesi | Sistem serbest bırakır | Yeniden açılışta inat |
| Çıkış yönü değişimi | Yeniden yönlendirme akışı | Eski cihaz kimliğiyle devam |

### §4.5 Yeniden deneme politikası

| # | Kural | Değer kaynağı |
|---|---|---|
| 1 | Sınırlı deneme sayısı | Politika (sabit) |
| 2 | Geri çekilme (backoff) | Zamanlama |
| 3 | Her denemede cihaz listesi taze | Enumerasyon |
| 4 | Başarısızlıkta kullanıcıya görünür durum | UI |
| 5 | Olay kaydı (RT dışı) | Teşhis |

## §5 Xrun / Underrun Yönetimi

### §5.1 Türler

| Tür | Neden | Belirti |
|---|---|---|
| Underrun | Uygulama veri yetiştiremedi | Kesik/tıkırtı |
| Taşma (overflow) | Cihaz tüketemedi | Gecikme yükselişi |
| Zamanlama sapması | Zamanlayıcı kayması | Titreme |
| Kopma | Cihaz değişimi | Sessizlik + hata |

### §5.2 Müdahale matrisi

| # | Gözlem | Müdahale | Sonrası |
|---|---|---|---|
| 1 | Tekil underrun | Süreç/pipeline gözden geçirme | Yeniden ölç |
| 2 | Sistematik underrun | Buffer/öncelik analizi | Yapısal düzeltme |
| 3 | Yalnız yoğunlukta | CPU yarışması | Öncelik/çekirdek planı |
| 4 | Kopma sonrası xrun | Bağlantı kuralı | §4.3 |
| 5 | Sartname dışına taşma | Revizyon talebi | Belge güncellemesi |

### §5.3 Ölçüm sayaçları

| Sayaç | Ne sayar | Nereden |
|---|---|---|
| Underrun sayısı | Boş blok olayı | Uygulama/ölçüm katmanı |
| Xrun toplamı | Oturum boyu | Ölçüm penceresi |
| En kötü callback süresi | Tepe yük | Profil |
| Doluluk min/maks | Kuyruk davranışı | Sayaç |
| Kopma olayı | Cihaz değişimi | Error callback |

---

### §5.4 Xrun kök-neden ağacı

| # | Belirti | Olası kök neden | Doğrulama yolu |
|---|---|---|---|
| 1 | İlk dakikada xrun | Buffer çok küçük | Kapasite artırma testi |
| 2 | Yalnız sıkıştırılmış CPU'da | İş yükü/priority | Profil |
| 3 | Yalnız belirli cihazda | HAL/üretici farkı | Cihaz matrisi |
| 4 | Kopmadan hemen önce | Cihaz değişimi | Error callback kaydı |
| 5 | Uzun oturumda | Bellek/sayaç sızıntısı | Gözlem penceresi |
| 6 | Girdi yolunda | Yön kuyruğu | Simetrik plan testi |

### §5.5 Önleyici tasarım

| # | Önlem | Etki |
|---|---|---|
| P1 | Callback minimal tutulur | Tepe süre düşer |
| P2 | Buffer tahsisi baştan | Çalışma-anı sürprizi yok |
| P3 | Atomik sayaçla izleme | Erken uyarı |
| P4 | Yeniden bağlanma otomasyonu | Sessizlik önlenir |
| P5 | Ölçüm penceresi zorunlu | Kanıtsız iddia yok |

## §6 Ölçüm

### §6.1 Ölçüm planı (PLANNED)

| # | Adım | Çıktı | Durum |
|---|---|---|---|
| 1 | Sabit ortam (cihaz · API · mod) | Öncül kayıt | PLANNED |
| 2 | Build/open/start doğrulama | Yaşam döngüsü logu | PLANNED |
| 3 | Burst/capacity okuma | Donanım sınırları | PLANNED |
| 4 | Gecikme ölçümü (timestamp) | One-way süre | PLANNED |
| 5 | Xrun sayımı | Kararlılık | PLANNED |
| 6 | Kopma testi | Yeniden bağlanma | PLANNED |
| 7 | Sartname karşılaştırması | 512/48k uyumu | PLANNED |
| 8 | Sonuç raporu | Cihaz × API × mod | PLANNED |

### §6.2 Kabul kriterleri

| Kriter | Hedef | Kaynak |
|---|---|---|
| Callback süresi | < burst aralığı | Ölçüm |
| Xrun (kararlı pencere) | 0 | Ölçüm |
| Kopma sonrası kurtarma | Otomatik yeniden bağlanma | Test |
| Gecikme bütçesi | sartname ile karşılaştırılır | Sartname (ölçüm PLANNED) |
| Biçim | 32-bit float (ADR-017) | Doğrulama |

### §6.3 Bağlı olduğu açık sorular

| # | Soru | Etiket |
|---|---|---|
| 1 | 512 örnek/48 kHz hedefi Android cihazda tutuyor mu? | ⚠️ VERIFICATION REQUIRED |
| 2 | Hangi cihaz × API matrisi ölçülecek? | UNKNOWN |
| 3 | Round-trip ölçümü kimde (mikrofon–hoparlör)? | PLANNED |

---

### §6.4 Oturum rapor şablonu (doldurulacak)

| Alan | Değer | Durum |
|---|---|---|
| Cihaz / API | Ölüm ortamı | PLANNED |
| Mod (performance) | NONE/POWER_SAVING/LOW_LATENCY | PLANNED |
| Paylaşım | Shared/Exclusive | PLANNED |
| Burst / capacity | frame cinsinden | PLANNED |
| One-way gecikme | timestamp tabanlı | PLANNED |
| Xrun (pencere) | adet | PLANNED |
| Kopma kurtarma | otomatik/el ile | PLANNED |
| Sartname farkı | 512/48k ile karşılaştırma | PLANNED |
| Karar | kabul / revizyon talebi | PLANNED |

### §6.5 Sartname revizyon kuralı

| Durum | Aksiyon | Yetki |
|---|---|---|
| Ölçüm 512/48k'yı tutuyor | Sartname aynen kalır | Vault Steward |
| Burst sartnamenin altındaysa | Yeni hedef öner + gerekçelendir | Üst karar |
| Cihazlar arasında sapma varsa | Cihaz-özel hedef tablosu | Üst karar |
| Ölçüm yoksa | İddia yazılmaz (UNKNOWN) | Zorunlu |
| Sapma tekrarlanmıyorsa | Tekrar ölç | Ölçüm ekibi |

### §6.6 Riskler

| # | Risk | Etki | Azaltma |
|---|---|---|---|
| R1 | Ölçümsüz "düşük gecikme" iddiası | Yanlış mimari karar | §6.4 rapor zorunlu |
| R2 | Cihaz üretici farkları | Tek cihazdan genelleme | Cihaz matrisi |
| R3 | Callback'e kilit bağlanması | Xrun | §2.3 C2 denetimi |
| R4 | Yeniden bağlanmama | Kalıcı sessizlik | §4.5 politikası |
| R5 | Sartname sabitleme | Uygulanamayan hedef | §6.5 üst karar |

---

## §7 Referanslar — Web Araştırma Tablosu

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| `AudioPlaybackCaptureConfiguration` referansı erişilebilir (yakalama yüzeyi — bu dalda yalnız sınırlar için) | https://developer.android.com/reference/android/media/AudioPlaybackCaptureConfiguration | 2026-10-13 | ✅ HEAD 200 |
| NDK indirme sayfası erişilebilir (araç zinciri sürüm kaynağı) | https://developer.android.com/ndk/downloads | 2026-10-13 | ✅ HEAD 200 |
| Resmi `android/ndk-samples` deposu erişilebilir (yaşam döngüsü örnekleri referansı) | https://github.com/android/ndk-samples | 2026-10-13 | ✅ HEAD 200 |
| AAudio ana dokümantasyonu bu turda kullanılamadı (envanter kilidi) | envanter kilidi — URL yazılmadı | 2026-10-13 | ⚠️ VERIFICATION REQUIRED |
| OpenSL ES ana sayfası da envanter kilidinde; "deprecated" eşiği ikincil kaynaktan | envanter kilidi — URL yazılmadı | 2026-10-13 | ⚠️ VERIFICATION REQUIRED |
| Repo'da yaşam döngüsü kodu yok (215 dosya · 0 `aaudio` isabeti) | yerel disk taraması | 2026-10-13 | ✅ ölçüm |
| Ölçüm sonuçları bu turda üretilmedi (cihaz erişimi yok) | yerel ortam | 2026-10-13 | UNKNOWN · PLANNED |

> Okuma sırası: anayasa (§ kimlik) → bu dosya (yaşam döngüsü) → OpenSL ES geçişi
> (§ ikili yol). Üç dosya birlikte okunmadan Android ses kararı verilmez.

| Sıra | Dosya | Ne verir |
|---|---|---|
| 1 | `aaudio-anayasa.md` | Kimlik · mimari · modlar · sınır |
| 2 | `aaudio-stream-yasam-dongusu.md` (bu dosya) | Yaşam döngüsü · RT kuralları · ölçüm |
| 3 | `aaudio-ve-opensl-es-gecisi.md` | İkili yol · API seviyesi matrisi · risk |

> Not: `github.com/android/ndk-samples` deposundaki `native-audio` alt klasörü
> ikincil okumada OpenSL ES'in "deprecated" ifadesiyle anılmıştır — sürüm eşiği
> resmi sayfayla doğrulanmadan sayılmaz (§7 [W14] anayasası).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
