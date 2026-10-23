---
title: "CoreMusic — Depo Kökü Context"
type: docs
category: docs
date: 2026-10-12
updated: 2026-10-12
version: 2.0.0
status: active
authority: "Derived — SSOT: .ai/CONTEXT.md (vault context) + kök AGENTS.md"
docType: context
---

# CoreMusic — Depo Kökü Context

**docType:** context · **Konum:** depo kökü (`C:\www\coremusic.net`) · **Sorumlu:** MO (vault-updater)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[.ai/CONTEXT.md]]

---

## 1. Amaç

Bu dosya, depo kökünde **ne olduğunu** (dosya/dizin envanteri), **sayıların nereden geldiğini** (bu oturumun ölçümü), **okuma sırasını** (boot) ve **hangi kök dosyanın hangi vault dosyasına gösterdiğini** (kök ↔ vault) tek sayfada taşır; kural metni ve vault içeriği üretmez, yalnız gösterir.

Not: Önceki sürümün K-space katman mimarisi atıfları bu revizyonda kaldırıldı; yerine yenisi üretilmedi.

| Karar | Kaynak (disk) |
|-------|---------------|
| Top-level envanter: **17 dizin, 19 dosya, 36 giriş** | `list_directory` ölçümü, 2026-10-12 |
| Kök `.md` dosyası: **7** (`AGENTS` · `CLAUDE` · `CONTEXT` · `README` · `RAG` · `WORKFLOW` · `notes`) | aynı ölçüm, 2026-10-12 |
| Alt alan adı dizinleri: **5** (`api` · `assets` · `auth` · `home` · `media`) | `list_directory` + her birinde `CONTEXT.md` bulunduğu glob ile doğrulandı (2026-10-12) |
| PRD ailesi: **28 yüzey** — kök `PRD.md` (SSOT indeksi) + `.ai/PRD.md` (vault) + 26 alt PRD | 2026-10-23 revizyonu — indeks: `[[PRD.md]]` §5 |
| **2026-10-23 düzeltme notu:** diskte **6** alt alan adı dizini var (`admin` · `api` · `assets` · `auth` · `home` · `media`); `admin.coremusic.net/` boot seti (CONTEXT/AGENTS/CLAUDE) **YOK** — üstteki "5" ölçümü 2026-10-12 tarihlidir, `admin` o tarihte ölçülmemiş olabilir | `list_directory` ölçümü 2026-10-23 |
| `bin/` **1 dosya** · `shared/` **15 üst düzey giriş** · `node_modules/` **85 üst düzey giriş** | `list_directory` ölçümü, 2026-10-12 |
| `--/` **297 dosya, 65 dizin** (içerik sayılmadı, yalnız adet) | `directory_tree` ölçümü, 2026-10-12 (iki ayrı sayım aynı sonucu verdi) |
| Kök `CLAUDE`/`WORKFLOW`/`RAG` dosyaları **pointer**'dır | frontmatter `authority` okuması, 2026-10-12 |
| Vault context SSOT: **`.ai/CONTEXT.md`** | kök `AGENTS.md` §9 + `.ai/CONTEXT.md` disk varlığı |

> **eli10 (basit):** Bu dosya, kökte ne olduğunu ve nereden okunacağını anlatan kısa bir kılavuzdur.
> **eli15 (detay):** Ayrı yazıldı çünkü sayı iddiaları tek yerde ve ölçümle durmalı; başka dosyaya dağılsa eski sayılar yeni oturumu yanıltır. Okunması, envanteri, boot sırasını ve otorite zincirini tek bakışta verir. Yalnız envanter ölçümü değişince düzenlenir. Olmazsa her oturum aynı sayımı tekrar yapar ve kök dosyalar yanlış SSOT'a bağlanır.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Kök dosya ve dizin envanteri (ölçülen sayılar) | `.ai/` vault içeriği (SSOT → [[.ai/CONTEXT.md]]; burada üretilmez) |
| Boot okuma sırası + kök ↔ vault ilişkisi (§3.2) | Alt dizin envanterlerinin ayrıntısı (her alt dizinin kendi `CONTEXT.md`'si) |
| Kök `.md` dosyalarının rolleri (§3.1) | PHP / JS / CSS kaynak kodu (ilgili katmanın işi) |
| Vault ↔ disk çelişki kaydı (§3.3) | Mimari katman atıfları (bu revizyonda kaldırıldı) |

- **Kullananlar:** MO (vault-updater, bu dosyanın sahibi), tüm agent'lar (oturum başı kök okuması), Claude Code / OpenCode (boot).
- **Ön koşul:** Envanter bu oturumda ölçülmüş (2026-10-12); kanıtı olmayan sayısal iddia `UNKNOWN` taşır.
- **Dokunulmaz:** Kök `AGENTS.md`, `CLAUDE.md`, `WORKFLOW.md`, `README.md` bu görevde değiştirilmez.

> **eli10 (basit):** Burada yalnız kökün kendi envanteri anlatılır; kural metni, vault içeriği ve kaynak kod konu dışıdır.
> **eli15 (detay):** Sınır ayrı yazıldı çünkü kapsam kayarsa bu dosya kural defterine veya vault kataloğuna dönüşür ve tekrar okunmaz. Okunması, hangi sorunun buradan cevaplanacağını gösterir. Yazılması, ölçümün ve çelişki kaydının nerede durduğunu sabitler. Sınır yoksa her görev aynı işi başka dosyada yeniden kurar.

---

## 3. Mimari

### 3.1 Envanter (5 sütunlu — ölçüm 2026-10-12: 17 dizin, 19 dosya)

| Katman/Dosya | Ne için | Neyden | Neden var | Ne zaman düzenlenir |
|--------------|---------|--------|-----------|---------------------|
| `CONTEXT.md` (bu dosya) | Kök envanteri + boot özeti + otorite tablosu | 7 bölüm, 5 sütunlu tablo, bu oturum ölçümü | Sayılar tek yerde ve ölçümle dursun | Envanter ölçümü değişince |
| `CLAUDE.md` (kök) | Boot talimatı + Skill Registry SSOT | pointer (frontmatter: `SSOT: .ai/CLAUDE.md` · v3.3.0) | Anayasa ayrı, boot köprüsü ayrı dursun | Yalnız vault-sync (MO) |
| `AGENTS.md` (kök) | Ana agent kuralları: akış, execution loop, persona hiyerarşisi | master kurallar (frontmatter: `v1.2.0`) | Kanonik kök kural tek dosyada olsun | Kural revizyonu (kullanıcı onayı) |
| `WORKFLOW.md` (kök) | Süreç pointer'ı | pointer (frontmatter: `SSOT: .ai/WORKFLOW.md` · v3.2.0) | Uzun süreç kökte tekrarlanmasın | Yalnız vault-sync (MO) |
| `README.md` (kök) | İnsan okuru için proje tanıtımı | bölümlü readme (başlık + teknoloji rozetleri) | Proje ilk buradan tanıtılır | Tanıtım/teknoloji değişince |
| `RAG.md` (kök) | Retrieval pointer'ı | pointer (frontmatter: `SSOT: .ai/RAG.md`; `.ai/keys.md` bağlantılı) | "Hangi soru → hangi dosya" tek yerde | Retrieval indeksi değişince |
| `notes.md` (kök) | Serbest çalışma notları (CSS/düzenleme hatırlatmaları) | düz metin notlar | Geçici not kalıcı dosyaya karışmasın | Not kapanınca (elle) |
| Diğer kök dosyaları (**12**) | Repo ayarları + çalışma çıktıları | 5 `coremusic_php_*.log` + 5 nokta dosyası (`.claudeignore` `.cursorrules` `.gitattributes` `.gitignore` `.gitleaks.toml`) + `package.json` + `package-lock.json` | Ayar, log ve manifest ayrı durur | Araç ayarı / bağımlılık / log rotasyonu değişince |
| `.ai/` (dizin) | Vault: anayasa, ADR, envanter, şablonlar | kök `.md` pointer'larının hedefi (ör. `.ai/CONTEXT.md`) | Tüm uzun bilgi tek kasada | Yalnız MO (vault-updater) |
| `shared/` (dizin) | Ortak PHP çekirdeği + test | **15 üst düzey giriş** (6 dizin: `src` `tests` `config` `database` `vendor` `.phpunit.cache` · 9 dosya: `composer.json` `composer.lock` `phpunit.xml` `test_bypass.php` `.phpunit.result.cache` + 4 boot md) | Çekirdek tek yerde, subdomain'ler tekrar etmesin | Backend + QA (onayla) |
| `bin/` (dizin) | Tek araç betiği | **1 dosya** (`api-key-create.php`) | Üretim betiği köke serilmesin | Güvenlik/DevOps (onayla) |
| `api` · `assets` · `auth` · `home` · `media.coremusic.net/` (**5 dizin**) | Alt alan adı uygulamaları | her biri kendi `CONTEXT.md` + `CLAUDE`/`AGENTS`/`WORKFLOW` setine sahip (glob doğrulaması) | Her subdomain kendi kuralıyla yaşar | İlgili subdomain agent'ı |
| `node_modules/` (dizin) | npm bağımlılıkları (test araçları) | **85 üst düzey giriş** | Üçüncü taraf kod kendi kutusunda | `package.json` değişince (npm) |
| `--/` (dizin) | Depoya alınmamış çalışma alanı | **297 dosya, 65 dizin** (içerik sayılmadı) | Atık/özel dosyalar ana ağa karışmasın | Depo sahibi (elle) |
| Diğer kök dizinleri (**7**) | Altyapı ve araç çıktıları | `.claude/` · `.git/` · `.github/` · `.opencode/` · `.workflows/` · `playwright-report/` · `test-results/` | Gizli ayar, CI ve test çıktısı ayrı durur | İlgili araç değişince |

**`CONTEXT.md` (bu dosya)**
> **eli10 (basit):** Kökte hangi dosya olduğunu ve nereye bakılacağını yazan kılavuz.
> **eli15 (detay):** Ayrı yazıldı çünkü sayı iddiaları ölçümle tek yerde durmalı; başka dosyaya dağılsa eski sayı yeni oturumu yanıltır. Okunması envanter, boot ve otorite zincirini verir. Yalnız ölçüm değişince yazılır. Olmazsa her oturum aynı sayımı tekrar yapar.

**`CLAUDE.md` (kök)**
> **eli10 (basit):** Oturum başında okunan kısa talimat ve beceri listesi.
> **eli15 (detay):** Ayrıdır çünkü anayasa (`.ai/CLAUDE.md`) ile boot köprüsü farklı hızda değişir; ikisi tek dosyada birleşse her kural revizyonunda kök taranır. Okunması, beceri listesinin ve vault bağlantılarının nerede olduğunu gösterir. Yalnız vault-sync tarafından düzenlenir. Silinirse oturum beceri listesini bulamaz.

**`AGENTS.md` (kök)**
> **eli10 (basit):** Tüm ajanların uyması gereken ana kuralların defteri.
> **eli15 (detay):** Tek kanonik kök kural dosyasıdır; detay `.ai/AGENTS.md` (registry SSOT) ile tamamlanır. Okunması, akışı, yasakları ve yetki sınırlarını verir. Değişikliği kullanıcı onayı ister. Bu dosya değişirse herkes kendi kuralını uygular ve commit/dispatch sırası bozulur.

**`WORKFLOW.md` (kök)**
> **eli10 (basit):** Sürecin nerede olduğunu gösteren yol işareti.
> **eli15 (detay):** Ayrı durur çünkü uzun süreç metni vault'ta yaşar (ADR-042); kökte kopyalansa iki sürüm ayrışır. Okunması, gerçek iş akışına giden kapıyı gösterir. Yalnız vault-sync düzenlenir. Kopya yazılırsa eski faz tabloları geçerli sanılır.

**`README.md` (kök)**
> **eli10 (basit):** Projenin insanlara kendini tanıttığı sayfa.
> **eli15 (detay):** Ayrıdır çünkü okuru AI ajanı değil insandır; kural dosyasına gömülse okunmaz. Okunması, proje kapsamını ve teknolojiyi verir. Tanıtım değişince düzenlenir. Silinirse depoya ilk bakan projeyi tanıyamaz.

**`RAG.md` (kök)**
> **eli10 (basit):** "Şu soru hangi dosyada?" sorusunun cevabını gösteren işaret.
> **eli15 (detay):** Ayrı çünkü retrieval indeksi tek SSOT'ta (`.ai/RAG.md`) durur; kökte kopya iki indeks demektir. Okunması, arama hedefine giden yolu verir. Retrieval değişince vault-sync günceller. Koptuğu an ajanlar yanlış dosyayı okur.

**`notes.md` (kök)**
> **eli10 (basit):** Aklına gelen kısa yapılacak iş listesi.
> **eli15 (detay):** Ayrı durur çünkü düzenli değil, serbest akışlıdır; kural dosyasına girse denetim imkânsızlaşır. Okunması, açık kalan küçük işleri gösterir. Elle düzenlenir. Silinirse unutulmuş CSS/düzenleme işleri kaybolur.

**Diğer kök dosyaları (12)**
> **eli10 (basit):** Ayar dosyaları, log kayıtları ve Node.js manifestosu.
> **eli15 (detay):** Bunlar ayrı durur çünkü log üreten, ayar okuyan ve paket kuran mekanizmalar farklıdır; tek dosyada birleşse log büyürken ayarlar da kaybolur. Okunması, hata kaynağını ve araç ayarlarını gösterir. Log runtime'ın, nokta dosyaları aracın, manifest npm'in sorumluluğundadır. Karışırsa hata izi ve sürüm kilidi kaybolur.

**`.ai/` (dizin)**
> **eli10 (basit):** Projenin kasa odası: bütün uzun ve hassas bilgiler burada.
> **eli15 (detay):** Ayrıdır çünkü kasaya yazma yetkisi tektir (MO) ve kök dosyalar yalnız ona gösterir. Okunması, anayasa/ADR/şablona giden yolu verir. Yalnız MO yazar. Dizin köke yayılırsa SSOT dağılır ve çelişki çözülemez.

**`shared/` (dizin)**
> **eli10 (basit):** Bütün subdomain'lerin kullandığı ortak PHP kodu ve testleri.
> **eli15 (detay):** Ayrıdır çünkü ortak çekirdek tekrarlanırsa her subdomain'de farklı sürüm oluşur. Okunması, kodun nerede başladığını (src/tests/composer) gösterir. Backend + QA onayıyla değişir. Kayarsa beş subdomain aynı hatayı ayrı ayrı taşır.

**`bin/` (dizin)**
> **eli10 (basit):** Elle çalıştırılan tekil üretim betiklerinin klasörü.
> **eli15 (detay):** Ayrıdır çünkü betikler uygulama kodu değildir ve köke serilirse yanlışlıkla çalıştırılabilir. Okunması, hangi aracın elde çalıştırıldığını gösterir. Güvenlik/DevOps onayıyla değişir. Silinirse anahtar üretimi elde yapılır.

**5 alt alan adı dizini**
> **eli10 (basit):** api, assets, auth, home ve media için ayrı ayrı web uygulamaları.
> **eli15 (detay):** Her biri ayrıdır çünkü alt alan adları ayrı dağıtım ve ayrı kural seti taşır; hepsi köke bağlanırsa bir deploy hepsini bozar. Okunması, hangi uygulamanın nerede durduğunu gösterir. Her biri kendi `CONTEXT.md`'sinden güncellenir. Dizinler kayarsa routing ve deploy hedefi bulunamaz.

**`node_modules/` (dizin)**
> **eli10 (basit):** İndirilen üçüncü taraf JavaScript paketleri.
> **eli15 (detay):** Ayrıdır çünkü dış kod kendi kutusunda durmalı; el ile düzenlenmez ve commit edilmez. Okunması yalnız bağımlılık sorununda gerekir. `package.json` değişince npm yeniden üretir. Elle dokunulursa sürüm kilidi bozulur.

**`--/` (dizin)**
> **eli10 (basit):** Ana ağa karıştırılmayan özel/atalama çalışma alanı (içeriği burada anlatılmaz).
> **eli15 (detay):** Ayrı durur çünkü dosya sayısı büyük ve kapsamı bu belgenin dışında; içeriği belgelemek envanteri şişirir. Okunması gerekmez, yalnız varlığı ve adedi (297 dosya) bilinir. Dizin sahibi düzenler. Köke taşınırsa atık kod üretime karışır.

**Diğer kök dizinleri (7)**
> **eli10 (basit):** Gizli ayarlar, git/CI klasörleri ve test çıktıları.
> **eli15 (detay):** Ayrı dururlar çünkü her biri farklı bir aracın alanıdır (editor, git, GitHub Actions, Playwright, test); köke serilseler araçlar kendi dosyasını bulamaz. Okunmaları yalnız ilgili görevde gerekir. İlgili araç değişince güncellenir. Karışırsa CI ve test çıktıları kaynak kod sanılır.

### 3.2 Boot Okuma Sırası ve Kök ↔ Vault İlişkisi

Kaynak: kök `AGENTS.md` §7 (Execution Loop) + §14 (Boot Zinciri) — okuma ölçümü 2026-10-12.

```text
1) kök AGENTS.md — ana kurallar (akış, zero-hallucination, execution loop)
2) kök CLAUDE.md — Skill Registry + vault bağlantıları (boot zincirinde birlikte okunur)
3) SKILL EŞLEŞMESİ — kök CLAUDE.md §Skill Registry; eşleşme varsa ilk işlem Skill tool ile yüklemek
4) görevle ilgili .ai/ dosyası — yalnız ihtiyaç anında @ ile (toplu okuma yasak)
5) hedef dosya → üretim → doğrulama → .ai/log.md (append) → kapanış .ai/SYNC.md
```

| Bu kök dosyası | Gösterdiği vault dosyası | Ne taşır |
|----------------|--------------------------|----------|
| `CONTEXT.md` (bu dosya) | `[[.ai/CONTEXT.md]]` | Vault envanteri + boot ilişkisi (SSOT) |
| `CLAUDE.md` | `[[.ai/CLAUDE.md]]` | Anayasa + Hard Guardrails; kökte ayrıca Skill Registry |
| `WORKFLOW.md` | `[[.ai/WORKFLOW.md]]` | Fazlar, kapılar, otomasyon senaryoları |
| `RAG.md` | `[[.ai/RAG.md]]` · `[[.ai/keys.md]]` | Konu → dosya indeksi + keyword haritası |
| `AGENTS.md` | `[[.ai/AGENTS.md]]` | Agent registry (routing, handover, escalation) |
| `README.md` | — (pointer değil, özgün içerik) | İnsan okuru tanıtımı |

> **eli10 (basit):** Okuma sırası: önce ana kurallar, sonra beceri listesi, en son yalnız ihtiyaç olan vault dosyası.
> **eli15 (detay):** İlişki ayrı tabloda çünkü çelişkide hangi kaynağa bakılacağı belli olsun: kök dosyalar türevdir, vault kazanır. Okunması, boot sırasında sırayı ve otorite zincirini gösterir. Yazılması, kök dosyaların ikinci SSOT sanılmasını engeller. Tablo unutulursa her dosya kendi iddiasıyla bağımsız okunur ve bilgi ayrışır.

### 3.3 Çelişki Kaydı (vault ↔ disk — disk kazanır)

| # | İddia (kaynak, okuma ölçümü 2026-10-12) | Disk ölçümü (2026-10-12) | Karar |
|---|------------------------------------------|---------------------------|-------|
| 1 | Eski `CONTEXT.md` (bu dosya, 1 nüsha): frontmatter `version: 1.0.0` | aynı dosyanın alt satırı `v1.1.0` yazıyordu | **Disk/kazanan yok** — dosya v2.0.0 olarak tamamen yeniden yazıldı |
| 2 | Eski `CONTEXT.md` başlığı dekoratif emoji taşıyordu | Şablon kuralı: emoji yasak | **Kaldırıldı** |
| 3 | Eski `CONTEXT.md` boot özeti "13 proje skill" diyordu | Skill sayısı kök `CLAUDE.md` §Skill Registry'tedir (bu oturumda o bölüm ölçülmedi) | Bu dosyada skill sayısı yazılmaz → `UNKNOWN` |

> **eli10 (basit):** Eski dosyadaki tutarsız ve emoji'li satırlar burada kayda geçti; gerçeği disk söyler.
> **eli15 (detay):** Çelişki tablosu ayrı tutulur çünkü dokunulmaz dosyalar düzeltilemez, yalnız işaretlenir. Okunması, hangi bilginin eski olduğunu gösterir. Yazılması ölçüm kanıtıyla olur. Tablo olmazsa eski iddialar yeni oturumları yanlış yönlendirir.

### 3.4 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur (aynı dosyada toplarsak) |
|---|---------|----------|----------------------------------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek dosyada kalır, inceleme (diff) küçük ve güvenli olur | Tek dosyada her şey değişince hangi kuralın ne yaptığı kaybolur, inceleme imkânsızlaşır |
| 2 | **Tek sorumluluk (tek iş)** | Her dosyanın tek görevi ve tek sahibi olur (kural → AGENTS, envanter → CONTEXT) | Dosya büyüyüp okunmaz; "bunu kim yazdı, neden" sorusu cevapsız kalır |
| 3 | **Token tek kaynak** | Ortak değer tek yerde durur (ör. envanter sayıları bu dosyada) | Her dosyada ayrı sayı → tutarsızlık, hangisinin doğru olduğu tartışılmaya başlanır |
| 4 | **Cihaz izolasyonu** | Depo kökü ile subdomain kuralı birbirine karışmaz | Kök kuralı alt alan adına, alt alan kuralı köke sıçrar; drift doğar |
| 5 | **Vendor karantinası** | Üçüncü taraf kod (`node_modules/`, `shared/vendor/`) kendi klasöründe durur | Dış kod içimize karışıp güncellemede kaybolur, güvenlik yamaları şaşar |

> **eli10 (basit):** Bilgiler tek kocaman dosyaya değil de küçük küçük kutulara konmuş; çünkü kutulardan birini değiştirmek diğerlerine zarar vermez.
> **eli15 (detay):** Ayrı dosyalar çünkü aynı işi yapan herkes aynı yere baksın diye: kural `AGENTS.md`'de, envanter bu dosyada, uzun bilgi `.ai/` içinde durur. Böylece biri değişince sadece o değişir, diğerleri bozulmaz. Hepsi tek dosyada toplansa bakım ve kontrol edilebilirlik kaybolurdu.

---

## 4. Kurallar

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Her sayısal iddia bu oturumun ölçümüyle kanıtlanır; kanıt yoksa `UNKNOWN` | Zero-Hallucination ihlali |
| 2 | Çelişki (vault ↔ disk) → **disk kazanır** + §3.3 tablosunda işaretlenir | SSOT ihlali |
| 3 | Kök `AGENTS.md` · `CLAUDE.md` · `WORKFLOW.md` · `README.md` bu görevde değiştirilmez | Dokunulmazlık ihlali |
| 4 | Bu dosya yalnız envanter/boot/ilişki taşır; kural metni veya vault içeriği üretilmez | İkinci SSOT doğması |
| 5 | Wiki-link `[[...]]` formatı; diskte olmayan hedef üretilmez | Kırık link |
| 6 | Emoji yasak; şablon yer tutucusu (değişken placeholder) kalamaz | Şablon ihlali (Guardrail #16) |
| 7 | Yeni context §1–§7 sırası ve şablon frontmatter'i ile üretilir | Şablon ihlali |
| 8 | Subagent `git commit` ATMAZ — rapor orkestratöre gider | Yetkisiz commit, revert riski |

> **eli10 (basit):** Bu kurallar, dosyanın yalan sayı taşımasını, ikinci kaynak olmasını ve izinsiz değişmesini engeller.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü kural ile envanter farklı hızda değişir; tabloya gömülse her ölçümde revize edilir. Okunmaları, işe başlamadan sınırları gösterir. Yazılmaları, denetimi tekrarlanabilir kılar. Kural değişince yalnız bu tablo güncellenir; envanter tablosu sessizce değişmez.

---

## 5. Workflow

```text
ÖLÇ (kök envanter — bu oturumda) → YAZ (şablondan, yalnız CONTEXT.md)
  → ÇELİŞKİ KAYDI (§3.3 — vault ↔ disk → disk kazanır) → DOĞRULAMA (§6 tablosu)
  → RAPOR (commit ORCHESTRATÖRDE — subagent atmaz)
```

| # | Adım | Çıktı | Neden | Atlarsan ne olur |
|---|------|-------|-------|------------------|
| 1 | Şablon oku: [[.ai/.templates/documentation/context-md-template.md]] + [[.ai/.templates/frontend/context-template.md]] | İskelet + format kuralları | Şablonsuz üretim yasak (Guardrail #16) | Tutarsız, denetlenemez dosya |
| 2 | Kök envanterini ölç (`list_directory` + `directory_tree`) | 17/19/7/5/297 sayıları | Sayı iddiası ölçümle kanıtlanır | Halüsinasyon, yanlış "eklendi/silindi" iddiası |
| 3 | Bu dosyayı §1–§7 + 5 sütun tablo + eli10/eli15 ile yaz | Bu dosya | Tutarlı iskelet | Şablon dışı dosya |
| 4 | §3.3 çelişki tablosunu güncelle | Karar satırları | Eski bilgi yeni oturumu yanıltır | Eski sürüm/emoji iddiası sürer |
| 5 | §6 doğrulama tablosunu çalıştır | 11/11 | Teslimden önce kanıt | Hatalı dosya teslim edilir |
| 6 | Rapor (index kaydı + commit orkestratörde) | Rapor | Yetki sınırı (kök `AGENTS.md` §7) | Yetkisiz commit, revert |

> **eli10 (basit):** İş sırayla: say, şablona göre yaz, farkları kaydet, kontrol et, haber ver; commit'i sen atmazsın.
> **eli15 (detay):** Adımlar ayrı satırlarda çünkü her adımın çıktısı ve "atlarsan ne olur" gerekçesi ölçülür. Okunması, kapıları önceden bilmeyi sağlar. Yazılması, tekrarlanabilir ve denetlenebilir bir akış bırakır. Ölçüm ve doğrulama atlanırsa hatalı iddia dosyaya girer ve sonradan pahalıya bulunur.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | Şablon §3.2 kalıbı: title, type, category, date, updated, version, status, authority + `docType: context` |
| 2 | Bölüm yapısı | §1–§7 aynı sıra, ≤3 başlık seviyesi |
| 3 | Placeholder | Şablon yer tutucusu (değişken kalıbı) kalmadı |
| 4 | Envanter | 17 dizin · 19 dosya · 36 giriş · 7 kök `.md` · 5 subdomain · 297/65 (`--/`) bu oturum ölçümüyle uyumlu |
| 5 | Disk kanıtı | Her sayı `list_directory` / `directory_tree` ölçümünden gelir; kanıtsız iddia `UNKNOWN` |
| 6 | Wiki-link | `[[...]]` formatı; §7 hedefleri diskte mevcut |
| 7 | eli10 + eli15 | §1, §2, §3.1 (15 madde), §3.2, §3.3, §3.4, §4, §5, §6, §7 bloklarında çift var (eli10 en fazla 2 cümle, eli15 3-4 cümle) |
| 8 | Çelişki kuralı | §4'te "disk kazanır" var |
| 9 | Boot sırası | Kök ↔ vault ilişkisi tablosu §3.2'de var |
| 10 | Emoji + kaldırılan atıf | Dekoratif emoji yok; kaldırma yalnız §1'deki tek satır |
| 11 | Uzunluk | 300 satır altı (kök pointer dosyası) |

> **eli10 (basit):** Bu liste, dosya teslim edilmeden önce kendini kontrol etmek için.
> **eli15 (detay):** Doğrulama ayrı bölüm çünkü üretim ile denetim aynı oturumda birbirine karışmamalı. Okunması, hangi kriterin kanıtlanacağını gösterir. Yazılması, denetimi tekrarlanabilir kılar. Tablo kalmazsa "bitti" iddiası kanıtsız kalır.

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Kök master kurallar | [[AGENTS.md]] | §7 execution loop, §14 boot zinciri, commit kuralı |
| Kök boot + Skill Registry | [[CLAUDE.md]] | Boot köprüsü ve beceri listesi (SSOT) |
| Kök süreç pointer'ı | [[WORKFLOW.md]] | Vault süreç bağlantısı |
| Kök tanıtım | [[README.md]] | İnsan okuru için proje özeti |
| Kök retrieval pointer'ı | [[RAG.md]] | Retrieval + keyword bağlantısı |
| Vault context SSOT | [[.ai/CONTEXT.md]] | Tam envanter, boot ilişkisi, disk çelişkileri |
| Vault anayasası | [[.ai/CLAUDE.md]] | Hard Guardrails |
| Agent registry (SSOT) | [[.ai/AGENTS.md]] | Routing, handover, escalation |
| Vault süreç | [[.ai/WORKFLOW.md]] | Fazlar ve kapılar |
| Retrieval indeksi | [[.ai/RAG.md]] · [[.ai/keys.md]] | Konu → dosya + keyword haritası |
| Master katalog | [[.ai/index.md]] | Vault dosya kataloğu |
| Audit trail | [[.ai/log.md]] | Değişiklik kaydı (append) |
| Session güncelleme | [[.ai/SYNC.md]] | Kapanış senkronu |
| Bağlayıcı iskelet | [[.ai/.templates/frontend/context-template.md]] | §1–§7 + eli10/eli15 (çelişkide bu kazanır) |
| CONTEXT şablonu | [[.ai/.templates/documentation/context-md-template.md]] | 5 sütunlu envanter + sayım disiplini |
| Template registry | [[.ai/.templates/index.md]] | Şablon kayıt satırı |
| Örnek yeniden yazılmış context | [[.github/CONTEXT.md]] | Bu şablonun bitmiş uygulaması (referans) |

> **eli10 (basit):** Buradaki bağlantılar kural, envanter ve şablon dosyalarına giden yolların listesi.
> **eli15 (detay):** Referanslar ayrı bölüm çünkü her iddianın dayanağı tek yerde toplanmalı; dağınık olsa link denetimi yapılamaz. Okunması, çelişkide hangi kaynağa gideceğini gösterir. Yazılması, tüm hedeflerin diskte bulunabilirliğini kanıtlar. Kayıp olursa bu dosya kendini dayanaksız bırakır.

---

**Template Version:** 1.0.0 · **Şablonlar:** `.ai/.templates/frontend/context-template.md` + `.ai/.templates/documentation/context-md-template.md`
**Last Updated:** 2026-10-12
