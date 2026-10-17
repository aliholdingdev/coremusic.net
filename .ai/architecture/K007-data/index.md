---
node_id: K007-data
node_type: domain
status: draft
parent_id: null
children:
  - schema
  - migrations
  - repositories
  - transactions
  - indexing
  - cache
  - search
  - synchronization
  - backup
  - data-quality
depends_on:
  - K008-domain
  - K009-security
evidence:
  - "shared/src/Database/DatabaseManager.php:14-27 — PDO MySQL DSN + 4 attribute (2026-10-13 okundu)"
  - ".ai/.sql/mysql/ — 18 BCNF coremusic_* veritabanı / 20 SQL dump (2026-10-07)"
validation:
  - "2026-10-13 — children listesi Get-ChildItem ile diskten enumerate edildi: 10/10"
open_questions:
  - "search (K007) ile K020-discovery/search/ sahipliği sınırı → VERIFICATION REQUIRED"
group: mimari
## §BAĞLANTILAR

> 2026-10-17 · orkestratör · kaynak: K000–K013 arasi gercek dosya taramasi (ad gecen K eslesmesi) + v2 S4.2 depends_on · iliskiler dogrulanmadir, kesin sozlesme degildir.

| Hedef K | Wiki-link | Göreli dosya yolu | İlişki |
|---|---|---|---|
| K000-isletim-sistemi | [[K000-isletim-sistemi]] | ../K000-isletim-sistemi/index.md | dosya sistemi ve bellek |
| K002-electroncis | [[K002-electroncis]] | ../K002-electroncis/index.md | depolama donanimi (SSD) |
| K003-audio | [[K003-audio]] | ../K003-audio/index.md | ses dosya formatlari |
| K006-yapay-zeka | [[K006-yapay-zeka]] | ../K006-yapay-zeka/index.md | ML veri hatti |
| K008-domain | [[K008-domain]] | ../K008-domain/index.md | domain entity persistence |
| K009-security | [[K009-security]] | ../K009-security/index.md | seifreleme ve erisim kontrolu |
| K010-network | [[K010-network]] | ../K010-network/index.md | replication ve ag aktarimi |
| K011-application | [[K011-application]] | ../K011-application/index.md | repository katmani |
| K012-services | [[K012-services]] | ../K012-services/index.md | servis veri erisimi |
| K013-integraion | [[K013-integraion]] | ../K013-integraion/index.md | sync ve entegrasyon verisi |

---

# K007-data — Domain Künyesi

> **ELI15:** Veritabanı — şarkılar, kullanıcılar, listeler, yedekler.

Bu oda CoreMusic'in **uzun süreli hafızasıdır**: yazılan her şey (şarkı künyesi, kullanıcı
kaydı, playlist, cihaz eşlemesi) burada şekillenir, indekslenir, önbelleklenir, aranır,
yedeklenir ve kalite denetiminden geçirilir. Kod içermez — bu vault **plan, sınır ve kayıt**
tutar; gerçek kod `shared/` ve `.ai/.sql/` altındadır.

## §1 Alt-Konu Haritası (kademe-2 → kademe-3)

| # | Alt konu | Rol | Odak | Dosya |
|---|----------|-----|------|-------|
| 1 | [schema](schema/index.md) | Şema tasarımı | ER/ilişkisel, BCNF, PK/FK/UNIQUE | 6 MD |
| 2 | [migrations](migrations/index.md) | Şema sürümleme | İleri-tek-geri-yok (ADR-014), online DDL | 6 MD |
| 3 | [repositories](repositories/index.md) | Erişim katmanı | PDO/prepared, repository sözleşmesi | 6 MD |
| 4 | [transactions](transactions/index.md) | İşlem bütünlüğü | ACID, MVCC, izolasyon, deadlock | 6 MD |
| 5 | [indexing](indexing/index.md) | İndeks stratejisi | B-Tree, prefix, composite | 6 MD |
| 6 | [cache](cache/index.md) | Önbellek | APCu, page cache, invalidation | 6 MD |
| 7 | [search](search/index.md) | Arama | Full-text, inverted index, novasearch | 6 MD |
| 8 | [synchronization](synchronization/index.md) | Eşitleme | Çift kayıt sınırı (K013 overlap) | 6 MD |
| 9 | [backup](backup/index.md) | Yedekleme | Tam/artık (binlog), PITR, 3-2-1 | 6 MD |
| 10 | [data-quality](data-quality/index.md) | Veri kalitesi | Bütünlük/geçerlilik/tekrar, K-05 | 6 MD |

**Doğrulama (2026-10-15):** 10 çocuk × 6 MD = **60 dosya** disk enumerate edildi
(her çocuk: index + kapsam + bilesenler/veya anayasa + dogrulama/surucu + ornekler +
web-arastirma). ADR-097 §5.2 rol sözlüğü uygulandı.

## §2 Sınır (kapsam dışı komşular)

| Komşu | İlişki | Not |
|-------|--------|-----|
| K008-domain | Veri modelinin sahibi | Entity/ValueObject/domain event → K008 |
| K009-security | Şifreleme/anahtar | key_hash, cipher sınırı |
| K013-integraion | synchronization çift kayıt | Sınır → VERIFICATION REQUIRED |
| K020-recomenataon | Arama keşfi | search sahipliği sınırı → VERIFICATION REQUIRED |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
