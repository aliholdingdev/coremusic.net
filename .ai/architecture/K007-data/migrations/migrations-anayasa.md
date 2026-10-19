---
node_id: K007-data/migrations-anayasa
node_type: subdomain
status: draft
parent_id: K007-data/migrations
children: []
depends_on:
  - "K007-data/schema"
  - "K007-data/transactions"
evidence:
  - "shared/database/migrations/oauth_connections_migration.php:11-41 — CREATE TABLE + INSERT"
  - "shared/database/migrations/oauth_states_migration.php — states migration'ı"
  - "shared/database/migrations/2026-10_07_drop_coremusic_api_api_keys_migration.php — DROP (geri alınamaz)"
validation:
  - "2026-10-13 — 3 migration dosyası sayıldı; 4 URL HTTP 200"
open_questions:
  - "Migration çalıştırma aracı (CLI/komut) repo'da YOK → ⚠️ VERIFICATION REQUIRED"
  - "Down/rollback migration stratejisi → UNKNOWN"
group: mimari
---

# migrations-anayasa — Şema Taşıma Anayasası (K007)

> **Rol:** Şema değişikliklerinin (DDL) nasıl sürümlenip uygulanacağının
> **bağlayıcı kuralları**. Sürüş akışı `migrations-surucu.md`'de, gerçek
> dosya örnekleri `migrations-ornekler.md`'dedir.

## §1 Amaç ve Kapsam

| Alan | Değer |
|---|---|
| node_id | K007-data/migrations-anayasa |
| node_type | subdomain |
| parent_id | K007-data/migrations |
| Grup | mimari |
| Kapsam | sürümleme · geri alma · DDL/transaction sınırı · ortam tutarlılığı |
| Kapsam dışı | Tablo tasarımı (schema) · veri kalitesi (data-quality) |

## §2 Terimler (Türkçe / English)

| Türkçe | English | Tanım |
|---|---|---|
| Göç / taşıma | Migration | Şema değişikliğinin sürümlü uygulaması |
| Yükseltme | Upgrade / up | İleriye doğru değişiklik |
| Geri alma | Down / rollback | Tersine değişiklik |
| Şema | Schema | Tablo/indeks/constraint kümesi |
| DDL | Data Definition Language | CREATE/ALTER/DROP |
| DML | Data Modification Language | INSERT/UPDATE/DELETE |
| Sürümlendirme | Versioning | Değişikliklerin sıralanması |
| İmza | Checksum | Dosyanın değişmez özeti |

## §3 Temel Kurallar

| Madde | Kural | Yaptırım |
|---|---|---|
| M-01 | Migration dosyası adı **sürümlü** olur (tarih/sıra) | Adlandırma RED |
| M-02 | Bir migration = tek mantıksal değişiklik | İncelemede böl |
| M-03 | Geri alma (down) ya tanımlanır ya da "geri alınamaz" olarak **açıkça** işaretlenir | Bilinmeyen = RED |
| M-04 | DROP/DELETE migration'ları geri alınamaz sınıf → yedek ön koşuldur | Yedek yoksa BLOCK |
| M-05 | Migration içinde iş verisi **yorumlanmaz**; yalnız şema/sınırlı dönüşüm | RED |
| M-06 | Ortamlar (dev/test/prod) aynı dosya sırasını uygular | Tutarlılık |
| M-07 | Uygulanmış migration tekrar uygulanmaz (idempotent / kayıtlı) | Çift uygulama RED |
| M-08 | Şablon zorunlu: `migration-template.md`'den türetilir | `shared/AGENTS.md §4.4` |

## §4 DDL × Transaction Sınırı

| Konu | Kural | Kanıt |
|---|---|---|
| DDL implicit commit riski | Bazı DDL komutları transaction öncesi durumu commit'ler | `transactions-anayasa.md` Y-09 |
| Tek migration zarfı | Migration'ı transaction sarmalayıcısına güvenme | ⚠️ VERIFICATION REQUIRED (motor davranışı) |
| İndeks oluşturma | Uzun kilit riski → bakım penceresi | K007-data/indexing |
| Tablo yeniden adlandırma | DROP+CREATE değil, taşıma adımı | `postgresql.org ddl.html` |

| İddia | Durum |
|---|---|
| MySQL'de DDL implicit commit | Motor geneli bilgi → aktarımda doğrula ⚠️ |
| Repo'da migration transaction içinde | Kanıt **yok** → UNKNOWN |

## §5 Sürümleme Kuralları

| Kural | Değer |
|---|---|
| Dosya adı | Tarih-önekli (kanıt: `2026-10_07_drop_...php`) |
| Sıra | Alfabetik/tarihsel sıra = uygulama sırası |
| İmza (checksum) | Repo'da uygulanmış mı → **UNKNOWN** |
| Kayıt tablosu | Repo'da `schema_migrations` benzeri tablo → taramada **görülmedi** → ⚠️ |
| Çakışma | İki migration aynı tabloyu değiştirirse → çakışan RED |

## §6 Geri Alma (Rollback) Kuralları

| # | Kural | Not |
|---|---|---|
| R-01 | Down adımı up'un tersidir | ADN/YAÑNAZ: tek yön |
| R-02 | Geri alınamaz sınıf: DROP tablo, veri silme | `2026-10_07_drop_...` dosyası |
| R-03 | Geri alınamazsa alternatif: yedek + geri yükleme | `K007-data/backup` |
| R-04 | Down testi zorunlu mu → **UNKNOWN** | qa kapsamı |
| R-05 | Prod'da down yalnız onayla | Operasyon kuralı |

## §7 Ortam Kuralları (Environment)

| Ortam | Kural |
|---|---|
| Geliştirme | Migration dosyası ilk burada denenir |
| Test | Aynı sıralama, izole DB |
| Production | Onay + yedek ön koşul (M-04) |
| CI | `composer lint` kapısı — migration testi kanıtı **UNKNOWN** |

## §8 Yasaklar (Forbidden)

| # | Yasak | Sınıf |
|---|---|---|
| Y-01 | Tek migration'da birden fazla mantıksal değişiklik | netlik |
| Y-02 | Dosya adında sıra/veri yoksa sırasız uygulama | determinizm |
| Y-03 | Migration içinde production secret'ı/parola | güvenlik |
| Y-04 | DROP öncesi yedek almadan çalıştırma | veri kaybı |
| Y-05 | Uygulanmış migration'ı elle değiştirme | tarih bozulması |
| Y-06 | Migration'ı transaction sarmalayıcısına koyma varsayımı | Y-09 DDL |
| Y-07 | Migration dosyasında iş mantığı kodu | katman |
| Y-08 | Elle (kod dışı) şema değişikliği — dosyasız ALTER | tekrarlanabilirlik |
| Y-09 | Test şeması ile prod şemasını elle hizalama | drift |
| Y-10 | Şablon dışı migration üretimi | `shared/AGENTS.md §4.4` |

## §9 İlişkisel Bütünlük Kuralları

| # | Kural | Not |
|---|---|---|
| F-01 | Tablo ekleme → FK hedefi önce var olmalı | Sıra |
| F-02 | FK düşürme → önce bağlı kayıt yok ya da ON-delete politikası | `ON DELETE CASCADE` kanıtı: `coremusic_musics.sql` (`artist_credit_names`) |
| F-03 | UNIQUE constraint ekleme → mevcut veri ihlal ediyor mu? | Veri kontrolü |
| F-04 | Sütun tipi daraltma → kayıp riski | Yedek |
| F-05 | NOT NULL ekleme → boş değer var mı? | Veri kontrolü |

## §10 Uyumluluk Kuralları (Compatibility)

| Durum | Kural |
|---|---|
| Eski sürüm kod + yeni şema | Geçici uyumluluk penceresi |
| Yeni kod + eski şema | Riskli — migration önce |
| İki servis aynı DB | Orkestrasyon → `K007-data/migrations` kuralı: tek sıra |
| Deprecation | Önce eskiyi kullanımdan kaldır, sonra sil (2 faz) |

## §11 Performans Kuralları

| # | Kural | Neden |
|---|---|---|
| P-01 | Büyük tabloda indeks oluşturma bakım penceresine | Uzun lock |
| P-02 | Sütun ekleme (nullable/default) hafif | Anlık |
| P-03 | Sütun silme/Tip değiştirme ağır | Kopya/lock |
| P-04 | Tablo taşıma (rename stratejisi) | İndeks/FK dikkat |
| P-05 | Veri dönüşümü ayrı faz | M-05 |

> Büyük tablo limitleri repo'da tanımlı değil → **UNKNOWN**.

## §12 Gözlem (Observability)

| Sinyal | Durum |
|---|---|
| Migration uygulama log'u | Repo'da kanıt **yok** → ⚠️ VERIFICATION REQUIRED |
| Kayıt tablosu | Görülmedi → UNKNOWN |
| Süre ölçümü | Yok → UNKNOWN |
| Drift tespiti (beklenen vs gerçek şema) | Yok → UNKNOWN |

## §13 Doğrulama Listesi (Checklist)

| # | Kontrol | Kriter |
|---|---|---|
| D-1 | Dosya adında sıra var mı? | M-01 |
| D-2 | Tek mantıksal değişiklik mi? | M-02 |
| D-3 | Down açık mı yoksa "geri alınamaz" mı? | M-03 |
| D-4 | DROP için yedek ön koşulu? | M-04 |
| D-5 | Şablondan türedi mi? | M-08 |
| D-6 | FK sırası doğru mu? | §9 |
| D-7 | Uygulama sırası deterministik mi? | §5 |
| D-8 | Transaction varsayımı yok mu? | Y-06 |

## §14 Kanıt Envanteri

| # | Dosya : Kanıt | Ne gösteriyor |
|---|---|---|
| 1 | `shared/database/migrations/oauth_connections_migration.php:11-41` | CREATE TABLE + INSERT |
| 2 | `shared/database/migrations/oauth_states_migration.php` | states migration'ı |
| 3 | `shared/database/migrations/2026-10_07_drop_coremusic_api_api_keys_migration.php` | DROP (geri alınamaz sınıf) |
| 4 | `shared/AGENTS.md §4.4` | Şablon zorunluluğu |
| 5 | `coremusic_musics.sql` | `ON DELETE CASCADE` FK örneği |
| 6 | `coremusic_ai.sql:75-76` | CHECK constraint örnekleri |

## §15 Web Kaynakları (Doğrulanmış — HTTP 200 / 2026-10-13)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| DDL genel kuralı | https://www.postgresql.org/docs/current/ddl.html | 2026-10-13 | 200 OK |
| Constraint kuralları | https://www.postgresql.org/docs/current/ddl-constraints.html | 2026-10-13 | 200 OK |
| CREATE TABLE referansı | https://www.postgresql.org/docs/current/sql-createtable.html | 2026-10-13 | 200 OK |
| Oracle CREATE-TABLE referansı | https://docs.oracle.com/en/database/oracle/oracle-database/23/sqlrf/CREATE-TABLE.html | 2026-10-13 | 200 OK |

> PostgreSQL/Oracle kaynakları referanstır; MySQL InnoDB aktarımı ayrıca doğrulanır (`dev.mysql.com` 403 → kullanılmayacak).

## §16 Bilinmeyenler Defteri

| # | Soru | Etiket |
|---|---|---|
| U-1 | Migration'ı çalıştıran araç/CLI nedir? | ⚠️ VERIFICATION REQUIRED |
| U-2 | Uygulama kaydı tutuluyor mu (schema_migrations)? | UNKNOWN |
| U-3 | Checksum doğrulaması var mı? | UNKNOWN |
| U-4 | Down migration testleri var mı? | UNKNOWN |
| U-5 | Ortamlar arası migration farkı nasıl izleniyor? | UNKNOWN |
| U-6 | Uzun running migration için zaman aşımı politikası | UNKNOWN |
| U-7 | MySQL DDL implicit commit davranışının netleşmesi | ⚠️ VERIFICATION REQUIRED |

## §17 Rolleri (RACI)

| Görev | Sorumlu | Onay |
|---|---|---|
| Migration dosyası yazımı | Data Engineer | Backend Architect |
| FK/şema etkisi | Data Engineer | Architect |
| Yedek ön koşulu (M-04) | Data Engineer + DevOps | — |
| Prod uygulama onayı | DevOps | İnsan |
| Test (down dahil) | QA Engineer | Data Engineer |
| Template uyumu | Data Engineer | Guardrail #16 |

## §18 Anti-Pattern Tablosu

| # | Anti-Pattern | Doğrusu |
|---|---|---|
| AP-1 | Elle prod ALTER | Sürümlü dosya (Y-08) |
| AP-2 | Tek dosyada 10 değişiklik | Böl (M-02) |
| AP-3 | Down "gerekirse eklerim" | M-03: işaretle/yaz |
| AP-4 | DROP + yedeksiz | M-04 |
| AP-5 | Migration'da veri yorumlama | M-05 |
| AP-6 | Ortam başına farklı sıra | M-06 |
| AP-7 | Uygulanmış dosyayı düzenleme | M-07/Y-05 |
| AP-8 | Şablon dışı başlangıç | M-08 |

## §19 Senaryo × Sonuç Matrisi

| # | Senaryo | Sonuç | Kural |
|---|---|---|---|
| S-1 | Yeni tablo | FK hedefi önce ekle | F-01 |
| S-2 | FK ekleme | Veri ihlali kontrolü | F-03 |
| S-3 | Sütun tipi daraltma | Yedek + pencere | P-04 |
| S-4 | DROP tablo | Yedek zorunlu | M-04 |
| S-5 | İndeks oluşturma | Bakım penceresi | P-01 |
| S-6 | İki servis aynı şema | Tek sıra | §10 |
| S-7 | Migration yarım kalır | Bilinmeyen durum → manual | ⚠️ U-6 |
| S-8 | Aynı migration 2 kez | İkinci RED | M-07 |
| S-9 | Dosya adı sırasız | RED | M-01 |
| S-10 | Down tanımsız | "geri alınamaz" işareti | M-03 |

## §20 Doğrulama Sorusu Seti

| # | Soru | Evet | Hayır |
|---|---|---|---|
| 1 | Dosya adında sıra/tarih var mı? | Devam | RED |
| 2 | Tek mantıksal değişiklik mi? | Devam | Böl |
| 3 | Down tanımlı ya da işaretli mi? | Devam | RED |
| 4 | DROP mu? | Yedek ön koşulu | Atla |
| 5 | Template'den türedi mi? | Devam | YENİDEN |
| 6 | FK sırası doğru mu? | Devam | Böl |
| 7 | Veri yorumlama var mı? | Ayır (M-05) | Devam |
| 8 | Transaction varsayımı var mı? | Kaldır (Y-06) | Devam |

## §21 Terim Karşılıkları (TR ↔ EN ↔ Kavram)

| Türkçe | English | Kavram |
|---|---|---|
| Göç | Migration | Şema sürüm dosyası |
| Yükseltme | Upgrade | İleri adım |
| Geri alma | Down / rollback | Ters adım |
| Şema kaydı | Migration record | Uygulama defteri |
| İmza | Checksum | Değişmezlik kontrolü |
| Drift | Schema drift | Beklenen ≠ gerçek |
| Bakım penceresi | Maintenance window | Uygun zaman |
| Kademeli dağıtım | Staged rollout | Ortam sırası |
| İdempotent | Idempotent | Tekrar güvenli |
| Geri alınamaz | Irreversible | Yedek şart |

## §22 Bağlantılar (Relations)

| Düğüm | İlişki |
|---|---|
| `K007-data/schema` | Hedef şema |
| `K007-data/transactions` | DDL/transaction sınırı (Y-09) |
| `K007-data/backup` | DROP öncesi yedek (M-04) |
| `K007-data/data-quality` | Constraint kalitesi |
| `K007-data/indexing` | İndeks DDL'si (P-01) |

## §23 Test Gereksinimleri

| # | Test | Kriter |
|---|---|---|
| T-1 | Up uygulama | Şema hedefte |
| T-2 | Down uygulama | Eski şemaya döner |
| T-3 | Tekrar uygulama (2. kez) | Red/idempotent |
| T-4 | Sıra bozuk dosya | Red |
| T-5 | Orta hata | Yarım şema tespiti → kurtarma |
| T-6 | FK sırası | Başarılı uygulama |

## §24 Durum Makinesi

| # | Durum | Tetikleyici | Sonraki |
|---|---|---|---|
| 1 | DOSYA | migration yazıldı | INCELEME |
| 2 | INCELEME | checklist §13 geçti | ORTAM |
| 3 | ORTAM | dev uygulandı | TEST |
| 4 | TEST | up/down geçti | ONAY |
| 5 | ONAY | insan onayı | PROD |
| 6 | PROD | uygulandı | UYGULANDI |
| 7 | HATA | herhangi | GERİ ALMA/YEDek |
| 8 | UYGULANDI | — | arşiv |

## §25 Metrikler

| Metrik | Hedef | Durum |
|---|---|---|
| Uygulanan migration sayısı | izlenir | repo'da kayıt yok → UNKNOWN |
| Ortalama süre | izlenir | UNKNOWN |
| Başarı oranı | %100 hedef | UNKNOWN |
| Drift sayısı | 0 hedef | UNKNOWN |
| Down test kapsamı | hedef tanımsız | UNKNOWN |

## §26 Sınır ve Kapsam Notları

| Sınır | Durum |
|---|---|
| Veri migration'ı (büyük dönüşüm) | Ayrı sınıf → bilinmiyor (UNKNOWN) |
| Multi-DB migration | Repo'da 18 DB → sıra kuralı yok (UNKNOWN) |
| Zero-downtime migration | Politika yok → UNKNOWN |
| Geri dönüşüm şeması | Backup'a bağlı (M-04) |

## §27 Ornek-Kural Uyumu (Önizleme)

| Kural | Repo durumu | Not |
|---|---|---|
| M-01 sürüm adı | `2026-10_07_drop_...php` ✅ | Tarih önekli |
| M-03 geri alma işareti | (dosya içeriği okunmadı) | ⚠️ inceleme |
| M-04 yedek ön koşulu | `K007-data/backup` var | Kural |
| M-08 şablon | `shared/AGENTS.md §4.4` ✅ | Şablon zorunlu |
| Y-08 dosyasız ALTER | (değişiklik geçmişi okunmadı) | UNKNOWN |

## §28 SSS (FAQ)

| # | Soru | Cevap |
|---|---|---|
| 1 | Migration transaction içinde olur mu? | Y-06: varsayma (DDL/implicit commit) |
| 2 | DROP ne zaman güvenli? | Yedek + onay (M-04) |
| 3 | Kim yazar? | Data Engineer (§17) |
| 4 | Şablon nerede? | `.ai/.templates/infrastructure/migration-template.md` |
| 5 | Orta hata ne olur? | §24 durum 7 → kurtarma |

## §29 İlgili Kararlar (ADR)

| Karar | İlişki | Not |
|---|---|---|
| ADR-002 (PDO, ORM yok) | Migration PDO ile | `shared/AGENTS.md §4.1` |
| ADR-040 (BCNF) | Şema normalizasyonu | Data Engineer |
| ADR-097 K4 | Kademe-3 yapı kilitlenmesi | `migrations/index.md` |
| Guardrail #16 | Şablon zorunluluğu | `shared/AGENTS.md §4.4` |

## §30 Dosya Sınıflandırması

| Sınıf | Örnek | Risk |
|---|---|---|
| Ekleme (ADD/CREATE) | `oauth_connections_migration.php` | Düşük |
| Silme (DROP) | `2026-10_07_drop_coremusic_api_api_keys_migration.php` | **Yüksek** |
| Dönüşüm (ALTER) | (repo'da örneği görünmedi) | Orta/Yüksek |
| Veri taşıma | (görülmedi) | Yüksek |

## §31 Komşu Konulara Ayırma

| Konu | Ayrıştırma |
|---|---|
| Tablo tasarımı | `schema/` |
| İndeks tasarımı | `indexing/` |
| Yedekleme | `backup/` |
| Veri doğruluğu | `data-quality/` |
| Transaction atomikliği | `transactions/` |
| Uygulama senkronu | `synchronization/` |

## §32 Kalite Kapısı

| # | Kontrol | Sonuç |
|---|---|---|
| Q-1 | FM 10 alan | ✅ |
| Q-2 | `parent:` yok | ✅ |
| Q-3 | `group: mimari` tırnaksız | ✅ |
| Q-4 | Mojibake yok | ✅ |
| Q-5 | Her iddia kanıtlı/etiketli | ✅ |
| Q-6 | Son satır Authority birebir | ✅ |

## §33 Ek Kayıtlar

| # | Kayıt | Durum |
|---|---|---|
| E-1 | Migration aracı taraması | 0 sonuç → ⚠️ VERIFICATION REQUIRED |
| E-2 | Kayıt tablosu taraması | 0 sonuç → UNKNOWN |
| E-3 | Checksum kodu | 0 sonuç → UNKNOWN |
| E-4 | Down migration testi | 0 sonuç → UNKNOWN |
| E-5 | 3 migration dosyası | ✅ diskte |

## §34 Geri Alma (Down) Derinlemesine

| # | Soru | Yanıt | Etiket |
|---|---|---|---|
| G-1 | Repo'da down implementasyonu var mı? | Dosyalar okunmadı → bilinmiyor | ⚠️ VERIFICATION REQUIRED |
| G-2 | DROP'ın down'ı ne olur? | Tabloyu yeniden yarat + veri yok | M-04 |
| G-3 | ALTER'ın down'ı ters ALTER | Sıra duyarlı | Kural |
| G-4 | Down testi var mı? | 0 kanıt | UNKNOWN |
| G-5 | Down zamanı | Bilinmiyor | UNKNOWN |

| Sınıf | Down Zorunluluğu |
|---|---|
| CREATE TABLE/INDEX | Düşür (DROP) — güvenli |
| ADD COLUMN | Sütun sil — veri kaybı sınıfı |
| DROP TABLE | **Yeniden oluştur — veri kaybı, yedek şart** |
| ALTER TYPE | Ters dönüşüm riskli — ⚠️ doğrula |

## §34.1 İdempotentlik (Tekrar Güvenliği)

| İşlem | İdempotent mi? | Not |
|---|---|---|
| `CREATE TABLE IF NOT EXISTS` | Evet | Tekrar güvenli |
| `CREATE TABLE` (korumasız) | Hayır | 2. uygulamada hata |
| `DROP TABLE IF EXISTS` | Evet | Ama veri siler |
| `ADD COLUMN` | Hayır | 2. sütun hatası |
| `INSERT ... ON DUPLICATE KEY` | Evet | Veri migration'ı |
| `UPDATE` (aynı değer) | Evet | — |

| Kural | Değer |
|---|---|
| Repo'da `IF NOT EXISTS` kullanımı | Kanıt okunmadı → UNKNOWN |
| Çift uygulama savunması | M-07 (kayıt) + idempotent |

## §35 Ortam-Ötesi Tutarlılık

| # | Kontrol | Amaç |
|---|---|---|
| O-1 | Aynı dosya sırası tüm ortamlarda | §5 |
| O-2 | Ortam spesifik migration (ör. test seed) | Ayrı sınıf → UNKNOWN |
| O-3 | 18 DB'nin migration bağımlılığı | Sıra kuralı yok → UNKNOWN |
| O-4 | CI'da migration koşusu | Kanıt yok → UNKNOWN |
| O-5 | Prod onayı | §17 RACI |

## §36 Şablon Uyumu (Template Compliance)

| Şablon | Zorunlu mu? | Kaynak |
|---|---|---|
| `migration-template.md` | Evet | `shared/AGENTS.md §4.4` |
| `php-template.md` | PHP dosyası ise evet | `shared/AGENTS.md §4.5` |
| Guardrail #16 | Yeni dosya üretiminde | `.ai/AGENTS.md` §13.1 #5 |

## §37 Risk Kaydı

| # | Risk | Etki | Savunma |
|---|---|---|---|
| RK-1 | Yedeksiz DROP | Veri kaybı | M-04 |
| RK-2 | Yarım migration | Şema drift | §24 durum 7 |
| RK-3 | Sıra bozukluğu | Yanlış uygulama | M-01 |
| RK-4 | Elle prod ALTER | Tekrarlanamaz | Y-08 |
| RK-5 | Orta hata + kurtarma yok | Bilinmeyen durum | ⚠️ U-7 |

## §38 Revizyon

| Tarih | Değişiklik |
|---|---|
| 2026-10-13 | İlk yazım — 6 kanıt + 4 doğrulanmış URL + 7 UNKNOWN |

## §39.1 Migration Kontrol Listesi (Özet)

| # | Soru | Hayır ise |
|---|---|---|
| 1 | Sıra/tarih adı var mı? | RED (M-01) |
| 2 | Tek mantıksal değişiklik mi? | Böl (M-02) |
| 3 | Down ya da "geri alınamaz" işareti var mı? | RED (M-03) |
| 4 | DROP için yedek var mı? | BLOCK (M-04) |
| 5 | Şablondan türedi mi? | YENİDEN (M-08) |
| 6 | FK sırası sağlandı mı? | Böl (F-01) |
| 7 | Veri yorumlama ayrıldı mı? | Ayır (M-05) |
| 8 | Transaction varsayımı temizlendi mi? | Kaldır (Y-06) |
| 9 | Elle şema değişikliği yapıldı mı? | Geri al (Y-08) |
| 10 | Uygulanmış dosya değiştirildi mi? | RED (Y-05) |

## §40.1 Ek Madde — Yedek Ön Koşulu Detayı

| Adım | Eylem | Kural |
|---|---|---|
| Y-1 | Son yedek zamanını doğrula | M-04 |
| Y-2 | Yedek geri yükleme testi | `K007-data/backup` |
| Y-3 | DROP listesi üret | İlgili tablolar |
| Y-4 | Onay al | §17 RACI |
| Y-5 | Uygula | §24 durum 6 |
| Y-6 | Yedek doğrula (uygulama sonrası) | M-04 |

## §41 Sürüm

| Alan | Değer |
|---|---|
| Versiyon | 1.0.0 |
| Tarih | 2026-10-13 |
| Durum | draft |
| Erişim | `.ai/architecture/K007-data/migrations/migrations-anayasa.md` |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
