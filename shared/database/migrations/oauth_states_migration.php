<?php declare(strict_types=1);

/**
 * CoreMusic — OAuth States Table (CSRF koruması için)
 *
 * ADR-088 compliant — OAuth state token yönetimi.
 * Her state token 10 dakika geçerli, tek kullanımlık.
 *
 * @see [[decisions/accepted/ADR-088-gender-based-social-oauth]]
 */

// oauth_connections tablosu ADR-072 social database'e eklenir
// Bu dosya sadece oauth_states tablosunu oluşturur

$queries = [

// ============================================================
// oauth_states — OAuth CSRF state token yönetimi
// ============================================================
"CREATE TABLE IF NOT EXISTS oauth_states (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    state_token VARCHAR(64) NOT NULL,
    provider VARCHAR(32) NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    code_verifier VARCHAR(128) DEFAULT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_state_token (state_token),
    INDEX idx_provider_user (provider, user_id),
    INDEX idx_expires (expires_at),
    FOREIGN KEY (user_id) REFERENCES coremusic_user.users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

];

return $queries;
