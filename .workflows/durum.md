---
title: ".workflows — Oturum Durum & Devir Dosyası (DURUM.md)"
type: state
category: session-state
version: 1.0.0
status: active
authority: "SSOT: .workflows/durum.md"
updated: 2026-11-01
---

# DURUM.md — .workflows (İş Akışları & Süreç Otomasyonu) (Tek Sayfalık Hafıza)

> **Kural (PDF Standardı):** İş akışlarının tek sayfalık hafızasıdır. AI bir adımı bitirince buraya yazar; yeni sohbet yalnız bu dosyayı okuyup başlar. Tüm iş akışı dosyalarını aynı anda okumak token limitini tüketir.

---

**İş:** .workflows/ — Süreç Otomasyonları, Faz Geçişleri & Doğrulama Protokolleri  
**Durum:** 12 temel iş akışı (ADR üretimi, dağıtım, halüsinasyon kontrolü, orkestratör akışı, güvenlik denetimi, oturum başlatma, vault senkronu vb.) hazır ve aktif.  
**Bitenler:**  
✓ `adr-creation.md`: Mimari karar taslağı ve onay döngüsü akışı.  
✓ `hallucination-control.md`: Sıfır halüsinasyon ve disk kanıtı doğrulama akışı.  
✓ `session-init.md`: Oturum başı durum okuma ve çevre hazırlığı.  
✓ `vault-sync.md`: Değişiklik sonrası arşiv ve indeks senkronizasyonu.  
✓ `security-audit.md`: OWASP ve kod güvenlik denetimi protokolü.  
✓ `design.md`: Kiro standardı merkezi teknik mimari ve veri sözleşmeleri belgesi.  
✓ `tasks.md`: Kiro standardı faz bazlı (Phase 1..4), atomik görev şartnamesi.  
✓ `workflow.md`: Kiro standardı 6 adımlı ajan yürütme iş akışı kılavuzu.  
✓ `RAG.md`: Süreçler alanı akıllı bilgi erişim yönlendiricisi.  
**Kaldığım yer:** `.workflows/` içinde Kiro triad (`design.md`, `tasks.md`, `workflow.md`) mühürlendi; kök `TODO.md` ve `PRD.md` ile bağlandı.  
**Sırada:** Kullanıcı tarafından talep edilecek sonraki geliştirme görevi; orkestratör tarafından yapılacak git commit.  
**Kararlar:**  
- Süreç Sırası: Understand → Discover → Research → Verify → Resolve Context → Analyze → Architect → Plan → Implement → Track → Test → Review → Secure → Verify → Document (15 Aşama).  
- Oturum Kapama: Konu değiştiğinde veya teslim tamamlandığında `durum.md` güncellenir ve yeni sohbete geçilir.  
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
