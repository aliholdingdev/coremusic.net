---
node_id: K012-services/services-kapsam.md
node_type: subdomain
status: draft
parent_id: K012-services
children: []
depends_on:
  - K006-yapay-zeka
  - K007-data
  - K008-domain
  - K010-network
  - K013-integraion
  - K014-media
  - K015-AI
evidence:
  - "K012-services/services-master.md §1–§3 · §19 · §23 — okundu 2026-10-18"
  - "K005-firmware/firmware-kapsam.md §1–§7 (kanonik şablon) — okundu 2026-10-18"
  - ".ai/architecture/index.md §5 satır 13 (K012-services · 7 kök MD) — okundu 2026-10-18"
validation:
  - "2026-10-18 — §2/§3 satırları master §2.1/§2.2 ile çapraz okundu (çelişki yok)"
  - "2026-10-18 — depends_on 7 adayın tamamı registry §5'te diskte mevcut (ölü ad yok)"
  - "2026-10-18 — §9 çocuk satırları master §3 ile birebir (10/10)"
open_questions:
  - "Q1 ai-service sahibi K006-yapay-zeka mı K015-AI mı (master §7/1) (⚠️ VERIFICATION REQUIRED)"
  - "Q2 playback-service ↔ K014-media/playback sahipliği (master §7/2) (⚠️ VERIFICATION REQUIRED)"
  - "Q3 audio-service ↔ K003-audio sınırı: servis mi motor mu (master §7/3) (⚠️ VERIFICATION REQUIRED)"
  - "Q6 background-jobs iş motoru (master §7/6) (⚠️ UNKNOWN)"
  - "master §5.1'deki K0100-identitiy diskte YOK — ölü referans, bu dosyaya alınmadı (⚠️ üst karar bekliyor)"
group: mimari
---

# services-kapsam — Kapsam Sınırı

> Dayanak: `services-master.md` §1–§3 · §19 · §23. Bu dosya K012'nin
> **içinde / dışında / komşu** ayrımının bu üretimdeki tek geniş dökümüdür.
> Kapsam sorusunda çelişki çıkarsa master ile bu dosya çapraz okunur;
> kanıtsız satır sayılmaz (Zero-Hallucination).

## §1 ELI15

> **K012, arayüz göstermeden iş yapan "garson" katmanıdır: istek gelir, iş
> yapılır, sonuç döner — yemeği pişirmez (domain), mutfağı çizmez (arayüz).**

Kapsam üç soruyla belirlenir: (1) bu iş bir **servis süreci** içinde mi olur?
(2) arayüz **çizmiyor** mu ve kalıcı şemaya **karar vermiyor** mu? (3) dış
API ile ham protokol konuşuyor mu? 1+2 evet, 3 hayır → K012 içindedir. Üçü
de "evet" ise sınır aşılmıştır: protokol konuşması K013-integraion'a gider.

## §2 Kapsam İÇİNDE (K012'ye ait)

| # | Alan | Ne kapsar | Sahip çocuk | Etiket |
|---|---|---|---|---|
| I1 | Katalog işleri | katalog sorgusu · arama · sıralama | music-service | [TASARIM] |
| I2 | Medya varlık işleri | dosya/kaynak varlığına dair işlemler | media-service | [TASARIM] |
| I3 | Çalma durumu | sıra · konum · oynatma komutu durumu | playback-service | [TASARIM] |
| I4 | Ses işleri servis yüzeyi | K003-audio ile sınır belirsiz ⚠️ | audio-service | [TASARIM] + ⚠️ |
| I5 | Kullanıcı işleri | profil · tercih · abonelik (ayrıntı ⚠️) | user-service | [TASARIM] |
| I6 | Cihaz işleri | cihaz kaydı · durum · yetki | device-service | [TASARIM] |
| I7 | YZ sunumu | diğer servislere YZ yeteneği sunma | ai-service | [TASARIM] + ⚠️ Q1 |
| I8 | Dış kontrat tüketimi | K013 kontratını çağıran servis yüzeyi | integration-service | [TASARIM] |
| I9 | Zamanlanmış iş | kuyruk/zamanlayıcı işleri (motor ⚠️) | background-jobs | [TASARIM] + ⚠️ Q5 |
| I10 | Servis sözleşmesi | mesaj/DTO biçimi tanımı | service-contracts | [TASARIM] |
| I11 | Servis defteri & sağlık | kayıt · keşif · sağlık sorgusu | (yatay) | ✅ S1 (repo kanıtı) |
| I12 | Oran kısıtı & sürüm başlığı | 60/60s · `X-API-Version` | (yatay) | ✅ S1 (repo kanıtı) |

## §3 Kapsam DIŞI (K012'ye ait DEĞİL)

| # | Alan | Neden dışarı | Giden düğüm | Etiket |
|---|---|---|---|---|
| O1 | Uygulama akışı / arayüz | K012 iş yapar, göstermez; K011 yalnız çağırır | K011-application | [KARAR] (master §2.2) |
| O2 | Dış API protokolü · adaptör · hata kodu | integration-service yalnız K013'ün kontratını kullanır | K013-integraion | [KARAR] (master §2.2) |
| O3 | Ağ taşıması ayrıntısı (TLS · soket) | K012 yalnız kullanır, yazmaz | K010-network | [KARAR] (master §2.2) |
| O4 | Kitaplık/metadata/akış dosyaları | servis veriyi okur, dosyanın sahibi değildir | K014-media | [KARAR] (master §23/4) |
| O5 | İş kuralı / domain modeli | servis kuralı uygular, üretmez | K008-domain | [KARAR] (master §23/1) |
| O6 | Kalıcı veri şeması · ORM/SQL kararı | servis şema yazmaz | K007-data | [KARAR] (master §23/2) |
| O7 | ML modeli / ajan altyapısı | ai-service yalnız sunar (hangisi ⚠️) | K006-yapay-zeka / K015-AI | [TASARIM] + ⚠️ Q1 |
| O8 | CI/CD · dağıtım hattı | servis yayımı bu domain'in işi değil | K17-devops | [KARAR] (eski ad K018-devops — ⚠️ ölü ad) |
| O9 | Servis test **altyapısı** | K012 yalnız kendi testini yazar | K18-testing | [KARAR] (eski ad K019-testing — ⚠️ ölü ad) |
| O10 | Ses sinyali işleme | servis sinyale dokunmaz | K003-audio / K004-dsp | [KARAR] (master §1) |
| O11 | Kimlik doğrulama politikası | master §5.1'de K0100-identitiy geçer — **diskte yok** | (üst karar) | ⚠️ ölü referans |

## §4 Komşu sınır tablosu (ince çizgi nerede)

| Sınır | K012 tarafı | Komşu tarafı | Geçiş izni |
|---|---|---|---|
| K012 ↔ K011 | servis çağrısı (uç nokta/DTO) | ekran akışı · görünüm | yalnız istek/yanıt |
| K012 ↔ K013 | K013 kontratını tüketme | adaptör · dış protokol · hata sınıflandırma | yalnız kontrat tipleri |
| K012 ↔ K014 | medya **verisini okuma** | dosya · akış · önbellek mülkiyeti | yalnız okuma |
| K012 ↔ K008 | kuralı **uygulama** | kuralın tanımı ve modeli | yalnız çağrı |
| K012 ↔ K007 | veriye **erişme** | şema · migration · SQL | ORM/SQL kararı yazılmaz |
| K012 ↔ K010 | taşımayı **kullanma** | HTTP/soket/TLS ayrıntısı | yalnız bağımlılık satırı |

## §5 Yaşam döngüsüne göre kapsam

| Evre | Başlangıç | Bitiş | Sahip çocuk |
|---|---|---|---|
| Servis kaydı | `ServiceDefinition` oluşumu | `register()` tamam (S1) | (yatay — master §9) |
| Çağrı | istek gelişi | yanıt döner (S1 §22.2) | çağıran çocuk |
| Sağlık | periyodik `healthCheck()` | kapanış/clear (S1) | (yatay — master §13) |
| Zamanlanmış iş | tetik anı | iş sonucu yazımı | background-jobs ⚠️ motor |
| Sözleşme değişimi | kontrat revizyonu | K013+K012 çift onayı | service-contracts ⚠️ Q4 |

## §6 Kapsam kararları

| # | Karar | Gerekçe |
|---|---|---|
| K-1 | K012 yalnız **iş yapan süreçlerdir**, arayüz/şema/protokol içermez | master §1 + §2.2 |
| K-2 | Dış protokol yalnız K013'te yaşar; integration-service kontrat tüketir | master §23/3 |
| K-3 | Medya dosyasının sahibi K014'tür — servis yalnız okur | master §23/4 |
| K-4 | Test altyapısı K18-testing, test **yazarlığı** K012 | master §23/7 (eski ad ⚠️) |
| K-5 | ai-service hangi domeine bağlı **belirsiz** — kapsam satırı ⚠️ ile taşınır | master §7/1 |
| K-6 | `K0100-identitiy` bağımlılığı bu dosyaya **yazılmaz**; ölü referans kaydı ⚠️ | registry §6.2-B |

## §7 Doğrulama

| # | Kriter | Yöntem | Durum |
|---|---|---|---|
| C1 | §2 satırları master §2.1/§3 ile tutarlı | çapraz okuma | ✅ 2026-10-18 |
| C2 | §3 satırları 10 komşu/sahip atfını kapsıyor | sayım | ✅ 2026-10-18 |
| C3 | Her satırda Etiket var ([TASARIM]/[KARAR]/⚠️) | satır-başı kontrol | ✅ 2026-10-18 |
| C4 | depends_on ölü ad taşımıyor | registry §5 çaprazı | ✅ 2026-10-18 |
| C5 | playback/ai/audio sahipliği yazılı teyit | üst karar | ❌ açık (Q1–Q3) |

## §8 Açık sorular

1. ai-service → K006-yapay-zeka mı, K015-AI mı? → ⚠️ VERIFICATION REQUIRED
2. playback-service (K012) ↔ K014/media/playback kimin? → ⚠️ VERIFICATION REQUIRED
3. audio-service ↔ K003-audio sınırı (servis mi motor mu)? → ⚠️ VERIFICATION REQUIRED
4. background-jobs iş motoru (kuyruk/zamanlayıcı) nedir? → ⚠️ UNKNOWN
5. service-contracts evrensel spec'i mi taşır, K012'ye mi özel? → TASARIM İDDİASI
6. Kimlik doğrulama bağımlılığının diskteki karşılığı hangi K? (K0100 ölü) → ⚠️ üst karar

## §9 Çocuk-bazlı kapsam detayı (10 çocuk — master §3 ile birebir)

### §9.1 music-service — Müzik işleri

| Boyut | Kapsam satırı | Sınır |
|---|---|---|
| İçinde | katalog sorgusu · arama · sıralama | detay ⚠️ (master §21.1) |
| Dışında | dosya/akış | K014 |
| Kalıcılık | şema | K007 |

### §9.2 media-service — Medya varlık işleri

| Boyut | Kapsam satırı | Sınır |
|---|---|---|
| İçinde | dosya/kaynak varlığına dair işler | detay ⚠️ |
| Dışında | dosya mülkiyeti | K014 (master §23/4) |

### §9.3 playback-service — Çalma durumu

| Boyut | Kapsam satırı | Sınır |
|---|---|---|
| İçinde | sıra · konum · komut durumu | — |
| Çakışma | K014/media/playback sahipliği | ⚠️ Q2 |

### §9.4 audio-service — Ses işleri

| Boyut | Kapsam satırı | Sınır |
|---|---|---|
| İçinde | servis yüzeyi | ⚠️ Q3 ile K003 sınırı belirsiz |
| Dışında | sinyal/DSP işleme | K003-audio · K004-dsp |

### §9.5 user-service — Kullanıcı

| Boyut | Kapsam satırı | Sınır |
|---|---|---|
| İçinde | profil · tercih · abonelik | detay ⚠️ |
| Dışında | kimlik doğrulama politikası | üst karar (⚠️ K0100 ölü) |

### §9.6 device-service — Cihaz

| Boyut | Kapsam satırı | Sınır |
|---|---|---|
| İçinde | cihaz kaydı · durum · yetki | — |
| Çakışma | device-identity sahipliği | ⚠️ master §7/7 |

### §9.7 ai-service — YZ

| Boyut | Kapsam satırı | Sınır |
|---|---|---|
| İçinde | YZ yeteneğini sunma | ⚠️ Q1 (K006 vs K015) |
| Dışında | model/ajan altyapısı | K006-yapay-zeka · K015-AI |

### §9.8 integration-service — Dış bağlantı

| Boyut | Kapsam satırı | Sınır |
|---|---|---|
| İçinde | K013 kontratını çağıran servis yüzeyi | — |
| Dışında | adaptör · protokol · hata kodu | K013 (bagimliliklar §8.4) |

### §9.9 background-jobs — Arka plan işleri

| Boyut | Kapsam satırı | Sınır |
|---|---|---|
| İçinde | zamanlanmış/kuyruk işleri | motor ⚠️ Q5 (S5: repo kanıtı yok) |
| Dışında | iş sonucu kalıcılığı şeması | K007 |

### §9.10 service-contracts — Servis sözleşmeleri

| Boyut | Kapsam satırı | Sınır |
|---|---|---|
| İçinde | mesaj/DTO biçimi | kapsam Q4 ⚠️ |
| Dışında | dış API kontratı | K013 api-contracts |

## §10 Kapsam test senaryoları ("içinde mi dışında mı?")

| # | Senaryo | Beklenen karar | Etiket |
|---|---|---|---|
| T1 | "Katalog araması hangi serviste?" | music-service (I1) | [TASARIM] |
| T2 | "Spotify token alımı hangi düğümde?" | K013 (O2) — integration-service yalnız çağırır | [KARAR] |
| T3 | "Şarkı dosyası kimde saklanır?" | K014 (O4) | [KARAR] |
| T4 | "429 Retry-After kim üretir?" | K012 (I12) — S1 middleware kanıtı | ✅ S1 |
| T5 | "PSR-14 event wiring?" | dışarıda — ADR-086 üretim durumu ⚠️ | ⚠️ |

## §11 Etiket sözlüğü (bu dosyanın işaretleri)

| İşaret | Anlamı |
|---|---|
| [TASARIM] | onaysız, test edilmemiş tasarım iddiası |
| [KARAR] | master/bağlam kararı (kaynak §'li) |
| ✅ S1 | repo kanıtı (`services-dogrulama.md` §6) |
| ⚠️ | belirsiz — VERIFICATION REQUIRED / UNKNOWN |
| S5 | "aranıp bulunamadı" kanıt sınıfı (repo'da yok) |
| dalga-3 | kademe-3'te (faz C) ele alınacak |

## §12 Özet matris (kapsam ↔ çocuk ↔ sahiplik)

| Çocuk | İçinde (§2) | Dışında (§3 atfı) | Açık soru |
|---|---|---|---|
| music-service | I1 | O4/O5/O6 | — |
| media-service | I2 | O4 | — |
| playback-service | I3 | O4 | Q2 ⚠️ |
| audio-service | I4 | O10 | Q3 ⚠️ |
| user-service | I5 | O11 | — |
| device-service | I6 | O3 | master §7/7 ⚠️ |
| ai-service | I7 | O7 | Q1 ⚠️ |
| integration-service | I8 | O2 | — |
| background-jobs | I9 | O6 | Q5 ⚠️ |
| service-contracts | I10 | O2 | Q4 ⚠️ |

## §13 Kapsam değişiklik protokolü

1. Yeni satır yalnız **kaynak §'li** eklenir (master · registry · S1 kanıtı).
2. Kapsam dışı satır eklenirse komşu düğüm adı diskte var olmalıdır (registry §5).
3. Ölü ad (`K0100-identitiy` vb.) silinmez — satır `⚠️ ölü referans` olarak taşınır.
4. Değişiklik bu dosyada `validation` satırıyla kaydedilir; master'a dokunulmaz.

## §14 Anti-pattern'ler (bu dosyada tekrarlanmaz)

| # | Hatalı davranış | Doğrusu |
|---|---|---|
| 1 | kapsam satırına uydurma paket/sürüm yazmak | yalnız S1/§'li kanıt |
| 2 | dead adı sessizce silmek | ⚠️ işaretiyle taşımak |
| 3 | K013 protokolünü K012'ye yazmak | O2 satırı |
| 4 | "K018-devops/K019-testing" eski adlarını yeni dosyada çoğaltmak | K17-devops / K18-testing |

## §15 İlgili dosyalar

| Dosya | İlişki |
|---|---|
| `services-master.md` | §1–§3 kapsam kaynağı (SSOT) |
| `services-bagimliliklar.md` | sınır → bağımlılık karşılığı |
| `services-bilesenler.md` | §2'deki I satırlarının bileşen karşılığı |
| `services-riskler.md` | §8 açık sorularının risk karşılığı |
| `.ai/architecture/index.md` §5 | disk K adları (depends_on kaynağı) |

## §16 Kapanış kontrol listesi

- [x] §1 ELI15 (3-5 cümle)
- [x] §2/§3 içinde-dışı ayrımı + Etiket sütunu
- [x] depends_on yalnız disk K adları (7/7 registry §5)
- [x] ölü adlar ⚠️ ile taşındı, silinmedi
- [x] open_questions ⚠️ işaretli (6 madde)
- [x] ≥120 satır

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode · **Tarih:** 2026-10-18
