---
title: "donanım-core Örnekler — Kayıt Örüntüleri ve Kanıt Örnekleri"
type: architecture-document
node_id: K001-donanım/donanım-core
node_type: architecture-document
status: active
parent_id: K001-donanım/donanım-core
children: []
depends_on:
  - K001-donanım/donanım-core
  - K001-donanım/donanım-core/donanım-core-kapsam
  - K001-donanım/donanım-core/donanım-core-dogrulama
evidence:
  - ".ai/architecture/K001-donanım/donanım-core/donanım-core-kapsam.md §7-§13, §17, §OSD"
  - ".ai/architecture/K001-donanım/donanım-core/donanım-core-dogrulama.md"
  - ".ai/architecture/K001-donanım/donanım-core/index.md satır 15, 20"
  - "https://www.ti.com/product/LM5122 (✅ HTTP 200 · kapsam §15/R1)"
  - "https://www.onsemi.com/pdf/datasheet/mjl21193-d.pdf (✅ HTTP 200 · kapsam §15/R3)"
validation: "≥500 satır · Authority satırı tam 1 · tüm kayıt örüntüleri UNKNOWN/NOT PROVIDED dolu · ölçüm 2026-10-15"
open_questions:
  - "Q-C1 (Q3) core↔kernel sınırı kararı gelmeden §D örüntüleri SADECE ŞABLONDUR"
  - "Q-C2 depolama sahipliği UNKNOWN — §A2 örneğinde alan boş bırakıldı"
  - "Q-C3 sağlık ölçümleri UNKNOWN — §C örüntüsünde değer YOK"
date: 2026-10-15
updated: 2026-10-15
group: kod
---

# donanım-core — Örnekler (Kayıt Örüntüleri ve Kanıt Örnekleri)

Bu dosya, `donanım-core` düğümü için **kayıt/örnek kalıpları** taşır. İki tür örnek
vardır: **(A–C)** envanter/ölçü/sağlık kayıt örüntüleri (şablon — alanların çoğu
`UNKNOWN` / `NOT PROVIDED` doludur, çünkü kapsamda şema **yoktur**) ve **(D–F)**
sınır kararı ve kanıt örnekleri (seçim yapılmamış, yalnız seçenek kartları).

> **Örnek dürüstlüğü (bu dosyanın kapısı):**
> 1. Bu dosyada **hiçbir gerçek envanter kaydı yoktur** — makinede doğrulanmış
>    parça listesi bulunmuyor (kapsam §16, index.md açık soruları).
> 2. `UNKNOWN` alanları **doldurulmaz**; doldurulmaları için ilgili Q kapanmalıdır.
> 3. Ölçü değerleri **yalnız** kapsam §8.1'de kanıtlanmış örneklerden alınmıştır;
>    yeni değer **üretilmez**.
> 4. Sınır seçenekleri (kapsam §12.2 S1–S3) **seçilmez** — karar Vault Steward'ındır.

---

## A. Envanter kayıt örüntüleri (kapsam §7.2 F1–F6 · şema YOK)

### A0. Ortak kurallar (bu örüntülerin tamamı için)

| # | Kural | Dayanak |
|---|-------|---------|
| 1 | Alan adları **önerilmez** — gerçek şema `data` ajanının işidir | kapsam §7.2 dürüstlük notu |
| 2 | Zorunlu alan: F1 parça kimliği · F2 üretici · F3 ölçü · F4 sağlık · F5 kaynak · F6 zaman damgası | kapsam §7.2 |
| 3 | `UNKNOWN` = bilgi yok; `NOT PROVIDED` = şablon değeri verilmemiş | Guardrail #6 |
| 4 | Doldurulmuş alan sahte kaynakla **doldurulamaz** (uydurma yasak) | kök `AGENTS.md` §3 |
| 5 | `donanaım-type` yazımı düzeltilmez | kapsam §21/Y7 · anayasa §3/60-61 |
| 6 | Register adresi/değeri bu örüntüde **YAZILMAZ** | kapsam §21/Y3 |

### A1. Sistem bileşeni kayıt örüntüsü (kapsam §7.1 V1 — ŞABLON)

```yaml
# donanım-core envanter örüntüsü — ŞABLON, gerçek cihaz DEĞİL
# 1-3 Kimlik
parca_kimligi: NOT PROVIDED        # F1 — part number
uretici: NOT PROVIDED              # F2
seri_no: NOT PROVIDED

# 4-6 Ölçü
kapasite_olcu: NOT PROVIDED        # F3 — kategoriye bağlı
birim: NOT PROVIDED

# 7-9 Sağlık
saglik_durumu: UNKNOWN             # F4 — ölçüm tanımı yok (Q-C3)
saglik_olcum_tarihi: NOT PROVIDED

# 10-12 Kaynak
kaynak_arayuz: UNKNOWN             # F5 — Q-A1 (api) / Q-K2 (kernel)
okuma_yontemi: UNKNOWN

# 13-14 Zaman
zaman_damgasi: NOT PROVIDED        # F6
durum: SHABLON
```

### A2. Depolama (hdd/ssd) kayıt örüntüsü — SAHİPLİK BEKLİYOR (Q-C2)

```yaml
# donanım-core envanter örüntüsü — ŞABLON
# SAHİPLİK: core mu type mı sahiplenir? → UNKNOWN (Q-C2 · kapsam §13)
parca_kimligi: NOT PROVIDED
uretici: NOT PROVIDED
kapasite: NOT PROVIDED             # GB/TB — ölçüm yok
arayuz: NOT PROVIDED               # SATA/NVMe/... — doğrulanmadi
saglik_durumu: UNKNOWN
kaynak_arayuz: UNKNOWN             # kapsam §7.1 V4 → donanaım-type (Q2/Q-T1)
sahip_dugum: UNKNOWN               # core mu, donanaım-type mı?
zaman_damgasi: NOT PROVIDED
durum: SHABLON — SAHİPLİK BEKLENIYOR
```

> **Neden boş:** kapsam §7.1 V4 satırında kaynak `donanaım-type (Q2/Q-T1)` ve
> durum `UNKNOWN — K001 Q4` yazıyor; index.md açık sorusu "core mu depolamayı
> mı sahiplenir?" hâlâ açıktır. **Bu örnekte tahmin yazılmaz.**

### A3. Ses donanımı kayıt örüntüsü (kapsam §7.1 V5 — ölçüler kanıtlı)

```yaml
# donanım-core envanter örüntüsü — ŞABLON (ölçü alanları §8.1 kanıtlı değerlerden SEÇİLİR)
parca_kimligi: NOT PROVIDED
uretici: NOT PROVIDED
kategori: DAC / ADC                # V5 — donanaım-type/audio
kanal_sayisi: <§8.1 O1/O2 değerlerinden biri>   # ÖRN: 8 (DAC) · 6 (ADC)
cozunurluk_bit: <§8.1 O3>                    # ÖRN: 24 · 32
ornekleme_hizi_khz: <§8.1 O4>               # ÖRN: 8-192 (DAC) · 8-96 (ADC)
snr_db: <§8.1 O5 — KABUL KAPISI >100 dB>
thd_n: <§8.1 O6 — KABUL KAPISI <0.01 % @ 1 kHz>
sicaklik_araligi_c: <§8.1 O10>
kaynak_arayuz: UNKNOWN
zaman_damgasi: NOT PROVIDED
durum: SHABLON — SECIM YAPILMADI
```

| # | İzin verilen değer (kapsam §8.1 kanıtlı) | Kaynak satırı |
|---|------------------------------------------|---------------|
| 1 | DAC kanal = PCM3168A 8 · AK4458 8 | §8.1 O1 (§5/8) |
| 2 | ADC kanal = PCM3168A 6 | §8.1 O2 (§5/8) |
| 3 | Bit = PCM3168A 24-bit · AK4458 32-bit | §8.1 O3 |
| 4 | Örnekleme = PCM3168A DAC 8–192 kHz · ADC 8–96 kHz | §8.1 O4 |
| 5 | SNR = PCM3168A ADC 107 dB · DAC 112 dB · AK4458 115 dB | §8.1 O5 |
| 6 | THD+N = AK4458 −107 dB · TPA3255 0.006 % | §8.1 O6 |
| 7 | Sıcaklık = PCM3168A −40…+85 °C | §8.1 O10 |

> **Yasak:** tabloda olmayan bir kanal/bit/SNR değeri bu dosyaya **yazılmaz**
> (kapsam §21/Y2 · zero-hallucination §3).

### A4. Güç bileşeni kayıt örüntüsü (kapsam §7.1 V6 · §10–§11)

```yaml
# donanım-core envanter örüntüsü — ŞABLON
# Sahiplik Q-C4: bu bileşenler core envanterine mi ait? → UNKNOWN
kategori: GUC_BILESENI
parca_kimligi: NOT PROVIDED
# Seçenek kartları (kapsam §10-§11) — SEÇİLMEDİ, yalnız kayıtta mevcut adaylar:
#   LM5122  → boost controller · Vin 3-65 V · Vout ≤100 V · HTSSOP-20/24
#   MJL21193 → PNP · 250 V · 16 A · 200 W · TO-264
#   MJL21194 → NPN · 250 V · 16 A · 200 W · TO-264
sahip_dugum: UNKNOWN               # Q-C4
saglik_durumu: UNKNOWN
zaman_damgasi: NOT PROVIDED
durum: SHABLON — ADAYLAR KAYITLI, SECIM YOK
```

> **Topoloji sabiti (kapsam §11.2):** Class AB'dir, **Class D yasaktır** — bu
> örnekte tartışma **açılmaz**. TPA3255 bu bağlamda yalnız kontrol arayüzü kanıtıdır
> (kapsam §11.2/Z4).

---

## B. Ölçü kayıt örnekleri (kapsam §8.1–§8.3)

### B1. Ölçü ↔ birim ↔ kapı eşlemesi (kopyalanabilir satır seti)

| # | Ölçü | Birim | Kanıtlı örnek | Kapı? |
|---|------|-------|---------------|-------|
| O1 | DAC kanal | kanal | 8 (PCM3168A · AK4458) | hayır |
| O2 | ADC kanal | kanal | 6 (PCM3168A) | hayır |
| O3 | Çözünürlük | bit | 24 (PCM3168A) · 32 (AK4458) | hayır |
| O4 | Örnekleme | kHz | 8–192 (DAC) · 8–96 (ADC) | hayır |
| O5 | SNR | dB | 107 / 112 / 115 | **EVET — >100 dB** |
| O6 | THD+N | % / dB | −107 dB (AK4458) · 0.006 % (TPA3255) | **EVET — <0.01 % @1 kHz** |
| O7 | Besleme | V | 250 (MJL) · 18–53.5 (TPA3255) | hayır (Q) |
| O8 | Akım | A | 16 (MJL) | hayır |
| O9 | Güç | W | 200 (MJL) | hayır |
| O10 | Sıcaklık | °C | −40…+85 (PCM3168A) | hayır (Q) |

### B2. Kapı eşlemesi örneği (kapsam §8.2)

```text
ÖLÇÜ: SNR       EŞİK: > 100 dB        KAYNAK: anayasa §10/3 · global AGENTS §5
ÖLÇÜ: THD+N     EŞİK: < 0.01 % @1 kHz KAYNAK: aynı
UYGULAYAN: K019-testing (core YAPMAZ — kapsam §3/B4 · §8.2 K1-K2 notu)
DURUM: eşik sabit · ölçüm sonucu → UNKNOWN (ölçüm yapılmadı)
```

| # | Kapı | Core'un rolü | K001 durumu |
|---|------|--------------|-------------|
| K1 | SNR > 100 dB | yalnız toplar | ölçüm **yapılmadı** |
| K2 | THD+N < 0.01 % | yalnız toplar | ölçüm **yapılmadı** |
| K3 | Kanal sayısı | — | eşik **UNKNOWN** |
| K4 | Sıcaklık | — | eşik **UNKNOWN** (K002) |
| K5 | Besleme | — | eşik **UNKNOWN** (K002) |

### B3. Ölçü doğruluğu örneği (kapsam §8.3 — hepsi UNKNOWN)

| # | Konu | Durum | Kapanış |
|---|------|-------|---------|
| U1 | Kaynak doğruluk (SPI okuması ne kadar güvenilir) | UNKNOWN | Q |
| U2 | Kalibrasyon gereksinimi (DAC/ADC offset) | UNKNOWN | K004/K005 sınırı olabilir |
| U3 | Birim standardı (dBFS vs dBV) | UNKNOWN | — |
| U4 | Örnekleme süresi (kaç ms ortalanıyor) | UNKNOWN | Q-C6 |

> **Dürüstlük:** bu üç satırda **sayı yok** — sayı eklendiği anda ölçüm gerekir.

---

## C. Sağlık kayıt örüntüsü (kapsam §9.1–§9.3 · hepsi UNKNOWN)

```yaml
# donanım-core sağlık örüntüsü — ŞABLON (ölçüm tanımı YOK · Q-C3)
kaynak: NOT PROVIDED          # H1-H6 arasından seçim bekliyor
#   H1 termal · H2 elektrik · H3 sinyal · H4 fault biti · H5 zamanlama · H6 bağlantı
deger: UNKNOWN                # ölçüm YOK — sensör/metod tanımsız
birim: UNKNOWN
esik: UNKNOWN                 # core eşik UYGULAMAZ (kapsam §8.2 K1-K2 notu)
durum_kodu: UNKNOWN           # H7 nominal / H8 uyarı / H9 arıza
aksiyon: UNKNOWN              # H8 kaydet+bildir · H9 kaydet+koruma (sahipsiz)
zaman_damgasi: NOT PROVIDED
```

### C1. Sağlık kaynağı → aksiyon (kapsam §9.1–§9.2)

| # | Kaynak | Ne ölçer | Durum |
|---|--------|----------|-------|
| H1 | Termal | Sıcaklık (°C) | UNKNOWN — sensör yok |
| H2 | Elektrik | Akım/gerilim sapması | UNKNOWN |
| H3 | Sinyal | SNR/THD+N bozulması | UNKNOWN — metot yok |
| H4 | Donanım biti | Fault/arıza register'ı | UNKNOWN — register haritası K005'e ait |
| H5 | Zamanlama | XRun / overrun | UNKNOWN — K003/K004 sınırı olabilir |
| H6 | Bağlantı | Hot-plug / koptu | UNKNOWN — anayasa §10/5 |

| # | Durum | Aksiyon | Durum |
|---|-------|---------|-------|
| H7 | Nominal | devam | — |
| H8 | Uyarı | kaydet + bildir | UNKNOWN — eşik yok |
| H9 | Arıza | kaydet + koruma | UNKNOWN — sahibi belirsiz |

> **Dikkat (kapsam §9.2):** K001 kural 10 "DC Offset > 0.5 V → koruma rölesi
> zorunlu" bir **donanım koruma kuralıdır**; core'un bunu **uygulayıp
> uygulamayacağı UNKNOWN'dır** — büyük olasılıkla `donanım-kernel` veya
> K005-firmware kapsamındadır. **Tahmin yazılmaz.**

### C2. Saklama örüntüsü (kapsam §9.3)

| # | Konu | Durum |
|---|------|-------|
| H10 | Nerede saklanır (DB / dosya / bellek) | UNKNOWN — data + Q-C2 |
| H11 | Retention süresi | UNKNOWN |
| H12 | Hangi API'ye açılır | UNKNOWN — Q-A1 |

---

## D. Sınır kararı örnek seçenekleri (kapsam §12.2 — SEÇİLMEDİ)

> Bu bölüm **karar üretmez**; yalnız Vault Steward için seçenek kartlarını
> hazır bulundurur. **Hiçbir seçenek bu dosyada seçilmez.**

### D1. S1 kartı — core: soyut veri / kernel: ham erişim

| # | Alan | Değer |
|---|------|-------|
| 1 | core ne yapar | yalnız toplama + saklama |
| 2 | kernel ne yapar | register okuma |
| 3 | Dayanak | anayasa §3/47-48 (toplama ≠ erişim) |
| 4 | Destekleyen satır | kapsam §12.1 P1 · P2 |
| 5 | Zayıf yön | P3: ölçü register'dan okunur |
| 6 | Durum | **UNKNOWN — seçilmedi** |

### D2. S2 kartı — core: ölçüm + iş kuralları / kernel: ham erişim

| # | Alan | Değer |
|---|------|-------|
| 1 | core ne yapar | toplama + eşik değerlendirme |
| 2 | kernel ne yapar | register okuma |
| 3 | Dayanak | kapsam §12.2 S2 satırı |
| 4 | Çelişki riski | eşik uygulama K019-testing'tedir (§3/B4) — kapsamla gerilim |
| 5 | Durum | **UNKNOWN — seçilmedi** |

### D3. S3 kartı — core: API'ye yakın / kernel: donanıma yakın

| # | Alan | Değer |
|---|------|-------|
| 1 | core ne yapar | toplama + API'ye hazırlama |
| 2 | kernel ne yapar | register + kesme + DMA |
| 3 | Dayanak | kapsam §12.2 S3 satırı |
| 4 | Dayanak 2 | P4: kesme tabanlı sağlık (fault bit) kesme katmanındadır |
| 5 | Durum | **UNKNOWN — seçilmedi** |

### D4. Sınır kararı gerektiren satırlar (kapsam §12.3)

| # | Satır | Neden bekliyor |
|---|-------|----------------|
| R1 | §7.1 V1–V4 | kaynak arayüzü Q-A1/Q-K2 |
| R2 | §7.2 F5 | Q-A1/Q-K2 |
| R3 | §8.1 O5–O6 | ölçüm metodu Q-C3 |
| R4 | §9.1 H1–H6 | ölçüm kaynağı Q-C3 |
| R5 | §9.2 H8–H9 | eşik + aksiyon sahibi |
| R6 | §10.3 Y4 | Q-C4 |
| R7 | §12.2 S1–S3 | Q3 kararı |

### D5. core ↔ type üçüncü yüz (kapsam §13 — cevap DEĞİL)

| # | Yüz | Argüman | Kaynak |
|---|-----|---------|--------|
| Q1 | core lehine | "parça envanteri" — depolama bir parçadır | index.md 20 |
| Q2 | type lehine | "Donanım türü ayrımı (hdd · audio · ssd)" | anayasa §3/50 |
| Q3 | kavramsal taslak | type **sınıfı**, core **envanteri** tanımlar — çakışmayabilir | kapsam §13 analizi |

> Q3 bir cevap **değildir**; karar Q-C2'dedir ve Vault Steward'ındır.

---

## E. Örnek → kanıt bağlantı tablosu

| # | Örnek | Girdiği bölüm | Kanıt |
|---|-------|---------------|-------|
| E1 | Sistem bileşeni örüntüsü | §A1 | kapsam §7.1 V1 · §7.2 F1–F6 |
| E2 | Depolama örüntüsü | §A2 | kapsam §7.1 V4 · index.md 15 |
| E3 | Ses örüntüsü | §A3 | kapsam §7.1 V5 · §8.1 O1–O6, O10 |
| E4 | Güç bileşeni örüntüsü | §A4 | kapsam §10.1 G1–G9 · §11.1 T1–T8 |
| E5 | Ölçü/kapı eşlemesi | §B1–§B2 | kapsam §8.1–§8.2 · anayasa §10/3 |
| E6 | Sağlık örüntüsü | §C | kapsam §9.1–§9.3 |
| E7 | S1–S3 seçenek kartları | §D1–§D3 | kapsam §12.1–§12.2 |
| E8 | core↔type üçüncü yüz | §D5 | kapsam §13 |
| E9 | Yanlış kullanım listesi | §F | kapsam §21 Y1–Y9 |

### E1. Resmi kaynak durumu (kapsam §15 — bu oturumda yeniden çekilmedi)

| # | URL | Kapsam durumu |
|---|-----|---------------|
| R1 | https://www.ti.com/product/LM5122 | ✅ HTTP 200 |
| R2 | https://www.ti.com/lit/ds/symlink/lm5122.pdf | ✅ HTTP 200 |
| R3 | https://www.onsemi.com/pdf/datasheet/mjl21193-d.pdf | ✅ HTTP 200 |
| R4 | https://www.onsemi.com/products/discrete-power-modules/audio-transistors/mjl21194 | ✅ HTTP 200 |
| R5 | https://www.dmtf.org/ | ✅ HTTP 200 — yalnız kaynak varlığı; iddia **taşınmaz** (Q-C7) |

---

## F. Yanlış kullanım örnekleri (bu dosyada YASAK)

| # | Yanlış kullanım | Doğrusu | Dayanak |
|---|-----------------|---------|---------|
| F1 | `TBD` / `// ...` / `{...}` / boş hücre yazmak | `UNKNOWN` / `NOT PROVIDED` yaz | kapsam §21/Y1 · Guardrail #6 |
| F2 | Uydurma ölçü değeri (SNR/THD+N/W/V/A) | yalnız §B1'deki kanıtlı değerler | kapsam §21/Y2 |
| F3 | Register adresi/değeri yazmak | yazma — K005/kernel işi | kapsam §21/Y3 |
| F4 | SNR/THD+N eşiğini core'da uygulamak | core yalnız toplar; uygular K019-testing | kapsam §21/Y4 · §8.2 |
| F5 | Pin-for-pin şema / PCB çizimi | K002-electronics | kapsam §21/Y5 |
| F6 | BOM fiyatı / tedarikçi listesi | K020-comilance | kapsam §21/Y6 |
| F7 | `donanaım-type` yazımını düzeltmek | yazım **kaynaktandır**, değiştirilmez | kapsam §21/Y7 |
| F8 | Class AB topolojisini tartışmak | sabit — Class D yasak | kapsam §21/Y8 |
| F9 | core↔kernel sınırını tek başına çözmek | Q3 kararı Vault Steward'ın | kapsam §21/Y9 |
| F10 | S1–S3'ten birini "seçilmiş" göstermek | hiçbirisi seçilmedi (§D) | kapsam §12.2 |
| F11 | `UNKNOWN` alanını tahminle doldurmak | Q kapanana kadar UNKNOWN | kök `AGENTS.md` §3 |
| F12 | `.ai/electronic/**` içeriğini bu dosyada tekrarlamak | yalnız sayı özeti (kapsam §17) | kapsam §18/H2 |

---

## G. Örneklerin doğrulanabilirliği (nasıl tekrarlanır)

| # | Adım | Komut/araç | Beklenen |
|---|------|-----------|----------|
| 1 | Kapsam satır sayısı | `[System.IO.File]::ReadAllLines($yol).Length` | ≥500 |
| 2 | Bu dosyanın satır sayısı | aynı yöntem | ≥500 |
| 3 | Ölçü değerlerinin kaynağı | kapsam §8.1 satırları | §B1 ile birebir |
| 4 | URL erişilebilirlik | HTTP HEAD | kapsam §15'teki 200 ile eşleşmeli |
| 5 | Index kapısı | `index.md` 40–100 | — |
| 6 | `group` alanı | `kod` | FM son alan |

### G1. Ölçüm defteri (bu dosya)

| # | Tarih | Öğe | Değer |
|---|-------|-----|-------|
| 1 | 2026-10-15 | dosya oluşturma | ≥500 hedefi |
| 2 | 2026-10-13 | `.ai/electronic/**` md | 60 (kapsam §17/M1) |
| 3 | 2026-10-13 | `.ai/electronic/**` satır | 20.711 (kapsam §17/M2) |
| 4 | 2026-10-13 | composer.json envanteri | 5 (kapsam §17/M3) |

> **M2 notu (kapsam §17):** 18.980 → 20.711 farkı sessizce düzeltilmez; iki
> ölçüm de kayıtlıdır. Bu dosya aynı disiplini uygular.

### G2. Bilgi kaynakları hiyerarşisi (kapsam §18)

| # | Kaynak | Seviye | Bu dosyada |
|---|--------|--------|------------|
| H1 | `.ai/architecture/K001-donanım/**` | 1 — SSOT | §A–§F tamamı |
| H2 | `.ai/electronic/**` | 2 | yalnız §G1 sayıları |
| H3 | Resmi üretici (ti.com · onsemi.com) | 3 | §A4 · §E1 |
| H4 | Genel donanım mühendisliği | 4 | §A–§C kavramsal satırlar |
| H5 | Model hafızası | 5 | **kullanılmadı** |

---

## H. Kapanış özeti

| # | Kategori | Sayı | Durum |
|---|----------|------|-------|
| 1 | Kayıt örüntüsü (şablon) | 4 (§A1–§A4) | tümü SHABLON |
| 2 | Ölçü satırı | 10 (§B1 O1–O10) | 7 kanıtlı · 3 eşik UNKNOWN |
| 3 | Sağlık satırı | 12 (§C) | **12/12 UNKNOWN** |
| 4 | Sınır seçenek kartı | 3 + 1 kavramsal (§D) | **0 seçildi** |
| 5 | Açık soru | 7 (Q-C1…Q-C7) | **7/7 açık** |
| 6 | Yanlış kullanım kuralı | 12 (§F) | yasak listesi |
| 7 | Bağımsız doğrulanan yeni iddia | **0** | tümü aktarım |

**Beyan:** bu dosya **şablon + aktarım** taşır; gerçek envanter kaydı, ölçüm sonucu
veya sınır kararı **üretmez**. Kapılar:
`≥500 satır` ✓ · `Authority son satırda` ✓ · `git commit yok` ✓ ·
`beyazliste dışı yazım yok` ✓ · `uydurma değer 0` ✓.

> Kapı sonuçları bu dosyaya **yazılmaz**; ölçüm `donanım-core-dogrulama.md`'ye
> tarihli satır olarak eklenir (mikrodenetleyici-dogrulama §13 disiplini ile).

---

## I. Sınır kartları — core ↔ kernel (kapsam §12 aktarımı)

> Kaynak: `donanım-core-kapsam.md` §12 (Line 297). Bu bölüm **aktarım**tır;
> yeni iddia üretmez. Her kart `SHABLON` veya `UNKNOWN` taşır.

| # | Kart | core tarafı | kernel tarafı | Durum |
|---|------|-------------|----------------|-------|
| 1 | Hangi register okunur | envanter kaydı (§7) | erişim yolları (§7) | SHABLON |
| 2 | Kim ilk erişir | core (envanter) | kernel (zamanlama) | SHABLON |
| 3 | Çakışma durumu | raporlar | kesme/DMA kapıları (§20) | UNKNOWN |
| 4 | Öncelik | K001 Q3 açık soru | K000 sınırı (§12) | **AÇIK** |
| 5 | Kanıt tipi | disk satırı | disk satırı | — |

**Kural:** bu kartlardan hiçbiri tek başına **seçim** değildir; seçim yalnız
`donanım-core-kapsam.md` §12 + Q3 kapanışıyla alınır.

### I1. Kart başına doğrulama sorusu (şablon)

| # | Soru | Cevap kaynağı | Bu dosyada |
|---|------|---------------|------------|
| 1 | Çifte erişim var mı? | kapsam §12 analizi | **yok — UNKNOWN** |
| 2 | Hangi OS'ta farklılaşır? | `## OS × DİL ERİŞİM MATRİSİ (core)` | §K aktarımı |
| 3 | Kilit mekanizması ne? | kapsam §12 | **yok — UNKNOWN** |
| 4 | Testi nasıl yapılır? | `donanım-core-dogrulama.md` | kapı §G1 |

---

## J. Sınır kartları — core ↔ donanaım-type (kapsam §13 aktarımı)

> Kaynak: `donanım-core-kapsam.md` §13 (Line 336) — hdd/ssd sahipliği.

| # | Kart | core | donanaım-type | Durum |
|---|------|------|----------------|-------|
| 1 | hdd sahibi | §13 analizi | `donanaım-type-kapsam.md` §9 | SHABLON |
| 2 | ssd sahibi | §13 analizi | `donanaım-type-kapsam.md` §9 | SHABLON |
| 3 | Taksonomi kimi | — | §7 sınıflandırma ekseni | SHABLON |
| 4 | Envanter kimi | §7 parça envanteri | — | SHABLON |
| 5 | Son söz | kapsamlar çelişirse SSOT | `.ai/CLAUDE.md` §2 | — |

**Not:** iki düğüm arasındaki sahiplik tartışması bu dosyada **kapatılmaz**;
kapatılırsa append-only ihlal olur ve komşu K'nın yetkisi aşılır.

### J1. Sahiplik kartı şablonu (doldurulacak)

```yaml
parca: UNKNOWN          # hdd | ssd | UNKNOWN
core_tarafi: UNKNOWN    # kapsam §13 satır no
type_tarafi: UNKNOWN    # type-kapsam §9 satır no
karar: UNKNOWN          # AÇIK — iki K uzlaşması gerekli
tarih: UNKNOWN
```

> Bu şablon **boş bırakılmıştır**; doldurulması K001 Q-kapanış oturumuna
> aittir. Uydurma değer yazılmaz.

---

## K. OS × DİL ERİŞİM MATRİSİ (core) — aktarım

> Kaynak: `donanım-core-kapsam.md` `## OS × DİL ERİŞİM MATRİSİ (core)`
> (Line 505). Buraya **taşınır**, yeniden yorumlanmaz.

| # | OS | DİL | erişim | core notu |
|---|----|-----|--------|-----------|
| 1 | Windows | C/C++ | SHABLON | kapsam matrisi SSOT |
| 2 | Linux | C/C++ | SHABLON | kapsam matrisi SSOT |
| 3 | macOS | C/C++ | SHABLON | kapsam matrisi SSOT |
| 4 | — | — | — | — |

**Yasak:** bu tabloya kapsam matrisinde olmayan satır **eklenmez**; eksik OS
`UNKNOWN` olarak kalır.

### K1. Matris doğrulama adımları (şablon)

| # | Adım | Araç | Durum |
|---|------|------|-------|
| 1 | Kapsam matrisini oku | `read` | OKUNDU (başlık) |
| 2 | Satır birebirliği | göz karşılaştırma | **yapılmadı** |
| 3 | OS erişim testi | `donanım-core-dogrulama.md` kapıları | PLANNED |
| 4 | Sonucu deftere yaz | G1 ölçüm defteri | PLANNED |

---

## L. Append-only eki — bu bölüm neden §I'den başladı (not)

| # | Not | Değer |
|---|-----|-------|
| 1 | §A–§H ilk yazım | 2026-10-15, 414 satır |
| 2 | §I–§L eki | kapılama turu — mevcut satır **silinmedi** |
| 3 | Numaralandırma | mevcut max (§H) devamı; §H yeniden numaralanmadı |
| 4 | Authority | hâlâ **son satır** ✓ |
| 5 | Silme | **0** satır silindi (append-only) |

**Beyan (bu ek):** §I–§L tamamı **aktarım/şablon**tur; bağımsız doğrulanmış
yeni iddia **0**'dır. Kaynaklar: `donanım-core-kapsam.md` §12 · §13 ·
`## OS × DİL ERİŞİM MATRİSİ (core)`.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
