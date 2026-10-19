---
title: "CoreMusic — .opencode/ Rolleri & Routing"
type: docs
category: opencode
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/AGENTS.md (routing) + kök AGENTS.md (master)"
docType: agents
---

# CoreMusic — .opencode/ Rolleri & Routing

**docType:** agents · **Klasör:** `.opencode/` · **Sorumlu:** MO (routing), tooling (skill/command üretimi)

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[CLAUDE.md]] · [[CONTEXT.md]] · [[../.ai/AGENTS.md]] · [[../.ai/WORKFLOW.md]]

---

## 1. Amaç

Bu doküman `.opencode/` dizininde **kimin ne yaptığını** tanımlar: rol sorumlulukları, skill/command sahipliği, bu dizine özgü keyword routing, handover ve escalation. Global routing SSOT'u [[../.ai/AGENTS]] §6'dır; bu dosya **çalışma ortamına özgü daraltmadır** — çelişkide kök registry kazanır.

| Karar | Karşılığı |
|-------|-----------|
| `skills/` · `command/` üretimi → tooling + ilgili uzman agent | [[../.ai/AGENTS]] §5 domain boundary |
| `opencode.json` sır/servis ayarı → Security denetimi | [[../AGENTS]] §3 (Zero-Hallucination + REDACTED) |
| Vault senkronu → MO (vault-updater) | [[.workflows/vault-sync]] |
| Commit → yalnız orkestratör | [[../AGENTS]] §7/§8 |

> **eli10 (basit):** Bu sayfa, bu klasörde çalışırken hangi işin kime düştüğünü ve kime teslim edileceğini gösterir.
> **eli15 (detay):** Ayrı dosyadır çünkü rol ile kural farklı sorumluluk taşır; roller tek yerde olmazsa iş yanlış ajana gider. İçine rol tablosu, skill sahipliği, routing ve teslim (handover) kuralları yazılır. Göreve başlarken okunur. Yeni skill/komut eklendiğinde ya da routing değişince güncellenir.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `.opencode/` içindeki dosya sahipliği (skill, command, workflow, config) | Genel routing/handover/escalation tanımı → [[../.ai/AGENTS]] §6-§10 |
| Bu dizine özgü keyword → agent eşlemesi | Kod dosyası sahipliği (`*.php`, `*.css`, `*.js`) → ilgili klasör AGENTS.md |
| Skill/command üretiminin şablon kapısı (Guardrail #16) | Model/sağlayıcı seçimi kararı → kullanıcı + `opencode.json` |
| Bu dizinde commit/yetki sınırı | Deploy/CI → DevOps |

- **Kullananlar:** MO (birincil routing), tooling (skill/command yazarı), Security (sır denetimi), QA (doğrulama), OpenCode (boot — okur).
- **Ön koşul:** Hedef dosya diskte mevcut; rol iddiası vault registry ile çelişiyorsa kök registry kazanır.
- **Not:** `docType: agents` = rol ağırlıklı; her rol maddesi "ne için / ne zaman devreye girer" taşır.

> **eli10 (basit):** Kapsam, hangi işlerin burada konuşulduğunu ve hangi işlerin başka dosyalara ait olduğunu ayırır.
> **eli15 (detay):** Kapsam ayrı çizildi ki bu dosya genel ajan kuralının kopyası olmasın; yalnızca bu dizin için geçerli olan satırlar taşınır. İçine skill/komut sahipliği ve teslim kuralları girer. Rol tartışması çıktığında burası okunur. Kapsam genişlerse önce kök registry'ye sorulur.

---

## 3. Mimari

### 3.1 Rol Tablosu — Kim ne yapar (bu dizin)

| Agent / Birim | Kod | Ne için (işlev) | Ne zaman devreye girer (tetikleyici) | Ne yapar | Ne YAPMAZ |
|---------------|-----|-----------------|--------------------------------------|----------|-----------|
| **Master Orchestrator** | `mo` | Routing + kapanış — tek commit kalemi, vault senkronu | Görev başında (keyword eşlemesi); iş bitiminde (`vault-sync`); lock çatışmasında | Routing, `CONTEXT.md`/`WORKFLOW.md` envanter güncellemesi, `log.md` append, **commit** | Skill/komut içeriğini tek başına üretmez; kod yazmaz |
| **Tooling (skill/command yazarı)** | `tooling` | `skills/` ve `command/` dosyalarının üretimi ve revizyonu — bu dizin tek elden büyür | Yeni iş tipi için beceri/komut talebi; Guardrail #16 şablonu hazır | `skills/*/SKILL.md`, `command/*.md` yazar; `_archive-keep/`'e kaldırma önerir | `opencode.json` içeriğini değiştirmez (Security+MO onayı) |
| **Security Engineer** | `security` | Sır/servis ayarı denetimi — anahtar ve yetki yüzeyi | `opencode.json` değişikliğinde; MCP/servis eklendiğinde; güvenlik akışı (`security-audit`) çağrıldığında | REDACTED denetimi (anahtar metin belgeye girmez), `security-audit.md` akışını yürütür | Skill/komut metni üretmez |
| **QA Engineer** | `qa` | Dosya biçim ve link doğrulaması — şablon/placeholder/emoji kapıları | Yeni `.md` üretildiğinde (zorunlu son kapı); kırık link raporunda | Frontmatter 7+docType, §1-§7 sırası, doldurulmamış placeholder yok, wiki-link formatı, eli10/eli15 blokları | Dosyayı kendisi düzeltmez — bulguyu yazana handover eder |
| **DevOps Engineer** | `devops` | Paket/ağaç sağlığı — `npm` kurulumu, lock dosyası, gitignore | Paket eklenip çıkarken; `node_modules` sorununda; CI paket adımı | `package.json`/`package-lock.json` değişikliği, `.gitignore` gözden geçirme | Vault `.md` dosyalarına yazmaz |
| **Uzman Agent'lar** (ui, backend, data, embedded, qa, dsp-fw, audio-hw, win-sw) | `ui`…`win-sw` | Kendi domainine ait skill/komutun **içeriği** | Domain becerisi gerektiğinde (routing [[../.ai/AGENTS]] §6) | İlgi alanı becerisini içerik olarak besler (`db-engine`, `ui-workbench`, `composer-sync` vb.) | Dizinin genel yapısını/registry'yi değiştirmez |

**Rol eli10/eli15 blokları (6 madde):**

**Master Orchestrator (`mo`)**
> **eli10 (basit):** Kimin ne yapacağını ayarlayan, iş bitince tek elden kaydeden yönetici.
> **eli15 (detay):** Görev dağıtımı ve kayıt tek elden olmalı ki dağınık değişiklik bir araya gelmesin. Görev başında (routing) ve iş bittiğinde (senkron/commit) devreye girer. Diğerlerinin dosyalarını okur, kod yazmaz. Tek kalemden geçmişi tutmak için.

**Tooling (`tooling`)**
> **eli10 (basit):** Hazır komut ve beceri dosyalarını yazan, onları güncel tutan kişi.
> **eli15 (detay):** Ayrı rol çünkü kılavuz dosyaları koddan farklı üretilir ve şablona bağlıdır. Yeni iş tipi doğduğunda ya da eski kılavuz yanlış yönlendirdiğinde devreye girer. Şablonu okur, dosyayı yazar, doğrulatır. Sahiplik tek olmazsa iki beceri birbirine girer.

**Security Engineer (`security`)**
> **eli10 (basit):** Ayar dosyasındaki sırların ve dış servis bağlantılarının güvenliğini denetleyen uzman.
> **eli15 (detay):** Ayar dosyası servis ve anahtar taşıdığı için ayrı denetime tabidir. Yapılandırma değiştiğinde ya da güvenlik taraması istendiğinde devreye girer. Dosyayı okur, sızıntı riskini kontrol eder, kural metni yazmaz. Anahtar hiçbir belgeye sızmaz.

**QA Engineer (`qa`)**
> **eli10 (basit):** Yeni yazılan kılavuz dosyalarının formunu ve bağlantılarını kontrol eden denetçi.
> **eli15 (detay):** Yapan ile kontrol eden aynı olursa hata kaçar; o yüzden ayrı rol. Her yeni `.md` üretildiğinde devreye girer. Dosyayı okur, düzeltmez; bulguyu yazana geri gönderir. Kapı geçmezse iş tamamlanmış sayılmaz.

**DevOps Engineer (`devops`)**
> **eli10 (basit):** Gerekli eklentilerin kurulup sürüm kayıtlarının düzgün tutulmasından sorumlu.
> **eli15 (detay):** Paket ağacı ayrı dosyalarda (`package.json`, `package-lock.json`) tutulur ki kurulum tekrarlanabilir olsun. Paket eklenip çıkarıldığında devreye girer. Dosyaları okur ve günceller; vault dokümanına yazmaz. Kilit (lock) bozulursa kurulum sürdürülemez.

**Uzman Agent'lar (`ui`…`win-sw`)**
> **eli10 (basit):** Kendi alanıyla ilgili becerinin içeriğini besleyen uzmanlar.
> **eli15 (detay):** İçerik ile iskelet ayrıdır: iskeleti tooling yazar, domain bilgisini uzman verir. Domain becerisi çağrıldığında devreye girerler. Kendi alanlarının kılavuzunu okur, ortak yapıya dokunmaz. Bilgi yanlış olursa beceri tüm işleri yanlış yürütür.

### 3.2 Dosya Tipi → Sorumluluk Matrisi

| Dosya / Dizin | Yazım | Denetim | Not |
|---------------|-------|---------|-----|
| `CONTEXT.md` · `WORKFLOW.md` (bu dizin) | MO (vault-updater) | QA (§6) | Yeni dosya → context-template (Guardrail #16) |
| `AGENTS.md` (bu dosya) | MO (registry) | QA | Routing türevidir; çelişkide kök kazanır |
| `CLAUDE.md` (MEVCUT — dokunulmaz) | MO + Security | QA | Bu görevde değiştirilmez |
| `skills/*/SKILL.md` | Tooling + uzman agent | QA | Şablon zorunlu; `_archive-keep/` salt saklama |
| `command/*.md` | Tooling | QA | Komut adı değişmez (kullanıcı alışkanlığı) |
| `.workflows/*.md` | MO (workflow) | QA + Security (`security-audit`) | Kapı değişimi kayda bağlanır |
| `opencode.json` | tooling (teklif) | **Security + MO onayı** | Sır/RPC değeri belgeye kopyalanmaz |
| `package.json` · `package-lock.json` | DevOps | CI | Lock elle düzenlenmez |
| `rules/` | — (0 dosya) | — | İçerik yok; dosya varmış sayılmaz |
| `node_modules/` | YALNIZ `npm install` | — | Elle düzenleme yasak |
| `*.bak` yedekleri | — | — | Değiştirilmez/silinmez |

> **eli10 (basit):** Hangi dosyayı kimin yazacağı bu tabloda; denetleyen her zaman yazandan ayrı biri.
> **eli15 (detay):** Matris ayrı çizilir çünkü eşzamanlı oturumlarda aynı dosyaya iki kişi yazarsa değişiklik kaybolur. Yazım ile denetim ayrımı, hata kaçmasını önler. Tablo okunmadan başlanırsa yetki ihlali (layer violation) doğar. Sorumluluk değişince bu satır güncellenir.

### 3.2.1 Yetki İhlali (Layer Violation) Tanımı ve Sonucu

| Durum | Örnek | Sonuç |
|-------|-------|-------|
| Yanlış role dosya yazımı | QA'nın `SKILL.md`'yi tek başına düzeltmesi | Dosya geri alınır → tooling |
| Onaysız yapılandırma değişikliği | `opencode.json` değişimi Security/MO'suz | Değişiklik revert edilir |
| Saklama alanında değişiklik | `_archive-keep/` içeriğinin düzenlenmesi | Düzeltme silinir; onay ister |
| Kural/SSOT çelişkisi | Bu dosyanın routing'i kök registry ile çelişirse | Kök kazanır + düzeltme kaydı |
| Vault dışı yazım | `.ai/` içine bu dizinden yazım | Reddedilir; MO kanalı kullanılır |

> **eli10 (basit):** Yetki dışına çıkılınca ne olacağını anlatan beş satır.
> **eli15 (detay):** İhlal tanımı ayrıdır çünkü ceza değil gerekçe yazılır: geri alma, onay ve düzeltme kaydı önceden bellidir. İçine durum, örnek ve sonuç girer. Teslimden önce okunur. Kural değişince tablo güncellenir.

### 3.3 Skill / Command Sahipliği

| Öğe | Dosya sayısı | Sahip | Devreye giriş anı |
|-----|--------------|-------|-------------------|
| `agent-debate` · `context-report` · `orchestration` · `truth-engine` · `vault-sync-post` | 1+1+1+1+2 | MO (koordinasyon/kayıt) | Görev koordinasyonu, rapor, senkron |
| `composer-sync` | 2 | DevOps + Backend (içerik) | Paket senkronu |
| `db-engine` | 1 | Data Engineer | Şema/sorgu işi |
| `ui-workbench` | 1 | UI Designer | CSS/JS/ITCSS işi |
| `verify-loop` | 1 | QA Engineer | Doğrulama döngüsü (PHP/CSS/JS) |
| `_archive-keep/` | 20 | MO | Arşiv — salt okunur |
| `command/prompt-maker` | 1 | MO | Görev başlangıcı (kök §8) |
| `command/commit` · `changelog` · `issues` · `ai-deps` · `init-vault` · `learn` · `rmslop` · `spellcheck` · `translate` | 9 | tooling (ortak) | İlgili komut çağrısında |

> **eli10 (basit):** Her beceri ve komutun tek bir sahibi var; hangi işe aitse onun uzmanı yazıyor.
> **eli15 (detay):** Sahiplik tablosu ayrıdır çünkü ortak sahiplikte güncelleme kimde olduğu belli olmaz. İçine dosya grubu, sahip ve tetikleyici yazılır. Yeni beceri eklenince bir satır eklenir. Sahipsiz kalırsa kılavuz eskiyor ve yanlış yönlendiriyor.

### 3.4 Bu Dizine Özgü Keyword Routing (kök §6 türevi — çelişkide kök kazanır)

| Keyword grubu | Birincil | İkincil |
|---------------|----------|---------|
| skill, beceri, SKILL.md, komut, command, slash | **MO (registry)** | tooling |
| `opencode.json`, MCP, provider, reasoning, anahtar, token, secret | **Security Engineer** | DevOps |
| `package.json`, `npm`, `node_modules`, lock, plugin | **DevOps Engineer** | tooling |
| vault-sync, senkron, session-init, security-audit | **MO (workflow)** | Security (`security-audit`) |
| docType, frontmatter, wiki-link, placeholder, şablon | **QA Engineer** | MO (vault-updater) |

> **eli10 (basit):** Hangi kelime geçince kime gideceğini gösteren kısa tablo.
> **eli15 (detay):** Routing ayrıdır çünkü genel tablo tüm projeyi kapsar, bu tablo yalnız bu dizini. Doğru ajana gitmeyen iş ya bekler ya yanlış elde değişir. Tablo okunmadan işe başlanırsa yetki ihlali çıkar. Kök routing değişince bu tablo da güncellenir.

### 3.5 Handover Senaryoları (bu dizine özgü — kök §9.3 türevi)

| Kaynak | Hedef | Tetikleyici | Öncelik |
|--------|-------|-------------|---------|
| Tooling | QA | Yeni skill/command dosyası üretildi | MEDIUM |
| QA | Tooling | Frontmatter/§/placeholder/link ihlali | MEDIUM |
| tooling (teklif) | Security + MO | `opencode.json` değişiklik talebi | HIGH |
| Security | MO | Sır/anahtar belgeye sızmış | CRITICAL |
| DevOps | MO | Paket/lock değişikliği | LOW |
| Uzman agent | Tooling | Domain becerisi içerik güncellemesi | MEDIUM |
| MO | Tümü | Senkron sonrası düzeltme | LOW |

### 3.6 Escalasyon (bu dizine özgü — kök §10 türevi)

| Durum | Seviye 1 | Seviye 2 | Seviye 3 |
|-------|----------|----------|----------|
| Skill dosyası şablonsuz üretildi | Tooling düzeltir | QA RED | MO |
| `opencode.json` sır sızıntısı | Security durdurur | MO revert | İnsan |
| Lock/paket çatışması | DevOps çözer | MO | İnsan |
| Routing çelişkisi (bu dosya ↔ kök) | MO düzeltir | — | İnsan |

> **eli10 (basit):** Sorun büyüyünce kime gidileceğini gösteren merdiven.
> **eli15 (detay):** Escalasyon ayrı tablodur çünkü her seviyenin bekleme süresi ve yetkisi farklıdır. Okunması, kör deneme döngüsüne girmeyi engeller. Seviye atlanırsa yetkisiz değişiklik olur. Kök registry değişince bu tablo güncellenir.

### 3.7 Senaryo → Agent → Handover Matrisi

| # | Senaryo (tetikleyen) | Birincil | Handover / not |
|---|----------------------|----------|----------------|
| 1 | Yeni skill dosyası (`skills/<ad>/SKILL.md`) | Tooling | Şablon (Guardrail #16) yoksa → MO'ya DUR raporu |
| 2 | Yeni komut dosyası (`command/<ad>.md`) | Tooling | Komut adı değişmez — ad değişikliği onay ister |
| 3 | `.workflows/` kapısı değişikliği | MO (workflow) | `security-audit` ise → Security ortak imza |
| 4 | `opencode.json` anahtar/servis değişikliği | tooling (teklif) | → Security + MO onayı (HIGH) |
| 5 | Paket ekleme/çıkarma (`package.json`) | DevOps | Lock güncellemesi zorunlu → CI kapısı |
| 6 | `_archive-keep/` içeriğini yeniden değerlendirme | MO | Silme ONAY ister — salt saklama |
| 7 | Yeni `CONTEXT`/`AGENTS`/`WORKFLOW` dosyası | MO (vault-updater) | context-template zorunlu → QA denetimi |
| 8 | Frontmatter/§/placeholder/link ihlali | QA Engineer | → yazana handover (MEDIUM) |
| 9 | Sır/anahtar `.md` içine girdi | Security Engineer | CRITICAL — derhal durdurma + MO |
| 10 | `rules/` dizinine içerik önerisi | MO | Dizin boş; içerik → kök/`.ai/` kuralı tartışması |
| 11 | Vault ↔ disk çelişkisi | İlk farkeden | `VERIFICATION REQUIRED` + MO; disk kazanır |
| 12 | Dizinde toplu değişiklik / branch birleştirme | MO | Context lock; en esik kilidi MO kırar |

> **eli10 (basit):** Hangi olayda kime haber vereceğimizi gösteren kısa liste.
> **eli15 (detay):** Matris ayrıdır çünkü gerçek hayatta çıkan olaylar tek tek tarif edilmezse her seferinde yeniden karar alınır. İçine olay, sorumlu ve teslim yolu yazılır. Üretim başlarken okunur. Yeni olay tipi çıktığında bir satır eklenir.

### 3.8 Ölçüm ve Kapsam Sınırları (2026-10-03)

| Konu | Durum | Davranış |
|------|-------|----------|
| Dizin/dosya sayıları | **ÖLÇÜLDÜ** (recursive, gizli dahil) | Sayılar yeniden ölçülmeden değiştirilmez |
| `skills/*/SKILL.md` içeriği | **OKUNMADI** | İşlev açıklaması dosya adı + vault kaydına dayanır |
| `command/*.md` · `.workflows/*.md` içeriği | `.workflows` 3 dosya **OKUNDU**; `command` 10 dosya **OKUNMADI** | Komut işlevi dosya adıyla sınırlı |
| `opencode.json` tüm anahtarları | **OKUNMADI** (44.862 bayt) | §3.4'te yalnız bilinen anahtarlar; geri kalanı VERIFICATION REQUIRED |
| `.opencode/.workflows` ↔ kök `.workflows` karşılaştırması | **YAPILMADI** | Kopya olup olmadığı bilinmiyor |

> **eli10 (basit):** Bu tablo, hangi bilginin gerçekten okunduğunu, hangisinin henüz okunmadığını açıkça söylüyor.
> **eli15 (detay):** Sınırlar ayrı yazılır çünkü okunmayan şeyin özeti uydurma olur; envanter ile içerik farklı şeylerdir. Okunmuş kısım dayanak, okunmamış kısım soru olarak durur. Belge her açıldığında bu satırlar güven kaynağıdır. İçerik okunduğunda satır güncellenir.

---

### 3.9 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur (aynı dosyada toplarsak) |
|---|---------|----------|----------------------------------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek dosyada kalır, inceleme (diff) küçük ve güvenli olur | Tek dosyada her şey değişince hangi kuralın ne yaptığı kaybolur |
| 2 | **Tek sorumluluk (tek iş)** | Her dosyanın tek görevi ve tek sahibi olur | "Bunu kim yazdı, neden" sorusu cevapsız kalır |
| 3 | **Token tek kaynak** | Yapılandırma tek JSON'da | Ayar dağınıksa araç farklı davranır |
| 4 | **Cihaz izolasyonu** | Araç ayarı proje verisine yayılmaz | Ayar kod içine yayılınca sürüm değişimi kodu bozar |
| 5 | **Vendor karantinası** | `node_modules/` ve `_archive-keep/` ayrı durur | Dış içerik içimize karışıp güncellemede kaybolur |

> **eli10 (basit):** Roller ve dosyalar küçük kutulara bölünmüş; çünkü kutulardan birini değiştirmek diğerine zarar vermez.
> **eli15 (detay):** Ayrı dosyalar çünkü rol, kural ve süreç farklı hızda değişir: kural kökte, süreç `.ai/`de, rol burada durur. Böylece biri değişince diğeri bozulmaz. Hepsi tek dosyada toplansa kimin ne yetkisi olduğu kaybolurdu.

---

## 4. Kurallar

| # | Kural | Neden var (gerekçe) | Ref |
|---|-------|---------------------|-----|
| 1 | Routing bu dizinde **türevidir**; çelişkide [[../.ai/AGENTS]] §6 kazanır | Tek registry | [[../.ai/AGENTS]] §2.1 |
| 2 | Yeni `skills/`/`command/`/`.md` dosyası şablondan üretilir | Tutarlı denetim (Guardrail #16) | [[../.ai/AGENTS]] §14 |
| 3 | `CLAUDE.md` ve kök boot dosyaları bu görevde **değiştirilmez** | Boot zinciri kırılmasın | Görev kuralı |
| 4 | `opencode.json` içeriğine sır/anahtar **kopyalanmaz** | REDACTED | [[../AGENTS]] §3 |
| 5 | `node_modules/` · `*.bak*` · `_archive-keep/` elle düzenlenmez | Kurtarma/güncelleme güvencesi | [[CONTEXT]] §3.2-§3.3 |
| 6 | Disk kanıtı olmayan sayı/rol iddiası yazılmaz; okunmayan `VERIFICATION REQUIRED` | Zero-Hallucination | [[../.ai/.templates/frontend/context-template]] §4.1 #2 |
| 7 | Sayı iddiası ölçümle kanıtlanır | Envanter doğruluğu | context-template §4.1 #3 |
| 8 | Wiki-link (çift köşeli bağlantı); markdown path linki değil | Link denetimi | context-template §4.1 #5 |
| 9 | Emoji/dekoratif işaret yasak | Metin taşınabilirliği | context-template §4.1 #8 |
| 10 | **Commit subagent tarafından ATILMAZ** | Tarih/entegrasyon yetkisi orkestratörde | [[../AGENTS]] §7/§8 |
| 11 | 3 başarısız düzeltme → DUR + 1 kısa soru | Kör deneme döngüsü yasak | [[../AGENTS]] §5 |

> **eli10 (basit):** Bu kurallar, bu klasörde yetkiyi ve doğruluğu korur; en önemlisi commit'i başkasına bırakmak.
> **eli15 (detay):** Kurallar ayrı tabloda tutulur çünkü rol ile kural farklı hızda değişir; role gömülse revizyonda kaybolur. Okunmaları, işe başlamadan yetki sınırını gösterir. Yazılmaları, denetimin tekrarlanabilir olmasını sağlar. Kök kural değişince yalnız bu tablo güncellenir.

---

## 5. Workflow

```text
MO ROUTING (§3.4 keyword → agent) → GÖREV ATAMA
  → ÖN UÇ KONTROL (şablon var mı? sır riski var mı? lock var mı?)
  → ÜRET (skills/ | command/ | .workflows/ | CONTEXT/WORKFLOW)
  → QA DENETİMİ (frontmatter · §1-§7 · placeholder · wiki-link · eli10/eli15)
  → HANDOVER (§3.5 tablosu) → MO: log.md append + COMMIT
```

| # | Adım | Çıktı | Neden | Atlarsan ne olur |
|---|------|-------|-------|------------------|
| 1 | Routing (§3.4) | Doğru agent | İş doğru ele gitsin | Yetki ihlali, yanlış dosya |
| 2 | Pre-flight: şablon + lock + sır kontrolü | Geçiş | Guardrail #16 / REDACTED | Şablonsuz dosya, sızıntı |
| 3 | Üretim (şablona uygun) | Yeni dosya | Tutarlı iskelet | Denetlenemez içerik |
| 4 | QA denetimi (§6) | Onay/red | Kırık iş girmesin | Sessiz hata, kırık link |
| 5 | Handover (§3.5) | İkinci imza | Yapan ≠ denetleyen | Aynı hata iki kez |
| 6 | `log.md` append + rapor | Audit satırı | Geçmiş tek yönlü | İz kaybı |
| 7 | **Commit yok (subagent)** | Teslim | Yetki sınırı | Yetkisiz commit |

> **eli10 (basit):** İş sırası: kime verdiğim, doğru araçla üretimi, başkasının kontrolü ve kaydı; commit hariç.
> **eli15 (detay):** Adımlar ayrı tutulur çünkü bu dizin hem kılavuz hem ayar üretir; kapı atlanırsa hata tüm oturumlara yayılır. QA kapısı, yapan ile denetleyeni ayırır. Senkron adımı, kaydın zincire girmesini sağlar. Adım değişince bu tablo güncellenir.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: agents` |
| 2 | Bölüm yapısı | §1–§7 aynı sıra, ≤3 başlık seviyesi |
| 3 | Rol tablosu | Her rolde "Ne için" + "Ne zaman devreye girer" dolu |
| 4 | Sahiplik | 9 skill + 10 command + 3 workflow sahipsiz kalmadı |
| 5 | Routing | §3.4 kök §6 ile uyumlu; çelişki notu mevcut |
| 6 | Commit | §3.1 + §4 #10 + §5 #7 "ATMAZ" ifadesi (3 yerde) |
| 7 | eli10 + eli15 | §1-§5 maddelerinin altında etiketli blok; eli10 ≤2 cümle, eli15 3-4 cümle |
| 8 | İçerik neden ayrı | §3.9 tablosu 5 satır |
| 9 | Halüsinasyon | `rules/` 0 dosya olarak yazıldı; okunmayan roller `VERIFICATION REQUIRED` |
| 10 | Dokunulmaz | `CLAUDE.md` / kök / `.ai/` boot dosyaları değiştirilmedi; commit atılmadı |

---

## 7. Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Agent registry (SSOT) | [[../.ai/AGENTS]] | §5 domain · §6 routing · §9 handover · §10 escalation |
| Kök master kurallar | [[../AGENTS]] | §4 keşif · §5 anti-overthink · §7 loop · §8 prompt-maker |
| Bu dizin envanteri | [[CONTEXT]] | Ölçülmüş dosya/dizin sayıları |
| Bu dizin süreci | [[WORKFLOW]] | Adım → kapı → rapor |
| Ortam kuralı (MEVCUT) | [[CLAUDE]] | Çalışma ortamı kural özeti |
| Vault süreç | [[../.ai/WORKFLOW]] | Faz kapıları |
| Context şablonu | [[../.ai/.templates/frontend/context-template]] | İskelet + §4.1 guardrail |
| Skill registry | [[../.ai/.templates/index]] | Şablon/kayıt satırı |
| Alt registry | [[../.ai/.agents/AGENTS]] | 11 agent profili |
| Senkron akışı | `.workflows/vault-sync.md` | Oturum kapanış kapısı |
| Güvenlik akışı | `.workflows/security-audit.md` | Sır/servis denetimi |
| Paket kanıtı | `package.json` | `@opencode-ai/plugin` 1.18.13 |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-03 · **docType:** agents
