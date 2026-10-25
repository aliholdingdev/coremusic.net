---
title: "api.coremusic.net — TODO Listesi"
type: todo
category: project-tracking
version: 1.2.0
status: active
authority: SSOT
updated: 2026-10-25
---

# api.coremusic.net — TODO Listesi

**Zorunlu Bağlantılar:** [[PRD.md]] · [[CONTEXT.md]] · [[../TODO]] · [[../PRD.md]] · [[../.ai/TODO.md]]

**Kural (Zero-Hallucination):** Her madde PRD §7 / disk kanıtına dayanır; kanıtlanamayan madde yazılmaz. Durum: `[ ]` açık · `[x]` kapalı.

**eli10:** API'nin kalan işleri: shared sözleşmesiyle çapraz kontrol, test kapsamı, uç genişleme.
**eli15:** Maddeler `PRD.md §7`'den türetildi; QA kapısı `composer test`'tir. Ölçümsüz iddia yazılmaz — gerekirse `⚠️ VERIFICATION REQUIRED`.

## Açık Maddeler

- [ ] **#7 (shared) + ADR-020 route sözleşmesi çapraz kontrolü** — API route'ları shared sözleşmesiyle karşılaştırılır · Kaynak: `[[PRD.md]]` §7.1
- [ ] **PRD-API-006 test ölçümü** — route + controller + test üçlüsü; QA kapısı `composer test` · Kaynak: `[[PRD.md]]` §7.2
- [ ] **Uç genişlemesi** — yeni uç için route + controller + test birlikte (ölçüm gerekirse `⚠️ VERIFICATION REQUIRED` ile üst `[[../PRD.md]]`'ye bildirilir) · Kaynak: `[[PRD.md]]` §7.3

## TAM Madde Listesi — Küçükten Büyüğe (Geçmiş · Açık · Gelecek) — 2026-10-25

> Tek doğruluk listesi; `## Geçmiş & Mikro Ölçek` kanıt detayıdır. Sıra: MICRO → ORTA → EPIC.

### A. GEÇMİŞ — TAMAMLANAN
- [x] MICRO: `.gitleaks.toml` tracked — `43e6763` · phpstan level 5 → 0 hata — `3e3ed57`
- [x] ORTA: `RouteConfigTest` + `SmokeTest` + `AuthControllerTest` yeşil (AGENTS §6 kanıtı)
- [x] ORTA: SEC-03 public-route tek tanımı + SEC-01 rate-limit ip bucket zorunlu — `d3d5c4e`
- [x] ORTA: DTO kütüphanesi + `PaginationResponse` + RouteTable `fromArray` deseni
- [x] EPIC: API gateway iskeleti — `index.php` 10-adım hat (ADR-020/094) + route kayıtları + `composer.json` senkron

### B. AÇIK — EKSİK / YARIM (küçükten büyüğe)
- [ ] MICRO: G-2a — `composer test` tek çalıştırma → route/controller/test sayısı
- [ ] MICRO: G-1a — `config/routes.php` satır sayısı + `public:true` sayısı (baseline ölçümü)
- [ ] MICRO: G-4a — `/api/v1/power/*` uçlarının giriş yüzeyi için 2 seçenek + dayanak notu
- [ ] MICRO: ADR-020 açık kalanları 1 satır durum (API key kod YOK · JWT stub null · CORS · audit SQL var/PHP yok)
- [ ] MICRO: G-1 sapma listesi için route adlarını CSV gibi çıkar (okunabilir liste)
- [ ] ORTA: G-1 **#7 shared + ADR-020 route sözleşmesi çapraz kontrol**
- [ ] ORTA: G-2 **PRD-API-006 test ölçümü** → QA kapısı `composer test`
- [ ] ORTA: G-3 Uç genişlemesi kuralı: route + controller + test birlikte
- [ ] EPIC: G-4 **Power Governor route yüzeyi kararı (api vs pro)** → PRD-35 satırına bağlanır
- [ ] EPIC: `/stream` endpoint PHPUnit HTTP testi (`.ai/TODO` §4 — curl kanıtı var, test YOK)

### C. GELECEK — SIRADAKİ BÜYÜK İŞLER (öncelik sırası)
| Sıra | EPIC iş | İlk MICRO adım |
|---|---|---|
| 1 | G-4 yüzey kararı (T-04 7 uç ile birlikte) | 2 seçenek notu |
| 2 | G-2/G-3 ölçüm + yeni uç | `composer test` |
| 3 | `/stream` HTTP testi | test iskeleti |
| 4 | PRD-API-006 kapı kapanışı + OpenAPI spec | route listesi |## Adım Adım Yürütme (2026-10-25 güçlendirme)

**Kural:** Her adım tek oturum · `[ ]` → çalış · `[x]` + kanıt → kapanır · **YAPMA** bağlayıcı · komutu gönder → agent yapar.

| # | Adım | Ne yapılacak (kaynak) | YAPMA | Hazır komut |
|---|---|---|---|---|
| G-1 | [ ] | #7 (shared) + ADR-020 route sözleşmesi çapraz kontrol | middleware sırası değişikliği (ADR-020 frozen) | `G-1: api route'larını shared sözleşmesi + ADR-020 ile çapraz doğrula, sapmaları listele` |
| G-2 | [ ] | PRD-API-006 test ölçümü — route+controller+test üçlüsü; QA kapısı `composer test` | kanıtsız "testli" | `G-2: composer test çalıştır, route/controller/test kapsamını ölçüp PRD §4'e yaz` |
| G-3 | [ ] | Uç genişlemesi kuralı: yeni uç = route + controller + test birlikte | testsiz tek uç ekleme | `G-3: sıradaki yeni ucu route+controller+test üçlüsü olarak ekle` |
| G-4 | [ ] | **Power Governor route yüzeyi kararı** — T-04 sözleşmesindeki `/api/v1/power/*` uçları `api` mi `pro` mu? (PRD-35 satırı YOK, VR) | kararı beklemeden routes.php'ye ekleme | `G-4: Power Governor uçlarının hangi yüzeyde olacağı kararını ver (api vs pro) — dayanağı PRD'ye yaz, routes.php'ye ŞİMDİ EKLEME` |

**Yüzey yasakları:** middleware sırası (10 adım frozen) · `public:true` uç · Bearer stub'ı sahile çıkarma (session-only) · `.env`/secret · `shared/**` düzenleme · commit.
## Geçmiş & Mikro Ölçek — Tam Liste (2026-10-25)

**Geçmiş — tamamlanan (kanıtlı):**
- [x] `[ORTA]` Public-route tek tanımı (SEC-03) + rate-limit ip bucket zorunlu (SEC-01) — `d3d5c4e`
- [x] `[ORTA]` RouteConfigTest + SmokeTest + AuthControllerTest mevcut ve yeşil (AGENTS §6 kanıtı)
- [x] `[MİKRO]` `.gitleaks.toml` tracked — `43e6763` · phpstan level 5 → 0 hata — `3e3ed57`

**Mikro işler (en küçük → büyük):**
- [ ] `[MİKRO]` G-2 altı: `composer test` tek çalıştırma → route/controller/test sayısını yaz
- [ ] `[MİKRO]` G-1 altı: `config/routes.php` satır sayısını ve `public:true` sayısını say (baseline)
- [ ] `[MİKRO]` G-4 altı: `/api/v1/power/*` uçlarının giriş yüzeyi için 2 seçenek + dayanak notu
- [ ] `[ORTA]` G-1 sapma listesi → G-3 ile yeni uç → `[EPİK]` PRD-API-006 kapsam kapısı## Referanslar

- `[[PRD.md]]` (§7 Sonraki Adım) · `[[CONTEXT.md]]` · `[[WORKFLOW.md]]` · `[[AGENTS.md]]` · `[[CLAUDE.md]]`
- Aile indeksi: `[[../TODO]]` · `[[../PRD.md]]` · Vault TODO: `[[../.ai/TODO.md]]`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
