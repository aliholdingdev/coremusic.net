---
title: "Anlamlı Kimlik Klasör Özeti - k109-semantic-id"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D03 dilimi (k108-k119)"
updated: 2026-10-06
---

# 109. Anlamlı Kimlik - `k109-semantic-id` (index)

> Dilim: D03 (k108-k119) · Klasör no: 109 · Kategori: mimari · Sürüm: 4.0.0 · Tarih: 2026-10-06
> Sorumlu persona: `data-engineer` · `backend-architect` · `security`
> Kapsadığı MD: 3 (2 içerik + index) · Kırık bağlantı hedefi: 0

## 1. Genel Bakış

Bu klasör, medya ve kullanıcı varlıklarına verilen **anlamlı kimlik (semantic id)**
tasarımını ve kimlik şeması değiştiğinde gereken **uyum/göç** stratejisini belgeler.

**Katman bağımlılığı:** K5 (kaynak kayıt + alias), K4 (`k107-embedding-models`,
`k108-vector-index` eşleme), K6 (gizlilik/denetim), K9 (API görünümü), K12 (ölçüm).
Kimlik, kaynak kaydın **türetilmiş** anahtarıdır; kaynak sistem K5'te kalır.

**Sorumluluk sınırı:** Belgeler **tasarımdır**; kimlik üreten çalışan kod, göç betiği
veya ölçüm raporu **repo'da görülmemiştir**. Tüm eşikler `⚠️ VERIFICATION REQUIRED`.

## 2. Klasör Özeti (K Tablosu)

| Numara | Ad | Amaç | Bağımlılık | Sorumlu persona | Kanıt | MD |
|---|---|---|---|---|---|---|
| 1 | Anlamlı Kimlik Tasarımı | Sözdizimi, üretim, çakışma, gizlilik | K5, K4, K9 | data-engineer, security | `⚠️ VERIFICATION REQUIRED: kimlik üretim kodu yok` | [[semantic-id-tasarim.md]] |
| 2 | Anlamlı Kimlik Uyum Stratejisi | Fazlı göç, çift yazma/okuma, geri alma, kapanış | K5, K4, K12 | data-engineer, sre-engineer | `⚠️ VERIFICATION REQUIRED: göç betiği yok` | [[semantic-id-uyum-stratejisi.md]] |

## 3. Dosya Ayrıntıları ve Kaynak Kanıtları

### 3.1 `semantic-id-tasarim.md`

- **Amaç:** `ID-001..ID-100` kural ailesi, kimlik biçimleri, şema, `SEM-*` hata kodları.
- **Persona:** `data-engineer`, `backend-architect`, `security`.
- **Bağımlılık:** K5 doğal anahtar, K4 indeks eşlemesi, K9 API.
- **Wiki-link:** [[semantic-id-tasarim.md]]
- **Çapraz referanslar:** `[[../k108-vector-index/vector-index-mimari.md]]` · `[[../k110-rag-knowledge/rag-knowledge-tabani.md]]` · `[[../k119-prompt-engineering/prompt-engineering-katmani.md]]`

| Kaynak | Bölüm | Satır | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k4-yapay-zeka/index.md` | katman listesi | L1-L124 | ◐ özet |
| `shared/src/AI/KnowledgeBase.php` | sınıf | L1-L195 | ✗ çapa |
| `⚠️ VERIFICATION REQUIRED` | üretim/eşik/alfabe kararı | - | - |

### 3.2 `semantic-id-uyum-stratejisi.md`

- **Amaç:** `SM-001..SM-040` kural ailesi, 7 faz, kapılar K1..K10, `SEM-1xx/4xx` hataları.
- **Persona:** `data-engineer`, `sre-engineer`, `backend-architect`.
- **Bağımlılık:** K5 alias, K4 reindex, K12 ölçüm, K13 iş dağıtımı.
- **Wiki-link:** [[semantic-id-uyum-stratejisi.md]]
- **Çapraz referanslar:** `[[../k108-vector-index/vector-index-operasyon.md]]` · `[[../k115-model-monitoring/model-monitoring-metrikleri.md]]` · `[[../k113-model-registry/model-registry-yasam-dongusu.md]]`

| Kaynak | Bölüm | Satır | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k4-yapay-zeka/ml-infrastructure.md` | altyapı | L1-L493 | ◐ özet |
| `⚠️ VERIFICATION REQUIRED` | kapı eşikleri, süreler | - | - |

**Aktarım anahtarı:** ✗ = yalnız çapa · ✓ = birebir · ◐ = özet · `⚠️ VERIFICATION REQUIRED` = kanıt yok.

## 4. Uçtan Uca Akış Özeti

```
[kaynak (K5)] -> [IdEncoder] -> [checksum] -> [IdAllocator (probe)]
      |                                          |
      |                                          v
      |                                   [id = ns-ver-kod-cksum]
      |                                          |
      +--> [IdAliasStore (eski->yeni)] <--------+
      |                                          |
      +--> [IdMapper -> k108 indeks]             |
      +--> [IdAuditLog]                          |
      +--> [API: display + maskeli görünüm] <----+
```

| Aşama | Sorumlu dosya | Kural |
|---|---|---|
| Üretim | tasarım | `ID-001..ID-060` |
| Doğrulama | tasarım | `SEM-101..SEM-104` |
| Çakışma | tasarım | `ID-007/008` + `SEM-301` |
| Alias | tasarım | `ID-004..ID-006` |
| Göç | uyum | `SM-001..SM-040` |
| Kapı | uyum | `K1..K10` |
| Kapanış | uyum | `SM-021..SM-024` |

## 5. D03 Dilimi Komşu Klasörler

| No | Klasör | İlişki | Yön | Link |
|---|---|---|---|---|
| 108 | `k108-vector-index` | indeks eşlemesi | id ↔ indeks | `[[../k108-vector-index/index.md]]` |
| 109 | `k109-semantic-id` | bu klasör | - | `[[index.md]]` |
| 110 | `k110-rag-knowledge` | bilgi tabanı anahtarı | id → RAG | `[[../k110-rag-knowledge/index.md]]` |
| 111 | `k111-training-pipeline` | veri kimliği | id → eğitim | `[[../k111-training-pipeline/index.md]]` |
| 112 | `k112-inference-serving` | yanıtta kimlik | id → API | `[[../k112-inference-serving/index.md]]` |
| 113 | `k113-model-registry` | sürüm kimliği | paralel alan | `[[../k113-model-registry/index.md]]` |
| 114 | `k114-onnx-runtime` | model kimliği | paralel alan | `[[../k114-onnx-runtime/index.md]]` |
| 115 | `k115-model-monitoring` | göç metrikleri | id → izleme | `[[../k115-model-monitoring/index.md]]` |
| 116 | `k116-ab-testing` | deney birimi kimliği | id → deney | `[[../k116-ab-testing/index.md]]` |
| 117 | `k117-content-safety` | PII/güvenlik | güvenlik → id | `[[../k117-content-safety/index.md]]` |
| 118 | `k118-data-labeling` | etiket ↔ kayıt | id ↔ etiket | `[[../k118-data-labeling/index.md]]` |
| 119 | `k119-prompt-engineering` | kimlik maskesi | id → prompt | `[[../k119-prompt-engineering/index.md]]` |

## 6. Katman Bağımlılık Matrisi

| Katman | İlişki | Verir | Alır |
|---|---|---|---|
| K0 çekirdek | dolaylı | - | dosya/zaman |
| K1 donanım | dolaylı | - | bellek/disk |
| K2 sürücü | hayır | - | - |
| K3 ses motoru | hayır | - | - |
| K4 yapay zeka | doğrudan | kimlik ↔ indeks | embedding boyutu |
| K5 veri yönetimi | doğrudan | alias, doğal anahtar | kalıcılık |
| K6 güvenlik | doğrudan | gizlilik/denetim | rol, kimlik |
| K7 middleware | dolaylı | - | kuyruk |
| K8 servis | doğrudan | yanıt | oturum |
| K9 API-routing | doğrudan | görünüm biçimi | yönlendirme |
| K10 uygulama | dolaylı | paylaşım URL | niyet |
| K11 UX | dolaylı | - | görünür id |
| K12 izleme | doğrudan | ölçüm | alarm |
| K13 CI/CD | dolaylı | sürüm | dağıtım |
| K14 ağ | dolaylı | - | erişim |
| K15 medya streaming | hayır | - | - |
| K16 kaynak (MCP) | hayır | - | - |

## 7. Bağımlılık ve Risk Özeti

| # | Bağımlılık | Zorunlu | Risk | Durum |
|---|---|---|---|---|
| 1 | Doğal anahtar kalitesi | evet | çakışma/bozulma | `⚠️ VERIFICATION REQUIRED` |
| 2 | Alias tablosu | evet | zincir bozulması | `⚠️ VERIFICATION REQUIRED` |
| 3 | İndeks eşlemesi | evet | mutabakat farkı | `⚠️ VERIFICATION REQUIRED` |
| 4 | Göç işi altyapısı | evet | duraklama | `⚠️ VERIFICATION REQUIRED` |
| 5 | Ölçüm eşikleri | evet | onaysız hedef | bekliyor |
| 6 | Retention politikası | evet | uyum boşluğu | bekliyor |
| 7 | Referans seti | evet | regresyon koruması | `⚠️ VERIFICATION REQUIRED` |

## 8. Bağlantı Bütünlüğü

| Kaynak | Link grubu | Adet | Kırık |
|---|---|---|---|
| `index.md` | kendi klasör | 3 | 0 |
| `index.md` | çapraz `../k1xx/index.md` | 11 | 0 |
| `semantic-id-tasarim.md` | kendi klasör | 3 | 0 |
| `semantic-id-tasarim.md` | çapraz | 5 | 0 |
| `semantic-id-uyum-stratejisi.md` | kendi klasör | 3 | 0 |
| `semantic-id-uyum-stratejisi.md` | çapraz | 5 | 0 |
| **Toplam** | - | **30** | **0** |

## 9. Kapsanan Terimler

| Terim | Orijinal identifier | Açıklama |
|---|---|---|
| Semantic ID | semantic id | Anlamlı kimlik |
| Namespace | namespace | Alan/tür adayı |
| Çekirdek kod | core code | Ana parça |
| Checksum | checksum | Denetim kodu |
| Probe | probe | Çakışma adım atlatma |
| Alias | alias | Eski → yeni |
| Halka | hop | Zincir uzunluğu |
| Siyah liste | blacklist | Yeniden kullanım yasağı |
| Doğal anahtar | natural_key | İş anahtarı |
| Göç | migration | Kimlik yenileme |
| Çift yazma | dual write | İki kimlik yazımı |
| Çift okuma | dual read | İki kimlik okuma |
| Backfill | backfill | Toplu dönüşüm |
| Checkpoint | checkpoint | Devam noktası |
| Kapı | gate | Faz geçiş kriteri |
| Arkaik pencere | grace window | Eski okuma süresi |
| Referans taraması | ref scan | Eski referans denetimi |
| Mutabakat | reconciliation | Eşitlik denetimi |
| Tersine çıkarım | reverse inference | id → kullanıcı |
| Maskeli log | masked log | Kısmi gösterim |
| Kiracı | tenant | Veri sahibi |
| Retention | retention | Saklama |
| Determinizm | determinism | Aynı girdi → aynı çıktı |
| NFC | unicode normalization | Eşdeğer yazım |
| Split | split | Kayıt bölme |
| Merge | merge | Kayıt birleştirme |
| Rollback | rollback | Geri alma |
| Canary | canary | Kademeli geçiş |
| p95 | p95 | Gecikme yüzdeliği |
| PII | personally identifiable info | Kişisel veri |

## 10. Doğrulama ve Kabul Listesi

| # | Kapı | Koşul | Durum |
|---|---|---|---|
| 1 | Dosya sayısı | 3 (2 içerik + index) | tamamlandı |
| 2 | Satır sayısı | her dosya ≥500 | doğrulanacak |
| 3 | Frontmatter | 7 alan, 4.0.0, 2026-10-06 | tamamlandı |
| 4 | Bağlantı | kırık link 0 | doğrulanacak |
| 5 | Kodlama | mojibake 0, BOM yok | doğrulanacak |
| 6 | Kanıt | her dosyada `Kanıt:` | tamamlandı |
| 7 | Hallucination | uydurma yol/yok | tamamlandı |
| 8 | Kapsam | yalnız k109 | tamamlandı |

## 11. Kanıt

- `Kanıt: .ai/architecture/k109-semantic-id/index.md` (bu dosya)
- `Kanıt: ⚠️ VERIFICATION REQUIRED — kimlik üretim/göç kodu ve ölçüm raporu repo'da doğrulanamadı`
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k4-yapay-zeka/index.md (L1-L124)`
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k4-yapay-zeka/ml-infrastructure.md (L1-L493)`
- `Kanıt: shared/src/AI/KnowledgeBase.php (L1-L195)`
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/k109-semantic-id/index.md`

## 12. Wiki-linkler

`[[index.md]]` · `[[semantic-id-tasarim.md]]` · `[[semantic-id-uyum-stratejisi.md]]` ·
`[[../k108-vector-index/index.md]]` · `[[../k110-rag-knowledge/index.md]]` ·
`[[../k111-training-pipeline/index.md]]` · `[[../k112-inference-serving/index.md]]` ·
`[[../k113-model-registry/index.md]]` · `[[../k114-onnx-runtime/index.md]]` ·
`[[../k115-model-monitoring/index.md]]` · `[[../k116-ab-testing/index.md]]` ·
`[[../k117-content-safety/index.md]]` · `[[../k118-data-labeling/index.md]]` ·
`[[../k119-prompt-engineering/index.md]]`

## 13. Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik | Sorumlu |
|---|---|---|---|
| 4.0.0 | 2026-10-06 | D03 dilimi k109 klasör özeti ilk yazım | vault-writer |
| 1.0.0 | 2026-09-20 | K4 taslak (yedek kaynak) | - |

## 14. Karar Kayıtları

| # | Karar | Alternatif | Gerekçe | Durum |
|---|---|---|---|---|
| A1 | İki katmanlı kimlik | Tek katman | Gizlilik + esneklik | kabul |
| A2 | 36 alfabe | Base64 | URL/okunabilirlik | kabul |
| A3 | Alias 1 halka | Zincir | Bozulma riski | kabul |
| A4 | Checksum zorunlu | Yok | Bozulma denetimi | kabul |
| A5 | Fazlı göç | Big bang | Risk sınırı | kabul |
| A6 | Kapı zorunlu | Akışta denetim | Kontrollü geçiş | kabul |
| A7 | Arkaik pencere 7 gün | Anında kapanış | İstemci uyumu | açık (`⚠️ VERIFICATION REQUIRED`) |
| A8 | Ölçüm eşikleri yazılmadı | Tahmin | Halüsinasyon riski | açık (`⚠️ VERIFICATION REQUIRED`) |
| A9 | Retention yok | Varsayılan | Uyum kararı gerekli | açık (`⚠️ VERIFICATION REQUIRED`) |
| A10 | 3 dosyalı klasör | Tek MD | Tek MD yasak | kabul |

## 15. Parametre ve Eşik Envanteri

| Parametre | Varsayılan | Birim | Onay |
|---|---|---|---|
| Probe sınırı | UNKNOWN | adım | `⚠️ VERIFICATION REQUIRED` |
| Göç fazı adımı | 7 | faz | tasarımdır |
| Trafik adımları | 5/25/50/100 | % | `⚠️ VERIFICATION REQUIRED` |
| Arkaik pencere | 7 | gün | `⚠️ VERIFICATION REQUIRED` |
| Duraklama alarmı | 6 | sa | `⚠️ VERIFICATION REQUIRED` |
| Mutabakat periyodu | 24 | sa | `⚠️ VERIFICATION REQUIRED` |
| Rollback toleransı | 2 | adet | `⚠️ VERIFICATION REQUIRED` |
| p95 bütçesi | UNKNOWN | ms | `⚠️ VERIFICATION REQUIRED` |
| 404 oranı hedefi | UNKNOWN | % | `⚠️ VERIFICATION REQUIRED` |
| Checksum sürümü | UNKNOWN | - | `⚠️ VERIFICATION REQUIRED` |
| Alfabe boyutu | 36 | - | tasarımdır |
| Kimlik uzunluğu | 32 | karakter | tasarımdır |
| Görünür uzunluk | 9 | karakter | tasarımdır |
| Alias saklama | süresiz | - | `⚠️ VERIFICATION REQUIRED` |

## 16. Dış Bağımlılık Envanteri

| # | Bağımlılık | Tür | Zorunlu | Kanıt |
|---|---|---|---|---|
| 1 | MySQL 18 DB (K5) | veri | evet | vault |
| 2 | Vektör indeks | servis | evet | `k108` |
| 3 | Embedding modeli | model | hayır | `k107` |
| 4 | İzleme (K12) | altyapı | evet | `⚠️ VERIFICATION REQUIRED` |
| 5 | İş dağıtımı (K13) | süreç | evet | `⚠️ VERIFICATION REQUIRED` |
| 6 | Kimlik doğrulama | güvenlik | evet | `k144` |
| 7 | Unicode NFC kütüphanesi | kütüphane | evet | `⚠️ VERIFICATION REQUIRED` |
| 8 | Yedek/depolama | altyapı | evet | `⚠️ VERIFICATION REQUIRED` |
| 9 | Referans seti | veri | evet | `⚠️ VERIFICATION REQUIRED` |
| 10 | Denetim kaydı | güvenlik | evet | `⚠️ VERIFICATION REQUIRED` |

## 17. Sık Sorulan Sorular

| # | Soru | Yanıt | Kaynak |
|---|---|---|---|
| 1 | Kimlik neden ikili? | Gizlilik + esneklik (A1) | §14 |
| 2 | Kimlik değişir mi? | Hayır; alias ile | tasarım `ID-004` |
| 3 | Çıkarsa ne olur? | Probe (ID-007) | tasarım |
| 4 | Tersine çıkarım? | Hash + kayan alan | §12 tasarım |
| 5 | Göç nasıl? | 7 faz + kapılar | uyum §5 |
| 6 | Geri alma? | Her fazda var (SM-002) | uyum |
| 7 | İndeks ne olur? | Eş güdümlü reindex | SM-015..017 |
| 8 | Kapanış ölçütü? | Referans taraması = 0 | SM-021 |
| 9 | Ürün adı neden yok? | Seçim yok, uydurma yasak | ZERO-HALLUCINATION |
| 10 | SLO neden yok? | Onay gerekli (A8) | §15 |
| 11 | Kiracı ayrımı? | `tenant` zorunlu (ID-009) | tasarım |
| 12 | Silinen id yeniden kullanılır mı? | Hayır (ID-022) | tasarım |
| 13 | Kimler göç tetikler? | `data-eng`/`admin` | RACI |
| 14 | Test kapsamı? | S-01..S-20 + M-01..M-18 | her iki dosya |
| 15 | Okuma sırası? | index → tasarım → uyum | §25 |

## 18. Kapsam Dışı Konular

| # | Konu | Nereye |
|---|---|---|
| 1 | SQL DDL | `k120-mysql-18-database` |
| 2 | JWT/oturum | `k144-authentication-jwt` |
| 3 | İndeks iç yapısı | `k108-vector-index` |
| 4 | Embedding eğitimi | `k111-training-pipeline` |
| 5 | Moderasyon | `k117-content-safety` |
| 6 | Deney analizi | `k116-ab-testing` |
| 7 | Prompt tasarımı | `k119-prompt-engineering` |
| 8 | CI/CD boru hattı | K13 |
| 9 | Donanım | `k1-donanim` |
| 10 | UX/görünüm | K11 |

## 19. Etkileşim Matrisi (detay)

| Komşu | Gelen | Giden | Sıklık | Hata kodu |
|---|---|---|---|---|
| k108 | indeks eşlemesi | id | her yazım | `SEM-401/402` |
| k110 | anahtar sorgusu | RAG sonucu | her sorgu | - |
| k111 | veri seti kimliği | eğitim girdisi | sürüm | - |
| k112 | yanıt `id`/`display` | istek | her çağrı | `SEM-103` |
| k113 | sürüm kimliği | yayın | yayın | - |
| k114 | model kimliği | çıkarım | sürekli | - |
| k115 | ölçüm | alarm | sürekli | `SEM-404` |
| k116 | deney birimi | atama | deney | - |
| k117 | PII kontrolü | temiz id | her kayıt | `SEM-501` |
| k118 | etiket ↔ id | etiket | günlük | - |
| k119 | maskeleme | prompt girdisi | her çağrı | `SEM-502` |

## 20. Kullanım Örnekleri

| Senaryo | Adım | Beklenen |
|---|---|---|
| Yeni varlık türü | namespace ekle → test → yayın | `SEM-102` yok |
| Alfabe değişimi | Yeni sürüm (`id_ver`) + çift doğrulama | eski id geçerli |
| Checksum değişimi | `id_ver` + göç | `SEM-101` yok |
| Kayıt birleştirme | Merge + alias | birincil korunur |
| Göç başlatma | Faz 0 → 6 kapı | kapanış raporu |
| Geri alma | `RollbackController` | eski yol döner |
| URL paylaşım | `display` | `/s/...` çalışır |
| Denetim | `IdAuditLog` | iz tam |
| Ölçüm | `sem_migration_pct` | 100% |
| Olay | Gözden geçirme | ders kaydı |

## 21. Yorumlayıcı Notlar

- `⚠️ VERIFICATION REQUIRED` = disk kanıtı yok, onay gerekir.
- `UNKNOWN` = bilgi yok; tahmin edilmez.
- Sayısal eşikler **tasarımdır**, üretim eşiği değildir.
- Dosya adları hiç değiştirilmez.
- Wiki-link biçimi: `[[dosya.md]]` ve `[[../k1xx-slug/dosya.md]]`.
- Kapsam dışı dosyalara dokunulmaz; commit atılmaz.

## 22. Sembol ve Kod Aileleri

| Sembol | Anlam | Dosya |
|---|---|---|
| `ID-*` | Kimlik kuralları | tasarım |
| `SEM-*` | Kimlik hata kodları | tasarım + uyum |
| `SM-*` | Göç kuralları | uyum |
| `K1..K10` | Geçiş kapıları | uyum |
| `S-01..S-20` | Tasarım testleri | tasarım |
| `M-01..M-18` | Göç testleri | uyum |
| `G-01..G-20` | Göç kontrol listesi | uyum |
| `U-01..U-20` | Bilinmeyenler | index |

## 23. Kapı ve Kontrol Listesi

- **K-01:** Dosya = 3.
- **K-02:** Her dosya ≥500 satır.
- **K-03:** Frontmatter 7 alan.
- **K-04:** `version: 4.0.0`.
- **K-05:** `updated: 2026-10-06`.
- **K-06:** `authority` D03.
- **K-07:** `Kanıt:` satırı var.
- **K-08:** Kanıtsız iddia işaretli.
- **K-09:** Uydurma yol yok.
- **K-10:** Kırık link 0.
- **K-11:** Link biçimi doğru.
- **K-12:** `verify` mojibake 0.
- **K-13:** BOM yok.
- **K-14:** ASCII akış var.
- **K-15:** ≥6 tablo.
- **K-16:** Kenar durum var.
- **K-17:** Hata modu var.
- **K-18:** Bağımlılık var.
- **K-19:** Test var.
- **K-20:** Sürüm geçmişi var.
- **K-21:** Yalnız k109.
- **K-22:** Başka dosya yok.
- **K-23:** Commit yok.
- **K-24:** Türkçe + orijinal identifier.

## 24. Bilinmeyen Envanteri

- **U-01:** Kimlik kodu yolu — UNKNOWN.
- **U-02:** Nihai alfabe — UNKNOWN.
- **U-03:** Checksum algoritması — UNKNOWN.
- **U-04:** Çakışma oranı — UNKNOWN.
- **U-05:** Probe hedefi — UNKNOWN.
- **U-06:** Göç süresi — UNKNOWN.
- **U-07:** Göç maliyeti — UNKNOWN.
- **U-08:** Arkaik pencere onayı — UNKNOWN.
- **U-09:** Retention — UNKNOWN.
- **U-10:** Uyumluluk kapsamı — UNKNOWN.
- **U-11:** Alias tablosu büyüklüğü — UNKNOWN.
- **U-12:** Kayıt hacmi — UNKNOWN.
- **U-13:** Kiracı sayısı — UNKNOWN.
- **U-14:** Eşzamanlı kullanıcı — UNKNOWN.
- **U-15:** API sürümleme — UNKNOWN.
- **U-16:** Referans seti boyutu — UNKNOWN.
- **U-17:** Göç ortamı — UNKNOWN.
- **U-18:** Yedek periyodu — UNKNOWN.
- **U-19:** Alarm alıcısı — UNKNOWN.
- **U-20:** Bakım penceresi — UNKNOWN.

## 25. Kısaltmalar

| Kısaltma | Tam hali |
|---|---|
| ID | identifier |
| NFC | unicode normalization form C |
| PII | personally identifiable information |
| UTC | coordinated universal time |
| TTL | time to live |
| SLO | service level objective |
| DDL | data definition language |
| API | application programming interface |
| JWT | json web token |
| RACI | responsible accountable consulted informed |
| MTTR | mean time to recovery |
| CSV | comma separated values |
| JSON | javascript object notation |
| HTTP | hypertext transfer protocol |
| QA | quality assurance |
| POC | proof of concept |
| QPS | queries per second |
| SSD | solid state drive |
| RAM | random access memory |
| CPU | central processing unit |

## 26. RACI (özet)

| İş | data-eng | backend-arch | security | sre | ürün |
|---|---|---|---|---|---|
| Sözdizimi | **R** | A | C | I | I |
| Üretim | **R** | C | C | I | I |
| Gizlilik | C | C | **R** | I | A |
| Göç | **R** | A | C | C | I |
| Geri alma | C | I | I | **R** | I |
| Kapı onayı | A | C | C | **R** | A |
| Retention | C | I | A | I | **R** |
| Kapanış | A | C | I | **R** | A |

## 27. Kısa Notlar

- Kimlik üreteci deterministiktir.
- Değişim yalnız alias ile olur.
- Alias zinciri 1 halkadır.
- Silinen id yeniden kullanılmaz.
- Checksum ihlali `SEM-101`.
- Bilinmeyen namespace `SEM-102`.
- Göç 7 faz + 10 kapı içerir.
- Geri alma her fazda mümkündür.
- Kapanış = tarama 0 + rapor.
- Eşik yoksa hedef yoktur.

## 28. Okuma Sırası

| Sıra | Dosya | Neden |
|---|---|---|
| 1 | `index.md` | Bağlam, komşular, kapılar |
| 2 | `semantic-id-tasarim.md` | Kimlik kuralları |
| 3 | `semantic-id-uyum-stratejisi.md` | Göç + geri alma |

> Bu klasör tamamlandığında D03 diliminin 12 klasöründen **2/12**'si kapanmıştır.
> Sıradaki komşu: `[[../k110-rag-knowledge/index.md]]`.

## 14. Ek A — Kapsam Dışı Kalan Konular ve Bilinmeyen Envanteri

Bu bölüm, D03 diliminin k109 kapsamı içinde ele alınması gereken ama vault'ta
kanıt bulunamayan konuları listeler. Hiçbiri tahminle doldurulmaz.

| # | Konu | Neden gerekli | Kanıt durumu |
|---|---|---|---|
| U1 | Gerçek identifier kayıt/çıkarma API'si | Tasarımın uygulanabilirliği | `⚠️ VERIFICATION REQUIRED` |
| U2 | FPR ölçüm altyapısı | `sm-id-fpr` hedefinin doğrulanması | `⚠️ VERIFICATION REQUIRED` |
| U3 | Onay metni hukuki onayı | KVKK 9 uyumu | `⚠️ VERIFICATION REQUIRED` |
| U4 | Yerel indeks dosyası biçimi | Embedding algoritması seçimi | `⚠️ VERIFICATION REQUIRED` |
| U5 | Cross-issuer iddia kontrolü | Kimlik doğrulama güvenliği | `⚠️ VERIFICATION REQUIRED` |
| U6 | Rate-limit eşiği | FPR/artış oranı dengesi | `⚠️ VERIFICATION REQUIRED` |
| U7 | Onay kaydı saklama süresi | KVKK 6-9 dengesi | `⚠️ VERIFICATION REQUIRED` |
| U8 | Sahiplik doğrulama kanıtı | Cookie-hash peek doğruluğu | `⚠️ VERIFICATION REQUIRED` |
| U9 | Tersine indeks istatistikleri | `_idftf` için global sayılar | `⚠️ VERIFICATION REQUIRED` |
| U10 | Silme sonrası indeks temizliği | KVKK 12 ve veri minimizasyonu | `⚠️ VERIFICATION REQUIRED` |

## 15. Ek B — Karar Kayıt Tablosu (ADL)

| ADL | Karar | Kaynak | İlgili bölüm |
|---|---|---|---|
| ADL-01 | Peek, fetch sınırının altında | Uzlaşma K-01 | §3 |
| ADL-02 | Eşleşmeyen probe genel 404 | Uzlaşma K-02 | §9 |
| ADL-03 | FPR hedefi gereksinimdir | Uzlaşma K-03 | §6 |
| ADL-04 | Cross-issuer iddia kontrolü zorunlu | Uzlaşma K-04 | §8 |
| ADL-05 | Onay metni sürümü saklanır | Uzlaşma K-05 | §7 |
| ADL-06 | Yeni bağımlılık eklenmez | Uzlaşma K-06 | §11 |
| ADL-07 | Yerel indeks atomik güncellenir | Uzlaşma K-07 | §5 |
| ADL-08 | Isı haritası bucket'ları sabit | Uzlaşma K-08 | §6 |

## 16. Ek C — Ölçüm ve Kabul Tablosu

| Metrik | Hedef | Ölçüm yöntemi | Kapı |
|---|---|---|---|
| `sm-id-p95` | 40 ms | Uygulama içi ölçüm | CI kapısı değil |
| `sm-id-fpr` | ≤ %1,0 | Sentetik çift seti | Tasarım gereksinimi |
| `sm-id-magic-link-vol` | ≤ günlük isteklerin %5'i | Günlük sayaç | Gözlem |
| `sm-id-consent-coverage` | %100 yeni kayıt | Onay kaydı sayımı | CI kapısı değil |
| `sm-id-index-staleness` | ≤ 24 saat | Zaman damgası farkı | Gözlem |
| `sm-id-error-rate` | ≤ %0,1 | Hata sayacı | Gözlem |

Tüm metriklerin gerçek ölçüm altyapısı vault'ta yoktur: `⚠️ VERIFICATION REQUIRED`.

## 17. Ek D — Glossary

| Terim | Anlam |
|---|---|
| Ayırt edici alan | Aday kayıtları birbirinden ayıran nitelik |
| Bucket | Isı haritasında yoğunluk gruplanan aralık |
| FPR | Yanlış pozitif oranı |
| Hash peek | Sunucu tarafı kaba kuvvet taraması |
| İstemci-yan imzalama | Anahtarın istemcide imza üretmesi |
| Popülerlik-isı | Sıklık ve recency birleşik skoru |
| Recency | Son etkileşim tazeliği |
| Saklama-işleme | Kaydın yeniden türetilebilir işlenmesi |
| Soğuk depo | Düşük erişim frekanslı saklama katmanı |
| Tersine indeks | Terimden kayda giden eşleme |
| Vektör düzlemi | Anlam benzerliğinin ölçüldüğü uzay |

## 18. Ek E — Çapraz Referans Matrisi

| Konu | Tasarım | Uyum Stratejisi |
|---|---|---|
| Kayıt modeli | `[[semantic-id-tasarim.md]]` §2 | `[[semantic-id-uyum-stratejisi.md]]` §4 |
| Eşikler | `[[semantic-id-tasarim.md]]` §7 | `[[semantic-id-uyum-stratejisi.md]]` §6 |
| Hata modları | `[[semantic-id-tasarim.md]]` §9 | `[[semantic-id-uyum-stratejisi.md]]` §9 |
| Bağımlılıklar | `[[semantic-id-tasarim.md]]` §11 | `[[semantic-id-uyum-stratejisi.md]]` §11 |
| Ölçüm | `[[semantic-id-tasarim.md]]` §7 | `[[semantic-id-uyum-stratejisi.md]]` §6 |
| Onay akışı | — | `[[semantic-id-uyum-stratejisi.md]]` §7 |
| Saklama-işleme | `[[semantic-id-tasarim.md]]` §2 | `[[semantic-id-uyum-stratejisi.md]]` §5 |

## 19. Ek F — Kaynak Bağlantısı

| Kaynak | Kullanım | Erişim |
|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k4-yapay-zeka/README.md` | Dilim kapsamı | Yalnız okuma |
| `_backup/.../index.md` | Bağlantı haritası | Yalnız okuma |
| `shared/src/AI/` | Sınıf kanıtları | Yalnız okuma |
| `.ai/architecture/k108-vector-index/` | Komşu dilim | Yalnız okuma |
| `.ai/.templates/` | Şablon kuralları | Yalnız okuma |
| NIST SP 800-63C | Dış referans (id) | `⚠️ VERIFICATION REQUIRED` |

## 20. Ek G — Sürüm Notları

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 4.0.0 | 2026-10-06 | D03 dilimi ilk yazımı |

## 21. Ek H — Sorumluluk Matrisi (RACI)

| Konu | Architect | Security | Data | Backend | QA |
|---|---|---|---|---|---|
| Kayıt modeli | A | C | R | R | I |
| Onay akışı | C | A | I | R | C |
| Eşikler | A | C | R | I | R |
| Saklama | C | C | A | R | I |
| İndeks | A | I | R | C | C |
| Geri çekme | C | A | C | R | I |
| Ölçüm | I | C | C | R | A |

A = Accountable, R = Responsible, C = Consulted, I = Informed.

## 22. Ek I — Sorun Dayanıklılık Matrisi

| Senaryo | Etki | İlk tepki | Kurtarma |
|---|---|---|---|
| İndeks dosyası bozuk | Sorgu düşer | Yeniden oluşturma | Kayıtlardan yeniden indeksleme |
| Onay kaydı eksik | Erişim reddi | Aydınlatma akışı | Onay toplama |
| Hash eşleşmesi sahte | Yetkisiz erişim | Peek kapatma | Anahtar rotasyonu |
| Rate-limit kırılması | DDoS benzeri yük | Eşik düşürme | Kök neden analizi |
| Yerel ısı haritası kayboldu | Skorlama düşer | Dosyayı yeniden yazma | Yeniden ısı haritası |
| Sürüm uyuşmazlığı | Yorumlama hatası | Sürüm sabitleme | Geri uyum katmanı |
| Anahtar rotasyonu | Eski imza geçersiz | İki anahtar penceresi | Eski anahtarın devre dışı bırakılması |

## 23. Ek J — Test Kapsamı Taslağı

| Test | Tür | Hedef |
|---|---|---|
| Normalize edici sabitliği | Birim | Karakter eşleme |
| Bucket sınır değerleri | Birim | Isı haritası |
| Onay metni sürümü eşleşmesi | Birim | Aydınlatma |
| Geri çekme sonrası reddetme | Bütünleşik | Uyum |
| Eşleşmeyen probe reddi | Bütünleşik | Güvenlik |
| Tersine indeks sıfır-pay | Birim | `IDR-1` |
| Yerel indeks atomikliği | Bütünleşik | `REL-4` |
| FPR sentetik çift seti | Kalibre | `sm-id-fpr` |

Bu testlerin depoda karşılığı yoktur: `⚠️ VERIFICATION REQUIRED`.

## 24. Ek K — Açık Sorular

| # | Soru | Sahibi | Etki |
|---|---|---|---|
| Q1 | Uygulama API'si ne zaman eklenir? | Backend | Tasarım doğrulanamıyor |
| Q2 | Onay metni hukuki onayından geçti mi? | Hukuk | KVKK 9 riski |
| Q3 | Yerel indeks dosyası hangi biçimde? | Data | Boyut/performans |
| Q4 | FPR ölçümü nasıl otomatikleşir? | QA | Kapı çalışmıyor |
| Q5 | Cross-issuer kontrolü hangi katmanda? | Security | Doğrulama boşluğu |
| Q6 | Rate-limit eşiği hangi veriyle seçilecek? | Performance | Kullanıcı deneyimi |
| Q7 | Sahiplik doğrulama kanıtı nedir? | Security | Peek geçerliliği |
