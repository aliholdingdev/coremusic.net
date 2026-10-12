---
title: "CoreMusic — .opencode Context"
type: docs
category: opencode
date: 2026-10-03
updated: 2026-10-12
version: 2.0.0
status: active
authority: "Derived — SSOT: .ai/ + kök AGENTS.md"
docType: context
---

# CoreMusic — .opencode Context

**docType:** context · **Klasör:** `.opencode/` · **Sorumlu:** MO (vault-updater)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[../AGENTS.md]]

---

## 1. Amaç

`.opencode/`, OpenCode V2 aracının bu projedeki çalışma ortamıdır: anayasa kopyası `CLAUDE.md`, rol/routing dosyası `AGENTS.md`, süreç dosyası `WORKFLOW.md`, yapılandırma `opencode.json` (+ 6 yedek), paket kilidi `package.json` / `package-lock.json`, alt dizinler `agents/` (17 subagent tanımı), `command/` (10 slash komutu), `.workflows/` (3 akış reçetesi), `rules/` (boş) ve `node_modules/` (kurulu paketler). Bu doküman klasörün disk envanterini (14 kök dosya, 5 alt dizin — ölçüm 2026-10-12), boot/otorite ilişkisini ve eski nüsha ile disk arasındaki çelişkileri kanıtlarıyla tanımlar; kod üretmez, yalnız okunur ve ölçümle güncellenir.

Not: Önceki nüshanın (2026-10-03) mimari katman/K-space atıfları bu revizyonda tamamen kaldırıldı; yerine yenisi üretilmedi (grep ölçümü 2026-10-12: eski dosyada K-space/`architecture/` izi 0 eşleşme).

| Karar | Kaynak (disk) |
|-------|---------------|
| Envanter: 14 kök dosya, 5 alt dizin | kök dizin listesi ölçümü (gizli dahil, 2026-10-12) |
| `skills/` kapandı — envanterden çıkarıldı | `skills/` diskte YOK; tek ev `.claude/skills` (kayıt: kök `CLAUDE.md` §Skill Registry, kapanış 2026-10-07) |
| `agents/` yeni dizin — 17 subagent tanımı | `agents/` glob ölçümü (2026-10-12) |
| `node_modules/` 3.667 dosya / 270 dizin | recursive sayım (2026-10-12) |
| `opencode.json` içeriği okunmadı | 44.643 bayt JSON — yalnız boyut ölçüldü, anahtar envanteri `VERIFICATION REQUIRED` |
| Paket bağımlılığı tek: `@opencode-ai/plugin` 1.18.13 | `package.json` (3 satır, okundu 2026-10-12) |
| Boot zinciri: kök → `.opencode/` → `.ai/` (ihtiyaç anında) | [[CLAUDE.md]] + kök [[../AGENTS.md]] §7 |

> **eli10 (basit):** Bu klasör, yapay zekâ aracının çalıştığı oda: ayarlar, kural özeti, hazır komutlar ve oturum reçeteleri burada durur.
> **eli15 (detay):** Ayrı klasördür çünkü çalışma ortamı kurulumu projenin kendi verisinden farklı hızda değişir; kod içine gömülse araç güncellemesi kodu kirletir. İçine yapılandırma, kural özeti, subagent/komut tanımları ve akış dosyaları girer. Aracın her oturumunda okunur. Araç sürümü ya da komut değiştiğinde düzenlenir; proje kuralı değil, araç kuralıdır değişen.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `.opencode/` kök envanteri (14 dosya) + 5 alt dizin (`agents/` `command/` `.workflows/` `rules/` `node_modules/`) | `.ai/` vault içeriği (SSOT; yalnız ihtiyaç anında okunur) |
| Boot okuma sırası ve otorite (frontmatter) ilişkisi | `opencode.json` içeriğinin anahtar/dğer envanteri (okunmadı → `VERIFICATION REQUIRED`) |
| Eski nüsha ↔ disk çelişki kaydı (disk kazanır) | `node_modules/` paket içi dosyalar (kurulu paket — okunmaz, üretilmez) |
| `package.json` bağımlılık kanıtı | Proje kodu (`*.php`, `*.js`, `*.css`, `*.sql`) — ilgili katmanın işi |

- **Kullananlar:** OpenCode (boot okuyucu), MO (bu doküman + envanter güncellemesi), Security (`opencode.json` sır denetimi), DevOps (paket/lock), tooling (command/agents üretimi).
- **Ön koşul:** Klasör diskte mevcut ve envanter bu oturumda ölçülmüş; kanıt olmayan sayısal iddia `UNKNOWN` taşır.
- **Not:** `docType: context` = bilgi ağırlıklı; bu dosya kural koymaz, kural için [[CLAUDE.md]] ve kök [[../AGENTS.md]]'e gönderir.

> **eli10 (basit):** Burada yalnız bu klasörün dosyaları ve sayıları anlatılır; kural metni ve vault içeriği konu dışıdır.
> **eli15 (detay):** Kapsam ayrı çizildi ki envanter ile kural karışmasın: buradaki sayılar ölçülmüştür, kurallar ise kök ve vault dosyalarındadır. Aracı kuran ve güncelleyen kişi burayı okur. Kapsam yazılmazsa bu dosya ikinci bir kural setine dönüşür ve SSOT dağılır.

---

## 3. Mimari

### 3.1 Kök Dosya Envanteri (depth 0 — ölçüm 2026-10-12: 14 dosya)

| Katman/Dosya | Ne için kullanılır | Neyden oluşur | Neden var | Ne zaman düzenlenir |
|--------------|--------------------|---------------|-----------|---------------------|
| `CLAUDE.md` (49.089 bayt, 946 satır, v27.3.4) | Araç anayasası — kural özeti, boot, yasaklar (MEVCUT — dokunulmaz) | Frontmatter (authority: SSOT — kendi iddiası) + bölüm başlıkları (MAX THINKING, Purpose, Scope, Rules, Workflow, Validation, References) | Araç her oturumda kuralları tek yerden okusun | Yalnız vault-sync (MO) |
| `AGENTS.md` (23.859 bayt, 313 satır, v1.0.0, docType: agents) | Bu klasörde rol, yetki sınırı ve routing | 7 bölümlü agent iskeleti — rol tablosu, matris, handover, escalation | Kimin hangi dosyaya dokunacağı baştan belli olsun | Agent registry revizyonunda (MO) |
| `WORKFLOW.md` (22.794 bayt, 307 satır, v1.0.0, docType: workflow) | Adım → kapı → rapor süreç dosyası | 7 bölümlü workflow iskeleti (authority: Derived — SSOT `.ai/WORKFLOW.md`) | Onaysız değişiklik yapılmasın | Süreç değişince (MO) |
| `CONTEXT.md` (bu dosya; eski nüsha 24.279 bayt / 311 satır) | Klasör envanteri + çelişki kaydı | 7 bölüm + 5 sütunlu envanter tablosu + eli10/eli15 blokları | Sayılar tek yerde, ölçümle dursun | Envanter ölçümü değişince |
| `opencode.json` (44.643 bayt; değişiklik 2026-10-11) | Araç yapılandırması — model, provider, MCP, plugin, reasoning | JSON anahtarları (içerik bu oturumda OKUNMADI → `VERIFICATION REQUIRED`) | Araç davranışı kod olmadan ayarlansın | Araç/servis yapılandırması değişince (Security + MO onayı) |
| `opencode.json.bak` · `.bak-faz2` · `.bak-mcp` · `.bak-senior` · `.bak-skillfix` · `.bak-thinking` (6 dosya, 29.378–37.506 bayt) | Yapılandırma yedeği (faz/sürüm geçmişi: 2026-08-15 → 2026-10-01 arası) | JSON kopyaları | Yanlış değişiklikten geri dönülebilsin | Yedekleme işinde — değiştirilmez/silinmez |
| `package.json` (65 bayt, 3 satır) | Paket tanımı — `@opencode-ai/plugin` `1.18.13` | 2 satır JSON (dependencies) | Eklenti sürümü sabitlensin | Eklenti sürüm değişikliğinde (DevOps) |
| `package-lock.json` (14.632 bayt) | Kurulum kilidi | Bağımlılık ağacı | Kurulum tekrarlanabilir olsun | Yalnız `npm install` çıktısı — elle düzenlenmez |
| `.gitignore` (63 bayt, 5 satır) | Git dışında tutulanlar | 5 yoksayma kuralı: `node_modules`, `package.json`, `package-lock.json`, `bun.lock`, `.gitignore` | Kurulu paket/yedek commit edilmesin | Yeni yoksayma eklenince |

**`CLAUDE.md`**
> **eli10 (basit):** Bu odada çalışırken uyulacak temel kuralların yazılı olduğu kural kitabı.
> **eli15 (detay):** Ayrı dosyadır çünkü araç kuralı proje kodundan farklı hızda değişir; kod içine gömülse her sürümde revize edilir. Oturum başında okunur, yasakları ve okuma sırasını verir. Yalnız vault-sync (MO) tarafından düzenlenir. Elle değişilirse boot zinciri ve SSOT hiyerarşisi bozulur.

**`AGENTS.md`**
> **eli10 (basit):** Bu klasörde kimin hangi dosyaya dokunabileceğini yazan rol defteri.
> **eli15 (detay):** Rol ile kural farklı hızda değişir; kural dosyasına gömülse her revizyonda rol aranır. Göreve başlarken okunur, yetki sınırını ve routing'i verir. Agent registry revizyonunda MO günceller. Sahip belirsiz olunca herkes düzenler ve çatışma doğar.

**`WORKFLOW.md`**
> **eli10 (basit):** Bir görevin kapı kapı nasıl yürütüleceğini anlatan adım listesi.
> **eli15 (detay):** Süreç ile dosya içeriği farklı hızda değişir; kurala gömülse her revizyonda süreç aranır. İşe başlarken okunur, onay ve ölçüm kapılarını önceden gösterir. Süreç değişince MO günceller. Kapı atlanırsa onaysız değişiklik doğrudan işe girer.

**`CONTEXT.md` (bu dosya)**
> **eli10 (basit):** Klasörün kaç dosya içerdiğini ve eski bilgilerle disk arasındaki farkları yazan envanter kaydı.
> **eli15 (detay):** Ayrı yazıldı çünkü sayı iddiaları tek yerde ve ölçümle durmalıdır. Okunması, envanter ve çelişki kaydını verir; kimse tahminle "kaç komut var" sorusunu cevaplamaz. Envanter ölçümü değişince düzenlenir. Bu dosya olmazsa eski sayılar yeni oturumları yanlış yönlendirir.

**`opencode.json`**
> **eli10 (basit):** Aracın ayar dosyası: hangi modelle çalışılacağı, hangi eklenti ve sunucuların açık olduğu.
> **eli15 (detay):** Ayrı dosyadır çünkü yapılandırma aracın okuyacağı JSON biçimindedir; `.md` içine gömülse okunmaz. Araç başlarken okunur. Eklenti/servis eklenince Security + MO onayıyla düzenlenir; içerik belgeye kopyalanmaz, sır anahtarları asla yazılmaz. İçerik bu oturumda okunmadı → anahtar envanteri `VERIFICATION REQUIRED`.

**Yedek dosyalar (`opencode.json.bak*` — 6 adet)**
> **eli10 (basit):** Ayar dosyasının eski halleri — biri bozulursa geri dönülecek kopyalar.
> **eli15 (detay):** Ayrı ve değişmez dururlar çünkü kurtarma kaynağıdırlar; düzenlenseler geri dönüş kalmaz. Dosya adlarındaki eki (faz2, mcp, senior, skillfix, thinking) hangi değişiklik dalgasının yedeği olduğunu gösterir. Yeni yapılandırma fazında yenisi eklenir. Silinmezler; temizlik talebinde önce onay istenir.

**`package.json` + `package-lock.json`**
> **eli10 (basit):** Eklentinin hangi sürümle kurulduğunu ve kurulumun nasıl tekrarlanacağını kaydeden iki küçük dosya.
> **eli15 (detay):** Ayrı dururlar çünkü tanım ile kilit farklı iş görür: `package.json` isteği, lock dosyası birebir ağacı sabitler. Paket eklenince DevOps birlikte günceller. İkisi birlikte değişmezse kurulum sürümü şaşar ve eklenti uyumsuzluğu doğar.

**`.gitignore`**
> **eli10 (basit):** Commit edilmesin diye işaretlenen beş kural: kurulu paket ve yedekler depoya girmez.
> **eli15 (detay):** Ayrı dosyadır çünkü git davranışı proje kodundan bağımsızdır; `.gitignore` olmadan 3.667 paket dosyası ve yedekler commit geçmişine girer. Yeni yoksayma eklenince gözden geçirilir. Kural kalkarsa depo gereksiz şişer ve yedekler herkese açılır.

### 3.2 Alt Dizin Envanteri (ölçüm 2026-10-12: 5 dizin)

| Dizin | Dosya | Ne için kullanılır | Neyden oluşur | Ne zaman düzenlenir |
|-------|-------|--------------------|---------------|---------------------|
| `agents/` | 17 md | Subagent tanımları — uzman roller (`master-orchestrator`, `backend-architect`, `data-engineer`, `security-engineer`, `ui-designer`, `ui-ux-analyzer`, `qa-engineer`, `devops-engineer`, `embedded-engineer`, `dsp-firmware-engineer`, `audio-hardware-engineer`, `windows-software-engineer`, `electronics-engineer`, `image-analysis-engineer`, `visual-render-studio`, `enterprise-prompt-architect`, `prompt-normalization-architect`) | Her dosya bir uzman rol tanımı (içerikleri bu oturumda OKUNMADI — işlev adlara dayanır) | Yeni subagent ekleme/değişiklik (onaylı) |
| `command/` | 10 md | Slash komut tanımları — `/komut` ile çağrılır | `ai-deps` · `changelog` · `commit` · `init-vault` · `issues` · `learn` · `prompt-maker` · `rmslop` · `spellcheck` · `translate` | Komut davranışı değişince (onaylı) |
| `.workflows/` | 3 md | Oturum/iş akışı reçeteleri | `vault-sync.md` · `session-init.md` · `security-audit.md` | Akış kapısı değişince |
| `rules/` | 0 | Kural dizini — **diskte boş (0 dosya)** | — | İçerik gelince |
| `node_modules/` | 3.667 dosya / 270 dizin | Kurulu npm paketleri (içerik okunmaz, üretilmez) | 28 üst girdi (27 dizin + 1 lock dosyası): `@ai-sdk`, `@opencode-ai`, `@standard-schema`, `effect`, `zod`, `yaml`, `toml`, `uuid` vb. | Yalnız `npm install` — elle düzenleme yasak |

**Toplam:** `.opencode/` altında recursive **3.711 dosya** (44 + `node_modules/` 3.667); `node_modules/` hariç **44 dosya** (2026-10-12).

**`agents/` (17 dosya)**
> **eli10 (basit):** Aracın çağırabileceği uzman rollerinin tanım dosyaları: ses, veri, güvenlik, arayüz, donanım uzmanları vb.
> **eli15 (detay):** Ayrı dizindir çünkü araç subagent'leri bu klasörden yükler (komut ve akıştan farklı mekanizma). Dosya adları rollerin alanını taşır; içerikleri bu oturumda okunmadığından işlev iddiaları adlara dayanır. Yeni uzman gerektiğinde onayla eklenir. Tanım olmadan roller çağrılamaz ve iş dağılımı elle yürütülür.

**`command/` (10 dosya)**
> **eli10 (basit):** Tek tuşla çağrılan hazır komutlar: commit yazma, changelog, prompt genişletme, yazım denetimi gibi işler.
> **eli15 (detay):** Ayrı dizin çünkü komut, subagent'den farklı tetiklenir (kullanıcı `/` ile çağırır). İçine komutun ne yaptığı ve hangi adımları çalıştırdığı yazılır. Komut davranışı değişince güncellenir; ad değişikliği kullanıcı alışkanlığı kırar, onay ister.

**`.workflows/` (3 dosya)**
> **eli10 (basit):** Oturumun nasıl başlayıp biteceğini anlatan üç reçete: başlatma, güvenlik taraması ve vault senkronu.
> **eli15 (detay):** Ayrı dizindir çünkü akış adımları kural metninden daha sık değişir ve kapı (gate) içerir. Oturum başında ve kapanışında okunur. Kapı değişince reçete güncellenir; adım atlanırsa kayıt zinciri kopar.

**`rules/` (0 dosya)**
> **eli10 (basit):** Kural dosyalarının konacağı boş klasör — şimdilik içinde hiçbir şey yok.
> **eli15 (detay):** Dizin diskte var ama içi boş; bu bilgi ölçümdür, dosya varmış gibi yazılmaz. Kural metinleri şimdilik kök ve `.ai/` dosyalarındadır. İçerik eklenince bu satır güncellenir.

**`node_modules/` (3.667 dosya / 270 dizin)**
> **eli10 (basit):** Aracın ihtiyacı olan hazır kütüphanelerin kurulduğu klasör — biz yazmadık, `npm` kurdu.
> **eli15 (detay):** Ayrı ve dokunulmazdır çünkü içerik 3. taraf koddur; elle düzenlenirse sonraki kurulumda silinir ve güvenlik yamaları kaybolur. `.gitignore` ile commit edilmez. Sadece `npm install` ile yeniden üretilir; sorun çıkarsa lock dosyasıyla kilit geri yüklenir.

### 3.3 Boot Okuma Sırası ve Otorite İlişkisi

```text
kök (AGENTS.md + CLAUDE.md) → .opencode/ (CLAUDE.md → AGENTS.md → WORKFLOW.md → CONTEXT.md)
  → .ai/ vault yalnız ihtiyaç anında (§7'deki hedeflerle)
```

| Bu klasör dosyası | Otorite satırı (frontmatter ölçümü) | Taşıdığı bilgi |
|-------------------|--------------------------------------|----------------|
| `.opencode/CLAUDE.md` | `authority: SSOT` — kendi iddiası (v27.3.4, güncelleme 2026-10-01) | Araç anayasası özeti |
| `.opencode/AGENTS.md` | `authority: "Derived — SSOT: .ai/AGENTS.md (routing) + kök AGENTS.md (master)"` | Rol + routing (türev) |
| `.opencode/WORKFLOW.md` | `authority: "Derived — SSOT: .ai/WORKFLOW.md + kök WORKFLOW.md"` | Süreç + kapılar (türev) |
| `.opencode/CONTEXT.md` | `authority: "Derived — SSOT: .ai/ + kök AGENTS.md"` | Envanter + çelişki kaydı (türev) |

> **eli10 (basit):** Her dosyanın kimi takip ettiği frontmatter'da yazılıdır; üçü vault'taki ana dosyadan türetilmiştir.
> **eli15 (detay):** İlişki ayrı tabloda çünkü çelişkide hangi kaynağa bakılacağı belli olsun: türev dosyalar kazanamaz, SSOT kaynağı kazanır. Okunması, boot sırasını ve otorite zincirini verir. Tablo unutulursa her dosya kendi iddiasıyla bağımsız çalışır ve bilgi ayrışır.

### 3.4 Çelişki Kaydı (eski nüsha ↔ disk — disk kazanır)

| # | İddia (eski CONTEXT.md, 2026-10-03 nüshası) | Disk ölçümü (2026-10-12) | Karar |
|---|---------------------------------------------|---------------------------|-------|
| 1 | §3.1: "11 kök dosya" | 14 kök dosya (`AGENTS.md`, `WORKFLOW.md`, `CONTEXT.md` eklenmiş) | **Disk kazanır** — bu dosya yeniden yazıldı |
| 2 | §3.2: "4 dizin: `skills/`, `command/`, `.workflows/`, `rules/`" | 5 dizin: `.workflows/`, `agents/`, `command/`, `node_modules/`, `rules/` — `skills/` YOK, `agents/` YENİ | **Disk kazanır** |
| 3 | §3.3 + §3.3.1: "`skills/` 10 dizin / 31 dosya; 9 `SKILL.md` + `_archive-keep/` 20 dosya" + 9 skill bloğu | `skills/` diskte yok — kapatıldı 2026-10-07 (kayıt: kök `CLAUDE.md` §Skill Registry; `.claude/CONTEXT.md` §3.4 notu) | **Disk kazanır** — skill tablosu ve blokları bu revizyonda kaldırıldı |
| 4 | §3.6: "'8 aktif skill' vs '9 SKILL.md'" çelişki satırı | Çelişki kendiliğinden güncelini yitirdi (`skills/` yok) | **Disk kazanır** — satır taşınmadı |
| 5 | §6 #4: "node_modules 3.667 · hariç 55 dosya" | 3.667 dosya **doğrulandı** (270 dizin); hariç artık **44** dosya | **Kısmen kazanır** — sayı doğrulandı, hariç toplam güncellendi |
| 6 | §3.1: "`opencode.json` (44.862 bayt)" | 44.643 bayt (değişiklik 2026-10-11) | **Disk kazanır** |
| 7 | §3.1: "`CLAUDE.md` (49.007 bayt)" | 49.089 bayt / 946 satır | **Disk kazanır** |
| 8 | §3.2: "`rules/` 0 dosya" | 0 dosya — doğrulandı | Tutarlı |

> **eli10 (basit):** Eski dosyada 11 dosya, "skills" klasörü ve eski bayt sayıları yazıyor; gerçeği disk söyler.
> **eli15 (detay):** Çelişki tablosu ayrı tutulur çünkü eski nüsha silinemez, yalnız işaretlenir. Okunması, hangi bilginin eskidiğini gösterir; yazılması ölçüm kanıtıyla olur. Bu tablo olmazsa "skills/ var" efsanesi ve eski sayılar yeni oturumları yanlış yönlendirir.

### 3.5 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur (aynı dosyada toplarsak) |
|---|---------|----------|----------------------------------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek dosyada kalır, inceleme (diff) küçük ve güvenli olur | Tek dosyada her şey değişince hangi ayarın ne yaptığı kaybolur, inceleme imkânsızlaşır |
| 2 | **Tek sorumluluk (tek iş)** | Her dosyanın tek görevi ve tek sahibi olur (ayar → Security+MO, komut → tooling, paket → DevOps) | Dosya büyüyüp okunmaz; "bunu kim yazdı, neden" sorusu cevapsız kalır |
| 3 | **Token tek kaynak** | Yapılandırma tek yerde durur (`opencode.json`); yedekler ayrı dosyalarda | Ayar dağınıksa araç farklı davranır, tekrar tarama gerekir |
| 4 | **Cihaz izolasyonu** | Araç/yerel makine ayarı proje verisine yayılmaz | Ayar kod içine yayılınca sürüm değişimi kodu bozar |
| 5 | **Vendor karantinası** | 3. taraf kod `node_modules/`'te kendi klasöründe durur | Dış kod içimize karışıp güncellemede kaybolur, güvenlik yamaları şaşar |

> **eli10 (basit):** Bilgiler tek kocaman dosyaya değil de küçük küçük kutulara konmuş; çünkü kutulardan birini değiştirmek diğerlerine zarar vermez.
> **eli15 (detay):** Ayrı dosyalar çünkü aynı işi yapan herkes aynı yere baksın diye: ayar tek JSON'da (`opencode.json`), kural tek dosyada ([[CLAUDE.md]]), rol tek dizinde (`agents/`), komut tek dizinde (`command/`), akış tek dizinde (`.workflows/`) durur. Böylece biri değişince diğeri bozulmaz. Paketler ayrı olunca güncelleme bizim dosyalarımızı kirletmez. Hepsi tek dosyada toplansa bakım ve kontrol edilebilirlik kaybolurdu.

---

## 4. Kurallar

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Her sayısal iddia bu oturumun ölçümüyle kanıtlanır; kanıt yoksa `UNKNOWN` | Zero-Hallucination ihlali |
| 2 | Çelişki (eski nüsha/vault ↔ disk) → **disk kazanır** + §3.4 tablosunda işaretlenir | SSOT ihlali |
| 3 | `CLAUDE.md` (bu dizin + kök) ve kök boot dosyaları bu görevde değiştirilmez | Boot zinciri kırılması |
| 4 | `opencode.json` içeriği belgeye kopyalanmaz; sır/anahtar `.md`'e yazılmaz (REDACTED) | Anahtar sızıntısı |
| 5 | `node_modules/` · `*.bak*` · lock dosyası elle düzenlenmez/silinmez | Kurtarma/güncelleme güvencesi kaybı |
| 6 | `rules/` boş olarak yazılır — dosya varmış sayılmaz | Yalan envanter |
| 7 | Yeni `.md` dosyası şablondan üretilir (Guardrail #16 — [[../.ai/.templates/documentation/context-md-template.md]] + [[../.ai/.templates/frontend/context-template.md]]) | Şablon ihlali |
| 8 | Wiki-link `[[...]]` formatı; diskte olmayan hedef üretilmez | Kırık link |
| 9 | Emoji/dekoratif işaret yasak; şablon placeholder'ı kalamaz | Şablon/teslim ihlali |
| 10 | Subagent `git commit` ATMAZ — rapor orkestratöre gider | Yetkisiz commit, revert riski |

> **eli10 (basit):** Bu kurallar, ayar ve klasör dosyalarına yanlış davranmayı engeller; hepsi tek sebepten: yanlış dokunuş tüm oturumları bozar.
> **eli15 (detay):** Kurallar ayrı tabloda durur çünkü kural ile dosya içeriği farklı hızda değişir; içeriğe gömülse revizyonda kaybolur. Okunmaları, bu dizinde işe başlamadan sınırları gösterir. Yazılmaları, denetimin tekrarlanabilir olmasını sağlar. Kök kural değişince yalnız bu tablo güncellenir.

---

## 5. Workflow

```text
GÖREV → KURAL OKU (kök AGENTS + .opencode/CLAUDE.md + AGENTS.md)
  → ENVANTER ÖLÇ (ilk okumada — sayı uydurulmaz) → KARAR VER
  → UYGULA (yalnız hedef dosya; onay kapıları: opencode.json → Security+MO)
  → DOĞRULA (§6 tablosu) → RAPOR (log append; commit ORCHESTRATÖRDE — subagent atmaz)
```

| # | Adım | Çıktı | Neden | Atlarsan ne olur |
|---|------|-------|-------|------------------|
| 1 | Kural oku: [[CLAUDE.md]] + [[AGENTS.md]] | Kural listesi | Klasör yasakları baştan belli olsun | Yasak ihlali, sır riski |
| 2 | Envanter ölç (glob + okuma; sayı uydurulmaz) | Ölçüm tablosu | Sayı iddiası ölçümle kanıtlanır | Halüsinasyon, yanlış "eklendi/silindi" iddiası |
| 3 | Değişikliği ilgili dosyaya yap (onay kapıları korunarak) | Diff | Tek sorumluluk sınırı | Yanlış dosyaya yazma |
| 4 | §6 doğrulama tablosunu çalıştır | 12/12 | Teslimden önce kanıt | Hatalı dosya teslim edilir |
| 5 | Çelişki/eksik referansı §3.4'e işaretle | Açık liste | Doğrulanmayan iddia yazılmaz | Yanlış "tamamlandı" |
| 6 | Rapor yaz; **commit ATMA** | Rapor | Yetki sınırı (kök [[../AGENTS.md]] §7/§8) | Yetkisiz commit, revert riski |

> **eli10 (basit):** İş sırayla yürür: kuralı oku, dosyaları say, izinle değiştir, farkları kaydet, kontrol et ve haber ver; commit'i sen atmazsın.
> **eli15 (detay):** Adımlar ayrı satırlarda çünkü her adımın çıktısı ve "atlarsan ne olur" gerekçesi ölçülür. Okunması, kapıları önceden bilmeyi sağlar. Yazılması, tekrarlanabilir ve denetlenebilir bir akış bırakır. Özellikle ölçüm ve doğrulama adımı atlanırsa hatalı iddia dosyaya girer ve sonradan pahalıya bulunur.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | Şablon alanları + `docType: context` (frontend şablon §3.2) |
| 2 | Bölüm yapısı | §1–§7 aynı sıra, ≤3 başlık seviyesi |
| 3 | Placeholder | Doldurulmamış şablon değişkeni kalmadı |
| 4 | Envanter | 14 kök dosya · 5 dizin · `agents/` 17 · `command/` 10 · `.workflows/` 3 · `rules/` 0 · `node_modules/` 3.667/270 — hepsi 2026-10-12 ölçümüyle eşit |
| 5 | Disk kanıtı | Bayt/satır sayıları (49.089/23.859/22.794/44.643/65/14.632/63) `get_file_info` + okuma ile kanıtlı |
| 6 | Wiki-link | `[[...]]` formatı; §7 hedefleri diskte mevcut (hepsi 2026-10-12 doğrulandı) |
| 7 | eli10 + eli15 | §1, §2, §3.1 (9 madde), §3.2 (5 madde), §3.3, §3.4, §3.5, §4, §5 bloklarında etiketli çift var; eli10 ≤2 cümle, eli15 3-4 cümle |
| 8 | Çelişki kuralı | §4 #2 "disk kazanır" + §3.4 tablosu 8 satır |
| 9 | Boot sırası | §3.3 boot + otorite tablosu mevcut |
| 10 | Kaldırılan atıf | K-space/mimari katman atıfı yok (grep 2026-10-12: 0 eşleşme); kaldırma notu §1'de tek satır |
| 11 | Emoji | Dekoratif emoji yok |
| 12 | Uzunluk | 250–400 satır bandı |

---

## 7. Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Klasör kuralları (MEVCUT — dokunulmaz) | [[CLAUDE.md]] | Araç anayasası |
| Klasör rolleri | [[AGENTS.md]] | Rol + yetki + routing |
| Klasör süreci | [[WORKFLOW.md]] | Adım → kapı → rapor |
| Kök master kurallar | [[../AGENTS.md]] | §3 Zero-Hallucination · §5 anti-overthink · §7 loop |
| Vault anayasası | [[../.ai/CLAUDE.md]] | Hard Guardrails |
| Agent registry (SSOT) | [[../.ai/AGENTS.md]] | Routing, handover, skill kaydı |
| Vault süreç | [[../.ai/WORKFLOW.md]] | Faz kapıları |
| Audit trail | [[../.ai/log.md]] | Değişiklik kaydı (append) |
| Bağlayıcı iskelet | [[../.ai/.templates/frontend/context-template.md]] | §1–§7 + eli10/eli15 (çelişkide bu kazanır) |
| CONTEXT şablonu | [[../.ai/.templates/documentation/context-md-template.md]] | Envanter tablosu formatı + sayım disiplini |
| Template registry | [[../.ai/.templates/index.md]] | Şablon kayıt satırı |
| Alt registry | [[../.ai/.agents/AGENTS.md]] | Agent profilleri indeksi |
| Senkron akışı | [[.workflows/vault-sync.md]] | Oturum kapanış kapısı |
| Oturum başlangıcı | [[.workflows/session-init.md]] | Açılış reçetesi |
| Güvenlik akışı | [[.workflows/security-audit.md]] | Sır/servis denetimi |
| Paket kanıtı | `package.json` · `package-lock.json` | `@opencode-ai/plugin` 1.18.13 |
| Ayar kanıtı | `opencode.json` | Yapılandırma (içerik okunmadı — `VERIFICATION REQUIRED`) |
| Disk kanıtı | `.opencode/` gerçek envanteri | §3 tüm sayıları |

---

**Template Version:** 1.0.0 · **Şablonlar:** `.ai/.templates/frontend/context-template.md` + `.ai/.templates/documentation/context-md-template.md`
**Last Updated:** 2026-10-12 · **docType:** context
