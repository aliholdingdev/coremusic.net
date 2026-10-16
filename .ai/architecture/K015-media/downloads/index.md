---
node_id: K015-media/downloads
node_type: subdomain
status: draft
parent_id: K015-media
children: []
depends_on: []
evidence:
  - "v2 §2.15 — çocuk adı kaynağı (birebir)"
  - "downloads-kapsam.md · downloads-web-arastirma.md (16 URL, 2026-10-16) · downloads-ornekler.md (PHP 8.4 ×2 + Vanilla JS ×2)"
validation:
  - "İsim v2 §2.15 ile birebir karşılaştırıldı — 2026-10-12"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Saklama politikası/sınırı → ⚠️ UNKNOWN"
group: mimari
---


# downloads — K015-media (kademe-2 · subdomain)

**ELI15:** İndirme: dosyanın yerel kopyasının alınması ve saklanması.

**Sınır (ne DEĞİL):** Taşıma K014 · çevrimdışı K015/offline · önbellek K015/media-cache.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler media-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `downloads-kapsam.md` | Kapsam içi/dışı, durum makinesi, veri akışı, açık sorular | Kademe-2 |
| kanit | `downloads-web-arastirma.md` | Canlı URL doğrulama havuzu, cross-check, erişim kaydı | Kademe-2 |
| kod | `downloads-ornekler.md` | PHP 8.4 + Vanilla JS tasarım örnekleri (TASARIM İDDİASI) | Kademe-2 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | Saklama politikası/sınırı → ⚠️ UNKNOWN | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
