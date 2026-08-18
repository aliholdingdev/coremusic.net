<?php declare(strict_types=1);

/**
 * CoreMusic — OAuth Connections Migration (coremusic_social DB)
 *
 * ADR-088 + ADR-072 compliant.
 * oauth_connections tablosu coremusic_social veritabanına eklenir.
 *
 * @see [[decisions/accepted/ADR-088-gender-based-social-oauth]]
 * @see [[decisions/accepted/ADR-072-social-database-schema]]
 */

$queries = [

// ============================================================
// oauth_connections — Sosyal medya OAuth bağlantıları
// ============================================================
"CREATE TABLE IF NOT EXISTS oauth_connections (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    provider ENUM(
        'instagram', 'pinterest', 'tiktok', 'snapchat',
        'discord', 'reddit', 'x', 'linkedin', 'youtube', 'facebook'
    ) NOT NULL,
    provider_user_id VARCHAR(255) NOT NULL,
    provider_username VARCHAR(255) DEFAULT NULL,
    access_token_encrypted TEXT NOT NULL,
    refresh_token_encrypted TEXT DEFAULT NULL,
    token_expires_at TIMESTAMP NULL DEFAULT NULL,
    scopes TEXT DEFAULT NULL,
    profile_data JSON DEFAULT NULL,
    connected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_used_at TIMESTAMP NULL DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    UNIQUE KEY uk_provider_user (provider, provider_user_id),
    INDEX idx_user_active (user_id, is_active),
    INDEX idx_provider (provider),
    INDEX idx_expires (token_expires_at),
    FOREIGN KEY (user_id) REFERENCES coremusic_user.users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

];

return $queries;
