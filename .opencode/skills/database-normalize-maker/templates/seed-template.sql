-- =========================================================================
-- CoreMusic - Seed Data Template
-- 
-- Test verileri KESİNLİKLE gerçek bir insanın PII bilgisini içermez.
-- Ad, soyad, TC kimlik ve şifreler kurgusal olmak zorundadır.
-- 
-- Standard: BIGINT UNSIGNED PK (UUID kullanılmaz)
-- =========================================================================

-- ADR: Test verileri stres testi ve UI doğrulaması için üretilmiştir.

-- {SEED_SCRIPT}

-- Örnek (Kullanıcılar):
-- INSERT INTO users (email, password_hash, first_name, last_name, is_active) VALUES 
-- ('test1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test', 'User 1', 1),
-- ('test2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test', 'User 2', 1);

-- Örnek (Kategoriler):
-- INSERT INTO categories (name, slug) VALUES ('Rock', 'rock'), ('Pop', 'pop');

-- Örnek (Audit):
-- INSERT INTO users_audit (user_id, action, old_values, new_values, acted_by) VALUES
-- (1, 'INSERT', NULL, '{"email":"test1@example.com"}', NULL);
