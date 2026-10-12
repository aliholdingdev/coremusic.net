---
title: "CoreMusic — Enterprise Architecture (Canonical Graph)"
type: diagram
category: architecture
date: 2026-10-12
updated: 2026-10-12
status: draft
version: 1.0.0
authority: "Kaynak: v2 spec §5.1 (canonical graph) · §1.2 (hiyerarşi)"
---

# Enterprise Architecture — Canonical Graph

> v2 spec'teki canonical ASCII graph'ın (§5.1) **tam kaydı** ve hiyerarşi şemasının (§1.2)
> **birebir render'ı**. Yeni node/kenar **eklenmemiştir**; yalnız kaynaktaki çizim taşınmıştır.

## §A Canonical Graph — v2 §5.1 (tam kayıt)

```text
                        ┌─────────────────────┐
                        │      COREMUSIC      │
                        └──────────┬──────────┘
                                   │
                                   ▼
                             ┌───────────┐
                             │ K-LAYER   │
                             └─────┬─────┘
                                   │
                     ┌─────────────┼─────────────┐
                     ▼             ▼             ▼
                  DOMAIN       RUNTIME        DATA
                     │             │             │
                     ▼             ▼             ▼
                 SUBDOMAIN      PROCESS       BOUNDARY
                     │
                     ▼
              BOUNDED CONTEXT
                     │
                     ▼
                  MODULE
                     │
                     ▼
                 COMPONENT
                     │
                     ▼
                  SERVICE
                     │
                     ▼
                  ADAPTER
                     │
                     ▼
               IMPLEMENTATION
```

## §B Hiyerarşi Şeması — v2 §1.2 (birebir)

```text
                        ┌─────────────────────┐
                        │      COREMUSIC      │
                        └──────────┬──────────┘
                                   │
                                   ▼
                        ┌─────────────────────┐
                        │ K000–K5000          │
                        │ ARCHITECTURE        │
                        │ REGISTRY            │
                        └──────────┬──────────┘
                                   │
              ┌────────────────────┼────────────────────┐
              ▼                    ▼                    ▼
           DOMAIN               RUNTIME                DATA
              │                    │                    │
              ▼                    ▼                    ▼
         SUBDOMAIN             PROCESS              BOUNDARY
              │
              ▼
      ┌───────────────┐
      │ BOUNDED       │
      │ CONTEXT       │
      └───────┬───────┘
              │
              ▼
           MODULE
              │
              ▼
        COMPONENT
              │
              ▼
      SERVICE / ADAPTER
              │
              ▼
     IMPLEMENTATION
```

## Lejant

| Sembol | Anlam |
|---|---|
| `┌───┐` · `│ X │` | **Düğüm (node)** — katman / kayıt kutusu |
| `▼` | akış yönü (yukarıdan aşağıya) — tek yön |
| `├────┼────┤` | aynı kademeden üç kol: **DOMAIN · RUNTIME · DATA** |
| `K-LAYER` | kademe 1 — K000–K5000 Architecture Registry |
| `DOMAIN → … → IMPLEMENTATION` | §4.1 sekiz kademe (registry → implementation) |
| `→` / `▼` | **grafik akış**tır; `depends_on` kenarı **DEĞİLDİR** — gerçek kenarlar `dependency-graph.md`'dedir |

## Kaynak Atfı

- **v2 §5.1** "Ana Mimari Graf (v0.1) / CANONICAL GRAPH v0.1" → §A (birebir taşındı).
- **v2 §1.2** "Hiyerarşi Şeması (kuralın kendisi)" → §B (birebir taşındı).
- **v2 §5.2** (Kanıt → Doğrulama Zinciri) → `dependency-graph.md` §D'ye taşındı (kenar doğrulama zinciri).
- **v2 §5.3** (Hedef Okuma Sırası) → `runtime-architecture.md`.
- **v2 §5.4** (K5000 Kayıt Rafı) → §1.2 ile aynı hiyerarşinin iç içe kameradan tekrarı; yeniden yazılmadı.
- Dönüşüm: `index.md` §3 — "Yapı (v2 §1.1 — birebir)". Bu dosya setteki 5 diyagramın 1'incisidir.

## Durum Notu

- `status: draft` — diyagram **kaynak sadakati** taşır, tamamlanmışlık iddiası taşımaz.
- **Kenarlar doldukça genişler:** `depends_on` verisi Band-1 üretiminde doldukça bu graf genişletilecek.
