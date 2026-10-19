---
node_id: K0100-identitiy/credentials/credentials-ornekler
node_type: subdomain-scope
status: draft
parent_id: K0100-identitiy/credentials
children: []
depends_on:
  - K0100-identitiy
evidence:
  - "auth.coremusic.net/include/Domain/ValueObject/Password.php L44-62 (hashWithPepper + verify) — 2026-10-12"
  - "shared/src/Repository/ApiKeyRepository.php başlık docblock (hash-only kontratı) — 2026-10-12"
validation: "credentials-dogrulama.md §1 (12 iddia) · §5-§8 resmi kaynaklar HTTP 200 doğrulandı"
group: kod
open_questions:
  - "A1: parola hash'i K010 mu K007 mi — ADR-097 K4 bekliyor"
  - "A3: APP_PEPPER saklama/rotasyon örneği üretilmedi (kanıt yok)"
---

# credentials — Örnekler

## §1 Gerçek repo kodu (kanıt — uydurma yok)

### Örnek 1.1 — Parola hash ValueObject (Argon2id + pepper)

`auth.coremusic.net/include/Domain/ValueObject/Password.php` satır 44–62 (okundu, 2026-10-12):

```php
public function hashWithPepper(string $pepper): string
{
    if ($pepper === '') {
        throw \CoreMusic\Exception\ServerException::configError('APP_PEPPER');
    }
    $peppered = hash_hmac('sha256', $this->raw, $pepper);
    return password_hash($peppered, PASSWORD_ARGON2ID, [
        'memory_cost' => 65536,
        'time_cost'   => 4,
        'threads'     => 2,
    ]);
}

public function verify(string $hash, string $pepper): bool
{
    if ($pepper === '') {
        return false;
    }
    $peppered = hash_hmac('sha256', $this->raw, $pepper);
    return password_verify($peppered, $hash);
}
```

### Örnek 1.2 — API anahtarı hash-only kontratı

`shared/src/Repository/ApiKeyRepository.php` başlık docblock (okundu):

```
Güvenlik: ham anahtar ASLA DB'ye yazılır yalnız SHA-256 hash + prefix),
loglara/yanıtlara yalnız `key_prefix` düşer.
```

> Not: dosya başlığının tam metni "ham anahtar ASLA DB'ye yazılmaz (yalnız SHA-256
> hash + prefix)" şeklindedir; burada satır sonu kaynaklı kısaltma vardır — ham
> anahtar saklama yasağı含义 aynıdır. Doğrulama akışı: prefix index taraması →
> `hash_equals(key_hash, sha256(raw))` → `is_active=1 AND is_deleted=0`.

### Örnek 1.3 — Şema: vault + anahtar sürümü

`.ai/.sql/mysql/coremusic_auth.sql` (canlı dump 2026-10-07):

```sql
`encrypted_value` text NOT NULL COMMENT 'AES-256-GCM encrypted credential value',
`encryption_algorithm` varchar(50) DEFAULT 'AES-256-GCM',
`rotation_interval_days` int DEFAULT '90' COMMENT 'Auto-rotation interval in days',
```

```sql
CREATE TABLE `credential_keys` (… `key_hash` char(64) NOT NULL, … )
… COMMENT='Encryption key version management — hash-only, no raw keys';
```

## §2 Resmi doküman örneği

Bu dalın dış web bağımlılığı bu turda doğrulanmadı — parola hash(Argon2id)
seçimi bir **standart zorunluluğu değil**, ürün kararıdır; OWASP M05
referansı master §15'te taşınır ve burada tekrarlanmaz. Token/imza tarafının
resmi dokümanları `tokens` ve `identity-integration` dalındadır.

## §3 Uygulama notu

- Parola hash'i runtime'da `Password` VO'da üretilir (repo gerçeği); "K007/pgcrypto'da üretilsin" tasarımıyla olan çakışma ADR-097 K4'e bağlıdır — bu kayıt iki olasılığı da açık tutar.
- Vault'taki `credential_type='password'` satırı kullanıcı parolası olarak yorumlanmaz; kullanımı A4 incelemesine bağlıdır.

## §4 İlişkili dosyalar

- `K0100-identitiy/credentials/credentials-kapsam.md` — sınır ve disk kanıtları
- `K0100-identitiy/credentials/credentials-dogrulama.md` — iddia tablosu (12 satır)
- `identity-master.md` §3 · `identity-dogrulama.md` §G-10.1

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
