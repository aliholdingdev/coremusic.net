---
title: "input — Kapsam"
type: architecture-scope
node_id: K003-audio/input
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
date: 2026-10-12
updated: 2026-10-13
group: mimari
---

# input — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`audio-master.md` §3 satır 5 bu dalı **"Kaynak girişi (dosya, mikrofon, akış)"**
olarak yazar. Basitçe: sesin **nereden geldiğini** toplayan düğüm — diskten
okunan bir dosya, mikrofonun yakaladığı canlı sinyal ya da ağdan gelen bir
akış. Tıpkı bir mutfağın malzeme kabulü gibi: malzeme (ses) buradan girer,
sonrası ayrı tezgâhlardır (routing/mixing).

> ⚠️ VERIFICATION REQUIRED: tanım yalnız v2 §2.3'teki **adından** türetilmiştir
> (v2 §3 KURAL 3); içerik iddiası değildir. Kaynak türleri, tamponlama ve
> kesinti davranışı kanıt turunda netleşecektir.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Kaynak türlerinin tek yerde tanımlanması: dosya · mikrofon · akış (adından türetilmiş) | Kaynak cihazının envanteri ve seçimi | device (master §3 satır 2) |
| 2 | Kaynaktan okuma/çekme işleminin sahipliği (ne zaman, ne kadar) | Okumanın zamanlama kuralları (lock-free, öncelik) | realtime (master §3 satır 9) |
| 3 | Kaynak verisinin zincire **verilmesi** (pull-model'de ilk düğüm olma) | Zincirin yürütülmesi | engine (master §3 satır 1) |
| 4 | Girdi gecikmesi (capture latency) niyetinin taşınması | Ölçüm metodu ve rapor | diagnostics (master §3 satır 11) |
| 5 | Kaynak kesintisi/taşması (xrun) durumunda güvenli davranış niyeti | Xrun'un sınıflandırılması | diagnostics · testing |
| 6 | *(dışında)* mikrofon/dosya cihazının sürücüyle konuşması | Sürücü çevirimi | driver (master §3 satır 3) |
| 7 | *(dışında)* çoklu kaynağın birleştirilmesi | Karıştırma matematiği | mixing (master §3 satır 8) |
| 8 | *(dışında)* kaynak kutularının yönlendirilmesi | Kanal eşleme kuralları | routing (master §3 satır 7) |

**Yasak:** satır 1'deki ayrım Q6 (input/output ↔ device/driver) kapanana kadar
**sınır önerisi** değildir; input ile device aynı kararı iki yerde verirse
`audio-bagimliliklar.md` §2 çakışma riski gerçekleşir.

## §3 Alt ayrım noktaları

| # | Ayrım | Ayıran şey | Durum |
|---|---|---|---|
| I1 | input ↔ device | "neyi oku" (akış kaynağı) ↔ "hangi cihaz" (envanter/seçim) | ⚠️ master §8 Q6 — VERIFICATION REQUIRED |
| I2 | input ↔ driver | veri niyeti ↔ sürücü çevirimi (capture) | ⚠️ ad-türetme (master §3 satır 5 ve 3) |
| I3 | input ↔ engine | kaynağı besleme ↔ zinciri yürütme | ⚠️ Q5 ile kilitli (pull-model sahipliği) |
| I4 | input ↔ realtime | ne kadar okunacağı ↔ hangi kuralla zamanlanacağı | ⚠️ ad-türetme (master §3 satır 5 ve 9) |
| I5 | input ↔ output | giriş yönü ↔ çıkış yönü (aynı veri modeli mi?) | ⚠️ Q6 ile bağlantılı — üst karar |
| I6 | input ↔ mixing | tek kaynak ↔ çok kaynağın birleşimi | ✅ master §3 satır 8 (ayrı çocuklar) |

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk satırı: "input · Kaynak girişi (dosya, mikrofon, akış) · draft" | `.ai/architecture/K003-audio/audio-master.md` §3 satır 66 | ✅ dosya okundu |
| 2 | Sınır kuralı: K003 içinden K004'e sinyal gönderilir, içine sokulmaz | `audio-master.md` §2.2 | ✅ dosya okundu |
| 3 | Giriş unit'leri iş isteyecek bir iş parçacığındaki önbellekten örnek alır | `audio-master.md` §4 A1–A2 (kaynak: Apple AudioUnitProgrammingGuide / TN2091) | ✅ dosya okundu |
| 4 | Açık soru Q6: input/output ↔ device/driver sorumluluk ayrımı | `audio-master.md` §8 Q6 | ✅ dosya okundu |
| 5 | Repo'da mikrofon/dosya/akış okuma kodu yok | kök `*.cpp`/`*.h` glob 0 isabet (2026-10-12) | ✅ shell ölçümü |
| 6 | İlgili tek gerçek kod: ffmpeg metadata çıkışı (dosya okuma benzeri, ama AI içindir) | `shared/src/AI/AIEngine.php` satır 300–339 (okundu) | ✅ dosya okundu |
| 7 | ALSA capture tarafı da aynı hw_params/sw_params akışını kullanır (read yolu) | `audio-master.md` §5 T13 → alsa-project.org pcm.html | ✅ dosya okundu (URL fetch) |
| 8 | Pull-model'de giriş verisi önbellekten okunur (yayında örneklemek yerine) | `audio-master.md` §4 A1–A2 (Apple) | ✅ dosya okundu |
| 9 | Input için tampon boyutu/kanal formatı CoreMusic tasarımı | — | **UNKNOWN** (tasarım yok) |
| 10 | Kaynak türlerinin sayısı ve listesi (dosya/mikrofon/akış dışında) | — | **UNKNOWN** (kanıt yok) |

## §5 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| R1 | input ↔ device/driver sorumluluk ayrımı nedir? (master §8 Q6) | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| R2 | Pull-model'de kaynak verisinin sahibi input mu, engine mi? (master §8 Q5) | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| R3 | Kaynak kesintisi (xrun/dropout) nasıl raporlanır? | ⚠️ VERIFICATION REQUIRED | diagnostics |
| R4 | Capture gecikme bütçesi kaç ms? | **UNKNOWN** — ölçüm yok | performance-engineer |
| R5 | Dosya kaynağı ne zaman ön-okunur (buffer ne doldurulur)? | **UNKNOWN** — tasarım yok | embedded-engineer |
| R6 | input ↔ output aynı veri modelini paylaşır mı (çift yönlü akış)? | ⚠️ VERIFICATION REQUIRED | Vault Steward |

**Kapanış:** status: draft kalır. R1/R2 kapanmadan "input cihazı seçmez" ya da
"engine kaynak sahibidir" iddiası yazılmaz; R4/R5 için sayı uydurulmaz → UNKNOWN.

---

## §6 Kaynak türü şablonu (§2 satır 1 — liste UNKNOWN değil, türler aday)

> Üç tür adından türetilmiştir (dosya · mikrofon · akış — §1). Bunların
> **özellik kataloğu** (kanal sayısı, format, bit derinliği) bu turda yok:
> §4 #9/#10 iki ayrı UNKNOWN bırakmıştır.

### §6.1 Kaynak türleri karşılaştırma tablosu

| # | Tür (adından) | Veri yönü | Kesinti riski (aday) | Ön-okuma ihtiyacı | Sınır komşusu |
|---|---|---|---|---|---|
| 1 | dosya | disk → zincir | kesik okuma olası | R5 AÇIK | backend depolama |
| 2 | mikrofon | cihaz → zincir | canlı, telafisiz | — | device · driver (R1) |
| 3 | akış (ağ) | ağ → zincir | gecikme/kesinti olası | tampon politikası yok | ⚠️ hangi dal? kanıt yok |
| 4 | (diğer) | — | — | — | §4 #10 UNKNOWN |

### §6.2 Tür başına davranış şablonları

| # | Tür | Okuma sahipliği (aday) | Durma davranışı | Xrun davranışı | Kapı |
|---|---|---|---|---|---|
| 1 | dosya | §2 satır 2 (input sahiplenir) | dosya biterse ne olur? → UNKNOWN | — | R5 |
| 2 | mikrofon | driver capture → input | cihaz kesilirse | rapor (§2 satır 5) | R1/R3 |
| 3 | akış | ağa kim çeker? → UNKNOWN | akış biterse → UNKNOWN | — | ⚠️ kanıt yok |
| 4 | tümü | §2 satır 2 | — | sınıflandırma diagnostics | R3 |

**Yasak:** tabloya "ağ akışını da destekleriz" gibi **kapsam genişletme**
kararı yazılmaz; tür yalnız adından gelir (§1).

## §7 Okuma/çekme akış şablonları

### §7.1 Pull-model ilk düğüm şablonu (§2 satır 3)

| # | Adım | Ne olur | Sahip (aday) | Kapı |
|---|---|---|---|---|
| 1 | zincir ister (pull) | engine çekirdek çağrı | engine | §4 #3 |
| 2 | input hazır olana bakar | önbellek dolu mu? | input (R2 ⚠️) | R2 |
| 3 | dolu | veriyi verir | input → engine | R2 |
| 4 | boş | ne olur? (bloklama mı, sessizlik mi?) | **UNKNOWN** | ⚠️ R2 + realtime |
| 5 | önbellek dolumu | ne zaman doldurulur? | — | R5 UNKNOWN |

### §7.2 Dosya kaynağı okuma akışı (aday)

```text
dosya kaynağı seçimi (R1: seçim kimde?)
  → ön-okuma penceresi (R5: ne zaman? — UNKNOWN)
    → okuma tamponu dolumu (boyut: UNKNOWN — §4 #9)
      → zincire verme (R2 sahipliği AÇIK)
        → dosya sonu: döngü mü, sessizlik mi, hata mı? — UNKNOWN
          → kapılar: R1 · R2 · R5 (hepsi açık)
```

### §7.3 Mikrofon/line canlı kaynak akışı (aday)

| # | Adım | Doğrulanmış kanıt | Bilinmeyen | Kapı |
|---|---|---|---|---|
| 1 | cihaz capture başlatma | ALSA read yolu hw/sw (§4 #7) | — | ✅ URL |
| 2 | sürücüden veri gelir | pull (§4 #8) | — | ✅ URL |
| 3 | input'a teslim | §2 satır 2/3 | sahiplik R2 | R2 |
| 4 | gecikme niyeti | §2 satır 4 (taşınır) | ms değeri | R4 UNKNOWN |
| 5 | kesinti | §2 satır 5 (niyet) | rapor biçimi | R3 |

### §7.4 Akış (ağ) kaynağı akışı — KANIT YOK

| # | Adım | Durum |
|---|---|---|
| 1 | ağa kim bağlanır? | ⚠️ bilinmiyor (dal tanımı adından) |
| 2 | tampon politikası | UNKNOWN |
| 3 | gecikme toleransı | UNKNOWN |
| 4 | kesinti davranışı | UNKNOWN |

**Kural:** bu dört satır **boş bırakılır**; "WebSocket kullanırız" gibi
teknoloji adı yazılmaz (kanıt yok).

## §8 Sınır tabloları (I1–I5 genişletmesi)

### §8.1 input ↔ device/driver (Q6 girdisi — cevap değil)

| # | Soru | input cevabı | device/driver cevabı | Etiket |
|---|---|---|---|---|
| 1 | hangi mikrofon? | hayır (envanter değil) | evet (device) | R1 |
| 2 | cihazla nasıl konuşulur? | hayır | evet (driver) | R1 |
| 3 | kaynak türü hangisi? | evet (dosya/mikrofon/akış) | hayır | — |
| 4 | capture başlatma | niyet | yürütme | R1 |
| 5 | gecikme ölçümü | niyet taşır (§2 satır 4) | — | diagnostics |
| 6 | xrun olduğunda | bildirir niyeti | — | R3 |

### §8.2 input ↔ engine (Q5 girdisi)

| # | Soru | input | engine | Etiket |
|---|---|---|---|---|
| 1 | veri sahibi kim? | aday (R2) | aday (R2) | ⚠️ R2 |
| 2 | pull isteği kimde? | karşılar | başlatır (§7.1 adım 1) | R2 |
| 3 | boş tamponda ne olur? | UNKNOWN | UNKNOWN | ⚠️ R2 + realtime |
| 4 | dosya sonu? | UNKNOWN | — | R5 |
| 5 | önbellek dolumu | R5 (aday: input) | — | R5 |

### §8.3 input ↔ output (R6 — çift yönlü akış)

| # | Konu | Aynı model mi? | Durum |
|---|---|---|---|
| 1 | veri modeli (çerçeve/kanal) | ? | ⚠️ R6 |
| 2 | tampon şeması | ? | ⚠️ R6 |
| 3 | gecikme kavramı (capture vs render) | ? | ⚠️ R6 |
| 4 | kesinti raporu | ? | ⚠️ R6 + R3 |

**Bu tablo R6'ya CEVAP DEĞİLDİR** — yalnız iki yakayı gösterir.

## §9 Yaygın hata modları (failure modes)

| # | Hata modu | Belirti | Olası kök neden (aday) | İlk tepki | Sınıf (diagnostics) |
|---|---|---|---|---|---|
| 1 | dosya bulunamıyor | ses yok | yol/izin/eksik dosya | bildir | H2 |
| 2 | dosya biçimi desteklenmiyor | okuma ret | format | bildir | H4 |
| 3 | dosya sonu erken | sessizlik | beklenen uzunluk? | — | UNKNOWN |
| 4 | mikrofon yok | canlı kaynak boş | cihaz seçimi (R1) | device'a devir | H2/H3 |
| 5 | canlı kesinti | ses kesik | sürücü/cihaz | bildir niyeti | H1 |
| 6 | xrun (taşma) | dropout | okuma yetersiz | bildir niyeti (§2 satır 5) | H5 |
| 7 | ön-okuma yetmiyor | tekrarlayan xrun | R5 tasarımı yok | — | H5 |
| 8 | ağ akışında boşluk | kopukluk | — (kanıt yok) | — | ⚠️ bilinmiyor |
| 9 | gecikme bütçe aşımı | capture gecikmesi | R4 UNKNOWN | — | H8 |
| 10 | kanal/format uyuşmazlığı | bozuk/yanlış sinyal | veri modeli (R6) | — | H4 |
| 11 | çift kaynak yarışı | iki okuyucu | sahiplik (R2) | — | H3 |
| 12 | durdurulamıyor | kapanışta ses | sahiplik/linger | niyet (§2 satır 5) | H12 komşu |
| 13 | önbellek boşluğu | tik'te veri yok | pull boş (§7.1 adım 4) | UNKNOWN | H6 |
| 14 | tür kapsamı taşması | "başka kaynak da ekleriz" | §4 #10 UNKNOWN | kapsamı değiştirme | — |

### §9.1 Hata modu → açık soru eşlemesi

| # | Hata modu | Hangi R'ye girdi | Kapı |
|---|---|---|---|
| 1, 4 | dosya/cihaz bulunamadı | R1 (Q6) | KAPALI |
| 2, 10 | format/kanal | R6 (veri modeli) + device | KAPALI |
| 3, 12 | dosya sonu/kapanış | R5 (tasarım yok) | KAPALI |
| 5, 6, 7 | kesinti/xrun | R3 (rapor) | KAPALI |
| 9 | gecikme | R4 (bütçe — UNKNOWN) | KAPALI |
| 11, 13 | sahiplik/boşluk | R2 (Q5) | KAPALI |
| 8 | ağ | kanıt yok → — | — |
| 14 | kapsam | §4 #10 UNKNOWN | — |

## §10 Uygulama kapıları (hepsi KAPALI)

| # | Kapı | Soru | Sahip | Durum |
|---|---|---|---|---|
| 1 | R1 | Q6 sorumluluk ayrımı | Vault Steward | KAPALI |
| 2 | R2 | kaynak sahibi (Q5) | Vault Steward | KAPALI |
| 3 | R3 | xrun raporu | diagnostics | KAPALI |
| 4 | R4 | capture gecikme bütçesi (ms) | performance-engineer | KAPALI (UNKNOWN) |
| 5 | R5 | dosya ön-okuma zamanı | embedded-engineer | KAPALI (UNKNOWN) |
| 6 | R6 | input ↔ output veri modeli | Vault Steward | KAPALI |
| 7 | ölçüm | capture gecikme ölçümü | diagnostics | KAPALI (ölçüm yok) |
| 8 | tür listesi | kaynak türü envanteri | — | KAPALI (§4 #10 UNKNOWN) |
| 9 | akış (ağ) | akış kaynağı kanıtı | researcher | KAPALI (kanıt yok) |
| 10 | kod | input okuma kodu | — | KAPALI (glob 0) |
| 11 | §6.2 | tür davranışları kesin mi? | embedded-engineer | KAPALI (aday) |
| 12 | §7.4 | akış akışı tamamlandı mı? | researcher | KAPALI (4 satır boş) |
| 13 | §8.3 | R6 matrisi cevaplandı mı? | Vault Steward | KAPALI ("?") |

## §11 Kapsam dışı taşma denetimi

| # | Eklenen bölüm | Denetim | Sonuç |
|---|---|---|---|
| 1 | §6.1 | tür özellik kataloğu yazıldı mı? | ✅ HAYIR — UNKNOWN (#9/#10) |
| 2 | §6.2 | davranış kesin mi? | ✅ HAYIR — "aday" + kapılar |
| 3 | §7.1 satır 4 | boş tampon davranışı verildi mi? | ✅ HAYIR — UNKNOWN |
| 4 | §7.2 | dosya sonu kararı verildi mi? | ✅ HAYIR — UNKNOWN |
| 5 | §7.4 | akış teknolojisi adı yazıldı mı? | ✅ HAYIR — boş tablo |
| 6 | §8.1/#8.2 | Q6/Q5 cevaplandı mı? | ✅ HAYIR — girdi |
| 7 | §8.3 | R6 cevaplandı mı? | ✅ HAYIR — "?" sütunu |
| 8 | §9 | kök neden kesin mi? | ✅ HAYIR — "aday" |
| 9 | §9 | sayısal eşik/geri sayım? | ✅ HAYIR |
| 10 | §10 | kapı "geçti" mi? | ✅ HAYIR — 13/13 KAPALI |
| 11 | genel | §1–§5 değişikliği | ✅ hayır |
| 12 | genel | yasaklı yer tutucu / derlenebilir kod | ✅ yok (yalnız ```text) |
| 13 | §15.1 | ölçüm → R zinciri kapatıldı mı? | ✅ HAYIR — yalnız yön |
| 14 | §15.2 | kapı sayımı tutuyor mu? | ✅ 6+21+8 ayrı ayrı, hiçbiri geçmedi |
| 15 | §15.3 | sahte ifadeler gerçekten yazılmadı mı? | ✅ HAYIR — hepsi "yazılmaz" sütunu |
| 16 | §16.3 | "akış" sütunu dolduruldu mu? | ✅ HAYIR — 4 kez UNKNOWN |

## §12 Atıf tablosu

| # | Bölüm | Dayanak | Sınıf |
|---|---|---|---|
| 1 | §6.1 | §1 (üç tür adından) | iç kanıt (ad-türetme) |
| 2 | §6.1 satır 4 | §4 #10 (liste UNKNOWN) | bilinmeyen |
| 3 | §6.2 | §2 satır 2/5 + §4 #7 | iç kanıt + URL |
| 4 | §7.1 | §2 satır 3 + §4 #3/#8 (pull) | ✅ URL kanıtlı |
| 5 | §7.2 | §5 R5 + §4 #9 | açık soru + UNKNOWN |
| 6 | §7.3 | §4 #7 (ALSA capture) + §2 satır 4/5 | ✅ URL + iç kanıt |
| 7 | §7.4 | §1 (akış adı) + kanıt yokluğu | bilinmeyen |
| 8 | §8.1 | §5 R1 (Q6) | ⚠️ açık |
| 9 | §8.2 | §5 R2 (Q5) | ⚠️ açık |
| 10 | §8.3 | §5 R6 | ⚠️ açık |
| 11 | §9 | §2 satır 4/5 + diagnostics H-şeması | komşu eşleme |
| 12 | §10 | §5 R1–R6 + §4 #5/#9/#10 | açık soru + ölçüm |
| 13 | §11 | §1–§5 | iç kanıt |
| 14 | §7.1 adım 1 | §4 #3 (önbellekten örnek alan iş parçacığı) | ✅ fetch edilmiş URL |

## §13 Capture gecikme bütçesi (şablon — sayısal değer YOK)

> R4 (ms bütçesi) UNKNOWN'dur; bu bölüm yalnız **kalemleri** listeler.

### §13.1 Bütçe kalemleri

| # | Kalem | Başlangıç | Bitiş | Sahip | Değer |
|---|---|---|---|---|---|
| 1 | cihaz/sürücü capture tamponu | cihaz | sürücü | driver | UNKNOWN (driver R5) |
| 2 | sürücü → input teslimi | sürücü | input | I2 sınırı | UNKNOWN |
| 3 | input önbellek/dolumu | input | zincir | R5 (tasarım yok) | UNKNOWN |
| 4 | zincir işleme payı | input | çıkış | engine | UNKNOWN |
| 5 | toplam capture latansı | — | — | ⚠️ sahip: performance-engineer (R4) | UNKNOWN |

### §13.2 Bütçe denetimi

| # | Soru | Durum |
|---|---|---|
| 1 | hedef ms nedir? | KAPALI — sayı yok (R4) |
| 2 | kalem payları dağıtıldı mı? | KAPALI — pay yok |
| 3 | ölçüm yapıldı mı? | KAPALI — ölçüm yok |
| 4 | ölçüm nasıl yapılır? | KAPALI — yöntem yok |

## §14 Xrun/kesinti şablonları (R3 girdisi — rapor biçimi yazılmaz)

### §14.1 Xrun yaşam şeması (aday)

```text
okuma yetersiz / gecikme (§9 satır 6/7)
  → taşma/eksik olayı (§2 satır 5 — niyet taşınır)
    → input bunu raporlar mı, driver mı üretir? → R1 AÇIK
      → rapor biçimi/sınıfı → R3 AÇIK (diagnostics)
        → ölçüm yok → sayı/eşik yazılmaz (UNKNOWN)
```

### §14.2 Xrun tablosu (aday)

| # | Durum | Ne olur (aday) | Sahiplik | Kapı |
|---|---|---|---|---|
| 1 | tampon taşması | eski veri ezilir mi? | R2 | ⚠️ |
| 2 | tampon boşluğu | boşluk doldurulur mu? | R2 | ⚠️ |
| 3 | tekrarlayan xrun | sayaç? eşiği yok | diagnostics | R3 |
| 4 | canlı kesinti | cihaz kaybı niyeti | driver/device | R1 |
| 5 | dosya sonu | ne olur? | — | R5 UNKNOWN |

**Yasak:** "jitter buffer", "doldur", "tekrar et" gibi **davranış kararı**
burada verilmez; hepsi R2/R5 kapıdadır.

## §15 Ölçüm kapıları (hepsi AÇIK)

| # | Kapı | Ne ölçülecek | Durum | Sahip |
|---|---|---|---|---|
| 1 | capture latansı | §13.1 5 kalem | AÇIK (ölçüm yok) | performance-engineer (R4) |
| 2 | xrun sıklığı | §14.2 satır 3 | AÇIK (eşik yok) | diagnostics (R3) |
| 3 | ön-okuma yeterliliği | §7.2 | AÇIK (tasarım yok) | embedded-engineer (R5) |
| 4 | dosya sonu davranışı | §7.2 | AÇIK | embedded-engineer |
| 5 | boş tampon davranışı | §7.1 satır 4 | AÇIK | R2 + realtime |
| 6 | ağ akış kanıtı | §7.4 | AÇIK (kanıt yok) | researcher |
| 7 | veri modeli eşleşmesi | §8.3 | AÇIK (R6) | Vault Steward |
| 8 | kod oluşumu | input okuma kodu | AÇIK (glob 0) | — |

**Kural:** sekiz kapı da açık kalır; "geçti/ölçüldü" yazısı konmaz.

### §15.1 Ölçüm → R dönüş haritası

| # | Ölçüm | Kapı | Beslediği R | Kapanış koşulu |
|---|---|---|---|---|
| 1 | capture latansı | kapı 1 | R4 (bütçe) | yöntem + ilk kayıt |
| 2 | xrun sıklığı | kapı 2 | R3 (rapor) | rapor biçimi + sayı |
| 3 | ön-okuma yeterliliği | kapı 3 | R5 | tasarım + ölçüm |
| 4 | dosya sonu | kapı 4 | R5 | davranış kararı |
| 5 | boş tampon | kapı 5 | R2 (+realtime) | sahiplik kararı |
| 6 | ağ kanıtı | kapı 6 | — (kanıt) | fetch edilmiş kaynak |
| 7 | veri modeli | kapı 7 | R6 | iki dosya kararı |
| 8 | kod oluşumu | kapı 8 | — | glob >0 |

### §15.2 Kapı sayım özeti

| # | Kaynak | Toplam | Durum |
|---|---|---|---|
| 1 | §10 uygulama kapıları | 13 | 13/13 KAPALI |
| 2 | §13.2 bütçe soruları | 4 | 4/4 KAPALI |
| 3 | §15 ölçüm kapıları | 8 | 8/8 AÇIK |
| 4 | §5 R1–R6 | 6 | 6/6 açık (devir §16.2) |
| 5 | §9.1 hata eşlemesi | 7 | 7/7 KAPALI |

**Toplam:** 6 açık soru + 24 uygulama kapısı KAPALI + 8 ölçüm kapısı AÇIK —
hiçbiri bu oturumda kapatılmadı.

### §15.3 Negatif örnek: kapanmış gibi görünen kapı

| # | Sahte ifade | Neden sahte | Doğru hâli |
|---|---|---|---|
| 1 | "capture bütçesi 5 ms" | R4 UNKNOWN | "bütçe sahibi R4, değer yok" |
| 2 | "xrun eşikleri belirlendi" | R3 açık | "rapor biçimi kapıda" |
| 3 | "dosya ön-okuması 512 örnek" | R5 UNKNOWN | "tasarım yok" |
| 4 | "input cihaz seçmez" | R1 (Q6) açık | "ayrım önerisi, karar R1" |
| 5 | "engine kaynak sahibidir" | R2 (Q5) açık | "aday sahiplik" |
| 6 | "akış kaynağı desteklenir" | kanıt yok | "tür adından; kanıt yok" |
| 7 | "boş tamponda sessizlik yazılır" | R2+realtime | "UNKNOWN" |
| 8 | "ölçüm tamamlandı" | 8/8 AÇIK | "ölçüm PLANNED" |
| 9 | "input=output veri modeli aynı" | R6 açık | "'?' hücresi" |
| 10 | "giriş gecikmesi ölçüldü" | ölçüm yok | "niyet taşınıyor (§2 satır 4)" |

---

## §16 Komşu eşleme ve devir notları

### §16.1 Komşu eşleme

| # | Bu dosya konusu | Komşu dosya | Pay |
|---|---|---|---|
| 1 | Q6 ayrımı (§8.1) | `device/device-kapsam.md` | birlikte karar |
| 2 | sürücü capture (§7.3) | `driver/driver-kapsam.md` §7 | I2 sınırı |
| 3 | pull sahipliği (§8.2) | `engine/engine-kapsam.md` §8.1 | Q5 (R2) |
| 4 | boş tampon (§7.1) | `realtime/` | kurallar |
| 5 | xrun (§14) | `diagnostics/` · `testing/` | rapor + kabul |
| 6 | veri modeli (§8.3) | `output/` | R6 |
| 7 | kaynak yönlendirme | `routing/` | komşu (§2 satır 8) |
| 8 | çoklu kaynak | `mixing/` | komşu (§2 satır 7) |
| 9 | bütçe (§13) | `testing/` | kabul kriteri |
| 10 | gecikme ölçümü | `diagnostics/` | rapor |

### §16.2 Devir notları (sonraki tur)

| # | Devir | Bekleyen iş | Sahip |
|---|---|---|---|
| 1 | Q6 | input ↔ device/driver ayrımı | Vault Steward (R1) |
| 2 | Q5 | kaynak sahibi | Vault Steward (R2) |
| 3 | xrun raporu | biçimi/sınıfı | diagnostics (R3) |
| 4 | capture bütçesi | ms hedefi + ölçüm | performance-engineer (R4) |
| 5 | dosya ön-okuma | zamanlama tasarımı | embedded-engineer (R5) |
| 6 | veri modeli | input ↔ output eşleşmesi | Vault Steward (R6) |
| 7 | akış (ağ) türü | kanıt toplama | researcher |
| 8 | tür listesi | envanter — UNKNOWN | sonraki tur |

**Kural:** sekiz madde de bu dosyada kapatılmaz; kapanış ilgili R numarasında
olur, Authority satırı değişmez.

### §16.3 Kaynak türü × sınır matrisi (tam)

| # | Konu | Dosya | Mikrofon | Akış | Hangi sınır? |
|---|---|---|---|---|---|
| 1 | envanter/seçim | device | device | device (kanıt yok) | R1 (Q6) |
| 2 | sürücü çevirimi | driver (dosya okuma backend) | driver capture | ⚠️ bilinmiyor | R1 |
| 3 | okuma sahipliği | R5/R2 AÇIK | R2 AÇIK | UNKNOWN | R2 |
| 4 | ön-okuma | R5 AÇIK | — (canlı) | UNKNOWN | R5 |
| 5 | gecikme niyeti | — | §2 satır 4 | UNKNOWN | R4 |
| 6 | kesinti/xrun | §9 satır 6 | §9 satır 5/6 | §9 satır 8 | R3 |
| 7 | dosya sonu | §9 satır 3 → R5 | — | — | R5 |
| 8 | veri modeli | R6 AÇIK | R6 AÇIK | R6 AÇIK | R6 |
| 9 | yönlendirme | routing | routing | routing | komşu |
| 10 | birleştirme | mixing (tek kaynak) | mixing | mixing | komşu (I6) |

**Okuma kuralı:** hiçbir hücre kesin cevap değildir; hepsi R1–R6 kapısına
bakar. "Akış" sütunu dört kez UNKNOWN/kanıtsızdır — bu, dalın en zayıf
kanıt yüzeyidir (§4 #10).

### §16.4 Taşma denetimi eki (§13–§16)

| # | Eklenen bölüm | Denetim | Sonuç |
|---|---|---|---|
| 1 | §13.1 | bütçeye ms/pay yazıldı mı? | ✅ HAYIR — 5 hücre UNKNOWN |
| 2 | §13.2 | bütçe sorusu kapatıldı mı? | ✅ HAYIR — 4/4 KAPALI |
| 3 | §14.1 | xrun davranışı verildi mi? | ✅ HAYIR — yalnız yön + kapılar |
| 4 | §14.2 | doldur/tekrar kararı yazıldı mı? | ✅ HAYIR — yasak |
| 5 | §15 | ölçüm kapısı "geçti" mi? | ✅ HAYIR — 8/8 AÇIK |
| 6 | §16.1/16.2 | komşuya karar/uydurma sahip mi? | ✅ HAYIR — §5 R sahipleri |
| 7 | §16.3 | matriste kesin cevap var mı? | ✅ HAYIR — "?" + UNKNOWN |
| 8 | §13–§16 | §1–§5 değişikliği | ✅ hayır |
| 9 | genel | yasaklı yer tutucu kalıbı | ✅ kullanılmadı |
| 10 | genel | derlenebilir kod iddiası | ✅ yok (yalnız ```text) |

## §17 Ek kapı sayımı (§13–§16 için)

| # | Kapı | Ek bölümde soruldu mu? | Durum |
|---|---|---|---|
| 1 | R1 (Q6) | §16.3 matrisi "?" | KAPALI |
| 2 | R2 (Q5) | §7.1/§14.2 sahiplik | KAPALI |
| 3 | R3 (xrun raporu) | §14 | KAPALI |
| 4 | R4 (bütçe ms) | §13 — 5 hücre UNKNOWN | KAPALI |
| 5 | R5 (ön-okuma) | §7.2/§15 kapı 3 | KAPALI |
| 6 | R6 (veri modeli) | §8.3/§15 kapı 7 | KAPALI |
| 7 | ölçüm 8 kapı | §15 — 8/8 AÇIK | KAPALI |
| 8 | akış kanıtı | §7.4 4 satır boş | KAPALI |

**Sonuç:** ek bölümler açık soru sayısını **artırmadı** (6 → 6); yalnız
girdi tabloları genişledi. Dürüstlük bloğu ve Authority bu dosyada tektir.

## §18 Atıf tablosu (§13–§16)

| # | Bölüm | Dayanak | Sınıf |
|---|---|---|---|
| 1 | §13.1 | §5 R4 + §2 satır 4 | açık soru (değerler UNKNOWN) |
| 2 | §13.2 | ölçüm yokluğu | ölçüm kapısı |
| 3 | §14.1 | §2 satır 5 + §5 R1/R3 | iç kanıt + açık soru |
| 4 | §14.2 | §9 satır 5/6/7 + R2/R5 | aday + açık soru |
| 5 | §15 | §5 R1–R6 + §4 #5 | ölçüm kapısı |
| 6 | §16.1 | §2 sınır satırları | iç kanıt |
| 7 | §16.2 | §5 R sahipleri | açık soru |
| 8 | §15.1 | §15 kapıları + §5 R bağlantıları | iç kanıt |
| 9 | §15.2 | §10 + §13.2 + §9.1 sayım | ölçüm |
| 10 | §15.3 | §5 kapanış cümlesi + §4 #9/#10 | negatif örnek |
| 11 | §16.3 | §6.1 türler + §8.1–8.3 | matris (hepsi "?" ) |
| 12 | §16.4 | §13–§16 denetimi | iç kanıt |
| 13 | §13.1 satır 4 | §4 #3/#8 (pull + önbellek) | ✅ fetch edilmiş URL |
| 14 | §14.1 | §2 satır 5 niyeti | iç kanıt |
| 15 | §6.1 | §4 #9 (tampon UNKNOWN) | bilinmeyen |

---

## §19 Dürüstlük bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu dosyada AÇIK kalır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
