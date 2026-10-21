# power-thermal-calcs — Guca / Termale Hesap Protokolu (K17 + K18)

> Kaynaklar: `.ai/PROJECTS.md` §7.2.2 (guc) · §7.2.3 (termal) · `.ai/CLAUDE.md` §5 K17/K18 ·
> `ADR-089-classab-24v.md` · `ADR-090-channel-variant-product-family.md` ·
> dis kaynaklar: `references/sources.md` S4 (LM5122), S5-S8 (Fischer), S9 (IPC-2221).
> Zero Hallucination: formuller genel muhendislik kanunudur (Thevenin/enerji dengesi — kaynak gerekmez);
> **sayisal degerler** yalniz vault veya S-kaynakli tablodan gelir; kaynaksiz rakam `⚠️ VERIFICATION REQUIRED`.

## §1 Guclu Sinyal Amfisi — Anlik Tuketim ve Isil Yuk (K16 icin temel)

Simplifiye Class AB (push-pull, ±Vs sırt, RL yuk, ihmal edilebilir akim):

| Buyukluk | Formul | Not |
|----------|--------|-----|
| Tepe gerilimi | `Vp` (Vrms = Vp/√2) | Sinus varsayimi |
| Cikis gucu | `Pout = Vp² / (2·RL)` | Klips onu |
| Sirt besleme gucu | `Pin = 2·Vs·Vp / (π·RL)` | Her rail yarım-sine akim verir |
| Cikisteki toplam dagilim | `Pd = Pin − Pout` | Class AB klasik sonuc |
| **Maks dagilim noktasi** | `Vp = 2·Vs/π` (≈ 0.637·Vs) | `dPd/dVp = 0` |
| **Maks toplam dagilim** | `Pd_max = 2·Vs² / (π²·RL)` ≈ 0.203·Vs²/RL | Her iki cikis ciftinde toplam |
| O daki cikis gucu | `Pout = Pd_max` (esit) | Klasik "maks isil yuk = 1/2 guc" kurali |

**K16 uygulamasi (±35 V, 8 Ω, vault 50 W):**

| Adim | Hesap | Sonuc |
|------|-------|-------|
| 50 W icin gereken Vrms | √(50·8) | 20 Vrms · 28.3 Vpe |
| Klips bas | ±35 − 28.3 | ≈ 6.7 V (Darlington Vce_sat + surucu icin ayni anda yeterli pay — daraltma yapilmaz) |
| Maks dagilim noktasi Vp | 2·35/π | ≈ 22.3 Vpe → Pout = 22.3²/16 ≈ **31 W** |
| Maks dagilim (toplam cift) | 2·35²/(π²·8) | ≈ **31 W** (ciftte toplam; cikis cifti basina ≈ 15.5 W) |
| 50 W surekli klips hedefinde dagilim | 2·35·28.3/(π·8) − 50 | ≈ 78.8 − 50 = **28.8 W** (ciftte toplam) |

- Vault **41 W/kanal** isi yonetimi (K18) bu degerlerin uzerine surucu asamalari + guvenlik payi icerir;
  skill bunu **butce** olarak kullanir, yeniden hesaplamaz.
- **4 Ω yuk** (LFE): ayni Vp'de Pout iki kat, dagilim da buyur. K16 spec'i 8 Ω uzerindendir;
  4 Ω destegi vault'ta tanimsiz → `⚠️ VERIFICATION REQUIRED` (LFE kanali icin ayrica ADR gerekir).
- **Akim tepe degeri:** `Ip = Vp/RL` → 50 W/8 Ω'da 3.54 A; cikis transistoru I_C (max) 16 A (S2) ile
  akim siniri sorun degil; **sinir SOA'dir** (§2).

## §2 SOA ve Derating (MJL21194/93 — S1, S2)

- S1: SOA iki sinirla tanimlanir — **ort junction sikligi** ve **secondary breakdown**; egriler
  **TJ(pk) = 150°C** icin verilir. Tasarim TJ hedefi bu degil, **derating** ile asagida tutulur.
- K16 icin tipik SOA noktasi (klips onu, 8 Ω): `Vce ≈ 2·Vs = 70 V`, `Ic ≈ 3.5 A` → (70 V, 3.5 A) noktasinin
  DC/10 ms/1 s SOA egrisinin altinda oldugu **datasheet egrisinden okunarak** dogrulanir (S1 Fig. 13 —
  grafik okumasi gereken bir degerdir → uretim dosyasina olcum/not olarak girer).
- **Derating kurali (bu skill):** uretim oncesi kabulde `TJ ≤ 110°C` hedeflenir (150°C mutlak sinirin
  ~%73'u; 25°C ambient + 85°C sink hedefi ile uyumlu — K18 `<85°C`).
- **Induktif yuk / hoparlör kablo acilmasi:** clamping agi (Zobel + flyback diyot/TVS) olmadan Vce anlık
  `2·Vs` uzerine tasinabilir → SOA disi kalir. **Zobel zorunlu** (`standard-circuits.md` §4).
- Eski/yipranik/ikinci el transistor **kullanilmaz**; `G` (RoHS) sürümü tercih edilir (S2/S3 MJL21194G).

## §3 Termal Zincir (θja → heatsink → junction)

```
TJ = TA + Pd · (θJC + θCS + θSA)
```

| Buyukluk | Anlam | K16-K18 kaynakli deger |
|----------|-------|-------------------------|
| θJC | junction → case | MJL21194 TO-264 icin datasheet degeri `⚠️ VERIFICATION REQUIRED` (S1 PDF'ten tablo satiri tasinmadi) |
| θCS | case → sink (termal ped/maca + tork) | Ped secimine bagli; tipik 0.2–0.5 K/W bandi **tasarim karari** — vault'ta yok → `⚠️ VERIFICATION REQUIRED` |
| θSA | sink → ambient | K18: toplam `<0.8 °C/W`, Fischer SK53-100-SA vault `0.42 °C/W` — **catiski C1**: dis kaynaklar 0.6–1.25 K/W diyor (`sources.md` §3 C1) |
| TA | ortam | 25°C masaustu kabul; kapali kasa icin 35°C'ye kadar kres → worst-case TA ile hesap |
| TJ_hedef | kabul siniri | ≤ 110°C (derating) · 150°C mutlak (S1/S3) |

**Calisma ornegi (vault degerleriyle, 8 Ω 50 W surekli, cift-basina 15.5 W):**

| Adim | Hesap | Sonuc |
|------|-------|-------|
| Sink uzerinde toplam (kanal) | 2 × 15.5 W (cikis) + surucu payi (vault 41 W butce icinde) | kanal butcesi 41 W (K18) |
| Sink artisi (vault 0.42 K/W ile) | 41 × 0.42 | ≈ 17.2°C → TA 25'te sink ≈ 42°C |
| Sink artisi (dis kaynak 0.65 K/W ile — C1 gercekci senaryo) | 41 × 0.65 | ≈ 26.7°C → sink ≈ 52°C |
| 8 kanal butcesi (AED-089 §4.2) | 8 × 41 W | **328 W** — tek sink'e 328 W gomulu katta `0.65·328 ≈ 213°C` IMKANSIZ → **modüler kanal sink'i dogrular** (her kanal kendi sink dilimi + kendi hava akisi) |

**Sonuc (karar tablosu):** 328 W sistem isi, **tek ortak sink + dogal konveksiyon ile tasnif edilemez**;
vault mimarisi (modüler kanal PCB + Noctua NF-A8 zorlu hava) bu yuzden sarttir. Fan'li θSA icin
uretim dosyasinda **olcumle** (termokupl + guc sarfi) teyit gerekir → `⚠️ VERIFICATION REQUIRED`.

## §4 Fan / PWM Profili (K18 — vault degerleri)

| Sicaklik | Davranis | Kaynak |
|----------|----------|--------|
| <30°C | sessiz (fan minimal/devre disi) | PROJECTS §7.2.3 |
| 30–70°C | lineer artis | PROJECTS §7.2.3 |
| >70°C | tam hiz | PROJECTS §7.2.3 |

- Sıralama degistirilemez; tam hiz esigi 70°C uzerine cekilemez (K18 `<85°C` hedefiyle catisir → DUR + sor).
- KSD301 **97°C kesme** son savunmadir (PROJECTS §7.2.3); ADR-089'da 72°C gecer — **catiski C3**,
  `references/sources.md` §3. Iki deger birlikte anilir; uretim oncesi sahibi sabitler.

## §5 Guc Kaynagi Hesaplari (K17 — LM5122, S4)

Vault mimarisi: **LM5122 ×2 (boost + inverting) · ±35 V simetrik · 800 W · %96 verim ·
6S LiPo 22.2 V veya 19–24 V DC · 2200 µF ×4 low-ESR · 33 µH ×2 toroid** (PROJECTS §7.2.2).

| Buyukluk | Formul | K16-K17 uygulamasi |
|----------|--------|---------------------|
| Giris akimi | `Iin = Pout / (η · Vin)` | 800/(0.96·22.2) ≈ **37.5 A** (6S nominal); 19 V adaptorde: 800/(0.96·19) ≈ **43.9 A** → **worst-case giris akimi budur**, sigorta/kablo/bobin secimi buna gore |
| Boost duty | `D = 1 − Vin/Vout` (tek asama) | 35 V bar: 22.2 V'da D ≈ 0.366; 19 V'ta D ≈ 0.457 |
| LM5122 sinir kontrolü | Vin_max **65 V**, Vout **≤100 V** (S4) | ±35 V (toplam 70 V span) ve 24 V giris → sinir icinde |
| Simetri | `|+35| − |−35| ≤ %5` (hardware-template §3.7 T1) | ±1.75 V pay |
| Koruma zinciri | UVP/OVP/OCP/OTP **dortu de etkin** (CLAUDE K17 · ADR-089 §3.3.3) | LM5122 hiccup-OCP + programlanabilir UVLO (S4) **ek koruma devreleriyle** birlikte; tek basina yetmez sayilmaz |
| Ripple/ses riski | Boost PWM ±35 V barina sizar → SNR duser | Ripple Eater / aktif filtre degerlendirmesi ADR-089 §4.2'de acik |

**Hold-up / bulk (2200 µF ×4 = 8.8 mF):**

| Adim | Hesap | Sonuc |
|------|-------|-------|
| 22.2 V → 19 V'a bosalma (adaptor min) enerjisi | `½·C·(V1²−V2²)` = ½·0.0088·(492.8−361) | ≈ **0.58 J** |
| Bu enerjinin 800 W'a dayanma suresi | 0.58/800 | ≈ **0.72 ms** → boost hiccup/UVLO suresiyle karsilastirilir (S4 UVLO programlanabilir); battery (6S) tarafinda bu sure anlamsizdir |

## §6 Sigorta / Crowbar / Kablo Kesiti

| Kalem | Kural | Hesap |
|-------|-------|-------|
| Sigorta (giris) | Yavas yanma (T), surekli akimin **≥1.5 kat** nominal; kirma kapasitesi > besleyici kisa devre akimi | 43.9 A worst-case → ≥65 A nominal; **per-kanal sigorta** daha iyidir (tek kanal arizasi sistemi indirmez — ADR-089 §3.1-A4 ruhu) |
| Crowbar | Rail >OVP esiginde SCR + sigorta; OVP kisa devre disinda **devre disi birakilamaz** | SCR tetik esigi: ±35 V bar icin ~±39–40 V (transformator/regulator hata payina gore) — **tasarim karari**, vault'ta yok → `⚠️ VERIFICATION REQUIRED` |
| PCB iz kesiti (IPC-2221, S9) | `I = k·(ΔT)^0.44·(A)^0.725` · k=0.048 dis / 0.024 ic · A mil² | 1 oz dis katman, ΔT 10°C: 5 A → ≈109 mil (2.77 mm); 37.5 A tek izde ≈ **imkansiz** → **bakir bolge/guc plakasi + via demeti** kullanilir (K19 2 oz + thermal via) |
| Kablo kesiti (DC giris, 44 A) | Tipik: 4–6 A/mm² (uzun sureli, kapali kasa) | ≈ **8–11 mm²** (8 AWG sınıfı) → kesin kesit ortam sicakligi + kablo boyuna gore son halini alır; `⚠️ VERIFICATION REQUIRED` (kablo boyu bilinmiyor) |

## §7 Isil Hesap Kontrol Listesi (her revizyonda)

- [ ] `Pd_max = 2·Vs²/(π²·RL)` yeniden hesaplandi (RL worst-case: 8 Ω + varsa 4 Ω)
- [ ] TJ = TA + Pd·(θJC+θCS+θSA) ≤ 110°C (derating) — TA worst-case (kapali kasa) ile
- [ ] θSA **olcusu kesinlesmis** heatsink icin (C1 catiskisi kapanmis mi?)
- [ ] SOA noktasi (Vce≈2·Vs, Ip) datasheet egrisinin altinda (S1)
- [ ] Zobel + clamping mevcut (induktif yuk acilmasi)
- [ ] Giris akimi (19 V worst-case) icin sigorta/kablo/bobin ≥ 1.5x pay ile
- [ ] Fan PWM profili siralama korunmus; KSD301 kesme bagli ve etkin
- [ ] 8-kanal toplam isi (328 W) hava akisi/montaj ile dogrulanmis (tek sink yok)
- [ ] Crowbar/OVP tetik esigi ve sigorta kirma kapasitesi dokumante
- [ ] Her `⚠️ VERIFICATION REQUIRED` maddesi uretim oncesi kapatilmis veya sahibine devredilmis

*CoreMusic hardware-electronics · references/power-thermal-calcs.md · Updated: 2026-10-21*
