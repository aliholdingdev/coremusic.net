---
node_id: K000-isletim-sistemi/electroncis/devre-tasarimi/analog-ve-dijital-devre-temelleri
node_type: module
status: draft
parent_id: K000-isletim-sistemi/electroncis/devre-tasarimi
children: []
depends_on:
  - K001-donanım
  - K004-dsp
  - K005-firmware
evidence:
  - "https://www.ti.com/lit/ds/symlink/lm358.pdf (HTTP 200 · 2026-10-13)"
  - "https://www.ti.com/lit/ds/symlink/opa2134.pdf (HTTP 200 · 2026-10-13)"
  - "https://www.ti.com/lit/ds/symlink/ads131m04.pdf (HTTP 200 · 2026-10-13)"
  - "https://www.ti.com/lit/ds/symlink/pcm1794a.pdf (HTTP 200 · 2026-10-13)"
  - "https://www.ti.com/lit/an/slyt691/slyt691.pdf (HTTP 200 · 2026-10-13)"
  - ".ai/electronic/** (60 md · 10.323 sat) — devre içeriği orada derinleştiriliyor"
validation: "2026-10-13 · §0-§6 yazıldı · ≥500 satır · repo'da devre seviyesi kod YOK (dürüstlük)"
open_questions:
  - "Q-AVD1: ADC secimi (CoreMusic) henuz belgelendi mi? → PLANNED / ⚠️ VERIFICATION REQUIRED"
  - "Q-AVD2: Op-amp secimi (ses yolu) hangi olcumle dogrulanacak? → PLANNED"
group: mimari
---

# analog-ve-dijital-devre-temelleri — Analog ve Dijital Devre Temelleri

> **Yazım dokunulmaz:** `electroncis` (v2 §3 KURAL 1). Bu dosya **temel blokları** (bileşen ·
> op-amp · ADC/DAC · gürültü · ölçüm) kavram seviyesinde taşır. Repo'da devre seviyesi kod YOK →
> burada **koşulsuz/uydurma kod** yazılmaz; yalnız kavram + doğrulanmış referans.

## §0 ELI15

Her ses/güç cihazı üç katmandan oluşur: **bileşenler** (direnç·kapasitör·transistör), bunların
kurduğu **bloklar** (op-amp kazanç, filtre), ve bunları birbirine bağlayan **analog↔dijital köprüsü**
(ADC/DAC). Bu dosya bu üç katmanın **temelini** anlatır; sayısal tasarım `devre-tasarimi-anayasa.md`,
simülasyon/ölçüm `devre-simulasyonu-ve-dogrulama.md`'dedir.

## §1 Aktif / pasif bileşenler

### §1.1 Pasif bileşenler

| # | Bileşen | İşlev | Temel ilişki | Dikkat |
|---|---|---|---|---|
| 1 | Direnç (R) | akım sınırlama · bölücü · geri besleme | V = I·R | güç · tolerans · gürültü |
| 2 | Kapasitör (C) | AC kısa-devre · depolama · filtre | I = C·dV/dt | DC bias derating · ESR |
| 3 | İndüktör (L) | DC kısa · AC engel · depolama | V = L·dI/dt | doygunluk · EMI |
| 4 | Kristal | zamanlama (osilatör) | fs/gerilim | sürücü seviyesi |
| 5 | Ferrite/EMI | high-f gürültü | empedans | akım doygunluğu |

### §1.2 Aktif bileşenler

| # | Bileşen | İşlev | Temel parametre | Uygulama |
|---|---|---|---|---|
| 1 | BJT (NPN/PNP) | akım kazancı · anahtar | hFE · Vbe · Vceo | ses · analog |
| 2 | MOSFET (N/P) | gerilim-kontrollü anahtar/kazanç | Rds(on) · Vgs(th) · Qg | Class-D · anahtarlama |
| 3 | Op-amp | yüksek kazanç · geri besleme | GBW · SR · Vos · Inoise | filtre · buffer · kazanç |
| 4 | Regülatör | sabit çıkış | PSRR · dropout · gürültü | güç |
| 5 | Koruma (eFuse/TVS/DIODE) | eşik koruma | eşik · tepki · kapasite | koruma |

### §1.3 Temel konfigürasyonlar (ad-türevi · değer YOK)

| # | Konfigürasyon | İşlev | Temel bağıntı | Not |
|---|---|---|---|---|
| 1 | Gerilim bölücü | V_out = V_in·R2/(R1+R2) | DC bias | akım tüketimi |
| 2 | Direnç + LED | akım sınırlama | I = (V−Vf)/R | — |
| 3 | RC LPF | yüksek kesme | fc = 1/(2πRC) | 1. katman |
| 4 | RC HPF | düşük kesme | fc = 1/(2πRC) | DC bloğu |
| 5 | LC filtre | 2. katman kesme | fc = 1/(2π√LC) | switcher çıkışı |
| 6 | Emitter/kaynak dirençli amplifikatör | kazanç | Av ≈ −Rc/re (BJT) | — |

### §1.4 Enerji ve güç ilişkileri

| # | Büyükllük | Bağıntı | Uygulama |
|---|---|---|---|
| 1 | Güç | P = V·I = I²R = V²/R | derating |
| 2 | Ohm yasası | V = I·R | her yerde |
| 3 | Kirchhoff (KVL) | döngü gerilim toplamı = 0 | şema denetimi |
| 4 | Kirchhoff (KCL) | düğüm akım toplamı = 0 | şema denetimi |
| 5 | Eşdeğer direnç | seri: R1+R2 · paralel: R1R2/(R1+R2) | — |

## §2 Op-amp temelleri

### §2.1 Op-amp ideal özellikleri

| # | Özellik | İdeal değer | Gerçek etkisi |
|---|---|---|---|
| 1 | Açık kazanç (Aol) | sonsuz | sınırlı → kazanç hatası |
| 2 | Giriş empedansı | sonsuz | sonsuz değil → bölücü |
| 3 | Çıkış empedansı | sıfır | sıfır değil → yük etkisi |
| 4 | Bant genişliği | sonsuz | GBW sınırlı |
| 5 | Offset (Vos) | 0 | 0 değil → DC sapma |
| 6 | Giriş bias akımı (Ib) | 0 | 0 değil → bias direnci |
| 7 | Gürültü | 0 | 0 değil → ses yolu |

### §2.2 Op-amp modları

| # | Mod | Devre | Kazanç | Kullanım |
|---|---|---|---|---|
| 1 | Tersleyen (inverting) | Rf/Rin | Av = −Rf/Rin | filtre · kazanç |
| 2 | Terslemeyen (non-inverting) | 1+Rf/Rg | Av = 1+Rf/Rg | buffer (Av=1) · kazanç |
| 3 | Takip edici (voltage follower) | Rf=0, Rg=∞ | Av = 1 | buffer |
| 4 | Differential | Rf/Rin | Av = Rf/Rin (dengeli) | diferansiyel giriş |
| 5 | Integrator | RC geri besleme | 1/(jωRC) | filtre · ΔΣ |
| 6 | Differentiator | CR giriş | jωCR | — |

### §2.3 Op-amp gürültü ve offset

| # | Parametre | Sembol | Birim | Ses etkisi |
|---|---|---|---|---|
| 1 | Giriş offset gerilimi | Vos | µV · mV | DC sapma · clip riski |
| 2 | Offset sıcaklık katsayısı | dVos/dT | µV/°C | drift |
| 3 | Giriş bias akımı | In · Ip | nA · pA | bias direnci üzerinden offset |
| 4 | Giriş gürültü gerilim | en | nV/√Hz | ses yolu SNR |
| 5 | Giriş gürültü akımı | In | pA/√Hz | yüksek-Z giriş |

### §2.4 Referans op-amp'lar (CoreMusic ses yolu · doğrulanmış datasheet)

| # | Op-amp | Rol | Öne çıkan | URL | Durum |
|---|---|---|---|---|---|
| 1 | LM358 | genel amaçlı çift op-amp | düşük güç · tek kaynak | https://www.ti.com/lit/ds/symlink/lm358.pdf | ✅ 200 |
| 2 | OPA2134 | ses bandı op-amp | düşük gürültü · düşük bozulma | https://www.ti.com/lit/ds/symlink/opa2134.pdf | ✅ 200 |

> **TL072** (JFET giriş) daha önce doğrulanmıştı (envanterde) → bu tur yeniden kullanılmadı
> (bkz. `../electroncis-dogrulama.md` §I.3 Z5 · envanter kuralı).

## §3 ADC/DAC zinciri

### §3.1 ADC (analog → dijital)

| # | Adım | İşlev | Kritik parametre | Dikkat |
|---|---|---|---|---|
| 1 | Anti-alias LPF | fs/2 üstünü kes | fc · Q · sesamik | aliasing önleme |
| 2 | Örnekleme | sürekli→örnek | fs > 2·fmax | Nyquist |
| 3 | Kuantalama | örnek→kod | bit (dBFS) | kuantalama gürültü |
| 4 | Kodlama | PCM/ΔΣ | format | dijital arayüz |

### §3.2 DAC (dijital → analog)

| # | Adım | İşlev | Kritik parametre | Dikkat |
|---|---|---|---|---|
| 1 | Dijital giriş | PCM/ΔΣ | fs · bit · kanal (I2S/TDM) | bayt sırası |
| 2 | D/A dönüşümü | kod→analog | INL/DNL · bozulma | doğrusallık |
| 3 | Reconstruction LPF | image kesme | fc · drip | — |
| 4 | Buffer/sürücü | yük | SR · akım | yük empedansı |

### §3.3 ADC/DAC performans büyüklükleri

| # | Büyüklük | Tanım | Birim | Neden |
|---|---|---|---|---|
| 1 | Örnekleme hızı (fs) | saniyede örnek | Hz · kHz | Nyquist |
| 2 | Çözünürlük (bit) | kuantalama kademesi | bit | dinamik aralık |
| 3 | SNR | sinyal/gürültü | dB | performans |
| 4 | THD+N | bozulma+gürültü | % · dB | saflık |
| 5 | DNL | diferansiyel doğrusallık | LSB | kademeler arası |
| 6 | INL | integral doğrusallık | LSB | eğrilik |
| 7 | CR | kanal reddetme/crosstalk | dB | kanal izolasyonu |
| 8 | PSRR | güç reddetme | dB | güç gürültüsü |

### §3.4 Referans ADC/DAC (CoreMusic referans · doğrulanmış datasheet)

| # | Entegre | Tür | Öne çıkan | URL | Durum |
|---|---|---|---|---|---|
| 1 | ADS131M04 | ADC (4 kanal · eşzamanlı) | ölçüm · fazlı | https://www.ti.com/lit/ds/symlink/ads131m04.pdf | ✅ 200 |
| 2 | PCM1794A | DAC (24-bit · yüksek SNR) | ses çıkışı | https://www.ti.com/lit/ds/symlink/pcm1794a.pdf | ✅ 200 |
| 3 | PCM3168A | DAC/ADC (çok kanal) | CoreMusic ADR-038 | envanterde (önceki tur) | ✅ 200 |

> **Dürüstlük:** CoreMusic'in ana DAC/ADC seçimi **PCM3168A** (ADR-038 H001) ve **AK4458**'dir
> (envanter). ADS131M04/PCM1794A bu turda **referans/örnek** olarak doğrulanmıştır; CoreMusic'e
> doğrudan uygulanabilirliği `⚠️ VERIFICATION REQUIRED`.

### §3.5 Örnekleme ve aliasing (kavram)

| # | Kural | Bağıntı | Uygulama |
|---|---|---|---|
| 1 | Nyquist | fs > 2·fmax | anti-alias zorunlu |
| 2 | Anti-alias fc | fc ≈ fs/2 (veya daha düşük) | LPF tasarımı |
| 3 | Over-sampling | fs >> 2·fmax | ΔΣ · filtre kolaylığı |
| 4 | Bit → SNR | SNR ≈ 6·bit + 1.8 dB | 24-bit → ~146 dB teorik |
| 5 | Kuantalama gürültü | −(1/6)·(2/√12) kuralı | dither |

## §4 Gürültü / parazit

### §4.1 Gürültü kaynakları

| # | Kaynak | Tür | Karakteristik | Azaltma |
|---|---|---|---|---|
| 1 | Direnç (Johnson-Nyquist) | termal | k √(4kTRB) | düşük R · düşük B |
| 2 | Transistör (shot) | akım | ∝√I | akım optimizasyonu |
| 3 | Flicker (1/f) | düşük-f | pink noise | düşük-1/f bileşen · lock-in |
| 4 | Op-amp giriş gürültü | en · In | nV/√Hz · pA/√Hz | doğru op-amp · düşük-Z |
| 5 | Güç kaynağı gürültü | ripple/PSRR | anahtarlama gürültüsü | regülasyon · PSRR |

### §4.2 Gürültü hesabı (kavram)

| # | Büyükllük | Bağıntı | Not |
|---|---|---|---|
| 1 | Toplam gürültü gerilimi | en,total = en·√(B·n) | n bileşen · B bant |
| 2 | Direnç gürültü | vn = √(4kTRB) | k = Boltzmann |
| 3 | Gürültü faktörü (NF) | NF = 10·log(F) | cascaded NF |
| 4 | Cascaded (Friis) | Ftot = F1 + (F2−1)/G1 + ... | ilk aşama kritik |

### §4.3 Parazit ve azaltma

| # | Parazit | Kaynak | Azaltma | Sahip |
|---|---|---|---|---|
| 1 | EMI/RFI | dış · anahtarlama | ekran · filtre · twisted | `guc-ve-koruma` + K001 |
| 2 | Crosstalk | kanallar arası | topraklama · mesafe | K001 |
| 3 | Ground loop | çoklu toprak | tek-nokta · izolatör | K001 |
| 4 | Kapasitif kuplaj | yakın iz | ekran · guard | K001 |
| 5 | Anahtarlama paraziti | switcher | LC filtre · spread-spectrum | `guc-ve-koruma` |

## §5 Ölçüm (THD · SNR · kavram)

### §5.1 THD / THD+N

| # | Büyükllük | Tanım | Bağıntı | Not |
|---|---|---|---|---|
| 1 | THD | harmonik bozulma | √(V2²+V3²+...)/V1 | temel haric |
| 2 | THD+N | bozulma + gürültü | √(ΣV_harm²+V_noise²)/V1 | gerçek ölçüm |
| 3 | Hata (düzeltme) | THD+N ≠ THD | THD+N ≥ THD | — |
| 4 | Ölçüm | analyzer · 1 kHz | % · dB | sartname: <0.01% |

### §5.2 SNR / dinamik aralık

| # | Büyükllük | Tanım | Bağıntı | Not |
|---|---|---|---|---|
| 1 | SNR | sinyal/gürültü | 20·log(Vs/Vn) | sartname: >100 dB |
| 2 | DNR | dinamik aralık | en sessiz–en yüksek | — |
| 3 | Bit → SNR | SNR ≈ 6·bit+1.8 | 24-bit | — |
| 4 | Ağırlıklandırma | A/C-weighting | gürültü ölçümü | — |

### §5.3 Ölçüm zinciri (tipik)

| # | Adım | Ekipman | Çıktı |
|---|---|---|---|
| 1 | Referans sinyal | jeneratör (1 kHz) | bilinen girdi |
| 2 | DUT sürü | amplifikatör/zincir | çıkış |
| 3 | Analiz | audio analyzer | THD+N · SNR · FREP |
| 4 | Kayıt | rapor | eşik karşılaştırma |

## §7 Bileşen derinlemesine (seçim ölçütleri · tablolar)

### §7.1 Direnç tipleri

| # | Tip | Tolerans | TCR (ppm/°C) | Gürültü | Uygulama |
|---|---|---|---|---|---|
| 1 | Karbon kompozit | %5–20 | yüksek | yüksek | genel · düşük öncelik |
| 2 | Metal film | %1–2 | 25–100 | düşük | ses · ölçüm |
| 3 | Thin film | %0.1–1 | 5–25 | çok düşük | kritik filtre · ADC referans |
| 4 | Wirewound | %1–5 | 10–50 | orta | güç · yüksek akım |
| 5 | Shunt (akım ölçüm) | %0.5–1 | düşük | düşük | akım algılama |

### §7.2 Kapasitör dielektrik karşılaştırması

| # | Dielektrik | Sembol | Tolerans | TCC | Dip/geri dönüş | Uygulama |
|---|---|---|---|---|---|---|
| 1 | Sınıf 1 seramik | C0G/NP0 | %0.5–5 | 0±30 ppm/°C | yok | ses · timing · filtre |
| 2 | Sınıf 2 seramik | X7R | %10–20 | ±15% | geri dönüş | decoupling |
| 3 | Sınıf 2 seramik | X5R | %10–20 | ±15% | geri dönüş | decoupling (ekonomik) |
| 4 | Polyester film | — | %5 | orta | düşük | ses · genel |
| 5 | Polypropylene film | — | %1–5 | çok düşük | çok düşük | yüksek-Q filtre · ölçüm |
| 6 | Al elektrolit | — | %20 | yüksek | yüksek | güç filtresi |
| 7 | Tantal | — | %5–20 | orta | düşük | kompakt · düşük ESR |

### §7.3 Transistör karşılaştırması

| # | Özellik | BJT | MOSFET | JFET |
|---|---|---|---|---|
| 1 | Kontrol | akım (Ib) | gerilim (Vgs) | gerilim (Vgs) |
| 2 | Giriş empedansı | orta | çok yüksek | yüksek |
| 3 | Gürültü | düşük-orta | düşük-orta | düşük (giriş) |
| 4 | Anahtarlama hızı | hızlı | çok hızlı | yavaş |
| 5 | Uygulama | ses · analog | Class-D · güç | ses · düşük-gürültü giriş |

## §8 Op-amp uygulama devreleri (kavram · değer YOK)

### §8.1 Kazanç devreleri

| # | Devre | Kazanç | Gürültü/offset etkisi | Kullanım |
|---|---|---|---|---|
| 1 | Tersleyen | Av = −Rf/Rin | Ib·Rf offset | filtre · sum |
| 2 | Terslemeyen | Av = 1+Rf/Rg | düşük-Z giriş | genel kazanç |
| 3 | Differential | Av = Rf/Rin (dengeli) | CMRR | diferansiyel |
| 4 | Instrumentation | G = 1+2Rg/R1 | yüksek CMRR | ölçüm · shunt |

### §8.2 Filtre devreleri (op-amp tabanlı)

| # | Filtre | Sıra | Kesim | Q etkisi | Uygulama |
|---|---|---|---|---|---|
| 1 | Sallen-Key LPF | 2 | fc | Q (0.707 Butterworth) | anti-alias |
| 2 | Sallen-Key HPF | 2 | fc | Q | DC bloğu |
| 3 | Multiple-Feedback BPF | 2 | fc · Q | dar bant | EQ bandı |
| 4 | Twin-T Notch | 2 | f0 | çok dar | 50/60 Hz |
| 5 | Active all-pass | 1 | — | faz kaydırma | faz düzeltme |

### §8.3 Op-amp pratik kuralları

| # | Kural | Neden |
|---|---|---|
| 1 | Her aktif bileşene decoupling | yüksek-f gürültü |
| 2 | Geri besleme direncini optimum tut | gürültü · stabilite |
| 3 | Giriş offset için bias direnci dengesi | In kaynaklı offset |
| 4 | Kapasitif yük için geri besleme kesme | osilasyon |
| 5 | Rail-to-rail seçimi (düşük gerilim) | headroom |

## §9 ADC/DAC arayüz detayı (I2S · TDM · kavram)

| # | Arayüz | Sinyal | Kanal | Not |
|---|---|---|---|---|
| 1 | I2S | BCLK · LRCLK · DOUT/DIN | 2 | standart ses |
| 2 | TDM | BCLK · FS · DOUT(dahili slot) | 4–8+ | 8.1 surround |
| 3 | Left-justified | BCLK · WS · DOUT | 2 | I2S varyantı |
| 4 | USB audio | paket | çok | K003 sınırı |
| 5 | SPI/I2C (control) | register | — | K005-firmware |

### §9.1 Arayüz mutabakatı (bayt sırası · clock)

| # | Mutabakat | Uyuşmazlık etkisi | Sahip |
|---|---|---|---|
| 1 | MSB/LSB first | veri bozulması | K005-firmware |
| 2 | BCLK/FS oranı | çerçeve kayması | K004-dsp |
| 3 | MCLK = 256·fs | clock uyuşmazlığı | K005 |
| 4 | Master/slave yönü | iki master çakışması | K005 |

## §10 Gürültü bütçesi (metodoloji · sayısal değer YOK)

> Bu bölüm bir zincirin **gürültü bütçesini** nasıl kuracağını taşır; sayısal değer uydurulmaz.

| # | Adım | İşlem | Çıktı |
|---|---|---|---|
| 1 | Zincir tanımı | aşama listesi (giriş→çıkış) | blok şema |
| 2 | Her aşama gürültüsü | en (nV/√Hz) · kazanç | aşama gürültüsü |
| 3 | Friis uygulama | Ftot = F1 + (F2−1)/G1 + ... | toplam NF |
| 4 | Bant genişliği | gürültü bant (B) | en·√B |
| 5 | Sinyal seviyesi | referans sinyal | SNR hesabı |
| 6 | Eşik karşılaştırma | SNR ≥ hedef | PASS/FAIL |

### §10.1 Gürültü azaltma stratejileri

| # | Strateji | Etki | Maliyet |
|---|---|---|---|
| 1 | İlk aşama düşük-gürültü | dominanter (Friis) | maliyet |
| 2 | Bant genişliğini daralt | √B doğrusal | gecikme |
| 3 | Düşük-empedans zincir | Johnson gürültü azalır | akım |
| 4 | Lock-in / chopper | 1/f kaçınma | karmaşıklık |
| 5 | Ekranlama · twisted pair | EMI azalır | PCB/kablo |

## §11 Ölçüm protokolü detayı (THD/SNR · tekrarlanabilir)

| # | Adım | Koşul | Kayıt |
|---|---|---|---|
| 1 | Sıcaklama | cihaz stabil (dk) | süre |
| 2 | Referans | 1 kHz · nominal seviye | girdi |
| 3 | THD+N ölçümü | analyzer · bant | % · dB |
| 4 | SNR ölçümü | referans/ sessiz | dB |
| 5 | FREP sweep | 20 Hz–20 kHz | dB vs Hz |
| 6 | Kanal izolasyonu | tek kanal aktif | crosstalk dB |
| 7 | Sıcaklık | oda/sıcak | drift |

### §11.1 Ölçüm tuzakları

| # | Tuzak | Etki | Önleme |
|---|---|---|---|
| 1 | Jeneratör gürültüsü | ölçülen gürültü | referans doğrula |
| 2 | Toprak loop | hüm/parazit | tek-nokta toprak |
| 3 | Kablo/sonlandırma | yansıma · parazit | doğru Z · ekran |
| 4 | Analizör limiti | yanlış SNR | floor ölçümü |
| 5 | Sıcaklık drift | yanlış tekrar | sabit sıcaklık |

## §12 Sınır kontrol listesi (bu dosyada YAZILMAZ)

| # | Konu | Nereye gider |
|---|---|---|
| 1 | C++/JUCE · biquad/FFT | K004-dsp · K003-audio |
| 2 | PCB iz/katman/yerleşim · toprak plane | K001-donanım |
| 3 | I2C/SPI register yazımı · boot | K005-firmware |
| 4 | Buck/boost/regulator hesabı | `../guc-ve-koruma/` |
| 5 | Koruma eşiği (akım/gerilim/termal/ESD) | `../guc-ve-koruma/` |
| 6 | ASIO/WASAPI buffer/lock | K003-audio |

## §13 Terim sözlüğü (analog/dijital temel)

| # | Terim | Tanım | Bölüm |
|---|---|---|---|
| 1 | Empedans (Z) | AC direnci (R + jX) | §1 |
| 2 | Reaktans (X) | L/C'nin AC direnci | §1 |
| 3 | Gain-Bandwidth (GBW) | kazanç×bant sabiti | §2 |
| 4 | Slew Rate (SR) | maks. çıkış değişim hızı | §2 |
| 5 | CMRR | ortak mod reddetme oranı | §2 |
| 6 | PSRR | güç reddetme oranı | §2 |
| 7 | Nyquist | fs > 2·fmax kuralı | §3 |
| 8 | Aliasing | fs/2 üstü sinyalin sarması | §3 |
| 9 | Kuantalama | sürekli→kademe | §3 |
| 10 | DNL/INL | doğrusallık hatası | §3 |
| 11 | Johnson gürültü | termal direnç gürültüsü | §4 |
| 12 | Flicker (1/f) | düşük frekans gürültüsü | §4 |
| 13 | NF (gürültü faktörü) | aşama gürültü artışı | §4 |
| 14 | Friis formülü | cascaded NF toplamı | §4 |
| 15 | THD/THD+N | bozulma ölçüleri | §5 |
| 16 | SNR | sinyal/gürültü | §5 |
| 17 | Crosstalk | kanallar arası sızma | §5 |
| 18 | Anti-aliasing | ADC öncesi LPF | §3 |
| 19 | Reconstruction | DAC sonrası image kesme | §3 |
| 20 | Headroom | clip öncesi pay | §2 |

## §14 Sık yapılan hatalar (checklist)

| # | Hata | Sonuç | Önleme |
|---|---|---|---|
| 1 | Anti-alias filtresi yok | aliasing bozulma | §3.5-1 |
| 2 | fs ≤ 2·fmax | Nyquist ihlali | §3.5-1 |
| 3 | Derating yok | güvenilirlik · hasar | anayasa §10.1 |
| 4 | Decoupling yok | osilasyon · gürültü | §8.3-1 |
| 5 | PM < 45° | overshoot · osilasyon | anayasa §11 |
| 6 | Gürültü bütçesi yok | SNR sürprizi | §10 |
| 7 | Toprak loop | hüm · 50/60 Hz | §4.3-3 |
| 8 | Bit/fs uyuşmazlığı | dinamik aralık kaybı | §3.3 |
| 9 | Tolerans yok | üretim sürprizi | anayasa §10.2 |
| 10 | Ölçüm koşulu sabit değil | yanlış tekrar | §11.1 |

## §15 Ölçüm → şartname köprüsü (CoreMusic hedefleri)

| # | Büyükllük | CoreMusic hedefi | Durum | Ölçüm |
|---|---|---|---|---|
| 1 | THD+N | <0.01% @1kHz | PLANNED | §5.3 |
| 2 | SNR | >100 dB | PLANNED | §5.3 |
| 3 | FREP | 20 Hz–20 kHz düz | PLANNED | §5.3 |
| 4 | fs | 48 kHz | sartname | §3 |
| 5 | Bit (dış) | 24-bit | ADR-017 | §3 |
| 6 | Kanal | 2 / 8.1 | sartname | §9 |

> **Dürüstlük:** Bu hedefler **mühendislik şartnamesidir**; ölçüm henüz yapılmadı → `PLANNED`
> (sartname §8). Uydurma ölçüm sonucu yazılmaz.

## §16 SSS (hızlı soru-cevap)

| # | Soru | Cevap | Bölüm |
|---|---|---|---|
| 1 | Nyquist nedir? | fs > 2·fmax (anti-alias zorunlu) | §3.5 |
| 2 | THD ile THD+N farkı? | THD+N gürültüyü de içerir (≥ THD) | §5.1 |
| 3 | Op-amp nasıl seçilir? | GBW · SR · Vos · en (gürültü) | §2.3 |
| 4 | Anti-alias fc nasıl? | fc ≈ fs/2 veya daha düşük | §3.5-2 |
| 5 | Johnson gürültü nedir? | k√(4kTRB) termal gürültü | §4.2 |
| 6 | Cascaded gürültü? | Friis: ilk aşama dominanter | §4.2-4 |
| 7 | I2S vs TDM? | 2 kanal vs 4–8+ slot | §9 |
| 8 | 24-bit SNR? | ~146 dB teorik (6·bit+1.8) | §5.2-3 |
| 9 | C0G ne zaman? | kritik filtre · timing · ses | §7.2 |
| 10 | Derating neden? | worst-case güvenilirlik | §1.1 · anayasa §10 |

### §16.1 Komşu K-node referansları

| # | Konu | K-node | İlgili dosya |
|---|---|---|---|
| 1 | PCB · kart · ısıl | K001-donanım | dış |
| 2 | Dijital filtre (C++) | K004-dsp | dış |
| 3 | Firmware · register | K005-firmware | dış |
| 4 | Ses API (ASIO/WASAPI) | K003-audio | dış |
| 5 | Güç · koruma | bu K-node | `../guc-ve-koruma/` |
| 6 | Devre akışı · katman | bu K-node | `../devre-tasarimi-anayasa.md` |
| 7 | Simülasyon · Monte Carlo | bu K-node | `../devre-simulasyonu-ve-dogrulama.md` |

## §17 Kapanış durum özeti

| # | Metrik | Değer |
|---|---|---|
| 1 | Doğrulanmış URL (bu dosya) | 7 (HTTP 200 · envanter dışı) |
| 2 | Erişilemeyen aday | işaretli (`../electroncis-dogrulama.md` §I.3) |
| 3 | repo devre seviyesi kod | 0 (dürüstlük) |
| 4 | `group` | `mimari` |
| 5 | FM alanı | 9 (v2 §4.2 + `group`) |
| 6 | `status` | `draft` (kapsam Q1 açık) |
| 7 | Vault atıfı | `.ai/electronic/**` (dokunulmaz) |

---

## §6 Kaynakça (bu tur doğrulanmış · HTTP 200 · envanter dışı)

| # | Kaynak | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| 1 | TI LM358 datasheet | https://www.ti.com/lit/ds/symlink/lm358.pdf | 2026-10-13 | ✅ HTTP 200 |
| 2 | TI OPA2134 datasheet | https://www.ti.com/lit/ds/symlink/opa2134.pdf | 2026-10-13 | ✅ HTTP 200 |
| 3 | TI ADS131M04 datasheet | https://www.ti.com/lit/ds/symlink/ads131m04.pdf | 2026-10-13 | ✅ HTTP 200 |
| 4 | TI PCM1794A datasheet | https://www.ti.com/lit/ds/symlink/pcm1794a.pdf | 2026-10-13 | ✅ HTTP 200 |
| 5 | TI slyt691 uygulama notu | https://www.ti.com/lit/an/slyt691/slyt691.pdf | 2026-10-13 | ✅ HTTP 200 |
| 6 | TI sboa122 uygulama notu | https://www.ti.com/lit/an/sboa122/sboa122.pdf | 2026-10-13 | ✅ HTTP 200 |
| 7 | TI sloa093 uygulama notu | https://www.ti.com/lit/an/sloa093/sloa093.pdf | 2026-10-13 | ✅ HTTP 200 |

> **Not (dürüstlük):** slyt691 · sboa122 · sloa093 başlıkları bu tur **tek tek doğrulanmadı** →
> başlık iddiası `⚠️ VERIFICATION REQUIRED`; yalnız "TI bu numaralı literatürü yayımlar" HTTP 200 ile
> desteklenir (bkz. `../electroncis-dogrulama.md` §I.2). Erişilemeyen adaylar §I.3'te işaretlidir.

### §6.1 `.ai/electronic` atıfı

| # | Konu | Vault dosyası | İlişki |
|---|---|---|---|
| 1 | SNR/THD ölçüm | `.ai/electronic/hardware/snr-thd-measurement.md` | atıf |
| 2 | Frekans tepkisi | `.ai/electronic/hardware/frequency-response.md` | atıf |
| 3 | Ses mimarisi | `.ai/electronic/audio-architecture.md` | atıf |
| 4 | Amplifikatör | `.ai/electronic/amplifier-architecture.md` | atıf |
| 5 | DSP filtreleri (dijital) | `.ai/electronic/dsp/filters.md` | sınır (K004) |

> Vault dokunulmaz; yalnız atıf (içerik kopyalanmaz — `../electroncis-kapsam.md` §G U9).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
