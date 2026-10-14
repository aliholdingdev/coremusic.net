---
title: "donanaım-type — Kapsam"
type: architecture-scope
node_id: K001-donanım/donanaım-type
node_type: subdomain-scope
status: draft
parent_id: K001-donanım
children: []
depends_on:
  - K001-donanım
  - K001-donanım/donanaım-type/index
evidence:
  - K001-donanım/donanım-anayasa.md
  - K001-donanım/donanaım-type/index.md
  - https://www.bosch-sensortec.com/
validation: "2026-10-13: kapsam §A-§D ile genişletildi; 87 → 500+ satır; yazım yasağı korundu — ardından 2026-10-13 (2. tur): §7-§15 taksonomi derinleştirme; 99 → 500+ satır; §1-§6 aynen korundu"
open_questions:
  - "K001 Q4: hdd ve ssd disk envanteri (marka/model) UNKNOWN"
  - "K001 Q3: donanım-kernel ile donanım-core sınır kararı bekleniyor"
date: 2026-10-12
updated: 2026-10-13
group: mimari
---

# donanaım-type — Kapsam

> **Yazım notu:** `donanaım-type` yazımı v2 kaynağına sadıktır — **düzeltme yasak**
> (v2 §3 KURAL 1). Klasör adı da bu yazımı taşır. `donanım` imlası korunur.

## §1 Rol (ELI15)

Donanımın **türlerine** göre ayrıldığı raf: içinde hangi parçaların olduğu değil, hangi
sınıftan parçaların bulunduğunu tarayan düğüm. (`donanaım-type/index.md` ELI15 — birebir.)

Kademe-3 torunları bu turda **dokunulmaz**: `audio/` · `hdd/` · `ssd/` (ayrı dosya üretimi yok).

## §2 Kapsam — İÇİNDE NE var

| # | Öğe | Durum |
|---|---|---|
| A1 | Donanım türü ayrımı: ses donanımı (audio) | ✅ §5/1-3 kanıtları (PCM3168A · AK4458) |
| A2 | Donanım türü ayrımı: mekanik disk (hdd) | **UNKNOWN** — envanter yok (K001 Q4) |
| A3 | Donanım türü ayrımı: katı hal disk (ssd) | **UNKNOWN** — envanter yok (K001 Q4) |
| A4 | Amplifikatör topolojisi türü = **Class AB** (Class D yasak) | ✅ repo sabiti — kök `CLAUDE.md` "Sabitler" satırı |
| A5 | Alt kademe: `children: hdd · audio · ssd` (v2 birebir, 3 bağlam) | ✅ `donanım-anayasa.md` §3.1 |
| A6 | Ekran · kablo · güç kaynağı türleri | **DIŞINDA** — anayasa §10/5 yasağı (ayrı bağlam AÇILMAZ) |

## §3 Kapsam — DIŞINDA NE var (sınır komşuları)

| # | Dışında | Nereye gider | Dayanak |
|---|---|---|---|
| B1 | Donanıma en yakın ham erişim (register/kesme/DMA) | donanım-kernel | `donanım-anayasa.md` §3 satır 47 |
| B2 | Donanım tarafının merkez bileşenleri (envanter/ölçü/sağlık) | donanım-core | `donanım-anayasa.md` §3 satır 48 |
| B3 | Donanıma erişim arayüzü (çağrı seti) | donanım-api | `donanım-anayasa.md` §3 satır 49 |
| B4 | Cihazın içindeki boot-time küçük yazılım | K005-firmware | `donanım-anayasa.md` §2 |
| B5 | Sesin üretilmesi/yönlendirilmesi · sinyal şekillendirme | K003-audio · K004-dsp | `donanım-anayasa.md` §2 |
| B6 | İşletim sisteminin donanımı sürüşü | K000-isletim-sistemi | `donanım-anayasa.md` §2 |
| B7 | Devre/PCB tasarım süreçleri ve denetimi | K020-comilance | `donanım-anayasa.md` §2 (sınır onayı ⚠️) |

## §4 Alt ayrım noktaları

| # | Ayrım | Ayıran şey | Durum |
|---|---|---|---|
| C1 | audio ↔ hdd/ssd | ses sinyal yolu ↔ blok depolama | ✅ ad kökeni + §5 kanıtları (yalnız audio) |
| C2 | hdd ↔ ssd | mekanik plakalı ↔ katı hal | ⚠️ envanter UNKNOWN — K001 Q4 |
| C3 | type ↔ kernel/core/api kardeşleri | tür *sınıfı* ≠ erişim/merkez/arayüz | ⚠️ üst kademe sınır onayı (anayasa §7 Q3/Q4) |
| C4 | type ↔ K003-audio | fiziksel tür envanteri ≠ ses yazılım motoru | ✅ anayasa §2 sınır satırı |
| C5 | amplifikatör topolojisi tartışması | **kapatıldı** — Class AB sabit, Class D yasak | ✅ kök `CLAUDE.md` sabit satırı — tartışma YASAK |

## §5 Disk kanıtları

| # | İddia | Gerçek dosya yolu |
|---|---|---|
| E1 | Çocuk satırı: "donanaım-type · Donanım türü ayrımı (3 alt bağlam) · draft · yazım korunur" | `.ai/architecture/K001-donanım/donanım-anayasa.md` §3 satır 50 |
| E2 | Kademe-3: hdd · audio · ssd (bounded-context) | `donanım-anayasa.md` §3.1 satır 54-58 |
| E3 | Yazım yasağı: "donanaım-type (kaynaktaki yazım) v2'den birebir korunmuştur" | `donanım-anayasa.md` §3 yazım notu satır 60-61 |
| E4 | Sınır kararı #4: "donanım-type altına yalnız 3 bağlam açılır" | `donanım-anayasa.md` §10 satır 152 |
| E5 | Sınır kararı #5: ekran/kablo için ayrı bağlam AÇILMAZ — envanter UNKNOWN | `donanım-anayasa.md` §10 satır 153 |
| E6 | Çocuk index.md açık sorusu: envanter eksik (ekran, kablo, güç kaynağı) | `donanaım-type/index.md` frontmatter satır 17 |
| E7 | ELI15 metni ve 3 alt bağlam sayımı | `donanaım-type/index.md` satır 22-24 |
| E8 | Ölçüm eşikleri (SNR > 100 dB · THD+N < 0.01 % @ 1 kHz) bu domainin kabul kapısı | `donanım-anayasa.md` §10/3 satır 151 |
| E9 | Kademe-3 genişletme ön koşlu: "§4 kanıtları genişletilir" (audio) · "Q4 envanter" (hdd/ssd) | `donanım-anayasa.md` §9 satır 142-144 |
| E10 | audio türü kanıtları: AK4458 (32-bit 8ch DAC) · PCM3168A (6-in/8-out codec) | https://www.akm.com/us/en/products/audio/audio-dac/ak4458vn/ · https://www.ti.com/product/PCM3168A |

## §6 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q-T1 | hdd/ssd disk envanteri (marka/model) nedir? | **UNKNOWN** | Vault Steward (K001 Q4) |
| Q-T2 | hdd/ssd dışı türler (ekran, kablo, güç kaynağı) nereye gidiyor? | ⚠️ VERIFICATION REQUIRED | Vault Steward (K001 Q4) |
| Q-T3 | audio bağlamının kademe-3 kapsamı §4 kanıtlarıyla mı sınırlanacak? | ⚠️ VERIFICATION REQUIRED | audio-hw |
| Q-T4 | K002 ID GAP bu domaini etkiliyor mu? | ⚠️ VERIFICATION REQUIRED | registry §6 (K001 Q1) |

**Kapatma kuralı:** Q-T1 gelmeden §2/A2-A3 satırları UNKNOWN kalır; Q-T2 gelmeden
ekran/kablo için bağlam açılmaz (anayasa §10/5 — yasak).

---

## §7 Donanım tipi taksonomisi — sınıflandırma ekseni (derinleştirme 2026-10-13)

> **Bu bölüm §1-§6'ya dokunmaz** (satır-satır ekleme; ADR-042 revizyon kuralı). Amaç: "hangi
> donanım *türü* var, türler birbirine nasıl bağlanır" sorusunu taksonomi düzeyinde yanıtlamak.
> Kademe-3 torun dosyaları bu turda **üretilmez** — yalnızca mevcut dosyalara `[[...]]` bağlanır.

### §7.1 Sınıflandırma eksenleri

| Eksen | Soru | Ayırma çizgisi | Bu eksendeki dallar |
|---|---|---|---|
| X1 — Veri biçimi | Taşınan şey nedir? | sürekli zaman sinyali ↔ blok tabanlı veri | `audio/` (sinyal) · `hdd/`+`ssd/` (blok) |
| X2 — Fiziksel dönüşüm | Ne neye dönüşüyor? | elektrik↔akustik · manyetik↔elektrik · elektriksel yük durumu | DAC/codec/amp · HDD plaka · SSD NAND |
| X3 — Zaman davranışı | Ne kadar deterministik? | gerçek zamanlı kesintisiz akış ↔ isteğe bağlı erişim | ses yolu (zorunlu deadline) · depolama (esnek) |
| X4 — Durum (envanter) | Tür envanteri elimizde mi? | kanıtlanmış ↔ UNKNOWN | audio ✅ (§5/E10) · hdd/ssd ⚠️ (K001 Q4) |

**Kural:** Bu düğümde *içerik* (hangi marka disk, hangi hoparlör) değil, *sınıf* (hangi tür
donanım) kataloglanır; içerik envanteri kademe-3'e ve `donanım-core` envanterine aittir
(§3/B2). Bu ayrım §4/C3 ile aynı prensiptir.

### §7.2 Mini-ağaç (linkli görünüm)

```text
donanaım-type (kademe-2 — yazım yasağı §1 notu)
├── audio/            [[audio/index]]      ses sinyal yolu (sürekli zaman, gerçek zamanlı)
│   └── cipler/
│       ├── AK4458        [[audio/cipler/ak4458-dac]]        DAC (dönüştürme)
│       ├── PCM3168A      [[audio/cipler/pcm3168a-codec]]    codec (ADC+DAC, çoklu kanal)
│       └── TPA3255       [[audio/cipler/tpa3255-amplifier]] amplifikatör — REFERANS, Class D yasak
├── hdd/              [[hdd/index]]        blok depolama — mekanik (dönüşümlü) — envanter UNKNOWN
└── ssd/              [[ssd/index]]        blok depolama — katı hal — envanter UNKNOWN
```

### §7.3 Taksonomi kuralı — ne eklenir, ne eklenmez

| # | Kural | Dayanak |
|---|---|---|
| T1 | ~~Altına yalnız 3 bağlam açılır (`hdd` · `audio` · `ssd`)~~ **STALE (2026-10-13)** — 9 bağlam: `hdd` · `audio` · `ssd` · `display` · `power` · `cables` · `network` · `memory` · `cpu` · `PROPOSED — APPROVAL REQUIRED` (ADR-102 taslağı) | anayasa §10/3 (satır 195 — eski "§10/5 satır 152" atıfı line-drift: o satır artık §7 hata modeli) |
| T2 | ~~Ekran · kablo · güç kaynağı türü bağlamı AÇILMAZ~~ **STALE (2026-10-13)** — anayasa §10'da yasağı YOK; §10/3 açık soruydu, ADR-102 ile cevaplandı: display (2026-10-13) ve power (2026-10-13, önceki oturum) açıldı · `PROPOSED` | anayasa §10/3 (satır 195 — eski "§10/5 satır 153" atıfı line-drift: §10/5 artık ses donanımı sınır sorusu) |
| T3 | `audio/` altına çip dosyaları *envanter derin-dalı* olarak açılır (tartışma/BOM değil) | `audio/cipler/*` başlık notları |
| T4 | hdd/ssd altına dosya açma ön koşlu: marka/model envanteri (K001 Q4) | anayasa §9 — satır 142-144 (§5/E9) |
| T5 | `donanaım-type` yazımı düzeltilemez | v2 §3 KURAL 1 — anayasa §3 satır 60-61 |
| T6 | Amplifikatör topolojisi türü sabittir: **Class AB** (Class D yasak) — taksonomi bunu *tür* olarak kaydeder, tartışmaz | kök `CLAUDE.md` Sabitler (§4/C5) |

---

## §8 Ses çipleri taksonomisi (DAC · codec · amplifikatör)

### §8.1 Rol haritası

| Rol | Çip | Görev | Dosya (wiki-link) | Kayıt durumu |
|---|---|---|---|---|
| DAC (dönüştürücü) | AK4458 (AKM) | Dijital → analog, 8 kanal, 32-bit | [[audio/cipler/ak4458-dac]] | ✅ §5/E10 + doğrulama §2/8-9 |
| Codec (ADC+DAC) | PCM3168A (TI Burr-Brown) | 6-in/8-out, 24-bit, hem kayıt hem çıkış | [[audio/cipler/pcm3168a-codec]] | ✅ §5/E10 + doğrulama §2/10-11 |
| Amplifikatör | TPA3255 (TI) — **salt referans** | Class-D çıkış (entegre MOSFET) | [[audio/cipler/tpa3255-amplifier]] | ⚠️ referans — **Class D yasak** (§7/3-T6) |

### §8.2 DAC sınıfı — AK4458

| Alan | Taksonomik konum | Kanıt |
|---|---|---|
| Tür | Premium DAC · diferansiyel analog çıkış · TDM/I2S giriş | `ak4458-dac.md` §0 künye |
| Ölçü kaydı | SNR 115 dB · THD+N −107 dB @ 44.1 kHz · 768 kHz / 32-bit / 8 ch | doğrulama §2/9 (datasheet) |
| Yerleşim | `audio/cipler/` derin-dalı — kademe-4 (yaprak) | bu dosya §7.2 |
| Etkileşim | Amplifikatör öncesi son dijital→analog sınır | §10/3 |

### §8.3 Codec sınıfı — PCM3168A

| Alan | Taksonomik konum | Kanıt |
|---|---|---|
| Tür | Audio codec — 6 ADC girişi + 8 DAC çıkışı, 24-bit | `pcm3168a-codec.md` §0 künye |
| Ölçü kaydı | ADC SNR 107 dB · DAC SNR 112 dB · DAC 8–192 kHz · ADC 8–96 kHz | doğrulama §2/10-11 |
| Kontrol | SPI/I2C (MODE pin strap'i — açık soru) | `pcm3168a-codec.md` open_questions |
| Rol ayrımı | AK4458 "çıkış dönüşümü", PCM3168A "çıkış + kayıt dönüşümü" | §8.1 tablosu |

### §8.4 Amplifikatör sınıfı — durum notu

| Kayıt | Değer | Dayanak |
|---|---|---|
| Topoloji türü (proje sabiti) | **Class AB** — tartışması YASAK | kök `CLAUDE.md` Sabitler; §4/C5 |
| TPA3255 kaydı | 18–53.5 V besleme iddiası ⚠️ · "315W stereo / 600W mono" ürün başlığı · Class-D | doğrulama §2/12 (⚠️) + `tpa3255-amplifier.md` §0 |
| Kullanım biçimi | **Referans/alternatif teknoloji envanteri** — BOM'a alınmaz, şema üretilmez | `tpa3255-amplifier.md` en üst uyarı notu |
| Gerçek amplifikatör sınıfı | Class AB — devre detayı K002-electroncis alanında | §13/D7 |

### §8.5 Yasaklı / önerilen ikameler

| Yasak | Yerine önerilen | Dayanak |
|---|---|---|
| PCM5122 kullanımı | PCM3168A / AK4458 | kök `.ai/AGENTS.md` §17 Edge Case 8 |
| Class D topoloji | Class AB | kök `CLAUDE.md` Sabitler (§7/3-T6) |

---

## §9 Depolama taksonomisi (hdd · ssd)

### §9.1 Sınıf karşılaştırması

| Özellik ekseni | hdd — [[hdd/index]] | ssd — [[ssd/index]] |
|---|---|---|
| Fiziksel ilke | dönen plak + manyetik kayıt | katı hal (NAND) — hareketli parça yok |
| ELI15 (birebir) | "dönen disk… kapasite ve dayanıklılık" | "mekanik parça içermeyen hızlı depolama" |
| Tipik görev (vault kaydı) | arşiv / büyük ve yavaş okuma | sistem ve sıcak veri |
| Envanter (marka/model) | **UNKNOWN** — K001 Q4 | **UNKNOWN** — K001 Q4 |
| `depends_on` | `K000-isletim-sistemi` (frontmatter) | `K000-isletim-sistemi` (frontmatter) |
| Doğrulama zemini | SMART / TRIM kapısı → [[donanaım-type-dogrulama]] §9 | SMART / TRIM kapısı → [[donanaım-type-dogrulama]] §9 |

### §9.2 Depolama altındaki bilinmeyenler (dürüstlük)

| # | Bilinmeyen | Etiket |
|---|---|---|
| S1 | hdd marka/model/kapasite | **UNKNOWN** (Q-T1) |
| S2 | ssd marka/model/kapasite | **UNKNOWN** (Q-T1) |
| S3 | arayüz sınıfı (SATA/NVMe/USB…) | ⚠️ VERIFICATION REQUIRED — envantere bağlı |
| S4 | SMART eşik değerleri (üreticiye özgü) | ⚠️ VERIFICATION REQUIRED — üretim boyunca okunacak |

> S3-S4 **uydurulmaz**: envanter gelmeden bu alanlar kapalı kalır (§6 kapatma kuralı).

---

## §10 Sinyal/depolama zinciri: depolama → DAC → amplifikatör → hoparlör

### §10.1 Zincir şeması

```text
[hdd/ veya ssd/]  --blok veri-->  [yazılım köprüsü]  --dijital ses-->  [DAC/codec]
      §9                  okuma+decode             K003-audio         §8.1
                                                                          |
                                                          analog sinyal (düşük seviye)
                                                                          v
                                                          [amplifikatör — Class AB]
                                                                          |
                                                          yüksek akım/signal
                                                                          v
                                                              [hoparlör] --> akustik
```

### §10.2 Halka halka tablo

| # | Halka | Taşıdığı şey | Dönüşüm | Tip kaynağı | Sonraki halkaya etkisi |
|---|---|---|---|---|---|
| Z1 | Depolama (hdd/ssd) | dosya/blok veri | yok (ham saklama) | [[hdd/index]] · [[ssd/index]] | okuma sürekliliği → yazılım tamponu davranışı |
| Z2 | Yazılım köprüsü (decode/routing) | blok → örnek akışı | veri → dijital ses karesi | K003-audio (**bu düğümün dışı**, §3/B5) | DAC'a giden kare bütünlüğü/saat |
| Z3 | DAC / codec çıkışı | dijital kare → analog voltaj | D→A | [[audio/cipler/ak4458-dac]] · [[audio/cipler/pcm3168a-codec]] | analog gürültü tabanı + THD → amplifikatör girişini sınırlar |
| Z4 | Amplifikatör (Class AB) | düşük seviye analog → güç | gerilim/akım kazancı | topoloji sabiti (§8.4) | hoparlöre iletilen güç + DC güvenliği |
| Z5 | Hoparlör (uç nokta) | elektrik → akustik | elektro-mekanik | **tip envanteri UNKNOWN** ⚠️ | (zincir sonu) |

### §10.3 Zincir kuralı — zayıf halka ilkesi

Zincirin uçtan-uca ölçüm kapısı [[donanaım-type-dogrulama]] §10'dur. Eşikler (SNR > 100 dB ·
THD+N < 0.01 % @ 1 kHz — §5/E8) zincirin **toplam** performansına uygulanır: en zayıf halka
zincir kotasını belirler. Bu nedenle Z3'ün datasheet değeri tek başına "geçmiştir" anlamına
gelmez — kapı Z1→Z5 bütünüyle kapatılır.

**Hoparlör notu ⚠️:** Hoparlör/kablo türü envanteri bu düğümde yoktur; ekran/kablo türleri
zaten bağlam dışıdır (§2/A6). Zincir şemasında hoparlör yalnızca *uç nokta* olarak gösterilir,
tipi ayrı bağlam olarak AÇILMAZ → ⚠️ VERIFICATION REQUIRED.

---

## §11 Her tipin bir sonraki katmana etkisi

| # | Tip | Etki mekanizması | Etkilediği sonraki katman | İşaret | Doğrulama kapısı |
|---|---|---|---|---|---|
| F1 | hdd | okuma sürekliliği / tampon underrun riski yönü | yazılım köprüsü (Z2) | ⚠️ büyüklük UNKNOWN | [[donanaım-type-dogrulama]] §9 (SMART) · §10 (uçtan uca) |
| F2 | ssd | düşük erişim gecikmesi → daha küçük tamponla kesintisiz akış olasılığı (tasarım mantığı, ölçülmedi) | yazılım köprüsü (Z2) | ⚠️ ölçülmedi | [[donanaım-type-dogrulama]] §9 · §10 |
| F3 | AK4458 (DAC) | SNR 115 dB / THD+N −107 dB → amplifikatör girişinde gürültü-tabanı tavanı | amplifikatör (Z4) | ✅ datasheet (ölçülmedi) | [[donanaım-type-dogrulama]] §6 |
| F4 | PCM3168A (codec) | ADC 107 dB / DAC 112 dB → kayıt ve çıkış kanalları arası eşik | yazılım köprüsü + amplifikatör (Z2/Z4) | ✅ datasheet (ölçülmedi) | [[donanaım-type-dogrulama]] §7 |
| F5 | Amplifikatör (Class AB) | kazanç + DC güvenliği → hoparlör sürücüsü | hoparlör (Z5) | ✅ topoloji sabiti; güç ölçümü ⚠️ | [[donanaım-type-dogrulama]] §8 (referans) |
| F6 | Hoparlör | akustik dönüşüm → duyulan sonuç | (zincir sonu) | ⚠️ envanter UNKNOWN | §10.3 — zincir kapısı |

**Yön kuralı:** Etki **her zaman ileri** doğru akar (depolama → ses → akustik); geriye doğru
etki yalnızca *geri besleme/tasarım* düzeyindedir ve bu düğümün kapsamı değildir (§3/B5-B7).

---

## §12 Tip-seçim matrisi (taslak — envantere bağlanmamış)

> Bu matris "hangi durumda hangi tip" sorusunun **katalog** karşılığıdır; satın alma/BOM kararı
> üretmez. Envanter (Q-T1) gelmeden hdd/ssd satırları uygulanamaz.

| # | İhtiyaç senaryosu | Aday tip | Dayanak | Durum |
|---|---|---|---|---|
| M1 | Çok kanallı, yüksek çözünürlüklü analog çıkış | `audio` → DAC sınıfı (AK4458) | §8.2 · doğrulama §2/8-9 | ✅ kanıtlanmış sınıf |
| M2 | Eşzamanlı çoklu giriş kaydı + çıkış | `audio` → codec sınıfı (PCM3168A) | §8.3 · doğrulama §2/10-11 | ✅ kanıtlanmış sınıf |
| M3 | Güçlendirme (hoparlör sürücüsü) | `audio` → amplifikatör sınıfı **Class AB** | §8.4 · kök `CLAUDE.md` Sabitleri | ✅ sabit — tartışma YASAK |
| M4 | Büyük kapasite / arşiv okuma | `hdd` | [[hdd/index]] ELI15 | ⚠️ sınıf var, envanter UNKNOWN |
| M5 | Sistem + sıcak veri (hızlı erişim) | `ssd` | [[ssd/index]] ELI15 | ⚠️ sınıf var, envanter UNKNOWN |
| M6 | Ekran / kablo / güç kaynağı türü | — | anayasa §10/5 | ❌ kapsam dışı — bağlam AÇILMAZ |
| M7 | Amplifikatör topolojisi seçimi | Class AB (tek seçenek) | §7/3-T6 | ✅ kapalı kapı |

---

## §13 Tip-bağımlılıkları tablosu

| # | Bağımlılık | Tür | Yön | Dayanak |
|---|---|---|---|---|
| D1 | DAC → amplifikatör | teknolojik (sinyal seviyesi uyumu) | ileri (Z3→Z4) | §10.2 Z3-Z4; arayüz seviyesi tasarımı ⚠️ (K002 alanı) |
| D2 | amplifikatör → hoparlör | teknolojik (güç/empedans uyumu) | ileri (Z4→Z5) | §10.2; hoparlör tipi UNKNOWN (§10.3) |
| D3 | depolama → yazılım köprüsü | veri (okuma sürekliliği) | ileri (Z1→Z2) | F1/F2; büyüklük ölçülmedi |
| D4 | hdd ↔ ssd | alternatif (aynı sınıf, farklı ilke) | kardeş | §9.1 · §4/C2 |
| D5 | audio ↔ hdd/ssd | yok (bağımsız dallar) | — | §4/C1 — yalnız ortak üst düğüm |
| D6 | Tüm tipler → K000-isletim-sistemi | üst-yapı (sürücü/FS) | dikey | `hdd/index` · `ssd/index` · `audio/index` frontmatter `depends_on` |
| D7 | amplifikatör sınıfı → K002-electroncis/devre-ve-analog | devre tasarımı | çapraz | [[../../K002-electroncis/devre-ve-analog/index]] (sınırlar §3/B7) |
| D8 | audio çıktısı → K003-audio/output | yazılım çıktısının fiziksel muhatabı | çapraz | [[../../K003-audio/output/index]]; anayasa §2 (B5) |
| D9 | Tüm tipler → K001-donanım/donanım-core | envanter/ölçü/sağlık | dikey | §3/B2 |

### §7.4 Kademe ve dosya-yerleşimi tablosu

| Kademe | Dosya | group | Rol |
|---|---|---|---|
| 2 | `donanaım-type-kapsam.md` (bu dosya) | mimari | tür kapsamı + taksonomi |
| 2 | `donanaım-type-dogrulama.md` | kanit | tür doğrulama kapıları |
| 2 | `index.md` | mimari | klasör künyesi + çocuk kataloğu |
| 3 | `audio/index.md` | mimari | ses donanımı sınıfı künyesi |
| 3 | `hdd/index.md` | mimari | mekanik depolama sınıfı künyesi |
| 3 | `ssd/index.md` | mimari | katı hal depolama sınıfı künyesi |
| 4 | `audio/cipler/ak4458-dac.md` | mimari | DAC teknoloji derin-dalı |
| 4 | `audio/cipler/pcm3168a-codec.md` | mimari | codec teknoloji derin-dalı |
| 4 | `audio/cipler/tpa3255-amplifier.md` | mimari | amplifikatör referans derin-dalı (Class D yasak) |

**Yerleşim kuralı:** Kademe-4 yalnız `audio/cipler/` altında açılmıştır; `hdd/` ve `ssd/`
altında kademe-4 **yoktur** (ön koşul: envanter — §7/3-T4). İki-eksen `group` alanı her
dosyanın frontmatter'ında zorunludur (ADR-097 §5.5 · registry §8.1).

---

### §8.6 Çip bazında karşılaştırma matrisi

| Ölçüt | AK4458 | PCM3168A | TPA3255 (referans) |
|---|---|---|---|
| Sınıf | DAC | codec (ADC+DAC) | Class-D amplifikatör |
| Yön | dijital → analog | çift yönlü (kayıt + çıkış) | analog → güç |
| Kanal | 8 çıkış | 6 giriş + 8 çıkış | 2 (stereo) / 1 (mono) |
| Çözünürlük (kayıt) | 32-bit | 24-bit | — (analog giriş) |
| SNR kaydı | 115 dB (§8.2) | ADC 107 / DAC 112 dB (§8.3) | ⚠️ SNR kaydı bu vault'ta yok |
| Zincirdaki halka | Z3 | Z3 (+ Z2 kayıt yönü) | Z4 **(ama Class D yasak — §7/3-T6)** |
| Doğrulama kapısı | [[donanaım-type-dogrulama]] §6 | [[donanaım-type-dogrulama]] §7 | [[donanaım-type-dogrulama]] §8 |
| Dosya | [[audio/cipler/ak4458-dac]] | [[audio/cipler/pcm3168a-codec]] | [[audio/cipler/tpa3255-amplifier]] |

**Okuma notu:** AK4458 ile PCM3168A **rakip değil, tamamlayıcıdır** — biri çıkış dönüşümüne,
diğeri kayıt+çoklu çıkışa bakar (§8.3 rol ayrımı). TPA3255 satırı ise *seçim* satırı değildir:
proje amplifikatörü Class AB'dir (§8.4).

### §8.7 Çip içi sinyal yolu — taksonomik konumlandırma

```text
dijital ses karesi (I2S/TDM)
        |
        +--> AK4458  : 8 ch D->A  --> analog diferansiyel çıkış   (Z3a)
        |
        +--> PCM3168A: 8 ch D->A  --> analog çıkış                (Z3b)
                 ^                              |
        analog 6 ch giriş --> A->A dönüşüm      (kayıt yönü, Z2'ye geri)
                 |
                 v
        Class AB amplifikatör (topoloji sabiti)  (Z4)
                 |
                 v
             hoparlör (Z5, tip UNKNOWN)
```

- **Z3a/Z3b ayrımı:** Çıkışta iki DAC sınıfı çip vardır; hangisinin hangi kanal grubuna
  bağlanacağı **tasarım kararıdır ve bu düğümün dışındadır** (§3/B5-B7) → ⚠️ VERIFICATION REQUIRED.
- **Kayıt yönü:** PCM3168A'nın ADC kolu ses yolu *girişine* bakar; taksonomide `audio/` içinde
  kalır çünkü fiziksel sınıf aynıdır (ses donanımı).
- **Saat ağı:** Her iki çip de ortak dijital saate bağımlıdır — saat ağının sahibi
  donanım-kernel/kernel kardeşidir (§3/B1) → bu dosyada tanımlanmaz.

---

### §9.3 Depolama sınıflandırma eksenleri (hdd · ssd)

| Eksen | hdd | ssd | İşaret |
|---|---|---|---|
| Erişim birimi | blok (dosya sistemi üstü) | blok (dosya sistemi üstü) | ortak — sınıf tanımı |
| Fiziksel erişim | dönen plak + kafa pozisyonlama | doğrudan hücre erişimi | sınıf tanımı (ELI15 birebir) |
| Hareketli parça | VAR | YOK | sınıf tanımı |
| Arayüz sınıfı | ⚠️ envantere bağlı (kapsam §9.2/S3) | ⚠️ envantere bağlı (kapsam §9.2/S3) | UNKNOWN |
| Kapasite | ⚠️ UNKNOWN | ⚠️ UNKNOWN | K001 Q4 |
| Dayanıklılık/yorulma ölçütü | mekanik yorulma + SMART | yazma-tükenme (NAND) + SMART | ⚠️ eşik üreticiye özgü (doğrulama §9/3) |
| TRIM davranışı | TRIM anlamsız/harcanan (dönüşümlü ortam) ⚠️ | TRIM geri kazanım sağlar | ⚠️ — spesifikasyon okunmadı (doğrulama §9/4) |
| Zincirdeki yeri | Z1 (kaynak) | Z1 (kaynak) | §10.2 |

**Sınır notu:** Depolamanın *dosya sistemi/sürücü* tarafı K000-isletim-sistemi'ne (§3/B6) ve
`depends_on` üzerinden bu sınıfın ön-koşuludur; depolamanın *sağlık ölçümü* `donanım-core`
envanteriyle (§3/B2) çakışmaz — ölçüm kapısı burada, envanter orada tanımlıdır.

---

### §10.4 Zincir üzerindeki saat/veri etkileşimi (tip → tip)

| # | Etkileşim | Taşıyan | Tip düzeyi sonuç |
|---|---|---|---|
| Z1→Z2 | dosya okuma → örnek akışı | okuma sürekliliği | depolama tipi bozulursa (hdd SSD'ye göre daha kesintili olabilir ⚠️ ölçülmedi) tampon davranışı değişir |
| Z2→Z3 | kare → DAC | dijital saat (MCLK/BCLK/FS) | DAC tipi saat beklentisini belirler (AK4458 768 kHz üst sınırı §8.2) |
| Z3→Z4 | analog seviye → kazanç | voltaj seviyesi | DAC çıkış seviyesi amplifikatör kazanç planını belirler ⚠️ (değer tasarımı K002) |
| Z4→Z5 | güç → akustik | akım/empedans | amplifikatör sınıfı (Class AB) hoparlör empedans uyumunu belirler ⚠️ |

**İşaret okuma:** "⚠️ ölçülmedi/tasarım" ibaresi olan hiçbir satır ölçüm sonucu değildir;
kapılar `donanaım-type-dogrulama.md` §6-§10'da UYGULANMADIR.

### §10.5 Zincir riskleri (tip bazlı — katalog)

| # | Risk | Kaynak halka | Etkilediği tip | Azaltma kapısı |
|---|---|---|---|---|
| R1 | okuma kesintisi → underrun | Z1 | hdd/ssd | doğrulama §10/2 |
| R2 | DAC gürültü tabanı zinciri sınırlar | Z3 | AK4458/PCM3168A | doğrulama §6-§7 |
| R3 | DC offset hoparlöre zarar verir | Z4 | amplifikatör | doğrulama §10/4 — **kural 10: >0.5 V → röle** |
| R4 | hoparlör/empedans uygunsuzluğu | Z5 | hoparlör (UNKNOWN) | kapsam §10.3 ⚠️ |
| R5 | envantere bağlı eşiklerin uydurulması | Z1 | hdd/ssd | §9.2 dürüstlük listesi |

---

### §11.1 Etki matrisi — okuma notları (§11'e ek)

| Not | Açıklama |
|---|---|
| N1 | §11'deki "✅ datasheet" işareti **kayıt** demektir — tezgâh ölçümü DEĞİLDİR. |
| N2 | Etki yalnız ileri akar; geri besleme/tasarım geri-etkisi bu düğümün kapsamı değildir (§11 kuralı). |
| N3 | F1/F2 satırlarının büyüklüğü bilinmiyor → kapı: doğrulama §9 (SMART) + §10 (uçtan uca). |
| N4 | F5'te topoloji sabit olduğu için etki *yönü* sabittir; büyüklük (kazanç/güç) ölçülmemiştir. |
| N5 | Her satır en az bir doğrulama kapısına bağlanmıştır — kapısı olmayan etki satırı bu dosyaya yazılmaz. |

---

### §12.2 Anti-matris — kaçınılan seçimler

| # | Kaçınılan | Neden | Dayanak |
|---|---|---|---|
| X1 | PCM5122 kullanımı | yasaklı ikame | `.ai/AGENTS.md` §17 Edge Case 8 |
| X2 | Class D amplifikatör seçimi | topoloji yasağı | kök `CLAUDE.md` Sabitler · §7/3-T6 |
| X3 | Ekran/kablo/güç kaynağı için bağlam açmak | anayasa yasağı | §2/A6 · §7/3-T2 |
| X4 | hdd/ssd marka/model iddiası | envanter yok | §9.2 · §6 kapatma kuralı |
| X5 | `donanaım-type` yazımını düzeltmek | v2'den birebir koruma | §1 yazım notu · §7/3-T5 |
| X6 | DAC çiplerinden birini "tek doğru" ilan etmek | tamamlayıcı roller (§8.6) | §8.1-§8.3 |

---

### §13.10 Bağımlılık grafiği (görsel)

```text
                  K001-donanım/donanım-core (D9: envanter/ölçü)
                          ^
                          |
   K000-isletim-sistemi --+--> hdd/ ----\
             (D6)         |             \
                          +--> ssd/ ---->+--> [Z1] --> (Z2 K003) --> DAC/codec [Z3]
                          |                                             |
                          +--> audio/ --> cipler/ (AK4458·PCM3168A) -----+
                                                            |
                                                  amplifikatör (Class AB)
                                                            |
                                                    hoparlör (Z5, UNKNOWN)
                                                            |
                                        K002-electroncis/devre-ve-analog (D7)  <-- devre
                                        K003-audio/output (D8)                  <-- yazılım çıkış
```

### §13.11 Çakışma çözüm kuralları

| # | Çakışma | Çözüm | Dayanak |
|---|---|---|---|
| K1 | Kapsam (bu dosya) ↔ Doğrulama (kapılar) | kapı *sonucu* doğrulamada, kapı *kapsamı* burada | §5/§12 ayrımı |
| K2 | tür sınıfı ↔ envanter içeriği | sınıf burada, envanter `donanım-core` | §7.1 X4 · §3/B2 |
| K3 | Topoloji tartışması ↔ taksonomi kaydı | sabit kaydedilir, tartışılır | §4/C5 · §7/3-T6 |
| K4 | Çapraz-K bağlantı ↔ komşu kapsam | yalnız bağlantı satırı; içerik komşuda | §13/D7-D8 · §3 |
| K5 | Yazım düzeltme isteği ↔ kaynak sadakati | yazım korunur | §1 yazım notu · §7/3-T5 |

### §7.5 Bu taksonomide sık yapılan hatalar (denetim listesi)

| # | Hata | Doğrusu | Dayanak |
|---|---|---|---|
| H1 | "donanım-type" yazmak | `donanaım-type` (kaynak sadakati) | §1 yazım notu · §7/3-T5 |
| H2 | Marka/model'i taksonomiye yazmak | sınıf yazılır, envanter `donanım-core`'a | §7.1 X4 · §3/B2 |
| H3 | DAC çiplerinden birini çıkarmak | ikisi de tamamlayıcıdır (§8.6) | §8.1-§8.3 |
| H4 | TPA3255'ı "amplifikatör seçimi" sanmak | referanstır; proje Class AB (§8.4) | §2/A4 · §7/3-T6 |
| H5 | hdd/ssd için kademe-4 dosyası açmak | ön koşul: envanter (§7/3-T4) | anayasa §9 · §5/E9 |
| H6 | Zinciri yazılım olmadan anlatmak | Z2 halkası K003'tür (§3/B5) — dışarıda bırakılmaz, işaretlenir | §10.2 |
| H7 | Ölçümü kanıt sanmak | bu dosyada ölçüm YOK; kapılar doğrulama dosyasındadır | §15 dürüstlük |

---

## §14 Kaynak erişim kaydı (bu tur — 2026-10-13, PowerShell HEAD)

> Yöntem: `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`.
> Aşağıdakiler **erişim doğrulamasıdır** (HTTP durumu), içerik iddiası değildir; içerik
> iddiaları §5 ve `donanaım-type-dogrulama.md` §2/§12 tablolarındadır.

| # | URL | HTTP | Kullanım amacı |
|---|---|---|---|
| W1 | https://www.akm.com/us/en/products/audio/audio-dac/ak4458vn/ | 200 ✅ | DAC sınıfı kaydı (§8.2 · §5/E10) |
| W2 | https://www.ti.com/product/PCM3168A | 200 ✅ | codec sınıfı kaydı (§8.3 · §5/E10) |
| W3 | https://www.ti.com/product/TPA3255 | 200 ✅ | amplifikatör referansı (§8.4 · doğrulama §2/12 ⚠️) |
| W4 | https://www.t13.org/ | 200 ✅ | depolama komut-seti standartları kurumu (§9.1 SMART zemini) |
| W5 | https://nvmexpress.org/ | 200 ✅ | NVMe/TRIM spesifikasyonu yayıncısı (§9.1) |
| W6 | https://www.aes.org/ | **403 ❌** | erişilemedi → **bu tur kullanılmadı** (uydurulmaz) |

---

## §15 Dürüstlük bloğu (bu derinleştirme)

**Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler 0 uydurma URL/ölçüm içerir;
doğrulanmayan iddialar ⚠️ işaretli.**

| Kontrol | Sonuç |
|---|---|
| §1-§6 silindi mi? | HAYIR — aynen korundu (satır-satır ekleme) |
| Yeni uydurma dosya/klasör | 0 (beyazliste: yalnız bu 2 dosya) |
| Yeni URL | 6 kayıt (§14) — 5× HTTP 200, 1× HTTP 403 (kullanılmadı) |
| Yapılan ölçüm | **0** — hiçbir fiziksel ölçüm yapılmadı; tüm ölçüm iddiaları `donanaım-type-dogrulama.md` §6-§10'da UYGULANMADI durumundadır |
| UNKNOWN alanlar | §9.2 (S1-S4) · §10.3 hoparlör · §12 M4/M5 — kapatma kuralı §6 |
| git commit | ATILMADI (subagent yasağı) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode (tek Authority)
