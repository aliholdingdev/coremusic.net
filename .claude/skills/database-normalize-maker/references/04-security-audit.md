# 04 — Güvenlik Denetimi (OWASP / PII / Audit)

> Bu alan **security-engineer** ile ortaktır; güvenlik veto yetkisi vardır.
> Bu referans şema-güvenliği denetimini tanımlar. Kod-uzmanı: `security-audit.php`.

## Şema-güvenliği kontrolleri

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | PII kolonları şifreli mi? | Hassas alan (email, ip, token, ödeme) AES-256-GCM / uygun şifreleme veya ADR etiketi |
| 2 | Audit tablo var mı? | Kritik tablo (`users`, `payments`, `orders`) için `{tablo}_audit` |
| 3 | Soft delete | Hard delete yasak; `deleted_at` |
| 4 | Naming (SQLi yüzeyi) | Tablo/kolon `snake_case`; dinamik sorgu yüzeyini daralt |
| 5 | Secret yok | Şemada/API'de sabit anahtar/token yok; `.env`/vault |
| 6 | Rol/izin | Hassas tabloya erişim ayrı yetki |
| 7 | Para tipi | `DECIMAL`, `FLOAT` yasak (yuvarlama → tutarsızlık) |

## PII şifreleme notu

- Hassas kolon, şifreli blob için yeterli alan sağlamalı (ör. `TEXT`/`VARBINARY` + AEAD).
- Anahtar yönetimi şemanın dışında (`.env`/vault); şemada asla sabit anahtar yok.
- Hash (parola) ≠ şifreleme (geri alınabilir PII). Parola: Argon2id; PII: AEAD.

## Audit trail deseni

```sql
CREATE TABLE payments_audit (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    payment_id BIGINT UNSIGNED NOT NULL,
    action ENUM('INSERT','UPDATE','DELETE') NOT NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    acted_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_payments_audit_payment (payment_id),
    KEY idx_payments_audit_action (action),
    KEY idx_payments_audit_created (created_at),
    CONSTRAINT fk_payments_audit_payments FOREIGN KEY (payment_id) REFERENCES payments (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
```

> Trigger ile `old_values`/`new_values` JSON olarak doldurulur (bkz. SKILL.md §4.4 / 06).
> `ENUM` burada sabit ve değişmez bir aksiyon kümesi olduğu için kabul edilir (lookup'a gerek yok).

## OWASP eşlemesi (şema düzeyi)

| OWASP teması | Şema karşılığı |
|--------------|----------------|
| A01 Broken Access Control | Rol bazlı yetki, sahiplik kolonu (`user_id`) |
| A02 Cryptographic Failure | PII AES-256-GCM; parola Argon2id; `FLOAT` para yasak |
| A04 Insecure Design | Soft delete + audit; polymorphic FK yasak |
| A05 Security Misconfig | Collation/engine tutarlılığı; gizli bilgi şemada yok |
| A08 Software/Data Integrity | Migration checksum + forward-only |

## Denetim script'i

`scripts/security-audit.php` — PII/encrypt, audit tablo ve naming kontrollerini tarar.
Canlı çalıştırma bu ortamda `VERIFICATION REQUIRED` (PHP yok; bkz. SKILL.md §11).
