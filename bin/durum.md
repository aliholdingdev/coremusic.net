---
title: "bin — Oturum Durum & Devir Dosyası (DURUM.md)"
type: state
category: session-state
version: 1.0.0
status: active
authority: "SSOT: bin/durum.md"
updated: 2026-11-01
---

# DURUM.md — bin (Operasyonel CLI Betikleri) (Tek Sayfalık Hafıza)

> **Kural (PDF Standardı):** CLI ve operasyonel araç betiklerinin tek sayfalık hafızasıdır. AI bir adımı bitirince buraya yazar; yeni sohbet yalnız bu dosyayı okuyup başlar.

---

**İş:** bin/ — CLI Araçları & Operasyonel Betikler  
**Durum:** `api-key-create.php`, `db-access-guard.php` ve `duration-backfill.php` üretim betikleri devrede; yeni betik ihtiyacı durumunda bu dosyadan izlenir.  
**Bitenler:**  
✓ `api-key-create.php`: UUIDv7 tabanlı API anahtarı üretimi ve SHA-256 hash saklama aracı.  
✓ `db-access-guard.php`: Doğrudan PDO açılmasını engelleyen CI denetim bekçisi.  
✓ `duration-backfill.php`: Eksik medya sürelerini arka planda dolduran CLI aracı.  
**Kaldığım yer:** `bin/`.  
**Sırada:** İhtiyaç halinde yeni CLI araçlarının ve bakım betiklerinin sözleşmeye uygun eklenmesi.  
**Kararlar:**  
- Standart: PHP 8.4 `declare(strict_types=1);`, CLI hata kodları (exit 0/1) ve güvenli input sanitizasyonu.  
- İzolasyon: Üretim betikleri web erişimine açık dizinlere serilemez; doğrudan kökten CLI ile çalıştırılır.  
- Karpathy İlkeleri (`KURAL-11`): Think Before Coding, Simplicity First, Surgical Changes, Goal-Driven Execution.  
- Git: `git commit` subagent tarafından atılmaz; kullanıcı/orkestratör yetkisindedir.  
**Açık:** Yok.

---

### KOMUT · İŞİN SONUNDA CLAUDE'A / AGENT'A YAPIŞTIR
```text
Bu sohbette yaptıklarımızı yukarıdaki DURUM kalıbına göre güncelle. Kısa yaz; bir sonraki sohbet yalnız bu dosyayı okuyup kaldığımız yerden devam edebilsin.
```

### KOMUT · YENİ SOHBETİN İLK MESAJI
```text
Ekteki DURUM dosyasını oku. Bana önce tek cümleyle nerede kaldığımızı söyle, sonra "Sırada" yazan adımdan devam et.
```
