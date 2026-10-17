---
node_id: K012-services/integration-service
node_type: module
role: anayasa
status: draft
parent_id: K012-services/integration-service
children: []
depends_on:
  - "integration-service/index.md"
  - "services-master.md"
  - "services-bagimliliklar.md"
evidence:
  - "v2 §2.12 — çocuk adı kaynağı (birebir)"
  - "S1 — shared/src/Contracts/Events/IntegrationEventInterface.php 46 satır (interface:17, 4 method)"
  - "S1 — shared/src/Events/Integration/AuthValidatedEvent.php 86 satır (class:20)"
  - "S1 — shared/src/Events/Integration/NotificationEvent.php 104 satır (class:20)"
  - "S1 — shared/src/Events/Integration/SessionCreatedEvent.php 94 satır (class:20)"
  - "S1 — shared/src/Events/EventDispatcher.php 119 satır (dispatch:28)"
  - "S1 — shared/src/Events/StoppableEventTrait.php 36 satır (trait:17)"
  - "S1 — shared/composer.json:18 psr/event-dispatcher ^1.0"
validation:
  - "§3–§9 kod kanıtı 2026-10-13 read — birebir"
  - "§12 repo taraması 2026-10-13 grep — üretim çağrısı 0"
  - "§16 sınır services-master §21.8 ile hizalı"
open_questions:
  - "K013 ile çağrı yönü ve hata sınırı → ⚠️ VERIFICATION REQUIRED (index FM)"
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Integration event'lerin üretime yayılmış tek bir tetikleyicisi yok → nerede dispatch edileceği bilinmiyor → ⚠️ VERIFICATION REQUIRED"
  - "K010-network ile ilişki (giden bağımlılık iddiası) kodda kanıtlanmadı → ⚠️ VERIFICATION REQUIRED"
date: 2026-10-13
updated: 2026-10-13
version: 1.0.0
authority: "SSOT: v2 §2.12 + shared/src/Events/ (S1, 2026-10-13 read)"
group: mimari
---

# integration-service — Anayasa (Constitution)

> **Ön not:** integration-service, **dış sistem çağrılarını** barındıran K012
> servisidir (master §21.8: "Dış bağlantı servisi — K013 kontratlarını çağıran
> servis"). Bu dosya servisin anayasasını yazar. Kritik ölçüm: repo'da bu
> servise ait **çağrı yapan production kodu yoktur** — eldeki kanıt, ortak
> event altyapısı (EventDispatcher + 3 integration event + 2 interface)
> ve testlerdir. Bu ayrım §12'de kanıtlanır.

## §1 ELI15 — Bu Servis Ne Yapar?

Dış dünyayla konuşan servistir. Kendi başına protokol uygulamaz: protokol,
adaptör ve hata yönetimi **K013-integraion**'dadır; bu servis yalnızca K013'ün
kontratlarını **çağırır** (master §2.2, §21.8). Repo kanıt düzeyi ise daha
dar: `shared/src/Events/Integration/` altında **3 adet integration event
sınıfı** ve bunların uyması gereken **1 interface** vardır; production kodda
bunları üreten/çağıran tek bir satır bulunamamıştır (§12). Yani bu servisin
anayasası iki katmanlıdır: (a) tasarımdaki dış-bağlantı rolü, (b) diskteki
gerçek event sözleşmesi.

## §2 Sınır Tablosu

| Konu | Kapsam | Sahibi | Kanıt |
|---|---|---|---|
| Dış API protokolü (HTTP/HTTPS, hata kodları) | DIŞINDA | K013-integraion | master §2.2 satır 91 |
| Adaptör/dönüşüm katmanı | DIŞINDA | K013-integraion | bagimliliklar §8.4 |
| Kontratı çağıran servis iş mantığı | İÇİNDE | K012/integration-service | master §21.8 |
| Integration event sözleşmesi (4 method) | İÇİNDE (repo) | K012 (shared Events) | S1 IntegrationEventInterface:17 |
| Event dispatch mekanizması | ortak altyapı | shared/src/Events | S1 EventDispatcher:20 |
| Domain event'ler (9 adet) | DIŞINDA (ailiği) | K012'nin diğer servisleri | §11 |
| Ağ/bağlantı yönetimi | DIŞINDA (iddia) | K010-network | master §21.8 giden bağımlılık |
| Kademe-3 bounded-context kapsamı | AÇIK | — | §20 Q2 ⚠️ |

## §3 IntegrationEventInterface — Sözleşme (Birebir)

Dosya: `shared/src/Contracts/Events/IntegrationEventInterface.php` / Satır: 17–46

Dosya 46 satırdır; interface satır 17'de başlar; 4 public method taşır:

| # | Method | Dönüş | Satır | Amaç (docblock birebir) |
|---|---|---|---|---|
| 1 | `eventName(): string` | string | 24 | "Get the event name." |
| 2 | `source(): string` | string | 31 | "Get the source service that originated the event." |
| 3 | `payload(): array` | array | 38 | "Get the event payload data." |
| 4 | `occurredOn(): \DateTimeImmutable` | DateTimeImmutable | 45 | "Get the timestamp when the event occurred." |

**Şuul-notlar (birebir):** `@see ADR-086-event-driven-architecture` (satır 9) ·
namespace `CoreMusic\Contracts\Events` (satır 12) · `declare(strict_types=1)`
(satır 2).

**Domain/entegrasyon farkı (kanıtla):** `DomainEventInterface` (39 satır)
yalnız **3** method taşır — `eventName:24`, `occurredOn:31`, `payload:38`;
`source()` **yoktur**. Yani entegrasyon olayları, "kim başlattı" bilgisini
zorunlu kılar; domain olayları kılmaz. Bu, iki interface arasındaki tek
imzalı farktır (S1 read 2026-10-13).

## §4 Üç Integration Event — Envanter

Dosya: `shared/src/Events/Integration/` (3 dosya, glob 2026-10-13)

| # | Sınıf | Dosya satır | `eventName()` dönen değer | `source()` dönen değer |
|---|---|---|---|---|
| 1 | `AuthValidatedEvent` | 86 | `auth.validated` (S1:39) | constructor `source` (S1:45–48) |
| 2 | `NotificationEvent` | 104 | `notification.sent` (S1:40) | sabit `notification.service` (S1:48) |
| 3 | `SessionCreatedEvent` | 94 | `session.created` (S1:39) | sabit `auth.service` (S1:47) |

**Ortak özellikler (üçü de birebir):** `final class` + `implements
IntegrationEventInterface` (satır 20) · `use StoppableEventTrait;` (satır 22) ·
`private readonly \DateTimeImmutable $occurredOn;` (satır 24) · constructor
içinde `$this->occurredOn = new \DateTimeImmutable();` · `@see
ADR-086-event-driven-architecture` (satır 9) · namespace
`CoreMusic\Events\Integration` · `use CoreMusic\Contracts\Events\IntegrationEventInterface;`
(satır 14).

## §5 AuthValidatedEvent — Ayrıntı (Birebir)

Dosya: `shared/src/Events/Integration/AuthValidatedEvent.php` / Satır: 26–61

Constructor (satır 26–32) 3 promoted parametre alır: `string $userId`,
`string $source`, `\DateTimeImmutable $validatedAt`.

`payload(): array` (satır 53–61) 4 anahtar döner:

| Anahtar | Kaynak | Değer biçimi |
|---|---|---|
| `userId` | constructor | string |
| `source` | constructor | string |
| `validatedAt` | constructor | `format('c')` (ISO 8601) |
| `timestamp` | `occurredOn` | `format('c')` |

Ek getter'lar: `getUserId(): string` (satır 74) · `getValidatedAt(): \DateTimeImmutable`
(satır 82). `source()` bu sınıfta **sabit değil**, constructor'dan gelir — yani
kaynak servis çağıran tarafça bildirilir.

## §6 NotificationEvent — Ayrıntı (Birebir)

Dosya: `shared/src/Events/Integration/NotificationEvent.php` / Satır: 26–62

Constructor (satır 26–33) 4 parametre alır: `string $userId`, `string $type`,
`string $message`, `array $data = []` (tek default değerli parametre — üç
event içindeki tek default).

`payload(): array` (satır 54–62) 5 anahtar: `userId`, `type`, `message`,
`data`, `timestamp`. Getter'lar: `getUserId:76` · `getType:84` ·
`getMessage:92` · `getData:100`. `source()` (satır 46–49) **`return
'notification.service';`** — sabit dize, yani bu olay her zaman kendi
kaynağını notification.service olarak beyan eder.

## §7 SessionCreatedEvent — Ayrıntı (Birebir)

Dosya: `shared/src/Events/Integration/SessionCreatedEvent.php` / Satır: 26–61

Constructor (satır 26–32) 3 parametre: `string $sessionId`, `string $userId`,
`\DateTimeImmutable $expiresAt`.

`payload(): array` (satır 53–61) 4 anahtar: `sessionId`, `userId`, `expiresAt`
(`format('c')`), `timestamp`. Getter'lar: `getSessionId:74` · `getUserId:82` ·
`getExpiresAt:90`. `source()` (satır 45–48) **`return 'auth.service';`** — yani
oturum olayını auth servisinin başlattığı interface üzerinden sabitlenmiştir.

**Gözlem (tasarım iddiası değil, kod gerçeği):** üç event de `payload()` içinde
`timestamp` anahtarını `occurredOn` üzerinden `format('c')` ile üretir;
`occurredOn()` methodu ayrıca `DateTimeImmutable` nesnesinin kendisini döner
(her üç dosyada satır 66–69 aralığı).

## §8 EventDispatcher — Mekanizma (Birebir)

Dosya: `shared/src/Events/EventDispatcher.php` / Satır: 119

`final class EventDispatcher implements EventDispatcherInterface` (satır 20);
`use Psr\EventDispatcher\EventDispatcherInterface;` (satır 14) ve
`use Psr\EventDispatcher\StoppableEventInterface;` (satır 15). Docblock birebir:
"Event Dispatcher implementation following PSR-14." (satır 18).

| Method | Satır | Davranış (birebir) |
|---|---|---|
| `dispatch(object $event): object` | 28–45 | event sınıf adıyla listener bulur; yoksa event'i olduğu gibi döner (32–34) |
| `addListener(string, callable, int $priority = 0)` | 50–59 | listener + priority ekler; sıralı önbelleği `unset` eder (58) |
| `removeListener(string, callable)` | 64–77 | `array_filter` ile `!==` karşılaştırması yapar (70–73) |
| `getListeners(string): array` | 82–96 | önbellek yoksa `sortListeners` çağırır (88–90) |
| `hasListeners(string): bool` | 101–104 | `!empty(...)` |
| `sortListeners(string)` (private) | 109–118 | `usort` + `$b['priority'] <=> $a['priority']` — büyük priority önce |

**Duraklama entegrasyonu (satır 37):** her listener çağrısından önce
`$event instanceof StoppableEventInterface && $event->isPropagationStopped()`
kontrol edilir; true ise döngü `break` eder. Yani duraklatma, bir sonraki
listener'dan önce devreye girer (mevcut listener tamamlanır, sonrakiler
çalışmaz).

## §9 StoppableEventTrait — Duraklatma (Birebir)

Dosya: `shared/src/Events/StoppableEventTrait.php` / Satır: 36

`trait StoppableEventTrait` (satır 17) tek property + 2 method:

| Üye | Satır | Davranış |
|---|---|---|
| `private bool $propagationStopped = false` | 19 | başlangıç değeri `false` |
| `isPropagationStopped(): bool` | 24–27 | alanı okur |
| `stopPropagation(): void` | 32–35 | alanı `true` yapar |

Hem 3 integration event hem — trait'in aday kullanıcısı olarak — diğer
event sınıfları bu trait'i `use` edebilir; diskte trait'i `use` eden
sınıflar 3 integration event ile sınırlıdır (grep 2026-10-13: `use
StoppableEventTrait` = 3 eşleşme, üçü de Integration/ altinda).

## §10 PSR-14 Bağımlılığı

| İddia | Kanıt | Durum |
|---|---|---|
| `psr/event-dispatcher` paketi bağımlılıklarda | `shared/composer.json:18` → `"psr/event-dispatcher": "^1.0"` | S1 doğrulandı |
| Dispatcher PSR-14 interface'ini uygular | `EventDispatcher.php:14,20` | S1 doğrulandı |
| PSR-14 sürüm/özellik uyumu ayrıntıları | — | ⚠️ VERIFICATION REQUIRED (kod dışı iddia) |
| Diğer composer.json'larda event-dispatcher bağımlılığı | grep 2026-10-13: `shared/composer.json` dışında eşleşme YOK | ölçüldü |

**Kural:** PSR-14 adı geçen her yerde, interface adları koddan okunur;
şablon/ürüne ait olmayan PSR-14 özelliği iddia edilmez (bu dosyada PSR-14
URL'si kullanılmamıştır — 2026-10-13 doğrulama listesinde PSR sayfası yoktur).

## §11 Domain Event Envanteri (Komşuluk — Sınır Amaçlı)

Dosya: `shared/src/Events/Domain/` (9 dosya, glob 2026-10-13); hepsi satır
20'de `final class ... implements DomainEventInterface`.

| # | Sınıf | Implement satırı |
|---|---|---|
| 1 | `UserRegisteredEvent` | 20 |
| 2 | `UserLoggedInEvent` | 20 |
| 3 | `UserLoggedOutEvent` | 20 |
| 4 | `PasswordResetRequestedEvent` | 20 |
| 5 | `GenderSetEvent` | 20 |
| 6 | `MusicPlayedEvent` | 20 |
| 7 | `MusicAddedEvent` | 20 |
| 8 | `PlaylistCreatedEvent` | 20 |
| 9 | `MediaAccessedEvent` | 20 |

**Sınır kuralı:** domain event'ler bu servisin **kapsamı değildir** — onlar
ilgili iş servislerinin (user/music/media) olaylarıdır; integration-service
yalnız `CoreMusic\Events\Integration\` altındaki 3 olayı ve bunların
sözleşmesini taşır. Kesişen tek nokta: ikisi de aynı `EventDispatcher`
mekanizmasını ve ADR-086 referansını paylaşır.

## §12 Repo Taraması — Üretim Kullanımı (Kanıt)

Tarama: 2026-10-13, tüm `*.php` (grep, bu dosyadaki tüm iddialar o tarihte
alınmış ölçümlerdir).

| Arama | Sonuç | Yorum |
|---|---|---|
| `new (AuthValidatedEvent\|NotificationEvent\|SessionCreatedEvent)` | **0 eşleşme** | 3 event hiçbir yerde örneklendirilmiyor |
| `use CoreMusic\Events\EventDispatcher` | **1 eşleşme** — `shared/tests/Events/EventDispatcherTest.php:7` | üretimde import YOK |
| `->dispatch(` üretim kodunda | Gateway/Router dispatch'leri (api.coremusic.net/index.php:182, PageRouterKernel.php:118) — **event dispatcher değil** | ad çakışması, kanıt sayılmaz |
| `addListener(` | yalnız `shared/tests/Events/EventDispatcherTest.php` | test kapsamı |
| Integration event testleri | `shared/tests/Events/` altında dosya **YOK** (yalnız `EventDispatcherTest.php`, `DomainEventTest.php`) | ⚠️ |

**Sonuç (ölçüm, yorum değil):** event altyapısı **tam olarak test edilmiş**
(`EventDispatcherTest`, `DomainEventTest`), fakat 3 integration event'in
tetiklendiği tek bir production veya test satırı **diskte yoktur**. Servisin
"çağrı yapan kodu yok" durumu, master §21.8'deki `Repo kanıtı: ⚠️
VERIFICATION REQUIRED` satırıyla uyumludur.

## §13 K013 İlişkisi — Çağrı Yönü

| İddia | Kaynak | Durum |
|---|---|---|
| "K013 kontratlarını çağıran servis" | master §21.8 (vault) | tasarım iddiası |
| "dış protokol yalnız adaptörde yaşar" | bagimliliklar §8.4 (vault) | tasarım kuralı |
| K013'ü çağıran production kodu | repo taraması | ⚠️ VERIFICATION REQUIRED — bulunamadı |
| K013 ile hata sınırı | index FM open_questions | ⚠️ VERIFICATION REQUIRED |
| Çağrı yönü (K012 → K013 mü, tersi mi) | index FM open_questions | ⚠️ VERIFICATION REQUIRED |

**Kural:** çağrı yönü ve hata sınırı bu dosyada **çözülmez**; iki open
question birlikte taşınır (index FM + §20).

## §14 K014 İlişkisi

master §21.8 "Giden bağımlılık: K013, K014" der. K010-network ile doğrudan
iletişim kuran bir kod parçası repo'da tespit edilmemiştir (§12 taraması
kapsamında); bu nedenle K014 bağımlılığı bu dosyada **iddia olarak** kalır →
⚠️ VERIFICATION REQUIRED. K014 dosyalarına yazım bu anayasa kapsamında
değildir (beyaz liste sınırı).

## §15 Event İsimlendirme Kuralı (Koddan Türetilmiş)

| Kural | Kanıt |
|---|---|
| Entegrasyon olayı adı `isim.eylem` biçiminde dotted string | `auth.validated` (S1:39) · `notification.sent` (S1:40) · `session.created` (S1:39) |
| `eventName()` interface ile zorunlu, `payload()` interface ile zorunlu | IntegrationEventInterface:24,38 |
| Domain olaylarında da `eventName()` zorunlu | DomainEventInterface:24 |
| Olay adının büyük/küçük harf standardı ya da ad kaydı (registry) | ⚠️ VERIFICATION REQUIRED — kayıt tablosu diskte YOK |

## §16 Servis Kartı Uyumu (master §21.8)

| Alan | master değeri | Bu dosyadaki karşılığı |
|---|---|---|
| Rol (ELI15) | Dış bağlantı servisi | §1 |
| Kapsam | K013 kontratlarını çağıran servis | §13 |
| Giden bağımlılık | K013, K014 | §13, §14 |
| Kural | dış protokol yalnız adaptörde (bagimliliklar §8.4) | §2 |
| Repo kanıtı | ⚠️ VERIFICATION REQUIRED | §12 — gerekçesiyle birlikte |

Uyumsuzluk **yoktur**; bu dosya master kartını genişletir, karşı çıkmaz.

## §17 Sınır Kararları (Bu Dosyanın Bağlayıcı Kuralları)

| # | Karar | Gerekçe |
|---|---|---|
| 1 | Protokol/adapter/hata K013'te kalır | master §2.2, §21.8 |
| 2 | 3 integration event bu servisin; 9 domain event değil | §4, §11 |
| 3 | `source()` zorunluluğu yalnız entegrasyon olaylarında | §3 interface farkı |
| 4 | Duraklatma trait üzerinden; dispatcher döngüsü listener öncesi kontrol | §8, §9 |
| 5 | Üretim tetikleyicisi olmadan "entegrasyon çalışıyor" denmez | §12 ölçümü |

## §18 Riskler

| # | Risk | Etki | Durum |
|---|---|---|---|
| 1 | Integration event'ler ölü kod halinde (0 tetikleyici) | sözleşme kaynar | açık — §12 |
| 2 | `NotificationEvent::source()` sabit; çok-kaynaklı kullanımda yanıltıcı | izleme/loğ | gözlem |
| 3 | Dispatcher production'da kayıtlı değil | olaylar hiçbir yerde yayılmaz | açık — §12 |
| 4 | Integration event testi yok | regresyon yakalanmaz | açık — §12 |
| 5 | K013 hata sınırı belirsiz | hata yayılımı | ⚠️ §13 |

## §19 Doğrulama Listesi

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | Interface 4 method sayımı | GEÇTİ (§3) |
| 2 | 3 event dosyası satır sayısı (86/104/94) | GEÇTİ (§4) |
| 3 | Event name değerleri birebir | GEÇTİ (§4–§7) |
| 4 | Dispatcher method envanteri (6 method) | GEÇTİ (§8) |
| 5 | Üretim tetikleyici taraması = 0 | GEÇTİ (§12) |
| 6 | Composer bağımlılık kanıtı | GEÇTİ (§10) |
| 7 | Open question'lar çözüldü mü | HAYIR — §20 |

## §20 Açık Sorular

| # | Soru | Kaynak | Durum |
|---|---|---|---|
| Q1 | K013 ile çağrı yönü ve hata sınırı | index FM | ⚠️ VERIFICATION REQUIRED |
| Q2 | Kademe-3 bounded-context kapsamı | index FM (faz C) | ⚠️ VERIFICATION REQUIRED |
| Q3 | Integration event tetikleyicisi nerede? | §12 | ⚠️ VERIFICATION REQUIRED |
| Q4 | K014 giden bağımlılığının kod kanıtı | master §21.8 | ⚠️ VERIFICATION REQUIRED |

Hiçbiri bu dosyada uydurma yanıtla kapatılmaz.

## §21 İddia Kayıt Defteri

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| Bu dosyada yeni URL'li dış iddia üretilmedi | — | 2026-10-13 | — |
| `psr/event-dispatcher ^1.0` | yerel dosya: shared/composer.json:18 | 2026-10-13 | S1 kanıt |

## §22 Örnek/Referans Ayrımı

| Öğe | Nerede | Bu dosyada |
|---|---|---|
| Event kod blokları (birebir) | ornekler dosyası | §4–§9 özet + satır referansı |
| Çağrı akışı/diyagramı | surucu dosyası | §12 ölçümü yeter |
| Kapsam/giriş | index.md | §1 bu dosyaya bağımlı |

## §23 Bağımlılık Özeti

| Bağımlılık | Tür | Kanıt |
|---|---|---|
| `psr/event-dispatcher` | composer | shared/composer.json:18 |
| `CoreMusic\Contracts\Events\{Domain,Integration}EventInterface` | yerel interface | shared/src/Contracts/Events/ |
| `CoreMusic\Events\StoppableEventTrait` | yerel trait | StoppableEventTrait.php:17 |
| K013-integraion kontratları | vault iddiası | master §21.8 ⚠️ |
| K010-network | vault iddiası | master §21.8 ⚠️ |

## §24 Kod Bloğu — Duraklama Akışı (Birebir Özet)

Dosya: `shared/src/Events/EventDispatcher.php` / Satır: 36–42 (dispatch döngüsü)

Döngü satır sırası ve anlamı:

| Satır | İfadе (birebir özgünleştirme yok — Türkçe açıklama) | Anlam |
|---|---|---|
| 36 | `foreach ($this->getListeners($eventName) as $listener) {` | sıralı listener listesi üzerinde tur |
| 37 | `$event instanceof StoppableEventInterface && $event->isPropagationStopped()` | duraklama kontrolü — tur başında |
| 38 | `break;` | durakladıysa kalan listener'lar çalışmaz |
| 41 | `$listener($event);` | listener çağrısı — argüman olayın kendisi |
| 44 | `return $event;` | döngü sonrası olay döner |

**Gözlem A (sıra):** kontrol, çağrıdan **öncedir** — ilk listener her zaman
çalışır; `stopPropagation()` diyen listener'ın **kendisi** tamamlanır, sıradaki
başlamaz. Bu sıralama bilgisi, §8 tablosundaki "mevcut listener tamamlanır"
iddiasını satır düzeyinde doğrular.

**Gözlem B (dönüş):** `dispatch` her koşulda aynı olay nesnesini döner
(satır 33 erken dönüş + satır 44); kopyalama yoktur.

**Gözlem C (sınıf anahtarı):** `get_class($event)` (satır 30) kullanılır —
yani olay adı interface/metod değil, **PHP sınıf adıdır**; `eventName()`
değeri dispatcher eşleştirmesinde kullanılmaz. Bu, §15'teki `eventName()`
kuralıyla **farklı** bir katmandır: `eventName()` sözleşme/lojik adı,
sınıf adı ise dispatch anahtarıdır.

## §25 Öncelik Sıralaması — Ölçüm

Dosya: `shared/src/Events/EventDispatcher.php` / Satır: 109–118

| Kural | İfade | Sonuç |
|---|---|---|
| Varsayılan priority | `int $priority = 0` (satır 50) | tüm listener'lar 0 ise sıra ekleme sırasıdır |
| Karşılaştırma | `$b['priority'] <=> $a['priority']` (satır 114) | azalan — büyük priority önce |
| Önbellek | `unset($this->sortedListeners[$eventName])` (satır 58, 76) | ekleme/silme sırayı kirletir |
| Önbellek dolumu | `getListeners` içinde (satır 88–90) | ilk okumada hesaplanır |
| Eşit priority kararlılığı | `usort` kararlı değil (PHP) | ⚠️ VERIFICATION REQUIRED — eşit priority'de sıra garantisi kodda yok |

## §26 Interface Karşılaştırma Tablosu

| Özellik | DomainEventInterface | IntegrationEventInterface |
|---|---|---|
| Dosya | shared/src/Contracts/Events/DomainEventInterface.php | shared/src/Contracts/Events/IntegrationEventInterface.php |
| Dosya satırı | 39 | 46 |
| Method sayısı | 3 | 4 |
| `eventName()` | var (satır 24) | var (satır 24) |
| `occurredOn()` | var (satır 31) | var (satır 45) |
| `payload()` | var (satır 38) | var (satır 38) |
| `source()` | YOK | var (satır 31) |
| ADR referansı | `@see ADR-086-event-driven-architecture` (satır 9) | `@see ADR-086-event-driven-architecture` (satır 9) |
| Uygulayan sınıf sayısı (repo) | 9 (§11) | 3 (§4) |

**İmza farkı tek maddedir:** `source()`. Bunun dışında iki interface aynı
üç methodu aynı sırayla taşır (okuma 2026-10-13).

## §27 Test Kanıtı — Ne Öğreniyor, Ne Öğrenmiyor

Dosya: `shared/tests/Events/EventDispatcherTest.php` (okundu — grep özeti)

| Test davranışı | Satır | Ne kanıtlar |
|---|---|---|
| `new EventDispatcher()` | 16 | sınıf doğrudan örneklenir (fabrika/DI yok) |
| `dispatch($event)` olayı geri döner | 23 | dönüş sözleşmesi |
| `addListener('test.event', ...)` | 30 | string olay adıyla kayıt da çalışır |
| priority sıralaması | 53–63 | 3 listener farklı priority, sıra doğrulaması |
| `hasListeners` | 79–80 | varlık kontrolü |
| 3 listener + 1 dispatch sayacı | 91–95 | çoklu listener çağrısı |

| Test **yok** | Durum |
|---|---|
| Integration event dispatch'i | ⚠️ dosya YOK |
| `removeListener` davranışı | ⚠️ bu dosyada tespit edilmedi |
| `stopPropagation` entegrasyonu (trait + dispatcher) | ⚠️ tespit edilmedi |
| `NotificationEvent`/`SessionCreatedEvent` payload doğrulaması | ⚠️ dosya YOK |

**Kural:** test varlığı, üretim kullanımını **kanıtlamaz** (§12 ile birlikte
okunmalı): dispatcher test teorisi sağlam, üretimde tetik yok.

## §28 Kayıp Alanlar (Bu Dosyanın Dışında Kalanlar)

| Alan | Neden dışarıda | Giden dosya |
|---|---|---|
| Çağrı akış diyagramı (surucu) | rol ayrımı | integration-service-surucu.md |
| Birebir kod blokları (tam dosya) | rol ayrımı | integration-service-ornekler.md |
| Kademe-3 kapsam genişletmesi | faz C bekliyor | ADR-097 K4 |
| K013 kontrat metinleri | başka K-düğümü | K013-integraion (beyazliste dışı) |
| K014 bağlantı ayrıntıları | başka K-düğümü | K010-network (beyazliste dışı, bu görevde ayrıca işleniyor) |

## §29 Bilgi Kaynakları ve Güven Sırası

| Sıra | Kaynak | Güven |
|---|---|---|
| 1 | S1 — repo kod okuması (2026-10-13) | en yüksek — birebir |
| 2 | S1 — composer.json (shared) | yüksek — dosya kanıtı |
| 3 | Vault — services-master.md §21.8 | tasarım iddiası |
| 4 | Vault — index.md open_questions | açık soru |
| 5 | Model hafızası | kullanılmadı (zero-hallucination) |

Çelişkide sıra 1 kazanır; §12'deki "üretim tetikleyicisi 0" ölçümü, vault
tasarım iddiasını **etkilemez** — ikisi farklı katmandır ve ikisi de
belgelenir.

## §30 Sık Sorulanlar (Bu Dosyanın Kapsamı İçinde)

| # | Soru | Yanıt | Kaynak |
|---|---|---|---|
| 1 | Bu servis HTTP konuşur mi? | Hayır — protokol K013'te | master §2.2, §21.8 |
| 2 | `eventName()` dispatch anahtarı mı? | Hayır — dispatch anahtarı `get_class` | §24 Gözlem C (EventDispatcher:30) |
| 3 | Entegrasyon olayı `source()` zorunlu mu? | Evet — interface methodu | IntegrationEventInterface:31 |
| 4 | Domain olayları da `source()` taşır mı? | Hayır | DomainEventInterface (3 method) |
| 5 | Duraklayınca mevcut listener iptal olur mu? | Hayır — o tamamlanır, sıradaki başlamaz | §8, §24 Gözlem A |
| 6 | Üretimde olay yayılıyor mu? | Yayılıyorluğu kanıtlanamadı — 0 tetikleyici | §12 |
| 7 | Öncelik eşitliğinde sıra garantisi var mı? | Kodda yok → ⚠️ VERIFICATION REQUIRED | §25 |
| 8 | Integration event'ler hangi servislere ait? | Bu servise (K012/integration-service) | §4 |
| 9 | Event'ler serialize ediliyor mu? | `payload()` array döner; serileştirme katmanı yok → ⚠️ | §3 |
| 10 | K013 kontratı diskte okundu mu? | Hayır — K013 bu görevin beyazlistesinde değil | §28 |

## §31 Sayaçlar (Ölçüm — 2026-10-13)

| Sayaç | Değer |
|---|---|
| Integration event dosyası | 3 |
| Domain event dosyası | 9 |
| Event interface dosyası | 2 |
| Dispatcher + trait dosyası | 2 |
| Toplam Events ağacı PHP dosyası | 14 (glob: 9 Domain + 3 Integration + dispatcher + trait) |
| Üretimde event dispatch çağrısı | 0 (ölçüm, §12) |
| Event test dosyası | 2 |
| Bu dosya § sayısı | 31 (§1–§31) |

## §32 Değişiklik Kaydı

| Tarih | Değişiklik | Not |
|---|---|---|
| 2026-10-12 | index.md iskeleti oluşturuldu (v2 §2.12) | ilk kayıt |
| 2026-10-13 | anayasa dosyası yazıldı — §1–§23; kod kanıtı S1 read (3 event + dispatcher + trait + 2 interface); üretim tetikleyici taraması = 0 | group mimari |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
