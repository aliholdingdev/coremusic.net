---
node_id: K000-isletim-sistemi/electroncis/devre-tasarimi/devre-tasarimi-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/electroncis/devre-tasarimi
children: []
depends_on:
  - K001-donanım
  - K004-dsp
  - K005-firmware
evidence:
  - "https://ngspice.sourceforge.io/docs/ngspice-html-manual/manual.xhtml (HTTP 200 · 2026-10-13)"
  - "https://qucs.sourceforge.net/ (HTTP 200 · 2026-10-13)"
  - ".ai/electronic/** (60 md · 10.323 sat) — devre içeriği orada derinleştiriliyor"
validation: "2026-10-13 · §0-§9 yazıldı · ≥500 satır · repo'da devre seviyesi kod YOK (dürüstlük)"
open_questions:
  - "Q-DTA1: Devre tasarimi akisinin PCB girişi K001 ile siniri netlesti mi? → ⚠️ VERIFICATION REQUIRED"
  - "Q-DTA2: Olcum/simulasyon araclari CoreMusic'te hangi asamada? → PLANNED (sartname §8)"
group: mimari
---

# devre-tasarimi-anayasa — Devre Tasarımı Anayasası

> **Yazım dokunulmaz:** `electroncis` (v2 §3 KURAL 1). Bu dosya devre tasarımının **anayasa**
> (akış · temel bloklar · sınır) seviyesini taşır; spesifik hesap/datasheet ayrıntısı alt dosyalardadır.
> Repo'da devre seviyesi kod YOK → burada **tasarım şartnamesi** düzeyinde kalınır, uydurma kod yazılmaz.

## §0 ELI15

Devre tasarımı dört adımdır: (1) **şema** çiz — hangi bileşen hangi pine bağlanır; (2) **simüle et**
— bilgisayarda (SPICE) çalıştır, hataları basit yakala; (3) **PCB'ye ver** — K001 ekibi yeri physically
yerleştirir; (4) **üret + ölç** — prototipi osiloskopla doğrula. Bu dosya bu dört adımın **kural**
seviyesidir; sayısal hesaplar `devre-simulasyonu-ve-dogrulama.md` ve `.ai/electronic/**` vault'undadır.

## §1 Devre tasarımı akışı (şema → simülasyon → PCB → üretim)

### §1.1 Akış aşamaları

| # | Aşama | Girdi | Çıktı | Sahip | Kapı (gate) |
|---|---|---|---|---|---|
| 1 | Gereksinim | bant · kazanç · seviye · güç | şartname parçası | bu alt-dal | gereksinim onayı |
| 2 | Mimarî seçim | topoloji (Class A/AB/D · lineer/switcher) | mimari şema | bu alt-dal | mimari onayı |
| 3 | Bileşen seçimi | datasheet · tolerans · stok | BOM taslağı | bu alt-dal | stok/tolerans |
| 4 | Şema çizimi | mimari + BOM | netlist/şema | bu alt-dal | şema incelemesi |
| 5 | Simülasyon | netlist + model | dalga biçimi · THD/SNR | `devre-simulasyonu` | sim geçmişi |
| 6 | Monte Carlo | tolerans seti | dağılım · verim yüzdesi | `devre-simulasyonu` | % verim eşiği |
| 7 | PCB girişi | netlist + kısıt | PCB tasarım girdisi | K001-donanım | şema dondurma |
| 8 | Prototim | PCB B+ | fiziksel kart | K001-donanım | — |
| 9 | Ölçüm/validasyon | prototim | ölçüm raporu | bu alt-dal + K001 | THD/SNR eşiği |
| 10 | Üretim | doğrulanmış tasarım | üretim dosyası | K001-donanım | üretim onayı |

### §1.2 Aşama detayları (1–6 · bu alt-dal sahibi)

| # | Aşama | Ne yapılır | Tipik hata | Yakalama yöntemi |
|---|---|---|---|---|
| 1 | Gereksinim | ölçülebilir hedef yaz (ör. THD+N <0.01% @1kHz) | belirsiz hedef | ölçüm planı |
| 2 | Mimarî | topoloji seç (lineer vs anahtarlamalı) | yanlış topoloji | verim/gürültü ödünleşimi |
| 3 | Bileşen | tolerans · gerilim · akım · sıcaklık derating | derating yok | worst-case hesap |
| 4 | Şema | netlist · geri besleme · kompanzasyon | kararsız geri besleme | simülasyon (AC) |
| 5 | Simülasyon | DC/AC/transient çalıştır | model yanlışı | model doğrulama |
| 6 | Monte Carlo | tolerans saçılımı | üretim sürprizi | verim yüzdesi |

### §1.3 PCB girişi (K001 sınırı — bu alt-dalın BİTİŞİ)

| # | Teslim edilen | Biçim | K001'in beklediği |
|---|---|---|---|
| 1 | Netlist | şema + bağlantı listesi | bağlantı doğruluğu |
| 2 | BOM | bileşen + tolerans + package | stok/üretim |
| 3 | Kısıtlar | iz genişliği · akım · gerilim · ısıl | PCB yerleşim kuralları |
| 4 | Arayüz pinleri | konnektör · pin map | kart-arası bağlantı |
| 5 | Test noktaları | probe point · ground | test jig (K001) |

> **Sınır kuralı:** PCB **yerleşimi** (iz · katman · soğutucu) bu alt-dalda **tartışılmaz** → K001'e
> taşınır (bkz. `electroncis-kapsam.md` §E.3 S1).

### §1.4 Üretim sonrası ölçüm (kapı 9)

| # | Ölçüm | Büyüklük | Araç | Eşik (CoreMusic hedefi) |
|---|---|---|---|---|
| 1 | Tepki | frekans yanıtı | analyzer | 20 Hz–20 kHz düz (±dB) |
| 2 | Bozulma | THD+N | audio analyzer | <0.01% @1kHz |
| 3 | Gürültü | SNR | audio analyzer | >100 dB |
| 4 | Güç | verim | güç kaynağı + yük | topolojiye bağlı |
| 5 | Termal | sıcaklık | termal kamera | derating sınırı |

> **Dürüstlük:** Bu eşikler **CoreMusic mühendislik hedefleridir** (AGENTS.md §5 Audio & Electronics);
> **ölçüm henüz yapılmadı** → `PLANNED` (sartname §8). Uydurma ölçüm sonucu yazılmaz.

## §2 Temel bloklar (besleme · amplifikatör · filtre · koruma)

### §2.1 Besleme bloğu (bu alt-dal: kavram · sahibi: `guc-ve-koruma/`)

| # | Blok | İşlev | Ana bileşen | Sahip alt-dal |
|---|---|---|---|---|
| 1 | AC→DC | şebeke/DC → ham DC | transformer · rectifier | `guc-ve-koruma` |
| 2 | Regülasyon | ham DC → temiz DC | lineer/switcher regulator | `guc-ve-koruma` |
| 3 | Filtreleme | ripple/EMI azaltma | LC · pi filtre | `guc-ve-koruma` |
| 4 | Koruma | akım/gerilim/termal sınır | eFuse · TVS · termistör | `guc-ve-koruma` |

### §2.2 Amplifikatör bloğu (bu alt-dal ANA SAHİP)

| # | Sınıf | İşlev | Karakteristik | Tipik kullanım |
|---|---|---|---|---|
| 1 | Class A | tek transistör, tamamen açık | en düşük bozulma, en düşük verim | preamp |
| 2 | Class AB | iki transistör, kısma açık | düşük bozulma, orta verim | güçlendirici |
| 3 | Class D | PWM anahtarlama | yüksek verim (>90%), gürültü filtresi gerekir | güç amplifikatörü |
| 4 | Op-amp | düşük seviye sinyal | yüksek kazanç, kolay kompanzasyon | filtre · buffer |

### §2.3 Filtre bloğu (bu alt-dal ANA SAHİP · analog)

| # | Filtre | İşlev | Parametre | Uygulama |
|---|---|---|---|---|
| 1 | Alçak geçiren (LPF) | yüksek frekans kesme | kesim frekansı fc | anti-aliasing (ADC öncesi) |
| 2 | Yüksek geçiren (HPF) | düşük frekans kesme | fc | DC offset bloğu |
| 3 | Bant geçiren (BPF) | belirli bant | fc · Q | EQ bandı |
| 4 | Notch | tek frekans kesme | f0 · Q | 50/60 Hz hüm |

### §2.4 Koruma bloğu (bu alt-dal: destek · sahibi: `guc-ve-koruma/`)

| # | Koruma | Tetikleyici | Eylem | Eşik sahibi |
|---|---|---|---|---|
| 1 | Aşırı akım | I > I_max | kısma/kapatma | `guc-ve-koruma` |
| 2 | Aşırı gerilim | V > V_max | kapatma · TVS | `guc-ve-koruma` |
| 3 | Termal | T > T_max | kısma/kapatma | `guc-ve-koruma` + K001 |
| 4 | Kısa devre | I tepe | anlık kapatma | `guc-ve-koruma` |

## §3 Analog vs dijital sınır

### §3.1 Sınır tanımı

| # | Bölge | Ne taşır | Dijitale geçiş noktası | Sahip K-node |
|---|---|---|---|---|
| 1 | Analog giriş | konnektör → preamp | — | bu alt-dal |
| 2 | Analog işleme | kazanç · filtre · dinamik | — | bu alt-dal |
| 3 | ADC | analog → dijital örnek | ADC çıkışı (dijital veri) | bu alt-dal (seçim) |
| 4 | Dijital işleme | dijital örnek işleme | — | K004-dsp |
| 5 | DAC | dijital → analog | DAC çıkışı (analog) | bu alt-dal (seçim) |
| 6 | Analog çıkış | DAC → yük sürücüsü | — | bu alt-dal |

### §3.2 Sınır kararları

| # | Karar | Analog tarafı | Dijital tarafı | Kural |
|---|---|---|---|---|
| 1 | Anti-aliasing filtresi | LPF tasarımı (bu alt-dal) | — | ADC öncesi zorunlu |
| 2 | Bileşilememe (reconstruction) | — | LPF (bu alt-dal) | DAC sonrası |
| 3 | Örnekleme hızı | — | fs seçimi (K004/K003) | Nyquist: fs > 2·fmax |
| 4 | Bit derinliği | dinamik aralık | kuantalama gürültüsü | 24-bit hedef |
| 5 | Kanal sayısı | analog zincir | TDM/USB (K004/K003) | 8.1 (sartname) |

### §3.3 Sınır ihlali belirtileri

| # | Belirti | Örnek | Nereye taşınır |
|---|---|---|---|
| 1 | Dijital filtre kodu (biquad/FFT) | C++ | K004-dsp |
| 2 | Örnekleme/kanal kararı tartışması | fs · TDM | K003-audio/K004 |
| 3 | ADC/DAC register yazımı | I2C/SPI | K005-firmware |
| 4 | Analog LPF tasarımı | fc · Q · Op-amp | bu alt-dal (kalır) |

## §4 Ölçüm / simülasyon araçları

### §4.1 Simülasyon araçları

| # | Araç | Tür | Kaynak | Durum |
|---|---|---|---|---|
| 1 | ngspice | açık kaynak SPICE | https://ngspice.sourceforge.io/docs/ngspice-html-manual/manual.xhtml | ✅ HTTP 200 |
| 2 | QUCS | açık kaynak simülatör | https://qucs.sourceforge.net/ | ✅ HTTP 200 |
| 3 | LTspice | ticari (ADI), ücretsiz | ⚠️ VERIFICATION REQUIRED (URL ERR bu tur) | doğrulanmadı |
| 4 | PSpice | ticari (Cadence) | ⚠️ VERIFICATION REQUIRED | doğrulanmadı |

> **Dürüstlük:** LTspice/PSpice için bu tur resmi URL erişimi **ERR** oldu → kullanılmadı; yalnız
> ngspice ve QUCS canlı 200 ile doğrulandı (bkz. `electroncis-dogrulama.md` §I.3).

### §4.2 Ölçüm araçları

| # | Araç | Büyüklük | Kullanım |
|---|---|---|---|
| 1 | Osiloskop | dalga biçimi · zaman | transient · ring · overshoot |
| 2 | Fonksiyon jeneratörü | girdi sinyali | tepki · freq sweep |
| 3 | Güç kaynağı + yük | güç · verim | yük regülasyonu |
| 4 | Audio analyzer | THD+N · SNR | performans doğrulama |
| 5 | Multimetre | DC gerilim/akım · süreklilik | bias · shorts |
| 6 | Termal kamera/termistör | sıcaklık | ısıl doğrulama |

### §4.3 Araç × aşama matrisi

| # | Araç | Şema | Sim | PCB | Üretim/Ölçüm |
|---|---|---|---|---|---|
| 1 | ngspice/QUCS | doğrula | ANA | — | — |
| 2 | Osiloskop | — | karşılaştır | — | ANA |
| 3 | Audio analyzer | — | THD/SNR tahmin | — | ANA |
| 4 | Multimetre | — | DC bias | süreklilik | ANA |

## §5 Komşu sınırı (PCB→K001 · DSP→K004 · firmware→K005)

| # | Komşu | Sınır konusu | Bu alt-dal ne VERİR | Komşu ne ALIR | Etiket |
|---|---|---|---|---|---|
| 1 | K001-donanım | PCB · kart · mekanik · ısıl | netlist · BOM · kısıt · test nokta | PCB tasarım girdisi | ⚠️ VERIFY |
| 2 | K004-dsp | dijital filtre · EQ · dinamik | analog LPF · fs/bit gereksinimi | dijital tasarım girdisi | taslak |
| 3 | K005-firmware | boot · RTOS · koruma döngüsü | koruma eşik değeri · register haritası | firmware parametresi | taslak |
| 4 | K003-audio | ses API'si (ASIO/WASAPI) | ADC/DAC zinciri seçimi · kanal map | API donanım girdisi | taslak |
| 5 | `guc-ve-koruma/` | besleme + koruma | güç gereksinimi (V · I) | koruma/regüle tasarımı | bu K-node içi |

## §6 Terim sözlüğü

| # | Terim | Tanım | İlgili |
|---|---|---|---|
| 1 | Netlist | bileşen-bağlantı listesi | şema |
| 2 | BOM | malzeme listesi (bill of materials) | üretim |
| 3 | THD+N | toplam bozulma + gürültü | ölçüm |
| 4 | SNR | sinyal-gürültü oranı | ölçüm |
| 5 | Nyquist | fs > 2·fmax örnekleme kuralı | ADC |
| 6 | Anti-aliasing | ADC öncesi LPF | filtre |
| 7 | Derating | bileşeni nominalin altında çalıştırma | güvenilirlik |
| 8 | Tolerans | bileşen sapma yüzdesi (%) | Monte Carlo |
| 9 | Geri besleme | çıktının girdiye yönlendirilmesi | kazanç kararlılığı |
| 10 | Kompanzasyon | kararlılık için faz/kazanç düzeltmesi | op-amp/switcher |
| 11 | Class AB/D | amplifikatör sınıfı | amplifikatör |
| 12 | Ripple | DC üzerinde AC dalgalanma | güç |
| 13 | EMI | elektromanyetik girişim | güç/uyumluluk |
| 14 | Monte Carlo | tolerans saçılım simülasyonu | doğrulama |

## §7 Kapsam dışı (nereye gider)

| # | Kapsam dışı | Nereye gider | Dayanak |
|---|---|---|---|
| 1 | PCB yerleşimi · iz · katman | K001-donanım | §5-1 |
| 2 | Mekanik/soğutucu tasarımı | K001-donanım | §5-1 |
| 3 | Dijital filtre algoritması (C++) | K004-dsp | §3.3-1 |
| 4 | Firmware (boot · RTOS · register yazımı) | K005-firmware | §3.3-3 |
| 5 | Ses API'si (ASIO/WASAPI) | K003-audio | §5-4 |
| 6 | Sürücü/modül modeli | `isletim-sistemi-kernel` | §5 (komşu) |
| 7 | Güç topolojisi hesabı · eşik | `guc-ve-koruma/` | §2.1 |
| 8 | Bina/tesisat güç dağıtımı | kapsam dışı | K-node kararı |

## §8 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q1 | PCB girişi (K001) sınırı hangi belgeyle kapanır? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q2 | analog LPF ↔ K004-dsp sınırı net mi? | UNKNOWN | Vault Steward |
| Q3 | LTspice/PSpice resmi URL'leri doğrulanacak mı? | ⚠️ VERIFICATION REQUIRED | bu dosya |
| Q4 | THD/SNR hedefleri hangi prototimde ölçülecek? | PLANNED | ölçüm planı |
| Q5 | `node_type: module` Kademe-4 için geçerli mi? | ⚠️ VERIFICATION REQUIRED | v2 §4.2 |
| Q6 | ADC/DAC seçimi (PCM3168A · AK4458) bu alt-dalda mı? | UNKNOWN | Vault Steward |

## §10 Worst-case tasarım metodolojisi (tolerans · derating · güvenlik payı)

> Bu bölüm, §1.2-3 (bileşen seçimi) ve §1.2-6 (Monte Carlo) arasındaki **tasarım kuralı** seviyesidir.
> Amaç: her bileşemin **en kötü durumda** (tolerans · sıcaklık · yaşlanma · üretim saçılımı) hâlâ
> güvenli çalışmasını garantilemek. Sayısal değer uydurulmaz; yalnız **hesap yöntemi** taşınır.

### §10.1 Derating kuralları

| # | Büyüklük | Kural (tipik mühendislik) | Uygulama |
|---|---|---|---|
| 1 | Kapasitör gerilimi | nominal ≥ 2× çalışır gerilim (elektrolit) | güç filtresi |
| 2 | Kapasitör gerilimi (seramik) | DC bias derating (X5R/X7R) dikkate alınır | decoupling |
| 3 | Direnç gücü | P ≤ ½ · P_nom | sıcaklık |
| 4 | Transistör Vceo/Vds | ≤ ½ · maks | amplifikatör/switcher |
| 5 | Sıcaklık | T_j ≤ T_jmax − 20 °C pay | termal |

### §10.2 Tolerans bütçesi

| # | Kaynak | Tipik etki | Toplama yöntemi |
|---|---|---|---|
| 1 | Direnç tolerans (%1/%5) | kazanç sapması | RSS (kök-toplam-kare) veya worst-case |
| 2 | Kapasitör tolerans (%10/%20) | fc sapması | worst-case (filtre) |
| 3 | Op-amp offset/IB | DC sapma | worst-case |
| 4 | ADC/DAC DNL/INL | bozulma | datasheet |
| 5 | Sıcaklık katsayısı (ppm/°C) | drift | RSS + sıcaklık aralığı |

### §10.3 Güvenlik payı hesabı

| # | Adım | İşlem | Çıktı |
|---|---|---|---|
| 1 | Nominal değer | tasarım noktası | V_nom · I_nom · T_nom |
| 2 | Tolerans aralığı | ±tolerans uygula | V_min/max · I_min/max |
| 3 | Sıcaklık | katsayıyla genişlet | T aralığı |
| 4 | Worst-case | en kötü kombinasyon | sınır değer |
| 5 | Pay | sınır − nominal | güvenlik payı |
| 6 | Karar | pay > 0 → geç | PASS/FAIL |

## §11 Kararlılık · geri besleme · kompanzasyon

| # | Kavram | Tanım | Hata belirtisi | Düzeltme |
|---|---|---|---|---|
| 1 | Açık kazanç (Aol) | geri besleme olmadan kazanç | — | — |
| 2 | Geri besleme faktörü (β) | çıktının girdiye oranı | — | — |
| 3 | Kazanç marjı (GM) | faz −180° iken kazanç payı | osilasyon | kompanzasyon |
| 4 | Faz marjı (PM) | kazanç 0 dB iken faz payı | overshoot/ringing | Miller kapasitör |
| 5 | Unity-gain bant (GBW) | kazanç=1 frekansı | bant daralması | doğru op-amp |
| 6 | Slew rate | maksimum çıkış değişim hızı | distorsiyon (yüksek f) | SR seçimi |

### §11.1 Kararlılık doğrulama adımları

| # | Adım | Simülasyon | Beklenen |
|---|---|---|---|
| 1 | AC analiz | faz/kazanç grafiği | PM ≥ 45° (hedef) |
| 2 | Transient | adım/impuls yanıtı | overshoot sınırlı |
| 3 | Worst-case | tolerans + sıcaklık | PM hâlâ pozitif |
| 4 | Yük varyasyonu | kapasitif yük | kararlılık korunur |

## §12 Topraklama · parazit · güç kaynağı düzeni (kavram seviyesi — PCB DEĞİL)

> **Sınır uyarısı:** Bu bölüm **topraklama/decoupling kavramını** taşır; PCB **yerleşimi** (iz rotası,
> katman yığını) K001-donanıma aittir ve burada **tartışılmaz**.

| # | Kavram | Amaç | Kavramsal kural | Sahip |
|---|---|---|---|---|
| 1 | Analog/dijital toprak ayrımı | gürültü izolasyonu | tek-nokta birleşim | bu alt-dal (kavram) + K001 |
| 2 | Decoupling kapasitörü | yüksek-f gürültü | her aktif bileşene yakın | bu alt-dal (kavram) + K001 |
| 3 | Star toprak / ground plane | dönüş akımı yolu | düşük empedans | K001 |
| 4 | EMI filtresi | giriş/çıkış paraziti | LC · ferrite | `guc-ve-koruma` |
| 5 | Ekranlama | dış EMI | konektör/şasi | K001 |
| 6 | Guard ring | sızıntı akımı | yüksek-empedans düğüm | bu alt-dal |

## §13 Tipik analog zincir şablonları (ad-türevi · değer YOK)

> Bu şablonlar **kavramsal** zincirlerdir; sayısal değer **uydurulmaz** (ölçüm PLANNED).

| # | Zincir | Adımlar | Sahip |
|---|---|---|---|
| 1 | Mikrofon ön-yükseltici | HPF → preamp (op-amp) → LPF (anti-alias) → ADC | bu alt-dal |
| 2 | Hat girişi | HPF → gain → BPF → ADC | bu alt-dal |
| 3 | DAC → hat çıkışı | DAC → LPF (reconstr.) → buffer → yük | bu alt-dal |
| 4 | DAC → güçlendirici | DAC → LPF → Class-AB/D → yük (hoparlör) | bu alt-dal + amplifikatör |
| 5 | Kalibrasyon/ölçüm | bilinen kaynak → DUT → analyzer | bu alt-dal + ölçüm |

## §14 Sınır ihlali kontrol listesi (bu dosyada YAZILMAZ)

| # | Konu | Nereye gider |
|---|---|---|
| 1 | C++/JUCE ses motoru kodu | K003-audio/K004-dsp |
| 2 | PCB iz/katman/yerleşim | K001-donanım |
| 3 | I2C/SPI register yazımı (firmware) | K005-firmware |
| 4 | Buck/boost/regulator hesabı | `guc-ve-koruma/` |
| 5 | Koruma eşiği (akım/gerilim/termal/ESD) | `guc-ve-koruma/` |
| 6 | ASIO/WASAPI buffer/exclusive lock | K003-audio |

## §15 Bileşen seçim rehberi (ad-türevi · datasheet odaklı)

> Bu bölüm bileşen **sınıfı seçim kurallarını** taşır; spesifik marka/model seçimi `.ai/electronic/**`
> vault'unda ve ilgili K-node'laradır. Sayısal değer uydurulmaz, yalnız **seçim ölçütü** verilir.

### §15.1 Direnç seçimi ölçütleri

| # | Ölçüt | Neden önemli | Seçim kuralı |
|---|---|---|---|
| 1 | Tolerans (%) | kazanç/fc doğruluğu | kritik: %1 · genel: %5 |
| 2 | Sıcaklık katsayısı (ppm/°C) | drift | kritik: ≤100 ppm |
| 3 | Güç (W) | ısınma | ≥2× hesaplanan güç |
| 4 | Gürültü (Johnson) | ses yolu | düşük-değer · metal-film |
| 5 | Voltage katsayısı | gerilim altı direnç kayması | yüksek-gerilim yerine yüksek-katsayı |

### §15.2 Kapasitör seçimi ölçütleri

| # | Tip | Uygulama | Kural | Dikkat |
|---|---|---|---|---|
| 1 | C0G/NP0 | filtre · timing · audio kritik | tolerans %1–5 · düşük kayıp | pahalı, küçük değer |
| 2 | X7R | decoupling · bypass | DC bias derating | değer düşüşü |
| 3 | X5R | decoupling | ekonomik | sıcaklık |
| 4 | Elektrolit (Al) | güç filtresi | ≥2× gerilim · ESR | ripple akım · ömür |
| 5 | Tantal | düşük ESR · kompakt | gerilim derating | ateşlenme riski |
| 6 | Film | ses yolu · yüksek kalite | düşük kayıp · düşük gürültü | boyut |

### §15.3 Yarı iletken seçimi ölçütleri

| # | Tür | Uygulama | Ölçüt |
|---|---|---|---|
| 1 | BJT | ses · analog | gürültü · hFE · eşleşme |
| 2 | MOSFET | anahtarlama · Class-D | Rds(on) · Vgs(th) · Qg |
| 3 | Op-amp | filtre · kazanç | GBW · SR · offset · gürültü |
| 4 | Regülatör | güç | PSRR · dropout · gürültü |
| 5 | Koruma (eFuse/TVS) | koruma | eşik · tepki · kapasite |

### §15.4 Entegre seçimi (ses yolü — PCM3168A referans)

| # | Rol | Tipik bileşen (CoreMusic) | Seçim ölçütü |
|---|---|---|---|
| 1 | DAC | PCM3168A · AK4458 | kanal · THD+N · SNR · I2S/TDM |
| 2 | ADC | ⚠️ UNKNOWN (PLANNED) | fs · bit · SNR |
| 3 | Amplifikatör | Class-AB/D | güç · verim · bozulma |
| 4 | Koruma | eFuse · TVS · termistör | eşik · tepki |

> **Dürüstlük:** PCM3168A/CoreMusic donanımı **ADR-038 H001** ile sabittir; ADC seçimi henüz
> belgelenmemiştir → `⚠️ VERIFICATION REQUIRED` / `PLANNED`.

## §16 Ölçüm büyüklükleri (THD · SNR · kavram)

| # | Büyüklük | Tanım | Birim | Neden |
|---|---|---|---|---|
| 1 | THD | toplam bozulma (harmonik) | % · dB | ses saflığı |
| 2 | THD+N | bozulma + gürültü | % · dB | gerçek performans |
| 3 | SNR | sinyal/gürültü oranı | dB | dinamik aralık |
| 4 | DNR | dinamik aralık | dB | en sessiz–en yüksek |
| 5 | FREP | frekans tepkisi | dB vs Hz | düz bant |
| 6 | DNL/INL | ADC/DAC doğrusallık | LSB | çözünürlük |
| 7 | Crosstalk | kanallar arası sızma | dB | kanal izolasyonu |
| 8 | PSRR | güç reddetme oranı | dB | güç gürültüsü |

### §16.1 Ölçüm koşulları (tipik şartname)

| # | Koşul | Değer | Not |
|---|---|---|---|
| 1 | Referans frekans | 1 kHz | THD/SNR standardı |
| 2 | Örnekleme hızı | 48 kHz | sartname |
| 3 | Bit derinliği | 24-bit | ADR-017 (32-float iç · 24 dış) |
| 4 | Kanal | 2 (stereo) · 8.1 (çevre) | sartname |
| 5 | Yükleme | nominal | yük regülasyonu |
| 6 | Sıcaklık | oda (25 °C) · sıcak | drift ölçümü |

## §17 Uygulama notu referansları (bu tur doğrulanan URL'ler)

| # | Kaynak | URL | 2026-10-13 | Kullanım |
|---|---|---|---|---|
| 1 | ngspice kılavuzu | https://ngspice.sourceforge.io/docs/ngspice-html-manual/manual.xhtml | ✅ 200 | simülasyon |
| 2 | QUCS | https://qucs.sourceforge.net/ | ✅ 200 | simülasyon |
| 3 | TI LM358 ds | https://www.ti.com/lit/ds/symlink/lm358.pdf | ✅ 200 | op-amp |
| 4 | TI OPA2134 ds | https://www.ti.com/lit/ds/symlink/opa2134.pdf | ✅ 200 | ses op-amp |
| 5 | TI ADS131M04 ds | https://www.ti.com/lit/ds/symlink/ads131m04.pdf | ✅ 200 | ADC |
| 6 | TI PCM1794A ds | https://www.ti.com/lit/ds/symlink/pcm1794a.pdf | ✅ 200 | DAC |
| 7 | TI sloa093 | https://www.ti.com/lit/an/sloa093/sloa093.pdf | ✅ 200 | uygulama notu |
| 8 | TI sboa122 | https://www.ti.com/lit/an/sboa122/sboa122.pdf | ✅ 200 | uygulama notu |

> Tam doğrulama matrisi: `../electroncis-dogrulama.md` §I. Başlık iddiaları §I.2 notuna tabidir.

## §18 Tasarım kapı (gate) kontrol listesi

> Her aşama geçmeden sonrakine başlanmaz (WORKFLOW §8.9 hard-gate mantığı). Bu liste **tasarım-anı**
> kapılarını taşır; üretim kapıları K001'dedir.

### §18.1 Aşama kapıları

| # | Kapı | Geçme kiteriyumu | Kanıt | Durum |
|---|---|---|---|---|
| 1 | Gereksinim | ölçülebilir hedef (THD · SNR · fs) | şartname satırı | PLANNED |
| 2 | Mimari | topoloji gerekçeli | mimari notu | PLANNED |
| 3 | Bileşen | worst-case derating geçti | hesap tablosu | PLANNED |
| 4 | Şema | netlist incelemesi | inceleme kaydı | PLANNED |
| 5 | Sim (AC/DC/trans) | PM ≥ 45° · DC bias nominal | sim raporu | PLANNED |
| 6 | Monte Carlo | verim yüzdesi ≥ hedef | dağılım raporu | PLANNED |
| 7 | PCB girişi | şema donduruldu | teslim paketi | PLANNED |
| 8 | Ölçüm | THD/SNR/FREP eşiği geçti | ölçüm raporu | PLANNED |

### §18.2 Kapı reddi davranışları

| # | Red nedeni | Aksiyon | Sahip |
|---|---|---|---|
| 1 | PM < 45° | kompanzasyon → tekrar sim | bu alt-dal |
| 2 | Worst-case pay ≤ 0 | bileşen/tolerans revizyonu | bu alt-dal |
| 3 | Verim < hedef | Monte Carlo → tolerans daralt | bu alt-dal |
| 4 | THD/SNR eşiği tutmadı | zincir revizyonu → tekrar ölç | bu alt-dal + K001 |
| 5 | PCB kısıtı çelişkisi | K001 ile müzakere | K001 |

### §18.3 Roller ve sorumluluk (RACI — tasarım-anı)

| # | Rol | R/A/C/I | Not |
|---|---|---|---|
| 1 | Devre tasarımı (bu alt-dal) | R | şema · sim · ölçüm planı |
| 2 | `guc-ve-koruma/` | C | güç gereksinimi |
| 3 | K001-donanım | C (PCB girişi) | teslim alır |
| 4 | K004-dsp | C (fs/bit gereksinimi) | dijital tasarım |
| 5 | K005-firmware | C (eşik/register) | firmware |
| 6 | Vault Steward (Q1) | A (kapsam) | kapsam kararı |
| 7 | QA/ölçüm | C | ölçüm doğrulama |

## §19 Hızlı referans — birimler ve semboller

| # | Sembol | Büyükllük | Birim | Not |
|---|---|---|---|---|
| 1 | V | gerilim | V · mV · µV | — |
| 2 | I | akım | A · mA · µA | — |
| 3 | R | direnç | Ω · kΩ · MΩ | — |
| 4 | C | kapasitans | F · µF · nF · pF | — |
| 5 | L | indüktans | H · mH · µH | — |
| 6 | f | frekans | Hz · kHz · MHz | — |
| 7 | fc | kesim frekansı | Hz | filtre |
| 8 | fs | örnekleme hızı | Hz | ADC/DAC |
| 9 | Z | empedans | Ω | — |
| 10 | dB | kazanç/oran | dB | 20·log(V) · 10·log(P) |

### §19.1 Sık kullanılan dönüşümler

| # | Dönüşüm | Bağıntı | Not |
|---|---|---|---|
| 1 | Voltaj kazancı → dB | dB = 20·log10(Av) | Av > 0 |
| 2 | Güç oranı → dB | dB = 10·log10(P2/P1) | — |
| 3 | dB → voltaj oranı | Av = 10^(dB/20) | — |
| 4 | RC kesim frekansı | fc = 1/(2π·R·C) | 1. katman LPF |
| 5 | LC kesim frekansı | fc = 1/(2π·√(LC)) | 2. katman |
| 6 | Nyquist | fs > 2·fmax | anti-alias |
| 7 | Q ↔ bant genişlik | Q = fc / BW | filtre |

## §9 `.ai/electronic` atıfı

| # | Konu | Vault dosyası | İlişki |
|---|---|---|---|
| 1 | Amplifikatör mimarisi | `.ai/electronic/amplifier-architecture.md` · `amplifier/*` | atıf |
| 2 | Devre/genel | `.ai/electronic/core-music-electronics-overview.md` | atıf |
| 3 | Ses mimarisi | `.ai/electronic/audio-architecture.md` | atıf |
| 4 | Donanım tasarımı | `.ai/electronic/hardware-design.md` · `hardware/*` | sınır (K001) |
| 5 | DSP motoru | `.ai/electronic/dsp-engine-architecture.md` · `dsp/*` | sınır (K004) |

> **Kural:** Vault dosyaları **dokunulmaz**; yalnız atıf (içerik kopyalanmaz). Derin teknik içerik
> `.ai/electronic/**` (60 dosya · 10.323 sat) vault'unda derinleştiriliyor.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
