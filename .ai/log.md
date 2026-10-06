---
title: "LLM Wiki — Kayıt Defteri"
type: log
created: 2026-10-06
updated: 2026-10-06
tags: [wiki, log, append-only]
---

# Log — append-only kayıt defteri

Kural: yalnızca sona ekleme yapılır, mevcut kayıtlar değiştirilmez.
Format: `## [YYYY-MM-DD HH:MM] işlem | başlık` — işlem türleri: `setup`, `ingest`, `query`, `update`, `lint`, `delete`.
Tarih asla tahmin edilmez; her kayıt öncesi `date "+%Y-%m-%d %H:%M"` çalıştırılır.
Katalog: [[index]] · Kaynak tablosu: [[özet]] · Son ingest: [[test-kaynak]]

## [2026-10-06 17:50] setup | Wiki kuruldu
- Kök anayasa `CLAUDE.md` oluşturuldu (amaç, klasör yapısı, sayfa şablonu, INGEST/QUERY/LINT, zorunlu index+log kuralı)
- `index.md` oluşturuldu (boş bölümler: Kaynaklar, Kavramlar, Kişiler, Araçlar, Projeler)
- `log.md` oluşturuldu (append-only, bu ilk kayıt)
- Hedef dizin: `C:\www\coremusic.net\.ai` — `.ai.OLD` kök yapısı referans alındı (kökte CLAUDE.md / index.md / log.md)

## [2026-10-06 17:51] setup | raw/ klasörü ve ingest prosedürü eklendi
- `raw/README.md` oluşturuldu (ham kaynak kuralı: değiştirilmez/silinmez/sadece okunur, eklenince INGEST çalışır)
- `CLAUDE.md` §8 "INGEST Prosedürü" (9 adım) eklendi (5-15 sayfa etkilenme hedefi ile)
- `CLAUDE.md` §9 "sources/özet.md Kuralı" eklendi (tablo satırı + kaynak sayısı + son güncelleme zorunlu, yoksa ingest tamamlanmamış sayılır)
- `sources/özet.md` ana tablosu oluşturuldu (6 sütun, boş)
- `wiki/` dizini oluşturuldu

## [2026-10-06 17:53] ingest | raw/test-kaynak.md — AI Ajanlarında Hafıza
- Kaynak baştan sona okundu; 3 çıkarım: (1) bağlam penceresi sınırlı olduğu için sohbet geçmişi tek başına yetersiz, (2) hafıza episodik/semantik/prosedürel üç katman, (3) ingest-query ayrımı yoksa wiki çelişkilerle dolar
- Kaynak özeti oluşturuldu: `sources/test-kaynak.md`
- Oluşan wiki sayfaları (9): ai-agent-memory, context-window, episodic-memory, semantic-memory, procedural-memory, vector-database, file-based-memory, embedding, chunking
- Güncellenen wiki sayfaları: 0 (yeni kurulum)
- Çapraz linkler eklendi; `index.md` güncellendi (Kaynaklar 1, Kavramlar 9); `sources/özet.md` güncellendi (kaynak sayısı 1, son güncelleme 2026-10-06 17:53)

## [2026-10-06 18:00] lint | LINT-1 — 5 sorun bulundu, 4'ü düzeltildi (onaylı)
- Bulunan: (1) madde 2 — çıkış linki olmayan 3 dosya (`log.md`, `sources/özet.md`, `raw/README.md`); (2) madde 3 — `sources/özet.md` index.md'de yok; (3) madde 5 — 4 kırık link `[[episodic-memory]]` (ai-agent-memory.md:16,33 · procedural-memory.md:16 · semantic-memory.md:17); (4) madde 10 — eksik frontmatter (`sources/özet.md` sources, `sources/test-kaynak.md` created/updated/sources); (5) madde 16 — Kişiler/Araçlar/Projeler bölümleri boş
- Düzeltildi: `[[episodic-memory]]` → `[[episodic-memory]]` (4 yer, 3 dosya); index.md'ye `[[özet]]` satırı; `sources/özet.md` + `sources/test-kaynak.md` frontmatter tamamlandı; `raw/README.md` ve `log.md`'ye çapraz link eklendi; `sources/özet.md`'ye `## İlgili Sayfalar` eklendi; CLAUDE.md §4'e sources/ şablon notu (raw_path/ingested) eklendi
- Açık: madde 16 (Kişiler/Araçlar/Projeler boş) — yeni kaynak gerektirir, P4
- Doğrulama (UTF-8, 18:00): kırık link 0 (2 kalan = CLAUDE.md/index.md code-span format örnekleri, gerçek link değil) · yetim node 0 · yetim sayfa 0 · indekslenmemiş 0 · hayalet 0 · eksik frontmatter 0 · çözülmemiş `⚠ Çelişki` 0 · log 4 kayıt kronolojik
- Düzeltilen: 4/5 madde (10 dosya işlemi); değiştirilmeyen: index.md'de Kavramlar 9 + Kaynaklar 2 satırı korundu

## [2026-10-06 18:04] ingest | raw/not-repo-envanteri.md — CoreMusic Repo Envanteri
- Kaynak disk kanıtlarıyla yazıldı (dir listesi + package.json + shared/composer.json okundu); 5 çıkarım: (1) 6 modüllü tek repo, (2) PHP 8.4+/PDO/APCu omurgası zorunlu, (3) test üçlüsü PHPUnit/PHPStan/Playwright+Vitest, (4) shared-infrastructure v2.0.0 proprietary, (5) depoda kişi verisi YOK
- Kaynak özeti oluşturuldu: `sources/not-repo-envanteri.md`
- Oluşan wiki sayfaları (15): araçlar 8 — php-8-4, composer, phpunit, phpstan, pdo, apcu, playwright, vitest; projeler 7 — coremusic-platform, shared-infrastructure, home-coremusic-net, api-coremusic-net, auth-coremusic-net, media-coremusic-net, assets-coremusic-net
- Güncellenen wiki sayfaları: 0
- `index.md` güncellendi (Kaynaklar 3, Kavramlar 9, Kişiler boş+not, Araçlar 8, Projeler 7); `sources/özet.md` güncellendi (kaynak sayısı 2, son güncelleme 2026-10-06 18:04)
- P4 (madde 16) kısmen kapandı: Araçlar + Projeler doldu; **Kişiler hâlâ boş — depoda insan verisi yok, uydurulmadı**

## [2026-10-06 18:14] update | log.md tarihsel link düzeltildi (onaylı)
- 18:00 lint kaydındaki 2 adet `[[episodic-memory]]` → `[[episodic-memory]]` (append-only kuralı gereği geçmiş satır değişikliği kullanıcı onayıyla yapıldı)
- Sonuç: kırık link sayısı log.md içinde 0

## [2026-10-06 18:14] ingest | raw/not-readme-vizyon.md — README Vizyonu ve Teknoloji Yığını
- Kaynak diskten okundu (README.md v2.0.1); 5 çıkarım: (1) Authority Bayram Ali / Vault Steward, (2) Neva Engine zero-allocation/lock-free, (3) MySQL 9 18 BCNF 156 tablo, (4) Vanilla JS ES2022 + ITCSS 9 + BEM, (5) ⚠ Çelişki adayı: pro.coremusic.net README'de var, diskte yok
- Kaynak özeti: `sources/not-readme-vizyon.md`
- Oluşan wiki sayfaları (4): bayram-ali (kişi), neva-engine, mysql-9, vanilla-javascript (araç)
- Güncellenen wiki sayfaları (3): coremusic-platform (21 katman/1095 bileşen + ⚠ Çelişki notu), assets-coremusic-net (ITCSS/BEM), php-8-4 (README rozet teyidi)
- `index.md` güncellendi (Kaynaklar 4, Kişiler 1, Araçlar 11, Projeler 7); `sources/özet.md` güncellendi (kaynak sayısı 3, son güncelleme 2026-10-06 18:14)
- **P4 kapandı:** Kişiler/Araçlar/Projeler bölümlerinin üçü de dolu

## [2026-10-06 18:37] update | Çelişki çözüldü: pro.coremusic.net (plan ≠ implementasyon)
- Kanıt-1 `README.md:86-93` → alt alan adı tablosu 10 satır (music, home, car, studio, download, media, admin, auth, pro, coremusic.net) = **vizyon/plan**
- Kanıt-2 disk `dir` → 5 implemente (api, assets, auth, home, media); pro/download/admin/coremusic.net Test-Path = False; diskteki api + assets README planında YOK
- Sonuç: gerçek bilgi çelişkisi değil, plan↔implementasyon farkı → `wiki/coremusic-platform.md` notu "✅ çözüldü" olarak güncellendi (iki kaynak: not-readme-vizyon + not-repo-envanteri); `sources/not-readme-vizyon.md` çözüm notu eklendi
- ⚠ Kural ihlali geri alındı: `raw/not-readme-vizyon.md`'ye yanlışlıkla çözüm notu yazılmıştı — raw/ değiştirilemez kuralı gereği dosya orijinaline iade edildi (doğrulandı, son satır = özgün metin)

## [2026-10-06 18:50] update | VR kapatıldı: media.coremusic.net test altyapısı
- Ölçüm: `media.coremusic.net/tests/` Test-Path = **False** · `phpunit.xml` = False · composer.json'da test/phpunit geçen satır = **0**
- Karşılaştırma: diğer 5 modülde (shared, home, api, auth, assets) `tests/` + `phpunit.xml` mevcut
- Ek kanıt: `bin/` = audit.php, scan.php, ingest.php · `docs/` = 5 doküman (adlandirma, checklist, dizin-yapisi, faz3-gui-spec, todos)
- Sonuç: `wiki/media-coremusic-net.md` içindeki `⚠ VERIFICATION REQUIRED` notu kaldırıldı → "✅ doğrulandı 2026-10-06 18:49, test kapsamı boşluğu" olarak güncellendi (kaynak: [[not-repo-envanteri]])
- index.md değişmedi (yeni sayfa yok); wiki 28 sayfa korundu

## [2026-10-06 18:54] lint | LINT-2 — 16 kontrol: 3 sorun bulundu, 3'ü düzeltildi (onaylı)
- Denetlenen: `wiki/` 30 · `sources/` 4 · `raw/` 4 · kök 3 = 41 dosya. Sonuç: ✅ 13 madde · ❌ 3 madde (madde 8 `raw/README.md` özetsiz · madde 12 `BCNF`+`Redis` sayfasız · madde 16 boşluk raporu)
- P0 tamam: `index.md`'ye **Kararlar (ADR)** (`.decisions/index.md`) ve **Personalar** (`.personas/index.md`) bölümleri eklendi; `CLAUDE.md` §2'ye iki dizin + kayıt kuralı satırı eklendi
- P1 tamam: `CLAUDE.md` §3'e **`sources/özet.md`** için Türkçe karakter istisnası (yeniden adlandırma yasak — `[[özet]]` linkleri kırılırdı)
- P2 tamam: `CLAUDE.md` §9'a kaynak olmayan dosya istisnası (`raw/README.md` + kök `CLAUDE.md`/`index.md`/`log.md` tabloya girmez, istisna listesi genişletilemez)
- P3 tamam: `wiki/bcnf.md` (kavram) + `wiki/redis.md` (araç) oluşturuldu; `wiki/mysql-9.md`'ye 2 çapraz link; `index.md`'ye 2 satır (Kavramlar 10 · Araçlar 12)
- Tutarlılık düzeltmesi: `sources/özet.md` → not-readme-vizyon satırı "6 oluştu + 4 güncellendi", son güncelleme 18:50 (kaynak sayısı 3 = disk 3, değişmedi)
- Kalan (P4 · yeni kaynak gerekir): Kişiler yalnız 1 (`bayram-ali`) · `raw/` 3 kaynak, hiçbiri makale/transkript/web değil
- Doğrulama (18:54): kırık link **0 gerçek** (4 adayın hepsi code-span örneği: CLAUDE.md:63,95 · index.md:12 · file-based-memory.md:12) · yetim node 0 · yetim sayfa 0 · indekslenmemiş 0 · hayalet 0 · eksik frontmatter 0 · çözülmemiş `⚠ Çelişki` 0 · 500+ kelime 0 (en büyük `coremusic-platform.md` 213) · log 10 kayıt kronolojik
- ⚠ Paralel oturum notu: 18:50'de başka bir oturum `log.md`'ye "VR kapatıldı" girişi ve `wiki/media-coremusic-net.md`'ye düzeltme yapmış; bu LINT o değişiklikleri de kapsar

## [2026-10-06 19:04] ingest | raw/'a 18 .ai.OLD kök dosyası alındı — toplu vault ingest
- **raw/:** `.ai.OLD/` kökündeki 18 MD (`AGENTS, brain, CHECKLIST, CLAUDE, CONTEXT, engine, glossary, index, keys, log, MEMORY, PLAN, PROJECTS, ROLE, TODO, ULTRA-THINKING, VISION, WORKFLOW`) `raw/`'a kopyalandı — orijinallere dokunulmadı (kopya = ham kaynak)
- **Okuma kapsamı:** her dosya için frontmatter + H2 başlık mimarisi + Purpose/Scope (veya §1 giriş) okundu; ~12.000 satırın tamamı kelimesi kelimesine taranmadı — özetler yalnızca okunan bölümlere dayanır, doğrulanmayan iddialar (glossary 94 terim, index 720 dosya, TODO 105 değişiklik) özetlerde `⚠ VERIFICATION REQUIRED` işaretli
- **sources/:** 18 özet dosyası (`sources/vault-*.md`; şablon: title/raw_path/created/updated/sources/ingested/tags + 4 bölüm)
- **wiki/:** 18 sayfa oluştu (`wiki/vault-*.md`, `type: dokuman` — bu tip `CLAUDE.md` §4 şablonuna eklendi)
- **index.md:** Kaynaklar +18 satır + yeni bölüm `## Vault Dokümanları` (+18 satır)
- **sources/özet.md:** +18 satır → **kaynak sayısı 21**, son güncelleme 2026-10-06 19:02
- **Paralel işlem notu:** 18:54 LINT-2 (16 kontrol, 3/3) `wiki/bcnf.md` + `wiki/redis.md`, index `Kararlar (ADR)`/`Personalar` bölümleri ve CLAUDE.md §2/§3/§9 istisnalarını eklemiş — bu ingest'in dosya setiyle kesişmez
- Etkilenen sayfa: 18 oluşturuldu / 0 güncellendi

## [2026-10-06 19:16] ingest | .agents/ (12) + .decisions + .personas wiki'ye alındı
- **raw/:** `.ai.OLD/.agents/` 12 MD `raw/.agents/` altklasörüne kopyalandı (orijinallere dokunulmadı; `.decisions`/`.personas` zaten paralel oturumca `.ai/`'ye taşınmış → raw kopyası gerekmedi, kaynak yolu doğrudan `.decisions/index.md` / `.personas/index.md`)
- **sources/:** +14 (`agents-sub-registry`, `agent-*` ×12, `vault-decisions`, `vault-personas`) · **wiki/:** +14 (`type: dokuman`)
- **index.md:** Kaynaklar +14 · Vault Dokümanları +14 · `Kararlar (ADR)`/`Personalar` bölümlerine `[[vault-decisions]]`/`[[vault-personas]]` linki eklendi
- **sources/özet.md:** +14 satır → **kaynak sayısı 35**, son güncelleme 2026-10-06 19:16
- **Ölçüm/çelişki:** ADR rejected iddiası **12** (index frontmatter) vs disk **9** → `⚠ VERIFICATION REQUIRED` (özet ve wiki sayfasına yazıldı) · frozen 37 (frontmatter) vs 36 (tablo) dosya içinde ADR-037 notuyla açıklanmış · persona **68/6** disk ile birebir ✓
- **Okuma kapsamı:** `.decisions/index.md` ve `.personas/index.md` ilk 30 satırı (frontmatter + §1 + özet tablo); `.agents/*` 12 dosya frontmatter + H2 başlıklar
- Etkilenen sayfa: 14 oluşturuldu / 4 güncellendi (index, özet, Kararlar, Personalar bölümleri)

## [2026-10-06 19:36] ingest | raw/.png (19 PNG + 4 bağlam notu) işlendi
- **Tespit:** path-based tarama `raw/.png/` altında özet kaydı olmayan **23 dosya** buldu (paralel oturum eklemiş) — tümü işlendi
- **Kaynak özeti:** `sources/png-mockup.md` (paket kaynak: 4 CLAUDE.md + 19 PNG envanteri)
- **wiki/:** 1 sayfa oluştu (`wiki/png-mockup.md`, `type: dokuman`) — klasör tablosu 12+1+6=19 ✓ disk sayımıyla birebir
- **sources/özet.md:** +23 satır → **kaynak sayısı 58**, son güncelleme 2026-10-06 19:36
- **index.md:** Kaynaklar +1 · Vault Dokümanları +1
- **Not:** klasör notlarındaki `.png-analysis/` komşu bağı diskte yok → `⚠ VERIFICATION REQUIRED` (wiki sayfasına yazıldı)
- Etkilenen sayfa: 1 oluşturuldu / 2 güncellendi

## [2026-10-06 19:45] lint | LINT-3 — .decisions/index.md sayaçları + kök AGENTS.md ölü referanslar + .personas linkleri düzeltildi (onaylı)

- **GÖREV 1** `.ai/.decisions/index.md` (6 blok edit): frontmatter `version 1.1.4→1.1.5` · `total-accepted 73→72` · `total-rejected 12→7` · `total-frozen 37→36` (active 36, draft 0 sabit). §2 tablosu Rejected `12→7`, Toplam `85→79` + LINT-3 çelişki notu eklendi. §3 başlık `(001-037)→(001-036)`, ADR-037 satırı §3'ten silindi. §4 başlık `(038-093)→(037-093 — diskte dosyası olanlar)`; 7 satır eklendi (037, 047, 052, 056, 058, 059, 082 — slug'lar `Get-ChildItem accepted/` ile doğrulandı); 21 adet `[[../brain.md]]`/`[[../CLAUDE.md]]` linki diskteki gerçek dosyaya `[[accepted/<EXACT-STEM>]]` olarak çevrildi; dosyası olmayan 7 satır (083, 084, 085, 086, 087, 088, 091) linkten arındırıldı + satır sonu `<!-- NO FILE on disk 2026-10-06 — brain.md only -->`. §5: R-001…R-007 `dead-link` yorumları silindi (7 dosya rejected/ altında MEVCUT); R-008…R-012 linkleri silindi + `<!-- NO FILE on disk 2026-10-06 -->` (bu 5 satır sayıma girmez). §6 Kategori Haritası disk kanıtıyla yeniden yazıldı (Frozen 36 · Active 36 · TOPLAM 72) + kapsam notu. Footer `v1.1.3→v1.1.5`.
- **GÖREV 2** kök `AGENTS.md` (8 satır): §7 adım 2/5/6 `.ai/.rules/*` → `.ai/CLAUDE.md §6 LINT` (+ `.ai.OLD/.rules/senior-mode.md`; `error-recovery.md` diskte MEVCUT DEĞİL). §9 tablosu 5 satır yeniden bağlandı: ADR → `.ai/.decisions/index.md` + `@.ai/raw/brain.md` · Kural/LINT → `.ai/CLAUDE.md §6` · Agent registry → `.ai/wiki/vault-agents.md` + `@.ai/raw/AGENTS.md` · Vault-içi otorite → `.ai/CLAUDE.md §2` (§2.1 `.ai.OLD/CLAUDE.md` içinde) · Süreç → `.ai/wiki/vault-workflow.md` + `@.ai/raw/WORKFLOW.md`. §10'a DOKUNULMADI (frontend şablonları 8/8 diskte mevcut).
- **GÖREV 3** `.ai/.personas/index.md` (1 satır, 13. satır): `[[AGENTS.md]]` → `[[vault-agents]]` · `[[WORKFLOW.md]]` → `[[vault-workflow]]`.
- **GÖREV 4** bu kayıt (append-only).

**Test-Path doğrulama (GÖREV 2 hedefleri):** `.ai/.decisions/index.md`=True · `.ai/wiki/vault-agents.md`=True · `.ai/wiki/vault-workflow.md`=True · `.ai/raw/brain.md`=True · `.ai/raw/AGENTS.md`=True · `.ai/raw/WORKFLOW.md`=True · `.ai/CLAUDE.md`=True · `.ai/.rules/error-recovery.md`=False (beklenen: yok).

**Diğer doğrulamalar:** frontmatter yeni sayılar OK (16 satır okundu) · `.decisions/index.md` içinde `../brain.md` = 0 isabet, `../CLAUDE.md` = 0 isabet · disk sayımı accepted/ = 72 ADR (+CLAUDE.md) · rejected/ = 7 R-*.md (+CLAUDE.md, +index.md) · `draft/` = 0 ADR · `frozen/` dizini YOK.

**Not (yasak dosyalar):** bu oturum yalnız 3 dosya yazdı (`.ai/.decisions/index.md` 19:43:29 · `AGENTS.md` 19:43:45 · `.ai/.personas/index.md` 19:43:57) + bu log append'i. Yasak olarak bildirilen `.ai/index.md` (mtime 19:40:24) ve `.ai/sources/özet.md` (mtime 19:40:10) ilk yazımdan (19:43) ÖNCE değişmiş — paralel oturum işi, bu oturuma ait DEĞİL (tahmin edilen 19:16 yerine 19:40).

**Araç notu:** `.ai/scripts/vault-utf8-writer.mjs` ve `vault-cmd.mjs` diskte YOK (git status: `D`) → bu append .NET `[System.IO.File]::AppendAllText(..., UTF8Encoding($false))` ile, mevcut içeriğe dokunmadan bayt-seviyesinde yapıldı.

**git status --short (çalışma ağacının tamamı — çok sayıda önceden varmış `D`/`M`; bu oturumun katkıları yalnız 4 izinli dosya):**
 D .ai/.agents/AGENTS.md
 D .ai/.agents/audio-hardware-engineer.md
 D .ai/.agents/backend-architect.md
 D .ai/.agents/data-engineer.md
 D .ai/.agents/devops-engineer.md
 D .ai/.agents/dsp-firmware-engineer.md
 D .ai/.agents/embedded-engineer.md
 D .ai/.agents/master-orchestrator.md
 D .ai/.agents/qa-engineer.md
 D .ai/.agents/security-engineer.md
 D .ai/.agents/ui-designer.md
 D .ai/.agents/windows-software-engineer.md
 M .ai/.decisions/index.md
 D .ai/.obsidian/app.json
 D .ai/.obsidian/appearance.json
 D .ai/.obsidian/core-plugins.json
 D .ai/.obsidian/graph.json
 D .ai/.obsidian/workspace.json
 M .ai/.personas/index.md
 D .ai/.png/CLAUDE.md
 D .ai/.png/home-1024/CLAUDE.md
 D ".ai/.png/home-1024/Linux  1024 - Albumler Details Detay Page.png"
 D ".ai/.png/home-1024/Linux  1024 - Albumler Page.png"
 D ".ai/.png/home-1024/Linux  1024 - Bluetooth Quick Page Base.png"
 D ".ai/.png/home-1024/Linux  1024 - Göz At - Tıklama Clicked.png"
 D ".ai/.png/home-1024/Linux  1024 - Göz At Page.png"
 D ".ai/.png/home-1024/Linux  1024 - Home Page Welcome Popup.png"
 D ".ai/.png/home-1024/Linux  1024 - Home Page.png"
 D ".ai/.png/home-1024/Linux  1024 - Playlist Page - Video Played.png"
 D ".ai/.png/home-1024/Linux  1024 - Playlist Page.png"
 D ".ai/.png/home-1024/Linux  1024 - Singer Page.png"
 D ".ai/.png/home-1024/Linux  1024 - Wifi Connect Light.png"
 D ".ai/.png/home-1024/Linux  1024 - Wifi Quick Page Base.png"
 D .ai/.png/home-1920/CLAUDE.md
 D ".ai/.png/home-1920/Linux - 1920 - Home.png"
 D .ai/.png/shared-1024/CLAUDE.md
 D ".ai/.png/shared-1024/Linux  1024 - Login Girl.png"
 D ".ai/.png/shared-1024/Linux  1024 - Register Girl step 2.png"
 D ".ai/.png/shared-1024/Linux  1024 - Register Girl step 3.png"
 D ".ai/.png/shared-1024/Linux  1024 - Register Girl.png"
 D ".ai/.png/shared-1024/Linux  1024 - Select Gender - selected.png"
 D ".ai/.png/shared-1024/Linux  1024 - Select Gender.png"
 D .ai/.rules/senior-mode.md
 D .ai/.sql/CLAUDE.md
 D .ai/.sql/mssql/README.md
 D .ai/.sql/mysql/coremusic_ai.sql
 D .ai/.sql/mysql/coremusic_albums.sql
 D .ai/.sql/mysql/coremusic_api.sql
 D .ai/.sql/mysql/coremusic_auth.sql
 D .ai/.sql/mysql/coremusic_catalog.sql
 D .ai/.sql/mysql/coremusic_cms.sql
 D .ai/.sql/mysql/coremusic_download.sql
 D .ai/.sql/mysql/coremusic_logs.sql
 D .ai/.sql/mysql/coremusic_media.sql
 D .ai/.sql/mysql/coremusic_musics.sql
 D .ai/.sql/mysql/coremusic_neva.sql
 D .ai/.sql/mysql/coremusic_patch.sql
 D .ai/.sql/mysql/coremusic_playlist.sql
 D .ai/.sql/mysql/coremusic_social.sql
 D .ai/.sql/mysql/coremusic_studio.sql
 D .ai/.sql/mysql/coremusic_system.sql
 D .ai/.sql/mysql/coremusic_user.sql
 D .ai/.sql/mysql/coremusic_wireless.sql
 D .ai/.sql/mysql/media_catalog.sql
 D .ai/.sql/postgresql/README.md
 D .ai/.sql/sqlite/README.md
 D .ai/.subdomains/CLAUDE.md
 D .ai/AGENTS.md
 D .ai/CHECKLIST.md
 M .ai/CLAUDE.md
 D .ai/CONTEXT.md
 D .ai/MEMORY.md
 D .ai/PLAN.md
 D .ai/PROJECTS.md
 D .ai/ROLE.md
 D .ai/TODO.md
 D .ai/ULTRA-THINKING.md
 D .ai/VISION.md
 D .ai/WORKFLOW.md
 D .ai/architecture.1/AGENTS.md
 D .ai/architecture.1/CLAUDE.md
 D .ai/architecture.1/INDEX.md
 D .ai/architecture.1/README.md
 D .ai/architecture.1/WORKFLOW.md
 D .ai/architecture.1/k0-isletim-sistemi/01-platformlar/linux-core.md
 D .ai/architecture.1/k0-isletim-sistemi/01-platformlar/macos-core.md
 D .ai/architecture.1/k0-isletim-sistemi/01-platformlar/rpi5-core.md
 D .ai/architecture.1/k0-isletim-sistemi/01-platformlar/windows-api.md
 D .ai/architecture.1/k0-isletim-sistemi/01-platformlar/windows-core.md
 D .ai/architecture.1/k0-isletim-sistemi/02-cekirdek-mekanizmalar/ipc-mekanizmalari.md
 D .ai/architecture.1/k0-isletim-sistemi/02-cekirdek-mekanizmalar/memory-management.md
 D .ai/architecture.1/k0-isletim-sistemi/02-cekirdek-mekanizmalar/system-calls.md
 D .ai/architecture.1/k0-isletim-sistemi/02-cekirdek-mekanizmalar/threading-model.md
 D .ai/architecture.1/k0-isletim-sistemi/03-guvenlik-izolasyon/container-runtime.md
 D .ai/architecture.1/k0-isletim-sistemi/03-guvenlik-izolasyon/process-isolation.md
 D .ai/architecture.1/k0-isletim-sistemi/04-tasinabilirlik/cross-platform-api.md
 D .ai/architecture.1/k0-isletim-sistemi/README.md
 D .ai/architecture.1/k0-isletim-sistemi/index.md
 D .ai/architecture/k000-windows-core/asio-cekirdek-entegrasyonu.md
 D .ai/architecture/k000-windows-core/index.md
 D .ai/architecture/k000-windows-core/wasapi-ses-yolu-cekirdek.md
 D .ai/architecture/k000-windows-core/win32-olay-dongusu-ve-mesaj-kuyrugu.md
 D .ai/architecture/k000-windows-core/windows-api-yuzeyi.md
 D .ai/architecture/k000-windows-core/windows-core-mimari.md
 D .ai/architecture/k000-windows-core/windows-guvenlik-ve-olcullu-kisitlar.md
 D .ai/architecture/k000-windows-core/windows-performans-ve-gozlemlenebilirlik.md
 D .ai/architecture/k001-linux-rpi5/index.md
 D .ai/architecture/k001-linux-rpi5/linux-cekirdek-mimari.md
 D .ai/architecture/k001-linux-rpi5/rpi5-gomulu-platform.md
 D .ai/architecture/k002-macos-tasinabilirlik/cross-platform-api-soyutlama.md
 D .ai/architecture/k002-macos-tasinabilirlik/index.md
 D .ai/architecture/k002-macos-tasinabilirlik/macos-core-mimari.md
 D .ai/architecture/k003-cagri-thread/index.md
 D .ai/architecture/k003-cagri-thread/system-calls-rehberi.md
 D .ai/architecture/k003-cagri-thread/threading-model-detay.md
 D .ai/architecture/k003-cagri-thread/threading-ve-gercek-zaman.md
 D .ai/architecture/k004-surec-ipc/index.md
 D .ai/architecture/k004-surec-ipc/ipc-mekanizmalari-detay.md
 D .ai/architecture/k004-surec-ipc/ipc-mekanizmalari-karsilastirma.md
 D .ai/architecture/k004-surec-ipc/process-isolation-stratejileri.md
 D .ai/architecture/k004-surec-ipc/surec-izolasyon.md
 D .ai/architecture/k005-bellek-container/bellek-yonetimi.md
 D .ai/architecture/k005-bellek-container/container-runtime-ortami.md
 D .ai/architecture/k005-bellek-container/container-runtime.md
 D .ai/architecture/k005-bellek-container/index.md
 D .ai/architecture/k006-dac-adc-zinciri/dac-adc-zinciri-mimari.md
 D .ai/architecture/k006-dac-adc-zinciri/dac-adc-zinciri.md
 D .ai/architecture/k006-dac-adc-zinciri/index.md
 D .ai/architecture/k006-dac-adc-zinciri/pcm3168a-donemi.md
 D .ai/architecture/k006-dac-adc-zinciri/pcm3168a-donusum-asamasi.md
 D .ai/architecture/k007-ak4458-xmos/ak4458-dac-mimarisi.md
 D .ai/architecture/k007-ak4458-xmos/ak4458-dac.md
 D .ai/architecture/k007-ak4458-xmos/ak4458-xmos-islemci.md
 D .ai/architecture/k007-ak4458-xmos/index.md
 D .ai/architecture/k007-ak4458-xmos/xmos-xu316-entegrasyon.md
 D .ai/architecture/k008-analog-giris/analog-sinyal-yolu.md
 D .ai/architecture/k008-analog-giris/diff-pair-giris-asamasi.md
 D .ai/architecture/k008-analog-giris/diff-pair-giris.md
 D .ai/architecture/k008-analog-giris/index.md
 D .ai/architecture/k009-vas-feedback/feedback-agi-mimarisi.md
 D .ai/architecture/k009-vas-feedback/feedback-ve-kararlilik.md
 D .ai/architecture/k009-vas-feedback/index.md
 D .ai/architecture/k009-vas-feedback/vas-feedback-tasarimi.md
 D .ai/architecture/k009-vas-feedback/vas-stage.md
 D .ai/architecture/k010-class-ab-cikis/cikis-asamasi-mjle21194-93.md
 D .ai/architecture/k010-class-ab-cikis/class-ab-cikis-asamasi.md
 D .ai/architecture/k010-class-ab-cikis/index.md
 D .ai/architecture/k011-guc-koruma-termal/guc-koruma-termal.md
 D .ai/architecture/k011-guc-koruma-termal/index.md
 D .ai/architecture/k011-guc-koruma-termal/koruma-devreleri-detay.md
 D .ai/architecture/k012-dijital-arayuz/dijital-arayuzler.md
 D .ai/architecture/k012-dijital-arayuz/i2s-tdm-arayuz.md
 D .ai/architecture/k012-dijital-arayuz/index.md
 D .ai/architecture/k012-dijital-arayuz/usb-audio-ve-konnektor-arayuzleri.md
 D .ai/architecture/k012-dijital-arayuz/usb-ve-konnektorler.md
 D .ai/architecture/k013-pcb-hoparlor/hoparlor-dizilimi-detay.md
 D .ai/architecture/k013-pcb-hoparlor/hoparlor-dizilimi.md
 D .ai/architecture/k013-pcb-hoparlor/index.md
 D .ai/architecture/k013-pcb-hoparlor/pcb-tasarim-ilkeleri.md
 D .ai/architecture/k013-pcb-hoparlor/pcb-ve-hoparlor.md
 D .ai/architecture/k014-surucu-yigin/buffer-ve-yonetim.md
 D .ai/architecture/k014-surucu-yigin/driver-stack-ve-buffer.md
 D .ai/architecture/k014-surucu-yigin/index.md
 D .ai/architecture/k014-surucu-yigin/latency-optimization-teknikleri.md
 D .ai/architecture/k014-surucu-yigin/surucu-yigini-mimari.md
 D .ai/architecture/k015-platform-suruculeri/asio-ve-wasapi.md
 D .ai/architecture/k015-platform-suruculeri/asio-wasapi-coreaudio.md
 D .ai/architecture/k015-platform-suruculeri/core-audio-ve-pipewire.md
 D .ai/architecture/k015-platform-suruculeri/index.md
 D .ai/architecture/k015-platform-suruculeri/wasapi-coreaudio-platform-detay.md
 D .ai/architecture/k016-linux-ses/alsa-native.md
 D .ai/architecture/k016-linux-ses/index.md
 D .ai/architecture/k016-linux-ses/linux-ses-yigini.md
 D .ai/architecture/k016-linux-ses/pipewire-modern-yigin.md
 D .ai/architecture/k016-linux-ses/pipewire-modern.md
 D .ai/architecture/k017-uzak-bluetooth-usb/bluetooth-a2dp-akis.md
 D .ai/architecture/k017-uzak-bluetooth-usb/bluetooth-a2dp.md
 D .ai/architecture/k017-uzak-bluetooth-usb/index.md
 D .ai/architecture/k017-uzak-bluetooth-usb/usb-ve-ag-ses-suruculeri.md
 D .ai/architecture/k017-uzak-bluetooth-usb/uzak-ses-suruculeri.md
 D .ai/architecture/k018-dma-kesinti-yonetimi/dma-olcum-ve-test.md
 D .ai/architecture/k018-dma-kesinti-yonetimi/dma-yonetimi.md
 D .ai/architecture/k018-dma-kesinti-yonetimi/index.md
 D .ai/architecture/k018-dma-kesinti-yonetimi/irq-kesinti-yoneticisi.md
 D .ai/architecture/k019-windows-core/index.md
 D .ai/architecture/k019-windows-core/windows-api.md
 D .ai/architecture/k019-windows-core/windows-core.md
 D .ai/architecture/k020-linux-core/alsa-native.md
 D .ai/architecture/k020-linux-core/index.md
 D .ai/architecture/k020-linux-core/linux-core.md
 D .ai/architecture/k021-macos-core/core-audio-macos.md
 D .ai/architecture/k021-macos-core/index.md
 D .ai/architecture/k021-macos-core/macos-core.md
 D .ai/architecture/k022-rpi5-core/index.md
 D .ai/architecture/k022-rpi5-core/rpi5-core.md
 D .ai/architecture/k022-rpi5-core/rpi5-pwm-gpio-audio.md
 D .ai/architecture/k023-system-calls/index.md
 D .ai/architecture/k023-system-calls/syscall-guvenlik-ve-hata.md
 D .ai/architecture/k023-system-calls/system-calls.md
 D .ai/architecture/k024-ipc-mekanizmalari/index.md
 D .ai/architecture/k024-ipc-mekanizmalari/ipc-mekanizmalari.md
 D .ai/architecture/k024-ipc-mekanizmalari/ipc-performans-karsilastirma.md
 D .ai/architecture/k025-threading-model/gercek-zamanli-zamanlama.md
 D .ai/architecture/k025-threading-model/index.md
 D .ai/architecture/k025-threading-model/threading-model.md
 D .ai/architecture/k026-memory-management/bellek-havuzlari-ve-leak.md
 D .ai/architecture/k026-memory-management/index.md
 D .ai/architecture/k026-memory-management/memory-management.md
 D .ai/architecture/k027-process-isolation/index.md
 D .ai/architecture/k027-process-isolation/process-isolation.md
 D .ai/architecture/k027-process-isolation/sandbox-ve-guvenlik.md
 D .ai/architecture/k028-container-runtime/container-runtime.md
 D .ai/architecture/k028-container-runtime/docker-orkestrasyon.md
 D .ai/architecture/k028-container-runtime/index.md
 D .ai/architecture/k029-cross-platform-api/cross-platform-api.md
 D .ai/architecture/k029-cross-platform-api/index.md
 D .ai/architecture/k029-cross-platform-api/platform-soyutlama-katmani.md
 D .ai/architecture/k030-driver-stack/driver-stack-mimari.md
 D .ai/architecture/k030-driver-stack/index.md
 D .ai/architecture/k030-driver-stack/surucu-yigini-yonetimi.md
 D .ai/architecture/k031-buffer-management/buffer-management.md
 D .ai/architecture/k031-buffer-management/index.md
 D .ai/architecture/k031-buffer-management/kilitsiz-kuyruklar.md
 D .ai/architecture/k032-latency-optimization/gecikme-olcum-ve-tuning.md
 D .ai/architecture/k032-latency-optimization/index.md
 D .ai/architecture/k032-latency-optimization/latency-optimization.md
 D .ai/architecture/k033-platform-ses-suruculeri/asio-drivers.md
 D .ai/architecture/k033-platform-ses-suruculeri/index.md
 D .ai/architecture/k033-platform-ses-suruculeri/pipewire-modern.md
 D .ai/architecture/k033-platform-ses-suruculeri/wasapi-exclusive.md
 D .ai/architecture/k034-usb-audio/i2s-interface.md
 D .ai/architecture/k034-usb-audio/index.md
 D .ai/architecture/k034-usb-audio/usb-audio-class.md
 D .ai/architecture/k034-usb-audio/usb-hotplug-enumerasyon.md
 D .ai/architecture/k035-ag-ve-bluetooth-ses/bluetooth-a2dp.md
 D .ai/architecture/k035-ag-ve-bluetooth-ses/index.md
 D .ai/architecture/k035-ag-ve-bluetooth-ses/network-audio-drivers.md
 D .ai/architecture/k036-asio-drivers/asio-buffer-callback.md
 D .ai/architecture/k036-asio-drivers/asio-device-lifecycle.md
 D .ai/architecture/k036-asio-drivers/asio-exclusive-mode.md
 D .ai/architecture/k036-asio-drivers/asio-hata-yonetimi.md
 D .ai/architecture/k036-asio-drivers/asio-latency-hesap.md
 D .ai/architecture/k036-asio-drivers/asio-sdk-entegrasyon.md
 D .ai/architecture/k036-asio-drivers/asio-thread-model.md
 D .ai/architecture/k036-asio-drivers/index.md
 D .ai/architecture/k037-wasapi-exclusive/index.md
 D .ai/architecture/k037-wasapi-exclusive/wasapi-audio-session.md
 D .ai/architecture/k037-wasapi-exclusive/wasapi-buffer-latency.md
 D .ai/architecture/k037-wasapi-exclusive/wasapi-device-hotplug.md
 D .ai/architecture/k037-wasapi-exclusive/wasapi-exclusive-mode.md
 D .ai/architecture/k037-wasapi-exclusive/wasapi-exclusive-shared.md
 D .ai/architecture/k037-wasapi-exclusive/wasapi-format-negotiation.md
 D .ai/architecture/k037-wasapi-exclusive/wasapi-hata-kodlari.md
 D .ai/architecture/k037-wasapi-exclusive/wasapi-shared-mode.md
 D .ai/architecture/k038-core-audio-macos/coreaudio-hal.md
 D .ai/architecture/k038-core-audio-macos/dusuk-gecikme-yolu.md
 D .ai/architecture/k038-core-audio-macos/index.md
 D .ai/architecture/k039-alsa-native/alsa-pcm-device.md
 D .ai/architecture/k039-alsa-native/index.md
 D .ai/architecture/k039-alsa-native/rt-thread-ve-pipewire-siniri.md
 D .ai/architecture/k054-dac-adc-zinciri/clock-jitter-analizi.md
 D .ai/architecture/k054-dac-adc-zinciri/index.md
 D .ai/architecture/k054-dac-adc-zinciri/olcum-ve-test-noktalari.md
 D .ai/architecture/k054-dac-adc-zinciri/zincir-kaynak-karsilastirma.md
 D .ai/architecture/k054-dac-adc-zinciri/zincir-mimari.md
 D .ai/architecture/k055-ak4458-dac/ak4458-dac-rehberi.md
 D .ai/architecture/k055-ak4458-dac/ak4458-kaynak-karsilastirma.md
 D .ai/architecture/k055-ak4458-dac/index.md
 D .ai/architecture/k056-pcm3168a-dac-adc/index.md
 D .ai/architecture/k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi.md
 D .ai/architecture/k056-pcm3168a-dac-adc/pcm3168a-kaynak-karsilastirma.md
 D .ai/architecture/k057-i2s-interface/i2s-firmware-surucu.md
 D .ai/architecture/k057-i2s-interface/i2s-ve-tdm-rehberi.md
 D .ai/architecture/k057-i2s-interface/index.md
 D .ai/architecture/k058-xmos-xu316/index.md
 D .ai/architecture/k058-xmos-xu316/xu316-entegrasyon.md
 D .ai/architecture/k058-xmos-xu316/xu316-firmware.md
 D .ai/architecture/k059-usb-audio/index.md
 D .ai/architecture/k059-usb-audio/usb-audio-firmware-surucu.md
 D .ai/architecture/k059-usb-audio/usb-audio-yolu.md
 D .ai/architecture/k060-analog-sinyal-yolu/analog-yol-rehberi.md
 D .ai/architecture/k060-analog-sinyal-yolu/index.md
 D .ai/architecture/k061-diff-pair-input/diff-pair-tasarim.md
 D .ai/architecture/k061-diff-pair-input/index.md
 D .ai/architecture/k062-vas-stage/index.md
 D .ai/architecture/k062-vas-stage/vas-stage-tasarim.md
 D .ai/architecture/k063-output-stage/index.md
 D .ai/architecture/k063-output-stage/output-stage-tasarim.md
 D .ai/architecture/k064-feedback-network/feedback-tasarim.md
 D .ai/architecture/k064-feedback-network/index.md
 D .ai/architecture/k065-mjle21194-93/index.md
 D .ai/architecture/k065-mjle21194-93/mjle-op-amp-kurulum.md
 D .ai/architecture/k066-konnektorler/baglanti-ve-etiketleme.md
 D .ai/architecture/k066-konnektorler/index.md
 D .ai/architecture/k066-konnektorler/konnektor-cesitleri.md
 D .ai/architecture/k066-konnektorler/konnektor-envanteri.md
 D .ai/architecture/k067-koruma-devreleri/dc-offset-ve-termal-koruma.md
 D .ai/architecture/k067-koruma-devreleri/index.md
 D .ai/architecture/k067-koruma-devreleri/koruma-devreleri-turleri.md
 D .ai/architecture/k067-koruma-devreleri/koruma-rehberi.md
 D .ai/architecture/k068-guc-kaynagi-analog/analog-besleme.md
 D .ai/architecture/k068-guc-kaynagi-analog/index.md
 D .ai/architecture/k068-guc-kaynagi-analog/regulasyon-ve-filtreleme.md
 D .ai/architecture/k068-guc-kaynagi-analog/topraklama-ve-guc-dagilimi.md
 D .ai/architecture/k069-hoparlor-dizilimi/hoparlor-dizilim.md
 D .ai/architecture/k069-hoparlor-dizilimi/index.md
 D .ai/architecture/k070-pcb-tasarim/index.md
 D .ai/architecture/k070-pcb-tasarim/pcb-rehber.md
 D .ai/architecture/k071-termal-yonetim/index.md
 D .ai/architecture/k071-termal-yonetim/termal-rehber.md
 D .ai/architecture/k072-neva-engine-core/index.md
 D .ai/architecture/k072-neva-engine-core/neva-engine-core.md
 D .ai/architecture/k073-dsp-chain/analysis-spectrum.md
 D .ai/architecture/k073-dsp-chain/dsp-chain.md
 D .ai/architecture/k073-dsp-chain/index.md
 D .ai/architecture/k074-eq-parametric/eq-parametric.md
 D .ai/architecture/k074-eq-parametric/index.md
 D .ai/architecture/k075-sample-rate-conversion/index.md
 D .ai/architecture/k075-sample-rate-conversion/sample-rate-conversion.md
 D .ai/architecture/k076-mixer-routing/index.md
 D .ai/architecture/k076-mixer-routing/mixer-routing.md
 D .ai/architecture/k077-stream-buffer/index.md
 D .ai/architecture/k077-stream-buffer/stream-buffer.md
 D .ai/architecture/k078-playback-gapless/index.md
 D .ai/architecture/k078-playback-gapless/playback-gapless.md
 D .ai/architecture/k079-bit-depth-conversion/bit-depth-conversion.md
 D .ai/architecture/k108-vector-index/index.md
 D .ai/architecture/k108-vector-index/vector-index-mimari.md
 D .ai/architecture/k108-vector-index/vector-index-operasyon.md
 D .ai/architecture/k109-semantic-id/index.md
 D .ai/architecture/k109-semantic-id/semantic-id-tasarim.md
 D .ai/architecture/k109-semantic-id/semantic-id-uyum-stratejisi.md
 D .ai/architecture/k120-mysql-18-database/mysql-18-database-sema.md
 D .ai/architecture/k132-data-migration-history/goc-gecmisi-mimari.md
 D .ai/architecture/k132-data-migration-history/goc-gecmisi-operasyon.md
 D .ai/architecture/k132-data-migration-history/index.md
 D .ai/architecture/k133-read-replica/index.md
 D .ai/architecture/k133-read-replica/read-replica-dengeleme.md
 D .ai/architecture/k133-read-replica/read-replica-mimari.md
 D .ai/architecture/k134-shard-strategy/index.md
 D .ai/architecture/k134-shard-strategy/shard-strateji-mimari.md
 D .ai/architecture/k134-shard-strategy/shard-yeniden-dagilim.md
 D .ai/architecture/k135-event-store/event-store-mimari.md
 D .ai/architecture/k135-event-store/event-store-saklama-ve-replay.md
 D .ai/architecture/k135-event-store/index.md
 D .ai/architecture/k136-cdcdc-pipeline/cdc-pipeline-mimari.md
 D .ai/architecture/k136-cdcdc-pipeline/cdc-pipeline-operasyon.md
 D .ai/architecture/k136-cdcdc-pipeline/index.md
 D .ai/architecture/k137-integrity-check/index.md
 D .ai/architecture/k137-integrity-check/integrity-check-denetim.md
 D .ai/architecture/k137-integrity-check/integrity-check-mimari.md
 D .ai/architecture/k138-gdpr-export/gdpr-export-operasyon.md
 D .ai/architecture/k138-gdpr-export/gdpr-export-tasarim.md
 D .ai/architecture/k138-gdpr-export/index.md
 D .ai/architecture/k139-session-store/index.md
 D .ai/architecture/k139-session-store/session-store-mimari.md
 D .ai/architecture/k139-session-store/session-store-operasyon.md
 D .ai/architecture/k140-profile-store/index.md
 D .ai/architecture/k140-profile-store/profile-store-mimari.md
 D .ai/architecture/k140-profile-store/profile-store-operasyon.md
 D .ai/architecture/k141-playlist-store/index.md
 D .ai/architecture/k141-playlist-store/playlist-store-mimari.md
 D .ai/architecture/k141-playlist-store/playlist-store-operasyon.md
 D .ai/architecture/k142-library-metadata/index.md
 D .ai/architecture/k142-library-metadata/library-metadata-mimari.md
 D .ai/architecture/k142-library-metadata/library-metadata-operasyon.md
 D .ai/architecture/k143-stats-rollup/index.md
 D .ai/architecture/k143-stats-rollup/stats-rollup-mimari.md
 D .ai/architecture/k143-stats-rollup/stats-rollup-operasyon.md
 D .ai/archives/AGENTS.md
 D .ai/archives/CLAUDE.md
 D .ai/archives/prompt-shared-base.md
 D .ai/archives/prompt-unified-2026-08-15.md
 D .ai/archives/prompt0-genel-ana-prompt-2026-08-15.md
 D .ai/archives/prompt0-genel-ana-prompt-2026-09-01.md
 D .ai/archives/prompt1-spa-router-2026-08-15.md
 D .ai/archives/prompt1-spa-router-2026-09-01.md
 D .ai/archives/prompt2-auth-2026-08-15.md
 D .ai/archives/prompt2-auth-2026-09-01.md
 D .ai/archives/prompt3-api-2026-08-15.md
 D .ai/archives/prompt3-api-2026-09-01.md
 D .ai/archives/vault-faz4-sweep.mjs
 D .ai/brain.md
 D .ai/broken-links-report.md
 D .ai/checklists/todos.md
 D .ai/ecosystem/README.md
 D .ai/ecosystem/asio-wasapi-rehber.md
 D .ai/ecosystem/donanım-devre-referanslari.md
 D .ai/ecosystem/ekosistem-mimarileri.md
 D .ai/ecosystem/index.md
 D .ai/ecosystem/muzik-streaming-sunuculari.md
 D .ai/ecosystem/service-integration.md
 D .ai/ecosystem/ses-dsp-acik-kaynak.md
 D .ai/engine.md
 D .ai/glossary.md
 M .ai/index.md
 D .ai/keys.md
 M .ai/log.md
 D .ai/prompts/2026-09-23-vault-refactor-engine.md
 D .ai/prompts/2026-09-27-component-system-master-prompt.md
 D .ai/reports/500-error-report.md
 D .ai/reports/auth-bypass-audit.md
 D .ai/reports/broken-code-scan.md
 D .ai/reports/broken-files-report.md
 D .ai/reports/config-analysis-report.md
 D .ai/reports/faz6-link-ledger.md
 D .ai/reports/hardcoded-config-audit.md
 D .ai/reports/include-structure-map.md
 D .ai/reports/unused-files-report.md
 D .ai/scripts/device-matrix-catid.ps1
 D .ai/scripts/figma-extract.ps1
 D .ai/scripts/figma-tokens.ps1
 D .ai/scripts/fix-mojibake.py
 D .ai/scripts/index.md
 D .ai/scripts/kalip-abc-check.ps1
 D .ai/scripts/screens-frontmatter-check.ps1
 D .ai/scripts/vault-utf8-writer.mjs
 D .ai/scripts/wiki-link-check.ps1
 D .ai/servers/linux-nginx.md
 D .ai/servers/windows-apache.md
 D .ai/servers/windows-iis.md
 D .ai/ui-design/00-device-matrix.md
 D .ai/ui-design/01-mockup-index.md
 D .ai/ui-design/02-component-inventory.md
 D .ai/ui-design/03-implementation-plan.md
 D .ai/ui-design/04-accessibility-gaps.md
 D .ai/ui-design/05-responsive-architecture.md
 D .ai/ui-design/flow/00-flow-index.md
 D .ai/ui-design/flow/auth/01-login.md
 D .ai/ui-design/flow/auth/02-register.md
 D .ai/ui-design/flow/auth/03-forgot-password.md
 D .ai/ui-design/flow/auth/04-select-gender.md
 D .ai/ui-design/flow/auth/05-logout.md
 D .ai/ui-design/flow/automotive/01-android-auto-layout.md
 D .ai/ui-design/flow/automotive/02-carplay-layout.md
 D .ai/ui-design/flow/music/01-playback.md
 D .ai/ui-design/flow/music/02-playlist-queue.md
 D .ai/ui-design/flow/music/03-album-browse.md
 D .ai/ui-design/flow/music/04-artist-browse.md
 D .ai/ui-design/flow/music/05-search.md
 D .ai/ui-design/flow/navigation/01-spa-routing.md
 D .ai/ui-design/flow/navigation/02-header-nav.md
 D .ai/ui-design/flow/navigation/03-footer-player.md
 D .ai/ui-design/flow/settings/01-wifi-connect.md
 D .ai/ui-design/flow/settings/02-bluetooth-connect.md
 D .ai/ui-design/flow/settings/03-equalizer.md
 D .ai/ui-design/flow/settings/04-general.md
 D .ai/ui-design/flow/watch/01-now-playing.md
 D .ai/ui-design/prompt/00-prompt-index.md
 D .ai/ui-design/prompt/component/C01-nav-link.md
 D .ai/ui-design/prompt/component/C02-status-widget.md
 D .ai/ui-design/prompt/component/C03-user-pill.md
 D .ai/ui-design/prompt/component/C04-primary-button.md
 D .ai/ui-design/prompt/component/C05-secondary-button.md
 D .ai/ui-design/prompt/component/C06-form-input.md
 D .ai/ui-design/prompt/component/C07-gender-button.md
 D .ai/ui-design/prompt/component/C08-social-login.md
 D .ai/ui-design/prompt/component/C09-media-card.md
 D .ai/ui-design/prompt/component/C10-detail-panel.md
 D .ai/ui-design/prompt/component/C11-genre-tabs.md
 D .ai/ui-design/prompt/component/C12-star-rating.md
 D .ai/ui-design/prompt/component/C13-track-list.md
 D .ai/ui-design/prompt/component/C14-modal.md
 D .ai/ui-design/prompt/component/C15-toggle.md
 D .ai/ui-design/prompt/component/C16-network-row.md
 D .ai/ui-design/prompt/component/aaaa.md
 D .ai/ui-design/prompt/layout/01-mobile-stack.md
 D .ai/ui-design/prompt/layout/02-tablet-grid.md
 D .ai/ui-design/prompt/layout/03-embedded-split.md
 D .ai/ui-design/prompt/layout/04-laptop-sidebar.md
 D .ai/ui-design/prompt/layout/05-desktop-3col.md
 D .ai/ui-design/prompt/layout/06-4k-expanded.md
 D .ai/ui-design/prompt/layout/07-tv-focus.md
 D .ai/ui-design/prompt/layout/08-car-touch.md
 D .ai/ui-design/prompt/layout/09-watch-micro.md
 D .ai/ui-design/prompt/layout/10-spatial-ar.md
 D .ai/ui-design/prompt/page/01-home.md
 D .ai/ui-design/prompt/page/02-albums.md
 D .ai/ui-design/prompt/page/03-album-detail.md
 D .ai/ui-design/prompt/page/04-artists.md
 D .ai/ui-design/prompt/page/05-playlist.md
 D .ai/ui-design/prompt/page/06-browse.md
 D .ai/ui-design/prompt/page/07-settings.md
 D .ai/ui-design/prompt/page/08-login.md
 D .ai/ui-design/prompt/page/09-register.md
 D .ai/ui-design/prompt/page/10-gender-select.md
 D .ai/ui-design/prompt/page/11-wifi.md
 D .ai/ui-design/prompt/page/12-bluetooth.md
 D .ai/ui-design/prompt/screen/00-prompt-index.md
 D .ai/ui-design/prompt/screen/T1-phone.md
 D .ai/ui-design/prompt/screen/T10-watch.md
 D .ai/ui-design/prompt/screen/T2-tablet-small.md
 D .ai/ui-design/prompt/screen/T3-tablet-large.md
 D .ai/ui-design/prompt/screen/T4-embedded.md
 D .ai/ui-design/prompt/screen/T5-laptop.md
 D .ai/ui-design/prompt/screen/T6-desktop.md
 D .ai/ui-design/prompt/screen/T7-desktop-4k.md
 D .ai/ui-design/prompt/screen/T8-tv.md
 D .ai/ui-design/prompt/screen/T9-car.md
 D .ai/ui-design/prompt/web-research.md
 D .ai/ui-design/reference/01-php-source-architecture.md
 D .ai/ui-design/reference/02-text-strings.md
 D .ai/ui-design/reference/03-icon-asset-catalog.md
 D .ai/ui-design/reference/04-verification.md
 D .ai/ui-design/reference/05-backend-reference.md
 D .ai/ui-design/reference/06-frontend-reference.md
 D .ai/ui-design/reference/07-session-notes.md
 D .ai/ui-design/reference/08-css-design-tokens.md
 D .ai/ui-design/reference/09-interaction-states.md
 D .ai/ui-design/reference/10-device-specific-guidelines.md
 D .ai/ui-design/reference/figma/_extraction-notes.md
 D .ai/ui-design/reference/figma/extracted-1024.md
 D .ai/ui-design/reference/figma/extracted-1047-15802.md
 D .ai/ui-design/reference/figma/extracted-1920.md
 D .ai/ui-design/reference/figma/grid-rules.md
 D ".ai/ui-design/reference/figma/png/1024 - Diiv2 Button.png"
 D ".ai/ui-design/reference/figma/png/1024 - Footer.png"
 D ".ai/ui-design/reference/figma/png/1024 - Menu En Son Şarkılar.png"
 D ".ai/ui-design/reference/figma/png/1024 - Menu Oynatma Listesi.png"
 D ".ai/ui-design/reference/figma/png/1024 - Player Info.png"
 D ".ai/ui-design/reference/figma/png/1024 - Sıradaki Şarkı.png"
 D ".ai/ui-design/reference/figma/png/1024 - Welcome Div.png"
 D .ai/ui-design/reference/figma/png/1047-29968-Linux-1024---Wizard-Layout---Hosgelidniz.png
 D .ai/ui-design/reference/figma/png/1047-30186-Linux-1024---Wizard-Layout---Cinsiyet-Secimi.png
 D .ai/ui-design/reference/figma/png/1047-30421-Linux-1024---Wizard-Layout---Depolama-Se-enkleri.png
 D .ai/ui-design/reference/figma/png/1047-30674-Linux-1024---Wizard-Layout---Depolama-Se-enkleri-2.png
 D .ai/ui-design/reference/figma/png/1047-30939-Linux-1024---Wizard-Layout---Lisans-S-zle-mesi.png
 D .ai/ui-design/reference/figma/png/1047-31154-Linux-1024---Wizard-Layout---Tema-Secimi.png
 D .ai/ui-design/reference/figma/png/1047-32063-Linux-1024---Wizard-Layout---Dil-B-lge-Zaman-Dilimi-Secimi.png
 D .ai/ui-design/reference/figma/png/1047-32284-Linux-1024---Wizard-Layout---Dil-Ya-Secimi.png
 D .ai/ui-design/reference/figma/png/1339-23464-Laptop-1920---Ge-mi-Page.png
 D .ai/ui-design/reference/figma/png/1376-25222-Group-22.png
 D .ai/ui-design/reference/figma/png/1378-22109-Laptop-1920---G-z-At-Page.png
 D .ai/ui-design/reference/figma/png/1390-24448-Laptop-1920---Playlist-Page.png
 D .ai/ui-design/reference/figma/png/15-563-XD-Components.png
 D .ai/ui-design/reference/figma/png/1509-20501-Group-7.png
 D .ai/ui-design/reference/figma/png/1509-20527-Wifi-Passwd-Div.png
 D .ai/ui-design/reference/figma/png/1509-20550-Group-5.png
 D .ai/ui-design/reference/figma/png/1509-20572-Wifi-Passwd-Div.png
 D .ai/ui-design/reference/figma/png/1513-21840-Bt-Qucik-Div.png
 D .ai/ui-design/reference/figma/png/1513-22528-Wifi-Qucik-Div.png
 D .ai/ui-design/reference/figma/png/1639-10160-Linux-1024---Home-Page.png
 D .ai/ui-design/reference/figma/png/1639-9773-Footer.png
 D .ai/ui-design/reference/figma/png/1639-9775-Diiv2-Button.png
 D .ai/ui-design/reference/figma/png/1639-9892-Menu-Oynatma-Listesi.png
 D .ai/ui-design/reference/figma/png/1639-9904-Menu-En-Son-ark-lar.png
 D .ai/ui-design/reference/figma/png/1639-9910-S-radaki-ark.png
 D .ai/ui-design/reference/figma/png/1646-17727-Player-nfo.png
 D .ai/ui-design/reference/figma/png/1670-21525-Albumsitem.png
 D .ai/ui-design/reference/figma/png/1670-22509-Linux-1024---G-z-At---T-klama-Clikced.png
 D .ai/ui-design/reference/figma/png/1703-16776-Group-23.png
 D .ai/ui-design/reference/figma/png/1703-18219-Frame-18.png
 D .ai/ui-design/reference/figma/png/175-871-Player-İmg-Div-pi.png
 D .ai/ui-design/reference/figma/png/1760-13366-Admin-Login-Redesign-Variant-1.png
 D .ai/ui-design/reference/figma/png/1760-13440-Core-Music-OS-File-Manager-Variant-3.png
 D .ai/ui-design/reference/figma/png/1760-13697-Music-Site-Register-Glass-Side-Panel.png
 D .ai/ui-design/reference/figma/png/1760-13815-Admin-Dashboard-Variant-1.png
 D .ai/ui-design/reference/figma/png/1760-13924-Music-Site-Admin-Login-Screen.png
 D .ai/ui-design/reference/figma/png/1760-13996-Core-Music-OS-A-Terminal-Variant-1.png
 D .ai/ui-design/reference/figma/png/1760-14274-Core-A-Dashboard-Variant-3.png
 D .ai/ui-design/reference/figma/png/1760-14599-Core-A-Dashboard-Variant-1.png
 D .ai/ui-design/reference/figma/png/1760-14874-Core-A-Neural-Terminal-Variant-1.png
 D .ai/ui-design/reference/figma/png/1760-15214-Core-A-Dashboard-Variant-2.png
 D .ai/ui-design/reference/figma/png/1760-15518-Core-A-Neural-Terminal-Variant-2.png
 D .ai/ui-design/reference/figma/png/1760-15886-Core-A-mmersive-Hub-Variant-3.png
 D .ai/ui-design/reference/figma/png/1760-16326-Core-A-Neural-Hub-Variant-1.png
 D .ai/ui-design/reference/figma/png/1760-16619-Full-Height-A-Sidebar-Variant-1.png
 D .ai/ui-design/reference/figma/png/1760-16966-Full-Height-A-Sidebar-Variant-2.png
 D .ai/ui-design/reference/figma/png/1760-17332-Core-Music-OS-Collapsible-A-Variant-1.png
 D .ai/ui-design/reference/figma/png/1760-17704-Core-Music-OS-Toggleable-A-Variant-3.png
 D ".ai/ui-design/reference/figma/png/1920 - Div2 Button.png"
 D ".ai/ui-design/reference/figma/png/1920 - Player Info.png"
 D ".ai/ui-design/reference/figma/png/1920 - Welcome Div.png"
 D .ai/ui-design/reference/figma/png/1988-17916-Linux-1024---Albumler-Page.png
 D .ai/ui-design/reference/figma/png/1988-18095-Linux-1024---Singer-Page.png
 D .ai/ui-design/reference/figma/png/1988-18256-Linux-1024---Playlist-Page---Video-Played.png
 D .ai/ui-design/reference/figma/png/1988-18293-Linux-1024---G-z-At---T-klama-Clikced.png
 D .ai/ui-design/reference/figma/png/1988-18435-Linux-1024---G-z-At---T-klama-Clikced.png
 D .ai/ui-design/reference/figma/png/1988-18555-Linux-1024---G-z-At---T-klama-Clikced.png
 D .ai/ui-design/reference/figma/png/1988-18675-Linux-1024---G-z-At---T-klama-Clikced.png
 D .ai/ui-design/reference/figma/png/1988-18797-Linux-1024---G-z-At---T-klama-Clikced.png
 D .ai/ui-design/reference/figma/png/1988-18919-Linux-1024---G-z-At---T-klama-Clikced.png
 D .ai/ui-design/reference/figma/png/1988-19185-Linux-1024---G-z-At-Page.png
 D .ai/ui-design/reference/figma/png/1988-8655-Linux-1024---Settings-Popup-Menu-Sistem-White-Light.png
 D .ai/ui-design/reference/figma/png/1988-8973-Linux-1024---Settings-Popup-Menu-Ses-Light.png
 D .ai/ui-design/reference/figma/png/1988-9467-Linux-1024---Singer-Page-Kopya.png
 D .ai/ui-design/reference/figma/png/2003-18162-Linux-1024---G-z-At---T-klama-Clikced.png
 D .ai/ui-design/reference/figma/png/2003-20542-Linux-1024---Playlist-Page---Video-Played.png
 D .ai/ui-design/reference/figma/png/2003-20809-Linux-1024---Singer-Page.png
 D .ai/ui-design/reference/figma/png/2003-21063-Linux-1024---Albumler-Page.png
 D .ai/ui-design/reference/figma/png/2003-21431-Linux-1024---Albumler-Details-Detay-Page.png
 D .ai/ui-design/reference/figma/png/2003-25036-iPhone-16-17-Pro-Max---1.png
 D .ai/ui-design/reference/figma/png/2003-7105-Linux-1024---Home-Page.png
 D .ai/ui-design/reference/figma/png/2003-7439-Linux-1024---G-z-At-Page.png
 D .ai/ui-design/reference/figma/png/2099-19716-widget-componets.png
 D .ai/ui-design/reference/figma/png/2135-24715-Linux-1024---Terminal.png
 D .ai/ui-design/reference/figma/png/2135-33342-Laptop-1920---Home-Page.png
 D .ai/ui-design/reference/figma/png/2135-34323-Laptop-1920---Playlist-Page-full-screen-mode-2.png
 D .ai/ui-design/reference/figma/png/2161-12499-Admin---layout.png
 D .ai/ui-design/reference/figma/png/2161-12506-Admin---Login.png
 D .ai/ui-design/reference/figma/png/2161-12529-Admin---Register.png
 D .ai/ui-design/reference/figma/png/2281-10728-Sa-Panel.png
 D .ai/ui-design/reference/figma/png/2531-15480-Frame-19.png
 D .ai/ui-design/reference/figma/png/2531-15492-Rectangle-57-color-scale.png
 D .ai/ui-design/reference/figma/png/2681-6134-9880fb8c02-9212f8cbf6c9011f5490-pdf.png
 D .ai/ui-design/reference/figma/png/2729-3653-Linux-1024---Select-Gender.png
 D .ai/ui-design/reference/figma/png/2729-3680-Linux-1024---Select-Gender.png
 D .ai/ui-design/reference/figma/png/2795-21199-Linux-1024---G-z-At---T-klama-Clikced.png
 D .ai/ui-design/reference/figma/png/2795-21324-Linux-1024---G-z-At-Page.png
 D .ai/ui-design/reference/figma/png/2831-10020-Linux-1024---Register-Girl-step-3.png
 D .ai/ui-design/reference/figma/png/2831-10086-Linux-1024---Albumler-Details-Detay-Page.png
 D .ai/ui-design/reference/figma/png/2831-10265-Linux-1024---Home-Page-Welcome-Popup.png
 D .ai/ui-design/reference/figma/png/2831-10267-Welcome-Div.png
 D .ai/ui-design/reference/figma/png/2831-10282-Linux-1024---G-z-At---T-klama-Clikced.png
 D .ai/ui-design/reference/figma/png/2831-13454-Romantic-Background-03-2.png
 D .ai/ui-design/reference/figma/png/2831-13459-Footer.png
 D .ai/ui-design/reference/figma/png/2831-13460-Navbar.png
 D .ai/ui-design/reference/figma/png/2831-13461-Diiv2-Button.png
 D .ai/ui-design/reference/figma/png/2831-13539-Player-nfo.png
 D .ai/ui-design/reference/figma/png/2831-13553-Menu-Oynatma-Listesi.png
 D .ai/ui-design/reference/figma/png/2831-13564-Menu-En-Son-ark-lar.png
 D .ai/ui-design/reference/figma/png/2831-13570-S-radaki-ark.png
 D .ai/ui-design/reference/figma/png/2831-13747-Linux---1920---Home.png
 D .ai/ui-design/reference/figma/png/2831-8530-Linux-1024---Settings-Popup-Menu-Tema-Light.png
 D .ai/ui-design/reference/figma/png/2831-8672-Linux-1024---Home-Page.png
 D .ai/ui-design/reference/figma/png/2831-8673-Linux-1024---Terminal.png
 D .ai/ui-design/reference/figma/png/2831-9176-Linux-1024---Albumler-Page.png
 D .ai/ui-design/reference/figma/png/2831-9273-Linux-1024---Singer-Page.png
 D .ai/ui-design/reference/figma/png/2831-9443-Linux-1024---Playlist-Page.png
 D .ai/ui-design/reference/figma/png/2831-9555-Linux-1024---G-z-At-Page.png
 D .ai/ui-design/reference/figma/png/2831-9644-Linux-1024---Wifi-Coonect-Light.png
 D .ai/ui-design/reference/figma/png/2831-9665-Linux-1024---Wifi-Qucik-Page-Base.png
 D .ai/ui-design/reference/figma/png/2831-9687-Linux-1024---Bluethoot-Qucik-Page-Base.png
 D .ai/ui-design/reference/figma/png/2831-9710-Linux-1024---Playlist-Page---Video-Played.png
 D .ai/ui-design/reference/figma/png/2831-9748-Linux-1024---Select-Gender.png
 D .ai/ui-design/reference/figma/png/2831-9787-Linux-1024---Select-Gender---selected.png
 D .ai/ui-design/reference/figma/png/2831-9826-Linux-1024---Login-Girl.png
 D .ai/ui-design/reference/figma/png/2831-9894-Linux-1024---Register-Girl.png
 D .ai/ui-design/reference/figma/png/2831-9957-Linux-1024---Register-Girl-step-2.png
 D .ai/ui-design/reference/figma/png/2849-21489-Player-nfo.png
 D .ai/ui-design/reference/figma/png/2850-21494-Div2-Button.png
 D .ai/ui-design/reference/figma/png/292-3176-Group-1.png
 D .ai/ui-design/reference/figma/png/292-3177-MacBook-Air---1-1024.png
 D .ai/ui-design/reference/figma/png/296-1760-Buttons-Switch---lite-theme.png
 D .ai/ui-design/reference/figma/png/298-520-Media-List---Buttons-Media-cons.png
 D .ai/ui-design/reference/figma/png/299-3640-Disk-Media-nfo-Battery-cons.png
 D .ai/ui-design/reference/figma/png/319-3696-MacBook-Air---1-1920.png
 D .ai/ui-design/reference/figma/png/326-4366-MacBook-Air---1-3840.png
 D .ai/ui-design/reference/figma/png/370-5913-nputs---lite-theme.png
 D .ai/ui-design/reference/figma/png/462-6772-MacBook-Air---Admin.png
 D .ai/ui-design/reference/figma/png/506-6110-İnput-None-Checkbox-1024.png
 D .ai/ui-design/reference/figma/png/736-7470-Buttons-Switch---dark-theme.png
 D .ai/ui-design/reference/figma/png/850-6190-Test.png
 D .ai/ui-design/reference/figma/png/871-7906-cons.png
 D .ai/ui-design/reference/figma/png/875-8654-Caret-down.png
 D .ai/ui-design/reference/figma/png/878-10567-Other-tems.png
 D .ai/ui-design/reference/figma/png/878-9936-Playlist-Status-Div-Mavi-Light-1024.png
 D .ai/ui-design/reference/figma/png/882-8879-Componets.png
 D .ai/ui-design/reference/figma/png/894-12050-Linux-1024---Wifi-Coonect-Light.png
 D .ai/ui-design/reference/figma/png/932-10547-İnput-None-Checkbox-1920.png
 D .ai/ui-design/reference/figma/png/974-18451-Disk-Buttons-Sistem-Disk-Light-Mavi-1024.png
 D ".ai/ui-design/reference/figma/png/Core Music - Linux Pi.png"
 D ".ai/ui-design/reference/figma/png/Linux  1024 - Home Page.png"
 D ".ai/ui-design/reference/figma/png/Linux - 1920 - Home.png"
 D .ai/ui-design/reference/figma/raw/images-1024-1920.json
 D .ai/ui-design/reference/figma/raw/node-1047-15802.json
 D .ai/ui-design/reference/figma/raw/nodes-1024-1920.json
 D .ai/ui-design/reference/figma/raw/nodes-user-12.json
 D .ai/ui-design/reference/figma/raw/page-1047-15802.json
 D .ai/ui-design/reference/figma/raw/page-15-403.json
 D .ai/ui-design/reference/figma/raw/page-16-106.json
 D .ai/ui-design/reference/figma/raw/page-18-2907.json
 D .ai/ui-design/reference/figma/raw/page-1801-12472.json
 D .ai/ui-design/reference/figma/raw/page-1801-12473.json
 D .ai/ui-design/reference/figma/raw/page-1988-14156.json
 D .ai/ui-design/reference/figma/raw/page-1991-12056.json
 D .ai/ui-design/reference/figma/raw/page-2003-24752.json
 D .ai/ui-design/reference/figma/raw/page-2135-19832.json
 D .ai/ui-design/reference/figma/raw/page-2161-12438.json
 D .ai/ui-design/reference/figma/raw/page-319-2789.json
 D .ai/ui-design/reference/figma/raw/page-326-3386.json
 D .ai/ui-design/reference/figma/raw/page-462-5874.json
 D .ai/ui-design/reference/figma/raw/page-608-11052.json
 D .ai/ui-design/reference/figma/token-conflicts.md
 D .ai/ui-design/reference/figma_1920_main.json
 D .ai/ui-design/reference/legacy-inventory.md
 D .ai/ui-design/screens/00-ascii-art-index.md
 D .ai/ui-design/screens/T07-embedded/album-detail.md
 D .ai/ui-design/screens/T07-embedded/albums.md
 D .ai/ui-design/screens/T07-embedded/bluetooth-quick.md
 D .ai/ui-design/screens/T07-embedded/browse-clicked.md
 D .ai/ui-design/screens/T07-embedded/browse.md
 D .ai/ui-design/screens/T07-embedded/home-dashboard.md
 D .ai/ui-design/screens/T07-embedded/playlist-video.md
 D .ai/ui-design/screens/T07-embedded/playlist.md
 D .ai/ui-design/screens/T07-embedded/singer.md
 D .ai/ui-design/screens/T07-embedded/welcome-popup.md
 D .ai/ui-design/screens/T07-embedded/wifi-connect-light.md
 D .ai/ui-design/screens/T07-embedded/wifi-quick.md
 D .ai/ui-design/screens/T17-monitor-22fhd/home-dashboard.md
 D .ai/ui-design/screens/T17-monitor-22fhd/welcome-popup.md
 D .ai/ui-design/screens/shared/login.md
 D .ai/ui-design/screens/shared/register-step1.md
 D .ai/ui-design/screens/shared/register-step2.md
 D .ai/ui-design/screens/shared/register-step3.md
 D .ai/ui-design/screens/shared/select-gender-selected.md
 D .ai/ui-design/screens/shared/select-gender.md
 D .ai/ui-design/tokens/color-palettes.md
 D .ai/ui-design/tokens/component-tokens.md
 D .ai/ui-design/tokens/design-tokens-master.md
 D .ai/ui-design/tokens/platform-tokens.md
 D .ai/ui-design/tokens/tokens-1024.json
 D .ai/ui-design/tokens/tokens-1920.json
 D .ai/ui-design/tokens/tokens-3840.json
 D .ai/ui-design/tokens/tokens-mobile.json
 D .ai/ui-design/tokens/tokens-plan.json
 D .ai/ui-design/tokens/tokens-system.json
 D .ai/ui-design/tokens/tokens-tv.json
 M AGENTS.md
 D api.coremusic.net.zip
 D assets.coremusic.net.zip
 D auth.coremusic.net.zip
 D home.coremusic.net.zip
 M media.coremusic.net.zip
 M shared.zip
?? .ai.OLD/
?? .ai.zip
?? .ai/.decisions/rejected/R-007-firebase-authentication.md
?? .ai/raw/
?? .ai/sources/
?? .ai/wiki/
?? .work/
?? coremusic.net.zip
2026-10-06 | kök-md + wiki hizalama (A1,B1-B5,C1-C4) | vault-updater | 10 düzeltme
## [2026-10-06 20:17] ingest | architecture (19) + prompt arşivi (20) işlendi — tema bazlı
- **Kapsam kararı (onaylı):** tema bazlı ingest; `architecture.old` (271 dosya) kullanıcı kararıyla **kapsam dışı** — raw kopyası ve 4 arch-old sources/wiki silindi, yeni architecture sayfalarına old referansı yazılmadı
- **raw/:** `raw/architecture/` 19 dosya (robocopy, `.ai.OLD/architecture`) + `raw/prompt-arsivi/` 20 dosya (G: sürücüsü, Coremusic/ 6 + kök 14)
- **sources/ +7:** `arch-katman`, `arch-k0-isletim-sistemi`, `prompt-api`, `prompt-auth`, `prompt-ui`, `prompt-vault-plan`, `prompt-genel`
- **wiki/ +7:** aynı slagonlar (`type: dokuman`) — 39 raw dosya 7 temaya gruplandı; dosya-bazlı 39 sayfa yerine 7 tema sayfası (onaylı granularity)
- **sources/özet.md:** +39 satır → **kaynak sayısı 97**, son güncelleme 19:59 (271 architecture-old satırı ekleme sonrası çıkarıldı)
- **index.md:** Kaynaklar +7 · Vault Dokümanları +7
- **⚠ VERIFICATION REQUIRED:** auth promptları 3 ikiz aday (içerik karşılaştırılmadı) · katmanlı-mimari iki farklı boyutlu kopya · figma promptları ikiz olabilir · b.md/c.md başlıksız
- Etkilenen sayfa: 7 oluşturuldu / 2 güncellendi (index, özet)

## [2026-10-06 20:25] lint | LINT-4 - 16 kontrol: 2 sorun bulundu, 2 duzeltildi (onayli) + 1 yeni bulgu
- Denetlenen: wiki/ 70 - sources/ 44 - raw/ 88 MD - kok 3 = 205 dosya
- **MADDE 10+15 duzeltildi (onayli):** paralel oturumun LINT-3 kaydi (19:45) 20:17 kaydinin SONUNA eklenmisti - LINT-3 bloku kronolojik yerine tasinid (19:36 ile 20:17 arasi); artik 15 kayit kronolojik, son giris 20:17
- Sonuc tekrar: 14 PASS / 0 FAIL (madde 10/15 dahil) / madde 16 INFO
- **YENI BULGU (onay bekliyor):** paralel oturum 
aw/ecosystem/ (8 MD) + 
aw/servers/ (3 MD) eklemis - 11 dosya raw'da ama sources/ozet.md'de kayitli degil (madde 7 FAIL 11) - ingest edilmeli ya da silinmeli
- Not: madde 16'da 8 bolum / Kişiler hala 1 (bayram-ali) - yeni kaynak gerekir
- Etkilenen sayfa: 0 olusturuldu / 1 guncellendi (log.md)
## [2026-10-06 20:28] ingest | raw/ecosystem (8) + raw/servers (3) islendi - tema bazli
- **Tespit:** LINT-4 madde 7 - paralel oturum raw/'a 11 dosya eklemisti, ozet kaydi yoktu
- **sources/ +2:** ecosystem, servers (11 raw dosya 2 temaya gruplandi)
- **wiki/ +2:** ecosystem (8 dosya tablosu), servers (3 dosya tablosu), ikisi de 	ype: dokuman
- **sources/ozet.md:** +11 satir -> **kaynak sayisi 108**, son guncelleme 20:28
- **index.md:** Kaynaklar +2 - Vault Dokumanlari +2
- Etkilenen sayfa: 2 olusturuldu / 2 guncellendi (index, ozet)

## [2026-10-06 20:30] update | servers cift ingest uzlastirmasi + ecosystem link duzeltmesi
- **Cift ingest tespit:** paralel oturum raw/servers/ icin kendi sources/servers-deployment.md + wiki/servers-deployment.md olusturmus; benim ayni tur ekledigim sources/servers.md + wiki/servers.md ile carisiyor - ikisi de SILINMEDI, c cross-link ile baglandi (onaysiz silme yasak)
- **wiki/servers-deployment.md:** kisitli duzeltme - olmayan [[ecosystem-genel]] linki var olan [[ecosystem]] + [[servers]] yapti
- **wiki/servers.md:** [[servers-deployment]] c cross-link eklendi
- **wiki/ecosystem.md:** benim hatam - silinmis arch-old-* sayfalarina 2 link vardi; [[arch-k0-isletim-sistemi]] + [[vault-brain]] ile degistirildi
- **index.md:** Vault Dokumanlari +1 (servers-deployment)
- Etkilenen sayfa: 0 olusturuldu / 5 guncellendi (servers-deployment, servers, ecosystem, index, ozet)

## [2026-10-06 20:33] update | ecosystem- alt sayfalari tamamlandi (paralel index plani)
- **Tespit:** paralel oturum index.md'ye 4 satir eklemis (ecosystem-genel/mimarileri/audio-dsp/donanim) ama wiki sayfalarini yazmamis - LINT madde 6 hayalet 4 link
- **wiki/ +4:** ayni 4 sayfa olusturuldu (onlarin index planina uyum, silme yok) - raw/ecosystem 8 dosya 4 temaya ayrildi; hepsi sources: [ecosystem] baglantili
- Etkilenen sayfa: 4 olusturuldu / 0 guncellendi

## [2026-10-06 20:37] ingest | raw/servers (3) + raw/ecosystem (8) derin okuma katmani - cift katman onayli
- **Kullanici istegi:** `.ai.OLD/servers` + `.ai.OLD/ecosystem` -> `.ai`. Kopya `robocopy` ile 11/11 **MD5 birebir** (raw/ degistirilmedi).
- **Kaynagi okuma:** servers 3 dosya 467 satir **bastan sona** (ben) + ecosystem 8 dosya 3.193 satir **bastan sona** (vault-updater subagent, ses_eedbf27bcffeeG7sV6GWOQRt3w).
- **sources/ +5:** `servers-deployment`, `ecosystem-genel`, `ecosystem-mimarileri`, `ecosystem-audio-dsp`, `ecosystem-donanim`.
- **wiki/ +5:** ayni sluglar (`type: dokuman`), hepsi `## Ilgili Sayfalar` ile `[[ecosystem]]` / `[[servers]]` ozet sayfalarina bagli. **Karar (kullanici, 20:33): CIFT KATMAN** - paralel oturumun `[[ecosystem]]` + `[[servers]]` sayfalari korundu, silme yok.
- **index.md:** Kaynaklar +5 (satir 64-68) · Vault Dokumanlari +5 (satir 160-164).
- **sources/ozet.md:** 11 satir paralel oturum tarafindan ekilmisti (97 -> **108**); bu geciste satir eklenmedi. **Satir -> tema eslemesi:** `asio-wasapi-rehber` + `ses-dsp-acik-kaynak` -> ecosystem-audio-dsp · `ekosistem-mimarileri` + `muzik-streaming-sunuculari` -> ecosystem-mimarileri · `README` + `index` + `service-integration` -> ecosystem-genel · `donanim-devre-referanslari` -> ecosystem-donanim · 3 servers -> servers-deployment.
- **Dogrulama (20:36):** 14 dosya frontmatter `title/type/created/updated/sources/tags` **6/6 OK** · wiki link taramasi **662 link / yeni kirik 0** (18 cozulmeyen: 17'si onceden vardi - vault-todo [[CLAUDE.md]] kalibi, file-based-memory [[link]], ozet [[log]] - + 1 kanonik `[[.decisions/accepted/ADR-089-classab-24v]]`, `.templates/adr/adr-index.md:316` sablonu).
- **Cekirdek bulgular:** (1) katalog drift `README.md:33` Download **IMPLEMENTED** vs ADR-039 **PLANNED** -> ⚠ VERIFICATION REQUIRED · (2) JUCE yildiz **8.811** (index.md) vs **8.011** (ses-dsp/asio) raw ici celiski ⚠ · (3) donanim dosyasi kendi DÜZELTME bloguyla celisiyor (boost/DAC K katmanlari) ⚠ · (4) "11 subdomain" sayisi cikarimdir - README'de 10 panel + akis semasinda api.coremusic.net var, 11 satirlik tablo raw'da YOK.
- **Etkilenen sayfa:** 10 olusturuldu (5 sources + 5 wiki) / 3 guncellendi (index.md, wiki/servers-deployment.md, sources/ozet.md - son ikisi paralel oturumla es zamanli)

## [2026-10-06 23:02] update | agent registry koz+alt+profil senkronu (6 yeni ajan, v22.0.9 / v1.3.1)
- **Kullanic onayiyla** koz `.ai/raw/AGENTS.md` v22.0.8 -> **v22.0.9**: S1 17 ajan, S4 +6 satir (#12-#17), S6 +5 keyword grubu, S15 +6 profil satiri + eklenenti notu, S23 (Agent Count 17 - Routing 14)
- **Alt registry** `.ai/.agents/AGENTS.md` v1.3.0 -> **v1.3.1**: authority (v22.0.9) 3 nokta, S3.3 "kork S15 bekliyor" kapandi (17 satir birebir), S3.1 6 satir isaret -> islendi, S9 changelog
- **6 yeni profil**: S1 + S6 registry bekleyis isareti -> islendi (v22.0.9, 2026-10-06); kalan registry bekleyis **0**
- **.ai/.templates/index.md S5.1** +6 Agent->Template satiri, version 4.9.0 -> **4.9.1** (4 satir VERIFICATION REQUIRED: prompt/analiz dedicated template diskte yok - atama onay bekliyor; Electronics -> hardware/hardware-template.md)
- **Dogrulama:** S15 profil satiri **17/17** - profil bekleyis **0** - kalan `22.0.8` yalniz tarihsel S2.1/S26.2 kaydi - **commit YOK (onay bekliyor)**

## [2026-10-06 23:26] create | wiki/subdomains.md � subdomain baglami (ELI10 + Truth)
- **Kaynak:** kullanici metni `.ai/.subdomains` v2.0.1 (diskte yoktu) -> `.ai/wiki/subdomains.md` olarak yazildi (5 soru ile onayli: kayit yeri, gercek liste, planli odalar, derinlik, log+index)
- **Dogrulama (Truth Mode):** klasorler api/assets/auth/home/media + shared VAR (5/5); shared/src/PageRouter+Middleware+Database+Session+Security+OAuth VAR (6/6); music/download/car/studio/pro klasoru YOK -> "Planlandi"; vhost/DNS config dosyasi depoda YOK -> uyarili (`?`)
- **Yeni oda listesi:** media "Planlandi" -> "Var" tasindi; pro/coremusic eklendi (kullanici: yeni fikirler append-only eklenir)
- **Guncelleme:** `.ai/index.md` Projeler +1 (`[[subdomains]]`); log.md bu giris
- Etkilenen sayfa: 1 olusturuldu / 1 guncellendi (index)
