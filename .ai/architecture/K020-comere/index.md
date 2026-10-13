---
node_id: K020-comere
node_type: domain
status: verify-required
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on: []
evidence:
  - "v2 §2.20 mini-ağaç (K020-comere → index.md · scope-verification.md) — G:/Drive'ım/Projelerim/Project-notes/Bayram Ali Prompt Arşivi & Notlar/Coremusic/coremusic-katmanli-mimari-agaci-v2.md (satır 557–561, 586–589)"
  - "v2 §3.1 — 'K020-comere içeriği bilinmiyor → UNKNOWN → VERIFY REQUIRED'"
  - ".ai/architecture/index.md §5 satır 80 (verify-required) · §6 satır 91 (UNKNOWN)"
  - ".ai/.decisions/draft/ADR-097-…md §2 K6 · §5.3 ('comere' kelimesi hiçbir yazılım sözlüğünde/geçerli terimde yok)"
validation:
  - "2026-10-12 — v2 §2.20 şekli birebir üretildi; içerik doldurulmadı (tahmin yasak)"
open_questions:
  - "⚠️ ID CONFLICT: 6 klasör K020 önekinde paylaşır (adr/comilance/comere/licence/discovery/recomenataion) — ADR-097 §6 · v2 §3.1"
  - "UNKNOWN: K020-comere neyi temsil ettiği bilinmiyor — v2 §2.20 uyarısı ('kapsam tanımı yok; tahmin yasak'). Onay gelene kadar yalnız iskelet durur, bağımlılık/çocuk ATANMAZ."
  - "'comere' yazımı kaynaktadır, düzeltilmez (v2 §3 KURAL 1 · ADR-097 §5.3)"
group: mimari
---


# K020-comere — Kapsamı Bilinmeyen Yönetişim Kaydı

## ELI15 — Bu klasör ne işe yarar?

**Bilinmiyor.** Kaynak belgede (`K020-comere`) bu klasörün neyi temsil ettiği
**açık yazılmamış**. Tahmin etmek yasak olduğu için burada yalnızca **iskelet** durur;
içerik, kapsam tanımı onaylanana kadar **boş** bırakılmıştır.

## Çocuk kataloğu (v2 §2.20 — birebir)

| Öğe | Tür | Rol | Durum |
|---|---|---|---|
| `scope-verification.md` | dosya | Kapsam doğrulama kaydı — **içeriği boş** (yalnız başlık) | `verify-required` |

## Durum

- **status:** `verify-required` (registry §5 satır 22 ile aynı) — `draft` **değildir**.
- **children / depends_on:** boş — atama yapmak tahmin olurdu.
- **Kapı:** kapsam tanımı gelene kadar bu klasörde içerik üretimi **DUR** (v2 §3 KURAL 3).
- **İlişki:** çakışma kaydı `architecture/index.md` §6 ve ADR-097 §2 K6'dadır; burada tekrar edilir, düzeltilmez.

## Açık soru özeti

`⚠️ ID CONFLICT` (6× K020) + `UNKNOWN` (comere kapsamı) — her ikisi de `open_questions`
alanında kayıtlıdır; **onaysız isim/içerik değiştirilmez.**

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-1 |
| kanit | `scope-verification.md` | ⚠️ VERIFICATION REQUIRED — kapsam tanımı yok (tahmin yasak) | Kademe-1 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
