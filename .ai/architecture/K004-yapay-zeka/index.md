---
title: "K004 AI — Katman Index"
type: index
category: architecture
version: "1.0.0"
status: draft
authority: "SSOT: .ai/architecture/K004-yapay-zeka/index.md — K-space band-1 katmanı (ADR-096)"
updated: 2026-10-08
tier: 3
domain: K004-yapay-zeka
ssot: true
risk: low
owner: mo
depends-on: [".ai/architecture/00-kspace-anayasa.md"]
---

# K004 AI — Katman Index

**Künye**

| Alan | Değer |
|---|---|
| Kategori | architecture (katman `index.md`) |
| Durum | draft (FM `status: draft` · katman durumu = PROPOSED — R16.2, 👤 onayı bekliyor) |
| Tarih | 2026-10-08 |
| Yazar | Claude — Band-1 üretim ajanı (F1 KAPI 9) |
| Onay | Vault Steward (Kapı 10) — **BEKLİYOR**, bu dosya vault'a taşınmadı |
| Bant | Bant 1 — `K000-K020 EXISTING FOUNDATION` (21 kart) |
| Uçak | SOFTWARE (EK A §A.0) |
| Teatral epitet | «MIKNATIS» — yalnız sıfat, K-ID'yi ezmez (R2.3) |
| Sahip (owner) | `mo` (Master Orchestrator — AGENTS.md §4 madde 1: görev dağıtımı, koordinasyon) |
| Üretim yeri | staging (`b1-K004-yapay-zeka.md`) → hedef `.ai/architecture/K004-yapay-zeka/index.md` |

> **Durum etiketleri (F1 §5.4):** `[CURRENT]` (dosya kanıtlı) · `[TARGET]` ·
> `[PROPOSED]` · `[PLANNED]`/`[DESIGN]` · `[VERIFY REQUIRED]`. Ajan mimarisi
> maddeleri F1 §8.13 kapsamında **[PROPOSED]** olarak taşınır (CoreMusic'te
> henüz doğrulanmış uygulama yok — F1 §8.13 notu).

---

#### §1 Genel Bakış

K004, CoreMusic'in **yapay zekâ katmanıdır**: müzik analizi, öneri, sesli
komut, otomatik EQ, ses zekâsı, uç (edge) yapay zekâsı, ML altyapısı ve
kişiselleştirme (EK A §A.1 K004 kartı). Bu kartın en sert sınırı
`.ai/CLAUDE.md` §5 K4 guardrail'idir: **"K5 harici veriye erişemez"** — yani
K004'ün veriye erişimi yalnız K005 üzerinden tanımlıdır; K005 dışı doğrudan
veri kaynağına erişim yasaktır. Repo'da `shared/src/AI/` bileşen ailesi
**mevcuttur** (dosya kanıtlı), bu yüzden maddelerin bir bölümü `IMPLEMENTED`
olarak işaretlenmiştir.

### 1.1 Kapsam Dışı (K004 ne YAPMAZ)

| # | Kapsam dışı | Asıl sahip | Kaynak |
|---|---|---|---|
| 1 | DB şeması, normalizasyon, cache, backup | K005 | EK A §A.1 K005 kartı · §5 K4 guardrail'i |
| 2 | DSP zinciri / EQ uygulamasının kendisi | K003 | EK A §A.1 K003 kartı |
| 3 | Model/oturum/CSRF/CSP/kripto kararı | K006 | `.ai/CLAUDE.md` §5 K6 |
| 4 | İstek hattı, rate limit, session | K007 | EK A §A.1 K007 kartı |
| 5 | REST/BFF/CQRS/event bus yüzeyi | K009 | EK A §A.1 K009 kartı |
| 6 | Kullanıcı arayüzü/tema | K010 · K011 | EK A §A.1 K010/K011 kartları |
| 7 | Gözlem kural/olay üretimi | K012 | R7.1 |

---

## §2 Özet Satır — 16 Alan (R4.1 · F1 §10.2 kontratı)

| K-ID | KANONİK_AD | TEATRAL_EPİTET | DOMAIN | RUNTIME | SORUMLULUK | GİRDİ | ÇIKTI | İZİNLİ_BAGIMLILIK | YASAK_BAGIMLILIK | DATA_BOUNDARY | SECURITY_BOUNDARY | FAILURE_MODE | OBSERVABILITY | TEST | KANIT |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| K004 | AI | «MIKNATIS» | AI | server · edge · desktop | Music Analysis · Recommendation · Voice · Auto EQ · Audio Intelligence · Edge AI · ML Infra · Personalization (EK A, 8 madde) | dinleme/olay akışı (play/skip/like) · sorgu/metin (RAG) · ses analiz girdisi · kullanıcı bağlamı · K005'ten okunan veri (port ile) | öneri/analiz çıktısı · EQ önerisi · sınıflandırma/embedding · yanıt (metin/komut) · olay (öneri üretildi) | K000 · K001 · K002 · K003 (EK A: izinli=K000-K003) + **K005 üzerinden port** (guardrail: K5 dışı veri erişimi yok) | üst katmana (K005-K020) doğrudan erişim · geri çağrı (H20) · veri paylaşımı (H19) · **K005'in DIŞINDAKİ doğrudan veri kaynağı** | çekirdek veri sınırı (EK A) + §5 K4 guardrail'i: "K5 harici veriye erişemez" | security=ORTA (EK A) · OWASP A06 (insecure design — LLM/ajan tasarımı) · prompt/üretim doğruluğu (RAG citation) | fail-over (EK A) · model/servis yoksa kural tabanlı geri düşüş (fallback) — sessiz fallback YASAK (F1 §8.11 I06) | model/çağrı süresi · öneri sayısı · fallback sayısı · hata oranı [PLANNED — K012 ile] | öneri kalite ölçümü (ablasyon) · RAG citation doğruluğu · birim test (§17 ≥80%) [PLANNED] | `.ai/architecture/00-kspace-anayasa.md` satır 89-92 · `.ai/CLAUDE.md` §5 K4 satırı (117) + §10 AI Service · `shared/src/AI/*.php` (7 dosya) + `shared/src/Contracts/AI/*` (6 arayüz) · `.ai/.sql/mysql/coremusic_ai.sql` · ADR-030 · ADR-075 · ADR-035 · EK B #6 · #12 · #24 · #25 · #32 · #33 · #34 |

### 2.1 Alan bazlı değer gerekçesi (R4.1 kontratının açılımı)

| # | Alan | Bu karttaki değer | Gerekçe (kaynak) |
|---|---|---|---|
| 1 | K-ID | `K004` | EK A §A.0 (R2.3) |
| 2 | KANONİK_AD | `AI` | EK A §A.0 |
| 3 | TEATRAL_EPİTET | «MIKNATIS» | EK A §A.0 |
| 4 | DOMAIN | `AI` | katmanın konusu yapay zekâdır |
| 5 | RUNTIME | server · edge · desktop | EK C enum · §10 (AI Service dahili) · Edge AI (EK A) |
| 6 | SORUMLULUK | 8 madde | EK A §A.1 K004 — birebir |
| 7 | GİRDİ | olay akışı · sorgu · ses analizi · bağlam · K005 port'u | Sorumluluk + F1 §8.11 I03'ten türetildi |
| 8 | ÇIKTI | öneri · EQ önerisi · sınıflandırma · yanıt · olay | aynı türetme |
| 9 | İZİNLİ_BAGIMLILIK | K000-K003 (+ K005 port ile) | EK A satır 91 + §5 K4 guardrail'i |
| 10 | YASAK_BAGIMLILIK | K005-K020 doğrudan · H20 · H19 · K005 dışı veri | EK A satır 91 + §5 K4 guardrail'i |
| 11 | DATA_BOUNDARY | çekirdek veri sınırı + "K5 harici veriye erişemez" | EK A satır 91 + §5 satır 117 |
| 12 | SECURITY_BOUNDARY | ORTA + A06 + RAG citation | EK A satır 91 + F1 §9.1 · §8.11 I04 |
| 13 | FAILURE_MODE | fail-over (fallback) | EK A satır 91 + F1 §8.11 I06 |
| 14 | OBSERVABILITY | çağrı süresi · öneri · fallback · hata | tasarım [PLANNED], owner K012 |
| 15 | TEST | ablasyon · citation · birim | F1 §8.11 I07 + §17 hedef |
| 16 | KANIT | anayasa + repo (13 dosya) + ADR + EK B (7 kaynak) | R9.5 3'lü format |

---

## §3 Tam Kimlik Kartı — EK C (20 alan · R4.2/R4.3)

```yaml
K-ID:               K004
KANONİK_AD:         AI
TEATRAL_EPİTET:     «MIKNATIS»
DOMAIN:             AI
SUBDOMAIN:          music-ai                          # Music Analysis · Recommendation · Personalization
BOUNDED_CONTEXT:    audio-intelligence                # Auto EQ · Audio Intelligence · Edge AI · ML Infra
RUNTIME:            server · edge · desktop
SORUMLULUK:         Music Analysis · Recommendation · Voice · Auto EQ · Audio
                    Intelligence · Edge AI · ML Infra · Personalization
GIRDI:              dinleme/olay akışı (play/skip/like/save) · sorgu/metin (RAG) ·
                    ses analiz girdisi · kullanıcı bağlamı · K005'ten port ile okunan veri
CIKTI:              öneri/analiz çıktısı · EQ önerisi · sınıflandırma/embedding ·
                    yanıt (metin/komut) · olay (öneri üretildi)
IZINLI_BAGIMLILIK:  [K000, K001, K002, K003]          # EK A: izinli=K000-K003
                    + K005 (yalnız port üzerinden okuma — guardrail §5 K4)
YASAK_BAGIMLILIK:   [K005..K020 doğrudan erişim, geriye çağrı H20, veri paylaşımı H19,
                    K005 DIŞINDAKİ doğrudan veri kaynağı (filesystem/HTTP/DB dış servis)]
DATA_BOUNDARY:      çekirdek veri sınırı (EK A) + .ai/CLAUDE.md §5 K4 guardrail'i:
                    "K5 harici veriye erişemez" — veri erişiminin tamamı K005 üzerinden
SECURITY_BOUNDARY:  security=ORTA (EK A) · OWASP A06:2025 Insecure Design (F1 §9.1) ·
                    RAG'de citation/provenance zorunlu, kaynaksız üretim REDDEDİLİR
                    (F1 §8.11 I04) · prompt içi sır/credential yok (H04/SEC06)
FAILURE_MODE:       fail-over (EK A) — model/servis yoksa kural tabanlı geri düşüş;
                    sessiz fallback YASAK, başarısızlık sebebi geri beslenir
                    (retry bütçesi ≤ 2 — F1 §8.11 I06)
OBSERVABILITY:      model/çağrı süresi · öneri sayısı · fallback sayısı · hata oranı ·
                    citation kapsamı [PLANNED — K012 ile]
TEST:               öneri kalite ölçümü (her katman ablasyon edilebilir) · RAG citation
                    doğruluğu · birim test (§17 ≥80% — Vitest/PHPUnit) [PLANNED]
KANIT:              .ai/architecture/00-kspace-anayasa.md satır 89-92 |
                    .ai/CLAUDE.md §5 K4 satırı (117) + §10 (AI Service) |
                    shared/src/AI/AIEngine.php · AIOrchestrator.php · AIWorkflow.php ·
                    PromptEngine.php · KnowledgeBase.php · MemorySystem.php ·
                    ToolCalling.php (7 dosya) + shared/src/Contracts/AI/* (6 arayüz) |
                    .ai/.sql/mysql/coremusic_ai.sql | ADR-030 · ADR-075 · ADR-035 |
                    web: EK B #6 (2024-08-16, 95) · #12 (2025, 90 — ön baskı) ·
                    #24 (2026-02-12, 97) · #25 (2025-04-07, 95) · #32 (2025-12-02, 93) ·
                    #33 (95) · #34 (96)
kanit-tarihi:       2026-10-08
kart-durumu:        PROPOSED (R16.2 — 👤 onayı bekliyor)
```

**Kart kalite kapıları (R4.4):**

| Kapı | Sonuç |
|---|---|
| (a) Her alan dolu | GEÇTİ — 20/20 (18 EK C + `kanit-tarihi` + `kart-durumu`) |
| (b) IZINLI ∩ YASAK = ∅ | GEÇTİ — IZINLI = {K000..K003} + K005(port); YASAK = {K005..K020 doğrudan, K005 dışı veri} → kesişim ∅ (K005 port ile izinli, doğrudan yasaklı — iki farklı erişim biçimi) |
| (c) KANIT `⚠️` ise research kapısı | HAYIR — bu kartta web ayağı 7 kaynakla dolu; `⚠️` yalnız ön baskı notu (#12) |
| (d) Aynı veri sınırı iki katman paylaşırsa YARGI ihlali | GEÇTİ — çekirdek veri sınırı münhasır; veri sahibi K005 |

**Epitet kalite notu:** «MIKNATIS» EK A §A.0'dan alınmıştır (R8.1); epitet
kimliği ezmez (R2.3).

---

## §4 Sorumluluk Derinliği

### 4.1 EK A kartı Sorumluluk maddeleri (00-kspace-anayasa.md §A.1 K004)

| # | Kalem (EK A) | Ne yapar | Durum | Kanıt |
|---|---|---|---|---|
| 1 | Music Analysis | Müzik içerik analizi (özellik çıkarımı, sınıflandırma) | IMPLEMENTED (iskelet) | `shared/src/AI/AIEngine.php` docblock: "Recommendation, Audio Analysis, EQ Optimization, Hardware Analysis, Fault Prediction" |
| 2 | Recommendation | Öneri üretimi (üç aşamalı boru hattı — §4.6 I01) | IMPLEMENTED (iskelet) | `AIEngine.php` docblock "Recommendation" · EK B #6 |
| 3 | Voice | Sesli komut/ses arayüzü | PLANNED | `⚠️` — repo'da sesli-komut dosyası eşleşmedi |
| 4 | Auto EQ | Otomatik EQ önerisi | IMPLEMENTED (iskelet) | `AIEngine.php` docblock "EQ Optimization" · ADR-025 (EQ sistemi, K003 tarafı) |
| 5 | Audio Intelligence | Ses donanımı/arıza zekâsı | IMPLEMENTED (iskelet) | `AIEngine.php` docblock "Hardware Analysis, Fault Prediction" |
| 6 | Edge AI | Uç (cihaz-yakını) çıkarım | DESIGN | EK A Sorumluluk · `⚠️` ayrıntı yok |
| 7 | ML Infra | Model/çalışma zamanı altyapısı | DESIGN | EK A Sorumluluk · `⚠️` |
| 8 | Personalization | Kullanıcıya göre kişiselleştirme | IMPLEMENTED (iskelet) | `.ai/.sql/mysql/coremusic_ai.sql` (user preference profiles, listening features — §18 DB12) |

### 4.2 Anayasa §5 K4 satırındaki gerçek kapsam (`.ai/CLAUDE.md` §5, satır 117)

**Satır:** `K4 Yapay Zeka | Music Analysis (10), Recommendation (5), Voice (5), Edge AI, ML Infra | 50 | K5 harici veriye erişemez`

| Alt kapsam | Kapsam | Durum | Kanıt |
|---|---|---|---|
| Music Analysis (10) | 10 analiz birimi (hedef sayım) | TARGET · sayı **hedef** | §5 satır 117 — H10: hedef ≠ kanıt |
| Recommendation (5) | 5 öneri birimi (hedef sayım) | TARGET · sayı **hedef** | §5 satır 117 |
| Voice (5) | 5 ses birimi (hedef sayım) | TARGET · sayı **hedef** | §5 satır 117 |
| Edge AI | uç çıkarım | TARGET | §5 satır 117 · EK A |
| ML Infra | model altyapısı | TARGET | §5 satır 117 · EK A |
| **Hard Guardrail** | "K5 harici veriye erişemez" | BAĞLAYICI | §5 satır 117 |
| "50 bileşen" | §5 sayım sütunu | **HEDEF** | H10 |
| AI Service (§10) | port dahili · PHP + Python · sorumluluk: Recommendations | TARGET | `.ai/CLAUDE.md` §10 satır 205 (7 servis tablosu) |

### 4.3 Repo envanteri (dosya kanıtlı — `shared/src/AI/`)

| Dosya | Docblock özeti (dosyadan okundu) | Durum | Kanıt |
|---|---|---|---|
| `shared/src/AI/AIEngine.php` | "Ana AI işleme motoru implementasyonu. Recommendation, Audio Analysis, EQ Optimization, Hardware Analysis, Fault Prediction." (v1.0.0, since 2026-09-01) | IMPLEMENTED (dosya mevcut) | dosya yolu + docblock (2026-10-08 okundu) |
| `shared/src/AI/AIOrchestrator.php` | "Görev dağıtımı ve orkestrasyon implementasyonu." | IMPLEMENTED | dosya yolu + docblock |
| `shared/src/AI/AIWorkflow.php` | "AI iş akış süreçleri implementasyonu." | IMPLEMENTED | dosya yolu + docblock |
| `shared/src/AI/PromptEngine.php` | "Prompt üretim ve yönetim motoru implementasyonu." | IMPLEMENTED | dosya yolu + docblock |
| `shared/src/AI/KnowledgeBase.php` | "Bilgi bankası implementasyonu — semantic search, RAG, knowledge lifecycle." | IMPLEMENTED | dosya yolu + docblock |
| `shared/src/AI/MemorySystem.php` | "Session hafızası ve persistent state yönetimi implementasyonu." | IMPLEMENTED | dosya yolu + docblock |
| `shared/src/AI/ToolCalling.php` | "Dış servis ve araç çağrısı implementasyonu." | IMPLEMENTED | dosya yolu + docblock |
| `shared/src/Contracts/AI/*.php` | 6 arayüz: AIEngine · AIOrchestrator · KnowledgeBase · MemorySystem · PromptEngine · ToolCalling (Interface) | IMPLEMENTED | `ls shared/src/Contracts/AI/` |
| `.ai/.sql/mysql/coremusic_ai.sql` | AI DB şeması (§18 DB12: tercih profilleri, dinleme özellikleri, öneriler) | IMPLEMENTED (şema) | `git ls-files .ai/.sql/mysql/` (20 dosya) |
| İşlev **doğruluğu** (kod içi davranış) | kod okunmadı → yalnız docblock | `[INFERRED]` | F1 §5.4: çıkarım ≠ olgu |

### 4.4 EK A K004 bloğunun birebir alıntısı (kaynak metin)

```text
### K004 - AI «MIKNATIS» Bant: K000-K020
- Sorumluluk: Music Analysis · Recommendation · Voice · Auto EQ · Audio Intelligence · Edge AI · ML Infra · Personalization
- Sınır: data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=K000-K003 (alt katmanlar) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault
```

*Kaynak:* `.ai/architecture/00-kspace-anayasa.md` satır 89-92 (In-Place · H4/R7).

### 4.5 EK A Sınır satırının madde madde açılımı

| Sınır maddesi | Değer | Bu karttaki karşılığı | Açıldığı bölüm |
|---|---|---|---|
| `data` | çekirdek veri sınırı (+ §5 K4: "K5 harici veriye erişemez") | veri erişimi yalnız K005 port'u üzerinden | §5.3 |
| `security` | ORTA | A06 · RAG citation · sır yok (H04) | §5.4 |
| `failure` | fail-over | kural tabanlı geri düşüş; sessiz fallback yasak (I06) | §5.5 |
| `izinli` | K000-K003 | kök · donanım · sürücü · ses motoru | §5.1 |
| `yasak` | üst katmana doğrudan erişim (yalnız port/adapter) | K005-K020 + H20 + H19 + K005 dışı veri | §5.2 |
| `Kanıt` (EK A) | kaynak prompt K0–K20 bloğu · .ai/ vault | web ayağı EK B ile dolu (7 kaynak) | §7 |

### 4.6 Öneri/AI kural metni (F1 §8.11 I01-I07 — bu kartın bağlayıcı kuralları)

| Kural | Metin (özet) | Kanıt | Durum |
|---|---|---|---|
| I01 | Üç aşamalı boru hattı: RETRIEVAL (binlerce aday) → RANKING (puan) → FILTERING (kısa liste) | EK B #6 (Google Research, 2024-08-16, güven 95) | VERIFIED |
| I02 | Transformer ranking: sırasal eylemler dikkat ağırlıklarıyla; bağlama göre eylem ağırlığı değişir | EK B #6 (aynı kaynak) | VERIFIED |
| I03 | Olay akışı: play/skip/like/save → event bus; çevrimiçi (düşük gecikme) · nearline (embedding) · çevrimdışı (eğitim) üçlü tüketici | F1 §8.11 kural metni + EK B #6 | DESIGN |
| I04 | RAG: chunking → embedding → retrieval ranking → grounding → **citation/provenance zorunlu**; kaynaksız üretim REDDEDİLİR | F1 §8.11 · repo `KnowledgeBase.php` ("semantic search, RAG, knowledge lifecycle") | DESIGN + IMPLEMENTED (iskelet) |
| I05 | Entity grounding üç katmanı: katalog reverse-lookup → prompt-level tüketim kuralları → plan-time guard | F1 §8.11 (arXiv 2607.23718 atıfı — **EK B'de listelenmedi → `⚠️`**) | DESIGN · `⚠️` |
| I06 | Reflective retry: başarısızlık SEBEBİ geri beslenir; **sessiz fallback YASAK**; retry bütçesi ≤ 2 | F1 §8.11 | DESIGN |
| I07 | Her öneri katmanı bağımsız olarak ABLATE edilebilir (ölçüm şart) | F1 §8.11 · EK B #12 (A/B kanıtı bağlamı) | DESIGN |

### 4.7 Ajan/orkestrasyon kuralı (F1 §8.13 — [PROPOSED] durum notuyla)

| Kalıp | Kaynak | Durum |
|---|---|---|
| 5 orkestrasyon kalıbı: sequential · concurrent · group chat · handoff · magentic | EK B #24 (MS Learn, 2026-02-12, 97) · #34 (ikinci bağımsız çapraz, 96) | VERIFIED (kaynak) · uygulama [PROPOSED] |
| Önce tek ajan; model + tools + instructions üçlüsü; guardrail katmanları | EK B #25 (OpenAI, 2025-04-07, 95) | VERIFIED (kaynak) · uygulama [PROPOSED] |
| Merkezî vs dağıtık koordinasyon · supervisor/router · blackboard · context editing | EK B #32 (Anthropic, 2025-12-02, 93) | VERIFIED (kaynak) · uygulama [PROPOSED] |
| Manager (ajanlar-araç olarak) vs handoffs (decentralized) | EK B #33 (OpenAI Agents SDK, 95) | VERIFIED (kaynak) · uygulama [PROPOSED] |
| Repo karşılığı | `AIOrchestrator.php` ("Görev dağıtımı ve orkestrasyon") + `AIWorkflow.php` | IMPLEMENTED (iskelet) |

> F1 §8.13 bu bloğu **[PROPOSED]** etiketiyle yayımlar; CoreMusic'te ajan
> mimarisinin çalıştığına dair **repo doğrulaması yapılmadı** (yalnız docblock).
> Etiket `[PROPOSED]` olarak korunur (H10).

### 4.8 K004 ↔ K005 sınır ayrımı (guardrail'in işletilmesi)

| İş | K004 (bu kart) | K005 (komşu) | Ayırıcı kural |
|---|---|---|---|
| Veri **okuma** | yalnız K005 port'u üzerinden | kaynağı | §5 K4: "K5 harici veriye erişemez" |
| Veri **yazma** | doğrudan yok (öneri sonucu olay/istek olarak iletilir) | sahibi | H19 · YARGI 2 |
| Şema/migrasyon | yok | sahibi (Data Engineer) | AGENTS §5 (`.sql` = Data Engineer) |
| Dosya sistemi (medya) | doğrudan yok | dosya depolama K015/K005 sınırında | guardrail |
| Dış HTTP/servis | ToolCalling üzerinden izinli mi? → **K006/K005 politikası gerekir** | — | `⚠️` — açık madde (§6.7 no. 2) |
| Cache | doğrudan yok (K005 cache zinciri) | APCu/CacheManager | §12 |

---

## §5 Bağımlılık & Sınır

### 5.1 İZİNLİ bağımlılıklar (EK A: izinli = K000-K003 · R6.1)

| Hedef | Tür | Aralık / not |
|---|---|---|
| K000 | alt katman | süreç/bellek servisleri |
| K001 | alt katman | donanım arayüzü (port/adapter) |
| K002 | alt katman | sürücü (özellikle Edge AI/ses girdisi bağlamında) |
| K003 | alt katman | ses analiz girdisi (port/adapter) |
| K005 | **port ile okuma** | guardrail gereği **tek** veri yolu; K005 dışı veri kaynağı yasak |

### 5.2 YASAK bağımlılıklar (R6.1 · F1 H19/H20 · §5 K4 guardrail)

| Yasak | Kaynak | Sonuç |
|---|---|---|
| K005-K020'ye doğrudan erişim | EK A satır 91 | Layer Violation → revert + log CRITICAL |
| Geri çağrı (H20) | F1 §5.1 H20 | yalnız port/adapter |
| Doğrudan veri paylaşımı (H19) | F1 §5.1 H19 · YARGI 2 | data boundary ihlali |
| **K005 harici doğrudan veri kaynağı** (filesystem, doğrudan DB, dış servis) | `.ai/CLAUDE.md` §5 K4 guardrail'i | "K5 harici veriye erişemez" — mutlak yasa |
| Prompt/sır içine credential | F1 H04 · SEC06 | credential kodda/log'da yasak |
| K006 bypass'ı | §5 K6 | asla |

**Olay (event) yukarı serbest:** öneri üretimi/geri besleme olayları
K008/K009/K012'ye event ile yayılabilir (R6.2 · F1 §8.11 I03 event bus);
senkron çağrı yukarı yasaktır.

### 5.3 DATA_BOUNDARY (YARGI 2 + §5 K4 guardrail)

| Konu | Kural |
|---|---|
| Sınır adı | çekirdek veri sınırı (EK A satır 91) + "K5 harici veriye erişemez" (§5 satır 117) |
| K004'ün verisi | model/çıktı durumu (bellek içi), embedding, öneri kuyruğu |
| Kalıcı veri | `coremusic_ai` şemasının sahibi K005'tir; K004 şemaya **doğrudan** yazılmaz |
| Paylaşılan tablo | YOK |
| Veri erişim biçimi | yalnız K005 port'u (read) + olay (write) |

### 5.4 SECURITY_BOUNDARY (EK A: security=ORTA)

| Konu | Değer |
|---|---|
| Seviye | ORTA (EK A satır 91) |
| OWASP eşlemesi | A06:2025 Insecure Design — LLM/ajan tasarımı + threat model (F1 §9.1) |
| Bağlayıcı kural | F1 §8.11 I04: RAG citation/provenance zorunlu, kaynaksız üretim REDDEDİLİR |
| Bağlayıcı kural | F1 §5.1 H04/H10/H15: credential, uydurma kanıt, kaynaksız iddia yasak |
| Prompt güvenliği | prompt üretimi `PromptEngine.php` üzerinden; prompt'a sır enjekte edilmez (SEC06) |
| Devre dışı | K006 bypass'ı yok |

### 5.5 FAILURE_MODE (EK A: fail-over)

| Senaryo | Davranış | Kaynak | Durum |
|---|---|---|---|
| Model/servis yok | kural tabanlı geri düşüş (fallback) | EK A fail-over · F1 §8.11 I06 | DESIGN |
| Başarısız öneri | sebep geri beslenir (reflective retry ≤ 2) | F1 §8.11 I06 | DESIGN |
| Sessiz fallback | **YASAK** | F1 §8.11 I06 | bağlayıcı |
| fail-open | YOK — yalnız ADR ile (F1 SEC14) | — | — |

### 5.6 Observability / Test sınırı

Log/metrik üretimi K012'dedir (R7.1); K004 yalnız **değer** üretir (çağrı
süresi, fallback sayısı). Test stratejisi K013 CI/CD + §17 (≥80%, PHPUnit/Vitest).

### 5.7 Port/Adapter deseni (F1 §8.2 — K004'e uygulaması)

| Port tipi | Yön | Örnek | Adapter | Durum |
|---|---|---|---|---|
| Inbound | K005 → K004 | veri okuma portu | K005 read portu | DESIGN |
| Inbound | K003 → K004 | ses analiz girdisi | K003 event/outbound | DESIGN |
| Outbound | K004 → K005/K008 | öneri sonucu (olay/istek) | event bus / API | DESIGN |
| Kural | — | adapter değişince AI çekirdeği değişmez (F1 §8.2) | — | bağlayıcı |
| Kural | — | K004 doğrudan DB'ye bağlanamaz (H19) | — | bağlayıcı |

### 5.8 Sınır ihlali denetim ve yaptırım akışı (R6.5/R6.6 · anayasa §5.1)

| Adım | Aksiyon | Kaynak |
|---|---|---|
| 1 | İhlal tespiti (ör. K004 → K005 doğrudan PDO sorgusu, ya da K005 dışı dosya okuma) | R6.1 · §5 K4 guardrail |
| 2 | Derhal revert | `.ai/CLAUDE.md` §5.1 |
| 3 | `log.md` CRITICAL girişi | anayasa §5.1 |
| 4 | 👤 bilgi | R6.5 |
| 5 | Döngü → `dep-check` exit 1 → ADR | R6.6 |
| 6 | Kardeş ilişki `refers-to` (düz metin K-ID) | R6.4 · §8.1 |

---

## §6 Girdi-Çıktı · Runtime · Observability-Test

### 6.1 Girdi

| Girdi | Kaynak | Biçim | Durum |
|---|---|---|---|
| Dinleme/olay akışı (play/skip/like/save) | K008/K015 (event bus) | olay kaydı | DESIGN (I03) |
| Sorgu/metin (RAG) | K010/K011 (kullanıcı) | metin | DESIGN (I04) |
| Ses analiz girdisi | K003 (port/adapter) | analiz verisi | DESIGN |
| Kullanıcı bağlamı | K005 port'u | kayıt | DESIGN |
| Model/araç tanımı | K006/K005 yapılandırması | yapılandırma | DESIGN |

### 6.2 Çıktı

| Çıktı | Tüketen katman | Durum |
|---|---|---|
| Öneri/analiz çıktısı | K010/K011 (gösterim) · K009 (API) | DESIGN |
| EQ önerisi | K003 (uygulama K003'te) · K011 | DESIGN |
| Sınıflandırma/embedding | K005 (kalıcılık, olayla) | DESIGN |
| Yanıt (metin/komut) | K009/K010 | DESIGN |
| Olay (öneri üretildi) | K008/K012 (event yukarı) | DESIGN |

### 6.3 Runtime

| Alan | Değer |
|---|---|
| Runtime sınıfları | server · edge · desktop (EK C) |
| Servis karşılığı | AI Service — port dahili · PHP + Python · Recommendations (`.ai/CLAUDE.md` §10) [TARGET] |
| Uçak | SOFTWARE |
| Model/altyapı | ML Infra hedef (§5 K4) — sağlayıcı/sürüm **belirsiz → `⚠️`** |
| Deployment modları | Home · Studio · Car (edge) — §14 |

### 6.4 Observability

| Alan | İçerik | Durum |
|---|---|---|
| Log | — (kural K012'de) | owner K012 |
| Metric | model/çağrı süresi · öneri sayısı · fallback sayısı · hata oranı · citation kapsamı | PLANNED (`⚠️`) |
| Trace | öneri zinciri (retrieval → ranking → filtering) | DESIGN (I01) |
| Health | servis/model sağlık durumu | DESIGN |
| Alert | K012 üzerinden | PLANNED |

### 6.5 Test

| Test tipi | Kapsam | Kabul kriteri | Durum |
|---|---|---|---|
| Ablasyon | her öneri katmanı ayrı kapatılır | ölçüm kaydı zorunlu (I07) | PLANNED |
| Citation doğruluğu | RAG yanıtındaki kaynak | kaynaksız üretim yok (I04) | PLANNED |
| Retry bütçesi | başarısızlık senaryosu | ≤ 2 (I06) | PLANNED |
| Guardrail testi | K005 dışı veri erişimi denemesi | erişim REDDEDİLİR | PLANNED |
| Birim test | `shared/src/AI/*` | ≥80% (§17) | PLANNED |

### 6.6 F1 §8.4 YARGI'larının K004'e uygulaması

| Yargı | Kural | K004 uygulaması | Durum |
|---|---|---|---|
| YARGI 1 | yalnız alt katman tanınır | K000-K003 + K005 (port) | GEÇERLİ |
| YARGI 2 | DATA BOUNDARY | çekirdek veri sınırı + K5 guardrail | GEÇERLİ |
| YARGI 3 | SECURITY BOUNDARY | ORTA + A06 + citation | GEÇERLİ |
| YARGI 4 | OBSERVABILITY | çağrı/fallback/öneri/hata [PLANNED] | TASARIM |
| YARGI 5 | FAILURE_MODE | fail-over (fallback, sessiz yasak) | GEÇERLİ |
| YARGI 6 | EVENT BOUNDARY | olay akışı + öneri olayı yukarı | GEÇERLİ |

### 6.7 Bilinen açık maddeler ve riskler (K004'e özgü)

| # | Açık madde | Etki | Aksiyon |
|---|---|---|---|
| 1 | I05 atıfı (arXiv 2607.23718) EK B'de listelenmedi | `⚠️` | R14 + EK B'ye ekle |
| 2 | `ToolCalling.php` dış servis erişiminin K005 guardrail'iyle ilişkisi belirsiz | sınır boşluğu | K006/K005 ile politika sorusu → 👤 |
| 3 | Voice maddesi için repo kanıtı yok | PLANNED | sesli komut üretimi/kaynağı |
| 4 | ML Infra sağlayıcı/sürümü belirsiz | `⚠️` | R14 research |
| 5 | Kod davranışı docblock ile sınırlı (kod okunmadı) | `[INFERRED]` | kod incelemesi (sonraki faz) |
| 6 | "10 / 5 / 5 / 50" sayıları hedef | H10 | hedef ≠ kanıt ayrı raporla |

---

## §7 Kanıt Kaynakları (R9 — 3'lü kanıt)

| # | Tür | Kaynak (tam yollar) | İçerik | Durum |
|---|---|---|---|---|
| 1 | Anayasa/defter | `.ai/architecture/00-kspace-anayasa.md` satır 89-92 | K004 kartı: Sorumluluk 8 madde · Sınır · Kanıt | GEÇERLİ |
| 2 | Anayasa | `.ai/architecture/00-kspace-anayasa.md` satır 49 (§A.0) | K004 · AI · «MIKNATIS» · SOFTWARE | GEÇERLİ |
| 3 | Anayasa (vault) | `.ai/CLAUDE.md` §5 K4 satırı (satır 117) | Music Analysis (10) · Recommendation (5) · Voice (5) · Edge AI · ML Infra · 50 · guardrail | GEÇERLİ |
| 4 | Anayasa (vault) | `.ai/CLAUDE.md` §10 AI Service (satır 205) · §18 DB12 (`coremusic_ai`) | port dahili · PHP+Python · öneriler · AI DB | GEÇERLİ |
| 5 | Repo (dosya yolu) | `shared/src/AI/AIEngine.php` · `AIOrchestrator.php` · `AIWorkflow.php` · `PromptEngine.php` · `KnowledgeBase.php` · `MemorySystem.php` · `ToolCalling.php` | 7 dosya — docblock içerikleri §4.3'te | GEÇERLİ (dosya + docblock) |
| 6 | Repo (dosya yolu) | `shared/src/Contracts/AI/` (6 Interface dosyası + CLAUDE.md) | arayüz sözleşmeleri | GEÇERLİ |
| 7 | Repo (dosya yolu) | `.ai/.sql/mysql/coremusic_ai.sql` | AI DB şeması | GEÇERLİ |
| 8 | ADR (`ls .ai/.decisions/accepted/`) | `ADR-030-ai-strategy-core.md` | AI strateji çekirdeği | GEÇERLİ |
| 9 | ADR (`ls`) | `ADR-075-ai-database-schema.md` | AI veritabanı şeması | GEÇERLİ |
| 10 | ADR (`ls`) | `ADR-035-system-prompt-engineering.md` | sistem promptu mühendisliği (`PromptEngine` bağlamı) | GEÇERLİ |
| 11 | Web (EK B #6) | `https://research.google/blog/transformers-in-music-recommendation/` (2024-08-16, güven 95) | retrieval → ranking → filtering · attention ağırlıkları | GEÇERLİ |
| 12 | Web (EK B #12) | `https://arxiv.org/pdf/2507.15994` (2025, güven 90) | milyar-parametre önerici · A/B ölçümü (+2.26% / +6.37%) | GEÇERLİ · **ön baskı → hakemli değil, `⚠️` notlu** |
| 13 | Web (EK B #24) | `https://learn.microsoft.com/en-us/azure/architecture/ai-ml/guide/ai-agent-design-patterns` (2026-02-12, güven 97) | 5 orkestrasyon kalıbı | GEÇERLİ |
| 14 | Web (EK B #25) | `https://openai.com/business/guides-and-resources/a-practical-guide-to-building-ai-agents` (2025-04-07, güven 95) | tek ajan önce · model+tools+instructions · guardrail katmanları | GEÇERLİ |
| 15 | Web (EK B #32) | `https://resources.anthropic.com/hubfs/Building%20Effective%20AI%20AGENTS-%20Architecture%20Patterns%20and%20Implementation%20Frameworks.pdf` (2025-12-02, güven 93) | merkezî/dağıtık koordinasyon · supervisor/router · blackboard | GEÇERLİ |
| 16 | Web (EK B #33) | `https://openai.github.io/openai-agents-python/multi_agent/` (güven 95) | manager vs handoffs | GEÇERLİ |
| 17 | Web (EK B #34) | `https://learn.microsoft.com/en-us/training/modules/agent-orchestration-patterns` (güven 96) | #24 ikinci bağımsız çapraz kaynak | GEÇERLİ |
| 18 | Spec (arşiv) | `.ai/prompts/2026-10-08-master-prompt-v2.2.0-f1.md` §8.11 (satır 772-792) · §8.13 (satır 796-801) | I01-I07 kural metinleri · ajan bloğu [PROPOSED] | GEÇERLİ |
| 19 | Kural | `.ai/architecture/rules.md` R2/R3/R4/R6/R9 | isimlendirme · iskelet · format · yön · kanıt | GEÇERLİ |
| 20 | Repo (grep) | Voice/otonom ajan kodu eşleşmedi (2026-10-08) | Voice maddesi PLANNED | `⚠️` |

**3'lü kanıt dengesi (R9.2):** repo 3 · ADR 3 · web 7 → **3/3 dolu → VERIFIED**
(ön baskı #12 ve docblock-tabanlı `[INFERRED]` notları hariç; onlar `⚠️`).

### 7.1 Kanıt dengesi özeti (R9.2/R9.3)

| Ayağı | Durum | Sayı | Sonuç |
|---|---|---|---|
| Repo (dosya yolu + satır) | VAR | 13 dosya (7 AI + 6 interface) + 1 şema | IMPLEMENTED (iskelet/şema) |
| ADR (`ls` ile görülen adlar) | VAR | 3 (+ ADR-096) | strateji/şema/prompt |
| Web (URL + tarih) | VAR | 7 (#6 · #12 · #24 · #25 · #32 · #33 · #34) | öneri + ajan kanıtı |
| Anayasa/kural/spec | VAR | 6 kayıt | kartın taşıyıcısı |
| `kanit-tarihi` | 2026-10-08 | — | R9.3 dolu |
| `STALE` (R11) | gerekmedi | — | tümü 2026-10-08 |

### 7.2 R14 research boşluğu (K004'e özgü — EK B'ye taşınacak)

| # | Açık soru | İlgili alan | Beklenen kaynak tipi |
|---|---|---|---|
| 1 | I05 atıfı: arXiv 2607.23718 (entity grounding) | §4.6 I05 | birincil yayın → EK B'ye ekle |
| 2 | Voice/sesli komut mimarisi | §4.1 madde 3 | tasarım + kaynak |
| 3 | Edge AI çalıştırma ortamı | §4.1 madde 6 | tasarım + kaynak |
| 4 | ML Infra sağlayıcı/sürümü | §4.2 | birincil kaynak |
| 5 | `ToolCalling` dış servis erişiminin sınır politikası | §6.7 no. 2 | K005/K006 kararı → 👤 |
| 6 | Öneri metrikleri (eşik değerleri) | §6.5 | ölçüm — repo'da YOK |

> Cevap bulunana kadar maddeler `⚠️ VERIFICATION REQUIRED` / `PLANNED` kalır (H15).

---

## §8 İlişki & Değişiklik

### 8.1 Kardeş ve bant ilişkileri (düz metin K-ID — wiki-link YASAK)

| Yön | K-ID | İlişki |
|---|---|---|
| Alt katmanlar | K000 · K001 · K002 · K003 | tek izinli `depends-on` |
| Veri sahibi (port ile) | K005 | guardrail: yalnız K005 üzerinden veri |
| Üst komşu | K005 | veri sağlayıcı (port) |
| Band-1 kardeşler | K006 · K007 · K008 · K009 · K010 · K011 · K012 · K013 · K014 · K015 · K016 · K017 · K018 · K019 · K020 | `refers-to` (R6.4) — `depends-on` DEĞİL |
| En sık etkileşim | K005 (veri) · K003 (ses girdisi) · K009/K010 (çıktı) | port/adapter + event |

### 8.2 İzinli wiki-linkler (yalnız mevcut kontrol-plane dosyaları)

- [[architecture/00-kspace-anayasa]] — EK A (K004 kartı kaynağı)
- [[architecture/rules]] — R2/R3/R4/R6/R9
- [[architecture/00-master-index]] — giriş navigasyonu

>Kardeş K-ID'lere wiki-link **yasaktır** (hedefler henüz yok; link-check ihlali).

### 8.3 Geçmiş

| Tarih | Değişiklik | Yapan |
|---|---|---|
| 2026-10-08 | İlk üretim — band-1 K004 `index.md` (staging) | Vault Steward (üretim ajanı) |

### 8.4 Dosya yerleşim planı (ADR-096 §2.2 dizin deseni · R3 iskelet)

| Dosya | Rol | Zorunluluk |
|---|---|---|
| `.ai/architecture/K004-yapay-zeka/index.md` | bu dosyanın vault'taki hedefi | zorunlu (R3.1) |
| `.ai/architecture/K004-yapay-zeka/*.md` (derinleşme) | gerçek karmaşıklık var: öneri boru hattı · RAG · ajan orkestrasyonu | koşullu (R3.1 · F2 §42) |
| `b1-K004-yapay-zeka.md` (staging) | üretim kopyası — vault'a taşınmadı | bu görevin çıktısı |
| Dizin adı | `K004-yapay-zeka` (R2.2) | değişmez (R2.4) |
| Tek dev md | yasak (R3.2) | bağlayıcı |
| min-500 | üretim dosyası ≥500 satır; şişirme yasak (R3.3 · H1) | bağlayıcı |

---

**Authority:** SSOT hedefi `.ai/architecture/K004-yapay-zeka/index.md` — bu kopya şimdilik **staging**'dedir.
**Last Updated:** 2026-10-08
**Mode:** Red Team · Human Mode · Truth Mode
