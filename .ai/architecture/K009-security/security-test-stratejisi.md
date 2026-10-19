---
node_id: K009-security/security-test-stratejisi
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/security-dogrulama
  - K009-security/security-riskler
evidence: "2 mevcut test dosyası kanıtı + T-01..T-20 test planı + WSTG referansı"
validation: "Mevcut testler kod kanıtlı; boşluklar listeli (uydurma test yok)"
open_questions: "CI entegrasyonu doğrulanmadı; coverage metriği yok"
group: kod
---

# K009 · Güvenlik · Test Stratejisi (Kök)

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-12 · Rol: K009 güvenlik test planı.
> Kural: mevcut test = Dosya·Satır kanıtı; eksik test = BOŞLUK (uydurma "geçiyor" yok).

## §1 MEVCUT TEST KANITLARI

| Test Dosyası | Kanıt (Dosya·Satır) | Ne Doğrular | Durum |
|---|---|---|---|
| CsrfMiddlewareTest.php | 12 / 308 | CSRF token reddi + kabulü | ✅ MEVCUT |
| ApiKeyRepositoryTest.php | 15 / 72 | API key doğrulama akışı | ✅ MEVCUT |
| LoggerRedactionTest.php | 132 | Log'da secret/parola redaction | ✅ MEVCUT |

| Test Kanıt Kuralı | |
|---|---|
| "Test var" iddiası | Dosya·Satır olmadan yazılmaz |
| Test çalıştırıldı mı? | Bu oturumda test SÜRDÜRÜLMEDİ (shell yasağı) → durum: mevcut, çalıştırılmadı ⚠️ |

## §2 TEST PİRAMİDİ (K009)

| Katman | Kapsam | Durum |
|---|---|---|
| Unit | Password, Csrf, ReturnUrlPolicy, hash_equals | Kısmi (3 test dosyası) |
| Integration | Middleware pipeline sırası, rate limit akışı | YOK (boşluk) |
| Security (SAST/DAST) | OWASP WSTG senaryoları | YOK (boşluk) |
| Regression | Redaction, API key | ✅ mevcut |

## §3 TEST VAKALARI (PLAN)

| # | Test Vakası | İlgili Risk/Kontrol | Durum |
|---|---|---|---|
| T-01 | CSRF token yoksa state-changing istek reddi | D-03, R-04 | ✅ mevcut (CsrfMiddlewareTest·12/308) |
| T-02 | CSRF token yanlışsa reddi | D-03 | ✅ mevcut |
| T-03 | GET isteği CSRF'siz geçer | D-03 | ⚠️ doğrulanmadı (mevcut mu) |
| T-04 | Argon2id hash doğrulama (password_verify) | D-01/D-02 | ⚠️ yok → plan |
| T-05 | Pepper boşken fail | D-01 | ⚠️ yok → plan |
| T-06 | API key yanlış hash → reddi | D-10, R-03 | ✅ mevcut (ApiKeyRepositoryTest·72) |
| T-07 | API key süresi dolmuş → reddi | K-08 | ⚠️ yok → plan |
| T-08 | Log'da parola/secret görünmüyor | D-18, R-08 | ✅ mevcut (LoggerRedactionTest·132) |
| T-09 | Rate limit aşıldığında 429/fail | D-12, R-12 | ⚠️ yok → plan |
| T-10 | Cache yokken /login fail-closed | K-05, R-13 | ⚠️ yok → plan |
| T-11 | Yetkisiz rol → forbidden (role_insufficient) | D-15, R-14 | ⚠️ yok → plan |
| T-12 | Permission'da strict karşılaştırma (tip tuzağı) | D-15 | ⚠️ yok → plan |
| T-13 | Open redirect varyantları (%0A, //evil) reddi | D-21, R-05 | ⚠️ yok → plan |
| T-14 | Session idle timeout sonrası istek reddi | D-06, R-09 | ⚠️ yok → plan |
| T-15 | Oturum sabitleme: login sonrası ID değişimi | D-09, K-03 | ⚠️ yok → plan |
| T-16 | Güvenlik başlıkları görünür (HSTS/CSP/XCTO) | D-05, R-15 | ⚠️ yok → plan (entegrasyon) |
| T-17 | JWT exp/iss/aud reddi (fail-closed) | D-16, R-02 | ⚠️ yok → plan |
| T-18 | BypassAuth whitelist dışındaki uç auth ister | R-01 | ⚠️ yok → plan (P0) |
| T-19 | Injection denemesi (SQL enjeksiyon payload'ı) | R-06 | ⚠️ yok → plan (WSTG) |
| T-20 | Cookie bayrakları (HttpOnly/SameSite) | R-09 | ⚠️ yok → plan (P0) |

## §4 WSTG / ASVS REFERANSLARI

| Kaynak (kök web-arastira §2) | Test Bölümü | İlgili Test |
|---|---|---|
| WSTG (owasp.org/www-project-web-security-testing-guide/) | Identity/Session testing | T-14, T-15, T-20 |
| WSTG | Authentication testing | T-04, T-18 |
| WSTG | Input validation testing | T-19 |
| ASVS (owasp.org/www-project-application-security-verification-standard/) | Session/Verification seviyeleri | O-05 (seviye kararı) |
| OWASP Top10/2025 | A01..A10 denetim | §5 eşleme |

## §5 OWASP A01..A10 → TEST EŞLEMESİ

| OWASP | Test No | Durum |
|---|---|---|
| A01 | T-11, T-13, T-18 | 0/3 çalıştırıldı |
| A02 | T-04, T-17 | 0/2 |
| A03 | T-19 | 0/1 |
| A05 | T-16 | 0/1 |
| A07 | T-04, T-14, T-20 | 0/3 |
| A08 | T-01, T-06 | mevcut, çalıştırılmadı ⚠️ |
| A09 | T-08 | mevcut, çalıştırılmadı ⚠️ |
| A10 | — | BOŞLUK (kanıt yok) |

## §6 TEST ALTYAPI GEREKSİNİMLERİ

| Gereksinim | Durum |
|---|---|
| PHPUnit/çalıştırıcı erişimi | Bu oturumda shell erişimi YOK → çalıştırılamadı |
| CI entegrasyonu (pipeline) | ⚠️ doğrulanmadı |
| Test verisi (fabrika) | UNKNOWN |
| Security test tooling (SAST/DAST) | YOK (boşluk) |
| Coverage metriği | YOK (boşluk) |

## §7 ÖNCELİKLİ ÇALIŞTIRMA LİSTESİ

| Sıra | İşlem | Gerekçe |
|---|---|---|
| 1 | Mevcut 3 test dosyasını çalıştır (T-01/T-06/T-08) | Kanıt canlı |
| 2 | P0 planları: T-18, T-20, T-04 | R-01/R-09/D-01 |
| 3 | T-09/T-10 (rate limit) | R-12/R-13 |
| 4 | T-16 header entegrasyonu | D-05 |
| 5 | T-19 (WSTG) | A03 |

| Son Durum | |
|---|---|
| Dosya | security-test-stratejisi.md · group: kod |
| Mevcut test | 3 dosya (kanıtlı, çalıştırılmadı ⚠️) |
| Plan | 20 vakadan 3 mevcut · 17 boşluk |

## §8 ÇOCUK DOSYA TEST DAĞILIMI

| Çocuk | Test Vakaları |
|---|---|
| authentication | T-04, T-05, T-18 |
| authorization | T-11, T-12 |
| session-security | T-01..T-03, T-13, T-14, T-15, T-20 |
| encryption | T-17, T-16 (HSTS/CSP) |
| secrets | T-06, T-07, T-08 |
| rate-limiting | T-09, T-10 |
| dalga-3 security-testing | T-19 + SAST/DAST altyapısı |

## §9 TEST — KABUL KRİTERLERİ

| Kriter | Ölçüt | Durum |
|---|---|---|
| Mevcut testler yeşil | 3 dosya çalıştırılıp PASS | ⚠️ çalıştırılmadı |
| P0 testleri eklendi | T-18, T-20, T-04 | BOŞLUK |
| Redaction testi korunuyor | LoggerRedactionTest PASS | mevcut |
| CI'da güvenlik testi | pipeline kaydı | ⚠️ doğrulanmadı |
| uydurma test iddiası | 0 | ✅ (bu dosyada "geçiyor" yazmıyor) |

| Son Durum (test · geniş) | |
|---|---|
| Vaka | 20 · mevcut 3 · boşluk 17 |
| Çalıştırma | bu oturumda YAPILMADI (shell erişimi yok) → açık ⚠️ |
| Bağ | dogrulama + riskler |
