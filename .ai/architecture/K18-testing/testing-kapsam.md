---
node_id: K18-testing-kapsam
node_type: subdomain
status: draft
parent_id: K18-testing
children: []
depends_on: [K17-devops, K009-security, K007-data, K003-audio, K004-dsp]
evidence:
  - ".ai/architecture/K18-testing/testing-master.md §1-§2 (rol + kapsam/sınır + registry §6 tescilli ayrım — 2026-10-18)"
  - ".ai/architecture/K18-testing/testing-master.md §3 (11 çocuk: 4 kanıtlı · 2 kısmi · 4-5 kanıt bekliyor — 2026-10-18)"
  - ".ai/architecture/K18-testing/testing-bagimliliklar.md §2-§4 (depends_on/used_by + entegrasyon sınırları — 2026-10-18)"
validation: "Kapsam ± sınır her § disk kanıtıyla (55 PHP + 10 JS sayımı, ci.yml okuma); ölü referanslar ⚠️ olarak listelenir; depends_on yalnız gerçek K adları."
open_questions:
  - "⚠️ ID CONFLICT: dosya adı K18-testing ama master/bagimliliklar/dogrulama frontmatter'ı node_id: K019-testing taşıyor — düzeltilmedi, tescillendi"
  - "⚠️ K018-devops ölü referanstır (registry §5'te gerçek ad K17-devops) — bagimliliklar.md §2 satır 1 hâlâ K018-devops yazar, depends_on'a YAZILMAZ"
  - "⚠️ K19-testing master'ın kendi içinde andığı ölü referanstır — depends_on'a YAZILMAZ"
  - "⚠️ regression · performance · audio-quality · hardware-validation (4 çocuk) kanıt bekliyor"
group: mimari
---

# Testing — Kapsam

> K18-testing'in neyi kapsadığını, neyi kapsam dışında bıraktığını ve test
> çıktısının kime gittiğini tanımlar. Kanıt: `testing-master.md` (rev. 1.1.0,
> 2026-10-17) + `testing-bagimliliklar.md` + `testing-dogrulama.md`.

## §1. ELI15 — Bu klasör ne işe yarar?

**K18-testing = "her şeyin gerçekten çalıştığını kanıtlayan ekip."** Kod
yazıldıktan sonra o kodun çalıştığını, bozulmadığını, güvenli olduğunu ve ses
kalitesini ölçen alandır. Test, "yazayım da çalışır herhalde" değil;
**çalıştığını kanıtlayan, tekrarlanabilir gözlemdir**. Bu domain bir özellik
değil, **güven kapısıdır** — CI bu domainin ölçtüğünü kapıya uygular
(`testing-master.md` §1). Basit bir kural: **kanıt yoksa iddia yok** — bir test
türü "var" sayılmak için diskinde dosyası olmalıdır.

## §2. Kapsam (in-scope)

| # | Alan | Açıklama | Kanıt |
|---|---|---|---|
| 1 | Test stratejisi | Test pyramid, kat dağılımı, coverage hedefi | `testing-master.md` §2.1 |
| 2 | Test türleri | unit · integration · contract · e2e · security · build-verification | `testing-master.md` §3 (1-3, 7, 11) |
| 3 | Test envanteri | 55 PHP *Test.php + 10 JS spec + phpunit.xml ×4 + toolchain | `testing-master.md` §4/§4.1 (2026-10-12 sayımı) |
| 4 | CI kapısı eşlemesi | Hangi test türü hangi ci.yml job'unda koşuluyor | `testing-master.md` §4.2 (disk okuma) |
| 5 | Flaky politikası | Kararsız test tespiti, retry, karantina | `testing-master.md` §3 satır 5 (retry kanıtlı, karantina YOK) |
| 6 | Doğrulama disiplini | İddia → kanıt (S1-S4 sınıfı) zinciri | `testing-dogrulama.md` §2 |

## §3. Kapsam DIŞI (ne DEĞİL)

| Kapsam dışı alan | Nereye ait | Kanıt |
|---|---|---|
| Testlerin hangi pipeline'da çalıştırıldığı, deploy | **K17-devops** (ci.yml jobları orada tanımlı) | `testing-master.md` §2 (kayıt: master "K018-devops" yazar — ölü ad, gerçek K17-devops) |
| Ajan davranışının testi (agent-testing) | **K015-AI**/agent-testing | `testing-master.md` §2 |
| Sistem auth/CSRF/CSP politikasının kendisi | **K009-security** (burada: o politikanın *sınanması*) | `testing-master.md` §2 |
| Ses motorunun kendisi (DSP/ASIO) | **K003-audio** / **K004-dsp** (burada: ölçülen taraf) | `testing-master.md` §2 |
| Build sisteminin kurulumu, pipeline | **K17-devops**/build | `testing-master.md` §2 |

**KURAL (registry §6 — tescilli ayrım):** K17-devops = testleri **koşturan**
altyapı (CI job) · K18-testing = testlerin **neyi, nasıl ölçtüğü** (strateji +
senaryo). İkisi ayrı domaindir; kapsam kayması tespit edilirse ADR ile
düzeltilir, sessiz taşınmaz (`testing-master.md` §2).

## §4. Durum Özeti (2026-10-18 — disk sayımı 2026-10-12)

| Gösterge | Değer | Kanıt sınıfı |
|---|---|---|
| PHP test dosyası | **55** (shared 40 + auth 6 + home 6 + api 3) | S2 glob (2026-10-12; master 2026-10-17'de 49→55 düzeltti — `testing-dogrulama.md` §3-ç1) |
| JS spec dosyası | **10** (9 .spec.js + 1 .spec.ts — assets.coremusic.net) | S2 glob |
| phpunit.xml | **4** (shared · auth · api · home — media'da YOK) | S1/S2 |
| Coverage hedefi ≥80% | vault beyanı (`.ai/AGENTS.md` §16); ci.yml'da **soft** gate | S3 🔶 |
| "CI geçti" | **doğrulanmadı** — GitHub run kanıtı diskte yok; vault §25.2 de aynı der | S4 ⚠️ |
| 11 çocuğun durumu | 4 kanıtlı (unit/integration/contract/security) + build-verification kanıtlı · 2 kısmi (e2e · flaky) · 4 kanıt bekliyor (regression/performance/audio-quality/hardware-validation) | S2 (`testing-master.md` §3) |

## §5. Karar Kapıları (özet — detay: testing-kararlar.md)

| Kapı | Durum |
|---|---|
| Kapsam sınırı (K17-devops ↔ K18-testing ayrımı) | tescilli — registry §6; ADR gerekmiyor (mevcut ayrım) |
| Coverage soft gate → hard gate | ADR GEREKLİ (açık soru 1 — `testing-master.md` §7) |
| 4 kanıtsız çocuk (regression/performance/audio/hardware) | ADR GEREKLİ: kanıt topla ya da kapsam dışı tescille (`testing-dogrulama.md` §6) |
| media.coremusic.net phpunit.xml'siz | kapsam kararı ADR bekliyor (açık soru 7) |

## §6. Kaynaklar

| Kaynak | Referans |
|---|---|
| Domain anayasası | `testing-master.md` §1-§8 (rev. 1.1.0 — 2026-10-17) |
| Bağımlılık haritası | `testing-bagimliliklar.md` §2-§6 |
| Doğrulama | `testing-dogrulama.md` §2-§6 (S1-S4 sınıfı + web kanıtları) |
| Registry kaydı | `.ai/architecture/index.md` §5 satır 19 (K019-testing · testing-master.md · draft) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode · 2026-10-18 (Stage-2-F)
