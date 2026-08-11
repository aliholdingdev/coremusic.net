-- =========================================================================
-- AI Agentic Orchestrator - Seed Data Template
-- 
-- Kural (Security Engineer): Test verileri KESİNLİKLE gerçek bir insanın 
-- PII (Kişisel) bilgisini içeremez. Ad, soyad, TC kimlik ve şifreler 
-- kurgusal (fictional/hashed) olmak zorundadır.
-- =========================================================================

-- ADR: Test verileri sistemin stres testi ve UI doğrulamasında kullanılmak
-- üzere (Zero-Hallucination) belirlenen sınır kısıtlarına uygun üretilmiştir.

-- {SEED_SCRIPT}

-- Örnek (Kullanıcılar):
-- INSERT INTO users (uuid, email, password_hash, first_name, last_name, is_active) VALUES 
-- (UUID(), 'test1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test', 'User 1', 1),
-- (UUID(), 'test2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test', 'User 2', 1);

-- Örnek (Audit):
-- INSERT INTO users_audit (user_id, action, changed_fields, acted_by) VALUES
-- (1, 'INSERT', '{"email":"test1@example.com"}', 1);
