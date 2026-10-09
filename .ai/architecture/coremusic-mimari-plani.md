---
type: architecture
category: architecture
title: "CoreMusic — Web Platformu Mimari Planı (Sıfırdan)"
date: 2026-10-09
status: active
version: 1.0.0
authority: SSOT — mimari plan (bağlayıcı değil; ADR'ye dönüşüm Vault Steward onayı ister)
---

# CoreMusic — Web Platformu Mimari Planı (Sıfırdan)

> Bu plan, 50+ yıllık kanıtlanmış mimari prensiplere (Parnas'72 · Codd'70 · Fielding'00 ·
> Cockburn'05 · Fowler'15 · Modular Monolith 2024) dayanır; dayanaklar
> `[[reports/2026-10-09-50-yillik-mimari-ilkeler-arastirmasi]]` raporundadır.
> Mevcut ADR'ler (frozen dahil) **değiştirilmez**; bu plan onları yerinde kullanır.

## §1 Sistem Özeti (disk kanıtı)

| Varlık | Kanıt (disk) |
|---|---|
| Subdomain'ler | kök dizin: `api.` · `auth.` · `home.` · `media.` · `assets.coremusic.net` |
| Ortak çekirdek | `shared/` (composer package; `shared/src/{Middleware,PageRouter,Database}/` — AGENTS.md §25.2) |
| Katman modeli | `.ai/architecture/K000-K020` — HEAD'de 29 dosya, **çalışma ağacından silinmiş** (`git status: D`, kullanıcı kararıyla korunuyor, 2026-10-09); katman referansları yalnız ADR/HEAD kanıtıyla taşınır |
| Veri | "18 BCNF DB" (ADR-003/040 iddiası — sayı **⚠️ VERIFICATION REQUIRED**, Data Engineer sayımı bekleniyor), PDO-only (ADR-002 frozen) |
| Frontend | Vanilla JS + ITCSS (ADR-001 frozen), Multi-Domain SPA (ADR-004 frozen) |
| API | Gateway/BFF×4 IMPLEMENTED (ADR-084), CQRS+OpenAPI PLANNED |
| Olaylar | PSR-14 event altyapısı implement, production wiring PLANNED (ADR-086) |
| Senkron | Multi-provider Outbox+WAL (ADR-081) |

## §2 Mimari Karar Özeti (prensip → CoreMusic)

| # | Prensip (kaynak-yıl) | CoreMusic Uygulaması | Durum |
|---|---|---|---|
| 1 | Katmanlı mimari (EWD196'68/OSI'80) | K000-K020; her katman yalnız alt katmana bağımlı | UYGULANIYOR |
| 2 | Bilgi gizleme (Parnas'72) | Modül sınırı = gizlenecek karar: modül kendi PDO/şemasını saklar | UYGULANACAK (§5.1) |
| 3 | İlişkisel model (Codd'70) | BCNF DB (sayı ⚠️ VERIFICATION REQUIRED), prepared statement, `SELECT *` yasak (ADR-002/033) | UYGULANIYOR |
| 4 | Üç katman (90'lar) | Sunum (vanilla JS/ITCSS) · Uygulama (PHP 8.4 controller/service) · Veri (PDO/MySQL) | UYGULANIYOR |
| 5 | REST (Fielding'00) | Stateless API uçları, JSON birlik arayüzü, cache başlıkları (ADR-084) | UYGULANIYOR |
| 6 | Altıgen (Cockburn'05) | Domain servisleri DB/HTTP bilmez; PDO & adapter = altyapı | KISMEN (§5.2) |
| 7 | Modular Monolith (2024) | Tek deploy; subdomain → modül; modül = klasör+namespace+arayüz | HEDEF (§5.1) |
| 8 | MonolithFirst (Fowler'15) | Servis çıkarma yalnız sınır netleşince; erken dağıtım yasak | HEDEF |
| 9 | OWASP savunma derinliği | PDO→validator→CSRF→escape→yetki→rate-limit sırası (ADR-010/012/013/020/094) | UYGULANIYOR |
| 10 | Outbox (Richardson) | Cross-DB yazmada outbox zorunlu; tek DB'de tek transaction (ADR-081) | UYGULANIYOR |

Reddedilen/bilinçli olarak kullanılmayanlar (nedeniyle): mikroservis dağıtımı (R-012, erken
optimizasyon) · GraphQL (R-011) · ORM (R-002/006) · tam CQRS/event-store (yalnız okuma sorgusu
düzeyinde, §5.3) · ESB/WS-* (etiket öldü, ilke yaşadı — Manes'09).

## §3 Topoloji (Subdomain ↦ Sorumluluk)

```
Tarayıcı (Vanilla JS SPA, ITCSS --cm-* token)
   │
   ├─ home.coremusic.net   → public/uygulama yüzeyi (sunum katmanı)
   ├─ auth.coremusic.net   → kimlik, oturum, JWT RS256, MFA (ADR-043/052/059/095)
   ├─ api.coremusic.net    → Gateway/BFF (ADR-084): rate-limit, Origin/CSRF (ADR-094), sözleşme
   ├─ media.coremusic.net  → medya arşivi/teslim (ADR-092 ULID dizin ekseni)
   └─ assets.coremusic.net → statik varlık (ITCSS katmanları, token'lar)
          │
       shared/  → tek PSR-4 package: Middleware (PSR-15 ×11), PageRouter, Database (PDO)
          │
       MySQL 9 — 18 BCNF DB (modül başına veri sahipliği; cross-DB = outbox/WAL)
```

Kural: subdomain'ler **birbirinin içine kod IMPORT etmez**; yalnız `shared/` ve API sözleşmesiyle
konuşur (Parnas'72 — her subdomain kendi gizli kararını saklar).

## §4 İstek Yaşam Döngüsü (kanonik akış)

```
HTTP istek
 → 1. OriginCheck + CSRF koşullu doğrulama (ADR-094)
 → 2. Rate limiting APCu (ADR-013)
 → 3. CSP nonce strict-dynamic (ADR-012)
 → 4. Auth: session + JWT RS256 hybrid (ADR-052/095) — bypass listesi ADR-008
 → 5. PageRouter (shared) → controller
 → 6. Controller → Domain Service (DTO girer/çıkar; PDO bilmez)
 → 7. Repository (PDO prepared; modül-içi private — `SELECT *` yasak)
 → 8. Yanıt: JSON birlik arayüzü; hata modeli tutarlı; log stdout + redaction
```

Katman ihlali (ör. controller'ın doğrudan PDO çağırması) → **revert + log ERROR** (AGENTS.md §5).

## §5 Hedef Mimari — Sıfırdan Kurulum Planı

### §5.1 Modül Sınırı (Parnas'72 + Modular Monolith'24)
- Her domain (`music` · `social` · `podcast` · `radio` · `ai` · `video` · `studio` · `cms` · `i18n`
  — ADR-072..079 şemaları) = tek modül: `namespace` + klasör + **public arayüz** + private iç.
- Modüller arası doğrudan sınıf/`new` çağrısı **yasak**; yalnız arayüz (interface) veya PSR-14
  olayı ile iletişim (ADR-086).
- Modül → tablo sahipliği: bir tabloyu **tek modül** yazar; okuma paylaşımlı, yazma münhasır.

### §5.2 Altıgen Uyum Açığı (kapatılacak)
- Mevcut durum: **⚠️ VERIFICATION REQUIRED** — servis→repository çağrısı oranı diskte
  ölçülmüş değil (kod keşfi yapılmadan "çoğunluk" iddiası yazılmaz); Faz 0 ölçümü ister.
- Hedef: domain servisine yalnız **port** (interface) enjekte edilir; PDO bir adapter'dır.
  Erişim: PSR-11 container ile binding. Kapsam: yeni kod için zorunlu; mevcut kod için kademeli.

### §5.3 Veri & Senkron
- Tek DB içi işlem: tek transaction (doğal atomiclik).
- **Cross-DB yazma**: outbox tablosu + WAL (ADR-081) zorunlu; ikili (dual) yazma yasak.
- Okuma/yazma ayrımı: yalnızca sorgu düzeyinde (read-only repository metotları); ayrı okuma
  DB'si/event-store **kurulmaz** (gereksiz karmaşıklık — CQRS post-mortem dersi).
- Migration: sıralı, geri alınabilir, ADR-014 stratejisiyle.

### §5.4 Frontend
- Vanilla JS ES6+ (ADR-001 frozen), framework yok; ITCSS 11 katman + `--cm-*` token tek kaynak;
  ViewModes tek yükleme yolu `<link id="cm-view-css">` (ADR-093).
- Widget grid kanonik (1024: 12 slot · 1920: 20 slot — kullanıcı kuralı, bağlayıcı).

### §5.5 Güvenlik (kanonik sıra = §4'teki istek yaşam döngüsü)
İstek sırası **tek kaynaktan** okunur: §4 (Origin/CSRF → rate-limit → CSP → auth → …).
Kod tarafı savunma katmanları: PDO prepared → input validator → CSRF (`csrf_token`) → output
escape → yetki (RBAC, ADR-056) → hata gizleme. Rate-limit'in auth öncesi/sonrası nihai sırası
**security-engineer kararıyla** sabitlenecek (review şartı — bu planda iki farklı sıra
taşınmayacak). Tek katmana güvenmek yasak.

### §5.6 Gözlemlenebilirlik & Teslim (K012/K013)
- Log: stdout, JSON, `[RECTED]` redaction; CI: GitHub Actions + GitLeaks (`.github/workflows/`
  `ci.yml`, `secret-scan.yml`).
- Uzak çağrılara (3. parti API, CDN): timeout + retry sınırlı + fallback zorunlu
  (düşen ağ saçmaları — Deutsch/Gosling'94).

## §6 Uygulama Yol Haritası (fazlar — onay kapıları ayrı)

| Faz | İş | Kapı |
|---|---|---|
| 1 | Modül sınırlarının kod tarafında zorlanması (namespace/kural denetimi, CI check) | Mimari onay |
| 2 | Domain servislerine port/adaptor enjeksiyonu (yeni kod) | — |
| 3 | Cross-DB akışların outbox denetimi (dual-write taraması) | Data Engineer |
| 4 | PSR-14 production wiring (ADR-086 PLANNED → IMPLEMENTED) | DevOps |
| 5 | OpenAPI + CQRS-okuma sözleşmesi (ADR-084 PLANNED kısmı) | Backend |

## §7 Riskler

| Risk | Etki | Azaltma |
|---|---|---|
| Vault'ta "18 BCNF DB" iddiası ile gerçek şema sayısı farkı | Yanlış kapasite planı | Data Engineer doğrulaması gerekli ⚠️ VERIFICATION REQUIRED |
| `shared/` içeriği yalnız §25.2 alıntılarıyla biliniyor | Port tasarımı körlükle yapılabilir | Uygulama öncesi `shared/src/` keşfi zorunlu |
| Modül sınırı kuralı mevcut kodu kırabilir | Build/regresyon | Kademeli: uyarı → hata → engel |
| ADR-083/087/088/091 dosyasız (brain-only) | Plan-ADR senkron boşluğu | Vault Steward'a raporlandı (mevcut borç, index §6 notu) |

## §8 Kararlar (bağlayıcı değil — ADR önerisi)

1. Modül sınırı = gizlenecek karar (Parnas'72) — bir domain'in PDO/şema detayını başka domain göremez.
2. Tek deploy + zorlanabilir modül sınırları (Modular Monolith) — mikroservis erken değil.
3. Cross-DB yazmada outbox zorunlu; CQRS yalnız okuma sorgusu düzeyinde.
4. Uzak çağrılara timeout+fallback; tek katman güvenlik asla yeter sayılmaz.

> Bu §8 kararları taslaktır; `.ai/.decisions/accepted/` ADR'sine dönüşümü **Vault Steward +
> kullanıcı onayı** ister (Human Approval Gate).

---
*Version 1.0.0 — 2026-10-09 — Mode: Red Team · Human Mode · Truth Mode*
*Dayanak rapor: [[reports/2026-10-09-50-yillik-mimari-ilkeler-arastirmasi]]*
