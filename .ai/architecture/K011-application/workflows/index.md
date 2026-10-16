---
node_id: K011-application/workflows
node_type: subdomain
status: draft
parent_id: K011-application
children:
  - K011-application/workflows/workflows-kapsam.md
  - K011-application/workflows/workflows-dogrulama.md
  - K011-application/workflows/workflows-web-arastirma.md
  - K011-application/workflows/workflows-ornekler.md
  - K011-application/workflows/workflows-bilesenler.md
depends_on: []
evidence:
  - "v2 §2.11 — çocuk adı kaynağı (birebir)"
validation:
  - "İsim v2 §2.11 ile birebir karşılaştırıldı — 2026-10-12"
  - "children: 5 dosya — kapsam üretildi, kalan 4 dosya PLANNED (2026-10-15)"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "İş akışı motoru teknolojisi ve sahibi → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# workflows — K011-application (kademe-2 · subdomain)

**ELI15:** Çok adımlı iş akışları: adım sırası, durum ve geri dönüş noktaları (tasarım iddiası).

**Sınır (ne DEĞİL):** Kalıcı iş akışı motoru K012/background-jobs'ta olabilir → ⚠️ UNKNOWN.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler application-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.

---

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `workflows-kapsam.md` | CI/seçim kapıları, dead reference kaydı | Kademe-2 |
| kanit | `workflows-dogrulama.md` | Kapı ölçümü ve sayım çapraz kontrolü | Kademe-2 |
| kanit | `workflows-web-arastirma.md` | Resmi kaynak araştırması (havuz) | Kademe-2 |
| kod | `workflows-ornekler.md` | Çok dilli kod örnekleri (yml + süreç) | Kademe-2 |
| mimari | `workflows-bilesenler.md` | Bileşen envanteri (.workflows + .github) | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

