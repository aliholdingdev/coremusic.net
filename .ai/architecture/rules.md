---
title: "Architecture — Mimari Kurallar (SSOT)"
type: rules
category: architecture
version: "1.0.0"
status: active
authority: "SSOT: .ai/architecture/rules.md — .ai/architecture/ katmanının tek kural kaynağı"
updated: 2026-10-07
tier: 3
domain: architecture
ssot: true
risk: high
owner: "Vault Steward"
depends-on: [".ai/CLAUDE.md"]
---

# Architecture — Mimari Kurallar (SSOT)

> **Authority:** Bu dosya `.ai/architecture/**` için **tek kural kaynağıdır**. Çelişkide bu dosya kazanır. Anayasa (guardrails, yasaklar): [[CLAUDE]] · Agent registry: [[AGENTS]].
> **Değişiklik riski: high** → insan onayı zorunlu (Q32).

---

## R1 — Kapsam ve Sınır

1. `.ai/architecture/` = K0-K20 + `firmware/` mimari envanteri; başka konu buraya girmez.
1a. **Authority (2026-10-07 kararı):** `.ai/architecture/`'ın tek esası **500 katman (K000–K499) + 10 domain** — giriş `00-master-index.md` (kök `CLAUDE.md` §Mimari v4.0.0 ile hizalı). `k0–k20` dizinleri = **wiki-link landing + envanter evidir** (anlatı içeriği domain/katalogda, bileşen kayıtları `inventory/` part'larında). `00-enterprise-index.md` = derived fiziksel görünüm.
2. **Yasak diziler (SSOT çakışması önlemi):** `architecture/adr/` (→ `.ai/.decisions/`), `architecture/scripts/` (→ `.ai/scripts/`). Bu isimlerle dizin açılmaz.
3. Kaynak kopyalar `_backup/arch-2026-10-06_1057/` ve `.ai.copy2/` **salt-okunur referanstır** — içerik kopyalanmaz/taşınmaz; sıfırdan, kanıtla yazılır (Q8).

## R2 — İsimlendirme (Q31)

1. Dizin adı: `k{0-20}-{türkçe-ad}` — örn. `k0-isletim-sistemi`, `k9-api-routing`, `k20-bom`. Alt bölümler: `01-`, `02-`, `03-`, `04-` iki haneli.
2. Envanter dosyası: `inventory/k{X}-{tür}-part{NN}.md` — tür: `sinif`, `servis`, `tablo`, `endpoint`, `bilesen`…
3. Dizin adları **wiki-link hedefidir, DEĞİŞTİRİLMEZ** (≈489 link + yeni katalog).
4. İçerik dili Türkçe; kod yolları ve teknik adlar özgün (İngilizce) kalır.

## R3 — Dosya İskeleti (Q10/Q19/Q25)

1. Her katman: `index.md` + `README.md` + `AGENTS.md` + `CLAUDE.md` + `WORKFLOW.md` (tam set).
2. `index.md` içeriği: **arc42-tam (12 bölüm) + vault 8-iskelet HİBRİT** (Q25) — arc42 bölüm adları korunur, vault Amaç/Kapsam/Doğrulama/Referanslar gövdesiyle birleşir.
3. Derinleşme (`01-04`): yalnız kritik/aktif katmanlarda açılır (Q19 kademeli derinlik) — her katmanda zorunlu değil.
4. Her dosyanın frontmatter'i **13 alan zorunlu** (Control Plane v2, Q4).

## R4 — Parçalama (Q17/Q21)

1. **Tek MD yasak:** hiçbir konu tek dosyaya gömülür.
2. Envanter **tür-bazlı** bölünür (sınıflar / tablolar / endpointler ayrı dosya).
3. **Min 500 satır/md:** içerik bir dosyada <500 satırsa komşu türle veya `partNN` ile birleşir; \> büyürse `partNN` devam ettirilir.
4. Bağlantı: parçalar birbirine wiki-link ile bağlanır (orphan dosya üretilmez → `orphan-check`).

## R5 — Bileşen ID (Q22)

1. Şema: `Kx.yy.zzz` (katman.bölüm.sıra) — örn. `K5.02.017`.
2. **Tekillik zorunlu** — validator `id-check`; tekrar = ihlal.
3. ID yaşam boyu özeldir: bileşen silinirse kayıt `deprecated` kalır, ID yeniden kullanılmaz.

## R6 — Envanter Alanları (Q26)

12 alan zorunlu: `id` · `ad` · `tip` · `kod-yolu` · `durum` · `A-alanı` · `bagimlilik` · `sahip-agent` · `risk` · `guvenlik` · `kanit-tarihi` · `not`.
Format ve tanım: [[architecture/context]] §4 (derived — bu dosyada tekrar edilmez).

## R7 — SSOT ve Tekrar (§9-§11)

1. Her konunun **tek canonical owner'ı** vardır; katalog/context yalnız link/özet taşır.
2. `ssot: true` yalnız konu sahibinde: `00-master-index.md` (giriş) · `00-enterprise-index` → **`ssot: false` (derived)** · `context` → `ssot: false`.
3. Bağımsız, çelişkili kural/kopya **yasak**; özet yalnız link + tek cümle.

## R8 — Öncelik ve Çelişki Çözümü (§23-§24)

1. Öncelik: `.ai/CLAUDE.md` (anayasa) > `architecture/rules.md` (bu dosya) > katman `index.md` kuralı > görev-notu.
2. Düşük seviye, yükseği **override edemez**.
3. Çelişkide Agent taraf tutmaz: `Kaynak A / Kaynak B / Etki / Otorite analizi / Öneri / Gerekli karar` formatında yukarı eskale eder → insan kararı (Q32).

## R9 — Kaynak Hiyerarşisi (Q11/Q20)

1. İskelet: `.ai/CLAUDE.md` §5 K-matrix (21 katman, 1095 bileşen).
2. Doğrulama: **repo kodu** (sınıf/dosya/tablo/endpoint taraması) + **web araştırması** (deepwiki/exa) + mevcut envanterler (K20 BOM 1.775 satır vb.).
3. Doğrulanamayan satır `UNKNOWN` / `⚠️ VERIFICATION REQUIRED` — uydurma `IMPLEMENTED` **yasak** (Zero-Hallucination).
4. Hedef sayı 5000+ kayıt: doldurulabilirse doldurulur; kanıt yetmezse **DUR + rapor** — sayı için uydurma yok.

## R10 — Onay Matrisi (Q30/Q32)

| Değişiklik | Onay |
|---|---|
| rules.md · ID şeması · SSOT yapısı · kök boot revizyonu | 👤 insan (her seferinde) |
| Katman içeriği (index, envanter) | 👤 **katman başına** onay |
| Katalog/özet satırı, stub durum güncelleme | 👤 onay (bu görevde her şey onaylı) |
| Otomatik | validator çıktıları (rapor üretir, dosya değiştirmez) |

## R11 — Tarama ve Kapılar (Q27→Q29/Q24)

1. **Tam tarama faz kapılarında** yapılır (her kayıt değişiminde değil): Faz 1 k0 pilotu, her katman grubu kapanışında.
2. Tarama: validator 8 check + kod ile `durum`/`kanit-tarihi` doğrulaması → `STALE` işaretleme.
3. Kapı geçmeden sonraki faza geçilmez; kapı sonucu [[log]]'a append edilir.

## R12 — Validator (Q23)

`node .ai/scripts/validate.mjs [--check]` → 8 check: fm · link · ssot · dep (circular) · tier · orphan · id · budget (context ≤660).
Genişletme yalnız `.ai/scripts/validate.mjs` içinden yapılır; **yeni validator scripti yazılmaz**.

## R13 — Versiyon (Q28)

**Tek mimari sürümü:** `00-enterprise-index.md → version` (semver). Katman index'leri kendi `version` alanını taşır ama global sürümü **ezmez**; global bump = katalog sürümü + log kaydı.
