---
title: "Vault Kaynağı: engine.md (Orchestration Engine)"
type: kaynak
raw_path: raw/engine.md
created: 2026-10-06
updated: 2026-10-06
sources: [vault-engine]
ingested: 2026-10-06 18:58
tags: [orkestrasyon, kuyruk, metric]
---

# Kaynak Özeti — engine.md (v21.0.4)

## Genel Özet

11 uzmanlık alanındaki AI ajanların koordinasyonunu ve görev dağıtımını yöneten orkestrasyon motorunun indeksi.

## Ana Fikirler

1. Orkestrasyon bölümleri AGENTS.md'ye işaret eder: Task Dispatch (7 adım), Keyword Routing (11 grup), Handover, Escalation (4 seviye), Health Check (5 durum), Context Lock, Priority (CRITICAL/HIGH/MEDIUM/LOW).
2. Ek içerik: Task Queue kuyruk yapısı, Agent Communication kanalları, Metrics & Monitoring, Troubleshooting senaryoları.
3. Teknoloji yığını politikası (Dynamic Tech Stack, §9), Checkpoint & Uncertainty protokolü (§10), Faz yürütme planı (§12), Quality Report (§13).
4. Vault sağlığı için bash/PowerShell kontrol snippet'leri içerir (MD sayfa, kırık referans, domain varlık, ADR sayım, PNG 19).

## Önemli Alıntılar/Veriler

- Authority: "Orchestration Index - SSOT: .ai/AGENTS.md (v22.0.4)", updated 2026-10-01

## Bu Kaynaktan Oluşan/Güncellenen Wiki Sayfaları

[[vault-engine]] (yeni)
