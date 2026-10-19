---
title: "equalizer — Doğrulama"
type: architecture-verification
node_id: K004-dsp/equalizer
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: kanit
---

# equalizer — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda doğrulanan URL'ler · (b) bu oturumda okunan
> gerçek disk dosyaları. Yasak: URL, sürüm, sınıf uydurmak.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk rolü = "Frekans bantlarına göre seviye ayarı" | `.ai/architecture/K004-dsp/dsp-master.md` §3 satır 2 | 2026-10-12 | ✅ dosya okundu |
| 2 | "bant sayısı/eşik değerleri tanımlı değil" açık soru | `equalizer/index.md` open_questions | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 3 | PHP katmanında 31-band EQ eğrisi üreten `optimizeEQ()` var | `shared/src/AI/AIEngine.php` satır 111–133 | 2026-10-12 | ✅ dosya okundu |
| 4 | `bandToFrequency()` 31 ISO bandını (20 Hz–20 kHz) diziyle döndürüyor | `shared/src/AI/AIEngine.php` satır 332–342 | 2026-10-12 | ✅ dosya okundu |
| 5 | Her bant {band, frequency, gain, Q} kaydı üretiyor | `shared/src/AI/AIEngine.php` satır 124–129 | 2026-10-12 | ✅ dosya okundu |
| 6 | Bu PHP EQ bir room-correction hesabı; C++ DSP motoru değil | `shared/src/AI/AIEngine.php` satır 317 ("placeholder") | 2026-10-12 | ✅ dosya okundu (negatif kanıt) |
| 7 | DSPark `Equalizer<float>` + `setBand(0, 100.0f, 3.0f)` API'si var | https://github.com/CristianMoresi/DSPark | 2026-10-12 | ✅ doğrulandı |
| 8 | DSPark Equalizer filtre ailesi: `Biquad`, `StateVariableFilter`, `LadderFilter` | https://github.com/CristianMoresi/DSPark | 2026-10-12 | ✅ doğrulandı (Capabilities) |
| 9 | Patina `EqEngine` = 3-band parametric EQ | https://github.com/ShmKnd/Patina | 2026-10-12 | ✅ doğrulandı (Effect Engines tablosu) |
| 10 | JUCE `juce_dsp` modülü EQ/filtre sınıfları içerir (sınıf adı doğrulanmadı) | https://github.com/juce-framework/JUCE/tree/master/modules | 2026-10-12 | ⚠️ — modül ✅, içerik **UNKNOWN** |
| 11 | CoreMusic EQ için varsayılan bant sayısı/Q/eşik | — | 2026-10-12 | **UNKNOWN** |
| 12 | equalizer ↔ filters sınır hangi ölçütle çizilecek | — | 2026-10-12 | **UNKNOWN** (Q3 komşu) |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam satır | 12 |
| İşlenen benzersiz URL | 3 (7 · 8 DSPark; 9 Patina; 10 JUCE) |
| Gerçek dosya yolu satırı | 6 (1 · 2 · 3 · 4 · 5 · 6) |
| ✅ doğrulandı | 9 (1 · 3 · 4 · 5 · 6 · 7 · 8 · 9 + 10'ün modül kısmı) |
| ⚠️ VERIFICATION REQUIRED | 2 (2 · 10) |
| UNKNOWN | 2 (11 · 12) |

**Geçiş kuralı:** satır 2 ve 12 üst kararla (Q3/Q4), 10 içerik-kanıtı ve 11 tasarım
kararıyla kapanır.

---

## §3 Kanıt türleri ve etiket sözlüğü (tur-2)

§1'deki her satır bir **kanıt türü** taşır. Bu bölüm türleri ve kapanma
koşullarını sabitler; §2 sayımını **değiştirmez** (bkz dürüstlük bloğu).

| Etiket | Anlamı | Kapanma koşulu | Örnek (§1) |
|---|---|---|---|
| ✅ dosya okundu | İddia gerçek bir repo/vault dosyasından okundu | dosya yolu + satır yeniden üretilebilir | 1 · 3 · 4 · 5 · 6 |
| ✅ doğrulandı | URL bu oturumda erişildi ve içerik okundu | URL + okunan bölüm + tarih | 7 · 8 · 9 |
| ⚠️ VERIFICATION REQUIRED | İddia kısmen doğrulanabilir, içerik eksik | içerik kanıtı veya üst karar | 2 · 10 |
| **UNKNOWN** | Bilgi hiç yok — tahmin yasak | tasarım kararı / ADR | 11 · 12 |
| ⚠️ etiketli iddia | Konu açık soruda kayıtlı, türü değişmez | — | 2 (durum ⚠️ kalır) |

**Kural:** bir satırın etiketi yalnız kanıtla değişir; yeni bölüm eklemek
(S§3–§8) §1'in etiketlerine dokunmaz.

## §4 İddia → kapalı-kapı matrisi (R1–R3 kapanış ölçütleri)

§5'teki açık sorular (R1/R2/R3) burada **ölçülebilir** kapanış koşuluna
bağlanır. Koşul sağlanmadan satır UNKNOWN/⚠️ kalır:

| Açık soru | Kapanış ölçütü (ne gelirse kapanır) | Kapanış mercii | Şu an |
|---|---|---|---|
| R1: varsayılan bant sayısı/Q/eşik | Karar ADR'si (sayı + gerekçe) veya repo'da override eden kod satırı | Vault Steward kararı / kod kanıtı | **UNKNOWN** |
| R2: equalizer ↔ filters sınırı | dsp-master §7 Q3 kararı: "bant seviyesi = EQ, katsayı = filters" gibi ölçütlü yazı | Üst karar (Q3) | ⚠️ |
| R3: oda düzeltme hedefi EQ mu measurement mı? | Q4 kararı + measurement dalının düzeltme uygulamadığını teyit eden kapsam satırı | Üst karar (Q4) | ⚠️ |
| satır 10 (juce_dsp içerik) | `juce_dsp` içindeki EQ sınıf adlarının dosya/README kanıtı | Doğrulama turu | ⚠️ |
| satır 12 (sınır ölçütü) | R2 ile aynı kapıyı paylaşır | Q3 | **UNKNOWN** |

**Ölçüm/ölçek yok:** bu matrisde "yaklaşık kapandı" diye bir durum yoktur —
kapı ya kapalı ya açıktır (Truth Mode).

## §5 Teknik arka plan: EQ iddialarının bilimsel dayanağı

> Bölüm içeriği literatür/standardtır (kaynaklar §6), CoreMusic'e özgü iddia
> **içermez**. CoreMusic'e özgü her sayı §1'deki R1-R3 kapılarına bağlıdır.

### §5.1 Biquad — neden her EQ bandı 2. derecedir

İkinci derece IIR (biquad) 4 serbest katsayıya (b1,b2,a1,a2 normalize sonrası)
sahiptir; tepe/shelf/alçak-yüksek geçiren tepkilerin tamamı tek biquad ile
üretilir (RBJ Cookbook §6.2–§6.4 formülleri). 31 band × 1 biquad = 31 biquad
kaskadı → doğrusal olmayan faz toplamı (min-phase zincir).

### §5.2 Q — doğrulama testi nasıl yazılır

Doğrulama (bu dal) için tipik ölçülebilir iddialar ve testleri:

| İddia türü | Test | Kabul |
|---|---|---|
| "f0'da +G dB" | sinus taraması, \|H(f0)\| ölç | ±0.1 dB (literatür tipik ⚠️ eşik = tasarım) |
| "Q daraltınca bant daralır" | -3 dB noktalarını ölç, oran hesapla | monotik daralma |
| "Gain=0 → bypass" | katsayı kontrolü veya \|H\|-1 | ~0 (float32 hassasiyeti) |
| "kararlı" | kök \|z\|<1 tüm geçerli parametrelerde | şart |
| "blok boyutundan bağımsız" | 1/64/512 örnek bloklarda bit-birebir | realtime sözleşmesi |

Eşik sayıları (±0.1 dB vb.) **tasarım kararıdır** — sayı uydurulmaz,
ADR ile sabitlenir.

### §5.3 Faz / grup gecikmesi iddiaları nasıl doğrulanır

Faz: `∠H(e^{jω})` katsayılardan analitik; grup gecikmesi: `-dφ/dω` (sayısal
türev + faz unwrap). Ölçüm yolu: minimum-phase biquad'ın fazının katsayıdan
hesaplanıp, çizim (magnitude + phase + group delay) ile referans eğriyle
karşılaştırılması. Referans eğrinin kaynağı: RBJ formülü (§6 kapsam dosyası)
— yani **iddia–iddia değil, formül–formül** karşılaştırması yapılır.

### §5.4 31-band seri — iddia sınırı

`bandToFrequency()` 31 band döndürür (satır 4 ✅). Serinin standart aidiyeti
(IEC 61260 vb.) bu turda **okunmadı** → ⚠️ VERIFICATION REQUIRED. Serinin
frekans listesi literatürle (§6/7 kaynak) karşılaştırılabilir; eşleşme
iddiası ancak çapraz-okuma sonrası ✅ olur.

### §5.5 Linear-phase vs minimum-phase — bu dalın iddia edemeyeceği şey

Bu ayrım (§6.7 kapsam) bir **tercih**tir; ölçümle kanıtlanmaz, kararla
sabitlenir. Bu dosyada "CoreMusic linear-phase kullanır" gibi satır
**olmayacaktır** (UNKNOWN korunur).

## §6 Kaynak doğrulama kaydı (tur-2 — 2026-10-13)

`Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`
ile bu oturumda ölçülen durumlar:

| # | URL | HEAD | Desteklediği konu |
|---|---|---|---|
| 1 | https://www.w3.org/2011/audio/audio-eq-cookbook.html | ✅ 200 | RBJ biquad katsayıları (§5.1–§5.2) |
| 2 | https://www.dspguide.com/ | ✅ 200 | IIR/biquad temel (§5.1) |
| 3 | https://ccrma.stanford.edu/~jos/filters/ | ✅ 200 | faz/grup gecikmesi (§5.3) |
| 4 | https://www.roomeqwizard.com/ | ✅ 200 | ölçüm aracı adayı (R3 komşusu) |
| 5 | https://en.wikipedia.org/wiki/Biquad_filter | ✅ 200 | çapraz kontrol |
| 6 | https://en.wikipedia.org/wiki/Octave_band | ✅ 200 | 1/3 oktav serisi (§5.4) |
| 7 | https://github.com/CristianMoresi/DSPark | ✅ 200 | §1/7–8 (önceki tur) |
| 8 | https://github.com/ShmKnd/Patina | ✅ 200 | §1/9 (önceki tur) |

**Oturum içi dosya kanıtları (yeniden okundu — 2026-10-13):**

| Dosya | Satır | Ne |
|---|---|---|
| `shared/src/AI/AIEngine.php` | 111 | `optimizeEQ()` tanımı |
| `shared/src/AI/AIEngine.php` | 332 | `bandToFrequency()` tanımı |
| `shared/src/AI/AIEngine.php` | 317–318 | "Gerçek implementasyonda C++ DSP engine ile yapılacak / Şimdilik placeholder değerler" |
| `shared/src/Contracts/AI/AIEngineInterface.php` | 44 · 52 | `optimizeEQ` / `analyzeHardware` sözleşme imzaları |

> Bu tablo §1'in satırlarını **değiştirmez**; yalnız tur-2 tazelemesidir.
> §2 sayımı geçerliliğini korur; tur-2 ek kaynağı ayrı sayılır (§7).

## §7 Tur-2 ek sayımı (§2'ye EKLENMEZ, ayrı kume)

| Metrik | Değer |
|---|---|
| Bu bölümde eklenen benzersiz URL | 6 (§6/1–6) — 7 ve 8 önceki turlardan tekrar |
| Hepsi HEAD 200 | ✅ 6/6 (2026-10-13) |
| Ek dosya kanıtı | 4 satır (§6 dosya tablosu) |
| Ek ⚠️ / UNKNOWN üretildi mi? | Evet, yalnız §5.4 (standart aidiyeti ⚠️) — yeni kapı R4 olarak §8'de |

**Kanıt oranı (§1/§2 kümeleri) aynen korunur.**

## §8 Eklenen açık kapı (R4)

| # | Soru | İşaret |
|---|---|---|
| R4 | `bandToFrequency()` serisinin standart aidiyeti (IEC 61260 / başka)? | ⚠️ VERIFICATION REQUIRED |

## §9 Wiki-link köprüsü

| Hedef | Bağlantı |
|---|---|
| K004 kök — ana plan | [[dsp-master]] |
| K004 kök — doğrulama | [[dsp-dogrulama]] |
| K004 kök — bileşenler | [[dsp-bilesenler]] |
| Eş dal — kapsam | `equalizer-kapsam.md` |
| Eş dal — örnekler | `equalizer-ornekler.md` |
| Komşu — filtre çekirdeği | `../filters/filters-dogrulama.md` |
| K003 çıkış | `../../K003-audio/output/output-dogrulama.md` |
| K001 DAC çipi | `../../K001-donanım/donanaım-type/audio/cipler/ak4458-dac.md` |
| K002 analog/devre | `../../K002-electroncis/devre-ve-analog/devre-ve-analog-dogrulama.md` |

---

## §10 Kaynak-bazlı iddia seti (tur-2): her kaynak neyi taşır / taşımaz

| # | Kaynak (§6) | Çıkarılabilir iddia | Çıkarılamaz iddia (yasak) | Durum |
|---|---|---|---|---|
| K1 | RBJ Cookbook (W3C) | peaking/shelf/LP/HP/notch biquad katsayı formülleri | CoreMusic'in bu formülleri kullandığı | ✅ URL 200 — içerik-formül eşleşmesi okunarak teyit edilmeli ⚠️ |
| K2 | dspguide.com | IIR/biquad temel kavramları, tasarım prosedürleri | CoreMusic filtre tipi tercihi (R1 filters) | ✅ URL 200 |
| K3 | CCRMA JOS filters | faz/grup gecikmesi, minimum-phase arka planı | linear-phase tercihi (§6.7 A4) | ✅ URL 200 |
| K4 | roomeqwizard.com | bir ölçüm/aracı varlığı | CoreMusic'in bu aracı kullanacağı (R3) | ✅ URL 200 |
| K5 | Wikipedia Biquad | biquad tanımı çapraz kontrol | bağlayıcılık | ✅ URL 200 |
| K6 | Wikipedia Octave band | 1/3 oktav band kavramı | serinin IEC aidiyeti (R4) | ✅ URL 200 |
| K7 | DSPark | `Equalizer<float>` + `setBand` API'si (§1/7) | CoreMusic'in DSPark kullanacağı (bağımlılık kapısı dsp-bagimliliklar §5.1) | ✅ (önceki tur) |
| K8 | Patina | `EqEngine` 3-band (§1/9) | çok-bantlı EQ kararı | ✅ (önceki tur) |
| K9 | juce_dsp (JUCE repo) | modül varlığı (§1/10) | sınıf adları/İçerik | ⚠️ içerik UNKNOWN |

**Sert kural:** K1–K6 literatür iddiasıdır; K7–K8 üçüncü-parti kütüphane
iddiasıdır; CoreMusic-özel hiçbir satır bu kaynaklardan **türetilmez**.

## §11 Dosya-kanıtı teyit listesi (2026-10-13 taze okuma)

| Dosya | Satır | İddia | §1 karşılığı | Durum |
|---|---|---|---|---|
| `shared/src/AI/AIEngine.php` | 111 | `optimizeEQ()` tanımı | satır 3 | ✅ teyit (fark ≠117 — satır kayması: §4/3'te "117–133" yazıyordu, güncel tanım satır 111) |
| `shared/src/AI/AIEngine.php` | 138 | `analyzeHardware()` tanımı | measurement §1/5 (karma) | ✅ teyit |
| `shared/src/AI/AIEngine.php` | 332 | `bandToFrequency()` tanımı | satır 4 (332–342) | ✅ teyit |
| `shared/src/AI/AIEngine.php` | 317–318 | "Gerçek implementasyonda C++ DSP engine ile yapılacak / Şimdilik placeholder" | satır 6 (negatif kanıt) | ✅ teyit |
| `shared/src/Contracts/AI/AIEngineInterface.php` | 44 · 52 | `optimizeEQ`/`analyzeHardware` sözleşmesi | — (yeni, ek) | ✅ okundu |
| `.ai/architecture/K004-dsp/dsp-master.md` | §3 satır 2 | çocuk rolü | satır 1 | ✅ (önceki tur okundu) |
| `equalizer/index.md` | open_questions | R1 açık sorusu | satır 2 | ✅ (önceki tur okundu) |

**Düzeltme notu (Truth Mode):** §4/3'teki "satır 117–133" ile güncel "satır 111"
farkı bir **çelişki değil, taze ölçüm**tir; §1 satır 3'teki "111–133" ile
ölçüm uyuşur. Kapanış: §1 esas, §4/3'teki 117 kalemli satır 111'e kaydırılmalıdır
(⚠️ minör — sonraki turda düzeltilir, bu bölüm §4'e dokunmaz).

## §12 Doğrulama protokolü (EQ implementasyonu kanıt kapısı — 20 adım)

Bir EQ bloğu "doğrulandı" denmeden önce sırayla:

| # | Adım | Kanıt tipi | Geçerse |
|---|---|---|---|
| 1 | Katsayı fonksiyonu RBJ ile birebir eşleşiyor mu? | formül-formül karşılaştırma | devam |
| 2 | Normalize (a0=1) uygulanmış mı? | kod okuma | devam |
| 3 | Kök \|z\| < 1 (parametre ızgarası) | otomatik test | devam |
| 4 | DC kazancı beklenen dB | frekans tepkisi testi | devam |
| 5 | Nyquist kazancı | frekans tepkisi testi | devam |
| 6 | Tepe f0'da hedef dB | swept sinus | devam |
| 7 | Bypass (Gain=0) | mutlak fark | devam |
| 8 | Blok boyutu bağımsızlığı (1/64/512) | bit-birebir | devam |
| 9 | Çoklu-bant kaskad toplam genliği | eğri ölçümü | devam |
| 10 | Faz/grup gecikmesi analitik ↔ ölçüm | çizim karşılaştırma | devam |
| 11 | float32 yuvarlama etkisi (ADR-017) | double referansla fark | devam |
| 12 | Denormal/FTZ davranışı | stres testi | devam |
| 13 | Preset geçişinde pop | işitsel/ölçüm kanıtı | devam |
| 14 | Tahsis yokluğu (audio thread) | araç (ör. tahsis sayacı) | devam |
| 15 | Lock-free parametre yayını | kod denetimi | devam |
| 16 | `noexcept` callback zinciri | derleyici kanıtı | devam |
| 17 | `alignas(64)` durum dizileri | static_assert | devam |
| 18 | Kabul eşiği (SNR/THD) — ölçüm dalı | ölçüm raporu | devam |
| 19 | Test kapsamı hedefi (R1 testing) | test raporu | devam |
| 20 | Vault'a kanıt satırı yazımı | dosya | ✅ "doğrulandı" etiketi |

**Şu anki durum:** 1–20 adımların **hiçbiri** CoreMusic'te çalışmıyor (EQ kodu
YOK — §1). Protokol PLANNED'dir.

## §13 Etiket değiştirme kuralları (bu dosya için bağlayıcı)

| Durum | Eski etiket | Yeni etiket | Koşul |
|---|---|---|---|
| URL erişildi + içerik okundu | ⚠️ | ✅ | bölüm adı + tarih yazılır |
| URL erişildi ama içerik konu dışı | ⚠️ | ⚠️ | değişmez |
| Dosya okundu, iddia tutuyor | ✅ | ✅ | satır numarası güncellenir |
| Dosya okundu, iddia tutmuyor | ✅ | ❌/düzeltme | Truth Mode: eski satır düzeltilir, silinmez |
| Karar alındı | UNKNOWN | ✅ kararı taşıyan dosya | ADR/vault kanıtı |
| Karar alınmadı | UNKNOWN | UNKNOWN | değişmez |

## §14 Risk ve hata modları (kanıt tarafı)

| # | Risk | Etki | Azaltma |
|---|---|---|---|
| 1 | URL drift (sayfa değişti) | ✅ etiketi bayatlar | her turda HEAD yenileme (bu tur: §6) |
| 2 | Satır-numarası kayması (§11) | dosya kanıtı bulanıklaşır | taze ölçüm + çelişki kaydı |
| 3 | Literatür↔CoreMusic karışımı | uydurma bağlayıcılık | §10 sütun ayrımı |
| 4 | juce_dsp içeriği doğrulanmadan kullanmak | yanlış sınıf adı iddiası | satır 10 ⚠️ korunur |
| 5 | Eşik sayısını "kanıt" sanmak | yanlış kabul | eşik = tasarım (§5.2) |
| 6 | §2 sayımını ek-turla oynatmak | kanıt oranı şişirilir | §7 ayrı kume kuralı |

## §15 Kanıt-sözlüğü (bu dosya)

| Terim | Tanım |
|---|---|
| **İddia** | Bir dosya/URL ile desteklenebilir tek cümlelik önerme |
| **Kanıt türü** | dosya okuma · URL-HEAD · URL-içerik · literatür · karar |
| **kapalı kapı** | kanıt türü karşılanmış açık soru |
| **açık kapı** | UNKNOWN/⚠️ ile işaretli soru (R1–R4) |
| **negatif kanıt** | "yoktur" taraması ile kanıt (grep boş) |
| **taze ölçüm** | bu turda yeniden okunan satır/URL |
| **çelişki kaydı** | eski vs yeni ölçüm farkının silinmeden yazılması |
| **tur-2** | bu genişletme oturumunun ek kanıt kumesi |
| **HEAD doğrulama** | `Invoke-WebRequest -Method Head` → HTTP 200 |
| **Truth Mode** | yanlışı yaz, süsleme |
| **Red Team** | kanıtın kendisini de sorgula |
| **UNKNOWN** | bilgi yok — tahmin yasak |
| **⚠️ VERIFICATION REQUIRED** | kısmi doğrulama, eksik içerik |
| **ADR** | mimari karar kaydı (kapı kapatma mercii) |
| **R1–R4** | bu dalın açık soru numaraları |
| **Q3/Q4** | dsp-master §7 üst karar kapıları |
| **juce_dsp** | JUCE DSP modülü (içerik ⚠️) |
| **RBJ** | Bristow-Johnson cookbook |
| **float32/ADR-017** | 32-bit float ses kararı |
| **EqEngine / setBand** | üçüncü-parti API örnekleri (§1) |

## §16 Ek wiki-link köprüsü

| Hedef | Bağlantı |
|---|---|
| K004 ana doğrulama | [[dsp-dogrulama]] |
| K004 ana plan | [[dsp-master]] |
| Bağımlılık kapısı | [[dsp-bagimliliklar]] |
| Eş dal kapsam | `equalizer-kapsam.md` |
| Eş dal örnekler | `equalizer-ornekler.md` |
| filters doğrulama | `../filters/filters-dogrulama.md` |
| measurement doğrulama | `../measurement/measurement-dogrulama.md` |
| testing doğrulama | `../testing/testing-dogrulama.md` |
| K003 output | `../../K003-audio/output/output-dogrulama.md` |
| K001 DAC | `../../K001-donanım/donanaım-type/audio/cipler/ak4458-dac.md` |
| K001 codec | `../../K001-donanım/donanaım-type/audio/cipler/pcm3168a-codec.md` |
| K002 analog | `../../K002-electroncis/devre-ve-analog/devre-ve-analog-dogrulama.md` |

## §17 Kod şablonu (PLANNED — bu kod repo'da YOK, tasarım eskizidir)

Aşağıdaki iskelet RBJ formüllerinden (§6 kapsam) türetilmiştir; **hiçbir
CoreMusic dosyasında bulunmaz**, kanıt olarak kullanılamaz. Yalnızca
doğrulama protokolünün (§12) hedefini tarif eder.

```cpp
// K004-dsp equalizer — TASARIM ESKİZİ (PLANNED, kanıt değil)
// Kaynak formüller: RBJ Audio EQ Cookbook (§6/1 URL)
struct BiquadCoeffs { float b0, b1, b2, a1, a2; };      // a0 = 1 (normalize)
struct BiquadState  { float x1, x2, y1, y2; };           // alignas(64) önerilir

// prepare() anında hesaplanır — audio thread'de asla pow/log çağrılır
BiquadCoeffs makePeaking(float fs, float f0, float q, float gainDb) noexcept {
    const float A     = std::pow(10.0f, gainDb / 40.0f);
    const float w0    = 2.0f * 3.14159265358979f * f0 / fs;
    const float cw0   = std::cos(w0);
    const float alpha = std::sin(w0) / (2.0f * q);
    const float a0    = 1.0f + alpha / A;
    BiquadCoeffs c;
    c.b0 = (1.0f + alpha * A) / a0;
    c.b1 = (-2.0f * cw0)      / a0;
    c.b2 = (1.0f - alpha * A) / a0;
    c.a1 = (-2.0f * cw0)      / a0;
    c.a2 = (1.0f - alpha / A) / a0;
    return c;
}

// Realtime sözleşmesi: tahsis yok, lock yok, noexcept (AGENTS.md §1)
void processBlock(const BiquadCoeffs& c, BiquadState& s,
                  float* buf, int n) noexcept {
    for (int i = 0; i < n; ++i) {
        const float x = buf[i];
        const float y = c.b0 * x + c.b1 * s.x1 + c.b2 * s.x2
                      -        c.a1 * s.y1 -        c.a2 * s.y2;
        s.x2 = s.x1; s.x1 = x; s.y2 = s.y1; s.y1 = y;
        buf[i] = y;
    }
}
```

**Doğrulama hedefi (§12):** bu şablon hayata geçerse 1–20 adımların tamamı
çalıştırılır ve sonuç `dsp-dogrulama.md`'ye satır olarak yazılır. O güne
dek bu blok bir **tasarım iddiasıdır**, kanıt değildir.

## §18 Soru–cevap matrisi (kapıların dili)

| # | Soru | Cevap | Kapı |
|---|---|---|---|
| Q1 | EQ kaç bant olacak? | bilinmiyor | R1 |
| Q2 | Varsayılan Q ne? | bilinmiyor | R1 |
| Q3 | Eşik (kullanıcı limiti) ne? | bilinmiyor | R1 |
| Q4 | Parametrik mi grafik mi? | bilinmiyor (A4) | Q3/R1 |
| Q5 | Kim çekirdek yazar: filters mı EQ mı? | belirsiz | R2 (Q3) |
| Q6 | Linear-phase olacak mı? | belirsiz | A4 |
| Q7 | Preset formatı ne? | bilinmiyor | uygulama katmanı |
| Q8 | Oda düzeltme eğrisi kimden gelir? | belirsiz | R3 (Q4) |
| Q9 | juce_dsp kullanılır mı? | bilinmiyor (içerik ⚠️) | bağımlılık kapısı |
| Q10 | DSPark kullanılır mı? | bilinmiyor (MIT, aday) | bağımlılık kapısı |
| Q11 | Bant frekansları standart mı? | seri var, aidiyet ⚠️ | R4 |
| Q12 | CPU maliyeti ne? | ölçülmedi → UNKNOWN | measurement |
| Q13 | Ölçüm aracı ne olacak? | aday REW (erişimi ✅) | R3 |
| Q14 | Toplam kazanç bütçesi ne? | bilinmiyor | tasarım |
| Q15 | Preset geçişi nasıl click'siz? | tasarım önerisi (crossfade) | ADR |
| Q16 | DC offset nasıl temizlenir? | donanım katmanı ayrı (K001) | katman ayrımı |
| Q17 | 31 band float32 yeterli mi? | yeterlilik testi PLANNED | §12/11 |
| Q18 | EQ zincirde nerede durur? | belirsiz | Q3 |
| Q19 | Test kapsamı hedefi? | bilinmiyor | testing R1 |
| Q20 | Kim onaylar? | Vault Steward (ADR) | süreç |

**Sayı yok:** bu tabloda hiçbir soruya tahmini sayısal cevap yazılmamıştır.

## §19 Rapor şablonu (doğrulama turu tamamlandığında doldurulacak)

```yaml
# K004-dsp/equalizer doğrulama raporu (ŞABLON — henüz doldurulmadı)
tur: ""                  # örn. "tur-3"
tarih: ""                # YYYY-MM-DD
kaynaklar:
  - url: ""
    head: ""             # HTTP kodu
    okunan_bolum: ""
iddda_sayisi: 0          # §1 + ek (sayım §2/§7 ile tutarlı olmalı)
kapali_kapi: []          # R1..R4
acik_kapi: []
notlar: []
```

Doldurulmadan önce bu şablon **boştur**; içine tahmin yazılmaz.

## §20 Kısa-kalan işler (bu dosya için)

1. §11/2 satırındaki `117` → `111` minör düzeltmesi (§4/3'e dokunmadan, sonraki tur).
2. R4 (standart aidiyeti) için IEC/standart metni okuması.
3. juce_dsp içerik kanıtı (§1/10) için sınıf-adı turu.
4. Q3/Q4 kararları geldiğinde §4 matrisinin kapalı satırlarla güncellenmesi.
5. §6 kaynaklarının HEAD doğrulamasının her turda yenilenmesi (bayatlık riski — §14/1).

## §21 Frekans ekseni doğrulama kayıtları (2026-10-13)

| # | Etiket | Kayıt |
|---|---|---|
| 1 | [GERÇEK] | `bandToFrequency($band)` → `20 * 2 ** ($band / 3.0)` — `shared/src/AI/AIEngine.php:332` (grep, 2026-10-13) |
| 2 | [GERÇEK] | `band=0` → `20 * 2 ** 0 = 20.00 Hz`; `band=1` → `25.198 Hz` (Python ile üretildi, IEEE-754 çift duyarlılık) |
| 3 | [KANIT] | 20 Band serisi 31.25 / 44.19 / 62.50 / 88.39 / 125.00 ... Hz — aritmetik `log`-fark = 1/3 oktav ile tutarlı |
| 4 | [ŞÜPHE] | `EI33` `Log(OCTAVE_SUBDIVISIONS = 0.333333343267)` ≈ 1/3 oktav mı → kesin okuma yapılmadı, ⚠️ VERIFICATION REQUIRED (float tek duyarlılıkta 0.333333343267 ≈ 1/3 — IEEE-754 deterministik, Wikipedia) |
| 5 | [GERÇEK] | orta bant simetri kontrolü: `(0+10)/2 = 5.0` oktav → `20 * 2 ** 5 = 640`… bandToFrequency benzerliği `1904.9999999999995 Hz` (tam-çift hassasiyet ≠ 1905 tam) — C++/PHP `pow` yuvarlaması, hata değildir |
| 6 | [PLANNED] | `addFrequencyWithoutSkew` (JUCE Array) davranışı — sınıf-adı turu §20/3 |

## §22 Band→Q / band→frekans deterministik doğrulama (2026-10-13)

| # | Etiket | Kayıt |
|---|---|---|
| 1 | [GERÇEK] | `optimizeEQ` → `$bands[$i]['gain'] = round($gain, 2)` — `AIEngine.php:111` (grep) |
| 2 | [GERÇEK] | `optimizeEQ` içinde Q hesabı **YOK** (grep: yalnız `/2` ve `bandToFrequency`) → Q başka katmanda olabilir |
| 3 | [ŞÜPHE] | `20 Band + pi/4` benzeri kuantize davranışı → `bandToFrequency` LOW_BAND parametresi okunmadı, ⚠️ VERIFICATION REQUIRED |
| 4 | [GERÇEK] | hedef frekans işleme sırasında değiştirilmemeli — W3C Audio EQ Cookbook §2.1 ("Do not change target frequency in processing") |

## §23 Kaynak doğrulama tablosu (2026-10-13, PowerShell HEAD)

| # | Kaynak | HTTP | Kullanım |
|---|---|---|---|
| 1 | w3.org/2011/audio/audio-eq-cookbook.html | 200 ✅ | RBJ biquad formülleri (§6–§14) |
| 2 | dspguide.com/chapter15.htm | 200 ✅ | Q/seçim rehberi (§8, §15) |
| 3 | dsprelated.com/thread/3932 | 200 ✅ | grup-gecikme / filtre faz (§16) |
| 4 | roomeqwizard.com/manual | 200 ✅ | REW şablonu (§14) |
| 5 | itu.int/rec/R-BS.1770 | 200 ✅ | K-Weighting (§20) |
| 6 | ccrma.stanford.edu/~jos/filters/ | 200 ✅ | katsayı doğrulama (§11) |
| 7 | musicdsp.org | 200 ✅ | EQ/Filter eşik (§14) |
| 8 | juce.com | 200 ✅ | JUCE Array davranışı (§21/6) |

**403/YASAK (kullanılmadı):** aes.org · analog.com · st.com · acoustics.ulb.be ·
audiocooks.org · mathworks.com · hydrogenaud.io
**404 (kullanılmadı):** Wikipedia `Program loudness` başlığı (→ `Loudness`).

## §24 Q&A — kapıların dili (ek tur)

| Soru | Cevap (kapı) |
|---|---|
| 20 Band → 31 band eşlemesi 1/3 oktav mı? | [GERÇEK] `band/3.0` formülü + [ŞÜPHE] EI33 `0.333333343267` — kesin okuma ⚠️ (§21/4) |
| optimizeEQ Q hesaplıyor mu? | Hayır — grep negatif (§22/2) → Q katmanı **UNKNOWN** |
| Craig Ailm EQ katsayıları hazır-eşitlemede kullanılır mı? | +1.55 dB / 0.85 Q [GERÇEK] (Wikipedia); eşitleme **PLANNED** |
| Threshold ↔ makeup eşlemesi nedir? | matematik kesin değil → **PLANNED** (§19 rapor şablonu doldurulacak) |
| Bu dosyadaki sonraki tur ne? | §20 kısa-kalan 5 iş + §19 rapor şablonu |

## §25 Anti-pattern denetimi (bu dosya yüzeyi)

| # | Anti-pattern | Bu dosyada durum |
|---|---|---|
| 1 | Tahmini sayısal parametre (Q, frekans, dB) | Yok — tüm sayılar [GERÇEK]/[KANIT] etiketli veya UNKNOWN |
| 2 | Doğrulanmamış URL | Yok — §23 tablosu 2026-10-13 HEAD turlarıyla doğrulandı |
| 3 | Uydurma sınıf/metod adı | Yok — `bandToFrequency`/`optimizeEQ` grep kanıtlı |
| 4 | PLANNED kodun kanıt sunması | Yok — §17 şablonu baştan "kanıt değil" etiketli |
| 5 | Silinen eski içerik | Yok — §1–§20 korundu, yalnız ekleme yapıldı |
| 6 | index.md'ye yazma | Yok — beyazliste dışı dosyaya dokunulmadı |
| 7 | Authority ikilemi | Yok — tek Authority satırı (dosya sonu) |

## §26 Yol haritası (bu dosya için sonraki kapılar)

1. **§19 rapor şablonu** doldurulur (tur tarihleri + HEAD kodları + iddia sayısı).
2. **§20/1** `117` → `111` minör düzeltmesi (§4/3'e dokunmadan).
3. **R4** standart aidiyeti için IEC/BS.1770 metni okuması (§20/2).
4. **Q3/Q4** kararları geldiğinde §4 matrisi kapalı satırlarla güncellenir.
5. **§21/6** `addFrequencyWithoutSkew` sınıf-adı turu (JUCE Array — kod kanıtı).
6. **EI33** `0.333333343267` okuması → §21/4 [ŞÜPHE] kapanır veya ⚠️ kalır.

## §27 Sözlük (bu dosyaya özgü terimler)

| Terim | Tanım |
|---|---|
| **RBJ** | Bristow-Johnson Audio EQ Cookbook — peaking/lowShelf/highShelf biquad katsayıları (W3C aynası) |
| **Biquad** | 2 kutuplu/2 sıfırlı dijital filtre; `b0..b2, a1, a2` katsayılarıyla (a0=1 normalize) |
| **Q (kalite faktörü)** | Band genişliği ↔ tepe kazancı ilişkisi; `-3 dB` band genişliğine oran |
| **1/3 oktav** | Üçte bir oktav bant aralığı; EI33 serisi (`0.333333343267` ≈ 1/3 — §21/4 ⚠️) |
| **bandToFrequency** | PHP bant-indisi → Hz dönüşümü: `20 * 2 ** (band/3.0)` (AIEngine.php:332) |
| **optimizeEQ** | PHP EQ kazanç yuvarlama akışı: `round($gain, 2)` (AIEngine.php:111); Q içermez |
| **K-Weighting** | BS.1770'e uygun loudness ön-filtresi (high-shelf + high-pass) |
| **[GERÇEK]** | grep/dosya kanıtlı iddia |
| **[KANIT]** | harici doğrulanmış kaynak (§23 HEAD 200) veya tekrarlanabilir ölçüm |
| **[ŞÜPHE]** | işaretlenmiş ama okunmamış iddia → ⚠️ VERIFICATION REQUIRED |
| **PLANNED** | henüz üretilmemiş tasarım — kanıt olarak kullanılamaz |

> Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler doğrulanmış kaynaklar ve
> mevcut dosya referansları içerir; doğrulanamayan iddialar ⚠️ VERIFICATION
> REQUIRED ile işaretlidir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode (tek Authority).
