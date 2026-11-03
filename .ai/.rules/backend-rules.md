---
title: "CoreMusic — Backend Kuralları (PHP 8.4 · PSR-12 · Raw PDO)"
type: guide
category: governance
date: 2026-10-22
updated: 2026-11-01
version: 2.0.0
status: active
authority: "Derived — SSOT: .ai/CLAUDE.md §6A-§6B, §21 · .ai/.rules/kurallar.md · .ai/.rules/karar-kurallari.md (ADR-113, ADR-114)"
docType: claude
---

# CoreMusic — Backend Kuralları (Backend Rules — PHP)

**docType:** claude · **Klasör:** `.ai/.rules/` · **Sorumlu:** backend-architect (birincil) + security-engineer (ikincil)

**Zorunlu Bağlantılar:** [[RULES.md]] · [[kurallar.md]] · [[karar-kurallari.md]] · [[architecture-rules.md]] · [[security-rules.md]] · [[database-rules.md]] · [[.ai/CLAUDE.md]]

> **eli10:** PHP tarafında yazdığın her satır: declare(strict_types=1), raw PDO prepared, tek route tablosu (ADR-113), I* repository standardı (ADR-114); framework/ORM/eval/SELECT * kesinlikle yok.

---

## 1. Amaç

PHP 8.4 backend geliştirme standartlarını; katmanlı mimari, route yönetimi (ADR-113), repository standardı (ADR-114), veri erişimi, hata yönetimi ve güvenlik kısıtlamaları halinde vault SSOT'larından toplar. Backend kalite hedefi: **%100 strict types, PSR-12, sıfır ORM, sıfır SELECT \*** (`.ai/AGENTS.md` §16).

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `*.php` (Controller, Service, Repository, Middleware, DTO, UseCase) | Şema/BCNF tasarımı → [[database-rules.md]] |
| `shared/src/**` (PSR-4, `coremusic/shared` paketi) | Secret/CSRF/CSP nonce → [[security-rules.md]] |
| Route tanımları (`config/routes.php`) ve `VersionResolver` | Mimari katman tanımı → [[architecture-rules.md]] |
| Sayısal oran eşleştirmeleri (`HL-06`) ve RBAC rolleri (`HL-09`) | UI ve CSS kuralları → [[frontend-rules.md]] |

---

## 3. Mimari

### 3.1 Kodlama & Dil Standartları

| Kural | Standart Değer | Açıklama / Yaptırım |
|---|---|---|
| **Dil Sürümü** | **PHP 8.4+** | Her PHP dosyasının en başında `declare(strict_types=1);` zorunludur. |
| **Kod Stili** | **PSR-12** | Tip bildirimleri tam, dönüş tipleri zorunlu. |
| **Bağımlılık Yönetimi** | **PHP-DI & PSR-11** | Container üzerinden constructor injection; `new` ile servis üretimi yasaktır. |
| **Veri Transfer Nesneleri** | **DTO (Immutable / Readonly)** | Tip güvenli DTO'lar; framework bağımsız. |
| **İstisnalar** | **Typed Exceptions** | `shared/src/Exception/` altındaki özel istisnalar; genel `catch (\Exception)` yasaktır. |

### 3.2 Veri Erişimi & Repository Standartları (`HG-09` & ADR-114)

| Kural | Durum | Standart / İhlal |
|---|---|---|
| **ORM Kullanımı** | **KESİNLİKLE YASAK** | Raw PDO kullanılır (ADR-002). Eloquent, Doctrine vb. yasaktır. |
| **Parametreli Sorgular** | **ZORUNLU** | Tüm SQL sorguları PDO prepared statement (`prepare` + `execute`) ile çalıştırılır. |
| **Kolon Bildirimi** | **`SELECT *` YASAK** | İhtiyaç duyulan kolonlar tek tek açıkça yazılır (`SELECT id, title, artist_id FROM ...`). |
| **N+1 Sorguları** | **YASAK** | Çoklu kayıt okumalarında döngü içinde sorgu yasaktır; JOIN veya `WHERE IN (...)` ile çözülür. |
| **Repository Mimarisi** | **ADR-114 Standardı** | Yeni kodlarda `I[Name]Repository` arayüzü + concrete `final class [Name]Repository` + `IDatabaseRegistry`/PDO constructor injection. Taban şablon: `api.coremusic.net/include/Repository/`. |
| **Soft Delete Sözleşmesi** | **ADR-112 Standardı** | Silme kontrollerinde veritabanı dump sözleşmesi esas alınır (`WHERE is_deleted = 0`). |

### 3.3 API Sürümleme & Tek Rota Tablosu (ADR-113)

- **Tek Rota Tablosu:** Yeni endpoint'ler `config/routes.php` içerisinde tek route kaydında `/api/{version}/...` placeholder'ı ile tanımlanır. Ayrı bir V2 rota tablosu oluşturmak YASAKTIR.
- **Sürüm Çözümleme (`VersionResolver`):**
  1. Default sürüm `v1`'dir.
  2. `v2` seçimi URL path (`/api/v2/...`) veya HTTP Accept header (`application/vnd.coremusic.v2+json`) ile yapılır.
  3. Sürüm davranış farkı yalnız `V2Container` override'ı (V1Container'dan türeyen) ile enjekte edilir.
  4. Middleware pipeline sırası sürümden bağımsız olarak ortaktır.

### 3.4 Katman İletişimi & Yasaklar

```text
SPA → ApiClient → HTTP → Gateway → Middleware → Controller → UseCase → Domain → Repository → MySQL
```

1. **Controller'da İş Mantığı Yasaktır:** Controller yalnız request ayrıştırma, DTO doğrulaması ve response formatlamasından sorumludur (Presentation katmanı).
2. **Servisler Arası Doğrudan Çağrı Yasaktır:** K8 servisleri birbirini doğrudan çağıramaz; PSR-14 Event Bus kullanılır (ADR-086).
3. **OpenAPI Sözleşmesi İhlal Edilemez:** Önce OpenAPI spec tanımlanır, ardından kod yazılır.
4. **Sayısal Oran Hassasiyeti (`HL-06`):** Ses seviyesi gibi oran eşleşmeleri yuvarlanmış yüzdeyle değil, ham float oranla (`$volRatio <= 0.02 / 0.33 / 0.66`) yapılır.
5. **Standart RBAC Rolleri (`HL-09`):** Sözlük dışı uydurma rol (`requiredRole`) yazılamaz; uydurulan roller 403 hatasına sebep olur.

---

## 4. Kurallar

| # | Kural | İhlal Sonucu |
|---|---|---|
| 1 | `declare(strict_types=1);` ve PSR-12 eksikliği | Kod reddedilir |
| 2 | ORM, `SELECT *`, `eval()`, `unserialize(user_input)` kullanımı | Derhal revert |
| 3 | Ayrı V2 route tablosu açılması (ADR-113 ihlali) | Rota reddedilir |
| 4 | Repository arayüz ve DI standardına uymamak (ADR-114 ihlali) | Kod reddedilir |
| 5 | Middleware sırasını değiştirme veya bypass etme | Sistem durdurulur (`HG-07`) |
| 6 | Domain boundary: `*.php` backend agent yetkisindedir | Layer violation → revert |
| 7 | Subagent commit çalıştırması (`HL-03`) | İhlal; orkestratör yetkisindedir |

---

## 5. Workflow

```text
Backend Görevi (Endpoint / Servis / Repository / Middleware)
  → Vault: @.ai/CLAUDE.md §6A-§6B + karar-kurallari.md (ADR-113, ADR-114)
  → Skill: php-backend-standards
  → OpenAPI Spec → DTO Tanımı → UseCase → Repository Arayüzü & Uygulaması
  → Tek Rota Tanımı: /api/{version}/... (routes.php)
  → PHPUnit Testleri (shared/tests/) & PHPStan Denetimi
  → durum.md güncelleme + log.md append
```

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---|---|
| 1 | Frontmatter 7 alan + docType | Mevcut |
| 2 | strict_types ve PSR-12 zorunluluğu | Açıkça belirtilmiş |
| 3 | Veri erişim standartları | Raw PDO · No ORM · No SELECT * · Prepared |
| 4 | ADR-113 tek route tablosu | `/api/{version}/` ve V2Container vurgulandı |
| 5 | ADR-114 repository standardı | `I*` interface + `final class` + raw PDO |
| 6 | Hata listesi uyumu | `HL-03`, `HL-06`, `HL-09` kuralları işlendi |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|---|---|---|
| Karar Kuralları | [[karar-kurallari.md]] | ADR-113 (API sürümleme) & ADR-114 (Repository) |
| Temel Direktifler | [[kurallar.md]] | KURAL-04 backend guardrail'ları |
| Hata Listesi | [[hata-listesi.md]] | HL-03, HL-06, HL-09 tuzakları |
| Master Kurallar | [[RULES.md]] | Genel aktivite kuralları |
| Shared Paketi | `shared/src/` | PSR-4 paylaşılan sınıflar |
| Route Konfigürasyonu | `api.coremusic.net/config/routes.php` | Tek route tablosu SSOT |

---

**Authority:** Bayram Ali / Kurucu & Mimari Otorite  
**Last Updated:** 2026-11-01 · **Version:** 2.0.0
