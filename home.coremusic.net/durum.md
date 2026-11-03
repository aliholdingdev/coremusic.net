---
title: "home.coremusic.net — Oturum Durum & Devir Dosyası (DURUM.md)"
type: state
category: session-state
version: 1.0.0
status: active
authority: "SSOT: home.coremusic.net/durum.md"
updated: 2026-11-01
---

# DURUM.md — home.coremusic.net (Tek Sayfalık Hafıza)

> **Kural (PDF Standardı):** Bu alanın tek sayfalık hafızasıdır. AI bir adımı bitirince buraya yazar; yeni sohbet yalnız bu dosyayı okuyup başlar. Bütün vault'u baştan okumak token limitini bitirir; bu dosya okunarak doğrudan işe başlanır.

---

**İş:** home.coremusic.net — Ana Müzik Portalı & SPA Panel Geliştirmesi  
**Durum:** 4-Tier responsive düzen (Phone, Embedded, Wide, 4K) ve Backend 01-11 adımları tamamlandı; offline akış kontrolü ve birim testler bekliyor.  
**Bitenler:**  
✓ Header CSP + idempotent `replaceChildren` + dropdown rol filtreleme (`f3a6cc9`, `afe8634`).  
✓ Footer volume ikonu + mute toggle + ham oran (`$volRatio <= 0.02`) hassasiyeti (`59ea405`, `339104d`).  
✓ Home song button Figma kapak overlay (cd-case frame + tur rozeti) (`b0921d8`).  
✓ Sidebar/history scrollbar Figma ölçüm onarımı (`2ce12f1`).  
✓ HistoryComponent hibrit render ve 7 birim test (`1f60449`, `27c568b`).  
✓ `NewPagesTest` genişletildi: Albümler, Albüm Detay, Sanatçılar, Playlist Wide/Fullscreen, Ayarlar EQ, Terminal Telemetri ve 4K UHD testleri eklendi (103 test, 412 assertions %100 yeşil).  
**Kaldığım yer:** `home.coremusic.net/pages/` ve `include/Stream/MusicStreamHandler.php` akış doğrulaması.  
**Sırada:** H-1 coreplayer Offline-First çapraz kontrolü (PRD-PLT-001) ve volume slider `change` testleri.  
**Kararlar:**  
- Tema: YALNIZCA LIGHT MODE (Dark Mode kodu veya sınıfı yazmak KESİNLİKLE YASAK).  
- Donanım: Kesinlikle Class AB amfi topolojisi.  
- Router: `shared/src/PageRouter` tek giriş kapısıdır.  
- Karpathy İlkeleri (`KURAL-11`): Think Before Coding, Simplicity First, Surgical Changes, Goal-Driven Execution.  
- Git: `git commit` subagent tarafından atılmaz; kullanıcı/orkestratör yetkisindedir.  
**Açık:** Yok (Tüm rotalar ve arayüzler stabil).

---

### KOMUT · İŞİN SONUNDA CLAUDE'A / AGENT'A YAPIŞTIR
```text
Bu sohbette yaptıklarımızı yukarıdaki DURUM kalıbına göre güncelle. Kısa yaz; bir sonraki sohbet yalnız bu dosyayı okuyup kaldığımız yerden devam edebilsin.
```

### KOMUT · YENİ SOHBETİN İLK MESAJI
```text
Ekteki DURUM dosyasını oku. Bana önce tek cümleyle nerede kaldığımızı söyle, sonra "Sırada" yazan adımdan devam et.
```
