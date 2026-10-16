---
node_id: K009-security/secrets/secrets-surucu
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on: [K009-security/secrets/secrets-anayasa, K009-security/encryption/encryption-surucu]
evidence:
  - "shared/src/Config/EnvParser.php:7-50 (load / loadIntoEnv)"
  - "shared/src/Config/ConfigManager.php:22-71, 125-154"
  - "shared/src/Log/Redactor.php:65-105 (redact zinciri)"
  - "shared/src/Log/FileHandler.php:220-245"
  - ".github/workflows/secret-scan.yml:7-31"
  - ".gitleaks.toml:1-19 (2026-10-15 disk ölçümü — F-5 düzeltmesi)"
  - "auth.coremusic.net/index.php:131-136"
validation: "2026-10-15 — 26 satır-kanıt; V-1..V-9 açık; ⚠️ 4 dükkan; §18 disk yeniden doğrulama (gitleaks düzeltmesi)"
open_questions:
  - "V-3: kesik .env satırı fail-closed'u atlayabilir mi? (EnvParser sessiz atlama)"
group: mimari
---

# K009 · secrets · Sürücü

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Mimari Sürücü

Bu belge `secrets-anayasa` kararlarını **çalışan akışlara** bağlar: env okuma, mask
uygulama, CI taraması, bypass denetimi. Her adım dosya/satır ile kanıtlanır.

## 1. Sürüş Ailesi

| # | Sürüş | Tetikleyici |
|---|---|---|
| F-1 | env yükleme akışı (boot) | uygulama başlangıcı |
| F-2 | sensitive okuma → fail-closed | `getSecure()` çağrısı |
| F-3 | config dump maskesi | `all()` (debug/yanıt) |
| F-4 | log redaction zinciri | her `FileHandler::write` |
| F-5 | gitleaks taraması | push / PR |
| F-6 | `.env` okunabilirlik / parse hata dalı | EnvParser |
| F-7 | bypass HMAC fallback | auth root redirect |
| F-8 | env vs dosya önceliği | `loadIntoEnv` |

## 2. F-1 · ENV Yükleme Akışı

```
boot (subdomain config/constants.php → EnvParser)
  │
  EnvParser::load(path)                      EnvParser.php:7-40
    ├─ !is_file → []                         :9-11  (exception değil)
    ├─ file() false → []                     :14-17
    ├─ '#' veya '=' yok → satır ATLA         :19-26  ⚠️ sessiz (V-3)
    ├─ explode('=', $line, 2) → key/value    :27-29
    ├─ tırnak temizliği                      :30-35
    └─ $env[$key] = $value  → SON satır kazanır :36
  │
  EnvParser::loadIntoEnv(path)               :42-50
    └─ foreach: !isset($_ENV[$key]) ise yaz   :46-48
       → MEVCUT $_ENV bir kez yazılmaz (dış ortam > dosya)
```

**Sürücü pseudocode (F-1):**

```php
// EnvParser.php:46-48 (paraphrase)
foreach ($env as $key => $value) {
    if (!isset($_ENV[$key])) {      // dış ortamda tanımlı → dosya ezilmez
        $_ENV[$key] = $value;
    }
}
```

**Çıktı:** CI/prod'da değişken önceden tanımlıysa `.env` ihmal edilir; tanımlı değilse
`.env` doldurur; hiç yoksa `getEnv` default'a düşer.

## 3. F-2 · Sensitive Okuma → Fail-Closed

```
$svc = $config->getSecure('security.app_pepper')   ConfigManager.php:42-49
  │
  ├─ get('security.app_pepper')            :22-40 (dot-path + cache)
  ├─ value null veya '' ?
  │     EVET → throw ServerException::configError($key)   :45-46
  │             → DI konteyner kurulumu DÜŞER
  │             → prod: 500 (fail-closed); fallback DEĞER YOK
  └─ HAYIR → (string) döner → kod kullanır (log değil)
```

**Doğrulanmış sır listesi** (`ConfigManager.php:10-15`):

| # | Key | Kullanan |
|---|---|---|
| 1 | `database.mysql.password` | DB bağlantısı |
| 2 | `security.app_pepper` | pepper/HMAC |
| 3 | `security.encryption_key` | encryption-surucu F-4 |
| 4 | `security.session_key` | session imzası |

**Sürücü kuralı:** bu 4 anahtar için **hiçbir yerde** `$config->get(..., 'default')`
kullanılmaz; kullanılırsa anayasa §11.4 ihlali (doğrulama: `secrets-dogrulama` grep
testi).

## 4. F-3 · Config Dump Maskesi

```
yanıt/debug gerekiyor → $config->all()        ConfigManager.php:125-131
  ├─ all(true) → ham (yalnız debug; loga YASAK — anayasa §11.5)
  └─ all() / all(false)
        → filterSensitive($config)            :136-154
             ├─ isSensitiveKey(fullPath)      :68-71
             │     EVET → '[REDACTED]'        :143
             └─ dizi → recursive prefix ile   :147-148
```

**Pseudocode (paraphrase, :136-154):**

```php
foreach ($data as $key => $value) {
    $fullKey = $prefix !== '' ? $prefix . '.' . $key : $key;
    if ($this->isSensitiveKey($fullKey)) { $filtered[$key] = '[REDACTED]'; continue; }
    if (is_array($value)) { $filtered[$key] = $this->filterSensitive($value, $fullKey); }
    else { $filtered[$key] = $value; }
}
```

**⚠️ V-1:** `OAUTH_*_CLIENT_SECRET` ve JWT yolları listede değil (anayasa Z-4) →
`all()` OAuth alanlarını **düz** döndürür; dump log'a yazılırsa sızıntı. Doğrulama
gerekli.

## 5. F-4 · Log Redaction Zinciri

```
Log çağrısı
  FileHandler::write(...)                          FileHandler.php:~210-245
    ├─ satır kur: [ts] [lvl] [method uri] [ip] [trace] msg ctx   :220
    ├─ $line = Redactor::redact($line)   ZORUNLU                 :224
    │     Redactor.php:65-93, 3 adım:
    │       1) KEY_VALUE_PATTERN  (11 anahtar + Bearer/Basic/Digest) :40,72-76
    │       2) LONG_HASH_PATTERN  (64 hex/base64; UUID korunur)      :49,83
    │       3) EMAIL_PATTERN      (→ ***@domain)                     :56,86-90
    ├─ errors.log append                                             :228
    └─ $securityStream → coremusic_php_security.log                  :233-235
```

**MaskValue davranışı** (`Redactor.php:98-105, paraphrase):**

```php
// "password":"x"  → "password":"[REDACTED]"   (tırnak korunur)
// password=x      → password=[REDACTED]
if ($value !== '' && ($value[0] === '"' || $value[0] === "'")) {
    return $prefix . $value[0] . self::MASK . $value[0];
}
return $prefix . self::MASK;
```

**⚠️ V-2:** `client_secret=…` anahtar-adı desene girmez (anayasa U-2/Z-5 regex
analizi) → bu sürüşte **kısmi kaçış** olabilir; 64-char secret yakalanır, kısa
secret yakalanmaz. Doğrulama: `secrets-dogrulama` test senaryosu.

## 6. F-5 · Gitleaks Tarama Akışı

```
push | pull_request (branch filtresi YOK)     secret-scan.yml:7-9
  → permissions: contents:read                :11-12
  → concurrency (ref grubu, cancel)           :14-16
  → job: ubuntu-latest, timeout 10dk          :18-22
  → checkout@v4, fetch-depth:0                :24-26   ← geçmiş commit'ler dahil
  → gitleaks-action@v2                      :28-31
       ├─ .gitleaks.toml (kök, 19 satır)     VAR — useDefault + test allowlist
       │    (workflow:4 yorumu "YOK" diyor → eskimiş; disk kazanır, 2026-10-15)
       └─ ihlal → job fail → PR kilit
```

**Çıktı:** geçmişte sızan gerçek sır da (depth:0) taranır; allowlist **yalnız** test
dosyalarını kapsar (`.gitleaks.toml:11-18`) → anayasa U-1 **kapandı** (2026-10-15).
Yeni deny-pattern eklemek `.github/AGENTS.md` §3.1 gereği **ADR/Security onayı** ister.
⚠️ VERIFICATION REQUIRED: action'ın config'i otomatik yüklediği iddiası
`.gitleaks.toml:2`'nin kendi beyanıdır; bu oturumda CI çalıştırılmadı.

## 7. F-6 · `.env` Okunabilirlik / Parse Hata Dalı

```
EnvParser::load(path)
  ├─ dosya yok          → []      (boot devam; getEnv default zinciri çalışır)
  ├─ read başarısız     → []      (file() false :14-17)
  ├─ satırda '=' yok    → ATLA    (:24-26)   ⚠️ sessiz
  └─ KEY="kesik değer   → değerin tırnağı strip edilir (:30-35), KALAN YARIM DEĞER yazılır
        → getSecure: '' değil → fail-closed ATLANABİLİR  ← V-3
```

**⚠️ V-3 (VERIFICATION REQUIRED):** yarım/kesik değer `null|''` kontrolünü geçerse
fail-closed **atlanır** ve bozuk sır kullanılır. Test: `.env`'e kesik satır atıp
ne olduğunu ölçmek gerekir (davranış testi `secrets-dogrulama`'da).

## 8. F-7 · Bypass HMAC Fallback

```php
// auth.coremusic.net/index.php:131-136 (paraphrase)
if ($bypassActive) {                                   // :131 — yalnız bypass dalı
    $bypassKey = hash_hmac('sha256',
        'bypass_' . date('Y-m-d'),                     // günlük anahtar
        defined('APP_PEPPER') ? APP_PEPPER : 'coremusic-bypass');   // :132 ⚠️
    header('Location: ' . MUSIC_URL . '/auth/callback?auth_key=' . urlencode($bypassKey), true, 302);
    exit;
}
```

**Sürücü davranışı:** `APP_PEPPER` **tanımlıyken** sır güçlü (64-hex pepper,
`.env.example:41` zorunlu); **tanımlı değilse** sabit `'coremusic-bypass'` ile günlük
`auth_key` **tahmin edilebilir** (kaynağı bilinen 1 değer + tarih).

**Bağlam:** bypass yalnız `FORCE_AUTH_BYPASS` + prod dışı aktif (ADR-008 notu
`.env.example:17`) → prod dışı etki; **yine de fail-open** — anayasa §11.1 ihlali,
düzeltme: tanımsız pepper → exception (fail-closed).

## 9. F-8 · Env vs Dosya Önceliği

| Durum | Kazanan | Kanıt |
|---|---|---|
| shell/CI env tanımlı + `.env` var | **shell/CI env** (`$_ENV` korunur) | `EnvParser.php:46-48` |
| yalnız `.env` | `.env` değeri | `EnvParser.php:47` |
| ikisi de yok | `getEnv` default / `getSecure` exception | `ConfigManager.php:51-54, 45-46` |
| `.env` içinde tekrar eden key | **son satır** | `EnvParser.php:36` |

## 10. Çevrimdışı (Air-Gap) Dalı

| Senaryo | Akış |
|---|---|
| CI runner'da `.env` yok + env yok | F-2 exception → deploy DUR (doğru davranış) |
| Pre-prod (Stage-2) | env→secrets-man → uç bileşenler (encryption-surucu §8); `.env` yalnız dev |

## 11. Varsayılanlar & Bilinmeyenler

| # | Soru | Karar |
|---|---|---|
| V-1 | `all()` OAuth secret sızıntısı | anayasa Z-4 ⚠️ |
| V-2 | `*_SECRET` log kaçış regexi | anayasa U-2 ⚠️ |
| V-3 | kesik `.env` değeri fail-closed'u atlar mı? | ⚠️ doğrulama gerekli |
| V-4 | ✅ `.gitleaks.toml` VAR (19 satır); yeni desen eklemek ADR onayı ister | U-1 kapandı (2026-10-15) |
| V-5 | Bypass fallback düzeltme tarihi? | ⚠️ karar bekliyor |
| V-6 | Pre-commit hook (`.githooks/`) aktif mi? | UNKNOWN |
| V-7 | İkinci log write yolu? | UNKNOWN |
| V-8 | Secret rotation saati? | UNKNOWN |
| V-9 | Prod `.env` dağıtım yolu? | UNKNOWN |

## 12. Hata Dalı Tablosu (sürüş → hata → sonuç)

| Sürüş | Hata durumu | Sonuç | Kanıt |
|---|---|---|---|
| F-1 | `.env` yok | `[]` → boş env, boot devam | EnvParser:9-11 |
| F-1 | `file()` false | `[]` | EnvParser:15-17 |
| F-1 | satırda `=` yok | sessiz atlama ⚠️ V-3 | EnvParser:24-26 |
| F-2 | sır boş/null | `ServerException::configError` → DI düşer | ConfigManager:45-46 |
| F-2 | sır `false`/`0` (string) | geçer → ⚠️ fail-closed'u atlayabilir | V-3 ile aynı sınıf |
| F-3 | `all(true)` log'a yazıldı | §11.5 ihlali (incelemede RED) | kural |
| F-4 | `preg_replace` null döner | satır **maskeyle geri döner** (`redact:78-80`) | Redactor:78-80 |
| F-4 | context JSON encode | satır `:217` → sonra yine `:224` redact | FileHandler:217,224 |
| F-5 | gitleaks ihlali | job fail → PR kırmızı | secret-scan.yml:28-31 |
| F-5 | `.gitleaks.toml` VAR (kök, 19 satır) | useDefault + test-only allowlist | `.gitleaks.toml:1-19` |
| F-7 | `APP_PEPPER` tanımlı | güçlü HMAC (`index.php:132` true kolu) | index.php:132 |
| F-7 | `APP_PEPPER` tanımsız | ⚠️ fallback `'coremusic-bypass'` | index.php:132 |
| F-8 | `$_ENV[$key]` mevcut | dosya değeri **yazılmaz** | EnvParser:46-48 |

## 13. F-2a · SENSITIVE_KEYS Denetim Akışı (yeni alan eklendiğinde)

```
Yeni config anahtarı eklendi
  → sır niteliği mi? (şifre / pepper / anahtar / token)
      EVET → ConfigManager.php:10-15 listesine EKLE
              → getSecure ile mi okunacak?  (§11.4: get() ile okunamaz)
              → all() dump'ında [REDACTED] olur mu?  (test T-2)
      HAYIR → get(key, default)
  → alan .env.example'a mı giriyor?
      → EVET → boş değer / CHANGE_ME placeholder (§11.3)
  → alan log'a taşınıyor mu?
      → Redactor:31 deseninde anahtar adı var mı? (YOKSA → Z-5 sınıfı, desene ekle)
```

**Yasak anı:** yeni sır alanı `get()` ile okunup "boşsa default" ile çalışırsa
fail-closed kırılır — bu, sürüş F-2'nin anayasa ihlalidir.

## 14. F-4a · Log Dosya Envanteri (FileHandler çıkışları)

| Dosya | Satır | Ne zaman yazılır | Redact |
|---|---|---|---|
| `coremusic_php_errors.log` | :228 | her seviye, her zaman | ✅ :224 (öncesi) |
| `coremusic_php_security.log` | :233-235 | `$securityStream` — auth + security olayları (prod dâhil) | ✅ aynı satır |
| `coremusic_php_warnings.log` | :238-240 | debug modda warning/error | ✅ |
| `coremusic_php_info.log` | :241-243 | debug modda info/debug | ✅ |

**Tek nokta kuralı:** dört çıkış da **aynı** `$line`'ı kullanır → maskeleme bir kez,
kaçışsız yapılır (anayasa S-07).

## 15. Test Senaryoları (sürücü düzeyi — `secrets-dogrulama`)

| # | Senaryo | Beklenen |
|---|---|---|
| S-1 | `.env` olmayan dizinde boot | F-1 `[]` → F-2 exception (sır zorunluysa) durur |
| S-2 | shell'de `DB_PASSWORD=shadow` + `.env` farklı değer | `$_ENV` kazanır (F-8) |
| S-3 | `.env`'de aynı key iki kez | **son** değer (F-1, :36) |
| S-4 | sır boş string | `ServerException` — prod 500 (F-2) |
| S-5 | log'a `Authorization: Bearer eyJhbG...` | `Bearer [REDACTED]` sınıfı maske (Redactor:40) |
| S-6 | log'a `password=short` | `password=[REDACTED]` (F-4) |
| S-7 | CI'a sahte sır commit'i | gitleaks job **fail** (F-5) |
| S-8 | kesik `.env` değeri | ⚠️ V-3 davranışının ölçülmesi |
| S-9 | bypass + pepper tanımsız | ⚠️ Z-2: fallback mi exception mı |
| S-10 | `all(false)` çıktısı | 4 sır alanı `[REDACTED]`, OAuth alanları düz (⚠️ Z-4 gözlemi) |

## 16. İhlal Runbook (sır sızarsa — prosedür önerisi, kanıt değil)

1. **Tespit:** gitleaks job fail (F-5) veya log/`[REDACTED]` kaçışı (V-2) → hangi anahtar?
2. **Kapsam:** hangi dosya/commit → `git log -- <dosya>` (secret-scan `fetch-depth:0` geçmişi verir).
3. **İptal:** sır rotate edilir (DB şifresi / pepper / OAuth secret) — rotasyon saati U-3 (bilinmiyor).
4. **Temizlik:** geçmişten temizleme kararı **onay ister** (rewrite + force-push = yıkıcı işlem).
5. **Kapanış:** desen güncellemesi (Z-5) → ADR/Security onayı (`.gitleaks.toml`
   mevcut — U-1 kapandı 2026-10-15).

> Bu bölüm prosedür **önerisidir**; CoreMusic'te uygulanmış ihlal runbook'u diskte
> **yok** → `UNKNOWN` (U-5 ile ilişkili).

## 17. Boot → CI Zaman Çizelgesi (secrets bakış açısı)

| # | An | Olan | Kanıt |
|---|---|---|---|
| 1 | Subdomain boot | `EnvParser::load(.env)` → `loadIntoEnv` (mevcut `$_ENV` korunur) | EnvParser:7-50 |
| 2 | DI kurulumu | `ConfigManager` `SENSITIVE_KEYS` ile başlar | ConfigManager:10-15 |
| 3 | Sır erişimi | `getSecure` → ya string ya exception (F-2) | :42-49 |
| 4 | İlk log satırı | `FileHandler` → `Redactor::redact` → 1-2 dosya (F-4) | FileHandler:220-235 |
| 5 | Güvenlik olayı | `csrf_invalid`/`securityEvent` → `coremusic_php_security.log` | Csrf:52-55 · FileHandler:233 |
| 6 | Konfigürasyon dump | `all()` → `filterSensitive` `[REDACTED]` (F-3) | :125-154 |
| 7 | Bypass tetiklenirse | günlük HMAC; pepper yoksa ⚠️ fallback (F-7) | index.php:131-136 |
| 8 | `git push` | gitleaks (depth:0) → fail kırmızı (F-5) | secret-scan.yml:7-31 |
| 9 | Sızıntı bulunursa | runbook §16 adımları (öneri) | — |

**Kapanış sorusu (açık U'lar):** rotation saati (U-3) · ACL (U-8) · log retention (U-4)
(U-1) — üçü de bu zaman çizelgesinin 9. adımını yönetir.

## 18. Disk Yeniden Doğrulama Kaydı (2026-10-15 — düzeltme turu)

| # | Komut (repo kökü) | Gözlem | Karar |
|---|---|---|---|
| 1 | glob `**/.gitleaks.toml` — `hidden: false` | **0 isabet** (sahte negatif: nokta-önekli dosya) | arama kuralı ihlali |
| 2 | glob `**/.gitleaks.toml` — `hidden: true` | `.gitleaks.toml` (19 satır) | **VAR** → F-5/V-4/U-1 düzeltildi |
| 3 | grep `SENSITIVE_KEYS` — `*.php` | `ConfigManager:10,70` · `Redactor:31,40` (4 isabet) | iki ayrı liste: config maskesi (S-02) + log maskesi (Z-5) |
| 4 | grep `SESSION_LIFETIME` — `*.php` | auth `constants:47`+`app:22` · api `constants:92`+`app:21` | U-9 kısmi kapandı (config katmanı OK, iletim ⚠️) |
| 5 | glob `.github/workflows/*` — `hidden: true` | `ci.yml` + `secret-scan.yml` (2 dosya) | envanter sabit (registry §25.2 ile aynı) |
| 6 | grep `APP_PEPPER` — `.env.example` | `:41` boş + `REQUIRED` notu | Z-1 kapalı (placeholder, gerçek sır yok) |

**Ders (arama kuralı):** `.gitleaks.toml` gibi nokta-önekli dosyalar `hidden: true`
olmadan **görünmez** — kök `AGENTS.md` §7 adım 2'deki `.ai/` kuralının aynısı.
İlk yazımın "yok" iddiası iki kaynağa dayanmıştı: (a) eskimiş `secret-scan.yml:4`
yorumu, (b) `hidden` aramasız glob — ikisi de **disk ölçümüyle düzeltildi**
(`.gitleaks.toml:1-19` VAR, 2026-10-15).

## 19. Bağlantılar

- [secrets-anayasa](secrets-anayasa.md) — kararlar S-01..S-09, Z-1..Z-6
- [encryption-surucu](../encryption/encryption-surucu.md) — `security.encryption_key` tüketimi
- [session-security-surucu](../session-security/session-security-surucu.md) — `csrf_token` akışı

**BAĞLANTILAR:** [[secrets-kapsam]] · [[secrets-ornekler]] · [[secrets-dogrulama]] ·
[[secrets-bilesenler]] · [[secrets-web-arastirma]] · [[encryption-surucu]] ·
[[session-security-kapsam]] · [[security-kod-ornekleri]]

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
