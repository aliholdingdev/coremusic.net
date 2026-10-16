---
node_id: K011-application/local-runtime
node_type: subdomain
status: draft
parent_id: K011-application
children: []
depends_on: []
evidence:
  - "v2 Â§2.11 â€” Ã§ocuk adÄ± kaynaÄŸÄ± (birebir)"
validation:
  - "Ä°sim v2 Â§2.11 ile birebir karÅŸÄ±laÅŸtÄ±rÄ±ldÄ± â€” 2026-10-12"
open_questions:
  - "Kademe-3 bounded-context kapsamÄ± â†’ âš ï¸ VERIFICATION REQUIRED (faz C)"
  - "BarÄ±ndÄ±rÄ±ldÄ±ÄŸÄ± yer ve Ã§alÄ±ÅŸtÄ±rma biÃ§imi â†’ âš ï¸ VERIFICATION REQUIRED (Â§4/2)"
group: mimari
---

# local-runtime â€” K011-application (kademe-2 Â· subdomain)

**ELI15:** KullanÄ±cÄ± makinesinde Ã§alÄ±ÅŸan uygulama Ã§alÄ±ÅŸma zamanÄ± (Ã§ekirdek + yÃ¼kleyici).

**SÄ±nÄ±r (ne DEÄÄ°L):** Sunucu tarafÄ± K011/server-runtime'de Â· arayÃ¼z Ã§izimi web/desktop/electron'da.

**BaÄŸÄ±mlÄ±lÄ±k (Ã¶zet):** DÃ¼ÄŸÃ¼mÃ¼n tamamÄ± Ã¼st kademeye baÄŸlÄ±dÄ±r; ayrÄ±ntÄ± ve etiketler application-bagimliliklar.md Â§1'dedir. Bu dosyadaki satÄ±rlar TASARIM Ä°DDÄ°ASI'dÄ±r.

**Durum:** draft Â· kademe-3 (bounded-context) ve kademe-4 geniÅŸletmesi ADR-097 K4 fazÄ± (C) bekliyor Â· module'e inilmez.

---

## Â§GRUPLAR

> Ä°ki-ekseni gruplama (ADR-097 Â§5.5 Â· registry Â§8.1) â€” her dosyanÄ±n frontmatter'Ä±nda **9. alan `group`**.

| Grup | Dosya | AmaÃ§ | Kademe |
|---|---|---|---|
| mimari | `index.md` | KlasÃ¶r kÃ¼nyesi ve Ã§ocuk kataloÄŸu | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

