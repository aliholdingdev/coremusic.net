---
title: "CoreMusic — Süreç & Kalite Kuralları (Workflow · QA · Commit · Kiro Spec)"
type: guide
category: governance
date: 2026-10-22
updated: 2026-11-01
version: 2.0.0
status: active
authority: "Derived — SSOT: .ai/WORKFLOW.md · .ai/AGENTS.md §7-§12, §16 · .ai/.rules/kurallar.md · .workflows/workflow.md"
docType: workflow
---

# CoreMusic — Süreç & Kalite Kuralları (Workflow & Quality Rules)

**docType:** workflow · **Klasör:** `.ai/.rules/` · **Sorumlu:** Master Orchestrator (süreç) + qa-engineer (kapı)

**Zorunlu Bağlantılar:** [[RULES.md]] · [[kurallar.md]] · [[hata-listesi.md]] · [[senior-mode.md]] · [[.ai/WORKFLOW.md]] · [[.ai/AGENTS.md]] · [[.workflows/workflow.md]]

> **eli10:** Görev yaşam döngüsü 15 aşamadır; Kiro 6-adımlı spec akışı esastır; durum.md ile oturum devredilir; subagent commit atamaz.

---

## 1. Amaç

CoreMusic'te her görevin **yaşam döngüsünü, Kiro spec akışını, kalite kapılarını, oturum devir protokolünü (`durum.md`), token koruma tetikleyicilerini (`KURAL-08`) ve commit yasaklarını** vault SSOT'larından toplar. QA hedefi: **Coverage ≥%80, flaky test %0** (`.ai/AGENTS.md` §16).

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 15 aşamalı otonom yaşam döngüsü ve Kiro 6-adımlı spec akışı | Domain kodlama kuralları → ilgili `*-rules.md` |
| Oturum devri (`durum.md`, `log.md`, `MEMORY.md`) | Mimari katman sınırları → [[architecture-rules.md]] |
| Öncelik, timeout, retry ve context lock kuralları | Güvenlik şifreleme ve sır denetimi → [[security-rules.md]] |
| Subagent commit yasağı (`HL-03`) ve onay kapıları (`KURAL-07`) | Veritabanı şema sözleşmeleri → [[database-rules.md]] |

---

## 3. Mimari

### 3.1 15 Aşamalı Otonom Yaşam Döngüsü

```text
Understand → Discover → Research → Verify → Resolve Context → Analyze
→ Architect → Plan → Implement → Track → Test → Review → Secure → Verify → Document
```

1. **Analiz Bitmeden Kod Yazılamaz:** İlk 6 aşama (anlama, keşif, araştırma, doğrulama, bağlam çözme, analiz) tamamlanmadan kod yazımına geçilemez.
2. **Soru Sorma Kapısı (Asking Questions):** Belirsizlik durumunda soru sorma bütçesi (≤8 soru, P0-P3 öncelikli) işletilir; onaysız sonraki aşamaya geçilmez.

### 3.2 Kiro Spec Akışı (`.workflows/`)

Her teknik görevde `.workflows/` dizinindeki kanonik Kiro spesifikasyon üçlüsü işletilir:
- **`design.md`:** Mimari ve teknik tasarım spesifikasyonu.
- **`tasks.md`:** Fazlara bölünmüş, onaylı görev listesi.
- **`workflow.md`:** 6 adımlı otonom icra akışı:
  - **Adım 1:** Gereksinim Alma & Netleştirme (Requirements & Questions)
  - **Adım 2:** Teknik Tasarım & Mimari Hizalama (Design & Alignment)
  - **Adım 3:** Görev Planlama (Tasks Specification)
  - **Adım 4:** Otonom İcra Döngüsü (Implementation Loop)
  - **Adım 5:** Dağıtım Öncesi Doğrulama (Pre-Flight & Automated Testing)
  - **Adım 6:** Oturum Kapanışı & Durum Devri (Handoff & Logging)

### 3.3 Oturum Hafızası & Durum Devri Protokolü (`KURAL-05` & `HL-11`)

- **Oturum Başlangıcı (Boot):** AI her işe başlarken kök [`.ai/durum.md`](.ai/durum.md) ve üzerinde çalışılacak subdomain'in `[subdomain]/durum.md` dosyasını okuyarak güncel durumu yükler.
- **Oturum Kapanışı:** Görev tamamlandığında:
  1. `durum.md` güncellenir (son durum, tamamlanan işler, bekleyen görevler).
  2. `.ai/log.md` dosyasına timestamp ile **append** edilir (geçmiş satırlar silinmez).
  3. `.ai/MEMORY.md` dosyası tazelenir.

### 3.4 Oturum Triggers & Token Koruma Protokolü (`KURAL-08`)

Aşağıdaki 4 durumda mevcut sohbet sürdürülmez; durum kaydedilir ve **YENİ SOHBET** açılır:
1. **Konu Değişimi:** Önceki görev bittiğinde yeni bir modüle veya sayfaya geçilirken.
2. **Tekrarlama / Ağırlaşma:** AI eski kararları unutmaya veya bağlamı karıştırmaya başladığında.
3. **Büyük Dosya / Analiz Kapanışı:** Geniş kapsamlı log/kod incelemesi bittiğinde (bağlamı şişirmemek için).
4. **Teslim & Revize Sonrası:** Her kod teslimatı veya revizyon onaylandığında.

### 3.5 Görev Öncelikleri, Timeout ve Context Lock (`.ai/AGENTS.md` §8)

| Öncelik | Tanım / Kapsam | Timeout | Max Retry |
|---|---|---|---|
| **CRITICAL** | Sistem durması, aktif güvenlik açığı | 5s | 1 |
| **HIGH** | Kritik işlev kaybı, veri kaybı riski | 15s | 3 |
| **MEDIUM** | Normal geliştirme ve özellik ekleme | 30s | 3 |
| **LOW** | İyileştirme, dokümantasyon, refactoring | 60s | 2 |

**Context Lock:** Maksimum 30 saniye. Deadlock oluştuğunda Master Orchestrator en eski kilidi kırar; CRITICAL görevler diğer tüm kilitleri ezebilir.

### 3.6 Sürüm Kontrolü & Subagent Commit Yasağı (`HL-03`)

- **Subagent Commit ATAMAZ:** `git commit` ve `git add` yetkisi yalnızca kullanıcıya (Bayram Ali) ve ana orkestratöre aittir.
- **Commit Mesaj Formatı:** `tur: kısa açıklama` (Türkçe, düz ve net).

### 3.7 Karpathy Davranışsal İlkeleri & Kod Kalitesi (`.claude/skills/karpathy-guidelines/`)

Andrej Karpathy'nin LLM kodlama zaafları üzerine yaptığı gözlemlerden türetilen 4 ilke, tüm geliştirme ve QA akışında zorunludur:
1. **Think Before Coding:** Varsayım yapma. Şüpheyi gizleme. Ödünleşimleri sun. Basit yol varsa itiraz et (push back).
2. **Simplicity First:** Sorunu çözen minimum kod. Spekülatif soyutlama üretme. 200 satırlık iş 50 satırda çözülebiliyorsa yeniden yaz.
3. **Surgical Changes:** Yalnızca dokunman gereken yere dokun. İlgisiz koda, formata ve yoruma dokunma. Kendi yetim kodlarını temizle.
4. **Goal-Driven Execution:** Başarı kriterini testlerle tanımla. Doğrulanana kadar döngüde kal (`[Step] → verify: [check]`).

---

## 4. Kurallar

| # | Kural | İhlal Sonucu |
|---|---|---|
| 1 | Analiz ve Kiro tasarım adımları tamamlanmadan kod yazmak | İş geri alınır (revert) |
| 2 | Pre-flight kontrollerini atlamak (Mockup, Light Mode, Class AB) | İşlem durdurulur |
| 3 | Subagent tarafından `git commit` veya `git add` çalıştırılması (`HL-03`) | İhlal bildirimi; yetki dışı |
| 4 | Karpathy ilkelerini ihlal etmek (varsayımla iş yapma, overengineering, cerrahi olmayan düzenleme) | Kod basitleştirme ve revert |
| 5 | `log.md` geçmiş satırlarını değiştirmek veya silmek | Append-only ihlali (CRITICAL) |
| 6 | Durum devrini işletmeden sohbeti kapatmak (`KURAL-05`) | Bağlam kaybı |
| 7 | Kanıtsız ve test edilmemiş kodu "tamamlandı" saymak | Zero-Hallucination ihlali |

---

## 5. Workflow

```text
Her Görevde:
  1. Boot: kök durum.md + subdomain durum.md okuma
  2. Pre-Flight: kurallar.md + hata-listesi.md (HL-01..12) taraması
  3. Kiro Spec: design.md → tasks.md hazırlığı
  4. İcra: Domain boundary içinde temiz kodlama (strict types, raw PDO, no ORM)
  5. Doğrulama: Otomasyon testleri + browser doğrulaması (UI için)
  6. Kapanış: durum.md güncelleme + log.md append (subagent commit atmaz)
```

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---|---|
| 1 | Frontmatter 7 alan + docType: workflow | Mevcut |
| 2 | Yaşam döngüsü 15 aşama | Eksiksiz tanımlandı |
| 3 | Kiro spec triad entegrasyonu | design.md, tasks.md, workflow.md belirtildi |
| 4 | Subagent commit yasağı | `HL-03` kuralı işlendi |
| 5 | Token koruma protokolü | `KURAL-08` 4 tetikleyici durumu mevcut |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|---|---|---|
| Süreç SSOT | [[.ai/WORKFLOW.md]] | Faz kapıları ve akış anayasası |
| Kiro Akışı | `.workflows/workflow.md` | 6 adımlı otonom spec akışı |
| Temel Direktifler | [[kurallar.md]] | KURAL-05 (oturum hafızası) & KURAL-08 (token triggers) |
| Hata Listesi Kataloğu | [[hata-listesi.md]] | HL-03 commit yasağı, HL-11 oturum optimizasyonu |
| Master Kurallar | [[RULES.md]] | Genel aktivite kuralları |
| Karpathy Yönergeleri | `.claude/skills/karpathy-guidelines/` · `CURSOR.md` | Kodlama zaaflarını önleme standartları |
| Durum Devir Dosyası | `.ai/durum.md` | Tek sayfalık oturum hafızası |

---

**Authority:** Bayram Ali / Kurucu & Mimari Otorite  
**Last Updated:** 2026-11-02 · **Version:** 2.1.0
