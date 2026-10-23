---
title: "CoreMusic — CorePlayer PRD (Çekirdek Oynatıcı Gereksinim Belgesi)"
type: prd
category: frontend
version: 1.0.0
status: active
authority: "Derived — SSOT: [[../PRD.md]] (platform + aile indeksi) · [[../../PRD.md]] (js çekirdek) · bağ: [[CLAUDE.md]]"
updated: 2026-10-23
---

# assets.coremusic.net/js/coreplayer — PRD

**docType:** prd · **Klasör:** `js/coreplayer/` · **Yüzey:** #20 (aile indeksi §5) · **Sorumlu:** UI Designer (+ embedded iş birliği)

**Bağlı platform gereksinimleri:** PRD-PLT-001 · PRD-PLT-008

---

## 1. Amaç

Bu PRD, `js/coreplayer/` çekirdeğinin gereksinimlerini tanımlar: panelin ses oynatma çekirdeği — oynat/duraklat/atla, ilerleme, kuyruk ve **Offline-First** davranış (PRD-PLT-001: ağ kopsa oynatma ≥30sn kesintisiz, reconnect'ta sessiz eşitleme). DSP/ASIO çekirdeği (Neva Engine) farklı katmandır; bu PRD yalnız frontend oynatıcı sözleşmesini kapsar.

> **eli10 (basit):** Bu dosya, tarayıcıdaki oynatıcının ne yapması gerektiğini ve ağ kesintisinde nasıl dayanması gerektiğini söyler.
> **eli15 (detay):** Ayrı yazıldı çünkü oynatıcı kullanıcının duyduğu tek davranıştır; gereksinimi yoksa ağ dalgalanmasında müzik durur (PRD-PLT-001 ihlali) ve kuyruk tutarsızlaşır.

---

## 2. Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `js/coreplayer/**` (oynatıcı çekirdeği) | DSP/algoritma katmanı → Neva Engine (arşiv PRD) |
| Oynatma durumu + kuyruk + reconnect davranışı | Backend medya katalog → media PRD (#6) |
| `features/PlayerController` ilişkisi (#18) | Panel layout → home PRD (#5) |

- **Dokunulmaz:** frozen ADR metinleri · CSP.

> **eli10 (basit):** Kapsam tarayıcı oynatıcısı; ses algoritması ve katalog ayrı.
> **eli15 (detay):** Sınır, frontend oynatıcının DSP kodu taşımasını engeller — duyulan davranış burada, matematik orada.

---

## 3. Gereksinimler (PRD-PLY-001..004)

| ID | Gereksinim | Kabul Kriteri |
|----|-----------|----------------|
| PRD-PLY-001 | Offline-First: ağ kesintisinde oynatma ≥30sn kesintisiz (PRD-PLT-001) | Ağ kopsa playback durmaz; ölçüm kaydı |
| PRD-PLY-002 | Reconnect'ta sessiz eşitleme | Kullanıcı aksiyonu gerekmez; oturum/kuyruk korunur |
| PRD-PLY-003 | Oynatma durumu tek kaynak (state) | Çarpık UI state'i yok; EventBus ile yayılır |
| PRD-PLY-004 | Kuyruk işlemleri öngörülebilir (sıra/atla/geri) | Sıra bozulmaz; cihaz değiştirince durum korunur |

> **eli10 (basit):** Dört kural: kesintisiz oynatma, sessiz eşitleme, tek durum, güvenilir kuyruk.
> **eli15 (detay):** Kimlikler ayrı çünkü oynatıcı kusuru doğrudan kullanıcı deneyimi kaybıdır; denetlenebilir olmalıdır.

---

## 4. Kabul Kriteri

| # | Kapı | Ölçüt |
|---|------|-------|
| 1 | Offline | PRD-PLY-001/002 — ≥30sn + sessiz eşitleme (ölçümlü) |
| 2 | Durum | PRD-PLY-003 — tek state kaynağı |
| 3 | Kuyruk | PRD-PLY-004 — sıralı işlemler |
| 4 | Kanıt | Süre/id kanıtsız yazılmaz → `⚠️ VERIFICATION REQUIRED` |

---

## 5. ADR Bağları

| Karar | ADR |
|-------|-----|
| Offline-First | PRD-PLT-001 (bağ) |
| Router deseni (panel içi gezinme) | ADR-021 · ADR-083 |
| Yeni ADR numarası | `.ai/.decisions/index.md` — next: **111** |

---

## 6. Risk

| # | Risk | Etki | Azaltma |
|---|------|------|---------|
| 1 | Ağ kesintisinde anlık durma | PRD-PLT-001 ihlali | PRD-PLY-001 + ölçüm |
| 2 | Çarpık oynatma durumu (UI vs gerçek) | Kullanıcı güvensizliği | PRD-PLY-003 |
| 3 | Reconnect'te elle müdahale ihtiyacı | Kötü UX | PRD-PLY-002 |

---

## 7. Sonraki Adım

1. #5 (home PRD) ve #18 (js çekirdek) ile `PlayerController` ilişkisi hizalanır.
2. ≥30sn ölçümü: ölçüm yoksa iddia `⚠️` ile taşınır (PRD-PLT-011).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
