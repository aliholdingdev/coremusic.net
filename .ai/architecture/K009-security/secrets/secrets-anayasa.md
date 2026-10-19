---
node_id: K009-security/secrets/secrets-anayasa
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on: [K009-security/encryption/encryption-anayasa]
evidence:
  - "shared/src/Config/ConfigManager.php:10-15, 42-71, 125-154 (SENSITIVE_KEYS / getSecure / maskSecret / filterSensitive)"
  - "shared/src/Config/EnvParser.php:7-50 (.env parse + $_ENV yükleme)"
  - "shared/src/Log/Redactor.php:28-56, 65-105 (MASK / 4 desen / redact())"
  - "shared/src/Log/FileHandler.php:220-245 (redaction pipeline + security stream)"
  - ".gitignore:6-9, 69-71"
  - "shared/config/.env.example:5, 17, 27, 41, 45-47, 98-105 (105 satır)"
  - ".github/workflows/secret-scan.yml:1-31"
  - ".gitleaks.toml:1-19 — kök gitleaks config (2026-10-15 disk ölçümü, hidden arama)"
  - "auth.coremusic.net/index.php:131-136 (bypass HMAC fallback)"
  - "auth.coremusic.net/config/constants.php:47 · config/app.php:22 · index.php:107,162 (SESSION_LIFETIME tüketimi)"
validation: "2026-10-15 — 35 satır-kanıt; U-1 kapandı (.gitleaks.toml diskte VAR — §8 düzeltme notu); U-2..U-9 açık; ⚠️ 6 dükkan (maskeleme biçimi §7'de düzeltildi)"
open_questions:
  - "U-1 KAPANDI (2026-10-15): .gitleaks.toml repo kökünde VAR (19 satır, ADR-034 allowlist) — secret-scan.yml:4 yorumu eskimiş; yeni deny-pattern talebi için ADR/Security onayı sürer"
  - "U-2: OAuth *_CLIENT_SECRET anahtarları SENSITIVE_KEYS listesinde değil — config dump'ta görünür mü?"
group: mimari
---

# K009 · secrets · Anayasa

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Mimari Anayasa (SSOT)

Bu belge **secrets** alanının anayasasıdır: sır nerede yaşar, kim okur, nasıl maskelenir,
sızarsa ne olur. Tüm kararlar dosya/satır kanıtına dayanır; kanıtsız iddia yazılmaz
(Zero-Hallucination §3). Sır = şifreleme anahtarı değil, **kimlik bilgisidir** — bu dosya
`encryption` alanının tamamlayıcısıdır (anahtar şifreleme ≠ sır saklama).

## 1. Kapsam

| İçerik | Dışarıda |
|---|---|
| .env / EnvParser / SENSITIVE_KEYS | Sıra dışı: nonce/IV (encryption) |
| Config üzerinden sır okuma (getSecure) | TTL/rotation saati (encryption §7) |
| Log redaction (Redactor + maskSecret) | Raw key pinning (encryption §7) |
| CI secret scan (gitleaks) | `.env` dosyası içeriğini okuyup yazmak — **Guardrail yasağı** |
| Bypass fallback sır sabiti | Şifreli artifact üretimi (encryption) |

## 2. Karar Özeti

| # | Karar | Durum |
|---|---|---|
| S-01 | Sır kaynağı TEK: env/.env; kodda sabit sır yasak | ✅ EnvParser + ConfigManager |
| S-02 | `SENSITIVE_KEYS` merkezi liste = hangi config anahtarı sır sayılır | ✅ ConfigManager.php:10-15 |
| S-03 | Fail-closed: sensitive key boşsa `ServerException::configError` | ✅ ConfigManager.php:42-49 |
| S-04 | Mask: sır ne kısmi (`maskSecret`) ne düz çıkar — `[REDACTED]` iki katman | ✅ :56-66 + :136-154 |
| S-05 | Sır `.md`/`.json`/`.log`'a yazılmaz → `.gitignore` + `Redactor` | ✅ .gitignore:6-9 · Redactor:65-93 |
| S-06 | CI'da gitleaks; repo kökünde `.gitleaks.toml` (useDefault + test-only allowlist) | ✅ `.gitleaks.toml:1-19` (2026-10-15 disk) |
| S-07 | Log write yolu tek nokta: `FileHandler` → `Redactor::redact` zorunlu | ✅ FileHandler.php:224 |
| S-08 | `.env` düz metin (disk); `.env.example` yalnız placeholder | ✅ §4 env envanteri |
| S-09 | Bypass yolunda `APP_PEPPER` yoksa **tahmin edilebilir** fallback | ⚠️ Z-3 |

## 3. Kanıt Tablosu

| # | Dosya | Satır | Kanıt |
|---|---|---|---|
| 1 | `shared/src/Config/ConfigManager.php` | 10-15 | `SENSITIVE_KEYS = ['database.mysql.password','security.app_pepper','security.encryption_key','security.session_key']` |
| 2 | `ConfigManager.php` | 22-40 | `get()`: dot-path traversal + cache; key yoksa `$default` |
| 3 | `ConfigManager.php` | 42-49 | `getSecure()`: `null\|''` → `ServerException::configError($key)` (fail-closed) |
| 4 | `ConfigManager.php` | 51-54 | `getEnv()`: `$_ENV[$key] ?? getenv($key) ?: $default` |
| 5 | `ConfigManager.php` | 56-66 | `maskSecret()`: ilk 3 + `*` + son 3; kısa değer tam `*` |
| 6 | `ConfigManager.php` | 68-71 | `isSensitiveKey()`: `in_array(..., true)` |
| 7 | `ConfigManager.php` | 117-131 | `all($includeSensitive=false)`: hassas hariç döner |
| 8 | `ConfigManager.php` | 136-154 | `filterSensitive()`: recursive; sır → `[REDACTED]` |
| 9 | `shared/src/Config/EnvParser.php` | 9-11 | dosya yok → `[]` (exception değil) |
| 10 | `EnvParser.php` | 19-27 | `#` yorum ve `=` içermeyen satır **sessiz atlanır** |
| 11 | `EnvParser.php` | 36 | parse'da **son satır kazanır** (overwrite) |
| 12 | `EnvParser.php` | 42-50 | `loadIntoEnv()`: mevcut `$_ENV` **kazanır** (`!isset` guard) |
| 13 | `shared/src/Log/Redactor.php` | 28 | `MASK = '[REDACTED]'` (tek maskeleme değeri) |
| 14 | `Redactor.php` | 31 | 11 anahtar: `auth_key\|password\|passwd\|csrf_token\|token\|secret\|api_key\|session_id\|session\|authorization\|cookie` (case-insensitive) |
| 15 | `Redactor.php` | 40 | `KEY_VALUE_PATTERN`: `key=` / `key:` / `"key":"value"` / `Bearer …` |
| 16 | `Redactor.php` | 49 | `LONG_HASH_PATTERN`: 64-char hex/base64 → mask; UUID korunur |
| 17 | `Redactor.php` | 56 | `EMAIL_PATTERN`: `yerel@domain` → `***@domain` (PII) |
| 18 | `Redactor.php` | 65-93 | `redact()`: 3 adımlı zincir; temiz satır aynen döner |
| 19 | `shared/src/Log/FileHandler.php` | 220-224 | satır kurulur → `Redactor::redact($line)` **zorunlu** |
| 20 | `FileHandler.php` | 228, 233-235 | errors.log + güvenlik streami (`coremusic_php_security.log`) |
| 21 | `.gitignore` | 6-9 | `.env`, `.env.local`, `.env.*.local`, `config/.env` ignore |
| 22 | `.gitignore` | 69-71 | `.ai/.env.figma`, `.ai/.env.*`, `!.env.example` istisna |
| 23 | `shared/config/.env.example` | 5 | `# NEVER commit .env files to version control.` |
| 24 | `shared/config/.env.example` | 27 | `DB_PASSWORD=` (boş — gerçek değer yok) |
| 25 | `shared/config/.env.example` | 41 | `APP_PEPPER=` boş + `REQUIRED: random 64-char hex` notu |
| 26 | `shared/config/.env.example` | 45-47 | `BYPASS_USER_UUID/ROLE/USERNAME` test-only değerler |
| 27 | `auth.coremusic.net/config/.env.example` | 33 | `APP_PEPPER=CHANGE_ME_TO_RANDOM_64_HEX_STRING` (placeholder) |
| 28 | `api.coremusic.net/.env.example` | 33 | `API_KEY_PEPPER=CHANGE_ME_TO_RANDOM_64_HEX_STRING` (placeholder) |
| 29 | `.gitleaks.toml` | 1-19 | kök config: `[extend] useDefault = true` + allowlist **yalnız** 2 test yolu (`LoginRequestTest.php` · `shared/tests/Fixtures/jwt/*private.pem` — ADR-034 §5.2/5, ADR-095) — 2026-10-15 disk ölçümü |
| 30 | `secret-scan.yml` | 7-9, 18-26 | `on: push/pull_request` (filtresiz) · job ubuntu-latest · checkout fetch-depth:0 |
| 31 | `secret-scan.yml` | 28-31 | `gitleaks/gitleaks-action@v2` + `GITHUB_TOKEN` |
| 32 | `auth.coremusic.net/index.php` | 131-136 | ⚠️ bypass HMAC: `APP_PEPPER` tanımsızsa `'coremusic-bypass'` fallback |
| 33 | `app-nodejs-projects/nova-search-engine/.env` | diskte | gerçek `.env` var; `.gitignore:6` her seviyede yakalar |
| 34 | `.github/workflows/secret-scan.yml` | 4 | yorum "diskte .gitleaks.toml YOK" — **GÜNCEL DEĞİL** (diskte VAR): eskimiş yorum, disk ölçümü kazanır |
| 35 | `auth.coremusic.net/config/constants.php` · `config/app.php` | 47 · 22 | `SESSION_LIFETIME` env → define → `session.lifetime` config anahtarı (api: 92 · 21 aynı kalıp) |

## 4. Sır Yaşam Alanları (Env Envanteri)

### 4.1 `.env.example` şablonları (git'e girer — değer içermez)

| Dosya | Öne çıkan satırlar | Sır niteliğindeki alan |
|---|---|---|
| `shared/config/.env.example` (105 satır) | :27 `DB_PASSWORD=` · :41 `APP_PEPPER=` · :59-96 10 platform `OAUTH_*_SECRET=` · :98-105 JWT yolları | hepsi **boş** |
| `auth.coremusic.net/config/.env.example` | :33 `APP_PEPPER=CHANGE_ME_…` | placeholder |
| `api.coremusic.net/.env.example` | :33 `API_KEY_PEPPER=CHANGE_ME_…` | placeholder |
| `nova-search-engine/.env.example` | node servis şablonu | placeholder |

**Karar S-08a:** şablonlarda gerçek sır **yok** (boş değer veya `CHANGE_ME_…`) —
`.env.example` `!.env.example` (:71) ile bilinçli olarak git'e girer.

### 4.2 Gerçek `.env` (git'e girmez)

| Kural | Kanıt |
|---|---|
| her seviyede `.env` ignore | `.gitignore:6` (dizin-bağımsız desen) |
| `config/.env` ayrıca | `.gitignore:9` |
| `.ai` envleri de | `.gitignore:69-70` |
| istisna: yalnız `.env.example` | `.gitignore:71` |
| diskte gerçek `.env` örneği | `nova-search-engine/.env` (ignore ✅) |

## 5. Sır Yaşam Döngüsü (Karar Ağacı)

```
Sır ihtiyacı doğar
  → KODDA sabit sır var mı?            EVET → yasak (§11.1); .env'e taşı
  → Hayır
  → Config anahtarı SENSITIVE_KEYS'te mi?     ConfigManager.php:10-15
      EVET → getSecure(key)  (:42-49)
              → config'te null/'' ?
                  EVET → ServerException::configError  ← fail-closed, kod çalışmaz
                  HAYIR → string döner; log'a asla girmez
      HAYIR → get(key, default) (:22-40) — normal ayar (DB_HOST, PORT…)
  → Tüm config dump mı gerekiyor?  → all() (:125-131)
      → filterSensitive (:136-154) → sır alanı '[REDACTED]' recursive
  → Değer log'a mı gidecek?
      → Redactor::redact (:65-93) → '[REDACTED]' / '***@domain'
  → Değer git'e mi gidecek?  → .gitignore:6-9 engeller
```

**Fail-closed çekirdeği (ConfigManager.php:42-49, paraphrase):**

```php
public function getSecure(string $key): string
{
    $value = $this->get($key);
    if ($value === null || $value === '') {
        throw ServerException::configError($key);   // fallback değer YOK
    }
    return (string)$value;
}
```

**Kural S-03b:** `getSecure` çağrısını `try/catch` ile yutup default dönmek **yasak** —
aksi halde fail-closed kâğıt üstünde kalır (§11.5).

## 6. ENV Okuma Zinciri (Paradigma)

```
disk/.env
   │  EnvParser::load()            EnvParser.php:7-40
   │    · dosya yok → []           :9-11
   │    · '#'/ '=' yok → atla      :19-26   ⚠️ sessiz atlama (U-6)
   │    · son satır kazanır        :36
   │  EnvParser::loadIntoEnv()     :42-50
   │    · mevcut $_ENV KAZANIR     :46-48   (dış ortam > dosya)
   ▼
$_ENV / getenv()
   │  ConfigManager::getEnv()      :51-54
   ▼
┌──────────────────────┴──────────────────────┐
get(key)  (:22-40)                 getSecure(key)  (:42-49)
 normal ayar                          SENSITIVE_KEYS üyesi
   │                                     │ boş → exception
   ▼                                     ▼
config array ── all(false) ──▶ filterSensitive → '[REDACTED]'  (:136-154)
                                        │
                                        ▼
                              DI container → kod (log değil)
```

**İki yön tek kural:** sır **giriş** yönünde yalnız env'den okunur; **çıkış** yönünde
(kod → log/disk/git) üç engel vardır: `filterSensitive`, `Redactor::redact`,
`.gitignore`.

## 7. Mask & Redaction Katmanları (§ düzeltmesi — 2026-10-15)

> **⚠️ Düzeltme kaydı:** ilk taslakta maske değeri `************` ve maske yalnız
> `maskSecret` sanılıyordu — **yanlış**. Gerçek: tek maske değeri `[REDACTED]`
> (`Redactor.php:28`), dört desen zinciri (`redact():65-93`) + config tarafında iki
> ayrı mekanizma (`maskSecret` kısmi, `filterSensitive` `[REDACTED]`).

| Katman | Dosya:Satır | Kapsam | Çıktı biçimi |
|---|---|---|---|
| Config partial mask | `ConfigManager.php:56-66` | tek sır değerini gösterirken | `abc***xyz` (3+3 görünür) |
| Config dump mask | `ConfigManager.php:136-154` | `all()` sızdırmaz dump | `[REDACTED]` |
| Log key=value mask | `Redactor.php:31,40` | 11 hassas anahtar adı | `password=[REDACTED]` |
| Log hash mask | `Redactor.php:49` | 64-char hex/base64 | `[REDACTED]` (UUID korunur) |
| Log PII mask | `Redactor.php:56` | e-posta yerel kısmı | `***@domain` |
| Pipeline zorunluluğu | `FileHandler.php:224` | tüm log satırları | `Redactor::redact` kaçışsız |

**Karar S-07:** log yazımı yalnız `FileHandler` üzerinden; header'da "tek maskeleme
noktası" tanımı var (`Redactor.php:10-11`). ⚠️ **U-7:** ikinci bir write yolu
(örn. `StructuredLogger` farklı dosyaya mı yazar) bu belge kapsamında **doğrulanmadı**.

**⚠️ U-2 (analitik bulgu):** `Redactor` deseni `\bsecret\b` kullanır; anahtar adı
`OAUTH_PINTEREST_CLIENT_SECRET` gibi **alt çizgi-`SECRET`** bitişinde `\b` eşleşmez
(alt çizgi kelime karakteridir) → `client_secret=deger` satırı **key-name maskesine
girmeyebilir**, yalnız 64-char hash desenine düşer (kısa secret kalır).
`VERIFICATION REQUIRED` — test `secrets-dogrulama`'ya yazıldı.

## 8. Sızıntı Taraması (CI)

```
push / pull_request          secret-scan.yml:7-9  (branch filtresi YOK)
  → permissions: contents:read           :11-12
  → concurrency: ref başına 1, iptal     :14-16
  → job gitleaks: ubuntu-latest, 10 dk   :18-22
  → actions/checkout@v4 fetch-depth:0    :24-26  ← geçmiş commit'ler de taranır
  → gitleaks/gitleaks-action@v2          :28-31  ← kökteki .gitleaks.toml'u kullanır (dosya beyanı)
```

**U-1 → KAPANDI (2026-10-15 disk ölçümü):** `.gitleaks.toml` repo kökünde **VAR**
(19 satır: `[extend] useDefault = true` + allowlist yalnızca `LoginRequestTest.php` ve
`shared/tests/Fixtures/jwt/*private.pem` — ADR-034 §5.2/5 · ADR-095). **İlk yazım** bu
belgeyi `secret-scan.yml:4` yorumuna dayanarak "yok" yazmıştı — **yorum disk gerçeğiyle
çelişiyor (stale); disk kazanır** (kök `AGENTS.md` §3 Zero-Hallucination ·
`.github/AGENTS.md` kural 6). Nokta-önekli dosya araması `hidden: true` olmadan
0 isabet verir (`.ai/` kuralının aynısı) — bu yüzden önceki turda görünmedi.
Açık kalan: yeni deny-pattern (CoreMusic önekli sırlar) talebi → `.github/AGENTS.md`
§3.1 gereği **ADR/Security onayı** ister.
⚠️ VERIFICATION REQUIRED: action'ın config'i otomatik yüklediği iddiası dosyanın
kendi beyanına dayanır (`.gitleaks.toml:2`); bu oturumda CI çalıştırılmadı —
`.gitleaks.toml`'ın git takip durumu da UNKNOWN (shell izni yok).

## 9. Zayıflıklar (Z)

| # | Bulgu | Kanıt | ⚠️ |
|---|---|---|---|
| Z-1 | `.env.example` sır içermez (boş/`CHANGE_ME`) — **kapatıldı, gerçek sır yok** | `.env.example:27,41` · `auth:33` · `api:33` | ✅ |
| Z-2 | Bypass HMAC fallback sabiti: `APP_PEPPER` tanımsızsa `'coremusic-bypass'` → günlük `auth_key` tahmin edilebilir | `auth index.php:131-136` | ⚠️ VERIFICATION REQUIRED |
| Z-3 | **DÜZELTİLDİ (2026-10-15):** `.gitleaks.toml` repo kökünde **VAR** (ilk yazım `secret-scan.yml:4` eskimiş yorumuna dayanmıştı) — kalan bulgu yalnız yorum temizliği | `.gitleaks.toml:1-19` · `secret-scan.yml:4` | ⚠️ yorum düzeltmesi DevOps/ADR |
| Z-4 | `SENSITIVE_KEYS` yalnız 4 anahtar; `OAUTH_*_CLIENT_SECRET` / JWT yolları listede **değil** → `all()` dump'ta görünür olabilir | `ConfigManager.php:10-15` | ⚠️ VERIFICATION REQUIRED |
| Z-5 | `Redactor` `*_SECRET` anahtar-adı desenine girmez (U-2 regex analizi) | `Redactor.php:31,40` | ⚠️ VERIFICATION REQUIRED |
| Z-6 | `.env` düz metin; parse hataları sessiz atlanır (yarım değer → fail-closed'u atlayabilir) | `EnvParser.php:19-26` | ⚠️ U-6 |

**Z-2 bağlamı (dürüst değerlendirme):** fallback yalnız `if ($bypassActive)` dalında
(`index.php:131`) ve bypass'ın prod'da kapatıldığı ADR-008 notu `.env.example:17`'de
kayıtlı → **prod etkisi düşük, dev/stage etkisi tahmin edilebilir günlük imza**.

## 10. Bilinmeyenler (U)

| # | Soru | Durum |
|---|---|---|
| U-1 | ✅ **kapandı (2026-10-15):** `.gitleaks.toml:1-19` diskte VAR; yeni deny-pattern talebi için ADR/Security onayı sürer | kapatıldı |
| U-2 | `*_CLIENT_SECRET` log/dump sızıntısı test edildi mi? | ⚠️ doğrulama gerekli |
| U-3 | Sır rotation saati? | UNKNOWN |
| U-4 | Log retention süresi (maskelenmiş log ne kadar saklanır)? | UNKNOWN |
| U-5 | Geçmişte sızan secret için revoke/rotate kaydı? | UNKNOWN |
| U-6 | `.env` parse hata modu davranış testi (kesik satır)? | ⚠️ doğrulama gerekli |
| U-7 | `FileHandler` dışında ikinci log write yolu var mı? | UNKNOWN |
| U-8 | Prod `.env` dağıtım yolu (secrets manager vs manuel)? | UNKNOWN |
| U-9 | `SESSION_LIFETIME` okunuyor mu? → **EVET (config katmanı)**: auth `constants.php:47` + `app.php:22` · api `constants.php:92` + `app.php:21`; ancak auth session'ı `SessionBootstrapper::ensureStarted()` **parametresiz** çağırıyor (`auth index.php:107,162`) → cookie/gc'ye iletimi | ⚠️ VERIFICATION REQUIRED (kısmi kapandı) |

## 11. Yasaklar (Fail-Closed)

1. Kodda `const`/`define` ile **sır değeri** — her istisnada yasak (Z-2 işaretli ihlal, düzeltilecek).
2. Sır değeri `.md` / `.json` / `.log` dosyasına — yasak.
3. `.env` dosyasını git'e eklemek — yasak (`.gitignore:6-9`); `.env.example` içine gerçek değer — yasak.
4. `getSecure` exception'ını yutup fallback dönmek — yasak (S-03b).
5. `all(true)` (hassas dump) çıktısını log'a/yana yazmak — yasak; yalnız debug amaçlı, geçici.

## 12. Tehdit Modeli

| Tehdit | Savunma | Kanıt | Kalan boşluk |
|---|---|---|---|
| Kod içi sır sızıntısı (repo) | `.gitignore:6-9` + yalnız şablon commit | .gitignore · .env.example | ✅ kapalı |
| Log sızıntısı | `Redactor::redact` tek nokta + FileHandler:224 zorunlu | Redactor:65-93 | Z-5 (`*_SECRET` regex) |
| Config dump sızıntısı | `all()` → `filterSensitive` → `[REDACTED]` | ConfigManager:125-154 | Z-4 (4 anahtar sınırı) |
| `.env` disk erişimi | düz metin — dosya izinleri | EnvParser:7-40 | Z-6 + U-8 (ACL doğrulanmadı) |
| Geçmiş commit'e sızmış sır | gitleaks + `fetch-depth:0` + kök `.gitleaks.toml` (allowlist yalnız test dosyaları) | secret-scan.yml:24-26 · .gitleaks.toml:11-18 | allowlist dışı yeni desen → ADR onayı |
| Bypass imzası tahmini | `APP_PEPPER` HMAC | index.php:132 | Z-3 (fallback sabit) |
| `.env` bozuk satır → yanlış sır | parse sessiz atlama | EnvParser:19-26 | U-6 (davranış testi yok) |
| Prod'a eksik sır ile yayına çıkma | `getSecure` fail-closed | ConfigManager:42-49 | — (✅) |

## 13. Kural Kaynakları (hangi kural nereden geliyor)

| Kural | Kaynak (vault/CI) |
|---|---|
| Hassas log → `[REDACTED]` | `.ai/AGENTS.md` §17 Edge Case 3 (Redactor:8 bu kuralı kaynak gösterir) |
| MD5/SHA-1/DES yasak | `.ai/CLAUDE.md` §Security (bkz. [[encryption-anayasa]] §3) |
| Secret `.md`/`.log`/`.json`'a yazılmaz | `.github/AGENTS.md` §3.3 yasak 2 |
| `.env` okunup vault'a/chat'e yazılmaz | `api.coremusic.net/AGENTS.md` kural 3 · `auth.coremusic.net/AGENTS.md` yasak 1 |
| Session/cookie değeri log'a yazılmaz | `auth.coremusic.net/AGENTS.md` yasak 3 |
| `.gitleaks.toml` eklemek onay ister | `.github/AGENTS.md` §3.1 (secret-scan satırı) |
| Doğrulanamayan iddia `VERIFICATION REQUIRED` | kök `AGENTS.md` §3 Zero-Hallucination |

## 14. Doğrulama Sorusu Seti (her kod incelemesinde)

| # | Soru | Evet ise | Hayır ise |
|---|---|---|---|
| 1 | Kodda yeni literal sır var mı? | §11.1 ihlali → RED | devam |
| 2 | Yeni config anahtarı sır mı? | `SENSITIVE_KEYS`'e ekle (§5) | normal `get()` |
| 3 | Yeni log alanı sır/PII taşıyor mu? | Redactor desenine ekle | devam |
| 4 | `getSecure` try/catch ile yutuluyor mu? | §11.4 ihlali → BLOCK | devam |
| 5 | `.env.example`'a gerçek değer yazıldı mı? | §11.3 ihlali → RED | devam |
| 6 | Yeni workflow secret ekliyor mu? | onay kapısı (.github AGENTS §4.2) | devam |
| 7 | `all(true)` çıktısı bir yere yazılıyor mu? | §11.5 ihlali → RED | devam |
| 8 | OAuth/JWT yeni secret alanı eklendi mi? | Z-4 listesini güncelle | devam |
| 9 | Bypass/ fallback sabiti eklendi mi? | fail-closed'a çevir | devam |
| 10 | Parse hatası artık sessiz mi? | U-6 testi zorunlu | devam |

## 15. Test Gereksinimleri (`secrets-dogrulama` için)

| # | Test | Kriter |
|---|---|---|
| T-1 | `getSecure` eksik anahtar | `ServerException::configError` — fallback yok |
| T-2 | `all()` dump | 4 sır alanı `[REDACTED]`, derinlik recursive |
| T-3 | `maskSecret('abcdefghij')` | `abc***hij` (3+3), uzunluk korunur |
| T-4 | `maskSecret('ab')` | `**` (tam star, :59-61) |
| T-5 | log satırında `password=x` | `password=[REDACTED]` (FileHandler çıkışında) |
| T-6 | log satırında 64-char hex | `[REDACTED]`; UUID `8-4-4-4-12` **korunur** |
| T-7 | `user@example.com` logda | `***@example.com` |
| T-8 | `client_secret=abc123` (kısa) | ⚠️ V-2/Z-5: beklenen maske **test edilecek** |
| T-9 | kesik `.env` satırı (`KEY="yarım`) | ⚠️ V-3: getSecure davranışının ölçülmesi |
| T-10 | bypass dalı + `APP_PEPPER` tanımsız | Z-2: fallback yerine exception mı? |

## 16. Env Envanteri Ayrıntısı

### 16.1 OAuth secret alanları (`shared/config/.env.example:59-96`)

| Platform | Satırlar | Alanlar |
|---|---|---|
| Pinterest · Instagram · TikTok · Snapchat | :59-72 | `OAUTH_*_CLIENT_ID` / `_CLIENT_SECRET` (boş) |
| YouTube · Discord · Reddit · X | :74-88 | aynı ikili (boş) |
| LinkedIn · Facebook | :90-96 | aynı ikili (boş) |

**Not (Z-4):** bu alanlar `SENSITIVE_KEYS`'te değil → `all()` düz döndürür.

### 16.2 JWT alanları (`shared/config/.env.example:98-105`)

| Satır | Alan | Not |
|---|---|---|
| :100 | `# private.pem .gitignore dadir; openssl CLI ile uretilir` | üretim talimatı yorumda |
| :101-102 | `JWT_PRIVATE_KEY_PATH=` / `JWT_PUBLIC_KEY_PATH=` | boş → default yola düşer |
| :103-105 | `JWT_ISSUER` / `JWT_AUDIENCE` / `JWT_TTL_SECONDS=3600` | sır değil |

### 16.3 Bypass test alanları (`shared/config/.env.example:44-47`)

| Satır | Alan | Not |
|---|---|---|
| :17 | `FORCE_AUTH_BYPASS=false` | ADR-008 kill: `env=production => bypass always off` |
| :45-47 | `BYPASS_USER_UUID` / `_ROLE` / `_USERNAME` | test-only; sır sınıfı değil ama prod'da **yasak** |

## 17. İlişkiler

```
K009-security/encryption/encryption-anayasa ──(anahtarı korur)──▶ K009-security/secrets/secrets-anayasa ──(sırı korur)──▶ auth/api
        ▲                                            │
        └──── K009-security/session-security/session-security-anayasa (csrf_token, session verisi) ◀────────┘
```

**BAĞLANTILAR:** [[secrets-kapsam]] · [[secrets-ornekler]] · [[secrets-dogrulama]] ·
[[secrets-bilesenler]] · [[secrets-web-arastirma]] · [[encryption-anayasa]] ·
[[session-security-kapsam]] · [[security-master]]

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
