---
title: "K011 · routing — Klasör İndeksi"
type: architecture
kodeme: kademe-2
module: K011-application
parent: routing
status: active
updated: 2026-10-15
children:
  - routing-yonlendirme-cati.md
  - routing-dogrulama.md
  - routing-web-arastirma.md
  - routing-ornekler.md
  - routing-bilesenler.md
group: mimari
---

# K011 · routing — Klasör İndeksi

> **Amaç:** `routing/` klasörünün künyesi: 5 dosyalık çocuk kataloğu, her dosyanın
> grubu (mimari/kod/kanit) ve okuma sırası. Ünite-3 istisnası (şartname §7) gereği
> kapsam dosyası ayrı üretilmedi; kapsamı mevcut `routing-yonlendirme-cati.md` taşır.

**ELI15:** Bu klasör, CoreMusic'in isteklerin doğru yere gitmesini sağlayan
"yol haritası" katmanını belgeler: API route tablosu (PHP), SPA pushState
router'ı (Vanilla JS) ve bunları doğrulayan testler/kaynaklar.

**Sınır (ne DEĞİL):** İş kuralı K008-domain'de · önbellek politikası
`caching/` klasöründe · veri taşıyıcı sözleşmesi `dto/` klasöründe · servis
koordinasyonu `usecase/` klasöründe.

---

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|---|---|---|---|
| 0 | `index.md` — bu dosya | mimari | katalog | Klasör giriş noktası, children listesi |
| 1 | `routing-yonlendirme-cati.md` | mimari | kapsam | Ana kapsam + ELI15 + sınır (kapsam yerine geçer) |
| 2 | `routing-dogrulama.md` | kanit | iddia↔kanıt | İddia↔Dosya↔Satır tablosu, test envanteri, PASS/BLOCK kapısı |
| 3 | `routing-web-arastirma.md` | kanit | kaynak↔repo | Resmi kaynak havuzu (200) + claim↔repo çapraz matris |
| 4 | `routing-ornekler.md` | kod | uygulama | PHP + Vanilla JS + SQL + CSS gerçek repo örnekleri |
| 5 | `routing-bilesenler.md` | mimari | envanter | Dizin/dosya sayıları, bileşen↔akış iz düşümü, bağımlılık grafiği |

**Okuma sırası:** `index.md` → ana dosya (kapsam) → `bilesenler` (envanter) →
`dogrulama` (kanıt) → `web-arastirma` (dış kaynak) → `ornekler` (kod).

**URL havuzu (şartname §9 · routing):** 6 URL — her URL bu klasörde TEK dosyada
kullanılır:
`web-arastirma` (3) · `dogrulama` (1) · `ornekler` (1) · `bilesenler` (1).

**Kanıt yüzeyi (özet):** `api.coremusic.net/config/routes.php` (17 kayıt) ·
`shared/src/Api/Routing/RouteTable.php` (405/404 ayrımı) ·
`shared/src/Api/Gateway.php` (dispatch) · `shared/src/PageRouter/RouteRegistry.php`
(SPA `{param}` kalıbı) · `assets.coremusic.net/js/router/` (28 dosya) ·
`shared/tests/Api/RouteTableTest.php`.

**Durum:** active · kademe-2 · K011 kök `index.md`'ye bu dosyadan bağımsız olarak
orkestratör karar verir.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
