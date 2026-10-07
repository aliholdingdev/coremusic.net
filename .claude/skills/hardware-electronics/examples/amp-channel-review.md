# Örnek — Tek Amplifikatör Kanalı Tasarım Değişikliği İncelemesi

> Girdi → çıktı tam döngüsü: bir kanal revizyon talebi gelir, skill kontrol listesi
> (DC offset · termal · BOM kaynak · Class AB uyumu) uygulanır, bulgu raporu üretilir.

## §1 Girdi (talep)

> **Talep (Kanal 3 revizyonu, "maliyet + ısı düşürme" paketi):**
> 1. Çıkış transistörleri MJL21194/MJL21193 yerine **tek bir Class D çıkış modülü** ile
>    değiştirilsin (daha verimli, daha az ısınır).
> 2. **DC offset koruma rölesi** karttan çıkarılsın (PCB alanı ve maliyet için).
> 3. Kanal 3 ve Kanal 4 PCB'leri **tek ortak PCB** üzerinde birleştirilsin.
> 4. Yeni soğutucu: "X tipi kompakt heatsink" — teklif dosyasında termal değer yok,
>   tedarikçi **bölgesel dağıtıcı**.
> 5. BOM'a yeni bir **stereo DAC (PCM5122)** satırı eklensin (kanal 3 giriş DAC'ı için).

## §2 Uygulanan Kontrol Listesi

| # | Kontrol | Kural | Sonuç |
|---|---------|-------|-------|
| 1 | **Class AB uyumu** | Tek geçerli topoloji Class AB Darlington (MJL21194/93); Class D yasak (H001) | ❌ **FAIL** — talep 1 reddedilir |
| 2 | **DC offset** | >0.5V DC offset riskine karşı koruma rölesi zorunlu | ❌ **FAIL** — talep 2 reddedilir |
| 3 | **Modüler kanal** | Her kanal bağımsız PCB + enable pinli; monolitik birleştirme RED | ❌ **FAIL** — talep 3 reddedilir |
| 4 | **Termal** | Heatsink 0.42°C/W, toplam <0.8°C/W, tam yük <85°C; ölçüleri olmayan parça doğrulanamaz | ⚠️ **V.R.** — talep 4 bekletilir (kaynak yok) |
| 5 | **BOM kaynak + yasak** | Tedarik Mouser/Digikey; PCM5122 yasak → PCM3168A/AK4458 | ❌ **FAIL** — talep 5 reddedilir (PCM5122 H001) |

## §3 Çıktı — Bulgu Raporu

| # | Değişiklik | Bulgu | Karar | Kaynak |
|---|-----------|-------|-------|--------|
| 1 | MJL21194/93 → Class D modül | Topoloji ihlali — Class D ana topoloji olarak yasak | 🔴 **RED (H001)** | ADR-089 §3.1-A1 · `references/class-ab-constraints.md` §5 |
| 2 | DC offset rölesi çıkarılıyor | Güvenlik koruması eksiliyor — >0.5V DC offset rölesi zorunlu | 🔴 **RED** | `.ai/CLAUDE.md` §23/7 · `class-ab-constraints.md` §4 |
| 3 | Kanal 3+4 tek PCB | Modülerlik ihlali — monolitik 8-kanal/birleştirme reddedildi; arıza izolasyonu kaybolur | 🔴 **RED** | ADR-089 §3.1-A4 · `class-ab-constraints.md` §3 |
| 4 | Kompakt heatsink (termal değer yok) | 0.42°C/W · <0.8°C/W · <85°C hedefine karşı doğrulanamıyor; teklif kaynağı bölgesel dağıtıcı (Mouser/Digikey dışı) | 🟡 **BEKLET — ⚠️ VERIFICATION REQUIRED** | PROJECTS §7.2.3 · `power-thermal.md` §3 · `bom-rules.md` §1 |
| 5 | PCM5122 stereo DAC satırı | PCM5122 yasaklı desen; doğru çip PCM3168A/AK4458 | 🔴 **RED (H001)** | CLAUDE §21/§22 · ADR-038 · `bom-rules.md` §3 |

**Geçen madde:** 0 (5 talebin 4'ü reddedildi, 1'i doğrulanamadığı için bekletildi).

## §4 Önerilen Doğru Yol

1. **Isı sorunu** Class D'ye geçişle değil, K18 içinde çözülür: heatsink/fan_PWM profili
   (`power-thermal.md` §3-§4) ve 41W/kanal hava akışı doğrulaması ile.
2. **Maliyet/alan** röle çıkarımıla değil, ortak çekirdek + kanal kademesi ile düşer
   (ADR-090 §2.2 — varyant SKU mantığı).
3. **Kanal 3+4 birleştirme** yerine: aynı ortak çekirdek revizyonundan **iki ayrı modül**
   (enable pinli) — arıza izolasyonu korunur.
4. **Yeni heatsink** yalnız termal değerleri (°C/W) ile Mouser/Digikey kaynağı belgelenirse
   yeniden incelenir; rakamsız teklif BOM'a girmez.
5. **DAC satırı** PCM3168A çekirdeğinde kalır (kullanılmayan kanallar disable — ADR-090 §2.2-b).

**Sonraki adım:** Topoloji/koruma reddleri nedeniyle talep sahibine rapor sunulur;
herhangi bir topoloji değişikliği isteği **yeni ADR + Human Approval Gate** (Guardrail #14)
ile ancak yürütülebilir.

*CoreMusic hardware-electronics · examples/amp-channel-review.md · Updated: 2026-10-07*