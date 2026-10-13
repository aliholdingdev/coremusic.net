---
node_id: K009-security/authorization/bilesenler
node_type: module
status: verify-required
parent_id: K009-security/authorization
children: []
depends_on:
  - K009-security/authorization
evidence:
  - "Dosya haritası: shared/src/Middleware (3) + shared/src/Api/Middleware (1) okundu"
validation:
  - "2026-10-12 — haritadaki tüm dosyalar glob ile varlık doğrulandı"
open_questions:
  - "Rol matrisinin tek kaynağı (config/domain.php vs route meta) → ADR-097 K4"
  - "Test dosyalarının adları → UNKNOWN (okunmadı)"
group: mimari
---

# authorization-bilesenler.md — Kademe-3 Planı

## §1 Kademe-3 Bileşen Listesi

| # | Bileşen | Dosya | Katman | Görev |
|---|---|---|---|---|
| B-01 | WebYetkiBileşeni | shared/src/Middleware/PermissionMiddleware.php | Infrastructure/Pipeline | role + permission |
| B-02 | KaynakPolitikası | shared/src/Middleware/OriginCheckMiddleware.php | Infrastructure/Pipeline | origin allowlist |
| B-03 | CORS-Başlık | shared/src/Middleware/CorsMiddleware.php | Infrastructure/Pipeline | başlık yayımı |
| B-04 | ApiYetkiBileşeni | shared/src/Api/Middleware/AuthorizationMiddleware.php | Infrastructure/Api | 401/403 kararı |
| B-05 | RolKaynağı | route meta (_route_meta) + _auth | Application | karar girdisi |
| B-06 | OlayKaydı | Log/LoggerFactory::securityEvent | Infrastructure/Log | authz_denied, origin_blocked |

## §2 Pipeline Sırası (frozen — ADR-010/022)

~~~text
#1 OriginCheck  →  #2 Cors  →  ...  →  #9 Permission  →  #10 Validation
   (satır 13)        (satır 13)            (satır 13)
~~~

Kural: sıra DEĞİŞTİRİLMEZ; sıra değişikliği ayrı ADR gerektirir.
Sıra ihlali senaryosu: Permission → OriginCheck tersyüz edilirse CORS
reddi RO BİRÇAK istekte geç kalır.

## §3 API Pipeline Sırası (ADR-084)

~~~text
RouteTable (404/405) → public rota kontrolü → kimlik (_auth_user)
  → requiredRole → requiredPermission → handler
~~~

## §4 Kademe-3 → Kademe-4 Dosya Haritası

~~~text
src/Middleware/            (web yüzeyi)
  PermissionMiddleware.php
  OriginCheckMiddleware.php
  CorsMiddleware.php
src/Api/Middleware/        (API yüzeyi)
  AuthorizationMiddleware.php
  AuthenticationMiddleware.php  ← PUBLIC_ROUTE_PATTERNS kaynağı (103)
src/Log/
  LoggerFactory.php        ← securityEvent ortak noktası
tests/Middleware/          (test yüzeyi — adlar UNKNOWN)
~~~

## §5 Bağımlılık Grafiği

| Kaynak | Hedef | Tür | Not |
|---|---|---|---|
| PermissionMiddleware | $request['_auth'] | veri | AuthenticationMiddleware üretir |
| PermissionMiddleware | _route_meta | veri | RouteTable üretir |
| CorsMiddleware | OriginCheck | sıra | frozen: önce origin |
| AuthorizationMiddleware | AuthenticationMiddleware | statik | PUBLIC_ROUTE_PATTERNS (103) |
| AuthorizationMiddleware | ApiResponse | dönüş | hata gövdesi |
| OriginCheck/Cors | LoggerFactory | log | P5-32 olay kaydı |
| Tümü | Config (corsConfig) | config | allowed_origins, dev_fallback |

## §6 Katman Kuralı

- Bu bileşenler **Infrastructure/Pipeline** katmanındadır; iş kuralı taşımaz
  (rol matrisi veri olarak gelir, türetilmez).
- Domain katmanına bağımlılık YOK → test edilebilirlik yüksek.
- shared/ içine subdomain'e özel yetki gömülmez (shared AGENTS.md §4 Yasak 1).

## §7 Kademe-4 Alt Birimleri (plan)

| Bileşen | Alt birim | İçerik |
|---|---|---|
| B-01 | rolKontrol / permissionKontrol / forbidden | §1 kod blokları |
| B-02 | isOriginAllowed / suffixEsi / devFallback | OriginCheck 63-121 |
| B-03 | isOriginAllowed / preflightResponse | Cors 84-142 |
| B-04 | isPublicRoute / roleKontrol / permissionKontrol | Api 25-110 |

## §8 Test Yüzeyi Matrisi

| Bileşen | Test türü | Senaryo (doğrulama §9-§10) |
|---|---|---|
| B-01 | unit | 34, 35, 42, 43 |
| B-02 | unit | 38, 39, 45, 46 |
| B-03 | unit + header | 45, 20, 22 |
| B-04 | unit | 37, 40, 41, 44 |
| B-05 | entegrasyon | rota meta → karar |
| B-06 | gözlemsel | 28, 29 |

## §9 Tahmini İş Yükü (dalga-3)

1. Test dosyalarını bulma ve okuma (UNKNOWN → PASS kapama).
2. CORP başlığı doğrulaması (kontrol 21).
3. Rol matrisi kaynağının ADR-097 K4'te kilitlenmesi.
4. PermissionMiddleware ile ApiAuthorization arasında ortak soyutlama
   (iki kez aynı in_array mantığı) — DRY adayı, ADR gerektirir.

## §10 Değişiklik Riski

| Dosya | Risk | Gerekçe |
|---|---|---|
| PermissionMiddleware | Yüksek | tüm sayfa rotaları |
| OriginCheck | Yüksek | CORS reddi kırılırsa site çöker |
| Cors | Orta | başlık yalnız |
| ApiAuthorization | Yüksek | tüm API rotaları |

Ortak kural: bu 4 dosyada değişiklik → security-audit workflow (shared AGENTS.md §3).
