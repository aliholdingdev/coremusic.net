---
type: architecture
category: layer
title: "K016 — Amplifikatör"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT
---

# K016 — Amplifikatör

## §1 Kimlik
- Katman: K016 · Alan: **A5** (K16-K20 — Bileşenler/donanım).
- Kapsam: Class AB amplifikatör tasarımı; kanal varyant ürün ailesi.

## §2 Sorumluluk
1. Class AB amplifikatör topolojisi ve çalışma noktası (ADR-089).
2. Kanal varyant SKU ailesi: mono → 8+1 (ADR-090).
3. Ses zinciri gürültü/bozulma hedefleri — şartname düzeyi.

## §3 Bağlantılar (Dependency Rule)
- **Alt:** A0 altyapı/donanım zemini (K000–K005) — sayısal kural: yalnız alt katman (plan §2-1).
- **Üst (çağıran):** K020 üretim katmanı.
- A5 içi fiziksel akış (K017 güç → K018 termal → K019 PCB) plan §3'te tanımlanmadı → **UNKNOWN**.

## §4 ADR Bağlantıları
| ADR | Başlık |
|---|---|
| ADR-089 | Class AB Amplifikatör + 6S LiPo + ±35V Boost (accepted) |
| ADR-090 | Kanal Varyant Ürün Ailesi (mono → 8+1 SKU) |

## §5 Durum
**PLANNED** — ADR dosyaları diskte (`.ai/.decisions/accepted/ADR-089-classab-24v.md`); devre/ölçüm kanıtı yok.

## §6 Risk / Not
- Ölçüm/termal doğrulama verisi yok → performans iddiası yazılmaz (**UNKNOWN**).

---

## §7 Makro Katman Karşılığı (K0–K4)

| Bu K | §6 durumu | Makro eşleme | Makro kimliği |
|---|---|---|---|
| K016 | PLANNED | **Yok** — A5 (K016–K020) makro K0–K4'e girmez | A5 (AGENTS §5) · `[[../katmanli-mimari-k0-k4/index]]` §6.1 |

- Bağlayıcı gruplama ve 21 katmanın makro karşılığı: `[[../00-master-index]]` §8; bağımlılık kenarları: `[[../katman-baglilik-matrisi]]` §4; makro tanımı/istisna defteri: `[[../katmanli-mimari-k0-k4/index]]` §1.1 · §6.1.
- Gerekçe (index §6.1): A5, K000–K005 zeminine **aralık bağımlılığıyla** bağlıdır ve K0'un *hedef donanımı*dır; makro bağımlılık grafiğine ayrı düğüm olarak eklenmez, yalnız A5 etiketiyle taşınır.

## §8 Arayüz (Girdi/Çıktı & API)

| Kenar | Yön | Girdi → Çıktı | Kanıt |
|---|---|---|---|
| K017 → K016 | besleme | 12–24V DC girdi → boost → ±35V bar | ADR-089 §2 kalem 5 |
| K016 → K018 | ısıl | 41W/kanal ısı yükü → termal koruma eşiği | ADR-089 §3.3.3-5 |
| K016 → K019 | taşıyıcı | kanal modülü → bağımsız PCB yerleşimi | ADR-089 §2 kalem 4 · K019 §3 |
| K016 → K020 | üretim | tasarım → BOM/imalat girdisi | K020 §2-3 |
| A5 ↔ A0 | zemin | **yazılım API'si yok** — fiziksel/güç arayüzüdür | K016 §2 · §3 |

- Haberleşme: MCU ↔ modül hatları (enable/telemetri/koruma) **zorunludur**; hibrit MCU zinciri XMOS XU316 + STM32H7/RP2040 + RPi5 (ADR-089 §2.1). Arayüzün **sözleşme/protokol dokümanı yok** → **UNKNOWN**.

## §9 Hata Modları ve Güvenlik Sınırı

| Hata modu | Sonuç | Belirlenmiş kontrol | Kanıt durumu |
|---|---|---|---|
| Aşırı akım / kısa devre | çıkış stage hasarı | **UVP/OVP/OCP/OTP** | ADR-089 §3.3.3-4 (kabul kriteri) — eşik/şema **yok** |
| Aşırı sıcaklık | ısıl kaçış | KSD301 72°C kesme + 80mm PWM fan | ADR-089 §3.3.3-5 — ölçüm **yok** |
| DC offset | hoparlör hasarı | koruma rölesi | ADR-089 §1.1 (PROJECTS L305-311 alıntısı — ikincil) |
| Ölçülmemiş performans | şartname ihlali | AES17 fabrika ölçüm kapısı | ADR-063 §2(e) — ☐ işaretsiz |

**Güvenlik sınırı:** K016 donanım katmanıdır; K006 yazılım primitifleri (CSRF/CSP/RBAC) bu katmana **uygulanmaz**. Sınır donanım korumalarıdır; yüksek enerjili besleme güvenliği K017'ye, ısıl güvenlik K018'e devredilmiştir.

## §10 Bilinmeyenler (⚠️ / UNKNOWN)

1. **UNKNOWN** — A5 içi fiziksel akış (K017 güç → K018 termal → K019 PCB) plan §3'te tanımlanmadı (§3).
2. **UNKNOWN** — THD+N <0.005% · SNR >105/>100dB · 120dB+ iddiaları **ölçülmemiştir** (şartname düzeyi; ADR-089 §2 kalem 3, §3.3.3-2/3 — ölçüm tanımı §4.3 **okunmadı**).
3. **⚠️ VERIFICATION REQUIRED** — ADR-089 §3.3.4: *"Frozen ADR-001–037 metinlerine dokunulamaz; **ADR-090 ve ADR-027 açılmaz**"* der; buna karşılık `.ai/.decisions/accepted/ADR-090-channel-variant-product-family.md` **diskte ve status: accepted** (okundu 2026-10-10). İki metin arasındaki üstünlük bu görevde çözülmedi.
4. **⚠️ VERIFICATION REQUIRED** — ADR-089 §1.1/§3.3.2'nin dayandığı `architecture/k16-class-ab/CLAUDE.md` bu çalışma ağacında **bulunamadı**: kök `architecture/` **yok**; eşleşen dizin `--\architecture\k16-class-ab\ = **2 dosya** (`index.md`, `bom-classab.md`) — `CLAUDE.md` **YOK**. Alıntılanan `CLAUDE.md`/`PROJECTS.md`/`brain.md` satırları **ikincil kaynaktır, bu görevde okunmadı**.
5. **UNKNOWN** — kanal varyant SKU kararı ADR-090'dadır; K016 katmanında üretim/SKU karşılığı yok (bkz. K020 §2-2).
