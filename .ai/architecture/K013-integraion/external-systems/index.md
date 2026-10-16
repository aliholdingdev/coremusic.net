---
node_id: K013-integraion/external-systems
node_type: subdomain
status: draft
parent_id: K013-integraion
children:
  - K013-integraion/external-systems/external-anayasa
  - K013-integraion/external-systems/external-sistem-envanteri
  - K013-integraion/external-systems/external-dogrulama
  - K013-integraion/external-systems/external-kod-ornekleri
  - K013-integraion/external-systems/external-web-arastirma
depends_on:
  - K013-integraion/integration-master
  - K013-integraion/api-contracts/openapi
evidence:
  - "v2 §2.13 — çocuk adı kaynağı (birebir)"
  - "14 kanıt URL — external-web-arastirma.md §2 (hepsi 2026-10-16 çekildi)"
  - "2026-10-12 beyanları #2 #3 #4 → external-web-arastirma.md §6 (3/3 kapatıldı)"
validation:
  - "İsim v2 §2.13 ile birebir karşılaştırıldı — 2026-10-12"
  - "5 içerik dosyası ≥350 satır + §GRUPLAR + §Kaynak — 2026-10-16"
  - "Her envanter satırı 10 alanlı (external-anayasa §3) — 2026-10-16"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "1. dalga dış sistem listesi (aktif/beklemede dağılımı) → üst otorite kararı"
  - "YouTube Music kapsam kesinliği (ToS 'e.g.' notu) → kanıt sunuldu, karar üst otoritede"
  - "Spotify/Deezer canlı kayıt akışı → ⚠️ VERIFICATION REQUIRED (canlı test yok)"
group: mimari
---


# external-systems — K013-integraion (kademe-2 · subdomain)

**ELI15:** Tanımlı dış sistemlerin envanteri: hangisi var, hangisi bekliyor, hangisi yasaklı.

**Sınır (ne DEĞİL):** Sistem başına entegrasyon kodu adapters/child'da; envanter yalnız kayıt tutar. Kapsam kararı (aktif/kapsam-dışı) üst otoritededir.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler integraion-bagimliliklar.md §1'dedir. Envanter satırları kanıt URL'siz yazılmaz (anayasa P2).

**Durum:** draft · 5 içerik dosyası 2026-10-16'da eklendi · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §Çocuk Katalog (2026-10-16)

| # | Dosya | Grup | Satır | Amaç |
|---|-------|------|-------|------|
| 1 | `external-anayasa.md` | mimari | 356 | İlkeler (P1–P8), kayıt şeması (§3, 10 alan), kapsam sınırı, karar mercii, anti-pattern'ler |
| 2 | `external-sistem-envanteri.md` | mimari | 353 | 8 sistem kaydı (Spotify/Apple Music/YouTube/SoundCloud/Deezer/Last.fm/MusicBrainz/Discogs) + kimlik matrisi + uç nokta tablosu |
| 3 | `external-dogrulama.md` | kod | 355 | L1–L4 katmanları, GATE-EXT-1..5 exit-code, test senaryo kataloğu, dönemsel denetim, bilinen kısıtlar |
| 4 | `external-kod-ornekleri.md` | kod | 579 | 13 dil (`# Dil: X — Rol: …`), PLACEHOLDER-only, akış kaynaklı |
| 5 | `external-web-arastirma.md` | kanit | 361 | İddia/URL/tarih/durum tablosu (14 kaynak), çekim günlüğü, alıntı kartları, elenen adaylar, 2026-10-12 beyan kapanışı |

> Satır sayıları 2026-10-16 disk ölçümüdür; yeniden ölçümlerde güncellenir.

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `external-anayasa.md` | Kural: kayıt şeması, kapsam sınırı, karar mercii | Kademe-2 |
| mimari | `external-sistem-envanteri.md` | 8 dış sistem kaydı (10 alanlı) | Kademe-2 |
| kod | `external-dogrulama.md` | Doğrulama katmanları + CI kapıları | Kademe-2 |
| kod | `external-kod-ornekleri.md` | 13 dil referans örnekleri (PLACEHOLDER) | Kademe-2 |
| kanit | `external-web-arastirma.md` | URL doğrulama + beyan kapanışı (2026-10-16) | Kademe-2 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | YouTube Music resmi public API yok iddiası → ✅ kanıt sunuldu (`external-web-arastirma.md` §3.3); kesinlik notu + karar üst otoritede | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |
| 4 | 1. dalga dış sistem listesi (aktif/beklemede) | ⏳ Üst otorite kararı |
| 5 | Spotify/Deezer canlı kayıt akışı | ⚠️ VERIFICATION REQUIRED (canlı test yapılmadı) |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
