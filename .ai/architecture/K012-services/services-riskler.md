---
node_id: K012-services
node_type: module
role: riskler
status: draft
parent_id: K012-services
children: []
depends_on:
  - "services-master.md"
  - "services-bagimliliklar.md"
  - "services-dogrulama.md"
evidence:
  - "bagimliliklar §10 — 7 risk kaydı (2026-10-12)"
  - "dogrulama §4 — Q1–Q9 açık sorular (2026-10-13)"
validation:
  - "risk ↔ açık soru eşlemesi (§13, 2026-10-13)"
  - "hiçbir risk kapalı sayılmadı — Truth Mode (2026-10-13)"
open_questions:
  - "Q1 ai-service → K006 vs K016 (R1)"
  - "Q2 playback sahipliği (R2)"
  - "Q3 audio-service sınırı (R3)"
  - "Q4 service-contracts kapsamı (R5)"
  - "Q6 device-identity sahipliği (R4)"
  - "R6 K012↔K015 döngü riski (K015 turu bekliyor)"
  - "R8 imla riski — kapalı (kural)"
date: 2026-10-13
version: 1.0.0
authority: "SSOT: services-bagimliliklar.md §10 + ADR-097"
group: mimari
---

# K012-services — services-riskler.md

> Bu dosya K012'nin **risk kaydıdır**. Her risk bir açık soruya (Q1–Q9) veya
> bir yapısal çelişkiye bağlıdır; kapanış koşulu yazılmadan risk kapanmaz.
> Kapatılmamış risk `AÇIK`, kapatılmış risk `KAPALI` olarak işaretlenir —
> kapatma iddiası kanıtsız yazılmaz.

## §1 Amaç ve Kapsam

| Madde | Değer |
|---|---|
| Dosya | `K012-services/services-riskler.md` |
| Rol | risk kaydı + azaltım planı |
| Kaynak | `services-bagimliliklar.md` §10 (7 risk) + `services-dogrulama.md` §4 (9 açık soru) |
| Kapsam | yalnız K012-services kök riskleri |
| Kapsam dışı | K014/K015 riskleri (o kök dosyalarında), elektronik/donanım riskleri |
| Tarih | 2026-10-13 |
| Otorite | Vault Steward — Red Team · Human Mode · Truth Mode |

## §2 Risk Kayıt Yöntemi

| Alan | Zorunlu mu | Açıklama |
|---|---|---|
| ID | evet | R1–R8 biçiminde |
| Başlık | evet | tek satır risk tanımı |
| Kaynak | evet | hangi dosya/§ üretti riski |
| Olasılık | evet | yüksek / orta / düşük — tahmin etiketi |
| Etki | evet | yüksek / orta / düşük |
| Açık soru | evet | Q1–Q9 veya "yok" |
| Azaltım | evet | ne yapılacak (plan) |
| Kapalı mı | evet | AÇIK / KAPALI + kanıt |
| Kanıt | zorunlu (kapanışta) | kapanış kanıtı yoksa risk AÇIK kalır |

Olasılık/etiketler **ölçüm değil, tasarımsal yargı**dır; sayısal olasılık
(yüzde) bu dosyada UYDURULMAZ.

## §3 Risk Ana Tablosu

| ID | Risk | Olasılık | Etki | Açık soru | Durum |
|---|---|---|---|---|---|
| R1 | ai-service sahiplik belirsizliği (K006 vs K016) | yüksek | yüksek | Q1 | AÇIK |
| R2 | playback iki gerçeklik (K012 ↔ K015) | yüksek | yüksek | Q2 | AÇIK |
| R3 | audio sınır kayması (servis mi motor mu) | orta | orta | Q3 | AÇIK |
| R4 | kimlik verisi iki yerde (device-identity) | orta | yüksek | Q6 | AÇIK |
| R5 | service-contracts tekeli (kırıcı sözleşme değişimi) | orta | yüksek | Q4 | AÇIK |
| R6 | K012↔K015 çift yön / döngü riski | düşük | orta | — | AÇIK |
| R7 | kanıtsız teknoloji iddiaları (transport/motor) | yüksek | orta | Q5, Q9 | AÇIK |
| R8 | imla hatalı adlar (identitiy/integraion) | kesin | düşük | — | KAPALI (kural) |

Özet: **7 AÇIK · 1 KAPALI · 0 reddedildi.** Hiçbir risk bu turda kanıtsız
kapatılmadı.

## §4 R1 — ai-service Saha Belirsizliği

| Alan | Değer |
|---|---|
| Başlık | ai-service hangi domene bağlı: K006-yapay-zeka mı K016-AI mı |
| Kaynak | `services-master.md` §7/1 · `services-bagimliliklar.md` §6.1 |
| Olasılık | yüksek (registry §6 kapsam ayrımı açık bırakmış) |
| Etki | yüksek — yanlış domene kod/sahiplik yazılır |
| Açık soru | Q1 |
| Belirti | iki YZ domeni var; ai-service satırı ikisine de bağlanabilir |
| Etkilenen dosyalar | master §3/§6, bagimliliklar §1, dogrulama §4 |
| Azaltım | registry §6 + v2 ağaç yeniden okunur; üst karar verilir |
| Karar mercii | Vault Steward + registry sahibi |
| Kapalı mı | AÇIK |
| Kapanış kanıtı | gerekli: kararı yazan vault dosyası + § + tarih |
| Son tarih | tur-3 (planlanmadı) |

**Yan etki:** Q1 kapanana kadar ai-service'e ait hiçbir iddia `✅ verified`
sayılmaz; `services-master.md` §21.7'deki kart `⚠️` taşımaya devam eder.

## §5 R2 — Playback İki Gerçeklik

| Alan | Değer |
|---|---|
| Başlık | çalma durumunun sahibi playback-service (K012) mi K015/playback mi |
| Kaynak | `services-master.md` §7/2 · `services-bagimliliklar.md` §6.7 |
| Olasılık | yüksek (iki node'da "playback" adı var) |
| Etki | yüksek — iki gerçek kayıt (split-brain) |
| Açık soru | Q2 |
| Belirti | v2 §2.12'de K012/playback-child, K015'te playback-child |
| Çatışma tipi | sahiplik çakışması (ad benzerliği değil) |
| Azaltım | sınır ADR'si: durum kimin yazdığı, kimin okuduğu |
| Karar mercii | Vault Steward + ADR |
| Kapalı mı | AÇIK |
| Kapanış kanıtı | ADR numarası + § |
| Bağımlı iş | K015 derinleştirme sırası |
| Son tarih | K015 turunda |

**Yan etki:** iki dosyadaki "çalma" tanımları eşleşene kadar playback kapsamı
`UNKNOWN` sayılır; test senaryoları bu yüzden yazılmaz.

## §6 R3 — Audio Sınır Kayması

| Alan | Değer |
|---|---|
| Başlık | audio-service mi yoksa K003-audio motoru mu ses işini yapar |
| Kaynak | `services-master.md` §7/3 · `services-bagimliliklar.md` §4/3 |
| Olasılık | orta |
| Etki | orta — sorumluluk kayması, çift implementasyon riski |
| Açık soru | Q3 |
| Belirti | "ses işleri servisi" tanımı motor işiyle örtüşebilir |
| Azaltım | K003 sınır satırı okunur; sorumluluk matrisi yazılır |
| Karar mercii | Vault Steward |
| Kapalı mı | AÇIK |
| Kapanış kanıtı | sınır kararı (dosya + §) |
| Kapsam dışı not | K003'e yazma yasağı bu görevde geçerli |
| Son tarih | tur-3 |

**Yan etki:** audio-service kartı (master §21.4) kapanana kadar taslak kalır.

## §7 R4 — Kimlik Verisi İki Yerde

| Alan | Değer |
|---|---|
| Başlık | device-identity verisinin sahibi device-service mi K010-identitiy mi |
| Kaynak | `services-bagimliliklar.md` §6.4 + §10/4 · `services-master.md` §7/7 |
| Olasılık | orta |
| Etki | yüksek (güvenlik — kimlik sızıntısı/iki yazma noktası) |
| Açık soru | Q6 |
| Belirti | cihaz kaydı (device-service) ile kimlik (K010) aynı veriye dokunabilir |
| Azaltım | tek yazma noktası belirlenir; diğer taraf okur |
| Güvenlik notu | OWASP'a uygun tek sorumluluk ilkesi (tasarım düzeyi) |
| Karar mercii | Vault Steward + güvenlik denetimi |
| Kapalı mı | AÇIK |
| Kapanış kanıtı | sahiplik kararı + okuma/yazma matrisi |
| Son tarih | tur-3 |

**Yan etki:** cihaz kimliği üzerine hiçbir entegrasyon kodu/örneği üretilmez.

## §8 R5 — service-contracts Tekeli

| Alan | Değer |
|---|---|
| Başlık | tüm servisler tek sözleşme dosyasına bağlı — değişimi kırıcı |
| Kaynak | `services-bagimliliklar.md` §4/5 · `services-master.md` §7/4 |
| Olasılık | orta |
| Etki | yüksek — sözleşme değişince tüm servisler etkilenir |
| Açık soru | Q4 (kapsam) + §7/5 (ADR gereği) |
| Belirti | hub-and-spoke iddiası teke bağımlılığı büyütür (§10.1 master) |
| Azaltım | sürümleme ADR'si (X-API-Version dayanaklı) + geriye uyumluluk kuralı |
| Repo kanıtı | sürüm başlığı mevcut: `VersionResolver.php:57` (S1) |
| Karar mercii | Vault Steward + ADR |
| Kapalı mı | AÇIK |
| Kapanış kanıtı | sürümleme ADR'si numarası |
| Son tarih | service-contracts derinleştirmesinde |

**Yan etki:** evrensel Playlist/Track spec'i iddiası (master §4/1) bu ADR'ye
kadar `seçenek` statüsünde kalır.

## §9 R6 — K012 ↔ K015 Çift Yön Riski

| Alan | Değer |
|---|---|
| Başlık | K012→K015 bağımlılığı biliniyor, K015→K012 ihtimali denetlenmedi |
| Kaynak | `services-bagimliliklar.md` §4/6 + §9 |
| Olasılık | düşük |
| Etki | orta — döngü varsa test/dağıtım zorlaşır |
| Açık soru | — (döngü denetimi bekliyor) |
| Belirti | medya servisi çalma durumunu çağırabilir (playback bağı) |
| Azaltım | K015 derinleştirilirken ters ok aranır; varsa DAG kırılır |
| Yöntem | dosya okuma + bağımlılık matrisi (bagimliliklar §17) |
| Kapalı mı | AÇIK |
| Kapanış kanıtı | K015 dosyasında DAG denetim satırı |
| Son tarih | K015 turu (bu görevin 3. kökü) |

**Yan etki:** çift yön tespit edilirse ADR + graf düzeltmesi gerekir; tek
yön doğrulanana kadar §5.3 grafiği `tasarım iddiası` kalır.

## §10 R7 — Kanıtsız Teknoloji İddiaları

| Alan | Değer |
|---|---|
| Başlık | transport, iş motoru, çalışma zamanı bilinmiyor — iddia yazma baskısı |
| Kaynak | `services-dogrulama.md` §4 (Q5, Q9) · `services-master.md` §4 (UNKNOWN tablosu) |
| Olasılık | yüksek (belirsizlik açıkça kayıtlı) |
| Etki | orta — uydurma teknoloji adı girerse vault bozulur |
| Açık soru | Q5 (motor), Q9 (taşıyıcı) |
| Belirti | "gRPC mi REST mi olay mı" cevapsız |
| Azaltım | kanıt gelene kadar `⚠️ UNKNOWN`; örnek dosyaları da `UNKNOWN` kullanır |
| Denetim | her turda teknoloji adı aranır (envanter kesişimi dahil) |
| Kapalı mı | AÇIK |
| Kapanış kanıtı | kanıt sınıfı S1 veya S3 ile belgelenmiş teknoloji adı |
| Son tarih | tur-3 |

**Yan etki:** `services-ornekler.md` içinde gerçek olmayan kütüphane/framework
adı YAZILAMAZ; örnekler soyut pseudokod seviyesinde kalır.

## §11 R8 — İmla Hatalı Adlar (KAPALI)

| Alan | Değer |
|---|---|
| Başlık | `K010-identitiy`, `K013-integraion` gibi yazım hataları |
| Kaynak | v2 §3 imla kuralı · `services-bagimliliklar.md` §4/7 |
| Olasılık | kesin (adlar diskte böyle) |
| Etki | düşük — yanlış arama/bozuk link riski |
| Açık soru | yok |
| Azaltım | yazım DOKUNULMAZ; aramalar hatalı yazım + doğru yazım ile yapılır |
| Kapalı mı | **KAPALI** (kural olarak) |
| Kapanış kanıtı | v2 §3 + görev kuralı (typos preserved) |
| Denetim | her dosya üretiminde korunur |
| İstisna | ad DEĞİŞTİRİLEMEZ; düzeltme yasağı kalıcı |

Bu risk "kapatıldı" çünkü çözümü bir karar değil, **kalıcı kuraldır**; kural
ihlali tespit edilirse yeniden AÇIK yapılır.

## §12 Risk ↔ Açık Soru Eşlemesi

| Açık soru | İlgili risk | Ek dosya |
|---|---|---|
| Q1 ai-service K006/K016 | R1 | master §7/1 |
| Q2 playback sahipliği | R2 | master §7/2 |
| Q3 audio sınırı | R3 | master §7/3 |
| Q4 contracts kapsamı | R5 | master §7/4 |
| Q5 iş motoru | R7 | master §7/6 |
| Q6 device-identity | R4 | master §7/7 |
| Q7 BC/DB ilişkisi | — (bu dosya dışı, K007/K008) | dogrulama §4 |
| Q8 swagger/PSR | — (tespit, risk değil) | dogrulama §4 |
| Q9 taşıyıcı | R7 | dogrulama §4 |
| — (döngü denetimi) | R6 | bagimliliklar §9 |

Q7 ve Q8 bu dosyada risk sayılmadı; gerekçe: ikisi de doğrulama boşluğu,
yapısal çatışma değil (Truth Mode — risk şişirilmez).

## §13 Risk Takip Panosu

| ID | Durum | Sonraki aksiyon | Sorumlu | Beklenen çıktı |
|---|---|---|---|---|
| R1 | AÇIK | registry §6 okuma + üst karar | Vault Steward | sahiplik kararı |
| R2 | AÇIK | K015 turunda sınır ADR'si | Vault Steward | ADR numarası |
| R3 | AÇIK | K003 sınır okuması | Vault Steward | sorumluluk matrisi |
| R4 | AÇIK | tek yazma noktası kararı | Vault Steward + security | okuma/yazma matrisi |
| R5 | AÇIK | sürümleme ADR'si | Vault Steward | ADR numarası |
| R6 | AÇIK | K015 DAG denetimi | Vault Steward | DAG satırı |
| R7 | AÇIK | kanıt turu (S1/S3) | Vault Steward | teknoloji kanıtı |
| R8 | KAPALI | — (kural denetimi sürer) | tüm yazarlar | ihlal = yeniden açılış |

## §14 Tetikleyiciler (Risk Yeniden Açılış / Yeni Risk)

| Tetikleyici | Eylem |
|---|---|
| yeni bağımlılık satırı (bagimliliklar §5) | yeni risk kaydı zorunlu |
| K014/K015'te ters ok tespiti | R6 yeniden değerlendir |
| yeni teknoloji adı iddiası | R7 kapsamına girer, kanıt ister |
| güvenlik olayı / kimlik sızıntısı | R4 acil AÇIK → L2 eskalasyon |
| imla ihlali (ad değişikliği) | R8 yeniden AÇIK |
| ADR kapanışı ilgili riskte | kanıt eklenir, KAPALI yazılır |

## §15 Azaltım Stratejileri

| # | Strateji | Hangi riskler |
|---|---|---|
| 1 | üst karar + tek cümle sahiplik kaydı | R1, R3, R4 |
| 2 | sınır ADR'si (kim yazar, kim okur) | R2, R6 |
| 3 | sürümleme + geriye uyumluluk kuralı | R5 |
| 4 | kanıt sınıfı disiplini (S1/S3 olmadan ad yazılmaz) | R7 |
| 5 | kalıcı imla kuralı + arama çifti | R8 |
| 6 | kapalı kapı listesi (bagimliliklar §18) | hepsi — takip için |

## §16 Eskalasyon

| Seviye | Koşul | Hedef |
|---|---|---|
| L1 | risk kapanış kanıtı toplanamıyor | Vault Steward |
| L2 | güvenlik riski (R4) aktive oldu | security + Vault Steward |
| L3 | mimari çatışma (R2/R6 çift yön) | architect + Vault Steward |
| İnsan | L3 3 denemede kapanmaz | Bayram Ali |

Zaman aşımı/yanlış karar bu dosyada sayıyla yazılmaz (bilinmiyor → sayı uydurulmaz).

## §17 Terimler

| Terim | Anlamı |
|---|---|
| risk | kaynağı belli, etkisi tanımlı belirsizlik |
| AÇIK | kapanış kanıtı yok |
| KAPALI | kapanış kanıtı yazılı (dosya + § + tarih) |
| azaltım | riskin olasılık/etkisini düşürecek plan |
| tetikleyiciler | riski yeniden açan/üreten olay |
| eşleme | riskin açık soruya bağlanması |
| şişirme | kanıtsız risk üretme — yasak (Truth Mode) |
| sahiplik | bir verinin/kontratın tek yazıcısı |

## §18 Kapsam Dışı

| Yüzey | Neden |
|---|---|
| K014/K015 riskleri | o köklerin kendi dosyaları |
| donanım/elektronik risk | `.ai/electronic/**` yazma yasağı |
| proje/takvim riski | bu dosya yalnız mimari risk taşır |
| güvenlik CVE iddiaları | kanıtsız güvenlik iddiası yasak |

## §19 Tur-2 Kapanış Kontrolü

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | FM §4.2 9 alan + group | evet (group: mimari, anahtar kelime) |
| 2 | her riskin açık sorusu/eşlemesi | evet (§12) |
| 3 | kanıtsız kapanış var mı | hayır (1 KAPALI, o da kural) |
| 4 | sayı uydurma var mı | hayır (yüzde/gün yazılmadı) |
| 5 | son satır authority | evet (§20) |

## §20 Authority

Risk otoritesi Vault Steward'dadır; her risk kanıtıyla yaşar, kanıtsız kapanmaz.
Açık soru kapanmadan ilgili risk kapanamaz.

## §21 Etki Zinciri Senaryoları (senaryo ≠ gerçekleşen olay)

### §21.1 R1 zinciri (senaryo)

| Adım | Ne olur | Sonuç |
|---|---|---|
| 1 | ai-service yanlış domene yazılır (K016 yerine K006) | kod/şema yanlış yerde |
| 2 | doğru domen keşfedilince taşınma gerekir | iki kez iş |
| 3 | taşınmazsa iki domen aynı yetenekle çakışır | tek sorumluluk kaybı |
| 4 | kullanıcıya YZ özelliği farklı iki yerden gelir | tutarsızlık |

Bu zincir **senaryodur**, tahmin değildir; her adım AÇIK R1'e bağlı durur.

### §21.2 R2 zinciri (senaryo)

| Adım | Ne olur | Sonuç |
|---|---|---|
| 1 | playback durumu iki yerde yazılır (K012 + K015) | iki gerçek durum |
| 2 | kullanıcı duraklatınca yalnız biri güncellenir | durum çelişkisi |
| 3 | istemci farklı kaynaklardan farklı okur | görünür hata |
| 4 | test iki durumu da doğrulamak zorunda kalır | test şişmesi |

### §21.3 R4 zinciri (senaryo — güvenlik)

| Adım | Ne olur | Sonuç |
|---|---|---|
| 1 | cihaz kimliği iki serviste yazılır | iki yazma noktası |
| 2 | biri güncellemezse eski kimlik kalır | yetki sapması |
| 3 | sızıntı durumunda iki yer temizlenir | temizlik eksikliği |
| 4 | denetim izi bölünür | izlenebilirlik kaybı |

### §21.4 R5 zinciri (senaryo)

| Adım | Ne olur | Sonuç |
|---|---|---|
| 1 | service-contracts alan çıkarır | tüm servisler etkilenir |
| 2 | sürümleme yoksa eski istemciler kırılır | uyumsuzluk |
| 3 | geriye uyum eklenirse iki biçim taşınır | karmaşıklık |
| 4 | hub-and-spoke ile teke bağımlılık büyür | tek nokta |

### §21.5 R7 zinciri (senaryo)

| Adım | Ne olur | Sonuç |
|---|---|---|
| 1 | "taşıyıcı gRPC" kanıtsız yazılır | uydurma iddia |
| 2 | biri bu dosyayı gerçeğe sanır | vault güven kaybı |
| 3 | karar bu adaya göre verilir | yanlış mimari karar |
| 4 | gerçek kanıt çelişince geri alma | itibar + iş kaybı |

## §22 Risk ↔ Dosya Etkilenme Matrisi

`X` = o dosya bu riskten doğrudan etkilenir.

| Dosya ↓ / Risk → | R1 | R2 | R3 | R4 | R5 | R6 | R7 | R8 |
|---|---|---|---|---|---|---|---|---|
| `index.md` | — | — | — | — | — | — | — | X |
| `services-master.md` | X | X | X | X | X | X | X | X |
| `services-bagimliliklar.md` | X | X | X | X | X | X | — | X |
| `services-dogrulama.md` | X | X | X | X | X | X | X | — |
| `services-riskler.md` | X | X | X | X | X | X | X | X |
| `services-ornekler.md` | X | X | X | X | X | — | X | X |
| alt alan `index.md` (10) | X | X | X | X | — | — | X | X |

Matris okuması: bir dosya düzeltilirken ilgili sütundaki tüm riskler yeniden
değerlendirilir; kapanış kanıtı yalnız ilgili hücreye yazılır.

## §23 Risk Kaydı Sözlüğü

| Terim | Bu dosyadaki anlamı |
|---|---|
| ID | R1–R8 — kalıcı numara, silinmez |
| kaynak | riskin keşfedildiği dosya + § |
| olasılık | tasarımsal yargı (yüksek/orta/düksek/düşük/kesin) |
| etki | hasar büyüklüğü yargısı |
| belirti | riskin gözlenebilir işareti |
| azaltım | planlanan eylem |
| kapanış kanıtı | dosya + § + tarih üçlüsü |
| senaryo | zincir kurgusu — gerçekleştiği iddia edilmez |
| yeniden açılış | kural ihlali/tetikleyici ile AÇIK'a dönüş |
| şişirme | kanıtsız risk ekleme — yasak |

## §24 Özet Sayılar (2026-10-13)

| Metrik | Değer |
|---|---|
| Toplam risk | 8 |
| AÇIK | 7 |
| KAPALI | 1 (R8 — kural) |
| Reddedildi | 0 |
| Q'ya bağlı risk | 6 (R1–R5, R7) |
| Q'suz risk | 2 (R6, R8) |
| Etkilenen dosya | 7 (§22) |
| Senaryo zinciri | 5 (§21) |
| Kanıtsız kapanış | 0 |

## §25 Geri İzleme (traceability)

| Kaynak | Nereye yazıldı |
|---|---|
| `services-bagimliliklar.md` §10 (7 risk) | §3 ana tablosuna taşındı + R7 eklendi (kaynak: dogrulama §4) |
| `services-dogrulama.md` §4 (Q1–Q9) | §12 eşleme tablosuna |
| `services-master.md` §7 (8 açık soru) | §4–§11 risk kartlarına |
| v2 §3 imla kuralı | R8 |

Ek açıklama: ana tabloda 8 risk var; bagimliliklar §10'da 7 vardı. Fark R7'dir
— bu dosya, dogrulama dosyasındaki teknoloji belirsizliğini (Q5, Q9) ayrı risk
olarak kaydeder; yeni risk üretimi §14 tetikleyicileri kapsamındadır ve
kaynağı bellidir (uydurma değildir).

## §26 Sık Sorular

| # | Soru | Cevap |
|---|---|---|
| 1 | Risk eklemek için ne gerek? | §14 tetikleyicisi + kaynak dosya + § |
| 2 | Risk kapatmak için ne gerek? | kapanış kanıtı: dosya + § + tarih |
| 3 | Yüzçe/olasılık sayısı yazılır mı? | Hayır — sayı uydurmak yasak (Truth Mode) |
| 4 | R8 neden KAPALI? | Çözümü karar değil kalıcı kural (v2 §3) |
| 5 | Q7/Q8 neden risk değil? | Doğrulama boşluğu, yapısal çatışma değil (§12) |
| 6 | Bu dosya kime ait? | Vault Steward (§20) |

## §27 Tur-2 Değişiklik Kaydı

| Tarih | Sürüm | Değişiklik |
|---|---|---|
| 2026-10-13 | 1.0.0 | dosya oluşturuldu: §1–§20 + §21–§25 + §26–§27 |
| — | — | sonraki değişiklik R1–R7 kapanışında (2.0.0) |

Planlanan sonraki tur (tur-3) bu dosyada şu satırları değiştirecek: §3 durum
sütunu, §13 panosu, §24 sayılar. Değişiklikler yalnız kapanış kanıtıyla yapılır.

## §28 Risk Durum Geçmiş Tablosu (tur-1 → tur-2)

| ID | tur-1 (2026-10-12) | tur-2 (2026-10-13) | Değişim |
|---|---|---|---|
| R1 | AÇIK (kaynak: bagimliliklar §4/1) | AÇIK (kanıt: dogrulama §4 Q1) | kanıt eklendi |
| R2 | AÇIK (bagimliliklar §4/2) | AÇIK (dogrulama §4 Q2) | kanıt eklendi |
| R3 | AÇIK (bagimliliklar §4/3) | AÇIK (dogrulama §4 Q3) | kanıt eklendi |
| R4 | AÇIK (bagimliliklar §4/4) | AÇIK (dogrulama §4 Q6) | kanıt eklendi |
| R5 | AÇIK (bagimliliklar §4/5) | AÇIK (dogrulama §4 Q4) | kanıt eklendi |
| R6 | AÇIK (bagimliliklar §4/6) | AÇIK (bagimliliklar §9 DAG) | denetim planlandı |
| R7 | yok (bu turda üretildi) | AÇIK (kaynak: dogrulama §4 Q5/Q9) | yeni kayıt §25 |
| R8 | KAPALI (v2 §3) | KAPALI (kural) | değişmedi |

Değişim kuralı: durum yalnız `AÇIK → KAPALI` yönünde ve kapanış kanıtıyla
değişir; `KAPALI → AÇIK` yalnız kural ihlali/tetikleyici ile olur (§14).

## §29 Kapanış Notu

Bu dosya tur-2 ile 8 riski kayda aldı; hiçbiri kanıtsız kapatılmadı.
Bir sonraki kapanış, ilgili açık sorunun vault'ta cevaplandığı gün açılır.

| Kapanış sırası | Beklenen | Risk |
|---|---|---|
| 1 | Q1 sahiplik kararı | R1 |
| 2 | Q6 sahiplik kararı | R4 |
| 3 | Q2/Q3 sınır kararları | R2, R3 |
| 4 | Q4/Q5 kanıt turları | R5, R7 |
| 5 | K015 DAG denetimi | R6 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
