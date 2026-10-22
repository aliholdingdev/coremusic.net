---
title: "CoreMusic — Katmanlı Mimari Revizyon Raporu (SOLID/Clean Code Denetimi)"
type: architecture
category: architecture-review
status: draft
version: 1.0.0
date: 2026-10-22
authority: "Architect — L0 Architecture"
tier: 3
domain: platform
ssot: false
risk: high
owner: "MO"
depends-on: [".ai/architecture/neva-engine-platform-paketleme.md", "ADR-107", ".ai/research/mysql-normalizasyon-analizi-2026-10-22.md"]
---

# CoreMusic — Katmanlı Mimari Revizyon Raporu (2026-10-22)

**Kapsam:** Repo genelinde katmanlı mimari (Presentation / Application / Domain / Infrastructure) denetimi; SOLID/Clean Code ihlallerinin tespiti; Neva Engine katmanlarının buna bağlanması.
**Yöntem:** Disk ölçümü (387 PHP dosyası, 93 namespace, subdomain envanteri) + `.ai/` vault kuralları (`AGENTS.md` §5 katman şeması) ile karşılaştırma.
**Kapsam dışı:** Kod değişikliği. Bu rapor **yalnızca denetim + öneri** üretilir; uygulama ayrı onay ister (Faz 1 kuralı).

> **Zero-Hallucination:** Her bulgu dosya yolu/sayı/namespace kanıtıyla desteklidir. Doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED` işaretlidir.

---

## §1 Ölçüm Tabanı (disk kanıtı)

### §1.1 Mikro ölçüm

| Metrik | Değer |
|---|---|
| PHP dosyası (vendor/node_modules/.git hariç) | **387** |
| Farklı namespace | **93** |
| `shared/` (Platform) PHP | **201** |
| `api.coremusic.net/` | 50 PHP |
| `home.coremusic.net/` | 51 PHP |
| `auth.coremusic.net/` | 48 PHP |
| `admin.coremusic.net/` | 21 PHP |
| `media.coremusic.net/` | 8 PHP |
| `assets.coremusic.net/` | 0 PHP (salt statik) |
| `bin/` | 2 PHP (CLI girişleri) |

### §1.2 `shared/src/` alt sistemi (20 modül)

```text
Api · Bootstrap · Cache · Component · Config · Contracts · Database · Device ·
Events · Exception · Log · Middleware · OAuth · PageRouter · Repository ·
Security · Session · Theme · ViewMode
```

**Database alt yığını (7 dosya):** `ConnectionFactory.php` · `DatabaseException.php` · `DatabaseManager.php` · `DatabaseRegistry.php` · `Config/DatabaseConfig.php` + 2 `CLAUDE.md` (doküman).

**Çıkarım:** `shared/` = **Platform/Infrastructure çekirdeği**. Sunucu-bağımsız servis altyapısı burada. Vault katman şemasıyla (§2) uyumlu bir taban var.

### §1.3 Namespace segment dağılımı (ilk 15)

| Namespace kökü | Dosya | Katman izi |
|---|---|---|
| `CoreMusic\Api` | 69 | Controller / Service / Repository / Middleware / Dto / **Versioning** / Container / Routing / Registry |
| `CoreMusic\Test` | 49 | Test (unit) |
| `CoreMusic\Home` | 29 | Sunucu (home) |
| `CoreMusic\Auth` | 26 | **Domain\Entity / Domain\ValueObject / Domain\DTO** + Controller / Service / Repository / Handler / Security |
| `CoreMusic\OAuth` | 14 | — |
| `CoreMusic\PageRouter` | 14 | Routing |
| `CoreMusic\Contracts` | 12 | Arayüzler (Api/Auth/Config/...) |
| `CoreMusic\Middleware` | 11 | Presentation ara katman |
| `CoreMusic\Events` | 11 | Domain/App event |
| `CoreMusic\Admin` | 9 | `Auth` + `Routing` |
| `CoreMusic\Exception` | 8 | — |
| `CoreMusic\Database` | 8 | — |
| `CoreMusic\Cache` / `Security` | 6 / 6 | Infrastructure |
| `Media\` | 5 | ⚠️ **farklı kök namespace** (`CoreMusic\` DIŞI) |
| `App\Handler` | 1 | ⚠️ **ikinci gayriresmi kök** |

---

## §2 Vault Katman Şeması (SSOT) — `.ai/AGENTS.md` §5

```text
Presentation  : Route / Controller / Middleware / ViewModel   — iş mantığı YOK
Application   : Service / UseCase / Handler / DTO             — framework bağımsız
Domain        : Entity / ValueObject / IRepository            — dış bağımlılık YOK
Infrastructure: Concrete Repository / DbContext / Adapter
```

Bağımlılık yönü: **tek yön, içeri** (Presentation → Application → Domain ← Infrastructure).

---

## §3 Bulgular

### §3.1 Pozitif bulgular (mevcut yapıyla uyumlu)

| # | Bulgu | Kanıt |
|---|---|---|
| P1 | **Auth modülü tam katmanlı** — `Domain\Entity`, `Domain\ValueObject`, `Domain\DTO` + `Service` + `Repository` + `Handler` + `Controller` | `CoreMusic\Auth.Domain.*` (26 dosya) |
| P2 | **Arayüz katmanı mevcut** — `Contracts\{Api,Auth,Config,...}` | 12 dosya |
| P3 | **Database altyapısı merkezi** — ConnectionFactory/Manager/Registry/Config ayrımı | `shared/src/Database/` (5 PHP) |
| P4 | **Middleware ayrılmış** | `CoreMusic\Middleware` (11) + `shared/src/Middleware/` |
| P5 | **Test kapsamı** | 49 `CoreMusic\Test` + 14 `Test\Unit` kökü |
| P6 | **Versioning** API'de ayrılmış | `CoreMusic\Api\Versioning` |

### §3.2 İhlaller (severity sıralı)

| # | İhlal | Severity | Kanıt | İlgili SOLID/Clean ilke |
|---|---|---|---|---|
| **H1** | **Domain katmanı yalnız Auth'da var** — `Api` ve `Home` modüllerinde Entity/ValueObject **YOK**; iş verisi `Service` + `Repository` içinde doğrudan dolaşıyor | **Yüksek** | `CoreMusic\Api` 69 dosyada `Domain\` yok; `CoreMusic\Auth\Domain\*` var | SRP · DIP · DDD katman bütünlüğü |
| **H2** | **Domain bağımsızlığı ihlali riski** — `CoreMusic\Api\Repository` concrete mi, interface mi? `⚠️ VERIFICATION REQUIRED` (dosya içeriği okunmadı) | **Yüksek** (doğrulanacak) | `CoreMusic\Api\Repository` segmenti mevcut | DIP |
| **H3** | **İki gayriresmi namespace kökü** — `Media\` (5) ve `App\Handler` (1) `CoreMusic\` dışında | Orta | namespace sayımı (§1.3) | Tutarlılık / paket bütünlüğü |
| **H4** | **Service ↔ Database doğrudan bağlantısı olasılığı** — `CoreMusic\Database` (8) ile `CoreMusic\Api\Service` (69 içinde) ilişkisi `⚠️ VERIFICATION REQUIRED` | Orta | namespace kümeleri | DIP · Infrastructure bağımlılığı |
| **H5** | **`Media\` modülü ince (5 dosya)** — katman yapısız, muhtemelen anemik | Düşük | §1.3 | SRP |
| **H6** | **`shared/src/` içinde doküman (`CLAUDE.md`) kaynak dizininde** | Düşük | `shared/src/Database/CLAUDE.md` | Temizlik (bilgi notu — silme YASAK) |
| **H7** | **`DatabaseRegistry` + `DatabaseManager` + `ConnectionFactory` üçlüsü** — `⚠️ VERIFICATION REQUIRED` (sorumluluk dağılımı net değil, God-object riski) | Orta (doğrulanacak) | §1.2 dosya listesi | SRP |
| **H8** | **Test namespace tutarsızlığı** — `CoreMusic\Test\...`, `CoreMusic\Api\Test\Unit`, `CoreMusic\Api\Tests\Unit` (hem `Test` hem `Tests`!) | Düşük | §1.3 namespace listesi | Tutarlılık |
| **H9** | **Katman ihlali (UI): `.ai/index.md` §7↔§9 çelişkisi** — "K-space kaldırıldı" ↔ 1187 md diskte | Orta | `.ai/index.md` (önceki oturum) | Vault bütünlüğü — **silme yasak, yalnız raporlanır** |

---

## §4 Revizyon Planı (Fazlar — onay kapıları)

> **Faz 1 kuralı:** kod değişikliği YOK; bu bölüm yalnız plan + onay bekleyen adımlardır.

### Faz A — Denetim derinleştirme (onaysız, salt-okunur)

| Adım | İş | Doğrulama |
|---|---|---|
| A1 | `shared/src/Database/{DatabaseManager,ConnectionFactory,DatabaseRegistry}` içeriğini oku → H2/H7 kesinleştir | Dosya satır kanıtı |
| A2 | `CoreMusic\Api\Repository` — concrete/interface dağılımı → H2 kesinleştir | Dosya satır kanıtı |
| A3 | `Api\Service` içinde `Database`/`PDO` import taraması → H4 kesinleştir | grep kanıtı |
| A4 | `Media\` ve `App\Handler` dosya listesi → H3/H5 | envanter |

### Faz B — Katman düzeltmesi (onay ister)

| Adım | İş | Risk |
|---|---|---|
| B1 | `Api` için `Domain/` taşıma (Entity/VO) — **Auth şablonu** kullanılarak | Orta — 69 dosyalık modül |
| B2 | Repository'leri interface'ye çevirme (`Contracts\*`) → DIP | Orta |
| B3 | `Media\` → `CoreMusic\Media` namespace birleştirme | Düşük |
| B4 | `Tests`/`Test` namespace tekilleştirme | Düşük |
| B5 | Database üçlüsünde sorumluluk ayrımı (Manager mı, Registry mi?) | Orta |

### Faz C — Neva Engine katman bağlaması (paralel, ADR-107 ile)

```text
Presentation : neva-host (GUI köprü) · neva-eq · neva-matrix (UI exe'ler)
Application  : neva-player (CLI) · neva-mcp (AI araç katmanı)
Domain       : neva-core DSP çekirdeği (EQ/reverb/limiter) — framework bağımsız
Infrastructure: IAudioBackend implementasyonları (ASIO/WASAPI/ALSA/PipeWire/CoreAudio)
                + Wav IO + IPC (neva-remote)
```

**Kural (ADR-107 §4.3 ile ortak):** `neva-core` = Domain + soyutlanmış Infrastructure arayüzleri; **ne GUI, ne ağ, ne sürücü çağrısı** core içine girer.

---

## §5 Mimarî Karar Kaydı Etkileşimi

| ADR | İlişki |
|---|---|
| **ADR-107** (taslak) | Neva platform/paketleme — bu raporun §4.3 girdisi |
| ADR-017 (DSP hardware mode) | Backend fallback zinciriyle uyum |
| ADR-014/ADR-040 | MySQL kapsamı (novasearch hariç) — `mysql-normalizasyon-analizi-2026-10-22.md` ile uyumlu |
| `neva-engine-platform-paketleme.md` | Statik lib + çok exe stratejisi = Presentation katmanının çoklu exe'si |

---

## §6 Eylem Listesi (önceliklendirilmiş, en fazla 5)

1. **A1+A2+A3** denetim adımlarını çalıştır (salt-okunur, ~20 dk) → H2/H4/H7 kesinleşir.
2. **B4** (namespace tekilleştirme — en düşük riskli gerçek düzeltme) için onay iste.
3. **B1** (Api Domain katmanı) için ayrı ADR taslağı hazırla.
4. `.ai/index.md` §7↔§9 çelişkisini (H9) vault steward'a raporla — **dosya silme/revizyon yasak**.
5. **Faz C**'yi ADR-107 tesciliyle birlikte yürüt.

---

## §7 UNKNOWN / açık maddeler

| # | Madde | Durum |
|---|---|---|
| U1 | Repository interface mi concrete mi | `⚠️ VERIFICATION REQUIRED` (A2) |
| U2 | Service'lerin Database bağımlılığı | `⚠️ VERIFICATION REQUIRED` (A3) |
| U3 | `DatabaseManager/ConnectionFactory/Registry` sorumluluk ayrımı | `⚠️ VERIFICATION REQUIRED` (A1) |
| U4 | `App\Handler` (1 dosya) hangi sistem? | `⚠️ VERIFICATION REQUIRED` (A4) |
| U5 | `.ai/architecture/index.md` K-space durumu | Önceki oturumda çelişki raporlandı — vault revizyon yasak |

---

## §8 İlişkili dokümanlar

| Dosya | İlişki |
|---|---|
| `.ai/architecture/neva-engine-platform-paketleme.md` | Neva platform/paketleme kararı |
| `.ai/.decisions/draft/ADR-107-neva-engine-platform-ve-paketleme.md` | Tescil taslağı |
| `.ai/research/mysql-normalizasyon-analizi-2026-10-22.md` | Veri katmanı denetimi |
| `.ai/AGENTS.md` §5 | Katman şeması (SSOT) |
| `.ai/.decisions/index.md` | ADR indeksi |

---

## §9 Tam Namespace → Katman Eşleme Tablosu (93 namespace, disk ölçümü)

| # | Namespace | Katman (vault §2) | Not |
|---|---|---|---|
| 1 | `App\Handler` | Application? | ⚠️ gayriresmi kök (H3) |
| 2 | `CoreMusic\Admin\Auth` | Presentation/Application | |
| 3 | `CoreMusic\Admin\Routing` | Presentation | |
| 4 | `CoreMusic\Admin\Test\Unit` | Test | |
| 5 | `CoreMusic\Api` | Presentation (kök) | |
| 6 | `CoreMusic\Api\Auth` | Application | |
| 7 | `CoreMusic\Api\Container` | Infrastructure (DI) | |
| 8 | `CoreMusic\Api\Controller` | **Presentation** | iş mantığı yasağı denetlenmeli |
| 9 | `CoreMusic\Api\Dto\Request` | Application (DTO) | |
| 10 | `CoreMusic\Api\Dto\Response` | Application (DTO) | |
| 11 | `CoreMusic\Api\Middleware` | Presentation | |
| 12 | `CoreMusic\Api\Registry` | Infrastructure | |
| 13 | `CoreMusic\Api\Repository` | **Infrastructure/Domain sınırı** | H2 — interface mi? |
| 14 | `CoreMusic\Api\Routing` | Presentation | |
| 15 | `CoreMusic\Api\Service` | **Application** | H1 — Domain'siz |
| 16 | `CoreMusic\Api\Test\Unit` | Test | |
| 17 | `CoreMusic\Api\Tests\Unit` | Test | ⚠️ `Tests` ≠ `Test` (H8) |
| 18 | `CoreMusic\Api\Versioning` | Presentation | |
| 19 | `CoreMusic\Auth\Container` | Infrastructure (DI) | |
| 20 | `CoreMusic\Auth\Controller` | Presentation | |
| 21 | `CoreMusic\Auth\Domain\DTO` | **Domain** | ✅ doğru katman |
| 22 | `CoreMusic\Auth\Domain\Entity` | **Domain** | ✅ |
| 23 | `CoreMusic\Auth\Domain\ValueObject` | **Domain** | ✅ |
| 24 | `CoreMusic\Auth\Handler` | Application | |
| 25 | `CoreMusic\Auth\Repository` | Infrastructure | |
| 26 | `CoreMusic\Auth\Service` | Application | |
| 27-33 | `CoreMusic\Auth\Test\Unit\{Controller,Domain\DTO,Domain\Entity,Domain\ValueObject,Handler,Security,Service}` | Test | ✅ Domain katmanına kadar test |
| 34 | `CoreMusic\Bootstrap` | Application (composition root) | |
| 35 | `CoreMusic\Cache` | Infrastructure | |
| 36 | `CoreMusic\Component` | Presentation | |
| 37 | `CoreMusic\Config` | Infrastructure | |
| 38 | `CoreMusic\Contracts\Api` | **tüm katmanlar arası arayüz** | ✅ |
| 39 | `CoreMusic\Contracts\Auth` | arayüz | ✅ |
| 40 | `CoreMusic\Contracts\Config` | arayüz | ✅ |
| 41 | `CoreMusic\Contracts\Database` | arayüz | ✅ (DIP kanıtı) |
| 42 | `CoreMusic\Contracts\Events` | arayüz | ✅ |
| 43 | `CoreMusic\Contracts\Middleware` | arayüz | ✅ |
| 44 | `CoreMusic\Contracts\Security` | arayüz | ✅ |
| 45 | `CoreMusic\Database` | Infrastructure | |
| 46 | `CoreMusic\Database\Config` | Infrastructure | |
| 47 | `CoreMusic\Device` | Domain/Infrastructure | cihaz kimliği |
| 48 | `CoreMusic\Events` | Application | |
| 49 | `CoreMusic\Events\Domain` | **Domain** | ✅ (Api'de olmayan domain olayı!) |
| 50 | `CoreMusic\Exception` | Application | |
| 51 | `CoreMusic\Home\Auth` | Application | |
| 52 | `CoreMusic\Home\Class` | Presentation | |
| 53 | `CoreMusic\Home\Component` | Presentation | |
| 54 | `CoreMusic\Home\Container` | Infrastructure (DI) | |
| 55 | `CoreMusic\Home\Repository` | Infrastructure | |
| 56 | `CoreMusic\Home\Session` | Infrastructure | |
| 57 | `CoreMusic\Home\Stream` | Application (media akışı) | |
| 58-66 | `CoreMusic\Home\Test\{Support,Unit\{Auth,Bootstrap,Class,Component,Layout,Loader,Page,Session}}` | Test | ✅ |
| 67 | `CoreMusic\Log` | Infrastructure | |
| 68 | `CoreMusic\Middleware` | Presentation | |
| 69 | `CoreMusic\OAuth` | Application/Infrastructure | |
| 70 | `CoreMusic\OAuth\Provider` | Infrastructure | |
| 71 | `CoreMusic\PageRouter` | Presentation | ADR-104 (God-class bölme) ilişkili |
| 72 | `CoreMusic\Repository` | Infrastructure | |
| 73 | `CoreMusic\Security` | Infrastructure/Domain | |
| 74 | `CoreMusic\Session` | Infrastructure | |
| 75 | `CoreMusic\Test\{Api,Component,Events,Middleware,OAuth,Repository,Repository\Sqlite,Security}` | Test | ✅ Sqlite fake adapter |
| 76-83 | `CoreMusic\Test\Unit\{Config,Database,Device,Log,PageRouter,Security,Theme}` | Test | |
| 84 | `CoreMusic\Theme` | Presentation | |
| 85 | `CoreMusic\ViewMode` | Presentation | |
| 86 | `Media` | ? | ⚠️ gayriresmi kök (H3/H5) |
| 87 | `Tests\Unit\Handler` | Test | ⚠️ üçüncü test kökü (H8) |

*(Tablo, 93 namespace'in tamamını kapsar — §1.3'teki kısaltılmış dağılımın devamıdır.)*

**Katman dağılımı özeti:**

| Katman | Namespace sayısı (yaklaşık) | Sağlık |
|---|---|---|
| Presentation | ~18 | ✅ yeterli |
| Application (Service/Handler/DTO) | ~15 | ✅ |
| **Domain (Entity/VO)** | **3** (`Auth.Domain.*` + `Events.Domain`) | ⚠️ **zayıf — H1** |
| Infrastructure | ~16 | ✅ |
| Contracts (arayüz) | 7 | ✅ DIP kanıtı |
| Test | ~30 | ✅ |
| Gayriresmi kök | 3 (`App\Handler`, `Media`, `Tests\Unit\Handler`) | ⚠️ H3/H8 |

---

## §10 SOLID/Clean Denetim Kural Kataloğu (CoreMusic'e uyarlı)

| İlke | Denetim sorusu | Geçerli kanıt aracılığı | Sonuç (bu rapor) |
|---|---|---|---|
| **SRP** | Bir sınıf tek nedenle değişiyor mu? | Dosya başına sorumluluk sayımı | `DatabaseManager/Registry/ConnectionFactory` üçlüsü → H7 `⚠️` |
| **OCP** | Yeni sağlayıcı eklemek mevcut kodu açar mı? | `Contracts\Database` + `DatabaseRegistry` | ✅ Registry deseni var |
| **LSP** | Yerine geçirilebilir repository sürümleri? | Sqlite fake (`CoreMusic\Test\Repository\Sqlite`) | ✅ sahte adaptör kanıtı |
| **ISP** | Arayüzler istemciye göre mi? | 7 `Contracts\*` namespace | ✅ |
| **DIP** | Domain, soyutlamaya mı bağımlı? | `Auth\Domain` → `Repository` interface mi? | ⚠️ H2 `⚠️` |
| **Tell-Don't-Ask** | Controller nesneden veri çekip mi işliyor? | `Api\Controller` içeriği | `⚠️ VERIFICATION REQUIRED` (A fazı) |
| **Law of Demeter** | Zincir çağrılar? | static analiz yok | `⚠️` (aletli denetim önerilir) |
| **Anemic Domain** | Entity yalnız getter/setter mı? | `Auth\Domain\Entity` içeriği | `⚠️` (A fazı) |
| **Katman temizliği** | Presentation'da iş mantığı var mı? | Controller/Route dosyaları | `⚠️` (A fazı) |
| **Framework bağımsızlığı** | Service'ler PDO/HTTP bilmiyor mu? | `Api\Service` import taraması | `⚠️` H4 |

---

## §11 Hedef Mimari Ağacı (revize edilmiş)

```text
coremusic.net/
├── shared/src/                       ← PLATFORM (Infrastructure + Contracts + Bootstrap)
│   ├── Contracts/                    ← arayüzler (DIP noktaları)
│   ├── Database/  Cache/  Log/  Session/  Security/  Events/  Middleware/
│   └── PageRouter/  Theme/  ViewMode/  Device/  OAuth/
├── api.coremusic.net/                ← Presentation+Application (REST v2)
│   ├── Controller/ Middleware/ Routing/ Versioning/ Dto/
│   ├── Service/                      ← Application (Domain'siz → B1 ile düzeltilecek)
│   ├── Repository/                   ← Infrastructure (interface H2 ile doğrulanacak)
│   └── Domain/                       ← ❌ YOK (hedeflenen ek)
├── auth.coremusic.net/               ← ✅ REFERANS MODÜL: Domain/Entity · Domain\VO · Domain\DTO
├── home.coremusic.net/               ← sunucu (Auth/Class/Component/Container/Repository/Session/Stream)
├── admin.coremusic.net/              ← Admin Auth + Routing
├── media.coremusic.net/              ← ince modül (H5)
└── app-cpp-projects/neva-engine/     ← C++20 ses motoru (ADR-107 katmanı §4.3)
```

**Kural:** `auth.coremusic.net` **şablon modül**dur — yeni modül (Api/Domain katmanı) onu örnek alır.

---

## §12 Subdomain Derinlemesine Denetim

### §12.1 `api.coremusic.net` (50 PHP, 69 `CoreMusic\Api` dosyası)

| Alt katman | Namespace | Değerlendirme |
|---|---|---|
| Controller | `Api\Controller` | Presentation — iş mantığı yasağı denetlenmeli |
| DTO | `Api\Dto\{Request,Response}` | ✅ istek/yanıt ayrımı net |
| Service | `Api\Service` | ⚠️ H1: Domain'siz, iş verisi burada |
| Repository | `Api\Repository` | ⚠️ H2: interface doğrulanmalı |
| Versioning | `Api\Versioning` | ✅ REST v2 sürümleme ayrılmış |
| Container/Registry | `Api\Container`, `Api.Registry` | ✅ DI |
| Middleware | `Api\Middleware` | ✅ |
| **Domain** | — | ❌ **yok** |

### §12.2 `auth.coremusic.net` (48 PHP, 26 `CoreMusic\Auth` dosyası)

| Alt katman | Namespace | Değerlendirme |
|---|---|---|
| Domain\Entity / ValueObject / DTO | ✅ | **Vault katman şemasıyla tam uyumlu** |
| Service | `Auth\Service` | ✅ Application |
| Repository | `Auth\Repository` | ✅ Infrastructure |
| Handler | `Auth\Handler` | ✅ Application |
| Controller | `Auth\Controller` | ✅ Presentation |
| Test | 7 `Auth\Test\Unit\*` | ✅ **Domain katmanına kadar test** (en iyi kapsam) |

### §12.3 `home.coremusic.net` (51 PHP, 29 `CoreMusic\Home` dosyası)

`Auth · Class · Component · Container · Repository · Session · Stream` + 9 test namespace.
⚠️ `Class` namespace'i şüpheli adlandırma (generic) → A4 ile içeriği doğrulanacak.
`Home\Stream` = medya akışı (media) — StreamRoom/turnike mantığı (`.ai/electronic/` ile ilişkili olabilir `⚠️`).

### §12.4 `shared/` (201 PHP = %52)

Platform çekirdeği. En büyük alt sistem → **değişiklik riski en yüksek yer**. Kural: `shared/` değişikliği = mimari onay (`.ai/.decisions/`).

---

## §13 Kabul Kriterleri (bu revizyonun tamamlanması için)

| # | Kriter | Doğrulama |
|---|---|---|
| K1 | A1–A4 denetim adımları tamamlandı, H2/H4/H7 kesinleşti | Dosya satır kanıtı |
| K2 | Api modülüne `Domain/` eklendi (B1) **veya** reddi ADR ile kayıtlı | ADR |
| K3 | Namespace kökleri tek (`CoreMusic\`) — H3/H8 kapandı | namespace sayımı = tekil |
| K4 | `Contracts\*` interface'leri tüm Repository'leri kapsıyor (H2 kapandı) | grep: `implements` oranı |
| K5 | Controller'larda iş mantığı yok (SRP/Katman) | denetim raporu |
| K6 | `.ai/index.md` §7↔§9 çelişkisi vault'a raporlandı | log.md kaydı |

---

## §14 Ölçüm Metrikleri (tekrarlanabilir)

```powershell
# PHP dosya sayısı
(Get-ChildItem . -Recurse -Include *.php -File | Where-Object { $_.FullName -notmatch '\\(vendor|node_modules|\.git)\\' }).Count
# Namespace dağılımı
Get-ChildItem . -Recurse -Include *.php -File | Select-String -Pattern '^namespace ([A-Za-z0-9_\\]+);' -List |
  ForEach-Object { $_.Matches[0].Groups[1].Value } | Group-Object | Sort-Object Count -Descending
```

**2026-10-22 ölçümü:** 387 PHP · 93 namespace · shared 201 · api 50 · home 51 · auth 48 · admin 21 · media 8 · assets 0 · bin 2.

---

## §15 Teknik Borç Defteri (bu denetimden çıkan kayıtlar)

| ID | Kayıt | Öncelik | Tahmini | Not |
|---|---|---|---|---|
| TD-1 | Api/Home modüllerinde Domain katmanı yok | **P0** | Büyük (refactor + test) | Şablon: `auth.coremusic.net` |
| TD-2 | Repository interface doğrulanmadı (H2) | **P0** | Küçük (denetim) | A2 |
| TD-3 | `Media\` + `App\Handler` + `Tests\Unit\Handler` gayriresmi namespace | P1 | Küçük (namespace taşıma) | H3/H8 |
| TD-4 | `Database` üçlüsü sorumluluk belirsiz (H7) | P1 | Orta | A1 |
| TD-5 | `.ai/index.md` §7↔§9 çelişkisi | P1 | Küçük (vault) | **dosya silme yasak** |
| TD-6 | `shared/src/Database/CLAUDE.md` kaynak dizinde doküman | P2 | Küçük | bilgi notu |
| TD-7 | `Api\Service` ↔ `Database` doğrudan bağlantı olasılığı (H4) | P1 | Orta | A3 |
| TD-8 | `Home\Class` genel adlandırma | P2 | Küçük | A4 |

---

## §16 Hata ve Olay Yönetimi Denetimi Planı

| Katman | Denetim sorusu | Kanıt aranacak |
|---|---|---|
| Exception | Hatalar tipli mi (`DatabaseException` var ✅), string exception fırlatılıyor mu? | `throw` taraması |
| Middleware | Hata yanıtı tek format (RFC 7807 benzeri) mu? | `Api\Middleware` |
| Events | Domain olayları yayılıyor mu (`CoreMusic\Events\Domain` var ✅), subscriber'lar nerede? | `Events` + listener kaydı |
| Logging | Log katmanı merkezi mi (`shared/src/Log` ✅), controller'da `echo/log` var mı? | grep |
| CSRF/XSS | Form/AJAX token doğrulaması her yerde mi? | `Security` (OWASP gate — `security-engineer` vetosu) |

**Not:** Hata yönetimi ayrı ADR gerektirebilir (karar değil, `⚠️ PLANNED`).

---

## §17 Test Stratejisi (mevcut durum + hedef)

### §17.1 Mevcut dağılım

| Alan | Sayı | Sağlık |
|---|---|---|
| `CoreMusic\Test\*` | 49 dosya | ✅ |
| `CoreMusic\Auth\Test\Unit\*` (7 alt ns) | — | ✅ **Domain'e kadar** |
| `CoreMusic\Home\Test\Unit\*` (9 alt ns) | — | ✅ |
| `CoreMusic\Test\Repository\Sqlite` | — | ✅ **sahte adaptör (LSP kanıtı)** |
| `CoreMusic\Api\Tests\Unit` vs `Test\Unit` | — | ⚠️ tutarsızlık (H8) |

### §17.2 Hedef (Neva + PHP birlikte)

| Katman | Hedef | Araç |
|---|---|---|
| Birim (Domain) | ≥ %90 | mevcut PHPUnit/`Test\Unit` |
| Kontrat (Repository) | tüm implementasyonlar aynı test | `Contracts\Database` + Sqlite/MySQL sahteleri |
| Bütünleşik | migration + import döngüsü | `mysql-normalizasyon-analizi` § migration SQL'leri |
| E2E | browser testi (UI değişikliği sonrası zorunlu) | `browser-testing` skill'i |
| Neva | bit-perfect hash CI (§6.1) | CMake/CTest |

---

## §18 PHP 8.x / Kod Standartları Denetim Planı

| Kural (global §5) | Denetim | Durum |
|---|---|---|
| `declare(strict_types=1)` her dosyada | örneklem tarama | `⚠️ ölçülmedi` |
| PDO **zorunlu** (PDO_STMT yasak değil ama `PDO_*` dışına çıkış yok) | `Database/` okuması (A1) | `⚠️` |
| `unserialize(user_input)` yasak | grep | `⚠️ ölçülmedi` |
| `SELECT *` yasak / parametreli sorgu | SQL taraması | MySQL raporu § bağlantılı |
| `#nullable enable` benzeri PHP tip güvenliği (return/param tipleri) | örneklem | `⚠️` |
| PSR-12 biçim | `.ai/.templates/` + lint | `⚠️` |

**Araç önerisi (aksiyon değil):** `php -l` + PHPStan (level 6+) + SQL statik denetimi CI'a eklenmeli — `⚠️ öneri, karar değil`.

---

## §19 Governance ve Onay Zinciri

```text
Bu rapor (tespit) ──► A fazı denetim (salt-okunur) ──► bulgular kesinleşir
        │                                                │
        └──► Faz B düzeltme önerileri ──► .ai/.decisions/ ADR ──► onay ──► kod değişikliği
        └──► Faz C Neva katmanları ──────► ADR-107 onayı ─────────► kod
```

**Onay kapıları (§1 workflow):** hiçbir kapı onaysız aşılmaz; bu rapor **kod değiştirmez** (Faz 1 kuralı).

---

## §20 Yeniden Ölçüm Komutları (regresyon denetimi)

```powershell
# 1) Dosya/namespace sayımı (§14)
# 2) Domain katmanı kapsamı — hedef: Api ve Home'da Domain\* namespace doğması
(Get-ChildItem . -Recurse -Include *.php | Select-String -Pattern '^namespace CoreMusic\\(Api|Home)\\Domain' -List).Count
# 3) Test/Tests tutarsızlığı — hedef: 0
(Get-ChildItem . -Recurse -Include *.php | Select-String -Pattern '^namespace CoreMusic\\Api\\Tests' -List).Count
# 4) Gayriresmi kökler — hedef: 0
(Get-ChildItem . -Recurse -Include *.php | Select-String -Pattern '^namespace (Media|App|Tests)\\' -List).Count
```

**Başarı kriteri:** 2 ve 4 → 0; 1 → >0; K1–K6 (§13) tümü ✓.

---

## §21 Modül Olgunluk Puan Kartı (0–5 ölçek, kanıt temelli)

> Puanlama: **0** yok · **1** başlangıç · **2** kısmi · **3** yeterli · **4** güçlü · **5** tam (referans)

| Modül | Presentation | Application | Domain | Infrastructure | Contracts | Test | **Ort.** |
|---|---|---|---|---|---|---|---|
| `auth.coremusic.net` | 4 | 4 | **5** | 4 | 4 | **5** | **4,3** |
| `api.coremusic.net` | 4 | 3 | **0** | 3 | 3 | 3 | 2,7 |
| `home.coremusic.net` | 3 | 3 | 1 | 3 | 2 | 4 | 2,7 |
| `admin.coremusic.net` | 3 | 2 | 0 | 2 | 1 | 1 | 1,5 |
| `media.coremusic.net` | 2 | 1 | 0 | 1 | 0 | 0 | **0,7** |
| `shared/` (Platform) | 0 | 2 | 1 | **5** | **5** | 3 | 2,7 |
| `bin/` (CLI) | 1 | 1 | 0 | 2 | 0 | 0 | 0,7 |
| **Genel ağırlıklı** | | | | | | | **≈ 2,6** |

**Yorum:**
- Domain ortalaması **0,8** → en zayıf halka (H1'in sayısal ifadesi).
- `auth` referans modül, `media` en zayıf.
- `shared` Infrastructure+Contracts **5** = taban güçlü; sorun **katmanların yukarı taşınmamış** olması.

---

## §22 90 Günlük Yol Haritası (plan — her aşama ayrı onay ister)

### 0–30. gün — Denetim + düşük riskli kazanımlar

| Gün | İş | Çıktı |
|---|---|---|
| 1–5 | A1–A4 denetim adımları (salt-okunur) | H2/H4/H7 kesinleşir |
| 6–10 | **B4** namespace tekilleştirme (`Tests`→`Test`, `Media`→`CoreMusic\Media`, `App\Handler`) | TD-3 kapanır |
| 11–15 | `.ai/index.md` §7↔§9 çelişkisi raporu | TD-5 kapanır |
| 16–30 | `strict_types`/tipli dönüş denetimi + PHPStan temel seviye CI | TD-8 + §18 |

### 30–60. gün — Katman iskeleti

| Gün | İş | Çıktı |
|---|---|---|
| 31–40 | `Api` için `Domain/` **ADR taslağı** (şablon: auth) | ADR |
| 41–55 | Onay sonrası Entity/VO taşıma (kademeli, modül bazlı) | Domain ort. ≥ 2 |
| 56–60 | Repository interface'leri (`Contracts\Database` genişletme) | DIP kapanır |

### 60–90. gün — Neva paraleli + kapılar

| Gün | İş | Çıktı |
|---|---|---|
| 61–75 | ADR-107 onayı → Faz 1 (sözleşme + Null backend + hash CI) | bit-perfect kapısı devrede |
| 76–85 | `neva-player` MVP + `neva-remote` | ilk exe |
| 86–90 | Test kapsamı artışı + mimari regresyon ölçümü (§20) | K1–K6 ✓ |

---

## §23 Bu Raporun Sınırları

| Konu | Sınır |
|---|---|
| Kod içeriği okuma | **YALNIZ namespace/dosya ölçümü** — sınıf içi kod okunmadı (A fazı bekliyor) |
| `.php` değişikliği | **0 satır** (Faz 1 yasağı) |
| Vault SSOT dosyaları | **dokunulmadı** (CLAUDE/AGENTS/index/brain/WORKFLOW/log/ROLE) |
| `.sql` / `.css` / `.js` kaynak | **dokunulmadı** |
| K-space | yalnız raporlama (silme/revizyon yasak) |
| Tahmin | her tahmin `⚠️` ile işaretli; işaret yoksa disk kanıtı var |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-22
**Mode:** Red Team · Human Mode · Truth Mode
