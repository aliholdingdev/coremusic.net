---
node_id: K012-services/audio-service
node_type: subdomain
status: draft
parent_id: K012-services
children:
  - "audio-service-anayasa.md"
  - "audio-service-surucu.md"
  - "audio-service-ornekler.md"
depends_on: []
evidence:
  - "v2 §2.12 — çocuk adı kaynağı (birebir)"
validation:
  - "İsim v2 §2.12 ile birebir karşılaştırıldı — 2026-10-12"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "K003-audio ile sınır (servis mi motor mu) → ⚠️ VERIFICATION REQUIRED (§7/3)"
group: mimari
---


# audio-service — K012-services (kademe-2 · subdomain)

**ELI15:** Ses katmanına ait servis işleri (kullanıcıya dönük ses ayarları vb.).

**Sınır (ne DEĞİL):** Ses sinyali işleme K003-audio/K004-dsp'dir; bu servis sinyale dokunmaz (iddia).

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler services-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan group**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | K003-audio ile sınır (servis mi motor mu) → ⚠️ VERIFICATION REQUIRED (§7/3) | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
