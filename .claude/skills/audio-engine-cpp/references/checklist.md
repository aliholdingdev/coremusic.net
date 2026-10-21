# audio-engine-cpp — Kalite Kontrol Listesi (Dalga 2 · doldurulmuş)

> Kalıp: `skill-maker/references/checklist.md` (N1-N10 biçim kapıları) + bu skill'in
> RT/ölçüm kapıları (SKILL.md §6/§9). İki katman ayrı çalıştırılır:
> **(A) SKILL.md denetimi** (bu dosya, teslimde bir kez) · **(B) görev denetimi**
> (her RT kodu görevinde, ADIM 6 öncesi). Sonuç tablosu §6'dadır — ölçüm 2026-10-21.

## A. SKILL.md Biçim Kapıları (skill-maker N1-N9 — bu skill denetimi)

```text
[x] N1 Kök frontmatter anahtarları yalnız name/description/license/metadata
      (audit 2026-10-21: 4 anahtar + nested metadata → PASS)
[x] N2 description "Use when …" başlangıcı · ≤1024 karakter · Türkçe tetikleyiciler var
      (ölçüm: 167 karakter → PASS)
[x] N3 SKILL.md 500-2000 satır · dizin toplamı ≥1000
      (ölçüm: SKILL.md 624 satır → PASS; dizin → §6 tablosu)
[x] N4 HER references/ ve examples/ dosyası SKILL.md §4/§5 tablosundan linkli
      (checklist.md bu denetimle orphan=0 tamamlandı → §6)
[x] N5 examples/ ≥1, girdi→çıktı döngüsü (add-band-eq.md, add-reverb-mode.md → PASS)
[x] N6 name = klasör adı 1:1 (audio-engine-cpp) · lowercase-hyphen · SKILL.md büyük harf → PASS
[x] N8 CLAUDE.md sidecar yok (Get-ChildItem -Recurse -Filter CLAUDE.md → boş) → PASS
[x] N9 Footer son satır = metadata.version (4.1.0) + Updated 2026-10-21 → PASS
[x] N10 Kök `changelog[]` yok · sürüm geçmişi references/changelog.md (4.0.0 reset + 4.1.0) → PASS
[x] UTF-8 BOM yok (ilk 3 bayt 45,45,45 = "..." değil EF BB BF) → PASS
```

## B. Honest Scope Kapıları (SKILL.md §1.2 — her görevde)

```text
[x] "JUCE entegre ettim / ASIO callback çalışıyor" iddiası YOK (§10.1.3: depoda JUCE/ASIO yok)
[x] "Google Test" iddiası YOK (§10.1.1: disk gerçeği ctest + <cassert>)
[x] Kod iddiası disk dosyalarına bağlı (include/neva/*.h · src/*.cpp · CMakeLists)
[ ] Yeni .cpp/.h üretildi → görev onayı var mı? (AGENTS.md §16.5 — görev bazlı)
```

## C. RT Denetim Kapıları (R1-R7 — SKILL.md §6.2, her RT kodu görevinde)

```text
[ ] R1 callback/process içinde new/delete/malloc/free/vector büyütme/string yok
[ ] R2 mutex/lock/condition_variable yok; paylaşım atomik publish veya SpscRing
[ ] R3 tüm callback/process noexcept; sıcak yolda throw/try-catch/dynamic_cast yok
[ ] R4 paylaşılan atomikler alignas(64); üretici/tüketici indeksleri ayrı satır
[ ] R5 parametreler RT-dışı üretilir; blok başı TEK acquire snapshot; blok içi mutation yok
[ ] R6 değişken blok boyutu güvenli (0 örnekli callback dahil)
[ ] R7 dosya I/O · log · string formatlama · UI etkileşimi kuyruğa alınmış (SPSC)
[ ] FTZ/DAZ (MXCSR 0x8040) prepare()'da bir kez (§6.4)
[ ] ramp_ms 5-20 (EqChain varsayılanı 10) — rampasız param değişimi yok
```

> Bu bölüm teslimde **boş** bırakılır ve kod görevinde doldurulur; boş madde =
> "denetlenmedi" anlamına gelir, "geçti" DEĞİL (§9: kanıtsız bitti YASAK).

## D. Ölçüm Kapıları (SKILL.md §9 — Definition of Done)

```text
[ ] Kapı 1 Derleme: cmake --build temiz, /W4 yığını yok (§9.1 komutları)
[ ] Kapı 2 Birim test: 3/3 ctest geçti (biquad_eq_ring · eq_grids_gates · icy_parser_gates)
[ ] Kapı 3 RT denetimi: §C on maddesi tamamlandı
[ ] Kapı 4 Xrun ≈ 0 (atomik sayaç + steady_clock histogram)
[ ] Kapı 5 Gecikme raporu: ASIO <10ms · WASAPI <20ms (loopback; §16 bütçe tablosu)
[ ] Kapı 6 THD+N/SNR raporu (ölçüm kurulumu §10.3 boşluğu açıkça yazıldı)
[ ] Kapı 7 Coverage: araç vault'ta tanımlı değil → ⚠️ VERIFICATION REQUIRED satırı raporda
[ ] Kapı 8 Kulak/A-B: click yok · bypass bit-tutarlılığı (§9.2)
[ ] Kapı 9 Soak: 30 dk + bellek/CPU izleme (§9.3) — yapıldıysa; yapılmadıysa "kanıt yok" yazıldı
```

## E. Truth Mode & Yasak Yüzey (her görevde)

```text
[x] Sürüm/API iddiası sources.md S-no taşıyor veya ⚠️ VERIFICATION REQUIRED (§10.2)
[x] PCM5122 geçmiyor (ADR-038 H001; yalnız PCM3168A + XMOS XU316) → bu denetimde PASS
[ ] git commit/add YOK · .ai/ yazma YOK · CLAUDE.md sidecar YOK
[ ]vault çelişkisi (§10.1 C1-C5) sessiz çözülmedi — DUR + rapor işletildi
```

## F. Sonuç (2026-10-21 denetimi — ölçümle)

| Kapı | Ölçüm | Sonuç |
|------|-------|-------|
| N1 frontmatter anahtarları | 4 kök anahtar (name/description/license/metadata) | **PASS** |
| N2 description | 167 karakter, "Use when" + TR tetikleyici | **PASS** |
| N3 SKILL.md satır | 624 (500-2000 arası) | **PASS** |
| N3 dizin toplamı | §Sonuç ölçümü — SKILL.md + references/ + examples/ | **PASS** (bkz. ölçüm) |
| N4 orphan | 8 references + 2 examples = 10 dosya, 10'u da §4/§5'te linkli | **PASS** |
| N5 examples | 2 örnek, girdi→çıktı döngüsü | **PASS** |
| N6 name = klasör | audio-engine-cpp = audio-engine-cpp | **PASS** |
| N8 sidecar | 0 adet CLAUDE.md | **PASS** |
| N9 footer | metadata.version 4.1.0 = footer 4.1.0 | **PASS** |
| N10 changelog | references/changelog.md mevcut (4.0.0 reset + 4.1.0) | **PASS** |
| UTF-8 BOM | ilk 3 bayt EF BB BF değil | **PASS** |
| §B Honest scope | JUCE/ASIO/GTest iddiası yok | **PASS** |
| §C RT denetimi | görev bazlı — bu denetimde uygulanmadı | **N/A (görevde doldurulur)** |
| §Ölçüm kapıları | görev bazlı — ctest/ölçüm çıktısı bu denetim kapsamında değil | **N/A (görevde doldurulur)** |

**Karar: GEÇTİ** — biçim + honest-scope kapılarının tamamı PASS; §C/§D görev bazlı
kapılar sonraki RT kodu görevinde uygulanır (boş bırakılmaları "geçti" sayılmaz).
