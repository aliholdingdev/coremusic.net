---
title: "Engineering Rules SSOT for CoreMusic ELECTRONICS"
type: architecture
category: engineering-rules
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Engineering Rules SSOT for CoreMusic ELECTRONICS

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]] · [[.claude/rules/core-rules.md]] · [[.claude/rules/orchestration.md]] · [[.claude/rules/vault.md]]

---

## 1. Amaç

Tüm `.claude/` ve `.ai/` kurallarının ana referans kaynağıdır. Mühendislik yönetişimi için kanonik dokümandır.

---

## 2. Kurallar Kategorileri

### 2.1 Red Team Protokolü

| Özellik | Tanım |
|---------|-------|
| Düşmanca İnceleme | Her AI çıktısı düşmanca incelemeye tabidir |
| Self-Review Kontrol Listesi | Kaynak kodu, ADR uyumluluğu, güvenlik |
| Doğrulama Komutları | PHP syntax, test, ADR compliance |

### 2.2 Truth Mode

| Özellik | Tanım |
|---------|-------|
| Zero Hallucination | ASLA uydurma API, sınıf veya tablo |
| VERIFICATION REQUIRED | Doğrulanamayan bilgi işaretlenir |
| Fabrikasyon Yasak | Doğrulanmamış bilgi reddedilir |

### 2.3 Human Mode

| Özellik | Tanım |
|---------|-------|
| ADHD Dostu Çıktı | Kısa, öz, eylem odaklı |
| Eylem ile Başla | İlk yanıt komut veya adım |
| Numaralı Adımlar | Çok adımlı çalışma numaralandırılır |
| Süre Tahmini | Somut birimlerde süre |
| İlerleme Takibi | Her turda ilerleme belirtilir |

### 2.4 Zero Hallucination (ADR-005)

| Kural | Değer |
|-------|-------|
| Uydurma Yasak | API endpoint, sınıf veya veritabanı tablosu uydurulmaz |
| Doğrulama Zorunlu | Kod yazmadan önce vault dokümanları kontrol edilir |
| Belirsizlik | Kullanıcıya sorulur |
| Web Araması | Doğrulama için Yasak |

### 2.5 Dokümantasyon Önce

| Kural | Değer |
|-------|-------|
| Koddan Önce Yaz | Önce doküman, sonra kod |
| ADR Önce | Mimari karar öncesi |
| Test Önce | Test öncesi |

### 2.6 Mimari Önce

| Kural | Değer |
|-------|-------|
| Tasarım Önce | Uygulama öncesi tasarım |
| Plan Önce | Kod öncesi plan |
| ADR Oluştur | Karar öncesi ADR |

### 2.7 Zero Code Before Plan (ADR-007)

| Kural | Değer |
|-------|-------|
| Plan Onayı | Onay olmadan kod yasağı |
| Mimari Onay | Mimari onay zorunlu |
| ADR Oluştur | Karar öncesi ADR |

### 2.8 Doğrulama Zorunlu (ADR-005)

| Kural | Değer |
|-------|-------|
| Tamamlanma Öncesi | Tamamlanmadan önce doğrulama |
| Test Çalıştır | Test sonuçlarını göster |
| Kaynak Kodu Oku | Gerçek kodu oku, tahmin yürütme |

### 2.9 ADR Kuralları

| Kural | Değer |
|-------|-------|
| Yaşam Döngüsü | Draft→Review→Active→Frozen |
| Frozen 001-037 | Değiştirilemez |
| Yeni ADR | ADR-038+ |

### 2.10 Karar Kayıtları

| Özellik | Tanım |
|---------|-------|
| Format | Frontmatter + wiki-link |
| Süreç | Oluştur→İncele→Onayla |
| Onay | Vault Steward onayı |

### 2.11 AI Kuralları

| Kural | Değer |
|-------|-------|
| Oturum Protokolü | 10 adım boot |
| Boot Dosyaları | CLAUDE.md, AGENTS.md, WORKFLOW.md |
| MSA Limiti | 15 dosya max |
| Zorunlu Skills | 5 skill |

### 2.12 Ajan Kuralları

| Kural | Değer |
|-------|-------|
| Domain Boundary | Sadece kendi alanında |
| Handover | Transfer protokolü |
| Eskalasyon | L1→L2→L3 |
| Sağlık Kontrolü | 30s timeout |
| Context Lock | Max 30s |

---

## 3. Hard Guardrails (14 Kural)

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Zero-Allocation: Audio thread'de heap allocation yasak | Ses takılması / crash |
| 2 | Lock-Free: Audio thread'de mutex yasak | Deadlock |
| 3 | Layer Violation: L0 → L3 import yasak | Derhal revert |
| 4 | SELECT *: Açık sütun listesi zorunlu | SQL injection riski |
| 5 | Hardcoded Secret: API key/log'da yasak | Güvenlik ihlali |
| 6 | csrf_token: Key ismi değişmez (ADR-010) | CSRF bozulması |
| 7 | Zero Code Before Plan: Plan onayı olmadan kod yok | Mimari bozulma |
| 8 | MSA Limit: Görev başına max 15 dosya | Token aşımı |
| 9 | In-Place Refactoring: Dosya adı/konumu değişmez | Link kırılması |
| 10 | ORM Yasak: Sadece PDO prepared (ADR-002) | SQL injection |
| 11 | Framework Yasak: Sadece Vanilla JS (ADR-001) | Bağımlılık artışı |
| 12 | Middleware Sırası: Değişmez (ADR-010/011/012/013/022) | CSP/CSRF bozulması |
| 13 | Port 81: music.coremusic.net PHP 8.4 | Servis çökmesi |
| 14 | PCM5122 Yasak: 8.1 surround için yetersiz (H001) | Yanlış donanım |

---

## 4. Referans Dosyaları

### 4.1 `.claude/rules/` Dosyaları

| Dosya | Kapsam |
|-------|--------|
| [[.claude/rules/core-rules.md]] | Temel kurallar |
| [[.claude/rules/orchestration.md]] | Orkestrasyon kuralları |
| [[.claude/rules/vault.md]] | Vault kuralları |
| [[.claude/rules/php-standards.md]] | PHP standartları |
| [[.claude/rules/js-standards.md]] | JavaScript standartları |
| [[.claude/rules/css-standards.md]] | CSS standartları |
| [[.claude/rules/security-standards.md]] | Güvenlik standartları |
| [[.claude/rules/testing-standards.md]] | Test standartları |
| [[.claude/rules/database-standards.md]] | Veritabanı standartları |
| [[.claude/rules/api-standards.md]] | API standartları |

### 4.2 `.ai/` Vault Dosyaları

| Dosya | Kapsam |
|-------|--------|
| [[CLAUDE.md]] | AI Anayasası |
| [[AGENTS.md]] | Agent Kayıt Defteri |
| [[WORKFLOW.md]] | Süreçler |
| [[index.md]] | Master Katalog |
| [[keys.md]] | Keyword Haritası |
| [[brain.md]] | Mimari Kararlar |
| [[MEMORY.md]] | Oturum Hafızası |
| [[log.md]] | Audit Trail |
| [[engine.md]] | Orkestrasyon Motoru |

---

## 5. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2.7 Zero Code Before Plan | [[ADR-007-cache-namespace]] | Plan zorunluluğu |
| § 2.4 Zero Hallucination | [[ADR-005-ultrathink-protocol]] | Hallüsinasyon politikası |
| § 3.6 csrf_token | [[ADR-010-csrf-protection-strategy]] | CSRF koruması |
| § 3.10 ORM Yasak | [[ADR-002-pdo-mandatory-no-orm]] | PDO kuralları |
| § 3.11 Framework Yasak | [[ADR-001-vanilla-js-itcss]] | Framework yasağı |
| § 4.1 rules | [[.claude/rules/core-rules.md]] | Temel kurallar |

---

## 6. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 6 |
| Rule Categories | 12 |
| Hard Guardrails | 14 |
| Reference Files | 19 |
| ADR References | 5 |
| Cross References | 6 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
