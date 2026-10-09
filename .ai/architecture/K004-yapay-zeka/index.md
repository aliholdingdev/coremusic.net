---
type: architecture
category: layer
title: "K004 — Yapay Zeka"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT
---

# K004 — Yapay Zeka

## §1 Kimlik
- Katman: K004 · Alan: **A0** (K0-K5 aralığı — AGENTS §5 etiketi "Altyapı/Donanım"; kapsam uyuşmazlığı §6).
- Kapsam: AI stratejisi — çekirdek kararlar, prompt engineering, RAG pipeline, AI veri şeması.

## §2 Sorumluluk
1. AI stratejisi çekirdeği (ADR-030) — model/ürün karar çerçevesi.
2. System prompt engineering standardı (ADR-035) + multi-project prompt maker (ADR-036).
3. Startup prompt loader (ADR-049).
4. RAG pipeline tasarımı (ADR-030 — PLANNED).
5. AI domain veri şeması (ADR-075) — K005 üzerinden.

## §3 Bağlantılar (Dependency Rule)
- **Alt:** K005 (AI DB şeması) → K000 — plan §2-1.
- **Üst (çağıran):** K008 servislerin `ai` modülü → K010 → K009 (plan §5.1).

## §4 ADR Bağlantıları
| ADR | Başlık |
|---|---|
| ADR-030 | AI Strategy Core |
| ADR-035 | System Prompt Engineering |
| ADR-036 | Multi-Project Prompt Maker |
| ADR-049 | Startup Prompt Loader |
| ADR-075 | AI DB Schema |

## §5 Durum
**PLANNED** — ADR'ler active/frozen; RAG pipeline PLANNED (ADR-030, AGENTS §9). Uygulama dosyası kanıtı bu görevde yok → **UNKNOWN**.

## §6 Risk / Not
- ⚠️ VERIFICATION REQUIRED: A0 alan etiketi "Altyapı/Donanım"; K004 bu aralığa sayısal olarak düşer — matris düzeltmesi Vault Steward onayı ister.
- Agent registry §4'te AI'dan sorumlu agent yok → routing **UNKNOWN** (geçici sahip: MO).
