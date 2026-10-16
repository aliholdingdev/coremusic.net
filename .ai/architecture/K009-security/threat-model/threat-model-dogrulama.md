---
node_id: "K009-security/threat-model/threat-model-dogrulama"
node_type: "module"
status: "verify-required"
parent_id: "K009-security"
children: []
depends_on: []
evidence:
  - "shared/src/Middleware/*.php (8 middleware — disk okundu 2026-10-15)"
  - "shared/src/Security/ReturnUrlPolicy.php:102-181"
  - "shared/src/Session/CLAUDE.md:49 + shared/src/Middleware/CLAUDE.md:102 (cookie bayrakları: HTTPOnly, Secure, SameSite=Lax)"
  - ".ai/architecture/K009-security/security-riskler.md (R-06 KISMİ · R-07 · R-09/R-10 — satır 41-45)"
validation: "AD-01..AD-11 satırları Dosya·Satır kanıtıyla 2026-10-15'te doğrulandı; AD-07/AD-08/AD-09/AD-10/AD-11 kısmi veya bulgu taşıyan ⚠️ satırlardır (bkz §2/§4)"
open_questions: []
group: "kanit"
---

# K009 · threat-model · Doğrulama

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Doğrulama Kayıtları: Tehdit Modelinin Kontrolleri Gerçek Kodda Var mı?

---

## §1 DOĞRULAMA TABLOSU (AD-xx)

| # | Doğrulama | Beklenen | Gerçek (kanıt) | Durum |
|---|---|---|---|---|
| AD-01 | CSRF token'ı state-changing'de zorunlu; bypass yok | safe-method dışı her POST 403 ile reddedilir; bypass köprüsü kaldırılmış | `shared/src/Middleware/CsrfMiddleware.php:15-17` — "bypass köprüsü **kaldırıldı** (ADR-010 §4)"; `:24` safe-method ayrımı; `:42` `hash_equals`; `:49-63` 403 `csrf_invalid` + securityEvent | ✅ |
| AD-02 | Origin check fail-open değil (prod) | prod'da dev-fallback default kapalı | `shared/src/Middleware/OriginCheckMiddleware.php:109-111` — "prod'da fallback asla açık gelmez"; fail-open dali sadece `origin_check_dev_fallback=true` (Or. `:36-43`) | ✅ |
| AD-03 | CORS allowlist-only yansıma | origin allowlist'te ise yansıt; değilse hiç basma | `shared/src/Middleware/CorsMiddleware.php:63-75` — allowlist ise `:69-70` `withHeader(ACAO, origin)`; allowlist dışı ise header yok (`:65-67`); allowlist config'den `:44-45`; yansıma kuralı `:63-64` (ADR-010/022) | ✅ (config içeriği ⚠️ okunmadı) |
| AD-04 | BypassAuth fail-closed + securityEvent | flag yoksa header'lı istek reddedilir; her kullanımda log satırı | `shared/src/Middleware/BypassAuthMiddleware.php:29-31` fail-closed; `:42-44` "dev bypass yok"; `:49-54` securityEvent | ✅ |
| AD-05 | Rate limiter auth yollarında fail-closed | auth yolu + servis yoksa 503 | `shared/src/Middleware/RateLimiterMiddleware.php:20-30` `AUTH_FAIL_CLOSED_PATHS`; `:53-65` 503 fail-closed; `:86-99` 429 + `RATE 005` securityEvent | ✅ |
| AD-06 | Open-redirect savunması 5 katman | protokol-göreli, kontrol karakteri, scheme, userinfo, host allowlist | `shared/src/Security/ReturnUrlPolicy.php:102-112` (protokol-göreli), `:39-41`+`:121-125` (kontrol karakteri), `:169-171` (scheme reddi), `:173-175` (userinfo reddi), `:177-181` (host allowlist) | ✅ |
| AD-07 | SecurityHeaders header set'i | global header set'i mevcut | `shared/src/Middleware/SecurityHeadersMiddleware.php:26-41` — `X-Frame-Options: DENY` (`:28`), `Referrer-Policy: strict-origin-when-cross-origin` (`:30`), CSP nonce üretimi `:17-19` + `'strict-dynamic'` `:54-56` (ADR-012); **HSTS YOK** — `shared/src/Middleware/CLAUDE.md:42` "HSTS — PLANNED (kodda yok)" | ⚠️ (header set ✓; HSTS eksik — §4 bulgu) |
| AD-08 | Session cookie bayrakları | `HttpOnly; Secure; SameSite` | `shared/src/Session/CLAUDE.md:49` + `shared/src/Middleware/CLAUDE.md:102` — "HTTPOnly, Secure, **SameSite=Lax**" (ADR-011); `SessionManager.php` dosyası **YOK** — gerçek dosyalar `Session/SessionBootstrapper|Config|Initializer|Lifecycle.php` | ⚠️ (Lax kanıtlandı; Strict iddiası diskte YOK) |
| AD-09 | 403/429/503 → log'a düşüyor mu? | ihlal olaylarının log'da görünür olması | `shared/logs/app.log` — `BACC 006` (bypass kullanıldı), `RATE 005` (rate-limit) satırları **canlı** görüldü (2026-10-09); 403'ler için runbook gerekir | ⚠️ Kısmi (log var, 403 kapsamı doğrulanmadı) |
| AD-10 | Migration → şema tutarlılığı | migration kararının vault kaydı + şema kanıtı | migration kararı: `.ai/.decisions/accepted/ADR-014-multi-db-migration-strategy.md`; şema kanıtı: security-riskler.md:41 — R-06 **KISMİ** ("şema kanıtı dalga-3"); migration/backup/healthcheck runbook kanıtı bu vault'ta **bulunamadı** (⚠️ VERIFICATION REQUIRED) | ⚠️ Kısmi |
| AD-11 | XSS/SQLi envanteri — tam yüzey taraması | tüm 1p endpoint'lerde sınıf-sınıf tarama | R-06 KISMİ (security-riskler.md:41); kontrol temeli: ValidationMiddleware + ORM yasak (ADR-002); **tam envanter bu modelleme ile başlıyor** — güvenlik testi çocuğu `security-testing`'e devir | ⚠️ Yeni (modelleme güncellemesi) |
| AD-12 | RBAC reddi gözlemlenir (R-14 sınıf E) | yetkisiz rol/izin → 403 **+** olay satırı | `shared/src/Middleware/PermissionMiddleware.php:30-42` (rol eşitlik + `in_array(..., true)` strict) · `:50-54` `securityEvent('authz_denied', …)` · `:57-62` 403 `halt` — "öncekte sessizce 403" yorumu `:49` | ✅ |
| AD-13 | Şema doğrulaması 422 (R-06 sınıf T) | state-changing'de eksik/geçersiz alan → 422 | `ValidationMiddleware.php:72-80` (422 `validation_failed` + `halt`) · kurallar `:41-69` · **ama** `:29-31` `required_fields` boşsa doğrulama **atlanır** — uç route meta kanıtı (`shared/config/routes.php`) bu turda okunmadı | ⚠️ Kısmi (boşluk kaydı) |
| AD-14 | CSP nonce + strict-dynamic (ADR-012) | nonce'lu `script-src`, `frame-ancestors 'none'` | `SecurityHeadersMiddleware.php:17-19` (nonce `bin2hex(random_bytes(32))`) · `:54-56` (`'strict-dynamic' 'nonce-…'`) · `:71` (`frame-ancestors 'none'`) · `:73` (`form-action 'self'`) | ✅ |
| AD-15 | Middleware sıra dondurulmuş (frozen) | sıra testle korunuyor; ekleme kaydı zorunlu | `shared/tests/Middleware/MiddlewarePipelineTest.php:190-199` (`1_OriginCheck → 2_Cors → 3_RateLimiter → 4_SecurityHeaders → 5_SessionManager → 6_Csrf → 7_BypassAuth → 8_Auth → 9_Permission → 10_Validation`) · `MiddlewarePipeline.php:32-34` (reverse sarma) · `shared/AGENTS.md` §4 Zorunlu kural 2 | ✅ (üretim pipe kaydı ⚠️ — `PageRouterKernel.php:300` satır içeriği okunmadı) |
| AD-16 | Bileşen envanteri disk gerçeğiyle hizalı mı? | envanter = glob sonucu | glob `shared/src/Middleware/*.php` → **11 dosya / 10 `IMiddleware` sınıfı**; `LoggingMiddleware.php` ve `ApiVersionMiddleware.php` → **0 glob isabeti (diskte YOK)** — bilesenler §2'deki B-07/B-08 "aktif" satırları bu gerçeğe aykırı (bkz §7a) | ⚠️ DÜZELTME GEREKTİRİR |
| AD-17 | Log maskeleme kod + test (R-08 sınıf I) | hassas değerler logda maskelenir; UUID korunur | `Redactor.php:31` (hassas anahtar listesi) · `:40/:49/:56` (desenler) · `:65-93` (`redact()`) · `tests/Unit/Log/LoggerRedactionTest.php:122-138` — `:127` hash maskelenir, `:134` base64 maskelenir, `:137` UUID korunur | ✅ |

---

## §2 DOĞRULAMA NOTLARI

- **AD-07/AD-08/AD-09/AD-10/AD-11 ⚠️**: Kısmi doğrulama — AD-07'de HSTS kodda yok (yalnız PLANNED notu); AD-08'de cookie SameSite=**Lax** kanıtlandı (Strict değil); AD-09'da log'da canlı `BACC 006` + `RATE 005` satırları var ama 403 olaylarının tamamı (`origin_forbidden`) log'a düşüyor mu ayrı koşu ile teyit edilmeli; AD-10'da runbook kanıtı vault'ta yok; AD-11 XSS/SQLi envanteri **yeni başlıyor**.
- **Doğrulama biçimi**: Dosya·Satır → **gerçek kod okundu** (2026-10-15); ADR kararı → **Vault kaydı**; gate → **doküman kanıtı**.
- **Doğrulama sıklığı**: her ADR eklemesinde + tehdit modeli güncellemesinde (anayasa §7).

---

## §3 SONRAKİ ADIMLAR (doğrulama → aksiyon)

1. `OriginCheck` 403'lerinin (origin-mismatch) log'da göründüğünü teyit et — `BACC 006` var, `origin_forbidden` için ayrı test (bulgu: log yok, §4).
2. **AD-09**: 403 kapsamını genişlet — 403'lerin tümü `securityEvent` yazıyor mu? (`CsrfMiddleware.php:49-63` yazıyor; OriginCheck 403'ü `OriginCheckMiddleware.php:43`'te **log yok** → §4 bulgu-1.)
3. **AD-11**: XSS/SQLi envanteri — security-riskler.md:41 R-06 "dalga-3" satırına bağlanır; tarama `security-testing` çocuğunun işidir.
4. **AD-07/AD-08 bulguları**: HSTS eklenmeli (R-09 notu "koşullu secure → HSTS ile" diyor); SameSite=Lax → ADR-010 kararı ile karşılaştırılıp gerekirse ADR revizyonu açılır.

## §4 BULGU (Doğrulama sırasında tespit — kayıt)

| # | Bulgu | Kanıt | Etki | Öneri |
|---|---|---|---|---|
| B-1 | Origin-mismatch 403'ünde **securityEvent/log yok** | `OriginCheckMiddleware.php:43` — `->withStatus(403)` (log satırı yok; CSRF 403'ü log'a yazıyor `CsrfMiddleware.php:49-63`) | Sınıf R (log yoksayma, R-07) — saldırgan origin'leri tespit edilemez | OriginCheck 403'lerine `securityEvent('origin_403')` eklenmeli (anayasa Z-4) |
| B-2 | **HSTS header'ı kodda yok** | `SecurityHeadersMiddleware.php:26-41` header set'inde `Strict-Transport-Security` yok; `shared/src/Middleware/CLAUDE.md:42` "HSTS — PLANNED (kodda yok)"; risk defteri R-09 notu: "koşullu secure → HSTS ile" | Sınıf I/M (downgrade → MITM; R-09/R-15 ilişkili) | HSTS eklenmeli; ADR-012 kapsamı ile hizalanır |
| B-3 | Cookie **SameSite=Lax** (Strict değil) | `shared/src/Session/CLAUDE.md:49` + `shared/src/Middleware/CLAUDE.md:102` | Sınıf T/E — CSRF katmanı 2 (SameSite) Lax ile daha geniş | ADR-010 karar metni ile karşılaştır; fark varsa ADR revizyonu |

---

## §5 DOĞRULAMA PROTOKOLÜ (6 adım — her yeni AD satırı için sıra değişmez)

> Doğrulama, tehdit modelinin **kanıt kapısı**dır (anayasa §5 Z-5): "kontrol var" cümlesi değil, **Dosya·Satır** geçer.

| Adım | İşlem | Geçerlilik kuralı | Yapılamazsa |
|---|---|---|---|
| K-1 | Beklenen davranışı tek cümlede yaz | "ne doğrulanıyor" belirsizse AD açılmaz | DUR (surucu H-1) |
| K-2 | Kanıtı diskten **oku** + tarih yaz | okunmayan satır kanıt sayılmaz | yalnız `⚠️ VERIFICATION REQUIRED` |
| K-3 | Yokluğu da say (set / sayaç okumayla doğrulama) | eksik başlık ya da eksik satır da kanıttır (ör. HSTS — AD-07) | "kontrol vardır" varsayımı yasak |
| K-4 | Durum ata: ✅ / ⚠️ / düzeltme | kanıtsız ✅ yazılmaz (Y-02 / T-04) | `⚠️` kalır |
| K-5 | Bulgu çıktıysa §4'e taşı | aynı bulgu iki yerde çoğaltılmaz (AD ↔ B-xx tek kayıt) | kayıt dağınıklığı → BLOCK |
| K-6 | Aksiyon sahibi + sibling bağı yaz | sahipsiz kapanış yok | §9 (devir) |

| Protokol Kuralı | |
|---|---|
| Doğrulama ≠ test | test DOSYASI ek kanıttır; davranış kod satırıyla gösterilir (anayasa Z-5 / Y-09) |
| Test isterse | `security-testing` çocuğuna devir — bu dosya test **yazmaz** (AD-09/AD-11) |
| Durum değişikliği | `AÇIK → AZALTILMIŞ` yalnız AD-xx + Dosya·Satır ile (surucu §2b B-3) |

---

## §6 YENİDEN DOĞRULAMA TETİKLEYİCİLERİ (ne zaman yeni AD satırı açılır)

| # | Tetikleyici | Yeniden doğrulanacak AD | Kaynak |
|---|---|---|---|
| 1 | Yeni middleware / pipeline sırası değişikliği | AD-15 + sıra bağı AD-01..AD-05 | anayasa §7-3 · `shared/AGENTS.md` §4 Zorunlu kural 2 |
| 2 | ADR açılışı / ADR revizyonu (ör. SameSite Lax↔Strict) | ilgili AD + §4 B-3 | anayasa §7-4 · §8.1 |
| 3 | Header/çerez/config değişikliği | AD-07, AD-08, AD-02 | anayasa §7-7 |
| 4 | Yeni risk satırı (R-15+) deftere eklenirse | R'nin kanıt satırı (AD-xx) | security-riskler §3/§9 |
| 5 | Olay / pentest bulgusu | bulgunun sınıfına en yakın AD | anayasa §7-8 |
| 6 | Envanter değişikliği (dosya eklendi/silindi) | AD-16 (glob) + bilesenler §2a | §7 aşağıda |

Yeniden doğrulamada eski satır **silinmez**: yeni AD eklenir, eski AD "2026-10-15 tur-2 öncesi" diye tarihlenir (append — anayasa §17 revizyon disiplini).

---

## §7 ENVANTER DOĞRULAMASI (2026-10-15 glob kanıtı)

| Ölçüm | Sonuç | Etki |
|---|---|---|
| `shared/src/Middleware/*.php` | **11 dosya** | ön sayım "8 middleware" iddiasıyla çelişir (frontmatter evidence satırı bu yüzden düzeltilmedi, gövde §7a kazanır) |
| `implements IMiddleware` sınıfı | **10** (11. dosya `MiddlewarePipeline.php` — taşıyıcı sınıf) | kapı bileşeni sayısı = 10 |
| `LoggingMiddleware.php` | **0 glob isabeti — YOK** | bilesenler B-07 "aktif" satırı disk gerçeğiyle çelişir |
| `ApiVersionMiddleware.php` | **0 glob isabeti — YOK** | bilesenler B-08 "aktif" satırı disk gerçeğiyle çelişir; sürüm işlevi `shared/src/Api/Versioning/ApiVersion.php` (3 dosya) tarafında |
| Güvenlik loglarının sahibi | `shared/src/Log/LoggerFactory.php` (`securityEvent`) + `shared/src/Log/Redactor.php` + `shared/src/Log/FileHandler.php` | "log yok" değil "LoggingMiddleware yok" — sınıf R'nin gerçek bileşenleri |

### §7a Bileşen Envanteri Çelişkisi (AD-16 — bilesenler §2a ile ortak kayıt)

| İddia (bilesenler) | Disk gerçeği (2026-10-15) | Karar |
|---|---|---|
| B-07 `LoggingMiddleware.php` "aktif" | dosya YOK (0 glob); log `Log/LoggerFactory` + `Redactor` ile | satır silinmedi → ⚠️ düzeltme kaydı bilesenler §2a'da |
| B-08 `ApiVersionMiddleware.php` "aktif" | dosya YOK (0 glob); sürüm `Api/Versioning/ApiVersion.php` | aynı |
| frontmatter "8 middleware dosyası" | 11 dosya / 10 sınıf | gövde §7 kazanır; frontmatter'a dokunulmadı (silme yasak) |

Çelişki giderilmeden bileşen envanteri "doğrulanmış" sayılmaz (AD-16 durumu `⚠️ DÜZELTME GEREKTİRİR`).

---

## §8 KANIT TİPİ → GEÇERLİLİK HİYERARŞİSİ

| Sıra | Kanıt tipi | Örnek | Tek başına yeterli mi? |
|---|---|---|---|
| 1 | Kod Dosya·Satır (okunmuş) | `CsrfMiddleware.php:42` | **EVET** (anayasa Z-5) |
| 2 | Yokluk/okuma kanıtı (set sayımı) | HSTS `:26-41` set'te yok | evet — negatif kanıt da kanıttır |
| 3 | Canlı log satırı | `BACC 006` / `RATE 005` (2026-10-09) | olay bazlı evet; genelleme için AD gerekir |
| 4 | Test dosyası + satır | `MiddlewarePipelineTest.php:190-199` | **HAYIR** — ek kanıttır (Y-09) |
| 5 | ADR / vault dokümanı | ADR-013 §5.4 | karar kanıtıdır, davranış kanıtı **değil** |
| 6 | Yorum / "benzer yerde vardı" | — | **HAYIR** (Z-5 son kuralı) |

---

## §9 DOĞRULAMA → DEVİR VE SAHİP

| Durum | Devir | Sahip |
|---|---|---|
| AD ⚠️ + test eksikliği | `security-testing` (test yazımı) | qa-engineer · security-engineer |
| AD ⚠️ + envanter çelişkisi (AD-16) | bilesenler §2a düzeltme kaydı | Vault Steward |
| Bulgu B-xx + kontrol eklenmesi | ilgili K009 çocuğu (ör. HSTS → `security-headers` kapsam yüzeyi) | security-engineer |
| K009 dışı yüzey | kapsam §7 devir protokolü (T-08) | Vault Steward |
| ADR gereken karar | anayasa §8 / §8.1 kapısı | Vault Steward + security-engineer |

---

## §10 REVİZYON

| Tarih | Değişiklik | Sorumlu |
|---|---|---|
| 2026-10-15 | İlk yazım — AD-01..AD-11, §1-§4 | Vault Steward (subagent) |
| 2026-10-15 | Genişletme (tur-2) — AD-12..AD-17, §5 protokol, §6 tetikleyici, §7/§7a envanter, §8 hiyerarşi, §9 devir | Vault Steward (subagent) |

## § BAĞLANTILAR

- [[threat-model-anayasa]] · [[threat-model-surucu]] · [[threat-model-ornekler]] · [[threat-model-kapsam]] · [[threat-model-bilesenler]] · [[threat-model-web-arastirma]] · [[index]]
- [[security-bilesenler]] · [[security-test-stratejisi]] · [[security-riskler]] · [[ADR-012-csp-nonce-strict-dynamic]] · [[ADR-014-multi-db-migration-strategy]]

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
