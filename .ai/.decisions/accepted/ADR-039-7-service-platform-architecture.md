---
title: "CoreMusic — ADR-039: 7-Service Platform Architecture (11 servis + PLANNED katmanlar)"
type: "architecture-decision"
category: "architecture"
date: "2026-09-26"
updated: "2026-09-26"
version: "1.0.0"
status: "accepted"
authority: "SSOT — CoreMusic servis platformu: 11 servis listesi, servis sınırları (sahip + veri sınırı), iletişim sözleşmesi ve kademeli ayrışma sırası"
kaynak: "Kullanıcı onaylı servis kapsamı (11 servis + PLANNED katmanlar) + disk/kod kanıtı taraması + exa web araştırması (18 kaynak)"
governance: "Red Team · Human Mode · Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-039: 7-Service Platform Architecture (Seven-Service Platform)

> **Durum:** ✅ **ACCEPTED** (kullanıcı onaylı kapsam) · **Tarih:** 2026-09-26 · **Debate:** ✅ **TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** · **Tech Lead:** ✅ · **Arch Lead:** ⏳
> **Karar serisi:** `.ai/.decisions/accepted/` · **Slug:** `ADR-039-7-service-platform-architecture`
> **İlgili kararlar:** [[ADR-004-multi-domain-spa]] (çok-domainli SPA iskeleti) · [[ADR-032-ipc-contract-versioning]] (IPC sözleşmesi) · [[ADR-026-download-service-architecture]] (download servisi — ilk ayrışacak servis) · [[../../brain.md]] (`:999` slotu) · [[../index.md]] (`:81` slug satırı)
> **Ad gerekçesi:** Başlıktaki "7-Service" sabittir — slug [[../index.md]] `:81` kaydından, "7" sayısı ise iki vault kaynağından gelir: k8 servis katmanı README §2 (7 backend servis haritası) ve `shared/config/domain.php` (7 subdomain). **Gerçek servis sayısı 11'dir** (§2.1) — hem kullanıcı onayı hem `.ai/CLAUDE.md` §5 K8 satırı (`+ Infra (11)`) ile hizalıdır. Ad değiştirilmez (In-Place Refactoring); sayı uyuşmazlığı bu ADR'nin gövdesinde çözülür.
> **Frozen notu:** ADR-001-037 **dokunulmamıştır** (yalnız atıf).

---

## §1 Bağlam (Context)

### §1.1 Mevcut Durum (disk + kod kanıtı — dürüst etiket)

**A) Sayım kanıtları — vault içinde 5 farklı sayı, çelişki açık:**

| Kaynak | Sayım | İçerik |
|---|---|---|
| [[../../CLAUDE.md]] §5 K8 satırı | **11** | `Control, Media, Audio, Device, Network, AI, Download + Infra (11)` · sınır: doğrudan çağrı yasak, Event Bus |
| Kullanıcı onayı (bu kararın kapsamı) | **11** | main, auth, music, media, download, admin, studio, car, home, assets, dev (§2.1) |
| `shared/src/Config/CLAUDE.md` `:45-55` | 9 satır | Domain/Port/Stack tablosu (assets ve api YOK; car/studio `—` port) |
| `shared/config/domain.php` | 7 + primary | auth, home, assets, music, admin, media, **api** (stüdyo/car/dev YOK) |
| [[../../architecture/k8-servis/README.md]] §2 | 7 | Control, Media, Audio, Device, Network, AI, Download |
| [[../../architecture/k8-servis/index.md]] Servis Haritası | 11 (farklı liste) | + Health, Search, Notification, Sync (domain listesi değil, işlev listesi) |

→ **Çelişki C1:** 4 farklı sayım (9 / 7 / 7 / 11) + 2 farklı 11 listesi (alan listesi ↔ işlev listesi). **Kazanan: kullanıcı onaylı 11 alan servisi = `.ai/CLAUDE.md` §5 "(11)" hizası**; k8 README 7 = `7 backend işlev düğümü` (K8.1-K8.7), domain.php 7 = subdomain listesi. Sayılar birbirinin rakibi değil, **aynı platformun üç kesitidir** — bu ADR hepsini tek tabloya bağlar (§2.1, §2.2).
→ **Çelişki C2:** `domain.php`'daki **api** subdomain'i 11 servis listesinde yok; listeye göre alan tanımlanmayan **dev** servisinin ise ne index.md'de ne brain'de slotu var (`ADR-082` **diskte YOK** → `⚠️ VERIFICATION REQUIRED`).

**B) KOD kanıtları (IMPLEMENTED — gerçek dosya):**

| Kanıt | İçerik | Etiket |
|---|---|---|
| `shared/src/Api/Gateway.php` | `dispatch()` + middleware pipeline + `/api/v1/{auth,user,music,playlist,media,download}` 6 rota · `@see ADR-084-api-gateway-architecture` | **IMPLEMENTED** (tek Gateway, ağ değil süreç-içi) |
| `shared/src/Api/Registry/ServiceRegistry.php` | `private array $services` · `register/discover/healthCheck` · `@see ADR-084` | **IMPLEMENTED** — **süreç-içi** servis dizini; **ağ keşfi (service discovery) DEĞİL** |
| `shared/src/Api/Bff/` (5 dosya) | `BffLayer` + `Desktop/Embedded/Mobile/Spa` varyantları | **IMPLEMENTED** (BFF katmanı iskeleti) |
| `shared/src/Events/` | 9 Domain event + 3 Integration event + `EventDispatcher` (PSR-14) | **IMPLEMENTED** |
| `shared/composer.json` `:16-17` | `psr/event-dispatcher ^1.0` + `symfony/event-dispatcher ^7.0` | **IMPLEMENTED** (olay altyapısı) |
| `auth.coremusic.net/` (70 dosya), `home.coremusic.net/` (29), `assets.coremusic.net/` (550) | Kök dizinlerdeki tek fiziksel subdomain kodları | **IMPLEMENTED** |

**C) PLANNED katmanlar — kod 0 (dürüst bulgu):**

Tarama: `*.cpp / *.hpp / *.h / *.c / *.cs / *.ts` = **0 dosya**; `*.py` = **1** (`.ai/scripts/fix-mojibake.py` — vault aracı, servis kodu değil); `.ai/.sql/mysql/` 18 dosya içinde `outbox` = **0 eşleşme**; kök `package.json` yalnız `playwright`; fiziksel kök dizinler: `.ai .claude .git .github .opencode .workflows assets.coremusic.net auth.coremusic.net home.coremusic.net shared` → **`music/ admin/ media/ download/ studio/ car/ dev/` fiziksel olarak YOK, kökte `index.html` YOK.**

| PLANNED katman | İddia | Kod kanıtı |
|---|---|---|
| Node.js download servisi (:3001, TS) | ADR-026 + config tablo `⚠️ PLANNED (dizin yok)` | **0** (`download.coremusic.net/` = false, `*.ts` = 0) |
| C++ app / server / client (Audio, Device, Network) | k8 README §2 C++20 JUCE satırları | **0** (`*.cpp/*.h/*.hpp` = 0) |
| Python araçlar (AI katmanı) | k8 README §2 "PHP + Python" | **0 servis kodu** (yalnız 1 vault scripti `.py`) |
| Outbox / event depolama şeması | k8 README §3 olay listesi + `ADR-081` outbox başlığı | **0** (18 SQL dosyasında `outbox` = 0) |
| k8 `index.md` `status: "implemented"` | katman dosyasının kendi iddiası | **Çelişki C6:** kod 0 → bu etiket dokümanseldir, implementasyon değildir |

**D) K8 mimari kanıtları (doküman olarak IMPLEMENTED):** [[../../architecture/k8-servis/README.md]] §3 = 9 olay (`user.login` … `device.disconnect`) · §5 = doğrudan çağrı **yasak**, PSR-14 Event Bus zorunlu, circuit breaker, max 3 retry · Alt Katman Şeması = 10 düzey-2 düğüm (K8.1-K8.10), 43 düzey-3, çelişki defteri C1-C5 (kendi içlerinde kayıtlı) · `.ai/brain.md` `:179` = `Event Driven (ADR-086)` · `:1021-1027` = ADR-083-089 slotları.

### §1.2 Sorun Tanımı (Problem)

1. Vault'ta **4 farklı servis sayımı** (9 / 7 / 7 / 11) ve **2 farklı 11 listesi** var; hangisinin bağlayıcı olduğu yazılmamış → her yeni servis/doküman kendi sayısını üretiyor.
2. "Servis" kelimesi **iki şey** için kullanılıyor: (a) **alan/doman servisi** (auth, music, media …) ve (b) **işlevsel K8 düğümü** (Control, Media, Audio …). Sınır sahipliği (kim hangi veriye yazar) hiçbir yerde tek tabloda toplanmamış.
3. İletişim sözleşmesi parçalı: `ADR-032` (IPC) diskte, `ADR-084` (Gateway), `ADR-086` (Event Driven), `ADR-085` (shared lib) **diskte YOK**; `ServiceRegistry` süreç-içi olduğu hâlde "registry" kelimesi ağ keşfi gibi okunabilir → hangi modelin geçerli olduğu belirsiz.
4. Ayrışma sırası (hangi servis önce kopar, hangi veri önce sahiplenilir) tanımsız → big-bang mikroservis riski ve/veya sonsuz monolit kalma riski birlikte duruyor.

### §1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (v7.2.0 — resmi/üretici kaynağı önce, her ana iddia ≥2 çapraz kaynak, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`).
> **Araç notu:** `websearch` kanalı boş sonuç döndürdü → araştırma **exa** ile yapıldı (5 exa sorgusu; aşağıdaki 18 kaynak dosyaya giren doğrudan görülen kaynaklardır). Sorgu alanları **konu bazlı** verilmiştir (birebir metin dökümü saklanmadı).

| Alan | Değer |
|------|-------|
| Web Search **Query** | `1)` microservices vs monolith — ne zaman bölünür, doğru servis sayısı / right-sizing · `2)` service boundary design — bounded context keşfi, veri sahipliği, katman değil domain bölme · `3)` service registry vs server-side discovery — servis adreslenebilirliği kalıpları · `4)` saga pattern — dağıtık işlemde tutarlılık sınırları ve alternatifleri · `5)` shared kernel (DDD) — sınırlar arası paylaşımlı kodun kuralı (5 exa sorgusu) |
| Web Search **Konusu** | Mikroservis ↔ monolit ayrım noktası, servis sınırı keşfi, servis keşfi (registry / discovery), saga'nın sınırları, shared kernel yönetişimi |
| Web Search **Bağlam** | ADR-039 kapsamı: 11 servis listesi, sahip + veri sınırı, iletişim (IPC + olay/outbox), paylaşımlı `shared/` katmanı ve kademeli ayrışma sırasının gerekçelendirilmesi |
| Web Search **Kısa Açıklama** | Doğru servis sınırı **veri sahipliği + bounded context** ile çizilir (katman/takım değil); erken bölünme ağ + operasyonel maliyet üretir, monolith-first önerilir; **registry (dizin) ≠ discovery (çözümleme)** ayrı kalıplardır; saga ACID benzeri rollback/izolasyon **sunmaz**; shared kernel ancak açık rıza ve küçük tutarlılıkla çalışır |
| Web Search **Uzun Açıklama** | **(1)** Right-sizing: s5labs (2025-12) ve getdx (2025-06) servis sayısının ekip/ölçek verisiyle belirlenmesini; AWS compare, IBM Think ve Atlassian monolith-önce-kademeli-bölme tezini anlatır. **(2)** Sınır: Martin Fowler *Bounded Context*, Microsoft *microservice boundaries* (tek servis = tek takım + tek veri sahibi), nilus.be ve mvanleest sınır keşfi pratikleri — hepsi "önce bağlam, sonra süreç" der. **(3)** Keşif: microservices.io *service registry* ve *server-side discovery*, Microsoft *addressability/service registry* ve HashiCorp Consul — registry bir **dizin servisidir**, istemcinin adresi çözümlemesi ayrı adımdır; süreç-içi array tabanlı kayıt bu kalıpların yerine geçmez. **(4)** Saga: microservices.io, Microsoft Azure ve Oracle "sagas are great" saga'yı dağıtık işlem çözümü olarak sunar; ufried *limits of the saga pattern* ise compensating transaction'ların ACID izolasyonu vermediğini, business-level idempotency ve tekrar-çalıştırılabilirlik gerektiğini söyler → outbox + idempotent event şartı. **(5)** Shared kernel: DDD fed.wiki paylaşımlı çekirdeğin yazılı rıza ve dar tutum ister. |
| Web Search **Paragraf Veri Uzun** | Beş sorgu tek bir resim çiziyor: platform, servis **sayısından** önce servis **sınırını** çizen bir mimari ister; sınır = "kim bu veriye yazar" (veri sahipliği) olduğundan CoreMusic'in 11 alan servisi birbirinin kopyası değil, **veri sahipliğiyle ayrılan** birimler olmalıdır. Bölünme sırası, dış bağımlılığı en yüksek ve en az paylaşımlı birimden başlar (download gibi izole iş), kritik kimlik akışının (auth) en sona kalması önerilir — çünkü erken bölme operasyonel yük (dağıtık gözlem, ağ hata modu) üretir. İletişim tarafında registry/discovery ayrımı, süreç-içi `ServiceRegistry`'nin **tek başına yetersiz** olduğunu; saga literatürü ise outbox + idempotent event olmadan dağıtık yazımın veri kaybı ürettiğini gösterir. Bu üç bulgu, §2 kararının (kademeli ayrışma + olay/outbox + süreç-içi registry'ye ağ keşfiyme etiketi koymama) doğrudan gerekçesidir. |
| Web Search **Sonucu** | **18 doğrudan kaynak** (aşağıda); her ana iddia (bölme tezine direnç, sınır = veri sahipliği, registry ≠ discovery, saga sınırları, shared kernel kuralı) **en az 2 bağımsız kaynakla** çaprazlandı. **Dış kaynakla doğrulanamayan tek konu = CoreMusic'in 11 servis sayısı ve listesi** → iç kanıt (kullanıcı onayı + `.ai/CLAUDE.md` §5) ile sabitlendi, `⚠️ VERIFICATION REQUIRED` değil (dış doğrulama istemez; iç karardır). |
| Web Search **Alınan Karar** | **Kademeli modüler monolit → servis**: 11 alan servisi tek tabloda tanımlanır ve sınırlar **sahip + veri sınırı** ile çizilir; ayrışma sırası **download → media → auth** (en izole → en kritik); iletişim ADR-032 IPC sözleşmesi + **olay/outbox** modeliyle (doğrudan servis↔servis HTTP yasak); paylaşımlı kod tek `shared/` namespace'inde kalır; **ani (big-bang) mikroservis yok** |
| Web Search **Sonuç** | Dış kaynaklar **kademeli ayrışma + veri-sahipliği sınırı + outbox idempotency** tezini **destekliyor**; big-bang dağıtık mimari ve katman bazlı bölünme **karşıtlığı** kaynaklarda net (§3 alternatif 1 ve 3); saga/registry bulguları §2.2 iletişim kurallarına ve §4.3 risk tablosuna (R2, R3) işlendi |

**Kaynak listesi (18):** 1) s5labs.io — right-sizing microservices (2025-12) · 2) getdx.com — microservices vs monolith (2025-06) · 3) aws.amazon.com/compare — microservices vs monolith · 4) ibm.com/think — microservices vs monolith · 5) atlassian.com — microservices vs monolith · 6) martinfowler.com — BoundedContext · 7) learn.microsoft.com — microservice boundaries · 8) nilus.be — service boundary discovery · 9) mvanleest.com — service boundaries · 10) microservices.io — service registry · 11) microservices.io — server-side discovery · 12) learn.microsoft.com — microservices addressability / service registry · 13) docs.hashicorp.com — Consul service discovery · 14) microservices.io — saga pattern · 15) learn.microsoft.com — saga (Azure) · 16) blogs.oracle.com — sagas are great · 17) ufried.com — limits of the saga pattern · 18) ddd.fed.wiki.org — shared kernel.

### §1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| **Frozen 001-037 immutabel** | Yalnız atıf; metin değiştirilmez |
| **Kod 0 → PLANNED disiplini** | download (Node/TS), C++ (audio/device/network), Python (AI) ve outbox SQL katmanlarının tamamı repoda 0 (§1.1-C); uygulama adımları bu etiketle yazılır |
| **Doğrudan servis↔servis HTTP yasak** | `.ai/CLAUDE.md` §5 K8 sınırı + k8 README §5; iletişim yalnız IPC sözleşmesi ve olay üzerinden |
| **FFmpeg/transcode K8'in dışındadır** | CLAUDE §5 K15 sahipliğidir (k8 README C4 düzeltmesi) |
| **Diskte olmayan ADR'ye wiki-link kurulmaz** | ADR-040, ADR-082, ADR-084, ADR-085, ADR-086, ADR-087 dosyaları diskte YOK → düz metin + `⚠️ VERIFICATION REQUIRED` (§6) |
| **Sayım çelişkisi kapatılmadan ikinci sayı üretilmez** | C1/C2 kayıtlı kalır; yeni bir "servis sayısı" iddiası bu ADR'ye bağlanır |
| **Tek yazma kanalı** | Tüm vault yazımı `.ai/scripts/vault-utf8-writer.mjs`; `log.md` yalnız append |
| **REDACTED** | Secret/credential hiçbir koşulda yazılmaz |

---

## §2 Karar (Decision)

**CoreMusic platformu 11 alan servisi olarak tanımlanır ve ANİ DEĞİL, KADEMELİ olarak ayrıştırılır: mevcut çok-domainli yapı (ADR-004) korunur; her servise önce *sahip + veri sınırı* verilir, sonra ayrışır. Servisler arası doğrudan HTTP çağrısı yasaktır — iletişim ADR-032 IPC sözleşmeleri ve olay tabanlı (event + outbox) model ile yürür. Paylaşımlı kod tek `shared/` namespace'inde toplanır. Ayrışma sırası: download → media → auth.**

### §2.1 Servis Listesi (11 — kullanıcı onaylı, sayım çelişkisi bu tabloda kapanır)

| # | Servis | Domain / Port | Stack (kaynak) | Fiziksel kanıt | Durum |
|---|--------|---------------|----------------|----------------|-------|
| 1 | **main** | coremusic.net:80 | Vanilla JS + PHP 8.4 (config tablo) | kök dizin var, `index.html` **YOK** | **PLANNED** (giriş dosyası yok) |
| 2 | **auth** | auth.coremusic.net (`—`) | PHP 8.4 | `auth.coremusic.net/` = **70 dosya** | **IMPLEMENTED** (dizin + kod) |
| 3 | **music** | music.coremusic.net:81 | PHP 8.4 + JS | dizin **YOK** | **PLANNED** |
| 4 | **media** | media.coremusic.net:5000/6000 | PHP (+ FFmpeg → **K15**, K8 dışı) | dizin **YOK** | **PLANNED** |
| 5 | **download** | download.coremusic.net:3001 | Node.js + TS | dizin **YOK**, `*.ts` = 0 | **PLANNED** (ADR-026 şart 1a) |
| 6 | **admin** | admin.coremusic.net:80 | PHP 8.4 | dizin **YOK** | **PLANNED** |
| 7 | **studio** | studio.coremusic.net:81 | Vanilla JS | dizin **YOK** | **PLANNED** |
| 8 | **car** | car.coremusic.net (`—`) | Vanilla JS | dizin **YOK** | **PLANNED** |
| 9 | **home** | home.coremusic.net:81 | Vanilla JS | `home.coremusic.net/` = **29 dosya** | **IMPLEMENTED** (dizin + kod) |
| 10 | **assets** | assets.coremusic.net | statik (CSS/JS) | `assets.coremusic.net/` = **550 dosya** | **IMPLEMENTED** (dizin + kod) |
| 11 | **dev** | dev.coremusic.net (PLANNED) | `⚠️ VERIFICATION REQUIRED` (stack vault'ta sabitlenmemiş) | dizin **YOK**; `ADR-082` **diskte + index.md'de YOK** | **PLANNED** |

**Liste dışı not (C2):** `domain.php`'deki `api` subdomain'i 11'e **girmez** — API trafiği Gateway (`shared/src/Api/Gateway.php`) tarafından karşılanır; ayrı servis olarak sayılmaz.

**Kesişim tablosu (aynı platformun üç kesiti):** alan listesi (11, bu tablo) · K8 işlev düğümü (README §2 = 7: Control, Media, Audio, Device, Network, AI, Download; k8 index = 11 işlev) · domain listesi (domain.php = 7 subdomain). Eşleme: `auth↔Control`, `media↔Media`, `main/home/assets/studio/car↔sunum`, `download↔Download`, `dev/infra↔+Infra`, `audio/device/network` işlevleri **C++ katmanı = PLANNED kod 0** (§1.1-C).

### §2.2 Servis Sınırı, İletişim ve Paylaşımlı Katman

**(a) Sınır kuralı — sahip + veri sınırı (karar):** Her servis kendi veri şemasına **tek yazar**dır; başka servisin şemasına doğrudan SELECT/UPDATE **yok**. Çapraz okuma yalnızca **olay** (yayın → abone) ile olur. Şema tarafı ADR-003 (18 BCNF) ve ADR-002 (PDO, ORM yok) ile uyumludur; veri↔servis dağılım tablosu bu ADR'nin eki değil, **uygulama adımının** (§5.1 adım 5) ürünüdür — burada yalnız **kural** sabitlenir.

**(b) İletişim (mevcut kanıt + hedef):**

| Katman | Kural | Durum |
|---|---|---|
| Olay yayını/aboneliği | PSR-14 `EventDispatcher` + 9 Domain / 3 Integration event (`shared/src/Events/`) | **IMPLEMENTED** |
| Olay adları (hedef) | `user.login`, `user.logout`, `track.play`, `track.download`, `playlist.create`, `playlist.update`, `eq.preset.change`, `device.connect`, `device.disconnect` (k8 README §3) | **PLANNED** (adlar kodda yok) |
| IPC sözleşmesi | ADR-032 sürümleme + kontrat disiplini | **IMPLEMENTED/PLANNED ayrımı ADR-032'dedir** |
| Ağ keşfi | `ServiceRegistry` = **süreç-içi dizin** (`register/discover/healthCheck`, `private array`); ağ keşfi (server-side discovery / Consul sınıfı) **kurulmamıştır** | süreç-içi **IMPLEMENTED** · ağ keşfi **PLANNED** (`⚠️ VERIFICATION REQUIRED` — hangisinin hedef olduğu deploy öncesi sabitlenir) |
| Doğrudan çağrı | Servis↔servis HTTP **yasak**; circuit breaker + max 3 retry | kural **IMPLEMENTED** (doküman), uygulama **PLANNED** |
| Outbox / idempotent event | `ADR-081` outbox başlığı + saga literatürü (§1.3 kaynak 14-17) | **PLANNED** — 18 SQL dosyasında `outbox` = 0 |

**(c) Paylaşımlı `shared/` katmanı:** Tek namespace, tek BOM (ADR-085 hedefi — `ADR-085` dosyası **diskte YOK** → `⚠️ VERIFICATION REQUIRED`, wiki-link kurulmadı). Kural: servisler `shared/`'i **bağımlılık olarak** kullanır, `shared/` hiçbir servise bağımlı değildir.

**(d) Kademeli ayrışma sırası (karar):**

| Sıra | Ayrışacak katman | Gerekçe | Durum |
|---|---|---|---|
| 1 | **download** (Node.js :3001) | En izole iş; dış bağımlılık (Deezer/YouTube) + queue/cache; en az paylaşımlı veri (ADR-026 + ADR-028 anti-ban) | ⏳ PLANNED (kod 0) |
| 2 | **media** (5000/6000) | Ağır iş (FFmpeg → K15 hattı) ayrı ölçek ister; kütüphane metadata'sı net sahipli | ⏳ PLANNED (kod 0) |
| 3 | **auth** | En kritik akış; yalnız diğer ikisi stabil iken ayrılır (session/RBAC tek noktada kalmalı) | ⏳ PLANNED |

Ayrışma tetikleyicisi: **ölçülmüş** bir darboğaz (gecikte, deploy süresinde veya ekip bağımsızlığında) — tahminle bölme yok.

---

## §3 Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Ani (big-bang) mikroservis — 11 servis aynı anda ağ üzerinden** | Tek seferde "hedef mimari" | Dağıtık gözlem/ağ hata modu henüz yok; outbox 0; C++/Node/Python katmanları kod 0 → ilk gün 11 başarısız servis | §1.3 kaynak 1-5 (right-sizing, monolith-önce) doğrudan karşıt; §4.3 R1'e düşer |
| 2 | **Tek tam monolit — servis listesi/ sınırı olmadan** | En az operasyonel yük | Kullanıcı 11 servis onayladı; ADR-004 zaten 7 subdomain'de ayrışmış durumda; sınır tanımsız kalırsa veri sahipliği çakışır | Mevcut yapı **modüler monolit + net sınır** ile korunur (bu ADR §2.2-a); "hiç sınır yok" reddedildi |
| 3 | **Katman bazlı bölme (her katman ayrı servis: Controller-servis, Repo-servis)** | Kod içi benzerlik | Ağ I/O ile katmanlar arası her çağrı; performans ve test maliyeti katlanır | §1.3 kaynak 6-9: sınır **domain/bounded context** ile çizilir, katmanla değil; ADR-002/003 yapısını da bozar |
| 4 | **Outbox'sız olay yayını (ara belleğe yazıp hemen gönder)** | Daha az altyapı | Olay ↔ veri yazımı atomik değil; crash'te olay kaybı; saga'nın ACID izolasyonu zaten yok (§1.3 kaynak 14-17) | §2.2-b outbox/idempotent şartı; 0 outbox şeması bu kararın PLANNED işi olarak yazıldı |
| 5 | **`shared/` yerine servis başına kopya kod (fork)** | Servisler tam bağımsız görünür | 11× güvenlik/yama/ürümlülük takibi; ADR-085 "tek shared/ + PSR-4" hedefiyle çelişir | Paylaşımlı çekirdek tek namespace'te kalır (§2.2-c); shared kernel kuralı §1.3 kaynak 18 |

---

## §4 Sonuçlar (Consequences)

### §4.1 Olumlu Sonuçlar

- **Tek sayım:** 11 servis + 3 kesit eşlemesi tek tabloda (§2.1) → yeni doküman/kod kendi sayısını üretemez.
- **Sahiplik net:** "kim bu veriye yazar" kuralı (§2.2-a) veri çakışmasını ve gizli bağımlılığı önler; 18 BCNF şeması (ADR-003) servis sınırına oturur.
- **Ölçülebilir ayrışma:** download → media → auth sırası izole→kritik akıştır; ilk adımın iptal maliyeti düşüktür (kod 0).
- **Mevcut kod değerlenir:** Gateway (6 rota), BFF (4 varyant), PSR-14 Events (12 event) zaten IMPLEMENTED — sıfırdan yazım gerekmez.
- **Dış destek:** Kademeli ayrışma + veri sahipliği sınırı 18 kaynakla çaprazlandı (§1.3).

### §4.2 Olumsuz Sonuçlar

- **Ağ maliyeti:** olay tabanlı iletişim, süreç-içi çalıştırmaya göre daha yüksek gecikme ve daha fazla gözlem bileşeni ister.
- **PLANNED yükü:** 11 servisin 8'i + tüm C++/Node/Python/outbox katmanı kod 0 → bu ADR **üretim değil, iskelet/karar** seviyesindedir.
- **Sayım çelişkisi kalıcı iz taşır:** C1/C2 (9 vs 7 vs 11, `api`/`dev` tutarsızlığı) §2.1'de kapatıldı ama vault dosyalarının kendi metinleri hâlâ eski sayılarını taşır (düzeltme §5.1 adım 4).
- **`status: accepted` ≠ yürürlükte uygulama:** onay, kararın bağlayıcı olduğunu söyler; kod PLANNED olarak kalır.
- **Keşif ikiliği:** süreç-içi registry hazır olduğu için ağ keşfi ertelenebilir → hangi modelin hedef olduğu belirsiz kalabilir (`⚠️ VERIFICATION REQUIRED`).

### §4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **R1 Erken parçalanma — bölünen servisler istenenden önce operasyonel yük getirir** | Orta (%40-70) | Yüksek | Ayrışma tetikleyici **ölçülmüş darboğaz** (§2.2-d); ölçüm yoksa adım atlanmaz; big-bang yasak (§3 alt.1) |
| **R2 Ağ maliyeti + dağıtık tutarlılık — olay kaybı/tekrar** | Yüksek (>%70) | Yüksek | Outbox + idempotent consumer (§2.2-b, PLANNED); saga sınırları §1.3 kaynak 14-17; 0 outbox şeması §5.1 adım 6 |
| **R3 Sayım/çelişki geri gelir (C1, C2, C6)** | Yüksek (>%70) | Orta | Bu ADR §2.1 = tek sayı kaynağı; k8 README/index/config/domain.php düzeltmeleri §5.1 adım 4'te; `k8 index status: implemented` etiketi düzeltilir |
| **R4 Servis sınırı yanlış çizilir (yanlış veri sahibi)** | Orta (%40-70) | Yüksek | Sınır önce **kurallar** olarak (§2.2-a), dağılım sonra (adım 5); yanlışlık = yeni ADR ile düzeltilir, frozen'a geçmeden |
| **R5 Ağ keşfi modeli belirsiz (süreç-içi ↔ dağıtık)** | Orta (%40-70) | Orta | Deploy öncesi karar: süreç-içi registry + Gateway yeterli mi, yoksa dağıtık keşif mi → `⚠️ VERIFICATION REQUIRED` satırı §2.2-b'de açık; ADR-084 dosyası yazılınca kapanır |
| **R6 PLANNED katmanlar hiç yazılmazsa ADR kâğıt üzerinde kalır** | Orta (%40-70) | Orta | §5.1 adımları 4-9 PLANNED etiketli ve sahipli; `log.md` append ile izlenir |

### §4.4 Fallback (geri birleşim / geri dönüş)

1. **download ayrılamazsa:** servis süreç olarak değil, **monolit içindeki modül** olarak kalır; §2.1 satır 5 `PLANNED (modül)` işaretlenir, sayı 11'de sabit kalır (liste değişmez, yalnız ayrışma durumu değişir).
2. **Olay modeli taşıyamazsa:** geçici olarak Gateway üzerinden **stateless** çağrı (mevcut `Gateway::dispatch`) kullanılır — servis↔servis doğrudan çağrı **yine yasak** kalır; outbox ertelenir, R2 açık risk olarak izlenir.
3. **Ağ keşfi gerekmese:** süreç-içi `ServiceRegistry` + Gateway yeterlidir; ek altyapı kurulmaz (YAGNI).
4. **Sınır yanlış çizilirse:** veri sahipliği yeniden dağıtılır → **yeni ADR** (NNN+1) + bu dosya `superseded-by` ile bağlanır; metin silinmez.
5. **Tam geri birleşim:** ayrışmış herhangi bir katman, süreç-içi modüle geri alınabilir (kod 0 olduğu sürece iptal = yalnız plan işidir).

---

## §5 Uygulama (Implementation)

### §5.1 Adımlar

| # | Adım | Sorumlu | Süre | Durum |
|---|------|---------|------|-------|
| 1 | Bu ADR'yi şablondan üret + künye/§1-§7 dolu (Guardrail #16) | Vault Steward | 2 dk | ✅ UYGULANDI (2026-09-26) |
| 2 | `.ai/log.md`'ye 1 satır append (`ADR-039 yazıldı (debate PENDING)`) | Vault Steward | 1 dk | ✅ UYGULANDI (2026-09-26) |
| 3 | Debate + Tech Lead onayı | MO + Tech Lead | 2 gün | ✅ UYGULANDI (2026-09-26 — 3 tur / 20 persona, 18/2/0 KABUL; Tech Lead ✅) |
| 4 | **Sayım düzeltmesi:** `k8-servis/README.md` §1/§2 (7) + `k8-servis/index.md` (11 işlev) + `shared/src/Config/CLAUDE.md` (9 satır) + `domain.php` (7 subdomain) → §2.1'e atıf/sayım notu eklenir; `k8 index status: implemented` → dokümansel etiket (C6) | Vault Steward + Backend | 3 saat | ⏳ PLANNED |
| 5 | **Veri sahipliği dağılımı:** 18 BCNF şemasının (ADR-003) 11 servise sahip eşlemesi yazılır (kural §2.2-a'nın somut hâli) | Data Engineer | 2 gün | ⏳ PLANNED |
| 6 | **Outbox + idempotent event şeması** (18 SQL'de 0 → ekleme) + olay adlarının (k8 README §3) `shared/src/Events/` koduna taşınması | Data + Backend | 3 gün | ⏳ PLANNED |
| 7 | **download servisi ilk ayrışma:** Node.js :3001 uygulaması (ADR-026 + ADR-028 anti-ban) | Backend + DevOps | 10 gün | ⏳ PLANNED (kod 0) |
| 8 | **Ağ keşfi kararı:** süreç-içi registry yeterli mi, dağıtık keşif mi → R5 kapanışı (`ADR-084` yazıldığında wiki-link kurulur) | Backend + DevOps | 1 gün | ⏳ PLANNED |
| 9 | **media ayrışması** (FFmpeg işi K15 hattında kalır — C4) → sonra **auth** ayrışması | Backend + Security | 10 gün | ⏳ PLANNED |
| 10 | `.ai/.decisions/index.md:81` `../brain.md` satırının — index.md içindeki mevcut biçim (bu dosyadan hedef: ../../brain.md) bu dosyaya bağlanması + brain.md ADR-039 özeti (vault sync — ayrı işlem) | MO (vault-updater) | 1 dk | ⏳ PLANNED |
| 11 | **(Debate Şartı 1a)** `ServiceRegistry` etiketi = **süreç-içi dizin (private array, ağ keşfi DEĞİL)** her atıfta net yazılır + `ADR-084-api-gateway-architecture` (6× `@see`) için düz metin + `⚠️ VERIFICATION REQUIRED` (dosya diskte yok → wiki-link KURULMAZ) | Backend + Vault Steward | 1 saat | ⏳ PLANNED (debate şartı) |
| 12 | **(Debate Şartı 1b)** Servis sayımı SSOT = **§2.1 Config/Domain tablosu (11)**; 9/7/7/11 çelişkileri (C1-C2) yalnız bu tabloya atıf ile çözülür, ikinci sayı üretilmez | Vault Steward | 3 saat | ⏳ PLANNED (debate şartı — §5.1 adım 4'e bağlı) |
| 13 | **(Debate Şartı 2)** PLANNED katmanlar (kod 0) için **faz yol haritası**: Node.js download → C++ (audio/device/network) → Python (AI) + outbox; her fazın **çıkış ölçütü** tanımlı (dizin + kod dosyası + test eşiği) | Tech Lead + Backend | 2 gün | ⏳ PLANNED (debate şartı) |
| 14 | **(Debate Şartı 3)** **Servis sınırı + outbox idempotent test paketi**: §2.2-a sınır kuralı (tek yazar) ve outbox idempotency için testler yazılır; çıkış ölçütü yeşil test | QA + Data | 3 gün | ⏳ PLANNED (debate şartı) |

### §5.2 Geri Dönüş Planı

1. **Karar seviyesi:** `status: accepted` ama **frozen değil** → vazgeçiş/yeni karar = yeni ADR (NNN+1) ve bu dosya `superseded-by` ile bağlanır; metin **silinmez/değiştirilmez**.
2. **Ayrışma seviyesi:** Herhangi bir adım (7, 9) geri alınabilir — servis süreç olarak kapatılıp **modüle** düşürülür (§4.4-1); sayı 11 değişmez.
3. **Şema seviyesi:** Outbox (adım 6) eklenirse geri dönüş = yeni migration satırı; `log.md` append-only olduğu için geri dönüş de **yeni satırdır** (silme yok).
4. **Dizin seviyesi:** `index.md` kayıt satırı (adım 10) geri alınırsa satır `—` olarak işaretlenir, silinmez.
5. **Bozulma durumunda:** `node .ai/scripts/vault-utf8-writer.mjs repair --file <dosya>` (yedek alır) → gerekirse `git checkout` (AGENTS §18 #5).

---

## §6 İlgili Dokümanlar (İlişki tablosu — tüm wiki-link hedefleri diskte doğrulandı: 25/25)

| Dosya (wiki-link) | İlişki |
|-------|--------|
| [[../../CLAUDE.md]] | Ana sözleşme; §5 K8 satırı = **11** sayımının kaynağı + "doğrudan çağrı yasak / Event Bus" sınırı |
| [[../../brain.md]] | `:999` ADR-039 slotu · `:179` Event Driven (ADR-086) · `:1021-1027` ADR-083-089 slotları |
| [[../../AGENTS.md]] | Agent registry; domain boundary (§5) ve frozen kuralı (§25.3) |
| [[../../WORKFLOW.md]] | Süreç/fazlar — uygulama adımlarının bağlandığı akış |
| [[../../index.md]] | Master katalog |
| [[../../log.md]] | Audit trail — bu ADR'nin append kaydı |
| [[../index.md]] | Karar dizini `:81` slug satırı (bu dosyaya bağlanması §5.1 adım 10) |
| [[../CLAUDE.md]] | Karar dizini kuralı |
| [[../../.templates/adr/adr-template.md]] | Guardrail #16 şablonu (bu dosyanın iskeleti) |
| [[ADR-001-vanilla-js-itcss]] | Frontend stack — `main/home/assets/studio/car` sunum servislerinin temeli |
| [[ADR-002-pdo-mandatory-no-orm]] | Veri erişim kuralı — §2.2-a veri sınırının uygulama zemini |
| [[ADR-003-multi-db-bcnf]] | 18 BCNF şeması — veri sahipliği dağıtımının girdisi (§5.1 adım 5) |
| [[ADR-004-multi-domain-spa]] | 7 subdomain + tek build + link kurma — mevcut iskelet, bu ADR onu **korur** |
| [[ADR-017-dsp-hardware-mode]] | C++/ASIO katmanı — §1.1-C'de kod 0 olarak sayılan PLANNED katmanın sahibi |
| [[ADR-020-api-public-security]] | API yüzeyi güvenliği — Gateway `/api/v1/*` rotalarının güvenlik şartı |
| [[ADR-024-ecosystem-modular-docs]] | Modüler doküman yapısı — servis dokümanlarının düzeni |
| [[ADR-026-download-service-architecture]] | **download servisi** (§2.2-d 1. sıra) — ADR-026 şart 1a = dizin yok/planned kanıtı |
| [[ADR-028-anti-ban-system]] | download servisinin dış bağımlılık riski — ayrışma gerekçesi |
| [[ADR-030-ai-strategy-core]] | AI işlevi (K8.6) — Python katmanı kod 0 (§1.1-C) |
| [[ADR-032-ipc-contract-versioning]] | **IPC sözleşmesi** — §2.2-b iletişim katmanı |
| [[ADR-081-multi-provider-data-sync]] | Outbox+WAL başlığı — §2.2-b outbox satırının vault kaynağı (dosya var, içerik `⚠️ VERIFICATION REQUIRED`) |
| [[../../architecture/k8-servis/README.md]] | 7 servis haritası, 9 olay, iletişim kuralları, çelişki defteri C1-C5 |
| [[../../architecture/k8-servis/index.md]] | 11 işlev haritası + `status: implemented` iddiası (C6) |
| [[../../architecture/k9-api-routing/README.md]] | K9 → K8 çağrısı: Gateway/middleware zinciri |
| [[../../architecture/k0-isletim-sistemi/README.md]] | K0 altyapı katmanı — servislerin barınma zemini |

**Wiki-link KURULMAYAN (diskte dosya YOK → düz metin + `⚠️ VERIFICATION REQUIRED`):**

| Referans | Durum |
|---|---|
| **ADR-084-api-gateway-architecture** | Dosya **diskte YOK** (`brain :1022`, `index.md :106` kaydı var; kodda 6× `@see`) → `⚠️ VERIFICATION REQUIRED` |
| **ADR-086-event-driven-architecture** | Dosya **diskte YOK** (`brain :179/:1024`, `index.md :108`) → `⚠️ VERIFICATION REQUIRED` |
| **ADR-085-modular-composer-packages** (shared lib) | Dosya **diskte YOK** (`index.md :107`) → `⚠️ VERIFICATION REQUIRED` |
| **ADR-087-master-implementation-plan** | Dosya **diskte YOK** (`brain :1025`) → `⚠️ VERIFICATION REQUIRED` |
| **ADR-040-database-authority** | Dosya **diskte YOK** (`index.md :82`) → `⚠️ VERIFICATION REQUIRED` |
| **ADR-082** (dev servisi atfı) | Ne dosya ne `index.md`/`brain.md` kaydı **YOK** → `⚠️ VERIFICATION REQUIRED` |

**Debate şartlarının bağlandığı dokümanlar (§7.1 — 3 şart):** Şart 1a → `ServiceRegistry` (süreç-içi etiket) + **ADR-084 düz metin/V.R.** · Şart 1b → §2.1 sayım SSOT (11) · Şart 2 → §1.1-C PLANNED faz yol haritası (Node → C++ → Python, çıkış ölçütü) · Şart 3 → §2.2-a/b sınır + outbox idempotent test paketi (uygulama: §5.1 adımlar 11-14).

---

## §7 Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali / Vault Steward | 2026-09-26 | ✅ |
| Tech Lead | — | 2026-09-26 | ✅ |
| Arch Lead | — | — | ⏳ |

### §7.1 Tartışma Kaydı (Debate)

| Alan | Değer |
|---|---|
| **Durum** | ✅ **TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** — frontmatter `debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"` |
| **Tur 1 — Bulgular (20 persona)** | ServiceRegistry **süreç-içi** (private array, ağ keşfi DEĞİL) · Gateway `dispatch()` IMPLEMENTED 6 rota ama 6× `@see ADR-084` (dosya diskte YOK) · sayım çelişkisi C1-C2 (9 config / 7 domain.php / 7 k8 / 11 kullanıcı → **11 kazandı**) · PLANNED katman kodu **0** (`*.cpp/ts` = 0, `*.py` = 1 vault script) · implemente: auth 70 dosya, home 29, assets 550, Events 12, Bff 5 · ADR-082/084/085/086/087 diskte YOK → `⚠️ VERIFICATION REQUIRED` · 18 kaynak (5 exa sorgu) · 15 kabul/neutral + 4 uyarı (DevOps: registry etiketi + ADR-084 şart; QA: sayım SSOT; Critic: PLANNED faz + V.R. zinciri şart) |
| **Tur 2 — İtiraz→çözüm** | 1) Registry süreç-içi ≠ discovery + 6× `@see ADR-084` dosya yok → düz etiket + ADR-084 düz metin/V.R. → **Şart 1a** · 2) Servis sayımı 4 çelişki → SSOT: Config tablosu = sayım kaynağı (11) → **Şart 1b** · 3) PLANNED katman kod 0 → faz yol haritası (Node → C++ → Python, çıkış ölçütü ile) → **Şart 2** · 4) Test yok → servis sınırı + outbox idempotent test paketi → **Şart 3** |
| **Tur 3 — Oy** | **18 kabul / 2 çekimser / 0 red → KABUL** |
| **Şartlar (bağlayıcı)** | **Şart 1:** registry etiketi + sayım SSOT (1a-1b) · **Şart 2:** PLANNED faz yol haritası · **Şart 3:** sınır + idempotent test paketi → uygulama §5.1 adımlar 11-14 |
| **Karar kapsamı kaynağı** | Kullanıcı onaylı: mevcut 11 servis + PLANNED katmanlar + servis sınırları + iletişim (ADR-032 IPC + ADR-086 event/outbox) + paylaşımlı `shared/` katmanı |
| **Bekleyenler** | Arch Lead onayı · §5.1 adımları 4-14 (3 debate şartı dahil) · R5 (ağ keşfi) kararı; `ADR-084`/`ADR-086` dosyalarının yazılması |

**Statü özeti:** `status: accepted` (kullanıcı onaylı kapsam) · debate **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** · **Tech Lead ✅** · **Arch Lead ⏳** · **frozen YOK** (ADR-001-037 dokunulmaz; bu dosya Active aralığı) · **3 bağlayıcı şart** (§5.1 adımlar 11-14) · uygulama adımlarının **11/14'ü PLANNED** (kod 0 kanıtı §1.1-C) · wiki-link **25/25 diskte**, 6 referans diskte olmadığı için düz metin + `⚠️ VERIFICATION REQUIRED`.

---

*ADR-039 v1.0.0 — CoreMusic Architecture Decision Record*
*Authority: ADR-039 Karar Metni (SSOT)*
*Last Updated: 2026-09-26*
*Mode: Red Team · Human Mode · Truth Mode*
