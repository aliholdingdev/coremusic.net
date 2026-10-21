# PCB Kuralları ve BOM Kural Seti

> Bağlantı: `standard-circuits.md` (topoloji) · `power-thermal-calcs.md` (sayı) ·
> `sources.md` (kanıt). Kurallar ADR-038 / K19 / K20 ile uyumlu; çelişkiler açık.

## 1. Katman ve stackup (min. 4 katman — K19)

| Katman | Rol |
|---|---|
| L1 (Top) | Sinyal + yüksek di/dt köşe çıkışı, küçük loop alanı |
| L2 (In1) | **GND plane** (kesintisiz; sinyal dönüş yolu) |
| L3 (In2) | **Power plane** (+42 / -42 / +35 / -35 bölme) |
| L4 (Bottom) | Sinyal + boostsüz bileşen; geniş toprak bakır (ısı) |

Kural:
- **Sinyal In2'ye/In1'e inerse In1'de GND kesintisi yok** — referans katmanı
  hiç bölünmez; bölünmek zorundaysa komşu katmanda akım dönüş köprüsü konur.
- Analog (DAC/sürücü) ve güç çıkış bölgeleri tek taraflı radyal yerleşim (K19).

## 2. EMI / gölgeleme

- OSC, Boost (LM5122 f_SW) ve USB izolatör bölgeleri **gölge halkası** ile kuşatılır.
- Yüksek di/dt ucu (SW düğümü) bakır alanı minimum, GND via çiti ile çevrili.
- USB: izolatör (kural 2) öncesi/sonrası ayrı GND bölgesi, tek nokta bağ.
- Çıkış rölesi ve termostatik koruma, osilatör bloklarından uzakta.

## 3. İz boşlukları (worst-case + tolerans)

- Prim-sekonder / izolatör sınırı: **5 mm+** (izolasyon kapasitesi hesapla;
  hedef gerilim ve kateter sınıfına göre `sources.md` kaynaklarından doğrula).
- Sürme çıkış → hassas analog: en az 1 ayırma (yüzey yarı-feza bakır boşluğu).
- ±42 V rail → GND: ortalama iz ~0.5 mm/kV (IPC-2221 tablo B.1, dış katman,
  seviye 1b; **bitmiş yüzey kaplama ve nem dahil değil → VERIFICATION REQUIRED**).
- Tolerans: en kötü durum = nominal × (1 + tolerans); sigorta/NTC gecikmeli
  açılışta yükselir.

## 4. Kaçak akım / röle

- Çıkış rölesi: **DC > 0.5 V'u AC'den ayırma kapasitesi** — röle DC kesme
  ratings'i datasheet'ten oku, DC akım için ayrı kontakt (K18).
- NRND alternatif: temas direnci ve DC yıldık (welding) riski nedeniyle
  **alternatif parça kodu olmadan** röle BOM'a giremez (K20 →
  `⚠️ VERIFICATION REQUIRED`).
- Pre-charge: röle öncesi sınırlayıcı direnç (kaynak: 47R 5W).

## 5. ESD / koruma

- Giriş (XLR/TRS, USB): TVS + gerilim sınırlandırma; TVS kaçak akımı düşük sınıf
  (GND'ye ölçülü kaçak — kural: kaçak akımı datasheet'ten).
- Koruyucu toprak (chassis) ile sinyal GND arasında tek nokta + RC/snubber
  (topraklama K19).

## 6. Termal — iz genişliği (IPC-2221, bakır 1 oz)

Aynı parametreli taşıyıcıda, dış katman:

| Akım | ~0.5 mm | ~1.0 mm | ~1.6 mm | ~2.5 mm |
|---|---|---|---|---|
| 1 A | ✓ | ✓ | ✓ | ✓ |
| 2 A | ✗ | ✓ | ✓ | ✓ |
| 3 A | ✗ | ✗ | ✓ | ✓ |
| 5 A | ✗ | ✗ | ✗ | ✓ (sınır) |

Kök: `I = k·ΔT^0.44·A^0.725` (IPC-2221, external, k=0.048, level 1b,
ΔT=10 °C, A=0.0241 mm² (5 mil, 1 oz), 0.049 mm² (10 mil, 1 oz)).

- Kural: **emniyet faktörü ≥ 2** → 5 A taşıyacak iz nominalde en az 2× alan;
  iç katman (L2/L3) kuralı için IPC-2221 iç katman katsayısı ayrıca okunur
  (`sources.md` S8).
- Yüksek akımlı yollar (çıkış, rail) L3/L4'te bakır olarak alanla, L1'de değil.

## 7. BOM kural seti (K20)

| Kural | Uygulama |
|---|---|
| Tedarik | Mouser / DigiKey — **MPN zorunlu**, doğrulanmış stok/fiyat |
| Doğrulama | Her MPN üretici sayfası + distribütör sayfası → 2+ resmi kaynak |
| Kaçak | Doğrulanmamış MPN/fiyat/stok → `⚠️ VERIFICATION REQUIRED` |
| Tedarikçi kodu | Tedarikçi part no. ≠ MPN → ikisi ayrı sütun |
| Kritik BOM | Her bileşenin en az **1 alternatif MPN** (aktif, röle, regülatör) |
| Termal | Soğutucu + termal ped dahil (C1 — `sources.md` §3) |
| Sensör | KSD301 trip noktası **montaj gövdesiyle** kodlanır (C3) |
| Mika | Isıtkıcı-çıktı arası mika/ped, yalıtım cihazı değil (sivri uçlu) |
| Frekansli | Toroid kırpma, OSC ve boot kapasitörü üretici referans yerleşimi |
| Alan | 4 katman (K19) — **2 katman daraltma gerekçesiz yasak** |
| Görünüm | STEP/3D + paket çizimi doğrulaması (dizgi/dizgi yok) |

> BOM çıktısı `examples/bom-extraction.md` şablonuyla üretilir; fiyat/stok
> **alıntı tarihiyle** yazılır (TTL ≤ 24 s).
