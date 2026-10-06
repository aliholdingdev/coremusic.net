---
title: "Vault Kaynağı: TODO.md"
type: kaynak
raw_path: raw/TODO.md
created: 2026-10-06
updated: 2026-10-06
sources: [vault-todo]
ingested: 2026-10-06 18:58
tags: [todo, öncelik, açık iş]
---

# Kaynak Özeti — TODO.md (v1.0.0)

## Genel Özet

Depodaki gerçek iş kalemlerini (açık commit'ler, eksik testler, vault tutarsızlıkları, karar bekleyenler) öncelik sırasıyla tutan tek liste; session başına CHECKLIST §A3 ile taranır.

## Ana Fikirler

1. P0 — Depo bütünlüğü: 105 değişikliğin sınıflandırılması (63 M / 25 D / 17 ??, ölçüm 2026-09-29 `git status --porcelain`).
2. P0 — Interface→Contract taşıması: `shared/src/Interfaces/**` (14 D) + `shared/src/AI/Contracts/**` (7 D) worktree'de silinmiş; karşılığı `shared/src/Contracts/{AI,Auth,Config,Database,Middleware,Security}/**` (6 dizin) henüz tracked değil → stage veya revert kararı bekliyor.

## Önemli Alıntılar/Veriler

- "105 değişikliði sınıflandır ve commit'e hazırla" (2026-09-29 ölçümü — anlık, güncelliği ⚠ VERIFICATION REQUIRED)

## Bu Kaynaktan Oluşan/Güncellenen Wiki Sayfaları

[[vault-todo]] (yeni)
