---
title: "api.coremusic.net — Oturum Durum & Devir Dosyası (DURUM.md)"
type: state
category: session-state
version: 1.0.0
status: active
authority: "SSOT: api.coremusic.net/durum.md"
updated: 2026-11-01
---

# DURUM.md — api.coremusic.net (Tek Sayfalık Hafıza)

> **Kural (PDF Standardı):** API geliştirmenin tek sayfalık hafızasıdır. AI bir adımı bitirince buraya yazar; yeni sohbet yalnız bu dosyayı okuyup başlar. Tüm API şemalarını ve dokümanlarını tek sohbette biriktirmek limiti tüketir.

---

**İş:** api.coremusic.net — REST & Streaming API Geliştirmesi  
**Durum:** Billing v1 sözleşmeleri (plan/invoices), OpenAPI şemaları ve testler hazır; canlı DB deploy ve api_keys tablosu konsolidasyonu onay bekliyor.  
**Bitenler:**  
✓ Billing contract-first 2 uç (`GET /api/v1/billing/plan`, `/invoices`) + 5 dosya (Controller, Service, Repository) + 10 test (`composer test` 133/476 yeşil).  
✓ SEC-01 rate-limit IP bucket zorunluluğu + SEC-03 public-route tanımları (`d3d5c4e`).  
✓ DTO kütüphanesi + `PaginationResponse` + `RouteTable::fromArray` deseni.  
✓ V1 default + V2 paralel rota mimarisi (`{version}` parametresi ile tek route tablosu).  
✓ TASK-12: API V1 & V2 Paralel Route Test Paritesi (`RouteConfigTest` + `VersionResolverTest`, 141 test / 696 assertion %100 yeşil).  
✓ TASK-13: Repository Standart Şablon İzolasyonu (`I*` interface + `final class` standardı, mevcut kod donduruldu).  
✓ Karpathy Davranışsal İlkeleri (`KURAL-11`): Think Before Coding, Simplicity First, Surgical Changes, Goal-Driven Execution.  
**Kaldığım yer:** `api.coremusic.net/include/Controller/BillingController.php` ve `openapi.yaml`.  
**Sırada:** `coremusic_billing` veritabanı canlı deploy onayı ve `api_keys` tablosunun `coremusic_auth` ile konsolidasyonu.  
**Kararlar:**  
- API sürümleme: Tek rota tablosu içinde `{version}` segmanı; ayrık V2 tablosu açmak YASAK.  
- Veritabanı: Parametreli raw PDO, `SELECT *` yasak, DDL doğrudan canlıda çalıştırılamaz.  
- Rol Sözlüğü: Kural dışı rol (`requiredRole`) uydurulamaz; RBAC sözlüğü onaylanmadan 403 üretecek filtre konulamaz.  
- Git: `git commit` subagent tarafından atılmaz; kullanıcı/orkestratör yetkisindedir.  
**Açık:** Canlı veritabanında `coremusic_billing` DB'si henüz oluşturulmadı (onay bekleniyor).

---

### KOMUT · İŞİN SONUNDA CLAUDE'A / AGENT'A YAPIŞTIR
```text
Bu sohbette yaptıklarımızı yukarıdaki DURUM kalıbına göre güncelle. Kısa yaz; bir sonraki sohbet yalnız bu dosyayı okuyup kaldığımız yerden devam edebilsin.
```

### KOMUT · YENİ SOHBETİN İLK MESAJI
```text
Ekteki DURUM dosyasını oku. Bana önce tek cümleyle nerede kaldığımızı söyle, sonra "Sırada" yazan adımdan devam et.
```
