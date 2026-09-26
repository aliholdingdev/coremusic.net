---
title: "CoreMusic — ADR-005: Ultrathink Protokolü (Zero Hallucination Hedefi)"
type: adr
category: architecture
date: 2026-09-24
updated: 2026-09-24
version: 1.0.0
status: accepted
authority: ADR-005 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 17/3/0 KABUL)"
---

# CoreMusic — ADR-005: Ultrathink Protokolü (Zero Hallucination Hedefi)

**Durum:** accepted (kabul — frozen YOK; okunur + yazılabilir)
**Tarih:** 2026-09-24
**Karar Veren:** Vault Steward (kullanıcı onaylı — "sıfırdan yaz") · debate: ✅ TAMAMLANDI (17/3/0 KABUL) · Tech Lead: ✅
**İlgili ADR'ler:** [[ADR-004-multi-domain-spa]] (kabul edilmiş karar örneği — bu protokolün hedef kitlesi) · [[ADR-001-vanilla-js-itcss]] (kod yazımı bağlamı) · karar dizini [[../index]] §3 satırı `ADR-005-ultrathink-protocol` (slug eşleşmesi ✅ — dosya yazıldıkça dizin linki canlı hale gelir) · Eski seri (ADR-009/021/044/045/046/083/085 vb.) dosyaları diskte YOK → eski numaralara wiki-link KURULMAZ, düz metin.

---

## 1. Bağlam (Context)

LLM tabanlı ajanlar vault üzerinde çalışırken **kaynaksız iddia** (hallüsinasyon) üretme riski taşır: var olmayan dosya yolu, doğrulanamayan yüzde/sürüm bilgisi, disk kanıtı olmadan `IMPLEMENTED` etiketi. Bu risk, kabul edilmiş kararların (ör. [[ADR-004-multi-domain-spa]]) yanlış yorumlanmasına ve `.ai/` vault'unun SSOT niteliğini kaybetmesine yol açar. Bu karar **üç parçayı** tescil eder: **(1)** 5 adımlı Ultrathink protokolü — `[[../../AGENTS.md]]` §24.1 ile birebir hizalı; **(2)** `⚠️ VERIFICATION REQUIRED` etiketi kuralları (neye vurur, ne zaman kalkar); **(3)** `/hallucination-control` skill'inin diskteki gerçek varlık durumu.

**Konumlandırma (bilimsel dürüstlük):** Başlık/önerme olan **"Zero Hallucination" bir HEDEF konumundadır — iddialı sıfır hallüsinasyon garantisi DEĞİL, ölçülebilir hedeftir.** 2025-26 benchmarkları sıfır hallüsinasyonun mevcut modellerde ölçülebilir biçimde sağlanamadığını gösterir (§1.3); bu ADR "garanti" değil, "ölçülebilir ve denetlenebilir hedef" der.

### 1.1 Mevcut Durum

- **Protokol (disk kanıtı — IMPLEMENTED, doküman):** `[[../../AGENTS.md]]` §24.1 "5-Step Thinking Protocol" tablosu (Vault Oku → Bağlamı Anla → Hata Kontrolü → Sonuç Tahmini → Doğrulama; timeout sütunlarıyla) + §24.4 otomatik temizlik (Hallüsinasyon → "VERIFICATION REQUIRED" etiketi) + §18 Warnings #3 (Hallüsinasyon → `VERIFICATION REQUIRED` etiketi) + §18 #1 (ihlal → "Sistem durur, MO müdahale eder").
- **Skill — varlık bulgusu (disk kanıtı):** `.claude/skills/hallucination-control/` klasörü **VAR** (`SKILL.md` + `CLAUDE.md` — glob/dizin kanıtı). `[[../../AGENTS.md]]` §14 zorunlu 5 skill tablosunda 4. satır `/hallucination-control` ("Halüsinasyon doğrulama / Kod yazma öncesi"). **`.opencode/skills/` altında aktif `hallucination-control/` klasörü YOKTUR** — §14 Faz 1 doğrulama notundaki 8 aktif SKILL.md (composer-sync, db-engine, orchestration, truth-engine, ui-workbench, vault-sync-post, agent-debate, context-report) arasında yer almaz; yalnızca arşivde `.opencode/skills/_archive-keep/hallucination-control/CLAUDE.md` (yalnız CLAUDE.md, SKILL.md yok) bulunur.
- **Etiket altyapısı (disk kanıtı — IMPLEMENTED):** `[[../../AGENTS.md]]` §17 #5 (`// ⚠️ VERIFICATION REQUIRED` — bilinmeyen class/API), §24.4, §25.2 "Stack Etiketi" (IMPLEMENTED/PLANNED ayrımı — `[[../../engine]]` §9.2 ile uyumlu); vault genelinde etiket kullanımı örnekleri (ör. `[[../index]]` §5 dead-link notları, `[[../../AGENTS.md]]` §24.3 Embedded okuma satırı).
- **Doğrulama standardı altyapısı:** `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (diskte OKUNDU — "Cross-Verification: 2+ bağımsız kaynak", "Source cited for every non-obvious claim", Anti-Hallucination Enforcement tablosu).
- **Audit trail (disk kanıtı — IMPLEMENTED):** `[[../../log]]` — append-only; mevcut 400 satır; ADR-003/004 yazım + debate satırları görünür.
- **Kapsam dışı:** Karar protokol/kuralları belgeler — kod üretmez; agent routing/handover `[[../../AGENTS.md]]` tekelindedir (DIP).

### 1.2 Sorun Tanımı

(1) **Protokol dağılmış durumda:** 5 adım `[[../../AGENTS.md]]` §24.1'de yazılı ama ADR olarak tescilli değil; ajanlar "neden bu sıra?" sorusuyla tek tek karşılaşır. (2) **Etiket kuralı belirsiz:** `⚠️ VERIFICATION REQUIRED` neye vurur, ne zaman kalkar, kalkınca ne olur — yazılı sınır yoksa etiket ya kaba kullanılır (her şeye takılır → yorgunluk) ya da hiç takılmaz (örtbas → hallüsinasyon). (3) **Skill konumu çelişkili görünüyor:** §14 tablosunda skill var, `.opencode/skills/` altında yok — dürüst kayıt gerekli (uydurma yasak). (4) **"Zero Hallucination" iddia riski:** ölçülmemiş mutlak iddia, hallüsinasyonun kendisidir — konumu "hedef" olarak sabitlenmeli (§1.3 kanıtlar).

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: prompt-maker web araştırma protokolü (`.claude/skills/prompt-maker/references/10-web-research-protocol.md` — diskte OKUNDU ✅) — birincil/resmi kaynak önce, **her iddiaya ≥2 bağımsız çapraz kaynak**, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`. **Odak: (a) LLM hallüsinasyon oranları 2025-26 (FACTS, TruthfulQA türevleri, ölçüm paper'ları); (b) citation/fact-check yöntemleri; (c) agent/denetim güvenilirliği.**

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "LLM hallucination rate benchmark 2025 2026 FACTS Leaderboard TruthfulQA measurement" · (2) "LLM citation accuracy fact-checking verification methods 2025 NLI chain-of-verification" · (3) "AI agent reliability evaluation 2025 unsupported statements deep research citation audit" · (4) "hallucination-free guarantee measurable target claim decomposition evidence verification" |
| Web Search **Konusu** | 2025-26 LLM hallüsinasyon oranları (benchmarklar: FACTS Leaderboard, DefAn, TruthfulQA/HalluLens, multilingual ölçüm); citation/fact-check yöntemleri (kanıt-temelli VERIFY, NLI entailment, claim-decomposition, chain-of-verification); derin-araştırma/agent denetimi (citation accuracy, unsupported statement oranı); "sıfır hallüsinasyon" iddiasının bilimsel konumu. |
| Web Search **Bağlam** | 10 birincil kaynak (2025 Nisan - 2025 Aralık): EMNLP/Findings 2025, ACL 2025, arXiv (2512.10791, 2504.17550, 2509.04499, 2510.11394), MDPI Information 16(11):937, ICCS 2025; ikincil: TruthfulQA (Lin et al. 2022, arXiv:2109.07958 — kaynak 3/4/5 içinde referans), Chain-of-Verification (Dhuliawala et al. 2024 — kaynak 8/9 içinde referans). |
| Web Search **Kısa Açıklama** | Hallüsinasyon ölçülebilir, düşürülebilir — ama **2026'da "sıfır" DEĞİL**: en iyi FACTS skoru 68.8/100 (Gemini 3 Pro), DefAn'te olgusal hallüsinasyon %48-82 (public set), DeepTRACE'te citation accuracy sistemler arası %40-80. "Zero Hallucination" bu yüzden **ölçülebilir hedeftir, garanti değildir.** En güvenilir doğrulama ailesi: iddiayı parçala → kanıt getir → NLI/insan hizasıyla doğrula (FactBench/VERIFY, FASTFACT, VeriCite — 3 kaynak); kendi-kendine değerlendirme ve tek-kaynaklı self-check yetersizdir (Mirage, CiteVerifier). |
| Web Search **Uzun Açıklama** | **(a) Oranlar 2025-26:** FACTS Leaderboard (Google, arXiv 2512.10791, Ara. 2025): 4 alt-test (Multimodal/Parametric/Search/Grounding), en iyi FACTS Score **68.8** (Gemini 3 Pro), Parametric en iyi **76.4**; GPT-5'i sık-cekine (hedge) %13.3 — bile en güçlü modeller doğrulukta %100'den uzak. DefAn (MDPI Information 2025): 9 SoTA modelde olgusal hallüsinasyon **%48-82** (public), **%31-76** (hidden); prompt-uyumsuzluğu %95'e kadar; sayısal bilgide en kötü performans. Multilligual ölçüm (EMNLP 2025): 30 dilde token hallüsinasyon oranı **%5.83 (Sindhi) - %16.81 (Hebrew)**; küçük modeller anlamlı biçimde daha çok halüsinasyon üretir (p=0.01); dil sayısıyla pozitif korelasyon (r=0.88). **(b) Benchmark sağlığı:** HalluLens (arXiv 2504.17550): TruthfulQA **doymuş** (eğitim verisine girdi), yanlış gold cevaplar içeriyor; hallüsinasyon ≠ factuality ayrımı (intrinsic/extrinsic). Mirage — Evaluating Evaluation Metrics (EMNLP Findings 2025): 6 metrik ailesi × 4 dataset × 37 model — **GPT-4 dışında hiçbir metrik insan yargısıyla tutarlı hizalanmıyor** (UniEval-suite rastgele şansın altında). FactBench/VERIFY (ACL 2025): kanıt-temelli Supported/Unsupported/Undecidable etiketlemesi insan değerlendirmesiyle en güçlü korelasyonu verir; factuality zor promptlarda düşer ve **ölçekle garanti edilmez** (Llama3.1-405B ≈/≤ 70B); Gemini1.5-Pro redlerinin %25'i geçersiz. **(c) Citation/agent denetimi:** DeepTRACE (arXiv 2509.04499): generative search + deep-research sistemlerinde **unsupported statement oranı yüksek** (PPLX-DR %97.5, YouChat-DR %74.6) ve **citation accuracy %40-80**; LLM-judge ↔ insan korelasyonu 0.62-0.72 (ortalama). CiteVerifier (ICCS 2025): citation verification'da en iyi genel model Command R+ **%68 exact accuracy**; pratik sistemler için asgari hedef **≥%90** (makalece). VeriCite (arXiv 2510.11394): NLI ile 3 aşamalı (yanıt üret → kanıt seç → rafine et) doğrulama citation kalitesini artırır; **genel LLM'e "üretici + doğrulayıcı" çift rolü vermek doğruluğu düşürür.** FASTFACT (EMNLP Findings 2025): chunk-seviyesi claim çıkarma + document-seviyesi kanıt + doğrulama = insan hizasında en yüksek + en verimli pipeline; kısa SERP snippet'i "yetersiz kanıt" üretir (~24 kelime yerine ~7054 kelime doküman). |
| Web Search **Paragraf Veri Uzun** | FACTS 68.8/100 en iyi skor · Parametric en iyi 76.4 · GPT-5 hedge %13.3 · DefAn olgusal halüsinasyon %48-82 public / %31-76 hidden · PMH %95'e kadar · sayısal bilgi en zayıf halka · token oranı %5.83-%16.81 (30 dil) · küçük model > halüsinasyon (p=0.01) · dil sayısı korelasyonu r=0.88 · TruthfulQA doymuş + yanlış gold · hallüsinasyon ≠ factuality · 6 metrik ailesinden yalnız GPT-4judge insanla hizalı · UniEval rastgele altında · VERIFY: Supported/Unsupported/Undecidable en güçlü insan korelasyonu · factuality zor promptta düşer · ölçek garanti değil (405B ≈ 70B) · Gemini red %25 geçersiz · unsupported statement %74.6-%97.5 · citation accuracy %40-80 · judge-insan Pearson 0.62-0.72 · citation verification %68 exact accuracy · asgari hedef ≥%90 · NLI 3 aşamalı doğrulama işe yarar · genel LLM çift rolü zararlı · claim-decomposition + doc-level evidence en iyi hiza · kısa snippet ≈ yetersiz kanıt. |
| Web Search **Sonucu** | 1) **"Zero Hallucination" garanti değil HEDEF olarak konumlandırıldı:** sıfır hallüsinasyon 2025-26 ölçümünde mevcut modellerde yok (FACTS 68.8/100, DefAn %48-82, token %5.83-16.81 — **kaynak 1, 2, 3**; DeepTRACE %40-80 citation — **kaynak 7, 8**). 2) **Her iddia ≥2 bağımsız kaynak kuralı literatürle desteklendi:** tek-kaynaklı/self-eval yetersiz (Mirage: yalnız GPT-4judge hizalı — **kaynak 4, 10**; FactBench: kanıt-temelli en güçlü hiza — **kaynak 6, 10**). 3) **Doğrulama standardı = claim-parçalama + kanıt + bağımsız doğrulayıcı:** FactBench/VERIFY, FASTFACT, VeriCite aynı üçlüyü önerir (**kaynak 6, 9, 10**; NLI çift-role uyarısı **kaynak 8, 9**). 4) **Citation doğrulama insan/otomasyon hizası olmadan güvenilmez:** %40-80 aralığı + judge-insan 0.62-0.72 (**kaynak 7, 8**) → IMPLEMENTED için disk kanıtı (dosya yolu okuma) şartı bu boşluğu kapatır. 5) **Agent/denetim güvenilirliği kırılgan:** unsupported statement %74.6-97.5 + benchmark doygunluğu (**kaynak 5, 7**) → `⚠️ VERIFICATION REQUIRED` etiketinin "açıksa kalır" kuralı. 6) **Skill/domain iddiaları disk kanıtıyla sınırlı** (web değil disk) — §1.1 varlık bulgusu. |
| Web Search **Alınan Karar** | **ADR-005 kabul edilir — üç parça:** **(1) 5 adımlı Ultrathink protokolü** [[../../AGENTS.md]] §24.1 ile birebir hizalı (aşağıda §2.2a) — her kod/doc yazımından ÖNCE zorunlu; **(2) `⚠️ VERIFICATION REQUIRED` etiketi kuralları** (§2.2b): doğrulanamayan her iddiaya vurur; kalkışı yalnız yeni bağımsız kaynak veya disk kanıtıyla (§2.2c); **kalkamıyorsa AÇIK KALIR — örtbas edilmez**; **(3) `/hallucination-control` skill kaydı** (§2.2e): `.claude/skills/hallucination-control/SKILL.md` VAR, `.opencode/skills/` altında YOK (arşivde yalnız CLAUDE.md) — `[[../../AGENTS.md]]` §14 notuyla birlikte dürüstçe yazıldı. **Doğrulama standardı:** her iddia ≥2 kaynak; `IMPLEMENTED` için disk kanıtı (dosya yolu) zorunlu. **İhlal yaptırımı — OTURUM KİLİDİ** (§2.2d): kaynaksız iddia/kanıtsız IMPLEMENTED tespit edilirse oturum DURUR, MO onayı olmadan devam EDİLMEZ + revert + `log.md` ERROR (`[[../../AGENTS.md]]` §18 ile uyumlu). **Konum:** "Zero Hallucination" = ölçülebilir HEDEF, garanti değil. |
| Web Search **Sonuç** | Karar 2025-26 verisiyle **desteklendi**: oranlar/benchmarklar (3 kaynak), citation doğrulama boşluğu (3 kaynak), kanıt-temelli doğrulama yöntemleri (3 kaynak), benchmark-sağlığı/doymuşluk (3 kaynak) — toplam **10 birincil URL + 2 ikincil referans**, çapraz doğrulama ≥2 kaynak/tüm iddialarda karşılanır. Disk iddiaları (skill, protokol, etiket) glob/dizin kanıtıyla doğrulandı; kanıtsız iddia bırakılmadı. |

**Kaynak listesi (10 birincil + 2 ikincil):**
1. https://doi.org/10.48550/arxiv.2512.10791 — The FACTS Leaderboard (Ara. 2025): FACTS Score 68.8 zirve, Parametric 76.4, hedge/oran tabloları
2. https://www.mdpi.com/2078-2489/16/11/937 — DefAn (Information 2025, 16(11):937): 9 model, olgusal hallüsinasyon %48-82 / %31-76, PMH %95
3. https://aclanthology.org/2025.emnlp-main.1481.pdf — How Much Do LLMs Hallucinate across Languages? (EMNLP 2025): 30 dil, %5.83-16.81 token oranı, küçük model etkisi
4. https://aclanthology.org/2025.findings-emnlp.1035.pdf — Evaluating Evaluation Metrics: The Mirage of Hallucination Detection (EMNLP Findings 2025): 6 metrik × 37 model, insan hizası yalnız GPT-4judge
5. https://arxiv.org/html/2504.17550v1 — HalluLens (Nis. 2025): TruthfulQA doymuş + yanlış gold; intrinsic/extrinsic taksonomi (hallüsinasyon ≠ factuality)
6. https://aclanthology.org/2025.acl-long.1587.pdf — FactBench / VERIFY (ACL 2025): kanıt-temelli etiketleme, ölçek-garanti-egmez, over-refusal %25
7. https://arxiv.org/abs/2509.04499 — DeepTRACE (Eyl. 2025): unsupported statement %74.6-97.5, citation accuracy %40-80, judge-insan 0.62-0.72
8. https://www.iccs-meeting.org/archive/iccs2025/papers/159110235.pdf — CiteVerifier (ICCS 2025): Command R+ %68 exact accuracy, pratik asgari ≥%90
9. https://arxiv.org/html/2510.11394 — VeriCite (Eki. 2025): NLI 3 aşamalı doğrulama; genel LLM çift rolü zararlı
10. https://aclanthology.org/anthology-files/pdf/findings/2025.findings-emnlp.1295.pdf — FASTFACT (EMNLP Findings 2025): claim-decomposition + document-level kanıt + doğrulama (en iyi insan hizası)
11. (ikincil) TruthfulQA — Lin, Hilton, Evans 2022, arXiv:2109.07958 — kaynak 3/4/5 içinde referans alınan asıl benchmark
12. (ikincil) Chain-of-Verification — Dhuliawala et al. 2024 — kaynak 8/9 içinde referans alınan self-verification yöntemi

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Her iddia ≥2 kaynak | Harici/ölçümsel her iddianın ≥2 bağımsız kaynağı olmalı; tek kaynaklı iddia `⚠️ VERIFICATION REQUIRED` alır (§2.2c). |
| IMPLEMENTED = disk kanıtı | `IMPLEMENTED` etiketi yalnız dosya yolu diskten okunarak verilir; okunamayan yol → etiket düşer, PLANNED/⚠️ yazılır (AGENTS.md §25.2, engine §9.2 hizası). |
| Örtbas yasağı | Doğrulanamayan iddia silinmez/susturulmaz — etiket düşer ve **açıksa kalır**; "etiketi kaldırıp geçmek" yasaktır (§2.2c). |
| Oturum kilidi (AGENTS.md §18) | İhlal tespitinde oturum durur + MO onayı + revert + `log.md` ERROR (§2.2d) — istisnasız. |
| log.md append-only | Tüm kilit/düzeltme kayıtları append ile yazılır; geçmiş satıra dokunulmaz (AGENTS.md §25.3 kural 3). |
| Frozen dokunulmaz + REDACTED | ADR-001…037 metinleri okunur/referanslanır, değiştirilmez (AGENTS.md §25.3 kural 2); secret/credential hiçbir koşulda yazılmaz. |

---

## 2. Karar (Decision)

**CoreMusic, kod/doc yazımından önce zorunlu 5 adımlı Ultrathink protokolünü, `⚠️ VERIFICATION REQUIRED` etiketi kurallarını ve `/hallucination-control` skill kaydını ADR-005 olarak tescil eder. "Zero Hallucination" bir HEDEF konumundadır: ölçülebilir, denetlenebilir bir hedeftir — sıfır hallüsinasyon garantisi DEĞİL.** İhlal yaptırımı **oturum kilididir**: kaynaksız iddia veya kanıtsız `IMPLEMENTED` tespit edilirse oturum durur; MO onayı olmadan devam edilmez; ihlal üreten değişiklik revert edilir ve `log.md`'ye ERROR satırı append edilir (`[[../../AGENTS.md]]` §18 ile uyumlu).

### 2.1 Neden Bu Seçenek?

1. **Vault SSOT'unun tek koruması denetlenebilirliktir:** kararlar (ADR-001…004 vb.) ancak iddiaları disk/kaynakla hizalanmışsa SSOT kalır; kanıtsız `IMPLEMENTED` etiketi SSOT'u içerden çürütür (§1.2).
2. **Protokol zaten vault'ta yazılı — tek eksik tescil:** `[[../../AGENTS.md]]` §24.1 5 adım hâlihazırda SSOT; bu ADR yeni kural icat etmez, birebir hizalar ve sonuçlarını (etiket, kilit, standart) yazar (§2.2a).
3. **Literatür tek-kendini-doğrulamayı reddediyor:** yalnız GPT-4-judge insanla hizalı (kaynak 4), citation doğrulama %40-80 (kaynak 7), çift-rollü LLM doğrulayıcı zararlı (kaynak 9) → bağımsız kanıt (disk/web) şartı (§1.3 Sonucu 2-4).
4. **Etiketin iki yönlü sabitlenmesi yorgunluğu da örtbası da engeller:** "neye vurur / ne zaman kalkar / kalkamazsa açık kalır" üçlüsü olmazsa etiket ya silinir ya da her yere yayılır (§1.2 madde 2).
5. **Tutarlı disiplin:** %48-82 olgusal hallüsinasyon (kaynak 2) ve %74.6-97.5 unsupported statement (kaynak 7) ortalaması, tek oturumda sıfır yanlışı garanti etmez — asıl güvence **yakalama mekanizmasıdır** (etiket + kilit + revert + log).

### 2.2 Teknik Detaylar

**(a) 5 Adımlı Ultrathink Protokolü — `[[../../AGENTS.md]]` §24.1 ile birebir hizalı (kaynak: [[../../AGENTS.md]] §24.1):**

| Adım | Kontrol | Kaynak | Timeout |
|------|---------|--------|---------|
| **1. Vault Oku** | CLAUDE.md → AGENTS.md → WORKFLOW.md → brain.md → ROLE.md → ilgili ADR'ler | `.ai/` vault | Max 25s |
| 2. Bağlamı Anla | Domain, katman, dosyalar, bağımlılıklar | Mevcut kod | Değişken |
| 3. Hata Kontrolü | Syntax, imports, types, style, security | LSP + Manuel | Anlık |
| 4. Sonuç Tahmini | Etki alanı, edge cases, performance | Düşünce | Değişken |
| 5. Doğrulama | LSP, typecheck, test, template uyumu | Build araçları | Anlık |
*Kural:* Adım 1 tamamlanmadan adım 3-5'e atlanmaz; adım 5'te `[[../../.templates/adr/adr-template]]`/ilgili şablon uyumu (Guardrail #16) ve cross-reference geçerliliği de kontrol kapsamındadır (`[[../../AGENTS.md]]` §24.5 kalite listesi).

**(b) `⚠️ VERIFICATION REQUIRED` etiketi kuralları:**

| | Kural |
|---|-------|
| **Neye vurur** | (i) diskte varlığı doğrulanamayan dosya/yol/satır iddiası; (ii) <2 bağımsız kaynağı olan dış iddia (yüzde, sürüm, tarih, benchmark); (iii) `IMPLEMENTED` denip dosya yolu okunamadığında; (iv) bilinmeyen class/API (AGENTS.md §17 #5); (v) `[[../../brain]]` vb. "henüz yazılmadı" sınıfı boşluklar; (vi) okunamayan/vault-dışı tek kaynaklı iddialar. |
| **Ne zaman kalkar** | Yalnız iki koşuldan biriyle: **(1)** ikinci bağımsız kaynak bulundu ve çapraz doğrulandı (≥2 kaynak doldu); **(2)** disk kanıtı elde edildi (dosya yolu diskten okundu — IMPLEMENTED için). Kalkış = vault edit'i + `log.md` append (sessiz silme yasak). |
| **Ne zaman KALKMAZ** | Koşullardan hiçbiri sağlanamıyorsa **etiket açıksa kalır, örtbas edilmez** — iddia metinde kalır, `⚠️ VERIFICATION REQUIRED` işaretli kalır ve sonraki oturumda yeniden denenir. Etkinleştirme: otomatik; kaldırma: yalnız MO/vault edit'ı onayıyla. |
| **Yanlış-pozitif (aşırı etiket)** | Her şeye etiket takmak üretimi kilitlemez: etiket "bilinmeyen" içindir; asıl kilidi §2.2d tetikler — yalnız **etiketsiz kaynaksız iddia** veya **kanıtsız IMPLEMENTED** oturumu durdurur. |

**(c) Doğrulama standardı (madde madde):**

1. Her (harici/ölçümsel) iddia ≥2 bağımsız kaynak taşır — tek kaynak → etiket (§1.4 kısıt 1; web protokolü "cross-verification 2+").
2. `IMPLEMENTED` etiketi için disk kanıtı (okunabilir dosya yolu) zorunludur — kanıt yoksa etiket düşer, `PLANNED`/`⚠️` yazılır.
3. Doğrulanamayan iddia: `⚠️ VERIFICATION REQUIRED` etiketi düşer ve **açıksa kalır, örtbas edilmez** (silme/susturma yasak).
4. Tahmin/uydurma yazılmaz; bilinmeyen "tahminle doldurulmaz" (AGENTS.md §24.4 "Hallüsinasyon → VERIFICATION REQUIRED").
5. Her kalkış/etiket değişikliği `log.md` append ile izlenir (append-only — §25.3 kural 3).

**(d) İhlal yaptırımı — OTURUM KİLİDİ (madde madde):**

1. **Tespit:** Kaynaksız iddia (kural c.1 ihlali) veya disk kanıtsız `IMPLEMENTED` (kural c.2 ihlali) — code review, debate, denetim veya otomatik tarama ile.
2. **Durdurma:** Oturum **DURUR** — yeni çıktı üretilmez, iş genişletilmez; kuralc.3 ihlali (örtbas) da aynı kilidi tetikler.
3. **Onay:** **MO (Master Orchestrator) onayı olmadan devam EDİLMEZ** (`[[../../AGENTS.md]]` §18 #1 "Sistem durur, MO müdahale eder"; eskalasyon zinciri §10).
4. **Revert:** İhlal üreten değişiklik geri alınır (`git revert` / dosya revert) — hatalı iddia vault'ta bırakılmaz (AGENTS.md §18 #6 benzeri revert dili).
5. **Log:** `[[../../log]]`'e **ERROR** satırı append edilir (append-only; §25.3 kural 3) — ihlal türü, dosya, iddia özeti, MO kararı.
6. **Devam:** MO onayı + düzeltme (ek kaynak veya disk kanıtı) + log kaydı tamamlanınca oturum yeniden başlar; tekrarlayan ihlal → §10 L2/L3 eskalasyonu.

**(e) `/hallucination-control` skill — varlık bulgusu ( dürüst kayıt — uydurma yok ):**

| Konum | Durum | Kanıt |
|-------|-------|-------|
| `.claude/skills/hallucination-control/SKILL.md` (+ `CLAUDE.md`) | **VAR** | dizin okuması (glob/dizin kanıtı) |
| `.opencode/skills/hallucination-control/` | **YOK** (aktif 8 skill arasında değil) | `.opencode/skills/` dizin listesi: agent-debate, composer-sync, context-report, db-engine, orchestration, truth-engine, ui-workbench, vault-sync-post |
| `.opencode/skills/_archive-keep/hallucination-control/CLAUDE.md` | VAR — yalnız CLAUDE.md (arşiv) | arşiv dizini kanıtı |
| `[[../../AGENTS.md]]` §14 4. satır `/hallucination-control` | VAR (zorunlu 5 skill tablosu; amaç: "Halüsinasyon doğrulama", kullanım: "Kod yazma öncesi") | AGENTS.md §14 |

*Not:* §14 Faz 1 doğrulama notu yalnızca `/brainstorming` ve `/vault-sync` için "klasör yok" der; hallucination-control için ayrım diskte yukarıdaki gibidir — `.claude/` altında aktif, `.opencode/` altında yalnız arşiv. Kullanım sırasında skill `.claude/skills/` altındaki SKILL.md'den yüklenir; `.opencode/` beklenmez.

**(f) "Zero Hallucination" konumu:** ölçüler = etiketli iddia oranı, kanıtsız IMPLEMENTED sayısı, oturum kilidi tetik sayısı (log.md ERROR satırları) — **hedef: bu metriklerin sprint sonunda sıfıra yaklaştırılması**; garanti: YOK (§1.3: 2026'da sıfır hallüsinasyon ölçümde yok).

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Etiketsiz serbest akış (proses yok — ajan "kendi doğru"sunu denetler)** | Sıfır proses maliyeti, hızlı çıktı | Self-eval yetersiz: yalnız GPT-4judge insanla hizalı (kaynak 4); çift-rollü LLM doğrulayıcı zararlı (kaynak 9); unsupported statement %74.6-97.5 (kaynak 7) | §1.3 Sonucu 2-4 — kendi kendini denetleyen sistem hata üretir; vault SSOT riski |
| 2 | **Yalnız self-check / chain-of-verification (disk kanıtı olmadan LLM iç doğrulaması)** | Ek kaynak gerekmez, ucuz | Citation doğrulama tek başına %68'e kadar (kaynak 8); kanıt kısa snippet ise yetersiz (kaynak 10); hallucine edilmiş "doğrulama" hallüsinasyonu gizler | Kanıtsız doğrulama = hallüsinasyonun 2. katmanı; disk kanıtı zorunluluğu (c.2) kaldırılamaz |
| 3 | **Her iddia için zorunlu otomatik oturum kilidi (aşırı-katı: etiket varsa anında kilit)** | Maksimum güvenlik hissi | Yanlış-pozitif üretkenliği kilitler; "etiket = kilit" yorgunluğu (§4.3 risk 1) — debate/üretim tıkanır | Kilidi yalnız **ihlal** (kaynaksız iddia/kanıtsız IMPLEMENTED/örtbas) tetikler; etiket tek başına kilit değildir (b. Yanlış-pozitif satırı) |
| 4 | **Yalnız doküman: etiket kuralı yazılır, protokol/skill yazılmaz** | En kısa ADR | §24.1 zaten SSOT'ta olduğu için tek başına etiket kuralı izole kalır; skill disk gerçeği (çelişki) kayda geçmez | Kapsam 3 parça kullanıcı onaylı; skill varlık bulgusu olmadan §14 kuralı yanlış anlaşılmaya açık |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Hallüsinasyon yakalama mekanizması yazılı:** her iddia ≥2 kaynak + IMPLEMENTED↔disk kanıtı + etiket → örtbas imkansızlaşır (§2.2c).
- **Protokol tek kaynakta hizalanır:** §24.1 ile birebir tablo → ajanlar ayrı ayrı "neden?" sormaz; adım sırası ve timeout'lar tek yerde (§2.2a).
- **"Zero Hallucination" dürüst konumlandırılır:** ölçülebilir hedef → yanlış vaat/yanlış-güven doğmaz (§1.3 Sonucu 1).
- **Skill gerçeği kayıtta:** `.claude/` aktif + `.opencode/` yalnız arşiv ayrımı yazıldı → §14 okuyucusu yanlış beklentiye girmez (§2.2e).
- **İhlal görünür ve geri alınabilir:** oturum kilidi + MO onayı + revert + log ERROR → hatalı iddia vault'ta yaşamaz (§2.2d; AGENTS.md §18).

### 4.2 Olumsuz Sonuçlar

- **Prosedürel sürtünme:** her iddianın 2 kaynak + disk kanıtı aranması yazım hızını düşürür (özellikle ADR/debate üretimi).
- **Etiket enflasyonu riski:** her şeye etiket takılması yanlış-pozitif etiketleri çoğaltır, metni kirletir (§4.3 risk 1).
- **Çift kayıt yükü:** etiket kalkışı ve kilit olayları ayrı log append'leri ister (append-only disiplini).
- **Skill-copy bağımlılığı:** `.claude/` ile `.opencode/` senkronu tutmazsa kullanım ortamına göre skill davranışı değişebilir (§2.2e).

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| Yanlış-pozitif `VERIFICATION REQUIRED` (her şeye etiket) → yorgunluk/üretim yavaşlar | 3 (olası) | 3 (orta) | Etiket yalnız "bilinmeyen"e; kilit yalnız gerçek ihlale (b. Yanlış-pozitif satırı); MO triage eder, kural c.1-2 sınırlayıcı |
| Oturum kilidi → tıkanma (devam edememe) | 3 (olası) | 4 (yüksek) | **Fallback: MO devreye girer** — onay + revert + log ile kilidi açar (§2.2d madde 3, 6; AGENTS.md §18 #1, §10); tekrarlayan ihlal L2/L3'e eskale edilir |
| Örtbas baskısı (etiketi kaldırıp geçme isteği) | 2 (mümkün) | 4 (yüksek) | c.3 yasağı + kilit tetikleyicisi (örtbas = ihlal) + log ERROR denetimi |
| Skill senkronsuzluğu (`.claude/` vs `.opencode/`) | 3 (olası) | 2 (düşük) | §2.2e tablosu tek gerçeklik; `.opencode/`'daki arşiv kullanılmaz; §14 notu okunur |
| Kaynak şişirme (2-kaynak kuralı = şişirilmiş liste) | 2 (mümkün) | 2 (düşük) | Çapraz kaynak aynı iddiayı kapsamalı (web protokolü kalite listesi); sayı yerine kapsayıcılık denetimi |

### 4.4 Fallback (Zaruri İstisna Kapısı)

| # | Koşul | Onay |
|---|-------|------|
| 1 | Oturum kilidi 3 kez üst üste aynı "yanlış-pozitif" sebeple tetiklenmişse | MO |
| 2 | Kilidi açma işlemi = revert + log ERROR + etiketin yeniden değerlendirilmesi — **etiket kuralı (c.3 "açıksa kalır") FALLBACK'LE KALDIRILAMAZ** | MO + Vault Steward |
| 3 | Protokol adımları (§24.1) ve ≥2 kaynak standardı FALLBACK'LE DEĞİŞMEZ | Tech Lead (§7) |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | Bu ADR'yi `.ai/.decisions/accepted/` altına yaz (slug `ADR-005-ultrathink-protocol`) + `log.md` append ("ADR-005 yazıldı (debate PENDING)") + dizin satırı doğrula (`[[../index]]` §3 satırındaki `[[ADR-005-ultrathink-protocol]]` artık dosyaya ulaşır — slug eşleşmesi) | Vault Steward | 30 dk |
| 2 | Debate (`⏳ PENDING` — 3 tur / 20 persona hedefi, ADR-004 formatı) + Tech Lead (`⏳`) onayı: sonuç §7.1'e yazılır; şart varsa §5.1'e satır eklenir | Vault Steward + Debate + Tech Lead | 2 gün |
| 3 | `[[../../AGENTS.md]]` §14/§24 ile karşılıklı referans ekleme: §24.1 tablosunun altına "ADR-005 tescilli" notu + §14 4. satırın altına skill disk notu (`.claude/` aktif, `.opencode/` arşiv) — **AGENTS.md dokunulmazlığı olmayan dosya; satır edit ile ekleme, silme yok** | Vault Steward | 1 saat |
| 4 | `[[../../CLAUDE.md]]` guardrail çapraz kontrolü: Guardrail #16 (şablon zorunluluğu) ve hallüsinasyon/etiket guardrail'leriyle uyum — çelişki varsa DUR + MO | Vault Steward + Security | 1 saat |
| 5 | İlk saha denemesi: sonraki 3 ADR/doc yazımında §2.2c-d kapıları uygulanır; kilit/etiket olayları `log.md`'ye ERROR/info append ile raporlanır; sprint sonunda ölçüm (§2.2f metrikleri) | Tüm ajanlar + MO | 1 sprint |
| 6 | **Şart 1 (debate 3. tur):** Oturum kilidini yalnız **MO** kırar; MO devreye girme süresi **60 sn L2 timeout** ile sınırlıdır — süre aşımında eskalasyon §10.2 L2 zincirine geçer (`[[../../AGENTS.md]]` §10.2 "L2 timeout = 60 saniye" ile uyumlu; DevOps false-positive uyarısının çözümü) | MO | Kalıcı kural |
| 7 | **Şart 2 (debate 3. tur):** **Kritik iddia tanımı** — ≥2 kaynak eşiği yalnız kritik iddialara uygulanır: `IMPLEMENTED`, güvenlik (security) ve **karar değiştirici** iddialar; bağlam/idari iddialar için 1 kaynak yeterlidir (§2.2c.1'in debate yorumu — mevcut metin değişmez, Devil's Advocate maliyet uyarısının çözümü) | Tüm ajanlar | Kalıcı kural |
| 8 | **Şart 3 (debate 3. tur):** `/hallucination-control` skill'in **tek kanonik lokalizasyonu** `.claude/skills/hallucination-control/`'dir; `.opencode/skills/_archive-keep/hallucination-control/CLAUDE.md` yalnız arşiv kopyasıdır, kanonik DEĞİL (§2.2e tablosu + §6) | Vault Steward | Kalıcı kural |

### 5.2 Geri Dönüş Planı

Karar süreç karardır; geri dönüş yalnız **yeni ADR** ile olur (In-Place Refactoring yasağı — bu dosya frozen olmasa da keyfi düzenlenmez). Senaryolar: (1) **Protokol geri alınırsa** (adımlar fazla ağır bulunursa): §4.4 koşulları + yeni ADR; bu dosya `superseded by` ile bağlanır, `[[../../AGENTS.md]]` §24.1 SSOT olduğu için protokol kendisi bozulmadan kalır. (2) **Oturum kilidi kaldırılırsa:** yeni ADR şart — MO onayı + `log.md` ERROR kapanış satırı; etiket kuralı (c.3) ve ≥2 kaynak standardı yine de geçerli kalır (bunlar bu ADR'ye değil, web protokolüne + AGENTS.md §18'e dayanır). (3) **Skill klasörü silinirse/arşivlenirse:** §2.2e tablosu güncellenir, §14 disiplin maskesi olduğu için işlev (`[[../../AGENTS.md]]` §24.4 etiket akışı) etkilenmez. (4) **Veri/state kaybı yoktur** — bu ADR süreçtir, veri/kod değiştirmez. (5) Vault bozulursa standart kurtarma `git checkout` + son commit (`[[../../AGENTS.md]]` §17 #10).

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../../AGENTS.md]] | **Birincil kaynak:** §24.1 5-adım protokolü (birebir hizalı), §24.4 otomatik temizlik, §14 zorunlu skill tablosu (4. satır /hallucination-control), §18 Warnings (#3 etiket, #1 MO durdurma), §10.2 L2 timeout 60 sn (Şart 1 — §5.1 satır 6), §25.3 orkestrasyon kuralları (frozen/append-only) |
| [[../../CLAUDE.md]] | Ana sözleşme — 16 Hard Guardrail (özellikle #16 şablon zorunluluğu; §24.5 "template'e uygun mu" kontrolü) |
| [[../../log]] | Audit trail — kilit/etiket/ERROR satırları bu dosyaya append edilir (append-only) |
| [[../index]] | Karar dizini — bu ADR'nin kaydı §3 `[[ADR-005-ultrathink-protocol]]` (slug eşleşmesi ✅; dizin başlığı "Frozen" altında listeliyor — bu dosyanın kendi durumu **accepted/frozen YOK**; dizin başlık düzeltmesi ayrı işlem) |
| [[../CLAUDE.md]] | Karar alt registry kuralı (accepted/) |
| [[ADR-004-multi-domain-spa]] | Kardeş ADR (aynı klasör) — debate akışı formatı referansı; §7.1 tartışma kaydı bu ADR'de de kullanılır |
| [[ADR-001-vanilla-js-itcss]] | Frozen ön koşul (okunur, referanslanır — AGENTS.md §25.3 kural 2) |
| [[../../brain]] | Mimari karar özeti — ADR-005 özeti MO (vault-updater) tarafından türetilir |
| [[../../.templates/adr/adr-template]] | Bu ADR'nin 7 bölümlük şablonu (Guardrail #16) |
| [[../../ULTRA-THINKING]] | Ultra düşünme protokolü dokümanı — §24.1'in vault içi uzantısı |
| `.claude/skills/hallucination-control/SKILL.md` | Skill kaynağı (§2.2e — VAR, disk kanıtlı) · **tek kanonik lokalizasyon — Şart 3 (§5.1 satır 8); arşiv kopya kanonik değil** |
| `.opencode/skills/_archive-keep/hallucination-control/CLAUDE.md` | Skill arşivi (yalnız CLAUDE.md — aktif değil) |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | §1.3 web araştırma protokolü (diskte OKUNDU ✅ — 2+ çapraz kaynak kuralı) |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali (kullanıcı onaylı — "sıfırdan yaz") | 2026-09-24 | ✅ |
| Tech Lead | Debate 3. tur onayı (17/3/0 KABUL) | 2026-09-24 | ✅ |
| Arch Lead | ⏳ | — | ⏳ |

### 7.1 Tartışma Kaydı (Debate — kaynak alanı)

| Alan | Değer |
|------|-------|
| Biçim | ADR-004 formatı — 3 tur / 20 persona (kayıt: 2026-09-24) |
| Tur 1 — 20 persona | 14 kabul/neutral · 2 uyarı: **DevOps** (oturum kilidi false-positive'i pipeline'ı durdurabilir), **Devil's Advocate** (≥2 kaynak maliyeti) · **Critic:** kilidi MO devreye girme süresi sınırı şart |
| Tur 2 — İtiraz→çözüm | (1) Kilidi **MO kırar + 60 sn L2 timeout** (`[[../../AGENTS.md]]` §10 uyum) · (2) ≥2 kaynak yalnız **kritik iddia** (IMPLEMENTED / security / karar değiştirici) — bağlam için 1 kaynak yeter · (3) skill **tek kanonik lokalizasyon**: `.claude/skills/hallucination-control/` — arşiv kopya kanonik değil |
| Tur 3 — Oy | **17 kabul / 3 çekimser / 0 red → KABUL** |
| Şartlar | 3 şart §5.1 satır 6-8'e eklendi: (1) kilidi MO + 60 sn timeout · (2) kritik iddia ≥2 kaynak eşiği · (3) skill tek kanonik kaynak `.claude/skills/hallucination-control/` |
| Tech Lead | ✅ |
| Sonuç | **✅ TAMAMLANDI (3 tur / 20 persona, 17/3/0 KABUL)** |

---

*ADR-005 v1.0.0 | 2026-09-24 | Created — CoreMusic Vault (.decisions/accepted/ yeni seri; slug: `ultrathink-protocol`)*
*Authority: ADR-005 Karar Metni · Mode: Red Team · Human Mode · Truth Mode*
