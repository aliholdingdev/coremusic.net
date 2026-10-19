---
node_id: boot-protocol-anayasa
node_type: module
status: active
parent_id: boot-protocol
children: []
depends_on: [boot-protocol-index]
evidence:
  - ".ai/MEMORY.md §5 (20 adım boot listesi, okundu 2026-10-17)"
  - ".claude/hooks/skill-mandate.cjs (boot tetikleyicisi kod kanıtı)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı"
open_questions: []
group: mimari
---

# boot-protocol-anayasa - Oturum Başlangıç Kuralları

> **Bu dosya ne değildir:** boot paketinin **dosyalarının kopyasını** (14/20 dosyanın içeriği) ve
> hook betiklerinin **kaynak kodunu** taşımaz; burada **boot anayasası** vardır — oturum başında ne
> okunur, hangi sırada, hangi bütçe ile, hook hangi kapıyı açar, boot nerede biter görev başlar.

## §1 Rol (ELI15)

Boot protokolü, "oturum açıldı → ilk görev yazıldı" arasındaki **tek yönlü ve sınırlı** geçiştir.
Amacı, ajanın bağlamı **en küçük yeterli paketle** kurmasıdır: anayasa + agent defteri + süreç +
son oturum izi. Boot bir **okuma maratonu değildir** — boot paketi dışında her dosya
**on-demand** (gerektiğinde) okunur. Boot'un bütçesi (süre + token) sınırlıdır; bütçe aşılırsa
görev bağlamı bozulur ve halüsinasyon riski artar.

| Soru | Cevap | Kanıt |
|------|-------|-------|
| Boot kimin için? | Tüm AI ajanları (MO + 11 uzman + subagent'lar) | `.ai/MEMORY.md:36` |
| Ne verir? | Kanonik kurallar + agent sınırları + son oturum izi | `.ai/AGENTS.md` §24.2 |
| Ne vermez? | Görev içeriği (ADR, ui-design, sql, raporlar) — on-demand | `.ai/RAG.md:47` |
| Sınır ne? | Süre (25s kural / 36s ölçüm) + token bütçesi | `.ai/MEMORY.md:71` · `:336` |
| Boot biterse ne olur? | Görev → discovery → kodlama başlar (§15) | kök `AGENTS.md` §7 |

> **eli10:** Boot, işe başlamadan önce okunan "kılavuz kitap"tır; kitabın tamamı değil, kapağı ve
> içindekiler okunur. > **eli15:** Boot ayrı yazılır çünkü kural metni (anayasa) ile görev metni
> (ADR/spec) farklı hızda değişir; ikisi karışınca boot şişer ve her oturum aynı token bedelini öder.

## §2 Kapsam (in / out)

| Kapsam | Kapsam Dışı | Kanıt |
|--------|-------------|-------|
| Boot paketi içeriği (hangi dosya, hangi sıra) | Dosyaların **içeriği** (kural metinleri kendi SSOT'unda) | `.ai/AGENTS.md` §24.2 |
| Boot sırası ve öncelik (P0→P1→P2) | Görev-başı okuma (domain reading) | `.ai/MEMORY.md:336` |
| Skill Usage Mandate (hook tetikleyicisi) | Skill içeriklerinin kural metinleri | `.claude/hooks/skill-mandate.cjs` |
| On-demand referans kuralı (toplu okuma yasağı) | On-demand dosyaların listesi (RAG §3) | `.ai/RAG.md:47` |
| Boot → görev geçişi + boot hata modları | Süreç fazları (WORKFLOW §8) | kök `AGENTS.md` §7 |
| Boot bütçesi (süre/token) ve doğrulama kapıları | Runtime oturum kilidi (Context Lock §12) | `.ai/MEMORY.md:71` |

**Ön koşul:** Boot dosyaları diskte mevcut ve frontmatter'lıdır; sayı iddiaları ölçümle yazılır.
**Kullananlar:** tüm ajanlar (oturum başında 1 kez), MO, Vault Steward, hook betikleri.

## §3 Terminoloji

| Terim | Tanım | Kanıt |
|-------|-------|-------|
| Boot paketi | Oturum başında **her koşulda** okunan dosya kümesi | `.ai/AGENTS.md` §24.2 |
| Kanonik boot | 14 `.ai` kök dosya (§24.2) — kök CLAUDE.md §16 ile çelişmez | `.ai/AGENTS.md:605` |
| Genişletilmiş boot | 20 adım (MEMORY §5) — `.claude/rules/*`, prompt arşivleri, mockup indeksi dahil | `.ai/MEMORY.md:311` |
| FULL boot | 17 öğe: root CLAUDE + root WORKFLOW + root README + 14 `.ai` kök + 3 dizin | `.ai/AGENTS.md:605` |
| On-demand | Görev eşleşmesinde 1 kez okunan dosya (boot'a girmez) | `.ai/RAG.md:63` |
| Skill mandate | Görev↔skill eşleşmesinde **ilk işlem** skill yüklemek | kök `AGENTS.md` §7 adım 0 |
| Hook | Araç yaşam döngüsü olayında çalışan betik (UserPromptSubmit vb.) | `.claude/settings.json:3` |
| Boot bütçesi | Süre (sn) + token üst sınırı | `.ai/MEMORY.md:71` |

> ⚠️ **Terim çatışması:** "boot" kelimesi hem **ajan oturum açılışını** hem PHP'nin
> `RuntimeBootstrap::boot()` **uygulama çalışma-zamanı açılışını** anlatır (§24); bu anayasada
> "boot" = **ajan boot protokolüdür**.

## §4 Boot Paketi — 4 Satırlık Boot Zinciri (kök AGENTS.md §14)

Kök `AGENTS.md` §14, boot'u **4 satırlık** bir tabloya indirger: her satır bir okuma katmanıdır.
Bu tablo "boot zinciri"dir; uzantıları §5 (14 dosya) ve §6 (20 adım) ile açılır.

| # | Dosya | Ne verir | Ne zaman |
|---|-------|----------|----------|
| 1 | `AGENTS.md` (kök) | Ana kurallar: akış, zero-hallucination, execution loop, persona hiyerarşisi | Her zaman (harness boot'u) |
| 2 | `CLAUDE.md` (kök) | Skill Registry (SSOT) · vault bağlantı tablosu · mimari referans | Her zaman — linklenir, atlanmaz |
| 3 | `@.ai/AGENTS.md` | Tam agent registry (11 agent routing — profil indeksi `.ai/.agents/*.md`) | Her görevde |
| 4 | `@.ai/` diğer dosyalar (CLAUDE/WORKFLOW/RAG/...) | Anayasa, süreç, retrieval | **Yalnız ihtiyaç anında** (`@` ile) |

**Çift yönlü bağlantı:** `AGENTS.md` → `CLAUDE.md` (Skill Registry + vault) · `CLAUDE.md` →
`AGENTS.md` (ana kurallar SSOT) — ikisi tek boot paketidir, biri diğerinin yerine geçmez
(`Dosya: AGENTS.md (kök) · §14`). 4. satır bu anayasanın çekirdeği: **boot paketi dışındaki her
`.ai/` dosyası on-demand'tir** — "boot'ta tüm vault'u oku" talimatı geçersizdir.

## §5 Genişletilmiş Boot — §24.2 14 Dosya Tablosu (her görev başında)

`.ai/AGENTS.md` §24.2, Ultra Thinking Protocol'ün 1. adımı olarak **14 dosyayı** "her görev
başında oku" listesi olarak zorunlu kılar (bu, kök §14'ün açılımıdır — §7 fark tablosu).

| Sıra | Dosya | Amaç | Sıra | Dosya | Amaç |
|------|-------|------|------|-------|------|
| 1 | `.ai/CLAUDE.md` | AI anayasası, 16 Hard Guardrails | 8 | `.ai/MEMORY.md` | Session hafızası |
| 2 | `.ai/AGENTS.md` | Agent sınırları, routing | 9 | `.ai/log.md` | Audit trail |
| 3 | `.ai/WORKFLOW.md` | Süreçler, fazlar | 10 | `.ai/ULTRA-THINKING.md` | Ultra düşünme protokolü |
| 4 | `.ai/brain.md` | Mimari kararlar | 11 | `.ai/engine.md` | Orkestrasyon motoru |
| 5 | `.ai/ROLE.md` | Rol tanımı | 12 | `.ai/glossary.md` | Terim sözlüğü — kod-referanslı |
| 6 | `.ai/index.md` | Master katalog | 13 | `.ai/VISION.md` | Vizyon ve yol haritası |
| 7 | `.ai/keys.md` | Keyword haritası | 14 | `.ai/PROJECTS.md` | Proje envanteri |

Kanıt: `.ai/AGENTS.md` §24.2 tablosu (14 satır). Ultra Thinking 5 adımı: Vault Oku (max 25s) →
Bağlamı Anla → Hata Kontrolü → Sonuç Tahmini → Doğrulama (`Dosya: .ai/AGENTS.md · §24.1`).
**§24.2 liste ile kök §14.4 çelişirse** kök `AGENTS.md` §14 (boot zinciri) değil, bu 14 satır
"görev-başı okuma"nın tamamıdır — ikisi aynı metnin iki açılımıdır (§7).

## §6 Genişletilmiş Boot — MEMORY §5 20 Adım Listesi

`.ai/MEMORY.md` §5, 14 kök dosyaya **6 genişletme adımı** ekler (`.claude/rules/*`, 4 prompt
arşivi, mockup indeksi) ve her satıra **öncelik + timeout** atar. Öncelik: P0 → P1 → P2.

| # | Dosya | Öncelik | # | Dosya | Öncelik |
|---|-------|---------|---|-------|---------|
| 1 | `.ai/CLAUDE.md` (3s) | P0 | 11 | `.ai/engine.md` (3s) | P1 |
| 2 | `.ai/AGENTS.md` (3s) | P0 | 12 | `.ai/ULTRA-THINKING.md` (3s) | P1 |
| 3 | `.ai/WORKFLOW.md` (3s) | P0 | 13 | `.ai/VISION.md` (3s) | P2 |
| 4 | `.ai/index.md` (4s) | P1 | 14 | `.ai/PROJECTS.md` (3s) | P2 |
| 5 | `.ai/keys.md` (3s) | P1 | 15 | `.claude/rules/*` (5s) | P2 |
| 6 | `.ai/glossary.md` (3s) | P1 | 16 | `archives/prompt0-genel-ana-prompt` (5s) | P2 |
| 7 | `.ai/brain.md` (4s) | P1 | 17 | `archives/prompt1-spa-router` (3s) | P2 |
| 8 | `.ai/MEMORY.md` (3s) | P1 | 18 | `archives/prompt2-auth` (3s) | P2 |
| 9 | `.ai/log.md` son 20 satır (2s) | P1 | 19 | `archives/prompt3-api` (3s) | P2 |
| 10 | `.ai/ROLE.md` (3s) | P1 | 20 | `ui-design/00-mockup-index.md` (3s) | P2 |

Kanıt: `.ai/MEMORY.md:313-334` (tablo), `:336` ("Toplam boot suresi: Max 36 saniye. P0 -> P1 -> P2
sirasyla okunur. Paralel okuma desteklenmez (sirali bagimlilik)"). #20 frontend'e özeldir:
"CSS/HTML/JS/layout/bileşen görevlerinde `00-mockup-index.md` okunmadan kod yazılamaz" (`:338`).

## §7 Fark Tablosu — 4 / 14 / 17 / 20 (boot listeleri nasıl uzlaşır?)

Dört farklı boot sayısı vault'ta birlikte yaşar; bunlar **çelişki değil katmandır** ve uzlaşma
kuralı `.ai/AGENTS.md` §25.4'te yazılıdır ("Boot List Compatibility").

| Liste | Kaynak | Sayı | Kapsam | Kim kullanır |
|-------|--------|------|--------|--------------|
| Boot zinciri | kök `AGENTS.md` §14 | 4 satır | Katman özeti (boot ≠ on-demand) | Her session (harness boot'u) |
| Kanonik kök boot | `.ai/AGENTS.md` §24.2 · WORKFLOW §8.7A | 14 dosya | `.ai/` kök okuma kümesi | Her görev başında |
| FULL boot | `.ai/AGENTS.md` §25.4 | 17 öğe | root CLAUDE+WORKFLOW+README + 14 `.ai` + 3 dizin | Tam envanter tanımı |
| Genişletilmiş | `.ai/MEMORY.md` §5 | 20 adım | 14 + rules + 4 prompt + mockup | Süre/timeout hesabı |

**Uzlaşma kuralı:** kanonik kök boot listesi `.ai/AGENTS.md` §24.2 ve WORKFLOW §8.7A'dır;
§5 20-adım listesi **genişletilmiş okuma setidir** (`Dosya: .ai/AGENTS.md · Satır: 605`).
Fark tablosu bilinen durumdur ve "adım sayısı farkı" **kusur sayılmaz** — sayım zinciri
(14 = 17−3 kök-dizin) korunur. Boot listesi bir dosya eklenince **yeniden ölçülür** (§20/8).

## §8 Boot Sırası Kuralları

Boot **sıralıdır**; paralel okuma, sıralı bağımlılık nedeniyle yasaktır. Sıra, öncelik
sırasıdır: P0 (anayasa/defter/süreç) → P1 (indeks/keyword/hafız) → P2 (vizyon/genişletme).

| # | Kural | Gerekçe | İhlal Sonucu | Kanıt |
|---|-------|---------|--------------|-------|
| 1 | P0 → P1 → P2 sırasıyla okunur | Öncelik bağımlılığı (anayasa önce) | Yanlış bağlam sırası | `.ai/MEMORY.md:336` |
| 2 | Paralel okuma desteklenmez | Sıralı bağımlılık (sıra bozulursa anlam kayar) | Boot geçersiz | `.ai/MEMORY.md:336` |
| 3 | `CLAUDE.md` → `AGENTS.md` çift yönlü link | Boot paketi tek bütündür | Eksik boot | kök `AGENTS.md` §14 |
| 4 | Fallback: `index.md` | Dosya bulunamazsa katalogdan yönel | Boot çökmesi | `.ai/AGENTS.md` §13.1 |
| 5 | Gereksiz dosya okunmaz (token aşımı) | Bütçe disiplini | Görev başarısız | `.ai/AGENTS.md` §13.3 |
| 6 | `.ai/` gizli dizin: glob/grep'te `hidden: true` | Yoksa `.ai/**` "0 sonuç" verir (sahte eksiklik) | Sahte boot hatası | kök `AGENTS.md` §7.2 |
| 7 | Gate script'ler yalnız repo **kökünden** | Göreli yollar `.ai/` içinden çözülemez → 19 sahte "PNG yok" | Sahte ihlal | `.ai/AGENTS.md` §13.6 |

İlgili boot zaman çizelgesi (MEMORY §4): boot 25s · sync start 10s · sync end 15s · session
close 5s = 55s sabit; kalan süre görev yürütmeye (`Dosya: .ai/MEMORY.md · Satır: 307`).

## §9 Boot Bütçesi (süre + token)

Bütçe iki rakamda ifade edilir ve **ikisi de vault'ta yazılıdır** — bu bir bilinçli çelişkidir,
yeni değer uydurulmaz: kural `Max 25 saniye` (§16 Limitations), ölçüm `Max 36 saniye` (§5 tablo
toplamı). Çözüm: 25s **hedef**, 36s **ölçülmüş tavan**; 36s aşılırsa boot kırpılır.

| Bütçe | Değer | Ne korur | Kanıt |
|-------|-------|----------|-------|
| Hedef süre (kural) | Max 25 sn | Görev için ayrılan süre | `.ai/MEMORY.md:71` |
| Ölçülmüş tavan | Max 36 sn (20 adım toplamı) | P2 genişletme dahil | `.ai/MEMORY.md:336` |
| Vault boyutu | Max 100MB / dosya 1000 satır | Boot okuma hacmi | `.ai/MEMORY.md:68-70` |
| Memory toplam | Max 10MB | Session hafızası | `.ai/MEMORY.md:72` |
| Session süresi | 5-15 dk (boot+sync 55s sabit) | Boot payı (~%6-18) | `.ai/MEMORY.md:307` |
| Token bütçesi | `⚠️ VERIFICATION REQUIRED` — sayısal token eşiği vault'ta yok | — | (ölçüm yok) |

Token tarafında upstream kural: CLAUDE.md 200 satırın üstünde bağlam ve uyum kaybı yaratır
(`https://code.claude.com/docs/en/memory`); auto memory index'i ilk 200 satır / 25KB yüklenir.
Bu anayasanın boot'a eklenmemesinin (§13) nedeni de bu bütçe disiplinidir.

## §10 Skill Usage Mandate — Hook Tetikleyicisi (kod kanıtlı)

Skill mandate, boot'tan **sonra** devreye giren ikinci kapıdır: kullanıcı prompt'u submit
edildiğinde hook çalışır ve "eşleşen skill'i YÜKLE" talimatını context'e enjekte eder. Bu
**vault kuralı değil, çalışır koddur** — tetikleyici `.claude/settings.json → UserPromptSubmit`.

```javascript
// Dosya: .claude/hooks/skill-mandate.cjs · Satır: 86-93
const additionalContext = [
  'SKILL ZORUNLULUĞU (kök CLAUDE.md §Skill Usage Mandate): Görev bir skill ile eşleşiyorsa',
  'İLK işlem Skill tool ile yüklemektir; eşleşen skill\'siz işlem başlatmak YASAKTIR.',
  `Proje skill'leri (.claude/skills): ${projectList}.`,
  globalList,
  thirdParty,
  "İstisna: kullanıcı açıkça 'skill kullanma' derse veya görev skill'lerle ilgisizse.",
].filter(Boolean).join(' ');
```

Proje skill'leri **diskten taranır**, elle yazılmaz (`listProjectSkills`, Satır: 56-63);
CoreMusic global kümesi sabittir: `human-mode, truth-engine, verify-loop, agent-debate,
context-report` (Satır: 26). Çıktı biçimi `hookSpecificOutput.additionalContext`'tur
(Satır: 95-100) — upstream hook sözleşmesiyle birebir (§22/2).

## §11 Hook Kayıt Yüzeyi — .claude/settings.json (3 hook)

Hook'lar **ayar dosyasına kayıtlıdır**, koda gömülü değildir; kayıt yüzeyi yetki yüzeyidir ve
onaysız değiştirilemez (`.claude/AGENTS.md` §3.3 #2). Boot protokolüyle doğrudan ilgili olan
`UserPromptSubmit` hook'u; ikisi görev içi kapılardır.

```json
// Dosya: .claude/settings.json · Satır: 3-11
"UserPromptSubmit": [
  {
    "hooks": [
      {
        "type": "command",
        "command": "node \"${CLAUDE_PROJECT_DIR}/.claude/hooks/skill-mandate.cjs\""
      }
    ]
  }
]
```

| Hook | Olay | Betik | Timeout | Rol |
|------|------|-------|---------|-----|
| Skill mandate | `UserPromptSubmit` | `skill-mandate.cjs` | upstream 30s | Prompt başında skill listesi enjeksiyonu (Satır: 8) |
| Risk gate | `PreToolUse` (Edit\|Write) | `ai-risk-gate.cjs` | 15s | Control Plane HIGH-RISK kapısı T1/T2 (Satır: 19-21) |
| Post validate | `PostToolUse` (Edit\|Write) | `ai-validate-post.cjs` | 60s | fm/link/ssot/dep/tier/orphan denetimi (Satır: 32-34) |

**Emniyet kuralı:** hook betiği "her koşulda exit 0 (prompt asla bloklanmaz); --check hariç"
(`Dosya: .claude/hooks/skill-mandate.cjs · Satır: 12`) — boot'a takılan bir hook oturumu
kilitlemez; upstream'te `UserPromptSubmit` timeout'u 30s'tir ve aşım prompt'suz devam demektir
(`https://code.claude.com/docs/en/hooks`).

## §12 Hook Doğrulama Modu — `--check` (v3.0 gate)

Aynı betik ikinci bir işlev daha taşır: `--check` modu, `.claude/skills/*/SKILL.md` dosyalarının
v3.0 formatını **CI kapısı gibi** denetler (exit 0 = temiz, 1 = ihlal). Boot anayasası için
önemi: mandate'in enjekte ettiği skill listesinin **kalitesini** bu kapı güvenceye alır.

```javascript
// Dosya: .claude/hooks/skill-mandate.cjs · Satır: 121-128
const declared = fmValue(fm, 'name');
if (declared !== name) violations.push(`${name}: name "${declared}" klasörle 1:1 değil`);
const desc = fmValue(fm, 'description');
if (!desc) violations.push(`${name}: description YOK`);
else {
  if (desc.length > 1024) violations.push(`${name}: description ${desc.length} char (>1024)`);
  if (!/^Use when /i.test(desc)) violations.push(`${name}: description "Use when" ile başlamıyor`);
}
```

| Kontrol | Kural | İhlal Sonucu |
|---------|-------|--------------|
| `name` ↔ klasör | 1:1 eşleşme | `name "..." klasörle 1:1 değil` |
| `description` | var, ≤1024 char, `"Use when"` ile başlar | ihlal satırı |
| Yasak root key | title/type/version/authority/... (Satır: 28-31) | yasak root key |
| `metadata` bloğu | zorunlu (Satır: 134) | metadata bloğu YOK |
| Kırık link / orphan / sidecar CLAUDE.md | Satır: 136-163 | ihlal listesi, exit 1 |

Çıktı biçimi: `skill-mandate --check: OK (N proje skill'i temiz)` (Satır: 176) veya ihlal listesi
(Satır: 172-174). Bu gate **boot sırasında çalışmaz**; görev öncesi/atölye zamanında çalıştırılır.

## §13 On-Demand Referans Kuralı — Boot'ta Toplu Okuma Yasağı

Bu anayasanın bağlayıcı çekirdeği: **boot paketi dışında kalan hiçbir `.ai/` dosyası otomatik
okunmaz.** "Başta tüm vault'u oku", "önce tüm .ai/ oku" talimatları geçersizdir; okuma yalnız
görevin gerektirdiği anda `@` ile yapılır.

| # | Kural | Neden | Kanıt |
|---|-------|-------|-------|
| 1 | Boot paketi = kök §14 (4 katman) + §24.2 (14 dosya) | Bütçe (süre/token) | `.ai/AGENTS.md` §24.2 |
| 2 | "Boot'ta tüm vault'u oku" YASAKTIR | Vault 100MB / boot 36s — uyumsuz | `.ai/MEMORY.md:68,336` |
| 3 | Okuma anı = görev eşleşmesinde 1 kez | Token israfı önlenir | `.ai/RAG.md:63` |
| 4 | Çelişkide `.ai/` (SSOT) kazanır; vault-içi otorite `.ai/CLAUDE.md` §2.1 | Katman hiyerarşisi | kök `AGENTS.md` §9 |
| 5 | `.ai/wiki/` diskte YOK (0 sayfa) — "62 sayfa oku" yönergesi geçersizdir | Hayalet kaynak | kök `AGENTS.md` §7.2 |
| 6 | `.ai/.rules/error-recovery.md` diskte YOK — düzeltme `.ai/CLAUDE.md` §6'dan | Hayalet kaynak | kök `AGENTS.md` §9 |
| 7 | `.ai/raw/`, `.ai.OLD/` diskte YOK | Hayalet kaynak | kök `AGENTS.md` §9 |

İstisna listesi (vault §9 tablosu): anayasa → `.ai/CLAUDE.md` · ADR → `.decisions/index.md` ·
routing → `.ai/AGENTS.md` · süreç → `WORKFLOW.md` · retrieval → `RAG.md` §3 / `keys.md`.
Bu istisnalar **görev tetikleyicisidir**, boot öğesi değildir (rag-anayasa §13 ile aynı kural).

## §14 On-Demand Yönlendirme — "Hangi Soru → Hangi Dosya"

Boot'tan sonra bağlam genişlemesi **rastgele değildir**: `.ai/RAG.md` §3.1 konu→dosya tablosu
üzerinden yapılır. Yönlendirme bilgisi kural metninden **daha sık değişir** → boot'a değil,
on-demand okumaya uyar.

| Adım | İşlem | Çıktı | Kanıt |
|------|-------|-------|-------|
| 1 | Sorunun keyword'leri çıkarılır | anahtar kelime kümesi | `.ai/RAG.md:98-105` |
| 2 | §3.1 `Keyword'ler` sütununda eşleşme | eşleşen satır | `.ai/RAG.md:71-94` |
| 3 | `Hedef Dosya(lar)` ilk okuma hedefi | 1-2 dosya | `.ai/RAG.md:71-94` |
| 4 | `Durum` sütunu okunur | IMPLEMENTED / PLANNED / UNKNOWN | `.ai/RAG.md:104` |
| 5 | PLANNED ise kod **okunmaz**, ADR/plan okunur | dürüst etiket | `.ai/RAG.md:104` |
| 6 | Domain reading (§24.3) ile alt dosyalar | görev dosyaları | `.ai/AGENTS.md` §24.3 |

Örnek satır: "Boot sırası, kanonik okuma listesi" → `.ai/CLAUDE.md` §16 · `.ai/WORKFLOW.md`
(`Dosya: .ai/RAG.md · Satır: 75`). Bu anayasa (`boot-protocol-anayasa`) da §3.1'de
`boot, okuma, kanonik` keyword'üyle çağrılır; boot listesine (§16 13 kanonik) **eklenmez**.

## §15 Boot → Görev Geçişi (execution loop)

Boot biter → görev başlar. Geçiş, kök `AGENTS.md` §7 execution loop'unun 0-5. adımlarıdır;
boot'un devamı bu adımların **önsözüdür**, kendisi execution değildir.

| # | Adım | Boot ile ilişkisi | Çıktı |
|---|------|-------------------|-------|
| 0 | Skill eşleşmesi (mandate) | §10-§12 hook'u bu adımı tetikler | yüklenen skill |
| 1 | Kök `AGENTS.md` okunur | Boot 1. satır | ana kurallar |
| 2 | Yalnız ilgili `.ai/` vault dosyaları | §13-§14 kuralı | konu dosyaları |
| 3 | İlgili `.ai/.rules/` (yalnız `senior-mode.md`) | `.rules/` minimal | ek kural |
| 4 | Hedef dosya okunur | görev dosyası | hedef metin |
| 5 | Kod yazılır | boot bitti → implementasyon | diff |
| 6 | `.ai/CLAUDE.md` §6 LINT | doğrulama | PASS/DUR |
| 8 | UI değişimi → browser testi | mockup gate (#20) | kanıt |
| 9 | Commit — **subagent ATMAZ** | orkestratöre ait | commit |

Kritik geçiş kuralı: **Faz 1'de (keşif) onaysız yoktur** — kod değişimi, yeni dosya/dizin, yeni
bağımlılık, büyük yapısal değişiklik (`Dosya: AGENTS.md (kök) · §4`). Boot tamamlandı ≠ onay
alındı; onay ayrı bir kapıdır (§1/§2 "ön koşul").

## §16 Görev-Başı Okuma — Domain-Based Reading (§24.3)

Boot paketi **ortaktır**; görev okuması **domain'e özeldir**. §24.3 tablosu, her domain agent'ının
boot sonrası ek okumasını tanımlar. Bu anayasa boot'un **altında** durur: boot 14 dosya
ortaktır, domain okuması değişkendir.

| Agent | Zorunlu Okuma (boot sonrası) | Boot'a girer mi? |
|-------|------------------------------|------------------|
| Backend | `.ai/.decisions/index.md` (ADR-083), `shared/src/PageRouter/` | Hayır — on-demand |
| Frontend | ui-design indeks + Kalıp A-D (Guardrail #16) + css-template | Hayır — on-demand |
| Security | `.ai/.decisions/index.md` (ADR-010), `shared/src/Middleware/` | Hayır — on-demand |
| Data | `.ai/.sql/mysql/*.sql`, `shared/src/Database/` | Hayır — on-demand |
| Embedded | `projects/NevaEngine/*.md`, `electronic/dsp/*.md`, `firmware/*.md` | Hayır — on-demand |
| QA | ui-design gaps + screens + `.ai/reports/` | Hayır — on-demand |
| DevOps | `.ai/ecosystem/*.md` | Hayır — on-demand |

Kanıt: `.ai/AGENTS.md` §24.3 (satır içi ölçüm notları ile). Ayrım kuralı: boot = **her oturum
aynı**; domain reading = **her domain farklı**; mockup gate = frontend'e ek **hard gate**
(dosya okunmadan kod yazılamaz — `.ai/MEMORY.md:338`).

## §17 Boot Hata Modları — Eksik Dosya / Kırık Link / Sahte İhlal

Boot'un üç bilinen hata sınıfı vardır; ikisi **sahte**dır (araç kaynaklı), birisi gerçektir.
Doğru davranış: sahte ihlali "onarmak" değil, **kökten yeniden ölçmektir**.

| # | Hata modu | Belirti | Doğru davranış | Kanıt |
|---|-----------|---------|----------------|-------|
| 1 | Eksik boot dosyası | Dosya diskte yok | `VERIFICATION REQUIRED` — içerik uydurulmaz | kök `AGENTS.md` §3 |
| 2 | `.ai/` gizli arama | `hidden:false` → sahte "0 sonuç" | `hidden: true` ile tekrar tara | kök `AGENTS.md` §7.2 |
| 3 | Gate script `.ai/` içinden | 19 sahte "PNG yok" | Repo **kökünden** çalıştır | `.ai/AGENTS.md` §13.6 |
| 4 | Kırık wiki-link | 227 link / 6 raporlanan | 6'sı sahte (şablon değişkeni) — düzeltme YASAK | `.ai/AGENTS.md` §13.7 |
| 5 | Mojibake | Bozuk UTF-8 görünümlü metin | `vault-utf8-writer.mjs` / yeniden ölçüm | `.ai/MEMORY.md:411` |
| 6 | Türkçe culture regex | PowerShell 5.1 `-match` 'I' hatası | CultureInvariant regex | `.ai/MEMORY.md:411` |
| 7 | Vault bozulması | Boot okuma hatası | `git checkout` + son commit | `.ai/AGENTS.md` §17 #10 |

Bilinen 6 sahte kırık link (`flow/auth/01-login.md [[C]]` vb.) "onarmaya" kalkışılırsa şablon
bozulur; sayı 6'da kalır ve kusur sayılmaz (`Dosya: .ai/AGENTS.md · §13.7`).

## §18 Boot Hata Modları — Doğrulama Betiği Kanıtı (PowerShell)

Kırık-link denetimi gerçek bir betikle yapılır; regex wiki-link biçimini (`[[hedef]]`) ve
sayımları raporlar. Betik yalnız **repo kökünden** çalıştırılır (§17/3-4).

```powershell
# Dosya: .ai/scripts/wiki-link-check.ps1 · Satır: 24, 41-43
foreach($m in [regex]::Matches($text,'\[\[([^\]|#]+)(?:[#|][^\]]*)?\]\]')){
  $t=$m.Groups[1].Value.Trim()
  ...
}
"kontrol edilen link: $checked"
"kirik link: "+$broken.Count
$broken | Select-Object -First 40 | ForEach-Object { "  "+$_ }
```

2026-09-29 kök ölçümü: `227 link / 6 raporlanan` = **gerçek kırık 0** (`Dosya: .ai/AGENTS.md ·
§13.7`). Önermeler (boot hata modu iddiaları) yalnız bu tür ölçümlerle yazılır; sayı yoksa
`UNKNOWN`. Frontmatter gate'i aynı disiplindedir: `dosya: N | sorunlu: M` çıktısı
(`Dosya: .ai/scripts/screens-frontmatter-check.ps1 · Satır: 73`).

## §19 Güvenlik — Boot'ta Secret Okunmaz

Boot paketi **salt-okunur** ve **gizli anahtar içermez**; boot'a giren her bayt context'e girer
ve context sızıntısı = secret sızıntısıdır. `.claude/settings.json` hem hook kaydı hem MCP/izin
yüzeyidir → onaysız değiştirilemez ve `EXA_API_KEY` değeri hiçbir `.md`'ye yazılmaz.

| # | Kural | Yüzey | İhlal Sonucu | Kanıt |
|---|-------|-------|--------------|-------|
| 1 | Secret `.md`/`.json`/`.log`'a yazılmaz; yalnız `.env`/env | boot + vault | Anahtar iptali + log ERROR | kök `AGENTS.md` §3 |
| 2 | `.ai/.env.figma` tek kaynak; betikte sabit anahtar yok | Figma | Güvenlik ihlali | `.ai/AGENTS.md` §13.9 #3 |
| 3 | `settings.json` onaysız değiştirilemez | hook/MCP/izin | Geri alma + bildirim | `.claude/AGENTS.md` §3.3 #2 |
| 4 | `log.md`'de hassas veri → `[REDACTED]` | audit trail | Maskeleme | `.ai/AGENTS.md` §17 #3 |
| 5 | `.env` dosyası → yalnız Security Engineer | credential | Yetkisiz erişim | `.ai/AGENTS.md` §5 |
| 6 | Boot dosyaları read-only (boot sırasında) | vault | Değişiklik boot'u bozar | `.ai/MEMORY.md:92` |

MCP yüzeyi de boot'un parçasıdır (`.claude/settings.json:48-98` — filesystem/chrome-devtools/
claude-mem; browsermcp/playwright/exa `disabled: true`). Boot'ta MCP **listelenir**, anahtarlar
**okunmaz**; `EXA_API_KEY` değeri bu anayasada ve hiçbir vault `.md`'de geçmez.

## §20 Zero-Hallucination — Boot İddiaları

Boot anayasasındaki her sayısal/eksel iddia **disk ölçümü** veya **dosya künyesi** taşır; taşımayan
iddia `⚠️ VERIFICATION REQUIRED` etiketlenir. Boot, halüsinasyonun en yaygın kaynağıdır çünkü
"her oturum tekrarlanır" — yanlış boot bilgisi her görevi bozar.

| # | İddia tipi | Yazım kuralı | Etiket |
|---|-----------|--------------|--------|
| 1 | Dosya varlığı | Test-Path / glob ile | yoksa `VERIFICATION REQUIRED` |
| 2 | Sayı (dosya, link, satır) | Ölçüm tarihiyle | yoksa `UNKNOWN` |
| 3 | URL / upstream davranış | Resmi docs + erişim tarihi | doğrulanmazsa `⚠️` |
| 4 | Hook davranışı | Betik satır künyesi (`Dosya:`/`Satır:`) | yorum ≠ kod |
| 5 | Diskte olmayan yüzey | "Repo'da bu alan kodu YOK" | uydurma yok |

Örnek: hook "her koşulda exit 0" iddiası betik yorum satırıdır (`Satır: 12`) ve kodla
desteklenir (dispatch `Satır: 182-195`); "skill listesi 351" gibi bir sayı **ölçülmeden yazılmaz**.
Boot'ta ayrıca `⚠️ VERIFICATION REQUIRED` geçen bir vault dosyası, o alanla ilgili görevde
"kaynak alındı" diye kullanılamaz (`.ai/AGENTS.md` §24.3 veri bütünlüğü notu).

## §21 Doğrulama Kapıları (boot + bu dosya)

| # | Kapı | Kriter | Yöntem |
|---|------|--------|--------|
| 1 | Frontmatter | 10 alan, `title`/`parent` anahtarı YOK | gözden geçirme |
| 2 | Boot listesi değişmedi | §24.2 = 14 · §5 = 20 · §14 = 4 satır | sayım |
| 3 | Hook kaydı | `settings.json` UserPromptSubmit → skill-mandate.cjs | Read |
| 4 | Hook `--check` | `OK (N temiz)` / exit 1 ihlal listesi | `node .claude/hooks/skill-mandate.cjs --check` |
| 5 | Wiki-link | Hedefler diskte | `wiki-link-check.ps1` (kökten) |
| 6 | Placeholder | Dosyada `{{` yok | grep |
| 7 | Authority | Son satır birebir | gözden geçirme |
| 8 | Satır ölçümü | `[IO.File]::ReadAllLines(...,UTF8).Count` → 500-520 | PowerShell |
| 9 | Boot bütçe çelişkisi | 25s hedef / 36s tavan — ikisi de kayıtlı | `.ai/MEMORY.md:71,336` |
| 10 | Secret tarama | Anahtar değeri dosyada yok | grep (REDACTED) |

Kapı 8 bu üretimin zorunlu ölçümüdür; kapı 3-4 boot tetikleyicisinin canlı kanıtıdır (§10-§12).

## §22 Upstream Doğrulama (resmi kaynaklar — 3)

Boot anayasasının hook/kural tarafı, araç üreticisinin resmi dokümanlarıyla **çapraz doğrulandı**
(erişim: 2026-10-17). Uydurma URL yoktur; doğrulanamayan her madde §9'da `⚠️` işaretlidir.

| # | Kaynak | Ne doğruladı | İlgili § |
|---|--------|--------------|----------|
| 1 | `https://code.claude.com/docs/en/hooks` | `SessionStart` = oturum başlar/devam eder; `UserPromptSubmit` prompt işlenmeden önce; olay timeout'u 30s; hook kaynakları (settings/skill/subagent frontmatter) | §10-§12 |
| 2 | `https://code.claude.com/docs/en/hooks-guide` | Olay tablosu: `SessionStart` başlangıç/devam; `UserPromptSubmit` context enjeksiyonu + doğrulama; `additionalContext` çıktısı | §10-§11 |
| 3 | `https://code.claude.com/docs/en/memory` | CLAUDE.md her oturum başında okunur; 200 satır üstünde uyum düşer; auto memory ilk 200 satır/25KB; `@import` launch'ta yüklenir | §9, §13 |

Anlamlı fark: upstream'te `SessionStart` ayrı bir olaydır; CoreMusic boot'u bu olayın **içine
sığdırılmış vault okuma kümesidir** (hook betiği değil, ajan okuması + mandate hook'u). Bu nedenle
"boot = SessionStart hook'u" denmez — boot, hook'u **kullanır**, hook'a **eşit değildir**.

## §23 PHP Kanıtı — Uygulama boot'u ≠ ajan boot'u

Repo'da **ajan boot protokolünü yükleyen PHP kodu YOKTUR** (`.ai/` dosyalarını okuyan, boot
listesini yürüten bir sınıf bulunmaz). `boot()` adını taşıyan tek PHP yüzeyi, uygulamanın
**çalışma-zamanı açılışıdır** — bu anayasayla ad paylaşır, iş paylaşmaz:

```php
// Dosya: shared/src/Bootstrap/RuntimeBootstrap.php · Satır: 7-20
public static function boot(bool $debugMode = false): void
{
    date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Europe/Istanbul');
    if ($debugMode) { error_reporting(E_ALL); ini_set('display_errors', '1'); }
    else { error_reporting(E_ERROR | E_WARNING | E_PARSE); ini_set('display_errors', '0'); }
    ini_set('log_errors', '1');
}
```

Bu boot: timezone + hata raporlama ayarıdır (`shared/AGENTS.md` §2 "Ortak çalışma zamanı
kurulumu"). Ajan boot protokolü **PHP'de değil**, markdown + hook betiği (JS) + PowerShell
gate'lerinde yaşar (§10-§12, §18). `PageRouterKernel.php:325` yalnız `APP_ENV_MODE` yorumu taşır.

## §24 İllüstratif Örnek — Python (session initializer)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
# boot paketi okuyucu iskeleti — upstream docs (pathlib) referansı; repo'da karşılığı YOK
from pathlib import Path

BOOT_P0 = ["CLAUDE.md", "AGENTS.md"]            # kök boot zinciri (AGENTS.md §14, satır 1-2)
BOOT_VAULT = ["CLAUDE.md", "AGENTS.md", "WORKFLOW.md"]  # .ai/ kökünün ilk üçü (P0)

def boot(repo: Path) -> list[str]:
    loaded: list[str] = []
    for rel in BOOT_P0 + [f".ai/{f}" for f in BOOT_VAULT]:
        p = repo / rel
        if not p.exists():
            loaded.append(f"VERIFICATION REQUIRED: {rel} yok")   # uydurma içerik YOK
            continue
        loaded.append(p.read_text(encoding="utf-8"))
    return loaded  # on-demand dosyalar BİLİNÇLİ olarak okunmaz (§13)
```

Kural: eksik dosya **uydurulmaz**, etiketlenir; on-demand kümeye dokunulmaz — boot bütçesi
(§9) bu iskeletin neden minimal olduğunu açıklar.

## §25 İllüstratif Örnek — TypeScript (file preloader + hook gate)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```ts
// hook gate iskeleti — upstream docs (node stdin/stdout hook sözleşmesi) referansı; repo kanıtı DEĞİL
type HookOut = { hookSpecificOutput: { hookEventName: string; additionalContext: string } };

export function mandateGate(prompt: string, skills: string[]): HookOut | null {
  const matched = skills.filter(s => prompt.toLowerCase().includes(s.toLowerCase()));
  if (matched.length === 0) return null;                       // eşleşme yok → mandate yok
  return {
    hookSpecificOutput: {
      hookEventName: "UserPromptSubmit",
      additionalContext: `SKILL ZORUNLULUĞU: ilk işlem şu skill'i yükleyin: ${matched.join(", ")}`,
    },
  };
}
```

Bu iskeletin gerçek repo karşılığı `skill-mandate.cjs`'tir (§10-§12); fark: gerçek betik skill
listesini **diskten tarar**, iskelet parametre alır. Boot ≠ hook: hook her prompt'ta çalışır,
boot oturum başında 1 kez (§22).

## §26 Açık Sorular

| # | Soru | Öncelik | Durum / Kanıt |
|---|------|---------|----------------|
| 1 | Boot süresi kuralı 25s (§16) ile 36s (§5 toplam) — hangi bağlayıcı? | HIGH | Açık — bu anayasa §9'da "25s hedef / 36s tavan" olarak kaydetti; Vault Steward onayı gerekir |
| 2 | `.claude/rules/*` (§5/15) diskte 1 dosya (`skill-usage.md`) — liste genişletilecek mi? | MEDIUM | `⚠️ VERIFICATION REQUIRED` (envanter ölçülmedi) |
| 3 | Prompt arşivleri (§5/16-19) `archives/prompt*-2026-09-01` konumunda — hâlâ güncel mi? | MEDIUM | `.ai/AGENTS.md` §14.1 "Faz 1 düzeltmesi" notu var; yeniden ölçüm yok |
| 4 | Token bütçesi için sayısal eşik vault'ta yok (§9) | HIGH | `UNKNOWN` — ölçülmeli (boot dosyaları toplam token) |
| 5 | `boot-protocol/index.md` children (kademe-3) ne zaman açılır? | LOW | ADR-097 K4 — sonraki faz (`boot-protocol/index.md:6`) |
| 6 | Hook `--check` CI'ye (`.github/workflows/`) bağlandı mı? | MEDIUM | `⚠️ VERIFICATION REQUIRED` — workflow dosyaları bu üretimde okunmadı |

## §27 Kaynaklar

| # | Kaynak | Yol / URL | Kullanım |
|---|--------|-----------|----------|
| 1 | Boot 20 adım + bütçe | `.ai/MEMORY.md` §5 (Satır: 311-338) · §16 (Satır: 71) | §6, §8, §9 |
| 2 | 14 dosya + ultra thinking | `.ai/AGENTS.md` §24.1-§24.3 · §25.4 | §5, §15, §16 |
| 3 | Boot zinciri 4 satır | kök `AGENTS.md` §14 | §4 |
| 4 | Skill mandate hook'u | `.claude/hooks/skill-mandate.cjs` (195 satır) | §10, §12 |
| 5 | Hook kaydı + instructions | `.claude/settings.json` Satır: 2-47 | §11, §19 |
| 6 | Wiki-link gate | `.ai/scripts/wiki-link-check.ps1` (43 satır) | §18 |
| 7 | Frontmatter gate | `.ai/scripts/screens-frontmatter-check.ps1` | §18 |
| 8 | Retrieval indeksi | `.ai/RAG.md` §3-§5 (+ rag-anayasa) | §13, §14 |
| 9 | PHP boot yüzeyi | `shared/src/Bootstrap/RuntimeBootstrap.php` | §23 |
| 10 | Upstream hooks | `https://code.claude.com/docs/en/hooks` + `/hooks-guide` | §22 |
| 11 | Upstream memory | `https://code.claude.com/docs/en/memory` | §9, §22 |
| 12 | Alt-dümğüm künyesi | `.ai/architecture/K015-AI/boot-protocol/index.md` | §26 |

## §28 Ölçüm / Gate Notu

| # | Eşik | Araç | Not |
|---|------|------|-----|
| 1 | ≥500 tam satır | `[IO.File]::ReadAllLines($hedef,[Text.Encoding]::UTF8).Count` | 500-520 bandı |
| 2 | FM 10 alan | gözden geçirme | `title`/`parent` anahtarı YAK |
| 3 | Authority son satır | gözden geçirme | birebir son satır |
| 4 | Kod bloğu | JS ×2 (skill-mandate) · JSON ×1 · PowerShell ×2 · PHP ×1 | künyeli alıntı |
| 5 | İllüstratif | Python ×1 · TypeScript ×1 | her blokta uyarı etiketi |
| 6 | Web kaynak | 3 resmi (code.claude.com) | erişim 2026-10-17 |
| 7 | Secret | `EXA_API_KEY` değeri dosyada yok | grep |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
