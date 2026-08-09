---
type: workflow
category: new-feature
title: "New Feature Workflow"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# New Feature Workflow

**Zorunlu Bağlantılar:** [[WORKFLOW.md]] · [[CLAUDE.md]] · [[AGENTS.md]]

## 1. Amaç

Yeni özelliklerin gereksinimlerinden başlayarak planlanmasını, uygulanmasını ve test edilmesini sağlayan iş akışı.

## 2. Adımlar

| # | Adım | Aksiyon | Sorumlu |
|---|------|---------|---------|
| 1 | Gereksinimler | Özellik gereksinimlerini tanımla | Product Owner |
| 2 | ADR Kontrolü | İlgili ADR'leri kontrol et | [[backend-architect]] |
| 3 | Planlama | Mimari planı hazırla | İlgili ajan |
| 4 | Hard Gate | Kullanıcı onayını al | Product Owner |
| 5 | Kodlama | Kodlamaya başla | İlgili ajan |
| 6 | Test | Testleri yaz ve çalıştır | [[qa-engineer]] |
| 7 | Vault-sync | Vault'u güncelle | [[master-orchestrator]] |
| 8 | Deployment | Production'a dağıt | [[devops-engineer]] |

## 3. Yaşam Döngüsü (20 Faz)

### 3.1 Vizyon & Analiz (Faz 1-6)

| Faz | Amaç |
|-----|------|
| 1 | Vizyon tanımı |
| 2 | Domain-Driven Design |
| 3 | Use case'ler |
| 4 | Akış diyagramları |
| 5 | Bilgi mimarisi |
| 6 | Rekabet analizi |

### 3.2 Teknik Mimari (Faz 7-9)

| Faz | Amaç |
|-----|------|
| 7 | **Mimari tasarım (HARD GATE)** |
| 8 | Diyagramlar |
| 9 | Klasör yapısı |

### 3.3 Kod & Tasarım (Faz 10-14)

| Faz | Amaç |
|-----|------|
| 10 | Kod standartları |
| 11 | UI/UX tasarımı |
| 12 | API tasarımı |
| 13 | BCNF DB tasarımı |
| 14 | OWASP güvenlik |

### 3.4 Test & DevOps (Faz 15-18)

| Faz | Amaç |
|-----|------|
| 15 | Test stratejisi |
| 16 | CI/CD kurulumu |
| 17 | Dokümantasyon |
| 18 | Monitoring |

### 3.5 MVP & Yol Haritası (Faz 19-20)

| Faz | Amaç |
|-----|------|
| 19 | MVP sürümü |
| 20 | 5 yıllık yol haritası |

## 4. Hard Gate Kuralları

| Faz | Hard Gate | Açıklama |
|-----|-----------|----------|
| Phase 7 | Teknik Mimari Tasarım | Mimari plan onayı |
| ADR Review | ADR Active'e Geçiş | ADR onayı |
| Deployment | Production Deploy | Deploy onayı |

## 5. Başarı Kriterleri

| Kriter | Değer |
|--------|-------|
| Gereksinimler | Tamamlandı |
| Planlama | Onaylandı |
| Kodlama | Tamamlandı |
| Test | ≥%80 coverage |
| Deployment | Başarılı |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
