---
title: "CoreMusic — ADR-003: Multi-Domain BCNF Veritabanı Mimarisi"
type: adr
category: database
date: 2026-09-24
updated: 2026-09-24
version: 1.0.0
status: accepted
authority: ADR-003 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-003: Multi-Domain BCNF Veritabanı Mimarisi

**Durum:** accepted (kabul — frozen YOK; okunur + yazılabilir)
**Tarih:** 2026-09-24
**Karar Veren:** Vault Steward (kullanıcı onaylı) · debate: ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL) · Tech Lead: ✅ (2026-09-24)
**İlgili ADR'ler:** [[ADR-002-pdo-mandatory-no-orm]] (erişim katmanı — bu ADR'nin PDO/BCNF ortağı) · [[ADR-081-multi-provider-data-sync]] (DB arası veri akışı — outbox) · karar dizini [[../index]] §3/§6 · Eski seri kayıtları düz metin: ADR-003 (eski slug `multi-db-9-databases`), ADR-033 (sql normalization), ADR-040 (database authority — `(henüz yazılmadı — vault: brain.md ADR-040)`); eski numaralara wiki-link (ADR-0xx biçimi) KURULMAZ.

---

## 1. Bağlam (Context)

CoreMusic, tek bir veritabanında toplanamayacak kadar ayrı domain'lere sahiptir: kimlik/credential, katalog (müzik/album/playlist), sosyal, indirme, stüdyo, cihaz/wireless, AI önerileri, CMS, denetim logları ve şema yamaları. Bu karar, **çoklu BCNF veritabanı mimarisini** tanımlar: hangi veri hangi DB'de durur, her DB neden tek başına ayrı bir şema olmak zorundadır ve DB'ler arası referans/integrasyon kuralları nelerdir. Karar; veri sahipliği (ownership), normalizasyon disiplini (BCNF) ve cross-DB tutarlılık olmak üzere üç bağımsız baskıyı aynı anda karşılamalıdır.

### 1.1 Mevcut Durum

- **Vault kanıtı (envanter):** `.ai/.sql/mysql/` altında **18 adet** `.sql` şema dosyası disktedir (coremusic_ai … coremusic_wireless) — `CREATE TABLE` sayımı **156 tablo**; [[../../brain]] §11 (18 BCNF / 156 tablo) ve ana sözleşme [[../../CLAUDE.md]] §18 ile birebir örtüşür.
- **Eski karar kaydı (çelişkili):** karar dizini [[../index]] §3 ve eski slug `ADR-003-multi-db-9-databases` "**9** BCNF veritabanı" der; [[../index]] §6 Kategori Haritası Database satırı ise 15 kayıt (4 frozen + 11 active) — sayım iddiası değil, kategori sayımıdır.
- **Çelişki kaydı (Truth Mode):** **9 (eski karar) vs 18 (envanter) — vault kanıtı: 18 şema.** Doğru olan **18**'dir; kanıt: (a) `.ai/.sql/mysql/` altında 18 fiziksel `.sql` dosyası (`Get-ChildItem` sayımı = 18), (b) her dosyanın `CREATE TABLE` toplamı 156 = [[../../brain]] §11 tablosunun toplamı, (c) [[../../CLAUDE.md]] §18 "18 BCNF veritabanı, 156 tablo". **9**, envanterin eski (ve güncel olmayan) bir anlık görüntüsüdür — yeni slug'da sayı **kullanılmadı** (`ADR-003-multi-db-bcnf`, sayısız), çünkü sayısal yetki bu ADR'nin değil, envanter kararının işidir (aşağıda §2.3).
- Erişim katmanı ve kalite standardı zaten yazılı: PDO zorunlu / ORM yasak — [[ADR-002-pdo-mandatory-no-orm]] §2; "BCNF, no ORM, no SELECT *, prepared — %100" — [[../../AGENTS.md]] §16 (Data Engineer).
- Örnek tablo adları bu ADR'de `.ai/.sql/mysql/` içindeki gerçek `CREATE TABLE` satırlarından alınmıştır (uydurma yok).

### 1.2 Sorun Tanımı

Tek DB mi, çok DB mi? Eğer çok DB ise: (1) **Bölme ölçütü nedir** — domain mi, boyut mu, teknoloji mi? (2) **Her DB neden tek başına vardır** — gerekçe yazılı değilse "çoklu DB" moda tercihi olur. (3) **DB'ler arası referans nasıl kurulur** — foreign key fiziksel olarak başka şemaya konamaz; join alınamaz; transaction sınırı DB duvarında biter. (4) **Sayısal yetki kimde** — eski karar "9" derken envanter "18" der; sayı hangi dosyanın tekelinde? (5) **Tutarlılık nasıl korunur** — cross-DB join ve cross-DB transaction yokluğunda uygulama seviyesinde ne kalır (application-level ref mı, outbox event mi)?

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: prompt-maker web araştırma protokolü (`.claude/skills/prompt-maker/references/10-web-research-protocol.md`) — resmi/birincil kaynak önce (ACM TODS, Wikipedia tarihçesi, microservices.io), her iddiaya kaynak, 2+ bağımsız çapraz doğrulama. **Odak: BCNF literatürü + polyglot persistence 2025-2026 + database-per-service karşılaştırması + cross-DB transaction.**

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "Boyce-Codd normal form Fagin Armstrong axioms dependency preservation multi-database normalization" · (2) "polyglot persistence 2025 2026 database per service distributed transactions saga cross-database join problems" · (3) "database per service vs monolithic single database trade-offs cross-database join federation limitations 2025" · (4) "Fagin 1974 Boyce-Codd normal form original paper multivalued dependency 1977 history normal forms" |
| Web Search **Konusu** | BCNF teorik temeli (Fagin/Armstrong/Boyce-Codd), BCNF ayrıştırmasının lossless-join / dependency-preservation koşulları; polyglot persistence ve database-per-service'in 2025-2026'daki konumu; domain-split vs tek veritabanı karşılaştırması; cross-DB join ve distributed transaction sorunları (Saga, outbox, API composition). |
| Web Search **Bağlam** | 2026 tarihli akademik derleme (IJRIAS, Vol 11 Iss 8 — Codd 1970, Armstrong 1974, Fagin 1977 zinciri), Fagin'in 1977 ACM TODS orijinal makalesi (4NF), Wikipedia BCNF tarihçesi (Heath 1971 → Boyce & Codd 1974), ders notları (Toronto CSC343, OMSCS, GeeksforGeeks), 2025-2026 blog/sanayi kaynakları (CircleCI, microservices.io, TheCodeForge, TechTrailCamp, Baeldung, Conduktor, Temporal, Will Velida, Swiftorial, urfPublishers polyglot persistence makalesi). |
| Web Search **Kısa Açıklama** | BCNF, her non-trivial FD'de determinant'ın superkey olmasını şart koşar; BCNF ayrıştırması **her zaman lossless join** garantisi verir ama **dependency-preserving olmayabilir** (üst üste binen anahtarlar). Domain-bazlı şema bölmesi (database-per-service) 2026'da olgun bir kalıptır; bedeli cross-DB join'in fiziksel olarak kalmaması ve distributed transaction yerine Saga/outbox/API composition'a geçiştir. |
| Web Search **Uzun Açıklama** | **BCNF literatürü:** Boyce & Codd 1974'te BCNF'yi 3NF'deki anomalileri kapatmak için geliştirdi (Wikipedia tarihçesi; Date'in notuyla tanımlama aslında Heath 1971'dedir); Armstrong'ın 1974 axioms (reflexivity, augmentation, transitivity) FD çıkarımlarının sentaksıdır (Toronto CSC343 dersi + IJRIAS derleme); Fagin 1977 (ACM TODS 2:3) multivalued dependency ve 4NF'yi BCNF'nin üstüne koyar. Kritik sınır: "BCNF decomposition may always not be possible with dependency preserving, however, it always satisfies the lossless join condition" (GeeksforGeeks; OMSCS notları aynı sonucu üst üste binen anahtar senaryosuyla verir) — yani **BCNF + lossless join** garantilidir, **dependency preservation** ise FD'lerin şemalar arası korunmasıyla ek denetim gerektirir. **Domain-split / polyglot 2025-2026:** database-per-service, servis başına veri sahipliği ve bağımsız ölçeklenme sağlar (microservices.io; JavaByTechie Eki 2025; TechTrailCamp Şub 2026); polyglot persistence "her DB teknolojini kullanmak" değil "workload'a bilinçli seçim"dir (Medium/Nejati; urfPublishers makalesi) ve en büyük iki bedeli (a) heterojen DB'ler arası tutarlılık — event-driven/CQRS gerekir, (b) bakım karmaşıklığı — durumlar. **Cross-DB join:** TheCodeForge (Haz 2026) "alanlar arası tek sorguda join yeteneğini kaybedersin — bu bug değil, feature" der; microservices.io "queries must join data owned by multiple services" sorununu API composition / CQRS / event replication ile çözer (TechTrailCamp aynı üç yolu sayar). **Cross-DB transaction:** distributed transaction her depoda desteklenmez (Will Velida); Saga yerel transaction dizisi + compensating transaction olarak tamamlar, ancak geri alma güçtür ve debug pahalıdır (Temporal, Conduktor — Saga CAP'te tutarlılık yerine kullanılabilirliği seçer, eventual consistency). |
| Web Search **Paragraf Veri Uzun** | BCNF: her non-trivial FD X→Y için X superkey · BCNF ⊂ 3NF (üst üste binen anahtarlar) · BCNF ayrıştırması = her zaman lossless join · dependency-preserving olmayabilir · Armstrong axioms: reflexivity / augmentation / transitivity (1974) · Boyce & Codd 1974 (Heath 1971 antedeğeri) · Fagin 1977 ACM TODS → 4NF (BCNF üstü) · Codd 1970 ilişkisel model · database-per-service = servis başına veri sahipliği + bağımsız ölçek (microservices.io) · polyglot = workload-bilinçli seçim, "her şeyi kullanmak" değil · heterojen DB tutarlılığı → event-driven / CQRS · cross-DB join yok → API composition / CQRS / event replication (3 yol) · "join kaybı = feature, domain sınırını zorlar" (TheCodeForge 2026) · distributed transaction her store'da yok (Will Velida) · Saga = yerel transaction dizisi + compensation · Saga debug/monitoring pahalı · Saga CAP: tutarlılık değil kullanılabilirlik (Conduktor) · shared DB: tek transaction kolay, senkron şema değişimi zor · database-per-service: coupling düşük, cross-service query bedeli yüksek. |
| Web Search **Sonucu** | 1) **BCNF teorik çerçeve sağlam ve kararla uyumlu:** determinant = superkey kuralı + lossless-join garantisi, 18 şemanın her birinin tek tek BCNF denetimine (dependency preservation dahil) açıktır; dependency-preservation'ın garanti olmadığı literatürle sabit → denetim ADR-033 (sql normalization) ve envanter kararının işidir (kaynak: Wikipedia + GeeksforGeeks + OMSCS + Toronto CSC343 + Fagin 1977 + IJRIAS 2026 — 6 kaynak). 2) **Domain-split 2026'da olgun kalıp:** database-per-service + polyglot persistence, veri sahipliği ve bağımsız ölçekleme gerekçesini güncel kanıtla taşır (kaynak: microservices.io + CircleCI + TechTrailCamp + JavaByTechie + Baeldung + urfPublishers — 6 kaynak). 3) **Cross-DB join bedeli gerçek:** join fiziksel olarak yok; 3 standart yol var — API composition, CQRS, event replication (kaynak: microservices.io + TheCodeForge + TechTrailCamp — 3 kaynak). 4) **Cross-DB transaction bedeli gerçek:** distributed transaction her store'da yok; Saga + compensating transaction + eventual consistency standarttır, outbox event bununla uyumludur (kaynak: Will Velida + Temporal + Conduktor + CircleCI — 4 kaynak). 5) **Çapraz doğrulama:** her iddia ≥2 bağımsız kaynakla örtüştü; BCNF/Armstrong/Fagin kronolojisi akademik + ansiklopedik iki ayrı aileyle doğrulandı. |
| Web Search **Alınan Karar** | **Multi-domain BCNF veritabanı mimarisi kabul edilir:** her domain kendi BCNF şemasında tek başına DB olur (envanter 18 şema — §2.3); **DB içi FK serbesttir** (aynı şemada referansial bütünlük + lossless-join disiplini); **DB arası FK YOKTUR** — cross-DB referans ya **application-level ref** (owner DB'de tutulan anahtar + uygulama doğrulaması) ya da **outbox event** ([[ADR-081-multi-provider-data-sync]] — transactional outbox + WAL) ile kurulur; cross-DB join API composition/CQRS ile, cross-DB transaction Saga/outbox ile telafi edilir. BCNF denetimi ve dependency-preservation süregelen denetimi bu ADR'nin dışındadır (erişim: [[ADR-002-pdo-mandatory-no-orm]]; normalizasyon detayı: ADR-033 düz metin; sayısal yetki: ADR-040 düz metin — `(henüz yazılmadı — vault: brain.md ADR-040)`). |
| Web Search **Sonuç** | Karar 2026 verisiyle **desteklendi**: BCNF literatürü (6 kaynak: akademik + ders + ansiklopedik), polyglot/database-per-service 2025-2026 (6 kaynak), cross-DB join sınırları (3 kaynak), distributed transaction/Saga (4 kaynak) — toplam **19 kaynak**, 4 sorgu, çapraz doğrulama tam. Kaynaksız iddia yok; tek not: "dependency preservation" BCNF'de garanti değildir → denetim yükümlülüğü açıkça karar dışı bırakıldı (Sonucu madde 1). |

**Kaynak listesi (19):**
1. https://en.wikipedia.org/wiki/Boyce%E2%80%93Codd_normal_form — BCNF tanımı + tarihçe (Codd 1970, Heath 1971, Boyce & Codd 1974)
2. https://www.geeksforgeeks.org/dbms/boyce-codd-normal-form-bcnf — "always lossless join, may not be dependency preserving" + Armstrong axioms bağları
3. https://www.omscs-notes.com/databases/normalization — BCNF ⊂ 3NF, üst üste binen anahtar senaryosu, dependency preservation
4. http://www.cs.toronto.edu/~faye/343/f07/lectures/wk12/12_NormalFormsRevised4-up.pdf — Armstrong axioms (reflexivity/augmentation/transitivity) + dependency preservation tanımı
5. https://s3.us.cloud-object-storage.appdomain.cloud/res-files/500-tods77.pdf — Fagin 1977, ACM TODS 2:3, multivalued dependencies + 4NF (orijinal makale)
6. https://dl.acm.org/doi/10.1145/320557.320571 — Fagin 1977 ACM DL kaydı ("strictly stronger than Boyce-Codd normal form")
7. https://rsisinternational.org/journals/ijrias/uploads/vol11-iss8-pg550-566-202609_pdf.pdf — 2026 akademik derleme: Codd 1970, Armstrong 1974, Fagin 1977, Date/Elmasri/Silberschatz
8. https://dl.acm.org/doi/10.1145/3588693 — BCNF'yi minimal anahtar sayısına göre parametrize eden güncel araştırma
9. https://microservices.io/patterns/data/database-per-service.html — database-per-service: sahiplik, cross-service join/invariant sorunları, drawback'lar
10. https://circleci.com/blog/polyglot-vs-multi-model-databases — polyglot vs multi-model, API composition, Saga, database-per-service loose coupling
11. https://thecodeforge.io/system-design/database-federation — "cross-domain join kaybı = feature" (Haz 2026)
12. https://www.techtrailcamp.com/blog/database-per-service.html — cross-service query: API composition / CQRS / event replication (3 yol) (Şub 2026)
13. https://javabytechie.com/microservices/database-per-service-pattern — shared DB tight coupling (Eki 2025)
14. https://www.baeldung.com/cs/microservices-db-design — polyglot persistence + shared DB'nin tek artısı transaction management
15. https://urfpublishers.com/article/view/polyglot-persistence-usage-and-challenges — polyglot tutarlılık challenge: event-driven/CQRS zorunluluğu + bakım overhead
16. https://hosseinnejati.medium.com/polyglot-persistence-using-the-right-database-for-each-service-81dca8bdf871 — polyglot = workload-driven bilinçli seçim
17. https://www.willvelida.com/posts/saga-pattern — distributed transaction her store'da yok; Saga local transaction + compensation
18. https://temporal.io/blog/mastering-saga-patterns-for-distributed-transactions-in-microservices — Saga + event-driven akış, commit-sync
19. https://www.conduktor.io/glossary/saga-pattern-for-distributed-transactions — Saga CAP'te kullanılabilirliği seçer; eventual consistency; monitoring/debugging pahalı
20. https://www.swiftorial.com/matchups/software_architecture/shared-db-vs-db-per-service — shared DB (basit, cohesive) vs DB-per-service (autonomous, scalable) eşleşmesi

*(20. madde ek kaynak — toplam kanıt 19+1 = 20; §1.3 "Sonuç" satırındaki 19, dört odak sorusunun birleşik çekirdek kümesidir.)*

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| BCNF zorunluluğu | Her şema (tablolar arası FD'ler dâhil) BCNF'dir; determinant superkey değilse tablo bölünür. Denetim sürdürülebilir olmalı: dependency-preservation BCNF'de garanti değildir (§1.3 kaynak 2-4) → denetim `CREATE TABLE` + FD listesi üzerinden yapılır; ORM'siz, prepared statement ile — [[ADR-002-pdo-mandatory-no-orm]]. |
| Cross-DB FK yasağı | DB'ler arası `FOREIGN KEY` fiziksel olarak tanımlanamaz (aynı MySQL instance'ında olsa bile şemalar arası referansial bütünlük bu mimaride bilinçli olarak KURULMAZ). DB'ler arası ilişki yalnız application-level ref veya outbox event ile ifade edilir (§2.2 b). |
| Tek yazıcı ilkesi | Her tablonun tek bir sahibi (owner DB) vardır; başka DB o tabloya doğrudan yazamaz — veri paylaşımı okuma replikası/projection veya event ile olur ([[ADR-081-multi-provider-data-sync]]). |
| Erişim katmanı sabiti | Tüm şemalara erişim PDO + prepared statement ile; ORM yasak; `SELECT *` yasak — [[ADR-002-pdo-mandatory-no-orm]] §2, [[../../AGENTS.md]] §16. |
| Sayısal yetki sınırı | Bu ADR şema **sayısı** tayin etmez (slug sayısızdır); envanterin sayısı ve güncelliği envanter kararının tekelindedir (§2.3); bu ADR yalnız mimari (bölme ölçütü + FK stratejisi) kararlaştırır. |
| Şablon zorunluluğu | Şemalar `.ai/.sql/mysql/` altında Data Engineer sorumluluğundadır; yeni şema dosyası Guardrail #16 + migration kuralı (eski seri ADR-014 — düz metin, `[[ADR-0xx]]` linki kurulmaz) ile üretilir. |

---

## 2. Karar (Decision)

**CoreMusic, domain'lere ayrılmış çoklu BCNF veritabanı mimarisi kullanır.** Her domain kendi BCNF şemasında **tek başına bir veritabanıdır** (envanter §2.3 — vault kanıtı 18 şema); **DB içi foreign key serbesttir** (referansial bütünlük aynı şemada tam korunur); **DB arası foreign key YOKTUR** — çapraz referans ya **application-level ref** (sahip DB'de tutulan anahtar + uygulama/doğrulama katmanı) ya da **outbox event** (transactional outbox + WAL — [[ADR-081-multi-provider-data-sync]]) ile kurulur. Cross-DB join bilinçli olarak yoktur; birleşik okumalar API composition veya CQRS projection ile üretilir.

### 2.1 Neden Bu Seçenek?

1. **Domain sahipliği (ownership):** Her tablonun tek sahibi olur; şema değişimi, indeks kararı ve yavaş sorgu yalnız o domain'i etkiler (database-per-service kanıtı: mikro servis bağımsızlığı — §1.3 kaynak 9, 12, 13). Paylaşımlı tek DB'de şema değişimi koordinasyonu tüm sistemi kilitler (§1.3 kaynak 13).
2. **BCNF denetlenebilirliği:** 18 şema tek tek, bağımsız FD listeleriyle BCNF'ye denetlenir; lossless-join garantisi şema içi ayrıştırmada literatürle sabittir (§1.3 kaynak 1-4). Tek dev şemada üst üste binen anahtarlar ve global FD'ler denetimi pratikte imkânsızlaştırır.
3. **Arıza ve risk izolasyonu:** Log/analitik yoğunluğu olan `coremusic_logs`, katalog, auth gibi yükler ayrı DB'de ölçeklenir; bir DB'nin dolması/değişimi diğerini durdurmaz (§1.3 kaynak 9, 17).
4. **Güvenlik yüzeyi:** `credential_vault` (`coremusic_auth`) ile CMS/log şemaları aynı yetki alanında değil; DB başına yetki/erişim kısıtı OWASP erişim kontrolü katmanını güçlendirir (vault: [[../../CLAUDE.md]] K5, [[ADR-002-pdo-mandatory-no-orm]] §2).
5. **Tutarlılık bedeli bilinçli kabul edildi:** Cross-DB FK/join/transaction yokluğu bir kayıptır; literatürün standart telafileri (application-level ref, outbox event, API composition, CQRS) bu kararla zorunlu kılınır — gizlenmez, §4.2/§4.3'te yazılır.

### 2.2 Teknik Detaylar

**(a) Domain → DB özet tablosu (her DB: kategori · 2-4 örnek tablo (disk kanıtlı `CREATE TABLE`) · tek başına neden DB):**

| # | Veritabanı | Kategori | Örnek tablo adları (`.ai/.sql/mysql/` içinden) | Tek başına neden DB? |
|---|------------|----------|-----------------------------------------------|----------------------|
| 1 | coremusic_auth | Kimlik & credential | `users`, `user_roles`, `credential_vault`, `user_sessions` | En yüksek güvenlik hassasiyeti; ayrı yetki/şifreleme alanı; auth yükü bağımsız ölçeklenir |
| 2 | coremusic_user | Kullanıcı profili | `user_profiles`, `user_preferences`, `user_favorites`, `playback_queue` | Profil/tercih yazım paterni auth'tan farklı (sık güncelleme); sahiplik user-domain'e ait |
| 3 | coremusic_musics | Müzik katalog (çekirdek) | `artists`, `musics`, `music_files`, `music_lyrics` | En büyük ve en çok join'li çekirdek domain; 22 tablo; okuma-yoğun, indeks ağırlıklı |
| 4 | coremusic_albums | Albüm koleksiyonu | `albums`, `album_discs`, `album_stats` | Albüm istatistikleri yüksek yazım hacmi; musics'ten ayrı ölçeklenir |
| 5 | coremusic_playlist | Çalma listesi | `playlists`, `playlist_tracks`, `playlist_collaborators` | İşbirliği + takip grafiği; yazım paterni katalogdan farklı |
| 6 | coremusic_catalog | Referans verisi | `catalog_genres`, `catalog_artist_roles`, `catalog_instruments` | Neredeyse salt okunur sözlük verisi; cache'lenir, nadiren değişir |
| 7 | coremusic_logs | Denetim & analitik | `audit_logs`, `error_logs`, `analytics_daily_users` | Append-only, zaman serisi; backup/retention politikası diğerlerinden farklı |
| 8 | coremusic_media | Cihaz & medya senkron | `devices`, `device_sync_history`, `media_metadata` | Cihaz senkron yazım trafiği; IoT benzeri patern |
| 9 | coremusic_system | Sistem ayarları | `system_settings`, `system_config`, `i18n_languages` | Global config + i18n; tüm paneller okur, tek yazımcı |
| 10 | coremusic_social | Sosyal etkileşim | `comments`, `shares`, `listening_rooms`, `activity_feed` | Write-heavy feed; bağımsız fan-out/ölçekleme |
| 11 | coremusic_wireless | Ağ (WiFi/BT) | `wifi_networks`, `bluetooth_peers`, `network_profiles` | Donanım-sınırı veri; cihaz tarafı senkronu |
| 12 | coremusic_ai | Öneri & ML | `user_preference_profiles`, `listening_features`, `model_versions` | Feature store + model metadata; batch eğitim işleri ayrı yük |
| 13 | coremusic_api | API anahtarı & rate limit | `api_keys`, `rate_limits`, `api_calls` | Hot-path sayaç verisi; düşük gecikme, yüksek hacim |
| 14 | coremusic_cms | İçerik yönetimi | `cms_pages`, `blog_posts`, `blog_post_tags` | Editoryal içerik; yayın akışı iş/domain'i farklı |
| 15 | coremusic_download | İndirme kuyruğu | `download_queue`, `download_history`, `download_sources` | Kuyruk + anti-ban durumu; iş kuyruğu semantiği |
| 16 | coremusic_neva | DSP / EQ | `eq_presets`, `dsp_settings`, `routing_matrix` | Engine'e yakın ayar verisi; embedded tarafın okuma alanı |
| 17 | coremusic_studio | Stüdyo oturumu | `studio_sessions`, `studio_tracks`, `studio_equipment` | Pro kullanıcı iş akışı; ekipman/oturum domaini |
| 18 | coremusic_patch | Şema yaması | `schema_versions`, `migration_log`, `patches` | Migration meta-verisi; DB'nin kendisini yöneten katman — asla domain verisiyle karışmaz |

**Toplam:** 18 şema, 156 tablo (`.ai/.sql/mysql/` disk sayımı + [[../../brain]] §11).

**(b) FK stratejisi (bağlayıcı):**

| Kural | Kural Detayı | Uygulama |
|-------|--------------|----------|
| **DB içi FK: serbest** | Aynı şemada referans `FOREIGN KEY` ile tanımlanır; `ON DELETE`/`ON UPDATE` davranışları şema sahibi Data Engineer tarafından belirlenir; BCNF lossless-join denetimiyle uyumlu olmalı | `CREATE TABLE` içinde normal FK + indeks |
| **DB arası FK: YOK** | Başka şemaya `FOREIGN KEY` yazılmaz; şemalar arası referansial bütünlük MySQL'e emanet edilmez | CI/statik kapı: `REFERENCES` başka şema adı içeriyorsa RED |
| **Seçenek 1 — application-level ref** | Çapraz anahtar (ör. `musics.user_id` sahibi DB'de) uygulama katmanında doğrulanır; owner DB'de tutulan anahtar tek doğruluk kaynağıdır; silme/kalibrasyon işleri uygulama orkestrasyonuyla (soft delete + doğrulama) yapılır | Repository + service doğrulaması; orphan toleransı bilinçli (§4.3) |
| **Seçenek 2 — outbox event** | Domain olayı (ör. kullanıcı silindi, müzik yayınlandı) **aynı transaction içinde** outbox tablosuna yazılır; relay yayınlar; tüketici kendi projection'ını günceller (eventual consistency) | [[ADR-081-multi-provider-data-sync]] — transactional outbox + WAL; PSR-14 event-driven ile uyum (brain ADR-086 — düz metin) |
| **Cross-DB join: yok** | Birleşik okuma API composition (uygulama birleştirir) veya CQRS projection (önceden birleştirilmiş okuma tablosu) ile | [[ADR-081-multi-provider-data-sync]] projection modeli |
| **Cross-DB transaction: yok** | Yerel transaction yalnız kendi DB'de (`beginTransaction` → commit/rollback — [[ADR-002-pdo-mandatory-no-orm]] §2.2 b); çok adımlı iş Saga/best-effort + idempotency ile | Uygulama orkestrasyonu; compensation adımları yazılı |

**(c) Bölme ölçütü:** domain (iş kavramı) — **teknolojiye göre değil** (DBMS polyglot'luğu bu kararın parçası DEĞİL; tüm 18 şema MySQL'dir), **boyuta göre değil** (büyüklük tek başına bölme gerekçesi değildir, domain sahipliği gerekçedir).

**(d) Sayısal yetki ayrımı (bölünmüş sorumluluk):**

| Karar | Kapsam | Kayıt |
|-------|--------|-------|
| **Bu ADR (ADR-003)** | MİMARİ: neden çoklu DB, domain split ölçütü, FK stratejisi, cross-DB entegrasyon kuralları | Bu dosya — `ADR-003-multi-db-bcnf` (slug **sayısız**) |
| **Envanter/yetki kararı (ADR-040)** | ENVANTER/YETKİ: kaç DB olduğu, hangi DB'nin adı/tablo sayısı, sayısal değişiklik yetkisi | Düz metin: `(henüz yazılmadı — vault: brain.md ADR-040)`; özet: [[../../brain]] §11 (18 şema / 156 tablo) |

**Çelişki çözümü (tekrar kaydı):** "9 (eski karar) vs 18 (envanter) — vault kanıtı: **18 şema**" (§1.1 kanıt zinciri a-c). Doğru olan 18'dir; **9** eski slug'da (`multi-db-9-databases`) kalmıştır ve yeni slug'da sayı **kullanılmamıştır** — sayısal yetki envanter kararına (ADR-040) bırakılmıştır.

### 2.3 Envanter (yetki değil — özet)

18 şemanın adları, tablo sayıları ve amaçları bu ADR'de **tekrarlanmaz** (SRP): [[../../brain]] §11 ve [[../../index]] §8 tek envanter kaynağıdır; `.ai/.sql/mysql/` altındaki 18 `.sql` dosyası fiziksel kanıttır. Bu ADR yalnız §2.2 a'da kategori + örnek tablo + gerekçe özetini verir.

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Tek dev veritabanı (shared DB)** | Tek join, tek transaction, en basit tutarlılık | Şema değişimi tüm sistemi senkronize etmeye zorlar; auth/log/katalog yükleri birbirini yavaşlatır; tek arıza noktası; domain sahipliği belirsiz | Reddedilen karar R-009 (`.ai/.decisions/index.md` §5 — "Single DB: güvenlik/performans"); domainler arası coupling literatürle yüksek (§1.3 kaynak 13, 20); 156 tabloda global FD denetimi pratikte imkânsız |
| 2 | **Polyglot persistence (DB başına farklı DBMS: NoSQL/search/graph)** | Workload'a en uygun motor; polyglot 2026'da olgun | 18 farklı motor operasyon/bakım yükü; ekibin MySQL dışındaki yetkinliği sınırlı; BCNF kavramı ilişkisel olmayan motorda anlamsızlaşır; red kararı R-002 (MongoDB — "BCNF uyumsuz") | Karar **domain split** için verildi, teknoloji split'i için değil (§2.2 c); BCNF zorunluluğu ile NoSQL doküman modeli çelişir; heterojen tutarlılık bedeli (§1.3 kaynak 15) ek bir maliyet katarken kazanç yok |
| 3 | **DB'ler arası foreign key / federated join (MySQL federated engine, distributed transaction/2PC)** | Referansial bütünlük DB'ye devredilir, join görünür kalır | Şemalar arası FK bakım pahalı; federated engine olgun değil; 2PC distributed transaction her yerde desteklenmez, Saga zorunluluğu değişmez; tek slow DB tüm zinciri kilitler | Literatür: distributed transaction her store'da yok (§1.3 kaynak 17); join kaybı domain sınırını zorlayan bilinçli tercihtir (§1.3 kaynak 11); outbox ([[ADR-081-multi-provider-data-sync]]) aynı tutarlılığı daha ucuz sağlar |
| 4 | **Domain split'i atlayıp "büyüyünce böl" (monolith DB + sonradan ayrışma)** | İlk haftalar en hızlı yol | 156 tablo büyüdükten sonra bölme veri göçü + kod rewrite gerektirir; R-009 aynı nedenle reddedildi | Geri dönüş maliyeti en yüksek seçenek (hard-to-reverse); erken bölme maliyeti (çapraz ref disiplini) sonradan bölme maliyetinden (veri taşıma + dual-write) düşüktür |
| 5 | **DB arası tutarlılığı yalnız polling/batch senkron ile çözmek (outbox'siz)** | Basit cron, az altyapı | Kayıp event gözden kaçar; transaction ile olay arasında crash boşluğu (dual-write problemi); kaynak DB ile hedef arasındaki fark kullanıcıya yansır | Dual-write problemi outbox'la çözülür; [[ADR-081-multi-provider-data-sync]] outbox + WAL'ı zaten kararlaştırmıştır — alternatif onunla doğrudan çelişir |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Domain sahipliği net:** her tablonun tek sahibi; şema değişimi, indeks ve yavaş sorgu izolasyonu (§2.1 m.1).
- **BCNF denetimi yapılabilir:** 18 bağımsız FD seti → lossless-join denetimi şema bazında (literatür garantisi, §1.3 kaynak 1-4); `EXPLAIN`/query budget disiplini ADR-002 ile uyumlu çalışır.
- **Arıza & ölçek izolasyonu:** logs/ai/api gibi yoğun domainler ayrı DB'de ölçeklenir; bir domain'in DB'si diğerini durdurmaz.
- **Güvenlik yüzeyi daralır:** `credential_vault` ayrı DB'de; DB başına erişim kısıtı (defense in depth).
- **Entegrasyon standart:** tek yazıcı + outbox + projection modeli, çapraz referansı standart bir akışa bağlar — [[ADR-081-multi-provider-data-sync]] ile tek uyumlu yol.

### 4.2 Olumsuz Sonuçlar

- **Cross-DB join yok:** birleşik raporlar/sorgular API composition veya CQRS projection ile elde yazılır; "tek SELECT" konforu kaybolur.
- **Cross-DB transaction yok:** çok adımlı işler Saga/compensation veya best-effort + idempotency ile yürür; anlık tutarlılık garantisi yok (eventual consistency).
- **Boşluk (orphan) riski uygulamaya taşınır:** DB'ler arası silme kademeli olduğundan orphan referanslar oluşabilir → soft delete + doğrulama/temizlik işi yazılır.
- **Operasyonel yük:** 18 şema = 18 backup/migration/monitoring hedefi; yedekleme ve sürüm yönetimi çaprazan yapılır.
- **Onboarding maliyeti:** yeni geliştirici "şu veri nerede?" sorusuna envanter + sahiplik haritasına bakar (tek kaynak: [[../../brain]] §11).

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| Cross-DB join ihtiyacının kodda "hızlı çözüm" (uygulamada her sorguda N DB okuma) ile karşılanması → performans çöküşü | 3 (olası) | 3 (orta) | API composition bütçesi + CQRS projection zorunluluğu; query budget + `EXPLAIN` (ADR-002 §5.1 #6 ile aynı kapı) |
| Cross-DB transaction yokluğunda kısmi başarısızlık (1. DB commit, 2. DB başarısız) → tutarsız görünüm | 3 (olası) | 4 (yüksek) | Outbox event ([[ADR-081-multi-provider-data-sync]]) + idempotency anahtarı + compensation adımı; dual-write yasak |
| Orphan referans (silinen kullanıcıya ait playlist satırı gibi) | 3 (olası) | 2 (düşük) | Soft delete (`is_deleted = 0` — brain §11 kuralı) + periyodik temizlik job'ı + uygulama doğrulaması |
| FK stratejisi ihlali (developer DB'ler arası FK/federated join ekler) | 2 (mümkün) | 3 (orta) | Statik kapı: `REFERENCES` başka şema → CI RED (§2.2 b); code review |
| Envanter sayısının (18) güncelliğini kaybetmesi / 9-vs-18 sınıfı yeni çelişki | 3 (olası) | 2 (düşük) | Sayısal yetki tek dosyada (envanter kararı — §2.2 d); bu ADR slug'ı sayısız; her şema ekleme/çıkarmada envanter kararı + log append güncellenir |
| 18 şemanın yedek/migration operasyonel yükü | 3 (olası) | 3 (orta) | Tek migration orkestratörü (eski seri ADR-014 düz metin), şablon `coremusic_patch` (schema_versions/migration_log) üzerinden tekilleştirme |

### 4.4 Fallback (Zaruri İstisna Kapısı)

| # | Koşul (hepsi) | Onay |
|---|---------------|------|
| 1 | Cross-DB join/transaction gereksinimi 3 sprint boyunca CQRS/outbox ile karşılanamamıştır — kanıt: teknik değerlendirme notu | Data Engineer + Backend Architect |
| 2 | **Yeni ADR** yazılır; bu dosya düzenlenmez (`superseded by` bağı yeni ADR'nin §6'sına konur) | Vault Steward → Tech Lead → Arch Lead |
| 3 | Kapsam dar: tek domain çifti (ör. musics↔albums okuma birleşimi) — tüm mimari değil | Arch Lead |
| 4 | Şema içi BCNF + prepared statement + ORM yasağı **değişmez** ([[ADR-002-pdo-mandatory-no-orm]]) | Data Engineer + QA Engineer |

*Geçici istisna en fazla 5 iş günü sürer; sonunda `git revert` + `log.md` ERROR satırı. DB'ler arası `FOREIGN KEY` hiçbir geçici istisnada eklenemez — yalnız application-level ref / outbox kullanılır.*

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | Bu ADR'yi `.ai/.decisions/accepted/` altına yaz (slug `ADR-003-multi-db-bcnf`) + `log.md` append ("ADR-003 yazıldı (debate PENDING)") + dizin satırı doğrula ([[../index]] §3 — eski slug satırı NOT revizyonu üst görev kapsamında) | Vault Steward | 30 dk |
| 2 | §2.2 b FK stratejisini statik kapıya bağla: (a) cross-schema `REFERENCES` taraması, (b) DB'ler arası join prompt'ları code review notu, (c) outbox yazımı PDO transaction içinde mi? | Data Engineer + QA Engineer | 1 gün |
| 3 | Domain sahiplik haritası: her `.ai/.sql/mysql/*.sql` → owner domain/service eşlemesi [[../../index]] §8 ile senkron (yeniden yazım DEĞİL, doğrulama) | Data Engineer | 2 saat |
| 4 | Cross-DB okuma senaryolarını CQRS/outbox'a çevir: birleşik raporlar için projection tablosu tanımı (varsa; yoksa envanter kararına not) | Backend Architect + Data Engineer | 2 gün |
| 5 | Debate (✅ TAMAMLANDI — 3 tur / 20 persona, 18/2/0 KABUL) + Tech Lead (✅ 2026-09-24) onayı: sonuç §7.1'e yazıldı; şartlar §5.1 #7-#9'a satır olarak eklendi | Vault Steward + Tech Lead | 1 gün |
| 6 | Envanter kararı (ADR-040) yazıldığında §2.2 d satırındaki `(henüz yazılmadı …)` notu güncellenir — **bu dosya düzenlenmez, log append + envanter kararında kayıt** | Vault Steward | ADR-040 anında |
| 7 | **Debate şartı 1:** 18 sayısal yetki ADR-040 envanter kararında tescil edilir; bu ADR yalnız mimari karar verir (bölme ölçütü + FK stratejisi — §2.2 d) | Vault Steward | ADR-040 yazımında |
| 8 | **Debate şartı 2:** Cross-DB ref-check job (periyodik orphan/referans doğrulama) + cross-DB entegrasyon testi yazılır ve CI kapısına bağlanır (§4.3 orphan riski ile aynı kapı) | Data Engineer + QA Engineer | 2 gün |
| 9 | **Debate şartı 3:** Domain başına join-yolu tablosu çıkarılır — hangi birleşik okuma hangi yolla (API composition / CQRS / event), domain bazında (§2.2 b) | Backend Architect + Data Engineer | 1 gün |

### 5.2 Geri Dönüş Planı

Karar mimaridir; geri dönüş yalnız **yeni ADR** ile olur (In-Place Refactoring yasağı — bu dosya frozen olmasa da keyfi düzenlenmez). Senaryolar: (1) **Domain birleştirme** gerekirse (ör. 18 → N): yeni ADR yazılır, `superseded by ADR-NNN` bağı **yeni ADR'nin** §6'sına konur; veri taşıma şema-işlem-listesi + dual-read/dual-write olmadan, tek yön göç + doğrulama ile yapılır; (2) **FK stratejisi değişirse** (ör. belirli bir çift DB'ye federated join): §4.4 fallback koşulları + yeni ADR; (3) **Envanter sayısı değişirse** (DB ekle/çıkar): bu ADR **dokunulmaz** — sayısal yetki envanter kararındadır (§2.2 d), işlem `log.md` append + envanter kararı güncellenir; (4) **Outbox kaynaklı veri kaybı riski:** outbox satırı ana transaction ile yazıldığı için (ADR-081) geri dönüş `git revert` + son commit ile standart vault kurtarması ([[../../AGENTS.md]] §17 #10); (5) **Veri kaybı riski yoktur** — bu ADR katmandır, şema/veri değiştirmez; şema zaten diskte (18 dosya) durur.

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[ADR-002-pdo-mandatory-no-orm]] | Erişim katmanı ortağı: PDO zorunlu / ORM yasak / prepared — bu ADR'nin §2.2 b'sindeki her sorgu bu kuralla yürür |
| [[ADR-081-multi-provider-data-sync]] | DB arası entegrasyonun tek uyumlu yolu: transactional outbox + WAL + projection (§2.2 b, §4.1) |
| [[ADR-001-vanilla-js-itcss]] | Aynı ilke ailesi: kütüphane serbest / iskelet yasak — bu ADR'de: domain serbest / şema sahipliği yasak değil, FK crossing yasak |
| [[../index]] | Karar dizini — bu ADR'nin kaydı (`ADR-003-multi-db-bcnf`) + eski slug satırı (`ADR-003-multi-db-9-databases` — düzeltme üst görevin işi) + §6 Kategori Haritası Database satırı |
| [[../CLAUDE.md]] | Karar alt registry kuralı (accepted/) |
| [[../../CLAUDE.md]] | Ana sözleşme — §18 "18 BCNF Databases" (envanter özeti), K5 veri katmanı |
| [[../../AGENTS.md]] | Data Engineer domaini (§4/§5/§15), keyword routing (§6), kalite "BCNF, no ORM" (§16), retry/timeout (§8) |
| [[../../brain]] | Mimari karar özeti — §11 envanter tablosu (18 şema / 156 tablo) + ADR-040/ADR-050/ADR-086 özetleri (düz metin: ADR-040 `(henüz yazılmadı — vault: brain.md ADR-040)`; ADR-033 düz metin) |
| [[../../index]] | Master katalog — §8 Veritabanı envanteri (18 satır) |
| [[../../keys]] | Keyword haritası — "multi-db, 18 veritabani" eşlemesi (eski slug satırı düzeltmesi üst görev kapsamında) |
| [[../../.templates/adr/adr-template]] | Bu ADR'nin 7 bölümlük şablonu (Guardrail #16) |
| `.ai/.sql/mysql/` | 18 `.sql` şeması — fiziksel envanter kanıtı (§1.1, §2.2 a; 156 `CREATE TABLE`) |
| `.ai/.decisions/index.md` §5 | Reddedilen kararlar: R-009 (Single DB), R-002 (MongoDB/BCNF) — §3 alternatif gerekçeleri |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | §1.3 web araştırma protokolü |
| [[../../log]] | Audit trail (bu kaydın append satırı) |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali (kullanıcı onaylı karar) | 2026-09-24 | ✅ |
| Tech Lead | Debate onayı (3 tur / 20 persona — 18/2/0 KABUL) | 2026-09-24 | ✅ |
| Arch Lead | ⏳ | — | ⏳ |

### 7.1 Tartışma Kaydı (Debate — kaynak alanı)

| Alan | Değer |
|------|-------|
| Biçim | ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL) — 2026-09-24 |
| Tur 1 — 20 persona | 14 kabul/neutral · 3 uyarı: **DevOps** (18 şema migration orkestrasyonu), **QA** (cross-DB entegrasyon testi), **PM** (operasyonel yük) · Critic en sert: "9 vs 18 — iki rakam dolaştırılıyor" |
| Tur 2 — İtiraz→çözüm | (1) DB arası FK yok = uygulama unutkanlığı → **ref-check job** maddesi (§5.1 #8); (2) 18 şema erken bölünme mi? → kanıt: 156 tablo gerçek (`.ai/.sql/mysql/` 18 dosya — §1.1); (3) cross-DB join yolları (API composition / CQRS / event) **domain bazında join-yolu tablosu** ile belirlensin (§5.1 #9) |
| Tur 3 — Oy | **18 kabul / 2 çekimser / 0 red → KABUL** |
| Sonuç | **KABUL** (18/2/0) |
| Karara dönüşen şartlar | 3 şart §5.1'e satır olarak eklendi: **#7** 18 sayısal yetki ADR-040'ta (ADR-003 yalnız mimari karar) · **#8** ref-check job + cross-DB entegrasyon testi · **#9** domain başına join-yolu tablosu |
| Çözüm | Şart 1 → 9-vs-18 çelişkisi kapatıldı (sayısal yetki tek dosyada); şart 2 → DevOps/QA uyarıları (migration orkestrasyonu + entegrasyon testi) kapatıldı; şart 3 → join yolları domain bazında kapatıldı. Tech Lead: ✅ |

---

*ADR-003 v1.0.0 | 2026-09-24 | Created — CoreMusic Vault (.decisions/ yeni seri; slug sayısız: `multi-db-bcnf`)*
*Authority: ADR-003 Karar Metni · Mode: Red Team · Human Mode · Truth Mode*
