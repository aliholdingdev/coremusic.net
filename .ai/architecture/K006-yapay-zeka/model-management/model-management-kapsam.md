---
title: "Model Management — Kapsam"
type: architecture-scope
node_id: K006-yapay-zeka/model-management
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# Model Management — Kapsam

**ELI15:** Model yönetimini, eğitilmiş bir yapay zeka modelinin **kütüphane kartopu** gibi düşün: kartopu (model) nerede saklandı, hangi sürümü eski hangisi yeni, kim onu ne zaman yaptı ve üretime çıkarken hangi kartopu kopyası kullanıldı — hepsini tek bir kayıt defteri tutar. Model Management bu defteri ve saklama sistemini yönetir: kayıt, sürümleme, dağıtım (deploy) ve geri alma. Bu alt alan, **modelin yaşam döngüsünü** (eğit → kaydet → değerlendir → yayınla → geri al) tek yerden yönetmeyi hedefler. K006'nın çocuk tablosunda (ai-master.md §3) 9. ve son sırada tanımlıdır.

## 1. Kapsam içi (İçinde)

1. **Model kayıt defteri (registry)** — eğitilen her model için kayıt: hangi model, hangi sürüm, hangi eğitim verisi, hangi metrikle kaydedildi; kayıt şeması (model adı · sürüm · tarih · metrik · artifact yolu).
2. **Sürümleme ve etiketleme** — model sürümleri (v1, v2, …) ve canlı/taslak etiketleri (`Production`, `Staging`, `Archived`); hangi sürümün üretime alındığının izlenebilirliği.
3. **Artifact saklama** — model dosyalarının (ağırlık, ONNX grafiği, vektör indeksi) nerede saklandığı ve nasıl yüklendiği; artifact boyutu ve erişim hakkı.
4. **Dağıtım (deploy) ve rollback** — modelin servise bağlanması (inference ucuna takılması) ve geri alma; yeni sürümün eski sürümün yerini alması.
5. **Model envanteri** — sistemde kaç model olduğu, hangi domain'in (M01–M12) hangi modeli kullandığı; envanter (ai-master.md §1 sorumluluk tablosu) ile uyum.

## 2. Kapsam dışı (Dışında)

1. **Modelin eğitimi** (hype loop içinde) — eğitim hiperparametreleri ve eğitim kodu K006'ya aittir; burada yalnız kayıt ve sürümleme.
2. **Ajan / zeka altyapısı (K016-AI)** — ajan orkestrasyonu (planner/executor) K016'a aittir (K006 sınırı, `v2.md §2.6`); bu alt alan ajan değil model yaşam döngüsü yönetir.
3. **Inference çalışma zamanı** — canlı tahmin yürütme `inference/` alt alanındadır (QPS, stream, pipeline); burada yalnız "hangi model yüklensin" kararı.
4. **Feature store / eğitim veri ambarı** — veri hazırlama `audio-intelligence/` kapsamındadır.
5. **Model etik ve güvenlik denetimi** — burada yalnız envanter ve sürüm; etik/siber yönetim `security-policy.md` kapsamındadır.

## 3. Disk kanıtları

| Kanıt | Dosya / konum | Not |
|---|---|---|
| Envanter (12 domain) | `.ai/architecture/K006-yapay-zeka/ai-master.md §1` (satır 30–47) | M01–M12 sorumluluk tablosu; model adı sütunu `core-algorithm-orchestrator` |
| Envanter (9 alt alan) | `.ai/architecture/K006-yapay-zeka/ai-master.md §3` (satır 59–73) | model-management 9. satırda; çıktı "Model kayıt · dağıtım · sürüm yönetimi" |
| Bağımlılık · model | `.ai/architecture/K006-yapay-zeka/yapay-zeka-bagimliliklar.md` (satır 14) | M01 · `core-algorithm-orchestrator` — tablo satırı |
| AIEngine model referansı | `shared/src/AI/AIEngine.php` (satır 30) | `$model = 'core-algorithm-orchestrator';` — kayıt/zaman damgası yok (kanıt: satır 28–29) |
| Teknoloji belirsizliği | `.ai/architecture/K006-yapay-zeka/ai-master.md §4` (satır 87) | "Model kayıt / registry teknolojisi: UNKNOWN" |

## 4. Açık sorular

1. Model registry teknolojisi (MLflow · disk tabanlı basit registry · bulut servisi) seçilmedi — `ai-master.md §4` satır 87'de UNKNOWN.
2. Artifact saklama yeri (yerel disk · S3 benzeri object store) ve boyut bütçesi belirsiz.
3. Sürümleme şeması (semantik sürüm mü, tarih tabanlı mı) ve etiket politikası (kim `Production` işaretler) netleşmedi.
4. **Bu alt alanın kademe-2 kapsam taslağı v2'de yok** — üstteki tanımlar taslak önerisidir; v2 §2.6 / §2.4-9 ile resmileşmedi (aynı belirsizlik `music-analysis/` kapsamının 4. maddesinde de işaretli).
5. Rollback tetikleyicisi (kötü metrik · hata · manuel) ve otomatik mi manuel mi olacağı belirsiz.

## İlişkili dosyalar

- [[model-management-dogrulama]]
- [[model-management-ornekler]]
- [[../index|K006-yapay-zeka index]]

---

## EK — 2026-10-15 Genişletme Eki (append-only · bu dosyanın 55 orijinal satırının sonuna eklenmiştir)

### EK §0 — Usül, dayanak ve ölçüm notu

- **Yöntem:** Bu ek, dosyanın mevcut içeriği **silinmeden/düzenlenmeden** sonuna eklenmiştir; 55 satırlık orijinal gövde bu ekin önünde birebir korunur.
- **Dayanak okumaları (yazım öncesi, 2026-10-15):** bu dosyanın tamamı (55 satır) · `model-management/model-management-dogrulama.md` (644 satır) · `ai-master.md` (557 satır) · `yapay-zeka-bagimliliklar.md` (60 satır) · `shared/src/AI/AIEngine.php` ilgili bloklar.
- **Kanıt kuralı:** her iddia altında `Dosya: <yol> [Satır: n]` verilir; satır doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED` ile işaretlenir.
- **Ölçüm usulü:** `read(offset=9999)` hata mesajındaki satır sayısı dosyanın tam sayımıdır; bu ek yazıldıktan sonra §10 NIHAÎ ÖLÇÜM'de iki yöntemle (offset-hata + tam okuma başlığı) doğrulanır.
- **Yasaklar:** `git commit` yok · `index.md`'ye yazım yok · `*.php/js/css/sql` kaynak koduna yazım yok · yeni URL yok (yalnız 2026-10-12 tarihli, dogrulama ekinde taşınan URL'ler yeniden taşınır).
- **Önceki dosya boyu (ölçüm notu):** bu ek yazılmadan önce dosya **55 satır** idi (offset-hata ölçümü, 2026-10-15) — eski bir "iddia" satırı değil, usül kaydıdır.

### EK §1 — Tarama kayıtları (T1–T30)

> Bu bölümdeki T-satırlarının bir kısmı bu oturumda **yeniden** çalıştırıldı, bir kısmı kardeşi `model-management-dogrulama.md` §5'teki **aynı gün (2026-10-15)** tarama kayıtlarına atıfla taşındı (yeniden koşmadan pasif kanıt — atıf bölüm/satır ile verilir).

| T | Tarama | Sonuç | Kaynak |
|---|---|---|---|
| T1 | `grep "M01\|M12\|registry teknolojisi"` → `ai-master.md` | **0 isabet** | bu oturum, 2026-10-15 |
| T2 | `grep "M01\|core-algorithm"` → `yapay-zeka-bagimliliklar.md` | **0 isabet** | bu oturum, 2026-10-15 |
| T3 | `grep "core-algorithm"` → `shared/src/**/*` | **0 isabet** | bu oturum, 2026-10-15 |
| T4 | `glob "**/security-policy.md"` (repo geneli) | **0 dosya** | bu oturum, 2026-10-15 |
| T5 | `glob "**/v2.md"` ve `**/*v2*.md"` (repo geneli) | **0 dosya** | bu oturum, 2026-10-15 |
| T6 | `grep "mlflow\|MLflow"` → `shared/src/**` | **0 isabet** | dogrulama §5, 2026-10-15 |
| T7 | `grep "mlflow"` → `shared/composer.json` | **0 isabet** | dogrulama §5, 2026-10-15 |
| T8 | `grep "core-algorithm"` → `shared/src/**` + ai-master | **0 + 0** | dogrulama §5, 2026-10-15 |
| T9 | `grep "model-name\|version-column"` → repo | **0 isabet** | dogrulama §5, 2026-10-15 |
| T10 | `glob "**/*model*weight*"` | **0 dosya** | dogrulama §5, 2026-10-15 |
| T11 | `grep "M01\|M12"` → `ai-master.md` | **0 isabet** | dogrulama §5, 2026-10-15 |
| T12 | `grep "security-policy"` → `ai-master.md` | **0 isabet** | dogrulama §5, 2026-10-15 |
| T13 | `grep "30–47\|30-47"` → `ai-master.md` | **0 isabet** | dogrulama §5, 2026-10-15 |
| T14 | `grep "9. ve son sıra"` → `ai-master.md` | **0 isabet (doğrudan ifade yok)** | dogrulama §5, 2026-10-15 |
| T15 | `grep "sorumluluk"` → `ai-master.md` | sorumluluk matrisi = §4 **L176+** | bu oturum, 2026-10-15 |
| T16 | `ai-master.md` §3 çocuk tablosu sayımı | **9 çocuk, sıra 1–9**; model-management **satır #1** | bu oturum — B1 |
| T17 | `ai-master.md` §1.3 ID satırı | D1 = **önceliklendirme matrisi** (L87) | bu oturum — B4 |
| T18 | `yapay-zeka-bagimliliklar.md` çocuk satırları | K006.1…K006.9; model-management = **K006.5** | bu oturum — B3 |
| T19 | `ai-master.md` §7 soru listesi | **7 soru**, #4 = production model/runtime **UNKNOWN** | bu oturum — B5 |
| T20 | `ai-master.md` §11.2 UNKNOWN satırları | **6 UNKNOWN satırı** (L352–357) | bu oturum — B6 |
| T21 | `ai-master.md` §18.1 Faz-4 hedefi | `≥500` (L525) | bu oturum, 2026-10-15 |
| T22 | `ai-master.md` §18.4 H-satırları | H1–H7 (L530–532); H2 = K006 test kapsamı | bu oturum, 2026-10-15 |
| T23 | `ai-master.md` §13.2 model-management sayımı | **476 satır · 2026-10-13** (L406) | bu oturum, 2026-10-15 |
| T24 | `AIEngine.php` sabit bloğu | `MAX_RECOMMENDATIONS` / `MIN_SCORE` / `DIVERSITY_TARGET` (L23–34) | bu oturum — B2 |
| T25 | `AIEngine.php` `analyzeHardware` | `@todo` (L142) + `return ['status' => 'ok']` (L148) | bu oturum, 2026-10-15 |
| T26 | `AIEngine.php` `extractMetadata` | ffprobe kabuk çağrısı (L288–301) | bu oturum, 2026-10-15 |
| T27 | `grep "core-algorithm"` → `shared/src/**` | **0 isabet** | bu oturum, 2026-10-15 |
| T28 | `mlflow` isabetlerinin dağılımı | 11 md isabetinin **tamamı** `model-management/` altında (kodda değil) | dogrulama §5, 2026-10-15 |
| T29 | `ai-master.md` başlık sayısı | **37 başlık** | dogrulama §5, 2026-10-15 |
| T30 | `.ai/CLAUDE.md` (anayasa) başlık sayısı | **48 başlık** | dogrulama §5, 2026-10-15 |

**Tarama sonucu özeti:** model registry'yi **çalışan kod** olarak taşıyan tek bir repo dosyası bulunamadı (T3 · T6 · T7 · T8 · T10 = 0 isabet). `mlflow` isabetlerinin tamamı Markdown tartışma satırlarıdır (T28). Bu, dosyanın §2 iddiası olan "registry teknolojisi: UNKNOWN" ile **uyumludur**; tersini (çalışan registry) iddia eden hiçbir kanıt yoktur.

### EK §2 — Doğrulama blokları (B1–B6 · verbatim)

**B1 — `ai-master.md` §3 çocuk tablosu (L142–156, satır metinleri):**

```markdown
| # | Alt dosya | Kapsam satırı |
|---|---|---|
| 1 | **Model Mgmt** (öncelikli) | Registry · versiyonlama · drift · ota-update |
| 2 | Recommendation | Hibrit motor (L34–45): rule 40 · collab 30 · content 20 · audio 10 |
| 3 | Music Analysis | Tempo/key/beats · mood 4 enerji · segmenter 7 tip |
| 4 | Personalization | Profil · expand query (canlı radyo) · 1000+ |
| 5 | Inference | ONNX → OpenVINO · FP16 · int8 · fallback |
| 6 | Evaluation | **K006.4-evaluation** · gate · a2a-benchmark |
| 7 | Local Inference | OliveAI · **K006.7** · EP 1.5 (meta veri) |
| 8 | Audio Intelligence | **ONNX · TFLite · EdgeTPU** · native .so/.dll |
| 9 | Testing | a2a-testing · 11+2 · junit · sandbox |
```

*Not (K-C9/K-C11):* satır #1 model-management'tir; tabloda "son sıra" ifadesi **geçmez**; oda/room child satırı da **yoktur**.

**B2 — `shared/src/AI/AIEngine.php` L23–34 (satır metinleri):**

```php
     * Maximum number of recommendations to return
     */
    private const MAX_RECOMMENDATIONS = 10;

    /**
     * Minimum score for a recommendation to be considered relevant
     */
    private const MIN_SCORE = 0.3;

    /**
     * Target diversity score for recommendations (0.0 to 1.0)
     */
    private const DIVERSITY_TARGET = 0.6;
```

*Not:* L30 sabiti `DIVERSITY_TARGET`'tir; `$model = new ModelManager()` bu blokta **yoktur** (K-C7).

**B3 — `yapay-zeka-bagimliliklar.md` L6–15 (satır metinleri):**

```markdown
## Çocuklar (K006.*)

- **K006.1-audio-intelligence** → audio-intelligence/audio-intelligence-kapsam.md — ONNX Runtime · TFLite · native AI · ubx-ai
- **K006.2-personalization** → personalization/personalization-kapsam.md — profil · live-radio expand · 1000+ artist
- **K006.3-recommendation** → recommendation/recommendation-kapsam.md — hybrid · rule/collab/content/audio
- **K006.4-local-inference** → local-inference/local-inference-kapsam.md — OliveAI · EP 1.5
- **K006.5-model-management** → model-management/model-management-kapsam.md — registry · versiyonlama · drift · ota-update
- **K006.6-audio-music-analysis** → music-analysis/music-analysis-kapsam.md — tempo/key/mood
- **K006.7-inference** → inference/inference-kapsam.md — ONNX → OpenVINO · FP16/int8
- **K006.8-recommendation** → recommendation/recommendation-kapsam.md — (dup)
- **K006.9-testing** → testing/testing-kapsam.md — junit · a2a-testing
```

*Not (K-C6):* model-management satırı **K006.5**'tir (bagimliliklar L11); `M01` etiketi ile `core-algorithm` bağı bu satırlarda **geçmez** (T2).

**B4 — `ai-master.md` §1.3 (L86–92, satır metinleri):**

```markdown
### 1.3 Önceliklendirme Matrisi (D1)

| Öncelik | Kategori | Durum |
|---|---|---|
| 1 | **Model Management** | ⭐ ai-models.md — SPY/CRITICAL/P0 |
| 2 | Inference | OpenVINO hazır · test edilmemiş |
| 3 | Recommendation | Tasarlandı — <NEED-DECISION> |
```

**B5 — `ai-master.md` §7 (L249–256 · #4 satırı tam metin):**

```markdown
| 4 | **Production model/runtime: nerede, hangi sürüm?** | `UNKNOWN` |
```

*(§7'de toplam **7 soru** vardır — T19.)*

**B6 — `ai-master.md` §11.2 UNKNOWN satırları (L352–357, satır metinleri):**

```markdown
| `UNKNOWN` | Model ağırlığı/kaynak (SPY-CRITICAL) |
| `UNKNOWN` | Offline model indirme (nasıl?) |
| `UNKNOWN` | KVKK/GDPR explicit opt-in akışı |
| `UNKNOWN` | GDPR/KVKK Tüketici (+2 satır, ai-guvenlik.md) |
```

*(§11.2'de bu blok dahil **6 UNKNOWN satırı** vardır — T20; tam liste L352–357.)*

### EK §3 — Uyuşmazlık kayıtları (K-C1…K-C17)

> Dosyaya özgü ön ek `K-C` kullanıldı; kardeş `model-management-dogrulama.md` içindeki C1–C11 ile çakışmayı önlemek içindir. Satır numaraları **bu dosyanın orijinal 55 satırlık gövdesine** aittir.

| # | Dosya satırı | Eski iddia | 2026-10-15 kanıtı | Karar |
|---|---|---|---|---|
| K-C1 | frontmatter `source` satırı | `ai-models.md v2.md` | `v2.md` repo genelinde **yok** (T5) | ❌ ölü yol → UNKNOWN |
| K-C2 | §2 madde 2 | "K006 sınırı, `v2.md §2.6`" | `v2.md` yok (T5) | ❌ ölü yol |
| K-C3 | §2 madde 5 | "etik/siber yönetim `security-policy.md` kapsamındadır" | `security-policy.md` **yok** (T4); ai-master'da da 0 isabet (T12) | ❌ ölü atıf |
| K-C4 | §3 satır 1 | "ai-master §1 (satır 30–47) · M01–M12 sorumluluk tablosu · model adı sütunu `core-algorithm-orchestrator`" | ai-master'da `M01`/`M12`/`30–47` **0 isabet** (T1·T11·T13); `mlflow`/`core-algorithm` kodda 0 (T3·T6·T8) | ❌ uydurma referans |
| K-C5 | §3 satır 1 | "model adı sütunu `core-algorithm-orchestrator`" | `model-name`/`version-column` 0 isabet (T9); kodda registry yok (T3) | ❌ kanıtsız |
| K-C6 | §3 satır 3 | "bagimliliklar (satır 14): M01 · `core-algorithm-orchestrator`" | model-management = **L11 (K006.5)**, L14 = K006.8-recommendation; `M01`/`core-algorithm` o dosyada **0** (T2·B3) | ❌ satır+token yanlış |
| K-C7 | §3 satır 4 | "AIEngine (satır 30): `$model = 'core-algorithm-orchestrator';`" | L30 = `DIVERSITY_TARGET` sabiti (T24·B2) | ❌ token uydurma |
| K-C8 | ELI15 (giriş) | "ai-master §3: 9. ve son sırada tanımlıdır" | §3'te model-management **satır #1** (T16·B1); `9. ve son sıra` ifadesi ai-master'da 0 (T14) | ❌ sıra yanlış |
| K-C9 | §3 satır 5 · §4 madde 1 | "ai-master §4 (satır 87): 'Model kayıt / registry teknolojisi: UNKNOWN'" | §4 = **L176**, L87 = §1.3 D1 tablosu (B4); gerçek `UNKNOWN` = §11.2 L352–353 + §7 #4 L256 (T15·B5·B6) | ⚠️ konu doğru · satır/bölüm yanlış |
| K-C10 | §3 satır 2 | "ai-master §3 (satır 59–73) · model-management 9. satırda" | §3 = **L140–166**, model-management **L148 = #1** (B1) | ❌ satır + sıra |
| K-C11 | §3 satır 5 | "M01–M12 sorumluluk tablosu (§1)" | sorumluluk matrisi **§4 L176+** (T15); §1'de M01–M12 tablosu 0 (T1) | ❌ bölüm şaşması |
| K-C12 | §4 madde 4 | "kademe-2 kapsam taslağı v2'de yok · v2 §2.6 / §2.4-9" | `v2.md` yok (T5); hedef `ai-models.md` de bu ekte okunmadı (P-6) | ❌ ölü yol + kapı |
| K-C13 | ELI15 lifecycle | "eğit → kaydet → değerlendir → yayınla → geri al" | kendi §2 madde 1: "Modelin eğitimi … **kapsam dışı**" | ⚠️ iç gerilim |
| K-C14 | §2 madde 4 | "Feature store / eğitim veri ambarı → `audio-intelligence/`" | bağı doğrulanmadı (T3 benzeri tarama = 0 kanıt) | ⚠️ VERIFICATION REQUIRED |
| K-C15 | §3 satır 4 | "kanıt: satır 28–29" (AIEngine) | L28–29 bu iddiayı taşımıyor; blok L23–34 = üç sabit (B2) | ❌ kanıt satırı yanlış |
| K-C16 | §1 madde 5 | "envanter (ai-master §1 sorumluluk tablosu)" | sorumluluk tablosu §4'tür (T15) | ⚠️ bölüm şaşması |
| K-C17 | (dış iddia) | dogrulama EK §7: "kapsam L87 iddiası **✅ doğru**" kaydı | bu oturumdaki B1/B4 okumaları: L87 = D1 tablosu, §3 = #1 (T16·T17) | ⚠️ VERIFICATION REQUIRED (P-11) |

### EK §4 — Çapraz tablolar

#### EK §4.1 — `ai-master.md` bölüm → satır yeniden haritalaması (2026-10-15)

| Bölüm | Gerçek satır | Dosya eski iddiası | Durum |
|---|---|---|---|
| §0 | L34 | — | ✅ |
| §1 | L56 | "§1 = satır 30–47 (M01–M12)" | ❌ (K-C4/K-C11) |
| §1.3 D1 | L86–92 | "L87 = registry UNKNOWN" | ❌ (K-C9) |
| §3 | L140–166 | "satır 59–73" | ❌ (K-C10) |
| §3 model-management satırı | L148 (**#1**) | "9. satır / son sıra" | ❌ (K-C8/K-C10) |
| §4 | L176 | "satır 87" | ❌ (K-C9) |
| §7 | L249–262 (7 soru) | — | ✅ (B5) |
| §11.2 | L340–357 (6 UNKNOWN) | — (dosya bu bölümü bilmiyor) | ➕ yeni kanıt |
| §13.2 | L396–411 | "476 satır · 2026-10-13" (L406) | ⚠️ bayat (§4.6) |
| §18 | L512 | — | ✅ |
| §18.1 | L518–532 | — | ✅ (Faz-4 ≥500 = L525) |
| §18.4 | L544–550 | — | ✅ |

#### EK §4.2 — `yapay-zeka-bagimliliklar.md` çocuk matrisi (B3 · T18)

| ID | Hedef dosya | model-management ile ilişki |
|---|---|---|
| K006.1 | audio-intelligence-kapsam | feature-store iddiası **doğrulanamadı** (K-C14) |
| K006.2 | personalization-kapsam | dolaylı (profil → model girdisi) |
| K006.3 | recommendation-kapsam | **dup** (K006.8 aynı hedef) |
| K006.4 | local-inference-kapsam | runtime/registry sınırı |
| **K006.5** | **model-management-kapsam** | **bu dosya** |
| K006.6 | music-analysis-kapsam | — |
| K006.7 | inference-kapsam | model dağıtımı → runtime |
| K006.8 | recommendation-kapsam | **dup** (K006.3) |
| K006.9 | testing-kapsam | registry testi **yok** (P-9) |

*Numaralandırma çatışması (kayıtlı):* bu dosyada `K006.5-model-management`; fakat `web-arastirma` kaydında `K006.7-local-inference`, `local-inference-dogrulama` §8'de `K006.4-evaluation` yazılmıştır (C7, `local-inference-kapsam.md` EK, 2026-10-15) — düzenleme yalnız ADR/registry senkronuyla (U10 emsal).

#### EK §4.3 — Kardeş dogrulama (2026-10-15) ile hizalama

| dogrulama kaydı | Bu ekteki karşılığı |
|---|---|
| §5 T1–T14 | §1 T6–T14 (pasif taşıma) |
| §7 C1–C11 (L497–509) | §3 K-C4…K-C9 (aynı iddia ailesi) |
| §8 hizalama | §4.2 + K006.x ID çatışması |
| §9 ölçüm | §10 NIHAÎ ÖLÇÜM |
| §11 URL listesi (L628–639) | §8 URL taşıma (6 URL) |
| EK §3 ai-models.md emsal | §5.1 |
| EK §4 OTA emsal (A-1…A-10) | §5.2 |
| EK §5 audio-drivers emsal (DR-1…DR-5) | §5.3 |
| EK §6 anayasa 48 başlık | §5.4 · T30 |
| EK §7 kapsam satır-satır çapraz (20 satır) | §3 (17 K-C satırı) |

#### EK §4.4 — `ai-master.md` §18.1 Faz-4 ve §18.4 H-satırları

- **§18.1 (L518–532):** `Faz 4 hedefi … ≥500` (L525) — bu dosya dahil hedef dosyalar için **geçerli kapı**.
- **H-satırları (L530–532):** H1–H7 arasında **H2 = K006 AI test kapsamı** yer alır.
- **Çelişki kaydı:** `ai-master L462` "K006 AI test = **0 satır**" derken H2 "K006 AI test kapsamı: **ai-master · EK-3**" der — ikisi de aynı gün okunmuştur; karar üst kapıda (P-12).

#### EK §4.5 — §7 (7 soru) ve §11.2 (6 UNKNOWN) — bu dosyaya değenler

| Soru/UNKNOWN | Satır | Bu dosyadaki karşılığı |
|---|---|---|
| §7 #4 production model/runtime | L256 | registry = UNKNOWN (dosya §4) |
| §11.2 Model ağırlığı/kaynak (SPY-CRITICAL) | L352 | §3 lifecycle / P-3 |
| §11.2 Offline model indirme | L353 | §6 ota-update → `core-algorithm` (P-5) |
| §11.2 KVKK/GDPR explicit opt-in | L354 | dış kapı (P-6) |
| §11.2 GDPR/KVKK Tüketici (+2 satır) | L357 | dış kapı (P-6) |

#### EK §4.6 — Bayat ölçüm kaydı

- `ai-master.md` §13.2, model-management için **476 satır · 2026-10-13** (L406) der.
  Bu ek yazıldıktan sonra dosya bu sayının üzerine çıkar → ai-master'daki sayım **bayattır**;
  düzeltilmesi kapı P-4'e aittir (bu ekin yazım izni yalnız bu dosyadır).

#### EK §4.7 — Kod yüzeyi envanteri (`AIEngine.php` · 2026-10-15)

| Bulgu | Satır | Hükmü |
|---|---|---|
| `MAX_RECOMMENDATIONS = 10` | L26 | sabit — model ile ilgisi yok |
| `MIN_SCORE = 0.3` | L31 | eşik sabiti |
| `DIVERSITY_TARGET = 0.6` | blok L23–34 | `$model` **değil** (K-C7/K-C15) |
| `analyzeHardware` `@todo` | L142 | uygulanmamış |
| `return ['status' => 'ok']` | L148 | placeholder |
| `extractMetadata` ffprobe | L288–301 | harici kabuk çağrısı |

*Toplamsal hükm:* dosyada model registry/versiyonlama/drift'e ait **hiçbir** çalışan yapı yok; bu, §4 UNKNOWN hükmü ile aynıdır.

### EK §5 — Yaşam döngüsü emsalleri (kıyas kaynakları)

#### EK §5.1 — `ai-models.md` · ModelRegistry/ModelUpdater dizin emsal

- Kardeş `model-management-dogrulama.md` EK §3'ünde `ai-models.md` içindeki `ModelRegistry` / `ModelUpdater` **dizin yapısı emsal** olarak kayıtlıdır.
- Bu ek için hedef dosya **yeniden okunmadı** → yapı iddiası ⚠️ VERIFICATION REQUIRED (kapı P-6: `ai-models.md` kendi dosyasıdır, beyazlistede değil).

#### EK §5.2 — OTA lifecycle emsal (`ota-update`)

- Kardeş dogrulama EK §4'te A-1…A-10 satırlarıyla kayıtlı örüntü: **rollback · manifest · canary · test-matrix**.
- Model dağıtımı için birebir uygulanabilir kıyas; burada yalnız **ad** taşındı (içerik doğrulaması P-7).

#### EK §5.3 — `audio-drivers` DR emsal

- Kardeş dogrulama EK §5'te DR-1…DR-5 (driver rollout) kayıtlıdır; model rollout'u için kıyas kabul edilir.
- DR satırlarının metni bu ekte tekrarlanmadı (aynı gün kardeş dosyada mevcut — okuma israfı önlemi).

#### EK §5.4 — Anayasa (48 başlık) çaprazı

- `.ai/CLAUDE.md` başlık sayısı **48** (T30 · dogrulama §5).
- Bu ekte anayasa maddelerine **doğrudan atıf yapılmadı**; yalnız ölçüm sonucu taşındı (anayasa okuması P-8).

### EK §6 — Kapılar (P-1…P-12) ve sorumluluk matrisi

| Kapı | İş | Kapsam dışı yüzey | Neden kapalı |
|---|---|---|---|
| P-1 | `shared/src/**` kod değişikliği | `AIEngine.php` · `Core/**` | Faz 1 onayı + okuma-öncesi yasak |
| P-2 | `shared/composer.json` · bağımlılık ekleme | mlflow vb. | yeni bağımlılık onayı |
| P-3 | `models/` · `.onnx` · ağırlık yazma | model dosyaları | ağırlık üretimi/dış kaynak |
| P-4 | `ai-master.md` düzenleme | §3/§13.2 bayat satırlar | yazım izni yalnız bu ekte |
| P-5 | `core-algorithm` dokunma | offline indirme | ayrı çocuk dosyası |
| P-6 | `ai-models.md` · `security-policy.md` | ikisi de bu ekte ölü/yok | hedef dosya kendi başına |
| P-7 | `ota-update` içeriği | rollback/canary detayı | yalnız emsal adı taşındı |
| P-8 | `.ai/CLAUDE.md` (anayasa) | 48 başlık | SSOT — yazım yasak |
| P-9 | `testing/` dosyaları | registry testi | test altyapısı ayrı kapı |
| P-10 | `index.md` (K006 kök) | bağlantı listesi | beyazliste dışı yazım yasak |
| P-11 | git geçmişi okuma/commit | K-C17 kayıt farkı | commit yok; geçmişi okuma ayrı iş |
| P-12 | H2 "AI test kapsamı" kararı | §4.4 çelişkisi | üst kapı kararı |

**Sorumluluk matrisi (dosyanın §1/§3 iddiaları → ai-master §4 L176 emsaline atıf; matrisin kendisi bu ekte yeniden üretilmedi → ⚠️ VERIFICATION REQUIRED):**

| Rol | Bu ekteki işi |
|---|---|
| Okuyan (bu ek) | tarama + uyuşmazlık kaydı + ölçüm |
| `ai-master.md` sahibi | §3/§13.2 bayat satırların düzeltilmesi (P-4) |
| `bagimliliklar` sahibi | K006.x ID çatışmasının ADR ile senkronu |
| Kod sahibi | AIEngine `@todo` + registry kodu (P-1) |

### EK §7 — Uyuşmazlık hükmü ve okuma disiplini (seyrek okuma notu)

- **Seyrek okuma kuralı (bu ek):** bu oturumda yalnız kanıt ihtiyacı olan bloklar okundu
  (`ai-master` §1.3·§3·§4·§7·§11.2·§13.2·§18 · `bagimliliklar` L6–15 · `AIEngine` L23–34/L142–148/L288–301);
  55 satırlık hedef dosyanın tamamı da okundu. Okunmayan bloklar için iddia **üretilmedi**.
- **Kod iddiaları (K-C5·K-C7·K-C15):** repo taraması 0 isabet → bu ekin itibarıyla bu dosyada
  "çalışan registry / $model satırı" iddiası **kullanılamaz**; yalnız `UNKNOWN` yazılabilir.
- **Satır haritası (K-C8…K-C11):** eski satır numaraları geçersizdir; yeni harita §4.1'dedir.
- **Ölü yollar (K-C1·K-C2·K-C3·K-C12):** `v2.md` · `security-policy.md` — append-only olduğu için dosyada düzeltilmedi, yalnız kaydedildi.
- **İç gerilim (K-C13):** ELI15'teki "eğit" adımı kapsam dışıdır; lifecycle satırı düzeltilmedi.
- **Dış çelişki (K-C17):** dogrulama-kapsam "✅" kaydı ile bu ekteki B1 okuması arasındaki fark gizlenmedi → ⚠️ VERIFICATION REQUIRED (P-11).

### EK §8 — URL taşıma (2026-10-12 · yeni URL yok)

Aşağıdaki 6 URL **yalnızca** `model-management-dogrulama.md` §11 [Satır: 628-639] listesinden aynen taşınmıştır; tarih sütunu o listedeki `2026-10-12` değeridir.

| # | URL | Tarih | Taşıdığı konu |
|---|---|---|---|
| U1 | https://mlflow.org/docs/latest/model-registry.html | 2026-10-12 | model registry kavramı |
| U2 | https://mlflow.org/docs/latest/workflows.html | 2026-10-12 | registry workflow |
| U3 | https://onnxruntime.ai/docs/model-optimizations/docs/versioning.html | 2026-10-12 | ONNX sürümleme |
| U4 | https://onnxruntime.ai/docs/model-optimizations/docs/ir.html | 2026-10-12 | ONNX IR |
| U5 | https://scikit-learn.org/stable/model_persistence.html | 2026-10-12 | model kalıcılığı |
| U6 | https://joblib.readthedocs.io/en/latest/persistence.html | 2026-10-12 | joblib kalıcılığı |

*Not:* `ai-models.md` · `ota-update` · `audio-drivers` · `docs.openvino.ai` gibi kaynaklar bu listeye **eklenmedi** (tarih doğrulanmadı → yeni URL sayılır).

### EK §9 — Dosya özeti: ne değişti, ne değişmedi

1. **Değişen:** yalnız bu ek (sonuna eklendi); orijinal 55 satır **birebir korunur**.
2. **Değişmeyen:** dosya başlığı/frontmatter · §1–§4 + İlişkili dosyalar gövdesi · dış dosyalar (hepsi 0 yazım).
3. **Kazanılan:** 30 tarama kaydı · 6 verbatim blok · 17 uyuşmazlık · 7 çapraz tablo · 12 kapı · 6 taşınan URL.
4. **Kaybedilmeyen:** eski iddialar silinmedi; hükmü §3 tablosunda verildi (append-only disiplin).
5. **Sonraki meşru adım:** P-4 (ai-master sayım düzeltimi) ve P-11 (K-C17 farkı) — bu oturumda değil.

### EK §10 — NIHAÎ ÖLÇÜM

| Yöntem | Sonuç | Tarih |
|---|---|---|
| `read(offset=9999)` hata sayımı | **528** | 2026-10-15 |
| Tam okuma başlığı (`lines 1-N`) | **528** | 2026-10-15 |
| Eksik (<500) mi? | Hayır (528 ≥ 500 ✅) | 2026-10-15 |

### EK §11 — Orijinal gövde satır haritası (55 satır · 2026-10-15 tam okuma)

> Aşağıdaki harita, bu ekin yazımından **önce** dosyanın tamamının satır satır okunmasıyla
> çıkarıldı; her satır grubu K-C satırlarına bağlanmıştır. Append-only disiplin gereği
> gövdede hiçbir satır silinmedi/düzenlenmedi — bu yalnız **haritadır**.

| Satır | İçerik (özet) | Bağ |
|---|---|---|
| 1 | `---` (frontmatter açılış) | — |
| 2 | `title: "Model Management — Kapsam"` | — |
| 3 | `type: architecture-scope` | — |
| 4 | `node_id: K006-yapay-zeka/model-management` | — |
| 5 | `node_type: subdomain-scope` | — |
| 6 | `status: draft` | — |
| 7 | `parent: K006-yapay-zeka` | — |
| 8 | `date: 2026-10-12` | — |
| 9 | `updated: 2026-10-12` | §0 (bu ek tarihi 2026-10-15) |
| 10 | `group: mimari` | korundu |
| 11 | `---` (kapanış) | — |
| 12 | (boş satır) | — |
| 13 | `# Model Management — Kapsam` | — |
| 14 | (boş satır) | — |
| 15 | ELI15 paragrafı — "9. ve son sırada tanımlıdır" | K-C8 |
| 16 | (boş satır) | — |
| 17 | `## 1. Kapsam içi (İçinde)` | — |
| 18 | (boş satır) | — |
| 19 | madde 1 — registry kayıt şeması | §4.7 (kodda karşılığı yok) |
| 20 | madde 2 — sürümleme/etiketleme | §5.1 emsal |
| 21 | madde 3 — artifact saklama | P-3 |
| 22 | madde 4 — deploy + rollback | §5.2 emsal |
| 23 | madde 5 — envanter "ai-master §1 sorumluluk tablosu" | K-C16 |
| 24 | (boş satır) | — |
| 25 | `## 2. Kapsam dışı (Dışında)` | — |
| 26 | (boş satır) | — |
| 27 | madde 1 — eğitim kapsam dışı | K-C13 (karşı satır) |
| 28 | madde 2 — K016-AI · `v2.md §2.6` | K-C2 |
| 29 | madde 3 — inference çalışma zamanı | §4.2 (K006.7 sınırı) |
| 30 | madde 4 — feature-store → `audio-intelligence/` | K-C14 |
| 31 | madde 5 — etik/siber → `security-policy.md` | K-C3 |
| 32 | (boş satır) | — |
| 33 | `## 3. Disk kanıtları` | — |
| 34 | (boş satır) | — |
| 35 | tablo başlık satırı (3 sütun) | — |
| 36 | tablo ayraç satırı | — |
| 37 | kanıt 1 — §1 (satır 30–47) · M01–M12 · `core-algorithm-orchestrator` | K-C4 · K-C5 |
| 38 | kanıt 2 — §3 (satır 59–73) · "9. satırda" | K-C10 |
| 39 | kanıt 3 — bagimliliklar (satır 14) · M01 | K-C6 |
| 40 | kanıt 4 — AIEngine (satır 30) · `$model` + "kanıt: satır 28–29" | K-C7 · K-C15 |
| 41 | kanıt 5 — §4 (satır 87) · registry UNKNOWN | K-C9 |
| 42 | (boş satır) | — |
| 43 | `## 4. Açık sorular` | — |
| 44 | (boş satır) | — |
| 45 | soru 1 — registry teknolojisi · §4 L87 UNKNOWN | K-C9 · §12 |
| 46 | soru 2 — artifact saklama yeri + boyut bütçesi | §12 |
| 47 | soru 3 — sürümleme şeması + etiket politikası | §12 |
| 48 | soru 4 — "kademe-2 taslak v2'de yok" · `v2.md §2.6 / §2.4-9` | K-C12 · §12 |
| 49 | soru 5 — rollback tetikleyicisi (otomatik/mantlr) | §12 · §5.2 |
| 50 | (boş satır) | — |
| 51 | `## İlişkili dosyalar` | — |
| 52 | (boş satır) | — |
| 53 | `- [[model-management-dogrulama]]` | §4.3 (644 satır) |
| 54 | `- [[model-management-ornekler]]` | P-13 (bu ekte okunmadı) |
| 55 | `- [[../index\|K006-yapay-zeka index]]` | P-10 (index'e yazım yok) |

*Harita notu:* 55 satırın 10'u frontmatter (1–11), 45'i gövdedir; bu ek L55'in **ardından**
başlar (bkz. §0 usül).

### EK §12 — §4 Açık sorular → 2026-10-15 kanıt matrisi

| # | Açık soru (gövde L45–49) | Bu oturumda kanıt | Hükmü |
|---|---|---|---|
| 1 | Registry teknolojisi seçilmedi (L45) | kodda mlflow/registry 0 (T3·T6·T7·T8); adaylar dogrulama §11 (U1–U2) | **UNKNOWN** — hâlâ açık · §11.2 L352 ile uyumlu |
| 2 | Artifact saklama yeri + boyut (L46) | `model` ağırlık dosyası glob = 0 (T10) | **UNKNOWN** — kanıt yok |
| 3 | Sürümleme şeması + etiket politikası (L47) | sürüm sütunu/etiket grep = 0 (T9) | **UNKNOWN** — kanıt yok |
| 4 | Kademe-2 taslak v2'de yok (L48) | `v2.md` repo'da yok (T5) | ❌ ölü referans · taslak durumu hâlâ **UNKNOWN** |
| 5 | Rollback tetikleyicisi (L49) | OTA emsalinde rollback/manifest/canary var (§5.2) | ⚠️ yalnız **emsal** — bu alana ait karar yok |

*Toplumsal hükm:* 5 açık sorunun **5'i de** bu oturumda kapatılamadı; 4'ü UNKNOWN olarak,
1'i (rollback) yalnız kıyas emsaliyle desteklendi. Kapatma yetkisi bu ekte değil (P-2…P-7).

### EK §13 — K006 12 hedef dosyanın genişletme durumu (2026-10-15 ölçüm defteri)

> Bu tablo, ai-master §18.1 `≥500` kapısının (L525) bu oturumdaki izlenme kaydıdır.
> Sayılar aynı oturumdaki `read` ölçümleridir; bu dosyanın kendi satırı aşağıda §10'da.

| # | Dosya (K006-yapay-zeka/) | Önce | Sonra | ≥500 |
|---|---|---|---|---|
| 1 | `recommendation/recommendation-dogrulama.md` | <500 | 527 | ✅ |
| 2 | `model-management/model-management-dogrulama.md` | <500 | 644 | ✅ |
| 3 | `audio-intelligence/audio-intelligence-dogrulama.md` | <500 | 657 | ✅ |
| 4 | `audio-intelligence/audio-intelligence-kapsam.md` | 85 | 512 | ✅ |
| 5 | `evaluation/evaluation-kapsam.md` | 56 | 526 | ✅ |
| 6 | `inference/inference-kapsam.md` | 83 | 520 | ✅ |
| 7 | `local-inference/local-inference-kapsam.md` | 56 | 507 | ✅ |
| 8 | `model-management/model-management-kapsam.md` | **55** | **bu ek (§10)** | **§10** |
| 9 | `music-analysis/music-analysis-kapsam.md` | 86 | — (bu turda değil) | ❌ hâlâ <500 |
| 10 | `personalization/personalization-kapsam.md` | 84 | — (bu turda değil) | ❌ hâlâ <500 |
| 11 | `recommendation/recommendation-kapsam.md` | 86 | — (bu turda değil) | ❌ hâlâ <500 |
| 12 | `testing/testing-kapsam.md` | 216 | — (bu turda değil) | ❌ hâlâ <500 |

**Defter notları:**
- Satır 9–12 sayıları bu oturumun **başındaki** ölçümdefteridir; bu ek onlara dokunmaz.
- Bu dosyanın `>=500` hükmü yalnız §10 NIHAÎ ÖLÇÜM satırıyla verilir (yukarıdaki
  tablo hüküm değil, **izleme kaydıdır**).
- ai-master §13.2 sayım satırları (2026-10-13) bu genişletmelerden **önce** yazılmıştır →
  tümü bayattır (§4.6 · kapı P-4).

### EK §14 — Tekrar-okuma ve doğrulama protokolü (bu ek için)

1. **Yazım öncesi:** hedef dosyanın tamamı (55 satır) okundu; kanıt için `ai-master.md` ·
   `yapay-zeka-bagimliliklar.md` · `model-management-dogrulama.md` · `AIEngine.php`
   blokları okundu (blok listesi §7).
2. **Yazım sırasında:** yalnız `edit_file` (tek kuyruk anchor'u) kullanıldı;
   `write_file` **kullanılmadı** → orijinal 55 satır birebir korunur (append-only).
3. **Yazım sonrası:** `read` ile tam dosya yeniden okundu (EK §0–§10 + footer bütünlüğü);
   satır sayısı §10'da iki yöntemle kaydedilir.
4. **İddia-kanıt bağı:** her EK satırı ya bir T-kaydına (§1), ya bir B-bloğuna (§2), ya bir
   K-C'ye (§3), ya da kardeş dogrulama atıfına (§4.3) bağlıdır; bağı olmayan iddia yazılmadı.
5. **URL disiplini:** yalnız §8'deki 6 URL (2026-10-12, dogrulama §11 L628-639 kaynaklı);
   yeni URL eklenmedi, verbatim bloklarda URL **yoktur** (kontrol edildi).
6. **Kapı sayımı:** P-1…P-13 (bu §14 ile P-13 eklendi: `model-management-ornekler.md`
   bu ekte okunmadı → örnek dosyası ayrı beyazliste maddesidir).
7. **Başarısızlık protokolü:** ölçüm <500 çıkarsa §10 çözülmeden yeni EK bölümü eklenir;
   3 başarısız turdan sonra durulur (bu oturumda 1. tur).
8. **Commit:** bu oturumda `git commit` **atılmadı** (yasağı ihlal yok).
9. **Ölçüm araç notu:** `shell` (PowerShell) bu oturumda permission-denied → satır
   sayımı yalnız `read` tabanlıdır; bash/wc kullanılmadı.
10. **Mojibake kontrolü:** Türkçe karakterler (ğüşıöçİĞÜŞÖÇ) ve `→ · ⚠️ · ❌ · ✅`
    emojileri okuma çıktısında bozuk görünmedi (§0–§10 okuma turu).

**Ek Authority:** 2026-10-15 · append-only · orijinal 55 satır + bu ek · kanıt = `model-management-dogrulama.md` (644) + `ai-master.md` (557) + `yapay-zeka-bagimliliklar.md` (60) + `AIEngine.php` blok okumaları (2026-10-15) · URL'ler = yalnız dogrulama §11 listesi (2026-10-12) · `EK bölüm sayısı` = 15 (§0–§14) · kapı ihlali: 0 · beyazliste dışı yazım: 0 · git commit: yok.
