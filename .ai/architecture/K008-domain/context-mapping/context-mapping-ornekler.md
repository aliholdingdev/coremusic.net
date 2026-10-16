---
node_id: "K008-context-mapping-ornekler"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-context-mapping-kapsam"
evidence:
  - "shared/config/domain.php satır 1–19 (tam dosya)"
  - "shared/src/Config/DomainConfig.php satır 28–45, 47–57, 84–96"
  - "shared/config/routes.php satır 14–22"
validation: "2026-10-15 — 3 örnek (PHP config, PHP class ×2); hepsi disk'ten birebir kopyalandı"
open_questions:
  - "fallback drift (5 vs 7) düzeltmesi → kod değişikliği, onay bekliyor"
group: kod
---

# context-mapping — Örnekler (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> Üç örnek de disk'ten birebir kopyalanmıştır.

## §1 ÖRNEK 1 — PHP: domain.php — haritanın tamamı (satır 1–19)

```php
<?php declare(strict_types=1);

return [
    'primary'         => 'coremusic.net',
    'primary_port'    => 80,
    'subdomain_port'  => 80,
    'subdomains' => [
        'auth'   => 'auth.coremusic.net',
        'home'   => 'home.coremusic.net',
        'assets' => 'assets.coremusic.net',
        'music'  => 'music.coremusic.net',
        'admin'  => 'admin.coremusic.net',
        'media'  => 'media.coremusic.net',
        'api'    => 'api.coremusic.net',
    ],
    'subdomain_ports' => [
        'home' => 81,
    ],
];
```

**Dosya:** `shared/config/domain.php` · **Satır:** 1–19 (tam dosya).
**Ne kanıtlıyor:** 7 subdomain haritası tek kaynaktadır; `home` için ayrı port
tanımı (`subdomain_ports`, satır 16–18) — port eşlemesinin de bağlamın bir
parçası olduğu kanıtı. Bu dosyayı onaysız değiştirmek `shared/AGENTS.md` §4'te
yasağıdır.

## §2 ÖRNEK 2 — PHP: DomainConfig — fallback + fail-safe eşleme (satır 28–45, 89–96)

```php
<?php
// Dosya: shared/src/Config/DomainConfig.php · satır 28-45 (kesit)
if ($domainConfigPath === null || !file_exists($domainConfigPath)) {
    $this->config = [
        'primary' => 'coremusic.net',
        'primary_port' => 80,
        'subdomain_port' => 80,
        'subdomains' => [
            'auth'   => 'auth.coremusic.net',
            'home'   => 'home.coremusic.net',
            'assets' => 'assets.coremusic.net',
            'media'  => 'media.coremusic.net',
            'api'    => 'api.coremusic.net',
        ],
    ];
} else {
    $this->config = require $domainConfigPath;
}
$this->subdomains = $this->config['subdomains'] ?? [];
```

```php
<?php
// Dosya: shared/src/Config/DomainConfig.php · satır 89-96 (kesit)
public function getUrl(string $name): string
{
    $host = $this->subdomains[$name] ?? null;
    if ($host === null) {
        return $this->getPrimaryUrl();
    }
    return $this->buildUrl($this->getScheme(), $host, $this->getSubdomainPort());
}
```

**Ne kanıtlıyor:** (a) fallback haritası 5 subdomain'e düşer — `music` ve
`admin` yok (**D-CM4 drift kanıtı**, domain.php satır 11–12 ile çelişir);
(b) `getUrl()` tanınmayan adı primary URL'e çevirir — fail-safe invariant
(D-CM8), exception yok.

## §3 ÖRNEK 3 — PHP: routes.php — hardcoded domain yasağı + kurulum (satır 10–22)

```php
<?php
// Dosya: shared/config/routes.php · satır 10-22 (kesit)
/**
 * CoreMusic Home — SPA Route Definitions
 *
 * Tüm URL'ler DomainConfig'den dinamik üretilir.
 * Hardcoded domain KULLANILMAZ.
 */

// Domain config'den URL'leri üret (fallback: fallback domain'ler)
$domainFile = dirname(__DIR__, 2) . '/shared/config/domain.php';
$domainConfig = new DomainConfig(file_exists($domainFile) ? $domainFile : null);

$scheme = 'http'; // Local dev — production'da DomainConfig.scheme kullanılır
$authDomain  = AuthRouteConfig::getAuthUrl($scheme, $domainConfig);
$homeDomain  = AuthRouteConfig::getHomeUrl($scheme, $domainConfig);
$clientId    = 'coremusic-web';
$callbackUrl = $homeDomain . '/auth/callback';
```

**Ne kanıtlıyor:** tüketicinin bağlamı nasıl çektiği — `routes.php` haritayı
`DomainConfig` üzerinden okur, yorum satırında hardcoded domain yasağı yazılıdır
(satır 11, invariant D-CM12). `$scheme = 'http'` lokal içindir; üretimde
`DomainConfig.scheme` kullanılır (satır 18 yorumu).

## §5 DÜZELTME/NOT KAYDI

| # | Not | Durum |
|---|---|---|
| 1 | Eski notlarda `ContextMap` sınıfı/sheması varmış gibi ifadeler görüldü — grep 0 isabet: sınıf yok, şema yok | ✅ düzeltildi (D-CM14) |
| 2 | fallback 5 vs harita 7 subdomain — sessiz degradation; düzeltme kod onayı ister | ⚠️ raporlandı (D-CM4) |

**Kardeş dosyalar:** [[context-mapping-kapsam]] · [[context-mapping-dogrulama]] · [[context-mapping-web-arastirma]] · [[context-mapping-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
