---
title: "CoreMusic — .workflows Bağlam"
type: context
folder: ".workflows"
category: config
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
---

# .workflows — CLAUDE.md

**Zorunlu Bağlantılar:** · [[../.ai/WORKFLOW.md]] · [[../AGENTS.md]]

## 1. Bağlam

Bu klasördeki dosyalar çalıştırılabilir süreç tanımlarıdır; oturum başlatma, ADR üretimi, vault senkronizasyonu ve güvenlik denetimi bu akışlarla yürütülür. Kök `[[../WORKFLOW.md]]` pointer dosyası bu klasöre yönlendirir.

## 2. Mevcut Durum

| Dosya | Tetikleyici | Çıktı |
|-------|-------------|-------|
| `session-init.md` | Her oturum başı | Vault boot (10+ dosya okuma) |
| `adr-creation.md` | Mimari karar ihtiyacı | İki seri: `.ai/.decisions/` (001-089) veya `.ai/architecture/adr/` (023-026) |
| `vault-sync.md` | Vault değişikliği sonrası | Senkronizasyon + index güncelleme |
| `security-audit.md` | Güvenlik değişikliği | Denetim raporu |
| `deployment.md` | Sürüm çıkışı | Dağıtım onay akışı |
| `hallucination-control.md` | Belirsiz bilgi tespiti | Verification required protokolü |
| `orchestrator-flow.md` | Multi-agent görev | Görev dağıtım grafiği |
| `architecture-write.md` | Mimari dosya yazımı | Batch ≤31 dosya, 0 silme, K0-K20/A0-A5 denetimli rapor |

## 3. Komşu İlişkiler

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../AGENTS.md]] | Kök registry |
| Canonical süreçler | [[../.ai/WORKFLOW.md]] | Süreç anayasası — bu klasör uygulama detayıdır |
| Audit | [[../.ai/log.md]] | Her workflow çalışması append-only kayıt bırakır |
| ADR hedefi | [[../.ai/.decisions/index.md]] | adr-creation.md çıktı hedefi (numaralı seri — mimari seri ayrı: `.ai/architecture/adr/`) |

## 4. Değişiklik Protokolü

1. Yeni workflow ekleme → `[[../.ai/WORKFLOW.md]]` dosya tablosuna kayıt → kullanıcı onayı
2. Mevcut workflow güncelleme → yalnızca ilgili dosyada değişiklik → `[[../.ai/log.md]]` audit
3. Workflow silme onaya tabidir; silinen akışın tetikleyicisi kök dokümanlardan da kaldırılır

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-24
