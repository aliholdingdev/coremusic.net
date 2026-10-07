---
name: hardware-electronics
description: "Use when editing Class AB amplifier, power supply, thermal, PCB, or BOM documentation and hardware decisions — Tetikleyiciler: 'amfi tasarımı', 'güç kaynağı', 'termal tasarım', 'PCB/BOM'."
license: MIT
metadata:
  version: 3.0.0
  format: claude-skill-v3
  author: Bayram Ali (ULTRATHINK Engineering)
  category: hardware
  tags: [hardware, class-ab, power-supply, thermal, pcb, bom, coremusic]
  updated: 2026-10-07
---

# Hardware Electronics (K16-K20)

## §1 Genel Bakış

CoreMusic donanım katmanları **K16-K20** (Class AB amfi · güç kaynağı · termal · PCB · BOM)
için doküman/düzenleme ve donanım kararı skill'i. Vault sabit gerçekleri (kaynak gösterilmeden
hiçbir değer yazılmaz — Zero Hallucination):

| Katman | Sabit spec | Kaynak |
|--------|-----------|--------|
| **K16 Amfi** | Class AB Darlington MJL21194 (NPN) / MJL21193 (PNP) · 50W/kanal @ 8Ω · THD <0.005% @ 1W · SNR >105dB · 1-8 modüler kanal (her kanal bağımsız PCB, enable pinli) | `.ai/PROJECTS.md` §7.2.1 · `.ai/CLAUDE.md` §5 K16 |
| **K17 Güç** | LM5122 ×2 (boost + inverting) · ±35V simetrik · 6S LiPo 22.2V veya 19-24V DC · 800W · %96 verim · UVP/OVP/OCP/OTP | `.ai/PROJECTS.md` §7.2.2 · `.ai/CLAUDE.md` §5 K17 |
| **K18 Termal** | Fischer SK53-100-SA (300×75×49mm) · Noctua NF-A8 PWM 80mm · KSD301 kesme 97°C · 41W/kanal ısı yönetimi | `.ai/PROJECTS.md` §7.2.3 · `.ai/CLAUDE.md` §5 K18 |
| **K19 PCB** | 6-layer · 200×100mm · 2oz copper · IPC Class 3 · ENIG · 90Ω USB / 50Ω I2S · thermal vias · star ground | `.ai/CLAUDE.md` §5 K19 + H4 |
| **K20 BOM** | 1.775 BOM satırı · Mouser/Digikey · ~$682 sistem maliyeti | `.ai/CLAUDE.md` §5 K20 + H5 |

**Kesin yasaklar:** Class D topoloji **YASAK** (Class AB-only, ADR-089 §3.1-A1) ·
PCM5122 **YASAK** (8.1'de → PCM3168A / AK4458, ADR-038 — H001 REJECT) ·
sinyal zincirine parazit yasak (star ground şart, K19).

**Bağlı kararlar:** ADR-089 (Class AB + ±35V boost, **Accepted**) · ADR-090 (kanal varyant
SKU ürün ailesi, **Accepted**).

## §2 When-to-use

| Trigger | Ne zaman | Bu skill |
|---------|----------|----------|
| `'amfi tasarımı'` | K16 şema/topoloji/transistör dokümanı düzenleme | ✅ |
| `'güç kaynağı'` | K17 LM5122 / ±35V / koruma revizyonu | ✅ |
| `'termal tasarım'` | K18 heatsink / fan profili / KSD301 | ✅ |
| `'PCB/BOM'` | K19 stackup / K20 BOM satırı / SKU maliyeti | ✅ |
| Vault `.md` şablonu / prompt üretimi | `.ai/` şablon & prompt işi | ❌ → `skill-maker` / `.ai/.templates/` |

## §3 Otonom Çalışma Protokolü

1. **Analiz:** Talep incelenir; hangi katman(lar) (K16-K20) etkileniyor belirlenir.
2. **Doğrulama (Truth Mode):** Her spec rakamı vault kaynağına (§6) çaprazlanır;
   kaynağı olmayan iddia → `⚠️ VERIFICATION REQUIRED`; çelişki → DUR (Guardrail #12).
3. **Execution:** İlgili referans dosyası okunur ve kısıtlar uygulanır:
   amfi → `references/class-ab-constraints.md` · güç/termal → `references/power-thermal.md` ·
   BOM/SKU → `references/bom-rules.md`.
4. **Execution:** Kontrol listesi çalıştırılır (DC offset · termal · BOM kaynak doğrulama ·
   Class AB uyumu) — şablon: `examples/amp-channel-review.md`.
5. **Raporlama:** Sıfır halüsinasyon garantisiyle bulgu tablosu sunulur;
   mimari/topoloji değişikliği Human Approval Gate'e (Guardrail #14) bağlıdır.

## §4 Zorunlu Okumalar

| Dosya | İçerik | Ne zaman okunur |
|-------|--------|-----------------|
| `references/class-ab-constraints.md` | Topoloji, transistör çifti, modüler kanal, DC-offset koruma, Class D yasağı, termal kesme | K16 amfi işinde (her zaman) |
| `references/power-thermal.md` | LM5122 ±35V tasarımı, korumalar, heatsink/fan sayıları, PWM profili | K17/K18 işinde |
| `references/bom-rules.md` | Mouser/Digikey tedarik, PCM5122 yasağı, maliyet rakamları + kaynakları, varyant SKU (ADR-090) | K19/K20 işinde |

## §5 Örnekler

| Dosya | Ne gösterir |
|-------|-------------|
| `examples/amp-channel-review.md` | Tek kanal tasarım değişikliği incelemesi: DC offset · termal · BOM kaynak · Class AB kontrol listesi → bulgu raporu (girdi → çıktı tam döngü) |

## §6 Truth Mode & Güvenlik

- **Zero Hallucination:** Vault'ta olmayan voltaj/güç/ölçü/maliyet **uydurulmaz** →
  `⚠️ VERIFICATION REQUIRED`. Tahmin edilen maliyet bandı her zaman TAHMIN etiketiyle yazılır.
- **Güvenlik — DC offset:** Class AB amfide **>0.5V DC offset koruma rölesi** zorunludur
  (`.ai/CLAUDE.md` §23/7); röle çıkaran değişiklik REDDEDİLİR.
- **Güvenlik — termal:** **KSD301 termal kesme (97°C)** son savunmadır, devre dışı
  bırakılamaz/eksiltilemez; UVP/OVP/OCP/OTP korumaları her revizyonda etkin kalmalıdır.
- **H001 Kritik Reddi:** Class D topoloji · PCM5122 · monolitik 8-kanal PCB → anında reddet + uyar.
- **Yıkıcı komut / büyük değişiklik:** silme, deploy, topoloji değişikliği → kullanıcı onayı zorunlu.

## §7 Otorite & Vault Bağlantıları

| Otorite | Bağ |
|---------|-----|
| `.ai/CLAUDE.md` §5 K16-K20 (+ H1-H5, §21 Forbidden, §23 Critical Warnings) | Katman SSOT |
| ADR-089 `classab-24v` (Accepted) | Amfi topolojisi + ±35V boost kararı |
| ADR-090 `channel-variant-product-family` (Accepted) | Kanal varyant SKU ailesi + maliyet kademesi |
| ADR-038 (brain.md) | DAC/USB çekirdeği PCM3168A + XMOS XU316; PCM5122 yasağı |
| `.ai/PROJECTS.md` §7.2 Donanım Projeleri | Ölçülebilir spec tabloları (7.2.1-7.2.3) |

Çelişki durumunda SSOT sırası: `.ai/CLAUDE.md` > `.ai/AGENTS.md` > `.ai/WORKFLOW.md` >
`.ai/brain.md` — çelişki varsa DUR + sor (Guardrail #12).

Anti-overthink: kök AGENTS.md §5 (MAX THINKING 7 madde) geçerlidir.

*CoreMusic Skill v3.0 — metadata.version: 3.0.0 — Updated: 2026-10-07*