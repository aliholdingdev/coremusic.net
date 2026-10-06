---
title: "CoreMusic — Class AB Amplifikatör & Güç Kaynağı (Devre Seviyesi)"
type: architecture-spec
category: architecture
date: 2026-10-06
updated: 2026-10-06
status: active
version: 1.0.0
authority: "SSOT — K16 (Class AB) + K17 (Güç) devre tasarımı"
governance: Red Team · Human Mode · Truth Mode
---

# Class AB Amplifikatör & Güç Kaynağı — Devre Seviyesi

> **Zorunlu gereksinim:** Class AB (**Class D YASAK**) · boost converter tabanlı ·
> **12V normal / 24V maksimum** · DC **12-24V** giriş aralığı · pil + adapter uyumlu ·
> temiz ses kalitesi (düşük gürültü, düşük bozulma).

---

## §1 Sistem Şeması (Güç Yolu)

```
 [Pil 12V] ─┐
            ├─► [Giriş koruma] ─► [Boost Converter] ─► [+24V rail] ─► [Class AB çıkış]
 [Adapter] ─┘      (seçim/öncelik      (K17)              (maks)         (K16)
                    + sigorta +                             │
                    ters polarite)                    [−24V/0V referans]
                                                          │
                                                      [Termal koruma K18]
                                                          │
                                                      [Hoparlör / kulaklık]
```

| Mod | Rail | Kaynak |
|-----|------|--------|
| Normal çalışma | **12V** | Pil/adapter doğrudan |
| Maksimum güç | **24V** | Boost converter çıkışı |
| Giriş aralığı | **12-24V DC** | Kullanıcı gereksinimi |

---

## §2 Boost Converter Tasarımı (K17)

### §2.1 Örnek tasarım zinciri (TI SNVA824 / LM5155)

| Adım | Parametre | Örnek Değer |
|------|-----------|-------------|
| 1 | Girdi | 6V-18V (TI örneği) |
| 2 | Çıkış | 24V / 2A |
| 3 | Anahtarlama frekansı | **440 kHz** (AM 530k-1.8M dışında) |
| 4 | Tahmini verim η | %90 |
| 5 | Ripple oranı RR | %60 (30-70 aralığında) |
| 6 | İndüktör LM | **6.8 µH** (standart değer) |
| 7 | Akım sınırlama payı | **%20** |
| 8 | Sense direnci RS | **8 mΩ** |
| 9 | Slope compensation RSL | 0 Ω (yeterli iç kompanzasyon) |
| 10 | Çıkış kap. ripple hedefi | 100 mV |
| 11 | Giriş kapasitesi | 100 µF (düşük ESR seramik) |
| 12 | RFBT | 47 kΩ |
| 13 | Kompensasyon | Type II · sıfır ≈ **682 Hz** |

### §2.2 Kayb bileşenleri (toplam kayıp modeli)

```
P_TOTAL = P_IC (P_G + P_IQ) + P_MOSFET + P_DİYOT + P_L + P_RS
```

| Kayıp | Kaynak |
|-------|--------|
| Kapı sürme kaybı `P_G` | Anahtarlama frekansı × Qg × Vgs |
| Boşta akım kaybı `P_IQ` | Kontrol IC quiescent |
| MOSFET kaybı `P` | `I²R_DS(on)` + anahtarlama geçişleri |
| Diyot kaybı `P_D` | İleri gerilim × ortalama akım |
| İndüktör kaybı `P_L` | `I²R_DC` + çekirdek kaybı |
| Sense direnç kaybı `P_RS` | `I²R_S` |

### §2.3 CoreMusic'e özel hesap `[VERIFY REQUIRED]`

> Aşağıdaki değerler **henüz hesaplanmamıştır** — sistem gücü bilinmiyor
> (kaç watt hoparlör/kulaklık? kaç kanal?).
>
> | Henüz belirsiz | Ne zaman hesaplanır |
> |----------------|---------------------|
> | Hedef çıkış gücü `P_out` | Hoparlör empedansı + istenen SPL |
> | `L`, `C_in`, `C_out` nihai | `P_out` ve `V_in(min)` sonrası |
> | MOSFET/indyüktör akım ratingi | `I_peak` hesabından |
> | Termal kasa hesabı | K18 ile birlikte |

---

## §3 Class AB Çıkış Kademesi (K16)

### §3.1 Topoloji

```
        V+ (12/24V)
         │
     ┌───┴───┐
     │ Q12   │  sürücü (emiter takipçi)
     │(NPN)  │
     └───┬───┘
   R8 ───┤        R8 = 33Ω → 21mA (0.7/33)
         ├────────────► ÇIKIŞ (hoparlör)
   R10 ──┤        R10 = emiter direnci (termal geri besleme)
     ┌───┴───┐
     │ Q14   │  çıkış transistörü
     │(NPN)  │
     └───┬───┘
        GND/V−

  Bias: Vbe multiplier (Q11) + ayarlanabilir R7 — Q11 AYNI soğutucuda
```

### §3.2 Bias hesap kuralları (kanıt kaynaklı)

| Kural | Formül | Örnek |
|-------|--------|-------|
| Optimum bias | `V_Re ≈ 26 mV` | — |
| Quiescent akım | `Ic = 26mV / Re` | Re = 0.33 Ω → **79 mA** |
| Sürücü akımı | `0.7 / R_sürücü` | 33 Ω → 21 mA |
| Crossover minimum | `Re = re(idle)` | `re = 26mV / Ic` |
| Zayıf sinyal çıkış empedansı | ≈0.3 Ω (8Ω, 100mA) | — |
| Darlington bias ihtiyacı | 4 × Vbe ≈ **2.6V** | QEL 3048 (4×0.65V) |
| QEL gerçek ölçüm | LED 1.93V + pot 0.12V ≈ bias | pot 22Ω, Ic 5.44mA |

### §3.3 Termal stabilite (K18 ile ortak)

1. **Vbe multiplier** sıcaklık katsayısı, çıkış + sürücü transistörlerinin Vbe'si ile **eşleşmeli**.
2. Q11 **aynı soğutucuya** monte edilir (çıkış transistörleri en çok ısınır).
3. Bias ayarı: 1 kHz sine girdisiyle crossover bozulması yok olana kadar `R7` ayarlanır.
4. Aşırı bias → gereksiz quiescent akım → **ısınma** → termal koruma (K18).

### §3.4 Sınıf davranışı — neden Class AB?

| Sınıf | Durum | Karar |
|-------|-------|-------|
| A | Sürekli iletim → yüksek quiescent kayıp | ❌ verimsiz |
| B | Push-pull → **crossover distorsiyonu** | ❌ ses kalitesi |
| **AB** | Hafif bias (>%50 <%100 iletim) | ✅ **zorunlu** |
| D | Anahtarlama → yüksek verim | ❌ **YASAK** (kullanıcı gereksinimi) |

> ⚠️ **gm-doubling notu (D. Self):** Class-A bölgesinde iki cihazın eşzamanlı iletimi
> `gm` iki katına çıkarır → orta bölgede bozulma artar. Bias "çok yüksek" yapılmamalıdır.

---

## §4 Güç Modu Karşılaştırması

| Mod | Rail | Örnek çıkış gücü (8Ω, QEL verisi) |
|-----|------|-----------------------------------|
| Normal | 12V | **684 mW** |
| Normal+ | 15V | 1.22 W |
| Maksimum | 18V | 1.68 W |
| **Maksimum (boost)** | **24V** | `≈ 16.8W × (24/18)²` → **`[VERIFY REQUIRED]`** |

> QEL ölçümü 18V'a kadar doğrudan ölçülmüştür; **24V değeri ölçekleme hesabıdır,
> ölçülmüş değildir** → `⚠️ VERIFICATION REQUIRED`.

**Güç yasası (doğrulanmış):** `P ∝ Vrms²` → **Vcc iki katlanırsa güç dört katlanır.**
12V → 24V geçişi teorik olarak **~4×** güç artışı sağlar.

---

## §5 Koruma Devreleri (Zorunlu)

| Koruma | Uygulama | Katman |
|--------|----------|--------|
| Ters polarite | Serbest diyot / P-MOSFET | K17 |
| Aşırı akım | Sense direnci + akım limiti (%20 pay) | K17 |
| Aşırı sıcaklık | Termistör + Q11 izleme → kısma/kapama | K18 |
| Kısaltma | Hızlı sigorta + akım limiti | K17 |
| Aşırı voltaj | 24V clamp / TVS | K17 |
| Pop/noise | Giriş kademeli açılış (soft-start) | K16 |

---

## §6 Doğrulanmış Kaynaklar

| # | Kaynak | Kullanılan |
|---|--------|------------|
| 1 | TI **SNVA824** — LM5155 Design Example | §2 tabloları |
| 2 | leungengineering.com — *The Output Stage* (ngspice) | §3.1-§3.3 |
| 3 | QEL Kit 3048 — *Introduction to Power Audio Amplifiers* | §3.2, §4 |
| 4 | D. Self — *Audio Power Amplifier Design Handbook* (ISBN 0 7506 56360) | §3.4 gm-doubling |
| 5 | allaboutcircuits — Biasing Techniques (BJT) | sınıf tanımları |
| 6 | Energies 2017, 10, 1128 — *Combined Boost Converter* (USM) | boost topoloji alternatifi |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-06
