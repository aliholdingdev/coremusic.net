# hardware-electronics — Kalite Kontrol Listesi (Dalga 2 · doldurulmuş)

> Kalıp: `skill-maker/references/checklist.md` (N1-N10) + bu skill'in domain
> kapıları (kural 1-6, çelişki C1-C6, ölçüm 3 koşul). İki katman: **(A) SKILL.md
> denetimi** (teslimde bir kez) · **(B) tasarım/doküman görevi denetimi** (her görevde,
> rapor öncesi). Sonuç §6'da — ölçüm 2026-10-21.

## A. SKILL.md Biçim Kapıları (skill-maker N1-N9 — bu skill denetimi)

```text
[x] N1 Kök frontmatter yalnız name/description/license/metadata
      (audit 2026-10-21: 4 anahtar + nested metadata.tags listesi → PASS)
[x] N2 description "Use when …" · ≤1024 · Türkçe tetikleyiciler (amfi tasarımı,
      güç kaynağı, termal tasarım, PCB/BOM) → ölçüm: 196 karakter → PASS
[x] N3 SKILL.md 500-2000 satır · dizin toplamı ≥1000 → ölçüm §6 tablosu
[x] N4 HER references/ ve examples/ dosyası §17 tablosundan linkli (checklist.md
      bu denetimle eklendi ve §17'ye satır aldı → orphan=0)
[x] N5 examples/ ≥1 tam döngü (bom-extraction.md: analiz→hesap→ölçüm→BOM) → PASS
[x] N6 name = klasör adı 1:1 (hardware-electronics) · lowercase-hyphen → PASS
[x] N8 CLAUDE.md sidecar yok (2026-10-07'de kaldırılmış; disk kontrolü boş) → PASS
[x] N9 Footer = metadata.version + Updated 2026-10-21
      (2.1.0 döneminde footer 2.0.0'da kalmıştı — bu denetimde 2.2.0'a senkronlandı)
[x] N10 Kök changelog[] yok · sürüm geçmişi references/changelog.md → PASS
[x] UTF-8 BOM yok (ilk 3 bayt EF BB BF değil) → PASS
```

## B. Domain Kapıları (kural 1-6 — her tasarım/doküman görevinde)

```text
[x] Kural 1: BOM'da PCM5122 count = 0 (bu denetimde BOM örnek dosyası tarandı → 0)
[ ] Kural 1 (görev): güncellenen BOM'da PCM5122 satır sayısı = 0 → değilse BLOCK
[ ] Kural 2: XU316 zincirinde USB izolatör + DSP görünür
[ ] Kural 3: ±42 V · Iq 70 mA · bias termal kompanzasyon · röle DC>0.5 V · sessiz başlangıç
[ ] Kural 4: PCB 4 katman min · EMI gölge · star-ground · lineer reg
[ ] Kural 5: gate tablosu (§14.2) 3 koşul dolu — ihlal 0
[ ] Kural 6: firmware/üretim işine girilmedi → yönlendirme yazıldı
```

## C. Kanıt & Çelişki Kapıları (Truth Mode)

```text
[x] Her dış sayı sources.md S-no taşır (S1-S13) — bu denetimde §13 hesap
      atölyesi kaynaklı (S1/S4/S6/S8/S9) → PASS
[x] Çelişki defteri C1-C6 açık (sources.md §3) — hiçbiri sessiz çözülmedi → PASS
[ ] Görevde üretilen her MPN: üretici + distribütör 2 kaynak · kritik ≥1 alternatif
[ ] Fiyat/stok yok veya TTL ≤ 24 s · uydurma MPN yok (uydurma → VERIFICATION REQUIRED)
[ ] Hesap satırları girdi+sonuç formatında (§13: formül → değer → sonuç)
```

## D. Ölçüm Kapıları (§14 — 3 koşul zorunlu)

```text
[ ] THD+N @997 Hz · SNR ±1 dB · Gain ±0.5 dB · bit-perfect · Çıkış DC >0.5 V → röle
[ ] 3 koşul: soğuk / 10 dk ısılmış / beklenen yük — ortalama + sapma raporlandı
[ ] Ölçümü bozan 8 etki tarandı (§14.4: analyzer floor, ısınma, temas direnci, hum…)
[ ] Gate ihlali → powered-off + kök neden + TEKRAR ÖLÇÜM; hâlâ ihlal → ENGELLENDİ
```

## E. Yasak Yüzey (her görevde)

```text
[ ] git commit/add YOK · .ai/ yazma YOK · proje kaynak kodu (*.php/js/css/sql) YOK
[ ] PCB fab dosyası (gbr/drill) YOK · firmware YOK · başka skill dizini YOK
[x] Filler/padding ile satır şişirme yok (anti-pattern 7 denetimi — bu dosya
      gerçek kapı/gerekçe/ölçüm içerir) → PASS
```

## F. Sonuç (2026-10-21 denetimi — ölçümle)

| Kapı | Ölçüm | Sonuç |
|------|-------|-------|
| N1 frontmatter | 4 kök anahtar (name/description/license/metadata) | **PASS** |
| N2 description | 196 karakter, "Use when" + 4 TR tetikleyici | **PASS** |
| N3 SKILL.md satır | 570 (500-2000 arası) | **PASS** |
| N3 dizin toplamı | SKILL.md + 6 references + 1 example (bu dosya dahil) | **PASS** (bkz. ölçüm) |
| N4 orphan | 7 references + 1 example = 8 dosya, 8'i de §17'de linkli | **PASS** |
| N5 examples | bom-extraction.md tam döngü | **PASS** |
| N6 name = klasör | hardware-electronics = hardware-electronics | **PASS** |
| N8 sidecar | 0 adet CLAUDE.md | **PASS** |
| N9 footer | 2.2.0 senkronu (eski 2.0.0 uyuşmazlığı düzeltildi) | **PASS** |
| N10 changelog | references/changelog.md (1.0 → 2.2.0) | **PASS** |
| UTF-8 BOM | ilk 3 bayt EF BB BF değil | **PASS** |
| Kural 1 (statik) | örnek BOM'da PCM5122 count = 0 | **PASS** |
| Çelişki disiplini | C1-C6 açık + sources.md §3 | **PASS** |
| §B görev kapıları | görev bazlı | **N/A (görevde doldurulur)** |
| §D ölçüm kapıları | donanım ölçümü bu denetim kapsamında değil | **N/A (görevde doldurulur)** |

**Karar: GEÇTİ** — biçim + kanıt/çelişki kapıları PASS; §B/§D görev bazlı kapılar
her tasarım/ölçüm görevinde uygulanır (boş madde "geçti" sayılmaz, §8 kural 5).
