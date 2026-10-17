---
node_id: "K020-discovery/master"
node_type: "master"
status: "draft"
parent_id: "K020-discovery"
children: []
depends_on:
  - "K020-discovery/index.md"
  - "K007-data/schema/schema-kapsam.md"
  - "K008-domain/discovery/index.md"
  - ".ai/architecture/index.md (registry route — yazım yalnız orkestratör)"
  - "accepted/ADR-010-csrf-protection-strategy.md (OAuth state/PKCE)"
  - "accepted/ADR-095-hybrid-jwt-rs256-access-token.md (session cookie ≠ API JWT · secret/log yasağı)"
  - "draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md (§1/6 tur-1 · §2 K2/K6 · §5.5 gruplama)"
  - "accepted/ADR-101-guvenlik-kanonik-sirasi-uzak-cagri.md (§2.2 fallback zorunlu)"
evidence:
  - "2026-10-17 disk doğrulaması: v2 ağacı satır 571–575 · ADR-097 §1/6 (satır 33) · K007 data-master.md:219 · recomenataion/index.md:12 · K008 domain-master.md:188"
  - "2026-10-17 repo Test-Path/glob: client/ dizini YOK · signal.js · manifest.json · seed_kit.json · signal_registry.json = 0 isabet → uydurma repo referansları silindi (§9.3)"
  - "2026-10-17 tur-2 web: 12 servis · 18 URL · 0 hata (discovery-web-arastirma.md §6)"
validation: "partial"
open_questions:
  - "K008-domain/discovery ↔ K020-discovery sahiplik örtüşmesi (D-3 — kapsam ayrımı yok)"
  - "S-1 search sahipliği: K007-data/search ↔ K020-discovery/search (K007 data-master.md:219 — sahiplik ADR'si bekliyor)"
  - "Keşif çıktısı kümeleme eşiği tanımsız (release 0.1 demet adı dahil tanımsız — uydurma ad yazılmaz)"
group: "mimari"
---

# K020-discovery — Master (keşif mimarisinin omurgası)

> **Rol:** Bu dosya, `K020-discovery` alt disiplinlerinin (root · agent · search) ve
> K020 öncesi omurga node'larının (K007-data/schema · K008-domain/discovery) mimari
> sözleşmesini tek yerde toplar. Kapsam, bağımlılıklar, gate haritası ve sentez
> noktalarını tanımlar; kod/araştırma/doğrulama detayı alt disiplin dosyalarındadır.
>
> **Konum:** `.ai/architecture/K020-discovery/discovery-master.md`
> **Ölçüm (2026-10-17):** canonical `[System.IO.File]::ReadAllLines().Length`.

---

## 1. Bu master neyi kapsar — ve neyi KAPSAM DIŞI bırakır

### 1.1 Kapsam (in-scope)

| # | Kapsam alanı | Açıklama | Kanıt |
|---|---|---|---|
| 1 | Keşif omurgası | Root `discovery-*` dosyalarının mimari rolü | bu dosya §2 |
| 2 | Alt disiplin sözleşmesi | `agent/` ve `search/` keşif çıktılarının root'a raporlaması | root `index.md` §GRUPLAR |
| 3 | K020 öncesi omurga | K007-data/schema (omurga girdisi) ve K008-domain/discovery (D-3 sahiplik sınırı) | `index.md` "Durum" |
| 4 | Keşif süreci | Kaynak → tohum → skorla → küme → K020 node bağlama | bu dosya §3 — **⚠️ ADR-097'de bağlayıcı karşılığı YOK** (§9.3/1) |
| 5 | Gate haritası | Keşif-kod sınırındaki bağlayıcı kapılar | bu dosya §6 |
| 6 | Sinyal sözleşmesi durumu | Sinyal/enum iddialarının disk gerçeği | bu dosya §4 — **⚠️ vault'ta karşılık YOK** (§9.3/2) |

### 1.2 Kapsam dışı (out-of-scope) — bu master'da İŞLENMEZ

| # | Dışarıda | Nerede işlenir | Not |
|---|---|---|---|
| 1 | İçerik keşfi/flora analizi | `K020-discovery/agent/**` | agent dosyaları sahibi |
| 2 | Arama/sorgu keşfi (discovery arama) | `K020-discovery/search/**` | search dosyaları sahibi |
| 3 | Şema normalizasyonu (BCNF) | `K007-data/schema/*` | data-engineer omurgası |
| 4 | Dönem ayrıştırma/şarkı alanı | `K008-domain/discovery/*` | domain-adjacent (D-3) |
| 5 | K020 alt katman kod uygulaması | K020-{auth,library,preferences,search,recommendation,api}/ | `.ai/architecture/README.md:16` ("Burada kod yok") |

**Kural:** Bu master **kod üretmez**; yalnız mimari sözleşmeyi ve gate'leri yazar.
Kod yalnız `*-ornekler.md` dosyalarında ve K020 alt katmanlarında yazılır
(onay kapısı: root `AGENTS.md` §1 — `APPROVAL → IMPLEMENTATION`).

---

## 2. K020-discovery mimari omurgası (3 disiplin + 2 omurga)

### 2.1 Düğüm haritası

```
K020-discovery/                      (parent · keşif disiplini kökü)
├── index.md                         ORCHESTRATION  — route, gruplar, ölçüm, giriş
├── discovery-master.md              MASTER        — bu dosya (omurga sözleşmesi)
├── discovery-bagimliliklar.md       mimari        — disk kanıtlı bağımlılıklar (D-1…D-5 · S-1)
├── discovery-dogrulama.md           kanıt         — kanıt seti + kapılar
├── discovery-web-arastirma.md       kanıt         — geniş servis taraması (tur-2)
├── agent/                           (alt disiplin · içerik-keşif)
│   ├── index.md                     ORCHESTRATION
│   └── agent-kapsam.md … agent-dogrulama.md  (5 dosya)
└── search/                          (alt disiplin · sorgu-keşif)
    ├── index.md                     ORCHESTRATION
    └── search-kapsam.md … search-dogrulama.md  (5 dosya)
```

**Disk ölçümü (2026-10-17):** 17 md · kök 6 + `search/` 6 + `agent/` 6.

### 2.2 Katman sözleşmesi (kim neye bağımlıdır)

| Katman | Bağımlı olduğu üst katman | Raportladığı yer | Tek yetkili olduğu alan |
|---|---|---|---|
| root `discovery-*` | `.ai/architecture/index.md` (registry SSOT — yazım yalnız orkestratör, §9.8/10 satır 655) | registry §9.x kapanışı | keşif omurgası + gate haritası |
| `agent/` | root `discovery-master.md` (bu dosya) | `agent/index.md` → root `index.md` | içerik/flora keşfi, servis auth keşfi |
| `search/` | root `discovery-master.md` (bu dosya) | `search/index.md` → root `index.md` | sorgu-keşif (discovery arama), sonuç dilbilgisi |

**Kural (tek doğruluk):** Bir mimari karar **yalnız bir katmanda** yazılır. Alt katman
`⚠️ VERIFY REQUIRED` işaretiyle **referanslar**, tekrar **etmez** (SSOT ilkesi:
registry §3 "v1+v2 tek ağaç" + ADR-097 K1; kopya-karar yasağı bu keşif katmanlarına
da uygulanır).

### 2.3 Dikey bağımlılık zinciri (keşif → uygulama)

| Sıra | Düğüm | Ne verir | Ne alır |
|---|---|---|---|
| 1 | `K007-data/schema/schema-kapsam.md` (omurga girdisi) | normalization formülü, key/ünite kuralları | — (omurga girdisi) |
| 2 | `K008-domain/discovery/index.md` (D-3) | dönem ayrıştırma, shuffle sözleşmesi | K007 formülü |
| 3 | `K020-discovery/index.md` (route) | disiplin dağılımı, gruplar, ölçüm | omurga girdileri |
| 4 | `K020-discovery/discovery-master.md` (bu dosya) | mimari sözleşme + gate haritası | registry route |
| 5 | `agent/**` + `search/**` | keşif çıktıları (servis/arama bulguları) | bu dosya §3 süreç |
| 6 | K020-{auth,search,…} (gelecek node'lar) | uygulama kodu | keşif çıktıları + ADR gate'leri |

**Zincir kuralı:** Yukarıdan aşağı tek yön; **geri besleme** yalnız `open_questions`
alanıyla yapılır (alt katman üst katmana soru açar, kararı vermez).

---

## 3. Keşif süreci (bu dosyanın taslak sıralaması — ⚠️ ADR-097 §2.2 YOK)

> **Truth Mode düzeltmesi (2026-10-17):** Bu sıralama daha önce "ADR-097 §2.2'de
> bağlayıcı" diye sunulmuştu. **ADR-097 §2 = K1–K7** (isimlendirme, bant rezervasyonu,
> çoklu-MD şema, üretim biçimi — satır 37–81) olup **pipeline/ödül sinyali/skorlama
> içermez**. Sıralama bu nedenle **keşif disiplininin kendi taslağıdır**; bağlayıcılığı
> registry kapanışına (§9.x) ve onay kapısına bağlıdır (kaynak: ADR-097 başlıkları
> 2026-10-17 grep ile doğrulandı — §9.3/1).

| # | Aşama | Ne yapılır | Çıktı | Sorumlu disiplin | Kaynak durumu |
|---|---|---|---|---|---|
| 1 | Kaynaklar | vault + repo taraması (gizli dizin `hidden: true`) | ham kayıt kümesi | root `discovery` | ✓ `.ai/AGENTS.md` §7/2 arama kuralı |
| 2 | Tohum sorgular | katalog tohumu (playlist/küme tabanlı ilk sorgular) | tohum listesi | `agent/` + `search/` | ⚠️ keşif taslağı — ADR'de karşılığı yok |
| 3 | Skorla | popülerlik/kanıt gücü ile önceliklendirme | skorlanmış küme | `agent/` | ⚠️ eşik tanımsız — **uydurma sayı yazılmaz** |
| 4 | Kümeleme | ortak küme/demet şeması | demet şeması | `agent/` + `search/` | ⚠️ demet adı/eşiği tanımsız |
| 5 | Node bağlama | keşif çıktısını K020 node'una bağlama kararı | kapanış kaydı | root (registry §9.x) | ✓ registry yazım yetkisi §9.8/10 (yalnız orkestratör) |
| 6 | Kod | yalnız gate'ten **GEÇTİYSE** ve **onaydan sonra** | kod | K020 alt katmanlar | ✓ root `AGENTS.md` §1 (APPROVAL → IMPLEMENTATION) |

**Kapı:** Aşama 6'ya geçiş için §6 gate tablosundaki kapılar zorunludur;
aşamalar 1–5 kod **üretmez**, yalnız keşif verisi yazar.

---

## 4. Sinyal sözleşmesi durumu — ⚠️ vault/repo'ta karşılık YOK

> **Truth Mode düzeltmesi (2026-10-17):** Önceki sürüm §4'ü `SignalType` (25 enum),
> `ListenType` (7 enum), `client/js/core/signal.js`, `manifest.json`, `seed_kit.json`
> ve `/api/v2/signal` iddialarını "ADR-097 §2.3–2.4 + repo kanıtı" diye taşıyordu.
> **Hepsi uydurmadır** — disk taraması sıfır isabet verdi; bu dosyanın/klasörün
> dışındaki vault ve repo'da tek referans yok (bkz. §9.3/2).

| İddia (önceki sürüm) | Disk gerçeği (2026-10-17 Test-Path/grep) | Durum |
|---|---|---|
| `SignalType` (25 enum) — ADR-097 §2.3 | vault + repo + v1/v2 spec = **0 isabet** | **silindi** |
| `ListenType` (7 enum) — ADR-097 §2.4 | **0 isabet** | **silindi** |
| `client/js/core/signal.js` (149 satır) | `client/` dizini **YOK**; `**/signal*.js` glob → yalnız `assets.coremusic.net/js/router/config/signal-utils.js` (19 satır, AbortSignal birleştirme — domain sinyali DEĞİL) | **silindi** |
| `manifest.json` (27 manifest) | **0 isabet** | **silindi** |
| `database/seed_kit.json` + `signal_registry.json` | **0 isabet** | **silindi** |
| `POST /api/v2/signal` + `signal_log` | **0 isabet** | **silindi** |
| `history.js` / `vhistory.js` / `client/js/api.js` | **0 isabet** | **silindi** |
| **Gerçek repo arama yüzeyi:** `app-nodejs-projects/nova-search-engine/` | `packages/search/{engine(255),ranking(58),search-index(127),recommend(35),bootstrap(49)}.ts` · `packages/workers/{rate-limiter(43),queue(92),collector-worker(123)}.ts` · `packages/common/signals.ts` (60 satır: dil/spam skoru — **sinyal ≠ domain event**) · `packages/youtube/{search(232),client(75),mapper(172)}.ts` · `.ai/decisions/ADR-001-youtube-api-v3-yasak.md` | ✓ **VAR** (proje kökü: `.ai/projects/NovaSearchEngine/01-kurulum-mimari-ve-acik-konular.md:4`) |

**Kural:** Sinyal sözleşmesi (enum/ucu/kayıt tablosu) **henüz tanımlı değildir**.
Keşif çıktıları var olmayan bir enum'a **atıf yapmaz**; sinyal yüzeyi ayrı bir
tasarım/ADR gerektirir (açık soru). `nova-search-engine` çıktıları keşif için
**mevcut repo kanıtı** sayılır; `signals.ts`'teki skor **arama kalite skorudur**
(domain olay sinyali değildir).

---

## 5. Keşif → K020 bağlama haritası (hangi keşif hangi node'a bağlanır)

| Keşif çıktısı | Hedef K020 node'u | Bağlantı türü | Durum |
|---|---|---|---|
| Auth keşfi (OAuth/PKCE/JWT) | `K020-auth` | gate girdisi | ⚠️ "auth gate" adlı ADR **YOK** — gerçek `accepted/ADR-010` = **CSRF** (state/PKCE koruması) · `ADR-095` = JWT ayrımı |
| Arama keşfi (federasyon, sonuç dilbilgisi) | `K020-search` | doğrudan | **S-1**: K007-data/search ↔ K020-discovery/search — sınır açık (data-master.md:219) |
| Tercih/kişiselleştirme keşfi (dinleme davranışı) | `K020-preferences` | doğrudan | ⚠️ ADR-097'de karşılığı yok — keşif taslağı |
| Kütüphane keşfi (koleksiyon/ünite) | `K020-library` | doğrudan | omurga girdisi: `K007-data/schema/schema-kapsam.md` |
| Öneri keşfi (katalog/tohum verisi) | `K020-recomenataion` | **bağımlılık** | **D-1**: `recomenataion → discovery` (recomenataion/index.md:12) |
| API keşfi (uç model, istek/atlas) | `K020-api` | doğrudan | ⚠️ uç model kaynağı **UNKNOWN** (önceki "U-L06 fragment" iddiası vault'ta 0 isabet — §9.3/3) |
| Schema/ünite keşfi | `K007-data/schema` | omurga | `schema-kapsam.md` (651 satır) |
| Dönem/şarkı keşfi | `K008-domain/discovery` | omurga | **D-3** sahiplik sınırı (domain-master.md:188) |

**D-1 kuralı:** `K020-recomenataion → K020-discovery` bağımlılığı vault'ta yazılıdır
(`recomenataion/index.md:12` depends_on · `recommendation-master.md:16,34,43`); keşif
**bitmeden** öneri node'u kodlanamaz (ne tersi: öneri, keşfin girdisini **belirlemez**).

---

## 6. Gate haritası (keşif-kod sınırındaki bağlayıcı kapılar)

| Gate | Kaynak (SSOT — 2026-10-17 disk doğrulandı) | Keşif etkisi | Kırılırsa |
|---|---|---|---|
| **Auth/CSRF gate** | `accepted/ADR-010-csrf-protection-strategy.md` — **⚠️ "media-provider auth gate" adlı ADR YOKTUR** (eski atıf uydurma dosya adıydı) | servis keşfi sonuçları onaydan **önce** sunulur; `state`/PKCE doğrulaması atlanmaz | entegrasyon bloke (karar gerekir) |
| **YouTube Music KAPALI** | `draft/ADR-097` §1 Bağlam **madde 6** (satır 33): "YouTube Music resmi public API yok (TOS riski)" · çapraz: `K020-recomenataion/index.md:55` · tur-2: `discovery-web-arastirma.md` | `⚠️ KAPALI` — resmi API yok; TOS riski | kod yazımı yasak (kapı **değişmez**) |
| **Secret/log yasağı** | `accepted/ADR-095` **§1.2** (satır 36): "`private.pem` asla git'e girmez; Secret'lar vault'a/log'a yazılmaz" | keşif raporlarında token **yalnız** `.env`'den anılır, `.md`'ye yazılmaz | zero-hallucination/secret ihlali |
| **Session ayrımı** | `accepted/ADR-095` **§2.1** (satır 51) + authority: session cookie tarayıcıya, API JWT istemciye ait | keşif "session" dediğinde CoreMusic session'ı ayrı JWT | karışım tespiti → düzeltme zorunlu |
| **Fallback zorunluluğu** | `accepted/ADR-101` **§2.2** (satır 51): "fallback zorunlu; API yanıt modeli asla 'her zaman 200' kabul edilmez" — **⚠️ "sentinel/seq.1–30" iddiası bu ADR'de YOK** | keşifte eksik-fallback **tespiti**; harici çağrıda timeout/retry/fallback zinciri | eksik fallback → tasarım reddi |
| **Ölçüm kanonu** | `.ai/architecture/index.md` **§9.12.1** (satır 761–764): `[System.IO.File]::ReadAllLines().Length` | keşif dosya ölçümü kanonik yöntemle | `Get-Content\|Measure-Object -Line` ile ölçülen değer **geçersiz** |
| **Şablon gate** | `.ai/AGENTS.md` §13/5 (Guardrail #16) | `.ai/ui-design/**` yazımı önce `.templates/ui-design/` şablonları | şablonsuz dosya üretilmez |
| **Onay** | root `AGENTS.md` §1 akışı (`APPROVAL → IMPLEMENTATION`) | keşif bulgusu → K020 **kod** uygulaması **onay ister** | onaysız kod = kural ihlali |

**Sınır özeti:** Keşif disiplini **bulgu üretir**; **kod yalnız K020 alt katmanlarında**
ve **onaydan sonra** yazılır. Keşif `*-ornekler.md` dosyaları dilekçe/sözleşme
örneğidir, **entegrasyon kodu değildir** (kapı gate kontrolünde belirtilir).

---

## 7. Sentez noktaları (root ↔ agent ↔ search köprüsü)

| # | Köprü | Ne sentezlenir | Nerede yazılır |
|---|---|---|---|
| 1 | agent → root | keşif bulgularının omurgaya etkisi | root `discovery-dogrulama.md` §kanıt |
| 2 | search → root | sorgu-keşif dilbilgisinin K020-search'e devri | root `index.md` §çocuklar |
| 3 | agent ↔ search | ortak kapı/kuyruk dili (gate_state, rate-aware kuyruk) | her iki `index.md` §GRUPLAR |
| 4 | root → registry | keşif kapanışının işlenmesi | `.ai/architecture/index.md` §9.x (yalnız orkestratör) |
| 5 | root → K008 | keşif/sahiplik sınırı | `K008-domain/discovery/index.md` (⚠️ açık soru — D-3) |

**Çakışma kuralı:** Sahiplik çakışmasında (S-1 ↔ K007 · D-3 ↔ K008) **karar verilmez**;
her iki dosyada `⚠️ VERIFY REQUIRED` olarak taşınır ve registry `open_questions`'a eklenir.

---

## 8. Riskler ve açık sorular (keşif omurgası)

| # | Risk / soru | Etki | Durum |
|---|---|---|---|
| 1 | K008 discovery ↔ K020-discovery sahiplik örtüşmesi | aynı "discovery" adı iki yerde | `⚠️ VERIFY REQUIRED` (D-3) |
| 2 | search sahipliği K007 ↔ K020-discovery | çift indeks, tutarsız sonuç | `⚠️ VERIFY REQUIRED` (S-1) |
| 3 | YouTube Music KAPALI (ADR-097 §1/6) | keşif bulgusu → kod yasağı | kapı **değişmez** |
| 4 | Keşif çıktı kümeleme eşiği/demet adı tanımsız | hangi sinyal hangi demete girer | tanımsız — uydurma ad/sayı yazılmaz |
| 5 | "auth gate" ADR'si yok (ADR-010 gerçekte CSRF) | servis entegrasyonu onaya bağlı | `⚠️ VERIFY REQUIRED` — ayrı ADR gerekir |
| 6 | Sinyal sözleşmesi (enum/ucu) yok | keşif çıktısı sözdizimi belirsiz | `⚠️ VERIFY REQUIRED` (§4) |

---

## 9. Doğrulama ve İlgili dosyalar

### 9.1 Doğrulama (bu master için)

- [x] Frontmatter 9 alan + `group` (registry §9.4.2 satır 321–325 · ADR-097:155 `group` = 9. alan) + Authority footer.
- [x] `group: mimari` (registry §3 satır 58 · §9.11 — 3 grup: mimari · kod · kanit).
- [x] Sıra yasağı ihlali yok (root `AGENTS.md` §1: `… → APPROVAL → IMPLEMENTATION`); bu dosya **doc**.
- [x] `validation: partial` — S-1 sahiplik ADR'si + D-3 kararı + auth-gate ADR'si bekleniyor.
- [x] Yanlış kaynak iddiaları (ADR-097 §1.6/§2.2/§2.3–2.4 · `log.md:xxx` · `signal.js` · `U-L06`) **2026-10-17 Truth Mode düzeltmesiyle silindi** — döküm §9.3.

### 9.2 İlgili dosyalar (tek bakışta)

| Dosya | İlişki |
|---|---|
| `.ai/architecture/index.md` | registry route SSOT + §9.x kapanışı (yazım: §9.8/10 — yalnız orkestratör) |
| `.ai/architecture/README.md` | çoklu-MD şema (satır 24–41) + "Burada kod yok" (satır 16) |
| `.ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md` | §1/6 tur-1 servis bulguları · §2 K2 çoklu-MD · §2 K6 ID CONFLICT · §5.5 gruplama |
| `.ai/.decisions/accepted/ADR-010-csrf-protection-strategy.md` | CSRF (`state`/PKCE) — **auth-gate ADR'si değildir** (⚠️ ayrı ADR yok) |
| `.ai/.decisions/accepted/ADR-095-hybrid-jwt-rs256-access-token.md` | secret/log yasağı §1.2 · session ≠ API JWT §2.1 |
| `.ai/.decisions/accepted/ADR-101-guvenlik-kanonik-sirasi-uzak-cagri.md` | §2.2 fallback zorunlu (sentinel/seq iddiası YOK) |
| `K007-data/schema/schema-kapsam.md` | omurga girdisi (651 satır) |
| `K008-domain/discovery/index.md` | D-3 omurga (sahiplik sınırı) |
| `K020-recomenataion/index.md:12` | D-1 depends_on (gerçek) |
| `app-nodejs-projects/nova-search-engine/` | gerçek repo arama yüzeyi (§4 — packages/search·workers·common) |

### 9.3 Truth Mode düzeltme kaydı (2026-10-17 — uydurma referans sınıfları)

> Önceki oturumda üretilen ve **disk kanıtı olmayan** referansların tam dökümü.
> Kural: her madde ya gerçek kaynakla değiştirildi ya da silindi (AGENTS.md §3).

| # | Uydurma sınıfı | Eski hali (örnek) | Düzeltme (disk kanıtı) |
|---|---|---|---|
| 1 | ADR-097 sanal bölümleri | `§1.6` · `§2.2 pipeline` · `§2.3–2.4 SignalType/ListenType` · `§6.1 DUP` | `§1/6 = §1 Bağlam madde 6 (satır 33, servis bulguları GERÇEK)` · `§2 K2/K6 (satır 42–53/77–78)` · `§5.5 gruplama (gerçek)` · pipeline/enum/DUP **silindi** (grep: 0 isabet) |
| 2 | Uydurma repo kanıtları | `client/js/core/signal.js` (149 satır) · `manifest.json` (27) · `seed_kit.json` · `signal_registry.json` · `history.js` · `/api/v2/signal` | `Test-Path/glob = 0 isabet` → silindi; yerine gerçek `nova-search-engine` paketleri (§4) |
| 3 | Uydurma ADR adları | `adr-010-media-provider-auth-gate.md` · `adr-095-mobile-auth-part1.md` · `adr-097-discovery-contract.md` · `U-L06 fragment` | gerçek dosya adları §9.2'de; `U-L06` vault'ta 0 isabet → silindi |
| 4 | Uydurma `log.md` satır atıfları | `log.md:774–801 · 788 · 1189 …` | `log.md` toplam **118 satır** — atfedilen satırlar yok; tüm `log.md:xxx` silindi (discovery-dogrulama §2 yeniden yazıldı) |
| 5 | Uydurma S-etiketleri | `S-2 … S-7` (S-3 schema, S-4 domain, S-5 auth gate, S-7 öneri) | yalnız **S-1** gerçek (K007-data 40+ atıf); yerine **D-1/D-3/D-4/D-5** (discovery-bagimliliklar §2) |
| 6 | Uydurma bölüm atıfları | `registry §9.9.2 ölçüm kanonu` · `README §2/§7 · ** yasağı` · `S-1 §6 kod→ar-ge→onay→doc` | kanon = **§9.12.1 (satır 761–764)** · README'de numaralı bölüm yok (satır 16/24–41) · sıra kuralı = root AGENTS.md §1 |
| 7 | Uydurma kavram adları | `_bileşik_dinleme_demeti` · `playlist.to (DMD)` · `sentinel/seq.1–30/oracle modu` | vault + v1/v2 spec = **0 isabet** → silindi; `⚠️ tanımsız` olarak taşındı |

### 9.4 Dosya ölçümü (2026-10-17 — kanonik yöntem)

| Dosya | Satır |
|---|---|
| `discovery-master.md` (bu dosya) | bkz. rapor satırı |
| K020-discovery toplamı (17 md) | `.ai/architecture/index.md` §9.x kapanışında orkestratör ölçer |

**Yanlış yöntem (geçersiz):** `Get-Content $f | Measure-Object -Line` (registry §9.12.1).

---

**Authority:** Bayram Ali / Vault Steward
