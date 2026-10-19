---
node_id: K009-security/secrets/bilesenler
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/encryption
  - K009-security/dogrulama
evidence:
  - shared/src/Log/Redactor.php:28-105
  - auth.coremusic.net/include/Config/ConfigManager.php:53
  - auth.coremusic.net/include/Security/Password.php:26-59
  - auth.coremusic.net/include/Infrastructure/Security/ApiSessionManager.php:85
  - auth.coremusic.net/index.php:60,131-137
  - shared/src/OAuth/OAuthManager.php:170-235
  - shared/src/Session/SessionConfig.php:18-40
validation:
  - secrets-bilesenler §1 · Password: 2 pepper kanıtı · R-21
  - secrets-bilesenler §2 · ConfigManager: .env · tek dosya
  - secrets-bilesenler §3 · Redactor: 3 boşluk · R-22/R-23
  - secrets-bilesenler §4 · ApiSessionManager/OAuthManager/SessionConfig
  - secrets-bilesenler §5 · rotasyon · ADR E-05
  - secrets-bilesenler §6 · kademe-3 matris
  - secrets-bilesenler §7 · iş kalemleri
  - secrets-bilesenler §8 · doğrulanacaklar
open_questions:
  - E-05: .env rotasyon prosedürü ADR?
  - R-21: sabit bypass pepper non-prod?
group: mimari
---

# K009-security / secrets — Bileşenler (Kademe-3)

> Authority: Bayram Ali / Vault Steward · SSOT: coremusic-katmanli-mimari-agaci-v2.md (§2.9)
> Bu dosya salt-okunur: kod kanıtları disk'ten, iddialar doğrulanabilir.

## §1 Password (2 pepper)

~~~php
// auth.coremusic.net/include/Domain/ValueObject/Password.php:26-33
$peppered = hash_hmac('sha256', $password, self::PEPPER);
$hash = password_hash($peppered, PASSWORD_ARGON2ID, [
    'memory_cost' => 65536, 'time_cost' => 4, 'threads' => 1
]);
// :54-59 verify da peppered üzerinden
$peppered = hash_hmac('sha256', $password, self::PEPPER);
return password_verify($peppered, $this->hash);
~~~

2 pepper (app + api, Password.php:16-17) .env'den (Password.php:24). **Bypass pepper R-21**:
index.php:132 sabit non-prod pepper.

## §2 ConfigManager

Auth backend tek dosya: ConfigManager.php:53. API tarafı .env.local yüklüyor
(ApiSessionManager:85 → EnvLoader) — 2 farklı .env okuma yolu.

## §3 Redactor (3 boşluk)

:42-47 regex + :49-52 özel + :63-85 context + :88-105 JSON. Boşluklar R-22/R-23.

## §4 Token depoları

| Sınır | Kod | Kanıt |
|---|---|---|
| ApiSessionManager | redis-prefixed token | :85 .env |
| OAuthManager | AES-256-GCM (state/token) | :170-235 |
| SessionConfig | session.name/cookie | :18-40 |

## §5 Rotasyon — ADR E-05

| Sınır | Rotasyon | Durum |
|---|---|---|
| argon2id | password_hash rehash (verifyUpdate) | :43 — mekanizma var |
| app/api pepper | manuel | **ADR E-05** |
| bypass pepper (R-21) | — | non-prod sabit |
| OAuth AES key | manuel | OAuthManager:180 |
| API pepper | — | ADR E-05 |

## §6 Kademe-3 matris

| # | Sınır | Rol | Kanıt |
|---|---|---|---|
| 1 | Password (2 pepper) | argon2id + HMAC | Password:16-59 |
| 2 | ConfigManager | .env tek dosya | :53 |
| 3 | Redactor | maskeleme | Redactor:28-105 |
| 4 | ApiSessionManager | redis token | :85 |
| 5 | OAuthManager | AES-256-GCM | :170-235 |
| 6 | SessionConfig | cookie/session | :18-40 |

## §7 İş Kalemleri

| Kalem | Aksiyon | Öncelik |
|---|---|---|
| R-21 | bypass pepper — yalnız non-prod guard'lı | Medium |
| R-22 | Redactor 3 boşluk | Medium |
| R-23 | .log rotasyon | Low |
| E-05 | pepper/KEY rotasyon ADR | ADR |

## §8 Doğrulanacaklar

1. E-05 pepper rotasyon ADR yazımı
2. R-22 Redactor kapsam genişletme
3. R-23 log rotasyon (envanter sonrası)
4. API .env yolu harmonizasyonu (Auth vs API)
5. Bypass pepper envanteri (R-21)
6. Redis token TTL doğrulaması (CacheRateLimiter)
7. OAuth AES key rotasyon planı
8. password_hash rehash akışı doğrulaması
9. Argon2id parametre revizyonu envanteri
10. LogRedactSettings doğrulaması (K010-devops)


## §9 Kademe-2 Referans

Kademe-2 ortak yüzeyler: config-manager · secret-store · log-redactor (bkz.
security-master.md §3). §6'daki 6 bileşen bu kademe-2 sınıfların kademe-3
somutlaşmasıdır: ConfigManager → config-manager; Redactor → log-redactor;
Password/ApiSessionManager/OAuthManager/SessionConfig → secret-store
bağımlıları (encryption kanalı üzerinden).

---
Authority: Bayram Ali / Vault Steward · node_id: K009-security/secrets/bilesenler · group: mimari
