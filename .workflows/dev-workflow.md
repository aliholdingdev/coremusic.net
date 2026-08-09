---
title: "CoreMusic — Development Workflow (Bug Fix + New Feature)"
type: workflow
category: development
updated: 2026-08-06
status: active
version: 1.0.0
---

# Development Workflow

## Purpose
Hata düzeltme ve yeni özellik geliştirme için tek iş akışı.

## Workflow Steps

### 1. Gereksinim / Hata Analizi
- Kullanıcı gereksinimlerini topla VEYA hata mesajını analiz et
- Etkilenen dosyaları belirle
- İlgili ADR'leri kontrol et

### 2. Tasarım
- Mimari tasarımı yap (layer violations kontrol)
- API tasarımını oluştur (varsa)
- Test senaryolarını planla

### 3. Planlama
- Zero Code Before Plan kuralı (ADR-007)
- Görevleri dağıt (MSA limit = 15 dosya)
- Bağımlılıkları belirle

### 4. Uygulama
- Kodlamayı yap (strict_types, prepared stmt)
- Testleri yaz (min %80 coverage)
- Dokümantasyonu oluştur

### 5. Doğrulama
- Unit testleri çalıştır
- Regression testi çalıştır
- Eski davranışın korunduğunu doğrula
- Güvenlik kontrolü yap (CSRF, CSP, session)

### 6. Deployment
- Staging'de test et
- Production'a deploy et
- Monitoring alerts kur

## Yasaklar
- Zero Code Before Plan (ADR-007)
- Layer violation (L0→L3 import)
- `SELECT *` kullanımı
- Hardcoded secret
- `innerHTML` kullanımı (DOMParser + TrustedTypes)

## Related Files
- `.ai/WORKFLOW.md`
- `.ai/decisions/accepted/ADR-007-cache-namespace`
- `.ai/AGENTS.md`

## Activation
- "bug", "fix", "hata", "düzelt", "feature", "özellik", "yeni", "ekle"
