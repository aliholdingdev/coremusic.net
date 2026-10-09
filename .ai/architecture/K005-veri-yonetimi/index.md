---
title: "K005 DATA — Katman Index"
type: index
category: architecture
version: "1.0.0"
status: draft
authority: "SSOT: .ai/architecture/K005-veri-yonetimi/index.md — K-space band-1 katmanı (ADR-096)"
updated: 2026-10-08
tier: 3
domain: K005-veri-yonetimi
ssot: true
risk: medium
owner: data
depends-on: [".ai/architecture/00-kspace-anayasa.md"]
---

# K005 DATA — Katman Index

**Künye**

| Alan | Değer |
|---|---|
| Kategori | architecture (katman `index.md`) |
| Durum | draft (FM `status: draft` · katman durumu = PROPOSED — R16.2, 👤 onayı bekliyor) |
| Tarih | 2026-10-08 |
| Yazar | Claude — Band-1 üretim ajanı (F1 KAPI 9) |
| Onay | Vault Steward (Kapı 10) — **BEKLİYOR**, bu dosya vault'a taşınmadı |
| Bant | Bant 1 — `K000-K020 EXISTING FOUNDATION` (21 kart) |
| Uçak | SOFTWARE (EK A §A.0) |
| Teatral epitet | «FENER» — yalnız sıfat, K-ID'yi ezmez (R2.3) |
| Sahip (owner) | `data` (Data Engineer — AGENTS.md §4 madde 5: MySQL 18 BCNF · PDO · migration) |
| Üretim yeri | staging (`b1-K005-veri-yonetimi.md`) → hedef `.ai/architecture/K005-veri-yonetimi/index.md` |

> **Durum etiketleri (F1 §5.4):** `[CURRENT]` (repo kanıtlı) · `[TARGET]` ·
> `[PROPOSED]` · `[PLANNED]`/`[DESIGN]` · `[VERIFY REQUIRED]`. Bu kartta
> **APCu = IMPLEMENTED, Redis = PLANNED** ayrımı `.ai/CLAUDE.md` §12
> hizalamasıyla korunur (APCu `CacheManager` zinciri mevcut; Redis adaptör yok).

---

#### §1 Genel Bakış

K005, CoreMusic'in **veri sahibi katmanıdır**: MySQL, BCNF şemaları,
işlemler (transactions), indeksler, Redis, APCu, SQLite ve Restic yedekleme
(EK A §A.1 K005 kartı). `.ai/CLAUDE.md` §5 K5 guardrail'i bağlayıcıdır:
**"18 Veritabanı kesinlikle BCNF kurallarına uymalıdır."** Band-1 içinde
**tek veri sahibi** budur — üst katmanların tamamı (K004-K020) veriye yalnız
port/adapter üzerinden erişir ve birbirleriyle doğrudan tablo paylaşamaz
(H19 · YARGI 2). Repo'da 20 şema dosyası, PDO tabanlı `DatabaseManager`,
`CacheManager` + APCu adaptörü ve migration dosyaları **mevcuttur**.

### 1.1 Kapsam Dışı (K005 ne YAPMAZ)

| # | Kapsam dışı | Asıl sahip | Kaynak |
|---|---|---|---|
| 1 | Kimlik/credential **kararı** (JWT/CSRF/CSP) | K006 | `.ai/CLAUDE.md` §5 K6 · ADR-034 (credential vault normalizasyonu — şema sahibi K005) |
| 2 | İş kuralı / use-case yürütme | K008 | EK A §A.1 K008 kartı |
| 3 | API sözleşmesi / CQRS okuma-yazma ayrımı | K009 | EK A §A.1 K009 kartı |
| 4 | Medya dosyasının kendisi (FLAC/MP3) | K015 | EK A §A.1 K015 kartı |
| 5 | Log/metrik üretimi | K012 | R7.1 (`coremusic_logs` şeması K005'indir) |
| 6 | Backup **aracı politikası** (hangi sistem) | K005'tir; **DR hedefi** K013/K012 ile | §12 Backup = "Disaster Recovery" |

---

## §2 Özet Satır — 16 Alan (R4.1 · F1 §10.2 kontratı)

| K-ID | KANONİK_AD | TEATRAL_EPİTET | DOMAIN | RUNTIME | SORUMLULUK | GİRDİ | ÇIKTI | İZİNLİ_BAGIMLILIK | YASAK_BAGIMLILIK | DATA_BOUNDARY | SECURITY_BOUNDARY | FAILURE_MODE | OBSERVABILITY | TEST | KANIT |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| K005 | DATA | «FENER» | DATA | server · desktop · edge | MySQL · BCNF Schema · Transactions · Indexes · Redis · APCu · SQLite · Restic Backup (EK A, 8 madde) | SQL sorgusu (prepared statement) · migration · cache anahtarı · yedekleme tetiği · port üzerinden gelen okuma/yazma isteği | sorgu sonucu (DTO) · transaction sonucu · cache hit/miss · yedek arşivi · hata kodu | K000 · K001 · K002 · K003 · K004 (EK A: izinli=K000-K004) + port/adapter | üst katmana (K006-K020) doğrudan erişim · geri çağrı (H20) · katmanlar arası doğrudan tablo paylaşımı (H19) · `SELECT *` · ORM | **veri sahibi**: 20 şema dosyası (18 BCNF + `media_catalog` + `novasearch`); paylaşılan tablo YOK (YARGI 2 · R4.4d) | security=ORTA (EK A) · OWASP A05:2025 Injection → prepared statement (M06/SEC08) · credential şeması ADR-034 · `SELECT *`/ORM yasak (ADR-001/002) | fail-over (EK A) · servis/disk hatasında yeniden deneme + yedekten kurtarma (restore) · cache hatasında cache'i devre dışı bırakma | sorgu süresi (EXPLAIN) · cache hit/miss · bağlantı havuzu · yedek sonucu [PLANNED — K012 ile] | BCNF denetimi (ADR-040/FD tabanlı) · migration geri alma · backup restore doğrulama · birim test (§17 ≥80%) [PLANNED] | `.ai/architecture/00-kspace-anayasa.md` satır 94-97 · `.ai/CLAUDE.md` §5 K5 satırı (116) + §18 (18 BCNF) + §12 (APCu/Redis) + §21 (ORM/SELECT * yasak) · `git ls-files .ai/.sql/mysql/` = 20 · `shared/src/Database/DatabaseManager.php` · `shared/src/Cache/{CacheManager,ApcuAdapter}.php` · `shared/database/migrations/*` (3) · ADR-003/040/014/022/033/007/027/050/034/041/081 · EK B #16 |

### 2.1 Alan bazlı değer gerekçesi (R4.1 kontratının açılımı)

| # | Alan | Bu karttaki değer | Gerekçe (kaynak) |
|---|---|---|---|
| 1 | K-ID | `K005` | EK A §A.0 (R2.3) |
| 2 | KANONİK_AD | `DATA` | EK A §A.0 |
| 3 | TEATRAL_EPİTET | «FENER» | EK A §A.0 |
| 4 | DOMAIN | `DATA` | katmanın konusu veri yönetimidir |
| 5 | RUNTIME | server · desktop · edge | EK C enum + §14 (NAS/edge SQLite) |
| 6 | SORUMLULUK | 8 madde | EK A §A.1 K005 — birebir |
| 7 | GİRDİ | SQL · migration · cache anahtarı · yedek tetiği · port isteği | Sorumluluk + F1 §8.8'den türetildi |
| 8 | ÇIKTI | DTO · transaction sonucu · cache durumu · yedek arşivi · hata | aynı türetme |
| 9 | İZİNLİ_BAGIMLILIK | K000-K004 | EK A satır 96 "izinli=K000-K004" |
| 10 | YASAK_BAGIMLILIK | K006-K020 doğrudan · H20 · H19 | EK A satır 96 |
| 11 | DATA_BOUNDARY | veri sahibi (20 şema) | EK A satır 96 + §18 |
| 12 | SECURITY_BOUNDARY | ORTA + A05 + ADR-002/001 | EK A satır 96 + F1 §9.1 |
| 13 | FAILURE_MODE | fail-over | EK A satır 96 + restore (EK B #16) |
| 14 | OBSERVABILITY | sorgu süresi · cache · bağlantı · yedek | tasarım [PLANNED], owner K012 |
| 15 | TEST | BCNF denetimi · migration geri alma · restore | ADR-040 + D04/D05 |
| 16 | KANIT | anayasa + repo (20+ dosya) + ADR (11) + EK B #16 | R9.5 3'lü format |

---

## §3 Tam Kimlik Kartı — EK C (20 alan · R4.2/R4.3)

```yaml
K-ID:               K005
KANONİK_AD:         DATA
TEATRAL_EPİTET:     «FENER»
DOMAIN:             DATA
SUBDOMAIN:          relational-store                   # MySQL · BCNF schema · transactions · indexes
BOUNDED_CONTEXT:    cache-and-recovery                 # Redis · APCu · SQLite · Restic backup
RUNTIME:            server · desktop · edge
SORUMLULUK:         MySQL · BCNF Schema · Transactions · Indexes · Redis · APCu ·
                    SQLite · Restic Backup
GIRDI:              SQL sorgusu (prepared statement) · migration dosyası · cache
                    anahtarı · yedekleme tetiği · port/adapter üzerinden okuma/yazma
CIKTI:              sorgu sonucu (DTO) · transaction sonucu · cache hit/miss ·
                    yedek arşivi · hata kodu
IZINLI_BAGIMLILIK:  [K000, K001, K002, K003, K004]     # EK A: izinli=K000-K004
YASAK_BAGIMLILIK:   [K006..K020 doğrudan erişim, geriye çağrı H20, katmanlar arası
                    doğrudan tablo paylaşımı H19, SELECT *, ORM]
DATA_BOUNDARY:      VERİ SAHİBİ — 20 şema dosyası (18 BCNF + media_catalog +
                    novasearch); başka katmanla paylaşılan tablo YOK; erişim yalnız
                    port/adapter (YARGI 2 · R4.4d)
SECURITY_BOUNDARY:  security=ORTA (EK A) · OWASP A05:2025 Injection → prepared
                    statement zorunlu (M06 · SEC08) · SELECT * ve ORM yasak
                    (ADR-001/ADR-002) · credential tabloları ADR-034 kapsamındadır
FAILURE_MODE:       fail-over (EK A) — bağlantı/servis hatasında yeniden deneme ·
                    disk/veri kaybında Restic restore · cache hatasında cache'i
                    devre dışı bırakma (yanıt üretilir)
OBSERVABILITY:      sorgu süresi (EXPLAIN) · cache hit/miss · bağlantı havuzu ·
                    yedek sonucu [PLANNED — K012 ile]
TEST:               BCNF/FD tabanlı denetim (ADR-040) · migration geri alma ·
                    backup restore doğrulama · birim test (§17 ≥80%) [PLANNED]
KANIT:              .ai/architecture/00-kspace-anayasa.md satır 94-97 |
                    .ai/CLAUDE.md §5 K5 satırı (116) + §18 (18 BCNF/156 tablo) +
                    §12 (APCu IMPLEMENTED · Redis PLANNED) + §21 |
                    git ls-files .ai/.sql/mysql/ = 20 · shared/src/Database/ ·
                    shared/src/Cache/ · shared/database/migrations/ (3 dosya) |
                    ADR-003 · ADR-040 · ADR-014 · ADR-022 · ADR-033 · ADR-007 ·
                    ADR-027 · ADR-050 · ADR-034 · ADR-041 · ADR-081 |
                    web: EK B #16 (restic.readthedocs.io · restic.net, er. 2026-10-08,
                    güven 94)
kanit-tarihi:       2026-10-08
kart-durumu:        PROPOSED (R16.2 — 👤 onayı bekliyor)
```

**Kart kalite kapıları (R4.4):**

| Kapı | Sonuç |
|---|---|
| (a) Her alan dolu | GEÇTİ — 20/20 (18 EK C + `kanit-tarihi` + `kart-durumu`) |
| (b) IZINLI ∩ YASAK = ∅ | GEÇTİ — IZINLI = {K000..K004}; YASAK = {K006..K020, H20, H19} → ∅ |
| (c) KANIT `⚠️` ise research kapısı | HAYIR — 3 ayağın tamamı dolu (repo · ADR · web #16) |
| (d) Aynı veri sınırı iki katman paylaşırsa YARGI ihlali | GEÇTİ — K005 tek veri sahibi |

**Epitet kalite notu:** «FENER» EK A §A.0'dan alınmıştır (R8.1); epitet kimliği ezmez (R2.3).

---

## §4 Sorumluluk Derinliği

### 4.1 EK A kartı Sorumluluk maddeleri (00-kspace-anayasa.md §A.1 K005)

| # | Kalem (EK A) | Ne yapar | Durum | Kanıt |
|---|---|---|---|---|
| 1 | MySQL | Birincil ilişkisel motor (MySQL 9) | IMPLEMENTED (erişim katmanı) | `shared/src/Database/DatabaseManager.php` · `DatabaseRegistry.php` · `.ai/CLAUDE.md` §12 Database (Primary) = MySQL 9 |
| 2 | BCNF Schema | 18 BCNF veritabanı / 156 tablo hedefi + 20 şema dosyası | IMPLEMENTED (şema dosyaları) | `git ls-files .ai/.sql/mysql/` = 20 (§4.2) · §18 |
| 3 | Transactions | İşlem sınırı (service'te tanımlanır — F1 §8.7 B04) | DESIGN | F1 §8.7 B04 · `⚠️` kod kanıtı okunmadı |
| 4 | Indexes | İndeks tasarımı; sorgu EXPLAIN ile ölçülür | DESIGN | F1 §8.8 D03 · `⚠️` |
| 5 | Redis | Paylaşılan cache | **PLANNED** | `.ai/CLAUDE.md` §12: "Redis: PLANNED (hedef: symfony/cache+predis; adapter yok)" |
| 6 | APCu | Yerel cache — `CacheManager` zinciri | **IMPLEMENTED** | `shared/src/Cache/ApcuAdapter.php` · `CacheManager.php` · §12 "APCu (CacheManager zinciri)" |
| 7 | SQLite | Çevrimdışı/yerel depolama (Offline-First kuyruk) | IMPLEMENTED (test/src) | `shared/tests/Repository/Sqlite/SqliteDatabaseManager.php` · §22 "Network outage → Offline-First + SQLite queue" |
| 8 | Restic Backup | Periyodik yedek + doğrulanmış restore | TARGET | EK B #16 (restic resmi dokümantasyon, güven 94) · F1 §8.8 D05 · §12 Backup = "Disaster Recovery" [hedef] |

### 4.2 Anayasa §5 K5 satırındaki gerçek kapsam (`.ai/CLAUDE.md` §5, satır 116)

**Satır:** `K5 Veri Yönetimi | MySQL 9 (18 BCNF, 156 tablo), Redis, APCu, SQLite, Restic | 55 | 18 Veritabanı kesinlikle BCNF kurallarına uymalıdır`

| Alt kapsam | Kapsam | Durum | Kanıt |
|---|---|---|---|
| MySQL 9 | birincil motor | TARGET (sürüm §12/§24) | §5 satır 116 · §12 |
| 18 BCNF DB / 156 tablo | şema hedefi | TARGET (hedef) + şema dosyaları var | §18 · `git ls-files .ai/.sql/mysql/` = 20 |
| Redis | paylaşılan cache | **PLANNED** (adapter yok) | §12 cache satırı |
| APCu | yerel cache | **IMPLEMENTED** | §12 · `shared/src/Cache/ApcuAdapter.php` |
| SQLite | çevrimdışı depolama | IMPLEMENTED (test yüzeyi) | `shared/tests/Repository/Sqlite/SqliteDatabaseManager.php` |
| Restic | yedek | TARGET | §12 · EK B #16 |
| **Hard Guardrail** | "18 Veritabanı kesinlikle BCNF kurallarına uymalıdır" | BAĞLAYICI | §5 satır 116 |
| "55 bileşen" | §5 sayım sütunu | **HEDEF** | H10: hedef ≠ kanıt |

**Şema envanteri (`.ai/.sql/mysql/` — 20 dosya, 2026-10-08 `git ls-files`):**

| # | Dosya | # | Dosya |
|---|---|---|---|
| 1 | `coremusic_ai.sql` | 11 | `coremusic_neva.sql` |
| 2 | `coremusic_albums.sql` | 12 | `coremusic_patch.sql` |
| 3 | `coremusic_api.sql` | 13 | `coremusic_playlist.sql` |
| 4 | `coremusic_auth.sql` | 14 | `coremusic_social.sql` |
| 5 | `coremusic_catalog.sql` | 15 | `coremusic_studio.sql` |
| 6 | `coremusic_cms.sql` | 16 | `coremusic_system.sql` |
| 7 | `coremusic_download.sql` | 17 | `coremusic_user.sql` |
| 8 | `coremusic_logs.sql` | 18 | `coremusic_wireless.sql` |
| 9 | `coremusic_media.sql` | 19 | `media_catalog.sql` (18'lik sayıma **dahil değil** — PLANNED) |
| 10 | `coremusic_musics.sql` | 20 | `novasearch.sql` (18'lik sayıma **dahil değil** — knex ile, ADR-040 takibi) |

*Kaynak notu:* 18 + 2 ayrımı `.ai/CLAUDE.md` §18 "Ek sistemler" tablosuyla birebir uyumludur.

### 4.3 Veri erişim / cache / migration envanteri (repo dosyaları)

| Dosya | Ne yapar (ad/rol) | Durum | Kanıt |
|---|---|---|---|
| `shared/src/Database/DatabaseManager.php` | PDO bağlantı/yönetim | IMPLEMENTED | dosya yolu (2026-10-08) |
| `shared/src/Database/DatabaseRegistry.php` | veritabanı kaydı | IMPLEMENTED | dosya yolu |
| `shared/src/Database/Config/DatabaseConfig.php` | yapılandırma | IMPLEMENTED | `ls shared/src/Database/Config` |
| `shared/src/Cache/CacheManager.php` | cache zinciri | IMPLEMENTED | dosya yolu · §12 |
| `shared/src/Cache/ApcuAdapter.php` | APCu adaptörü | IMPLEMENTED | dosya yolu |
| `shared/src/Cache/MemoryAdapter.php` | bellek içi adaptör (test/fallback) | IMPLEMENTED | dosya yolu |
| `shared/src/Cache/PageCacheAdapter.php` + `PageCacheInterface.php` | sayfa cache | IMPLEMENTED | dosya yolu |
| `shared/src/Cache/CacheInterface.php` | cache sözleşmesi | IMPLEMENTED | dosya yolu |
| `shared/src/Repository/ApiKeyRepository.php` | repository örneği | IMPLEMENTED | dosya yolu |
| `auth.coremusic.net/include/Repository/UserRepository.php` | kullanıcı repository | IMPLEMENTED | dosya yolu |
| `home.coremusic.net/include/Repository/MusicRepository.php` | müzik repository | IMPLEMENTED | dosya yolu |
| `shared/src/OAuth/OAuthRepository.php` | OAuth repository (PDO) | IMPLEMENTED | dosya yolu + PDO eşleşmesi |
| `media.coremusic.net/src/Media/CatalogWriter.php` | katalog yazıcısı (PDO) | IMPLEMENTED | dosya yolu + PDO eşleşmesi |
| `shared/database/migrations/*.php` | migration dosyaları (3: drop api_keys · oauth_connections · oauth_states) | IMPLEMENTED | `git ls-files shared/database/migrations/` |
| `bin/api-key-create.php` | yardımcı betik (PDO) | IMPLEMENTED | dosya yolu |
| Redis adaptörü | — | **YOK** | `ls shared/src/Cache/` içinde `*Redis*` yok → PLANNED (§12) |

### 4.4 EK A K005 bloğunun birebir alıntısı (kaynak metin)

```text
### K005 - DATA «FENER» Bant: K000-K020
- Sorumluluk: MySQL · BCNF Schema · Transactions · Indexes · Redis · APCu · SQLite · Restic Backup
- Sınır: data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=K000-K004 (alt katmanlar) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault
```

*Kaynak:* `.ai/architecture/00-kspace-anayasa.md` satır 94-97 (In-Place · H4/R7).

### 4.5 EK A Sınır satırının madde madde açılımı

| Sınır maddesi | Değer | Bu karttaki karşılığı | Açıldığı bölüm |
|---|---|---|---|
| `data` | çekirdek veri sınırı | **veri sahibi** — 20 şema, paylaşılan tablo yok | §5.3 |
| `security` | ORTA | A05 → prepared statement · ADR-001/002 (ORM/`SELECT *` yasak) | §5.4 |
| `failure` | fail-over | yeniden deneme · Restic restore · cache devre dışı | §5.5 |
| `izinli` | K000-K004 | kök + donanım + sürücü + ses motoru + AI | §5.1 |
| `yasak` | üst katmana doğrudan erişim (yalnız port/adapter) | K006-K020 + H20 + H19 | §5.2 |
| `Kanıt` (EK A) | kaynak prompt K0–K20 bloğu · .ai/ vault | web ayağı EK B #16 (restic) | §7 |

### 4.6 Veri alan kuralları (F1 §8.8 D01-D06 — bu kartın bağlayıcı kuralları)

| Kural | Metin | Durum | Kanıt |
|---|---|---|---|
| D01 | BCNF hedefi: her aday anahtar belirleyici, geri kalanı tam bağımlı | BAĞLAYICI | §5 K5 guardrail · ADR-003 · ADR-040 (FD tabanlı BCNF denetimi) |
| D02 | İlişki tasarımı: gereksiz JOIN yerine normalize şema + ölçülü denorm | DESIGN | F1 §8.8 |
| D03 | İndeks: sorgu EXPLAIN ile ölçülür; görünürlük ≥ selectivity | DESIGN | F1 §8.8 |
| D04 | Migration: her şema değişikliği dosyalı + geri alınabilir | IMPLEMENTED (dosyalı) | `shared/database/migrations/` (3 dosya) · "geri alınabilir" → `⚠️` (geri alma kodu okunmadı) |
| D05 | Backup: restic — periyodik + doğrulama restore | TARGET | EK B #16 · F1 §8.8 |
| D06 | Cache: Redis (paylaşılan) / APCu (yerel); invalidation olayla | KISMİ | APCu IMPLEMENTED · Redis PLANNED (§12) · invalidation `⚠️` |

### 4.7 Yasaklı sorgu/teknoloji envanteri (`.ai/CLAUDE.md` §21 · ADR-001/002)

| Yasaklı | Doğru | Kaynak |
|---|---|---|
| ORM (Eloquent, Doctrine, Propel, RedBean) | Raw PDO | §21 · ADR-002 · F1 H02 |
| `SELECT *` | açık sütun listesi | §21 · F1 H03 |
| Doğrulanmamış `$_GET`/`$_POST`/`$_REQUEST` | doğrulanmış giriş | F1 H06 · §21 |
| Hardcoded credential / DSN parolası | `.env` / credential vault | F1 H04 · §21 · ADR-034 |
| MD5/SHA-1 hash | Argon2id (K006 sahası) | F1 H05 |

---

## §5 Bağımlılık & Sınır

### 5.1 İZİNLİ bağımlılıklar (EK A: izinli = K000-K004 · R6.1)

| Hedef | Tür | Aralık / not |
|---|---|---|
| K000 | alt katman | dosya sistemi/süreç (yedek dosyası, socket) |
| K001 | alt katman | donanım arayüzü (port/adapter) |
| K002 | alt katman | sürücü |
| K003 | alt katman | ses motoru (port/adapter) |
| K004 | alt katman | AI — **yalnız** K004'ün port'u üzerinden veri okur (§5 K4 guardrail) |
| Üstten gelen istek | gelen | K006-K020 port/adapter ile iner |

### 5.2 YASAK bağımlılıklar (R6.1 · F1 H19/H20)

| Yasak | Kaynak | Sonuç |
|---|---|---|
| K006-K020'ye doğrudan erişim | EK A satır 96 | Layer Violation → revert + log CRITICAL |
| Geri çağrı (H20) | F1 §5.1 H20 | yalnız port/adapter |
| Katmanlar arası doğrudan tablo paylaşımı (H19) | F1 §5.1 H19 · YARGI 2 | "Bir önceki katmanla doğrudan DB paylaşımı" |
| `SELECT *` · ORM | ADR-001/002 · §21 | Frozen ADR — ihlal reddedilir |
| K005'in üst katman iş kuralına karışması | M02 (Handler → Service → Repository) | katman sırası bağlayıcı |
| K006 bypass'ı | §5 K6 | asla |

**Olay (event) yukarı serbest:** veri değişikliği/inceleme olayları
event ile yukarı yayılabilir (R6.2 · ADR-086 olay yönlendirmeli mimari);
senkron çağrı yukarı yasaktır.

### 5.3 DATA_BOUNDARY (YARGI 2 — bu kart merkezdir)

| Konu | Kural |
|---|---|
| Sınır adı | çekirdek veri sınırı (EK A satır 96) — **veri sahibi K005** |
| Sahip olduğu | 20 şema dosyası (§4.2) · cache katmanı · yedek deposu |
| Paylaşılan tablo | **YOK** — başka katmanla aynı veri sınırı paylaşılırsa YARGI ihlali (R4.4d) |
| Erişim biçimi | yalnız port/adapter + prepared statement |
| Ek sistemler | `media_catalog` (9 tablo, `.sql` var, canlıya deploy edilmedi → PLANNED) · `novasearch` (7 tablo, canlıda var, knex ile — 18'lik sayımda yok, ADR-040 takibi) — `.ai/CLAUDE.md` §18 |
| Kalıcılık | tek kalıcı katman |

### 5.4 SECURITY_BOUNDARY (EK A: security=ORTA)

| Konu | Değer |
|---|---|
| Seviye | ORTA (EK A satır 96) |
| OWASP eşlemesi | A05:2025 Injection → `K7 Middleware · K5 Data (PDO)` (F1 §9.1 — K005 açıkça eşlenir) |
| Bağlayıcı kurallar | M06 (PDO prepared statement — her sorguda) · SEC08 (LIKE/ORDER BY dinamikleri allowlist) |
| Yasaklılar | `SELECT *` · ORM · hardcoded DSN parolası (§21 · H03/H02/H04) |
| Credential | `coremusic_auth` şeması içindeki credential vault → ADR-034 kapsamı; **kripto kararı K006**'dadır |
| DB sertleştirme | ADR-022 (database hardened security) |
| Devre dışı | K006 bypass'ı yok |

### 5.5 FAILURE_MODE (EK A: fail-over)

| Senaryo | Davranış | Kaynak | Durum |
|---|---|---|---|
| Bağlantı/servis hatası | yeniden deneme + hata kodu | EK A fail-over | DESIGN |
| Veri kaybı / disk | Restic restore (doğrulamalı) | EK B #16 · F1 §8.8 D05 | TARGET |
| Cache hatası | cache'i devre dışı bırakma, yanıt üretilmeye devam eder | tasarım | DESIGN |
| Cache stampede | mutex ile tek-load (`.ai/CLAUDE.md` §22) | §22 Edge Cases | BAĞLAYICI |
| Network outage | Offline-First + SQLite queue | §22 Edge Cases | BAĞLAYICI |
| fail-open | YOK — yalnız ADR ile (F1 SEC14) | — | — |

### 5.6 Observability / Test sınırı

Log/metrik üretim kuralı K012'dedir (`coremusic_logs` şemasının sahibi K005'tir,
üretim kuralı K012) — R7.1 ikili ayrım: **şema = K005, kural = K012**. Test
stratejisi K013 CI/CD + §17 (PHPUnit ≥80%).

### 5.7 Port/Adapter deseni (F1 §8.2 — K005'e uygulaması)

| Port tipi | Yön | Örnek | Adapter | Durum |
|---|---|---|---|---|
| Inbound | K004/K006… → K005 | repository portu (okuma/yazma) | PDO adapter | IMPLEMENTED (Repository örnekleri §4.3) |
| Inbound | K000 → K005 | dosya sistemi (yedek) | dosya adapter'ı | DESIGN |
| Outbound | K005 → K000 | yedek dosyası yazma | dosya adapter'ı | DESIGN |
| Kural | — | adapter (PDO) değişirse domain/iş kuralı değişmez (F1 §8.2) | — | bağlayıcı |
| Kural | — | repository yalnız SQL bilir; DTO/doman nesnesi dışarı çıkar (F1 §8.7 B03) | — | bağlayıcı |

### 5.8 Sınır ihlali denetim ve yaptırım akışı (R6.5/R6.6 · anayasa §5.1)

| Adım | Aksiyon | Kaynak |
|---|---|---|
| 1 | İhlal tespiti (ör. K010 → K005 doğrudan PDO; ya da iki katmanın aynı tabloyu kullanması) | R6.1 · §5.2 · R4.4d |
| 2 | Derhal revert | `.ai/CLAUDE.md` §5.1 |
| 3 | `log.md` CRITICAL girişi | anayasa §5.1 |
| 4 | 👤 bilgi | R6.5 |
| 5 | Döngü → `dep-check` exit 1 → ADR | R6.6 |
| 6 | Kardeş ilişki `refers-to` (düz metin K-ID) | R6.4 · §8.1 |

---

## §6 Girdi-Çıktı · Runtime · Observability-Test

### 6.1 Girdi

| Girdi | Kaynak | Biçim | Durum |
|---|---|---|---|
| SQL sorgusu | K008 (Repository) | prepared statement | IMPLEMENTED (PDO yolu) |
| Migration dosyası | K013 / Data Engineer | PHP/SQL dosyası | IMPLEMENTED (3 dosya) |
| Cache anahtarı | K006-K011 (cache kullanıcısı) | anahtar/değer | IMPLEMENTED (APCu) |
| Yedekleme tetiği | K013 (zamanlayıcı) | zamanlayıcı olayı | TARGET (Restic) |
| Port/adapter isteği | üst katmanlar | sözleşme | IMPLEMENTED (Repository örnekleri) |

### 6.2 Çıktı

| Çıktı | Tüketen katman | Durum |
|---|---|---|
| Sorgu sonucu (DTO) | K008 (Repository → Service) | IMPLEMENTED (DTO çıkışı kuralı F1 §8.7 B03) |
| Transaction sonucu | K008 (service sınırı) | DESIGN |
| Cache hit/miss | K012 (gözlem) · K005 (iç karar) | IMPLEMENTED (APCu) · ölçüm `⚠️` |
| Yedek arşivi | K000 (dosya) · K013 | TARGET |
| Hata kodu | K008/K012 | DESIGN |

### 6.3 Runtime

| Alan | Değer |
|---|---|
| Runtime sınıfları | server · desktop · edge (EK C) |
| Motor sürümleri | MySQL 9 (birincil) · SQL Server (backup/reporting) · MongoDB (analytics) — `.ai/CLAUDE.md` §12 [TARGET] |
| Cache | APCu IMPLEMENTED · Redis PLANNED · APCu 60 req/60s rate-limit (§12 §11) |
| Uçak | SOFTWARE |
| Deployment modları | NAS Audio Server (Linux) · Home · Studio — §14 |

### 6.4 Observability

| Alan | İçerik | Durum |
|---|---|---|
| Log | `coremusic_logs` şeması (K005) · üretim kuralı K012 | sahiplik ayrımı (§5.6) |
| Metric | sorgu süresi · cache hit/miss · bağlantı havuzu · yedek sonucu | PLANNED (`⚠️`) |
| Trace | sorgu zinciri | PLANNED |
| Health | bağlantı/şema sağlık durumu | DESIGN |
| Alert | K012 üzerinden | PLANNED |

### 6.5 Test

| Test tipi | Kapsam | Kabul kriteri | Durum |
|---|---|---|---|
| BCNF denetimi | 18 DB FD tabanlı | ihlal yok (ADR-040) | PLANNED |
| Migration geri alma | her migration | geri alınabilir (D04) | PLANNED (`⚠️` kod okunmadı) |
| Restore doğrulama | Restic yedeği | başarılı restore | PLANNED (TARGET) |
| Cache davranışı | hit/miss + invalidation | tutarlı yanıt | PLANNED |
| Birim test | Repository/Cache | ≥80% (§17) | PLANNED |
| Forbidden tarama | `SELECT *` / ORM | 0 eşleşme | PLANNED |

### 6.6 F1 §8.4 YARGI'larının K005'e uygulaması

| Yargı | Kural | K005 uygulaması | Durum |
|---|---|---|---|
| YARGI 1 | yalnız alt katman tanınır | K000-K004 | GEÇERLİ |
| YARGI 2 | DATA BOUNDARY | **veri sahibi** — tablo paylaşımı yok | GEÇERLİ |
| YARGI 3 | SECURITY BOUNDARY | ORTA + A05 + prepared statement | GEÇERLİ |
| YARGI 4 | OBSERVABILITY | sorgu/cache/bağlantı/yedek [PLANNED] | TASARIM |
| YARGI 5 | FAILURE_MODE | fail-over + restore + cache-off | GEÇERLİ |
| YARGI 6 | EVENT BOUNDARY | veri olayları yukarı serbest (ADR-086) | GEÇERLİ |

### 6.7 Bilinen açık maddeler ve riskler (K005'e özgü)

| # | Açık madde | Etki | Aksiyon |
|---|---|---|---|
| 1 | Redis adaptörü yok (§12: "adapter yok") | D06 kısmi | hedef: symfony/cache + predis — ADR gerektirebilir |
| 2 | `media_catalog` canlıya deploy edilmedi | PLANNED | deploy kararı → 👤 |
| 3 | `novasearch` knex ile (ADR-014 runner dışı) | ADR-040 takibi | sahiplik kararı → 👤 |
| 4 | Migration "geri alınabilir" iddiası kodda doğrulanmadı | `⚠️` | kod incelemesi |
| 5 | Restic yedeği çalıştırma kanıtı yok | TARGET | çalıştırma kanıtı (log) |
| 6 | "156 tablo / 55 bileşen" hedef sayımları | H10 | hedef ≠ kanıt ayrı raporla |

---

## §7 Kanıt Kaynakları (R9 — 3'lü kanıt)

| # | Tür | Kaynak (tam yollar) | İçerik | Durum |
|---|---|---|---|---|
| 1 | Anayasa/defter | `.ai/architecture/00-kspace-anayasa.md` satır 94-97 | K005 kartı: Sorumluluk 8 madde · Sınır · Kanıt | GEÇERLİ |
| 2 | Anayasa | `.ai/architecture/00-kspace-anayasa.md` satır 50 (§A.0) | K005 · DATA · «FENER» · SOFTWARE | GEÇERLİ |
| 3 | Anayasa (vault) | `.ai/CLAUDE.md` §5 K5 satırı (satır 116) | MySQL 9 (18 BCNF, 156 tablo) · Redis · APCu · SQLite · Restic · guardrail | GEÇERLİ |
| 4 | Anayasa (vault) | `.ai/CLAUDE.md` §18 (18 BCNF listesi + "Ek sistemler" notu) · §12 (cache: APCu IMPLEMENTED / Redis PLANNED) · §21 · §22 | veritabanı listesi · cache durumu · yasaklılar · edge case'ler | GEÇERLİ |
| 5 | Repo (dosya yolu) | `git ls-files .ai/.sql/mysql/` = **20** (2026-10-08) | 18 BCNF + `media_catalog` + `novasearch` | GEÇERLİ |
| 6 | Repo (dosya yolu) | `shared/src/Database/DatabaseManager.php` · `DatabaseRegistry.php` · `Config/DatabaseConfig.php` | PDO yönetim katmanı | GEÇERLİ |
| 7 | Repo (dosya yolu) | `shared/src/Cache/CacheManager.php` · `ApcuAdapter.php` · `MemoryAdapter.php` · `PageCacheAdapter.php` · `CacheInterface.php` · `PageCacheInterface.php` | cache zinciri (APCu) | GEÇERLİ |
| 8 | Repo (dosya yolu) | `shared/database/migrations/2026-10_07_drop_coremusic_api_api_keys_migration.php` · `oauth_connections_migration.php` · `oauth_states_migration.php` | dosyalı migration (3) | GEÇERLİ |
| 9 | Repo (dosya yolu) | `shared/src/Repository/ApiKeyRepository.php` · `auth.coremusic.net/include/Repository/UserRepository.php` · `home.coremusic.net/include/Repository/MusicRepository.php` · `shared/src/OAuth/OAuthRepository.php` · `media.coremusic.net/src/Media/CatalogWriter.php` | repository/PDO örnekleri | GEÇERLİ |
| 10 | Repo (dosya yolu) | `shared/tests/Repository/Sqlite/SqliteDatabaseManager.php` | SQLite yöneticisi (test yüzeyi) | GEÇERLİ |
| 11 | ADR (`ls .ai/.decisions/accepted/`) | `ADR-003-multi-db-bcnf.md` · `ADR-040-database-authority.md` · `ADR-041-database-normalization-supplementary.md` | BCNF · veritabanı otoritesi · normalizasyon ek kararları | GEÇERLİ |
| 12 | ADR (`ls`) | `ADR-014-multi-db-migration-strategy.md` · `ADR-033-sql-normalization-strategy.md` | migration stratejisi · SQL normalizasyonu | GEÇERLİ |
| 13 | ADR (`ls`) | `ADR-007-cache-namespace.md` · `ADR-027-dual-mode-storage-strategy.md` · `ADR-050-multi-db-sync-strategy.md` | cache ad alanı · çift mod depolama · çoklu DB senkron | GEÇERLİ |
| 14 | ADR (`ls`) | `ADR-022-database-hardened-security.md` · `ADR-034-credential-vault-normalization.md` · `ADR-081-multi-provider-data-sync.md` | DB sertleştirme · credential vault · çoklu sağlayıcı senkron | GEÇERLİ |
| 15 | Web (EK B #16) | `https://restic.readthedocs.io/` · `https://restic.net/` (er. 2026-10-08, güven 94) | snapshot · restore · depo parolası | GEÇERLİ |
| 16 | Spec (arşiv) | `.ai/prompts/2026-10-08-master-prompt-v2.2.0-f1.md` §8.8 (satır 704-713) · §8.7 B03/B04 | D01-D06 · repository/transaction sınırları | GEÇERLİ |
| 17 | Kural | `.ai/architecture/rules.md` R2/R3/R4/R6/R9 | isimlendirme · iskelet · format · yön · kanıt | GEÇERLİ |
| 18 | Karar | `.ai/.decisions/accepted/ADR-096-kspace-5000-boundary-model.md` §2.2 | uçak ayrımı + dizin deseni | GEÇERLİ |

**3'lü kanıt dengesi (R9.2):** repo 6 grup · ADR 11 · web 1 → **3/3 dolu → VERIFIED**.

### 7.1 Kanıt dengesi özeti (R9.2/R9.3)

| Ayağı | Durum | Sayı | Sonuç |
|---|---|---|---|
| Repo (dosya yolu + satır) | VAR | 20 şema + 6 DB dosyası + 6 cache + 3 migration + 5 repository | IMPLEMENTED (erişim/şema/cache) |
| ADR (`ls` ile görülen adlar) | VAR | 11 (+ ADR-096) | BCNF · migrasyon · cache · sync |
| Web (URL + tarih) | VAR | 1 (#16 restic) | yedek kanıtı |
| Anayasa/kural/spec | VAR | 6 kayıt | kartın taşıyıcısı |
| `kanit-tarihi` | 2026-10-08 | — | R9.3 dolu |
| `STALE` (R11) | gerekmedi | — | tümü 2026-10-08 |

### 7.2 R14 research boşluğu (K005'e özgü — EK B'ye taşınacak)

| # | Açık soru | İlgili alan | Beklenen kaynak tipi |
|---|---|---|---|
| 1 | MySQL 9 resmi sürüm notları | §6.3 | üretici (dev.mysql.com) + tarih |
| 2 | Redis/symfony-cache+predis hedefi için kaynak | §6.7 no. 1 | paket dokümantasyonu |
| 3 | APCu davranış/ölçüm kaynağı | §6.3 | resmi dokümantasyon |
| 4 | Restic periyodik+restore uygulama kanıtı | §6.5 | çalışma kaydı (log) |
| 5 | BCNF denetimi sonuç kaydı (ADR-040 §8 addendum) | §6.5 | FD tabanlı denetim çıktısı |
| 6 | `media_catalog` deploy kararı | §6.7 no. 2 | 👤 kararı |

> Cevap bulunana kadar maddeler `⚠️ VERIFICATION REQUIRED` / `PLANNED` kalır (H15).

---

## §8 İlişki & Değişiklik

### 8.1 Kardeş ve bant ilişkileri (düz metin K-ID — wiki-link YASAK)

| Yön | K-ID | İlişki |
|---|---|---|
| Alt katmanlar | K000 · K001 · K002 · K003 · K004 | tek izinli `depends-on` |
| Üst komşu (doğrudan) | K006 | güvenlik (credential şeması ilişkisi — ADR-034) |
| Band-1 kardeşler | K006 · K007 · K008 · K009 · K010 · K011 · K012 · K013 · K014 · K015 · K016 · K017 · K018 · K019 · K020 | `refers-to` (R6.4) — `depends-on` DEĞİL |
| Veri tüketicileri (port ile) | K004 · K007 · K008 · K012 · K015 | erişim yalnız port/adapter |
| Yedek iş birliği | K013 (zamanlayıcı) · K012 (gözlem) | olay ile |

### 8.2 İzinli wiki-linkler (yalnız mevcut kontrol-plane dosyaları)

- [[architecture/00-kspace-anayasa]] — EK A (K005 kartı kaynağı)
- [[architecture/rules]] — R2/R3/R4/R6/R9
- [[architecture/00-master-index]] — giriş navigasyonu

>Kardeş K-ID'lere wiki-link **yasaktır** (hedefler henüz yok; link-check ihlali).

### 8.3 Geçmiş

| Tarih | Değişiklik | Yapan |
|---|---|---|
| 2026-10-08 | İlk üretim — band-1 K005 `index.md` (staging) | Vault Steward (üretim ajanı) |

### 8.4 Dosya yerleşim planı (ADR-096 §2.2 dizin deseni · R3 iskelet)

| Dosya | Rol | Zorunluluk |
|---|---|---|
| `.ai/architecture/K005-veri-yonetimi/index.md` | bu dosyanın vault'taki hedefi | zorunlu (R3.1) |
| `.ai/architecture/K005-veri-yonetimi/*.md` (derinleşme) | gerçek karmaşıklık var: 20 şema · cache · backup — ayrı dosyalar beklenir | koşullu (R3.1 · F2 §42) |
| `b1-K005-veri-yonetimi.md` (staging) | üretim kopyası — vault'a taşınmadı | bu görevin çıktısı |
| Dizin adı | `K005-veri-yonetimi` (R2.2) | değişmez (R2.4) |
| Tek dev md | yasak (R3.2) | bağlayıcı |
| min-500 | üretim dosyası ≥500 satır; şişirme yasak (R3.3 · H1) | bağlayıcı |

---

**Authority:** SSOT hedefi `.ai/architecture/K005-veri-yonetimi/index.md` — bu kopya şimdilik **staging**'dedir.
**Last Updated:** 2026-10-08
**Mode:** Red Team · Human Mode · Truth Mode
