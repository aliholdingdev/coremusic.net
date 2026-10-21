-- ===========================================================================
-- 20261021_backend_restructure.sql — CoreMusic Backend Yeniden Yapılandırma
-- ===========================================================================
-- Hedef Veritabanları: coremusic_musics, coremusic_playlist, coremusic_user
-- Prensipler: FORWARD-ONLY (down() yok) · BCNF / 3NF Uyumlu · SELECT * Yasak
-- Cross-DB FK: Uygulama seviyesinde doğrulanır (DB seviyesinde yazılmaz)
-- ===========================================================================

-- ---------------------------------------------------------------------------
-- 1. coremusic_musics: İndeks Güçlendirme & Hızlı Arama
-- ---------------------------------------------------------------------------
USE `coremusic_musics`;

-- Şarkı arama ve son eklenenler için bileşik indeks
ALTER TABLE `musics` 
  ADD INDEX `idx_musics_active_created` (`is_deleted`, `created_at` DESC),
  ADD INDEX `idx_musics_artist_created` (`artist_id`, `created_at` DESC);

-- Medya dosyası birincil format ve müzik erişim hızlandırması
ALTER TABLE `music_files`
  ADD INDEX `idx_mf_music_primary` (`music_id`, `is_primary`, `is_deleted`);

-- ---------------------------------------------------------------------------
-- 2. coremusic_playlist: Çalma Listesi Sıralama & Kullanıcı Erişimi
-- ---------------------------------------------------------------------------
USE `coremusic_playlist`;

-- Kullanıcının aktif çalma listelerini getirme indeksi
ALTER TABLE `playlists`
  ADD INDEX `idx_pl_user_updated` (`user_id`, `is_deleted`, `updated_at` DESC);

-- Çalma listesi içi parçaları sıralı getirme indeksi
ALTER TABLE `playlist_tracks`
  ADD INDEX `idx_pt_playlist_pos_created` (`playlist_id`, `position`, `created_at`);

-- ---------------------------------------------------------------------------
-- 3. coremusic_user: Dinleme Geçmişi (History & Recent Tracks) Hızlandırma
-- ---------------------------------------------------------------------------
USE `coremusic_user`;

-- Son dinlenen parçalar (recent tracks) ve tekilleştirilmiş son dinleme sorguları
-- SELECT music_id, MAX(started_at) FROM user_listening_history WHERE user_id=? GROUP BY music_id
ALTER TABLE `user_listening_history`
  ADD INDEX `idx_ulh_user_recent` (`user_id`, `started_at` DESC, `music_id`),
  ADD INDEX `idx_ulh_user_music_recent` (`user_id`, `music_id`, `started_at` DESC);

-- ===========================================================================
-- BİTTİ — Kullanıcı localhost ortamında bu dosyayı uygulayabilir.
-- ===========================================================================
