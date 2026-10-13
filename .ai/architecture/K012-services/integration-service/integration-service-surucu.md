---
node_id: K012-services/integration-service
node_type: module
role: surucu
status: draft
parent_id: K012-services/integration-service
children: []
depends_on:
  - "integration-service/index.md"
  - "integration-service-anayasa.md"
  - "services-master.md"
  - "services-bagimliliklar.md"
evidence:
  - "v2 §2.12 — çocuk adı kaynağı (birebir)"
  - "S1 — shared/src/Events/EventDispatcher.php 119 satır (addListener:50, dispatch:28)"
  - "S1 — shared/tests/Events/EventDispatcherTest.php (okuma özeti 2026-10-13)"
  - "S1 — shared/composer.json:18 psr/event-dispatcher ^1.0"
  - "S1 — grep 2026-10-13: EventDispatcher import = 1 (test), integration event new = 0"
validation:
  - "§3–§8 kod kanıtı 2026-10-13 read — birebir"
  - "§9 akış çizimleri ölçümlerden türetildi — uydurma adım yok"
  - "§12 sınır anayasa §13/§16 ile hizalı"
open_questions:
  - "K013 ile çağrı yönü ve hata sınırı → ⚠️ VERIFICATION REQUIRED"
  - "Dispatcher'ın production yaşam döngüsü (bootstrap kaydı) → ⚠️ VERIFICATION REQUIRED"
  - "Listener kaydının hangi süreçte yapılacağı → ⚠️ VERIFICATION REQUIRED"
date: 2026-10-13
updated: 2026-10-13
version: 1.0.0
authority: "SSOT: v2 §2.12 + shared/src/Events/ (S1, 2026-10-13 read)"
group: mimari
---

# integration-service — Sürücü (Driver)

> **Ön not:** bu dosya servisin **işleyişini** yazar: olaylar nasıl dispatch
> edilir, listener nasıl kaydedilir, öncelik nasıl sıralanır, dış dünyaya
> çıkış kapısı (K013) tasarımında nasıl çalışır. Anayasa dosyası (§ referanslı)
> **ne olduğu**, bu dosya **nasıl çalıştığı** üzerinedir. Her akış, diskteki
> kod ölçümüne dayanır; üretimde tetikleyici bulunmayan yollar açıkça
> "tasarım" etiketi taşır (anayasa §12).

## §1 ELI15 — Sürücü Ne Anlatır?

Bir olay geldiğinde sistemde **ne olur**: dispatcher sınıf adına bakar,
sıralı listener listesini çalıştırır, duraklama sinyali gelirse döngüyü
keser. Bu dosya bu makinenin kullanım kılavuzudur: kaydet (addListener),
gönder (dispatch), sırala (priority), durdur (stopPropagation). Ayrıca dış
dünyaya açılan kapının (K013 adaptörü) tasarım sürüşünü anlatır — kapı
kodda henüz sürülmüyor olabilir; o durumlar §8'de "tasarım" etiketilidir.

## §2 Sürücü Kural Tablosu

| # | Kural | Kaynak |
|---|---|---|
| 1 | Dispatch anahtarı PHP sınıf adıdır, `eventName()` değil | S1 EventDispatcher:30 |
| 2 | Listener öncelikli çalışır; büyük priority önce | S1:114 |
| 3 | Priority değişince sıralı önbellek düşer | S1:58, 76 |
| 4 | Duraklama bir sonraki listener'dan önce kontrol edilir | S1:37–39 |
| 5 | `dispatch` aynı nesneyi geri döner | S1:33, 44 |
| 6 | Kayıt/silme yalnız `addListener`/`removeListener` üzerinden | S1:50, 64 |
| 7 | Protokol konuşması bu serviste değil, K013'te | bagimliliklar §8.4 |

## §3 Olay Kaydı — addListener Sürüşü

Dosya: `shared/src/Events/EventDispatcher.php` / Satır: 50–59

İmza (birebir): `public function addListener(string $eventName, callable $listener, int $priority = 0): void`

| Adım | Satır | Eylem |
|---|---|---|
| 1 | 52–55 | `$this->listeners[$eventName][]` dizisine `['listener'=>..., 'priority'=>...]` eklenir |
| 2 | 58 | `unset($this->sortedListeners[$eventName])` — önbellek düşer |
| 3 | — | kayıt dönüş değeri yoktur (void) |

**Sürüş notları:**

- `string $eventName` hem sınıf adı hem düz metin kabul eder; testte
  `'test.event'` (EventDispatcherTest:30) ve `UserLoggedInEvent::class`
  (EventDispatcherTest:53) birlikte kullanılmıştır — iki biçim de çalışır.
- Aynı listener iki kez kaydedilebilir; ikinci kez iki kez çağrılır
  (kayıt deduplication'ı yoktur — kodda kontrol bulunmaz, okuma 2026-10-13).
- Kayıt sırası, **eşit priority** durumunda görünür sıra olur; fakat `usort`
  kararlılığı garanti değildir (anayasa §25 ⚠️).

## §4 Olay Gönderimi — dispatch Sürüşü

Dosya: `shared/src/Events/EventDispatcher.php` / Satır: 28–45

| Adım | Satır | Eylem | Hata/iş durumu |
|---|---|---|---|
| 1 | 30 | `$eventName = get_class($event)` | sınıf adı anahtar |
| 2 | 32–34 | kayıt yoksa `$event` döner | sessiz — istisna yok |
| 3 | 36 | `getListeners` ile sıralı liste alınır | önbellek dolabilir |
| 4 | 37–39 | duraklama kontrolü → `break` | kalan listener'lar atlanır |
| 5 | 41 | `$listener($event)` | istisna yayılırsa dispatch kesilir → ⚠️ (try/catch yok, okuma 2026-10-13) |
| 6 | 44 | `return $event` | aynı nesne |

**Güçlü ifade (kod kanıtlı):** dispatcher içinde `try/catch` **yoktur**;
listener'ın fırlattığı istisna dispatch'i durdurur ve yukarıya yayılır.
Yakalama stratejisi dinleyici tarafının sorumluluğudur → bu davranış
`shared/src/Events/EventDispatcher.php:28–45` okumasından türetilmiştir,
kod dışı varsayım eklenmemiştir.

## §5 Silme Sürüşü — removeListener

Dosya: `shared/src/Events/EventDispatcher.php` / Satır: 64–77

| Adım | Satır | Eylem |
|---|---|---|
| 1 | 66–68 | olay adına kayıt yoksa sessiz çıkış |
| 2 | 70–73 | `array_filter` + `fn($entry) => $entry['listener'] !== $listener` |
| 3 | 76 | önbellek `unset` |

**Davranış:** aynı callable iki kez kaydedildiyse `!==` karşılaştırması
**ikisini de** kaldırır (filter tüm eşleşenleri eler) — kod okumasından
türetilen gözlem (satır 70–73). Test kanıtında `removeListener` kullanımı
tespit edilmedi (anayasa §27) → davranış test edilmemiş → ⚠️.

## §6 Okuma Sürüşü — getListeners / hasListeners

Dosya: `shared/src/Events/EventDispatcher.php` / Satır: 82–104

| Method | Satır | Sürüş |
|---|---|---|
| `getListeners(string): array` | 82–96 | kayıt yoksa `[]`; önbellek boşsa `sortListeners` (88–90); `array_map` ile yalnız callable listesi döner (92–95) |
| `hasListeners(string): bool` | 101–104 | `!empty($this->listeners[$eventName])` |
| `sortListeners(string): array` (private) | 109–118 | kopya üzerinde `usort`, azalan priority |

**Önbellek akışı:**

```
getListeners(A)
  → listeners[A] yok mu?  → evet → [] dön
  → sortedListeners[A] yok mu? → evet → sortListeners(A) → önbelleğe yaz
  → array_map ile callable'ları ayıkla → dön

addListener(A, ...) / removeListener(A, ...)
  → unset(sortedListeners[A])   ← önbellek düşer, bir sonraki okumada yeniden sıralanır
```

(Yukarıdaki çizim, satır 32/58/76/88–90 okumalarından türetilmiştir —
uydurma adım yoktur.)

## §7 Duraklama Sürüşü — Trait + Dispatcher Birlikteliği

| Parça | Dosya / Satır | Rol |
|---|---|---|
| `stopPropagation()` | StoppableEventTrait.php:32–35 | sinyali yayar |
| `isPropagationStopped()` | StoppableEventTrait.php:24–27 | sinyali okur |
| kontrol | EventDispatcher.php:37 | `instanceof StoppableEventInterface` + okuma |
| kesme | EventDispatcher.php:38 | `break` |

**Sürüş sırası (olay bazında):**

1. Event nesnesi dispatch edilir (sınıf adı anahtarlanır).
2. Sıralı listener listesi döngüye girer.
3. **Her tur başında** duraklama kontrolü yapılır.
4. `stopPropagation()` diyen listener tamamlanır; sıradaki turda `break`.
5. Döngü biter, olay döner.

**Kapsam notu:** trait `bool` alanını olay nesnesinde tutar; olay nesnesi
`readonly` property'lere rağmen duraklama alanını **mutable** tutar (trait
satır 19). Bu, olay nesnesinin paylaşıldığı tek süreçte güvenlidir; süreçler
arası paylaşım → ⚠️ VERIFICATION REQUIRED (kod kanıtı yok).

## §8 Dış Kapı Sürüşü (K013) — Tasarım Akışı

Bu bölüm **tasarımdır** — üretim kodu §10'da ölçüldüğü gibi 0 tetikleyicidir.

Dosya: `services-bagimliliklar.md` / Satır: 255–261 (§8.4)

| Adım | Tasarım | Sahip |
|---|---|---|
| 1 | Olay oluşur / iş çağrısı gelir | K012 servisi |
| 2 | integration-service kontratı kullanır | K012/integration-service |
| 3 | Adaptör | K013-integraion |
| 4 | Dış sistem | dış dünya |

bagimliliklar §8.4 (birebir özet): "Sahip: K013-integraion · Geçiş:
integration-service → adaptör → dış sistem". Çağrı yönü ve hata sınırı
açık sorudur (index FM) → bu sürüşte **hangi yöne** hata döndüğü yazılmaz.

## §9 Yaşam Döngüsü — İki Durum

| Durum | Akış | Kanıt |
|---|---|---|
| **A — Test** (bugün) | `new EventDispatcher()` → `addListener` × n → `dispatch(event)` → sayaçlar artar | EventDispatcherTest:16, 30, 46 |
| **B — Tasarım** (hedef) | bootstrap dispatcher kaydı → servis olay üretir → dispatcher yayınlar → dinleyici servisler | ⚠️ VERIFICATION REQUIRED |

**Durum A ayrıntısı (testten):**

| Test akışı | Satır | Sonuç |
|---|---|---|
| tek listener + tek dispatch | 46 | sayaç 1 |
| 3 listener farklı priority | 53–63 | sıra öncelik sırasına uyar |
| 3 listener aynı çağrı | 91–95 | 3 çağrı |
| kayıt yokken dispatch | 23 (çoğu) | istisna yok, olay döner |

**Durum B'nin eksik halkaları (ölçüm):**

| Halka | Durum |
|---|---|
| Production bootstrap'ta dispatcher kaydı | ⚠️ — `use CoreMusic\Events\EventDispatcher` üretimde 0 (anayasa §12) |
| Integration event üreten servis | ⚠️ — `new ...Event` = 0 |
| Dinleyen servis | ⚠️ — `addListener` üretimde 0 |

## §10 Production Sürüş Tablosu (Ölçüm)

Tarama: 2026-10-13, tüm `*.php`.

| Sürüş noktası | Arama | Üretim eşleşmesi | Test eşleşmesi |
|---|---|---|---|
| Dispatcher oluştur | `new EventDispatcher` | 0 | 1 (EventDispatcherTest:16) |
| Listener kaydet | `addListener(` | 0 | çok (test) |
| Olay gönder | `->dispatch($event)` (event türü) | 0 | test |
| Integration event üret | `new AuthValidatedEvent\|NotificationEvent\|SessionCreatedEvent` | 0 | 0 |
| Domain event üret | `new UserLoggedInEvent\|UserRegisteredEvent` | 0 | 6 (DomainEventTest + EventDispatcherTest) |

**Sonuç:** sürüş makinesi sağlam ve testli; **gaz (tetik) pedalı basılmıyor**.
Bu, servisin yanlış çalıştığı anlamına gelmez — henüz hiç çalıştırılmadığı
anlamına gelir ve anayasa §12 ile aynı ölçümdür.

## §11 Öncelikli Dinleyici Tablosu (Tasarım — Ad Sayılmaz)

> Bu tablo **kodda var olan** bir listener listesi değildir; kodda listener
> kaydı 0'dır (§10). Tablo, sahip ataması yapılmayan bir boşluk envanteridir.

| Olası olay | Olası dinleyici | Durum |
|---|---|---|
| `auth.validated` | bilinmiyor | ⚠️ UNKNOWN |
| `notification.sent` | bilinmiyor | ⚠️ UNKNOWN |
| `session.created` | bilinmiyor | ⚠️ UNKNOWN |
| `UserLoggedInEvent` | bilinmiyor | ⚠️ UNKNOWN |

Dinleyici adı **uydurulmaz**; boşluk boş bırakılır.

## §12 Sınır — Bu Sürücüde Olmayanlar

| Konu | Neden yok | Giden yer |
|---|---|---|
| K013 kontrat detayı / HTTP | protokol sahipliği K013 | K013-integraion (beyazliste dışı) |
| Adaptör uygulaması | K013 | K013-integraion |
| Ağ/timeout/retry | K014 | K014-network (bu görevde ayrı işlenir) |
| Event tanım kodu (birebir) | rol | anayasa §4–§9 + ornekler |
| Kademe-3 BC kapsamı | faz C | ADR-097 K4 |

## §13 Sürüş Kuralları (Bağlayıcı)

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Olay gönderirken sınıf adı anahtarı unutulmaz | `get_class` (S1:30) |
| 2 | Priority yalnız 0 dışına çıkıyorsa anlam taşır | varsayılan 0 (S1:50) |
| 3 | Listener istisna fırlatırsa dispatch yarım kalır | try/catch yok (S1:28–45) |
| 4 | Duraklama `break` ile sonuçlanır — kalan listener'lar sessiz atlanır | S1:37–39 |
| 5 | Protokol/hata sınırı K013'e bırakılır | bagimliliklar §8.4, master §2.2 |
| 6 | §8/§11 tabloları tasarım etiketli okunur | §10 ölçümü |

## §14 Test Sürüşü — Tekrar Koşu

| # | Koşu | Adım | Beklenen | Kanıt |
|---|---|---|---|---|
| 1 | dispatch dönüş | dispatch → gelen nesne | aynı nesne | EventDispatcherTest:23 |
| 2 | öncelik sırası | 3 kayıt farklı priority → dispatch | azalan sıra | :53–63 |
| 3 | çoklu çağrı | 3 aynı → dispatch | 3 sayaç | :91–95 |
| 4 | boş kayıtlı dispatch | dispatch | istisna yok | :23 |
| 5 | integration event testi | — | **yok** | anayasa §27 ⚠️ |

Bu koşuların hiçbiri bu vault dosyasında çalıştırılmaz; yalnız kod okumasıyla
varlıkları raporlanır (çalıştırma komutu verilmez, sonuç uydurulmaz).

## §15 Hata Sınırı — Bilinen / Bilinmeyen

| Senaryo | Davranış | Bilgi düzeyi |
|---|---|---|
| Listener istisna fırlatır | dispatch kesilir, istisna yayılır | kod kanıtı (try/catch yok) |
| Kayıt olmayan sınıfa dispatch | sessiz olay dönüşü | kod kanıtı (S1:32–34) |
| `removeListener` bilinmeyen callable | sessiz, değişiklik yok | kod kanıtı (S1:66–68) |
| Dış sistem hatası (K013) | bilinmiyor | ⚠️ VERIFICATION REQUIRED (açık soru) |
| Çift dispatch (aynı olay nesnesi) | duraklama alanı kalıcı olabilir | gözlem — trait satır 19 mutable |

## §16 K014 ile Sürüş İlişkisi

| İddia | Durum |
|---|---|
| master §21.8: giden bağımlılık K013 + K014 | tasarım iddiası |
| K014'e bağlanan üretim kodu | ⚠️ VERIFICATION REQUIRED — §10 taramasında yok |
| Ağ katmanı sorumluluğu K014 | kural (beyazliste: K014 ayrı düğüm) |

## §17 Gözlem Defteri

| # | Gözlem | Kaynak |
|---|---|---|
| 1 | Test ve üretim kullanımı arasındaki uçurum (test 1, üretim 0) | §10 |
| 2 | `removeListener` test edilmemiş | anayasa §27 |
| 3 | Listener istisna yönetimi yok | §4 |
| 4 | Eşit priority kararlılığı garantisiz | anayasa §25 |
| 5 | `payload()` serileştirme katmanı yok | anayasa §29–§30 |

## §18 Riskler (Sürüş Açısından)

| # | Risk | Etki | Durum |
|---|---|---|---|
| 1 | İlk üretim tetikleyicisi yazıldığında kayıt sırası denenmemiş olur | sürpriz sıra | açık |
| 2 | Listener hatası tüm yayını keser | zincirleme kesinti | açık (§4) |
| 3 | Tasarım tabloları gerçek sanılır | yanlış envanter | §11 etiketi korunur |
| 4 | K013 hata sınırı belirsiz | hata yayılımı | ⚠️ §15 |

## §19 Doğrulama Listesi

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | Her sürüş adımı satır referanslı mı | GEÇTİ (§3–§7) |
| 2 | Tasarım/kod ayrımı etiketli mi | GEÇTİ (§8, §9, §11) |
| 3 | Uydurma listener adı var mı | HAYIR (§11 UNKNOWN) |
| 4 | Production ölçümü anayasa §12 ile tutarlı mı | GEÇTİ (§10) |
| 5 | Açık sorular çözüldü mü | HAYIR → §20 |

## §20 Açık Sorular

| # | Soru | Durum |
|---|---|---|
| Q1 | K013 ile çağrı yönü ve hata sınırı | ⚠️ VERIFICATION REQUIRED |
| Q2 | Bootstrap'ta dispatcher kaydı ne zaman yapılacak | ⚠️ VERIFICATION REQUIRED |
| Q3 | İlk listener'ları hangi servisler taşıyacak | ⚠️ VERIFICATION REQUIRED |

## §21 İddia Kayıt Defteri

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| Bu dosyada yeni URL'li dış iddia üretilmedi | — | 2026-10-13 | — |
| psr/event-dispatcher ^1.0 | yerel: shared/composer.json:18 | 2026-10-13 | S1 kanıt |

## §22 İlişki Matrisi

| İlişki | Yön | Durum |
|---|---|---|
| anayasa ↔ surucu | tamamlayıcı | bu dosya anayasa §1–§23'e atıf yapar |
| surucu ↔ ornekler | özet ↔ birebir kod | ornekler dosyası blokları taşır |
| surucu ↔ index | children | index children bu dosyayı içerir |
| K013 | dış kapı | ⚠️ tasarım (§8) |
| K014 | dış ağ | ⚠️ (§16) |

## §23 Bağımlılık Özeti

| Bağımlılık | Tür | Kanıt |
|---|---|---|
| psr/event-dispatcher | composer | shared/composer.json:18 |
| EventDispatcher + trait + 2 interface | yerel | shared/src/Events/, Contracts/Events/ |
| services-master §21.8 | vault | kart |
| services-bagimliliklar §8.4 | vault | dış kapı |
| index.md open_questions | vault | FM |

## §24 Özet Sayılar

| Sayaç | Değer |
|---|---|
| Sürüş methodu (dispatcher) | 6 |
| Trait üye sayısı | 3 |
| Test dosyası | 2 (EventDispatcherTest, DomainEventTest) |
| Production sürüş eşleşmesi | 0 (§10) |
| Açık soru | 3 (§20) |
| Bu dosya § sayısı | 35 (§1–§35) |

## §25 Senaryo Tabloları (Sürüş Vaka Anları)

| # | Senaryo | Giriş | Sürüş sonucu | Kaynak satır |
|---|---|---|---|---|
| V1 | Tek listener, duraklama yok | dispatch(A) | 1 çağrı, olay döner | EventDispatcher:41, 44 |
| V2 | 3 listener, priority 10/0/5 | dispatch(A) | çağrı sırası 10 → 5 → 0 | :114 |
| V3 | Listener 2 `stopPropagation()` der | dispatch(A) | L1 tamam, L2 tamam, L3 atlanır | :37–38 |
| V4 | Kayıt yokken dispatch | dispatch(A) | istisna yok, olay döner | :32–34 |
| V5 | Aynı listener iki kez kayıtlı | dispatch(A) | iki kez çağrılır | :52–55 |
| V6 | Kayıttan sonra priority değişimi | addListener → getListeners | önbellek yeniden hesaplanır | :58, 88–90 |
| V7 | `removeListener` bilinmeyen callable | removeListener(A, x) | sessiz, liste değişmez | :66–68 |
| V8 | `removeListener` iki kez kayıtlı callable | removeListener(A, x) | iki kayıt da elenir | :70–73 |
| V9 | Listener istisna fırlatır | dispatch(A) | zincir kesilir, istisna yayılır | try/catch yok |
| V10 | Integration event dispatch | — | **çalıştırılmamış** — tetik yok | anayasa §12 |

## §26 Ağırlık ve Öncelik Hesabı (Örnek — Sıradan Sayım)

| Durum | Kayıtlar | Beklenen çağrı sırası (kural :114) |
|---|---|---|
| Hepsi 0 | L1, L2, L3 | kural: azalan priority — eşitlikte sıra `usort` garantisi yok → ⚠️ |
| 5 / 0 / 5 | L1=5, L2=0, L3=5 | 5 grubu önce; iki 5'in iç sırası garantisiz → ⚠️ |
| -1 / 0 | L1=-1, L2=0 | L2 önce (0 > -1) |
| 100 / 100 | iki listener | eşit — sırası garantisiz → ⚠️ |

**Kural:** yalnızca **farklı** priority değerleri arasında sıralama garantisi
koddan okunur (`<=>`); eşitlik durumları hiçbir yerde sabitlenmemiştir.

## §27 Önbellek Ayrışma Noktaları

| # | İşlem | Önbellek etkisi | Satır |
|---|---|---|---|
| 1 | ilk `getListeners(A)` | hesapla, yaz | 88–90 |
| 2 | ikinci `getListeners(A)` | kullan | 88 (`isset` true) |
| 3 | `addListener(A,...)` | düşür | 58 |
| 4 | `removeListener(A,...)` | düşür | 76 |
| 5 | `getListeners(B)` (farklı ad) | A'yı etkilemez | anahtar ayrı |
| 6 | dispatch — doğrudan önbellek erişimi yok | `getListeners` üzerinden | 36 |

## §28 Observer Kalıbı — Bu Koddaki Karşılığı

| Observer kavramı | Bu koddaki karşılığı | Satır |
|---|---|---|
| Subject (konu) | dispatch edilen olay nesnesi | 28 |
| Observer (gözlemci) | `callable $listener` | 50 |
| kayıt | `addListener` | 50–59 |
| kaldırma | `removeListener` | 64–77 |
| bildirim | `dispatch` döngüsü | 36–42 |
| sıralama anahtarı | `priority` | 50, 114 |
| iptal | `stopPropagation` (trait) | 37–38 |

**Kalıp adı koddan çıkmaz:** dosya docblock'u yalnız "Event Dispatcher
implementation following PSR-14." der (satır 18); "Observer" adı bu tabloda
**eşleme** olarak kullanılır, kod içi iddia olarak değil.

## §29 Sürüş Eksenleri Özeti

| Eksen | Durum | Bölüm |
|---|---|---|
| Kaydetme ekseni | kodda tam, testli | §3, §14 |
| Gönderme ekseni | kodda tam, testli | §4, §14 |
| Sıralama ekseni | kodda tam, testli (eşitlik hariç) | §6, §26 |
| Duraklama ekseni | kodda tam | §7 |
| Dış kapı ekseni | yalnız tasarım | §8, §16 |
| Tetik ekseni (üretim) | boş — 0 | §10 |

## §30 Bilgi Kaynakları

| Sıra | Kaynak | Güven |
|---|---|---|
| 1 | S1 kod okuması (EventDispatcher 119 satır, trait 36, testler) | en yüksek |
| 2 | S1 composer.json | yüksek |
| 3 | Vault — anayasa §12 (bu dosyanın bağımlısı) | ölçüme dayalı |
| 4 | Vault — master/bagimliliklar/index | tasarım |
| 5 | Model hafızası | kullanılmadı |

## §31 Sık Yapılan Hatalar (Sürüş Aynası)

| # | Hata | Sonuç | Doğru davranış |
|---|---|---|---|
| 1 | `eventName()` değerini dispatch anahtarı sanmak | listener hiç çalışmaz | anahtar `get_class` (satır 30) |
| 2 | Priority'yi sıralama türü sanmak | ters sıra | `<=>` azalan (satır 114) |
| 3 | `dispatch` sonucunu yeni nesne sanmak | gereksiz kopya | aynı nesne döner (satır 44) |
| 4 | Duraklamayı "önceki iptal" sanmak | yanlış beklenti | mevcut tamamlanır (§7) |
| 5 | `removeListener` ile tek kayıt silme beklentisi | ikisi de silinir | §5 Gözlem |
| 6 | Üretimde yayıldığını varsaymak | yanlış envanter | §10: 0 tetik |
| 7 | Listener istisnasının yakalanacağını varsaymak | yayılır | §4: try/catch yok |

## §32 Kıyaslama — Benzer Ama Farklı Mekanizmalar

| Mekanizma | Fark | Bu sürücüdeki karşılığı |
|---|---|---|
| PSR-14 dispatcher | interface Uygulaması kanıtlanır (satır 14, 20) | §1 |
| Observer kalıbı | eşleme — kod adı değil (§28) | §28 |
| Middleware zinciri | sıralı çalışma benzer; middleware veri taşır, listener olay taşır | §6 |
| Job/queue kuyruğu | kalıcılık + yeniden deneme yok | §15 (istisna yayılır) |
| Signal/slot | ad kaydı yok — doğrudan sınıf adı anahtarı | §4 |
| Domain event yayını | aynı dispatcher, farklı interface | anayasa §26 |

**Kural:** bu karşılaştırmalar **eşleme** olarak kullanılır; hiçbirinin
bu repo'da var olduğu iddia edilmez. Kuyruk/yeniden-deneme mekanizması
diskte gözlenmemiştir → §15.

## §33 Parça Envanteri (Sürücü Kapsamındaki Dosyalar)

| # | Dosya | Satır | Sürücüdeki rolü |
|---|---|---|---|
| 1 | shared/src/Events/EventDispatcher.php | 119 | ana makine (§3–§6) |
| 2 | shared/src/Events/StoppableEventTrait.php | 36 | duraklama (§7) |
| 3 | shared/src/Contracts/Events/IntegrationEventInterface.php | 46 | olay sözleşmesi |
| 4 | shared/src/Contracts/Events/DomainEventInterface.php | 39 | komşu sözleşme (karşılaştırma) |
| 5 | shared/src/Events/Integration/AuthValidatedEvent.php | 86 | olay taşıyıcı |
| 6 | shared/src/Events/Integration/NotificationEvent.php | 104 | olay taşıyıcı |
| 7 | shared/src/Events/Integration/SessionCreatedEvent.php | 94 | olay taşıyıcı |
| 8 | shared/tests/Events/EventDispatcherTest.php | — | sürüş testi (§14) |
| 9 | shared/tests/Events/DomainEventTest.php | — | domain event testi |

## §34 Test Dosyası Satır Eşlemesi (Okuma Özeti)

Dosya: `shared/tests/Events/EventDispatcherTest.php` (okundu 2026-10-13 — özet eşleme)

| Satır aralığı | İçerik | Sürücü bağlantısı |
|---|---|---|
| 7 | `use CoreMusic\Events\EventDispatcher;` | tek import (anayasa §12) |
| 16 | `$this->dispatcher = new EventDispatcher();` | §9 Durum A |
| 23 | `$result = $this->dispatcher->dispatch($event);` | §14/1 |
| 30 | `addListener('test.event', $listener)` | §3 düz metin anahtar |
| 39 | `addListener(` ikinci biçim | §3 sınıf adı anahtarı |
| 45–46 | dispatch + assert | §14/1 |
| 53–63 | 3 listener + priority sırası | §14/2, §26 |
| 70, 79–80 | kayıt/silme + hasListeners | §5, §6 |
| 91–95 | 3 aynı listener + tek dispatch | §14/3 |

**Not:** dosyada satır numaraları bu okumanın eşlemesidir; dosyanın tam metni
bu vault dosyasına kopyalanmaz (birebir bloklar ornekler dosyasının
kapsamındadır — rol ayrımı).

## §35 Değişiklik Kaydı

| Tarih | Değişiklik | Not |
|---|---|---|
| 2026-10-13 | surucu dosyası yazıldı — §1–§25; dispatcher sürüş adımları satır referanslı; production = 0 ölçümü anayasa §12 ile hizalı | group mimari |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
