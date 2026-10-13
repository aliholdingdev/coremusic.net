---
node_id: K012-services
node_type: module
role: bagimliliklar
status: draft
parent_id: K012-services
children: []
depends_on:
  - "K006-yapay-zeka"
  - "K007-data"
  - "K008-domain"
  - "K010-identitiy"
  - "K013-integraion"
  - "K014-network"
  - "K015-media"
evidence:
  - "v2 §2.12 çocuk adları (birebir, 2026-10-12)"
  - "2026-10-12 beyanları"
validation:
  - "DAG denetimi — §1'de K012'ye geri dönüş yok (2026-10-12)"
  - "Etiket denetimi — her satır TASARIM İDDİASI/verified/⚠️ (tur-1)"
open_questions:
  - "ai-service → K006 vs K016 (Q1)"
  - "playback-service ↔ K015/playback sınırı (Q2)"
  - "audio-service ↔ K003-audio sınırı (Q3)"
  - "service-contracts kapsamı (Q4)"
  - "background-jobs iş motoru (Q5)"
  - "device-identity sahipliği (Q6)"
date: 2026-10-12
updated: 2026-10-13
version: 2.0.0
etiket: "TASARIM İDDİASI — bu belge kanıt değil, onay gerektiren tasarım iddiasıdır"
authority: "SSOT: v2 §2.12 + ADR-097 §5.2"
group: mimari
---


# K012-services — services-bagimliliklar.md

> **Etiket kuralı:** Aşağıdaki her bağımlılık satırı **TASARIM İDDİASI**'dır —
> isimlerden ve v2 ağacından türetildi, test edilmedi. Kanıt olarak yalnızca
> (a) v2 §2.12 çocuk adları ve (b) 2026-10-12 beyanları geçerlidir.
> Bu dosyada değişiklik = Vault Steward onayı + ADR-097 kaydı ister.

## §0 Etiket sözlüğü (bu belge boyunca)

| Etiket | Anlamı |
|---|---|
| TASARIM İDDİASI | Türetilmiş, test edilmemiş tasarım — onaylanana kadar bağlayıcı değil |
| ⚠️ VERIFICATION REQUIRED | Cevabı bilinmiyor, tahmin yazılmadı |
| ✅ verified | Kanıt var (v2 metni veya 2026-10-12 beyanı) |

## §1 Giden bağımlılıklar (depends_on →)

| # | Hedef düğüm | Ne için (ELI15) | Bağımlılık tipi | Risk |
|---|---|---|---|---|
| 1 | K006-yapay-zeka / K016-AI | ai-service'in arkasındaki YZ yeteneği | yetenek çağrısı | **Hangisi belli değil** ⚠️ (registry §6) |
| 2 | K007-data | Kalıcı veri okuma/yazma | veri erişimi | Şema kararı K007'de kalmalı |
| 3 | K008-domain | İş kuralı ve model | kural tüketimi | Servis kuralı **üretirse** ihlal |
| 4 | K010-identitiy | Kullanıcı/cihaz kimliği | kimlik tüketimi | device-identity sahipliği belirsiz ⚠️ |
| 5 | K013-integraion | Dış sistem kontratları | kontrat | Servis protokolü elle konuşmamalı |
| 6 | K014-network | Servisler arası taşıma | taşıma | Taşıma biçimi UNKNOWN ⚠️ |
| 7 | K015-media | Kitaplık/metadata/akış verisi | veri okuma | İki gerçeklik riski (playback sahipliği) |

## §2 Gelen bağımlılıklar (used_by ←)

| # | Kaynak düğüm | Ne için | Etiket |
|---|---|---|---|
| 1 | K011-application | Uygulamanın servis çağrıları | TASARIM İDDİASI |
| 2 | K019-testing | Servis seviyesi testleri | TASARIM İDDİASI (sahiplik ⚠️) |
| 3 | Diğerleri | — | ⚠️ VERIFICATION REQUIRED (v2'de ters ok yok) |

## §3 Entegrasyon sınırları (K012 dışına çıkış kapıları)

| # | Sınır | Hangi arayüzden geçilir | Sahip düğüm | Arayüz adı |
|---|---|---|---|---|
| 1 | Kalıcılık | servis ↔ veri | K007-data | ⚠️ UNKNOWN (repository/ORM biçimi) |
| 2 | Kural | servis ↔ domain | K008-domain | ⚠️ UNKNOWN (çağrı biçimi) |
| 3 | Kimlik | servis ↔ kimlik | K010-identitiy | ⚠️ UNKNOWN (token/oturum biçimi) |
| 4 | Dış dünya | integration-service ↔ adaptör | K013-integraion | ⚠️ UNKNOWN (kontrat biçimi) |
| 5 | Taşıma | servis ↔ ağ | K014-network | ⚠️ UNKNOWN (protokol seti) |
| 6 | Medya | servis ↔ kitaplık/akış | K015-media | ⚠️ UNKNOWN (görünüm sözleşmesi) |
| 7 | Arayüz | uygulama ↔ servis | K011-application | ⚠️ UNKNOWN (çağrı biçimi) |

### §3.1 Sınır kuralı (Dependency Rule — bu domain için)

1. K012 yalnız bu yedi kapıdan çıkar; başka domain'e doğrudan dosya bağımlılığı **yasak**.
2. Dış dünya (K013) dışında kalan kapıların sahibi kontratı tanımlar; K012 dayatmaz.
3. Servisler birbirine doğrudan değil, service-contracts üzerinden konuşur → **iddia**.
4. Her kapı için arayüz adı ayrı kanıtla belgelenir (tur-2) — şimdi UNKNOWN.

## §4 Riskli bağımlılıklar

| # | Risk | Neden riskli | Etki | Aksiyon |
|---|---|---|---|---|
| 1 | ai-service → K006 / K016 belirsizliği | iki YZ domain'i (registry §6) | yanlış sahiplik | §7/1 — üst karar |
| 2 | playback-service ↔ K015/playback | iki "çalma" sahibi | iki gerçek durum | §7/2 — sınır ADR'si |
| 3 | audio-service ↔ K003-audio | servis mi motor mu belirsiz | sorumluluk kayması | §7/3 |
| 4 | device-service ↔ K010 device-identity | kimlik verisi iki yerde | güvenlik riski | §7/7 |
| 5 | service-contracts tekeli | tüm servisler buraya bağlı | sözleşme değişimi kırıcı | sürümleme ADR'si |
| 6 | K015'e çift bağımlılık (K012→K015, K015→K012 ihtimali) | döngü riski | test/dağıtım zorluğu | K015 dosyasında DAG denetimi |
| 7 | Kimlik (K010) yazımı "identitiy" | v2 kuralı imla hatalarını korur | yanlış arama | yazım DOKUNULMAZ (v2 §3) |

**Döngü denetimi:** §1'de K012'ye geri dönüş yok → graf **DAG** (2026-10-12).

## §5 Değişiklik kuralı

1. Satır eklemek/çıkarmak: Vault Steward onayı + ADR-097 §5.2 kaydı.
2. Yeni bağımlılık = yeni entegrasyon sınırı (§3 + §3.1) ve risk satırı (§4).
3. Etiket zorunlu: her satır TASARIM İDDİASI, ✅ verified veya ⚠️ ile işaretli.
4. "Kesin öyle" cümlesi bu dosyada **yazılmaz** (Truth Mode).
5. v2'de geçen adlar imla düzeltmesi yapılmadan yazılır (K010-identitiy dahil).

## §6 Giden Bağımlılık Detayları (7 kapı)

### §6.1 K006-yapay-zeka / K016-AI → ai-service

| Alan | Değer | Etiket |
|---|---|---|
| Hedef | iki adaydan hangisi | ⚠️ VERIFICATION REQUIRED (Q1) |
| Ne için | YZ yeteneği (öneri, prompt, araç çağrısı) arayüzünün arkası | TASARIM İDDİASI |
| Bağımlılık tipi | yetenek çağrısı | TASARIM İDDİASI |
| Kanıt (varsa) | vault `ai-service/index.md` ELI15 satırı | ✅ verified (metin) |
| Kanıt (yoksa) | ai-service implementasyon dosyası repo'da YOK | S5 — §7/3 dogrulama |
| Risk | yanlış domene atama → sahiplik kargaşası | §4/1 |
| Karar mercii | üst karar (Vault Steward + registry §6) | bekliyor |

### §6.2 K007-data

| Alan | Değer | Etiket |
|---|---|---|
| Hedef | K007-data | ✅ verified (v2 §2.12 adı) |
| Ne için | kalıcı veri okuma/yazma | TASARIM İDDİASI |
| Bağımlılık tipi | veri erişimi | TASARIM İDDİASI |
| Sınır | şema kararı K007'de kalır; K012 şema yazmaz | TASARIM İDDİASI |
| Bilinmeyen | repository/ORM biçimi | ⚠️ UNKNOWN (§3/1) |
| Repo kanıtı | veri erişim katmanı kodu bu görevde incelenmedi | ⚠️ VERIFICATION REQUIRED |

### §6.3 K008-domain

| Alan | Değer | Etiket |
|---|---|---|
| Hedef | K008-domain | ✅ verified (v2 §2.12 adı) |
| Ne için | iş kuralı ve model tüketimi | TASARIM İDDİASI |
| Bağımlılık tipi | kural tüketimi | TASARIM İDDİASI |
| Kural | servis kural **üretirse** ihlal — üretmek K008'e ait | TASARIM İDDİASI |
| Bilinmeyen | çağrı biçimi (arayüz adı) | ⚠️ UNKNOWN (§3/2) |
| Denetim | kural üretimi tespiti → ihlal kaydı | bekliyor |

### §6.4 K010-identitiy

| Alan | Değer | Etiket |
|---|---|---|
| Hedef | K010-identitiy (yazım korunur) | ✅ verified (v2 §3 imla kuralı) |
| Ne için | kullanıcı/cihaz kimliği tüketimi | TASARIM İDDİASI |
| Bağımlılık tipi | kimlik tüketimi | TASARIM İDDİASI |
| Bilinmeyen | device-identity sahipliği (device-service mi K010 mu) | ⚠️ (Q6) |
| Güvenlik | kimlik verisi iki yerdeyse sızıntı riski | §4/4 |
| Denetim | kimlik verisinin tek yazma noktası aranır | bekliyor |

### §6.5 K013-integraion

| Alan | Değer | Etiket |
|---|---|---|
| Hedef | K013-integraion (yazım korunur) | ✅ verified (v2 §2.12 adı) |
| Ne için | dış sistem kontratları | TASARIM İDDİASI |
| Bağımlılık tipi | kontrat | TASARIM İDDİASI |
| Kural | servis protokolü elle konuşmaz; adaptör üzerinden | TASARIM İDDİASI |
| Bilinmeyen | kontrat biçimi | ⚠️ UNKNOWN (§3/4) |
| Kapsam dışı | K013'e yazma yasağı (görev kapsamı) | ✅ verified (görev kuralı) |

### §6.6 K014-network

| Alan | Değer | Etiket |
|---|---|---|
| Hedef | K014-network | ✅ verified (v2 §2.12 adı) |
| Ne için | servisler arası taşıma | TASARIM İDDİASI |
| Bağımlılık tipi | taşıma | TASARIM İDDİASI |
| Bilinmeyen | taşıma biçimi (HTTP? queue? CLI?) | ⚠️ UNKNOWN (Q9) |
| İlişki | K014 bu görevin ikinci kökü — derinleştirme sırası | ✅ verified (görev planı) |
| Döngü riski | K014→K012 bağımlılığı varsa çift yön | §9 — DAG denetimi bekliyor |

### §6.7 K015-media

| Alan | Değer | Etiket |
|---|---|---|
| Hedef | K015-media | ✅ verified (v2 §2.12 adı) |
| Ne için | kitaplık/metadata/akış verisi okuma | TASARIM İDDİASI |
| Bağımlılık tipi | veri okuma | TASARIM İDDİASI |
| Bilinmeyen | görünüm sözleşmesi (ne okunuyor) | ⚠️ UNKNOWN (§3/6) |
| Risk | playback sahipliği K012/K015 iki gerçeklik | §4/2 |
| İlişki | K015 bu görevin üçüncü kökü | ✅ verified (görev planı) |

## §7 Gelen Bağımlılık Detayları (used_by)

### §7.1 K011-application

| Alan | Değer | Etiket |
|---|---|---|
| Kaynak | K011-application | TASARIM İDDİASI |
| Ne için | uygulamanın servis çağrıları | TASARIM İDDİASI |
| Yön | K011 → K012 (gelen ok) | TASARIM İDDİASI |
| Kanıt | v2'de ters ok açıkça yazılmıyor | ⚠️ VERIFICATION REQUIRED |
| Çağrı biçimi | bilinmiyor | ⚠️ UNKNOWN |

### §7.2 K019-testing

| Alan | Değer | Etiket |
|---|---|---|
| Kaynak | K019-testing | TASARIM İDDİASI |
| Ne için | servis seviyesi testleri | TASARIM İDDİASI |
| Sahiplik | testin K019'da mı K012'de mi | ⚠️ VERIFICATION REQUIRED |
| Repo kanıtı | `shared/tests/` 22 dosya (vault §25.2 beyanı) | ✅ metin-seviyesi |

### §7.3 Diğerleri

| Alan | Değer | Etiket |
|---|---|---|
| Kaynak | v2'de K012'ye gelen başka ok yok | ✅ verified (okuma) |
| Sonuç | ters bağımlılık listesi kapalı mı | ⚠️ VERIFICATION REQUIRED (v2 genişletilebilir) |
| Denetim | v2 §2.12 yeniden okunarak doğrulanır | tur-3 |

## §8 Entegrasyon Sınırı Detayları (7 kapı)

### §8.1 Kalıcılık kapısı (servis ↔ veri)

| Alan | Değer |
|---|---|
| Sahip | K007-data |
| Arayüz adı | ⚠️ UNKNOWN (repository/ORM biçimi) |
| Geçiş | servis → repository arayüzü → K007 |
| Kural | K012 SQL şema yazmaz (Data Engineer domain'i) |
| Etiket | TASARIM İDDİASI |

### §8.2 Kural kapısı (servis ↔ domain)

| Alan | Değer |
|---|---|
| Sahip | K008-domain |
| Arayüz adı | ⚠️ UNKNOWN (çağrı biçimi) |
| Geçiş | servis → domain servisi/arayüzü → K008 |
| Kural | iş kararı servis içinde tekrarlanmaz |
| Etiket | TASARIM İDDİASI |

### §8.3 Kimlik kapısı (servis ↔ kimlik)

| Alan | Değer |
|---|---|
| Sahip | K010-identitiy |
| Arayüz adı | ⚠️ UNKNOWN (token/oturum biçimi) |
| Geçiş | servis → kimlik doğrulama → K010 |
| Kural | kimlik doğrulama middleware'de (PSR-15 iddiası — Q8) |
| Etiket | TASARIM İDDİASI |

### §8.4 Dış dünya kapısı (integration-service ↔ adaptör)

| Alan | Değer |
|---|---|
| Sahip | K013-integraion |
| Arayüz adı | ⚠️ UNKNOWN (kontrat biçimi) |
| Geçiş | integration-service → adaptör → dış sistem |
| Kural | dış protokol yalnız adaptörde yaşar |
| Etiket | TASARIM İDDİASI |

### §8.5 Taşıma kapısı (servis ↔ ağ)

| Alan | Değer |
|---|---|
| Sahip | K014-network |
| Arayüz adı | ⚠️ UNKNOWN (protokol seti) |
| Geçiş | servis → taşıma katmanı → K014 |
| Repo kanıtı | yalnız `.htaccess`/`web.config` yönlendirme (S1) |
| Etiket | TASARIM İDDİASI + kısmi S1 |

### §8.6 Medya kapısı (servis ↔ kitaplık/akış)

| Alan | Değer |
|---|---|
| Sahip | K015-media |
| Arayüz adı | ⚠️ UNKNOWN (görünüm sözleşmesi) |
| Geçiş | servis → medya verisi → K015 |
| Repo kanıtı | `MusicStreamHandler.php` (MIME/Range) — K015 ortak kanıtı |
| Etiket | TASARIM İDDİASI + kısmi S1 |

### §8.7 Arayüz kapısı (uygulama ↔ servis)

| Alan | Değer |
|---|---|
| Sahip | K011-application |
| Arayüz adı | ⚠️ UNKNOWN (çağrı biçimi) |
| Geçiş | K011 → servis girişi |
| Repo kanıtı | registry `discover()` (S1) — çağrının kendisi bilinmiyor |
| Etiket | TASARIM İDDİASI |

## §9 Döngü ve DAG Denetimi

| # | Parite | Yön | Döngü? | Kanıt |
|---|---|---|---|---|
| 1 | K012 → K006/K016 | giden | hayır | §6.1 |
| 2 | K012 → K007 | giden | hayır | §6.2 |
| 3 | K012 → K008 | giden | hayır | §6.3 |
| 4 | K012 → K010 | giden | hayır | §6.4 |
| 5 | K012 → K013 | giden | hayır | §6.5 |
| 6 | K012 → K014 | giden | hayır (K014→K012 bilinmiyor) | §6.6 + ⚠️ |
| 7 | K012 → K015 | giden | hayır (K015→K012 ihtimali §4/6) | §6.7 + ⚠️ |
| 8 | K011 → K012 | gelen | hayır | §7.1 |
| 9 | K019 → K012 | gelen | hayır | §7.2 |

**Sonuç:** K012 iç grafiği **DAG** (2026-10-12). Çift-yön riski iki kapıda:
K014 ve K015 — bu kökler derinleştirilirken ters ok aranacak (tur-3).

## §10 Risk Kaydı Derinlemesine (7 risk)

| # | Risk | Olasılık | Etki | Aksiyon | Kapandı mı |
|---|---|---|---|---|---|
| 1 | ai-service sahiplik belirsizliği | yüksek | yüksek | üst karar (Q1) | hayır |
| 2 | playback iki gerçeklik | yüksek | yüksek | sınır ADR'si (Q2) | hayır |
| 3 | audio sınır kayması | orta | orta | Q3 kapanışı | hayır |
| 4 | kimlik verisi iki yerde | orta | yüksek (güvenlik) | tek yazma noktası denetimi (Q6) | hayır |
| 5 | service-contracts tekeli | orta | yüksek (kırıcı değişim) | sürümleme ADR'si (Q4) | hayır |
| 6 | K012↔K015 döngü riski | düşük | orta | K015 dosyasında DAG denetimi | hayır |
| 7 | imla hatalı adlar (identitiy vb.) | kesin | düşük (arama) | yazım DOKUNULMAZ | kapalı (kural) |

Her risk satırı bir açık soruya (Q1–Q6) bağlanmıştır; soru kapanmadan risk kapanmaz.

## §11 Bağımlılık Tipi Sözlüğü

| Tip | Tanım | K012 örneği |
|---|---|---|
| yetenek çağrısı | başka domene ait yetenek kullanma | ai-service → YZ |
| veri erişimi | kalıcı veri okuma/yazma | K007 |
| kural tüketimi | iş kuralını okuma (üretme) | K008 |
| kimlik tüketimi | kimlik doğrulama/okuma | K010 |
| kontrat | dış sistem sözleşmesi | K013 |
| taşıma | ağ/kanal katmanı | K014 |
| veri okuma | medya verisi görme | K015 |
| gelen çağrı | başka node'un K012'yi çağırm | K011, K019 |

## §12 Açık Sorularla Eşleme

| Açık soru | Bağımlılık satırı | Sınır | Risk |
|---|---|---|---|
| Q1 ai-service K006/K016 | §6.1 | — | §10/1 |
| Q2 playback sahipliği | §6.7 | §8.6 | §10/2 |
| Q3 audio-service sınırı | §6 (K003 doğrudan yok) | — | §10/3 |
| Q4 service-contracts kapsamı | §3.1/3 | tüm kapılar | §10/5 |
| Q5 background-jobs motoru | iç bağımlılık (bu dosya dışı) | — | — |
| Q6 device-identity sahipliği | §6.4 | §8.3 | §10/4 |

## §13 Kanıt Haritası (bu dosya ↔ services-dogrulama.md)

| Bu dosyadaki iddia | Kanıt sınıfı | Doğrulama dosyası |
|---|---|---|
| çocuk adları (v2 §2.12) | S2 | dogrulama §5 (metin) |
| repo kanıtı 19 satır | S1 | dogrulama §6/§12 |
| yokluk iddiaları | S5 | dogrulama §7 |
| URL kanıtları 15 adet | S3 | dogrulama §5/§11 |
| açık sorular Q1–Q9 | açık-soru | dogrulama §4/§15 |

## §14 Değişiklik Günlüğü

| Tarih | Sürüm | Değişiklik | Onay |
|---|---|---|---|
| 2026-10-12 | 1.0.0 | ilk iskelet (§0–§5) | Vault Steward |
| 2026-10-13 | 2.0.0 | §4.2 FM + §6–§16 derinleştirme | Vault Steward (bu görev) |
| — | — | planlanan sonraki değişiklik | Q1–Q6 kapanışı |

## §15 İlgili Dosyalar

| Dosya | İlişki |
|---|---|
| `index.md` | kök indeks — bu dosyanın üstü |
| `services-master.md` | omurga — bağımlılık iddialarının kaynağı |
| `services-dogrulama.md` | kanıt defteri — §13 haritası |
| `services-riskler.md` | risk kaydı — §10 ile eşleşmeli |
| `.ai/architecture/index.md` | kayıt defteri (salt-okunur, bu görevde) |

## §16 Authority

Bu dosyanın her bağımlılık satırı **TASARIM İDDİASI** olarak kalır; yalnız
`✅ verified` etiketli satırlar kanıt taşır. Değişiklik yetkisi Vault Steward'dır.

## §17 Bağımlılık Matrisi (K012 × 7 dış node)

Satırlar K012'nin giden yönü, sütunlar dış node'lar. `X` = bağımlılık var,
`—` = yok, `?` = bilinmiyor.

| Node → | K006/K016 | K007 | K008 | K010 | K013 | K014 | K015 |
|---|---|---|---|---|---|---|---|
| ai-service | X | ? | ? | — | — | ? | ? |
| audio-service | — | ? | X | — | — | ? | X |
| background-jobs | — | X | ? | — | — | ? | — |
| device-service | — | X | — | X | — | ? | — |
| integration-service | — | ? | X | — | X | X | ? |
| media-service | — | X | — | — | — | ? | X |
| music-service | — | X | X | X | — | ? | X |
| playback-service | — | X | X | — | — | ? | X |
| service-contracts | — | — | — | — | — | X | — |
| user-service | — | X | X | X | — | ? | — |

Matris hücreleri **TASARIM İDDİASI**'dır (alt alan §kapsam dosyalarından
türetildi); `?` hücreleri açık soru ile bağlıdır. Matrisin kanıtı yoktur —
tur-3'te her hücre tek tek doğrulanacak veya `UNKNOWN` kalacaktır.

## §18 Kapı Denetim Listesi (tur-3 için)

| # | Kapı | Ne doğrulanacak | Kaynak | Durum |
|---|---|---|---|---|
| 1 | Kalıcılık | gerçek repository arayüzü | repo (`shared/src`) | ⏳ |
| 2 | Kural | domain çağrısı yönü | repo + vault K008 | ⏳ |
| 3 | Kimlik | middleware/token biçimi | repo (Q8) | ⏳ |
| 4 | Dış dünya | adaptör kontratı | K013 (kapsam dışı yazma) | ⏳ |
| 5 | Taşıma | gerçek protokol (Q9) | ağ/traci + repo | ⏳ |
| 6 | Medya | görünüm sözleşmesi | K015 derinleştirme | ⏳ |
| 7 | Arayüz | K011→K012 çağrı biçimi | repo + v2 | ⏳ |

`⏳` = bekliyor; bu kapıların hiçbiri bu turda kapatılmadı, kapatılmış gibi
yazılmadı (Truth Mode).

## §19 Sık Sorular

| # | Soru | Cevap |
|---|---|---|
| 1 | Bu dosyadaki satırlar bağlayıcı mı? | Hayır — TASARIM İDDİASI; onay + ADR ister |
| 2 | İmla hataları düzeltilebilir mi? | Hayır — v2 adları dokunulmaz (K010-identitiy dahil) |
| 3 | Yeni bağımlılık eklenebilir mi? | Evet — §5 kuralı: onay + ADR + §3/§4 kaydı |
| 4 | Bağımlılık silinebilir mi? | Aynı onay zinciri; silme de değişikliktir |
| 5 | Kanıt nerede? | `services-dogrulama.md` (§13 haritası) |

## §20 Bağımlılık Terminolojisi

| Terim | Bu dosyadaki anlamı |
|---|---|
| giden bağımlılık | K012'nin başka node'a yönelen ihtiyacı (depends_on →) |
| gelen bağımlılık | başka node'un K012'yi kullanması (used_by ←) |
| entegrasyon sınırı | K012'nin dış dünyaya açıldığı tek kapı (§3, 7 adet) |
| Dependency Rule | iç katmanın dış katmanı tanımaması/ilkesi — bu dosyada 7 kapı kuralı |
| DAG | döngüsüz yönlü graf — §9 sonucu |
| kapı | sınırın uygulama anındaki arayüz geçişi (§8) |
| sahip | sınır kontratını yazan node (K007 vb.) |
| risk kaydı | §10'daki 7 madde — her biri Q1–Q6'ya bağlı |
| dönüş oku | dış node'dan K012'ye gelen ok (§2) |
| çift yön | iki node arasındaki karşılıklı bağımlılık — döngü riski |
| imla kilidi | v2 adlarının değişmezliği (K010-identitiy vb.) |
| tur-3 | kapıların tek tek doğrulanacağı planlı sonraki tur |

## §21 Komşu Dosyalarla Senkron

| Komşu dosya | Bu dosyayla ortak veri | Senkron durumu |
|---|---|---|
| `index.md` | children listesi (10 alt alan) | ✅ senkron (2026-10-13) |
| `services-master.md` | omurga iddiaları, terimler | 🟡 master derinleştirilince tekrar |
| `services-dogrulama.md` | Q1–Q9, kanıt sınıfları | ✅ senkron (§13 haritası) |
| `services-riskler.md` | 7 risk kaydı | 🟡 riskler dosyası yazılınca |
| `services-ornekler.md` | bağımlılık örnekleri | 🟡 dosya henüz yok |
| `.ai/architecture/index.md` | K012 kayıt satırı | salt-okunur — bu görevde dokunulmaz |

Senkron kuralı: bir dosyada bağımlılık satırı değişirse §5 onay zinciri +
`services-dogrulama.md` kanıt kaydı birlikte güncellenir; tek başına değişiklik yasak.

## §22 Tur-2 Kapanış Kontrol Listesi (bu dosya)

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | FM §4.2 9 alan + group var mı | evet (parent_id, children, depends_on, evidence, validation, open_questions) |
| 2 | group quoted mı | hayır — `group: mimari` anahtar kelime |
| 3 | `parent:` anahtarı var mı | hayır (yalnız `parent_id:`) |
| 4 | TASARIM İDDİASI etiketi korundu mu | evet (başlık + §0 + §5) |
| 5 | imla hataları korundu mu | evet (K010-identitiy, K013-integraion) |
| 6 | dolu olmayan hücre var mı | hayır — bilinmeyenler `⚠️ UNKNOWN` yazılı |
| 7 | mojibake var mı | hayır (2026-10-13 sayımı 0) |
| 8 | son satır authority mi | evet (§23) |
| 9 | satır sayısı ≥500 | evet |

## §23 Authority

Bağımlılık otoritesi Vault Steward'dadır; her satır etiketiyle yaşar:
`TASARIM İDDİASI` onaysız bağlayıcı değildir, `✅ verified` kanıtsız yazılmaz.

## §24 Tur-3 Bekleyen İşler

Tur-3, `services-dogrulama.md` §19 kapı listesiyle birlikte yürütülür; her iş
bir açık soruya (Q1–Q6) bağlanmıştır ve tek başına kapatılamaz.

| # | İş | Tetikleyici | Durum |
|---|---|---|---|
| 1 | Q1 ai-service K006/K016 kararı | üst karar | ⏳ bekliyor |
| 2 | Q2 playback sınır ADR'si | K015 derinleştirme | ⏳ bekliyor |
| 3 | Q3 audio-service sınır kararı | K003 okuması | ⏳ bekliyor |
| 4 | Q4 service-contracts kapsam kararı | sözleşme dosyası | ⏳ bekliyor |
| 5 | Q5 background-jobs motoru araştırması | repo taraması | ⏳ bekliyor |
| 6 | Q6 device-identity sahiplik kararı | güvenlik denetimi | ⏳ bekliyor |
| 7 | §17 matrisindeki `?` hücrelerin kanıtlanması | kapı denetimi (§18) | ⏳ bekliyor |
| 8 | K014/K015 ters ok araması (çift yön) | o köklerin derinleştirilmesi | ⏳ bekliyor |

Bu sekiz iş kapanmadan dosya `status: draft` kalır; kapanışta sürüm 2.0.0 → 2.1.0
yapılır ve `services-dogrulama.md` §17'ye tur-3 satırı eklenir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
