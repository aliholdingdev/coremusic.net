---
title: "assets.coremusic.net — Oturum Durum & Devir Dosyası (DURUM.md)"
type: state
category: session-state
version: 1.0.0
status: active
authority: "SSOT: assets.coremusic.net/durum.md"
updated: 2026-11-01
---

# DURUM.md — assets.coremusic.net (Tek Sayfalık Hafıza)

> **Kural (PDF Standardı):** CSS, JS ve medya varlıklarının tek sayfalık hafızasıdır. AI bir adımı bitirince buraya yazar; yeni sohbet yalnız bu dosyayı okuyup başlar. Devasa CSS/JS dosyalarını tek sohbette biriktirmek limiti tüketir.

---

**İş:** assets.coremusic.net — ITCSS Tasarım Sistemi & Vanilla JS Kütüphanesi  
**Durum:** ITCSS 11 katman mimarisi ve Playwright e2e testleri (18/18 passed) tamamlandı; package.json devDependencies birleşimi ve test-artifact gitignore bekliyor.  
**Bitenler:**  
✓ CSS Template v4.0.0 (11 katman: `01_Abstracts` → `11_OAuth`) tam hizalandı.  
✓ Playwright e2e WCAG alt-text ve player-info testleri: 18/18 passed.  
✓ Figma token SSOT (`.env.figma` + `.gitignore:69`) mühürlendi.  
✓ 21 ekran (151 hedef / 136 indirilen) tasarım sistemi ve şablon hizası tamamlandı.  
✓ Coreplayer ve ses kontrol slider JS mantığı optimize edildi.  
**Kaldığım yer:** `assets.coremusic.net/Css/` ve `assets.coremusic.net/js/`.  
**Sırada:** `package.json` devDependencies ikiliğinin tekilleştirilmesi ve test çıktıları (`test-results/`) için gitignore eklenmesi.  
**Kararlar:**  
- Tema Kuralı: KESİNLİKLE YALNIZCA LIGHT MODE (Dark Mode kodu veya değişkeni yazmak KESİNLİKLE YASAKTIR).  
- CSS Token Kuralı: Ham hex/px katman dosyalarına yazılamaz; sabit değerler yalnızca `01_Abstracts/a-*.css` içinde `--cm-*` olarak tutulur.  
- JS Mimarisi: Vanilla ES2022+; dış framework veya TailwindCSS kesinlikle yasaktır.  
- Karpathy İlkeleri (`KURAL-11`): Think Before Coding, Simplicity First, Surgical Changes, Goal-Driven Execution.  
- Git: `git commit` subagent tarafından atılmaz; kullanıcı/orkestratör yetkisindedir.  
**Açık:** Yok (Tüm Playwright e2e testleri yeşil).

---

### KOMUT · İŞİN SONUNDA CLAUDE'A / AGENT'A YAPIŞTIR
```text
Bu sohbette yaptıklarımızı yukarıdaki DURUM kalıbına göre güncelle. Kısa yaz; bir sonraki sohbet yalnız bu dosyayı okuyup kaldığımız yerden devam edebilsin.
```

### KOMUT · YENİ SOHBETİN İLK MESAJI
```text
Ekteki DURUM dosyasını oku. Bana önce tek cümleyle nerede kaldığımızı söyle, sonra "Sırada" yazan adımdan devam et.
```
