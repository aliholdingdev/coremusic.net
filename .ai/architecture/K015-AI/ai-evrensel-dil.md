---
title: "K015-AI — Evrensel Dil"
node_id: K015-AI/ai-evrensel-dil.md
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
  - "shared/src/AI/*.php terim karşılıkları (AIOrchestrator/ToolCalling/PromptEngine/MemorySystem) · 2026-10-18"
  - ".ai/architecture/K015-AI/ai-master.md §B (karar dili) · 2026-10-18"
validation: "2026-10-18 — ≥15 terim · EN/TR karşılıkları kod ile birebir; resmi İngilizce karşılık yoksa işaretli"
open_questions: "⚠️ K006 ile ortak terimlerin tekilleşmesi (kademe-3) bekliyor"
group: mimari
---

# K015-AI — Evrensel Dil

## §1 ELI15

Bu dosya, AI katmanında kullandığımız kelimelerin **tek ve sabit anlamını**
sabitler. "Agent" dediğimizde TR'de ne, EN'de ne — herkes aynı şeyi anlasın diye
sözlük gibi çalışır. Terim bir kez tanımlanır; sonraki dosyalarda bu tablo geçerlidir.
Karşılık kodla çakışıyorsa kod (disk) kazanır.

## §2 Kapsam

- Yalnız K015-AI terimleri (bileşen + karar dili).
- Genel vault terimleri (`.ai/` genel sözlük) bu dosyayı **ezmez**; çelişki olursa
  genel sözlük + bu tablo birlikte okunur.

## §3 EN ↔ TR Terim Tablosu

| # | EN Terim | TR Karşılık | Tanım (kısa) | Kod karşılığı |
|---|----------|-------------|--------------|----------------|
| 1 | Agent | Ajan / Vekil | Görevi üstlenen otonim birim | `AGENT_MAP` değeri |
| 2 | Orchestrator | Orkestratör | Görevi doğru ajan'a dağıtan | `AIOrchestrator` |
| 3 | Task | Görev | Kullanıcı talebinin adlandırılmış biçimi | `dispatch(task)` |
| 4 | Tool | Araç | Ajan'ın çağırabildiği dış işlev | `AGENT_PERMISSIONS` |
| 5 | Tool calling | Araç çağrısı | Ajan → araç çalıştırma adımı | `ToolCalling` |
| 6 | Whitelist | Beyaz liste | Açıkça izin verilenlerin listesi | `AGENT_PERMISSIONS[]` |
| 7 | Permission | İzin | Ajan → araç yetkisi | `allows()` |
| 8 | Prompt | İstek / yönerge* | Modele giden hazırlanmış metin | `build_prompt` |
| 9 | Prompt injection | İstek enjeksiyonu | Girdiyle modeli yönlendirme saldırısı | `INJECTION_PATTERNS` |
| 10 | Sanitize | Arındırma | Şüpheli girdiyi temizleme/red | `sanitize()` |
| 11 | Fail-closed | Kapalı-hata (reddet) | Belirsizlikte reddet varsayılanı | 401 / `false` |
| 12 | Fail-open | Açık-hata (geç-bırak) | Belirsizlikte geç-bırak (**yasadışı** bu projede**) | — (yok) |
| 13 | Memory / Hierarchy | Bellek / Hiyerarşi | Katmanlı bellek yapısı | `MemorySystem::HIERARCHY` |
| 14 | Layer L1/L2/L3 | Katman L1/L2/L3 | Oturum / profil / arşiv | `L1`·`L2`·`L3` |
| 15 | Knowledge base | Bilgi tabanı | Kaynaklara erişim yüzeyi | `KnowledgeBase` |
| 16 | Retrieval (RAG) | Getirme (RAG) | Kaynak getirerek zenginleştirme | ADR-030 (PLANNED) |
| 17 | Authentication | Kimlik doğrulama | Ucun kimlik kapısı | `AuthenticationMiddleware` |
| 18 | Dispatch | Yönlendirme | Görevi ajana iletme | `dispatch()` |
| 19 | Evaluation | Değerlendirme | Model/çıktı sınama (K006 sınırı) | — (kademe-3, D-07) |
| 20 | Runtime tag | Çalışma etiketi | PLANNED / IMPLEMENTED rozeti | §G açık soru (R-08) |
| 21 | Orchestration | Orkestrasyon | Ajan/bileşen koordinasyonu | AIOrchestrator |
| 22 | Hallucination | Halüsinasyon | Kanıtsız/uydurma çıktı (vault için de geçerli) | zero-hallucination kuralı |

\* **Not:** "Prompt" için standart TR tek karşılığı yoktur; bu vault'ta **"istek
(yönerge)"** kullanılır — akışta karışmaması için tercih edilir (⚠️ genel vault
sözlüğüyle tekleştirme kademe-3, K006 ortak terim kararı bekliyor).
\** "Fail-open" bu projede **kabul edilemez** davranıştır (K005 kök kuralının
AI'daki karşılığı: `AuthenticationMiddleware.php:22-24` fail-closed).

## §4 Kullanım Kuralları

1. **TR metinlerde** ilk geçişte "ajan (agent)" gibi çift yazımla, sonrasında tek
   karşılıkla yazılır.
2. **Kod/identifier** adları asla çevrilmez: `AIOrchestrator` İngilizce kalır.
3. **Karşılık yoksa** (ör. evaluation ↔ K006 sınırı) terim tabloya alınır ama
   kararı işaretlenir — uydurma karşılık yazılmaz.
4. Çelişkide **disk (kod) kazanır**; tablo kodun davranışını anlatır, kodu
   değiştirmez.

## §5 Karar ↔ Terim Eşlemesi

| Karar | Kullandığı terim(ler) |
|-------|------------------------|
| D-01 | Agent · Dispatch · Orchestrator |
| D-02 | Whitelist · Permission · Tool calling |
| D-03 | Prompt · Prompt injection · Sanitize |
| D-04 | Authentication · Fail-closed |
| D-05 | Memory · Hierarchy · Layer L1/L2/L3 |
| D-06 | Retrieval (RAG) |
| D-07 | Evaluation |
| D-08 | Runtime tag (bilinmeyen) |

## §6 Bilinmeyenler (⚠️ VERIFICATION REQUIRED)

| # | Bilinmeyen | Not |
|---|-----------|-----|
| L-01 | "Prompt" için standart TR karşılık | teklik kararı yok (kullanım notu §3) |
| L-02 | K006 ile ortak terimlerin (evaluation vb.) tekilliği | kademe-3 kararı |
| L-03 | "Runtime tag" için standart TR | bu vault'a özgü işaret, resmi karşılık yok |

## §6.1 Örnek Cümle Kullanımları

| Bağlam | Yanlış | Doğru |
|--------|--------|-------|
| İlk geçiş | "AIOrchestrator agent'ı dağıtır" | "AIOrchestrator, görevi ajana (agent) dağıtır" |
| Karar metni | "prompt güvenlidir" | "tarama yapılır; model güvenli **sayılmaz**" |
| Auth | "hata olursa geç" | "belirsizlikte reddet (fail-closed)" |
| Bellek | "L2'ye yazıldı" | "profil katmanına (L2) yazıldı" |
| Kod | "Orchestator sınıfı" | `AIOrchestrator` (kod adı çevrilmez) |

## §6.2 Sözlük Bakım Kuralları

1. **Ekleme:** Yeni terim yalnız disk kanıtıyla (yeni dosya/yeni sembol) eklenir;
   fikirle eklenmez.
2. **Tartışma:** Karşılık polemiği §6 bilinmeyenlerine yazılır, tabloya
   tahminle girmez.
3. **Kaldırma:** Terim silinmez; karşılığı eskidiyse "kullanım dışı (eski: …)"
   olarak işaretlenir — bilgi kaybı yok.
4. **Çapraz K:** K006-yapay-zeka ile ortak terimler kademe-3 kararına kadar her
   iki dosyada da taşınır (çelişkide tekilleştirme kararı beklenir).

## §7 İlgili Dosyalar

- `ai-bilesenler.md` · `ai-kararlar.md` — terimlerin kullanıldığı dosyalar
- `../K006-yapay-zeka/` — ortak terimlerin kaynak K'si (kademe-3 sınır bekliyor)
- `../../CLAUDE.md` — genel vault dil kuralları

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
