---
title: "CoreMusic — RAG Retrieval Index & Pipeline"
type: docs
category: vault
date: 2026-10-07
updated: 2026-10-07
version: 1.0.0
status: active
authority: "SSOT (klasör kökü) — çelişkide disk kazanır"
docType: rag
# Control Plane v2 (Q7 geniş şema — 2026-10-07):
tier: 5
domain: navigation
ssot: true
risk: low
owner: "MO"
depends-on: [".ai/CLAUDE.md", ".ai/index.md", ".ai/keys.md"]
---

# CoreMusic — RAG Retrieval Index & Pipeline

**docType:** rag · **Klasör:** `.ai/` · **Sorumlu:** MO (vault-updater)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[index.md]] · [[keys.md]] · [[CONTEXT.md]] · [[.templates/index]]

> **Bu dosya ne işe yarar:** AI'ın "bu soru hangi dosyayı okumalı?" sorusunu tek tablodan
> çözmesini sağlar (§3 retrieval indeksi) ve embedding/retrieval sisteminin tasarımını
> **dürüst durum etiketiyle** taşır (§4 pipeline — büyük bölümü `PLANNED`).

---

## §1 Amaç

Vault'taki bilgi parçalıdır: anayasa `.ai/CLAUDE.md`'de, süreç `.ai/WORKFLOW.md`'de,
envanter `.ai/CONTEXT.md`'de, kararlar `.ai/.decisions/`'de, mimari `.ai/architecture/`'dadır.
Bir soru geldiğinde AI'ın **tüm vault'u taraması** yerine **ilk okuyacağı dosyayı bu dosyadan**
eşlemesi hedeflenir (retrieval indeksi); ayrıca gelecekteki otomatik retrieval (embedding /
vektör arama) sisteminin tasarım kaydı burada tutulur (pipeline). Bu dosya bir **süreç dosyası
değildir**; kural koymaz, yalnız yönlendirir ve durum raporlar.

| Karar | Karşılığı (kaynak) |
|-------|--------------------|
| Retrieval = vault dosyaları; harici kaynak vault altındadır | Kök [[../AGENTS]] §3 (Zero-Hallucination) |
| Pipeline durum kanıtı = ADR-030 (RAG/embedding: kod 0, tablo 0 → PLANNED) | [[.decisions/accepted/ADR-030-ai-strategy-core]] |
| İndeks satırı disk kanıtlıdır (glob) | Guardrail #3 → [[CLAUDE]] §7 |
| Yeni dosya şablonla üretilir | Guardrail #16 → [[.templates/documentation/rag-md-template]] |
| Boot listesine (13 kanonik) eklenmez — on-demand referanstır | [[CLAUDE]] §16 (sayım zinciri değişmez) |

> **eli10 (basit):** Bu dosya, "şu soru için hangi dosyaya bakılır" cevabının listesidir; ayrıca gelecekte kurulacak otomatik arama sisteminin tasarımını saklar.
> **eli15 (detay):** Ayrı dosyadır çünkü yönlendirme bilgisi kural metninden daha sık değişir ve tek tabloda taranır. İndeks satırları diskteki gerçek dosyalardan üretilir; uydurma satır yanlış dosyaya götürür. Okunması, aranan konuya doğrudan dosya hedefi vermeyi sağlar. Vault'a yeni dosya eklendiğinde veya taşındığında §3 tablosu güncellenir; pipeline bileşenlerinin kodu yazıldığında §4'teki durum `PLANNED`'dan `IMPLEMENTED`'a çevrilir.

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| §3 Retrieval indeksi — konu → hedef dosya → keyword → durum | Dosya içeriği özetleri (yalnız yol + rol + keyword) |
| §4 Pipeline tasarımı — chunk/embedding/store/arama + durum sütunu | Embedding/vektör **kodu** (ADR-030; kod ayrı görevde üretilir) |
| §5-§6 güncelleme kuralı ve tekrarlanabilir doğrulama | ADR metinleri (frozen — yalnız wiki-link) |
| Kaynak kapsamı: `.ai/**/*.md` + kök boot dosyaları | Alt alan adı uygulama kodunun tamamı (yalnız eşleme satırı) |

- **Kullananlar:** tüm AI ajanları (görev başı 1 kez okur), MO (indeks güncellemesi), Vault Steward (denetim).
- **Ön koşul:** İndeks satırları glob ile doğrulanmıştır; `Durum` sütunu her satırda doludur.
- **Ölçüm tarihi:** 2026-10-07 (tüm sayılar bu tarihte diskten ölçülmüştür).

---

## §3 Mimari — Retrieval İndeksi

### §3.1 Konu → Dosya Eşlemesi (20 satır — 2026-10-07 disk ölçümü)

| # | Konu / Soru Sınıfı | Hedef Dosya(lar) | Keyword'ler | Durum |
|---|--------------------|------------------|-------------|-------|
| 1 | Boot sırası, kanonik okuma listesi | [[CLAUDE]] §16 · [[../WORKFLOW]] (kök) boot | boot, okuma, kanonik, 13 dosya | IMPLEMENTED |
| 2 | Guardrail, yasak, anayasa, forbidden pattern | [[CLAUDE]] §7 · §21 · §23 | guardrail, yasak, revert, anayasa | IMPLEMENTED |
| 3 | Agent routing, handover, persona hiyerarşisi | [[AGENTS]] §4 · §6 · §15 · [[../AGENTS]] (kök) | agent, routing, persona, handover | IMPLEMENTED |
| 4 | Süreç, faz, hard gate, ADR lifecycle | [[WORKFLOW]] §8-§13 | faz, gate, süreç, workflow | IMPLEMENTED |
| 5 | Vault envanteri, dizin yapısı, boot ilişkisi | [[CONTEXT]] | envanter, dizin, context, boot | IMPLEMENTED |
| 6 | Master katalog, dosya/ADR listesi | [[index]] | katalog, indeks, dosya listesi | IMPLEMENTED |
| 7 | Keyword routing eşlemesi | [[keys]] | keyword, arama, eşleme | IMPLEMENTED |
| 8 | Mimari kararlar (ADR) | [[.decisions/index]] · [[brain]] (accepted 78 · draft 1 · rejected 11 + index — 2026-10-07) | adr, karar, frozen, decision | IMPLEMENTED |
| 9 | 500 katmanlı mimari (K000-K499, 10 domain) | [[architecture/00-master-index]] + `10-domain-d01` … `19-domain-d10` (architecture/ yeniden yapılandırması sürüyor — dosya sayısı bkz. [[CONTEXT]] §3.2) | katman, domain, 500, architecture | IMPLEMENTED |
| 10 | Şablon seçimi (Guardrail #16) | [[.templates/index]] (65 dosya, 13 kategori — 2026-10-07) | şablon, template, guardrail-16 | IMPLEMENTED |
| 11 | UI mockup, bileşen, token, ekran spec | [[ui-design/01-mockup-index]] · `ui-design/02-component-inventory.md` (ui-design/ altında 121 md · 20 PNG) | mockup, bileşen, C01-C16, token, wcag | IMPLEMENTED |
| 12 | DB şema, BCNF, migration | `.ai/.sql/` (21 .sql) · [[brain]] ADR-040 · skill `database-normalize-maker` | bcnf, şema, sql, migration, 18 db | IMPLEMENTED |
| 13 | Skill registry ve eşleşme kuralı | [[../CLAUDE]] (kök) §Skill Registry (12 proje skill) | skill, mandate, registry | IMPLEMENTED |
| 14 | Vizyon, kapsam, yetenekler | [[VISION]] · [[PROJECTS]] | vizyon, yetenek, kapsam, pazar | IMPLEMENTED |
| 15 | Terimler | [[glossary]] | terim, sözlük, glossary | IMPLEMENTED |
| 16 | Session hafızası, audit trail | [[MEMORY]] · [[log]] (append-only) | session, log, audit, geçmiş | IMPLEMENTED |
| 17 | PHP/backend kod yüzeyi | `shared/src` (165 .php) · alt alan adı `*/include/` | php, pdo, middleware, shared | IMPLEMENTED |
| 18 | AI stratejisi, RAG/embedding kararı | [[.decisions/accepted/ADR-030-ai-strategy-core]] | rag, embedding, ai-stratejisi, llm | PLANNED |
| 19 | Güvenlik: CSRF/CSP/JWT/session | [[.decisions/accepted/ADR-010-csrf-protection-strategy]] · ADR-011 · ADR-022 · skill `security-hardening` | csrf, csp, jwt, auth, güvenlik | IMPLEMENTED |
| 20 | Class AB donanım, PCB, güç, termal | `architecture/amplifier-classab-circuit.md` · `pcb-classab.md` · `power-supply-classab.md` · `thermal-design-classab.md` · ADR-089 | class-ab, amf, pcb, ±35v, termal | IMPLEMENTED |

**Kapsam notu (olmayanlar — uydurulmaz):** `.ai/wiki/` (kök `AGENTS.md` §7'de anılır, diskte **0 dosya** → VERIFICATION REQUIRED) · `.ai/.rules/error-recovery.md` (diskte YOK — bkz. [[CONTEXT]] §3.5).

### §3.2 Eşleme Kuralları

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Satır yalnız diskte var olan dosyaya gider (Test-Path / glob) | Hayalet satır silinir |
| 2 | Birden çok hedef varsa en dar (domain-specific) dosya önce yazılır | Yanlış öncelik |
| 3 | Durum sütunu: `IMPLEMENTED` (dosya kanıtlı) · `PLANNED` (ADR/plan var, kod/yok) · `UNKNOWN` | Durumsuz satır geçersiz |
| 4 | Sayılar (96 md, 65 şablon, 21 sql …) yalnız ölçümle yazılır | Zero-Hallucination ihlali |

---

## §4 Pipeline Tasarımı (Durum: ÇOĞU PLANNED — kanıt ADR-030)

> ADR-030 (AI Strategy Core) tespiti: *"RAG / embedding / vektör — ❌ PLANNED (kod 0, tablo 0)"*.
> Aşağıdaki tablo **tasarımdır**; `IMPLEMENTED` satırı hariç hiçbir bileşen kodla desteklenmez.

| # | Bileşen | Tasarım (karar) | Durum | Kanıt |
|---|---------|----------------|-------|-------|
| 1 | Manuel keyword indeksi | [[keys]] + bu dosyanın §3 tablosu (taban retrieval'ı) | IMPLEMENTED | `.ai/keys.md` · `.ai/RAG.md` (diskte) |
| 2 | Chunk stratejisi | Bölüm (`§`) bazlı chunk, hedef ~500 token; başlık + dosya yolu metadata olarak korunur | PLANNED | bu dosya (tasarım) |
| 3 | Embedding adayı | Model/servis seçimi YAPILMADI — hangi modelin kullanılacağı karar bekliyor | PLANNED | ADR-030 (karar yok) |
| 4 | Vektör store | Seçim YAPILMADI (adaylar: SQLite-vec / MySQL 9 / harici servis — hepsi değerlendirilmedi) | PLANNED | ADR-030 (tablo 0) |
| 5 | Arama sırası | Hedef: exact keyword → BM25 (metin) → vektör (semantik); ilk ikisi bugün el ile yapılır | PLANNED | ADR-030 + bu dosya §3 |
| 6 | Güncelleme tetikleyicisi | Vault'a dosya ekleme/ taşıma → `vault-sync-post` ile §3 satırı yeniden ölçülür | PLANNED | skill `vault-sync-post` (var) |
| 7 | Kaynak kapsamı | `.ai/**/*.md` + kök boot dosyaları (`.md`); kod `.php/.js` şimdilik KAPSAM DIŞI | PLANNED | bu dosya §2 |

> **eli10 (basit):** Otomatik arama sistemi henüz kurulmadı; bugün arama, bu dosyadaki liste ve keyword tablosuyla elle yapılıyor.
> **eli15 (detay):** Pipeline ayrı bölüm tutuldu çünkü tasarımı olan ile olan şeyi karıştırınca "sistem var" sanılır. Kodu olmadığı için her satır `PLANNED` işaretlidir ve kanıtı ADR-030'tur. Embedding modeli seçilmediği için vektör store kararı da bekler. §4'te bir satır `IMPLEMENTED` olacaksa o bileşenin dosyası diskte bulunmalı ve kanıt sütununa yazılmalıdır.

---

## §5 Kurallar

| # | Kural | Neden var | Ref |
|---|-------|-----------|-----|
| 1 | İndeks satırı = disk kanıtı; olmayan dosya yazılmaz | Yanlış hedef, isabetsiz analiz | Guardrail #3 |
| 2 | Durum kanıtsız `IMPLEMENTED` yapılmaz | "Var" sanılan sistem aslında yok | ADR-030 |
| 3 | Vault dosya ekleme/taşıma işleminden sonra §3 yeniden ölçülür | İndeks bayatlar | `vault-sync-post` |
| 4 | Boot §16 listesi bu dosyayla değiştirilmez (13 kanonik sabit) | Sayım zinciri (13 = 14−1) bozulur | [[CLAUDE]] §16 |
| 5 | `.env.figma` / credential anahtarları hiçbir satıra girmez | Sızıntı | REDACTED |
| 6 | Wiki-link `[[...]]` formatı; markdown path linki değil | Link denetimi çalışır | context-template §4.1 #5 |

---

## §6 Doğrulama

| # | Kontrol | Kriter | Komut / Yöntem |
|---|---------|--------|----------------|
| 1 | Frontmatter | 7 alan + Control Plane v2 bloğu + `docType: rag` | gözden geçirme |
| 2 | §3 hedefleri | Her satırdaki dosya diskte | Test-Path / glob |
| 3 | Durum sütunu | §3 ve §4'te her satır dolu | gözden geçirme |
| 4 | ADR tutarlılığı | §4 `PLANNED` satırları ADR-030 ile çelişmiyor | ADR-030 okuma |
| 5 | Vault bütçe/doğrulama | 8 check yeşil | `node .ai/scripts/validate.mjs --check` |
| 6 | Placeholder | Dosyada `{{` yok | grep |
| 7 | Wiki-link | Hedefler diskte | `.ai/scripts/wiki-link-check.ps1` (kökten) |
| 8 | Boot listesi | §16 13 kanonik değişmedi | [[CLAUDE]] §16 |
| 9 | Çift katman | Kök `RAG.md` pointer (≤80 satır) · bu dosya SSOT | gözden geçirme |
| 10 | Sayı tazeliği | §3/§4 sayıları ölçüm tarihiyle (2026-10-07) | yeniden sayım |

---

## §7 Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Vault anayasası | [[CLAUDE]] | Guardrail #3/#16, boot §16 |
| Master katalog | [[index]] | Dosya/ADR indeksi kaynağı |
| Keyword haritası | [[keys]] | §3 ile eşleşen keyword tablosu |
| Vault envanteri | [[CONTEXT]] | Dizin/dosya sayıları |
| AI strateji ADR'si | [[.decisions/accepted/ADR-030-ai-strategy-core]] | Pipeline durum kanıtı (PLANNED) |
| Şablon | [[.templates/documentation/rag-md-template]] | Bu dosyanın iskeleti (Guardrail #16) |
| Şablon registry | [[.templates/index]] | Şablon envanteri |
| Kök pointer | [[../RAG]] | Kök RAG.md (pointer) |
| Doğrulama betiği | `.ai/scripts/validate.mjs` | 8 check + bütçe |
| Disk kanıtı | 2026-10-07 glob sayımı | §3/§4 tüm sayılar |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/documentation/rag-md-template.md`
**Last Updated:** 2026-10-07 · **docType:** rag · **Status:** §3 IMPLEMENTED (manuel indeks) · §4 PLANNED (ADR-030)
