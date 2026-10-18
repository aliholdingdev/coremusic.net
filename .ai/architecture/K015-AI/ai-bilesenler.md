---
title: "K015-AI — Bileşenler"
node_id: K015-AI/ai-bilesenler.md
node_type: subdomain
status: draft
parent_id: K015-AI/index.md
children: []
depends_on:
  - K007-data
  - K009-security
  - K011-application
  - K012-services
evidence:
  - "shared/src/AI/AIOrchestrator.php:26-38 (AGENT_MAP) · 2026-10-18"
  - "shared/src/AI/ToolCalling.php:28-40 (AGENT_PERMISSIONS) · 2026-10-18"
  - "shared/src/AI/PromptEngine.php:27-35 (INJECTION_PATTERNS) · 2026-10-18"
  - "shared/src/AI/MemorySystem.php:18-31 (L1/L2/L3 + HIERARCHY) · 2026-10-18"
  - "shared/src/AI/KnowledgeBase.php:48 · 2026-10-18"
  - ".ai/architecture/K015-AI/ai-master.md §B (karar envanteri) · 2026-10-18"
validation: "2026-10-18 — disk kanıtı ile çelişki yok; §F eksik kanıtlar bölümü korunuyor"
open_questions: "⚠️ runtime etiketi (PLANNED/IMPLEMENTED) yok · ⚠️ evaluation (K006) sınırı kademe-3 · ⚠️ MCP sürüm/star kanıtı yok"
group: mimari
---

# K015-AI — Bileşenler

## §1 ELI15

Bu dosya, CoreMusic'in yapay zeka katmanındaki **parçaları** (bileşenleri) tek tek
anlatır. Düşün: mutfakta bir şef (AIOrchestrator) var; gelen siparişi (kullanıcı
talebi) doğru usta arkadaşa (agent) dağıtıyor. Ustalardan biri elindeki aletleri
kullanıyor (ToolCalling), başka birinin yazdığı tehlikeli tarifleri (prompt
injection) almıyor (PromptEngine), iş bitince not alıyor (MemorySystem) ve
gerektikçe kitaplığa (KnowledgeBase) bakıyor. Her bileşenin ne yaptığı, nerede
durduğu ve kime güvendiği aşağıda tek tek yazılı.

## §2 Kapsam

- **Kapsamda:** `shared/src/AI/` altındaki bileşenler ve master karar envanterinde
  (`ai-master.md` §B) adı geçen bileşen düzeyindeki birimler.
- **Kapsamda değil:** Implementation ayrıntıları (→ `ai-kod-ornekleri.md`),
  tehdit modeli (→ `ai-riskler.md`), doğrulama planı (→ `ai-test-stratejisi.md`),
  dış kaynaklar (→ `ai-web-arastirma.md`).

## §3 Bileşen Envanteri

| # | Bileşen | Dosya | Sorumluluk | Kanıt (satır) |
|---|---------|-------|-----------|---------------|
| C-01 | AIOrchestrator | `shared/src/AI/AIOrchestrator.php` | Görevi doğru agent'a yönlendirir (AGENT_MAP) | `:26-38` |
| C-02 | ToolCalling | `shared/src/AI/ToolCalling.php` | Agent → araç izni kontrolü (AGENT_PERMISSIONS) | `:28-40` |
| C-03 | PromptEngine | `shared/src/AI/PromptEngine.php` | Prompt birleştirme + injection taraması | `:27-35` |
| C-04 | MemorySystem | `shared/src/AI/MemorySystem.php` | L1/L2/L3 bellek hiyerarşisi | `:18-31` |
| C-05 | KnowledgeBase | `shared/src/AI/KnowledgeBase.php` | Bilgi/kaynak erişimi | `:48` |
| C-06 | AuthenticationMiddleware (AI yüzeyi) | `shared/src/AI/AuthenticationMiddleware.php` | AI uçlarını 401 ile korur, fail-closed | `:22-24` |

## §4 C-01 AIOrchestrator

- **Sorumluluk:** Kullanıcı talebini agent'a yönlendirme. `AGENT_MAP` sabiti
  (`AIOrchestrator.php:26-38`) hangi görevin hangi agent'a gideceğini tanımlar.
- **Arayüz:** Sınıf içinde yönlendirme (dispatch) çağrısı — ayrıntı
  `ai-kod-ornekleri.md` §3.1.
- **Bağımlılık:** PromptEngine (istek hazırlanır), ToolCalling (agent seçimi sonrası
  izinler).
- **Durum:** DISK'TE MEVCUT (kod var) — ancak master'daki runtime etiketi
  PLANNED/IMPLEMENTED ayrımı yapılmamış (⚠️ açık soru, `ai-master.md` §G).

## §5 C-02 ToolCalling

- **Sorumluluk:** Agent'ın çağırabileceği araçları beyazliste ile sınırlar;
  `AGENT_PERMISSIONS` (`ToolCalling.php:28-40`) agent → araç izni matrisidir.
- **Güvenlik rolü:** Yetkisiz araç çağrısı reddi — bu, K009-security ile kesişir
  (depends_on). Red-ret (fail-closed) davranışının AI yüzeyindeki karşılığı
  `AuthenticationMiddleware.php:22-24` 401'idir.

## §6 C-03 PromptEngine

- **Sorumluluk:** Prompt şablonlarını birleştirir ve `INJECTION_PATTERNS`
  (`PromptEngine.php:27-35`) listesiyle tarama yapar.
- **Sınır:** Tarama modelin güvenlik katmanı DEĞİLDİR; model-güvenli olduğu
  varsayılmaz (K005 kök kuralı §16.4 → AI bağlamında `ai-master.md` §B.4).

## §7 C-04 MemorySystem

- **Sorumluluk:** `HIERARCHY` (`MemorySystem.php:25-31`) ve L1/L2/L3 katmanları
  (`:18-21`) ile kısa/uzun vadeli bellek ayrımı.
- **İlişki:** K007-data'ya bağımlı (kalıcı katman veri deposuna gider).

## §8 C-05 KnowledgeBase

- **Sorumluluk:** Kaynak/bilgi erişimi; `KnowledgeBase.php:48` içindeki yapı
  master `§B` kararlarıyla ilişkilidir.
- **İlişki:** RAG bağlamı → ADR-030 (RAG pipeline, PLANNED) — bu dosyada
  numara uydurulmaz, yalnızca master'da geçen gerçek numara referans verilir.

## §9 C-06 AuthenticationMiddleware (AI yüzeyi)

- **Sorumluluk:** AI uçlarını kimlik doğrulamasız bırakmaz; fail-closed: doğrulanamaz
  ise **401** (`AuthenticationMiddleware.php:22-24`).
- **Kesişim:** K009-security (depends_on) — auth zinciri orada tanımlıdır; bu dosya
  yalnız AI katmanındaki karşılığını belgeler.

## §10 Bağımlılık Haritası (bileşen düzeyi)

```
AIOrchestrator ──► PromptEngine ──► (model/LLM)
      │
      ├──► ToolCalling ──► araçlar (beyazliste)
      ├──► MemorySystem ──► K007-data (kalıcı katman)
      └──► KnowledgeBase ──► K007-data / kaynaklar
Tüm AI uçları ──► AuthenticationMiddleware (401 fail-closed) ──► K009-security
```

## §11 Master Envanterle Uyum (`ai-master.md` §B)

- Master 28 karar (§B) üzerinden yürür; bu dosya kararları **tekrar etmez**, yalnız
  bileşen eşlemesini verir. Karar listesi → `ai-kararlar.md`.
- §F (eksik kanıtlar) kapsamındaki boşluklar burada **doldurulmaz**; UNKNOWN olarak
  işaretlenir (zero-hallucination).

## §12 Bilinmeyenler (⚠️ VERIFICATION REQUIRED)

| # | Bilinmeyen | Etki | Not |
|---|-----------|------|-----|
| U-01 | Bileşenlerin runtime etiketi (PLANNED/IMPLEMENTED) | Orkestrasyon riski | `ai-master.md` §G açık soru |
| U-02 | evaluation bileşeni K006 sınırı (kademe-3) | Kapsam kayması | K006-data ile sınır kararı bekliyor |
| U-03 | MCP bileşeninin sürüm/yıldız kanıtı | Bağımlılık olgunluğu | web-arastirma §5'te işaretli |

## §13 İlgili Dosyalar

- `ai-master.md` — karar envanteri (§B), açık sorular (§G)
- `ai-kararlar.md` — karar kayıtları (bu paket)
- `ai-kod-ornekleri.md` — bileşen kod pencereleri
- `ai-dogrulama.md` — doğrulama zinciri
- `../K009-security/` · `../K007-data/` · `../K011-application/` · `../K012-services/`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
