-- ============================================================================
-- media_catalog — MEDYA ARŞİVİ (MySQL TÜRETİLMİŞ İNDEKS)
-- Dosya     : .ai/.sql/mysql/media_catalog.sql
-- Veritabanı: media_catalog
-- Görev: ADR-092 §5.2 adım 6 — "MySQL türetilmiş indeks"
-- Author: CoreMusic Data Engineer
-- Date: 2026-09-29
-- Version: 1.0.0 (forward-only — rollback yok)
--
-- SSOT (Tek Doğruluk Kaynağı) = yan JSON dosyalarıdır:
--   media.coremusic.net/config/media.schema.json  (schema: 2)
--   media.coremusic.net/config/taxonomy.json      (version: 1, 17 anahtar)
-- Bu şema yalnızca YENİDEN ÜRETİLEBİLİR bir türetilmiş indekstir
-- (bin\scan.php --rebuild). DB çökse arşiv yaşar (JSON + dosya = asıl kayıt).
--
-- Charset : utf8mb4 / utf8mb4_tr_0900_ai_ci  (Türkçe sıralama zorunlu)
-- Engine  : InnoDB
-- ORM     : YOK (ADR-002)  ·  SELECT * : YOK (ADR-002)
-- Silme   : hard delete YASAK → is_deleted soft delete zorunlu
-- BCNF    : ADR-040 — her tabloda aday anahtar ve dışarı çıkarılmış
--           fonksiyonel bağımlılıklar tablo yorumlarında belirtilmiştir.
-- Isimlendirme: idx_{tablo}_{sutun} · fk_{tablo}_{referans_tablo}
--               uk_{tablo}_{sutun} (unique) · ftx_{tablo}_{sutun} (fulltext)
--
-- ⚠️ VERIFICATION REQUIRED: MySQL bu makinede yok — DDL çalıştırılıp
--    doğrulanamadı (faz 2 ortamı).
-- ============================================================================

CREATE DATABASE IF NOT EXISTS media_catalog
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_tr_0900_ai_ci;

USE media_catalog;

-- ============================================================================
-- BÖLÜM 1 — VARLIK TABLOLARI
-- ============================================================================

-- ----------------------------------------------------------------------------
-- Table: artist
-- Neden burada: media.schema.json $defs/artist (artist.json) — sanatçı
--   kimliği tekilleştirilir. BCNF dışarısı: kimlik.sanatci_alt ve
--   muzik/tur, iliski[] gibi yeniden kullanılan sözlük değerleri JSON'da
--   satır başına tekrar ederdi → sözlük tarafı taxonomy/asset_tag'a,
--   sanatçı düzeyindeki örtük değerler varsayilan_tag JSON'a indirildi.
-- Aday anahtar: {id} (ULID) · alternatif anahtar: {slug}
-- Türetilmiş: varsayilan_tag → media.schema.json $defs/artist.varsayilan_tag
-- ----------------------------------------------------------------------------
CREATE TABLE artist (
  id             CHAR(26)       NOT NULL COMMENT 'ULID (Crockford base32, 26 karakter) — $defs/ulid',
  slug           VARCHAR(80)    NOT NULL COMMENT 'kebab-case, $defs/slug (maxLength 80)',
  sanatci        VARCHAR(200)   NOT NULL COMMENT 'kimlik.sanatci (minLength 1, maxLength 200)',
  tip            ENUM('kisi','grup','topluluk','cesitli') NOT NULL COMMENT 'kimlik.tip — SYNC: taxonomy.json#sanatci_tip',
  varsayilan_tag JSON           NOT NULL COMMENT 'varsayilan_tag {tur[], dil} — $defs/artist.varsayilan_tag',
  durum          ENUM('inbox','aktif','sakli','tekrar','arsiv') NOT NULL DEFAULT 'inbox' COMMENT 'SYNC: taxonomy.json#durum',
  eklenme        DATETIME       NOT NULL COMMENT 'eklenme (+03:00 sabit, $defs/datetime03)',
  guncelleme     DATETIME       NULL COMMENT 'guncelleme (NULL = hiç güncellenmedi)',
  is_deleted     TINYINT(1)     NOT NULL DEFAULT 0 COMMENT 'soft delete (hard delete yasak)',
  deleted_at     TIMESTAMP      NULL COMMENT 'silinme zamanı',
  PRIMARY KEY (id),
  UNIQUE KEY uk_artist_slug (slug),
  INDEX idx_artist_tip (tip),
  INDEX idx_artist_durum (durum),
  INDEX idx_artist_sanatci (sanatci)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_tr_0900_ai_ci
COMMENT='Sanatçı — media.schema.json $defs/artist (BCNF: her non-key satır id/slug''a bağımlı)';

-- ----------------------------------------------------------------------------
-- Table: album
-- Neden burada: media.schema.json $defs/album (album.json).
--   BCNF dışarısı: ust.sanatci (ad cache'i) → artist.sanatci'ya bağımlıydı
--   (sanatci_id → sanatci adı); ayrı sütun/JSON alanı olarak tutulmadı,
--   sanatçı adı JOIN ile alınır. tarih/bicim/kapsam/kredi alt nesneleri
--   tek seviyeye indirildi (alt nesne başına satır yok = 2NF/3NF ihlali yok).
-- Aday anahtar: {id} · alternatif anahtar: {slug}
-- Türetilmiş: fiziksel ← $defs/album.bicim.fiziksel, tag ← $defs/album.tag
-- ----------------------------------------------------------------------------
CREATE TABLE album (
  id             CHAR(26)       NOT NULL COMMENT 'ULID — $defs/album.id',
  slug           VARCHAR(80)    NOT NULL COMMENT 'kebab-case — $defs/slug',
  album          VARCHAR(300)   NOT NULL COMMENT 'kimlik.album (maxLength 300)',
  album_tip      ENUM('studyo','canli','derleme','ep','soundtrack','tekli') NOT NULL COMMENT 'kimlik.album_tip — SYNC: taxonomy.json#album_tip',
  sanatci_id     CHAR(26)       NULL COMMENT 'ust.sanatci_id (ulid_null) — NULL = tekli/çeşitli istisnası',
  yil            SMALLINT       NULL COMMENT 'tarih.yil (1900..2100)',
  ay             TINYINT        NULL COMMENT 'tarih.ay (1..12)',
  ulke           CHAR(2)        NULL COMMENT 'tarih.ulke ISO-3166 alpha-2',
  fiziksel       ENUM('lp','45lik','7lik','cd','kaset','dijital') NOT NULL COMMENT 'bicim.fiziksel — SYNC: taxonomy.json#fiziksel_bicim',
  sarki_sayisi   SMALLINT UNSIGNED NULL COMMENT 'kapsam.sarki_sayisi (min 0)',
  sure_sn        INT UNSIGNED   NULL COMMENT 'kapsam.sure_sn (saniye, min 1)',
  cover          VARCHAR(255)   NULL COMMENT 'gorsel.cover (maxLength 255)',
  etiket_sirketi VARCHAR(200)   NULL COMMENT 'kredi.etiket_sirketi',
  katalog_no     VARCHAR(100)   NULL COMMENT 'kredi.katalog_no',
  tag            JSON           NOT NULL COMMENT 'tag {tur[], dil} — $defs/album.tag',
  durum          ENUM('inbox','aktif','sakli','tekrar','arsiv') NOT NULL DEFAULT 'inbox' COMMENT 'SYNC: taxonomy.json#durum',
  eklenme        DATETIME       NOT NULL COMMENT 'eklenme (+03:00)',
  guncelleme     DATETIME       NULL COMMENT 'guncelleme (datetime03_null)',
  is_deleted     TINYINT(1)     NOT NULL DEFAULT 0 COMMENT 'soft delete',
  deleted_at     TIMESTAMP      NULL COMMENT 'silinme zamanı',
  PRIMARY KEY (id),
  UNIQUE KEY uk_album_slug (slug),
  INDEX idx_album_sanatci_id (sanatci_id),
  INDEX idx_album_album_tip (album_tip),
  INDEX idx_album_yil (yil),
  INDEX idx_album_durum (durum),
  CONSTRAINT fk_album_artist FOREIGN KEY (sanatci_id)
    REFERENCES artist(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_tr_0900_ai_ci
COMMENT='Albüm — media.schema.json $defs/album (FK sanatci_id → artist, cache sütunu düşüldü)';

-- ----------------------------------------------------------------------------
-- Table: koleksiyon
-- Neden burada: media.schema.json $defs/koleksiyon (koleksiyon.json).
--   Ayrı tablo çünkü kaynak_tur/sarki_sayisi/tablo düzeyi tag yalnızca
--   koleksiyona ait; asset'e kopyalansaydı `asset.koleksiyon_id → kaynak_tur`
--   gibi bir {non-key → non-key} bağımlılık BCNF'yi ihlal ederdi.
-- Aday anahtar: {id} · alternatif anahtar: {slug}
-- Türetilmiş: kaynak_tur ← $defs/koleksiyon.kaynak_tur, tag ← $defs/koleksiyon.tag
-- ----------------------------------------------------------------------------
CREATE TABLE koleksiyon (
  id             CHAR(26)       NOT NULL COMMENT 'ULID — $defs/koleksiyon.id',
  slug           VARCHAR(80)    NOT NULL COMMENT 'kebab-case — $defs/slug',
  koleksiyon     VARCHAR(300)   NOT NULL COMMENT 'koleksiyon (maxLength 300)',
  kaynak_tur     ENUM('usb','disk','indirme','diger') NOT NULL COMMENT 'SYNC: taxonomy.json#koleksiyon_kaynak',
  sarki_sayisi   INT UNSIGNED   NOT NULL DEFAULT 0 COMMENT 'sarki_sayisi (min 0)',
  tag            JSON           NOT NULL COMMENT 'tag {tur[]} — $defs/koleksiyon.tag',
  durum          ENUM('inbox','aktif','sakli','tekrar','arsiv') NOT NULL DEFAULT 'inbox' COMMENT 'SYNC: taxonomy.json#durum',
  eklenme        DATETIME       NOT NULL COMMENT 'eklenme (+03:00)',
  is_deleted     TINYINT(1)     NOT NULL DEFAULT 0 COMMENT 'soft delete',
  deleted_at     TIMESTAMP      NULL COMMENT 'silinme zamanı',
  PRIMARY KEY (id),
  UNIQUE KEY uk_koleksiyon_slug (slug),
  INDEX idx_koleksiyon_kaynak_tur (kaynak_tur),
  INDEX idx_koleksiyon_durum (durum)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_tr_0900_ai_ci
COMMENT='Koleksiyon — media.schema.json $defs/koleksiyon (bağımsız varlık, asset''ten ayrıldı)';

-- ----------------------------------------------------------------------------
-- Table: asset
-- Neden burada: media.schema.json $defs/meta_base + meta_ses/meta_video
--   (meta.json). Tek dosya seması; `tip` discriminator (ses|video).
--   BCNF dışarısı: ust.sanatci / ust.album / ust.yil adları (JSON ad cache'i)
--   → artist/album'a bağımlıydı; ayrı sütun yapılmadı, FK ile çözülür.
--   tag[]/etiket[] satır başına tekrar ediyordu → asset_tag'a indirildi
--   (etiket[] açık set olduğu için DB'ye alınmaz, SSOT JSON'da kalır).
-- Aday anahtar: {id} · alternatif: {dosya_yolu} · alternatif: {sha256}
-- FULLTEXT: ftx_asset_baslik (baslik) → v_asset_search ile birlikte arama
-- ----------------------------------------------------------------------------
CREATE TABLE asset (
  id             CHAR(26)       NOT NULL COMMENT 'ULID — $defs/meta_base.id',
  tip            ENUM('ses','video') NOT NULL COMMENT 'meta discriminator (meta_ses | meta_video)',
  slug           VARCHAR(80)    NOT NULL COMMENT 'kimlik.slug — kebab-case',
  baslik         VARCHAR(300)   NOT NULL COMMENT 'kimlik.baslik (minLength 1, maxLength 300)',
  sira           INT UNSIGNED   NOT NULL COMMENT 'kimlik.sira (min 1) — albüm içi sıralama',
  disk           TINYINT UNSIGNED NULL COMMENT 'kimlik.disk (min 1, NULL = tek disk)',
  album_id       CHAR(26)       NULL COMMENT 'ust.album_id (ulid_null)',
  sanatci_id     CHAR(26)       NULL COMMENT 'ust.sanatci_id (ulid_null) — tekli/çeşitli istisnasında tek kaynak',
  koleksiyon_id  CHAR(26)       NULL COMMENT 'koleksiyon bağı (asset koleksiyon üyeliği)',
  dosya_yolu     VARCHAR(500)   NOT NULL COMMENT 'repo-göreli dosya yolu — ingest kanıtı, UNIQUE',
  durum          ENUM('inbox','aktif','sakli','tekrar','arsiv') NOT NULL DEFAULT 'inbox' COMMENT 'SYNC: taxonomy.json#durum',
  eklenme        DATETIME       NOT NULL COMMENT 'eklenme (+03:00)',
  guncelleme     DATETIME       NULL COMMENT 'guncelleme (datetime03_null)',
  ekleyen        VARCHAR(60)    NULL COMMENT 'ekleyen (maxLength 60)',
  -- teknik (media.schema.json $defs/meta_base.teknik)
  dosya          VARCHAR(255)   NOT NULL COMMENT 'teknik.dosya — ses ^audio.(mp3|flac|wav|m4a|ogg|wma)$ / video ^video.(mp4|mkv|avi|webm)$',
  format         VARCHAR(16)    NOT NULL COMMENT 'teknik.format — ^[a-z0-9]{1,16}$',
  kapsayici      VARCHAR(16)    NULL COMMENT 'teknik.kapsayici — yalniz video (meta_video z-alani)',
  boyut_bayt     BIGINT UNSIGNED NOT NULL COMMENT 'teknik.boyut_bayt (min 1)',
  sha256         CHAR(64)       NOT NULL COMMENT 'teknik.sha256 — kucuk harf hex, UNIQUE',
  sure_sn        INT UNSIGNED   NULL COMMENT 'teknik.sure_sn (min 1)',
  bitrate        INT UNSIGNED   NULL COMMENT 'teknik.bitrate (bps, min 1)',
  kanal          TINYINT UNSIGNED NULL COMMENT 'teknik.kanal (min 1)',
  orneklem       INT UNSIGNED   NULL COMMENT 'teknik.orneklem (Hz, min 1)',
  derinlik       TINYINT UNSIGNED NULL COMMENT 'teknik.derinlik (bit, min 1)',
  lufs           DECIMAL(5,2)   NULL COMMENT 'teknik.lufs (-70.00 .. 0.00)',
  peak_db        DECIMAL(6,2)   NULL COMMENT 'teknik.peak_db',
  cozunurluk     VARCHAR(11)    NULL COMMENT 'teknik.cozunurluk — ^\\d{2,5}x\\d{2,5}$ (yalniz video)',
  fps            DECIMAL(7,3)   NULL COMMENT 'teknik.fps (yalniz video)',
  video_kodec    VARCHAR(40)    NULL COMMENT 'teknik.video_kodec (yalniz video)',
  ses_kodec      VARCHAR(40)    NULL COMMENT 'teknik.ses_kodec (yalniz video)',
  bitrate_kbps   INT UNSIGNED   NULL COMMENT 'teknik.bitrate_kbps (yalniz video)',
  -- müzikal (media.schema.json $defs/meta_base.muzikal)
  bpm            SMALLINT UNSIGNED NULL COMMENT 'muzikal.bpm (20..300)',
  ton            VARCHAR(20)    NULL COMMENT 'muzikal.ton (maxLength 20)',
  enerji         TINYINT UNSIGNED NULL COMMENT 'muzikal.enerji (1..10)',
  intro_sn       DECIMAL(8,3)   NULL COMMENT 'muzikal.intro_sn (min 0)',
  outro_sn       DECIMAL(8,3)   NULL COMMENT 'muzikal.outro_sn (min 0)',
  -- görsel (yalniz video — $defs/meta_base.gorsel)
  poster         VARCHAR(255)   NULL COMMENT 'gorsel.poster (yalniz video; ses kaydinda NULL)',
  poster_zaman   DECIMAL(8,3)   NULL COMMENT 'gorsel.poster_zaman (0 .. sure_sn)',
  is_deleted     TINYINT(1)     NOT NULL DEFAULT 0 COMMENT 'soft delete',
  deleted_at     TIMESTAMP      NULL COMMENT 'silinme zamanı',
  PRIMARY KEY (id),
  UNIQUE KEY uk_asset_dosya_yolu (dosya_yolu),
  UNIQUE KEY uk_asset_sha256 (sha256),
  INDEX idx_asset_album_id (album_id),
  INDEX idx_asset_sanatci_id (sanatci_id),
  INDEX idx_asset_koleksiyon_id (koleksiyon_id),
  INDEX idx_asset_tip (tip),
  INDEX idx_asset_durum (durum),
  INDEX idx_asset_album_sira (album_id, sira),
  FULLTEXT INDEX ftx_asset_baslik (baslik),
  CONSTRAINT fk_asset_album FOREIGN KEY (album_id)
    REFERENCES album(id) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_asset_artist FOREIGN KEY (sanatci_id)
    REFERENCES artist(id) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_asset_koleksiyon FOREIGN KEY (koleksiyon_id)
    REFERENCES koleksiyon(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_tr_0900_ai_ci
COMMENT='Medya varlığı (ses|video) — $defs/meta_base; tekil dosya yolu + sha256 ile yinelenme imkansız';

-- ----------------------------------------------------------------------------
-- Table: variant
-- Neden burada: media.schema.json $defs/meta_base.teknik.varyantlar[] —
--   aynı eserin ikinci/üçüncü kodlamaları (flac + mp3 vb.).
--   BCNF dışarısı: varyant dosyasının adı/şifresi asset'e bağımlı değil;
--   (asset_id → dosya, format, boyut_bayt, sha256) değil, tamamı
--   {asset_id, dosya} ile belirlenir → tek tabloda tek başına durur.
-- Aday anahtar: {asset_id, dosya}
-- ----------------------------------------------------------------------------
CREATE TABLE variant (
  asset_id       CHAR(26)       NOT NULL COMMENT 'Ana varlık (FK) — $defs/meta_base.id',
  dosya          VARCHAR(255)   NOT NULL COMMENT 'varyant dosya yolu (maxLength 255)',
  format         VARCHAR(16)    NOT NULL COMMENT 'varyant format — ^[a-z0-9]{1,16}$',
  boyut_bayt     BIGINT UNSIGNED NOT NULL COMMENT 'varyant boyutu (min 1)',
  sha256         CHAR(64)       NOT NULL COMMENT 'varyant sha256 — hex, UNIQUE (yinelenen kodlama imkansız)',
  PRIMARY KEY (asset_id, dosya),
  UNIQUE KEY uk_variant_sha256 (sha256),
  -- FK asset_id → PK(asset_id, dosya) ilk sütunu ile zaten indeksli
  CONSTRAINT fk_variant_asset FOREIGN KEY (asset_id)
    REFERENCES asset(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_tr_0900_ai_ci
COMMENT='Varyant kodlamalar (teknik.varyantlar[]) — PK (asset_id, dosya) BCNF aday anahtarı';

-- ============================================================================
-- BÖLÜM 2 — KAPALI SÖZLÜK (TAXONOMY) VE TAG İLİŞKİLERİ
-- ============================================================================

-- ----------------------------------------------------------------------------
-- Table: taxonomy
-- Neden burada: taxonomy.json (version 1) — 17 anahtar, kapalı (closed) set.
--   JSON'daki her enum ayrı ENUM sütununa taşınsa 17 sözlük birden fazla
--   yerde tekrar ederdi (JSON ↔ DDL senkron kaybı riski). Tek sözlük +
--   asset_tag FK'si ile "drift" DB seviyesinde de imkânsız hale gelir.
-- Aday anahtar: {alan, deger}
-- seed: taxonomy.json definitions → aşağıdaki INSERT'ler (değerler dosyadan
--       okunmuştur, uydurulmamıştır). Toplam 17 anahtar / 98 değer.
-- ----------------------------------------------------------------------------
CREATE TABLE taxonomy (
  alan           VARCHAR(60)    NOT NULL COMMENT 'taxonomy.json anahtarı (17 tanım: tur, kullanim, ...)',
  deger          VARCHAR(60)    NOT NULL COMMENT 'o anahtarın değeri (kapalı set — yeni değer = taxonomy.json''a 1 satır)',
  sira           INT UNSIGNED   NOT NULL DEFAULT 0 COMMENT 'taxonomy.json diziliş sırası (1..N)',
  aktif          TINYINT(1)     NOT NULL DEFAULT 1 COMMENT 'sözlükte aktif mi?',
  PRIMARY KEY (alan, deger),
  INDEX idx_taxonomy_sira (alan, sira),
  INDEX idx_taxonomy_aktif (aktif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_tr_0900_ai_ci
COMMENT='Kapalı sözlük — taxonomy.json''ın 1:1 türetilmiş indeksi (17 anahtar)';

-- --- taxonomy SEED (dosyadan okundu — 17 anahtar, 98 satır) -----------------
-- taxonomy.json#tur → 14 deger
INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  ('tur','arabesk',1,1), ('tur','damar',2,1), ('tur','turk-pop',3,1), ('tur','turkce',4,1),
  ('tur','yabanci',5,1), ('tur','nostalji',6,1), ('tur','oryantel',7,1), ('tur','halay',8,1),
  ('tur','roman-sorti',9,1), ('tur','dans',10,1), ('tur','klasik',11,1), ('tur','slow',12,1),
  ('tur','enstrumantal',13,1), ('tur','turk-rock',14,1);

-- taxonomy.json#kullanim → 8 deger
INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  ('kullanim','dugun',1,1), ('kullanim','hane',2,1), ('kullanim','org-esligi',3,1),
  ('kullanim','alt-yapi',4,1), ('kullanim','kina',5,1), ('kullanim','pasta-nikah',6,1),
  ('kullanim','ara-show',7,1), ('kullanim','dj-set',8,1);

-- taxonomy.json#media_tipi → 6 deger
INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  ('media_tipi','full',1,1), ('media_tipi','enstrumantal',2,1), ('media_tipi','karaoke',3,1),
  ('media_tipi','canli',4,1), ('media_tipi','remix',5,1), ('media_tipi','cover',6,1);

-- taxonomy.json#donem → 7 deger
INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  ('donem','on-yil',1,1), ('donem','70ler',2,1), ('donem','80ler',3,1), ('donem','90ler',4,1),
  ('donem','2000ler',5,1), ('donem','2010lar',6,1), ('donem','2020lar',7,1);

-- taxonomy.json#ruh → 5 deger
INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  ('ruh','duygusal',1,1), ('ruh','yavas',2,1), ('ruh','enerjik',3,1),
  ('ruh','nostaljik',4,1), ('ruh','huzunlu',5,1);

-- taxonomy.json#klip_tipi → 5 deger
INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  ('klip_tipi','official',1,1), ('klip_tipi','live',2,1), ('klip_tipi','lyric',3,1),
  ('klip_tipi','dance',4,1), ('klip_tipi','acoustic',5,1);

-- taxonomy.json#dil → 12 deger
INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  ('dil','tr',1,1), ('dil','en',2,1), ('dil','de',3,1), ('dil','fr',4,1),
  ('dil','it',5,1), ('dil','es',6,1), ('dil','ar',7,1), ('dil','fa',8,1),
  ('dil','ru',9,1), ('dil','az',10,1), ('dil','ku',11,1), ('dil','sq',12,1);

-- taxonomy.json#durum → 5 deger
INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  ('durum','inbox',1,1), ('durum','aktif',2,1), ('durum','sakli',3,1),
  ('durum','tekrar',4,1), ('durum','arsiv',5,1);

-- taxonomy.json#album_tip → 6 deger
INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  ('album_tip','studyo',1,1), ('album_tip','canli',2,1), ('album_tip','derleme',3,1),
  ('album_tip','ep',4,1), ('album_tip','soundtrack',5,1), ('album_tip','tekli',6,1);

-- taxonomy.json#fiziksel_bicim → 6 deger
INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  ('fiziksel_bicim','lp',1,1), ('fiziksel_bicim','45lik',2,1), ('fiziksel_bicim','7lik',3,1),
  ('fiziksel_bicim','cd',4,1), ('fiziksel_bicim','kaset',5,1), ('fiziksel_bicim','dijital',6,1);

-- taxonomy.json#sanatci_tip → 4 deger
INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  ('sanatci_tip','kisi',1,1), ('sanatci_tip','grup',2,1),
  ('sanatci_tip','topluluk',3,1), ('sanatci_tip','cesitli',4,1);

-- taxonomy.json#video_kaynak → 4 deger
INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  ('video_kaynak','yt',1,1), ('video_kaynak','vhs',2,1),
  ('video_kaynak','dvd',3,1), ('video_kaynak','cekim',4,1);

-- taxonomy.json#koleksiyon_kaynak → 4 deger
INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  ('koleksiyon_kaynak','usb',1,1), ('koleksiyon_kaynak','disk',2,1),
  ('koleksiyon_kaynak','indirme',3,1), ('koleksiyon_kaynak','diger',4,1);

-- taxonomy.json#altyazi_tip → 2 deger
INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  ('altyazi_tip','yan',1,1), ('altyazi_tip','govde',2,1);

-- taxonomy.json#cover_kaynak → 3 deger
INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  ('cover_kaynak','kaynak',1,1), ('cover_kaynak','uretilmis',2,1), ('cover_kaynak','dis',3,1);

-- taxonomy.json#koleksiyon_hak → 3 deger
INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  ('koleksiyon_hak','kisisel',1,1), ('koleksiyon_hak','ticari',2,1), ('koleksiyon_hak','bilinmiyor',3,1);

-- taxonomy.json#bitrate_tip → 4 deger
INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  ('bitrate_tip','yok',1,1), ('bitrate_tip','cbr',2,1),
  ('bitrate_tip','vbr',3,1), ('bitrate_tip','abr',4,1);

-- ----------------------------------------------------------------------------
-- Table: asset_tag
-- Neden burada: media.schema.json $defs/meta_base.tag {tur[], kullanim[],
--   donem, ruh[], dil, media_tipi, klip_tipi} — satır başına dizi tekrarı
--   1NF/BCNF ihlaliydi; (asset_id, alan, deger) ile tekilleşir.
--   BCNF dışarısı: deger → sira/aktif bağımlılığı taxonomy'a taşındı (FK).
--   etiket[] AÇIK set (taxonomy dışı) → bilerek bu tabloya alınmaz,
--   yoksa (alan,deger) FK ihlali oluşurdu; yeri SSOT JSON'dur.
-- Aday anahtar: {asset_id, alan, deger}
-- FK (alan, deger) → taxonomy(alan, deger) ⇒ drift DB seviyesinde imkânsız
-- ----------------------------------------------------------------------------
CREATE TABLE asset_tag (
  asset_id       CHAR(26)       NOT NULL COMMENT 'Varlık (FK)',
  alan           VARCHAR(60)    NOT NULL COMMENT 'tag anahtarı (taxonomy.alan)',
  deger          VARCHAR(60)    NOT NULL COMMENT 'tag değeri (taxonomy.deger) — kapalı set',
  PRIMARY KEY (asset_id, alan, deger),
  INDEX idx_asset_tag_alan_deger (alan, deger),
  -- FK asset_id → PK(asset_id, ...) ilk sütunu ile indeksli
  CONSTRAINT fk_asset_tag_asset FOREIGN KEY (asset_id)
    REFERENCES asset(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_asset_tag_taxonomy FOREIGN KEY (alan, deger)
    REFERENCES taxonomy(alan, deger) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_tr_0900_ai_ci
COMMENT='Asset ↔ kapalı sözlük bağı — taxonomy FK sayesinde enum drift DB seviyesinde imkansiz';

-- ============================================================================
-- BÖLÜM 3 — İZ (AUDIT) TABLOLARI
-- ============================================================================

-- ----------------------------------------------------------------------------
-- Table: path_history
-- Neden burada: ingest/repair sırasında dosya yeniden adlandırmasının izi
--   (rename audit). BCNF dışarısı: eski_yol/yeni_yol asset'e bağımlı değil
--   — asset adı değişse de eski yol geçmişi değişmez; ayrı satır olarak
--   tutulur (yinelenen yol geçmişi yok).
-- Aday anahtar: {id} (sayaç) — append-only, güncellenmez
-- ----------------------------------------------------------------------------
CREATE TABLE path_history (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Monotonic sayaç (iz satırı, ULID değil)',
  asset_id       CHAR(26)       NOT NULL COMMENT 'İzlenen varlık (FK)',
  eski_yol       VARCHAR(500)   NOT NULL COMMENT 'Yeniden adlandırma öncesi repo-göreli yol',
  yeni_yol       VARCHAR(500)   NOT NULL COMMENT 'Yeniden adlandırma sonrası repo-göreli yol',
  degistiren     VARCHAR(60)    NULL COMMENT 'Değiştiren (agent/kullanıcı)',
  zaman          DATETIME       NOT NULL COMMENT 'Değişiklik zamanı (+03:00)',
  PRIMARY KEY (id),
  INDEX idx_path_history_asset_id (asset_id),
  INDEX idx_path_history_zaman (zaman),
  CONSTRAINT fk_path_history_asset FOREIGN KEY (asset_id)
    REFERENCES asset(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_tr_0900_ai_ci
COMMENT='Dosya yolu değişiklik izi (rename audit) — append-only';

-- ----------------------------------------------------------------------------
-- Table: ingest_batch
-- Neden burada: arşiv taramasının parti kaydı. mod (dry-run|commit) ve
--   rapor_yolu ADR-092 §5.2 yıkım/taşıma adımlarının kanıtıdır.
--   BCNF dışarısı: rapor_yolu partinin kendisine bağımlı, asset'e değil —
--   ayrı tablo tutulur (batch başına tek satır, tekrar yok).
-- Aday anahtar: {id} (sayaç)
-- ----------------------------------------------------------------------------
CREATE TABLE ingest_batch (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Parti kimliği (sayaç)',
  kaynak         VARCHAR(500)   NOT NULL COMMENT 'Tarama kaynağı (repo-göreli dizin/yol)',
  mod            ENUM('dry-run','commit') NOT NULL COMMENT 'dry-run = yalnız rapor, commit = yaz',
  dosya_sayisi   INT UNSIGNED   NOT NULL DEFAULT 0 COMMENT 'İşlenen dosya sayısı',
  byte           BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'İşlenen toplam bayt',
  baslangic      DATETIME       NOT NULL COMMENT 'Parti başlangıcı (+03:00)',
  bitis          DATETIME       NULL COMMENT 'Parti bitişi (NULL = sürüyor)',
  durum          ENUM('planlandi','calisiyor','basarili','hatali') NOT NULL DEFAULT 'planlandi' COMMENT 'Parti durumu',
  rapor_yolu     VARCHAR(500)   NULL COMMENT 'Üretilen rapor dosyası yolu (repo-göreli)',
  PRIMARY KEY (id),
  INDEX idx_ingest_batch_durum (durum),
  INDEX idx_ingest_batch_baslangic (baslangic),
  INDEX idx_ingest_batch_mod (mod)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_tr_0900_ai_ci
COMMENT='Ingest/tarama parti kaydi — ADR-092 §5.2 adımlarının denetlenebilir izi';

-- ============================================================================
-- BÖLÜM 4 — ARAMA GÖRÜNÜMÜ
-- ============================================================================

-- ----------------------------------------------------------------------------
-- View: v_asset_search
-- Neden burada: asset ⋈ album ⋈ artist tekilleştirmesi arama için gereklidir;
--   uygulama katmanında 3 ayrı sorgu yerine tek görünüm. Sütunlar ADR-002
--   gereği açıkça listelenir (SELECT * yok). donem/tur gibi kapalı sözlük
--   alanları JSON'dan okunur; etiket[] (açık set) bilerek dahil edilmez.
-- ----------------------------------------------------------------------------
CREATE OR REPLACE VIEW v_asset_search AS
SELECT
  a.id                    AS asset_id,
  a.slug                  AS asset_slug,
  a.baslik,
  a.tip                   AS asset_tip,
  a.sira,
  a.disk,
  a.durum                 AS asset_durum,
  a.dosya_yolu,
  a.sure_sn,
  a.sha256,
  al.id                   AS album_id,
  al.slug                 AS album_slug,
  al.album,
  al.album_tip,
  al.yil                  AS album_yil,
  al.fiziksel             AS album_fiziksel,
  ar.id                   AS sanatci_id,
  ar.slug                 AS sanatci_slug,
  ar.sanatci,
  ar.tip                  AS sanatci_tip,
  COALESCE(JSON_EXTRACT(al.tag, '$.tur'),
           JSON_EXTRACT(ar.varsayilan_tag, '$.tur')) AS tur
FROM asset a
LEFT JOIN album  al ON al.id = a.album_id      AND al.is_deleted = 0
LEFT JOIN artist ar ON ar.id = COALESCE(a.sanatci_id, al.sanatci_id) AND ar.is_deleted = 0
WHERE a.is_deleted = 0;

-- ============================================================================
-- IG (ingest) NOTU
-- ============================================================================
-- Bu şema HER ZAMAN `bin\scan.php --rebuild` ile yeniden üretilebilir.
-- SSOT = media.coremusic.net/config/*.json olduğu için DB'ye elle yazılan
-- hiçbir satır kalıcı değildir; indeks silinse bile arşiv (JSON + dosyalar)
-- eksiksiz yaşar ve tek komutla geri kurulur. VERİ KAYBI RİSKİ YOKTUR.
--
-- Yeniden üretme:  php bin\scan.php --rebuild
-- Yalnız tarama :  php bin\scan.php --dry-run   (ingest_batch.mod='dry-run')
--
-- Beklenen seed: taxonomy 17 anahtar → 98 INSERT satırı (fark = 0).
-- ============================================================================
-- ⚠️ VERIFICATION REQUIRED: MySQL bu makinede yok — DDL çalıştırılıp
--    doğrulanamadı (faz 2 ortamı).
-- ============================================================================
