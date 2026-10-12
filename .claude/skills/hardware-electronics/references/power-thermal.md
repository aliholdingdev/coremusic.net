# power-thermal — K17 Güç Kaynağı + K18 Termal

> Kaynaklar: `.ai/PROJECTS.md` §7.2.2 (güç) · §7.2.3 (termal) · `.ai/CLAUDE.md` §5 K17/K18 ·
> `.ai/.decisions/accepted/ADR-089-classab-24v.md` (risk/isıl yük) ·
> `ADR-090-channel-variant-product-family.md` (varyant boyutlandırma)

## §1 LM5122 ±35V Tasarımı (K17)

| Kalem | Değer | Kaynak |
|-------|-------|--------|
| Topoloji | **LM5122 ×2 — boost + inverting** (dual interleaved boost) | CLAUDE §5 K17 · PROJECTS §7.2.2 |
| Giriş | **6S LiPo 22.2V nominal** veya **19-24V DC adaptör** (OR-ing ile kaynak seçimi) | PROJECTS §7.2.2 · CLAUDE K17 |
| Çıkış | **±35V simetrik** | PROJECTS §7.2.2 |
| Güç | **800W** | PROJECTS §7.2.2 |
| Verim | **%96** | PROJECTS §7.2.2 |
| Kapasitör | **2200μF × 4 — low-ESR** | PROJECTS §7.2.2 |
| Bobin | **33μH × 2 — toroid** | PROJECTS §7.2.2 |
| Boost'suz doğrudan besleme | ❌ Reddedildi (12-24V giriş şartı ±35V barını boost'a bağlar) | ADR-089 §3.1-A5 |
| AC/şebeke | ❌ Reddedildi — DC-Only, sıfır 50Hz şebeke gürültüsü | ADR-089 §3.1-A7 · README L171 atfı |

## §2 Koruma Zorunlulukları

- **UVP / OVP / OCP / OTP** dördü de etkin kalmalıdır (CLAUDE §5 K17 · ADR-089 §3.3.3 kabul 4).
- Boost gürültüsü ±35V barına sızabilir → SNR düşer; aktif filtreleme referansı
  (Ripple Eater) değerlendirilir (ADR-089 §4.2 risk satırı, §1.4).
- Koruma devreleri revizyonda **eksiltilemez**; Human Approval Gate (Guardrail #14) geçerlidir.

## §3 Heatsink / Fan Sayıları (K18)

| Kalem | Değer | Kaynak |
|-------|-------|--------|
| Heatsink | **Fischer SK53-100-SA — 300×75×49mm** (alüminyum ekstrüzyon) | PROJECTS §7.2.1/§7.2.3 |
| Heatsink termal direnci | **0.42°C/W** | PROJECTS §7.2.3 |
| Toplam termal direnç | **<0.8°C/W** | PROJECTS §7.2.3 |
| Tam yük sıcaklık hedefi | **<85°C** | PROJECTS §7.2.3 |
| Fan | **Noctua NF-A8 PWM — 80mm, 2000rpm max** | PROJECTS §7.2.3 |
| Kanal ısı yönetimi | **41W/kanal** | CLAUDE §5 K18 |
| Sistem ısıl yükü | 8 kanal × 41W = **328W** — hava akışı doğrulaması gerekir | ADR-089 §4.2 |

## §4 PWM Fan Profili

| Sıcaklık | Davranış | Kaynak |
|----------|----------|--------|
| **<30°C** | Sessiz çalışma (fan minimal/devre dışı) | PROJECTS §7.2.3 |
| **30-70°C** | Lineer artış | PROJECTS §7.2.3 |
| **>70°C** | Tam hız | PROJECTS §7.2.3 |

Profil eğrisi revize edilebilir ama **sıralama (sessiz → lineer → tam) korunur**;
tam hız eşiği 70°C üzerine çekilemez (hedef <85°C ile çelişir → DUR + sor).

## §5 KSD301 Termal Kesme

- **KSD301 kesme sıcaklığı: 97°C** (PROJECTS §7.2.3).
- ⚠️ **VERIFICATION REQUIRED — vault içi çelişki:** ADR-089 (§3.3.3 kabul 5 ve §5 K18
  satırı) "**KSD301 72°C kesme**" der; PROJECTS §7.2.3 "**97°C**" der. Contradiction Gate
  (`.ai/CLAUDE.md` §7 #12) gereği bu değer **üretim öncesi sahibi tarafından sabitlenmelidir**;
  çözülene kadar iki kaynak birlikte anılır, tek değer kesin diye yazılmaz.
- KSD301 **son savunmadır** — devre dışı bırakılamaz, eşiği yazılı değiştirilemez (kayıt:
  değişiklik `.ai/log.md` append + onay).

## §6 Varyant Boyutlandırması (ADR-090)

- K17 ve K18 her varyantta **aynı mimarinin küçültülmüş hâli**dir: mono → boost kapasitesi
  ve heatsink küçülür; 8+1 → 9 kanal yüküne göre büyür. **Yeni devre topolojisi değil,
  boyutlandırma** değişir (ADR-090 §2.2-c, §2.2-b).
- Çıkış transistörleri / topoloji varyantlarda **değişmez** (ADR-089 hizası zorunlu —
  ADR-090 §1.4).

*CoreMusic hardware-electronics · references/power-thermal.md · Updated: 2026-10-07*