---
type: architecture
category: layer
title: "K008 — Servisler"
date: 2026-10-09
status: active
version: 2.0.1
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
## §7 Makro Katman Karşılığı
K008-servisler → Makro **K3** (bağlayıcı gruplama, 2026-10-10) · Kök eşleme: [[../00-master-index]] (Makro Eşleme bölümü) · makro özet: [[../katmanli-mimari-k0-k4/index]]

- K3 içinde K008 = **domain servisleri çekirdeği**: controller'ın (K010) çağırdığı, DTO ile veri taşıyan, PDO/HTTP bilmeyen iş kuralı katmanıdır (makro özet §4 K3 satırı).
- Makro komşular: alt = **K2** Çekirdek Servisler & Middleware (mikro kenar: `K008→K005` servis → veri — [[../katman-baglilik-matrisi]] §2) · üst = **K4** Sunum (mikro zincir: `K009→K010→K008`; K009 K008'i doğrudan import etmez — matris §2).
- K3 içi kardeşler: K004 (`ai` modülü, wiring PLANNED) · K015 (medya) · K010 (çağıran controller) — aralarında iç içe implementasyon importu YASAK, yalnız sözleşme üzerinden kenar (makro özet §2.4; §6.2-4/5).

## §8 Arayüz (Girdi/Çıktı & API)
- **Girdi (üstten):** K010 controller → domain servisi çağrısı — matris §2: "K010 uygulama → K008 servisler · plan §4 (controller → service)". Servis DTO alır; DTO girer/çıkar, PDO/HTTP bilmez (ADR-039, §2.4).
- **Çıktı (aşağıya):** K005 veri katmanı üzerinden port/adaptor çağrısı — matris §2: "K008 servisler → K005 veri · K004 yapay zeka · K015 medya · plan §5.1-§5.2 (port/adaptor)".
- **Diskte kanıtlı somut varlıklar (§5, 2026-10-09 ölçümü):**

| Varlık | Gerçek yol | Rol |
|---|---|---|
| `AuthService` | `auth.coremusic.net/include/Service/AuthService.php` | Domain servisi — oturum/anahtar yönetimi |
| `SessionManager` | `auth.coremusic.net/include/Service/SessionManager.php` | Domain servisi — oturum yönetimi |
| `SessionManagerTest` | `auth.coremusic.net/tests/Unit/Service/SessionManagerTest.php` | Servis birim testi |

- **İzinli/yasaklı çağrı özeti:** servis → veri yalnız port (interface) üzerinden (plan §5.1-§5.2) · modüller arası doğrudan sınıf/`new` çağrısı YASAK (§2.2) · modüller arası iletişim yalnız interface veya PSR-14 olayı (ADR-086 — **wiring PLANNED**, makro özet §6.2-4/5) · servisin K4'e (sunum) doğrudan bağımlılığı YASAK (Dependency Rule).
- `AuthService`/`SessionManager` method imzaları bu görevde okunmadı → **imza UNKNOWN** (uydurma yasak; makro özet §7-9/10).

## §9 Hata Modları ve Güvenlik Sınırı
- **Failure modes (katman sınırında):**
  1. **7-service vs 9-domain eşlemesi** — ADR-039'un 7'si ile plan §5.1'in 9 domain'i örtüşmeyebilir → kayıp domain riski, UNKNOWN (§6; makro özet §5-FM2).
  2. **PSR-14 wiring PLANNED** — event akışı implemente değil; servis→servis gevşek bağlılığı henüz yok (ADR-086; makro özet §5-FM3).
  3. **Modül sınırı kod tarafında zorlanmıyor** (§5 PLANNED — plan §6 Faz 1) → sınır ihlali sessiz kalabilir.
  4. Servis → repository oranı ölçülmüş değil (§6) → servislerin veriye dolaylı erim bilinmiyor.
- **İzolasyon:** domain servisi hatası diğer servislere yayılmaz; hata K008 sınırında yakalanır ve DTO/hata modeliyle üstte K010'a taşınır (makro özet §5 K3). Uzak çağırı timeout+retry+fallback kuralı K012'nin cross-cutting kuralıdır (makro özet §5; [[../K012-izleme/index]] §2.4).
- **Güvenlik sınırı (kim neyi doğrular):** yetkilendirme K008'de uygulanmaz — istek K008'e varmadan önce K2 kanonik sırasından geçer: OriginCheck+CSRF → RateLimit (APCu) → CSP nonce → Auth → PageRouter (makro özet [[../katmanli-mimari-k0-k4/k2-cekirdek-servisler-middleware]] §3.2). K008'in kendi sınırı: iş kuralı doğrulaması + bypass yasağı (controller→PDO ihlali → derhal revert + log ERROR, matris §3).

## §10 Bilinmeyenler (⚠️ / UNKNOWN)

| Bulgu | Durum |
|---|---|
| 7-service vs 9-domain eşlemesi (ADR-039 ↔ plan §5.1) | ⚠️ UNKNOWN (§6; makro özet §7-10) |
| `AuthService` / `SessionManager` method imzaları | UNKNOWN — okunmadı |
| 7 servisin tam ad listesi | UNKNOWN — dosyalarda teyit edilmedi (makro özet §7-9) |
| PSR-14 event wiring (ADR-086) | PLANNED — kod kanıtı yok |
| Servis → repository çağrısı oranı (plan §5.2 Faz 0 ölçümü) | ⚠️ VERIFICATION REQUIRED (§6) |
| Modül sınırının kod tarafında zorlanması (plan §6 Faz 1) | PLANNED — denetim mekanizması yok |
