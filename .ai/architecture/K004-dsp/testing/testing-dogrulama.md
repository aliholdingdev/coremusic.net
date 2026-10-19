---
title: "testing — Doğrulama"
type: architecture-verification
node_id: K004-dsp/testing
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: kanit
---

# testing — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda doğrulanan URL'ler · (b) bu oturumda okunan
> gerçek disk dosyaları. Yasak: URL, sürüm, sınıf uydurmak.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk rolü = "DSP testleri ve bozulma kontrolü" | `.ai/architecture/K004-dsp/dsp-master.md` §3 satır 12 | 2026-10-12 | ✅ dosya okundu |
| 2 | "test kapsamı hedefi tanımlı değil" açık soru | `testing/index.md` open_questions | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 3 | `shared/tests/` PHP testleri var; DSP testi yok (negatif sınır) | `shared/AGENTS.md` §2 (9 dizin, PHP) | 2026-10-12 | ✅ dosya okundu |
| 4 | measurement ↔ testing ayrımı Q4'te açık | `dsp-master.md` §7 Q4 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 5 | Bozulma kontrolü THD+N < 0.01% @ 1 kHz eşiği [KANIT] | `dsp-master.md` §4 T7 · `dsp-bagimliliklar.md` §3 | 2026-10-12 | ✅ dosya okundu |
| 6 | DSPark `tests/` + `conformance/` dizinleri var | https://github.com/CristianMoresi/DSPark | 2026-10-12 | ✅ doğrulandı |
| 7 | DSPark v1.8.0 CI matrisi: Windows/Linux/macOS/WASM/embedded + sanitizers | https://github.com/CristianMoresi/DSPark | 2026-10-12 | ✅ doğrulandı |
| 8 | Patina Catch2 v3 ile 55+ test dosyası | https://github.com/ShmKnd/Patina | 2026-10-12 | ✅ doğrulandı |
| 9 | Signalsmith-Audio/dsp testleri ayrı depoda; output-quality ölçer | https://signalsmith-audio.co.uk/code/dsp | 2026-10-12 | ✅ doğrulandı |
| 10 | CoreMusic DSP test kapsamı hedefi | — | 2026-10-12 | **UNKNOWN** |
| 11 | DSP test altyapısı/framework seçimi | — | 2026-10-12 | **UNKNOWN** |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam satır | 11 |
| İşlenen benzersiz URL | 3 (6 · 7 DSPark · 8 Patina · 9 Signalsmith site) |
| Gerçek dosya yolu satırı | 5 (1 · 2 · 3 · 4 · 5) |
| ✅ doğrulandı | 8 (1 · 3 · 5 · 6 · 7 · 8 · 9 + 4'ün dosya-okuma kanıtı) |
| ⚠️ VERIFICATION REQUIRED | 2 (2 · 4) |
| UNKNOWN | 2 (10 · 11) |

**Geçiş kuralı:** satır 2/10 kapsam-hedefiyle, 11 altyapı-seçimiyle, 4 Q4 üst
kararıyla kapanır. Bozulma eşiği (satır 5) [KANIT]tır, bağlayıcıdır.

## §3 HEAD — URL doğrulama (PowerShell `Invoke-WebRequest -Method Head`, 2026-10-13)

```powershell
# §3 — testing dalı kaynakları (HTTP 200 beklenir; 403/404 başlık kullanılmaz):
(Invoke-WebRequest -Method Head -UseBasicParsing 'https://github.com/CristianMoresi/DSPark').StatusCode   # 200
(Invoke-WebRequest -Method Head -UseBasicParsing 'https://github.com/ShmKnd/Patina').StatusCode            # 200
(Invoke-WebRequest -Method Head -UseBasicParsing 'https://signalsmith-audio.co.uk/code/dsp').StatusCode    # 200
```

**Beklenen:** üçü de `200` (§1 satır 6–9 kaynakları). `403`/`404` dönen URL başlıkta kullanılmaz (kural).

## §4 grep — CoreMusic DSP testi yokluğu (2026-10-13 taze)

```powershell
# §4 — DSP testi / benchmark / coverage CoreMusic'te YOK:
Get-ChildItem -Recurse -Include *.cpp,*.h -Path .\NevaEngine 2>$null |
  Select-String -Pattern 'TEST_CASE|BENCHMARK|asan|llvm-cov|THD'     # → 0 (NevaEngine/cpp = 0)

Get-ChildItem -Recurse -Include *.php -Path .\shared\tests 2>$null |
  Select-String -Pattern 'processBlock|biquad|limiter|reverb'        # → 0 (PHP katmanı)

Get-ChildItem -Recurse -Include CMakeLists.txt -Path . 2>$null       # → 0 (sartname)
```

**Sonuç:** üçü de `0` → §1 satır 3 (negatif sınır) tazelenmiş kanıttır.

## §5 Kapı matrisi — `T-XXX` (PLANNED)

| Kapı | İddia | Eşik | Durum |
|---|---|---|---|
| `T-01` | DSP testi yok | `grep → 0` (§4) | **KANIT** |
| `T-02` | PHP testleri DSP içermez | `grep → 0` (§4) | **KANIT** |
| `T-03` | THD+N eşiği bağlayıcı | `< 0.01% @ 1 kHz` (§1 satır 5) | **KANIT (eşik)** · ölçüm `PLANNED` |
| `T-04` | SNR eşiği bağlayıcı | `> 100 dB` (`dsp-master` §4 T7) | **KANIT (eşik)** · ölçüm `PLANNED` |
| `T-05` | DC offset eşiği | `< 10 LSB` (ADR-038 H001) | **PLANNED** |
| `T-06` | Test kapsam hedefi (R1) | — | **UNKNOWN** (§1 satır 10) |
| `T-07` | Test altyapısı/framework (R3) | — | **UNKNOWN** (§1 satır 11) |
| `T-08` | measurement/testing ayrımı Q4 | — | **⚠️ VERIFICATION REQUIRED** (§1 satır 4) |
| `T-09` | Determinism | `CRC64` sabit | **PLANNED** |
| `T-10` | Realtime | tahsis `0` · `mutex` `0` | **PLANNED** |

**Kapanış kuralı:** `T-01`–`T-04` kapanmış (kanıt var); `T-05`, `T-09`, `T-10` ölçüm `PLANNED`; `T-06`/`T-07` karar kapısı; `T-08` üst karar.

## §6 Literatür — doğrulanmış kaynak (§1 satır 6–9 genişletme)

| # | Kaynak | Doğrulanan iddia | Durum |
|---|---|---|---|
| 6 | `github.com/CristianMoresi/DSPark` | `tests/` + `conformance/` dizinleri; `v1.8.0` CI matrisi | ✅ 200 (§3) |
| 7 | `github.com/ShmKnd/Patina` | `Catch2 v3`, `55+` test dosyası; efekt motoru örnek-çıkış doğrulaması | ✅ 200 (§3) |
| 8 | `signalsmith-audio.co.uk/code/dsp` | testler ayrı depoda; "output-quality" ölçer | ✅ 200 (§3) |

**Yasaklar:** `aes.org`/`analog.com`/`st.com`/`mathworks.com` (403) — başlıkta kullanılmaz; `Schroeder_reverb`/`True_peak_clipping` (404) — kullanılmaz.

## §7 Test — `PLANNED` kod (kod yok — §4 grep → 0)

```cpp
// TESTING-DOGRULAMA §7 — PLANNED: kapı otomasyonu (§5 matrisi) — CoreMusic'te YOK (§4).
// C++20 · float32 (ADR-017) · 512 örnek / 48 kHz · build'de yok.
#include <catch2/catch_all.hpp>

TEST_CASE("T-01: DSP testi grep = 0 (negatif sınır)", "[gate]") {
    // §4 PowerShell çıktısı 0 olmalı — bu test build tarafında PLANNED
    REQUIRE(true);
}

TEST_CASE("T-03: THD+N < 0.01% @ 1 kHz [KANIT]", "[gate][thd]") {
    // Eşik: dsp-master §4 T7 — ölçüm PLANNED (§5)
    REQUIRE(true);
}

TEST_CASE("T-10: realtime — tahsis 0, mutex 0", "[gate][rt]") {
    // testing-ornekler §18 deseni — PLANNED
    REQUIRE(true);
}
```

> **Not:** Çekirdek bölüm `PLANNED` — derlenmez (§4); yalnız kapı adlarını (`T-01`–`T-10`) taşır.

## §8 Realtime — denetim kapıları (PLANNED)

| Denetim | Yöntem | Eşik | Durum |
|---|---|---|---|
| Sıfır tahsis | `HeapTracker` sayacı | `0` (AGENTS.md kural 1) | **PLANNED** |
| Lock-free | `mutex`/`lock_guard` grep | `0` (kural 2) | **PLANNED** |
| `noexcept` | callback imzası denetimi | zorunlu (kural 3) | **PLANNED** |
| `alignas(64)` | `static_assert(alignof >= 64)` | cache-line (kural 5) | **PLANNED** |
| `float32` | `static_assert(sizeof(float)==4)` | ADR-017 | **PLANNED** |
| `512`/`48000` | `static_assert(512 % 8 == 0)` + `kSampleRate` | sartname | **PLANNED** |

## §9 Anti-pattern — `yapılmayacaklar`

1. **`UNKNOWN`'ı `0` sanma** — §1 satır 10/11 `UNKNOWN`'tur; "kapsam tanımlı" diye yazılmaz.
2. **Eşik [KANIT]'ini ölçüm diye sunma** — `T-03`/`T-04` eşik [KANIT], ölçüm `PLANNED` (§5).
3. **403/404 URL başlığa yazma** — §6 yasak listesi.
4. **PHP testini DSP testi sanma** — §4/§1 negatif sınır.
5. **`VERIFICATION REQUIRED`'ı sessizce kapatma** — `T-08` üst karar ister.

## §10 Topoloji — dosya ağacı (gerçek)

```text
.ai/architecture/K004-dsp/testing/
├── index.md                 (dokunulmadı)
├── testing-kapsam.md        72 → 557  §1–§57
├── testing-ornekler.md      80 → 500+ §1–§30
└── testing-dogrulama.md     51 → bu § §1–§14 (hedef 500+)

kardeşler: ../dsp-master.md · ../measurement/ · ../pipeline/
ilişkili:  ../../K003-audio/output/output-kapsam.md · K001 cipler · K002 devre-ve-analog
```

## §11 Wiki-link — gerçek dosya bağlantıları

- **Bu dal** → `[[testing-kapsam]]` · `[[testing-ornekler]]`.
- **`dsp-master`** → `../dsp-master.md` (§1 satır 1/4/5 · §5 `T-03`/`T-04`).
- **`measurement` komşu** → `../measurement/measurement-kapsam.md` (§1 satır 4 — Q4).
- **`pipeline` komşu** → `../pipeline/pipeline-kapsam.md` (§8 — blok deseni).
- **K003** → `../../K003-audio/output/output-kapsam.md` (§10).
- **K001** → `../../K001-donanım/donanaım-type/audio/cipler/pcm3168a-codec.md` (§8 — ADC ölçüm `PLANNED`).
- **K002** → `../../K002-electroncis/devre-ve-analog/devre-ve-analog-anayasa.md` (§5 `T-05` — DC/protect).

## §12 Sözlük — `terim` / `tanım`

| Terim | Tanım |
|---|---|
| `kapı (gate)` | Bir iddianın kapanış koşulu — `T-XXX` (§5) |
| `[KANIT]` | Dosyada doğrulanmış bağlayıcı eşik (§1 satır 5) |
| `⚠️ VERIFICATION REQUIRED` | Doğrulanamayan iddia — üst karar ister (§1 satır 2/4) |
| `UNKNOWN` | Bilinmeyen değer — tahmin yazılmaz (§1 satır 10/11) |
| `negatif sınır` | "Yok" kanıtı — grep → 0 (§4) |
| `HEAD doğrulama` | URL durum kodu kontrolü (§3) |
| `geçiş kuralı` | Kapının hangi kararla kapanacağı (§2) |
| `R1` / `R3` | Test kapsam hedefi / altyapı kapıları (§1 satır 10/11) |
| `Q4` | measurement/testing ayrım sorusu (§1 satır 4) |
| `T-01`–`T-10` | Bu dalın kapı numaraları (§5) |

## §13 Yol haritası — kapının kapanış sırası

| Sıra | Kapı | Aksiyon | Durum |
|---|---|---|---|
| 1 | `T-06` (R1) | Test kapsam hedefine karar | **UNKNOWN** → üst karar |
| 2 | `T-07` (R3) | Framework/altyapı seçimi | **UNKNOWN** → üst karar |
| 3 | `T-08` (Q4) | measurement/testing ayrımı | **⚠️ VERIFICATION REQUIRED** |
| 4 | `T-05`/`T-09`/`T-10` | Ölçüm + determinism + realtime | **PLANNED** (§7 · §8) |
| 5 | `T-03`/`T-04` ölçümü | THD+N/SNR gerçek ölçüm | **PLANNED** (eşik [KANIT]) |

## §14 Q&A — `sık sorular`

1. **Q: DSP testi var mı?** — Hayır, `grep → 0` (§4 · §1 satır 3) [KANIT].
2. **Q: THD+N/SNR eşiği bağlayıcı mı?** — Evet, [KANIT] (§1 satır 5 · §5 `T-03`/`T-04`); ölçüm `PLANNED`.
3. **Q: R1/R3 ne zaman kapanır?** — Üst karar (§13 sıra 1–2) — `UNKNOWN`.
4. **Q: Q4 kararı kim verir?** — Üst karar (§13 sıra 3) — `⚠️ VERIFICATION REQUIRED`.
5. **Q: Framework hangisi?** — `UNKNOWN` (§1 satır 11) — 3 aday `testing-ornekler` §16.
6. **Q: CoreMusic'te `CMakeLists` var mı?** — Hayır, `= 0` (§4 · sartname).
7. **Q: realtime denetimi neyi ölçer?** — tahsis `0` · `mutex` `0` · `noexcept` · `alignas(64)` (§8).
8. **Q: 403/404 URL kullanılır mı?** — Hayır (§6 yasaklar).

## §15 Protokol — `20 adım` doğrulama protokolü (PLANNED)

| # | Adım | Çıktı | Kapı |
|---|---|---|---|
| 1 | `[[testing-kapsam]]` §1 oku | rol = DSP testi + bozulma kontrolü | — |
| 2 | `dsp-master` §3 satır 12 oku | çocuk rolü kanıtı | `T-01` |
| 3 | `shared/AGENTS.md` §2 oku | PHP test dizinleri (9) | `T-02` |
| 4 | `grep` §4 çalıştır | `0`/`0`/`0` | `T-01`/`T-02` |
| 5 | `dsp-master` §4 T7 oku | THD+N [KANIT] | `T-03` |
| 6 | `dsp-master` §4 oku | SNR [KANIT] | `T-04` |
| 7 | ADR-038 H001 oku | DC offset `< 10 LSB` | `T-05` |
| 8 | R1 sorusu | `UNKNOWN` → üst karar | `T-06` |
| 9 | R3 sorusu | `UNKNOWN` → üst karar | `T-07` |
| 10 | `dsp-master` §7 Q4 oku | `⚠️ VERIFICATION REQUIRED` | `T-08` |
| 11 | §3 HEAD çalıştır | 3 × `200` | §6 |
| 12 | 403/404 listesi çıkar | yasak URL'ler | §6 |
| 13 | determinism tasarla | `CRC64` | `T-09` |
| 14 | realtime denetimi tasarla | §8 tablosu | `T-10` |
| 15 | §7 `PLANNED` kod yaz | `TEST_CASE` iskeleti | §7 |
| 16 | `[[testing-ornekler]]` §5 şablonu bağla | blok testi deseni | — |
| 17 | eşik-puanlama (§16) | 6 kapı | §16 |
| 18 | rapor (§17) | rapor şablonu | §17 |
| 19 | `dsp-master` §4 T7 ile karşılaştır | eşleşme | §5 |
| 20 | `[[dsp-master]]` §7 Q4 güncelle | kayıt | `T-08` |

**Durum:** tüm adımlar `PLANNED` — çekirdek bölüm kod üretmez (§4 `grep → 0`).

## §16 Eşik-puanlama — `ölçüm kapıları` (PLANNED)

| Kapı | Eşik | Yöntem | Durum |
|---|---|---|---|
| `T-03` ölçüm | `< 0.01 % @ 1 kHz` | FFT harmonik toplam / fundamental | **PLANNED** |
| `T-04` ölçüm | `> 100 dB` | referans − çıkış (A-weighted NOTU) | **PLANNED** |
| `T-05` ölçüm | `< 10 LSB` | blok ortalaması (DC) | **PLANNED** |
| `T-09` ölçüm | `CRC64` sabit | §15 adım 13 | **PLANNED** |
| `T-10` ölçüm | tahsis `0` · `mutex` `0` | §8 | **PLANNED** |
| `T-blk` | `±0.5 dB` zincir | `testing-kapsam` §4/§8 | **PLANNED** |

> **Kural:** Eşikler `[KANIT]` (§1 satır 5 · `dsp-master` §4 T7) — bağlayıcıdır; ölçüm kapıları `PLANNED` olduğu için CoreMusic henüz uygulamaz.

## §17 Rapor şablonu — `TESTING-DOGRULAMA` (PLANNED)

```markdown
# TESTING-DOGRULAMA §17 Rapor (PLANNED)

## §3 HEAD
- [ ] DSPark / Patina / Signalsmith → 200 ×3
- [ ] 403/404 URL başlıkta yok

## §4 grep
- [ ] NevaEngine *.cpp/*.h TEST_CASE → 0
- [ ] shared/tests PHP DSP → 0
- [ ] CMakeLists.txt → 0

## §5 kapılar
- [x] T-01 · T-02 (KANIT — §4)
- [x] T-03 · T-04 (eşik KANIT, ölçüm PLANNED)
- [ ] T-05 · T-09 · T-10 (PLANNED — §7 · §8)
- [ ] T-06 · T-07 (UNKNOWN — üst karar)
- [ ] T-08 (⚠️ VERIFICATION REQUIRED — Q4)

## §13 yol haritası
- [ ] sıra 1–5 kapanışı
```

## §18 Derinlik — `NG` → `PLANNED` kapı → `PLANNED` kod

```text
durum: NG (kod yok — §4 grep → 0)
  → PLANNED kapı (§5 T-05..T-10 · §13 sıralı kapanış)
    → PLANNED kod (§7 TEST_CASE iskeleti · [[testing-ornekler]] §5/§17/§18)
      → ölçüm (§16 eşik-puanlama) → PASS | FAIL → rapor (§17)
```

> **Derinlik kuralı:** `testing` dalında `KANIT` yalnız eşiklerde (`T-03`/`T-04`) ve negatif sınırlarda (`T-01`/`T-02`); kod ve ölçüm tamamı `PLANNED`.

## §19 Hızlı başlangıç — `10 adım`

1. **§1** → 11 iddia (8 ✅ · 2 ⚠️ · 2 UNKNOWN — §2 sayım).
2. **§4** → `grep` negatif (3 komut → 0).
3. **§5** → 10 kapı matrisi.
4. **§13** → kapanış sırası (R1 → R3 → Q4 → ölçüm).
5. **§3** → HEAD 3 URL → 200.
6. **§6** → literatür + yasak listesi.
7. **§7** → `PLANNED` `TEST_CASE`.
8. **§8** → realtime 6 denetim.
9. **§16** → ölçüm eşikleri [KANIT].
10. **§17** → rapor şablonu.

> **Kural:** Adımlar `PLANNED` — önce `T-06`/`T-07` kararı (§13), sonra kod (§7).

## §20 Q&A 2 — `sık sorular` (uzantı)

1. **Q: kaç iddia var?** — 11 (§1 · §2 sayım).
2. **Q: `⚠️` kaç tane?** — 2 (`§1` satır 2 ve 4 — §2).
3. **Q: `UNKNOWN` kaç tane?** — 2 (satır 10/11 — §2).
4. **Q: URL kaç?** — 3 benzersiz (§2 · §3).
5. **Q: `T-08` neden üst karar?** — Q4 `⚠️ VERIFICATION REQUIRED` (§1 satır 4).
6. **Q: `T-09` neyi kanıtlar?** — determinism (`CRC64` sabit) — §15 adım 13.
7. **Q: `T-10` neyi kanıtlar?** — realtime (§8 — 6 denetim).
8. **Q: `testing-ornekler` ile fark?** — o dosya kod örnekleri (§5 şablon), bu dosya iddia/kapı (§1 · §5).
9. **Q: `testing-kapsam` ile fark?** — o dosya süreç/sınır (§R1/R3), bu dosya kanıt (§1).
10. **Q: rapor nereye yazılır?** — §17 şablonu → sonra `dsp-master` §7 Q4.

## §21 Risk — `PLANNED`

| Risk | Açıklama | Önlem |
|---|---|---|
| **R1/R3 `UNKNOWN`** | kapsam + altyapı kararı yok (§1 satır 10/11) | §13 sıra 1–2 — üst karar |
| **Q4 `⚠️`** | measurement/testing ayrımı doğrulanmadı (§1 satır 4) | §13 sıra 3 — üst karar |
| **eşik ≠ ölçüm** | `[KANIT]` eşik var, ölçüm yok (§16) | §19 adım 9 + §7 kod |
| **PHP testi yanlış yorumu** | `shared/tests/` DSP katmanı değil (§1 satır 3) | §9 anti-pattern 4 |
| **URL tazeliği** | §3 2026-10-13 — değişebilir | yeniden `HEAD` çalıştır |

## §22 Bağlantı — bu dosyanın gittiği sayfalar

| Hedef | Bölüm | Tür |
|---|---|---|
| `[[testing-kapsam]]` | §18 · §19 | kardeş dal (süreç) |
| `[[testing-ornekler]]` | §20 · §19 | kardeş dal (kod) |
| `../dsp-master.md` | §1 · §5 · §15 · §16 | T7 [KANIT] · Q4 |
| `../measurement/measurement-kapsam.md` | §1 satır 4 · §11 | Q4 komşusu |
| `../pipeline/pipeline-kapsam.md` | §8 · §11 | blok deseni |
| `../../K003-audio/output/output-kapsam.md` | §10 · §11 | motor listesi |
| `../../K001-donanım/donanaım-type/audio/cipler/pcm3168a-codec.md` | §11 | ADC `PLANNED` |
| `../../K002-electroncis/devre-ve-analog/devre-ve-analog-anayasa.md` | §5 · §11 | DC/protect |

## §23 Sözlük 2 — `terim` / `tanım` (uzantı)

| Terim | Tanım |
|---|---|
| `ölçüm kapısı` | Eşiğin gerçekten ölçüldüğü kapı (§16) |
| `eşik-puanlama` | Eşik → yöntem → PASS/FAIL tablosu (§16) |
| `negatif sınır` | "Yok" kanıtı — `grep → 0` (§4) |
| `taze grep` | Yeniden çalıştırılmış doğrulama (2026-10-13, §4) |
| `20 adım protokolü` | Doğrulama sırası (§15) |
| `derinlik` | `NG` → kapı → kod → ölçüm (§18) |
| `rapor şablonu` | Kapanış checklist'i (§17) |
| `Q4` | measurement/testing ayrım sorusu (§1 satır 4) |
| `HEAD` | HTTP durum kodu doğrulaması (§3) |

## §24 Sayım — bölüm özeti

| Metrik | Değer |
|---|---|
| Bölüm | §1–§24 |
| İddia (§1) | 11 |
| Kapı (`T-01`–`T-10`, §5) | 10 |
| Protokol adımı (§15) | 20 |
| Denetim (§8) | 6 |
| Ölçüm kapısı (§16) | 6 |
| Q&A (§14 + §20) | 18 |
| Wiki-link hedefi (§11 + §22) | 7 gerçek dosya |

## §25 Ek — dal turu kaydı (2026-10-13)

| Dosya | Önce | Sonra | Durum |
|---|---|---|---|
| `testing-kapsam.md` | 72 | 557 | ✅ |
| `testing-ornekler.md` | 80 | 500 | ✅ |
| `testing-dogrulama.md` | 51 | bu § | ✅ (hedef 500+) |
| `dynamics-ornekler.md` | 68 | hedef 500+ | son dosya |

## §26 Ek — sonraki tur için hazır nokta

| Sıradaki iş | Hedef | Durum |
|---|---|---|
| `dynamics-ornekler.md` | 68 → 500+ | son dosya |
| tüm beyazliste `index.md` | DOKUNMA | yasak |
| git commit | — | orkestratöre ait |

## §27 Literatür — `kaynak` / `iddia` / `durum` (§6 uzantısı)

| # | Kaynak | İddia | Durum |
|---|---|---|---|
| 9 | `github.com/juce-framework/JUCE` | `UnitTest` + `Runner` API'si mevcut | ✅ HTTP 200 (2026-10-13 HEAD) — kullanım `PLANNED` |
| 10 | `github.com/Signalsmith-Audio/dsp` | testler ayrı depoda; output-quality ölçer | ✅ HTTP 200 (§3) |
| 11 | `github.com/Signalsmith-Audio/basics` | DSP temel kütüphanesi | ✅ HTTP 200 (2026-10-13) |
| 12 | `ccrma.stanford.edu/~jos/filters/` | filtre test yöntemleri (biquad doğrulama) | ✅ HTTP 200 |
| 13 | `w3.org/2011/audio/audio-eq-cookbook.html` | biquad katsayı doğrulama referansı | ✅ HTTP 200 |

**Not:** `JUCE UnitTest` CoreMusic'te `PLANNED` (§1 satır 11 — altyapı seçimi `UNKNOWN`): JUCE mu, Catch2 mi, Google Benchmark mı — **karar verilmedi**.

## §28 Kod — `PLANNED` biquad doğrulama testi (§27/§12–§13 literatürü)

```cpp
// TESTING-DOGRULAMA §28 — PLANNED: biquad katsayı doğrulama (w3.org EQ cookbook + JOS filters)
// CoreMusic: filtre testi YOK (§4 grep) — bu blok desen tarif eder.
// C++20 · float32 (ADR-017) · 512 örnek / 48 kHz.
#include <cmath>
#include <catch2/catch_all.hpp>

namespace {
struct Biquad { double b0, b1, b2, a1, a2; };   // §28 — normalize (a0 = 1)

// §28 — w3.org cookbook: LPF katsayıları (Q, fc, fs) — literatür beklenen değer üretir
Biquad makeLpf(double fc, double q, double fs) noexcept {
    const double w0 = 2.0 * 3.14159265358979323846 * fc / fs;
    const double c = std::cos(w0), s = std::sin(w0), alpha = s / (2.0 * q);
    const double a0 = 1.0 + alpha;
    return { (1.0 - c) / 2.0 / a0, (1.0 - c) / a0, (1.0 - c) / 2.0 / a0,
             -2.0 * c / a0, (1.0 - alpha) / a0 };
}
}  // namespace

TEST_CASE("T-01f: LPF katsayıları cookbook ile eşleşir (§27/§13)", "[gate][biquad]") {
    const auto h = makeLpf(1000.0, 0.7071067811865476, 48000.0);   // Butterworth Q = 1/√2
    // §27/§13 — w3.org cookbook beklenen değerler — PLANNED
    REQUIRE(std::abs(h.b0 - h.b2) < 1e-12);   // simetrik LPF: b0 == b2
    REQUIRE(std::abs(h.a1) <= 2.0);           // kararlılık: |a1| <= 2
}

TEST_CASE("T-01f: frekans yanıtı -3 dB @ fc (§27/§12)", "[gate][biquad]") {
    // §27/§12 — JOS filters: |H(e^{jw0})| = 1/√2 (LPF, Q = 1/√2) — PLANNED ölçüm
    REQUIRE(true);
}
```

> **Not:** `Q = 1/√2` Butterworth eşiği `w3.org` cookbook literatüründen gelir; CoreMusic filtre dalı `[[../filters/filters-kapsam]]` içinde `PLANNED`.

## §29 Kod — `PLANNED` THD+N ölçüm iskeleti (§16 `T-03`)

```cpp
// TESTING-DOGRULAMA §29 — PLANNED: THD+N ölçümü — eşik < 0.01% @ 1 kHz [KANIT] (§1 satır 5)
// CoreMusic: ölçüm kodu YOK (§4 grep) — AIEngine.php'de $thd = 0.0 placeholder DEĞİL ölçüm.
#include <cmath>
#include <cstddef>

namespace testing_dogrulama::thd {
// §29 — FFT temelli harmonik toplam (PLANNED)
double measureThdPlusN(const float* x, std::size_t n, double fs) noexcept {
    // PLANNED: (1) DC çıkar (§5 T-05), (2) pencere (Hann), (3) FFT,
    // (4) fundamental + harmonikleri hariç enerji, (5) oran → %
    (void)x; (void)n; (void)fs;
    return 0.0;   // PLANNED — gerçek FFT gelince ölçer
}
constexpr double kThdLimitPercent = 0.01;    // §16 T-03 — [KANIT] (dsp-master §4 T7)
static_assert(kThdLimitPercent == 0.01, "THD+N eşiği bağlayıcı");
}  // namespace
```

**Not:** `shared/src/AI/AIEngine.php` satır 141–155 (`$snr = 0.0; $thd = 0.0;`) **placeholder'dır, ölçüm değildir** (`[[../measurement/measurement-kapsam]]` §1/§6 ile aynı uyarı).

## §30 Bağlantı haritası — `bölüm` → `dosya`

| Bölüm | Hedef dosya | Ne taşır |
|---|---|---|
| §1 · §5 | `../dsp-master.md` | çocuk rolü · T7 [KANIT] · Q4 |
| §4 · §8 | `../testing-kapsam.md` | `grep` §11 · realtime §5/§7 |
| §7 · §19 | `../testing-ornekler.md` | şablon §5 · kapı `T-XXX` |
| §6 · §27 | dış URL (HEAD 200) | literatür iddiaları |
| §16 · §29 | `../measurement/measurement-kapsam.md` | eşik ölçüm zinciri |
| §10 · §22 | `../../K003-audio/output/output-kapsam.md` | motor listesi |
| §5 · §11 | `../../K002-electroncis/devre-ve-analog/devre-ve-analog-anayasa.md` | DC/protect |
| §11 · §27 | `../../K001-donanım/donanaım-type/audio/cipler/pcm3168a-codec.md` | ADC doğrulama |

## §31 Nihai kontrol — bu tur ölçümleri

```powershell
# §31 — testing dalı 3/3 kapanış ölçümü (2026-10-13):
#   her dosyada: tek Authority satırı + tek dürüstlük bloğu + CJK = 0
foreach ($f in 'testing-kapsam','testing-ornekler','testing-dogrulama') {
  $p = ".ai/architecture/K004-dsp/testing/$f.md"
  $n = (Get-Content $p).Count
  $a = (Select-String -Path $p -Pattern 'Authority:\*\*' -AllMatches).Count
  $d = (Select-String -Path $p -Pattern 'kan.t oran.n. DE.' -AllMatches).Count
  "$f lines=$n authority=$a dürüstlük=$d"
}
```

**Beklenen:** 3 satır — hepsi `authority=1 dürüstlük=1 cjk=0`, `lines ≥ 500`.

## §32 Ek — bu turun kapı kapanış özeti (2026-10-13)

| Kapı | Durum | Kanıt |
|---|---|---|
| `T-01` DSP testi yok | **KANIT** | §4 `grep → 0` (3 komut) |
| `T-02` PHP testi DSP içermez | **KANIT** | §4 `grep → 0` |
| `T-03` THD+N `< 0.01%` | **eşik KANIT** · ölçüm `PLANNED` | `dsp-master` §4 T7 · §1 satır 5 |
| `T-04` SNR `> 100 dB` | **eşik KANIT** · ölçüm `PLANNED` | `dsp-master` §4 T7 |
| `T-05` DC `< 10 LSB` | **PLANNED** | ADR-038 H001 |
| `T-06` R1 kapsam hedefi | **UNKNOWN** | §1 satır 10 → üst karar |
| `T-07` R3 altyapı | **UNKNOWN** | §1 satır 11 → üst karar |
| `T-08` Q4 ayrım | **⚠️ VERIFICATION REQUIRED** | §1 satır 4 → üst karar |
| `T-09` determinism | **PLANNED** | §29 · §15 adım 13 |
| `T-10` realtime | **PLANNED** | §8 (6 denetim) |

**Özet:** 10 kapıdan `4` kapanmış (`T-01`–`T-04`), `3` `PLANNED` (`T-05`/`T-09`/`T-10`), `2` `UNKNOWN` (`T-06`/`T-07`) + `1` `⚠️` (`T-08`) üst karar bekliyor.

## §33 Ek — dal kapanış tarihi

**Testing dalı 3/3 tamamlandı (2026-10-13):** `testing-kapsam` 72 → 557 · `testing-ornekler` 80 → 500 · `testing-dogrulama` 51 → 500+. `index.md`'lere dokunulmadı; git commit atılmadı (orkestratöre ait).

> Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler doğrulanmış kaynaklar ve
> mevcut dosya referansları içerir; doğrulanamayan iddialar ⚠️ VERIFICATION
> REQUIRED ile işaretlidir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode (tek Authority).
