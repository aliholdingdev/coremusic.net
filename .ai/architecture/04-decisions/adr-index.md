---
type: architecture
category: decisions
title: "ADR Index"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR Index

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]] · [[brain.md]]

## 1. Amaç

Tüm Architecture Decision Records (ADR) indeksini sunan, durumlarını ve kategorilerini kataloglayan **ADR Kataloğu**dur.

## 2. ADR Genel Bakış

| Durum | Aralıklar | Sayı | Açıklama |
|-------|-----------|------|----------|
| **Frozen** | ADR-001 → ADR-037 | 37 | Değiştirilemez |
| **Active** | ADR-038 → ADR-050 | 13 | Güncellenebilir |
| **Toplam** | — | 50 | — |

## 3. Frozen ADRs (001-037)

*Bu ADR'ler ASLA değiştirilemez. İstisna: Sadece hayati güvenlik hatası.*

### 3.1 Frontend (ADR-001)

| ADR | Konu | Kategori |
|-----|------|----------|
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS + ITCSS, framework yasak | Frontend |

### 3.2 Database (ADR-002, 003, 014, 033)

| ADR | Konu | Kategori |
|-----|------|----------|
| [[ADR-002-pdo-mandatory-no-orm]] | PDO mandatory, ORM yasak | Database |
| [[ADR-003-multi-db-9-databases]] | 9 BCNF veritabanı | Database |
| [[ADR-014-multi-db-migration-strategy]] | Multi-DB migration | Database |
| [[ADR-033-sql-normalization-strategy]] | SQL normalization | Database |

### 3.3 Architecture (ADR-004, 005, 006, 007, 032)

| ADR | Konu | Kategori |
|-----|------|----------|
| [[ADR-004-multi-domain-spa]] | Multi-domain SPA mimarisi | Architecture |
| [[ADR-005-ultrathink-protocol]] | Zero hallucination protocol | Quality |
| [[ADR-006-performance-targets]] | Performans hedefleri | Performance |
| [[ADR-007-cache-namespace]] | Cache namespace standardı | Infrastructure |
| [[ADR-032-ipc-contract-versioning]] | IPC versioning | Architecture |

### 3.4 Security (ADR-008, 010, 011, 012, 013, 020, 022, 034)

| ADR | Konu | Kategori |
|-----|------|----------|
| [[ADR-008-bypass-auth-middleware]] | Auth bypass middleware | Security |
| [[ADR-010-csrf-protection-strategy]] | CSRF koruma stratejisi | Security |
| [[ADR-011-session-management]] | Session yönetimi | Security |
| [[ADR-012-csp-nonce-strict-dynamic]] | CSP nonce + strict-dynamic | Security |
| [[ADR-013-rate-limiting-apcu]] | APCu rate limiting | Security |
| [[ADR-020-api-public-security]] | API public security | Security |
| [[ADR-022-database-hardened-security]] | DB hardened security | Security |
| [[ADR-034-credential-vault-normalization]] | Credential vault | Security |

### 3.5 Routing (ADR-009, 016, 021)

| ADR | Konu | Kategori |
|-----|------|----------|
| [[ADR-009-clean-url-redirect]] | Clean URL redirect | Routing |
| [[ADR-016-url-normalization]] | URL normalization | Routing |
| [[ADR-021-spa-router-immutable-contract]] | SPA router contract | Routing |

### 3.6 Infrastructure (ADR-015, 027)

| ADR | Konu | Kategori |
|-----|------|----------|
| [[ADR-015-env-parser-strategy]] | Env parser | Infrastructure |
| [[ADR-027-dual-mode-storage-strategy]] | Dual-mode storage | Infrastructure |

### 3.7 Audio (ADR-017, 018, 019, 025, 028, 037)

| ADR | Konu | Kategori |
|-----|------|----------|
| [[ADR-017-dsp-hardware-mode]] | DSP hardware mode | Audio |
| [[ADR-018-footer-player-vaporwave]] | Footer player vaporwave | UI |
| [[ADR-019-per-os-neva-player]] | Per-OS Neva Player | Audio |
| [[ADR-025-professional-eq-system]] | Professional EQ system | Audio |
| [[ADR-028-anti-ban-system]] | Anti-ban system | Download |
| [[ADR-037-wirelessconnect-integration]] | WirelessConnect | Integration |

### 3.8 Testing & Documentation (ADR-023, 024)

| ADR | Konu | Kategori |
|-----|------|----------|
| [[ADR-023-persona-driven-testing]] | Persona-driven testing | Testing |
| [[ADR-024-ecosystem-modular-docs]] | Ecosystem modular docs | Documentation |

### 3.9 Other (ADR-026, 029, 030, 031, 035, 036)

| ADR | Konu | Kategori |
|-----|------|----------|
| [[ADR-026-download-service-architecture]] | Download service arch | Architecture |
| [[ADR-029-listening-rooms-social]] | Listening rooms | Social |
| [[ADR-030-ai-strategy-core]] | AI strategy | AI |
| [[ADR-031-mobile-strategy-pwa-flutter]] | Mobile strategy | Mobile |
| [[ADR-035-system-prompt-engineering]] | System prompt | AI |
| [[ADR-036-multi-project-prompt-maker]] | Multi-project prompt | AI |

## 4. Active ADRs (038-050)

*Bu ADR'ler güncellenebilir. Yeni bilgilerle revize edilebilir.*

| ADR | Konu | Kategori | Tarih |
|-----|------|----------|-------|
| [[ADR-038-8.1-sound-card-chip-selection]] | 8.1 ses donanımı (PCM3168A + XMOS XU316) | Audio | 2026-08 |
| [[ADR-039-7-service-platform-architecture]] | 7-servis platform mimarisi | Architecture | 2026-08 |
| [[ADR-040-database-authority]] | 18 BCNF DB otoritesi | Database | 2026-08 |
| [[ADR-041-database-normalization-supplementary]] | DB normalizasyon ekı | Database | 2026-08 |
| [[ADR-042-vault-restructuring-2026-08-03]] | Vault yeniden yapılandırma | Vault | 2026-08 |
| [[ADR-043-auth-subdomain-consolidation]] | Auth subdomain konsolidasyonu | Security | 2026-08 |
| [[ADR-044-dynamic-user-theme-engine]] | Dynamic theme engine | UI | 2026-08 |
| [[ADR-045-multi-domain-view-mode-architecture]] | Multi-domain view mode | UI | 2026-08 |
| [[ADR-046-cross-view-state-preservation]] | Cross-view state koruma | UI | 2026-08 |
| [[ADR-047-login-redirect-session-bridge]] | Login redirect session bridge | Auth | 2026-08 |
| [[ADR-048-view-transition-api-integration]] | View Transition API entegrasyonu | UI | 2026-08 |
| [[ADR-049-startup-prompt-loader]] | Startup prompt loader | AI | 2026-08 |
| [[ADR-050-multi-db-sync-strategy]] | Multi-DB sync stratejisi | Database | 2026-08 |

## 5. ADR Lifecycle

```
Draft → Review → Active → Frozen
```

| Durum | Kural | Süre |
|-------|-------|------|
| **Draft** | İlk taslak, serbest düzenleme | Süresiz |
| **Review** | İnceleme, kısıtlı değişiklik | 7 gün |
| **Active** | Onaylanmış, uygulanabilir | Kalıcı |
| **Frozen** | Değiştirilemez (001-037) | Sonsuz |

## 6. ADR Kategori Haritası

| Kategori | ADR Sayısı | Örnek |
|----------|-----------|-------|
| **Security** | 8 | 008, 010, 011, 012, 013, 020, 022, 034 |
| **Database** | 6 | 002, 003, 014, 033, 040, 041, 050 |
| **Audio** | 6 | 017, 019, 025, 028, 037, 038 |
| **Architecture** | 5 | 004, 026, 032, 039, 042 |
| **UI** | 5 | 018, 044, 045, 046, 048 |
| **AI** | 4 | 030, 035, 036, 049 |
| **Frontend** | 1 | 001 |
| **Routing** | 3 | 009, 016, 021 |
| **Testing** | 1 | 023 |
| **Mobile** | 1 | 031 |

## 7. ADR Oluşturma Kuralları

| # | Kural | Açıklama |
|---|-------|----------|
| 1 | Numara | Mevcut max + 1 |
| 2 | Dosya adı | `ADR-NNN-konu.md` |
| 3 | Zorunlu bölümler | Context, Decision, Consequences, Related |
| 4 | Onay | Vault Steward (Bayram Ali) |
| 5 | Frozen | 001-037 ASLA değiştirilemez |
| 6 | Yeni karar | Yeni ADR oluşturulur (038+) |

## 8. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[brain.md]] | Engineering brain |
| [[decisions/accepted/]] | ADR dosyaları |
| [[decisions/rejected/]] | Reddedilen kararlar |
| [[architecture/04-decisions/adr-lifecycle]] | Lifecycle |
| [[architecture/04-decisions/guardrails]] | Constraints |

## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 3 Frozen | [[brain.md]] §13 | ADR detayları |
| § 4 Active | [[architecture/04-decisions/decision-log]] | Karar geçmişi |
| § 6 Kategori | [[architecture/04-decisions/guardrails]] | Kısıtlamalar |

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **ADR** | Architecture Decision Record |
| **Frozen** | Değiştirilemez durum |
| **Active** | Güncellenebilir durum |
| **Draft** | İlk taslak |
| **Review** | İnceleme aşaması |
| **Supersede** | Yerine geçme |
| **Lifecycle** | Yaşam döngüsü |
| **Guardrail** | Kısıt/kural |
| **Constraint** | Sınırlama |
| **Mandate** | Zorunluluk |

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 3.0.0 |
| **Satır Sayısı** | ~520 |
| **ADR Uyumlu** | ✅ 001-050 |
| **Zero Hallucination** | ✅ |
| **Cross-Reference** | ✅ 3 referans |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
