---
title: "CoreMusic — Karar Kuralları (Onaylı Kararların Kalıcı Kural Türevleri)"
type: rule
category: decision-rules
version: 1.3.0
status: active
authority: "SSOT — karar→kural türevi (companion: .ai/.rules/senior-mode.md)"
updated: 2026-11-01
# Control Plane v2 (2026-10-07):
tier: 2
domain: rules
ssot: true
risk: medium
owner: "Vault Steward"
depends-on: [".ai/.rules/senior-mode.md", ".ai/.rules/kurallar.md"]
---

# KARAR KURALLARI

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[.ai/.rules/senior-mode.md]] · [[.ai/.rules/kurallar.md]] · [[log.md]] · ADR arşivi: `.ai/.decisions/index.md`

---

## 1. Amaç

Kullanıcı onayıyla kilitlenen proje kararlarının, kod / DB / süreç tarafına yansıyan **kalıcı kuralları**. Her § başlığı bir karara bağlıdır (ADR numarası referanslı); ADR metni detay SSOT'tur, bu dosya uygulanabilir kural özetidir. Çelişkide ADR metni kazanır.

Okuma kuralı: Bu dosya `senior-mode.md`'e partnerdir; kod öncesi 16 adım analizinde ilgili başlık göreve değiyorsa okunur (boot'ta toplu okuma yok).

---

## §1 — DB Şema Sözleşme Hizalama Kuralı (ADR-112 · 2026-11-01)

**Karar özeti:** `media_access` / `media_audit` soft-delete kolonları — canlı şema dump'a hizalanır (seçenek A, 2026-11-01 kullanıcı onayı).

| # | Kural |
|---|---|
| 1 | `.ai/.sql/mysql/*.sql` = şema sözleşme SSOT'u. Canlı DB bu sözleşmeye **migration ile** hizalanır. Canlı'yı referans alıp dump'ı revize etme: yalnız kullanıcı onaylı revizyon kaydıyla olur. |
| 2 | Şema drift bulunduğunda **kod esnetilmez**: fail-closed davranış (403/5xx) korunur; drift raporlanır → kullanıcı kararı → migration veya onaylı dump revizyonu ile kapanır. |
| 3 | Migration paketi = forward + rollback (ayrı dosya) + idempotent pre-check (`information_schema.COLUMNS`). Çalıştırma yalnız kullanıcı onayıyla; önce `mysqldump --single-transaction` yedeği. |
| 4 | Kod içindeki şema varsayımları (`is_deleted = 0`, DEFAULT'lü kolonların bilerek yazılmaması vb.) dump satır referansı taşıyarak yazılır (örn. `coremusic_media.sql:167-168, 193-194`) — sözleşme kanıtı olmadan kod yazılmaz. |
| 5 | Veri dönüşümlü planlarda expand (ADD) fazı önceliklidir; contract (DROP/RENAME) fazı ayrı release'te planlanır. |

**Referanslar:** ADR-112 (draft, `.decisions/draft/`) · `coremusic_media.sql` · `media.coremusic.net/src/Media/MySqlMediaAccessRepository.php`

---

## §2 — API Sürüm Paralellik Kuralı (ADR-113 · 2026-11-01)

**Karar özeti:** API sürüm stratejisi — V1 default + V2 paralel kurumsallaşır (seçenek C, 2026-11-01 kullanıcı onayı).

| # | Kural |
|---|---|
| 1 | Yeni uç TEK route kaydında `/api/{version}/...` placeholder'ı ile tanımlanır (`config/routes.php`); runtime `{version}` → v1&#124;v2. Ayrık V2 route tablosu / kopya tanım YASAK. |
| 2 | `v1` default kalır (`VersionResolver` DEFAULT_VERSION='v1'). `v2` seçimi yalnız URL path (`/api/v2/...`) veya Accept header (`application/vnd.coremusic.v2+json`) ile — öncelik: path → Accept → default. |
| 3 | v2 davranış farkı yalnız `V2Container` override'ı ile tanımlanır (extends V1Container — tek genişleme noktası). Middleware pipeline sırası (ADR-020) sürümden bağımsız ortaktır. |
| 4 | V1 sunset/deprecation bu ADR kapsamı DIŞI: kapatma kararı yeni onaylı ADR'de tanımlanır. |
| 5 | v1/v2 davranış paritesi test edilir (RouteConfigTest parity kapsamı); sessiz sürüm drift'i yetki kapsamı dışına çıkmadan yakalanır. |

**Referanslar:** ADR-113 (draft, `.decisions/draft/`) · `api.coremusic.net/include/Router/VersionResolver.php` · `api.coremusic.net/index.php:51-58, 224-238` · `api.coremusic.net/include/Container/V2Container.php` · `api.coremusic.net/config/routes.php`

---

## §3 — Repository Pattern Standardı Kuralı (ADR-114 · 2026-11-01)

**Karar özeti:** Repository pattern taban şablonu = api yüzeyi; kural yalnız YENİ kodu bağlar — mevcut repository dosyaları DONDU, refactor ayrı onaylı görev (seçenek A, 2026-11-01 kullanıcı onayı).

| # | Kural |
|---|---|
| 1 | Her yeni repository = (a) `I*` interface, (b) concrete `final class implements`, (c) `IDatabaseRegistry`/PDO constructor injection, (d) raw prepared statement + explicit kolon (`SELECT *` yasak — ADR-002), (e) soft-delete kolonlu tabloda `is_deleted = 0` filtresi (satır referansı zorunlu — ADR-112 kural 4). |
| 2 | Taban şablon: `api.coremusic.net/include/Repository/` (interface + concrete ayrı dosya; kanıt `MusicRepository.php:15,19-21` + `IMusicRepository.php:8-11`) — yeni kod bu örneğe hizalanır. |
| 3 | Mevcut repository dosyaları (ADR-114 §1 envanteri) DOKUNULMAZ; repository refactor ayrı onaylı görevdir — bu ADR'den muaf, yeni ADR gerekir. |
| 4 | Subdomain, başka subdomain'in repository sınıfını IMPORT etmez; paylaşım yalnız `shared/` + API sözleşmesiyle (AGENTS §5/§6). |
| 5 | Yeni repo için en az bir birim test dosyası eşlenir (örnek: `shared/tests/Repository/ApiKeyRepositoryTest.php`). |

**Referanslar:** ADR-114 (draft, `.decisions/draft/`) · `api.coremusic.net/include/Repository/MusicRepository.php` · `api.coremusic.net/include/Repository/IMusicRepository.php` · `shared/tests/Repository/ApiKeyRepositoryTest.php`

---

## §4 — Media Yönetimi Admin Panelde Kuralı (ADR-115 · 2026-11-01)

**Karar özeti:** Media yönetim arayüzü admin.coremusic.net panelinde yürütülür; media'nın kendi Faz 3 web paneli İPTAL — `faz3-gui-spec.md` superseded (kullanıcı yönü: "media yönetimi admin.coremusic.net'te yönetilir", 2026-11-01).

| # | Kural |
|---|---|
| 1 | Media yönetimi `admin.coremusic.net` panelinde yapılır; admin media genişlemesi YENİ ve AYRI bir görevdir — ADR-115 yalnız sahiplik kuralı yazar, admin GUI kodu bu kararla yazılmaz. |
| 2 | Media'nın kendi web paneli (`media.coremusic.net/docs/faz3-gui-spec.md`, 8 ekran, v0.1.0→0.1.1) İPTAL'dir; spec `status: superseded` + ADR-115 notu taşır — screen-spec bu yoldan üretilmez. |
| 3 | "Ayrı görev" kayıt zinciri revize: media AGENTS §3.2 hedefi admin (ADR-115), media TODO M-2 kapalı, `.ai/TODO.md` B5 karar notu — eski "ayrı görev" okuması geçersizdir. |
| 4 | Admin media ekranları media HTTP API'yi API sözleşmesiyle tüketir; subdomain kod import yasak (AGENTS §5/§6); mevcut `admin-*` sayfa deseni korunur (`admin.coremusic.net/pages/` — media referansı bugün 0). |
| 5 | Spec'teki iki VR (breakpoint çakışması §6 · BEM `Cxx` §5.4 VR-6) superseded spec'te kapanmaz; admin görevi planlanırken güncel bileşen envanteri + `05-responsive-architecture` üzerinden yeni değerlendirme yapılır. |
| 6 | Herkese açık katalog görüntüleme ekranları bugün kararsız — açık madde; ihtiyaç doğarsa ayrı karar. B8/M-3 port kararı (ADR-039 :5000/:6000) AÇIK kalır. |

**Referanslar:** ADR-115 (draft, `.decisions/draft/`) · `media.coremusic.net/docs/faz3-gui-spec.md` (superseded 0.1.1) · `media.coremusic.net/AGENTS.md` §3.2 · `media.coremusic.net/TODO.md` M-2 · `.ai/TODO.md` PART B B5 · `admin.coremusic.net/pages/`

---

## 4. Yeni Karar → Kural Ekleme Akışı

```text
Karar geliği → kanıt toplama (dosya:satır) → kullanıcı onayı (A/B/C seçenekli, tek soru)
→ ADR draft (.decisions/draft/, sıradaki boş numara — son en yüksek numarayı grep ile doğrula)
→ bu dosyaya yeni § ekle (tablo-maddeli kurallar + disk referansları)
→ kurallar.md'ye 1 satır özet + minor sürüm artışı
→ kapanışta: .ai/.decisions/index.md draft satırı + log.md append
```

## 5. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + Control Plane bloğu |
| 2 | Her § ADR'ye bağlı | Başlıktaki ADR numarası diskte mevcut |
| 3 | Kural ↔ ADR çelişkisi | Yok (RED'de ADR kazanır) |
| 4 | kurallar.md özeti | Her § için 1 satır özet mevcut |
| 5 | Yazım | Bozuk karakter/boş yer tutucu kalmadı |
