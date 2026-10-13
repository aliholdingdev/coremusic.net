---
node_id: K001-donanım
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - donanım-kernel
  - donanım-core
  - donanım-api
  - donanaım-type
depends_on:
  - K000-isletim-sistemi
evidence:
  - "v2 §2.2 mini-ağaç — coremusic-katmanli-mimari-agaci-v2.md satır 170-186"
  - "https://www.ti.com/lit/gpn/TPA3255 (erişim 2026-10-12)"
  - "https://github.com/EliMattingly22/TPA3255_ClassD_PBTL (erişim 2026-10-12)"
  - ".ai/AGENTS.md §4 (Audio Hardware Engineer: PCM3168A · AK4458 · Class AB)"
validation:
  - "Çocuk listesi v2 §2.2 ile birebir karşılaştırıldı — 4/4 eşleşme (2026-10-12)"
  - "donanaım-type altında 3 bounded-context (hdd · audio · ssd) ayrı ayrı açıldı (2026-10-12)"
open_questions:
  - "v2 §3.1: K001 ile K003 arasında K002 yok (ID GAP) → ⚠️ VERIFICATION REQUIRED"
  - "TPA3255 PVDD aralığı: görev iddiası 24–52 V, TI datasheet 18–53.5 V → ⚠️ VERIFICATION REQUIRED"
  - "donanım-kernel ile donanım-core sınırı v2'de tanımsız → ⚠️ VERIFICATION REQUIRED"
  - "donanaım-type yazımı kaynaktan birebir korunmuştur (düzeltme YASAK) — bilgi notudur"
group: mimari
---

# K001-donanım — Kayıt

**ELI15 (v2 §2.2, birebir):** Kasadaki fiziksel parçalar — disk, ses kartı, ekran, kablolar.

> **Alan notu:** depends_on satırı **tasarım iddiasıdır** [TASARIM] — gerekçesi ve
> etiketli tam matris donanım-bagimliliklar.md içindedir.

## Çocuk kataloğu (kademe-2)

| # | Çocuk | Ne işe yarar (1 satır) | Kanıt |
|---|---|---|---|
| 1 | donanım-kernel | Donanıma en yakın donanım katmanı (çekirdek/alt donanım) | ⚠️ yok |
| 2 | donanım-core | Donanım tarafının merkez bileşenleri | ⚠️ yok |
| 3 | donanım-api | Donanıma erişim arayüzü (sürücü/cihaz API'si) | ⚠️ yok |
| 4 | donanaım-type | Donanım türü ayrılan dal — altında 3 alt bağlam | ✔ v2 mini-ağaç |

> 1 satırlıklar **ad kökeninden** türetilmiştir; v2 §2.2 tek tek tanım yazmaz → içerik
> VERIFICATION REQUIRED'dır. yazım (donanaım-type) **dokunulmaz** (v2 §3 KURAL 1).

### Kademe-3 (donanaım-type altı — bounded-context)

| # | Alt bağlam | Ne işe yarar (1 satır) | Kanıt |
|---|---|---|---|
| 1 | hdd | Disk depolama donanımı | ⚠️ yok |
| 2 | audio | Ses kartı / ses donanımı | ✔ §4 (PCM3168A · AK4458) |
| 3 | ssd | Katı hal depolama donanımı | ⚠️ yok |

## Durum

| Öğe | Değer |
|---|---|
| status | draft |
| Kademe | 2 · domain (4 alt klasör + 3 alt bağlam) |
| Master dosyası | donanım-anayasa.md (v2'de yoktu — ADR-097 K2 istisnası) |
| Ek dosyalar | donanım-bagimliliklar.md · donanım-dogrulama.md |
| Bloklayan açık soru | PVDD aralığı · core/kernel sınırı · K002 GAP (hepsi ⚠️) |

**Doğrulama kapısı:** donanım-dogrulama.md §2 ölçütlerinin tamamı → status: verified.

---

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `donanım-anayasa.md` | Anasözleşme ve bağlayıcı mimari kurallar | Kademe-1 |
| mimari | `donanım-bagimliliklar.md` | Katman bağı ve used_by haritası | Kademe-1 |
| mimari | `donanım-evrensel-dil.md` | TR↔EN terim karşılık sözlüğü | Kademe-1 |
| mimari | `donanım-kararlar.md` | Karar, alternatif ve gerekçe kaydı | Kademe-1 |
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-1 |
| kod | `donanım-kod-ornekleri.md` | Gerçek repo kodu, dosya ve satır | Kademe-1 |
| kod | `donanım-test-stratejisi.md` | Test kapsamı ve kabul kriterleri | Kademe-1 |
| kanit | `donanım-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-1 |
| kanit | `donanım-riskler.md` | Risk, etki, olasılık ve azaltım planı | Kademe-1 |
| kanit | `donanım-web-arastirma.md` | Sistemli resmi kaynak tarama kaydı | Kademe-1 |

---

**Authority:** Bayram Ali / Vault Steward

