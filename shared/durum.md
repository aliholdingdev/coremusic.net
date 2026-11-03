---
title: "shared (Ortak Çekirdek) — Oturum Durum & Devir Dosyası (DURUM.md)"
type: state
category: session-state
version: 1.0.0
status: active
authority: "SSOT: shared/durum.md"
updated: 2026-11-01
---

# DURUM.md — shared (Ortak Çekirdek) (Tek Sayfalık Hafıza)

> **Kural (PDF Standardı):** Ortak çekirdeğin tek sayfalık hafızasıdır. AI bir adımı bitirince buraya yazar; yeni sohbet yalnız bu dosyayı okuyup başlar. Tüm çekirdek sınıflarını tek sohbette taramak token limitini tüketir.

---

**İş:** shared — Kurumsal Çekirdek Kütüphaneleri & Güvenlik Altyapısı  
**Durum:** Temel sözleşmeler (contracts), PageRouter zinciri ve fail-closed test paketi tamamlandı; coverage sürücüsü ve tablo duplikasyon konsolidasyonu sırada.  
**Bitenler:**  
✓ SEC-01..05 güvenlik standartları + session save-path paritesi + RBAC claims (`d3d5c4e`).  
✓ DB erişimi sertleştirme + db-access-guard CI kapısı (`2513de8`).  
✓ Interface→Contract refactoring (6 dizin, 20 tracked sınıf).  
✓ Alt PRD'ler: 9/9 alt PRD diskte doğrulandı (`PageRouter`, `Middleware`, `Api`, `Security`, `Database`, `OAuth`, `Events`, `Cache`, `Session`).  
✓ `RateLimiterUnavailableException` + `UuidV7Test` + fail-closed test paketi.  
✓ `ViewModeManagerTest` (8 test) ile view-mode oturum/rota hiyerarşisi doğrulandı.  
**Kaldığım yer:** `shared/src/` ve `shared/tests/`.  
**Sırada:** `ViewModeManager` birim testi yazımı ve `coremusic_api` / `coremusic_auth` tablo tekilleştirme planı (ADR-112).  
**Kararlar:**  
- Dil & Standart: PHP 8.4 `declare(strict_types=1);`, raw PDO (ORM yasak).  
- Fail-Closed İlkesi: Güvenlik, cache ve rate limiter arızalarında sistem açık bırakılmaz (fail-closed davranır).  
- Router: Tüm subdomain'ler ortak `PageRouter` zincirini kullanır.  
- Karpathy İlkeleri (`KURAL-11`): Think Before Coding, Simplicity First, Surgical Changes, Goal-Driven Execution.  
- Git: `git commit` subagent tarafından atılmaz; kullanıcı/orkestratör yetkisindedir.  
**Açık:** Yok (Tüm çekirdek testleri yeşil).

---

### KOMUT · İŞİN SONUNDA CLAUDE'A / AGENT'A YAPIŞTIR
```text
Bu sohbette yaptıklarımızı yukarıdaki DURUM kalıbına göre güncelle. Kısa yaz; bir sonraki sohbet yalnız bu dosyayı okuyup kaldığımız yerden devam edebilsin.
```

### KOMUT · YENİ SOHBETİN İLK MESAJI
```text
Ekteki DURUM dosyasını oku. Bana önce tek cümleyle nerede kaldığımızı söyle, sonra "Sırada" yazan adımdan devam et.
```
