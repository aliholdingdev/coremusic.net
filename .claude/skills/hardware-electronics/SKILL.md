---
name: hardware-electronics
description: "Use when editing Class AB amplifier, power supply, thermal, PCB, or BOM documentation and hardware decisions — Tetikleyiciler: 'amfi tasarımı', 'güç kaynağı', 'termal tasarım', 'PCB/BOM'."
license: Proprietary (CoreMusic internal)
metadata:
  version: 2.1.0
  previous-version: 2.0.0
  format: claude-skill-v3
  author: "Bayram Ali (ULTRATHINK Engineering)"
  category: hardware
  tags:
    - class-ab-amplifier
    - power-supply
    - thermal-design
    - pcb
    - bom
    - dac
  updated: 2026-10-21
---

# Hardware Electronics — Class AB Amfi, Güç, Termal, PCB, BOM

> **Kapsam (§2 domain):** DAC/ADC, PCB, amplifikatör; analog yol, güç, koruma, EMC, BOM.
> **Bu skill tasarımcıdır; üretim/yazılım değildir:** firmware → `dsp-firmware-engineer`,
> üretim → `embedded-systems`; hedef değişikliği onay ister (kural 6).
> **Kaynak hiyerarşisi:** bu dosyadaki sayılar `PROJECTS.md` K16-K20 + ADR-038 +
> `references/sources.md` (S1-S13) gelir. Doğrulanmayan her iddia
> `⚠️ VERIFICATION REQUIRED` taşır (zero-hallucination).

---

## 0. Ne zaman kullanılır / tetikleyiciler

| Tetikleyici | Ne yapılır | Referans |
|---|---|---|
| `amfi tasarımı` | Class AB güç aşaması, bias, koruma | `standard-circuits.md` §1 |
| `güç kaynağı` | LM5122 boost, rail bütçesi, filtre | `standard-circuits.md` §2 |
| `termal tasarım` | P_diss, ΔT, ısıtıcı, fan, KSD301 | `power-thermal-calcs.md` §2-3 |
| `PCB/BOM` | 4 katman, star-ground, EMI, MPN | `pcb-bom-rules.md` |
| datasheet okuma | parametre çıkarımı, çelişki bayrağı | `datasheet-tips.md` |
| arıza tanıs | ölçüm → neden → çözüm | `troubleshooting.md` |

**Yasak yüzeyler (her görevde):**
`git commit/add/write` · `.ai/` vault'a yazma (salt okunur, `hidden: true`) ·
proje kaynak kodu `*.php/*.js/*.css/*.sql` · başka skill dizinleri ·
PCB fab dosyaları (gbr/drill) · firmware · uydurma MPN/fiyat/stok.

---

## 1. Standart iş akışı (her tasarım/gözden geçirmede)

```
1. Girdi topla     : kanal sayısı, yük Ω, rail V, hedef güç, ambalaj/soğutma
2. Hesap et        : P_out → P_diss → ΔT → iz genişliği (power-thermal-calcs §1-4)
3. Devreyi kur     : referans topoloji (standard-circuits §1-3), guardrail ⚠️
4. Kaynak doğrula  : datasheet resmi PDF + 2. kaynak (sources.md §1)
5. Çelişki bayrağı : uyuşmayan sayı → sources.md §3, ASLA tek taraflı çözme
6. PCB kuralı uygula : katman/star-ground/EMI/iz (pcb-bom-rules §1-6)
7. BOM üret        : MPN zorunlu + alternatif + TTL fiyat (pcb-bom-rules §7)
8. Ölçüm protokolü : THD+N · SNR · gain · bit-perfect — 3 koşul, ihlal 0
9. Rapor           : hesap + kaynak + çelişki + ölçüm tablosu + karar
```

**Kapı kuralı (§5 gates):** hesap/ölçüm OLMADAN "geçti/tasarlandı" yazımı →
`VERIFICATION REQUIRED` · gate ihlali → engelle · 3 koşul zorunlu.

---

## 2. Hızlı referans — Sistem spesifikasyonu (K16-K20)

| Anahtar | Değer |
|---|---|
| Mimar | Class AB Darlington, **MJL21194/93** (K16) |
| Güç | **50 W / 8 Ω** kanal · THD **<0.005 %** @1 W · SNR **>105 dB** |
| Kanal | **1-8 modüler** |
| Besleme | **LM5122 ×2**, **±35 V** (boost), **800 W**, **%96** verim (K17) |
| Rail (amfi) | **±42 V**, Iq **70 mA** (kural 3) |
| Termal | **41 W/kanal · 328 W (8 kanal)** · NF-A8 fan · KSD301 (K18) |
| PCB | **4 katman min** · star-ground · gölge · lineer reg (K19) |
| BOM | Mouser/DigiKey · MPN + alternatif (K20) |
| ADC/DAC | PCM3168A 8ch (ADR-038), alt AK4458; **PCM5122 yasak** (kural 1) |
| USB | XMOS XU316: USB izolatör + DSP (kural 2) |

> `%96 / 800 W / ±35 V` **K17 mutlak hedefidir**; kaynak teyidi
> `sources.md` S6 (TI LM5122 datasheet) ile çaprazlanır — doğrulanmamış
> uygulama sayısı `⚠️ VERIFICATION REQUIRED`.

---

## 3. GÜÇ AŞAMASI (Class AB, kural 3 + K16)

### 3.1 Zorunlu parametreler

| Param | Hedef | Kaynak | Doğrulama |
|---|---|---|---|
| Topoloji | Class AB, tamamlayıcı çift | K16 | — |
| Çıkış transistörü | MJL21194 (NPN) / MJL21193 (PNP), Darlington | K16 + S1 | onsemi datasheet rev. |
| Rail | ±42 V | kural 3 | PSU bütçesi (K17 ile uyum) |
| Iq | **70 mA** (bias) | kural 3 | ölçüm ±tolerans |
| Termal kompanzasyon | evet (KSD301 / Vbe multiplier) | K18 | trip noktası C3 |
| Çıkış röle | **DC > 0.5 V** DC koruma | kural 3 | röle DC kesme ratings |
| Sessiz başlangıç | evet (soft-start + mute) | kural 3 | pop ölçümü |
| Koruma | DC, termal, kısa devre | kural 3 + K18 | davranış testi |

### 3.2 Bias (Iq = 70 mA) hesabı

```
P_diss_kanal ≈ f(V_rail, Iq, P_out)   →  K18 tablosu: 41 W/kanal
V_rail = 42 V  →  Iq × V_rail = 0.07 × 42 ≈ 2.94 W (boşta, tek taraf)
Q noktası sıcaklığı:  R_θJC + ısıtıcı → power-thermal-calcs §2
```

**Bias kararlılığı:** Vbe multiplier veya diode kompanzasyon → sıcaklık
**arttıkça akım düşmeli** (negatif T.C. zorunlu). KSD301 trip ısındığında
akımı kısar/röleyi açar (C3 — trip noktası çelişkili).

### 3.3 Çıkış koruma rölesi (kural 3)

- Eşik: **çıkış DC > 0.5 V → röle aç**; gecikme (silent-start ile uyumlu).
- Röle seçimi: DC kesme kapasitesi, temas mΩ, NRND değilse alternatif kod (**K20 §4**).
- Pre-charge direnci: röle öncesi sınırlayıcı (kaynak 47R 5W — `standard-circuits.md` §3).

### 3.4 Topoloji referansı

Bipolar güç aşaması + sürücü + geri besleme iskeleti →
`references/standard-circuits.md` §1 (⚠️ guardrail: şema/simülasyon/firmware bu
skill'de YOK).

---

## 4. GÜÇ KAYNAĞI (K17, kural 3-6)

### 4.1 Mimar

```
AC giriş → EMI filtresi → (PFC yok sayım: bilinmiyor → VERIFICATION REQUIRED)
        → DC bus → LM5122 boost ×2 → +35 V / -35 V
        → lineer reg (analog/rail) → ±42 V amfi rail (transformatör/ikinci arıma: sources.md)
```

| Kalem | Hedef | Not |
|---|---|---|
| Regülatör | **LM5122 ×2** (TI, S6) | boost, çift hat |
| Çıkış | **±35 V** | rail ayrımı ve transformatör oranı ayrı hesap |
| Güç | **800 W** | 8 kanal 328 W + kafa payı → **kaynak/ölçek VERIFICATION REQUIRED** |
| Verim | **%96** | datasheet η tipik ile çapraz (S6) |
| Boşluk-akım/NTC | sessiz start (kural 3) | inrush, pre-charge |

### 4.2 Lineer regülatör (kural 4)

- Sinyal/DAC/önyükseltici hatları → **lineer reg** (şalter gürültüsü yalıtımı).
- Ana güç boost'ta kalır; lineer reg'de **P = (V_in - V_out) × I** → ΔT hesabı
  (power-thermal-calcs §3) zorunlu; büyük düşüşte heatsink gerekir.

### 4.3 Filtre / ana hat

- Ω filtresi (boost çıkış) → kendi R_θJC + kondansatör sıcaklığı (S12/IoS bağlamı yok →
  değer hesapla, `⚠️` işaretle).
- Kondansatör ESR, sıcaklık ömrü (105 °C, saat) → BOM'da MPN ile doğrula (K20).

---

## 5. TERMAL (K18 + kural 3)

### 5.1 Zorunlu zincir

```
P_diss (kanal) → R_θJC → R_θHS → R_θSA (fan) → T_amb → T_case
T_case > trip (KSD301) → koruma aktif
```

| Kalem | Değer | Durum |
|---|---|---|
| P_diss/kanal | **41 W** (K18) | ✓ K18 iç tutarlı (41×8=328) |
| Toplam 8 kanal | **328 W** (K18) | ✓ |
| Fan | **Noctua NF-A8** sınıfı | ⚠️ **C5** (ikincil kaynak) |
| Termal sigorta | **KSD301** | ⚠️ **C3**: 97 °C vs 72 °C |
| Isıtıcı | Fischer SK53 sınıfı | ⚠️ **C1**: 0.42 vs 0.6-1.25 K/W |

### 5.2 Hesap örneği (C1 aralığı)

```
ΔT = P × R_θ :  41×0.42=17.2 · 41×0.6=24.6 · 41×1.25=51.3 °C
T_amb 25 → 42 / 50 / 76 °C
C1-c + C3-b (72 °C trip) → 76 > 72 → KORUMA KALICI AÇILIR → TASARIM ÇALIŞMAZ
```

**Çıkarım:** C1 ve C3 çözülmeden termal/BOM kilitlenmez. Aksiyon →
`sources.md` §3 (2 kaynak doğrulama), tek başına çözüm yok.

### 5.3 Kural kontrolü

- Worst-case: en yüksek R_θ + en yüksek P + en yüksek T_amb + toz/fan
  kirlenmesi payı (fan hava debisi payını hesaba kat).
- Fail-safe: fan dururken trip'e ulaşım; trip sonrası **sessiz reset** beklenmez,
  röle/DC koruma devrede.
- KSD301 **montaj basınçı** ve gövde teması trip'i kaydırır (datasheet-tips §3).

---

## 6. PCB (kural 4 + K19)

### 6.1 Minimum zorunluluklar

| Kural | Uygulama |
|---|---|
| **4 katman min** | L1 sinyal · L2 GND · L3 power · L4 sinyal/güç (K19) |
| EMI gölge | OSC/boost/USB bölgesi gölge halkası (K19) |
| Star-ground | analog + güç toprakları **tek düğüm**, ortak empedans yok (K19) |
| Lineer reg | hassas hatlar (§4.2) |
| Tek taraflık | güç/neon radyal yerleşim; geri besleme kısa |
| Termal via | güç transistörü pad → L2/L4 bakır, via çiti |
| İz | IPC-2221 (S8) — worst-case, tolerans (pcb-bom-rules §3/§6) |

### 6.2 KiCad doğrulama (kural 4, §4-V)

- DRC/ERC çalıştır, **gerçek dosyadan** oku — "temiz" iddiası kanıtsız →
  `VERIFICATION REQUIRED` (kural 5).
- Katman sayısını `pcb-bom-rules.md` §1 tablosuyla karşılaştır.
- Yerleşim denetimi: star-ground düğümü tek nokta mı? boost SW alanı kısalı mı?

---

## 7. EMC / KORUMA / ANALOG YOL

1. **Giriş koruma:** TVS + gerilim sınırlama; kaçak akımı datasheet'ten
   (pcb-bom-rules §5) · ESD sağlamlık: en kötü devre (gerilim/tolerans).
2. **Bölme:** analog (DAC- ön-yükseltici) ↔ güç ↔ USB izolatör; izolatör
   (kural 2) GND bölmesi tek nokta bağ.
3. **Kablo/şema:** giriş-çıkış çiftli, ortak mod çemberi; röle/NTC uzakta.
4. **Toprak döngüsü:** chassis-sinyal GND RC/snubber; K19 star-ground.
5. **DAC/ADC (kural 1):** PCM3168A 8ch (ADR-038) · alternatif AK4458 ·
   **PCM5122 → count 0 → BLOCK**. 8 kanal sayımla doğrula (kural 5).

---

## 8. ÖLÇÜM KAPILARI (§5 — bağlayıcı)

| Gate | Hedef | Tolerans | Durum |
|---|---|---|---|
| THD+N | **-6 dB** hedefi | — | ihlal 0, **3 koşul** |
| SNR | spec (K16 >105 dB) | **±1 dB** | 3 koşul |
| Gain | nominal | **±0.5 dB** | 3 koşul |
| Bit-perfect | bilinen PCM girişi | hattasız | 3 koşul |
| Çıkış DC | röle mantığı | **>0.5 V** → röle | davranış |

**Yöntem (S9-S12):** AES17 / AP ses analizörü / THD+N tanımı (S10) —
**ölçüm yöntemi kaynağa bağlanır**, "gibi görünüyor" yazılmaz.

**Rapor zorunlu alanları:** hesap satırı · kaynak (S no) · ölçüm (3 koşul,
ortalama) · çelişki bayrağı · karar (GEÇTİ/ENGELLENDİ).
Hesap/ölçümsüz iddia → **`VERIFICATION REQUIRED`** (kural 5).

---

## 9. BOM (K20)

**Zorunlu sütunlar:** Rol · Değer/Tip · **MPN** · Tedarikçi P/N · Alt MPN ·
Kaynak (S) · Durum (✓/⚠️) · Fiyat+TTL.
**Kurallar:** Mouser/DigiKey · MPN üretici + distribütör 2 kaynak ·
kritik ≥1 alternatif · NRND yok · PCM5122 sayım 0 · fiyat/stok TTL ≤ 24 s ·
**uydurma yasak** → `⚠️ VERIFICATION REQUIRED`.
Şablon: `examples/bom-extraction.md` §D · ayrıntı: `pcb-bom-rules.md` §7.

---

## 10. ÇELİŞKİ DEFTERİ (özet — asla sessiz çözülmez)

| ID | Konu | Durum |
|---|---|---|
| C1 | Isıtıcı R_θ: 0.42 vs 0.6-1.25 K/W | `sources.md` §3 — 2 kaynak bekliyor |
| C2 | Vceo/Vcbo 250 V ayrımı | datasheet pin kontrolü |
| C3 | KSD301 trip: 97 °C vs 72 °C | aile kodu doğrulanacak |
| C4 | "100W" vs ADR-089 50 W/8 Ω | ADR/PROJECTS okunacak |
| C5 | NF-A8 (ikincil kaynak) | resmi ürün datasheet'i |
| C6 | 800 W / %96 bütçe (328 W yük) | güç haritası revizyonu |

---

## 11. ANTI-PATTERNS (BLOCK)

1. Hesap/ölçümsüz "geçti/uygun/çalışır" → `VERIFICATION REQUIRED`.
2. `PCM5122` kullanmak (kural 1) → **BLOCK** · 8 kanal sayımı.
3. Uydurma MPN/fiyat/stok/URL/sürüm → `VERIFICATION REQUIRED`.
4. Çelişkiyi tek kaynakla sessizce çözme (C1-C6) → madde + 2 kaynak.
5. `.ai/` vault'a yazma · git commit · PCB fab/firmware üretme → yasak.
6. 2 katman "tasarrufu", star-ground'sız yerleşim, shunt DC koruma eşiği → düzeltme.
7. Filler/padding ile satır şişirme → içerik zorunlu (skill-maker anti-patterns).
8. Firmware/üretim işine girme → `dsp-firmware-engineer` / `embedded-systems`
   (kural 6) · hedef değişikliği → onay.

---

## 12. DAC/ADC ve USB (kural 1 + kural 2)

### 12.1 Zorunlu parça kuralı (ADR-038)

| Kural | Durum |
|---|---|
| **PCM3168A** 8 kanal DAC | BİRİNCİL — varsa devam |
| **AK4458** | Onaylı alternatif |
| **PCM5122** | **YASAK — count 0 ise BLOCK** (§1 kural 1) |

**Sayım prosedürü (kural 5):**
1. BOM'da `PCM5122` geçen satır say → **0 olmalı**; >0 ise görev **BLOCK**.
2. 8 kanal: PCM3168A 8ch doğrulaması (ADR-038) + kanal eşlemesi dokümanda.
3. Değişiklik (DAC marka/model) → **hedef değişikliği = onay** (kural 6).

### 12.2 XMOS XU316 (kural 2)

- **İzolatör + DSP tek çipte/zincirde** (kural 2 zorunlu).
- USB bölgesi GND bölmesi `pcb-bom-rules.md` §2 (gölge + tek nokta bağ).
- Firmware/ DSP kodu **bu skill'e girmez** → `dsp-firmware-engineer` (kural 6).

### 12.3 Analog yol sırası (tasarım/kontrol)

```
XU316 (USB izolatör+DSP) → DAC (PCM3168A/alt AK4458) → filtre/DC offset
  → ön-yükseltici (lineer reg besleme §4.2) → Class AB güç aşaması (§3)
  → DC koruma rölesi (§3.3) → hoparlör
```

Kontrol: her aşamanın **kazanç katkısı** toplamı §13 gain hedefine oturmalı
(±0.5 dB gate); kazanç tek noktada toplanmaz (rüzgar/kayan-ekleme riski).

---

## 13. HESAP ATÖLYESİ — tekrar edilebilir sayılar

> Formüller genel mühendislik kanunu (kaynak gerekmez); **sayısal sonuç** her zaman
> girilen değerlerle birlikte yazılır. Eksik girdi → `⚠️ VERIFICATION REQUIRED`.

### 13.1 Çıkış gücü ve tepe değerleri

```
Vp  = √(Pout × RL)          Pout = Vp²/(2·RL)     Vrms = Vp/√2
Ip  = Vp / RL
```

| Pout (8 Ω) | Vp | Vrms | Ip |
|---|---|---|---|
| 1 W | 2.83 V | 2.00 V | 0.35 A |
| 10 W | 8.94 V | 6.32 V | 1.12 A |
| 50 W (K16) | 20.0 V | 14.14 V | 2.50 A |
| 100 W (C4'e dikkat) | 28.3 V | 20.0 V | 3.54 A |

**C4 notu:** ADR-089 = 50 W (25-75 W aralık) SSOT; **"100W" yeni dokümana yazılmaz**
(`sources.md` §3 C4).

### 13.2 Class AB dağılımı

```
Pin(rail) = 2·Vs·Vp/(π·RL)   ·   Pd = Pin − Pout
Maks dağılım: Vp = 2·Vs/π  →  Pd_max = 2·Vs²/(π²·RL)
```

| Vs | RL | Pd_max (çift) | Vp (max) | Pout |
|---|---|---|---|---|
| ±35 V | 8 Ω | 31 W | 22.3 V | 31 W |
| ±42 V | 8 Ω | 45.0 W | 26.7 W | 45 W |

> ±42 V / 8 Ω satırı **hesaptan** gelir (2·42²/(π²·8)=45.0 W); K18'in 41 W/kanal
> bütçesi bunun üzerini (sürücü+güvenlik payı) kapsar → **bütçe ile uyumlu**,
> ama 50 W/8 Ω hedefi sürekli klips değil kısa süreli kabul edilmeli
> (klips-üstü: 50 W'ta Vp=20 V, ±35 V'ta 6.7 V pay; ±42 V'ta 22 V pay — K16 8 Ω).

### 13.3 Termal

```
TJ = TA + Pd·(θJC + θCS + θSA)          ΔT(sink) = Pd × θSA
```

| Pd | θSA | ΔT |
|---|---|---|
| 41 W | 0.42 K/W (C1-a) | 17.2 °C |
| 41 W | 0.65 K/W (C1 gerçekçi, S7) | 26.7 °C |
| 41 W | 1.25 K/W (C1 üst) | 51.3 °C |

**Derating:** `TJ ≤ 110 °C` hedef (S1 mutlak 150 °C) — 328 W tek sink'e gömülmez,
**modüler kanal sink** zorunlu (`power-thermal-calcs.md` §3 karar tablosu).

### 13.4 Besleme akımı (K17)

```
Iin = Pout/(η·Vin)
```

| Vin | Pout | η | Iin |
|---|---|---|---|
| 22.2 V (6S) | 800 W | 0.96 | 37.5 A |
| 19 V (adaptör) | 800 W | 0.96 | 43.9 A ← **worst-case** |

Sigorta: ≥1.5 × 43.9 ≈ **65 A** nominal (yavaş yanma T); per-kanal sigorta tercih
(`power-thermal-calcs.md` §6). LM5122 limitleri: Vin ≤ 65 V, Vout ≤ 100 V (S4) —
±35 V span 70 V → **toplam** iki rail sırasıyla beslenir; her bir regülatörün
kendi Vout 35 V ≤ 100 V ✓.

### 13.5 İz (IPC-2221, S9)

```
I = k·ΔT^0.44·A^0.725      k=0.048 dış / 0.024 iç, ΔT=10 °C, 1 oz
```

| Akım | Dış katman 1 oz | Not |
|---|---|---|
| 1 A | ≈ 13 mil (0.33 mm) | kılcal |
| 3 A | ≈ 47 mil (1.2 mm) | ön-yükseltici |
| 5 A | ≈ 109 mil (2.77 mm) | kanal çıkışı |
| 37.5 A | izle olmaz | **bakır bölge + via demeti** (K19) |

### 13.6 Kazanç (dB)

```
A_dB = 20·log10(A)      A = 10^(A_dB/20)
```

| A | dB |
|---|---|
| 2 | 6.02 |
| 10 | 20.00 |
| 20 | 26.02 |
| 31.6 | 30.00 |

Gate `gain ±0.5 dB` → mutlak değil **sapma** ölçülür (ör. 26.02 ± 0.5 → 25.52…26.52).

### 13.8 Hat direnci, güç düşümü ve ön şarj (tamamlayıcı hesaplar)

```
V_drop = I × R_hat        P_hat = I² × R_hat        V_ilk_an = V / R_pre ,  I_ilk_an = V / R_pre
```

| Hat direnci R̂ | V_drop @ 43.9 A (§13.4 worst-case) | P_hat @ 43.9 A | Yorum |
|---|---|---|---|
| 1 mΩ (sağlam cıvata) | 43.9 mV | 1.93 W | kabul edilebilir başlangıç |
| 5 mΩ (zayıf temas) | 219.5 mV | 9.64 W | ısınma kaynağı — temas kontrolü |
| 10 mΩ (klemens/kablo ucu) | 439.5 mV | 19.3 W | sıcak nokta → kablo/ iz genişliği (§13.5) |

**Ön şarj (pre-charge) kontrolü** — `standard-circuits.md` §3'teki 47 Ω / 5 W
sınırlayıcı için (gerilim düğümü şemadan okunur; aşağıda **örnek** olarak boost
çıkışının 35 V'u alınmıştır — düğüm şemada farklıysa formüle V_bus ile girilir):

```
I_ilk_an = 35 V / 47 Ω ≈ 0.74 A        P_ilk_an = 35² / 47 ≈ 26 W (kısa süreli, sönmekte)
```

- 26 W **anlık** bir güçtür; 5 W'lık direncin kısa süreli **pulse dayanımı**
  datasheet'inden doğrulanır → `⚠️ VERIFICATION REQUIRED` (tek taraflı "yeter" yazılmaz).
- Ön şarj tamamlandıktan sonra röle kapanır (§3.3); kapanış anı da DC koruma
  gecikmesiyle (§3.3) çakışmamalıdır.
- Hat düşümü payı vault'ta tanımlı değil → başlangıç kılavuzu: **mV mertebesinde
  kalınır, W mertebesine çıkan her düşüm PCB/temas gözden geçirmesi ister** (rapor satırı).

---

## 14. ÖLÇÜM PROTOKOLÜ (§5 kapıları — 3 koşul zorunlu)

### 14.1 Kurulum

| Öğe | Değer |
|---|---|
| Yük | 8 Ω (K16), dirençli; hoparlör empedansı **varsa ayrıca** (kapasitif yük testi) |
| Test tonu | **997 Hz** (AES17, S12) · alternatif 1 kHz dokümante |
| Seviye | -1 / -10 / -20 dBFS kombinasyonları (S10); THD+N hedefi için işaretleme seviyesi raporlanır |
| Bant | **20 Hz – 20 kHz** (S11 — bant dışı THD+N raporlanmaz) |
| Analyzer | APx sınıfı; analyzer DUT'tan iyi olmalı (S11) |
| Yöntem | Sine → notch (Q=5, S12) → bant → RMS oran (S10) |
| Koşullar | 3 × (soğuk / 10 dk ısılmış / beklenen yük) — ortalama + sapma |

### 14.2 Gate tablosu (rapor formu)

| Gate | Hedef | Tolerans | Koşul-1 | Koşul-2 | Koşul-3 | Karar |
|---|---|---|---|---|---|---|
| THD+N @997 Hz | -6 dB (§5) | ihlal 0 | | | | |
| SNR | K16 >105 dB | ±1 dB | | | | |
| Gain | nominal (§13.6) | ±0.5 dB | | | | |
| Bit-perfect | bilinen PCM | hattasız | | | | |
| Çıkış DC | — | >0.5 V → röle | | | | |
| Kanal sıcaklığı | K18 <85 °C | trip C3 | | | | |

**Dynamic Range** istenirse: DR = THD+N@-60 dB + 60 dB (S11 örneği: -53 dB → 113 dB).

### 14.3 İhlal döngüsü

```
Gate ihlali → cihaz powered-off · kök neden (troubleshooting.md) → düzelt
  → ÖLÇÜMÜ 3 KOŞULDA TEKRARLA → hâlâ ihlal → ENGELLENDİ (rapor)
```

Tek koşul geçerli değildir; ortalama + sapma yayınlanır. Hesap/ölçümsüz iddia
→ `VERIFICATION REQUIRED` (kural 5).

### 14.4 Ölçümü Bozan Etkiler (sonuç neden yanlıştır — ölçmeden önce taranır)

| # | Etki | Belirti | Kontrol / düzeltme |
|---|------|---------|--------------------|
| 1 | Analyzer gürültü tabanı DUT'tan kötü | THD+N yerinde sayar (floor) | analyzer DUT'tan iyi olmalı (S11); floor ölçülüp rapora yazılır |
| 2 | Yük henüzısınmadan ölçüm | ilk dakika değerleri kayar | 10 dk ısıtma koşulu zorunlu (§14.1 koşul-2) || 3 | Yük sıcaklığı direnci değiştirir | güç/kazanç koşuldan sapar | 3 koşul ortalaması + sapma (§14.3); dirençli yük sıcaklık notu |
| 4 | Temas/klemens direnci | değerler titreşir, tekrar edilemez | bağlantı noktaları sıkılır, ölçüm tekrarlanır; ±tolerans aşılırsa ENGELLENDİ |
| 5 | Masa/toprak döngüsü | 50/100 Hz hum eklendi | izolasyon/toprak düzeni (§7.4), tek nokta bağ kontrolü |
| 6 | Yanlış drive seviyesi | THD+N belirsizleşir | dBFS seviyesi rapora zorunlu yazılır (S10) |
| 7 | Bit-perfect doğrulanmadan dinleme | "temiz" yanıltması | bilinen PCM girişi + hattasızlık (§14.2) |
| 8 | Tek koşul tek sefer ölçüm | tesadüfi iyi/kötü değer | 3 koşul zorunlu; tekrarlanmayan sonuç sayılmaz (§14.3) |

**Kural:** bu etkilerden biri açıkken ölçüm sonucu **tek başına kabul edilmez**;
ölçüm koşulları raporda eksiksiz yazılır (§14.1 kurulum tablosu).

---

## 15. GÖZDEN GEÇİRME CHECKLIST'İ (doküman/BOM/PCB revizyonunda)

**A. Kapsam**
- [ ] Kural 1: BOM'da `PCM5122` **count = 0** (değilse BLOCK)
- [ ] Kural 2: XU316 zincirinde USB **izolatör + DSP** görünür
- [ ] Kural 3: ±42 V · Iq 70 mA · bias+termal kompanzasyon · röle DC>0.5 V · sessiz başlangıç
- [ ] Kural 4: PCB **4 katman min** · EMI gölge · star-ground · lineer reg
- [ ] Kural 5: gate tablosu (§14.2) dolu — 3 koşul, ihlal 0
- [ ] Kural 6: hedef değişikliği/firmware/üretim sınırı — yönlendirme yapılmış

**B. Kanıt**
- [ ] Her dış sayı `sources.md` S-no taşır (S1-S13)
- [ ] Her `⚠️ VERIFICATION REQUIRED` açık (C1-C6 dahil) — kapatılmamışsa raporlanır
- [ ] Her hesap satırı girdi+sonuç içerir (§13 formatı)
- [ ] Fiyat/stok yok veya TTL ≤ 24 s (K20)
- [ ] MPN uydurulmamış — kaynaksız MPN silinir

**C. Yasak yüzey**
- [ ] git commit/atama yok · `.ai/` yazma yok · `*.php/js/css/sql` yok ·
      PCB fab (gbr) / firmware yok · başka skill dizini yok

**D. Çıktı**
- [ ] Raporda: hesap · S-kaynak · 3 koşul ölçüm · çelişki · karar (GEÇTİ/ENGELLENDİ)

---

## 16. ÇIKTI RAPOR ŞABLONU (kısa)

```markdown
### [Görev] — hardware-electronics v2.0.0
Girdi     : <kanal/Ω/rail/güç/hedef>
Hesap     : <Pout/Pd/ΔT/Iin/iz — §13 girdi+sonuç>
Kaynak    : Sx, Sy · vault K1x
Çelişki   : Cx → durum (açık/kapandı, 2 kaynak)
Ölçüm     : 3 koşul tablo (THD+N/SNR/gain/bitsel) veya "önce tasarım aşaması"
BOM       : MPN+alternatif; uydurma yok; TTL
Karar     : GEÇTİ / ENGELLENDİ / VERIFICATION REQUIRED (neden)
Yönlendirme: firmware → dsp-firmware-engineer · üretim → embedded-systems (kural 6)
```

---

## 17. REFERANSLAR (içindekiler — N4)

| Dosya | Ne verir | Zorunlu |
|---|---|---|
| [`references/sources.md`](references/sources.md) | S1-S13 kaynak + **çelişki §3** + erişim 2026-10-21 | **Evet (N4)** |
| [`references/power-thermal-calcs.md`](references/power-thermal-calcs.md) | P/ΔT/iz/gain hesapları + ölçüm §6 | Evet |
| [`references/standard-circuits.md`](references/standard-circuits.md) | Class AB + boost + koruma topolojisi (⚠️) | Evet |
| [`references/datasheet-tips.md`](references/datasheet-tips.md) | BJT/regülatör/termal datasheet okuma | Evet |
| [`references/troubleshooting.md`](references/troubleshooting.md) | arıza tanısı ölçüm→neden→çözüm | Evet |
| [`references/pcb-bom-rules.md`](references/pcb-bom-rules.md) | katman/EMI/iz/BOM kural seti | Evet |
| [`references/changelog.md`](references/changelog.md) | sürüm günlüğü (N10) | Evet |
| [`references/checklist.md`](references/checklist.md) | kalite kapısı: PASS/BLOCK maddeleri (N1-N10 + devre/hesap/BOM kapıları) | **Teslim öncesi son kapı** |
| [`examples/bom-extraction.md`](examples/bom-extraction.md) | analiz→hesap→ölçüm→BOM tam döngü | **Evet (N5)** |

**Yetki zinciri:** proje `AGENTS.md` → `.ai/CLAUDE.md` (SSOT) → K16-K20
(PROJECTS.md) → ADR-038 → bu skill → `sources.md`. Çelişkide **vault kazanır**;
yoksa `VERIFICATION REQUIRED`.

---

*CoreMusic Skill v3.0 — metadata.version: 2.0.0 — Updated: 2026-10-21*
