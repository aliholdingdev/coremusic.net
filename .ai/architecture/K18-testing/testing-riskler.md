---
node_id: K18-testing-riskler
node_type: subdomain
status: draft
parent_id: K18-testing
children: []
depends_on: [K17-devops, K009-security, K003-audio, K004-dsp, K007-data]
evidence:
  - ".ai/architecture/K18-testing/testing-dogrulama.md §2 (S4 iddia + ❌ negatif satırlar = R1-R5 kanıt tabanı — 2026-10-18)"
  - ".ai/architecture/K18-testing/testing-bagimliliklar.md §5 (7 riskli bağımlılık — 2026-10-18)"
  - ".ai/architecture/K18-testing/testing-master.md §6 ('CI geçti' doğrulanmadı — .git/HEAD ENOENT — 2026-10-18)"
validation: "Olasılık/Etki NİTEL (düşük/orta/yüksek) — sayısal eşik/skor uydurulmaz; her satır disk kanıtına bağlı; sahip rolü vault rollerinden (registry §4)."
open_questions:
  - "⚠️ R1-R2'deki kapı sertliği kararı (soft→hard) ADR bekliyor — risk azaltması henüz karar değil, öneri"
  - "⚠️ R4-R7'deki 4 kanıtsız çocuk için kanıt toplama tarihi UNKNOWN"
group: kod
---

# Testing — Riskler

> K18-testing'in kanıt zincirindeki zayıf halkaları; her risk disk
> gözlemine bağlı, Olasılık/Etki **nitel** (düşük/orta/yüksek), sayısal skor
> uydurulmaz. Kaynak: `testing-dogrulama.md` §2 · `testing-bagimliliklar.md` §5.

## §1. ELI15 — Risk tablosu ne işe yarar?

Test domaininin en büyük riski "test var ama kanıt yok" durumudur: kapı
yeşil görünür ama kapı aslında açık olmuştur. Bu tablo her zayıf noktayı
**Olasılık (ne sıklıkla başımıza gelir) × Etki (gelince ne olur) × Azaltma
(ne yaparız) × Sahip (kim yapar)** dörtlüsüyle kayıt altına alır. Sayısal
"risk puanı" uydurulmaz — kanıt yoksa nitel değer yazılır.

## §2. Risk Kayıtları

| # | Risk | Olasılık | Etki | Azaltma (öneri — karar değil) | Sahip |
|---|---|---|---|---|---|
| R1 | **Coverage gate soft** — ci.yml L167 `continue-on-error: true`; ADR-023 madde (d) "PR'da düşerse merge yok" der | yüksek (kapı hâlâ soft — disk kanıtı) | yüksek — coverage sessiz düşer, ≥80% hedefi (vault beyanı) bağlayıcı olmaz | soft→hard gate ADR'si (testing-kararlar.md Örnek A) | QA Engineer + DevOps Engineer |
| R2 | **E2E gate soft + kapsam dar** — ci.yml L183 soft; tek spec (player-info.spec.ts); login/play/settings akışları E2E'de yok | yüksek (1 spec / tüm ürün akışları) | yüksek — tarayıcı bozulmaları kapıdan kaçar | spec ailesi büyütme kararı (açık soru 2) + gate sertleştirme | QA Engineer |
| R3 | **"CI geçti" iddiası kanıtsız** — GitHub run geçmişi diskte değil; .git/HEAD ENOENT; vault §25.2 de "doğrulanmadı" der | orta (kanıt tarihsel olarak toplanabilir) | yüksek — yeşil sanılan kapı kırmızı olabilir | GitHub arayüzünden run kanıtı toplama + rapora ekleme | DevOps Engineer |
| R4 | **regression/ kanıtsız** — ayrı suite glob = 0 isabet | yüksek (dosya yok) | orta — "önceki çalışan" bozulması ancak unit/integration ile dolaylı yakalanır | ya minimal suite etiketleme ya ADR ile kapsam dışı tescilleme | QA Engineer |
| R5 | **performance/ kanıtsız** — k6/locust/jmeter config'i de YOK | yüksek | orta — yük/regresyon farkı üretime kadar görünmez | hedef metrik tanımı (açık soru 4) + ilk smoke-load seti | QA + Performance |
| R6 | **audio-quality/ kanıtsız** — SNR/THD/latency ölçüm dosyası YOK | yüksek | yüksek — CoreMusic'in temel iddiası (ses kalitesi) ölçülmüyor | K003-audio ile ortak ölçüm hedefi kararı (laboratuvar vs CI — açık soru 5) | Audio Hardware + QA |
| R7 | **hardware-validation/ kanıtsız** — fiziksel doğrulama kanıtı YOK | yüksek | yüksek — DAC/ADC/ASIO zinciri doğrulanmamış | rig tanımı (PCM3168A/AK4458/ASIO — açık soru 6) + doğrulama protokolü | Audio HW + DSP FW |
| R8 | **flaky karantina YOK** — retry kanıtı var (playwright.config.ts retry:2), karantina mekanizması repo'da yok | orta (E2E kapsamı küçükken gizli) | orta — E2E büyüyünce kararsız testler kapıyı güvenilmez kılar | flaky karantina ADR'si (testing-dogrulama.md §5/7) | QA Engineer |
| R9 | **phpstan yalnız shared'de** — auth/api/home composer.json'larında phpstan YOK | yüksek (grep 2/4) | orta — alt alanlar statik analiz dışında; tip hatası üretime kaçar | subdomainlere phpstan ekleme ADR'si (bagimliliklar §5/3) | Backend + QA |
| R10 | **media.coremusic.net kapsam dışı** — phpunit.xml YOK, tests/ = 0 dosya | kesin (glob 0) | orta — bu alt alanın regresyonu kanıtsız | kapsam kararı: test ekle ya da istisnayı tescille (açık soru 7) | QA + Backend |
| R11 | **ID CONFLICT: K019-testing vs K18-testing** — klasör K18-testing ama master/bagimliliklar/dogrulama `node_id: K019-testing` taşır | kesin (disk kanıtı) | orta — arama/registry tutarsızlığı, ~referans kırılabilir | tekilleştirme kararı (ADRsiz sessiz düzeltme YASAK — isimlendirme dokunulmaz kuralı gereği tescillendi) | Vault Steward |
| R12 | **Master–disk sayım çelişkisi tekrarı** — 49→55 düzeltmesi (2026-10-17) tek turdu; envanter elle, elle tekrar kayabilir | orta | orta — envanter güvenilirliği zedelenir | envanteri script ile üretme (glob→tablo) — sayı uydurma yasağı sürer | QA + Vault Steward |

## §3. Eşikler Hakkında Kural (zero-hallucination)

| # | Kural |
|---|---|
| 1 | "Risk puanı = Olasılık × Etki" sayısal formülü bu domainde **kullanılmaz** — eşik değerleri diskte tanımlı değil (UNKNOWN), sayı yazmak uydurma olur |
| 2 | Vault hedefleri (≥80% coverage, flaky %0 — `.ai/AGENTS.md` §16) **hedeftir, ölçüm değildir** — S3 sınıfı |
| 3 | Azaltma sütunu **öneridir**; bağlayıcı olması için ADR gerekir (testing-kararlar.md §3) |

## §4. Kaynaklar

- `testing-dogrulama.md` §2 (18 iddia: S4 + 4 ❌ negatif = R1-R7 tabanı) — 2026-10-18
- `testing-bagimliliklar.md` §5 (7 riskli bağımlılık = R1/R2/R9/R10/R12 tabanı) — 2026-10-18
- `testing-master.md` §6 ("CI geçti" doğrulanmadı = R3) — 2026-10-18
- `.ai/AGENTS.md` §16 (QA hedefi — vault beyanı S3) — 2026-10-12 ölçümü

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode · 2026-10-18 (Stage-2-F)
