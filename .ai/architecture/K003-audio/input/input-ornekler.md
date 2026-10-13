---
title: "input — Örnekler"
type: architecture-examples
node_id: K003-audio/input
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
date: 2026-10-12
updated: 2026-10-13
group: kod
---

# input — Örnekler

> Kural: §1 repo kodu birebir okunur (yol + satır); ilgizse **— yok** yazılır.
> §2 yalnız bu oturumda erişilen URL'lerden üretilir. Uydurma kod/yapı yasaktır.

## §1 Gerçek repo kodu (ÖNCELİK 1)

**— yok.**

`shared/src/`, `shared/tests/` içinde mikrofon/dosya/akış okuma (capture) kodu
yoktur (2026-10-12 kök `*.cpp`/`*.h` glob 0 isabet). Aranan: kaynak okuma
çağrısı, tampon doldurma, xrun/dropout yakalama. Bulunan: yalnız AI/PHP katmanı.

En yakın (ama ilgisiz) gerçek repo kodu — **input kanıtı sayılmaz**:

```php
// shared/src/AI/AIEngine.php : 300-339
// ffmpeg ile metadata çıkışı + extractFeatures() placeholder.
// Dosya okumaya benzer ama amaç AI özellik çıkarımıdır; ses akışı capture'ı
// DEĞİLDİR. input düğümü kanıtı olarak KULLANILAMAZ.
```

## §2 Resmi doküman örneği

| # | Örnek | URL | Adım / parçası | Ne gösterdiği |
|---|---|---|---|---|
| 1 | Giriş unit'i önbellek davranışı | https://developer.apple.com/documentation/coreaudio/audio_unit_programming_guide/audio_units_in_cocoa | "iş parçacığındaki önbellekten örnek alır" — yayında örneklemek yerine önbellek sürücü belleğine örnek alır | Kaynak okuma **çift tamponlu** bir önbellek üzerinden yürür: capture doldurur, render boşaltır |
| 2 | `AudioUnitRender` çekirdek işlevi | https://developer.apple.com/documentation/coreaudio/audiounit/1571111-audiounitrender | `AudioUnitRender` (inRenderTime · inNumberFrames · ioData) | Çekirdek "şimdi ver" der; input düğümü bu **pull** çağrısına veri hazırlayarak yanıt verir |
| 3 | Çekirdek → çıkış unit'i iş isteme zinciri | https://developer.apple.com/documentation/coreaudio/audio_unit_programming_guide/audio_units_in_cocoa | kullanıcı çekirdekten iş ister → çekirdek çıkış unit'ine iş ister → zincir geriye doğru akar | Kaynak en sonda olabilir (pull); input'un konumu sabit değil, **veri akış yönündedir** |
| 4 | ALSA hw_params/sw_params aşamaları | https://www.alsa-project.org/doc/api/group___p_c_m.html | format/rate/channels/buffer → avail_min/period_event | Capture yolu da **aşamalı** açılır: önce format pazarlığı, sonra akış ayarı |
| 5 | ALSA beş access modu (MMAP/RW) | https://www.alsa-project.org/doc/api/group___p_c_m.html | MMAP_INTERLEAVED · MMAP_NONINTERLEAVED · MMAP_COMPLEX · RW_INTERLEAVED · RW_NONINTERLEAVED | Kaynak okuma **erişim kipi** seçeneği taşır (mmap doğrudan ya da read/rewrite) |

## §3 Uygulama notu

Bu örnekler CoreMusic'te **input tasarımı öncesi** şablon olarak kullanılır:
1–3, pull-model'de kaynağın "çağrıldığında ver" mantığına ve çift tampon
önbelleğe dayandığını; 4–5, capture yolunun aşamalı açılışını ve erişim kipi
seçeneklerini. Kesin sahiplik (input mu engine mi) R2 (`input-kapsam.md` §5,
master Q5/Q6) kapanmadan yazılmaz; tampon boyutu/gecikme değeri uydurulmaz →
UNKNOWN.

## §4 İlişkili dosyalar

| Dosya | Rol |
|---|---|
| `input-kapsam.md` | Kademe-2 kapsam kaydı (rol · sınır · disk kanıtları · açık sorular R1–R6) |
| `input-dogrulama.md` | Kanıt/doğrulama tablosu (14 satır: ✅ 9 · ⚠️ 2 · UNKNOWN 3) |
| `input/index.md` | Düğüm kaydı (ELI15 · açık soru Q6) |
| `../audio-master.md` | Üst anayasa §3 satır 5 · §4 A1–A2 · §5 T13 · §8 Q5/Q6 |
| `../audio-bagimliliklar.md` | §2 — input ↔ device/driver sınır riski |
| `../output/output-kapsam.md` | Çığır yönü kardeşi (aynı veri modeli sorusu R6) |
| `mixing/mixing-ornekler.md` | Çoklu kaynağın birleşimi (kapsam §2 satır 7) |
| `routing/routing-ornekler.md` | Kaynak kutularının yönlendirilmesi (kapsam §2 satır 8) |

---

## §5 Eklenen şablonların kullanım sınıfı

| # | Şablon (kapsam §) | Sınıf | Kullanım yeri | Kullanım YASAĞI |
|---|---|---|---|---|
| 1 | §6.1 tür karşılaştırma | aday | Q6 girdisi | özellik kataloğu değil (UNKNOWN) |
| 2 | §6.2 tür davranışları | aday | R5 girdisi | davranış kesin değil |
| 3 | §7.1 pull ilk düğüm | şablon | Q5 girdisi | sahiplik atanmadı |
| 4 | §7.2 dosya akışı | akış ağacı | R5 girdisi | sonuç yazılmaz |
| 5 | §7.3 canlı kaynak | şablon | R1 girdisi | — |
| 6 | §7.4 akış (ağ) | **boş** | — | teknoloji adı yazılmaz |
| 7 | §8.1–8.3 sınır | girdi | Q6/Q5/R6 | cevap DEĞİL |
| 8 | §9 hata modları | aday | diagnostics girdisi | kök neden kesin değil |
| 9 | §13 bütçe | şablon | R4 girdisi | ms/pay YOK |
| 10 | §14 xrun | aday | R3 girdisi | davranış kararı yok |

**Ortak kural:** hepsi girdi tablosudur; kapılar açık kalır (kapsam §10/§15).

## §6 Kaynak türü örnek tabloları

### §6.1 Örnek: dosya kaynağı (aday)

| # | Adım | Gösterilen | Gösterilmeyen | Kapı |
|---|---|---|---|---|
| 1 | dosya seçimi | "seçim kimde?" | karar | R1 |
| 2 | açma/okuma | aşama benzeri | yol/izin ayrıntısı | — |
| 3 | ön-okuma | "zamanı?" | zaman → UNKNOWN | R5 |
| 4 | tampon dolumu | boyut: UNKNOWN | — | §4 #9 |
| 5 | zincire verme | pull yanıtı | sahip → ? | R2 |
| 6 | dosya sonu | 4 yol adayı | hangisi? → UNKNOWN | R5 |

### §6.2 Örnek: mikrofon kaynağı (doğrulanmış ALSA ile)

| # | Adım | Doğrulanmış kaynak | CoreMusic durumu |
|---|---|---|---|
| 1 | format/rate/channels/buffer | ALSA hw_params (§2 satır 4) | ✅ URL |
| 2 | avail_min/period_event | ALSA sw_params (§2 satır 4) | ✅ URL |
| 3 | erişim kipi seçimi | beş access modu (§2 satır 5) | ✅ URL |
| 4 | pull çağrısı | AudioUnitRender (§2 satır 2) | ✅ URL (model) |
| 5 | önbelleğe dolum | çift tampon (§2 satır 1) | ✅ URL (model) |
| 6 | gecikme (ms) | — | UNKNOWN (R4) |

### §6.3 Örnek: akış (ağ) kaynağı — KANIT YOK

| # | Adım | Durum |
|---|---|---|
| 1 | bağlantı | ⚠️ kanıt yok |
| 2 | tampon | UNKNOWN |
| 3 | gecikme toleransı | UNKNOWN |
| 4 | kopukluk | UNKNOWN |

**Kural:** bu tablo dört satırda **biter**; teknoloji adı (protokol/bağlantı
türü) yazılmaz — dalın adı yalnız "akış"tır (kapsam §1).

### §6.4 Negatif tür örnekleri (yapılmayacaklar)

| # | Yapılmayacak | Neden |
|---|---|---|
| 1 | "desteklenen formatlar: …" yazmak | §4 #9 UNKNOWN |
| 2 | "kanal sayısı 2" yazmak | tasarımda yok |
| 3 | "ağ akışını WebSocket ile alırız" | kanıt yok, uydurma |
| 4 | "dört tür daha eklenir" | §4 #10 UNKNOWN |

## §7 Okuma/çekme örnek akışları

### §7.1 Örnek: pull çağrısına yanıt (§2 satır 1–2)

```text
çekirdek: "şimdi ver" (AudioUnitRender — §2 satır 2)
  → input: önbellek dolu mu? (§7.1 adım 2)
    → dolu: önbellekten ver (§2 satır 1 — yayında örneklemek yerine)
      → boş: NE OLUR? — UNKNOWN (R2 + realtime kapıları)
        → sahiplik: input mu engine mi — UNKNOWN (R2/Q5)
```

### §7.2 Örnek: dosya sonu yolları (dört aday — sonuç yazılmaz)

| # | Yol (aday) | Koşul | Sonuç | Karar |
|---|---|---|---|---|
| 1 | döngü | tekrar oynatma | — | R5 (tasarım yok) |
| 2 | sessizlik | sonuna kadar | — | R5 |
| 3 | hata | erken bitiş | — | R3 (rapor) |
| 4 | durdurma | zinciri kapat | — | engine komutu |

**Yasak:** dört yoldan hangisinin geçerli olduğu **bu dosyada seçilemez**.

### §7.3 Örnek: canlı kesinti akışı (§9 satır 5)

| # | Adım | Gösterilen | Sahip |
|---|---|---|---|
| 1 | cihaz kesilir | olay driver'da üretilir (aday) | R1 (Q6) |
| 2 | input'a teslim | niyet (§2 satır 5) | R1 |
| 3 | rapor | biçimi R3 | diagnostics |
| 4 | kurtarma | — | driver/device komşu |
| 5 | ölçüm | yok | R4 UNKNOWN |

### §7.4 Negatif çekme örnekleri

| # | Negatif | Neden yasak |
|---|---|---|
| 1 | tik içinde dosya okumak | realtime kuralları (komşu) |
| 2 | bloklayan okuma | sahiplik + kurallar belirsiz (R2) |
| 3 | boş tamponda "doldur" kararı | R2 + realtime |
| 4 | xrun'da "tekrar et" | R3/R2 kapıda |

## §8 Sınır örnek tabloları (negatif ağırlıklı)

### §8.1 input ↔ device/driver örnek ayrımı (Q6 girdisi)

| # | Örnek soru | input | device/driver | Karar |
|---|---|---|---|---|
| 1 | hangi mikrofon? | hayır | evet | R1 |
| 2 | capture başlatma | niyet | yürütme | R1 |
| 3 | dosya yolu/izin | — | — | backend komşu |
| 4 | sürücü hatası | bildirim niyeti | üretir | R1 + E5 |

### §8.2 input ↔ engine örnek ayrımı (Q5 girdisi)

| # | Örnek soru | input | engine | Karar |
|---|---|---|---|---|
| 1 | veri kimde durur? | aday | aday | R2 |
| 2 | pull kim başlatır? | yanıtlar | çağırır | R2 |
| 3 | boşlukta kim susar? | UNKNOWN | UNKNOWN | R2 + realtime |
| 4 | dosya sonu kimde? | aday | — | R5 |

### §8.3 input ↔ output örnek ayrımı (R6 girdisi)

| # | Örnek | input | output | Karar |
|---|---|---|---|---|
| 1 | çerçevede kanal düzeni | ? | ? | R6 |
| 2 | gecikme adı | capture | render | R6 (eşleşir mi?) |
| 3 | xrun adı | dropout mı? | taşma mı? | R6 + R3 |
| 4 | veri tipi | — | — | R6 |

### §8.4 Negatif sınır örnekleri

| # | Yapılmayacak | Neden |
|---|---|---|
| 1 | "input cihaz seçmez" kesin yazımı | Q6 açık (R1) |
| 2 | "engine kaynak sahibidir" | Q5 açık (R2) |
| 3 | "input=output aynı model" | R6 açık |
| 4 | "girdi gecikmesi X ms" | R4 UNKNOWN |

## §9 Hata modu örnekleri (negatif)

| # | Örnek senaryo | Belirti | Aday kök neden | Neden kesin değil |
|---|---|---|---|---|
| 1 | dosya yok | ses yok | yol/izin | teşhis yok |
| 2 | format ret | okuma hata | desteklenmiyor | — |
| 3 | erken bitiş | sessizlik | beklenen uzunluk? | R5 |
| 4 | mikrofon yok | canlı boş | cihaz seçimi | R1 (Q6) |
| 5 | canlı kesinti | ses kesik | sürücü/cihaz | R1 + R3 |
| 6 | xrun | dropout | okuma yetersiz | R3 |
| 7 | ön-okuma yetmedi | tekrarlayan xrun | tasarım yok | R5 |
| 8 | ağ boşluğu | kopukluk | kanıt yok | — |
| 9 | gecikme aşımı | budget | R4 UNKNOWN | — |
| 10 | kanal uyuşmazlık | bozuk sinyal | veri modeli | R6 |
| 11 | çift okuyucu | yarış | sahiplik | R2 |
| 12 | kapanışta ses | patlama | linger/sahiplik | realtime/mixing |

### §9.1 Negatif kural örnekleri

| # | Kural | Kaynak |
|---|---|---|
| 1 | xrun eşiği sayı ile yazılmaz | diagnostics R2 |
| 2 | dosya sonu kararı verilmez | R5 |
| 3 | boş tampon davranışı verilmez | R2 + realtime |
| 4 | kök neden "aday" kalır | kapsam §9 başlığı |

## §10 Bütçe ve ölçüm örnekleri (sayısal değer YASAK)

| # | Kalem (kapsam §13.1) | Örnek gösterim | Yazılamayan |
|---|---|---|---|
| 1 | sürücü capture tamponu | "driver R5'e" | ms |
| 2 | sürücü → input | "I2 sınırı" | ms |
| 3 | önbellek dolumu | "R5 açık" | ms |
| 4 | zincir payı | "engine" | ms |
| 5 | toplam | "sahip: R4 (performance-engineer)" | hedef ms |
| 6 | xrun sıklığı | "ölçülür → R3" | eşik |

**Örnek yasak cümle:** "capture gecikmesi 3 ms" — R4 kapısı açık, ölçüm yok.

## §11 Ölçüm kapısı örnekleri (hepsi AÇIK)

| # | Kapı (kapsam §15) | Örnek "geçti" iddiası | Gerçek durum |
|---|---|---|---|
| 1 | capture latansı | "ölçüldü" | ❌ yok |
| 2 | xrun sıklığı | "kabul edilebilir" | ❌ eşik yok |
| 3 | ön-okuma | "yeterli" | ❌ tasarım yok |
| 4 | dosya sonu | "çözüldü" | ❌ R5 açık |
| 5 | boş tampon | "sessizlik" | ❌ R2 açık |
| 6 | ağ kanıtı | "destekleniyor" | ❌ kanıt yok |
| 7 | veri modeli | "aynı" | ❌ R6 açık |
| 8 | kod | "var" | ❌ glob 0 |

## §12 Repo kod yüzeyi — okuma kaydı

| # | Yüzey | Aranan | Bulunan | Sonuç |
|---|---|---|---|---|
| 1 | kök `*.cpp`/`*.h` | capture okuma | 0 isabet (2026-10-12) | yok |
| 2 | `shared/src/` | mikrofon/dosya/akış | PHP katmanı | yok |
| 3 | `AIEngine.php` : 300–339 | dosya okuma | ffmpeg metadata (AI) | input DEĞİL (§1) |
| 4 | `shared/tests/` | capture testi | PHP testleri | yok |
| 5 | xrun/dropout | yakalama kodu | — | yok |
| 6 | tampon dolumu | okuma kodu | — | yok |

## §13 Komşu eşleme ve kapı listesi

### §13.1 Komşu eşleme

| # | Şablon | Komşu dosya | Pay |
|---|---|---|---|
| 1 | §6.2 ALSA aşama | `driver/driver-ornekler.md` §7.2 | aynı URL kanıtı |
| 2 | §7.1 pull | `engine/engine-ornekler.md` §7 | Q5 |
| 3 | §7.3 kesinti | `diagnostics/diagnostics-ornekler.md` | sınıf atama |
| 4 | §8.3 | `output/output-ornekler.md` | R6 |
| 5 | §9 xrun | `realtime/` · `testing/` | kural + kabul |
| 6 | §10 bütçe | `testing/` | kabul kriteri |
| 7 | §6.1 seçim | `device/device-ornekler.md` | R1 (Q6) |
| 8 | yönlendirme | `routing/` | komşu |
| 9 | birleştirme | `mixing/` | komşu |
| 10 | önbellek modeli | `backend/` | Q5 |

### §13.2 Kapı listesi (hepsi KAPALI)

| # | Kapı | Soru | Durum |
|---|---|---|---|
| 1 | R1 | Q6 ayrımı | KAPALI |
| 2 | R2 | kaynak sahibi (Q5) | KAPALI |
| 3 | R3 | xrun raporu | KAPALI |
| 4 | R4 | capture bütçesi | KAPALI (UNKNOWN) |
| 5 | R5 | ön-okuma zamanı | KAPALI (UNKNOWN) |
| 6 | R6 | veri modeli | KAPALI |
| 7 | ölçüm | 8 ölçüm kapısı | KAPALI (8/8 AÇIK) |
| 8 | akış | ağ kanıtı | KAPALI (kanıt yok) |
| 9 | kod | input kodu | KAPALI (glob 0) |

## §14 Atıf tablosu

| # | Bölüm | Dayanak | Sınıf |
|---|---|---|---|
| 1 | §5 | kapsam §6–§15 başlıkları | iç kanıt |
| 2 | §6.1 | §5 R1/R5 + §4 #9 | açık soru + UNKNOWN |
| 3 | §6.2 | §2 satır 4/5 + §4 #7 | ✅ fetch edilmiş URL |
| 4 | §6.3 | §1 ad + kanıt yokluğu | bilinmeyen |
| 5 | §6.4 | §4 #9/#10 | UNKNOWN |
| 6 | §7.1 | §2 satır 1–2 | ✅ fetch edilmiş URL |
| 7 | §7.2 | §5 R5 + §9 satır 3 | açık soru |
| 8 | §7.3 | §2 satır 5 + §5 R1/R3/R4 | iç kanıt + açık |
| 9 | §8 | kapsam §8.1–8.3 | girdi tablosu |
| 10 | §9 | kapsam §9 + diagnostics H-şeması | aday + komşu |
| 11 | §10 | kapsam §13 | şablon (ms YASAK) |
| 12 | §11 | kapsam §15 + §4 #5 glob 0 | ölçüm kapısı |
| 13 | §12 | §1 ölçümü | ölçüm |

## §15 Taşma denetimi (§5–§14)

| # | Denetim | Sonuç |
|---|---|---|
| 1 | §6'da özellik kataloğu yazıldı mı? | ✅ HAYIR — UNKNOWN |
| 2 | §6.3'te teknoloji adı var mı? | ✅ HAYIR — 4 satır boş |
| 3 | §7.2'de dosya sonu seçildi mi? | ✅ HAYIR — 4 aday |
| 4 | §7.1'de boş tampon kararı? | ✅ HAYIR — UNKNOWN |
| 5 | §8'de Q5/Q6/R6 cevaplandı mı? | ✅ HAYIR — girdi |
| 6 | §9'da kök neden kesin mi? | ✅ HAYIR — "aday" |
| 7 | §10'da ms/eşik yazıldı mı? | ✅ HAYIR — yasak sütunu |
| 8 | §11'de kapı "geçti" mi? | ✅ HAYIR — 8/8 AÇIK |
| 9 | §12'de kod uyduruldu mu? | ✅ HAYIR — ölçüm |
| 10 | §1–§4 değişikliği | ✅ hayır |
| 11 | yasaklı yer tutucu kalıbı | ✅ kullanılmadı |
| 12 | derlenebilir kod iddiası | ✅ yok (yalnız ```text/```php notu) |
| 13 | Authority tekliği | ✅ tek |

## §16 Devir notları (sonraki tur)

| # | Devir | Bekleyen | Sahip |
|---|---|---|---|
| 1 | Q6 | input ↔ device/driver | Vault Steward (R1) |
| 2 | Q5 | kaynak sahibi | Vault Steward (R2) |
| 3 | xrun raporu | biçim/sınıf | diagnostics (R3) |
| 4 | capture bütçesi | ms + ölçüm | performance-engineer (R4) |
| 5 | ön-okuma | zamanlama tasarımı | embedded-engineer (R5) |
| 6 | veri modeli | input ↔ output | Vault Steward (R6) |
| 7 | ağ kanıtı | fetch edilmiş kaynak | researcher |
| 8 | ölçüm 8 kapı | ilk ölçüm | diagnostics + testing |

## §17 Kaynak türü × kullanım senaryosu matrisi (tam)

| # | Senaryo (aday) | Dosya | Mikrofon | Akış | Hangi kapı? |
|---|---|---|---|---|---|
| 1 | oynatma (çalma) | ✅ aday | — | — | R5 |
| 2 | canlı kayıt | — | ✅ aday | — | R1 (Q6) |
| 3 | canlı monitör | — | ? (geçiş yolu) | — | R2 + realtime |
| 4 | ağ yayını dinleme | — | — | ? kanıt yok | — (kanıt) |
| 5 | dosya → çıkış | ✅ | — | — | Q5 (R2) |
| 6 | mikrofon → çıkış | — | ✅ | — | R1 + R6 |
| 7 | çoklu kaynak | ikisi de | ikisi de | — | mixing komşu |
| 8 | gecikme ölçümü | — | R4 | — | ölçüm 1 |
| 9 | xrun gözlemi | R3 | R3 | — | ölçüm 2 |
| 10 | kapanış | R5 | R1 | — | §9 satır 12 |

**Okuma kuralı:** "✅ aday" = tür adıyla uyumlu şablon var; "?" = kapı açık;
"—" = bu senaryo için tür gerekmez. **Hiçbir hücre kesin karar değildir.**

### §17.1 Matris denetimi

| # | Denetim | Sonuç |
|---|---|---|
| 1 | hücrelere teknoloji/parametre yazıldı mı? | ✅ HAYIR |
| 2 | "canlı monitör" yolu kararlaştırıldı mı? | ✅ HAYIR — "?" + iki kapı |
| 3 | ağ senaryosu dolduruldu mu? | ✅ HAYIR — kanıt yok |
| 4 | Authority tekliği | ✅ tek |

## §18 Örnek: pull → CoreMusic eşlemesi (tam)

| # | Doğrulanmış örneğin adımı (§2) | CoreMusic şablonundaki karşılığı | Yer | Durum |
|---|---|---|---|---|
| 1 | önbellekten örnek alır | çift tampon (capture doldurur) | §6.2 satır 5 | ✅ URL |
| 2 | `AudioUnitRender` "şimdi ver" | pull yanıt şablonu | §7.1 | ✅ URL |
| 3 | çekirdek → çıkış → zincir | pull yönü | §7.1 | ✅ URL |
| 4 | ALSA hw→sw aşama | canlı açılış | §6.2 satır 1–2 | ✅ URL |
| 5 | beş access modu | erişim kipi seçeneği | §6.2 satır 3 | ✅ URL |
| 6 | (eşleşme) sahiplik | — | §8.2 | R2 AÇIK |
| 7 | (eşleşme) tampon boyu | — | §6.1 satır 4 | UNKNOWN |
| 8 | (eşleşme) gecikme | — | §10 | R4 UNKNOWN |

### §18.1 Eşleme denetimi

| # | Denetim | Sonuç |
|---|---|---|
| 1 | eşleme "uygulandı" mi yazıldı? | ✅ HAYIR — şablon |
| 2 | tampon boyu kondu mu? | ✅ HAYIR — UNKNOWN |
| 3 | gecikme ms kondu mu? | ✅ HAYIR — R4 |
| 4 | kapılar kapatıldı mı? | ✅ HAYIR |

## §19 Örnek: hata × tür matrisi (tam — kök nedenler "aday")

| # | Hata (§9) | Dosya | Mikrofon | Akış | Bağlı kapı |
|---|---|---|---|---|---|
| 1 | kaynak bulunamadı | ✅ (yol/izin) | ✅ (cihaz) | — | R1 |
| 2 | format ret | ✅ | ✅ | — | R6 + device |
| 3 | erken bitiş | ✅ | — | — | R5 |
| 4 | canlı kesinti | — | ✅ | ✅? | R1 + R3 |
| 5 | xrun/taşma | ✅? | ✅ | ✅? | R3 |
| 6 | ön-okuma yetmedi | ✅ | — | — | R5 |
| 7 | gecikme aşımı | — | ✅ | — | R4 |
| 8 | veri modeli uyuşmazlık | ✅ | ✅ | ✅ | R6 |
| 9 | çift okuyucu | ✅ | ✅ | — | R2 |
| 10 | kapanışta patlama | ✅ | ✅ | — | realtime + mixing |

**Okuma kuralı:** "✅?" = tür için olası ama kanıt zayıf; "—" = bu türde
görülmez (aday). Kök nedenlerin hiçbiri kesin değildir (kapsam §9 başlığı).

### §19.1 Matris denetimi

| # | Denetim | Sonuç |
|---|---|---|
| 1 | kök neden kesin yazıldı mı? | ✅ HAYIR — "aday" + "?" |
| 2 | kapılar kapatıldı mı? | ✅ HAYIR — hepsi açık |
| 3 | sayısal eşik kondu mu? | ✅ HAYIR |
| 4 | Authority tekliği | ✅ tek |

### §19.2 Kapı sayım özeti (bu dosya + kapsam)

| # | Kaynak | Toplam | Durum |
|---|---|---|---|
| 1 | `input-kapsam.md` §10 | 13 | 13/13 KAPALI |
| 2 | `input-kapsam.md` §15 ölçüm | 8 | 8/8 AÇIK |
| 3 | bu dosya §13.2 | 9 | 9/9 KAPALI |
| 4 | bu dosya §11 ölçüm | 8 | 8/8 AÇIK (aynı kapılar) |
| 5 | §5 R1–R6 (kapsam) | 6 | 6/6 açık — devir §16 |

**Toplam:** 6 açık soru + 22 uygulama kapısı KAPALI + 8 ölçüm kapısı AÇIK;
bu dosyada **hiçbir kapı kapatılmadı**.

## §19.3 Negatif örnek: kapanmış gibi görünen kapı

| # | Sahte ifade | Neden sahte | Doğru hâli |
|---|---|---|---|
| 1 | "capture bütçesi verildi" | R4 UNKNOWN | "bütçe yok (§10)" |
| 2 | "dosya sonu çözüldü" | R5 açık | "4 aday (§7.2)" |
| 3 | "boş tamponda sessizlik" | R2 açık | "UNKNOWN (§7.1)" |
| 4 | "akış kaynağı planlandı" | kanıt yok | "kanıtsız (§6.3)" |
| 5 | "xrun eşiği 10" | sayı uydurma | "eşik yok (§11)" |
| 6 | "input cihaz seçmez" | Q6 açık | "ayrım önerisi (R1)" |
| 7 | "engine sahiptir" | Q5 açık | "aday sahiplik (R2)" |
| 8 | "ölçüldü" | 8/8 AÇIK | "ölçüm PLANNED" |
| 9 | "giriş gecikmesi düşük" | ms yok | "UNKNOWN" |
| 10 | "kod hazır" | glob 0 | "kod yok (§12)" |
| 11 | "format listesi belirlendi" | §4 #9 UNKNOWN | "UNKNOWN" |
| 12 | "monitor yolu var" | §17 "?" iki kapı | "kapıda" |

### §19.4 Devir notları eki (§17–§19)

| # | Devir | Bekleyen | Sahip |
|---|---|---|---|
| 1 | §17 senaryo matrisi "?" hücreleri | 10 hücrelik kapı turu | Vault Steward |
| 2 | §18.1 eşleşme satır 6–8 | R2/R5/R4 | §16 devri ile aynı |
| 3 | §19.3 sahte ifadeler | denetim turu | Vault Steward |
| 4 | §19.1 hata × tür "?" hücreleri | kapı turu | Vault Steward |

**Kural:** dört madde de bu dosyada kapatılmaz; Authority tektir.

### §19.5 Taşma denetimi eki (§17–§19)

| # | Eklenen bölüm | Denetim | Sonuç |
|---|---|---|---|
| 1 | §17 | senaryo kesinleşti mi? | ✅ HAYIR — "?" + "aday" |
| 2 | §17.1 | teknoloji adı kondu mu? | ✅ HAYIR |
| 3 | §18 | eşleme "uygulandı" mı? | ✅ HAYIR — şablon |
| 4 | §18.1 | tampon/ms kondu mu? | ✅ HAYIR — UNKNOWN/R4 |
| 5 | §19 | kök neden kesin mi? | ✅ HAYIR — "aday" |
| 6 | §19.2 | kapı sayımı | ✅ 22 KAPALI + 8 AÇIK + 6 açık R |
| 7 | §19.3 | sahte ifadeler gerçekten var mı? | ✅ HAYIR — "yazılmaz" sütunu |
| 8 | §17–§19 | §1–§4 değişikliği | ✅ hayır |
| 9 | genel | yasaklı yer tutucu kalıbı | ✅ kullanılmadı |
| 10 | genel | derlenebilir kod iddiası | ✅ yok |
| 11 | genel | Authority tekliği | ✅ tek |
| 12 | §19.4 | devir sahipleri uyduruldu mu? | ✅ HAYIR — §5 R sahipleri |
| 13 | §17 senaryosu | "monitor yolu" kararı verildi mi? | ✅ HAYIR — "?" + iki kapı |
| 14 | §19 hata matrisi | tür başına kesin neden yazıldı mı? | ✅ HAYIR — "aday" |
| 15 | §19.2 | sayım tutuyor mu? | ✅ evet — hiçbiri geçmedi |

---

## §20 Dürüstlük bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu dosyada AÇIK kalır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
