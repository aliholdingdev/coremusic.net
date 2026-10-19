---
title: "K021-recomenataion — Recommendation Master (Öneri Programı)"
type: master
category: architecture
node_id: K021-recomenataion/recommendation-master
node_type: file
date: 2026-10-12
updated: 2026-10-17
status: draft
version: 2.0.0
authority: "SSOT: v2 §2.20 (şekil) · ADR-097 §5 (isimlendirme) · accepted/ADR-030 (YZ stratejisi) · repo kanıtı shared/src/AI/AIEngine.php"
related:
  - "index.md"
  - "ranking/index.md"
  - "personalization/index.md"
  - "evaluation/index.md"
  - "recommendation-kod-ornekleri.md"
  - "recommendation-arastirma.md"
  - "../K020-discovery/index.md"
  - "../../K006-yapay-zeka/recommendation/recommendation-kapsam.md"
group: mimari
---


# K021-recomenataion — Recommendation Master

## 1. Rol (ELI15)

Öneri sisteminin **tek sayfalık planıdır**: neyle beslenecek (girdi), ne üretecek
(çıktı), parçaları neler (alt sistemler) ve **işe yaradığını nasıl anlayacağız**
(ölçüm). Bu dosya algoritma **yazmaz** — sınır ve kapıları çizer. Uygulama kodu
`recommendation-kod-ornekleri.md` içindeki dil matrisinde referans olarak durur;
araştırma kanıtı `recommendation-arastirma.md` içindedir.

## 2. Kapsam / Kapsam dışı

| Kapsam | Kapsam dışı |
|---|---|
| Öneri girdi/kaynak haritası | Algoritma kodu (implementation — kademe 8) |
| Aday üretimi + sıralama + sunum **boru hattı** (pipeline) tanımı | Dış servis entegrasyonu → `../K020-discovery/` |
| Alt sistemlerin sınırları (ranking/personalization/evaluation) | **Kişisel veri işleme kuralları** → KVKK kapsamı tanımsız (`⚠️`) |
| Ölçüm/metric çerçevesi (ne ölçülür — eşik **yok**, uydurma yasak) | Model eğitimi altyapısı (var mı yok mu belirsiz) |
| Onay kapıları (veri → tasarım → ölçüm → kod → yayım) | ML model eğitimi ve feature store → `K006-yapay-zeka` |

## 3. Girdi / Çıktı haritası

| Yön | Kaynak | Kanıt/durum |
|---|---|---|
| **Girdi** | Yerel çalma geçmişi / favori / kütüphane (MySQL kataloğu, BCNF) | mevcut veri modeli varsayımı → `⚠️ VERIFY REQUIRED` (şema kanıtı K007'de) |
| **Girdi** | Çalma sinyalleri (dinleme, atlama, favori, paylaşma) | repo kodu bağlam alır (`AIEngine.php:49-57` — `history`, `mood`, `genre`) — **log şeması yok** → `⚠️` |
| **Girdi** | Dış katalog keşfi (`../K020-discovery/`) | keşif notu — **entegrasyon onayı yok** (YouTube Music API yok) |
| **Girdi** | Kullanıcı bağlamı (cihaz/saat/ortam/mood) | repo kodu `context['mood']` okur (`AIEngine.php:54`) — **bağlam şeması tanımsız** → `⚠️` |
| **Girdi** | Ses analiz özellikleri (BPM/key/energy/mood — `AIEngine.php:94-100`) | **mevcut** (K003-audio / K004-dsp hattı) |
| **Çıktı** | Sıralı öneri listesi (sıra + skor + gerekçe) | repo kodu döner (`AIEngine.php:76` — `array_slice`) · gerekçe alanı **yok** |
| **Çıktı** | Kişisel çalma listesi / radyo | tasarım yok → `⚠️ VERIFY REQUIRED` |

## 4. Sistem mimarisi — 6 aşamalı boru hattı (kanonik)

> **Tutarlılık düzeltmesi (2026-10-17):** önceki sürümde "4 katman" ve "6 adımlı
> pipeline" iki ayrı listeydi ve 2-4. maddeler tekrar ediyordu. Tek kanonik tanım
> aşağıdadır; mevcut repo kodu (`AIEngine.php:49-77`) **Faz-1 karşılığı** olarak
> her aşamay eşleştirilmiştir.

| # | Aşama | Ne yapar | Girdi → Çıktı | Faz-1 repo karşılığı |
|---|---|---|---|---|
| **S1** | **Aday üretimi** (candidate generation) | Katalogdan binlerce adayı yüzlerce adaya indirir | katalog + profil → aday havuzu | yok (repo doğrudan skorlar — bkz §13 boşluk B1) |
| **S2** | **Skorlama** (scoring/ranking) | Her adayı hibrit skorla puanlar | aday havuzu + sinyaller → skorlu liste | `AIEngine.php:58-65` (collab 0.6 + content 0.4) |
| **S3** | **Yeniden sıralama** (re-rank) | Çeşitlilik + iş kuralları + yasaklar | skorlu liste → nihai sıra | `AIEngine.php:67-71` (diversity + `MIN_SCORE`) |
| **S4** | **Sunum** (serving) | Listeyi API/SDK ile iletir, önbellekler | nihai sıra → istemci yanıtı | `AIEngine.php:73-76` (sırala + limit) |
| **S5** | **Geri bildirim** (feedback loop) | Kullanıcı davranışını toplar, S1'e akıtır | dinleme/atlama/favori → olay logu | **yok** → `⚠️ VERIFY REQUIRED` |
| **S6** | **Ölçüm ve izleme** (evaluation & monitoring) | Offline metrik + online deney + sürüklenme | olay logu + sıra → metrik + karar | **yok** → `[[evaluation/index]]` |

**Akış:** `S1 → S2 → S3 → S4 → S5 → S6 ─(akış)→ S1` (kapalı döngü).
S6 tanımlanmadan S4 yayına alınamaz (süreç kuralı — §9 K4/K5).

```text
[Katalog + Çalma Geçmişi + Ses Özellikleri + Bağlam]
        │
        ▼
   ┌─────────┐   ┌─────────┐   ┌──────────────┐   ┌─────────┐
   │  S1     │──▶│  S2     │──▶│  S3          │──▶│  S4     │──▶ kullanıcı
   │ aday    │   │ hibrit  │   │ çeşitlilik + │   │ API +   │
   │ üretimi │   │ skor    │   │ iş kuralı    │   │ önbellek│
   └─────────┘   └─────────┘   └──────────────┘   └────┬────┘
        ▲                                              │
        │            ┌──────────────┐   ┌─────────┐    │
        └─── akış ───│  S6          │◀──│  S5     │◀───┘
                     │ ölçüm/izleme │   │ geri    │
                     └──────────────┘   │ bildirim│
                                        └─────────┘
```

## 5. Alt sistemler

| Alt sistem | Sorumluluk | Soru | Dosya |
|---|---|---|---|
| **Ranking** | Adayları **sıralamak** — ne üstte çıkacak? | kural tabanlı mı, skor mu? | `[[ranking/index]]` |
| **Personalization** | Kullanıcıya **özel kılmak** — profile göre uyarlamak | profil nerede tutulur, KVKK kapsamı? | `[[personalization/index]]` |
| **Evaluation** | **Ölçmek** — öneri gerçekten işe yaradı mı? | hangi metrik, hangi eşik? | `[[evaluation/index]]` |

> Sıra **zorunlu**: kişiselleştirme olmadan sıralama yapılabilir; sıralama olmadan
> ölçüm **anlamsız**dır. Değerlendirme en sona konur, ama **kapı olarak** en baştan
> tanımlanır (ölçülmeyen öneri yayına alınamaz — süreç kuralı, tasarım iddiası değil).

## 6. Komşu sınırı (kim neyin sahibi)

| Komşu K | İlişki | Sınır (bu dosyanın sahip olmadığı) |
|---|---|---|
| `K006-yapay-zeka` | ML ürün tarafı — model eğitimi, feature çıkarma | **model eğitimi, eğitim veri hattı, feature store** K006'da (`../../K006-yapay-zeka/recommendation/`) |
| `K007-data` | Şema/şema kalitesi — öneri verisinin kaynağı | **tablo şeması, migration, indeks** K007'de |
| `K008-domain` | Entity/ValueObject/domain event | **domain entity persistence** K008'de |
| `K009-security` | Şifreleme, erişim kontrolü, oturum | **auth/CSRF/rate-limit** K009 + `shared/src/Middleware/` |
| `K015-AI` | Ajan altyapısı (Orchestrator, PromptEngine, ToolCalling) | **ajan hattı** K015'te — öneri ≠ ajan |
| `K020-discovery` | Dış servis keşfi (Spotify PKCE, Apple JWT) | **dış katalog erişimi ve entegrasyon onayı** discovery'de |
| `K019-comilance` | KVKK/GDPR kapsam kararı | **kişisel veri işleme hukuku** comilance'da |

> **Açık sahiplik çakışması (korunuyor, düzeltilmedi):** öneri motoru K006 (ML ürün)
> mü, K015 (ajan altyapısı) mı, K021 (bu program) mı — üçlü ayrım **ADR gerektirir**
> (§12 madde 3). Bu dosya yalnızca **program/sınır/kapı** sahibidir; uygulama kodu
> (`shared/src/AI/AIEngine.php`) repo kanıtı olarak okunur, **sahiplik iddiası değildir**.

## 7. Veri sinyalleri ve gizlilik sınırı

| Sinyal türü | Örnekler | Durum |
|---|---|---|
| **Açık (explicit)** | favori, beğeni, oy, "bu ilgimi çekmiyor" | tasarım yok → `⚠️` |
| **Örtük (implicit)** | dinleme süresi, tamamlama oranı, atlama, tekrar dinleme, paylaşma | repo `history` bağlamı okur (`AIEngine.php:56`) — **log şeması yok** → `⚠️` |
| **Bağlam (context)** | mood, genre, cihaz, saat | repo `context['mood']` / `context['genre']` okur (`AIEngine.php:54-55`) — **şema yok** → `⚠️` |
| **Ses özelliği** | BPM, key, energy, danceability, acousticness | **mevcut** — `AIEngine.php:94-100` (K003/K004 hattı) |

**Gizlilik sınırı (bağlayıcı):** kişiselleştirme profili = kişisel veri işleyebilir.
KVKK/GDPR kapsamı **tanımsız** (`K020-comilance` açık sorusu) → kapsam kararı
verilmeden profil şeması **yazılmaz**, veri **toplanmaz** (§9 K1).

## 8. Ölçüm çerçevesi (tanım — eşik YOK)

> **Zero-hallucination:** aşağıdaki metrik **isimleri** çerçevenin parçasıdır;
> **hiçbir sayısal eşik yazılmamıştır**. Eşikler ancak veri seti + ölçüm sonrası
> ADR ile atanır (`[[evaluation/index]]` §4).

| Katman | Ne ölçülür | Ne için | Kaynak |
|---|---|---|---|
| **Offline** | Precision@K · Recall@K · NDCG@K · MAP · MRR | sıra kalitesi (geçmiş veriyle) | scikit-learn `ndcg_score` · `precision_recall_fscore_support` (HTTP 200 doğrulandı — `recommendation-arastirma.md` §2) |
| **Çeşitlilik/kapsam** | Intra-list diversity · catalog coverage · novelty | filter bubble ve keşif | repo `DIVERSITY_TARGET = 0.30` zaten var (`AIEngine.php:32`) |
| **Online** | A/B deneyi — tamamlama oranı, atlama oranı, oturum süresi | gerçek kullanıcı etkisi | Evan Miller A/B kaynağı (HTTP 200) |
| **İzleme** | Skor dağılımı, boş çıkma (cold-start), hata oranı, sürüklenme | üretim sağlığı | Vertex AI model-monitoring kaynağı (HTTP 200) |

## 9. Onay kapıları (adımlar)

```text
① Veri kaynağı onayı (KVKK kapsamı + hangi veri)   → K020-comilance (VERIFY REQUIRED)
        ↓
② Öneri tasarımı (S1–S4 boru hattı + yöntem)        → ADR gerektirir (K020-adr)
        ↓
③ Ölçüm tanımı (evaluation metrikleri + veri seti)  → [[evaluation/index]] (eşik ADR'de)
        ↓
④ Kod (kademe 5–8)                                  → yalnız ①②③ onaylıysa
        ↓
⑤ Yayım (canlıya alma + izleme)                     → yalnız ④ test edildiyse
```

- Kapılardan biri `VERIFY REQUIRED` ise **④ başlamaz**.
- Metrik/eşik **uydurulmaz** — ölçüm yapılmadan sayı yazılmaz (zero-hallucination).

## 10. Faz planı (durum etiketleri)

| Faz | Ne | Kanıt/durum |
|---|---|---|
| **Faz 1 — mevcut çekirdek** | Hibrit skorlama + çeşitlilik + min-skor + sıralama | ✅ **repo kodu var** — `AIEngine.php:49-77` |
| **Faz 2 — aday üretimi (S1)** | Aday havuzu katmanı (katalog + co-olearşim + trend) | ❌ yok → `⚠️` (bkz §13 B1) |
| **Faz 3 — geri bildirim (S5)** | Olay logu şeması + akış | ❌ yok → `⚠️` |
| **Faz 4 — ölçüm (S6)** | Offline metrik hattı + online deney | ❌ yok → `[[evaluation/index]]` |
| **Faz 5 — öğrenen sıralama** | Kural/skor → öğrenen model geçişi | ❌ yok — **Faz 4 sonrası** ADR (K006 iş birliği) |

> Faz etiketleri **durum** işaretidir; her faz için başarı ölçütü tanımsız sayılmaz:
> Faz-2 "başarılı" demek için Faz-4 metrikleri **tanımlı ve ölçülmüş** olmalıdır.

## 11. Riskler

1. **Ölçümsüz yayım** — metrik yokken "öneri iyi" iddiası kanıtsız olur (yasak).
2. **Gizlilik** — kişiselleştirme, KVKK kapsamı tanımsızken başlanırsa geri alınamaz maliyet.
3. **Kapsanmayan dış kaynak** — YouTube Music resmi API'siz (bkz. `../K020-discovery/`); katalog boşluğu öneri kalitesini doğrudan düşürür.
4. **Kapsam kayması** — öneri motoru K006 (ML ürünü) mü, K015 (ajan altyapısı) mı: ayrım netleşmedi (§12 madde 3).
5. **Sıfır aday üretimi** — Faz-1 kodu S1 olmadan çalışır; küçük katalogda "her şey önerilir" riski (§13 B1).
6. **Ağırlık dondurma** — `0.6/0.4` sabit ağırlık (`AIEngine.php:65`) veriyle doğrulanmadı; yanlış ağırlık sessiz kalite kaybı üretir → Faz-4 ölçümüne bağlandı.

## 12. Açık sorular (VERIFY REQUIRED)

| # | Soru | Etiket |
|---|---|---|
| 1 | Öneri kişisel veri işliyor mu? KVKK/GDPR kapsamı tanımsız (`K020-comilance`) | `⚠️ VERIFY REQUIRED` |
| 2 | Veri kaynağı: yerel geçmiş mi, dış servis mi, ikisi mi? | `⚠️ VERIFY REQUIRED` |
| 3 | Öneri motoru K006 (ML ürün) ile mi, K015 (ajan altyapısı) ile mi ilişkili? — v2 §2.6/§2.16 kapsam ayrımı + bu dosya §6 | `⚠️ VERIFY REQUIRED` |
| 4 | Ölçüm metrikleri ve eşikleri ne olacak? (uydurma eşik yazılmaz) | `⚠️ VERIFY REQUIRED` |
| 5 | ⚠️ ID CONFLICT: klasör diskte `K021-recomenataion` (registry Band-2 kapısı ADR-098 öncesi — bkz §17) | `⚠️ VERIFY REQUIRED` |
| 6 | Aday üretimi (S1) hangi stratejiyle? (item-item CF / içerik benzerliği / trend karışımı) | `⚠️ VERIFY REQUIRED` — ADR-2 kapısı |
| 7 | `0.6/0.4` hibrit ağırlıkları hangi ölçümle doğrulanacak? | `⚠️ VERIFY REQUIRED` — Faz-4 kapısı |

## 13. Repo kanıtları (okunan, uydurulmayan)

| Kanıt | Dosya:satır | Ne gösterir |
|---|---|---|
| `MAX_RECOMMENDATIONS = 20` | `shared/src/AI/AIEngine.php:30` | öner limiti sabiti |
| `MIN_SCORE = 0.7` | `shared/src/AI/AIEngine.php:31` | minimum skor eşiği (repo değeri — **tasarım onayı yok**) |
| `DIVERSITY_TARGET = 0.30` | `shared/src/AI/AIEngine.php:32` | çeşitlilik hedefi (repo değeri) |
| 6 adımlı hibrit akış | `shared/src/AI/AIEngine.php:49-77` | collab → content → hybrid → diversity → filter → sort |
| Ağırlıklar `0.6 / 0.4` | `shared/src/AI/AIEngine.php:65` | sabit hibrit ağırlık |
| Ses özellikleri çıkarımı | `shared/src/AI/AIEngine.php:94-100` | bpm/key/energy/mood/danceability/acousticness |

**Boşluklar (kodda görülen eksik — kanıtla):**

| # | Boşluk | Kanıt |
|---|---|---|
| B1 | **Aday üretimi (S1) yok** — motor doğrudan skorlar; aday havuzu katalog sınırına bağlı değil | `AIEngine.php:58-62` — iki skorlayıcı doğrudan çağrılır |
| B2 | **Geri bildirim (S5) yok** — dinleme/atlama olayı bu motora geri bağlanmaz | dosyada geri bildirim API'si yok (382 satır okundu) |
| B3 | **Ölçüm (S6) yok** — metrik hesabı, log kaydı yok | dosyada ölçüm kodu yok |
| B4 | **Gerekçe alanı yok** — çıktı `(item, score)`; "neden önerildi" alanı üretilmez | `AIEngine.php:76` — `array_slice` çıktısı |

## 14. Web araştırması özeti

Tam tablo, HTTP durumları ve dürüst elenme kayıtları → **`recommendation-arastirma.md`**
(`group: kanit`). Özet (2026-10-17, 15 kaynak HTTP 200 doğrulandı):

- İki aşamalı mimari (retrieval → ranking) endüstri standardı olarak doğrulandı (Google ML Recommendation dev guide · TF Recommenders · Microsoft `recommenders` repo — hepsi HTTP 200).
- Offline metrik uygulamaları scikit-learn ile doğrulanabilir (`ndcg_score`, `precision_recall_fscore_support` — HTTP 200).
- Online A/B istatistiği ve üretim izleme kalıpları doğrulandı (Evan Miller A/B · Vertex AI evaluations · model monitoring — HTTP 200).
- **Elendi (404 ×9):** TF Recommenders `/examples` · Microsoft Learn `microsoft.ai.evaluation` · `microsoft.github.io/recommenders` · `gdpr.eu` dayanak sayfası · Google `/content-based` · 4 blog/egitim URL'si → **cite edilmez**.
- **Erişim engelli (403 ×4):** ACM DL (3 DOI) + Netflix Tech Blog (Medium bot koruması) → URL gerçek, içerik okunamadı; **içerik iddiası yazılmaz**.
- Sayım düzeltmesi (Truth Mode): ilk yazım "17 URL / 6×404" yanlıştı → **28 URL = 15 + 9 + 4** (arastirma.md §0-§1).

## 15. Kod örnekleri özeti

`recommendation-kod-ornekleri.md` (`group: kod`) — **22 dil** matrisi: PHP · JavaScript ·
TypeScript · Python · C · C++ · C# · Go · Rust · Java · Swift · Kotlin · SQL · Bash ·
PowerShell · YAML · HCL · Ruby · Scala · Elixir · Dart · R. Her örnek bu dosyadaki
S1–S6 boru hattının bir aşamasını gösterir; hiçbirinde **eşik/skor uydurulmaz**
(sabitler repo kanıtına bağlanmıştır).

## 16. §BAĞLANTILAR (bu grup içi — 5+2 dosya)

| Hedef | Wiki-link | Göreli dosya yolu | İlişki |
|---|---|---|---|
| Klasör künyesi | `[[index]]` | `index.md` | bu grubun kapısı ve çocuk kataloğu |
| Ranking | `[[ranking/index]]` | `ranking/index.md` | S2/S3 sıralama alt bölgesi |
| Personalization | `[[personalization/index]]` | `personalization/index.md` | profil ve uyarlama alt bölgesi |
| Evaluation | `[[evaluation/index]]` | `evaluation/index.md` | S6 ölçüm alt bölgesi |
| Kod örnekleri | `[[recommendation-kod-ornekleri]]` | `recommendation-kod-ornekleri.md` | 22 dil uygulama referansı |
| Araştırma | `[[recommendation-arastirma]]` | `recommendation-arastirma.md` | kaynak kanıtı + HTTP durumları |
| Dış komşu | `[[../K020-discovery/index]]` | `../K020-discovery/index.md` | dış katalog keşfi |
| Dış komşu | `[[../../K006-yapay-zeka/recommendation/recommendation-kapsam]]` | `../../K006-yapay-zeka/recommendation/recommendation-kapsam.md` | ML tarafı kapsam sınırı |

> K000–K013 arası bağlantılar **kapsam dışı bırakıldı** (kullanıcı kararı, 2026-10-17).

## 17. Doğrulama kaydı

| Tarih | Ne doğrulandı | Sonuç |
|---|---|---|
| 2026-10-12 | v2 §2.20 şekli birebir (5 öğe) | ✅ |
| 2026-10-17 | 28 URL HTTP kontrolü (curl + UA, 2 tur) | 15×200 · 9×404 (elendi) · 4×403 (erişim engelli) |
| 2026-10-17 | Repo kanıtları `AIEngine.php` okunarak tazelendi | §13 — 6 kanıt + 4 boşluk |
| 2026-10-17 | ID `K020-` → `K021-` dönüşümü (frontmatter + gövde + linkler) | §12 madde 5'te kapı kaydı açık |

## 18. API sözleşmesi iskeleti (S4 — tanım, üretim onayı yok)

> Sadece **şekil** tanımıdır; alan adları/eşikler ADR-2 sonrası kesinleşir.
> HTTP semantiği: erişim reddi **403** (RFC 9110 + MDN — HTTP 200 doğrulandı).

```json
GET /api/v1/recommendations?user_id={id}&limit={n}&context={mood|device}

{
  "items": [
    {
      "item_id": "track-42",
      "score": 0.83,
      "source": "collaborative",          // aday kaynak etiketi (S1)
      "reason": "similar_listeners",       // gerekçe alanı — §13 B4: Faz-1'de YOK
      "rank": 1
    }
  ],
  "generated_at": "2026-10-17T00:00:00Z",
  "personalized": false                    // profil yoksa false (fail-safe)
}
```

| Alan | Zorunlu mu | Not |
|---|---|---|
| `items[].item_id` | evet | katalog kimliği (K007) |
| `items[].score` | evet | hibrit skor (S2) |
| `items[].reason` | ADR-2 | Faz-1'de yok (§13 B4) — ekleme kararı kapalı |
| `personalized` | evet | KVKK/gizlilik şeffaflığı — profil yoksa `false` |
| `generated_at` | evet | önbellek/izleme |

**Hata yanıtı şekli (aynı sözleşme):**

```json
GET /api/v1/recommendations?user_id={id}
HTTP/1.1 200 OK
{
  "items": [],
  "personalized": false,
  "fallback": "catalog-empty",        // neyin fallback olduğu — uydurma içerik YOK
  "generated_at": "2026-10-17T00:00:00Z"
}
```

| Hata durumu | Kod | Davranış |
|---|---|---|
| Katalog boş / aday yok | 200 + `items: []` | boş liste **uydurma öneri ile doldurulmaz** |
| Kimlik doğrulanamadı | 401 | öneri verilmez (K009 sınırı) |
| Erişim reddi (KVKK/kapsam) | 403 | RFC 9110 semantiği (§14 kaynak 13-14) |
| Sunum hatası | 500 | istemci boş listeyle devam eder (fail-safe) |

## 19. Hata ve azaltma senaryoları (S1–S6)

| # | Senaryo | Etkilenen aşama | Azaltma (fail-safe) |
|---|---|---|---|
| 1 | Aday havuzu boş (katalog dar / S1 yok) | S1 | boş liste + `personalized:false` — **uydurma öneri YOK** |
| 2 | İşbirlikçi veri yapısı boş (yeni sistem) | S2 | content-only skor (`W_CONTENT` tek başına) — ADR-2 kararı |
| 3 | Profil okuma hatası | S1/S2 | kişiselleştirme atlanır, genel sıralama korunur |
| 4 | KVKK kapsamı daraltıldı / kullanıcı sildi | S5/S1 | profil + özetler imha → cold-start |
| 5 | Skor dağılımı çöktü (hepsi eşiğin altında) | S3 | `MIN_SCORE` filtresi boşluk üretir → S6 izleme alarmı (§21) |
| 6 | Ölçüm verisi yok (S5 henüz kurulmadı) | S6 | **yayım yasak** (§9 K4/K5) — sessiz geçiş yok |
| 7 | Dış katalog erişimi kesildi (K020-discovery) | S1 | yerel katalog ile devam; dış zenginleştirme bekler |

## 20. Test stratejisi (kapı tanımı — uygulama K019-testing / diskte `K18-testing`)

| Katman | Ne test edilir | Kanıt türü |
|---|---|---|
| Birim | skor birleştirmesi (0.6/0.4), min-skor filtresi, limit | saf fonksiyon testi — `AIEngine.php:49-77` davranışının korunması |
| Birim | çeşitlilik kotası (`DIVERSITY_TARGET=0.30`) | listede tür dağılımı assertion'ı |
| Birim | fail-safe (profil yok / hata) | kişiselleştirme atlama davranışı |
| Entegrasyon | S1 aday üretimi ↔ katalog şeması | K007 şema sözleşmesi |
| Sözleşme | §18 API yanıtı | JSON şema doğrulama |
| Offline metrik | S6 NDCG/Precision döngüsü | veri seti üzerinde çalışır çıktı (eşik ADR-3) |
| Online deney | A/B split bütünlüğü | sızıntı/çakışma denetimi |

## 21. Performans ve izleme metrikleri (tanım — eşik YOK)

> Aşağıdakiler **izlenecek sinyallerdir**; eşikler kapasite ölçümü + Faz-4 sonrası
> atanır (uydurma sayı yasak).

| Sinyal | Neden izlenir | Aşama |
|---|---|---|
| Yanıt süresi (p50/p95) | sunum (S4) bütçesi | S4 |
| Aday havuzu boyutu | S1 sağlığı (daralma = katalog/olay kaybı) | S1 |
| Boş çıkma oranı | §19 madde 1/5 alarmı | S3/S4 |
| Skor dağılımı kayması | sürüklenme (Vertex AI monitoring deseni — HTTP 200) | S6 |
| Önbellek isabet oranı | maliyet + gecikme | S4 |
| Olay akışı gecikmesi (S5→S1) | döngü tazeliği | S5 |

## 22. Değişiklik günlüğü

| Tarih | Değişiklik |
|---|---|
| 2026-10-12 | İlk iskelet (v2 §2.20 şekli birebir) — v1.0.0 |
| 2026-10-17 | v2.0.0 — ID `K021-` dönüşümü · S1–S6 kanonik boru hattı (eski "4 katman/6 adım" çelişkisi giderildi) · repo kanıtları (§13) · komşu sınırı (§6) · 28 URL doğrulamalı araştırma (§14) · 22 dil kod matrisi (§15) · API sözleşmesi/hata/test/izleme (§18–§21) |

## 23. Aşama sözleşmeleri (S1–S6 — girdi/çıktı + başarısızlık davranışı)

| Aşama | Girdi sözleşmesi | Çıktı sözleşmesi | Başarısızlık davranışı |
|---|---|---|---|
| S1 aday üretimi | katalog + (varsa) etkileşim komşuluğu + bağlam | aday kümesi (`item_id` + kaynak etiketi) | katalog boşsa → **boş liste** (uydurma yok) |
| S2 skorlama | aday kümesi + özellikler (F1–F6) | `item_id → skor` eşlemesi | özellik eksikse → o özellik **atlanır**, ağırlıklar yeniden normalize (ADR-2) |
| S3 re-rank | skorlu liste + çeşitlilik/iş kuralı | nihai sıralı liste (≤ `MAX_RECOMMENDATIONS`) | kota çakışırsa → skor önceliği korunur |
| S4 sunum | nihai sıra | API yanıtı (§18) + önbellek | önbellek hatası → hesaplama yine de döner (fail-safe) |
| S5 geri bildirim | istemci olayları (dinleme/atlama/favori) | olay kaydı (S1'e akış) | olay kaydedilemez → **sessizce kaybolmaz**, kuyruk/log'a düşer |
| S6 ölçüm/izleme | sıra + olay kayıtları | metrik raporu + karar | veri yoksa → **yayım durur** (sessiz geçiş yasak) |

## 24. Faz-1 kod okuma dökümü (`shared/src/AI/AIEngine.php:49-77`)

| Satır | Ne yapıyor | Aşama karşılığı |
|---|---|---|
| 51 | `$limit = min($limit, MAX_RECOMMENDATIONS)` — limit sınırı | S4 |
| 52-56 | bağlam ayrıştırma: `user_id`, `session_id`, `mood`, `genre`, `history` | S1/S2 girdisi |
| 59 | `getCollaborativeScores()` çağrısı | S2 (collab) |
| 62 | `getContentBasedScores()` çağrısı | S2 (content) |
| 65 | `combineScores(..., 0.6, 0.4)` — hibrit ağırlık | S2 |
| 68 | `applyDiversityFilter()` — çeşitlilik | S3 |
| 71 | `array_filter(... score >= MIN_SCORE)` — eşik filtresi | S3 |
| 74 | `usort(... $b['score'] <=> $a['score'])` — skora göre sıralama | S3 |
| 76 | `array_slice(..., 0, $limit)` — çıktı kesme | S4 |

> **Gözlem:** dosyada S1 (aday havuzu) ve S5 (geri bildirim toplama) **yok** (§13 B1/B2) —
> mevcut kod S2→S3→S4 segmentidir; bu okuma Faz-2/Faz-3 kapsamını kanıtlar.

### §24.1 Okuma gözlemleri (dikkat — tasarım kararı değil, tespit)

| # | Gözlem | Etki |
|---|---|---|
| 1 | Sıra: collab önce, content sonra (satır 59/62) — birleştirme `combineScores`'ta | sıra değil, **ağırlık** belirleyici (satır 65) |
| 2 | `MIN_SCORE` filtresi **sort ÖNCESİ** (satır 71 → 74) | eşiğin altı hiç sıralanmaz — performans artısı, davranış notu |
| 3 | `DIVERSITY_TARGET` sabiti `AIEngine.php:32`'de tanımlı ama okuma sırasında `applyDiversityFilter` **içindeki gerçek kullanım satırı dosyanın 110+ satırında** (okuma bütçesi dışında) | çeşitlilik uygulamasının ayrıntısı için dosyanın tamamı okunmalı — **iddia yazılmadı** |
| 4 | `$limit` hem sabit (`MAX_RECOMMENDATIONS=20`) hem parametre | istemci 20'yi aşamaz (satır 51) |
| 5 | `context['user_id'] ?? null` — kimlik **opsiyonel** | misafir/anonymous akış mümkündür → kişiselleştirme kapalı çalışır |

## 25. Karar defteri (bu program için)

| # | Karar | Durum | Sahip |
|---|---|---|---|
| D1 | Boru hattı kanonik tanımı S1–S6 (bu dosya §4) | ✅ alındı (2026-10-17) | Vault Steward |
| D2 | Sıralama yöntemi (A kural / B skor / C öğrenen) | ⏸ bekliyor — ADR-2 | `K020-adr` |
| D3 | Metrik seçimi + eşikler | ⏸ bekliyor — ADR-3 | `evaluation/` + ADR |
| D4 | KVKK kapsamı + hukuki dayanak | ⏸ bekliyor — K1 | `K019-comilance` |
| D5 | `0.6/0.4` ağırlık doğrulaması | ⏸ bekliyor — Faz-4 ölçümü | Vault Steward + K006 |
| D6 | Klasör ID'si (K021 vs 40+ K020 referansı) | ⏸ üst karar | Vault Steward + kullanıcı |

## 26. Sözlük (bu dosyaya özgü terimler)

| Terim | Tanım |
|---|---|
| Aday üretimi (S1) | kataloğdan binlerce adayı yüzlerce indirme katmanı |
| Hibrit skor | işbirlikçi + içerik-bazlı skorun ağırlıklı bileşimi (repo: 0.6/0.4) |
| Re-rank (S3) | skorlu listeye çeşitlilik/iş kuralı uygulama |
| Cold-start | verisi olmayan kullanıcı/içerik için kişiselleştirme kapalı durum |
| Filter bubble | kişiselleştirmenin monopoliye dönmesi — coverage/novelty ile denetlenir |
| NDCG@K | sıralama kalitesi metriği (sıra duyarlı, 200 doğrulamalı scikit-learn kaynağı) |
| A/B deneyi | kullanıcıları kontrol/deney olarak ayırıp gerçek davranış farkı ölçme |
| Sürüklenme | zamanla veri/model dağılımının kayması — S6 izleme sinyali |
| Fail-safe | profil/veri yokken kişiselleştirmeyi **atlayıp** genel sıralamayı koruma |
| Eşik | geç/kal sayısı — bu vaultta **atanmadı**, ADR-3 kapısı |
| Gerekçe (reason) | önerinin "neden" alanı — Faz-1'de yok (§13 B4) |
| Olay şeması | dinleme/atlama/favori kaydının alanları — S5 kapısı, tanımsız |
| Fail-open | hatada devam eden (güvensiz) davranış — bu programda **yasak**, fail-safe kullanılır |
| Adressizlik | öneri verisiz/kimliksiz çalışabilme (misafir akışı — §24.1 madde 5) |

## 27. Kapı checklist'i (özet — tek bakış)

| Kapı | Ne gerekiyor | Durum |
|---|---|---|
| K1 — veri kapsamı | KVKK/GDPR kararı | `⚠️` |
| K2 — veri kaynağı | gerçek şema/log kanıtı | `⚠️` |
| K3 — yöntem (ADR-2) | sıralama stratejisi kararı | ⏸ |
| K4 — ölçüm (ADR-3) | metrik + eşik + veri seti | ⏸ |
| K5 — yayım | K1–K4 kapalı + test raporu | ⏸ |

> K1 açıkken K2/K3 kararları **kısıtlı** (veri kapsamı belirsizken yöntem seçmek
> spekülatif olur). Sıra: K1 → K2 → K3 → K4 → K5.

## 28. SSS (sık sorular)

| Soru | Cevap |
|---|---|
| Algoritma kodu bu dosyada mı? | Hayır — sınır/kapı burada; kod referansı `recommendation-kod-ornekleri.md` (22 dil), üretim kodu ADR sonrası `shared/src/` |
| Eşikler neden yok? | Zero-hallucination: ölçüm verisi olmadan sayı **uydurulmaz** (K4/ADR-3) |
| K006 ile çakışıyor mu? | K006 ML **ürün/egitim** tarafı, bu program **sınır/kapı** tarafı — sahiplik ayrımı ADR bekliyor (§12 madde 3) |
| Neden iki aşama (retrieval+ranking)? | Doğrulanmış endüstri deseni (§14 — Google/TF/Microsoft kaynakları HTTP 200) |
| Kişiselleştirme hemen başlar mı? | Hayır — KVKK kapsamı (K1) kapalıyken profil toplanmaz (§7) |

## 29. Uçtan uca örnek akış (bir öneri isteğinin hayatı)

```text
Kullanıcı "Yeni bir şeyler dinle" der
  │
  ├─ S1  aday üretimi: katalog + (varsa) etkileşim komşuluğu + bağlam
  │        └─ Faz-1'de BU ADIM YOK (§13 B1) → katalog doğrudan skorlanır
  ├─ S2  skorlama: 0.6·collab + 0.4·content (§24 satır 65)
  │        └─ özellik eksikse o özellik atlanır (§23)
  ├─ S3  re-rank: DIVERSITY_TARGET=0.30 + MIN_SCORE=0.7 (§24 satır 68,71)
  ├─ S4  sunum: sırala + limit 20 + API yanıtı (§18) + önbellek
  │        └─ kişiselleştirme yoksa personalized:false (§18)
  ├─ S5  geri bildirim: dinleme/atlama olayı toplanır (§19 madde 4)
  │        └─ Faz-1'de BU ADIM YOK (§13 B2) → Faz-3 kapsamı
  └─ S6  ölçüm: NDCG@K + A/B + izleme (§8, evaluation/index.md)
           └─ Faz-1'de BU ADIM YOK (§13 B3) → Faz-4 kapsamı, K5 kapısı
```

## 30. Bağımlılık grafiği (dosya/komşu düzeyi)

```text
                     K019-comilance ──(K1 veri kapsamı)──┐
                             │                            │
K020-discovery ──(dış katalog)─┤                            ▼
                             │              K021-recomenataion (bu program)
K007-data ──(şema/olay)────────┤                  │
                             │        ┌───────────┼───────────────┐
K006-yapay-zeka ─(ML tarafı)──┤        ▼           ▼               ▼
                             │   ranking/   personalization/  evaluation/
K009-security ──(auth/CSRF)───┤        │           │               │
                             │        └─────┬─────┘               │
K020-adr ──(ADR-2/ADR-3)──────┘            ▼                       │
                                    S2/S3 sıralama ────────────────┘
                                            │ (S6 ölçer)
                                            ▼
                              shared/src/AI/AIEngine.php (Faz-1 kanıtı)
```

## 31. İlgili ADR / karar referansları

| Kaynak | Ne verir | Kullanım |
|---|---|---|
| `@.accepted/ADR-030` | YZ stratejisi çerçevesi | §1 authority |
| `@.accepted/ADR-097 §5` | isimlendirme + iki-ekseni şema (`group` + §GRUPLAR) | bu dosyanın frontmatter'ı |
| `@.accepted/ADR-029` · `ADR-025` | dinleme odaları / EQ — ilgili sosyal kararlar | sınır notları |
| ADR-098 (henüz yazılmadı) | Band-2 açılışı + K021 ID kararları | §12 madde 5 (açık kapı) |
| K006 `recommendation-kapsam.md` | ML tarafı kapsam sınırı | §6 komşu sınırı |
| `.ai/architecture/index.md` §4 | Band kayıt cetveli (Band-2 = K021–K120, ADR-098 kapısı) | §12 madde 5 |
| `.ai/architecture/index.md` §9.13 | kanonik ölçüm + kod örnekleri/arastirma geçiş örneği | §15 formatı |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
