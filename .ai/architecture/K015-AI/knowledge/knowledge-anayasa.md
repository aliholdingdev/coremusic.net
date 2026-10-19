---
node_id: knowledge-anayasa
node_type: module
status: active
parent_id: knowledge
children: []
depends_on: [knowledge-index]
evidence:
  - ".ai/RAG.md (177 satır, §3 konu→dosya indeksi, okundu 2026-10-17)"
  - ".ai/keys.md + .ai/index.md + .ai/.decisions/index.md (okundu 2026-10-17)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı"
open_questions: []
group: mimari
---

# knowledge-anayasa - Ajan Bilgi Kuralları

> **Bu dosya ne değildir:** `.ai/keys.md` · `.ai/index.md` · `.ai/RAG.md` §3 tablolarının **kopyasını**
> ve vault bilgi bankası pipeline'ının **kodunu** taşımaz; burada **bilgi yönetimi anayasası** vardır —
> bilgi nereye yazılır, hangi dosya hangi konunun sahibidir, sayılar nasıl ölçülür, çeliştiğinde kim kazanır.

## §1 Rol (ELI15)

Knowledge alt-domaini, vault içindeki **bilgi bankası kurallarının** sahibidir: konu→dosya indeksi (`.ai/RAG.md` §3),
keyword haritası (`.ai/keys.md`), master katalog (`.ai/index.md`), terim sözlüğü (`.ai/glossary.md`) ve ADR karar
bütünlüğü (`.ai/.decisions/`) arasındaki **sorumluluk sınırını** tanımlar. Retrieval'ın kendisi RAG alt-dümüğümünün
işidir (`rag-anayasa`); bu dosya bilginin **kalitesini, sahipliğini ve yazımını** yönetir.

| Soru | Cevap | Kanıt |
|------|-------|-------|
| Bilgi bankası sahibi kim? | MO (vault-updater) + Vault Steward (denetim) | `Dosya: .ai/RAG.md · Satır: 22` · `Satır: 63` |
| Konu→dosya indeksi nerede? | `.ai/RAG.md` §3.1 — 20 satır | `Dosya: .ai/RAG.md · Satır: 71` |
| Otomatik retrieval var mı? | Hayır — §4 pipeline PLANNED (ADR-030: kod 0) | `Dosya: .ai/RAG.md · Satır: 111` |
| Kim ilk referans dosyasını okur? | "Ajanların ilk basvurduğu referans" = `keys.md` | `Dosya: .ai/keys.md · Satır: 36` |

> **eli10:** Bu dosya, "bu bilgi nereye girer ve hangi dosya okunur" cevabının kural kitabıdır.
> **eli15:** Yönlendirme (RAG) ile bilgi kalitesi (knowledge) ayrıştırıldı çünkü indeksi güncelleyen kural ile
> içeriğin doğruluğunu denetleyen kural farklı disiplinlerdir; tek dosyada birleşince "kod var" sanrısı doğar.

## §2 Kapsam — knowledge ≠ memory ≠ context

| Kavram | Ne taşır | Sahip dosya | Kanıt |
|--------|----------|-------------|-------|
| **Knowledge** (bu anayasa) | Konu→dosya, keyword, katalog, glossary, ADR — **kalıcı, olgusal** | `.ai/RAG.md` · `keys.md` · `index.md` · `glossary.md` | `Dosya: .ai/index.md · Satır: 49-74` |
| **Memory** | Oturum hafızası, cache, session state — **geçici/oturumsal** | `.ai/MEMORY.md` | `Dosya: .ai/index.md · Satır: 88` |
| **Context** | Dizin envanteri, boot ilişkisi, disk çelişkileri — **yapısal** | `.ai/CONTEXT.md` | `Dosya: .ai/index.md · Satır: 98` |
| **Log** | Audit trail — append-only olay kaydı | `.ai/log.md` | `Dosya: .ai/index.md · Satır: 89` |

Sınır kuralı: bir bilgi **bir kez** ve tek sınıfta yaşar; terim tanımı knowledge (glossary), oturum notu memory,
değişiklik kanıtı log'dur. Aynı olgunun iki sınıfta tekrarlanması kopyala-yapıştır klonu doğurur → SSOT çelişkisi.

## §3 SSOT Hiyerarşisi

| # | Sıra | Dosya | Ne kazanır | Kanıt |
|---|------|-------|------------|-------|
| 1 | CLAUDE | `.ai/CLAUDE.md` | Anayasa, guardrail, boot §16 | `Dosya: .ai/index.md · Satır: 82` |
| 2 | AGENTS | `.ai/AGENTS.md` | Agent registry, routing, handover | `Dosya: .ai/index.md · Satır: 83` |
| 3 | WORKFLOW | `.ai/WORKFLOW.md` | Süreç, faz, kapı | `Dosya: .ai/index.md · Satır: 84` |
| 4 | brain | `.ai/brain.md` | Mimari karar özeti | `Dosya: .ai/index.md · Satır: 86` |
| 5 | index | `.ai/index.md` | Master katalog | `Dosya: .ai/index.md · Satır: 85` |
| 6 | templates | `.ai/.templates/index` | Şablon envanteri (Guardrail #16) | `Dosya: .ai/index.md · Satır: 71` |

Çatışma kuralı (bağlayıcı): **çelişkide üst sıradaki ve diskteki dosya kazanır** — "authority: SSOT (klasör kökü) —
çelişkide disk kazanır" (`Dosya: .ai/RAG.md · Satır: 9`). Alt sıradaki dosya üsttekini **ezmez**, yalnız detaylandırır
(alt registry demote örneği: `.ai/.agents/AGENTS.md` → profil indeksi, `Dosya: .ai/.decisions/index.md` mantığının aynısı).

## §4 Konu→Dosya İndeksi Kuralları (RAG §3)

| # | Kural | İhlal Sonucu | Kanıt |
|---|-------|-------------|-------|
| 1 | Satır yalnız diskte var olan dosyaya gider (Test-Path / glob) | Hayalet satır silinir | `Dosya: .ai/RAG.md · Satır: 102` |
| 2 | Birden çok hedef varsa en dar (domain-specific) dosya önce yazılır | Yanlış öncelik | `Dosya: .ai/RAG.md · Satır: 103` |
| 3 | Durum sütunu: `IMPLEMENTED` / `PLANNED` / `UNKNOWN` — her satırda dolu | Durumsuz satır geçersiz | `Dosya: .ai/RAG.md · Satır: 104` |
| 4 | Sayılar (96 md, 65 şablon, 21 sql …) yalnız ölçümle yazılır | Zero-Hallucination ihlali | `Dosya: .ai/RAG.md · Satır: 105` |
| 5 | Vault dosya ekleme/taşımadan sonra §3 yeniden ölçülür | İndeks bayatlar | `Dosya: .ai/RAG.md · Satır: 135` |

Okuma sırası: (i) sorunun anahtar kelimeleri §3.1 `Keyword'ler` sütununda aranır; (ii) eşleşen satırın hedefi ilk
okumadır; (iii) `PLANNED` ise o konuda **kod yoktur**, yalnız ADR/plan okunur. §3.1 başlığı "20 satır" der ve tabloda
satır 9 **yoktur** (1-8, 10-21) — boşluk bilinçlidir, yeniden numaralandırma yasaktır (`Dosya: .ai/RAG.md · Satır: 71`).

## §5 §3.1 Örnek Okuma (seçilmiş 8 satır — SSOT kopyası DEĞİLDİR)

| # | Soru sınıfı | Hedef dosya(lar) | Keyword | Durum | Kanıt |
|---|-------------|------------------|---------|-------|-------|
| 6 | Master katalog, dosya/ADR listesi | `.ai/index.md` | katalog, indeks, dosya listesi | IMPLEMENTED | `Dosya: .ai/RAG.md · Satır: 80` |
| 7 | Keyword routing eşlemesi | `.ai/keys.md` | keyword, arama, eşleme | IMPLEMENTED | `Dosya: .ai/RAG.md · Satır: 81` |
| 8 | Mimari kararlar (ADR) | `.ai/.decisions/index.md` · `.ai/brain.md` | adr, karar, frozen | IMPLEMENTED | `Dosya: .ai/RAG.md · Satır: 82` |
| 15 | Terimler | `.ai/glossary.md` | terim, sözlük, glossary | IMPLEMENTED | `Dosya: .ai/RAG.md · Satır: 88` |
| 16 | Session hafızası, audit trail | `.ai/MEMORY.md` · `.ai/log.md` | session, log, audit | IMPLEMENTED | `Dosya: .ai/RAG.md · Satır: 89` |
| 18 | AI stratejisi, RAG/embedding kararı | `.ai/.decisions/accepted/ADR-030-ai-strategy-core.md` | rag, embedding, llm | **PLANNED** | `Dosya: .ai/RAG.md · Satır: 91` |

Bu tablo **örnek okuma**dır; güncel sürüm `.ai/RAG.md` §3.1'dedir ve çelişirse o kazanır. Knowledge-altı satırlar
(6, 7, 8, 15) bu anayasanın nesnesidir; 18 numaralı satır RAG alt-dümüğümüne devredilmiştir (`rag-anayasa` §6-§10).

## §6 Keyword Haritası (keys.md) Rolü

| # | Bölüm | Kapsam | Kanıt |
|---|-------|--------|-------|
| 1 | §2 Core Vault Keywords | 15 satır — dosya→keyword eşlemesi (vision…architecture/index) | `Dosya: .ai/keys.md · Satır: 44-60` |
| 2 | §4 Security Keywords | CSRF/session/CSP/rate-limit/OAuth → ADR hedefleri | `Dosya: .ai/keys.md · Satır: 64-86` |
| 3 | §5 Database Keywords | BCNF/PDO/migration + 18 `coremusic_*` DB adı → `.sql/mysql/` | `Dosya: .ai/keys.md · Satır: 90-118` |
| 4 | Durum | `status: active` · `ssot: false` (katalog `index.md` kazanır) | `Dosya: .ai/keys.md · Satır: 8 · Satır: 18` |

Kural: keys.md **yönlendiricidir, sahip değildir** — bir konunun doğruluk kaynağı o konunun hedef dosyasıdır; keyword
satırı yalnız oraya **götürür**. `.ai/keys.md` güncellenince `.ai/RAG.md` §3 ile **aynı oturumda** hizalanır (ikisi
aynı soru sınıfını farklı hedefe götürürse `RAG.md` §3.1 kazanır — en dar hedef kuralı, `Satır: 103`).

## §7 Master Katalog (index.md)

| # | Alan | Değer (ölçüm 2026-10-12) | Kanıt |
|---|------|--------------------------|-------|
| 1 | `total_root_md` | 21 (`.ai/` kök `.md`) | `Dosya: .ai/index.md · Satır: 11` |
| 2 | `total_recursive_md` | 435 (72 dizin içinde toplam) | `Dosya: .ai/index.md · Satır: 14-15` |
| 3 | `total_adr` | 91 (FM sayımı) | `Dosya: .ai/index.md · Satır: 16-19` |
| 4 | Quick Reference | 24 satırlık "İhtiyaç → İlk Adım" tablosu | `Dosya: .ai/index.md · Satır: 49-74` |
| 5 | §3 SSOT Core | 21 kök dosya, sınıf sütunu (CRITICAL/LOG/ON-DEMAND) | `Dosya: .ai/index.md · Satır: 78-100` |

index.md **hub'dır, içerik ambarı değildir**: künye + navigasyon taşır, detay alt sayfalardadır
("Bu dosya hub'dır: içerik alt sayfalarda" — `Dosya: .ai/index.md · Satır: 41`). Katalog satırı eklenmeden üretilen
hiçbir vault `.md` dosyası "bulunabilir" sayılmaz.

## §8 Glossary Kuralları

| # | Kural | Uygulama | Kanıt |
|---|-------|----------|-------|
| 1 | Terim tanımı tek yerde: glossary SSOT | "Tek Doğruluk Kaynağıdır (SSOT)" | `Dosya: .ai/glossary.md · Satır: 37` |
| 2 | Kullanım 3 adım: tablo ara → harita → blok oku | §2/§3 → §4/§5 → §4.1.x | `Dosya: .ai/glossary.md · Satır: 39-42` |
| 3 | Katkı 3 adım: §3 tablosu → kanıt araması → kanıt varsa blok | kanıtsız terim `DOĞRULAMA GEREKLİ` | `Dosya: .ai/glossary.md · Satır: 44` |
| 4 | Ölçüm (2026-10-06): §2 32 · §2.1 11 · §3 51 → **toplam 94** | sayılar ölçümle yazılır | `Dosya: .ai/glossary.md · Satır: 57` |
| 5 | Doğrulanamayan kullanım `DOĞRULAMA GEREKLİ` (Truth Mode) | uydurma kullanım alanı yazılmaz | `Dosya: .ai/glossary.md · Satır: 51` |

Ek kayıt: `.ai/AGENTS.md` §3 stub'u 12 terimin "glossary.md'de yer almadığını" `⚠️ VERIFICATION REQUIRED` ile
işaretler — glossary boşluğu **bilinen açık**tır ve "tamamlandı" diye kullanılmaz.

## §9 ADR/Decisions Bütünlüğü (frozen dokunulmaz)

| # | Durum | Sayı | Kural | Kanıt |
|---|-------|------|-------|-------|
| 1 | Frozen | 36 (ADR-001 → ADR-036) | Değiştirilemez — okunur, referans edilir, **dokunulmaz** | `Dosya: .ai/.decisions/index.md · Satır: 38 · Satır: 50` |
| 2 | Active | 45 (`accepted/` 44 + kökte ADR-091) | ADR lifecycle ile ilerler | `Dosya: .ai/.decisions/index.md · Satır: 39` |
| 3 | Rejected | 10 (`rejected/`) | Karar reddi de kayıttır — silinmez | `Dosya: .ai/.decisions/index.md · Satır: 40` |
| 4 | Draft | 2 (ADR-097 · ADR-102) | Onay bekliyor — bağlayıcı değildir | `Dosya: .ai/.decisions/index.md · Satır: 41` |
| 5 | Silme örneği | ADR-061 kaldırıldı → yedek `C:\temp\opencode\vault-backup-2026-10-12\` | ADR silinmez, taşınır+yedeklenir | `Dosya: .ai/.decisions/index.md · Satır: 44` |

Dikkat (ölçüm çelişkisi — dürüst kayıt): decisions §2 disk ölçümü **toplam 93** der (80+10+2 draft+1 kök,
`Satır: 33`), kendi tablosu **92** der (`Satır: 42`), `index.md` FM'i **91** (80/10/0, `Satır: 16-19`) der. Üç sayı
farklıdır → yeniden sayım gerekir (açık soru #3); hiçbiri bu anayasada "doğru" ilan edilmez.

## §10 Bilgi→Vault Yazım Kuralları (yeni bilgi nereye girer)

| # | Yeni bilgi türü | Hedef | Sahip | Kanıt |
|---|-----------------|-------|-------|-------|
| 1 | Yeni terim | `.ai/glossary.md` §3 tablosu → kanıt araması | MO | `Dosya: .ai/glossary.md · Satır: 44` |
| 2 | Yeni mimari karar | `.ai/.decisions/` (draft → accepted lifecycle) | Vault Steward | `Dosya: .ai/.decisions/index.md · Satır: 29` |
| 3 | Yeni konu sınıfı | `.ai/RAG.md` §3.1 satırı (glob ile ölçülür) | MO (vault-updater) | `Dosya: .ai/RAG.md · Satır: 121` |
| 4 | Yeni keyword | `.ai/keys.md` ilgili § tablosu | MO | `Dosya: .ai/keys.md · Satır: 36` |
| 5 | Yeni dosya/katalog girişi | `.ai/index.md` (künye + Quick Reference) | MO | `Dosya: .ai/index.md · Satır: 41` |
| 6 | Revizyon kaydı | `.ai/log.md` — **append-only**, geçmiş satıra dokunulmaz | tüm ajanlar | `Dosya: .ai/AGENTS.md · Satır: §25.3 #3` |

Yazım sırası (bağlayıcı): **şablon oku (Guardrail #16) → yaz → katalog/keyword/RAG satırını güncelle → ölç → log**.
Tek başına üretilen dosya (katalog satırısız) görünmez; katalog satırısız dosya hayalet sayılır.

## §11 Retrieval Pipeline PLANNED Durumu (ADR-030)

| # | Bileşen | Durum | Kanıt |
|---|---------|-------|-------|
| 1 | Manuel keyword indeksi (`keys.md` + RAG §3) | **IMPLEMENTED** | `Dosya: .ai/RAG.md · Satır: 116` |
| 2 | Chunk stratejisi (§ bazlı, ~500 token) | PLANNED | `Dosya: .ai/RAG.md · Satır: 117` |
| 3 | Embedding modeli | PLANNED — seçim YAPILMADI | `Dosya: .ai/RAG.md · Satır: 118` |
| 4 | Vektör store | PLANNED — adaylar değerlendirilmedi | `Dosya: .ai/RAG.md · Satır: 119` |
| 5 | Arama sırası: exact → BM25 → vektör | PLANNED (ilk ikisi elle) | `Dosya: .ai/RAG.md · Satır: 120` |
| 6 | Güncelleme tetikleyicisi `vault-sync-post` | PLANNED | `Dosya: .ai/RAG.md · Satır: 121` |

Bağlayıcı ifade: *"RAG / embedding / vektör — ❌ PLANNED (kod 0, tablo 0)"* (`Dosya: .ai/RAG.md · Satır: 111`).
Bu anayasanın hiçbir bölümünde "bilgi bankası/otomatik retrieval çalışıyor" ifadesi **geçemez**; geçerse ihlaldir.

## §12 Bilgi Kalitesi — Zero-Hallucination

| # | Kural | Doğru davranış | Kanıt |
|---|-------|----------------|-------|
| 1 | Repo/URL/API/dosya/sayı asla uydurulmaz | doğrulanamayan = `⚠️ VERIFICATION REQUIRED` | `.ai/AGENTS.md` (Zero-Hallucination, kök boot) |
| 2 | Bilinmeyen değer = `UNKNOWN` | tahmin yazılmaz | `.ai/AGENTS.md` (Zero-Hallucination) |
| 3 | Her iddia disk kanıtı taşır | `Dosya: … · Satır: …` künyesi | `Dosya: .ai/RAG.md · Satır: 105` |
| 4 | Ölçülmemiş sayı iddiası yazılmaz | sayım tarihiyle yazılır | `Dosya: .ai/RAG.md · Satır: 64` |
| 5 | Eksik-veri işareti "kaynak alındı" yapılmaz | `⚠️` = eksik; tamamlanmış sayılmaz | `.ai/AGENTS.md` §24.3 veri bütünlüğü |

Kalite kapısı: sayı tazeliği yeniden sayım ile denetlenir (`Dosya: .ai/RAG.md · Satır: 155`); dosya yoksa link
**düzeltmez**, yokluk kaydedilir (`.ai/wiki/` = 0 dosya örneği, `Dosya: .ai/RAG.md · Satır: 96`).

## §13 Bilgi Öncelik Sırası / Çatışma Çözümü

| # | Çatışma | Kazanan | Gerekçe | Kanıt |
|---|---------|---------|---------|-------|
| 1 | Adayasa ↔ vault dosyası | vault dosyası (disk) | "çelişkide disk kazanır" | `Dosya: .ai/RAG.md · Satır: 9` |
| 2 | index.md ↔ decisions/index.md (ADR sayısı) | **yeniden sayım** | üç farklı sayı — hiçbiri ilan edilmez | `Dosya: .ai/.decisions/index.md · Satır: 33 · 42` |
| 3 | keys.md ↔ RAG §3.1 (hedef farkı) | RAG §3.1 | en dar hedef kuralı | `Dosya: .ai/RAG.md · Satır: 103` |
| 4 | Model hafızası ↔ vault | vault | "Model hafızası ve web, vault'un altındadır" | `.ai/AGENTS.md` §3 |
| 5 | ADR metni ↔ kod davranışı | kod (gerçeklik) + ADR (niyet) ayrı raporlanır | IMPLEMENTED kanıtsız yazılmaz | `Dosya: .ai/RAG.md · Satır: 104` |

Öncelik zinciri (özet): **disk kanıtı > ölçüm > ADR > katalog > keyword > model hafızası > web**. Web kaynakları
yalnız "upstream/docs" etiketiyle ve URL ile taşınır; vault kuralı **olamaz**, yalnız destekleyicidir.

## §14 Güvenlik — Secret Bilgi Bankasına Girmez

| # | Yasak | Karşılığı | Kanıt |
|---|-------|-----------|-------|
| 1 | `.env.figma` / credential anahtarları hiçbir indeks/keyword satırına girmez | REDACTED | `Dosya: .ai/RAG.md · Satır: 137` |
| 2 | `FIGMA_TOKEN` / `FIGMA_FILE_KEY` yalnız `.ai/.env.figma`'dan okunur | `.md`/`.json`/`.log`'a yazılmaz | `.ai/AGENTS.md` §13.9 #3 |
| 3 | Secret log'a sızarsa | `[REDACTED]` maskeleme | `.ai/AGENTS.md` §17 #3 |
| 4 | Prompt içine hassas veri | risk taraması (kodda mevcut, bağlı değil) | `Dosya: shared/src/AI/PromptEngine.php · Satır: 176-179` |
| 5 | Sızıntı şüphesi | DUR + Security Engineer eskalasyonu (CRITICAL) | `.ai/AGENTS.md` §10.1 |

`.ai/` vault'taki bilgi bankası **salt-okunur kanıt ambarıdır**; credential bilgisi hiçbir bilgi bankası katmanına
( indeks, keyword, glossary, örnek blok) **kopyalanmaz** — erişim her zaman `.env` üzerinden yapılır.

## §15 Doğrulama Kapıları

| # | Kapı | Kriter | Yöntem |
|---|------|--------|--------|
| 1 | Frontmatter | 10 alan, `title`/`parent` anahtarı YOK | gözden geçirme |
| 2 | Hedef dosyalar | § tablolarındaki her yol diskte | Test-Path / glob |
| 3 | Wiki-link | Hedefler diskte | `.ai/scripts/wiki-link-check.ps1` (kökten) |
| 4 | Vault bütçe | 8 check yeşil | `node .ai/scripts/validate.mjs --check` |
| 5 | Satır sayısı | ≥500 tam satır | `[IO.File]::ReadAllLines($hedef, UTF8).Count` |
| 6 | Kod blokları | PHP künyeli gerçek alıntı + illüstratif bloklarda uyarı etiketi | gözden geçirme |
| 7 | Authority | Son satır birebir | gözden geçirme |

Betikler yalnız **repo kökünden** çalıştırılır — `.ai/` içinden çalıştırılan gate sahte "PNG yok" üretir
(`Dosya: .ai/RAG.md · Satır: 152` · `.ai/AGENTS.md` §13.6).

## §16 PHP Kanıtı — search + skorlama (repo arşivi)

`shared/src/AI/KnowledgeBase.php` — bilgi bankasının bugünkü retrieval yüzeyi (keyword eşleşmesi):

```php
// Dosya: shared/src/AI/KnowledgeBase.php · Satır: 42-49
foreach ($this->knowledge as $id => $item) {
    // Kategori filtresi
    if (!empty($filters['category']) && $item['category'] !== $filters['category']) {
        continue;
    }
    // Basit keyword matching (gerçek implementasyonda vector cosine similarity)
    $score = $this->calculateRelevanceScore($queryLower, $item);
```

Ağırlık şeması: title **0.6** (`Satır: 171-172`) · content **0.3** (`Satır: 176-177`) · kısmi keyword **0.1**
(`Satır: 191`); 3 karakter altı token elenir (`Satır: 184`). Sıralama `usort` + `array_slice`
(`Dosya: shared/src/AI/KnowledgeBase.php · Satır: 64-66`). **Vector cosine similarity YOKTUR** — yorum satırıdır.

## §17 PHP Kanıtı — ragQuery + augment + placeholder

```php
// Dosya: shared/src/AI/KnowledgeBase.php · Satır: 72-81
public function ragQuery(string $query, array $context = []): array
{
    // 1. Retrieval — ilgili belgeleri bul
    $retrieved = $this->search($query, 5, $context['filters'] ?? []);
    // 2. Augmentation — prompt'a bağlam ekle
    $augmentedContext = $this->augmentContext($query, $retrieved, $context);
    // 3. Generation — LLM ile yanıt üret (şimdilik placeholder)
    $answer = $this->generateAnswer($query, $augmentedContext);
```

Çıktı şeması `answer/sources/confidence` üçlüsüdür (`Satır: 83-90`); generation placeholder'dır:
`"Gerçek implementasyonda LLM API çağrılacak"` (`Satır: 213`). `augmentContext` yalnız
`retrieved_documents` + `query_expansion` ekler (`Satır: 199-206`); güven skoru = ort. skor × 1.2, tavan 1.0
(`Dosya: shared/src/AI/KnowledgeBase.php · Satır: 223-231`).

## §18 PHP Kanıtı — kategori + lifecycle

```php
// Dosya: shared/src/AI/KnowledgeBase.php · Satır: 25-27
private const CATEGORIES = ['adr', 'architecture', 'security', 'audio', 'hardware', 'workflow', 'template'];
private const MAX_SEARCH_RESULTS = 50;
private const LIFECYCLE_MAX_AGE_DAYS = 365;
```

Kategori dışı girdi `InvalidArgumentException` atar (`Satır: 98-99`); lifecycle 365 günden eski kayıtları siler
(`Satır: 139-145`); depolama **in-memory dizidir** — `private array $knowledge = []` (`Satır: 30`) → kalıcılık
yoktur, yeniden başlatınca bilgi bankası **boşalır**. Bu, vault bilgi bankasıyla (kalıcı `.md`) en kritik farktır.

## §19 PowerShell — indeks ölçüm + keyword arama kalıbı

> Betik kalıbı — sözdizimi repo `.ai/scripts/*.ps1` kalıbındadır; gövdeler okunmadı (kalıp olarak kullanılır).
> İlk blok bu üretimin **gerçek** ölçüm komutudur (§30 gate).

```powershell
# (gerçek) satır ölçümü — üretim kapısı
$hedef = 'C:\www\coremusic.net\.ai\architecture\K015-AI\knowledge\knowledge-anayasa.md'
[IO.File]::ReadAllLines($hedef, [Text.Encoding]::UTF8).Count
```

```powershell
# (kalıp) vault geneli keyword arama — keys.md §2-§5 tablosunun elle karşılığı
$root = 'C:\www\coremusic.net'
$kw  = 'csrf'
Get-ChildItem -Path (Join-Path $root '.ai') -Recurse -Filter *.md |
    Select-String -Pattern $kw -SimpleMatch |
    Select-Object -First 20 Path, LineNumber
# eşleşen hedef RAG §3.1 satırıyla karşılaştır → çelişki varsa §13 zinciri uygulanır
```

## §20 PowerShell — glob doğrulama + wiki-link kalıbı

```powershell
# (kalıp) §3 hedefleri diskte mi? — RAG §6/2 kapısının komut kalıbı
$root = 'C:\www\coremusic.net'
$yollar = @('.ai\index.md', '.ai\keys.md', '.ai\glossary.md', '.ai\.decisions\index.md')
$yollar | ForEach-Object {
    [pscustomobject]@{ Yol = $_; Diskte = Test-Path (Join-Path $root $_) }
}
```

```powershell
# (kalıp) wiki-link hedefi diskte mi? — gerçek betik: .ai/scripts/wiki-link-check.ps1 (kökten çalışır)
$md = [IO.File]::ReadAllLines((Join-Path $root '.ai\RAG.md'), [Text.Encoding]::UTF8)
$links = $md | Select-String -Pattern '\[\[([^\]]+)\]\]' -AllMatches |
    ForEach-Object { $_.Matches } | ForEach-Object { $_.Groups[1].Value } | Sort-Object -Unique
"toplam wiki-link: $($links.Count)"   # kırık hedef => hayalet satır → §4 kural 1
```

## §21 İllüstratif — Python (inverted index)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
# inverted index iskeleti — upstream docs referansı; repo'da karşılığı YOK (RAG §4 PLANNED)
from collections import defaultdict

def build_index(docs: dict[str, str]) -> dict[str, set[str]]:
    inv: dict[str, set[str]] = defaultdict(set)
    for doc_id, text in docs.items():
        for tok in text.lower().split():
            if len(tok) > 2:                      # PHP ile aynı 3-karakter eşiği
                inv[tok].add(doc_id)
    return dict(inv)

def lookup(inv: dict[str, set[str]], query: str) -> set[str]:
    toks = [t for t in query.lower().split() if len(t) > 2]
    if not toks:
        return set()
    out = inv.get(toks[0], set())                 # ilk terimle kesişim (AND)
    for t in toks[1:]:
        out &= inv.get(t, set())
    return out
```

Vault karşılığı bugün `keys.md` + `RAG.md` §3.1 tablolarıdır (manuel inverted index) — `Dosya: .ai/RAG.md · Satır: 116`.

## §22 İllüstratif — TypeScript (BM25 ranking)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```ts
// BM25 sıralama kalıbı — upstream docs referansı; repo'da yok (pipeline sırası: exact → BM25 → vektör)
type Doc = { id: string; terms: string[] };

export function bm25rank(query: string, docs: Doc[], k1 = 1.2, b = 0.75) {
  const qs = query.toLowerCase().split(/\s+/).filter(t => t.length > 2);
  const avg = docs.reduce((n, d) => n + d.terms.length, 0) / docs.length;
  return docs
    .map(d => {
      const s = qs.reduce((acc, t) => {
        const f = d.terms.filter(x => x === t).length;
        const n = docs.filter(x => x.terms.includes(t)).length + 1;
        const idf = Math.log(1 + (docs.length - n + 0.5) / (n + 0.5));
        return acc + idf * (f * (k1 + 1)) / (f + k1 * (1 - b + b * d.terms.length / avg));
      }, 0);
      return { id: d.id, s };
    })
    .sort((x, y) => y.s - x.s);
}
```

Yeri: `Dosya: .ai/RAG.md · Satır: 120` — "exact keyword → BM25 → vektör (semantik); ilk ikisi bugün el ile".

## §23 İllüstratif — Go (knowledge graph triple store)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
// triple store iskeleti (subject/predicate/object) — upstream docs; repo'da karşılığı YOK
package kg

type Triple struct{ S, P, O string }

type Store struct{ byS map[string][]Triple }

func (s *Store) Add(t Triple) { s.byS[t.S] = append(s.byS[t.S], t) }

// konu→dosya eşlemesi üçlü olarak: (konu, "hedef", dosyaYolu)
func (s *Store) Resolve(subject string, predicate string) []string {
    var out []string
    for _, t := range s.byS[subject] {
        if t.P == predicate {
            out = append(out, t.O)
        }
    }
    return out
}
```

Vault üçlüsü: `(konu, "hedef-dosya", .ai/…)` — RAG §3.1 tablosunun sözdizimsel karşılığı (`Satır: 73-94`).

## §24 İllüstratif — Rust (chunk dedup hash)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```rust
// chunk dedup kalıbı — upstream docs; repo'da yok (chunk stratejisi: PLANNED, RAG §4/2)
use std::collections::hash_map::DefaultHasher;
use std::hash::{Hash, Hasher};

pub fn chunk_id(path: &str, heading: &str, body: &str) -> u64 {
    let mut h = DefaultHasher::new();          // üretimde kriptografik hash tercih edilir
    path.hash(&mut h);
    heading.hash(&mut h);
    body.trim().hash(&mut h);
    h.finish()
}

pub fn dedup(ids: Vec<u64>) -> Vec<u64> {
    let mut seen = std::collections::HashSet::new();
    ids.into_iter().filter(|i| seen.insert(*i)).collect()
}
```

İlişki: chunk ~500 token hedefi **tasarımdır** (`Dosya: .ai/RAG.md · Satır: 117`) — bu kalıp o tasarımın
dedup katmanıdır; kod olarak repo'da **yoktur**.

## §25 İllüstratif — C# (knowledge lifecycle TTL)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```csharp
// bilgi lifecycle TTL kalıbı — upstream docs; repo karşılığı PHP cleanupLifecycle (Satır: 134-156)
public sealed record Entry(string Id, DateTimeOffset CreatedAt, string Category);

public static (int Removed, int Kept) Cleanup(IEnumerable<Entry> all, DateTimeOffset cutoff)
{
    int removed = 0, kept = 0;
    foreach (var e in all)
    {
        if (e.CreatedAt < cutoff) removed++;     // PHP: 365 gün eşiği (Satır: 139-145)
        else kept++;
    }
    return (removed, kept);
}
```

Vault tarafında karşılığı **yoktur**: bilgi bankası `.md` dosyalarıdır, TTL uygulanmaz — silme yalnız ADR
lifecycle ve hayalet-satır kurallarıyla olur (`Dosya: .ai/RAG.md · Satır: 102`).

## §26 İllüstratif — C++ (posting list skoru)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```cpp
// posting list + ağırlıklı skor — upstream docs; PHP şemasının (0.6/0.3/0.1) C++ karşılığı
#include <string>
#include <vector>

struct Hit { std::string id; float score; };

float lexical(const std::string& q, const std::string& title, const std::string& body) {
    float s = 0.f;
    if (title.find(q) != std::string::npos) s += 0.6f;   // başlık: yüksek ağırlık
    if (body.find(q)  != std::string::npos) s += 0.3f;   // gövde: düşük ağırlık
    return s;                                             // kısmi katman ayrıca (Satır: 191)
}
```

Ağırlık şeması birebir PHP'deki mantıktır — `Dosya: shared/src/AI/KnowledgeBase.php · Satır: 171-191`; bu blok
**illüstratiftir**, repo'da C++ bilgi bankası **yoktur** (ses motoru yüzeyi ayrı alt-dümüğüm).

## §27 Açık Sorular

| # | Soru | Öncelik | Durum / Kanıt |
|---|------|---------|----------------|
| 1 | ADR sayısı hangisi doğru: 91 (index FM) / 92 (decisions tablo) / 93 (decisions §2 disk ölçümü)? | HIGH | `⚠️ VERIFICATION REQUIRED` — `index.md:16-19` ↔ `decisions/index.md:33 · 42` |
| 2 | `glossary.md` §3 stub 12 terimi bekliyor — ne zaman eklenir? | MEDIUM | `.ai/AGENTS.md` §3 (VERIFICATION REQUIRED işareti) |
| 3 | Bilgi bankası PHP katmanı kalıcı depoya (DB/dosya) ne zaman taşınır? | HIGH | `PLANNED` — in-memory (`KnowledgeBase.php:30`) |
| 4 | `keys.md` §3 bölümü (Core Vault'tan sonra §4'e atlamış) — eksik mi? | LOW | `.ai/keys.md` §2 → §4 sıçraması (Satır: 60-64) |
| 5 | `.ai/architecture/**` kaldırıldı (2026-10-12) mi, yoksa ağaç diskte duruyor mu? | HIGH | `shared/AGENTS.md` iddiası ↔ diskte `K015-AI/` ağacı (bu dosyanın yeri) |

## §28 Kaynaklar

| # | Kaynak | Yol / URL | Kullanım |
|---|--------|-----------|----------|
| 1 | Retrieval indeksi (SSOT) | `.ai/RAG.md` (177 satır, v1.1.0) | §4-§5, §11-§15 |
| 2 | Keyword haritası | `.ai/keys.md` (v28.3.6) | §6 |
| 3 | Master katalog | `.ai/index.md` (v30.0.0) | §7 |
| 4 | Terim sözlüğü | `.ai/glossary.md` (v2.3.0) | §8 |
| 5 | ADR indeksi | `.ai/.decisions/index.md` (v1.5.0) | §9, §13 |
| 6 | Bilgi bankası kodu | `shared/src/AI/KnowledgeBase.php` (232 satır) | §16-§18 |
| 7 | Anayasa şablonu (yapı) | `.ai/architecture/K015-AI/rag/rag-anayasa.md` (516 satır) | § yapısı, FM 10 alan |
| 8 | Upstream — RAG aşamaları | https://developers.llamaindex.ai/python/framework/understanding/rag/ (webfetch 2026-10-17) | §11, §21-§24 — Loading/Indexing/Storing/Querying/Evaluation |
| 9 | Upstream — bilgi paylaşımı/SSOT | https://www.atlassian.com/software/confluence/resources/guides/best-practices/knowledge-management (webfetch 2026-10-17) | §3, §10 — "Build one location for all your team's knowledge" |
| 10 | Upstream — SSOT makalesi | https://www.atlassian.com/work-management/knowledge-sharing/documentation/building-a-single-source-of-truth-ssot-for-your-team (websearch 2026-10-17, içerik okunmadı) | §3 — yalnız başlık/URL doğrulandı |

## §29 Hızlı Referans (tek ekran)

| # | Soru | Cevap |
|---|------|-------|
| 1 | Bu bilgi nereye girer? | §10 tablosu — terim→glossary, karar→decisions, konu→RAG §3, keyword→keys, dosya→index |
| 2 | Çeliştiğinde kim kazanır? | Disk > ölçüm > ADR > katalog > keyword > hafıza (§13) |
| 3 | Otomatik retrieval var mı? | Hayır — PLANNED, kod 0 (§11) |
| 4 | Bilgi bankası kalıcı mı? | PHP katmanı hayır (in-memory, §18); vault `.md` evet |
| 5 | Secret bilgi bankasına girer mi? | Asla — REDACTED (§14) |
| 6 | Yeni `.md` üretim sırası? | şablon → yaz → index/keys/RAG satırı → ölç → log (§10) |

## §30 Ölçüm / Gate Notu

| # | Eşik | Araç | Not |
|---|------|------|-----|
| 1 | ≥500 tam satır | `[IO.File]::ReadAllLines($hedef, UTF8).Count` | üretim sonrası ölçülür |
| 2 | FM 10 alan | gözden geçirme | sıra sabit, `title`/`parent` anahtarı YOK |
| 3 | Authority son satır | gözden geçirme | birebir son satır |
| 4 | Kod blokları | PHP 3 künyeli gerçek alıntı + PS 4 kalıp + 6 dil illüstratif | illüstratiflerde uyarı etiketi zorunlu |
| 5 | Web kaynakları | 3 (2 webfetch + 1 websearch işaretli) | uydurma URL yasak |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
