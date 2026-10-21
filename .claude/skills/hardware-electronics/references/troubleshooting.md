# Arıza Tanı — Amfi / Güç / PCB

> Her belirti → olası neden → ölçüm → çözüm sırasıyla. **Ölçmeden parça değiştirme.**

## 1. Amfi tarafı

### 1.1 Çıkışta DC var (röle açılmıyor)
- **Ölçüm:** güç kaynağı kapalıyken çıkış uçları DC direnç (Ω modu) — 0 Ω kısa devre;
  güçlendirmeden önce kesin.
- **Nedenler:** sürme transistörü kısa devre · VAS sızıntısı · geri besleme ağı açık
  (nç/kaçak) · zemin kayması (K19 star-ground ihlali).
- **Çözüm:** kanal başına çıkış DC > 0.5 V ise röle mantığı doğru çalışıyor (K18);
  kaynağı kapat, kısa devre yoksa önce sürücü/çıkış çiftini ayırarak izole et.

### 1.2 Çıkışta DC var ama röle bağlı
- Röle eşiği > 0.5 V'a kaymış veya ölçüm noktası yanlış (röle öncesi/sonrası).
- **Ölçüm:** röle giriş ve çıkışını ayrı ayrı ölç.

### 1.3 Osilasyon (yük kapalıyken titreşim / ısı)
- **Ölçüm:** osiloskop, 20 MHz band-limit **kapalı** — ~1-20 MHz arası sinüs.
- **Nedenler:** Miller kapasitansı yetersiz · tel uzunluğu (geri besleme düğümü devri)
  · zemin düğümü · güç kaynağı bobinlenmesi.
- **Çözüm:** geri besleme direncini çıkışa **en yakın** pin'e al; +42/-42 V rail
  beslemelerini 100 nF + 10 µF ile kısa tut.

### 1.4 Gürültü (hum) — 50/100 Hz
- **Ölçüm:** çıkışta spectrum; 50 Hz ve harmonikleri.
- **Nedenler:** ortak empedans (star-ground ihlali) · tek taraflı radyal yerleşim
  (K19) · zayıf filtre kapasitansı · röle temas direnci.
- **Çözüm:** K19'a dön — filtre kapasitansı yerine dokunmadan önce toprak topolojisini doğrula.

### 1.5 Sessiz başlangıç çalışmıyor (pop)
- Soft-start / mute RC zaman sabiti · röle ön-şarj (pre-charge) direnci ·
  DC koruma gecikmesi ayarı (K18).

### 1.6 Kanal sıcaklığı dengesiz
- Bias (Iq = 70 mA) kanallar arası farklı · termal kompanzasyon (KSD301) yanlış
  gövdeye monte edilmiş · ısıtıcı-kanal arası termal ped eksik (C1/C3 —
  `sources.md` §3).

## 2. Güç kaynağı / besleme

| Belirti | Kontrol | Not |
|---|---|---|
| Çıkış yok | UVLO / PGOOD / giriş sigortası | LM5122 K17; soft-start |
| 96 % verim tutmuyor | f_SW, indüktör doyması, FET ısısı | datasheet §2 `datasheet-tips.md` |
| Sıçrama akımı | NTC / pre-charge / soft-start | sessiz başlanc (K18) |
| Rail asimetrik (+42/-42 değil) | ikinci regülatör, dengeleme dirençleri | 800 W toplam (K17) |

## 3. PCB / BOM

- **Açık devre bir net:** continuity modu + net adını BOM/şemadan doğrula; vias
  kırığı ve termal via eksikliği tipik.
- **Termal via yok / eksik:** çıkış alanı ısınmaz ama röle/trace ısınır —
  4 katman (K19) In2 = GND omurgası delik sayısını kontrol et.
- **Yanlış parça (değer/paket):** 0603 yerine 0402, THT SOT-23 — paket kontrolü
  `pcb-bom-rules.md` §2-3.
- **ESD hasarı:** giriş koruma (TVS) yoksa dokunuşta kanal ölü —
  `pcb-bom-rules.md` §5.

## 4. Ölçüm protokolü (önce-sonra)

1. Sessiz ortam, anlamlı akım çeken yük yok (şebekeden beslenen ekipman dışarıda).
2. Referans toprak: ölçü aletinin COM'u star-ground düğümüne (K19).
3. Önce DC (rail, çıkış DC), sonra AC (hum, osilasyon), sonra spektrum.
4. Sonuçları **3 koşul** altında tekrarla (örn. soğuk/ısılmış/yük altında).
5. Gate: THD+N · SNR · gain · bit-perfect sayımları → `power-thermal-calcs.md` §6
   ve SKILL.md §ölçüm.

> ⚠️ Bu dosyadaki eşik değerleri K16-K19 spesifikasyonundan gelir; ölçüm
> sayımları **kanıt olmadan** "geçti" diye yazılmaz (N5/anti-pattern).
