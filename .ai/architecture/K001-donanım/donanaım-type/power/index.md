---
title: power — Tip Dizini (K001 donanaım-type)
type: mimari-index
node_id: K001-donanım/donanaım-type/power
node_type: architecture-index
status: proposed
parent_id: K001-donanım/donanaım-type
children:
  - power-kapsam
  - power-dogrulama
  - power-ornekler
depends_on: K001-donanım
evidence:
  - "K001-donanım/donanım-anayasa.md §2 (yalnız 3 bağlam: hdd·audio·ssd) — çatışma kaydı power-kapsam §0"
  - "K001-donanım/donanaım-type/donanaım-type-kapsam.md §2/A6, §7/3-T1, §12/M6 — çatışma kaydı power-kapsam §0"
  - "K002-electroncis/ (16 md) + .ai/.decisions/accepted/ADR-063, ADR-064 — sınır notu (yokluk kanıtı: varlık)"
validation: "⚠️ VERIFICATION REQUIRED — bu klasör Vault Steward onayıyla 2026-10-13 açıldı; anayasa/kapsam metni revizesi ONAY BEKLİYOR; Q-T2 yanıtlandı ama KAPANDI DEĞİL"
open_questions:
  - "VD-02: power tipsiz mi — 700 dosyada PSU/VRM/LDO/BMS/fuse=0 (kasıtlı-yok kararı bekliyor)"
  - "anayasa §2 'güç kaynağı bağlamı AÇILMAZ' metni ne zaman revize edilir"
  - "power/ ölçümleri (S1=0) hangi oturumda yapılacak"
date: 2026-10-13
updated: 2026-10-13
group: mimari
---

# power — Tip Dizini

> **Neden bu klasör var**: `donanaım-type/index.md` kademe-3 dizininde `power` satırı
> (Vault Steward onayı, 2026-10-13) bu klasörle karşılanır. Anayasa §2 ve
> `donanaım-type-kapsam.md` metinleri henüz "yalnız 3 bağlam" dediği için **çatışma kaydı**
> `power-kapsam.md §0`'dadır; metin revizesi = APPROVAL REQUIRED (bu klasörde yapılmaz).
> **Sınır**: K001 power = tip/enventar sınıflandırma; devre/PCB/BOM/hesap ⇒ K002-electroncis
> + ADR-063/064 (bkz. `power-kapsam.md §sınır`).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

## GRUPLAR

| Dosya | group | node_id | Ne verir |
|---|---|---|---|
| `power/index.md` | mimari | K001-donanım/donanaım-type/power | bu dizin — children, kapı özeti |
| `power-kapsam.md` | mimari | K001-donanım/donanaım-type/power/power-kapsam | §0 çatışma kaydı · §2 kapsam · §sınır K002 · §6 P-T01…P-T13 tip kayıtları |
| `power-dogrulama.md` | kanit | K001-donanım/donanaım-type/power/power-dogrulama | kanıt seviyeleri S1–S5 · kapılar K-P1…K-P8 · boş ölçüm formları · HEAD kaydı |
| `power-ornekler.md` | kod | K001-donanım/donanaım-type/power/power-ornekler | repo-code kanıtı (700 dosya/0 eşleşme) · sigorta/LDO şablonları · koruma doğrulama formu |

---

## Kapı özeti (detay: `power-dogrulama.md`)

| Kapı | Ne | Status 2026-10-13 |
|---|---|---|
| K-P1 | 8 alan dosyası + Authority son satır | KAPALI (bu görev) |
| K-P2 | §0 çatışma kaydı + K002/ADR sınır notu | KAPALI (bu görev) |
| K-P3 | anayasa §2 metni revizesi | **AÇIK** (APPROVAL REQUIRED) |
| K-P4 | kendi ölçümüm (PSU/VRM/LDO/koruma) — S1=0 | **AÇIK** |
| K-P5 | VD-02 (power tipsiz mi) sonucu | **AÇIK** |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
