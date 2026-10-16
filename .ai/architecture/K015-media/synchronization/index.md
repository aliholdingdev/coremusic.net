---
node_id: K015-media/synchronization
node_type: subdomain
status: draft
parent_id: K015-media
children: []
depends_on: []
evidence:
  - "v2 §2.15 — çocuk adı kaynağı (birebir)"
  - "synchronization-kapsam.md (grup: mimari) — §1-§6 kapsam/durum makinesi/veri akışı/hata/açık sorular, 2026-10-16"
  - "synchronization-web-arastirma.md (grup: kanit) — 9 URL canlı doğrulama + C-1..C-4 cross-check, 2026-10-16"
  - "synchronization-ornekler.md (grup: kod) — PHP 8.4 fusion iskeleti + Vanilla JS sync queue UI, 2026-10-16"
validation:
  - "İsim v2 §2.15 ile birebir karşılaştırıldı — 2026-10-12"
  - "§GRUPLAR 4 satıra genişletildi (index + 3 dosya) — 2026-10-16"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Aynı ad iki domain'de: ne ayırıyor → ⚠️ VERIFICATION REQUIRED (§7/3) — açık bırakıldı"
  - "Yön (çift/tek) + çakışma stratejisi → ⚠️ UNKNOWN (kapsam Q-1/Q-2, media-master §12/9/§9.10)"
group: mimari
---


# synchronization — K015-media (kademe-2 · subdomain)

**ELI15:** Medya verisinin (kitaplık/bilgi/ilerleme) eşitlenmesi.

**Sınır (ne DEĞİL):** Dış sistem eşitlemesi K013/synchronization (aynı ad) · ağ K014.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler media-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `synchronization-kapsam.md` | Kapsam içi/dışı, durum makinesi, veri akışı, hata durumları, açık sorular | Kademe-2 |
| kanit | `synchronization-web-arastirma.md` | Canlı URL doğrulama tablosu + çapraz kontrol (WebDAV/CalDAV, CRDT, kuyruk eşitleme) | Kademe-2 |
| kod | `synchronization-ornekler.md` | TASARIM İDDİASI iskeletler: PHP 8.4 çakışma tespiti/fusion + Vanilla JS sync queue UI | Kademe-2 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | Aynı ad iki domain'de: ne ayırıyor → ⚠️ VERIFICATION REQUIRED (§7/3) | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |
| 4 | Yön (çift/tek) + çakışma stratejisi → ⚠️ UNKNOWN (kapsam Q-1/Q-2) | FM open_questions |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
