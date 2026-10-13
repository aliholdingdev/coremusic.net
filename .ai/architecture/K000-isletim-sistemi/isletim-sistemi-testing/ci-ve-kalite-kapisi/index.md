---
node_id: K000-isletim-sistemi/isletim-sistemi-testing/ci-ve-kalite-kapisi
node_type: subdomain
status: active
parent_id: K000-isletim-sistemi/isletim-sistemi-testing
children:
  - K000-isletim-sistemi/isletim-sistemi-testing/ci-ve-kalite-kapisi/ci-ve-kalite-kapisi-anayasa.md
  - K000-isletim-sistemi/isletim-sistemi-testing/ci-ve-kalite-kapisi/ci-ve-kalite-kapisi-surucu.md
  - K000-isletim-sistemi/isletim-sistemi-testing/ci-ve-kalite-kapisi/ci-ve-kalite-kapisi-ornekler.md
depends_on: []
evidence: []
validation: []
open_questions:
  - T2 [UNKNOWN] — kalite kapısındaki eşikler: PHPStan baseline seviyesi, test coverage yüzdesi, lint eşikleri bilinmiyor; uydurulmaz.
  - T5 [UNKNOWN] — secret-scan.yml workflow'unun tetikleyicisi `workflow_dispatch` mu başka event mi: yalnız `on:` anahtarı görülüyor, alt satırları doğrulanmadı.
group: mimari
---

# CI-Ve-Kalite-Kapisi — Kalite Kapıları ve DevOps Test Altyapısı

> **Kapsam:** Bu alt-dal CI pipeline'ı, kalite kapıları (lint/type/test/security gate)
> ve secret taramasıdır. Uygulama test içeriği → `uygulama-testi/`; çekirdek test
> içeriği → `cekirdek-test/`. Komşu dal `K018-devops` bu daldan AYRIDIR (pipeline
> sahipliği oradadır; burada test adımlarının doğrulanması vardır).

## §1 Dosyalar (Kademe-3)

| Dosya | Tür | `group` | Ne verir |
|---|---|---|---|
| `ci-ve-kalite-kapisi-anayasa.md` | modül | `mimari` | GitHub Actions workflow yapısı, kalite kapıları, artifact/caching, determinizm |
| `ci-ve-kalite-kapisi-surucu.md` | modül | `mimari` | `ci.yml` sürücüsü, job/matris matrisi, secret-scan, kapı sıralaması |
| `ci-ve-kalite-kapisi-ornekler.md` | modül | `kod` | Gerçek workflow blokları, kapı kalıpları, anti-pattern tablosu |

## §2 Konu haritası (issue → dosya)

| Issue (kapsam.md §C) | Dosya |
|---|---|
| E-01 GitHub Actions yapısı | anayasa §1 + surucu §1 |
| E-02 kalite kapıları | anayasa §2 + surucu §4 |
| E-03 artifact/caching | anayasa §3 |
| E-04 secret taraması | anayasa §4 + surucu §3 |
| E-05 gerçek repo kanıtı | ornekler §1 |
| E-06 determinizm | anayasa §5 |

## §3 Sorumluluk sınırı (komşu dallar)

| Konu | Sınır | Bu alt-dal ne YAPAR |
|---|---|---|
| CI/CD pipeline sahipliği, deploy | → `K018-devops` | Test adımlarının doğrulanması; deploy YOK |
| Uygulama test içeriği | → `isletim-sistemi-testing/uygulama-testi` | Kapıda ÇALIŞTIRILAN komut; testin kendisi DEĞİL |
| Çekirdek test altyapısı | → `isletim-sistemi-testing/cekirdek-test` | CI'da koşabilecek çekirdek komut; QEMU kurulumu DEĞİL |
| Güvenlik politikası | → `isletim-sistemi-security` | Secret gate; OWASP denetimi DEĞİL |
| Observability/CI metrikleri | → `isletim-sistemi-observability` | Job süreleri; canlı telemetri DEĞİL |
| Kimlik doğrulama (deploy) | → `isletim-sistemi-auth` | OIDC/secret gizliliği; auth akışı DEĞİL |
| Dağıtım ortamı matrisi | → `isletim-sistemi-turu` | OS matrisi; kurulum DEĞİL |
| Bilgi deposu kuralları | → `K001-K019` (başka K düğümü) | K019'a 0 satır yazımı |
| Sürücü/çekirdek arayüzü | → `isletim-sistemi-kernel` | Kapıda koşan test; kod DEĞİL |
| Süreç/örtü bölgesi modeli | → `isletim-sistemi-core` | Kapı davranışı; model DEĞİL |
| Zamanlama/gecikme ölçümü | → `isletim-sistemi-plan` | Job süreleri; planlayıcı DEĞİL |

## §4 Kanıt ve validation durumu

| Alan | Değer |
|---|---|
| `evidence` | `[]` — doğrulanmış iddia YOKTUR |
| `validation` | `[]` |
| Disk kanıtı | Bu alt-dalda ÜRETİLEN gerçek repo kanıtı: `.github\workflows\ci.yml` (203 satır), `.github\workflows\secret-scan.yml` (31 satır) |
| `open_questions` | T2 eşik + T5 `on:` tetikleyicisi → `[UNKNOWN]` |

## §5 Bilinen sınırlar

1. `ci.yml` L35–L64 PHP seviyelerindeki Türkçe/İngilizce açıklama satırları konsol
   mojibake'si nedeniyle bloklarca alıntılanamaz; yalnız L34 `name: PHP ${{ matrix.php }}` gibi
   ASCII satırlar doğrudan quotelenir (bkz. ornekler §1 notu).
2. `secret-scan.yml` yalnızca `on:` anahtarı + `gitleaks` adımları görünür (L5–L30);
   tetikleyici satırları doğrulanmadı → T5.
3. `shared/composer.json` → `ci` scripti; `shared\phpunit.xml` → kapıda koşan test.
4. Kod örneklerinde satır numaraları `Select-Object -Index` ile doğrulanmıştır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
