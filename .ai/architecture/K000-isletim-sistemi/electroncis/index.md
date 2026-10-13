---
node_id: K000-isletim-sistemi/electroncis
node_type: subdomain
status: verify-required
parent_id: K000-isletim-sistemi
children: [devre-tasarimi, guc-ve-koruma]
depends_on: []
evidence:
  - ".ai/architecture/K000-isletim-sistemi/os-master.md §3 · §8"
  - ".ai/architecture/K000-isletim-sistemi/os-bagimliliklar.md §5"
  - ".ai/electronic/** (60 md · 10.323 sat · ölçüm 2026-10-13)"
validation: "2026-10-13 · kök index 40–100 satıra indirildi · children 2 alt-dal disk kanıtlı · kapsam Q1 hâlâ açık"
open_questions:
  - "Q1: electroncis kapsami — elektronik donanim mi (K000 altinda), K001 ile sinir nasil? → ⚠️ VERIFICATION REQUIRED"
  - "Q1c: K002-altyapi ID GAP bu dali etkiliyor mu? → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# electroncis — Kademe-2 Kayıt

> **Yazım dokunulmaz:** `electroncis` (v2 §3 KURAL 1 — `electronics` DEĞİL, düzeltilmez).
> Kapsam tanımı v2 §2.1'de YOK; bu dosya tahmin üretmez, yalnız kanıt + soru taşır.

**ELI15:** İşletim sisteminin elektronik donanımla yüzleştiği söylenen alt dal — ama bu cümlenin
kaynağı yalnızca **ad kökenidir, teknik kanıt değildir** (v2 §2.1'de tanım yok).

**Kaynak içerik `.ai/electronic/**` (60 dosya · 10.323 sat) — orada derinleştiriliyor.**

| Öğe | Değer |
|---|---|
| parent_id | K000-isletim-sistemi |
| Kademe | 2 · subdomain (alt-dallar Kademe-3 · içerik Kademe-4) |
| Master referansı | `os-master.md` §3 · §8 |
| Alt kademe | `children: 2 alt-dal (disk kanıtlı)` |
| Durum | `verify-required` (kapsam Q1 doğrulanmadı) |

## §1 Kapsam durumu

| # | Alan | Değer | Etiket |
|---|---|---|---|
| 1 | Bu dal neyi kapsıyor? | **UNKNOWN** | ⚠️ VERIFICATION REQUIRED |
| 2 | K000 içindeki işlevi | **UNKNOWN** | ⚠️ VERIFICATION REQUIRED |
| 3 | K001-donanım ile sınırı | **UNKNOWN** — boşluk kayıtlı | ⚠️ VERIFICATION REQUIRED |
| 4 | Alt-dal 1: `devre-tasarimi` | `draft` (Kademe-3) | disk kanıtlı |
| 5 | Alt-dal 2: `guc-ve-koruma` | `draft` (Kademe-3) | disk kanıtlı |

## §2 Sınır komşuları (aday — kesin değil)

| Komşu | İlişki | Etiket |
|---|---|---|
| K001-donanım | fiziksel parçalar · PCB · kart · cihaz | `[TASARIM]` aday |
| K004-dsp | DSP algoritması (dijital filtre · EQ) | `[TASARIM]` aday |
| K005-firmware | cihaz boot-time kendi yazılımı · koruma döngüsü | `[TASARIM]` aday |
| `isletim-sistemi-kernel` | sürücü/çekirdek sınırı | `[TASARIM]` aday |
| K003-audio | ses API'si yüzeyi (ASIO/WASAPI) | `[TASARIM]` aday |

> Bu tablo **aday** korelasyondur; Vault Steward Q1 kararı gelene kadar kesin sayılmaz.

## §3 Disk kanıtları

| # | İddia | Dosya : konum |
|---|---|---|
| D1 | Çocuk satırı "electroncis · draft · ⚠️ kapsam tanımsız" | `os-master.md` §3 |
| D2 | Açık soru "electroncis neyi temsil ediyor?" | `os-master.md` §8 (Q1) |
| D3 | Risk kaydı "electroncis tanımsız → K000 ↔ K001 boşluk" | `os-bagimliliklar.md` §5 |
| D4 | Yazım yasağı `electroncis` düzeltilmez | v2 §3 KURAL 1 |

## §4 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q1 | electroncis neyi temsil ediyor? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q1b | Elektronik donanım (K001) mi, OS sürücü katmanı mı? | UNKNOWN | Vault Steward |
| Q1c | K002-altyapi ID GAP bu dalı etkiliyor mu? | ⚠️ VERIFICATION REQUIRED | registry §6 |
| Q1d | 2 alt-dal K001/K004/K005 ile çakışıyor mu? | ⚠️ VERIFICATION REQUIRED | Vault Steward |

**Vault Steward kararı bekliyor (Q1).** Karar gelmeden bu dosyaya kapsam satırı eklenmez.

## İlişkili dosyalar

- `electroncis-kapsam.md` — kapsam `UNKNOWN` + sınır komşuları + alt-dal envanteri
- `electroncis-dogrulama.md` — web/disk kanıt tabloları
- `electroncis-ornekler.md` — repo/OS kod örnekleri
- `devre-tasarimi/index.md` · `guc-ve-koruma/index.md` — 2 Kademe-3 deep-dal indeksi (her biri 4 md)
- `../os-master.md` · `../os-dogrulama.md`

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `devre-tasarimi/` | Şema → simülasyon → PCB girişi · analog/dijital temeller | Kademe-3 |
| mimari | `guc-ve-koruma/` | AC→DC · regulator · akım/gerilim/termal/ESD eşikleri | Kademe-3 |

> Kök `index.md` (Kademe-2) · `electroncis-kapsam.md` (mimari) · `electroncis-ornekler.md` (kod) · `electroncis-dogrulama.md` (kanit) bu tablonun üst kataloğudur; Kademe-4 içerik dosyaları (6 adet) kendi alt-dal `index.md` §GRUPLAR tablolarında listelenir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
