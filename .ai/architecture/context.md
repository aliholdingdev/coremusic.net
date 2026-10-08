---
title: "CoreMusic — Architecture Context"
type: docs
category: architecture
docType: context
date: 2026-10-08
updated: 2026-10-08
version: 2.0.0
status: active
authority: "Derived: .ai/architecture/rules.md (kurallar) + 00-master-index.md (giriş) — bu dosya hot-memory projection, SSOT değildir"
tier: 3
domain: architecture
ssot: false
risk: medium
owner: "MO"
depends-on: [".ai/architecture/rules.md", ".ai/architecture/00-master-index.md"]
---

# CoreMusic — Architecture Context

**Zorunlu Bağlantılar:** [[.templates/index]] · [[../CLAUDE.md]] · [[../AGENTS.md]] · [[architecture/rules]] · [[architecture/00-master-index]]

> **Yükleme:** Bu dosya hot-memory sınıfındadır — mimari görevlerde P1'de okunur (bkz. §3.4 boot sırası).
> **Bütçe:** ≤660 satır (validator `budget-check`). **SSOT değildir:** kurallar [[architecture/rules]] · K-space [[architecture/00-kspace-anayasa]] · giriş [[architecture/00-master-index]].

---

## §1 Amaç

`.ai/architecture/` altındaki **K-Space V2** mimari rejiminin (K000→K5999 · 9 bant · boundary model · 5000+ hedef) hot-memory özeti: bu klasör ne işe yarar, hangi dosyalar var (ölçümlü), boot sırası nedir ve en güncel sayılar neler — sorularının AI analiz hızını envanter doğruluğuyla beslemesi içindir. Bu dosya yalnız taşır; kural/karar üretmez.

| İlgili karar / kaynak | Tarih | Rol |
|---|---|---|
| [[decisions/accepted/ADR-096-kspace-5000-boundary-model]] | 2026-10-08 | Kurucu karar (K-space, format, kapılar, eski rejim terki) |
| F1 arşivi → `.ai/prompts/2026-10-08-master-prompt-v2.2.0-f1.md` | 2026-10-08 | Spec v2.2.0 (15 perde + EK A/B/C) |
| F2 arşivi → `.ai/prompts/2026-10-08-enterprise-arch-f2.md` | 2026-10-08 | Spec 50 bölüm (boundary prensipleri · §42 multi-MD) |
| 12 kullanıcı kararı (plan v2.0 onayı) | 2026-10-08 | Karar zinciri (ADR-096 §2 özü) |

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Mimari kontrol-plane envanteri (ölçümlü) + boot sırası + çelişki kuralı | Kural metni → [[architecture/rules]] (SSOT) |
| K-space bant kataloğu ÖZETİ (pointer) | K-space içeriği/kartlar → `00-kspace-anayasa.md` |
| Sayılar = disk ölçümü (tarih damgalı) | Kod dosyaları · ADR metinleri · şablon listesi |
| Vault kök ↔ architecture ilişkisi | Agent routing → [[../AGENTS.md]] |

- **Ön koşul:** envanter sayıları diskten ölçülmüştür; ölçülmeyen `UNKNOWN` (uydurulmaz).
- **Kullanıcılar:** mimari üretim yapan tüm agent'lar (P1 okuma) + insan + validator gate'leri.

## §3 Mimari

### §3.1 Kontrol-Plane Dosya Envanteri (2026-10-08 ölçümü)

| Dosya | Satır | Sürüm | Rol |
|-------|------:|-------|-----|
| `00-master-index.md` | 134 | 2.0.0 | Giriş navigasyonu + sürüm SSOT (bu yazım, transfer sonrası ölçülecek) |
| `00-kspace-anayasa.md` | 5.857 | 1.0.0 | EK A kopyası — 9 bant + 21 foundation kartı (K-space SSOT) |
| `rules.md` | 174 | 2.0.0 | Kural SSOT (ADR-096'dan türetildi — R1-R20) |
| `context.md` | (bu dosya) | 2.0.0 | Hot memory — ≤660 bütçe |
| `00-final-rapor.md` | — | — | §10 28-başlık rapor — **HENÜZ YOK** (R4, ≥5000 satır hedefi) |

> **eli10 (basit):** Bu dosyalar mimarinin beyni: hangi katman var, kurallar ne, kapılar nerede — hepsi burada başlar.
> **eli15 (detay):** master-index sizi doğru dosyaya götürür, kspace-anayasa K-space'in kendisini (bant/kart) tutar, rules neyin yapılabileceğini söyler, context (bu dosya) hızlı bağlamı verir; final rapor kapı 10'da eklenir. Bunlar olmazsa her oturum vault yeniden keşfedilir, kapılar tutarsız geçer.

### §3.2 Alt Dizin Envanteri (ölçümlü)

| Dizin | Dosya sayısı | Rol |
|-------|-------------:|-----|
| `.ai/architecture/` (kök md) | 2 (+1 bu yazım +1 rapor R4) | kontrol-plane |
| `.ai/prompts/` | 3 (1 eski + F1 + F2) | spec/çalıştırma promptu arşivi |
| `.ai/SESSIONS/` | 1 | keşif notları (On Kapı 3 çıktısı) |
| `.ai/.decisions/accepted/` | ADR-096 dahil (index: accepted 73 · rejected 7 · FM) | karar defteri |
| `.ai/architecture/K000-…/` katman dizinleri | **0** | R3 üretim hedefi (21 foundation + bant 2-9) |

> **eli10 (basit):** Klasörler işlevsel: spec'ler prompts'ta, keşif SESSIONS'ta, kararlar .decisions'ta, mimari kökte.
> **eli15 (detay):** Mimari üretim (KNNN- dizinleri) henüz başlamadı — bu bilerek eksik: kapı disiplini (R11) R2 iskelet + R3 bant üretimi olmadan içerik istemez. Sayılar her faz sonunda tazelenir.

### §3.3 Vault Kök ↔ Mimari İlişkisi

| Kök dosya | İlişki (5 sütun: Ne için / Neyden / Neden var / Ne zaman) |
|-----------|----------------|
| `.ai/CLAUDE.md` | Kural çerçevesi / anayasa / mimari üst otorite / anayasa değişince |
| `.ai/AGENTS.md` | Agent routing+ownership / registry / sahiplik ataması / agent eklenince |
| `.ai/WORKFLOW.md` | Süreç / süreç SSOT / kapıların genel bağlamı / süreç değişince |
| `.ai/brain.md` | ADR özeti / kararlar / ADR-096 özeti buraya (R4 senkronu) / yeni ADR'de |
| `.ai/index.md` | Vault katalog / master katalog / mimari satırı / yeni kök bölümde |
| `.ai/log.md` | Audit trail / olaylar / append-only kapı kayıtları / her faz sonu |

### §3.4 Boot Okuma Sırası

```text
P0 (her oturum):  .ai/CLAUDE.md + kök boot (AGENTS/WORKFLOW)
P1 (mimari görev): architecture/context.md (bu dosya) → 00-master-index.md
P2 (K-space/iş):  00-kspace-anayasa.md → rules.md → ilgili ADR-096
P3 (detay):       KNNN-<ad>/index.md → inventory/*-partNN.md
```

### §3.5 İçerik Neden Dosyalara Ayrıldı?

| Neden | Açıklama |
|-------|----------|
| Bütçe | Tek dosya 660 satırı aşar; hot-memory ayrı kalır |
| SSOT | Kural/karar/giriş ayrı sahiplerde (R7) — tekrar yasak |
| Tembellik değil optimizasyon | P0-P3 ile ilgisiz katman yüklenmez (token bütçesi) |
| Kapı uyumu | Validator her dosyayı ayrı denetler (fm/id/link) |
| Yerel edit | Bir dosyanın değişimi bütünü kirletmez (In-Place) |

## §4 Kurallar

1. **Guardrail özeti:** Zero-Hallucination (sayı = ölçüm) · Vault-First · In-Place · template (Guardrail #16) · commit = insan onayı (H6).
2. **Çelişki kuralı: vault ↔ disk → disk kazanır** + `⚠️` işaretlenir (SSOT korunur).
3. Bu dosya **SSOT değil** — kural isteği [[architecture/rules]], K-space isteği `00-kspace-anayasa`, karar isteği ADR-096'a gider.
4. Sayı yalnız `wc -l`/`ls`/`grep` ölçümüyle yazılır; hedef ile kanıt ayrı satırda (H10).

## §5 Workflow (envanter güncelleme)

```text
ÖLÇ (wc -l / ls / grep / glob) → YAZ (vault-utf8-writer) → validate --check exit 0
→ 00-master-index §3.2 sayım satırını tazele → log.md append → (faz sonu) brain/index senkron
```

## §6 Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | FM | 13 alan + `docType: context` |
| 2 | Bölüm | §1-§7 sıra |
| 3 | Placeholder | `{{` = 0 |
| 4 | Sayılar | Her sayı ölçümle veya `UNKNOWN` |
| 5 | eli10/eli15 | §3.1-§3.2 bloklarında, format birebir |
| 6 | Çelişki kuralı | §4'te "disk kazanır" var |
| 7 | Boot | §3.4 sırası var |
| 8 | Wiki-link | Hedefler diskte (master-index/context/rules/kspace-anayasa + ADR-096) |
| 9 | Bütçe | ≤660 satır |
| 10 | Gate | `validate.mjs --check` exit 0 |

## §7 Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Şablon (Guardrail #16) | [[.templates/documentation/context-md-template]] | Bu dosyanın iskeleti |
| Bağlayıcı ortak iskelet | `frontend/context-template` | 4用途 §1-§7 + eli10/eli15 |
| Kural SSOT | [[architecture/rules]] | Kuralların kaynağı |
| Giriş | [[architecture/00-master-index]] | Navigasyon + sürüm |
| K-space | [[architecture/00-kspace-anayasa]] | Bant/kart içeriği |
| Kurucu karar | [[decisions/accepted/ADR-096-kspace-5000-boundary-model]] | Rejim kararı |
| Anayasa | [[../CLAUDE.md]] | Guardrail #3 / #16 |
| Validator | `.ai/scripts/validate.mjs` | 11 check + budget (≤660) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-08
**Mode:** Red Team · Human Mode · Truth Mode