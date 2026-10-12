---
node_id: K020-comilance
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - policies
  - controls
  - verification
depends_on:
  - "K000–K5000 Architecture Registry"
  - K020-adr
evidence:
  - "v2 §2.20 mini-ağaç (K020-comilance → index.md · compliance-master.md · policies/ · controls/ · verification/) — G:/Drive'ım/Projelerim/Project-notes/Bayram Ali Prompt Arşivi & Notlar/Coremusic/coremusic-katmanli-mimari-agaci-v2.md (satır 548–555)"
  - ".ai/architecture/index.md §5 satır 79 (draft) · §6 satır 92 (imla: 'comilance' KORUNUR)"
  - "Kontrol dayanakları: kök AGENTS.md §5 Security · .ai/AGENTS.md §16 Quality Standards · OWASP Top 10:2025 (top10.owasp.org/2025/en)"
validation:
  - "2026-10-12 — v2 §2.20 şekli birebir karşılaştırıldı (5 öğe)"
  - "2026-10-12 — OWASP Top 10:2025 başlık listesi resmi OWASP kaynağından okundu (A01–A10)"
open_questions:
  - "⚠️ ID CONFLICT: 6 klasör K020 önekinde paylaşır (adr/comilance/comere/licence/discovery/recomenataion) — ADR-097 §6 · v2 §3.1"
  - "Uyum kapsamı: KVKK / GDPR / ISO 27001 hangisi geçerli? — tanımlı değil → VERIFY REQUIRED (tahmin yasak)"
  - "'comilance' yazımı kaynağa sadık korunur; 'compliance' olarak düzeltilmez (v2 §3 KURAL 1)"
---

# K020-comilance — Uyum (Compliance) Rafı

## ELI15 — Bu klasör ne işe yarar?

Kurallara **uyulduğunu kanıtlayan kayıtların** tutulduğu raftır. Kurallar başka
yerde yazılır (politikalar, kod kuralları); burada "şu tarihte kontrol ettik,
şu sonuç çıktı" satırları durur. Kanıtı olmayan uyum **uyum sayılmaz**.

## Çocuk kataloğu (v2 §2.20 — birebir)

| Öğe | Tür | Rol | Durum |
|---|---|---|---|
| `compliance-master.md` | dosya | Uyum programının anayasası: alanlar, sorumlular, denetim döngüsü | draft |
| `policies/` | dizin | Politika kayıt iskeleti (`policies/index.md`) | draft |
| `controls/` | dizin | Kontrol listesi — OWASP Top 10:2025 + vault kontrolleri (`controls/index.md`) | draft |
| `verification/` | dizin | Doğrulama turu kayıtları (`verification/index.md`) | draft |

## Durum

- **status:** `draft` — şekil birebir; **hiçbir uyumluluk iddiası kanıtlanmış değil** (denetim turları boş).
- **depends_on:** registry + `K020-adr` (kontroller, kararlardan türetilir).
- **Yerel dosyalar:** `index.md` · `compliance-master.md` · 3 alt `index.md`.

## İlişkili kaynaklar

| Kaynak | Ne verir |
|---|---|
| `[[compliance-master]]` | Uyum alanları + denetim döngüsü |
| `[[controls/index]]` | OWASP Top 10:2025 haritası (A01–A10) |
| `[[../K020-licence/index]]` | Bağımlılık lisans uyumu (farklı alan — burada değil) |
| `@.ai/CLAUDE.md` §6 · `@.ai/AGENTS.md` §16 | Vault LINT + kalite standartları |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
