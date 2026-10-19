---
node_id: K005-firmware/firmware-riskler.md
node_type: subdomain
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K000-isletim-sistemi
  - K001-donanım
  - K003-audio
  - K004-dsp
evidence:
  - "firmware-master.md §8 (bu üretim) — risk özeti — yazıldı 2026-10-13"
  - ".ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md satır 140 (§5.5 — iki-ekseni gruplama: kategori MD × teknoloji) — okundu 2026-10-13"
  - "shared/src/Middleware/RateLimiterMiddleware.php — komşu güvenlik yüzeyi (sınır örneği) — okundu 2026-10-13"
  - "repo kök taraması — *.cpp / *.h / *.c = 0 dosya (firmware kodu yok → risk 6/11 dayanağı) — 2026-10-13"
validation:
  - "2026-10-13 — 12 risk × (olasılık × etki) = skor; kural: ≥15 kırmızı, 8–14 sarı, <8 yeşil"
  - "2026-10-13 — her kırmızı riskte azaltma sahibi var (6/6)"
  - "2026-10-13 — §9 ölçüt tabloları ile §2 puanları çapraz okundu (12/12 tutarlı)"
open_questions:
  - "Risk 10 (ID CONFLICT) çözüm onayı üst mercide bekliyor (⚠️)"
  - "Risk 6/11 (hedef seçimi · kanıt tazeliği) takvimi bilinmiyor (⚠️ UNKNOWN)"
group: kanit
---

# firmware-riskler — Risk Matrisi

> Dayanak: `firmware-master.md` §8 · ADR-097 §5.5 (kanit grubu · satır 140).
> Skor = Olasılık (1–5) × Etki (1–5). **≥15 kırmızı · 8–14 sarı · <8 yeşil.**
> Bu dosya K005'in **risk defteridir**: risk silinmez, kapanır; kapanan risk
> `kapandı` notuyla satırda kalır (§5 kural 3).

## §1 ELI15

> **Firmware'de en pahalı iki hata: cihaz açılmaz (boot/rollback) ve güvensiz
> açılır (imza). Onları kırmızıda tutan azaltma sahibi vardır — sahipsiz kırmızı yok.**

Riskler üç kaynaktan çıkar: **kapsam** (sınır kayması), **güvenlik** (imza/erişim),
**süreç** (seçim yokluğu · şema eksikliği · kanıt tazeliği).

Risk, "olsa kötü olur" cümlesi değildir. Buradaki her satır üç şeyi birlikte
taşıdır: (1) neyin bozulduğu, (2) ne kadar kötü olduğu (Etki), (3) ne sıklıkla
beklendiği (Olasılık). Üçünden biri eksikse satır risk değil, kaygıdır ve bu
deftere yazılmaz — kaygı §7 açık sorulara yazılır.

## §2 Risk matrisi (12 risk)

| # | Risk | Olasılık | Etki | Skor | Seviye | Sahip |
|---|---|---|---|---|---|---|
| 1 | Kapsam kayması: K004-dsp işi firmware'e taşınırsa sınır bozulur | 4 | 4 | 16 | 🔴 | Vault Steward |
| 2 | Secure boot zinciri kırılırsa cihaz güvensiz açılır | 3 | 5 | 15 | 🔴 | security (dalga-3) |
| 3 | OTA geri alma (rollback) çalışmazsa cihaz tuğla (brick) olur | 3 | 5 | 15 | 🔴 | update |
| 4 | Kanıtsız iddialar `[KANIT]` gibi kullanılırsa yanlışlık yayılır | 4 | 4 | 16 | 🔴 | bu ajan (D-gate) |
| 5 | Kapsam kayması: K001 register/API'si firmware'de tekrar tanımlanır | 3 | 4 | 12 | 🟡 | Vault Steward |
| 6 | Hedef platform/dil seçilmedi → tüm iddialar genel kalır, doğrulanamaz | 5 | 3 | 15 | 🔴 | embedded |
| 7 | Watchdog beslenmezse plansız reset döngüsü oluşur | 3 | 4 | 12 | 🟡 | runtime |
| 8 | Kapanma sırası yanlışsa ses yolunda pop/hasar oluşur | 3 | 3 | 9 | 🟡 | power-control |
| 9 | Dalga-3 3 çocuk yazılmazsa şema 9 çocuk taahhüdünü karşılamaz | 4 | 3 | 12 | 🟡 | Vault Steward |
| 10 | ID CONFLICT (`K005-veri-yonetimi` ↔ `K005-firmware`) çözülmezse kayıt ikiye ayrılır | 3 | 5 | 15 | 🔴 | Vault Steward |
| 11 | Web iddiaları 90 günden eskiyse kanıt bayatlar, `✅` yanlış kalır | 4 | 3 | 12 | 🟡 | researcher |
| 12 | Bus yarışı (I²C/SPI eşzamanlı erişim) veri bozması | 3 | 4 | 12 | 🟡 | device-control |

**Dağılım:** 🔴 6 · 🟡 6 · 🟢 0 · **Toplam 12**.

Skorlar elle çarpılmaz; §9'daki 1–5 ölçütlerinden okunur. Örnek: risk 1'de
Olasılık 4 = "sınır kayması deneyimlenmiş bir desen", Etki 4 = "iki dosya ailesi
yeniden yazılır" → 16. Aynı mantık 12 satırın tamamında geçerlidir.

## §3 Azaltma planı (kırmızılar zorunlu)

| # | Risk | Azaltma | Tetik / ölçüt | Sahip |
|---|---|---|---|---|
| 1 | K004 sınırı (16) | `firmware-kapsam.md` §3/§4 sınır tablosu + ortak inceleme | K004 ile yazılı teyit | Vault Steward |
| 2 | Secure boot (15) | İmza zinciri tasarımı dalga-3 `security`; anahtar salt-okunur bölgede | ADR onayı | security |
| 3 | Rollback (15) | A/B bölmeli güncelleme + geri-alma sayacı; güncelleme öncesi doğrulama | test senaryosu (HIL) | update |
| 4 | Kanıt sahteciliği (16) | D6/D9 kapısı: `✅` yalnız çekilen URL; dolgu kelime taraması | `firmware-dogrulama.md` §5 | bu ajan |
| 6 | Hedef seçimi (15) | §5 seçim tablosu → onaylı karar + ADR | K-10/K-11 kapanışı | embedded |
| 10 | ID CONFLICT (15) | Onaylı yeniden adlandırma (kopya kayıt yasak) | üst karar | Vault Steward |

Azaltma satırları **yapılabilirlik** taşımalı: her satırda "ne yapılacak" +
"ne zaman tam sayılacak" (Tetik sütunu) vardır. "İzlenecek" yazan bir azaltma
azaltma değildir; çünkü ölçütü yoktur. Azaltma, sahibi olan riskle birlikte
kapanır — sahibi değişirse satır da değişir (§11).

## §4 Sarılar — izleme

| # | Risk | İzleme sinyali | Sıklık |
|---|---|---|---|
| 5 | Register tekrarı | K001 ile dosya karşılaştırması | her sprint |
| 7 | Watchdog | Reset nedeni raporu (diagnostics dalga-3) | sürekli |
| 8 | Kapanma sırası | HIL kapanma testi + pop ölçümü | sürümde |
| 9 | Dalga-3 eksik | D3 dosya sayımı (kök 12 + çocuk şeması) | her üretim |
| 11 | Bayat URL | 90 gün tazelik denemesi | çeyrek |
| 12 | Bus yarışı | Tek-sahip kuralı + kuyruk denetimi | her sprint |

Sarılar **azaltma değil izleme** ister: sinyal üretilir, eşiğe ulaşılırsa satır
kırmızıya döner (§12). Sarı bir riskin "sahibi yok" demek değildir — izleme
sinyalini kim üretiyorsa sahip odur. Sıklık sütunu dört değerden birini alır:
`sürekli` (her çalışma anı) · `her sprint` · `sürümde` · `çeyrek`.

## §5 Risk kuralları

| # | Kural | Kaynak |
|---|---|---|
| 1 | Sahipsiz kırmızı risk yoktur — her 🔴 satırda azaltma sahibi yazılır | bu dosya §3 |
| 2 | Skor uydurulmaz: Olasılık × Etki çarpımı tabloda görünür | ADR-097/AGENTS |
| 3 | Yeni risk = bu tabloya satır; silme yok (yalnız kapanış: `kapandı` notu) | izlenebilirlik |
| 4 | 🔴 sayısı azalmadan `status: verified` açılmaz | D9 kapısı |
| 5 | Ölçülmüş olasılık yoksa sayı tahminidir — `⚠️ TAHMİN` kabul edilir | Zero-Hallucination |
| 6 | Azaltma sahibi rol/agent olabilir; kişi adı uydurulmaz | AGENTS §4 |
| 7 | Yeni risk satırı eklenirse §9'dan Olasılık/Etki gerekçesi de yazılır | bu dosya §9 |
| 8 | Kapanan risk satırda kalır; seviye sütunu `⚪ kapandı` olur | izlenebilirlik |

Kural 4, bu dosyanın kapı gücünü tanımlar: kırmızılar açıkken doğrulanmışlık
iddiası yapılamaz. Kural 5 ise skor dürüstliğini korur — ölçüm yoksa skor
tahmin olarak işaretlenir ve bu işaretle birlikte kullanılır.

## §5.1 Azaltma durum kaydı (kırmızılar — 2026-10-13)

| # | Risk | Azaltma durumu | Kanıt | Sonraki adım |
|---|---|---|---|---|
| 1 | K004 sınırı | Sınır tablosu yazıldı | `firmware-kapsam.md` §4 | yazılı teyit |
| 2 | Secure boot | Tasarım yok — dalga-3 | — | `security` çocuk dosyaları |
| 3 | Rollback | Tasarım yok — dalga-2 `update` | — | `update/*` kapsam dosyaları |
| 4 | Kanıt sahteciliği | D-gate'ler kuruldu | `firmware-dogrulama.md` §3 | V3–V5 adımları |
| 6 | Hedef seçimi | Seçenek envanteri yazıldı | `firmware-bilesenler.md` §5 | K-10 onayı |
| 10 | ID CONFLICT | Kayıt açık tutuldu | `index.md` açık sorular | üst karar |

Durum kaydı, §3 azaltma planının **uygulama tarafıdır**: §3 "ne yapılacak",
bu tablo "ne yapıldı". Kanıt sütununda `—` olan iki satır (2 · 3) henüz başlamamış
azaltmalardır; bunlar kırmızı listeyi temizlemez ve kapı (kural 4) kapalı kalır.

## §6 Doğrulama

| # | Kriter | Yöntem | Durum |
|---|---|---|---|
| M1 | 12 riskte skor = Olasılık × Etki | hesap | ✅ 12/12 |
| M2 | Her 🔴 için azaltma satırı var | çapraz (§2 ↔ §3) | ✅ 6/6 |
| M3 | Sahipler tanımlı (gerçek rol/agent) | okuma | ✅ 12/12 |
| M4 | Sarılar için izleme sinyali var | çapraz (§2 ↔ §4) | ✅ 6/6 |
| M5 | Ölçüt tabloları §2 puanlarıyla tutarlı | §9 çaprazı | ✅ 12/12 |
| M6 | Kırmızılar kapatılmadan verified yok | D9 bağlantısı | 🔄 açık |

M1–M5 bu revizyonun ölçülebilir kapılarıdır; M6 üretim kapağıdır ve 6 kırmızı
açık olduğu için **kapalıdır**. M5, skorların keyfî olmadığını kanıtlar: her
puan, §9'daki tanımlardan birine eşleşmek zorundadır; eşleşmeyen puan M1'i düşürür.

## §7 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q1 | Risk 10 (ID CONFLICT) çözüm tarihi/onayı? | `⚠️ ID CONFLICT — VERIFY REQUIRED` |
| Q2 | Risk 6 hedef seçimi ne zaman? (diğer iddiaları da etkiliyor) | `⚠️ UNKNOWN` |
| Q3 | Risk 11 için 90 günlük tazelik döngüsü kimde? | `⚠️ VERIFY REQUIRED` |
| Q4 | Risk 2 (secure boot) dalga-3'e mi ertelendi, yoksa ADR ile mi öne alınacak? | `⚠️ VERIFY REQUIRED` |

Açık sorular, defterin **bilinmeyenler** yüzüdür: her soru bir riskin ya da
azaltmanın bilinmeyen bağımlılığını taşır. Q1 doğrudan risk 10'u, Q2 risk 6'yı
ve dolaylıyla 1'i, Q3 risk 11'i, Q4 risk 2'yi bloke eder. Soru kapanmadan
ilgili satır kırmızıdan çıkamaz (kural 4).

## §8 Risk kayıt formatı (satır anatomyisi)

| # | Alan | Kural | Örnek (risk 3) |
|---|---|---|---|
| 1 | `#` | `R-` benzeri ikincil etiket gerekmez; tablo sıra no kullanır | `3` |
| 2 | `Risk` | "X olursa Y bozulur" biçiminde tek cümle | "OTA geri alma çalışmazsa brick olur" |
| 3 | `Olasılık` | §9.1 ölçeğinden; ölçüm yoksa `⚠️ TAHMİN` ile | `3` |
| 4 | `Etki` | §9.2 ölçeğinden; ölçülmüşse ölçüm yazılır | `5` |
| 5 | `Skor` | Olasılık × Etki; elle değil, çarpım | `15` |
| 6 | `Seviye` | §9.3 bantlarından | 🔴 |
| 7 | `Sahip` | AGENTS §4'teki rol/agent adı | `update` |
| 8 | Azaltma | §3'e satır (yalnız 🔴 için zorunlu) | A/B slot + rollback counter |
| 9 | İzleme | §4'e satır (yalnız 🟡 için) | HIL test senaryosu |
| 10 | Kapanış | §5.1'e `kapandı` notu + tarih | henüz yok |

Satır anatomyisi, §2 tablosunun 7 sütununun nereden geldiğini gösterir. 8–10.
alanlar §2'de görünmez ama §3/§4/§5.1'de yaşar; bu dağılım kasıtlıdır (tablo
okunur kalsın diye) ve §16 M7 kapısıyla korunur.

## §9 Olasılık / Etki / Bant ölçütleri

### §9.1 Olasılık ölçeği (1–5)

| # | Değer | Anlamı | K005 bağlamı |
|---|---|---|---|
| 1 | Nadir | Tek seferlik, dış tetikleyicili | deprem/elektrik kesintisi benzeri |
| 2 | Seyrek | Yılda birkaç kez | nadir donanım hatası |
| 3 | Olası | Sprint bazında beklenebilir | sınır kayması, tekrar |
| 4 | Sık | Her üretimde beklenebilir | kanıt sahteciliği deseni |
| 5 | Kesin | Şu an yaşanıyor | hedef seçimsizliği (risk 6) |

### §9.2 Etki ölçeği (1–5)

| # | Değer | Anlamı | K005 bağlamı |
|---|---|---|---|
| 1 | Küçük | Tek dosya, tek satır | biçim düzeltmesi |
| 2 | Düşük | Birkaç dosya | tablo satırı ekleme |
| 3 | Orta | Bir çocuk şema | dosya ailesi revizyonu |
| 4 | Yüksek | Kök dosya ailesi | 4+ dosya yeniden yazımı |
| 5 | Kritik | Cihaz/kimlik | brick, güvensiz açılış, kayıt bölünmesi |

### §9.3 Skor bantları

| # | Bant | Aralık | Aksiyon |
|---|---|---|---|
| 1 | 🔴 kırmızı | 15–25 | azaltma sahibi zorunlu (§3) — kapı kapalı |
| 2 | 🟡 sarı | 8–14 | izleme sinyali zorunlu (§4) — çeyrek gözden geçirme |
| 3 | 🟢 yeşil | 1–7 | yalnız kayıt; izleme gerekmez |
| 4 | ⚪ kapandı | — | kapanış notu + tarih ile (§5) |

Ölçek üç tabloya bölünmüştür çünkü tek tabloda hem sayı hem anlam hem aksiyon
karışır. Ölçekler, ISO benzeri **sabit** ölçeklerdir: bir revizyonda 4'ün
anlamı değişirse tüm skorlar yeniden hesaplanır ve §15 günlüğüne satır düşer.

## §10 Yeşil riskler (0 satır — neden)

| # | Durum | Sayı | Yorum |
|---|---|---|---|
| 1 | Kayıtlı yeşil | 0 | defter yalnız kayda değer riskleri taşır |
| 2 | Olası yeşil (hesap) | 0 | 12 satırın tamamı ≥9 skor aldı |
| 3 | Silinen yeşil | 0 | silme yasağı (§5 kural 3) |

Yeşil riskler **kaydedilmez** (bilinçli boşluk): skoru <8 olan bir durum, bu
defterin eşiğinin altındadır ve `firmware-dogrulama.md` kapılarıyla izlenir. Bu
nedenle yeşil satır sayısı 0'dır ve bu, "yeşil risk yok" anlamına gelmez —
"yeşil risk defterde tutulmaz" anlamına gelir. Bu ayrım §16 M8 kapısında ölçülür.

## §11 Risk → dosya izleme

| # | Risk | Yansıyan dosya | Bölüm |
|---|---|---|---|
| 1 | 1 | `firmware-kapsam.md` · `firmware-bilesenler.md` | §3 · §5 |
| 2 | 2 | `firmware-bilesenler.md` · dalga-3 `security` | §6 · gelecek çocuk |
| 3 | 3 | `firmware-bilesenler.md` · `update` çocuk | §7 · gelecek çocuk |
| 4 | 4 | `firmware-dogrulama.md` · `firmware-web-arastirma.md` | §3 · §6 |
| 5 | 5 · 12 | `firmware-bagimliliklar.md` | §4 (K001/K003/K004) |
| 6 | 6 | `firmware-bilesenler.md` · `firmware-kararlar.md` | §5 · K-10 |
| 7 | 7 | `runtime` çocuk · `diagnostics` (dalga-3) | gelecek çocuk |
| 8 | 8 | `power-control` çocuk | gelecek çocuk |
| 9 | 9 | `index.md` · `firmware-kararlar.md` | §GRUPLAR · K-8 |
| 10 | 10 | `index.md` · `firmware-master.md` | açık sorular |
| 11 | 11 | `firmware-web-arastirma.md` · `firmware-dogrulama.md` | §6 · §7 |
| 12 | 12 | `device-control` çocuk · `firmware-bagimliliklar.md` | gelecek çocuk |

İzleme tablosu, her riskin **nerede yaşandığını** gösterir: kök dosya mı, çocuk
dosya mı, gelecek dosya mı. "Gelecek çocuk" ifadesi, riskin henüz dosyası olmayan
bir alanda beklediğini belirtir (dalga-3 + 8 yalnız-`index.md` çocuk). Risk,
dosyası olmayan alanda azaltılamaz; yalnız izlenir.

## §12 Komşu domain risk devri

| # | Risk | Devredilen | Neden | K005'te kalan |
|---|---|---|---|---|
| 1 | 2 (secure boot) | K009-security | imza/anahtar politikası komşu | zincir sırası |
| 2 | 12 (bus yarışı) | K001-donanım | bus fiziksel katman | tek-sahip kuralı |
| 3 | 5 (register tekrarı) | K001-donanım | ham harita komşunun | kullanım |
| 4 | 7 (watchdog) | K000-isletim-sistemi | host WDT API'si | besleme politikası |
| 5 | 8 (kapanma) | K001-donanım + K003-audio | güç/ses yolu komşu | sıralama kararı |
| 6 | 3 (rollback) | — (tamamı K005) | paket formatı K005'in | A/B slot tasarımı |

Devir kuralı: bir riskin **teknik çözümü** komşu domaindeyse risk oraya bildirilir,
ama riskin **sahibi** K005'te kalır (sahip sütunu değişmez). 6. satır istisnadır:
rollback'ın tamamı K005'tir, devredilemez. Bu tablo, `firmware-evrensel-dil.md`
§11 köprüsüyle aynı mantığı paylaşır — terimler gibi riskler de komşuya devredilir,
ama sahiplik dağılmaz.

## §13 Risk denetim adımları (QA)

| # | Adım | Soru | Kanıt |
|---|---|---|---|
| 1 | §2 12 satır okundu mu? | risk listesi biliniyor mu? | okuma notu |
| 2 | Skorlar çarpım ile tutarlı mı? | M1 ihlali | elle çarpım |
| 3 | Her 🔴'de §3 azaltma var mı? | M2 ihlali | çapraz okuma |
| 4 | Her 🟡'de §4 izleme var mı? | M4 ihlali | çapraz okuma |
| 5 | Sahipler AGENTS §4 ile uyumlu mu? | uydurma rol | §4 kontrolü |
| 6 | Ölçütler §2 puanlarıyla tutarlı mı? | M5 ihlali | §9 çaprazı |
| 7 | Kapanan risk var mı? | §5.1 kontrolü | tarih + not |
| 8 | Açık sorular riskleri bloke ediyor mu? | Q1–Q4 durumu | §7 çaprazı |

Denetim 8 adımdır ve §13 (evrensel-dil) ile aynı mantığı paylaşır: önce içerik
(1–6), sonra durum (7–8). Adım atlanan denetim geçersizdir; çünkü bir adımın
kaçırılması, diğerlerinin sonuçsuz kalmasına yol açar (ör. skor tutarsızsa
azaltma eşleşmesi de anlamsızdır).

## §14 Senaryo bazlı risk değerlendirme

| # | Senaryo | Tetikleyici | Etkilenen risk | İlk aksiyon |
|---|---|---|---|---|
| 1 | Cihaz açılmıyor (brick) | bozuk OTA paketi | 3 | A/B slot'a geri dön |
| 2 | Güvensiz açılış | imza doğrulama atlandı | 2 | boot'u durdur |
| 3 | Sınır kayması | K004 işi firmware'e taşındı | 1 | kapsam §3/§4'e bak |
| 4 | Kanıt bayatladı | URL 90+ gün eski | 11 | yeniden çek |
| 5 | Bus verisi bozuldu | eşzamanlı I²C/SPI yazımı | 12 | tek-sahip kilidi |
| 6 | Kayıt bölündü | iki klasör aynı ID ile | 10 | üst karar iste |

Senaryolar, risk defterinin **test tarafıdır**: her satır bir "şu olsa ne yaparız"
cevabı taşır. Senaryo 1 ve 2 acil durumlardır ve sırasıyla risk 3 ve 2'nin
azaltmalarıyla doğrudan bağlantılıdır. Senaryo 6 ise tek acil olmayan ama
kayıt-bölücü senaryodur ve K-12'ye bağlanır.

## §15 Artış / kapanış günlüğü

| Tarih | Olay | Risk | İşlem |
|---|---|---|---|
| 2026-10-12 | Defter kuruldu (§1–§7) | R1…R12 | 12 satır yazıldı |
| 2026-10-13 | §8–§14 eklendi (format · ölçüt · yeşil · izleme · devir · denetim · senaryo) | tümü | denetlenebilirlik |
| 2026-10-13 | R-11 (bayat URL) izleme sinyali güncellendi | 11 | 90 gün → çeyrek gözden geçirme |
| 2026-10-13 | Kırmızılar yeniden ölçüldü: 6 kırmızı, 0 kapandı | 1…10 | kapı kapalı kaldı |

Günlük append-only'dir ve yalnız **değişim olaylarını** taşır: yeni risk, kapanış,
azaltma durumu, ölçüt değişikliği. Değişim olmayan revizyonlarda günlüğe satır
eklenmez (boş satır üretme yasağı).

## §16 Ölçüm ve kabul kriterleri

| # | Kriter | Hedef | Ölçüm yöntemi | Son durum (2026-10-13) |
|---|---|---|---|---|
| M1 | Risk sayısı | 12 | §2 satır sayısı | ✅ 12 |
| M2 | Kırmızı | 6 | §2 seviye sayımı | ✅ 6 |
| M3 | Sarı | 6 | §2 seviye sayımı | ✅ 6 |
| M4 | Sahipsiz kırmızı | 0 | §3 çaprazı | ✅ 0 |
| M5 | Ölçüt tablosu | 3 (§9.1/§9.2/§9.3) | bölüm sayımı | ✅ 3 |
| M6 | Denetim adımı | 8 | §13 satır sayısı | ✅ 8 |
| M7 | Senaryo | 6 | §14 satır sayısı | ✅ 6 |
| M8 | Yeşil kaydı | 0 | §10 sayımı | ✅ 0 |

M1–M4 defterin çekirdeğini, M5–M7 denetim araçlarını, M8 bilinçli boşluğu ölçer.
M8 özellikle önemlidir: yeşil kaydının 0 olması, defterin eşiğinin (≥8 skor)
uygulandığının sayısal kanıtdır. Hedef ile ölçüm uyuşmuyorsa defter teslim
sayılmaz; ölçüm dosyanın kendisinden okunarak yapılır.

## §17 Risk ↔ karar defteri bağı

| # | Risk | İlişkili karar (K-…) | İlişki türü | Birlikte kapanır mı? |
|---|---|---|---|---|
| 1 | 1 (kapsam kayması) | K-1 (kapsam sınırı) | risk, kararın ihlaliyle doğar | evet |
| 2 | 2 (secure boot) | — (karar değil, tasarım) | ADR bekliyor | hayır |
| 3 | 3 (rollback) | K-3 (çoklu-MD) + K-11 (dil) | tasarım diline bağlı | hayır |
| 4 | 4 (kanıt sahteciliği) | K-7 (kanıt kuralı) | risk, kural ihlaliyle doğar | evet |
| 5 | 5 (register tekrarı) | K-1 (kapsam sınırı) | sınır ihlali | evet |
| 6 | 6 (hedef seçimi) | K-10 · K-11 | aynı konu, iki yüz | evet (Q2 kapısı) |
| 7 | 10 (ID CONFLICT) | K-12 | birebir aynı kayıt | evet |
| 8 | 11 (bayat URL) | K-14 (URL dağılımı) | kanıt tazeliği | hayır |
| 9 | 9 (dalga-3) | K-8 (dalga-3 kapsam dışı) | karar, riski bilinçli bırakır | hayır |
| 10 | 7 · 8 · 12 | — (teknik riskler) | karar defterinde değil | hayır |

Bağın iki yönü vardır: (a) karar ihlal edilirse risk doğar (1 · 4 · 5), (b) karar
yokluğu riski sürekli açık tutar (6 · 10). (b) durumu en tehlikelidir: kapı
kapanana kadar risk her revizyonda yeniden ölçülür. Karar defteri
(`firmware-kararlar.md`) bu tabloyla çift yönlü bağlanır; iki dosya arasında
çelişki olursa karar defteri kazanır (kararlar §6.2).

## §18 Azaltma teknik desenleri

| # | Desen | Kullanıldığı risk | Ne yapar | Sınır |
|---|---|---|---|---|
| 1 | Sınır tablosu | 1 · 5 | hangi iş kime ait yazar | ortak inceleme gerekir |
| 2 | Zincirleme doğrulama | 2 · 3 | her adım imza/hash ile | anahtar yönetimi |
| 3 | A/B dual-bank | 3 | geri dönüşlü güncelleme | flaş alanı maliyeti |
| 4 | Kapı-gate (D1–D10) | 4 · 11 | kanıtsız satır geçmez | kapı sayısı şişebilir |
| 5 | Tek-sahip kilidi | 7 · 12 | eşzamanlı erişim engeli | kilitleme gecikmesi |
| 6 | Seçim-öncesi envanter | 6 | seçenek + kriter listesi | karar mercii bekler |
| 7 | Onaylı yeniden adlandırma | 10 | kopya kayıt önlenir | üst onay |
| 8 | Sürümleme + tazelik | 11 | bayat kanıt işaretlenir | otomasyon yok |

Desenler soyut değildir; her biri en az bir kırmızı/sarı riskin azaltmasında
kullanılmıştır (3. sütun). 4. desen (kapı-gate) bu üretimde **dört dosyada**
çalışır: `firmware-dogrulama.md` D1–D10 · `firmware-master.md` §9 · bu dosya §6 ·
`firmware-kararlar.md` §16. Desen 6 ise yalnız K-10/K-11 kapanışına bağlıdır ve
o kapanış gerçekleşene kadar 6 numaralı risk kırmızıda kalır.

## §19 Risk ölçüm metodolojisi (tahmin ↔ ölçüm)

| # | Risk | Ölçüm var mı? | Değerin kaynağı | `⚠️ TAHMİN` işaretli mi? |
|---|---|---|---|---|
| 1 | 1 | hayır | deneyim deseni (sınır kayması) | evet |
| 2 | 2 | hayır | güvenlik literatürü (güvensiz açılış) | evet |
| 3 | 3 | hayır | OTA literatürü (brick senaryosu) | evet |
| 4 | 4 | hayır | bu üretim gözlemi (kağıt-kanıt deseni) | evet |
| 5 | 6 | kısmen | depo ölçümü: 0 firmware dosyası | hayır (kanıt var) |
| 6 | 11 | kısmen | envanter ölçümü: 1441 artefakt + tarih | hayır (kanıt var) |
| 7 | 12 | hayır | bus literatürü (eşzamanlı yazım) | evet |
| 8 | 5 · 7 · 8 · 9 · 10 | hayır | mimari yorum | evet |

Metodoloji iki sütunla dürüstlüğü korur: "ölçüm var mı?" ve "`⚠️ TAHMİN`
işaretli mi?". Ölçüm yoksa skor yine yazılır (§5 kural 5), ama tahmin işareti
taşır. Risk 6 ve 11'de ölçülebilir kanıt var (0 firmware dosyası · 1441 artefakt)
ve bu yüzden tahmin işareti taşımazlar; diğer 8 satır tahmin olarak işaretlenmiştir.
İşaretsiz tahmin, zero-hallucination ihlali olur.

## §20 Bağımlılık zinciri analizi (zincirleme etki)

| # | Başlangıç riski | Zincirleme etki | Ulaştığı ikincil risk | Sonuç |
|---|---|---|---|---|
| 1 | 6 (hedef seçimi yok) | iddialar genel kalır → doğrulanamaz → kanıt zayıf | 4 · 11 | kapı kapalı |
| 2 | 10 (ID CONFLICT) | kayıt ikiye ayrılır → iki SSOT → çelişki | 1 · 5 | defter bölünmesi |
| 3 | 3 (rollback yok) | brick → garanti/iade → alan daralır | 8 (güven kaybı) | ürün riski |
| 4 | 2 (imza kırığı) | güvensiz açılış → alan etkisi → güvenlik olayı | — | en yüksek etki |
| 5 | 1 (sınır kayması) | K004 işi firmware'e taşınır → iki defter | 5 | kapsam şişmesi |
| 6 | 12 (bus yarışı) | bozulmuş veri → yanlış ses çıktısı → pop/hasar | 8 | donanım riski |

Zincir analizi, risklerin **tekil değil bağlı** olduğunu gösterir: en tehlikeli
zincir 4. satırdır (imza → güvensiz açılış) ve tek başına en yüksek Etki (5)
taşıyan risk budur. Zincir 1. satır, K-10/K-11 kapısının neden acil olduğunu
açıklar: hedef seçimi yapılmadan diğer kapılar fiilen kapalıdır.

## §21 Çocuk dosya risk devir listesi

| # | Çocuk | Devredilen risk | Beklenen azaltma/izleme | Durum |
|---|---|---|---|---|
| 1 | boot | 3 (brick) · 2 (imza) | boot-ornekler.md + imza doğrulama | kısmi (4 dosya var) |
| 2 | runtime | 7 (watchdog) | WDT besleme + reset nedeni | yalnız index.md |
| 3 | device-control | 12 (bus yarışı) | tek-sahip kilidi + RMW | yalnız index.md |
| 4 | audio-control | 8 (pop/hasar) | mute sırası + kazanç rampası | yalnız index.md |
| 5 | power-control | 8 (kapanma sırası) | brown-out + güvenli kapanma | yalnız index.md |
| 6 | update | 3 (rollback) | A/B slot + rollback counter | yalnız index.md |
| 7 | diagnostics | 7 (reset nedeni raporu) | POST + reset sayacı | yalnız index.md |
| 8 | security | 2 (secure boot) | zincirleme imza | yalnız index.md |
| 9 | testing | 3 (HIL senaryosu) | donanım-de-test düzeneği | yalnız index.md |

Devir listesi, §11 izleme tablosunun **dosya-yüzünü** taşır: hangi risk hangi
çocuk dosyasında azaltılacak. 1. satır dışındaki 8 çocukta henüz yalnız `index.md`
olduğu için azaltma fiilen başlamamıştır; bu, §5.1'deki iki `—` kanıtının da
nedenidir. Çocuk dosyaları büyüdükçe bu tablo güncellenir ve azaltma durumu
§5.1'e yansıtılır.

## §22 Kapanış protokolü (bir risk nasıl kapanır)

| # | Adım | Koşul | Kanıt |
|---|---|---|---|
| 1 | Azaltma tamamlandı | §3'teki Tetik/ölçüt gerçekleşti | tetik kanıtı |
| 2 | Ölçüm yapıldı | azaltmadan önce/sonra skor yeniden hesaplandı | iki skor |
| 3 | Sahip onayı | risk sahibi (rol/agent) onayı | onay satırı |
| 4 | Kapı etkisi | D9 kapısı yeniden değerlendirildi | kapı sonucu |
| 5 | §5.1'e yazım | `kapandı` + tarih | günlük satırı |
| 6 | §2 seviye değişimi | 🔴/🟡 → ⚪ | tablo güncelleme |

Kapanış 6 adımlıdır ve atlanamaz. En kritik adım 2'dir: azaltma "yapıldı" demek
yetmez, skorun gerçekten düşmüş olması gerekir (ör. risk 7'de watchdog beslemesi
eklendikten sonra reset döngüsü raporu hâlâ üretiyorsa risk kapanmaz). Adım 3'teki
sahip onayı, kural 6'nın yansımasıdır — kapanışı sahibi dışında ilan edemez.

## §23 Yeniden açma (kapanmış risk geri dönerse)

| # | Tetik | Aksiyon | Süre |
|---|---|---|---|
| 1 | İzleme sinyali eşiği aşarsa | risk satırı yeniden açılır, skor yeniden hesap | hemen |
| 2 | Yeni sürüm/regresyon | kapanan satır `⚪` → 🔴/🟡 | hemen |
| 3 | Komşu domain değişikliği | K001/K004 kaynaklı riskler gözden geçirilir | sprint |
| 4 | Ölçüt değişimi (§9 ölçeği) | tüm skorlar yeniden hesaplanır | revizyon |

Yeniden açma, kapanışın tersidir ve aynı disiplinle yürür: kapanan satır silinmez,
durum sütunu değişir, §15 günlüğüne satır düşer. Yeniden açılan bir risk, kapanış
kanıtını taşır (eski azaltma neden işe yaramadı yazılır) — bu, aynı hatanın iki
kez yapılması riskini azaltır.

## §24 Dış bağımlılık riskleri (K005 dışındaki tetikleyiciler)

| # | Bağımlılık | Tetik | Etkilenen K005 riski | K-… |
|---|---|---|---|---|
| 1 | MCU/ürün seçimi | ürün kararı ertelenirse | 6 | K-10 |
| 2 | Dil/toolchain | toolchain lisans/erası | 6 | K-11 |
| 3 | `.ai/.decisions/` ADR süreci | ADR gecikirse | 2 · 10 | K-12 |
| 4 | K004-dsp dosyaları | komşu değişirse | 1 · 5 | K-1 |
| 5 | URL çekme otomasyonu | otomasyon yoksa | 11 | K-14 |
| 6 | Dalga-3 çocuk üretimi | takvim bilinmiyor | 9 | K-8 |

Dış bağımlılık tablosu, iç risklerin **kök nedenlerini** taşır: içerdeki 6 riskin
asıl kaynağı dışarıdadır (karar mercii, toolchain, ADR süreci). Bu nedenle §3'teki
azaltma planı tek başına yeterli değildir; §26 kapıları da izlenmelidir. Dış
bağımlılık, K005'in kontrolü dışındadır ve bu yüzden "azaltılamaz, yalnızca
izlenir" (§5 kural 5 ile aynı ilke).

## §25 Ters risk (fırsat) tablosu

| # | Fırsat | Ters riskin kaynağı | Etki | Ne kazandırır? |
|---|---|---|---|---|
| 1 | Tek sözlük (evrensel-dil) | risk 1/5'in tersi | dosyalar arası tutarlılık | azalan sınır kayması |
| 2 | Kapı-gate (D1–D10) | risk 4'ün tersi | kanıt disiplini | azalan sahtecilik |
| 3 | A/B dual-bank tasarımı | risk 3'ün tersi | geri dönüş | azalan brick korkusu |
| 4 | Seçim-öncesi envanter | risk 6'nın tersi | karar kalitesi | azalan belirsizlik |
| 5 | Tek-sahip kilidi | risk 12'nin tersi | veri bütünlüğü | azalan bus yarışı |

Ters riskler, bu defterin **olumlu yansımasıdır** ve kasıtlı olarak 5 satırla
sınırlıdır (fırsat listesi şişirilmez). Her ters risk, bir iç riskin tersidir
(3. sütun) — bu, defterin iki yüzlü olduğunu ama aynı zamanda tutarlı olduğunu
gösterir: risk → azaltma → kapı → fırsat aynı eksendedir.

## §26 Kapanış özeti (2026-10-13 durumu)

| # | Ölçüm | Değer | Yorum |
|---|---|---|---|
| 1 | Toplam risk | 12 | defterin tamamı |
| 2 | Kırmızı | 6 | kapı kapalı (kural 4) |
| 3 | Sarı | 6 | izleniyor |
| 4 | Kapalı (⚪) | 0 | henüz kapanan yok |
| 5 | Tahmin işaretli | 8/12 | ölçüm eksikliği dürüstçe işaretli |
| 6 | Çocuk dosyaya devredilen | 9 (§21) | 8'i yalnız `index.md` |

Özet tablosu, bu revizyonun **son durumunu** taşır: 6 kırmızı açık olduğu için
`status: verified` yolunda kapı kapalıdır (kural 4). En büyük iki eksik, hedef
seçimi (risk 6 · K-10/K-11) ve çocuk dosya kapsamıdır (risk 9); ikisi de dış
onaya bağlıdır (§26/§24). Bu özet, bir sonraki revizyonun başlangıç çizgisidir:
§2 ölçümü yenilendiğinde bu tablo ile karşılaştırılır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
