---
title: "media.coremusic.net — Oturum Durum & Devir Dosyası (DURUM.md)"
type: state
category: session-state
version: 1.0.0
status: active
authority: "SSOT: media.coremusic.net/durum.md"
updated: 2026-11-01
---

# DURUM.md — media.coremusic.net (Tek Sayfalık Hafıza)

> **Kural (PDF Standardı):** Medya deposu ve yayın servisinin tek sayfalık hafızasıdır. AI bir adımı bitirince buraya yazar; yeni sohbet yalnız bu dosyayı okuyup başlar. Depo tarama ve CLI loglarını tek sohbette biriktirmek limiti tüketir.

---

**İş:** media.coremusic.net — Medya Depolama, ULID İndeksleme & Akış Servisi (ADR-092)  
**Durum:** PHP CLI arşiv araçları (`scan`, `audit`, `ingest`), ULID yol yapısı ve `media_catalog` sözleşmesi hazır; ADR-112 şema hizalaması ve HLS transcode planı bekliyor.  
**Bitenler:**  
✓ PHP CLI arşiv üçlüsü (`bin/scan.php`, `bin/audit.php`, `bin/ingest.php`) + `--dry-run` / `--deep` kapıları.  
✓ `media_catalog` 9 tablo + `v_asset_search` view + `taxonomy.json` enum sözleşmesi.  
✓ ADR-092 v1.1.0 revizyonu: Medya dizin ekseni ve gitignore kuralları (38 GB+ depo repoya girmez).  
✓ PathGuard traversal koruması ve MySqlCatalogRepository altyapısı.  
**Kaldığım yer:** `media.coremusic.net/src/Media/` ve `bin/`.  
**Sırada:** ADR-112 (media_access soft-delete canlı şema hizalama migration'ı) ve `/upload` CORS preflight kararı.  
**Kararlar:**  
- Mülkiyet & Kalite: Kayıpsız FLAC/WAV mülkiyeti korunur; dosyalar şifrelenmez veya silinmez.  
- Depolama Yolu: `media.coremusic.net/` altında ULID tabanlı ayrık dizinler.  
- Güvenlik: Byte-range ve token/grant guard ile fail-closed akış.  
- Karpathy İlkeleri (`KURAL-11`): Think Before Coding, Simplicity First, Surgical Changes, Goal-Driven Execution.  
- Git: `media/` altındaki ikili medya dosyaları asla git'e commit edilmez (`.gitignore`). Subagent commit yetkisi yoktur.  
**Açık:** Canlıda `media_access` soft-delete kolonları henüz eklenmedi (ADR-112 onay bekliyor).

---

### KOMUT · İŞİN SONUNDA CLAUDE'A / AGENT'A YAPIŞTIR
```text
Bu sohbette yaptıklarımızı yukarıdaki DURUM kalıbına göre güncelle. Kısa yaz; bir sonraki sohbet yalnız bu dosyayı okuyup kaldığımız yerden devam edebilsin.
```

### KOMUT · YENİ SOHBETİN İLK MESAJI
```text
Ekteki DURUM dosyasını oku. Bana önce tek cümleyle nerede kaldığımızı söyle, sonra "Sırada" yazan adımdan devam et.
```
