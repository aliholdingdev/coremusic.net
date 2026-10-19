---
node_id: "K009-security/threat-model/threat-model-bilesenler"
node_type: "module"
status: "draft"
parent_id: "K009-security"
children: []
depends_on: []
evidence:
  - "shared/src/Middleware/ (8 middleware dosyası — disk listesi 2026-10-15)"
  - "shared/src/Security/ReturnUrlPolicy.php"
  - ".ai/architecture/K009-security/authentication/bilesenler.md (auth bileşen envanteri)"
validation: "Draft — envanter disk listesiyle hizalı (2026-10-15)"
open_questions: []
group: "mimari"
---

# K009 · threat-model · Bileşenler

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Tehdit-Modeli Bileşen Envanteri: Kapılar, Katmanlar, Sahipler

---

## §1 BİLEŞEN SINIFLANDIRMASI

Tehdit modelinin **bileşenleri**, tehditlerin **yüzey taşıdığı** birimlerdir. Üç sınıf:

1. **Kapı bileşenleri** (middleware) — istek akışında sırayla devreye girer; bypass/bulgu = saldırganın doğrudan geçiş kapısı.
2. **Karar bileşenleri** (policy/ADR) — hangi kontrolün uygulanacağını belirler (ADR-008/010/011/012/013/020/022).
3. **Kanıt bileşenleri** (log/runbook/gate) — kontrolün gerçekten uygulandığını gösterir.

---

## §2 KAPI BİLEŞENLERİ (shared/src/Middleware + Security)

| # | Bileşen | Dosya | Sıra/konum | Ana tehdit sınıfı | Durum |
|---|---|---|---|---|---|
| B-01 | CorsMiddleware | `shared/src/Middleware/CorsMiddleware.php` | en erken (preflight) | sınıf 10 (misconfig), S | aktif (ADR-010/022 yansıma + ADR-020 §2.2E header) |
| B-02 | OriginCheckMiddleware | `shared/src/Middleware/OriginCheckMiddleware.php` | 1p API öncesi | sınıf 10 (fail-open riski) | aktif (kod yorumu ADR-010/022 — `:13`) |
| B-03 | CsrfMiddleware | `shared/src/Middleware/CsrfMiddleware.php` | state-changing POST'ta | sınıf T (CSRF) | aktif (ADR-010) |
| B-04 | RateLimiterMiddleware | `shared/src/Middleware/RateLimiterMiddleware.php` | auth yollarında fail-closed | sınıf D (brute-force) | aktif (ADR-013 §5.4) |
| B-05 | BypassAuthMiddleware | `shared/src/Middleware/BypassAuthMiddleware.php` | dev bypass kapısı | S + E (auth atlatma) | aktif (ADR-008) |
| B-06 | SecurityHeadersMiddleware | `shared/src/Middleware/SecurityHeadersMiddleware.php` | global | T/I (XSS, referer sızıntısı) | aktif (ADR-012 — CSP nonce strict-dynamic `:54-56`; **HSTS yok** bkz dogrulama §4) |
| B-07 | LoggingMiddleware | `shared/src/Middleware/LoggingMiddleware.php` | global | R (log yoksayma) | aktif |
| B-08 | ApiVersionMiddleware | `shared/src/Middleware/ApiVersionMiddleware.php` | 1p API | sürüm sapması | aktif |
| B-09 | ReturnUrlPolicy | `shared/src/Security/ReturnUrlPolicy.php` | login redirect | sınıf 8 (open redirect) | aktif (ADR-010 §2 5-ek) |
| B-10 | AuthMiddleware | `shared/src/Middleware/AuthMiddleware.php` | SessionManager sonrası (`_session` → `_auth`) | sınıf S (zayıf/sahte kimlik verisi) | aktif (`:11-31` — 2026-10-15 okundu; Ör-10) |
| B-11 | SessionManagerMiddleware | `shared/src/Middleware/SessionManagerMiddleware.php` | oturum başlatma + nonce köprüsü | S + I (oturum/çerez) — R-09 | aktif (`:17-21`; Ör-10 · AD-08) |
| B-12 | PermissionMiddleware | `shared/src/Middleware/PermissionMiddleware.php` | Auth sonrası RBAC (`Auth → Permission → Validation`) | sınıf E — R-14 | aktif (`:30-62` + `authz_denied` gözlemi; Ör-07 · AD-12) |
| B-13 | ValidationMiddleware | `shared/src/Middleware/ValidationMiddleware.php` | Controller öncesi şema doğrulaması | sınıf T (enjeksiyon girişi) — R-06 | aktif (`:41-80`; `:29-31` boşsa atlama ⚠️; Ör-08 · AD-13) |
| B-14 | MiddlewarePipeline | `shared/src/Middleware/MiddlewarePipeline.php` | `pipe()` + reverse sarma + timing | §3.2-10 (sıra değişimi) + sınıf R (ölçüm) | aktif (`:17-34`, `:41-48`; Ör-11 · AD-15) |

### §2a ENVANTER DÜZELTME KAYDI (AD-16 — B-07/B-08 disk gerçeğiyle çelişiyor, 2026-10-15)

> **Silme yasak** (görev kuralı + anayasa Y-10 benzeri disiplin): aşağıdaki satırlar **silinmedi**; çelişki bu kayda bağlandı.
> Kanıt: glob `shared/src/Middleware/*.php` → 11 dosya / 10 `IMiddleware` sınıfı (2026-10-15).

| İddia satırı | Disk gerçeği | Gerçek karşılık (K009 içi) | Karar |
|---|---|---|---|
| B-07 `shared/src/Middleware/LoggingMiddleware.php` "aktif" | **dosya YOK** (0 glob isabeti) | `shared/src/Log/LoggerFactory.php` (`securityEvent`) + `shared/src/Log/Redactor.php` + `shared/src/Log/FileHandler.php` | ⚠️ satır geçersiz — Vault Steward düzeltmesi beklenir |
| B-08 `shared/src/Middleware/ApiVersionMiddleware.php` "aktif" | **dosya YOK** (0 glob isabeti) | `shared/src/Api/Versioning/ApiVersion.php` (+ `VersionRegistry`, `VersionResolver`) | ⚠️ satır geçersiz — aynı |
| frontmatter evidence "8 middleware dosyası" | **11 dosya / 10 sınıf** | §2 tablosu (B-01..B-14, 11. dosya = taşıyıcı pipeline) | gövde kazanır; frontmatter'a dokunulmadı |

`logging`/`API version` işlevleri **yok değil, farklı bileşendedir** — tehdit modelinde sınıf R ve sürüm sapması B-07/B-08 yerine bu gerçek bileşenlere bağlanır (dogrulama §7a ile ortak kayıt).

### §2b FROZEN SIRA KANITI (pipeline hangi sırayla akar)

| Kanıt | Dosya·Satır | Ne der |
|---|---|---|
| Docblock (kural) | `PermissionMiddleware.php:8` "L1 — Pipeline #9" · `:13` "Frozen sıra: ...Auth → Permission → Validation → Controller" · `ValidationMiddleware.php:8` "Pipeline #10" · `:13` "Permission → Validation → Controller" | sıra yazılıdır |
| Kod (davranış) | `MiddlewarePipeline.php:32-34` — `array_reverse` ile sarma: ilk `pipe()` en dış katman olur | sıra çalışma zamanında |
| Test (regresyon) | `shared/tests/Middleware/MiddlewarePipelineTest.php:190-199` — `1_OriginCheck → 2_Cors → 3_RateLimiter → 4_SecurityHeaders → 5_SessionManager → 6_Csrf → 7_BypassAuth → 8_Auth → 9_Permission → 10_Validation` | sıra testte kilitli |
| Kural (sahiplik) | `shared/AGENTS.md` §4 Zorunlu kural 2 — "Middleware ekleme → `MiddlewarePipeline` kaydı + config güncelleme gerekirse" | yeni bileşen kayıtsız eklenmez |

Sıra değişikliği anayasa §7 tetikleyicisi 3'tür (modele alma zorunlu) — surucu A2/A5 adımları yeni sıra için yeniden sürülür.

---

## §3 KARAR BİLEŞENLERİ (ADR → tehdit kontrolü)

| # | Karar | Dosya | Bağlı tehdit | Etki |
|---|---|---|---|---|
| D-01 | CSRF stratejisi (double-submit token; cookie SameSite=Lax) | `ADR-010-csrf-protection-strategy.md` | sınıf T (CSRF) — R-04 | login bypass önlenir |
| D-02 | Session stratejisi | `ADR-011-session-management.md` | S + I (oturum) — R-09 | oturum sabitleme |
| D-03 | Origin/CORS stratejisi | kod yorumu: ADR-010/022 (`CorsMiddleware.php:63`, `OriginCheckMiddleware.php:13`) + ADR-020 §2.2E (`CorsMiddleware.php:35`) | sınıf 10 | cross-origin okuma |
| D-04 | Rate limiter | `ADR-013-rate-limiting-apcu.md` | sınıf D — R-12/R-13 | auth brute-force |
| D-05 | Secret yasağı (vault) | kök `AGENTS.md` §16.3 (anahtar asla `.md`/`.json`/`.log`'a yazılmaz) | sınıf I | `.ai/`'de secret yasak |
| D-06 | Bileşen envanteri (auth) | `authentication-bilesenler.md` (K009-security/authentication) | sınıf 10 (envanter boşluğu) | hangi bileşen tehdit modelinde? |

---

## §4 KANIT BİLEŞENLERİ

| # | Bileşen | Nerede | Görevi |
|---|---|---|---|
| E-01 | securityEvent log'ları | `shared/logs/app.log` — `BACC 006` (bypass), `RATE 005` (rate-limit) satırları | AD-09: olayların görünürliği |
| E-02 | production-readiness | `docs/05-operations/production-readiness.md` (P0: migration/backup/healthcheck) | AD-10 |
| E-03 | launch gate | `docs/08-reports/production-launch-gate-2026-10-02.md` (R-08: XSS/SQLi ayrı tarama) | AD-11 |
| E-04 | risk defteri | `security-riskler.md` R-01..R-14 | tehdit ↔ risk bağlantısı |
| E-05 | sıra regresyon testi | `shared/tests/Middleware/MiddlewarePipelineTest.php:190-199` (frozen 10 adım) | AD-15 · Ör-11 |
| E-06 | maskeleme testi | `shared/tests/Unit/Log/LoggerRedactionTest.php:122-138` (hash/base64 maskelenir, UUID korunur) | AD-17 · R-08 |
| E-07 | envanter glob kaydı | `shared/src/Middleware/*.php` → 11 dosya / 10 sınıf (2026-10-15) | AD-16 · §2a |

---

## §5 BİLEŞEN-TEHDİT HARİTASI (özet)

```text
Tarayıcı/CLI
  → [B-01 CORS]      sınıf 10 (misconfig) / S
  → [B-02 Origin]    sınıf 10 — fail-open dali (dogrulama §4 bulgu)
  → [B-03 CSRF]      sınıf T (CSRF)   ← D-01 ADR-010 · R-04
  → [B-04 Rate]      sınıf D          ← D-04 ADR-013 · R-12/R-13
  → [B-05 BypassAuth]S + E            ← ADR-008 · R-01
  → [B-06 Headers]   T/I (XSS/referrer) ← ADR-012 · R-10
  → [B-07 Logging]   sınıf R          → E-01 log kanıtı (R-07)
1p API + admin + doküman servisleri (bkz K009-index §1)
```

### §5a BİLEŞEN → K009 ÇOCUĞU → KARDEŞ KAYIT (atama matrisi)

| Bileşen (§2) | Tehdit sınıfı | K009 çocuğu | Kardeş kayıt |
|---|---|---|---|
| B-01 Cors · B-02 Origin | 10 (misconfig), S | `session-security` (origin/CORS) | ornekler Ör-01/Ör-02 · dogrulama AD-02/AD-03 |
| B-03 CSRF | T (+E) | `session-security` | ornekler Ör-03 · dogrulama AD-01 · surucu §3 |
| B-04 RateLimiter | D | `rate-limiting` | ornekler Ör-05 · dogrulama AD-05 · surucu §5 |
| B-05 BypassAuth | S + E | `authentication` | ornekler Ör-04 · dogrulama AD-04 · surucu §4 |
| B-06 SecurityHeaders | I + T | `security-headers` yüzeyi (kapsam §1b) | ornekler Ör-09 · dogrulama AD-07/AD-14 · bulgu B-2 |
| B-07/B-08 (çelişkili) | R · sürüm | `audit` (log) — ⚠️ gerçek bileşen `Log/*` | §2a · dogrulama §7a |
| B-09 ReturnUrlPolicy | 8 (open redirect) | `session-security` | ornekler Ör-06 · dogrulama AD-06 |
| B-10 Auth · B-11 SessionManager | S + I | `authentication` · `session-security` | ornekler Ör-10 · dogrulama AD-08 |
| B-12 Permission | E | `authorization` | ornekler Ör-07 · dogrulama AD-12 · riskler R-14 |
| B-13 Validation | T | `input-validation` | ornekler Ör-08 · dogrulama AD-13 · riskler R-06 |
| B-14 Pipeline | §3.2-10 + R | bu çocuk (tehdit kataloğu) + `audit` | ornekler Ör-11 · dogrulama AD-15 |

Çocuk adı bulunmayan satır (ör. B-07/B-08) → anayasa §13'e göre §8 seçeneklerine düşer; "eşleşme yok" sessiz geçilmez (T-05).

---

## §6 BİLEŞEN SAHİPLERİ (kim onaylar)

- **middleware**: backend-developer (PHP) + architect-reviewer (katman).
- **ADR kararı**: architect + security-engineer (Expert) + qa-engineer (kanıt).
- **canary/log kanıtı**: devops-engineer (deploy gate) + SRE (runbook).
- **envanter düzeltmesi (§2a)**: Vault Steward — bileşen satırı silinmez, düzeltme kaydıyla kapanır (dogrulama AD-16).
- **tehdit modelleme turu**: security-engineer sürer, Vault Steward onaylar (anayasa §7).

### §6a ONAY MATRİSİ (işlem → onay → kayıt yeri)

| İşlem | Onay | Zorunlu kayıt |
|---|---|---|
| Yeni kapı bileşeni (`pipe()` ekleme) | backend-developer + architect-reviewer | pipeline kaydı (`shared/AGENTS.md` §4 Zorunlu 2) + anayasa §7-1 tetikleyicisi |
| Sıra değişikliği | security-engineer + Vault Steward | yeni AD/AD-15 yeniden okuması + ornekler Ör-11 güncellemesi |
| ADR ile kapsam değişikliği (BypassAuth vb.) | Vault Steward | ADR (anayasa §8.1) + kapsam §6 izi |
| Bileşen satırı düzeltmesi (silme değil) | Vault Steward | §2a tablosu + dogrulama §7a (tek çift kayıt) |
| Kanıt tazeleme | okuyan agent | surucu §11 okuma kaydı (tarih yazılır) |

---

## §7 REVİZYON

| Tarih | Değişiklik | Sorumlu |
|---|---|---|
| 2026-10-15 | İlk yazım — §1-§6 (B-01..B-09, D-01..D-06, E-01..E-04) | Vault Steward (subagent) |
| 2026-10-15 | Genişletme (tur-2) — B-10..B-14 eklendi · §2a envanter düzeltme kaydı (B-07/B-08) · §2b frozen sıra kanıtı · E-05..E-07 · §5a atama matrisi · §6a onay matrisi | Vault Steward (subagent) |

## § BAĞLANTILAR

- [[threat-model-anayasa]] · [[threat-model-surucu]] · [[threat-model-ornekler]] · [[threat-model-kapsam]] · [[threat-model-dogrulama]] · [[threat-model-web-arastirma]] · [[index]]
- [[ADR-008-bypass-auth-middleware]] · [[ADR-010-csrf-protection-strategy]] · [[ADR-011-session-management]] · [[ADR-012-csp-nonce-strict-dynamic]] · [[ADR-013-rate-limiting-apcu]] · [[ADR-020-api-public-security]] · [[security-riskler]]

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
