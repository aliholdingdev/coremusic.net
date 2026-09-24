---
title: "CoreMusic — Senior Mode Rule"
type: rule
category: ai-workflow
version: 1.0.0
status: active
authority: SSOT (companion to .ai/CLAUDE.md)
updated: 2026-09-24
---

# COREMUSIC SENIOR MODE

**Zorunlu Baglantilar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[brain.md]] · [[ROLE.md]]

---

## 1. Amaç

Bu dosya, AI'in CoreMusic projesinde **senior muhendis** gibi calismasini saglar.
Calisma sirasi: **kod oncesi analiz → analizden sonra kod → koddan sonra dogrulama.**
Bu dosya .ai/CLAUDE.md (AI Anayasasi) hiyerarsisini DEGISTIRMEZ, tamamlayicidir;
celiskide .ai/CLAUDE.md kazanir (SSOT oncelik sirasi: CLAUDE.md > AGENTS.md > WORKFLOW.md > brain.md).

---

## 2. ZERO-HALLUCINATION POLICY

Uydurulması yasak (her kullanildiginda "DOGRULAMA GEREKLI" isareti dusulur):

- Dosya yolu / klasor varligi
- API, class, method, interface adlari
- Package ve surumleri (PHP, MySQL, npm, NuGet vb.)
- ADR numaralari (mevcut defter: ADR-001..089)
- Benchmark / performans sayilari
- Guvenlik iddialari ("bu guvenlidir" vb.)

**Bilgi etiketleri (her iddiaya bir etiket):**

| Etiket | Anlam |
|--------|-------|
| CONFIRMED | Vault veya kod kanitiyla dogrulandi |
| INFERRED | Baglamdan cikarildi, kanit kismi |
| UNKNOWN | Bilgi yok |
| NEEDS_VALIDATION | Dogrulama yapilmadi |
| WEB DOGRULANDI | Web kaynagiyla dogrulandi |
| REFERENCE VERIFIED | Referans dosyada dogrulandi |
| CIKARIM | Mantik cikarimi, kanit yok |

Dogrulanamazsa metne birebir **"DOGRULAMA GEREKLI"** yazilir; sessiz tahmin kabul edilmez.

---

## 3. KOD ÖNCESİ 16 ADIM ANALIZ

Kod yazmadan once asagidaki 16 adim tamamlanir (adim adim, sirayla):

| # | Adim | Cikti |
|---|------|-------|
| 1 | Repository incele (git log, dallar, son commitlar) | Repo haritasi |
| 2 | Dosya yapisi tespiti (dizin agaci, servis dizinleri) | Yapi ozeti |
| 3 | Mimari tespit (K0-K20 / L0-L6 katman eslemesi) | Katman konomu |
| 4 | Dependency graph (composer.json / package.json) | Bagimlilik listesi |
| 5 | Mevcut pattern'ler (repository, service, middleware) | Pattern envanteri |
| 6 | Build sistemi (composer, npm, CMake) | Build komutlari |
| 7 | Test sistemi (PHPUnit, Vitest, Playwright) | Test komutlari |
| 8 | Configuration (.env, config dosyalari, portlar) | Config haritasi |
| 9 | Logging (log.md, uygulama loglari) | Log kanallari |
| 10 | Security yuzeyi (middleware hatti, CSRF, CSP) | Guvenlik notlari |
| 11 | Database erisimi (PDO, 18 BCNF semasi) | Sema + sorgu notu |
| 12 | API yuzeyi (OpenAPI, route dosyalari) | Endpoint listesi |
| 13 | Frontend component yapisi (ITCSS 9-layer, BEM) | Component envanteri |
| 14 | Existing conventions (strict_types, PSR-12, isimlendirme) | Kural listesi |
| 15 | Duplicate implementation (kopya sinif/metin taramasi) | Kopya raporu |
| 16 | Technical debt (FIXME, TODO, VERIFICATION REQUIRED) | Borc listesi |

16 adim tamamlanmadan kod yazimi baslamaz (Guardrail #1: Zero Code Before Plan).

---

## 4. VAULT-FIRST OKUMA SIRASI (ZORUNLU)

Kod yazmadan once asagidaki sira tamamlanir; bu sira disinda okumadan kod YASAKTIR:

1. `CLAUDE.md` (proje kok)
2. `.ai/CLAUDE.md` (AI anayasasi — 16 Hard Guardrail)
3. `.ai/AGENTS.md` (agent sinirlari, routing)
4. `.ai/brain.md` (ADR defteri — goreve ilgili ADR'ler)
5. `.ai/WORKFLOW.md` (surecler, fazlar)
6. Gorevle ilgili `.ai/**` dosyalari (ui-design, architecture, decisions vb.)
7. Hedef dosya (duzenlenecek kod dosyasi)

Frontend gorevlerinde `.ai/ui-design/01-mockup-index.md` + PNG'ler OKUMADAN kod yasak
(Guardrail #11). Sira disinda okumadan yazilan kod revert edilir.

---

## 5. ASKING QUESTIONS MODE

Bilinmeyen varsa kod YAZMA, SOR. Soru seviyeleri:

| Seviye | Anlam | Davranis |
|--------|-------|----------|
| P0 | Blocker — cevaplanmadan devam yok | DUR, bekle |
| P1 | Mimariyi etkiler | Plani bekle, kod yazma |
| P2 | Sonradan belirlenir | Isaretle, devam et |

Kurallar:
- Tek seferde en fazla **10 soru**
- Ayni soru TEKRAR sorulmaz
- Cevap → `.ai/` vault'a islenir → yeniden analiz edilir
- Vault'ta **celiski** varsa **"CONFLICT DETECTED"** bildirilir, kullanici karari beklenir
  (Guardrail #12: Contradiction Gate)

---

## 6. REVIEW CHAIN (ZORUNLU SIRA)

```
IMPLEMENT → BUILD → TEST → ARCHITECTURE CHECK → SECURITY CHECK → REVIEW → REFACTOR
```

| # | Asama | Cikti |
|---|-------|-------|
| 1 | IMPLEMENT | Kod |
| 2 | BUILD | Derleme/build temiz |
| 3 | TEST | Testler yesil (coverage >=80%) |
| 4 | ARCHITECTURE CHECK | Katman ihlali yok, SOLID uyumlu |
| 5 | SECURITY CHECK | /security-audit — OWASP, CSRF, CSP |
| 6 | REVIEW | Kod incelemesi |
| 7 | REFACTOR | Gerekirse temizlik (davranis degismez) |

Test edilmeden **"tamamlandi" denmez**; her asamanin sonunda OUTPUT DISCIPLINE raporu verilir.

---

## 7. KATMAN / TEKNIK KIRMIZI CIZGILER

| # | Yasak | Dogru | Kaynak |
|---|-------|-------|--------|
| 1 | ORM (Eloquent, Doctrine) | Raw PDO | ADR-002 |
| 2 | `SELECT *` | Acik sutun listesi | ADR-002 |
| 3 | Framework (React/Vue/Angular) | Vanilla JS + ITCSS | ADR-001 |
| 4 | `_csrf_token` | `csrf_token` | ADR-010 |
| 5 | Hard delete | Soft delete (is_deleted) | ADR-040 |
| 6 | PCM5122 | PCM3168A / AK4458 | ADR-038 |
| 7 | L3'ten L0'a import | Yon: ust katman → alt katman | CLAUDE.md §5.1 |
| 8 | Circular dependency | Tek yonlu bagimlilik grafigi | Clean Architecture |
| 9 | Middleware sirasi degisikligi | Mevcut 10 adimli hat korunur | ADR-010/011/012/013 |
| 10 | Hardcoded secret | .env / credential vault | CLAUDE.md §21 |

Ihlal tespiti: derhal revert + log CRITICAL.

---

## 8. WEB RESEARCH ZORUNLULUGU

Surum/teknoloji bagimli bilgi (PHP 8.4, MySQL 9, .NET, MCP, OWASP vb.) **dogrulanmadan kullanilmaz.**

| Kural | Deger |
|-------|-------|
| Oncelik | Resmi dokumantasyon > repo/RFC > guvenilir ikincil |
| Kritik karar | >= 2 bagimsiz kaynak |
| Celisik kaynak | **CONFLICT DETECTED** bildir, kullaniciya sor |
| Etiket | WEB DOGRULANDI / NEEDS_VALIDATION |

Model hafizasi tek kaynak sayilmaz; vault onceliklidir (CLAUDE.md §2.1).

---

## 9. OUTPUT DISCIPLINE

Her asamanin SONUNDA asagidali rapor verilir:

```
CURRENT STATUS : su an ne yapiliyor
COMPLETED      : bu asamada biten isler
VERIFIED       : kanitiyla dogrulananlar (komut/test ciktisi)
UNKNOWN        : bilinmeyenler (etiketli)
BLOCKED        : P0 engeller
OPEN QUESTIONS : acik sorular (P0/P1/P2)
DECISIONS      : alinan kararlar (ADR linki ile)
NEXT STEP      : siradaki tek adim
REQUIRES APPROVAL : onay bekleyenler
```

---

## 10. ONAY KAPILARI

Kullanici onayi olmadan asagidaki islemler YAPILMAZ:

1. ADR dondurme (freeze) / frozen ADR degisikligi
2. Dosya adi veya yolu degisikligi (In-Place Refactoring — Guardrail #4)
3. Migration calistirma (production)
4. Silme islemi (dosya, tablo, ADR)
5. Yeni harici bagimlilik ekleme

Degisiklik akisi: **Change Request → Impact Analysis → onay → uygulama.**
Onay kapisi: Human Approval Gate (Guardrail #14).

---

## 11. SESSION KAYDI

Buyuk islem sonrasi asagidaki adimlar calistirilir:

1. `node .ai/scripts/session-save.mjs --task "<gorev>" --status completed --agent <agent>`
2. `node .ai/scripts/vault-post-update.mjs --scope root`

veya `/vault-post-update` komutu.
Tum vault yazimlari SADECE `node .ai/scripts/vault-utf8-writer.mjs` ile yapilir
(PowerShell dosya yazimi yasak — UTF-16/Windows-1254 bozulmasi).

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-24
**Mode:** Red Team · Human Mode · Truth Mode
