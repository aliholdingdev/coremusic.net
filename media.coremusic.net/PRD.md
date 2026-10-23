---
title: "CoreMusic — media.coremusic.net PRD (Medya Arşiv Aracı Gereksinim Belgesi)"
type: prd
category: media
version: 1.0.0
status: active
authority: "Derived — SSOT: [[../PRD.md]] (platform + aile indeksi) · bağ: [[CONTEXT.md]] · [[AGENTS.md]]"
updated: 2026-10-23
---

# media.coremusic.net — PRD

**docType:** prd · **Klasör:** `media.coremusic.net/` · **Yüzey:** #6 (aile indeksi §5) · **Sorumlu:** Backend Architect + Data Engineer (+ Security denetimi)

**Zorunlu Bağlantılar:** [[../PRD.md]] · [[CONTEXT.md]] · [[AGENTS.md]] · [[CLAUDE.md]] · [[WORKFLOW.md]]

**Bağlı platform gereksinimleri (tekrar edilmez, yalnız bağlanır):** PRD-PLT-001 · PRD-PLT-002

---

## 1. Amaç

Bu PRD, `media.coremusic.net/` yüzeyinin **medya arşiv PHP CLI aracı** gereksinimlerini tanımlar: `scan` (tarama + katalog), `audit` (arşiv denetimi), `ingest` (taşıma — dry-run varsayılan). Klasör statik medya servisi değildir; medya dosyası (mp3/flac/jpg) sayısı **0**'dır. Amaç, arşivin **tek disk ekseni** (sanatçı → albüm → parça; tür/dönem/durum = TAG) ile kataloglanması ve master dosyalara dokunulmadan taşma yapılabilmesidir.

> **eli10 (basit):** Bu dosya, müzik arşivini tarayan, denetleyen ve taşıyan üç komutun kurallarını söyler.
> **eli15 (detay):** Ayrı yazıldı çünkü bu yüzey hem veritabanı (katalog) hem master dosya güvenliği
> (dokunulmazlık) taşıdığından kural ihlali geri alınamaz sonuç üretir. Okunması, komut/DB/onay kapılarını
> verir. Yazılması, Data ile Security yetkisini ayırır. Olmazsa taşıma kayıpsız yapılamaz, master riski doğar.

---

## 2. Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `bin/{scan,audit,ingest}.php` (CLI komutları) | `media/` medya ağacı içeriği (salt okunur — dokunulmaz) |
| `src/Media/*.php` (Taxonomy, Validator, mojibake-fix) | DB şeması metni → `.ai/.sql/mysql/media_catalog.sql` (#25) |
| `config/{media.schema.json, taxonomy.json, mojibake-fix.json}` | Faz 3 GUI uygulaması → `docs/faz3-gui-spec.md` (⚠️ ayrı görev) |
| `media_catalog` DB yazımı (scan) + `path_history` | Panel oynatma akışı → home/coreplayer PRD (#5/#20) |

- **Ölçüm:** `media/` klasöründe medya dosyası sayısı **0** (klasör statik servis değil, arşiv aracı). Detaylı envanter `[[CONTEXT.md]]` §1'dedir, burada tekrarlanmaz.
- **Ön koşul:** ADR-092 (dizin ekseni + ULID) — tek disk ekseni; ADR-039 (servis yerleşimi / portlar :5000/:6000).
- **Dokunulmaz:** `media/` ağacı **herkes için salt okunur**; yazma yalnız `ingest.php` üzerinden ve onayla (ADR-092 §5) · master `.flac`/`.wav` byte değişikliği CRITICAL · secret/REDACTED.

> **eli10 (basit):** Kapsam üç komut ve katalog; medya dosyalarının kendisi ve panel dışarıda.
> **eli15 (detay):** Sınır, aracın medyaya doğrudan yazmasını engeller — taşma tek kapıdan ve onayla
> yürür. Sınır yoksa master dosyalar kaybolabilir.

---

## 3. Gereksinimler (PRD-MED-001..006)

| ID | Gereksinim | Kabul Kriteri |
|----|-----------|----------------|
| PRD-MED-001 | `ingest` varsayılan dry-run'dur; gerçek taşıma `--commit` + onayla | Dry-run CSV çıktısı üretilmeden gerçek yazma yok |
| PRD-MED-002 | `media/` ağacı salt okunur; yazma yalnız ingest ile | Diğer komutlarda `media/` yazma çağrısı 0 |
| PRD-MED-003 | Hash kuralı: sha256 yalnız girişte (ingest) üretilir | Scan/audit hash yeniden hesaplamaz; kopya kilidi denetlenir |
| PRD-MED-004 | `media_catalog` ↔ `taxonomy.json` enum senkronu korunur | Drift yok; `SELECT *` yasak; prepared statement |
| PRD-MED-005 | Dizin ekseni + ULID (ADR-092) bozulmaz | Sanatçı→albüm→parça dışı eksen yok; adlandırma 6 kurala uyar |
| PRD-MED-006 | Kapılar kanıtla kapanır | `php -l` 8/8 · `composer lint` · `docs/checklist.md` kanıt satırı |

> **eli10 (basit):** Altı kural: önce dry-run, medya okunur, hash tek yerde, katalog senkron, eksen sabit, kanıt zorunlu.
> **eli15 (detay):** Kimlikler ayrı çünkü taşma hatası veri kaybı üretir ve denetlenebilir olmalıdır.
> Okunması, komut çalıştırmadan önce kontrol verir. Kimliksiz kural "uyarı"ya döner; master riski doğar.

---

## 4. Kabul Kriteri (bu yüzeyin kapıları)

| # | Kapı | Ölçüt |
|---|------|-------|
| 1 | Onay | PRD-MED-001 — dry-run CSV → onay → `--commit` |
| 2 | Dokunulmazlık | PRD-MED-002/003 — `media/` salt okunur; master byte değişmez (CRITICAL tespit) |
| 3 | Veri | PRD-MED-004/005 — enum senkron + ADR-092 ekseni |
| 4 | Kanıt | PRD-MED-006 — `php -l` + `composer lint` + checklist kanıtı |
| 5 | Denetim | Security (kopya kilidi/secret) + QA (kapı) — yapan rol kendi denetimini kapatmaz |

> **eli10 (basit):** Beş kapı: onay, dokunulmazlık, veri, kanıt, bağımsız denetim.
> **eli15 (detay):** Kapılar üretimden ayrıdır; denetim bağımsızdır (bağımsız denetim ilkesi). Okunması,
> kapanış raporu checklist'ini verir. Kapı yoksa taşıma "güvenli" iddiası kanıtsız olur.

---

## 5. ADR Bağları

| Karar | ADR | Yüzeye etkisi |
|-------|-----|---------------|
| Dizin ekseni + ULID + master dokunulmazlık | ADR-092 | PRD-MED-002/005 |
| Servis yerleşimi / portlar | ADR-039 | CLI/daemon bağlamı |
| DB erişimi (ORM yasak, PDO) | ADR-002 | PRD-MED-004 |
| Kayıpsız veri mülkiyeti | PRD-PLT-002 (bağ) | PRD-MED-001/002 |
| Yeni ADR numarası | `.ai/.decisions/index.md` — next: **111** | Eksen/onay değişikliğinde |

> **eli10 (basit):** Taşma ve eksen kararları ADR'dedir; bu PRD yalnız bağ verir.
> **eli15 (detay):** Bağ, frozen ADR metninin tek sürüm kalmasını sağlar.

---

## 6. Risk

| # | Risk | Etki | Azaltma |
|---|------|------|---------|
| 1 | Doğrudan yazma/taşma (dry-run atlanırsa) | Kayıpsız veri kaybı (PRD-PLT-002 ihlali) | PRD-MED-001 — onay kapısı |
| 2 | Master `.flac`/`.wav` byte değişikliği | Geri alınamaz master bozulma | PRD-MED-002/003 + CRITICAL eskalasyon |
| 3 | `taxonomy.json` ↔ `media.schema.json` drift'i | Katalog/enum tutarsızlığı | PRD-MED-004 + QA kanıtı |
| 4 | Hash'in ingest dışında üretilmesi | Kopya kilidi zayıflığı | PRD-MED-003 + Security denetimi |
| 5 | `.gitignore` negasyonunun unutulması | `media/`/`catalog/` git'e girer | MO + Security denetimi (senaryo 5) |

> **eli10 (basit):** En büyük tehlike: onaysız taşma ve master dosyaya dokunma.
> **eli15 (detay):** Her riskin azaltması bir kapıya bağlıdır; kapı atlanırsa risk geri döner.

---

## 7. Sonraki Adım

1. #25 (`.ai/.sql` PRD) ile `media_catalog` şeması (9 tablo + `v_asset_search`) çapraz kontrol edilir.
2. Faz 3 GUI (`docs/faz3-gui-spec.md`) uygulama kararı bu PRD §2 "⚠️ ayrı görev" kaydına bağlı kalır.
3. Port netleştirme (ADR-039 :5000/:6000) açık kalmışsa `checklist` VR maddesi olarak taşınır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
