---
title: "donanaım-type Örnekler — Taksonomi Kayıt Örüntüleri ve Zincir Şablonları"
type: architecture-document
node_id: K001-donanım/donanaım-type
node_type: architecture-document
status: active
parent_id: K001-donanım
children: []
depends_on:
  - K001-donanım
  - K000-isletim-sistemi
evidence:
  - ".ai/architecture/K001-donanım/donanaım-type/donanaım-type-kapsam.md"
  - ".ai/architecture/K001-donanım/donanaım-type/donanaım-type-dogrulama.md"
  - ".ai/architecture/K001-donanım/donanaım-type/audio/index.md"
  - ".ai/architecture/K001-donanım/donanaım-type/hdd/index.md"
  - ".ai/architecture/K001-donanım/donanaım-type/ssd/index.md"
validation: "≥500 satır · Authority satırı tam 1 · parent: 0 · tüm kayıt örüntüleri UNKNOWN/SHABLON dolu · ölçüm bu oturumda"
open_questions:
  - "hdd/ssd marka/model envanteri UNKNOWN — K001 Q4 (kapsam §9.2 S1-S2)"
  - "Z5 hoparlör tip envanteri yok — ⚠️ VERIFICATION REQUIRED (kapsam §10.3)"
  - "9 bağlam genişlemesi PROPOSED — APPROVAL REQUIRED (ADR-102 taslağı, kapsam §7.3 T1)"
date: 2026-10-15
updated: 2026-10-15
group: kod
---

# donanaım-type — Örnekler (Taksonomi Kayıt Örüntüleri ve Zincir Şablonları)

Bu dosya iki tür örnek taşır: **(A)** taksonomi/zincir başına **şablon** kayıt
örüntüleri (tümü `UNKNOWN`/`SHABLON` dolu — gerçek envanter yoktur) ve
**(B)** kapsam §7–§15 tablolarının **aktarım** kartları.

> **Örnek dürüstlüğü:** hdd/ssd envanteri **UNKNOWN** (K001 Q4); Z5 hoparlör
> tipi **yok**; hiçbir kayıt gerçek bir cihazı temsil etmez. `marka`/`model`/
> `kapasite` alanları bilinçli boş bırakılmıştır; doldurulması K001 Q4
> kapanışına aittir. Kaynak: `donanaım-type-kapsam.md` (§7–§15).

## A. Sınıflandırma Ekseni Örnekleri (kapsam §7.1 — aktarım)

### A1. Sınıflandırma eksenleri (kapsam §7.1 birebir)

| Eksen | Soru | Ayırma çizgisi | Bu eksendeki dallar |
|---|---|---|---|
| X1 — Veri biçimi | Taşınan şey nedir? | sürekli zaman sinyali ↔ blok tabanlı veri | `audio/` (sinyal) · `hdd/`+`ssd/` (blok) |
| X2 — Fiziksel dönüşüm | Ne neye dönüşüyor? | elektrik↔akustik · manyetik↔elektrik · elektriksel yük durumu | DAC/codec/amp · HDD plaka · SSD NAND |
| X3 — Zaman davranışı | Ne kadar deterministik? | gerçek zamanlı kesintisiz akış ↔ isteğe bağlı erişim | ses yolu (zorunlu deadline) · depolama (esnek) |
| X4 — Durum (envanter) | Tür envanteri elimizde mi? | kanıtlanmış ↔ UNKNOWN | audio ✅ (§5/E10) · hdd/ssd ⚠️ (K001 Q4) |

**Kural (taşıma):** bu düğümde *içerik* değil *sınıf* kataloglanır; içerik
envanteri kademe-3'e ve `donanım-core` envanterine aittir (kapsam §3/B2).

### A2. [KALIP-PLANNED] eksen-bazlı tip kayıt şablonu (26 alan değil — 10 alan)

```yaml
# KALIP-PLANNED — sınıf kaydı, cihaz kaydı DEĞİL (§7.1 kuralı).
tip_sinifi: UNKNOWN        # audio | hdd | ssd | UNKNOWN
ekseni_x1: UNKNOWN         # sinyal | blok | UNKNOWN
ekseni_x2: UNKNOWN         # donusum tanimi — UNKNOWN
ekseni_x3: UNKNOWN         # gercek-zamanli | esnek | UNKNOWN
ekseni_x4: kanitli | UNKNOWN
envanter: UNKNOWN          # K001 Q4 — hdd/ssd icin daima UNKNOWN
kaynak_dosya: UNKNOWN      # wiki-link hedefi
durum: SHABLON
karar_tarihi: UNKNOWN
```

> Bu şablon **cihaz** (marka/model) değil **sınıf** kaydıdır; §7.1 kuralı
> gereği `marka` alanı taşımaz. Cihaz kayıt örüntüsü arayanlar:
> `donanım-devices-ornekler.md` (26 alan).

### A3. Mini-ağaç aktarımı (kapsam §7.2 birebir)

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

### A4. Taksonomi kuralı kartları (kapsam §7.3 T1–T6 — özet aktarım)

| # | Kural | Özet |
|---|---|---|
| T1 | 9 bağlam (hdd · audio · ssd · display · power · cables · network · memory · cpu) | **PROPOSED — APPROVAL REQUIRED** (ADR-102 taslağı) |
| T2 | display (2026-10-13) ve power (önceki oturum) açıldı · kalanlar PROPOSED | anayasa §10/3 |
| T3 | `audio/` altına çip dosyaları *envanter derin-dalı* olarak açılır | `audio/cipler/*` başlık notları |
| T4 | hdd/ssd altına dosya açma ön koşlu: marka/model envanteri (K001 Q4) | anayasa §9 |
| T5 | `donanaım-type` yazımı düzeltilemez | v2 §3 KURAL 1 |
| T6 | Amplifikatör topolojisi **Class AB** (Class D yasak) — tartışmaz | kök `CLAUDE.md` Sabitler |

**Bu dosyada T1–T4 uygulanmaz** (yeni bağlam/dosya açılmaz — yalnız aktarım).

## B. Ses Çipleri Taksonomi Örnekleri (kapsam §8)

### B1. Rol haritası (kapsam §8.1 birebir)

| Rol | Çip | Görev | Dosya (wiki-link) | Kayıt durumu |
|---|---|---|---|---|
| DAC (dönüştürücü) | AK4458 (AKM) | Dijital → analog, 8 kanal, 32-bit | [[audio/cipler/ak4458-dac]] | ✅ §5/E10 + doğrulama §2/8-9 |
| Codec (ADC+DAC) | PCM3168A (TI Burr-Brown) | 6-in/8-out, 24-bit, hem kayıt hem çıkış | [[audio/cipler/pcm3168a-codec]] | ✅ §5/E10 + doğrulama §2/10-11 |
| Amplifikatör | TPA3255 (TI) — **salt referans** | Class-D çıkış (entegre MOSFET) | [[audio/cipler/tpa3255-amplifier]] | ⚠️ referans — **Class D yasak** (§7/3-T6) |

### B2. DAC sınıfı — AK4458 (kapsam §8.2 birebir)

| Alan | Taksonomik konum | Kanıt |
|---|---|---|
| Tür | Premium DAC · diferansiyel analog çıkış · TDM/I2S giriş | `ak4458-dac.md` §0 künye |
| Ölçü kaydı | SNR 115 dB · THD+N −107 dB @ 44.1 kHz · 768 kHz / 32-bit / 8 ch | doğrulama §2/9 (datasheet) |
| Yerleşim | `audio/cipler/` derin-dalı — kademe-4 (yaprak) | bu dosya §A3 |
| Etkileşim | Amplifikatör öncesi son dijital→analog sınır | §10/3 |

### B3. Codec sınıfı — PCM3168A (kapsam §8.3 birebir)

| Alan | Taksonomik konum | Kanıt |
|---|---|---|
| Tür | Audio codec — 6 ADC girişi + 8 DAC çıkışı, 24-bit | `pcm3168a-codec.md` §0 künye |
| Ölçü kaydı | ADC SNR 107 dB · DAC SNR 112 dB · DAC 8–192 kHz · ADC 8–96 kHz | doğrulama §2/10-11 |
| Kontrol | SPI/I2C (MODE pin strap'i — açık soru) | `pcm3168a-codec.md` open_questions |
| Rol ayrımı | AK4458 "çıkış dönüşümü", PCM3168A "çıkış + kayıt dönüşümü" | §8.1 tablosu |

### B4. Amplifikatör sınıfı — durum notu (kapsam §8.4 birebir)

| Kayıt | Değer | Dayanak |
|---|---|---|
| Topoloji türü (proje sabiti) | **Class AB** — tartışması YASAK | kök `CLAUDE.md` Sabitler; §4/C5 |
| TPA3255 kaydı | 18–53.5 V besleme iddiası ⚠️ · "315W stereo / 600W mono" ürün başlığı · Class-D | doğrulama §2/12 (⚠️) + `tpa3255-amplifier.md` §0 |
| Kullanım biçimi | **Referans/alternatif teknoloji envanteri** — BOM'a alınmaz, şema üretilmez | `tpa3255-amplifier.md` en üst uyarı notu |
| Gerçek amplifikatör sınıfı | Class AB — devre detayı K002-electroncis alanında | §13/D7 |

### B5. Yasaklı / önerilen ikameler (kapsam §8.5 birebir)

| Yasak | Yerine önerilen | Dayanak |
|---|---|---|
| PCM5122 kullanımı | PCM3168A / AK4458 | kök `.ai/AGENTS.md` §17 Edge Case 8 |
| Class D topoloji | Class AB | kök `CLAUDE.md` Sabitler (§7/3-T6) |

### B6. [KALIP-PLANNED] yasak-kapı kontrol kalıbı

```cpp
// KALIP-PLANNED — ADR-038 H001 + §8.5: PCM5122 yasak, Class D yasak.
constexpr bool isForbiddenCodec(const char* part) noexcept {
    // yalniz iki yasak adi; gerisi UNKNOWN sayilir (uydurma ad yasak).
    return equals(part, "PCM5122");   // -> PCM3168A / AK4458 öner (§8.5)
}
static_assert(isForbiddenCodec("PCM5122"),
              "ADR-038 H001: PCM5122 yasak kapisi her zaman kapali olmali");
// TPA3255 salt referanstir: BOM'a ALINMAZ (B4) — bu kalip uretimde kullanilmaz.
```

> `equals` yardımcısının kendisi bu dosyada **tanımlanmaz** (şablon bütünlüğü
> için yer tutucu); gerçek derleme için `donanım-devices-ornekler.md` §G
> disiplini geçerlidir.

## C. Depolama Taksonomi Örnekleri (kapsam §9)

### C1. Sınıf karşılaştırması (kapsam §9.1 birebir)

| Özellik ekseni | hdd — [[hdd/index]] | ssd — [[ssd/index]] |
|---|---|---|
| Fiziksel ilke | dönen plak + manyetik kayıt | katı hal (NAND) — hareketli parça yok |
| ELI15 (birebir) | "dönen disk… kapasite ve dayanıklılık" | "mekanik parça içermeyen hızlı depolama" |
| Tipik görev (vault kaydı) | arşiv / büyük ve yavaş okuma | sistem ve sıcak veri |
| Envanter (marka/model) | **UNKNOWN** — K001 Q4 | **UNKNOWN** — K001 Q4 |
| `depends_on` | `K000-isletim-sistemi` (frontmatter) | `K000-isletim-sistemi` (frontmatter) |
| Doğrulama zemini | SMART / TRIM kapısı → [[donanaım-type-dogrulama]] §9 | SMART / TRIM kapısı → [[donanaım-type-dogrulama]] §9 |

### C2. Depolama bilinmeyenleri (kapsam §9.2 birebir — dürüstlük)

| # | Bilinmeyen | Etiket |
|---|---|---|
| S1 | hdd marka/model/kapasite | **UNKNOWN** (Q-T1) |
| S2 | ssd marka/model/kapasite | **UNKNOWN** (Q-T1) |
| S3 | arayüz sınıfı (SATA/NVMe/USB…) | ⚠️ VERIFICATION REQUIRED — envantere bağlı |
| S4 | SMART eşik değerleri (üreticiye özgü) | ⚠️ VERIFICATION REQUIRED — üretim boyunca okunacak |

### C3. [KALIP-PLANNED] depolama kayıt şablonu (envanter UNKNOWN dolu)

```yaml
# KALIP-PLANNED — bu kayıt GERCEK BIR DISK TEMSIL ETMEZ (kapsam §9.2).
sinif: hdd | ssd          # (biri seçilecek — secim YOK)
marka: UNKNOWN            # Q-T1
model: UNKNOWN            # Q-T1
kapasite: UNKNOWN         # Q-T1
arayuz: UNKNOWN           # S3 — envantere bagli
smart_esikleri: UNKNOWN   # S4 — uretim boyunca okunacak
depends_on: K000-isletim-sistemi
durum: SHABLON
```

## D. Sinyal/Zincir Şablonları (kapsam §10)

### D1. Zincir şeması (kapsam §10.1 birebir)

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

### D2. Halka halka tablo (kapsam §10.2 birebir)

| # | Halka | Taşıdığı şey | Dönüşüm | Tip kaynağı | Sonraki halkaya etkisi |
|---|---|---|---|---|---|
| Z1 | Depolama (hdd/ssd) | dosya/blok veri | yok (ham saklama) | [[hdd/index]] · [[ssd/index]] | okuma sürekliliği → yazılım tamponu davranışı |
| Z2 | Yazılım köprüsü (decode/routing) | blok → örnek akışı | veri → dijital ses karesi | K003-audio (**bu düğümün dışı**, §3/B5) | DAC'a giden kare bütünlüğü/saat |
| Z3 | DAC / codec çıkışı | dijital kare → analog voltaj | D→A | [[audio/cipler/ak4458-dac]] · [[audio/cipler/pcm3168a-codec]] | analog gürültü tabanı + THD → amplifikatör girişini sınırlar |
| Z4 | Amplifikatör (Class AB) | düşük seviye analog → güç | gerilim/akım kazancı | topoloji sabiti (§8.4) | hoparlöre iletilen güç + DC güvenliği |
| Z5 | Hoparlör (uç nokta) | elektrik → akustik | elektro-mekanik | **tip envanteri UNKNOWN** ⚠️ | (zincir sonu) |

### D3. Zayıf halka ilkesi (kapsam §10.3 birebir — özet)

> Uçtan-uca ölçüm kapısı `donanaım-type-dogrulama.md` §10'dur. Eşikler
> (SNR > 100 dB · THD+N < 0.01 % @ 1 kHz — §5/E8) zincirin **toplam**
> performansına uygulanır: en zayıf halka zincir kotasını belirler. Z3'ün
> datasheet değeri tek başına "geçmiştir" anlamına gelmez — kapı Z1→Z5
> bütünüyle kapatılır.

**Hoparlör notu ⚠️ (taşıma):** hoparlör/kablo türü envanteri bu düğümde yoktur;
zincir şemasında hoparlör yalnızca *uç nokta* olarak gösterilir →
⚠️ VERIFICATION REQUIRED.

### D4. [KALIP-PLANNED] zincir eşik kontrolü (toplam kapı)

```cpp
// KALIP-PLANNED — esikler §10.3'ten aktarim; OLCUM YAPILMADI.
constexpr double kMinSnrDb   = 100.0;   // §5/E8: SNR > 100 dB
constexpr double kMaxThdPct  = 0.01;    // §5/E8: THD+N < 0.01 % @ 1 kHz

// toplam zincir (Z1..Z5) — tek halka degeriyle GECILMEZ (D3).
struct ChainMeasurement {
    double snr_db;    // UNKNOWN — olcum PLANNED (dogrulama §10)
    double thd_pct;   // UNKNOWN — olcum PLANNED
    bool   measured;  // baslangic: false
};

bool chainGate(const ChainMeasurement& m) noexcept {
    if (!m.measured) return false;  // olculmemis zincir KAPALI kapi
    return m.snr_db > kMinSnrDb && m.thd_pct < kMaxThdPct;
}
```

> `measured == false` varsayılanı kasıtlıdır: bu dosyada zincir ölçümü
> **yoktur**; kapı kapalıdır.

## E. Tip Etkisi ve Seçim Matrisi (kapsam §11–§12)

### E1. Her tipin bir sonraki katmana etkisi (kapsam §11 birebir)

| # | Tip | Etki mekanizması | Etkilediği sonraki katman | İşaret | Doğrulama kapısı |
|---|---|---|---|---|---|
| F1 | hdd | okuma sürekliliği / tampon underrun riski yönü | yazılım köprüsü (Z2) | ⚠️ büyüklük UNKNOWN | [[donanaım-type-dogrulama]] §9 (SMART) · §10 |
| F2 | ssd | düşük erişim gecikmesi → daha küçük tamponla kesintisiz akış olasılığı (tasarım mantığı, ölçülmedi) | yazılım köprüsü (Z2) | ⚠️ ölçülmedi | [[donanaım-type-dogrulama]] §9 · §10 |
| F3 | AK4458 (DAC) | SNR 115 dB / THD+N −107 dB → amplifikatör girişinde gürültü-tabanı tavanı | amplifikatör (Z4) | ✅ datasheet (ölçülmedi) | [[donanaım-type-dogrulama]] §6 |
| F4 | PCM3168A (codec) | ADC 107 dB / DAC 112 dB → kayıt ve çıkış kanalları arası eşik | yazılım köprüsü + amplifikatör (Z2/Z4) | ✅ datasheet (ölçülmedi) | [[donanaım-type-dogrulama]] §7 |
| F5 | Amplifikatör (Class AB) | kazanç + DC güvenliği → hoparlör sürücüsü | hoparlör (Z5) | ✅ topoloji sabiti; güç ölçümü ⚠️ | [[donanaım-type-dogrulama]] §8 (referans) |
| F6 | Hoparlör | akustik dönüşüm → duyulan sonuç | (zincir sonu) | ⚠️ envanter UNKNOWN | §10.3 — zincir kapısı |

**Yön kuralı (taşıma):** etki **her zaman ileri** akar (depolama → ses →
akustik); geriye doğru etki yalnızca *geri besleme/tasarım* düzeyindedir ve bu
düğümün kapsamı değildir (§3/B5-B7).

### E2. Tip-seçim matrisi (kapsam §12 birebir — taslak)

| # | İhtiyaç senaryosu | Aday tip | Dayanak | Durum |
|---|---|---|---|---|
| M1 | Çok kanallı, yüksek çözünürlüklü analog çıkış | `audio` → DAC sınıfı (AK4458) | §8.2 · doğrulama §2/8-9 | ✅ kanıtlanmış sınıf |
| M2 | Eşzamanlı çoklu giriş kaydı + çıkış | `audio` → codec sınıfı (PCM3168A) | §8.3 · doğrulama §2/10-11 | ✅ kanıtlanmış sınıf |
| M3 | Güçlendirme (hoparlör sürücüsü) | `audio` → amplifikatör sınıfı **Class AB** | §8.4 · kök `CLAUDE.md` Sabitleri | ✅ sabit — tartışma YASAK |
| M4 | Büyük kapasite / arşiv okuma | `hdd` | [[hdd/index]] ELI15 | ⚠️ sınıf var, envanter UNKNOWN |
| M5 | Sistem + sıcak veri (hızlı erişim) | `ssd` | [[ssd/index]] ELI15 | ⚠️ sınıf var, envanter UNKNOWN |
| M6 | Ekran / kablo / güç kaynağı türü | — | anayasa §10/5 | ❌ kapsam dışı — bağlam AÇILMAZ |
| M7 | Amplifikatör topolojisi seçimi | Class AB (tek seçenek) | §7/3-T6 | ✅ kapalı kapı |

> Bu matris **satın alma/BOM kararı üretmez**; envanter (Q-T1) gelmeden M4/M5
> satırları uygulanamaz.

## F. Tip-Bağımlılıkları Şablonları (kapsam §13)

### F1. Bağımlılık tablosu (kapsam §13 birebir)

| # | Bağımlılık | Tür | Yön | Dayanak |
|---|---|---|---|---|
| D1 | DAC → amplifikatör | teknolojik (sinyal seviyesi uyumu) | ileri (Z3→Z4) | §10.2 Z3-Z4; arayüz seviyesi tasarımı ⚠️ (K002 alanı) |
| D2 | amplifikatör → hoparlör | teknolojik (güç/empedans uyumu) | ileri (Z4→Z5) | §10.2; hoparlör tipi UNKNOWN (§10.3) |
| D3 | depolama → yazılım köprüsü | veri (okuma sürekliliği) | ileri (Z1→Z2) | F1/F2; büyüklük ölçülmedi |
| D4 | hdd ↔ ssd | alternatif (aynı sınıf, farklı ilke) | kardeş | §9.1 · §4/C2 |
| D5 | audio ↔ hdd/ssd | yok (bağımsız dallar) | — | §4/C1 — yalnız ortak üst düğüm |
| D6 | Tüm tipler → K000-isletim-sistemi | üst-yapı (sürücü/FS) | dikey | `hdd/index` · `ssd/index` · `audio/index` frontmatter `depends_on` |

### F2. [KALIP-PLANNED] bağımlılık doğrulama kartı (D1–D6 denetimi)

```text
[ ] D1 DAC→amp: sinyal seviyesi uyumu tasarimi var mi?  → K002 alani, bu dosyada YOK
[ ] D2 amp→hoparlor: guc/empedans uyumu?                → hoparlor tipi UNKNOWN
[ ] D3 depolama→yazilim: okuma surekliligi olcumu?      → OLCELMEDI (F1/F2)
[ ] D4 hdd↔ssd: ayni sinif alternatif mi?               → §9.1 (kardes — evet, tasima)
[ ] D5 audio↔hdd/ssd: bagimsiz dal mi?                  → §4/C1 (evet — tasima)
[ ] D6 hepsi→K000: depends_on frontmatter'da mi?        → evet (C1 tablosu, tasima)
Bu dosyada uygulama: YOK — denetim dogrulama dosyasinin kapisi.
```

## G. Kaynak Erişim Kaydı ve Dürüstlük (kapsam §14–§15)

### G1. Kaynak erişim kaydı (kapsam §14 başlığı — aktarım özeti)

> `donanaım-type-kapsam.md` §14 (Line 479) "Kaynak erişim kaydı (bu tur —
> 2026-10-13, PowerShell HEAD)" başlığını taşır; HEAD sonuçlarının tam listesi
> **kapsam dosyasında SSOT**tur, buraya kopyalanmaz (kopya → çift-kaynak
> riski). Bu dosyadan yapılan erişim kaydı:

| # | Tarih | Kaynak | Yöntem | Sonuç |
|---|---|---|---|---|
| 1 | 2026-10-15 | `donanaım-type-kapsam.md` §7–§15 | `read` | OKUNDU |
| 2 | 2026-10-15 | `donanaım-type-dogrulama.md` | okunmadı | bu turda **yapılmadı** |
| 3 | 2026-10-15 | dış URL | — | **hiçbir URL bu dosyada yok** |

### G2. Dürüstlük bloğu (kapsam §15 başlığı — bu dosyanın beyanı)

| # | İddia | Durum |
|---|---|---|
| 1 | Bağımsız doğrulanmış yeni iddia | **0** — tümü aktarım |
| 2 | Gerçek envanter kaydı (hdd/ssd/hoparlör) | **0** — tümü UNKNOWN |
| 3 | Ölçüm sonucu (SNR/THD/SMART) | **0** — tümü PLANNED |
| 4 | Uydurma URL/dosya | **0** |
| 5 | Kapı sonucu bu dosyaya yazıldı mı | **HAYIR** — yanıt tablosunda |

## H. Kapanış Özeti

| # | Kategori | Sayı | Durum |
|---|---|---|---|
| 1 | Sınıflandırma ekseni (A1) | 4 (X1–X4) | taşıma |
| 2 | Taksonomi kuralı (A4) | 6 (T1–T6) | taşıma — T1/T2 PROPOSED |
| 3 | Çip rol kaydı (B1–B4) | 3 çip | taşıma — TPA3255 referans ⚠️ |
| 4 | Depolama bilinmeyeni (C2) | 4 (S1–S4) | **4/4 UNKNOWN/⚠️** |
| 5 | Zincir halkası (D2) | 5 (Z1–Z5) | taşıma — Z5 UNKNOWN |
| 6 | Tip etkisi (E1) | 6 (F1–F6) | taşıma |
| 7 | Seçim matrisi (E2) | 7 (M1–M7) | taslak — 2 ⚠️, 1 ❌ |
| 8 | Bağımlılık (F1) | 6 (D1–D6) | taşıma |
| 9 | [KALIP-PLANNED] kod/şablon bloğu | 5 (A2, B6, C3, D4, F2) | tümü derlenmedi |
| 10 | Bağımsız doğrulanmış yeni iddia | **0** | tümü aktarım |

**Beyan:** bu dosya **aktarım + şablon** taşır; gerçek envanter, ölçüm veya
tip-seçim kararı **üretmez**. Kapılar: `≥500 satır` (ölçüm bu oturumda) ·
`Authority son satırda` ✓ · `git commit yok` ✓ · `beyazliste dışı yazım yok` ✓ ·
`uydurma değer 0` ✓ · `donanaım-type` yazımı kaynaktan korundu (T5) ✓.

> Kapı sonuçları bu dosyaya **yazılmaz**; ölçüm raporu yanıt tablosundadır.

## I. Taksonomi — Derin Denetim Kartları (ek; §A uzantısı)

### I1. Eksen ↔ düğüm denetim tablosu (X1–X4 bağlama göre)

| # | Eksen | audio/ | hdd/ | ssd/ | Durum |
|---|---|---|---|---|---|
| 1 | X1 veri biçimi | sürekli zaman sinyali | blok | blok | taşıma |
| 2 | X2 fiziksel dönüşüm | elektrik↔akustik zinciri (D1) | manyetik↔elektrik | elektriksel yük durumu | taşıma |
| 3 | X3 zaman davranışı | gerçek zamanlı — zorunlu deadline | esnek | esnek | taşıma |
| 4 | X4 envanter durumu | ✅ kanıtlı (§5/E10) | ⚠️ UNKNOWN (Q4) | ⚠️ UNKNOWN (Q4) | **2/3 UNKNOWN** |

### I2. Bağlam genişleme denetimi (T1/T2 — PROPOSED yüzeyi)

| # | Bağlam | Durum | Bu dosyada |
|---|---|---|---|
| 1 | hdd · audio · ssd | **AÇIK** (mevcut) | §A3 ağaçta var |
| 2 | display | AÇIK (2026-10-13 açıldı, T2) | **dosya yok — aktarım** |
| 3 | power | AÇIK (önceki oturum, T2) | **dosya yok — aktarım** |
| 4 | cables · network · memory · cpu | **PROPOSED — APPROVAL REQUIRED** (ADR-102 taslağı) | açılmaz |
| 5 | — | — | — |

**Kural:** bu dosya 4. satırdaki bağlamı **açmaz**; onay (APPROVAL REQUIRED)
öncesi dosya üretmek Guardrail ihlali olur.

### I3. Sınıf ↔ kademe yerleşim denetimi

| # | Öğe | Kademe | Yerleşim | Durum |
|---|---|---|---|---|
| 1 | donanaım-type | kademe-2 | kök | taşıma (T5 yazım yasağı) |
| 2 | audio/ · hdd/ · ssd/ | kademe-3 | bağlam | taşıma |
| 3 | audio/cipler/* | kademe-4 (yaprak) | çip | taşıma |
| 4 | display/ · power/ | kademe-3 | — | açıldı ama içerik **UNKNOWN** |
| 5 | hdd/ssd altı dosyalar | kademe-4 | — | **YASAK ön koşul** (T4: envanter) |

### I4. [KALIP-PLANNED] sınıf kaydı doğrulayıcı (A2 tamamlayıcı)

```cpp
// KALIP-PLANNED — sinif kaydi: marka TASIMAZ (A2 notu).
struct ClassRecord {
    const char* tip_sinifi;   // UNKNOWN | audio | hdd | ssd
    const char* eksen_x1;     // sinyal | blok | UNKNOWN
    bool        envanter_var; // hdd/ssd icin daima false (Q4)
    bool        shablon;      // bu dosyadaki tum kayitlar true
};

bool classRecordOk(const ClassRecord& r) noexcept {
    if (!r.shablon) return false;                 // shablon disi kayit bu dosyada yok
    if (!r.envanter_var) return true;             // UNKNOWN kabul (Q4 beklemede)
    return r.tip_sinifi != nullptr;               // envanterli kayit sinif zorunlu
}
```

> Kalıp **hiçbir kaydı geçerli kılmaz**; gerçek envanter gelmeden
> `envanter_var` hep `false` (C3'teki `UNKNOWN` alanlarıyla aynı disiplin).

---

## J. Çip/DAC Sınıfı — Ölçü ve Kapı Kartları (ek; §B uzantısı)

### J1. Ölçü kaydı — hangi sayı nereden geldi (aktarım izi)

| # | Çip | Ölçü | Sayı | Kaynak seviyesi | Ölçüldü mü? |
|---|---|---|---|---|---|
| 1 | AK4458 | SNR | 115 dB | datasheet (doğrulama §2/9) | **HAYIR** |
| 2 | AK4458 | THD+N | −107 dB @ 44.1 kHz | datasheet | **HAYIR** |
| 3 | AK4458 | örnek/frekans | 768 kHz / 32-bit / 8 ch | datasheet | — |
| 4 | PCM3168A | ADC SNR | 107 dB | datasheet (doğrulama §2/10-11) | **HAYIR** |
| 5 | PCM3168A | DAC SNR | 112 dB | datasheet | **HAYIR** |
| 6 | PCM3168A | DAC/ADC aralık | 8–192 kHz / 8–96 kHz | datasheet | — |
| 7 | TPA3255 | besleme | 18–53.5 V **⚠️** | doğrulama §2/12 (⚠️ işareti) | **⚠️ doğrulanmadı** |

**Kural:** hiçbir ölçü bu dosyada **yeniden yorumlanmaz**; kapı `donanaım-type-dogrulama.md`
§6–§8'dedir ve **kapalıdır** (ölçüm yapılmadı).

### J2. Class AB / Class D — sabit kapı kartı

| # | Soru | Yanıt | Dayanak |
|---|---|---|---|
| 1 | Proje topolojisi ne? | **Class AB** | kök `CLAUDE.md` Sabitler (T6) |
| 2 | Class D tartışılır mı? | **HAYIR — yasak** | §8.5 |
| 3 | TPA3255 BOM'a girer mi? | **HAYIR — referans** | §8.4 |
| 4 | TPA3255 şeması üretilir mi? | **HAYIR** | §8.4 |
| 5 | Devre detayı nerede? | K002-electroncis | §13/D7 |

### J3. MODE pin strap'i — açık soru kartı (PCM3168A)

| # | Alan | Değer | Durum |
|---|---|---|---|
| 1 | Kontrol arayüzü seçimi | SPI **veya** I2C | **AÇIK** (kapsam §8.3) |
| 2 | MODE pin durumu | UNKNOWN | `pcm3168a-codec.md` open_questions |
| 3 | Karar sahibi | K005 boot-time (A2/2 mantığı) | bu dosyada **yok** |
| 4 | Bağlantı şeması | — | K002 alanı |

---

## K. Zincir/Depolama — Kapı Kartları (ek; §C–§D uzantısı)

### K1. Zincir kapısı — Z1→Z5 adım listesi (§D3'ün uygulama yüzeyi)

| # | Halka | Ölçüm | Kapı | Durum |
|---|---|---|---|---|
| 1 | Z1 depolama | SMART/TRIM | dogrulama §9 | **yapılmadı** |
| 2 | Z2 yazılım köprüsü | kare bütünlüğü | K003 alanı | bu düğüm dışı |
| 3 | Z3 DAC/codec | SNR/THD | dogrulama §6–§7 | **yapılmadı** |
| 4 | Z4 amplifikatör | güç ölçümü ⚠️ | dogrulama §8 | **yapılmadı** |
| 5 | Z5 hoparlör | — | §10.3 | **envanter UNKNOWN** |
| 6 | **Toplam** | Z1→Z5 | dogrulama §10 | **KAPALI** (D4: measured=false) |

### K2. SMART/TRIM kapısı — bilinmeyenler kartı (S1–S4 uygulaması)

| # | SMART alanı | Değer | Durum |
|---|---|---|---|
| 1 | Gerçek disk kimliği | UNKNOWN | Q-T1 |
| 2 | Üretici eşikleri (S4) | UNKNOWN | üretim boyunca okunacak |
| 3 | TRIM destek durumu | ⚠️ VERIFICATION REQUIRED | arayüze bağlı (S3) |
| 4 | Eşik ihlali tepkisi | UNKNOWN | kapsamdaki kapı SSOT |

### K3. Depolama ↔ performans etkisi — ölçülmemişlik kartı

| # | İddia | Kaynak | Ölçüldü mü? |
|---|---|---|---|
| 1 | hdd "büyük ve yavaş okuma" | vault kaydı (§9.1) | **HAYIR** |
| 2 | ssd "düşük erişim gecikmesi" | tasarım mantığı (§11 F2) | **HAYIR — "ölçülmedi"** |
| 3 | underrun riski büyüklüğü | §11 F1 ⚠️ | **UNKNOWN** |
| 4 | tampon boyutu etkisi | — | **UNKNOWN** |

> Üçü de bu dosyada **kesin gerçek** gibi yazılamaz; §11'deki `⚠️` işaretleri
> korunur (işaretli eksikliktir, tamamlanmışlık değil).

### K4. [KALIP-PLANNED] SMART kapısı kalıbı

```cpp
// KALIP-PLANNED — esikler URETICIYE OZELDIR (S4): uydurma deger YASAK.
struct SmartGate {
    bool identity_known;   // Q-T1: false
    bool thresholds_known; // S4: false
    bool interface_known;  // S3: false
};

bool smartGateOpen(const SmartGate& g) noexcept {
    // hepsi bilinmeden kapali kapi (S1-S4 disiplini).
    return g.identity_known && g.thresholds_known && g.interface_known;
}
static_assert(!smartGateOpen({false, false, false}),
              "envanter yokken SMART kapisi kapali kalmali");
```

---

## L. Kaynak/Referans — Erişim Denetimi (ek; §G uzantısı)

### L1. Wiki-link hedef envanteri (ad olarak — erişim doğrulanmadı)

| # | Bağlantı | Tür | Erişim |
|---|---|---|---|
| 1 | [[audio/index]] | bağlam indeksi | ⚠️ ERİŞİM YAPILMADI |
| 2 | [[hdd/index]] | bağlam indeksi | ⚠️ ERİŞİM YAPILMADI |
| 3 | [[ssd/index]] | bağlam indeksi | ⚠️ ERİŞİM YAPILMADI |
| 4 | [[audio/cipler/ak4458-dac]] | çip dosyası | ⚠️ ERİŞİM YAPILMADI |
| 5 | [[audio/cipler/pcm3168a-codec]] | çip dosyası | ⚠️ ERİŞİM YAPILMADI |
| 6 | [[audio/cipler/tpa3255-amplifier]] | çip dosyası | ⚠️ ERİŞİM YAPILMADI |
| 7 | [[donanaım-type-dogrulama]] | kapı dosyası | ⚠️ okunmadı (§A/G1) |

> Bağlantı **adları** kapsamdaki hedef adlarıyla birebirdir; içerikleri bu
> turda okunmadığı için erişim `⚠️` işaretli kalır — "link çalışıyor" denmez.

### L2. Bu ek ne üretmedi (dürüstlük)

| # | Üretilmeyen | Sayı |
|---|---|---|
| 1 | yeni wiki-link hedefi | 0 |
| 2 | yeni bağlam dosyası | 0 (T1/T2 onayı olmadan) |
| 3 | envanter kaydı | 0 (Q-T1 açık) |
| 4 | ölçüm sonucu | 0 |
| 5 | URL | 0 |

### L3. Append-only kaydı

| # | Not | Değer |
|---|---|---|
| 1 | İlk yazım | §A–§H (bu oturum, 363 satır) |
| 2 | Ek | §I–§L — mevcut satır **silinmedi** |
| 3 | Numaralandırma | §H devamı — §H yeniden numaralanmadı |
| 4 | Authority | hâlâ **son satır** ✓ |
| 5 | Silme | **0** satır |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
