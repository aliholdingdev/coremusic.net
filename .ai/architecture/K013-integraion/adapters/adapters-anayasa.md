---
node_id: K013-integraion/adapters/adapters-anayasa
node_type: technology-deepdive
title: "Adaptör Anayasa — Port/Adapter, Anti-Corruption Layer, Kapsam-Sınır ve Desen Envanteri"
type: technology-deepdive
status: draft
date: 2026-10-16
parent_id: K013-integraion/adapters
children: []
depends_on:
  - K013-integraion/adapters
  - K013-integraion
  - K000–K5000 Architecture Registry
evidence:
  - "https://alistair.cockburn.us/hexagonal-architecture/ (2026-10-16 HEAD 200 · içerik çekildi)"
  - "https://learn.microsoft.com/en-us/azure/architecture/patterns/anti-corruption-layer (2026-10-16 HEAD 200 · içerik çekildi)"
  - "https://docs.aws.amazon.com/prescriptive-guidance/latest/cloud-design-patterns/acl.html (2026-10-16 HEAD 200 · içerik çekildi)"
  - "https://docs.aws.amazon.com/prescriptive-guidance/latest/cloud-design-patterns/hexagonal-architecture.html (2026-10-16 HEAD 200 · içerik çekildi)"
  - "https://learn.microsoft.com/en-us/azure/architecture/patterns/circuit-breaker (2026-10-16 HEAD 200 · içerik çekildi)"
  - "https://refactoring.guru/design-patterns/adapter (2026-10-16 HEAD 200 · içerik çekildi)"
  - "https://refactoring.guru/design-patterns/facade (2026-10-16 HEAD 200 · içerik çekildi)"
  - "Dosya: shared/src/Cache/CacheInterface.php · ApcuAdapter.php · MemoryAdapter.php · PageCacheAdapter.php (2026-10-16 dizin ölçümü)"
  - "Dosya: shared/src/OAuth/Provider/IOAuthProvider.php · BaseOAuthProvider.php + 10 concrete provider (2026-10-16 dizin ölçümü)"
validation:
  - "Her iddia §Kaynak Tablosu'nda URL ile eşleştirilmiştir (2026-10-16 HEAD + içerik çekimi)"
  - "Repo iddiaları dosya yolu ile verilmiştir ve 2026-10-16 diskten doğrulanmıştır"
  - "Uydurma desen adı, sürüm, dosya adı yok; doğrulanamayan satır ⚠️ VERIFICATION REQUIRED / UNKNOWN işaretlidir"
open_questions:
  - "CoreMusic'te dış sistem başına kaç adaptör yazılacak? → ⚠️ VERIFICATION REQUIRED (envanter §4, faz C)"
  - "Adaptörler K013 altında mı, K012 integration-service içinde mi barınacak? → ⚠️ VERIFICATION REQUIRED (ADR bekliyor)"
  - "Circuit breaker / retry CoreMusic'te hangi katmanda devreye giriyor? → UNKNOWN (karar yok)"
  - "OAuth provider ailesi bir adaptör olarak mı sınıflandırılacak? → UNKNOWN (ADR-088 kapsamı okunmadı)"
group: mimari
---

# Adaptör Anayasa — Port/Adapter · ACL · Kapsam-Sınır · Desen Envanteri

> **Bu dosya ne değildir:** Kod rehberi, kütüphane kurulumu veya dış sistem
> envanteri değildir. Bu dosya **adaptörün kimliğini ve sınırını** tanımlar:
> nedir, K013 ağacında nerede durur, hangi desenlerle anılır, hangi
> komşuya girmez. Tanımlar `alistair.cockburn.us` / `learn.microsoft.com` /
> `docs.aws.amazon.com` / `refactoring.guru` sayfalarından alınmıştır.
>
> **Durum etiketi:** ŞARTNAME/desen IMPLEMENTED · CoreMusic dış-sistem
> adaptörü ⚠️ PLANNED (repo'da açık adıyla "anti-corruption layer" sınıfı
> bu turda tespit edilmedi — bkz. §7).

---

## §0 ELI15 — Adaptör bir cümlede

Adaptör, **iç dünyanın (CoreMusic modeli)** ile **dış dünyanın (üçüncü
taraf sistem modeli)** arasında duran ve iki dili birbirine çeviren
katmandır: dış sistem ne gönderirse göndersin, içeri iç modelin anladığı
dille girer; iç model ne isterse istesin, dışarı dış sistemin anladığı
dille çıkar — ve **çeviri dışında hiçbir şey yapmaz**.
(Kaynak: §Kaynak Tablosu [A1], [A2], [A5])

---

## §GRUPLAR

> ADR-097 §5.5 · registry §8.1 · `group` = frontmatter 9. alan.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | 2 |
| mimari | `adapters-anayasa.md` (bu dosya) | Kimlik, kapsam-sınır, K013 içi rol, desen envanteri | 3 |
| mimari | `adapters-sozlesme.md` | İç model ↔ dış sistem sözleşmesi: çeviri, tip, hata, versioning | 3 |
| kod | `adapters-dogrulama.md` | Kontrat testi, sahte sunucu, kabul testi, CI kapısı | 3 |
| kod | `adapters-kod-ornekleri.md` | 12+ dilde port + concrete adaptör + hata eşleme örnekleri | 3 |
| kanit | `adapters-web-arastirma.md` | ≥12 benzersiz resmi URL + iddia/URL/tarih/durum | 3 |

**Gruplama kuralı:** registry §8.1 + ADR-097 §5.5 — `group: mimari | kod | kanit`
alanı her dosyanın frontmatter'ında zorunludur.

---

## §1 Adaptör Kimlik Kartı

| Alan | Değer | Kanıt |
|---|---|---|
| Desen adı | Adapter (ayrıca: Wrapper) | [A5] |
| Yapısal aile | GoF yapısal desenleri (Adapter, Facade) | [A5], [A6] |
| Mimarî ad | Ports & Adapters = Hexagonal Architecture | [A1], [A3] |
| İlk teknik rapor | Alistair Cockburn, HaT Technical Report 2005.02, 2005-09-04 | [A1] |
| ACL (Anti-Corruption Layer) | Eric Evans, *Domain-Driven Design* (2004) içinde tanımlandı | [A2] |
| ACL niyeti (alıntı) | "Create an isolating layer to provide clients with functionality in terms of their own domain model…" | [A13] |
| Circuit breaker | Michael Nygard — *Release It* ile popülerleşti | [A8] |
| CoreMusic sahibi agent | Backend Architect (A2 katmanı) — adaptör kodu `shared/src/` | `.ai/AGENTS.md` §4/§15 |
| CoreMusic routing | "API, endpoint, routing, middleware, PHP" → Backend (primary) | `.ai/AGENTS.md` §6 |
| Bağlandığı K-düğümü | `K013-integraion` — "Dış dünya ile köprü" | v2 §2.13 |

---

## §2 Kapsam–Sınır Tablosu (bu dosya neyi kapsar, neyi KAPSAMAZ)

| Yüzey | Nerede | Adaptörle ilişkisi | Kanıt |
|---|---|---|---|
| **Adaptör (bu düğüm)** | `K013-integraion/adapters` | Çeviri katmanının kendisi | v2 §2.13 |
| Çağıran (iş kuralı) | `K012` / integration-service | Adaptörü **çağırır**, adaptör onun adına konuşur | v2 §2.13 (sınır) |
| Taşıma (HTTP/TLS/socket) | `K014-network` | Adaptörün **altındaki** katman; adaptör taşımayı uydurmaz | v2 §2.13 (sınır) |
| Secret / kimlik | `K009-security` | API anahtarı, token, imzalama — adaptör **üretmez**, K009'dan alır | v2 §2.13 (sınır) |
| Dış sistem envanteri | `K013/external-systems` | "Hangi sistem var" sorusu — adaptör bunu **listelemez** | adapters/index.md §Sınır |
| Protokol çevrimi | `K013/protocol-adapters` | gRPC/GraphQL vb. protokol dönüşümü — ayrı çocuk | adapters/index.md §Sınır |
| Olay sözleşmesi | `K013/event-contracts` | Webhook/olay şeması — adaptörün veri sözleşmesi ayrı | kardeş düğüm |
| Hata biçimi | `K013/error-handling` | Adaptör **hata eşler**, hata modelini **icat etmez** | kardeş düğüm |
| Uyum (sync) | `K013/synchronization` | Zamanlama/çakışma — adaptör değil | kardeş düğüm |
| Kontrat testi | `K013/integration-testing` | Adaptörün doğrulama kapısı buraya bağlanır | kardeş düğüm |
| API sözleşmesi (üretici/tüketici) | `K013/api-contracts/openapi` | Adaptörün dışarıya açtığı belge | kardeş düğüm |

**Sınır cümlesi (bağlayıcı):** Adaptör **yalnızca çevirir**. İş kuralı
K012'de, taşıma K014'te, secret K009'da, veri şeması K007-data'dadır.
Adaptörde iş kuralı yazılması = katman ihlali (`.ai/AGENTS.md` §18 uyarı 7).

---

## §3 K013 İçindeki Rolü (ağaçtaki yeri)

| Kademe | Düğüm | Ne katar |
|---|---|---|
| 0 | `K000–K5000 Architecture Registry` | Ağacın kökü |
| 1 | `K013-integraion` | Dış dünya köprüsü — v2 §2.13 |
| **2** | **`K013-integraion/adapters`** | **Üçüncü taraf sistemlere adaptör (bu klasör)** |
| 3 | `adapters-anayasa.md` (bu dosya) | Kimlik, sınır, desen envanteri |
| 3 | `adapters-sozlesme.md` | Çeviri sözleşmesi |
| 3 | `adapters-dogrulama.md` | Doğrulama katmanları |
| 3 | `adapters-kod-ornekleri.md` | Diller bazında örnek |
| 3 | `adapters-web-arastirma.md` | URL kanıtı |

**Kademe-2 kardeşleri (K013'ün 8 çocuğu):** `api-contracts` · **`adapters`** ·
`external-systems` · `event-contracts` · `protocol-adapters` ·
`synchronization` · `error-handling` · `integration-testing`.
(Kaynak: `api-contracts/openapi/index.md` §1 — kardeş listesi.)

**Bağ okları:**

| Yön | Düğüm | İlişki |
|---|---|---|
| ↑ | `K013-integraion` | Bu rolün içindedir |
| ↑ | `K009-security` | Secret/Token kaynağı |
| ↑ | `K014-network` | Taşıma katmanı |
| ↑ | `K007-data` | İç model veri tipleri |
| ↓ | `K012` / integration-service | Adaptörü çağıran katman |
| ↓ | Dış sistem (üçüncü taraf) | Adaptörün konuştuğu muhatap |

---

## §4 Desen Envanteri — bu klasörün anayasa desenleri

### §4.1 Port & Adapter (Hexagonal) — iskelet

| Kavram | Tanım (kaynak dili) | Kanıt |
|---|---|---|
| Port | Uygulamanın dış dünyayla konuştuğu **arayüz** | [A1], [A3] |
| Adapter | Port üzerindeki teknik dönüşümü yapan **somut katman** | [A1], [A3] |
| Sürücü (driver) | Uygulamayı **çalıştıran** taraf (UI, test, batch) | [A1] |
| Sürdürülen (driven) | Uygulamanın **çağırdığı** taraf (DB, dış servis) | [A1] |
| Niyet (alıntı) | "Allow an application to equally be driven by users, programs, automated test or batch scripts…" | [A1] |

> **Kritik cümle (alıntı, [A1]):** "As events arrive from the outside world
> at a port, a technology-specific adapter converts it into a usable
> procedure call or message and passes it to the application. The
> application is blissfully ignorant of the nature of the input device."
> — Uygulama, **adaptörün neyi çevirdiğini bilmez**; bilirse sınır kalkar.

**AWS'in aynı desene verdiği işlev (alıntı, [A3]):** "the application
communicates with external components over interfaces called ports, and
uses adapters to translate the technical exchanges with these components."

### §4.2 Anti-Corruption Layer (ACL) — dış bozulmayı kesme

| Kavram | Tanım | Kanıt |
|---|---|---|
| ACL | Farklı semantiğe sahip iki alt sistem arasına konan **çeviri katmanı** | [A2] |
| Microsoft tanımı (alıntı) | "Implement a facade or adapter layer between different subsystems that don't share the same semantics." | [A2] |
| AWS niyeti (alıntı) | "acts as a mediation layer that translates domain model semantics from one system to another system" | [A3] |
| Evans niyeti (alıntı) | "Create an isolating layer to provide clients with functionality in terms of their own domain model." | [A13] |
| Nezaret ettiği durum | "Your application is communicating with an external system." | [A3] |
| Mikroservis tanımı | "Define an anti-corruption layer, which translates between the two domain models." | [A4] |

**ACL ile adapter farkı (ölçülebilir):** Adapter *arayüzü* uyarlar; ACL
*dilbilgisini* (domain model semantics) uyarlar. CoreMusic'te dış sistem
konusu **ACL kapsamındadır** — çünkü dış sistemin modeli iç modelle
**paylaşılmaz** (v2 §2.13 köprü tanımı).

### §4.3 Facade — sadeleştirilmiş tek kapı

| Kavram | Tanım | Kanıt |
|---|---|---|
| Facade | Karmaşık alt sisteme **basit bir arayüz** sunan yapısal desen | [A6] |
| Ne yapmaz | Alt sistemin işini **değiştirmez**; yalnızca sadeleştirir | [A6] |
| Kullanım yeri | Bir dış SDK'nın onlarca sınıfı yerine tek giriş | [A6] |
| Microsoft'ta yeri | ACL "facade **or** adapter layer" olarak tanımlanır | [A2] |

> **Karışıklık giderici:** Facade **çevirmez** — sadece daraltır. Dış
> sistem modeli farklıysa Facade **yetmez**, ACL gerekir. (Kaynak: [A2], [A6])

### §4.4 Circuit breaker'a giriş — dayanıklılık eki

| Kavram | Tanım | Kanıt |
|---|---|---|
| Sorun | Başarısız olan uzak çağrı tekrar tekrar denenirse kaynak tükenir | [A7] |
| Çözüm | Eşik aşıldığında erişimi **geçici olarak kesme**, servis iyileşince geri açma | [A7] |
| AWS tanımı | "prevent a caller service from retrying a call to another service (callee) when the call has previously caused repeated timeouts or failures" | [A8] |
| Retry ≠ Circuit breaker | Retry **başarılı olacağını umarak** dener; circuit breaker **başarısız olacaksa** denetmez | [A7] |
| Neden adaptör dosyasında | ACL, çağrı yapan sistemin tek hata noktası olabilir → AWS açıkça retry + circuit breaker önerir | [A3] |

> **Bu dosya circuit breaker'ı yalnızca giriş düzeyinde tanımlar**; tam
> durum makinesi, eşik ve pencere kavramları `adapters-dogrulama.md` §5'te
> ve kanıtları `adapters-web-arastirma.md` §4'tedir.

### §4.5 Desen seçim matrisi

| Durum | Desen | Kanıt |
|---|---|---|
| Dış sistem modeli farklı, semantik farklı | **ACL** | [A2], [A3] |
| Dış SDK'nın arayüzü farklı ama semantik aynı | **Adapter** | [A5] |
| Birkaç dış sınıfı tek girişte toplama | **Facade** | [A6] |
| Uygulamayı UI/DB'den bağımsız tutma | **Ports & Adapters (hexagonal)** | [A1], [A3] |
| Dış çağrı tekrar tekrar başarısızsa | **Circuit breaker** (+ retry) | [A7], [A8] |
| Yavaş ama kesintisiz göç (eski sistem yerine yenisi) | Strangler fig | [A14], [A15] |
| Dış çağrıları süreç-dışı proxy'ye verme | Ambassador | [A16] |

> **REDDEDİLEN eşitlik:** "Hexagonal = ACL" **DEĞİLDİR**; hexagonal iç
> yapı iskeletidir, ACL iki sistem arasındaki çeviri niyetidir. İkisi
> üst üste biner ama eşdeğer sayılmaz. (Kaynak: [A1] vs [A2])

---

## §5 Adaptörün Yaşam Döngüsü (tasarım iddiası — kanıt değil)

> Bu bölüm **tasarım iddiasıdır**; repo karşılığı §7'de aranır.

| # | Adım | Çıktı | Kime devredilir |
|---|---|---|---|
| 1 | Dış sistem modeli okunur (docs/OpenAPI) | Ham dış model | `external-systems` |
| 2 | İç model seçilir (K007-data tipleri) | Hedef model | Data Engineer |
| 3 | Eşleme tablosu yazılır (alan → alan) | Sözleşme | `adapters-sozlesme.md` §2–§3 |
| 4 | Port arayüzü tanımlanır | Arayüz | `adapters-kod-ornekleri.md` |
| 5 | Concrete adaptör yazılır | Kod | Backend Architect |
| 6 | Hata eşleme yazılır | Kod + tablo | `adapters-sozlesme.md` §5 |
| 7 | Kontrat + kabul testi | Test | `adapters-dogrulama.md` §2–§4 |
| 8 | CI kapısı | Gate | `adapters-dogrulama.md` §6 |

**Adım 0 kuralı:** Eşleme tablosu (adım 3) **yazılmadan kod (adım 5)
başlamaz** — sözlü eşleme, kod değişince kaybolur.

---

## §6 CoreMusic'te Bu Desenin Rolü (tasarım iddiası)

| Kullanım | Beklenen yer | Durum |
|---|---|---|
| Dış servis adaptörü (ödeme, harita, sosyal giriş …) | `K013/adapters` altında sistem başına sınıf | ⚠️ VERIFICATION REQUIRED (envanter faz C) |
| OAuth sağlayıcı ailesi | `shared/src/OAuth/Provider/` | ✅ Repo kanıtı (§7.2) |
| Cache adaptörleri (iç donanım tarafı) | `shared/src/Cache/` | ✅ Repo kanıtı (§7.1) |
| Dayanıklılık (retry/circuit breaker) | — | ❓ UNKNOWN (karar yok) |
| Secret okuma | `K009-security` (`.env`) | Tasarım iddiası — bkz. v2 §2.13 sınırı |

---

## §7 KANIT — Repo yüzeyi (2026-10-16 disk okuması)

### §7.1 Port/Adapter örneği — Cache (iç donanım adaptörü)

| Dosya | Rol |
|---|---|
| `shared/src/Cache/CacheInterface.php` | **Port** (arayüz) |
| `shared/src/Cache/ApcuAdapter.php` | Concrete adaptör (APCu) |
| `shared/src/Cache/MemoryAdapter.php` | Concrete adaptör (dosya/bellek) |
| `shared/src/Cache/PageCacheAdapter.php` | Concrete adaptör (tam sayfa önbelleği) — `PageCacheInterface` |
| `shared/src/Cache/CacheManager.php` | Adaptör **seçimi** (runtime) — satır 9/13/15/21 |

> Bu dört sınıf, "port + birden çok concrete adaptör + seçim" iskeletinin
> depodaki **gözlenebilir** örneğidir. **Dış sisteme** yönelen bir adaptör
> **değildir** (iç donanım/altyapı katmanıdır) — bu ayrım kasıtlıdır.

### §7.2 Dış sistem adaptörü örneği — OAuth provider ailesi

| Dosya | Rol |
|---|---|
| `shared/src/OAuth/Provider/IOAuthProvider.php` | **Port** (arayüz) |
| `shared/src/OAuth/Provider/BaseOAuthProvider.php` | Ortak OAuth 2.0 mantığı (abstract, `curl` ile istek — satır 9, 76, 106) |
| `DiscordOAuth.php` · `FacebookOAuth.php` · `InstagramOAuth.php` · `LinkedInOAuth.php` · `PinterestOAuth.php` · `RedditOAuth.php` · `SnapchatOAuth.php` · `TikTokOAuth.php` · `XOAuth.php` · `YouTubeOAuth.php` | **10 somut dış-sistem adaptörü** (dizin ölçümü) |

> **Dürüstlük notu:** `shared/AGENTS.md` §2 bu aileyi "12 provider" diye
> listeler; `Provider/` dizininde **10 somut sınıf** vardır (Base + IOAuthProvider
> hariç). Fark **raporlandı**, sessizce hizalanmadı → ⚠️ VERIFICATION REQUIRED
> (belki provider'lar başka dizindedir, bu turda aranmadı).

### §7.3 Negatif kanıt

| Arama | Sonuç | Tarih |
|---|---|---|
| `shared/src/` içinde `AntiCorruption` dizesi | **0 eşleşme** | 2026-10-16 |
| `shared/src/` içinde `CircuitBreaker` dizesi | **0 eşleşme** | 2026-10-16 |
| `.ai/architecture/K013-integraion/adapters/` içinde içerik dosyası | **Yok** (yalnız `index.md`) | 2026-10-16 |

→ "CoreMusic'te adı `AntiCorruptionLayer`/`CircuitBreaker` olan sınıf
**yoktur**" — bu **negatif kanıttır**, "hiç yok" **iddiası değildir**
(yan adlandırılmış eşdeğerler aranmadı → ⚠️ VERIFICATION REQUIRED).

---

## §8 Terim Sözlüğü (bu dosyaya özgü)

| Terim | Anlamı |
|---|---|
| **Port** | Uygulama ile dış dünya arasındaki **arayüz** |
| **Adapter** | Port'u somut teknolojiye bağlayan **çeviri katmanı** |
| **ACL** | Anti-Corruption Layer — dil/semantik çeviri katmanı |
| **Facade** | Alt sisteme **sadeleştirilmiş tek arayüz** (çeviri yapmaz) |
| **Driver / Driven** | Uygulamayı başlatan / uygulamanın çağırdığı taraf |
| **Semantik fark** | Aynı alanın iki sistemde **farklı anlam** taşıması |
| **Koruma katmanı** | İç modelin dış modelle **kirletilmesini** engelleyen sınır |
| **Anti-corruption** | Dış modelin iç modele sızmaması (Evans) |

---

## §9 Sık Karıştırılan Kavramlar

| Karışıklık | Doğrusu |
|---|---|
| Adapter = ACL mi? | Adapter **arayüz** uyarlar; ACL **domain model** uyarlar (bkz. §4.2/§4.5) |
| Facade = Adapter mi? | Hayır; Facade **sadeleştirir**, çeviri yapmaz |
| Adaptör = proxy? | Proxy **iletişimi devralır** (adres/protokol); adaptör **dili çevirir** |
| Adaptör = gateway? | Gateway **yönlendirir/toplar** (bkz. `gateway-aggregation`) — ayrı desen |
| Circuit breaker = retry mi? | Değil — retry dener, circuit breaker **durdurur** ([A7]) |
| Port = HTTP endpoint mi? | Hayır; port **arayüzdür**, endpoint taşımadır (K014) |
| Adaptörde iş kuralı olur mu? | **Hayır** — iş kuralı K012'de (bkz. §2) |
| Cache adapter = dış sistem adaptörü? | **Değil** — §7.1 iç katman örneğidir; ayrım §7.2'dedir |

---

## §10 Zaman Çizelgesi (kanıtlı satırlar)

| Dönem | Olay | Kanıt |
|---|---|---|
| 2004 | Eric Evans'ın kitabı — ACL tanımı | [A2], [A13] |
| 2005-09-04 | Cockburn, Ports & Adapters teknik raporu (v0.9) | [A1] |
| 2011 | Netflix API dayanıklılık çalışması → Hystrix | [A17] |
| 2015-02-17 | Fowler, "Refactoring code that accesses external services" | [A11] |
| 2017-07-03 | Hystrix wiki son düzenleme (GitHub) | [A17] |
| 2018 | Nygard *Release It* (circuit breaker'ın popüler kaynağı) | [A8] |
| 2022-01-12 | Legacy Mimic makalesi (ACL niyet alıntısı) | [A13] |
| 2024-08-22 | Fowler, "Strangler Fig" bliki güncellemesi | [A14] |
| 2026-10-16 | Bu dosyanın çekim tarihi | — |

> **Uydurma yasak:** Yıl/idia ayrımı — yukarıdaki her yıl bir URL'ye
> bağlanmıştır; dayanağı olmayan tarih yazılmamıştır.

---

## §11 Kapsam Dışı (bu dosyada işlenmez)

1. `adapters-sozlesme.md` — çeviri/tip/hata/versioning sözleşmesi
2. `adapters-dogrulama.md` — kontrat testi, sahte sunucu, CI
3. `adapters-kod-ornekleri.md` — diller bazında gerçekçi kod
4. `adapters-web-arastirma.md` — tam URL kanıtı
5. Dış sistem **envanteri** → `K013/external-systems`
6. Protokol dönüşümü (gRPC/GraphQL) → `K013/protocol-adapters`
7. Taşıma/HTTP ayrıntısı → `K014-network`
8. Secret üretimi/ saklanması → `K009-security`
9. Araç/kütüphane envanteri (resilience4j, Hystrix sürümleri vb.) →
   **sürümler çekilmedi → UNKNOWN**

---

## §12 İlgili Düğümler — Ağaçtaki Yeri

| Yön | Düğüm | İlişki |
|---|---|---|
| ↑ | `K013-integraion` | Ana düğüm (v2 §2.13) |
| ↑ | `K013-integraion/api-contracts/openapi` | Dışarıya açılan belge kardeşi |
| ↑ | `K013-integraion/external-systems` | Hangi sistemler kardeşi |
| ↓ | `adapters-sozlesme.md` | Sözleşme ayrıntısı |
| ↓ | `adapters-dogrulama.md` | Doğrulama |
| ↓ | `adapters-kod-ornekleri.md` | Kod örnekleri |
| ↓ | `adapters-web-arastirma.md` | Kanıt |

---

## § Kaynak Tablosu (bu dosyadaki tüm URL'ler)

| # | İddia (kısa) | URL | Tarih | Durum |
|---|---|---|---|---|
| A1 | Ports & Adapters (hexagonal) orijinal 2005 teknik raporu; port/adaptör niyet cümlesi | https://alistair.cockburn.us/hexagonal-architecture/ | 2026-10-16 | ✅ 200 · çekildi |
| A2 | ACL: facade or adapter layer; Evans'in DDD'sinde tanımlandı | https://learn.microsoft.com/en-us/azure/architecture/patterns/anti-corruption-layer | 2026-10-16 | ✅ 200 · çekildi |
| A3 | AWS ACL niyeti + "uygulama dış sistemle konuşuyorsa" + hexagonal port/adaptör tanımı | https://docs.aws.amazon.com/prescriptive-guidance/latest/cloud-design-patterns/acl.html | 2026-10-16 | ✅ 200 · çekildi |
| A4 | microservices.io ACL: iki domain modeli arasında çeviri | https://microservices.io/patterns/refactoring/anti-corruption-layer.html | 2026-10-16 | ✅ 200 · çekildi |
| A5 | Adapter (Wrapper) — yapısal desen, niyeti | https://refactoring.guru/design-patterns/adapter | 2026-10-16 | ✅ 200 · çekildi |
| A6 | Facade — sadeleştirilmiş arayüz, yapısal desen | https://refactoring.guru/design-patterns/facade | 2026-10-16 | ✅ 200 · çekildi |
| A7 | Circuit breaker — eşik sonrası geçici erişim kesme; retry'dan farkı | https://learn.microsoft.com/en-us/azure/architecture/patterns/circuit-breaker | 2026-10-16 | ✅ 200 · çekildi |
| A8 | AWS circuit breaker — Nygard/Release It; ACL'te retry + circuit breaker önerisi (A3 ile birlikte) | https://docs.aws.amazon.com/prescriptive-guidance/latest/cloud-design-patterns/circuit-breaker.html | 2026-10-16 | ✅ 200 · çekildi |
| A9 | AWS hexagonal architecture — "ports and adapters" adı, Cockburn 2005 | https://docs.aws.amazon.com/prescriptive-guidance/latest/cloud-design-patterns/hexagonal-architecture.html | 2026-10-16 | ✅ 200 · çekildi |
| A13 | Legacy Mimic — Evans ACL niyetinin doğrudan alıntısı | https://martinfowler.com/articles/patterns-legacy-displacement/legacy-mimic.html | 2026-10-16 | ✅ 200 · çekildi |
| A14 | Strangler Fig — Fowler'ın göç metaforu (§4.5 satırı) | https://martinfowler.com/bliki/StranglerFigApplication.html | 2026-10-16 | ✅ 200 · çekildi |
| A16 | Ambassador — süreç-dışı proxy (§4.5 satırı) | https://learn.microsoft.com/en-us/azure/architecture/patterns/ambassador | 2026-10-16 | ✅ 200 · çekildi |
| A17 | Hystrix — izolasyon, fail-fast, fallback (§10 satırı) | https://github.com/Netflix/Hystrix/wiki | 2026-10-16 | ✅ 200 · çekildi |

> **Kapsam beyanı:** Bu dosyada listelenmeyen hiçbir URL dayanak
> alınmamıştır. `⚠️ VERIFICATION REQUIRED` / `UNKNOWN` işaretli satırların
> **dayanağı yoktur**. Tam URL envanteri, elenen adaylar ve çakışma
> denetimi `adapters-web-arastirma.md`'dedir.

---

## §A Kapılar ve Açık Sorular (bu dosyanın)

| # | Kapı / Soru | İşaret | Kime gider |
|---|---|---|---|
| 1 | Kaç adaptör, hangi sistemler (envanter §4) | ⚠️ VERIFICATION REQUIRED | `external-systems` + faz C |
| 2 | Adaptörler K013'te mi, K012'de mi barınacak? | ⚠️ VERIFICATION REQUIRED | ADR |
| 3 | Circuit breaker/retry kararı (hangi katman, hangi eşik) | UNKNOWN | ADR |
| 4 | OAuth ailesinin adaptör sınıflandırması | UNKNOWN | ADR-088 kapsamı okunacak |
| 5 | "12 provider" (shared/AGENTS.md) vs 10 somut sınıf (dizin) farkı | ⚠️ VERIFICATION REQUIRED | `shared/OAuth` envanteri |
| 6 | Yan adlandırılmış ACL/eşdeğer sınıf taraması | ⚠️ VERIFICATION REQUIRED | sonraki tur |
| 7 | Kademe-3 bounded-context kapsamı → faz C | ⚠️ VERIFICATION REQUIRED | ADR-097 K4 |

> Bu tablo frontmatter `open_questions` alanını genişletir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** v2 §2.13 · ADR-097 §5.5 · registry §8.1 · Cockburn (2005) · Evans (2004) · Microsoft Learn · AWS Prescriptive Guidance · **Tarih: 2026-10-16**
