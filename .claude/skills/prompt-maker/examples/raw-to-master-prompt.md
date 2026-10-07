---
title: "prompt-maker Example — Ham İstekten 8 Bölüm Çıktı (Questions Modu)"
type: example
category: skills
version: 1.0.0
updated: 2026-10-07
---

# Örnek: Ham Prompt → Questions Modu → 8 Bölüm Çıktı

Bu örnek, prompt-maker'ın **§ PROMPT-MAKER ZORUNLU AKIŞ** bölümünü (A. Raw Prompt,
B. Çıktı Şablonu, C. Kapılar, D. Akış Zinciri) gerçek bir istekte nasıl uyguladığını gösterir.

---

## 1. Girdi (kullanıcı — ham/raw)

```text
"kullanıcı: müzik indirme servisine kuyruk ekle, youtube'dan ama güvenli olsun"
```

**Kural (§A.1):** HEMEN işleme alma — önce **questions modu** çalışır.

---

## 2. P0/P1/P2 Sorular (proje bağlamına göre — generic DEĞİL)

| Seviye | Soru | Neden |
|--------|------|-------|
| **P0** (engelleyici) | Kuyruk kalıcı mı (DB) mı, bellek içi mi? — CoreMusic'te `coremusic_download` DB'si var mı, yoksa yeni tablo mu? | Mimariyi belirler |
| **P0** | Aynı kaynaktan 2. kez indirme: yasak mı, cache mi? (`coremusic_download` cache alanı §18'de var) | Davranışı belirler |
| **P1** (kalite) | Öncelik kuyruğu mu (FIFO)? Paralel indirme limiti? | Performans |
| **P1** | Anti-ban davranışı (rate limit, user-agent) ADR-028 kapsamı mı? | Güvenlik |
| **P2** (tercih) | UI: kuyruk görünümü ayrı sayfa mı, modal mı? | UX |

**Durdurma kuralı:** P0'lar cevaplanmadan final ÜRETİLMEZ (Completion Gate).

*(Kullanıcı cevapları varsayım — bu örnekte örnek cevaplar: "DB'de kalsın, ADR-028 kapsamı,
FIFO + 2 paralel, modal". Gerçek kullanımda gerçek cevaplar beklenir.)*

---

## 3. Akış Zinciri (§D — adım adım)

```text
Kullanıcı Promptu: "kuyruk ekle youtube ama güvenli"
  → [1] Exploration Gate: .ai/ içinde indirme/kuyruk ara (ADR-026 download service,
        ADR-028 anti-ban, coremusic_download §18) — kod: download-service/ var mı?
  → [2] Exploration Context: ilgili 3-4 vault dosyası + download-service kodu saklanır
  → [3] Prompt Maker: P0/P1/P2 soruları (yukarıda) → cevaplar
  → [4] Intent Router: tech stack = Node.js/TS (download service) confidence boost
        + vault filtreleme (yalnız ilgili: ADR-026, ADR-028, §18)
  → [5] Instruction: yalnız ilgili vault dosyaları (50K token bütçe)
  → [6] System Prompt: exploration context + routing guidance
```

**Hard-coded model YASAK (§A.3):** sağlayıcı/model yazılmaz — o an seçili model kullanılır.

---

## 4. Çıktı (§ B — 8 bölüm, SIRA ZORUNLU)

```markdown
## 1. /eli10 Uzun Paragraf
[Ham promptun derin analizi — yapay zekaya adım adım anlatır gibi: kuyruk = sıralı
iş kuyruğu; "güvenli" = anti-ban (ADR-028) + rate limit; CoreMusic'te indirme zaten
`coremusic_download` DB'sinde queue/history/cache alanlarıyla tanımlı (§18) — yeni yapı
UYDURULMAZ, mevcut şema okunur]

## 2. İstenen Şey
- YouTube kaynaklı indirme için kalıcı kuyruk (DB), FIFO + max 2 paralel
- Anti-ban uyumu (ADR-028), rate limit
- UI: modal kuyruk görünümü

## 3. Vault Referansları
- `.ai/.decisions/accepted/ADR-026-download-service-architecture.md`
- `.ai/.decisions/accepted/ADR-028-anti-ban-system.md`
- `.ai/CLAUDE.md` §18 (coremusic_download: queue, history, cache, source APIs)

## 4. Proje Kod Referansları
- `download-service/` (kuyruk mevcut yapısı — [VERIFY] diskte okunacak)
- Frontend: ilgili panel (kapsam dışı sayılabilir — P2 cevabına bağlı)

## 5. Kullanıcı Kararları + Sorular + Cevaplar
| Soru | Cevap |
|------|-------|
| P0 kalıcılık | DB (coremusic_download) |
| P0 tekrar indirme | cache hit |
| P1 anti-ban | ADR-028 kapsamı |
| P1 paralellik | max 2 |
| P2 UI | modal |

## 6. Orijinal Prompt (aynısı)
"kullanıcı: müzik indirme servisine kuyruk ekle, youtube'dan ama güvenli olsun"

## 7. Görevlere Bölme (Task Breakdown)
1. download-service kuyruk şeması incele (disk okuma — mevcut yapı)
2. Gerekirse migration: queue tablosu alanları (mevcut şema ile çakışırsa DUR)
3. Worker: FIFO + max 2 paralel, ADR-028 anti-ban gate
4. API: kuyruk durumu/sıra/iptal endpoint (API-First: OpenAPI önce)
5. UI modal (mockup gate — Guardrail #11)
6. Test: kuyruk birim testi + anti-ban entegrasyonu

## 8. Kapanış
✅ Prompt Cevaplarla İşlendi | Dil: Türkçe | Görev Sayısı: 6 | Cevap: 5
```

---

## 5. Kapılar (§C)

- Çıktı **ONAYLANIR → SONRA** session başlar. Onaysız session start **YASAK**.
- Vault'ta çelişki görürse (ör. ADR-028 ≠ anti-ban isteği) → **DUR** + kullanıcıya sor
  (Guardrail #12 Contradiction Gate).

---

## Öğrenilecek dersler

1. **Soru-cevap olmadan final yok** — P0 engelleyiciler ilk.
2. **Vault önce** — Exploration Gate olmadan intent router çalışmaz (§D).
3. **Sıra değişmez** — 8 bölümün sırası bağlayıcı; orijinal prompt eklenmeden eksik teslim.
4. **Sağlayıcı yazılmaz** — model bağımsızlık (free modellerde de çalışır).
