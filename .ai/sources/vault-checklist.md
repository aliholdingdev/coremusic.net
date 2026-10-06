---
title: "Vault Kaynağı: CHECKLIST.md (Session Checklist)"
type: kaynak
raw_path: raw/CHECKLIST.md
created: 2026-10-06
updated: 2026-10-06
sources: [vault-checklist]
ingested: 2026-10-06 18:58
tags: [session, checklist, yaşam döngüsü]
---

# Kaynak Özeti — CHECKLIST.md (v1.0.0)

## Genel Özet

Session yaşam döngüsünün (başlangıç → orta → kapanış) her oturumda aynı şekilde, kanıtlanabilir biçimde uygulanmasını sağlayan kontrol listesi SSOT'u.

## Ana Fikirler

1. A fazı (session başlangıcı, boot'tan hemen sonra): A1 vault boot protokolü (CLAUDE §16 kanonik 13 dosya P0→P1→P2, boot ≤36s), A2 mevcut durum doğrulama (MEMORY §6 5 sorusu + git status + git log -10), A3 açık iş taraması (TODO.md), A4 hedef netleştirme (Guardrail #1 Zero Code Before Plan).
2. Her madde tek action + ölçüt + kaynak referansı taşır.
3. Kapanışta .workflows/vault-sync.md Amaça 8 satır ile değişen dosyalar güvenceye alınır.

## Önemli Alıntılar/Veriler

- "yarım iş, doğrulanmamış iddia ve vault tutarsızlığı session başına en az bir kez kontrol edilir"
- Ölçüt örnekleri: "boot →36s", "cevap [[vault-log]]'a 1 satır INFO"

## Bu Kaynaktan Oluşan/Güncellenen Wiki Sayfaları

[[vault-checklist]] (yeni)
