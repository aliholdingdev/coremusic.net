---
title: "CoreMusic — AI Memory System"
type: architecture
category: memory-system
updated: 2026-08-09
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — AI Memory System

**Zorunlu Bağlantılar:** [[index]] · [[MEMORY.md]] · [[brain.md]] · [[CLAUDE.md]] · [[AGENTS.md]]

---

## 1. Amaç

AI agent'larının session ve persistent hafıza yönetimini tanımlar. Memory hierarchy, session lifecycle, persistent state, cache strategies, backup ve conflict resolution mekanizmalarını kapsar. Bu dosya, `.ai/` vault'un bellek sisteminin SSOT'udur.

---

## 2. Memory Hierarchy

| Öncelik | Dosya | İçerik | Mod | Max Boyut |
|---------|-------|--------|-----|-----------|
| En Yüksek | `brain.md` | Mimari kararlar, ADR 001-050 | Read-Write | 1000 satır |
| Yüksek | `index.md` | Master katalog, vault indeksi | Read-Write | 1000 satır |
| Yüksek | `AGENTS.md` | Agent tanımları, yetkiler, handover | Read-Write | 1000 satır |
| Orta | `MEMORY.md` | Session state, bu dosya | Read-Write | 1000 satır |
| En Düşük | `log.md` | Append-only audit trail | Append-Only | 1000 satır |

**Öncelik Sıralaması:** Cakışma durumunda üst seviye kazanır. brain → index → AGENTS → MEMORY → log.

**Bellek Modları:**
- **Read-Only:** Boot protokolu sırasında
- **Append-Only:** `log.md` için kalıcı mod
- **Read-Write:** `MEMORY.md` ve diğerleri için
- **Locked:** Context Lock sırasında (max 30s)

---

## 3. Session Lifecycle

```
1. Initialize    → Boot protokolu (10 dosya oku)          Max 25s
2. Sync Start    → 5 soru ile vault durumunu analiz et    Max 10s
3. Execute Task  → Vault dosyalarını oku ve görevi yürüt  Değişken
4. Log Actions   → Değişiklikleri log.md'ye yaz           Anlık
5. Vault-Sync    → Vault'u güncelle (gerekiyorsa)         Değişken
6. Sync End      → 6 adım ile oturumu kapat               Max 15s
7. Session Close → Final state kaydı                      Max 5s
```

**Toplam Sabit Süre:** Boot 25s + sync start 10s + sync end 15s + session close 5s = 55s. Kalan: görev yürütme.

---

## 4. 10-Step Boot Protocol

| # | Dosya | Amaç | Öncelik | Timeout |
|---|-------|------|---------|---------|
| 1 | `.ai/CLAUDE.md` | Kanonik AI talimatı | P0 | 3s |
| 2 | `.ai/AGENTS.md` | Agent kayıt defteri | P0 | 3s |
| 3 | `.ai/WORKFLOW.md` | Süreçler | P0 | 3s |
| 4 | `.ai/index.md` | Master katalog | P1 | 4s |
| 5 | `.ai/keys.md` | Anahtar kelime haritası | P1 | 3s |
| 6 | `.ai/AGENTS.md` | Agent yetkileri (tekrar) | P1 | 3s |
| 7 | `.ai/brain.md` | Mimari kararlar | P1 | 4s |
| 8 | `.ai/MEMORY.md` | Oturum hafızası | P1 | 3s |
| 9 | `.ai/log.md` | Aktivite günlüğü (son 20 satır) | P1 | 2s |
| 10 | `.claude/rules/*` | Tüm kurallar | P2 | 5s |

**Toplam boot süresi:** Max 25 saniye. P0 → P1 → P2 sırasıyla okunur.

---

## 5. MSA Sparse Attention (ADR-042/C5)

**Görev başına max 15 dosya okunur.**

| Öncelik | Dosya Grubu | Max | Toplam |
|---------|-------------|-----|--------|
| P0 (Kritik) | CLAUDE.md, AGENTS.md, WORKFLOW.md | 3 | 3 |
| P1 (Yüksek) | index.md, keys.md, brain.md, MEMORY.md, log.md | 5 | 8 |
| P2 (Görev) | decisions/accepted/ADR-NNN, architecture/L[0-3]/* | 5 | 13 |
| P3 (Düşük) | testing/*, ui-design/*, personas/* | 2 | 15 |

**Kurallar:**
1. P0 → P1 → P2 → P3 sırasıyla okunur
2. Fallback: `index.md`
3. Limit aşılırsa `log.md`'ye WARN yazılır
4. Token aşımı önlenir: gereksiz dosya okunmaz
5. Seçici okuma (Sparse Attention) uygulanır

---

## 6. Persistent State

| Kategori | Dosya | Güncelleme Sıklığı | Mod |
|----------|-------|-------------------|-----|
| Mimari Kararlar | `brain.md` | Yeni ADR'de | Read-Write |
| Session Hafızası | `MEMORY.md` | Her oturum sonunda | Read-Write |
| Audit Trail | `log.md` | Her kritik olayda | Append-Only |
| Agent Tanımları | `AGENTS.md` | Değişiklikte | Read-Write |
| Süreçler | `WORKFLOW.md` | Değişiklikte | Read-Write |
| Master Katalog | `index.md` | Yeni dosya eklendiğinde | Read-Write |
| Keyword Haritası | `keys.md` | Yeni kelime eklendiğinde | Read-Write |

**Kurallar:**
- Immutability: ADR 001-037 frozen, değiştirilemez
- Append-Only: `log.md` geçmiş satırları silinemez
- Timestamp zorunlu: Her giriş UTC timestamp içermeli
- No Secrets: Hassas veri ASLA vault'a yazılmaz
- Cross-Reference: Tüm wiki-link'ler geçerli olmalı

---

## 7. Cache Strategies

| Seviye | Açıklama | Ömür | Geçersiz Kılma |
|--------|----------|------|----------------|
| L1 (Hot) | SSOT dosyaları (CLAUDE, AGENTS, WORKFLOW) | Oturum sonu | Otomatik |
| L2 (Warm) | Görev dosyaları (ADR, architecture) | Görev sonu | Dosya değişikliği |
| L3 (Cool) | Referans dosyaları (testing, ui-design) | İsteğe bağlı | Manuel |

**Politika:** Read-Through, Write-Through, LRU eviction. P0 dosyaları eviction'a uğramaz.

---

## 8. Backup & Recovery

| Yöntem | Sıklık | Saklama | Kullanım |
|--------|--------|---------|----------|
| Git History | Her commit | Sonsuz | Birincil kurtarma |
| Manuel Snapshot | Haftalık | 3 ay | Haftalık yedek |
| Tam Vault Yedeği | Aylık | 1 yıl | Tam kurtarma |
| Session Backup | Her oturum sonu | 30 gün | Session kurtarma |

| Durum | Kurtarma Yöntemi | Hedef Süre |
|-------|------------------|------------|
| Dosya bozulması | `git checkout <hash> -- .ai/dosya.md` | <1 dk |
| Kırık wiki-link | index.md + keys.md güncelle | <5 dk |
| Vault silinmesi | `git restore .ai/` | <5 dk |
| Session kaybı | log.md'den resume | <2 dk |
| ADR çelişkisi | L1 → L2 → L3 → İnsan | <30 dk |
| Vault corruption | `git checkout` + son commit | <5 dk |
| Cache bozulması | L1 flush, yeniden yükle | <1 dk |

---

## 9. Security Boundaries

| Veri Türü | Sınıf | Vault'a Yazılabilir mi? | Loglanırken |
|-----------|-------|--------------------------|-------------|
| API Key | SECRET | ❌ ASLA | `[REDACTED]` |
| DB Password | SECRET | ❌ ASLA | `[REDACTED]` |
| JWT Secret | SECRET | ❌ ASLA | `[REDACTED]` |
| Session Token | SECRET | ❌ ASLA | `[REDACTED]` |
| ARL Token | SECRET | ❌ ASLA | `[REDACTED]` |
| Credential Vault Şifresi | SECRET | ❌ ASLA | `[REDACTED]` |
| Kullanıcı E-postası (masked) | PII | ✅ Kısmi | Kısmi maskeleme |
| Kullanıcı ID | PUBLIC | ✅ | Yok |
| ADR Kararı | PUBLIC | ✅ | Yok |
| Port Numarası | PUBLIC | ✅ | Yok |
| Dosya Yolu | PUBLIC | ✅ | Yok |

**Doğru:** `API Key: [REDACTED] (service: deezer)` | **Yanlış:** `API Key: abc123` (ASLA!)

**Redaction Kontrolü:** Her oturum sonunda `Select-String -Path .ai/log.md -Pattern "password|api[_-]?key|secret|token"` ile tarama yapılır.

---

## 10. Memory Conflict Resolution

| Çakışma Türü | Belirti | Çözüm | Sorumlu |
|---------------|---------|-------|---------|
| Write-Write | İki ajan aynı dosyayı düzenlemek ister | Context Lock + Queue | MO |
| Read-Write | Bir ajan okurken diğeri yazar | Read lock (eşzamanlı okuma serbest) | Otomatik |
| Version Conflict | Farklı versiyonlar oluşturulur | Master Orchestrator müdahalesi | MO |
| Reference Conflict | Kırık wiki-link'ler oluşur | Cross-reference update | MO |
| ADR Conflict | Çelişkili kararlar | Escalasyon (L1→L2→L3→İnsan) | L3 |

**Context Lock:**
- Max 30 saniye
- Deadlock'da MO en eski kiliti kırar
- Öncelik sırası: CRITICAL > HIGH > MEDIUM > LOW
- Lock acquire/release `log.md`'ye yazılır

---

## 11. Memory Debugging

| Sorun | Belirti | Çözüm | Öncelik |
|-------|---------|-------|---------|
| Kırık wiki-link | `[[dosya]]` geçersiz | Doğru dosya yolunu bul, link'i güncelle | HIGH |
| Eksik frontmatter | 7 zorunlu alan eksik | Frontmatter'i tamamla | MEDIUM |
| Boyut limit aşımı | Dosya >1000 satır | Dosyayı böl veya arşivle | MEDIUM |
| Hallüsinasyon | `VERIFICATION REQUIRED` etiketi yok | Etiketi ekle | CRITICAL |
| Token overflow | MSA >15 dosya | Görev parçalama | HIGH |
| Session kaybı | Oturum yarım kaldı | log.md'den resume | MEDIUM |
| Vault tutarsızlığı | Çelişkili dosyalar | Cross-reference update | HIGH |
| Eski bilgi | `VERIFICATION REQUIRED` var | Doğrula veya sil | MEDIUM |

---

## 12. Warnings

| # | Uyarı | ADR |
|---|-------|-----|
| 1 | Hassas veri ASLA `.ai/` dizinine yazılmaz | ADR-022 |
| 2 | `log.md` Append-Only, geçmiş silinemez | ADR-004 |
| 3 | Session timestamp'leri UTC immutable olmalı | ADR-004 |
| 4 | ADR 001-037 FROZEN, yeni karar için ADR-038+ | ADR-042 |
| 5 | **MSA Limit = 15 dosya** | ADR-042 |
| 6 | **music.coremusic.net = Port 81, PHP 8.4** | ADR-042 |
| 7 | **CSRF Token Key = `csrf_token`** | ADR-010 |
| 8 | Layer Violation: L0→L3 import yasak | CLAUDE.md |
| 9 | ORM yasak — sadece PDO prepared | ADR-002 |
| 10 | Framework yasak — sadece Vanilla JS | ADR-001 |
| 11 | Middleware sırası değişmez | ADR-010/011/012/013/022 |
| 12 | PCM5122 yasak — 8.1 için yetersiz | ADR-038 |

---

## 13. Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 Hierarchy | [[MEMORY.md]] §3 | Memory hierarchy |
| § 3 Session | [[MEMORY.md]] §4 | Session lifecycle |
| § 5 MSA | [[ADR-042-vault-restructuring-2026-08-03]] | MSA limit |
| § 6 Persistent | [[ADR-004-multi-domain-spa]] | Vault versiyonlama |
| § 8 Backup | [[ADR-022-database-hardened-security]] | Güvenlik |
| § 9 Security | [[ADR-010-csrf-protection-strategy]] | CSRF |
| § 10 Conflict | [[ADR-008-bypass-auth-middleware]] | BypassAuth |

---

## 14. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 14 |
| SSOT Authority | AI Memory System |
| Last Updated | 2026-08-09 |
| ADR Coverage | ADR-002/004/008/010/011/022/038/042 |
| MSA Uyumlu | ✅ |
| Security Boundary | ✅ REDACTED policy |
| Cross References | 7 çapraz referans |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
