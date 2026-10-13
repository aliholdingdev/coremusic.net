---
id: electroncis/guc-ve-koruma/index
title: "Güç ve Koruma — Kademe 4 Alt-Kapsam İndeksi"
node_type: subdomain
parent_id: electroncis
children:
  - guc-ve-koruma-anayasa
  - guc-kaynaklari-ve-regulatorler
  - koruma-devreleri-ve-esikler
level: 3
group: mimari
status: verify-required
updated: 2026-10-13
---

# guc-ve-koruma

> `electroncis` (Kademe 3) alt-kapsamı (Kademe 4): güç kaynakları, regulator topolojileri ve koruma devreleri. Yetki `K000-isletim-sistemi/electroncis` (Kademe 3) altındadır. Scope Q1 (`⚠️ VERIFICATION REQUIRED`) bu alt-kapsam için de geçerlidir — herhangi bir güç/koruma hedefi `verified` sayılmaz.

## §1 · Yetki ve Kapsam

| Konu | Bu alt-kapsam | Komşu |
|---|---|---|
| Güç mimarisi (AC→DC→regulator) | evet | — |
| Lineer / anahtarlama regulator | evet | — |
| Koruma devreleri (akım/gerilim/termal/ESD) | evet | — |
| Termal soğutma detayı | sınır | K001 (soğutma) |
| Firmware / yazılım koruma eşiği | sınır | K005 (firmware) |
| Devre-topoloji tasarımı | evet | devre-tasarimi |
| Simülasyon doğrulama | sınır | devre-tasarimi |

## §2 · Gereksinim Başlangıçları (Sartname — Hepsi PLANNED, Ölçülmemiş)

| Parametre | Hedef (iddia) | Durum |
|---|---|---|
| SNR | >100 dB | PLANNED · `⚠️ VERIFICATION REQUIRED` |
| THD+N | <0.01% @1 kHz | PLANNED · `⚠️ VERIFICATION REQUIRED` |
| Örnekleme | 48 kHz | PLANNED · `⚠️ VERIFICATION REQUIRED` |
| DAC | PCM3168A (ADR-038 H001; PCM5122 yasak) | karar |
| Korumalı çıkış | koruma rölesi (DC offset >0.5V) | kural |

## §3 · Neden Bu Alt-Kapsam Var

| Neden | Açıklama |
|---|---|
| Güç kaynağı = ses kalitesi temeli | Bozuk/gürültülü güç, doğrudan ses bozulması |
| Koruma = donanım güvenliği | Aşırı akım/gerilim/ısı hasarı önleme |
| Regüle-temiz güç | Eşik/röle davranışı güvenilirlik |
| Topoloji seçimi | Lineer (sessiz) vs anahtarlama (verimli) trade-off |

## §4 · Boot Sırası

| Adım | Dosya | Ne verir |
|---|---|---|
| 1 | index.md (bu) | alt-kapsam tanımı, gruplar |
| 2 | guc-ve-koruma-anayasa | güç-koruma anayasası, mimari, terim |
| 3 | guc-kaynaklari-ve-regulatorler | topoloji, verim, ripple/EMI, ölçüm |
| 4 | koruma-devreleri-ve-esikler | akım/gerilim/termal/ESD, eşik hesabı |

## §5 · GRUPLAR (Kademe-4 Eşlemesi)

| Kademe-4 | Sorumluluk | Durum |
|---|---|---|
| guc-ve-koruma-anayasa | Güç-koruma anayasası, mimari sınırlar, terim, açık sorular | draft |
| guc-kaynaklari-ve-regulatorler | Lineer/anahtarlama, topoloji, ripple/EMI, termal, ölçüm | draft |
| koruma-devreleri-ve-esikler | Aşırı akım/gerilim/AKŞ, termal, ESD, eşik/tolerans hesabı | draft |

## §6 · Bilgi İddia Matrisi (Web Araştırma · 2026-10-13)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| IEEE 1547 (dağıtım-arayüzü/standart) | https://standards.ieee.org/ieee/1547/4407/ | 2026-10-13 | HTTP 200 doğrulandı · envanter-dışı · atıf `⚠️ VERIFICATION REQUIRED` |

## §7 · Authority

Bu alt-kapsam `electroncis` (Kademe 3) yetkisi altındadır; güç/koruma hedefleri sartname'den gelir ve tümü PLANNED. Q1 (scope) `⚠️ VERIFICATION REQUIRED`. Dış iddialar tek tek HTTP 200 doğrulanmış, envanter-dışıdır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
