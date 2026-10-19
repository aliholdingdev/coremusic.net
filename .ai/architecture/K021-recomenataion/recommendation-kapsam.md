---
node_id: K021-recomenataion-kapsam
node_type: subdomain
status: draft
parent_id: K021-recomenataion
children: []
depends_on: [K006-yapay-zeka, K007-data, K009-security, K015-AI, K020-discovery]
evidence:
  - ".ai/architecture/K021-recomenataion/recommendation-master.md §2-§6 (kapsam/kapsam-dışı + S1-S6 + komşu sınırı — 2026-10-18)"
  - "shared/src/AI/AIEngine.php:30-32,49-77,94-100 (Faz-1 repo kanıtı — 2026-10-17 okundu)"
  - ".ai/architecture/K021-recomenataion/recommendation-arastirma.md §0-§1 (28 URL = 15×200 + 9×404 + 4×403 — 2026-10-17)"
validation: "Kapsam ± sınır her § disk/repo kanıtıyla; KVKK sınırı ⚠️ olarak; depends_on yalnız gerçek K adları (K019-comilance diskte)."
open_questions:
  - "⚠️ ID CONFLICT: master içinde 'K020-comilance' geçer — diskte gerçektir K019-comilance (klasör adı immutable, yazım değiştirilmedi)"
  - "⚠️ KVKK/GDPR kapsamı tanımsız → profil şeması yazılmaz, veri toplanmaz (master §7 K1)"
  - "⚠️ Öneri motoru sahipliği üçlü çakışma: K006 (ML ürün) mü / K015 (ajan) mı / K021 (program) mı — ADR bekliyor"
group: mimari
---

# Recommendation — Kapsam

> K021-recomenataion programının neyi kapsadığını ve sınırının nerede bittiğini
> tanımlar. Kaynak: `recommendation-master.md` (v2.0.0, 2026-10-17) + repo kanıtı.

## §1. ELI15 — Bu klasör ne işe yarar?

**K021 = öneri sisteminin plan dosyası.** Müziği öneren motorun "şemasıdır":
neyle beslenecek (çalma geçmişi, ses özellikleri, keşif kataloğu), ne üretecek
(sıralı liste), parçaları neler (ranking/personalization/evaluation) ve **işe
yaradığını nasıl anlayacağız** (ölçüm). Bu dosya algoritma **yazmaz** — sınır ve
**kapıları** çizer. Kod `recommendation-kod-ornekleri.md` (22 dil), kanıt
`recommendation-arastirma.md` içindedir.

## §2. Kapsam (in-scope)

| # | Alan | Kanıt |
|---|---|---|
| 1 | Öneri girdi/kaynak haritası | master §3 (6 girdi/çıktı satırı) |
| 2 | S1-S6 boru hattı tanımı (aday→skor→re-rank→sunum→geri bildirim→ölçüm) | master §4 (kanonik — 2026-10-17 birleştirildi) |
| 3 | Alt sistem sınırları: ranking · personalization · evaluation | master §5 |
| 4 | Ölçüm çerçevesi (metrik **isimleri** — eşik YOK, uydurma yasak) | master §8 |
| 5 | Onay kapıları K1-K5 (veri→yöntem→ölçüm→kod→yayım) | master §9, §27 |
| 6 | Faz planı (Faz-1 mevcut çekirdek — repo kanıtlı) | master §10, §13 |

## §3. Kapsam dışı (out-of-scope)

| # | Dışarıda | Nereye |
|---|---|---|
| 1 | Algoritma üretim kodu (kademe 5-8) | `shared/src/` yalnız ADR sonrası |
| 2 | Dış servis entegrasyonu | `../K020-discovery/` |
| 3 | Kişisel veri işleme hukuku (KVKK) | `K019-comilance` (⚠️ master 'K020-comilance' yazar — diskte K019) |
| 4 | Model eğitimi, feature store, ML hattı | `K006-yapay-zeka` |
| 5 | Tablo şeması/migration/indeks | `K007-data` |
| 6 | Ajan hattı (Orchestrator/PromptEngine) | `K015-AI` — öneri ≠ ajan |

## §4. Karar yüzeyi özeti

| Karar | Durum | Kaynak |
|---|---|---|
| S1-S6 boru hattı kanonik tanımı | ✅ alındı (D1 — 2026-10-17) | master §25 |
| Sıralama yöntemi (A kural / B skor / C öğrenen) | ⏸ ADR-2 bekliyor | master §25 D2 |
| Metrik + eşikler | ⏸ ADR-3 bekliyor (uydurma eşik yasak) | master §8, §25 D3 |
| KVKK kapsamı + hukuki dayanak | ⚠️ K1 kapısı | master §27 |
| `0.6/0.4` ağırlık doğrulaması | ⏸ Faz-4 ölçümü | master §11/6 |
| Klasör ID'si (K021 vs 40+ K020 referansı) | ⏸ üst karar | master §12/5 |

## §5. Sınır haritası (komşu K — kim neyin sahibi)

| Komşu K | İlişki | Sahip olmadığı yüzey |
|---|---|---|
| K006-yapay-zeka | ML ürün tarafı | model eğitimi + feature store |
| K007-data | şema kaynağı | tablo/migration/indeks |
| K009-security | auth/CSRF/rate-limit | `shared/src/Middleware/` |
| K015-AI | ajan altyapısı | ajan hattı |
| K020-discovery | dış katalog keşfi | entegrasyon onayı (YTM kapalı) |
| K019-comilance | kişisel veri hukuku | KVKK kapsam kararı (⚠️ K1) |
| K18-testing | test altyapısı | test uygulaması |

## §6. Ölü referanslar (⚠️ — depends_on'a YAZILMAZ)

| Ölü ad | Durum |
|---|---|
| `K020-comilance` (master içinde geçer) | diskte YOK — gerçek `K019-comilance` (ID CONFLICT, immutable) |
| `K020-adr` (master §25 D2/§30 grafiği) | disk K listesinde doğrulanmadı → ⚠️ ADR mercii, klasör adı doğrulanmadı |
| `K019-testing` | diskte YOK — gerçek `K18-testing` |
| `K0100-identitiy` · `K020-licence` · `K020-comere` · `K002-altyapi` · `K001-infrastructure` · `K002-data` · `K018-devops` | diskte YOK — yazım/envanter hataları |

## §7. Bağımlılıklar (gerçek K adları — disk ölçümü)

| K | Yön | Ne |
|---|---|---|
| K020-discovery | upstream (D-1) | dış katalog keşfi → aday girdisi; keşif bitmeden K021 kodlanmaz |
| K007-data | upstream | katalog/olay şeması (⚠️ şema kanıtı bekleniyor) |
| K006-yapay-zeka | yan | ML eğitim tarafı (sahiplik ayrımı ADR bekliyor) |
| K015-AI | yan | ajan altyapısı (öneri ≠ ajan) |
| K009-security | yan | auth/CSRF (401/403 semantiği) |
| K019-comilance | gate (K1) | KVKK kapsam kararı — açık |

## §8. Açık sorular (dosya sonu)

1. ⚠️ K1: Öneri kişisel veri işliyor mu? KVKK kapsamı tanımsız → profil toplanmaz.
2. ⚠️ K2: Veri kaynağı (yerel geçmiş / dış servis / ikisi) — şema kanıtı yok.
3. ⚠️ K3/ADR-2: sıralama yöntemi ve S1 stratejisi kararı.
4. ⚠️ K4/ADR-3: metrik eşikleri — ölçüm yapılmadan sayı yazılmaz.
5. ⚠️ K021 ID CONFLICT (registry Band-2 kapısı ADR-098 öncesi — master §12/5).

---

**Authority:** Bayram Ali / Vault Steward
