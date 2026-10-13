---
title: "engine — Örnekler"
type: architecture-examples
node_id: K003-audio/engine
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
date: 2026-10-12
updated: 2026-10-13
group: kod
---

# engine — Örnekler

> Kural: §1 repo kodu birebir okunur (yol + satır); ilgizse **— yok** yazılır.
> §2 yalnız bu oturumda erişilen URL'lerden üretilir. Uydurma kod/yapı yasaktır.

## §1 Gerçek repo kodu (ÖNCELİK 1)

**— yok.**

`shared/src/`, `shared/tests/` ve `.github/workflows/` içinde oynatma motoru ana
döngüsü/y yaşam döngüsü kodu bulunmaz (2026-10-12 kök `*.cpp`/`*.h` glob 0
isabet). Dikkat notu: `shared/src/AI/AIEngine.php` adında "AIEngine" sınıfı
vardır; bu sınıf bir **AI öneri/analiz motorudur, ses motoru değildir** —
`extractFeatures()` yalnız dosya özelliklerini çıkarır, gerçek zamanlı döngü
içermez. engine düğümü kanıtı olarak **kullanılamaz**.

İlgili tek gerçek repo kodu (yan sınır — engine DEĞİL, eşik/bant tablosu):

```php
// shared/src/AI/AIEngine.php : 300-339
// ffmpeg ile metadata çıkışı + extractFeatures() placeholder
// + bandToFrequency() ISO 31-band EQ (20 Hz - 20 kHz)
```

Bu blok `equalizer` (K004) içindir; engine'in döngü kanıtı sayılmaz.

## §2 Resmi doküman örneği

| # | Örnek | URL | Adım / parçası | Ne gösterdiği |
|---|---|---|---|---|
| 1 | Core Audio pull-model zinciri | https://developer.apple.com/documentation/coreaudio/audio_unit_programming_guide/audio_units_in_cocoa | Çıkış unit'i veriye "ister"; çekirdek çıkış unit'ine iş ister | Motor döngüsü **pull** olarak kurgulanır: baştaki düğüm çağrıldıkça kendinden sonrakine sorar (push değil) |
| 2 | `AudioUnitRender` çekirdek işlevi | https://developer.apple.com/documentation/coreaudio/audiounit/1571111-audiounitrender | `AudioUnitRender` çağrı imzası (inRenderTime · inNumberFrames · ioData) | Tek bir render çağrısı = tek bir döngü adımı; engine'in "tik" tanımı budur |
| 3 | Giriş unit'i önbellek davranışı | https://developer.apple.com/documentation/coreaudio/audio_unit_programming_guide/audio_units_in_cocoa | "iş parçacığındaki önbellekten örnek alır" | Kaynak tarafı da aynı döngüde önbellekten okur → engine ↔ backend sahipliği (Q5) burada netleşmeli |
| 4 | iOS'ta unit üretimi zamanlaması | https://developer.apple.com/documentation/coreaudio/audio_unit_hosting_guide/producing_audio_units_in_an_ios_application | "ilk normal oynatma iş parçacığı başladığında" | Yaşam döngüsü **geç** oluşturulabilir; create/start ayrımı zorunlu |
| 5 | Core Audio API kapsamı | https://developer.apple.com/documentation/coreaudio/core_audio_overview/core_audio_api_reference | sürücü · format · codec · sesun listesi | engine, alt katmanı (driver/backend) yalnız bu arayüz üzerinden çağırır |

### §2.1 Fetch durumu notu (şeffaflık)

Tablodaki 5 satır 4 benzersiz URL'den üretilmiştir; hepsi bu oturumda GET
edilip içerik başlığı doğrulanmıştır — bu yüzden `has-been-fetched
2026-10-12` taşırlar. AUGraphNewNode/AUGraphInitialize/AUGraphStart
adımları (#1'e değil, `audio-master.md` §4 A3'e dayanır) bu turda kaynak
dokümandan yeniden açılmadı; örnekte "AUGraph ile node ekle" tarzı kesin
adım numarası bu yüzden yazılmaz.

### §2.2 Örnek satırlarının iddia sınıfı

| Satır | İddia sınıfı | Dayanak |
|---|---|---|
| 1–3 | `[TASARIM]` öncesi döngü/tik şablonu | doğrulanmış URL |
| 4 | `[TASARIM]` öncesi yaşam döngüsü faz şablonu | doğrulanmış URL |
| 5 | Alt katman sınırının resmi tanımı | doğrulanmış URL |
| Hepsi | Kesin uygulama iddiası DEĞİL | `UNKNOWN` korunur (durum kümesi · thread önceliği · veri sahipliği) |

## §3 Uygulama notu

Bu örnekler CoreMusic'te **engine tasarım kararı öncesi** kullanılır: 1–3, kendi
döngü/tik tanımı ve veri sahipliği (Q5) için şablon; 4, yaşam döngüsü fazlarını
(create → initialize → start) ayırma kanıtı; 5, alt katman sınırının resmi
tanımıdır. Kesin uygulama (durum kümesi, thread modeli, veri sahipliği)
R1/R2/R3 (`engine-kapsam.md` §5) kapanmadan yazılmaz — sayı/parametre uydurmak
yerine `UNKNOWN` bırakılır.

## §4 İlişkili dosyalar

| Dosya | Rol |
|---|---|
| `engine-kapsam.md` | Kademe-2 kapsam kaydı (rol · sınır · disk kanıtları · açık sorular) |
| `engine-dogrulama.md` | Kanıt/doğrulama tablosu (✅ · ⚠️ · UNKNOWN) |
| `engine/index.md` | Düğüm kaydı (ELI15 · açık sorular Q5/Q7) |
| `../audio-master.md` | Üst anayasa §3 satır 1 (engine satırı) · §4 A1–A6 · §8 Q5/Q7 |
| `../audio-bagimliliklar.md` | §2/§6 — backend ↔ engine örtüşme riski |
| `../backend/backend-kapsam.md` | E1 sınırının karşı tarafı (Q5) |

---

## §5 Eklenen şablonların kullanım sınıfı

| # | Şablon (kapsam §) | Sınıf | Kullanım yeri | Kullanım YASAĞI |
|---|---|---|---|---|
| 1 | §6.1 yaşamlık döngüsü L1–L10 | aday şablon | tasarım öncesi tartışma | bağlayıcı aşama listesi değil |
| 2 | §6.2 durum kümesi S1–S9 | **aday** | Q7 (R3) girdisi | kesin küme değil |
| 3 | §6.3 geçiş tablosu | aday | R5 girdisi | tetikleyici yazmadan kullanılamaz |
| 4 | §7.1 komut listesi | şablon | UI ↔ motor sözleşmesi tartışması | UI'nın komutu getirdiği yer değil |
| 5 | §7.2 uçtan uca akış | şablon | R6 girdisi | sıra sahibi atanmadan iddia değil |
| 6 | §7.3 tik adımı | şablon | realtime tartışması | süre/öncelik yazılmaz |
| 7 | §8.1–8.3 sınır tabloları | girdi | Q5/E2 tartışması | cevap DEĞİL |
| 8 | §9 hata modları | aday | diagnostics girdisi | kök neden kesin değil |
| 9 | §13.1 latans kalemleri | şablon | bütçe tartışması | ms/pay YOK |
| 10 | §16.1 komut×durum×hata | aday matris | R3/R5 girdisi | "?" hücreleri kesin değil |

**Ortak kural:** hiçbir şablon "budur" demez; hepsi **girdi tablosu**dur ve
kapılar (§10) açık kalır.

## §6 Yaşam döngüsü örnek akışları

### §6.1 Örnek: ilk açılış (doğrulanmış adımlarla)

```text
create (§2 satır 4 — fetch edilmiş)
  → addNode… (§2.1 uyarısı: adım numarası bu turda açılmadı, yazılmaz)
    → initialize (§2 satır 4)
      → start (§2 satır 4)
        → ilk normal oynatma iş parçacığı (§2 satır 4 — "geç" oluşum kanıtı)
          → tik'ler başlar (§2 satır 2 — AudioUnitRender)
```

### §6.2 Örnek: duraklat–devam döngüsü (aday)

| # | Adım | Durum (kapsam §6.2) | Komut | Kanıt sınıfı |
|---|---|---|---|---|
| 1 | çalışıyor | S4 | — | şablon |
| 2 | duraklat alır | S5 | §7.1 #3 | şablon |
| 3 | tik durur mu? | S5 içinde | — | ⚠️ UNKNOWN (R5) |
| 4 | devam alır | S4 | §7.1 #3 | şablon |
| 5 | tik devam eder | S4 | — | şablon |

### §6.3 Örnek: hata → yeniden kurulum (aday)

```text
S4 CALISIYOR
  → adım hatası (§9 satır 5)
    → S6 HATA
      → yeniden kurulum isteği (§6.3 satır 9)
        → S8 YENIDEN_KURULUYOR
          → başarı → S3 · başarısız → S6 (döngü riski §9 satır 7)
            → kapı: R3 (küme) + R5 (geçiş) KAPALI
```

### §6.4 Negatif yaşam döngüsü örnekleri (yapılmayacaklar)

| # | Yapılmayacak | Neden | Kapı |
|---|---|---|---|
| 1 | "AAudio 5 durum alındı" yazmak | Q7 açık | R3 |
| 2 | durum geçişini "asenkron" diye yazmak | R5 UNKNOWN | R5 |
| 3 | start'a süre (ms) eklemek | ölçüm yok | ölçüm |
| 4 | S8 döngüsüne deneme sayısı yazmak | eşik UNKNOWN | — |
| 5 | pull model'i "CoreMusic'te uygulandı" demek | kod yok (glob 0) | — |

## §7 Komut ve yürütme örnek akışları

### §7.1 Örnek: oynat komutunun akışı (şablon)

```text
uygulama katmanı (dışarıda — §2 satır 4)
  → motor komut yorumlar (§7.1 #1)
    → durum kontrolü (§16.1 satır 1: S3 → S4)
      → L4 start / L5 tik adımı
        → §7.2 zincir: kaynak → K004 → mix → çıkış
          → monoton sayaç (§4 #9 — fetch edilmiş)
```

### §7.2 Örnek: pull model'e göre tek tik (doğrulanmış §2 satır 1–3)

| # | Adım | Örneğin gösterdiği | CoreMusic'te durumu |
|---|---|---|---|
| 1 | çıkış "ister" | pull yönü | ⚠️ tasarım öncesi şablon |
| 2 | çekirdek işler | render çağrısı | tek tik tanımı (§7.3) |
| 3 | giriş önbellekten okur | aynı döngüde | Q5 girdisi (R1) |
| 4 | çerçeve tamam → sonraki | zincir devri | sıra sahipliği R6 |
| 5 | hata | — | §9 (aday) |

### §7.3 Örnek: tik adımının negatifleri

| # | Negatif | Sonuç (aday) | Sahip |
|---|---|---|---|
| 1 | tik içinde tahsis | sıfır-tahsis ihlali (E2) | realtime |
| 2 | tik içinde lock | yasak (E2) | realtime |
| 3 | tik içinde log | log bu dalda değil (§8.2) | diagnostics |
| 4 | tik içinde DSP kodu | kod girmez (§8.3) | K004 |
| 5 | tik süresine sayı | UNKNOWN | ölçüm |

## §8 Sınır örnek tabloları (negatif ağırlıklı)

### §8.1 engine ↔ backend örnek ayrımı (Q6/Q5 girdisi)

| # | Örnek soru | engine şablonu | backend şablonu | Karar |
|---|---|---|---|---|
| 1 | buffer kimde? | okur/yazar (nihai) | allocate eder (aday) | ⚠️ R1 |
| 2 | kuyruk kimde? | komut kuyruğu okur | veri kuyruğu? | ⚠️ R1 |
| 3 | döngüyü kim başlatır? | start komutu | — | ⚠️ R1 |
| 4 | hata kime gider? | üretir | — | diagnostics (E5) |

### §8.2 engine ↔ K004 örnek ayrımı (✅ kural kanıtlı)

| # | Örnek | engine | K004 | Kural |
|---|---|---|---|---|
| 1 | EQ parametresi | iletir | uygular | kod girmez |
| 2 | sinyal çerçevesi | taşır | işler | sinyal gider |
| 3 | reverb hesabı | — | evet | — |
| 4 | düğüm ekleme | kurar (R4) | tanım verir | ⚠️ R4 |

### §8.3 Negatif sınır örnekleri

| # | Yapılmayacak | Neden |
|---|---|---|
| 1 | "backend sadece buffer sağlar" yazmak | Q5 açık |
| 2 | "engine realtime kurallarını yazar" | E2 — kurallar realtime'de |
| 3 | K004 içine motor kodu koymak | §4 #5 ihlali |
| 4 | diagnostics'e tik süresi atamak | ölçüm yok → UNKNOWN |

## §9 Hata modu örnekleri (negatif — kök neden kesin değil)

| # | Örnek senaryo | Belirti | Aday kök neden | Neden kesin değil |
|---|---|---|---|---|
| 1 | start sonrası ses yok | §9 satır 1 | L3/L4 ret | teşhis yok |
| 2 | tik durdu | §9 satır 2 | thread/plan | R2 UNKNOWN |
| 3 | tik düzensiz | §9 satır 3 | zamanlama | ölçüm yok |
| 4 | komut yutuldu | §9 satır 4 | kuyruk | R1 açık |
| 5 | zincir koptu | §9 satır 5 | düğüm | R4 açık |
| 6 | duraklat takıldı | §9 satır 6 | küme | R3 açık |
| 7 | kurulum döngüsü | §9 satır 7 | hata kökü | — |
| 8 | xrun | §9 satır 8 | adım süresi | ölçüm yok |
| 9 | cihaz ret | §9 satır 9 | driver/device | Q6 açık |
| 10 | çökme | §9 satır 10 | tahsis ihlali | — |
| 11 | DSP hatası yayıldı | §9 satır 11 | K004 | komşu |
| 12 | clock | §9 satır 12 | synchronization | komşu |

### §9.1 Negatif kural örnekleri

| # | Kural | Kaynak |
|---|---|---|
| 1 | hata koduna sınıf atanmaz (uydurulmaz) | diagnostics R3 |
| 2 | "çözüldü" yazısı ölçüm olmadan konmaz | — |
| 3 | xrun eşiği sayı ile yazılmaz | diagnostics R2 |
| 4 | kök neden "aday" olarak kalır | §9 başlığı |

## §10 Latans bütçesi örnekleri (sayısal değer YASAK)

| # | Kalem (kapsam §13.1) | Örnek gösterim | Yazılamayan |
|---|---|---|---|
| 1 | giriş tamponu | "vardır, ölçülür" | ms değeri |
| 2 | tik işleme | "vardır, ölçülür" | ms değeri |
| 3 | DSP kuyruğu | "var mı? → K004'e sor" | — |
| 4 | toplama | "vardır, ölçülür" | ms değeri |
| 5 | çıkış tamponu | "vardır, ölçülür" | ms değeri |
| 6 | sürücü/OS | "driver R5'e" | ms değeri |
| 7 | toplam | "bütçe sahibi yok → UNKNOWN" | hedef ms |

**Örnek yasak cümle:** "hedef 10 ms" — yazılması için bütçe sahibi kararı +
ölçüm gerekir; ikisi de yok.

## §11 Ölçüm kapısı örnekleri (hepsi AÇIK)

| # | Kapı (kapsam §16.2) | Örnek "geçti" iddiası | Gerçek durum |
|---|---|---|---|
| 1 | tik süresi | "ölçüldü" | ❌ yok — AÇIK |
| 2 | jitter | "kabul edilebilir" | ❌ ölçüm yok |
| 3 | komut gecikmesi | "anlık" | ❌ sayı yok |
| 4 | toplam latans | "bütçe içinde" | ❌ bütçe yok |
| 5 | xrun sıklığı | "sıfır" | ❌ ölçüm yok |
| 6 | S8 döngüsü | "gözlemlenmedi" | ❌ veri yok |
| 7 | CPU payı | "düşük" | ❌ ölçüm yok |
| 8 | sıfır-tahsis | "uygun" | ❌ denetim yok |

## §12 Repo kod yüzeyi — okuma kaydı

| # | Yüzey | Aranan | Bulunan | Sonuç |
|---|---|---|---|---|
| 1 | kök `*.cpp`/`*.h` | motor döngüsü | 0 isabet (2026-10-12) | kod yok |
| 2 | `shared/src/` | oynatma motoru | PHP katmanı | yok |
| 3 | `AIEngine.php` : 300–339 | motor kanıtı | AI özellikleri + EQ band | engine DEĞİL (§1) |
| 4 | `AIEngine.php` `extractFeatures()` | gerçek zamanlı döngü | dosya özelliği çıkarır | döngü yok |
| 5 | `shared/tests/` | motor testi | PHP testleri | yok |
| 6 | `.github/workflows/` | ses işi | — | yok |

## §13 Komşu eşleme ve kapı listesi

### §13.1 Komşu eşleme

| # | Şablon | Komşu dosya | Pay |
|---|---|---|---|
| 1 | §6.1 kurulum | `driver/driver-ornekler.md` §6 | komut zinciri |
| 2 | §7.2 pull akışı | `backend/` (Q5) | birlikte karar |
| 3 | §7.2 giriş önbelleği | `input/input-ornekler.md` | kaynak tarafı |
| 4 | §7.2 mix | `mixing/mixing-ornekler.md` | toplama |
| 5 | §7.2 çıkış | `output/output-ornekler.md` | yazma |
| 6 | §9 hata | `diagnostics/diagnostics-ornekler.md` | sınıf atama |
| 7 | §7.3 tik kuralları | `realtime/` | kurallar |
| 8 | §10 bütçe | `testing/` | kabul kriteri |
| 9 | §8.2 DSP | `../dsp/` (K004) | sınır kanıtlı |
| 10 | §6.2/§6.3 | Q7 (master §8) | açık soru |

### §13.2 Kapı listesi (hepsi KAPALI)

| # | Kapı | Soru | Durum |
|---|---|---|---|
| 1 | R1 | engine ↔ backend (Q5) | KAPALI |
| 2 | R2 | thread/süreç önceliği | KAPALI (UNKNOWN) |
| 3 | R3 | durum kümesi (Q7) | KAPALI |
| 4 | R4 | grafik API'si | KAPALI |
| 5 | R5 | geçiş senkron/asenkron | KAPALI (UNKNOWN) |
| 6 | R6 | yürütme sırası | KAPALI |
| 7 | ölçüm | tik süresi/bütçe | KAPALI (8/8 AÇIK) |
| 8 | kod | engine kodu | KAPALI (glob 0) |

## §14 Atıf tablosu

| # | Bölüm | Dayanak | Sınıf |
|---|---|---|---|
| 1 | §5 | kapsam §6–§16 başlıkları | iç kanıt |
| 2 | §6.1 | §2 satır 4 + §2.1 uyarısı | ✅ fetch + şeffaflık notu |
| 3 | §6.2/§6.3 | kapsam §6.2/§6.3 | aday şablon |
| 4 | §6.4 | §5 R3/R5 | açık soru |
| 5 | §7.1 | §2 satır 1–2 + §16.1 | ✅ URL + iç kanıt |
| 6 | §7.2 | §2 satır 1–3 | ✅ fetch edilmiş URL |
| 7 | §7.3 | kapsam §7.3 + E2 | şablon + sınır |
| 8 | §8 | kapsam §8.1–8.3 | girdi tablosu |
| 9 | §9 | kapsam §9 + diagnostics H-şeması | aday + komşu |
| 10 | §10 | kapsam §13 | şablon (sayı YASAK) |
| 11 | §11 | kapsam §16.2 + §4 #6 glob 0 | ölçüm kapısı |
| 12 | §12 | §1 ölçümü | ölçüm |
| 13 | §13 | kapsam §5 + komşu dosyalar | iç kanıt |
| 14 | §16 | §5 R1–R6 + kapsam §10 | açık soru + iç kanıt |
| 15 | §17 | kapsam §16.1 (üçlü matris) + §6 durumları | aday matris |
| 16 | §18 | §2 satır 1–5 (fetch edilmiş URL) | ✅ fetch |
| 17 | §19 | kapsam §16.2 (ölçüm yok) | boş şablon |
| 18 | §19.2 | kapsam §16.3 zinciri | şablon |
| 19 | §19.3 | kapsam §10 + §16.2 + bu dosya §13.2 | sayım |
| 20 | §19.4 | §19 boşluğu + R1–R6 | negatif örnek |

## §15 Taşma denetimi (§5–§14)

| # | Denetim | Sonuç |
|---|---|---|
| 1 | §6'da durum kümesi kesin mi? | ✅ HAYIR — aday |
| 2 | §7'de sıra/üretici atandı mı? | ✅ HAYIR — R6 |
| 3 | §8'de Q5 cevaplandı mı? | ✅ HAYIR — girdi |
| 4 | §9'da kök neden kesin mi? | ✅ HAYIR — "aday" |
| 5 | §10'da ms/pay yazıldı mı? | ✅ HAYIR — yasak sütunu |
| 6 | §11'de kapı "geçti" mi? | ✅ HAYIR — 8/8 AÇIK |
| 7 | §12'de kod uyduruldu mu? | ✅ HAYIR — ölçüm |
| 8 | §1–§4 değişikliği | ✅ hayır |
| 9 | yasaklı yer tutucu kalıbı | ✅ kullanılmadı |
| 10 | derlenebilir kod iddiası | ✅ yok (yalnız ```text/```php yol notu) |
| 11 | Authority tekliği | ✅ tek |

## §16 Devir notları (sonraki tur)

| # | Devir | Bekleyen | Sahip |
|---|---|---|---|
| 1 | Q5 sınırı | iki dosya kararı | Vault Steward (R1) |
| 2 | Q7 küme | AAudio şablonu | embedded-engineer (R3) |
| 3 | grafik API | düğüm ekleme | embedded-engineer (R4) |
| 4 | öncelik/ thread | sayı yok | embedded-engineer (R2) |
| 5 | geçiş asenkronluğu | tasarım | embedded-engineer (R5) |
| 6 | sıralama sahibi | R1 ile kilitli | Vault Steward (R6) |
| 7 | ölçüm kapısı 8/8 | ilk ölçüm | diagnostics + testing |
| 8 | günlük §19 ilk satır | ortam + tik süresi | ölçüm ekibi |
| 9 | §19.4 sahte kapılar | denetim turu | Vault Steward |

## §17 Komut × durum × hata örnek matrisi (tam — "?" = kapı)

| # | Komut/olay | Geçerli durum | Örnek tepki (şablon) | Hata (§9) | Kapı |
|---|---|---|---|---|---|
| 1 | oynat | S3 | L4/L5'e geç | 1 | — |
| 2 | oynat | S4 | yok say / idempotent | — | R5 |
| 3 | oynat | S7 | ? yeniden start yolu | 1 | R5 |
| 4 | oynat | S9 | ret (yok edilmiş) | 1 | — |
| 5 | dur | S4 | L8 → S7 | — | — |
| 6 | dur | S7 | yok say | — | R5 |
| 7 | dur | S2 | ? kurulumda ret | 1 | R5 |
| 8 | duraklat | S4 | S5 | 6 | — |
| 9 | duraklat | S5 | yok say | — | R5 |
| 10 | duraklat | S3 | ret (henüz yok) | 6 | — |
| 11 | devam | S5 | S4 | 6 | — |
| 12 | devam | S4 | yok say | — | R5 |
| 13 | ileri-al | S4/S5 | backend'e okuma | 4 | R1 |
| 14 | geri-al | S4/S5 | backend'e okuma | 4 | R1 |
| 15 | zincire ekle | S1/S2/S3 | L2 (R4) | 5 | R4 |
| 16 | zincire ekle | S4 | ? canlı ekleme | 5 | R5 |
| 17 | cihazda çalıştır | S3/S4 | niyet → device/driver | 9 | Q6 |
| 18 | kip/format değiştir | S3 | L3/L4 tekrarı | 9 | driver R1 |
| 19 | destroy | S7 | S9 | — | — |
| 20 | destroy | S4 | önce durdur mu? | 10 | R5 |
| 21 | (olay) hata | S4/S2 | S6 | 6, 7 | E5 |
| 22 | (olay) tik kaybı | S4 | politika yok → UNKNOWN | 2 | R2 |
| 23 | (olay) DSP hatası | S4 | sınır §8.3 → K004 | 11 | komşu |
| 24 | (olay) cihaz kaybı | S4 | niyet → driver/device | 9 | Q6 |
| 25 | (olay) clock | S4 | komşuya devir | 12 | komşu |

**Yasak:** "?" hücrelerine yanıt bu dosyada yazılmaz — hepsi R5/R4/R1/Q6
kapısına bakar; matris **girdidir, cevap değildir**.

## §18 Örnek: pull model → CoreMusic şablonu eşlemesi (tam)

| # | Doğrulanmış örneğin adımı (§2) | CoreMusic şablonundaki karşılığı | Dosya içi yer | Durum |
|---|---|---|---|---|
| 1 | çıkış unit'i veri "ister" | tik adımı pull yönünde kurgulanır | §7.3 | şablon |
| 2 | `AudioUnitRender(inRenderTime · inNumberFrames · ioData)` | tek tik = tek render çağrısı | §7.1–§7.2 | ✅ URL |
| 3 | giriş unit'i önbellekten okar | kaynak okuma adımı | §7.2 adım 1 | Q5 girdisi |
| 4 | çekirdek çıkışa iş ister | zincir devri (sıra) | §7.2 adım 2–5 | R6 açık |
| 5 | "ilk normal oynatma iş parçacığı" | create/start ayrımı | §6.1 L1 vs L4 | ✅ URL |
| 6 | API kapsamı: sürücü·format·codec·sesun | alt katman çağrısı sınırı | §8 | ✅ URL |
| 7 | AUGraph adımları | düğüm ekleme yaşam döngüsü | §6.1 L2 | ⚠️ adım numarası yazılmaz (§2.1) |
| 8 | render zamanı (`inRenderTime`) | tik zamanlaması | §13.2 | süre YOK |

### §18.1 Eşleme denetimi

| # | Denetim | Sonuç |
|---|---|---|
| 1 | eşleme CoreMusic'e "uygulandı" mi yazıldı? | ✅ HAYIR — şablon |
| 2 | AUGraph adım numarası uyduruldu mu? | ✅ HAYIR (§2.1) |
| 3 | render zamanına ms kondu mu? | ✅ HAYIR |
| 4 | Q5/R6 açık kapıları kapatıldı mı? | ✅ HAYIR |

---

## §19 Ölçüm günlüğü şablonu (boş — ilk satır bekleniyor)

| # | Alan | Değer | Not |
|---|---|---|---|
| 1 | tarih | — | doldurulmadı |
| 2 | ortam (OS/sürücü) | — | doldurulmadı |
| 3 | tik süresi | — | ölçüm PLANNED |
| 4 | jitter | — | ölçüm PLANNED |
| 5 | komut gecikmesi | — | ölçüm PLANNED |
| 6 | toplam latans | — | bütçe yok |
| 7 | xrun/dropout sayısı | — | eşik yok |
| 8 | CPU payı | — | ölçüm PLANNED |
| 9 | sıfır-tahsis sonucu | — | denetim yok |
| 10 | sonuç → kapı etkisi | — | §16.3 zinciri |

**Kural:** on hücre de boştur; doldurulduğunda ilgili kapıya (§16.2) yazılır
ve ilgili R güncellenir — **bu turda hiçbiri doldurulmamıştır** (gerçek
ölçüm yok).

### §19.1 Günlük denetimi

| # | Denetim | Sonuç |
|---|---|---|
| 1 | sahte ölçüm satırı var mı? | ✅ HAYIR — hepsi "—" |
| 2 | kapı "geçti" mi? | ✅ HAYIR — 8/8 AÇIK |
| 3 | Authority tekliği | ✅ tek |
| 4 | §1–§4 değişikliği | ✅ hayır |

## §19.2 Ölçüm → kapı → R dönüş haritası (örnek)

| # | Ölçüm kalemi | Hangi kapıya yazar? | Hangi R'yi besler? | Kapanış koşulu |
|---|---|---|---|---|
| 1 | tik süresi | §16.2 kapı 1 | R2 (thread/süreç) | yöntem + ilk kayıt |
| 2 | jitter | kapı 2 | R2 + realtime | yöntem + ilk kayıt |
| 3 | komut gecikmesi | kapı 3 | R5 (senkron/asenkron kanıtı) | tasarım kararı + ölçüm |
| 4 | toplam latans | kapı 4 | bütçe sahibi (yok → ⚠️ üst karar) | sahip atanmalı |
| 5 | xrun sıklığı | kapı 5 | R2 + driver R5 (birim) | birim kararı + ölçüm |
| 6 | S8 döngüsü | kapı 6 | R3 (durum kümesi gerçekliği) | küme kararı + veri |
| 7 | CPU payı | kapı 7 | R2 | yöntem + kayıt |
| 8 | sıfır-tahsis | kapı 8 | E2 (realtime) | denetim yöntemi |

**Döngü kuralı:** ölçüm → günlük (§19) → kapı → R. Bu dosyada döngü
**hiç başlatılmaz**; yalnızca yolu gösterilir.

## §19.3 Kapı sayım özeti (bu dosya + kapsam)

| # | Kaynak | Toplam kapı | Açık/KAPALI durumu |
|---|---|---|---|
| 1 | `engine-kapsam.md` §10 | 9 | 9/9 KAPALI |
| 2 | `engine-kapsam.md` §16.2 ölçüm | 8 | 8/8 AÇIK (ölçüm yok) |
| 3 | bu dosya §13.2 | 8 | 8/8 KAPALI |
| 4 | bu dosya §19 günlük | 10 hücre | boş (doldurulmadı) |
| 5 | §5 R1–R6 (kapsam) | 6 | 6/6 açık — devir §16 |

**Toplam:** 6 açık soru (R1–R6) + 17 uygulama kapısı KAPALI + 8 ölçüm kapısı
AÇIK — hiçbiri bu oturumda kapatılmadı.

## §19.4 Negatif örnek: kapanmış gibi görünen kapı

| # | Sahte ifade | Neden sahte | Doğru hâli |
|---|---|---|---|
| 1 | "ölçüm tamamlandı" | günlük boş (§19) | "ölçüm PLANNED" |
| 2 | "durum kümesi belirlendi" | R3 açık | "aday küme (§6.2)" |
| 3 | "backend sınırı net" | R1 açık | "girdi tablosu (§8.1)" |
| 4 | "latans hedefi tuttu" | bütçe yok | "UNKNOWN" |
| 5 | "xrun eşiği aşılmadı" | eşik yok | "eşik yazılmaz" |
| 6 | "geçişler asenkron tasarlandı" | R5 UNKNOWN | "yönü gösterilir (§6.3)" |
| 7 | "grafik API'si seçildi" | R4 açık | "aday değil, kapıda" |
| 8 | "thread önceliği verildi" | R2 UNKNOWN | "UNKNOWN" |

---

## §20 Dürüstlük bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu dosyada AÇIK kalır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
