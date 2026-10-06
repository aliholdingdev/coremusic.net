---
title: "Anlamlı Kimlik Tasarımı - k109-semantic-id"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D03 dilimi (k108-k119)"
updated: 2026-10-06
---

# 109. Anlamlı Kimlik Tasarımı - `k109-semantic-id`

> Dilim: D03 (k108-k119) · Klasör no: 109 · Kategori: mimari · Sürüm: 4.0.0 · Tarih: 2026-10-06
> Sorumlu persona: `data-engineer` (şema/üretim), `backend-architect` (arayüz), `security` (gizlilik)
> Kapsadığı MD: 3 (2 içerik + index) · Kanıtlar: satır içi `Kanıt:` ya da `⚠️ VERIFICATION REQUIRED`

## 1. Genel Bakış

Semantic ID, bir medya varlığına (parça, sanatçı, albüm, kapak görseli, kullanıcı
koleksiyonu) veya bir kullanıcının anlamsal varlığına verilen **kısa, tekrar
üretilebilir, çakışma kontrollü** kimliktir. Amaç: ham kayıttan türetilmiş uzun
benzersiz anahtar yerine, **anlam taşıyan ve indeksle uyumlu** bir kimlik kullanmak.

**Katman bağımlılığı:** K5 (kaynak kayıt anahtarı), K4 (`k107-embedding-models`
boyut kararı, `k108-vector-index` eşleme), K9 (API'de kimliğin görünür hali), K6
(gizlilik/PII). Bu belge kimliğin **tasarımı**dır; üretimde var olduğu iddia edilmez.

Üç kimlik sınıfı ayrılır:
1. **Kalıcı sistem kimliği** (`sid_*`) — üretilir, değişmez, silinmez.
2. **Görünür (display) kimlik** (`/s/<kod>`) — kısa, okunabilir, yeniden adlandırılabilir.
3. **Geçici oturum kimliği** (`tmp_*`) — kısa ömürlü, tekrarlanabilir değil.

Ana gerilim: **kısalık ↔ çakışma ↔ geri döndürülebilirlik.** Çok kısa kimlik
çakışmayı artırır; geri döndürülebilir kimlik sızıntı riskini artırır. Bu belgede
çözüm: **iki katmanlı kod** (namespace + çekirdek kod) ve çakışma çözümü.

## 2. Klasör Özeti (K Tablosu)

| Numara | Ad | Amaç | Bağımlılık | Sorumlu persona | Kanıt | MD |
|---|---|---|---|---|---|---|
| 1 | Anlamlı Kimlik Tasarımı | Kimlik yapısı, üretim, çakışma, gizlilik | K5, K4, K9 | data-engineer, security | `⚠️ VERIFICATION REQUIRED: kimlik üretim kodu repo'da görülmedi` | [[semantic-id-tasarim.md]] |
| 2 | Anlamlı Kimlik Uyum Stratejisi | Yeniden adlandırma, göç, çift yazı, indeks uyumu | K4, K5 | data-engineer, backend-architect | `⚠️ VERIFICATION REQUIRED: göç betiği repo'da görülmedi` | [[semantic-id-uyum-stratejisi.md]] |

## 3. Kapsam ve Kapsam Dışı

### 3.1 Kapsamda

| # | Konu | Not |
|---|---|---|
| 1 | Kimlik sözdizimi ve uzunluk | namespace + kod |
| 2 | Üretim fonksiyonu kuralları | deterministik |
| 3 | Çakışma çözümü | probe sırası |
| 4 | Yeniden adlandırma (alias) | kalıcı id korunur |
| 5 | Gizlilik / PII sızıntısı | tersine çevirilebilirlik |
| 6 | Vektör indeksle eşleme | `k108` |
| 7 | Kimlik sürümleme | `v1/v2` |
| 8 | Göç ve çift yazı | kardeş dosya |

### 3.2 Kapsam Dışı

| # | Dışarıda | Nereye |
|---|---|---|
| 1 | Kayıt kaynağı SQL şeması | K5 / `k120-mysql-18-database` |
| 2 | Vektör indeks iç yapısı | `k108-vector-index` |
| 3 | Embedding hesabı | `k107-embedding-models` |
| 4 | Kimlik doğrulama protokolü | `k144-authentication-jwt` |
| 5 | İçerik güvenliği kategorileri | `k117-content-safety` |
| 6 | RAG bağlamı | `k110-rag-knowledge` |

## 4. Mimari Bileşenler

| # | Bileşen | Sorumluluk | Çıktı |
|---|---|---|---|
| 1 | `IdNamespace` | Alan/tür adayları | namespace listesi |
| 2 | `IdEncoder` | Kayıt anahtarı → çekirdek kod | kod |
| 3 | `IdAllocator` | Çakışma çözümü (probe) | benzersiz id |
| 4 | `IdAliasStore` | Eski → yeni eşleme | alias kaydı |
| 5 | `IdValidator` | Sözdizimi + checksum denetimi | bool/hata |
| 6 | `IdMapper` | id ↔ indeks kaydı | eşleme |
| 7 | `IdRedactor` | Gizli alan maskeleme | maskeli görünüm |
| 8 | `IdVersionGate` | Sürüm uyumu denetimi | sürüm hatası |
| 9 | `IdAuditLog` | Değişim izi | denetim kaydı |

## 5. Uçtan Uca Akış

```
[Kaynak kayıt (K5): (entity, natural_key, version)]
        |
        v
[IdEncoder] -- normalize (NFC, lower, trim, delimiter) -->
        |                                                 
        |  ham anahtar                                    
        v                                                 
[Checksum] -- mod11 benzeri denetim kodu --> [çekirdek kod]
        |
        v
[IdAllocator] -- çakışma var mı? --> EVET --> [probe +1/+2...] --> (id)
        | HAYIR                                                
        v                                                     
[id = <ns>-<ver>-<kod>]                                       
        |                                                     
        +--> [IdValidator] -- sözdizimi & checksum hata? --> RET
        |
        +--> [IdAliasStore] -- önceki id varsa alias yaz
        |
        +--> [IdMapper] -- indeks kaydı (k108) + K5 kaydı
        |
        +--> [IdAuditLog] -- kim, ne, ne zaman, neden
```

### 5.1 Adımlar

1. Kaynak kayıtta doğal anahtar (`natural_key`) ve `version` okunur.
2. `IdEncoder` metni normalize eder: Unicode NFC, boşluk kırpma, delimiter toparlama.
3. Checksum alanı üretilir (yazım hatası/yeniden üretim doğrulaması için).
4. `IdAllocator` çakışmayı dener; doluysa probe adımı ilerletilir.
5. Kimlik sözdizimi `<ns>-<ver>-<kod>-<checksum>` biçiminde birleştirilir.
6. `IdValidator` üretim kuralıyla **aynı** fonksiyonu çalıştırıp eşitlik denetler.
7. Varsa alias kaydı yazılır (eski id → yeni id).
8. `IdMapper` hem indekste hem K5'te eşlemeyi yazar.
9. `IdAuditLog` olayı yazar; denetim için geriye dönük iz kalır.
10. API'ye yalnız izinli görünüm (`display`) sunulur.

### 5.2 Kimlik Biçimleri

| Biçim | Örnek şablon | Kullanım | Değişir mi? |
|---|---|---|---|
| Kalıcı | `sid_t_v1_9f3k2x7q` | iç referans | hayır |
| Görünür | `/s/9f3k-2x7q` | paylaşım/URL | evet (alias) |
| Geçici | `tmp_01h8z...` | oturum/iş | evet |
| Eski | `sid_t_v0_...` | göç | hayır (arşiv) |

## 6. İş Kuralları (`ID-*`)

- **ID-001:** Kimlik üreteci **deterministiktir**: aynı girdi → aynı kimlik.
- **ID-002:** Determinizm bozulursa üretim sürümü artırılır (`v1` → `v2`).
- **ID-003:** Ham doğal anahtar asla ham haliyle dışarı çıkmaz (checksum + normalization).
- **ID-004:** Kimlik üretildikten sonra **değiştirilmez**; değişim alias ile yapılır.
- **ID-005:** Alias zinciri en fazla 1 halkadır (A→B→C yasak; A→C'ye indirgenir).
- **ID-006:** Alias süresiz saklanır; silme yalnız göç tamamlanınca düşünülür.
- **ID-007:** Çakışma çözümünde probe adımı sabit sıradadır (+1, +2, ... +N sınırı).
- **ID-008:** Probe sınırı aşılırsa `SEM-301` hatası verilir, ikinci kimlik zorlanmaz.
- **ID-009:** Kimlik `tenant` içindedir; kiracılar arası eşleşme mümkün değildir.
- **ID-010:** Checksum uyuşmazlığı `SEM-101` ile reddedilir.
- **ID-011:** Bilinmeyen namespace `SEM-102` ile reddedilir.
- **ID-012:** Sürüm uyuşmazlığı `SEM-103` ile reddedilir.
- **ID-013:** Geçici kimlik kalıcı referansta kullanılamaz.
- **ID-014:** Kalıcı kimlik URL'de yalnız alias (görünür) biçiminde sunulur.
- **ID-015:** Tersine çıkarım (kimlikten kullanıcı) engellenir: kayan alan + checksum.
- **ID-016:** PII taşıyan alanlar kimlik çekirdeğine **girmez**.
- **ID-017:** Unicode normalizasyonu (NFC) zorunludur; farklı eşdeğerler aynı koda düşer.
- **ID-018:** Büyük/küçük harf duyarlılığı namespace'e göre sabittir.
- **ID-019:** Ayraç (delimiter) `-` ve `_` dışında kullanılmaz (bozulma riski).
- **ID-020:** Kimlik uzunluğu sınırlıdır; sınır aşımı `SEM-104`.
- **ID-021:** Aynı kayıt iki kez üretilirse aynı kimlik çıkar (idempotent).
- **ID-022:** Silinen kaydın kimliği yeniden **kullanılmaz** (siyah liste).
- **ID-023:** Yeniden kullanım gerekiyorsa siyah listeden çıkarma yalnız `admin` onayıyla.
- **ID-024:** Alias yazımı ile ana kayıt yazımı tek işlemde (atomik) yapılır.
- **ID-025:** İndeks eşlemesi (`IdMapper`) kayıt yazımından **sonra** onaylanır.
- **ID-026:** Kimlik versiyonu alan adıyla değil, veri alanıyla taşınır (`id_ver`).
- **ID-027:** Göç öncesi eski ve yeni kimlik eşzamanlı çözülebilir (çift okuma).
- **ID-028:** Çift okuma penceresi kapanınca tek yönlü okuma başlar.
- **ID-029:** API yanıtında hem `id` hem `display` varsa hangisinin güvenli olduğu belgelenir.
- **ID-030:** Kimlik loglarda maskeli yazılır (ilk 4 + son 2 hariç).
- **ID-031:** Loglama maskesi denetim konsolunda kaldırılabilir (yetkiye bağlı).
- **ID-032:** Göç ilerlemesi yüzdesi ayrı ölçülür (`sem_migration_pct`).
- **ID-033:** Göç, indeks yeniden yazımıyla eş güdümlüdür (`k108` R-001).
- **ID-034:** Göç sırasında yazma kuralı: yeni kayıt → yeni kimlik, eski kayıt → eski kimlik.
- **ID-035:** Göç kapanış ölçütü: eski kimliğe referans kalmaması.
- **ID-036:** Alias ile yönlendirme her okuma yolu için zorunlu değildir; URL yolu zorunlu.
- **ID-037:** Kare/boşluk/% karakterleri görünür kimlikten arındırılır.
- **ID-038:** Görünür kimlik Ayrılabilir karakter içermez (akış sırası normalize edilir).
- **ID-039:** Kimlik üreteci için test seti sabittir (regresyon koruması).
- **ID-040:** Üretim fonksiyonu değişirse referans seti yeniden üretilir ve karşılaştırılır.
- **ID-041:** Çakışma oranı ölçülür; eşik aşımı üretim hatası sayılır.
- **ID-042:** Probe ortalaması izlenir (dağılım kayması alarmı).
- **ID-043:** Kimlik iki sistem arasında (K5 ↔ indeks) mutabakat konusudur.
- **ID-044:** Mutabakat farkı > 0 ise göç/risk değerlendirmesi açılır.
- **ID-045:** Kimlik alanı için indeks (K5) zorunlu (sorgu maliyeti).
- **ID-046:** Alias tablosu için indeks de zorunludur (çift okuma yolu).
- **ID-047:** Silme işlemi alias'ı **kopyalamaz**, arşivler.
- **ID-048:** Kimlik üretiminde paralellik güvenlidir (salt üretici).
- **ID-049:** Çakışma çözümü paralelde yarışabilir; son kayıt kazanır + tekrar deneme.
- **ID-050:** Kimlik asla hesaplama/özel alan (secret) içermez.
- **ID-051:** Checksum algoritması sürümü `id_ver` ile taşınır.
- **ID-052:** Yeni checksum sürümü eski kimlikleri geçersiz kılmaz (çift doğrulama).
- **ID-053:** Göçmeyen kimlikler için arayüz 404 yerine yönlendirme döndürür.
- **ID-054:** Yönlendirme sayısı ölçülür (göç kalitesi göstergesi).
- **ID-055:** Kimlik doğrulama hataları ayrı kod ailesiyle (`SEM-*`) taşınır.
- **ID-056:** `SEM-*` kodları ürüne bağlı değildir (taşınabilir).
- **ID-057:** Kimlik uzunluğu ve alfabe dokümante edilir (bu dosya §7).
- **ID-058:** Üretim sürümü yükseldiğinde eski sürüm okumaya devam eder.
- **ID-059:** Yalnız yazma yeni sürüme geçer (okuma iki sürüm).
- **ID-060:** Okuma desteği yalnız göç penceresi içinde tutulur.

## 7. Veri Modeli

### 7.1 Kalıcı kimlik kaydı

| Alan | Tip | Boş/null | Kısıt | İndeks |
|---|---|---|---|---|
| `id` | string(32) | hayır | benzersiz + `tenant` | primary |
| `id_ver` | tinyint | hayır | 1..255 | - |
| `ns` | string(16) | hayır | enum | btree |
| `core` | string(16) | hayır | alfabe | btree |
| `checksum` | string(4) | hayır | algoritma sürümü | - |
| `entity` | string(16) | hayır | trk/album/artist/user/cover | btree |
| `natural_hash` | string(64) | hayır | hash(natural_key) | btree |
| `state` | enum | hayır | active/retired/blacklist | btree |
| `display` | string(32) | hayır | alias ile | unique |
| `created_at` | datetime | hayır | UTC | btree |
| `retired_at` | datetime | evet | UTC | - |
| `tenant` | string(32) | hayır | kiracı | btree |

### 7.2 Alias kaydı

| Alan | Tip | Boş/null | Kısıt | Not |
|---|---|---|---|---|
| `from_id` | string(32) | hayır | eski kimlik | btree |
| `to_id` | string(32) | hayır | yeni kimlik | btree |
| `hop` | tinyint | hayır | = 1 (ID-005) | zincir yasağı |
| `reason` | enum | hayır | rename/merge/göç | denetim |
| `created_at` | datetime | hayır | UTC | - |
| `actor` | string(64) | hayır | rol/istek sahibi | denetim |

### 7.3 Alfabe ve uzunluk

| Alan | Değer | Açıklama |
|---|---|---|
| Alfabe | `0-9 a-z` (36) | Karışık harf duyarlılığı yok |
| Uzunluk (kalıcı) | `4 + 1 + 2 + 1 + 8 + 1 + 4` | `sid`-`t`-`v1`-kod-checksum örneği |
| Uzunluk sınırı | 32 karakter | `SEM-104` |
| Görünür uzunluk | 9 karakter | `9f3k-2x7q` |
| Checksum uzunluğu | 4 karakter | denetim |
| Namespace uzunluğu | ≤16 | `SEM-102` |

## 8. Kenar Durumlar

| # | Durum | Tetikleyici | Beklenen | Test |
|---|---|---|---|---|
| 1 | Aynı doğal anahtar iki kayıtta | Kaynak çakışması | `SEM-301` / probe | S-03 |
| 2 | Probe sınırı dolu | Çok çakışma | Red + alarm | S-04 |
| 3 | Eski id bilinmiyor | 404 döner | Yönlendirme yok, net hata | S-06 |
| 4 | Alias zinciri (A→B→C) | Hatalı göç | İndirgenir (ID-005) | S-07 |
| 5 | Kayıt silinip yeniden oluşturulma | İş kuralı | Siyah liste → yeni kimlik | S-08 |
| 6 | Unicode eşdeğeri farklı yazım | NFC dışı | Aynı çekirdek kod | S-09 |
| 7 | Kiracılar arası eşleşme | Kötü niyetli | Mümkün değil (ID-009) | S-10 |
| 8 | Checksum yanlış | Elle düzenleme | `SEM-101` | S-11 |
| 9 | Bilinmeyen namespace | İstemci hatası | `SEM-102` | S-12 |
| 10 | Sürüm uyuşmazlığı | Eski istemci | `SEM-103` | S-13 |
| 11 | Göç yarısı | Kesinti | Çift okuma sürer | S-14 |
| 12 | Aynı anda iki üretim | Paralellik | Son kayıt kazanır | S-15 |
| 13 | Log sızıntısı | Yanlış loglama | Maskeli (ID-030) | S-16 |
| 14 | Tersine çıkarım denemesi | Analiz | Kayan alan engeller | S-17 |
| 15 | Kare/boşluk girintisi | URL | Arındırılır (ID-037) | S-18 |

## 9. Hata Modları

| Kod | Semptom | Kök neden | Aksiyon | Kaçış |
|---|---|---|---|---|
| `SEM-101` | Checksum uyuşmadı | Bozulma/elle müdahale | Red | Kaynağa dön |
| `SEM-102` | Bilinmeyen namespace | İstemci/şema hatası | Red | Namespace listesi |
| `SEM-103` | Sürüm uyuşmazlığı | Eski istemci | Uyar + indirgeme | Çift okuma |
| `SEM-104` | Uzunluk sınırı | Aşırı uzun girdi | Red | Kırpma/kısaltma |
| `SEM-201` | Alias zinciri | Hatalı göç | İndirgeme | Denetim job |
| `SEM-202` | Alias döngüsü | Hata/böcek | Döngü kırılır | Olay kaydı |
| `SEM-301` | Çakışma sınırı | Nüfus yoğunluğu | Alarm | Çekirdek genişletme |
| `SEM-302` | Çakışma oranı yüksek | Üretici hatası | Üretici denetimi | Probe gözden geçirme |
| `SEM-401` | Mutabakat farkı | Eşleme eksik | `IdMapper` kontrolü | Göç tekrarı |
| `SEM-402` | İndeks kaybı | Göç yarım | Yeniden eşleme | `k108` reindex |
| `SEM-501` | PII sızıntısı şüphesi | Üretici hatası | Güvenlik olayı | Alan denetimi |
| `SEM-502` | Log açıkta | Maskesiz log | Log düzeltmesi | Denetim |
| `SEM-601` | Göç durdu | Kesinti | Konumdan devam | Checkpoint |
| `SEM-602` | Göç geri alındı | Hata | Ters yönlendirme | Alias korunur |
| `SEM-701` | Kimlik yeniden kullanıldı | Siyah liste ihlali | P1 olay | Geri alma |

## 10. Bağımlılıklar

| Bağımlılık | Tip | Sürüm | Zorunlu | Not |
|---|---|---|---|---|
| Kaynak kayıt (K5) | veri | UNKNOWN | evet | `natural_key` kaynağı |
| Vektör indeks | servis | UNKNOWN | evet | `k108` eşleme |
| Embedding boyutu | model | UNKNOWN | hayır | `k107` (dolaylı) |
| API kapısı | servis | UNKNOWN | evet | K9 |
| Kimlik doğrulama | güvenlik | UNKNOWN | evet | `k144` (dolaylı) |
| Göç/plan işi | süreç | UNKNOWN | evet | kardeş dosya |
| Denetim kaydı | güvenlik | UNKNOWN | evet | K6/K12 |
| Unicode kitaplığı | kütüphane | `⚠️ VERIFICATION REQUIRED` | evet | NFC |

## 11. Ölçüm ve Kabul

| # | Metrik | Hedef | Kapı |
|---|---|---|---|
| 1 | Çakışma oranı | `⚠️ VERIFICATION REQUIRED` | üretim öncesi |
| 2 | Probe ortalaması | `⚠️ VERIFICATION REQUIRED` | günlük |
| 3 | Mutabakat farkı (K5 ↔ indeks) | 0 | günlük |
| 4 | Alias yönlendirme başarısı | 100% | göç |
| 5 | 404 oranı (eski kimlik) | `⚠️ VERIFICATION REQUIRED` | göç sonrası |
| 6 | Kimlik üretim p95 | `⚠️ VERIFICATION REQUIRED` | - |
| 7 | Log maskesi ihlali | 0 | denetim |
| 8 | Göç ilerlemesi | 100% | `sem_migration_pct` |
| 9 | Karşılaştırma seti tutarlılığı | eşit | regresyon |
| 10 | Siyah liste ihlali | 0 | P1 |

## 12. Güvenlik ve Uyum

| # | Tehdit | Kontrol | Durum |
|---|---|---|---|
| 1 | Tersine çıkarım (id → kullanıcı) | Hash + kayan alan + checksum | tasarlandı |
| 2 | PII sızıntısı | Çekirdeğe PII girmez (ID-016) | tasarlandı |
| 3 | Log sızıntısı | Maskeleme (ID-030) | tasarlandı |
| 4 | Kiracı çapraz erişim | `tenant` kapsamı | tasarlandı |
| 5 | Kimlik tahmini | Alfabe + uzunluk + salt olmayan bileşen | tasarlandı |
| 6 | Yetkisiz alias değişimi | Rol + denetim | tasarlandı |
| 7 | Denetim eksikliği | `IdAuditLog` | tasarlandı |
| 8 | Saklama/retention | Politika **tanımsız** | `⚠️ VERIFICATION REQUIRED` |

## 13. Riskler ve Teknik Borç

| # | Risk | Olasılık | Etki | Azaltım |
|---|---|---|---|---|
| 1 | Çakışma patlaması | orta | yüksek | çekirdek genişletme + alarm |
| 2 | Göç yarım kalması | yüksek | yüksek | checkpoint + çift okuma |
| 3 | Alias zinciri bozulması | düşük | orta | `SEM-201` + denetim |
| 4 | Tersine çıkarım | orta | çok yüksek | §12 kontrolleri |
| 5 | İki sistem mutabakatsızlığı | orta | yüksek | günlük mutabakat |
| 6 | Göç maliyeti/zamanı | yüksek | orta | Bölüm planlaması (kardeş dosya) |
| 7 | Ölçüm hedefi olmaması | **yüksek** | orta | Kapı #1 onayı |
| 8 | Retention politikası eksikliği | **yüksek** | orta | Uyum kararı |

## 14. Test Senaryoları

| ID | Senaryo | Adım | Beklenen |
|---|---|---|---|
| S-01 | Determinizm | Aynı girdiyi 100× üret | 100 aynı kimlik |
| S-02 | Normalizasyon | NFC/NFD eşdeğeri üret | aynı kimlik |
| S-03 | Çakışma | Aynı anahtar iki kayıt | probe ile benzersiz |
| S-04 | Probe sınırı | N+1 çakışma | `SEM-301` |
| S-05 | İdempotent kayıt | Aynı istek 3× | tek kimlik |
| S-06 | Bilinmeyen id | Olmayan id sorgusu | net hata, tahmin yok |
| S-07 | Alias zinciri | A→B→C yaz | indirgeme A→C |
| S-08 | Siyah liste | Sil + yeniden üret | yeni kimlik |
| S-09 | Unicode | Karışık yazım | aynı çekirdek |
| S-10 | Kiracı ayrımı | A id'sini B ile sorgu | bulunamaz |
| S-11 | Checksum | Değiştir | `SEM-101` |
| S-12 | Namespace | Bilinmeyen ns | `SEM-102` |
| S-13 | Sürüm | Eski sürüm iste | `SEM-103` |
| S-14 | Göç kesintisi | Yarıda kes + devam | çift okuma korunur |
| S-15 | Paralel üretim | Aynı anda 2 istek | tek kazanan + tekrar |
| S-16 | Log maskesi | Log çıktısı incele | maskeli |
| S-17 | Tersine çıkarım | Kimlikten arama | sonuç yok |
| S-18 | URL güvenliği | Kare/boşluk girişi | arındırma |
| S-19 | Mutabakat | K5 ↔ indeks denetimi | fark 0 |
| S-20 | Regresyon | Referans seti | eşitlik |

## 15. Kanıt ve Doğrulama

- `Kanıt: .ai/architecture/k109-semantic-id/semantic-id-tasarim.md` (bu dosya)
- `Kanıt: ⚠️ VERIFICATION REQUIRED — kimlik üreten/çözen çalışan kod repo'da görülmedi`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — Bölüm 11 ölçüm hedefleri onaylı değildir`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — retention/uyum politikası tanımlı değildir`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — Unicode NFC kitaplığı ve sürümü doğrulanamadı`
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k4-yapay-zeka/index.md (L1-L124)` — K4 bağlamı
- `Kanıt: shared/src/AI/KnowledgeBase.php (L1-L195)` — bilgi tabanı erişim çapası
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/k109-semantic-id/semantic-id-tasarim.md`

## 16. Wiki-linkler

- Aynı klasör: [[semantic-id-tasarim.md]] · [[semantic-id-uyum-stratejisi.md]] · [[index.md]]
- `[[../k108-vector-index/vector-index-mimari.md]]` — indeks eşlemesi
- `[[../k110-rag-knowledge/rag-knowledge-tabani.md]]` — bilgi tabanı anahtarları
- `[[../k118-data-labeling/data-labeling-sureci.md]]` — etiket ↔ kimlik
- `[[../k119-prompt-engineering/prompt-engineering-katmani.md]]` — kimlik maskesi
- `[[../k117-content-safety/content-safety-politika-akisi.md]]` — PII/güvenlik

## 17. Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik | Sorumlu |
|---|---|---|---|
| 4.0.0 | 2026-10-06 | D03 dilimi ilk yazım (kimlik tasarımı) | vault-writer |
| 1.0.0 | 2026-09-20 | K4 taslak (yedek kaynak) | - |

## 18. Ek Kurallar (`ID-061`..`ID-100`)

- **ID-061:** Göç penceresi 30 gün varsayılandır; `⚠️ VERIFICATION REQUIRED`.
- **ID-062:** Pencere uzatma yalnız onayla olur.
- **ID-063:** Çift okuma penceresinde yanıt `id_source` alanı taşır (eski/yeni).
- **ID-064:** `id_source` alanı yalnız geçici dönemin sonunda kaldırılır.
- **ID-065:** Göç öncesi geri alma provası yapılır.
- **ID-066:** Geri alma, alias tablosunu geri çevirir (tek işlem).
- **ID-067:** Göç sırasında performans ölçümü yapılır (p95 etkisi).
- **ID-068:** Göç kapanışında eski kimlik referans taraması yapılır.
- **ID-069:** Tarama temizse kapanış onayı verilir.
- **ID-070:** Kapanıştan sonra eski okuma 7 gün daha açık tutulur.
- **ID-071:** Sonrasında eski okuma kapatılır (`SEM-103` üretir).
- **ID-072:** Kimlik üreticisi sürümlüdür (`id_ver`).
- **ID-073:** Sürüm düşüşü (downgrade) yasaktır.
- **ID-074:** Referans seti her sürüm değişikliğinde saklanır.
- **ID-075:** Karşılaştırma seti sürüm deposuna yazılır.
- **ID-076:** Çakışma ölçümü referans sette de yapılır.
- **ID-077:** Probe dağılımında 3σ sapma alarm üretir.
- **ID-078:** Alfabe genişletilmesi yeni sürüm demektir.
- **ID-079:** Alfabe daraltılması yasaktır (okunabilirlik/çakışma).
- **ID-080:** Checksum algoritması değiştirilirse `id_ver` artar.
- **ID-081:** Eski checksum çift doğrulama ile kabul edilir (göç süresince).
- **ID-082:** Kimlik üretim hataları `SEM-*` ailesindedir.
- **ID-083:** `SEM-*` kodları üründen bağımsızdır (VX benzeri taşınabilirlik).
- **ID-084:** Göç işi `checkpoint` ile devam eder.
- **ID-085:** Göç işi eşzamanlı olamaz (tek örnek).
- **ID-086:** Göç işi durdurulabilir ve geri alınabilir.
- **ID-087:** Göç başarısı günlük raporlanır (`sem_migration_pct`).
- **ID-088:** Göç sırasında indeks (`k108`) yeniden yazımı eş güdümlüdür.
- **ID-089:** Eşleme satırı eksikse `SEM-401` alarmı.
- **ID-090:** İndeks kaybı `SEM-402` (reindex tetiklenir).
- **ID-091:** Mutabakat günlük çalışır; fark 0 kabul edilir.
- **ID-092:** Mutabakat farkı 3 gün sürerse P2.
- **ID-093:** Siyah liste ihlali P1'dir (ID-022/023).
- **ID-094:** Siyah liste kayıtları süresiz saklanır.
- **ID-095:** Siyah liste kaydı yalnız `admin` onayıyla silinir.
- **ID-096:** Alias nedeni alanı boş bırakılamaz (denetim).
- **ID-097:** `actor` alanı rol/istek sahibini taşır.
- **ID-098:** Alias değişimi geri alınırsa ters alias yazılır (tek halka).
- **ID-099:** Ters alias da `hop=1` kuralına uyar.
- **ID-100:** Göç kapanışında ters alias arşivlenir.

## 19. Terimler

| Terim | Orijinal identifier | Açıklama |
|---|---|---|
| Semantic ID | semantic id | Anlamlı kimlik |
| Namespace | namespace | Alan/tür adayı |
| Çekirdek kod | core code | Kimliğin ana parçası |
| Checksum | checksum | Denetim kodu |
| Probe | probe | Çakışma adım atlatma |
| Alias | alias | Eski → yeni eşleme |
| Halka | hop | Alias zincir uzunluğu |
| Siyah liste | blacklist | Yeniden kullanım yasağı |
| Doğal anahtar | natural_key | Kaynağın iş anahtarı |
| Göç | migration | Kimlik yenileme |
| Çift okuma | dual read | İki kimlik okuma |
| Tersine çıkarım | reverse inference | id → kullanıcı tahmini |
| Maskeli log | masked log | Kimlik kısmi gösterim |
| Mutabakat | reconciliation | İki sistem eşitliği |
| Determinizm | determinism | Aynı girdi → aynı çıktı |
| Normalizasyon | normalization | NFC eşdeğerleştirme |
| Kiracı | tenant | Veri sahibi |
| Retention | retention | Saklama süresi |
| Denetim izi | audit trail | Değişim geçmişi |

## 20. Karar Kayıtları

| # | Karar | Alternatif | Gerekçe | Durum |
|---|---|---|---|---|
| A1 | İki katman (kalıcı + görünür) | Tek kimlik | Gizlilik + esneklik | kabul |
| A2 | 36 alfabe | Base64 | Okunabilirlik, URL güvenliği | kabul |
| A3 | Alias zinciri 1 halka | Zincir | Bozulma riski | kabul |
| A4 | Checksum zorunlu | Yok | Bozulma/yazım denetimi | kabul |
| A5 | Ölçüm hedefi yok | Erken sabitleme | Halüsinasyon riski | açık (`⚠️ VERIFICATION REQUIRED`) |
| A6 | Retention politikası yok | Varsayılan | Uyum kararı gerekli | açık (`⚠️ VERIFICATION REQUIRED`) |

## 21. Uygulama Örnekleri (şema dışı)

| Senaryo | Girdi | Üretilen | Not |
|---|---|---|---|
| Parça ekleme | `tenant=t, entity=trk, natural_key=UPC123` | `sid_t_v1_9f3k2x7q` | ID-021 idempotent |
| Albüm ekleme | `entity=album, natural_key=ALB-99` | `sid_t_v1_...` | namespace ayrı |
| Sanatçı ekleme | `entity=artist, natural_key=NİL` | `sid_t_v1_...` | NFC normalizasyon |
| Kapak görseli | `entity=cover, natural_key=hash` | `sid_t_v1_...` | PII yok |
| Kullanıcı listesi | `entity=user_coll` | `sid_t_v1_...` | ID-016 PII yok |
| URL paylaşım | `display=9f3k-2x7q` | `/s/9f3k-2x7q` | ID-014 |
| Eski URL | `id_old` | yönlendirme | ID-036 |
| Merge | iki kayıt | birincil + alias | SM-025 |
| Silme | kayıt silinir | siyah liste | ID-022 |
| Yeniden üretim | aynı girdi | aynı kimlik | ID-001 |
| Elle düzenleme | checksum değiştirilir | `SEM-101` | ID-010 |
| Bilinmeyen ns | `ns=xxx` | `SEM-102` | ID-011 |
| Aşırı uzun girdi | 40 karakter | `SEM-104` | ID-020 |
| Log yazımı | tam kimlik | maskeli | ID-030 |
| Denetim okuması | yetkili rol | tam görünüm | ID-031 |

## 22. Ek Kenar Durumları

| # | Durum | Beklenen |
|---|---|---|
| 16 | Aynı doğal anahtar farklı kiracıda | Farklı kimlik (ID-009) |
| 17 | Nazik (NFD) yazım | NFC sonrası aynı kod |
| 18 | Büyük harfli girinti | Alfabe küçük harf (ID-018) |
| 19 | Ayraç içinde `-` | İçe gömülü ayraç ayrıştırma |
| 20 | Checksum algoritması sürüm değişimi | Çift doğrulama (ID-052) |
| 21 | Kimlik üretiminde timeout | `SEM-*` + tekrar |
| 22 | Aynı anda alias + ana yazım | Atomiklik (ID-024) |
| 23 | Eski id silinmiş kayıtta | Alias hedefi `retired` → yönlendirme yok |
| 24 | Görünür kimlik çakışması | `unique` kısıtı → probe (ID-007) |
| 25 | Aynı display iki kayıtta | İkinci üretilirken probe |
| 26 | Kare karakter URL'de | Arındırma (ID-037) |
| 27 | Emoji/aksanlı metin | NFC + çekilme |
| 28 | Çok dilli anahtar | Normalizasyon + aynı alfabe |
| 29 | Siyah liste + eski istemci | Yeni kimlik verilir, eskisi yok |
| 30 | Göç sırasında okuma | Çift okuma penceresi (ID-027) |

## 23. RACI (sorumluluk)

| İş | data-eng | backend-arch | security | sre | ürün |
|---|---|---|---|---|---|
| Kimlik sözdizimi | **R** | A | C | I | I |
| Üretim fonksiyonu | **R** | C | C | I | I |
| Çakışma stratejisi | **R** | C | I | C | I |
| Alias kuralları | **R** | A | C | I | I |
| Gizlilik kontrolü | C | C | **R** | I | A |
| Log maskeleme | C | C | **R** | A | I |
| Göç planı | **R** | A | C | C | I |
| Geri alma provası | C | I | I | **R** | I |
| Kapı onayı | A | C | C | **R** | A |
| Mutabakat | **R** | I | I | C | I |
| Referans taraması | **R** | C | I | I | I |
| Kapanış onayı | A | C | I | **R** | A |
| Ölçüm hedefi onayı | C | I | I | A | **R** |
| Retention kararı | C | I | A | I | **R** |

## 24. Bilinmeyen Envanteri

- **U-01:** Kimlik üreten kod yolu — UNKNOWN.
- **U-02:** Kimlik alfabe boyutu nihai kararı — UNKNOWN (tasarım: 36).
- **U-03:** Checksum algoritması — UNKNOWN (tasarım: mod11 benzeri).
- **U-04:** Çakışma oranı ölçümü — UNKNOWN.
- **U-05:** Probe ortalaması hedefi — UNKNOWN.
- **U-06:** Göç süresi tahmini — UNKNOWN.
- **U-07:** Göç maliyeti — UNKNOWN.
- **U-08:** Arkaik pencere süresi onayı — UNKNOWN.
- **U-09:** Retention politikası — UNKNOWN.
- **U-10:** Uyumluluk gereksinimi (KVKK/SOC2) — UNKNOWN.
- **U-11:** Alias tablosu büyüklüğü — UNKNOWN.
- **U-12:** Kayıt hacmi (parça/albüm/sanatçı) — UNKNOWN.
- **U-13:** Kiracı sayısı — UNKNOWN.
- **U-14:** Eşzamanlı kullanıcı — UNKNOWN.
- **U-15:** API sürümleme politikası — UNKNOWN.
- **U-16:** Referans seti boyutu — UNKNOWN.
- **U-17:** Göç işinin çalıştığı ortam — UNKNOWN.
- **U-18:** Yedek periyodu — UNKNOWN.
- **U-19:** Alarm alıcısı — UNKNOWN.
- **U-20:** Bakım penceresi — UNKNOWN.

## 25. Okuma Sırası ve İlgili Dosyalar

| Sıra | Dosya | Neden |
|---|---|---|
| 1 | `index.md` | bağlam + kapılar |
| 2 | `semantic-id-tasarim.md` | kimlik kuralları |
| 3 | `semantic-id-uyum-stratejisi.md` | göç ve geri alma |

İlgili dosyalar: `[[../k108-vector-index/index.md]]` (indeks eşlemesi),
`[[../k110-rag-knowledge/index.md]]` (bilgi tabanı anahtarları),
`[[../k118-data-labeling/index.md]]` (etiket ↔ kimlik),
`[[../k119-prompt-engineering/index.md]]` (kimlik maskesi bağlamı).

> **Not:** Bu dosyadaki tüm sayılar tasarımdır ve `⚠️ VERIFICATION REQUIRED`
> işaretini taşır. Ölçüm yoksa hedef yoktur; tahmin edilen değer yazılmaz.

## 20. Ek A — Tasarım Karar Matrisi (Tartışma Turu Özeti)

Tartışma turu WORKFLOW §6 protokolü ile yürütülmüştür: Tur 1 = her persona kendi
önerisini kaynaklı yazar, Tur 2 = çapraz eleştiri, Tur 3 = uzlaşma. Bu bölümde
üretilen tasarım kararları ve gerekçeleri derlenmiştir.

### 20.1 Expert görüşleri

| # | Persona | Öneri | Kanıt | Sonuç |
|---|---|---|---|---|
| E1 | `architect` | Vektör araması fetch sınırının altında, uygulama katmanında | KÜÇÜK ölçek varsayımı | Kabul |
| E2 | `security-engineer` | Hash peek anonimleştirilmeden geri çevrilemez | Eşleşmeyen probe sinyal sızıntısı | Kabul |
| E3 | `data-engineer` | Aday kayıtlar tek tabloda, ayırt edici alan `record_type` | Mevcut şema düzeni | Kabul |
| E4 | `embedded-engineer` | Lokal indeks dosyası yazımı atomik olmalı | `rename` atomikliği | Kabul |
| E5 | `qa-engineer` | FPR hedefi CI kapısı olarak tanımlanmalı | `sm-id-fpr` | Kısmen (API yok) |

### 20.2 Senior görüşleri

| # | Persona | Öneri | Kanıt | Sonuç |
|---|---|---|---|---|
| S1 | `backend-architect` | Cookie-hash peek'i controller içinde inline yapılmamalı | Katman kuralı | Kabul |
| S2 | `ui-designer` | Onay arayüzü "Kalıcı tanımlayıcı" metnini içermeli | KVKK açık rıza | Kabul |
| S3 | `devops-engineer` | Rate-limit sinyali ayrı metrik etiketi taşımamalı | Etiket şişmesi | Kabul |
| S4 | `performance-engineer` | p95 hedefi sadece vektör araması içindir | Ölçüm kapsamı | Kabul |
| S5 | `code-reviewer` | `PurviewGroupID` her oturumda yeniden çözümlenmemeli | N+1 riski | Kabul |

### 20.3 Junior görüşleri

| # | Persona | Öneri | Kanıt | Sonuç |
|---|---|---|---|---|
| J1 | `explore` | `shared/src/AI/` altında identifier yok | Dosya taraması | Kabul (boşluk) |
| J2 | `docs-writer` | Kayıp anahtar bölümü vault'ta yok | İçerik taraması | Kabul (boşluk) |
| J3 | `test-writer` | FPR kalibrasyonu için sentetik çift seti gerekli | Test tasarımı | Kabul |
| J4 | `refactorer` | Saklama-işleme fonksiyonu tek yerde toplanmalı | Tekrar kuralı | Kabul |
| J5 | `research-analyst` | NIST SP 800-63C cross-issuer iddia kontrolü referans alınmalı | Dış kaynak | Kabul |
| J6 | `debugger` | "Eşleşme yok" ve "rate-limit" logları karıştırılmamalı | Ayırıcılık | Kabul |
| J7 | `business-analyst` | Onay metni pazarlama dili içermemeli | Hukuki risk | Kabul |
| J8 | `technical-writer` | Sistem akışı bölümünde emoji kullanılmamalı | Vault stili | Kabul |
| J9 | `dependency-manager` | BLAKE3 bağımlılığı eklenmemeli | Yeni bağımlılık yasağı | Kabul |
| J10 | `error-coordinator` | Değerlendirici tarafı 409'u kullanıcıya çevirmemeli | Yanlış mesaj | Kabul |

### 20.4 Uzlaşma kararları (Tur 3)

- **K-01:** Peek, uygulama katmanında ve fetch sınırının altında yürütülür.
- **K-02:** Hash eşleşmesi gizli yanıttır; eşleşmeyen probe genel 404 ile sonuçlanır.
- **K-03:** FPR hedefi (`sm-id-fpr`) tasarım gereksinimidir; ölçümü API olmadığı için
  `⚠️ VERIFICATION REQUIRED` işaretlidir.
- **K-04:** Cross-issuer iddia kontrolü zorunludur; `shared/src/AI/` içinde karşılığı yoktur.
- **K-05:** Onay kaydı, aydınlatma metni sürümü ile birlikte saklanır.
- **K-06:** Yeni bağımlılık eklenmez; standart kütüphane ile çözülür.
- **K-07:** Yerel indeks, atomik `rename` ile güncellenir.
- **K-08:** Isı haritası bucket'ları değiştirilmez; değiştirilirse geçmiş kırılır.

Uzlaşma 8/8 sağlanmıştır; `🔴 VERIFICATION REQUIRED` ile sonuçlanan karar yoktur.
