---
title: "CoreMusic — .opencode/ Çalışma Ortamı Context"
type: docs
category: opencode
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/ + kök AGENTS.md"
docType: context
---

# CoreMusic — .opencode/ Çalışma Ortamı Context

**docType:** context · **Klasör:** `.opencode/` · **Sorumlu:** MO (vault-updater), tooling (skill/command)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[../AGENTS.md]] · [[../.ai/CLAUDE.md]] · [[../.ai/AGENTS.md]] · [[../.ai/WORKFLOW.md]] · [[../WORKFLOW.md]]

---

## 1. Amaç

`.opencode/`, CoreMusic'in **OpenCode çalışma ortamı** dizinidir: çalıştırıcı yapılandırması (`opencode.json`), ortam kuralları (`CLAUDE.md`), beceri paketleri (`skills/`), komut dosyaları (`command/`) ve oturum akışları (`.workflows/`) burada yaşar. Bu doküman dizinin **ne işe yaradığını** ve diskteki **gerçek** envanterini tanımlar; mevcut [[CLAUDE]] dosyasına ve kök [[../AGENTS]]/[[../WORKFLOW]] dosyalarına **dokunmaz**, yalnız bağlar.

| Karar | Karşılığı (kaynak) |
|-------|--------------------|
| Vault SSOT, `.opencode/` türevdir | Kök [[../AGENTS]] §9 · bu doküman `authority: Derived` |
| Yeni context dokümanı şablonla üretilir | Guardrail #16 → [[../.ai/.templates/frontend/context-template]] §1 |
| Skill kuralı: şablon zorunlu (Guardrail #16) | [[../.ai/AGENTS]] §14 · [[../.ai/CLAUDE]] §27A |
| Reasoning varsayılanı düşük | [[CLAUDE]] ön başlığı (`reasoningEffort: low`) |
| Commit subagent'ta değil | Kök [[../AGENTS]] §7/§8 |

> **eli10 (basit):** Bu klasör, yapay zekâ aracının çalıştığı oda: ayarlar, hazır komutlar ve oturum reçeteleri burada durur.
> **eli15 (detay):** Ayrı klasördür çünkü çalışma ortamı kurulumu projenin kendi verisinden farklı hızda değişir; kod içine gömülse araç güncellemesi kodu kirletir. İçine yapılandırma, kural özeti, beceri (skill), komut ve akış dosyaları girer. Aracın her oturumunda okunur. Araç sürümü ya da komut değiştiğinde düzenlenir; proje kuralı değil, araç kuralıdır değişen.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `.opencode/**` kök dosyalar + 4 alt dizin envanteri (dosya/alt dizin sayısı) | Vault içeriği → `.ai/` (SSOT; burada üretilmez) |
| `skills/`, `command/`, `.workflows/`, `rules/` envanteri ve işlevi | `node_modules/` içeriği (kurulu paket — içerik okunmaz) |
| Dizin ↔ vault ↔ kök ilişkisi (wiki-link) | Proje kodu (`*.php`, `*.js`, `*.css`) |
| Envanter ↔ vault iddiası çelişki kaydı | `opencode.json` içindeki tüm anahtar değerleri (kısmi — §3.4) |

- **Kullananlar:** OpenCode (boot), tüm agent'lar (skill/command kullanır), MO (senkron), tooling (paket kurulumu).
- **Ön koşul:** Dizin diskte mevcut; sayılar 2026-10-03 ölçümüdür; içeriği okunmayan dosyanın rolü `VERIFICATION REQUIRED` taşır.
- **Not:** `docType: context` = bilgi ağırlıklı; bu dosya kural koymaz, kural için [[CLAUDE]] ve kök [[../AGENTS]]'e gönderir.

> **eli10 (basit):** Bu dosya klasörün tanıtımı: hangi dosya ne işe yarar, hangi klasörde ne var; kural metnini burada tekrar etmez.
> **eli15 (detay):** Kapsam ayrı çizildi ki envanter ile kural karışmasın: buradaki sayılar ölçülmüştür, kurallar ise kök ve vault dosyalarındadır. İçine yalnız dizin yapısı, işlevler ve ilişkiler girer. Aracı kuran kişi burayı okur. Kapsam yazılmazsa bu dosya ikinci bir kural setine dönüşür ve SSOT dağılır.

---

## 3. Mimari

### 3.1 Kök Dosya Envanteri (depth 0 — 2026-10-03 ölçümü: 11 dosya)

| Dosya | Ne için kullanılır | Neyden oluşur | Neden var | Ne zaman düzenlenir |
|-------|--------------------|---------------|-----------|---------------------|
| `CLAUDE.md` (49.007 bayt) | Çalışma ortamı anayasası — kural özeti, boot, yasaklar | Frontmatter + bölüm başlıkları (MAX THINKING, Purpose, Scope, Architecture, Rules, Workflow, Validation, References) | Araç her oturumda kuralları tek yerden okusun | Kural/ADR revizyonu |
| `opencode.json` (44.862 bayt) | OpenCode yapılandırması — model, provider, reasoning, MCP, plugin | JSON anahtarları | Araç davranışı kod olmadan ayarlansın | Araç/servis yapılandırması değişince |
| `opencode.json.bak` · `.bak-faz2` · `.bak-mcp` · `.bak-senior` · `.bak-skillfix` · `.bak-thinking` (6 dosya) | Yapılandırma yedeği (faz/sürüm geçmişi) | JSON kopyaları | Yanlış değişiklikten geri dönülebilsin | Yedekleme işinde — içerik değiştirilmez |
| `package.json` | Paket tanımı — `@opencode-ai/plugin` `1.18.13` | 2 satır JSON | Eklenti sürümü sabitlensin | Eklenti sürüm değişikliğinde |
| `package-lock.json` | Kurulum kilidi | Bağımlılık ağacı | Kurulum tekrarlanabilir olsun | `npm install` çıktısı |
| `.gitignore` | Git dışında tutulanlar (63 bayt) | Yoksayma kuralları | Kurulu paket/yedek commit edilmesin | Yeni yoksayma eklenince |

**Kökdosya eli10/eli15 blokları (başlıca 3 madde):**

**`CLAUDE.md`**
> **eli10 (basit):** Bu odada çalışırken uyacağımız temel kuralların yazılı olduğu dosya.
> **eli15 (detay):** Ayrı dosyadır çünkü araç kuralları vault kuralından farklı hızda değişir; `.ai/CLAUDE.md` ile karıştırılmamalıdır. İçine kural özeti, okuma sırası ve yasaklar girer. Oturum başında okunur. Araç ayarı ya da kural değişince güncellenir; içeriği vault'a taşınmaz, vault'a bağlanır.

**`opencode.json`**
> **eli10 (basit):** Aracın ayar dosyası: hangi modelle çalışılacağı, ne kadar düşünüleceği ve hangi eklentilerin açık olduğu.
> **eli15 (detay):** Ayrı dosyadır çünkü yapılandırma koddur, okunur ve sürümle değişir; içeriğe gömülse araç açılmaz. İçine model, üretkenlik ayarı (reasoning), eklenti ve sunucu tanımları girer. Araç başlarken okunur. Eklenti/servis eklenince düzenlenir; anahtar değerleri belgelere kopyalanmaz.

**Yedek dosyalar (`opencode.json.bak*` — 6 adet)**
> **eli10 (basit):** Ayar dosyasının eski halleri — biri bozulursa geri dönülecek kopyalar.
> **eli15 (detay):** Ayrı ve değişmez dururlar çünkü kurtarma kaynağıdırlar; düzenlenseler geri dönüş kalmaz. İçlerine yeni ayar yazılmaz, yalnızca okunurlar. Yeni bir yapılandırma fazında yenisi eklenir. Silinmezler; temizlik talebinde önce onay istenir.

### 3.2 Alt Dizin Envanteri (2026-10-03 ölçümü: 4 dizin)

| Dizin | Alt dizin | Dosya | Ne için kullanılır | Neyden oluşur | Ne zaman düzenlenir |
|-------|-----------|-------|--------------------|---------------|---------------------|
| `skills/` | 10 | 31 | Hazır beceri paketleri — görev tipine göre yüklenir | 9 `SKILL.md` + `_archive-keep/` 20 dosya + `composer-sync/` 2 · `vault-sync-post/` 2 | Yeni skill eklenince / skill revizyonunda |
| `command/` | 0 | 10 | Komut dosyaları — `/komut` ile çağrılır | `ai-deps` · `changelog` · `commit` · `init-vault` · `issues` · `learn` · `prompt-maker` · `rmslop` · `spellcheck` · `translate` | Komut davranışı değişince |
| `.workflows/` | 0 | 3 | Oturum/iş akışı reçeteleri | `security-audit.md` · `session-init.md` · `vault-sync.md` | Akış kapısı değişince |
| `rules/` | 0 | **0** | Kural dizini — **diskte boş (0 dosya)** | — | İçerik gelince |
| `node_modules/` | — | 3.667 | Kurulu npm paketleri (içerik okunmaz, üretilmez) | Paket ağacı | Yalnız `npm install` |

**Toplam:** `.opencode/` altında recursive **3.693 dosya**; `node_modules/` hariç **55 dosya** (2026-10-03).

**Dizin eli10/eli15 blokları (başlıca 4 madde):**

**`skills/` (10 dizin / 31 dosya)**
> **eli10 (basit):** Hazır görev reçeteleri: doğru işi, doğru sırayla yapmayı öğreten küçük kılavuzlar.
> **eli15 (detay):** Ayrı dizin tutulur çünkü beceri, işe göre yüklenir ve gereksiz yüklemeyi önler. İçine her beceri için bir klasör ve bir kılavuz dosyası girer; kullanılmayanlar `_archive-keep/` altında saklanır. Bir işe başlarken ilgili beceri okunur. Yeni iş tipi çıktığında beceri eklenir; vault kuralı değil, uygulama kılavuzudur.

**`command/` (10 dosya)**
> **eli10 (basit):** Tek tuşla çağrılan hazır komutlar, örneğin görevi düzgün prompta çevirmek ya da commit yazmak.
> **eli15 (detay):** Ayrı dizin çünkü komut, beceriden farklıdır: kısa, tetiklenebilir ve tekrarlanabilirdir. İçine komutun ne yaptığı ve hangi adımları çalıştırdığı yazılır. Kullanıcı komutu yazınca okunur. Komut davranışı değişince dosya güncellenir.

**`.workflows/` (3 dosya)**
> **eli10 (basit):** Oturumun nasıl başlayıp biteceğini anlatan üç reçete: başlatma, güvenlik taraması ve kayıt güncelleme.
> **eli15 (detay):** Ayrı dizindir çünkü akış adımları kural metninden daha sık değişir ve test edilebilir olmalıdır. İçine kapılar (onay, tarama, senkron) adım adım yazılıdır. Oturum başında ve kapanışında okunur. Kapı değişince reçete güncellenir; adım atlanırsa kayıt zinciri kopar.

**`rules/` (0 dosya)**
> **eli10 (basit):** Kural dosyalarının konacağı boş klasör — şimdilik hiçbir şey yok.
> **eli15 (detay):** Dizin diskte var ama içi boş; bu bilgi ölçümdür, dosya varmış gibi yazılmaz. Kural metinleri şimdilik kök ve `.ai/` dosyalarındadır. İçerik eklenince bu satır güncellenir. Boş olduğu için okunması gereken bir şey yoktur.

### 3.3 Skills Envanteri (depth 2 — 9 `SKILL.md` + arşiv)

| # | Skill | Dosya | İşlev (dosya adı + vault kaydı) |
|---|-------|-------|--------------------------------|
| 1 | `agent-debate` | 1 | Agent tartışması / ikinci bakış |
| 2 | `composer-sync` | 2 | Composer/paket senkronu |
| 3 | `context-report` | 1 | Bağlam raporu üretimi |
| 4 | `db-engine` | 1 | Veritabanı tasarımı/mysql sorgu stratejisi |
| 5 | `orchestration` | 1 | Çok-agent orkestrasyonu |
| 6 | `truth-engine` | 1 | İddia doğrulama (disk kanıtı) |
| 7 | `ui-workbench` | 1 | UI geliştirme tezgâhı (ITCSS/BEM) |
| 8 | `vault-sync-post` | 2 | İşlem sonrası vault senkronu |
| 9 | `verify-loop` | 1 | Doğrulama döngüsü |
| — | `_archive-keep/` | 20 | Arşiv — kaldırılmış/beklemede içerik (salt saklama) |

> **eli10 (basit):** Dokuz beceri, dokuz farklı işin kılavuzu; kullanılmayanlar ayrı rafta saklanıyor.
> **eli15 (detay):** Her beceri ayrı klasördedir çünkü tek bir kılavuz tüm işleri taşıyamaz ve gereksiz metin bağlam şişirir. İçine işin amacı, adımları ve doğrulama kriteri yazılır. İlgili işe başlarken okunur. Kural değişince yalnız o beceri güncellenir; `_archive-keep/` içeriği değiştirilmez.

### 3.3.1 Skill Dosyası Blokları (9 madde — eli10 / eli15)

**`agent-debate` (1 dosya)**
> **eli10 (basit):** Bir fikrin ikinci bir gözle, karşı görüşle sınanmasını sağlayan kılavuz.
> **eli15 (detay):** Ayrı beceri çünkü tek başına tartışmayı başlatır ve kararı sağlamlaştırır. İçine tartışma adımları yazılır. Karar anında okunur. Karar yöntemi değişince güncellenir.

**`composer-sync` (2 dosya)**
> **eli10 (basit):** PHP paket kayıtlarını güncellemek için izlenecek yol.
> **eli15 (detay):** Ayrı beceri çünkü paket işlemi geri alınabilir olmalı ve adım adım yürür. İçine paket senkron adımları yazılır. Paket değişikliğinde okunur. Paket kuralı değişince güncellenir.

**`context-report` (1 dosya)**
> **eli10 (basit):** Yapılan işin kısa ve düzenli raporuna dönüştürülmesini anlatan kılavuz.
> **eli15 (detay):** Ayrı beceri çünkü rapor biçimi tekrarlanabilir olmalı, her seferinde yeniden tarif edilmemelidir. İçine rapor başlıkları yazılır. Teslim anında okunur. Rapor formatı değişince güncellenir.

**`db-engine` (1 dosya)**
> **eli10 (basit):** Veritabanı tablolarını ve sorguları düzenleme kılavuzu.
> **eli15 (detay):** Ayrı beceri çünkü veri tasarımının kendi kuralları (normalizasyon, indeks) vardır. İçine tablo/sorgu adımları yazılır. Şema işinde okunur. Veri kuralı değişince güncellenir.

**`orchestration` (1 dosya)**
> **eli10 (basit):** Birden çok ajanla işi paylaştırmanın ve sonucu toplamanın tarifi.
> **eli15 (detay):** Ayrı beceri çünkü iş dağıtımının bağımlılık ve kuyruk kuralları vardır. İçine dağıtım ve toplama adımları yazılır. Çok parçalı işte okunur. Ekip değişiminde güncellenir.

**`truth-engine` (1 dosya)**
> **eli10 (basit):** Söylenen her sayı ve dosya adının gerçek olup olmadığını kontrol etme yolu.
> **eli15 (detay):** Ayrı beceri çünkü doğrulama, üretimden bağımsız ve zorunlu bir kapıdadır. İçine disk kanıtı adımları yazılır. İddia yazmadan önce okunur. Doğrulama kuralı değişince güncellenir.

**`ui-workbench` (1 dosya)**
> **eli10 (basit):** Arayüz kodu yazarken izlenecek katman ve isimlendirme sırası.
> **eli15 (detay):** Ayrı beceri çünkü arayüz kuralları (katman sırası, bileşen adlandırma) kod içinde tekrar edilemeyecek kadar ayrıntılıdır. İçine üretim adımları yazılır. CSS/JS işinde okunur. Tasarım sistemi değişince güncellenir.

**`vault-sync-post` (2 dosya)**
> **eli10 (basit):** İş bitince kayıtların vault'ta güncellenmesini anlatan kapanış kılavuzu.
> **eli15 (detay):** Ayrı beceri çünkü kapanış adımları zorunludur ve oturumlar arası bütünlüğü korur. İçine senkron adımları yazılır. Her işin sonunda okunur. Kapanış kapısı değişince güncellenir.

**`verify-loop` (1 dosya)**
> **eli10 (basit):** Yazılan kodu tekrar tekrar kontrol edip doğrulayan döngünün tarifi.
> **eli15 (detay):** Ayrı beceri çünkü doğrulama döngüsü üretimden ayrıdır ve tekrar eder. İçine kontrol adımları yazılır. Kod/doğrulama işinde okunur. Kontrol kriteri değişince güncellenir.

**Ölçüm notu:** Yukarıdaki sayılar dosya sayımıdır; becerilerin **içerik metinleri okunmadı** — işlev açıklamaları dosya adı ve vault kaydına dayanır (kapsam genişletme isteğinde içerik okunup güncellenir).

---

### 3.4 `opencode.json` Anahtar Görünümü (kısmi — değerlerin tamamı okunmadı)

| Anahtar (gözlemlenen) | Bilinen değer | Kanıt |
|-----------------------|---------------|-------|
| `reasoningEffort` | `low` | [[CLAUDE]] ön başlığı (MAX THINKING madde 1) |
| plugin bağımlılığı | `@opencode-ai/plugin` `1.18.13` | `package.json` |
| Diğer anahtarlar (provider/MCP/model) | OKUNMADI — VERIFICATION REQUIRED | 44.862 bayt JSON, tamamı incelenmedi |

### 3.4.1 Bu Ayarlarla İlgili Kullanım Notu

| # | Not | Kaynak |
|---|-----|--------|
| 1 | `opencode.json` değişikliği önce yedeğe (`*.bak`) yansır; yedeksiz değişiklik önerilmez | §3.1 envanteri |
| 2 | Yapıcı (plugin) sürümü `package.json` ile kilitlenir; ikisi birlikte değişir | `package.json` (1.18.13) |
| 3 | Ayar değişikliği QA kapısının **öncesinde** Security + MO onayından geçer | [[AGENTS]] §3.2 matrisi |
| 4 | Ayar içeriği belgeye kopyalanmaz; yalnız anahtar adı (key) anılır | REDACTED — [[../AGENTS]] §3 |

> **eli10 (basit):** Ayar dosyasına dokunmadan önce bilinmesi gereken dört kısa not.
> **eli15 (detay):** Notlar ayrıdır çünkü ayar hatası tüm oturumları etkiler; yedeği olmayan değişiklik geri alınamaz. İçine kaynak ve onay sahibi yazılır. Ayar işine başlarken okunur. Onay zinciri değişince satır güncellenir.

### 3.5 Dizin ↔ Vault ↔ Kök İlişkisi

| Yön | Ne taşır | Kanıt |
|-----|----------|-------|
| `.ai/` → `.opencode/` | Kural, şablon, envanter (skill bunları uygular) | `.ai/.templates/` · `.ai/AGENTS.md` §14 |
| `.opencode/` → `.ai/` | Senkron ve rapor çıktıları | `skills/vault-sync-post/` (2 dosya), `.workflows/vault-sync.md` |
| kök → `.opencode/` | Master kurallar + commit yetkisi | Kök [[../AGENTS]] §7/§8 · kök [[../WORKFLOW]] |
| `.opencode/` → araç | Model/eklenti/MCP yapılandırması | `opencode.json` · `package.json` |

### 3.6 Vault İddiası ↔ Disk Çelişkileri (disk kazanır)

| İddia (kaynak) | Disk gerçeği (2026-10-03) | İşaret |
|----------------|---------------------------|--------|
| `.ai/AGENTS.md` §14 / `.ai/CLAUDE.md` §27A: "8 aktif skill" | **9 `SKILL.md`** (ek: `verify-loop/SKILL.md`) | VERIFICATION REQUIRED — disk kazanır |
| `.ai/AGENTS.md` §14 notu: `_archive-keep/` 20 benzersiz dosya | `_archive-keep/` **20 dosya** — doğrulandı | dogrulandi |
| `rules/` kural dizini beklentisi | **0 dosya (boş)** | VERIFICATION REQUIRED |
| `.workflows/` akış sayısı | **3 dosya** (`security-audit`, `session-init`, `vault-sync`) — doğrulandı | dogrulandi |

### 3.7 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur (aynı dosyada toplarsak) |
|---|---------|----------|----------------------------------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek dosyada kalır, inceleme (diff) küçük ve güvenli olur | Tek dosyada her şey değişince hangi kuralın ne yaptığı kaybolur, inceleme imkânsızlaşır |
| 2 | **Tek sorumluluk (tek iş)** | Her dosyanın tek görevi ve tek sahibi olur | Dosya büyüyüp okunmaz; "bunu kim yazdı, neden" sorusu cevapsız kalır |
| 3 | **Token tek kaynak** | Yapılandırma tek JSON'da durur (`opencode.json`) | Ayar dağınıksa araç farklı davranır, tekrar tarama gerekir |
| 4 | **Cihaz izolasyonu** | Araç ayarı proje verisine yayılmaz | Ayar kod içine yayılınca sürüm değişimi kodu bozar |
| 5 | **Vendor karantinası** | Kurulu paketler `node_modules/` içinde ayrı durur | Paket içi dosyalar elde düzenlenirse güncellemede kaybolur |

> **eli10 (basit):** Bilgiler tek kocaman dosyaya değil de küçük küçük kutulara konmuş; çünkü kutulardan birini değiştirmek diğerlerine zarar vermez.
> **eli15 (detay):** Ayrı dosyalar çünkü aynı işi yapan herkes aynı yere baksın diye: ayar tek JSON'da (`opencode.json`), kural tek dosyada ([[CLAUDE]]), beceri tek klasörde (`skills/`), komut tek klasörde (`command/`), akış tek klasörde (`.workflows/`) durur. Böylece biri değişince diğeri bozulmaz. Paketler ayrı olunca güncelleme bizim dosyalarımızı kirletmez. Hepsi tek yerde toplansa bakım ve kontrol edilebilirlik kaybolurdu.

---

## 4. Kurallar

Bu dizinde geçerli kuralların tam kaynağı: [[CLAUDE]] (çalışma ortamı) · kök [[../AGENTS]] (master) · [[../.ai/CLAUDE]] (16 Hard Guardrails). Bu dosya yalnız özetler.

| # | Kural (özet) | Neden var | Ref |
|---|--------------|-----------|-----|
| 1 | `.opencode/` türevdir; çelişkide `.ai/` ve kök kazanır | SSOT dağılmasın | Kök [[../AGENTS]] §9 |
| 2 | `skills/` ve `command/` dosyaları şablondan üretilir | Tutarlı denetim (Guardrail #16) | [[../.ai/AGENTS]] §14 |
| 3 | `node_modules/` elle düzenlenmez | Güncelleme kaybı/güvenlik | `.gitignore` + `package-lock.json` |
| 4 | `opencode.json` anahtarları belgeye kopyalanmaz (özel anahtar/sır) | Sızıntı engellenir (REDACTED) | Kök [[../AGENTS]] §3 |
| 5 | Yedek dosyalar (`*.bak*`) silinmez/değiştirilmez | Kurtarma kaynağı | §3.1 |
| 6 | `.opencode/CONTEXT.md` üretilirken mevcut `CLAUDE.md` değiştirilmez | Bağımlı boot zinciri | Görev kuralı |
| 7 | Disk kanıtı olmayan sayı/skill iddiası yazılmaz | Halüsinasyon | [[../.ai/.templates/frontend/context-template]] §4.1 #2 |
| 8 | `rules/` boş olarak yazılır — dosya varmış sayılmaz | Yalan envanter | §3.2 ölçümü |
| 9 | Commit subagent tarafından ATILMAZ | Yetki sınırı | Kök [[../AGENTS]] §7/§8 |
| 10 | Emoji/dekoratif işaret yasak | Metin taşınabilirliği | context-template §4.1 #8 |

> **eli10 (basit):** Bu kurallar, ayar ve klasör dosyalarına yanlış davranmayı engeller; hepsi tek sebepten: araç ayarı kaybolursa proje açılamaz.
> **eli15 (detay):** Kurallar ayrı tabloda durur çünkü araç kuralı ile proje kuralı farklı yerdedir; içeriğe gömülse revizyonda kaybolur. Okunmaları, bu dizinde işe başlamadan sınırları görmeyi sağlar. Yazılmaları, denetimin tekrarlanabilir olmasını garantiler. Kök kural değişince yalnız bu tablo güncellenir.

---

## 5. Workflow

```text
GÖREV → KURAL OKU (kök AGENTS + .opencode/CLAUDE.md)
  → İLGİLİ skill/command SEÇ (skills/ · command/)
  → DISK ÖLÇ (hedef dosya 1. kez oku) → KARAR
  → UYGULA → DOĞRULA (verify-loop · wiki-link · eli10+eli15)
  → .workflows/vault-sync.md ile KAYDET → RAPOR (commit yok)
```

| # | Adım | Çıktı | Neden | Atlarsan ne olur |
|---|------|-------|-------|------------------|
| 1 | Kök + `.opencode/CLAUDE.md` oku | Kural listesi | Ortam kuralları orada | Ortam kuralı ihlali |
| 2 | Görev tipine göre skill seç | Doğru reçete | Her iş tek reçete ile yürümür | Yanlış sıralı/eksik iş |
| 3 | Komut dosyasını çağır (gerekirse) | Tek tuş iş | Tekrarlanabilir adım | Elle yazım hatası |
| 4 | Hedefi ölç + karar ver | Kanıtlı karar | Anti-overthink | Tekrarlı okuma, gecikme |
| 5 | Uygula + doğrula | Temiz çıktı | Kırık iş girmesin | Sessiz hata |
| 6 | `.workflows/vault-sync.md` ile senkron | Güncel vault | Kayıt zinciri | Oturum izi kaybolur |
| 7 | Rapor; **commit ATMA** | Teslim raporu | Yetki sınırı | Yetkisiz commit |

> **eli10 (basit):** Sıra şu: kuralı oku, işe uygun reçeteyi seç, ölç, yap, kontrol et, kaydet ve commit'i başkasına bırak.
> **eli15 (detay):** Adımlar ayrı tutulur çünkü bu dizin hem aracı hem işi besler; sıralama bozulursa ya araç ayarı ya da kayıt zinciri kopar. Kapılar (ölçüm, doğrulama, senkron) atlanırsa hata sonradan bulunur. Commit yetkisi orkestratörde bırakılmıştır. Adım değişirse bu tablo güncellenir, kural metni değil.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: context` |
| 2 | Bölüm yapısı | §1–§7 aynı sıra, ≤3 başlık seviyesi |
| 3 | Placeholder | Dosyada doldurulmamış placeholder kalmadı |
| 4 | Envanter | Kök 11 · skills 10/31 · command 10 · .workflows 3 · rules 0 · node_modules 3667 · toplam hariç 55 — ölçümle eşit |
| 5 | Çelişki | §3.6 tablosu mevcut; "8 skill" iddiası disk ile işaretli |
| 6 | Wiki-link | Wiki-link formatı (çift köşeli); hedefler diskte mevcut |
| 7 | eli10 + eli15 | §1–§5 maddelerinin altında etiketli blok var; eli10 ≤2 cümle, eli15 3-4 cümle |
| 8 | İçerik neden ayrı | §3.7 tablosu 5 satır |
| 9 | Halüsinasyon | Okunmayan `opencode.json` değerleri `VERIFICATION REQUIRED`; `rules/` boş olarak yazıldı |
| 10 | Dokunulmaz | Mevcut `CLAUDE.md` / kök dosyalar / `.ai/` boot dosyaları değiştirilmedi; commit atılmadı |

---

## 7. Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Ortam kuralı (MEVCUT — dokunulmaz) | [[CLAUDE.md]] | Çalışma ortamı anayasası |
| Kök master kurallar | [[../AGENTS]] | §4 keşif · §5 anti-overthink · §7 loop · §9 vault referansları |
| Kök süreç | [[../WORKFLOW]] | Fazlar ve kapılar |
| Vault anayasası | [[../.ai/CLAUDE]] | 16 Hard Guardrails |
| Agent registry | [[../.ai/AGENTS]] | §14 skill kaydı · §24.2 boot listesi |
| Vault süreç | [[../.ai/WORKFLOW]] | Faz kapıları |
| Context şablonu | [[../.ai/.templates/frontend/context-template]] | Bu dokümanın iskeleti (Guardrail #16) |
| Şablon registry | [[../.ai/.templates/index]] | Şablon envanteri |
| Skill kılavuzu | `skills/orchestration/SKILL.md` | Orkestrasyon becerisi (örnek) |
| Komut örneği | `command/prompt-maker.md` | Görev genişletme komutu |
| Akış örneği | `.workflows/vault-sync.md` | Oturum kapanış akışı |
| Paket tanımı | `package.json` · `package-lock.json` | Eklenti sürümü kanıtı |
| Disk kanıtı | `.opencode/` gerçek envanteri | §3 tüm sayıları |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-03 · **docType:** context
