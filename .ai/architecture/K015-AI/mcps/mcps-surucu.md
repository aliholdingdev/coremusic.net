---
node_id: mcps-surucu
node_type: module
status: active
parent_id: mcps
children: []
depends_on: [mcps-anayasa]
evidence:
  - ".claude/mcp.json (tool envanteri, 2026-10-17 okuma)"
  - ".claude/hooks/skill-mandate.cjs (hook-MCP ilişkisi kanıtı)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı"
open_questions: []
group: mimari
---

# mcps-surucu - MCP Kullanım Sürucusu

> **Kapsam:** Bir görev geldiğinde hangi MCP sunucusunun, hangi tool'unun, hangi argümanlarla
> çağrılacağı — akış, karar ağacı, çağrım sonrası doğrulama, sunucu ekleme/kaldırma, arıza
> fallback zinciri, context lock ilişkisi ve doğrulama kapıları. **Kural koyar değil, tarif
> eder:** bağlayıcı kurallar `Dosya: .claude/AGENTS.md · Satır: 44, 77, 91` (onay kapısı) ve
> `Dosya: .ai/AGENTS.md · Satır: 439-472` (§12 Context Lock) içindedir.
>
> **Ölçüm tarihi:** 2026-10-17 (satır sayıları `[IO.File]::ReadAllLines(...UTF8).Count` ile
> diskten ölçülmüştür — §17). Bu dosya `.claude/mcp.json` ve `.claude/hooks/skill-mandate.cjs`
> **tam okuma** üzerine kuruludur; okunmayan tool args uydurulmamıştır.

## §1. ELI15 — Bu sürücü ne işe yarar

Bir ajan görev aldığında elindeki işi tek bir tool'a bağlamaz: işi önce **ihtiyaç sınıfına**
ayıırır (arama mı, dosya mı, tarayıcı mı), sonra bu sınıfa karşılık gelen **sunucuyu**
seçer, sonra o sunucunun **gerçek tool adını** çağırır, sonra dönen sonucu **doğrular**
(künye/kanıt), sonra devam eder. Sunucu yoksa ya da arızalanırsa fallback zinciri
devreye girer (§9): sunucu → native tool → manuel → escalation. **Hiçbir tool çağrımı
doğrulanmadan sonuca yazılmaz** — MCP çıktısı da tıpkı dosya okuması gibi kanıttır ama
**tek başına yeterli kanıt değildir** (§6).

> **eli10:** "Bu işi hangi dış araç yapar?" sorusunun cevabı bir tabloda yazılıdır (§4); sürücü
> bu tabloyu kullanmayı, çağrılan aracın gerçekte ne döndüğünü doğrulamayı ve araç yoksa
> dürüstçe "elle yaparım / yukarı sorarım" demeyi tarif eder.
>
> **eli15:** MCP sunucuları 3. taraf kod çalıştırır (`npx -y ...` — §2) ve secret taşır
> (`EXA_API_KEY`); bu yüzden sürücünün "günlük davranış" bölümü (§7) yalnızca **çağrılmadan
> önce** hangi kapının geçildiğini (onay · skill mandate · context lock) ve **çağrımdan sonra**
> hangi doğrulamanın yapıldığını anlatır. Eklenen her yeni sunucu bir güvenlik yüzeyidir
> (§11) — bu yüzden ekleme akışı ayrı bölümde durur.

## §2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Görev → tool ihtiyacı → sunucu seçimi → çağrım → doğrulama akışı (§3-§6) | MCP protokolünün kendisi (JSON-RPC/spesifikasyon — §19 yalnız kaynak) |
| Tool seçimi karar ağacı: filesystem/exa/browsermcp/chrome-devtools/playwright (§4-§5) | Tool yazımı / sunucu geliştirme (upstream SDK işi) |
| Yeni sunucu ekleme/kaldırma akışı + güvenlik kontrolü (§11-§12) | `settings.json` izin listesi (`.claude/AGENTS.md` sahibi) |
| Arıza fallback zinciri (§9) + context lock ilişkisi (§10) | ADR metinleri (frozen — yalnız okunur) |
| PowerShell doğrulama kalıpları + token ekonomisi (§13-§16) | `.claude/mcp.json` içerik düzenlemesi (bu dosya salt-okunur okuma yapar) |

- **Kullananlar:** görev başına tool çağıran her ajan · MO (sunucu envanteri) · Security (secret/izin denetimi) · kullanıcı (ekleme/kaldırma onayı).
- **Ön koşul:** `.claude/mcp.json` diskte var ve JSON-parse edilebilir (§20 kanıt A).
- **Kanıt standardı:** her iddia `Dosya:` + `Satır:` künyelidir; MCP spec iddiaları URL künyelidir (§18); künyesiz iddia yazılmaz (§8).

## §3. Kanıt Tabanı (2026-10-17 tam okuma)

| # | Kaynak | Ne verdi | Ölçüm |
|---|---|---|---|
| **E1** | `.claude/mcp.json` | **1 sunucu**: `exa-mcp-server` (type local, command `npx -y exa-mcp-server`, env `EXA_API_KEY`) | **9 satır** — JSON-parse OK (§20 kanıt A) |
| **E2** | `.claude/hooks/skill-mandate.cjs` | Hook ↔ skill/MCP ilişkisi: UserPromptSubmit enjeksiyonu + `--check` doğrulayıcı | **195 satır** (§20 kanıt B) |
| **E3** | `.ai/AGENTS.md` §6 + §12 | Routing tablosu (Satır: 230-244) · Context Lock (Satır: 439-472) | 775 satır |
| **E4** | `~/.config/opencode/opencode.jsonc` | **Runtime sunucu envanteri**: exa · filesystem · chrome-devtools · figma (URL tipi) | grep 9 isabet (Satır: 17-45) |
| **E5** | Bu oturum tool kataloğu | Çağrılabilir tool yüzeyi: browsermcp 12 · chrome-devtools 32 · exa 2 · filesystem 14 · opencode 5 · playwright 24 | katalog başlıkları (§5) |

**Kritik ayrım (karıştırılmaz):** `.claude/mcp.json` (Claude Code proje yapılandırması — E1) ile
`opencode.jsonc` (OpenCode runtime yapılandırması — E4) **farklı dosyalardır**. Bugün diskte
Claude tarafında **yalnız exa** tanımlıdır; filesystem/chrome-devtools/playwright/tool isimleri
**bu oturumun runtime kataloğundan** (E5) gelir. İki envanterin senkronu §21/Q1'de açık sorudur —
"her yerde aynı" denmez.

**Secret notu (bağlayıcı):** E1 ve E4 içinde `EXA_API_KEY` ham değer taşır; bu dosyaya **hiçbir
ham anahtar yazılmaz** (§12/4 · kök `AGENTS.md §16.3`). Aşağıdaki JSON alıntısında değer
`REDACTED` ile değiştirilmiştir — yapı birebir, gizli değer yoktur.

## §4. Tool Çağrım Akışı (A-F — adım adım)

| Adım | Ad | Ne olur | Kanıt |
|---|---|---|---|
| **A** | Görev → ihtiyacı ayıkla | Görev cümlesinden ihtiyaç sınıfı çıkar: web arama / dosya oku-yaz / tarayıcı doğrulama / konsol | §4.1 tablo |
| **B** | Karar öncesi kapılar (sıra DEĞİŞMEZ) | 1) Skill eşleşmesi varsa **İLK** Skill tool ile yüklenir (hook enjeksiyonu, E2) · 2) dosya yüzeyi ise domain routing (E3 §6) · 3) eşzamanlı yazım ise context lock (E3 §12) | §7 |
| **C** | Sunucu seçimi | İhtiyaç sınıfı → sunucu karar ağacı (§5). Kural: **en dar araç önce**, eşitlikte native tool (§9) | §5 |
| **D** | Çağrım | Tool adı **katalogdaki birebir** adıyla, args şemaya uygun çağrılır; `execute` içinden `tools.<ns>["tool-name"]` sözdizimi korunur | §19 (spec) |
| **E** | Sonuç doğrulama | `isError`/boş sonuç/gerçeklik kontrolü: sayısal iddia ölçümle, dosya iddiası `Test-Path` ile, URL erişimi fetch ile doğrulanır | §6 |
| **F** | Devam / kapanış | Doğrulandıysa sonucu kullan; doğrulanmadıysa `⚠️ VERIFICATION REQUIRED` işaretle ve zinciri düşür (§9); kapanış listesi (§17) | §8 |

```text
[A] Görev → [B] skill+lock kapıları → [C] sunucu seç → [D] tool çağrımı
   → [E] doğrulama → [F] devam
        doğrulama başarısızsa → [R] fallback (§9): sunucu → native → manuel → escalation
```

**Adım [B] kanıtı (hook ↔ MCP ilişkisi — E2 gerçek alıntı):** `skill-mandate.cjs` her
`UserPromptSubmit` olayında skill isimlerini disktarar ve "Görev bir skill ile eşleşiyorsa
İLK işlem Skill tool ile yüklemektir" cümlesini enjekte eder — yani **MCP çağrımından önce**
skill kapısı gelir:

```javascript
// Dosya: .claude/hooks/skill-mandate.cjs · Satır: 87-89 (birebir alıntı)
'SKILL ZORUNLULUĞU (kök CLAUDE.md §Skill Usage Mandate): Görev bir skill ile eşleşiyorsa',
'İLK işlem Skill tool ile yüklemektir; eşleşen skill\'siz işlem başlatmak YASAKTIR.',
`Proje skill'leri (.claude/skills): ${projectList}.`,
```

```javascript
// Dosya: .claude/hooks/skill-mandate.cjs · Satır: 12 (birebir alıntı — emniyet sözleşmesi)
// * Emniyet: her koşulda exit 0 (prompt asla bloklanmaz); --check hariç.
```

**Hook ↔ MCP sonucu (gerçek çalıştırma, §20 kanıt B):** `node .claude/hooks/skill-mandate.cjs
--check` → `skill-mandate --check: OK (13 proje skill'i temiz)` · exit **0**. Yani hook
düzgün çalışır; hook arızasında prompt **bloklanmaz** (Satır: 12) — bu, MCP fallback zincirinin
de en alt prensibidir: **engelleme yok, düşürme var**.

## §4.1. İhtiyaç Sınıfı → Sunucu Eşlemesi (A adımının kaynağı)

| # | İhtiyaç sınıfı (görev cümlesi ipucu) | Birincil sunucu | Tool yüzeyi (katalog) | Native eşdeğer |
|---|---|---|---|---|
| 1 | "araştır · bul · kaynak doğrula · sayfa oku" | **exa** | `web_search_exa` · `web_fetch_exa` (2 tool) | `websearch` · `webfetch` |
| 2 | "dosya oku · dizin listele · klasör oluştur" | **filesystem** | `list_directory` · `read_file` · `get_file_info` · `create_directory` (14 tool) | `read` · `glob` · `grep` · `write` |
| 3 | "tarayıcıda gez · form doldur · tıkla · key bas" | **playwright** | `browser_navigate` · `browser_press_key` · `browser_close` (24 tool) | — |
| 4 | "DOM incele · network isteği · console log · screenshot" | **chrome-devtools** | `list_pages` · `select_page` · `screencast_start/stop` · `close_page` (32 tool) | — |
| 5 | "hızlı gez + screenshot + console" | **browsermcp** | `browser_navigate` · `browser_go_back/forward` · `browser_get_console_logs` · `browser_screenshot` (12 tool) | — |
| 6 | "oturum/session yönet · model listele · MCP resource" | **opencode** | `session_rename` · `models` · `list_mcp_resources` (5 tool) | — |

**Eşitlik kuralı (3↔4↔5):** gezinme + etkileşim (deterministik) → **playwright**; hata ayıklama
(DOM/network/console profilleme) → **chrome-devtools**; tek seferlik hızlı kontrol →
**browsermcp**. Üçü aynı sayfaya erişirse **tek seferde tek sunucu** seçilir, paralel tarayıcı
çağrımı yapılmaz (context lock ruhu, §10).

## §5. Tool Seçimi Karar Ağacı (görev tipi → sunucu → fallback)

| # | Görev tipi | Seçim | Eşleşmezse | Sonuç |
|---|---|---|---|---|
| 1 | Web araştırma + kaynak okuma | `exa.web_search_exa` → `exa.web_fetch_exa` | native `websearch`/`webfetch` | künyeli özet |
| 2 | Tek URL okuma (biliniyor) | `exa.web_fetch_exa` (batch) | `webfetch` | sayfa metni |
| 3 | Vault/repo dosyası okuma | `filesystem.read_file` | native `read` | dosya içeriği + künye |
| 4 | Dosya listeleme / pattern arama | `filesystem.list_directory` | native `glob`/`grep` (`.ai/**` → `hidden: true`) | dosya listesi |
| 5 | Yeni dosya/klasör yazımı | native `write` (filesystem.write yoksa) | `filesystem.create_directory` + manuel | yazım + ölçüm |
| 6 | Eşzamanlı yazılan dosya yüzeyi | **context lock** (§10) → sonra tek yazıcı | escalation | tek yazıcı garantisi |
| 7 | UI doğrulama (ekran görüntüsü) | `playwright.browser_navigate` | `browsermcp.browser_navigate` + screenshot | görsel doğrulama |
| 8 | UI hata ayıklama (console/network) | `chrome-devtools` (sayfa seç → incele) | `browsermcp.browser_get_console_logs` | log kanıtı |
| 9 | Tarayıcı geri/ileri gezinme | `playwright.browser_navigate_back` | `browsermcp.browser_go_back` | sayfa durumu |
| 10 | Figma/MCP resource erişimi | `opencode.list_mcp_resources` | `⚠️ VERIFICATION REQUIRED` | resource listesi |
| 11 | Görev bir skill ile eşleşiyor | **Skill tool önce** (§4 [B]) | — | skill yüklenmiş ≻ MCP |
| 12 | Hiçbir sunucu bu işi yapamıyor | native tool (shell/read/write) | manuel → escalation (§9) | dürüst durum |

**Boşluk kaydı (uydurulmaz):** `.claude/mcp.json`'da **filesystem/playwright/chrome-devtools/
browsermcp/figma TANIMLI DEĞİL** (E1: 1 sunucu); bu sunucular yalnız **runtime kataloğu**
(E4/E5) üzerinden görünür. Tablo "proje yapılandırmasında var" demez — "çağrılabilir yüzeyde
görünüyor" der (§21/Q1).

## §6. Çağrım Sonrası Doğrulama (sonuç gerçek mi — hallucination riski)

| # | Doğrulama | Yöntem | İhlal sonucu |
|---|---|---|---|
| 1 | Tool gerçekten döndü mü | Boş/`isError` kontrolü (spec: tool execution error `isError: true`, §19) | Sonuç yok sayılır, zincir düşer (§9) |
| 2 | Sayısal iddia | **Ölçüm** ile teyit (`ReadAllLines`/`Count`) — MCP çıktısı ölçüm değildir | `UNKNOWN` |
| 3 | Dosya/dizin iddiası | `Test-Path` / `glob` ile disk kontrolü | Hayalet iddia — `⚠️ VERIFICATION REQUIRED` |
| 4 | URL/API iddiası | Gerçekten fetch edildi mi (erisim kanıtı) | Erişilemediyse "çalışıyor" DENMEZ |
| 5 | Tool args doğru mu | Şema ile uyum (`inputSchema`); yanlış argüman → JSON-RPC hatası (§19) | Düzelt → yeniden çağır (max 3) |
| 6 | Doğru tool çağrıldı mı | Ad birebir katalog adı; `search` gibi çakışan adlarda sunucu öneki (§19) | Yanlış araç → yanlış sonuç |
| 7 | Küne yazıldı mı | Çağrım + sonucu `Dosya:`/URL künyesiyle raporlanır | Kanıtsız iddia (§8) |

**Kural (bağlayıcı):** MCP tool çıktısı **araç çıktısıdır, doğrulanmış gerçek değildir**.
Aynı kanıt hiyerarşisi (§18): disk/ölçüm > fetch edilmiş URL > tool çıktısı > model hafızası.
Tool "dosya var" dediyse sürücü yine `Test-Path` ile teyit eder.

## §7. Tool Çağrımı Öncesi Kapılar (sıra bağlayıcı)

| # | Kapı | Ne zaman | Kaynak |
|---|---|---|---|
| 1 | **Skill Usage Mandate** | Her görevde, MCP çağrımından ÖNCE — eşleşen skill varsa ilk işlem o | E2 · Satır: 87-89 |
| 2 | **Domain routing** | Kod yüzeyi görevlerinde (PHP/CSS/SQL...) → ilgili agent'a gider, tool çağrımı routing SONRA | E3 §6 · Satır: 230-244 |
| 3 | **Context lock** | Eşzamanlı dosya erişimi varsa (filesystem ile yazım) | E3 §12 · Satır: 439-472 |
| 4 | **Onay kapısı** | MCP/skill/ayar değişikliği yapacaksa (ekleme/kaldırma) | `.claude/AGENTS.md` · Satır: 30, 91 |
| 5 | **Mockup gate** | UI işinde `.ai/ui-design/**` görseli okunmadan kod yazılamaz | E3 §13 |

**Anti-pattern:** skill varken doğrudan MCP tool'u çağırmak. Hook bunu enjeksiyonla önler;
yine de ajan sırayı atlar sa **adım [B] ihlali**dir ve sonuç red edilir (§16/1).

## §8. Durum Etiketleri (MCP çıktısı için)

| Etiket | Anlamı | Ne zaman kullanılır |
|---|---|---|
| `OK` | Tool çağrımı成功 + doğrulama geçti | `isError` false + teyit yapıldı |
| `DEGRADED` | Tool döndü ama doğrulama eksik/şüpheli | Kısmi sonuç — `⚠️ VERIFICATION REQUIRED` eklenir |
| `FALLBACK` | Sunucu arızalı → native tool'a düşüldü | §9 zinciri |
| `MANUEL` | Hiçbir araç yapamadı → elle/insan adımı | §9 son kadem |
| `UNKNOWN` | Değer/sonuç bilinmiyor | Ölçülmeyen iddia |
| `⚠️ VERIFICATION REQUIRED` | İddia var, doğrulama yok | Zero-Hallucination (§18) |

## §9. Arıza Fallback Zinciri (sunucu yok → native → manuel → escalation)

| Kademe | Ne denenir | Ne zaman | Çıkış |
|---|---|---|---|
| **F0** | Skill mandate kontrolü (belki iş skill'dedir, tool'a gerek yok) | her zaman ilk | skill çözümü |
| **F1** | Birincil sunucu + tool çağrımı (§4 [D]) | normal akış | sonuç / F2 |
| **F2** | Aynı işi gören **ikinci sunucu** (playwright ↔ browsermcp ↔ chrome-devtools; exa ↔ webfetch) | birincil timeout/hata | sonuç / F3 |
| **F3** | **Native tool** (`read`/`write`/`glob`/`grep`/`shell`/`webfetch`) | sunucu yok ya da `npx` başarısız | sonuç / F4 |
| **F4** | **Manuel**: kullanıcıya tek kısa soru ya da elle komut | araç yüzeyi yetmedi | insan adımı |
| **F5** | **Escalation** — MO/L1 domain lead → L2 → L3 → insan | 3 başarısız tur | üst karar |

**Kurallar:**
- Max **3 retry** aynı tool için (timeout/yapılandırma hatası) — sonra zincir düşer (E3 §8 · §18/2).
- Zincir düşerken **engelleme yok** (hook emniyeti, E2 Satır: 12 ruhu): iş durmaz, **yöntem** değişir.
- F5'te **şüpheli varsayım yazılır**; tahminle devam edilmez (3 başarısız düzeltme turu → DUR).
- Her düşüş `FALLBACK`/`MANUEL` olarak raporlanır (§8) — sessiz yöntem değişimi yasak.

## §10. MCP + Context Lock (filesystem sunucusu ile eşzamanlı yazım riski)

**Risk:** `filesystem` sunucusu (14 tool, E5) ile native `write`/`edit` ve `shell` **aynı dosyaya**
yazabilirse yarış durumu (torn write) doğar. Bağlayıcı mekanizma `.ai/AGENTS.md` §12'dir
(Satır: 439-472).

| # | Kural | Değer | Kaynak |
|---|---|---|---|
| 1 | Kilitleme süresi | Max 30 saniye | E3 · Satır: 445 |
| 2 | Deadlock prevention | MO en eski kilidi kırar | E3 · Satır: 448 |
| 3 | Öncelik | CRITICAL > HIGH > MEDIUM > LOW | E3 · Satır: 447 |
| 4 | Logging | Lock acquire/release `log.md`'ye yazılır | E3 · Satır: 449 |
| 5 | Pre-flight | "Dosya etkileniyor mu? → Context lock" | E3 · Satır: 273 |

```text
Ajan dosyaya erişmek ister
  → Lock acquire (max 30s bekleme)
    → Başarılı → dosyayı DÜZENLE (tek yazıcı)
    → Başarısız → kuyruk → öncelik sırası
      → Timeout → escalation
```

**Sürücü kuralı (filesystem + native eşzamanlılığı):**

| # | Durum | Davranış |
|---|---|---|
| 1 | Tek yazıcı, okuma yok | Lock gerekmez; okuma serbest (E3 §5 — `.ai/` okuma serbest) |
| 2 | Aynı dosyaya 2+ yazıcı | **Lock acquire** (F5 ruhu) — kilidi alamayan bekler/kuyruğa girer |
| 3 | filesystem sunucusu ile native `write` aynı anda | **YASAK** — tek yazıcı seçilir (filesystem.write yoksa native write tek başına) |
| 4 | MO müdahale | En eski kilit kırılır (§12.3) — kırılma log'a yazılır |
| 5 | Kilit bilgisi yoksa | `UNKNOWN` — kilit mekanizmasının diskte uygulaması bu üretimde kanıtlanmadı (§21/Q3) |

## §11. Yeni Sunucu Ekleme Akışı (mcp.json + onay)

| Adım | Ne yapılır | Kanıt/kapı |
|---|---|---|
| 1 | **İhtiyaç gerekçesi** yazılır (hangi tool yüzeyi eksik, hangi görev tipi) | §4.1 boşluğu |
| 2 | **Güvenlik denetimi**: paket kaynağı (resmi `@modelcontextprotocol/*` mı?), lisans, secret ihtiyacı | §12 kontrol listesi |
| 3 | **Onay alınır** — araç yapılandırması kullanıcı onayı ister, onaysız değişiklik YOK | `.claude/AGENTS.md` · Satır: 30, 91 |
| 4 | `mcp.json` düzenlenir: sunucu adı + `type` + `command`/`url` + `environment` | §2 iskelet |
| 5 | **JSON-parse doğrulaması** (bozuk JSON tüm yapılandırmayı düşürür) | §20 kanıt A |
| 6 | `--check`/sağlık betiği + envanter sayacı yeniden ölçülür | §20 |
| 7 | Runtime'da tool listesi tazelenir (`tools/list` / katalog yenileme) | §19 |
| 8 | Kayıt: envanter + gerekçe vault'a (MO) | §17 |

**İskelet (gerçek E1 yapısı — secret REDACTED):**

```json
{
  "exa-mcp-server": {
    "type": "local",
    "command": ["npx", "-y", "exa-mcp-server"],
    "environment": {
      "EXA_API_KEY": "REDACTED — ham değer .claude/mcp.json Satır 6'dadır, .md'ye yazılmaz"
    }
  }
}
```

**Not (upstream, §18):** uzak sunucu `type` + `url` ile tanımlanır (ör. figma — E4 Satır: 45
`"url": "https://mcp.figma.com/mcp"`); yerel sunucu `command` + `environment` ile (stdio).
İkisi aynı JSON'da birlikte bulunabilir.

## §12. Sunucu Ekleme Öncesi Güvenlik Kontrol Listesi

| # | Kontrol | Kriter | Reddedilme nedeni |
|---|---|---|---|
| 1 | Paket kaynağı | Resmi/ bilinen yayıncı; `npx -y` sürümü sabitlenebilir mi? | Bilinmeyen yayıncı → karantina |
| 2 | Secret yüzeyi | Hangi anahtar gerekli? `.env`/env substitution ile mi? | Ham anahtar `.md`/`.log` yasağı (§3) |
| 3 | Yetki kapsamı | Tool yüzeyi görev için gerekli mi? (en dar yetki) | Fazla yetki → red |
| 4 | Yazma yetkisi | filesystem sunucusu hangi klasörlere yazabilir? | Vault yüzeyine yazma → kısıtla |
| 5 | Lisans uyumu | OSS lisansı projeyle uyumlu | Uyuşmazlık → red |
| 6 | Onay | Kullanıcı onayı alındı mı? | Onaysız ekleme → geri alma (§3.3/#2) |
| 7 | Karantina | 3. taraf kod kendi klasöründe/isminde | Vendor karışması (`.claude/AGENTS.md` · Satır: 123) |

## §13. Sunucu Kaldırma Akışı

| Adım | Ne yapılır | Dikkat |
|---|---|---|
| 1 | Kullanım kanıtı: bu sunucuyu çağıran görev var mı? (envanter) | Kullanılmayan = aday kaldırma |
| 2 | Onay alınır | Onaysız kaldırma da yasak (§3.3/#2) |
| 3 | `mcp.json` girdisi silinir | Yalnız o sunucu bloğu — diğerleri korunur |
| 4 | JSON-parse + envanter yeniden ölçülür | Kalan sunucu sayısı doğru mu |
| 5 | Fallback hazırlığı: o iş bundan sonra hangi araçla yapılacak? (§9) | Kaldırma → boşluk bırakmaz |
| 6 | Kayıt + secret temizliği: sunucuya ait anahtar artık gereksizse devre dışı | Anahtar sızıntısı yüzeyi küçülür |

## §14. PowerShell Doğrulama Kalıpları (mcp.json sağlığı)

> ✅ **Çalıştırılabilir PowerShell betiği** — repo kökünden (`C:\www\coremusic.net`) çalışır;
> sayılar 2026-10-17 gerçek ölçümüdür (§20 kanıt A).

```powershell
# A) mcp.json sağlığı: parse + sunucu sayısı + env anahtar anahtarları (değer YAZILMAZ)
$p = '.claude\mcp.json'
try {
    $j = Get-Content $p -Raw -Encoding UTF8 | ConvertFrom-Json
    $names = $j.PSObject.Properties.Name
    "json-parse => OK"
    "sunucu sayisi => {0}" -f $names.Count
    foreach ($n in $names) {
        "sunucu => {0} | type={1} | command={2}" -f $n, $j.$n.type, ($j.$n.command -join ' ')
        "env-keys => {0}" -f (($j.$n.environment.PSObject.Properties.Name) -join ',')
    }
} catch { "json-parse => FAIL: $($_.Exception.Message)" }
# 2026-10-17 cikti: json-parse => OK · sunucu sayisi => 1
#   sunucu => exa-mcp-server | type=local | command=npx -y exa-mcp-server
#   env-keys => EXA_API_KEY
```

```powershell
# B) Hook-MCP iliskisi saglik kontrolu (skill-mandate --check, exit 0 = temiz)
node .claude\hooks\skill-mandate.cjs --check
"exit => $LASTEXITCODE"
# 2026-10-17 cikti: skill-mandate --check: OK (13 proje skill'i temiz) · exit => 0
```

## §15. Token Ekonomisi (tool çağrım bütçesi)

| # | Kural | Sonuç |
|---|---|---|
| 1 | Toplu gereksiz çağrım yasak — her tool çağrımı budgettur | İsraf önlenir |
| 2 | Skill önce, MCP sonra (§7/1) — belki tool hiç gerekmez | Çağrım sayısı düşer |
| 3 | En dar tool önce (`read_file` vs tüm klasör tarama) | Küçük sonuç, az token |
| 4 | Batch okuma (exa `urls: [...]` array) — tek çağrıda çok URL | Çağrım başına verim |
| 5 | Aynı tool'u 3 kez tekrarlama (retry cap) — zincir düşür (§9) | Sonsuz döngü yok |
| 6 | Tool çıktısını özetle, ham uzun çıktıyı tampona geri yazma | Bağlam şişmesi yok |

## §16. Hata Modları

| # | Hata | Semptom | Tepki |
|---|---|---|---|
| 1 | Skill atlanıp MCP çağrıldı | Hook ihlali | Adım [B]'ye geri dön (§4) |
| 2 | Yanlış tool adı | JSON-RPC "Unknown tool" | Katalogdaki birebir adla tekrar (§19) |
| 3 | Şemaya aykırı args | JSON-RPC -32602 | Args düzelt → max 3 retry |
| 4 | Tool `isError: true` döndü | İşlem hatası | Düzeltilebilir mi bak; değilse zincir düşür (§9) |
| 5 | Sunucu `npx` başlatılamıyor | Timeout / boş tool listesi | F2/F3 native'e düş |
| 6 | Ham secret `.md`'e yazıldı | Sızıntı yüzeyi | **DUR** — anahtar iptali (§12/2) |
| 7 | 2+ yazıcı aynı dosyada | Torn write | Context lock (§10) |
| 8 | Tool çıktısı doğrulandı sanıldı | Hallüsinasyon | §6 doğrulaması zorunlu |
| 9 | İki envanter (E1↔E4) çelişti | "var" sanılan sunucu çağrılamıyor | §21/Q1 — `⚠️ VERIFICATION REQUIRED` |
| 10 | 3 başarısız tur | Aynı hata tekrarı | DUR — şüpheli varsayımı yaz, üst karar |

## §17. Doğrulama Kapıları

| # | Kapı | Kriter | Yöntem |
|---|---|---|---|
| 1 | Frontmatter | 10 alan eksiksiz | §20 FM sayacı |
| 2 | Satır sayısı | `>= 500` | `ReadAllLines` (§14 benzeri) |
| 3 | Authority son satır | birebir | §20 eşitlik kontrolü |
| 4 | `mcp.json` parse | OK + sunucu sayısı ölçülü | §14/A |
| 5 | Hook sağlığı | `--check` exit 0 | §14/B |
| 6 | Secret | Ham `EXA_API_KEY` bu dosyada YOK | `Select-String` taraması |
| 7 | Placeholder | `{{` yok | `Select-String -Pattern '\{\{'` → 0 |
| 8 | Envanter tutarlılığı | E1 (1 sunucu) ile E4/E5 ayrımı yazıldı mı | §3 |
| 9 | Künye disiplini | Her tool iddiası URL/Dosya künyeli | gözden geçirme |
| 10 | Kapsam dışı yazım | 0 dosya | git status sayacı |

## §18. Kaynaklar (disk + upstream)

| Kaynak | Yol/URL | Kullanım |
|---|---|---|
| Proje MCP yapılandırması | `.claude/mcp.json` (9 satır) | E1 — 1 sunucu envanteri |
| Hook | `.claude/hooks/skill-mandate.cjs` (195 satır) | E2 — skill↔MCP ilişkisi |
| Agent registry (SSOT) | `.ai/AGENTS.md` §6 (Satır: 230-244) · §12 (Satır: 439-472) | E3 — routing + context lock |
| Runtime yapılandırma | `~/.config/opencode/opencode.jsonc` (Satır: 17-45) | E4 — exa/filesystem/chrome-devtools/figma |
| Tool kataloğu | Bu oturum (E5) | Çağrılabilir tool yüzeyi sayıları |
| `.claude` kuralı | `.claude/AGENTS.md` (Satır: 30, 44, 77, 91, 123) | Onay kapısı + vendor karantinası |
| MCP Spec — Transports | `https://modelcontextprotocol.io/specification/2025-06-18/basic/transports` | stdio · Streamable HTTP · JSON-RPC UTF-8 |
| MCP Docs — Tools | `https://modelcontextprotocol.io/docs/concepts/tools` | `tools/list` · `tools/call` · `isError` · `inputSchema` |
| MCP Docs — Architecture | `https://modelcontextprotocol.io/docs/concepts/architecture` | host/client/server · primitives · tools/resources/prompts |

> **Web doğrulama (2026-10-17):** 3 resmi `modelcontextprotocol.io` sayfası erişilip okundu
> (transports · tools · architecture). Diğer tüm iddialar disk üzerindedir (E1-E5).

## §19. MCP Sözleşme Özeti (upstream — künyeli, §18)

| Kavram | Ne der | İlgili bölüm |
|---|---|---|
| Katman | Veri katmanı JSON-RPC 2.0; taşıma katmanı stdio / Streamable HTTP | §11 |
| `stdio` | Yerel sunucu alt süreç olarak açılır; `stdin`/`stdout` mesaj kanalı | §11 iskelet |
| `tools/list` | Sunucunun tool listesi keşfi (`name` · `description` · `inputSchema`) | §4.1 · §5 |
| `tools/call` | `params.name` + `params.arguments` çağrımı | §4 [D] |
| `inputSchema` | Argümanların JSON Schema doğrulaması — ihlalinde JSON-RPC hatası | §16/3 |
| Tool execution error | `isError: true` + `content` — model düzeltebilir | §6/1 · §9 |
| `resources`/`prompts` | Tool dışındaki bağlam/primitifler | §18 |
| Güvenlik | Sunucu girdi doğrular; client hassas işlemde onay ister · `x-mcp-header` gizli parametre için yasak | §12 |

## §20. Kanıt Blokları (gerçek okuma — uydurma yok)

> ✅ **Gerçek disk kanıtı.** Aşağıdaki alıntılar `.claude/mcp.json` (E1) ve hook'tan (E2)
> birebir alınmıştır; gizli anahtar kırpılmıştır.

**Kanıt A — `.claude/mcp.json` (9 satır, tam okuma):**

```json
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

**Kanıt B — `.claude/hooks/skill-mandate.cjs` (Satır: 87-89) — skill kapısı MCP'den önce:**

```javascript
// Dosya: .claude/hooks/skill-mandate.cjs · Satır: 87-89 (birebir)
'SKILL ZORUNLULUĞU (kök CLAUDE.md §Skill Usage Mandate): Görev bir skill ile eşleşiyorsa',
'İLK işlem Skill tool ile yüklemektir; eşleşen skill\'siz işlem başlatmak YASAKTIR.',
```

**Kanıt C — `.ai/AGENTS.md` §12.2 Lock Flow (Satır: 452-460) — birebir:**

```text
Ajan dosyaya erişmek ister
  → Lock acquire (max 30s bekleme)
    → Başarılı → dosyayı düzenle
    → Başarısız → kuyruk → öncelik sırası
      → Timeout → escalation
```

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
# MCP tool router iskeleti (illüstratif — upstream SDK ruhu)
SERVERS = {
    "research": ["exa.web_search_exa", "exa.web_fetch_exa"],
    "files":    ["filesystem.read_file", "filesystem.list_directory"],
    "browser":  ["playwright.browser_navigate", "chrome-devtools.list_pages"],
}

def route(need: str) -> str:
    if need in ("research", "fetch"):  return SERVERS["research"][0]
    if need in ("read", "list"):       return SERVERS["files"][0]
    if need in ("navigate",):          return SERVERS["browser"][0]
    raise LookupError("sunucu yok → fallback zinciri (§9)")
```

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```typescript
// Fallback zinciri iskeleti (illüstratif)
type Tier = 'skill' | 'server' | 'second' | 'native' | 'manual' | 'escalate';
const CHAIN: Tier[] = ['skill', 'server', 'second', 'native', 'manual', 'escalate'];

export async function run(task: () => Promise<unknown>): Promise<Tier> {
  for (const tier of CHAIN) {           // F0 → F5 (§9)
    try { await task(); return tier; }  // engelleme yok, yöntem düşer
    catch { continue; }
  }
  return 'escalate';                    // DUR + üst karar
}
```

## §21. Açık Sorular

| # | Soru | Durum |
|---|---|---|
| Q1 | `.claude/mcp.json` (1 sunucu — exa) ile `opencode.jsonc` runtime envanteri (exa·filesystem·chrome-devtools·figma) neden senkron değil? Hangisi bağlayıcı? | `⚠️ VERIFICATION REQUIRED` |
| Q2 | `depends_on: [mcps-anayasa]` — `mcps/` altında şu an yalnız `index.md` + bu dosya var; `mcps-anayasa` diskte mi? | `⚠️ VERIFICATION REQUIRED` |
| Q3 | Context lock mekanizmasının **diskte uygulaması** (kilit dosyası/servisi) var mı, yoksa yalnız `.ai/AGENTS.md` §12'de tarif mi? | `⚠️ VERIFICATION REQUIRED` (kanıt yok) |
| Q4 | `filesystem` sunucusunun yazma izni hangi klasörlere kısıtlı (vault yüzeyi açık mı)? | `⚠️ VERIFICATION REQUIRED` |
| Q5 | figma sunucusu `url` tipinde (E4) — Streamable HTTP; `.claude/mcp.json`'a taşınacak mı? | kullanıcı onayı |
| Q6 | playwright/browsermcp **runtime'da** görünüyor ama `.claude/mcp.json`'da yok — proje yapılandırılmasına eklenecek mi? | §11 ekleme akışı + onay |

## §22. Hızlı Eşleme — Görev İfadesi → İlk Çağrı (sürükleyici özet)

> Bu tablo §5 karar ağacının **hızlı kopyasıdır**; ayrışma olursa §5 kazanır.

| Görev ifadesi (ipucu) | İlk çağrı | Sunucu |
|---|---|---|
| "araştır · bul · kaynak doğrula" | `web_search_exa` | exa |
| "şu URL'yi oku · sayfayı çek" | `web_fetch_exa` (batch) | exa |
| "dosya oku · içerik çek" | `read_file` | filesystem |
| "dizin listele · dosya bul" | `list_directory` / `read_file` | filesystem |
| "yeni dosya yaz" | `write` (native) | native |
| "sayfayı aç · gez · tıkla" | `browser_navigate` | playwright |
| "console/network hatası" | `list_pages` → `select_page` | chrome-devtools |
| "hızlı ekran görüntüsü" | `browser_screenshot` | browsermcp |
| "hangi MCP resource var" | `list_mcp_resources` | opencode |
| "skill ile eşleşen iş" | Skill tool (önce) | skill |

## §23. Sunucu Profilleri (ne yapar · ne YAPMAZ)

| Sunucu | Tip | Güçlü yön | Yapmaz |
|---|---|---|---|
| **exa** | local (`npx`) | Web arama + fetch (batch) | Dosya/tarayıcı |
| **filesystem** | runtime | Oku · listele · klasör oluştur | Vault'a yazım yetkisi kısıtsız değil (Q4) |
| **playwright** | runtime | Deterministik gezinme/etkileşim | DOM/network profilleme |
| **chrome-devtools** | runtime (`--isolated`) | DOM · network · console · screencast | Form etkileşimi (playwright işi) |
| **browsermcp** | runtime | Hızlı gez + screenshot + console | Derin profil |
| **opencode** | built-in | Session · models · MCP resource | Dosya/tarayıcı işi |

## §24. Sık Yapılan Hatalar (anti-pattern)

| # | Yanlış | Doğru |
|---|---|---|
| 1 | Skill varken doğrudan MCP çağırmak | Skill önce (§7/1) |
| 2 | Tool çıktısını doğrulanmış gerçek saymak | §6 doğrulaması zorunlu |
| 3 | 3 sunucuyu aynı sayfada paralel çağırmak | Tek seferde tek sunucu (§4.1) |
| 4 | Onaysız sunucu eklemek/kaldırmak | Kullanıcı onayı (§11/3) |
| 5 | Ham anahtar `.md`'ye alıntılamak | REDACTED (§3 · §12/2) |
| 6 | Retry'yı sınırsız tekrarlamak | Max 3 → zincir düşer (§9) |
| 7 | 2+ yazıcıyı kilitsiz bırakmak | Context lock (§10) |
| 8 | E1↔E4 envanteri "aynı" sanmak | Ayrım yazılır (§3 · Q1) |

## §25. Kapanış Kontrol Listesi

| # | Soru | Zorunlu cevap |
|---|---|---|
| 1 | Adım [B] kapıları izlendi mi (skill → routing → lock)? | evet |
| 2 | Doğrulama (§6) yapıldı mı? | evet |
| 3 | Fallback gerektiyse zincir (§9) işletildi mi? | evet/gerekmedi |
| 4 | Ham secret yazıldı mı? | **hayır (0)** |
| 5 | Ölçüm yapıldı mı (§14 · §17)? | evet |
| 6 | Kapsam dışı dosyaya yazıldı mı? | **hayır** |
| 7 | `git commit` atıldı mı? | **hayır (orkestratöre ait)** |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
