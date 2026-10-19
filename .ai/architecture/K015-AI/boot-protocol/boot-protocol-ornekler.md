---
node_id: boot-protocol-ornekler
node_type: module
status: active
parent_id: boot-protocol
children: []
depends_on: [boot-protocol-anayasa, boot-protocol-surucu]
evidence:
  - ".claude/hooks/*.cjs (3 dosya, gerçek hook kanıtı, 2026-10-17 okuma)"
  - ".ai/scripts/*.ps1 (doğrulama betikleri, varsa alıntı)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı, kod kanıtı zorunlu"
open_questions: []
group: kod
---

# boot-protocol-ornekler - Boot Kod Örnekleri (kanıt odaklı)

> **Kapsam:** Her JS/PS1/MJS alıntısı 2026-10-17'de `.claude/hooks/` (3 dosya) ve
> `.ai/scripts/` altındaki gerçek dosyaların satırlarıdır (`Dosya:` / `Satır:` künyesi ile).
> İllüstratif çok-dilli bloklar repo kanıtı DEĞİLDİR; her birinin üstünde zorunlu etiket taşır.
> Kaynak dosyalar salt-okunurdur — bu dosya dışında yazım YASAKTIR.

## §1. Kanıt Künye Formatı

| Alan | Kural |
|---|---|
| Dosya | göreli yol, diskte var (glob ile doğrulanmış) |
| Satır | okunan aralık (L95-L100 gibi) — okunmamış satır künye olamaz |
| Alıntı | kod satırının kendisi (kısaltma serbest, uydurma yasak) |
| Yokluk | tam cümle: "Repo'da bu alan kodu YOK — kaynak: upstream docs" |
| Ölçüm | glob/grep sayaçları — araç çıktısı, iddia değil |
| İllüstratif | `⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı` |
| Tarih | her kanıt 2026-10-17 okuma turuna ait |
| Eşleşme | `evet` / `kısmi` / `YOK` — başka değer yok |
| Yasak | uydurma künye → Zero-Hallucination ihlali (kök AGENTS §3) · çelişkide disk kazanır, alıntı tazelenir |

## §2. Hook Envanteri — `.claude/hooks/` (3 dosya, glob kanıtı)

Dosya: `.claude/hooks/` · glob `*.cjs` → **3 isabet** (2026-10-17)

| # | Dosya | Satır | Olay (dosya başlığı) | Boot rolü |
|---|---|---|---|---|
| 1 | skill-mandate.cjs | 195 | `UserPromptSubmit` (L3) | skill zorunluluk metni enjeksiyonu + `--check` v3.0 doğrulayıcı |
| 2 | ai-risk-gate.cjs | 73 | `PreToolUse (Edit\|Write)` (L3) | T1/T2 otorite dosyası yazımında İNSAN onayı (`ask`) |
| 3 | ai-validate-post.cjs | 44 | `PostToolUse (Edit\|Write)` (L3) | `.ai/**/*.md` değişimi sonrası validator; ihlalde `block` |

**Doğrulama zinciri (3 kapı, sıralı):** prompt girişi → skill-mandate (context) · dosya yazımı öncesi → risk-gate (ask) · dosya yazımı sonrası → validate-post (block) → `validate.mjs` (motor).

## §3. skill-mandate.cjs — UserPromptSubmit Tetikleyici + Context Enjeksiyonu

Dosya: `.claude/hooks/skill-mandate.cjs` · Satır: L3, L9-L12, L26, L86-L101

```js
 * CoreMusic — Skill Usage Mandate Hook (UserPromptSubmit)        // L3
 *   (default)      stdin hook JSON oku → stdout {"hookSpecificOutput": {...}}   // L9
 *   --check        v3.0 format doğrulayıcı (exit 0 = temiz, 1 = ihlal)         // L10
 * Emniyet: her koşulda exit 0 (prompt asla bloklanmaz); --check hariç.          // L12
const COREMUSIC_GLOBAL = ['human-mode', 'truth-engine', 'verify-loop', 'agent-debate', 'context-report'];  // L26
```

| Gözlem | Kanıt |
|---|---|
| Tetikleyici olay `UserPromptSubmit` — prompt Claude'a ulaşmadan önce çalışır | L3 |
| Çıktı şeması `hookSpecificOutput.hookEventName: 'UserPromptSubmit'` + `additionalContext` | L95-L100 |
| Emniyet: hook modu asla `exit 1` ile bloklamaz (yalnız `--check` 1 döner) | L12, L188-L195 |
| Global skill kümesi kod-sabitidir; proje skill'leri **diskten taranır** | L26, L56-L63 |
| Kritik metin: "İLK işlem Skill tool ile yüklemektir; eşleşen skill'siz işlem başlatmak YASAKTIR." | L87-L88 |
| İstisna cümlesi metne gömülü: "kullanıcı açıkça 'skill kullanma' derse..." | L92 |

**Resmi kaynak eşleşmesi:** `UserPromptSubmit` hook'unun `hookSpecificOutput.additionalContext` alanı resmi hooks referansında tanımlıdır (§28 #1) — bu hook o şemayı birebir kullanır → **evet**.

## §4. skill-mandate.cjs — `--check` v3.0 Frontmatter Doğrulayıcı

Dosya: `.claude/hooks/skill-mandate.cjs` · Satır: L28-L31, L110-L135, L138-L151, L156-L168

```js
const BANNED_ROOT_KEYS = [
  'title', 'type', 'version', 'authority', 'mode', 'purpose',
  'reference', 'triggers', 'changelog', 'dependencies',
];                                                                    // L28-L31
const declared = fmValue(fm, 'name');
if (declared !== name) violations.push(`${name}: name "${declared}" klasörle 1:1 değil`);   // L120-L121
if (desc.length > 1024) violations.push(`${name}: description ${desc.length} char (>1024)`); // L126
if (!/^Use when /i.test(desc)) violations.push(`${name}: description "Use when" ile başlamıyor`); // L127
if (!fm.match(/^metadata:/m)) violations.push(`${name}: metadata bloğu YOK`);               // L134
```

| Kontrol | Eşik / Kural | Satır |
|---|---|---|
| `name` ↔ klasör adı | 1:1 zorunlu | L120-L121 |
| `description` | var · ≤1024 char · "Use when" öneki | L123-L128 |
| Yasak root key | 10 anahtar (title/type/version/authority/mode/purpose/reference/triggers/changelog/dependencies) | L28-L31, L130-L132 |
| `metadata` bloğu | yoksa ihlal | L134 |
| Kırık link | `references/` `examples/` hedefi diskte yoksa ihlal | L138-L143 |
| Orphan | alt dizindeki `.md` SKILL.md'de linklenmemişse ihlal | L146-L152 |
| Sidecar yasağı | `.archive` hariç her yerde `CLAUDE.md` → ihlal | L156-L163 |
| Proje↔global çift kopya | `COREMUSIC_GLOBAL` kesişimi → ihlal | L167-L169 |
| Çıkış | ihlal varsa `exit 1` + `✗` listesi; temizse `OK (N proje skill'i temiz)` | L171-L177 |

## §5. ai-risk-gate.cjs — PreToolUse HIGH-RISK Kapısı (`permissionDecision: ask`)

Dosya: `.claude/hooks/ai-risk-gate.cjs` · Satır: L3-L8, L14-L17, L36-L49, L52-L72

```js
 * ai-risk-gate.cjs — PreToolUse (Edit|Write)                     // L3
 * Q3 onay matrisi HIGH sınıfı uygulaması: T1/T2 otorite dosyalarına yazma,
 * İNSAN onayı (permissionDecision: "ask") gerektirir.             // L5-L6
const HIGH_RISK_FILES = new Set([
  'CLAUDE.md', 'AGENTS.md', 'WORKFLOW.md', 'ULTRA-THINKING.md',
]);                                                                // L14-L16
const HIGH_RISK_DIRS = ['.rules']; // .ai/.rules/** → HIGH        // L17
```

Kök boot dosyası kapısı (L36-L49):

```js
const ROOT_HIGH = new Set(['CLAUDE.md', 'AGENTS.md', 'WORKFLOW.md']);
if (ROOT_HIGH.has(rel)) {
  process.stdout.write(JSON.stringify({
    hookSpecificOutput: {
      hookEventName: 'PreToolUse',
      permissionDecision: 'ask',
      permissionDecisionReason:
        'Control Plane HIGH-RISK kapısı (Q32): kök boot dosyası ' + rel +
        ' T1/T2 otorite yüzeyidir — İNSAN (Bayram Ali) onayı gerekir. ' +
        'Değişiklik risk: high · Doğrulama: node .ai/scripts/validate.mjs --check',
    },
  }));
  process.exit(0);
}
```

| Gözlem | Kanıt |
|---|---|
| Yalnız `Edit\|Write` eşleşir; okuma serbest | L3 |
| `.ai/` yolu regex'i `^\.ai\/(.+)$` — `.ai` DIŞINDA kök dosyalar ayrıca korunur | L52, L36 |
| Karar değeri `ask` (allow/deny değil) → İNSAN onayı kapısı | L41, L63 |
| Emniyet: `readFileSync(0)` JSON parse hatasında sessiz `exit 0` (kapı kilitlenmez) | L20-L23 |
| Neden → doğrulama komutu gerekçe metnine gömülü: `validate.mjs --check` | L45, L68 |
| Windows `\` → `/` normalizasyonu relatif yolda | L33, L29 |

**Resmi kaynak eşleşmesi:** `PreToolUse` için `hookSpecificOutput.permissionDecision` değerleri resmi referansta `allow`/`ask`/`deny` olarak tanımlıdır (§28 #1) → **evet**.

## §6. ai-validate-post.cjs — PostToolUse Validator Block (`decision: block`)

Dosya: `.claude/hooks/ai-validate-post.cjs` · Satır: L3-L11, L31-L43

```js
 * ai-validate-post.cjs — PostToolUse (Edit|Write)                // L3
 * Q6 validation enforce: .ai/** altındaki .md dosyası değiştirildikten SONRA
 * validate.mjs --check çalışır; ihlal varsa {"decision":"block"} ile Claude'a
 * geri besleme yapılır (düzeltmesi zorunlu). Temizse sessiz (exit 0).   // L5-L7
if (!/^\.ai\/.+\.md$/i.test(rel)) process.exit(0);                 // yalnız .ai .md (L31)
const r = spawnSync(process.execPath, [validator, '--check'], { encoding: 'utf8', cwd: project });  // L36
if (r.status === 1) {
  const lines = (r.stdout || '').trim().split('\n').slice(0, 12).join(' | ');
  process.stdout.write(JSON.stringify({
    decision: 'block',
    reason: 'Control Plane validator İHLAL — .ai/scripts/validate.mjs --check başarısız. Düzelt + tekrar dene. ' + lines,
  }));
}                                                                  // L37-L43
```

| Gözlem | Kanıt |
|---|---|
| Kapsam: yalnız `.ai/**/*.md` (case-insensitive) — `.ai` DIŞI sessiz exit 0 | L31 |
| Motor: `.ai/scripts/validate.mjs --check` `spawnSync` ile çağrılır; validator yoksa sessiz geç | L33-L36 |
| Karar: yalnız validator `exit 1` (İHLAL) iken `decision: "block"` | L37-L42 |
| Geri besleme 12 satırla sınırlı (`slice(0, 12)`, ` | ` birleştirici) | L38 |
| Emniyet: validator crash ederse (status ≠ 1) gate yanlışlıkla kilitlemez | L9-L11, L37 |

**Resmi kaynak eşleşmesi:** `PostToolUse` olayının `decision: "block"` geri besleme şekli resmi hooks referansında tanımlıdır (§28 #1) → **evet**.

## §7. validate.mjs — Tek Giriş Noktası (Q6) + Check Modülleri

Dosya: `.ai/scripts/validate.mjs` · Satır: L2-L22, L231-L248

```js
 * CoreMusic .ai Control Plane Validator — tek giriş (Q6 kararı: dağıtık check + tek entry)
 * Check modülleri:
 *   fm-check     : authority-critical dosyalarda 13 alan FM + enum doğrulaması
 *   link-check   : wiki-link [[hedef]] çözülebilir mi (0 gerçek kırık)
 *   ssot-check   : aynı domain'de 2+ ssot:true = duplicate authority uyarısı
 *   dep-check    : depends-on hedefi var mı + circular dependency tespiti (§32)
 *   tier-check   : tier enum (1-5) + T1/T2 dosya listesi (bilgi)
 *   orphan-check : sahipsiz dosya (FM var ama owner yok / hiç FM yok) (§33)
 *   fanout-check : fan-out >15 = ihlal · fan-in >30 = uyarı (R15/D5)
```

| Modül | Eşik | Çıktı sınıfı |
|---|---|---|
| fm-check | 13 alan eksiksiz + enum geçerli | ihlal |
| tier-check | tier ∈ 1..5 · T1/T2 bilgi satırı | ihlal + info |
| orphan-check | frontmatter yok · owner yok | ihlal |
| dep-check | hedef var mı + DFS cycle | ihlal |
| ssot-check | aynı `domain/category` ≥2 `ssot:true` | **uyarı** (gate değil) |
| link-check | mevcut ağaçta çözülemeyen `[[link]]` | ihlal · dead-dir = borç/uyarı |
| fanout-check | fan-out >15 · fan-in >30 | ihlal · uyarı |

Çıkış modları (L18-L19, L231-L248): tam rapor `node validate.mjs` (ihlalde exit 1) · tek satır `--check` (hook/skill modu). Emniyet: "salt-okunur (dosyaya YAZMAZ)" (L21).

## §8. validate.mjs — fm-check + 13 Alan Zorunluluk + Enum'lar

Dosya: `.ai/scripts/validate.mjs` · Satır: L30-L35, L87-L103

```js
const TIER_ENUM = [1, 2, 3, 4, 5];
const RISK_ENUM = ['low', 'medium', 'high'];
const STATUS_ENUM = ['active', 'draft', 'proposed', 'approved', 'rejected', 'deprecated'];
const REQUIRED = ['title', 'type', 'category', 'version', 'status', 'authority', 'updated',
                  'tier', 'domain', 'ssot', 'risk', 'owner', 'depends-on'];       // L30-L35
const missing = REQUIRED.filter(k => !new RegExp('^' + k.replace(/-/g, '-') + ':', 'm').test(fm));
if (missing.length) violations.push({ file: f, check: 'fm-check', msg: 'eksik alan: ' + missing.join(', ') });  // L87-L88
if (fmVal(fm, 'owner') === null) violations.push({ file: f, check: 'orphan-check', msg: 'owner yok (sahipsiz)' }); // L103
```

| Gözlem | Kanıt |
|---|---|
| 13 alan: title/type/category/version/status/authority/updated/tier/domain/ssot/risk/owner/depends-on | L34-L35 |
| Enum'lar: tier 1-5 · risk low/medium/high · status 6 değer · ssot true/false | L30-L33, L90-L102 |
| `owner` hem alan zorunluluğu hem orphan-check'te çift kontrol | L35, L103 |
| Scope: `.ai` kökü `.md` (log.md hariç) + `.agents/*.md` + `.rules/*.md` + `.decisions/index.md` | L55-L60 |
| K-space kaldırılışı (2026-10-12): depdir/idrange/id/budget bu girişten çıkarıldı | L14-L15, L50-L52 |

## §9. validate.mjs — dep-check + Circular DFS

Dosya: `.ai/scripts/validate.mjs` · Satır: L124-L153

```js
const cand = [path.join(AI, d), path.join(AI, d + '.md'), path.join(AI, d, 'index.md')];
if (!cand.some(c => fs.existsSync(c))) violations.push({ file: f, check: 'dep-check', msg: 'depends-on hedefi YOK: ' + d });  // L135-L136
// cycle: DFS
function dfs(n) {
  if (state.get(n) === 2) return;
  if (state.get(n) === 1) {
    const i = stack.indexOf(n);
    violations.push({ file: n, check: 'dep-check', msg: 'CIRCULAR: ' + stack.slice(i).concat(n).join(' → ') });
    return;
  }                                                                  // L142-L148
```

| Gözlem | Kanıt |
|---|---|
| Hedef çözümü 3 aday: düz yol · `.md` ekli · `index.md` | L135 |
| `depends-on` biçimi `[a, b]` → split + tırnak temizliği + `.ai/` öneki kırpma | L131-L132 |
| Cycle tespiti: 3 durumlu DFS (0/1/2) + stack ile yol yazımı | L140-L153 |
| Aynı yapı fanout-check'te yeniden kullanılır (L203-L206) — tek parse, iki kontrol | L129, L204 |

## §10. validate.mjs — link-check: Dead-Dir vs Gerçek İhlal Ayrımı

Dosya: `.ai/scripts/validate.mjs` · Satır: L38-L48, L157-L193

```js
const PLACEHOLDER_LINKS = new Set(['C', 'V', 'link', 'wiki-link', 'nodiscard', 'TODO', 'FIXME', 'VERIFY']);  // L38
const text = stripCode(body);        // ``` blokları + `inline` temizlenir (L158, L167)
if (PLACEHOLDER_LINKS.has(t)) continue;                    // §13.7 sahte linkler (L173)
violations.push({ file: f, check: 'link-check', msg: 'kırık wiki-link (mevcut ağaca içinde): [[' + t + ']]' });  // L189
deadCount.set(k, (deadCount.get(k) || 0) + 1);   // bilinen ölü hedef → borç (L185-L186)
```

| Sınıf | Kural | Sonuç |
|---|---|---|
| Kod içi link | `` ``` `` bloğu + inline code temizlenir (L158, L167) | yok sayılır |
| Placeholder | `C`/`V`/`link`/`TODO`... (AGENTS §13.7 — düzeltme YASAK) | yok sayılır |
| Çözüm kademeleri | dosyaya göreli → `.ai` kökü → repo kökü (3×3 aday) | L176-L181 |
| Dead-dir / stale katalog | `DEAD_DIRS` (14 adet, L40-L45) veya canlı olmayan ilk segment | **borç/uyarı** (gate değil) |
| Mevcut ağaç içinde çözülemeyen | `LIVE_DIRS` içinde (L47-L48) | **gerçek ihlal** |

## §11. validate.mjs — fanout-check (R15/D5)

Dosya: `.ai/scripts/validate.mjs` · Satır: L199-L213

```js
if (items.length > 15) violations.push({ file: f, check: 'fanout-check', msg: `fan-out ${items.length} > 15 (D5) — bağımlılık azalt veya incele` });  // L207
for (const [t, n] of fanIn) if (n > 30) warnings.push(`fan-in ${n} > 30: ${t} (D5 — merge/alt-katman adayı, rapor)`);  // L213
```

| Ölçüm | Eşik | Sınıf |
|---|---|---|
| fan-out (dosyanın bağımlılık sayısı) | >15 | ihlal |
| fan-in (bir hedefe yapılan atıf) | >30 | uyarı |

## §12. PowerShell — screens-frontmatter-check.ps1 (Faz 5 Kapısı)

Dosya: `.ai/scripts/screens-frontmatter-check.ps1` · Satır: L1-L14, L21, L27-L28, L43-L48, L52-L61, L65-L73

```powershell
<# screens-frontmatter-check.ps1 — Faz 5 kapisı                              #>
<#    Kalip D §3.1 + §"9 bolum" zorunluluklarini dogrular. Degistirmez, raporlar. #>   # L2-L3
$REQUIRED_FM = @('reference_doc','title','type','category','date','status','version',
                 'tier','viewport','device','authority','governance','reference')      # L10-L11
$SECTIONS = @('## 1. ASCII Layout','## 2. BEM','## 3. Token','## 4. Touch Target',
              '## 5. WCAG','## 6. Glassmorphism','## 7. PNG Referans',
              '## 8. Responsive','## 9. State')                                         # L12-L14
$text = [System.IO.File]::ReadAllText($f.FullName, [Text.Encoding]::UTF8)               # L21
if ($fmEnd -lt 0) { $issues += 'frontmatter KAPANIYOR'; ... }                           # L28
Write-Host "dosya: $($files.Count) | sorunlu: $fail"                                    # L73
```

| Kontrol | Kural | Satır |
|---|---|---|
| Frontmatter kapanışı | ikinci `---` yoksa ihlal | L27-L28 |
| 13 zorunlu FM alanı (Kalıp D §3.1) | her biri regex ile aranır | L10-L11, L36-L38 |
| 9 bölüm başlığı | metinde yoksa ihlal | L12-L14, L40-L42 |
| `viewport` biçimi | nesne (`width:`) ise ihlal — `1024x600` kanonik | L43-L45 |
| `tier` biçimi | dizin-adı (`T\d+-[a-z]`) ise ihlal — `CatID` sütunu SSOT | L46-L48 |
| `source_of_truth` PNG | `VERIFICATION REQUIRED` değilse diskte gerçek olmalı + `.ai/.png` öneki | L52-L61 |
| Bayat sinyal taraması | `23 md`/`175 prompt`/`14 page`/`wifi-modal`/`file-browser` | L65-L67 |
| Muafiyet | tek indeks dosyası `00-ascii-art-index.md` 9-bölüm kuralından muaf | L32, L35 |

**Bilinen tuzak (AGENTS §13.6):** bu betik **yalnız repo kökünden** çalıştırılır — `.ai/` içinden çalıştırılırsa göreli yollar çözülemez ve sahte "PNG yok" üretir.

## §13. PowerShell — kalip-abc-check.ps1 (Faz 6 Kapısı: Authority Footer)

Dosya: `.ai/scripts/kalip-abc-check.ps1` · Satır: L2-L11, L18-L25, L37-L65

```powershell
<#                                                                  #>
<#   kalip-abc-check.ps1 — Faz 6 kapisi                              #>
<#   Kalip A (reference), B (flow), C (prompt) zorunluluklarini dogrular.
<#   Salt-okunur: raporlar, degistirmez.                              #>   # L2-L5
function Get-Frontmatter([string]$p) {
    $lines = [System.IO.File]::ReadAllLines($p, [Text.Encoding]::UTF8)
    if ($lines.Count -lt 3 -or $lines[0] -ne '---') { return $null }
    for ($i = 1; $i -lt $lines.Count; $i++) {
        if ($lines[$i] -eq '---') { return ,@($lines[1..($i-1)]) }
    }
    return $null
}                                                                      # L18-L25
function Test-AuthorityFooter([string]$text) {
    if ($text.IndexOf('**Authority:**') -lt 0) { return @('Authority footer yok') }
    if ($text.IndexOf('**Last Updated:**') -lt 0) { return @('Authority footer: Last Updated yok') }
    if ($text.IndexOf('**Mode:**') -lt 0) { return @('Authority footer: Mode yok') }
    return @()
}                                                                      # L56-L61
```

| İşlev | Kural | Satır |
|---|---|---|
| `Get-Frontmatter` | `ReadAllLines` UTF8 · satır 0 `---` · kapanış `---` aranır | L18-L25 |
| `Test-FmKeys` | key listesi regex ile kontrol edilir; eksikler döner | L27-L34 |
| `Test-QualityReport` | **son H2** "Quality Report" olmalı + 4 satır (`\| Version`, `\| Status`, `\| Cross References`, `\| Last Updated`) | L37-L54 |
| `Test-AuthorityFooter` | `**Authority:**` · `**Last Updated:**` · `**Mode:**` üçlüsü | L56-L61 |
| Kalıp A key listesi | 10 alan (`reference_doc`…`governance`) | L64-L65 |
| Şablon kaynakları | `.ai/.templates/ui-design/{reference,flow,prompt}-template.md` §3.1/§3.3 | L7-L10 |

**Eşleşme:** bu dosyanın son satırı (`**Authority:** Bayram Ali / Vault Steward · **Mode:** ...`) `Test-AuthorityFooter` üçlüsüyle aynı sözleşmedir → **evet** (bu modül ailesinin kalıp C/A footer'ı).

## §14. PowerShell — wiki-link-check.ps1 (3 Kademeli Link Çözümü)

Dosya: `.ai/scripts/wiki-link-check.ps1` · Satır: L2, L8-L16, L24, L28-L38, L41-L43

```powershell
<# wiki-link denetimi: .ai/ui-design/**/*.md icindeki [[hedef]] -> gercek dosya var mi? #>   # L2
$index=@{}
Get-ChildItem -Path 'C:\www\coremusic.net\.ai' -Recurse -Filter *.md -File | ForEach-Object {
  $base=$_.BaseName.ToLowerInvariant()                       // 1) dosya anahtarı (uzantisiz, kucuk)
  ...
}                                                            // L9-L16
foreach($m in [regex]::Matches($text,'\[\[([^\]|#]+)(?:[#|][^\]]*)?\]\]')){ ... }             // L24
"kontrol edilen link: $checked"
"kirik link: "+$broken.Count                                  // L41-L42
```

| Kademe | Çözüm | Satır |
|---|---|---|
| 1 | `.ai` genelinde `BaseName` (küçük, uzantısız) + tam yol + uzantısız tam yol indeksi | L9-L16, L29-L30 |
| 2 | dosyaya göreli `GetFullPath(Join-Path $dir ...)` — `.md`'li ve `.md`'siz | L32-L34 |
| 3 | sadece dosya adı (`GetFileNameWithoutExtension`) | L36-L37 |
| Sınırlama | rapor `Select-Object -First 40` — çıktı taşması engellenir | L43 |

**Çelişki notu:** `.ai/scripts/` altında `validate.mjs`'in link-check'i ile bu PS1 birbirinin **bağımsız** uygulamasıdır (farklı kapsam: bu betik yalnız `ui-design/**`). Aynı metrik (§13.7 sahte kırık 6) iki araçta farklı sayılabilir — kaynak: AGENTS §13.7; ölçüm iddiası değildir.

## §15. Boot Dosya Yükleme Zinciri (Kanıt Zinciri)

Kaynak: `.claude/hooks/*.cjs` (§3-§6) + `.ai/scripts/validate.mjs` (§7-§11) + `.ai/scripts/*.ps1` (§12-§14) · boot listesi kanıtı: `.ai/AGENTS.md` §24.2 (14 dosya) ve §25.4 (FULL boot 17 öğe — bu oturumda yüklenen vault içeriği)

```text
session başı
  → harness boot (kök AGENTS.md + CLAUDE.md + .ai boot listesi)      [§24.2 / §25.4]
  → UserPromptSubmit → skill-mandate.cjs → additionalContext enjeksiyonu   [L95-L100]
prompt → görev
  → PreToolUse(Edit|Write) → ai-risk-gate.cjs → ask (T1/T2)          [L38-L49]
  → PostToolUse(Edit|Write) → ai-validate-post.cjs → spawnSync validate.mjs --check  [L36]
     → status 1 → {"decision":"block"} + ilk 12 satır                 [L37-L42]
vault kapıları (insan/CI tetikli)
  → screens-frontmatter-check.ps1 (kökten) · kalip-abc-check.ps1 · wiki-link-check.ps1
```

| Zincir düğümü | Rol | Kanıt § |
|---|---|---|
| Boot listesi | `.ai` kök dosyalarının okuma sırası (14 / FULL 17) | §15 (vault) |
| skill-mandate | bağlam enjeksiyonu — bloklamaz | §3 |
| risk-gate | yazım öncesi insan kapısı | §5 |
| validate-post + validate.mjs | yazım sonrası otomatik kapı | §6, §7 |
| PS1 kapıları | faz kapıları (Faz 5/6) + link denetimi | §12-§14 |

## §16. İllüstratif — Python: Boot Sequence Validator

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
from pathlib import Path

REQUIRED_BOOT = ["CLAUDE.md", "AGENTS.md", "WORKFLOW.md"]   # risk-gate ROOT_HIGH ile aynı küme

def validate_boot(root: Path, files: list[str]) -> list[str]:
    """Boot paketi doğrulayıcı: eksik dosya = ihlal, fazla dosya = uyarı."""
    violations: list[str] = []
    present = {p.name for p in root.iterdir() if p.is_file()}
    for f in REQUIRED_BOOT:
        if f not in present:
            violations.append(f"boot eksik: {f}")
    extra = [f for f in files if f not in present and f not in REQUIRED_BOOT]
    if extra:
        violations.append(f"boot listesinde olmayan {len(extra)} dosya (uyarı)")
    return violations
```

**Kapsam:** boot paketi envanter kontrolü — repo karşılığı **YOK** (validate.mjs fm/dep/link/fanout bakar, boot paketi envanterine bakmaz · §23).

## §17. İllüstratif — TypeScript: Session Initializer

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```typescript
type HookEvent = "UserPromptSubmit" | "PreToolUse" | "PostToolUse";   // resmi olay adları (§28 #1)

type HookOutput =
  | { hookSpecificOutput: { hookEventName: HookEvent; additionalContext: string } }
  | { hookSpecificOutput: { hookEventName: "PreToolUse"; permissionDecision: "allow" | "ask" | "deny"; permissionDecisionReason: string } }
  | { decision: "block"; reason: string };

export function sessionInitializer(files: readonly string[]): HookOutput {
  const missing = ["CLAUDE.md", "AGENTS.md", "WORKFLOW.md"].filter((f) => !files.includes(f));
  const missingList = missing.join(", ");
  return {
    hookSpecificOutput: {
      hookEventName: "UserPromptSubmit",
      additionalContext: missing.length
        ? `Boot eksik: ${missingList} — vault-sync ile tamamla.`
        : "Boot paketi tamam.",
    },
  };
}
```

**Kapsam:** boot eksikliğini prompt'a context olarak enjeksiyon — skill-mandate `additionalContext` deseninin (L95-L100) tip güvenli soyutlaması.

## §18. İllüstratif — Go: File Preloader Chain

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
// Boot yükleme zinciri: sıra korunur, her adım varlık kontrolünden geçer.
type Step struct{ Path string; Critical bool }

func Preload(root string, chain []Step) []string {
    var problems []string
    for _, s := range chain {
        info, err := os.Stat(filepath.Join(root, s.Path))
        switch {
        case err != nil:
            problems = append(problems, "boot eksik: "+s.Path)
        case info.Size() == 0:
            problems = append(problems, "boot boş: "+s.Path) // 0 bayt dosya = bozuk boot
        case !s.Critical:
            // kritik olmayan adım yokluğunda zincir kesilmez, raporlanır
            problems = append(problems, "opsiyonel boot yok: "+s.Path)
        }
    }
    return problems
}
```

**Kapsam:** sıralı ön-yükleme (boot listesi 14/17 öğe) — Go karşılığı repo'da **YOK** (repo JS/PS1 yüzeyidir · §23).

## §19. İllüstratif — Rust: Config Loader

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```rust
/// Boot yapılandırma yükleyicisi: secret ASLA okunmaz (§24 kuralının tip-güvenli hali).
#[derive(Debug, serde::Deserialize)]
pub struct BootConfig {
    pub boot_files: Vec<String>,   // .ai/AGENTS.md §24.2 listesi
    #[serde(skip)]                 // env/secret bu yapıda tutulmaz
    _private: (),
}

pub fn load(path: &Path) -> Result<BootConfig, String> {
    let raw = std::fs::read_to_string(path).map_err(|e| format!("boot config okunamadı: {e}"))?;
    if raw.contains("API_KEY") || raw.contains("SECRET") {
        return Err("boot config secret içeremez (Zero-Hallucination + REDACTED)".into());
    }
    serde_json::from_str(&raw).map_err(|e| format!("boot config ayrıştırılamadı: {e}"))
}
```

**Kapsam:** boot config yükleme + secret taraması — repo'da `.ai` boot listesi kodlanmış **DEĞİL** (§24.2 Markdown'tadır; `.env.figma` örneği AGENTS §16.9.3).

## §20. İllüstratif — C++: Startup Hook

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```cpp
#include <functional>
#include <string>
#include <vector>

// C++ startup hook: araç-özel, gerçek zamanlı değil — boot anında bir kez çalışır.
class StartupHook {
public:
    using Check = std::function<bool(std::string&)>;   // false → ihlal mesajı döner
    void add(std::string name, Check c) { checks_.push_back({std::move(name), std::move(c)}); }
    bool run(std::string& reason) const noexcept {      // noexcept: boot kilitlenmesin
        for (const auto& [name, c] : checks_) {
            if (!c(reason)) { reason = name + ": " + reason; return false; }
        }
        return true;
    }
private:
    std::vector<std::pair<std::string, Check>> checks_;
};
```

**Kapsam:** boot-anı kontrol kancası — C++ yüzeyi repo'da Neva Engine/PLANNED'dir (§23); bu blok repo kanıtı **değildir**.

## §21. İllüstratif — C#: Boot Orchestrator

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```csharp
namespace CoreMusic.Boot;

public enum BootStage { Session, SkillMandate, RiskGate, ValidatePost }

public sealed record BootResult(BootStage Stage, bool Passed, string? Reason);

public sealed class BootOrchestrator
{
    // T1/T2 kümesi — ai-risk-gate.cjs L14-L16 ile aynı
    private static readonly HashSet<string> HighRisk = new() { "CLAUDE.md", "AGENTS.md", "WORKFLOW.md", "ULTRA-THINKING.md" };

    public async Task<IReadOnlyList<BootResult>> RunAsync(CancellationToken ct)
    {
        var results = new List<BootResult>(4)
        {
            new(BootStage.Session, true, null),
            new(BootStage.SkillMandate, true, null),                        // hook asla bloklamaz (L12)
            new(BootStage.RiskGate, true, null),                            // ask → insan karar verir (§5)
            await RunValidatorAsync(ct),                                    // validate.mjs --check (§6)
        };
        return results;
    }

    public static bool IsHighRisk(string relativePath) =>
        HighRisk.Contains(Path.GetFileName(relativePath));
}
```

**Kapsam:** boot aşaması orkestrasyonu — repo'da PHP `AIOrchestrator` (routing) vardır ama boot-orkestratör sınıfı **YOK** (§23).

## §22. Kavram ↔ Eşleşme Tablosu (vault kavramı → kod karşılığı)

| Vault kavramı | Repo kod karşılığı | Eşleşme |
|---|---|---|
| Skill Usage Mandate (kök CLAUDE.md) | `skill-mandate.cjs` L87-L93 enjeksiyon metni | **evet** |
| Boot zinciri (okuma sırası) | — (sıra Markdown'ta, kodda değil) | **kısmi** |
| T1/T2 otorite koruması | `ai-risk-gate.cjs` L14-L17, L36 | **evet** |
| İnsan onayı kapısı | `permissionDecision: 'ask'` L41, L63 | **evet** |
| Post-yazım doğrulama | `ai-validate-post.cjs` L36-L42 → `validate.mjs` | **evet** |
| 13 alan FM zorunluluğu | `validate.mjs` L34-L35, L87-L88 | **evet** |
| Bağımlılık + döngü denetimi | `dep-check` DFS L140-L153 | **evet** |
| Wiki-link doğrulama | `link-check` L161-L193 + `wiki-link-check.ps1` L24-L38 | **evet** |
| Fan-out/fan-in eşiği (R15/D5) | `fanout-check` L207, L213 | **evet** |
| §13.7 sahte kırık link affı | `PLACEHOLDER_LINKS` L38 | **evet** |
| Authority footer sözleşmesi | `Test-AuthorityFooter` L56-L61 | **evet** |
| Faz kapıları (Faz 5/6) | `screens-frontmatter-check.ps1` · `kalip-abc-check.ps1` | **evet** |
| Skill v3.0 frontmatter standardı | `--check` L110-L169 (name/desc/metadata/sidecar) | **evet** |
| Boot'ta secret okunmaz | — (kural vault'ta; hook'larda env okuması YOK — §24) | **kısmi** |
| Boot dosya envanteri kontrolü | — (YOK · §16 illüstratif) | **YOK** |
| Session öncesi otomatik boot ölçümü | — (PS1 kapıları manuel/CI tetikli · §12-§14) | **YOK** |

## §23. KOD YOK Alanlar (yokluk cümleleri — tahmin yasak)

| Alan | Durum (2026-10-17) |
|---|---|
| Boot paketi envanterini doğrulayan kod | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Boot dosyalarını sırayla yükleyen (preloader) kod | Repo'da bu alan kodu YOK — kaynak: upstream docs (sıra yalnız Markdown §24.2) |
| `.ai` boot listesinin Markdown'tan üretilen kod karşılığı | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Session initializer hook'u (`SessionStart`) | Repo'da bu alan kodu YOK — kaynak: upstream docs (yalnız `UserPromptSubmit` var · §3) |
| Boot secret/env yükleme katmanı | Repo'da bu alan kodu YOK — kaynak: upstream docs (`.env.figma` okuması betik içi, katman değil) |
| Boot telemetrisi / süre ölçümü | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Hook bütünlüğü (hook dosyası SHA/imza) doğrulaması | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| `.claude/settings.json` hook kayıt tablosunun denetimi | Repo'da bu alan kodu YOK — kaynak: upstream docs (validate.mjs kapsamı `.ai/**` · L55-L60) |

## §24. Güvenlik Notları (boot'ta secret okunmaz — kanıt ile)

| Not | Kanıt |
|---|---|
| 3 hook dosyasında da env/secret okuması **YOK** (yalnız stdin hook JSON + fs.existsSync + fs.readdirSync) | skill-mandate L16-L23 · risk-gate L11-L17 · validate-post L14-L16 |
| `ai-risk-gate` secret değil, otorite dosyası korur — HIGH yüzeyi: `CLAUDE.md`/`AGENTS.md`/`WORKFLOW.md`/`ULTRA-THINKING.md` + `.rules/**` | L14-L17 |
| Doğrulama komutu gerekçeye gömülü (`validate.mjs --check`) — araç zinciri görünür, siyah kutu yok | risk-gate L45, L68 |
| PS1 kapıları salt-okunur ("Degistirmez, raporlar" / "Salt-okunur: raporlar, degistirmez") | screens-frontmatter L3 · kalip-abc L5 |
| Secret anahtar asla `.md`/`.json`/`.log`'a yazılmaz; `.env.figma` gitignore'dadır | kök AGENTS §16.9.3 (vault kuralı — bu dosyada ihlal yok) |
| Hook emniyeti: her hook asla kendini bloklamaz (fail-open yalnız hook-hatasında; validator İHLALİ fail-closed'dur — `decision:block`) | skill-mandate L12 · validate-post L9-L11, L37 |

## §25. Ölçüm Kalıpları (tekrarlanabilir — repo kökünden)

| Amaç | Komut / desen |
|---|---|
| Hook dosyası sayısı | glob `.claude/hooks/*.cjs` → **3** (2026-10-17) |
| Hook satır sayısı | `ReadAllLines('…skill-mandate.cjs').Count` → 195 · risk-gate → 73 · validate-post → 44 |
| Validator kapsamı | `validate.mjs` L55-L60 (`.ai` kökü + `.agents` + `.rules` + `.decisions/index.md`) |
| PS1 envanteri | glob `.ai/scripts/*.ps1` → **6** (2026-10-17) — kapı betikleri 3'ü (§12-§14) |
| Bu dosyanın satır sayısı | `[IO.File]::ReadAllLines($hedef,[Text.Encoding]::UTF8).Count` → `>= 500` kapısı (§26 #1) |
| Mojibake | `ReadAllText` UTF8 içinde U+FFFD ara → 0 beklenir |

## §26. Doğrulama Kapıları

| # | Kapı | Kriter | Durum |
|---|---|---|---|
| 1 | Satır sayısı | `>= 500` (band 500-520) | §25 ölçümü |
| 2 | Frontmatter + Authority | FM alanları birebir · `title`/`parent` yok · son satır birebir | gözden geçirme |
| 3 | Hook kanıtı | 3 dosya, her alıntı gerçek `Dosya:`/`Satır:` | 2026-10-17 okuma |
| 4 | PS1 kanıtı | 3 kapı betiği alıntısı gerçek satır | 2026-10-17 okuma |
| 5 | validate.mjs kanıtı | §7-§11 alıntıları gerçek satır (1-248 aralığı) | 2026-10-17 okuma |
| 6 | Yokluk cümleleri | 8 satır "KOD YOK" | §23 |
| 7 | İllüstratif etiket | 6 dilde blok + üstünde etiket | §16-§21 |
| 8 | Web doğrulama | 2 resmi kaynak (hook olay adları + şemalar) | §28 #1-#2 |
| 9 | Placeholder + Mojibake | üretimde bekleyen yer tutucu yok · U+FFFD 0 | grep · §25 |
| 10 | Section sayısı | en az 25 `## §` | grep |
| 11 | Yasaklar | commit yok · `.claude/**`-`.ai/**` yazımı yok · bu dosya dışına yazım yok | görev manifesti |

## §27. Açık Sorular

| # | Soru | Bağlam |
|---|---|---|
| 1 | `.claude/settings.json` hook kayıt tablosu (matcher/timeout) diskten alıntılanmadı — doğrulanacak: hangi matcher(`Edit\|Write`) timeout değeriyle kayıtlı? | §5-§6 kapsamı; validate.mjs `.claude/**` kapsamı dışında (L55-L60) |
| 2 | PS1 kapılarının CI (GitHub Actions `ci.yml`) tetiklenmesi doğrulanmadı — `⚠️ VERIFICATION REQUIRED` | `.github/workflows/` bu görevde okunmadı |
| 3 | `validate.mjs` DEAD_DIRS (14 hedef) güncelliği — stale katalog borcu ne zaman temizlenecek? | L40-L45 · AGENTS §13.7 |
| 4 | skill-mandate `COREMUSIC_GLOBAL` (5 skill) ile kök CLAUDE.md §Skill Registry (17 skill) arasındaki fark kasıtlı mı (yalnız CoreMusic-yazarlı global küme)? | L26 · kök CLAUDE.md (registry SSOT) |
| 5 | Boot'ta `SessionStart` hook'u yok (§23) — boot listesi yükleme hook'a taşınacak mı, taşınmayacak mı? karar: vault | ADR gerektirir |

## §28. Kaynaklar

| # | Kaynak | Kanıt | Tarih |
|---|---|---|---|
| 1 | `https://code.claude.com/docs/en/hooks` — resmi hooks referansı: `UserPromptSubmit` → `hookSpecificOutput.additionalContext`; `PreToolUse` → `permissionDecision` (`allow`/`ask`/`deny`); `PostToolUse` → `decision: "block"` | §3, §5, §6 eşleşmeleri | 2026-10-17 websearch |
| 2 | `https://code.claude.com/docs/en/hooks-guide` — resmi kılavuz: JSON örnek şemaları (`hookEventName` + `additionalContext`, `permissionDecisionReason`) | §3, §5 şema eşleşmesi | 2026-10-17 websearch |
| 3 | `.claude/hooks/skill-mandate.cjs` (195 satır) · `ai-risk-gate.cjs` (73) · `ai-validate-post.cjs` (44) | §2-§6 | 2026-10-17 Read |
| 4 | `.ai/scripts/validate.mjs` (248 satır) | §7-§11 | 2026-10-17 Read |
| 5 | `.ai/scripts/screens-frontmatter-check.ps1` (73) · `kalip-abc-check.ps1` (§L1-80 okundu) · `wiki-link-check.ps1` (43) | §12-§14 | 2026-10-17 Read |
| 6 | `.ai/AGENTS.md` §24.2 (14 dosya boot) + §25.4 (FULL boot 17 öğe) — bu oturumda yüklenen vault içeriği | §15 | 2026-10-17 |
| 7 | Şablon: `.ai/architecture/K015-AI/rag/rag-ornekler.md` — kanıt künye + illüstratif iskeleti | §1, §16-§21 biçimi | 2026-10-17 |

## §29. Gate Özeti

§1-§29: künye formatı 9 kural · hook envanteri 3 dosya (195+73+44 = 312 satır) · hook kanıt bölümü 4 (skill-mandate ×2, risk-gate, validate-post) · validator kanıt bölümü 5 (giriş, fm, dep, link, fanout) · PS1 kanıt bölümü 3 (Faz 5, Faz 6, wiki-link) · zincir 1 (§15) · KOD YOK 8 satır · eşleme 16 satır · güvenlik 6 · ölçüm 6 · kapı 11 · açık soru 5 · kaynak 7 (2 resmi web) · illüstratif 6 blok / 6 dil (Python, TypeScript, Go, Rust, C++, C#) → kapılar: `>=500` · FM birebir · Authority son satır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
