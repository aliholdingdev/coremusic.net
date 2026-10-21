-- ===========================================================================
-- 20261021_bcnf_normalize_active_schemas.sql
-- Aktif şemalar BCNF/normalizasyon düzeltmesi (yalnız normalization — FK YOK)
-- ===========================================================================
-- Karar 3 NOTU: Önceki cross-DB FK'lar (playlist ×4, history ×1) bu dosyadan
--   KALDIRILDI ve ilgili migration dosyalarına TAŞINDI:
--     · playlist FK'lar   -> 20261021_create_playlist_tables.sql   (BÖLÜM 1, CASCADE)
--     · history FK'lar    -> 20261021_create_listening_tables.sql  (BÖLÜM 1, RESTRICT)
--     · notification FK   -> 20261021_create_notifications.sql     (BÖLÜM 1, CASCADE)
--   Bu dosya artik YALNIZCA normalizasyon/standartlasma (BCNF) icerir — FK yoktur.
-- Kapsam : novasearch search_log/search_logs isim standardizasyon notu.
-- Prensip: FORWARD-ONLY (ADR-007/014) — sütun DROP/DELETE YOK. Veri kaybı YOK.
-- Charset: utf8mb4 / utf8mb4_unicode_ci (aktif şemalarla hizalı — dump teyitli).
-- Idempotent: CREATE OR REPLACE VIEW ✓ (guvenli, tekrar calistirilabilir).
-- ===========================================================================

-- ===========================================================================
-- UP
-- ===========================================================================

-- ---------------------------------------------------------------------------
-- BÖLÜM 1 — novasearch: search_log / search_logs standardizasyon notu
-- ---------------------------------------------------------------------------
-- ÇELİŞKİ [PROJECT EVIDENCE]:
--   · Canlı MySQL dump (novasearch.sql:92)  -> tablo `search_logs` (çoğul),
--     kolon `response_time_ms`.
--   · Node.js nova-search-engine (schema.ts:69, repositories.ts:281)
--     -> tablo `search_log` (tekil), kolon `took_ms`.
-- Karar: MySQL tarafı kanonik = `search_logs` (fix_constraints_20261021 C-5 bu
--   tabloya idx_search_logs_created ekledi). Kod tarafı standardizasyonu Node.js
--   paketinde yapılır (bu migration PHP/DB sınırını aşmaz — §10 domen sınırı).
--   MySQL'de DESTRUCTIVE rename YAPILMAZ (knex migration-lock tabloları bu DB'de).
--   Aşağıdaki VIEW, tekil adı okuyan tüketiciler için salt-okunur köprüdür.
--   BCNF notu: `videos.channel_name` / `playlists.channel_name` transitive bağımlılık
--   (N4) — CONTRACT fazında ele alınır (normalization-analysis §4.5); şu an dokunulmaz.

CREATE OR REPLACE VIEW `novasearch`.`v_search_log_singular` AS
SELECT
  `id`,
  `query`,
  `filters`,
  `results_count`,
  `response_time_ms` AS `took_ms`,
  `source`,
  `created_at`
FROM `novasearch`.`search_logs`;

-- ===========================================================================
-- DOWN (ROLLBACK — yalnız referans; ADR-014 forward-only: production DOWN çalıştırmaz)
-- ===========================================================================
-- DROP VIEW IF EXISTS `novasearch`.`v_search_log_singular`;

-- ===========================================================================
-- POST-CHECK (uygulama sonrası):
--   SELECT * FROM information_schema.views WHERE table_name='v_search_log_singular';
-- ===========================================================================
