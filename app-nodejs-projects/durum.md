---
title: "app-nodejs-projects — Oturum Durum & Devir Dosyası (DURUM.md)"
type: state
category: session-state
version: 1.0.0
status: active
authority: "SSOT: app-nodejs-projects/durum.md"
updated: 2026-11-01
---

# DURUM.md — app-nodejs-projects (Tek Sayfalık Hafıza)

> **Kural (PDF Standardı):** Node.js ve NovaSearchEngine projelerinin tek sayfalık hafızasıdır. AI bir adımı bitirince buraya yazar; yeni sohbet yalnız bu dosyayı okuyup başlar. Monorepo paketlerini tek sohbette biriktirmek limiti tüketir.

---

**İş:** app-nodejs-projects — Turbo Monorepo, NovaSearchEngine & Node.js Servisleri  
**Durum:** Turbo monorepo pnpm lock yapısı ve NovaSearchEngine sqlite test paketi tamamlandı; %75 coverage kapısı ve MySQL canlı test doğrulaması bekliyor.  
**Bitenler:**  
✓ Turbo monorepo + pnpm workspace + strict TypeScript kuralları kuruldu.  
✓ NovaSearchEngine sqlite test suite geçti.  
✓ NovaSearchEngine test coverage ölçümü %60.30 olarak kaydedildi.  
✓ `apps/import-map-importer` ayrı workspace kuralı mühürlendi.  
**Kaldığım yer:** `app-nodejs-projects/nova-search-engine/`.  
**Sırada:** `turbo typecheck && turbo run lint` çalıştırılması ve coverage hedefinin %75'e çıkarılması.  
**Kararlar:**  
- Dil & Derleme: TypeScript strict:true, no `any`, async/await zorunludur.  
- Paket Yöneticisi: pnpm (kök lock tek kaynak).  
- Karpathy İlkeleri (`KURAL-11`): Think Before Coding, Simplicity First, Surgical Changes, Goal-Driven Execution.  
- Git: `git commit` subagent tarafından atılmaz; kullanıcı/orkestratör yetkisindedir.  
**Açık:** Yok (Monorepo yapısı stabil).

---

### KOMUT · İŞİN SONUNDA CLAUDE'A / AGENT'A YAPIŞTIR
```text
Bu sohbette yaptıklarımızı yukarıdaki DURUM kalıbına göre güncelle. Kısa yaz; bir sonraki sohbet yalnız bu dosyayı okuyup kaldığımız yerden devam edebilsin.
```

### KOMUT · YENİ SOHBETİN İLK MESAJI
```text
Ekteki DURUM dosyasını oku. Bana önce tek cümleyle nerede kaldığımızı söyle, sonra "Sırada" yazan adımdan devam et.
```
