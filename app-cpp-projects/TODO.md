---
title: "app-cpp-projects — TODO Listesi"
type: todo
category: project-tracking
version: 1.2.0
status: active
authority: SSOT
updated: 2026-10-25
---

# app-cpp-projects — TODO Listesi

**Zorunlu Bağlantılar:** [[PRD.md]] · [[CONTEXT.md]] · [[../TODO]] · [[../PRD.md]] · [[../.ai/TODO.md]]

**Kural (Zero-Hallucination):** Her madde PRD §11 / disk kanıtına dayanır; kanıtlanamayan madde yazılmaz. Durum: `[ ]` açık · `[x]` kapalı.

**eli10:** C++/Neva projelerinin kalan işleri: taşıma kararı, build kanıtı, kararın PRD'lere işlenmesi.
**eli15:** `app-cpp-projects/` klasörü doküman-only; C++ kodu `--/neva-engine/`'dedir (commit `9fd9991`). `.ai/TODO.md` §2'deki "sınıflandır (163 dosya)" maddesi ile bu dosya eşgüdümlüdür — kod taşındığı için madde yeniden ölçülmeli.

## Açık Maddeler (PRD §11 — 24–48h)

- [ ] **Taşıma TODO'su kapatma kararı** — `.ai/PLAN.md:330` ve mimari §19 "kullanıcı onayı ⏳" → kapat/karar ver (kullanıcı) · Kaynak: `[[PRD.md]]` §11.1
- [ ] **Build/test kanıtı** — `cmake` + `ctest` çalıştırılıp sonuç kaydedilir · Kaynak: `[[PRD.md]]` §11.2
- [ ] **Karar sonucunun işlenmesi** — bu PRD §5 + kök `[[../PRD.md]]`'ye yansıtılır · Kaynak: `[[PRD.md]]` §11.3

## İlişkili Vault Maddesi

- `[[../.ai/TODO.md]]` §2 "`app-cpp-projects/` sınıflandır" — kod `--/neva-engine/`'e taşınmış (`9fd9991`) → madde yeniden ölçülüp kapatılabilir · Kaynak: `git log` + `ls -R` 2026-10-23

## TAM Madde Listesi — Küçükten Büyüğe (Geçmiş · Açık · Gelecek) — 2026-10-25

> Tek doğruluk listesi; `## Geçmiş & Mikro Ölçek` kanıt detayıdır. Sıra: MICRO → ORTA → EPIC.

### A. GEÇMİŞ — TAMAMLANAN
- [x] MICRO: ADR-0105 native media stack + 112 kaynaklı web doğrulama (soxr LGPL, ASIO dual-license) — `8ecb59c`
- [x] ORTA: C++ kodu `--/neva-engine/`'e taşındı — `9fd9991`
- [x] ORTA: `app-cpp-projects/` sınıflandırma (5 md tracked) — 2026-10-24 · `a44c567`
- [x] ORTA: Neva PRD'ler **K-6 ONAY** (8004+8006 satır · 182+215 gereksinim ↔ TC) — 2026-10-23
- [x] ORTA: RE turları tur5a-5e + tur6/9/13 — FxSound kompresör envelope kapandı (doc 04 v1.4.5) · VRAM 3. denetim (GTX 1660/6144MB)
- [x] EPIC: ADR defteri 101 karar (next 112) + M0 PRD kapıları (§20.1) + legacy 23 dosya arşivi

### B. AÇİK — EKSİK / YARIM (küçükten büyüğe)
- [ ] MICRO: C-2a — `cmake -S . -B build` configure → 0/1 hata
- [ ] MICRO: C-1a — `.ai/PLAN.md` L330 satırını oku → tam metni bu dosyaya yapıştır
- [ ] MICRO: C-4a — Neva PRD §20.1 K-1/K-5 kapı tanımlarını oku → 3 madde
- [ ] MICRO: `--/` dizini hâlâ var mı → `Test-Path` (`3e3ed57` yalnız kopyayı sildi, kod `9fd9991`)
- [ ] ORTA: C-2 **Build/test kanıtı** (`cmake --build` + `ctest` = 0 fail) → PRD §11.2
- [ ] ORTA: C-1 **Taşıma TODO kararı** (`.ai/PLAN.md:330` + mimari §19 "kullanıcı onayı ⏳")
- [ ] ORTA: C-3 Karar sonucu → bu PRD §5 + kök `PRD.md` §5.1
- [ ] ORTA: `.ai/TODO` açık maddeleri: Neva **M0** (K-1+K-5) · Player **T-P01/T-P02/T-P03** (M4'ü bloke)
- [ ] EPIC: C-4 **Neva M0 açılışı** → engine ilk `.cpp` (benchmark kapısı ADR-0105 §5.6 geçmeden IMPLEMENTED YOK)

### C. GELECEK — SIRADAKİ BÜYÜK İŞLER (öncelik sırası)
| Sıra | EPIC iş | İlk MICRO adım |
|---|---|---|
| 1 | C-2 build gate (cmake+ctest) | configure komutu |
| 2 | C-1/C-3 taşıma kararı kapanışı | PLAN L330 okuma |
| 3 | C-4 M0 kapıları (K-1 + K-5) | PRD §20.1 okuma |
| 4 | Engine ilk kod + benchmark kapısı | ADR-0105 §5.6 |## Adım Adım Yürütme (2026-10-25 güçlendirme)

**Kural:** Her adım tek oturum · `[ ]` → çalış · `[x]` + kanıt → kapanır · **YAPMA** bağlayıcı · komutu gönder → agent yapar.

| # | Adım | Ne yapılacak (kaynak) | YAPMA | Hazır komut |
|---|---|---|---|---|
| C-1 | [ ] | **Taşıma kararı** — `.ai/PLAN.md:330` + mimari §19 "kullanıcı onayı ⏳" → kapat/karar ver | onaysız kod taşıma/üretme (`app-cpp-projects/` doküman-only kalır) | `C-1: .ai/PLAN.md:330 taşıma TODO'sunu karara bağla — kod --/neva-engine'de mi kalıyor, kararı yaz` |
| C-2 | [ ] | **Build kanıtı** — `--/neva-engine` altında `cmake configure + build + ctest` = 0 hata | kanıtsız "derleniyor" | `C-2: cmake -S . -B build && cmake --build build && ctest --test-dir build — çıktıları PRD §11.2'ye yaz` |
| C-3 | [ ] | Karar sonucunun işlenmesi — bu PRD §5 + kök `PRD.md` §5.1 | çift kayıt/çelişki | `C-3: C-1 kararını app-cpp/PRD §5 + kök PRD §5.1'e işle` |
| C-4 | [ ] | **Neva M0 kapısı** — PRD'ler K-6 onaylı ama M0 = K-1 (architect) + K-5 (ürün sahibi) kapıları | benchmark/kapı olmadan IMPLEMENTED etiketi | `C-4: Neva M0 için K-1 + K-5 kapılarını başlat — K-6 onaylı PRD'leri referans ver` |

**Yüzey yasakları:** onaysız kod taşıma · WASAPI dışı backend (ADR'siz) · RT callback'te `new`/`lock`/`throw` · ölçümü uydurma · frozen ADR · commit.
## Geçmiş & Mikro Ölçek — Tam Liste (2026-10-25)

**Geçmiş — tamamlanan (kanıtlı):**
- [x] `[ORTA]` C++ kodu `--/neva-engine/`'e taşındı — `9fd9991`
- [x] `[ORTA]` `app-cpp-projects/` sınıflandırması kapandı (5 md tracked) — `.ai/TODO` §2, 2026-10-24 (`a44c567`)
- [x] `[ORTA]` Neva PRD'ler K-6 ONAY (8004/8006 satır, 182+215 gereksinim) — 2026-10-23
- [x] `[MİKRO]` ADR-0105 native media stack accepted + 112 kaynaklı web doğrulama — `8ecb59c` tur9

**Mikro işler (en küçük → büyük):**
- [ ] `[MİKRO]` C-2 altı: `cmake -S . -B build` tek komut → configure hatası var mı (0/1)
- [ ] `[MİKRO]` C-1 altı: `.ai/PLAN.md` L330 satırını oku → tam metni bu dosyaya yapıştır
- [ ] `[MİKRO]` C-4 altı: K-1/K-5 kapı tanımlarını Neva PRD §20.1'den oku → ne istiyor 3 madde
- [ ] `[ORTA]` C-2 build yeşil → C-1/C-3 kararı → `[EPİK]` C-4 M0 → engine ilk .cpp## Referanslar

- `[[PRD.md]]` (§11 Atılacak Adımlar) · `[[CONTEXT.md]]` · `[[AGENTS.md]]` · `[[CLAUDE.md]]`
- `.ai/projects/NevaEngine/coremusic-neva-engine-prd.md` (SSOT) · `.ai/PLAN.md:330` · `[[../PRD.md]]` §5.1
- Aile indeksi: `[[../TODO]]` · Vault TODO: `[[../.ai/TODO.md]]`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
