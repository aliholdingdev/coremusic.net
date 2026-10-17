---
title: "K006-yapay-zeka — ai-master (K-Space V3)"
type: architecture-master
category: architecture
date: 2026-10-12
updated: 2026-10-14
status: draft
version: 2.1.0
node_id: K006-yapay-zeka
node_type: domain
authority: "SSOT: .ai/architecture/index.md"
related:
  - "index.md"
  - "yapay-zeka-kapsam.md"
  - "yapay-zeka-bagimliliklar.md"
  - "yapay-zeka-dogrulama.md"
  - "yapay-zeka-web-arastirma.md"
  - "yapay-zeka-bilesenler.md"
  - "yapay-zeka-kod-ornekleri.md"
  - "yapay-zeka-kararlar.md"
  - "yapay-zeka-riskler.md"
  - "yapay-zeka-test-stratejisi.md"
  - "yapay-zeka-evrensel-dil.md"
group: mimari
---

# ai-master — K006-yapay-zeka

> **Master adı v2'den birebir:** `ai-master.md` (ADR-097 §5.4 — İngilizce `-master` adları
> DEĞİŞTİRİLMEZ; bu dosya 12 dosyalık setin tek İngilizce rol adıdır).

---

## §0 Bu dosya ne? — 12 dosyalık set haritası

| # | Dosya | Ne verir | Ne vermez |
|---|---|---|---|
| 1 | `index.md` | 8 zorunlu alan · çocuk haritası · gezinme | içerik ayrıntısı |
| 2 | `ai-master.md` | **kimlik · sınırlar · kademe-2 envanter · sorumluluk matrisi** | kanıt tablosu (→ §5) |
| 3 | `yapay-zeka-kapsam.md` | ELI15 + içinde/dışında/komşu + sınır kararları | bağımlılık kırılganlığı (→ §4) |
| 4 | `yapay-zeka-bagimliliklar.md` | yön · neden · kırılganlık · entegrasyon sınırı | risk olasılığı (→ §9) |
| 5 | `yapay-zeka-dogrulama.md` | ≥25 satır **iddia · URL · tarih · durum** | tarama hikâyesi (→ §6) |
| 6 | `yapay-zeka-web-arastirma.md` | sistemli tarama · kategori · bulgu · **çelişki** | karar gerekçesi (→ §8) |
| 7 | `yapay-zeka-bilesenler.md` | 9 çocuk × rol · sorumluluk · bağımlılık · kademe-3 ipucu | kod (→ §7) |
| 8 | `yapay-zeka-kod-ornekleri.md` | **gerçek repo kodu** — dosya yolu + satır + blok | mimari karar (→ §8) |
| 9 | `yapay-zeka-kararlar.md` | karar · alternatif · gerekçe · ADR-097 ref | uygulama planı (→ §7) |
| 10 | `yapay-zeka-riskler.md` | risk · etki · olasılık · azaltma · UNKNOWN | test kapsamı (→ §10) |
| 11 | `yapay-zeka-test-stratejisi.md` | değerlendirme metrikleri + test kabul kriteri | üretim ölçümü (→ §10) |
| 12 | `yapay-zeka-evrensel-dil.md` | TR↔EN sözlük | — |
| — | 9 çocuk klasörü | her biri 6 dosya → **54 dosya** | — |

**Set toplamı: 12 kök + 54 çocuk = 66 dosya.**

---

## §1 Kimlik

### §1.1 Kaynak rol cümlesi (v2 §2.6 — birebir, değiştirilmez)

> **"Şarkı öneren, sesi analiz eden yapay zeka parçaları."**

| Alan | Değer | Kaynak |
|---|---|---|
| `node_id` | `K006-yapay-zeka` | v2 §1.1 ağaç · registry §5 satır 6 |
| `node_type` | `domain` (kademe-2) | v2 §4.1 · §4.2 |
| `parent_id` | `K000–K5000 Architecture Registry` | v2 §4.3 şablonu |
| Kaynak dosya | `coremusic-katmanli-mimari-agaci-v2.md` satır 251–268 | disk (G: sürücüsü) |
| Master adı | `ai-master.md` | registry §5 satır 6 |
| Durum | `draft` | registry §5 |
| Üretim tarihi | 2026-10-12 | ADR-097 K7 |

### §1.2 ELI15 kimlik (bu oda ne yapar?)

CoreMusic'in **akıllı tarafı**. Kullanıcı açtığında:

1. **Önerir** — "bunu sevebilirsin" der (`recommendation` · `personalization`).
2. **Dinler** — şarkının BPM'sini, tonunu, enerjisini çıkarır (`music-analysis` · `audio-intelligence`).
3. **Çalıştırır** — modeli barındırıp sonucu üretir (`model-management` · `inference` · `local-inference`).
4. **Ölçer** — önerinin gerçekten iyi olup olmadığını kanıtla söyler (`evaluation` · `testing`).

Bu dört fiil, tek rol cümlesinin **okunabilir açılımıdır**; v2 bunlardan hiçbirini yazmaz →
bu tablo **tasarım yorumu** olarak işaretlidir ve kademe-3'te (ADR-097 K4) kilitlenir.

### §1.3 Kimliğin 4 dayanağı

| # | Dayanak | Ne kanıtlar | Durum |
|---|---|---|---|
| D1 | v2 §2.6 satır 251–268 | rol cümlesi + 9 çocuk adı | ✅ okundu (G: sürücüsü) |
| D2 | `.ai/architecture/index.md` §5 satır 6 | master adı = `ai-master.md` | ✅ okundu |
| D3 | `shared/src/AI/` (7 PHP + 1 CLAUDE.md) | bu domainin **gerçek kod karşılığı var** | ✅ disk ölçümü |
| D4 | `shared/src/Contracts/AI/` (6 arayüz) | sözleşme katmanı mevcut | ✅ disk ölçümü |

---

## §2 Sınır — ne DEĞİL (özet; tam metin: `yapay-zeka-kapsam.md`)

### §2.1 Kapsam DIŞI

| # | Kapsam dışı | Nereye | Tür | Kaynak |
|---|---|---|---|---|
| 1 | LLM/ajan altyapısı, ajan çalışma zamanı, MCP, RAG pipeline | **K015-AI** | ✅ kapsam ayrımı | registry §6 · v2 §2.16 |
| 2 | Şarkı/kullanıcı/liste verisinin saklanması | K007-data | yapısal | v2 §2.7 |
| 3 | İş kuralları ("çal", "beğen", "listeye ekle" anlamı) | K008-domain | yapısal | v2 §2.8 |
| 4 | Öneri/keşif **domain sahipliği** | K020-recomenataion | ⚠️ SINIR AÇIK | ai-master §7/2 |
| 5 | Öneriyi yürüten arka plan süreci | K012-services | TASARIM İDDIASI | — |
| 6 | Kimlik · yetki · oturum güvenliği | K0100-identitiy · K009-security | yapısal | v2 §2.9/§2.10 |
| 7 | UI'da öneri kartlarının çizimi | K011-application | TASARIM İDDIASI | — |
| 8 | Ses motorunun gerçek DSP'si (EQ curve üretimi hariç) | K004-dsp · K003-audio | yapısal | v2 §2.3/§2.4 |
| 9 | Model/depo güvenlik taraması · CI | K009-security · K018-devops | yapısal | v2 §2.18 |
| 10 | Ölçüm/telemetri altyapısı | K017-observability | yapısal | v2 §2.17 |

### §2.2 K006 ↔ K015-AI — bağlayıcı ayrım

| | **K006-yapay-zeka** (bu domain) | **K015-AI** |
|---|---|---|
| Ne | **ML ürün yetenekleri** — öneri, müzik analizi, personalizasyon | **Ajan altyapısı** — agent çalışma zamanı, orchestration, prompt/araç yönetimi |
| Örnek | "Kullanıcıya 10 şarkı öner" | "Bir ajan başlat, aracı çağır, prompt'u yönet" |
| Sahip olduğu | 9 subdomain (§3) | kendi subdomainleri (v2 §2.16) |
| İlişki | ajanlar **bu domaini çağırır** | ajanlar **bu domaini çağırmaz** |
| Kayıt | `.ai/architecture/index.md` §6 satır 94 | aynı |

> **Kural:** ajan/MCP/RAG altyapısı K006'nın içine **taşınamaz**. Depoda bu ayrım ihlal edilmişse
> bu bir **bulgudur**, karar değil → `yapay-zeka-riskler.md` R-07'de kayıtlıdır.

### §2.3 Depodaki gerçek: `shared/src/AI/CLAUDE.md` ihlali

`shared/src/AI/CLAUDE.md` (satır ~14–25) bileşenleri **AIOrchestrator · KnowledgeBase · MemorySystem ·
PromptEngine · ToolCalling** olarak sayar — bunların 5'i ajan/RAG tarafıdır (K015-AI profilinde).
Yani depo katmanı, registry §6'daki **kapsam ayrımıyla çelişmektedir**.

| Bulgu | Durum |
|---|---|
| `shared/src/AI/` altında K015-AI profilinde 5 bileşen | ⚠️ **KAPSAM İHLALİ — VERIFY REQUIRED** |
| Registry §6 ayrımı (ML ürün vs ajan altyapısı) | ✅ kayıtlı |
| Ayrımın kodda uygulanması | ❌ uygulanmamış → `yapay-zeka-riskler.md` R-07 |

> Bu dosyada **taşımaz/yeniden düzenlemez** — yalnızca kayıt tutulur (YASAK 2: yazım yüzeyi
> yalnız `.ai/architecture/K006*`).

---

## §3 Kademe-2 envanter (9 çocuk)

Kaynak: v2 §2.6 mini-ağaç satır 255–268 — **9 çocuk · 0 derinlik** (v2 daha derin göstermiyor).

| # | Çocuk | v2 satır | Rol (bu ağaçtan — tasarım yorumu) | 6 dosya | Durum |
|---|---|---|---|---|---|
| 1 | `model-management/` | 259 | Model kaydı · sürüm · ağırlık saklama · hangi modelin ürettiği | ✅ 6/6 | draft |
| 2 | `inference/` | 260 | Uzak/merkezi model çalıştırma — istek başına tahmin | ✅ 6/6 | draft |
| 3 | `recommendation/` | 261 | Aday üretimi + skorlama + sıralama (hybrid CF + content-based) | ✅ 6/6 | draft |
| 4 | `personalization/` | 262 | Kullanıcı-özel profil · oturum bağlamı · tercih kalıcılığı | ✅ 6/6 | draft |
| 5 | `music-analysis/` | 263 | Ses dosyasından özellik çıkarma (BPM, ton, enerji, mood) | ✅ 6/6 | draft |
| 6 | `audio-intelligence/` | 264 | Ses zekâsı üst katmanı — sınıflandırma, etiketleme, anomali | ✅ 6/6 | draft |
| 7 | `local-inference/` | 265 | Cihaz üzerinde (offline) çalıştırma — yerel donanım | ✅ 6/6 | draft |
| 8 | `evaluation/` | 266 | Öneri/analiz kalitesinin offline metrikle ölçülmesi | ✅ 6/6 | draft |
| 9 | `testing/` | 267 | Regresyon · birim · entegrasyon testi (K019 ile sınır) | ✅ 6/6 | draft |

> **Ad kuralı:** çocuk adları İngilizce teknik terimdir (ADR-097 §5.1: *Türkçe domain + İngilizce
> teknik terim*). Anlam addan okunur; **resmi kapsam tanımı v2'de yoktur** → hepsi
> `⚠️ VERIFICATION REQUIRED` etiketlidir ve kademe-3'te kilitlenir.

### §3.1 Kademe-2 sayım özeti

| Metrik | Değer | Kaynak |
|---|---|---|
| v2 §2.6'daki çocuk sayısı | 9 | satır 259–267 |
| Diskteki alt klasör sayısı | 9 | `Get-ChildItem -Recurse -Directory` (2026-10-12) |
| Eşleşme | **9/9 ✅** | — |
| Her çocukta dosya | 6 (`index` + 5 rol) | disk |
| Çocuk dosyası toplamı | **54** | disk |
| Kök dosya | 12 | disk |
| **Set toplamı** | **66** | disk |

---

## §4 Sorumluluk matrisi (sorumluluk × çocuk × kanıt)

> Bu matris **tasarım yorumudur** — v2 tek cümle verir. Her satır `evidence` sütunuyla taşınır.

| # | Sorumluluk | Birincil çocuklar | İkincil | Repo kanıtı | Etiket |
|---|---|---|---|---|---|
| S1 | Öneri üretmek | recommendation · personalization | evaluation | `AIEngine::getRecommendations` L49–77 | ✅ kod var |
| S2 | Sesi/metriği analiz etmek | music-analysis · audio-intelligence | — | `AIEngine::analyzeAudio` L82–106 | ✅ kod var |
| S3 | Modeli barındırmak & çağırmak | model-management · inference · local-inference | — | `AIOrchestrator::dispatchTask` L71–100 | ⚠️ kısmi |
| S4 | Kaliteyi ölçmek | evaluation · testing | — | (repo'da karşılık **YOK**) | ❌ yok |
| S5 | Oturum bağlamını tutmak | personalization | — | `MemorySystem` (K016 profilinde) | ⚠️ sınır açık |
| S6 | Prompt/tool yönetimi | **(K006'DA DEĞİL)** | — | `PromptEngine` · `ToolCalling` | 🔴 K015-AI |
| S7 | Bilgi bankası / RAG | **(K006'DA DEĞİL)** | — | `KnowledgeBase::ragQuery` L72–91 | 🔴 K015-AI |

### §4.1 Sorumluluk × çocuk kapsama tablosu

| Çocuk | Karşıladığı sorumluluk(lar) | Boşta kalan sorumluluk |
|---|---|---|
| model-management | S3 | — |
| inference | S3 | — |
| recommendation | S1 | — |
| personalization | S1 · S5 | — |
| music-analysis | S2 | — |
| audio-intelligence | S2 | — |
| local-inference | S3 | — |
| evaluation | S4 | — |
| testing | S4 | — |
| **—** | — | **S6 · S7 bu domainde değil (K015-AI)** |

---

## §5 Bağımlılık özeti

Tam tablo: `yapay-zeka-bagimliliklar.md`. Özet — **hepsi TASARIM İDDİASI** (verified değil):

| Yön | Düğüm | Ne | Kırılganlık | Etiket |
|---|---|---|---|---|
| → depends_on | K007-data | öneri girdisi (etkileşim + katalog) | veri yoksa öneri boş döner | TASARIM İDDİASI |
| → depends_on | K008-domain | "öneri/beğeni" iş kuralı tanımı | kural değişince skorlama geçersizleşir | TASARIM İDDİASI |
| → depends_on | K012-services | inference'ı çalıştıracak süreç | süreç yoksa iş kuyrukta kalır | TASARIM İDDİASI |
| → depends_on | K003-audio · K004-dsp | ses özellikleri çıkarma (gerçek DSP) | `extractFeatures()` şu an **placeholder** | ✅ KOD KANITI |
| ← used_by | K011-application | önerinin UI'da gösterilmesi | — | TASARIM İDDİASI |
| ← used_by | K015-AI | ajanların ML yeteneğini çağırması | sınır ihlali riski (§2.3) | TASARIM İDDİASI |
| ← used_by | K019-testing | model kalite / regresyon testi | — | TASARIM İDDİASI |

> **Kod kanıtı ile tasarım iddiası ayrımı:** `extractFeatures()`'ın placeholder olduğu
> `AIEngine.php` satır 315–327'de **kod olarak** okunmuştur — bu bir iddia değil, gözlemdir.

---

## §6 Doğrulama özeti

Tam tablo: `yapay-zeka-dogrulama.md` §8 (30 satır). Özet:

| Ölçüt | Nasıl ölçülür | Sonuç |
|---|---|---|
| Çocuklar = v2 §2.6 birebir (9/9) | satır 255–268 karşılaştırması | ✅ GEÇTİ |
| Master adı = registry §5 satır 6 | `ai-master.md` eşleşmesi | ✅ GEÇTİ |
| `index.md` 8 zorunlu alan | v2 §4.2 kontrolü | ✅ GEÇTİ (8/8) |
| Yeni dosya adları ADR-097 §5.1 | kebab-case · ≤3 kelime · Türkçe rol | ✅ GEÇTİ (11/11) |
| Kök dosya = 12 | disk sayımı | ✅ GEÇTİ |
| Çocuk dosyası = 9×6 = 54 | disk sayımı | ✅ GEÇTİ |
| Web kanıtı ≥25 satır | `-dogrulama` §5 | ✅ GEÇTİ (30 satır) |
| Web taraması ≥8 URL/çocuk | 9 × `-web-arastirma` | ✅ GEÇTİ (9/9 çocukta ≥9 URL) |
| Benzersiz URL toplamı | tekilleştirme | ✅ GEÇTİ (32) |
| Başarısız URL'ler işaretli mi | §6.2 | ✅ GEÇTİ (5 satır) |
| depends_on kanıtlandı | karşılıklı kayıt | ❌ AÇIK → TASARIM |
| K006↔K016 ayrımı kodda uygulanmış mı | `shared/src/AI/` envanteri | ❌ AÇIK → R-07 |
| Öneri sahipliği | ADR | ❌ AÇIK |
| Kademe-3 kapsamı | ADR-097 K4 | ⏳ BEKLİYOR |

---

## §7 Açık sorular

| # | Soru | Etiket | Kapanış koşulu | Kayıt |
|---|---|---|---|---|
| 1 | `ai-master.md` adı registry §5'te K015-AI ile de geçiyor (satır 6 ve 16) | ⚠️ VERIFICATION REQUIRED | ADR kararı | `-dogrulama` §7/1 |
| 2 | Öneri sahipliği: K006-recommendation · K008-recommendation · K020-recomenataion? | ⚠️ KISMEN KAPANDI — 2026-10-14 hibrit bölen kararı (K-11 · §18); ADR kaydı bekliyor | sahiplik ADR'si | `-dogrulama` §7/2 |
| 3 | local-inference donanım gereksinimi (K001/K004 sınırı) | ⚠️ VERIFICATION REQUIRED | gereksinim kaydı | `-dogrulama` §7/3 |
| 4 | Üretim modeli/runtime seçimi (hangi model, hangi format) | **UNKNOWN** | teknoloji karar kaydı | `-dogrulama` §7/4 |
| 5 | 9 çocuğun kademe-3 (bounded-context) yapısı | ⚠️ VERIFICATION REQUIRED | ADR-097 K4 | `-dogrulama` §7/5 |
| 6 | `shared/src/AI/` içindeki K016 bileşenleri taşınacak mı? | ⚠️ VERIFICATION REQUIRED | kapsama ADR'si | `-riskler` R-07 |
| 7 | `extractFeatures()` / `getCollaborativeScores()` placeholder'ları ne zaman gerçeklenecek? | ⚠️ IMPLEMENTED değil | kod teslimi | `-kod-ornekleri` §6 |
| 8 | Otomatik EQ / oda akustiği analizi sahibi (K006.1 ↔ K003/K004) — doküman §1.1–§1.2 vaadi | ⚠️ SINIR AÇIK | kapsam ADR'si | `-kapsam` OQ-4 · §6/P5 |
| 9 | Smart playlist sahibi (K006.7 ↔ K006.8) — doküman §1.2 vaadi | ⚠️ SINIR AÇIK | kapsam ADR'si | `-kapsam` OQ-5 · §6/P4 |
| 10 | "Hata tahmin AI" sahipliği (K006 ↔ K016) — doküman §1.1 vaadi | ⚠️ SINIR AÇIK | kapsam ADR'si (Q2 ile birlikte) | `-kapsam` OQ-6 · kararlar Q2 |

---

## §8 Karar özeti

Tam metin: `yapay-zeka-kararlar.md`. Özet:

| # | Karar | Alternatif | Gerekçe | ADR ref |
|---|---|---|---|---|
| K-01 | K006 = ML ürün, K016 = ajan altyapısı | tek domain | v2 §2.6/§2.16 + registry §6 | ADR-097 K6 |
| K-02 | Master adı `ai-master.md` Türkçe `-anayasa.md` yapılmaz | `-anayasa.md` | ADR-097 §5.4 (v2 adı dokunulmaz) | ADR-097 §5.4 |
| K-03 | 12 kök + 6/çocuk çoklu-MD şeması | tek master dosyası | ADR-097 K2 | ADR-097 K2 |
| K-04 | Çocuk rol dosyası adı `{çocuk}-web-arastirma.md` | `-arastirma.md` | ADR-097 §5.1 ≤3 kelime · Türkçe rol | ADR-097 §5.1 |
| K-05 | Aday teknolojiler **seçilmedi**, yalnızca doğrulandı | "X kullanacağız" | zero-hallucination — karar onayı yok | — |

---

## §9 Risk özeti

Tam metin: `yapay-zeka-riskler.md`. Özet:

| # | Risk | Etki | Olasılık | Azaltma |
|---|---|---|---|---|
| R-01 | Collaborative filtering kolu boş → öneriler tek boyutlu | Yüksek | Yüksek (kod kanıtlı) | ALS entegrasyonu (implicit) |
| R-02 | `extractFeatures()` placeholder → analiz sahte veri üretiyor | Yüksek | Yüksek (kod kanıtlı) | K004-dsp köprüsü |
| R-03 | Kişiselleştirme = kişisel veri → KVKK/GDPR ihlali | Çok yüksek | Orta | açık rıza + itiraz kanalı |
| R-04 | Model/runtime seçimi yok → teknoloji borcu | Orta | Yüksek | teknoloji karar ADR'si |
| R-05 | Öneri sahipliği 3 düğüm → sorumluluk belirsiz | Orta | Yüksek | sahiplik ADR'si |
| R-06 | Ölçüm/eval altyapısı repo'da yok → kalite görünmez | Yüksek | Yüksek | evaluation test altyapısı |
| R-07 | K006/K016 ayrımı kodda uygulanmamış | Orta | Orta | kapsam ihlali ADR'si |

---

## §10 Test stratejisi özeti

Tam metin: `yapay-zeka-test-stratejisi.md`. Özet:

| Katman | Ne ölçer | Kabul kriteri | Durum |
|---|---|---|---|
| T1 Birim | skorlama/filtre saf fonksiyonlar | coverage ≥ %80 (K019 standardı) | ❌ repo'da yok |
| T2 Sözleşme | `AIEngineInterface` 5 imza | dönüş şeması şema-uyumlu | ❌ yok |
| T3 Öneri offline metrik | Precision@k · Recall@k · nDCG@k · MAP | net improvement > baseline | ❌ yok |
| T4 Ses analizi | BPM/ton doğruluğu (ground truth set) | ±0 BPM · ±0 yarım ton | ❌ yok |
| T5 Regresyon | önceki sürümle karşılaştırma | metrik düşüşü %0 | ❌ yok |
| T6 Güvenlik/uyum | kişisel veri sızıntısı, KVKK m.11/1-g | 0 sızıntı · itiraz kanalı var | ❌ yok |

> **Tüm kabul kriterleri HEDEF'TİR** (disk verisi değil) — v2 §4.6 mantığıyla aynı etiket: hedef ≠ ölçüm.

---

## §11 Teknoloji / ekosistem

> Kural: **yalnızca doğrulanmış** kanıt yazılır; doğrulanamayan `UNKNOWN` (uydurma yasak).
> Doğrulama kaydı: `yapay-zeka-dogrulama.md` §5 · tarama: `yapay-zeka-web-arastirma.md`.

### §11.1 Doğrulanmış aday envanteri (seçim YAPILMADI)

Bu tablo **"CoreMusic şunu kullanacak" demez** — yalnızca 2026-10-12'de resmi kaynağı açılıp
doğuğrulanmış adayları listeler. Karar onayı yok → `status: draft` kalır.

| Alan | Aday | Doğrulanmış kanıt | URL | Durum |
|---|---|---|---|---|
| Model formatı (çapraz platform) | ONNX Runtime | "cross-platform machine-learning model accelerator … PyTorch, Tensorflow/Keras, TFLite, scikit-learn" | https://onnxruntime.ai/docs/ | ✅ 2026-10-12 |
| Eğitim çatısı | PyTorch | "PyTorch Foundation" · sayfa 2026-09-24 güncelli | https://pytorch.org/ | ✅ 2026-10-12 |
| Eğitim çatısı (alternatif) | TensorFlow | "easy to create ML models that can run in any environment" | https://www.tensorflow.org/ | ✅ 2026-10-12 |
| Konuşma tanıma | Whisper | 110.308★ · MIT · 2022-09-16 | https://github.com/openai/whisper | ✅ 2026-10-12 |
| Konuşma tanıma (yerel, C/C++) | whisper.cpp | 54.204★ · MIT · 2022-09-25 | https://github.com/ggml-org/whisper.cpp | ✅ 2026-10-12 |
| Ses/MIR özellikleri | librosa | 1.0.0 · "foundational algorithms … MIR systems" | https://librosa.org/doc/latest/ | ✅ 2026-10-12 |
| Ses/MIR özellikleri (C++) | Essentia | 2.1-beta6-dev · cross-platform | https://essentia.upf.edu/ | ✅ 2026-10-12 |
| Ses işleme (Python, JUCE tabanlı) | pedalboard | 6.346★ · GPL v3 · 2021-07-06 | https://github.com/spotify/pedalboard | ✅ 2026-10-12 |
| Öneri framework'ü | Recommenders | 21.928★ · MIT · Linux Foundation AI & Data | https://github.com/recommenders-team/recommenders | ✅ 2026-10-12 |
| Öneri (hybrid) | LibReCommender | 1.5.2 · libreco + libserving | https://librecommender.readthedocs.io/en/stable/ | ✅ 2026-10-12 |
| CF (ALS/BPR) | implicit | 3.829★ · MIT · v0.7.3 · Python ≥3.9 | https://github.com/benfred/implicit | ✅ 2026-10-12 |
| CF (explicit rating) | Surprise | 6.805★ · BSD-3 · 2016-10-23 | https://github.com/NicolasHug/Surprise | ✅ 2026-10-12 |
| Sınıflandırma/temel ML | scikit-learn | 1.9.0 · BSD | https://scikit-learn.org/stable/ | ✅ 2026-10-12 |
| Yerel LLM (C/C++) | llama.cpp | 130.787★ · MIT · 2023-03-10 | https://github.com/ggml-org/llama.cpp | ✅ 2026-10-12 |
| Yerel çalıştırma (masaüstü) | Ollama | 9M+ kurulum/ay · 200T+ token | https://ollama.com/ | ✅ 2026-10-12 |
| Yüksek verimli sunum | vLLM | Stable 0.31.0 · PagedAttention | https://vllm.ai/ | ✅ 2026-10-12 |
| Model kaydı/denetim | MLflow | 50+ metrik · trace · prompt lineage | https://mlflow.org/ | ✅ 2026-10-12 |
| Model deposu | Hugging Face Hub | 2M+ model · 1.5M veri seti | https://huggingface.co/docs/hub/ | ✅ 2026-10-12 |
| Değerlendirme/izleme | Evidently AI | Apache 2.0 · 7500+★ · 40m+ indirme | https://www.evidentlyai.com/ | ✅ 2026-10-12 |
| Takip/visualizasyon | TensorBoard | metrik · histogram · embedding projeksiyonu | https://www.tensorflow.org/tensorboard/ | ✅ 2026-10-12 |
| Uyum — GDPR | GDPR.eu | "Complete guide to GDPR compliance" | https://gdpr.eu/ | ✅ 2026-10-12 |
| Uyum — KVKK | KVKK | resmi kurum sitesi | https://www.kvkk.gov.tr/ | ✅ 2026-10-12 |

### §11.2 UNKNOWN (kanıt yok — uydurulmaz)

| İddia | Durum |
|---|---|
| CoreMusic hangi model formatını kullanacak? | **UNKNOWN** |
| CoreMusic hangi eğitim çatısını kullanacak? | **UNKNOWN** |
| Üretim modelinin boyutu / bellek gereksinimi | **UNKNOWN** |
| local-inference için donanım eşiği (GPU? NPU? sadece CPU?) | **UNKNOWN** |
| K006'nın servis/latency SLO'su | **UNKNOWN** |
| Model lisans uyumlulugu (ticari kullanım) | **UNKNOWN** |

---

## §12 Kaynaklar

| Kaynak | Ne verdi | Tarih |
|---|---|---|
| `G:\Drive'ım\Projelerim\Project-notes\Bayram Ali Prompt Arşivi & Notlar\Coremusic\coremusic-katmanli-mimari-agaci-v2.md` §2.6 (satır 251–268) | rol cümlesi + 9 çocuk | 2026-10-12 okundu |
| aynı dosya §4.1 · §4.2 · §4.3 · §4.4 · §4.5 · §4.6 | 8 kademe · 8 zorunlu alan · şablon · durum geçişi · ID biçimi · matematik | 2026-10-12 okundu |
| `.ai/architecture/index.md` §5 satır 6 · §6 satır 94 · §7 · §8 | master adı · K006↔K016 kaydı · katman sayımı · isimlendirme | 2026-10-12 okundu |
| `.ai/.decisions/draft/ADR-097-…md` §5 · K2 · K4 · K6 | isimlendirme · çoklu-MD · kademe-3 · çakışma kaydı | 2026-10-12 okundu |
| `shared/src/AI/` (7 PHP + CLAUDE.md) · `shared/src/Contracts/AI/` (6 arayüz) | gerçek kod kanıtı | 2026-10-12 disk |
| 32 resmi URL (exa ile açıldı) | teknoloji + uyum kanıtı | 2026-10-12 |

---

## §13 Set derinlik ölçümü — dosya satırları & sürüm alanları (ek 2026-10-13)

> **Ölçüm:** `Get-ChildItem -Recurse -Filter *.md | %{ (Get-Content $_.FullName -Encoding UTF8).Count }`
> · kök dizin `.ai/architecture/K006-yapay-zeka/` · 2026-10-13 · sürüm disiplini: https://semver.org/ (HEAD 200 · 2026-10-13).

### §13.1 Kök 12 dosya — satır derinliği

| # | Dosya | Satır | ≥500 eşiği | Not |
|---|---|---|---|---|
| 1 | `index.md` | 100 | ❌ | gezinme — kapsam dışı (bu görev) |
| 2 | `ai-master.md` | 371 (ölçüm anı) | ❌ | bu ekle kapatıldı |
| 3 | `yapay-zeka-kapsam.md` | 117 | ❌ | §0/§1 özeti yoğun |
| 4 | `yapay-zeka-bagimliliklar.md` | 208 | ❌ | composer kanıtı + aday tablosu |
| 5 | `yapay-zeka-dogrulama.md` | 138 | ❌ | 30 satır iddia·URL·tarih |
| 6 | `yapay-zeka-web-arastirma.md` | 235 | ❌ | 32 URL taraması |
| 7 | `yapay-zeka-bilesenler.md` | 213 | ❌ | 7 PHP + 6 interface envanteri |
| 8 | `yapay-zeka-kod-ornekleri.md` | 396 | ❌ | 9 kod bloğu (setin en derini) |
| 9 | `yapay-zeka-kararlar.md` | 200 | ❌ | 12 karar |
| 10 | `yapay-zeka-riskler.md` | 188 | ❌ | 14 risk |
| 11 | `yapay-zeka-test-stratejisi.md` | 165 | ❌ | 6 katman |
| 12 | `yapay-zeka-evrensel-dil.md` | 190 | ❌ | 104 madde |

### §13.2 Çocuk klasörler — satır toplamı (2026-10-13)

| # | Çocuk | Dosya | Satır toplamı | ≥500 olan dosya |
|---|---|---|---|---|
| 1 | `recommendation/` | 7 | 914 | 1 (`-anayasa` 331) |
| 2 | `music-analysis/` | 6 | 569 | 0 |
| 3 | `audio-intelligence/` | 6 | 563 | 0 |
| 4 | `evaluation/` | 6 | 550 | 0 |
| 5 | `inference/` | 6 | 550 | 0 |
| 6 | `personalization/` | 6 | 539 | 0 |
| 7 | `model-management/` | 6 | 476 | 0 |
| 8 | `testing/` | 6 | 472 | 0 |
| 9 | `local-inference/` | 6 | 453 | 0 |

> **Gözlem:** 9 çocuğun **hiçbir tek dosyası** 500 eşiğini geçmiyor; derinlik üst üste
> binen kısa rol dosyalarından geliyor (index + 5 rol). Bu bir **ölçüm sonucudur**, kusur
> sayılmaz — eşiğe takılan dosyalar ayrı bir derinleştirme işidir.

### §13.3 Sürüm / durum alanları (set geneli)

| Alan | Gözlenen değer | Kaynak | Semver karşılığı |
|---|---|---|---|
| `version` (ai-master) | `2.0.0` | bu dosyanın frontmatter'i | MAJOR=şema, MINOR=ek, PATCH=düzeltme |
| `status` (master) | `draft` | aynı | — |
| `date` / `updated` | `2026-10-12` | aynı | — |
| `node_type` | `domain` | kademe-2 kaydı | — |
| `group` | `mimari` | bu dosya | — |
| Çocuk `status` | `verified` / `verify-required` (dosyaya göre) | çocuk frontmatter'leri | — |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §14 Uyum & veri koruma haritası — master seviyesi (ek 2026-10-13)

> **Kaynak:** mevcut `yapay-zeka-riskler.md` §3 · `recommendation-anayasa.md` §6/§7 ·
> yeni doğrulanan: https://www.edpb.europa.eu/ (HEAD 200 · 2026-10-13) ·
> https://commission.europa.eu/law/law-topic/data-protection_en (HEAD 200 · 2026-10-13).

| # | Yükümlülük yüzeyi | K006'yı nerede vurur | Sorumlu çocuk | Kanıt durumu |
|---|---|---|---|---|
| C1 | Kişisel veri işlemenin hukuki dayanağı | personalization profil kurgusu | K006.7-personalization | ⚠️ VERIFICATION REQUIRED (dayanak kaydı yok) |
| C2 | Açık rıza & geri çekme | öneri tercihleri | K006.7 · K006.8 | ⚠️ TASARIM İDDİASI (`yapay-zeka-riskler.md` R-03) |
| C3 | Veri minimizasyonu | `MemorySystem::setSession` yazılan oturum anahtarları | K006.7 | ⚠️ alt depo konumu **UNKNOWN** |
| C4 | Otomatik karar sınırı | öneri sıralaması + `score` eşiği | K006.8 | ⚠️ VERIFICATION REQUIRED (madde no yazılmadı) |
| C5 | Silme / itiraz talebi | oturum belleğindeki geçmiş | K006.7 | ⚠️ implementasyon **YOK** |
| C6 | Amaç sınırlaması | analiz öznitelikleri (`bpm`, `key`, `mood`) | K006.1 · K006.6 | ✅ öznitelikler şu an **placeholder** (§5 §4) |
| C7 | KVKK üretken YZ şeffaflığı | model çıktısının kullanıcıya sunuluşu | K006.8 | ⚠️ rehber maddeleri **okunmadı** (`-web-arastirma` açık soru) |
| C8 | AB veri koruma kurulumu referansı | sınır ötesi veri aktarımı | K006.7 | ⚠️ VERIFICATION REQUIRED — yalnız resmi kapı açıldı |

> **Dürüstlük:** bu tablo **hukuki görüş değildir**; yalnızca "hangi çocuk hangi sorumlulukla
> karşılaşır" haritasıdır. Madde numarası yazılmayan satırlar uydurulmamış, `⚠️` ile bırakılmıştır.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §15 Doğrulama kanalı & CI yüzeyi (ek 2026-10-13)

> **Kaynak:** https://docs.github.com/en/actions (HEAD 200 · 2026-10-13) ·
> repo gözlemi `.github/workflows/` (2026-09-27 ölçümü, `AGENTS.md` §25.2 — bu oturumda **tekrarlanmadı** → ⚠️).

| # | Kanal | Ne doğrular | K006 için durum |
|---|---|---|---|
| V1 | `ci.yml` (GitHub Actions) | PHP build/test hattı | ⚠️ ölçüm bayat (2026-09-27) → yeniden sayım gerekli |
| V2 | `secret-scan.yml` | sızıntı taraması | ⚠️ aynı |
| V3 | `phpunit ^10.5` (test altyapısı) | birim test koşusu | ❌ K006 AI testi **0 satır** (`-test-stratejisi` §1) |
| V4 | `phpstan` seviye 5 (composer `stan` scripti) | statik tip analizi | ⚠️ bu oturumda çalıştırılmadı |
| V5 | Vault gate scriptleri (`.ai/scripts/*.ps1`) | vault biçim/doğruluk | ❌ K006 kapsamı yok — kökten çalıştırılır |
| V6 | Web URL HEAD doğrulaması | iddia↔kaynak | ✅ bu ekin §13–§16'sında 2026-10-13 yapıldı |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §16 Aday teknoloji genişlemesi — 2. tur envanter (ek 2026-10-13)

> **Kural aynıdır (§11):** bu tablo **seçim değildir** — yalnız 2026-10-13'te HEAD 200 ile
> doğrulanmış resmi kaynaklardır. §11.1'deki 22 adayla **kesişmez** (farklı kaynaklar).

| # | Aday | Ne verir | K006 hangi çocukta kullanır | URL (HEAD 200 · 2026-10-13) |
|---|---|---|---|---|
| N1 | ONNX (şema/operatör kümesi) | model değişim biçimi tanımı | model-management · inference | https://onnx.ai/ |
| N2 | Keras | yüksek seviye eğitim API'si | evaluation (deney) | https://keras.io/ |
| N3 | MLCommons | ölçüm/benchmark projeleri | evaluation · testing | https://mlcommons.org/ |
| N4 | TensorFlow Recommenders | öneri katmanı referans uygulaması | recommendation | https://www.tensorflow.org/recommenders |
| N5 | DVC | veri/model sürümleme | model-management | https://dvc.org/doc |
| N6 | Pydantic | şema doğrulama (Python tarafı) | inference (girdi/çıktı sözleşmesi) | https://docs.pydantic.dev/ |
| N7 | mirdata | ses veri seti yükleyicileri | music-analysis (evaluation verisi) | https://mirdata.readthedocs.io/en/latest/ |
| N8 | OpenVINO | Intel donanımında çalışma zamanı | local-inference | https://docs.openvino.ai/ |
| N9 | ggml | tensör/çalıştırma kütüphanesi (llama.cpp tabanı) | local-inference | https://ggml.ai/ |
| N10 | MLflow model registry (mevcut aday, kayıt akışı) | model kaydı/denetim | model-management | (§11.1 N-· mlflow.org — tekrar değil, akış referansı) |

> **Seçim yapılmadı** — `status: draft` korunur; herhangi birinin "kullanılacağı" iddiası yoktur.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §17 Ek kaynaklar (yalnız §13–§16)

| # | Kaynak | Ne verdi | Erişim |
|---|---|---|---|
| 1 | https://semver.org/ | sürüm disiplini referansı (§13.3) | HEAD 200 · 2026-10-13 |
| 2 | https://www.edpb.europa.eu/ | AB veri koruma kurulumu resmi kapısı (§14) | HEAD 200 · 2026-10-13 |
| 3 | https://commission.europa.eu/law/law-topic/data-protection_en | veri koruma konu sayfası (§14) | HEAD 200 · 2026-10-13 |
| 4 | https://docs.github.com/en/actions | CI kanalı dokümantasyonu (§15) | HEAD 200 · 2026-10-13 |
| 5 | https://spdx.org/licenses/ | lisans tanımları (set lisansı **UNKNOWN**) | HEAD 200 · 2026-10-13 |
| 6 | https://onnx.ai/ · https://keras.io/ · https://mlcommons.org/ | §16 aday kanıtları | HEAD 200 · 2026-10-13 |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode · v2.0.0 · 2026-10-12

---

## §18 Uygulama Yol Haritası (hibrit plan — 2026-10-14, Authority onaylı)

> **Kaynak:** kullanıcı kararları §G/K-11–K-14 (`yapay-zeka-kararlar.md`) · bağlayıcı
> uygulama kuralları §H (aynı dosya) · veri kaynakları kanıtı `yapay-zeka-web-arastirma.md`
> §21 (tur-4). Bu bölüm **plan**tur; hiçbir teknoloji "seçilmiş" değildir (H2).

### §18.1 Faz tablosu (bağlayıcı sıra — K-14)

| Faz | Hedef | Kabul kriteri (ölçülebilir) | Kapanan risk | Durum |
|---|---|---|---|---|
| **1 — Veri temeli** | Etkileşim event logging (`play · like · skip · add_to_playlist · search`) + dış tohum (MusicBrainz katalog · Last.fm/MSD Taste Profile) + sentetik seed | ≥1 etkileşim tablosu BCNF + tek `InteractionLogger` yüzeyi + lisans kapıları (H6) kapatılmış | R-01'e zemin | 🔄 taslak hazır (`yapay-zeka-bagimliliklar.md` §21 — K007 uygulaması bekliyor) |
| **2 — Ses analizi gerçekleştirmesi** | `extractFeatures()` placeholder'ını librosa/Essentia pipeline'ıyla değiştir (BPM · key · energy · mood) | placeholder kalmaz (K-09 kapanır) · 50 şarkılık ground-truth seti · T4: ±0 BPM · ±0 yarım ton | **R-02** | 🔄 şartname hazır (`music-analysis/music-analysis-bilesenler.md` §8 — ADR + kod bekliyor) |
| **3 — Öneri kolları** | Content-based kol (Faz-2 öznitelikleri) + ALS CF (`implicit` adayı) hibrit skorlama | T3 offline metrik (Precision@k · nDCG@k) baseline'a karşı net improvement | **R-01** | ⏳ |
| **4 — Hibrit inference & model-management** | Sunucu ONNX Runtime (aday) + cihaz embedding cache (K-12) · MLflow registry (aday) | üretim model/format/runtime UNKNOWN'ları ADR ile dolar | **R-04** | ⏳ |
| **5 — Evaluation & KVKK** | Drift izleme (Evidently adayı) · açık rıza + silme/itiraz kanalı | T5 regresyon met %0 düşüş · T6 0 sızıntı · itiraz kanalı canlı | **R-06 · R-03** | ⏳ |

### §18.2 Kurallar (özet — tam metin: kararlar §H)

H1 sıralama bağlayıcı · H2 seçim = ADR, aday kodda yok · H3 K016 taşınmaz ·
H4 placeholder üretim sayılmaz · H5 kişisel veri rıza şartı · H6 dış veri lisans kapısı ·
H7 her faz kapanışında `-dogrulama.md`'ye iddia·URL·tarih satırı.

### §18.3 Kapsam notları (bu plan K006 dışına taşmaz)

| Etki | Sahibi | K006 rolü |
|---|---|---|
| Etkileşim veri şeması (BCNF) | K007-data | yalnız gereksinim tanımı (H3/§2.1) |
| "çal/beğen" iş kuralı filtresi | K008-domain | K-11 böleni — K006 yalnız algoritma |
| Keşif akışı/UI sunumu | K020-recomenataion | K-11 böleni |
| Öneriyi çalıştıran arka plan süreci | K012-services | iddia (§5) |
| KVKK rıza/silme uygulaması | K009-security · K010 | gereksinim + test (T6) |

### §18.4 Açık kapılar (planla birlikte yürüyen)

| # | Kapı | Faz | Etiket |
|---|---|---|---|
| 1 | Öneri sahipliği ADR'si (K-11 böleninin resmi kaydı) | 1 öncesi | ⚠️ ADR bekliyor |
| 2 | Last.fm ticari izin + MSD/Taste Profile lisansı (§21.1/O1 · O4) | 1 | ⚠️ hukuki kapı |
| 3 | Model format/runtime teknoloji ADR'si (§11.1 — hâlâ aday) | 4 | UNKNOWN→ADR |
| 4 | Ground-truth ses seti kaynak/ürün hakkı | 2 | ⚠️ VERIFICATION REQUIRED |
| 5 | K006↔K016 veri sözleşmesi (Q5) | 3–4 sınırı | 🔴 açık |

> **Dürüstlük:** bu yol haritasındaki hiçbir faz başlamamıştır; durum sütunu "⏳" = plandadır.
> Faz-1 başladığında bu tablo §18.5 ile güncellenir ve `-dogrulama.md` §2'ye kanıt satırları düşer.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode · v2.1.0 · 2026-10-14
