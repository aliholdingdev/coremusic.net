---
title: "CoreMusic — Sıralı Yürütme Planı"
type: plan
category: project-planning
version: 1.1.0
status: active
authority: SSOT
updated: 2026-10-01
# Control Plane v2 (Q7 geniş şema — 2026-10-07):
tier: 5
domain: task-state
ssot: false
risk: low
owner: "MO"
depends-on: [".ai/CLAUDE.md"]
---

# CoreMusic — Sıralı Yürütme Planı

**Zorunlu Bağlantılar:** [[TODO.md]] · [[CHECKLIST.md]] · [[WORKFLOW.md]] · [[CLAUDE.md]]

---

## §1 Amaç

Bu dosya, kullanıcının **adım adım, tek tek** takip edebileceği tek dosyalık **sıralı yürütme planıdır**. Her adım birebir [[TODO.md]]'deki açık (`[ ]`) bir maddeden türetilmiştir; yeni iş eklenmemiştir (Zero-Hallucination). Bu dosya **yalnızca plandır** — hiçbir adım burada uygulanmaz, kod yazılmaz.

**Kullanım:**

1. Her session'da [[CHECKLIST.md]] §A3 ile bu plan taranır ve **tek** adım seçilir (session başına 1 P0 **ya da** 2-3 P1/P2).
2. Adım bitti → bu dosyada `[ ]` → `[x]` yapılır **ve** [[TODO.md]]'de de aynı madde kapatılır (kapanış [[CHECKLIST.md]] §C3).
3. Yeni iş çıkarsa önce [[TODO.md]]'ye 1 satır + kaynakla eklenir; bu plana sonradan eklenir — uydurma madde buraya yazılmaz.

---

## §2 Sıra (Adım Adım — 19 adım)

**Sıra mantığı:** P0 (§2) → P1 (§3) → P2 (§4); numaralar bu sırayı izler.

### P0 — Depo bütünlüğü & çalışır durum (Adım 1-5)

#### Adım 1 — Commit bekleyen 105 değişikliği sınıflandır
- **Öncelik:** P0 ([[TODO.md]] §2, satır 33)
- **Ne yapılacak:** `git status --porcelain` çıktısındaki 63 M / 25 D / 17 `??` değişikliği sınıflandırıp commit'e hazırlanacak.
- **Etkilenen dosya(lar):** çalışma ağacı (git durumu — dosya içeriği değil)
- **"Bitti" ölçütü:** `git status --porcelain` sayıları sınıflandırılmış; M/D/?? her satırın commit planı yazılı.
- **Bağımlılık:** — (git ölçümü anlık, 2026-09-29)

#### Adım 2 — Interface → Contract taşımasını tamamla
- **Öncelik:** P0 (§2, satır 34)
- **Ne yapılacak:** `shared/src/Interfaces/**` (14 D) ve `shared/src/AI/Contracts/**` (7 D) ile `shared/src/Contracts/{AI,Auth,Config,Database,Middleware,Security}/**` (6 dizin, tracked değil) için taşıma stage edilecek **veya** revert edilecek.
- **Etkilenen dosya(lar):** `shared/src/Interfaces/**`, `shared/src/AI/Contracts/**`, `shared/src/Contracts/**`
- **"Bitti" ölçütü:** İlgili D/?? satırları git status'ta karara göre stage edilmiş ya da revert edilmiş (karar seçeneği tek).
- **Bağımlılık:** Adım 1 (sınıflandırma)

#### Adım 3 — auth testlerindeki çift ağacı tek ağaç yap
- **Öncelik:** P0 (§2, satır 35)
- **Ne yapılacak:** HEAD'de hem `tests/Domain/**` (4 dosya) hem `tests/Unit/Domain/**` (6 dosya) var; eski 4 yolu stage ederek silinmeyi onayla.
- **Etkilenen dosya(lar):** `auth.coremusic.net/tests/Domain/**`, `auth.coremusic.net/tests/Unit/Domain/**`
- **"Bitti" ölçütü:** `git ls-files auth.coremusic.net/tests` tek ağaç gösterir; D satırları onaylanmış.
- **Bağımlılık:** Adım 1

#### Adım 4 — Kökteki stash/artık dosyaları temizle
- **Öncelik:** P0 (§2, satır 36)
- **Ne yapılacak:** `.ai.zip`, `coremusic.net.zip`, `aaa.md`, `output.md` (untracked) silinecek / arşivlenecek / `.gitignore`'a eklenecek; `.ai.zip` için önce REDACTED kontrolü.
- **Etkilenen dosya(lar):** kök dosyalar (4), `.gitignore` (mümkünse)
- **"Bitti" ölçütü:** `git status` bu 4 dosyayı `??` olarak göstermez; `.ai.zip` REDACTED kontrolü yapılmış.
- **Bağımlılık:** — (.ai.zip REDACTED kontrolü bu adımın içindedir)

#### Adım 5 — Untracked kaynak varlıklarını commit et
- **Öncelik:** P0 (§2, satır 37)
- **Ne yapılacak:** `.gitleaks.toml`, `.ai/.decisions/accepted/ADR-048-…/ADR-049-…/ADR-050-…` (3 dosya), `home.coremusic.net/include/Repository/`, `shared/src/OAuth/OAuthRepository.php` commit edilecek.
- **Etkilenen dosya(lar):** bu 4 untracked küme
- **"Bitti" ölçütü:** `git status --porcelain` bu yollar için `??` satırı göstermez.
- **Bağımlılık:** Adım 1
- **Not:** commit'i orchestrator atar (subagent atmaz — [[CLAUDE.md]] §13.9/5).

### P1 — Vault tutarlılığı & karar bekleyenler (Adım 6-12)

#### Adım 6 — ADR numara çelişkisini çöz
- **Öncelik:** P1 ([[TODO.md]] §3, satır 41)
- **Ne yapılacak:** Kök `CLAUDE.md` "next new = 091", `.ai/.decisions/index.md` "ADR-038 → 092", `accepted/ADR-092-…` diskte var, `.ai/CLAUDE.md` §29 "001-089" → tek sayıya hizalanacak.
- **Etkilenen dosya(lar):** kök `CLAUDE.md` L16, `.ai/.decisions/index.md` §2, `.ai/CLAUDE.md` §29
- **"Bitti" ölçütü:** Üç kaynakta aynı ADR numara aralığı yazıyor.
- **Bağımlılık:** Vault Steward onayı

#### Adım 7 — §27A skills tablosunu düzelt
- **Öncelik:** P1 (§3, satır 42)
- **Ne yapılacak:** `.ai/CLAUDE.md` §27A'daki 10 satırlık liste, diskteki 8 SKILL.md ile `.ai/index.md` §11B'ye göre hizalanacak.
- **Etkilenen dosya(lar):** `.ai/CLAUDE.md` §27A
- **"Bitti" ölçütü:** Tablo satır sayısı disk gerçeği (8) ile tutarlı; "DOĞRULAMA GEREKLİ" notu kapanır.
- **Bağımlılık:** `.ai/index.md` §11B (kaynak)

#### Adım 8 — dead-mark 8 hedefi karara bağla
- **Öncelik:** P1 (§3, satır 43)
- **Ne yapılacak:** Eşdeğer dosya üretilirse `⚠️ DEAD (faz6-D)` etiketi kaldırılıp retarget edilecek; 1 `blocked` satır sahip kararı bekliyor.
- **Etkilenen dosya(lar):** `.ai/broken-links-report.md` §B (8 hedef)
- **"Bitti" ölçütü:** 8 dead-mark için karar yazılı; blocked satır kapatılmış.
- **Bağımlılık:** Sahip kararı (TODO'da karar bekleyen 1 satır)

#### Adım 9 — H026/H027 sözlük düzeltmesini kapat
- **Öncelik:** P1 (§3, satır 44)
- **Ne yapılacak:** truth/hallucination terimleri `.ai/CLAUDE.md` #15/16 ile işaretli ama `glossary.md` düzeltilmedi → düzeltme yapılacak.
- **Etkilenen dosya(lar):** `.ai/glossary.md`
- **"Bitti" ölçütü:** `glossary.md` H026/H027 terimleri #15/16 ile tutarlı; madde `[x]`.
- **Bağımlılık:** — (TODO'da "vault-updater'a bırakılmış")

#### Adım 10 — Çakışan skill kurallarını tek kurala indir
- **Öncelik:** P1 (§3, satır 45)
- **Ne yapılacak:** `orchestration` "max 400 satır" ↔ `skill-maker` "max 2000"; `truth-engine` "web arama yasak" ↔ `prompt-maker`/`skill-maker" zorunlu arama → karar + skill güncelleme.
- **Etkilenen dosya(lar):** ilgili SKILL.md dosyaları (`.opencode/skills/`)
- **"Bitti" ölçütü:** Her çakışma çifti için tek kural yazıldı; iki skill aynı konuda çelişmiyor.
- **Bağımlılık:** Karar (kullanıcı/Vault Steward)

#### Adım 11 — composer-sync frontmatter/dup-ID kararını ver
- **Öncelik:** P1 (§3, satır 46)
- **Ne yapılacak:** `.claude/skills/composer-sync` `name`/`description` taşımıyor, duplicate ID var → frontmatter eklensin mi kararı verilecek.
- **Etkilenen dosya(lar):** `.claude/skills/composer-sync/SKILL.md`
- **"Bitti" ölçütü:** Karar yazılı (eklenecek/eklenmeyecek) ve uygulanmış.
- **Bağımlılık:** Karar

#### Adım 12 — Post-op sync scriptlerini karara bağla
- **Öncelik:** P1 (§3, satır 47)
- **Ne yapılacak:** `.ai/scripts/session-save.mjs`, `vault-post-update.mjs`, `vault-cmd.mjs` ve `project-state.md` diskte YOK → script üretilsin mi kararı verilecek (üretim = kod, onay gerektirir).
- **Etkilenen dosya(lar):** `.ai/scripts/` (üretim kararı), kapanış akışı (manuel)
- **"Bitti" ölçütü:** Karar verildi (üret / manuel devam) ve `MEMORY.md` §20 Known Issue'ya işlendi.
- **Bağımlılık:** Kullanıcı onayı (kod üretimi)

### P2 — Kalite & teknik borç (Adım 13-19)

#### Adım 13 — Coverage raporu üretil
- **Öncelik:** P2 ([[TODO.md]] §4, satır 51)
- **Ne yapılacak:** Mevcut phpunit sayıları (`shared 216/545`, `auth unit 31/59`, `home 23/54`) coverage raporuyla tamamlanıp `.ai/CLAUDE.md` §17 hedefi (≥%80) ile karşılaştırılacak.
- **Etkilenen dosya(lar):** coverage raporu (çıktı), karşılaştırma `.ai/CLAUDE.md` §17
- **"Bitti" ölçütü:** Üç projede de yüzde değer raporlanmış; §17 ile karşılaştırma tablosu yazılı.
- **Bağımlılık:** phpunit çalıştırma (log'daki son sayılar kaynak)

#### Adım 14 — `/stream` endpoint'i için PHPUnit HTTP testi yok
- **Öncelik:** P2 (§4, satır 52)
- **Ne yapılacak:** curl kanıtı logta olan `/stream` endpoint'i için PHPUnit HTTP testi eklenecek (suite'e dokunulmadı).
- **Etkilenen dosya(lar):** ilgili test suite'i (dosya yolu TODO'da belirtilmemiş → **UNKNOWN**, yazım anında tespit edilecek)
- **"Bitti" ölçütü:** Yeni test suite içinde geçiyor (`phpunit` yeşil).
- **Bağımlılık:** —
- **Not:** Bu adım KOD içerir — ayrı session, Guardrail #1 (Zero Code Before Plan).

#### Adım 15 — §8 Soft Constraint #3'ü kapat
- **Öncelik:** P2 (§4, satır 53)
- **Ne yapılacak:** `.ai/CLAUDE.md` §8 Soft Constraint #3 "DOĞRULAMA GEREKLİ (Vault Steward)" → satır eklenecek ya da madde kaldırılacak.
- **Etkilenen dosya(lar):** `.ai/CLAUDE.md` §8 (§31-5 notu)
- **"Bitti" ölçütü:** §8'de `DOĞRULAMA GEREKLİ` etiketi kalmadı (eklendi ya da kaldırıldı).
- **Bağımlılık:** Vault Steward onayı

#### Adım 16 — §7.2/§31 geçersiz referanslarını düzelt
- **Öncelik:** P2 (§4, satır 54)
- **Ne yapılacak:** `.ai/CLAUDE.md` §7.2 "ROLE.md §435 → Guardrail #15" (#2 olmalı) ve §31 "§12A" (böyle bölüm yok) → ikisi de kapsam dışı bırakılan VR olarak işaretlenecek.
- **Etkilenen dosya(lar):** `.ai/CLAUDE.md` §7.2, §31
- **"Bitti" ölçütü:** İki referans düzeltildi ya da VR olarak işaretlendi; taramada geçersiz referans kalmadı.
- **Bağımlılık:** —

#### Adım 17 — Vault metin hijyeni temizlik kararı
- **Öncelik:** P2 (§4, satır 55)
- **Ne yapılacak:** `log.md` içinde mojibake 6 / CJK 12 + 423 dosyada frontmatter yok (zorla eklenmedi) → temizlik kararı verilecek.
- **Etkilenen dosya(lar):** `.ai/log.md`, 423 frontmatter'siz dosya (temizlik kararı sonrası)
- **"Bitti" ölçütü:** Karar yazıldı (temizle / temizleme) + kapsamı net.
- **Bağımlılık:** Karar

#### Adım 18 — 14 case-variant duplicate başlık + NULL süreler
- **Öncelik:** P2 (§4, satır 56)
- **Ne yapılacak:** MVP'de kabul edildi → ileride import pass için not tutulacak (bu adımda sadece planlama/kayıt).
- **Etkilenen dosya(lar):** [[TODO.md]] kaydı (import pass kapsamı)
- **"Bitti" ölçütü:** Import pass kapsamı TODO'ya 1 satır + kaynakla yazıldı.
- **Bağımlılık:** — (ffprobe yok — bilinen kısıt)

#### Adım 19 — `.ai/index.md` §3 satır 15 düzeltmesi
- **Öncelik:** P2 (§4, satır 57)
- **Ne yapılacak:** `TECHNICAL_DOCUMENTATION.md` "diskte yok" kaydı dosyanın kendi VR notu → kayıt düzeltilecek.
- **Etkilenen dosya(lar):** `.ai/index.md` §3 satır 15
- **"Bitti" ölçütü:** Satır disk gerçeğiyle tutarlı; VR notu doğru yere taşındı.
- **Bağımlılık:** —

---

## §3 Oturum Planı

[[CHECKLIST.md]] §A3 kuralı: session başına **tek** P0 satırı; kural talimatı "session başına 1 P0 ya da 2-3 P1/P2" → 19 adım **11 session**'a dağıtır.

| Session | Adımlar | Öncelik | Odak (CHECKLIST akışı) |
|---|---|---|---|
| S1 | 1 | P0 | §A3 tek P0 seç → git durumu sınıflandırma |
| S2 | 2 | P0 | Interface → Contract kararı |
| S3 | 3 | P0 | auth tests tek ağaç |
| S4 | 4 | P0 | kök artık dosyalar + REDACTED |
| S5 | 5 | P0 | untracked commit (orchestrator commit) |
| S6 | 6, 7, 8 | P1 | vault tutarlılık (3 madde) |
| S7 | 9, 10, 11 | P1 | glossary + skill çakışmaları (3 madde) |
| S8 | 12 | P1 | post-op script kararı (onay bekliyor) |
| S9 | 13, 14, 15 | P2 | coverage + test + §8 (3 madde) |
| S10 | 16, 17, 18 | P2 | referans/hijyen/duplicate (3 madde) |
| S11 | 19 | P2 | index.md düzeltmesi + plan kapanışı |

**Dağılım:** 5 session × 1 P0 · 3 session × P1 (3+3+1) · 3 session × P2 (3+3+1) = **11 session**.
Her session [[CHECKLIST.md]] §A (başlama) → §B (orta) → §C (kapanış, `[[TODO.md]]` + bu dosya `[x]`) akışına bağlıdır.

---

## §4 Durum Tablosu

| Adım | Öncelik | Kaynak (TODO.md) | Durum |
|---|---|---|---|
| 1 | P0 | §2 satır 33 | [ ] |
| 2 | P0 | §2 satır 34 | [ ] |
| 3 | P0 | §2 satır 35 | [ ] |
| 4 | P0 | §2 satır 36 | [ ] |
| 5 | P0 | §2 satır 37 | [ ] |
| 6 | P1 | §3 satır 41 | [ ] |
| 7 | P1 | §3 satır 42 | [ ] |
| 8 | P1 | §3 satır 43 | [ ] |
| 9 | P1 | §3 satır 44 | [ ] |
| 10 | P1 | §3 satır 45 | [ ] |
| 11 | P1 | §3 satır 46 | [ ] |
| 12 | P1 | §3 satır 47 | [ ] |
| 13 | P2 | §4 satır 51 | [ ] |
| 14 | P2 | §4 satır 52 | [ ] |
| 15 | P2 | §4 satır 53 | [ ] |
| 16 | P2 | §4 satır 54 | [ ] |
| 17 | P2 | §4 satır 55 | [ ] |
| 18 | P2 | §4 satır 56 | [ ] |
| 19 | P2 | §4 satır 57 | [ ] |

**Toplam:** 19 açık madde (P0 5 · P1 7 · P2 7) — sayı [[TODO.md]] §2/§3/§4'teki `[ ]` sayımından türetildi (2026-10-01 okuma).

---

## §5 Doğrulama / Validation

- [ ] 19 adım = [[TODO.md]] açık madde sayısı (5+7+7); yeni iş eklenmedi (Zero-Hallucination)
- [ ] Her adımda TODO.md § numarası + orijinal satır aralığı korunmuş (§2 33-37 · §3 41-47 · §4 51-57)
- [ ] Sıra P0 → P1 → P2; bağımlılıklar yalnız TODO'da yazılı olanlardan (onay bekleyenler: adım 6, 10, 11, 12, 15, 17)
- [ ] [[CHECKLIST.md]] §A3/§C3 ile kapanış çifti tutarlı (bu dosya `[x]` + TODO `[x]`)
- [ ] [[TODO.md]] §5 Uyum Kontrolü ile çelişki YOK (bu plan yeni doküman üretmez, yalnız sıralar)
- [ ] Frozen ADR (001-037), dosya adı ve secret'a dokunulmadı (REDACTED)
- [ ] Wiki-link'ler `[[relative/path]]` formatında: [[TODO.md]] · [[CHECKLIST.md]] · [[WORKFLOW.md]] · [[CLAUDE.md]]

**Tutarlılık notu:** [[TODO.md]] §5'teki kontroller bu planı da kapsar; bu dosya TODO'yu değiştirmez, yalnız sıralar. Çelişki halinde [[TODO.md]] (kaynak) kazanır, bu plan düzeltilir.

---

## §6 UI Görevi — Home Page Rebuild (aaa.md · Kalıp C)

> **Bu bölüm §2'deki 19 vault adımından FARKLI bir yazılım geliştirme görevidir** — §2-§5'teki 19 adımı, 11 session'ı ve durum tablosunu **etkilemez** (kapanış yine [[CHECKLIST.md]] §A3/§C3 çiftiyle [[TODO.md]] üzerinden yürür). Görevin **tek kaynağı** `aaa.md` (Kalıp C: *Home Page Whole-Page Rebuild + Multi-Resolution Verify (1024→3840)*). Süreç aşamaları: [[WORKFLOW.md]].

### §6.1 Kapsam özeti

- **Dokunulmaz yüzeyler:** header, footer/player bar, `_player-info.css`, `PlayerInfoComponent.js`, `player-info.php` — ihlal = görev reddi (`aaa.md §Senaryo Kapsam`).
- **İki ayrı sayfa:** 1024 (T07 embedded, 1024×600, **12** widget slot) ve 1920 (T17 desktop, 1920×1080, **20** widget slot) — asla birbirine karıştırılmaz; her breakpoint kendi mockup'ıyla ölçülür (`aaa.md §Senaryo`, §13.9).
- **Bilinen hatalar:** track btn & playlist btn bozuk (etiket + oynat/aksiyon davranışı yanlış) → mockup'taki gibi çalışacak (tıklama → doğru eylem, hover/focus state'leri, PNG ile birebir); yanlış render'lar tespit edilip düzeltilecek, doğrular bırakılacak (`aaa.md §Senaryo Bilinen hatalar`).
- **Dosya kuralı:** **yeni dosya YOK** — mevcut dosyalarda mevcut satırlarda edit (HTML/CSS/PHP/JS); CSS yalnız `assets.coremusic.net/Css/` (SSOT) (`aaa.md §Senaryo Kapsam`).
- **Mockup Gate:** kod ÖNCE 3 PNG okunur — okunamazsa **DUR** (`aaa.md §Senaryo Mockup Gate`).
- **Ölçü/token:** yalnız PNG + Figma raw + `01_Abstracts`; uydurma ölç, tahmin token, `clamp()` varsayımı yasak (`aaa.md §Senaryo Kapsam`).

### §6.2 Adımlar (Dispatch — 9 adım · `[ ]` todo)

1. [ ] **Adım 1 — [Keşif] Referansları topla ve oku.** Toplama komutlarını çalıştır; 3 PNG + `figma/png` + raw JSON oku (ölçü uydurma yasak); raw yetmezse Figma Data API adım adım (`figma-extract.ps1` → `/v1/files/:key/nodes` → `/v1/images/:key`), token yalnız `.ai/.env.figma`.
   - **"Bitti" ölçütü:** 3 PNG okunabilir (Mockup Gate geçildi) + raw JSON ölçüsü çapraz doğrulandı; okunamadıysa DUR — hiçbir ölçüt yazılmamış.
   - **Bağımlılık:** — (ilk adım) · **Kaynak:** `aaa.md §Dispatch ad.1`
2. [ ] **Adım 2 — [Ayıklama] Blokları kendi mockup'ıyla karşılaştır.** 1024↔1024, 1920↔1920 (KARIŞTIRMA); DOĞRU = dokunma · HATALI = listele (dosya:satır + ne yanlış); 10 dk'da bitir.
   - **"Bitti" ölçütü:** Her blok için DOĞRU/HATALI listesi yazılı (her hatalı satır: dosya:satır + ne yanlış) — hatalı blok kalmamış listelenmemiş.
   - **Bağımlılık:** Adım 1 · **Kaynak:** `aaa.md §Dispatch ad.2`
3. [ ] **Adım 3 — [Edit] Sadece mevcut dosyalarda düzelt.** ITCSS katmanına uygun CSS dosyası; yeni dosya YOK; inline `style`/`script` YOK; ham hex/px yalnız `01_Abstracts`.
   - **"Bitti" ölçütü:** `git status` → yeni dosya YOK; inline style/script yok; ham hex/px yalnız `01_Abstracts` (`aaa.md §Validation` 6, 7).
   - **Bağımlılık:** Adım 2 · **Kaynak:** `aaa.md §Dispatch ad.3`
4. [ ] **Adım 4 — [Butonlar] Track btn & playlist btn düzelt.** Etiket + aksiyon + hover/focus/active state'leri PNG/Figma birebir.
   - **"Bitti" ölçütü:** Tıklama doğru eylemi üretir; etiket/state PNG ile birebir (`aaa.md §Validation` 4).
   - **Bağımlılık:** Adım 3 · **Kaynak:** `aaa.md §Dispatch ad.4`
5. [ ] **Adım 5 — [Kural] Widget grid slot kuralını uygula.** §13.9: 1024 → 12 · 1920 → 20 slot; çelişkide kural PNG'ye precedanslı.
   - **"Bitti" ölçütü:** 1024 → 12 slot · 1920 → 20 slot (`aaa.md §Validation` 5).
   - **Bağımlılık:** Adım 4 · **Kaynak:** `aaa.md §Dispatch ad.5`
6. [ ] **Adım 6 — [Popup] 1024 welcome popup'ı bağla.** Mevcut `c-modal.css` / `_welcome-banner.css`; Başla → localStorage.
   - **"Bitti" ölçütü:** Başla → localStorage → ikinci girişte görünmez (`aaa.md §Validation` 10).
   - **Bağımlılık:** Adım 5 · **Kaynak:** `aaa.md §Dispatch ad.6`
7. [ ] **Adım 7 — [Test] Genişlik süpürmesi (8 genişlik).** `TEST_MODE=true` · `http://home.coremusic.net:81/home`; genişlikler `1024, 1524, 1920, 2024, 2524, 3024, 3524, 3840` (yükseklik tier'e göre 600/1080/2160); her genişlikte screenshot + DOM ölçü; bozuk çıktı → düzelt → YENİDEN render; cookie `cm_viewport_w` lag: resize ÖNCE navigate, gerekirse 2× yükle; cache takılıysa CDP `Network.clearBrowserCache` + `?v` bump.
   - **"Bitti" ölçütü:** 8 genişlikte de screenshot var; Console 0 hata; CSP violation yok; Player Info regression yok; bozuk çıktı kalmamış (`aaa.md §Validation` 3, 7, 11).
   - **Bağımlılık:** Adım 6 · **Kaynak:** `aaa.md §Dispatch ad.7`
8. [ ] **Adım 8 — [Git gate] Push yok, sinyal bekle.** COMMIT/PUSH SONRASI DUR → "git pull bekleniyor" de; kullanıcı "git pull çalıştır" der → pull al; kullanıcı diğer PC'de pull yapıp "devam" der → 8 genişlik screenshot ile KONTROL ET.
   - **"Bitti" ölçütü:** Kullanıcı sinyali alınmadan push/pull/deploy YOK; sinyal sonrası 8 genişlik kontrolü tamam, hata yok (`aaa.md §Validation` 6; `aaa.md §Git Akışı` NOT).
   - **Bağımlılık:** Adım 7 · **Kaynak:** `aaa.md §Dispatch ad.8`
9. [ ] **Adım 9 — [Sonuç] Raporu teslim et.** 8 screenshot + düzeltme tablosu (dosya · satır · ne değişti · neden) + Validation checklist + `git status` (yeni dosya yok kanıtı).
   - **"Bitti" ölçütü:** 8 screenshot, düzeltme tablosu, 11 maddelik Validation ve `git status` kanıtı teslim edildi (`aaa.md §Validation` 6).
   - **Bağımlılık:** Adım 8 · **Kaynak:** `aaa.md §Dispatch ad.9`

### §6.3 Validation (kontrol kutuları — birebir)

- [ ] 1024×600 screenshot → mockup birebir (12 slot, 2 kolon, Sıradaki, popup)
- [ ] 1920×1080 screenshot → mockup birebir (20 slot, welcome-banner, tek satır listeler)
- [ ] **Çok çözünürlük süpürmesi:** genişlik `1024, 1524, 1920, 2024, 2524, 3024, 3524, 3840` → her birinde screenshot; bozuk olanları düzelt + yeniden render (500px adım + 1920 zorunlu ara nokta)
- [ ] Track btn & playlist btn: tıklama doğru eylemi üretir, etiket/state PNG ile birebir
- [ ] Widget slot: 1024 → 12 · 1920 → 20
- [ ] `git diff` → header/footer/player-info değişikliği YOK; `git status` → yeni dosya YOK
- [ ] Ham hex/px yalnız 01_Abstracts; inline style/script yok; Console 0 hata; CSP violation yok
- [ ] BEM `block__element--modifier`; `innerHTML` yok
- [ ] WCAG 2.2 AA: kontrast ≥ 4.5:1; focus-visible `2px solid var(--color-primary)` — ⚠️ İSTİSNA (kullanıcı kararı 2026-10-01): bölüm başlığı kontrastı 3.14, PNG mockup 2.76 → **PNG korunur** (birebir mockup kuralı > WCAG); scrim eklenmez. Madde "istisna" sayılır.
- [ ] Popup: Başla → localStorage → ikinci girişte görünmez
- [ ] Regression: Player Info 392×131 / 469×184 / 750×300 bozulmadı

*(Kaynak: `aaa.md §Validation` — 11 madde, uydurma ekleme/çıkarma yok.)*

### §6.4 Git akışı uyarısı (4 adım — ihlal = dur)

1. Uzman kodu yazar + yerel doğrular. **COMMIT/PUSH YAPMAZ** (veya commit eder, **push ETMEZ**).
2. Uzman DURUR → orkestratöre: "değişiklikler hazır, git pull bekleniyor".
3. Orkestratör kullanıcıya sorar; kullanıcı "git pull çalıştır" der → repo'da `git pull` ALINIR (pull = yerel güncelleme sinyali).
4. Kullanıcı diğer server PC'de `git pull` çalıştırıp "devam" der → uzman sunucuda 8 genişlik screenshot süpürmesi + Validation ile KONTROL EDER; hata varsa 2-3'e döner.

**NOT:** Kullanıcı sinyali olmadan push/pull/deploy ATLANMAZ — **uzman push ETMEZ, kullanıcı sinyali beklenir.** *(Kaynak: `aaa.md §Git Akışı`.)*

---

## §7 Referanslar

| Hedef | İlişki |
|---|---|
| [[TODO.md]] | Bu planın tek kaynağı (§2/§3/§4 maddeleri) |
| [[CHECKLIST.md]] | Session baş/orta/kapanış akışı (§A3 seçim, §C3 kapanış) |
| [[WORKFLOW.md]] | Süreçler, §8.7 bitiş adımları |
| [[CLAUDE.md]] | Guardrail'ler (#1 Zero Code Before Plan, #16 şablon) |
| `.ai/.templates/index.md` | Şablon registry (Guardrail #16) |
| `aaa.md` | §6 UI Görevi tek kaynağı (Kalıp C promptu) |

| Tarih | Versiyon | Değişiklik |
|---|---|---|
| 2026-10-01 | 1.0.0 | İlk üretim — TODO.md açık maddelerinden 19 adımlık sıralı plan |
| 2026-10-01 | 1.1.0 | §6 UI Görevi eklendi (aaa.md · Kalıp C — 9 adım + 11 validation + git uyarısı); §1-§5 değişmedi; eski "§6 Referanslar" → §7 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-01
**Mode:** Red Team · Human Mode · Truth Mode
