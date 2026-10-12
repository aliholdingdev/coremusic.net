---
title: "K018-devops — Bağımlılıklar"
node_id: K018-devops
node_type: domain-dependencies
status: draft
updated: 2026-10-12
version: 1.0.0
---

# K018-devops — devops-bagimliliklar.md

> **Etiketler:** kanıtlı = dosya/okuma gösterildi · planlanan = mimari yargı · vault beyanı = bağımsız ölçüm yok.

## §1 Amaç

K018-devops'un depends_on/used_by haritası, entegrasyon sınırları ve riskli bağımlılıkları.
Kanıt temeli: ci.yml + secret-scan.yml okuması + .github/AGENTS.md (2026-10-12).

## §2 depends_on (bu domain kime muhtaç)

| # | Hedef | Etiket | Gerekçe | Kanıt / dayanak |
|---|---|---|---|---|
| 1 | K019-testing | kanıtlı | CI test job'ları phpunit/vitest/e2e koşar | ci.yml php-test · js-test · js-e2e job'ları (okundu 2026-10-12) |
| 2 | K009-security | kanıtlı | GitLeaks secret taraması güvenlik domaininin iş birliği | secret-scan.yml (okundu) + .github/AGENTS.md §3.1 sahiplik |
| 3 | PHP/Node toolchain | kanıtlı | composer + npm kurulumları | ci.yml composer install ×3 · npm ci · Node 26 · PHP 8.4 |
| 4 | GitHub Actions platformu | kanıtlı | runner/aksiyon altyapısı | actions/checkout@v4 · setup-php@v2 · cache@v4 · setup-node@v4 · gitleaks-action@v2 |
| 5 | K017-observability | planlanan | alert → pipeline geri beslemesi | mimari yargı (ADR gerekli) |

## §3 used_by (bu domain kimleri besliyor)

| # | Kullanıcı | Etiket | Gerekçe |
|---|---|---|---|
| 1 | Tüm üretim domainleri (K003–K015) | planlanan | her push/PR bu kapıdan geçer |
| 2 | K020-comilance / K020-licence | planlanan | composer-audit + secret-scan uyum kanıtı üretir |
| 3 | Operatör (deploy onayı) | planlanan | .workflows/deployment.md onay akışı (.github/AGENTS.md §7 — doğrulanmadı) |

## §4 Entegrasyon Sınırları

| Sınır | Kural | Kaynak |
|---|---|---|
| K018 ↔ K019 | Pipeline testi **koşar**, testi **tasarlar/test eder** K019 | v2 §2.18/§2.19 |
| K018 ↔ K017 | Uyarı üretimi K017'de; deploy/rollback aksiyonu K018'de | v2 §2.17/§2.18 |
| K018 ↔ K009 | .gitleaks.toml gibi config eklemek ADR/Security onayı ister | .github/AGENTS.md §3.1 |
| K018 ↔ K016 | workflow yml sahipliği DevOps'ta; ajan hook'ları harness'da | .ai/AGENTS.md §5 · .github/AGENTS.md §3.1 |
| Onay kapıları | Yeni workflow: taslak → kullanıcı onayı → uygulama | .github/AGENTS.md §4 kural 2 |

## §5 Riskli Bağımlılıklar

| # | Risk | Etki | Azaltma (öneri) |
|---|---|---|---|
| 1 | **Git deposu YOK** (.git/HEAD 0 isabet, 2026-10-12) | kaynak geçmişi/geri alma/branch koruması yok; rollback/-release children'ları köksüz | K-Space V3 kararı: deponun durumu tescillenecek (geri yükleme mi, yeni init mi) |
| 2 | **CI geçişi kanıtsız** (yalnız dosya var; vault §25.2 uyarısı) | yeşil kapı sanılan şey kırmızı olabilir | GitHub run geçmişi bir sonraki turda kontrol edilecek |
| 3 | **Soft gate'ler** (js-coverage A7 · js-e2e A6 continue-on-error: true) | kapılar görünür ama bloklamıyor | eşikler büyüyünce hard gate (ci.yml yorumu) |
| 4 | **3. taraf aksiyon bağımlılığı** (setup-php · gitleaks-action · cache · setup-node) | upstream kırılması pipeline'ı durdurur | sürüm pinleme politikası (şu an @v2/@v4 major-pin) |
| 5 | **Eski plan referansı** (k13-cicd diskte yok) | ci.yml yorumları ölü referans taşır | yorum temizliği ya da klasörün V3'e taşınması |
| 6 | **.gitleaks.toml YOK** | varsayılan config ile tarama — özel kural yok | Security onayıyla config eklenmeli (.github/AGENTS.md §3.1 kuralı) |

## §6 Açık Sorular (⚠️ VERIFICATION REQUIRED)

1. GitHub repo ayarları (branch protection, environments, secrets) diskte değil — arayüz kanıtı gerekli.
2. deployment/ hangi mekanizmayla? (.workflows/deployment.md okunmadı — sonraki turda.)
3. artifact-management: composer cache dışında artifact saklama politikası var mı?

## §7 Kaynaklar

- v2 spec §2.18 · ADR-097 §2/§5 · .ai/architecture/index.md §5 satır 18
- Disk (2026-10-12): .github/workflows/ci.yml · secret-scan.yml · .github/AGENTS.md · .git/HEAD glob

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
