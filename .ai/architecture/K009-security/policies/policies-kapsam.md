---
node_id: K009-security/policies/policies-kapsam
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on: K009-security/policies/policies-anayasa
evidence: .ai/architecture/K009-security/policies/index.md §2
validation: pending
open_questions: ''
group: mimari
---

# K009 · policies · policies-kapsam

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Kapsam Sınırı
> Bu belge, `index.md` §2 kalem listesinin **kapsam tanımıdır**: ne politika
> sayılır, ne sayılmaz, sınır nerededir. Kapsam dışı işler ayrı node'a gider;
> buradan sarkmaz. P-numaraları `policies-anayasa.md` §4'e göredir.

## BAĞLANTILAR

- [[index]] — §2 bağlayıcı kalem listesi (bu belgenin kaynağı)
- [[policies-anayasa]] — kapsamın RFC2119 politikaları hâli (P-01..P-18)
- [[policies-surucu]] — kapsam → kod yüzeyi eşlemesi
- [[../threat-model/index]] — tehdit modeli (kapsam girdisi)
- [[../security-testing/index]] — kapsamın test ayağı

---

## 1. Kapsam Kalemleri (index §2 esasli)

| # | Kapsam kalemi | Politika karşılığı | node karşılığı | Kapsamda |
|---|---|---|---|---|
| 1 | Parola (Argon2id, min 8 repo / 12+ OWASP ⚠️, pepper, ham parola) | P-01..P-04 | [[../authentication/index]] | EVET |
| 2 | Kripto (AES-256-GCM / ChaCha20; MD5/SHA-1/DES/ECB yasak) | P-05..P-06 | [[../encryption/index]] | EVET |
| 3 | Sır yüzeyi (`.env`, hardcoded secret, log/ADR yasağı) | P-07 | [[../secrets/index]] | EVET |
| 4 | Katman (tek yön bağımlılık, K6 bypass yok, pipeline kaydı) | P-08 | [[policies-surucu]] §3 | EVET |
| 5 | Fail-safe (fail-closed; fail-open yalnız ADR ile) | P-09 | [[policies-surucu]] §6 | EVET |
| 6 | OWASP Top 10:2025 + KVKK/ISO/QSA çerçevesi | P-10 | [[../audit/index]] | EVET |
| 7 | CSRF/CSP (token, 3 katman, nonce, başlık seti) | P-11..P-15 | ADR-010 (node yok) | EVET |
| 8 | Log redaksiyonu `[REDACTED]` | P-16 | [[../audit/index]] | EVET |
| 9 | Rate-limit (auth fail-closed, genel uç ADR-013) | P-17 | [[../rate-limiting/index]] | EVET |
| 10 | Bypass kapsamı (yalnız ADR ile) | P-18 | [[../authorization/index]] | EVET |

Her kalem en az bir P-xx politikasına bağlanmıştır; bağlanmayan kalem kapsam dışıdır.

---

## 2. Kapsam Ici Yuzeyler

- **Ortak katman**: `shared/src/Middleware/` (Csrf, SecurityHeaders, OriginCheck,
  BypassAuth, RateLimiter, Session) + `shared/src/PageRouter/PageRouterKernel.php:322-341`.
- **Auth servisi**: `auth.coremusic.net/include/Domain/ValueObject/Password.php` +
  `auth.coremusic.net/AGENTS.md` §4 kuralları.
- **Testler**: `shared/tests/Middleware/*` + `shared/tests/Unit/Log/LoggerRedactionTest.php`.
- **Vault belgeleri**: `.ai/CLAUDE.md` (:112,113,336,682,687,719,721) ·
  `.ai/AGENTS.md` §5/§16/§17/§18.
- **ADR**: `.ai/.decisions/accepted/ADR-010-csrf-protection-strategy.md` (+ ADR-008/011/013 köprüleri).

---

## 3. Kapsam Disi (bilinçli)

| Kalem | Neden dışarıda | Nereye gider |
|---|---|---|
| HTML/CSS/JS işlevsellik | güvenlik politikası değil | UI katmanı |
| Yeni endpoint tasarımı | API sözleşmesi işi | backend-architect |
| Yetki/rol modeli detayı | dokümante edilmiş yetki ağacı | [[../authorization/index]] (K0100-identitiy) |
| KVKK saklama/imha süresi metni | vault'ta metin yok | `⚠️ ONAY BEKLİYOR` → `K020-comilance` (dogrulama §6) |
| OS patch/izin politikası | ayrı domen | `K000-isletim-sistemi/security` |
| CI gate / deploy | ayrı domen | `K018-devops` |
| Figma token üretimi | `FIGMA_TOKEN` yalnız `.ai/.env.figma` | ui-design |
| ADR-013 rate-limit kotası ayrıntısı | ADR-013 kapsamı | [[../rate-limiting/index]] (P-17 köprüsü) |

**Sınır kuralı**: kapsam dışı konu burada politikaya dönüştürülmez; ayrı node
veya ADR açılır.

---

## 4. Kapsam Disleme Akışı

```text
yeni güvenlik davranışı önerilir
  → index §2 / bu dosya §1 kalemlerinden birine oturuyor mu?
      EVET  → ilgili politika (P-xx) var mı?
                 VAR   → policies-surucu.md §2 tablosuna kod/test satırı ekle
                 YOK   → anayasa §4'e P-19.. ekle (ONAY)
      HAYIR → kapsam dışıdır → node/ADR öner (kapsam dışına yazma)
  → K009 dışına taşan iş: parent'a (K009-security) rapor
```

**İkili kontrol**: `index.md` §2 · bu dosya §1 · anayasa §14 (devir tablosu)
birbirine eşit olmalıdır; uyuşmazlık C1 ihlali gibi raporlanır
(`policies-surucu.md` §5).

---

## 5. Kapsam Sayimi

| Metrik | Değer | Kaynak |
|---|---|---|
| Bağlayıcı kalem (index §2) | 7 | `index.md` §2 |
| Kapsam kalemi (bu dosya §1) | 10 (7 ana + 3 alt: log/rate/bypass) | §1 |
| Politika (anayasa) | 18 (P-01..P-18) | `policies-anayasa.md` §4 |
| Kalem ↔ politika eşleşmesi | 18/18 | §1 |
| Kapsam dışı kayıt | 8 | §3 |
| Açık kapsam sorusu | 4 (KVKK metni, parola min, Origin fail-open, JWT stub) | dogrulama §6 |

---

## 6. Degisiklik Kurali

Kapsam genişlemesi (yeni kalem) yalnız ONAY ile olur ve sırayla güncellenir:
`index.md` §2 → `policies-anayasa.md` (yeni P-xx, §4 + ilgili § altı) →
`policies-surucu.md` §2 → `policies-kapsam.md` §1. Tek dosya değişimi kapsam
drifti üretir (C1 ihlali gibi raporlanır — surucu §5).

Tek dosya **daraltma** (kalemin devri) da aynı sıra ile yapılır; devir anayasa
§14'te kayıtlı kalmalıdır.

---

## 7. Node Sahiplik ve Devir Matrisi

| Politika grubu | Sahip node | İletişim yolu | Kapı |
|---|---|---|---|
| Parola (P-01..P-04) | `K0100-identitiy` / [[../authentication/index]] | anayasa §14 devir satırı | auth test |
| Kripto (P-05..P-06) | [[../encryption/index]] | surucu §2 + ADR-022 zinciri | security-audit |
| Sır (P-07) | [[../secrets/index]] | `.env` envanteri (değer yazılmaz) | olay kaydı |
| Katman (P-08) | bu çocuk (surucu §3) + `K018-devops` (CI) | pipeline test | revert + log ERROR |
| Fail-safe (P-09) | bu çocuk (surucu §6) | ADR-010/013 | RED/OPEN |
| OWASP (P-10) | [[../security-testing/index]] + [[../audit/index]] | QA gate raporu | BLOCK |
| CSRF/CSP (P-11..P-15) | ADR-010 (`.ai/.decisions/accepted/`, node yok) | ADR revizyonu (ONAY) | 403 / başlık testi |
| Log (P-16) | [[../audit/index]] | LoggerRedactionTest | RED |
| Rate-limit (P-17) | [[../rate-limiting/index]] (ADR-013) | ADR gerekçe + log | BLOCK |
| Bypass (P-18) | [[../authorization/index]] + ADR-008 | ADR değişikliği (ONAY) | RED |
| KVKK metni | `K020-comilance` | `⚠️ ONAY BEKLİYOR` (dogrulama §6) | üst karar |

**Devir kuralı**: sahip node'a taşınan iş anayasa §14'te satır olarak kalır; silinmez.
Bu çocuk yalnız bu matristeki kapıları izler, sahiplerinin içeriğine girmez (§3).

## 8. Kapsam Onay Listesi

- [x] 7 index kalemi §1'e eşlendi (parola · kripto · sır · katman · fail-safe · OWASP · CSRF/CSP).
- [x] Eklenen 3 kalem (P-16 log · P-17 rate · P-18 bypass) anayasa §4 ile kapsandı.
- [x] Kapsam dışı 8 kalem sahibi node/ADR ile işaretlendi (§3).
- [ ] KVKK saklama metni — `⚠️ ONAY BEKLİYOR` (K020-comilance).
- [ ] index §1'deki `.ai/CLAUDE.md §Security` atfı — `⚠️ VERIFICATION REQUIRED` (anayasa §4 notu).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
