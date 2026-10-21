# Changelog — hardware-electronics

> Format: skill-maker v3 (N10). Sürümler `metadata.version` ile eşleşir.

## 2.0.0 — 2026-10-21

**Format:** skill-template v3.0 + new-skill-format (N1–N10); sidecar `CLAUDE.md` kaldırıldı (§5-N8).

**Silinen dosyalar (eski v1 içerikleri, git'te geri alınabilir):**
- `SKILL.md` (98 satır)
- `references/class-ab-constraints.md`
- `references/power-thermal.md`
- `references/bom-rules.md`
- `examples/amp-channel-review.md`

**Eklenen dosyalar:**
- `references/sources.md` — kaynak + çelişki defteri (zorunlu, N4)
- `references/power-thermal-calcs.md` — koparıcı güç/ısı/iz hesabı (yeni derinlik, K17/K18)
- `references/standard-circuits.md` — referans devre topolojileri (K16/K17, guardrail ⚠️)
- `references/datasheet-tips.md` — BJT/regülatör/termal datasheet okuma
- `references/troubleshooting.md` — arıza tanı
- `references/pcb-bom-rules.md` — PCB katman/EMI/iz + BOM kural seti (K19/K20)
- `references/changelog.md` — bu dosya
- `examples/bom-extraction.md` — devre analizi → BOM tam döngü

**İçerik:** K16 (MJL21193/94, 50 W/8 Ω, THD<0.005 %, SNR>105 dB),
K17 (LM5122 ×2 ±35 V, 800 W, %96), K18 (41 W/kanal, 328 W, NF-A8, KSD301),
K19 (star-ground, 4 katman), K20 (Mouser/DigiKey BOM).

**Çelişkiler açık bırakıldı (C1–C6, `sources.md` §3) — tek taraflı çözülmedi.**

## 1.0.0 — (ilk sürüm)

- `SKILL.md` (98 satır) + 3 reference + 1 example: Class AB kısıtları,
  güç/termal, BOM kuralları, kanal inceleme örneği.

## Kural (gelecek sürümler)

- Yeni çelişki → `sources.md` §3'e **madde olarak** eklenir, sessizce çözülmez.
- `description` tetikleyicileri (`amfi tasarımı`, `güç kaynağı`, `termal tasarım`, `PCB/BOM`)
  korunur (N6, sabit).
- Dosya ekleme/çıkarma → bu changelog'a satır.
