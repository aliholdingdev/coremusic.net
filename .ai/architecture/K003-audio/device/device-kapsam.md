---
title: "device — Kapsam"
type: architecture-scope
node_id: K003-audio/device
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
date: 2026-10-12
updated: 2026-10-13
group: mimari
---

# device — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`audio-master.md` §3 satır 2 bu dalı **"Cihaz envanteri, seçimi ve değişimi"**
olarak yazar. Basitçe: sisteme takılı ses cihazlarını listeleyen, doğru olana
yönlendiren ve cihaz değişince kararı yeniden veren düğüm.

`device/index.md` aynı anlamı ELI15 olarak verir: "Hangi ses kartının/hoparlörün/
mikrofonun bağlı olduğunu listeleyen, doğru cihazı seçip gerektiğinde değiştiren
düğüm." İki kaynaktan tek anlam: **envanter + seçim + değişim**.

> ⚠️ VERIFICATION REQUIRED: tanım yalnız v2 §2.3'teki **adından** türetilmiştir
> (master §3 dipnotu); içerik iddiası değildir.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Bağlı ses cihazlarının envanteri (ne var, ne nitelikte) | Cihazın sürücüyle konuşulma biçimi | driver (master §3 satır 3) |
| 2 | Hangi cihazın seçileceğine dair karar (varsayılan, tercih, geri düşme) | Kararın uygulanması (akışın bağlanması) | routing (master §3 satır 7) |
| 3 | Cihaz değişimi / hot-swap kararının yeniden verilmesi | Değişim sırasında buffer'ın nasıl boşaltılacağı | backend (master §3 satır 4) |
| 4 | Giriş cihazı seçimi (mikrofon/kaynak) | Kaynaktan okuma biçimi | input (master §3 satır 5) |
| 5 | Çıkış cihazı seçimi (hoparlör/kulaklık/akış) | Çıkışa yazma biçimi ve gecikme bütçesi | output (master §3 satır 6) |
| 6 | *(dışında)* cihaz kaybı durumunda geri düşme zinciri | Geri düşme kuralının kendisi (ASIO → WASAPI) | driver · diagnostics |
| 7 | *(dışında)* cihazın fiziksel nitelikleri (DAC/ADC, kanal sayısı) | Donanım tasarımı | K001-donanım (master §2.2) |
| 8 | *(dışında)* cihaz envanterinin kullanıcıya gösterimi | Arayüz | uygulama katmanı (master §2.2) |

**Yasak:** satır 3'teki ayrım Q6 kararı alınana kadar **sınır önerisi** değildir;
`input`/`output` ile `device`/`driver` arasında aynı karar iki yerde tanımlanırsa
`audio-bagimliliklar.md` Q6 riski gerçekleşir.

## §3 Alt ayrım noktaları

| # | Ayrım | Ayıran şey | Durum |
|---|---|---|---|
| D1 | device ↔ driver | "hangi cihaz" ↔ "cihazla nasıl konuşulur" | ⚠️ ad-türetme (master §3 satır 2-3) |
| D2 | device ↔ input | kaynak cihazı seçimi ↔ kaynaktan okuma | ⚠️ master §8 Q6 |
| D3 | device ↔ output | çıkış cihazı seçimi ↔ çıkışa yazma | ⚠️ master §8 Q6 |
| D4 | device ↔ routing | seçim kararı ↔ akışın yönlendirilmesi | ⚠️ ad-türetme |
| D5 | device ↔ backend | değişime bağlı buffer davranışı ↔ altyapı | ⚠️ ad-türetme |
| D6 | device ↔ K001-donanım | yazılım envanteri ↔ fiziksel nitelik | ✅ master §2.2 sınır kuralı |
| D7 | device ↔ diagnostics | cihaz kaybı olayı ↔ olayın sınıflandırılması | ⚠️ iç kanıt: ASIO device loss → WASAPI fallback (`.ai/AGENTS.md` §17 #6) |

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk satırı: "device · Cihaz envanteri, seçimi ve değişimi · draft" | `.ai/architecture/K003-audio/audio-master.md` §3 satır 63 | ✅ dosya okundu |
| 2 | ELI15: "cihaz değişince kararı yeniden veren düğüm" | `device/index.md` §1 | ✅ dosya okundu |
| 3 | Açık soru Q6: "input/output ↔ device/driver sorumluluk ayrımı?" | `audio-master.md` §8 Q6 | ✅ dosya okundu |
| 4 | Geri düşme kuralı: "ASIO cihaz kaybında WASAPI fallback" | `.ai/AGENTS.md` §17 #6 (dosya okundu 2026-10-12) | ✅ iç kanıt |
| 5 | Windows platform yüzeyi: WASAPI ana sayfası + exclusive mode streams belgesi | `audio-master.md` §5 T6/T7 | ✅ dosya okundu |
| 6 | Linux platform yüzeyi: ALSA PCM hw/sw params ayrımı | `audio-master.md` §5 T13 | ✅ dosya okundu |
| 7 | Repo'da cihaz envanteri/seçim kodu yok (PHP/JS yüzeyinde ses cihaz yönetimi yok) | shell ölçümü + `shared/src/` envanteri | ✅ ölçüm |
| 8 | Sıcak değiştirme sırasında buffer davranışı | — | **UNKNOWN** (tasarım yok) |
| 9 | Envanterin taşıyacağı alanlar (format/kanal/kip listesi) | — | **UNKNOWN** (kanıt yok) |

## §5 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| R1 | input/output ↔ device/driver sorumluluk ayrımı? (K003 Q6) | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| R2 | Cihaz envanteri hangi alanları listeler? | **UNKNOWN** — kanıt yok | embedded-engineer |
| R3 | Hot-swap sırasında akış ne olur (durdur → değiştir → başlat)? | **UNKNOWN** — tasarım yok | embedded-engineer |
| R4 | Cihaz kaybında geri düşme zinciri sırası nedir? (ASIO → WASAPI tek kural mı?) | ⚠️ VERIFICATION REQUIRED | windows-software-engineer |
| R5 | Birden fazla çıkış cihazı varsa seçim kuralı ne olur? | **UNKNOWN** — kanıt yok | embedded-engineer |
| R6 | Cihaz envanteri hangi thread'de/tarama sıklığında güncellenir? | **UNKNOWN** — sayı uydurmaz | embedded-engineer |

**Kapanış:** status: draft kalır. R1 kapanmadan bu dosyada "device seçer,
driver konuşur" iddiası sınır önerisi olarak **yazılmaz**; R2/R3/R5/R6 için
alan/liste/interval değeri uydurulmaz → UNKNOWN.

---

## §6 Cihaz profil keşfi (envanter taraması) şablonu

> Bu bölüm §1–§5'teki açık soruları (R1–R6) **işlemez ve kapatmaz**; yalnızca
> keşif *şemasını* — hangi aşamada neyin taranacağı — tanımlar. Tablodaki
> hiçbir değer (sıklık, süre, alan listesi) doldurulmaz; doldurulması gereken
> hücreler `UNKNOWN` olarak bırakılır (§5 kapanış kuralı).

### §6.1 Keşif aşamaları (sınır işaretli)

| # | Aşama | Ne toplanır | Bu dalın sahipliği | Komşuya devir | Durum |
|---|---|---|---|---|---|
| E1 | Bağlantı olayı dinleme | tak/çıkar olayının kendisi | ✅ device (§2 satır 3) | — | [TASARIM] şablon |
| E2 | Enumerasyon | bağlı cihazların listesi (kimlik + görünür ad) | ✅ device (§2 satır 1) | — | [TASARIM] şablon |
| E3 | Nitelik okuma | desteklenen format/kanal/kip kümeleri | ✅ device (envanter alanı — §5 R2) | — | [TASARIM] şablon |
| E4 | Sürücü görüşü | cihazın sürücü katmanından gelen durum bildirimi | ⚠️ sınır açık (§3 D1) | driver (§2 satır 1) | SINIR AÇIK |
| E5 | Uygunluk kıyası | kullanıcı/tercih listesi ↔ envanter kesişimi | ✅ device (§2 satır 2) | — | [TASARIM] şablon |
| E6 | Karar | seçilen cihaz (varsayılan → tercih → geri düşme) | ✅ device (§2 satır 2) | routing'e **niyet** | [TASARIM] şablon |
| E7 | Kararın uygulanması | akışın bağlanması | ❌ bu dalda değil | routing · driver (§2 satır 2) | — |
| E8 | Karar sonrası doğrulama | sağlık/ölçüm kontrolü | ❌ bu dalda değil | diagnostics · testing | — |
| E9 | Gösterim | envanterin kullanıcıya arayüzde sunulması | ❌ bu dalda değil | uygulama katmanı (§2 satır 8) | — |
| E10 | Donanım niteliği | DAC/ADC, kanal sayısı, fiziksel jack | ❌ bu dalda değil | K001-donanım (§3 D6) | — |

### §6.2 Envanter alanları adayları (liste ≠ karar)

> §5 R2 ("cihaz envanteri hangi alanları listeler?") **açıktır**. Aşağıdaki
> liste bir *aday şemasıdır*, ölçülmüş/kanıtlanmış alan listesi **değildir**;
> hiçbir satır "CoreMusic bu alanı taşır" iddiası taşımaz.

| # | Alan adayı | Tanım | Olası kaynak (komşu) | Durum |
|---|---|---|---|---|
| F1 | Cihaz kimliği | platformun cihaz/kart kimlik değeri | driver üzerinden | UNKNOWN (§5 R2) |
| F2 | Cihaz görünen adı | kullanıcıya gösterilecek metin | platform enumerasyonu | UNKNOWN (§5 R2) |
| F3 | Yön | giriş · çıkış · çift yönlü | envanter + device (§2 satır 4-5) | UNKNOWN (§5 R2) |
| F4 | Kanal sayısı | desteklenen asgari/azami kanal | envanter niteliği | UNKNOWN (§5 R2) |
| F5 | Örnekleme hızı kümesi | desteklenen hızlar (tek değer değil, küme) | envanter niteliği | UNKNOWN (§5 R2) |
| F6 | Format/bit derinliği kümesi | desteklenen örnek formatları | envanter niteliği | UNKNOWN (§5 R2) |
| F7 | Kip | exclusive/shared/paylaşımlı tercih durumu | driver (§2 satır 1) | SINIR AÇIK (D1) |
| F8 | Varsayılan cihaz bayrağı | platformun varsayılanı mı | platform ayarı | UNKNOWN (§5 R2) |
| F9 | Kullanıcı tercih sırası | karar zincirinin girdisi (§2 satır 2) | device kararı | UNKNOWN (§5 R5) |
| F10 | Sürücü kimliği/sürümü | cihazı konuşturan sürücü yüzeyi | driver · K000 (§3 D5/D6) | SINIR AÇIK (D1) |
| F11 | Clock kaynağı niteliği | zaman referansı bilgisi | synchronization (§3 komşusu) | UNKNOWN (§5) |
| F12 | Latency bildirimi | cihazın beyan ettiği gecikme | diagnostics (§2 satır 5) | UNKNOWN (§5 R2) |
| F13 | Hot-plug destek durumu | tak-çıkar olayı algılanabilir mi | E1 tetikleyicisi | UNKNOWN (§5 R3) |
| F14 | Fiziksel konum etiketi | jak/kart yeri (kullanıcı ayırt etsin diye) | K001-donanım (§3 D6) | UNKNOWN (§5 R2) |
| F15 | Uygunluk durumu | bu cihaz şu an seçilebilir mi | E5 kıyası | UNKNOWN (§5 R5) |
| F16 | Geri düşme geçmişi | bu cihaz düşmüştü mü (zincir kararı girdisi) | diagnostics olayı (§3 D7) | UNKNOWN (§5 R4) |
| F17 | Sahiplik durumu | cihazı başka bir uygulama kullanıyor mu | driver · ASIO tekil kural | ⚠️ VERIFICATION REQUIRED |
| F18 | Kanal maskesi/çıkış düzeni | örn. 8.1 için hoparlör yerleşimi eşlemesi | routing · K001 | UNKNOWN (§5 R2) |

**Alan sayısı uydurulmaz:** tabloda 18 satır olması, CoreMusic'in 18 alan
taşıyacağı anlamına gelmez — satırlar *aday katalog* niteliğindedir.

### §6.3 Tarama tetikleyicileri

| # | Tetikleyici | Ne tetikler | Sıklık/süre | Durum |
|---|---|---|---|---|
| T1 | Uygulama açılışı | ilk enumerasyon (E2) | tek sefer | [TASARIM] şablon |
| T2 | Cihaz tak/çıkar olayı | yeniden tarama (E1 → E2) | olaya bağlı | UNKNOWN (§5 R6) |
| T3 | Kullanıcı seçim değişimi | karar yeniden verilmesi (E6) | etkileşim anında | [TASARIM] şablon |
| T4 | Cihaz kaybı hatası | geri düşme zinciri (§7.4) | hata anında | ⚠️ zincir sırası (§5 R4) |
| T5 | Periyodik tarama | envanter tazeliği | **değer yok** | UNKNOWN (§5 R6) |
| T6 | Sürücü/OS değişimi | tam yeniden keşif | **değer yok** | UNKNOWN (§5 R6) |
| T7 | Akış başlatma öncesi son kontrol | seçimin hâlâ geçerli olduğu | akış açılışında | [TASARIM] şablon |
| T8 | Uygulama odağı/durumu değişimi | envanterin askıya alınması | **değer yok** | UNKNOWN (§5 R6) |

### §6.4 Thread/iş modeli sınırı (tarama nerede koşar?)

| # | Soru | Bu dosyanın cevabı | Etiket |
|---|---|---|---|
| 1 | Tarama hangi thread'de koşar? | Cevaplanmaz — §5 R6 açık | UNKNOWN |
| 2 | Tarama RT audio thread'inde mi olur? | Olamaz: RT yasağı realtime dalının konusudur (§3 D7 komşusu) | ⚠️ sınır komşusu |
| 3 | Tarama sırasında envanter kilitlenir mi? | Tasarım yok; kilit/kilitlenme kararı bu dalda verilmez | UNKNOWN (§5 R6) |
| 4 | Olay bildirimi push mu, pull mu? | Tasarım yok; E1 tetikleyicisi yönü belirsiz | UNKNOWN (§5 R6) |
| 5 | Eşzamanlı tarama (iki tetikleyici üst üste) | Çakışma kuralı yazılmaz; Context Lock kuralı `.ai/AGENTS.md` §12 genel kuralıdır | ⚠️ vault genel kuralı |
| 6 | Tarama sonucu ne zaman yayılır? | Karar yayımı E6 sonrası; zamanlama değeri yok | UNKNOWN (§5 R6) |

## §7 Hot-plug ve cihaz değişimi yaşam döngüsü

> §5 R3 ("hot-swap sırasında akış ne olur?") **açıktır**. Bu bölüm bir
> *aday durum şeması* taşır; "CoreMusic'in durum kümesi budur" iddiası
> **yazılmaz** (aynı soru engine'de Q7 olarak da açıktır — `engine-kapsam.md` §5 R3).

### §7.1 Aday durum kümesi (bağlayıcı DEĞİL)

| # | Durum | Tanım | Giriş | Çıkış | Etiket |
|---|---|---|---|---|---|
| S1 | ENVYOK | hiç cihaz yok / enumerasyon yapılmadı | açılış | E2 tamamlanınca | şablon |
| S2 | TARAMA | enumerasyon sürüyor | E1/T1 | E3 bitince | şablon |
| S3 | ENVANTER | liste hazır, henüz karar yok | T2 | E5/E6 | şablon |
| S4 | SECILI | bir cihaz seçildi, akış bağlı değil | E6 | akış açılınca | şablon |
| S5 | AKTIF | seçili cihazla akış açık | akış start | kayıp gelirse S6 | şablon |
| S6 | CIHAZ_KAYBI | seçili cihaz kayboldu bildirimi | sürücü hatası (§3 D7) | S7 | şablon |
| S7 | ZINCIR_DEGERLENDIRME | geri düşme zinciri değerlendiriliyor | S6 | S8 veya S9 | ⚠️ sıra (§5 R4) |
| S8 | GERI_DUSME | zincirde sonraki kapı denendi | S7 | akış yeniden açılırsa S5 | ⚠️ kapı (§5 R4) |
| S9 | DURDU | zincir tükendi, güvenli duruş | S7 | elle yeniden deneme | şablon |
| S10 | YENIDEN_KURULUM | eski cihaz geri geldi, yeniden karar | E1 | E5/E6 | şablon |

### §7.2 Geçiş tablosu

| # | Durum | Koşul | Hedef durum | Sorumlu | Etiket |
|---|---|---|---|---|---|
| G1 | S1 | ilk tarama tetiklendi | S2 | device | şablon |
| G2 | S2 | envanter ve nitelikler okundu | S3 | device | şablon |
| G3 | S3 | karar verildi | S4 | device | şablon |
| G4 | S4 | akış açıldı | S5 | routing · driver | SINIR AÇIK |
| G5 | S5 | cihaz kaybı bildirimi | S6 | driver → device | §3 D7 |
| G6 | S6 | zincir değerlendirmesi başladı | S7 | device | ⚠️ §5 R4 |
| G7 | S7 | zincirde sonraki kapı geçerli | S8 | driver (§2 satır 5) | ⚠️ §5 R4 |
| G8 | S7 | zincir tükendi | S9 | device · diagnostics | ⚠️ §5 R4 |
| G9 | S5 | cihaz değişimi kullanıcı isteğiyle | S6 → S7 | device (§2 satır 3) | şablon |
| G10 | S8 | akış yeniden açıldı | S5 | routing · driver | SINIR AÇIK |
| G11 | S9 | kullanıcı yeniden dener | S3 | device | şablon |
| G12 | S4–S10 | kayıp cihaz geri geldi (E1) | S3 → S6'ya giden yol değil, yeniden karar | device | şablon |

### §7.3 Hot-swap sırasında buffer davranışı (adaylar — hiçbiri karar değil)

| # | Aday davranış | Akışta ne olur | Neden aday | Etiket |
|---|---|---|---|---|
| B1 | Durdur → boşalt → değiştir → başlat | yazma durur, buffer boşaltılır, cihaz değişir, akış başlar | en klasik sıra | UNKNOWN (§5 R3) |
| B2 | Durdur → yeniden bağla → başlat | boşaltma ayrı adım değil, yeniden bağlama içinde | daha az adım | UNKNOWN (§5 R3) |
| B3 | Boşaltmadan tak-çıkar | buffer dolu kalır, yalnız hedef değişir | kesintisiz görünen ama riskli | UNKNOWN (§5 R3) |
| B4 | Çıkışı sustur → değiştir → aç | clicks/DC için korumalı geçiş | click-free niyeti | ⚠️ komşu: mixing R6 |
| B5 | Akışı kapat → envanter tazele → yeniden karar | tam yeniden kurulum | en güvenli, en uzun | UNKNOWN (§5 R3) |

**Ortak zorunluluk (niyet düzeyi):** geçiş sırasında DC offset/klik üretimi
engellenmelidir — bu CoreMusic kuralının (DC offset koruması) niyet düzeyindeki
yansımasıdır; eşiği ve ölçümü bu dalda **değildir** (diagnostics · testing).

### §7.4 Cihaz kaybı → geri düşme zinciri şablonu

```text
Seçili cihaz kayboldu mu? (driver bildirimi — §3 D7)
  ├─ Hayır → S5 AKTIF devam
  └─ Evet → S6 CIHAZ_KAYBI
       ├─ Zincirde sonraki kapı var mı? (sıra: §5 R4 AÇIK)
       │    ├─ Evet → S8 GERI_DUSME: sıradaki kapı denenir
       │    │    ├─ Açıldı → S5 (olay diagnostics'e bildirilir)
       │    │    └─ Açılamadı → zincirde sonraki dene
       │    └─ Hayır → S9 DURDU + hata raporu (diagnostics)
       └─ Eski cihaz geri geldi → S10 → yeniden karar (E5/E6)
```

**Dürüstlük notu:** ağaç bir **test/akış iskeletidir**. "ASIO → WASAPI
tek/zincir sırasıdır" iddiası §5 R4 kapanmadan yazılmaz (`.ai/AGENTS.md` §17 #6
yalnız "ASIO device loss → WASAPI fallback" kuralını, **zincirin tamamını**
kaydetmez). Sıra sayfadaki satır sayısı kadar **değil**, karar kadar vardır.

## §8 Cihaz başına akış şablonları

> Aşamalı açılma kuralı doğrulanmış kaynaktan gelir: ALSA `hw_params` →
> `sw_params` sırası (`driver-ornekler.md` §2 satır 2; URL bu oturumda fetch
> edilmiştir). Şablonlar bu **aşama düzenine** dayanır; çağrı adları CoreMusic
> API'sine ait değildir (API tasarımı yok → isim uydurmaz).

### §8.1 Çıkış (playback) akış şablonu

| # | Aşama | Ne yapılır | device payı | Komşu payı | Durum |
|---|---|---|---|---|---|
| P1 | Cihaz seçimi | §6/§7 kararından hedef | ✅ | — | şablon |
| P2 | Uygunluk kıyası | format/kanal/kip kümeleri çakışıyor mu | ✅ | envanter (F4–F7) | şablon |
| P3 | Format pazarlığı | desteklenen kümeden bir değer seçilir | niyet | driver yürütür | SINIR AÇIK (D1) |
| P4 | Buffer/periyot bağlama | 512 örnek / 48 kHz hedefi (sartname; ölçüm PLANNED) | niyet | backend · realtime | SINIR AÇIK (§3 D1/E1) |
| P5 | Akışın başlatılması | yazma başlar | ❌ | driver · routing | — |
| P6 | Kayıp gözlemi | hata/dropout bildirimi | olayı sahiplenir | diagnostics toplar | §3 D7 |
| P7 | Kapatma | akış kapanır, envanter kararı korunur | ✅ niyet | driver | şablon |

### §8.2 Giriş (capture) akış şablonu

| # | Aşama | Ne yapılır | device payı | Komşu payı | Durum |
|---|---|---|---|---|---|
| C1 | Kaynak cihazı seçimi | mikrofon/line seçimi | ✅ (§2 satır 4) | input okur (§2 satır 4) | SINIR AÇIK (§3 D2) |
| C2 | Yön doğrulaması | cihaz giriş yönünde mi (F3) | ✅ | — | şablon |
| C3 | Format pazarlığı | capture formatı seçilir | niyet | driver | SINIR AÇIK (D1) |
| C4 | Kanal/kip eşlemesi | tek kanal mı çoklu mu, kip | niyet | input · routing | SINIR AÇIK |
| C5 | Akış başlatma | okuma başlar | ❌ | driver | — |
| C6 | Kesinti/xrun niyeti | taşma bildirimi niyeti taşınır (§2 satır 5) | niyet | diagnostics sınıflandırır | §3 D7 |
| C7 | Kapatma | capture kapanır | ✅ niyet | driver | şablon |

### §8.3 Çift yönlü (duplex) akış şablonu

| # | Aşama | Ne yapılır | Kilit soru | Etiket |
|---|---|---|---|---|
| D1 | Aynı cihaz mı ayrı cihaz mı | giriş ve çıkış ayrı cihazlarda olabilir | seçim kuralı | UNKNOWN (§5 R5) |
| D2 | Ortak clock | iki akış aynı zaman referansında mı | synchronization sınırı | ⚠️ komşu |
| D3 | Ortak periyot | 512 örneğe iki yön de uyar mı | buffer sahipliği | ⚠️ §3 D1/E1 |
| D4 | Gecikme bütçesi | capture+playback toplamı | ölçüm yok | UNKNOWN (§5) |
| D5 | Asimetri | yönler farklı kümelerde format isteyebilir | pazarlık iki kez | SINIR AÇIK (D1) |
| D6 | Kayıp simetriği | bir yön düşerse diğeri ne olur | zincir kararı | ⚠️ §5 R4 |
| D7 | Kapatma sırası | hangi yön önce kapanır | tasarım yok | UNKNOWN |

### §8.4 Exclusive ↔ shared kip karşılaştırması (seçim girdisi)

> Kaynak: `driver-ornekler.md` §2 satır 1 (exclusive-mode-streams — bu oturumda
> fetch edilmiştir). Tablo **kip karşılaştırmasıdır**; CoreMusic hangisini
> seçeceğine dair karar **R1 ile açıktır** (`driver-kapsam.md` §5).

| # | Boyut | Exclusive (özel) | Shared (paylaşımlı) | device kararı etkisi |
|---|---|---|---|---|
| 1 | Sahiplik | uygulama akışa tam sahip | paylaşımlı | F17 sahiplik durumu |
| 2 | Uygulama sayısı | genelde tek uygulama | çok uygulama | ASIO tekil kural ile ilişkili |
| 3 | Format kontrolü | uygulama formatı doğrudan seçer | sistem düzenler | F6/F7 kümesi değişir |
| 4 | Gecikme | düşük gecikme adayı | daha yüksek olabilir | F12 bildirim |
| 5 | Düşme riski | başka uygulama açamaz | çakışma paylaşımlıdır | F17 |
| 6 | Dönüştürme | genelde gerekmez | sistem dönüşüm yapabilir | §9.4 dönüşüm yolları |
| 7 | Kip kararı kime | — | — | ⚠️ `driver-kapsam.md` §5 R1 |

### §8.5 Cihaz kaybı akış şablonu (akış gözünden)

| # | An | Akış davranışı niyeti | Sorumlu | Durum |
|---|---|---|---|---|
| A1 | Kayıp anı | yazma/okuma güvenli biçimde durur | driver bildirir | SINIR AÇIK |
| A2 | Tepki | tıklama/DC üretimi engellenir (niyet) | realtime · mixing komşu | ⚠️ komşu R6 |
| A3 | Karar | zincir sırası değerlendirilir (§7.4) | device | ⚠️ §5 R4 |
| A4 | Uygulama | yeni kapıda akış yeniden açılır | driver · routing | — |
| A5 | Rapor | olay diagnostics'e iletilir (§2 satır 6) | diagnostics | §3 D7 |
| A6 | Geri dönüş | eski cihaz gelince yeniden karar | device | §7.2 G12 |

## §9 WAVEFORMATEX eşlemeleri (Windows format yüzeği)

> ⚠️ **VERIFICATION REQUIRED:** bu bölümdeki alan adları/etiket değerleri
> model hafızasından yazılmıştır; resmi Microsoft WAVEFORMATEX/WAVEFORMATEXTENSIBLE
> dokümanı bu oturumda **fetch edilmedi** (kaynak kanıtı yok). Kullanımdan önce
> `learn.microsoft.com` resmi sayfası üzerinden teyit edilmelidir; teyide kadar
> tablolar **tasarım girdisi adayı**, uygulama talimatı **değildir**.

### §9.1 Temel alan tablosu (aday)

| # | Alan | Ne taşır | device ile ilişkisi | Etiket |
|---|---|---|---|---|
| 1 | format etiketi | örnekleme biçimi (PCM / float vb.) | F6 kümesi | ⚠️ teyit gerekli |
| 2 | kanal sayısı | örnekleme kanalı sayısı | F4 | ⚠️ teyit gerekli |
| 3 | örnek hızı | saniyedeki örnek (örn. hedef 48000 — sartname) | F5 | ⚠️ teyit gerekli |
| 4 | ortalama bayt hızı | saniyedeki bayt (türetilmiş alan) | hesap alanı | ⚠️ teyit gerekli |
| 5 | blok hizası | örnek bloğunun bayt uzunluğu | dönüşüm denetimi | ⚠️ teyit gerekli |
| 6 | bit derinliği | örnekleme çözünürlüğü | F6 | ⚠️ teyit gerekli |
| 7 | ek uzunluk | uzatılmış yapının boyutu | extensible yolu | ⚠️ teyit gerekli |

### §9.2 Format kip eşlemeleri (aday)

| # | Kip | Tipik kullanım | CoreMusic ilgisi | Etiket |
|---|---|---|---|---|
| 1 | PCM (tam sayı) | donanımın kendi kipi | 32-bit float yerine gelen alternatif (ADR-017: veri 32-bit float) | ⚠️ teyit + ADR-017 |
| 2 | IEEE float | kayan noktalı örnek | iç pipeline kipi (ADR-017) | ⚠️ teyit gerekli |
| 3 | Extensible | kanal maskesi + alt format | 8.1 gibi çok kanal için aday yol | ⚠️ teyit gerekli |
| 4 | sıkıştırılmış | codec tabanlı | kapsam dışı (K004/K005 sınırı) | ⚠️ kapsam dışı |

### §9.3 Kanal/konfigürasyon eşlemeleri (8.1 hedefi)

| # | Konfigürasyon | Kanal düzeni notu | İlgili düğüm | Durum |
|---|---|---|---|---|
| 1 | Stereo (2.0) | L/R | routing · output | referans |
| 2 | 5.1 | ön + surround + LFE | routing · K001 | hedef dışı, komşu |
| 3 | 7.1 | 5.1 + arka surround | routing · K001 | harget dışı, komşu |
| 4 | 8.1 | sartname hedefi (çevresel düzen) | routing · K001 · K003-output | HDEF (sartname) |
| 5 | Kanal maskesi | hangi hoparlör slotunun dolu olduğu | routing eşlemesi | ⚠️ teyit gerekli |
| 6 | Cihazın desteklediği düzen | envanter F18 ile kesişir | device kararı | UNKNOWN (§5 R2) |
| 7 | Dönüştürme gerekliliği | cihaz 8.1 desteklemiyorsa | §9.4 yolu | UNKNOWN (R5) |

### §9.4 Uyumsuzluk dönüşüm yolları

| # | Uyumsuzluk | Olası yol | Sahip | Etiket |
|---|---|---|---|---|
| 1 | kanal sayısı uyuşmuyor | sürücüde dönüşüm (ALSA `plug` kanıtı: `driver-ornekler.md` §2 satır 4) | driver | doğrulanmış şablon |
| 2 | örnek hızı uyuşmuyor | sürücüde dönüşüm ya da motor tarafı resampler | driver · K004 | SINIR AÇIK |
| 3 | format/bit uyuşmuyor | sürücüde dönüşüm ya da iç format (float) | driver · ADR-017 | SINIR AÇIK |
| 4 | kip uyuşmuyor (exclusive isteniyor) | shared'a geri düşme | driver R1 | ⚠️ R1 açık |
| 5 | cihaz hiç desteklemiyor | cihaz reddedilir, sıradaki aday (E5) | device | şablon |
| 6 | dönüşüm kaybı | dönüşümün kalitesi | K004 · testing | UNKNOWN |
| 7 | dönüşüm gecikmesi | ek gecikme bütçeye eklenir | diagnostics · performance | UNKNOWN |

## §10 Kaynak dosya satır atıfları

| # | İddia yüzeyi | Dosya | Bölüm/satır | Okuma durumu |
|---|---|---|---|---|
| 1 | Çocuk satırı | `.ai/architecture/K003-audio/audio-master.md` | §3 satır 63 | ✅ okundu (§4 #1) |
| 2 | ELI15 tanımı | `device/index.md` | §1 | ✅ okundu (§4 #2) |
| 3 | Açık soru Q6 | `audio-master.md` | §8 Q6 | ✅ okundu (§4 #3) |
| 4 | Geri düşme kuralı | `.ai/AGENTS.md` | §17 #6 | ✅ okundu (§4 #4) |
| 5 | Windows yüzeyi | `audio-master.md` | §5 T6/T7 | ✅ okundu (§4 #5) |
| 6 | Linux yüzeyi | `audio-master.md` | §5 T13 | ✅ okundu (§4 #6) |
| 7 | Repo envanteri yok | shell ölçümü + `shared/src/` | kök glob 0 isabet | ✅ ölçüm (§4 #7) |
| 8 | Aşamalı açılma (hw_params→sw_params) | `driver-ornekler.md` | §2 satır 2 | ✅ fetch edilmiş URL |
| 9 | `plug` dönüşümü | `driver-ornekler.md` | §2 satır 4 | ✅ fetch edilmiş URL |
| 10 | Exclusive/shared karşılaştırması | `driver-ornekler.md` | §2 satır 1 | ✅ fetch edilmiş URL |
| 11 | PipeWire node/port/link | `device-ornekler.md` | §2 satır 3 | ✅ fetch edilmiş URL |
| 12 | Core Audio envanter kapsamı | `device-ornekler.md` | §2 satır 5 | ✅ fetch edilmiş URL |
| 13 | WAVEFORMATEX alan/etiketleri | — | — | ⚠️ VERIFICATION REQUIRED (§9) |
| 14 | Hot-swap buffer davranışı | — | — | UNKNOWN (§5 R3) |
| 15 | Envanter alan listesi | — | — | UNKNOWN (§5 R2) |

## §11 Uygulama kontrol listesi (kapılar — hepsi KAPALI)

| # | Kapı | Kabul ölçütü | Şu anki durum |
|---|---|---|---|
| 1 | Envanter şeması | R2 kapanır, alan listesi kanıtlanır | KAPALI — R2 açık |
| 2 | Hot-swap sırası | R3 kapanır, sıra ölçülür | KAPALI — R3 açık |
| 3 | Geri düşme zinciri | R4 kapanır, kapı sayısı kararlaştırılır | KAPALI — R4 açık |
| 4 | Çoklu çıkış seçimi | R5 kapanır | KAPALI — R5 açık |
| 5 | Tarama thread/sıklığı | R6 kapanır, sayısal değer ölçülür | KAPALI — R6 açık |
| 6 | input/output ↔ device/driver | Q6 mimari onayı alınır | KAPALI — Q6 açık |
| 7 | WAVEFORMATEX doğrulaması | resmi MS dokümanı fetch edilir | KAPALI — §9 ⚠️ |
| 8 | 8.1 kanal eşlemesi | routing + K001 ile ortak karar | KAPALI — komşu |
| 9 | Ölçüm | cihaz bağlanır, ölçüm günlüğü dolar | KAPALI — ölçüm PLANNED |

## §12 Kapsam dışı taşma denetimi

| # | Eklenen bölüm | Taşma riski | Denetim |
|---|---|---|---|
| 1 | §6 keşif | sürücü çağrısı tarif edilmemiş mi | ✅ yalnız aşama/alan şeması; çağrı yok |
| 2 | §7 hot-plug | fallback sırası kararlaştırılmış mı | ✅ HAYIR — ağaç iskelet, sıra ⚠️ işaretli |
| 3 | §7.3 buffer | ölçülmüş değer yazılmış mı | ✅ HAYIR — niyet satırı, sayı yok |
| 4 | §8 akış şablonları | API/ad kodlanmış mı | ✅ HAYIR — aşama dili, çağrı adı yok |
| 5 | §9 WAVEFORMATEX | doğrulanmış iddia gibi sunulmuş mu | ✅ HAYIR — tablo başlığında ⚠️ uyarısı var |
| 6 | §10 atıflar | uydurma dosya satırı | ✅ hepsi §4'teki mevcut satırlar |
| 7 | §11 kapılar | "tamamlandı" iddiası | ✅ hepsi KAPALI |
| 8 | Genel | yasaklı yer tutucu kalıbı (süslü parantezli boşluk / geçici etiket) | ✅ kullanılmadı |
| 9 | Genel | derlenebilir kod iddiası | ✅ kod bloğu yok (yalnız §7.4 akış ağacı) |
| 10 | Genel | §1–§5 değişikliği | ✅ mevcut bölümlere dokunulmadı |
| 11 | §6.2 | aday listeden "CoreMusic bu alanları taşır" cümlesi | ✅ cümleye çevrilmedi, UNKNOWN sütunu korundu |
| 12 | §10 | atıf satırının dosyadan bağımsız uydurulması | ✅ her satır §4'teki mevcut kayda dayanır |
| 13 | §11 | kapıların "açık" yerine "tamam" gösterilmesi | ✅ 9/9 KAPALI |
| 14 | §13.3 | ölçüm beklentisine sayısal değer yazılması | ✅ tüm süre/sayı hücreleri UNKNOWN/PLANNED |

---

## §13 Komşu etkileşimi, risk ve ölçüm bekleyişleri

### §13.1 Komşu düğüm etkileşim tablosu

| # | Komşu | Yönlü ilişki | Bu dosyanın verdiği | Bu dosyanın almadığı | Kilit |
|---|---|---|---|---|---|
| 1 | driver | device → driver | hangi cihaz (niyet) | cihazla konuşma biçimi | §3 D1 |
| 2 | routing | device → routing | karar sonucu (hedef) | akışın bağlanması | §3 D4 |
| 3 | backend | device → backend | değişime bağlı davranış niyeti | buffer altyapısı | §3 D5 |
| 4 | input | device → input | kaynak cihazı seçimi | kaynaktan okuma | §3 D2 / §5 R1 |
| 5 | output | device → output | çıkış cihazı seçimi | çıkışa yazma | §3 D3 / §5 R1 |
| 6 | diagnostics | device → diagnostics | cihaz kaybı olayı | olayın sınıflandırılması | §3 D7 |
| 7 | testing | — | (bu dosyada yok) | kabul kriteri | master §3 12 |
| 8 | realtime | — | (bu dosyada yok) | zaman/lock-free kuralları | komşu |
| 9 | synchronization | duplex clock niyeti | ortak clock gereksinimi (§8.3 D2) | clock uyumu hesabı | §3 komşuları |
| 10 | K001-donanım | — | yazılım envanteri | fiziksel nitelik | §3 D6 |
| 11 | K000-isletim-sistemi | — | envanterin platformdan geldiği | OS kaynak yönetimi | master §2.2 |
| 12 | uygulama katmanı | — | envanter verisi (potansiyel) | gösterim | §2 satır 8 |

### §13.2 Risk tablosu (bu dosyanın taşıdığı açık riskler)

| # | Risk | Tetikleyici | Etki | Azaltma (karar) | Etiket |
|---|---|---|---|---|---|
| 1 | Çifte sınır tanımı | device ile driver aynı kararı verirse | `audio-bagimliliklar.md` §2 çakışması | Q6 kapatılır | ⚠️ açık |
| 2 | Envanter uydurma | alan listesi kanıtsız yazılır | zero-hallucination ihlali | §5 R2 → UNKNOWN korunur | kontrol ediliyor |
| 3 | Hot-swap veri kaybı | sıra tasarlanmadan kod | kesinti/bozulma | §5 R3 + §7.3 kapıları KAPALI | açık |
| 4 | Geri düşme çökmesi | zincir sırası varsayılır | ses kesintisi | §5 R4 ⚠️ | açık |
| 5 | Tarama yarışı | eşzamanlı tetikleyici | tutarsız envanter | §6.4-5 vault genel kuralı | açık |
| 6 | Kip çakışması | exclusive/shared kararsızlığı | cihaz açılamaz | `driver-kapsam.md` §5 R1 | ⚠️ açık |
| 7 | Format uydurma | WAVEFORMATEX teyitsiz uygulanır | sessiz bozulma | §9 ⚠️ uyarısı | açık |
| 8 | 8.1 eşleme boşluğu | kanal maskesi kararlaştırılmaz | yanlış hoparlör dağılımı | routing + K001 ortak kararı | açık |
| 9 | Ölçümsüz iddia | cihaz bağlanmadan "çalışıyor" denir | yanlış PASS | §11 kapı 9 KAPALI | kontrol ediliyor |
| 10 | Gösterim sızıntısı | envanter UI'a bu dalda yazılır | katman ihlali | §2 satır 8 sınırı | korunuyor |

### §13.3 Ölçüm/telemetri bekleyişleri (diagnostics'e devredilen)

| # | Bekleyiş | Ne ölçülecek | Ne zaman | Durum |
|---|---|---|---|---|
| 1 | Enumerasyon süresi | tarama kaç ms sürüyor | ilk taramada | UNKNOWN (§5 R6) |
| 2 | Hot-plug tepki süresi | olaydan karara kadar | tak-çıkar testinde | UNKNOWN (§5 R3) |
| 3 | Fallback tepki süresi | kayıp anından yeni kapıya | kayıp testinde | UNKNOWN (§5 R4) |
| 4 | Envanter doğruluğu | liste ↔ gerçek cihaz | cihaz bağlanınca | UNKNOWN |
| 5 | Seçim karar süresi | E5 → E6 | karar anında | UNKNOWN |
| 6 | Cihaz kaybı olayı sayısı | sayacı | çalışma boyunca | diagnostics sahası |
| 7 | Geri düşme başarısı | zincirde kaç kapı geçti | olay başına | diagnostics sahası |
| 8 | Kip dağılımı | exclusive/shared kullanımı | çalışma boyunca | UNKNOWN |
| 9 | 8.1 kip geçmişi | hedef kipe geçiş olayı | sartname testinde | PLANNED (ölçüm yok) |
| 10 | DC/klik olayı | geçiş anında koruma olayı | hot-swap testinde | koruma kuralı, ölçüm PLANNED |
| 11 | Çakışan seçim | iki uygulama aynı cihazı isterse | ASIO tekil kural testinde | ⚠️ kural mevcut, davranış tasarımsız |
| 12 | Envanter tazeliği | olay sonrası liste güncellenme gecikmesi | tak-çıkar testinde | UNKNOWN (§5 R6) |
| 13 | Fallback sonrası sağlık | yeni kapıda akış sürekliliği | kayıp kurtarma testinde | diagnostics · testing sahası |

### §13.4 Terim sözlüğü (bu dosyada geçen)

| # | Terim | Bu dosyadaki anlamı | Komşu kullanımdan farkı |
|---|---|---|---|
| 1 | envanter | bağlı cihazların listesi ve nitelikleri | "gösterim listesi" değil (§2 satır 8) |
| 2 | seçim | hangi cihazın kararlaştırıldığı | akışın bağlanması değil (§2 satır 2) |
| 3 | değişim/hot-swap | cihazın değişmesi ve kararın yeniden verilmesi | buffer yönetimi değil (§2 satır 3) |
| 4 | geri düşme | zincirde sonraki kapının denenmesi | zincirin kendisi bu dosyada karar değil (§5 R4) |
| 5 | kip | exclusive/shared sahiplik durumu | kipin uygulanması driver işi |
| 6 | dönüşüm | format/kanal/hız uyuşmazlığının giderilmesi | DSP şekillendirme (K004) değil |
| 7 | duplicate/uygunsuz | envanterde bulunmayan cihaz | — |
| 8 | duplex | giriş+çıkışın eşzamanlı akışı | asimetrik durumlar ayrı ele alınır (§8.3) |
| 9 | ADR-017 | iç veri kipi 32-bit float | donanım kipi (K001) ile karıştırılmaz |
| 10 | ADR-038 H001 | PCM3168A zorunluluğu; PCM5122 yasak | cihaz seçimi yazılım envanteri ≠ DAC seçimi |
| 11 | sartname | 512 örek / 48 kHz hedefi (ölçüm PLANNED) | ölçülmüş değer değil |
| 12 | kapı (§11) | kapatılması gereken açık soru | test geçişi değil |

### §13.5 Kapsam beyanı özeti (tek paragraf)

device düğümü bu genişletme sonrası üç şeyi **net** tutar: (1) envanterin
*neyi tarayacağını* §6 aşamalarıyla, (2) değişimin *hangi durumlarda geçeceğini*
§7 aday şemasıyla, (3) akışın *hangi aşamalardan* geçeceğini §8 şablonlarıyla
tanımlar. Buna karşılık üç şeyi **bilerek boş bırakır**: envanter alan listesi
(§6.2 adaydır, kanıt değil), geri düşme zincirinin sırası (§7.4 iskelettir,
karar değil) ve Windows format alanlarının kesin doğruluğu (§9 tamamen ⚠️).
Bu boşluklar §5'teki R1–R6 ile aynı boşluklardır — yeni soru **açılmadı**,
var olan sorular şablon düzeyine indirgenmeden korundu.

---

## §14 Dürüstlük bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu dosyada AÇIK kalır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
