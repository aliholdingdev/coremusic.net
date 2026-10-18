---
node_id: K021-recomenataion-riskler
node_type: subdomain
status: draft
parent_id: K021-recomenataion
children: []
depends_on: [K006-yapay-zeka, K007-data, K019-comilance, K020-discovery]
evidence:
  - ".ai/architecture/K021-recomenataion/recommendation-master.md §11 (6 risk) ve §19 (7 hata/azaltma senaryosu — 2026-10-18)"
  - "shared/src/AI/AIEngine.php:31,32,65 (MIN_SCORE/DIVERSITY_TARGET/0.6-0.4 — risk R7/R8 kanıtı, 2026-10-17)"
  - "recommendation-arastirma.md §0-§1 (28 URL: 9×404 elendi · 4×403 — kaynak riski, 2026-10-17)"
validation: "Her risk Olasılık×Etki×Azaltma×Sahip taşır; değerler NİTEL — sayısal skor uydurulmaz."
open_questions:
  - "⚠️ KVKK kapsamı (K1) açıkken başlayan kişiselleştirme geri alınamaz maliyet üretir — sahip K019-comilance"
  - "⚠️ Ağırlık 0.6/0.4 yanlışsa sessiz kalite kaybı — ancak Faz-4 ölçümü (D5) ayırt edebilir"
group: mimari
---

# Recommendation — Riskler

> K021 program riskleri + fail-safe senaryoları. Değerler **NİTEL**tir
> (düşük/orta/yüksek) — sayısal olasılık/etki skoru uydurulmaz.

## §1. ELI15

Risk = ters giderse ne kaybederiz. Her riskte dört zorunlu alan: **Olasılık**,
**Etki**, **Azaltma**, **Sahip**. Bu programın en tehlikeli iki riski:
**ölçümsüz yayım** ("öneri iyi" kanıtsız iddia) ve **erken kişiselleştirme**
(KVKK kapsamı kapalıyken veri toplama).

## §2. Risk matrisi (R1-R10)

| ID | Risk | Olasılık | Etki | Azaltma | Sahip |
|---|---|---|---|---|---|
| R1 | Ölçümsüz yayım — metrik yokken "öneri iyi" iddiası kanıtsız | yüksek | yüksek | K4 kapısı: S6 tanımlanmadan S4 yayına alınamaz (master §9); eşik uydurulmaz | Vault Steward + evaluation/ |
| R2 | Erken kişiselleştirme — KVKK kapsamı (K1) tanımsızken profil toplama | orta | yüksek | K1 kapalıyken profil toplanmaz; `personalized:false` fail-safe (master §7, §18) | K019-comilance (kapsam kararı) |
| R3 | Kapsanmayan dış kaynak — YTM resmi API'siz; katalog boşluğu | orta | orta | K020-discovery kapısı; yerel katalog fallback (master §19/7) | K020-discovery |
| R4 | Kapsam kayması — motor K006 (ML) mı / K015 (ajan) mı / K021 (program) mı | orta | yüksek | Üçlü ayrım ADR bekliyor (master §12/3); bu K yalnız program/sınır sahibi | architect + Vault Steward |
| R5 | Sıfır aday üretimi (B1) — S1 yok, küçük katalogda "her şey önerilir" | yüksek | orta | Faz-2 kapsamı; boş aday → boş liste (uydurma yok — §19/1) | K021 (Faz-2) + K007-data |
| R6 | Geri bildirim yokluğu (B2/S5) — döngü kapalı değil, öneri kör kalır | yüksek | orta | Faz-3 olay şeması; kuyruk/log düşüşü (§23 S5) | K021 (Faz-3) |
| R7 | Ağırlık dondurma — 0.6/0.4 veriyle doğrulanmadı → sessiz kalite kaybı | yüksek | orta | Faz-4 ölçümü (D5); K006 iş birliği; offline metrik karşılaştırması | Vault Steward + K006 |
| R8 | Eşik davranışı — `MIN_SCORE=0.7` + sort öncesi filtre → hepsi altta kalır, boş çıkma | orta | orta | S6 izleme alarmı (boş çıkma oranı — §21); repo değeri tasarım onayına bağlanır | evaluation/ |
| R9 | Kaynak bayatlığı — servis URL'leri değişir (404×9 elendi; 403×4 içerik okunamadı) | orta | düşük | Erişim tarihli kanıt (2026-10-17); iddia yalnız okunan içerikten | K021 (arastirma) |
| R10 | ID CONFLICT geri dönüşü — 40+ K020-recomenataion referansı vs disk K021 | yüksek | orta | Disk adı immutable; dönüşüm yalnız üst karar + ADR-098 | Vault Steward + kullanıcı |

## §3. Hata/azaltma senaryoları (fail-safe — master §19, 7 senaryo)

| # | Senaryo | Aşama | Fail-safe davranış |
|---|---|---|---|
| 1 | Aday havuzu boş (S1 yok/katalog dar) | S1 | boş liste + `personalized:false` — **uydurma öneri YOK** |
| 2 | İşbirlikçi veri boş (yeni sistem) | S2 | content-only skor (ADR-2 kararı) |
| 3 | Profil okuma hatası | S1/S2 | kişiselleştirme atlanır, genel sıralama korunur |
| 4 | KVKK daraltıldı / kullanıcı sildi | S5/S1 | profil + özetler imha → cold-start |
| 5 | Skor dağılımı çöktü (hepsi eşiğin altında) | S3 | `MIN_SCORE` boşluk üretir → S6 alarmı |
| 6 | Ölçüm verisi yok (S5/S6 henüz yok) | S6 | **yayım durur** — sessiz geçiş yok |
| 7 | Dış katalog erişimi kesildi (K020-discovery) | S1 | yerel katalog ile devam |

**İlke:** `fail-open` (hatada devam) **yasak**; her yol `fail-safe`'e düşer
(master §26 sözlük).

## §4. Senaryo → azaltma özeti (QA eli)

| Azaltma bileşeni | Kapsadığı riskler |
|---|---|
| K1-K5 kapı sırası | R1, R2 |
| Boş liste/`personalized:false` fail-safe | R5, R2, R3 |
| Faz-4 offline ölçüm (NDCG/Precision) | R7, R8 |
| S6 izleme sinyalleri (boş çıkma, skor kayması) | R8, R6 |
| Immutable disk adı + üst karar | R10, R4 |

## §5. Risk sonrası kontrol listesi

- [ ] K1 (KVKK) kapalıyken profil/olay verisi toplandı mı? (toplanmamalı)
- [ ] Eşik/skor sayısı mı eklendi? (uydurma yasak — ADR-3)
- [ ] Boş aday/boş liste yolu dolduruldu mu? (doldurulmamalı)
- [ ] 0.6/0.4 ağırlığı "doğrulanmış" diye mi sunuldu? (D5 ⏸ — sunulmamalı)
- [ ] Yayımda K4 kapısı kapalı mı? (kapalıysa yayım başlamaz)

---

**Authority:** Bayram Ali / Vault Steward
