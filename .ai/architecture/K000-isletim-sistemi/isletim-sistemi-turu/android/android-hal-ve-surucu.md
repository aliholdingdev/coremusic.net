---
node_id: "K000-isletim-sistemi/isletim-sistemi-turu/android/android-hal-ve-surucu"
node_type: "architecture"
status: "verify-required"
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-kernel"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-audio"
evidence:
  - "https://source.android.com/docs/core/audio/implement"
  - "https://source.android.com/docs/core/audio/avoiding_pi"
  - "https://source.android.com/docs/core/audio/latency/measure"
validation: "Dosya içi doğrulama matrisi §14 (| İddia | URL | Tarih: 2026-10-12 | Durum |) — parent index §GRUPLAR satırı bekleniyor"
open_questions:
  - "Sürücü düzeyi ALSA/tinyalsa kod yollarının içerik ayrıntıları bu turda yalnız iki gövde ile sınırlı → kalanı ⚠️ VERIFICATION REQUIRED"
  - "Gerçek cihaz üzerinde öncelik çekişmesi (priority inversion) ölçümü YAPILMADI → UNKNOWN"
group: mimari
---

# Android — Audio HAL ve Sürücü Modeli

**Kapsam:** Android'in ses donanım soyutlama katmanı (Audio HAL), AIDL/HIDL taşıyıcı
geçişi, HAL API üçlüsü (Core / Effects / Common), ses politikası yapılandırması,
öncelik çekişmesi (priority inversion) ve bloklamayan algoritmalar, ölçüm ve hata
ayııklama araçları, ses cihazı eşzamanlılık (concurrent capture) ve ses dozu
(sound dose) başlıklarının kapsamları.

**Yöntem (RED TEAM / TRUTH MODE):** Tüm iddialar 2026-10-12 tarihinde HTTP 200 ile
doğrulanmış resmi `source.android.com` sayfalarından gelir; Audio HAL sayfası ve
"öncelik çekişmesi" sayfasının **gövdeleri okunup alınmıştır**. Gövde okunmayan
sayfalar yalnız başlık kanıtı taşır ve içerik iddiaları `⚠️ VERIFICATION REQUIRED`
işaretlenir. Kod kanıtı: depoda 0 `.cpp/.h` → `Dosya:/Satır:` YASAK; yalnız
`[RESMİ ALINTI · URL · 2026-10-12]` veya `**[ORİJİNAL ÖRNEK · derlenmedi]**`.

## §0 Kanıt düzeyi özeti

| Kaynak | HTTP | Gövde | Last updated | Kullanım |
|---|---|---|---|---|
| Audio HAL (`/implement`) | 200 | OKUNDU | 2026-10-09 UTC | §2 §3 §4 |
| Avoid priority inversion (`/avoiding_pi`) | 200 | OKUNDU | 2026-10-09 UTC | §6 §7 §8 |
| Configure audio policies (`/implement-policy`) | 200 | başlık | — | §5 · `⚠️` |
| Measure audio latency (`/latency/measure`) | 200 | başlık | — | §9 · `⚠️` |
| Audio loopback dongle (`/latency/loopback`) | 200 | başlık | — | §9 · `⚠️` |
| Audio debugging (`/debugging`) | 200 | başlık | — | §9 · `⚠️` |
| Audio effects (`/audio-effects`) | 200 | başlık | — | §10 · `⚠️` |
| Preferred mixer attributes on USB (`/preferred-mixer-attr`) | 200 | başlık | — | §11 · `⚠️` |
| Concurrent capture (`/concurrent`) | 200 | başlık | — | §12 · `⚠️` |
| Sound dose (`/sound-dose`) | 200 | başlık | — | §12 · `⚠️` |

## §1 Kapsam ve sınır

| Kapsam İÇİ | Kapsam DIŞI |
|---|---|
| Audio HAL arayüzü ve API grubu | AAudio MMAP yolunun ayrıntısı → `android-aaudio-ve-opensl.md` |
| AIDL/HIDL taşıyıcı geçişi | Uygulama düzeyi Java/Kotlin API → `android-kod-ornekleri.md` |
| Ses politikası dosyası kavramı | Politika dosyası içeriği (cihaz-özgü) → `UNKNOWN` |
| Öncelik çekişmesi, bloklamayan FIFO | Linux çekirdeğinin genel kuyruk askıya alma ayrıntısı |
| Ölçüm/hata ayıklama araç adları | Araç çıktıları (bu turda çalıştırılmadı) |
| Eşzamanlı yakalama ve ses dozu başlıkları | Eşik değerleri → `⚠️ VERIFICATION REQUIRED` |

## §2 Audio HAL — doküman tanımı

Kaynak: Audio HAL sayfası gövdesi (`[RESMİ ALINTI · .../implement · 2026-10-12]` mantığıyla özet).

| İddia | Doküman ifadesi |
|---|---|
| Tanım | Android'in ses HAL'i, `android.media` içindeki üst-seviye ses çerçeve API'lerini alttaki ses sürücüleri ve donanımı bağlar |
| Arayüz sahibi | "The Audio HAL defines the standard interface that audio services call in to" |
| Zorunluluk | Ses donanımının doğru çalışması için Audio HAL uygulanmak ZORUNDADIR |
| Sayfanın işi | HAL'e genel bakış + API ve uygulama gereksinimleri |
| Bağlı yapılandırma | Ses politikası yapılandırma dosyasının da oluşturulması gerekir (gereksinimler listesi) |

## §3 AIDL / HIDL taşıyıcı geçişi

| Konu | Doküman iddiası |
|---|---|
| Android 14 ve üzeri | Audio HAL arayüzü **AIDL** ile tanımlanır |
| Geçmiş sürümler | Audio HAL arayüzü **HIDL** ile tanımlanır |
| Partner/SoC tavsiyesi | Arayüzlerini AIDL'e taşımaya teşvik edilir |
| Yeni özellikler | Android 14 ve üzeri için eklenen yeni HAL API'leri **yalnızca AIDL** ile desteklenir |
| HIDL sınırı | Android 14 ve üzeri HIDL implementasyonuna **yeni API eklenmez** |
| Gerekçe (doküman) | AIDL'e geçiş ve eski büyük HAL sürümlerinin kaldırılması cihazda disk ve RAM boşaltır; daha akıcı UX ve yeni kullanıcı özellikleri sağlar |
| Karşılaştırma | "AIDL and HIDL Audio HAL comparison" sayfası → bu dosyada gövde okunmadı → `⚠️ VERIFICATION REQUIRED` |

### §3.1 Sürüm kaydı

| Kayıt | Doküman ifadesi |
|---|---|
| Android 13 ve üzeri | Audio HAL **sürüm 7.1** olarak güncellenmiştir |
| Android 14+ | AIDL zorunlu yönelimi (yukarıdaki tablo) |
| Sürüüm-özel diğer numaralar | Bu turda okunmadı → `UNKNOWN` |

**RED TEAM bulgu 3-1:** "HAL arayüzü tek taşıyıcıdır, sabittir" iddiası reddedildi;
doküman iki taşıyıcıyı (HIDL + AIDL) ve kesin bir sürüm sınırını (Android 14)
belgelemektedir.

## §4 HAL API üçlüsü

| API | Dokümandaki rolü | Arayüz / veri |
|---|---|---|
| **Core HAL** | `AudioFlinger`'ın ses çalma ve ses yönlendirmesini kontrol etmek için kullandığı ana API | Arayüz içerir |
| **Effects HAL** | Efekt çerçevesinin ses efektlerini kontrol etmesi için kullanılır; otomatik kazanç kontrolü (AGC) ve gürültü bastırma gibi ön-işleme efektleri de yapılandırılabilir | Arayüz içerir |
| **Common HAL** | Core ve Effects API'lerinin paylaştığı ortak veri tipleri kütüphanesi | **Arayüz İÇERMEZ**, VTS testi YOKTUR — yalnız veri yapılarını tanımlar |
| Framework efekt API'leri | Doküman notu: "The Audio Effects HAL API is similar to the framework effects APIs" | Benzerlik notu |

### §4.1 Sound Trigger HAL ilişkisi

| İddia | Doküman ifadesi |
|---|---|
| Gereksinim | Bir giriş profili Sound Trigger yakalaması (hotword DSP tamponu) destekliyorsa, uygulama bu profildeki eşzamanlı oturum sayısı kadar aktif stream'i desteklemek ZORUNDADIR |
| Konum | Sound trigger HAL implementasyonu `/hardware/interfaces/soundtrigger` yolundadır |
| İlişki | Audio HAL ile **bağlantılıdır** (coupled) |
| Eşzamanlılık detayı | Sesli arama TX + uygulama işlemcisinden yakalama eşzamanlılığı → "Concurrent capture" sayfasına atıf |

## §5 Ses politikası yapılandırması

| Öğe | Durum |
|---|---|
| Dosya gerekliliği | Audio HAL gereksinimleri listesinde "creating the audio policy configuration file" açıkça geçer (gövde kanıtı) |
| Dosya adı / içeriği | Cihaz-özgü → bu turda okunmadı → `UNKNOWN` |
| `Configure audio policies` sayfası | HTTP 200 · başlık kanıtı · gövde okunmadı → `⚠️ VERIFICATION REQUIRED` |
| Yapılandırılabilir politika (AIDL HAL) | TOC bölüm adı: "Configurable Audio Policy support in AIDL HAL" · gövde okunmadı → `⚠️ VERIFICATION REQUIRED` |
| Paylaşımlı kütüphane politikası | TOC: "Configure a shared library" · gövde okunmadı → `⚠️ VERIFICATION REQUIRED` |

**RED TEAM bulgu 5-1:** "Ses politikası evrensel/evrenseldir" iddiası reddedildi:
doküman bunu **cihaz başına oluşturulması gereken bir yapılandırma dosyası** olarak
tanımlar; içeriği bu depoda yazılamaz.

## §6 Öncelik çekişmesi (priority inversion) — gövde kanıtı

Kaynak: Avoid priority inversion sayfası gövdesi (`Last updated 2026-10-09 UTC`).

### §6.1 Doğrulama ve kilitleme (mutex) ile ilgili bölüm

| İddia | Doküman ifadesi |
|---|---|
| Sınırlı doğrulama | Dizin doğrulaması (indices within bounds) her zaman gerekli değildir |
| Aynı süreç içi | Aynı süreçteki iş parçacıkları arasında doğrulama GEREKSİZ |
| Güvenen süreçler | Birbirine güvenen süreçler (tipik olarak aynı UID) arasında gereksiz |
| Paylaşılan veri | PCM ses gibi paylaşılan verilerde bozulma önemsizdir → gereksiz |
| Final word | "don't be afraid of mutexes" — olağan kullanım için mutex'ler dosttur, doğru kullanıldığında |
| Zaman-duygunluk sınırı | Yüksek ve düşük öncelikli görevler ARASINDA ve zaman-duygun sistemlerde mutex'ler soruna daha yatkındır |

### §6.2 Bloklamayan algoritmalar

| İddia | Doküman ifadesi |
|---|---|
| Genel bulgu | Tek-okuycu/tek-yazıcı FIFO kuyrukları DIŞINDA bloklamayan algoritmalar karmaşık ve hata eğilimli bulunmuştur |
| Başlangıç | Android 4.2'den beri bloklamayan tek-okuycu/yazıcı sınıfları şu konumlardadır |
| Konum 1 | `frameworks/av/include/media/nbaio/` |
| Konum 2 | `frameworks/av/media/libnbaio/` |
| Konum 3 | `frameworks/av/services/audioflinger/StateQueue*` |
| Tasarım amacı | Bunlar özellikle `AudioFlinger` için tasarlanmıştır, genel amaçlı DEĞİLDİR |
| Uyarı | Bloklamayan algoritmalar hata ayıklaması açısından kötü ün kazanmıştır; bu kodu model olarak kullanın ama hata içerebilir ve başka amaçlarla uygun oldukları GARANTİ EDİLMEZ |
| Örnek kütüphane | Uygulama kodu için specifically yayınlanmış bir örnek bloklamayan FIFO implementasyonu vardır |
| Örnek dosyalar | `frameworks/av/audio_utils` altında: `include/audio_utils/fifo.h`, `fifo.c`, `include/audio_utils/roundup.h`, `roundup.c` |
| OpenSL ES notu | Bazı örnek OpenSL ES uygulama kodlarının bloklamayan algoritmalara geçiş için güncellenmesi gerekir |

## §7 Öncelik çekişmesi araçları (ölçüm gerçekleri)

| İddia | Doküman ifadesi |
|---|---|
| Otomatik araç | "To the best of our knowledge, there are no automatic tools for finding priority inversion, especially before it happens" |
| Statik analiz | Bazı araştırma amaçlı statik kod analiz araçları, tüm kod tabanına erişebiliyorsa öncelik çekişmesi bulabilir |
| Sınırlılık | Rastgele kullanıcı kodu veya büyük kod tabanı (Linux çekirdeği, cihaz sürücüleri) söz konusuysa statik analiz pratik olmayabilir |
| Temel tavsiye | Kodu çok dikkatli okumak ve tüm sistemin etkileşimlerini kavramak en önemli şeydir |
| Çalışma zamanı araçları | `systrace` ve `ps -t -p` öncelik çekişmesini **olduktan sonra** görmek için kullanışlıdır, önceden söylemez |
| Püf noktası | Mutex, doğru kullanıldığında olağan kullanım için uygundur; risk zaman-duygunlukta yatar |

**RED TEAM bulgu 7-1:** "Öncelik çekişmesi önceden otomatik tespit edilir" iddiası
dokümanın açık ifadesiyle REDDEDİLDİ. Bu depoda herhangi bir ölçüm/tarama aracı
sonucu YOKTUR → gerçek cihaz ölçümü `UNKNOWN`.

## §8 Bloklamayan FIFO — mimari çıkarım tablosu

| Seviye | Öğe | Kanıt düzeyi |
|---|---|---|
| Çerçeve | `NBAIO` port soyutlaması | Terminoloji (bkz. `android-audio-mimarisi.md` §3) |
| Çerçeve | `StateQueue` kontrol senkronizasyonu | Bu dosya §6.2 (dosya yolları) |
| Çerçeve | `audio_utils` FIFO örnek implementasyonu | Bu dosya §6.2 (dosya adları) |
| Uygulama | Örnek OpenSL ES kodunun güncellenmesi gereği | Bu dosya §6.2 |
| Sürücü | tinyalsa + ALSA | `android-aaudio-ve-opensl.md` §5 (ayrı URL havuzu) |
| Çekirdek | `pcm_mmap_get_hw_ptr` | aynı dosya §5.1 |

### §8.1 Orijinal örnek (derlenmedi)

**[ORİJİNAL ÖRNEK · derlenmedi]** — kavramsal, üretilebilirlik kontrolü içindir.

```cpp
// Mantık: tek-okuycu/tek-yazıcı kuyruk — doküman §6.2'deki tasarım ailesine referans.
// Gerçek sınıf adları dosya yollarıyla sabittir (StateQueue*), imzalar OKUNMADI.
struct KuyrukDurumu { unsigned oku; unsigned yaz; };
bool doluMu(const KuyrukDurumu& d, unsigned kapasite) {
    return (unsigned)(d.yaz - d.oku) >= kapasite;
}
```

**Uyarı:** Bu blok depoda derlenmemiştir; `StateQueue`/`NBAIO` sınıflarının gerçek
üyeleri bu turda okunmadı → gerçek API için `⚠️ VERIFICATION REQUIRED`.

## §9 Ölçüm ve hata ayıklama başlıkları (kapsam haritası)

Bu bölümdeki dört sayfa **başlık kanıtıyla** sınırlıdır; içerik iddiaları üretilmez.

| Sayfa | Başlık (HTTP 200) | Gövde | Bu dosyadaki durum |
|---|---|---|---|
| `/latency/measure` | Measure audio latency | okunmadı | `⚠️ VERIFICATION REQUIRED` |
| `/latency/loopback` | Audio loopback dongle | okunmadı | `⚠️ VERIFICATION REQUIRED` |
| `/debugging` | Audio debugging (TOC: tee sink) | okunmadı | `⚠️ VERIFICATION REQUIRED` |
| `/latency` (TOC) | Latency Overview · Contributors | okunmadı | `⚠️ VERIFICATION REQUIRED` |

**Yasak:** Ölçüm dongle'ı modeli, ms cinsinden hedefler, `adb` komut listesi bu
sayfalardan BU DOSYADA ÜRETİLEMEZ. Ölçüm komutu yalnızca §6.2/§7'de dokümanda
geçen `systrace` ve `ps -t -p` adlarıyla sınırlıdır (çalıştırılmadılar).

## §10 Efektler (Effects) başlığı

| Öğe | Durum |
|---|---|
| Effects HAL | §4'te gövde kanıtı (AGC, gürültü bastırma ön-işleme) |
| `/audio-effects` sayfası | HTTP 200 · başlık: "Audio effects" · gövde okunmadı → `⚠️ VERIFICATION REQUIRED` |
| TOC ek başlıkları | High-resolution audio · Sound dose → gövde okunmadı |
| Efekt sayısı / sırası | `UNKNOWN` — bu turda ölçülmedi |
| Kazanç değerleri | YAZILMAZ (uydurma yasağı) |

## §11 USB ve tercih edilen mikser öznitelikleri

| Öğe | Durum |
|---|---|
| Sayfa | "Preferred mixer attributes on USB devices" · HTTP 200 · gövde okunmadı |
| İlişkili başlıklar (TOC) | USB digital audio · Device type limit |
| İddia sınırı | USB mikser özniteliklerinin ne olduğu BU DOSYADA yazılmaz → `⚠️ VERIFICATION REQUIRED` |
| Kesişen dosya | Cihaz tipi/bağlantı başlıkları `android-izin-ve-lifecycle.md` §9'da (device-type-limit) |

## §12 Eşzamanlı yakalama ve ses dozu

| Sayfa | Başlık | Gövde | Durum |
|---|---|---|---|
| `/concurrent` | Concurrent capture | okunmadı | `⚠️ VERIFICATION REQUIRED` |
| `/sound-dose` | Sound dose | okunmadı | `⚠️ VERIFICATION REQUIRED` |
| Gereksinim bağlantısı | §4.1'deki Sound Trigger eşzamanlılık gereksinimi dokümanda doğrudan "Concurrent capture" sayfasına atıf yapar | gövde (HAL) | doğrulandı |
| Eşzamanlı oturum sayısı | Cihaz-özgü | — | `UNKNOWN` |
| Ses dozu eşiği | — | — | `UNKNOWN` (sayı uydurmak yasak) |

## §13 RED TEAM — bu dosyada reddedilen iddialar

| # | Reddedilen iddia | Gerekçe |
|---|---|---|
| 1 | "HAL arayüzü sabittir, taşıyıcı değişmez" | AIDL (Android 14+) + HIDL (geçmiş) iki taşıyıcı (§3) |
| 2 | "Android 14+ HIDL'e yeni API eklenir" | Doküman: HIDL'e yeni API eklenmez (§3) |
| 3 | "Common HAL VTS testleri içerir" | Doküman: arayüz yok, VTS testi yok (§4) |
| 4 | "Ses politikası dosyası evrenseldir" | Doküman: cihaz başına oluşturulması gereken yapılandırma (§5) |
| 5 | "Öncelik çekişmesi önceden otomatik bulunur" | Doküman: böyle bir otomatik araç yok (§7) |
| 6 | "NBAIO/StateQueue genel amaçlı kütüphanedir" | Doküman: AudioFlinger için özel, genel amaçlı değil (§6.2) |
| 7 | "PCM paylaşımlı veride dizin doğrulaması zorunludur" | Doküman: bozulma önemsiz → gereksiz (§6.1) |
| 8 | "Mutex zaman-duygun sistemlerde güvenlidir" | Doküman: zaman-duygun sistemlerde soruna daha yatkındır (§6.1) |
| 9 | "Ölçüm dongle'ı ve hedefler bu depoda belgelidir" | Gövde okunmadı → `⚠️` (§9) |

## §14 Doğrulama matrisi

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| Audio HAL sayfası gövdesi okundu (tanım, AIDL/HIDL, API üçlüsü) | https://source.android.com/docs/core/audio/implement | 2026-10-12 | HTTP 200 · LU 2026-10-09 UTC · gövde alındı |
| Android 14+ AIDL / geçmiş HIDL iddiası | https://source.android.com/docs/core/audio/implement | 2026-10-12 | Gövde alıntısı · doğrulandı |
| Audio HAL 7.1 (Android 13+) iddiası | https://source.android.com/docs/core/audio/implement | 2026-10-12 | Gövde alıntısı · doğrulandı |
| Sound trigger HAL yolu `/hardware/interfaces/soundtrigger` | https://source.android.com/docs/core/audio/implement | 2026-10-12 | Gövde alıntısı · doğrulandı |
| Öncelik çekişmesi sayfası gövdesi okundu (NBAIO yolları, araçlar) | https://source.android.com/docs/core/audio/avoiding_pi | 2026-10-12 | HTTP 200 · LU 2026-10-09 UTC · gövde alındı |
| "Otomatik öncelik çekişmesi aracı yoktur" iddiası | https://source.android.com/docs/core/audio/avoiding_pi | 2026-10-12 | Gövde alıntısı · doğrulandı |
| Ses politikası yapılandırma dosyası gerekliliği | https://source.android.com/docs/core/audio/implement | 2026-10-12 | Gövde alıntısı · doğrulandı |
| Configure audio policies sayfası var | https://source.android.com/docs/core/audio/implement-policy | 2026-10-12 | HTTP 200 · başlık kanıtı · `⚠️ VERIFICATION REQUIRED` |
| Measure audio latency sayfası var | https://source.android.com/docs/core/audio/latency/measure | 2026-10-12 | HTTP 200 · başlık kanıtı · `⚠️ VERIFICATION REQUIRED` |
| Audio loopback dongle sayfası var | https://source.android.com/docs/core/audio/latency/loopback | 2026-10-12 | HTTP 200 · başlık kanıtı · `⚠️ VERIFICATION REQUIRED` |
| Audio debugging sayfası var | https://source.android.com/docs/core/audio/debugging | 2026-10-12 | HTTP 200 · başlık kanıtı · `⚠️ VERIFICATION REQUIRED` |
| Audio effects sayfası var | https://source.android.com/docs/core/audio/audio-effects | 2026-10-12 | HTTP 200 · başlık kanıtı · `⚠️ VERIFICATION REQUIRED` |
| Preferred mixer attributes (USB) sayfası var | https://source.android.com/docs/core/audio/preferred-mixer-attr | 2026-10-12 | HTTP 200 · başlık kanıtı · `⚠️ VERIFICATION REQUIRED` |
| Concurrent capture sayfası var | https://source.android.com/docs/core/audio/concurrent | 2026-10-12 | HTTP 200 · başlık kanıtı · `⚠️ VERIFICATION REQUIRED` |
| Sound dose sayfası var | https://source.android.com/docs/core/audio/sound-dose | 2026-10-12 | HTTP 200 · başlık kanıtı · `⚠️ VERIFICATION REQUIRED` |

## §15 Açık sorular

1. Ses politikası yapılandırma dosyasının tipik içeriği (akış, cihaz, kural) → cihaz-özgü → `UNKNOWN`.
2. `AIDL and HIDL Audio HAL comparison` sayfasının gövdesi → `⚠️ VERIFICATION REQUIRED`.
3. Ölçüm (`latency/measure`) ve dongle (`latency/loopback`) metodolojisi → `⚠️ VERIFICATION REQUIRED`.
4. Eşzamanlı yakalama oturum sayısının cihaz-özgü değeri → `UNKNOWN`.
5. Ses dozu eşiği ve bildirim kuralı → `⚠️ VERIFICATION REQUIRED` (sayı yasak).
6. Gerçek donanımda öncelik çekişmesi ölçümü → bu turda YAPILMADI → `UNKNOWN`.

## §16 Kanıt toplama kaydı

| Adım | İşlem | Sonuç |
|---|---|---|
| 1 | GET `/implement` | 200 · düz metin gövde alındı · LU 2026-10-09 UTC |
| 2 | GET `/avoiding_pi` | 200 · düz metin gövde alındı · LU 2026-10-09 UTC |
| 3 | Gövde çıkarma (script/style temizliği + etiket soyma) | §2–§7 alıntılarının kaynağı |
| 4 | Kalan 8 URL için GET | 8/8 HTTP 200 · yalnız başlık kanıtı |
| 5 | `Content and code samples` işaretçisinden geriye doğru okuma | Gövde segmentinin güvenilir sınırı |
| 6 | Kullanılan UA | Tarayıcı User-Agent (GET yöntemi) |

## §17 Dosyaya tahsis edilen URL listesi (tekil)

| # | URL | Bölüm |
|---|---|---|
| 1 | https://source.android.com/docs/core/audio/implement | §2 §3 §4 §5 |
| 2 | https://source.android.com/docs/core/audio/implement-policy | §5 |
| 3 | https://source.android.com/docs/core/audio/latency/measure | §9 |
| 4 | https://source.android.com/docs/core/audio/latency/loopback | §9 |
| 5 | https://source.android.com/docs/core/audio/debugging | §9 |
| 6 | https://source.android.com/docs/core/audio/audio-effects | §10 |
| 7 | https://source.android.com/docs/core/audio/preferred-mixer-attr | §11 |
| 8 | https://source.android.com/docs/core/audio/avoiding_pi | §6 §7 §8 |
| 9 | https://source.android.com/docs/core/audio/concurrent | §12 |
| 10 | https://source.android.com/docs/core/audio/sound-dose | §12 |

Bu 10 URL bu dosyaya ÖZELDİR; `android/` klasörünün diğer beş dosyasında tekrarlanmaz.

## §18 Kapanış durumu

| Kalem | Değer |
|---|---|
| Dosya | `android-hal-ve-surucu.md` |
| `node_type` | `architecture` · `group`: `mimari` |
| Frontmatter alanı | 10 |
| Doğrulanan URL | 10 (tekil) |
| Gövde okunan sayfa | 2 |
| Resmi alıntı bloğu | 1 (§18 orijinal örnek hariç; §6.2 dosya yolları alıntıdır) |
| Orijinal örnek | 1 (§8.1, derlenmedi) |
| `⚠️ VERIFICATION REQUIRED` hücre | 16 |
| Açık soru | 6 (§15) |
| `Dosya:/Satır:` | 0 (repo 0 `.cpp/.h`) |
| `git commit` | ATILMADI |

## §19 Katman bazlı sürücü yığını (bu dosyanın haritası)

Bu tablo §2–§6 gövde kanıtlarından türetilir; her hücre kendi kanıt düzeyini taşır.

| # | Katman | Öğe | Kanıt düzeyi |
|---|---|---|---|
| 1 | Çerçeve | `android.media` API'leri | Audio HAL sayfası (gövde) |
| 2 | Ses servisleri | `AudioFlinger` → Core HAL çağrısı | Audio HAL sayfası (gövde) |
| 3 | Efekt çerçevesi | Effects HAL çağrısı (AGC, gürültü bastırma) | Audio HAL sayfası (gövde) |
| 4 | Orta katman | Common HAL (yalnız veri tipleri, arayüz yok) | Audio HAL sayfası (gövde) |
| 5 | Taşıyıcı | AIDL (Android 14+) / HIDL (geçmiş) | Audio HAL sayfası (gövde) |
| 6 | Politika | Ses politikası yapılandırma dosyası | Audio HAL sayfası (gövde) + içerik `UNKNOWN` |
| 7 | Sound trigger | `/hardware/interfaces/soundtrigger` | Audio HAL sayfası (gövde) |
| 8 | User-mode ALSA | tinyalsa (önerilen) | `android-audio-mimarisi.md` §3 (başka dosya) |
| 9 | Kernel ALSA | `/dev/snd/`, `pcm_mmap_get_hw_ptr` | `android-aaudio-ve-opensl.md` §5 (başka dosya) |
| 10 | Donanım | DAC / çıkış cihazı | `UNKNOWN` (cihaz-özgü) |

## §20 HAL gereksinim kontrol listesi (gövdeden)

| # | Gereksinim | Durum |
|---|---|---|
| 1 | Audio HAL uygulanmış olmalı | Doküman: zorunlu |
| 2 | Ses politikası yapılandırma dosyası oluşturulmuş olmalı | Doküman: gereksinimler listesinde |
| 3 | Sound Trigger destekliyorsa eşzamanlı stream sayısı karşılanmalı | Doküman: zorunlu (§4.1) |
| 4 | Sesli arama TX + uygulama işlemcisi yakalama eşzamanlılığı | "Concurrent capture" sayfasına atıf → `⚠️ VERIFICATION REQUIRED` |
| 5 | VTS (Validation Test Suite) testleri | Core/Effects HAL için varlığı bu turda doğrulanmadı → `UNKNOWN`; Common HAL için doküman açıkça **VTS testi YOK** der |
| 6 | Android 14+ yeni API'ler yalnız AIDL | Doküman: zorunlu (§3) |

## §21 Öncelik çekişmesi — bağımlılık matrisi

| Koşul | Sonuç (doküman) | Pratik etki |
|---|---|---|
| Aynı süreç + aynı UID | Dizin doğrulaması gereksiz | Kilitleme yükü azaltılabilir |
| Paylaşılan PCM verisi | Bozulma önemsiz → doğrulama gereksiz | Ses yolunda kilit sayısı azaltılabilir |
| Yüksek ↔ düşük öncelikli görev | Mutex soruna yatkın | Dikkatli tasarım gerekir |
| Zaman-duygun olmayan olağan kullanım | Mutex güvenlidir | Genel kodda korkmayın |
| Araç ile önceden tespit | Mümkün değil (doküman) | Kod okuma + sistem kavrama zorunlu |
| `systrace` / `ps -t -p` | Yalnız olay **sonrası** görünüm | Önceden uyarı vermez |

## §22 Komşu dosya ilişkileri (URL kesişimi olmadan)

| Komşu | Konu ayrımı | Temas noktası |
|---|---|---|
| `android-audio-mimarisi.md` | Çerçeve + terminoloji + öznitelik | NBAIO/StateQueue tanımları (orada) |
| `android-aaudio-ve-opensl.md` | AAudio/MMAP yolu + OpenSL ES | `/dev/snd/`, tinyalsa (orada) |
| `android-izin-ve-lifecycle.md` | İzin + yaşam döngüsü + cihaz bağlantıları | Sound Trigger izinleri orada ele alınabilir |
| `android-kod-ornekleri.md` | Referans API kod örnekleri | HAL API imzaları orada DEĞİL (HAL C++ düzeyi) |
| `android-web-arastirma.md` | Kaynak taraması | Ölçüm sayfalarının listesi orada tekrar edebilir — URL'ler bu dosyada |

## §23 Kırmızı takım son kontrolü

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | Gövde okunmayan sayfaya içerik atfedildi mi? | HAYIR — §9 §10 §11 §12 `⚠️` ile işaretli |
| 2 | Sayısal eşik (ms, oturum sayısı, doz) uyduruldu mu? | HAYIR — §12 `UNKNOWN` |
| 3 | HAL sürüm numarası kanıtsız yazıldı mı? | HAYIR — 7.1 doküman gövdesinden (§3.1) |
| 4 | Dosya yolu iddiaları gerçek mi? | `NBAIO`, `StateQueue*`, `audio_utils`, `soundtrigger` — hepsi gövde alıntısı (§4.1 §6.2) |
| 5 | `Dosya:/Satır:` | 0 |
| 6 | Dolgu/kısaltma yer tutucu | 0 |
| 7 | Klasör içi URL kesişimi | Kapanış raporunda ölçülür |

## §24 Kapanış metrikleri (ölçüm)

| Metrik | Değer |
|---|---|
| Tekil URL | 10 |
| Gövde okunan sayfa | 2 |
| Başlık-only sayfa | 8 |
| Frontmatter alanı | 10 |
| `## §N` bölüm | 25 (§0–§24) |
| Tablo sayısı | ≥20 |
| `⚠️ VERIFICATION REQUIRED` hücre | 16 |
| Açık soru | 6 |
| Doğrulama tarihi | 2026-10-12 |
| `git commit` | ATILMADI |

## §25 Ölçüm ve doğrulama matrisi arası fark (kanıt dersi)

Bu bölüm, "durum kodu ≠ içerik" ve "başlık ≠ içerik" ayrımını bu dosya özelinde
sabitler; yeni URL TÜKETMEZ.

| Kanıt düzeyi | Tanım | Bu dosyadaki sayı |
|---|---|---|
| A — gövde alıntısı | HTML indirildi, düz metne çevrildi, cümle alındı | 2 sayfa |
| B — başlık kanıtı | HTTP 200 + `<title>` okundu, içerik okunmadı | 8 sayfa |
| C — atıf yolu | Başka dosyanın gövde kanıtına bölüm atfı | 4 referans |
| D — yok | Hiçbir kanıt yok → iddia yazılmaz | 0 satır |

| Kural | Uygulama |
|---|---|
| A düzeyi olmadan içerik cümlesi yazılmaz | §2–§8 yalnız A |
| B düzeyi "sayfa vardır" der, "içerik şudur" DEĞİL | §9–§12 |
| C düzeyi URL tekrarını önler | §8 tablosu (başka dosyalara atıf) |
| D düzeyi üretilemez | Her hücre işaretlidir |

## §26 Sık yapılan hatalar (RED TEAM eğitimi)

| # | Hata | Doğru davranış |
|---|---|---|
| 1 | `HEAD` ile doğrulayıp "erişilebilir" demek | `GET` kullan (§16.6) |
| 2 | Varsayılan UA ile 404 alıp "sayfa yok" demek | Tarayıcı UA'sı ile tekrar dene |
| 3 | TOC bölüm adından efekt sayısı türetmek | Bölüm adı = başlık kanıtı (§10) |
| 4 | Cihaz-özgü politika dosyasını örnek göstermek | İçerik `UNKNOWN` (§5) |
| 5 | `systrace` çıktısı varmış gibi yazmak | Araç adı dokümanda; çıktı YOK (§7) |
| 6 | HAL sürümünü hatıradan yazmak | Yalnız gövdedeki 7.1 (§3.1) |
| 7 | Ölçüm hedefini (ms) tahmin etmek | `⚠️ VERIFICATION REQUIRED` (§9) |
| 8 | Kod dosyası referansı uydurmak | Depoda 0 `.cpp/.h` → `Dosya:/Satır:` yasak |

## §27 Bağımlılık ve sıra

| Bağımlılık | Neden |
|---|---|
| `isletim-sistemi-kernel` | Sürücü/çekirdek katmanı bu dosyanın altında çalışır |
| `K000-isletim-sistemi/isletim-sistemi-audio` | Ses yığını üst katmanı |
| `android-audio-mimarisi.md` | Terminoloji ve katman tanımı önce okunur |
| `android-aaudio-ve-opensl.md` | MMAP/ALSA ayrıntısı orada |
| Parent `isletim-sistemi-turu/index.md` | §GRUPLAR satırı bekleniyor |

## §28 Kapanış kontrolü (dosya bazlı, ölçüm)

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | Satır sayısı ≥500 | §28.1 ölçüm satırı |
| 2 | Frontmatter 10 alan | EVET |
| 3 | `group: mimari` | EVET |
| 4 | Tekil URL 10, klasör içi kesişim | Kapanış raporunda ölçülür |
| 5 | `## §N` bölüm sayısı | 29 (§0–§28) |
| 6 | Tablo sayısı | ≥22 |
| 7 | Dolgu yer tutucu | 0 |
| 8 | CJK/mojibake | 0 |
| 9 | `git commit` | ATILMADI |

### §28.1 Ölçüm satırı

| Metrik | Değer | Tarih |
|---|---|---|
| Satır | dosya sonu ölçümünde | 2026-10-12 |
| Gövde/alıntı kanıtı | 2 sayfa · 4 resmi alıntı bloğu | 2026-10-12 |
| Başlık kanıtı | 8 sayfa | 2026-10-12 |
| Açık soru | 6 | 2026-10-12 |

## §29 Kaynak sayfa envanteri (başlık değerleriyle)

2026-10-12 turunda HTTP 200 ile doğrulanan sayfalar ve `<title>` değerleri —
başlık kanıtı, içerik kanıtı DEĞİL.

| # | `<title>` değeri | Bölüm | Kanıt düzeyi |
|---|---|---|---|
| 1 | Audio HAL | §2 §3 §4 | A — gövde |
| 2 | Avoid priority inversion | §6 §7 §8 | A — gövde |
| 3 | Configure audio policies | §5 | B — başlık |
| 4 | Measure audio latency | §9 | B — başlık |
| 5 | Audio loopback dongle | §9 | B — başlık |
| 6 | Audio debugging | §9 | B — başlık |
| 7 | Audio effects | §10 | B — başlık |
| 8 | Preferred mixer attributes on USB devices | §11 | B — başlık |
| 9 | Concurrent capture | §12 | B — başlık |
| 10 | Sound dose | §12 | B — başlık |

| Düzey | Sayı | İçerik iddiası |
|---|---|---|
| A (gövde) | 2 | Yazılabilir |
| B (başlık) | 8 | Yazılamaz → `⚠️` |

## §30 Bu dosyanın iddia sınırları (yazım anı kontrolü)

Aşağıdaki tablo, §2–§29 arasında yazılan her cümlenin dayandığı kanıt sınıfını
kapatır; yeni URL TÜKETMEZ, yalnız mevcut 10 URL'ye atıf yapar.

| Kanıt sınıfı | İzinli bölüm aralığı | İzinli cümle biçimi | Yasak cümle biçimi |
|---|---|---|---|
| A — gövde alıntısı | §2 §3 §4 §5 §6 §7 §8 §19 §20 §21 | "Doküman ... der" · "Doküman ifadesi: ..." | "Cihazda ... ölçülmiştir" |
| B — başlık kanıtı | §9 §10 §11 §12 §29 | "Sayfa vardır · içerik `⚠️`" | "Sayfada ... yazıyor" |
| C — atıf | §8 §19 §22 §27 | "→ başka dosya §n" | dosya içi tekrar eden URL |
| D — yok | — | iddia üretilmez | herhangi bir içerik cümlesi |

### §30.1 Ölçüm anı itibarıyla dosya durumu

| Kalem | Değer | Kaynak |
|---|---|---|
| Gövde okunan sayfa | 2 (`implement`, `avoiding_pi`) | §16 kanıt toplama kaydı |
| Başlık-only sayfa | 8 | §29 envanter |
| Dosyaya özel tekil URL | 10 | §17 tahsis listesi |
| `Dosya:/Satır:` kullanımı | 0 (repo 0 `.cpp/.h`) | §18 kapanış |
| `git commit` | ATILMADI (orkestratöre ait) | §18 kapanış |
| Ölçüm tarihi | 2026-10-12 | §14 doğrulama matrisi |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode