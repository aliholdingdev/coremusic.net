---
node_id: K012-services/background-jobs
node_type: module
role: surucu
status: draft
parent_id: K012-services/background-jobs
children: []
depends_on:
  - "background-jobs/background-jobs-anayasa.md"
  - "background-jobs/index.md"
  - "services-master.md"
evidence:
  - "S1 — shared/src/Events/EventDispatcher.php 120 satır (dispatch: 28, addListener: 50)"
  - "S1 — shared/src/Api/Registry/ServiceHealth.php 101 satır (checkAll: 26)"
  - "S1 — shared/src/Events/Domain/ 9 + Integration/ 3 olay dosyası"
  - "S5 — Cron/Queue/Job/Worker/Schedule/Task/Timer/Async => 0 isabet (319 PHP dosyası)"
validation:
  - "§3 tetik tablosunda motor satırı YOK — motor adı yazılmadı"
  - "§2 yalnızca mevcut (S1) ve yok (S5) satırları içeriyor"
open_questions:
  - "Q5 — iş motoru bilinmiyor → ⚠️ VERIFICATION REQUIRED"
  - "dispatch gövdesi (senkron/asenkron) okunmadı → ⚠️ VERIFICATION REQUIRED"
date: 2026-10-13
updated: 2026-10-13
version: 1.0.0
authority: "SSOT: EventDispatcher.php (S1) + 0-isabet yokluk kaydı (S5)"
group: mimari
---

# background-jobs — Sürücü (Driver)

> **Özel durum:** bu alt alanın kendi sürücüsü (kuyruk/timer/worker) **kodda
> yoktur**. Bu dosya iki şeyi yapar: (1) mevcut tek sürücüyü — olay yayımını —
> adım adım tarif eder, (2) olmayan sürücüler için **boş satırı uydurmaz**,
// yokluğu kaydeder.

## §1 Sürücü Tanımı

| Alan | Değer | Kanıt |
|---|---|---|
| Mevcut sürücü | olay yayımı (`EventDispatcher::dispatch`) | S1:28 |
| Girdi | `object $event` | S1:28 |
| Çıktı | `object` ( aynı olay ) | S1:28 |
| Dinleyici kaydı | `addListener(eventName, listener, priority)` | S1:50 |
| Sıra | `sortListeners()` ile öncelik | S1:109 |
| Kuyruk sürücüsü | YOK | S5 |
| Zamanlanmış sürücü | YOK | S5 |
| Worker sürücüsü | YOK | S5 |
| Motor adı | YAZILMAZ (bilinmiyor) | Q5 |

## §2 Mevcut Sürücü Zinciri

| Sıra | Adım | Metot | Satır | Durum |
|---|---|---|---|---|
| 1 | dinleyici kaydı | `addListener` | 50 | ✅ S1 |
| 2 | dinleyici var mı? | `hasListeners` | 101 | ✅ S1 |
| 3 | dinleyicileri al | `getListeners` | 82 | ✅ S1 |
| 4 | sırala | `sortListeners` | 109 | ✅ S1 |
| 5 | yay | `dispatch` | 28 | ✅ S1 |
| 6 | kaldır | `removeListener` | 64 | ✅ S1 |
| 7 | çağıran kod (kim, ne zaman) | — | — | ⚠️ okunmadı |

## §3 Tetikleyici Tablosu (yalnızca gerçek)

| # | Tetikleyici | Kaynak | Tepki | Kanıt |
|---|---|---|---|---|
| T1 | olay nesnesi ile `dispatch()` | uygulama kodu (kim okunmadı) | dinleyiciler | S1:28 |
| T2 | dinleyici kaydı | uygulama kodu (okunmadı) | kayıt listesi | S1:50 |
| T3 | dinleyici kaldırma | uygulama kodu (okunmadı) | listeden çıkar | S1:64 |
| T4 | kuyruk işi gelmesi | — | **sürücü YOK (S5)** | S5 |
| T5 | zamanlayıcı tetiği | — | **sürücü YOK (S5)** | S5 |
| T6 | worker çağrısı | — | **sürücü YOK (S5)** | S5 |
| T7 | sağlık kontrol döngüsü | bilinmiyor | `checkAll()` | S1:26 (tetikleyici okunmadı) |

## §4 Durum Tablosu (mevcut sürücü)

| # | Durum | Çıkış | Sonraki | Kanıt |
|---|---|---|---|---|
| S1 | kayıt bekliyor | `addListener` | kayıtlı | S1:50 |
| S2 | kayıtlı | `hasListeners` | yayına hazır / yok | S1:101 |
| S3 | sıralama | `sortListeners` | sıralı liste | S1:109 |
| S4 | yayım | `dispatch` | dinleyiciler çağrılır | S1:28 |
| S5 | kaldırma | `removeListener` | kayıt silinir | S1:64 |

## §5 Olmayan Sürücüler (boş satır — uydurulmaz)

| # | Sürücü | Arama | Sonuç | Davranış |
|---|---|---|---|---|
| 1 | kuyruk sürücüsü | `**/*Queue*.php` | 0 | tanımsız |
| 2 | zamanlayıcı | `**/*Cron*.php`, `**/*Timer*.php` | 0 | tanımsız |
| 3 | worker | `**/*Worker*.php` | 0 | tanımsız |
| 4 | iş nesnesi | `**/*Job*.php`, `**/*Task*.php` | 0 | tanımsız |
| 5 | asenkron çalıştırma | `**/*Async*.php` | 0 | tanımsız |
| 6 | planlama | `**/*Schedule*.php` | 0 | tanımsız |

**Kural:** bu altı satır "ileride olacak" diye doldurulmaz; olduğu gibi boş
bırakılır ve Q5'e bağlanır.

## §6 Sürücü Bağımlılık Grafiği

```text
uygulama kodu (çağıran taraf ⚠️ okunmadı)
   └─> EventDispatcher::dispatch(object $event)
          ├─> hasListeners()     (101)
          ├─> getListeners()     (82)
          ├─> sortListeners()    (109)
          └─> dinleyici çağrıları (senkron — iddia ⚠️)
                 └─> olası sonuç yazımı → K007 (iddia, S2 §21.9)

kuyruk/timer/worker yolu → YOK (S5) → Q5 kapısı
```

## §7 Zamanlama

| Olay | Tetikleyici | Sıklık | Kanıt |
|---|---|---|---|
| `dispatch()` | çağrı | çağrı başına | S1:28 |
| `checkAll()` sağlık | bilinmiyor | bilinmiyor | S1:26 · tetikleyici ⚠️ |
| kuyruk tüketimi | — | — | S5 (yok) |
| zamanlanmış iş | — | — | S5 (yok) |

## §8 Geri Besleme

| # | Geri besleme | Nereye | Kanıt |
|---|---|---|---|
| F1 | dinleyici yok | yayım yapılmaz (davranış `UNKNOWN`) | S1:101 |
| F2 | dinleyici istisnası | `UNKNOWN` | dispatch gövdesi okunmadı |
| F3 | olay işi başarısız | `UNKNOWN` | iş yok (S5) |
| F4 | K007 yazım hatası | `UNKNOWN` | yazım kodu yok |

## §9 Hata Sürücüleri

| # | Kaynak | Tepki | Kanıt |
|---|---|---|---|
| H1 | dinleyici hatası | davranış `UNKNOWN` | ⚠️ |
| H2 | olay kayıt hatası | `UNKNOWN` | ⚠️ |
| H3 | sağlık kontrolü başarısız | `getUnhealthyServices()` | S1:87 |
| H4 | sağlık toplam durumu | `getAggregatedStatus()` | S1:34 |
| H5 | kuyruk kaybı | — | kuyruk yok (S5) |

## §10 Sürücü Metrikleri

| Metrik | Değer | Kanıt |
|---|---|---|
| EventDispatcher satır uzunluğu | 120 | S1 |
| EventDispatcher metot sayısı | 7 (satır 20–109) | S1 |
| Statik olmayan/özel ayrımı | 1 özel (`sortListeners`) | S1:109 |
| IntegrationEventInterface satır | 47 | S1 |
| EventDispatcherTest dosyası | mevcut | S1 |
| Kuyruk/cron/worker dosyası | 0 | S5 |
| Repo toplam PHP dosyası | 319 | S1 ölçüm |

## §11 Web Araştırması — İddia Kaydı

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| 12 faktörlük uygulama, süreçleri bağımsız olarak çalıştırır; arka plan işini ayrı süreç kabul eder | https://12factor.net/ | HTTP 200 | ✅ — tasarım dayanağı; uygulama kanıtlanmadı |
| CNCF proje ekosisteminde mesajlaşma/kuyruk projeleri bulunur | https://www.cncf.io/ | HTTP 200 | ✅ — yalnız bağlam; **seçim yapılmadı** |
| SMTP, e-posta gönderimi için protokoldür | https://www.rfc-editor.org/rfc/rfc5321.html | HTTP 200 | ✅ — olası bildirim kanalı (iddia) |

## §12 Açık Sorular

| # | Soru | Kapı |
|---|---|---|
| Q5 | iş motoru ne? | anayasa §9 |
| Q-bj3 | `dispatch` senkron mu? | ⚠️ |
| Q-bj4 | olay kuyruğa giriyor mu? | ⚠️ |
| Q-bj5 | retry politikası var mı? | UNKNOWN |
| Q-bj6 | `checkAll()` tetikleyicisi ne? | ⚠️ |

## §13 Red Team Bulgu Listesi

| # | Bulgu | Severity |
|---|---|---|
| 1 | Sürücü tarafı tamamen boş (6 desen, 0 sonuç) | HIGH |
| 2 | `dispatch` gövdesi okunmadı — senkron mu bilinmiyor | HIGH |
| 3 | Çağrıran kod okunmadı (olay nereden çıkıyor?) | MEDIUM |
| 4 | K007 yazım hedefi belirsiz | MEDIUM |
| 5 | Sağlık döngüsü tetikleyicisi bilinmiyor | LOW |

## §14 Sürücü Sınır Devirleri

| Durum | Devir | Gerekçe |
|---|---|---|
| Zamanlanmış iş gerekirse | ADR + motor seçimi | Q5 |
| Dış servise iş gönderilirse | K013 | dış protokol |
| Ağ üzerinden iş | K014 | taşıma |
| Sonuç yazımı | K007 | veri sahipliği |
| Ortam/cron kurulumu | K018 | dağıtım |
| Test altyapısı | K019 | test |

## §15 Sürücü Test Senaryoları

| # | Senaryo | Beklenen | Durum |
|---|---|---|---|
| 1 | dinleyici yokken dispatch | davranış `UNKNOWN` | ⚠️ test okunmadı |
| 2 | öncelik sırası | sıralı çağrı | ⚠️ test okunmadı |
| 3 | removeListener sonrası dispatch | çağrı yok | ⚠️ test okunmadı |
| 4 | kuyruk işi | — | kuyruk yok (S5) |
| 5 | zamanlanmış tetik | — | zamanlayıcı yok (S5) |

**Not:** `shared/tests/Events/EventDispatcherTest.php` **varlığı** ölçülmüştür
(S1); içeriği okunmamıştır → ⚠️ VERIFICATION REQUIRED.

## §16 Sürücü Riskleri

| # | Risk | Etki | Karşı önlem |
|---|---|---|---|
| 1 | senkron dinleyici uzun sürerse istek yavaşlar | yüksek | ölçüm PLANNED |
| 2 | olay yayımı "kuyruk" sanılır | yüksek | anayasa B3 |
| 3 | motor adı uydurulur | çok yüksek | Q5 kapısı |
| 4 | başarısız iş sessizce kaybolur | yüksek | retry `UNKNOWN` |
| 5 | olay payload'unda hassas veri | orta | anayasa G1 |

## §17 Sürücü Sembol Dizinı

| # | Sembol | Dosya | Satır |
|---|---|---|---|
| 1 | `EventDispatcher` | shared/src/Events/EventDispatcher.php | 20 |
| 2 | `dispatch()` | Aynı | 28 |
| 3 | `addListener()` | Aynı | 50 |
| 4 | `removeListener()` | Aynı | 64 |
| 5 | `getListeners()` | Aynı | 82 |
| 6 | `hasListeners()` | Aynı | 101 |
| 7 | `sortListeners()` | Aynı | 109 |
| 8 | `IntegrationEventInterface` | shared/src/Contracts/Events/ | 17 |
| 9 | `ServiceHealth` | shared/src/Api/Registry/ServiceHealth.php | 17 |
| 10 | `checkAll()` | Aynı | 26 |
| 11 | `getAggregatedStatus()` | Aynı | 34 |
| 12 | `isServiceHealthy()` | Aynı | 76 |
| 13 | `getUnhealthyServices()` | Aynı | 87 |

## §18 Sürücü → Anayasa Eşlemesi

| Sürücü konusu | Anayasa maddesi |
|---|---|
| motor adı yok | B1 |
| kuyruk/timer 0 | B2 |
| senkron yayım | B3 |
| öncelik | B4 |
| kaldırma | B5 |
| arayüz dört metod | B6–B9 |
| K007 yazımı iddia | B10 |
| ADR gerekliliği | B11–B12 |
| hata `UNKNOWN` | B13 |

## §19 Kapsam Dışı Liste

| # | İstek | Hedef |
|---|---|---|
| 1 | kuyruk kurulumu | ADR + DevOps |
| 2 | cron yapılandırması | K018 |
| 3 | iş sonucu şeması | K007 |
| 4 | dış bildirim | K013 |
| 5 | ağ hatası yeniden deneme | K014 |
| 6 | iş testi altyapısı | K019 |

## §20 Açık Kapı Sayımı

| Kapı | Sayı |
|---|---|
| `⚠️ VERIFICATION REQUIRED` | §2 · §3 · §6 · §7 · §8 · §9 · §12 · §15 = 15 satır |
| `UNKNOWN` | §8 · §9 · §12 · §15 = 9 satır |
| Açık soru (Q) | 6 |
| Red team bulgusu | 5 |

## §21 Bağlantılar

| Dosya | İlişki |
|---|---|
| background-jobs-anayasa.md | önce okunur |
| background-jobs-ornekler.md | kod örnekleri |
| background-jobs/index.md | künye |
| services-master.md §21.9 · §22.3 | kart ve hata akışı |
| services-bagimliliklar.md §17 | bağımlılık |

## §22 Doğrulama Özeti

| Kontrol | Hedef | Durum |
|---|---|---|
| Satır sayısı | ≥500 | dış ölçüm |
| Son satır | authority birebir | birebir |
| `parent:` | 0 | 0 |
| `group` | `mimari` alıntısız | mimari |
| Mojibake / CJK | 0 | 0 |
| Motor adı yazılmadı | evet | doğrulandı |
| Kod kanıtı | ≥1 `Dosya/Satır` | §2 · §10 · §17 |

## §23 PSR-14 Arayüz Uyum Tablosu

`EventDispatcher` sınıfı `Psr\EventDispatcher\EventDispatcherInterface` arayüzünü
uygular (S1:14 import, S1:20 sınıf). Aşağıdaki tablo arayüz metodlarının bu
uygulamadaki karşılığını ve satır konumunu verir.

| # | Arayüz Metodu | Uygulama | Satır | Durum |
|---|---|---|---|---|
| 1 | `dispatch(object): object` | mevcut | 28 | ✅ S1 |
| 2 | (arayüzde yok) `addListener()` | ek | 50 | ✅ S1 — kayıt API'si |
| 3 | (arayüzde yok) `removeListener()` | ek | 64 | ✅ S1 — kaldırma |
| 4 | (arayüzde yok) `getListeners()` | ek | 82 | ✅ S1 — liste |
| 5 | (arayüzde yok) `hasListeners()` | ek | 101 | ✅ S1 — sorgu |
| 6 | (arayüzde yok) `sortListeners()` | özel | 109 | ✅ S1 — sıralama |
| 7 | `StoppableEventInterface` desteği | `dispatch` içinde | 37 | ✅ S1 |

**Not (S1):** PSR-14 yalnız `dispatch` metodunu zorunlu kılar; `addListener` /
`removeListener` / `getListeners` / `hasListeners` kayıt-servisi tarafıdır ve bu
uygulamada tek sınıfta toplanmıştır. Ayrıştırma (listener provider) **yoktur**
— kayıt doğrudan `$listeners` dizisine yazılır (S1:52). Bu bir tasarım
tercihidir; genişletilebilirlik etkisi `⚠️ VERIFICATION REQUIRED` (boyut bilinmiyor).

## §24 Öncelik Sıralama Detay Analizi

`sortListeners()` (S1:109) dinleyicileri öncelik alanına göre **azalan** sıraya
dizer; `usort` karşılaştırıcısı `$b['priority'] <=> $a['priority]` şeklindedir
(S1:113–115). Bu, yüksek öncelikli dinleyicinin önce çağrılması demektir.

| # | Özellik | Değer | Kanıt |
|---|---|---|---|
| 1 | sıralama algoritması | `usort` | S1:113 |
| 2 | yön | azalan (`$b <=> $a`) | S1:114 |
| 3 | eşit öncelik | kararsız sıralama (usort kararsız) | ⚠️ |
| 4 | öncelik tipi | `int` (varsayılan 0) | S1:50 |
| 5| sıralama önbelleği | `$sortedListeners` | S1:23 |
| 6 | önbellek geçersizleştirme | `unset()` kayıt/kaldırmada | S1:58 · S1:76 |
| 7 | önbellek yeniden hesabı | ilk `getListeners` çağrısında | S1:88–89 |

**Eşit öncelik notu:** `usort` kararsızdır; aynı öncelik değerine sahip iki
dinleyicinin çağrı sırası **garanti edilmez** → davranış `UNKNOWN` (S1:113 okundu,
test okunmadı). Bu, deterministik olmayan yayım riski doğurur (bkz. §28-R2).

## §25 Olay Nesnesi Yaşam Döngüsü

`dispatch(object $event): object` çağrısı olay nesnesini referans olarak alır ve
geri döndürür (S1:28, S1:44); kopyalama yapılmaz. Yaşam döngüsü aşağıdaki
adımlardan ibarettir.

| # | Adım | Davranış | Satır | Kanıt |
|---|---|---|---|---|
| 1 | sınıf adı çıkarımı | `get_class($event)` | 30 | S1:30 |
| 2 | kayıt sorgusu | `isset($this->listeners[$eventName])` | 32 | S1:32 |
| 3 | dinleyici yoksa | olay olduğu gibi döner | 33–34 | S1:33 |
| 4 | döngü | `getListeners()` üzerinden | 36 | S1:36 |
| 5 | durdurma kontrolü | `isPropagationStopped()` | 37–38 | S1:37 |
| 6 | dinleyici çağrısı | `$listener($event)` | 41 | S1:41 |
| 7 | dönüş | aynı `$event` nesnesi | 44 | S1:44 |

**Kimlik notu (S1):** olay anahtarı tam sınıf adıdır (`get_class`), ayrı bir olay
adı alanı **kullanılmaz**. Buna karşılık `IntegrationEventInterface::eventName()`
ayrı bir ad döndürür (S1 interface:24) — bu iki adlandırma sistemi birbiriyle
nasıl bağlanıyor **okunmadı** → `⚠️ VERIFICATION REQUIRED`.

## §26 IntegrationEventInterface Sözleşme Analizi

`IntegrationEventInterface` (shared/src/Contracts/Events/, 47 satır) dört metod
tanımlar; her metod bir DocBlock ile belgelenmiştir. Bu arayüzü uygulayan somut
sınıflar **envanterde 3 adet** Integration event dosyası olarak sayıldı (S1:
`src/Events/Integration/`); içerikleri okunmadı.

| # | Metod | Dönüş | Amaç (DocBlock) | Satır |
|---|---|---|---|---|
| 1 | `eventName()` | `string` | olay adı | 24 |
| 2 | `source()` | `string` | köken servis adı | 31 |
| 3 | `payload()` | `array` | olay yükü | 38 |
| 4 | `occurredOn()` | `\DateTimeImmutable` | oluş zaman damgası | 45 |

**Sözleşme gözlemi (S1):** `payload()` `array` döndürür — şema **tip güvenli
değildir**; yük içindeki alanların biçimi arayüzce sabitlenmez. Hassas veri
taşınıp taşınmadığı `⚠️ VERIFICATION REQUIRED` (payload içerikleri okunmadı).
`occurredOn()` immutable zaman damgası, olayların sıralanması için gereken
zaman bilgisini sağlar; ancak sıralama **bu arayüzce zorunlu kılınmaz**.

## §27 ServiceHealth Toplama Mantığı

`ServiceHealth` (shared/src/Api/Registry/ServiceHealth.php, 101 satır) sağlık
kontrol sonuçlarını toplar ve tek bir bütünleşik durum üretir. `getAggregatedStatus()`
(S1:34) `match` ifadesiyle üç sayacı günceller.

| # | Durum | Sayıcı | Koşul | Satır |
|---|---|---|---|---|
| 1 | `healthy` | `$healthyCount` | match kolu | 45 |
| 2 | `degraded` | `$degradedCount` | match kolu | 46 |
| 3 | `unhealthy` | `$unhealthyCount` | match kolu | 47 |
| 4 | diğer | `default => null` | yok sayılır | 48 |
| 5 | genel `unhealthy` | `$unhealthyCount > 0` | 54–55 |
| 6 | genel `degraded` | `$degradedCount > 0` | 56–57 |
| 7 | genel `healthy` | aksi durum | 53 |

**Baskın-sonucu kuralı (S1):** tek bir `unhealthy` servis varsa genel durum
`unhealthy` olur; `unhealthy` yoksa tek bir `degraded` servis geneli `degraded`
yapar (S1:54–58). Bu **en-kötü-hal** (worst-case) yaklaşımıdır. `default => null`
kolu bilinmeyen bir durum değerini sessizce yok sayar → yeni bir durum eklendiğinde
sessizce kaybolur riski (bkz. §28-R4).

## §28 Senkron Yayım Davranış Analizi

`dispatch` gövdesi bir döngü içinde `$listener($event)` çağrısını **doğrudan**
yapar (S1:36–42); erteleme (defer), kuyruğa alma veya asenkron çalıştırma
görünmemektedir (S1 okundu). Bu, dinleyicilerin çağıran isteğin / çağıran
sürecin **aynı yığında** (call stack) çalıştığını gösterir.

| # | Gözlem | Sonuç | Kanıt |
|---|---|---|---|
| O1 | döngü senkron | evet | S1:36–41 |
| O2 | erteleme mekanizması | yok | S1 (böyle bir kod yok) |
| O3 | kuyruğa alma | yok | S1 + S5 |
| O4 | durdurma | `StoppableEventInterface` ile | S1:37 |
| O5 | istisna sarmalama | yok görünüyor | S1:41 (try/catch yok) |

| # | Risk | Açıklama | Severity |
|---|---|---|---|
| R1 | uzun süren dinleyici | isteği / süreci yavaşlatır | HIGH |
| R2 | eşit öncelikte kararsız sıra | deterministik olmayan sonuç | MEDIUM |
| R3 | dinleyici istisnası | çağırana yayılır (sarmalanmamış) | HIGH |
| R4 | bilinmeyen durum değeri | `default: null` ile sessiz kayıp | MEDIUM |
| R5 | senkron "kuyruk" sanılması | yanlış kapasite planı | HIGH |

**İstisna notu (O5):** döngüde `try/catch` görünmediği için bir dinleyicinin
fırlattığı istisna çağırana ulaşır ve sonraki dinleyiciler **çağrılmayabilir**
→ bu davranış `UNKNOWN` olarak işaretlidir (test okunmadı). Senkron yayımın
getirdiği bu risk, iş motoru seçimi (Q5) ile doğrudan ilgilidir.

## §29 Dinleyici Kayıt Yapısı Analizi

`EventDispatcher` iki özel dizi alan kullanır: `$listeners` (kayıtlı dinleyiciler)
ve `$sortedListeners` (sıralı önbellek) — ikisi de `private` ve sınıf-içi
dizidir (S1:22–23). Kayıt girişi `listener` ve `priority` anahtarlarını taşır
(S1:52–55).

| # | Alan | Tip | Amaç | Satır |
|---|---|---|---|---|
| 1 | `$listeners` | `array` | kayıt deposu | 22 |
| 2 | `$sortedListeners` | `array` | sıralı önbellek | 23 |
| 3 | kayıt girdisi | `['listener','priority']` | dinleyici + öncelik | 52–55 |
| 4 | önbellek geçersizleştirme | `unset()` | kayıt eklemede | 58 |
| 5 | önbellek geçersizlaştırma | `unset()` | kayıt silmede | 76 |
| 6 | silme | `array_filter` + `!==` | aynı dinleyiciyi bul | 70–73 |

**Bellek notu (S1):** dinleyiciler `$listeners` ve `$sortedListeners` dizilerinde
**iki kez** tutulur (önbellek dolduğunda). Önbellek `unset` edildiğinde yeniden
hesaplanır. Kayıt sayısı arttıkça bellek ve `getListeners` maliyeti artar —
ölçüm `⚠️ VERIFICATION REQUIRED` (dinleyici sayısı çalışma zamanında bilinmiyor).

## §30 Yokluk Deseni Detay Kaydı

§5'teki altı desenin her biri için arama ve sonuç aşağıdaki gibidir; hepsi 319
PHP dosyalık repo üzerinde çalıştırıldı (S5 ölçüm tarihi 2026-10-13).

| # | Desen | Glob | Sonuç |
|---|---|---|---|
| 1 | kuyruk | `**/*Queue*.php` | 0 |
| 2 | zamanlayıcı | `**/*Cron*.php` | 0 |
| 3 | zamanlayıcı (alternatif) | `**/*Timer*.php` | 0 |
| 4 | çalışan | `**/*Worker*.php` | 0 |
| 5 | iş | `**/*Job*.php` | 0 |
| 6 | görev | `**/*Task*.php` | 0 |
| 7 | asenkron | `**/*Async*.php` | 0 |
| 8 | planlama | `**/*Schedule*.php` | 0 |
| 9 | dağıtma | `**/*Dispatch*.php` | 1 (yalnız EventDispatcher.php + test) |

**Desen 9 notu (S1):** `Dispatch` deseni tek isabet verdi: `EventDispatcher.php`
ve test dosyası. Yani "dağıtma" adını taşıyan başka bir iş dağıtım sınıfı
**yoktur**; tek olan olay dağıtıcısıdır. Bu, §28-O2/O3 ile tutarlıdır — iş
dağıtımı (kuyruk/worker) kodda bulunmaz.

## §31 Ölçüm Kaydı (Sürücü Dosyaları)

Sürücü ile doğrudan ilgili dosyaların satır ve sembil ölçümleri (2026-10-13, S1).

| # | Dosya | Satır | Sınıf/interface satırı | Metin |
|---|---|---|---|---|
| 1 | shared/src/Events/EventDispatcher.php | 119 | 20 | final class |
| 2 | shared/src/Contracts/Events/IntegrationEventInterface.php | 46 | 17 | interface |
| 3 | shared/src/Api/Registry/ServiceHealth.php | 100 | 17 | final class |
| 4 | shared/src/Events/Domain/ (9 olay) | — | — | sayım 9 |
| 5 | shared/src/Events/Integration/ (3 olay) | — | — | sayım 3 |

**Ölçüm notu:** `read` ile okunan dosyaların görünür son satır numarası 119 / 46 /
100'dir (başlık satırı 1'den sayılır); checkpoint özeti bu dosyaları 120 / 47 / 101
olarak kaydetmiştir (muhtemelen sondaki boş satır dahil). Bu dosyada **satır ucu
farkı** olduğu kabul edilir; kesin sayı için `wc -l` gerekir → `⚠️ VERIFICATION
REQUIRED`. Sembol satır numaraları (dispatch:28, addListener:50 vb.) her iki
kaynakta da aynıdır ve çelişmez.

## §32 Sürücü Kapı Özeti (Sürücü Tarafı)

Sürücünün çözüm bekleyen kapıları; her kapı bir üst-belgede tanımlı kapıya bağlanır.

| # | Kapı | Konu | Bağlantı | Durum |
|---|---|---|---|---|
| 1 | Q5 | iş motoru bilinmiyor | anayasa §9 | açık |
| 2 | Q-bj3 | `dispatch` senkron mu (kanıt: §28-O1) | bu dosya §28 | analiz edildi, test yok |
| 3 | Q-bj4 | olay kuyruğa giriyor mu (kanıt: §28-O3) | bu dosya §28 | hayır görünüyor |
| 4 | Q-bj5 | retry politikası var mı | — | UNKNOWN |
| 5 | Q-bj6 | `checkAll()` tetikleyicisi ne | — | okunmadı |
| 6 | Yeni | eşit öncelik sıralaması kararsız mı | §24 | UNKNOWN |
| 7 | Yeni | dinleyici istisnası çağırana yayılıyor mu | §28-O5 | UNKNOWN |
| 8 | Yeni | `eventName()` ve `get_class` adları nasıl bağlanıyor | §25 | okunmadı |

**Kapı kuralı:** bu sekiz kapı hiçbirinde motor adı, kuyruk ürünü veya retry
değeri yazılmaz; hepsi açık kapı olarak sayılır ve §30'daki yokluk kaydıyla
bağlantılıdır.

## §33 Değişiklik Kaydı

| Tarih | Sürüm | Değişiklik |
|---|---|---|
| 2026-10-13 | 1.0.0 | sürücü yazıldı — mevcut tek yapı (olay yayımı) + 6 desenlik yokluk |
| 2026-10-13 | 1.1.0 | §23–§31 eklendi — PSR-14 uyum, öncelik sıralama, olay yaşam döngüsü, IntegrationEventInterface sözleşmesi, ServiceHealth toplama, senkron yayım analizi, kayıt yapısı, yokluk desen detayı, ölçüm kaydı |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
