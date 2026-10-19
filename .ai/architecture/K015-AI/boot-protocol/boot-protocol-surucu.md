---
node_id: boot-protocol-surucu
node_type: module
status: active
parent_id: boot-protocol
children: []
depends_on: [boot-protocol-anayasa]
evidence:
  - ".ai/MEMORY.md §5 + .ai/AGENTS.md §24.1 (5 adım protokolü, okundu 2026-10-17)"
  - ".claude/hooks/skill-mandate.cjs + ai-risk-gate.cjs (gate kanıtı)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı"
open_questions: []
group: mimari
---

# boot-protocol-surucu - Boot Kullanım Sürucusu

> **Kapsam:** Oturum başında ajanın gerçekten çalıştığı adımlar — hangi dosyanın hangi sırayla
> okunduğu, ne zaman durulduğu, boot'un doğrulandığı, boot sonrası göreve geçildiği, boot
> arızasında (eksik/bozuk dosya) ne yapıldığı ve boot'un tekrar (retry) kuralları.
> **Kural koyar değil, tarif eder:** bağlayıcı kurallar `Dosya: .ai/CLAUDE.md · Satır: 628-648`
> (§16 Boot Protocol — 13 kanonik) ve `Dosya: .ai/AGENTS.md · Satır: 24.2/25.4` (14 kök + FULL 17)
> içindedir; bu dosya o kuralların **sürüm kılavuzudur**.
>
> **Ölçüm tarihi:** 2026-10-17 (tüm satır sayıları `[IO.File]::ReadAllLines(...UTF8).Count`
> ile bugün diskten ölçülmüştür — §12).

## §1. ELI15 — Bu sürücü ne işe yarar

Her oturum (Claude Code / OpenCode) açıldığında ajan "hafıza sıfır" başlar. Boot protokolü bu
boş hafızayı **sıralı, kanonik dosya okumalarıyla** doldurur: anayasa (`.ai/CLAUDE.md`), agent
sınırları (`.ai/AGENTS.md`), süreç (`.ai/WORKFLOW.md`), kararlar (`.ai/brain.md`), session
hafızası (`.ai/MEMORY.md`), audit trail (`.ai/log.md`) ve katalog/keyword katmanları. Boot
tamamlanmadan plan/kod/faaliyet başlatılmaz (Guardrail #2 "Vault First").

> **eli10:** Ajan işe başlamadan önce "kural kitabını" sırayla okur; kitap eksikse ne yapacağını
> (durmak mı, devam etmek mi) bu sürücü tarif eder; iş bitince okuma listesi değişmez.
>
> **eli15:** Boot bugün **elle** yürütülür — hook'lar (`skill-mandate.cjs`, `ai-risk-gate.cjs`)
> prompt ve dosya-yazma kapılarını otomatikleştirir, ama dosya okuma sırasını ajanın kendisi
> uygular. Bu yüzden sürücünün "günlük davranış" bölümü (§4) otomasyonu değil, **bugünün elle
> akışını** anlatır; boot otomasyonu için bkz §26/Q4 (UNKNOWN — boot betiği diskte yok).

## §2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Oturum başı adım adım akış A-I + karar ağaçları (§4) | Dosya içeriği özetleri (içerik hedef dosyanın işidir) |
| Boot listesi varyantları ve fark çözümü (§5) | Boot listesinin **değiştirilmesi** (§18: değiştirilemez) |
| Boot doğrulama: varlık / link / mojibake (§6, §12-§13) | `.claude/hooks/*.cjs` kodunun yeniden yazımı (yalnız okuma) |
| Boot arızası + retry kuralları (§8-§9) | uygulama (PHP) bootstrap'i — `RuntimeBootstrap` bu konu DEĞİL (§20) |
| Hook gate akışları: skill-mandate + risk-gate (§10-§11) | ADR metinleri (frozen — yalnız okunur) |
| Token ekonomisi + kapılar + hata modları (§15-§17) | `git commit` (orkestratöre ait — §18) |

- **Kullananlar:** her oturum açan ajan · MO (boot kaydı) · Vault Steward (denetim).
- **Ön koşul:** kök `AGENTS.md` boot zinciri 4 satır (`Satır: 273-282`) bağlayıcıdır.
- **Kanıt standardı:** her iddia `Dosya:` + `Satır:` künyelidir; ölçüm iddiası tarihlidir (§12).

## §3. Boot Kaynakları — Hangi Dosya Ne Diyor

| # | Kaynak | Ne der | Kanıt |
|---|---|---|---|
| S1 | `AGENTS.md` (kök) §14 Boot Zinciri | 4 satır: AGENTS → CLAUDE → `.ai/AGENTS.md` → `.ai/` diğer (**yalnız ihtiyaç anında**) | `Dosya: kök AGENTS.md · Satır: 273-282` |
| S2 | `AGENTS.md` (kök) §7 Execution Loop | 0-9 adım (skill eşleşmesi → okuma → kod → lint → browser → commit orkestratörde) | kök `AGENTS.md §7` (bu sürümün boot instructions'ı) |
| S3 | `.ai/CLAUDE.md §16` | **13 kanonik** `.ai/` dosyası; 13 = 14 − 1 (CLAUDE kendisi hariç) | `Satır: 628-648` |
| S4 | `.ai/AGENTS.md §24.1` | 5 adım protokolü (Vault Oku → Bağlam → Hata → Tahmin → Doğrulama) | `.ai/AGENTS.md §24.1` |
| S5 | `.ai/AGENTS.md §24.2` | 14 satırlık `.ai/` referans tablosu (boot'ta okunan dosyalar) | `.ai/AGENTS.md §24.2` |
| S6 | `.ai/AGENTS.md §25.4` | Boot listesi uyumu: 14 kök + FULL 17 + MEMORY §5 (20 adım = genişletilmiş) | `.ai/AGENTS.md §25.4` |
| S7 | `.ai/MEMORY.md §5` | 20 adımlık **genişletilmiş** okuma seti (P0 3s → P2 5s, toplam max 36s) | `Dosya: .ai/MEMORY.md · Satır: 311-338` |
| S8 | `.ai/MEMORY.md §4` | Session lifecycle: Initialize (boot, 14 dosya, max 25s) → … → Session Close | `Dosya: .ai/MEMORY.md · Satır: 297-307` |
| S9 | `.claude/hooks/skill-mandate.cjs` | UserPromptSubmit gate: skill zorunluluğu bağlamı enjekte eder; asla bloklamaz | `Satır: 3, 87-88, 95-101` |
| S10 | `.claude/hooks/ai-risk-gate.cjs` | PreToolUse (Edit\|Write) gate: T1/T2 otorite dosyalarına yazma `ask` ister | `Satır: 3, 14-17, 36-49` |

**Öncelik kuralı:** çelişkide S3 (13 kanonik) boot **içeriği** için, S1 (kök §14) boot **zinciri**
için bağlayıcıdır; S5/S6 sayı farkını çözer (§5). S7 hiçbir zaman "kanonik liste" değildir.

## §4. Oturum Başına Adım Adım Akış (A-I)

```text
[A] Harness boot    → kök AGENTS.md + kök CLAUDE.md otomatik yüklenir (instructions[0..1])
[B] Hook tetik      → UserPromptSubmit: skill-mandate.cjs bağlam enjekte eder (§10)
[C] Skill eşleşmesi → Execution Loop 0: görev ↔ skill taraması; eşleşiyorsa İLK işlem skill (§7/S2)
[D] Vault boot      → .ai/CLAUDE.md → .ai/AGENTS.md → .ai/WORKFLOW.md (S3 sıra 1-3 + CLAUDE kendisi)
[E] Genişletme      → S3 kalan 10 dosya (brain/ROLE/index/keys/MEMORY/log/ULTRA/engine/glossary/VISION/PROJECTS)
[F] Session hafızası→ MEMORY.md §5 + log.md son 20 satır (session state + audit tail)
[G] Boot doğrulama  → varlık / FM / link / mojibake (§6, §12-§13); arıza → §8
[H] Görev geçişi    → Execution Loop 1-8 (hedef dosya okuma → kod → LINT → browser testi)
[I] Kapanış         → Execution Loop 9: commit YOK (orkestratöre ait) + vault-sync (§25)
```

### §4.1 Karar Ağacı — Adım D (vault boot sırasında durak noktaları)

| # | Durum | Eylem | Sonuç |
|---|---|---|---|
| 1 | `.ai/CLAUDE.md` okundu mu? | hayır → oku (P0) | okumadan devam YASAK |
| 2 | `.ai/AGENTS.md` okundu mu? | hayır → oku (P0) | routing yok → görev atanamaz |
| 3 | `.ai/WORKFLOW.md` okundu mu? | görev süreç/faz içeriyor mu? → evet ise oku (P0) | süreçsiz uygulama |
| 4 | Görev vault dışı konu (ör. genel kod) | E kalan 10 dosya **on-demand**: ihtiyaç anında `@` | token israfı yok |
| 5 | UI/CSS/HTML görevi | mockup gate: `.ai/ui-design/01-mockup-index.md` okunmadan kod YASAK | `Satır: 338` (MEMORY §5) |

### §4.2 Karar Ağacı — Adım G (boot doğrulama kapısından çıkış)

| Sonuç | Davranış | Referans |
|---|---|---|
| tüm P0 dosyalar var + FM sağlam | devam (H) | §6 |
| P1/P2 dosya eksik | raporla + devam (boot durmaz) | §8/1 |
| P0 dosya eksik | **DUR** + üst karar (Vault Steward) | §8/1 |
| mojibake tespit edildi | boot devam eder; okuma güveni düşer, işaretlenir | §6/3 · §26/Q2 |
| hook gate `ask` döndürdü | İNSAN onayı beklenir; onaysız yazım yok | §11 |

## §5. Boot Listesi Karşılaştırması (14 vs 20 vs 17 vs 13)

> **§25.4 fark çözümü (bağlayıcı özet):** Bu dört sayı **aynı şeyin farklı kapsamıdır**;
> biri diğerini "yanlışlamaz".

| Sayı | Kapsam | Kaynak | Durum |
|---|---|---|---|
| **13** | Kanonik `.ai/` okuma listesi (CLAUDE.md kendisi hariç) | `.ai/CLAUDE.md §16 · Satır: 630-646` | **kanonik içerik** |
| **14** | Kök `.ai/` boot dosyası (CLAUDE.md dahil) — 13 + 1 | `.ai/AGENTS.md §24.2` + `WORKFLOW.md §8.7A` (§25.4) | **kanonik kök liste** |
| **17** | FULL boot: root CLAUDE + root WORKFLOW + root README + 14 `.ai/` kök + 3 dizin (`.workflows`, `.ai/.templates`, `.ai/.agents`) | `.ai/AGENTS.md §25.4` | **genişletilmiş tam set** |
| **20** | MEMORY §5 genişletilmiş okuma: 14 kök + `.claude/rules/*` + prompt0-3 arşivleri + mockup indeksi | `Dosya: .ai/MEMORY.md · Satır: 311-334` | **genişletilmiş — kanonik DEĞİL** |

**Sayım zinciri:** 13 = 14 − 1 (`Satır: 648`) · 14 + 3 = 17 · 14 + `.claude/rules/*` + 4 prompt + 1 mockup = 20 (§25.4: "§5 genişletilmiş okuma setidir").
**Sürücü kuralı:** boot'ta **13 kanonik** okunur; 17/20 öğelik set görev tipine göre **talep üzerine** genişler (ör. frontend görevi → mockup indeksi zorunlu okunur, `Satır: 338`).
**Değiştirme yasağı:** 13 kanonik liste bu dosyayla değiştirilemez (`Dosya: .ai/CLAUDE.md · Satır: 648` — "bu kanonik listede `.ai/CLAUDE.md`'nin kendisi hariçtir").

## §6. Boot Doğrulama (varlık · link · mojibake)

| # | Kontrol | Yöntem | Kapı | Arıza davranışı |
|---|---|---|---|---|
| B1 | Dosya var mı? | `Test-Path` 14 kök + 13 kanonik | P0: hepsi var · P1/P2: eksik raporlanır | §8/1 |
| B2 | Frontmatter tam mı? | FM başlangıç `---` + kapanış `---`; 7 zorunlu alan (`.ai/CLAUDE.md §7`) | eksik → dosya "güvenilmez" işaretlenir | §8/2 |
| B3 | Wiki-link kırık mı? | regex `\[\[([^\]]+)\]\]` → hedef `Test-Path` | boot'ta **düzeltme YOK**, yalnız rapor | §8/3 |
| B4 | Mojibake var mı? | `U+00C3` / `U+00E2` sayımı (çift kodlama belirtisi) | 0 beklenir | §6/3 + §26/Q2 |
| B5 | Boot süresi | MEMORY §4: Initialize max 25s · §5: toplam max 36s | aşımda P2 adımları atlanabilir | §15 |

**Mojibake ölçümü (2026-10-17, gerçek):** `.ai/MEMORY.md` → **1063 × U+00C3 + 441 × U+00E2**
(çift kodlanmış UTF-8 belirtisi — read çıktısında `â€` dizgileri görünür);
karşılaştırma `.ai/AGENTS.md` → **0**. Boot sürücüsü bu bulguyu **işaretler, düzeltmez**
(düzeltme yetkisi Vault Steward — §26/Q2).

## §7. Boot Sonrası Görev Geçişi

| # | Adım | Boot ile ilişkisi | Kanıt |
|---|---|---|---|
| 1 | Skill eşleşmesi (Loop 0) | boot'tan hemen sonra, koddan önce | kök `AGENTS.md §7/0` |
| 2 | İlgili `.ai/` dosyaları on-demand | boot'ta olmayan dosya `@` ile açılır | kök `AGENTS.md §14` (4. satır) |
| 3 | Hedef dosya okuma | boot'ta olan dosya **yeniden okunmaz** (token kuralı) | §15/3 |
| 4 | Kod yazımı + LINT (Loop 5-6) | `.ai/CLAUDE.md §6` LINT + `.ai/.rules/senior-mode.md` | kök `AGENTS.md §7/6-7` |
| 5 | UI değişimi → browser testi | mockup gate boot'ta okunduysa geçerli | kök `AGENTS.md §7/8` |
| 6 | Commit | **subagent ATMAZ** — orkestratöre ait | kök `AGENTS.md §7/9` |

**Geçiş kuralı:** boot'un çıktısı "hafıza"dır; görev çıktısının kanıtı **ölçümdür** (§12-§13).
Boot tamamlanmadan üretilen çıktı kanıtsız sayılır (`Dosya: .ai/RAG.md · Satır: 128` F5 ruhu:
"kanıtsız bitti" yasak).

## §8. Boot Arızası Davranışı

| # | Arıza | Semptom | Davranış | Kaynak |
|---|---|---|---|---|
| 1 | P1/P2 dosya eksik | `Test-Path` false | raporla + devam; boot **durmaz**; görev riski yazılır | §4.2 |
| 2 | P0 dosya eksik (`CLAUDE`/`AGENTS`/`WORKFLOW`) | `Test-Path` false | **DUR** — boot tamamlanmaz, üst karar (Vault Steward) | `.ai/AGENTS.md §24.4` ("Eksik dosya → Hemen tamamla veya SIL" — silme yetkisi MO/Vault Steward) |
| 3 | Bozuk FM (kapanış `---` yok / alan eksik) | regex eşleşmiyor | dosya okunur ama **güvenilmez** işaretlenir; iddialar `⚠️ VERIFICATION REQUIRED` | `.ai/AGENTS.md §24.5` (FM 7 alan) |
| 4 | Kırık wiki-link | hedef `Test-Path` false | boot'ta **onarım YOK** (rapor); onarım vault-sync akışında | `Dosya: .ai/MEMORY.md · Satır: 360-366` (§7 bitiş 4. adım) |
| 5 | Mojibake | U+00C3/U+00E2 > 0 | işaretle + devam; okunan içerik güveni düşük; düzeltme Vault Steward | §6/3 |
| 6 | Hook `ask` (risk-gate) | `permissionDecision: ask` | İNSAN onayı bekle; onaysız yazım yok | §11 |
| 7 | Boot 3 kez aynı arızayla başarısız | tur sayacı 3 | **DUR** — şüpheli varsayımı yaz, üst karar iste | `.ai/AGENTS.md §17/4` (max 3 retry) |

## §9. Boot Retry Kuralları

| # | Kural | Değer | Kaynak |
|---|---|---|---|
| R1 | Aynı dosya okuma hatası | max 3 retry → DUR | `.ai/AGENTS.md §8` (öncelik tablosu max retry) |
| R2 | Retry öncesi | hatanın tekrarlanabilirliğini doğrula (yol/gizli dizin!) | §17/3 (`.ai/` → `hidden: true`) |
| R3 | Boot 25s/36s aşıldı | retry YOK; P2 adımları atlanır, raporlanır | `Dosya: .ai/MEMORY.md · Satır: 71, 336` |
| R4 | P0 arızası | retry değil **DUR + eskalasyon** (L1 → L2 → L3 → İnsan) | `.ai/AGENTS.md §10` |
| R5 | Vault bozulması (tümü okunamıyor) | `git checkout` + son commit → boot'u yeniden başlat | `.ai/AGENTS.md §17/10` |
| R6 | Hook hatası | hook her koşulda `exit 0` (prompt asla bloklanmaz); boot elle sürdürülür | `skill-mandate.cjs · Satır: 12, 188-195` |

## §10. Hook Gate Akışı — skill-mandate (tetik)

```text
Kullanıcı prompt yazar
  → UserPromptSubmit olayı tetiklenir (settings.json → hook kaydı)
    → skill-mandate.cjs stdin'den hook JSON okur
      → .claude/skills/ + global skills/ diskten taranır (SKILL.md varsa)
        → additionalContext enjekte edilir: "SKILL ZORUNLULUĞU … İLK işlem skill yüklemek"
          → stdout {"hookSpecificOutput": {...}} → exit 0 (asla bloklamaz)
```

**Gerçek alıntılar (`Dosya: .claude/hooks/skill-mandate.cjs`):**

| Satır | Alıntı |
|---|---|
| 3 | `CoreMusic — Skill Usage Mandate Hook (UserPromptSubmit)` |
| 12 | `Emniyet: her koşulda exit 0 (prompt asla bloklanmaz); --check hariç.` |
| 20 | `const SKILLS_DIR = path.join(PROJECT_DIR, '.claude', 'skills');` |
| 87-88 | `'SKILL ZORUNLULUĞU (kök CLAUDE.md §Skill Usage Mandate): Görev bir skill ile eşleşiyorsa'` / `'İLK işlem Skill tool ile yüklemektir; eşleşen skill'siz işlem başlatmak YASAKTIR.'` |
| 95-101 | `process.stdout.write(JSON.stringify({ hookSpecificOutput: { hookEventName: 'UserPromptSubmit', additionalContext } })); … process.exit(0);` |

**`--check` modu (denetim):** `node .claude/hooks/skill-mandate.cjs --check` → ihlal varsa
**exit 1** (`Satır: 171-178`); bu tek exit-1 yolu boot akışına dahil değildir, vault-sync
denetiminde çalıştırılır.

## §11. Hook Gate Akışı — ai-risk-gate (risk kontrol)

```text
Ajan bir dosyayı Edit/Write ile değiştirmek ister
  → PreToolUse (Edit|Write) olayı tetiklenir
    → ai-risk-gate.cjs stdin'den tool_input.file_path okur
      → yol göreli + '/' normalizasyonu
        → kök T1/T2 (CLAUDE.md · AGENTS.md · WORKFLOW.md · ULTRA-THINKING.md)?
           → evet: permissionDecision "ask" → İNSAN (Bayram Ali) onayı → exit 0
        → .ai/.rules/** (HIGH_RISK_DIRS)?
           → evet: permissionDecision "ask" → İNSAN onayı → exit 0
        → değilse: sessiz exit 0 (kapı kapalı, işlem normal akar)
```

**Gerçek alıntılar (`Dosya: .claude/hooks/ai-risk-gate.cjs`):**

| Satır | Alıntı |
|---|---|
| 3 | `ai-risk-gate.cjs — PreToolUse (Edit|Write)` |
| 8 | `Emniyet: her koşulda exit 0; ask yalnız HIGH risk dosyada.` |
| 14-16 | `const HIGH_RISK_FILES = new Set(['CLAUDE.md', 'AGENTS.md', 'WORKFLOW.md', 'ULTRA-THINKING.md']);` |
| 17 | `const HIGH_RISK_DIRS = ['.rules']; // .ai/.rules/** → HIGH` |
| 36 | `const ROOT_HIGH = new Set(['CLAUDE.md', 'AGENTS.md', 'WORKFLOW.md']);` |
| 42-45 | `permissionDecision: 'ask' … 'Control Plane HIGH-RISK kapısı (Q32): kök boot dosyası ' + rel + ' T1/T2 otorite yüzeyidir — İNSAN (Bayram Ali) onayı gerekir.'` |

**Boot ile ilişkisi:** risk-gate boot'un **yazma** yüzeyini korur; boot'un **okuma** yüzeyini
koruma yetkisi yoktur (hook PreToolUse Edit\|Write olayında tetiklenir, okumada tetiklenmez —
`Satır: 3`). Yani boot sırasında `.ai/CLAUDE.md`'yi okumak kapıdan geçer; onu değiştirmek `ask` ister.

## §12. PowerShell Ölçüm Kalıpları A — Boot Envanteri (gerçek, çalıştırılabilir)

> ✅ **Çalıştırılabilir PowerShell betiği** — repo kökünden (`C:\www\coremusic.net`) çalışır;
> çıkan sayılar 2026-10-17 ölçümüdür.

```powershell
# A) Boot envanteri: 14 kök .ai + kök boot dosyalarının satır sayısı (UTF8)
$files = @('.ai\MEMORY.md','.ai\CLAUDE.md','.ai\AGENTS.md','.ai\WORKFLOW.md',
  '.ai\index.md','.ai\keys.md','.ai\brain.md','.ai\log.md','.ai\ROLE.md','.ai\engine.md',
  '.ai\glossary.md','.ai\ULTRA-THINKING.md','.ai\VISION.md','.ai\PROJECTS.md',
  'AGENTS.md','CLAUDE.md','WORKFLOW.md')
foreach ($f in $files) {
  $p = Join-Path (Get-Location) $f
  if (Test-Path $p) { $n = [IO.File]::ReadAllLines($p, [Text.Encoding]::UTF8).Count
    "{0} => {1}" -f $f, $n } else { "{0} => MISSING" -f $f } }
```

**2026-10-17 ölçüm çıktısı (gerçek — §6 kapıları bu sayılarla okunur):**

| Dosya | Satır | Boot notu |
|---|---|---|
| `.ai\MEMORY.md` | **730** | §5 boot listesi 20 adım — mojibake VAR (§6/3) |
| `.ai\CLAUDE.md` | **987** | §16 kanonik 13 = 14 − 1 (Satır: 648) |
| `.ai\AGENTS.md` | **775** | §24.1 5 adım · §24.2 14 satır · §25.4 fark çözümü |
| `.ai\WORKFLOW.md` | **900** | §8.7A kök boot listesi |
| `.ai\index.md` | **246** | master katalog (boot P1) |
| `.ai\keys.md` | **763** | keyword haritası (boot P1) |
| `.ai\brain.md` | **1115** | mimari kararlar — 1000 satır limiti AŞILDI (§17/9) |
| `.ai\log.md` | **118** | audit trail — boot'ta son 20 satır (Satır: 323/641) |
| `.ai\ROLE.md` | **687** | rol tanımı (boot P1) |
| `.ai\engine.md` | **664** | orkestrasyon (boot P1) |
| `.ai\glossary.md` | **720** | terimler (boot P1) |
| `.ai\ULTRA-THINKING.md` | **769** | ultra düşünme (boot P1) |
| `.ai\VISION.md` | **638** | vizyon (boot P2) |
| `.ai\PROJECTS.md` | **722** | projeler (boot P2) |
| `AGENTS.md` (kök) | **285** | §14 boot zinciri Satır: 273-282 |
| `CLAUDE.md` (kök) | **285** | Skill Registry SSOT |
| `WORKFLOW.md` (kök) | **106** | süreç kökü |

## §13. PowerShell Ölçüm Kalıpları B — Doğrulama (mojibake · gate · üretim kapısı)

> ✅ **Çalıştırılabilir PowerShell betiği** — §6 kapıları B1/B4 ve bu dosyanın üretim kapısı.

```powershell
# B) Mojibake taraması: çift kodlama belirtisi (U+00C3 / U+00E2 sayımı) - kapı B4
$t = [IO.File]::ReadAllText((Join-Path (Get-Location) '.ai\MEMORY.md'), [Text.Encoding]::UTF8)
"c3 => {0}  e2 => {1}  (2026-10-17: 1063 / 441 - MOJIBAKE VAR)" -f `
  ([regex]::Matches($t, [char]0x00C3)).Count, ([regex]::Matches($t, [char]0x00E2)).Count

# C) P0/P1 varlık kapısı (B1): eksik dosya boot durdurur (P0) / raporlanır (P1)
$p0 = @('.ai\CLAUDE.md','.ai\AGENTS.md','AGENTS.md','CLAUDE.md')
$p1 = @('.ai\WORKFLOW.md','.ai\index.md','.ai\keys.md','.ai\brain.md','.ai\MEMORY.md',
        '.ai\log.md','.ai\ROLE.md','.ai\engine.md','.ai\glossary.md','.ai\ULTRA-THINKING.md',
        '.ai\VISION.md','.ai\PROJECTS.md')
$bad = 0
foreach ($f in $p0) { if (-not (Test-Path $f)) { $bad++; "P0-FAIL => $f" } }
foreach ($f in $p1) { if (-not (Test-Path $f)) { "P1-MISS => $f" } }
"p0 fail => {0} (kapı: 0)" -f $bad

# D) Wiki-link kırık taraması (B3): boot okunan dosyalarda [[...]] hedefleri
$doc = '.ai\MEMORY.md'
$links = [regex]::Matches([IO.File]::ReadAllText($doc), '\[\[([^\]]+)\]\]')
"link => {0}  (onarım boot'ta YOK - rapor only)" -f $links.Count

# E) Bu dosyanın üretim kapısı (>=500 + Authority + FM 10 alan tek seferde)
$p = '.ai\architecture\K015-AI\boot-protocol\boot-protocol-surucu.md'
$lines = [IO.File]::ReadAllLines((Join-Path (Get-Location) $p), [Text.Encoding]::UTF8)
"satir    => {0}   (kapı >=500: {1})" -f $lines.Count, ($lines.Count -ge 500)
$auth = '**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode'
"authority=> {0}" -f ($lines[$lines.Count - 1] -eq $auth)
$pat = '^(node_id|node_type|status|parent_id|children|depends_on|evidence|validation|open_questions|group):'
"fm 10    => {0}" -f ((Select-String -Path $p -Pattern $pat).Count -eq 10)
```

## §14. Ölçüm — Hook Kapılarının Sürücüdeki Karşılığı

| Hook | Olay | Boot sırasındaki yeri | Boot sonrası |
|---|---|---|---|
| `skill-mandate.cjs` | UserPromptSubmit | **adım B** — prompt anında tetik, boot okumasından önce bağlam verir | her prompt'ta yeniden tetik |
| `ai-risk-gate.cjs` | PreToolUse (Edit\|Write) | boot'ta tetiklenmez (okuma gate'i değil) | **adım H** — ilk yazım denemesinde `ask` doğar |

**Ölçüm notu (2026-10-17 glob kanıtı):** `.claude/hooks/` altında `skill-mandate.cjs` +
`ai-risk-gate.cjs` disktedir (`Dosya: .claude/hooks/` — bu üretimde iki dosya da okunup
alıntılanmıştır; uydurma hook yok). Hook kayıtları `.claude/settings.json` içindedir —
ayar dosyası salt-okunur bu üretimde (içerik `REDACTED` kuralı geçerli, `.claude/AGENTS.md` §3.3/1).

## §15. Token / Okuma Ekonomisi

| # | Kural | Sonuç | Kanıt |
|---|---|---|---|
| 1 | Boot'ta **13 kanonik** okunur; 17/20 set on-demand | token bütçesi korunur | §5 · `Satır: 648` |
| 2 | Boot'ta olan dosya görev sırasında **yeniden okunmaz** | çift israf yok | kök `AGENTS.md §7/2` |
| 3 | `.ai/` diğer dosyalar "yalnız ihtiyaç anında" | 14 kök + on-demand dengesi | kök `AGENTS.md §14` |
| 4 | Boot süresi 25s (Initialize) / 36s (toplam) — bütçe | uzun boot = görev başarısız riski | `Satır: 71, 307, 336` |
| 5 | log.md boot'ta **son 20 satır** (118 satırlık dosyanın kuyruğu) | tam dosya okuma israfı yok | `Satır: 323, 641` |
| 6 | Toplu vault okuma yasak ("başta tüm vault'u oku" geçersiz) | boot ≠ arşiv taraması | kök `AGENTS.md §9` |

## §16. Doğrulama Kapıları

| # | Kapı | Kriter | Yöntem / komut |
|---|---|---|---|
| 1 | Frontmatter | 10 alan eksiksiz | `Select-String` sayacı (§13/E) |
| 2 | Satır sayısı | `>= 500` | `[IO.File]::ReadAllLines(...).Count` (§13/E) |
| 3 | Authority son satır | birebir | `§13/E` eşitlik kontrolü |
| 4 | P0 varlık | 4/4 dosya var | `§13/C` (p0 fail = 0) |
| 5 | Mojibake | işaretleme yapıldı mı | `§13/B` (düzeltme Vault Steward — §26/Q2) |
| 6 | Boot listesi | 13 kanonik **değişmedi** | `Dosya: .ai/CLAUDE.md · Satır: 632-646` |
| 7 | Hook alıntıları | `Dosya:`/`Satır:` künyeli, birebir | §10-§11 tabloları (dosya bugün okundu) |
| 8 | Placeholder | şablon değişkeni (üçlü sürgü ile `\{`+`{`) yok | `Select-String -Pattern '\{\{'` → 0 |
| 9 | Kapsam dışı yazım | 0 dosya | `git status --porcelain` whitelist |
| 10 | `git commit` | **0** (subagent yasak) | §18 |

## §17. Hata Modları

| # | Hata | Semptom | Tepki |
|---|---|---|---|
| 1 | Boot listesi karıştırıldı (13/14/17/20) | yanlış "eksik dosya" iddiası | §5 tablosuna dön |
| 2 | `.ai/` gizli dizin unutuldu | sahte "0 sonuç" | `hidden: true` (kök `AGENTS.md §7/2`) |
| 3 | Boot tamamlanmadan kod | kanıtsız çıktı | DUR → §7'ye dön |
| 4 | Hook bloklanıyor sanma | hook `exit 0` ile asla bloklamaz | `skill-mandate.cjs · Satır: 12` |
| 5 | risk-gate boot'u engelliyor sanma | gate yalnız **yazmada** tetik | §11 (`Satır: 3`) |
| 6 | Bellekten boot satır sayısı | ölçüm tarihi yok | §12 betiği ile ölç |
| 7 | boot arızasında sessiz devam (P0) | anayasa okunmadan iş | **DUR + eskalasyon** (§8/2) |
| 8 | 3 başarısız boot turu | aynı hata tekrarı | DUR — üst karar (§9/R1) |
| 9 | `brain.md` 1115 satır > 1000 limit | MEMORY §16 limiti aşıldı | raporla — bölme yetkisi Vault Steward |
| 10 | `.ai/` içinden gate betiği çalıştırma | sahte başarısızlıklar | **kökten** çalıştır (kök `AGENTS.md §13.6`) |

## §18. Yasaklar ve Sınırlar

| # | Yasak | Neden |
|---|---|---|
| 1 | 13 kanonik boot listesini değiştirmek | sayım zinciri 13 = 14 − 1 bozulur (`Satır: 648`) |
| 2 | `git commit` atmak | orkestratöre ait (kök `AGENTS.md §7/9`) |
| 3 | Boot arızasında P0 dosyasız devam etmek | Guardrail #2 Vault First ihlali |
| 4 | Boot'ta wiki-link onarımı | boot okuma-only; onarım vault-sync (§8/4) |
| 5 | Mojibake'de sessiz düzeltme | yetki Vault Steward; boot'ta yalnız işaretleme |
| 6 | `.claude/hooks/*.cjs` yazma | salt-okunur bu üretimde (yalnız okuma/alıntı) |
| 7 | Hook `exit 1` ile prompt bloklama | tasarım gereği `exit 0` (Satır: 12/8) |
| 8 | 20 adımlık seti "kanonik" diye dayatmak | §25.4: genişletilmiş okuma setidir |

## §19. Anti-Patternler (sık yapılan hatalar)

| # | Yanlış | Doğru |
|---|---|---|
| 1 | "Boot = tüm vault'u oku" | Boot = 13 kanonik; gerisi on-demand (kök `AGENTS.md §9`) |
| 2 | Boot listesini MEMORY §5'ten okumak | Kanonik = `.ai/CLAUDE.md §16` (§5 bu dosya) |
| 3 | Boot'u 25sn'de bitiremezsem dosya atlamak | P2 atlanır, P0/P1 atlanmaz (§9/R3) |
| 4 | Hook exit code'undan boot kilidi çıkarmak | Hook `exit 0`; boot akışını hook yönetmez (§17/4) |
| 5 | Mojibake'li dosyayı "düzeltilmiş" saymak | Ölçüm (§13/B) + Vault Steward kararı (§26/Q2) |
| 6 | boot-protocol'i ajan boot'una PHP bootstrap ile karıştırmak | `RuntimeBootstrap::boot()` uygulama boot'udur (§20) |

## §20. Repo Kanıtı — PHP Bootstrap'i (bu konu DEĞİL, negatif kapsam kanıtı)

> **Gerçek alıntı** — repo'da "boot" adını taşıyan tek çekirdek sınıf uygulama (runtime)
> bootstrap'idir; **ajan boot protokolünü okuyan PHP kodu YOK** (grep 2026-10-17: `boot|Boot|BOOT`
> → `RuntimeBootstrap`, `SessionBootstrapper`, `SessionLifecycle`, `SessionManagerMiddleware` —
> hepsi HTTP oturum/zaman dilimi kurulumu).

```php
<?php declare(strict_types=1);                                    // Satır 1
namespace CoreMusic\Bootstrap;                                     // Satır 3
final class RuntimeBootstrap                                      // Satır 5
{
    public static function boot(bool $debugMode = false): void     // Satır 7
    {
        date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Europe/Istanbul'); // Satır 9
        // … error_reporting / ini_set (Satır 11-19)
    }
}
```

**Sonuç:** `RuntimeBootstrap::boot()` PHP uygulamasının çalışma zamanı kurulumudur (timezone,
error_reporting). Ajan boot protokolü (bu dosyanın konusu) **markdown + hook** katmanında yaşar;
PHP'de karşılığı **YOK** — `Dosya: shared/src/Bootstrap/RuntimeBootstrap.php · Satır: 5-20`.
Aynı dosyada `SessionBootstrapper` (`Satır: 8-15`) de `session_*` API'lerinin tek yetkili
giriş noktasıdır — ad benzerliği yanıltmasın, konu dışıdır.

## §21. İllüstratif — TypeScript Boot Validator

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```typescript
type BootTier = 'P0' | 'P1' | 'P2';
interface BootEntry { file: string; tier: BootTier; required: boolean; }

/** Boot kapıları: P0 eksikse DUR, P1/P2 eksikse rapor+devam (§8/1-2). */
export async function validateBoot(entries: BootEntry[], exists: (p: string) => boolean) {
  const missing = entries.filter((e) => !exists(e.file));
  const p0 = missing.filter((e) => e.tier === 'P0' && e.required);
  if (p0.length > 0) {
    return { ok: false, action: 'HALT' as const, reason: `P0 eksik: ${p0.map((e) => e.file).join(', ')}` };
  }
  return { ok: true, action: 'PROCEED' as const, report: missing.map((e) => e.file) };
}
```

## §22. İllüstratif — Python Mojibake + Retry

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
# Boot B4 kapısı (mojibake) + R1 (max 3 retry) ruhu — §6/3, §9/R1
def mojibake_hits(text: str) -> int:
    return text.count("\u00c3") + text.count("\u00e2")   # §13/B sayımının karşılığı

def boot_with_retry(read, path: str, max_retry: int = 3):
    for attempt in range(1, max_retry + 1):
        try:
            text = read(path)                            # IO hatası → retry
            flag = mojibake_hits(text) > 0               # işaretle, düzeltme YOK (§18/5)
            return {"ok": True, "mojibake": flag, "attempt": attempt}
        except OSError:
            continue
    return {"ok": False, "action": "HALT", "attempt": max_retry}   # §9/R1: 3 → DUR
```

## §23. İllüstratif — Go Boot Sıra Denetleyicisi

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
package boot

// CanonicalOrder, .ai/CLAUDE.md §16 13 kanonik sırasının iskeletidir (Satır: 632-646).
var CanonicalOrder = []string{
	".ai/CLAUDE.md", ".ai/AGENTS.md", ".ai/WORKFLOW.md", ".ai/brain.md", ".ai/ROLE.md",
	".ai/index.md", ".ai/keys.md", ".ai/MEMORY.md", ".ai/log.md", ".ai/ULTRA-THINKING.md",
	".ai/engine.md", ".ai/glossary.md", ".ai/VISION.md", ".ai/PROJECTS.md",
}

// OrderValid, okuma sırasında atlanan P0/P1 dosyası olup olmadığını denetler (§7/3).
func OrderValid(readFiles []string) (bool, string) {
	want := CanonicalOrder[:3] // P0 üçlü: CLAUDE, AGENTS, WORKFLOW
	for _, w := range want {
		found := false
		for _, r := range readFiles {
			if r == w { found = true; break }
		}
		if !found { return false, "P0 okunmadı: " + w }
	}
	return true, ""
}
```

## §24. Hızlı Eşleme — Boot Durumu → Davranış

| Durum | Davranış | Kapı |
|---|---|---|
| İlk oturum açılışı (session state boş) | 13 kanonik + MEMORY §5 (ilk 200 satır session kaydı) | §4/F |
| Devam oturumu (MEMORY'de önceki session var) | 13 kanonik + MEMORY §5 tail + log.md son 20 satır | §4/F |
| Frontend/UI görevi | boot + mockup indeksi zorunlu (kod öncesi) | §4.1/5 |
| Vault-yazma görevi | boot + risk-gate `ask` kapısı (İNSAN onayı) | §11 |
| Arızalı boot (P0 eksik/bozuk) | **DUR** + eskalasyon — retry değil | §8/2, §9/R4 |
| Boot 3. kez aynı hatayla | **DUR** — şüpheli varsayım yaz, üst karar iste | §9/R1 |

## §25. Kapanış Kontrol Listesi (boot tamamlanınca)

| # | Soru | Zorunlu cevap |
|---|---|---|
| 1 | 13 kanonik sırayla okundu mu (P0 önce)? | evet |
| 2 | Boot doğrulaması çalıştırıldı mı (§13 betikleri)? | evet |
| 3 | Mojibake/varlık bulguları raporlandı mı? | evet (işaretleme — düzeltme Vault Steward) |
| 4 | Boot listesi değiştirildi mi? | **hayır (13 sabit)** |
| 5 | Boot'ta okunan dosya görevde yeniden okundu mu? | **hayır** (token kuralı) |
| 6 | Hook alıntıları künyeli mi? | evet (§10-§11) |
| 7 | `git commit` atıldı mı? | **hayır (orkestratöre ait)** |
| 8 | Kapsam dışı dosyaya yazıldı mı? | **hayır (0)** |

## §26. Açık Sorular

| # | Soru | Durum |
|---|---|---|
| Q1 | `depends_on: [boot-protocol-anayasa]` — bu turda `boot-protocol/` altında ilk glob'da yalnız `index.md` vardı; üretim kapanışında `boot-protocol-anayasa.md` + `boot-protocol-ornekler.md` diskte belirdi (git status 2026-10-17) → bağımlılık **var**; içerik eşlemesi (bu dosya anayasa ile çelişiyor mu?) okunarak doğrulanmalı | `⚠️ VERIFICATION REQUIRED` (dosya var — içerik okunmadı) |
| Q2 | `.ai/MEMORY.md` mojibake (1063 × U+00C3 / 441 × U+00E2 ölçüldü 2026-10-17) — boot akışında düzeltilecek mi, ayrı Vault Steward görevi mi? | Vault Steward kararı — boot'ta düzeltme YOK (§18/5) |
| Q3 | Ebeveyn `boot-protocol/index.md` `children: []` + "Kademe-3 bu fazda AÇILMADI" (Satır: 6) diyor; bu modül eklendikten sonra `children` güncellenecek mi? | orkestratör kararı |
| Q4 | Boot otomasyonu (`.ai/scripts/boot-check.ps1` benzeri betik) diskte YOK — 2026-10-17 glob: boot doğrulama elle mi kalacak, betik üretilecek mi? | `UNKNOWN` (uydurulmaz) |
| Q5 | `.ai/MEMORY.md` 730 satır vs `.ai/AGENTS.md §16` limiti "1000 satır" (MEMORY §3) — `brain.md` 1115 satır limiti aşıyor; bölme/değişiklik planı? | Vault Steward kararı |
| Q6 | 20 adımlık MEMORY §5 seti ile 13 kanonik arasındaki `.claude/rules/*` + prompt arşivleri hâlâ güncel mi? (2026-10-07 `.opencode/skills` kapatıldı, prompt arşivleri `archives/`'a taşındı — §5'te yol `archives/prompt*-2026-09-01` yazıyor, diskte var mı?) | `⚠️ VERIFICATION REQUIRED` |

## §27. Kaynaklar

| Kaynak | Yol | Kullanım amacı | Ölçüm |
|---|---|---|---|
| Boot zinciri (bağlayıcı 4 satır) | `AGENTS.md` (kök) §14 | boot sırası + "yalnız ihtiyaç anında" | 285 satır |
| Execution Loop | `AGENTS.md` (kök) §7 | 0-9 adım (skill eşleşmesi → commit) | 285 satır |
| Kanonik boot listesi | `.ai/CLAUDE.md` §16 | 13 = 14 − 1 (Satır: 628-648) | 987 satır |
| 5 adım protokolü | `.ai/AGENTS.md` §24.1 | Vault Oku → Doğrulama | 775 satır |
| Fark çözümü | `.ai/AGENTS.md` §25.4 | 14 / 17 / 20 sayılarının anlamı | 775 satır |
| Genişletilmiş boot + lifecycle | `.ai/MEMORY.md` §4-§5 | 20 adım, P0-P2, 25s/36s bütçesi | 730 satır (mojibake VAR) |
| Skill gate hook | `.claude/hooks/skill-mandate.cjs` | §10 alıntıları (Satır: 3, 12, 20, 87-88, 95-101) | okundu 2026-10-17 |
| Risk gate hook | `.claude/hooks/ai-risk-gate.cjs` | §11 alıntıları (Satır: 3, 8, 14-17, 36, 42-45) | okundu 2026-10-17 |
| Uygulama bootstrap'i (negatif kapsam) | `shared/src/Bootstrap/RuntimeBootstrap.php` | §20 — ajan boot'u DEĞİL | 21 satır (boot() Satır: 7) |
| Ebeveyn düğüm | `.ai/architecture/K015-AI/boot-protocol/index.md` | kapsam sınırı + `children` durumu (Satır: 6) | 49 satır |
| Yapı referansı | `.ai/architecture/K015-AI/rag/rag-surucu.md` | H1 + § + karar ağacı + tablo iskeleti | 779 satır |

## §28. Gate (özet)

§1-§28 tamam: ELI15 · kapsam · 10 kaynak · A-I akış (karar ağaçları 2 tablo) · boot listesi
4 varyant çözümü (13/14/17/20) · 5 maddelik doğrulama · 6 maddelik görev geçişi · 7 maddelik
arıza · 6 retry kuralı · 2 hook gate akışı (gerçek alıntı 13 satır) · PowerShell 5 betik
(A-E, gerçek ölçüm 17 dosya) · 6 token kuralı · 10 kapı · 10 hata modu · 8 yasak · 6
anti-pattern · PHP negatif kapsam kanıtı · illüstratif 3 blok (TS/Python/Go) · hızlı eşleme
6 satır · 8 soruluk kapanış · 6 açık soru · 11 kaynak.

**Kapılar:** satır `>= 500` · FM **10 alan** · Authority son satır birebir · kapsam dışı yazım
**0** · `git commit` **0** · boot listesi **13 sabit**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode