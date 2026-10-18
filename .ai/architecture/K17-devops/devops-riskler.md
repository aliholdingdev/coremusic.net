---
title: "K17-devops — Riskler"
node_id: K17-devops/devops-riskler.md
node_type: subdomain
status: draft
parent_id: K17-devops/index.md
children: []
depends_on:
  - K009-security
  - K18-testing
evidence:
  - ".ai/architecture/K17-devops/devops-master.md §G.1 (11 çelişki) · §G.2 (8 bekleyen ADR) · 2026-10-18"
  - ".gitleaks.toml:3-4/13-18 (allowlist sınırı) · 2026-10-18"
  - ".github/workflows/ci.yml:167/183 (soft gate continue-on-error) · 2026-10-18"
  - ".workflows/deployment.md:260 (insan onayı) · 2026-10-18"
validation: "2026-10-18 — her satır Olasılık×Etki×Azaltma×Sahip; §4'te 11 çelişki + 8 bekleyen ADR bağlantılı"
open_questions: "⚠️ CI geçti mi doğrulanmadı · ⚠️ CD modeli/ortam sayısı ADR bekliyor · ⚠️ staging YOK"
group: mimari
---

# K17-devops — Riskler

## §1 ELI15

Burada **pipeline ve yayının ters gitme** yolları var: kapılar yumuşak olduğu
için hata sessiz geçer, secret allowlist'i şişer, deploy onayı bypass edilir,
çelişkiler bayat bilgiye dönüşür. Her satır **Olasılık × Etki × Azaltma × Sahip**
taşır; kanıt yoksa **UNKNOWN**.

## §2 Kapsam

- K17-devops (ci.yml · secret-scan.yml · .gitleaks.toml · deployment.md · §G kayıtları).
- K009-security / K18-testing ile kesişenler sahiplikte belirtilir.

## §3 Ölçek

Olasılık/Etki 1–5 · **P = O×E**: ≥12 kırmızı · 6–11 sarı · ≤5 yeşil.

## §4 Risk Kayıtları

### R-01 — CI'ın geçtiği doğrulanmadı ("yeşil" iddiası kanıtsız)
- **Olasılık:** 4 · **Etki:** 4 · **Öncelik:** 16 (kırmızı)
- **Tehdit:** Pipeline dosyaları var, ama son çalışma geçti mi bilinmiyor;
  "CI yeşil" denirse zero-hallucination ihlali + bozuk merge riski.
- **Azaltma:** CI sonucu **kanıtla** raporlanır (Actions run ekranı/URL); master
  §25.2 notu da "çalışma durumu doğrulanmadı" der.
- **Sahip:** devops-engineer + qa-engineer.
- **Durum:** ⚠️ açık (U-01, `devops-bilesenler.md` §10).

### R-02 — Soft gate'lerin kalıcı yumuşaklığı (A6/A7)
- **Olasılık:** 4 · **Etki:** 3 · **Öncelik:** 12 (kırmızı)
- **Tehdit:** Kapsama/e2e `continue-on-error: true` (`ci.yml:167/183`) — sürekli
  yumuşak kalırsa hatalar hiçbir zaman kırmızıya dönmez; test borcu büyür.
- **Azaltma:** Soft gate kararı bilinçli (D-12/D-13); **geri dönüş tarihi**
  (soft→hard) kararı tanımlanmalı (⚠️ tarih yok).
- **Sahip:** qa-engineer (kapsam) + devops-engineer (pipeline).

### R-03 — Allowlist'in şişmesi (gitleaks bypass kirliliği)
- **Olasılık:** 3 · **Etki:** 5 · **Öncelik:** 15 (kırmızı)
- **Tehdit:** Üretim kodu allowlist'e girerse sır taraması körleşir
  (`.gitleaks.toml:3-4` kuralı ihlal).
- **Azaltma:** ADR-034 §5.2/§4.1-R6 kuralı (allowlist yalnız test) + config
  yorumunda yazılı yasak; allowlist değişikliği security review (D-4 sahiplik).
- **Sahip:** security-engineer.
- **Kalan boşluk:** Allowlist'e **ekleme** yapan PR'ların otomatik denetimi
  UNKNOWN (elle review varsayımı).

### R-04 — CD modeli belirsizliği: insan onayı ↔ otomatik CD
- **Olasılık:** 3 · **Etki:** 5 · **Öncelik:** 15 (kırmızı)
- **Tehdit:** `deployment.md:260` onay yasağı ↔ otomatik CD önerisi çelişir;
  kural netleşmeden otomasyon yazılırsa onaysız üretim yayını riski doğar.
- **Azaltma:** §G.2/1 ADR bekliyor — otomasyon **yazılmaz**; bu arada onay
  zorunlu (D-22).
- **Sahip:** devops-engineer + Vault Steward (ADR).

### R-05 — staging yokluğu: ortam 3↔4 çelişkisi
- **Olasılık:** 3 · **Etki:** 4 · **Öncelik:** 12 (kırmızı)
- **Tehdit:** staging alt-alan diskte YOK (§G.2/3); ortam sayısı çelişkisi
  (§G.1/4) → doğrudan production'a yayılma zorunluluğu.
- **Azaltma:** §G.2/3-4 ADR'leri kapatılana kadar doğrudan üretim yayını
  **onaylı** (D-22); test ortamı alternatifi belgelenir.
- **Sahip:** devops-engineer + network (alt-alan, K010 kesişimi).

### R-06 — Environment secret ayrımı yok (PROD_*/STAGING_*)
- **Olasılık:** 3 · **Etki:** 5 · **Öncelik:** 15 (kırmızı)
- **Tehdit:** deploy job'larında `environment:` 0 isabet (§G.2/5) → secret'lar
  ortam ayrımı olmadan kullanılabilir; sızıntı/yanlış-ortam riski (K009 kesişimi).
- **Azaltma:** §G.2/5 ADR bekliyor; bu arada secret'lar `.env`/env substitution
  ile (yazılmaz) ve tarama (D-19) aktif.
- **Sahip:** security-engineer + devops-engineer.

### R-07 — Bayat doküman → yanlış karar (job sayısı, registry sayımı)
- **Olasılık:** 4 · **Etki:** 3 · **Öncelik:** 12 (kırmızı)
- **Tehdit:** `.github/AGENTS.md:191` "3 job" ↔ gerçekte 6 (§G.1/5) ·
  `index.md:462` "17 · 2.819" ↔ 23 · 8.877 (§G.1/9) → ekip bayat sayımla plan yapar.
- **Azaltma:** Çelişkiler master §G.1'de kayıtlı; **disk kazanır**; index.md
  güncellemesi kullanıcı onayı bekliyor (§G.2/8, beyaz-liste dışı).
- **Sahip:** Vault Steward.
- **Not:** `index.md` bu pakette **yazılmaz** (yasak) → onay orkestratör/kullanıcı.

### R-08 — Test/pipeline bağımlılık halkasının kopması (needs)
- **Olasılık:** 2 · **Etki:** 4 · **Öncelik:** 8 (sarı)
- **Tehdit:** php-test → php-lint bağımlılığı (D-11, `ci.yml:68`) gevşerse testler
  lint geçmeden koşar; kapı sırası bozulur.
- **Azaltma:** `needs` yapısı + şablon (D-24); workflow değişikliği onaylı (D-5).
- **Sahip:** devops-engineer.

### R-09 — Tam geçmiş taramasının atlanması (fetch-depth)
- **Olasılık:** 2 · **Etki:** 4 · **Öncelik:** 8 (sarı)
- **Tehdit:** checkout `fetch-depth: 0` (D-19, `secret-scan.yml:26`) değişirse
  geçmiş commit'lerdeki sırlar taranmaz.
- **Azaltma:** tarama işi kapsamı güvenlik (D-4); config/kapsam değişikliği
  security review; gitleaks config kökten okunur (D-20).
- **Sahip:** security-engineer.

### R-10 — Rollback'in denenmemiş olması
- **Olasılık:** 3 · **Etki:** 4 · **Öncelik:** 12 (kırmızı)
- **Tehdit:** Rollback prosedürü yazılmış (D-23, deployment.md:212) ama **denendi
  mi** doğrulanmadı → kriz anında bozulma.
- **Azaltma:** Staging ADR (R-05) kapandığında rollback tatbikatı; tatbikat
  kanıtı olmadan "hazır" denmez.
- **Sahip:** devops-engineer + sre-engineer.
- **Durum:** ⚠️ tatbikat kanıtı **UNKNOWN**.

### R-11 — Artefakt/registry modelinin belirsizliği
- **Olasılık:** 3 · **Etki:** 3 · **Öncelik:** 9 (sarı)
- **Tehdit:** git-snapshot / artifact-registry / imaj üçlüsünden hangisi
  seçilmedi (§G.2/2) → release izlenebilirliği zayıf.
- **Azaltma:** §G.2/2 ADR bekliyor; bu arada etiket + sürüm damgası iskeleleri
  (kod-ornekleri §11-§12) izlenebilirliği kısmen taşır.
- **Sahip:** devops-engineer + platform-engineer.

## §5 Çelişki-Öncelik Eşlemesi (master §G.1 → risk)

| Çelişki (#) | İlgili risk | Durum |
|-------------|-------------|-------|
| §G.1/1 depo | R-01 (kanıt zinciri) | düzeltildi |
| §G.1/2 config | R-03 | gövde korundu |
| §G.1/3 CD | R-04 | ADR bekliyor |
| §G.1/4 ortam | R-05 | ADR bekliyor |
| §G.1/5 job sayısı | R-07 | doküman bayat |
| §G.1/9 index sayım | R-07 | açık — onay bekliyor |
| §G.1/11 ölçüm | R-07 | FM güncellendi |

## §6 Öncelik Matrisi (özet)

| Risk | O | E | P | Renk | Sahip |
|------|---|---|---|------|-------|
| R-01 | 4 | 4 | 16 | kırmızı | devops-engineer |
| R-03 | 3 | 5 | 15 | kırmızı | security-engineer |
| R-04 | 3 | 5 | 15 | kırmızı | devops-engineer |
| R-06 | 3 | 5 | 15 | kırmızı | security-engineer |
| R-02 | 4 | 3 | 12 | kırmızı | qa-engineer |
| R-05 | 3 | 4 | 12 | kırmızı | devops-engineer |
| R-07 | 4 | 3 | 12 | kırmızı | Vault Steward |
| R-10 | 3 | 4 | 12 | kırmızı | devops-engineer |
| R-11 | 3 | 3 | 9 | sarı | devops-engineer |
| R-08 | 2 | 4 | 8 | sarı | devops-engineer |
| R-09 | 2 | 4 | 8 | sarı | security-engineer |

## §7 Bilinmeyenler (⚠️ VERIFICATION REQUIRED)

| # | Bilinmeyen | Risk |
|---|-----------|------|
| U-01 | CI son çalışması geçti mi | R-01 |
| U-02 | Soft gate'lerin hard'a dönüş tarihi | R-02 |
| U-03 | Allowlist ekleme PR denetimi | R-03 |
| U-04 | Rollback tatbikatı | R-10 |
| U-05 | Artefakt modeli seçimi | R-11 |
| U-06 | CD modeli / ortam sayısı / staging | R-04/R-05/R-06 |

## §8 İzleme ve Kapanış

- Kırmızılar `devops-test-stratejisi.md` §5 kapı senaryolarına bağlıdır.
- U-01…U-06 yalnız kanıtla kapanır; kapanışta "kapatıldı (kanıt: …)" işaretlenir.

## §9 İlgili Dosyalar

- `devops-kararlar.md` §7-§8 — çelişki + bekleyen ADR kayıtları
- `devops-test-stratejisi.md` — kapı doğrulamaları
- `../../K009-security/` · `../../K18-testing/` · `../../K010-network/`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
