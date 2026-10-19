---
node_id: rag-anayasa
node_type: module
status: active
parent_id: rag
children: []
depends_on: [rag-index]
evidence:
  - ".ai/RAG.md (177 satır, status: active, 2026-10-12)"
  - ".ai/.decisions/accepted/ADR-030-ai-strategy-core.md (389 satır)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı"
open_questions: []
group: mimari
---

# rag-anayasa - Retrieval Kuralları (RAG)

> **Bu dosya ne değildir:** retrieval pipeline'ının **kodunu** (embedding/vektör store uygulaması) ve
> `.ai/RAG.md` §3 indeksinin **tam kopyasını** taşımaz; burada **getirme anayasası** vardır —
> soru→dosya eşlemesi nasıl okunur, pipeline hangi dürüst etiketle taşınır, kapsam nerede biter.

## §1 Rol (ELI15)

RAG alt-domaini, "bu soru hangi dosyayı okumalı?" sorusunun **tek tablodan** cevaplanmasını sağlayan
retrieval indeksinin ve gelecekteki otomatik getirme (embedding / vektör arama) sisteminin **tasarım
kayıdının** sahibidir. Dosya bir **süreç dosyası değildir**; kural koymaz, yalnız yönlendirir ve durum
raporlar — kanıt: `.ai/RAG.md:38-39` ("Bu dosya bir süreç dosyası değildir; kural koymaz, yalnız
yönlendirir ve durum raporlar").

| Soru | Cevap | Kanıt |
|------|-------|-------|
| Bugünkü getirme nasıl yapılır? | Manuel keyword + §3 tablosu (elle okuma) | `.ai/RAG.md:116` · `.ai/RAG.md:120` |
| Otomatik sistem var mı? | Hayır — `PLANNED` | `.ai/RAG.md:111` (ADR-030 atfı) |
| Kod karşılığı var mı? | `KnowledgeBase::ragQuery` keyword-tabanlı iskelet | `shared/src/AI/KnowledgeBase.php:72-91` |
| Kim günceller? | MO (vault-updater) | `.ai/RAG.md:22` |

> **eli10:** Bu dosya, aramanın "nereden başlanacağını" ve "sistem henüz kurulu değil" dürüstlüğünü
> aynı yerde tutar. > **eli15:** Yönlendirme bilgisi kural metninden daha sık değişir → ayrı tablo
> (`Dosya: .ai/RAG.md · Satır: 50`).

## §2 Kapsam (in / out)

| Kapsam | Kapsam Dışı | Kanıt |
|--------|-------------|-------|
| §3 retrieval indeksi — konu → hedef dosya → keyword → durum | Dosya içeriği özetleri (yalnız yol + rol + keyword) | `.ai/RAG.md:58` |
| §4 pipeline tasarımı — chunk/embedding/store/arama + durum sütunu | Embedding/vektör **kodu** (ADR-030; kod ayrı görev) | `.ai/RAG.md:59` |
| §5-§6 güncelleme kuralı + doğrulama | ADR metinleri (frozen — yalnız wiki-link) | `.ai/RAG.md:60` |
| Kaynak kapsamı: `.ai/**/*.md` + kök boot dosyaları | Alt alan adı uygulama kodunun tamamı (yalnız eşleme satırı) | `.ai/RAG.md:61` |
| Kullananlar: tüm AI ajanları (görev başı 1 kez), MO, Vault Steward | İçerik yazımı/özetleme | `.ai/RAG.md:63` |
| Ön koşul: indeks satırları glob ile doğrulanmış, `Durum` sütunu dolu | Ölçülmemiş sayı iddiaları | `.ai/RAG.md:64` |

**Ölçüm tarihi:** 2026-10-07 (tüm sayılar o tarihte diskten ölçülmüştür — `Dosya: .ai/RAG.md · Satır: 65`).
`.ai/RAG.md` §3.1 başlığı kendi ölçümünü **2026-10-14** olarak taşır (`Dosya: .ai/RAG.md · Satır: 71`) —
iki tarih aynı tabloda geçerlidir, yenisi eskiyi silmez.

## §3 Retrieval İndeksi — nasıl okunur (.ai/RAG.md §3)

§3 iki alt bölümdür ve **sırayla** okunur:

| # | Alt bölüm | Ne verir | Kanıt |
|---|-----------|----------|-------|
| 1 | §3.1 Konu → Dosya Eşlemesi | 20 satırlık ana tablo (konu, hedef, keyword, durum) | `.ai/RAG.md:71-94` |
| 2 | §3.2 Eşleme Kuralları | 4 kural + ihlal sonucu | `.ai/RAG.md:98-105` |
| 3 | Kapsam notu | Olmayanlar (uydurulmaz) | `.ai/RAG.md:96` |

Okuma sırası: (i) sorunun anahtar kelimeleri §3.1 `Keyword'ler` sütununda aranır; (ii) eşleşen satırın
`Hedef Dosya(lar)` sütunu ilk okuma hedefidir; (iii) `Durum` sütunu `PLANNED` ise o konuda **kod yoktur**,
yalnız ADR/plan okunur. §3.1 tablosunda satır numarası **9 yoktur** (1-8, 10-21 = 20 satır) — boşluk
bilinçlidir, yeniden numaralandırma **yasak** (`Dosya: .ai/RAG.md · Satır: 71` "20 satır").

## §4 Soru → Dosya Eşleme Örnek Tablosu (§3.1'den seçilmiş 10 satır)

| # | Soru sınıfı | Hedef dosya(lar) | Keyword | Durum | Kanıt satırı |
|---|-------------|------------------|---------|-------|--------------|
| 1 | Boot sırası, kanonik okuma listesi | `.ai/CLAUDE.md` §16 · `.ai/WORKFLOW.md` | boot, okuma, kanonik, 13 dosya | IMPLEMENTED | `.ai/RAG.md:75` |
| 2 | Guardrail, yasak, anayasa | `.ai/CLAUDE.md` §7 · §21 · §23 | guardrail, yasak, revert | IMPLEMENTED | `.ai/RAG.md:76` |
| 3 | Agent routing, handover, persona | `.ai/AGENTS.md` §4 · §6 · §15 | agent, routing, persona | IMPLEMENTED | `.ai/RAG.md:77` |
| 4 | Süreç, faz, hard gate, ADR lifecycle | `.ai/WORKFLOW.md` §8-§13 | faz, gate, süreç | IMPLEMENTED | `.ai/RAG.md:78` |
| 8 | Mimari kararlar (ADR) | `.ai/.decisions/index.md` · `.ai/brain.md` | adr, karar, frozen | IMPLEMENTED | `.ai/RAG.md:82` |
| 17 | PHP/backend kod yüzeyi | `shared/src` (165 .php) · alt alan adı `*/include/` | php, pdo, middleware | IMPLEMENTED | `.ai/RAG.md:90` |
| 18 | AI stratejisi, RAG/embedding kararı | `.ai/.decisions/accepted/ADR-030-ai-strategy-core.md` | rag, embedding, ai-stratejisi, llm | **PLANNED** | `.ai/RAG.md:91` |
| 19 | Güvenlik: CSRF/CSP/JWT/session | ADR-010 · ADR-011 · ADR-022 · skill `security-hardening` | csrf, csp, jwt | IMPLEMENTED | `.ai/RAG.md:92` |
| 21 | Ses motoru, mixer, EQ/DSP, reverse-engineering | `projects/NevaEngine/` (36 md) · `.ai/brain.md` §7.4 | neva, mixer, juce, asio | IMPLEMENTED | `.ai/RAG.md:94` |
| 12 | DB şema, BCNF, migration | `.ai/.sql/` (21 .sql) · ADR-040 · skill `database-normalize-maker` | bcnf, şema, sql | IMPLEMENTED | `.ai/RAG.md:85` |

**Bu dosyanın işi bu tabloyu yeniden yazmak değil;** güncel sürüm `.ai/RAG.md` §3.1'dedir (SSOT) —
buradaki 10 satır **örnek okuma**dır ve çelişirse `.ai/RAG.md` kazanır (`Dosya: .ai/RAG.md · Satır: 9`
"çelişkide disk kazanır").

## §5 Eşleme Kuralları (.ai/RAG.md §3.2 — 4 madde)

| # | Kural | İhlal Sonucu | Kanıt |
|---|-------|-------------|-------|
| 1 | Satır yalnız diskte var olan dosyaya gider (Test-Path / glob) | Hayalet satır silinir | `.ai/RAG.md:102` |
| 2 | Birden çok hedef varsa en dar (domain-specific) dosya önce yazılır | Yanlış öncelik | `.ai/RAG.md:103` |
| 3 | Durum sütunu: `IMPLEMENTED` (dosya kanıtlı) · `PLANNED` (ADR/plan var, kod yok) · `UNKNOWN` | Durumsuz satır geçersiz | `.ai/RAG.md:104` |
| 4 | Sayılar (96 md, 65 şablon, 21 sql …) yalnız ölçümle yazılır | Zero-Hallucination ihlali | `.ai/RAG.md:105` |

Ek bağlayıcı kural: indeks satırı **disk kanıtlıdır** (glob) — Guardrail #3 (`Dosya: .ai/RAG.md · Satır: 45`);
yeni dosya **şablonla** üretilir — Guardrail #16 (`Dosya: .ai/RAG.md · Satır: 46`).

## §6 Pipeline (7 bileşen — `.ai/RAG.md` §4)

> "ADR-030 (AI Strategy Core) tespiti: *RAG / embedding / vektör — ❌ PLANNED (kod 0, tablo 0)*" —
> `Dosya: .ai/RAG.md · Satır: 111`. Aşağıdaki tablo **tasarımdır**; `IMPLEMENTED` tek satır hariç
> hiçbir bileşen kodla desteklenmez (`Dosya: .ai/RAG.md · Satır: 112`).

| # | Bileşen | Tasarım (karar) | Durum | Kanıt |
|---|---------|----------------|-------|-------|
| 1 | Manuel keyword indeksi | `.ai/keys.md` + `.ai/RAG.md` §3 (taban retrieval'ı) | IMPLEMENTED | `.ai/RAG.md:116` |
| 2 | Chunk stratejisi | Bölüm (`§`) bazlı, hedef ~500 token; başlık + dosya yolu metadata | PLANNED | `.ai/RAG.md:117` |
| 3 | Embedding adayı | Model/servis seçimi YAPILMADI | PLANNED | `.ai/RAG.md:118` |
| 4 | Vektör store | Seçim YAPILMADI (adaylar: SQLite-vec / MySQL 9 / harici servis — değerlendirilmedi) | PLANNED | `.ai/RAG.md:119` |
| 5 | Arama sırası | exact keyword → BM25 → vektör (semantik); ilk ikisi bugün el ile | PLANNED | `.ai/RAG.md:120` |
| 6 | Güncelleme tetikleyicisi | Vault'a dosya ekleme/taşıma → `vault-sync-post` ile §3 yeniden ölçülür | PLANNED | `.ai/RAG.md:121` |
| 7 | Kaynak kapsamı | `.ai/**/*.md` + kök boot `.md`; kod `.php/.js` şimdilik KAPSAM DIŞI | PLANNED | `.ai/RAG.md:122` |

> **eli10:** Otomatik arama sistemi henüz kurulmadı; bugün arama elle yapılıyor (`Dosya: .ai/RAG.md · Satır: 124`).
> **eli15:** Tasarımı olan ile olan şey karıştırılınca "sistem var" sanılır → her satır dürüst etiketlidir
> (`Dosya: .ai/RAG.md · Satır: 125`).

## §7 Durum Etiket Disiplini (IMPLEMENTED / PLANNED / UNKNOWN)

| # | Etiket | Koşul | Yanlış kullanım sonucu | Kanıt |
|---|--------|-------|------------------------|-------|
| 1 | `IMPLEMENTED` | Dosya diskte + içerik kanıtlı | "Var" sanılan sistem yok | `.ai/RAG.md:104` |
| 2 | `PLANNED` | ADR/plan var, kod/yok | Uydurma teslim vaadi | `.ai/RAG.md:104` |
| 3 | `UNKNOWN` | Ölçüm yapılmadı | Tahmin = hata | `.ai/RAG.md:104` |
| 4 | `⚠️ VERIFICATION REQUIRED` | İddia kanıtlanamadı | Hallüsinasyon | `.ai/AGENTS.md` (Zero-Hallucination, kök boot) |

**Zincir kuralı:** §4'te bir satır `IMPLEMENTED` olacaksa o bileşenin dosyası diskte bulunmalı ve kanıt
sütununa yazılmalıdır (`Dosya: .ai/RAG.md · Satır: 125`). RAG için bugünkü doğru harita: **§3 manuel
indeks IMPLEMENTED · §4 pipeline PLANNED** (`Dosya: .ai/RAG.md · Satır: 177`).

## §8 ADR-030 Kararları (RAG/embedding çekirdeği)

| # | Karar | Metin (özet) | Kanıt |
|---|-------|--------------|-------|
| 1 | ADR durumu | `status: accepted` — frozen YOK | `ADR-030:8` · `ADR-030:16` |
| 2 | Debate | ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL) | `ADR-030:11` · `ADR-030:296` |
| 3 | Altyapı | RAG/embedding **PLANNED (kod 0)** | `ADR-030:18` |
| 4 | RAG yüzeyi | `KnowledgeBase.php:48` "Basit keyword matching (gerçek implementasyonda vector cosine similarity)" → **yorum = niyet, kod 0** | `ADR-030:43` |
| 5 | Vektör tablosu | Yok — `coremusic_ai.sql` 6 tabloda embedding kolonu 0 → PLANNED | `ADR-030:56` |
| 6 | Boru hattı kanonu | chunk→embed→index→retrieve→rerank (standart, literatürle doğrulandı) | `ADR-030:207` · `ADR-030:95` |
| 7 | Karar adımı | §5.1/6 — vektör şeması + embedding modeli + chunk politikası **tek ayrı adım** | `ADR-030:272` |

## §9 ADR-030 Kanıt Zinciri (kod 0 → PLANNED)

| # | Tarama / gözlem | Sonuç | Kanıt |
|---|-----------------|-------|-------|
| 1 | `embedding\|vector_db` (`*.php`) | **0 eşleşme** (yorum satırı hariç) | `ADR-030:36` |
| 2 | Öneri/LLM **yazan** Controller/Service/Repository | 0 dosya | `ADR-030:55` |
| 3 | RAG/augmentation satırı | `KnowledgeBase.php:48` + `:77` — yorum var, kod 0 | `ADR-030:43` |
| 4 | Açık PLANNED kalemi | "RAG / embedding / vektör ❌ PLANNED (kod 0, tablo 0)" | `ADR-030:305` |
| 5 | Sonuç etiketi | PLANNED listesinde "RAG/embedding/vektör (kod 0)" | `ADR-030:69` |
| 6 | Durum özeti | "LLM API/RAG/embedding/yerel model/öneri verisi 0" | `ADR-030:356` |

**Sonuç:** bu anayasada hiçbir yerde "RAG sistemi çalışıyor" ifadesi **geçmez**; geçerse ihlaldir
(`ADR-030:239`: "AI özellikleri çalışıyor" iddiası bugün **yalandır**).

## §10 Debate Şartı 3 — RAG/Embedding Yol Haritası (bağlayıcı)

| # | Şart | Kapsam | Sorumlu | Durum | Kanıt |
|---|------|--------|---------|-------|-------|
| 1 | RAG/embedding yol haritası | chunk→embed→index→retrieve→rerank boru hattı yol haritası maddesi olarak yazılır | Data Engineer + Backend Architect | ⏳ PLANNED | `ADR-030:320` |
| 2 | Vektör tablosu + model seçimi | §5.1/6'da **ayrı karar adımı** — bu ADR'de taahhüt yok | Data + Backend | ⏳ PLANNED | `ADR-030:320` |
| 3 | Geri dönüş maddesi (e) | RAG kararı olumsuzsa `KnowledgeBase` keyword matching **dokunulmadan** kalır | Vault Steward | koşullu | `ADR-030:283` |

Kabul ≠ tamamlandı: 3 şart bağlayıcıdır, kapanış §5.1 adımlarındadır (`Dosya: ADR-030 · Satır: 322`).

## §11 K015 / K006 Sınırı (kapsam ayrımı)

| # | Konu | Sahip | Kanıt |
|---|------|-------|-------|
| 1 | Bu alt-dümğüm yalnız K015-AI (ajan altyapısı) altındadır | K015-AI/rag | `rag/index.md:27` |
| 2 | K006-yapay-zeka (ML ürün) kapsamı **DEĞİLDİR** | K006 | `rag/index.md:27` |
| 3 | Kademe-3 (Bounded Context/Module) bu üretimde AÇILMADI; children boş | — | `rag/index.md:6` · `rag/index.md:28` |
| 4 | Öneri motoru/LLM ürün specleri K4 (`architecture/k4-yapay-zeka/*`) | K4 / ADR-030 | `ADR-030:62` |
| 5 | Model eğitimi (fine-tune) bu anayasanın dışında | ayrı ADR (ADR-088+) | `ADR-030:219` |

## §12 `.ai/RAG.md` ile İlişki (çift katman)

| # | Dosya | Rol | Durum | Kanıt |
|---|-------|-----|-------|-------|
| 1 | `.ai/RAG.md` | **SSOT** — retrieval indeksi + pipeline tasarımının tam metni | active, v1.1.0, updated 2026-10-12 | `.ai/RAG.md:6-10` |
| 2 | Bu dosya (rag-anayasa) | K015-AI altındaki **anayasal özet + kural seti** | active | bu FM |
| 3 | `.ai/architecture/K015-AI/rag/index.md` | Klasör künyesi + çocuk kataloğu | draft | `rag/index.md:4` |
| 4 | Çelişki kuralı | `authority: "SSOT (klasör kökü) — çelişkide disk kazanır"` | bağlayıcı | `.ai/RAG.md:9` |

`.ai/RAG.md` §6/9 "çift katman" kontrolü şunu ister: kök pointer ile SSOT ayrımı korunur
(`Dosya: .ai/RAG.md · Satır: 154`). Bu anayasa üçüncü bir SSOT **açmaz** — indeks satırı çelişince
`.ai/RAG.md` kazanır.

## §13 Boot'a Neden Eklenmez

| # | Kural | Neden | Kanıt |
|---|-------|-------|-------|
| 1 | Boot listesine (13 kanonik) eklenmez — **on-demand** referanstır | Sayım zinciri (13 = 14−1) bozulur | `.ai/RAG.md:47` · `.ai/RAG.md:136` |
| 2 | Boot §16 listesi bu dosyayla **değiştirilmez** | `.ai/CLAUDE.md` §16 sabittir | `.ai/RAG.md:136` |
| 3 | Okuma anı | Görev başı 1 kez (soru sınıfı eşleşmesinde) | `.ai/RAG.md:63` |
| 4 | Yerleşim | `.ai/` kök boot paketi dışındaki architecture ağacında | `.ai/architecture/K015-AI/` (disk) |

Ek gerekçe: yönlendirme bilgisi kural metninden **daha sık değişir** ve tek tabloda taranır → boot'a
değil, on-demand okumaya uyar (`Dosya: .ai/RAG.md · Satır: 50`).

## §14 Güncelleme Kuralı (vault'a yeni dosya → §3 güncelle)

| # | Tetikleyici | Aksiyon | Sorumlu | Kanıt |
|---|-------------|---------|---------|-------|
| 1 | Vault'a dosya ekleme | §3 satırı glob ile yeniden ölçülür | MO (vault-updater) | `.ai/RAG.md:121` |
| 2 | Vault'ta dosya taşıma | Eski satır hedefi yeniden Test-Path edilir; hayalet satır silinir | MO | `.ai/RAG.md:102` |
| 3 | Pipeline bileşeni kod yazıldı | §4'te durum `PLANNED` → `IMPLEMENTED` + kanıt sütunu | Vault Steward | `.ai/RAG.md:50` |
| 4 | Şablonsuz yeni `.md` | DUR — Guardrail #16 şablon okuması zorunlu | tüm ajanlar | `.ai/RAG.md:46` |
| 5 | Yeni ADR | `.ai/.decisions/index.md` + `keys.md` eşlemesi birlikte güncellenir | MO | `ADR-030:21` (altı katalog kaydı örneği) |

Tetikleyici skill'i `vault-sync-post` **vardır** (`Dosya: .ai/RAG.md · Satır: 121`); skill adı vault
tarafında kayıtlıdır, kod tarafında **değil**.

## §15 Yokluk / Yasak Kuralları (olmayan uydurulmaz)

| # | Yokluk | Doğru davranış | Kanıt |
|---|--------|----------------|-------|
| 1 | `.ai/wiki/` — kök `AGENTS.md` §7'de anılır, diskte **0 dosya** | `VERIFICATION REQUIRED` — sayfa uydurma | `.ai/RAG.md:96` |
| 2 | `.ai/.rules/error-recovery.md` — diskte YOK | yok sayılır, linklenmez | `.ai/RAG.md:96` |
| 3 | Embedding modeli seçimi — yok | `UNKNOWN` / `PLANNED`, aday yazılmaz | `.ai/RAG.md:118` |
| 4 | Vektör store seçimi — yok | `PLANNED` (adaylar değerlendirilmedi) | `.ai/RAG.md:119` |
| 5 | `.env.figma` / credential anahtarları | hiçbir satıra girmez — REDACTED | `.ai/RAG.md:137` |
| 6 | Wiki-link biçimi | `[[...]]` — markdown path linki değil | `.ai/RAG.md:138` |

## §16 Doğrulama Kapıları (.ai/RAG.md §6 — 10 kontrol)

| # | Kontrol | Kriter | Yöntem |
|---|---------|--------|--------|
| 1 | Frontmatter | 7 alan + Control Plane v2 bloğu + `docType: rag` | gözden geçirme |
| 2 | §3 hedefleri | Her satırdaki dosya diskte | Test-Path / glob |
| 3 | Durum sütunu | §3 ve §4'te her satır dolu | gözden geçirme |
| 4 | ADR tutarlılığı | §4 `PLANNED` satırları ADR-030 ile çelişmiyor | ADR-030 okuma |
| 5 | Vault bütçe/doğrulama | 8 check yeşil | `node .ai/scripts/validate.mjs --check` |
| 6 | Placeholder | Dosyada `{{` yok | grep |
| 7 | Wiki-link | Hedefler diskte | `.ai/scripts/wiki-link-check.ps1` (kökten) |
| 8 | Boot listesi | §16 13 kanonik değişmedi | `.ai/CLAUDE.md` §16 |
| 9 | Çift katman | Kök pointer (≤80 satır) · SSOT ayrımı | gözden geçirme |
| 10 | Sayı tazeliği | §3/§4 sayıları ölçüm tarihiyle | yeniden sayım |

Satır kaynakları: `Dosya: .ai/RAG.md · Satır: 144-155`. Betikler yalnız **repo kökünden** çalıştırılır
(`Dosya: .ai/RAG.md · Satır: 152`).

## §17 PHP Kod Kanıtı — RAG üç adımı (repo arşivi)

`shared/src/AI/KnowledgeBase.php` — RAG akışı birebir kodda, **ama iskelet** (generate placeholder):

```php
// Dosya: shared/src/AI/KnowledgeBase.php · Satır: 72-91
public function ragQuery(string $query, array $context = []): array
{
    // 1. Retrieval — ilgili belgeleri bul
    $retrieved = $this->search($query, 5, $context['filters'] ?? []);

    // 2. Augmentation — prompt'a bağlam ekle
    $augmentedContext = $this->augmentContext($query, $retrieved, $context);

    // 3. Generation — LLM ile yanıt üret (şimdilik placeholder)
    $answer = $this->generateAnswer($query, $augmentedContext);
    ...
}
```

Generation placeholder'dır: `"Gerçek implementasyonda LLM API çağrılacak"` —
`Dosya: shared/src/AI/KnowledgeBase.php · Satır: 213`. Çıktı şeması `answer/sources/confidence`
üçlüsüdür (`Satır: 83-90`).

## §18 PHP Kod Kanıtı — skorlama (bugünün retrieval'ı)

```php
// Dosya: shared/src/AI/KnowledgeBase.php · Satır: 163-191
private function calculateRelevanceScore(string $query, array $item): float
{
    $titleLower = strtolower($item['title']);
    $contentLower = strtolower($item['content']);
    $score = 0.0;
    // Title match (yüksek ağırlık)
    if (str_contains($titleLower, $query)) { $score += 0.6; }
    // Content match (düşük ağırlık)
    if (str_contains($contentLower, $query)) { $score += 0.3; }
    ...
}
```

Ağırlıklar: title 0.6 · content 0.3 · kısmi keyword 0.1 (`Satır: 171-191`). Sıralama `usort` +
`array_slice` (`Satır: 64-66`); güven skoru ort. skor × 1.2, tavan 1.0 (`Satır: 223-231`).
**Vector cosine similarity YOKTUR** — yorum satırı olarak durur (`Satır: 48`).

## §19 PHP Kod Kanıtı — augment + doğrulama sınırları

```php
// Dosya: shared/src/AI/KnowledgeBase.php · Satır: 199-206
private function augmentContext(string $query, array $retrieved, array $context): array
{
    $augmented = $context;
    $augmented['retrieved_documents'] = $retrieved;
    $augmented['query_expansion'] = $query;
    return $augmented;
}
```

```php
// Dosya: shared/src/AI/PromptEngine.php · Satır: 176-179
if (preg_match('/password|secret|api[_-]?key|token/i', $prompt)) {
    $risks[] = "Potential sensitive data in prompt";
}
```

`validatePrompt()` ayrıca injection deseni (`:161-167`) ve token bütçesi (`:169-174`) taraması yapar —
**bağlı değildir** (`Dosya: ADR-030 · Satır: 304`: "validatePrompt kodda, bağlı değil → ❌ PLANNED").
Token sayımı tahminidir: `ceil(karakter / 3.5)` (`PromptEngine.php:90-96`).

## §20 Repo'da Olmayan Yüzey (açık beyan)

- **Vault indeksini okuyan/üreten RAG kodu repo'da YOK** — `.ai/RAG.md` §3 tablosunu üreten kod,
  chunk/embedding indeksleyici veya vektör sorgulayıcı **0 dosyadır**; kanıt: `embedding|vector_db`
  (`*.php`) taraması 0 eşleşme (`Dosya: ADR-030 · Satır: 36`) ve "RAG/embedding/vektör (kod 0)"
  (`Dosya: ADR-030 · Satır: 69`).
- Bugünkü vault retrieval'ı **insan + agent okumasıdır** (`.ai/keys.md` + §3 tablosu) —
  `Dosya: .ai/RAG.md · Satır: 116`.
- Otomatik retrieval için gerekli vektör tablosu **yoktur** (`Dosya: ADR-030 · Satır: 56`).
- **Repo'da bu alan (vault-RAG pipeline) kodu YOK — kaynak: upstream docs** (`ADR-030:95` literatür
  kanonu; uygulama taahhüdü yok: `ADR-030:207`).

## §21 İllüstratif Örnek — Python (cosine similarity)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
# embedding benzerliği — upstream docs (numpy) referansı, repo'da karşılığı YOK
import numpy as np

def cosine(a: list[float], b: list[float]) -> float:
    va, vb = np.asarray(a, dtype=np.float64), np.asarray(b, dtype=np.float64)
    denom = np.linalg.norm(va) * np.linalg.norm(vb)
    return 0.0 if denom == 0 else float(va @ vb / denom)

query_vec = embed("csrf token doğrulama")     # aynı modelle üretilir
docs = [(d, cosine(query_vec, v)) for d, v in index]
top_k = sorted(docs, key=lambda t: -t[1])[:5]
```

Kural: embedding **aynı modelle** üretilip sorgulanır (`Dosya: ADR-030 · Satır: 92` — TowardsAI/Pinecone
çapraz kaynağı).

## §22 İllüstratif Örnek — JS/TS (BM25 retrieval)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```ts
// BM25 — upstream docs (bir arama motoru kütüphanesi) referansı; repo'da yok
type Doc = { id: string; terms: string[] };

export function bm25(query: string, docs: Doc[], k1 = 1.2, b = 0.75): { id: string; s: number }[] {
  const qs = query.toLowerCase().split(/\s+/).filter(t => t.length > 2);
  const avgLen = docs.reduce((n, d) => n + d.terms.length, 0) / docs.length;
  return docs.map(d => {
    const tf = (t: string) => d.terms.filter(x => x === t).length;
    const s = qs.reduce((acc, t) => {
      const f = tf(t), n = docs.filter(x => x.terms.includes(t)).length + 1;
      const idf = Math.log(1 + (docs.length - n + 0.5) / (n + 0.5));
      return acc + idf * (f * (k1 + 1)) / (f + k1 * (1 - b + b * d.terms.length / avgLen));
    }, 0);
    return { id: d.id, s };
  }).sort((x, y) => y.s - x.s);
}
```

Pipeline'daki yeri: exact keyword → **BM25** → vektör (`Dosya: .ai/RAG.md · Satır: 120`).

## §23 İllüstratif Örnek — Go (chunking)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
// § bazlı chunk — tasarım: ~500 token hedefi (Dosya: .ai/RAG.md · Satır: 117)
package chunk

type Chunk struct{ Path, Heading, Text string }

func SplitBySection(path, body string, maxTokens int) []Chunk {
    var out []Chunk
    cur := Chunk{Path: path, Heading: "(giriş)"}
    for _, line := range strings.Split(body, "\n") {
        if strings.HasPrefix(line, "## ") {
            if cur.Text != "" { out = append(out, cur) }
            cur = Chunk{Path: path, Heading: strings.TrimPrefix(line, "## ")}
            continue
        }
        cur.Text += line + "\n"
        if estTokens(cur.Text) >= maxTokens { // başlık + dosya yolu metadata korunur
            out = append(out, cur); cur.Text = ""
        }
    }
    if cur.Text != "" { out = append(out, cur) }
    return out
}
```

## §24 İllüstratif Örnek — C++ (vektör benzerliği)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```cpp
// dense embedding benzerliği — upstream docs referansı (repo'da karşılığı YOK)
#include <cmath>
#include <vector>

float cosine(const std::vector<float>& a, const std::vector<float>& b) {
    float dot = 0.f, na = 0.f, nb = 0.f;
    for (size_t i = 0; i < a.size(); ++i) {          // gerçek üretimde SIMD/SIMD-within-a-register
        dot += a[i] * b[i];
        na  += a[i] * a[i];
        nb  += b[i] * b[i];
    }
    const float denom = std::sqrt(na) * std::sqrt(nb);
    return denom == 0.f ? 0.f : dot / denom;
}
// vektör store seçimi YAPILMADI (Dosya: .ai/RAG.md · Satır: 119) → bu kodu çağranacak taraf yoktur.
```

## §25 İllüstratif Örnek — C# (top-K vektör arama)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```csharp
// brute-force top-K — upstream docs referansı; vektör store kararı bekliyor
public sealed record Hit(string Id, double Score);

public static IReadOnlyList<Hit> TopK(float[] query, IReadOnlyDictionary<string, float[]> index, int k)
{
    var hits = new List<Hit>(index.Count);
    foreach (var (id, vec) in index)
        hits.Add(new Hit(id, Cosine(query, vec)));   // sıralama: skor > top-K

    hits.Sort((x, y) => y.Score.CompareTo(x.Score));
    return hits.GetRange(0, Math.Min(k, hits.Count));
}
```

Bu katmanın CoreMusic'teki durumu: vektör store **seçilmedi** (`Dosya: .ai/RAG.md · Satır: 119`) —
adaylar (SQLite-vec / MySQL 9 / harici servis) değerlendirilmedi.

## §26 İllüstratif Örnek — Rust (tokenizer + lexical skor)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```rust
// lexical retrieval iskeleti — upstream docs referansı, repo kanıtı DEĞİL
pub fn lexical_score(query: &str, title: &str, body: &str) -> f32 {
    let q: Vec<&str> = query.split_whitespace().filter(|t| t.len() > 2).collect();
    if q.is_empty() { return 0.0; }
    let (tl, bl) = (title.to_lowercase(), body.to_lowercase());
    let mut s = 0.0_f32;
    if q.iter().all(|t| tl.contains(t)) { s += 0.6 }        // başlık: yüksek ağırlık
    if q.iter().any(|t| bl.contains(t)) { s += 0.3 }        // gövde: düşük ağırlık
    let hits = q.iter().filter(|t| tl.contains(**t) || bl.contains(**t)).count();
    s + (hits as f32 / q.len() as f32) * 0.1                // kısmi eşleşme: 0.1
}
```

Ağırlık şeması (0.6/0.3/0.1) **repo'daki PHP ile aynı mantıktır** —
`Dosya: shared/src/AI/KnowledgeBase.php · Satır: 171-191`.

## §27 Açık Sorular

| # | Soru | Öncelik | Durum / Kanıt |
|---|------|---------|----------------|
| 1 | `rag/index.md` açık sorusu: ".ai/RAG.md diskte var mı ve hâlâ PLANNED mı?" | HIGH | ✅ **KAPANDI** — `.ai/RAG.md` diskte 177 satır, `status: active`, §4 PLANNED (`rag/index.md:14` · `.ai/RAG.md:8`) |
| 2 | Embedding modeli hangisi? | HIGH | `UNKNOWN` — karar yok (`Dosya: .ai/RAG.md · Satır: 118`) |
| 3 | Vektör store hangisi? | HIGH | `UNKNOWN` — seçim yapılmadı (`Dosya: .ai/RAG.md · Satır: 119`) |
| 4 | Chunk token eşiği (~500) gerçek corpus'ta tutuyor mu? | MEDIUM | `⚠️ VERIFICATION REQUIRED` — ölçüm yok (`Dosya: .ai/RAG.md · Satır: 117`) |
| 5 | `rag/index.md` children (kademe-3) ne zaman açılır? | LOW | ADR-097 K4 — sonraki faz (`rag/index.md:6`) |
| 6 | `KnowledgeBase` PLANNED ↔ DEAD CODE etiketi hangisi bağlayıcı? | HIGH | `⚠️ VERIFICATION REQUIRED` (`Dosya: ADR-030 · Satır: 309`) |

## §28 Kaynaklar

| # | Kaynak | Yol | Kullanım |
|---|--------|-----|----------|
| 1 | Retrieval indeksi + pipeline (SSOT) | `.ai/RAG.md` (177 satır, v1.1.0) | §3-§6, §14-§16 |
| 2 | AI strateji ADR'si | `.ai/.decisions/accepted/ADR-030-ai-strategy-core.md` (389 satır, accepted) | §8-§10, §20 |
| 3 | Alt-dümğüm künyesi | `.ai/architecture/K015-AI/rag/index.md` (48 satır) | §11, §27 |
| 4 | RAG kodu (iskelet) | `shared/src/AI/KnowledgeBase.php` (232 satır) | §17-§19 |
| 5 | Prompt doğrulama | `shared/src/AI/PromptEngine.php` | §19 |
| 6 | Anayasa iskeleti (şablon) | `.ai/architecture/K015-AI/agents/anayasa.md` | yapı: H1 + § + tablo |
| 7 | Frontmatter örneği | `.ai/architecture/K015-AI/agents/ornekler.md:1-15` | FM 10 alan |
| 8 | Doğrulama betiği | `.ai/scripts/validate.mjs` (8 check + bütçe) | §16/5 |

## §29 Hızlı Referans (tek ekran)

| # | Soru | Cevap |
|---|------|-------|
| 1 | Soru geldi nereden başlarım? | `.ai/RAG.md` §3.1 tablosu (keyword sütunu) |
| 2 | Sistem otomatik mi? | Hayır — §4 pipeline **PLANNED** (kod 0) |
| 3 | Durum etiketi nasıl seçilir? | dosya diskte mi → IMPLEMENTED; yalnız ADR var mı → PLANNED |
| 4 | Vault'a dosya ekledim, ne yapacağım? | §3 satırını yeniden ölç (`vault-sync-post`) |
| 5 | Boot'a ekler miyim? | Hayır — 13 kanonik sabit, on-demand okunur |
| 6 | Çelişki kim kazanır? | `.ai/RAG.md` (SSOT — disk kazanır) |

## §30 Ölçüm / Gate Notu

| # | Eşik | Araç | Not |
|---|------|------|-----|
| 1 | ≥500 tam satır | `[IO.File]::ReadAllLines(..., UTF8).Count` | 499 FAIL |
| 2 | FM 10 alan | gözden geçirme | sıra sabit, `title`/`parent` anahtarı YOK |
| 3 | Authority son satır | gözden geçirme | birebir son satır |
| 4 | Kod bloğu | PHP (repo kanıtı) + 6 dil illüstratif | illüstratif bloklarda uyarı etiketi zorunlu |
| 5 | Ölçüm tarihi | 2026-10-17 üretim | sayı iddiaları okuma künyesi taşır |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
