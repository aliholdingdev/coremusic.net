# Örnek — Devre Analizi → Ölçüm → BOM Çıkarımı (tam döngü)

> Bu örnek, skill'in günlük döngüsünü uçtan uca gösterir:
> **analiz → hesap → ölçüm protokolü → BOM çıktısı** (N5).
> Girdi: tek kanal Class AB güç aşaması (K16) + 800 W besleme (K17).
> Çıktılar: 4 sayı (güç/ısı/iz/gain) + ölçüm listesi + BOM tablosu.

---

## A. Girdi veri seti

```
Proje      : CoreMusic 1-8 kanal Class AB amplifikatör (ADR-038 / PROJECTS.md §7.2)
Kanal      : 1 adet, 8 Ω yük
Köprü      : MJL21194 (NPN) + MJL21193 (PNP) Darlington çifti
Güç        : ±42 V / kanal (K16), Iq = 70 mA
Kaynak     : LM5122 ×2 (boost, ±35 V → K17 800 W toplam)
Soğutma    : Alüminyum ısıtkıcı + NF-A8 fan (K18), KSD301 termal koruma
PCB        : 4 katman, L2 = GND, L3 = power (K19)
BOM kanalı : Mouser/DigiKey (K20)
```

**Türetilmiş (hesap edilecek):** P_out, P_diss, ΔT, iz genişliği, kanal sayısı
**Ölçülecek (gate):** THD+N, SNR, gain, bit-perfect

> ⚠️ Buradaki tüm sayılar **hesapla** üretilir; ölçüm olmadan "geçti" yazılmaz (N5).

---

## B. Hesap adım-adım (`references/power-thermal-calcs.md` §1-§4)

### B1. Çıkış gücü (K16: 50 W / 8 Ω)

```
V_pk  = √(P × R) = √(50 × 8) = 20 V
V_rms = V_pk / √2            = 14.14 V
I_pk  = 20 / 8               = 2.5 A
```

Rail marjı: ±42 V → tepe 42 V; sürücü+çıkış kaybı için **20 V tepe uygundur**
(kenar 22 V, doğal olarak sıcaklık/gövde düşüşünü kapsar — K16 hedefi 50 W/8 Ω).

### B2. Kanal başına çözülen güç (K18: 41 W)

En kötü durum = köprü yükü (saf dirençli yük, tepe sürme):

```
P_diss ≈ V_rail × I_q-bos... (K18 tablosu ile çapraz kontrol)
Tablo (PROJECTS.md §7.2.3): 41 W / kanal → 328 W (8 kanal)
```

Kontrol: `328 = 41 × 8` ✓ (K18 iç tutarlı).

### B3. Güç kaynağı bütçesi (K17: 800 W, %96)

```
P_in  = P_out_total / η = (41 × 8 × factor) / 0.96
```

K17 spec "800 W / %96 boost": **41×8 = 328 W** artı kafa payı (8 kanal × pik) →
800 W kaynağı **bütçenin üstünde** kalan pay; ayrıntılı slot hesabı
`⚠️ VERIFICATION REQUIRED` (revize güç haritası K17'de yok).

### B4. Termal (K18: 41 W → ΔT)

Isıtıcı seçimi C1'e tabi (`sources.md` §3):

```
ΔT = P_diss × R_θ
  C1-a: 41 × 0.42  = 17.2 °C   (Fischer SK53 100 SA — ürün sayfası)
  C1-b: 41 × 0.6   = 24.6 °C   (alt sınır)
  C1-c: 41 × 1.25  = 51.3 °C   (üst sınır)
Ortam 25 °C → gövde: 42 / 50 / 76 °C
KSD301 trip: 97 °C (C3-a) vs 72 °C (C3-b) — C1-c + C3-b = ÇALIŞMAZ
```

**Karar:** C1 ve C3 **çözülmeden** fan/termal BOM kilidi yok.
Aksiyon: Fischer SK53 datasheet revizyonu + KSD301 aile kodu 2 kaynaktan doğrula
(Kaynak S4, `sources.md`).

### B5. İz genişliği (pcb-bom-rules §6, IPC-2221)

Çıkış fazı beklenen tepe akımı 2.5 A, emniyet ×2 → 5 A:

```
I = k·ΔT^0.44·A^0.725, k=0.048 (external, level 1b), ΔT=10
A = I^(1/0.725) / (k·ΔT^0.44) = ... → 0.049 mm² ≈ 10 mil (1 oz) sınır →
5 A için ≥ 2× alan ≈ 20 mil (1 oz) veya L3 power plane'de alan bakır.
```

Rail (±42 V) taşıyıcıları L3'te **plane bölgesi** ile (K19).

### B6. Kazanç (gate girdisi)

Kanal voltaj kazancı (A_v) ölçüm öncesi **hedef** olarak yazılır; ölçüm
`gain ±0.5 dB` gate'ine göre yapılır. Hesap örneği:

```
A_v_dB = 20·log10(A_v), hedef A_v = 20 → 26.02 dB
Gate: ±0.5 dB → 25.52…26.52 dB aralığı
```

---

## C. Ölçüm protokolü (`references/power-thermal-calcs.md` §6 + troubleshooting §4)

| # | Ölçüm | Koşul | Alet | Gate |
|---|---|---|---|---|
| 1 | THD+N @1 kHz | 1 W, 8 Ω, soğuk | AP/ses analizörü | **-6 dB hedef** (S9-12 yöntemi) |
| 2 | SNR | giriş kısa, röle açık | AP | **±1 dB** (K16 >105 dB) |
| 3 | Gain @1 kHz | 1 kHz -10 dBFS | AP | **±0.5 dB** |
| 4 | Bit-perfect | bilinen PCM dizisi | loopback | hattasız kopya |
| 5 | Çıkış DC | röle öncesi/sonrası | multimetre | >0.5 V → röle açıksa beklenti |
| 6 | Kanal sıcaklığı | 10 dk sürekli | IR/termokupl | C1'e göre yorum |
| 7 | Hum | spectrum 50/100 Hz | AP | K19 doğrulaması |

**Protokol (3 çalıştırma, ihlal 0 — §5):**
1. Aynı kurulumda **3 kez** çalıştır → ortalama sapma raporla.
2. Gate ihlali → cihaz kapalı, kök neden (`troubleshooting.md`), düzelt, tekrar 3.
3. **Hesap/ölçüm OLMADAN iddia** → `VERIFICATION REQUIRED` etiketi.

**Sayılar (doldurulacak — ölçüm sonrası):**

| Gate | Çalıştırma 1 | 2 | 3 | Karar |
|---|---|---|---|---|
| THD+N | _ø_ | _ø_ | _ø_ | |
| SNR | _ø_ | _ø_ | _ø_ | |
| Gain | _ø_ | _ø_ | _ø_ | |
| bit-perfect | | | | |

---

## D. BOM çıktısı (`pcb-bom-rules.md` §7)

**Tam döngü:** B1-B5 → özet satırlar → MPN doğrulama → çelişki bayrakları.

### D1. Güç aşaması kanal BOM (8 kanal × bu set)

| # | Rol | Değer/Tip | MPN | Tedarikçi P/N | Alt MPN | Durum |
|---|---|---|---|---|---|---|
| 1 | NPN çıkış | Darlington NPN | MJL21194G | (distribütör) | ? | ✓ resmi onsemi + 2 dağıtım |
| 2 | PNP çıkış | Darlington PNP | MJL21193G | (distribütör) | ? | ✓ |
| 3 | Sürücü | ? | **?** | ? | ? | `⚠️ VERIFICATION REQUIRED` |
| 4 | Isıtkıcı | Alüminyum SK53 sınıfı | **C1 çelişkisi** | ? | ? | `⚠️ C1` |
| 5 | Termal sigorta | bimetal 97 °C / 72 °C | **C3 çelişkisi** | ? | ? | `⚠️ C3` |
| 6 | Fan 80 mm | NF-A8 sınıfı | **C5** | ? | ? | `⚠️ C5` (ikincil kaynak) |
| 7 | Çıkış röli | DC kesme ≥0.5 V ayrımı | **NRND/kod yok** | ? | ? | `⚠️ §4` |

*Tablo: değerler `sources.md` §1-§3'ten gelir; boş MPN uydurulmaz (zero-hallucination).*

### D2. Besleme (K17)

| # | Rol | Tip | Not |
|---|---|---|---|
| 1 | Boost regülatör | LM5122 | ×2 (±35 V) — TI datasheet S6 |
| 2 | Güç indüktörü | toroid | f_SW + I_pk hesabından → `⚠️` |
| 3 | Ana filtre C | elektrolit | 800 W / %96 bütçeden → `⚠️` |
| 4 | Giriş sigorta | I²t eğrisi | pre-charge devresiyle |

### D3. Tedarik notları
- Fiyat/stok **alıntı tarihi** ile ve TTL ≤ 24 s (K20).
- Her kritik MPN ≥1 alternatif; bulunamayan → `⚠️ VERIFICATION REQUIRED`,
  tahmini "stok var" yazılmaz.
- Tedarikçi part no. MPN değildir (K20, sütun ayrımı).

---

## E. Çıktı özeti (raporlama formatı)

```
Girdi: 1 kanal Class AB, 8 Ω, ±42 V, LM5122×2 besleme, 4 katman PCB
Hesap : P=50 W/8 Ω (V_pk 20 V) · P_diss=41 W/kanal · 328 W/8 kanal · ΔT 17-51 °C (C1)
Kaynak: onsemi MJL21193/94 (S1,S2) · TI LM5122 (S6) · IPC-2221 (S8)
Çelişki: C1 (ısıtkıcı), C3 (KSD301 trip) → BOM kilidi yok, VERIFICATION REQUIRED
Ölçüm: THD+N / SNR / gain / bit-perfect → 3 koşul, ihlal 0 (§5)
BOM  : MPN zorunlu, çelişkili satırlar bayraklı, fiyat+TTL
Karar : [GEÇTİ / ENGELLENDİ — ölçüm tablosu doldurulmadan "GEÇTİ" yazılmaz]
```

**Bağlantılar:** `sources.md` · `power-thermal-calcs.md` · `standard-circuits.md` ·
`pcb-bom-rules.md` · `troubleshooting.md` · `datasheet-tips.md`
