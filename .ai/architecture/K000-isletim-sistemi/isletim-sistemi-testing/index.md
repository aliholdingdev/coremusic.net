---
node_id: K000-isletim-sistemi/isletim-sistemi-testing
node_type: domain
status: active
parent_id: K000-isletim-sistemi
children:
  - K000-isletim-sistemi/isletim-sistemi-testing/cekirdek-test
  - K000-isletim-sistemi/isletim-sistemi-testing/uygulama-testi
  - K000-isletim-sistemi/isletim-sistemi-testing/ci-ve-kalite-kapisi
depends_on: []
evidence: []
validation:
  - "2026-10-14: isletim-sistemi-testing-kod-seti.md yazıldı (632 satır · 14 kod bloğu · 17 resmi URL HTTP 200 · spot-check: orkestratör)"
open_questions:
  - T1 [UNKNOWN] — "test kapsamı" tam olarak hangi artefaktları kapsıyor (çekirdek + uygulama + CI mı, yalnız uygulama mı): kapsam belirsiz → kapsam.md §G.1.
  - T2 [UNKNOWN] — coverage/ölçüm eşiği yok; rakam uydurmak yasak (kapsam.md §G.2).
group: mimari
---

# isletim-sistemi-testing — Test, Kalite Kapısı ve Doğrulama

> **Yer:** `K000-isletim-sistemi/isletim-sistemi-testing/` · **Kademe:** 2 (dal) +
> 3 (alt-dal) · **SSOT:** `.ai/CLAUDE.md` (vault-içi otorite sırası §2.1).

## §1 Ne yapar

İşletim sistemi ve üzerindeki POSIX kullanıcı uzayının **doğrulanması**: test
stratejisi, kalite kapıları (lint/type/test/security gate), CI altyapısı ve
kanıt protokolü. Testlerin ne olduğu değil, **testlerin kendisi** burada tanımlıdır.

## §2 Kapsam dışı (komşu dallar)

| Konu | Sınır | Kanıt |
|---|---|---|
| Uygulama (PHP/JS) testi içeriği | `K019-testing` | Faz-A olarak $K019'a teslim (kapsam.md §E.1) |
| Sürücü implementasyonu | `isletim-sistemi-kernel` | Backlog kaynağı #14 |
| Workflow sahipliği/deploy | `K17-devops` | Sadece test adımları |
| Telemetri altyapısı | `isletim-sistemi-observability` | Ölçümün altyapısı DEĞİL |

## §3 Alt-dallar (Kademe-3)

| Alt-dal | Kademe-3 dosyalar | `group` |
|---|---|---|
| `cekirdek-test/` | `index` + `-anayasa` + `-surucu` + `-ornekler` | `mimari` + `kod` |
| `uygulama-testi/` | `index` + `-anayasa` + `-surucu` + `-ornekler` | `mimari` + `kod` |
| `ci-ve-kalite-kapisi/` | `index` + `-anayasa` + `-surucu` + `-ornekler` | `mimari` + `kod` |

## §4 Dosya indeksi (10 dosya, 3 dalın dışındakiler kök)

| Dosya | Kademe | `group` | Özet |
|---|---|---|---|
| `index.md` | 2 | `mimari` | Bu dosya: dal kökü, alt-dal ve sınır özeti |
| `isletim-sistemi-testing-kapsam.md` | 2 | `mimari` | Kapsam, komşu sınır matrisi, sorumluluk |
| `isletim-sistemi-testing-anayasa.md` | 2 | `mimari` | Test anayasası, seviyeler, strateji |
| `isletim-sistemi-testing-dogrulama.md` | 2 | `kanit` | Doğrulama ve kanıt protokolü |
| `isletim-sistemi-testing-surucu.md` | 2 | `mimari` | Test sürücüsü, orkestrasyon |
| `isletim-sistemi-testing-riskler.md` | 2 | `kanit` | Riskler, anti-pattern, yeniden çalışma |
| `isletim-sistemi-testing-web-arastirma.md` | 2 | `kanit` | Web araştırma iddiaları (URL + tarih) |
| `isletim-sistemi-testing-kod-ornekleri.md` | 2 | `kod` | Kod örnekleri (gerçek repo satır referanslı) |
| `isletim-sistemi-testing-ornekler.md` | 2 | `kod` | Senaryo örnekleri + indeks doğrulama |
| `isletim-sistemi-testing-cross-platform-ornekleri.md` | 2 | `kod` | Windows/macOS/Linux taşınabilir test kodu — P-01..P-10 platform matrisi · 10 dil gruplu referans örnek · E-01..E-13 · Q-3 yanıtı (2026-10-14) |
| `isletim-sistemi-testing-kod-seti.md` | 2 | `kod` | Cross-platform test izolasyonu, platform matrisi deseni ve CI gate kod seti — 632 satır · 14 kod bloğu · 17 resmi URL (2026-10-14) |

## §5 Kanıt ve doğrulama durumu

| Alan | Değer |
|---|---|
| `evidence` | `[]` — 2026-10-13 anında doğrulanmış disk kanıtı YOK |
| `validation` | `[]` |
| Bilinen kanıt boşluğu | Bölüm doğrulama tabloları: **Tümü FAIL — disk kanıtı yok** (kapsam.md §E.2) |
| `open_questions` | T1 kapsam + T2 eşik → `[UNKNOWN]` |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
