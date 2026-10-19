---
title: "K015-AI — Kararlar"
node_id: K015-AI/ai-kararlar.md
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
  - ".ai/architecture/K015-AI/ai-master.md §B (28 karar envanteri) · 2026-10-18"
  - ".ai/.decisions/index.md (ADR numaraları — uydurma yasak) · 2026-10-18"
  - "shared/src/AI/AIOrchestrator.php:26-38 · 2026-10-18"
  - "shared/src/AI/PromptEngine.php:27-35 · 2026-10-18"
validation: "2026-10-18 — ADR numaraları yalnız .ai/.decisions/index.md'den; enumere edilmeyenlere 'ADR numarası atanacak'"
open_questions: "⚠️ evaluation (K006) sınırı kademe-3 kararı açık · ⚠️ MCP bileşen kararı ADR bekliyor"
group: mimari
---

# K015-AI — Kararlar

## §1 ELI15

Burada projenin yapay zeka ile ilgili **kararları** var: "şunu şu şekilde yapıyoruz,
çünkü şu şöyle". Her karar üç parçadır: ne oldu (durum), neden (gerekçe), sonucu ne
(sonuç). ADR numarası varsa gerçek numaradır (`.ai/.decisions/index.md`'den okunur);
henüz numara almamış kararlara **"ADR numarası atanacak"** yazarız — numara uydurmak
yasak, çünkü uydurma numara bir gün gerçek bir kararla çakışır.

## §2 Kapsam

- Bu dosya **K015-AI'ın** kararlarını kaydeder; master `ai-master.md` §B'deki 28
  kararın bu paketteki kayıtlıdır.
- Başka K'ların kararlarına burada dokunulmaz (beyazliste kuralı).

## §3 Karar Kayıt Şekli

Her kayıt: **Karar** (tek cümle) · **Durum** (Implemented / Planned / ADR bekliyor) ·
**Gerekçe** (disk kanıtı) · **Sonuç** (etki) · **ADR** (gerçek numara ya da
"ADR numarası atanacak").

## §4 Kararlar

### D-01 — Agent yönlendirme merkezi AIOrchestrator'dadır
- **Karar:** Görevler sabit `AGENT_MAP` ile agent'a dağıtılır.
- **Durum:** Implemented (kod diskte).
- **Gerekçe:** `AIOrchestrator.php:26-38` — harita kodda sabitlenmiş.
- **Sonuç:** Yeni agent eklemek haritayı güncelleme gerektirir; gizli davranış yok.
- **ADR:** ADR numarası atanacak (master §B'de karşılığı var, diskte ADR dosyası yok).

### D-02 — Agent → araç erişimi beyazlisteyle sınırlandırılır
- **Karar:** Her agent yalnız izinli araçları çağırır (`AGENT_PERMISSIONS`).
- **Durum:** Implemented.
- **Gerekçe:** `ToolCalling.php:28-40`.
- **Sonuç:** Yetki genişlemesi default değil, açıkça beyazliste değişikliği ister.
- **ADR:** ADR numarası atanacak.

### D-03 — Prompt injection taraması PromptEngine'de merkezidir
- **Karar:** Girdi taraması `INJECTION_PATTERNS` ile yapılır.
- **Durum:** Implemented.
- **Gerekçe:** `PromptEngine.php:27-35`.
- **Sonuç:** Tarama savunma katmanıdır; model "güvenli" sayılmaz (K005 §16.4 ruhu).
- **ADR:** ADR numarası atanacak.

### D-04 — AI yüzeyi fail-closed authentication (401)
- **Karar:** Doğrulanamayan AI isteği 401 ile reddedilir; fail-open yok.
- **Durum:** Implemented.
- **Gerekçe:** `AuthenticationMiddleware.php:22-24`.
- **Sonuç:** K009-security ile uyum; güvenlik testi bu davranışı doğrular
  (`ai-test-stratejisi.md` §5).
- **ADR:** ADR numarası atanacak.

### D-05 — Bellek L1/L2/L3 hiyerarşisidir
- **Karar:** Kısa/uzun vadeli bellek katmanlanır (`HIERARCHY`).
- **Durum:** Implemented.
- **Gerekçe:** `MemorySystem.php:18-31`.
- **Sonuç:** Kalıcı katman K007-data'ya bağımlıdır (depends_on).
- **ADR:** ADR numarası atanacak.

### D-06 — RAG pipeline PLANNED'dir (ADR-030)
- **Karar:** Retrieval-augmented generation hattı planlı, henüz tam işlevsel değil.
- **Durum:** Planned.
- **Gerekçe:** ADR-030 envanterde "RAG pipeline PLANNED" olarak geçer
  (`.ai/.decisions/index.md`).
- **Sonuç:** KnowledgeBase erişimi bu hat hazır olana kadar sınırlı kalır.
- **ADR:** **ADR-030** (gerçek numara, envanterden).

### D-07 — evaluation sınırı kademe-3'te (K006-data)
- **Karar:** Model değerlendirme yüzeyi K006 ile K015 arasında kademe-3'te duruyor.
- **Durum:** Açık karar (henüz çözülmedi).
- **Gerekçe:** `ai-master.md` §G açık sorusu.
- **Sonuç:** Kapsam sınırı netleşene kadar evaluation bileşeni bu pakette
  belgelenmez.
- **ADR:** ADR numarası atanacak (karar bekliyor).

### D-08 — MCP entegrasyonu kanıt bekliyor
- **Karar:** MCP (Model Context Protocol) bileşeni kullanılacak; sürüm/yıldız kanıtı yok.
- **Durum:** ADR bekliyor.
- **Gerekçe:** `ai-master.md` §G; sürüm kanıtı `ai-web-arastirma.md` §5'te işaretli.
- **Sonuç:** Bağımlılık kararı doğrulanmadan uygulanmaz (zero-hallucination).
- **ADR:** ADR numarası atanacak.

## §5 ADR Durum Özeti

| Karar | ADR | Durum |
|-------|-----|-------|
| D-01 … D-05, D-07, D-08 | ADR numarası atanacak | envanterde, dosya yok |
| D-06 | ADR-030 | Planned (envanter kanıtlı) |

> **Not (zero-hallucination):** `.ai/.decisions/index.md` grep'i bu oturumda yalnız
> ADR-083/087/088'i "NO FILE on disk" olarak döndürdü; K015 kararlarının numaraları
> bu yüzden **uydurulmadı**. Yeni ADR açıldığında D-01…D-08'e numara işlenir.

## §6 Karar ↔ Bileşen Eşlemesi

| Karar | Bileşen (`ai-bilesenler.md`) |
|-------|------------------------------|
| D-01 | C-01 AIOrchestrator |
| D-02 | C-02 ToolCalling |
| D-03 | C-03 PromptEngine |
| D-04 | C-06 AuthenticationMiddleware |
| D-05 | C-04 MemorySystem |
| D-06 | C-05 KnowledgeBase |

## §7 Bilinmeyenler (⚠️ VERIFICATION REQUIRED)

- ⚠️ D-07 (evaluation sınırı) kademe-3 kararı **açık** — sonuçsuz kapatılamaz.
- ⚠️ D-08 MCP sürüm/yıldız kanıtı **yok** — `ai-web-arastirma.md` §5.
- ⚠️ D-01…D-05 için envanter karşılığı var ama diskte ADR dosyası **yok**;
  numara ataması Vault Steward yetkisindedir.

## §8 İlgili Dosyalar

- `ai-master.md` §B — kaynak karar envanteri (28 madde)
- `ai-bilesenler.md` — kararların bağlandığı bileşenler
- `ai-riskler.md` — karar riskleri
- `../../.decisions/index.md` — gerçek ADR numaralarının tek kaynağı

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
