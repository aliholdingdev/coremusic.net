---
title: "K018-devops — Master (Domain Anayasa)"
node_id: K018-devops
node_type: domain-master
status: draft
authority: "Derived — SSOT: v2 spec §2.18 + ADR-097 (draft)"
updated: 2026-10-12
version: 1.0.0
---

# K018-devops — devops-master.md

## §1 Rol (ELI15)

K018-devops, kodun **üretilip yayılma yoludur**: depoda ne olur, push'ta ne çalışır, hangi testler
kapı olur, hangi ortama çıkar, nasıl geri alınır. Testleri **yazmaz** (K019), yalnız **koşar**.

## §2 Kapsam / Sınır (ne DEĞİL)

| Kapsam (bu domain) | Kapsam DIŞI |
|---|---|
| CI kapısı: lint/test/audit koşumu | Testlerin tasarımı ve içeriği → **K019-testing** (ci.yml php-test/js-test = K018'in K019'la entegrasyonu) |
| CD, ortam, deployment, rollback | Sistem logu/metrik/aler → **K017-observability** (uyarı K017, aksiyon K018) |
| Secret taraması (GitLeaks) | Auth/encryption politikası → **K009-security** (secret-scan.yml = iş birliği, sahiplik §5'te Security+DevOps) |
| Workflow dosyaları sahipliği (*.yml) | Ajan boot/hooks → **K016-AI** (hook'lar harness'da, workflow'lar GitHub'da) |

**Sahiplik kaydı (.github/AGENTS.md §3.1, 2026-10-03):** ci.yml = DevOps (birincil) + QA (test kapsamı) ·
secret-scan.yml = Security (kapsam) + DevOps (yml) · yeni .yml = taslak → kullanıcı onayı → uygulama.

## §3 Kademe-2 Çocuk Tablosu (v2 §2.18 — birebir, 10 çocuk)

| # | Çocuk | Rol (1 cümle) | Kanıt durumu |
|---|---|---|---|
| 1 | source-control/ | Depo, branch, commit kuralları | ⚠️ depo YOK (.git/HEAD 0 isabet) |
| 2 | build/ | Derleme ve bağımlılık kurulumu (composer/npm) | ✅ kanıtlı |
| 3 | ci/ | Push/PR kapıları (lint/test/audit) | ✅ kanıtlı (ci.yml okundu) |
| 4 | cd/ | Yayına sürme otomasyonu | ⚠️ deploy job'ı workflows'da YOK |
| 5 | environments/ | Ortam tanımları ve değişkenleri | ⚠️ environment: bloğu YOK |
| 6 | deployment/ | Yayın mekaniği + onay akışı | 🔶 .workflows/deployment.md referansı doğrulanmadı |
| 7 | rollback/ | Geri alma | ⚠️ kanıt bekliyor |
| 8 | release-management/ | Sürüm + release notu | ⚠️ kanıt bekliyor |
| 9 | artifact-management/ | Artifact saklama/paylaşma | 🔶 cache var; upload-artifact YOK |
| 10 | infrastructure-testing/ | Altyapı testi | ⚠️ kanıt bekliyor |

## §4 Teknoloji / Ekosistem (disk kanıtları — ölçüm 2026-10-12)

| Yüzey | Kanıt (gerçek dosya / satır) | Durum |
|---|---|---|
| Workflow envanteri | .github/workflows/ = 2 dosya: ci.yml · secret-scan.yml (glob 2026-10-12; önceki ölçüm .ai/AGENTS.md §25.2 → 2026-09-27 ile TUTARLI) | ✅ IMPLEMENTED |
| CI jobs | ci.yml: **6 job** — php-lint · php-test (matrix: shared · auth.coremusic.net --testsuite Unit) · composer-audit · js-test (vitest) · js-coverage (soft, continue-on-error, A7 kararı) · js-e2e (Playwright, soft, A6 kararı) | ✅ IMPLEMENTED (dosya) |
| CI araçları | PHP 8.4 (env), actions/checkout@v4, shivammathur/setup-php@v2, actions/cache@v4, actions/setup-node@v4, Node 26 | ✅ IMPLEMENTED (dosya) |
| Secret taraması | secret-scan.yml: gitleaks job — gitleaks/gitleaks-action@v2, fetch-depth: 0; yorum: .gitleaks.toml YOK (varsayılan config) | ✅ IMPLEMENTED (dosya) |
| Şablon zinciri | .ai/.templates/infrastructure/github-actions-template.md §3.2 — varlık doğrulandı (2026-10-12); ci.yml satır 2 şablona atıf yapıyor | ✅ |
| Sahiplik/yetki dokümanı | .github/AGENTS.md §3.1 (5 dosya sahiplik tablosu — ölçüm 2026-10-03) · §3.3 yasaklar (subagent commit YOK, onaysız yml değişikliği YOK) | ✅ (doküman) |
| .github envanteri | 8 dosya: AGENTS.md · CONTEXT.md · CLAUDE.md · WORKFLOW.md · workflows/ci.yml · workflows/secret-scan.yml · ISSUE_TEMPLATE/CLAUDE.md · ISSUE_TEMPLATE/01-bug-report.md (glob 2026-10-12) | ✅ |
| Git deposu | .git/HEAD glob = **0 isabet** (2026-10-12) + ortam bildirimi "Is directory a git repo: no" → **depo YOK**; vault commit kayıtları (.ai/AGENTS.md §25.5) eski dönem arşividir | ⚠️ KRİTİK BULGU |
| CI geçiş durumu | .ai/AGENTS.md §25.2: "workflow dosyaları mevcut, çalışma durumu doğrulanmadı — CI'nin geçtiği doğrulanmadı" | ⚠️ doğrulanmadı |
| Eski plan referansı | ci.yml satır 3: .ai/architecture/k13-cicd/README.md — bu klasör V3 registry'de YOK (glob .ai/architecture/**) | ⚠️ tarihsel referans |

## §5 Bağımlılık Özeti (etiketli — detay: devops-bagimliliklar.md)

| Yön | Hedef | Etiket | Gerekçe |
|---|---|---|---|
| depends_on | K019-testing | kanıtlı (ci.yml) | CI test job'ları K019 yetkinliğini koşar |
| depends_on | K009-security | kanıtlı (secret-scan.yml) | GitLeaks kapısı güvenlik domaininin iş birliği |
| used_by | tüm üretim domainleri | planlanan | her kod değişimi bu yoldan geçer |
| used_by | K017-observability | planlanan | alert → pipeline aksiyonu (sınır: uyarı K017, aksiyon K018) |

## §6 Doğrulama Özeti (detay: devops-dogrulama.md)

- ✅ **Doğrulandı (dosya okuma):** ci/ (6 job) · build/ (composer/npm adımları) · secret tarama iş birliği.
- 🔶 **Kısmi/negatif:** environments (blok yok) · artifact-management (upload yok) · deployment (doküman referansı doğrulanmadı).
- ⚠️ **Uyaran:** source-control (depo yok!) · cd/rollback/release/infrastructure-testing (kanıt yok).
- **En kritik açık:** CI dosyaları var ama **CI'nin geçtiğine dair kanıt yok** (vault uyarısıyla tutarlı).

## §7 Açık Sorular (⚠️ VERIFICATION REQUIRED)

| # | Soru | Etkilenen çocuk |
|---|---|---|
| 1 | CI gerçekten çalışıyor mu, son run geçti mi? (GitHub arayüzü/branch koruması diskte değil) | ci/ |
| 2 | Git deposu bilinçli mi yok? (yedek/taşınma dönemi?) — K-Space V3 kararı gerekli | source-control/ |
| 3 | cd/ ve deployment/ için hedef model ne? (manual deploy mu, otomatik CD mi) | cd/ · deployment/ |
| 4 | k13-cicd planı V3 registry'sine taşınacak mı, yoksa arşiv mi? | ci/ |
| 5 | release/artifact stratejisi tanımlı mı? | release-management/ · artifact-management/ |
| 6 | infrastructure-testing kapsamı (Docker? altyapı as-code?)? | infrastructure-testing/ |

## §8 Kaynaklar

| Kaynak | Referans |
|---|---|
| v2 spec | coremusic-katmanli-mimari-agaci-v2.md §2.18 |
| ADR-097 (draft) | §2 K1–K7 · §5 isimlendirme |
| Registry | .ai/architecture/index.md §5 satır 18 |
| Disk (2026-10-12) | .github/workflows/ci.yml · secret-scan.yml · .github/ envanteri · .git/HEAD (0 isabet) |
| Sahiplik dokümanı | .github/AGENTS.md (2026-10-03) · .ai/AGENTS.md §5/§16/§25.2 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode · 2026-10-12
