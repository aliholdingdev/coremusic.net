# Medya Arşivi — Yapılacaklar (todos)

> **Kapsam:** `media.coremusic.net/` · Faz 2 doğrulama kapıları + Faz 2b/3 işleri · **Tarih:** 2026-09-29
> **Eş:** [`checklist.md`](./checklist.md) (kanıt/denetim listesi — her todo buradaki bir açık maddeyi kapatır)
> **Sütunlar:** `Öncelik` · `Görev` · `Sahip` · `Tahmin` · `Bağımlılık` · `Kapı koşulu` (kapı kapandığında checklist'teki `- [ ]` → `- [x]` olur)
> **Sıra:** P0 → P1 → P2. P0'lar kapanmadan P2 işlerine başlanmaz (kapı koşulları buna bağlı).

---

## P0 — doğrulama kapıları (Faz 2'yi "tamam" yapmak için)

- [ ] **1 · PHP 8.4 ortamında CLI denetimi:** `php -l` 8 dosya (`composer.json:29-38` lint listesi) 0 hata + `php bin/audit.php` → exit 0 + `php bin/scan.php --dry-run` → exit 0 · **Sahip:** backend-architect · **Tahmin:** ~15 dk · **Bağımlılık:** PHP 8.4 kurulu makine · **Kapı:** 3 komut da exit 0 → `checklist.md` **VR-2** kapanır (VR-4 için gerçek medya gerekir, bu kapı tek başına VR-4'ü kapatmaz)
- [ ] **2 · MySQL 9'da DDL:** `mysql -u<user> -p media_catalog < .ai/.sql/mysql/media_catalog.sql` + `SELECT COUNT(*) FROM taxonomy;` = **98** + `SHOW FULL TABLES;` → 9 tablo + `v_asset_search` · **Sahip:** data-engineer · **Tahmin:** ~10 dk · **Bağımlılık:** MySQL 9 erişimi · **Kapı:** 98 ve 9+1 → `checklist.md` **VR-3** kapanır
- [ ] **3 · Kaynak makinede dosya sayımı:** `Get-ChildItem 'C:\Users\Bayram Ali\Music' -Recurse -File | Measure-Object` → **7.551** + `... | Measure-Object -Property Length -Sum` → **38,33 GB** · **Sahip:** verinin olduğu PC'de çalışan · **Tahmin:** ~2 dk · **Bağımlılık:** veri erişimi · **Kapı:** iki sayı da tutarsa `checklist.md` **VR-1** kapanır
- [x] **4 · F2.4 FAIL yeniden statik denetim (salt statik, PHP gerekmez) — TAMAM (2026-09-29):** qa-engineer **8 PASS / 0 FAIL** · `hash_file` yalnız `scan.php:155` (deep dalı `:154`) + `audit.php:398` (`:397`); yasaklı çağrı = 0; `copy(` = 1 (`ingest.php:328`); kapı koşulu sağlandı → `checklist.md` "FAIL düzeltmesi" maddesi `- [x]` oldu · Orijinal tanım: madde 8/14 `grep -n "hash_file" bin/scan.php` → gerçek çağrı yalnız `:155` (deep dalı) + madde 6 yasaklı çağrı grep = 0 + `copy(` grep = 1 (`ingest.php:328`) · **Sahip:** qa-engineer · **Tahmin:** ~10 dk · **Bağımlılık:** yok madde 8/14 `grep -n "hash_file" bin/scan.php` → gerçek çağrı yalnız `:155` (deep dalı) + madde 6 yasaklı çağrı grep = 0 + `copy(` grep = 1 (`ingest.php:328`) · **Sahip:** qa-engineer · **Tahmin:** ~10 dk · **Bağımlılık:** yok · **Kapı:** 3 grep de ihlalsiz → `checklist.md` "FAIL düzeltmesi yeniden teyit" maddesi kapanır

## P1 — kararlar (onay bekleyen)

- [ ] **5 · Breakpoint seti kararı:** k11-ux (576/768/992/1200/1400) mü, spec §6'daki (640/768/1024/1440) mı? → kazanan set spec `:402-405` tablosuna + responsive token dosyasına yazılır · **Sahip:** sen (onay) → ui-designer · **Tahmin:** 2 dk karar + ~15 dk hizalama · **Bağımlılık:** yok · **Kapı:** tek set + `:409` `⚠️` satırı kaldırılırsa `checklist.md` **VR-5** kapanır
- [ ] **6 · Port rolleri netleştirme:** ADR-039'daki `:5000` / `:6000` (router vs FFmpeg) ayrımı yazılır → spec §7 veri akışı port tablosu hizalanır · **Sahip:** vault-updater · **Tahmin:** ~15 dk · **Bağımlılık:** yok · **Kapı:** ADR-039'da tek satırlık net kayıt + spec §7 uyumu → `checklist.md` **VR-7** kapanır
- [ ] **7 · `.ai/ui-design/` serbestleşince faz3-gui-spec → Kalıp D screen-spec:** `.ai/.templates/ui-design/screen-spec-template.md` (Guardrail #16 zorunlu okuma) okunur, 8 ekran tek tek üretilir · **Sahip:** frontend-developer · **Tahmin:** ~1 sa · **Bağımlılık:** ui-design yazım kuyruğunun boşalması (Faz 7/8) · **Kapı:** 8 ekran dosyası + `screens-frontmatter-check` repo kökünden `21 ekran / 0 sorunlu` korunmalı → `checklist.md` "Asıl screen-spec" maddesi + **VR-6** (envanter eşlemesiyle birlikte) kapanır

## P2 — Faz 2b / Faz 3 işleri (onay bekliyor)

- [ ] **8 · `ingest.php --commit` canlı kopya akışı:** önce `--dry-run` CSV çıktısı onayı, sonra `--commit` ile kopya + `path_history` yazımı (`path_history` tablosu `media_catalog.sql:383`) · **Sahip:** backend-architect · **Tahmin:** ~1 sa · **Bağımlılık:** P0-1 kapalı · **Kapı:** kaynak veri erişimi (P0-3) + dry-run CSV'de 0 hata
- [ ] **9 · `public/` web iskeleti (router + API):** `/api/assets`, `/api/filters`, `/api/audit`, `/api/ingest/review` · **Sahip:** backend-architect · **Tahmin:** ~2 sa · **Bağımlılık:** P1-7 + P0-1 · **Kapı:** 4 endpoint openapi benzeri yanıt örneği + audit 0 hata
- [ ] **10 · MySQL gerçek indeksleme:** `scan.php` ilk koşusunun `media_catalog`'a yazımı + okuma yollarının indeksle karşılanması · **Sahip:** data-engineer · **Tahmin:** ~30 dk · **Bağımlılık:** P0-2 · **Kapı:** `EXPLAIN` ile sekiziz kullanımda `type=ref/eq_ref`, `SELECT *` yok
- [ ] **11 · Gerçek medyada audit 0 hata hedefi (ADR §7.1 k.4) + `--deep` örneği** · **Sahip:** qa-engineer · **Tahmin:** ~45 dk · **Bağımlılık:** ingest (8) · **Kapı:** `php bin/audit.php` → 0 hata + `--deep` ile kasıtlı 1 hash sapmasının `HASH-FARK` olarak raporlanması (VR-4 burada kapanır)
- [ ] **12 · ADR-092 §5.2 adım 4/5/7 + GUI adımları hâlâ ⏳** → uygulandıkça vault-updater ile işaretlenir · **Sahip:** vault-updater · **Tahmin:** sürekli · **Bağımlılık:** adım sahipleri · **Kapı:** adım `⏳` → `✅` + log append (append-only, tek satır)

---

## Bakım notları (checkbox değil — süreç kuralları)

- `log.md` içinde başka oturumların da commit'siz satırları var (append-only; bilerek tek dosya commit edilmedi) → toplu temizlik **ayrı onay** ister.
- Repoda eşzamanlı başka oturum çalışıyor (figma/ui-design) → push öncesi `git status --short` iki oturum izini birden kontrol et.
- Büyük pakette push 408/reset veriyor → reçete: `git config http.version HTTP/1.1` + `-c http.postBuffer=524288000` (447 MB bu kombinasyonla geçti, 2026-09-29).
