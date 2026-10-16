---
node_id: K006-yapay-zeka
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - model-management
  - inference
  - recommendation
  - personalization
  - music-analysis
  - audio-intelligence
  - local-inference
  - evaluation
  - testing
depends_on: [K007-data, K008-domain, K012-services]
evidence:
  - "v2 §2.6 — coremusic-katmanli-mimari-agaci-v2.md satır 251–268"
  - ".ai/architecture/index.md §5 satır 6 (master = ai-master.md)"
  - "shared/src/AI/ + shared/src/Contracts/AI/ — 13 dosya (disk ölçümü 2026-10-12)"
validation:
  - "2026-10-12 — children listesi v2 mini-ağaçı ile karşılaştırıldı: 9/9 birebir"
  - "2026-10-12 — 32 benzersiz URL exa ile açıldı, hepsi 200/gövde döndü"
  - "2026-10-12 — 5 başarısız URL kayda geçirildi (§6.2), hiçbiri doğrulanmadan kullanılmadı"
open_questions:
  - "ai-master.md adı registry §5'te K016-AI ile de geçiyor → ⚠️ VERIFICATION REQUIRED"
  - "Öneri sahipliği: K006-recommendation · K008-recommendation · K020-recomenataion → ⚠️ VERIFICATION REQUIRED"
  - "9 çocuğun kademe-3 (bounded-context) yapısı → ⚠️ VERIFICATION REQUIRED (ADR-097 K4)"
group: mimari
---

# K006-yapay-zeka — Domain Künyesi

> **ELI15 (v2 §2.6 birebir):** Şarkı öneren, sesi analiz eden yapay zeka parçaları —
> CoreMusic'in **akıllı tarafı**: öneriyi söyler, sesi okur, modeli ayakta tutar, iyi olup olmadığını ölçer.

## Gezinme

| Merak | Git |
|---|---|
| Ne, ne DEĞİL? · kim sorumlu · neye bağlı, hangi iddia kanıtlandı? | [`yapay-zeka-kapsam.md`](yapay-zeka-kapsam.md) · [`ai-master.md`](ai-master.md) · [`yapay-zeka-bagimliliklar.md`](yapay-zeka-bagimliliklar.md) · [`yapay-zeka-dogrulama.md`](yapay-zeka-dogrulama.md) |
| Web'de ne buldum? · gerçek kod karşılığı · test kabulü · TR↔EN | [`yapay-zeka-web-arastirma.md`](yapay-zeka-web-arastirma.md) · [`yapay-zeka-kod-ornekleri.md`](yapay-zeka-kod-ornekleri.md) · [`yapay-zeka-test-stratejisi.md`](yapay-zeka-test-stratejisi.md) |
| Ne karar verildi · neler riskli | [`yapay-zeka-kararlar.md`](yapay-zeka-kararlar.md) · [`yapay-zeka-riskler.md`](yapay-zeka-riskler.md) · [`yapay-zeka-evrensel-dil.md`](yapay-zeka-evrensel-dil.md) |

## Çocuk haritası (kademe-2 · 9 subdomain)

> Adlar v2 §2.6 mini-ağacından **birebir** alınmıştır (satır 255–268) — ad uydurulmamıştır;
> rol tanımları bu ağacın işidir, `⚠️ VERIFICATION REQUIRED` ile taşınır (ADR-097 K4);
> her çocuk 7 dosya taşır (`index` · `-anayasa` · `-kapsam` · `-dogrulama` · `-web-arastirma` · `-ornekler` · `-bilesenler`) → **9 × 7 = 63 dosya**.

| # | Çocuk (kademe-2) | v2 satır | Durum |
|---|---|---|---|
| 1 | `model-management/` | 259 | draft |
| 2 | `inference/` | 260 | draft |
| 3 | `recommendation/` (+ `recommendation-anayasa.md`) | 261 | draft |
| 4 | `personalization/` | 262 | draft |
| 5 | `music-analysis/` | 263 | draft |
| 6 | `audio-intelligence/` | 264 | draft |
| 7 | `local-inference/` | 265 | draft |
| 8 | `evaluation/` | 266 | draft |
| 9 | `testing/` | 267 | draft |

## Kök dosya haritası (12 dosya · ADR-097 K2 + §5.2)

`index.md` (registry) · `ai-master.md` (**v2 adı — İngilizce `-master` DEĞİŞTİRİLMEZ**, ADR-097 §5.4) ·
`-kapsam` (ne DEĞİL) · `-bagimliliklar` · `-dogrulama` (≥25 satır iddia·URL·tarih·durum) ·
`-web-arastirma` · `-bilesenler` (kademe-3 ipucu) · `-kod-ornekleri` · `-kararlar` · `-riskler` ·
`-test-stratejisi` · `-evrensel-dil` (TR↔EN).

## Durum

| Alan | Değer |
|---|---|
| `status` | **draft** — 3 açık soru kapandığında `verified` (`-dogrulama.md` §8) |
| Kanıt tabanı | 32 benzersiz URL (2026-10-12) + 14 repo dosyası · 5 başarısız URL · 8 UNKNOWN iddia · K006↔K016-AI = ML ürün vs ajan altyapısı (registry §6), kod yüzeyi salt-okunur |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan group** (tırnaksız: `mimari` · `kod` · `kanit`).

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `ai-master.md` | Master künye ve sorumluluk haritası | Kademe-1 |
| mimari | `index.md` | Domain kayıt defteri ve gezinme cetveli | Kademe-1 |
| mimari | `yapay-zeka-bagimliliklar.md` | Katman bağı ve used_by haritası | Kademe-1 |
| mimari | `yapay-zeka-bilesenler.md` | Bileşen planı ve kademe-3 ipuçları | Kademe-1 |
| mimari | `yapay-zeka-evrensel-dil.md` | TR↔EN terim karşılık sözlüğü | Kademe-1 |
| mimari | `yapay-zeka-kapsam.md` | Kapsam sınırı: ne içerir, ne içermez | Kademe-1 |
| mimari | `yapay-zeka-kararlar.md` | Karar, alternatif ve gerekçe kaydı | Kademe-1 |
| mimari | `recommendation/recommendation-anayasa.md` | Alt-dal anayasası (K006 ilk derin-dalı) | Kademe-2 |
| mimari | 8 × `<dal>/<dal>-anayasa.md` (bkz. Çocuk haritası · recommendation hariç) | Alt-dal anayasaları: kademe-2 ilkeleri, kapılar ve veri sözleşmeleri | Kademe-2 |
| kod | `yapay-zeka-kod-ornekleri.md` | Gerçek repo kodu, dosya ve satır | Kademe-1 |
| kod | `yapay-zeka-test-stratejisi.md` | ML test kabul kriterleri ve kapsamı | Kademe-1 |
| kanit | `yapay-zeka-dogrulama.md` | İddia·URL·tarih·durum doğrulama matrisi | Kademe-1 |
| kanit | `yapay-zeka-riskler.md` | Risk, etki, olasılık ve azaltma | Kademe-1 |
| kanit | `yapay-zeka-web-arastirma.md` | Sistemli resmi kaynak tarama kaydı | Kademe-1 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
