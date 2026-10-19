---
node_id: K009-security/policies/policies-bilesenler
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on: K009-security/policies/policies-surucu
evidence: shared/src/Middleware/ · shared/src/PageRouter/PageRouterKernel.php:322-341
validation: pending
open_questions: ''
group: mimari
---

# K009 · policies · policies-bilesenler

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Bileşen Haritası
> Politika sistemini oluşturan **bileşenler**: çalışır kod (middleware), doğrulayıcı
> (testler), düzenleyici belgeler (vault/ADR) ve aralarındaki bağımlılık.
> Bileşen değişirse hangi politika etkelenir — hepsi burada.
> P-numaraları `policies-anayasa.md` §4'e göredir.

## BAĞLANTILAR

- [[policies-surucu]] — politika → bileşen → test tablosu (§2)
- [[policies-anayasa]] — P-01..P-18
- [[policies-ornekler]] — bileşen kod alıntıları
- ADR-010 (csrf) — `.ai/.decisions/accepted/ADR-010-csrf-protection-strategy.md` (accepted) — bileşen kararı (P-11..P-13)
- [[index]] — dizin indeksi

---

## 1. Bileşen Envanteri

### A. Çalışır kod (shared/src/Middleware)

| Bileşen | Dosya | Sorumluluk | Politika |
|---|---|---|---|
| CsrfMiddleware | `shared/src/Middleware/CsrfMiddleware.php` | token doğrula, 403 + olay | P-11, P-12 |
| SecurityHeadersMiddleware | `shared/src/Middleware/SecurityHeadersMiddleware.php` | nonce + başlık seti | P-14, P-15 |
| OriginCheckMiddleware | `shared/src/Middleware/OriginCheckMiddleware.php` | same-origin kontrolü | P-13b, P-09f (⚠️ fail-open) |
| BypassAuthMiddleware | `shared/src/Middleware/BypassAuthMiddleware.php` | fail-closed bypass kapısı | P-09, P-18 |
| RateLimiterMiddleware | `shared/src/Middleware/RateLimiterMiddleware.php` | brute-force eşiği, fail-closed | P-17, P-09 |
| SessionCookieMiddleware | (pipeline #1) | SameSite=Lax + HttpOnly | P-13a |
| CorrelationIdMiddleware | (pipeline #2) | redaksiyonli telemetri | P-16 (destek) |
| PageRouterKernel (pipeline) | `shared/src/PageRouter/PageRouterKernel.php:322-341` | sıra mühürü + kayıt | P-08f, P-14b |

### B. Çalışır kod (auth.coremusic.net)

| Bileşen | Dosya | Sorumluluk | Politika |
|---|---|---|---|
| Password (VO) | `auth.coremusic.net/include/Domain/ValueObject/Password.php` | min-8 + Argon2id + pepper | P-01, P-02, P-03, P-04 |

### C. Doğrulayıcılar (testler)

| Bileşen | Dosya | Korumadığı politika | AD |
|---|---|---|---|
| CsrfMiddlewareTest | `shared/tests/Middleware/CsrfMiddlewareTest.php` | P-11, P-12 | AD-01..05 |
| BypassAuthMiddlewareFailClosedTest | `shared/tests/Middleware/BypassAuthMiddlewareFailClosedTest.php` | P-09, P-18 | AD-08 |
| MiddlewarePipelineTest | `shared/tests/Middleware/MiddlewarePipelineTest.php` | P-08f, P-14b | AD-06 |
| LoggerRedactionTest | `shared/tests/Unit/Log/LoggerRedactionTest.php` | P-04b, P-07e, P-16 | AD-07 |

**Boşluk**: P-01..P-03 (parola testi okunmadı) · P-05/P-06 (tarama) · P-10 (QA gate
raporu) · P-13 katman 3 (PLANNED) · P-15 (yanıt başlık testi) · P-17 (rate-limit
testi) için test bileşeni **YOK** → `policies-dogrulama.md` RED.

### D. Düzenleyici belgeler (vault + ADR)

| Bileşen | Dosya | Rol | Politika |
|---|---|---|---|
| Anayasa | `policies/policies-anayasa.md` | RFC2119 kaynak (SSOT) | hepsi P-01..P-18 |
| Sürücü | `policies/policies-surucu.md` | eşleme + ihlal akışı | §2/§6 |
| Kapsam | `policies/policies-kapsam.md` | sınır | P-10 eşlemesi |
| Doğrulama | `policies/policies-dogrulama.md` | AD-xx kaydı | tümü |
| Örnekler | `policies/policies-ornekler.md` | kanıt alıntıları | P-01..P-18 |
| Indeks | `policies/index.md` | SSOT indeks | §1-§5 |
| ADR-010 | `.ai/.decisions/accepted/ADR-010-csrf-protection-strategy.md` | CSRF kararı (accepted) | P-11, P-12, P-13 |
| Anayasa (vault) | `.ai/CLAUDE.md` :112,113,336,682,687,719,721 | kural metni | P-02, P-07, P-08, P-11, P-14 |
| Kurallar | `.ai/AGENTS.md` §5/§16/§17/§18 | katman/OWASP/log/revert | P-08, P-10, P-16 |

---

## 2. Bagimlilik Grafigi

```text
index.md (SSOT indeks)
  └─ policies-anayasa.md (P-01..P-18, RFC2119)
       ├─ policies-kapsam.md (7 kalem ↔ politika)
       └─ policies-surucu.md (yönlendirme + ihlal akışı)
            ├─ policies-ornekler.md (kod alıntıları)
            ├─ policies-bilesenler.md (bu dosya)
            └─ policies-dogrulama.md (AD-xx, kanıt)
```

**Yön**: ok → yaz. Doğrulama sürücüye bağımlıdır (AD satırı P-xx'siz olmaz);
örnekler anayasadan bağımsız metin üretmez; anayasa bu dört dosyanın numara
kaynağıdır (§1 notu).

---

## 3. Bilesen Etki Analizi

Değişiklik senaryosu → etkilenen politika → zorunlu güncelleme:

| Değişiklik | Etkilenen politika | Güncellenecek bileşen |
|---|---|---|
| Pipeline'a middleware ekleme | P-08f, P-14b | PageRouterKernel + surucu §3 + ornekler §6 + MiddlewarePipelineTest |
| Token adını değiştirme | P-11 | CsrfMiddleware + `.ai/CLAUDE.md:682` + anayasa + CsrfMiddlewareTest |
| Parola parametresi/uzunluk | P-01, P-02 | Password.php + ornekler §3 + dogrulama AD-09/AD-10 + index:21 (ONAY) |
| Origin fail-closed'a geçiş | P-09f, P-13b | OriginCheckMiddleware:39-41 + dogrulama AD-14 OPEN→GREEN + ADR-010 |
| Yeni başlık/CSP direktifi | P-14, P-15 | SecurityHeadersMiddleware + dogrulama AD-12/AD-13 |
| Rate-limit eşiği/log değişikliği | P-17, P-09e | RateLimiterMiddleware + dogrulama AD-15 |

**Kural**: tabloda adı geçen tüm bileşenler tek turda güncellenmezse C1/C3 ihlali
(`policies-surucu.md` §5) — yarım değişiklik yasak.

---

## 4. Bilesen Disiplin Kurallari

1. **Sorumluluk tek**: her bileşenin politika sütunu en az 1, en fazla 4 P-xx;
   5+ ise bileşen bölünür (SOLID SRP).
2. **Test zorunluluğu**: çalışan kod bileşeni (grup A/B) test bileşenisiz merge
   edilemez; §1-C boşluk listesi önce `security-testing` kapsamına yazılır.
3. **Vault bileşeni kod değiştirmez**: D grubu belgeler yalnız politika/metin
   üretir; `*.php` / `*.js` / `*.css` / `*.sql` yüzeyine dokunmaz (dokunursa
   surucu §6 akışı).
4. **ADR üstündür**: ADR-010 accepted ise CSRF bileşeni davranışı ona uyar;
   çelişki varsa bileşen değil ADR revize edilir (ONAY ile).

---

## 5. Bilesen Sayimi

| Grup | Adet | Not |
|---|---|---|
| A. Çalışır kod (shared) | 8 | 6 middleware + Session/Correlation (pipeline) + Kernel ayrı satır |
| B. Çalışır kod (auth) | 1 | Password VO |
| C. Test | 4 | GREEN kanıtlı (koşturulmadı — dogrulama §2) |
| D. Belge/ADR | 9 | 7 policies + `.ai/CLAUDE.md` + `.ai/AGENTS.md` (+ ADR-010 ayrı kaynak) |
| Test boşluğu olan politika | 9 | P-01..P-03, P-05, P-06, P-10, P-13(3), P-15, P-17 |

---

## 6. Acik Bilesen Sorulari

- `OriginCheckMiddleware` fail-open davranışı anayasa P-09f'ye (ADR-010 katman 3)
  aykırı → `⚠️ ONAY BEKLİYOR` (dogrulama AD-14 OPEN).
- RateLimiter/SecurityHeaders için test bileşeni yok (AD-12/AD-15 RED).
- `K0100-identitiy` (dizin disk adı) altındaki auth servisleri (AuthService,
  SessionManager) bu harita dışındadır; yalnız Password VO P-01..P-04 nedeniyle
  içeridedir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
