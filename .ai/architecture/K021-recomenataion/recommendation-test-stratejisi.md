---
node_id: K021-recomenataion-test-stratejisi
node_type: subdomain
status: draft
parent_id: K021-recomenataion
children: []
depends_on: [K007-data, K009-security, K18-testing]
evidence:
  - ".ai/architecture/K021-recomenataion/recommendation-master.md §20 (7 katmanlı test tablosu) ve §29 (uçtan uca akış — 2026-10-18)"
  - "shared/src/AI/AIEngine.php:49-77 (birim testi hedefi davranış — 2026-10-17 okundu)"
  - ".ai/architecture/K18-testing/testing-master.md (uygulama test altyapısı — 2026-10-18)"
validation: "Piramit + senaryolar + kabul kriterleri; eşik sayısı UYDURULMAZ (ADR-3); uygulama K18-testing'e devredilir."
open_questions:
  - "⚠️ K021 için hazır test dosyası diskte YOK — senaryolar taslak, uygulama K18'e devredildi"
  - "⚠️ 'K019-testing' ifadesi (master §20) ölü ad — diskte gerçek klasör K18-testing"
group: mimari
---

# Recommendation — Test Stratejisi

> Öneri sisteminin nasıl test edileceği. Piramit: birim → sözleşme → entegrasyon →
> offline metrik → online deney. **Kritik kural:** eşik sayıları ölçüm sonrası
> ADR-3 ile atanır — bu dosyada sayı **uydurulmaz**.

## §1. Test Piramidi

```
        [ ONLINE A/B DENEMYİ ]        ← gerçek kullanıcı davranışı (S6)
       [ OFFLINE METRİK ]             ← NDCG/Precision/coverage (veri seti)
      [ ENTEGRASYON + SÖZLEŞME ]      ← S1↔şema · API JSON doğrulama
   [ BİRİM (unit) ]                   ← skor birleştirmesi · filtre · fail-safe
  [ STATİK ]                          ← PHPStan/LINT (repo zorunluluğu)
```

**Uygulama yeri:** test altyapısı `K18-testing` (diskte; ⚠️ master §20'de
'K019-testing' der — ölü ad). K021 **kapı ve senaryo sahibidir**, test dosyası
üretmez.

## §2. Birim test senaryoları (Faz-1 davranışı — `AIEngine.php:49-77`)

| ID | Senaryo | Beklenen davranış |
|---|---|---|
| REC-T01 | Hibrit skor birleştirme (0.6/0.4) | `combineScores` ağırlıklı ortalama verir; ağırlık toplamı 1.0 |
| REC-T02 | `MIN_SCORE` filtresi (sort öncesi) | eşiğin altı sıralamaya girmez (satır 71 → 74 sırası korunur) |
| REC-T03 | `MAX_RECOMMENDATIONS` limiti | istemci 20'yi aşamaz (satır 51 `min()`) |
| REC-T04 | Çeşitlilik kotası (`DIVERSITY_TARGET=0.30`) | listede tür dağılımı kotası ihlal edilmez (uygulama satırı 110+ — ⚠️) |
| REC-T05 | Fail-safe: profil yok | kişiselleştirme atlanır, genel sıralama döner; `personalized:false` |
| REC-T06 | Fail-safe: `user_id` null (misafir) | 401 değil, genel öneri (§24.1/5) |
| REC-T07 | Boş aday havuzu | boş liste — **uydurma öneri eklenmez** (§19/1) |
| REC-T08 | Özellik eksik (mood/genre yok) | o özellik atlanır, ağırlıklar normalize (§23 S2) |

## §3. Entegrasyon + sözleşme senaryoları

| ID | Senaryo | Katman | Beklenen |
|---|---|---|---|
| REC-T10 | S1 aday üretimi ↔ katalog şeması (K007) | entegrasyon | geçerli `item_id` kümesi döner |
| REC-T11 | API sözleşmesi JSON doğrulama (master §18) | sözleşme | zorunlu alanlar: `items[].item_id/score`, `personalized`, `generated_at` |
| REC-T12 | Hata kodu semantiği: 401 / 403 / 500 / 200+boş | sözleşme | 403 = RFC 9110 erişim reddi (kaynak HTTP 200) |
| REC-T13 | Dış katalog kesintisi (K020-discovery) | entegrasyon | yerel katalog ile devam (§19/7) |
| REC-T14 | KVKK silme isteği → profil imhası | entegrasyon | cold-start'e düşer (§19/4) |

## §4. Offline / online ölçüm senaryoları (S6)

| ID | Senaryo | Metrik | Not |
|---|---|---|---|
| REC-T20 | Geçmiş veri üzerinde sıralama kalitesi | NDCG@K · Precision@K · Recall@K · MAP · MRR | scikit-learn `ndcg_score` kaynağı doğrulandı (HTTP 200); **eşik yok — ADR-3** |
| REC-T21 | Filter bubble denetimi | intra-list diversity · catalog coverage · novelty | repo `DIVERSITY_TARGET` çaprazı |
| REC-T22 | A/B split bütünlüğü | sızıntı/çakışma denetimi | online (master §20) |
| REC-T23 | Sürüklenme/izleme | skor dağılımı, boş çıkma, hata oranı | eşikler Faz-4 sonrası |

## §5. Kabul kriterleri

1. REC-T01..T08 birim senaryolarının **en az 6/8'i** otomatik testte yeşil
   (uygulama: `K18-testing`).
2. REC-T11 API sözleşmesi **JSON şema doğrulamasından** geçer; `reason` alanı
   ADR-2 öncesi **zorunlu değildir** (B4 — yokluğu hata sayılmaz).
3. REC-T12'de 403 semantiği RFC 9110'a uyar; 401 ile karıştırılmaz.
4. Boş liste yolu REC-T07 ile **kanıtlanır**: uydurma öğe eklenmediği test edilir.
5. K4 kapısı: offline metrik hattı çalışmadan yayım **başlamaz** (ölçüm raporu
   olmadan K5'e geçilmez).
6. Eşik **sayısı** bu dosyada yoktur; herhangi bir sayısal eşik iddiası = otomatik
   kırmızı (zero-hallucination).
7. Flaky test oranı %0 (K16/K18 kalite standardı — QA hedefi ≥80% coverage
   genel vault standardıyla uyumlu; K021 kapsamı ölçülmedi → ⚠️ UNKNOWN).

## §6. Otomasyon durumu (dürüst envanter)

| Katman | Durum |
|---|---|
| Mevcut PHP unit testi K021'e özel | ⚠️ **YOK** (kanıt: bu oturumda K021 test dosyası glob edilmedi — UNKNOWN) |
| CI coverage gate | soft (`continue-on-error` — K18 kanıtı) |
| Offline metrik hattı | **YOK** (boşluk B3 — Faz-4) |
| API sözleşme testi | **YOK** (üretim API'si onaysız — §18 şekil) |

## §7. Doğrulanmayan iddialar

| İddia | Durum |
|---|---|
| K021 testleri K18 CI'ında koşuyor | ⚠️ VERIFICATION REQUIRED |
| K021 kapsamındaki coverage ≥%80 | UNKNOWN (ölçülmedi) |
| REC-T04 çeşitlilik uygulaması davranış kodu | ⚠️ uygulama satırı okunmadı (110+) |

---

**Authority:** Bayram Ali / Vault Steward
