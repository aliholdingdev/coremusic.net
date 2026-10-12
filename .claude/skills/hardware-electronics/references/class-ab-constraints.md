# class-ab-constraints — Class AB Kısıtları (K16)

> Kaynaklar: `.ai/CLAUDE.md` §5 (K16/K19, H1) · `.ai/PROJECTS.md` §7.2.1 ·
> `.ai/.decisions/accepted/ADR-089-classab-24v.md` ·
> `.ai/.decisions/accepted/ADR-090-channel-variant-product-family.md`
> Zero Hallucination: bu dosyadaki her rakam aşağıdaki kaynaklardan gelir; kaynaksız iddia yoktur.

## §1 Topoloji

| Kural | Değer | Kaynak |
|-------|-------|--------|
| Tek geçerli topoloji | **Class AB Darlington** (çıkış) | ADR-089 §2 kalem 1 |
| **Class D (TPA3255)** | ❌ **YASAK** — ana topoloji olarak reddedildi (H001) | ADR-089 §3.1-A1 · `.ai/CLAUDE.md` (Class D yasak) |
| Class DC | ❌ Reddedildi | ADR-089 §3.1-A2 |
| AC/şebeke besleme | ❌ Reddedildi (DC-Only mimari, sıfır 50Hz şebeke gürültüsü) | ADR-089 §3.1-A7 · K17 |
| Sinyal zinciri paraziti | ❌ Yasak — **star ground** şart (K19) | `.ai/CLAUDE.md` K1 · ADR-089 §3.3.4 |

## §2 Transistör Çifti & Ses Performansı

| Parametre | Değer | Kaynak |
|-----------|-------|--------|
| Çıkış transistörleri | **MJL21194 (NPN) / MJL21193 (PNP)** — TO-264 | PROJECTS §7.2.1 · CLAUDE §5 "Class AB Amplifier System" |
| Güç | **50W/kanal @ 8Ω** (aralık 25-75W) | PROJECTS §7.2.1 · ADR-089 §2 kalem 2 |
| THD | **<0.005% @ 1W** | PROJECTS §7.2.1 |
| SNR | **>105dB** (min kabul eşiği) | PROJECTS L305 · ADR-089 §4.3 |
| 120dB+ hedefi | DR metriği olarak spec'te; ölçüm tanımı AÇIK → `⚠️ VERIFICATION REQUIRED` (ölçüm tanımlanana kadar SNR >105dB uygulanır) | ADR-089 §4.3 |

⚠️ **Güç çelişkisi notu (C1):** `architecture/k16-class-ab/8-channel-design.md` "100W/kanal"
der — **SSOT = ADR-089 (50W, 25-75W aralık)**; 100W satırı düzeltme bekliyor
(ADR-090 §5.1 adım 5, şart 1b). Yeni dokümanda 100W yazılmaz.

## §3 Modüler Kanal Kuralları

1. **1-8 kanal; her kanal bağımsız PCB ve enable pinli** (CLAUDE K16 · PROJECTS L306).
2. **Monolitik tek PCB üzerinde 8-kanal = RED** — ısıl yönetim (41W/kanal → K18) ve arıza
   izolasyonu kanal başına ayrı PCB ile sağlanır; tek kanal arızası sistemi indirmemeli
   (ADR-089 §3.1-A4, §3.2).
3. **MCU ↔ kanal modülü haberleşmesi zorunlu:** enable / telemetri / koruma hatları MCU'ya
   bağlanır; kanallar birbirinden bağımsızdır (ADR-089 §2.1).
4. **Varyant kombinasyonları (ADR-090 §2.2-b):** ADR-089 listesi 1/2/4/6/8'dir; matristeki
   **5 = 4+1**, **7 = 6+1** olarak üretilir (aynı modül, farklı adet/slot — topoloji değişmez).
5. **2+1 / 7+1 / 8+1 sub kanalları** aynı Class AB modülüdür (bağımsız PCB + enable);
   LFE için ayrı "subwoofer amfi" topolojisi **yoktur** (ADR-090 §2.2-b).
6. Her varyant aynı ortak çekirdek şemayı paylaşır; varyant başına **ayrı tasarım YOK**
   (ADR-090 §2.2-a, §3 alternatif 1).

## §4 DC Offset Koruma

- **>0.5V DC offset riski → DC offset koruma rölesi zorunludur** (`.ai/CLAUDE.md` §23/7 ·
  PROJECTS §7.2.1 "DC offset koruma rölesi"). Röleyi çıkaran/eksilten revizyon **REDDEDİLİR**.
- Kanal devresi koruma zinciri: diferansiyel giriş → VAS → Vbe mult → MJL21194/93,
  NFB, **DC offset / overcurrent / termal koruma** (ADR-090 §2.4-S3).
- Enable pinleri MCU'ya bağlıdır; koruma tetiklenince kanal MCU üzerinden kapatılır
  (ADR-089 §2.1).

## §5 Class D Yasağı (H001)

- Class D / TPA3255 ana topoloji olarak **kullanılamaz, referans edilemez, alternatif
  önerilemez** (ADR-089 §3.1-A1, §3.3.1; kök CLAUDE.md: "Amplifikatör topolojisi Class AB
  — Class D yasak").
- Topoloji değişikliği (Class AB → başka) **yeni ADR** gerektirir (ADR-089 §4.4).
- Ekosistem dokümanlarında Class D geçen "referans senaryo" ifadeleri bu ADR ile geçersizdir
  (yalnız topoloji dersi — entegre edilmez).

## §6 Termal Kesme (özet)

- **KSD301 termal kesme** kanal/kazan korumasının son savunmasıdır; devre dışı bırakılamaz.
  Kesme sıcaklığı ve PWM/fan sayıları → `references/power-thermal.md` §5 (orada çelişki notuyla).

*CoreMusic hardware-electronics · references/class-ab-constraints.md · Updated: 2026-10-07*