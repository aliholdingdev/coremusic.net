---
title: "app-cpp-projects — Oturum Durum & Devir Dosyası (DURUM.md)"
type: state
category: session-state
version: 1.0.0
status: active
authority: "SSOT: app-cpp-projects/durum.md"
updated: 2026-11-01
---

# DURUM.md — app-cpp-projects (Tek Sayfalık Hafıza)

> **Kural (PDF Standardı):** C++ ve Neva Audio Engine projelerinin tek sayfalık hafızasıdır. AI bir adımı bitirince buraya yazar; yeni sohbet yalnız bu dosyayı okuyup başlar. Devasa DSP analizlerini tek sohbette biriktirmek limiti tüketir.

---

**İş:** app-cpp-projects — C++20 Neva Engine & Yüksek Başarımlı Audio DSP  
**Durum:** Neva PRD'leri (K-6 Onay, 182+215 gereksinim), ADR-0105 web doğrulaması ve RE turları (kompresör/limiter) tamamlandı; CMake build/test kanıtı ve Faz 0 spike bekliyor.  
**Bitenler:**  
✓ ADR-0105 Native Media Stack + 112 kaynaklı web doğrulaması (soxr, ASIO dual-license, WASAPI exclusive).  
✓ Neva PRD seti onaylandı (8000+ satır, TC matrisi).  
✓ VoiceMeeter & FxSound reverse-engineering turları tamamlandı (doc 04 v1.4.5).  
✓ C++20 zero-allocation ve lock-free SPSC halka kuyruk mimarisi netleştirildi.  
✓ VRAM 3. denetim tamamlandı (GTX 1660 / 6144 MB baseline).  
✓ Neva Engine C++20 Kaynak Kodu app-cpp-projects/neva-engine/ Konumuna Yerleştirildi (16 Dosya).  
✓ CMake C++20 (MSVC 19.44) Yapılandırması ve Derlemesi Başarıyla Tamamlandı (neva_engine.lib, neva_session.dll).  
✓ 3 Birim Test Süiti %100 Başarıyla Çalıştırıldı:  
  - neva_tests.exe: ALL TESTS PASSED (biquad, dc_gain, spsc_ring, eq_chain).  
  - eq_grids_tests.exe: ALL EQ_GRID TESTS PASSED (10-band, 31-band maskeler, kazanç ve bypass).  
  - icy_parser_tests.exe: ALL ICY PARSER TESTS PASSED (UTF-8, XSS sanitization, metaint sınırları).  
**Kaldığım yer:** `app-cpp-projects/` ve `--/neva-engine/`.  
**Sırada:** `cmake -S . -B build` yapılandırması ve Neva Engine M0 prototipi.  
**Kararlar:**  
- Dil: C++20 (ISO/IEC 14882:2020), noexcept, alignas(64), RAII, smart pointers.  
- Ses Hattı: Dahili 32-bit Float boru hattı, True Peak Brickwall Limiter.  
- Donanım Amfisi: Kesinlikle Class AB topolojisi (Class D yasaktır).  
- Karpathy İlkeleri (`KURAL-11`): Think Before Coding, Simplicity First, Surgical Changes, Goal-Driven Execution.  
- Git: `git commit` subagent tarafından atılmaz; kullanıcı/orkestratör yetkisindedir.  
**Açık:** Yok (Planlama ve dokümantasyon tam hizalı).

---

### KOMUT · İŞİN SONUNDA CLAUDE'A / AGENT'A YAPIŞTIR
```text
Bu sohbette yaptıklarımızı yukarıdaki DURUM kalıbına göre güncelle. Kısa yaz; bir sonraki sohbet yalnız bu dosyayı okuyup kaldığımız yerden devam edebilsin.
```

### KOMUT · YENİ SOHBETİN İLK MESAJI
```text
Ekteki DURUM dosyasını oku. Bana önce tek cümleyle nerede kaldığımızı söyle, sonra "Sırada" yazan adımdan devam et.
```
