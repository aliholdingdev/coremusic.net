---
title: "K016-observability — Domain Kaydı (K-Space V3)"
node_id: K016-observability
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - logging
  - metrics
  - tracing
  - correlation
  - health-checks
  - alerts
  - performance
  - audio-diagnostics
  - incident-response
depends_on:
  - K012-services   # log/metric üreten servis katmanı — planlanan (mimari yargı)
  - K010-network    # tracing/health ağ katmanına dokunur — planlanan
  - K014-media      # audio-diagnostics ses zincirine bakar — planlanan
  - K17-devops      # alert/CI geri beslemesi — planlanan (eski ad K018-devops, registry §6.2-A eşlemesi)
evidence:
  - "shared/src/Log/ = LoggerFactory.php · FileHandler.php · Redactor.php (+ CLAUDE.md) — glob 2026-10-12"
  - "shared/tests/Unit/Log/Psr3LoggerTest.php · LoggerRedactionTest.php — glob 2026-10-12"
  - ".ai/log.md — vault append-only audit trail (varlık doğrulandı 2026-10-12; içerik okunmadı)"
validation:
  - "disk ölçümü (glob) 2026-10-12 — shared/src/Log/ ve test dosyaları"
  - "v2 spec §2.17 çocuk listesi birebir alındı (2026-10-12)"
open_questions:
  - "metrics/ · tracing/ · correlation/ · health-checks/ · alerts/ · performance/ · audio-diagnostics/ · incident-response/ için disk kanıtı YOK — kanıt bekliyor (⚠️ VERIFICATION REQUIRED)"
  - "PSR-3 uyumu: test adı (Psr3LoggerTest) ima ediyor, test içeriği OKUNMADI — doğrulanmadı (⚠️ VERIFICATION REQUIRED)"
  - "Uygulama logları ile vault .ai/log.md audit trail arasındaki ilişki/ayrım tanımlı mı? (⚠️ VERIFICATION REQUIRED)"
group: mimari
updated: 2026-10-18
---


# K016-observability — Sistem Gözetimi (Domain)

## §GENEL BAKIŞ

| Alan | Değer |
|---|---|
| **K Kodu** | K016-observability |
| **K Adı** | Gözetim (Observability) |
| **Kapsam** | "Şu an hasta mı, nerede takılıyor?" sorusunun cevabı: loglar, metrikler, izler (tracing), korelasyon, sağlık kontrolleri, uyarılar, performans, audio-diagnostics ve olay müdahalesi. Test stratejisi (K18-testing), CI/deploy (K17-devops) ve güvenlik denetimi (K009-security) kapsam dışıdır. Kanıt yüzeyi: `shared/src/Log/` 3 dosya + 2 birim testi. 9 kademe-2 çocuk. |
| **Kritik Bağımlılıklar** | K010-network · K012-services · K014-media (kanıt: FM depends_on — hepsi "planlanan"; FM'deki K018-devops kaydının diske karşılığı K17-devops ⚠️) |
| **Durum** | draft (9 kademe-2 çocuk · logging/ kanıtlı, 8 dal kanıt bekliyor) |
| **Son Güncelleme** | 2026-10-12 (v2 §2.17 çocuk listesi birebir · shared/src/Log/ glob ölçümü) |

> **ELI15:** Bu domain, "şu an hasta mı, nerede takılıyor?" sorusunun cevabıdır: loglar, metrikler,
> izler (tracing), sağlık kontrolleri, uyarılar ve olay müdahalesi. Sistem *görünürlüğü* buradadır.

## 1. Kapsam Sınırı (kısa)

- **Burada:** sistemin kendi gözlemi (log/metric/trace/health/alert/incident).
- **DEĞİL:** test stratejisi (K019-testing) · CI kapısı ve deploy (K018-devops) · güvenlik denetimi (K009-security).
- audio-diagnostics/ ses zincirine özgü teşhistir → K003-audio/K014-media ile **entegrasyon sınırı** vardır (K016 teşhisi tanımlar, ses motoru orada kalır).

## 2. Kademe-2 Çocuk Kataloğu (v2 §2.17 — birebir)

| # | Çocuk | Ne işe yarar (ELI15) | Disk kanıtı (2026-10-12) |
|---|---|---|---|
| 1 | logging/ | Logların yazımı, formatı, redaction'ı | ✅ shared/src/Log/ (3 dosya) + 2 birim testi |
| 2 | metrics/ | Sayaç/ölçüm toplama (istek, hata, gecikme) | ⚠️ kanıt bekliyor |
| 3 | tracing/ | İstek uçtan uca izi (trace/span) | ⚠️ kanıt bekliyor |
| 4 | correlation/ | İstekler arası korelasyon kimliği (request-id) | ⚠️ kanıt bekliyor |
| 5 | health-checks/ | Canlılık/sağlık uçları (health endpoints) | ⚠️ kanıt bekliyor |
| 6 | alerts/ | Uyarı kuralları ve kanalları | ⚠️ kanıt bekliyor |
| 7 | performance/ | Performans gözlemi (profil, bottleneck) | ⚠️ kanıt bekliyor |
| 8 | audio-diagnostics/ | Ses zincirine özgü teşhis (xrun, clip, gecikme) | ⚠️ kanıt bekliyor |
| 9 | incident-response/ | Olay müdahale akışı (runbook, eskalasyon) | ⚠️ kanıt bekliyor (kısmi: .ai/AGENTS.md §10 eskalasyon var) |

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (registry §5 — Band-1 üretim aşaması) |
| Kanıt dengesi | 1/9 çocuk kanıtlı (logging) · 8/9 kanıt bekliyor — bu, domainin **kağıtta geniş / diskte dar** olduğunu gösterir |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Bağlı dosyalar | observability-master.md · observability-bagimliliklar.md · observability-dogrulama.md |

## §BAĞLANTILAR

> **Yön işareti = kanıt yönü, sözleşme değil:** `→` bu K'nın kök içerik dosyaları hedefe referans veriyor · `←` hedef K'nın kök dosyaları bu K'ya referans veriyor · `↔` ikisi de · `⚠️` kök taramada kanıt yok.
> **Yöntem (2026-10-18):** her K'nın kök .md'leri (index.md hariç) taranıp hedef K adının toplam geçişi sayıldı (zero-hallucination; uydurma bağımlılık yok). Zenginleşik 4 tablo (K003/K008/K011/K012) korunmuştur.

| Hedef K | Wiki-link | Göreli dosya yolu | Yön | İlişki | Kanıt |
|---|---|---|---|---|---|
| K016-observability | [[K016-observability]] | ./K016-observability/index.md | ○ | bu K (kendi kökü) | registry §5 + disk |
| K000-isletim-sistemi | [[K000-isletim-sistemi]] | ../K000-isletim-sistemi/index.md | ↔ | iki yönlü atıf | M→1 · M←2 (kök .md, 2026-10-18) |
| K001-donanım | [[K001-donanım]] | ../K001-donanım/index.md | → | bu K referanslıyor | M→2 · M←0 (kök .md, 2026-10-18) |
| K002-electroncis | [[K002-electroncis]] | ../K002-electroncis/index.md | → | bu K referanslıyor | M→2 · M←0 (kök .md, 2026-10-18) |
| K003-audio | [[K003-audio]] | ../K003-audio/index.md | ↔ | iki yönlü atıf | M→5 · M←2 (kök .md, 2026-10-18) |
| K004-dsp | [[K004-dsp]] | ../K004-dsp/index.md | → | bu K referanslıyor | M→7 · M←0 (kök .md, 2026-10-18) |
| K005-firmware | [[K005-firmware]] | ../K005-firmware/index.md | → | bu K referanslıyor | M→2 · M←0 (kök .md, 2026-10-18) |
| K006-yapay-zeka | [[K006-yapay-zeka]] | ../K006-yapay-zeka/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K007-data | [[K007-data]] | ../K007-data/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K008-domain | [[K008-domain]] | ../K008-domain/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K009-security | [[K009-security]] | ../K009-security/index.md | → | güçlü çapraz referans | M→16 · M←0 (kök .md, 2026-10-18) |
| K010-network | [[K010-network]] | ../K010-network/index.md | → | bağımlı (depends_on) | M→8 · M←0 (kök .md, 2026-10-18) |
| K011-application | [[K011-application]] | ../K011-application/index.md | → | bu K referanslıyor | M→4 · M←0 (kök .md, 2026-10-18) |
| K012-services | [[K012-services]] | ../K012-services/index.md | → | bağımlı (depends_on) | M→14 · M←0 (kök .md, 2026-10-18) |
| K013-integraion | [[K013-integraion]] | ../K013-integraion/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K014-media | [[K014-media]] | ../K014-media/index.md | → | bağımlı (depends_on) | M→7 · M←0 (kök .md, 2026-10-18) |
| K015-AI | [[K015-AI]] | ../K015-AI/index.md | → | güçlü çapraz referans | M→15 · M←0 (kök .md, 2026-10-18) |
| K019-comilance | [[K019-comilance]] | ../K019-comilance/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K020-discovery | [[K020-discovery]] | ../K020-discovery/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K021-recomenataion | [[K021-recomenataion]] | ../K021-recomenataion/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K17-devops | [[K17-devops]] | ../K17-devops/index.md | ⚠️ | bağımlı (depends_on) | M→0 · M←0 (kök .md, 2026-10-18) |
| K18-testing | [[K18-testing]] | ../K18-testing/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-1 |
| mimari | `observability-bagimliliklar.md` | Katman bağı ve used_by haritası | Kademe-1 |
| kanit | `observability-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-1 |
| mimari | `observability-master.md` | Master künye ve sorumluluk haritası | Kademe-1 |
| mimari | `observability-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-1 |
| kod | `observability-ornekler.md` | Gerçek repo kodu ve örnekler | Kademe-1 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
