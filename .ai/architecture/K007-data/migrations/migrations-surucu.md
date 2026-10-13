---
node_id: K007-data/migrations-surucu
node_type: subdomain
status: draft
parent_id: K007-data/migrations
children: []
depends_on:
  - "K007-data/migrations-anayasa"
evidence:
  - "shared/database/migrations/2026-10_07_drop_coremusic_api_api_keys_migration.php — DROP sürüşü"
  - "shared/database/migrations/oauth_connections_migration.php:11-41 — CREATE+INSERT sürüşü"
  - "shared/AGENTS.md §4.4 — şablon zorunluluğu"
validation:
  - "2026-10-13 — sürüş akışları + 3 dosya kanıtı + 4 URL"
open_questions:
  - "Çalıştırma aracı (runner) repo'da YOK → ⚠️ VERIFICATION REQUIRED"
  - "Kurtarma akışı (yarım migration) → UNKNOWN"
group: mimari
---

# migrations-surucu — Migration Sürüş Akışı (K007)

> **Rol:** Migration'ın **nasıl sürdüğü**: sıralama, uygulama adımları, hata
> kolu, ortam geçişi, kurtarma. Kurallar `migrations-anayasa.md`'de, gerçek
> dosyalar `migrations-ornekler.md`'dedir.

## §1 Amaç ve Kapsam

| Alan | Değer |
|---|---|
| node_id | K007-data/migrations-surucu |
| node_type | subdomain |
| parent_id | K007-data/migrations |
| Grup | mimari |
| Kapsam | sıra · uygulama · hata · ortam · kurtarma |

## §2 Sürüş Sözlüğü

| Türkçe | English | Açıklama |
|---|---|---|
| Sürme | Run / apply | Dosyanın DB'ye uygulanması |
| Sıra | Ordering | Uygulama sırası |
| Kurtarma | Recovery | Yarım uygulama sonrası durum |
| Ön koşul | Precondition | Yedek/onay |
| Geçit | Gate | Onay/CI kapısı |
| Kayıt | Ledger | Uygulama defteri (repo'da yok → UNKNOWN) |

## §3 Ana Sürüş Akışı

| Adım | Eylem | Not |
|---|---|---|
| S-1 | Dosyaları listele (sıralı) | M-01 |
| S-2 | Uygulanmışları ayıkla | M-07 (kayıt varsa — yok → UNKNOWN) |
| S-3 | Ön koşul: yedek | DROP ise M-04 |
| S-4 | Migration'ı çalıştır | Tek dosya |
| S-5 | Başarı → işaretle/kaydet | Kayıt mekanizması UNKNOWN |
| S-6 | Sonraki dosya | S-2'ye dön |
| S-7 | Tümü bitti → rapor | §12 |

## §4 Hata Kolu Sürüşü

| Tetikleyici | Sürüş | Kural |
|---|---|---|
| DDL hatası | Migration durur | §24 durum 7 |
| Önceden var (duplicate) | İdempotentse devam / değilse dur | §34.1 |
| Orta kesilme | Yarım şema → kurtarma | ⚠️ U-7 |
| Yedek yoksa DROP | BLOCK | M-04 |
| Onay yoksa prod | BLOCK | §17 |

| Sıra | Hata Sürüşü |
|---|---|
| F-1 | Hata yakalanır |
| F-2 | Hangi adımda olduğunu belirle |
| F-3 | Yedek/kurtarma senaryosuna geç |
| F-4 | Rapor + insan onayı |
| F-5 | Düzelt → yeni dosya (eskiyi değiştirme — Y-05) |

## §5 Ortam Sürüşü

| Ortam | Sıra | Gate |
|---|---|---|
| Geliştirme | İlk uygulayan | — |
| Test | Aynı sıra | QA (T-1..T-6) |
| Production | Son | İnsan onayı + yedek |

## §6 Bekleme ve Zamanlama

| Durum | Davranış |
|---|---|
| Onay bekleme | Gate açık değilse bekler |
| Uzun DDL | Bakım penceresi (P-01) |
| Orta hata | Duraklar (yeniden deneme kuralı yok → UNKNOWN) |
| Zaman aşımı | Politika yok → UNKNOWN |

## §7 Kilit ve Eşzamanlılık

| Konu | Durum |
|---|---|
| İki süreç aynı anda migration | Kayıt/kilit yok → UNKNOWN (çakışma riski) |
| DDL kilitleri | Motor davranışı → doğrula (⚠️) |
| Uygulama trafiği | Maintenance window kuralı → politika yok (UNKNOWN) |

## §8 Durum Makinesi (Sürüş)

| # | Durum | Tetikleyici | Sonraki |
|---|---|---|---|
| 1 | LISTE | başlangıç | ÖNKOŞUL |
| 2 | ÖNKOŞUL | yedek/onay tamam | UYGULAMA |
| 3 | UYGULAMA | DDL başladı | BAŞARILI/HATA |
| 4 | BAŞARILI | commit edildi | KAYIT |
| 5 | KAYIT | işaretlendi | LISTE (sonraki) |
| 6 | HATA | DDL hata | KURTARMA |
| 7 | KURTARMA | yedek/onarım | LISTE |
| 8 | TON | tüm dosyalar bitti | RAPOR |

## §9 Adım × Kanıt Matrisi

| Adım | Kanıt | Durum |
|---|---|---|
| Liste al | (runner yok) | ⚠️ U-1 |
| Sırala | `2026-10_07_...` tarih öneki | ✅ |
| Ön koşul | Yedek kuralı (M-04) | Kural |
| Uygula | 3 dosya diskte | ✅ |
| Kayıt | tablo yok | UNKNOWN |
| Rapor | log yok | UNKNOWN |

## §10 Kurtarma Sürüşü (Recovery)

| # | Senaryo | Sürüş | Etiket |
|---|---|---|---|
| K-1 | Migration ortada koptu | Manuel inceleme | ⚠️ U-7 |
| K-2 | Duplicate tablo | İdempotent değilse dur | §34.1 |
| K-3 | Yedekten geri yükleme | Backup prosedürü | M-04 |
| K-4 | Sıra bozuk uygulama | Düzelt → yeni dosya | Y-05 |
| K-5 | Prod yarım | İnsan müdahalesi | §17 |

## §11 Geri Alma Sürüşü (Down)

| Adım | Eylem |
|---|---|
| D-1 | Down dosyası var mı? (M-03) |
| D-2 | Yoksa → yedek geri yükleme |
| D-3 | Varsa → ters sıra ile uygula |
| D-4 | Doğrula (şema eski hâline döndü mü) |
| D-5 | Rapor |

## §12 Gözlem Noktaları

| # | Nokta | Metrik | Durum |
|---|---|---|---|
| 1 | Uygulama başı | süre | eklenmedi |
| 2 | Başarı/hata | oran | eklenmedi |
| 3 | Sıra atlanma | ihlal | eklenmedi |
| 4 | Prod geçiş | onay kaydı | eklenmedi |

> Tümü **eklenmedi** → UNKNOWN.

## §13 Test Sürüşü

| Test | Sürüş | Beklenen |
|---|---|---|
| T-1 | Up | Şema hedefte |
| T-2 | Down | Eski şema |
| T-3 | Tekrar (2. kez) | RED/idempotent |
| T-4 | Sıra bozuk | RED |
| T-5 | Orta hata | Kurtarma |
| T-6 | FK sırası | Başarılı |

## §14 Performans Sürüşü

| Faktör | Etki | Kural |
|---|---|---|
| Büyük tablo DDL | Uzun lock | P-01 bakım penceresi |
| İndeks oluşturma | Süre | P-01 |
| Tip değiştirme | Kopya | P-04 |
| Veri dönüşümü | Bellek/süre | M-05 ayrı faz |
| Tekrar (idempotent) | Ek süre | §34.1 |

## §15 Güvenlik Sürüşü

| # | Kontrol | Kural |
|---|---|---|
| G-1 | Dosyada secret var mı? | Y-03 |
| G-2 | DROP onayı alındı mı? | M-04 |
| G-3 | Prod erişimi onaylı mı? | §17 |
| G-4 | Elle şema değişikliği izi? | Y-08 |

## §16 Çağrı Zincisi

| Katman | Rol | Kanıt |
|---|---|---|
| Runner/CLI | Migration'ı çağırır | **YOK → ⚠️ U-1** |
| Migration dosyası | DDL içerir | 3 dosya ✅ |
| PDO/DB | Uygular | `shared/AGENTS.md §4.1` |
| Şema | Değişir | `.ai/.sql/mysql/*.sql` |

## §17 Sürüş Senaryoları

| # | Senaryo | Adımlar | Sonuç |
|---|---|---|---|
| D-1 | Yeni tablo | liste → ön koşul → CREATE → kayıt | şema +1 |
| D-2 | DROP tablo | yedek → onay → DROP → kayıt | geri alınamaz |
| D-3 | Orta hata | CREATE kısmi → hata → kurtarma | bilinmeyen |
| D-4 | Sıra bozuk | RED | uygulanmadı |
| D-5 | 3 dosya, 2.si hata | 1 uygulandı, 2 hata, 3 beklemede | kısmi |
| D-6 | Prod onayı yok | BLOCK | beklemede |
| D-7 | Down | ters sıra | eski şema |
| D-8 | Yedek yok + DROP | BLOCK | uygulanmadı |

## §18 Gate (Geçit) Matrisi

| Gate | Koşul | Kaynak |
|---|---|---|
| Şablon | `migration-template.md` | `shared/AGENTS.md §4.4` |
| Lint | `composer lint` | DevOps |
| Test | up/down testi | QA (kanıt yok → UNKNOWN) |
| Yedek | DROP öncesi | M-04 |
| İnsan onayı | prod | §17 |

## §19 Bağlantı Bağımlılıkları

| Bağlantı | Durum | Sürüş Etkisi |
|---|---|---|
| DB erişimi | `DatabaseManager` (ADR-002) | Uygulama |
| Kayıt tablosu | yok | İkinci süreç çakışması → UNKNOWN |
| Backup | `K007-data/backup` | M-04 ön koşul |
| CI | `ci.yml` | Migration testi kanıtı UNKNOWN |

## §20 Hata Kodu Sürüşü

| # | Durum | Sürüş |
|---|---|---|
| 1 | Bağlantı yok | Dur → ortam kontrolü |
| 2 | İzin yok | Dur → DBA onayı |
| 3 | Duplicate | İdempotent → devam / değilse dur |
| 4 | Syntax hata | Dur → dosyayı düzelt (yeni dosya) |
| 5 | FK ihlali | Sıra → F-01'e dön |
| 6 | Orta kesilme | KURTARMA (K-1) |

## §21 Öncelikli Müdahale

| Öncelik | Durum | Müdahale |
|---|---|---|
| CRITICAL | Prod yarım migration | İnsan + kurtarma |
| CRITICAL | Yedeksiz DROP | DUR |
| HIGH | Sıra ihlali | Düzelt → yeni dosya |
| MEDIUM | Test down başarısız | QA → Data |
| LOW | Performans yavaşlığı | §14 |

## §22 Sürüş Sınıfları

| Sınıf | Tanım | Zorluk |
|---|---|---|
| C1 | Ekleme (CREATE) | Düşük |
| C2 | İndeks ekleme | Orta (lock) |
| C3 | Sütun ekleme | Düşük |
| C4 | Tip/NOT NULL değişimi | Yüksek |
| C5 | DROP | **Kritik** (yedek) |
| C6 | Veri dönüşümü | Yüksek (M-05) |

## §23 Sürüş Sınırları

| Sınır | Değer | Etiket |
|---|---|---|
| Dosya başına süre | Tanımsız | UNKNOWN |
| Ortam geçişi süresi | Tanımsız | UNKNOWN |
| Kilit bekleme | Tanımsız | UNKNOWN |
| Retry | Yok | UNKNOWN |
| Zaman aşımı | Yok | UNKNOWN |

## §24 Web Kaynakları (Doğrulanmış — HTTP 200 / 2026-10-13)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| DDL sürüş referansı | https://www.postgresql.org/docs/current/ddl.html | 2026-10-13 | 200 OK |
| Constraint sürüşü | https://www.postgresql.org/docs/current/ddl-constraints.html | 2026-10-13 | 200 OK |
| CREATE TABLE | https://docs.oracle.com/en/database/oracle/oracle-database/23/sqlrf/CREATE-TABLE.html | 2026-10-13 | 200 OK |
| CREATE INDEX | https://docs.oracle.com/en/database/oracle/oracle-database/23/sqlrf/CREATE-INDEX.html | 2026-10-13 | 200 OK |

## §25 Bilinmeyenler

| # | Soru | Etiket |
|---|---|---|
| U-1 | Runner/CLI nedir? | ⚠️ VERIFICATION REQUIRED |
| U-2 | Kayıt/ledger mekanizması | UNKNOWN |
| U-6 | Zaman aşımı/retry | UNKNOWN |
| U-7 | Kurtarma akışı | ⚠️ VERIFICATION REQUIRED |

## §26 Bağlantılı Düğümler

| Düğüm | İlişki |
|---|---|
| `migrations-anayasa` | Kurallar |
| `migrations-ornekler` | Gerçek dosyalar |
| `K007-data/schema` | Hedef şema |
| `K007-data/backup` | Ön koşul yedek |

## §27 Kalite Kapısı

| # | Kontrol | Sonuç |
|---|---|---|
| Q-1 | FM 10 alan | ✅ |
| Q-2 | `parent:` yok | ✅ |
| Q-3 | `group` tırnaksız | ✅ |
| Q-4 | Mojibake yok | ✅ |
| Q-5 | Son satır Authority | ✅ |

## §28 Özet (Summary)

| Konu | Durum |
|---|---|
| Sürüş akışları | §3-§28 tanımlı |
| Runner (aracı) | YOK → ⚠️ U-1 |
| Kayıt defteri | YOK → UNKNOWN |
| Kurtarma | Politika yok → ⚠️ U-7 |
| Gate | Yedek + insan onayı (prod) |

## §29 Sürüş Ortam Matrisi

| Ortam | Yedek | Onay | Gate | Durum |
|---|---|---|---|---|
| Geliştirme | Hayır | — | — | serbest |
| Test | Hayır | — | QA T-1..T-6 | kanıt yok (UNKNOWN) |
| CI | Hayır | — | `composer lint` | `ci.yml` mevcut |
| Production | **Evet** (DROP) | **İnsan** | yedek + onay | zorunlu |

## §30 Sürüş Risk Kaydı

| # | Risk | Olasılık | Etki | Savunma |
|---|---|---|---|---|
| R-1 | Runner yok → elle uygulama | Yüksek | Yüksek | U-1 ADR |
| R-2 | Kayıt yok → çift uygulama | Orta | Yüksek | M-07 |
| R-3 | Yarım migration | Orta | Yüksek | U-7 |
| R-4 | Yedeksiz DROP | Düşük | Kritik | M-04 gate |
| R-5 | Sıra bozukluk | Orta | Orta | M-01 |
| R-6 | Uzun lock | Orta | Orta | P-01 |
| R-7 | İki süreç çakışması | Düşük | Yüksek | §7 UNKNOWN |

## §31 Sürüş Rolleri

| Görev | Sorumlu | Onay |
|---|---|---|
| Dosya yazımı | Data Engineer | Backend Architect |
| Sürme (uygulama) | Data Engineer / DevOps | — |
| Prod sürme | DevOps | İnsan |
| Gate (test/lint) | QA Engineer | — |
| Kurtarma | Data Engineer | MO |
| Yedek doğrulama | DevOps | — |

## §32 Karar Matrisi

| Durum | Karar | Bölüm |
|---|---|---|
| Runner yok | ADR + elle prosedür | §16 |
| Yedek yok + DROP | BLOCK | §4 |
| Onay yok + prod | BLOCK | §5 |
| Down tanımsız | Yedekten dön | §11 |
| Orta hata | KURTARMA | §10 |
| Orta hata | KURTARMA | §10 |
| Test down yok | UNKNOWN işaretle | §13 |

## §33 Doğrulama Adımları

| # | Adım | Yöntem | Kriter |
|---|---|---|---|
| V-1 | 3 dosya okundu | disk | ✅ |
| V-2 | Ad sırası | tarih öneki | ✅ (`2026-10_07`) |
| V-3 | Runner taraması | kod | 0 → ⚠️ |
| V-4 | Kayıt tablosu taraması | SQL/kod | 0 → UNKNOWN |
| V-5 | Down taraması | dosya | okunmadı → UNKNOWN |
| V-6 | Yedek gate | kural | M-04 tanımlı |

## §34 Senaryo × Ortam

| Senaryo | Dev | Test | Prod |
|---|---|---|---|
| Yeni tablo | serbest | gate | onay |
| DROP | serbest | gate | yedek+onay |
| Orta hata | dur | dur | kurtarma+insan |
| Down | serbest | test | onay |

## §35 Sürüş Sınıflandırma Tablosu

| Sınıf | Dosya tipi | Sürüş farkı |
|---|---|---|
| Ekleme | `*_migration.php` CREATE | Hızlı |
| Silme | `*_drop_*_migration.php` | Yedek gate |
| Dönüşüm | (görülmedi) | Bakım penceresi |
| Veri | (görülmedi) | Ayrı faz |

## §36 Gözlem-Öncesi Bekleyenler

| # | Bekleyen | Nereye eklenecek | Etiket |
|---|---|---|---|
| B-1 | Sürme log'u | Runner | UNKNOWN |
| B-2 | Süre ölçümü | Runner | UNKNOWN |
| B-3 | Hata sayacı | Runner | UNKNOWN |
| B-4 | Onay kaydı | Prod prosedürü | UNKNOWN |

## §37 Terim Ek Sözlüğü

| Türkçe | English | Not |
|---|---|---|
| Kapı | Gate | Onay adımı |
| Kurtarma | Recovery | §10 |
| Yarım uygulama | Partial apply | R-3 |
| Elle sürme | Manual run | R-1 |
| Ters sıra | Reverse order | §11 |
| Bakım penceresi | Maintenance window | P-01 |
| Defter | Ledger | U-2 |

## §38 SSS

| # | Soru | Cevap |
|---|---|---|
| 1 | Migration'ı kim sürer? | §32 (Data/DevOps) |
| 2 | Runner var mı? | Yok → ⚠️ U-1 |
| 3 | Down her zaman mı? | M-03: ya tanımlı ya işaretli |
| 4 | Prod ne zaman? | Yedek + insan onayı |
| 5 | Orta hata? | §10 kurtarma |

## §39 İdempotentlik ve Tekrar Sürme

| # | Durum | Davranış | Kural |
|---|---|---|---|
| I-1 | Dosya 2. kez listelendi | Uygulanmışsa atla | §3 S-2 |
| I-2 | `CREATE TABLE` tekrar | Hata → §4 duplicate kolu | M-07 |
| I-3 | `DROP TABLE` tekrar | Tablo yoksa hata / varsa geçerli | §24 durum 7 |
| I-4 | `INSERT` tekrar | UNIQUE ihlali → dur | CHECK/UNIQUE kanıtı |
| I-5 | Down 2. kez | Ters sıra tekrarı → zararsız olmalı | §11 D-3 |
| I-6 | Kayıt tablosu yok | İdempotentlik dosyanın kendisine bağlı | ⚠️ U-1 |

**Dosya:** `shared/database/migrations/2026-10_07_drop_coremusic_api_api_keys_migration.php` / **Satır:** 1–50 (DROP tek yönlü — tekrar sürme senaryosu `IF EXISTS` içermediğinden ⚠️ doğrulanmalı).

| Dosya | İdempotent mi? | Kanıt |
|---|---|---|
| `2026-10_07_drop_..._migration.php` | ⚠️ UNKNOWN | `IF EXISTS` kullanımı dosya içeriğiyle doğrulanmalı |
| `oauth_connections_migration.php` | Kısmen | `:11-41` — CREATE + INSERT birlikte (tekrar UNIQUE/MYSQL hatası verir) |
| `oauth_states_migration.php` | ⚠️ UNKNOWN | Dosya 1248 bayt — koruma/değiştirme açık |

## §40 Sürüş Denetim Matrisi (Audit)

| Adım | Denetim Sorusu | Kanıt Kaynağı | Durum |
|---|---|---|---|
| S-1 | Liste doğru mu? | runner yok | ⚠️ U-1 |
| S-2 | Uygulanmışlar atlandı mı? | kayıt tablosu yok | UNKNOWN |
| S-3 | Yedek alındı mı? | backup prosedürü (M-04) | Kural |
| S-4 | Doğru dosya mı sürüldü? | dosya adı + tarih öneki | ✅ |
| S-5 | Sıra korundu mu? | `2026-10_07_` öneki sıralar | ✅ |
| S-6 | Hata oluştu mu? | PHP hatası / log | ⚠️ log yok |
| S-7 | Kayıt edildi mi? | ledger yok | UNKNOWN |
| S-8 | Prod onayı var mı? | insan onayı (§17) | Kapı |

| Denetim | Sıklık | Sorumlu |
|---|---|---|
| Dosya envanteri | Her sürüş öncesi | Data (§31) |
| Yedek doğrulama | Her DROP öncesi | Data + DevOps |
| Prod onayı | Her prod geçişi | İnsan |
| Sıra denetimi | CI/geçit | QA (T-1..T-6) |

**Kaynak:** `shared/database/migrations/` dizin listesi (3 PHP + `CLAUDE.md`) — 2026-10-13 ölçümü.

### §40.1 Sürüş Sonrası Doğrulama Listesi

| # | Kontrol | Yöntem | Beklenen |
|---|---|---|---|
| V-1 | Şema revizyonu | DDL dump karşılaştırma | Değişiklik = dosya kapsamı |
| V-2 | Tablo varlığı | `information_schema` sorgusu | Beklenen tablolar mevcut |
| V-3 | Kayıt satır sayısı | Hedef tablo COUNT | Eşleşme |
| V-4 | Uygulama açılıyor mu? | Sayfa/istek testi | 500 yok |
| V-5 | FK bütünlüğü | Dış anahtar ihlali yok | 0 ihlal |
| V-6 | İndeks varlığı | `SHOW INDEX` | Tanımlı indeksler yerinde |
| V-7 | Yedek güncelliği | Yedek zaman damgası | Sürüş sonrası yedek |
| V-8 | Log hatası | Uygulama logu | Yeni HATA satırı yok |

### §40.2 Denetim Kayıt Şeması (Tasarım — Uygulanmadı)

| Alan | Tip | Açıklama |
|---|---|---|
| run_id | GUID | Tek sürüş kimliği |
| dosya | VARCHAR | Migration dosya adı |
| baslangic | TIMESTAMP | Başlangıç |
| bitis | TIMESTAMP | Bitiş |
| sonuc | ENUM | OK / HATA / ATLANDI |
| ortam | ENUM | dev / test / prod |
| onay | VARCHAR | Prod onay vereni |

> **Durum:** Bu şema **repo'da uygulanmamıştır** — kayıt tablosu yok (⚠️ U-1).
> Yalnızca tasarım olarak taşınır; var olan sistem olarak sunulmaz.

## §41 Revizyon

| Tarih | Değişiklik |
|---|---|
| 2026-10-13 | İlk yazım — sürüş akışları + 3 dosya kanıtı + 4 URL |
| 2026-10-13 | §39–§40 eklendi (idempotentlik + denetim matrisi) |

## §42 Sürüm

| Alan | Değer |
|---|---|
| Versiyon | 1.0.0 |
| Tarih | 2026-10-13 |
| Durum | draft |
| Erişim | `.ai/architecture/K007-data/migrations/migrations-surucu.md` |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
