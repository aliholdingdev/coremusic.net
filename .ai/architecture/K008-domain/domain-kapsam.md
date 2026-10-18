---
node_id: K008-domain/kapsam
node_type: root
status: verify-required
parent_id: K008-domain
children:
  - music
  - media
  - user
  - library
  - playlist
  - playback
  - discovery
  - recommendation
  - device-management
  - context-mapping
depends_on:
  - K008-domain/domain-master.md
evidence:
  - shared/config/domain.php
  - .ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md
validation: 2026-10-12 — 7 subdomain config satırı (domain.php s.7–15) + v2 spec §2.8 hizası
open_questions:
  - "10 çocuk içinde 3'ü için config satırı kanıtı yok (discovery, recommendation, context-mapping) — VERIFICATION REQUIRED"
group: mimari
---

# K008-domain — Kapsam (Scope Boundary)

> **Authority: Bayram Ali / Vault Steward.** Bu dosya K008-domain'in **nei kapsadığını**
> tanım olarak açar: Bounded Context envanteri, her çocuğun sorumluluk alanı ve
> dış sınırlar. Karar/kanıt için `domain-kararlar.md` / `domain-dogrulama.md`.
> **Kaynaklar:** v2 spec §2.8 · ADR-097 §4–§5 · `shared/config/domain.php`.

## §1 KAPSAM TANIMI

| Madde | Değer |
|---|---|
| Tanım | CoreMusic domain gerçeğinin (alan modeli) tek kayıt kaynağı |
| Birim | Bounded Context (10 çocuk) + kök anayasa (12 MD) |
| Kapsam girdisi | Müzik kataloğu, medya varlıkları, kullanıcı, kütüphane, çalma listesi, oynatma, keşif, öneri, cihaz, bağlam haritası |
| Kapsam çıktısı | Sınır, aggregate, dil, event sözleşmesi, kod kanıtı, risk, test stratejisi |
| Kapsam dışı | Persistence şeması, endpoint, UI, deploy (bkz. master §2.2) |
| Dönem | 2026-10-12 — ADR-097 K-space v3 sıfırdan kurulum |

## §2 KAPSAM DIŞI SINIR (NEGATİF TANIM)

Sınırlar, içeriden neyin **girmediğinin** tanımlanmasıyla korunur:

| # | K008'e GİRMEZ | Gerekçe |
|---|---|---|
| N1 | MySQL tablo/DDL detayı | Şema data katmanına ait (`.ai/.sql/mysql/` sahipliğinde) |
| N2 | HTTP route, status code, pagination | API katmanı sahipliğinde |
| N3 | Şifreleme/Argon2id parametresi | security katmanı sahipliğinde (domain yalnız "kimlik doğrulama gerekli" der) |
| N4 | CSS token, mockup, breakpoint | ui-design sahipliğinde |
| N5 | Paket versiyonu, composer kararı | K016 |
| N6 | Servis topolojisi, saga | K006 |
| N7 | Background job kuyruğu seçimi | iş katmanı (kod dosyalarında yalnız gereksinim) |

**İhlal durumu:** K008 dosyasında N1–N7 konusunda **karar** görürseniz bu bir
hata kaydıdır → `domain-riskler.md` R-satırına işlenir ve orkestratöre raporlanır.

## §3 BOUNDED CONTEXT ENVANTERİ

### §3.1 Genel envanter

| # | BC | Çocuk | Tek cümlelik sorumluluk | Config kanıtı |
|---|---|---|---|---|
| 1 | Müzik Kataloğu | `music` | Müzik eseri, kayıt, sanatçı, albüm, radyo, podcast varlığının yaşam döngüsü | `domain.php` (7 subdomain satırı) |
| 2 | Medya Varlıkları | `media` | Ses/video dosya varlığı, transcode çıktısı, metadata okuma | Config kanıtı mevcut |
| 3 | Kimlik & Profil | `user` | Kayıt, oturum, profil, tercih, cinsiyet/yaş gibi profil nitelikleri | Config kanıtı mevcut |
| 4 | Kütüphane | `library` | Kullanıcıya ait favoriler, dinleme geçmişi, oynatma sırası | Config kanıtı mevcut |
| 5 | Çalma Listesi | `playlist` | Kullanıcı koleksiyonları, iş birliği (collaborator) ilişkileri | Config kanıtı mevcut |
| 6 | Oynatma | `playback` | Çalma oturumu, sıra, kuyruk, oynatma olaylarının yayını | Config kanıtı mevcut |
| 7 | Keşif | `discovery` | Trend/radar/keşif sorgularının domain tarafı | `⚠️ VERIFICATION REQUIRED` |
| 8 | Öneri | `recommendation` | Öneri kümesi, benzerlik, çeşitlilik kurallarının domain gereksinimi | `⚠️ VERIFICATION REQUIRED` |
| 9 | Cihaz Yönetimi | `device-management` | Çalma hedefi cihazların domain modeli | `⚠️ VERIFICATION REQUIRED` |
| 10 | Bağlam Haritası | `context-mapping` | BC'ler arası ilişki kaydı (upstream/downstream) | `⚠️ VERIFICATION REQUIRED` |

> **Kanıt notu:** `shared/config/domain.php` içinde 7 subdomain kaydı sayıldı
> (satır 7–15 aralığı, 2026-10-12 keşfi). 10 çocuktan 3'ünün config karşılığı
> aranıp `domain-dogrulama.md` §3'te satır kanıtıyla kapatılacak.

### §3.2 Çocuk başına kapsam cümleleri (kapsam.md — kısa tutulur, ayrıntı çocukta)

| Çocuk | K008'de kapsanan | K008'de KAPSANMAZ |
|---|---|---|
| `music` | Eser (work), kayıt (recording), sanatçı, albüm, radyo istasyonu, podcast kavram sınırları | Tag okuma kütüphanesi seçimi, dosya formatı çözümleyici kodu |
| `media` | Medya dosya varlığı, transcode çıktısı olarak varlık, metadata kavramı | FFmpeg/kütüphane seçimi, disk yolu politikası |
| `user` | Kullanıcı, profil, tercih, oturum kavramı | Şifreleme parametresi, JWT/OIDC akış detayı (security katmanı) |
| `library` | Favori, geçmiş, sıra (queue) varlıkları ve sahipliği | Sorgu performansı, index (data katmanı) |
| `playlist` | Çalma listesi, izleyici/katılımcı ilişkisi, sıra | Paylaşım URL/endpoint tasarımı |
| `playback` | Oynatma oturumu, gapless/kuyruk kurallarının domain dili | Web Audio/player UI davranışı |
| `discovery` | Keşif sorgusu domain gereksinimi | Trend algoritması uygulaması (K016) |
| `recommendation` | Öneri kuralları domain gereksinimi (MIN_SCORE benzeri eşiklerin sahipliği) | `AIEngine` uygulaması (kod sahibi farklı) |
| `device-management` | Cihaz varlığı ve "oynatma hedefi" kavramı | Ağ keşfi protokolü, sürücü |
| `context-mapping` | BC ilişkileri, ilişki türleri | CI/CD, repo yapılandırması |

## §4 SAHİPLİK VE ERİŞİM MATRİSİ

| Varlık | Sahip (yazma) | Okuyucu | Not |
|---|---|---|---|
| K008 kök MD (12) | K008-domain agent'ı | tüm çocuklar | master en üst otorite |
| Çocuk MD (6×6) | çocuk agent'ı | kök | kök-çocuk çelişkisinde kök kazanır |
| Domain event sınıfı | kod sahibi (shared/src) | K008 (yalnız kanıt okur) | K008 PHP yazmaz |
| Ubiquitous dil terimi | `domain-evrensel-dil.md` | tüm K008 dosyaları | terim çelişkisinde sözlük kazanır |
| CONFLICT kaydı | master §6 | herkes | kapatma yalnız orkestratör |

## §5 ÖN KOŞULLAR (bu kapsam geçerli sayılırken)

| # | Ön koşul | Durum |
|---|---|---|
| P1 | ADR-097 K-space v3 yürürlükte | Var (draft) — kesinlik `⚠️ VERIFY REQUIRED` (C3) |
| P2 | v2 spec §2.8 çocuk listesi güncel | 2026-10-12 okundu |
| P3 | Repo erişimi (kanıt için) | Var — `shared/`, `.ai/.sql/mysql/` okunabilir |
| P4 | Web erişimi (araştırma için) | Var — 2026-10-12'de ~80 URL doğrulandı |

## §6 BAŞARI KRİTERLERİ (kapsam tamamlanmış sayılırken)

| # | Kriter |
|---|---|
| K1 | 10 çocuk için sınır cümlesi ve aggregate adı yazılı |
| K2 | N1–N7 dışı hiçbir konu K008'de karar olarak açılmamış |
| K3 | Config kanıtı olmayan çocuklar açıkça `VERIFICATION REQUIRED` işaretli |
| K4 | Her çocuk kendi `-kapsam.md` dosyasında ayrıntıya inmiş (dalga 1–2) |

## §7 DIŞ BAĞLANTILAR

| Hedef | Dosya |
|---|---|
| Anayasa, CONFLICT, uyum maddeleri | `domain-master.md` |
| Bağımlılık ekseninin ayrıntısı | `domain-bagimliliklar.md` |
| Sözlük | `domain-evrensel-dil.md` |
| Kapsam iddialarının kanıtı | `domain-dogrulama.md` |

---

---
## §8 DDD KAVRAM ENVANTERİ (bu kapsamın bağladığı kavramlar — tanım + sahiplik)

Bu bölüm K008'in **kavram dilini** kilitler. Her kavramın tek bir tanımı vardır;
kavramın sahibi olmayan dosyada **tanım tekrarı yazılmaz**, yalnızca sahibine
bağlanır. Tanımlar master'ın kural bloğuna (§23–§29) bağlıdır; burada yalnız
**kapsam** (neyi kapsadığı / neyi kapsamadığı) yazılır.

### §8.1 Kavram tanım tablosu

| Kavram | Kapsam içindeki tanım | K008 sahibi | Ne değildir |
|---|---|---|---|
| Bounded Context | Bir dilin tutarlı biçimde yaşayabildiği model sınırı | bu dosya §3 | servis, deploy birimi, klasör |
| Aggregate | Tutarlılık sınırı içindeki nesne kümesi ve onun kökü | master §24 | tablo, sorgu, nesne grafiği |
| Aggregate Root | Aggregate'e girişin tek kapısı | master §24 | repository, controller |
| Entity | Kimliği olan ve zamanla değişen varlık | master §25 | değer taşıyan nesne |
| Value Object | Kimliksiz, değişmez, eşitliği değerdedir | master §25 | yapısal olarak değişen kayıt |
| Domain Event | Gerçekleşen iş olayının adlandırılmış kaydı | master §26 | log satırı, komut, webhook |
| Repository | Aggregate köküne erişim sözleşmesi | master §28 | DAO, query builder, ORM modeli |
| Specification | Tekrarlanabilir iş kuralı nesnesi | master §27 | filtre DTO'su, WHERE bloğu |
| Domain Service | Birden fazla aggregate'i kapsayan iş akışı | bu dosya §9 | utility sınıfı, iş katmanı |
| Domain Exception | Invariant ihlalinin dili | master §24.3 | HTTP hata, validasyon mesajı |
| Ubiquitous Language | Kodla aynı adları kullanan ortak dil | `domain-evrensel-dil.md` | teknik sözlük, README |
| Context Map | BC'ler arası ilişki kaydı | `context-mapping` çocuk | mimari diyagram dosyası |

### §8.2 Kavram başına kapsam cümleleri (kapsanır / kapsamaz)

**Bounded Context.** Kapsanır: 10 çocuğun model sınırı, komşu ile ilişki türü,
çeviri ihtiyacı. Kapsanmaz: hangi framework'ün servis başına oturduğu, hangi
portun açıldığı (master §2.2). Sahibi bu dosyadır; başka dosyada tanım **yazılmaz**.

**Aggregate.** Kapsanır: sınırın nerede bittiği, root'un kim olduğu, iç nesnelerin
yaşam döngüsüne bağlılığı. Kapsanmaz: şema, join, index (N1). Sahibi master §24.

**Value Object.** Kapsanır: hangi alanların VO olarak kurulacağı, değişmezlik ve
eşitlik dili. Kapsanmaz: kolon tipi, uzunluk sınırı (N1). Sahibi master §25.

**Domain Event.** Kapsanır: adlandırma, taşıdığı iş değeri, tek BC içinde kalma,
kimlik ve zaman alanı. Kapsanmaz: yayım altyapısı, kuyruk, retry (N6). Sahibi
master §26.

**Repository.** Kapsanır: arayüzün ne sunduğu (kök by ID, koleksiyon, yazma),
neyi bilmediği (framework, transaction). Kapsanmaz: implementasyon, ORM, SQL (N1).
Sahibi master §28.

**Specification.** Kapsanır: hangi iş sorusunun kurala dönüştüğü, birleşim dili,
çevrilebilirlik gereği. Kapsanmaz: sorgu performansı, index (N1). Sahibi master §27.

### §8.3 Kavram ve çocuk eşleşmesi (hangi çocuk hangi kavramı açar)

| Kavram | Zorunlu olduğu çocuklar | Beklemede olanlar | Not |
|---|---|---|---|
| Bounded Context | hepsi (10) | — | §3.1 envanteri |
| Aggregate | music · media · user · library · playlist · playback | discovery · recommendation · device-management · context-mapping (`⚠️ VERIFICATION REQUIRED`) | root adı çocuk dosyada |
| Value Object | playlist (görünürlük/tür/sıra) · music (eser/kayıt) | diğerleri | kanıt: repo SQL enum satırları |
| Domain Event | music · library · user · media · playlist | device-management · context-mapping | envanter: master §7 |
| Repository | user (arayüz kanıtı var) | diğer 9 çocuk | kanıt: master §28 sınır notu |
| Specification | playlist · discovery | diğerleri | desen kuralı master §27 |

## §9 BC SINIR VE KOMŞU KURALLARI (context map dili)

| # | Kural | Gerekçe |
|---|---|---|
| B-1 | Her BC'nin **tek** birinci komşusu vardır; ikinci komşu yalnız ilişki tablosunda görünür (master §23) | okunabilirlik |
| B-2 | İki BC arasında **paylaşılan tablo yoktur**; veri yalnız event ya da açık sorgu ile akar | sınır saflığı |
| B-3 | Komşunun **şeması kopyalanmaz**; gerekiyorsa çeviri (ant-corruption) katmanı kurulur | dil bozulmasın |
| B-4 | BC'ler arası **senkron çağrı** domen dilinde yazılmaz; senkron çağrı yalnızca altyapı kararıdır | C3 + N6 |
| B-5 | İlişki yönü **tek yönlüdür**: downstream, upstream'un dilini bilir; tersi değil | bağımlılık yönü |
| B-6 | BC adı, dosya adı ve klasör adı **aynı sözcüğü** kullanır | ortak dil (§8.1) |
| B-7 | Bir BC'nin **sınıf değişikliği** (çekirdek ↔ destekleyici) bir karardır: master §23 + §17 protokolü | kayıt disiplini |

**Sınır ihlali belirtisi:** bir çocuk dosyasında başka bir çocuğun aggregate'ine
doğrudan erişim isteği varsa bu **B-2/B-5 ihlalidir** → `domain-riskler.md` R-satırı
açılır, orkestratöre raporlanır.

## §10 KAPSAM KARAR MATRİSİ (çocuk × kavram — anlık durum)

| Çocuk | Aggregate | Value Object | Domain Event | Repository | Specification | Durum dili |
|---|---|---|---|---|---|---|
| `music` | aday yazıldı (çocuk dosyada) | aday yazıldı | envanterde VAR | kanıt YOK | aday yazıldı | teklif — kanıt sınırı korunur |
| `media` | aday yazıldı | aday yazıldı | envanterde VAR | kanıt YOK | kanıt YOK | teklif |
| `user` | aday yazıldı | aday yazıldı | envanterde VAR | **kanıt VAR** (arayüz) | kanıt YOK | yarı kanıtlanmış |
| `library` | aday yazıldı | aday yazıldı | envanterde VAR | kanıt YOK | kanıt YOK | teklif |
| `playlist` | aday yazıldı | **kanıt VAR** (SQL enum satırları) | envanterde VAR | kanıt YOK | aday yazıldı | yarı kanıtlanmış |
| `playback` | aday yazıldı | aday yazıldı | kuyruk olayı **YOK** (master §7) | kanıt YOK | kanıt YOK | açık — event eksiği |
| `discovery` | kanıt YOK | kanıt YOK | olay akışı girdisi | kanıt YOK | aday yazıldı | beklemede (`⚠️`) |
| `recommendation` | kanıt YOK | kanıt YOK | integration çıktısı | kanıt YOK | kanıt YOK | beklemede (`⚠️`) |
| `device-management` | kanıt YOK | kanıt YOK | **YOK** (master §3.4) | kanıt YOK | kanıt YOK | beklemede (`⚠️`) |
| `context-mapping` | kanıt YOK | kanıt YOK | **YOK** (master §3.4) | kanıt YOK | kanıt YOK | beklemede (`⚠️`) |

**Okuma kuralı:** "kanıt VAR" yalnızca depoda gözlemlenen şeydir; "aday" = bu
dosyada/sahibinde yazılmış **öneridir**. İki sütun karıştırılamaz.

## §11 N1–N7'NİN KAPSAM KARŞILIKLARI (negatif tanımın karşılığı)

| # | Kural | Neyi kapattı | Sahibi | K008'de ne görünür |
|---|---|---|---|---|
| N1 | DDL/şema yasağı | tablo, kolon, index, tip kararı | `.ai/.sql/mysql/` sahibi + data katmanı | yalnız **kavram** ve **invariant** |
| N2 | HTTP yasağı | route, status, pagination, header | API katmanı | yalnız **iş sorusu** |
| N3 | Güvenlik parametresi yasağı | Argon2id/JWT/OTP ayarı | security katmanı | yalnız **gereksinim cümlesi** |
| N4 | UI yasağı | token, mockup, breakpoint, layout | ui-design | yalnız **görünürlük dili** (ör. `public/private`) |
| N5 | Paket/yönetici yasağı | versiyon, composer, npm kararı | K016 | yalnız **davranış gereği** |
| N6 | Topoloji yasağı | servis yerleşimi, saga, kuyruk | K006 | yalnız **gerekçeli bağımlılık** |
| N7 | Kuyruk seçimi yasağı | worker, broker, retry stratejisi | iş katmanı | yalnız **idempotency gereği** (master §26 D-5) |

**Çapraz kural:** bir kavram ile bir teknoloji kararı aynı cümlede geçiyorsa cümle
ikiye bölünür — kavram K008'de kalır, karar sahibine döner.

## §12 DALGA VE KAPSAM GENİŞLEMESİ

| Dalga | Kapsamın durumu | Kapsama eklenen | Eklenmeyen |
|---|---|---|---|
| Dalga 1 (tamamlandı) | 6 çocuk yazıldı | sınır cümleleri, aggregate adayları, event envanteri | invariant ayrıntısı, specification |
| Dalga 2 (devam) | kanıt derinleştirme | SQL/kod satır kanıtları, VO envanteri, repository sözleşmesi | uygulama kodu, altyapı |
| Dalga 3 (beklemede) | 4 çocuk | discovery · recommendation · device-management · context-mapping | — |

**Genişleme kuralları:**

1. Kapsam **yalnız dalga planına göre** genişler (master §13); plansız genişleme §17 protokolü gerektirir.
2. Bir kavram yeni bir çocuğa taşınırsa **önce o çocuğun §3.2 kapsam cümlesi** güncellenir, sonra kavram işlenir.
3. Kapsam genişlerken N1–N7 **daralmaz**; yeni teknoloji kararı talebi doğrudan sahibine döner.
4. Dalga 3 çocuklarında kanıt yokluğu **boşluk değil, durumdur**: `⚠️ VERIFICATION REQUIRED` yazılır, tahmin yazılmaz.

## §13 KAPSAM DEĞİŞİKLİK PROTOKOLÜ

| Adım | İşlem | Kayıt |
|---|---|---|
| 1 | Değişiklik türü belirlenir: ekleme · daraltma · kavram taşınması · sınır düzeltmesi | bu dosya §13 |
| 2 | İlgili N kuralı ve §9 komşu kuralı taranır — ihlal var mı? | varsa `domain-riskler.md` R-satırı |
| 3 | master §4/§20/§23 tablolarıyla çelişki kontrolü | master §17 protokolü |
| 4 | Sahip dosyalar bilgilendirilir (kavram sahibi, çocuk dosya) | ilgili çocuk `-kapsam.md` |
| 5 | Doğrulama defterine satır eklenir | `domain-dogrulama.md` |
| 6 | Authority satırı güncellenir (tarih + sayaç) | bu dosya son satırı |

**Duraklama:** açık bir conflict (master §6) varken kapsam **genişletilmez** —
önce conflict kapanır, sonra protokol çalışır.

**Geri alma:** kapsam değişikliği geri alınırsa §13/§5/§11/§12 tabloları da geri
alınır; yalnızca tek satır geri almak **çelişki üretir**.

## §14 KAPSAM KAPANIŞI (bu dosyanın sınırı + genişletilmiş bağlar)

| Düzlem | Bölüm | Ne verir |
|---|---|---|
| Tanım | §1–§2 | kapsamın ne olduğu / olmadığı |
| Envanter | §3–§4 | 10 BC + sahiplik matrisi |
| Ön koşul & başarı | §5–§6 | kapsamın geçerliliği |
| Bağlar | §7 | dış dosya yönlendirmesi |
| Kavram dili | §8 | 12 kavramın tek tanımı ve sahibi |
| Sınır kuralları | §9 | komşu ilişkileri, çeviri, paylaşım yasağı |
| Durum | §10 | çocuk × kavram matrisi (kanıt/öneri ayrımı) |
| Negatif karşılık | §11 | N1–N7'nin kavram karşılığı |
| Genişleme | §12 | dalga kuralları |
| Değişim | §13 | protokol + duraklama + geri alma |

**Kapanış cümlesi:** bu dosya K008'in **sınır ve dil** belgesidir; aggregate
kuralı master'da, kanıt doğrulama defterinde, kod örnekleri örnek dosyasındadır.
Bu üçüne **girmez**, onlara **bağlar**. Bu dosyada yazılan her tanım, §4.2 şemasını
(8 alan + group) taşıyan çocuk dosyalarda yeniden üretilmez.

---
*Authority: Bayram Ali / Vault Steward · K008-domain kapsam · 2026-10-12*
---
## §15 KAVRAM DERİNLİK NOTLARI (ayrıntı değil, ayrım gücü)

Bu bölüm bir kitap değil; her kavramda **en sık karıştırılan sınırı** yazar.
Amaç: çocuk dosyaların aynı hatayı ayrı ayrı yapmaması.

### §15.1 Entity mi Value Object mi — ayrım testi

| Soru | Evet ise | Hayır ise |
|---|---|---|
| Bu nesnenin kimliği var mı ve iki kopyası "aynı şey" mi? | Entity | Value Object |
| Zamanla **iç değeri** değişiyor mu? | Entity | Value Object |
| İki farklı anlam aynı ilkel tipi mi kullanıyor (tarih metni, süre sayısı)? | primitive obsession → VO kur | zaten VO olabilir |
| Nesne bir aggregate'in **kökü** mü? | Aggregate Root | alt nesne: Entity ya da VO |

**Sınır notu:** kimlik alanı var ama **iş dünyasında anlamı yoksa** (örn. teknik
audit kimliği) o alan Entity'yi **Entity yapmaz**; kimlik, işin tanıdığı kimliktir.

### §15.2 Aggregate ne zaman bölünür

Bir aggregate aşağıdaki durumlardan birinde **bölünmek zorundadır**:

1. Aynı transaction'da beklenen **tutarlılık** aslında başka bir sınırın kuralıysa.
2. Bir aggregate'in kilidi **çok sık** başka bir aggregate'i bekletiyorsa (erişim
   sıklığı çatışması).
3. Bir aggregate **sınırsız büyüyebiliyorsa** (liste içine liste eklenerek).
4. Bir aggregate'in **farklı yaşam döngüsü** olan parçaları varsa (silinen vs.
   kalan).

**Bölme dili:** bölme bir **karardır**; §13 protokolünden geçer, master §4/§20
tablolarına yansır. Bölünmemiş aggregate **hata değildir**; gerekçesiz bölme **ihlaldir**.

### §15.3 Domain event ne zaman domain, ne zaman integration

| Durum | Sınıf | Sahip |
|---|---|---|
| Olay **tek BC'nin** dilinde anlamlı | domain event | o BC (master §26) |
| Olay **iki BC'nin** dilinde anlamlı ve ikisi de aynı sürümü paylaşmalı | integration event | K013 |
| Olay yalnız **telemetri/logs** amaçlı | domain event **değildir** | iş katmanı |
| Olay bir **komut** taşıyor (yap, ekle) | event değil, komut | uygulama katmanı |

**Kanıt sınırı:** bu depoda domain event envanteri ile integration event envanteri
**ayrı ayrı** sayılmıştır (master §7); ikisini birleştiren her ifade yeniden
sayım gerektirir.

### §15.4 Repository mi sorgu servisi mi

| Soru | Evet ise |
|---|---|
| Sonuç bir **aggregate kökü** mü? | Repository |
| Sonuç bir **ölçüm/rapor/liste** mi ve aggregate tutarlılığını ilgilendirmiyor mu? | Sorgu servisi |
| Sorgu **sayfa/filtre/sıralama** içeriyor mu? | Sorgu servisi (Repository şişirilmez — master R-7) |
| Sorgu **aggregate invariant'ını** doğruluyor mu? | Repository (kayıt, R-2) |

## §16 SINIR HİKAYELERİ (kavramların sınırda nasıl davrandığı — 5 kısa senaryo)

| # | Senaryo | K008 sınırı | Sahip |
|---|---|---|---|
| H1 | Bir parça iki farklı çalma listesinde | **Sorun değil** — aggregate'ler ayrı; çakışma yalnız aynı listede (sıra) | `playlist` |
| H2 | Bir kullanıcı hem çalma listesi sahibi hem işbirlikçisi | Rol ayrımı bir **iş kuralıdır**; iki kayıt (sahiplik + katılımcı) farklı varlıklardır | `playlist` |
| H3 | Aynı eserin iki kaydı (stüdyo + canlı) | İkisi de **aynı eser**in kaydıdır; eser ile kayıt ayrı varlıklardır | `music` |
| H4 | Bir cihaz çalma sırasında ağdan düştü | Cihaz durumu **device** dilinde; oynatma durumu **playback** dilinde — olayla geçiş | `device-management` + `playback` |
| H5 | Bir kullanıcı hesabı silindi | Kimlik silinir; **kütüphane/çalma listesi** ne olur — bu bir **sınır kararıdır**, tek BC'nin inisiyatifinde değildir | `user` + sahipler |

**H5 özel notu:** H5, K008'in **en riskli** sınırıdır: üç ayrı BC'nin verisi aynı
kimliğe bağlıdır. Kural olarak: kimlik **olay** yayınlar, sahipler **kendi**
verilerini kendi invariant'larına göre temizler (master §26 D-4). Senkron temizlik
**yazılmaz** — bu, N6 topoloji yasağına girer.

## §17 KAPSAM DIŞINDAN GELEN TALEPLERİN SINIFLANDIRILMASI

| Talep türü | Örnek | Yönlendirme | K008'de kalır mı? |
|---|---|---|---|
| Kavram sorusu | "Eser ile kayıt arasındaki fark nedir?" | `music` çocuk dosyası | evet |
| Şema sorusu | "Bu kolon hangi tabloda?" | data katmanı (N1) | hayır |
| Davranış sorusu | "Kullanıcı ne zaman listeyi silebilir?" | `playlist` + master §24 | evet (kural) |
| Performans sorusu | "Bu sorgu hızlı olur mu?" | data/performance katmanı (N1) | hayır |
| Uygulama sorusu | "PHP'de bu nasıl yazılır?" | `domain-kod-ornekleri.md` §7–§8 | yalnız kanıt/örnek |
| Altyapı sorusu | "Olaylar kuyruğa mı gitsin?" | K006/K013 (N6/N7) | yalnız gereksinim |
| UI sorusu | "Bu alan formda nasıl görünür?" | ui-design (N4) | hayır |

**Kural:** talep sahibine dönerken **tek cümlelik gerekçe** yazılır; gerekçesiz
red, kayıt disiplini ihlalidir.

## §18 KAPSAM İÇİ ÇELİŞKİ TESPİTİ

| Belirti | Ne anlama gelir | Ne yapılır |
|---|---|---|
| İki çocuk aynı kavramı **farklı** tanımlıyor | dil bozulması (master §10) | sözlük kazanır; çocuklar sözlüğe bağlanır |
| Bir çocuk, başka bir çocuğun aggregate'ine erişmek istiyor | B-2/B-5 ihlali | `domain-riskler.md` R-satırı + orkestratör |
| Bir kavram, sahibi olmayan dosyada **tanımlanmış** | sahiplik ihlali | tanım silinip sahibine bağlanır (bu dosya §8.1) |
| Bir invariant, master §24.3-I5'teki "kapanık liste"yi genişletiyor | I-5 ihlali | master §17 protokolü |
| Bir dalga, §12 planına göre ilerlememiş | plan ihlali | master §13 gözden geçirme |

**Tespit dili:** her çelişki **iki satırla** yazılır: (1) ne çelişiyor, (2) hangi
kurala göre hangi taraf kazanır. Üçüncü satır **yorum** olamaz.

## §19 KAPSAM ÖLÇÜM VE KAPANIŞ

| Ölçüm | Değer (2026-10-17) |
|---|---|
| Bounded Context sayısı | 10 (§3.1) |
| Kavram envanteri | 12 (§8.1) |
| Kapsam dışı kural | 7 (N1–N7) + 7 sınır kuralı (§9) |
| Çocuk × kavram matrisi | 10 satır (§10) |
| Örnek senaryo | 5 (§16) |
| Talep yönlendirme sınıfı | 7 (§17) |
| Çelişki belirtisi | 5 (§18) |

**Kapanış:** kapsam dosyası **tamamlanmış** sayılmaz; §6 K1–K4 kriterleri
açık kaldığı sürece "devam ediyor" dili kullanılır. Kapanış yalnızca o kriterler
kapatıldığında ve §13 protokolünden geçildiğinde yazılır.

---
---
## §20 KAPSAM VE DİL MATRİSİ (her çocuk için sözcük seti — çekirdek terim)

Terimlerin **tam** listesi `domain-evrensel-dil.md`dedir; burada yalnız kapsamın
**çekirdek** terimleri ve kapsam dışı terimler gösterilir. Amaç: bir çocuk
dosyasında başka bir çocuğun dilinin sızmasını engellemek.

| Çocuk | Çekirdek terim | Çekirdek DIŞI terim (sınır) | Sahibi olmayan terim (yazma) |
|---|---|---|---|
| `music` | eser · kayıt · sanatçı · albüm · radyo · podcast | ses dosyası yolu, bit hızı ayarı | transcode, cihaz |
| `media` | medya varlığı · çıktı · metadata | eser adı, sanatçı adı | playlist, oturum |
| `user` | kullanıcı · profil · tercih · oturum | favori listesi, çalma sırası | medya dosyası, cihaz |
| `library` | favori · geçmiş · sıra | koleksiyon başlığı, işbirliği | eser, kayıt |
| `playlist` | çalma listesi · katılımcı · izleyici · sıra | eser kimliği tanımı, kimlik doğrulama | kullanıcı profili, cihaz |
| `playback` | oynatma oturumu · kuyruk · hedef | cihaz keşfi, liste oluşturma | cihaz niteliği, öneri skoru |
| `discovery` | keşif sorgusu · radar · trend | öneri skoru, liste | öneri seti, oturum |
| `recommendation` | öneri seti · skor · çeşitlilik | keşif sorgusu metni | oynatma oturumu, profil |
| `device-management` | cihaz · hedef · durum | oynatma kuyruğu, sıra | eser, liste |
| `context-mapping` | ilişki · yön · çeviri | herhangi bir iş terimi | — |

**Kural:** "Yazma" sütunundaki terim bir çocuk dosyasında **kullanılamaz**; gerekiyorsa
terim önce sahibinin diline çevrilir ve o çevirinin adı sözlüğe eklenir.

## §21 KAPSAM İÇİ TEST SORUSU KÜMESİ (her çocuk için 3 soru)

Bu sorular kapsamın **gözle test edilmesi** içindir: cevabı bir çocukta başlayıp
başka bir çocuğa gitmiyorsa sınır sağlıktır.

| Çocuk | Soru 1 (sınır) | Soru 2 (invariant) | Soru 3 (event) |
|---|---|---|---|
| `music` | Bir "eser" bilgisi başka bir çocuğun verisine mi bağlı? | Aynı kayıt iki kez tanımlanabilir mi? | Kayıt ne zaman oluşturuldu / değişti? |
| `media` | Medya varlığı eser olmadan var olabilir mi? | Aynı çıktı iki varlıkta iki kez kayıtlı mı? | Varlık ne zaman erişildi? |
| `user` | Kimlik doğrulama parametresi burada mı? | Aynı kullanıcı iki kez kayıtlı mı? | Kayıt/oturum olayları ne zaman yayılır? |
| `library` | Favori, koleksiyon başlığı taşıyor mu? | Aynı parça iki kez favori mi? | Ekleme/çıkarma olayları ne zaman yayılır? |
| `playlist` | Eser kimliği burada tanımlanıyor mu? | Aynı parça aynı listede iki kez sıralı mı? | Oluşturma/ekleme olayları ne zaman yayılır? |
| `playback` | Oynatma kuyruğu başka çocuğun varlığı mı? | Aynı oturum iki kez başlatılabilir mi? | Oynatma olayı hangi sınırda doğar? |
| `discovery` | Keşif sorgusu öneri skoru içeriyor mu? | Aynı sorgu iki kez kayıtlı mı? | Sorgu olayı ne zaman yayılır? |
| `recommendation` | Öneri seti keşif metni içeriyor mi? | Aynı öneri iki kez üretilebilir mi? | Öneri ne zaman üretildi? |
| `device-management` | Cihaz durumu oynatma durumuyla mı karışıyor? | Aynı cihaz iki kez kayıtlı mı? | Cihaz olayı ne zaman yayılır? |
| `context-mapping` | İlişki kaydı iş terimi içeriyor mu? | Aynı ilişki iki kez kayıtlı mı? | İlişki değişimi bir olay mı? |

**Kullanım:** sorular çocuk dosyaların `-dogrulama.md` dosyalarına **satır satır**
yanıtlanır; yanıt yoksa o satır `⚠️ VERIFICATION REQUIRED` kalır.

## §22 KAPSAM SON KONTROL LİSTESİ (bu dosyanın kendisi için)

| # | Kontrol | Beklenen |
|---|---|---|
| K-1 | §3.1 envanteri 10 BC mi? | Evet |
| K-2 | §8.1 kavram tanım tekil mi (bir kavram iki kez tanımlanmış mı)? | Tekil |
| K-3 | §9 kural seti ile master §23–§29 çelişiyor mu? | Çelişki yok |
| K-4 | §10 matrisinde "kanıt VAR" satırları doğrulama defterine bağlı mı? | Bağlı |
| K-5 | §11 N1–N7, §2 negatif tanımıyla aynı mı? | Aynı |
| K-6 | §16 senaryolarında toplu karar var mı? | Yok — her senaryo tek sınır kararı |
| K-7 | §17 talep yönlendirmesi tek cümlelik gerekçe taşıyor mu? | Taşıyor |
| K-8 | Authority satırı son satır mı? | Evet |

**Uygulama:** kontrol başarısızsa **kapsam dosyası düzeltilir**, master'a karar
taşınmaz; yalnız §13 protokolü gerektiren konular master'a açılır.

---
---
## §23 KAPSAMIN SAHİPLERE TAŞIDIĞI YÜKÜMLÜLÜKLER (handover)

Kapsam bir belge değil, **sözleşmedir**; aşağıdaki yükümlülükler bu dosyayı
okuyan her sahibin üzerindedir.

| # | Yükümlülük | Kim | Kanıt |
|---|---|---|---|
| W-1 | Yeni kavram **önce** §8.1'e eklenir, sonra çocuk dosyada kullanılır | kapsam sahibi | §8.1 revizyonu |
| W-2 | Bir kavramın tanımı **tek** yerde durur; diğerleri yalnızca bağlanır | tüm çocuk sahipleri | §8.3 |
| W-3 | Çocuk dosya, kendi sınırının **dışına** terim sokmaz | çocuk sahibi | §20 `Yazma` sütunu |
| W-4 | Invariant listesi **kapanıktır**; genişleme master §17 protokolünden geçer | çocuk + master | master §24.3 I-5 |
| W-5 | Kanıt yokluğu **durumdur**, boşluk değildir; `⚠️ VERIFICATION REQUIRED` yazılır | tüm sahipler | master §9 C6 |
| W-6 | Teknoloji kararı talebi sahibine **tek cümlelik gerekçeyle** döner | kapsam sahibi | §17 |
| W-7 | Çelişki iki satırla yazılır (ne çelişiyor + hangi kural kazanır) | kapsam sahibi | §18 |

**Yükümlülük ihlali durumu:** ihlal, ihlal dosyada **kayıt altına alınır**; kural
değişikliği §13 protokolü olmadan **yapılmaz**. Sahiplik, dosyayı değiştirmekle
değil, kuralı taşımakla korunur.

---
---
## §24 ÖLÇÜM NOTU (bu dosyanın 2026-10-17 turundaki durumu)

| Ölçüm | Değer |
|---|---|
| Bölüm sayısı | §1–§24 (önceki tur §1–§7) |
| Eklenen bölüm | §8–§24 (kavram envanteri, sınır kuralları, matris, protokol, dil, test soruları, kontrol listesi, handover, ölçüm) |
| Korunan bölüm | §1–§7 (değiştirilmedi, yalnız Authority satırı güncellendi) |
| Kapsam dışı kural | N1–N7 aynen korundu |
| Yeni URL / kod | 0 (C4 + §16 sınırı) |
| Yer tutucu | 0 (master §9 C5) |
| `⚠️ VERIFICATION REQUIRED` taşıyan satır | §3.1 (4 satır) + §10 (4 satır) — kapatılmadı, **işaretlendi** |

---
*Authority: Bayram Ali / Vault Steward · K008-domain kapsam · 2026-10-17 · §8–§14 kavram envanteri + sınır kuralları + çocuk matrisi · N1–N7 korunur*
