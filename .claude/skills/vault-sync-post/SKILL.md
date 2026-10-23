---
name: vault-sync-post
description: "Use when closing a session or finishing an operation and the vault must be synced — root .md touchpoints, .claude/.opencode sync, and append-only log.md line — Tetikleyiciler: 'vault güncelle', 'session kapat', 'senkronize', 'log yaz', 'vault-post-update', 'session sync'."
license: MIT
metadata:
  version: 4.0.0
  format: claude-skill-v3
  author: Bayram Ali (ULTRATHINK Engineering)
  category: vault-management
  tags: [vault, session, sync, automation, post-operation]
  updated: 2026-10-21
  previous-version: "3.0.0"
---

# Vault Sync Post — İşlem Sonrası / Session Kapanışı Vault Senkronizasyonu

> **Ne yapar:** Bir görev veya session bittiğinde `.ai/` vault'unu ve kök touchpoint
> dosyalarını **kapanış gerçeğiyle** hizalar: session kaydı, touchpoint tazelik,
> `MEMORY.md` §20 state, `log.md` append-only kayıt, doğrulama ve rapor.
>
> **Kural otoritesi:** SSOT `.ai/SYNC.md` (26 dosyalık hedef set + üç kapı) ve
> `.workflows/vault-sync.md` (7 aşama + Aşama 8 post-op). Bu skill bu iki kaynağın
> **uygulama kılavuzudur**; çelişkide onlar (ve disk) kazanır.
>
> **Güncellik:** 2026-10-21 disk ölçümüne dayanır. Disk gerçeği ile eski sürüm
> iddiaları arasındaki farklar §9'da açıkça listelenmiştir (Zero-Hallucination).

**eli10 (basit):** İş bitince "bitti" demeden önce vault'u tazele: kaydı yaz, değişen
dosyaları 2-4 satırla güncelle, log'a ekle, doğrula, rapor et. Geçmişi asla silme.

**eli15 (detay):** Vault dosyaları sessiz bayatlar — sayım değişir, ADR eklenir, dizin
kapanır. Bayatlık yakalanmazsa sonraki session yanlış sayıya, kırık bağlantıya veya
olmayan bir dosyaya dayanır (Zero-Hallucination ihlali). Bu skill kapanışı **8 adımlı,
kanıt zorunlu, append-only** bir akışa oturtur; otomasyon araçları diskte olmadığı için
(§9) akış manuel yürür ama adımları atlanmaz. Kapanış C1-C5 kapıları
(`.ai/CHECKLIST.md` §C) tamamlanmadan session "tamamlandı" sayılmaz.

---

## §1 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Session/görev kapanışında vault senkronizasyonu (kapı C) | Session **başlangıcı** boot akışı (`.workflows/session-init.md`) |
| 26 hedef dosyalık tazelik setinin kapanış görünümü (`.ai/SYNC.md` §3) | Dosyaların içeriği — her dosyanın kendi SSOT'u |
| `log.md` append protokolü + dedup + rollback | ADR üretimi ve frozen ADR yönetimi (`.workflows/adr-creation.md`) |
| `MEMORY.md` §20 state + `TODO.md` kapanış satırı | Kod yazımı, test, deploy süreçleri |
| Doğrulama (verify/wiki-link/halüsinasyon sweep) + kapanış raporu | `git commit` — subagent ATMAZ, orkestratöre aittir |

- **Kullananlar:** MO (birincil — vault-updater routing'i `.ai/AGENTS.md` §6), görevi yapan
  agent (kendi kapanışını hazırlar), orkestratör (commit kararı).
- **Ön koşul:** Görevin kendisi tamamlanmış olmalı; vault yazım kanalı
  `.ai/scripts/vault-utf8-writer.mjs` diskte mevcut (2026-10-21 doğrulandı).
- **Tetikleyiciler (description ile aynı):** 'vault güncelle' · 'session kapat' ·
  'senkronize' · 'log yaz' · 'vault-post-update' · 'session sync'.

---

## §2 Hızlı Karar Ağacı (kapanış başlamadan)

```text
GÖREV/SESSION BİTTİ Mİ?
  ├─ HAYIR → bu skill kullanılmaz (devam eden işte orta kapı §B yeterli)
  └─ EVET → C1 kanıt hazır mı?
       ├─ HAYIR → DUR. "Tamamlandı" iddiası için kanıt üret (test/çıktı/dosya).
       │          Kanıtsız iddia = VERIFICATION REQUIRED (CHECKLIST C1).
       └─ EVET → Adım 1-8'i sırayla uygula (§4)
            → Doğrulama §7 geçti mi?
                 ├─ EVET → kapanış raporu + commit kararı orkestratöre
                 └─ HAYIR → §8 hata modları / rollback → tekrar Adım 5-7
```

**Erken çıkış kuralı:** Bu session'da vault'a hiç dokunulmadıysa bile kapanış
**boş değildir**: `log.md` append (Adım 5) + `MEMORY.md` §20 (Adım 4) + C5
"Sonraki adım" satırı yine yazılır. Değişmeyen touchpoint dosyalarına dokunulmaz
(§3 kural 4).

---

## §3 Temel Kurallar (bağlayıcı — her adımda geçerli)

| # | Kural | Neden | Kaynak |
|---|-------|-------|--------|
| 1 | Yazım yalnız `vault-utf8-writer.mjs` üzerinden (modlar §6.1) | PowerShell yazım cmdlet'leri Windows-1254/BOM/UTF-16 bozulması üretir | `.ai/CLAUDE.md` UTF-8 protokolü · `.workflows/vault-sync.md` §6 |
| 2 | `log.md` yalnız append; geçmiş satıra dokunulmaz | Denetim izi (audit trail); git log semantiği: kayıt yalnız eklenir | Kök `AGENTS.md` · web kaynak §10-W1 · ADR-042 |
| 3 | Update in-place; dosya adı/taşıma yok | ~yüzlerce wiki-link dosya yoluna bağlı | Guardrail #4 · `.ai/SYNC.md` §1 |
| 4 | Değişmeyen dosya zorla yeniden yazılmaz | Gereksiz diff = inceleme kirliliği + bozulma riski | `.ai/CHECKLIST.md` §C-Güçlendirme |
| 5 | Subagent `git commit` ATMAZ | Commit kararı ve tarihi orkestratörde | Kök `AGENTS.md` §7/§8 · `.ai/AGENTS.md` §25.3 |
| 6 | Secret asla vault'a yazılmaz (`[REDACTED]`) | Anahtar sızıntısı | Kök `AGENTS.md` §3 · Guardrail |
| 7 | Doğrulanmayan iddia → `⚠️ VERIFICATION REQUIRED` | Zero-Hallucination | Kök `AGENTS.md` §3 |
| 8 | Frozen ADR (001-037) metni dokunulmaz; yeni ADR ≥088 | Karar geçmişi korunur | Vault kuralı (guardrail #3) |
| 9 | Sayı iddiası ölçümle kanıtlanır (tarihli) | Uydurma sayı vault'u zehirler | Zero-Hallucination · `.ai/SYNC.md` §1 |
| 10 | Yeni `.md` üretimi şablondan (Guardrail #16) | Tutarlı iskelet | `.ai/.templates/` |

---

## §4 Kapanış Akışı — 8 Adım

> Her adımın **çıktısı** ve **kanıtı** vardır. Adımlar sıralıdır; 5-7 (log → doğrulama)
> tekrarlanabilir hata durumunda (§8). Aşama numaraları `.workflows/vault-sync.md`
> 7 aşamasıyla hizalıdır; Adım 0-1 = Aşama 1-3, Adım 2-3 = Aşama 4, Adım 5 = Aşama 6,
> Adım 6 = Aşama 5+7, Adım 7 = Aşama 8.

### Adım 0 — Durum Toplama (salt-okunur)

| # | Aksiyon | Çıktı |
|---|---------|-------|
| 0.1 | Tamamlanan görevi tek cümleyle tanımla (ne yapıldı, hangi dosyalar) | Görev özeti |
| 0.2 | Değişen dosyaları listele (git status/diff ile — salt okunur komutlar) | Dosya listesi |
| 0.3 | Etkilenen touchpoint'leri §5 matrisinden seç | Aday hedef seti |
| 0.4 | Bugün aynı iş için `log.md`'de kayıt var mı? (dedup — §6.4) | Append kararı |

**Adım 0.2 pratik detayı:**

```bash
git status --short            # ne değişti (A/M/D/?)
git diff --stat               # dosya bazlı özet
git diff --stat --cached      # stage varsa (subagent add ATMADIĞI için genelde boş)
```

- Değişen dosya listesi **session kaydına (§3.6 Files Changed) ve log bloğuna (Files)
  birebir taşınır** — iki yerde ayrı liste uydurulmaz, tek liste iki yere yazılır.
- `.ai/` içinde değişiklik varsa `git status` ile `.ai/SESSIONS/` ve `.ai/log.md` de
  listede görünür; bunlar "iş çıktısı" değil "senkron çıktısı"dır — raporda ayrı
  gruplanır (Adım 7).

### Adım 1 — Session Kaydı (`.ai/SESSIONS/`)

Session kaydı **`.ai/SESSIONS/YYYY-MM-DD-<slug>.md`** altına yazılır (dizin **büyük harf**;
2026-10-21 disk sayımı: 9 dosya; `.ai/sessions/` küçük harf **YOK**).

- Şablon: `.ai/.templates/session-log-template.md` (v2.0.1, TPL-SESSION-LOG) — Guardrail #16:
  şablon okunmadan dosya üretilmez.
- Yazım: `vault-utf8-writer.mjs` `write` modu (yeni dosya).
- Otomasyon: `session-save.mjs` **üretildi (2026-10-23, PLAN Adım 12)** —
  `node .ai/scripts/session-save.mjs --task "<gorev>" --status <completed|partial|blocked> --agent <agent>`
  şablonu doldurur, yazımı writer `write` moduyla (`@tmpfile`) yapar; kalan
  `{...}` placeholder'ları raporlar. Manuel yazım da mümkündür (Adım 1 alanları elle doldurulur).

**Şablon alanları (session-log-template §3 — `{...}` placeholder'ları doldurulmadan
kapanış YOK):**

| Bölüm | Alanlar | Kapanış kuralı |
|-------|---------|----------------|
| Frontmatter (7 alan) | `title` · `type` · `category` · `version` · `status` · `authority` · `updated` | 7 alan zorunlu; `authority` türev yazılır |
| §3.1 Session Info | Session ID · Date · Start/End Time · Duration · Status | `Status` = completed / partial / failed |
| §3.2 Summary | Tek paragraf | Ne yapıldı |
| §3.3 Tasks | # · Task · Status · Notes | Her görev satırı |
| §3.4 Decisions | Decision · Rationale | Karar + gerekçe |
| §3.5 Issues | Issue · Resolution | Sorun + çözüm |
| §3.6 Files Changed | File · Action (added/modified/deleted) | Gerçek dosya yolları |
| §3.7 Next Steps | `- [ ]` maddeleri | log.md C5 satırıyla tutarlı olmalı |
| §3.8 Vault Updates | log.md / MEMORY.md / ADR kutuları | Adım 4-5 tamamlanınca işaretlenir |
| §3.9 Notes | Serbest | ⚠️ işaretli belirsizlikler buraya |

**Dosya adı deseni:** `.ai/SESSIONS/YYYY-MM-DD-<slug>.md` (slug = görevden türetilir,
küçük harf-tire). Mevcut 9 dosya bu desendedir.

**`{PLACEHOLDER}` denetimi:** kayıt yazıldıktan sonra `Select-String -Pattern '\{[A-Z_]+\}'`
ile dosya taranır; `{...}` kalırsa kapanış eksiktir (şablon §4 yasağı: boş alan `—` ile
sahte-dolu gösterilmez).

### Adım 2 — Kök Pointer Tazelik (6 dosya)

Kökteki `.md` pointer'lardan **yalnız bu session'da etkilenenler** 1-3 satır güncellenir
(hepsi değil — kural 4):

| Dosya | Kapanışta ne tazelenir |
|-------|------------------------|
| `CLAUDE.md` | Skill Registry satırı / mimari not (yeni skill-registry değişikliği varsa) |
| `AGENTS.md` | Kural/ölçü satırı (ör. envanter sayısı değiştiyse) |
| `README.md` | Sayı/link satırı |
| `WORKFLOW.md` | Skill Noktaları/§14 satırı |
| `CONTEXT.md` | Bağlantı listesi satırı |
| `RAG.md` | SSOT bağlantısı satırı |

### Adım 3 — Vault Touchpoint Tazelik (20 `.ai/` kök md)

`.ai/` kökündeki hedef dosyalardan **değişenler** 2-4 satırla güçlendirilir
(in-place, dosya adı sabit). Sınıf önceliği: 🔴 CRITICAL → 🟠 ON-DEMAND → ⚪ LOG.
Setin tamamı ve sınıf etiketleri: `references/sync-touchpoints.md` §1.2 (SSOT: `.ai/SYNC.md` §3).
İndeks üçlüsü (`index.md` · `keys.md` · `RAG.md`) yeni dosya/konu eklendiyse **zorunlu**
(`.workflows/vault-sync.md` Aşama 4).

**Güçlendirme satırı formatı (SYNC §1: "her yakalama sonrası dosyayı 2-4 satırla
güçlendirir"):**

| Değişiklik türü | Güçlendirme | Örnek satır |
|-----------------|-------------|-------------|
| Yeni dosya eklendi | Katalog satırı + tarih | `| 2026-10-21 | .ai/SESSIONS/2026-10-21-x.md | Session kaydı | eklendi |` |
| Sayı değişti | Yeni sayı + ölçüm tarihi | `Envanter: 26 hedef dosya (ölçüm 2026-10-21)` |
| Yeni ADR | Özet + wiki-link (metin DEĞİL) | `ADR-088 özeti eklendi — bkz [[.decisions/...]]` |
| Kural değişikliği | İlgili § satırı düzeltme | Ölü atıf kaldırılır, yeni hedef yazılır |
| Bozuk link düzeltmesi | Link hedefi | `[[X]]` → diskteki gerçek yolla |

**Kural:** düzeltme = **satır edit** (`replace` modu, hedef satır); dosya baştan
yazılmaz (`write` yalnız yeni dosyada). `updated:` alanına dokunulmaz — yalnız
içerik satırı güncellenir (bu skill'de `updated` yalnız skill'in kendi dosyalarında
değişir).

### Adım 4 — `MEMORY.md` §20 State + `TODO.md`

| Dosya | Ne yazılır | Kanıt (2026-10-21) |
|-------|-----------|--------------------|
| `.ai/MEMORY.md` | `### §20 Current Session State` bölümü bu session'ı yansıtacak şekilde güncellenir | §20 başlığı L403 |
| `.ai/MEMORY.md` | `<!-- vault-sync:auto-begin -->` (L701) / `<!-- vault-sync:auto-end -->` (L741) bloğu: içerik **silinmeden** `insert-before-marker`/satır edit ile tazelenir | marker çifti L701/L741 |
| `.ai/MEMORY.md` | Known Issue satırı (L411) korunur — araç yokluğu bilinen durumdur | L411 |
| `.ai/TODO.md` | Biten iş `[x]`; yeni iş 1 satır + kaynak (ölçüm/doc/commit) — uydurma madde yok | CHECKLIST C3 |

> `MEMORY.md` 1000 satır sınırı: aşım varsa §20 önceki session state'leri arşiv satırına
> sıkıştırılır (silme yok, özetleme var).

**§20 state satırı minimal formatı (kanıt: `.ai/MEMORY.md` L403 başlığı):**

```markdown
### §20 Current Session State
- 2026-10-21: Footer responsive düzeltme — completed (agent: ui) · dosyalar: footer.php, _footer.css · sonraki: CTA hover QA
```

- Yeni state **üstüne** eklenir (en güncel satır en üstte) — geçmiş satırlar silinmez.
- `Known Issue` satırı (L411) silinmez/üzerine yazılmaz; yeni bilinen soru varsa
  **ayrı satır** eklenir.
- Auto-blok (`<!-- vault-sync:auto-begin -->` L701 … `<!-- vault-sync:auto-end -->` L741)
  içeriği bu bloğa bağlı satırlardan oluşur; marker satırları asla silinmez.

### Adım 5 — `log.md` Append (Adım 5 → tekrarlanabilir)

`log.md` sonuna **tek** giriş eklenir (append modu; detay ve şablon:
`references/log-append-rules.md`). Özet:

- Kapanış bloğu: `## YYYY-MM-DD HH:mm` + `**İşlem:**` alanları (Aşama 6 formatı).
- **C5 zorunlu son satır:** `Sonraki adım: <1 cümle>` — yoksa kapanış C5'siz sayılır.
- Format seçimi: madde satırı (`- YYYY-MM-DD | konu | detay`) hızlı kayıt için;
  damgalı blok vault-sync kapanışı için (her ikisi de mevcut ve geçerli — diskte ikisi
  bir arada, bkz. `references/log-append-rules.md` §2).

### Adım 6 — Doğrulama (Aşama 5 + 7)

| # | Kontrol | Geçme ölçütü |
|---|---------|--------------|
| 6.1 | Wiki-link regex doğrulama | Yeni/Değişen linklerin hedefleri diskte (`Test-Path`); bilinen sahte kırık linkler listesi dışındakiler sıfır |
| 6.2 | Halüsinasyon sweep | Kapanışta yazılan her sayı/iddea tarihli ölçüme bağlı; olmayan dosya/araca "var" denmemiş (§9 işaretleri yerinde) |
| 6.3 | Frontmatter 7 zorunlu alan | Kapanışta düzenlenen `.md` dosyalarında alanlar tam |
| 6.4 | `log.md` append-only diff | `git diff` yalnız **ek** satır gösterir; silme/düzeltme YOK |
| 6.5 | Yazım sağlığı | `vault-utf8-writer.mjs verify` → mojibake yok |
| 6.6 | Session kaydı bütünlüğü | Adım 1 dosyasında `{...}` placeholder kalmamış; 7 alanlı FM tam |

**Wiki-link doğrulama (6.1) pratik notu:**

- Doğrulama betiği **`wiki-link-check.ps1`** (`.ai/scripts/` envanteri 2026-10-21) —
  `.ai/scripts/*.ps1` gate betikleri yalnızca **repo kökünden**
  (`C:\www\coremusic.net`) çalıştırılır; `.ai/` içinden çalıştırılırsa göreli yollar
  çözülemez ve sahte "kırık link" üretir (`.ai/AGENTS.md` §13.6).
- Sonuç yorumu: raporlanan kırık link sayısına **bilinen sahte kırık linkler** dahildir
  (ui-design flow/reference şablon değişkenleri — düzeltme YASAK, sayıda kalır).
  Kapanışta yalnızca **bu session'da eklenen/değişen** linklerin hedefleri asıl
  kontrol edilir: `Test-Path <hedef>`.
- Yeni dosya eklendiyse `index.md`/`keys.md`/`RAG.md` §3 satırı da eklenir (Aşama 4) —
  link kontrolü bu üç satırın varlığını da doğrular.

### Adım 7 — Kapanış Raporu & Commit Kapısı (Aşama 8 manuel)

`.workflows/vault-sync.md` Aşama 8'in otomasyon araçları (**session-save.mjs /
vault-post-update.mjs / vault-cmd.mjs**) **üretildi (2026-10-23, PLAN Adım 12)** →
bu adım artık script'li senkron + rapordur:

```text
SESSION SAVE     → Adım 1 (session-save.mjs — elle yazım da mümkün)
VAULT UPDATE     → Adım 2-4 (vault-post-update.mjs --scope root [--dry-run])
VERIFY           → Adım 6
RESULTS CHECK    → §7 kontrol listesi
LOG APPEND       → Adım 5
MEMORY UPDATE    → Adım 4
```

Rapor formatı (orkestratöre/user'a):

| Alan | İçerik |
|------|--------|
| Durum | completed / partial / failed |
| Değişen vault dosyaları | Liste (session kaydı dahil) |
| Doğrulama | §7 listesi PASS/BLOCK |
| `log.md` girişi | Eklenen bloğun ilk satırı (kanıt) |
| Commit | **ATILMADI** — orkestratöre; öneri commit mesajı |

**Rapor örneği (worked — footer responsive kapanışı):**

```text
DURUM: completed (agent: ui)
VAULT DEĞİŞENLER: .ai/SESSIONS/2026-10-21-footer-responsive.md (yeni)
  · .ai/log.md (+1 blok) · .ai/MEMORY.md (§20 1 satır) · .ai/TODO.md (1 [x] + 1 yeni)
DOĞRULAMA: C1-C5 PASS · verify OK · wiki-link 0 kırık (bu session) · append-only OK
LOG: "## 2026-10-21 17:42 — Footer responsive düzeltme — vault sync (session kapanışı)"
COMMIT: ATILMADI — öneri: "ui: footer responsive fix + vault sync 2026-10-21"
```

---

## §5 Olay → Dosya Matrisi (özet — tamı: `references/sync-touchpoints.md` §3)

| Bu session'da olduysa | Yazılan dosya | Mod |
|-----------------------|---------------|-----|
| Görev tamamlandı | `.ai/SESSIONS/YYYY-MM-DD-<slug>.md` | write (yeni) |
| Görev tamamlandı | `.ai/log.md` | **append** |
| Durum değişti | `.ai/MEMORY.md` §20 | insert-before-marker / satır edit |
| Açık iş eklendi/kapandı | `.ai/TODO.md` | append / satır edit |
| Yeni ADR kararı | `.ai/brain.md` (+ `.decisions/index.md`) | append (frozen metne dokunma) |
| Katalog/keyword/konu indeksi değişti | `index.md` · `keys.md` · `RAG.md` | satır edit |
| Envanter sayısı değişti | `.ai/CONTEXT.md` §3.x | satır edit (tarihli ölçüm) |
| Skill Registry değişti | kök `CLAUDE.md` | satır edit |
| CLAUDE eşitleme | `.claude/CLAUDE.md` · `.opencode/CLAUDE.md` | copy (SSOT `.ai/` lehine) |
| Hiçbiri | — | **dokunma** |

**Matris okuma kuralı:** satırlar **koşulludur** — "olduysa" sütunu gerçekleşmeyen
satırlar uygulanmaz. Örnek: CSS düzeltmesi yapıldı, ADR verilmedi → `brain.md` satırı
uygulanmaz. Matrisin uygulandığı durum ile Adım 3 güçlendirme listesi birleşir;
sonuç = **o session'a özgü kısa liste**, 26 dosyanın tamamı değil (tam liste yalnız
SSOT'ta durur, §3 kural 4).

---

## §6 Yazma Protokolü

### 6.1 Yazma kanalı (tek)

```bash
node .ai/scripts/vault-utf8-writer.mjs <mod> ...
# modlar (disk kanıtı, L82-133): replace · append · insert-before-marker · write · copy · verify · repair · scan
```

**Kapanışta tipik kullanım sırası:**

| Sıra | Senaryo | Mod | Not |
|------|---------|-----|-----|
| 1 | Yeni session kaydı | `write` | Adım 1 · dosya yoksa |
| 2 | `log.md` girişi | `append` | Adım 5 · **tek izinli mod** |
| 3 | `MEMORY.md` §20 / auto-blok | `insert-before-marker` | Marker: `<!-- vault-sync:auto-begin/end -->` |
| 4 | `TODO.md` / `keys.md` / `brain.md` satır ekleme | `append` | Veya mevcut satır değişimi: `replace` |
| 5 | Kök pointer 1-3 satır düzeltme | `replace` | Satır bazlı; dosya tamamı DEĞİL |
| 6 | CLAUDE üçlüsü eşitleme | `copy` | Kaynak = `.ai/CLAUDE.md` (SSOT) |
| 7 | Yazım sonrası sağlık | `verify` | Mojibake + yapı |
| 8 | Bozuk dosya tespiti | `repair` | Yedek alır; sonra yeniden yaz |
| 9 | Tarama (bulmak için) | `scan` | Yazma yok |

- Yedekleme: writer işlem öncesi yedeği `C:/temp/opencode/vault-backups` altına alır.
- **YASAK:** PowerShell yazım cmdlet'leri (`Set-Content` · `Out-File` · `Add-Content` ·
  `echo >` · `New-Item -Value`). İzinli: salt-okunur komutlar (`Get-Content` ·
  `Select-String` · `Test-Path` · `git status/diff/log`).
- `log.md` için **append modu dışında yazım yok** (kural 2).
- Writer'ın tam bayrak sözdizimi (`<mod>` sonrası argümanlar) her kullanımda script
  başlığından doğrulanır — bu skill'de sabitlenmez (⚠️ tahmin yok).

### 6.2 CLAUDE üçlüsü eşitleme prosedürü (Adım 2b ayrıntısı)

```text
1. Kaynak = .ai/CLAUDE.md (SSOT). Türev: .claude/CLAUDE.md · .opencode/CLAUDE.md
2. Karşılaştır: hash/boyut (Windows: Get-FileHash · cmp yoksa diff)
3. Eşitse: dokunma (kural 4)
4. Eşit değilse: SSOT lehine copy modu ile TÜREV dosya yeniden yazılır
5. Tersi YASAK: .claude/ veya .opencode/ içeriği SSOT'a geri yazılmaz (akış tek yönlü)
6. Doğrulama: üç dosyanın hash'i eşit → §7 kutusu
```

Not: `.opencode/skills/` kapatıldı (2026-10-07 merge/port) — `.opencode` tarafında yalnız
`CLAUDE.md` eşitlenir; skill kopyası üretilmez.

### 6.3 Session kaydı şablonu

`.ai/.templates/session-log-template.md` Guardrail #16 gereği **yazmadan önce okunur**;
7 alanlı frontmatter alanları şablondan alınır, tahmin edilmez.

### 6.4 Dedup

Aynı görev için aynı gün giriş varsa: **aynı metni tekrarlama** — yeni durumu anlatan
farklı satır ekle ya da "mevcut kayıt yeterli" de. Ayrıntı: `references/log-append-rules.md` §3.

---

## §7 Kapanış Doğrulama Listesi (§4 Adım 6'nın checklist görünümü)

- [ ] C1: Her "tamamlandı" iddiası için en az 1 kanıt gösterildi
- [ ] C2: Vault kapanış 5 adımı (in-place yazım → log timestamp → MEMORY §20 → wiki-link → halüsinasyon sweep) uygulandı
- [ ] C3: `TODO.md` biten `[x]`, yeni iş 1 satır + kaynak
- [ ] C4: Post-op manuel senkron yapıldı (Adım 7)
- [ ] C5: `log.md` sonuna `## YYYY-MM-DD` + `Sonraki adım:` satırı eklendi
- [ ] `vault-utf8-writer.mjs verify` → mojibake yok
- [ ] `git diff` log'da yalnız ekleme (append-only ihlali yok)
- [ ] Yeni/değişen wiki-link hedefleri diskte
- [ ] `.claude/CLAUDE.md` == `.ai/CLAUDE.md` == `.opencode/CLAUDE.md`
- [ ] Frozen ADR metinlerine dokunulmadı; secret yazılmadı
- [ ] **Commit atılmadı** (subagent) — rapor orkestratöre sunuldu

> **Yasak (`.ai/CHECKLIST.md` §C birebir):** "C1'siz 'bitti' deme · C2/C4'ü atlayıp
> session kapanma · C5'siz kapanış."

**Kapılar arası bağ (SYNC §4):** Kapı A'da atlanan tazelik, Kapı B'de atlanan indeks
güncellemesi Kapı C'de (bu skill) **yakalanır ve tamamlanır** — kapanış, oturum içinde
kaçırılan tazeliğin son şansıdır; "zaten session başında bakmıştım" ile atlanamaz.
CHECKLIST §C-Güçlendirme: "bu session'da **değişen** hedef dosyalar 2-4 satırla
güçlendirilir (26 hedefin tamamı için geçerlidir)"; Değişmeyen dosya zorla yeniden
yazılmaz (Guardrail #4).

---

## §7B Kapsam Varyantları (duruma göre kapanış)

| Durum | Adım 1 (session) | Adım 2-3 (touchpoint) | Adım 5 (log) | Adım 7 (rapor) | Not |
|-------|------------------|-----------------------|--------------|----------------|-----|
| **Tam kapanış** (completed) | Tam kayıt (§3.1-§3.9) | Değişenler güçlendirilir | Tam blok + Sonraki adım | Tam rapor | Normal yol |
| **Partial** | Kayıt: Status=partial + Issues §3.5 | Yalnız yapılan kısım | Blok: partial + eksik listesi | Eksikler net yazılır | Sonraki session §3.7'den devam eder |
| **Failed** | Kayıt: Status=failed + neden | Vault'a boşuna dokunma (değişen yoksa) | 1 satır F1: hata + durum | Hata + sonraki adım | Hata modu §8 |
| **Vault-only iş** (doküman görevi) | Kayıt (doküman görevi olarak) | Güçlendirme ana iştir | Tam blok | Tam rapor | Çoğu touchpoint satırı bu varyantta dolu |
| **Kesintili (H9)** | Varsa koru | Tamamlanan kısım korunur | Eklenmediyse ekle | "Adım X'ten devam edildi" | Adım 5-7 tamamlanır |

---

## §8 Hata Modları & Kurtarma

| # | Hata | Tespit | Kurtarma | Rollback |
|---|------|--------|----------|----------|
| H1 | **Eksik senkron** — touchpoint unutuldu (sayı/link bayat kaldı) | §7 + SYNC §5 tazelik kontrolü; sonraki session'da tutarsızlık | İlgili dosya 2-4 satır güçlendirilir (Adım 2-3) + log'a append not | Tekrar gerekmez — in-place ekleme yeterli |
| H2 | **Duplicate log satırı** | Aynı tarih+konu 2× (`grep`/son satırlar) | Silme YOK; ikinci giriş `[DUPLICATE]` işareti yalnız **yeni eklenen** satırda ve orkestratör onayıyla | Geçmişe dokunulmaz (append-only) |
| H3 | **SSOT kirliliği** — türev dosya SSOT yerine elle değiştirildi | `cmp`/hash eşitsizliği; `.ai/` dışı elle yazım | SSOT (`.ai/`) lehine `copy` modu ile eşitle | Kopya dosya SSOT'tan yeniden üretilir; elle kısım geri alınır |
| H4 | **Overwrite / append-only ihlali** (`log.md` vs. `git diff`) | `git diff` → silme/düzeltme satırı | `git checkout -- .ai/log.md` (veya writer yedeği) → append'i tekrarla | Git son iyi duruma döner; sonra Adım 5 tekrar |
| H5 | **UTF-8 bozukluğu (mojibake)** | `verify` modu / gözle `Ã…` dizgeleri | Writer dışı yazım tespiti → `repair` (yedek alır) → yazımı writer ile tekrarla + CRITICAL log | Yedekten onarım |
| H6 | **Olmayan/bozuk araca çağrı** — `session-save.mjs` vb. | `Test-Path` False (artık nadir — araçlar 2026-10-23'te üretildi; §9) veya script hata verdi | Manuel akışa geç (Adım 1/7); araç yok/çalışmıyor diye adım atlanmaz | — |
| H7 | **Kırık wiki-link** | 6.1 + `wiki-link-check.ps1` (repo **kökünden**) | Hedefi üret veya linki düzelt (in-place); bilinen sahte kırık linkler "onarılmaz" (§9 + `.ai/AGENTS.md` §13.6/§13.7) | — |
| H8 | **Session kaydı şablonsuz** | `{...}` placeholder kaldı / 7 alanlı FM eksik | Kaydı sil, şablondan yeniden yaz (Guardrail #16); `write` modu | Hatalı kayıt writer yedeğinde kalır |
| H9 | **Yarım kapanış** — Adım 3'te kesildi (log yazılmadan çıktı) | §7 checklist eksik işaretli | Adım 5-7'yi tamamla; eksik adımı atlamadan sırayla | Kismi yazılan dosyalar in-place tamamlanır |
| H10 | **Çakışan session** — iki oturum aynı touchpoint'e yazdı | `git status` / context lock (`.ai/AGENTS.md` §12) | MO en eski kilidi kırar; son yazan MO koordinasyonunda tekrarlar | Çakışan satırlar SSOT'tan yeniden üretilir |

**Rollback yolu (özet):** writer yedeği (`C:/temp/opencode/vault-backups`) veya
`git checkout -- <dosya>` → sorunlu adımı (5-7) tekrarla → verify → rapora "H*n çözüldü" notu.

**Hata tespit komutları (hızlı başlangıç — salt okunur):**

```bash
git diff .ai/log.md                    # H4: append-only ihlali var mı? (silme/düzeltme satırı = ihlal)
git diff --stat                        # H1: hangi dosyalar değişti (kapanış kapsamı)
Get-Content .ai/log.md -Tail 10        # H2: son girişler — duplicate var mı?
Test-Path .ai/SESSIONS/YYYY-MM-DD-<slug>.md   # H6: session kaydı var mı?
node .ai/scripts/vault-utf8-writer.mjs verify  # H5: UTF-8 sağlığı (hedef dosya ile)
Select-String -Path <dosya> -Pattern 'Ã'      # H5: mojibake göz taraması
git status --short                     # genel: ne değişti / stage YOK (subagent add atmaz)
```

---

## §8A Doğrulama Komut Defteri (§7 kutularının kanıt komutları)

> Hepsi **salt-okunur**; repo kökünden (`C:\www\coremusic.net`) çalıştırılır.

| §7 kutusu | Kanıt komutu | Beklenen |
|-----------|--------------|----------|
| C1 kanıt | İlgili test/build komutunun çıktısı (görev tipine göre) | Çıktıda başarılı sonuç |
| C2 5 adım | Adım 6 tablosu 6.1-6.6 hepsi PASS | Tablo dolu |
| C3 TODO | `Get-Content .ai/TODO.md -Tail 15` | Biten `[x]`, yeni madde + kaynak |
| C4 manuel sync | Adım 7 rapor satırları (SESSIONS + log + MEMORY) | Üçü de mevcut |
| C5 Sonraki adım | `Select-String -Path .ai/log.md -Pattern 'Sonraki adım' \| Select-Object -Last 3` | Son girişte var |
| verify | `node .ai/scripts/vault-utf8-writer.mjs verify` (hedef dosya ile) | Mojibake yok |
| append-only | `git diff .ai/log.md` | Yalnız `+` satırları (silme/düzeltme yok) |
| wiki-link | `.\.ai\scripts\wiki-link-check.ps1` (**kökten**) + `Test-Path <yeni hedef>` | Bu session linkleri OK |
| eşitlik | `Get-FileHash .ai/CLAUDE.md, .claude/CLAUDE.md, .opencode/CLAUDE.md` | 3 hash eşit |
| frozen/secret | `git diff --stat` gözden geçirme | `.decisions/` yok · secret yok |
| commit yok | `git log --oneline -1` + rapor | Son commit bu session'dan değil (subagent) |

---

## §8B Kapanış ↔ Yaşam Döngüsü Bağları (bu skill nerede durur)

| Bağ | İlişki |
|-----|--------|
| `.workflows/session-init.md` | Kapı A (başlangıç) bu skill'in öncesidir; boot okuma listesi oradadır |
| `.workflows/vault-sync.md` | 7 aşama + Aşama 8 — bu skill'in uygulama kaynağı |
| `.ai/WORKFLOW.md` §8.7 | Bitiş soruları — Adım 0-6 bu tabloya hizalıdır |
| `.ai/WORKFLOW.md` §8.7A | **14 `.ai/` kök dosya** okuma/tazelik listesi — Adım 3'ün kaynağı (§25.4: kanonik kök boot listesi §24.2 + §8.7A) |
| `engine.md` §12.6/§12.7 | Faz kapanışı 8 madde kontrol listesi → rapor → vault-sync — faz bazlı işlerde Adım 7'yi tamamlar |
| `.ai/CHECKLIST.md` §C | C1-C5 — §7 kutularının otoritesi |
| `.ai/SYNC.md` §4 | Üç kapı (A/B/C) — bu skill **yalnız C** kapısıdır |

> Oturumun **başında** okunan 14 `.ai/` kök dosya (§8.7A) ile kapanışta **değişenlerin**
> güçlendirilmesi aynı seti iki uçtan korur: başta oku (bayatlık yakala), sonda yaz
> (bayatlığı gider). Setin adı ve sırası değiştirilmez; bu skill seti yeniden tanımlamaz.

---

## §9 Bilinen Disk Gerçekleri (⚠️ — eski iddiaların düzeltilmiş hali)

> Bu tablo, v3.0.0 ve öncesindeki iddiaların **2026-10-21 disk ölçümüyle** karşılaştırmasıdır.
> Zero-Hallucination gereği olmayan araç/dosya "var" olarak anlatılmaz.
>
> **2026-10-23 güncellemesi:** `session-save.mjs` · `vault-post-update.mjs` · `vault-cmd.mjs`
> **üretildi** (PLAN Adım 12, kullanıcı onaylı üretim; test: `--help` exit 0 + dry-run doğrulandı) —
> tablodaki üç "YOK" satırı **2026-10-21 tarihsel ölçümüdür**, güncel durum böyle değildir.
> `project-state.md` yine **üretilmez** (bu dosya §9 kuralı — 3. satır geçerliliğini korur).
> Güncel envanter: `.ai/scripts/index.md`.

| Konu | Eski iddia (v3 ve öncesi) | Disk gerçeği (2026-10-21) | Davranış |
|------|---------------------------|---------------------------|----------|
| `session-save.mjs` | Çalışır script | **YOK** (Test-Path False) | ⚠️ Manuel session kaydı (Adım 1); parametreler içerik olarak elle taşınır |
| `vault-post-update.mjs` | `--scope root/full` çalışır | **YOK** | ⚠️ Manuel senkron (Adım 2-4-7) — `.workflows/vault-sync.md` Aşama 8 ⚠️ notu ile uyumlu |
| `vault-cmd.mjs` | Türkçe komut arayüzü | **YOK** | ⚠️ Doğrudan vault-utf8-writer.mjs + salt-okunur komutlar |
| `.ai/sessions/` (küçük) | Session dosyaları burada | **YOK**; gerçek dizin **`.ai/SESSIONS/`** (büyük harf, 9 dosya) | Adım 1 büyük harf dizini kullanır |
| `context/project-state.md` | Güncellenir | **YOK** (hiçbir yolda değil) | Kural: adım listeden çıkarıldı; üretilmez |
| MEMORY.md §18 | "Session History" güncellenir | İlgili bölüm **§20 Current Session State** (L403) | Adım 4 §20'yi hedefler |
| "12 root .md touchpoint" | 12 dosya kökte | SYNC seti: **26** = 6 kök pointer + 20 `.ai/` kök md | §2-§3 bu seti kullanır |
| `.opencode/` sync | `.opencode/CLAUDE.md` eşitlenir | Eşitlik hedefi doğru; `.opencode/skills/` **kapandı** (2026-10-07, merge/port) | Kopya dosya eşitlenir; skill kopyası üretilmez |
| vault-utf8-writer modları | 7 mod (append…scan) | **8 mod** — `replace` da var (L82-133) | §6.1 listesi güncellendi |
| WORKFLOW §8.7 adım sayısı | — | Başlık "Bitiş (6 Adım)" der; tablo satırları 1,2,3,4,6 (5 yok); CHECKLIST C2 "5 adım" der | ⚠️ Sayı gerilimi kaydedildi; içerik olarak 5 adım (C2) uygulanır; uydurma "5. adım" yazılmaz |
| `.ai/SYNC.md` §3.2 sayaç | — | Sayaç satırı "CRITICAL 18 … = 22 `.ai/` satırı + 6 kök = 28; benzersiz 26" der; tablo 20 `.ai/` satırı gösterir | ⚠️ Gerilim taşınır; benzersiz dosya 26'dır |

---

## §9A Rol Sınırı: Kim Ne Yapar (kapanışta)

| Rol | Yapar | Yapmaz |
|-----|-------|--------|
| **Görevi yapan agent (subagent dahil)** | Adım 0-6'yı kendi oturumu için uygular; kapanış raporunu orkestratöre sunar; `.ai/SESSIONS/` ve `log.md` append'i hazırlar | `git commit` ATMAZ · frozen ADR'ye dokunmaz · secret yazmaz |
| **MO (vault-updater)** | Touchpoint seti koordinasyonu (Adım 2-3), `.ai/AGENTS.md` §6 vault routing'i, çakışma çözümü | Kullanıcı onayı gereken yüzeyde (deployment, ayar) tek başına karar vermez |
| **Orkestratör** | Commit kararını verir/atar; handover onayları; eskalasyon (L1→L2→L3→insan) | Kullanıcı onayı kapılarını (deploy, `.claude/settings.json`) onaysız geçmez |
| **Kullanıcı** | Yıkıcı/onaylı değişiklik onayı; final kapanış kabulü | — |

Kaynak: kök `AGENTS.md` §7/§8 · `.ai/AGENTS.md` §6/§9/§25.3.

---

## §9B Oturum İçi Ne Zaman Run Edilir (kapı bağlamı)

| Kapı | Bu skill'in parçası mı? | Ne yapılır |
|------|--------------------------|------------|
| **A — Baş** (her session başında) | Hayır (session-init alanı) | CRITICAL tazelik taraması · MEMORY §20 okuma · log'a 1 satır INFO |
| **B — Orta** (session ≥1) | Kısmen — değişiklik anında yapılan tazeleme | Değişen dosya + indeks üçlüsü tazeleme |
| **C — Son** ("tamamlandı" öncesi) | **Evet — bu skill'in çekirdeği** | Adım 0-8 + C1-C5 |

> Kapanış (C) A/B kapılarının yerine geçmez; A/B'de atlanan tazelik C'de **yakalanır ve
> tamamlanır** (SYNC §1: bayatlık session başına en az üç kez yakalanır).

---

## §10 Kaynaklar

| # | Kaynak | Ne destekliyor |
|---|--------|----------------|
| W1 | `https://git-scm.com/docs/git-log.html` (git resmi dokümantasyonu, erişim 2026-10-21) | Append-only denetim izi semantiği (log kuralı gerekçesi) |
| L1 | `.workflows/vault-sync.md` v1.1 (238 satır) | 7 aşama + Aşama 8 manuel notu + §6 yasaklar |
| L2 | `.ai/SYNC.md` v1.1.0 | 26 dosyalık set, üç kapı, tazelik, kurallar |
| L3 | `.ai/CHECKLIST.md` §C | C1-C5 + C-Güçlendirme + yasak satırı |
| L4 | `.ai/WORKFLOW.md` §8.7 / §8.7A | Bitiş soruları · 14 `.ai/` kök okuma seti |
| L5 | `.ai/.templates/session-log-template.md` | Session kaydı şablonu (7 alanlı FM) |
| L6 | `.ai/scripts/vault-utf8-writer.mjs` | Tek yazma kanalı (8 mod) + yedekleme |
| L7-L12 | `.ai/MEMORY.md` · `.ai/log.md` · `.ai/scripts/` · `.ai/SESSIONS/` · kök+`.ai/AGENTS.md` · skill-maker | Durum/ölçüm kanıtları |

Tam kaynak kaydı, kaynak→bölüm eşlemesi ve doğrulanmayanlar listesi:
[references/sources.md](references/sources.md).

---

## §11 Referanslar (bu skill'in dosyaları)

| Dosya | İçerik |
|-------|--------|
| [references/sources.md](references/sources.md) | Truth Mode kaynak kaydı: 1 web + 12 yerel; kaynak→bölüm eşlemesi; doğrulanmayanlar |
| [references/sync-touchpoints.md](references/sync-touchpoints.md) | 26 dosyalık hedef set · üç kapı (A/B/C) · C1-C5 · olay→dosya matrisi · tazelik · eşitleme |
| [references/log-append-rules.md](references/log-append-rules.md) | log.md append protokolü: formatlar · dedup · yazma prosedürü · hata modları · rollback |
| [references/changelog.md](references/changelog.md) | Sürüm geçmişi 1.0.0 → 4.0.0 (N10) |
| [references/checklist.md](references/checklist.md) | Kalite kapısı: N1-N10 + eşitleme/log/olay-matrisi/denetim kapıları (PASS/BLOCK) — kapanış öncesi son kapı |
| [examples/session-close-sync.md](examples/session-close-sync.md) | Çalışmış örnek: footer responsive kapanışı — girdi→çıktı tam döngü (N5) |

**Vault süreç SSOT'ları (dış):** `.ai/SYNC.md` · `.workflows/vault-sync.md` ·
`.ai/CHECKLIST.md` §C · `.ai/WORKFLOW.md` §8.7/§8.7A · `.ai/AGENTS.md` §6 (vault-updater routing).

---

## §11A Sık Yapılan Hatalar (bu skill'den bağımsız düzeltme geçmişi)

| Hata | Sonuç | Doğrusu |
|------|-------|---------|
| `.ai/sessions/` (küçük harf) kullanmak | Dosya bulunamaz / ikiye bölünmüş kayıt | `.ai/SESSIONS/` — büyük harf, 9 mevcut dosya |
| `session-save.mjs` çağırmak | Komut yok hatası | Manuel Adım 1 (şablon + write modu) |
| log.md'yi tam dosya `write` ile yenilemek | Geçmiş kaybı (append-only ihlali) | `append` modu, tek giriş |
| `.ai/` içinden `wiki-link-check.ps1` çalıştırmak | 19+ sahte "PNG yok"/kırık link | Repo kökünden çalıştır (§13.6) |
| Değişmeyen 26 dosyanın hepsini yeniden yazmak | Gereksiz diff + bozulma riski | Yalnız değişenler; 2-4 satır güçlendirme |
| Kapanışta C5 satırını atlamak | Sonraki session nereden devam edeceğini bilmez | `Sonraki adım:` satırı zorunlu |
| Eşitlik bozukken `.opencode` lehine düzeltmek | SSOT tersine akar | Kaynak her zaman `.ai/CLAUDE.md` |

---

## §12 Tarihçe (özet)

| Sürüm | Tarih | Ne değişti |
|-------|-------|------------|
| 4.0.0 | 2026-10-21 | Komple yeniden yazım: disk gerçeği hizası (§9), 8 adımlı akış, 5 referans dosyası, 26 dosyalık set, dedup/rollback |
| 3.0.0 | 2026-10-07 | Claude Skill v3 şeması; `.opencode` birleşimi; eksik araç işaretleri |
| 1.0.0 | ⚠️ | İlk sürüm (tarih diskte yok) |

Detay: [references/changelog.md](references/changelog.md).

---

## §13 Terimler & Kısaltmalar (bu skill bağlamı)

| Terim | Tanım |
|-------|-------|
| **Touchpoint** | Kapanışta tazelenmesi gereken vault/kök `.md` dosyası (SYNC §3 seti) |
| **Kapı A/B/C** | Session başı / ortası / sonu tazelik kapıları (SYNC §4) — bu skill C kapısı |
| **C-Güçlendirme** | Kapanışta zorunlu güncellenen dosya grubu: log · MEMORY §20 · TODO · CHECKLIST · değişen hedef dosyalar |
| **Append-only** | Yalnız ekleme; geçmiş satıra silme/düzeltme yok (log.md kuralı) |
| **In-place** | Dosya adı sabit, taşıma/silme yok; satır edit (Guardrail #4) |
| **Writer** | `.ai/scripts/vault-utf8-writer.mjs` — tek izinli yazma kanalı (8 mod) |
| **CRITICAL set** | `.ai/SYNC.md` §3.2'de 🔴 işaretli hedef dosyalar (boot + tazelik sınıfı) |
| **Sahte kırık link** | Şablon değişkeni/kaçış dizgesi gibi görünen, GERÇEK olmayan kırık link (§13.7 — onarım yasak) |
| **SSOT** | Tek doğruluk kaynağı — `.ai/` vault; türev dosyalar (`.claude/`, `.opencode/`) ondan eşitlenir |
| **REDACTED** | Secret/anahtar maskesi — ham değer vault'a yazılmaz |

---

## §14 SSS (disk gerçeğine dayalı)

**S: `session-save.mjs` neden çalışmıyor?**
C: Araç diskte yok (§9). Session kaydı manuel: Adım 1 — şablonu oku, `.ai/SESSIONS/`
altına `write` moduyla yaz. Süreç atlanmaz, yalnız elle yürür.

**S: `.ai/sessions/` mi, `.ai/SESSIONS/` mi?**
C: **`.ai/SESSIONS/`** (büyük harf) — 9 mevcut dosya ile doğrulandı. Küçük harf dizini
diskte yok.

**S: log.md'de iki format var, hangisi doğru?**
C: İkisi de geçerli (disk kanıtı: madde satırı + tarihli blok). Kapanışta F3 damgalı
blok + `Sonraki adım:` satırı tercih edilir. Ayrıntı: [references/log-append-rules.md](references/log-append-rules.md) §2.

**S: Session kapanmadan önce tüm 26 dosyaya dokunmalı mıyım?**
C: HAYIR. Yalnız **değişenler** (§5 matrisi + Adım 3). Değişmeyen dosya zorla yeniden
yazılmaz (kural 4).

**S: Commit'u ben atayım (subagent)?**
C: HAYIR — kural 5. Commit kararı orkestratöre aittir; sen raporu sun.

**S: `.opencode/` tarafına skill kopyası eklenmeli mi?**
C: HAYIR — `.opencode/skills/` kapandı (2026-10-07 merge/port). Eşitlenen yalnız
`.opencode/CLAUDE.md`'dir.

**S: Eşitlik bozulursa hangi taraf kazanır?**
C: Her zaman `.ai/CLAUDE.md` (SSOT). Türev dosyalar SSOT'tan yeniden yazılır; tersi
akış yasaktır (§6.2).

**S: `wiki-link-check.ps1` 6 kırık link gösteriyor, düzeltmeli miyim?**
C: ui-design flow/reference'daki bilinen sahte kırık linkler "onarılamaz" (şablon
değişkeni — `.ai/AGENTS.md` §13.7); sayı 6'da kalır, kusur sayılmaz. Yalnız **bu
session'da eklenen** linklerin hedefleri asıl kontrol edilir (`Test-Path`).

---

## §15 Hızlı Özet — Adım Kartı (baskın kullanım için)

> Bu kart günlük kullanım içindir; boşlukta §4-§8'deki tam açıklama geçerlidir.

| Adım | Tek satır | Kanıt |
|------|-----------|-------|
| 0 | Ne bitti, ne değişti, hangi touchpoint? (salt okunur) | `git status/diff --stat` |
| 1 | `.ai/SESSIONS/YYYY-MM-DD-<slug>.md` — şablon 7 alan + §3.1-3.9, `{...}` yok | Dosya var + placeholder taraması temiz |
| 2 | Değişen kök pointer'lar 1-3 satır (6 dosyalık set) | Satır edit diff'i |
| 3 | Değişen `.ai/` touchpoint'ler 2-4 satır güçlendirme (20 md, CRITICAL önce) | Satır edit diff'i |
| 4 | MEMORY §20 üstüne 1 satır; auto-blok marker'ları korunur; TODO `[x]` + yeni | L403/L701/L741 hizası |
| 5 | log.md **append**: F3 blok + `Sonraki adım:` (C5) | `git diff` yalnız + satır |
| 6 | verify · wiki-link (kökten) · FM 7 alan · append-only · placeholder yok | §7 kutuları |
| 7 | Rapor (durum/değişenler/DOĞRULAMA/log/COMMIT ATILMADI) | Orkestratöre sunum |

**Sıra kuralı:** 0→7; 5-6 hata durumunda tekrarlanabilir (§8). Adım atlamak yalnız
"değişmedi" kanıtıyla mümkündür (§5 matrisi + kural 4).

**Kart-üstü-not:** Bu skill'in tetikleyicileri description bloğundadır — 'vault güncelle' ·
'session kapat' · 'senkronize' · 'log yaz' · 'vault-post-update' · 'session sync'.
Tetikleyici geldiğinde ilk okuma §2 karar ağacı, ilk uygulama §15 kartıdır; belirsizlikte
§4-§8 tam metin açılır.

---

*CoreMusic Skill v3.0 — metadata.version: 4.0.0 — Updated: 2026-10-21*
