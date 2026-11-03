---
title: "admin.coremusic.net — Oturum Durum & Devir Dosyası (DURUM.md)"
type: state
category: session-state
version: 1.2.0
status: active
authority: "SSOT: admin.coremusic.net/durum.md"
updated: 2026-11-02
---

# DURUM.md — admin.coremusic.net (Tek Sayfalık Hafıza)

> **Kural (PDF Standardı):** Sistem yönetim panelinin tek sayfalık hafızasıdır. AI bir adımı bitirince buraya yazar; yeni sohbet yalnız bu dosyayı okuyup başlar. Tüm yönetim paneli kodlarını tek sohbette biriktirmek limiti tüketir.

---

**İş:** admin.coremusic.net — Sistem Yönetim Konsolu, Auth Ekranları & Açık Kristal Cam (Light Theme Glassmorphism) Tasarım Revizyonu  
**Durum:** Admin giriş/kayıt ekranları ve tüm admin yönetim paneli sayfaları (Dashboard, Şarkılar, Şarkı Ekle, Playlistler, Ayarlar), Figma SSOT'una (`.ai/.png/admin-1920/`) tam uygun şekilde Açık Kristal Cam (Light Theme Glassmorphism) mimarisine dönüştürüldü. Koyu mor/siyah opak paneller kaldırıldı; Golden Gate köprüsü ve pembe göl manzarası `rgba(255, 255, 255, 0.12 - 0.18)` saydam cam ve `backdrop-filter: blur(24px)` ile arkadan net görünür kılındı. Tüm metinler kristal saf beyaz (`#FFFFFF`) yapıldı. 35/35 Admin PHPUnit testi %100 yeşil.  
**Bitenler:**  
✓ **Açık Kristal Cam (Light Theme Glassmorphism) Mimarisi:**
  - `a-admin-tokens.css`: `--cm-white: #FFFFFF;` ve `--adm-c-white: #FFFFFF;` eklendi; kart, kenar çubuğu, navbar ve auth panelleri kristal beyaz cam renklerine (`rgba(255, 255, 255, 0.12 - 0.18)`) ve beyaz ince sınırlara (`rgba(255, 255, 255, 0.35)`) geçirildi.
  - `b-admin-core.css`: Varsayılan `body` metin rengi `color: var(--cm-white, #FFFFFF)` olarak mühürlendi.
  - `_admin-shell.css`: Sağ auth dock genişliği Figma 1920 SSOT'a uygun 500px yapıldı (`width: 500px !important`).
  - `p-admin-auth-light.css`: Giriş, kayıt ve şifre sıfırlama kartları kristal beyaz cama dönüştürüldü; pembe cam inputlar (`rgba(242, 0, 208, 0.45)`), saf beyaz başlıklar/etiketler ve pembe ışıltılı butonlar uygulandı.
  - `auth.coremusic.net/pages/admin/register.php`: Figma `Admin - Register.png` SSOT'una birebir uygun sağ dock cam panel mimarisine hizalandı.
  - `p-admin-layout.css` & `p-admin-crud-light.css`: Dashboard, şarkı listesi, şarkı ekleme, çalma listesi ve ayarlar sayfalarındaki tüm KPI kartları, CRUD tabloları, filtre panelleri ve alt bilgi (footer) pill'leri beyaz kristal cam olarak stilize edildi.
  - `admin.coremusic.net/config/constants.php` & `auth.coremusic.net/config/constants.php`: `$withPort` fonksiyonu ile port 81 dinamik URL tespiti ve resim yollarının (`assets.coremusic.net:81`) eksiksiz yüklenmesi sağlandı.
✓ `AdminMarkupContractTest` + ADR-058 markup sözleşmesi koruması.  
✓ db-access-guard CI kapısı entegrasyonu (ADR-002).  
✓ Admin PRD (PRD-ADM-001..007 + Risk §6-1) mühürlendi.  
✓ `/dev-login` rota koruması: `DevLoginGateTest` (6 test) ile kanıtlandı (üretimde 404).  
✓ `AdminRateLimitTest` (2 test) ile rate-limit kanıtlandı (429 Too Many Requests).  
**Kaldığım yer:** Admin ve Auth görsel doğrulamaları Chrome DevTools üzerinde tamamlandı; testler yeşil.  
**Sırada:** ADR-115 Media yönetimi admin ekranlarının planlaması.  
**Kararlar:**  
- Güvenlik: Admin erişiminde MFA ve katı RBAC (`admin` rolü) zorunludur; `dev-login` production ortamına sızamaz.  
- UI & Tema: YALNIZCA LIGHT MODE & KRİSTAL CAM EFEKTİ (Dark Mode tasarımı yoktur/yasaktır).  
- Veritabanı: Parametreli raw PDO, DDL doğrudan çalıştırılamaz.  
- Karpathy İlkeleri (`KURAL-11`): Think Before Coding, Simplicity First, Surgical Changes, Goal-Driven Execution.  
- Git: `git commit` subagent tarafından atılmaz; kullanıcı/orkestratör yetkisindedir.  
**Açık:** Yok (35 Admin, 156 Auth, 103 Home PHPUnit testleri %100 yeşil).

---

### KOMUT · İŞİN SONUNDA CLAUDE'A / AGENT'A YAPIŞTIR
```text
Bu sohbette yaptıklarımızı yukarıdaki DURUM kalıbına göre güncelle. Kısa yaz; bir sonraki sohbet yalnız bu dosyayı okuyup kaldığımız yerden devam edebilsin.
```

### KOMUT · YENİ SOHBETİN İLK MESAJI
```text
Ekteki DURUM dosyasını oku. Bana önce tek cümleyle nerede kaldığımızı söyle, sonra "Sırada" yazan adımdan devam et.
```
