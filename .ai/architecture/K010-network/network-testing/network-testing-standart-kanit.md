---
node_id: K010-network/network-testing/standart-kanit
node_type: module
status: draft
parent_id: K010-network/network-testing
role: kanit
children: []
depends_on: []
evidence:
  - "RFC 2544 — ağ cihazı/benchmark metodolojisi (rfc-editor.org, HTTP 200, 2026-10-15)"
  - "MDN Remote Playback API — teslim yüzeyi (handoff ile ortak; HTTP 200, 2026-10-15)"
  - "docs.python.org asyncio · docs.pytest.org · tokio.rs · docs.rs/warp — test çalışma zamanları/araçları (HTTP 200, 2026-10-15)"
validation:
  - "Tüm URL'ler Invoke-WebRequest ile 2026-10-15'te HTTP 200 doğrulandı (başlık §1–§2)"
open_questions:
  - "network-testing ↔ K019-testing sahipliği → ⚠️ VERIFICATION REQUIRED (§7/6) — bu dosya yalnız ağ testi aracılıkları taşır"
  - "Gecikme/kopma/senaryo eşikleri (ölçüm hedefleri) → ⚠️ UNKNOWN (faz C)"
group: kanit
---

# network-testing — standart-kanit (kademe-2 · module · rol: kanit)

**ELI15:** "Ağ davranışını test etme" iddiasının ölçüm metodolojisi ve çalışma
zamanı araçlarının URL kanıt defteri. Her satır 2026-10-15'te HTTP 200 ile
doğrulanmıştır (S1 kanıtı).

**Sınır (ne DEĞİL):** Ortak test altyapısı ve kapsam sahibi (K019-testing — §7/6) ·
eşik değerleri/SLA (faz C) · K014 dışı senaryolar.

## §1 Ölçüm Metodolojisi (S1 — HTTP 200 · 2026-10-15)

| # | Kaynak | Konu | URL | Durum |
|---|---|---|---|---|
| 1 | RFC 2544 | Ağ cihazları için benchmark metodolojisi (gecikme/throughput/frame-loss ölçüm çerçevesi) | https://www.rfc-editor.org/rfc/rfc2544 | ✅ 200 |

## §2 Test Çalışma Zamanları / Araçları (S1 — HTTP 200 · 2026-10-15)

| # | Kaynak | Konu | URL | Durum |
|---|---|---|---|---|
| 2 | Python asyncio docs | Async test/simülasyon çalışma zamanı (kopma-gecikme senaryoları) | https://docs.python.org/3/library/asyncio.html | ✅ 200 |
| 3 | pytest docs | Python test çalıştırıcı (senaryo organizasyonu) | https://docs.pytest.org/ | ✅ 200 |
| 4 | tokio.rs | Rust async çalışma zamanı (yük testi/denetçi kolu) | https://tokio.rs | ✅ 200 |
| 5 | warp (docs.rs) | Rust HTTP test sunucusu (sahte uç/entegrasyon kolu) | https://docs.rs/warp/ | ✅ 200 |

> Ekosistem listesi **seçim değildir** — hangi aracın K019 ile paylaşılacağı faz C'dedir.

## §3 Kanıt → Kapsam Eşlemesi (hangi iddiayı destekler)

| İddia (index open_questions / §7/6) | Destekleyen kanıt | Sınıf |
|---|---|---|
| Ağ performans ölçümünün resmi metodolojisi vardır | RFC 2544 (§1/1) | S1 ✅ |
| Async senaryo testi (gecikme/kopma enjeksiyonu) çalıştırılabilir | asyncio + pytest (§2/2–3) | S1 ✅ |
| Yük/denetçi kolu Rust'ta mümkündür | tokio + warp (§2/4–5) | S1 ✅ |
| **Kapsam sahipliği** (network-testing vs K019-testing) | Seçim YOK — araç ≠ sahiplik | ⚠️ §7/6 faz C |
| Ölçüm eşikleri (hedef gecikme/kopma toleransı) | Seçim YOK | ⚠️ faz C |

## §4 Kanıt Sınıfı Özeti

| Sınıf | Sayı |
|---|---|
| S1 (URL + HTTP 200) | 5 |
| S3 (beyan, URL'siz) | 0 |
| UNKNOWN (karar bekleyen) | 2 (sahiplik §7/6 · eşikler) |

**Kapı:** "ağ testi yapılabilir mi" sorusu S1 ile kapalıdır; "kimin kapsamı ve hedef
eşikler" (index §A/2 = master §7/6) açık kalır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
