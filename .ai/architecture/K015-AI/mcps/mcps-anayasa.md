---
node_id: mcps-anayasa
node_type: module
status: active
parent_id: mcps
children: []
depends_on: [mcps-index]
evidence:
  - ".claude/mcp.json (MCP sunucu envanteri kanıtı, 2026-10-17 okuma)"
  - ".claude/settings.json (hook/plugin env referansları, künyeli alıntı)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı"
open_questions: []
group: mimari
---

# mcps-anayasa - MCP Yapılandırma Kuralları

> **Bu dosya ne değildir:** MCP sunucularının **kodunu/patch'ini**, sürüm/star/lisans envanterini
> (bilinçli boş — `mcps/index.md:11-14`) ve `.claude/mcp.json`/`settings.json` dosyalarının **tam
> kopyasını** taşımaz; burada **MCP yapılandırma anayasası** vardır — hangi sunucu neden tanımlı,
> MCP hook/skill/plugin'den nasıl ayrılır, hangi kök/secret/izin kuralı bağlayıcı, hata modunda
> ne yapılır. Çelişkide disk kazanır.

## §1 Rol (ELI15)

MCP alt-domaini, ajanların dış dünyaya (dosya sistemi, tarayıcı, arama motoru, hafıza katmanı)
**protokol üzerinden** bağlandığı sunucu envanterinin ve bu bağlantının **güvenlik/izin/hata**
kurallarının sahibidir. MCP bir "araç listesi" değil, **JSON-RPC tabanlı bir sözleşme**dir: istemci
(ajan host'u) başlatır, sunucu yeteneklerini (tool/resource/prompt) beyan eder, oturum boyunca
capability sınırı geçilmez. Bu dosya süreç dosyası değildir; envanteri okur, kural koyar, durum
raporlar.

| Soru | Cevap | Kanıt |
|------|-------|-------|
| Bugünkü envanter nerede? | `.claude/mcp.json` + `.claude/settings.json → mcpServers` | 2026-10-17 okuma |
| Aktif sunucu sayısı (Claude Code yüzeyi)? | 1 (mcp.json exa) + 3 aktif + 3 disabled (settings.json) | §5-§6 |
| Hook/skill/plugin ile fark? | Hook = tetikleyici · skill = talimat · plugin = paket · MCP = protokol | §2 |
| Sahibi kim? | Kullanıcı (onay) · Security (secret/izin denetimi) | `.claude/AGENTS.md §3.1` |

> **eli10:** Bu dosya, ajanların dışarıya bağlandığı "kapıların" listesini ve kapı kurallarını yazar.
> **eli15:** Envanter ayrı yazılır çünkü sunucu listesi kural metninden daha sık değişir; tek
> dosyada toplansa kural ile sürüm birbirine karışır (`Dosya: .claude/AGENTS.md · Satır: §3.1`).

## §2 Kapsam (in / out) — dört ayrı mekanizma

| Kapsam | Kapsam Dışı | Kanıt |
|--------|-------------|-------|
| `.claude/mcp.json` + `settings.json → mcpServers` envanteri (7 sunucu girdisi) | Sunucu **sürüü/star/lisans** kanıtı — bilinçli boş (`.mcps/index.md:11-14`) | `.claude/mcp.json:1-9` · `.claude/settings.json:48-99` |
| MCP ↔ hook/skill/plugin ilişki haritası | Hook betiklerinin tam kodu (yalnız künyeli alıntı) | `.claude/settings.json:2-39` |
| MCP güvenlik kuralları (kök kilidi, secret, izin) | 3. taraf sunucu **iç** davranışı (upstream kodu) | §13-§16 |
| MCP hata modları (yok/timeout/yetkisiz) | Uygulama kodunun MCP'ye bağlanması — repo'da MCP istemci kodu YOK | §17-§19 |
| MCP'nin K015-AI (ajan altyapısı) kapsamı | K006-yapay-zeka (ML ürün) kapsamı | `mcps/index.md:27` |

**Dört mekanizma ayrımı (bağlayıcı):** MCP ≠ hook ≠ skill ≠ plugin. Hook `UserPromptSubmit` gibi
**olay anında çalışır** (`.claude/settings.json:3-12`); skill **yüklenen talimat paketidir**
(kök `CLAUDE.md` §Skill Usage Mandate); plugin **araç eklenti paketidir**
(`settings.json:123-125`); MCP ise **istemci-sunucu protokolüdür** ve JSON-RPC oturumu taşır.
Birbirinin yerine kullanılmaz; bir MCP sunucusu hook tetiklemez, hook bir tool çağırmaz.

## §3 MCP Protokol Çekirdeği (sunucu/istemci modeli + JSON-RPC)

Resmi tanımda MCP, AI uygulamalarını dış sistemlere bağlayan **açık standarttır**; USB-C benzeri
tek arayüz sağlar (`https://modelcontextprotocol.io/docs/getting-started/intro`, 2026-10-17
doğrulandı). Mimari **host → client → server** üçlüsüdür: host birden çok client'ı yönetir, her
client **1:1** bir sunucuyla stateful oturum kurar, güvenlik sınırı client'lar arasındadır
(`https://modelcontextprotocol.io/specification/2025-06-18/architecture`, 2026-10-17 doğrulandı).
Taşıma **JSON-RPC 2.0** üzerindedir; init sırasında **capability negotiation** yapılır ve oturum
boyunca beyan edilen yetenek dışına çıkılmaz (aynı kaynak).

| Bileşen | Ne yapar | Sınır | Kaynak |
|---------|----------|-------|--------|
| Host | Client lifecycle, izin/consent, LLM entegrasyonu | Güvenlik politikasının sahibi | architecture (intro §) |
| Client | 1:1 sunucu oturumu, capability exchange, mesaj yönlendirme | Diğer sunucuları "görmez" | architecture |
| Server | tool/resource/prompt beyanı, bağımsız çalışma | Yalnız bağlam ihtiyacı kadar bağlam alır | architecture |
| Transport | stdio (yerel süreç) / HTTP (uzak servis) | Yerel süreç = aynı makine ayrıcalığı | security_best_practices |

Tasarım ilkeleri bağlayıcıdır: sunucu **odaklı ve küçük** olmalı, sunucular birbirini **okuyamaz**,
özellikler **kademeli** eklenebilir (architecture "Design Principles" 1-4). CoreMusic'te tüm
sunucular `npx`/`node` ile **yerel stdio** sınıfındadır (§5-§6) — yani hepsi makine ayrıcalığıyla
çalışır; bu, §13 kök kilidini zorunlu kılar.

## §4 MCP Yetenekleri (tool / resource / prompt)

Sunucu init yanıtında üç temel yeteneği **beyan** eder; beyan edilmeyen çağrı oturumda geçersizdir
(architecture "Capability Negotiation"). CoreMusic envanterindeki sunucuların hangi capability'leri
sunduğu **upstream paket içeriğidir** — bu anayasada tool adı uydurulmaz (§21); envanter yalnız
"amaç" düzeyinde kayıt tutar (§6).

| Capability | Ne taşır | Kim çağırır | Tipik örnek sınıfı | Kaynak |
|------------|----------|-------------|--------------------|--------|
| **Tools** | Eylem/dış çağrı (arama, dosya okuma, tıklama) | Model (tool-use döngüsü) | `web_search`, `browser_click` | architecture |
| **Resources** | Veri/bağlam dosyası (salt okuma bağlam) | İstemci/host | dosya sistemi kökleri | architecture |
| **Prompts** | Şablon prompt'lar (kullanıcı tetikli) | Kullanıcı | görev şablonları | architecture |
| Sampling | Sunucunun LLM'e bağlam istemesi | Sunucu → client | özetleme isteği | architecture |
| Roots | İstemcinin sunucuya açtığı kökler | Host | `C://www//coremusic.net` | mcp.json args |

Capability negotiation kuralı: **beyan edilen = oturumda kullanılabilir**; taraf 3. özellik dayatamaz
(architecture). CoreMusic bu kuralı vault kuralına çevirir: ajan, envanterde **tanımlı olmayan** bir
tool'u "varmış gibi" kullanamaz — tool çağrımı listesi okunarak başlar (§20).

## §5 Envanter I — `.claude/mcp.json` (gerçek alıntı, künyeli)

Dosya diskte mevcuttur ve **tek sunucu** taşır (2026-10-17 okuma — 9 satır):

```json
// Dosya: .claude/mcp.json · Satır: 1-9 (tam dosya, EXA_API_KEY değeri REDACTED)
{
  "exa-mcp-server": {
    "type": "local",
    "command": ["npx", "-y", "exa-mcp-server"],
    "environment": {
      "EXA_API_KEY": "REDACTED"
    }
  }
}
```

Künye notları: `type: local` + `npx -y` = her başlatmada paket çekilir (sürüm sabitlenmemiştir —
§28 açık soru). `EXA_API_KEY` değeri dosyanın **6. satırında düz metin durmaktadır**; bu belgeye
kopyalanmaz (kural §16; değer REDACTED). Bu dosya Claude Code'un **kendi MCP yüzeyidir** ve
`settings.json → mcpServers` ile **kopya/çakışma** içindedir (§7).

## §6 Envanter II — `.claude/settings.json → mcpServers` (6 sunucu)

`settings.json:48-99` altı sunucu tanır; 3 aktif, 3 `disabled: true` (2026-10-17 okuma):

| # | Sunucu (anahtar) | Komut (özet) | Durum | Amaç (envanter düzeyi) |
|---|------------------|--------------|-------|------------------------|
| 1 | `filesystem` | `npx -y @modelcontextprotocol/server-filesystem C:/ C://www C://www//coremusic.net` | **aktif** | Yerel dosya sistemi erişimi — 3 köke kilitli (§13) |
| 2 | `chrome-devtools` | `npx -y chrome-devtools-mcp@latest` | **aktif** | Tarayıcı denetimi — browser testi/consol log |
| 3 | `claude-mem` | `node c:/.claude/plugins/marketplaces/thedotmack/plugin/scripts/mcp-server.cjs` | **aktif** | Bellek katmanı (claude-mem plugin sunucusu) |
| 4 | `browsermcp` | `npx @browsermcp/mcp@latest` | **disabled** | Tarayıcı MCP — kapalı (§28) |
| 5 | `playwright` | `npx -y @playwright/mcp --headless` | **disabled** | Headless tarayıcı otomasyonu — kapalı |
| 6 | `exa` | `npx -y exa-mcp-server` + `env.EXA_API_KEY` (REDACTED) | **disabled** | Web arama — settings yüzeyinde kapalı, mcp.json'da aktif (§7) |

Künye: `Dosya: .claude/settings.json · Satır: 48-99`. `@latest`/`-y` kullanımı iki sunucuda
sürüm **sabitlenmemiş** olduğunu gösterir (§28). `claude-mem` sunucusu sabit **yol** ile çalışır
(paket registry'si değil, marketplace klasörü) — sürüm/taşınabilirlik riski §16.

## §7 Envanter Birleşimi ve Çakışma Kaydı

Aynı anda **iki MCP yapılandırma yüzeyi** vardır ve içerik örtüşür; bu bilinçli bir çift-katman
değil, **ikili kayıt** durumudur (çelişkide disk kazanır kuralı bu belgeyi bağlar):

| Yüzey | Dosya | Sunucu | Aktif | Çakışma kaydı |
|-------|-------|--------|-------|---------------|
| Claude Code MCP | `.claude/mcp.json` | exa-mcp-server | evet | `settings.json`'da `exa` disabled → **iki farklı durum** |
| Claude Code ayar | `.claude/settings.json` | 6 sunucu | 3/6 | exa disabled; mcp.json aktif |
| OpenCode (global) | `~/.config/opencode/opencode.jsonc` (doc iddiası) | exa · filesystem · chrome-devtools · figma | ⚠️ VERIFICATION REQUIRED | dosya bu oturumda **okunmadı** — sayım yapılmaz |
| Çalışma zamanı (bu oturum) | agent katalog kanıtı | browsermcp · chrome-devtools · exa · filesystem · playwright tool'ları görünür | evet | disabled sunucular OpenCode yüzeyinde **aktif** görünür |

**Kural:** "exa kapalıdır" tek başına **yanlış** bir cümledir — hangi yüzeyde kapalı olduğu
söylenir (`settings.json:97 disabled: true`; `mcp.json:2-8 aktif`). Envanter iddiası her zaman
**yüzey + dosya** künyesi ile yazılır; künyesiz "X kurulu" cümlesi §21 ihlalidir.

## §8 Sunucu Amaç Kılavuzu (ne için, ne için DEĞİL)

| Sunucu | Bu ajan için kullanımı | Kullanımı DEĞİL | Kanıt |
|--------|------------------------|------------------|-------|
| `filesystem` | Vault/belge okuma-yazma kök sınırlı | Kök dışı (`D:/`, `~/.ssh`) erişim | `settings.json:49-58` (args) |
| `chrome-devtools` | Gerçek sayfa testi, konsol log, layout doğrulama | Üretim hesaplarıyla oturum açma | `settings.json:59-65` |
| `claude-mem` | Bellek okuma/kaydı (plugin MCP) | Vault yerine geçmek (`.ai/` SSOT'tur) | `settings.json:66-71` |
| `exa` (mcp.json) | Web araştırması (resmi docs doğrulama) | Vault iddiası üretmek — web vault'un altındadır | `mcp.json:2-8` · kök AGENTS §5 |
| `playwright` / `browsermcp` | (disabled) — çağrılmaz | "Varsayım" ile tool çağrısı | `settings.json:72-87` |

Kural: sunucu **amaç dışına** ilk kullanımda Security'ye raporlanır (registry §5 routing:
secret/izin → Security Engineer). Üretim hesap bilgisi tarayıcı MCP oturumuna **taşınmaz**.

## §9 MCP + Hook İlişkisi (skill-mandate tetikler mi, MCP çağırır mı)

`settings.json:2-39` üç hook tanımlar; **hiçbiri MCP çağırmaz** — hook stdin JSON alır, stdout
karar döner; MCP tool çağrımı model döngüsünde ayrı bir katmandır. İlişki **dolaylıdır**: hook
agent'ın bağlamını (skill listesi, risk kapısı) belirler, agent sonra o bağlamla tool çağırır.

| Hook | Tetikleyici | Betik | MCP ilişkisi | Kanıt |
|------|-------------|-------|--------------|-------|
| UserPromptSubmit | prompt gönderimi | `skill-mandate.cjs` | Yok — skill listesi enjekte eder | `settings.json:3-12` |
| PreToolUse (Edit\|Write) | dosya yazımı | `ai-risk-gate.cjs` | Yok — risk kapısı (T1/T2) | `settings.json:13-25` |
| PostToolUse (Edit\|Write) | yazım sonrası | `ai-validate-post.cjs` | Yok — fm/link/ssot validator | `settings.json:26-38` |

**Bağlayıcı soru:** "skill-mandate MCP çağırır mı?" → **HAYIR**. Kanıt: hook stdout'u yalnız
`hookSpecificOutput.additionalContext` üretir (`skill-mandate.cjs:95-100`); hiçbir MCP client/server
import yok (dosya genelinde `require('fs')`/`require('path')` dışındaki tek import yoktur —
`skill-mandate.cjs:16-17`). MCP tool çağrısı hook yaşam döngüsünde **fiziksel olarak mümkün değildir**.

## §10 Hook Kod Kanıtı — skill-mandate.cjs (künyeli alıntı)

```javascript
// Dosya: .claude/hooks/skill-mandate.cjs · Satır: 86-101
const additionalContext = [
  'SKILL ZORUNLULUĞU (kök CLAUDE.md §Skill Usage Mandate): Görev bir skill ile eşleşiyorsa',
  'İLK işlem Skill tool ile yüklemektir; eşleşen skill\'siz işlem başlatmak YASAKTIR.',
  `Proje skill'leri (.claude/skills): ${projectList}.`,
  globalList,
  thirdParty,
  "İstisna: kullanıcı açıkça 'skill kullanma' derse veya görev skill'lerle ilgisizse.",
].filter(Boolean).join(' ');

process.stdout.write(JSON.stringify({
  hookSpecificOutput: {
    hookEventName: 'UserPromptSubmit',
    additionalContext,
  },
}));
process.exit(0);
```

Çıktı şeması MCP tool çağrısı **içermez** — hook yalnız bağlam metni üretir. `thirdParty`
değişkeni (`skill-mandate.cjs:84`) üçüncü taraf skill gruplarını **metin olarak** anar; bu bir MCP
listesi değildir, skill listesidir — ikisi karıştırılmaz (§2).

## §11 MCP + Skill İlişkisi

Skill = yüklenen **talimat**; MCP = çağrılan **protokol tool'u**. Skill içinde bir MCP tool adı
geçiyorsa bu yalnız bir **talimat referansıdır** — tool'u var eden şey skill değil, mcp
yapılandırmasıdır (§5-§6). Skill Usage Mandate hook'u (§9) yalnızca "önce skill yükle" der;
skill yüklendikten sonra ajan MCP tool çağırabilir — ikisi **sıralı ve bağımsız** katmanlardır.

| Durum | Doğru davranış | Yanlış davranış | Kanıt |
|-------|----------------|------------------|-------|
| Görev skill ile eşleşiyor | İLK skill yükle, sonra tool çağır | Tool çağırıp sonra skill | kök CLAUDE.md §Skill Usage Mandate |
| Skill "exa_search kullan" diyor | Envanterde exa var mı kontrol et (§5-§7) | Envanter okumadan çağır | §21 |
| Skill disabled sunucu tool'u istiyor | DUR + kullanıcıya sor (§17) | "Yok sayayım" | `settings.json:72-98` |
| Skill yeni MCP eklemeyi istiyor | Onay kapısı (§15) | Doğrudan mcp.json edit | `.claude/AGENTS.md §3.3 #2` |

Skill'de MCP adı geçmesi o MCP'yi **aktif yapmaz**; disable kaydı (`settings.json`) kazanır.

## §12 MCP + Plugin İlişkisi

Plugin, araç eklentisi paketidir ve **kendi MCP sunucusunu taşıyabilir** — envanterdeki tek böyle
girdi `claude-mem`'dir: plugin marketplace klasöründeki `mcp-server.cjs` `node` ile çalıştırılır
(`settings.json:66-71`). Yani: plugin kuruldu → içinde MCP sunucusu var → mcpServers'a girdi →
tool'lar model kataloğuna düşer. Üçü ayrı katmandır, tek adımla değişmez.

| Katman | Kanıt | Ne verir | Kim onaylar |
|--------|-------|----------|-------------|
| Plugin (enabledPlugins) | `settings.json:123-125` (`frontend-design@claude-plugins-official`) | Araç özelliği | Kullanıcı |
| Plugin MCP sunucusu | `settings.json:66-71` (`claude-mem`) | Tool kataloğu | Kullanıcı + Security |
| Hook (plugin/hooks) | `settings.json:2-39` (proje kökü hook'ları) | Olay tetikleyicisi | Kullanıcı |

`frontend-design` plugin'inin MCP girdisi **yoktur** (settings.json'da yalnız `enabledPlugins`
anahtarında geçer) — "plugin kurulu = MCP var" çıkarımı yasaktır (§21).

## §13 Güvenlik I — filesystem sunucusu kök kilidi

Filesystem sunucusu, kökleri **argüman olarak** alır ve yalnız o köklerde çalışır
(`settings.json:49-58`). CoreMusic kök kilidi: `C:/` · `C://www` · `C://www//coremusic.net`. `C:/`
genişliği **bilinçli genişlik** olarak kayıtlıdır: repo dışı okuma-yazma teknik olarak mümkündür;
buna karşılık `.ai/` yazımı vault kuralıyla (guardrail) ayrıca korunur — MCP kök kilidi tek savunma
hattı değildir.

| # | Kural | Gerekçe | Kanıt |
|---|-------|---------|-------|
| 1 | Kök listesi yalnız onayla değişir | `settings.json` izin yüzeyi | `.claude/AGENTS.md §3.3 #2` |
| 2 | Yeni kök ekleme = Security raporu | Genişleyen veri erişimi | registry §5 routing |
| 3 | `~/.ssh`, kullanıcı profili kök yapılmaz | Credential yüzeyi | OWASP veri koruma |
| 4 | Üretim DB dosyası köke eklenmez | `.sql`/DB yüzeyi ayrı sahipte | registry §5 |

MCP security best practices dokümanı yerel sunucuları **özellikle riskli** sayar: aynı makine
ayrıcalığı çalıştırırlar; stdio transportu yerel süreç erişimi taşır
(`https://modelcontextprotocol.io/specification/2025-06-18/basic/security_best_practices`,
2026-10-17 doğrulandı — "Local MCP Server Compromise"). Bu nedenle kök listesi **daraltılmaya**
göre genişletilir.

## §14 Güvenlik II — secret erişimi (exa anahtarı, figma token)

| Secret | Yüzeydeki durumu | Doğru mu? | Kural / Kanıt |
|--------|------------------|-----------|---------------|
| `EXA_API_KEY` | `mcp.json:6` + `settings.json:95` — düz metin, iki dosyada | **HAYIR** — `.json`'a yazılmaz | `.claude/AGENTS.md §3.3 #1` (REDACTED) |
| `FIGMA_TOKEN` / `FIGMA_FILE_KEY` | `.ai/.env.figma` (gitignore:69) — MCP figma sunucusu bu repo yapılandırmasında **tanımlı değil** | Evet (env) | `.ai/AGENTS.md §13.9 #3` |
| Claude-mem plugin anahtarı | `⚠️ VERIFICATION REQUIRED` — plugin içi ayarlar bu oturumda okunmadı | Bilinmiyor | — |

**Tespit (Red Team):** EXA anahtarı düz metin iki yapılandırma dosyasındadır; bu belgeye
**kopyalanmaz** (REDACTED). İhlal sınıfı: kök kural "secret `.md`/`.log`/`.json` dışına yazılmaz" —
`.json` dahil. Doğru model: MCP sunucusu anahtarı **env substitution** ile alır
(`mcp.json` `environment` bloğu yerine ortam değişkeni referansı); taşınma kararı Security + kullanıcı
onayı ister (§15). Figma token için MCP yolu yoktur — token yalnız `.env.figma`'dan okunur, betiklere
sabitlenmez (§13.9 #3).

## §15 Güvenlik III — MCP izin/yasak kuralları (bağlayıcı 10 madde)

| # | Kural | İhlal sonucu | Kanıt |
|---|-------|--------------|-------|
| 1 | MCP sunucusu ekleme/çıkarma **yalnız kullanıcı onayıyla** | Geri alma + bildirim | `.claude/AGENTS.md §3.3 #2` |
| 2 | Secret değeri mcp.json'a **yazılmaz** (env kullanılır) | Anahtar iptali + log ERROR | `.claude/AGENTS.md §3.3 #1` |
| 3 | Disabled sunucu **çağrılmaz** (`browsermcp`/`playwright`/`exa-settings`) | Yetkisiz tool çağrımı | `settings.json:72-98` |
| 4 | Tool çağrımı öncesi envanter okunur (§6-§7) | Uydurma tool (§21) | bu anayasa |
| 5 | Üretim credential'ı tarayıcı MCP oturumuna taşınmaz | Sızıntı yüzeyi | OWASP / security_best_practices |
| 6 | Proxy/auth URL'lerinde `javascript:`/`data:` şeması red (upstream MUST) | XSS/RCE | security_best_practices "OAuth Authorization URL Validation" |
| 7 | Token passthrough yasak (upstream MUST NOT) | Denetim/audit kaybı | security_best_practices "Token Passthrough" |
| 8 | SSRF: özel/loopback IP hedefleri doğrulanır (upstream MUST consider) | Metadata sızıntısı | security_best_practices "SSRF" |
| 9 | Yerel sunucu komutu onaysız değiştirilmez | RCE yüzeyi | security_best_practices "Local MCP Server Compromise" |
| 10 | Scope min — tool genişliği asgari tutulur | Blast-radius | security_best_practices "Scope Minimization" |

6-10 maddeler upstream MUST/SHOULD maddelerinin CoreMusic karşılığıdır; upstream metni
`modelcontextprotocol.io/specification/2025-06-18/basic/security_best_practices` (doğrulandı
2026-10-17). Bu doküman bağlayıcılığını **repo kuralından** alır; upstream değişirse §29 kaynak
satırı yeniden doğrulanır.

## §16 Secret Kuralları — "mcp.json'a anahtar yazılmaz"

Kural çizgisi nettir: MCP yapılandırma dosyaları (`.json`) **versiyon kontrolündedir** ve bu
nedenle secret taşıyamaz; anahtar **env substitution** (ortam değişkeni / env dosyası) ile verilir.
CoreMusic'te doğru örnek Figma'dır: token yalnız `.ai/.env.figma`'dan okunur, `.gitignore:69`'dadır
(§14). Bugün EXA anahtarı bu kurala **aykırıdır** (§14 tespiti); kural metni değişmez, envanter
düzeltmesi beklenir.

| # | Kural | Mekanizma | Beklenen durum | Kanıt |
|---|-------|-----------|----------------|-------|
| 1 | MCP config'e ham anahtar yazılmaz | env substitution | `EXA_API_KEY` env'den okunur | §15 #2 |
| 2 | `.env` dosyaları gitignore'da | `.gitignore` | commit sızıntısı yok | `.gitignore:69` |
| 3 | Vault `.md`'ye anahtar kopyalanmaz | REDACTED | bu belge §14 kırmızı | `.claude/AGENTS.md §3.3 #1` |
| 4 | Anahtar rotasyonu ihlal sonrası | Security süreci | iptal + yenileme | registry §6 |
| 5 | Plugin içi secret yüzeyi de envanterdedir | denetim | claude-mem anahtarı ❓ | §14 VERIFICATION REQUIRED |

## §17 Hata Modu I — sunucu yok (fallback)

Sunucu yok demek üç farklı durumdur ve her birinin davranışı ayrıdır: (a) yapılandırma girdisi
var ama süreç başlamıyor (npx indirme hatası/offline), (b) girdi `disabled: true`, (c) tool adı
envanterde hiç yok. (c) doğrudan §21 ihlalidir; (a) ve (b) fallback zinciriyle karşılanır.

| Durum | Tespit | Fallback | Kanıt |
|-------|--------|----------|-------|
| (a) süreç başlamıyor | tool çağrımı timeout/çıkış hatası | yerel araca geç (filesystem yerine Read/Grep; exa yerine websearch) | §18 |
| (b) disabled | envanter kaydı `disabled: true` | **çağrımaz**; kullanıcıya sor | `settings.json:77,86,97` |
| (c) tanımsız tool adı | envanterde yok | DUR — uydurma tool yok (§21) | §6-§7 |
| Ağ yok (npx indirme) | offline çalışma | cache varsa başlat; yoksa fallback | upstream stdio |
| claude-mem yolu kırık | `node` çıkış hatası | bellek vault `.ai/MEMORY.md` elle | `settings.json:69` (sabit yol) |

**Fallback zinciri (bağlayıcı):** MCP tool → **aynı işi yapan yerleşik tool** (Read/Grep/webfetch/
browser) → elle işlem → kullanıcıya rapor. Zincir atlanmaz; MCP başarısızlığı sessizce "görev
yapıldı" sayılmaz (Truth Mode).

## §18 Hata Modu II — timeout (retry)

MCP çağrıları `npx`/yerel süreç üzerinden gittiği için timeout kaynağı **iki katmanlıdır**: süreç
başlatma (indirme + init) ve tool çağrım yanıtı. Retry kuralı registry §8 öncelik tablosuna bağlanır
(CRITICAL 5s/1 · HIGH 15s/3 · MEDIUM 30s/3 · LOW 60s/2 — `.ai/AGENTS.md §8`).

| Katman | Tipik belirti | Retry kuralı | Son çare |
|--------|---------------|--------------|----------|
| Başlatma (npx) | init timeout, offline | 1 retry; cache kontrol | yerleşik tool |
| Tool çağrımı | yanıt yok / geç yanıt | öncelik tablosundaki max retry | §17 zinciri |
| Tarayıcı (chrome-devtools) | sayfa kilitlendi | sayfa yenile + 1 retry | screenshot + rapor |
| Bellek (claude-mem) | süreç crash | oturum sonu elle kaydet (on-session-end) | `.ai/log.md` append |
| 3 başarısız tur | — | **DUR** + şüpheli varsayımı yaz | üst karar (kök AGENTS §7.5) |

Süreç-sıfırlama (queue reset, registry §11.2 "Failed") MCP içinde kalıcı oturum içermez; her
stdio sunucusu **stateless yeniden başlatılabilir**. Retry'da tool çağrımı **aynı argümanla**
tekrarlanır — argüman değiştirerek "çalıştırma" yapılmaz (Truth Mode: ölçüm kaydı bozulmasın).

## §19 Hata Modu III — yetkisiz tool → escalation

Yetkisiz tool çağrımı, modelin katalogda olmayan/dahil edilmemiş bir tool'u istemesidir. Upstream
capability kuralı basittir: beyan edilmemiş capability oturumda **yoktur** (architecture) — bu
nedenle "yetkisiz tool" ya (a) uydurma bir addır (§21) ya da (b) disabled/başka yüzeyde tanımlı bir
addır (§7). İkisi de escalation ister, sessiz yutulmaz.

| Durum | Aksiyon | Escalation hattı | Kanıt |
|-------|---------|------------------|-------|
| Envantersiz tool adı | RED — uydurma etiketi | Vault Steward (§21) | §21 |
| Disabled sunucu tool'u | RED + kullanıcıya sor | Kullanıcı onayı | §15 #3 |
| Kök dışı dosya isteği (filesystem) | RED + kök kuralı | Security Engineer | §13 |
| Secret okuma isteği | RED + REDACTED | Security Engineer | §16 |
| Üstelik tekrarlayan | 3 tur sonra DUR | L1→L2→L3 (registry §10) | `.ai/AGENTS.md §10` |

Escalasyon mesajı registry §9.1 formatını izler (konu, kaynak, öncelik, dosyalar, onay durumu) —
MCP kökenli olaylar `log.md`'ye ek satır olarak yazılır (append-only, registry §5).

## §20 Token Ekonomisi (tool çağrım bütçesi)

MCP tool çağrımı token maliyetlidir: her çağrı argument + result bağlamı taşır ve sonuç bağlam
penceresine girer. Bütçe kuralı: **envanter listesi 1 kez okunur** (§6-§7), tool çağrımı
**ihtiyaç başına** yapılır, sonucu **özetlenerek** bağlama alınır (ham büyük çıktı çekilmez).

| Kural | Uygulama | Ölçüt | Kanıt |
|-------|----------|-------|-------|
| Lazy envanter | §6 tablosu görev başında 1 kez | tekrar okuma yasak | bu anayasa |
| Sonuç kırpması | Büyük dosya/HTML parçalanır | tek çağrıda ≤ ~2K token özet | Token overflow → görev başarısız (registry §18 #4) |
| Bağımlı çağrılar sıralı | Aynı sunucuda ardışık | paralelde durum yarışı yok | architecture stateful oturum |
| Bağımsız sunucular paralel | filesystem ∥ exa | bekleme süresi düşer | host çok-client mimarisi |
| Retry bütçesi | max 3 (§18) | sonsuz döngü yok | registry §8 |

Ölçüm notu: MCP tool çağrım sayısını **sayan bir betik repo'da YOKTUR** (`⚠️ VERIFICATION
REQUIRED`) — bu anayasa bütçeyi kural olarak koyar, ölçüm vaat etmez.

## §21 Zero-Hallucination — var olmayan tool uydurulmaz

MCP bağlamında halüsinasyonun spesifik biçimi: envanterde **olmayan bir tool adını** çağırmak ya da
"X sunucusu şunu yapar" diye capability uydurmak. Kural: tool çağrımı **§6-§7 envanterinden**
gelir; capability düzeyi iddia (hangi tool'u sunuyor) yalnız upstream dokümantasyonla yazılır ve
`⚠️ VERIFICATION REQUIRED` taşır.

| # | Yasak iddia | Doğru davranış | Kanıt |
|---|-------------|----------------|-------|
| 1 | "figma MCP sunucusu var" (bu repo yapılandırmasında yok) | yok der; figma `.env.figma` + betik yoludur | §14 · §13.9 #3 |
| 2 | "playwright aktif" | disabled kaydı kazanır | `settings.json:86` |
| 3 | Tool adı ezberden | envanterden oku; yoksa DUR | §6 |
| 4 | Sürüm/lisans/star sayısı | bu turda TOPLANMAYACAK (bilinçli boş) | `mcps/index.md:11-14` |
| 5 | "OpenCode config 4 sunucu" | doc iddiası — dosya okunmadı → VERIFICATION REQUIRED | §7 |

## §22 Ultra Thinking (§24) Bağlamında MCP

Registry §24.1 5-adım protokolü (Vault Oku → Bağlam → Hata → Tahmin → Doğrulama) MCP çağrımını
**adım 2-5 arasına** yerleştirir: vault okunduktan **sonra** tool listesi taranır; tool sonucu
ham değil, doğrulanmış bağlam olarak alınır. §24.2 14 dosya okuma listesinde MCP dosyası **yoktur**
— bu anayasa on-demand referanstır (boot listesi değiştirilmez; rag-anayasa §13 ile aynı mantık).

| Adım | MCP ilgisi | Kural | Kanıt |
|------|-----------|-------|-------|
| 1 Vault Oku | MCP çağrımı YOK | önce `.ai/` | `.ai/AGENTS.md §24.1` |
| 2 Bağlam | envanter taraması (§6-§7) | tool listesi okunur | bu anayasa |
| 3 Hata kontrolü | tool argüman doğrulama | secret/kök kontrolü (§13-§16) | §15 |
| 4 Tahmin | sonuç tahmini + bütçe | §20 | §20 |
| 5 Doğrulama | tool sonucu = kanıt, iddia değil | sonucu dosyayla çaprazla | Truth Mode |

## §23 Execution Loop (§7) Bağlamında MCP

Kök `AGENTS.md` §7 execution loop'u (skill eşleşmesi → vault → hedef dosya → kod → LINT → browser
test → commit) MCP'yi **iki noktada** kullanır: keşif fazı (filesystem Read/Grep; exa/websearch ile
resmi docs doğrulama) ve doğrulama fazı (chrome-devtools ile browser testi). Faz 1 (keşif) kuralı
MCP için de geçerlidir: **onaysız yeni MCP sunucusu yok, onaysız kök genişletme yok**.

| Loop adımı | Kullanılan MCP | Yerel fallback | Kanıt |
|------------|----------------|----------------|-------|
| Skill eşleşmesi (0) | yok | hook çıktısı (§9-§10) | `settings.json:3-12` |
| Vault okuma (2) | filesystem | Read/Grep tool'u | §13 |
| Resmi docs doğrulama (Research) | exa (mcp.json) | websearch/webfetch | §5 |
| Kod yazımı (5) | yok — hook kapısı (Edit\|Write) | — | `settings.json:13-38` |
| Browser testi (8) | chrome-devtools | playwright (disabled) | §6 #2, #5 |
| Commit (9) | yok — **subagent atamaz** | orkestratör | kök AGENTS §7.9 |

## §24 Doğrulama Kapıları (envanter + anayasa)

| # | Kontrol | Kriter | Yöntem |
|---|---------|--------|--------|
| 1 | Frontmatter 10 satır | sabit sıra, `title`/`parent` anahtarı YOK | gözden geçirme |
| 2 | Envanter doğruluğu | 7 girdi (1 + 6) disk okumasıyla birebir | `.claude/mcp.json` + `settings.json` okuma |
| 3 | Secret REDACTED | `EXA_API_KEY` değeri bu dosyada YOK | grep |
| 4 | Küne her alıntı | `Dosya:`/`Satır:` mevcut | gözden geçirme |
| 5 | İllüstratif blok etiketi | her illüstratif blok üstünde uyarı | gözden geçirme |
| 6 | Placeholder | `{{` yok | grep |
| 7 | Authority son satır | birebir | gözden geçirme |
| 8 | Satır ölçümü | ≥500 (ReadAllLines, UTF8) | PowerShell |
| 9 | Web kaynakları | 3 URL gerçek HTTP 200 ile doğrulandı | webfetch 2026-10-17 |
| 10 | Uydurma yok | VAR/ yok ayrımı her satırda etiketli | Truth Mode |

## §25 PowerShell — Envanter Doğrulama Betiği Kalıbı

> Betik **kalıptır** — repo'daki mevcut bir dosyanın alıntısı DEĞİL; kökten çalıştırma kuralı
> (§24/9, registry §13.6) bağlayıcıdır.

```powershell
# Dosya: (betik kalıbı — .ai/scripts/ altında commit edilmemiş, kökten çalıştırılır)
$ErrorActionPreference = 'Stop'
$cfg = Get-Content .claude\settings.json -Raw | ConvertFrom-Json
$servers = $cfg.mcpServers.PSObject.Properties
$servers | ForEach-Object {
    $disabled = [bool]($_.Value.disabled)
    $roots = ($_.Value.args | Where-Object { $_ -match '^[A-Za-z]:/' }) -join ' · '
    [PSCustomObject]@{
        Sunucu = $_.Name
        Durum  = if ($disabled) { 'disabled' } else { 'aktif' }
        Kok    = $roots
    }
} | Format-Table -AutoSize
# Künye: .claude/settings.json · Satır: 48-99 (6 girdi beklenir: 3 aktif / 3 disabled)
```

Beklenen kapı çıktısı: `filesystem` aktif (3 kök) · `chrome-devtools` aktif · `claude-mem` aktif ·
`browsermcp`/`playwright`/`exa` disabled. Fark varsa envanter §6-§7 yeniden yazılır (disk kazanır).

## §26 İllüstratif Örnek — Python (minimal MCP server)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
# upstream docs (modelcontextprotocol.io — build server) referansı; repo'da karşılığı YOK
from mcp.server.fastmcp import FastMCP

mcp = FastMCP("coremusic-example")   # isim yalnız illustrasyon

@mcp.tool()
def read_vault_note(path: str) -> str:
    """Vault notu oku — kök kilidi uygulama katmanında zorunlu (§13)."""
    if not path.startswith(".ai/"):
        raise ValueError("kök dışı erişim — §13 ihlali")
    return open(path, encoding="utf-8").read()

if __name__ == "__main__":
    mcp.run()   # stdio transportu — yerel süreç ayrıcalığı
```

Repo'da bu sınıftan **hiçbir import yoktur**; CoreMusic'te MCP sunucusu **yazılmamıştır**, hazır
3. taraf paketler kullanılır (§6: `npx`/`node` girdileri).

## §27 İllüstratif Örnek — TypeScript (client tool çağrısı)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```ts
// upstream docs (modelcontextprotocol.io — build client) referansı; repo'da yok
import { Client } from "@modelcontextprotocol/sdk/client/index.js";
import { StdioClientTransport } from "@modelcontextprotocol/sdk/client/stdio.js";

const transport = new StdioClientTransport({
  command: "npx",
  args: ["-y", "exa-mcp-server"],
  env: { EXA_API_KEY: process.env.EXA_API_KEY ?? "" },  // §16: env substitution
});

const client = new Client({ name: "coremusic-agent", version: "0.0.0" });
await client.connect(transport);
const tools = await client.listTools();          // §20: envanter 1 kez okunur
const res = await client.callTool({              // §15: envanter dışı tool yok
  name: "web_search",                            // ad upstream pakete aittir
  arguments: { query: "model context protocol security" },
});
```

İllüstrasyon, §16 (env substitution) ve §20 (lazy envanter) kurallarının protokoldeki karşılığını
gösterir; `web_search` adının bugün hangi pakette hangi imzayla sunulduğu **§21 gereği doğrulanmadan
kullanılmaz**.

## §28 Açık Sorular

| # | Soru | Öncelik | Durum / Kanıt |
|---|------|---------|----------------|
| 1 | `EXA_API_KEY` env'e taşınacak mı? (bugün düz metin ×2) | **HIGH** | açık — §14 tespiti, onay bekliyor |
| 2 | `mcp.json` ↔ `settings.json` exa çakışması hangi yüzeyle kapatılır? | HIGH | açık — §7 (ikisi de diskte) |
| 3 | `playwright`/`browsermcp` neden disabled? Bilinçli mi? | MEDIUM | `⚠️ VERIFICATION REQUIRED` — gerekçe kaydı yok |
| 4 | `npx -y`/`@latest` sürümleri sabitlenecek mi? (reproducible build) | MEDIUM | açık — `mcp.json:4` · `settings.json:63,84` |
| 5 | `claude-mem` sunucusu marketplace yolundan npm'e taşınacak mı? | MEDIUM | açık — `settings.json:69` |
| 6 | Tool çağrım bütçesi nasıl ölçülür? (sayım betiği YOK) | LOW | `⚠️ VERIFICATION REQUIRED` — §20 |
| 7 | `mcps/index.md` açık soruları (sürüm/star/lisans) ne zaman kapanır? | LOW | bilinçli boş — `mcps/index.md:11-14` |

## §29 Kaynaklar

| # | Kaynak | Yol / URL | Doğrulama | Kullanım |
|---|--------|-----------|-----------|----------|
| 1 | MCP envanteri (Claude Code) | `.claude/mcp.json` (9 satır) | 2026-10-17 okuma | §5, §7 |
| 2 | MCP envanteri + hook/plugin | `.claude/settings.json` (126 satır) | 2026-10-17 okuma | §6-§16 |
| 3 | Hook kod kanıtı | `.claude/hooks/skill-mandate.cjs` (195 satır) | 2026-10-17 okuma | §9-§10 |
| 4 | Alt-dümğüm künyesi | `.ai/architecture/K015-AI/mcps/index.md` (48 satır) | 2026-10-17 okuma | §2, §21, §28 |
| 5 | Anayasa şablonu (yapı referansı) | `.ai/architecture/K015-AI/rag/rag-anayasa.md` | 2026-10-17 okuma | § yapsı |
| 6 | MCP nedir | `https://modelcontextprotocol.io/docs/getting-started/intro` | webfetch 2026-10-17 ✅ | §3 |
| 7 | Mimari (host/client/server, JSON-RPC, capability) | `https://modelcontextprotocol.io/specification/2025-06-18/architecture` | webfetch 2026-10-17 ✅ | §3-§4, §19 |
| 8 | Güvenlik (confused deputy, SSRF, local compromise, scope) | `https://modelcontextprotocol.io/specification/2025-06-18/basic/security_best_practices` | webfetch 2026-10-17 ✅ | §13-§15 |
| 9 | Ultra Thinking / Execution Loop | `.ai/AGENTS.md` §24 · kök `AGENTS.md` §7 | okuma (boot) | §22-§23 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode