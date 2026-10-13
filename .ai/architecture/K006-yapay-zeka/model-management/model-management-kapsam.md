---
title: "Model Management — Kapsam"
type: architecture-scope
node_id: K006-yapay-zeka/model-management
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# Model Management — Kapsam

**ELI15:** Model yönetimini, eğitilmiş bir yapay zeka modelinin **kütüphane kartopu** gibi düşün: kartopu (model) nerede saklandı, hangi sürümü eski hangisi yeni, kim onu ne zaman yaptı ve üretime çıkarken hangi kartopu kopyası kullanıldı — hepsini tek bir kayıt defteri tutar. Model Management bu defteri ve saklama sistemini yönetir: kayıt, sürümleme, dağıtım (deploy) ve geri alma. Bu alt alan, **modelin yaşam döngüsünü** (eğit → kaydet → değerlendir → yayınla → geri al) tek yerden yönetmeyi hedefler. K006'nın çocuk tablosunda (ai-master.md §3) 9. ve son sırada tanımlıdır.

## 1. Kapsam içi (İçinde)

1. **Model kayıt defteri (registry)** — eğitilen her model için kayıt: hangi model, hangi sürüm, hangi eğitim verisi, hangi metrikle kaydedildi; kayıt şeması (model adı · sürüm · tarih · metrik · artifact yolu).
2. **Sürümleme ve etiketleme** — model sürümleri (v1, v2, …) ve canlı/taslak etiketleri (`Production`, `Staging`, `Archived`); hangi sürümün üretime alındığının izlenebilirliği.
3. **Artifact saklama** — model dosyalarının (ağırlık, ONNX grafiği, vektör indeksi) nerede saklandığı ve nasıl yüklendiği; artifact boyutu ve erişim hakkı.
4. **Dağıtım (deploy) ve rollback** — modelin servise bağlanması (inference ucuna takılması) ve geri alma; yeni sürümün eski sürümün yerini alması.
5. **Model envanteri** — sistemde kaç model olduğu, hangi domain'in (M01–M12) hangi modeli kullandığı; envanter (ai-master.md §1 sorumluluk tablosu) ile uyum.

## 2. Kapsam dışı (Dışında)

1. **Modelin eğitimi** (hype loop içinde) — eğitim hiperparametreleri ve eğitim kodu K006'ya aittir; burada yalnız kayıt ve sürümleme.
2. **Ajan / zeka altyapısı (K016-AI)** — ajan orkestrasyonu (planner/executor) K016'a aittir (K006 sınırı, `v2.md §2.6`); bu alt alan ajan değil model yaşam döngüsü yönetir.
3. **Inference çalışma zamanı** — canlı tahmin yürütme `inference/` alt alanındadır (QPS, stream, pipeline); burada yalnız "hangi model yüklensin" kararı.
4. **Feature store / eğitim veri ambarı** — veri hazırlama `audio-intelligence/` kapsamındadır.
5. **Model etik ve güvenlik denetimi** — burada yalnız envanter ve sürüm; etik/siber yönetim `security-policy.md` kapsamındadır.

## 3. Disk kanıtları

| Kanıt | Dosya / konum | Not |
|---|---|---|
| Envanter (12 domain) | `.ai/architecture/K006-yapay-zeka/ai-master.md §1` (satır 30–47) | M01–M12 sorumluluk tablosu; model adı sütunu `core-algorithm-orchestrator` |
| Envanter (9 alt alan) | `.ai/architecture/K006-yapay-zeka/ai-master.md §3` (satır 59–73) | model-management 9. satırda; çıktı "Model kayıt · dağıtım · sürüm yönetimi" |
| Bağımlılık · model | `.ai/architecture/K006-yapay-zeka/yapay-zeka-bagimliliklar.md` (satır 14) | M01 · `core-algorithm-orchestrator` — tablo satırı |
| AIEngine model referansı | `shared/src/AI/AIEngine.php` (satır 30) | `$model = 'core-algorithm-orchestrator';` — kayıt/zaman damgası yok (kanıt: satır 28–29) |
| Teknoloji belirsizliği | `.ai/architecture/K006-yapay-zeka/ai-master.md §4` (satır 87) | "Model kayıt / registry teknolojisi: UNKNOWN" |

## 4. Açık sorular

1. Model registry teknolojisi (MLflow · disk tabanlı basit registry · bulut servisi) seçilmedi — `ai-master.md §4` satır 87'de UNKNOWN.
2. Artifact saklama yeri (yerel disk · S3 benzeri object store) ve boyut bütçesi belirsiz.
3. Sürümleme şeması (semantik sürüm mü, tarih tabanlı mı) ve etiket politikası (kim `Production` işaretler) netleşmedi.
4. **Bu alt alanın kademe-2 kapsam taslağı v2'de yok** — üstteki tanımlar taslak önerisidir; v2 §2.6 / §2.4-9 ile resmileşmedi (aynı belirsizlik `music-analysis/` kapsamının 4. maddesinde de işaretli).
5. Rollback tetikleyicisi (kötü metrik · hata · manuel) ve otomatik mi manuel mi olacağı belirsiz.

## İlişkili dosyalar

- [[model-management-dogrulama]]
- [[model-management-ornekler]]
- [[../index|K006-yapay-zeka index]]
