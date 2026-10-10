---
title: "K000-isletim-sistemi-turu/oturum-ses-yollari"
type: architecture
category: layer-detail
date: 2026-10-10
status: planned
version: 1.0.0
authority: SSOT
---

# Oturum & Ses Yolları

> **PLANNED — içerik YOK (2026-10-10)** · eşik/sayı/ölçüm bu dosyada **uydurulmaz** (şablon §7.4).

## §1 Kimlik

- Dosya: `oturum-ses-yollari.md` · Klasör: `isletim-sistemi-turu/` · Statü: **planned** · Tarih: 2026-10-10.
- Üst: `[[index]]` · Kardeş: `[[ses-yollari]]` · sınır komşusu: `[[../isletim-sistemi-security/index]]` (oturum ZEMİNİ).

## §2 Amaç

Bu dosya kapandığında hangi soruyu cevaplayacaktır:

- Oturum (session) durumu ile ses yolu durumu nasıl kesişir (çalma sürerken oturum bitimi)?
- Platform başına oturum davranışı (tarayıcı arka planı, TV uyku modu, mobil kesinti) nedir?
- Oturum ZEMİNİ (K000) ile politika (K006) sınırı bu kesişimde net midir?

## §3 Planlanan İçerik Dalları

| # | Dal | Kanıt kaynağı |
|---|---|---|
| 1 | Oturum zemini | `shared/src/Session/` (dosya varlığı glob ile doğrulanacak) |
| 2 | Oturum politikası | ADR-052 · ADR-095 · ADR-011 — dosyalar VAR, okunacak |
| 3 | Mobil oturum/kesinti | `[[ios]]` §4 Audio Session satırı |
| 4 | TV/tarayıcı arka plan davranışı | `[[web]]` · `[[tizen]]` §4 |

## §4 Bağlantılar

`[[index]]` · `[[ses-yollari]]` · `[[../isletim-sistemi-security/index]]` · `[[../isletim-sistemi-plan/adr]]`

**Doldurma koşulu:** oturum kodu okunmadan (`file:line` kanıtı) davranış iddiası yazılmaz.
