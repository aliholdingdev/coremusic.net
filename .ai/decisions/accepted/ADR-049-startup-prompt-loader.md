---
type: adr
category: ai
title: "ADR-049: Startup Prompt Loader"
date: 2026-08-03
updated: 2026-08-08
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-049: Startup Prompt Loader

**Status:** Active (güncellenebilir)
**Kategorisi:** AI System
**İlgili Agent:** Tüm agent'lar
**İlgili Division:** AI Engineering

---

## 1. Amaç

Bu ADR, CoreMusic platformundaki AI startup prompt loader mekanizmasını, boot protokolünü, vault senkronizasyonunu ve tüm AI ajanlarının oturum başlatma sürecini tanımlar.

CoreMusic'in startup prompt loader hedefi:
- Standart boot protokolü: Tüm ajanlar aynı süreci izler
- MSA uyumlu: Max 15 dosya okuma
- Hızlı başlatma: Max 25 saniye
- Vault sync: Oturum başlangıcında vault durumu analizi
- Hallüsinasyon kontrolü: Doğrulanamayan bilgi tespiti

---

## 2. Bağlam

### 2.1 Mevcut Durum

CoreMusic'te 8 AI ajanı bulunmaktadır:
- Master Orchestrator (MO)
- Backend Architect
- UI Designer
- Security Engineer
- Data Engineer
- Embedded Engineer
- QA Engineer
- DevOps Engineer

Her ajan oturum başladığında belirli dosyaları okumalıdır.

### 2.2 Problem

- Farklı ajanlar farklı dosyaları okuyor → Tutarlılık eksikliği
- Tüm vault'u okumak token aşımına yol açıyor → Verimsizlik
- Boot süreci tanımsız → Öngörülebilirlik eksikliği
- Vault senkronizasyonu yok → Eski bilgi riski

### 2.3 Gereksinimler

| # | Gereksinim | Değer | Kaynak |
|---|------------|-------|--------|
| R1 | Boot protokolü | 10 dosya, max 25s | ADR-049 |
| R2 | MSA limit | Max 15 dosya | ADR-042 |
| R3 | Vault sync | 5 soru + 6 adım | ADR-049 |
| R4 | Fallback | index.md | ADR-049 |
| R5 | Hallüsinasyon kontrolü | VERIFICATION REQUIRED | ADR-005 |
| R6 | Timestamp | UTC format | ADR-004 |
| R7 | Audit trail | log.md | ADR-004 |
| R8 | Prioritized okuma | P0 → P1 → P2 → P3 | ADR-042 |

### 2.4 Kısıtlamalar

| # | Kısıt | Açıklama |
|---|-------|----------|
| C1 | Token limiti | Context window ~200K |
| C2 | Dosya boyutu | Max 1000 satır |
| C3 | MSA limiti | Max 15 dosya |
| C4 | Sıralı okuma | P0 → P1 → P2 → P3 |
| C5 | Paralel okuma | Desteklenmez |

---

## 3. Karar

CoreMusic'te **startup prompt loader** kullanılacak.

### 3.1 Boot Protokolü (10 Dosya)

| # | Dosya | Amac | Öncelik | Timeout |
|---|-------|------|---------|---------|
| 1 | CLAUDE.md | Kanonik AI talimatı | P0 | 3s |
| 2 | AGENTS.md | Agent kayıt defteri | P0 | 3s |
| 3 | WORKFLOW.md | Süreçler | P0 | 3s |
| 4 | index.md | Master katalog | P1 | 4s |
| 5 | keys.md | Keyword haritası | P1 | 3s |
| 6 | brain.md | Mimari kararlar | P1 | 4s |
| 7 | MEMORY.md | Oturum hafızası | P1 | 3s |
| 8 | log.md | Aktivite günlüğü | P1 | 2s |
| 9 | .claude/rules/* | Tüm kurallar | P2 | 5s |
| 10 | engine.md | Orkestrasyon motoru | P1 | 3s |

**Toplam boot süresi:** Max 25 saniye.

### 3.2 MSA Uyumlu Okuma

| Öncelik | Dosya Grubu | Max | Toplam |
|---------|-------------|-----|--------|
| P0 (Kritik) | CLAUDE.md, AGENTS.md, WORKFLOW.md | 3 | 3 |
| P1 (Yüksek) | index.md, keys.md, brain.md, MEMORY.md, log.md | 5 | 8 |
| P2 (Görev) | decisions/accepted/ADR-NNN, architecture/* | 5 | 13 |
| P3 (Düşük) | testing/*, ui-design/*, personas/* | 2 | 15 |

### 3.3 Boot Akışı

```
1. Session başlat
2. P0 dosyaları oku (CLAUDE, AGENTS, WORKFLOW) → max 9s
3. P1 dosyaları oku (index, keys, brain, MEMORY, log) → max 16s
4. Vault sync başlat (5 soru) → max 10s
5. Session state oluştur
6. Görev bağlamını analiz et
7. MSA limit kontrolü
8. Hallüsinasyon sweep
9. Boot tamamlandı → log.md'ye yaz
10. Göreve başla
```

---

## 4. Teknik Detaylar

### 4.1 Boot Loader Implementasyonu

```javascript
const BootLoader = {
    P0_FILES: ['CLAUDE.md', 'AGENTS.md', 'WORKFLOW.md'],
    P1_FILES: ['index.md', 'keys.md', 'brain.md', 'MEMORY.md', 'log.md'],
    P2_FILES: ['decisions/accepted/', 'architecture/'],
    TIMEOUT: 25000,
    MAX_FILES: 15,

    async boot() {
        const startTime = performance.now();
        const loadedFiles = [];

        // P0 oku
        for (const file of this.P0_FILES) {
            const content = await this.loadFile(file);
            if (content) loadedFiles.push(file);
        }

        // P1 oku
        for (const file of this.P1_FILES) {
            const content = await this.loadFile(file);
            if (content) loadedFiles.push(file);
        }

        // MSA kontrolü
        if (loadedFiles.length > this.MAX_FILES) {
            console.warn('MSA limit exceeded:', loadedFiles.length);
        }

        // Timeout kontrolü
        const elapsed = performance.now() - startTime;
        if (elapsed > this.TIMEOUT) {
            console.warn('Boot timeout:', elapsed);
        }

        return loadedFiles;
    },

    async loadFile(path) {
        try {
            const response = await fetch(`/.ai/${path}`);
            if (!response.ok) return null;
            return await response.text();
        } catch (e) {
            return null;
        }
    }
};
```

### 4.2 Vault Sync Protokolü

**Başlangıç (5 Soru):**

| # | Soru | Kontrol Yöntemi | Kaynak |
|---|------|-----------------|--------|
| 1 | Son session'dan bu yana ne değişti? | git log --since="last session" | git, log.md |
| 2 | Yeni ADR var mı? | decisions/accepted/ taraması | filesystem |
| 3 | Kod değişikliği oldu mu? | git diff --name-only | git |
| 4 | Vault'ta eski bilgi var mı? | VERIFICATION REQUIRED taraması | grep |
| 5 | Skills durumu nedir? | .claude/skills/ kontrolü | filesystem |

**Bitiş (6 Adım):**

| # | Adım | Kontrol | Süre |
|---|------|---------|------|
| 1 | Değişiklikleri vault'a yaz (in-place) | Dosya boyutu | 5s |
| 2 | log.md'ye timestamp ekle | Format doğrulama | 2s |
| 3 | MEMORY.md session state güncelle | Session index | 3s |
| 4 | Wiki-link'leri doğrula | Regex pattern | 5s |
| 5 | MSA limit kontrolü (15 dosya) | Dosya sayacı | 2s |
| 6 | Hallüsinasyon sweep | VERIFICATION REQUIRED | 3s |

### 4.3 Hallüsinasyon Kontrolü

```javascript
function hallucinationCheck(content) {
    const patterns = [
        /VERIFICATION REQUIRED/g,
        /UNVERIFIED/g,
        /UNKNOWN/g,
        /TODO/g,
        /FIXME/g
    ];

    const found = [];
    for (const pattern of patterns) {
        if (pattern.test(content)) {
            found.push(pattern.source);
        }
    }

    return {
        hasHallucination: found.length > 0,
        patterns: found
    };
}
```

### 4.4 Wiki-Link Doğrulama

```javascript
function validateWikiLinks(content, validFiles) {
    const regex = /\[\[([^\]]+)\]\]/g;
    const links = [];
    let match;

    while ((match = regex.exec(content)) !== null) {
        const link = match[1];
        const isValid = validFiles.includes(link);
        links.push({ link, isValid });
    }

    return {
        total: links.length,
        valid: links.filter(l => l.isValid).length,
        invalid: links.filter(l => !l.isValid).map(l => l.link)
    };
}
```

### 4.5 Session Lifecycle

| Aşama | Açıklama | Süre | Çıktı |
|-------|----------|------|-------|
| 1. Initialize | Boot protokolü | Max 25s | Session state |
| 2. Sync Start | Vault analizi | Max 10s | Değişiklik listesi |
| 3. Execute Task | Görev yürütme | Değişken | Görev çıktısı |
| 4. Log Actions | Değişiklik loglama | Anlık | Audit trail |
| 5. Vault-Sync | Vault güncelleme | Değişken | Güncellenmiş vault |
| 6. Sync End | Oturum kapatma | Max 15s | Kapanış kaydı |
| 7. Session Close | Final state | Max 5s | Final state |

---

## 5. Yasak Örüntüler

| # | Yasak | Doğru | Kaynak |
|---|-------|-------|--------|
| 1 | MSA limit aşımı (>15 dosya) | Görev parçalama | ADR-042 |
| 2 | Tüm vault'u okuma | Sparse Attention | ADR-042 |
| 3 | Paralel dosya okuma | Sıralı okuma | ADR-049 |
| 4 | Hardcoded secret | .env / credential vault | ADR-034 |
| 5 | Hallüsinasyon yayılımı | VERIFICATION REQUIRED | ADR-005 |
| 6 | Timestamp eksik | UTC format | ADR-004 |
| 7 | log.md'de silme | Append-only | ADR-004 |
| 8 | Boot timeout | Max 25s | ADR-049 |

---

## 6. Edge Cases

| # | Edge Case | Tetikleyici | Çözüm | ADR |
|---|-----------|-------------|-------|-----|
| 1 | Boot timeout | 25s aşımı | Kısmi yükleme + fallback | ADR-049 |
| 2 | Dosya bulunamadı | Vault bozulması | index.md fallback | ADR-042 |
| 3 | MSA aşımı | >15 dosya | Görev parçalama | ADR-042 |
| 4 | Hallüsinasyon tespiti | VERIFICATION REQUIRED | Etiket ekle | ADR-005 |
| 5 | Vault tutarsızlığı | Eski bilgi | Vault sync | ADR-049 |
| 6 | Network yok | Offline | Cached boot | ADR-049 |
| 7 | Token overflow | Büyük context | Chunked read | ADR-042 |
| 8 | Kirik wiki-link | Dosya taşınması | Cross-reference update | ADR-042 |
| 9 | Session kaybı | Oturum kesintisi | log.md'den resume | ADR-004 |
| 10 | Concurrent boot | Eşzamanlı başlatma | Context Lock | ADR-022 |

---

## 7. Hard Guardrails

| # | Guardrail | Uygulama | İhlal Sonucu |
|---|-----------|----------|-------------|
| G1 | MSA Limit = 15 dosya | Görev başına max 15 | Token aşımı |
| G2 | Boot Timeout = 25s | Max başlatma süresi | Yavaş başlangıç |
| G3 | P0 → P1 → P2 → P3 | Öncelik sırası | Tutarlılık eksikliği |
| G4 | Hallüsinasyon kontrolü | VERIFICATION REQUIRED | Yanlış bilgi |
| G5 | Wiki-link doğrulama | Geçerli referanslar | Kırık link |
| G6 | Audit trail | Boot loglanır | İzlenebilirlik |
| G7 | Fallback index.md | Dosya bulunamazsa | Boot başarısız |
| G8 | Vault sync | Oturum başı senkron | Eski bilgi |

---

## 8. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-004-multi-domain-spa]] | Multi-domain SPA | Vault versiyonlama |
| [[ADR-005-ultrathink-protocol]] | Zero hallucination | Hallüsinasyon kontrolü |
| [[ADR-007-cache-namespace]] | Cache namespace | Zero Code Before Plan |
| [[ADR-022-database-hardened-security]] | DB güvenlik | Vault güvenliği |
| [[ADR-042-vault-restructuring-2026-08-03]] | Vault yeniden yapılandırma | MSA limit |

---

## 9. Çapraz Referanslar

| Bölüm | Hedef Dosya | İlişki |
|-------|-------------|--------|
| § 3.1 | [[MEMORY.md]] §5 | Boot protokolü |
| § 3.2 | [[MEMORY.md]] §8 | MSA Sparse Attention |
| § 3.3 | [[engine.md]] §4 | Task dispatch |
| § 4.1 | [[brain.md]] §3 | Core principles |
| § 4.2 | [[MEMORY.md]] §6-7 | Vault sync |
| § 4.5 | [[WORKFLOW.md]] §8.6 | Session init |
| § 5 | [[log.md]] §6 | Log formatı |
| § 7 | [[CLAUDE.md]] §7 | Hard guardrails |

---

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **Startup Prompt Loader** | AI oturum başlatma mekanizması |
| **Boot Protokolü** | 10 dosya, max 25s |
| **MSA** | Master System Architecture / Sparse Attention |
| **Sparse Attention** | Seçici okuma (token tasarrufu) |
| **Vault Sync** | Vault ile kod arasındaki tutarlılık |
| **Hallüsinasyon** | Doğrulanamayan bilgi üretme |
| **VERIFICATION REQUIRED** | Doğrulanamayan bilgi etiketi |
| **Wiki-Link** | [[dosya/yolu]] formatında referans |
| **P0/P1/P2/P3** | Öncelik seviyeleri |
| **Fallback** | Varsayılan değer (index.md) |
| **Audit Trail** | İzlenebilirlik günlüğü |
| **Session Lifecycle** | Oturum yaşam döngüsü |
| **Token** | AI context window birimi |
| **Context Window** | AI modelinin max gördüğü metin |
| **Chunked Read** | Dosyanın parça parça okunması |

---

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| ADR Version | 2.0.0 |
| Status | Active · Red Team · Human Mode · Truth Mode verified |
| Last Updated | 2026-08-08 |
| Sections | 11 |
| Boot Dosyası | 10 |
| Boot Süresi | Max 25s |
| MSA Limit | 15 dosya |
| Öncelik Seviyesi | 4 (P0-P3) |
| Vault Sync Başlangıç | 5 soru |
| Vault Sync Bitiş | 6 adım |
| Session Lifecycle | 7 aşama |
| Edge Cases | 10 |
| Hard Guardrails | 8 |
| Yasak Örüntü | 8 |
| İlgili ADR | 5 |
| Çapraz Referans | 8 |
| Sözlük Terim | 15 |

---

## 12. Authority

| Alan | Değer |
|------|-------|
| Authority | Bayram Ali / Vault Steward |
| Review Status | Red Team · Human Mode · Truth Mode verified |
| Last Updated | 2026-08-08 |
| Version | 2.0.0 |
| Immutability | Active (güncellenebilir) |
| Next Review | Boot protokolü değiştiğinde |
| Related Division | AI Engineering |
| Risk Seviyesi | Orta (tüm ajanları etkiler) |

---

## 13. Deployment Considerations

| # | Konu | Detay |
|---|------|-------|
| 1 | Boot script deploy | Startup script version control |
| 2 | Vault deploy | .ai/ dizini senkronizasyonu |
| 3 | Skills deploy | .claude/skills/ güncelleme |
| 4 | Rules deploy | .claude/rules/ güncelleme |
| 5 | Fallback strategy | index.md fallback hazır |
| 6 | Backup | Boot konfigürasyonu yedekleme |
| 7 | Monitoring | Boot süresi izleme |
| 8 | Documentation | Boot protokolü dokümanı |

---

## 14. Testing Strategy

| Test Türü | Kapsam | Framework |
|-----------|--------|-----------|
| Unit Test | BootLoader JS | Vitest |
| Integration Test | Vault sync | Custom script |
| Performance Test | Boot süresi | Performance API |
| MSA Test | 15 dosya limiti | Custom script |
| Hallucination Test | VERIFICATION REQUIRED tarama | Custom script |
| Wiki-link Test | Referans doğrulama | Custom script |
| E2E Test | Tam boot süreci | Playwright |
| Timeout Test | 25s limit | Custom script |

---

## 15. Risk Assessment

| # | Risk | Olasılık | Etki | Mitigasyon |
|---|------|----------|------|------------|
| R1 | Boot timeout | Orta | Orta | Kısmi yükleme |
| R2 | Dosya bulunamadı | Düşük | Orta | index.md fallback |
| R3 | MSA aşımı | Düşük | Yüksek | Görev parçalama |
| R4 | Hallüsinasyon | Orta | Yüksek | Sweep |
| R5 | Vault tutarsızlığı | Düşük | Orta | Vault sync |
| R6 | Token overflow | Düşük | Yüksek | Chunked read |
| R7 | Eski bilgi | Orta | Orta | VERIFICATION REQUIRED |
| R8 | Network yok | Düşük | Orta | Cached boot |

---

## 16. Maintenance Guidelines

| # | Görev | Siklik | Sorumlu |
|---|-------|--------|---------|
| 1 | Boot performance audit | Aylık | QA Engineer |
| 2 | Vault integrity check | Haftalık | Master Orchestrator |
| 3 | Wiki-link validation | Aylık | Master Orchestrator |
| 4 | Hallucination sweep | Oturum başında | Tüm ajanlar |
| 5 | MSA compliance check | Aylık | Master Orchestrator |
| 6 | Rules file update | Değişiklikte | Security Engineer |

---

## 17. Future Considerations

| # | Konu | Durum | Notlar |
|---|------|-------|--------|
| 1 | Parallel boot | Araştırılıyor | P0 paralel okuma |
| 2 | Smart caching | Planlanıyor | intelligent file cache |
| 3 | Boot analytics | Planlanıyor | Kullanım istatistikleri |
| 4 | Auto-optimization | Gelecek | Otomatik dosya önceliklendirme |
| 5 | Multi-agent sync | Planlanıyor | Eşzamanlı ajan başlatma |
| 6 | Pre-warming | Araştırılıyor | Vault önyükleme |

---

## 18. Boot Performance Budget

| Faz | Hedef | Maksimum | Kritik Eşik |
|-----|-------|----------|-------------|
| P0 okuma | 6s | 9s | 12s |
| P1 okuma | 10s | 16s | 20s |
| Vault sync | 5s | 10s | 15s |
| Hallüsinasyon sweep | 2s | 3s | 5s |
| Wiki-link doğrulama | 3s | 5s | 8s |
| Toplam boot | 20s | 25s | 35s |

---

## 19. Agent-Specific Boot Customizations

| Agent | Ek Dosya | Kullanım | Öncelik |
|-------|----------|----------|---------|
| Backend Architect | architecture/l2-routing.md | Routing kuralları | P2 |
| UI Designer | architecture/l3-presentation.md | Frontend kuralları | P2 |
| Security Engineer | architecture/l1-security.md | Güvenlik kuralları | P2 |
| Data Engineer | architecture/05-data/database_master.md | DB şemaları | P2 |
| Embedded Engineer | architecture/06-audio/index.md | Audio kuralları | P2 |
| QA Engineer | testing/strategy.md | Test stratejisi | P2 |
| DevOps Engineer | ecosystem/7-service-integration.md | Servis entegrasyonu | P2 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
