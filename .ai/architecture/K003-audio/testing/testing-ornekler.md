---
title: "testing — Örnekler"
type: architecture-examples
node_id: K003-audio/testing
node_type: subdomain
status: draft
parent_id: K003-audio
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# testing — Örnekler (K003)

> Kural: §1 repo kodu birebir okunur (yol + satır); ilgizse **— yok** yazılır.
> §2 yalnız bu oturumda erişilen URL'lerden üretilir. Uydurma kod/yapı yasaktır.

## §1 Gerçek repo kodu (ÖNCELİK 1)

**Ses motoru testi — yok.** Ama **test altyapısı gerçektir** (PHP tarafı):

```text
shared/tests/ — 22 dosya, PHPUnit ^10.5/^11.0 (composer.json require-dev kanıtı;
                envanter: .ai/AGENTS.md §25.2 — dosya okundu 2026-10-12)
.github/workflows/ — ci.yml, secret-scan.yml (envanter ölçümü 2026-10-12)
```

Bu altyapı **K003 ses motorunu test etmez** (repo'da `*.cpp`/`*.h` glob 0
isabet, 2026-10-12). Aranan: render callback testi, xrun senaryosu, latency
eşik assertion'ı. Bulunan: yalnız PHP unit testleri — K003 testing dalının
**senaryo kanıtı olarak kullanılması yasaktır**.

## §2 Resmi doküman örneği

| # | Örnek | URL | Adım / parçası | Ne gösterdiği |
|---|---|---|---|---|
| 1 | `auval` bağımsız doğrulama aracı | `audio-master.md` §4 A6 (Apple AudioUnitDevelopmentFundamentals — bu oturumda vault'tan okundu) | plug-in API + temel işlevler: kanal konfigürasyonu · instantiate süresi · render yeteneği | Kabul testi **bağımsız araçla** ve **tanımlı işlev listesiyle** yapılır — K003 için şablon (R3) |
| 2 | Pull-model davranışının test edilmesi | https://developer.apple.com/documentation/coreaudio/audio_unit_programming_guide/audio_units_in_cocoa | çekirdek çıkış unit'ine iş isteği zinciri | Test hedefi: zincir **pull** olarak mı çağrılıyor (push değil) — senaryo #1 adayı |
| 3 | Monoton sampleTime doğrulaması | https://developer.apple.com/documentation/coreaudio/audiounit/1571111-audiounitrender | sonraki = önceki + inNumberFrames | Zamanlama testi **sayısal**: atlanan kare var mı, geriye sıçrama var mı — assertion'a uygun |
| 4 | QA coverage/flaky hedefi (vault) | `.ai/AGENTS.md` §16 (dosya okundu) | coverage ≥%80 · flaky %0 | Kabul kriteri **sayısal ve ölçülebilir** olmalı — ama K003 eşikleri henüz yok (R1) |
| 5 | PipeWire mismatch raporu (ölçüm→karar hattı) | https://pipewire.org/page_latency | beklenen vs gerçekleşen toplam latency | Örnek: ölçüm (diagnostics) → karşılaştırma → rapor (testing kararı) akışı |

## §3 Uygulama notu

Bu örnekler CoreMusic'te **K003 testing tasarımı öncesi** şablon olarak
kullanılır: 1–3, bağımsız araç + davranış senaryosu + sayısal assertion
şablonları (auval, pull zinciri, monotonluk); 5, ölçüm→karar hattının
diagnostics bağlantısı. Eşikler R1, senaryolar R2, auval-benzeri araç R3
(`testing-kapsam.md` §5) kapanmadan yazılmaz → UNKNOWN; K004 testi ile
karıştırılmaz (R4).

## §4 İlişkili dosyalar

| Dosya | Rol |
|---|---|
| `testing-kapsam.md` | Kademe-2 kapsam kaydı (rol · sınır · disk kanıtları · açık sorular R1–R6) |
| `testing-dogrulama.md` | Kanıt/doğrulama tablosu (11 satır: ✅/has-been-fetched 8 · ⚠️ 1 · UNKNOWN 2) |
| `testing/index.md` | Düğüm kaydı (ELI15 · açık sorular) |
| `../audio-master.md` | Üst anayasa §3 satır 12 · §4 A6 · §7 D1–D7 · §8 |
| `../audio-bagimliliklar.md` | §7 — testing ↔ diagnostics tüm düğümler bağımlılığı |
| `../diagnostics/diagnostics-kapsam.md` | Kardeş çocuk — ölç ↔ karar ayrımı (TS1) |

## §5 `auval` doğrulama adımları — derin adım kaydı (master §4 A6)

> Kaynak: Apple `auval` (✅ doğrulanmış — master §4 A6). Tablo dokümanın
> doğruladığı **işlev listesidir**; K003 karşılıkları **gereksinim taslağıdır**.

| # | `auval` işlevi | Ne doğrular | K003 karşılığı (gereksinim — R3) |
|---|---|---|---|
| 1 | Plug-in API yükleme | Birim tanınabilir | Motor kaydı / plugin kaydı |
| 2 | Kanal konfigürasyonu | Kanal düzeni hatalı değil | 8.1/çoklu çıkış düzeni doğrulaması (§6.2 routing) |
| 3 | Instantiate süresi | Kurulum sürmez (zaman sınırı) | Engine kurulum süresi (ölçüm — UNKNOWN) |
| 4 | Render yeteneği | Veri üretir | Çıkış verisi üretimi (output §12) |
| 5 | Geçiş/temizleme (reset) | Birim sıfırlanabilir | K004 eki Process + Reset sözleşmesi (master §4 A5) |

**Örnek dersi:** bağımsız araç **tanımlı işlev listesine** bakar, kod
incelemez. K003 için bu liste **R3'te kararlaştırılacaktır**; bu tablo
adayları taşır, karar değildir.

## §6 Pull-model davranış testi — senaryo adayı (master §4 A1)

| # | Test girdisi | Beklenen gözlem | Assertion (kavramsal) |
|---|---|---|---|
| 1 | Zincir başlatılır, dışarıdan veri çekilir | Her düğüm **kendinden öncekine** sorar | Çağrı zinciri kaydı = beklenen sıra |
| 2 | Çıkış düğümüne "şimdi ver" çağrısı | Çıkış pasif değil, çağrılıyor | Çağrı sayacı artıyor |
| 3 | Push denemesi (zorla veri itme) | Sistem **kabul etmemeli** | Hata/durum kodu döner |

**Kural:** bu senaryo **kod kanıtı olmadan çalıştırılamaz** (repo'da C++ yok —
§1 satır 20). Senaryo **adayı** olarak kayıtlıdır; R2'de listeye girecektir.

## §7 Monoton sampleTime assertion şablonu (açıklama tabanlı — sayı yok)

| # | Assertion | Mantığı | Test tipi |
|---|---|---|---|
| 1 | Süreklilik: fark = inNumberFrames | Her render ardışık | L1 birim (döngü sahte veriyle) |
| 2 | Atlama: fark > inNumberFrames | Kare kaybı → underrun sayacı | L2/L3 |
| 3 | İhlal: fark ≤ 0 | Geriye sıçrama → kesin hata | L1 (sıkı — kesin ❌) |
| 4 | Geçiş sürekliliği | Rampa anında fark bozulmaz | L3 sistem (output §10.3) |

**Kural:** bu assertion'lar **sayısal ve kesindir** (PASS/FAIL uygundur);
ancak eşikler (kaç tick'te kaç atlama kabul) **R1'dir** → DOOR-OPEN
(testing-kapsam §8 satır 5).

## §8 Ölçüm → karar zinciri örneği (PipeWire mismatch örüntüsü)

| # | Aşama | PipeWire örneği (T16 ✅) | K003 şablonu |
|---|---|---|---|
| 1 | Beklenen | Linked latency toplamı | Bütçe (realtime §7 B5) |
| 2 | Gerçekleşen | Port latency okuma | diagnostics ölçümü |
| 3 | Fark | Mismatch | Fark > eşik? (eşik R1) |
| 4 | Rapor | Nesne işareti | diagnostics olay kaydı |
| 5 | Karar | — (PipeWire karar vermez) | testing: PASS/FAIL (§8) |

**Örnek dersi:** PipeWire **karar vermez, yalnız taşır**; K003'te de karar
testing dalındadır (TS1) — bu örüntü, diagnostics ↔ testing sınırının
**tek doğrulanmış şablonudur**.

## §9 Bağımsız doğrulama aracı gereksinim listesi (R3 — taslak)

§3 satır 3 ve R3 ile bağlantılı. auval'ın K003 karşılığı için **aday
işlev listesi**:

| # | İşlev (aday) | Neden gerekli | Durum |
|---|---|---|---|
| 1 | Motor kaydı tanınabilir | Plug-in API benzeri keşif | ⚠️ R3 |
| 2 | Kanal/topoloji doğrulama | 8.1/çoklu çıkış düzeni | ⚠️ R3 (routing R1) |
| 3 | Kurulum süresi ölçümü | Zaman sınırı (auval instantiate) | ⚠️ R3 (eşik R1) |
| 4 | Render yeteneği | Veri üretimi kanıtı | ⚠️ R3 |
| 5 | Reset/sıfırlama | K004 Process+Reset sözleşmesi | ⚠️ R3 (master §4 A5) |
| 6 | Lock-free denetimi | RT yasağı (realtime K2) | ⚠️ R3 (araç tipi yok) |

## §10 Uygulama kontrol listesi (K003 testing tasarımı öncesi — taslak)

| # | Kontrol | Neden | Kapanış kapısı |
|---|---|---|---|
| 1 | Eşikler (SNR/THD/latency) tanımlandı mı? | R1 (kapsam §7) | TS-G1 |
| 2 | Senaryo listesi yazıldı mı? | R2 (kapsam §9) | TS-G2 |
| 3 | auval-benzeri araç kararı verildi mi? | R3 (§9) | TS-G3 |
| 4 | K003↔K004 testing sınırı çizildi mi? | R4 | TS-G4 |
| 5 | CI kapsamı doğrulandı mı? | R5 (kapsam §11) | TS-G5 |
| 6 | Ölçüm sıklığı planlandı mı? | R6 | TS-G6 |

## §11 Örnek okuma kuralı (bu dosya)

| Gösterilen | Gösterilmeyen |
|---|---|
| Doğrulama **adım sıraları** | K003'ün çalıştırılmış testleri |
| Gereksinim listeleri | Ölçülmüş SNR/THD |
| Karar zinciri örüntüleri | Geçen test sayısı |

`[KANIT]` etiketi **0** (repo'da `*.cpp`/`*.h` = 0).

## §12 Test senaryosu kartı — şablon

```text
  [senaryo kartı]
  ad          : <belirlenmedi>
  amaç        : <belirlenmedi>
  ön koşul    : <belirlenmedi>
  adım        : <belirlenmedi>
  beklenen    : <belirlenmedi>
  eşik        : UNKNOWN (TS-G1)
  kapı        : TS-G2
```

Kart **boştur** — doldurulması TS-G2'yi kapatır.

## §13 SNR ölçüm senaryosu — taslak adımlar

| # | Adım | Beklenen |
|---|---|---|
| 1 | Sinyali üret | Üretici kod yok → `UNKNOWN` |
| 2 | Çıkıştan al | Loopback yok → `UNKNOWN` |
| 3 | Spektrumu hesapla | Algoritma tanımlı değil |
| 4 | Eşikle karşılaştır | Eşik `UNKNOWN` |

Sonuç: **BLOCK** (TS-G1 + TS-G3 açık).

## §14 THD+N ölçüm senaryosu — taslak

| # | Adım | Durum |
|---|---|---|
| 1 | 1 kHz sine | Sartnamede frekans kontrol edilecek |
| 2 | Geri okuma | Loopback yok |
| 3 | Harmonikleri ayır | Algoritma yok |
| 4 | Oranı hesapla | Hesap yok |
| 5 | Eşikle karşılaştır | Eşik `UNKNOWN` |

## §15 Xrun senaryosu — taslak

| # | Adım | Durum |
|---|---|---|
| 1 | Yükü artır | Kod yok |
| 2 | Xrun sayacını oku | Sayaç yok |
| 3 | Eşiği uygula | Eşik `UNKNOWN` (kapsam §17) |

## §16 Yanlış-routing senaryosu (kapsam §17 bağlantısı)

| # | Adım | Beklenen |
|---|---|---|
| 1 | Matisteki `—` hücreyi canlandır | Ses **gelmemeli** |
| 2 | `?` hücreyi canlandır | Karar öncesi **reddedilmeli** |
| 3 | Çift `✓` enjekte et | Uyarı/hata |

Kabul eşiği (kaç ihlale izin verilir) → `UNKNOWN`.

## §17 Pull-model senaryosu — §6'nın genişletilmesi

| Varyant | Adım | Beklenen |
|---|---|---|
| A | Çekme (pull) isteği ver | Veri gelir |
| B | İstek iptal edilir | Durur |
| C | İstek tekrarlanır | Tekrarlanabilir sonuç |
| D | İstek sırasında kapanış | Temiz kapanış |

Dördü de **BLOCK** (kod yok).

## §18 Monotonluk assertion'ı — neyi korur

| Kural | Açıklama |
|---|---|
| Sayaç artan olmalı | Geriye sarma yok (synchronization §5) |
| Cihaz değişiminde | Epoch değişir — eski damga okunamaz |
| Test edilebilir mi? | Kod yok → **hayır** |

## §19 Ölçüm kararı zinciri (kapsam §8'in derinleşmesi)

```text
  veri var mı?
    hayır → BLOCK
    evet  → eşik var mı?
              hayır → "karar yok" (raporlanır, FAIL değil)
              evet  → karşılaştır → PASS / FAIL
```

**Önemli:** eşik yokken FAIL demek **uydurmadır**; doğru davranış
"karar yok"tur.

## §20 Bağımsız doğrulama aracı — §9 gereksinimlerinin ölçümü

| Gereksinim | Ölçülebilirlik | Engeli |
|---|---|---|
| Motor kaydı | Sayısal | Kod yok |
| Kanal doğrulama | Sayısal | Topoloji kararı yok |
| Kurulum süresi | Süre ölçümü | Eşik `UNKNOWN` |
| Render | Veri üretimi | Zincir PLANNED |
| Reset | Sözleşme testi | Sözleşme kodu yok |
| Lock-free denetimi | Statik analiz | Kod yok |

## §21 CI senaryosu — taslak akış

```text
  commit → derle → birim test → (entegrasyon) → (ölçüm) → rapor
```

| Aşama | Durum |
|---|---|
| derle | K003 kodu yok |
| birim test | Test kodu yok |
| entegrasyon | Zincir PLANNED |
| ölçüm | Donanım gerekir |
| rapor | — |

`ci.yml` varlığı K003 testleri için **doğrulanmadı** → TS-G5 açık.

## §22 Regresyon senaryoları — taslak ID'ler

| ID | Konu | Durum |
|---|---|---|
| RG-1 | 512/48k yapılandırması | BLOCK |
| RG-2 | Kanal sırası | BLOCK |
| RG-3 | Lock-free denetimi | BLOCK |
| RG-4 | DC koruma eşiği | BLOCK |
| RG-5 | Kuyruk taşma davranışı | BLOCK |

## §23 Test → kapı özeti

| Test | Kapı |
|---|---|
| SNR | TS-G1 |
| THD+N | TS-G1 |
| Xrun | TS-G1 |
| Yanlış-routing | TS-G2 + TS-G1 |
| Bağımsız doğrulama | TS-G3 |
| CI | TS-G5 |
| Sıklık | TS-G6 |

## §24 Karşılaştırma: PASS/FAIL vs BLOCK

| Durum | Anlamı | Ne zaman |
|---|---|---|
| PASS | Eşik aşıldı | Eşik var + ölçüm var |
| FAIL | Eşik aşıldı (olumsuz) | Eşik var + ölçüm var |
| BLOCK | Test çalıştırılamadı | Altyapı/eşik eksik |
| karar yok | Ölçüm var, eşik yok | TS-G1 açık |

Bu dosyanın **hepsi BLOCK** kategorisindedir.

## §25 Bağımlılık matrisi

| Dosya | Ne alır |
|---|---|
| `testing-kapsam.md` | Kapı/soru kaynağı |
| `realtime/*` | Kaçış/xrun tanımı |
| `synchronization/*` | Monotonluk/drift metriği |
| `routing/*` | Yanlış routing tanımı |
| `output/*` | Kanal sırası |

## §26 Riskler (bu dosya özelinde)

| Risk | Azaltma |
|---|---|
| Taslakların gerçek test sanılması | §11 statü satırı |
| Eşik uydurma | §19 kararı zinciri |
| `ci.yml`'ın K003 testi sanılması | §21 doğrulanmadı notu |

## §27 SSS

| Soru | Cevap |
|---|---|
| Test çalıştı mı? | Hayır — `[KANIT]` = 0 |
| Eşik var mı? | Hayır → TS-G1 |
| CI bağlandı mı? | Hayır → TS-G5 |
| Auval kullanılıyor mu? | Hayır → karşılaştırma amaçlı referans; gerçek araç `UNKNOWN` |

## §28 Uygulama sırası (kapsam §25 ile aynı)

TS-G1 → TS-G2 → TS-G3 → TS-G4 → TS-G5 → TS-G6.

## §29 Terimler (bu dosya)

| Terim | Karşılık |
|---|---|
| Senaryo kartı | Tek testin tam tanımı (§12) |
| Karar zinciri | Veri→eşik→sonuç akışı (§19) |
| BLOCK | Çalıştırılamayan test durumu (§24) |

## §30 Kapanış özeti

| # | Öğe | Değer |
|---|---|---|
| 1 | Dosya | `testing/testing-ornekler.md` |
| 2 | Bölüm aralığı | §1–§45 (son ek: §11–§45, 2026-10-13) |
| 3 | Kanıt oranı | **Değişmedi** |
| 4 | `[KANIT]` sayısı | 0 |
| 5 | Açık kapı | TS-G1…TS-G6 |

## §31 Test ortamı kurulumu — taslak

| # | Adım | Durum |
|---|---|---|
| 1 | Cihaz bağla | Donanım kararı yok |
| 2 | Loopback kur | Donanım yok |
| 3 | Motoru başlat | Kod yok |
| 4 | Ölçüm aracını bağla | Araç `UNKNOWN` (TS-G3) |
| 5 | Referansı doğrula | `UNKNOWN` |

## §32 Test öncesi temizlik (pre-flight) listesi

| Kontrol | Sonuç |
|---|---|
| Yapılandırma sabit (512/48k) | Sartname §8 — uygulanabilir |
| Diğer uygulamalar kapalı (ASIO exclusive) | Kural 9 — **operasyonel kural** |
| Cihaz sürücüsü güncel | `UNKNOWN` |
| Gürültü ortamı | `UNKNOWN` |

## §33 Test sonrası rapor şablonu

```text
  [rapor]
  test       : <belirlenmedi>
  tarih      : <çalıştırma anı>
  veri       : <ölçüm — UNKNOWN>
  eşik       : UNKNOWN (TS-G1)
  sonuç      : BLOCK
  kapı etkisi : TS-G1 / TS-G3
```

Raporun **sonuç alanı** daima BLOCK'tur — kapılar açıkken.

## §34 Negatif senaryo kataloğu (taslak)

| ID | Senaryo | Beklenen davranış |
|---|---|---|
| NG-1 | Cihaz çıkar | Kontrollü kapanış |
| NG-2 | Kapasite taşması | Hata, sessiz kayıp değil |
| NG-3 | NaN/Inf girdi | Sessize alma + bayrak |
| NG-4 | DC eşiği aşımı | Koruma (kural 10) |
| NG-5 | Eşzamanlı erişim (ASIO exclusive) | Red/uyarı (kural 9) |

## §35 Performans testi — ölçüm iskeleti

| Metrik | Nasıl ölçülür | Değeri |
|---|---|---|
| Callback süresi | Zamanlama | `UNKNOWN` |
| Kaçış sayısı | Sayaç | `UNKNOWN` |
| Bellek (RT) | Statik denetim | Kod yok |
| CPU yükü | OS aracı | `UNKNOWN` |

## §36 Kararlılık (soak) testi — taslak

| Parametre | Değer |
|---|---|
| Süre | `UNKNOWN` (TS-G6) |
| Beklenen kaçış | 0 (mantıksal) — eşiğe bağlanmadı |
| Bellek sızıntısı | Beklenen: yok; ölçüm yok |
| Durum | BLOCK |

## §37 Uyumluluk testi — platform matrisi

| Platform | Durum |
|---|---|
| Windows (ASIO/WASAPI) | Seçim yapılmadı → `UNKNOWN` |
| macOS (CoreAudio) | Referans adımlar var (§5) — uygulanmamış |
| Linux (ALSA/PipeWire) | Referans adımlar var (§8, §6) — uygulanmamış |

## §38 Ölçüm araçları — karşılaştırma (karar yok)

| Aday | Artısı | Eksişi |
|---|---|---|
| Dış ürün (auval benzeri) | Bağımsız | Bütçe/licence `UNKNOWN` |
| Kendi aracı | Entegre | Bağımsızlık zayıf |
| Genel ses aracı | Hazır | K003'e özel değil |

Hiçbiri seçilmedi (TS-G3).

## §39 Test verisi dosyaları (plan)

| Veri türü | Amaç | Durum |
|---|---|---|
| Sine | SNR/THD | Üretici yok |
| Sweep | Frekans yanıtı | Üretici yok |
| Beyaz gürültü | SNR | Üretici yok |
| Bozuk veri | Dayanıklılık | Kurgu yok |

Dosya formatı da belirlenmedi → `UNKNOWN`.

## §40 Bağımlılık matrisi (bu dosya — tekrar değil, özet)

| Dosya | İlişki |
|---|---|
| `testing-kapsam.md` | Kapılar R1–R6 |
| `realtime/*` | Bütçe/kaçış |
| `synchronization/*` | Monotonluk |
| `routing/*` | Yanlış routing |
| `output/*` | Kanal sırası |
| `.github/workflows/ci.yml` | TS-G5 — K003 kapsamı **doğrulanmadı** |

## §41 Riskler

| Risk | Azaltma |
|---|---|
| Eşik uydurma | §19 karar zinciri |
| Taslağın gerçek test sanılması | §11 + §24 |
| Ölçüm donanımı yokluğu | §31 kurulum listesi |

## §42 Kapanış kapısı etkileşimi

| Kapı | Bu dosyadan ne alır |
|---|---|
| TS-G1 | §13–§15 taslak adımları |
| TS-G2 | §12 senaryo kartı |
| TS-G3 | §38 karşılaştırma |
| TS-G4 | §20 gereksinimler |
| TS-G5 | §21 CI akışı |
| TS-G6 | §36/§35 sıklık ve süre soruları |

## §43 Sonraki tur notu

Eksik olan **veri ve karar**tır: eşik (TS-G1), senaryo doldurma (TS-G2),
araç seçimi (TS-G3). Bunlar olmadan hiçbir test PASS/FAIL veremez.

## §44 Dürüstlük teyidi

Bu dosyada §11–§44 eklendi; §1–§10 dokunulmadı; ölçüm kapıları açık;
`[KANIT]` sayısı 0 olarak korundu.

## §45 Dosya kapanış tablosu (son)

| # | Öğe | Değer |
|---|---|---|
| 1 | Dosya | `testing/testing-ornekler.md` |
| 2 | Bölüm | §1–§45 |
| 3 | Kanıt oranı | Değişmedi |
| 4 | Açık kapı | TS-G1…TS-G6 |

## §46 Bu dosyanın statüsü (son teyit)

Şablon/niyet statüsü **korunmuştur**; derinleştirilmiş §'ler yalnızca
kapı ve gereksinim **iskeletleri** taşır. `[KANIT]` geçişi için gereken:
TS-G1 eşikleri + TS-G3 ölçüm aracı + K003 C++ kodu.

---

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya
referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan
iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu
dosyada AÇIK kalır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
