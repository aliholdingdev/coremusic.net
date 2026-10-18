---
title: "K17-devops — Evrensel Dil"
node_id: K17-devops/devops-evrensel-dil.md
node_type: subdomain
status: draft
parent_id: K17-devops/index.md
children: []
depends_on:
  - K009-security
  - K18-testing
evidence:
  - ".github/workflows/ci.yml iş adları/anahtar kelimeleri (jobs/needs/permissions/concurrency) · 2026-10-18"
  - ".gitleaks.toml:11-18 (allowlist) · .workflows/deployment.md aşamaları · 2026-10-18"
  - "https://semver.org/ · https://keepachangelog.com/en/1.1.0/ (erişim 2026-10-18)"
validation: "2026-10-18 — ≥15 terim · EN/TR + kod karşılığı; resmi TR yoksa işaretli"
open_questions: "⚠️ 'gate/kapı', 'soft gate' TR tekilleri vault'ta yok · ⚠️ ortam adları (staging) çevrilmez kararı"
group: mimari
---

# K17-devops — Evrensel Dil

## §1 ELI15

DevOps konuşurken kullandığımız kelimelerin **tek anlamı**: "gate", "rollback",
"allowlist" dediğimizde herkes aynı şeyi anlasın. Tablo EN ↔ TR ↔ disk karşılığını
verir. Resmi TR karşılık yoksa işaretlenir — uydurma çeviri yazılmaz; kod/CLI
adları hiç çevrilmez.

## §2 Kapsam

- Yalnız K17 terimleri (CI/CD, tarama, deploy).
- K016/lokalizasyon terimleri bu dosyaya kopyalanmaz; gerekiyorsa ilgili K'nin
  kendi sözlüğüne bakılır.

## §3 EN ↔ TR Terim Tablosu

| # | EN Terim | TR Karşılık | Tanım | Disk karşılığı |
|---|----------|-------------|-------|----------------|
| 1 | Pipeline | Boru hattı* | Otomatik iş dizisi | ci.yml (6 job) |
| 2 | Job | İş | Bağımsız çalışma birimi | `jobs:` (ci.yml:24) |
| 3 | Step | Adım | İş içi tek komut | `- name:` satırları |
| 4 | Runner | Çalıştırıcı** | İşin koştuğu makine | `runs-on: ubuntu-latest` |
| 5 | Workflow | İş akışı** | `.yml` dosyasının tamamı | `name: CI Pipeline` |
| 6 | Gate | Kapı** | Şartlı geçiş noktası | PHPStan/PHPUnit adımları |
| 7 | Soft gate | Yumuşak kapı | Başarısızlık yutulur ama görünür | `continue-on-error: true` (:167/:183) |
| 8 | Hard gate | Sert kapı | Başarısızlık durdurur (merge engeli) | zorunlu job'lar |
| 9 | Lint / Static analysis | Kod denetimi / Statik analiz | Kod inceleme, çalıştırma | `composer stan` (ci.yml:62) |
| 10 | Dependency (composer) | Bağımlılık kurulumu | Paket kurulumu | `composer install` (:48/:52/:56) |
| 11 | Audit | Denetim | Bağımlılık güvenlik taraması | `composer audit` (:111) |
| 12 | Coverage | Kapsama (test) | Test kapsama oranı | Vitest coverage (:175) |
| 13 | E2E | Uçtan uca test | Tam akış testi | Playwright (:202) |
| 14 | Secret scan | Sır taraması | Gizli anahtar avı | secret-scan.yml |
| 15 | Allowlist | Beyaz liste | İzin verilen istisnalar | `.gitleaks.toml:11-18` |
| 16 | Redaction | Maskeleme | Hassas veriyi gizleme | `[REDACTED]` (K016 ile aynı) |
| 17 | Checkout depth (fetch-depth) | Geçmiş derinliği | Kaç commit'ın alındığı | `fetch-depth: 0` (secret-scan:26) |
| 18 | Concurrency / cancel-in-progress | Eşzamanlılık / iptal | Aynı ref'te tekillik | ci.yml:17-19 |
| 19 | Permissions (least privilege) | Yetkiler (en az yetki) | Workflow erişim sınırı | ci.yml:14-15 |
| 20 | Deploy | Dağıtım / yayına alma | Sürümü ortama taşıma | deployment.md 8 aşama (:126-199) |
| 21 | Rollback | Geri alma** | Son iyi sürüme dönme | deployment.md:212 |
| 22 | Environment | Ortam | local/development/staging/production | environments (§G.1/4 — 3↔4 açık) |
| 23 | Staging | Sına ortamı** | Yayın öncesi ortam | ⚠️ YOK (§G.2/3) |
| 24 | Release | Sürüm yayımlama | Etiketli çıkış | §G.2/6 (ADR bekliyor) |
| 25 | Versioning / SemVer | Sürümleme | x.y.z biçimi | S-3 (semver.org) |
| 26 | Changelog | Sürüm notu** | Kategori değişiklik listesi | S-4 (Keep a Changelog) |
| 27 | Tag / Artifact | Etiket / Artefakt | Dönüş referansı / çıktı | rollback etiketi |
| 28 | Branch protection | Dal koruma | Merge kuralları | ⚠️ ayarlı mı bilinmiyor (§6 X-02) |
| 29 | Status check | Durum kontrolü | Zorunlu job sonucu | S-5 |
| 30 | Merge | Birleştirme | Dalı ana hatta alma | gate'lerin bağlayıcılığı |

\* Yaygın TR kullanımı "pipeline" **Olduğu gibi korunur** — CLI/dosya adları
çevrilmez; metin içinde "boru hattı (pipeline)" ilk geçişte çift yazılır.
\** Resmi/standart TR karşılığı **yok** (bu vault'un önerisi) → işaretli.

## §4 Kullanım Kuralları

1. İlk geçişte çift yazımla ("yumuşak kapı (soft gate)"); sonrası tek.
2. Kod/CLI/yaml adları çevrilmez: `needs` · `continue-on-error` · `fetch-depth` ·
   `gitleaks` aynen kalır.
3. **soft ≠ hard** ayrımı her zaman görünür: "soft gate" yazılır, "kapı" tek başına
   sert kapı anlamına gelmez (karıştırma → R-02).
4. Ortam adları (staging/production) **çevrilmez** (⚠️ §6 G-02); 3↔4 ortam kararı
   ADR beklerken adlar sabit kalır.
5. Karşılığı olmayan satır (§3 ⚠️: staging, branch protection) → dil eksiği değil,
  **karar/kanıt boşluğudur**; "var" denmez.

## §5 Karar ↔ Terim Eşlemesi + Kaynak

| Karar | Terim(ler) | Kaynak |
|-------|-----------|--------|
| D-8/D-9 | Lint / Static analysis | ci.yml:62 (kod kanıtı) |
| D-11 | Job · Step · hard gate | S-1 + ci.yml:68 |
| D-12/D-13 | Soft gate | ci.yml:167/183 (kod kanıtı) |
| D-17 | Permissions (least privilege) | S-1 + ci.yml:14 |
| D-18 | Concurrency | S-1 + ci.yml:17 |
| D-19 | Checkout depth | S-2 + secret-scan:26 |
| D-21 | Allowlist | S-2 + `.gitleaks.toml:11-18` |
| D-22/D-23 | Deploy · Rollback | deployment.md:126-199/:212 |
| §G.2/6 | Release · SemVer · Changelog | S-3 · S-4 (2026-10-18) |
| §G.2/3-4 | Environment · Staging | ⚠️ ADR bekliyor |
| §6 X-02 | Branch protection · Status check | S-5 (2026-10-13) |

## §6 Bilinmeyenler (⚠️ VERIFICATION REQUIRED)

| # | Bilinmeyen | Not |
|---|-----------|-----|
| G-01 | "gate/soft gate" TR tekilliği | vault sözlüğünde yok |
| G-02 | Ortam adlarının çevrilip çevrilmeyeceği | adlar sabit, karar yok |
| G-03 | §3 ⚠️ satırlarının karşılığı | staging YOK · branch protection kanıtsız |
| G-04 | "rollout/canary" gibi ileri terimler | bu pakette karşılanmadı (kapsam dışı) |

## §7 İlgili Dosyalar

- `devops-bilesenler.md` · `devops-kararlar.md` — terimlerin kullanıldığı yerler
- `devops-web-arastirma.md` — S-1…S-5 kaynakları (erişim tarihli)
- `../K016-observability/observability-evrensel-dil.md` — maske/redaction
  terimleriyle kesişim (tekilleşme kademe-3)

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
