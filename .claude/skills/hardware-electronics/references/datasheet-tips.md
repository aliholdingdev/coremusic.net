# Datasheet Okuma İpuçları — BJT / Regülatör / Termal

> Bu dosya: power datasheet'inden tasarım kararını hızla çıkarma yöntemi.
> Her iddia resmi datasheet'e dayanır; erişim tarihi `references/sources.md` içinde.

## 1. BJT (MJL21193/MJL21194) — bakılacak 10 satır

| # | Parametre | Nerede | Neden önemli |
|---|---|---|---|
| 1 | `V_CEO` | Absolute Maximum Ratings | Sürme gerilimi limiti → ±42 V'ta marj |
| 2 | `V_CBO` | Absolute Maximum Ratings | Genelde `V_CEO`'dan yüksek (250 V) — karıştırma kaynağı |
| 3 | `I_C` (continuous) | Absolute Maximum Ratings | Emitter direnci ve termal tasarımın girdisi |
| 4 | `h_FE` tablosu (I_C vs V_CE) | Electrical Characteristics | Darlington'un düşük h_FE'si → sürücü akımı hesabı |
| 5 | `V_CE(sat)` | Electrical Characteristics | Doyma kaybı → kısa devre/köprü hesabı |
| 6 | `f_T` (gain-bandwidth) | Electrical Characteristics | Kraft frekansı; geri besleme kararlılığı |
| 7 | `C_ob` (çıktı kapasitansı) | Electrical Characteristics | Yüksek frekans kararlılığı, Miller etkisi |
| 8 | `P_D` (T_C=25 °C) | Maximum Ratings | Isı üzerine kurulu maksimum güç — ısıtıcı ile ölçeklenir |
| 9 | `R_θJC` / `R_θJA` | Thermal Resistance | Çıkış → gövde → ortam zincirinin anahtarı |
| 10 | `SOA` (Safe Operating Area) | Graphs | Anlık güç/komütasyon sınırları — grafik okumak zorunlu |

**Kritik ayrım (S1, s.1):**
`V_CEO = 250 V` (collector-emitter, açık base) ile
`V_CBO = 250 V` (collector-base, açık emitter) **aynı sayı olabilir ama farklı pin arasıdır**.
Sürme transistörü kararı için `V_CEO` geçerlidir.

**Darlington specifics (S1, s.1):**
MJL21193 = **PNP** · MJL21194 = **NPN** · her ikisi de Darlington (iç direnç + diode),
`h_FE` discrete BJT'den düşük → sürücü (VAS) akım marjı datasheet'te `h_FE` tablosundan
okunur, varsayılmaz.

**⚠️ VERIFICATION REQUIRED (C2):** Bazı ikincil kaynaklar `V_CBO`'yu 250 V,
`V_CEO`'yu 250 V'tan farklı gösterir. Karar öncesi ilgili datasheet revizyonundaki
`Absolute Maximum Ratings` tablosu bizzat kontrol edilmelidir.

## 2. Boost regülatör (LM5122) — bakılacak 12 satır

| # | Parametre | Neden |
|---|---|---|
| 1 | `V_IN` operating range | ±35 V çift hattı (toplam ~70-80 V DC) giriş aralığında mı? |
| 2 | `V_OUT` max | Çıkış gerilimi hattan yüksek olmalı (boost) |
| 3 | `f_SW` (switching frequency) | Toroid/indüktör ve EMI filtresi hesabı |
| 4 | `I_LIM` / `I_PK` | 800 W hedefi için indüktör akım zirvesi |
| 5 | `R_DS(on)` (entegre FET varsa) | Konduksiyon kaybı → verim (96 % hedefi) |
| 6 | `η` (efficiency) tipik tablo | 96 % hedefinin datasheet karşılığı var mı? |
| 7 | `V_CC` / `gate drive` | Sürücü beslemesi, boot kapasitörü |
| 8 | `soft-start` (t_SS) | Sessiz başlangıç (K18) ile ilişkili |
| 9 | `PGOOD` / `UVLO` | Koruma ve röle mantığı |
| 10 | `R_θJA` (QFN) | Paket termal limiti |
| 11 | Compensation (R/C) | Kararlılık döngüsü — datasheet tasarım örneği |
| 12 | Layout note | Yüksek di/dt loop — PCB §K19 ile eşleşmeli |

## 3. Termal transistör / sigorta / röle

- **KSD301 (bimetal):** Trip sıcaklığı seri/kod ile belirlenir; **montaj basıncı ve
  gövde-Isıtkıcı teması** trip noktasını kaydırır (C3 çelişkisi — bak `sources.md` §3).
- **Sigorta:** `I²t` (akım-kare-zaman) eğrisi → açma süresi; sürekli akım = 8 Ω yükte
  beklenen RMS üstü ile çarpılır.
- **Çıkış rölesi:** kontakt direnci (mΩ), DC kesme kapasitesi (DC > 0.5 V tehlike),
  bobin gerilimi ve sessiz-kontak sırası (pre-charge / NRND alternatif — bak `pcb-bom-rules.md` §4).

## 4. Ortak hatalar (okuma sırasında)

1. **Tipik vs maksimum:** "typ" değer ile tasarım yapılıp "max" hesaplanmaz (C5 benzeri).
2. **Test koşulu:** `h_FE` 1 A / 2 V V_CE'de ölçülür; düşük akımda farklıdır.
3. **Grafik-tablo farkı:** `P_D` hem tabloda (T_C=25) hem grafikte (T_C=25/100) olur —
   gerçek hedef sıcaklıkta grafikten oku.
4. **Revizyon:** datasheet rev-x ile rev-y arasında `R_θJC` değişebilir → revizyonu not et.
5. **Kaynak hattı:** TI/onsemi resmi PDF; Mouser/DigiKey ürün sayfasındaki parametreler
   **ikincil** kaynaktır (S3, S5, S7).
