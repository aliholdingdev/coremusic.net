---
node_id: K015-media/media-cache
node_type: subdomain
status: draft
parent_id: K015-media
children: []
depends_on: []
evidence:
  - "v2 §2.15 — çocuk adı kaynağı (birebir)"
  - "media-cache-kapsam.md · media-cache-web-arastirma.md (13 URL, 2026-10-16) · media-cache-ornekler.md (PHP 8.4 ×3 + Vanilla JS ×2)"
validation:
  - "İsim v2 §2.15 ile birebir karşılaştırıldı — 2026-10-12"
  - "Grup dosyaları üretildi — 2026-10-16 (K009 format referansı)"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Boyut/süre (TTL) politikası → ⚠️ UNKNOWN (media-master §12/3) — media-cache-kapsam §6/1"
  - "Temizleme stratejisi (LRU/LFU/ARC) → ⚠️ UNKNOWN (media-master §12/8) — media-cache-kapsam §6/2"
  - "offline sahipliği (§7/2) → ⚠️ VERIFICATION REQUIRED — bu dosyada KESİNLEŞTİRİLMEZ"
group: mimari
---


# media-cache — K015-media (kademe-2 · subdomain)

**ELI15:** Medya önbelleği: tekrar indirilmemesi için tutulan geçici veri.

**Sınır (ne DEĞİL):** İndirme K015/downloads · önbellek geneli (medya dışı) başka domain → sınır ⚠️.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler media-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `media-cache-kapsam.md` | Kapsam içi/dışı, 5 durumlu makine (miss/hit/stale/evicted), açık sorular | Kademe-2 |
| kanit | `media-cache-web-arastirma.md` | Canlı URL doğrulama havuzu (13 URL), 7 cross-check, erişim kaydı | Kademe-2 |
| kod | `media-cache-ornekler.md` | PHP 8.4 (setter · PDO · range) + Vanilla JS (durum makinesi · Cache API) | Kademe-2 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | Boyut/süre (TTL) politikası → ⚠️ UNKNOWN (media-master §12/3) | FM open_questions |
| 3 | Temizleme stratejisi (LRU/LFU/ARC) → ⚠️ UNKNOWN (media-master §12/8) | FM open_questions |
| 4 | offline sahipliği (§7/2) → ⚠️ VERIFICATION REQUIRED — kesinleştirilmez | FM open_questions |
| 5 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
