---
node_id: K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security
children: [uygulama-guvenlik-siniri-anayasa, uygulama-guvenlik-siniri-surucu, uygulama-guvenlik-siniri-ornekler, uygulama-guvenlik-siniri-windows, uygulama-guvenlik-siniri-linux, uygulama-guvenlik-siniri-macos, uygulama-guvenlik-siniri-ios, uygulama-guvenlik-siniri-android, uygulama-guvenlik-siniri-tizen, uygulama-guvenlik-siniri-webos, uygulama-guvenlik-siniri-reactos]
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik
  - K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli
evidence:
  - https://man7.org/linux/man-pages/man2/memfd_create.2.html
  - https://man7.org/linux/man-pages/man7/uts_namespaces.7.html
  - https://man7.org/linux/man-pages/man2/openat2.2.html
  - shared/src/Middleware/OriginCheckMiddleware.php:48
  - shared/src/Middleware/RateLimiterMiddleware.php:86
validation:
  - "2026-10-13: 3 web URL HEAD ile HTTP 200; 2633'luk katalog envanteriyle kesisim 0."
  - "2026-10-13: 2 disk kaniti `Dosya:` + `Satir:` + Select-Object -Index (N-1) ile satir birebir okundu."
open_questions:
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri (kim hangini uygular)? [UNKNOWN]"
group: mimari
---

# uygulama-guvenlik-siniri — Deep-Dal İndeksi (Kademe-4)

## §0 ELI15

Bu klasör, uygulamanın **OS'a dayandığı güvenlik sınırını** anlatır: politika
nerede tanımlanır, nerede zorlanır, OS tarafı **boş kalırsa** ne olur. Üç dosya
vardır — anayasa, sürücü, örnekler. Bu index yalnız kapağı ve sınırları taşır.

## §1 Amaç

Bu alt-dal tek soruyu yanıtlar: **"Uygulama güvenlik kontrolü hangi noktada
OS'e devrolur ve bu devir repo'da kurulmuş mudur?"** Cevap üç parçadır:
anayasa sınırların tanımı · sürücü zorlamanın mekanizması · örnekler kanıtın
kendisi. Uygulama auth (K009/K010) bu dalın **kapsamı dışındadır**.

## §2 Kademe / rol

| Alan | Değer |
|---|---|
| Kademe | 3 (klasör) — içerik dosyaları Kademe-4 |
| Üst düğüm | `K000-isletim-sistemi/isletim-sistemi-security` |
| `node_type` | `subdomain` |
| Rol | uygulama ↔ OS güvenlik sınırı |
| Kapsam | syscall yüzeyi · sandbox · namespace/cgroup · dosya sistemi yetkisi · ağ filtresi — **kapsam dışı:** OWASP, auth, CSRF, CSP, kripto (K009/K010) |

## §3 Dosya envanteri (3 içerik)

| Dosya | `group` | Satır (2026-10-13) | Ne verir |
|---|---|---|---|
| `uygulama-guvenlik-siniri-anayasa.md` | `mimari` | 503 | sınırın tanımı, devir matrisi, fail-closed ilkesi |
| `uygulama-guvenlik-siniri-surucu.md` | `mimari` | 391 → `>=500` | politika yaşam döngüsü, gerçek kod kanıtları, sira analizi |
| `uygulama-guvenlik-siniri-ornekler.md` | `kod` | yeni `>=500` | repo kanıtı, OS karşılıkları, anti-pattern |
| `index.md` (bu dosya) | `mimari` | 40–100 | kapak, sınır, `depends_on`, açık soru |

## §4 §GRUPLAR

| Dosya | Kademe | group |
|---|---|---|
| `uygulama-guvenlik-siniri-anayasa` | Kademe-4 | mimari |
| `uygulama-guvenlik-siniri-surucu` | Kademe-4 | mimari |
| `uygulama-guvenlik-siniri-ornekler` | Kademe-4 | kod |

**Toplam:** 1 index (mimari) + 3 içerik (mimari x2 · kod x1) + 8 OS kartı (`kod`) = 12 dosya · OS kartları: `uygulama-guvenlik-siniri-<os>.md` — windows · linux · macos · ios · android · tizen · webos · reactos (2026-10-14, hedef referans).

## §5 Komşu sınırı

| Komşu dal | Ne devralır | Devretmez (sinir cümlesi) |
|---|---|---|
| `cekirdek-guvenlik` | sandbox'ın **cekeceği** koruma sonucu | koruma halkalarının tanımı (SMEP/SMAP/imzalama) |
| `yetki-ve-erisim-modeli` | sınırın **yetki etkisi** (kim izin alır) | UID/GID, capability, ACL, MAC tanımı |
| `isletim-sistemi-testing` | sınırın **test edilebilirliği** | kapı sırası, coverage eşiği |
| `K009-security` | sınırın uygulama-yüzü karşılığı | OWASP denetimi, auth/CSRF tasarımı |
| `K010-identitiy` | oturum/ kimlik aktarımı | kimlik doğrulama akışı |

> **K009/K010 ayrımı (baglayici):** OWASP Top 10 denetimi, auth/CSRF/CSP
> tasarımı, kripto seçimi ve rate limit politikası → `K009-security` +
> ADR-010/013/022. Bu dal yalnız **OS'e dayanan** sınırı konu alır.

## §6 `depends_on`

| Bağımlılık | Ne alır | Neden |
|---|---|---|
| `cekirdek-guvenlik` | koruma halkası sonucu | sınır OS üstünde durur |
| `yetki-ve-erisim-modeli` | yetki modelinin sonucu | sınır kimi durdurduğunu bilir |

## §7 Açık sorular

| # | Soru | Durum | Kapanış kriteri |
|---|---|---|---|
| S2 | Uygulama zorlaması hangi noktada OS zorlamasına devredilecek? | `[UNKNOWN]` | sinir matrisi onayı (`-kapsam.md` §3.1) + ADR (`>=088`) |
| S2b | CORS config'inde wildcard (`*`) değeri var mı? | `UNKNOWN` | config dosyası okunmalıdır (secret yasağı kapsamı dışında) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
