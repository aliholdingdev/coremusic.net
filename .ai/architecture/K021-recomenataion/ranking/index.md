---
node_id: K021-recomenataion/ranking
node_type: subdomain
status: draft
parent_id: K021-recomenataion
children: []
depends_on:
  - K021-recomenataion
evidence:
  - "v2 §2.20 — ranking/ (alt dosya listesi yok → children: [])"
  - "shared/src/AI/AIEngine.php:49-77 — S2/S3 Faz-1 karşılığı (2026-10-17 okundu)"
  - "shared/src/AI/AIEngine.php:30-32 — sabitler (MAX_RECOMMENDATIONS=20 · MIN_SCORE=0.7 · DIVERSITY_TARGET=0.30)"
validation:
  - "2026-10-12 — şekil v2 §2.20 ile birebir"
  - "2026-10-17 — içerik S1–S6 boru hattına göre derinleştirildi (master §4)"
open_questions:
  - "Sıralama yöntemi (kural tabanlı / skor / öğrenen model) seçilmedi → VERIFY REQUIRED (ADR-2 kapısı)"
  - "0.6/0.4 hibrit ağırlıkları hangi ölçümle doğrulanacak → VERIFY REQUIRED (Faz-4 kapısı)"
  - "MIN_SCORE=0.7 repo değeri tasarım onayı almadı → VERIFY REQUIRED"
group: mimari
---


# K021-recomenataion/ranking — Sıralama Alt Bölgesi

**ELI15:** "Adaylardan **hangisi üstte** olsun?" kararının verildiği yer.

| Alan | Değer |
|---|---|
| `node_type` | `subdomain` · `children: []` (v2'de alt liste yok) |
| Sorumluluk | Aday kümesini **sıralamak** (sıra = çıktı) |
| Pipeline konumu | **S2** (skorlama) + **S3** (yeniden sıralama) — master §4 |
| Girdi | `K021-recomenataion` aday havuzu (S1 çıktısı — master §13 B1: Faz-1'de **yok**) |
| Çıktı | sıralı liste — `evaluation/`ün (`S6`) ölçeceği şey |
| Yöntem | Faz-1: sabit ağırlıklı hibrit skor (repo kanıtı) · **gelecek yöntem ADR gerektirir** |
| Eşik/skor | repo sabitleri var (`MIN_SCORE=0.7`, `DIVERSITY_TARGET=0.30`) — **tasarım onayı yok** |

**Kapı:** yöntem seçimi ADR gerektirir (`../K020-adr` üzerinden).

## §1 Sıralama girdisi — özellik kümeleri (feature sets)

> Aşağıdaki tablo **tanımdır**; her özelliğin **mevcut/eksik** durumu kanıt sütunundadır.
> Eksik özellik için "var" iddiası yazılmaz.

| # | Özellik kümesi | Örnek özellikler | Kanıt/durum |
|---|---|---|---|
| F1 | İçerik (content) | tür, sanatçı, albüm, etiket, BPM, key, energy, acousticness | **var** — `AIEngine.php:94-100` (ses özellikleri çıkarılıyor) |
| F2 | İşbirlikçi (collaborative) | benzer kullanıcı tercihleri, item-item etkileşimi | **kısmi** — `AIEngine.php:59` çağırıyor; **arka plan veri yapısı repo'da tanımsız** → `⚠️` |
| F3 | Bağlam (context) | mood, genre isteği, cihaz, saat | **kısmi** — `AIEngine.php:54-55` (mood/genre) · cihaz/saat **yok** |
| F4 | Popülerlik/trend | son dönem dinlenme sayısı, yeni eklenen | **yok** → `⚠️` |
| F5 | Kullanıcı profili | tür tercihi vektörü, sanatçı affinity | **yok** → `[[personalization/index]]` (KVKK kapısı) |
| F6 | Çeşitlilik sinyali | listedeki tür dağılımı, sanatçı tekrarı | **var** — `AIEngine.php:32,67-68` |

## §2 Skorlama (S2) — hibrit denklem

Faz-1 denklemi (repo kanıtı — `AIEngine.php:65`):

```text
skor(aday) = w_collab × S_collab(aday) + w_content × S_content(aday)

repo değeri: w_collab = 0.6 · w_content = 0.4   (AIEngine.php:65 — sabit kod içinde)
```

| Karar | Durum |
|---|---|
| Ağırlık tipi | **sabit** (repo) — veriyle öğrenilmiyor |
| Ağırlık doğrulaması | **yok** → `⚠️ VERIFY REQUIRED` (Faz-4 metrikleriyle test edilecek) |
| Gelecek seçenekler | ağırlık ayarı (grid/A-B) · öğrenen sıralama (learning-to-rank) · iki aşamalı retrieval+ranking | hepsi **ADR-2 kapısı** |

## §3 Yeniden sıralama (S3) — çeşitlilik ve iş kuralları

| Kural | Ne yapar | Kanıt/durum |
|---|---|---|
| Çeşitlilik filtresi | listede tür çeşitliliği hedefini korur | **var** — `DIVERSITY_TARGET=0.30` (`AIEngine.php:32`) · uygulama `AIEngine.php:67-68` |
| Minimum skor | eşiğin altındaki adayları atar | **var** — `MIN_SCORE=0.7` (`AIEngine.php:31`) · `AIEngine.php:71` |
| Limit | çıktıyı 20 ile sınırlar | **var** — `MAX_RECOMMENDATIONS=20` (`AIEngine.php:30`) · `AIEngine.php:51,76` |
| Sonuç sıralama | skora göre azalan | **var** — `AIEngine.php:74` (`usort`) |
| Yasaklı içerik/iş kuralı (yaş sınırı, lisans bölgesi) | **yok** → `⚠️` (comilance + licence sınırı) |
| Aynı sanatçı üstelik tekrar engeli | **yok** → `⚠️` |

> **Dürüstlük notu:** `0.30` ve `0.7` değerleri **repo'da okunan değerlerdir**; "doğru"
> oldukları **iddia edilmez** — doğrulama Faz-4 ölçümüne bağlanmıştır (master §12 madde 7).

## §4 Yöntem seçenekleri (ADR-2 gündemi — henüz karar yok)

| Seçenek | Ne | Ne zaman makul | Durum |
|---|---|---|---|
| A. Kural tabanlı | elle yazılmış sıralama kuralları | çok küçük katalog, ilk sürüm | Faz-1 fiilen bunun bir türü (sabit skor) |
| B. Skor tabanlı (hibrit) | ağırlıklı özellik skoru | orta ölçek, açıklanabilirlik şart | **Faz-1 mevcut** (repo) |
| C. Öğrenen sıralama | model (learning-to-rank / iki aşamalı) | yeterli etiket/etkileşim verisi varsa | **Faz-5** — Faz-4 sonrası, K006 ile |

> Seçim, **veri varlığına** bağlanmıştır: yeterli etkileşim verisi olmadan C seçeneğine
> geçmek kanıtsızdır (K006 kapsamındaki ilerlemeyle birlikte değerlendirilir).

## §5 Sınır (bu alt bölgenin sahip olmadığı)

| Alan | Sahip | Not |
|---|---|---|
| Aday üretimi (S1) | `[[../recommendation-master]]` §4 | ranking aday **alır**, üretmez |
| Profil tutma | `[[../personalization/index]]` | ranking profili girdi olarak alır (F5) |
| Metrik hesabı | `[[../evaluation/index]]` | ranking çıktısını S6 ölçer |
| Dış servis | `../K020-discovery/` | katalog erişimi sınırı |
| Şema/migration | `K007-data` | özellik tabloları burada tanımlanmaz |

## §6 Kod referansları

| Aşama | Dil | Dosya |
|---|---|---|
| S2 hibrit skor (Faz-1 birebir) | PHP | `[[../recommendation-kod-ornekleri]]` §PHP |
| S3 çeşitlilik + min-skor | JavaScript · Python · Go · Rust · … | aynı dosya — her dilde bir aşama |
| Offline sıra metrikleri (NDCG@K) | Python (scikit-learn) | `[[../recommendation-arastirma]]` §2 kaynak 2 |

## §7 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| 1 | Sıralama yöntemi A/B/C seçeneklerinden hangi? | `⚠️ VERIFY REQUIRED` — ADR-2 |
| 2 | `0.6/0.4` ağırlık doğrulaması hangi veri setiyle? | `⚠️ VERIFY REQUIRED` — Faz-4 |
| 3 | F2 (collaborative) veri yapısı nerede ve ne zaman doldurulacak? | `⚠️ VERIFY REQUIRED` |
| 4 | `MIN_SCORE=0.7` — boş çıkma (cold-start) riski yaratıyor mu? | `⚠️ VERIFY REQUIRED` — S6 ölçmeli |

## §BAĞLANTILAR

| Hedef | Wiki-link | Göreli dosya yolu | İlişki |
|---|---|---|---|
| Program anayasası | `[[../recommendation-master]]` | `../recommendation-master.md` | S1–S6 boru hattı sahibi |
| Kişiselleştirme | `[[../personalization/index]]` | `../personalization/index.md` | F5 profil girdisi |
| Ölçüm | `[[../evaluation/index]]` | `../evaluation/index.md` | S6 çıktıyı ölçer |
| Kod örnekleri | `[[../recommendation-kod-ornekleri]]` | `../recommendation-kod-ornekleri.md` | S2/S3 uygulama referansı |
| Araştırma | `[[../recommendation-arastirma]]` | `../recommendation-arastirma.md` | iki aşamalı mimari + metrik kaynakları |
| Klasör kapısı | `[[../index]]` | `../index.md` | çocuk kataloğu |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
