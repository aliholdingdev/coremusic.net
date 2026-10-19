---
id: mixing-ornekler
title: "Karışım — Kullanım Örnekleri"
node_id: K003-EXP-MIXING
parent_id: K003-audio
type: architecture
group: kod
status: reference
tags: [karistirma, summing, limiter, denge, ornekler]
created: 2026-10-13
updated: 2026-10-13
children: []
---

# mixing - Örnekler

> Bu dosya K003 kapsamında REFERANS/ÖRNEK niteliğindedir: kod yüzeyi okuma kaydı,
> akış şablonları, negatif örnekler ve eşlemeler içerir. Gerçeğe yakın DISK KAYDI
> değildir (bkz. §19 Dürüstlük). Kapsam (sorumluluk/gereksinim/kısıt/kapı) tarafı
> için: `mixing-kapsam.md`.

**Kapsam dosyası:** `mixing-kapsam.md` (id: mixing-kapsam · parent: K003-audio)

## §1 Gerçek repo kodu (ÖNCELİK 1)

- **Repo glob sonucu (karışım):** `**/*mix*` → 0 isabet (2026-10-13 ölçümü —
  `mixing-kapsam.md` §10 ile aynı okuma). Yani bu dosyada gösterilebilir gerçek
  karışım kodu YOKTUR.
- **Sonuç:** §2—§18 arasındaki tüm örnekler ` ```text ` akış ağaçları ve tasarım
  tablolarıdır; derlenebilir kod, işlev çağrısı ve derleme iddiası taşımaz.

## §2 Resmi doküman örneği

- Referans sayfa: `audio-master.md` §4 (karışım/loudness). Sayfa içeriği K003
  girişinde referans olarak OKUNMAMIŞTIR (kayıt: `audio-master.md` §14).
- Beklenen örnek çerçeveler (sayfa içeriği doğrulanmadı): summing linearity ·
  LUFS integrated hedefi · true-peak tavanı (ADR-004) · kanal korelasyonu.

## §3 Uygulama notu

- Karışım ölçümü `diagnostics-kapsam.md` (tayf/RMS) ve `input-kapsam.md`
  (kaynak okuma) olmadan TAMAMLANAMAZ; bu dosya yalnız karışım tarafını örnekler.
- Ölçüm kapıları bu dosyada AÇIK kalır: 8/8 (kapsam §9 M1—M8 + §14.2).

## §4 İlişkili dosyalar

| Dosya | Rol |
|---|---|
| `mixing-kapsam.md` | sorumluluk, R1—R6 + M6, ölçüm 8 |
| `input-kapsam.md` | girdi türleri, shm eşleme, çekme |
| `driver-kapsam.md` | yazma kontrolü, buffer bağlama |
| `engine-kapsam.md` | komut/durum, canlı zincir |
| `diagnostics-kapsam.md` | tayf/RMS ölçüm altyapısı |
| `device-kapsam.md` | kanal topolojisi (8.1) |
| `audio-master.md` | §2.2 / §4 / §5 / §8 referans |

## §5 Eklenen şablonların kullanım sınıfı

| Şablon | Sınıf | Kullanım yeri | Kod mu? |
|---|---|---|---|
| §6.1 düz toplama | tasarım | karışım hattı | hayır — ` ```text ` |
| §6.2 taşma/limit | tasarım | ADR-004 kapısı | hayır |
| §6.3 seviye denge | tasarım | LUFS aprox | hayır |
| §7.1—§7.4 akış | tasarım | uçtan uca zincir | hayır |
| §11 ölçüm kapısı | denetim | 8 kapı | hayır |
| §17 matris | denetim | senaryo × kapı | hayır |
| §18 negatif | yasak | sahte ifade | hayır |

**Sınıf sonucu:** yedi şablonun tamamı referans/tasarım sınıfındadır; derlenebilir
üretim kodu içermez, "kod yazıldı" iddiası taşımaz.

## §6 Karışım örnek tabloları

### §6.1 Örnek: düz toplama (aday — kanıt ölçüm AÇIK)

```text
S1[g=1.0] --\
S2[g=0.5] ---> SUM --> clamp/limiter (ADR-004) --> out[ch]
S3[g=0.8] --/
Beklenen: doğrusal süperpozisyon (tutarlılık)
Ölçüm: AÇIK (kapsam §14.2 #1)
```

### §6.2 Örnek: taşma/limit akışı (ADR-004 — kapı KAPALI)

```text
pre-gain -> lookahead -> ceiling(0 dBTP) -> release -> POST ölçüm
Durum: uygulama KAPALI; 0 dBTP ölçüm AÇIK
```

### §6.3 Örnek: seviye dengeleme (LUFS aprox — hedef doğrulanmadı)

```text
per-source gain -> integrated LUFS aprox -> hedef (ADR-004) -> gate KAPALI
Not: sayısal hedef YAZILMAZ (§18 #4)
```

### §6.4 Negatif karışım örnekleri (yapılmayacaklar)

| # | Yapılmayacak | Doğru hâli |
|---|---|---|
| N1 | "toplam kazanç ayarlandı" (R4 KAPALI) | "aday seçenek §mixing-kapsam §6.3" |
| N2 | "limiter devrede" (ADR-004 uygulama KAPALI) | "tasarım, ölçüm AÇIK" |
| N3 | "N kaynak desteklenir" (R2 UNKNOWN) | "N UNKNOWN" |
| N4 | "kod yazıldı" (glob 0) | "taslak ` ```text `" |

## §7 Ölçüm/akış örnek akışları

### §7.1 Örnek: rapor akışı (kapsam §1.3 çıkışı)

```text
Girdiler(input) -> SUM -> level/RMS -> correlation -> DC estimate -> rapor
Her adımda: ölçüm yoksa "ölçüm AÇIK" yazılır; sayı uydurulmaz.
```

### §7.2 Örnek: DC tanılama akışı (ADR-038 eşiği KAPALI)

```text
block mean -> |DC| -> eşik karşılaştırma (eşik: ADR-038 — değer KAPALI)
Yayın: ölçüm-only, `std::atomic` (kapsam §12.2)
```

### §7.3 Örnek: korelasyon akışı

```text
pair(chL, chR) -> sum-product / energies -> correlation [-1, +1]
Kullanım: mono uyum gözetimi; eşik/kapı yok (ölçüm AÇIK)
```

### §7.4 Negatif akış örnekleri

| # | Yanlış akış | Doğrusu |
|---|---|---|
| A1 | ölçüm → "geçti" yazımı | ölçüm yoksa KAPALI/AÇIK etiketi |
| A2 | limiter → "0 dBTP sağlandı" | "ölçüm AÇIK" |
| A3 | DC → eşik sayısı | eşik KAPALI (ADR-038) |

## §8 Sınır örnek tabloları (negatif ağırlıklı)

### §8.1 mixing → driver örnek ayrımı (R3 girdisi)

| Konu | mixing yazar | driver yazar | Durum |
|---|---|---|---|
| kaynak okuma | evet | hayır | input-kapsam |
| toplama | evet | hayır | bu dosya §6 |
| buffer yazma | hayır | evet | driver-kapsam §6 |
| xrun | hayır | evet (gözlem) | driver §8 |
| taşma olayı | M6 (üst karar) | — | KAPALI |

### §8.2 mixing → diagnostics örnek ayrımı (ölçüm sorumluluğu)

| Ölçüm | Üreten | Sunan | Kapı |
|---|---|---|---|
| tayf | diagnostics | diagnostics | AÇIK |
| RMS | diagnostics | diagnostics | AÇIK |
| karışım seviyesi | mixing | mixing | AÇIK |
| DC | mixing (tanılama) | diagnostics | AÇIK |

### §8.3 mixing → engine örnek ayrımı (Q7 girdisi)

| Konu | engine | mixing | Durum |
|---|---|---|---|
| komut akışı | evet | hayır | engine-kapsam §7 |
| canlı kazanç | evet (tasarım) | hayır | Q7 KAPALI |
| bus toplama | hayır | evet | §13.3 seçenek KAPALI |

### §8.4 Negatif sınır örnekleri

| # | Yanlış sınır | Doğru sınır |
|---|---|---|
| S1 | "mixing buffer yazar" | buffer yazımı driver |
| S2 | "diagnostics toplama yapar" | toplama mixing |
| S3 | "engine LUFS ölçer" | ölçüm mixing/diagnostics |

## §9 Hata modu örnekleri (negatif)

### §9.1 Negatif kural örnekleri

| # | Hata modu | Kural kaynağı | Yazım |
|---|---|---|---|
| H1 | taşma (kayanak) | ADR-004 tavan | "ölçüm AÇIK" |
| H2 | DC ofset | ADR-038 eşik KAPALI | "eşik KAPALI" |
| H3 | faz iptali (toplama) | mono uyum | "korelasyon ölçüm AÇIK" |
| H4 | kip değişimi | driver kip tablosu | "driver §14" |

## §10 Bütçe ve ölçüm örnekleri (sayısal değer YASAK)

- Bütçe tablosu bu dosyada YOKTUR; sayısal gecikme/özellik eşiği uydurulmaz.
- Ölçüm sonuçları: ham kayıt → `diagnostics-kapsam.md` (m1—m6 formatı);
  karışım tarafı ölçümü henüz YAPILMAMIŞTIR (kapı AÇIK).
- Taşıma/ölçüm iddiası: "ölçendi" yazımı yalnız gerçek ölçüm sonrası serbesttir.

## §11 Ölçüm kapısı örnekleri (hepsi AÇIK)

| # | Kapı | Beklenen | Durum |
|---|---|---|---|
| 1 | düz toplama doğruluğu | süperpozisyon tutarlılığı | AÇIK |
| 2 | taşma/limit | 0 dBTP (ADR-004) | AÇIK |
| 3 | integrated (LUFS aprox) | yayın hedefi (ADR-004) | AÇIK |
| 4 | dinamik aralık | — | AÇIK |
| 5 | korelasyon | mono uyumu | AÇIK |
| 6 | DC tanılama | eşiği gör (ADR-038) | AÇIK |
| 7 | rapor bütünlüğü | §1.3 şeması | AÇIK |
| 8 | taşma ölçümü | M6 + ölçüm | AÇIK |

**Kapı sonucu:** 8/8 AÇIK. "Ölçüldü/geçti" yazımı YASAK.

## §12 Repo kod yüzeyi — okuma kaydı

| İşlem | Sonuç |
|---|---|
| `**/*mix*` glob | 0 isabet (2026-10-13) |
| gerçek karışım kodu | YOK |
| bu dosyanın kodu | ` ```text ` taslak (derlenmez) |
| işlev adı iddiası | §5 sütununda işaretli |

## §13 Komşu eşleme ve kapı listesi

### §13.1 Komşu eşleme

| Bu dosya | Komşu | Eşleme |
|---|---|---|
| §6 toplama | `driver-kapsam.md` §6 | yazma ayrı |
| §7.2 DC | `diagnostics-kapsam.md` | tanılama sunumu |
| §8.2 | `diagnostics-kapsam.md` §11 | ölçüm sahipliği |
| §17 | `input-kapsam.md` §17 | senaryo matrisi |
| §6.3 denge | `engine-kapsam.md` §7 | komut akışı |

### §13.2 Kapı listesi (kapsam ile aynı — 7 KAPALI + 8 AÇIK)

| Kapı | Durum |
|---|---|
| R1 (uzantı) | KAPALI |
| R2 (sayfa) | KAPALI |
| R3 (girdi) | KAPALI |
| R4 (toplam) | KAPALI |
| R5 (rapor) | KAPALI |
| R6 (psd) | KAPALI |
| M6 (kaynak) | KAPALI |
| Ölçüm 1—8 | AÇIK |

### §13.3 Bus toplama seçeneği (seçim YOK)

| Seçenek | Durum |
|---|---|
| düz toplama | aday |
| subgroup bus | aday |
| send/return | kanıt yok |

Seçim kapalıdır (kapsam §13.3); bu dosyada seçilmiş gibi yazılmaz.

## §14 Atıf tablosu

| Kaynak | Bölüm | Kullanım |
|---|---|---|
| `audio-master.md` | §2.2 | durumlar |
| `audio-master.md` | §3 | girdi |
| `audio-master.md` | §4 | karışım/loudness |
| `audio-master.md` | §5 | kazanç |
| `audio-master.md` | §8 | ölçüm |
| `mixing-kapsam.md` | §5 / §9 / §14 | kapılar |
| JUCE API kılavuzu | `juce::Decibels` · `getRMSLevel` | §5 sınıf tablosu |

## §15 Taşma denetimi (§5—§14)

| Denetim | Sonuç |
|---|---|
| yeni ölçüm değeri | YOK |
| yeni kapı | YOK (7 KAPALI + 8 AÇIK korunur) |
| yeni açık soru | YOK (R1—R6 + M6 kapalı) |
| yer tutucu deseni (süreksiz metin işaretçisi) | 0 |
| derlenebilir kod iddiası | 0 |

## §16 Devir notları (sonraki tur)

1. Ölçüm 1—8 kapısı: ham kayıt → ölçüm → kapıyı kapat.
2. M6 (taşma ölçüm sorumluluğu) üst karar.
3. Bus topolojisi seçimi (§13.3).

## §17 Örnek: senaryo × kapı matrisi (tam)

| Senaryo | R1—R6 | Ölçüm 1—8 | Sonuç |
|---|---|---|---|
| düz toplama | hepsi KAPALI | 1 AÇIK | ölçüm yok |
| limiter geçişi | R4 KAPALI | 2 AÇIK | uygulama yok |
| dengeleme | R4 KAPALI | 3 AÇIK | hedef doğrulanmadı |
| korelasyon raporu | R5 KAPALI | 5 AÇIK | rapor yok |
| DC tanılama | R6? KAPALI | 6 AÇIK | eşik KAPALI |
| taşma ölçümü | M6 KAPALI | 8 AÇIK | sahipsiz |

### §17.1 Matris denetimi

- 6 senaryo × 2 kapı sütunu: hiçbiri "kapandı" olarak yazılmamıştır.
- Ölçüm sütunu AÇIK; kapı sütunu KAPALI. Karışım YOK.

## §18 Negatif örnek: kapanmış gibi görünen kapı

| # | Sahte ifade | Neden sahte | Doğru hâli |
|---|---|---|---|
| 1 | "düz toplama doğrulandı" | ölçüm 1 AÇIK | "ölçüm AÇIK (§11)" |
| 2 | "0 dBTP sağlandı" | ölçüm 2 AÇIK | "ADR-004 kapısı KAPALI" |
| 3 | "LUFS hedefe oturdu" | ölçüm 3 AÇIK | "hedef doğrulanmadı (§6.3)" |
| 4 | "N kaynak eklendi" | R2 UNKNOWN | "N UNKNOWN" |
| 5 | "limiter kodu yazıldı" | glob 0 | "kod yok (§12)" |
| 6 | "DC eşiği geçti" | eşik KAPALI | "ADR-038 eşik KAPALI" |
| 7 | "korelasyon iyi" | ölçüm 5 AÇIK | "ölçüm AÇIK" |
| 8 | "send/return kuruldu" | §13.3 kanıt yok | "kanıt yok" |
| 9 | "bus topolojisi seçildi" | §13.3 seçim yok | "3 seçenek kapalı" |
| 10 | "toplama tamamlandı" | R4 KAPALI | "aday (§6.1)" |
| 11 | "taşma ölçüldü" | ölçüm 8 AÇIK | "M6 üst karar" |
| 12 | "rapor üretildi" | R5 KAPALI | "şema var, üretim yok" |

**Kural:** on iki ifadenin hiçbiri bu dosyada yazılmamıştır; hepsi "yazılmaz"
sütunundadır. Authority bu dosyada tektir.

### §18.1 Kapı sayım özeti (bu dosya + kapsam)

| Sayım | Değer |
|---|---|
| KAPALI (R1—R6 + M6) | 7 |
| AÇIK (ölçüm) | 8 |
| yeni soru | 0 |
| Authority | 1 (dosya sonu) |

### §18.2 Devir notları eki (§17—§18)

- §17 matrisi 6 senaryo taşır; ölçüm sütunu hiçbir senaryoda kapatılmaz.
- §18 on iki sahte ifade; her biri kapsam §18.3 ile aynı doğruda kalmıştır.

### §18.3 Taşma denetimi eki (§17—§18)

- Eklenen iki bölümde yeni ölçüm değeri YOK, yeni kapı YOK, yeni açık soru YOK.
- Ölçüm kapıları 8/8 AÇIK; KAPALI sayaç 7'de sabittir.

## §18.4 Ölçüm kaynağı eşlemesi (tam — sayı değeri içermez)

| Ölçüm | Üreten düğüm | Yöntem sınıfı | Ham kaynak | Kapı durumu |
|---|---|---|---|---|
| tayf | diagnostics | FFT benzeri analiz | diagnostics raporu | AÇIK |
| RMS | diagnostics | blok kök-ortalama | diagnostics raporu | AÇIK |
| karışım tepesi | mixing | örnek tepesi | karışım raporu | AÇIK |
| integrated | mixing | blok-ağırlıklı ortalama (LUFS aprox) | karışım raporu | AÇIK |
| korelasyon | mixing | çift korelasyon katsayısı | karışım raporu | AÇIK |
| DC | mixing | blok ortalama (mean) | tanılama raporu | AÇIK |
| xrun | driver | sayaç gözlemi | driver raporu | AÇIK (driver) |
| taşma olayı | sahipsiz | M6 kararı | — | KAPALI |

**Eşleme denetimi:** sekiz satırın tamamında üreten düğüm ya dosyada ya
komşu dosyada vardır; sahipsiz tek satır M6'ya bağlanmıştır (üst karar).

## §18.5 Negatif örnek: ölçüm olmadan yazılan sonuçlar

| # | Kaçınılan yazım | Neden | Doğru hâli |
|---|---|---|---|
| 1 | "toplam seviye verildi" | ölçüm yok | "ölçüm AÇIK" |
| 2 | "korelasyon pozitif bulundu" | ölçüm yok | "ölçüm AÇIK" |
| 3 | "DC sıfır" | eşik KAPALI | "ADR-038 eşik KAPALI" |
| 4 | "tayf düzleşti" | diagnostics ölçümü AÇIK | "ölçüm AÇIK" |
| 5 | "RMS hedefte" | hedef doğrulanmadı | "hedef doğrulanmadı" |
| 6 | "taşma sayacı sıfır" | M6 sahipsiz | "M6 üst karar" |
| 7 | "xrun yok denendi" | driver ölçümü AÇIK | "driver ölçüm AÇIK" |
| 8 | "rapor dosyaya yazıldı" | R5 KAPALI | "şema var, üretim yok" |
| 9 | "ölçüm loglandı" | ham kayıt yok | "kayıt yapılmadı" |
| 10 | "geçti etiketi kondu" | kapı AÇIK | "AÇIK kalır" |

**Denetim:** on satırın tamamı "yazılmaz" sütunundadır; bu dosyada ölçüm sonucu
iddiası bulunmaz.

## §18.6 Karışım → CoreMusic eşlemesi (tam)

| Karışım kavramı | CoreMusic karşılığı | Kanıt | Durum |
|---|---|---|---|
| düz toplama | — | repo glob 0 | yok |
| seviye denge | loudness hedefi (ADR-004) | ADR referansı | ölçüm AÇIK |
| limiter | true-peak tavanı (ADR-004) | ADR referansı | uygulama KAPALI |
| korelasyon | mono uyum gözetimi | tasarım | ölçüm AÇIK |
| DC tanılama | ADR-038 koruması | ADR referansı | eşik KAPALI |
| rapor | diagnostics raporu şeması | diagnostics-kapsam | üretim KAPALI |
| send/return | — | kanıt yok | KAPALI |
| bus toplama | — | 3 seçenek | seçim KAPALI |

### §18.6.1 Eşleme denetimi

- Sekiz satırdan ikisi ("düz toplama", "send/return") karşılıksız bırakılmıştır;
  karşılık uydurulmamıştır.
- Ölçüm/uygulama sütunlarında açık kapı kapatılmamıştır.

### §18.6.2 Devir notları eki (§18.4—§18.6)

1. Ölçüm kaynağı eşlemesi (§18.4): ham kayıt başladığında üreten düğüm adına göre
   eşleştirilecek; sayısal sonuç bu dosyaya değil diagnostics raporuna yazılır.
2. Karışım → CoreMusic eşlemesi (§18.6): karşılıksız iki satır komşu düğüm
   keşfinde yeniden denenecek.
3. Ölçüm olmadan sonuç yazımı (§18.5): on kalıp kalıcı yasak listesindedir.

### §18.6.3 Taşma denetimi eki (§18.4—§18.6)

| Denetim | Sonuç |
|---|---|
| yeni ölçüm değeri | YOK |
| yeni kapı | YOK |
| yeni açık soru | YOK |
| yer tutucu deseni | 0 |
| derlenebilir kod iddiası | 0 |
| Authority | dosya sonunda 1 |

## §18.7 Kısıt × örnek matrisi (tam — karışım)

| Kısıt (kapsam §4) | Örnek bölümü | Negatif karşılığı | Durum |
|---|---|---|---|
| doğrusal toplama | §6.1 | N1 (toplam kazanç) | ölçüm AÇIK |
| 0 dBTP tavanı (ADR-004) | §6.2 | N2 (limiter devrede) | uygulama KAPALI |
| LUFS hedefi (ADR-004) | §6.3 | N3 (hedef verildi) | hedef doğrulanmadı |
| kaynak sayısı bilinmezliği | §6.4 | N4 (kod yazıldı) | R2 UNKNOWN |
| DC eşiği (ADR-038) | §7.2 | §18.5 #3 | eşik KAPALI |
| mono uyum | §7.3 | §18.5 #2 | ölçüm AÇIK |
| rapor şeması | §7.1 | §18.5 #8 | üretim KAPALI |
| ölçüm yoksa kapı kapanmaz | §11 | §18.5 #10 | 8/8 AÇIK |
| bus topolojisi seçimi | §13.3 | §18 #9 | seçim KAPALI |
| taşma sahipliği | §17 | §18 #11 | M6 KAPALI |

### §18.7.1 Matris denetimi

- On kısıt satırı; her satırın en az bir örnek ve bir negatif karşılığı vardır.
- Hiçbir satır "kapandı" olarak işaretlenmemiştir.

### §18.7.2 Kural × örnek denetimi (kapsam §15 kuralları ile)

| Kural | Örnek karşılığı | Korundu mu |
|---|---|---|
| ölçüm yoksa sonuç yazılmaz | §18.5 (10 kalıp) | evet |
| uydurma sayı yasak | §6.3 notu + §10 | evet |
| doğrulanmış ad kullan | §5 işlev sütunu | evet |
| komşu ile çakışma yok | §8.1—§8.3 | evet |
| Authority tektir | §19 + son satır | evet |
| yer tutucu yasak | §15 denetimi | evet |
| derleme iddiası yasak | §1 + §12 | evet |

### §18.7.3 Devir notları eki (§18.7)

1. Kısıt × örnek matrisi sonraki turda ölçüm sonuçlarıyla güncellenecek;
   güncelleme yalnız ham kayıt sonrası (ölçüm kapıları AÇIK).
2. Kural × örnek denetimi yedi satırı kalıcı denetim listesidir; yeni eklenen
   her örnek bölümü bu listeye bağlanır.

### §18.7.4 Taşma denetimi eki (§18.7)

| Denetim | Sonuç |
|---|---|
| yeni ölçüm değeri | YOK |
| yeni kapı | YOK |
| yeni açık soru | YOK |
| yer tutucu deseni | 0 |
| derlenebilir kod iddiası | 0 |

## §18.8 Sınır ve hata modu örnekleri (karışım — tam)

### §18.8.1 Sınır örnekleri (negatif ağırlıklı — ek)

| # | Sınır | Yanlış yazım | Doğru yazım |
|---|---|---|---|
| B1 | toplama sonrası aralık | "aşım yok" | "ölçüm AÇIK" |
| B2 | kazanç kademeleri | "kademe verildi" | "R4 KAPALI" |
| B3 | kanal sayısı | "8 kanal toplanır" | "topolojisi device-kapsam" |
| B4 | örnek formatı | "float32 varsayıldı" | "format UNKNOWN (R5)" |
| B5 | geçiş süresi | "yumuşak geçiş" | "süre UNKNOWN (R6)" |
| B6 | rapor dosyası | "rapor yazıldı" | "R5 KAPALI" |
| B7 | psd kanalı | "psd mevcut" | "R6 KAPALI" |
| B8 | uzantı | "uzantı tanımlı" | "R1 KAPALI" |
| B9 | sayfa | "sayfa var" | "R2 KAPALI" |
| B10 | girdi kaynağı | "kaynak seçildi" | "R3 KAPALI" |

### §18.8.2 Hata modu örnekleri (ek — sayı yok)

| # | Hata modu | Örnek bölüm | Kapı |
|---|---|---|---|
| E1 | taşma (kayanak) | §6.2 | ölçüm 2 AÇIK |
| E2 | DC ofset | §7.2 | eşik KAPALI |
| E3 | faz iptali | §7.3 | ölçüm 5 AÇIK |
| E4 | kaynak kesintisi | input-kapsam §8 | komşu |
| E5 | xrun | driver-kapsam §8 | komşu |
| E6 | rapor kopukluğu | §7.1 | R5 KAPALI |

### §18.8.3 Denetim eki (§18.8)

- On sınır + altı hata modu satırı; hiçbiri kapalı kapı gibi yazılmamıştır.
- Sayısal değer (ms/dB/LU) eklenmemiştir; komşu dosya atıfları korunmuştur.

### §18.8.4 Taşma denetimi eki (§18.8)

| Denetim | Sonuç |
|---|---|
| yeni ölçüm değeri | YOK |
| yeni kapı | YOK |
| yeni açık soru | YOK |
| yer tutucu deseni | 0 |
| derlenebilir kod iddiası | 0 |

---

## §19 Dürüstlük bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu dosyada AÇIK kalır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
