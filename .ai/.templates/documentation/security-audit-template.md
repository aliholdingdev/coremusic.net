---
title: "CoreMusic — Security Audit Template"
type: template
category: template
date: 2026-09-06
updated: 2026-09-23
version: 2.0.0
status: active
authority: reference
---

# CoreMusic — Security Audit Template

**Zorunlu Bağlantılar:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]]

---

## §1 Amaç

CoreMusic güvenlik denetim raporunu standartlaştırmaktır: OWASP Top 10:2025 kontrol listesi, middleware pipeline doğrulaması, güvenlik parametreleri, sensitive data kontrolü, tespit edilen açık listesi ve sonuç formatını tek iskelette toplar. Kaynak: `reference_doc: Freelancer Technical Documentation v1.0`. **Guardrail #16:** yeni denetim raporu bu şablondan başlar; şablonsuz rapor kapatılamaz.

| Alan | Değer |
|------|-------|
| Template Name | `security-audit-template.md` |
| Template Path | `.ai/.templates/documentation/security-audit-template.md` |
| Hedef Dosya Tipi | Markdown güvenlik denetim raporu |
| Guardrail | #16 (Template Mandatory) |
| Birincil Yazar | Security Engineer |
| İkincil Yazarlar | QA Engineer (doğrulama), Backend Architect (middleware implementasyonu) |
| Kapsam Standardı | OWASP Top 10:2025 (A01–A10) |
| Middleware Sayısı | 10 (PSR-15 pipeline — sıra 1-10 değişmez) |
| Güvenlik Parametresi Sayısı | 8 (CSRF, CSP, HSTS, Argon2id ×2, AES-IV, Session, Rate Limit) |
| Sensitive Data Kontrolü | 5 madde |
| Kanıt — Middleware | `shared/src/Middleware/` (PSR-15 — glob kanıtı) |
| Kanıt — Kod | `shared/src/**` (PageRouter, Bff, Api, Database) |
| Kripto Standartları | AES-256-GCM (96-bit IV), Argon2id (64MB, 4 iterasyon) — ADR-022 |
| Oturum Standardı | `COREMUSIC_SESS`, 3600s idle timeout — ADR-011 |
| Rate Limit | 60 req/60s (APCu) — ADR-013 |
| CSRF Anahtarı | `csrf_token` — ADR-010 |
| CSP | strict-dynamic, nonce-based — ADR-012 |
| Severity Değerleri | `CRITICAL` / `HIGH` / `MEDIUM` / `LOW` |
| Sonuç Üçlüsü | ✅ GÜVENLİ / ⚠️ İYİLEŞTİRME GEREKLİ / ❌ KRİTİK AÇIK |
| Korunan İskelet | H1 + §1 Amaç → §7 Referanslar; OWASP/Middleware/Sensitive listeleri birebir korunur |
| Değişken Formatı | `{{VARIABLE}}` (TITLE, DATE, AUTHOR, CSRF_KEY, CSP_VALUE, HSTS_VALUE, ARGON2_MEMORY, ARGON2_TIME, AES_IV, SESSION_TIMEOUT, RATE_LIMIT, FINDING_*, FILE_*, FIX_*, RECOMMENDATION_*) |
| Dil | Türkçe (ç ğ ı İ ö ş ü doğru; mojibake YASAK); kod/dosya adı İngilizce |
| Versiyon | 2.0.0 (Vault Refactor Engine yeniden yazımı) |
| Authority | SSOT (bu dosya); üretilen rapor kendi authority değerini taşır |
| Governance | Red Team · Human Mode · Truth Mode |
| Kayıt Defteri | `.ai/.templates/index.md` (envanter SRP — bu şablonda tekrarlanmaz) |
| Son Kontrol | 2026-09-23 |

### §1.1 Denetim Neden "Okuma" İşlemidir?

Güvenlik denetimi, kodu **değil** kodun güvenlik posture'unu değerlendirir. Denetçi bulgu üretir; düzeltmeyi domain sahibi agent yapar. Bu ayrım denetimin tarafsızlığını korur ve denetim–düzeltme çakışmasını önler.

| Aşama | Kim | Ne Yapar | Ne Yapmaz |
|-------|-----|----------|-----------|
| Denetleme | Security Engineer | §3.1-§4.3 doldurur, bulgu listesi çıkarır | Kod değiştirmez |
| Sınıflandırma | Security Engineer | Severity atar (CRITICAL…LOW) | Priorite karar vermez (L2) |
| Eskalasyon | Security Engineer | CRITICAL/HIGH → `[[../../AGENTS.md]]` §10 | Kendi başına fix etmez |
| Düzeltme | İlgili domain agent'ı | Öneriyi uygular | Denetimi kendisi kapatmaz |
| Yeniden denetim | Security Engineer | Kapatılan bulguyu teyit eder | Kapanış onayını vermez (L2) |

### §1.2 Beklenen Değerlerin Kaynağı

§3.1'deki "Beklenen Değer" sütunu tahmin değildir; her birinin vault içinde bir kaynağı vardır.

| Parametre | Beklenen Değer | Kaynak |
|-----------|---------------|--------|
| CSRF Token Key | `csrf_token` | ADR-010 (`[[../../brain.md]]` özeti) |
| CSP | strict-dynamic, nonce | ADR-012 |
| HSTS | max-age=31536000 | `shared/src/Middleware/` SecurityHeaders |
| Argon2id Memory | 64MB | ADR-022 |
| Argon2id Time | 4 iterations | ADR-022 |
| AES-256-GCM IV | 96-bit (12 byte) | ADR-022 |
| Session Timeout | 3600s | ADR-011 |
| Rate Limit | 60 req/60s | ADR-013 (APCu) |
| Session Cookie Name | `COREMUSIC_SESS` | ADR-011 |
| Session Cookie HttpOnly | true | ADR-011 |
| Session Cookie Secure | true | ADR-011 |
| Password Hashing | Argon2id | ADR-022 |

### §1.3 Denetim Sıklığı ve Tetikleyicileri

| Tetikleyici | Zorunlu mu? | Öncelik | Not |
|-------------|-------------|---------|-----|
| Dönemsel denetim | Evet | MEDIUM | `§3.4` "Sonraki Denetim" alanı |
| Middleware sırası değişikliği | Evet | HIGH | §4.2 yeniden işaretlenir |
| Yeni auth/oturum kodu | Evet | HIGH | §3.1 + §4.1 A07 |
| CRITICAL/HIGH bulgu kapanışı | Evet | HIGH | Yeniden denetim zorunlu |
| Yeni bağımlılık eklendi | Evet | MEDIUM | §4.1 A06 (`composer audit`) |
| Güvenlik olayı (incident) | Evet | CRITICAL | Tam denetim + eskalasyon |

---

## §2 Kapsam

Denetim raporunun kapsadığı ve kapsam dışında bıraktığı alanlar. Denetim **okuma** işlemidir: kod değiştirmez, bulgu üretir.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Güvenlik denetim raporu (Markdown) | Uygulama kodu değişikliği (→ ilgili domain agent'ı) |
| OWASP Top 10:2025 + middleware pipeline denetimi | Performans testi (→ `[[../testing/phpunit-template]]`, QA) |
| Kripto / session / CSRF / CSP parametre doğrulaması | Donanım denetimi (→ `[[../hardware/hardware-template]]`) |
| Sensitive data kontrolü (5 madde) | Veritabanı şeması denetimi (→ Data Engineer, `.ai/.sql/mysql/*.sql`) |
| Tespit edilen açık listesi (severity + dosya + öneri) | CI/CD pipeline denetimi (→ `[[../infrastructure/github-actions-template]]`) |
| Genel sonuç + öneriler (3'lü format) | Envanter listesi (→ `[[.templates/index]]`, SRP) |
| `{{PLACEHOLDER}}` doldurma + Guardrail #16 doğrulaması | `.ai/log.md` append-only kayıt (üst görevin işi) |
| Türkçe doğruluk + mojibake denetimi | Secret/credential yazımı (REDACTED — yasak) |

**Dosya tipi:** Markdown güvenlik denetim raporu · **Uzantı:** `.md` · **Guardrail:** #16 · **Kural:** rapor = okuma, kod değişikliği yok

### §2.1 Denetim Kapsamı → Kanıt Kaynağı

Her kapsam maddesinin bir kanıt kaynağı vardır; kaynak yoksa o madde `⚠️ VERIFICATION REQUIRED` ile işaretlenir.

| Kapsam Maddesi | Kanıt Kaynağı | Doğrulama Yöntemi |
|----------------|---------------|-------------------|
| Middleware sırası (§4.2) | `shared/src/Middleware/` | Sıra okuma + glob |
| CSRF anahtarı (§3.1) | Kod + ADR-010 | `csrf_token` arama |
| Kripto parametreleri (§3.1) | Kod + ADR-022 | Argon2id/AES yapılandırma |
| Oturum (§3.1) | Kod + ADR-011 | Cookie adı + timeout |
| Rate limit (§3.1) | Kod + ADR-013 | 60/60s + APCu |
| Sensitive data (§4.3) | Kod + `.gitignore` + log | Grep + manuel okuma |
| OWASP A06 | `composer audit` | Komut çıktısı |

### §2.2 Kapsam Dışı İşlemlerin Gittiği Yer

| Kapsam Dışı | Gideceği Yer |
|-------------|--------------|
| Bulgu fix'i (kod değişikliği) | İlgili domain agent'ı (§1.1) |
| Performans testi | QA Engineer → `[[../testing/phpunit-template]]` |
| Donanım denetimi | `[[../hardware/hardware-template]]` |
| DB şema denetimi | Data Engineer → `.ai/.sql/mysql/*.sql` |
| CI/CD denetimi | `[[../infrastructure/github-actions-template]]` |
| API contract denetimi | `[[./api-doc-template]]` |
| Envanter/registry | `[[.templates/index]]` (SRP) |

---

## §3 Mimari

Rapor iskeleti. Gömme nedeniyle başlıklar iki seviye derinleştirilmiştir (H1 → `###`, H2 → `####`); placeholder ve tablolar birebir korunmuştur. Denetim kontrol listeleri (OWASP, Middleware, Sensitive Data) §4.1-§4.3'tedir.

### {{TITLE}}

**Denetim Tarihi:** {{DATE}}
**Denetçi:** {{AUTHOR}}
**Kapsam:** OWASP Top 10:2025

---

#### §3.1 Güvenlik Parametreleri

| Parametre | Beklenen Değer | Gerçek Değer | Durum |
|-----------|---------------|-------------|-------|
| CSRF Token Key | `csrf_token` | {{CSRF_KEY}} | ✅/❌ |
| CSP | strict-dynamic, nonce | {{CSP_VALUE}} | ✅/❌ |
| HSTS | max-age=31536000 | {{HSTS_VALUE}} | ✅/❌ |
| Argon2id Memory | 64MB | {{ARGON2_MEMORY}} | ✅/❌ |
| Argon2id Time | 4 iterations | {{ARGON2_TIME}} | ✅/❌ |
| AES-256-GCM IV | 96-bit (12 byte) | {{AES_IV}} | ✅/❌ |
| Session Timeout | 3600s | {{SESSION_TIMEOUT}} | ✅/❌ |
| Rate Limit | 60 req/60s | {{RATE_LIMIT}} | ✅/❌ |
| Session Cookie Name | `COREMUSIC_SESS` | {{SESSION_NAME}} | ✅/❌ |
| Session Cookie HttpOnly | true | {{SESSION_HTTPONLY}} | ✅/❌ |
| Session Cookie Secure | true | {{SESSION_SECURE}} | ✅/❌ |
| Password Hashing | Argon2id | {{PASSWORD_HASH}} | ✅/❌ |

---

#### §3.2 Tespit Edilen Açık Listesi

| # | Açıklama | Severity | Dosya | Öneri |
|---|----------|----------|-------|-------|
| 1 | {{FINDING_1}} | CRITICAL/HIGH/MEDIUM/LOW | {{FILE_1}} | {{FIX_1}} |
| 2 | {{FINDING_2}} | CRITICAL/HIGH/MEDIUM/LOW | {{FILE_2}} | {{FIX_2}} |
| 3 | {{FINDING_3}} | CRITICAL/HIGH/MEDIUM/LOW | {{FILE_3}} | {{FIX_3}} |
| 4 | {{FINDING_4}} | CRITICAL/HIGH/MEDIUM/LOW | {{FILE_4}} | {{FIX_4}} |

---

#### §3.3 Bulgu Detayları

**Bulgu #1 — {{FINDING_1}}**

| Alan | Değer |
|------|-------|
| Severity | {{SEVERITY_1}} |
| OWASP Kategorisi | {{OWASP_CATEGORY_1}} |
| Dosya | {{FILE_1}} |
| Kanıt | {{EVIDENCE_1}} |
| Etki | {{IMPACT_1}} |
| Öneri | {{FIX_1}} |
| Sorumlu Agent | {{OWNER_1}} |
| Durum | OPEN / IN_PROGRESS / FIXED |

---

#### §3.4 Sonuç

**Genel Durum:** ✅ GÜVENLİ / ⚠️ İYİLEŞTİRME GEREKLİ / ❌ KRİTİK AÇIK

**Öneriler:**

1. {{RECOMMENDATION_1}}
2. {{RECOMMENDATION_2}}
3. {{RECOMMENDATION_3}}

| Metrik | Değer |
|--------|-------|
| Toplam Bulgu | {{TOTAL_FINDINGS}} |
| CRITICAL | {{CRITICAL_COUNT}} |
| HIGH | {{HIGH_COUNT}} |
| MEDIUM | {{MEDIUM_COUNT}} |
| LOW | {{LOW_COUNT}} |
| OWASP Geçme Oranı | {{OWASP_PASS_RATE}} |
| Parametre Uyum Oranı | {{PARAM_PASS_RATE}} |
| Middleware Sıra Uyumu | {{MIDDLEWARE_ORDER_STATUS}} |
| Denetim Süresi | {{AUDIT_DURATION}} |
| Sonraki Denetim | {{NEXT_AUDIT_DATE}} |

---

## §4 Kurallar

Denetim sırasında uygulanan zorunlu/yasak kurallar. OWASP, Middleware ve Sensitive Data listeleri §4.1-§4.3'te birebir korunur.

| # | Kural | Tür | İhlal Sonucu |
|---|-------|-----|--------------|
| 1 | Kapsam her denetimde OWASP Top 10:2025'tir; her satır ✅/⚠️/❌ ile işaretlenir (§4.1) | Zorunlu | Eksik denetim |
| 2 | Middleware sırası §4.2'deki 1-10 düzeni değişmez; ihlal → derhal revert + log ERROR (AGENTS.md §5, §17) | Zorunlu | CSP/CSRF bozulması |
| 3 | Güvenlik parametreleri §3.1'deki *Beklenen Değer* sütununa birebir eşleşmelidir | Zorunlu | Kripto zayıflığı |
| 4 | Açık severity değerleri `CRITICAL/HIGH/MEDIUM/LOW` ile yazılır | Zorunlu | Öncelik belirsizliği |
| 5 | Sonuç üçlüsü ✅/⚠️/❌ kullanılır (§3.4) | Zorunlu | Kararsız sonuç |
| 6 | Hardcoded secret, `.env` commit'i, log'da sensitive data, error message'da stack trace YASAK (§4.3) | Yasak | Veri sızıntısı |
| 7 | `{{DATE}}`, `{{AUTHOR}}`, `{{CSRF_KEY}}` … `{{RECOMMENDATION_*}}` placeholder'ları doldurulmadan rapor kapatılamaz | Yasak | Yarım rapor |
| 8 | Doğrulanamayan gerçek değer `⚠️ VERIFICATION REQUIRED` ile işaretlenir; tahmin yazmak yasaktır | Zorunlu | Hallucination |
| 9 | Rapor bir **okuma** işlemidir — denetim sırasında kod değiştirilmez | Yasak | Denetim tarafsızlığı kaybı |
| 10 | Secret / credential / token rapora yazılmaz; kanıt `[REDACTED]` ile maskele | Yasak | REDACTED ihlali |
| 11 | CRITICAL/HIGH bulgu varsa eskalasyon zorunlu (`[[../../AGENTS.md]]` §10) | Zorunlu | Açık kalır |
| 12 | Frontmatter 7 zorunlu alan eksiksiz yazılır | Zorunlu | Frontmatter hatası |
| 13 | Wiki-link formatı `[[relative/path/to/file]]` | Zorunlu | Kırık çapraz referans |
| 14 | Türkçe karakterler (ç ğ ı İ ö ş ü) doğru; mojibake YASAK; kod adı İngilizce | Zorunlu | Mojibake → onarım |
| 15 | Envanter listesi bu şablonda tekrarlanmaz (SRP) → `[[.templates/index]]` | Yasak | İkincil kaynak çelişkisi |
| 16 | Doğrulanamayan middleware/yol adı uydurulmaz (kanıt: `shared/src/Middleware/`) | Zorunlu | YAGNI ihlali |

### §4.1 OWASP Top 10:2025 Kontrol Listesi

| # | Risk | Durum | Not |
|---|------|-------|-----|
| A01 | Broken Access Control | ✅/⚠️/❌ | RBAC uygulanmış |
| A02 | Cryptographic Failures | ✅/⚠️/❌ | AES-256-GCM + Argon2id |
| A03 | Injection | ✅/⚠️/❌ | Prepared statement |
| A04 | Insecure Design | ✅/⚠️/❌ | Threat modeling yapılmış |
| A05 | Security Misconfiguration | ✅/⚠️/❌ | CSP nonce-based |
| A06 | Vulnerable Components | ✅/⚠️/❌ | `composer audit` |
| A07 | Auth Failures | ✅/⚠️/❌ | JWT + Session hybrid |
| A08 | Data Integrity Failures | ✅/⚠️/❌ | CSRF token |
| A09 | Logging Failures | ✅/⚠️/❌ | Structured logging |
| A10 | SSRF | ✅/⚠️/❌ | Whitelist only |

### §4.2 Middleware Pipeline Doğrulaması

| # | Middleware | Sıra | Durum |
|---|-----------|------|-------|
| 1 | OriginCheckMiddleware | 1 | ✅/❌ |
| 2 | CorsMiddleware | 2 | ✅/❌ |
| 3 | RateLimiterMiddleware | 3 | ✅/❌ |
| 4 | SecurityHeadersMiddleware | 4 | ✅/❌ |
| 5 | SessionManagerMiddleware | 5 | ✅/❌ |
| 6 | CsrfMiddleware | 6 | ✅/❌ |
| 7 | BypassAuthMiddleware | 7 | ✅/❌ |
| 8 | AuthMiddleware | 8 | ✅/❌ |
| 9 | PermissionMiddleware | 9 | ✅/❌ |
| 10 | ValidationMiddleware | 10 | ✅/❌ |

### §4.3 Sensitive Data Kontrolü

| Kontrol | Durum |
|---------|-------|
| Hardcoded secret kodda yok | ✅/❌ |
| `.env` dosyası gitignore'da | ✅/❌ |
| Log'da sensitive data yok | ✅/❌ |
| Error message'da stack trace yok | ✅/❌ |
| API key'ler hashlenmiş | ✅/❌ |

### §4.4 OWASP Kategori → Denetim Kanıtı

Her OWASP maddesi hangi kanıtla doğrulanır — kanıtsız ✅ verilemez.

| # | Risk | Kanıt | İlgili § |
|---|------|-------|----------|
| A01 | Broken Access Control | PermissionMiddleware + RBAC kuralları | §4.2 #9 |
| A02 | Cryptographic Failures | Argon2id + AES-256-GCM yapılandırması | §3.1 |
| A03 | Injection | PDO prepared statement + açık sütun listesi | §4.1 A03 notu |
| A04 | Insecure Design | Threat modeling kaydı (vault) | §4.1 A04 notu |
| A05 | Security Misconfiguration | CSP nonce + HSTS başlıkları | §3.1 |
| A06 | Vulnerable Components | `composer audit` çıktısı | §4.1 A06 notu |
| A07 | Auth Failures | JWT + Session hybrid + timeout | §3.1 |
| A08 | Data Integrity Failures | `csrf_token` doğrulaması | §3.1 |
| A09 | Logging Failures | Structured logging + `[REDACTED]` | §4.3 |
| A10 | SSRF | Origin whitelist (`OriginCheckMiddleware`) | §4.2 #1 |

### §4.5 Severity Sınıflandırma Ölçeği

| Severity | Tanım | Yanıt Süresi | Eskalasyon |
|----------|-------|--------------|------------|
| CRITICAL | Sistem durması / veri sızıntısı / auth bypass | Anlık | L2 → L3 → İnsan |
| HIGH | Kritik işlev kaybı / geniş açığa maruziyet | 15s | L2 |
| MEDIUM | Normal geliştirme görevi kapsamı | 30s | L1 |
| LOW | İyileştirme / optimizasyon | 60s | L1 |

---

## §5 Workflow

```
ŞABLONU SEÇ → KOPYALA → {{PLACEHOLDER}} DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT
```

| Adım | Eylem | Detay | Çıktı |
|------|-------|-------|-------|
| 1 | ŞABLONU SEÇ | `.ai/.templates/documentation/security-audit-template.md` (Guardrail #16) | Şablon kopyası |
| 2 | KOPYALA | Denetim raporu konumuna kopyala | Yeni rapor iskeleti |
| 3 | DOLDUR | `{{TITLE}}`, `{{DATE}}`, `{{AUTHOR}}`; §3.1'de `{{CSRF_KEY}}`…`{{RATE_LIMIT}}` gerçek değerlerle; §3.2'de `{{FINDING_*}}`/`{{FILE_*}}`/`{{FIX_*}}`; §3.4'te `{{RECOMMENDATION_*}}` | Dolu denetim raporu |
| 4 | DOĞRULA | §6 kontrol listesi; §4.1-§4.3 listeleri işaretli; AGENTS.md §24.5 kalite listesi geçti | 8/8 gate |
| 5 | COMMIT | Raporu commit et; bulgular CRITICAL/HIGH ise `[[../../AGENTS.md]]` §10 eskalasyon protokolüne göre ilgili ajana handover yap | Vault senkronu + eskalasyon |

**Adım 3 detayı — denetim sırası:**

| Sıra | Denetim Alanı | Kaynak | Çıktı |
|------|---------------|--------|-------|
| 1 | Middleware pipeline sırası (§4.2) | `shared/src/Middleware/` | 10 satır ✅/❌ |
| 2 | Güvenlik parametreleri (§3.1) | Kod + config | 12 satır gerçek değer |
| 3 | OWASP Top 10 (§4.1) | Genel denetim | 10 satır ✅/⚠️/❌ |
| 4 | Sensitive data (§4.3) | Kod + gitignore + log | 5 satır ✅/❌ |
| 5 | Bulgu listesi (§3.2) | Bulgular 1-4 | Severity + dosya + öneri |
| 6 | Sonuç + öneriler (§3.4) | Tüm veri | 3'lü format + metrikler |

### §5.1 Denetim Sırasının Mantığı

Sıra rastgele değildir: önce **yapı** (middleware), sonra **değerler** (parametre), sonra **genel** (OWASP), sonra **sızıntı** (sensitive), en sonda **sentez** (bulgu + sonuç) denetlenir. Bu sıra, bulguların birbirini beslemesini (ör. yanlış middleware sırası → CSP'nin de ✅ olmaması) doğal olarak yakalar.

| Sıra | Mantık | Bağımlılık |
|------|-------|------------|
| 1 Middleware | Güvenlik hattının iskeleti önce doğrulanır | Yok |
| 2 Parametreler | Hattın içindeki değerler | Sıra doğruysa anlamlı |
| 3 OWASP | Genel risk yüzeyi | 1-2 sonucunu kullanır |
| 4 Sensitive data | Kaçak taraması | Bağımsız |
| 5 Bulgular | 1-4'ün sentezi | Tümüne bağlı |
| 6 Sonuç | Bulguların özeti | 5'e bağlı |

### §5.2 Denetim Çıktı Paketi

| Çıktı | İçerik | Alıcı |
|-------|--------|-------|
| Denetim raporu (bu şablon) | §3.1-§3.4 + §4.1-§4.3 | Vault Steward |
| Bulgu listesi (§3.2) | Severity + dosya + öneri | İlgili domain agent'ı |
| Eskalasyon mesajı | CRITICAL/HIGH bulgular | `[[../../AGENTS.md]]` §9.1 formatı |
| `log.md` append kaydı | Denetim taraması + sonuç | Audit trail |
| Yeniden denetim planı | Kapanış teyidi + `{{NEXT_AUDIT_DATE}}` | Security Engineer |

### §5.3 Handover Tetikleyicileri

| Tetikleyici | Kaynak | Hedef | Öncelik |
|-------------|--------|-------|---------|
| CRITICAL açık tespiti | Security Engineer | Tech Lead (L2) | CRITICAL |
| HIGH açık tespiti | Security Engineer | İlgili domain agent'ı | HIGH |
| Middleware sıra ihlali | Security Engineer | Backend Architect | HIGH |
| CSP/CSRF uyumsuzluğu | Security Engineer | Security Engineer (§10 L1→L2) | HIGH |
| A06 (bağımlılık) bulgusu | Security Engineer | DevOps Engineer | MEDIUM |
| Sensitive data log'da | Security Engineer | MO (REDACTED maskesi) | HIGH |

### §5.4 Denetim Zamanlama Penceresi

| Parametre | Değer | Not |
|-----------|-------|-----|
| Denetim penceresi | Sprint sonu + release öncesi | Çakışma varsa release öncesi kazanır |
| CRITICAL sonrası yeniden denetim | 24 saat içinde | §6.3 kapanış kuralı |
| HIGH sonrası yeniden denetim | 1 sprint içinde | Kapanış kaydı `FIXED` |
| OWASP tam tarama | Çeyreklik | §4.1 A01-A10 tamamı |
| Middleware regression | Her pipeline değişikliğinde | §4.2 sıra kontrolü |
| Bağımlılık taraması (`composer audit`) | Haftalık | A06 kanıtı §4.4 |
| Denetim süresi (oturum) | Max 60 dk / kesintisiz | Uzun oturumda göz kayması riski |
| Bulgu kayıt gecikmesi | Aynı gün içinde | Ertesi gün unutulur, kanıt soğur |

---

## §6 Doğrulama

Rapor kapatılmadan önce kalite kapıları sırayla kontrol edilir; tek bir madde bile ✅ değilse rapor açık kalır.

| # | Kontrol | Beklenen | Durum |
|---|---------|----------|-------|
| 1 | Frontmatter 7 zorunlu alan var mı? | title, type, category, date, updated, version, status, authority | ✅/❌ |
| 2 | H1 + §1-§7 iskeleti eksiksiz mi? | 7 bölüm silinmemiş | ✅/❌ |
| 3 | Tüm `{{PLACEHOLDER}}`'lar dolduruldu mu? | DATE, AUTHOR, CSRF_KEY…RECOMMENDATION_* | ✅/❌ |
| 4 | §3.1 parametre tablosu 12 satır mı? | Gerçek değerler dolu, ✅/❌ işaretli | ✅/❌ |
| 5 | §4.1 OWASP 10/10 işaretli mi? | A01–A10 | ✅/❌ |
| 6 | §4.2 middleware sırası 1-10 korundu mu? | Sıra değişmez | ✅/❌ |
| 7 | §4.3 sensitive data 5/5 işaretli mi? | 5 kontrol | ✅/❌ |
| 8 | Severity değerleri 4'lü kümede mi? | CRITICAL/HIGH/MEDIUM/LOW | ✅/❌ |
| 9 | Sonuç üçlüsü kullanıldı mı? | ✅ / ⚠️ / ❌ | ✅/❌ |
| 10 | Hardcoded secret / stack trace / log sızıntısı yok mu? | §4.3 + REDACTED | ✅/❌ |
| 11 | Secret/credential rapora yazılmadı mı? | `[REDACTED]` maskesi | ✅/❌ |
| 12 | Wiki-link'ler hedefe ulaşıyor mu? | `[[relative/path]]` formatı | ✅/❌ |
| 13 | Türkçe doğruluk + mojibake yok mu? | ç ğ ı İ ö ş ü doğru | ✅/❌ |
| 14 | Doğrulanamayan alan işaretlendi mi? | `⚠️ VERIFICATION REQUIRED` | ✅/❌ |
| 15 | CRITICAL/HIGH için eskalasyon başlatıldı mı? | `[[../../AGENTS.md]]` §10 | ✅/❌ |
| 16 | Envanter SRP ihlali yok mu? | Envanter `[[.templates/index]]`'de | ✅/❌ |
| 17 | § başlıkları kategori tutarlılığına uygun mu? | §1 Amaç → §7 Referanslar (DRY) | ✅/❌ |

### §6.1 Doğrulama Aşamaları

| Aşama | Kontrol Grubu | Kapsadığı Maddeler | Geçme Koşulu |
|-------|---------------|--------------------|--------------|
| A | Yapı | 1, 2 | Frontmatter + iskelet |
| B | İçerik | 3 | Placeholder kalmamış |
| C | Denetim Derinliği | 4, 5, 6, 7 | Parametre 12, OWASP 10, middleware 10, sensitive 5 |
| D | Sınıflandırma | 8, 9 | Severity kümesi + sonuç üçlüsü |
| E | Güvenlik & Protokol | 10, 11, 15 | Sızıntı yok, REDACTED, eskalasyon |
| F | Kanıt & Dil | 12, 13, 14, 16, 17 | Link/dil/VERIFY/SRP/DRY |

### §6.1.1 Her Aşamanın Kanıtı

| Aşama | Kanıt | Alınan Çıktı |
|-------|-------|--------------|
| A | Frontmatter okuma | 7 alan + iskelet §1-§7 |
| B | `grep '{{'` | 0 sonuç (placeholder kalmadı) |
| C | Tablo sayaçları | 12 / 10 / 10 / 5 satır |
| D | Severity kümesi + sonuç | 4 değer + 3'lü format |
| E | REDACTED + eskalasyon | `[REDACTED]` + §10 mesajı |
| F | Link/dil taraması | Kırık link 0, mojibake 0 |

### §6.2 Red Sinyalleri (Otomatik DUR)

| Red Sinyali | Neden | Aksiyon |
|-------------|-------|---------|
| §4.1 OWASP < 10 satır | Eksik denetim | DUR — A01-A10 tamamla |
| §4.2 middleware sırası değişmiş | §4.1 kural 2 | Derhal revert + log ERROR |
| §4.3 sensitive < 5 madde | Eksik sızıntı taraması | DUR — 5 kontrolü işaretle |
| §3.1 parametre "Gerçek Değer" boş | Doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (tahmin YASAK) |
| Severity `CRITICAL/HIGH` var, eskalasyon yok | §10 protokolü | DUR — eskalasyon başlat |
| Sonuç üçlüsü dışında değer | Format ihlali | DUR — ✅/⚠️/❌ kullan |
| Secret/credential raporda | REDACTED | DUR — `[REDACTED]` maskesi |
| Denetim sırasında kod değişti | §4.9 (okuma kuralı) | revert — denetim tarafsızlığı |
| Placeholder `{{...}}` kalmış | Yarım rapor | DUR — doldur |
| Mojibake tespiti | Encoding | `vault-utf8-writer.mjs repair` |

### §6.3 Kapanış Kuralları

| Kural | Değer |
|-------|-------|
| Kapanış yetkisi | Security Engineer teyit eder, L2 onaylar |
| Yeniden denetim | CRITICAL/HIGH bulgu için zorunlu |
| Kapanış kaydı | Bulgu satırı `FIXED` + `log.md` append |
| Süre | `{{NEXT_AUDIT_DATE}}` alanı doldurulur |
| Frozen | Kapatılmış ADR referansları değiştirilmez |

### §6.4 Read-Only Doğrulama Komutları

Yazma yasaklı kontroller (salt okunur); biri bile beklenen çıktıyı vermezse commit durur.

```bash
# 1) Placeholder kontrolü — 0 olmalı (12 parametre dahil)
grep -c '{{' <AUDIT-REPORT-FILE>

# 2) Secret taraması — sonuç 0 olmalı (REDACTED)
grep -Ei 'password|secret|api[_-]?key|token=' <AUDIT-REPORT-FILE>

# 3) Severity kümesi dışı değer — sonuç 0 olmalı
grep -Ev 'CRITICAL|HIGH|MEDIUM|LOW' <AUDIT-REPORT-FILE> | grep '^\|'

# 4) Mojibake taraması — sonuç 0 olmalı
grep -aP 'Ã|Â|\x{FFFD}' <AUDIT-REPORT-FILE>
```

*(`<AUDIT-REPORT-FILE>` gerçek dosya yolu ile değiştirilir; `[REDACTED]` maskesi hariç tutulur.)*

---

**REFACTOR REPORT:** FILE: security-audit-template.md · PURPOSE: Security Audit Template · VALIDATION: 7 alan + §1-§7 + bilgi korunumu (10 OWASP, 10 middleware, 5 sensitive, 12 parametre, 17 doğrulama) · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

---

## §7 Referanslar

| Kaynak | Wiki-Link | Rol |
|--------|-----------|-----|
| Template Registry | [[.templates/index]] | Bu şablonun kaydı (envanter SRP) |
| Vault ana sözleşmesi | [[../CLAUDE.md]] | 16 Hard Guardrail, Guardrail #16 kaynağı |
| Agent Registry (SSOT) | [[../../AGENTS.md]] | Routing §6 (security → Security Engineer), middleware sıra §5, eskalasyon §10 |
| API dokümanı | [[./api-doc-template]] | Auth başlıklarının contract'ı |
| Backend şablonu | [[../backend/php-template]] | Middleware kod iskeleti |
| Middleware kanıtı | `shared/src/Middleware/` | PSR-15 middleware dosyaları (glob kanıtı) |
| Mimari kararlar | [[../../brain.md]] | ADR-010/011/012/013/022 özetleri |
| Donanım denetimi | [[../hardware/hardware-template]] | Kapsam dışı güvenlik (donanım) |
| Şablon kuralı | [[../CLAUDE.md]] | Guardrail #16 |
| Süreçler | [[../../WORKFLOW.md]] | Fazlar, session protokolü |

---

**Template Version:** 2.0.0
**Last Updated:** 2026-09-23
