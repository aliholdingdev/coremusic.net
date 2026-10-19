---
node_id: K011-application/electron
node_type: subdomain
status: draft
parent_id: K011-application
children:
  - K011-application/electron/electron-kapsam.md
  - K011-application/electron/electron-dogrulama.md
  - K011-application/electron/electron-web-arastirma.md
  - K011-application/electron/electron-ornekler.md
  - K011-application/electron/electron-bilesenler.md
depends_on: []
evidence:
  - "v2 §2.11 — çocuk adı kaynağı (birebir)"
validation:
  - "İsim v2 §2.11 ile birebir karşılaştırıldı — 2026-10-12"
  - "children: 5 dosya — kapsam üretildi, kalan 4 dosya PLANNED (2026-10-15)"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Electron bir ürün mü, desktop'ın paketleme yolu mu? → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# electron — K011-application (kademe-2 · subdomain)

**ELI15:** Electron çatısıyla paketlenen masaüstü kabuğu (v2 §2.11: 'Electron çatısı').

**Sınır (ne DEĞİL):** İşletim sistemi penceresinin sahibi değildir; paketleme çatısıdır (tasarım iddiası).

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler application-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.

---

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `electron-kapsam.md` | Sınır, E1-E20 checklist, PLANNED akış | Kademe-2 |
| kanit | `electron-dogrulama.md` | Kapı ölçümü ve negatif kanıt | Kademe-2 |
| kanit | `electron-web-arastirma.md` | Resmi kaynak araştırması (havuz) | Kademe-2 |
| kod | `electron-ornekler.md` | Çok dilli kod örnekleri (PLANNED etiketli) | Kademe-2 |
| mimari | `electron-bilesenler.md` | Bileşen envanteri | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

