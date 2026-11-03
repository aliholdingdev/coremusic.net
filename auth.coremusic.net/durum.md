---
title: "auth.coremusic.net — Oturum Durum & Devir Dosyası (DURUM.md)"
type: state
category: session-state
version: 1.0.0
status: active
authority: "SSOT: auth.coremusic.net/durum.md"
updated: 2026-11-01
---

# DURUM.md — auth.coremusic.net (Tek Sayfalık Hafıza)

> **Kural (PDF Standardı):** Kimlik doğrulama panelinin tek sayfalık hafızasıdır. AI bir adımı bitirince buraya yazar; yeni sohbet yalnız bu dosyayı okuyup başlar. Güvenlik ve auth kodlarını tek sohbette biriktirmek token limitini tüketir.

---

**İş:** auth.coremusic.net — Kimlik Yönetimi, SSO & Güvenlik Paneli  
**Durum:** Hexagonal katmanlar (ADR-043), CSRF token entegrasyonu ve 7 sayfa akışı tamamlandı; OAuthPostHandler rota kararı ve SSO çapraz kontrolleri bekliyor.  
**Bitenler:**  
✓ Backend Adım 08-11 auth entegrasyonu (CSRF token + dropdown rol filtreleme) (`1e806d1`, `f3a6cc9`).  
✓ 7 sayfa akışı (login, register, forgot, reset, gender, logout) hexagonal katmanlarla kuruldu (ADR-043).  
✓ Auth testleri tek ağaçta konsolide edildi (227 tracked test).  
✓ Rate-limit pepper fallback guard + session save-path paritesi sağlandı (`d3d5c4e`).  
✓ `AutoRedirectHandlerTest` yazıldı ve yeşile alındı.  
✓ `ClientAuthPagesContractTest` eklendi; 7 istemci auth sayfası (login, register, forgot, reset, gender, logout) ve auth rotaları doğrulandı (156 test, 369 assertions %100 yeşil).  
✓ `CLAUDE.md §10` test tablosu güncellendi (diskteki Controller/Service/Handler/Repository/Security testleri tabloya işlendi · 156 test %100 yeşil).  
**Kaldığım yer:** `auth.coremusic.net/include/` ve `handler/OAuthPostHandler.php`.  
**Sırada:** `OAuthPostHandler.php` rotasının onaylanması ve home/api panelleriyle SSO/RBAC çapraz kontrolü.  
**Kararlar:**  
- Güvenlik: Argon2id şifreleme, katı CSP, HttpOnly+SameSite=Strict cookie.  
- UI & Tema: Kesinlikle YALNIZCA LIGHT MODE (Dark Mode kodu yazmak YASAKTIR).  
- Oturum: Session fixation koruması ve yetki bazlı menü sanitizasyonu zorunludur.  
- Karpathy İlkeleri (`KURAL-11`): Think Before Coding, Simplicity First, Surgical Changes, Goal-Driven Execution.  
- Git: `git commit` subagent tarafından atılmaz; kullanıcı/orkestratör yetkisindedir.  
**Açık:** `handler/OAuthPostHandler.php` için nihai rota kararı bekleniyor.

---

### KOMUT · İŞİN SONUNDA CLAUDE'A / AGENT'A YAPIŞTIR
```text
Bu sohbette yaptıklarımızı yukarıdaki DURUM kalıbına göre güncelle. Kısa yaz; bir sonraki sohbet yalnız bu dosyayı okuyup kaldığımız yerden devam edebilsin.
```

### KOMUT · YENİ SOHBETİN İLK MESAJI
```text
Ekteki DURUM dosyasını oku. Bana önce tek cümleyle nerede kaldığımızı söyle, sonra "Sırada" yazan adımdan devam et.
```
