---
node_id: K011-application/desktop
node_type: subdomain
status: draft
parent_id: K011-application
children:
  - "desktop-kapsam.md"
  - "desktop-dogrulama.md"
  - "desktop-web-arastirma.md"
  - "desktop-ornekler.md"
  - "desktop-bilesenler.md"
depends_on: []
evidence:
  - "v2 §2.11 — çocuk adı kaynağı (birebir)"
validation:
  - "İsim v2 §2.11 ile birebir karşılaştırıldı — 2026-10-12"
  - "5 içerik dosyası yazıldı ve kapılar ölçüldü — 2026-10-15"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "desktop ile electron sınırı → ⚠️ VERIFICATION REQUIRED (§4/6)"
  - "d-desktop.css L4 (1025–1440) ↔ DeviceDetector (1441–2560) çelişkisi → UI Designer handover"
  - "BFF testi yok (DesktopBff/BffLayer grep 0) → QA Engineer handover"
  - "breakpoints.spec.js 1px kayma (1440 vs 1441) → QA Engineer handover"
group: mimari
---

# desktop — K011-application (kademe-2 · subdomain)

**ELI15:** Yerel masaüstü penceresi ve kabuğu (OS seviyesinde pencere, menü, dosya erişimi).

**Sınır (ne DEĞİL):** Electron paketleme K011/electron'da · web tarayıcısı K011/web'de.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler application-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.

---

## §ÇOCUKLAR (5 içerik dosyası — 2026-10-15)

| # | Dosya | group | Kapı (satır) | Durum |
|---|-------|-------|--------------|-------|
| 1 | `desktop-kapsam.md` | mimari | 500 ✓ | yazıldı |
| 2 | `desktop-dogrulama.md` | kanit | 500 ✓ | yazıldı |
| 3 | `desktop-web-arastirma.md` | kanit | 500 ✓ | yazıldı (havuz 5 URL) |
| 4 | `desktop-ornekler.md` | kod | 554 ✓ | yazıldı (4 dil) |
| 5 | `desktop-bilesenler.md` | mimari | 503 ✓ | yazıldı (sayım betiği) |

**URL kuralı:** Yalnız `desktop-web-arastirma.md` URL içerir (havuz 5 php.net/MDN URL); diğer 4 dosyada `http(s)://` = 0.

---

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `desktop-kapsam.md` | Kapsam, sorular, sınırlar | Kademe-2 |
| kanit | `desktop-dogrulama.md` | Doğrulama satırları, test envanteri | Kademe-2 |
| kanit | `desktop-web-arastirma.md` | Havuz tablosu, kesişim betiği | Kademe-2 |
| kod | `desktop-ornekler.md` | PHP/JS/SQL/CSS örnekleri | Kademe-2 |
| mimari | `desktop-bilesenler.md` | Bileşen sayımı, bağımlılık | Kademe-2 |

---

## §AÇIK SORULAR (index özeti)

1. Kademe-3 bounded-context kapsamı → ADR-097 K4 fazı (C) bekliyor.
2. `desktop` ↔ `electron` sınırı → §4/6'da belirsiz.
3. CSS band çelişkisi (1025–1440 vs 1441–2560) → UI Designer'a handover.
4. BFF test boşluğu → QA Engineer'a handover.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
