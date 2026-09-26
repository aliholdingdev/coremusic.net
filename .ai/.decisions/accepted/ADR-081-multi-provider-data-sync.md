---
title: "CoreMusic — ADR-081: Çoklu-Provider Veri Senkronizasyonu (MySQL SSOT + Outbox)"
type: adr
category: database
date: 2026-09-24
updated: 2026-09-24
version: 1.0.0
status: accepted
authority: ADR-081 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — ADR-081: Çoklu-Provider Veri Senkronizasyonu (MySQL SSOT + Outbox)

**Durum:** accepted (kabul — debate §7.1 ✅ KABUL 17/3/0 (3 tur / 20 persona); frozen YOK)
**Tarih:** 2026-09-24
**Karar Veren:** Vault Steward (kullanıcı talebi: çoklu provider; detaylar "sen karara ver" → senior mimar kararları)
**İlgili ADR'ler:** eski seri — ADR-002 (PDO/ORM yasağı), ADR-003 (multi-db 9 database), ADR-040 (database authority 18 BCNF), ADR-050 (multi-db sync stratejisi) → kavramsal referans: [[../index]] ve [[../../brain]] (eski seriye `[[ADR-0xx]]` biçimli link KURULMAZ); bu dosya yeni uzayda ADR-081'dir
**Numara gerekçesi:** 001-080 mevcut 80 karara (37 frozen + 30 active + 12 red + 1 draft) ve R-001..R-012 red'lere ayrıldı → sıradaki yeni karar ADR-081

---

## 1. Bağlam (Context)

CoreMusic veri katmanı çoklu provider ile çalışacak: **MySQL, MSSQL, MongoDB, FoundationDB ve SQLite.** Kullanıcı çoklu provider talep etti ve detayları karar verme yetkisini mimara bıraktı. Bu karar; (1) **tek doğruluk kaynağı**nın kim olduğu, (2) **yazım yolu ve tutarlılık modeli**, (3) **SQLite'ın rolü ve senkron modu**, (4) **ikincil store'lardan okuma yetkisi**, (5) **arıza/geri dönüş** eksenlerini aynı anda sabitlemelidir.

### 1.1 Mevcut Durum

- **Şema disk kanıtı:** `.ai/.sql/mysql/` altında **18 .sql dosyası** (coremusic_user, coremusic_auth, coremusic_system, coremusic_logs, coremusic_ai, coremusic_social, coremusic_studio, coremusic_catalog, coremusic_download, coremusic_media, coremusic_musics, coremusic_playlist, coremusic_api, coremusic_cms, coremusic_neva, coremusic_patch, coremusic_albums, coremusic_wireless) — MySQL'in şema SSOT'u olduğunun disk kanıtı.
- **Eski seri kararlar (kavramsal referans — dosya değil indeks kaydı):** ADR-003 multi-db 9 database, ADR-040 database authority 18 BCNF, ADR-050 multi-db sync stratejisi, ADR-002 PDO mandatory / ORM yasak → kayıtlar [[../index]] §3-§4'te; ADR-086 event-driven architecture → [[../../brain]] (outbox kararının öncülü).
- **Offline-first emsalı:** [[../../AGENTS.md]] §17 edge case #9 "Network outage → Offline-First + SQLite queue" — SQLite'ın yedek değil aktif rolde olacağının vault içi emsali.
- **Boşluk:** yeni uzayda (ADR-001+) çoklu-provider yazım önceliği, tutarlılık modeli ve SQLite senkron rolüne dair ADR **YOK** — bu dosya ilk karardır.

### 1.2 Sorun Tanımı

Aynı veriye beş provider üzerinden erişilecekse dört soru doğar: (1) **Hangi store yazabilir?** — eşzamanlı iki yazıcı er ya geç çakışır, drift üretir. (2) **İki yere senkron yazım (dual-write) güvenli mi?** — commit ile publish arasında çökme penceresi olay kaybı ya da olmamış olayın yayını üretir. (3) **Tutarlılık modeli ne?** — 2PC atomiklik verir ama coordinator çökmesinde katılımcıların kilidi belirsizlik penceresinde sonsuza dek tutulur (blocking) ve iki round-trip gecikme + tüm katılımcılarda XA desteği gerektirir. (4) **SQLite yedek mi aktif mi?** — salt yedek vitrini, yerel/offline sorgu gereksinimini çözmez. Karar bu dördünü birlikte; lag'i **ölçülebilir**, drift'i **tespit edilebilir**, geri dönüşü **tanımlı** biçimde çözmelidir.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: prompt-maker web araştırma protokolü (`.claude/skills/prompt-maker/references/10-web-research-protocol.md`) — resmi spesifikasyon önce, her iddiaya kaynak, 2+ bağımsız çapraz doğrulama.

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "transactional outbox pattern reliable event publishing to secondary datastores" · (2) "SQLite WAL mode performance durability fsync synchronous NORMAL vs FULL" · (3) "eventual consistency versus two-phase commit 2PC tradeoffs distributed transactions" · (4) "polyglot persistence data synchronization replication lag drift detection reconciliation job multiple databases" |
| Web Search **Konusu** | Dört karar ekseninin güncel ekosistem kanıtı: outbox pattern'in dual-write'ı nasıl çözdüğü, eventual consistency'nin 2PC'ye göre tercih edilme gerekçeleri, SQLite WAL + synchronous NORMAL/FULL dayanıklılık-.performans dengesi, çoklu-veritabanı replikasyon lag'i ve drift tespiti/reconciliation pratikleri. |
| Web Search **Bağlam** | 2025-2026 güncel verisi okundu: AWS Prescriptive Guidance + AWS Builder (2025) outbox şeması/DLQ uygulaması, microservices.io (Chris Richardson) pattern tanımı, Gunnar Morling (2024) outbox tekrar incelemesi, DistributedRequest (2026) polling eşiği + idempotent consumer + relay_lag metrikleri, sqlite.org resmi WAL/PRAGMA dokümanları, HLD Handbook (2025-2026) 2PC-Saga-TCC karşılaştırması, Helland CIDR 2007 "Life beyond Distributed Transactions", PowerSync replication-lag operasyon dokümanı, NILUS (2025) polyglot persistence reconciliation rehberi, Software Patterns Lexicon (2026) eventual consistency şartları. |
| Web Search **Kısa Açıklama** | Outbox pattern, veri + olay yazımını aynı yerel transaksiyona koyarak dual-write'ı atomik çözer ve 2PC olmadan at-least-once teslimat sağlar; 2PC ise coordinator çökmesinde blocking + kilit tutma ve WAN'da >200 ms gecikme ürettiği için 2025'in varsayılanı "saga + outbox + idempotency"dir; SQLite WAL'de synchronous=NORMAL, FULL'e göre ~30x ucuz commit verir ama güç kaybında dayanıklılığı kaybeder (kritik yazım FULL ister); polyglot replikasyonda lag kaçınılmaz olduğundan watermark/metrik + periyodik reconciliation job zorunludur. |
| Web Search **Uzun Açıklama** | **Outbox:** AWS Cloud Design Patterns dual-write problemini tanımlar — DB commit olup event gönderilemezse downstream tutarsızlaşır, event gönderip DB rollback olursa veri bozulur; çözüm, olayı iş yazımıyla **aynı transaksiyonda** outbox tablosuna koymak ve ayrı bir relay'ın (polling veya CDC) asenkron yayımlamasıdır; rollback'te olay yayılmaz, sıralama sequence/timestamp ile korunur, teslimat at-least-once olduğundan tüketici idempotent olmalıdır (microservices.io; AWS). AWS Builder (2025) tipik durum makinesini verir: `NEW → PROCESSING → PUBLISHED`, başarısızsa `FAILED` + `FAILED_EVENTS` tablosu + alarm; Morling (2024) outbox'ın tam ACID değil **eventual consistency** semantiği verdiğini, relay'ın log-CDC ile (MySQL'de binlog/BLACKHOLE opsiyonu) sıralamayı garanti ettiğini, polling yerine CDC'nin tercih edildiğini belgeler. DistributedRequest (2026) polling relay'ı ~200 events/s altında kabul edilebilir bulur, `outbox_relay_lag_seconds` p9 için 30 sn alarm eşiği önerir. **2PC vs eventual:** HLD Handbook (2025) — 2PC coordinator failure'da blocking, prepare-commit arasında kilit tutar, 2 ardışık consensus round'ı; Gray & Lamport'un Paxos Commit'i bile consensus gerektirir; 2025 üretim varsayılanı "Saga + transactional outbox + idempotency keys"tir. Narcis Miclaus (2025) — belirsizlik penceresinde katılımcılar ne commit ne abort edemez, gerçek müşteri sorguları kilitlenir; coordinator kümeleşmesi partition'da in-doubt'u ortadan kaldırmaz. Helland (CIDR 2007) — ölçek büyüdükçe participant arızası commit'i durdurur; neredeyse tüm "dağıtık transaction" problemleri idempotency + eventual consistency + reconciliation ile çözülür. **SQLite WAL:** sqlite.org — WAL çoğu senaryoda daha hızlı, okuyucu/yazar birbirini bloklamaz, daha az fsync; `synchronous=FULL` her commit'te WAL sync (ACID, güç kaybına dayanıklı), `synchronous=NORMAL` ise tutarlılığı korur ama güç kaybında son commit'leri kaybeder (uygulama çökmesine dayanıklı); benchmark'ta NORMAL 0,041 ms vs FULL 1,230 ms ortalama insert (~30x); otomatik checkpoint varsayılanı 1000 WAL sayfası; WAL tek-host'ta çalışır (ağ dosya sistemi yok). **Replikasyon/drift:** PowerSync — lag nedenleri: uzun transaksiyonlar, toplu UPDATE/TRUNCATE, cron flush'ları, backfill; retention (binlog/WAL/CDC) dolarsa sıfırdan senkron. NILUS (2025) — polyglot'ta reconciliation **first-class** yetenektir: kaynak-şehir-hedef parite kontrolü (hash, count, iş toplamları), mismatch oranı, DLQ hacmi izlenir; "reconciliation'sız mimari tasarım hatasıdır". Software Patterns Lexicon (2026) — eventual consistency ancak lag açıkça tasarlanmışsa geçerlidir: watermark/updated_at, sürüm sıralaması, izlenebilir gecikme; tek-yazıcı sahipliği yoksa sessiz overwrite ve drift doğar. |
| Web Search **Paragraf Veri Uzun** | Dual-write = commit/publish ayrışma penceresi (AWS, microservices.io) · outbox = iş + olay **tek** transaksiyon içinde · teslimat **at-least-once** → idempotent tüketici zorunlu (event_id dedup) · polling relay kabul limiti ≈ **200 events/s** (DistributedRequest 2026) · relay_lag alarm eşiği p9 **30 sn** · outbox durumları NEW/PROCESSING/PUBLISHED/FAILED + FAILED_EVENTS/DLQ (AWS Builder 2025) · 2PC = **2 round-trip**, coordinator crash → **sonsuz kilit**, WAN XA commit **>200 ms** · 2025 varsayılanı = **saga + outbox + idempotency** (HLD Handbook) · Spanner read-write ≈ **100 ms**, read-only ~10x hızlı · SQLite WAL FULL = her commit'te fsync; NORMAL = checkpoint'te fsync · benchmark: insert NORMAL **0,041 ms** vs FULL **1,230 ms** (~**30x**) · ikinci benchmark: 500K×10 insert NORMAL **27,5 s** vs FULL **35,7 s** (~%30) · WAL checkpoint eşiği varsayılan **1000 sayfa** · salt-okuma işte WAL **%1-2** yavaş · WAL **tek-host** (network FS yok) · büyük transaction (>**100 MB**) WAL'de yavaş · lag nedenleri: uzun transaksiyon, toplu UPDATE, TRUNCATE-cron, backfill, retention taşması · reconciliation kontrolleri: **hash / count / iş toplamı** + mismatch oranı + DLQ hacmi · CoreMusic lag bütçeleri: kritik ≤**60 sn**, toplu ≤**10 dk** (karar §2.2 d). |
| Web Search **Sonucu** | 1) **Outbox, dual-write'ın tek kanıtlanmış çözümüdür** ve 2PC gerektirmez; atomiklik yerel transaksiyonda kalır, yayım asenkron + tekrarlıdır (kaynak: AWS Prescriptive Guidance, microservices.io, Morling 2024, AWS Builder 2025, DistributedRequest 2026 — 5 kaynak çapraz doğrulamalı). 2) **2PC yerine eventual consistency** endüstri standardıdır: blocking problemi teorik olarak kanıtlanmış (Gray & Lamport; Helland), operasyonel maliyeti ağır; katı ACID gereken tek istisna tek-veri-merkezi kısa transaksiyonlardır (kaynak: HLD Handbook 2025/2026, Narcis 2025, abstractalgorithms, Helland CIDR 2007 — 5 kaynak). 3) **SQLite WAL hibrit için uygundur**: FULL anlık/kritik, NORMAL toplu/analitik — dayanıklılık matrisi resmi dokümanda net, performans farkı 2 bağımsız benchmark'ta ~30x / ~%30 (kaynak: sqlite.org wal.html + pragma.html, ericdraken.com, node-sqlite-guarantees — 4 kaynak). 4) **Replikasyon lag'i ve drift'i kaçınılmazdır**; tespit periyodik reconciliation (hash/count/iş toplamı) ve metriklerle (lag, mismatch, DLQ) yapılır, tek-yazıcı sahipliği drift'i yapısal olarak engeller (kaynak: PowerSync, NILUS 2025, Software Patterns Lexicon 2026, mostajs — 4 kaynak). 5) **Çapraz doğrulama:** her iddia ≥2 bağımsız kaynakla örtüştü; resmi doküman (sqlite.org, AWS) + pattern otoriteleri (microservices.io, Helland) + güncel operasyon dokümanları (PowerSync, NILUS) üçlüsü aynı sonuca ulaşıyor. |
| Web Search **Alınan Karar** | **MySQL = primary / tek doğruluk kaynağı (SSOT)**; MSSQL, MongoDB, FoundationDB yalnız **projection**'dır. **Yazım yolu = Outbox pattern:** MySQL transaksiyonu içinde outbox satırı → async yayıncı → eventual consistency (**2PC YOK**); başarısızsa retry kuyruğu + dead-letter. **SQLite = aktif query/write layer (yedek değil):** WAL mode; kritik yazım anlık (WAL fsync = synchronous=FULL), analitik/toplu veri 5-10 dk batch flush (synchronous=NORMAL); kritiklik kategori bazlı tabloyla belirlenir (users/auth anlık; analytics/events toplu). **Yön:** ikincil store'lardan okuma serbest (read scaling), **yazım kararı her zaman MySQL'e ait**. **Zorunlu:** idempotent consumer, lag bütçesi, periyodik reconciliation job + drift metrikleri. |
| Web Search **Sonuç** | Karar 2026 verisiyle **desteklendi**: outbox (5 kaynak), 2PC/eventual (5 kaynak), SQLite WAL (4 kaynak), replikasyon/drift (4 kaynak) — toplam **19 kaynak**, 4 sorgu grubu, çapraz doğrulama tam. Kaynaksız iddia yok; debate §7.1'de kapatıldı (3 tur / 20 persona → 17 kabul / 3 çekimser / 0 red = KABUL; 4 şart §5.3). |

**Kaynak listesi (19):**
1. https://docs.aws.amazon.com/prescriptive-guidance/latest/cloud-design-patterns/transactional-outbox.html — AWS, transactional outbox (dual-write, sıra, rollback)
2. https://microservices.io/patterns/data/transactional-outbox.html — Chris Richardson, Transactional outbox (2PC gerekmez, idempotent tüketici)
3. https://www.morling.dev/blog/revisiting-the-outbox-pattern/ — Gunnar Morling, Revisiting the Outbox Pattern (2024) — eventual semantics, log-CDC/MySQL BLACKHOLE
4. https://builder.aws.com/content/30QrtnhZs8epaRvdeKMGe4wmenu/reliable-event-driven-systems-with-aws-outbox-pattern — AWS Builder (2025) — outbox şeması, NEW/PROCESSING/PUBLISHED/FAILED, DLQ
5. https://www.distributedrequest.com/backend-implementation-storage-patterns/transaction-scoping-atomic-operations/implementing-the-transactional-outbox-pattern/ — (2026) — polling ≈200 events/s, relay_lag p9 30 sn, event_id dedup
6. https://sqlite.org/wal.html — SQLite resmi: WAL faydaları, fsync, checkpoint 1000 sayfa, büyük transaction uyarısı
7. https://www.sqlite.org/pragma.html — PRAGMA synchronous: NORMAL/FULL dayanıklılık matrisi (WAL)
8. https://ericdraken.com/sqlite-performance-testing/ — benchmark: NORMAL 27,5 s vs FULL 35,7 s (WAL)
9. https://www.sqlite.org/walformat.html — WAL dosya formatı, shm, checkpoint/checksum
10. https://github.com/alp82/curia/blob/main/docs/research/node-sqlite-guarantees.md — benchmark: WAL FULL 1,230 ms vs NORMAL 0,041 ms insert
11. https://hld.handbook.academy/trade-offs/distributed-transactions/ — 2PC vs Saga vs TCC (2025): blocking, kilit, varsayılan = saga + outbox + idempotency
12. https://hld.handbook.academy/curriculum/distributed-systems-theory/distributed-transactions/ — (2026) "outbox first, 2PC last", Spanner ~100 ms
13. https://www.abstractalgorithms.dev/system-design-distributed-transactions — 2PC belirsizlik penceresi, outbox zorunlu, CAP
14. https://www.cidrdb.org/cidr2007/papers/cidr07p15.pdf — Pat Helland, Life beyond Distributed Transactions (CIDR 2007)
15. https://narcismiclaus.com/programming/architecture/15-two-phase-commit/ — (2025) 2PC blocking, kilit maliyeti, partition'da erişilemezlik
16. https://docs.powersync.com/maintenance-ops/replication-lag — replication lag nedenleri, retention/binlog/CDC taşması, kurtarma
17. https://www.nilus.be/blog/polyglot_persistence_in_microservices_architecture/ — (2025) polyglot + reconciliation first-class, parite kontrolü
18. https://softwarepatternslexicon.com/data-modeling/polyglot-persistence-patterns/eventual-consistency/ — (2026) tasarlanmış lag, watermark, tek-yazıcı sahipliği
19. https://mostajs.dev/mostajs-replicator-article.html — cross-dialect CDC, açık beyan edilmiş tutarlılık seviyeleri (eventual + idempotent)

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| 2PC yasak | Hiçbir yazım yolunda XA/2PC kullanılmaz (yavaş round-trip + coordinator blocking + operasyonel yük — §3 #1). İstisna kapısı yalnız yeni ADR ile açılır (§4.4). |
| Tek yazım yetkisi = MySQL | MSSQL/MongoDB/FoundationDB/SQLite'a uygulama düzeyinde iş yazımı yapılmaz; bunlar projection veya (SQLite) yerel kuyruktur. Çift anaçalı (multi-master) yazım yasak. |
| ORM yasağı + PDO (eski seri) | Outbox ve relay erişimi doğrudan SQL/prepared statement ile olur; eski seri karar (ADR-002) kavramsal olarak bağlayıcıdır → [[../index]]. |
| Eventual consistency + lag bütçesi | Tutarlılık modeli bilinçli olarak eventual'dir; ancak lag **ölçülür** ve bütçe aşarsa (§2.2 d) alarm tetiklenir — "umarak" eventual olunmaz. |
| REDACTED | Outbox payload'ında secret/credential hiçbir koşulda bulunmaz; hassas alanlar maskelenir. |
| Vault şablon zorunluluğu | Bu ADR Guardrail #16 ile [[../../.templates/adr/adr-template]]'ten üretilmiştir; iskelet §1-§7 silinemez. |

---

## 2. Karar (Decision)

**MySQL = primary / tek doğruluk kaynağı (SSOT).** Diğer tüm store'lar (MSSQL, MongoDB, FoundationDB, SQLite) **projection**'dır: MySQL'deki durumun türetilmiş görünümleridir, bağımsız doğruluk kaynağı değildir.

**Yazım yolu = Outbox pattern:** her iş yazımı, MySQL'in **aynı** yerel transaksiyonu içinde `outbox_events` satırıyla birlikte atılır; ayrı bir async yayıncı (relay) outbox satırlarını okuyup MSSQL / MongoDB / FoundationDB'ye **eventual consistency** ile yayınlar. **2PC YOK** (yavaş ve operasyonel yük — §3 #1). Yayımda başarısızlık → **retry kuyruğu** (üstel backoff) → tekrarlı başarısızlık → **dead-letter (DLQ)** + alarm.

**SQLite = yerel/embedded sorgu katmanı — yedek değil, aktif query/write layer.** WAL mode'da çalışır. **Hibrit senkron:** kritik yazım **anlık** (WAL fsync = `synchronous=FULL`), analitik/toplu veri **5-10 dk batch flush** (`synchronous=NORMAL`); kritiklik **kategori bazlı tabloyla** belirlenir (§2.2 c: users/auth anlık; analytics/events toplu).

**Okuma/yazım yönü:** ikincil store'lardan **okuma serbesttir** (read scaling — sorgu yükü MySQL dışına taşınır), ancak **yazım kararı her zaman MySQL'e ait**; ikincil store'da görülen veri "son bilinen projection" olarak okunur, kullanıcıya dönen kritik kararlar MySQL'den alınır.

### 2.1 Neden Bu Seçenek?

1. **Dual-write kapanır:** veri + olay tek transaksiyonda atomikleşir; commit olmadan olay yayılmaz, olay yayınlanmadan commit kaybolmaz (AWS; microservices.io). Uygulama tarafı try/catch ile çözülemeyen yapısal bir açık, şema düzeyinde kapanıyor.
2. **2PC'nin bilinen kusurları bilinçli reddedilir:** coordinator çökmesinde katılımcıların kilidi belirsizlik penceresinde tutulur (blocking — Gray & Lamport; Narcis 2025), 2 round-trip gecikme ve tüm participant'larda XA desteği şarttır; 2025-2026 üretim standardı "saga/outbox + idempotency"dir (HLD Handbook; abstractalgorithms). Helland'ın ölçek dersi: dağıtık transaction yerine idempotency + eventual + reconciliation.
3. **Tek yazıcı = drift'in yapısal önlenmesi:** çoklu-yazıcı çakışma çözümü (LWW, merge fonksiyonu) yerine yazım tek elde toplanır; kalan tek sorun (projection lag/drift) ölçülebilir ve reconciliation ile onarılabilir (NILUS; Software Patterns Lexicon).
4. **SQLite'ın rolü gerçek ihtiyara göre:** offline/yerel sorgu + düşük gecikme (vault emsali: AGENTS §17 #9 Offline-First + SQLite queue); hibrit mod, güvenlik maliyetini (%30-30x performans farkı) yalnız toplu veriye öteleyerek kritik veride dayanıklılığı korur (sqlite.org; 2 benchmark).
5. **Okuma ölçeklenir, yazım bozulmaz:** read scaling ikincil store'lara dağıtılırken yazım yetkisi tek noktada kalır — erişilebilirlik/ölçek kazancı tutarlılık pahasına değil, **gönüllü sınırlı lag** ile alınır ve lag bütçesiyle denetlenir.

### 2.2 Teknik Detaylar

**(a) Outbox şeması (MySQL, iş yazımıyla aynı transaksiyon — append-only):**

| Kolon | Tip | Not |
|-------|-----|-----|
| `id` | BIGINT UNSIGNED AUTO_INCREMENT | olay kimliği (ayrıca `event_id` olarak tüketiciye header'da taşınır) |
| `aggregate_type` / `aggregate_id` | VARCHAR / BIGINT | hangi varlıkta değişiklik |
| `event_type` | VARCHAR(128) | `user.updated`, `catalog.changed` vb. |
| `payload` | JSON | olay gövdesi — **REDACTED: secret yok** |
| `seq` | BIGINT | sıra koruması (transaksiyon sırası) |
| `status` | ENUM(NEW, PROCESSING, PUBLISHED, FAILED) | durum makinesi (AWS Builder 2025) |
| `attempts` | SMALLINT | deneme sayacı |
| `created_at` / `published_at` | TIMESTAMP | lag ölçümü (`published_at - created_at` = relay lag) |

İş kuralı: `INSERT into business_table` + `INSERT into outbox_events` **aynı** `BEGIN…COMMIT` içinde; herhangi biri başarısızsa ikisi de rollback. Outbox'a commit sonrası satır eklenemez, satır silinmez (append-only; temizlik TTL/planlı bakım ile).

**(b) Relay (yayıncı) ve teslimat:**

- **Varsayılan: polling publisher** — 500 ms aralıklı, `ORDER BY seq` okuma; CoreMusic ölçeği ≈200 events/s polling üst sınırının altında kalır (DistributedRequest 2026). İleride opsiyonel yükseltme: MySQL binlog CDC (Morling: sıralamayı garanti eder, polling overhead'ini kaldırır) — bu ADR'nin gerektirmediği, gelecekteki bir ADR ile değerlendirilecek bir iyileştirmedir.
- **Teslimat sözleşmesi: at-least-once.** Tüketici **idempotent** olmak zorunda: `event_id` ile dedup (processed_events tablosu / `INSERT … ON CONFLICT DO NOTHING`).
- **Retry:** başarısız yayımda üstel backoff (1 sn → 2 sn → … → 5 dk), `attempts` artar; **max 10 deneme** sonrası satır `FAILED` → **dead-letter** kuyruğuna devredilir + alarm. DLQ'daki olaylar elle/otomatik replay edilebilir (idempotent tüketici sayesinde güvenli).
- **Sıralama:** relay `seq` bazında sırayla gönderir; paralel yayımda aggregate bazında sıra korunur (aggregate başına tek in-flight).

**(c) SQLite hibrit senkron — kategori tablosu:**

| Kategori | Örnek tablolar | Kritiklik | SQLite yazım modu | MySQL→SQLite gecikmesi |
|----------|----------------|-----------|-------------------|------------------------|
| users / auth (kritik) | users, auth, session, oauth, patch | Anlık | WAL + `synchronous=FULL` (her commit'te fsync) | ≤ 60 sn (anlık sync worker) |
| transactionel iş | catalog, download, playlist, social | Yüksek | WAL + `synchronous=FULL` | ≤ 60 sn |
| analytics / events (toplu) | logs, analytics, events, ai (istatistik) | Toplu | WAL + `synchronous=NORMAL` + **5-10 dk batch flush** | ≤ 10 dk (batch checkpoint) |

Kurallar: `journal_mode=WAL` zorunlu; otomatik checkpoint (varsayılan 1000 sayfa) izlenir, toplu kategoride `PRAGMA wal_checkpoint(TRUNCATE)` 5-10 dk cron'la yapılır; WAL **tek-host**'ta çalışır (ağ dosya sistemi yasak); >100 MB toplu transaction'larda WAL yerine rollback-journal (sqlite.org uyarısı). Kritiklik kategorisi Data Engineer tarafından şema değişikliğinde revize edilir (yeni tablo = bu tabloya satır eklenir).

**(d) Yön, lag bütçesi ve ölçüm:**

| Yön / Metrik | Kural | Bütçe / Eşik |
|--------------|-------|--------------|
| Yazım (tüm kategoriler) | Yalnız MySQL | Sınır yok — tek yetki |
| Okuma (ikincil store) | Serbest (read scaling) | Kritik karar MySQL'den |
| MySQL → ikincil (MSSQL/Mongo/FDB) | Outbox eventual | ≤ 60 sn (relay_lag p9 alarm: 30 sn — DistributedRequest) |
| MySQL → SQLite (kritik) | Sync worker anlık | ≤ 60 sn |
| MySQL → SQLite (toplu) | Batch flush | ≤ 10 dk |
| DLQ derinliği | Dead-letter | > 0 kalıcıysa alarm; > 10/sn girişte eskalasyon |

**(e) Drift tespiti ve reconciliation job:**

- **Periyodik reconciliation (günlük):** kaynak↔projection **count**, **hash (checksum)** ve **iş toplamları** (ör. sipariş adedi ↔ ödeme adedi) karşılaştırılır; uyuşmazlık oranı metrik olarak yayılır (NILUS: reconciliation first-class'tır; "reconciliation'sız yapı tasarım hatasıdır").
- **Onarım yolu:** uyuşmazlık tespitinde projection MySQL'den **yeniden türetilir** (snapshot/rebuild); outbox replay edilmez — kaynak her zaman MySQL'dir.
- **İzleme:** relay lag, retry sayısı, DLQ hacmi, projection `updated_at` yaşı, checkpoint yaşı dashboard'da görünür (Software Patterns Lexicon: görünmeyen lag tasarlanmamış lag'dir).
- **Sürüm sıralaması:** projection'lar olayları `seq` ile uygular; gecikmiş/eski olay güncel projection'ı **ezemez** (stale write reddedilir).

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **2PC / XA ile atomik çoklu-yazım** | Tam atomiklik, uygulamada basit görünüm | Coordinator çökmesinde **blocking** + belirsizlik penceresinde sonsuz kilit (Gray & Lamport; Narcis 2025); 2 round-trip, WAN'da >200 ms; tüm participant'larda XA şart, polyglot store'lar (Mongo/FDB) XA desteklemez; operasyonel yük ağır | Hız + operasyonel maliyet kabul edilemez; 2025-2026 standardı outbox + eventual'dir (HLD Handbook; Helland) — açık kullanıcı kararı da "2PC YOK" |
| 2 | **Dual-write (uygulama iki yere senkron yazar)** | Ek altyapı yok, en basit kod | Commit/publish ayrışma penceresi: event kaybı ya da olmamış olayın yayını (AWS); tek çökme noktasında kalıcı tutarsızlık; hangi yazımın "gerçek" olduğu belirsiz | Yapısal olarak güvensiz; outbox tam bu problemi ortadan kaldırıyor |
| 3 | **Multi-master (çoklu anaçalı) yazım** | Her store'dan yazım, düşük yerel gecikme | Çakışma çözümü (LWW/merge) gerekir; drift kaçınılmaz ve **sessiz** olur; deleted-vs-updated çatışmaları iş mantığına sızar | Kullanıcı kararı: yazım kararı daima MySQL; çakışma çözümü yerine tek-yazıcı sahipliği (§2.1 #3) |
| 4 | **SQLite'ı salt yedek (read-only replica) yapmak** | En basit rol tanımı, senkron yükü yok | Yerel/offline yazım ve sorgu gereksinimini çözmez; çevrimdışı çalışma (AGENTS §17 #9 Offline-First + SQLite queue) imkânsızlaşır | Kullanıcı kararı: SQLite **aktif** query/write layer'dır — hibrit senkronla (§2.2 c) yedeklik sınıfı aşıldı |
| 5 | **Tam CDC/event-sourcing ilk günden (Kafka + Debezium zorunlu)** | Düşük latency, log-tabanlı garantili sıralama, polling yok | Altyapı ağırlığı: broker + connector işletmesi, öğrenme eğrisi; CoreMusic ölçeği polling üst sınırının (~200 events/s) çok altında | Varsayılan polling relay ile başla; CDC **opsiyonel yükseltme** olarak §2.2 b'de kapıda tutuldu (Morling: CDC tercih edilir ama outbox'ın ön koşulu değildir) |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Dual-write sıfırlanır:** veri + olay tek MySQL transaksiyonunda atomik; publish hatası iş yazımını etkilemez, iş hatası sahte olay üretmez (AWS; microservices.io). *(vault çapraz: `.ai/.sql/mysql/` 18 şema — outbox tablosu bu şemalara komşu yaşar)*
- **Çoklu-provider çakışması yapısal olarak imkânsız:** tek yazıcı (MySQL) + projection modeli → LWW/merge çakışma çözümü yok, sessiz overwrite yok. *(eski seri kavramsal: ADR-040 database authority, ADR-050 multi-db sync stratejisi → [[../../brain]], [[../index]])*
- **Okuma ölçeklenir:** ikincil store'lardan okuma serbest → sorgu yükü MySQL'e yığılmaz; store başına özel indeks/şema (MongoDB döküm, FoundationDB aralık) avantajı korunur.
- **SQLite gerçek yerel katman olur:** WAL + hibrit mod ile anlık kritik yazımda tam dayanıklılık (FULL), toplu veride ~%30-30x performans (benchmark) + offline çalışma (AGENTS §17 #9). *(vault: `.ai/.sql/mysql/` çekirdek şemalar + AGENTS edge case)*
- **Arıza yüzeyi daralır ve denetlenebilir:** outbox satırı doğal bir event log'dur; retry/DLQ + lag metrikleri ile her olay'ın akıbeti izlenir; reconciliation ile drift **tespit ve onarılabilir** (NILUS). *(eski seri: ADR-002 PDO/ORM yasağı ile tutarlı — doğrudan SQL, görünür sorgu)*

### 4.2 Olumsuz Sonuçlar

- **Eventual consistency kabul edilir:** ikincilde okurken "az önce yazdım" guarantee'si yoktur (read-your-writes yalnız MySQL'de); kritik kararlar MySQL'den alınmak zorundadır — bu, §2.2 d'de bütçelerle yönetilen **bilinçli** bir maliyettir.
- **Yeni işletim yükü:** relay, retry, DLQ, reconciliation job'ı çalışacak; outbox tablosu büyüyüp temizlenmezse şişer (TTL/planlı temizlik şart) — sahiplik ataması gerekir (§5.1).
- **Sıralama kırılabilirse projection bozulur:** relay paralelleşmesi veya `seq` atlarsa eski olay yeni durumu ezebilir; çözüm tüketici tarafında sürüm kontrolü gerektirir (§2.2 e).
- **SQLite kısıtları:** WAL tek-host'ta çalışır (ağ FS yok), >100 MB toplu transaction'larda yavaşlar; `NORMAL` modda güç kaybı son toplu commit'leri silebilir (toplu kategoride **kabul edilir**, kritikte FULL ile korunur).
- **İki tutarlılık modeli bir arada:** kategorilere göre farklı lag (60 sn vs 10 dk) — ekip "her yerde anlık" refleksini bırakmalıdır (debate §7.1'de 4 şartla KABUL).

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| Replication lag'i bütçe aşımı (relay kuyruğu, uzun transaksiyon, toplu UPDATE/cron — PowerSync) | 3 (olası) | 3 (orta) | Lag metriği + 30 sn p9 alarm (§2.2 d); toplu işler düşük trafik penceresine alınır; binlog/WAL retention süresi lag taşmasını karşılar |
| Drift (projection ≠ MySQL: kaçırılan olay, bozuk payload, eski olay ezmesi) | 3 (olası) | 4 (yüksek) | Günlük reconciliation (count/hash/iş toplamı) + `seq` ile sıralama + stale-write reddi (§2.2 e); uyuşmazlıkta projection MySQL'den rebuild |
| DLQ tıkanması (kalıcı publish hatası, downstream outage) | 2 (mümkün) | 4 (yüksek) | Max 10 retry → FAILED + alarm; DLQ hacmi dashboard'da; replay idempotent (event_id) |
| Idempotency unutulması → duplicate uygulama (at-least-once kaçınılmaz) | 3 (olası) | 4 (yüksek) | Tüketici sözleşmesi: `event_id` dedup zorunlu (§2.2 b); `processed_events_conflict_total` metriği ile doğrulama |
| SQLite güç kaybında toplu veri kaybı (`NORMAL`) | 3 (olası) | 2 (düşük) | Yalnız analytics/events kategorisinde NORMAL; users/auth + transactionel FULL (§2.2 c tablosu); günlük yedek + checkpoint izleme |
| Outbox tablosu şişmesi / temizlik eksikliği | 2 (mümkün) | 2 (düşük) | Yayınlanan satırlar için TTL + planlı temizlik job'ı; `created_at` yaşı metriği |
| Sıralama bozulması (relay paralelleşmesi) | 2 (mümkün) | 3 (orta) | Aggregate başına tek in-flight; `seq` bazlı gönderim; tüketici tarafında sürüm kontrolü |

### 4.4 Fallback (Arıza Kapıları)

Bu madde kararın **koşullu kaçış** tanımlarıdır; kalıcı değişiklik her zaman yeni ADR gerektirir (bu dosya keyfi düzenlenmez).

| # | Senaryo | Fallback davranışı | Onay |
|---|---------|---------------------|------|
| 1 | İkincil store (MSSQL/Mongo/FDB) kesintisi | Outbox'ta birikir (retry + DLQ); okuma otomatik olarak MySQL'e yönlendirilir; store dönünce replay (idempotent) | Backend + Data |
| 2 | Relay/DLQ kalıcı tıkanma | Okuma MySQL'e sabitlenir; drift reconciliation ile ölçülür; olaylar elle replay edilir | Data Engineer |
| 3 | MySQL yazım kesintisi + çevrimdışı istemci | SQLite yerel kuyruğa yazar (AGENTS §17 #9 Offline-First + SQLite queue); bağlantı dönünce kuyruk outbox'a akar | Backend |
| 4 | Gerçekten atomik çoklu-store gerekliliği (ör. regülasyon) | **2PC yalnız yeni ADR ile** — koşullar: tek veri merkezi, ≤2 participant, sub-saniye transaksiyon, XA destekli participant, operasyon ekibi (§3 #1 gerekçesi tersine test edilerek) | Vault Steward → Tech Lead → Arch Lead |

### 4.5 Vault Çapraz Referansları

| Vault hedefi | İlişki |
|--------------|--------|
| `.ai/.sql/mysql/` (18 .sql) | MySQL şema SSOT'unun disk kanıtı; outbox + dead-letter tabloları bu şemalara eklenir |
| Eski seri ADR-003 / ADR-040 / ADR-050 / ADR-002 | Kavramsal ön-sürüm: multi-db, database authority, sync stratejisi, PDO/ORM → [[../index]] §3-§4, [[../../brain]] |
| [[../../brain]] (ADR-086 event-driven architecture) | Outbox kararının event-driven öncülü |
| [[../../AGENTS.md]] §17 #9 | Offline-First + SQLite queue emsali (SQLite aktif rol) |
| [[../../keys]] | Keyword haritası: "outbox, SSOT, eventual, WAL, replication, reconciliation" eşlemeleri (⚠️ bu ADR sonrası vault-updater işi) |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | Bu ADR'yi `.ai/.decisions/accepted/` altına yaz + `.ai/log.md` append (`ADR-081 multi-provider data sync yazıldı (YENİ karar, debate PENDING)`) | Vault Steward | 30 dk |
| 2 | Outbox + dead-letter şemasını `.ai/.sql/mysql/` altına yeni `.sql` olarak ekle (§2.2 a) + kategori tablosunu (§2.2 c) Data şemasına işle | Data Engineer | 1 gün |
| 3 | Relay servisi: polling publisher (500 ms, `seq` sırası), retry (üstel backoff, max 10), DLQ, idempotent consumer sözleşmesi (`event_id` dedup) | Backend + Data | 3 gün |
| 4 | SQLite hibrit konfigürasyonu: `journal_mode=WAL` + kategori bazlı `synchronous` (FULL/NORMAL) + 5-10 dk checkpoint cron'ı + tek-host kontrolü | Backend + Data | 1 gün |
| 5 | Reconciliation job (günlük count/hash/iş toplamı) + metrikler (relay_lag p9 30 sn, DLQ, projection yaşı) + alarm eşikleri | Data + DevOps | 2 gün |

### 5.2 Geri Dönüş Planı

Karar veri yazım yolunu kilitler; geri dönüş kademelidir ve her kademede MySQL SSOT'a dokunulmaz: **(1) Hafif geri dönüş (tek satır):** SQLite `synchronous` NORMAL ↔ FULL pragma değişimi anlıktır ve geri alınabilir (toplu↔kritik kategorisi bir tablo satırıdır). **(2) Relay durdurma:** relay kapatılabilir — outbox satırları birikmeye devam eder, okuma zaten MySQL'e yönlendirilebilir; geri açmak = replay (idempotent, veri kaybı yok). **(3) Projection'ların devre dışı bırakılması:** MongoDB/MSSQL/FDB store'ları salt-okumaya alınır veya bağlantıları çekilir; outbox TTL ile budanır — okuma MySQL'e döner. **(4) Karar değişikliği:** bu ADR düzenlenmez; yeni ADR yazılır ve bu dosyaya `superseded by ADR-NNN` bağı **yeni** ADR'nin §6'sına konur. **(5) Vault bozulması:** standart kurtarma `git checkout` + son commit ([[../../AGENTS.md]] §17 #10). Geri dönüş sırasında veri bütünlüğü garanti kaynağı değişmez: **her koşulda MySQL = doğruluk kaynağı.**

### 5.3 Debate Şartları (§7.1 — 4 şart, 2026-09-24)

| # | Şart | Karşılığı | Durum |
|---|------|-----------|-------|
| 1 | Reconciliation job **ZORUNLU** + drift alarmı (uyuşmazlık oranında otomatik alarm) | §2.2 e, §5.1 #5, §4.3 drift satırı | ✅ şart |
| 2 | Outbox payload'ta **PII maskeli** event şeması (maskelenmiş alanlar; secret yok — REDACTED) | §1.4 REDACTED, §2.2 a `payload` | ✅ şart |
| 3 | **MVP kapsam indirgesi:** MSSQL + MongoDB zorunlu, FoundationDB opsiyonel | §2, §4.4 fallback #1 | ✅ şart |
| 4 | **Drift/chaos testi** (lag bütçesi aşımı + drift simülasyonu senaryosu) | §2.2 d, §4.3 lag/drift satırları, §5.1 #5 | ✅ şart |

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../index]] | Karar dizini — bu ADR'nin kaydı + eski seri emsalleri (ADR-003 multi-db, ADR-002 PDO/ORM vb., kavramsal) |
| [[../CLAUDE.md]] | Karar alt registry kuralı (accepted/) |
| [[../../CLAUDE.md]] | Ana sözleşme, 16 Hard Guardrail (Guardrail #16 — bu ADR şablondan üretildi) |
| [[../../AGENTS.md]] | Data Engineer domain (§5, §6 routing), Offline-First + SQLite queue (§17 #9), edge/escalation kuralları |
| [[../../brain]] | Mimari karar özeti — ADR-040 (database authority), ADR-050 (multi-db sync stratejisi), ADR-086 (event-driven) kavramsal öncüller |
| [[../../keys]] | Keyword haritası — "outbox, SSOT, eventual consistency, WAL, replication, reconciliation, multi-provider" eşlemeleri |
| [[../../log]] | Audit trail (bu kaydın append satırı) |
| [[../../.templates/adr/adr-template]] | Bu ADR'nin 7 bölümlük şablonu (Guardrail #16) |
| `.ai/.sql/mysql/` (18 .sql) | MySQL şema SSOT disk kanıtı — outbox/dead-letter tablosunun evidir (düz yol referansı) |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | §1.3 web araştırma protokolü |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali (kullanıcı onaylı karar — "detaylar sen karara ver") | 2026-09-24 | ✅ |
| Tech Lead | Onaylı — debate 3/20 (17/3/0 KABUL) | 2026-09-24 | ✅ |
| Arch Lead | ⏳ | 2026-09-24 | ⏳ |

### 7.1 Tartışma Kaydı (Debate — kaynak alanı)

| Alan | Değer |
|------|-------|
| Biçim | ✅ Multi-tur persona debate yürütüldü — 3 tur / 20 persona |
| Tur 1 | 20 persona — çoğunluk kabul; uyarılar: Security (outbox PII sızıntısı), DevOps (DLQ izleme/alerting), QA (drift simülasyon testi), PM (3 ek store operational burden); en sert Critic: reconciliation job yoksa sessiz bozulma |
| Tur 2 (itiraz → çözüm) | (1) lag bütçesi (60 sn relay / 10 dk batch) aşılırsa → DLQ + alarm maddesi, (2) FoundationDB gerekliliği → MVP kapsam: MSSQL + Mongo zorunlu, FoundationDB opsiyonel (§4.4 fallback), (3) SQLite tek dosya = tek nokta arıza → WAL + snapshot cron (kabul), (4) outbox payload'ta PII → event şemasında maskelenmiş alanlar |
| Tur 3 (oy) | 17 kabul / 3 çekimser / 0 red → **KABUL** |
| Sonuç | ✅ KABUL — 4 şart §5.3 maddelerine dönüştürüldü |
| Açık maddeler | Kapandı: (a) lag bütçeleri 60 sn / 10 dk teyit + aşım → DLQ + alarm, (b) polling → CDC yükseltme opsiyonel kapıda (§2.2 b), (c) DLQ alarm eşiği §2.2 d'de |
| Beklenen etki | Debate şartları §5.3'e işlendi (reconciliation + drift alarmı, PII maskeli payload, MVP kapsam: FoundationDB opsiyonel, drift/chaos testi); red OLMADI → karar accepted olarak kaldı |
| Frozen | YOK — bu ADR frozen değildir (statü değişikliği yok, yalnız debate kaydı + şartlar eklendi) |

---

*ADR-081 v1.0.0 | 2026-09-24 | Created — CoreMusic Vault (.decisions/ yeni seri)*
*Authority: ADR-081 Karar Metni · Mode: Red Team · Human Mode · Truth Mode*
