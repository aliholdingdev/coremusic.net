# standard-circuits — K16 Class AB Kanal Cekirdek Devreleri + Vault Kisitlari

> Kaynaklar: `.ai/CLAUDE.md` §5 K16/K19 (+ H1) · `.ai/PROJECTS.md` §7.2.1 ·
> `ADR-089-classab-24v.md` · `ADR-090-channel-variant-product-family.md` ·
> `.ai/.templates/hardware/hardware-template.md` (Guardrail #16) ·
> dis kaynak: `references/sources.md` S1-S3 (MJL21193/93).
> Formul/hesap satirlari muhendislik kanunudur; sayisal vault disi deger = `⚠️ VERIFICATION REQUIRED`.

## §1 Vault Kisi Tablosu (degistirilemez — SSOT)

| Kural | Deger | Kaynak |
|-------|-------|--------|
| Tek gecerli topoloji | **Class AB Darlington** cikis | ADR-089 §2.1 |
| Cikis cifti | **MJL21194 (NPN) / MJL21193 (PNP)** · TO-264 · 250 V / 16 A / 200 W / f_T 4 MHz | PROJECTS §7.2.1 · S2/S3 |
| Guç | **50 W/kanal @ 8 Ω** (aralik 25–75 W) | PROJECTS §7.2.1 · ADR-089 §2.2 |
| THD | **<0.005 % @ 1 W** | PROJECTS §7.2.1 |
| SNR | **>105 dB** (min kabul esigi; 120 dB+ DR hedefi olcum tanimi acik → `⚠️ VERIFICATION REQUIRED`) | PROJECTS L305 · ADR-089 §4.3 |
| Besleme | **±35 V simetrik DC** (K17'den; AC/sebeke reddedildi — ADR-089 §3.1-A7) | ADR-089 |
| Moduler kanal | **1–8 kanal; her kanal bagimsiz PCB + enable pinli** | CLAUDE K16 · ADR-089 §3.1-A4 |
| DC offset | **>0.5 V DC → koruma rölesi zorunlu** | `.ai/CLAUDE.md` §23/7 |
| **Class D (TPA3255 vb.)** | ❌ **YASAK** ana topoloji (H001) | ADR-089 §3.1-A1 |
| Class DC / AC sebeke | ❌ Reddedildi | ADR-089 §3.1-A2/A7 |
| Sinyal paraziti | ❌ — **star ground sart** (K19) | CLAUDE K1 · ADR-089 §3.3.4 |

⚠️ **Guclu catiskisi (C4):** `architecture/k16-class-ab/8-channel-design.md` "100W/kanal" der —
**SSOT = ADR-089 (50 W)**; 100W satiri duzeltme bekliyor. Yeni dokumanda **100 W yazılmaz**.

## §2 Kanal Zinciri — Referans Mimari (ADR-089 §2.4-S3)

```
Diferansiyel giris → VAS (voltage amp.) → Vbe mult (bias) → MJL21194/93 cikis
        ↑                    ↑                              ↑
       NFB  ←────────────────┴── DC offset / overcurrent / termal koruma
                                                 │
MCU ←── enable / telemetri / koruma hatlari (her kanal bagimsiz)     Röle → hoparlor
```

Her asamanin kanal PCB'si uzerinde bagimsiz olmasi ve enable'in MCU'ya bagli olmasi zorunludur
(ADR-089 §2.1). Tek kanal arizasi sistemi indirmez.

## §3 Bias (Vbe Carpim) — Isil Kompanzasyon

| Kalem | Formul / Kural | Not |
|-------|----------------|-----|
| Bias voltaji | `V_bias ≈ 2·V_BE + I_CQ·R_EE` (cikis asamasinda iki Vbe + emitter direnci drop) | Darlington iki Vbe zinciri demektir |
| Vbe carpim | `V_CE(mult) = V_BE0·(1 + R1/R2)` | R1/R2 oranina gore ayarlanir |
| Isil kompanzasyon | Vbe carpim transistoru **ayni heatsink'e/termal pede** monte | Sicakken bias azalir → `I_CQ` sicaklik arttikca geri besleme |
| I_CQ hedefi | Vault'ta tanimsiz → `⚠️ VERIFICATION REQUIRED` | Sessizlik (crossover) ile isil yuk arasinda secim; olcumle sabitlenir |
| Termal kacis kosulu | `dV_BE/dT ≈ −2 mV/°C` (silikon) · kompanzasyon `R_th(sink→mult)` yoluyla | Ped yoksa/uzaksa → **termal kacis** (troubleshooting §1) |

**H001 notu:** bias ayari "daha az isinsin" diye devre disi birakilamaz — crossover bozulmasi
THD'yi vault esiginin (%0.005) uzerine cikarir. I_CQ, THD + TJ olcumuyle birlikte sabitlenir.

## §4 Cikis Asamasi + Koruma Bloklari

| Blok | Islev | Kural / Formul |
|------|-------|----------------|
| **Cikis cifti** | MJL21194 (NPN, +35 V taraf) + MJL21193 (PNP, −35 V taraf) | Her cikis cifti icin heatsink dilimi + thermal via (K19) |
| **Zobel agi** | R + C seri, cikist → toprak; inductif yuk (hoparlor kablosu) sinirlar | `R_Zobel ≈ R_L` (8 Ω sınıfı), `C_Zobel` 100 nF sınıfı — **secim olcumle** `⚠️ VERIFICATION REQUIRED` |
| **NFB** | Geri besleme orani kazanci belirler | `Av ≈ 1 + Rf/Rg`; THD/SNR hedefine gore secim |
| **DC offset monitor** | Cikis DC'sini izler | **>0.5 V → röle ac** (`.ai/CLAUDE.md` §23/7 — röleyi cikaran revizyon RED) |
| **Overcurrent** | Emiter direnci uzerinden akim tasi | Sınır **tasarim degeri** → `⚠️ VERIFICATION REQUIRED` (vault'ta yok); LM5122 cycle-by-cycle sinir (S4) guc kaynagini korur |
| **Termal koruma** | KSD301 **97°C kesme** (PROJECTS §7.2.3; ADR-089'da 72°C — catiski C3) | Son savunma; devre disi birakilamaz |
| **Koruma rölesi** | Cikis → hoparlor terminali; DC/overcurrent/termal tetikler | Silent start: acilista röle **gec kapanir** (sistem oturuncaya kadar) — K16-K20 butunu |
| **Enable (MCU)** | Kanal kapatma/yonetim | ADR-089 §2.1; koruma tetiklenince MCU uzerinden kanal kapanir |

## §5 Modüler Kanal Kurallari (ADR-089/090 — H001 kapsamli)

1. **1–8 kanal; her kanal bagimsiz PCB + enable pinli.**
2. **Monolitik tek PCB uzerinde cok kanal = RED** — 41 W/kanal isi yonetimi (K18) + ariza izolasyonu
   bagimsiz PCB ile saglanir; tek kanal arizasi sistemi indirmemeli (ADR-089 §3.1-A4).
3. **Varyant kombinasyonlari (ADR-090 §2.2-b):** 1/2/4/6/8 listesi + matriste **5 = 4+1**, **7 = 6+1**;
   **2+1 / 7+1 / 8+1 sub kanallari** ayni Class AB moduludur (LFE icin ayri "subwoofer amfi" topolojisi YOK).
4. Her varyant **ayni ortak cekirdek semayi** paylasir; varyant basina ayri tasarim YOK (ADR-090 §2.2-a).
5. Cikis transistorleri/topoloji varyantlarda **degismez** (ADR-089 hizasi zorunlu).

## §6 Topoloji Degisikligi Kapisi

- Class AB → baska topoloji (Class D dahil) **yeni ADR** + Human Approval Gate (Guardrail #14) ister.
- Class D/TPA3255 "referans senaryo" ifadeleri ADR-089 ile gecersizdir — entegre edilmez, alternatif
  olarak **onerilemez** (H001). Isin/verim sorunu K18 icerisinde cozulur (fan PWM + hava akisi).

*CoreMusic hardware-electronics · references/standard-circuits.md · Updated: 2026-10-21*
