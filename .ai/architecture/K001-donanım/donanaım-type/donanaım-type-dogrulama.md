---
title: "donanaım-type — Doğrulama"
type: architecture-verification
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
  - K001-donanım/donanaım-type/donanaım-type-kapsam.md
  - https://www.murata.com/
validation: "2026-10-13: doğrulama §A-§D ile genişletildi; 63 → 500+ satır; iddia-URL-eşleşmesi korundu — ardından 2026-10-13 (2. tur): §5-§13 doğrulama kapıları derinleştirme; 75 → 500+ satır; §1-§4 aynen korundu"
open_questions:
  - "hdd/ssd envanteri doğrulanamıyor (K001 Q4) — envanter çıktısı bekleniyor"
  - "Fiziksel ölçüm kanıtı (SNR/THD+N) tezgâh kurulmadan kapanamaz"
date: 2026-10-12
updated: 2026-10-13
group: kanit
---

# donanaım-type — Doğrulama Tablosu

## §1 Disk kanıt tablosu (2026-10-12)

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk satırı: "donanaım-type · Donanım türü ayrımı (3 alt bağlam)" | `.ai/architecture/K001-donanım/donanım-anayasa.md` §3 satır 50 | 2026-10-12 | ✅ doğrulandı |
| 2 | Kademe-3 üç bağlam: hdd · audio · ssd | `donanım-anayasa.md` §3.1 satır 54-58 | 2026-10-12 | ✅ doğrulandı |
| 3 | Yazım yasağı: donanaım-type düzeltilemez (v2 §3 KURAL 1) | `donanım-anayasa.md` §3 satır 60-61 | 2026-10-12 | ✅ doğrulandı |
| 4 | Yalnız 3 bağlam açılır; ekran/kablo bağlamı AÇILMAZ | `donanım-anayasa.md` §10 satır 152-153 | 2026-10-12 | ✅ doğrulandı |
| 5 | Çocuk index.md açık sorusu: envanter eksik (ekran, kablo, güç kaynağı) | `donanaım-type/index.md` satır 17 | 2026-10-12 | ✅ doğrulandı |
| 6 | Kademe-3 ön koşlu: audio → §4 kanıtı · hdd/ssd → Q4 envanter | `donanım-anayasa.md` §9 satır 142-144 | 2026-10-12 | ✅ doğrulandı |
| 7 | Ölçüm eşikleri kabul kapısı: SNR > 100 dB · THD+N < 0.01 % @ 1 kHz | `~/.config/opencode/AGENTS.md` §5 | 2026-10-12 | ✅ dosya okundu |

## §2 Tur-3 web kanıt paketi (2026-10-12) — audio türü

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 8 | AK4458 = 115 dB / 768 kHz / 32-bit 8 ch Premium DAC (VELVET SOUND, TDM) | https://www.akm.com/us/en/products/audio/audio-dac/ak4458vn/ | 2026-10-12 | ✅ doğrulandı |
| 9 | AK4458 datasheet: SNR 115 dB · THD+N −107 dB @ 44.1 kHz · 48-pin QFN | https://www.akm-semi.com/pdf-c1/ak4458vn.pdf | 2026-10-12 | ✅ doğrulandı |
| 10 | PCM3168A = 24-bit 6-in/8-out audio codec; ADC SNR 107 dB · DAC SNR 112 dB; I2S/LJ/RJ/DSP/TDM | https://www.ti.com/product/PCM3168A | 2026-10-12 | ✅ doğrulandı |
| 11 | PCM3168A datasheet (Rev. A): DAC 8–192 kHz · ADC 8–96 kHz · SPI/I2C kontrol | https://www.ti.com/lit/ds/symlink/pcm3168a.pdf | 2026-10-12 | ✅ doğrulandı |
| 12 | TPA3255 Class-D amfi; 18–53.5 V besleme — K001/K005 round-1 kaydı | https://www.ti.com/product/TPA3255 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED — etkisi VERIFY; kapsam kararı anayasa §10/3 ve kök `CLAUDE.md` sabitidir |
| 13 | Amplifikatör topolojisi **Class AB** (Class D yasak) | kök `CLAUDE.md` §Sabitler satır 126 | 2026-10-12 | ✅ dosya okundu — tartışma YASAK |

## §3 Kapsam iddiaları — kanıt durumu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 14 | hdd türü envanteri (marka/model) | — | 2026-10-12 | UNKNOWN |
| 15 | ssd türü envanteri (marka/model) | — | 2026-10-12 | UNKNOWN |
| 16 | ekran/kablo/güç kaynağı bu ağaca nereye bağlanır? | `donanım-anayasa.md` §7 Q4 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 17 | audio bağlamı kapsamının §5/8-11 ile sınırlı olması | `donanaım-type-kapsam.md` §4 C5 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |

## §4 Durum özeti

| Metrik | Değer |
|---|---|
| Toplam satır | 17 |
| ✅ doğrulandı | 11 (§1/1-7 · §2/8-11, 13) |
| ⚠️ VERIFICATION REQUIRED | 4 (satır 12 · 16 · 17 — 12 tek başına iki taraflı kayıtlı) |
| UNKNOWN | 2 (satır 14 · 15) |
| İşlenen benzersiz URL | 5 (AKM ürün · AKM datasheet · TI PCM3168A ürün · TI PCM3168A datasheet · TI TPA3255) |

**Sonuç:** audio türü **kanıtlanmıştır** (satır 8-11); hdd/ssd türü envanteri **UNKNOWN**
(satır 14-15) → kademe-3 `hdd/` ve `ssd/` dosyaları bu turda üretilmedi (anayasa §9 ön koşul).
TPA3255 satırı (12) **kapsam kararı değil, karar girdisidir** — topoloji tartışması açılmaz.

---

## §5 Doğrulama kapıları mimarisi (derinleştirme 2026-10-13)

> **Durum okuma kuralı:** Bu bölümden sonraki tüm ölçüm adımları **ÖNERİLEN protokolüdür**;
> hiçbir tezgâh ölçümü YAPILMAMIŞTIR. Adım tablolarındaki "Durum" sütunu ya `UYGULANMADI
> (tezgâh yok)` ya da `kayıtlı (datasheet)`dır. Uydurma ölçüm değeri bu dosyaya YAZILMAZ.

### §5.1 Kapı listesi

| Kapı | Ad | Kapsam | Kabul eşiği (kaynak) | Durum |
|---|---|---|---|---|
| K-Ç1 | AK4458 THD+N doğrulama | DAC çıkış THD+N/SNR | THD+N < 0.01 % @ 1 kHz · SNR > 100 dB (§1/7) + datasheet −107 dB/115 dB hedef (§2/9) | UYGULANMADI |
| K-Ç2 | PCM3168A kanal-eşzamanlılık doğrulama | 6-in/8-out zaman uyumu + SNR | kanallar arası örnek-bozulması kabulü ⚠️ (eşik sayısalı yok) · SNR > 100 dB (§1/7) | UYGULANMADI |
| K-Ç3 | TPA3255 güç-doğrulama (REFERANS) | besleme/termal/çıkış gücü | ⚠️ — kapı **BOM'u etkilemez**; Class AB sabiti (§2/13) geçerli | UYGULANMADI ( referans) |
| K-D1 | hdd depolama doğrulama | SMART sağlığı · TRIM | üreticiye özgü eşikler ⚠️ (envanter UNKNOWN) | UYGULANMADI |
| K-D2 | ssd depolama doğrulama | SMART sağlığı · TRIM | üreticiye özgü eşikler ⚠️ (envanter UNKNOWN) | UYGULANMADI |
| K-Z | Zincir uçtan-uca doğrulama | depolama → DAC ses yolu | toplam SNR/THD §1/7 eşiği + DC offset < 0.5 V (kural 10) | UYGULANMADI |

### §5.2 Kapı sırası (bağımlılık)

```text
K-D1/K-D2 (depolama) --> K-Z (zincir) <--- K-Ç1 (DAC) <--- K-Ç2 (codec eşzamanlılık)
                                                ^
                                        K-Ç3 referans (bilgi girdisi, BOM değil)
```

Kural: K-Z yalnız **K-D\*** ve **K-Ç1** geçtiğinde anlamlıdır — zayıf halka ilkesi
(`donanaım-type-kapsam.md` §10.3). Kapılar bağımsız açılmaz; sıralı kapanır.

### §5.3 Ölçüm ortak altyapısı (plan — model adı YOK)

| Öğe | Not | İşaret |
|---|---|---|
| Sinyal kaynağı + ses analizör sınıfı cihaz | marka/model seçimi tezgâh kararında | ⚠️ model belirsiz |
| Osiloskop (kanal zamanlama) | K-Ç2 için | plan |
| Programatik güç kaynağı + ölçüm (K-Ç3) | referans kapı | plan |
| SMART istemcisi | `smartctl` (smartmontools — erişim §12/W7) | araç adı kayıtlı, çalıştırılmadı |
| Ortam sıcaklığı/termal ölçüm | K-Ç3 | plan |

---

### §5.4 Ortak giriş ve kapanış kriterleri

| Kriter | Tanım |
|---|---|
| G1 — Giriş (kapı açılışı) | protokol adımı yazılmış · hedef çip/dosya linki var · eşik kaynağı işaretli (kayıt/datasheet/kural) |
| G2 — Uygulama | adım "Durum" sütunu UYGULANMADI → UYGULANDI'ya yalnız tezgâh kaydıyla geçer |
| G3 — Kayıt | her ölçüm, ölçüm tarihi + cihaz kimliği (⚠️ model seçimi açık) + ham değer içerir |
| G4 — Karar | tüm adımlar UYGULANDI ve eşikler karşılanıyorsa PASS; tek FAIL = kapı FAIL |
| G5 — Engelli kapanış | ön koşul (envanter/tezgâh) yoksa kapı **BEKLEMEDE** kalır — "atlanarak PASS" YOK |
| G6 — Geri dönüş | PASS sonra bile çip/versiyon değişirse kapı yeniden açılır (kanıt bayatlar) |

### §5.5 Ölçüm belgeleme kuralı

1. Ölçüm değeri **yalnız** tezgâh çıktısından yazılır; datasheet değeri "hedef", vault
   kaydı "iddia" olarak kalır — ikisi aynı hücrede birleştirilmez.
2. Eşik aşımında birim (dB / % / V / °C) ve koşul (frekans, seviye, sıcaklık) yazılır.
3. Uygulanmayan adım "UYGULANMADI" olarak kalır; boş hücre **0 yerine geçmez**.
4. Bu dosyaya sonradan eklenecek her ölçüm satırı, §11 matrisindeki ilgili kapının
   adım sayısını günceller (matris = tek canlı sayaç).
### §5.6 Kapı durum sözlüğü (tek anlam kümesi)

Bu dosyadaki her kapı kaydı aşağıdaki dört durumdan birini taşır; aynı kelime iki farklı yerde iki farklı anlam taşımaz.

| Durum | Anlamı | Ne zaman yazılır |
|---|---|---|
| `UYGULANMADI` | Adım planlandı ama tezgâhta hiç çalıştırılmadı | Ölçüm aleti/tezgâh yoksa (dosyanın bugünkü durumu: 44 adımın tamamı) |
| `BEKLEMEDE` | Adım için ortam tanımlı, sıra bekliyor | Ekipman bağlanmadan önce |
| `GEÇTİ` | Ölçüm yapıldı, sonuç kabul eşiğini geçti | Yalnız gerçek ölçüm kaydından sonra |
| `KALDI` | Ölçüm yapıldı, sonuç eşiği geçmedi | Yalnız gerçek ölçüm kaydından sonra |

`UNKNOWN` bu tablonun bir durumu değildir: `UNKNOWN` bir **bilgi eksikliğidir** (ör. §3'teki K-Ç2 eşzamanlılık eşiği). Bir kapı hiçbir zaman `UNKNOWN` iken `GEÇTİ` sayılmaz.

Eşik değerleri bu dosyada üretilmez; §1'deki hedeflerden (SNR > 100 dB · THD+N < 0.01 % @ 1 kHz) ve §2'deki datasheet kayıtlarından okunur.

---

## §6 Kapı K-Ç1 — AK4458 THD+N doğrulama protokolü

**Hedef çip:** [[audio/cipler/ak4458-dac]] · **Rol:** DAC (kapsam §8.2) · **Sorumlu (öneri):** audio-hw ⚠️ (handover onayı beklenir).

| Adım | İşlem | Kabul/Referans | Durum |
|---|---|---|---|
| 1 | Test düzeneği kurulumu: AK4458 çıkışını analizöre bağla | düzenek şeması bu dosyada yok | UYGULANMADI |
| 2 | Saat ağını sabitle: MCLK/BCLK/FS — kayıtlı üst frekans 768 kHz (§2/8) | saat kararlılığı ölçülmedi | UYGULANMADI |
| 3 | 1 kHz sinus girişini uygula (test seviyeleri protokol önerisidir: −60 dBFS ve −20 dBFS) | seviye seçimi ⚠️ — kesin değer tezgâhta belirlenir | UYGULANMADI |
| 4 | THD+N ölç — hedef: datasheet −107 dB @ 44.1 kHz (§2/9) **ve** kabul kapısı < 0.01 % @ 1 kHz (§1/7) | her iki koşul da | UYGULANMADI |
| 5 | SNR ölç — hedef: datasheet 115 dB (§2/9) **ve** kabul > 100 dB (§1/7) | her iki koşul da | UYGULANMADI |
| 6 | 8 kanalın **her biri** için 4-5 tekrarla (kanal başına) | tek kanal PASS ≠ kapı PASS | UYGULANMADI |
| 7 | Sonuçları §11 matrisine işle; tekrar üretilebilirlik notu ekle | kayıt zorunlu | UYGULANMADI |
| 8 | Kapı kararı: tüm kanallar eşik altındaysa PASS | FAIL → audio-hw'e handover (registry §9) | BEKLEMEDE |

**Bağımlılık:** K-Ç1 sonucu K-Z'nin (§10) Z3 halkasını besler; bu kapı kapanmadan zincir
kapısı PASS sayılmaz (§5.2).

---

### §6.1 K-Ç1 — ölçüm kayıt şablonu (boş)

| Kanal | Frekans | Seviye | THD+N (ölçülen) | THD+N (hedef §2/9) | SNR (ölçülen) | SNR (hedef §2/9) | Tarih | Karar |
|---|---|---|---|---|---|---|---|---|
| OUT1 | 1 kHz | ⚠️ seviye seçilmedi | — | −107 dB @ 44.1 kHz | — | 115 dB | — | BEKLEMEDE |
| OUT2 | 1 kHz | ⚠️ | — | aynı | — | aynı | — | BEKLEMEDE |
| OUT3..OUT8 | 1 kHz | ⚠️ | — | aynı | — | aynı | — | BEKLEMEDE |

> Boş hücreler ölçülmemiştir (§5.5/3). Dolgu: yalnız tezgâh çıktısıyla.

### §6.2 K-Ç2'nin öncesindeki teknik hazırlık — K-Ç1 riskli noktaları

| # | Risk | Neden | Önleme |
|---|---|---|---|
| P1 | Saat yanlışlığı THD+N'yi bozar | MCLK/BCLK uyumsuzluğu ölçüme değil düzeneğe hata yazar | adım 2 zorunlu |
| P2 | Tek kanal PASS ile genelleme | 8 kanalın her biri ayrı ölçülür (adım 6) | kanal-bazlı matris |
| P3 | Giriş seviyesi ölçümü şişirir/indirger | −60/−20 dBFS önerisi sabit DEĞİL (adım 3 ⚠️) | seviye kararı tezgâhta |
| P4 | Toprak/EMI gürültüsü | analizör bağlantısı düzeneğe bağlı | protokolde bağlantı notu zorunlu |
| P5 | Kapı ile zincirin karıştırılması | K-Ç1 yalnız Z3 halkasını ölçer; Z1-Z5 bütünü K-Z'dir (§5.2) | kapı adı raporda açık yazılır |

---

### §6.3 K-Ç1 — kabul karar ağacı

```text
ölçüm alındı mı?
  ├─ HAYIR --> UYGULANMADI (kapı açık kalır; "varsayım ile PASS" YOK)
  └─ EVET
       ├─ THD+N < 0.01 % @ 1 kHz (§1/7)  VE  SNR > 100 dB (§1/7)
       │     ├─ EVET --> hedefe karşıla: datasheet −107 dB / 115 dB (§2/9)
       │     │            ├─ hedefe ULAŞTI --> PASS (tüm kanallar için tekrarla)
       │     │            └─ hedefe ULAŞAMADI ama kabul eşiğini geçti --> "PASS (kabul) / datasheet altı" notu ile kaydet
       │     └─ HAYIR --> FAIL --> §11.2 handover (audio-hw)
       └─ ölçüm koşulları eksik (frekans/seviye/sıcaklık yok) --> ölçüm GEÇERSİZ, adım tekrar
```

**Not:** "kabul eşiği geçildi ama datasheet'in altında" durumu **gizlenmez** — iki ayrı
sütun olarak §6.1 şablonuna yazılır (kural 7 dürüstlük).

## §7 Kapı K-Ç2 — PCM3168A kanal-eşzamanlılık doğrulama protokolü

**Hedef çip:** [[audio/cipler/pcm3168a-codec]] · **Rol:** codec 6-in/8-out (kapsam §8.3) · **Sorumlu (öneri):** audio-hw + dsp-fw ⚠️.

| Adım | İşlem | Kabul/Referans | Durum |
|---|---|---|---|
| 1 | Arayüz modunu seç ve kaydet: I2S / LJ / RJ / DSP / TDM (kayıtlı seçenekler §2/10) | MODE pin strap kararı AÇIK (`pcm3168a-codec.md` open_question) | UYGULANMADI |
| 2 | FS/BCLK zamanlamasını osiloskopla doğrula — TDM frame kenetlenmesi | frame bütünlüğü ölçülmedi | UYGULANMADI |
| 3 | Eşzamanlı 8 çıkış kanalına ortak sinyal ver; kanallar arası **zaman farkı** ölç | kabul: kanallar arası örnek-bozulması kabul eşiği **sayısal olarak tanımsız** ⚠️ — eşik audio-hw tarafından onaylanacak | UYGULANMADI |
| 4 | Aynı işlemi 6 giriş (ADC) kanalı için tekrarla | kayıt yolu da eşzamanlı olmalı | UYGULANMADI |
| 5 | SNR doğrulaması: ADC 107 dB · DAC 112 dB (§2/10) + kabul > 100 dB (§1/7) | datasheet ↔ tezgâh karşılaştırması | UYGULANMADI |
| 6 | Örnekleme aralığı geçerliliği: DAC 8–192 kHz · ADC 8–96 kHz (§2/11) | 48 kHz sistem kesişiminde çalışır beklentisi | UYGULANMADI |
| 7 | Kontrol yolunu doğrula: SPI mu I2C mi (MODE pin) — karar kayıt altına | açık soru kapanır | UYGULANMADI |
| 8 | Kapı kararı | PASS/FAIL → §11 matrisi | BEKLEMEDE |

**Neden eşzamanlılık:** 6-in/8-out bir codec'te kanallar arası kayma, mikrofon dizisi ve
çok hoparlörlü çıkışta faz tutarsızlığı yaratır; kapı bu yüzden yalnız SNR'ye bakmaz.

---

### §7.1 K-Ç2 detay — hata modları ve eşzamanlılık ölçütü

| # | Hata modu | Gözlem | Adım |
|---|---|---|---|
| E1 | TDM frame kayması | kanallar arası sabit örnek ofseti | 2-3 |
| E2 | BCLK/FS kenetlenme hatası | kanallar arası değişken kayma (jitter benzeri) | 2-3 |
| E3 | ADC/DAC yolu asimetrisi | giriş ve çıkış tarafında farklı kayma | 4 |
| E4 | Mod hatası (I2S/LJ/RJ/DSP/TDM) | tüm kanallarda sistematik kayma veya kanal kayması | 1 |
| E5 | Örnekleme aralığı ihlali | 48 kHz dışında anormallik | 6 |

**Eşik durumu dürüstlüğü:** Kanallar arası kabul eşiği bu vault'ta **sayısal olarak
tanımlı DEĞİLDİR** (§7/3 ⚠️). "0 örnek kayma" bir *tasarım beklentisi*dir, ölçülmüş kabul
değilidir; sayısal eşik audio-hw onayıyla bu dosyaya eklenecektir.

---

### §7.2 K-Ç2 — ölçüm kayıt şablonu (boş)

| Yön | Kanal grubu | Arayüz modu | Kanallar arası kayma | SNR (ölçülen) | SNR (hedef) | Tarih | Karar |
|---|---|---|---|---|---|---|---|
| Çıkış (DAC) | 8 ch | ⚠️ (adım 1 — MODE kararı açık) | — | — | 112 dB (§2/10) | — | BEKLEMEDE |
| Giriş (ADC) | 6 ch | ⚠️ | — | — | 107 dB (§2/10) | — | BEKLEMEDE |

- "Kanallar arası kayma" hücresinde **kabul eşiği tanımsız** (§7.1) → eşik onayı gelene
  kadar bu hücre `⚠️` kalır; ölçülen değer yine de yazılır (ölçüm ≠ kabul).
- Sistem kesişimi 48 kHz'tir (kural 8) — adım 6'daki aralık kontrolü bu noktada karşılanır.

### §7.3 K-Ç2 - eşzamanlılık ölçütünün iki bağımsız yolu (öneri)

§7.1'deki ε(t) ölçütü sahada iki farklı yolla elde edilebilir; ikisi de §7.2 kayıt şablonuna ayrı sütun açar ve ikisi de `UYGULANMADI` durumundadır.

| Yol | Nasıl | Artı | Eksi |
|---|---|---|---|
| A - ortak kaynak | Tüm kanalları ortak saatli tek çok-kanallı cihazla örneklemek | Saat kayması ε'e girmez | Baş başına kanal sayısı sınırlı; 8 kanal için kaç kart gerekli belirsiz (UNKNOWN) |
| B - referans tetik | Kanalları ortak bir tetik darbesiyle başlatmak | Tek karta sığar | Tetik gecikmesi ölçütü kirletir; tetik yolu kendisi ölçülmeli |

İki yolun sonucu birbirinden farklı çıkarsa ölçüt **geçersiz sayılır** ve kapı `KALDI` değil **tekrar** işaretlenir: hangi yolun doğru olduğu o ölçütten anlaşılamaz.

Bu tablo bir **protokol önerisidir**, ölçüm sonucu değildir — her iki yol da uygulanmamıştır.

---

## §8 Kapı K-Ç3 — TPA3255 güç-doğrulama protokolü (SALT REFERANS)

> ⚠️ **BAĞLAYICI ÇELİŞKİ NOTU (en üstte):** Proje amplifikatör topolojisi **Class AB'dir;
> Class D yasağıtır** (§2/13 · kök `CLAUDE.md` Sabitler). Bu kapı, TPA3255'ü *referans
> teknoloji* olarak doğrulamak içindir: **sonucu BOM'a, şemaya veya tasarım kararına
> GİREMEZ.** Class-D reopen talebi ADR + owner onayı gerektirir (Guardrail #14).

| Adım | İşlem | Kayıt/Referans | Durum |
|---|---|---|---|
| 1 | Besleme gerilimi aralığını kayıtlı iddia ile karşılaştır: 18–53.5 V (§2/12 ⚠️) | iddia ⚠️ — datasheet'ten teyit edilmedi | UYGULANMADI |
| 2 | Boşta (idle) kayıp ölç — kayıtlı: < 2.5 W (`tpa3255-amplifier.md` §0, datasheet §7.6) | datasheet kaydı ≠ tezgâh ölçümü | UYGULANMADI |
| 3 | Termal doğrulama — kayıtlı çalışma aralığı 0…70 °C (`tpa3255-amplifier.md` §0) | yük altı sıcaklık ölçülmedi | UYGULANMADI |
| 4 | Çıkış gücü iddiası (315 W stereo / 600 W mono — ürün başlığı) ⚠️ | iddia olarak kayıtlı; ölçülmedi | UYGULANMADI |
| 5 | EMI: "önerilen sistem tasarımıyla uyumlu" iddiası (ürün sayfası kaydı) | ölçüm yok | UYGULANMADI |
| 6 | Kapı sonucu | **yalnız bilgi girdisi** — topoloji kararı §2/13 ile KAPALI | REFERANS |

---

### §8.1 K-Ç3 referans kapı sınırları

| # | Sınır | İfade |
|---|---|---|
| L1 | Kapı, topoloji kararını DEĞİŞTİRMEZ | Class AB sabiti §2/13'tür; K-Ç3 sonucu ne olursa olsun Class D'ye geçiş yok |
| L2 | Kapı çıktısı bilgi düzeyindedir | sonuç yalnız `tpa3255-amplifier.md` derin-dalıyla uyumluluğu raporlar |
| L3 | BOM/şema üretmez | Guardrail #14 + `tpa3255-amplifier.md` en üst uyarı notu |
| L4 | İddialar iki taraflı kayıtlıdır | 18–53.5 V (§2/12 ⚠️) · 315 W/600 W ürün başlığı (§8/4 ⚠️) — hiçbirisi ölçülmedi |
| L5 | Ölçüm öncelik sırası en son | gerçek amplifikatör (Class AB) kapısı henüz TANIMLI DEĞİL ⚠️ — ayrı kapı açmak sahip (audio-hw) kararı |
## §9 Kapı K-D1/K-D2 — Depolama doğrulama (SMART / TRIM)

**Hedefler:** [[hdd/index]] (K-D1) · [[ssd/index]] (K-D2) · **Sorumlu (öneri):** win-sw +
audio-hw ⚠️. **Ön koşul:** K001 Q4 envanteri — marka/model UNKNOWN (§3/14-15).

| Adım | İşlem | Kaynak/Referans | Durum |
|---|---|---|---|
| 1 | Envanteri oku: marka/model/kapasite/arayüz — Q-T1 cevabı | **UNKNOWN** — kapı bu adım olmadan açılamaz | BEKLEMEDE (K001 Q4) |
| 2 | SMART sağlık raporunu çek (`smartctl` sınıfı istemci — smartmontools erişimi §12/W7) | araç erişimi doğrulandı; rapor ALINMADI | UYGULANMADI |
| 3 | Kritik SMART attributelerini değerlendir (yeniden-alokasyon, gerçek sektörlere aktarma, hata sayacı) ⚠️ eşikler üreticiye özgü | eşik tablosu envantere bağlı | UYGULANMADI |
| 4 | TRIM/DSM destek durumunu sorgula (dosya sistemi katmanı) | komut seti standartları: T13 (§12/W4) · NVMe (§12/W5); spesifikasyon metni bu turda OKUNMADI ⚠️ | UYGULANMADI |
| 5 | Yazma–silme yorulma/yoğunluk testi (kapasite ⚠️ envantere bağlı) | test parametresi tanımsız | UYGULANMADI |
| 6 | hdd↔ssd karşılaştırmalı okuma sürekliliği kaydı (Z1 halkası için girdi) | ölçüm yok — §11/F1-F2 etkisi yalnız yön iddiası | UYGULANMADI |
| 7 | Kapı kararı (K-D1 ve K-D2 ayrı ayrı) | PASS yalnız envanter + ölçüm sonrası | BEKLEMEDE |

---

### §9.1 K-D — SMART/TRIM detay adımları (dürüst ek)

| Alt-adım | İşlem | İşaret |
|---|---|---|
| 2a | SMART genel raporu çek — araç: `smartctl` sınıfı (§12/W7 erişim doğrulandı) | rapor alınmadı |
| 2b | Uzun dönem erezyon attributeleri: yeniden-alokasyon sayacı, bekleyen yeniden-alokasyon, gerçek sektörlere aktarma | adlar smartctl çıktısıyla eşleşir ⚠️ — envanter yok, çalıştırılmadı; **eşikler üreticiye özgü** |
| 2c | Yorulma: güç-saatleri / döngü-sayacı attributeleri | ⚠️ üreticiye özgü |
| 3a | Yüzde tükenme/önerilen yeniden alokasyon eşiği | ⚠️ üreticiye özgü — envanter (Q-T1) gelmeden YAZILMAZ |
| 4a | TRIM/DSM destek sorgusu (dosya sistemi + arayüz katmanı) | spesifikasyon metni okunmadı ⚠️ (yalnız yayıncı erişimi doğrulandı: §12/W8-W9) |
| 4b | Destek varsa: silme sonrası blok geri kazanım gözlemi | ölçülmedi |
| 5a | Yazma–silme yorulma koşusu: süre/yoğunluk parametresi | parametre tanımsız ⚠️ |
| 6a | Z1 süreklilik kaydı → doğrulama §10/2'ye girdi | ölçülmedi |

**Yasaklar:** Bu adımlarda bile marka/model/capacity **uydurulmaz** (§3/14-15 UNKNOWN);
SMART eşik tablosu envanterle birlikte gelir.

---

### §9.2 K-D — kapı karar ağacı (hdd ve ssd için ortak)

```text
K001 Q4 envanteri geldi mi?
  ├─ HAYIR --> BEKLEMEDE (adım 1 kilitli; SMART eşikleri YAZILMAZ)
  └─ EVET
       ├─ SMART raporu erişilebilir mi?  ├─ HAYIR --> FAIL (erişim/kart-sağlık sorunu)
       │                                └─ EVET --> attributeler üretici eşiğiyle karşılaştır
       │                                            ├─ sağlıksız --> FAIL
       │                                            └─ sağlıksız --> TRIM/DSM durumu (adım 4)
       │                                                           ├─ desteklenmiyor + ssd --> UYARI kaydı (kaynak: spesifikasyon okunmadı ⚠️)
       │                                                           └─ destekleniyor --> adım 5 yorulma koşusu
       └─ adım 6 süreklilik kaydı (Z1) tamam --> K-D1/K-D2 AYRI ayrı karar
```

### §9.3 Kapı → kanıt türü eşlemesi (K-Z için)

| Kapı adımı (§9.1) | Beklenen kanıt | Kanıt türü | Durum |
|---|---|---|---|
| Z1 kaynak bütünlüğü | hash çıktısı + dosya boyutu kaydı | dosya (yerel) | UYGULANMADI |
| Z2 ayrıştırma doğruluğu | bit-bazlı karşılaştırma raporu | dosya (yerel) | UYGULANMADI |
| Z3 oynatma yolu | oynatıcı logu / düşen örnek sayacı | log | UYGULANMADI |
| Z4 DAC çıkışı | dalga formu kaydı (WAV) | ölçüm dosyası | UYGULANMADI |
| Z5 hoparlör | **UNKNOWN** - mikrofon + oda kalibrasyonu yok | - | ekipman tanımlanmadan doldurulmaz |

Z5 satırındaki `UNKNOWN` kasıtlıdır: zincirin son halkası için ölçüm yolu (mikrofon, oda kalibrasyonu, referans SPL) tanımlanmadığından hiçbir sayı yazılmaz.

### §9.4 Kapı geçerliliğini kaldırma (rollback) - genel kural

§9.2 koşullarından biri gerçekleşip bir kapı `GEÇTİ` iken geçersiz sayılırsa:

1. Kapı `BEKLEMEDE` durumuna döner; eski `GEÇTİ` kaydı silinmez, üstü geçersiz damgasıyla işaretlenir.
2. §5.2 sırasındaki bu kapıya bağımlı tüm kapılar otomatik geçersiz sayılır.
3. Daha önce alınmış `GEÇTİ` değerleri sayılmaz; zincir baştan yürür.

Bu kural kapıların kolayca tekrar tekrar denenmesi için değildir: aynı adım art arda üç kez uygulanıp ikisinde geçildiyse kapı **kalmış** sayılır ve nedeni kayıt altına alınır.

---

## §10 Kapı K-Z — Zincir uçtan-uca doğrulama (depolama → DAC ses yolu)

**Zincir:** kapsam §10.2 (Z1→Z5) · **Bağımlılıklar:** K-D\* + K-Ç1 (§5.2) · **Sorumlu (öneri):** audio-hw + embedded ⚠️.

| Adım | Halka | İşlem | Kabul | Durum |
|---|---|---|---|---|
| 1 | Z1 | Test dosyasını hdd/ssd'den oku; hash doğrulaması (bit-birebirlik) | bozulma = FAIL | UYGULANMADI |
| 2 | Z2 | Dosyayı ses yoluna besle — 512 örnek / 48 kHz tampon rejimi (kural 8) | underrun/kesinti gözlemi | UYGULANMADI |
| 3 | Z3 | DAC çıkışını analizörle ölç (K-Ç1 çıktısını kullanır) | toplam SNR > 100 dB · THD+N < 0.01 % @ 1 kHz (§1/7) | UYGULANMADI |
| 4 | Z4 | Amplifikatör çıkış DC offset kontrolü | **DC offset > 0.5 V → koruma rölesi zorunlu** (kural 10) | UYGULANMADI |
| 5 | Z4→Z5 | Hoparlör ucunda (güvenli yük ile) akustik/elektriksel sonuç kaydı | hoparlör tipi UNKNOWN ⚠️ (kapsam §10.3) | UYGULANMADI |
| 6 | bütünü | Zayıf halka hesabı: en kötü halka zincir kotasını belirler (kapsam §10.3) | kapı = en kötü halkanın PASS'ı | UYGULANMADI |
| 7 | bütünü | Uçtan uca gecikme/kesinti gözlemi (depolama→çıkış) ⚠️ sayısal eşik tanımlı değil | eşik = anayasa §10/3 + §1/7 | UYGULANMADI |
| 8 | — | Kapı kararı → §11 matrisi + open_questions güncelle | PASS/FAIL | BEKLEMEDE |

---

### §10.1 K-Z — kayıt şablonu (boş)

| Halka | Ölçüm | Eşik | Değer | Tarih | Karar |
|---|---|---|---|---|---|
| Z1 | dosya hash doğrulaması | bit-birebirlik | — | — | BEKLEMEDE |
| Z2 | kesintisiz akış (512 örnek / 48 kHz) | underrun gözlemi | — | — | BEKLEMEDE |
| Z3 | SNR | > 100 dB (§1/7) | — | — | BEKLEMEDE |
| Z3 | THD+N @ 1 kHz | < 0.01 % (§1/7) | — | — | BEKLEMEDE |
| Z4 | DC offset | ≤ 0.5 V, aksi hâlde koruma rölesi zorunlu (kural 10) | — | — | BEKLEMEDE |
| Z5 | akustik/elektriksel sonuç | hoparlör tipi UNKNOWN ⚠️ | — | — | BEKLEMEDE |
| bütünü | uçtan uca gecikme/kesinti | eşik tanımsız ⚠️ (§10/7) | — | — | BEKLEMEDE |

### §10.2 K-Z — ön koşul zinciri (kapılar birbirini kilitler)

1. K-D1 ve K-D2 geçmeden Z1 güvenilir sayılmaz (depolama sağlıksa bit-birebirlik anlamsızdır).
2. K-Ç1 geçmeden Z3 ölçümü DAC mı düzeneğin mi hattığı ayrılamaz.
3. K-Ç2, Z3'ün çok kanallı senaryosunu (çıkış grubu) etkiler — tek kanal ölçümü yeterli değildir.
4. K-Ç3 (referans) hiçbir kapıyı açmaz/kilitlemez — bilgi girdisidir (§8.1/L2).
5. Zincir kapısı K-Z = min(K-D1, K-D2, K-Ç1[, K-Ç2]) · PASS (§5.2 zayıf halka).

---

### §10.3 K-Z — kapı geçersizliği (rollback) koşulları

| # | Koşul | Sonuç |
|---|---|---|
| B1 | Çip sürümü/revizyonu değişti | K-Ç1/K-Ç2 bayatlar → kapı yeniden (§5.4/G6) |
| B2 | Dosya sistemi/ürüncü sürücü değişti | K-D bayatlar → SMART raporu yeniden |
| B3 | Zincirde halka değiştirildi (ör. DAC ↔ codec rolü) | K-Z tamamen yeniden |
| B4 | Ölçüm cihazı/kimliği değişti | ilgili ölçüm adımı geçersiz → tekrar |
| B5 | Eşik kaynağı revize edildi (kural 7 / anayasa §10/3) | tüm kapılar yeniden değerlendirilir |

## §11 Ölçüm protokolü — kapı/özet matrisi (canlı durum)

| Kapı | Toplam adım | Uygulanan | Eşik kaynağı | Engelyici | Önerilen sorumlu ⚠️ |
|---|---|---|---|---|---|
| K-Ç1 (AK4458) | 8 | **0** | §1/7 + §2/9 | tezgâh yok | audio-hw |
| K-Ç2 (PCM3168A) | 8 | **0** | §1/7 + §2/10-11; eşzamanlılık eşiği tanımsız | tezgâh + MODE pin kararı | audio-hw + dsp-fw |
| K-Ç3 (TPA3255 ref.) | 6 | **0** | §2/12 ⚠️ + ürün kayıtları | tezgâh yok; **BOM'a girmez** | audio-hw |
| K-D1 (hdd) | 7 | **0** | üretici SMART eşikleri ⚠️ | **K001 Q4 envanter** | win-sw |
| K-D2 (ssd) | 7 | **0** | üretici SMART eşikleri ⚠️ | **K001 Q4 envanter** | win-sw |
| K-Z (zincir) | 8 | **0** | §1/7 + kural 10 (DC 0.5 V) | K-D ve K-Ç1 kapalı | audio-hw + embedded |
| **TOPLAM** | **44** | **0** | — | — | — |

**Hiçbir kapı kapanmadı** — bu durumun tek nedeni tezgâh + envanter eksikliğidir; kapı
*tasarımı* bu bölümde tamamlanmıştır (protokol hazır, ölçüm yok).

### §11.1 Kapı kapanış checklist'i

- [ ] Tüm adımlar UYGULANDI (0 adım atlanmamış)
- [ ] Her ölçümün tarih + cihaz kimliği + koşulu kayıtlı (§5.5/2)
- [ ] Eşik kaynağı her satırda işaretli (kayıt / datasheet / kural)
- [ ] Uydurma değer yok (boş hücre = — , 0 değil)
- [ ] İlgili çip dosyası wiki-linki doğrulandı (§12 öncesi bağlantı denetimi)
- [ ] Envanter ön koşlu kapılar (K-D) için K001 Q4 cevabı eklenmiş
- [ ] Karar §11 matrisine işlenmiş; FAIL varsa handover başlatılmış

### §11.2 Handover / eskalasyon (kapı bazlı)

| Senaryo | Hedef | Öncelik | Dayanak |
|---|---|---|---|
| K-Ç1/K-Ç2 eşik aşımı (FAIL) | audio-hw | MEDIUM (ölçüm hatası olabilir → önce tekrar) | registry §9/§10 |
| K-Ç2 eşzamanlılık eşiği tanımsız | audio-hw (eşik onayı) | HIGH — kapı kilitli | §7.1 E5 |
| K-D envanter eksikliği | Vault Steward (K001 Q4) | HIGH — iki kapı kilitli | §3/14-15 |
| K-Ç3 Class-D reopen talebi | ADR + owner onayı | yasak — doğrudan reddet | §8.1/L1 · Guardrail #14 |
| K-Z halka FAIL | ilgili halka sahibi (audio-hw/embedded) | HIGH | §10.2 |
| Ölçüm tezgâhı yok (durum) | audio-hw + win-sw | MEDIUM — kapılar BEKLEMEDE | §5.1 |

---

### §11.3 Kapı durum panosu (canlı özet)

| Kapı | Durum | Kilit | Kilidi açacak olay |
|---|---|---|---|
| K-Ç1 | UYGULANMADI | tezgâh | ölçüm düzeneği kurulumu |
| K-Ç2 | UYGULANMADI | tezgâh + MODE kararı | arayüz modu kararı + düzenek |
| K-Ç3 | REFERANS (kapalı) | — (topoloji sabit) | hiçbir — L1 kuralı |
| K-D1 | BEKLEMEDE | K001 Q4 envanter | envanter çıktısı |
| K-D2 | BEKLEMEDE | K001 Q4 envanter | envanter çıktısı |
| K-Z | BEKLEMEDE | K-D + K-Ç1 | yukarıdaki iki kapının kapanması |
| **Genel** | **0/6 kapı kapandı** | — | tezgâh + envanter |

---

### §11.4 Kapı → vault bağlantısı (harita)

Ölçüm kayıtları bu dosyada kalır; bileşen tanımları kendi sayfalarındadır. Her kapının dayandığı dosya:

| Kapı | Bileşen | Bağlantı |
|---|---|---|
| K-Ç1 | AK4458 DAC çipi | `audio/cipler/ak4458-dac` |
| K-Ç2 | PCM3168A codec | `audio/cipler/pcm3168a-codec` |
| K-Ç3 | TPA3255 amplifikatör (salt referans) | `audio/cipler/tpa3255-amplifier` |
| K-D1 | HDD | `hdd/index` |
| K-D2 | SSD | `ssd/index` |
| K-Z | devre / analog zinciri | `../../K002-electroncis/devre-ve-analog/index` |
| K-Z | hoparlör / çıkış katmanı | `../../K003-audio/output/index` |

Bağlantı biçimi wiki-link çözüm kuralına uyar (`.ai/scripts/wiki-link-check.ps1`); linkler yalnız bu harita tablosunda geçer, ölçüm metninin içine dağılmaz.

---

## §12 Web kanıt paketi (bu tur — 2026-10-13, HTTP HEAD doğrulaması)

> Yöntem: `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`.
> Bu tablo **erişim doğrulamasıdır**; içerik iddiaları §2'dedir. §4 sayımları bir önceki tura
> aittir ve bu turda DEĞİŞTİRİLMEMİŞTİR.

| # | URL | HTTP | İlgili kapı/bölüm |
|---|---|---|---|
| W1 | https://www.akm.com/us/en/products/audio/audio-dac/ak4458vn/ | 200 ✅ | K-Ç1 (§6) |
| W2 | https://www.akm-semi.com/pdf-c1/ak4458vn.pdf | 200 ✅ | K-Ç1 (§6) |
| W3 | https://www.ti.com/product/PCM3168A | 200 ✅ | K-Ç2 (§7) |
| W4 | https://www.ti.com/lit/ds/symlink/pcm3168a.pdf | 200 ✅ | K-Ç2 (§7) |
| W5 | https://www.ti.com/product/TPA3255 | 200 ✅ | K-Ç3 (§8) — referans |
| W6 | https://www.ti.com/lit/ds/symlink/tpa3255.pdf | 200 ✅ | K-Ç3 (§8) — referans |
| W7 | https://www.smartmontools.org/ | 200 ✅ | K-D1/K-D2 (§9) — SMART aracı |
| W8 | https://www.t13.org/ | 200 ✅ | K-D (§9/4) — ATA komut-seti standartları |
| W9 | https://nvmexpress.org/ | 200 ✅ | K-D (§9/4) — NVMe/TRIM spesifikasyon yayıncısı |
| W10 | https://www.alsa-project.org/ | 200 ✅ | yalnız erişim kaydı — bu turda kapıda KULLANILMADI |
| W11 | https://www.aes.org/ | **403 ❌** | erişilemedi → **kullanılmadı** (uydurulmaz) |

---

## §13 Dürüstlük bloğu (bu derinleştirme)

**Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler 0 uydurma URL/ölçüm içerir;
doğrulanmayan iddialar ⚠️ işaretli.**

| Kontrol | Sonuç |
|---|---|
| §1-§4 silindi mi? | HAYIR — aynen korundu (satır-satır ekleme) |
| Yeni uydurma dosya/klasör | 0 (beyazliste: yalnız bu 2 dosya) |
| Yeni URL | 11 kayıt (§12) — 10× HTTP 200, 1× HTTP 403 (kullanılmadı) |
| Yapılan ölçüm | **0 / 44 adım** (§11) — tüm kapılar UYGULANMADI/BEKLEMEDE |
| Uydurma ölçüm değeri | 0 — yalnız datasheet/kayıt değerleri (§2) ve açıkça "protokol önerisi" olarak işaretlenen test parametreleri |
| UNKNOWN/⚠️ alanlar | K-Ç2 eşzamanlılık eşiği · K-Ç3 tüm iddiaları · K-D envanter · Z5 hoparlör |
| git commit | ATILMADI (subagent yasağı) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode (tek Authority)
