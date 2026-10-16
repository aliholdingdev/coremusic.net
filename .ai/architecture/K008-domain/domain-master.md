---
node_id: K008-domain
node_type: root
status: verify-required
parent_id: K000–K5000 Architecture Registry
children:
  - music
  - media
  - user
  - library
  - playlist
  - playback
  - discovery
  - recommendation
  - device-management
  - context-mapping
depends_on: []
evidence:
  - .ai/architecture/K008-domain/index.md
  - .ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md
validation: 2026-10-12 — v2 spec §2.8 + ADR-097 §5.5 ile hizalı; CONFLICT kayıtları açık
open_questions:
  - "shared/AGENTS.md 'K-space mimarisi kaldırıldı (2026-10-12)' notu ile ADR-097 sıfırdan kurulum kararı çelişiyor — orkestratör kararı gerekli"
group: mimari
---

# K008-domain — Domain Master (Anayasa)

> **Authority: Bayram Ali / Vault Steward.** Bu dosya, K008-domain kök-nodesunun
> anayasasıdır. Alt dosyalar bu belgedeki kural ve tanımlarla çelişemez; çelişki
> tespitinde `CONFLICT` kaydı açılır ve düzeltme onaysız yapılmaz.
> **Kaynaklar:** v2 spec §2.8 (satır 298–307) · ADR-097 §5.5 · K008-domain/index.md.

## §1 ROL VE SORUMLULUK

| Madde | Tanım |
|---|---|
| Node adı | `K008-domain` (v2 spec §2.8'deki ad korunur — `domain-master.md` v2 sürümünün adıdır) |
| Katman | Domain (DDD) — CoreMusic katmanlı mimari ağacında Bounded Context sahipliği |
| Kapsam | 10 domain çocuğu + 12 kök-MD anayasa seti |
| Sahiplik | Domain modeli, aggregate sınırları, ubiquitous dil, domain event sözleşmeleri |
| Kapsam dışı | K006 (mikroservis/bağımlılık), K016 (teknoloji-seçim), persistence şeması detayı (data katmanı), UI |
| Konum | `.ai/architecture/K008-domain/` |
| Dönem | ADR-097 K-space v3 — sıfırdan kurulum (2026-10-12) |

K008-domain, CoreMusic'in **alan (domain) gerçeğinin tek kayıt kaynağıdır**: hangi
Bounded Context'in var olduğu, sınırlarının nerede olduğu, dilinin ne olduğu ve
domain event'lerinin hangi sözleşmeyi taşıdığı bu düğüm altında tanımlanır.
Uygulama kodu (PHP sınıfı, endpoint, SQL) bu belgelerin **kanıtıdır**, sahibi
değildir; kanıt kod tarafında `Dosya: … / Satır: …` ile gösterilir.

## §2 KAPSAM SINIRI (NE — NE DEĞİL)

### §2.1 K008-domain içindedir

1. Bounded Context envanteri ve haritası (10 çocuk).
2. Ubiquitous dil sözlüğü (domain ↔ İngilizce teknik terim eşlemesi).
3. Aggregate, Entity, Value Object iskeleti ve bounded context sınırları.
4. Domain event ve integration event sözleşmeleri (PSR-14 dispatcher kanıtı).
5. Domain katmanı kod örnekleri (shared/src/ altındaki gerçek sınıflar).
6. Domain katmanı doğrulama, test stratejisi, risk ve web araştırması kayıtları.

### §2.2 K008-domain dışında değildir

| Dışarıda olan | Sahibi |
|---|---|
| Mikroservis topolojisi, servis keşfi, saga orkestrasyonu | K006 |
| Teknoloji/bağımlılık seçimi, paket versiyon politikası | K016 |
| MySQL şema normalizasyonu, migration, index stratejisi | data katmanı (`.ai/.sql/mysql/` sahibi) |
| REST/gRPC endpoint tasarımı, auth middleware | api/security katmanı |
| ITCSS/BEM/CSS token, mockup | ui-design / frontend |
| CI/CD, Docker, deploy pipeline | devops |

**Sınır kuralı:** K008-domain dosyaları yukarıdaki dışarıdaki konularda **karar
üretmez**, yalnız "domain gereksinimi olarak ne istendiğini" kaydeder ve sahip
düğümü referans verir.

## §3 DDD MODEL İSKELETİ

### §3.1 Bounded Context envanteri

10 çocuk = 10 domain alanı. Her çocuk, kendi `-{çocuk}-kapsam.md` dosyasında
aggregate sınırını açar. Bu master yalnız iskeleti bağlar:

| # | Bounded Context | Çocuk | Ana Aggregate | Birincil Domain Event kaynağı |
|---|---|---|---|---|
| 1 | Müzik Kataloğu | `music` | Music (Recording/Work ilişkili) | MusicPlayedEvent |
| 2 | Medya Varlıkları | `media` | MediaFile (transcode varlığı) | MediaAccessedEvent |
| 3 | Kimlik & Profil | `user` | User (profile/preference) | UserRegisteredEvent |
| 4 | Kütüphane | `library` | LibraryEntry (favori/geçmiş/sıra) | MusicAddedEvent (kuyruk olayı henüz YOK — §7) |
| 5 | Çalma Listesi | `playlist` | Playlist (koleksiyon) | PlaylistCreatedEvent |
| 6 | Oynatma | `playback` | PlaybackSession | MusicPlayedEvent (kuyruk olayı henüz YOK — §7) |
| 7 | Keşif | `discovery` | DiscoveryQuery (radar/trend) | MusicPlayedEvent (akış girdisi) |
| 8 | Öneri | `recommendation` | RecommendationSet | AIEngine çıktısı (integration) |
| 9 | Cihaz Yönetimi | `device-management` | Device (çalma hedefi) | — (kanıt AŞAĞIDA §3.4) |
| 10 | Bağlam Eşleme | `context-mapping` | ContextMap (ilişki kaydı) | — (kanıt AŞAĞIDA §3.4) |

### §3.2 Aggregate kuralları (tüm çocuklar için bağlayıcı)

| Kural | Tanım |
|---|---|
| A1 | Her aggregate tek bir tutarlılık sınırıdır; transaction aggregate içinde kalır |
| A2 | Aggregate'ler arası referans **ID ile** yapılır, nesne grafiği ile değil |
| A3 | Aggregate dışı güncelleme domain event ile olur (yan etki event'e taşınır) |
| A4 | Value Object'ler değişmezdir (immutable) ve eşitliği değerdedir |
| A5 | Repository arayüzü domain katmanında tanımlanır, uygulaması infrastructure'dadır |

### §3.3 Domain event sözleşmesi (repo kanıtı ile)

Tüm domain event'leri `shared/src/Events/Domain/` altında ve ortak bir base
sınıftan türetilir. Doğrulama dosyasında satır kanıtı verilmiştir; burada
sözleşme bağlanır:

| Alan | Kural | Kanıt |
|---|---|---|
| Adlandırma | `{Olay}Event` — geçmiş zaman, düz İngilizce | `MusicPlayedEvent`, `PlaylistCreatedEvent` |
| Zorunlu alan | `eventId` (UUID), `occurredAt` (RFC 3339) | `domain-event.md` §4 (base class) |
| Taşıma | PSR-14 `EventDispatcher` ile senkron yayınlanır | `EventDispatcher.php` s.20, s.28 |
| Kapsam | Domain event = tek Bounded Context içi; arası geçiş integration event | ADR-097 §4 |
| Idempotency | Consumer aynı `eventId`'yi ikinci kez işlememeli | `domain-dogrulama.md` §5 |

### §3.4 Kanıt açığı (verify-required)

| Alan | Durum |
|---|---|
| `device-management` için domain event | `shared/src/Events/Domain/` altında event dosyası GÖRÜLMEDİ (keşif 2026-10-12) — `⚠️ VERIFICATION REQUIRED` |
| `context-mapping` için domain event | Aynı şekilde event dosyası GÖRÜLMEDİ — `⚠️ VERIFICATION REQUIRED` |
| `discovery`/`recommendation` sahipliği | v2 spec K008'e veriyor; eski disk notları K006/K020'ye işaret ediyor — CONFLICT (bkz. §6) |

## §4 ÇOCUK TABLOSU VE KADME PLANI

### §4.1 Çocuk × dosya matrisi (dalga planı)

| Çocuk | Dalga | Beklenen MD (6) | Durum (2026-10-15 güncel) |
|---|---|---|---|
| `music` | 1 | index + 5 | yazıldı (2026-10-12) |
| `media` | 1 | index + 5 | yazıldı (2026-10-12) |
| `user` | 1 | index + 5 | yazıldı (2026-10-12) |
| `library` | 2 | index + 5 | yazıldı (2026-10-12) |
| `playlist` | 2 | index + 5 | yazıldı (2026-10-12) |
| `playback` | 2 | index + 5 | yazıldı (2026-10-12) |
| `discovery` | 3 | index + 5 | yazıldı (2026-10-15) |
| `recommendation` | 3 | index + 5 | yazıldı (2026-10-15) |
| `device-management` | 3 | index + 5 | yazıldı (2026-10-15) |
| `context-mapping` | 3 | index + 5 | yazıldı (2026-10-15) |

### §4.2 Çocuk MD şeması (her çocuk için aynı)

| Dosya | Kademe | Amaç |
|---|---|---|
| `index.md` | — | Çocuk künyesi, grup tablosu, kırık/dalga-3 notu |
| `-{çocuk}-kapsam.md` | mimari | Bounded Context sınırı, aggregate, sözlük |
| `-{çocuk}-dogrulama.md` | kanit | ≥10 içerik satırı; Dosya/Satır kanıtı |
| `-{çocuk}-web-arastirma.md` | kanit | ≥8 benzersiz URL; aynı URL tekrarı yasak |
| `-{çocuk}-ornekler.md` | kod | ≥3 gerçek kod parçası (Dosya/Satır) |
| `-{çocuk}-bilesenler.md` | mimari | Kademe-3 planı (alt düğüm adayları) |

### §4.3 Kapsam dışı çocuk (açık sınır)

`discovery` ve `recommendation`, v2 spec §2.8'de K008 altındadır; ancak repo
kanıtı `recommendation` davranışının büyük kısmının `shared/src/AI/AIEngine.php`
(içinde: MAX_RECOMMENDATIONS sabiti, MIN_SCORE eşiği, DIVERSITY_TARGET
hedefi — sınıf s.28, sabitler s.30–32) içinde yaşadığı gösterir. K008 bu
kodun **sahibi değildir**; domain gereksinimi (öneri kuralları ne olmalı)
K008'de, uygulama K016/teknoloji katmanında konumlanır. Bu sınır
`domain-kararlar.md` §5'te ADR olarak kayıtlıdır.

## §5 GRUPLAMA (ADR-097 §5.5 — İKİ EKSEN)

Her root ve çocuk dosyası frontmatter'inde `group:` taşır. Amaç: aynı klasörde
aynı bilgi türü yan yana dursun.

| `group` | Dosya(lar) | Amaç | Kademe |
|---|---|---|---|
| `mimari` | `index.md`, `domain-master.md`, `domain-kapsam.md`, `domain-bagimliliklar.md`, `domain-bilesenler.md`, `domain-kararlar.md`, `domain-evrensel-dil.md` | Ne olduğu, sınırı, kararı | 1 |
| `kod` | `domain-kod-ornekleri.md`, `domain-test-stratejisi.md`, çocuk `-{çocuk}-ornekler.md` | Nasıl çalıştığı (gerçek kod) | 2 |
| `kanit` | `domain-dogrulama.md`, `domain-web-arastirma.md`, `domain-riskler.md`, çocuk `-{çocuk}-dogrulama.md` / `-{çocuk}-web-arastirma.md` | Neden güvenilir / ne doğrulandı | 2 |

**Kural:** `mimari` dosyası kanıt iddiası yapamaz (kaynak `kanit` dosyasına
yönlendirilir); `kanit` dosyası mimari karar veremez (yönlendirir); `kod`
dosyası yalnız gerçek, çalışır durumda kanıt gösterir.

## §6 CONFLICT KAYITLARI (AÇIK — ONAYSIZ KAPANMAZ)

| # | Çelişki | Kaynak A | Kaynak B | Durum |
|---|---|---|---|---|
| C1 | K008 kimliği: `domain` mi, eski diskteki `K008-servisler` mi? | v2 spec §2.8 `K008-domain` | eski disk K-space notları | `⚠️ VERIFY REQUIRED` — v2 spec kazanır, eski kayıt silinmez |
| C2 | `recommendation`/`discovery` sahipliği: K008 mi K006/K020 mi? | v2 spec §2.8 satır 304–305 | eski K020-recomenataion/K020-discovery notları | `⚠️ VERIFY REQUIRED` — orkestratör kararı |
| C3 | K-space var mı yok mu? | ADR-097 §1 (K-space v3 sıfırdan kurulum) | `shared/AGENTS.md` "`.ai/architecture/**` K-space mimarisi kaldırıldı (2026-10-12)" | `⚠️ VERIFY REQUIRED` — bu set ADR-097'ye göre yazıldı; yetki sorusu açık |
| C4 | `depends_on: []` boşluğu | ADR-097 §5.5 (bağımlılık ekseni varsayılır) | Repo bağımlılık kanıtı henüz toplanmadı (yalnız domain event → dispatcher kanıtı var) | `⚠️ VERIFY REQUIRED` — bilinçli boş; bağımlılık kanıtı toplanınca doldurulacak |

Bu dosyadaki hiçbir CONFLICT, `status: verified` yapılamaz; yalnız kanıt eklendikçe
`open_questions` azaltılır ve `domain-dogrulama.md` §'ye işlenir.

## §7 DOMAIN EVENT ENVANTERİ (repo kanıtı — 2026-10-12 keşfi)

`shared/src/Events/Domain/` altında **9 domain event** sınıfı sayıldı
(keşif 2026-10-12, dizin listesi). Satır kanıtları `domain-dogrulama.md` §4:

| # | Event sınıfı | Sahip çocuk (atanacak) | Durum |
|---|---|---|---|
| 1 | `MusicPlayedEvent` | `music` / `playback` | Dosya kanıtlı |
| 2 | `MusicAddedEvent` | `music` / `library` | Dosya kanıtlı |
| 3 | `PlaylistCreatedEvent` | `playlist` | Dosya kanıtlı |
| 4 | `UserRegisteredEvent` | `user` | Dosya kanıtlı |
| 5 | `UserLoggedInEvent` | `user` | Dosya kanıtlı |
| 6 | `UserLoggedOutEvent` | `user` | Dosya kanıtlı |
| 7 | `PasswordResetRequestedEvent` | `user` | Dosya kanıtlı |
| 8 | `GenderSetEvent` | `user` | Dosya kanıtlı |
| 9 | `MediaAccessedEvent` | `media` | Dosya kanıtlı |

**Düzeltme notu:** `domain-bilesenler.md` §3.2/§3.4'te önceki taslakta
`MediaTranscodedEvent` / `PlaybackQueueUpdatedEvent` adları **varsayımsal** olarak
geçmişti; dizin taraması bu iki adın diskte **bulunmadığını** gösterdi
(`⚠️ VERIFICATION REQUIRED` — çocuk dosyalarında düzeltildi).
`library`/`playback` çocukları için **kuyruk olayı henüz domain event olarak
kodlanmamış** (yalnız `playback_queue` tablosu var).

**Integration event ayrımı:** arkesteş Bounded Context'e geçen olay integration
event'tir ve `shared/src/Events/` altında ayrı dizinde taşınır (keşifte 3
adet sayıldı) — ayrıntı `domain-dogrulama.md` §4.2.

## §8 BAĞIMLILIK EKSENİ (ADR-097 §5.5 ikinci eksen)

| Bağımlılık türü | Yön | Durum |
|---|---|---|
| Domain → PSR-14 (dispatcher) | `shared/src/Events/EventDispatcher.php` (s.20 sınıf, s.28 `dispatch`) | Kanıtlı |
| Domain → PSR-3 (logger) | `domain-bagimliliklar.md` §3 | `⚠️ VERIFICATION REQUIRED` |
| Domain → DTO (Api/Dto/Response) | `MusicResponse.php`, `PlaylistResponse.php` (s.17, s.19+) | Kanıtlı — sınır ihlali riski §9-C3 |
| Domain → PDO / SQL şeması | **YASAK** (domain katmanı persistence bilmez) | Kural — ihlal `domain-riskler.md` R2 |
| Domain → HTTP/UI | **YASAK** | Kural |
| Discovery/recommendation → AIEngine | `shared/src/AI/AIEngine.php` s.28, s.30–32, s.49 | Kanıtlı — C2 CONFLICT ile bağlantılı |

`depends_on: []` bu yüzden bilinçli boş bırakıldı (C4): her satır ayrıca
kanıtlanmadan listeye yazılmaz.

## §9 UYUM (CONFORMANCE) MADDELERİ — her K008 dosyası için bağlayıcı

| # | Madde | Kontrol |
|---|---|---|
| C1 | Frontmatter 10 alan eksiksiz (group dahil) | `domain-dogrulama.md` §2 |
| C2 | Başlıklar `## §N` + tablo biçiminde | Doğrulama scripti / gözden geçirme |
| C3 | Domain katmanı kodu framework/HTTP/PDO bilmeyecek | `domain-kararlar.md` §3 (ADR) |
| C4 | Aynı URL iki farklı K008 dosyasında tekrarlanmaz | `domain-web-arastirma.md` §5 dağıtım cetveli |
| C5 | `TBD`, `TODO`, `Yakında`, `daha sonra`, `...` yer tutucusu yok | Doğrulama §6 |
| C6 | Doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED` işaretli | Doğrulama §7 |
| C7 | Kod örneği `Dosya: … / Satır: …` biçimsiz yazılmaz | `domain-kod-ornekleri.md` §2 |
| C8 | Çocuk dosyası, `index.md`'deki grup/kademe tablosuyla çelişmez | Çocuk index kontrolü |

## §10 TERİM ÖZETİ (tam sözlük → `domain-evrensel-dil.md`)

| Türkçe domain terimi | İngilizce teknik terim | Kullanım |
|---|---|---|
| Bounded Context | Bounded Context | 10 çocuk = 10 BC |
| Bağlam Haritası | Context Map | `context-mapping` çocuğu |
| Yaygın Dil | Ubiquitous Language | `domain-evrensel-dil.md` |
| Alan Olayı | Domain Event | §3.3, §7 |
| Tutarlılık Sınırı | Consistency Boundary (Aggregate) | §3.2 |
| Koleksiyon Sahibi | Aggregate Root | §3.2 |
| Değer Nesnesi | Value Object | §3.2-A4 |
| Depo | Repository | §3.2-A5 |

## §11 SIRADAKİ ADIM (bu master'ın bağladığı işler)

1. `domain-kapsam.md` → 10 Bounded Context sınırının tek tek açılışı (mimari).
2. `domain-dogrulama.md` → ≥25 satır Dosya/Satır kanıtı (kanit).
3. `domain-web-arastirma.md` → tekil URL havuzu (kanit).
4. Çocuk 6'lı setler (dalga 1–2) → dalga 3 yol listesi rapora.

## §12 MASTER YÜZEYİ VE KULLANIM SÖZLEŞMESİ

Bu master, K008 kök-nodesunun **tek giriş kapısı**'dır. Kullanım sözleşmesi:

| Rol | Master'dan aldığı | Master'a verdiği |
|---|---|---|
| Mimari (yeni BC) | §4 çocuk tablosu, §5 gruplama | yeni BC satırı (onayla) |
| Kod yazan | §3 iskelet, §8 bağımlılık sınırı | repo kanıtı satırı |
| Doğrulayıcı | §9 conformance maddeleri | bulgu satırı |
| Araştırmacı | §11 yol listesi | tekil URL kaydı |
| Sözlük sahibi | §10 terim özeti | terim genişletmesi |

**Uygulama kuralı:**
1. Master'a gelen her iş, önce §4'teki çocuğa **eşlenir**; eşlenemeyen iş bu dosyaya
   girmez — yeni BC talebi ayrı bir onay kapısıdır (§6 conflict kaydı).
2. Master'dan çıkan her karar, ilgili çocuk dosyada **satır karşılığı** bulur; karşılığı
   olmayan karar, master'da **beklemede** sayılır.
3. Master, kod/dogrulama/kanıt **barındırmaz** — yalnızca bağlar, yönlendirir, sınır
   çizer.

**Çapraz okuma kuralı:** master tek başına okunarak K008 hakkında karar verilmez;
karar, en az bir çocuk + doğrulama dosyasıyla birlikte okunur.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

## §13 DALGA PLANI (SEVİYE 1 → 3)

§4'teki kademe planının çalıştırma sırası ve bağımlılık kuralları:

| Dalga | Ne | Ön koşul | Bitiş ölçütü |
|---|---|---|---|
| 1 | `kapsam` + `evrensel-dil` (2 dosya) | master §9 conformance | her iki dosya ≥50 satır + Authority |
| 2 | `kararlar` + `bagimliliklar` + `bilesenler` + `test-stratejisi` | dalga 1 PASS | 4 dosya conformance geçti |
| 3 | `dogrulama` + `web-arastirma` + `kod-ornekleri` | dalga 2 PASS | kanıt ≥25 satır, URL tekil |

**Sıra kuralı:**
1. Dalga atlanmaz — dalga 3, dalga 2 onaysız başlamaz.
2. Aynı dalga içinde dosyalar **paralel** yazılabilir (bağımlılık yok).
3. Bir dosya FAILED olursa dalga durur; düzeltme o dosyada yapılır, diğerlerine
   **yayılmaz**.

**Bekleme:** dalga dışı kalan çocuk dosya, listeye girmez; gecikme nedeni §6'ya
kayıt olarak yazılır.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

## §14 CONFORMANCE DEĞERLENDİRME DİLİ (§9'un sözlüğü)

§9 maddelerinin sonuç dili — üç değer ve koşulları:

| Değer | Koşul | Sonuç |
|---|---|---|
| `PASS` | Madde tek bir çelişki olmadan karşılandı | Dosya dalga raporuna yazılır |
| `FAILED` | Madde ihlali var; ihlal satırı Dosya/Satır ile | Dosya raporuna "düzelt" işaretli |
| `PENDING` | Kanıt henüz yok (dosya yazılmadı veya eksik) | Beklemede; FAILED sayılmaz |

**Kural:**
1. `PENDING` asla `PASS` yerine kullanılmaz — kanıtsız PASS, doğrulamanın kendisini
   geçersiz kılar.
2. Bir dosyada 8 maddenin (C1-C8) **hepsi** `PASS` olmadan o dosya "geçti" sayılmaz.
3. `FAILED` satırında ihlalin nerede olduğu zorunludur: `madde + dosya + satır`.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

## §15 MASTER'IN ÇOCUK DOSYALARLA SÖZLEŞMESİ

Bu master ile çocuklar arasındaki **görsel ve kural** bağı — üç kural:

1. **§3 iskeleti bozulmaz:** çocuklar ADR-097 §3.1-§3.8 iskeletini kullanır; iskelet
   dışında yeni alt bölüm ancak §9 conformance'da tanımlanabilir.
2. **§5 gruplama tekeldir:** çocukların grup/kademe değişikliği master'a sorulur;
   çocuk kendi gruplamasını **değiştiremez**.
3. **§10 sözlük tekeldir:** yeni domain terimi önce `domain-evrensel-dil.md`'ye girer,
   oradan §10'a özetlenir; tersi yönde (master→çocuk) terim **taşınmaz**.

**Çelişki halinde:** çocuk ile master çelişirse **master kazanır** — child satırı
master'a göre düzeltilir, master satırı çocuğa göre **değiştirilmez**.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

## §16 MASTER'IN YAPMADIĞI (SINIR KAYDI)

Bu master'ın **bilinçli olarak** yapmadıkları — sınır dışı her şey ayrı dosyanın
yetki alanındadır:

| Konu | Bu master'da | Sahibi |
|---|---|---|
| BC içi kod örneği | Yok | `domain-kod-ornekleri.md` |
| URL erişim kaydı | Yok | `domain-web-arastirma.md` |
| Dosya/Satır kanıtı | Yok | `domain-dogrulama.md` |
| Terim tanımı (tam) | Yok | `domain-evrensel-dil.md` |
| Risk puanı | Yok | `domain-riskler.md` |
| Karar gerekçesi (ADR) | Yok | `domain-kararlar.md` |
| Bağımlılık çözümü | Yok | `domain-bagimliliklar.md` |
| Bileşen sahipliği | Yok | `domain-bilesenler.md` |

**Kural:** bu tabloda "Sahibi" sütununda adı geçen dosyaya **dokunulmaz**; master yalnız
yönlendirir. Sınır aşımı (master'da kod/kanıt/URL barındırma) doğrudan `C1` ihlalidir.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

## §17 MASTER GÜNCELLEME PROTOKOLÜ

Bu master değişeceğinde (yeni BC, dalga planı, conformance maddesi) sırayla:

1. **Değişiklik türü** §6 conflict kaydına yazılır (açık madde varsa önce o kapanır).
2. **Etkilenen çocuklar** §4'te işaretlenir — hangi çocuk bu değişiklikten etkilenir.
3. **Dalga planı** (§13) gerekiyorsa güncellenir; güncelleme, dalga 2/3'teyse geri
   alınır.
4. **Conformance** (§9) gerekiyorsa madde eklenir; eklenen madde **geçmişe dönük**
   uygulanır (eski dosyalar da yeniden kontrol edilir).
5. **Master son satırı (Authority) korunur** — Authority yalnız tarih/ADR no
   güncellenir, imza sahibi **değiştirilmez**.

**Duraklama kuralı:** protokolün 1-3. adımları açık conflict ile yapılamaz; önce
çözüm, sonra güncelleme.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

## §18 MASTER KAPANIŞI (BU DÜZEYİN SINIRI)

Bu master §1-18'i ile K008-domain kök-nodesunun **tamamını** bağlar:

| Düzlem | Bölümler | Ne taşır |
|---|---|---|
| Rol ve sınır | §1-2 | Bu dosyanın görevi ve ne olmadığı |
| Model iskeleti | §3 | DDD iskeleti + kod referansları |
| Plan ve gruplama | §4-5 | 10 çocuk, 4 grup, 3 dalga |
| Açık meseleler | §6 | Onaysız kapanmayan conflict'ler |
| Kanıt özeti | §7-8 | Domain event envanteri, bağımlılık sınırı |
| Bağlayıcılık | §9-10 | Conformance maddeleri, sözlük özeti |
| Çalıştırma | §11-13 | Sıradaki adım, kullanım sözleşmesi, dalga planı |
| Dil ve sınır | §14-16 | Değer dili, çocuk sözleşmesi, sınır kaydı |
| Yönetim | §17-18 | Güncelleme protokolü, kapanış |

**Kapanış:** bu master, K008'in **tek ve bağlayıcı** giriş yüzeyidir; master'da
yazılan her satır çocukların çalışma kuralıdır — ve master'ın kendisi de §14'teki
`PENDING` diliyle, kanıt bağlanana kadar **beklemede** sayılır.

## §19 KRİTİK BAĞIMLILIK SIRASI (master kararı)

K008 yüzeyinde hangi işin hangisinden **önce** gelmesi master düzeyinde zorunlu:

1. **Sözlük önce, kod sonra** — `domain-evrensel-dil.md` olmayan terim, kod örneğine
   yazılmaz (tartışma dili dağınıklaşır).
2. **Kapsam önce, karar sonra** — BC sınırı çizilmeden ADR yazılmaz; sınır, kararı
   belirler, karar sınırı değil.
3. **Karar önce, bağımlılık sonra** — hangi katmanın hangisine bağlı olduğu, karar
   verilmeden çözülemez.
4. **Bağımlılık önce, bileşen sonra** — sahiplik matrisi, bağımlılık yönü bilinmeden
   yazılamaz (yanlış sahip, yanlış sorumluluk).
5. **Test stratejisi her zaman son dalga değildir** — dalga 2'de girer; çünkü strateji,
   sınır ve bağımlılık olmadan **boş** kalır.

**İhlal etkisi:** sıradan sapma, dosyanın conformance `C8`'ini (index/kademe
çelişkisi) değil, doğrudan **kullanım sözleşmesini** (§12) ihlal eder.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

## §20 MASTER DURUM PANOSU (10 ÇOCUK — ANLIK DURUM DİLİ)

§4 kademe planının **durum dili** — hangi çocuk hangi durumdadır; durum, kanıt
bağlanana kadar sabitlenmez:

| # | Çocuk | Durum | Bekleyen |
|---|---|---|---|
| 1 | `kapsam` | DALGA 1 | BC sınırlarının tek tek açılışı |
| 2 | `evrensel-dil` | DALGA 1 | Tam sözlük (master §10 yalnızca özet) |
| 3 | `kararlar` | DALGA 2 | ADR gerekçeleri + §19 sırası |
| 4 | `bagimliliklar` | DALGA 2 | Bağımlılık yönü + sınır ihlali yok |
| 5 | `bilesenler` | DALGA 2 | Sahiplik/olgunluk matrisi |
| 6 | `test-stratejisi` | DALGA 2 | Test piramidi + strateji |
| 7 | `dogrulama` | DALGA 3 | ≥25 satır Dosya/Satır kanıtı |
| 8 | `web-arastirma` | DALGA 3 | Tekil URL havuzu |
| 9 | `kod-ornekleri` | DALGA 3 | `Dosya: … / Satır: …` biçimli örnek |
| 10 | `context-mapping` | DALGA 3 | Bağlam haritası (Context Map) |

**Durum yasağı:** bu tablo bir **hedef durum**'dur (planlanmış olan), fiili durum
**değildir**; fiili durum, ilgili dosyanın kendi içindedir ve master'dan okunmaz.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

## §21 MASTER RİSK KAYDI (MASTER DÜZEYİ)

Bu master'ın kendi seviyesindeki riskleri — çocuk dosyaların riski değil:

| # | Risk | Etki | Önlem (master düzeyi) |
|---|---|---|---|
| R-1 | Master, çocuklardan hızlı güncellenirse | Çocuklar eski kurala göre yazılır | §17 protokolü + dalga duraklatma |
| R-2 | §19 bağımlılık sırası ihlal edilirse | Karar-öncesi kod, geri dönüşsüz | §12 eşleme kapısı |
| R-3 | Conformance `PENDING`→`PASS` kayması | Kanıtsız onay | §14 dili zorunlu |
| R-4 | Yeni BC master'a onaysız eklenirse | Sözlük/gruplama kopması | §6 conflict + §12 eşlenemeyen iş |
| R-5 | Master'a kod/kanıt/URL sızması | Master'ın bağlayıcılığı aşınır | §16 sınır tablosu + C1 |

**Risk yorumu:** R-1 ve R-2, master'ın **tek başına** en kritik ikisidir; ikisi de
proses (kural) riskidir, teknik risk değildir.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

## §22 MASTER'IN SON KONTROL LİSTESİ

Master'ı güncelleyen herkes §17 protokolünden sonra bu listeyi satır satır doğrular:

| # | Kontrol | Beklenen |
|---|---|---|
| M-1 | §4 çocuk tablosu ile §20 durum panosu aynı çocukları mı sayıyor? | 10 çocuk, iki tabloda aynı |
| M-2 | §13 dalga planı, §4 kademe planı ile çelişiyor mu? | Çelişki yok |
| M-3 | §9 conformance maddesi sayısı değişti mi? | Değiştiyse geçmişe dönük kontrol kaydı |
| M-4 | §6 conflict açık mı? | Açık madde varsa §17 duraklatma uygulanmış |
| M-5 | §10 sözlük özeti, `domain-evrensel-dil.md` ile uyumlu mu? | Uyumlu; eksik terim tespit edilmiş |
| M-6 | Authority satırı son satır mı? | Evet — Authority en sonda |
| M-7 | Yeni ek, §12-§18 dışında bir bölüm mü açtı? | Yalnız § numarası artarak devam |

**Uygulama:** kontrol başarısızsa master **değil**, eksikliğin ait olduğu çocuk
dosya düzeltilir; master yalnızca §17 protokolüyle güncellenir.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

---

*Authority: Bayram Ali / Vault Steward · K008-domain master · 2026-10-12 · ADR-097 K-space v3*
