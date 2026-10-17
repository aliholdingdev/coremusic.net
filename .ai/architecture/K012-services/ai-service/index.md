---
node_id: K012-services/ai-service
node_type: subdomain
status: draft
parent_id: K012-services
children:
  - "ai-service-dogrulama.md"
  - "ai-service-kapsam.md"
  - "ai-service-ornekler.md"
  - "ai-service-web-arastirma.md"
depends_on: []
evidence:
  - "v2 §2.12 — çocuk adı kaynağı (birebir)"
validation:
  - "İsim v2 §2.12 ile birebir karşılaştırıldı — 2026-10-12"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Bağlı olduğu düğüm K006 mı K016 mı → ⚠️ VERIFICATION REQUIRED (§7/1)"
group: mimari
---


# ai-service — K012-services (kademe-2 · subdomain)

**ELI15:** Yapay zeka işlerini diğer servislere sunan arayüz servisi.

**Sınır (ne DEĞİL):** YZ yeteneği K006-yapay-zeka veya K015-AI'dadır; hangisi belli değil.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler services-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| kanit | `ai-service-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-2 |
| mimari | `ai-service-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| kod | `ai-service-ornekler.md` | Uygulama örnekleri ve kalıp gösterimi | Kademe-2 |
| kanit | `ai-service-web-arastirma.md` | Q1 bağlılık web kanıtlı seçenek matrisi (10 URL; karar ADR bekler) | Kademe-2 |

**Kanıt durumu (2026-10-13):** bu alt alanda repo implementasyonu YOK (S5 —
`services-dogrulama.md` §7/4); üç dosya da taslak/kanıt-bekliyor seviyesindedir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
