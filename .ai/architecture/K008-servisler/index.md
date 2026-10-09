---
type: architecture
category: layer
title: "K008 — Servisler"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT
---

# K008 — Servisler

## §1 Kimlik
- Katman: K008 · Alan: **A2** (K8-K9 — Routing/Backend).
- Kapsam: domain servisleri — 7-service platform, modül sınırları, tablo sahipliği.

## §2 Sorumluluk
1. 7-service platform mimarisi (ADR-039).
2. Modül sınırı = gizlenecek karar (plan §5.1): namespace + klasör + public arayüz + private iç; modüller arası doğrudan sınıf/`new` çağrısı YASAK.
3. Modüller arası iletişim yalnız interface veya PSR-14 olayı (ADR-086).
4. Domain servisi DTO girer/çıkar; PDO/HTTP bilmez — port/adaptor (plan §4-6, §5.2).
5. Modül → tablo sahipliği: yazma münhasır, okuma paylaşımlı (plan §5.1).

## §3 Bağlantılar (Dependency Rule)
- **Alt:** K005 (port/adaptor üzerinden veri) · K004 (`ai` modülü) · K015 (medya).
- **Üst (çağıran):** K010 (controller çağırır) → K009.

## §4 ADR Bağlantıları
| ADR | Başlık |
|---|---|
| ADR-039 | 7-Service Platform Architecture |
| ADR-086 | Event Driven Architecture (PSR-14 — wiring PLANNED) |
| ADR-085 | Shared Library Hybrid |
| ADR-072…ADR-079 | Domain şemaları (modül→tablo eşlemesi) |

## §5 Durum
**IMPLEMENTED (kısmi)** — kanıt (2026-10-09): `auth.coremusic.net/include/Service/{AuthService,SessionManager}.php` + `auth.coremusic.net/tests/Unit/Service/SessionManagerTest.php`. **PLANNED**: modül sınırının kod tarafında zorlanması (plan §6 Faz 1).

## §6 Risk / Not
- ⚠️ VERIFICATION REQUIRED: servis→repository çağrısı oranı diskte ölçülmedi (plan §5.2 Faz 0 ölçümü ister).
- ADR-039'un "7 service" sayısı ile plan §5.1'deki 9 domain listesi (`music·social·podcast·radio·ai·video·studio·cms·i18n`) farklı ölçekte; eşleme **UNKNOWN**.
