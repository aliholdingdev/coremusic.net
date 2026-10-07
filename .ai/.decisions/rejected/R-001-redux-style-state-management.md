---
title: "CoreMusic — R-001: Redux-Style State Management (REDDEDİLDİ — Framework yasağı)"
type: "architecture-decision"
category: "frontend"
date: "2026-10-01"
updated: "2026-10-01"
version: "1.0.0"
status: "rejected"
authority: "SSOT — R-001 red kararı: CoreMusic frontend'ine Redux tarzı (action/reducer/store/dispatch) state management library GIRMEZ. Gerekçe: ADR-001 framework/iskelet yasağı (frozen, immutable) + ADR-004 'R-001 ile aynı gerekçe ailesi' ('+': kod yüzeyinde redux/zustand/mobx izi 0 — 2026-10-01 taraması). Yerini alan: ADR-001-vanilla-js-itcss (frontend temeli) + ADR-027-dual-mode-storage-strategy (state'in kalıcı yüzeyi) + ADR-004 (saf istemci router). Bu dosya salt-okunur seri (rejected/) — değiştirilmez, yalnız referanslanır."
governance: "Red Team · Human Mode · Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 RED DOĞRULANDI)"
---

# CoreMusic — R-001: Redux-Style State Management (Rejected)

> **Durum:** rejected (**debate ✅ TAMAMLANDI — 3 tur / 20 persona, 19 kabul / 1 çekimser / 0 red → RED DOĞRULANDI**) — **Tarih:** 2026-10-01 — **Debate:** ✅ **TAMAMLANDI** (2026-10-01, 3 tur / 20 persona, 19/1/0 → **RED DOĞRULANDI**; 3 şart §5.3) — **Tech Lead:** ✅ (2026-10-01) — **Arch Lead:** ⏳
> **Seri:** `.ai/.decisions/rejected/` (salt-okunur) — **Slug:** `R-001-redux-style-state-management` (dizin otoritesi: [[../index]] **satır 126** — dosya adı ile birebir hizalı ✅)
> **Dizin satırı:** `| [[R-001-redux-style-state-management]] <!-- dead-link: R-001-redux-style-state-management no source 2026-09-24 --> | Redux-Style State | Framework yasağı |` — `<!-- dead-link ... -->` bayrağı **bu işlemde DOKUNULMADI** (düzeltme son sıfırlamaya ertelendi → §5.1/4 + §7.1)
> **İlgili kararlar:** [[../accepted/ADR-001-vanilla-js-itcss]] (yerini alan — framework/iskelet yasağı) · [[../accepted/ADR-004-multi-domain-spa]] (`:160` "R-001 (Redux — framework bağımlılığı) ile aynı gerekçe ailesi") · [[../accepted/ADR-027-dual-mode-storage-strategy]] (yerini alan — state'in kalıcı/depolama yüzeyi) — karar dizini [[../index]] §5.
> **Tur 1 düzeltmesi:** ADR-045 → `[[../accepted/ADR-045-multi-domain-view-mode-architecture]]` · ADR-046 → `[[../accepted/ADR-046-cross-view-state-preservation]]` — **diskte dosya VAR** (2026-10-01 glob doğrulaması; ADR-004 `:19` "henüz yazılmadı" notu eskimiş) → wiki-link kuruldu, ⚠️ kaldırıldı · `rejected/index.md` §5 tablosu **boş** (kayıt satırı yok → §7.1/2 — debate kapsamı dışı, dokunulmadı) · Redux npm indirme/indirge-büyüklüğü rakamları **tek kaynaklı** (§1.3 notu; şart 2 → §5.3).

---

## 1. Bağlam (Context)

CoreMusic frontend'i **framework'süz** kurulmuştur: ADR-001 (frozen, `ADR-001 → ADR-037` immutable kapsamı) vanilla JS ES6+ + ITCSS'i zorunlu kılar, React/Vue/Angular/Svelte ve build sistemi yasaktır. Redux tarzı state management (global store + action + reducer + subscribe) bu yasağın **doğrudan hedefidir**: bir kez kabul edilse kod tabanı tek bir state kütüphanesine bağlanır, o kütüphane çıkarılamaz hale gelir (iskelet bağımlılığı). Karar dizini bu seçeneği çoktan reddetmiştir (`index.md:126` — "Redux-Style State | Framework yasağı") ama **red metni hiç yazılmamıştır**: elde yalnız dizin satırı + `<!-- dead-link ... -->` notu vardır. Bu dosya, o satırın **gerekçeli red kaydıdır** — yeni bir karar değil, mevcut red'in (a) gerekçe, (b) güncel web araştırması, (c) yerini alan eşleme, (d) yeniden değerlendirme koşulu ile sıfırdan yazımıdır.

### 1.1 Mevcut Durum (disk kanıtı — dürüst etiket, 2026-10-01 taraması)

| # | İddia | Kanıt | Etiket |
|---|-------|-------|--------|
| 1 | Red kaydı var mı? | [[../index]] `:126` → `[[R-001-redux-style-state-management]]` + "Framework yasağı" + `<!-- dead-link ... no source 2026-09-24 -->` | ✅ **KAYITLI** (dizin satırı tek kanıt; red metni bu işlemde yazılıyor) |
| 2 | Bu işlem öncesi dosya var mıydı? | `.ai/.decisions/rejected/` içinde yalnız `CLAUDE.md` + `index.md`; `R-001*` = **0 dosya** | ❌ **YOKTU** → bu işlemde yazılıyor (klasör zaten var — `rejected/` oluşturulmadı) |
| 3 | Red gerekçesi başka yerde yazılı mı? | ADR-001 `:32` "R-001 Redux-state … hepsi framework/over-engineering gerekçeli" · ADR-001 `:126` "R-001 (Redux-state) emsal red kararıyla uyumlu" · ADR-001 `:201` (R-001/R-003/R-004 reddi) · ADR-004 `:160` "R-001 (Redux — framework bağımlılığı) ile aynı gerekçe ailesi" · ADR-004 `:242` (dizin §5 R-001 reddi) | ✅ **5 referans** — ama hiçbiri red'in kendi metni değil, **gerekçe ailesi** |
| 4 | Yerini alan frontend temeli diskte? | [[../accepted/ADR-001-vanilla-js-itcss]] **VAR** (frozen kapsamı 001-037 → immutable) | ✅ **IMPLEMENTED** |
| 5 | Yerini alan state/depolama kararı diskte? | [[../accepted/ADR-027-dual-mode-storage-strategy]] **VAR** — title: "Dual-Mode Storage Strategy (Online + Offline Yerel Mod …)" (görev tanımındaki "dual-mode playlist state" adı **dosyayla birebir değil** → gerçek ad budur) | ✅ **VAR** · ⚠️ ad düzeltmesi rapor-only |
| 6 | State'in kalıcı yüzeyi kodda? | ADR-027 `:37` → localStorage `SidebarManager.js:49,62,70,76-82`, `gender-select.js:29,61`, `welcome-modal.js:30,55-58`, `device-loader.js:356-367` (sessionStorage) | ✅ **IMPLEMENTED** (küçük veri cache; IndexedDB/SW/sync PLANNED — ADR-027 `:54`) |
| 7 | Redux/state library kodda izi? | `*.js/*.ts/*.mjs/*.cjs/*.jsx/*.tsx/*.json` taraması (node_modules/vendor/.git/.bak hariç): `redux\|zustand\|mobx` → **kod 0 isabet**; 7 isabet yalnız vault JSON'ında: `tokens-1047` Figma raw ×3 + `tokens-1042/1047` "State: Zustand / Pinia / Redux Toolkit" **metin etiketi** (`tokens-1042.json:750,22044` · `tokens-1042-1920.json:1467,59331`) — **tasarım mockup metni, kod DEĞİL** | ✅ **state library = 0** |
| 8 | `signals` izi? | `assets.coremusic.net/js/router/config/signal-utils.js:1,5` → `combineSignals(...)` **AbortSignal birleştirme** (router `AbortController` yüzeyi), reaktif state library'si **DEĞİL** | ✅ **0** (yalnız DOM signal API'si) |
| 9 | Diğer accepted ADR'ler? | ADR-004 `:30,:143` → istemci router 28 dosya saf History API ("hiçbirinde framework import'u yok") · ADR-004 `:160` R-001 ile aynı gerekçe ailesi | ✅ IMPLEMENTED |
| 10 | Eski seri cross-view state ADR'leri? | [[../accepted/ADR-045-multi-domain-view-mode-architecture]] + [[../accepted/ADR-046-cross-view-state-preservation]] **VAR** (2026-10-01 glob doğrulaması; ADR-004 `:19` notu eskimiş) | ✅ **DOSYA VAR** (Tur 1 slug düzeltmesi — şart 1) |

> **Ders notu:** bu red **hiçbir zaman kodda denenmedi** — yani "reddedildi" = "yazılmadı ve yazılmasına izin verilmedi"; gerekçe dizin satırında tek satır, uzun gerekçe ise yalnız başkalarının ADR'lerinde parmak izi olarak duruyordu. Bu dosya o boşluğu kapatır.

### 1.2 Sorun Tanımı

1. **Red kararı kanıtsız duruyor.** `index.md:126` bir sonuç cümlesi ("Framework yasağı") ama **ne 2025-26 ekosistem kanıtı ne yeniden değerlendirme koşulu ne yerini alan eşleme** yazılı — gelecekteki biri "redux yasak mı, neden, ne zaman tekrar sorulur?" sorusuna vault'tan cevap bulamıyor.
2. **Gerekçe ailesi parçalı.** Uzun gerekçe ADR-001 (`:126`, `:201`) ve ADR-004 (`:160`) içinde dağınık; red'in kendi dosyası olmadığı için `grep R-001` sonucu "gerekçe = başka ADR'nin alternatif satırı" düzeyinde kalıyor.
3. **Güncellik sorunu.** Redux 2025-26'da varsayılan olmaktan çıktı (§1.3); red'in bugün hâlâ geçerli olup olmadığı **araştırılmadan** yazılmadı — "eski bilgiye dayanan karar" sınıfı (şablon §1.1 uyarısı).
4. **Ölçüsüz eşik.** Vanilla state yüzeyinin nerede "elle yönetim şişmesine" döneceği (ne kadar store/pub-sub kodu, kaç abone) **hiç ölçülmedi** → yeniden değerlendirme tetikleyicisi tanımsız.

### 1.3 Web'den Araştırma Raporu & Sonuçları

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "Redux status 2025 2026 legacy Zustand Jotai signals state management shift" → (2) "TC39 signals proposal status 2026 stage validators framework signals" → (3) "vanilla JavaScript state management patterns without framework 2025 pub/sub module store event emitter" → (4) "React Server Components 2026 client-side state less server state shift state management" |
| Web Search **Konusu** | **(1)** Redux'un 2025-26 konumu: varsayılanlıktan çıkış, Zustand/Jotai'ye kayış, npm indirme ve bundle karşılaştırması · **(2)** TC39 Signals teklifinin aşama durumu (standart primitive ne zaman gelir) · **(3)** framework'süz vanilla state kalıpları: pub/sub, mini-store, event emitter — Redux'suz yaşam mümkün mü · **(4)** React Server Components'in istemci state'ine etkisi (state sunucuya kayıyor mu). |
| Web Search **Bağlam** | CoreMusic: ADR-001 framework/iskelet yasağı frozen; kodda state library **0** (§1.1/7); vanilla router + localStorage cache IMPLEMENTED (ADR-004/027); hedef soru — *"Red bugün hâlâ doğru mu, yoksa endüstri 'küçük state library'ye mi geçti, bu geçiş CoreMusic'i etkiler mi?"* Araştırma 2026-10-01'de yapıldı; **4 sorgu / 32 isabet** listelendi (tekrar kontrolü: r/reactjs iki sorguda → benzersiz ~31). Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (Test-Path = True; öncelik: resmi doküman → vendor → uzman blog). |
| Web Search **Kısa Açıklama** | **(1)** "Redux is no longer the default": Zustand ham npm indirmelerde Redux'u geçti (Şub 2026 verisi — **tek kaynak**); hafif library'ler (Zustand ~3KB, Jotai ~4KB, @preact/signals ~4KB) Redux Toolkit + react-redux (~15KB) karşısında bundle avantajlı (**tek kaynak benchmark**). **(2)** TC39 Signals **Stage 1** — dil primitive'i olarak yolun başında; "Signals stuck in committee" değerlendirmesi yaygın. **(3)** framework'süz state pub/sub + 30 satırlık `createStore` (`getState/setState/subscribe`) ile çözülür — "You do not need Redux" (kendi kanıtı: reducer/store/boilerplate'siz basitlik). **(4)** RSC istemci state'ini **azaltır ama ortadan kaldırmaz**; state farkındalığı "nerede yaşayacak" sorusuna kayıyor. |
| Web Search **Uzun Açıklama** | **(1) Redux 2025-26:** Sascha Becker (saschb2b) veri karşılaştırması — npm haftalık indirmelerde Zustand'ın Redux'u geçmesi "2025-2026'nın gerçek hikâyesi"; Redux yüksek sayısı legacy projelerden + RTK'nın kendisinden geliyor; GitHub sağlığı: Zustand 57.1K star / 4 açık issue, Jotai 21.1K; son yayın: Zustand 5.0.11 (2026-02-01), Jotai 2.18.0 (2026-02-19), RTK 2.11.2 (2025-12-14). dev.to/jsgurujobs karar matrisi: bundle/perf/bellek tabloları (RTK parse 34ms ↔ Zustand 8ms; bellek 3.2MB ↔ 2.1MB — **tek kaynak, bağımsız ölçüm YOK**). nextfuture "Redux era is over": greenfield'da Redux'un gerekçeli kullanımı daralmış (time-travel DevTools, 50+ kişilik ekip konvansiyonu, mevcut kod bakımı). svar.dev / State of React 2025: Zustand en popüler ikinci library (RTK çiftinin ardından). **(2) TC39 Signals:** github.com/tc39/proposal-signals ortak yön dokümanı (Promises/A+ benzeri öncül); reptile.haus (2026) teklifi **Stage 1** olarak raporluyor; Eisenberg (JSNation/TC39 oturumu) erken dönemde "henüz Stage 1'de değiliz, Stage 0 gibi düşünün" demişti → **ortak nokta: standard değil, üretim primitive'i değil**. **(3) Vanilla kalıplar:** CSS-Tricks "state management system with vanilla JS" — Store (`state/actions/mutations`) + PubSub `stateChange` olayı; grizzlypeak "State Management Without Frameworks" — EventBus `on/off/emit` + ~30 satır `createStore` ("30 satır, Redux'un binlerce satır yaptığı işi yapar"); Mark Onyango / namastedev / dev.to aynı kalıbı (Pub-Sub + observer + event emitter) doğrular; r/Frontend tartışması pub/sub önerisini tekrar üretir. **(4) RSC:** Josh Comeau (`'use client'` sınırı = state'in nerede yaşadığını belirler), mayank.co (bundle küçülür, veri sunucuya kayar), dev.to rsc-state (RSC ile 6 ay mücadele → sunucu-state library'si üretildi — yani RSC **yeni bir state sorunu** da açıyor), medium Rameshkan (RSC client state'i **ortadan kaldırmaz**, daha bilinçli yerleşim ister). |
| Web Search **Paragraf Veri Uzun** | 4 sorgu / 32 isabet: **(1)** saschb2b "React State Management in 2026: A Data-Driven Comparison" (Zustand indirmelerde Redux'u geçti; RTK 2.11.2 / Zustand 5.0.11 / Jotai 2.18.0 sürüm-tarihleri) · dev.to "State Management in 2026: Zustand vs Jotai vs Redux Toolkit vs Signals" (bundle: Zustand ~3KB, Jotai ~4KB, @preact/signals ~4KB, RTK+react-redux ~15KB; perf/bellek/parse tabloları) · nextfuture "React State Management 2026" ("Is Redux dead? No, but its role has narrowed") · tech-insider "7x Bundle Gap" · javascript.plainenglish "Redux-Free World Possible?" · svar.dev (State of React 2025: Zustand 2.) · makersden "State Management Trends 2025" · reddit r/reactjs (2026: "just use Zustand" kanıksanmış) — **8**. **(2)** github.com/tc39/proposal-signals · eisenbergeffect.medium "A TC39 Proposal for Signals" (Stage 0/1 erken dönem) · reptile.haus "JavaScript Signals Are Coming" (Stage 1, 2026) · youtube "Signals JavaScript's Stage 1 TC39 Proposal" · youtube JSNation US 2024 (Daniel Ehrenberg — Standardizing Signals) · reddit r/javascript (teklifin herkese açık oluşu tartışması) · discourse.aurelia (TC-39 Signals standard teklifi) · ecmascript-daily (proposal-signals, 2024-04-17) — **8**. **(3)** css-tricks "Build a state management system with vanilla JavaScript" · python.plainenglish "Mastering State Management Without a Framework" · grizzlypeak "State Management Without Frameworks — You do not need Redux" (~30 satır createStore) · medium Mark Onyango "State Management Patterns in Vanilla JavaScript" · namastedev "Vanilla Patterns That Scale" · dev.to "State Management in Vanilla JS" · youtube PubSub Design Pattern · reddit r/Frontend "Non-framework Javascript state management" — **8**. **(4)** joshwcomeau "Making Sense of React Server Components" · mayank.co "RSC: Good, Bad, Ugly" · dev.to "I Built a State Management Library After Fighting RSC" · medium "State Management Strategies for RSC in Next.js" · github pmndrs/zustand discussion #2200 (RSC + Zustand kafa karışıklığı, 2025-05-13) · reddit r/reactjs (server push hissiyatı) · youtube "RSC Change Everything" · linkedin RSC deneyimi — **8**. |
| Web Search **Sonucu** | **(1) Red destekleniyor + kısmi karşıt bulgu:** endüstri Redux'tan uzaklaşıyor — ama bu CoreMusic'e "küçük library al" demiyor; kayışın tamamı **React ekosistemi içinde** (Zustand/Jotai/TanStack Query hepsi React binding'li) → ADR-001 framework yasağı altında **seçenek alanı dar** (kaynak: saschb2b, dev.to, nextfuture, svar.dev — 4). **(2) Red destekleniyor:** TC39 Signals Stage 1 → dil primitive'i bile hazır değil; bugünden "signals'a geç" gerekçesi yok (kaynak: github tc39, reptile.haus, Eisenberg — 3). **(3) Red destekleniyor:** framework'süz vanilla state pub/sub + mini-store ile kanıtlanmış, "Redux gerekmez" literatürde açık (kaynak: CSS-Tricks, grizzlypeak, Onyango, namastedev, dev.to — 5) → CoreMusic'in ADR-001 + ADR-027 kombinasyonu bu kalıpla uyumlu. **(4) Karşıt/uyarı:** RSC istemci state'ini azaltıyor — yani "state kütüphanesi şart" argümanı da zayıflıyor; ama RSC React demektir → CoreMusic için doğrudan geçerli değil (kaynak: Comeau, mayank, rsc-state — 3). **İtiraz/karşıt bulgu (dürüst):** hafif library'lerin bundle/perf rakamları **tek kaynaklı** (bağımsız ölçüm YOK → ⚠️); indirme-sayaçları npm politikasına duyarlı; "Zustand = hafif kütüphane, library serbest değil mi?" sorusu ADR-001'in **iskelet** tanımıyla yanıtlanır — bir state library'si bağımsız kurulabilir bir paket değil, tüm state'in geçtiği **iskelet**dir (§2.1 m.2). |
| Web Search **Alınan Karar** | **Red (R-001) YÜRÜRLÜKTE KALIR.** (a) Redux/RTK, Zustand/Jotai ve signals-tabanlı herhangi bir state library'si CoreMusic frontend'ine **GİRMEZ** — ADR-001 frozen yasağı + §1.3 bulguları (React-bağlamlı ekosistem, bundle bedeli, iskelet bağımlılığı). (b) State yönetimi **vanilla kalıpla** sürer: modül-bazlı mini-store + pub/sub olay yüzeyi (web 3 kalıbı) + kalıcı yüzey ADR-027 (localStorage/sessionStorage → PLANNED IndexedDB). (c) TC39 Signals **izlenir, uygulanmaz** (Stage 1). (d) **Yeniden değerlendirme koşulu** (§2.3) yazılmadan bu red **otomatik olarak güncellenmez**; tersi için de tek kaynaklı benchmark'lar yeterli sayılmaz. |
| Web Search **Sonuç** | **4/4 araştırmada red desteklendi** (1 ekosistem + 2 standart + 3 vanilla pratik + 4 RSC bağlamı). **Üç açık işaretlendi:** (i) bundle/indirme rakamları **tek kaynaklı** → sayı olarak ADR'ye bağlayıcı yapılmadı (yalnız eğilim) · (ii) TC39 Stage bilgisi kaynağa tarihli (reptile.haus 2026; Eisenberg erken dönem) → "standard değil" dışında iddia kurulmadı · (iii) "vanilla yeterli" iddiasının **CoreMusic'e özgü ölçümü yok** (kaç store/pub-sub dosyası, kaç abone — UNKNOWN) → §2.3 koşul maddesi. **Kaynak sayısı: 4 sorgu / 32 isabet (benzersiz ~31); §1.3'te adı geçen benzersiz kaynak ~30.** |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ADR-001 frozen (immutable) | `ADR-001 → ADR-037` değiştirilemez; bu red onun frontend ayağıdır — yasağı kaldırmak **yeni ADR** ister, bu dosya düzenlenmez (AGENTS.md §25.3 kural 2) |
| Kütüphane/iskelet ayrımı | ADR-001/ADR-002 ilkesi: paket **kütüphane** olabilir, uygulama **iskeleti** olamaz — state library'si tüm state'in geçtiği iskelet olduğu için bu red o sınırın frontend uygulamasıdır |
| Kod yüzeyi gerçeği | state library = **0** (§1.1/7); red bir "kaldırma" değil, **girişi engelleme** kararıdır — geri dönüş planı kod tarafında işlem gerektirmez (§5.2) |
| Ölçüm eksikliği | vanilla state şişmesi eşiği **UNKNOWN** (ölçülmedi) → eşik uydurulmaz, §2.3 koşulu "ölçülerek belirlenecek" der |

---

## 2. Karar (Decision)

**R-001 REDDEDİLMİŞTİR: Redux tarzı state management (global store + action + reducer + dispatch + subscribe — Redux/Redux Toolkit dahil herhangi bir state management library'si) CoreMusic frontend'ine alınmaz.** Karar `index.md:126`'da bugünden vardı; bu dosya onu gerekçelendirir: red, ADR-001'in frozen framework/iskelet yasağının uygulamasıdır ve 2026-10-01 web araştırması (§1.3) red'in **bugün hâlâ doğru olduğunu** doğrulamıştır. State yönetimi vanilla kalıpla (modül store + pub/sub) ve ADR-027'nin kalıcı depolama yüzeyiyle sürer.

### 2.1 Neden Bu Seçenek?

1. **İlke tutarlılığı (kanıtlı):** ADR-001 "kütüphane serbest / iskelet yasak" sınırı + ADR-004 `:160` bu red'i "aynı gerekçe ailesi" olarak zaten sınıflandırmış — ayrı bir karar değil, **aynı ilkenin state ayağı**.
2. **Ekosistem gerçeği (kanıtlı):** 2025-26'da tüm hafif alternatifler React-bağlamlıdır (Zustand/Jotai/TanStack Query); "framework yok" olan CoreMusic'te bunların avantajı **kullanılamaz**, yalnızca paket bağımlılığı ve bundle bedeli kalır (§1.3-1).
3. **İhtiyaç kanıtı yok (kanıtlı):** kodda `redux|zustand|mobx` = **0** (yalnız Figma token metinleri) — mevcut router + localStorage cache + pub/sub-yüzey işi görüyor (ADR-004/027 IMPLEMENTED kanıtı).
4. **Bağımlılık ekonomisi (kanıtlı):** tek yazan `setState` akışı olmadan (vanilla'da zorunlu desen), kütüphane = çıkarılması pahalı iskelet; red, ADR-002'nin ORM dersinin frontend karşılığıdır (sihir katmanı → görünmez iskelet).

### 2.2 Teknik Detaylar

- **Yasak yüzeyi:** `redux`, `@reduxjs/*`, `react-redux`, `zustand`, `mobx`, `@preact/signals`, `jotai`, `nanostores` ve bunların eşdeğerleri (import edilen herhangi bir **state management library'si**). Kütüphane değil **iskelet** testi (ADR-001/002): state'in tamamı üzerinden geçiyorsa yasaktır.
- **İzinli yüzey (yerini alan uygulama):** (a) saf DOM + event dinleyicileri; (b) modül-bazlı mini-store (`getState/setState/subscribe` kalıbı — web 3); (c) pub/sub EventBus (`on/off/emit`); (d) kalıcı yüzey = ADR-027 (localStorage küçük veri IMPLEMENTED; IndexedDB/SW PLANNED); (e) router durumu = ADR-004 saf History API (`replaceState` initial-state dahil).
- **Kod yüzeyi ölçümü (2026-10-01):** `redux|zustand|mobx` kod isabeti **0** · `signals` → yalnız `signal-utils.js` `combineSignals` (AbortSignal, state library değil) · `tokens-*.json` "State: Zustand / Pinia / Redux Toolkit" = Figma mockup metni (kod değil).
- **DevTools yokluğu:** Redux'un time-travel DevTools avantajı kaybedilir (web 1: gerekçeli tek güçlü kullanım senaryosu) → mitigasyon: `console.debug` + olay logu, QA E2E regresyon (ADR-004 `:223` kalıbı).

### 2.3 Yeniden Değerlendirme Koşulu (şart satırı)

> **Bu red yalnız aşağıdaki koşullardan BİRİ yazılırsa yeniden değerlendirilir; aksi hâlde yürürlükte kalır:** (1) **ADR-001 framework/iskelet yasağı yeni bir ADR ile değiştirilirse** (frozen metin düzenlenmez — yeni ADR `superseded by` ile bağlar); (2) **vanilla state yüzeyinin elle yönetimi ölçülüp eşik aşarsa** — eşik değeri bugün **UNKNOWN**, ölçülmeden sayılmaz; ölçüm (kaç store/pub-sub dosyası, kaç abone, kaç tekrar eden update kalıbı) §5.1/2'ye bağlanır; (3) **TC39 Signals Stage 3+'a ulaşır ve React bağlamı olmadan kullanılabilir hâle gelirse** (kütüphane değil dil primitive'i — Stage 1, §1.3-2). Üç koşul da bugün **SAĞLANMAMIŞTIR** → red geçerli.

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Redux + Redux Toolkit (tam state library)** | olgun DevTools (time-travel), belgelenmiş kalıplar, tek yönlü veri akışı disiplini | React ekosistemi bağımlılığı; RTK+react-redux ~15KB bundle (**tek kaynak**, §1.3-1); action/reducer boilerplate; tüm state'in tek iskelete bağlanması | **ADR-001 frozen framework/iskelet yasağı doğrudan ihlal** (ADR-004 `:160` aynı gerekçe ailesi); kodda ihtiyaç kanıtı 0 (§1.1/7); red zaten `index.md:126`'da tescilli |
| 2 | **Hafif library (Zustand / Jotai / Nanostores)** | ~3-4KB bundle (**tek kaynak**), az boilerplate, hook'larla kısa API | hepsi React-binding'li (çekirdek vanilla'da ekosistem değeri sınırlı); "küçük = serbest" savı ADR-001 iskelet testini geçmez (state'in tamamı üzerinden geçer); ek paket = tedarik + sürüm yüzeyi | İlke: **iskelet yasak** (ADR-001/002 ayrımı); 2026'da endüstri kayışı React içinde olduğundan CoreMusic'e taşınabilir fayda **0** (§1.3-1) |
| 3 | **Signals tabanlı reaktif state (TC39 proposal-signals / @preact/signals)** | dil-level primitive hedefi, ince güncelleme (web 1: signals 3ms render), framework'ten bağımsız amaç | TC39 **Stage 1 — standard değil** (§1.3-2); @preact/signals yine paket + Preact eko bağımlılığı; bugün production primitive'i yok | **Standart değil + paket = iskelet riski**; izleme kararı (§2.3/3) — bugünden girilmez, Stage 3+'ta yeniden bakılır |

*(Kabul edilen uygulama alternatifi — vanilla mini-store + pub/sub — §3'te "reddedilmedi" olarak ayrılmadı; o, §2.2'deki **yerini alan** yaklaşımdır ve ADR-001/027 kapsamındadır.)*

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Tek bağımlılık eklenmez:** state katmanı için paket, sürüm, tedarik-zinciri ve bundle yüzeyi **0** (ADR-001 tek build disiplini korunur).
- **Red gerekçesi artık izlenebilir:** `grep R-001` → bu dosya; ADR-001 `:32/:126/:201` ve ADR-004 `:160/:242` ile gerekçe ailesi tek dosyada toplandı.
- **Güncelleme yapıldı:** red 2026 web verisiyle (4 sorgu / 32 isabet) yeniden sınandı — "eski bilgiye dayanan karar" sınıfından çıktı.
- **Kod tarafında sıfır iş:** geri hiçbir kod/dependency kaldırılmadı (zaten 0 — §1.1/7).

### 4.2 Olumsuz Sonuçlar

- **DevTools/time-travel yok:** hata ayıklama elle yapılır (§2.2 mitigasyonu).
- **Elle yönetim yükü:** store/pub-sub kalıpları kodda tekrarlanabilir; tekrar-azaltma disiplini QA denetimine bağlı (eşik UNKNOWN — §2.3/2).
- **Ekosistem kaybı:** Zustand/Jotai gibi bakımlı araçların faydası (devtools köprüsü, topluluk) kullanılamaz.
- **Signals fırsatı ertelendi:** dil primitive'i hazır olsaydı paketsiz reaktiflik mümkün olurdu — bugün mümkün değil (Stage 1).

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| Vanilla state şişmesi (elle store/pub-sub kodunun büyümesi, tutarsız update kalıpları) | 3 (olası — ölçülmedi, UNKNOWN) | 3 (orta) | §5.1/2 ölçümü + eşik belirleme (uydurulmaz, ölçülür) + mini-store kalıbını tek yerde toplama (ITCSS/BEM gibi katman disiplini) |
| Gizli iskelet bağımlılığı ("küçük helper" adıyla giren state paketi) | 2 (mümkün) | 4 (yüksek — ADR-001 ihlali) | §2.2 yasak listesi + PR/tarama denetimi (`redux\|zustand\|mobx` grep = 0 kalır) |
| Boilerplate / tekrar eden subscribe-abone kodu (memleak: abonelik kaldırılmaması) | 3 (olası) | 3 (orta) | subscribe'ın unsubscribe döndürme kalıbı (web 3 örneği) + QA E2E temizlik testi |
| "Redux aldıran" dış baskı (yeni ekip/klon proje) | 2 (mümkün) | 3 (orta) | §2.3 koşul satırı — yalnız yeni ADR ile tartışılır, dosya düzenlenmez |
| Tek kaynaklı benchmark'lara dayanarak yanlış "hafif library" gerekçesi kurulması | 2 (mümkün) | 2 (düşük) | §1.3 rakamlar **bağlayıcı değil** (işaretli) ; karar ilkeye (iskelet yasağı) dayanır, sayıya değil |

---

## 5. Uygulama (Implementation)

> Bu kayıt **salt-okunur seri**dir (`rejected/`); "uygulama" = kaydın vault'a doğru yerleştirilmesi ve denetimidir — kod değişikliği **yoktur**.

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | Bu dosyayı `.ai/.decisions/rejected/R-001-redux-style-state-management.md` olarak yaz (şablon §1-§7, 7 bölüm dolu) + `log.md` append | Vault Steward | 20 dk |
| 2 | **Ölçüm:** vanilla state yüzeyi sayımı (store/pub-sub dosyası, abone sayısı, tekrar update kalıbı) → eşik ÖNERİSİ üret (rakam ölçülmeden yazılmaz) | UI Designer + QA Engineer | 1 saat |
| 3 | Debate ✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 → RED DOĞRULANDI) + Tech Lead onayı ✅ — 3 şart §5.3 | MO + Tech Lead | 2026-10-01 |
| 4 | **`index.md:126` `<!-- dead-link ... -->` bayrağı → DOKUNULMADI** (talimat gereği son sıfırlamaya ertelendi — rapor-only) | Vault Steward | son sıfırlama |
| 5 | Periyodik denetim: `redux\|zustand\|mobx` kod taraması = 0 korunur (§2.2 yasak listesi) | QA Engineer | her sprint |

### 5.2 Geri Dönüş Planı

Bu karar **kod tarafında geri alınacak bir şey üretmedi** (state library hiç girmedi → `git revert` edilecek değişiklik **0**). Geri dönüş = **yeniden değerlendirme** demektir ve yalnız §2.3 koşullarından biri yazılırsa yeni ADR ile açılır: (1) ADR-001 yasağı değişirse → yeni ADR yazılır, bu dosya `superseded` notuyla **bağlanır, düzenlenmez**; (2) eşik aşılırsa → ölçüm kanıtıyla yeni ADR (bu red yeni ADR ile kapatılır); (3) TC39 Signals Stage 3+ → izleme notu + yeni ADR. Vault bozulursa standart kurtarma `git checkout` + son commit (AGENTS.md §17 #10). **Reddedilen teknolojinin kodda izi olmadığı için veri/kullanıcı etkisi YOKTUR.**

### 5.3 Debate Şartları (3 madde — 2026-10-01, 3 tur / 20 persona)

| # | Şart (Tur 2 itiraz → çözüm) | Durum |
|---|------------------------------|-------|
| 1 | **ADR-045/046 slug düzeltmesi:** yanlış slug/düz metin → gerçek dosyalarla wiki-link (`[[../accepted/ADR-045-multi-domain-view-mode-architecture]]`, `[[../accepted/ADR-046-cross-view-state-preservation]]`), `⚠️ VERIFICATION REQUIRED` işareti kaldırılır | ✅ **UYGULANDI** (bu işlem — §1.1/10 + §6) |
| 2 | **Bundle rakamı çift kaynak:** §1.3/§3'teki tek kaynaklı bundle/indirme rakamları **bağlayıcı değildir**; iki bağımsız kaynakla doğrulanana kadar yalnız eğilim olarak kullanılır, doğrulanamazsa `⚠️ VERIFICATION REQUIRED` işareti **kalıcı** kalır | ⚠️ **AÇIK** (izleniyor — §1.3, §3/1-2, §4.3 son satır) |
| 3 | **Signals yeniden değerlendirme kapısı:** TC39 Signals **Stage 3+'a** ulaşırsa **veya** Redux tarzı state'in projede zorunlu kalması gerektiği yazılırsa §2.3 kapısı açılır ve yeni ADR ile tartışılır (bugün Stage 1 → kapı kapalı) | ⏳ **KOŞUL BEKLİYOR** (§2.3/3) |

> **Debate sonucu:** 3 tur / 20 persona — Tur 1 bulgu + Tur 2 itiraz→çözüm (3 şart) + Tur 3 oylama **19 kabul / 1 çekimser / 0 red → RED DOĞRULANDI** · **Tech Lead:** ✅ (2026-10-01) · Arch Lead: ⏳.

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../index]] | Karar dizini — bu red'in kaydı (`:126`, slug + "Framework yasağı" + dead-link bayrağı §5) |
| [[../accepted/ADR-001-vanilla-js-itcss]] | **Yerini alan (birincil):** frontend temeli — framework/iskelet yasağı; `:32/:126/:201` R-001 gerekçe referansları |
| [[../accepted/ADR-004-multi-domain-spa]] | Gerekçe ailesi — `:160` "R-001 (Redux — framework bağımlılığı) ile aynı gerekçe ailesi"; `:30/:143` saf istemci router kanıtı |
| [[../accepted/ADR-027-dual-mode-storage-strategy]] | **Yerini alan (state'in kalıcı yüzeyi):** localStorage/sessionStorage IMPLEMENTED, IndexedDB/SW PLANNED (`:37/:54`) |
| [[../../raw/brain]] | Mimari karar özeti (frontend/state satırı) |
| [[../accepted/ADR-045-multi-domain-view-mode-architecture]] | Cross-view view mode mimarisi — **diskte VAR** (2026-10-01 glob doğrulaması; ADR-004 `:19` "henüz yazılmadı" notu eskimiş → Tur 1 slug düzeltmesi) |
| [[../accepted/ADR-046-cross-view-state-preservation]] | Cross-view state preservation — **diskte VAR** (2026-10-01 glob doğrulaması → Tur 1 slug düzeltmesi) |
| Debate şartları (3 madde) | Bu dosya **§5.3** — (1) ADR-045/046 slug düzeltmesi ✅ (2) bundle rakamı çift kaynak (3) Signals yeniden değerlendirme kapısı |
| Düz metin | Eski seri R-002…R-012 (`rejected/index.md` §5 tablosu boş → §7.1/2) |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali | 2026-10-01 | ✅ |
| Tech Lead | — | 2026-10-01 | ✅ |
| Arch Lead | — | — | ⏳ |

### 7.1 Rapor Notları (bu işlemde dokunulmayanlar)

1. **Dead-link bayrağı:** `index.md:126` `<!-- dead-link: R-001-redux-style-state-management no source 2026-09-24 -->` **olduğu gibi bırakıldı** — artık `no source` iddiası **geçersizdir** (bu dosya kaynaktır) → temizlik son sıfırlamaya ertelendi, burada rapor edildi.
2. **`rejected/index.md` durumu:** dosya **VAR** (`rejected/index.md`, v1.0.1, `total: 12`) ama §5 tablosu **BOŞ** (12 red'in hiçbiri satırlanmamış) → bu işlemde **oluşturulmadı ve doldurulmadı** (dizin satırı düzenleme yetkisi kapsam dışı) → rapor-only.
3. **Slug hizası:** dosya adı `R-001-redux-style-state-management` = `index.md:126` slug **birebir** ✅.
4. **Debate:** ✅ TAMAMLANDI (2026-10-01, 3 tur / 20 persona — 19 kabul / 1 çekimser / 0 red → **RED DOĞRULANDI**) · **Tech Lead:** ✅ (2026-10-01) · **Arch Lead:** ⏳ (debate dışı) — 3 şart §5.3'e eklendi; ADR-045/046 slug düzeltmesi §1.1/10 + §6'da uygulandı (şart 1).
5. **Ad düzeltmesi:** ADR-027'in gerçek title'ı "Dual-Mode Storage Strategy (…)"dir; görev tanımındaki "dual-mode playlist state" adı dosyayla birebir **değildir** → gerçek ad kullanıldı (§1.1/5).

---

*R-001 v1.0.0 | 2026-10-01 | Created — CoreMusic Vault (.decisions/rejected/ yeniden yazım, salt-okunur seri)*
*Authority: R-001 Red Karar Metni · Mode: Red Team · Human Mode · Truth Mode*
