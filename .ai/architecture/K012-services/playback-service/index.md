---
node_id: K012-services/playback-service
node_type: subdomain
status: draft
parent_id: K012-services
children:
  - "playback-service-anayasa.md"
  - "playback-service-surucu.md"
  - "playback-service-ornekler.md"
  - "playback-service-web-arastirma.md"
depends_on: []
evidence:
  - "v2 §2.12 — çocuk adı kaynağı (birebir)"
  - "playback-service/* — 3 dosya 2026-10-13 kapıdan geçirildi (≥500, § boşluksuz, group, §4.2)"
validation:
  - "İsim v2 §2.12 ile birebir karşılaştırıldı — 2026-10-12"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "playback sahipliği: K012 mi K015 mi? → ⚠️ VERIFICATION REQUIRED (§7/2)"
group: mimari
---


# playback-service — K012-services (kademe-2 · subdomain)

**ELI15:** 'Ne çalıyor / ne çalacak' durumunu (sıra, konum, komut) yöneten servis.

**Sınır (ne DEĞİL):** Asıl ses üretimi K003-audio + K014-media · durum saklama K007-data.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler services-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `playback-service-anayasa.md` | Sınır, session sözlüğü, render zinciri, yazan taraf taraması | Kademe-3 |
| mimari | `playback-service-surucu.md` | Sürüş sırası, varyant kararı, cmConfig, footer akışı | Kademe-3 |
| kod | `playback-service-ornekler.md` | Verbatim kod blokları ve kod analizleri | Kademe-3 |
| kanit | `playback-service-web-arastirma.md` | Q2 sahiplik web kanıtlı seçenek matrisi (10 URL; karar ADR bekler) | Kademe-3 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | playback sahipliği: K012 mi K015 mi? → ⚠️ VERIFICATION REQUIRED (§7/2) | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
