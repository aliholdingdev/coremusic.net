<?php declare(strict_types=1);

/**
 * CoreMusic — media_catalog Migration (yeni çok-DB — ADR-092 §5.2 adım 6)
 *
 * Medya arşivi MySQL türetilmiş indeksi: 9 tablo (artist, album, koleksiyon,
 * asset, variant, taxonomy, asset_tag, path_history, ingest_batch) +
 * v_asset_search görünümü + kapalı-sözlük seed'leri (taxonomy.json türevi).
 * Bu migration MEDIA_CATALOG veritabanını oluşturur (diğer DB'lere dokunmaz).
 *
 * Kaynak (SSOT): .ai/.sql/mysql/media_catalog.sql v1.0.1 (2026-10-21)
 * Forward-only — revert yasak (ADR-014 multi-DB migration stratejisi)
 * BCNF — ADR-040 · PDO/no ORM — ADR-002 · soft delete zorunlu (spec başlığı)
 *
 * @see [[decisions/accepted/ADR-092-media-dizin-ekseni-ve-ulid]]
 * @see [[decisions/accepted/ADR-014-multi-db-migration-strategy]]
 * @see [[decisions/accepted/ADR-040-database-authority]]
 */

$queries = [
    'CREATE DATABASE IF NOT EXISTS media_catalog
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_tr_0900_ai_ci;',
    'USE media_catalog;',
    'CREATE TABLE artist (
  id             CHAR(26)       NOT NULL COMMENT \'ULID (Crockford base32, 26 karakter) — $defs/ulid\',
  slug           VARCHAR(80)    NOT NULL COMMENT \'kebab-case, $defs/slug (maxLength 80)\',
  sanatci        VARCHAR(200)   NOT NULL COMMENT \'kimlik.sanatci (minLength 1, maxLength 200)\',
  tip            ENUM(\'kisi\',\'grup\',\'topluluk\',\'cesitli\') NOT NULL COMMENT \'kimlik.tip — SYNC: taxonomy.json#sanatci_tip\',
  varsayilan_tag JSON           NOT NULL COMMENT \'varsayilan_tag {tur[], dil} — $defs/artist.varsayilan_tag\',
  durum          ENUM(\'inbox\',\'aktif\',\'sakli\',\'tekrar\',\'arsiv\') NOT NULL DEFAULT \'inbox\' COMMENT \'SYNC: taxonomy.json#durum\',
  eklenme        DATETIME       NOT NULL COMMENT \'eklenme (+03:00 sabit, $defs/datetime03)\',
  guncelleme     DATETIME       NULL COMMENT \'guncelleme (NULL = hiç güncellenmedi)\',
  is_deleted     TINYINT(1)     NOT NULL DEFAULT 0 COMMENT \'soft delete (hard delete yasak)\',
  deleted_at     TIMESTAMP      NULL COMMENT \'silinme zamanı\',
  PRIMARY KEY (id),
  UNIQUE KEY uk_artist_slug (slug),
  INDEX idx_artist_tip (tip),
  INDEX idx_artist_durum (durum),
  INDEX idx_artist_sanatci (sanatci)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_tr_0900_ai_ci
COMMENT=\'Sanatçı — media.schema.json $defs/artist (BCNF: her non-key satır id/slug\'\'a bağımlı)\';',
    'CREATE TABLE album (
  id             CHAR(26)       NOT NULL COMMENT \'ULID — $defs/album.id\',
  slug           VARCHAR(80)    NOT NULL COMMENT \'kebab-case — $defs/slug\',
  album          VARCHAR(300)   NOT NULL COMMENT \'kimlik.album (maxLength 300)\',
  album_tip      ENUM(\'studyo\',\'canli\',\'derleme\',\'ep\',\'soundtrack\',\'tekli\') NOT NULL COMMENT \'kimlik.album_tip — SYNC: taxonomy.json#album_tip\',
  sanatci_id     CHAR(26)       NULL COMMENT \'ust.sanatci_id (ulid_null) — NULL = tekli/çeşitli istisnası\',
  yil            SMALLINT       NULL COMMENT \'tarih.yil (1900..2100)\',
  ay             TINYINT        NULL COMMENT \'tarih.ay (1..12)\',
  ulke           CHAR(2)        NULL COMMENT \'tarih.ulke ISO-3166 alpha-2\',
  fiziksel       ENUM(\'lp\',\'45lik\',\'7lik\',\'cd\',\'kaset\',\'dijital\') NOT NULL COMMENT \'bicim.fiziksel — SYNC: taxonomy.json#fiziksel_bicim\',
  sarki_sayisi   SMALLINT UNSIGNED NULL COMMENT \'kapsam.sarki_sayisi (min 0)\',
  sure_sn        INT UNSIGNED   NULL COMMENT \'kapsam.sure_sn (saniye, min 1)\',
  cover          VARCHAR(255)   NULL COMMENT \'gorsel.cover (maxLength 255)\',
  etiket_sirketi VARCHAR(200)   NULL COMMENT \'kredi.etiket_sirketi\',
  katalog_no     VARCHAR(100)   NULL COMMENT \'kredi.katalog_no\',
  tag            JSON           NOT NULL COMMENT \'tag {tur[], dil} — $defs/album.tag\',
  durum          ENUM(\'inbox\',\'aktif\',\'sakli\',\'tekrar\',\'arsiv\') NOT NULL DEFAULT \'inbox\' COMMENT \'SYNC: taxonomy.json#durum\',
  eklenme        DATETIME       NOT NULL COMMENT \'eklenme (+03:00)\',
  guncelleme     DATETIME       NULL COMMENT \'guncelleme (datetime03_null)\',
  is_deleted     TINYINT(1)     NOT NULL DEFAULT 0 COMMENT \'soft delete\',
  deleted_at     TIMESTAMP      NULL COMMENT \'silinme zamanı\',
  PRIMARY KEY (id),
  UNIQUE KEY uk_album_slug (slug),
  INDEX idx_album_sanatci_id (sanatci_id),
  INDEX idx_album_album_tip (album_tip),
  INDEX idx_album_yil (yil),
  INDEX idx_album_durum (durum),
  CONSTRAINT fk_album_artist FOREIGN KEY (sanatci_id)
    REFERENCES artist(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_tr_0900_ai_ci
COMMENT=\'Albüm — media.schema.json $defs/album (FK sanatci_id → artist, cache sütunu düşüldü)\';',
    'CREATE TABLE koleksiyon (
  id             CHAR(26)       NOT NULL COMMENT \'ULID — $defs/koleksiyon.id\',
  slug           VARCHAR(80)    NOT NULL COMMENT \'kebab-case — $defs/slug\',
  koleksiyon     VARCHAR(300)   NOT NULL COMMENT \'koleksiyon (maxLength 300)\',
  kaynak_tur     ENUM(\'usb\',\'disk\',\'indirme\',\'diger\') NOT NULL COMMENT \'SYNC: taxonomy.json#koleksiyon_kaynak\',
  sarki_sayisi   INT UNSIGNED   NOT NULL DEFAULT 0 COMMENT \'sarki_sayisi (min 0)\',
  tag            JSON           NOT NULL COMMENT \'tag {tur[]} — $defs/koleksiyon.tag\',
  durum          ENUM(\'inbox\',\'aktif\',\'sakli\',\'tekrar\',\'arsiv\') NOT NULL DEFAULT \'inbox\' COMMENT \'SYNC: taxonomy.json#durum\',
  eklenme        DATETIME       NOT NULL COMMENT \'eklenme (+03:00)\',
  is_deleted     TINYINT(1)     NOT NULL DEFAULT 0 COMMENT \'soft delete\',
  deleted_at     TIMESTAMP      NULL COMMENT \'silinme zamanı\',
  PRIMARY KEY (id),
  UNIQUE KEY uk_koleksiyon_slug (slug),
  INDEX idx_koleksiyon_kaynak_tur (kaynak_tur),
  INDEX idx_koleksiyon_durum (durum)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_tr_0900_ai_ci
COMMENT=\'Koleksiyon — media.schema.json $defs/koleksiyon (bağımsız varlık, asset\'\'ten ayrıldı)\';',
    'CREATE TABLE asset (
  id             CHAR(26)       NOT NULL COMMENT \'ULID — $defs/meta_base.id\',
  tip            ENUM(\'ses\',\'video\') NOT NULL COMMENT \'meta discriminator (meta_ses | meta_video)\',
  slug           VARCHAR(80)    NOT NULL COMMENT \'kimlik.slug — kebab-case\',
  baslik         VARCHAR(300)   NOT NULL COMMENT \'kimlik.baslik (minLength 1, maxLength 300)\',
  sira           INT UNSIGNED   NOT NULL COMMENT \'kimlik.sira (min 1) — albüm içi sıralama\',
  disk           TINYINT UNSIGNED NULL COMMENT \'kimlik.disk (min 1, NULL = tek disk)\',
  album_id       CHAR(26)       NULL COMMENT \'ust.album_id (ulid_null)\',
  sanatci_id     CHAR(26)       NULL COMMENT \'ust.sanatci_id (ulid_null) — tekli/çeşitli istisnasında tek kaynak\',
  koleksiyon_id  CHAR(26)       NULL COMMENT \'koleksiyon bağı (asset koleksiyon üyeliği)\',
  dosya_yolu     VARCHAR(500)   NOT NULL COMMENT \'repo-göreli dosya yolu — ingest kanıtı, UNIQUE\',
  durum          ENUM(\'inbox\',\'aktif\',\'sakli\',\'tekrar\',\'arsiv\') NOT NULL DEFAULT \'inbox\' COMMENT \'SYNC: taxonomy.json#durum\',
  eklenme        DATETIME       NOT NULL COMMENT \'eklenme (+03:00)\',
  guncelleme     DATETIME       NULL COMMENT \'guncelleme (datetime03_null)\',
  ekleyen        VARCHAR(60)    NULL COMMENT \'ekleyen (maxLength 60)\',
  dosya          VARCHAR(255)   NOT NULL COMMENT \'teknik.dosya — ses ^audio.(mp3|flac|wav|m4a|ogg|wma)$ / video ^video.(mp4|mkv|avi|webm)$\',
  format         VARCHAR(16)    NOT NULL COMMENT \'teknik.format — ^[a-z0-9]{1,16}$\',
  kapsayici      VARCHAR(16)    NULL COMMENT \'teknik.kapsayici — yalniz video (meta_video z-alani)\',
  boyut_bayt     BIGINT UNSIGNED NOT NULL COMMENT \'teknik.boyut_bayt (min 1)\',
  sha256         CHAR(64)       NOT NULL COMMENT \'teknik.sha256 — kucuk harf hex, UNIQUE\',
  sure_sn        INT UNSIGNED   NULL COMMENT \'teknik.sure_sn (min 1)\',
  bitrate        INT UNSIGNED   NULL COMMENT \'teknik.bitrate (bps, min 1)\',
  kanal          TINYINT UNSIGNED NULL COMMENT \'teknik.kanal (min 1)\',
  orneklem       INT UNSIGNED   NULL COMMENT \'teknik.orneklem (Hz, min 1)\',
  derinlik       TINYINT UNSIGNED NULL COMMENT \'teknik.derinlik (bit, min 1)\',
  lufs           DECIMAL(5,2)   NULL COMMENT \'teknik.lufs (-70.00 .. 0.00)\',
  peak_db        DECIMAL(6,2)   NULL COMMENT \'teknik.peak_db\',
  cozunurluk     VARCHAR(11)    NULL COMMENT \'teknik.cozunurluk — ^\\\\d{2,5}x\\\\d{2,5}$ (yalniz video)\',
  fps            DECIMAL(7,3)   NULL COMMENT \'teknik.fps (yalniz video)\',
  video_kodec    VARCHAR(40)    NULL COMMENT \'teknik.video_kodec (yalniz video)\',
  ses_kodec      VARCHAR(40)    NULL COMMENT \'teknik.ses_kodec (yalniz video)\',
  bitrate_kbps   INT UNSIGNED   NULL COMMENT \'teknik.bitrate_kbps (yalniz video)\',
  bpm            SMALLINT UNSIGNED NULL COMMENT \'muzikal.bpm (20..300)\',
  ton            VARCHAR(20)    NULL COMMENT \'muzikal.ton (maxLength 20)\',
  enerji         TINYINT UNSIGNED NULL COMMENT \'muzikal.enerji (1..10)\',
  intro_sn       DECIMAL(8,3)   NULL COMMENT \'muzikal.intro_sn (min 0)\',
  outro_sn       DECIMAL(8,3)   NULL COMMENT \'muzikal.outro_sn (min 0)\',
  poster         VARCHAR(255)   NULL COMMENT \'gorsel.poster (yalniz video; ses kaydinda NULL)\',
  poster_zaman   DECIMAL(8,3)   NULL COMMENT \'gorsel.poster_zaman (0 .. sure_sn)\',
  is_deleted     TINYINT(1)     NOT NULL DEFAULT 0 COMMENT \'soft delete\',
  deleted_at     TIMESTAMP      NULL COMMENT \'silinme zamanı\',
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
COMMENT=\'Medya varlığı (ses|video) — $defs/meta_base; tekil dosya yolu + sha256 ile yinelenme imkansız\';',
    'CREATE TABLE variant (
  asset_id       CHAR(26)       NOT NULL COMMENT \'Ana varlık (FK) — $defs/meta_base.id\',
  dosya          VARCHAR(255)   NOT NULL COMMENT \'varyant dosya yolu (maxLength 255)\',
  format         VARCHAR(16)    NOT NULL COMMENT \'varyant format — ^[a-z0-9]{1,16}$\',
  boyut_bayt     BIGINT UNSIGNED NOT NULL COMMENT \'varyant boyutu (min 1)\',
  sha256         CHAR(64)       NOT NULL COMMENT \'varyant sha256 — hex, UNIQUE (yinelenen kodlama imkansız)\',
  PRIMARY KEY (asset_id, dosya),
  UNIQUE KEY uk_variant_sha256 (sha256),
  CONSTRAINT fk_variant_asset FOREIGN KEY (asset_id)
    REFERENCES asset(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_tr_0900_ai_ci
COMMENT=\'Varyant kodlamalar (teknik.varyantlar[]) — PK (asset_id, dosya) BCNF aday anahtarı\';',
    'CREATE TABLE taxonomy (
  alan           VARCHAR(60)    NOT NULL COMMENT \'taxonomy.json anahtarı (17 tanım: tur, kullanim, ...)\',
  deger          VARCHAR(60)    NOT NULL COMMENT \'o anahtarın değeri (kapalı set — yeni değer = taxonomy.json\'\'a 1 satır)\',
  sira           INT UNSIGNED   NOT NULL DEFAULT 0 COMMENT \'taxonomy.json diziliş sırası (1..N)\',
  aktif          TINYINT(1)     NOT NULL DEFAULT 1 COMMENT \'sözlükte aktif mi?\',
  PRIMARY KEY (alan, deger),
  INDEX idx_taxonomy_sira (alan, sira),
  INDEX idx_taxonomy_aktif (aktif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_tr_0900_ai_ci
COMMENT=\'Kapalı sözlük — taxonomy.json\'\'ın 1:1 türetilmiş indeksi (17 anahtar)\';',
    'INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  (\'tur\',\'arabesk\',1,1), (\'tur\',\'damar\',2,1), (\'tur\',\'turk-pop\',3,1), (\'tur\',\'turkce\',4,1),
  (\'tur\',\'yabanci\',5,1), (\'tur\',\'nostalji\',6,1), (\'tur\',\'oryantel\',7,1), (\'tur\',\'halay\',8,1),
  (\'tur\',\'roman-sorti\',9,1), (\'tur\',\'dans\',10,1), (\'tur\',\'klasik\',11,1), (\'tur\',\'slow\',12,1),
  (\'tur\',\'enstrumantal\',13,1), (\'tur\',\'turk-rock\',14,1);',
    'INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  (\'kullanim\',\'dugun\',1,1), (\'kullanim\',\'hane\',2,1), (\'kullanim\',\'org-esligi\',3,1),
  (\'kullanim\',\'alt-yapi\',4,1), (\'kullanim\',\'kina\',5,1), (\'kullanim\',\'pasta-nikah\',6,1),
  (\'kullanim\',\'ara-show\',7,1), (\'kullanim\',\'dj-set\',8,1);',
    'INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  (\'media_tipi\',\'full\',1,1), (\'media_tipi\',\'enstrumantal\',2,1), (\'media_tipi\',\'karaoke\',3,1),
  (\'media_tipi\',\'canli\',4,1), (\'media_tipi\',\'remix\',5,1), (\'media_tipi\',\'cover\',6,1);',
    'INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  (\'donem\',\'on-yil\',1,1), (\'donem\',\'70ler\',2,1), (\'donem\',\'80ler\',3,1), (\'donem\',\'90ler\',4,1),
  (\'donem\',\'2000ler\',5,1), (\'donem\',\'2010lar\',6,1), (\'donem\',\'2020lar\',7,1);',
    'INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  (\'ruh\',\'duygusal\',1,1), (\'ruh\',\'yavas\',2,1), (\'ruh\',\'enerjik\',3,1),
  (\'ruh\',\'nostaljik\',4,1), (\'ruh\',\'huzunlu\',5,1);',
    'INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  (\'klip_tipi\',\'official\',1,1), (\'klip_tipi\',\'live\',2,1), (\'klip_tipi\',\'lyric\',3,1),
  (\'klip_tipi\',\'dance\',4,1), (\'klip_tipi\',\'acoustic\',5,1);',
    'INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  (\'dil\',\'tr\',1,1), (\'dil\',\'en\',2,1), (\'dil\',\'de\',3,1), (\'dil\',\'fr\',4,1),
  (\'dil\',\'it\',5,1), (\'dil\',\'es\',6,1), (\'dil\',\'ar\',7,1), (\'dil\',\'fa\',8,1),
  (\'dil\',\'ru\',9,1), (\'dil\',\'az\',10,1), (\'dil\',\'ku\',11,1), (\'dil\',\'sq\',12,1);',
    'INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  (\'durum\',\'inbox\',1,1), (\'durum\',\'aktif\',2,1), (\'durum\',\'sakli\',3,1),
  (\'durum\',\'tekrar\',4,1), (\'durum\',\'arsiv\',5,1);',
    'INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  (\'album_tip\',\'studyo\',1,1), (\'album_tip\',\'canli\',2,1), (\'album_tip\',\'derleme\',3,1),
  (\'album_tip\',\'ep\',4,1), (\'album_tip\',\'soundtrack\',5,1), (\'album_tip\',\'tekli\',6,1);',
    'INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  (\'fiziksel_bicim\',\'lp\',1,1), (\'fiziksel_bicim\',\'45lik\',2,1), (\'fiziksel_bicim\',\'7lik\',3,1),
  (\'fiziksel_bicim\',\'cd\',4,1), (\'fiziksel_bicim\',\'kaset\',5,1), (\'fiziksel_bicim\',\'dijital\',6,1);',
    'INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  (\'sanatci_tip\',\'kisi\',1,1), (\'sanatci_tip\',\'grup\',2,1),
  (\'sanatci_tip\',\'topluluk\',3,1), (\'sanatci_tip\',\'cesitli\',4,1);',
    'INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  (\'video_kaynak\',\'yt\',1,1), (\'video_kaynak\',\'vhs\',2,1),
  (\'video_kaynak\',\'dvd\',3,1), (\'video_kaynak\',\'cekim\',4,1);',
    'INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  (\'koleksiyon_kaynak\',\'usb\',1,1), (\'koleksiyon_kaynak\',\'disk\',2,1),
  (\'koleksiyon_kaynak\',\'indirme\',3,1), (\'koleksiyon_kaynak\',\'diger\',4,1);',
    'INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  (\'altyazi_tip\',\'yan\',1,1), (\'altyazi_tip\',\'govde\',2,1);',
    'INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  (\'cover_kaynak\',\'kaynak\',1,1), (\'cover_kaynak\',\'uretilmis\',2,1), (\'cover_kaynak\',\'dis\',3,1);',
    'INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  (\'koleksiyon_hak\',\'kisisel\',1,1), (\'koleksiyon_hak\',\'ticari\',2,1), (\'koleksiyon_hak\',\'bilinmiyor\',3,1);',
    'INSERT INTO taxonomy (alan, deger, sira, aktif) VALUES
  (\'bitrate_tip\',\'yok\',1,1), (\'bitrate_tip\',\'cbr\',2,1),
  (\'bitrate_tip\',\'vbr\',3,1), (\'bitrate_tip\',\'abr\',4,1);',
    'CREATE TABLE asset_tag (
  asset_id       CHAR(26)       NOT NULL COMMENT \'Varlık (FK)\',
  alan           VARCHAR(60)    NOT NULL COMMENT \'tag anahtarı (taxonomy.alan)\',
  deger          VARCHAR(60)    NOT NULL COMMENT \'tag değeri (taxonomy.deger) — kapalı set\',
  PRIMARY KEY (asset_id, alan, deger),
  INDEX idx_asset_tag_alan_deger (alan, deger),
  CONSTRAINT fk_asset_tag_asset FOREIGN KEY (asset_id)
    REFERENCES asset(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_asset_tag_taxonomy FOREIGN KEY (alan, deger)
    REFERENCES taxonomy(alan, deger) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_tr_0900_ai_ci
COMMENT=\'Asset ↔ kapalı sözlük bağı — taxonomy FK sayesinde enum drift DB seviyesinde imkansiz\';',
    'CREATE TABLE path_history (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT \'Monotonic sayaç (iz satırı, ULID değil)\',
  asset_id       CHAR(26)       NOT NULL COMMENT \'İzlenen varlık (FK)\',
  eski_yol       VARCHAR(500)   NOT NULL COMMENT \'Yeniden adlandırma öncesi repo-göreli yol\',
  yeni_yol       VARCHAR(500)   NOT NULL COMMENT \'Yeniden adlandırma sonrası repo-göreli yol\',
  degistiren     VARCHAR(60)    NULL COMMENT \'Değiştiren (agent/kullanıcı)\',
  zaman          DATETIME       NOT NULL COMMENT \'Değişiklik zamanı (+03:00)\',
  PRIMARY KEY (id),
  INDEX idx_path_history_asset_id (asset_id),
  INDEX idx_path_history_zaman (zaman),
  CONSTRAINT fk_path_history_asset FOREIGN KEY (asset_id)
    REFERENCES asset(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_tr_0900_ai_ci
COMMENT=\'Dosya yolu değişiklik izi (rename audit) — append-only\';',
    'CREATE TABLE ingest_batch (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT \'Parti kimliği (sayaç)\',
  kaynak         VARCHAR(500)   NOT NULL COMMENT \'Tarama kaynağı (repo-göreli dizin/yol)\',
  `mod`          ENUM(\'dry-run\',\'commit\') NOT NULL COMMENT \'dry-run = yalnız rapor, commit = yaz\',
  dosya_sayisi   INT UNSIGNED   NOT NULL DEFAULT 0 COMMENT \'İşlenen dosya sayısı\',
  byte           BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT \'İşlenen toplam bayt\',
  baslangic      DATETIME       NOT NULL COMMENT \'Parti başlangıcı (+03:00)\',
  bitis          DATETIME       NULL COMMENT \'Parti bitişi (NULL = sürüyor)\',
  durum          ENUM(\'planlandi\',\'calisiyor\',\'basarili\',\'hatali\') NOT NULL DEFAULT \'planlandi\' COMMENT \'Parti durumu\',
  rapor_yolu     VARCHAR(500)   NULL COMMENT \'Üretilen rapor dosyası yolu (repo-göreli)\',
  PRIMARY KEY (id),
  INDEX idx_ingest_batch_durum (durum),
  INDEX idx_ingest_batch_baslangic (baslangic),
  INDEX idx_ingest_batch_mod (`mod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_tr_0900_ai_ci
COMMENT=\'Ingest/tarama parti kaydi — ADR-092 §5.2 adımlarının denetlenebilir izi\';',
    'CREATE OR REPLACE VIEW v_asset_search AS
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
  COALESCE(JSON_EXTRACT(al.tag, \'$.tur\'),
           JSON_EXTRACT(ar.varsayilan_tag, \'$.tur\')) AS tur
FROM asset a
LEFT JOIN album  al ON al.id = a.album_id      AND al.is_deleted = 0
LEFT JOIN artist ar ON ar.id = COALESCE(a.sanatci_id, al.sanatci_id) AND ar.is_deleted = 0
WHERE a.is_deleted = 0;',
];

return $queries;
