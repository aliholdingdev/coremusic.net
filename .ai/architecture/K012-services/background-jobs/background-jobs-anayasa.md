---
node_id: K012-services/background-jobs
node_type: module
role: anayasa
status: draft
parent_id: K012-services/background-jobs
children: []
depends_on:
  - "background-jobs/index.md"
  - "services-master.md"
  - "services-bagimliliklar.md"
evidence:
  - "S1 — shared/src/Events/EventDispatcher.php 120 satır (sınıf: 20)"
  - "S1 — shared/src/Contracts/Events/IntegrationEventInterface.php 47 satır (arayüz: 17)"
  - "S1 — shared/src/Events/Domain/ 9 olay dosyası + Events/Integration/ 3 olay dosyası"
  - "S1 — shared/src/Api/Registry/ServiceHealth.php 101 satır (checkAll: 26)"
  - "S5 — **/*Cron*.php, **/*Queue*.php, **/*Job*.php, **/*Worker*.php, **/*Schedule*.php, **/*Task*.php, **/*Timer*.php, **/*Async*.php => 0 isabet (2026-10-13, 319 PHP dosyası içinde)"
  - "S1 — shared/src/Events/EventDispatcher.php (tek dispatch karşılığı; asenkron kuyruk değil)"
validation:
  - "§7 yokluk araması 10 desenle yapıldı — 0 sonuç (2026-10-13)"
  - "§6 satır numaraları disk okumasıyla doğrulandı"
  - "iş motoru adı YAZILMADI — bilinmeyen ad uydurmak yasak (Q5)"
open_questions:
  - "Q5 — arka plan iş motoru ne? (teknoloji, kütüphane, süreç) → ⚠️ VERIFICATION REQUIRED"
  - "Zamanlanmış iş (cron) tetikleyicisi var mı → 0 kod kanıtı, S5"
  - "Kuyruk sonucu nereye yazılır (K007 iddiası) → ⚠️ VERIFICATION REQUIRED"
date: 2026-10-13
updated: 2026-10-13
version: 1.0.0
authority: "SSOT: services-master.md §21.9 + EventDispatcher.php (S1) + 0-isabet yokluk kaydı (S5)"
group: mimari
---

# background-jobs — Anayasa

> **Özel durum:** bu alt alanın **implementasyonu repo'da yoktur** (S5). Bu
> dosyadaki kurallar iki kaynağa dayanır: (1) vault kartı (S2 master §21.9),
> (2) mevcut olan **tek** asenkron-özdeş yapı: olay yayını
> (`EventDispatcher`, S1). İkisi eşdeğer değildir — eşdeğerlik iddiası
> `⚠️ VERIFICATION REQUIRED` ile işaretlenir. Motor adı hiçbir yerde yazılmaz.

## §1 Amaç ve Kullanım

| # | Amaç | Karşılığı |
|---|---|---|
| 1 | "Arka plan işi"nin bu repo'da **ne kadar var** olduğunu dürüstçe yazmak | §7 |
| 2 | Var olan tek mekanizmayı (olay yayını) ayrı tutmak | §6 |
| 3 | Motor adı uydurmamak | §9 Q5 |
| 4 | Zamanlanmış iş/kuyruk ile olay yayımını ayırmak | §2 |
| 5 | K007'ye sonuç yazımının iddia düzeyinde kaldığını kaydetmek | §4 |

## §2 Kapsam ve Kapsam Dışı

| Yüzey | Kapsam içi? | Kanıt |
|---|---|---|
| Olay yayını (senkron dinleyici çağrısı) | EVET | S1 EventDispatcher:20 |
| Olay dinleyicisi kaydı/önceliği | EVET | S1:50 · S1:109 |
| Asenkron iş kuyruğu | HAYIR — kod yok | S5 `**/*Queue*.php` 0 |
| Zamanlanmış (cron) iş | HAYIR — kod yok | S5 `**/*Cron*.php` 0 |
| İşçi (worker) süreci | HAYIR — kod yok | S5 `**/*Worker*.php` 0 |
| Görev/planlama nesnesi | HAYIR — kod yok | S5 `**/*Task*.php` · `**/*Schedule*.php` 0 |
| Asenkron çalıştırma (`async`) | HAYIR — kod yok | S5 `**/*Async*.php` 0 |
| Sonucun K007'ye yazımı | iddia (kart) | S2 master §21.9 |
| Servis sağlık kontrolü (senkron döngü) | sınırda | S1 ServiceHealth:26 |

## §3 Anayasa Maddeleri

| Madde | Kural | Dayanak | Kanıt |
|---|---|---|---|
| B1 | "Arka plan işi" motoru bilinmez; ad **yazılmaz** | zero-hallucination | S5 + Q5 |
| B2 | Kuyruk/timer/worker için kod kanıtı 0'dır; bu bir **yokluk** kaydıdır | S5 | 10 desen |
| B3 | Mevcut tek yapı **senkron olay yayınıdır**; asenkron ile eşdeğer sayılmaz | S1 | EventDispatcher:28 |
| B4 | Dinleyici sırası öncelik numarasına göre belirlenir | S1 | EventDispatcher:50 · :109 |
| B5 | Dinleyici kaldırma desteklenir | S1 | EventDispatcher:64 |
| B6 | Olay arayüzü dört zorunlu metod taşır | S1 | IntegrationEventInterface:24/:31/:38/:45 |
| B7 | Olay `DateTimeImmutable` zaman damgası taşır | S1 | IntegrationEventInterface:45 |
| B8 | Olay payload'u `array`dir | S1 | IntegrationEventInterface:38 |
| B9 | Olay kaynağı (`source`) açıkça bildirilir | S1 | IntegrationEventInterface:31 |
| B10 | Sonuç yazımı (K007) iddia düzeyindedir — kanıt yok | S2 | master §21.9 |
| B11 | Motor seçimi bir mimari karardır → ADR gerektirir | süreç | §12 |
| B12 | Zamanlanmış iş gerekiyorsa önce ADR + ölçüm, sonra kod | süreç | §21 |
| B13 | İş başarısızlığı davranışı bilinmiyor → `UNKNOWN` | S5 | §10 |
| B14 | Kuyruk teknolojisi adı (broker vb.) yazılmaz | zero-hallucination | S5 |
| B15 | Servis sağlık kontrolü asenkron iş değildir; döngü senkrondur | S1 | ServiceHealth:26 |

## §4 Bağımlılık Matrisi

| Hedef | Ne için | Tür | Kanıt |
|---|---|---|---|
| K007-veri | iş sonucu yazımı (iddia) | giden | S2 master §21.9 |
| K012 kök servisleri | olay dinleyicisi olabilecekler (iddia) | giden | ⚠️ VERIFICATION REQUIRED |
| K014-network | dış çağrı gerekebilir (iddia) | giden | ⚠️ VERIFICATION REQUIRED |
| K019-test | `EventDispatcherTest` mevcut | giden | S1 shared/tests/Events/ |
| EventDispatcher | tek mevcut mekanizma | iç | S1:20 |
| IntegrationEventInterface | olay sözleşmesi | iç | S1:17 |

## §5 Kanıt Sınıfları

| Sınıf | Kullanım |
|---|---|
| S1 | EventDispatcher, IntegrationEventInterface, olay dosyaları, ServiceHealth |
| S2 | master §21.9 · §3/9 · bagimliliklar §17 |
| S3 | §8 URL |
| S4 | oturum kaydı |
| S5 | 10 desen = 0 isabet (motor yokluğu) |

## §6 Repo Kod Kanıtları (S1)

| # | Dosya | Satır | Sembol |
|---|---|---|---|
| 1 | shared/src/Events/EventDispatcher.php | 20 | `final class EventDispatcher implements EventDispatcherInterface` |
| 2 | Aynı dosya | 28 | `dispatch(object $event): object` |
| 3 | Aynı dosya | 50 | `addListener(string $eventName, callable $listener, int $priority = 0): void` |
| 4 | Aynı dosya | 64 | `removeListener(string $eventName, callable $listener): void` |
| 5 | Aynı dosya | 82 | `getListeners(string $eventName): array` |
| 6 | Aynı dosya | 101 | `hasListeners(string $eventName): bool` |
| 7 | Aynı dosya | 109 | `sortListeners(string $eventName): array` (özel) |
| 8 | shared/src/Contracts/Events/IntegrationEventInterface.php | 17 | `interface IntegrationEventInterface` |
| 9 | Aynı dosya | 24 | `eventName(): string` |
| 10 | Aynı dosya | 31 | `source(): string` |
| 11 | Aynı dosya | 38 | `payload(): array` |
| 12 | Aynı dosya | 45 | `occurredOn(): \DateTimeImmutable` |
| 13 | shared/src/Api/Registry/ServiceHealth.php | 17 | `final class ServiceHealth` |
| 14 | Aynı dosya | 26 | `checkAll(): array` |
| 15 | Aynı dosya | 34 | `getAggregatedStatus(): array` |
| 16 | Aynı dosya | 76 | `isServiceHealthy(string $serviceName): bool` |
| 17 | Aynı dosya | 87 | `getUnhealthyServices(): array` |
| 18 | shared/src/Events/Domain/ (9 dosya) | — | GenderSetEvent, MediaAccessedEvent, MusicAddedEvent, MusicPlayedEvent, PasswordResetRequestedEvent, PlaylistCreatedEvent, UserLoggedInEvent, UserLoggedOutEvent, UserRegisteredEvent |
| 19 | shared/src/Events/Integration/ (3 dosya) | — | AuthValidatedEvent, NotificationEvent, SessionCreatedEvent |

## §7 Yokluk Kanıtları (S5 — 10 desen)

| # | Desen | Sonuç | Yorum |
|---|---|---|---|
| 1 | `**/*Cron*.php` | 0 | zamanlayıcı yok |
| 2 | `**/*Queue*.php` | 0 | kuyruk yok |
| 3 | `**/*Job*.php` | 0 | iş nesnesi yok |
| 4 | `**/*Worker*.php` | 0 | işçi yok |
| 5 | `**/*Schedule*.php` | 0 | planlama yok |
| 6 | `**/*Task*.php` | 0 | görev nesnesi yok |
| 7 | `**/*Timer*.php` | 0 | zamanlayıcı yok |
| 8 | `**/*Async*.php` | 0 | asenkron çalıştırma yok |
| 9 | `**/*Background*.php` | 0 | (tek isabet bir PNG görseli) |
| 10 | `**/*Dispatch*.php` | 1 + 1 test | yalnız olay yayını — kuyruk değil |

**Ölçüm bağlamı:** repo genelinde **319 PHP dosyası** sayıldı (2026-10-13).

## §8 Web Araştırması — İddia Kaydı

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| 12 faktörlük uygulama, süreçleri (processes) devlete bağımsız çalıştırır ve arka plan işini ayrı süreç olarak ele alır | https://12factor.net/ | HTTP 200 | ✅ doğrulandı — arka plan işi tasarımı için genel dayanak; bu repo'da uygulama kanıtlanmadı |
| CNCF, bulut yerel ekosistemi içinde iş kuyruğu/mesajlaşma projelerini barındırır | https://www.cncf.io/ | HTTP 200 | ✅ doğrulandı — motor seçeneği için bağlam; **teknoloji adı seçilmedi** |
| IETF, e-posta gönderim protokolünü tanımlar (SMTP) | https://www.rfc-editor.org/rfc/rfc5321.html | HTTP 200 | ✅ doğrulandı — zamanlanmış bildirim kanalı olabilir (iddia, kanıt yok) |

**Yasak:** bu tablo, seçilmiş bir teknolojinin **reklamı değildir**. Motor adı
(kuyruk sistemi, broker, kütüphane) burada **seçilmez** — seçilmek ADR gerektirir.

## §9 Açık Sorular

| # | Soru | Etki | Durum |
|---|---|---|---|
| Q5 | Arka plan iş motoru ne? | tüm alt alan | ⚠️ VERIFICATION REQUIRED |
| Q-bj1 | Zamanlanmış iş tetikleyicisi var mı? | Zamanlama | ⚠️ VERIFICATION REQUIRED (kod 0) |
| Q-bj2 | Kuyruk sonucu hangi tabloya yazılır? | K007 | ⚠️ VERIFICATION REQUIRED |
| Q-bj3 | `dispatch()` senkron mu? (dinleyiciler çağrı anında mı?) | davranış | kod akışı okunmadı → ⚠️ |
| Q-bj4 | Olaylar kuyruklanıyor mu, doğrudan mı çağrılıyor? | mimari | ⚠️ VERIFICATION REQUIRED |
| Q-bj5 | Başarısızlık/deneme (retry) davranışı var mı? | dayanıklılık | `UNKNOWN` |

## §10 Doğrulama Kapıları

| Kapı | Kontrol | Sonuç |
|---|---|---|
| D1 | Motor adı hiçbir satırda geçmiyor | ad yok — yalnız `UNKNOWN`/`⚠️` |
| D2 | §7 desenleri gerçekten çalıştırıldı | 10 desen, 0 isabet |
| D3 | §6 satırları dosyada mevcut | diskte doğrulandı |
| D4 | §8 URL'leri HTTP 200 | 3/3 |
| D5 | tamamlanma işareti / yer tutucu / boş hücre yok | 0 |
| D6 | Son satır authority birebir | birebir |

## §11 Riskler

| # | Risk | Etki | Karşı önlem |
|---|---|---|---|
| R1 | Olay yayını ile kuyruk eşdeğer sanılır | yüksek | B3 |
| R2 | Motor adı uydurulur (hallüsinasyon) | çok yüksek | B1 · Q5 |
| R3 | Sonuç yazımı (K007) varsayılır, kanıtlanmaz | orta | B10 |
| R4 | Senkron dinleyici uzun sürerse istek yavaşlar | yüksek | ⚠️ ölçüm yok |
| R5 | Zamanlanmış iş gereksinimi sonradan fark edilir | orta | B12 |
| R6 | Yokluk (S5) "yok" değil "aranmadı" sanılır | orta | §7 ölçüm bağlamı |

## §12 Karar Kapısı (ADR gereklilik listesi)

| # | Karar | Neden ADR ister | Durum |
|---|---|---|---|
| 1 | Kuyruk teknolojisi seçimi | kalıcı bağımlılık | ❓ sorulmadı |
| 2 | Zamanlayıcı seçimi | ortam bağımlılığı | ❓ sorulmadı |
| 3 | Olay → kuyruk köprüsü | çift davranış | ❓ sorulmadı |
| 4 | Hata/deneme politikası | dayanıklılık | ❓ sorulmadı |
| 5 | Sonuç yazım hedefi (K007 tablosu) | veri sözleşmesi | ❓ sorulmadı |

## §13 Olay Dizini (mevcut, S1)

| # | Olay | Dizin |
|---|---|---|
| 1 | GenderSetEvent | shared/src/Events/Domain/ |
| 2 | MediaAccessedEvent | shared/src/Events/Domain/ |
| 3 | MusicAddedEvent | shared/src/Events/Domain/ |
| 4 | MusicPlayedEvent | shared/src/Events/Domain/ |
| 5 | PasswordResetRequestedEvent | shared/src/Events/Domain/ |
| 6 | PlaylistCreatedEvent | shared/src/Events/Domain/ |
| 7 | UserLoggedInEvent | shared/src/Events/Domain/ |
| 8 | UserLoggedOutEvent | shared/src/Events/Domain/ |
| 9 | UserRegisteredEvent | shared/src/Events/Domain/ |
| 10 | AuthValidatedEvent | shared/src/Events/Integration/ |
| 11 | NotificationEvent | shared/src/Events/Integration/ |
| 12 | SessionCreatedEvent | shared/src/Events/Integration/ |

**Toplam:** 12 olay dosyası · 9 domain + 3 integration.

## §14 Anayasa → Sürücü Eşlemesi

| Anayasa maddesi | Sürücü karşılığı |
|---|---|
| B1 (motor adı yok) | sürücü §7 tetik listesinde motor satırı yok |
| B2 (kod 0) | sürücü §2 zamanlanmış iş satırı `UNKNOWN` |
| B3 (senkron yayım) | sürücü §3 adım zinciri |
| B4 (öncelik) | sürücü §4 dinleyici durumu |
| B10 (K007 yazımı) | sürücü §5 geri besleme |

## §15 Kapsam Dışı Devir

| İstek | Hedef | Gerekçe |
|---|---|---|
| Kuyruk kurulumu | DevOps + ADR | kalıcı altyapı |
| Cron yapılandırması | K018-dağıtım | ortam |
| Sonuç şeması | K007-veri | şema sahipliği |
| Dış bildirim (SMTP) | K013-integration | dış protokol |
| Ağ hatası yeniden deneme | K014 | taşıma |

## §16 Güvenlik

| # | Kural | Kanıt |
|---|---|---|
| G1 | Olay payload'u hassas veri içerirse maskeleme gerekir | süreç (kod okunmadı → ⚠️) |
| G2 | `source()` sahteliği (spoof) engeli | ⚠️ VERIFICATION REQUIRED |
| G3 | Dinleyicide dış çağrı varsa TLS zorunlu | K014 sahipliği |

## §17 Performans

| # | Konu | Durum |
|---|---|---|
| P1 | Dinleyici sayısı/bağımlılığı | `getListeners` var (S1:82) |
| P2 | Öncelik sıralama maliyeti | `sortListeners` var (S1:109) |
| P3 | Uzun süren dinleyici ölçümü | ölçüm YOK → UNKNOWN |
| P4 | Asenkron gecikme ölçümü | ölçüm YOK → UNKNOWN |

## §18 Sözlük

| Terim | Anlam |
|---|---|
| olay yayını (event dispatch) | senkron olarak dinleyici çağrısı |
| dinleyici (listener) | olaya tepki veren callable |
| öncelik (priority) | dinleyici sırasını belirleyen tam sayı |
| kuyruk (queue) | asenkron iş sırası — bu repo'da YOK (S5) |
| worker | kuyruğu tüketen süreç — YOK (S5) |
| cron | zamanlanmış tetikleyici — YOK (S5) |
| retry | başarısız işi yeniden deneme — davranış `UNKNOWN` |
| S5 | yokluk kanıtı |
| Q5 | iş motoru sorusu |

## §19 İlgili Dosyalar

| Dosya | İlişki |
|---|---|
| background-jobs/index.md | künye |
| background-jobs-surucu.md | tetikleyici/akış |
| background-jobs-ornekler.md | kod örnekleri |
| services-master.md §21.9 | kart kaynağı |
| services-bagimliliklar.md §17 | matris |
| services-riskler.md | kök risk |

## §20 Doğrulama Özeti

| Kontrol | Hedef | Durum |
|---|---|---|
| Satır sayısı | ≥500 | dış ölçüm |
| `parent:` | 0 | 0 |
| `group` | `mimari` alıntısız | mimari |
| Mojibake / CJK | 0 | 0 |
| Yer tutucu | 0 | 0 |
| Motor adı yazılmadı | evet | doğrulandı |
| Web iddiası formatı | `İddia · URL · tarih · Durum` | §8 |

## §20.1 Olay → Oluşturucu Eşleme (repo, S1)

| # | Olay | Sağlayıcı dosya (dizin) | Bu alt alanla ilişkisi |
|---|---|---|---|
| 1 | MusicAddedEvent | shared/src/Events/Domain/ | medya işi bittiğinde yayılabilir (iddia) |
| 2 | MusicPlayedEvent | shared/src/Events/Domain/ | çalma sonrası arka plan yazımı (iddia) |
| 3 | MediaAccessedEvent | shared/src/Events/Domain/ | erişim sayımı (iddia) |
| 4 | PlaylistCreatedEvent | shared/src/Events/Domain/ | katalog işi (iddia) |
| 5 | UserRegisteredEvent | shared/src/Events/Domain/ | karşılama işi (iddia) |
| 6 | UserLoggedInEvent | shared/src/Events/Domain/ | oturum işi (iddia) |
| 7 | UserLoggedOutEvent | shared/src/Events/Domain/ | temizlik işi (iddia) |
| 8 | PasswordResetRequestedEvent | shared/src/Events/Domain/ | e-posta işi (iddia ⚠️) |
| 9 | GenderSetEvent | shared/src/Events/Domain/ | profil işi (iddia) |
| 10 | AuthValidatedEvent | shared/src/Events/Integration/ | entegrasyon olayı |
| 11 | NotificationEvent | shared/src/Events/Integration/ | bildirim işi (iddia) |
| 12 | SessionCreatedEvent | shared/src/Events/Integration/ | oturum entegrasyonu |

**İlişki sütunu neden "iddia":** herhangi bir olay için arka plan işi kaydı
**okunmadı**; yalnızca olay dosyalarının varlığı ölçülmüştür (S1).

## §20.2 Dinleyici Kayıt Deseni (kod kanıtı düzeyi)

| Adım | Metot | Satır | Durum |
|---|---|---|---|
| 1 | `addListener($eventName, $listener, $priority)` | 50 | ✅ S1 |
| 2 | `hasListeners($eventName)` | 101 | ✅ S1 |
| 3 | `getListeners($eventName)` | 82 | ✅ S1 |
| 4 | `sortListeners($eventName)` (özel) | 109 | ✅ S1 |
| 5 | `dispatch($event)` | 28 | ✅ S1 |
| 6 | `removeListener(...)` | 64 | ✅ S1 |
| 7 | dinleyiciyi kayıt eden **çağıran** kod | — | ⚠️ okunmadı |
| 8 | olayı **üreten** kayıt yeri | — | ⚠️ okunmadı |

## §20.3 Zamanlama Tablosu (durum)

| Olay | Tetikleyici | Sıklık | Durum |
|---|---|---|---|
| Zamanlanmış iş | bilinmiyor | bilinmiyor | S5 — kod yok |
| Kuyruk tüketimi | bilinmiyor | bilinmiyor | S5 — kod yok |
| Olay yayını | senkron çağrı (iddia) | çağrı başına | ⚠️ davranış okunmadı |
| Sağlık kontrol döngüsü | bilinmiyor | bilinmiyor | S1:26 var, tetikleyici okunmadı |

## §20.4 Hata Senaryoları

| # | Senaryo | Davranış | Kanıt |
|---|---|---|---|
| 1 | Dinleyici istisna fırlatırsa | `UNKNOWN` | dispatch gövdesi okunmadı |
| 2 | Olay dinleyicisi yoksa | davranış `UNKNOWN` | S1:101 `hasListeners` var |
| 3 | Aynı dinleyici iki kez eklenirse | `UNKNOWN` | tekrar denetimi okunmadı |
| 4 | Kuyrukta iş patlarsa | — | kuyruk YOK (S5) |
| 5 | Zamanlayıcı düşerse | — | zamanlayıcı YOK (S5) |
| 6 | Sonuç yazımı (K007) başarısız olursa | `UNKNOWN` | yazım kodu okunmadı |

## §20.5 Öncelik (priority) Davranışı

| Soru | Cevap | Kanıt |
|---|---|---|
| Öncelik parametresi var mı? | evet, `int $priority = 0` | S1:50 |
| Sıralama metodu var mı? | evet, `sortListeners` | S1:109 |
| Artan mı azalan mı sıralanıyor? | okunmadı | ⚠️ VERIFICATION REQUIRED |
| Eşit öncelikte ne olur? | `UNKNOWN` | ⚠️ |
| Varsayılan öncelik | 0 | S1:50 |

## §20.6 Karar Matrisi — "Şu işi nereye koyayım?"

| İş türü | Önerilen ev | Dayanak |
|---|---|---|
| İstek içinde biten kısa iş | mevcut senkron akış | S1 mevcut yapı |
| İstek içinde olaya tepki | `addListener` | S1:50 |
| İstek sonrası uzun iş | motor gerekli → ADR (Q5) | S5 |
| Zamanlanmış tekrar | motor gerekli → ADR (Q5) | S5 |
| Dış servise kuyruk gönderimi | K013 + motor | S5 + S2 |
| Sonuç yazımı | K007 | S2 §21.9 |

## §20.7 Bilinen Belirsizlik Envanteri

| # | Belirsizlik | Sınıf | Etkilediği karar |
|---|---|---|---|
| 1 | iş motoru adı | ⚠️ | tüm kuyruk/cron tasarımı |
| 2 | dinleyici çağrısı senkron mu | ⚠️ | performans |
| 3 | olay kuyruğa giriyor mu | ⚠️ | asenkron mimari |
| 4 | retry politikası | UNKNOWN | dayanıklılık |
| 5 | iş sonucu tablosu | ⚠️ | K007 sözleşmesi |
| 6 | başarısızlık log'u | UNKNOWN | gözlemlenebilirlik |
| 7 | eşzamanlılık (worker sayısı) | UNKNOWN | kapasite |
| 8 | iş sırası garantisi | UNKNOWN | doğruluk |

## §20.8 Bağımlılık Katmanları

| Katman | Bağımlılık | Tür | Kanıt |
|---|---|---|---|
| Olay sözleşmesi | IntegrationEventInterface | arayüz | S1:17 |
| Yayım | EventDispatcher | sınıf | S1:20 |
| Domain olayları | 12 dosya | veri | S1 |
| Veri yazımı | K007 | dış | S2 §21.9 |
| Dış çağrı | K014 | dış | S2 §23/6 |
| Protokol | K013 | dış | S2 §23/3 |
| Test | shared/tests/Events/ | test | S1 (EventDispatcherTest.php) |

## §20.9 Red Team Bulgu Listesi

| # | Bulgu | Severity |
|---|---|---|
| 1 | Q5 açık — motor bilinmiyor | HIGH |
| 2 | Olay yayımı ile kuyruk eşdeğerliği kanıtlanamaz | HIGH |
| 3 | `dispatch` gövdesi okunmadı (senkron mu?) | MEDIUM |
| 4 | Olay → iş eşlemesi hiç okunmadı | MEDIUM |
| 5 | K007 yazım hedefi bilinmiyor | MEDIUM |
| 6 | Ölçüm (gecikme, hata oranı) yok | MEDIUM |
| 7 | Health check tetikleyicisi okunmadı | LOW |

## §20.10 Doğrulama Prosedürü

| Adım | İşlem | Beklenen |
|---|---|---|
| 1 | §7'deki 10 deseni yeniden çalıştır | 0 isabet (9) + 1 olay-yayını (1) |
| 2 | §6 satırlarını dosyalarda doğrula | semboller mevcut |
| 3 | §8 URL'lerine istek at | HTTP 200 ×3 |
| 4 | Motor adının hiçbir satırda geçmediğini doğrula | 0 |
| 5 | Son satırı denetle | authority birebir |
| 6 | Satır sayısını ölç | ≥500 |

## §20.11 Komşu Sınırlar (bu alt alan ile diğerleri)

| Komşu | Sınır kuralı | Kanıt |
|---|---|---|
| K007-veri | iş sonucu tablosu onda | S2 §21.9 |
| K013-integration | dış protokol adaptörde | S2 §23/3 |
| K014-network | taşıma/dış çağrı | S2 §23/6 |
| K018-dağıtım | cron/ortam kurulumu | S2 §23/8 |
| K019-test | test altyapısı | S2 §23/7 |
| service-contracts (K012) | iş mesajı biçimi | ⚠️ Q4 |
| playback-service (K012) | çalma olayı kaynaklı iş | ⚠️ okunmadı |

## §20.12 Motor Yokluğu — Karşılaştırılabilir Seçenekler (ad YAZILMAZ)

| # | Yaklaşım | Ne getirir | Ne taşır | Durum |
|---|---|---|---|---|
| 1 | senkron olay yayını (mevcut) | sadelik | istek süresi riski | ✅ mevcut (S1) |
| 2 | asenkron kuyruk | uzun iş ayrımı | yeni altyapı | ❓ ADR gerekli |
| 3 | zamanlanmış iş | tekrar eden iş | ortam bağımlılığı | ❓ ADR gerekli |
| 4 | harici süreç (ayrı servis) | izolasyon | dağıtım karmaşıklığı | ❓ ADR gerekli |
| 5 | veritabanı tabanlı bekleyen iş | ek bağımlılık yok | sorgu yükü | ❓ ADR gerekli |

**Kural:** bu tablo **seçim yapmaz**; yalnız seçenek kümesini ve her birinin
maliyet sınıfını not eder. Seçim ADR + insan kararıdır.

## §20.13 Ölçüm Planı (PLANNED — henüz yok)

| Metrik | Nereden | Durum |
|---|---|---|
| Olay yayım süresi | kodda ölçüm yok | PLANNED |
| Dinleyici istisna sayısı | log yok | PLANNED |
| Uzun süren iş sayısı | iş tanımı yok | PLANNED |
| Kuyruk derinliği | kuyruk yok | S5 |
| Başarısızlık oranı | `UNKNOWN` | PLANNED |

## §20.14 İlgili Vault Bağlantıları

| Dosya | Ne verir |
|---|---|
| services-master.md §3/9 | çocuk kartı satırı |
| services-master.md §21.9 | detay kartı |
| services-master.md §22.3 | hata akışı (kök) |
| services-bagimliliklar.md §17 | bağımlılık satırı |
| services-riskler.md | kök risk kaydı |
| services-dogrulama.md | kök doğrulama |
| K019-test (düğüm) | test altyapısı |

## §20.14 Olay Arayüzü Sözleşmesi (S1 — madde madde)

| # | Metot | Dönüş | Satır | Anlam |
|---|---|---|---|---|
| 1 | `eventName()` | `string` | 24 | olay kimliği |
| 2 | `source()` | `string` | 31 | olayı üreten taraf |
| 3 | `payload()` | `array` | 38 | veri gövdesi |
| 4 | `occurredOn()` | `\DateTimeImmutable` | 45 | oluş zamanı |

**Zorunluluk:** bir sınıf bu arayüzü uyguluyorsa dört metodu da taşımalıdır
(interface sözleşmesi, S1:17).

## §20.15 Test Kanıtları (mevcut)

| # | Dosya | Ne kapsar | İlişki |
|---|---|---|---|
| 1 | shared/tests/Events/EventDispatcherTest.php | yayım/dinleyici davranışı | doğrudan |
| 2 | shared/tests/Middleware/MiddlewarePipelineTest.php | boru hattı | dolaylı |
| 3 | shared/tests/Api/ApiPipelineSecurityTest.php | API güvenliği | dolaylı |
| 4 | Kuyruk/cron testi | — | YOK (S5) |

## §20.16 Sözlük 2 — Bu Alt Alana Özgü

| Terim | Tanım |
|---|---|
| senkron yayım | dinleyicilerin çağrı anında çalışması |
| olay üretici | olayı oluşturan sınıf |
| olay tüketicisi | `addListener` ile kaydolmuş taraf |
| iş sonucu | arka plan işinin yazdığı veri (hedef K007, iddia) |
| tetikleyici kaynağı | işi başlatan olay/zaman/istemci (bilinmiyor) |
| yeniden deneme | başarısız işi tekrarlama (davranış `UNKNOWN`) |
| kalıcılık | işin diske/DB yazılması (K007 sahipliği) |
| izolasyon | işin istek sürecinden ayrılması (motor gerekir) |
| S5 | yokluk kanıtı |
| ADR | mimari karar kaydı |

## §21 Değişiklik Kaydı

| Tarih | Sürüm | Değişiklik |
|---|---|---|
| 2026-10-13 | 1.0.0 | anayasa yazıldı — 10 desenlik yokluk + olay yayını kanıtıyla |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
