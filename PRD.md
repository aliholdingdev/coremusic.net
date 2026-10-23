---
title: "CoreMusic — Platform PRD (Ürün Gereksinim Belgesi)"
type: prd
category: core
version: 1.2.0
status: active
authority: "Derived — SSOT: .ai/PROJECTS.md (ürün tanımı) + .ai/PRD.md (PRD sistemi)"
updated: 2026-10-23
---

# CoreMusic — Platform PRD

**docType:** prd · **Konum:** depo kökü (`C:\www\coremusic.net`) · **Sorumlu:** MO (vault-updater) + product-owner rolü (kullanıcı)

**Zorunlu Bağlantılar:** [[.ai/PRD.md]] · [[.ai/PROJECTS.md]] · [[.ai/VISION.md]] · [[CONTEXT.md]] · [[AGENTS.md]]

> Bu dosya, **PRD ailesinin indeksidir (SSOT)**: 34 yüzeyin her birinin PRD'si bu tabloda kayıtlıdır.
> Ailesel bağlam (vizyon, 10 özellik, 6 kitle) `.ai/PROJECTS.md`'dedir; burada tekrarlanmaz, yalnız gösterilir.

---

## 1. Amaç

Bu PRD, CoreMusic platformunun **sektör-ötesi ürün gereksinimlerini** tek belgede toplar ve 33 alt PRD'nin
ne içermediğini (kapsam sınırı) tanımlar. Her alt PRD (subdomain, modül, vault yüzeyi) yalnızca kendi
yüzeyinin gereksinimlerini taşır; platform-ötesi gereksinimler (hibrit mimari, veri mülkiyeti, ses kalitesi
taahhüdü) **yalnız burada** tanımlanır ve alt PRD'ler buna bağlanır.

> **eli10 (basit):** Bu dosya, projenin "ne yapacağı"nın ana listesidir; her klasörün kendi küçük listesi
> (alt PRD) ayrıca yazılır ve bu ana listeye bağlanır.
> **eli15 (detay):** Ayrı yazıldı çünkü platform ile modül farklı hızda değişir; hepsi tek dosyada toplansa
> modül değişikliği tüm belgeyi bozar. Okunması, ürünün ana iddiasını ve 34 PRD'nin nereye bağlandığını
> gösterir. Yazılması, gereksinim izlenebilirliğini (PRD → ADR → kod) mümkün kılar. Olmazsa her modül kendi
> versiyonunu icat eder ve platform iddiası parçalanır.

---

## 2. Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Platform-ötesi gereksinimler (§4) | Yüzey-bazlı gereksinimler → ilgili alt PRD (§5 tablosu) |
| PRD ailesi indeksi — 34 yüzey (§5) | Mimari kararlar → ADR'ler (`.ai/.decisions/`) |
| Kabul kriteri kapıları (§6) | Uygulama kodu (`*.php/js/css/sql` — yazım yasak) |
| ADR bağları (§7) | Vizyon/pazar metni → `.ai/VISION.md` (tekrar üretilmez) |

- **Kullananlar:** kullanıcı (ürün sahibi), MO, 11 agent (görev öncesi ilgili alt PRD'yi okur).
- **Ön koşul:** Ürün tanımı SSOT'u `.ai/PROJECTS.md` v3.1.0'dır (2026-10-12); bu PRD ondan türer, onu ezmeyecek şekilde yazılır.
- **Dokunulmaz:** Bu dosya silinmez; yalnız revizyon (sürüm + `updated` artışı) ile güncellenir.

> **eli10 (basit):** Burada platformun ana gereksinimleri ve hangi dosyanın nereye bağlandığı var; detay alt PRD'lerde.
> **eli15 (detay):** Sınır ayrı çizildi çünkü kapsam kayarsa bu dosya ikinci bir PROJECTS.md'ye dönüşür. Okunması,
> hangi sorunun buradan cevaplanacağını gösterir. Sınır yoksa her alt PRD platform iddiasını yeniden yazar ve
> sürüm ayrışır.

---

## 3. Ürün Özeti (kaynak: `.ai/PROJECTS.md` §1 — özet, tekrar değil)

**CoreMusic**, müzik yönetimi, ses işleme, cihaz entegrasyonu ve medya dağıtımını tek platformda birleştiren
kurumsal dijital ses ve medya ekosistemidir. Offline-First mimari + kayıpsız format mülkiyeti + C++20 DSP
çekirdeği + AI katmanı, tek omurgada durur.

| Katman | Taahhüt | Kaynak |
|--------|---------|--------|
| Hibrit çalışma (Online Cloud + Offline-First) | İnternet kopsa yerel akış sürer; bağlantı gelince sessiz eşitleme | PROJECTS.md §2.1 |
| Ses işleme | 32-bit Float dahili boru hattı · True Peak Brickwall Limiter · THD+N <%0.005 · SNR >105dB | PROJECTS.md §2.2-2.3 |
| Hoparlör matrisi | 1.0 Mono → 8.1 Surround (+1 LFE) | PROJECTS.md §2.4 |
| Arayüz | Ambient Aura (canlı tema) + AI Theme Maker | PROJECTS.md §2.5 |
| Çapraz cihaz | Web, TV, araç (`car`), ev medya merkezi (`home`) + multi-room | PROJECTS.md §2.6 |
| Medya depolama | NovaSearchEngine (YouTube arama) · DeezerDownloader (FLAC) · çoklu dışa aktarma | PROJECTS.md §2.7 |
| AI | 2 katmanlı öneri (collaborative + content-based) · BPM/Key/Energy/Mood analizi · otomatik EQ | PROJECTS.md §2.9 |

> **eli10 (basit):** Proje, müziği kesintisiz ve kayıpsız dinlemeyi, her cihazda ve her odada sağlayan tek sistemdir.
> **eli15 (detay):** Özet ayrı bölüm çünkü alt PRD'ler bu satırlara bağlanır; tam metin PROJECTS.md'dedir, burada
> iki kat büyütülmez. Okunması, platformun ana iddiasını tek bakışta verir. Yazılması, alt PRD'lerin hangi platform
> taahhüdünden türüldüğünü kanıtlar. Özet olmazsa her alt PRD kendi "platform" tanımını yazar.

---

## 4. Platform Gereksinimleri (PRD-PLT-001..012)

| ID | Gereksinim | Kabul Kriteri (özet) | İlgili Alt PRD |
|----|-----------|----------------------|----------------|
| PRD-PLT-001 | Offline-First akış: ağ kopsa oynatma durmaz | Ağ kesilmesinde oynatma ≥30sn kesintisiz; reconnect'ta sessiz eşitleme | #1 (kök), #2-6 (subdomain) |
| PRD-PLT-002 | Kayıpsız veri mülkiyeti: FLAC/WAV/MP3 kullanıcıda kalır | Hiçbir akış/yüzey dosyayı silmez veya şifrelemez; dışa aktarma her zaman mümkün | #23 (migrations), #25 (.sql) |
| PRD-PLT-003 | Ses kalitesi taahhüdü: 32-bit Float · THD+N <%0.005 · SNR >105dB | Ölçüm raporu yoksa iddia yazılmaz (`⚠️ VERIFICATION REQUIRED`) | #9-17 (backend), engine PRD (arşiv) |
| PRD-PLT-004 | Tek kimlik: SSO + RBAC (auth.coremusic.net) | Tüm subdomain'ler aynı session/RBAC akışını kullanır; yeni yüzey kendi auth'unu icat etmez | #5 (auth), #22 (auth/include) |
| PRD-PLT-005 | Ortak çekirdek: tüm subdomain'ler `shared/` paketini kullanır | Subdomain içinde middleware/router kopyası yok; Composer path-repo tek sürüm | #7 (shared), #9-17 |
| PRD-PLT-006 | OWASP uyumu: CSRF · CSP · Argon2id · rate limit | `security-engineer` denetimi PASS olmadan yüzey yayına girmez | #12 (Security), #10 (Middleware) |
| PRD-PLT-007 | BCNF veri bütünlüğü: 18 DB · 156 tablo · sıfır veri tekrarı | Migration dışı şema değişikliği yasak; `SELECT *` yasak | #13 (Database), #23-25 |
| PRD-PLT-008 | SPA router tutarlılığı: tüm paneller tek router deseni | Yeni sayfa `shared/src/PageRouter` üzerinden tanımlanır; hash/path sapması yok | #9 (PageRouter), #18-20 (js) |
| PRD-PLT-009 | ITCSS + BEM + `--cm-*` token disiplini | Ham hex/px katman dosyasına yazılmaz; token yalnız `01_Abstracts` | #21 (Css), #18-20 |
| PRD-PLT-010 | WCAG 2.2 AA minimum · touch ≥24×24px · görünür focus 3px | Component State Matrix (8 durum) eksiksiz; `accessibility-tester` PASS | #18-21, #26 (ui-design) |
| PRD-PLT-011 | Zero-hallucination: kanıtsız iddia dosyaya girmez | Bilinmeyen `UNKNOWN` / doğrulanamayan `⚠️ VERIFICATION REQUIRED` | Tüm PRD ailesi |
| PRD-PLT-012 | ADR izlenebilirliği: her gereksinim kararı ADR'ye bağlanır | Yeni ADR numarası `.ai/.decisions/index.md`'den alınır (next: 111) | #27 (.decisions) |

> **eli10 (basit):** Bunlar, hiçbir sayfada veya modülde değişmeyecek 12 ana kuraldır.
> **eli15 (detay):** Tablo ayrı yazıldı çünkü alt PRD'ler buraya bağlanır ve her gereksinim tek kimlikle
> izlenir. Okunması, hangi platform kuralının hangi yüzeyi kapsamını gösterir. Yazılması, gereksinim–karar–kod
> zincirini kurar. Tablo olmazsa alt PRD'ler birbirini çürütür (ör. biri offline'ı kapatır).

---

## 5. PRD Ailesi Indeksi (34 yüzey — SSOT)

> **Durum sütunu:** `YENİ` = bu revizyonda üretiliyor · `YOK` = üretim dışı (bilgi yok/kanıt yok).
> Yollar repo köküne göredir (`C:\www\coremusic.net`).

| # | Yüzey | Yol | Sahip Agent | Ana Gereksinim Bağı | Durum |
|---|-------|-----|-------------|---------------------|-------|
| 1 | Platform kökü | `/PRD.md` (bu dosya) | MO + product | PRD-PLT-001..012 | YENİ |
| 2 | API subdomain | `api.coremusic.net/PRD.md` | backend | PLT-004/005/006/008 | YENİ |
| 3 | Assets subdomain | `assets.coremusic.net/PRD.md` | ui | PLT-009/010 | YENİ |
| 4 | Auth subdomain | `auth.coremusic.net/PRD.md` | security | PLT-004/006 | YENİ |
| 5 | Home subdomain | `home.coremusic.net/PRD.md` | ui + embedded | PLT-001/010 | YENİ |
| 6 | Media subdomain | `media.coremusic.net/PRD.md` | backend | PLT-001/002 | YENİ |
| 7 | Ortak çekirdek | `shared/PRD.md` | backend | PLT-005 | YENİ |
| 8 | Vault | `.ai/PRD.md` | MO | PLT-011/012 | YENİ |
| 9 | PageRouter | `shared/src/PageRouter/PRD.md` | backend | PLT-008 | YENİ |
| 10 | Middleware | `shared/src/Middleware/PRD.md` | security | PLT-006 | YENİ |
| 11 | Api | `shared/src/Api/PRD.md` | backend | PLT-006/008 | YENİ |
| 12 | Security | `shared/src/Security/PRD.md` | security | PLT-004/006 | YENİ |
| 13 | Database | `shared/src/Database/PRD.md` | data | PLT-007 | YENİ |
| 14 | OAuth | `shared/src/OAuth/PRD.md` | security | PLT-004/006 | YENİ |
| 15 | Events | `shared/src/Events/PRD.md` | backend | PLT-005 | YENİ |
| 16 | Cache | `shared/src/Cache/PRD.md` | backend | PLT-005 | YENİ |
| 17 | Session | `shared/src/Session/PRD.md` | security | PLT-004 | YENİ |
| 18 | JS çekirdek | `assets.coremusic.net/js/PRD.md` | ui | PLT-008/010 | YENİ |
| 19 | JS router | `assets.coremusic.net/js/router/PRD.md` | ui | PLT-008 | YENİ |
| 20 | JS coreplayer | `assets.coremusic.net/js/coreplayer/PRD.md` | ui | PLT-001/008 | YENİ |
| 21 | CSS | `assets.coremusic.net/Css/PRD.md` | ui | PLT-009/010 | YENİ |
| 22 | Auth include | `auth.coremusic.net/include/PRD.md` | security | PLT-004/006 | YENİ |
| 23 | Migration | `shared/database/migrations/PRD.md` | data | PLT-002/007 | YENİ |
| 24 | Config | `shared/config/PRD.md` | backend | PLT-005 | YENİ |
| 25 | SQL şema kasası | `.ai/.sql/PRD.md` | data | PLT-002/007 | YENİ |
| 26 | UI-Design vault | `.ai/ui-design/PRD.md` | ui | PLT-010 | YENİ |
| 27 | Karar kasası | `.ai/.decisions/PRD.md` | MO | PLT-012 | YENİ |
| 28 | CI/CD | `.github/PRD.md` | devops | PLT-006/011 | YENİ |
| 29 | Admin subdomain | `admin.coremusic.net/PRD.md` | backend (+security denetimi) | PLT-004/005/006 | YENİ |
| 30 | Node.js yüzeyi | `app-nodejs-projects/PRD.md` | backend | PLT-011 | YENİ |
| 31 | C++ yüzeyi | `app-cpp-projects/PRD.md` | embedded | PLT-003 (araç: `--/neva-engine/`) | YENİ |
| 32 | Claude araç kurulumu | `.claude/PRD.md` | MO | PLT-011 | YENİ |
| 33 | OpenCode çalışma ortamı | `.opencode/PRD.md` | MO | PLT-011 | YENİ |
| 34 | Süreç akışları | `.workflows/PRD.md` | MO | PLT-011/012 | YENİ |

> **eli10 (basit):** Projede 34 ayrı "gereksinim dosyası" var; hangi klasörde hangisi olduğu bu listede.
> **eli15 (detay):** Indeks ayrı taşındı çünkü ailenin tek kopyasıdır; her alt PRD'de kopyalansa sürüm
> ayrışır. Okunması, yüzey–sahip–gereksinim eşlemesini verir. Yazılması, üretim/kapanış sayımının
> referansıdır. Kaybolursa hangi PRD'nin nerede olduğu bilinemez.
>
> **2026-10-23 genişletme (v1.2.0):** +6 yüzey (#29 admin · #30 app-nodejs · #31 app-cpp ·
> #32 .claude · #33 .opencode · #34 .workflows) — 28 → 34 satır; bu revizyonda üretilen 6 PRD
> disk kanıtıyla yazıldı (prompt-maker P0–P3 çıktısı).

### §5.1 Ürün PRD'leri (Neva — harici arşiv; 34 yüzey sayımının dışı)

| # | Ürün | Vault kopyası (SSOT) | Arşiv nüshası (ilk yazım) | Ölçüm | Durum |
|---|------|----------------------|---------------------------|-------|-------|
| N1 | NEVA ENGINE (C++20 ses motoru) | `[[.ai/projects/NevaEngine/coremusic-neva-engine-prd]]` | `G:\Drive'ım\Projelerim\Project-notes\Bayram Ali Prompt Arşivi & Notlar\Coremusic\coremusic-neva-engine-prd.md` | 8004 satır · 465314 bayt · 182 gereksinim ↔ 182 TC | K-6 ONAY (2026-10-23) |
| N2 | NEVA PLAYER (uçtan uca oynatıcı) | `[[.ai/projects/NevaPlayer/coremusic-neva-player-prd]]` | `G:\Drive'ım\Projelerim\Project-notes\Bayram Ali Prompt Arşivi & Notlar\Coremusic\coremusic-neva-player-prd.md` | 8006 satır · 458307 bayt · 215 gereksinim ↔ 215 TC | K-6 ONAY (2026-10-23) |

> **Not:** Bu iki PRD bir repo yüzeyinin değil, **ürünün tamamının** gereksinim belgesidir — §5 tablosundaki 34 yüzeyin dışındadır (başlıkları ürün adıyla açılır). Vault kopyaları SSOT'tur; arşiv nüshaları kullanıcının kişisel not deposunda. Yeni Neva kararı `.ai/.decisions/`'e ADR olarak girer, bu PRD'lere gömülmez.

---

## 6. Kabul Kriteri (aile kapısı)

| # | Kapı | Ölçüt |
|---|------|-------|
| 1 | Frontmatter | Her PRD: 7 alan (`title·type·category·version·status·authority·updated`) |
| 2 | Bölüm iskeleti | Amaç · Kapsam/Kapsam Dışı · Gereksinimler · Kabul Kriteri · ADR bağları · Risk · Sonraki adım |
| 3 | Zero-hallucination | Kanıtsız sayı/id dia `UNKNOWN` / `⚠️ VERIFICATION REQUIRED` |
| 4 | Link | Wiki-link `[[...]]`; diskte olmayan hedef üretilmez |
| 5 | Yetki | `git commit` YOK · silme YOK · kod yüzeyi (`*.php/js/css/sql`) YAZMAZ |
| 6 | Kapsam | Yüzey PRD'si platform gereksinimini tekrar yazmaz, yalnız bağlar |

> **eli10 (basit):** Her PRD dosyası bu 6 kontrolü geçmeden teslim sayılmaz.
> **eli15 (detay):** Kapılar ayrı çünkü üretim ile denetim ayrılmalıdır. Okunması, teslim öncesi öz-denetim
> listesini verir. Yazılması, denetimi tekrarlanabilir kılar. Kapı yoksa "bitti" iddiası kanıtsız olur.

---

## 7. ADR Bağları

| Karar Alanı | ADR | Bağ |
|-------------|-----|-----|
| Offline-First + hibrit mimari | `[[.ai/.decisions/index]]` (SSOT — ADR metinleri frozen) | PLT-001/002 |
| Auth bypass / middleware | ADR-008 (frozen) | PLT-006 · alt PRD #10/#22 |
| DSP donanım modu | ADR-017 (frozen) | PLT-003 |
| BFF/AI decommission | ADR-030 · ADR-084 (2026-10-18, 0 dış referans kanıtı) | alt PRD #7/#11 |
| Native media stack | ADR-0105 (draft → accepted 2026-10-19) | PLT-003 · arşiv engine PRD |
| Yeni ADR numarası | `.ai/.decisions/index.md` v1.5.2 — next: **111** | PLT-012 |

> **eli10 (basit):** Önemli teknik kararlar ADR dosyalarındadır; bu PRD onlara bağ verir, tekrar etmez.
> **eli15 (detay):** Bağlar ayrı çünkü ADR metni frozen'dır (dokunulmaz); PRD karar metnini kopyalasa iki
> sürüm doğar. Okunması, hangi gereksinimin hangi karara dayandığını gösterir. Yazılması, izlenebilirliği
> kanıtlar. Bağ koparsa karar-gereksinim ilişkisi kaybolur.

---

## 8. Risk

| # | Risk | Etki | Azaltma |
|---|------|------|---------|
| 1 | Alt PRD'lerin platform gereksinimini tekrar yazması | Sürüm ayrışması | §6 kapı 6 — yalnız bağ, tekrar yok |
| 2 | `⚠️ VERIFICATION REQUIRED` alanların "tamam" sanılması | Yanlış kabul | Status sütunu (`draft`/`active`) + kapı 3 |
| 3 | 34 dosyanın link sayısının büyümesi | Kırık link riski | G8 kapanışta `wiki-link-check` (repo kökünden) |
| 4 | ADR numarası çakışması (paralel üretim) | SSOT ihlali | Numara yalnız `.ai/.decisions/index.md`'den alınır |
| 5 | Kod yüzeyine yanlışlıkla yazım | Dokunulmazlık ihlali | §6 kapı 5 · subagent commit yasağı |

> **eli10 (basit):** En büyük tehlike: dosyaların birbirini tekrar etmesi ve eski bilginin yeni sanılması.
> **eli15 (detay):** Riskler ayrı çünkü her birinin azaltması farklı sorumluya aittir. Okunması, kapanış
> sırasında hangi kontrolün ihmal edilmeyeceğini gösterir. Yazılması, sorumluluk dağılımını sabitler.

---

## 9. Sonraki Adım

1. G2–G7: §5 tablosundaki 33 alt PRD üretilir (üretim sırası: subdomain → shared/.github → backend modül → frontend → auth+veri → vault → araç yüzeyleri #29–#34; 2026-10-23 itibarıyla 6 araç/yüzey PRD'si bu revizyonda üretildi).
2. G8 kapanış: `.ai/index.md` katalog satırı · `.ai/log.md` append · `.ai/SESSIONS/` kaydı · `wiki-link-check` + final rapor.
3. Commit: **orkestratörde** (kullanıcı onayı ile) — bu revizyonun tüm dosyaları commit edilmedi.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
