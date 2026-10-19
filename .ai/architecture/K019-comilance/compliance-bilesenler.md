---
node_id: K019-comilance/compliance-bilesenler.md
node_type: subdomain
status: draft
parent_id: K019-comilance
children: []
depends_on:
  - K007-data
  - K009-security
  - K017-devops
  - K18-testing
evidence:
  - "compliance-master.md §5 (Alt klasör kataloğu) + §8 (Rol → denetim haritası) — okundu 2026-10-18"
  - "policies/index.md — 8 politika satırı — okundu 2026-10-18"
  - "controls/index.md — OWASP A01–A10 · ISO 4 tema · K000–K013 haritası · kod-ornekleri — okundu 2026-10-18"
  - "verification/index.md — tur-1 (2026-10-12) · tur-2 (2026-10-17) — okundu 2026-10-18"
  - "controls/kod-ornekleri.md — 11 dil kontrol şablonu — referans 2026-10-18"
validation:
  - "2026-10-18 — her bileşen satırı gerçek disk dosyasına bağlandı (6 md envanteri)"
  - "2026-10-18 — bileşen ↔ sorumlu agent eşlemesi master §8 ile çapraz okundu"
  - "2026-10-18 — kod bileşeni yalnızca şablon/şablonatörlüğü ifade eder; üretim kodu iddiası yok"
open_questions:
  - "policies/ altına politika MD'leri (kvkk-politikasi.md vb.) v2 şekli gereği üretilmedi — children: [] korunur (⚠️ VERIFICATION REQUIRED)"
  - "verification/ tur sayısı artacak mı, otomatik kapıyı kim tetikler? — otomasyon tanımsız (⚠️ VERIFICATION REQUIRED)"
  - "controls/kod-ornekleri.md şablonları hangi CI job ile denetlenecek? — kapı K17-devops'ta tanımsız (⚠️ VERIFICATION REQUIRED)"
group: mimari
---

# compliance-bilesenler — Bileşen Kataloğu

> Dayanak: `compliance-master.md` §5 · §8. Bu dosya K019-comilance'ın
> **fiziksel bileşenlerini** (dosya/dizin/araç/görev) ve her birinin
> **sorumluluk/sahiplik** eşlemesini taşır.

## §1 ELI15

> **K019-comilance dört raftan oluşur: kural defteri (policies), kontrol
> haritası (controls), kod şablonu (kod-ornekleri) ve denetim günlüğü
> (verification).**

Her rafta tek bir soru cevaplanır: policies "ne kural?", controls "nasıl
sağlanıyor?", kod-ornekleri "kodda nasıl gövdelendiriliyor?", verification "ne
zaman denetlendi, sonuç ne?". Raflar birbirini tamamlar; bir raftaki boşluk
diğerinde kapatılmaz.

## §2 Bileşen tablosu (dosya/dizin düzeyi — disk envanteri 2026-10-17: 6 md)

| # | Bileşen | Tür | Ne taşır | Sahip (agent) | Durum |
|---|---|---|---|---|---|
| B1 | `index.md` | dosya (Kademe-1) | Klasör künyesi · çocuk kataloğu · K000–K013 özeti · §BAĞLANTILAR · §GRUPLAR | Vault Steward | ✅ mevcut (2026-10-18 güncelli) |
| B2 | `compliance-master.md` | dosya (Kademe-1) | Uyum programı anayasası: alanlar · sorumlular · denetim döngüsü · kayıt kuralları · açık sorular | Vault Steward | ✅ mevcut (v1.1.0) |
| B3 | `policies/index.md` | dizin+dosya (Kademe-2) | 8 politika satırı + kapsam kararı kaydı | Vault Steward + security-engineer | ✅ mevcut (draft) |
| B4 | `controls/index.md` | dizin+dosya (Kademe-2) | OWASP A01–A10 · ISO Annex A 93/4 · vault içi kalite · K000–K013 domain haritası | security-engineer | ✅ mevcut (draft) |
| B5 | `controls/kod-ornekleri.md` | dosya (Kademe-3) | 11 dilde kontrol kod şablonları (PHP·JS·Python·Go·C#·Java·C/C++·Rust·SQL·Bash·YAML+Rego) | security-engineer + backend-developer | ✅ mevcut (draft şablon) |
| B6 | `verification/index.md` | dizin+dosya (Kademe-2) | Denetim turu kayıtları (append-only) — tur-1 2026-10-12 · tur-2 2026-10-17 | qa-engineer + Vault Steward | ✅ mevcut (draft) |

## §3 Araç/bağımlılık bileşenleri (disk kanıtlı — bu K'nın kullandığı)

| # | Araç/Dosya | Rol | Kanıt yolu | K18-testing mi? |
|---|---|---|---|---|
| T1 | `.github/workflows/secret-scan.yml` | GitLeaks sır taraması (OWASP A03/A09 kapısı) | `.github/workflows/secret-scan.yml` (gitleaks-action@v2) | hayır — K17-devops |
| T2 | `.gitleaks.toml` | GitLeaks kural konfigürasyonu | repo kökü | hayır — K17-devops |
| T3 | `composer-audit` (ci.yml job) | Bağımlılık açığı taraması (A03) | ci.yml job listesi (2026-10-12 okundu) | hayır — K17-devops |
| T4 | `phpunit` + `phpstan` | Test/static analysis kanıtı (QA politikası #2) | shared/composer.json (phpstan ^1.10) | evet — K18-testing |
| T5 | `@playwright/test` + `axe-playwright` | E2E + erişilebilirlik denetimi (WCAG politikası #5) | root package.json | evet — K18-testing |

> **Sınır notu:** T1–T5 bu K'nın **dayandığı** araçlardır; kurulumu/çalıştırılması
> K17-devops (CI) ve K18-testing (test) sorumluluğundadır. K019 yalnız
> "bu kapı var mı, sonucu ne" satırını kaydeder.

## §4 Görev bileşenleri (kim ne denetler — master §8 özet)

| Görev | Ne denetler | Dayanak | Sıklık |
|---|---|---|---|
| security-engineer | OWASP A01–A10 karşılığı · auth/CSRF/CSP · crypto · KVKK m.12 tedbir | kök AGENTS §5 · `.ai/AGENTS.md` §16 | her güvenlik değişikliğinde |
| qa-engineer | Test stratejisi · coverage · gate raporu | `.ai/AGENTS.md` §16 (≥%80) | her yayım öncesi |
| data-engineer | BCNF · parametreli sorgu · PII saklama/silme · KVKK m.10 | `.ai/AGENTS.md` §16 · ADR-002/003 | şema değişikliğinde |
| dependency-manager | Paket/sürüm/lisans denetimi | **klasör yok** → açık soru | yeni paket eklenince |
| devops-engineer | CI/CD · GitLeaks · secret taraması | secret-scan.yml · ci.yml | her push |
| MO (vault-updater) | Vault LINT + Guardrail uyumu · log append-only | `.ai/CLAUDE.md` §6 | her oturum kapanışında |

> Sıklıklar **süreç niyetidir**; ölçülmüş SLA değildir → `⚠️ VERIFY REQUIRED`.

## §5 Bileşen ↔ OWASP Aile eşlemesi (hangi bileşen hangi aileyi gövdelendirir)

| Bileşen | Gövdelendiği OWASP ailesi | Not |
|---|---|---|
| `policies/index.md` #1 | A01–A10 (genel alt sınır) | kural satırı |
| `controls/index.md` | A01–A10 + ISO A.5–A.8 | harita satırı |
| `controls/kod-ornekleri.md` §2.1–§3.3 | A05 (injection) · A04 (crypto) · A07 (auth) | PHP/JS şablonları |
| `controls/kod-ornekleri.md` §4–§9 | A04/A05/A07/A10 — Go·C#·Java·C/C++·Rust·Python | dil bazlı şablonlar |
| `controls/kod-ornekleri.md` §10 | A08/A09 (integrity · logging) | SQL tetikleyici/audit |
| `controls/kod-ornekleri.md` §11–§12 | A02/A03 (misconfiguration · supply chain) | Bash gitleaks · YAML+Rego policy-as-code |
| `verification/index.md` | tümü — sonuç satırı | tur-1/tur-2 kaydı |

## §6 Bileşen olmayanlar (bu dosya kapsamında DEĞİL)

| Dışarıda | Nerede | Neden |
|---|---|---|
| Politika MD'leri (kvkk-politikasi.md vb.) | `policies/` children'ı boş — v2 şekli üretilmedi | uydurma politika metni yazılmaz |
| SoA belgesi (Statement of Applicability) | ayrı ADR + onay gerektirir | ISO standart metni ücretli; birebir numara doğrulanamadı |
| Denetim sonuç veritabanı / dashboard | — | K16-observability olabilir; K019 append-only md yeter sayıldı |
| Uyum eğitimi / farkındalık içeriği | ISO A.6 teması iskelette | içerik üretilmedi |

## §7 Doğrulama

- [x] 6 md envanteri disk ölçümüyle birebir (2026-10-17 · kök 6)
- [x] Her bileşen gerçek dosya yoluna bağlandı
- [x] Görev bileşenleri master §8 ile çapraz okundu
- [x] Araç bileşenleri disk kanıtlı (ci.yml · composer.json · package.json)
- [ ] politika MD'leri · SoA · denetim dashboard → açık soru

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
