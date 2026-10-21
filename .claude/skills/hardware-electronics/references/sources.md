# sources — Truth Mode Kaynak KayDI (erisim tarihi: 2026-10-21)

> Bu dosya SKILL.md §4 uzerindenden baglidir (N4). Aşağıdaki dis kaynaklar 2026-10-21'de
> dogrulanmistir; bu skill'de gecen her dis kaynakli rakam bu tablodan gelir. Kaynaksiz rakam
> yazılmaz — vault disi her iddia `⚠️ VERIFICATION REQUIRED` isareti tasir.
> Vault ici (`.ai/`) kaynaklar ayri tabloda (§2) — onlar ic kaynak sayilir, web dogrulamasi gerekmez
> ama catiski varsa Contradiction Gate isler.

## §1 Web Kaynaklari (dis, dogrulanmis)

| # | Kaynak (ad) | URL | Erisim | Ne dogrulandi (1 satir) |
|---|-------------|-----|--------|--------------------------|
| S1 | onsemi MJL21193 (PNP) / MJL21194 (NPN) resmi datasheet (Rev. 8, Eki 2024) | https://www.onsemi.com/download/data-sheet/pdf/mjl21193-d.pdf | 2026-10-21 | Perforated Emitter teknolojisi + yuksek guclu cikis transistoru uygulamasi; SNR/THD karakterizasyonu; SOA iki sinirla tanimlanir (ort junctionsicakligi + secondary breakdown); SOA egrileri TJ(pk)=150°C uzerinden verilir |
| S2 | DigiKey — onsemi MJL21194 urun sayfasi | https://www.digikey.com/en/products/detail/onsemi/MJL21194/606090 | 2026-10-21 | V_CEO (max) 250 V · I_C (max) 16 A listelemesi (MJL21194G: 250 V / 16 A / 4 MHz / 200 W / TO-264) |
| S3 | Mouser — onsemi MJL21194 urun sayfasi | https://www.mouser.com/ProductDetail/onsemi/MJL21194 | 2026-10-21 | P_D 200 W · f_T 4 MHz · calisma sicakligi -65°C..+150°C listelemesi |
| S4 | Texas Instruments — LM5122 resmi datasheet (SNVS954H, Sub 2013 – Rev Haz 2017) | https://www.ti.com/lit/ds/symlink/lm5122.pdf | 2026-10-21 | Maks giris 65 V · min giris 3 V (calisma) / 4.5 V (start-up) · cikis ≤100 V · 1.2 V referans ±%1 · 1 MHz'e kadar senkron/serbest calisma · 3 A surucu · peak-current-mode · cycle-by-cycle akim sinirlama · hiccup-mode overload korumasi · programlanabilir satir UVLO · bypass (VOUT=VIN) · "Audio Power Supply" uygulama alani · AEC-Q100 Grade 1 · akim sinir esigi 75 mV (R_CS uzerinden) |
| S5 | Fischer Elektronik SK 53 serisi teknik dokumani (RS Online yayinladigi PDF) | https://docs.rs-online.com/a146/0900766b813eb969.pdf | 2026-10-21 | SK 53 100 SA: profil kesiti 180 x 48 mm · uzunluk 100 mm · R_th 0.6–1.25 K/W araligi (montaja bagli) |
| S6 | Buerklin — Fischer SK 53 100 SA urun sayfasi | https://www.buerklin.com/en/p/fischer-elektronik/heat-sinks/sk-53-100-sa/72B926 | 2026-10-21 | 100 x 180 x 48 mm boyut · 1.25–0.6 K/W termal direnç araligi · siyah anodize |
| S7 | Conrad — Fischer Elektronik 10021164 SK 53 100 SA | https://www.conrad.com/en/p/fischer-elektronik-10021164-sk-53-100-sa-heat-sink-0-65-k-w-l-x-w-x-h-100-x-180-x-48-mm-188883.html | 2026-10-21 | Tekil deger ornegi: 0.65 K/W · 100 x 180 x 48 mm · taban kalinligi 5 mm · alüminyum |
| S8 | Fischer Elektronik — ozel uzunluk uretimi (basin bulteni) | https://www.fischerelektronik.de/en/latest-news/news/2025-fischer-pressemitteilungen/new-heatsinks-from-fischer-elektronik | 2026-10-21 | Fischer stok standart uzunluklarin yaninda **musteriye ozel her uzunlukta** profil kesebildigini beyan eder (SK53 icin 0.42 K/W gibi bir deger ancak uzunluk arttirilariyla anlamlidir — §3 notu) |
| S9 | IPC-2221 iz/akim formulu (3 bagimsiz teknik kaynak caprazlamasi) | https://standardclarity.com/standards/ipc-2221 · https://alternatronics.com/en/calculators/pcb-trace-width · https://www.advancedpcb.com/en-us/tools/trace-width-calculator | 2026-10-21 | I = k·(dT)^0.44·(A)^0.725 · k=0.048 dis katman / 0.024 ic katman · A mil² · gecerlilik: dT 10–100°C, bakir 0.5–3 oz, ≤35 A / ≤400 mil · IPC-2221C (2023) / B (2012) / A (2003) sürüm zinciri |
| S10 | AES17 tabanli THD+N olcum yontemi — IASA/NDLA ADC performans spesifikasyonu | https://www.digitizationguidelines.gov/audio-visual/documents/ADC_performIntro_20120820.pdf | 2026-10-21 | AES17 yontemi: dusuk bozulmali sine ile uyarm → fondamental notch filtresiyle cikar → 20 Hz–20 kHz bant sinirla → RMS oran olarak raporla · tipik frekans 997 Hz · -1/-10/-20/-60 dBFS kombinasyonlari |
| S11 | TI SLAA114 — Digital Audio Measurements (FAQ) | https://www.ti.com/lit/pdf/slaa114 | 2026-10-21 | Olcum bantinin duyulabilir bolgede (20 Hz–20 kHz) tanimlanmasi sart; Dynamic Range = THD+N@-60 dB olcumu + 60 dB (or. -53 dB THD+N → 113 dB DR); analyzer'in DUT'tan one cikmasi gerekir |
| S12 | MathWorks — THD+N measurement with tone-tracking (AES17-1998 referansi) | https://www.mathworks.com/help/audio/ug/thd-n-measurement-with-tone-tracking.html | 2026-10-21 | AES17-1998(r2004): 997 Hz test tonu · notch filtresi Q=5 · tonu peak icin peaking, temizlemek icin notch ayni merkezde |
| S13 | Audio Precision — APx500 User Manual v8.1.0 | https://www.ap.com/fileadmin-ap/technical-library/APx500_User_Manual_v8-1-0.pdf | 2026-10-21 | Olcum platformu referansi (dosya varligi + surum dogrulandi); icerik parse edilmedi → kullanilirken cihaz uzerinden teyit gerekir |

## §2 Vault Ici Kaynaklar (`.ai/` — okuma serbest, yazma yasak)

| # | Kaynak | Ne verir | Bu skill'deki kullanimi |
|---|--------|----------|--------------------------|
| V1 | `.ai/CLAUDE.md` §5 K16–K20 (+ H1–H5) · §21 Forbidden · §23 Critical Warnings | Katman spec'leri, yasak desenler, kritik uyarilar | SKILL.md §1, §10 · tüm references |
| V2 | `.ai/PROJECTS.md` §7.2.1–§7.2.3 | K16 amfi / K17 guc / K18 termal olcerulebilir spec tablolari | SKILL.md §1 · power-thermal-calcs.md |
| V3 | `.ai/.decisions/accepted/ADR-089-classab-24v.md` (Accepted) | Class AB + ±35V boost karari; reddedilen alternatifler; risk satirlari | standard-circuits.md §1 · SKILL.md §10 |
| V4 | `.ai/.decisions/accepted/ADR-090-channel-variant-product-family.md` (Accepted) | 10 varyant SKU ailesi; ortak cekirdek; maliyet kademeleri | pcb-bom-rules.md §4 |
| V5 | ADR-038 (`.ai/brain.md` uzerinden) | DAC/USB cekirdegi PCM3168A + XMOS XU316; PCM5122 yasagi | pcb-bom-rules.md §3 · SKILL.md §1 |
| V6 | `.ai/.templates/hardware/hardware-template.md` (v2.0.1, Guardrail #16) | Donanim tasarim dokumani sablonu — bileşen/PCB/termal/BOM/test iskeleti | SKILL.md §3 (uretimde yeni donanim dokumani bu sablonla) |

## §3 Capraz Dogrulama Notlari (catiskilar — gorulur kalir)

| # | Konu | Vault | Web | Davranis |
|---|------|-------|-----|----------|
| C1 | Fischer **SK 53 100 SA** termal direnci | PROJECTS §7.2.3: **0.42 °C/W** · olculer **300×75×49 mm** | S5/S6/S7: ayni MPN icin **0.6–1.25 K/W**, profil **180×48 mm**, uzunluk **100 mm** | **⚠️ VERIFICATION REQUIRED** — MPN icindeki "100" Fischer yapisinda uzunluk (mm) demektir (S5); vault'taki 300 mm uzunluk ancak **ozel uretim** ile anlamlidir (S8 fabrikasyonu mumkun diyor). Uretim oncesi sahibi tarafindan netlestirilir: ya MPN uzunluk/ozel-uretim notu duzeltilir, ya R_th degeri duzeltilir. Bu skill tek basina degeri "kesin" yazmaz |
| C2 | MJL21194 **V_CEO** | Vault'ta V_CEO yazmiyor (yalniz MPN + uygulama var) | S2: 250 V · eski bazi referanslarda 230 V geciyor | Skill'de **250 V (S2)** yazilir; 230 V goren eski dokuman karsilastirmasi yapilacaksa `⚠️ VERIFICATION REQUIRED` eklenir (datasheet PDF metni otomatik okunamadi — S1'den tablo satiri dogrudan tasinmadi) |
| C3 | KSD301 kesme sicakligi | PROJECTS §7.2.3: **97 °C** · ADR-089: **72 °C** | dis kaynak aranmadi (vault ici catiski) | **⚠️ VERIFICATION REQUIRED** — Contradiction Gate: uretim oncesi sahibi sabitler; iki deger birlikte anilir, tek deger kesin diye yazılmaz (power-thermal-calcs.md §7) |
| C4 | 100W/kanal ifadesi | ADR-089 §2 kalem 2: **50 W (25–75 W aralik)** — SSOT | — | `architecture/k16-class-ab/8-channel-design.md` icindeki 100W satiri duzeltme bekliyor (ADR-090 §5.1); yeni dokumanda **100 W yazılmaz** |
| C5 | Noctua NF-A8 teknik rakamlari (2000 rpm vb.) | PROJECTS §7.2.3 (vault) | Bu oturumda dogrulanmadi | `⚠️ VERIFICATION REQUIRED` — fan sayfalari sonraki research turunde teyit edilir; skill'de vault degeri kaynakli olarak taşınır |

## §4 Kullanim Kurallari

1. Bu dosyadaki S-numarali kaynak disinda **dis kaynakli rakam SKILL.md/ references/ dosyalarina yazılamaz**.
2. Kaynak erisilemez hale gelirse (link olu): deger **kaldirilmaz**; satir sonuna `(erisim 2026-10-21)` notu ve
   yeniden dogrulama `⚠️ VERIFICATION REQUIRED` isareti konur.
3. Uretim karari (BOM kilitleme, PCB fabrikasyonu) yalniz §2 vault kaynaklari + kapanmis §3 catiskilariyla verilir.
4. Fiyat/stok bilgisi **hiçbir zaman** bu dosyaya yazılmaz (REDACTED disiplini + ADR-090 §1.4 gizlilik).

*CoreMusic hardware-electronics · references/sources.md · Updated: 2026-10-21*
