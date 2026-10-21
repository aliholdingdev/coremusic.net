# ui-analyzer — Kalite Kontrol Listesi (Dalga 2 · doldurulmuş)

> Kalıp: `skill-maker/references/checklist.md` (N1-N10) + bu skill'in READ-ONLY ve
> rapor kapıları (SKILL.md §12-§15). İki katman: **(A) SKILL.md denetimi** (teslimde
> bir kez) · **(B) analiz görevi denetimi** (her analiz raporu öncesi). Sonuç §6'da —
> ölçüm 2026-10-21.

## A. SKILL.md Biçim Kapıları (skill-maker N1-N9 — bu skill denetimi)

```text
[x] N1 Kök frontmatter yalnız name/description/license/metadata
      (audit 2026-10-21: 4 anahtar + nested metadata → PASS)
[x] N2 description "Use when …" · ≤1024 · Türkçe tetikleyiciler (UI analiz, arayüz
      denetimi, tasarım sistemi kontrol, mockup karşılaştırma) → ölçüm: 225 karakter
[x] N3 SKILL.md 500-2000 satır · dizin toplamı ≥1000 → ölçüm §6 tablosu
[x] N4 HER references/ ve examples/ dosyası §17 tablosundan linkli (checklist.md
      bu denetimle eklendi, §17'ye satır aldı ve dosya sayısı 5 → 6 güncellendi)
[x] N5 examples/ ≥1 tam döngü (mockup-vs-live-analysis.md: ham giriş → 6 adım →
      FACT/INFERENCE/UNKNOWN → puanlı rapor) → PASS
[x] N6 name = klasör adı 1:1 (ui-analyzer) · lowercase-hyphen → PASS
[x] N8 CLAUDE.md sidecar yok (2026-10-07 kuralı; disk kontrolü boş) → PASS
[x] N9 Footer = metadata.version + Updated 2026-10-21 (4.1.0 senkronu) → PASS
[x] N10 Kök changelog[] yok · sürüm geçmişi references/changelog.md (1.0 → 4.1.0) → PASS
[x] UTF-8 BOM yok (ilk 3 bayt EF BB BF değil) → PASS
```

## B. READ-ONLY Kimlik Kapıları (§15 — her analiz görevinde)

```text
[ ] Kod üretimi/yazması YOK (CSS/JS/HTML/MD proje kaynak kodu) — devir: ui-code-generator
[ ] Dosya silme/taşıma/ad değiştirme YOK (yanlış yerleşim yalnız raporlanır)
[ ] .ai/ vault'a YAZMA yok (salt okunur, hidden: true)
[ ] git commit/add YOK · CLAUDE.md sidecar OLUŞTURMA YOK
[ ] Kanıtsız satır FACT yapılmadı (UNKNOWN/INFERENCE işaretleri yerinde)
```

## C. Analiz Protokolü Kapıları (§5 — her analiz görevinde)

```text
[ ] ADIM 1 girdi türü belirlendi + rapor başlığına yazıldı [PNG|Canlı|Kod|Karışık]
[ ] ADIM 2 referanslar okundu + DUR kontrolü satırı raporda ("PNG okundu EVET…")
[ ] ADIM 3 her ölçüm 4'lü: Ölçüm + Beklenen(kaynak) + Gerçek(kanıt) + Sapma
[ ] ADIM 4 kural denetimi: ITCSS 11 katman · --cm-* · @media var() yasağı · BEM · inline style
[ ] ADIM 5 her satıra işaret (FACT/INFERENCE/UNKNOWN) + severity (CRITICAL/HIGH/MEDIUM/LOW/⚠️)
[ ] ADIM 6 rapor §12 sekiz bölüm sırasıyla + sapma tablosu (mockup karşılaştırması varsa)
[ ] Kapanış: özet sayım + görünür puan formülü + "Sonraki adım: {skill}" devir satırı
```

## D. WCAG & Puanlama Kapıları (§8/§11)

```text
[ ] WCAG eşikleri sources.md kaynaklı (S1-S3, erişim 2026-10-21) — hafızadan yazılmadı
[ ] Kontrast 4.5:1 / 3:1 large / 3:1 non-text ayrı ayrı; 4.499 = geçersiz (yuvarlama yok)
[ ] Touch iki eşik ayrı: WCAG 2.5.8 = 24×24 · proje zemini --touch-min = 48px
[ ] 4.1.1 Parsing listelenmedi (WCAG 2.2'de silindi)
[ ] Puan: 100 − (15·HIGH + 7·MEDIUM + 3·LOW + 5·⚠️) · CRITICAL varsa puanlama YOK
[ ] Öneriler puana katmadı · UNKNOWN ihlal gibi yazılmadı
```

## E. Yasak Yüzey & Hallüsinasyon (her görevde)

```text
[ ] Token/dosya/satır/Figma değeri uydurulmadı (yoksa ⚠️ VERIFICATION REQUIRED −5)
[ ] Mockup PNG'siz "gördüm" iddiası yok (Guardrail #11; okunamıyorsa DUR)
[ ] Emoji ile mockup/screenshot uydurulmadı · innerHTML/var/hash-routing referansı yok
[ ] name/description tetikleyicileri değiştirilmedi (N2/N6)
```

## F. Sonuç (2026-10-21 denetimi — ölçümle)

| Kapı | Ölçüm | Sonuç |
|------|-------|-------|
| N1 frontmatter | 4 kök anahtar (name/description/license/metadata) | **PASS** |
| N2 description | 225 karakter, "Use when" + 4 TR tetikleyici | **PASS** |
| N3 SKILL.md satır | 571 (500-2000 arası) | **PASS** |
| N3 dizin toplamı | SKILL.md + 6 references + 3 examples (bu dosya dahil) | **PASS** (bkz. ölçüm) |
| N4 orphan | 9 dosyanın tamamı §17 tablosunda linkli (checklist dahil) | **PASS** |
| N5 examples | 3 örnek, en az 1 tam döngü (mockup-vs-live-analysis) | **PASS** |
| N6 name = klasör | ui-analyzer = ui-analyzer | **PASS** |
| N8 sidecar | 0 adet CLAUDE.md | **PASS** |
| N9 footer | metadata.version 4.1.0 = footer 4.1.0 | **PASS** |
| N10 changelog | references/changelog.md (1.0 → 4.1.0) | **PASS** |
| UTF-8 BOM | ilk 3 bayt EF BB BF değil | **PASS** |
| WCAG kaynaklılığı | §8 eşikleri S1-S3'e bağlı; 4.1.1 silinmiş olarak doğru listelendi | **PASS** |
| §B-§E görev kapıları | görev bazlı — analiz raporu üretildiğinde uygulanır | **N/A (görevde doldurulur)** |

**Karar: GEÇTİ** — biçim + kaynak disiplini kapıları PASS; §B-§E görev bazlı kapılar
her UI analiz raporunda uygulanır (boş madde "geçti" sayılmaz).
