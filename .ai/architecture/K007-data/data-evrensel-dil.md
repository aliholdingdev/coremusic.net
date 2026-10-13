---
node_id: "K007-data/evrensel-dil"
node_type: "domain"
status: "planned"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/data-master"
  - "K007-data/data-kapsam"
evidence:
  - "Bu dosyanın tüm terimleri K007 ağacındaki diğer 11 dosyada geçtiği için yazılmıştır (iç tutarlılık kanıtı)"
  - "Depo adları: shared/src/Database/ · shared/src/Cache/ · shared/src/Repository/ · shared/database/migrations/ — 2026-10-12 glob"
  - "ADR-097 §5.1-5.2 adlandırma kuralları + .ai/architecture/index.md §8 (dosya adlandırma) — 2026-10-12 okundu"
validation: "2026-10-12 — 60+ terim TR↔EN çifti; her terim K007'deki kullanım yeriyle eşleştirildi; yeni/karıştırılabilir terimler (soft delete vs purge, sync vs CDC) ayrıca uyarı ile."
open_questions:
  - "Terim 'baby-plan' (planlı indeks) — kaynak dil ve standart karşılığı bilinmiyor, İngilizce karşılığı yazılmadı (UNKNOWN)"
  - "'Yedekleme sahipliği' terimi K-09 kararı sonrası yeniden adlandırılabilir mi"
group: mimari
---

# K007-data / evrensel-dil — TR ↔ EN Terim Sözlüğü (K007'ye özgü)

> **Kapsam:** Yalnız K007-data ağacında geçen terimler. Proje-geneli sözlük vault'tadır
> (`.ai/glossary.md` — bu dosya onun **yerine** değil, **altında** çalışır: çelişkide
> vault kazanır). Her terimde: karşılık → K007 kullanımı → karıştırma uyarısı (varsa).

---

## 1. Şema & veri modeli

| TR | EN | K007 kullanımı | Uyarı |
|---|---|---|---|
| Veritabanı şeması | Database schema | `schema` çocuğu | "Şema" aynı zamanda SQL metni anlamına da gelir — bağlam |
| Tablo tanımı | Table definition | migration Ö-8 | — |
| Alan / sütun | Column | `SELECT_COLUMNS` (Ö-5) | "Alan" form alanı ile karıştırılmasın |
| Birincil anahtar | Primary key (PK) | `binary(16)` UUID PK | — |
| Yabancı anahtar | Foreign key (FK) | Ö-8 FK, R-02 | — |
| Birincil/yardımcı anahtar | Candidate / super key | BCNF tartışması (W07) | TR'de "aday anahtar" kullanılır |
| Normal form (BCNF) | Boyce–Codd normal form | `.ai/.sql/CLAUDE.md` beyanı | Kısaltma iki dilde aynı |
| İndeks | Index | `indexing` çocuğu | "İndeks" kitap-fihristiyle karıştırılmasın |
| Kısıt / doğrulama kuralı | Constraint / check | ENUM, NOT NULL, UNIQUE | — |
| Soft delete (yumuşak silme) | Soft delete | `is_deleted` + `deleted_at` | **PURGE ile karıştırılmaz:** soft delete = gizleme; purge = fiziksel silme (R-04) |
| Fiziksel silme | Hard delete / purge | KVKK talebi (S-7) | — |
| Sorgu planı | Query plan / EXPLAIN | R-10 | — |

## 2. Erişim & repository

| TR | EN | K007 kullanımı | Uyarı |
|---|---|---|---|
| Depolama/erişim katmanı | Persistence layer | `repositories` çocuğu | — |
| Repository (depo) | Repository | `ApiKeyRepository` | Türkçe metinde "depo" ≠ Git deposu — bağlam |
| Hazırlanmış ifade | Prepared statement | K-02, Ö-3 | "Parameterized query" ile aynı aile |
| Parametreli sorgu | Parameterized query | tüm örnekler | — |
| ORM (nesne-eşleme) | Object-Relational Mapping | K-02'de **yasak** | Yasak terim — anıldığı yerde ret anlamındadır |
| İşlem / muamele (DB) | Transaction | `transactions`, Ö-2 | "Transaction" finans/ödeme ile karıştırılmasın (K016 yüzeyi) |
| Commit / geri alma | Commit / rollback | Ö-2 | — |
| Bağlantı havuzu | Connection pool | `PERSISTENT=true` (R-07) | PDO kalıcı bağlantı ≠ tam havuz |
| Son eklenen kimlik | Last insert ID | `lastInsertId()` | UUID PK'da anlamı sınırlı — dikkat |

## 3. Önbellek

| TR | EN | K007 kullanımı | Uyarı |
|---|---|---|---|
| Önbellek | Cache | `cache` çocuğu | — |
| Önbellek geçersizleştirme | Cache invalidation | K-05 | "TTL" ile karıştırılmasın: TTL süre, invalidation olay tetikli |
| Yaşam süresi | TTL (time to live) | `ttlPage=600` (Ö-4) | — |
| Oku-üret deseni | Read-through | `remember()` (Ö-4) | write-through/write-behind K007'de yok |
| Önek | Prefix | `page_pub_` / `page_auth_` | — |
| Adaptör (sarmalayıcı) | Adapter | Apcu/Memory/PageCache adapter | DI "adapter" karışıklığı olmasın |

## 4. Arama

| TR | EN | K007 kullanımı | Uyarı |
|---|---|---|---|
| Tam metin arama | Full-text search | `search` çocuğu; MySQL FULLTEXT alternatifi | — |
| Dış arama motoru | External search engine | K-06 (UNKNOWN) | Motor ≠ indeks (farklı çocuklar) |
| Dizinleme (motor) | Indexing (engine) | motor tarafı | `indexing` çocuğu SQL indeksidir — **aynı kelime, iki anlam** |
| Sıralama / derecelendirme | Ranking | K020-isme bağlı | K007'de yok |
| Tibbs / tokenizasyon | Tokenization | motor içi | Türkçe karakter duyarlılığı motor seçimine bağlı (`UNKNOWN`) |

## 5. Senkronizasyon & dağıtık veri

| TR | EN | K007 kullanımı | Uyarı |
|---|---|---|---|
| Veri senkronizasyonu | Data synchronization | `synchronization` çocuğu | Replikasyon ≠ senkronizasyon |
| Replikasyon | Replication | K007'de yok (bilgi amaçlı) | SQLite WAL uyarısı (W06) |
| Değişiklik veri akışı | Change data capture (CDC) | K-07 (UNKNOWN) | outbox ile aynı değildir: CDC dinler, outbox yayınlar |
| Outbox deseni | Outbox pattern | ADR-100 başlığı (metin ⚠️) | — |
| Kuyruk | Queue | senkronizasyon akışı | ADR-086 event mimarisi bağlamı |
| Idempotentlik | Idempotency | R-01 azaltması | "Tekrar çalıştırılabilirlik" ile yakın |
| Dağıtık atomiklik | Distributed atomicity | R-01 | SAGA ile çözülebilir (web §8) |

## 6. Yedekleme & kurtarma

| TR | EN | K007 kullanımı | Uyarı |
|---|---|---|---|
| Yedek / yedekleme | Backup | `backup` çocuğu | "Snapshot" ≠ backup — K007'de snapshot yok |
| Kurtarma hedefi | RTO (recovery time objective) | R-08 — **değer yok, UNKNOWN** | — |
| Veri kaybı toleransı | RPO (recovery point objective) | R-08 — **değer yok, UNKNOWN** | — |
| 3-2-1 kuralı | 3-2-1 rule | web §9 | Kaynaklar satış blogu (W41/W46) — bağımsız değil |
| Geri yükleme | Restore / recovery | Ö-9 `rollback` tarifi | "Rollback" (transaction) ile **karıştırılmaz** — iki anlam |
| Çıkış (dump) | Dump (mysqldump) | Ö-9 | — |
| Yazma-değiştirilemez kopya | WORM | 3-2-1'in 3. kopya seçeneği | K007'de uygulanmadı |

## 7. Migration & değişim yönetimi

| TR | EN | K007 kullanımı | Uyarı |
|---|---|---|---|
| Göç / şema taşıma | Migration | `migrations` çocuğu | "Veri taşıma" (data migration) ile aynı kelime, farklı iş |
| Sürüm sırası | Versioned migration | Ö-8 deseni | — |
| Yıkıcı değişiklik | Destructive change | Ö-9 (`destructive: true`) | — |
| Öntest | Precheck | `$precheck` (Ö-9) | İngilizce "pre-check" |
| Onay kapısı | Approval gate | `approval: REQUIRED` | — |
| Geri alma planı | Rollback plan | `rollback` anahtarı | transaction rollback ile aynı kelime — bağlam ayırır |
| Şema farkı | Schema diff | Alembic autogenerate (web §4) | K007'de araç yok |

## 8. Veri kalitesi & uyum

| TR | EN | K007 kullanımı | Uyarı |
|---|---|---|---|
| Veri kalitesi | Data quality | `data-quality` çocuğu | — |
| Beklenti / kural | Expectation | Great Expectations (web §5) | Araç adı, genel terim değil |
| Nitelik kontrolü | Quality check / assertion | P-05, T-10 | — |
| Kişisel veri | Personal data | R-04 | KVKK/GDPR bağlamı |
| Unutulma hakkı | Right to erasure (GDPR art.17) | S-7 | KVKK'da karşılığı farklı md.'dir (W44) |
| Pseudonimleştirme | Pseudonymization | R-04 azaltma seçeneği | Anonimleştirme (anonymization) ile **farklı** — ikisi eşdeğer değil |
| Denetim izi | Audit trail | log/olay kayıtları | "Log" ile örtüşür |

## 9. Ağaç & süreç adları (dosya adları değişmez)

| Dosya adı (TR) | İçerik rolü | EN karşılığı (iş için) |
|---|---|---|
| `index.md` | Çocuk listesi + özet | index |
| `data-master.md` | Tam kapsam rehberi (ad değişmez, K-SCOPE-2) | master guide |
| `*-kapsam.md` | Kapsam dışı dahil kapsam tanımı | scope |
| `*-bagimliliklar.md` | Bağımlılıklar (komşu alanlar) | dependencies |
| `*-dogrulama.md` | İddia → kanıt tablosu | verification / claims ledger |
| `*-web-arastirma.md` | Kaynak taraması | web research |
| `*-ornekler.md` | Kod parçaları | code examples |
| `*-bilesenler.md` | Bileşen envanteri | components |
| `*-kararlar.md` | Karar defteri | decisions |
| `*-riskler.md` | Risk defteri | risks |
| `*-test-stratejisi.md` | Test planı | test strategy |
| `*-evrensel-dil.md` | Bu sözlük | glossary |

---

## 10. Karıştırılma riski yüksek 5 çift

| # | Çift | Ayırıcı cümle |
|---|---|---|
| 1 | Önbellek **invalidasyon** vs **TTL** | Invalidasyon olay tetikler; TTL zaman tetikler. |
| 2 | **Soft delete** vs **purge** | Soft delete kaydı saklar; purge siler (KVKK md.17 purge ister — R-04). |
| 3 | **Rollback** (transaction) vs **rollback** (migration) | Biri aynı-DB geri alma, diğeri şema geri alma planı. |
| 4 | **Index** (SQL indeks) vs **indexing** (arama motoru) | `indexing/` ≠ `search/` — ayrı çocuklar. |
| 5 | **CDC** vs **outbox** | CDC kaynağı dinler; outbox uygulama yazar, kuyruk okur (web §8). |

---

**Kullanım kuralı:** Bu dosyaya **yeni terim ancak** başka bir K007 dosyasında
kullanıldığında eklenir; sözlük için sözlük yazmak (kullanılmayan terim) yasaktır.
