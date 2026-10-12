---
title: "CoreMusic — .ai/ Vault Context"
type: docs
category: vault
date: 2026-10-03
updated: 2026-10-12
version: 2.0.0
status: active
authority: "SSOT (klasör kökü) — çelişkide disk kazanır"
docType: context
---

# CoreMusic — .ai/ Vault Context

**docType:** context · **Klasör:** `.ai/` · **Sorumlu:** MO (vault-updater)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[../AGENTS.md]] · [[.templates/index]] · [[SYNC.md]]

---

## 1. Amaç

`.ai/`, CoreMusic'in ikinci beyin (vault) klasörüdür: anayasa, agent registry, süreç, mimari karar özeti, şablon, ekran görseli, SQL şema ve audit trail burada yaşar; tüm alt alan adlarının tek doğruluk kaynağıdır. Bu doküman klasörün ne işe yaradığını ve diskteki gerçek envanterini tanımlar — tüm sayılar bu oturumun disk ölçümüdür (2026-10-12): depth-1 **22 dosya** (21 `.md` + `.env.figma`) · **17 dizin** · recursive **665 dosya / 435 `.md` / 72 dizin**. Mevcut [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] dosyaları değiştirilmez, yalnız bağlanır.

Not: Önceki sürümün K-space katman mimarisi içeriği bu revizyonda kaldırıldı; yerine yenisi üretilmedi.

| Karar | Kaynak (disk) |
|-------|---------------|
| Envanter: 22 kök dosya · 17 dizin · 665 dosya / 435 `.md` / 72 dizin | Recursive ölçüm 2026-10-12 — `index.md` v30.0.0 frontmatter (22/21/17/665/435/72) ile birebir aynı |
| Karar arşivi: 91 karar dosyası (81 `ADR-*.md` + 10 `R-*.md`) · `.decisions/` toplam 97 `.md` | `.decisions/` recursive ölçüm 2026-10-12 — `index.md` FM `total_adr: 91` (80/10/0) ile tutarlı |
| Şablon zorunluluğu (Guardrail #16) → yeni doküman şablondan üretilir | [[.templates/index]] (66 dosya · 13 kategori) · [[.templates/documentation/context-md-template]] |
| Yeni context iskeleti §1–§7 + 5 sütunlu envanter satırı | [[.templates/frontend/context-template]] — çelişkide bu şablon kazanır |
| Boot'ta toplu vault okuma yasak; uzun referans yalnız ihtiyaç anında | [[../AGENTS.md]] boot kuralı · [[CLAUDE.md]] boot protokolü |
| Çelişki (vault ↔ disk) → disk kazanır | [[../AGENTS.md]] §3 Zero-Hallucination · bu dosya §3.4 |
| `log.md` yalnız ekleme (append-only) | [[AGENTS.md]] §5 Domain Boundaries · [[log.md]] |

> **eli10 (basit):** Bu klasör, projenin hafızası: kurallar, kararlar, şablonlar, görseller ve geçmiş burada durur; kod yazmadan önce buraya bakılır.
> **eli15 (detay):** Ayrı klasör tutuldu çünkü kural ve karar koddan farklı hızda değişir; kod içine gömülse her revizyonda kaybolur. İçine anayasa, agent rolleri, süreç, ADR, şablon, token ve ekran görselleri yazılır. Okunur çünkü tek doğruluk kaynağıdır; yazılır çünkü değişiklik imzası ve ölçüm kanıtı taşır. Burası bozulursa projede hangi kuralın geçerli olduğu bilinemez.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `.ai/**` kök dosya envanteri (22 dosya) + 17 alt dizin envanteri | Uygulama kodu → `shared/`, `assets.coremusic.net/`, alt alan adları |
| Boot okuma sırası ve kök dokümanlarla ilişki (wiki-link) | OpenCode çalışma ortamı → `.opencode/` ve kök `.claude/` |
| Envanter ↔ önceki sürüm/`index.md` çelişki kaydı (disk kazanır) | ADR metinlerinin içeriği (frozen — yalnız wiki-link verilir) |
| `.ai/` içinde ne yazılır / ne yazılmaz (yazım protokolü özeti) | `.env.figma` içeriği (REDACTED — hiçbir yere yazılmaz) |

- **Kullananlar:** MO (birincil, vault-updater), 11 agent'ın tamamı (okur), Security (REDACTED denetimi), QA (wiki-link/link kontrolü).
- **Ön koşul:** Hedef dizin diskte mevcut ve sayımlar bu oturumda yapılmış olmalı; kanıtsız sayısal iddia `UNKNOWN` yazılır.
- **Not:** Bu doküman `docType: context` = bilgi ağırlıklıdır; kural koymaz, kurallara referans verir.

> **eli10 (basit):** Bu dosya klasörün haritasıdır: içinde ne var, kim okur, hangi sayı nereden geldi; kuralları burada yeniden yazmaz.
> **eli15 (detay):** Kapsam ayrı çizildi çünkü envanter ile kural birbirine karışırsa eski sayı yeni kural sanılır. İçine yalnız ölçülmüş dosya/dizin sayıları ve ilişkiler girer. Kural okumak isteyen [[CLAUDE.md]]'ye, süreç isteyen [[WORKFLOW.md]]'ye yönlendirilir. Kapsam yazılmazsa bu dosya kısa sürede ikinci bir anayasaya dönüşür.

---

## 3. Mimari

### 3.1 Kök Dosya Envanteri (depth-1 — ölçüm 2026-10-12: 22 dosya = 21 `.md` + `.env.figma`)

| Katman/Dosya | Ne için | Neyden | Neden var | Ne zaman düzenlenir |
|--------------|---------|--------|-----------|---------------------|
| `CLAUDE.md` | Vault anayasası — boot protokolü, Hard Guardrails, yasaklar | Bölüm tabloları + guardrail listesi | Kural metni tek yerde dursun (docType: claude) | Guardrail/ADR revizyonunda (MO + Security) |
| `AGENTS.md` | Agent registry SSOT — 11 uzman + MO, routing, handover, escalation | Rol tabloları, keyword routing, domain boundary | Görevin kime gideceği baştan belli olsun | Yeni agent/rol/kural değişince (MO) |
| `WORKFLOW.md` | Vault süreçleri — fazlar, kapılar, ADR lifecycle | Adım/kapı tabloları, faz kayıtları | Süreç tekrarlanabilir ve denetlenebilir olsun | Faz/kapı değişince (MO) |
| `index.md` | Master navigasyon hub'ı — envanter metrikleri + alt sayfa bağlantıları | §1–§8 indeks tabloları + frontmatter sayaçları | Kayıp dosya farkı için tek katalog | Dosya/ölçüm değişince (MO) |
| `keys.md` | Keyword haritası — kelime → konu/agent yönlendirme | Keyword eşleme satırları | Arama eşlemesi tek yerde dursun | Yeni keyword/konu eklenince |
| `RAG.md` | Retrieval indeksi — "hangi soru → hangi dosya" + pipeline tasarımı | Konu→dosya→keyword tablosu | Boot'ta toplu okuma yerine tek tablo okunsun | Vault'a dosya eklendiğinde/taşındığında |
| `brain.md` | Mimari karar özeti (ADR türevi) | Karar özet satırları | Kod öncesi karar okunsun | Yeni ADR çıktığında |
| `ROLE.md` | Rol → teknoloji/yüzey eşlemesi | Rol satırları | Rol ile stack eşleşsin | Stack değişince |
| `engine.md` | Orkestrasyon motoru — agent koordinasyonu, faz kapanışı | Orkestrasyon tabloları | Çok-agent akış tek yerden yönetsin | Orkestrasyon kuralı değişince |
| `MEMORY.md` | Session hafızası — boot listesi ve oturum durumu | Adım listesi, oturum notları | Oturumlar arası bağlam kaybolmasın | Her oturum kapanışında |
| `log.md` | Audit trail | Yalnız ekleme (append-only) | Kim-ne-ne-zaman tek yönlü kayıt | Yalnız sona ekleme — geçmişe dokunulmaz |
| `glossary.md` | Terimler sözlüğü (kod referanslı) | Terim satırları | Terim tek anlamda kalsın | Yeni terimde |
| `ULTRA-THINKING.md` | Ultra düşünme protokolü + kanıt dosyaları | Protokol adımları | Derin görevlerde tek akış zorunlu olsun | Protokol revizyonunda |
| `VISION.md` | Vizyon, pazar, konumlandırma metni | Vizyon paragrafları | Ürün yönü tek kaynakta dursun | Vizon değişince |
| `PROJECTS.md` | Proje tanımı, yetenekler, hedef kitle, sektörel çözümler | Proje/kitle tabloları | Kapsam tanımı tek yerde dursun | Kapsam değişince |
| `SYNC.md` | Sürekli güncelleme döngüsü SSOT'u — hedef set, kapılar, tazelik ölçütü | Döngü adımları + hedef seti | Güncelleme dağınık olmasın | Hedef set/kapı değişince |
| `CONTEXT.md` (bu dosya) | Vault klasör envanteri + çelişki kaydı | 7 bölüm + 5 sütunlu envanter tabloları | Sayılar ölçümle tek yerde dursun | Envanter ölçümü değişince |
| `CHECKLIST.md` · `PLAN.md` · `PLANNER.md` · `TODO.md` | Plan/kontrol listesi ve planner spec görünümü (içerik bu görevde OKUNMADI) | UNKNOWN (okunmadı) | Görev listesi kural dosyasını kirletmesin | İçerik okunduğunda — VERIFICATION REQUIRED |
| `.env.figma` | Figma erişim anahtarı taşıyan gizli dosya (MEVCUT — 58 bayt; içerik REDACTED) | Şifreli/metin anahtar (okunmadı, yazılmaz) | Anahtar git geçmişine ve belgelere sızmaz | Anahtar yenilendiğinde; hiçbir `.md`/`.json`/`.log`a yazılmaz |

**Kökdosya eli10/eli15 blokları:**

**`CLAUDE.md`**
> **eli10 (basit):** Projede uyulacak temel kuralların ve yasakların yazıldığı anayasa dosyasıdır.
> **eli15 (detay):** Ayrı durur çünkü kural koddan uzun yaşar ve tüm ajanlar aynı metni okumalıdır. İçine boot protokolü, guardrail listesi ve yasaklar yazılır. Okunması, işe başlamadan sınırları gösterir. Yeni guardrail ya da ADR kararı çıktığında düzenlenir; sessizce değişirse denetim imkânsızlaşır.

**`AGENTS.md`**
> **eli10 (basit):** Kimin hangi işi yaptığını ve görevin kime gideceğini gösteren kayıtlar defteridir.
> **eli15 (detay):** Ayrı dosyadır çünkü rol ile kural farklı sorumluluk taşır; roller tek yerde olmazsa görev yanlış ajana gider. İçine 11 uzman rolünün yetkisi, routing ve handover kuralları yazılır. Okunması, işe başlamadan doğru kapıyu bulmayı sağlar. Yeni agent eklendiğinde ya da routing değişince düzenlenir.

**`WORKFLOW.md`**
> **eli10 (basit):** İşlerin hangi sırayla ve hangi kontrollerden geçerek yürütüldüğünü anlatan yol haritasıdır.
> **eli15 (detay):** Ayrı dosyadır çünkü adım ve kapılar zamanla değişir, sabit kural ile karışmamalıdır. İçine faz tabloları, kapılar ve rapor adımları yazılır. Okunması, atlanan bir kontrolün fark edilmesini sağlar. Yeni faz ya da kapı eklendiğinde güncellenir.

**`index.md`**
> **eli10 (basit):** Vault içindeki her şeyi tek sayfada bulmamızı sağlayan fihristtir.
> **eli15 (detay):** Ayrı dosyadır çünkü içerik ile fihrist farklı hızda büyür. İçine kategori tabloları ve `index/` alt sayfalarına bağlantılar yazılır. Okunması, aranan dokümanın yerini kaybetmemeyi sağlar. Yeni dosya eklendiğinde bir satır eklenir; mevcut satırlara dokunulmaz.

**`keys.md`**
> **eli10 (basit):** Hangi kelimenin hangi konuya gittiğini gösteren anahtar listesidir.
> **eli15 (detay):** Ayrı dosyadır çünkü anahtar tablosu kural metninden daha sık değişir ve ayrıca taranır. İçine kelime → konu/agent eşlemesi yazılır. Arama ve routing tartışmasında okunur. Yeni konu açıldığında bir satır eklenir.

**`RAG.md`**
> **eli10 (basit):** "Bu soru hangi dosyada cevaplanır" sorusunun cevabının bulunduğu fihrist sayfasıdır.
> **eli15 (detay):** Ayrı dosyadır çünkü konu→dosya eşlemesi kural metninden bağımsız ve daha sık güncellenir. İçine konu, dosya ve keyword eşlemeleri ile arama tasarımı yazılır. Boot yerine tek tablo okumak için kullanılır. Vault'a dosya eklendiğinde ya da taşındığında güncellenir.

**`brain.md`**
> **eli10 (basit):** Mimari kararların kısa özetinin durduğu defterdir.
> **eli15 (detay):** Ayrı dosyadır çünkü karar özeti ile karar metni (ADR) farklı uzunluktadır; ikisi birleşirse okunmaz. İçine hangi kararın hangi konuda alındığı yazılır. Kod öncesi okunur. Yeni ADR çıktığında özet satır eklenir; karar metni değiştirilmez.

**`ROLE.md`**
> **eli10 (basit):** Her ajanda hangi teknolojinin ve sorumluluğun düştüğünü listeler.
> **eli15 (detay):** Ayrı dosyadır çünkü rol bilgisi görev kurallarından farklıdır ve teknoloji ile birlikte değişir. İçine rol → teknoloji → sorumluluk satırları yazılır. Görev atarken okunur. Stack değişince ilgili satır güncellenir.

**`engine.md`**
> **eli10 (basit):** Birden çok ajanda işin nasıl paylaştırılacağını anlatan motor tarifidir.
> **eli15 (detay):** Ayrı dosyadır çünkü orkestrasyon kuralları ayrı bir uzmanlık alanıdır ve ölçülebilir kapanış kriterleri taşır. İçine orkestrasyon bölümleri ve faz kapanışı listesi yazılır. Çok-agent işte okunur. Faz kapandığında kapanış kriteri güncellenir.

**`MEMORY.md`**
> **eli10 (basit):** Daha önceki oturumlardan aklımızda kalması gerekenlerin tutulduğu defterdir.
> **eli15 (detay):** Ayrı dosyadır çünkü oturum hafızası günlük, kural ise kalıcıdır; birleşirse eski oturum notu kural sanılır. İçine oturum listesi ve son durum yazılır. Oturum başında okunur, kapanışında güncellenir.

**`log.md`**
> **eli10 (basit):** Ne yaptığımızın tarihinin tutulduğu, yalnız üstüne yazılan defterdir.
> **eli15 (detay):** Ayrı ve yalnız eklenebilir tutulur ki geçmiş değiştirilemesin; aksi halde denetim değeri kalmaz. İçine tarih, yapılan iş ve sonuç yazılır. Okunması, hangi oturumda ne olduğunun kanıtını verir. Kapıdan sonra eklenir; eski satır silinemez.

**`glossary.md`**
> **eli10 (basit):** Projede kullanılan özel kelimelerin tek anlamlı karşılıklarıdır.
> **eli15 (detay):** Ayrı dosyadır çünkü terim kuraldan bağımsızdır ve her belgede aynı anlamda kullanılmalıdır. İçine terim → tanım ve kod referansı yazılır. Belge yazarken okunur. Yeni terim girince eklenir; eski tanım sessizce silinmez.

**`ULTRA-THINKING.md`**
> **eli10 (basit):** Zor işlerde izlenecek derin düşünme sırası ve kanıt dosyalarının listesidir.
> **eli15 (detay):** Ayrı dosyadır çünkü akış sabit kuraldan farklıdır ve adım adım uygulanır. İçine protokol adımları ve kanıt dosyaları yazılır. Derin görev başında okunur. Akış değişince yeniden yazılır.

**`VISION.md`**
> **eli10 (basit):** Ürünün nereye gittiğini ve kime hitap ettiğini anlatan vizyon metnidir.
> **eli15 (detay):** Ayrı dosyadır çünkü yön metni teknik belgeden farklı hızda ve dille değişir. İçine vizyon, pazar ve konumlandırma yazılır. Kapsam tartışmasında okunur. Yön değişince güncellenir.

**`PROJECTS.md`**
> **eli10 (basit):** Projede neler yapıldığının, yeteneklerin ve hedef kitlelerin listesidir.
> **eli15 (detay):** Ayrı dosyadır çünkü kapsam tanımı ayrı güncellenir; teknik dosyaya gömülürse ürün konuşması kaybolur. İçine yetenek, kitle ve sektörel çözüm tabloları yazılır. Kapsam sorusunda okunur. Yeni yetenek eklenince düzenlenir.

**`SYNC.md`**
> **eli10 (basit):** Vault'un sürekli güncelleme döngüsünün ve tazelik ölçütünün yazılı olduğu tarifdir.
> **eli15 (detay):** Ayrı dosyadır çünkü güncelleme süreci tek dosyada toplanırsa kural metniyle karışır. İçine döngü adımları, hedef seti ve kapılar yazılır. Düzenli güncelleme oturumunda okunur. Hedef set ya da kapı değişince burası güncellenir.

**`CONTEXT.md` (bu dosya)**
> **eli10 (basit):** Klasörün kaç dosya içerdiğini ve eski bilgilerle disk arasındaki farkları yazan kayıt dosyasıdır.
> **eli15 (detay):** Ayrı yazıldı çünkü sayı iddiaları tek yerde ve ölçümle durmalıdır. Okunması, envanter ve çelişki kaydını verir; kimse tahminle "kaç dosya var" sorusunu cevaplamaz. Envanter ölçümü değişince düzenlenir. Bu dosya olmazsa eski sayılar yeni oturumları yanlış yönlendirir.

**`CHECKLIST.md` · `PLAN.md` · `PLANNER.md` · `TODO.md`**
> **eli10 (basit):** Yapılacakların, planın ve kontrol listelerinin durduğu not dosyalarıdır (içerikleri bu görevde okunmadı).
> **eli15 (detay):** Rolleri okunmadığı için bilinmiyor; dosya adından işlev varsayımı yazılmaz. Ayrı durmaları, iş listesinin kural dosyasını kirletmemesi içindir. İçerik doğrulanmadan bu satır güncellenmez. Durumları `VERIFICATION REQUIRED` olarak kalır.

**`.env.figma`**
> **eli10 (basit):** Dış servise giriş anahtarını taşıyan, kimseye gösterilmeyen gizli dosyadır.
> **eli15 (detay):** Ayrı ve gizli dosyadır çünkü anahtar metin olarak yayılırsa erişim ele geçirilir. Diskte mevcuttur (58 bayt) ancak içeriği hiçbir belgeye kopyalanmaz (REDACTED). Okunması yalnız betikler içindir. Anahtar süresi dolunca yenilenir; kural ihlalinde derhal döndürülür.

### 3.2 Alt Dizin Envanteri (depth-1 — ölçüm 2026-10-12: 17 dizin)

**17 dizin:** `.agents` · `.decisions` · `.diagram` · `.obsidian` · `.personas` · `.png` · `.rules` · `.sql` · `.templates` · `ecosystem` · `index` · `prompts` · `reports` · `scripts` · `servers` · `SESSIONS` · `ui-design`

| Katman/Dosya | Ne için | Neyden | Neden var | Ne zaman düzenlenir |
|--------------|---------|--------|-----------|---------------------|
| `.agents/` | Agent profilleri indeksi | 12 `.md` (AGENTS.md + 11 profil) · 0 alt dizin | Profil ile kök registry ayrı durur (çelişkide kök kazanır) | Profil/stack değişince (MO) |
| `.decisions/` | ADR arşivi — kabul/redded/taslak kararlar | 97 `.md` · 3 alt dizin (accepted 81 · draft 1 · rejected 12) + kök 3 dosya | Karar yaşam döngüsü ayrı dosyalanır | Yeni ADR yazımında; frozen metin değişmez |
| `.diagram/` | Boş — içerik yok | 0 dosya · 0 alt dizin | Gelecek çizim alanı olarak ayrılmış (içerik gelmedi) | İçerik eklenince |
| `.obsidian/` | Obsidian/görünüm yapılandırması | 5 `.json` · 0 alt dizin | Araç ayarı vault içeriğinden ayrılır | Araç ayarı değişince |
| `.personas/` | Kullanıcı persona dosyaları | 84 `.md` · 7 alt dizin (6 persona klasörü + test-senaryolari) | Persona ile kural karışmasın | Persona revizyonunda |
| `.png/` | Görsel SSOT — salt okunur ekran görselleri | 19 `.png` + 4 `.md` = 23 dosya · 3 alt dizin (home-1024 · home-1920 · shared-1024) | Görsel ayrı durur ki mockup kuralı kirletilmesin | Onaylı görsel ekleme/çıkarmada |
| `.rules/` | Vault kural dosyası | 1 `.md` (`senior-mode.md`) | Kural dosyası tek konu taşır | Kural ekleme/revizyonunda |
| `.sql/` | SQL şema ve analiz dosyaları | 20 `.sql` + 5 `.md` = 25 dosya · 4 alt dizin (mssql · mysql · postgresql · sqlite) | DBMS başına ayrılır | Şema değişince |
| `.templates/` | Guardrail #16 şablonları | 66 `.md` · 13 kategori + 7 kök dosya (15 alt dizin, iç içe dahil) | Şablon ayrı olunca yeni dosya tutarlı üretilir | Yeni şablon/kategori eklenince |
| `ecosystem/` | Ekosistem dersleri ve kaynak notları | 13 `.md` · 0 alt dizin | Harici bilgi vault çekirdeğinden ayrılır | Yeni kaynak eklenince |
| `index/` | Master katalog hub alt sayfaları | 5 `.md` (`01-mimari` … `05-referanslar`) · 0 alt dizin | Çok-sayfalı hub yapısı | Bölüm/katalog değişince |
| `prompts/` | Boş — içerik yok | 0 dosya · 0 alt dizin | Prompt arşivi için ayrılmış klasör (dolu değil) | İçerik eklenince |
| `reports/` | Boş — içerik yok | 0 dosya · 0 alt dizin | Rapor çıktısı kural dosyasını kirletmesin | Rapor üretilince |
| `scripts/` | Doğrulama/vault betikleri | 6 `.ps1` + 2 `.mjs` + 1 `.py` + 1 `.md` = 10 dosya | Çalışan kod, okunan dokümandan ayrı hızda değişir | Betik revizyonunda (kökten çalıştırılır) |
| `servers/` | Sunucu ve kurulum notları | 3 `.md` · 0 alt dizin | Ortam bilgisi ayrı durur | Sunucu değişince |
| `SESSIONS/` | Session keşif/rapor kayıtları | 2 `.md` · 0 alt dizin | Keşif çıktısı ayrı durur | Yeni session kapanışında |
| `ui-design/` | Mockup indeksi, screens, flow, prompt, reference, tokens | 297 dosya (121 `.md` + 149 `.png` + 27 `.json`) · 23 alt dizin (6 kök `.md` + 5 kök dizin; 2 boş alt dizin: `flow/spatial`, `flow/voice`) | Görsel ve şablon kendi hiyerarşisinde yaşar | Mockup/tokens değişince |

**Toplam (recursive, 2026-10-12):** 665 dosya · 435 `.md` · 72 dizin · uzantılar: `.md` 435 · `.png` 168 · `.json` 32 · `.sql` 20 · `.ps1` 6 · `.mjs` 2 · `.py` 1 · `.figma` 1. Boş dizin: 5 (`.diagram` · `prompts` · `reports` · `ui-design/flow/spatial` · `ui-design/flow/voice`). `index.md` v30.0.0 frontmatter ile aynı ölçüm.

**Dizin eli10/eli15 blokları:**

**`.agents/`**
> **eli10 (basit):** Her ajanın kendi karakter ve yetenek dosyalarının durduğu klasördür.
> **eli15 (detay):** Ayrı durur çünkü profil bilgisi kök registry'den daha ayrıntılıdır ve farklı hızda değişir. İçine 11 agent profili ve alt registry dosyası yazılır. Görev dağıtımında rolün sınırını görmek için okunur. Profil değişince o dosya güncellenir.

**`.decisions/`**
> **eli10 (basit):** "Neyi, neden böyle yaptık" sorusunun cevabının saklandığı karar defteridir.
> **eli15 (detay):** Ayrı klasördür çünkü kararlar dondurulur ve kodla birlikte değişmez. İçine kabul edilen, reddedilen ve taslak kararlar ayrı alt dizinlerde durur. Kod öncesi okunur ki aynı tartışma tekrarlanmasın. Yeni karar yazılır; dondurulmuş karar metni değiştirilemez.

**`.diagram/`**
> **eli10 (basit):** Şimdilik içi boş, gelecekteki çizimler için ayrılmış klasördür.
> **eli15 (detay):** Dizin diskte mevcut ancak 0 dosya taşır; eski kayıt "yok" diyordu, disk kazanır. Boş olduğu için okunacak içerik yoktur. İçerik eklenince envanter satırı güncellenir. Silinirse gelecek çizim yeri kaybolur.

**`.obsidian/`**
> **eli10 (basit):** Not alma uygulamasının ayarlarını taşıyan küçük yapılandırma klasörüdür.
> **eli15 (detay):** Ayrı durur çünkü araç ayarı vault içeriği değildir ve uygulama tarafından okunur. İçine JSON ayar dosyaları yazılır. Araç davranışı değişince güncellenir. Klasör bozulursa yalnız not uygulamasının görünümü etkilenir.

**`.personas/`**
> **eli10 (basit):** Hangi kullanıcı tipleri için tasarım yaptığımızı anlatan karakter dosyalarının klasörüdür.
> **eli15 (detay):** Ayrı durur çünkü persona, kural ve karardan farklı bir içerik türüdür ve tasarımcı ile testçiyi ilgilendirir. İçine persona dosyaları, test senaryoları ve metodoloji yazılır. Ekran ve test kararlarında okunur. Kitle tanımı değişince güncellenir.

**`.png/`**
> **eli10 (basit):** Ekran resimlerinin (mockup) durduğu, kimse değiştiremeyi düşünmediği görsel klasörüdür.
> **eli15 (detay):** Ayrı ve salt okunur tutulur çünkü görsel onaydan sonra kanıt sayılır; kod içine gömülemez. İçine alt klasörlerde PNG dosyaları ve kısa notlar yazılır. CSS/HTML işine başlarken resme bakma kuralı buraya bağlıdır. Onaylı görsel eklenince ya da çıkarılınca değişir.

**`.rules/`**
> **eli10 (basit):** Vault'a yazarken uyulacak ek kural dosyasının durduğu klasördür.
> **eli15 (detay):** Ayrı durur çünkü kural dosyaları konu başına ayrılır; tek dosyada toplansa okunmaz. İçinde şu an tek dosya vardır (senior-mode). İlgili mod açıkken okunur. Yeni kural kararı eklenince dosya eklenir.

**`.sql/`**
> **eli10 (basit):** Veritabanı tablo ve sorgu dosyalarının, sunucu türüne göre ayrılmış klasörüdür.
> **eli15 (detay):** Ayrı durur çünkü SQL, dokümanla birlikte değişmez ve doğrudan veritabanına uygulanır. İçine 20 SQL dosyası ve analiz notları yazılır. Şema değişikliğinde okunur ve Data Engineer tarafından düzenlenir. Klasör bozulursa veritabanı kurulumu tekrarlanamaz.

**`.templates/`**
> **eli10 (basit):** Yeni dosya yazarken dolduracağımız boş formaların kutusudur.
> **eli15 (detay):** Ayrı klasör, şablon zorunluluğu (Guardrail #16) için şarttır; şablon olmazsa her dosya farklı görünür. İçine kategori kategoride boş iskelet ve kural tabloları yazılır. Yeni doküman üretmeden önce okunur. Yeni kategori eklendiğinde şablon ve registry satırı birlikte güncellenir.

**`ecosystem/`**
> **eli10 (basit):** Dış kaynaklardan topladığımız ders ve bilgi notlarının durduğu klasördür.
> **eli15 (detay):** Ayrı durur çünkü harici bilgi, proje kararından farklı bir statü taşır ve karıştırılırsa "bizim kuralımız" sanılır. İçine kaynak notları yazılır. Araştırma yaparken okunur. Yeni kaynak eklenince dosya eklenir.

**`index/`**
> **eli10 (basit):** Ana fihristin (index.md) bölünmüş hâldeki beş alt sayfasıdır.
> **eli15 (detay):** Ayrı dosyalardır çünkü tek dosya çok uzun olur ve hub yavaş yüklenir. İçine mimari, kurallar, süreç, doğrulama ve referans özetleri yazılır. Hub'dan bağlantı verilerek okunur. Bölüm değişince yalnız o alt sayfa güncellenir.

**`prompts/`**
> **eli10 (basit):** Şimdilik içi boş, prompt arşivi için ayrılmış klasördür.
> **eli15 (detay):** Dizin diskte mevcut ancak 0 dosya taşır; eski kayıt "3 dosya" diyordu, disk kazanır. Boş olduğu için okunacak içerik yoktur. İçerik eklenince envanter satırı güncellenir. Silinirse gelecek arşiv yeri kaybolur.

**`reports/`**
> **eli10 (basit):** Şimdilik içi boş, rapor çıktıları için ayrılmış klasördür.
> **eli15 (detay):** Dizin diskte mevcut ancak 0 dosya taşır; eski kayıt "7 dosya" diyordu, disk kazanır. Boş olduğu için okunacak içerik yoktur. İçerik eklenince envanter satırı güncellenir. Raporların klasörde ayrı durması kural dosyalarını kirletmez.

**`scripts/`**
> **eli10 (basit):** Otomatik kontrol yapan küçük programlar — bağlantıları ve formatı tarar.
> **eli15 (detay):** Ayrı klasördür çünkü çalışan kod, okunan dokümandan farklıdır ve elle karıştırılmamalıdır. İçine doğrulama betikleri (wiki-link, frontmatter, şablon kontrolü, UTF-8 yazım aracı) girer. Kök dizinden çalıştırılırlar; iç dizinden çalıştırıldıklarında yanlış sonuç verirler. Betik davranışı değişince güncellenir.

**`servers/`**
> **eli10 (basit):** Sunucu ve kurulum notlarının durduğu klasördür.
> **eli15 (detay):** Ayrı durur çünkü ortam bilgisi proje kuralından farklıdır ve sunucu başına değişir. İçine kurulum ve sunucu notları yazılır. Ortam değişikliğinde okunur. Sunucu değişince ilgili dosya güncellenir.

**`SESSIONS/`**
> **eli10 (basit):** Oturumlardan kalan keşif ve rapor çıktılarının saklandığı klasördür.
> **eli15 (detay):** Ayrı durur çünkü geçici keşif çıktısı kalıcı kural dosyasını kirletir. İçine tarihli session notları yazılır. Önceki oturumun devamında okunur. Yeni session kapanışında dosya eklenir; eski dosyalar silinmez.

**`ui-design/`**
> **eli10 (basit):** Ekranların resimleri, çizimleri ve ekran kurallarının durduğu klasördür.
> **eli15 (detay):** Ayrı klasördür çünkü görsel ve kod farklı üreticiden gelir; kod yazmadan önce resme bakma kuralı buraya bağlıdır. İçine indeks, ekran tanımları, akışlar ve tasarım değerleri (token) yazılır. CSS/JS işine başlarken okunur. Mockup değişince ilgili dosya güncellenir; okunmadan kod yazmak durdurucu hatadır.

### 3.3 Boot Okuma Sırası ve Kök ↔ Vault İlişkisi

```text
kök AGENTS.md + kök CLAUDE.md (master kurallar ve skill registry)
  → görevin gerektirdiği .ai/ dosyaları (yalnız ihtiyaç anında)
      CLAUDE.md → AGENTS.md → WORKFLOW.md → index.md → ilgili dosya
  → hedef dosya → doküman/kod → doğrulama → log.md (append)
```

| Dış kaynak | `.ai/` karşılığı | Ne taşır |
|------------|------------------|----------|
| kök `AGENTS.md` | [[AGENTS.md]] | Master kuralların vault uzantısı — routing, handover, execution loop |
| kök `CLAUDE.md` | [[index.md]] · [[.templates/index]] | Skill Registry + vault navigasyon bağlantıları |
| `.github/CONTEXT.md` | bu dosya (§7 şablonlar) | Türev klasör envanteri — SSOT `.ai/` tarafında |
| `.github/AGENTS.md` | [[AGENTS.md]] §5 | Dosya sahipliği (registry → `.github/`) |
| `.github/WORKFLOW.md` | [[WORKFLOW.md]] | Türev süreç — SSOT `.ai/` tarafında |

- **Kural:** boot'ta toplu vault okuma yasak; uzun referanslar yalnız görev anında okunur (kök [[../AGENTS.md]] · [[CLAUDE.md]]).
- **Kanıt:** kök boot zinciri + `.ai/AGENTS.md` §24.2 okuma listesi.

> **eli10 (basit):** Önce kök kurallar okunur, sonra yalnız işin tutduğu vault dosyaları; hepsi birden okunmaz.
> **eli15 (detay):** Sıra ayrı tabloda çünkü hangi kaynağın kazanacağı belli olsun: türev dosyalar SSOT'un yerine geçemez. Okunması, boot maliyetini düşük tutar ve yanlış dosyadan başlamayı önler. Yazılması, otorite zincirini görünür kılar. Tablo unutulursa her dosya kendi iddiasıyla bağımsız çalışır ve bilgi ayrışır.

### 3.4 Çelişki Kaydı (vault ↔ disk — disk kazanır · ölçüm 2026-10-12)

| # | İddia (kaynak) | Disk ölçümü (2026-10-12) | Karar |
|---|----------------|--------------------------|-------|
| 1 | Eski `CONTEXT.md` (2026-10-07): "804 dosya / 572 `.md` / 107 dizin" | 665 dosya / 435 `.md` / 72 dizin | **Disk kazanır** — bu dosya yeniden yazıldı |
| 2 | Eski `CONTEXT.md` (2026-10-10): "kök 20 `.md`, `.env.figma` YOK" | kök 21 `.md` + `.env.figma` MEVCUT (58 bayt) | **Disk kazanır** — satır düzeltildi |
| 3 | Eski `CONTEXT.md`: "`prompts/` 3 dosya · 2 alt dizin" | `prompts/` BOŞ (0 dosya) | **Disk kazanır** |
| 4 | Eski `CONTEXT.md`: "`reports/` 7 dosya" | `reports/` BOŞ (0 dosya) | **Disk kazanır** |
| 5 | Eski `CONTEXT.md`: "`.diagram/` YOK" | `.diagram/` MEVCUT, boş (0 dosya) | **Disk kazanır** |
| 6 | Eski `CONTEXT.md` §3.2: "`architecture/` 125 `.md` · 37 alt dizin" | `.ai/architecture/` depth-1 dizin listesinde YOK | **Disk kazanır** — içerik kaldırıldı |
| 7 | Eski `CONTEXT.md`: "`.decisions/` accepted 84 / draft 1 / rejected 12" | accepted 81 (80 ADR + 1 `.md`) · draft 1 · rejected 12 · kök 3 = 97 | **Disk kazanır** |
| 8 | `index.md` v30.0.0 frontmatter: 22 / 21 / 17 / 665 / 435 / 72 / 91 ADR | Bu dosyanın ölçümü ile birebir aynı | Tutarlı — çelişki yok |

> **eli10 (basit):** Eski dosyada yanlış tarihli ve şişirilmiş sayılar vardı; gerçeği bugünkü disk ölçümü söyler.
> **eli15 (detay):** Çelişki tablosu ayrı tutulur çünkü dokunulmaz dosyalar (index.md, kök kurallar) düzeltilemez, yalnız işaretlenir. Okunması, hangi bilginin eski olduğunu gösterir. Yazılması, ölçüm kanıtıyla olur. Bu tablo olmazsa eski "804 dosya" ve "3 dosyalı prompts" bilgisi yeni oturumları yanlış yönlendirir.

### 3.5 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur (aynı dosyada toplarsak) |
|---|---------|----------|----------------------------------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek dosyada kalır, inceleme (diff) küçük ve güvenli olur | Tek dosyada her şey değişince hangi kuralın ne yaptığı kaybolur, inceleme imkânsızlaşır |
| 2 | **Tek sorumluluk (tek iş)** | Her dosyanın tek görevi ve tek sahibi olur (kural → CLAUDE, rol → AGENTS, süreç → WORKFLOW) | Dosya büyüyüp okunmaz; "bunu kim yazdı, neden" sorusu cevapsız kalır |
| 3 | **Token tek kaynak** | Tanım ve değer tek yerde durur (`.templates/`, `ui-design/tokens/`) | Tanım her dosyada tekrar eder → değişince hepsinde tarama gerekir |
| 4 | **Cihaz izolasyonu** | Cihaz/ekran farkı başka katmana yayılmaz | Fark tüm dosyalara sıçrar, uyumsuzluk (drift) doğar |
| 5 | **Vendor karantinası** | 3. taraf ve arşiv içeriği ayrı durur (`.png/`, `ecosystem/`) | Dış içerik içimize karışıp güncellemede kaybolur |

> **eli10 (basit):** Bilgiler tek kocaman dosyaya değil de küçük küçük kutulara konmuş; çünkü kutulardan birini değiştirmek diğerlerine zarar vermez.
> **eli15 (detay):** Ayrı dosyalar çünkü aynı işi yapan herkes aynı yere baksın diye: kural tek yerde (`CLAUDE.md`), rol tek yerde (`AGENTS.md`), süreç tek yerde (`WORKFLOW.md`), karar tek yerde (`.decisions/`), şablon tek yerde (`.templates/`) durur. Böylece bir yer değişince sadece o değişir. Karar ayrı olunca frozen metin korunur, şablon ayrı olunca yeni dosyalar tutarlı üretilir. Hepsi tek dosyada toplansa bakım ve kontrol edilebilirlik kaybolurdu.

---

## 4. Kurallar

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Her sayısal iddia bu oturumun ölçümüyle kanıtlanır; kanıt yoksa `UNKNOWN` | Zero-Hallucination ihlali |
| 2 | Çelişki (vault ↔ disk) → **disk kazanır** + §3.4 tablosunda işaretlenir | SSOT ihlali |
| 3 | Yeni context/CLAUDE/AGENTS/WORKFLOW dokümanı şablondan üretilir (Guardrail #16) | Şablon ihlali, denetlenemez dosya |
| 4 | `log.md` yalnız append; frozen ADR metni kopyalanmaz, yalnız wiki-link verilir | Denetim izi ve karar metni bozulur |
| 5 | `.env.figma` içeriği hiçbir `.md`/`.json`/`.log`a yazılmaz (REDACTED) | Anahtar sızıntısı |
| 6 | Wiki-link `[[...]]` formatı; diskte olmayan hedef üretilmez | Kırık link |
| 7 | Dosya adı değişikliği (taşıma/silme) onaysız yapılmaz | Yüzlerce wiki-link kırılır |
| 8 | Emoji ve doldurulmamış placeholder (çift süslü parantezli desen) üretilen dokümanda kalamaz | Şablon/validator ihlali |
| 9 | Vault kök `CONTEXT.md` ≤660 satır bütçesi korunur | validator fail |
| 10 | Subagent `git commit` ATMAZ — rapor orkestratöre gider | Yetkisiz commit, revert riski |

> **eli10 (basit):** Bu kurallar, vault'a yazarken yanlış yapmayı engeller: bilmediğimiz şeyi uydurmayız, sayıyı ölçeriz, kararı bozmayız.
> **eli15 (detay):** Kurallar ayrı tabloda tutulur çünkü kural ile içerik farklı hızda değişir; içeriğe gömülse revizyonda kaybolur. Okunmaları, yazmadan önce sınırları görmeyi sağlar. Yazılmaları, denetimin tekrarlanabilir olmasını garantiler. Kural değişince yalnız bu tablo güncellenir; envanter sayıları yeni ölçümle değişir.

---

## 5. Workflow

```text
GÖREV → KURAL OKU (kök AGENTS + ilgili .ai dosyası)
  → DISK ÖLÇ (envanter / hedef dosya ilk okumada) → KARAR (ilk okumadan)
  → YAZ (yalnız hedef dosya; log.md append)
  → DOĞRULA (§6 tablosu + wiki-link kontrolü)
  → RAPOR (subagent COMMIT ATMAZ — orkestratöre aittir)
```

| # | Adım | Çıktı | Neden | Atlarsan ne olur |
|---|------|-------|-------|------------------|
| 1 | İlgili kural dosyasını oku | Kural listesi | Vault kuralları orada | Guardrail ihlali |
| 2 | Hedef dizini/dosyayı ölç | Sayı + envanter | Sayısız iddia yazılır | Halüsinasyonlu envanter |
| 3 | Şablonu seç (Guardrail #16) | Doğru iskelet | 4 docType ortak iskelet | Tutarsız, denetlenemez doküman |
| 4 | Yalnız hedef dosyayı yaz | UTF-8, BOM'suz içerik | Tek yazma yüzeyi | Mojibake / yanlış dosya değişikliği |
| 5 | §6 doğrulama + link kontrolü | Temiz rapor | Bozuk link sessiz kalır | Kırık zincir |
| 6 | `log.md`'ye append | Audit satırı | Geçmiş tek yönlü | Denetim izi kaybolur |
| 7 | Rapor + commit yok | Teslim raporu | Yetki sınırı | Yetkisiz commit, revert riski |

> **eli10 (basit):** İş sırası basit: önce kural, sonra ölç, sonra yaz, sonra kontrol et ve kaydettiğin yerde bırak.
> **eli15 (detay):** Adımlar ayrı tutuldu çünkü sıralama hatası doğrudan veri kaybı üretir; ölçmeden yazılan envanter ileride yanlış karar besler. Kapılar (ölçüm, doğrulama, kayıt) atlanırsa hata ancak sonradan fark edilir. Commit yetkisi orkestratörde bırakılmıştır ki tek kalemli tarih oluşsun. Adım değişirse bu tablo güncellenir, kod değil.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | Şablon alanları + `docType: context` (frontend şablon §3.2) |
| 2 | Bölüm yapısı | §1–§7 aynı sıra, ≤3 başlık seviyesi |
| 3 | Placeholder | Değişken yer tutucusu (çift süslü parantezli desen) kalmadı |
| 4 | Envanter | 22 kök dosya · 17 dizin · 665 dosya / 435 `.md` / 72 dizin — bu oturum ölçümüyle eşit |
| 5 | Disk kanıtı | Her sayı list_directory/recursive walk ölçümünden gelir; kanıtsız iddia `UNKNOWN` |
| 6 | Wiki-link | `[[...]]` formatı; §7 hedefleri diskte mevcut |
| 7 | eli10 + eli15 | Her envanter maddesi + §1, §2, §3.3–§3.5, §4, §5 bloklarında çift var (eli10 en fazla 2 cümle, eli15 3-4 cümle) |
| 8 | İçerik neden ayrı | §3.5 tablosu 5 satır (bakım · tek sorumluluk · token tek kaynak · cihaz izolasyonu · vendor karantinası) |
| 9 | Çelişki kuralı | §4'te "disk kazanır" var; §3.4 tablosu dolu |
| 10 | Kaldırılan atıf | Yalnız §1'deki tek satırlık kaldırıldı notu; katman mimarisi tablosu yok |
| 11 | Emoji | Dekoratif emoji yok |
| 12 | Uzunluk | ≤660 satır (vault kök bütçesi) |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Vault anayasası | [[CLAUDE.md]] | Hard Guardrails + boot protokolü |
| Agent registry (SSOT) | [[AGENTS.md]] | Routing, handover, escalation |
| Vault süreç | [[WORKFLOW.md]] | Fazlar, kapılar, ADR lifecycle |
| Master katalog | [[index.md]] | Envanter metrikleri (v30.0.0) |
| Hub alt sayfaları | `index/01-mimari.md` … `05-referanslar.md` | Katalog bölümleri |
| Keyword haritası | [[keys.md]] | Kelime → dosya yönlendirme |
| Retrieval indeksi | [[RAG.md]] | Konu→dosya eşlemesi |
| Mimari karar özeti | [[brain.md]] | ADR türevi özet |
| Karar arşivi | [[.decisions/index]] | ADR listesi (91 karar dosyası) |
| Alt registry | [[.agents/AGENTS]] | 11 agent profili indeksi |
| Şablon registry | [[.templates/index]] | Şablon envanteri (66 dosya · 13 kategori) |
| Bağlayıcı iskelet | [[.templates/frontend/context-template]] | §1–§7 + eli10/eli15 (çelişkide bu kazanır) |
| CONTEXT şablonu | [[.templates/documentation/context-md-template]] | Envanter tablosu formatı + sayım disiplini |
| Mockup indeksi | [[ui-design/01-mockup-index]] | Mockup Before Frontend kapısı |
| Sürekli güncelleme | [[SYNC.md]] | Güncelleme döngüsü SSOT'u |
| Audit trail | [[log.md]] | Değişiklik kaydı (append) |
| Kök master kurallar | [[../AGENTS.md]] | Boot zinciri, Zero-Hallucination, commit kuralı |
| Kök boot dosyası | [[../CLAUDE.md]] | Skill Registry + vault bağlantıları |
| Vault context örneği | [[../.github/CONTEXT.md]] | Tamamlanmış klasör context'i (referans) |
| Doğrulama betiği | `.ai/scripts/validate.mjs` | Bütçe + kontrol listesi |
| Link denetimi | `.ai/scripts/wiki-link-check.ps1` | Wiki-link doğrulama (kökten çalıştırılır) |
| Disk kanıtı | `.ai/` gerçek envanteri | §3 tüm sayıları |

---

**Template Version:** 1.0.0 · **Şablonlar:** `.ai/.templates/frontend/context-template.md` + `.ai/.templates/documentation/context-md-template.md`
**Last Updated:** 2026-10-12 · **docType:** context
