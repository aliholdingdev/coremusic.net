---
title: "CoreMusic — ADR-113: API Sürüm Stratejisi — V1/V2 Paralel Kurumsallaşma"
type: adr
category: architecture
date: 2026-11-01
status: draft
authority: ADR-113 Karar Metni (SSOT)
related: "ADR-084 (contract-first route tablosu) · ADR-020 (middleware pipeline sırası) · .ai/.rules/karar-kurallari.md §2"
---

# ADR-113: API Sürüm Stratejisi — V1/V2 Paralel Kurumsallaşma

**Durum:** draft — kullanıcı kararı ALINDI (2026-11-01, seçenek C — paralel kurumsallaşma)
**Karar Veren:** Bayram Ali
**Bulgu kaynağı:** karar turu #2 — "V2 API stratejisi hangisi" (2026-11-01)

## 1. Bağlam

V2 sürümü zaten uygulanmış ve çalışır durumda; karar sorusu "v2'ye ne kadar ileri gidilecek"ti. Mevcut davranışın disk kanıtları:

- Çözümleme kuralları `VersionResolver` (`api.coremusic.net/include/Router/VersionResolver.php`): `DEFAULT_VERSION = 'v1'` (:16), `SUPPORTED_VERSIONS = ['v1','v2']` (:17); öncelik sırası URL path (:30-36) → Accept header `application/vnd.coremusic.v2+json` (:39-46) → default v1 (:49).
- `index.php:52-54` — `RouterVersionResolver` ile `$version` üretilir; `:56-58` — route tablosundaki `{version}` placeholder runtime'da gerçek sürümle değiştirilir.
- `index.php:227` — `($version === 'v2') ? new V2Container() : new V1Container()`; `:238` — `$apiPrefix = '/api/' . $version` → iki sürüm aynı pipeline'ı kullanır.
- `V2Container.php:19` — `final class V2Container extends V1Container`; :9 "Şu an v1 ile birebir aynı davranır (backward compatible)"; v2 override noktaları hazır template olarak yorum bloğunda (:26-43).
- `routes.php:4` — tablo başlığı "ADR-084 Contract First · ADR-020 §1.1-B.1"; :24-26 — "Versiyon prefix'i `{version}` placeholder olarak tanımlanır. index.php runtime'da `{version}` → `v1` veya `v2` ile değiştirir."

Kullanıcıya üç seçenek sunuldu (A: V1 tek kanal · B: V2 default + V1 sunset · C: paralel kurumsallaşma); cevap **C**.

## 2. Karar

**Mevcut paralel davranış kurumsallaştırılır; davranış değişikliği YAPILMAZ:**

1. `v1` default kalır (`VersionResolver` DEFAULT_VERSION korunur).
2. `v2` seçimi yalnız iki kanaldan yapılır: URL path (`/api/v2/...`) veya Accept header (`application/vnd.coremusic.v2+json`); öncelik sırası path → Accept → default değişmez.
3. Tüm uçlar `config/routes.php`'de TEK tabloda `/api/{version}/...` placeholder'ı ile tanımlanır. Ayrık V2 route tablosu / kopya route tanımı YASAK.
4. v2 davranış farkı yalnız `V2Container` override'ı ile tanımlanır (extends V1Container — tek genişleme noktası). Middleware pipeline sırası (ADR-020) sürümden bağımsız ortaktır.
5. V1 sunset planı BU ADR kapsamı dışındadır; V1'in kapatılması/gölgeye çekilmesi ayrı onaylı yeni ADR konusudur.

## 3. Gerekçe

| Seçenek | Sonuç | Değerlendirme |
|---|---|---|
| **C (kabul): paralel kurumsallaşma** | davranış değişikliği yok; mevcut çalışma ADR ile kilitlenir | en küçük paket; bugün v1 ≡ v2 davrandığından tek kanal ile paralelin görünür farkı yok |
| A: V1 tek kanal | `V2Container` + `{version}` mekanizması sökülür | bugün v1 ≡ v2 iken kazanç yok; ileri genişleme noktası boşa gider |
| B: V2 default + sunset | default değişikliği v1 → v2 + V1 saat ayarı | gerçek davranış farkı olmadan değişiklik yalnızca risk; anlamlı olması için önce v2'nin farklılaştırılması gerekir |

## 4. Sonuçlar

- Her yeni uç TEK route kaydıyla (`{version}`) iki sürümü otomatik kapsar; istemci migrasyon yükü yok.
- Genişleme noktası öngörülür: davranış farkı yalnız `V2Container` override'ında; başka yüzeye dokunulmaz.
- Risk: iki sürüm arasında sessiz drift → v1/v2 davranış paritesi testi gereklidir (`RouteConfigTest`'e parity kapsamı; §5).
- v2 farklılaştıkça sözleşme yüzeyi ayrışması (openapi v2 ayrıştırması) gündeme gelir; bu ADR güncellenmez, yeni ADR açılır.

## 5. Gözden Geçirme Tetikleyicileri & Uygulama

1. **Parity testi (öneri):** `api.coremusic.net/tests/` altında `RouteConfigTest`'e aynı route kaydının v1/v2'de aynı sözleşmeyi verdiğine dair kapsam eklenir.
2. **Tetik 1:** V2'ye gerçek davranış farkı getiren ilk özellik → `V2Container` override + özellik testi (bu ADR değişmeden geçerli kalır).
3. **Tetik 2:** V1 sunset/deprecation düşünülüyorsa → yeni ADR; şartlar orada tanımlanır.
4. **Tetik 3:** v3 gündeme gelirse → `SUPPORTED_VERSIONS` genişletilir; paralellik modeli yeniden değerlendirilir.

## 6. Draft → Accepted Geçiş Şartı (tümü zorunlu)

- [ ] `.ai/.decisions/index.md`'ye draft kayıt satırı eklendi (kapanış paketiyle birlikte)
- [ ] `.ai/.rules/karar-kurallari.md` §2 ve `.ai/.rules/kurallar.md` özeti güncellendi (bu oturumda yapılıyor)
- [ ] (Önerilen) v1/v2 parity testi yazılmış ve `composer test` kapısı yeşil

## 7. Doğrulama Kanıtları

- `Router/VersionResolver.php:16-17, 30-36, 39-46, 49` (2026-11-01 okundu)
- `index.php:51-58` (resolve + `{version}` placeholder replace), `:224-238` (container seçimi + ortak `$apiPrefix`)
- `Container/V2Container.php:6-24` (extends V1Container, `getVersion 'v2'`, backward-compatible notu)
- `config/routes.php:4` (ADR-084 Contract First başlığı), `:24-26` (`{version}` placeholder kuralı)
- Kural türevi: `.ai/.rules/karar-kurallari.md` §2
