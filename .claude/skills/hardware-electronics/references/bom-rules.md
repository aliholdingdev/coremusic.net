# bom-rules — K20 BOM Disiplini & SKU Kuralları

> Kaynaklar: `.ai/CLAUDE.md` §5 K20 + H5, §21 Forbidden Patterns, §22 Edge Cases ·
> `ADR-089-classab-24v.md` · `ADR-090-channel-variant-product-family.md` ·
> ADR-038 (brain.md — PCM3168A + XMOS XU316 çekirdeği)

## §1 Tedarik Disiplini

- **Tedarik kanalları: Mouser / Digikey** (CLAUDE §5 K20; H5 atfı).
- **AGPL/uyumsuz lisanslı donanım kaynağı** (EAGLE/KiCad projesi) kopyalanamaz — yalnız
  şema fikri referans alınır (ADR-089 §3.3.4).
- Tedarikçi teklif gizliliği / sırrı BOM'a yazılmaz (REDACTED kuralı — ADR-090 §1.4).
- Her yeni BOM satırının **kaynağı** (dosya/tedarikçi) belgelenir; kaynaksız satır
  `⚠️ VERIFICATION REQUIRED` ile bekletilir.

## §2 Maliyet Rakamları (kaynaklarıyla — tahmin yazılmaz)

| Rakam | Kaynak |
|-------|--------|
| **K20: 1.775 BOM satırı, ~$682 sistem maliyeti** | `.ai/CLAUDE.md` §5 K20 |
| **H5: 639 bileşen, ~$1.067 (8 kanal) / ~$133 (kanal)** — `bom-classab.md`, disk doğrulandı 2026-09-26 | `.ai/CLAUDE.md` §5 H5 |
| **K16 sınıfı amfi BOM hedefi: <$430** | ADR-089 §1.3 (atfı: `architecture/k16-class-ab/CLAUDE.md`) |
| ADR-090 kanal kademe bantları (~$180 → ~$820) | **TAHMİN** — vault BOM'undan oransal türetildi → `⚠️ VERIFICATION REQUIRED`; F3'te tedarikçi teklifiyle kapanır (ADR-090 §1.4, §4.3-R4) |

⚠️ `8-channel-design.md` **$882** listesi 100W revizyonuna aittir, **SSOT değildir**
(ADR-090 §1.1-C1) — yeni dokümanda maliyet kaynağı olarak kullanılmaz.

## §3 PCM5122 Yasağı → PCM3168A / AK4458

| Yasaklı | Doğru | Kaynak |
|---------|-------|--------|
| **PCM5122** (8.1 surround'da; hiçbir yerde referans/alternatif önerilemez — H001 REJECT) | **PCM3168A** (8-out/6-in codec, ADC+çıkış çekirdeği) veya **AK4458** (8ch DAC, 32-bit/768kHz) | CLAUDE §21, §22 · ADR-089 §3.1-A3 · brain ADR-038 |

- DAC/USB çekirdeği **PCM3168A + XMOS XU316** sabittir (ADR-038); varyantlar bu çekirdeğin
  kanal kadesidir, alternatifi değil (ADR-090 §1.4).
- Düşük varyantta PCM3168A **değiştirilmez** — kullanılmayan kanallar **disable** edilir
  (tek BOM, tek firmware — ADR-090 §2.2-b). Stereo/4-kanal çip geçişi **ayrı ADR** ister.
- Mono/2 kademesinde PCM5122 tabanlı stereo DAC **kullanılamaz** (ADR-090 §1.4 H001).

## §4 Varyant SKU Kuralları (ADR-090)

- **10 varyant, her biri AYRI SKU:** mono · 2 · 2+1 · 4 · 5 · 6 · 7 · 8 · 7+1 · 8+1
  (ADR-090 §2.1 — kullanıcı onaylı matris).
- **Ortak çekirdek:** aynı PCB ailesi; kanal azaldıkça modül adedi/boş slot azalır;
  farklı varyant için **ayrı stackup YOK** (ADR-090 §2.2-a).
- **Amfi kademesi:** ADR-089 8×50W çekirdeğinden kanal sayısı kadar modül adedi
  (5 = 4+1, 7 = 6+1 kombinasyonu — §2.2-b).
- **Maliyet 4 kademe:** ortak çekirdek → DAC → amfi kanalı → ayrı SKU
  (stok, test, ambalaj, fiyat ayrı — ADR-090 §2.2).
- **SKU sayısı 10 ile sabittir**; "ara varyant" talebi = yeni ADR (ADR-090 §4.3-R1).
- SKU sözdizimi `CM-<kanal kademesi>` → `⚠️ VERIFICATION REQUIRED` (vault'ta tanımlı değil,
  üretim öncesi sabitlenir — ADR-090 §2.2-d).
- **9. çıkış boşluğu:** 7+1/8+1 varyantlarında PCM3168A 8-out yetersiz → ikinci DAC/TDM
  çözümü F2'de doğrulanmadan bu SKU'lar BOM'a girmez (ADR-090 §4.3-R2, §5.4 şart 1a).

## §5 PCB Üretim Notu (K19 — BOM ile birlikte kontrol edilir)

- 6-layer · 200×100mm · **2oz copper** · **IPC Class 3** · **ENIG** finish ·
  **90Ω USB / 50Ω I2S** impedans · thermal vias · star ground (CLAUDE §5 K19 + H4).
- Boş slot tapajı / panel-konnektör kademesi ortak BOM'u genişletir → varyant delta
  satırlarında ayrı izlenir (ADR-090 §4.2-4.3-R5).

*CoreMusic hardware-electronics · references/bom-rules.md · Updated: 2026-10-07*