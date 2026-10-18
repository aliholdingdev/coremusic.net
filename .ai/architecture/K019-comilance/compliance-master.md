---
title: "K019-comilance — Compliance Master (Uyum Programı)"
type: master
category: architecture
node_id: K019-comilance/compliance-master
node_type: file
date: 2026-10-12
updated: 2026-10-17
status: draft
version: 1.1.0
authority: "SSOT: .ai/CLAUDE.md §6 · .ai/AGENTS.md §16 · kök AGENTS.md §5 Security · KVKK 6698 · ISO/IEC 27001:2022"
related:
  - "policies/index.md"
  - "controls/index.md"
  - "controls/kod-ornekleri.md"
  - "verification/index.md"
  - "../adr/index.md"
group: mimari
---


# K019-comilance — Compliance Master

## 1. Rol (ELI15)

Bu dosya, CoreMusic'in **hangi kurallara** uymayı taahhüt ettiğini, **hangi
kontrollerin** o kuralları sağladığını ve **hangi tarihte** denetlendiğini tek
sayfada gösterir. Uyum = kural + kontrol + kanıt; üçünden biri eksikse kayıt
`verify-required` kalır.

## 2. Kapsam / Kapsam dışı

> **Kapsam kararı (kullanıcı onayı 2026-10-17):** aktif uyum çerçevesi =
> **KVKK + ISO/IEC 27001:2022**; GDPR yalnız AB veri öznesi tespit edilirse
> koşullu etkinleşir. Karar kaydı: `policies/index.md` §Kapsam kararı.

| Kapsam | Kapsam dışı |
|---|---|
| Güvenlik uyumu (OWASP Top 10:2025 ↔ vault kontrolleri) | Bağımlılık **lisans** uyumu → ayrı lisans denetimi (**klasör diskte YOK** — §7 madde 6) |
| **KVKK** (6698) — m.4/m.12/m.16/m.18 uyum programı | Kod incelemesi (code-reviewer akışı) |
| **ISO/IEC 27001:2022** — ISMS çerçevesi + Annex A (SoA öncesi) | Sertifika iddiası — **hiçbiri yoktur, yazılmaz** |
| Politika kayıtları ve denetim turları | Runtime güvenlik testi (pentest) sonuçları |
| Uyum kanıtlarının saklanması (tarih + kaynak) | AB GDPR tam uygulaması (koşul tespiti yapılmadı → §7 madde 7) |

## 3. Uyum alanları (katalog)

| # | Alan | Dayanak (gerçek dosya/kaynak) | Sorumlu | Durum |
|---|---|---|---|---|
| 1 | Güvenlik (OWASP) | kök `AGENTS.md` §5 Security · OWASP Top 10:2025 (`top10.owasp.org/2025/en` — yeniden doğrulandı 2026-10-17) | security-engineer | kontrol iskeleti — denetlenmedi |
| 2 | Kalite standartları | `@.ai/AGENTS.md` §16 (Backend/UI/Security/Data/Embedded/QA/DevOps hedefleri) | qa-engineer | ölçülmüş değer yok → VERIFY REQUIRED |
| 3 | Vault kuralları / LINT | `@.ai/CLAUDE.md` §6 + 16 Hard Guardrails (kök AGENTS.md §24.2) | MO (vault-updater) | iskelet |
| 4 | Politikalar | `policies/index.md` — 8 kayıt satırı (KVKK + ISO dahil) | Vault Steward | kayıt genişledi · denetim yok |
| 5 | Bağımlılık lisansı | **klasör diskte YOK** — registry §5 madde 23 `dependency-licenses.md` bekliyor | dependency-manager | `⚠️ VERIFY REQUIRED` |
| 6 | Gizli veri / secret yönetimi | kök AGENTS.md §5 (secret yalnız `.env`) · `.github/workflows/secret-scan.yml` (GitLeaks) | devops-engineer | pipeline dosyası var, çalıştığı doğrulanmadı |
| 7 | **Veri koruma (KVKK)** | 6698 sayılı Kanun — m.4 · m.6 · m.12 · m.16 · m.18 (`kvkk.gov.tr`, 2026-10-17 doğrulandı) | Vault Steward + security-engineer | kapsam kararlı · saha denetimi yok (tur-3) |
| 8 | **Bilgi güvenliği yönetimi (ISO 27001:2022)** | Annex A 93 kontrol/4 tema (A.5 37 · A.6 8 · A.7 14 · A.8 34) — secureframe/drata 2026-10-17 | Vault Steward | çerçeve benimsendi · SoA yazılmadı (tur-4) |

> **Not (zero-hallucination):** Bu tabloda "sertifikalıdır / uyumludur" cümlesi
> **yoktur**. Henüz yapılmamış denetim için sonuç yazılmaz.

## 4. Denetim döngüsü (adımlar)

```text
① Politika/gereksinim yaz (policies/) → ② Kontrolü ata (controls/)
        → ③ Kod gövdesini bağla (controls/kod-ornekleri.md — 11 dil)
        → ④ Denetle (verification/ turu) → ⑤ Sonucu kaydet (tarih + kaynak)
        → ⑥ Açık soru kaldıysa status: verify-required
        → ⑦ Karar gerektiriyorsa ADR aç (karar serisi: @.ai/.decisions/draft/ ·
           mimari seri: ../adr/decisions/)
```

- **Kanal:** her tur `verification/index.md`'ye **satır eklenerek** yazılır (append-only).
- **Başarı ölçütü:** kanıt = gerçek dosya yolu / URL / tarih. Tahmin, "muhtemelen",
  "uyumlu olmalı" **yazımaz**.
- **Kapı:** bir kontrol `fail` ise ilgili alan `status: verify-required` → üst karar
  (`.ai/AGENTS.md` §10 eskalasyon).

## 5. Alt klasör kataloğu

| Alt klasör/dosya | Ne taşır | Dosya |
|---|---|---|
| `policies/` | "Ne kural?" — 8 politika satırı (KVKK + ISO + GDPR koşullu) | `policies/index.md` |
| `controls/` | "Nasıl sağlanıyor?" — OWASP A01–A10 + ISO 4 tema + **K000–K013 domain haritası** | `controls/index.md` |
| `controls/kod-ornekleri.md` | "Kodda nasıl gövdelendi?" — 11 dilde kontrol şablonları | `controls/kod-ornekleri.md` |
| `verification/` | "Ne zaman denetlendi?" — tur kayıtları (tur-1 2026-10-12 · tur-2 2026-10-17) | `verification/index.md` |

## 6. Kayıt kuralları

1. Yeni satır yalnız **kanıtla** eklenir (dosya yolu + tarih + kaynak).
2. Mevcut satır **düzeltilmez** — yanlışsa yeni satır + `⚠️` notu eklenir (ADR-042 satır-satır edit).
3. Kaynak çelişirse çelişki `open_questions`a taşınır, biri "kazandırılmaz".
4. `comilance` yazımı değişmez (v2 §3 KURAL 1). **Klasör adı = disk kanıtı** (2026-10-17 kullanıcı kararı).

## 7. Açık sorular (VERIFY REQUIRED)

| # | Soru | Etiket |
|---|---|---|
| ~~1~~ | ~~Uyum kapsamı: KVKK · GDPR · ISO 27001 — hangisi geçerli?~~ | ✅ **KAPANDI 2026-10-17** — KVKK + ISO 27001 (kullanıcı onayı; GDPR koşullu) |
| 2 | `@.ai/AGENTS.md` §16 hedeflerinin (`%100`, `≥%80`, `≥%95`) son ölçümü ne? Rapor yok. | `⚠️ VERIFY REQUIRED` |
| 3 | `secret-scan.yml` / `ci.yml` gerçekten koşuyor mu (CI geçmişi kanıtı yok — kök AGENTS.md §25.2 notu) | `⚠️ VERIFY REQUIRED` |
| 4 | OWASP kontrol haritasındaki `fail`ler için ADR gerekir mi, yoksa düzeltme yeter mi? | `⚠️ VERIFY REQUIRED` |
| 5 | ⚠️ ID CONFLICT (2026-10-17 kısmen kapandı): disk kanıtı esas — klasör `K019-comilance` kalır; **registry index.md §5 madde 21 hâlâ 'K020-comilance'** → registry güncellemesi üst karar | `⚠️ VERIFY REQUIRED` |
| 6 | `K020-licence/` (dependency-licenses.md) diskte **YOK** (2026-10-17 ölçümü) — lisans denetimi hangi dosyada tutulacak? | `⚠️ VERIFY REQUIRED` |
| 7 | GDPR koşulu: AB veri öznesi var mı? Tespit yok → politika #8 şartlı etkin. | `⚠️ VERIFY REQUIRED` |
| 8 | ISO SoA: hangi A.5–A.8 kontrolü CoreMusic'e uygulanır? Ücretli standart metni → birebir numara doğrulaması yok. | `⚠️ VERIFY REQUIRED` |
| 9 | KVKK VERBİS kaydı yapıldı mı (m.16)? Kanıt yok. | `⚠️ VERIFY REQUIRED` |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

## 8. Ek — Rol → denetim haritası

| Denetçi (agent) | Ne denetler | Dayanak | Sıklık |
|---|---|---|---|
| `security-engineer` | OWASP A01–A10 karşılığı vault kontrolleri · auth/CSRF/CSP · crypto algoritması · KVKK m.12 teknik/idari tedbir | kök AGENTS §5 · `.ai/AGENTS.md` §16 | her güvenlik değişikliğinde |
| `qa-engineer` | Test stratejisi, coverage, gate raporu | `.ai/AGENTS.md` §16 (≥%80) | her yayım öncesi |
| `data-engineer` | BCNF, parametreli sorgu, `SELECT *`/ORM yasağı · KVKK m.10 silme/muhafaza · maskeleme view'ı | `.ai/AGENTS.md` §16 · `ADR-002/003` · `kod-ornekleri` §10.3 | şema değişikliğinde |
| `dependency-manager` | Paket/sürüm/lisans denetimi | **klasör yok** → §7 madde 6 | yeni paket eklenince |
| `devops-engineer` | CI/CD, GitLeaks, secret taraması (A03 kapıları) | `.github/workflows/secret-scan.yml` · `kod-ornekleri` §11–§12 | her push |
| `MO (vault-updater)` | Vault LINT + Guardrail uyumu · log append-only | `@.ai/CLAUDE.md` §6 | her oturum kapanışında |

> Sıklıklar **süreç niyetidir**, ölçülmüş SLA **değildir** → `⚠️ VERIFY REQUIRED`.

## 9. Kayıt satırı formatı (policies/controls/verification uyumu)

```text
| tarih | alan (§3 no) | kontrol (controls no) | yöntem | sonuç | kanıt (yol/URL) | durum | sorumlu |
```

- **İzleme (traceability):** her satır bir `policies/` politikasına ve bir
  `controls/` kontrolüne bağlanır; bağı olan satır **eksik kayıt** sayılır.
- **Sonuç değerleri:** `geçti` · `kaldı` · `VERIFY REQUIRED` — üçünden başka değer yok.

## 10. Sık yapılan hatalar (yasak — red team notu)

1. Denetim **yapılmadan** "uyumlu" yazmak → en ağır ihlal (kanıtsız iddia).
2. Eski satırı **düzeltip silmek** → append-only ihlali (ADR-042).
3. Çelişen iki kaynağı "biri doğru" diye birleştirmek → çelişki `open_questions`a gider.
4. Uyum alanını lisansla karıştırmak → kapsam hatası (lisans ayrı denetim — §7 madde 6).
5. Disk kanıtı ile registry çelişince registry'yi sessizce değiştirmek → üst karar (§7 madde 5).

## 11. K000–K013 uyum haritası (işaretleme)

Haritanın tamamı (14 domain · OWASP eşlemesi · KVKK teması · sorumlu agent):
`controls/index.md` §"K000–K013 domain uyum haritası". Özet:

| Kümülatif | Domain | KVKK teması |
|---|---|---|
| K000–K005 (OS/donanım/firmware) | 6 domain | yok (cihaz katmanı) |
| K006–K007 (YZ/veri) | 2 domain | **Evet** — özel nitelikli veri · saklama/silme |
| K008–K013 (domain/security/network/identity/app/services/integration) | 6 domain | **Evet** — kimlik · m.12 tedbir · aktarım |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
