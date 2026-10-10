---
type: architecture
category: layer
title: "K009 — API"
date: 2026-10-09
status: active
version: 2.0.1
authority: SSOT
---

# K009 — API

## §1 Kimlik
- Katman: K009 · Alan: **A2** (K8-K9).
- Kapsam: API Gateway/BFF — subdomain uçları, JSON birlik arayüzü, sözleşme yönetimi.

## §2 Sorumluluk
1. Gateway/BFF×4 (ADR-084): rate-limit, Origin/CSRF (ADR-094), public API güvenlik kuralları (ADR-020).
2. Stateless REST uçları; JSON birlik arayüzü; tutarlı hata modeli; cache başlıkları (plan §2-5, §4-8).
3. OpenAPI sözleşmesi üretimi (ADR-084 PLANNED; plan §6 Faz 5).
4. CQRS yalnızca okuma sorgusu düzeyinde; ayrı okuma DB'si/event-store KURULMAZ (plan §5.3).
5. Routing kenarı: clean URL/normalization/SPA router sözleşmesi (ADR-009/016/021).

## §3 Bağlantılar (Dependency Rule)
- **Alt:** K007 (middleware) → K006 → K010/K008 → K005 — plan §4 zinciri.
- **Üst (çağıran):** K011 UX — yalnız HTTP/API sözleşmesiyle; kod importu YASAK (plan §3 kuralı).

## §4 ADR Bağlantıları
| ADR | Başlık |
|---|---|
| ADR-084 | API Gateway Architecture (Gateway/BFF×4 IMPLEMENTED; CQRS+OpenAPI PLANNED) |
| ADR-020 / ADR-094 | API Public Security · Origin/CSRF pipeline |
| ADR-004 | Multi-Domain SPA Architecture |
| ADR-009 / ADR-016 / ADR-021 | Clean URL · URL Normalization · SPA Router Contract |

## §5 Durum
**IMPLEMENTED (Gateway/BFF)** — kanıt (2026-10-09): `api.coremusic.net/{index.php,composer.json,phpunit.xml}` + `auth.`/`home.`/`media.` composer.json. **PLANNED**: OpenAPI + CQRS-okuma sözleşmesi (ADR-084, plan §6 Faz 5).

## §6 Risk / Not
- "Gateway/BFF×4" sayısının tek kaynağı ADR-084'tür; kod içi sayaç doğrulanmadı → **UNKNOWN**.
- Uzak çağrılara (CDN/3. parti) timeout+retry+fallback zorunluluğu (plan §5.6) bu uçlarda denetlenmedi.
## §7 Makro Katman Karşılığı
K009-api → Makro **K4** (bağlayıcı gruplama, 2026-10-10) · Kök eşleme: [[../00-master-index]] (Makro Eşleme bölümü) · makro özet: [[../katmanli-mimari-k0-k4/index]]

- K4 içinde K009 = **Gateway/BFF**: istemciye sunulan HTTP/API'nin sahibi; K3'ün iş mantığını JSON/REST olarak sunar (makro özet §4 K4 satırı).
- Makro komşular: alt = **K3** Uygulama Mantığı (mikro kenar: `K009→K010` Gateway → controller — izinli, matris §2) ve **K2** (mikro skip: `K009→K007` Gateway → middleware — istisna kaydı, makro özet §6.2-13) · üst: K4 en üst makro katmandır, yalnız istemci/kullanıcı çağırır (makro özet §3).
- K4 içi kardeşler: K011 UX (K009'i yalnız HTTP/API ile çağırır) · K014 ağ (edge başlıkları) — kod importu YASAK (matris §2).

## §8 Arayüz (Girdi/Çıktı & API)
- **Girdi (dışarıdan):** istemciden HTTP isteği — edge başlıkları (CSP nonce, Origin) K014 sınırında uygulanır; K009 isteği K010 controller'a devreder (matris §2: "K009 API → K007 middleware · K010 uygulama · plan §4 (Gateway → controller)").
- **Çıktı:** JSON birlik arayüzü + tutarlı hata modeli + cache başlıkları (§2.2; makro özet §4 "DTO/JSON/HTML döner").
- **Diskte kanıtlı somut varlıklar (§5, 2026-10-09 ölçümü):**

| Varlık | Gerçek yol | Rol |
|---|---|---|
| Gateway kökü | `api.coremusic.net/index.php` | Gateway/BFF girişi |
| Composer zemini | `api.coremusic.net/composer.json` | Bağımlılık/autoload zemini |
| Test yapılandırması | `api.coremusic.net/phpunit.xml` | Gateway test kapısı |
| BFF composer zemini | `auth.` / `home.` / `media.coremusic.net/composer.json` (3 dosya) | Subdomain BFF bağımlılıkları |

- **İzinli/yasaklı çağrı özeti:** K009 → K010 (controller, plan §4) ve K009 → K007 (middleware, skip-edge istisna kaydı) izinli · K011 → K009 yalnız HTTP/API sözleşmesiyle; **JS/CSS kod importu YASAK** (matris §2, plan §3) · K009 → K005/K010 doğrudan veri erişimi YASAK (controller→service→K2 zinciri zorunlu, matris §3).
- `api.coremusic.net/index.php` akış detayı (routing/gateway mantığı) okunmadı → **UNKNOWN** (makro özet §7-8).

## §9 Hata Modları ve Güvenlik Sınırı
- **Failure modes (katman sınırında):**
  1. **controller→PDO bypass ihlali** — Gateway köprüsü atlanırsa katman ihlali → derhal revert + log ERROR (matris §3; makro özet §5-FM1).
  2. **Gateway "×4" belirsizliği** — kaç BFF/topoloji olduğu netleşmedi → dağıtım kararı UNKNOWN (§6; makro özet §5-FM5).
  3. **OpenAPI sözleşmesi yokluğu (PLANNED)** — API sözleşmesi belgelenmemiş; istemci uyumu garantilenemez (ADR-084, plan §6 Faz 5).
  4. **Uzak çağırı fallback'i denetlenmedi** — CDN/3. parti timeout+retry+fallback (plan §5.6) bu uçlarda doğrulanmadı (§6).
- **İzolasyon:** subdomain'ler birbirine kod yayılmaz (yalnız shared/ + API sözleşmesi — matris §2; [[../K014-ag/index]] §2.2); bir BFF hatası diğerine kod olarak sızmaz. Hata modeli tutarlılığı K009'un §2.2 kaydıdır.
- **Güvenlik sınırı (kim neyi doğrular):** K009 kendisi rate-limit, Origin/CSRF (ADR-094) ve public API güvenlik kurallarını (ADR-020) uygular (§2.1) — devamında kanonik sıra: OriginCheck+CSRF → RateLimit (APCu) → CSP nonce → Auth → PageRouter (makro özet [[../katmanli-mimari-k0-k4/k2-cekirdek-servisler-middleware]] §3.2). CSP nonce üretimi K014/ADR-012'nin sınırıdır.

## §10 Bilinmeyenler (⚠️ / UNKNOWN)

| Bulgu | Durum |
|---|---|
| Gateway "×4" sayısının tek kaynağı ADR-084; kod içi sayaç doğrulanmadı | UNKNOWN (§6; makro özet §7-3) |
| `api.coremusic.net/index.php` akış detayı (routing mantığı) | UNKNOWN — okunmadı (makro özet §7-8) |
| OpenAPI sözleşmesi | PLANNED — diskte yok (ADR-084; makro özet §7-1) |
| CQRS-okuma sözleşmesi | PLANNED (plan §5.3; makro özet §7-2) |
| Uzak çağırı timeout+retry+fallback denetimi (plan §5.6) | ⚠️ VERIFICATION REQUIRED (§6) |
