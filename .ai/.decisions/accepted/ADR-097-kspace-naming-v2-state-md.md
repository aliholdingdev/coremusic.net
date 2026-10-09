---
reference_doc: .ai/.templates/adr/adr-template.md (v2.0.2 — Guardrail #16)
title: "CoreMusic — K-space Naming v2 (Sıra Eki, Tekrar Yok) + Duruma Göre md Seti + 6.000 Katman Sayımı"
type: adr
category: decisions
date: 2026-10-09
updated: 2026-10-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/.decisions/accepted/ADR-096-kspace-5000-boundary-model.md · .ai/architecture/rules.md"
---

# CoreMusic — K-space Naming v2 + Duruma Göre md Seti + 6.000 Katman Sayımı

**Durum:** active (Draft → Active · Kullanıcı onayı "devam" 2026-10-09)
**Tarih:** 2026-10-09
**Karar Veren:** Bayram Ali / Vault Steward (tek onay mercii — F1 §15/I3)
**İlgili ADR'ler:** [[accepted/ADR-096-kspace-5000-boundary-model]] · [[accepted/ADR-042-vault-restructuring-2026-08-03]]

---

## 1. Bağlam (Context)

ADR-096 K-space'i K000–K5999 (6.000 katman) olarak tanımladı; master prompt (F1) v2.6.0'a revize edildi. Üretim öncesi üç yapısal sorun tespit edildi:

1. **Dizin adı tekrarı:** Eski plan `K121-yayin-canli-ses/` gibi adları 20 kez üretiyordu (157 bant × aynı iç ek) → benzersizlik ihlali.
2. **Sabit "5 md" varsayımı:** Her katmana 5 md dayatması durum gerçeğiyle uyuşmuyor (mevcut 21 katmanın 20'sinde yalnız `index.md` var).
3. **Sayım tutmazlığı:** Plan 404 girdi diyordu; tam ölçek 5.041 dizin / 5.089 md'dir. Ayrıca `.ai/architecture/` kökünde iddia edilen 4 dosyadan 3'ü diskte yok (`00-kspace-anayasa.md` · `00-master-index.md` · `context.md`).

Karar olmadan P3 (dizin üretimi) kapısı ADR'siz mimari üretim yasağına (F1 H08) takılır.

## 2. Karar (Decision)

### 2.1 Dizin ad kuralı v2 — tekrar yok

```
K{NNN}-{band-türkçe-ek}-{iç-sıra 01..40}/
```

- `NNN` = katman no (K000–K5999, 3 hane).
- `band-türkçe-ek` = EK A bant başlığından ASCII slug (ı→i, ş→s, ğ→g, ü→u, ö→o, ç→c).
- `iç-sıra` = bant içi programatik sıra: `K - band_start + 1` → `01`..`40` (20'lik bantlarda `01`..`20`).
- Örnek: `K1387-guvenlik-katmani-07/` · `K1020-otomotiv-derinligi-40/`.
- **Yasak (Y10):** sıra eksiksiz eski biçim (`K121-yayin-canli-ses/`) ve `k0-…` biçimi bu kök için kullanılmaz.

### 2.2 Duruma göre md seti

| Durum | Kapsam | md seti |
|---|---|---|
| CURRENT | K000 (1) | 5 md |
| CURRENT | K001–K020 (20) | 3 md: `index.md` · `kimlik-karti.md` · `bagimlilik-sinir.md` |
| PLANNED | K121–K1020 (900) | 1 md: `index.md` |
| PROPOSED | K1021–K5140 (4.120) | 1 md: `index.md` (ad onayı olmadan üretilmez — Y5/Y9) |
| GAP | K021–K120 (100) | **0 md · dizin YOK** (Y4 — ad bile yazılmaz) |
| FUTURE | K5141–K5500 (360) | **0 md · dizin YOK** |
| RESERVED | K5501–K5999 (499) | **0 md · dizin YOK** |

### 2.3 Sayım (kabul formülü)

```
toplam katman   = 6.000 (K000–K5999)
hedef dizin     = 21 mevcut + 900 PLANNED + 4.120 PROPOSED = 5.041
hedef md        = 4 kök + 5.041 index.md + 44 CURRENT derin = 5.089
P2 tamamlanırsa = +100 dizin / +100 md → 5.141 / 5.189
```

### 2.4 Bant kaynakları ve durum spliti

- Bant 001–157 = F1 EK A §A.0 (879 `#### KNNN` başlığından türetildi).
- Bant 001–032 (K121–K1020) = `[PLANNED]` — EK A'da `####` başlıkları **var**.
- Bant 033–135 (K1021–K5140) = `[PROPOSED]` — yalnız bant adı var, katman başlığı **yok**.
- Bant 136–144 = `[FUTURE]` · bant 145–157 = `[RESERVED]`.
- K021–K120 = `[GAP]` (Y4): EK A'da kaynak yok → ad uydurulmaz.

### 2.5 Üretim kapıları (P0–P6)

P0 (3 kayıp kök dosya kurtarma + süzme) → P1 (bu ADR) → P2 (GAP doldurma) → P3 (900 PLANNED dizin) → P4 (4.120 PROPOSED dizin, onaylı) → P5 (sayım: 5.045 girdi · 5.089 md) → P6 (+100).

Tam ağacın kanıtı (10.538 satır · 5.020 dizin benzersiz doğrulandı): `C:\Users\MARHAN\.opencode\plan\coremusic-architecture-tree-list.md`

## 3. Sonuçlar (Consequences)

**Pozitif:**
- Dizin adları 5.041/5.041 benzersiz — tekrar deseni kalktı.
- Sayım disk ölçümüyle birebir tutuyor; P5 kapısı gercekci.
- GAP/FUTURE/RESERVED (959 katman) sahte dosyadan korunuyor (zero-hallucination).

**Negatif / risk:**
- 4.120 PROPOSED adın tamamı kullanıcı onayı bekliyor (kapı 4) — tek seferde büyük onay.
- E.4 tablosundaki 379'luk eski sayım artık geçersiz; prompt içi tüm sayaçlar v2.6.0 ile hizalandı.

**Yasaklar (prompt E.7):** Y4 (GAP adı) · Y5 (onaysız PROPOSED) · Y9 (durum-md seti ihlali) · Y10 (ad tekrarı).

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-09
**Mode:** Red Team · Human Mode · Truth Mode
