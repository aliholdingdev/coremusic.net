---
title: ".agents — Oturum Durum & Devir Dosyası (DURUM.md)"
type: state
category: session-state
version: 1.0.0
status: active
authority: "SSOT: .agents/durum.md"
updated: 2026-11-01
---

# DURUM.md — .agents (Ajan Koordinasyon & Kural Kasası) (Tek Sayfalık Hafıza)

> **Kural (PDF Standardı):** Ajan kuralları ve koordinasyonun tek sayfalık hafızasıdır. AI bir adımı bitirince buraya yazar; yeni sohbet yalnız bu dosyayı okuyup başlar.

---

**İş:** .agents/ — Ajan Rolleri, Yetki Sınırları & Kural Sistemleri  
**Durum:** 8 alt kural dosyası (`rules/`) ve 11 iş akışı (`workflows/`) senkronize; orkestrasyon ve handover protokolleri aktif.  
**Bitenler:**  
✓ 8 alan kuralı tamamlandı: architecture, audio-hardware, backend, database, frontend, RULES, security, workflow.  
✓ Multi-agent swarm hiyerarşisi (Expert 5 / Senior 5 / Junior 10) entegre edildi.  
✓ PDF standardı kural ve oturum optimizasyonu (tek sayfalık hafıza, yeni sohbet tetikleyicileri) bağlandı.  
**Kaldığım yer:** `.agents/rules/` ve `.agents/workflows/`.  
**Sırada:** İhtiyaç halinde yeni ajan rollerinin ve kural güncellemelerinin yapılması.  
**Kararlar:**  
- Hiyerarşi: Expert > Senior > Junior; kararlar kanıt-önceliklidir (oy çokluğu yoktur).  
- Yasak: Subagent dosya yazamaz veya `git commit` atamaz; çıktılar metin olarak orkestratöre iletilir.  
- Karpathy İlkeleri (`KURAL-11`): Think Before Coding, Simplicity First, Surgical Changes, Goal-Driven Execution.  
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
