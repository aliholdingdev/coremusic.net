---
title: "K011 · dto-ve-mapper — index"
type: architecture
kodeme: kademe-2
module: K011-application
parent: dto-ve-mapper
status: active
updated: 2026-10-15
group: mimari
---

# K011 · dto-ve-mapper — index

**Amaç:** Bu klasör, K011-application kademe-2 kapsamında **veri transfer nesneleri (DTO) ve dizi→nesne eşleme (mapper)** katmanının kapsamını, kanıtlarını, bileşen envanterini, kod örneklerini ve doğrulama kapılarını tek noktada toplar. Dosya, repo'da hâlihazırda var olan DTO/mapper yüzeyini belgelemek için üretilmiştir — yeni üretim kodu içermez.

## §Çocuklar

| # | Dosya | Grup | Açıklama |
|---|-------|------|----------|
| 1 | `index.md` | mimari | Bu dosya — klasör özeti, kapı durumu |
| 2 | `dto-ve-mapper-kapsam.md` | mimari | ELI15 kapsam, sınır, akış tabloları, açık sorular |
| 3 | `dto-ve-mapper-dogrulama.md` | kanit | İddia↔Dosya↔Satır, test envanteri, kapı PASS/BLOCK, KPI |
| 4 | `dto-ve-mapper-web-arastirma.md` | kanit | Havuz 5 URL, claim↔repo matrisi, kesişim, reddedilenler |
| 5 | `dto-ve-mapper-ornekler.md` | kod | PHP + Vanilla JS + SQL + CSS (4 dil) repo kanıtlı örnekler |
| 6 | `dto-ve-mapper-bilesenler.md` | mimari | Dizin/dosya envanteri sayımları, bileşen↔akış, bağımlılık |

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|-------|------|---------|----------|
| 1 | Kapsam & Sınır | mimari | ne DEĞİL | Görev başı okunur; kapsam dışı netleştirme |
| 2 | Doğrulama & Kapı | kanit | iddia↔satır | Üretim öncesi kapı; PASS/BLOCK kararı |
| 3 | Web Araştırması | kanit | havuz↔repo | Dış kaynak iddialarının repo ile kesişimi |
| 4 | Kod Örnekleri | kod | 4 dil | Uygulama referansı; anti-pattern YAPMA↔YAP |
| 5 | Bileşenler & Envanter | mimari | sayımlar | Bağımlılık grafiği, boşluk listesi |

## §Kapı Durumu

| Kapı | Durum | Not |
|------|-------|-----|
| Kapsam tanımı | **PASS** | Sınır + kapsam dışı belgelendi |
| Repo kanıtı | **PASS** | 12+ gerçek Dosya·Satır referansı (PHP/JS/SQL/CSS) |
| Havuz doğrulama | **PASS** | 5/5 URL `curl` 200 (2026-10-15) |
| Kesişim | **ATLANIŞ** | Envanter dosyası (`k011-urls.txt`) diskte YOK → HIT hesaplanamaz |
| Test envanteri | **PASS** | `shared/tests/` 9 dizin sayıldı |
| Üretim kodu | **YOK (bilinçli)** | Belge seviyesi — kod yazımı kapsam dışı |

**Genel kapı:** **PASS (belge seviyesi)** · Kod üretimi kapsam dışı olduğundan üretim kapısı uygulanmaz.

## §Boşluklar (özet)

- Mapper sınıfı adı altında **tekilleşmiş** bir `*Mapper.php` repo'da YOK — eşleme `fromArray()`/`toArray()` ile DTO içinde gömülü.
- Ayrık `Hydrator`/`Denormalizer` katmanı YOK.
- Ödeme (payment) DTO'su YOK — bkz. kardeş klasör `odem-servisleri`.

> Yeni çocuk adı kullanıcı onayı ALINDI (2026-10-15, spec §8) — klasör adı v2 §2.11 dışıdır; belge seviyesinde kayıt.

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
