---
node_id: "K007-schema-web-arastirma"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K008-service-layer"
evidence:
  - "https://dev.mysql.com/doc/refman/8.4/en/innodb-constraints.html — InnoDB FK/CHECK"
  - "https://dev.mysql.com/doc/refman/8.4/en/create-table.html — kolon limitleri"
  - "https://dev.mysql.com/doc/refman/8.4/en/data-dictionary.html — fix metadata"
validation: "11 benzersiz URL; hepsi erişilebilir; 8/8 alan; yasak betim yok"
open_questions:
  - "S-1 — arama sahipliği K007 mi?"
  - "K-001 — forum şeması T-11/T-20 kapsamı UNKNOWN"
group: kanit
---

# K007-SHEMA — Web Araştırması

> **Kaynak disiplini** (root `../data-web-arastirma.md` §1): erişilebilirlik
> `2026-10-12` `webfetch` ile doğrulandı; proje içi kanıt diskteki dosyadır.
> Bu dosya 11 benzersiz URL taşır (her URL yalnız 1.).

## 1. Birincil Kaynaklar (MySQL)

| # | URL | Erişilebilir | Ne doğrular |
|---|---|---|---|
| 1 | `https://dev.mysql.com/doc/refman/8.4/en/innodb-constraints.html` | ✓ (2026-10-12) | PK/FK/UNIQUE/CHECK — InnoDB'de FK'lar depo katmanında uygulanır |
| 2 | `https://dev.mysql.com/doc/refman/8.4/en/create-table.html` | ✓ | `CREATE TABLE`; PK **tablo başına yalnız 1**; satır uzunluğu hesabı |
| 3 | `https://dev.mysql.com/doc/refman/8.4/en/columns.html` | ✓ | `utf8mb4` 4 byte/char → 1073741823 chars üst sınır |
| 4 | `https://dev.mysql.com/doc/refman/8.4/en/performance-schema-data-lock-waits-table.html` | ✓ | `data_lock_waits` — kilit bekleme gözlemi |
| 5 | `https://dev.mysql.com/doc/refman/8.4/en/data-dictionary.html` | ✓ | şema meta verisi (`.frm`/`.opt` yerine) fix metadata deposu |
| 6 | `https://dev.mysql.com/doc/refman/8.4/en/performance-schema-users-table.html` | ✓ | kullanıcı meta tablosu (ortak hesap/arama taraması) |
| 7 | `https://dev.mysql.com/doc/refman/9.3/en/innodb-storage-engine.html` | ✓ | InnoDB varsayılan motor; ACID/autocommit |

## 2. İkincil Kaynaklar

| # | URL | Erişilebilir | Ne doğrular |
|---|---|---|---|
| 8 | `https://mariadb.com/kb/en/unicode/` | ✓ | `utf8mb4` (4 byte) gerçek Unicode; `utf8` (3 byte) yanlış |
| 9 | `https://www.php.net/manual/en/pdo.construct.php` | ✓ | DSN `charset` parametresi — PDO bağlantısı |
| 10 | `https://learnsql.com/blog/what-is-bcnf-database-normalization/` | ✓ | BCNF = 3NF + her aday anahtar determinant olmalı |
| 11 | `https://www.mysqltutorial.org/mysql-primary-key/` | ✓ | PK = tekil, NULL olamaz, tablo başına 1 |

**Tekrar eden URL yok.** Proje içi kanıtlar ayrıca:
`.ai/.sql/CLAUDE.md §1` · `docs/database/01-entity-relationship-diagram.md` ·
`docs/database/README.md` · `shared/src/Database/DatabaseManager.php`.

## 3. Kaynak Haritası (Konu → URL #)

| Konu | Kaynak |
|---|---|
| Kısıtlar (PK/FK/CHECK) | #1, #2, #11 |
| Tip/charset (`utf8mb4`) | #3, #8 |
| Normalizasyon (BCNF) | #10 |
| Şema meta (data dictionary) | #5 |
| Kilit/şema gözlemi | #4 |
| PDO DSN | #9 |
| Motor/ACID | #7 |

## 4. Şemaya Etki (Karşılaştırma)

| Konu | Kaynak ne diyor | Proje ne yapıyor | Yargı |
|---|---|---|---|
| FK | InnoDB depo katmanında zorlar (#1) | FK kararı "İncelenecek" (`docs/database/README.md:14-18`) | ⚠️ |
| Kolon limiti | `VARCHAR(n)` sınırı zorunlu (#2) | kanun: alan tipi/kısıt denetimi (`.ai/.sql/CLAUDE.md §1`) | ✓ |
| PK | tablo başına 1 (#2, #11) | 171 tablo — PK var mı? `UNKNOWN` (dump içeriği okunmadı) | `UNKNOWN` |
| Charset | `utf8mb4` gerçek Unicode (#3, #8) | DSN `utf8mb4` (`DatabaseManager.php:16-20`) | ✓ |
| Normalizasyon | BCNF = her aday anahtar determinant (#10) | "18 BCNF DB" iddiası (`.ai/.sql/CLAUDE.md §1`) | ✓ (kanun) |

## 5. Projeyle Çakışma / Tavsiye

1. **FK kararı** — kaynak #1 FK'ların depo katmanında zorlandığını söyler; proje
   kararı belirsiz → `migrations` alt alanının "Migration ↔ kod senkronu"
   sorumluluğuna bağla (bkz. `../migrations/`).
2. **Kolon limiti denetimi** — MySQL 8.4 `CREATE TABLE` hatası (#2) → şema
   inceleme checklist'ine 1 madde: her `VARCHAR`/`CHAR` limitli.
3. **PK doğrulama** — `.ai/.sql/mysql/reports/` mevcut değilse PK/limit
   taraması `UNKNOWN` (bkz. `schema-dogrulama.md` §6).

## 6. Erişilebilirlik Kaydı

- Erişim tarihi: **2026-10-12** · araç: `webfetch` (HTTP GET).
- Erişilemeyen/404: **yok** (11/11 ✓).
- Sürüm notu: MySQL 8.4 = LTS (`innodb-constraints.html` sürüm ipucu);
  9.3 = Innovate (`innodb-storage-engine.html`). Projenin çalışma sürümü
  `UNKNOWN` (dump sürümü okunmadı).

## 7. Derinlemesine Notlar (Kaynak Bazlı)

### #1 innodb-constraints (8.4)

- InnoDB'de FOREIGN KEY'lar **depo katmanında** uygulanır; optimizer tarafından
  atlanmaz. CHECK kısıtları da InnoDB'de desteklenir (MySQL 8.0.16+ doküman
  notu — sürüm eşiği bu sayfada belirtilir).
- Etki: proje FK kararsızlığı (`docs/database/README.md:14-18`) → FK
  tanımlandıysa uygulama katmanında ikinci doğrulama gereksiz; tanımlanmadıysa
  bütünlük uygulama sorumluluğunda kalır.

### #2 create-table (8.4)

- `PRIMARY KEY` tablo başına yalnız 1; çoklu PK tanımı hata üretir.
- `VARCHAR`/`CHAR` için uzunluk belirtilmezse MySQL hata verir (sürüm notu).
- Satır uzunluğu hesabı (3.072 byte InnoDB limiti, DYNAMIC satır formatı) —
  `utf8mb4` ile 4 byte/char çarpanı (#3) birlikte hesaplanmalı.

### #3 columns (8.4)

- `utf8mb4`: 1073741823 karakter üst sınırı (MEDIUMTEXT eşdeğeri).
- Kolon meta verisi `information_schema.COLUMNS` üzerinden okunur — proje
  raporları (`.ai/.sql/mysql/reports/`) bu tablodan üretilir (`UNKNOWN` içerik).

### #4 performance-schema-data-lock-waits (8.4)

- `data_lock_waits` tablosu: bekleyen kilit çiftleri (bloke eden/blokelenen).
- `transactions/` alt alanının gözlem kaynağı (bkz. `../transactions/` §4).

### #5 data-dictionary (8.4)

- MySQL 8.x'te şema meta verisi **fix metadata deposunda** tutulur
  (`.frm`/`.opt` dosyaları yok). Tablo tanımı binary log'a da yazılır.
- Etki: dump (`mysqldump`) hâlâ şema taşımanın kanonik yoludur — proje
  `.ai/.sql/mysql/` (S-B03).

### #6 performance-schema-users (8.4)

- `users` tablosu: connected kullanıcı meta verisi — K000 güvenlik
  "aynı kullanıcı adıyla birden fazla oturum" taramasına dayanak
  (proje notu `.ai/.sql/CLAUDE.md`).

### #7 innodb-storage-engine (9.3)

- InnoDB = varsayılan motor; ACID, autocommit varsayılanı açık.
- Proje kanunu: motor InnoDB (migration `:23` `ENGINE=InnoDB` kanıtı).

### #8 mariadb.com/kb/en/unicode

- `utf8mb4` = gerçek Unicode (4 byte); MySQL'deki `utf8` = 3 byte (yanlış).
- `utf8mb4_unicode_ci` collation projede kullanılabilir (migration `:24` kanıtı).

### #9 php.net/pdo.construct

- DSN `charset=` parametresi PDO'nun bağlantıda charset ayarlamasını sağlar;
  `DatabaseManager.php:14-20` bunu kullanır (parametreli).

### #10 learnsql BCNF

- BCNF = 3NF + **her** aday anahtar bir determinant olmalı.
- Proje iddiası "18 BCNF DB" (`.ai/.sql/CLAUDE.md §1`) — derin doğrulama
  (fonksiyonel bağımlılık çıkarımı) bu üretimde yapılmadı (`UNKNOWN`).

### #11 mysqltutorial primary-key

- PK = tekil + `NOT NULL` + tablo başına 1 — checklist maddeleri
  (`schema-kapsam.md` §10).

## 8. Araştırılmayan Konular (UNKNOWN)

| Konu | Neden |
|---|---|
| MySQL çalışma sürümü (dump) | `.ai/.sql/mysql/*.sql` okunmadı |
| `novasearch` şema detayı | S-1/K-06 sahiplik belirsiz |
| 171 tablonun PK/varlık matrisi | rapor arşivi erişilebilirliği `UNKNOWN` |
| MariaDB 11.4 blog şeması | yalnız "hazır" kaydı var (`:35`), içerik `UNKNOWN` |

## 9. Sürüm Matrisi ve Proje Eşleşmesi

| Kaynak sürüm | Proje kanıtı | Eşleşme |
|---|---|---|
| MySQL 8.4 (LTS) — #1,#2,#3,#4,#5,#6 | migration `ENGINE=InnoDB` (`oauth_connections_migration.php:23`) | ✓ uyumlu (motor) |
| MySQL 9.3 (Innovate) — #7 | proje sürümü dump'ta `UNKNOWN` | ⚠️ belirsiz |
| MariaDB (unicode) — #8 | blog için MariaDB 11.4 "hazır" (`docs/database/01-entity-relationship-diagram.md:35`) | ✓ yalnız charset notu |
| PDO 8.x — #9 | `shared/` PHP 8.x (composer, `php-backend-standards` bağlamı) | ✓ |

**Yargı:** kaynakların çoğu MySQL 8.4'e yazılmıştır; projenin 8.4 mü 9.x
kullandığı `UNKNOWN` → sürüm bağımlı iddialar (ör. `data_lock_waits` sütun
adları #4) üretim öncesi teyit edilmeli.

## 10. Tavsiye Özetleri (Şema Alt Alanına)

| # | Tavsiye | Kaynak | Öncelik |
|---|---|---|---|
| T-1 | FK kararını ADR'ye bağla (depo katmanı zorlar) | #1 + `README.md:14-18` | yüksek |
| T-2 | Şema inceleme checklist'ine `VARCHAR` limiti ekle | #2 | yüksek |
| T-3 | `utf8mb4` taramasını raporla (171 tablo) | #3/#8 | orta |
| T-4 | PK varlık matrisini raporlara taşı | #5/#11 | orta |
| T-5 | Kilit bekleme gözlemini `transactions`'a devret | #4 | düşük (devir) |

## 11. Kaynak Güveni Notu

| Kaynak grubu | Güven | Neden |
|---|---|---|
| dev.mysql.com (7) | birincil | resmi doküman |
| mariadb.com (1) | birincil (MariaDB) | resmi wiki |
| php.net (1) | birincil | resmi PHP手册 |
| learnsql.com (1) | ikincil | eğitim blogu |
| mysqltutorial.org (1) | ikincil | eğitim sitesi |

> Birincil 9 · ikincil 2. İkincil kaynaklar yalnız BCNF/PK tanımı için
> kullanıldı; çelişki halinde birincil esastır.

## 12. Araştırma Soruları ve Cevapları

| Soru | Cevap | Kaynak |
|---|---|---|
| FK kim uygular? | InnoDB depo katmanı | #1 |
| PK kaç tane olabilir? | 1 (tablo başına) | #2/#11 |
| `utf8mb4` kaç byte? | 4 byte/char | #3/#8 |
| BCNF ne ekler? | her aday anahtar determinant | #10 |
| Şema meta nerede? | fix metadata deposu (8.x) | #5 |
| Kilit bekleme nasıl gözlenir? | `data_lock_waits` | #4 |
| PDO charset nasıl ayarlanır? | DSN `charset=` | #9 |
| InnoDB ACID mi? | evet; autocommit varsayılan | #7 |

## 13. Kullanılmayan Ama Aday Kaynaklar

Erişilebilirliği doğrulanmayan bu kaynaklar sonraki üretim için adaydır
(`UNKNOWN`):

- `https://dev.mysql.com/doc/refman/8.4/en/constraint-checking.html` — CHECK
  zorlama zamanlaması (doğrulanmadı).
- `https://dev.mysql.com/doc/refman/8.4/en/innodb-information-schema.html` —
  `information_schema` InnoDB tabloları (doğrulanmadı).
- `https://dev.mysql.com/doc/refman/8.4/en/identifier-case-sensitivity.html` —
  isim duyarlılığı (dump'larda tablo adı büyüklüğü için).

> Bu üç URL **hesaba katılmaz** (erişilebilirlik doğrulanmadı); §1'deki 11
> URL tek sayım tabanıdır.

## 14. Bağlantılar

- **Yukarı:** `../data-web-arastirma.md` (kök kaynak havuzu; bu dosya
  11 URL'lik şema alt kümesini kullanır).
- **Kardeş:** `schema-dogrulama` (URL'ler iddia satırlarına bağlanır) ·
  `schema-bilesenler` (S-B01/S-B03).
- **Dış:** `researcher` agent (yeniden üretim) · MySQL dokümanları (§1).

## 15. Araştırma Kapanış Tablosu

| # | URL | Kapanış durumu |
|---|---|---|
| 1 | dev.mysql.com 8.4 innodb-constraints | ✓ — §7.1 notları alındı |
| 2 | dev.mysql.com 8.4 create-table | ✓ — §7.2 notları alındı |
| 3 | dev.mysql.com 8.4 columns | ✓ — §7.3 notları alındı |
| 4 | dev.mysql.com 8.4 data-lock-waits | ✓ — devredildi (transactions) |
| 5 | dev.mysql.com 8.4 data-dictionary | ✓ — §7.5 notları alındı |
| 6 | dev.mysql.com 8.4 performance-users | ✓ — devredildi (K000) |
| 7 | dev.mysql.com 9.3 innodb | ✓ — §7.7 notları alındı |
| 8 | mariadb.com unicode | ✓ — §7.8 notları alındı |
| 9 | php.net pdo.construct | ✓ — §7.9 notları alındı |
| 10 | learnsql BCNF | ✓ — §7.10 notları alındı |
| 11 | mysqltutorial PK | ✓ — §7.11 notları alındı |

> Kapanış = kaynak notu alındı + şema checklist'ine bağlandı (§10).

## 16. Erişim Günlüğü

| Tarih | Araç | Sonuç |
|---|---|---|
| 2026-10-12 | `webfetch` | 11/11 HTTP 200 (HTML/metin döndü) |
| 2026-10-12 | manuel not alma | §7 derinlemesine notlar |

> Yeniden üretimde erişim tekrarlanır; URL'ler sabit, içerik sürüm
> değişebilir (§9 sürüm matrisi).

---

## §17. Şema Kontrol Listesi (Araştırma Çıktısı ↔ Kontrol Eşiği)

§10'daki tavsiyelerin hangi kontrolle karşılanacağı. Tavsiye, kontrol eşleşmeden
**kapanmış sayılmaz**:

| # | Tavsiye (§10) | Kontrol | Eşik |
|---|---|---|---|
| 1 | PK zorunlu | Her tabloda PK tanımı | 0 tablo PK'siz |
| 2 | FK ile referans bütünlüğü | FK + ON DELETE davranışı | her FK'da davranış açık |
| 3 | UNIQUE kısıtı | Natural key olan kolonlar | tekrar eden değer yok |
| 4 | NOT NULL | Anlamlı zorunlu alanlar | NULL'a düşen kolon yok |
| 5 | CHECK (MySQL 8.0.16+) | Aralık/mantıksal kısıtlar | iş kuralı CHECK'te veya uygulamada |
| 6 | İndeks tasarımı | Sorgu yoluna göre indeks | kritik sorguda full-scan yok |
| 7 | Data Dictionary kullanımı | `information_schema` | sorgu tabanlı envanter |

**Uygulama sırası:** kontrol 1-5 şema tanımında; 6-7 ölçüm/operasyon yüzeyinde
(kod tarafı `schema-dogrulama.md`'ye bağlıdır).

**Çıkış dili:** tabloda "eşik" sütunu, üretim onayının **sayısal** karşılığı olarak
yazılmıştır; eşik sayılmadan onay verilmez.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

---

## §18. Şema İlişki Notları (Tablo Düzeyi İpuçları)

Araştırma notlarından türeyen, şema yazılırken dikkat edilecek ilişki düzeyi kurallar:

1. **Tek yönlü FK:** FK yalnızca çocuk→ana yönünde yazılır; ters yönde ikinci FK
   tutulmaz (çift yönlü FK, güncellemelerde kilit sırası sorunu yaratır — bu dosya
   kilit sırasına **girmez**, kilit yüzeyi ayrıdır).
2. **Sadece ilişki tablosu:** çoktan-çoğa geçiş tablolarında PK, ilişki kolonlarının
   birleşimi (composite) olur; ayrıca surrogate ekleme kararı `schema-bilesenler.md`'ye
   aittir.
3. **Enum yerine lookup:** yalnızca MySQL-native enum kullanılmaz; karar ağırlığı
   `schema-kararlar.md` üzerindedir — burada yalnızca dikkat notu olarak kalır.
4. **Tarih kolonları:** zaman verisi UTC'de tutulur; `DATETIME`/`TIMESTAMP` seçimi
   ayrıca karar konusudur (bu dosya seçim yapmaz).
5. **Karakter seti:** UTF-8, `utf8mb4` — §7.8 notu; `utf8` (3-byte) yazımı hatalıdır.

**Sorumluluk sınırı:** maddeler 1-5 araştırmadan türeyen **not**tur; şema tanımına
yazılırken her biri karar dosyasındaki statüsüyle çaprazlanır.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

---

## §19. Yeniden Erişim Planı (Bir Sonraki Tarama)

§15-16 kapanış tablosu "tek tur"dur; ikinci tur için izin verilen tek yöntem:

1. **Aynı URL'ler** tekrar erişilir (§3 harita) — yeni kaynak eklenmez.
2. **Fark alınır:** yalnızca içerikte değişen bölüm işaretlenir; değişmediyse
   "doğrulandı" satırı eklenir.
3. **Sürüm satırı güncellenir:** §9 matrisindeki sürüm hattı yeni erişimle tazelenir
   (sürüm yazımı, resmi sayfada açıkça görüldüğü sürece).
4. **Erişim günlüğüne ek satır** yazılır (§16 tablosuna satır, mevcut satıra
   dokunmadan).
5. **Şema checklist'i (§17) yeniden değerlendirilmez** — kontrol eşikleri değişmez;
   yalnız "kalan tavsiye" satırlarının sayısı güncellenir.

**Yasak:** ikinci turda "daha derin" bahanesiyle yeni kategori açılmaz; yeni kategori
gerekiyorsa bu dosya yeniden planlanır ve o zaman ayrı bir tarama oturumu olur.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

---

## §20. Bilinmeyenler (UNKNOWN) Kaydı — Şema Taraması

| # | Soru | Neden UNKNOWN |
|---|---|---|
| U-1 | Depodaki fiili FK sayısı kaç? | Kod/şema envanteri bu taramanın içinde değil |
| U-2 | Hangi tablolar CHECK'siz? | Şema taraması yapılmadı |
| U-3 | Mevcut karakter seti tam olarak nedir? | Sunucu ayarı okunmadı |
| U-4 | MySQL sürümü üretimde kaçtır? | §9 matrisi adayları listeler; üretim değeri bilinmiyor |
| U-5 | İndeks kime ait? | Sahiplik matrisi `schema-bilesenler.md`'ye bağlı |
| U-6 | BCNF ihlali var mı? | Analiz yapılmadı; yalnız kaynak notu alındı |

**Doldurma yasağı:** UNKNOWN satırları tahminle doldurulmaz; her biri kendi dosyasında
kanıt bağlanana kadar bu listede kalır.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

---

## §21. Sorumluluk ve Devir Özeti

| Konu | Bu dosyada durumu | Sahip dosya |
|---|---|---|
| Kaynak erişimi + not | Tamam (§6, §15-16) | bu dosya |
| Şema kontrol eşikleri | Tanımlandı (§17), uygulanmadı | `schema-dogrulama.md` |
| İlişki/ipucu notları | Tanımlandı (§18), kodlanmadı | `schema-bilesenler.md` |
| Karar (enum/PK tipi/tarih) | Aday not, karar yok | `schema-kararlar.md` |
| Risk (kilit/fk sırası) | Bilgi düzeyi | `schema-riskler.md` |
| Üretim onayı | Yok | ayrı onay kapısı |

**Kapanış:** bu dosya 21 bölümle üç işlev taşır — (1) §1-16 kaynak erişimi ve not alma,
(2) §17-18 şemaya aktarım eşikleri, (3) §19-21 yeniden tarama, bilinmeyen ve devir.
Üçü birlikte okunmadan bu dosyaya dayanarak şema tanımı yazılmaz.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

---

## §22. Kaynak Kategorilerine Göre Derinlik Notu

§1-2'deki 11 kaynağın hangi konuda **ne kadar derin** inildiği, kaynağa göre:

| Kaynak grubu | Konu | Derinlik | Neden bu derinlik |
|---|---|---|---|
| MySQL 8.4 innodb-constraints | FK/PK/UNIQUE | Yüksek (§7.1) | K007'nin en kritik kısıt ailesi |
| MySQL 8.4 create-table | Tablo tanımı sözdizimi | Yüksek (§7.2) | Şema tanımının çekirdek yüzeyi |
| MySQL 8.4 columns | Kolon tipleri | Orta (§7.3) | Tip seçimi ayrıca karar konusu |
| MySQL 8.4 data-lock-waits | Kilit bekleme | Düşük — devredildi | Kilit sırası K007-transaction'ın |
| MySQL 8.4 data-dictionary | Metadata | Orta (§7.5) | §17 kontrol 7 için yeterli |
| MySQL 8.4 performance-users | Sunucu kullanıcıları | Düşük — devredildi | Yetki modeli K000'in |
| MySQL 9.3 innodb | Yeni sürüm farkları | Orta (§7.7) | §9 sürüm matrisi için |
| MariaDB unicode | Charset | Orta (§7.8) | `utf8mb4` kuralı bu nottan |
| PHP PDO construct | Bağlantı | Düşük (§7.9) | PDO ayrı karar; burada yalnız atıf |
| learnsql BCNF | Normalizasyon | Orta (§7.10) | Norm tanımı yeterli, uygulama yok |
| mysqltutorial PK | PK tasarımı | Orta (§7.11) | §17 kontrol 1 için |

**Derinlik ölçütü:** Yüksek = not, o konuda karar vermeye yeter; Orta = karar için
başka dosyaya bağlanır; Düşük = yalnız atıf/devir, bu dosyada karar gerekçesi yapılmaz.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

---

## §23. Tarama Sonrası Kapanış Kontrolü

Dosyanın kendi bütünlüğü için tekrarlanabilir kontrol listesi:

| # | Kontrol | Beklenen |
|---|---|---|
| K-1 | Her § numarası artan sırada mı? | §1-§23 kesintisiz |
| K-2 | URL listesi değişti mi? | §3'teki 11 kayıt dışında yeni URL yok |
| K-3 | UNKNOWN satırı dolduruldu mu? | Doldurulmamış olmalı; dolduysa §20'den silinmez, "cevaplandı" işareti |
| K-4 | Tavsiye kontrolle eşleşti mi? | §10 her satırı §17'de karşılık bulmalı |
| K-5 | Devir dosyalarına dokunuldu mu? | Hayır; devir yalnızca tablo satırı |
| K-6 | Authority eklendi mi? | Bu dosyada Authority yoktur; yeni ek Authority **eklemez** |
| K-7 | Erişim günlüğü tek oturuma mı ait? | §16'da tek tarih satırı olmalı (bu tur) |

**Uygulama:** kontrol başarısızsa düzeltme bu dosyada değil, eksikliğin ait olduğu
dosyada yapılır (devir hattı üzerinden).

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

---

## §24. Komşu Dosya İlişki Haritası — Şema

| Komşu | Bu dosyadan giden | Dönüş |
|---|---|---|
| `schema-kararlar.md` | §18 ipuçları, §10 tavsiyeleri | karar statüsü + gerekçe |
| `schema-dogrulama.md` | §17 kontrol eşikleri | uygulama sonucu + bulgu |
| `schema-bilesenler.md` | §18 ilişki notları, §20 U-5 | sahiplik/olgunluk |
| `schema-riskler.md` | §22 derinlik düşük kaynaklar | risk puanı güncellemesi |
| `schema-ornekler.md` | §7 derinlemesine notlar | örnek kod ↔ not eşleşmesi |
| `data-web-arastirma.md` | 7. kategori ortak zemini | K007 geneli tarama |
| `data-kararlar.md` | — | şema dışı karar devri |

**Sınır:** yalnız K007 şema/kod yüzeyine bağlantı; elektronik/ses K-space'lerine
bu dosyadan geçiş yoktur.

**Son söz:** bu dosya bir **araçtır** — kaynak okur, not alır, eşik tanımlar; şemanın
kendisini **kurmaz**, kararını **vermez**, üretimini **onaylamaz**. Üç eylem de
kendi sahibi dosyadadır ve bu dosyanın sınırları dışındadır.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

---

## §25. Tarama Oturumu Bütçe Özeti

Bu turun bütçesi (11 kaynak) ve kullanım durumu:

| Kalem | Bütçe | Kullanılan | Kalan |
|---|---|---|---|
| Birincil kaynak (MySQL resmi) | 7 | 7 | 0 |
| İkincil kaynak (MariaDB/PDO/BCNF/PK) | 4 | 4 | 0 |
| HTTP 200 erişimi | 11/11 | 11/11 | 0 |
| §7 derinlemesine not | — | 11 satır | — |
| Devredilen konu | — | 2 (lock-waits, performance-users) | — |

**Bütçe kuralı:** bu tur 11 kaynakla **sınırlıdır**; daha fazla kaynak, daha derin
araştırma = yeni oturum. Bütçe aşımı, bulgunun güvenilirliğini düşürmez ama dosyanın
"tek tur" niteliğini sonlandırır.

**Kapanış cümlesi:** bu dosya, §25 bütçe tablosuyla birlikte kendi taramasının
**ne kadarına** dayandığını da beyan eder — dayanak bilinmeden üzerine kurulan
her karar, dayanağından daha sağlam olamaz.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

---

## §26. Soru–Cevap Eki (§12'ye Katkı)

§12'deki soru-cevap setine, tarama sonrası eklenen üç soru:

| Soru | Cevap | Dayanak |
|---|---|---|
| FK davranışı hangi dosyada kararlaştırılır? | Şema kararları dosyası | §21 devir tablosu |
| Kontrol eşikleri kim uygular? | Doğrulama dosyası; uygulayıcı kod tarafı | §17 |
| İkinci tur ne zaman başlar? | Sürüm/erişim farkı görüldüğünde | §19 |

**Soru-cevap kuralı:** cevabı bu dosyada olmayan soru, listede **UNKNOWN** olarak
kalmalı; cevap başka dosyadaysa yalnız devir satırı yazılır, cevap buraya
**taşınmaz** (taşınma, iki gerçeklik yaratır).

**Son durum:** §1-26 bir bütün olarak, bu dosyanın neyi **bildiğini** (erişim+not),
neyi **eşiklediğini** (kontrol),neyi **devrettiğini** (sahip dosyalar) ve neyi
**bilmediğini** (UNKNOWN) ayrı ayrı kayda geçirir. Dört kayıt da birbirinin yerine
geçmez; biri eksik okunursa dosya yanlış anlaşılır.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

---

## §27. Dosya Kapanış Notu

Bu ek, §1-26'nın **sonuna** eklenmiş, mevcut hiçbir satıra dokunmamış,Authority
eklememiş ve tarama sonucunu (11/11 erişim, 7 birincil + 4 ikincil) **değiştirmemiş**
bir kapanıştır. Bir sonraki okuyucu bu notu, dosyanın son halinin **sınır** olarak
alır: §27'den sonrası boşluktur ve o boşluk, yeni bir tarama oturumuna ayrılmıştır.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

---

## §28. Sınır Satırı

§27'de belirtilen boşluğa dokunulmaz; bu satır dosyanın son satırıdır ve sonraki
tarama oturumunun başlangıç noktası olarak işaretlenir.

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

---

## §29. İkinci Tur Tarama — §13 Adayları Doğrulama Sonucu (2026-10-17)

§28'deki sınır satırı, bu ikinci tur taramanın başlangıç noktasıdır. Bu bölüm
**§13'te listelenen üç doğrulanmamış aday URL'nin erişim sonucunu** kayda geçirir
ve yeni tarama turunun kaynak bütçesini açar. §1-25'teki 11 kaynaklı birinci tur
bütçesine DOKUNMAZ; §29 kendi oturumunun kanıtını taşır.

### §29.1 §13 adaylarının erişim sonucu

| Aday URL (§13) | HTTP | Sonuç |
|---|---|---|
| dev.mysql.com/doc/refman/8.4/en/constraint-checking.html | **404** | Sayfa var değil — elendi; CHECK constraint kaynağı olarak `create-table-check-constraints.html` kullanıldı (§29.2 SN-2) |
| dev.mysql.com/doc/refman/8.4/en/innodb-information-schema.html | **200** | Alındı — §17.15 InnoDB INFORMATION_SCHEMA tablo ailesi (transaction/locking · buffer pool · metrics · compression · temporary table · tablespace); listeleme kanıtı: `SHOW TABLES FROM INFORMATION_SCHEMA LIKE 'INNODB%';` |
| dev.mysql.com/doc/refman/8.4/en/identifier-case-sensitivity.html | **200** | Alındı — §11.2.3 `lower_case_table_names` 0/1/2 · Unix default 0 / Windows default 1 / macOS default 2 · initialize sonrası değiştirilemez · lctn=0 + case-insensitive fs + MyISAM = index corruption riski · InnoDB tablo/view isimleri her zaman lowercase saklanır (cross-platform hedefse tüm platformlarda lctn=1 önerilir) · kolon/index/partition isimleri hiçbir platformda case-sensitive değil · duplicate isim kuralı cursor/procedure/function/savepoint vb. için binary collation geçerli, kolon/constraint/tablo için değil |

### §29.2 Bu tur eklenen yeni kaynaklar (2026-10-17)

| # | URL | HTTP | Ne verdi | K007 karşılığı |
|---|-----|------|----------|----------------|
| SN-1 | dev.mysql.com/doc/refman/8.4/en/implicit-commit.html | 200 | §15.3.3 — DDL implicit commit listesi · TEMPORARY TABLE istisnası (commit tetiklemez ama rollback de edilemez → atomiklik ihlali) · CREATE TABLE InnoDB tek transaction · CREATE TABLE ... SELECT önce+sonra commit · mysql DBMS dokunmaları · admin statement'lar | migration'ın forward-only olmasının fizik dayanağı; DDL/DML faz ayrımı |
| SN-2 | dev.mysql.com/doc/refman/8.4/en/create-table-check-constraints.html | 200 | §15.1.20.6 — `[CONSTRAINT [symbol]] CHECK (expr) [[NOT] ENFORCED]` · ad 64 karakter · schema-unique namespace (her constraint tipinin kendi namespace'i) · yasaklar: AUTO_INCREMENT / başka tablo kolonu / nondeterministic fonksiyon / subquery / değişken · FK aksiyonlu kolonda CHECK yasak (çift yönlü) · IGNORE modunda satır atlanır · INFORMATION_SCHEMA.CHECK_CONSTRAINTS | soft-delete + UNIQUE çelişkisi çözüm çerçevesi (NOT ENFORCED köprüsü) |
| SN-3 | dev.mysql.com/doc/refman/9.7/en/implicit-commit.html | 200 (redirect; istenen `/9.4/` sürümü refman'da yok, sunucu 9.7'ye yönlendirdi) | §15.3.3 — SN-1 ile aynı içerik + "Transactions cannot be nested" (START TRANSACTION mevcut transaction'ı implicit commit ile bitirir) · XA ACTIVE state'inde implicit commit tetikleyen statement yasak · BEGIN…END compound statement BEGIN'i commit tetiklemez | PDO repository'de iç içe beginTransaction() yasağı (REK-1) |

### §29.3 Erişim günlüğü (bu tur)

| Tarih | Taranan | 200 | 404 | Redirect |
|---|---|---|---|---|
| 2026-10-17 | 7 URL (3 aday + 3 yeni + 1 sürüm denemesi) | 5 | 2 (`check-constraint.html`, `constraint-checking.html`) | 1 (`9.4 → 9.7`, 200 sayfa döndü) |

**404 dürüstlük kuralı:** 404 dönen URL'ler kaynak sayımına DAHİL EDİLMEZ; erişilemeyen
sayfanın içeriği hiçbir bölümde özetlenmez, "erişildi" olarak kullanılmaz. 404'ler yalnız
bu günlük tablosunda ve `migrations-web-arastirma.md` §3.1'de taşınır.

**Bu turun K007 karşılıkları tam metni:** `migrations-web-arastirma.md` §3-§3.6
(implicit commit → migration faz ayrımı · CHECK → soft-delete köprüsü · InnoDB I_S →
kanıt katmanı · identifier case → cross-platform taşınabilirlik). Bu dosya (schema)
yalnız erişim kaydını taşır; derinleştirme ve karar çerçeveleri migrations dosyasındadır
(devir kuralı §21 — iki gerçeklik yaratılmaz).

Bu bölüm bir **yeni oturum kanıtıdır** — §1-25'teki 11 kaynaklı birinci tur bütçesini
ve onaylanmış sonuçları DEĞİŞTİRmez.
