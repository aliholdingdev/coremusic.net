---
node_id: K006-yapay-zeka-test-stratejisi
node_type: domain
status: verify-required
parent_id: "K000–K5000 Architecture Registry"
children:
  - K006.1-audio-intelligence
  - K006.2-evaluation
  - K006.3-inference
  - K006.4-local-inference
  - K006.5-model-management
  - K006.6-music-analysis
  - K006.7-personalization
  - K006.8-recommendation
  - K006.9-testing
depends_on:
  - K004-shared
  - K016-AI
evidence:
  - "shared/composer.json — phpunit ^10.5, phpstan ^1.10, script 'stan' level 5 (2026-10-12)"
  - "shared/tests/ — 9 dizin envanteri (shared/AGENTS.md §2, 2026-10-12)"
  - "shared/src/Contracts/AI/ — 6 interface · 33 imza = testlenebilir yüzey (2026-10-12)"
validation: "2026-10-12 · altyapı composer.json ile kanıtlandı; K006 AI testi 0 satır (boşluk) · status=verify-required (hedef coverage ölçülmedi)"
open_questions:
  - "K006 AI sınıfları için mevcut test dosyası yok — hedef coverage %80 kimin için bağlayıcı? (QA gate ADR)"
  - "ML metrik eşikleri (drift toleransı, Precision/Recall hedefi) tanımlı değil → UNKNOWN"
  - "pytest adayı K006.9 kapsamında mı, PHP tarafı mı — dil seçimi ADR bekliyor"
group: kod
---

# K006 — Yapay Zeka · Test Stratejisi

> Kapsam: K006'ın **gerçek test yüzeyi** (ne var) + **boşluk** (ne yok) + **katmanlı strateji** +
> **kapı (gate)**. Sayılar diskten; hedefler taslak ve `verify-required` işaretlidir.

---

## 1. Mevcut Test Altyapısı (disk kanıtı)

| Öğe | Değer | Kaynak (2026-10-12) |
|---|---|---|
| Test çerçevesi | `phpunit/phpunit ^10.5` (require-dev) | `shared/composer.json` |
| Statik analiz | `phpstan/phpstan ^1.10` · `scripts.stan` = **level 5** | `shared/composer.json` |
| Test komutu | `scripts.test` | `shared/composer.json` |
| Test dizinleri | 9: Api · Component · Events · Fixtures · Middleware · OAuth · Repository · Security · Unit | `shared/AGENTS.md` §2 |
| K006 AI sınıflarına özel test | **0 satır** | envanter (AIEngine/AIWorkflow/AIOrchestrator için test dosyası yok) |
| ML/metrik testi (drift, kalite) | **0 satır** | repo grep |
| Python/pytest testi | **0 satır** (pytest yalnız aday) | `yapay-zeka-bagimliliklar.md` §3 |

**Sonuç:** altyapı var (PHPUnit + PHPStan 5), K006'nın kendi testi **yok**. Bu, stratejinin
giriş koşuludur — `status: verify-required`.

---

## 2. Testlenebilir Yüzey (arayüz üzerinden)

Gerçek sözleşmeler (33 imza) birim testinin doğal sınırıdır:

| Yüzey | İmza | Öncelik | Neden |
|---|---|---|---|
| `AIEngineInterface` | 5 | P0 | ürün çıktısı (öneri/analiz/EQ/tahmin) |
| `AIOrchestratorInterface` | 6 | P1 | görev/kontekst yaşam döngüsü |
| `MemorySystemInterface` | 7 | P1 | TTL/hiyerarşi — veri kaybı riski (R-11) |
| `KnowledgeBaseInterface` | 5 | P2 | **K016** — K006'dan sınır, yalnız çağrı sözleşmesi |
| `PromptEngineInterface` | 5 | P2 | token sayımı/şablon doğrulama |
| `ToolCallingInterface` | 5 | P2 | izin kontrolü (güvenlik yüzeyi) |

**Sınırlama:** `AIWorkflow` **interface'siz** (L18) → kontrat testi imkânsız; yalnızca
davranış testi (girdi/çıktı) yazılabilir. Bu bir bulgudur (risk R-09/K-10).

---

## 3. Katmanlı Strateji

### 3.1 Birim (P0 — her metot için)
| Hedef | Test tipi | Kanıt beklenen davranış |
|---|---|---|
| `getRecommendations` L49–77 | senaryo | 6 adım sırası; `limit` > 20 kırpılır (L51); sonuç skor azalan (L74) |
| `combineScores` L243–262 | birim | 0.6/0.4 ağırlıklı ortalama; eksik anahtar `0` sayılır (L249) |
| `applyDiversityFilter` L264–286 | birim | %30 tür garantisi (DIVERSITY_TARGET L32) |
| `getCollaborativeScores` L209–218 | karakteristik | **`[]` döner** (R-01 için regresyon kapısı) |
| `extractFeatures` L315–330 | karakteristik | **placeholder sabit** — gerçek DSP gelene kadar bu kırılmalı (R-02) |
| `optimizeEQ` L111–133 | birim | 31 band; frekans listesi L335–339; `gain` yuvarlama 2 hane |
| `analyzeAudio` L82–106 | hata | dosya yoksa `\RuntimeException` (L84–86) |
| `extractMetadata` L288–313 | birim | eksik anahtar varsayılanları (L101–104) |
| `AIWorkflow::recommendationWorkflow` L49–91 | birim (mock) | 6 çağrı sırası; `metadata.task_id` dönüşte (L85) |

### 3.2 Entegrasyon (P1)
- `AIWorkflow` ↔ `AIEngine` gerçek example (mock'suz) — **K016 mock'lanır** (`knowledge`,
  `promptEngine`, `memory`, `orchestrator`) çünkü K006 testi K016 altyapısını bloklamamalı.
- `recommendationWorkflow` L70 `JSON_THROW_ON_ERROR` — UTF-8 bozuk `history` senaryosu
  (risk R-12) **zorunlu negatif test**.

### 3.3 Sözleşme (P1)
- Her interface için `assertInstanceOf` + imza sayısı (6 interface = 33 imza) —
  yeni imza eklendiğinde test kırılır.
- `AIEngineInterface` `@return` şeması: her öneride `{id,title,artist,score,reason}` zorunlu
  (Contracts L26) — **schema doğrulayıcı** test.

### 3.4 ML / Değerlendirme (P2 — K006.2 ile birlikte)
- Öneri kalitesi: Precision@k / Recall@k / NDCG — metrik kütüphanesi adayı
  `scikit-learn` (`https://scikit-learn.org/stable/`) ve `Surprise`
  (`https://github.com/NicolasHug/Surprise`) — **ikisi de kurulu değil (UNKNOWN)**.
- Drift: aday `Evidently` (`https://www.evidentlyai.com/`) — repo'da **0 satır** (R-03).
- Eşik değerleri **tanımlı değil** → `UNKNOWN`; strateji eşikleri **öneri** olarak yazılır,
  bağlayıcı gate **değil**.

### 3.5 Uyum (P2)
- KVKK Üretken YZ rehberi maddeleri okunmadan K006.7 profil testi **onaylanmaz**
  (risk R-10, `⚠️ VERIFICATION REQUIRED`).

---

## 4. Gate (Kapı) Matrisi

| Gate | Koşul | Durum (2026-10-12) |
|---|---|---|
| G1 Statik | `composer stan` (level 5) çıkış 0 | ⚠️ bu görevde **çalıştırılmadı** — çalıştırmak yetki/gereksinim dışı |
| G2 Birim | K006 P0 senaryoları yeşil | 🔴 test yok — **0 satır** |
| G3 Sözleşme | 6 interface · 33 imza eşleşmesi | 🔴 test yok |
| G4 ML metrik | Precision@k eşiği | 🔴 eşik **UNKNOWN** |
| G5 Uyum | KVKK YZ maddeleri okundu | 🔴 `⚠️ VERIFICATION REQUIRED` |

**Hiçbir gate bu görevde geçmedi/alınamadı** — kapılar tanımlı, durumları açık.

---

## 5. Boşluk Kapanış Yol Haritası (öncelik sırası)

1. **P0 · Karakteristik testler** — R-01 (`[]` döner) ve R-02 (placeholder) için bilinçli test;
   gerçek implementasyon gelince kırmızıya döner → dönüşüm sinyali.
2. **P0 · Hata yolu** — `analyzeAudio` istisnası + workflow `JSON_THROW_ON_ERROR` (R-04/R-12).
3. **P1 · Sözleşme testi** — 33 imza + `@return` şeması denetimi (K006.9).
4. **P1 · Workflow mock testi** — 6 bağımlılık sırası (K-10).
5. **P2 · ML metrik iskeleti** — Precision@k/NDCG; eşikler K006.2 ADR'siyle bağlayıcı olur.
6. **P2 · pytest/Python** — dil seçimi ADR bekliyor (K006.9 açık soru).

---

## 6. Sayım

| Metrik | Değer |
|---|---|
| Mevcut K006 AI testi | **0 dosya / 0 satır** |
| Testlenebilir imza | 33 (6 interface) |
| Tanımlı gate | 5 (G1–G5) · geçen: **0** |
| Strateji maddesi | 5 katman (3.1–3.5) |
| Yol haritası adımı | 6 (§5) |
| UNKNOWN | 2 (eşik · dil seçimi) |
| ⚠️ VERIFICATION REQUIRED | 2 (G1 çalıştırılmadı · KVKK maddeleri) |

---

## 7. Kaynaklar

1. `shared/composer.json` — require-dev + scripts, 2026-10-12.
2. `shared/AGENTS.md` §2 — `tests/` 9 dizin envanteri, 2026-10-12.
3. `shared/src/Contracts/AI/*.php` — 6 interface / 33 imza grep, 2026-10-12.
4. `shared/src/AI/AIEngine.php` L49–77 · L82–133 · L209–235 · L315–330 — 2026-10-12.
5. `shared/src/AI/AIWorkflow.php` L49–91 — 2026-10-12.
6. `yapay-zeka-web-arastirma.md` §2 (11 · 24) — scikit-learn / Surprise aday kayıtları.

---

`status: verify-required` · `validation: 2026-10-12 · altyapı composer.json kanıtlı, K006 testi 0 satır, 5 gate tanımlı/0 geçti, hedef eşikler UNKNOWN`
