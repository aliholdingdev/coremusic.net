---
node_id: K021-recomenataion/evaluation
node_type: subdomain
status: draft
parent_id: K021-recomenataion
children: []
depends_on:
  - K021-recomenataion
  - ranking
  - personalization
evidence:
  - "v2 §2.20 — evaluation/ (alt dosya listesi yok → children: [])"
  - "recommendation-arastirma.md §2 — 2026-10-17 web taraması (scikit-learn ndcg_score · precision_recall_fscore_support · Evan Miller A/B · Vertex AI monitoring — HTTP 200)"
  - "shared/src/AI/AIEngine.php — dosyada ölçüm kodu YOK (382 satır okundu) → S6 boşluğu"
validation:
  - "2026-10-12 — şekil v2 §2.20 ile birebir; içerik yalnız iskelet"
  - "2026-10-17 — offline/online/izleme katmanları + kaynak doğrulamasıyla derinleştirildi"
open_questions:
  - "Hangi metrik, hangi eşik, hangi veri seti? Eşik atanmadı → VERIFY REQUIRED (uydurma eşik YASAK)"
  - "A/B altyapısı var mı? → VERIFY REQUIRED"
  - "Ölçüm sonuçları nereye append edilecek (verification/ ile uyum)? → VERIFY REQUIRED"
group: mimari
---


# K021-recomenataion/evaluation — Öneri Değerlendirmesi

**ELI15:** "Öneri **gerçekten işe yaradı mı?**" sorusunun cevaplandığı yer.
Ölçüm tanımlanmadan öneri **yayına alınamaz** (süreç kuralı).

| Alan | Değer |
|---|---|
| `node_type` | `subdomain` · `children: []` |
| Sorumluluk | Öneri çıktısını **ölçmek** ve kaydetmek |
| Pipeline konumu | **S6** (ölçüm ve izleme) — master §4 |
| Girdi | `ranking` sırası (S3/S4 çıktısı) + kullanıcı davranışı (S5 olayları) |
| Metrikler | **tanımlı değil** — isimler çerçevedir, **eşik yazılmadı** |
| Eşik (geç/kal) | **yok** → `⚠️ VERIFY REQUIRED` (ADR-3 kapısı) |
| Sonuç kaydı | `verification` mantığıyla append-only; tur kayıtları `K020-comilance/verification/` ile uyumlu |

**Kapı:** metrik + eşik + veri tanımsızsa `status: verify-required` kalır.

## §1 Ölçüm katmanları

| Katman | Ne ölçülür | Veri kaynağı | Ne zaman |
|---|---|---|---|
| **Offline** | sıra kalitesi (geçmiş etkileşimle) | geçmiş etkileşim veri seti (S5'ten) | her sürüm öncesi |
| **Çeşitlilik/kapsam** | intra-list diversity · catalog coverage · novelty | katalog + öneriler | her sürüm öncesi |
| **Online (A/B)** | gerçek davranış farkı (tamamlama/atlama/oturum) | canlı trafik split | yayım öncesi/sonrası |
| **İzleme (production)** | skor dağılımı, boş çıkma, hata oranı, sürüklenme | üretim logları | sürekli |

> **Sıra kuralı:** offline geçmeden online deney **başlamaz**; online deney yoksa
> "canlıya alındı" iddiası **kanıtsızdır** (master §9 ④⑤).

## §2 Offline metrik çerçevesi (tanım — eşik yok)

> Aşağıdaki metrik **isimleri ve tanımları** çerçevenin parçasıdır; **hiçbir sayısal
> eşik verilmemiştir**. Uygulama referansı scikit-learn (HTTP 200 doğrulandı:
> `ndcg_score` · `precision_recall_fscore_support` — `recommendation-arastirma.md` §2).

| Metrik | Ne ölçer | Not |
|---|---|---|
| Precision@K | ilk K öneriden kaç tanesi ilgili | K değeri ADR-3'te |
| Recall@K | kullanıcının ilgili içeriklerden kaçını K içinde yakaladı | katalog büyüklüğüne duyarlı |
| NDCG@K | sıralama kalitesi (sıra duyarlı) | scikit-learn `ndcg_score` ile hesaplanabilir |
| MAP | ortalama precision (tüm ilgili pozisyonlarda) | ikili relevance varsayar |
| MRR | ilk ilgili önerinin ters sırası | tek hedefli senaryolarda |
| Catalog coverage | katalogda önerilen öğe oranı | keşif/monopoli denetimi |
| Intra-list diversity | tek listede çeşitlilik | repo `DIVERSITY_TARGET=0.30` ile ilişkili |
| Novelty | önerinin kullanıcının bilmediği içerik oranı | filter bubble denetimi |

## §3 Online ölçüm (A/B)

| Öğe | Tanım | Durum |
|---|---|---|
| Hipotez | "Yeni sıralama, tamamlama oranını <yön> kaydırır" | her deneyde **yazılır** |
| Split | kullanıcı bazlı rastgele atama (kontrol/deney) | altyapı **yok** → `⚠️` |
| Metrik | tamamlama oranı · atlama oranı · oturum süresi | eşik **yok** |
| İstatistik | güven aralığı / anlamlılık hesabı | kaynak: Evan Miller A/B (HTTP 200 — `recommendation-arastirma.md` §2) |
| Süre | önceden belirlenmiş süre; erken kesme **yasak** | süreç kuralı |

## §4 Üretim izleme (S6 sürekli kısım)

| Sinyal | Ne yakalar | Durum |
|---|---|---|
| Skor dağılımı kayması | model/veri sürüklenmesi | altyapı yok → `⚠️` |
| Boş çıkma (no-result) oranı | katalog/S1 boşluğu (master §13 B1) | log yok → `⚠️` |
| Hata/oran oranı | sunum (S4) sağlığı | log yok → `⚠️` |
| Popülerlik monopoli | coverage düşüşü | S6 metriği (§2) |

> Kaynak doğrulaması: Vertex AI "evaluating model performance" + "discover/evaluations"
> sayfaları HTTP 200 (kalıp referansı — CoreMusic'te Vertex kullanılmaz; **desen** alınır).

## §5 Veri seti gereksinimleri (kapı)

| Gereksinim | Açıklama | Durum |
|---|---|---|
| Etkileşim olay şeması | dinleme/atlama/favori olaylarının kaydı (S5) | **yok** → `⚠️` (master §13 B2) |
| Zaman bölünmesi | eğitim/ölçüm zaman ayrımı (sızıntı önleme) | tasarım yok |
| İlgili (ground truth) tanımı | "ilgili" ne demek (dinledi mi, favoriledi mi?) | **tanımsız** → ADR-3 |
| Minimum veri hacmi | metrik stabilitesi için gereken olay sayısı | **uydurulmaz** — ölçümle belirlenecek |

## §6 Geç/kal kapıları (ADR-3 — eşikler burada atanır)

| Kapı | Soru | Durum |
|---|---|---|
| K4a | Hangi metrikler? (§2'den seçim) | `⚠️ VERIFY REQUIRED` |
| K4b | Eşikler? (sayı **uydurulmaz** — veriyle belirlenecek) | `⚠️ VERIFY REQUIRED` |
| K4c | Veri seti + olay şeması? | `⚠️ VERIFY REQUIRED` |
| K4d | A/B altyapısı? | `⚠️ VERIFY REQUIRED` |
| K5 | Yayım | K4 kapalı değil → **kapalı** (master §9) |

## §7 Sınır (bu alt bölgenin sahip olmadığı)

| Alan | Sahip | Not |
|---|---|---|
| Sıralama stratejisi | `[[../ranking/index]]` | evaluation ölçer, karar vermez |
| Profil/gizlilik | `[[../personalization/index]]` + `../K019-comilance/` | veri işleme kapsamı |
| Olay üretimi | `[[../recommendation-master]]` §4 (S5) | evaluation olay **alır** |
| Test altyapısı (CI) | `K019-testing` (diskte `K18-testing`) | birim/E2E testi burada değil |

## §8 Kod referansları

| Konu | Dil | Dosya |
|---|---|---|
| NDCG@K / Precision-Recall hesabı | Python (scikit-learn) | `[[../recommendation-kod-ornekleri]]` §Python |
| Offline metrik döngüsü (uçtan uca) | Go · Rust · C# | aynı dosya |
| A/B kayıt satırı + istatistik | R · PowerShell | aynı dosya |
| Metrik depolama sorguları | SQL | aynı dosya |
| CI'da metrik kapısı | YAML (GitHub Actions) | aynı dosya |

## §9 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| 1 | Metrik seçimi + eşikler (K4a/K4b)? | `⚠️ VERIFY REQUIRED` — ADR-3 |
| 2 | Olay şeması ve veri seti (K4c)? | `⚠️ VERIFY REQUIRED` |
| 3 | A/B altyapısı (K4d)? | `⚠️ VERIFY REQUIRED` |
| 4 | Ölçüm sonuçlarının append hedefi? | `⚠️ VERIFY REQUIRED` |
| 5 | Offline metrik aracı seçimi (scikit-learn mi, kendi kodun mu)? | `⚠️ VERIFY REQUIRED` — K006 iş birliği |

## §BAĞLANTILAR

| Hedef | Wiki-link | Göreli dosya yolu | İlişki |
|---|---|---|---|
| Program anayasası | `[[../recommendation-master]]` | `../recommendation-master.md` | S6 tanımı + kapılar (§8-§9) |
| Ranking | `[[../ranking/index]]` | `../ranking/index.md` | ölçümün girdisi olan sıra |
| Personalization | `[[../personalization/index]]` | `../personalization/index.md` | kişiselleştirme kazancı ölçümü |
| Kod örnekleri | `[[../recommendation-kod-ornekleri]]` | `../recommendation-kod-ornekleri.md` | metrik uygulama referansı |
| Araştırma | `[[../recommendation-arastirma]]` | `../recommendation-arastirma.md` | metrik/A-B/izleme kaynakları |
| Klasör kapısı | `[[../index]]` | `../index.md` | çocuk kataloğu |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
