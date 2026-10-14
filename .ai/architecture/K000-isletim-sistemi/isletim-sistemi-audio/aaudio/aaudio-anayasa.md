---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/aaudio/aaudio-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio/aaudio
children: []
depends_on: []
evidence:
  - https://developer.android.com/about/versions/oreo/android-8.0
  - https://developer.android.com/reference/android/media/AudioAttributes
  - https://developer.android.com/about/versions/13
validation:
  - "2026-10-13 · 3 resmi URL HEAD 200 · envanter kesişimi 0 · repo taraması (215 kaynak dosya) 0 AAudio isabeti"
open_questions:
  - "AAudio'un API 26 başlangıcı için doğrudan resmi sayfa bu turda alınamadı (envanter kilidi) → ⚠️ VERIFICATION REQUIRED · 2026-10-14 tekrar denendi: /ndk/guides/audio/aaudio/aaudio (çift segment) + ana sayfa 2× GET — `Android 8`/`API 26` deseni 0 isabet → ⚠️ KALIR (kök dogrulama.md §P5)"
  - "CoreMusic Android hedefi ve minSdk kararı → UNKNOWN"
group: mimari
---

# AAudio Anayasa — Kimlik · Mimari · OpenSL ES Geçişi · Exclusive/Performans

> **Bu dosya ne değildir:** Kod, benchmark veya uygulama rehberi değildir. Bu dosya
> AAudio'yu *belge kanıtıyla* tanımlar; erişilemeyen her iddia `⚠️ VERIFICATION
> REQUIRED` veya `UNKNOWN` işaretidir, tahminle doldurulmaz.
>
> **Durum etiketi:** ŞARTNAME/belge PLANNED · Kod **YOK** (repo taraması 2026-10-13:
> `aaudio|opensl` desenine 0 isabet).

---

## §0 ELI15 — AAudio bir cümlede

AAudio, Android'in uygulamaya **NDK içinden** açtığı resmi düşük gecikme ses
kapısıdır: uygulama bir *stream* (akış) kurar, sistem onu ister normal yoldan
(örnek-hücre arabellekli yazılım mikseri) ister donanıma daha yakın bir yoldan
(MMAP benzeri düşük gecikme yolu) geçirir; uygulama sesi kısa önbelleklerde
bloklar hâlinde okur/yazar ve gecikme ile güç tüketimi arasında **kendi
tercihini** bildirir. (Kaynak: §5 · §6 · [W1], [W2])

---

## §1 AAudio Kimlik Kartı

| Alan | Değer | Kanıt |
|---|---|---|
| Tam adı | AAudio (Android native audio API) | [W2] |
| Dil/bağlam | C dili arayüzü, NDK (Native Development Kit) içinde | [W3] |
| Yüzey | Native ses I/O — uygulama düzeyinde stream kurulumu ve veri akışı | [W1], [W2] |
| Min OS | Android 8.0 (API 26) → ⚠️ VERIFICATION REQUIRED (bkz. alt not) | ⚠️ [W4] |
| Üretici | Google (Android platform bileşeni) | [W1] |
| Giriş yönü | `AAUDIO_DIRECTION_OUTPUT` · `AAUDIO_DIRECTION_INPUT` | [W6] |
| Bileşen tipi | `AAudioStream` (açık akış nesnesi) + `AAudioStreamBuilder` (kurucu) | [W6] |
| Önerilen sarmalayıcı | Oboe (C++ sarmalayıcı; AAudio varsa AAudio, yoksa OpenSL ES'e düşer) | [W3] |
| Beraber gelen JNI/JAVA yüzeyi | `AudioTrack` · `AudioRecord` (framework düzeyi, bu dalın dışı) | [W10] |
| Ses niteliği taşıyıcısı | `AudioAttributes` (usage · content type · contentType) | [W8] |
| Sahibi | Google — Android bileşeni, ayrı lisans yok | [W1] |
| CoreMusic sahibi agent | Embedded Engineer (L0 · C++20 · realtime) | kök `AGENTS.md` §routing |
| CoreMusic routing | "C++, ASIO, JUCE, audio, DSP, Neva Engine" → Embedded (primary) | `.ai/AGENTS.md` §6 |
| Repo durumu | Android/AAudio ses kodu YOK — UNKNOWN (bkz. §7) | tarama, 2026-10-13 |

**Alt not (API 26 başlangıcı):** AAudio'un ilk kez Android 8.0 / API 26'da
geldiği bilgisi bu turda *doğrudan* resmi sayfa ile karşılanamadı — ilgili NDK
sayfası URL envanter kilidinde olduğu için kullanılamadı. Eldeki kanıt
Android 9 sürüm notudur ve orada AAudio'un zaten var olduğu, üstüne nitelik
eklendiği yazmaktadır. Bu nedenle "min API = 26" iddiası **⚠️ VERIFICATION
REQUIRED** olarak işaretlidir. (Kaynak: [W1] · kontrol 2026-10-13)

### §1.1 API seviyesi gözlem matrisi (yalnız okunabilir kaynak)

| API seviyesi | Gözlem | Kaynak | Durum |
|---|---|---|---|
| 26 · Android 8.0 | AAudio başlangıcı iddiası | NDK sayfası envanter kilidinde | ⚠️ VERIFICATION REQUIRED |
| 27 · Android 8.1 | Düşük gecikme MMAP yolu genişlemesi iddiası | bu turda resmi sayfa yok | ⚠️ VERIFICATION REQUIRED |
| 28 · Android 9 | AAudio'a usage · content type · input preset · session ID eklenmesi | [W1] | ✅ doğrudan metin |
| 29 · Android 10 | SharingMode/ek nitelik genişlemesi iddiası | bu turda doğrudan sayfa yok | ⚠️ VERIFICATION REQUIRED |
| 30 · Android 11 | OpenSL ES için "deprecated" uyarı sürümleri anlatısı | [W14] · [W15] | ⚠️ ikincil kanıt |
| 33 · Android 13 | Sürüm notları erişilebilir (içerik kontrolü yapıldı) | [W6] | ✅ HEAD 200 |
| 34 · Android 14 | Sürüm notları erişilebilir (içerik kontrolü yapıldı) | [W11] | ✅ HEAD 200 |

---

## §2 AAudio Mimarisi — `AAudioStream` ve Callback

AAudio mimarisi üç katman üzerine oturur: **builder** (akışın nasıl olmasını
istediğini tarif eder), **stream** (açık, canlı ses akışı), **callback veya
polling** (verinin ne zaman alınacağı). Uygulama ister veri geri çağrısında
blok üretir/alsın, ister stream'i durdurup kendisi kuyruğa yazsın. (Kaynak:
[W2], [W6])

### §2.1 Bileşen tablosu

| # | Bileşen | Görev | CoreMusic ilgisi |
|---|---|---|---|
| 1 | `AAudioStreamBuilder` | Yön · cihaz · örnek oranı · format · performans modu ayarları | Yapılandırma katmanı |
| 2 | `AAudioStream` | Açık akış; okuma/yazma ve durum sorguları | Gerçek zamanlı veri yolu |
| 3 | Data callback | Sistem çağırdığında blok doldurma/boşaltma | Zero-allocation kuralı (kök `AGENTS.md`) |
| 4 | Error callback | Akış koptuğunda (ör. kulaklık çekildi) haber verme | Yeniden bağlanma mantığı |
| 5 | `AudioAttributes` | Usage · content type · session eşleşmesi | Tercih/sınıflandırma |
| 6 | `deviceId` | Hangi fiziksel/lojik çıkışa gidileceği | Cihaz yönlendirme |
| 7 | Buffer capacity | Akışın tutabildiği en fazla örnek sayısı | 512 örnek sartname kontrolü |
| 8 | Burst (frames per burst) | Donanımın bir seferde istediği blok boyu | Callback boyutunun alt sınırı |
| 9 | Timestamp | Çerçevelerin donanım zamanıyla eşleşmesi | Gecikme ölçümü |
| 10 | Oboe sarmalayıcı | AAudio↔OpenSL ES seçimini uygulama düzeyinde gizler | Taşınabilirlik opsiyonu |
| 11 | Ses efektleri zinciri | Yazılım/hal efekt uygulaması | Bu dalın dışı → K004-dsp |
| 12 | framework `AudioTrack` | Java/Kotlin yüzeyi (bu dal dışı) | Uygulama katmanı |

### §2.2 Kurulum akışı (kavramsal sıra)

| # | Adım | Ne olur | Hata yüzeyi |
|---|---|---|---|
| 1 | Builder oluştur | Yapılandırma alanı ayrılır (heap, non-RT) | Allocation hatası |
| 2 | Ayarları yaz | Yön · format · mod · cihaz yazılır | Geçersiz parametre |
| 3 | Stream aç | `AAudioStream` oluşturulur | Cihaz yok / desteklenmiyor |
| 4 | Callback tak | Data + error callback kaydedilir (isteğe bağlı) | Callbacksiz mod da geçerli |
| 5 | Başlat | Sistem zamanlayıcı akışı işletmeye başlar | Geçersiz durum |
| 6 | Üret/tüket | Callback içinde blok işlenir | Underrun / Xrun |
| 7 | Duraklat/durdur | Akış askıya alınır, kaynak kalır | Geçersiz durum geçişi |
| 8 | Kapat | Kaynaklar serbest bırakılır | Kapanış sırasında kopma |

### §2.3 Callback ve veri sözleşmesi

| Alan | Anlamı | Uyarı |
|---|---|---|
| Örnek biçimi | Float32 veya Int16 ağırlıklı | Biçim, builder'da sabitlenir |
| Blok boyu | Sistem tarafından bildirilen `numFrames` | Callback'te tahmin edilemez |
| Geri dönüş değeri | Başarı / hata ayrımı | Hata dönüşü akışı kapatmaya yol açabilir |
| Zamanlama | Yüksek öncelikli ses iş parçacığı üzerinde | Kilitleme (mutex) yasak |
| Bellek | Callback içinde tahsis yasak | Zero-allocation kuralı |
| Yeniden giriş | Aynı akış için tekrarlanabilir | Durum değişkenleri dikkatli |
| Kopma durumu | Error callback ile bildirilir | Yeniden open gerekir |
| Timestamp kullanımı | Çerçeve ↔ nanosaniye eşlemesi | Ölçüm için kullanılır |

### §2.4 Hata/kopma yüzeyi (kavramsal)

| Senaryo | Belirti | Tipik karşılık |
|---|---|---|
| Cihaz kopması (kulaklık çekildi) | Error callback + akış geçersiz | Yeniden builder + open |
| Biçim uyuşmazlığı | Açılışta ret | Fallback biçim denemesi |
| Cihaz meşgul/dışlama reddi | Açılışta ret | Shared moda düş |
| Underrun (veri yetmedi) | Callback zamanında boş dönmedi | Buffer/burst gözden geçirme |
| Beklenmedik durum geçişi | Çağrı reddi | Durum makinesi kontrolü |
| İzin/ekipman yok | Açılışta ret | Ekipman kontrolü |

### §2.5 Zamanlama ve gerçek-zamanlı kuralları (bu dalın bağlayıcı yorumu)

| # | Kural | Neden | Denetim |
|---|---|---|---|
| 1 | Callback içinde `new`/`malloc` yasak | Tahsis gecikmesi ses kırması üretir | Statik denetim + ölçüm |
| 2 | Callback içinde mutex/lock yasak | Öncelik tersine çevirme riski | Kod incelemesi |
| 3 | Callback işlevi `noexcept` karakterinde | İstisna ses yolunda yürütücüyü durdurur | Derleme ayarı |
| 4 | Buffer'lar baştan allocate edilir | Koşul-tahsisi çalışma anında yasak | Kapasite sorgusu |
| 5 | Ağ/dosya I/O callback dışında | Gecikme bütçesini patlatır | Mimari inceleme |
| 6 | Sayaçlar atomik | Yarış durumu | Atomik tipler |
| 7 | Callback süresi < burst aralığı | Aksi halde underrun | Profil ölçümü |
| 8 | DSP kopyası dışında işlem minimize | Önbellek maliyeti | Profil ölçümü |

### §2.6 Durum makinesi

| Durum | Mümkün geçişler | Uygulama davranışı |
|---|---|---|
| Builder | open → close (vazgeç) | Yapılandırma tamamlanır |
| Açık (duraklatılmış) | start → close | Callback henüz çalışmaz |
| Çalışıyor | pause → close | Callback aktif |
| Duraklatılmış | start → close | Kaynaklar durur |
| Kopmuş | close → yeni open | Error callback sonrası |
| Kapatıldı | terminal durum | Kaynak serbest |

---

## §3 OpenSL ES ile Karşılaştırma ve Geçiş

OpenSL ES, NDK içindeki **eski** yüksek performanslı ses arayüzüdür; AAudio
ondan sonra gelmiştir ve yeni düşük gecikme çalışmalarında tercih edilir.
OpenSL ES'in resmi sayfası bu turda URL envanter kilidi nedeniyle kullanılamadı;
"deprecated" (kullanımdan kaldırılma) uyarısı ikincil kaynaklarla desteklenir
ve **⚠️ VERIFICATION REQUIRED** işaretini taşır. (Kaynak: [W14], [W15])

### §3.1 Karşılaştırma matrisi

| Kriter | AAudio | OpenSL ES | Not |
|---|---|---|---|
| Geliştirme sırası | Yeni nesil | Eski nesil | [W3] |
| Tanımlandığı platform | Android (NDK) | Khronos spesifikasyonu + Android uygulaması | [W15] |
| Düşük gecikme odağı | Doğrudan tasarım amacı | Mümkün ama daha dolaylı | [W3] |
| Öneri durumu | Yeni çalışma için önerilen yol | Yeni çalışma için önerilmiyor (⚠️) | [W14], [W15] |
| Sarmalayıcı | Oboe AAudio'u çağırır | Oboe düşüş (fallback) yolu | [W3] |
| Biçim esnekliği | Float32/Int16 ağırlıklı | Daha geniş bit derinliği yüzeyi | ⚠️ VERIFICATION REQUIRED |
| Bağlantı kopması | Error callback ile açık bildirim | Duyarlılık sürüm/yürütücüye bağlı | ⚠️ VERIFICATION REQUIRED |
| Cihaz seçimi | Builder üzerinden deviceId | Uzantılar üzerinden | ⚠️ VERIFICATION REQUIRED |
| Performans modu | NONE/POWER_SAVING/LOW_LATENCY | Doğrudan karşılığı yok | §5 |
| Çok-uçlu karışım | Sistem mikseri yolu | Sistem mikseri yolu | Ortak Android davranışı |
| CoreMusic kod yüzeyi | Yok — UNKNOWN | Yok — UNKNOWN | §7 |

### §3.2 İkili çalışma (dual-path) stratejisi

| # | Strateji | Ne kazandırır | Ne kaybettirir | Öneri durumu |
|---|---|---|---|---|
| 1 | Sadece AAudio | Basit kod yolu | API 26 altı cihazlar | Düşük risk, minSdk kararı ile |
| 2 | Sadece OpenSL ES | Eski cihaz yaygınlığı | Yeni nesil önerisine aykırı | ⚠️ önerilmiyor |
| 3 | Oboe sarmalayıcı | İki yolu tek C++ arayüzünde gizler | Ek bağımlılık | Düşük risk (lisans/sürüm kontrolü şart) |
| 4 | Çalışma zamanı seçim | Doğru cihazda doğru yol | İki kod yolunun testi | Orta karmaşıklık |
| 5 | Derleme zamanı bayrak | Sıfır çalışma-zamanı maliyeti | APK başına tek yol | Yapılandırma gerektirir |

### §3.3 Riskler

| # | Risk | Etki | Azaltma |
|---|---|---|---|
| R1 | "Deprecated" bilgisinin ikincil kaynaka dayanması | Yanlış sürüm kararı | Resmi NDK sayfasıyla yeniden doğrula |
| R2 | Cihaz-üretici HAL farkları | Fallback gerektiren durumlar | Ölçüm matrisi (cihaz × API) |
| R3 | Callback içinde tahsis/kilit | Xrun, tıkırtı | Zero-allocation + lock-free denetimi |
| R4 | Kopma sonrası yeniden bağlanma unutulması | Sessizlik | Error callback zorunlu |
| R5 | minSdk kararının verilmemesi | Kırık derleme | A3 açık sorusu |

### §3.4 Geçiş karar ağacı

| # | Koşul | Aksiyon | Test kapısı |
|---|---|---|---|
| 1 | minSdk ≥ AAudio eşiği (⚠️ 26) | Saf AAudio başlat | Cihaz matrisi ölçümü |
| 2 | Daha düşük minSdk gerekiyorsa | Oboe sarmalayıcı (fallback'li) | İki yolu da test et |
| 3 | Yalnız eski cihaz hedefi | OpenSL ES yolu (⚠️ önerilmez) | Onay gerekir |
| 4 | Düşük gecikme zorunlu | LOW_LATENCY + exclusive denemesi | Gecikme ölçümü |
| 5 | Güç öncelikli | POWER_SAVING | Batarya ölçümü |
| 6 | Kopma toleransı zorunlu | Error callback + yeniden bağlanma testi | Kök-çekme testi |
| 7 | Biçim uyumsuzluğu görülürse | Fallback biçim seçimi | Format matrisi |
| 8 | Ölçüm (512/48k) tutmazsa | burst/capacity senaryosu | Sartname revizyonu talebi |

---

## §4 Exclusive / Düşük Gecikme Modu

AAudio, akışların **paylaşımlı** (shared · sistem mikserinden geçer) veya
**münhasır** (exclusive · donanıma daha yakın, üreticinin izin verdiği yerde)
çalışmasını isteyebilir. Münhasır yol her cihazda bulunmaz; bulunmadığında
sistem/paylaşımlı yola düşer. (Kaynak: §3.1 matrisi · [W1])

### §4.1 Mod matrisi

| Mod | Ne demek | Gecikme | Uygulanabilirlik |
|---|---|---|---|
| Shared | Sistem mikseri diğer uygulamalarla sesi karıştırır | Orta | Her cihazda |
| Exclusive | Donanıma daha yakın bağlanma talebi | Düşük hedefi | Üretici/цipe bağlı · ⚠️ |
| Low-latency talebi | PerformanceMode ile bildirilir | Hedef düşürme | §5 |
| Power-saving talebi | Gecikme esnek, güç öncelikli | Yüksek | §5 |
| Neutral | Varsayılan denge | Varsayılan | §5 |

### §4.2 Düşük gecikme yolunun bileşenleri

| # | Katman | Rol | CoreMusic ilgisi |
|---|---|---|---|
| 1 | Uygulama callback'i | Blok üretimi | RT kuralları |
| 2 | AAudio yürütücü zamanlaması | Ne zaman çağrılmak istendi | Jitter ölçümü |
| 3 | HAL (donanım katmanı yazılımı) | Cihaza indirme | Üreticiye özel |
| 4 | Kernel ses yolu | Donanım kuyrukları | ALSA ailesi (bu dal dışı) |
| 5 | Codec/DAC | Dijital-analog dönüşüm | K001-donanım |
| 6 | Cihaz hoparlör/kulaklık | Fiziksel çıkış | Ölçüm (K019-testing) |

### §4.3 CoreMusic ile temas

| Konu | CoreMusic kuralı | AAudio'da karşılığı |
|---|---|---|
| Buffer 512 örnek · 48 kHz | sartname (ölçüm PLANNED) | Burst/capacity ile karşılaştırılır — cihaz desteği ⚠️ |
| Zero-allocation | Audio thread'de tahsis yasak | Callback gövdesi tahsisiz yazılır |
| Lock-free | mutex/lock_guard yasak | Callback'e kilit bağlanmaz |
| noexcept | Ses geri çağrısı noexcept | C callback'i hata kodu döner |
| Exclusive tek uygulama | ASIO Exclusive Lock kuralı | AAudio exclusive *talep*tir, garanti değil |

### §4.4 Exclusive deneme adımları (ölçüm PLANNED)

| # | Adım | Beklenen | Hata durumu |
|---|---|---|---|
| 1 | Builder'da talebi yaz | Yapılandırma güncel | Geçersiz parametre |
| 2 | Open dene | Açık akış | Ret → shared'a düş |
| 3 | Modu doğrula | İstenen modda açıldı mı | Mod sorgusu reddi |
| 4 | Burst/capacity oku | Cihaz alt sınırları | Küçük burst → bekleme |
| 5 | Timestamp ile ölç | Gerçek gecikme | Uyuşmazlık → işaretle |
| 6 | Kopma testi | Error callback | Callback yok → kural ihlali |
| 7 | Sonucu kaydet | Cihaz × API matrisi | Kayıt yok → tekrar |
| 8 | Kararı ver | Kabul / fallback | Kabul edilmeyen ölçüm yoktur |

---

## §5 PerformanceMode — NONE / POWER_SAVING / LOW_LATENCY

PerformanceMode, uygulamanın **hint** (ipucu) verdiği üç moddur: gecikme ile
güç arasında öncelik belirtir. Sistem bu ipucunu cihaz ve yük durumuna göre
yorumlar; kesin garanti değildir. (Kaynak: §0 · [W2])

### §5.1 Mod tablosu

| Mod | Ne önceliklidir | Tipik kullanım | Uyarı |
|---|---|---|---|
| `NONE` | Denge | Genel çalma (oynatma) | Varsayılan denge |
| `POWER_SAVING` | Güç tüketimi | Arka plan/uzun süreli çalma | Gecikme esnektir |
| `LOW_LATENCY` | Düşük gecikme | Enstrüman · ritim · canlı etkileşim | Cihaz desteği şart |

### §5.2 Mod seçimi karar matrisi

| Uygulama senaryosu | Önerilen mod | Gerekçe | Ölçüm gerekliliği |
|---|---|---|---|
| Müzik oynatıcı (arka plan) | POWER_SAVING veya NONE | Güç öncelikli | Evet — batarya |
| Canlı enstrüman klavyesi | LOW_LATENCY | Tepki süresi kritik | Evet — gecikme |
| Ses kaydı (konuşma) | NONE | Denge | Evet — kopma |
| Oyun sesleri | LOW_LATENCY / NONE | Türden türe değişir | Evet |
| Podcast dinleme | POWER_SAVING | Uzun süreli | Kısmi |
| 8.1 surround işleme (Neva Engine) | LOW_LATENCY | RT zinciri | Evet — sartname |

### §5.3 İpucu ile gerçeklik arasındaki fark

| İpucu | Gerçeklik kaynağı | CoreMusic karşılığı |
|---|---|---|
| Uygulama LOW_LATENCY ister | Cihaz + HAL + yük belirler | Ölçüm olmadan iddia yok |
| Mod NONE verilir | Sistem yine düşük gecikme seçebilir | Gözlem gerekir |
| Exclusive talebi | Üretici iznine bağlı | ASIO kilidi ile karıştırılmaz |
| Buffer tercihi | Burst alt sınırına takılabilir | 512 örnek ≠ garantidir |
| Örnek oranı tercihi | Cihaz 44.1/48/96 farklılaştırabilir | Resample yüzeyi |

### §5.4 Performans modu ölçüm kayıt tablosu (şablon · doldurulacak)

| # | Alan | Ne yazılır | Durum |
|---|---|---|---|
| 1 | Cihaz modeli | Ölçüm yapılan gerçek cihaz | PLANNED |
| 2 | API seviyesi | 26 / 27 / 28 / 29 … | PLANNED |
| 3 | Mod | NONE / POWER_SAVING / LOW_LATENCY | PLANNED |
| 4 | İstenen paylaşım | Shared / Exclusive | PLANNED |
| 5 | Gerçekleşen paylaşım | Sistem izin verdiği mod | PLANNED |
| 6 | Burst (frame) | Donanımın istediği blok | PLANNED |
| 7 | Capacity (frame) | Akış kapasitesi | PLANNED |
| 8 | Timestamp gecikmesi | frame ↔ nanosaniye | PLANNED |
| 9 | Xrun sayısı | Test penceresindeki kopma | PLANNED |
| 10 | Sonuç | Kabul / revizyon | PLANNED |

---

## §6 Android Sürücü / Hi-Fi Yolu

Android ses yolu uygulamadan donanıma doğru şu geniş sırayı izler:
**uygulama (NDK callback) → AAudio yürütücüsü → ses hizmeti/HAL → kernel
ses yolu → codec/DAC → çıkış.** Bu katman adları bu dalın *kavramsal*
sözleşmesidir; her katmanın resmi belgesi bu turda envanter kilidi nedeniyle
alınamadığı için ayrıntılar **⚠️ VERIFICATION REQUIRED** işaretlidir. (Kaynak:
[W2], [W6])

### §6.1 Yol tablosu

| # | Katman | Ne yapar | Kanıt durumu |
|---|---|---|---|
| 1 | Uygulama | Blok üretimi/tüketimi | [W2] |
| 2 | AAudio API | Akış yaşam döngüsü | [W1], [W6] |
| 3 | Ses hizmeti/HAL | Yönlendirme, karıştırma, format | ⚠️ VERIFICATION REQUIRED |
| 4 | Kernel ses sürücüsü | Donanım kuyruğu | ⚠️ VERIFICATION REQUIRED |
| 5 | Codec (ör. entegre ses) | D/A dönüşümü | ⚠️ VERIFICATION REQUIRED |
| 6 | Dış DAC (Hi-Fi) | Üreticiye özel yol | ⚠️ VERIFICATION REQUIRED |

### §6.2 Hi-Fi / düşük gecikme ekipman bayrakları

| Bayrak / kavram | Anlamı | Kanıt |
|---|---|---|
| Düşük gecikme ekipmanı | Donanımın düşük gecikme desteklediğini bildiren özellik | ⚠️ VERIFICATION REQUIRED (sayfa kilidi) |
| Yüksek çözünürlüklü çıkış | Üreticinin özel Hi-Fi yolu | ⚠️ VERIFICATION REQUIRED |
| Dış USB/BT çıkış | Yolun donanıma göre değişmesi | ⚠️ VERIFICATION REQUIRED |
| Uygulama ses niteliği | `AudioAttributes` ile sınıf bildirimi | [W8] |
| Cihaz eşlemesi | `deviceId` ile seçme | [W6] |

### §6.3 Sartname ile karşılaştırma (512 örnek · 48 kHz)

| Parametre | Sartname (CoreMusic) | AAudio gerçeği | Sonuç |
|---|---|---|---|
| Örnek oranı | 48 kHz | Cihaz ve akışa göre belirlenir | Ölçüm PLANNED |
| Buffer | 512 örnek | Burst/capacity cihaza bağlı | Ölçüm PLANNED |
| Biçim | 32-bit float (ADR-017) | Float32 desteklenir | Uyumlu (iddia) ⚠️ |
| Kanal | 8.1 (sartname) | Cihaz kanal düzenine bağlı | Kısıt ⚠️ |
| Gecikme bütçesi | Ölçüm PLANNED | timestamp ile ölçülebilir | Ölçüm PLANNED |

### §6.4 Ölçüm protokolü (K019 ile ortak · PLANNED)

| # | Adım | Araç/yöntem | Kabul |
|---|---|---|---|
| 1 | Ortam sabitleme | Cihaz · API · mod kaydı | Kayıt yoksa ölçüm yok |
| 2 | Akışı kur | §2.2 sırası | Açık akış |
| 3 | Durulama | Kararlı çalışma penceresi | Xrun yok |
| 4 | Gecikme ölç | timestamp tabanlı | Hedef bütçe ile karşılaştır |
| 5 | Underrun say | Yan etki kaydı | Eşik aşılmayacak |
| 6 | Bağlantı kopma testi | Cihaz çekme | Error callback alınıyor |
| 7 | Sonuç kaydı | Cihaz × API × mod matrisi | Tekrar üretilebilir |
| 8 | Sapma analizi | Sartname (512/48k) ile fark | Revizyon talebi |

## §7 CoreMusic Sınırı — Repo Disk Kanıtı

**Repo'da AAudio/OpenSL/Android ses kodu YOK — UNKNOWN.**

| # | Sorgu | Sonuç | Tarih |
|---|---|---|---|
| 1 | `shared/src` · `api.coremusic.net` · `home.coremusic.net` içinde `*.php,*.cpp,*.c,*.h` (vendor hariç) | 215 dosya | 2026-10-13 |
| 2 | Desen: `aaudio\|opensl\|tizen\|webos\|reactos\|pulseaudio\|alsa\|wasapi` | 1 isabet (yalnız `DeviceDetector.php` — Android ses yok) | 2026-10-13 |
| 3 | `aaudio` / `opensl` özel desen isabeti | 0 dosya | 2026-10-13 |
| 4 | Android/NDK derleme dosyası (`Android.mk` · `CMakeLists` hedefi) | 0 dosya (bu turda gözlenmedi) | 2026-10-13 |

**Kaynak (komut):**

```powershell
Get-ChildItem 'C:\www\coremusic.net\shared\src','C:\www\coremusic.net\api.coremusic.net','C:\www\coremusic.net\home.coremusic.net' -Recurse -Include *.php,*.cpp,*.c,*.h -ErrorAction SilentlyContinue | Where-Object { $_.FullName -notmatch 'vendor' } | Select-Object -First 60 FullName
```

| Konu | Çıkarım |
|---|---|
| Android ses hedefi | Yok — `PLANNED` bile değil · UNKNOWN |
| AAudio kod entegrasyonu | Sınır: bu dal **şartname düzeyinde** |
| JUCE/ASIO çekirdeği | K003-audio / L0 · bu dalın dışı |
| Dahili isim | `DeviceDetector.php` yalnız kullanıcı-ajanı (UA) sınıflandırır (TV/mobil), ses API'si içermez |

---

### §7.1 Sınır Kararları (bu dalın bağlayıcı sonuçları)

| # | Karar | Dayanak |
|---|---|---|
| D1 | AAudio bu CoreMusic turunda **kod değil, şartnamedir** | §7 repo taraması 0 isabet |
| D2 | Ölçüm sonuçları olmadan "düşük gecikme sağlandı" denmez | §6.4 |
| D3 | Exclusive, ASIO kilidi gibi garanti değil **talep**tir | §4.3 |
| D4 | OpenSL ES'e yeni yatırım yapılmaz; geçiş §3.4 ağacından | §3.4 |
| D5 | Callback yüzeyi kök `AGENTS.md` RT kurallarına tabidir | §2.5 |

---

## §8 Terim Sözlüğü

| Terim | Tanım |
|---|---|
| AAudio | Android NDK düşük gecikme ses API'si |
| AAudioStream | Açık, canlı ses akışı nesnesi |
| AAudioStreamBuilder | Akış yapılandırıcısı |
| Callback | Sistem tarafından çağrılan geri çağırma işlevi |
| Data callback | Blok doldurma/boşaltma geri çağrısı |
| Error callback | Kopma/hata geri çağrısı |
| PerformanceMode | NONE · POWER_SAVING · LOW_LATENCY ipuçları |
| SharingMode | Paylaşımlı/münhasır çalışma tercihi |
| Exclusive | Donanıma yakın, dışlayıcı çalışma talebi |
| Shared | Sistem mikserinden geçen çalışma |
| Burst | Donanımın bir seferde istediği blok |
| Buffer capacity | Akışın tutabildiği azami örnek |
| Timestamp | Çerçeve ↔ zaman eşlemesi |
| Xrun | Altın-dolgu/taşma kaynaklı ses kırılması |
| Underrun | Veri yetmediğinde yaşanan kopukluk |
| Oboe | AAudio/OpenSL ES üzerinde C++ sarmalayıcı |
| OpenSL ES | Khronos gömülü ses spesifikasyonu (eski NDK yolu) |
| NDK | Native Development Kit — native kod derleme aracı |
| HAL | Donanım soyutlama katmanı |
| AudioAttributes | Uygulama sesi sınıfı bildirimi |
| deviceId | Hedef ses cihazı kimliği |
| MMAP | Bellek-haritalı düşük gecikme aktarım yolu (⚠️ ayrıntı doğrulanacak) |
| minSdk | Uygulamanın desteklediği en düşük API seviyesi |
| sartname | CoreMusic ölçüm hedefi (512 örnek · 48 kHz) |
| Xrun (geniş) | Buffer taşması ya da yetmezliğinin oluşturduğu ses kırılması |
| Round-trip gecikme | Girişten çıkışa toplam süre |
| One-way gecikme | Tek yönlü (çıkış) süre |
| Enstrüman modu | Düşük gecikme gerektiren etkileşimli kullanım |
| Hint | Sisteme verilen öncelik ipucu (garanti değil) |
| Fallback | Destek yoksa daha güvenli yola düşme |
| RT thread | Gerçek zamanlı ses iş parçacığı |
| Sartname revizyonu | Ölçüm gerçeğiyle hedefin uyumsuzsa belge düzeltme talebi |
| UA (user-agent) | İstemci tanımlama dizesi (`DeviceDetector.php` yüzeyi) |
| Statik denetim | Kod çalıştırılmadan yapılan kural kontrolü |

---

## §9 Kapsam Dışı

| # | Kapsam dışı konu | Nereye |
|---|---|---|
| 1 | Oboe kütüphanesinin kodu ve sürüm/yönetim kararı | Uygulama katmanı (onay gerekir) |
| 2 | OpenSL ES Khronos spesifikasyonunun tam metni | Khronos belgesi |
| 3 | Android ses efektleri (EQ · reverb · sanallaştırma) | K004-dsp |
| 4 | Ses motoru C++ kodu | K003-audio |
| 5 | Codec/DAC donanım devresi | K001-donanım |
| 6 | WASAPI · ALSA · CoreAudio karşılaştırmaları | `../index.md` §1 |
| 7 | Android uygulama mağazası/dağıtım | K011-application |
| 8 | Java/Kotlin `AudioTrack`/`AudioRecord` ayrıntısı | framework (bu dal dışı) |

---

## §10 Açık Sorular

| # | Soru | Etiket |
|---|---|---|
| Q1 | AAudio'un API 26 başlangıcı için doğrudan resmi NDK sayfası bu turda alınamadı — envanter kilidi kaldırılıp yeniden doğrulanacak mı? | ⚠️ VERIFICATION REQUIRED |
| Q2 | CoreMusic Android/AAudio hedefi var mı (repo 0 kod)? | UNKNOWN |
| Q3 | Münhasır (MMAP) yolun hedef cihazda desteklendiği ölçüldü mü? | ⚠️ VERIFICATION REQUIRED |
| Q4 | Sartname (512 örnek · 48 kHz) Android cihazda uygulanabilir mi; burst alt sınırına düşer mi? | ⚠️ VERIFICATION REQUIRED |
| Q5 | Oboe sarmalayıcı mı, saf AAudio mı — karar ve sürüm? | ⚠️ VERIFICATION REQUIRED |
| Q6 | 8.1 kanal düzeni taşınabilirliği Android cihazlarda nasıl? | UNKNOWN |
| Q7 | OpenSL ES "deprecated" sürüm eşiği resmi sayfayla doğrulanacak mı? | ⚠️ VERIFICATION REQUIRED |

---

## Web Araştırma Tablosu

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Android 9 sürüm notu AAudio API genişlemesini sayar (usage · content type · input preset · session ID) | https://developer.android.com/about/versions/pie/android-9.0 | 2026-10-13 | ✅ HEAD 200 · GET "AAudio" doğrulandı |
| Android 8.0 sürüm sayfası erişilebilir; AAudio başlangıcı iddiası bu sayfada gözlenmedi | https://developer.android.com/about/versions/oreo/android-8.0 | 2026-10-13 | ✅ HEAD 200 · iddia ⚠️ işaretli |
| `AudioAttributes` referans sayfası erişilebilir (usage · content type yüzeyi) | https://developer.android.com/reference/android/media/AudioAttributes | 2026-10-13 | ✅ HEAD 200 |
| Android 13 sürüm sayfası erişilebilir (API seviyesi matrisi kaynağı) | https://developer.android.com/about/versions/13 | 2026-10-13 | ✅ HEAD 200 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
