---
node_id: K019-comilance/policies
node_type: subdomain
status: draft
parent_id: K019-comilance
children: []
depends_on:
  - K019-comilance
evidence:
  - "v2 §2.20 — K019-comilance/ policies/ (alt dosya listesi yok → children: [])"
  - "Dayanaklar gerçek vault dosyaları: kök AGENTS.md §5 · .ai/AGENTS.md §16 · .ai/CLAUDE.md §6"
  - "KVKK dayanakları (2026-10-17 web doğrulama): 6698 sayılı Kanun m.4 işleme ilkeleri · m.6 özel nitelikli veri · m.12 veri güvenliği yükümlülüğü (üç görev + teknik/idari tedbir) · m.12/5 ihlal bildirimi ('en kısa süre' = 72 saat — Kurul 2019/10 sayılı karar) · m.16 VERBİS kayıt (Yönetmelik m.13: değişiklik 7 gün) · m.18 idari yaptırımlar (m.12 ihlali: 15.000–1.000.000 TL) — kvkk.gov.tr (okundu: 2026-10-17)"
  - "ISO/IEC 27001:2022 — ISMS çerçevesi; Annex A 93 kontrol/4 tema; SoA zorunlu — secureframe.com · drata.com (2026-10-17)"
  - "Kullanıcı kararı 2026-10-17: uyum kapsamı = KVKK + ISO 27001 (+ GDPR yalnız AB kullanıcısı varsa)"
validation:
  - "2026-10-12 — her satır için dayanak dosyası mevcutluğu doğrulandı"
  - "2026-10-17 — kapsam kararı işlendi: KVKK + ISO 27001 aktif; #6 satırı 'kaynak yok' → KVKK kaynaklı satıra dönüştü"
  - "2026-10-17 — node_id diske hizalandı: K019-comilance (kullanıcı kararı: disk kanıtı esastır)"
open_questions:
  - "KVKK VERBİS kaydı yapıldı mı? — kayıt kanıtı yok → VERIFY REQUIRED"
  - "ISO 27001 sertifika hedefi var mı, yoksa iç ISMS yeterli mi? — sertifika iddiası YOK, yazılmaz"
  - "Politika MD'leri (kvkk-politikasi.md vb.) v2 şekli gereği henüz üretilmedi — children: [] korunur"
group: mimari
---


# K019-comilance/policies — Politika Kayıt İskeleti

**ELI15:** "Bu projede **ne kural** var?" sorusunun cevap listesi. Politikanın
kendisi vault'ta yaşar (kök `AGENTS.md` §5, `.ai/AGENTS.md` §16 vb.); burada
yalnız **kayıt satırı** vardır. Kapsam kararı (2026-10-17): **KVKK + ISO 27001**.

## Politika kaydı

| # | Politika | Dayanak (gerçek dosya) | Sorumlu | Durum |
|---|---|---|---|---|
| 1 | Güvenlik alt sınırı — AES-256-GCM/ChaCha20 · MD5/SHA-1/DES yasak · auth + rol · rate limit · fail-safe · secret yalnız `.env` | kök `AGENTS.md` §5 Security (OWASP Top 10:2025) | security-engineer | kural var · denetim yok |
| 2 | Kod kalite standartları (strict_types/PSR-12 · ITCSS/BEM/WCAG 2.2 AA · BCNF/no ORM · zero-allocation · test ≥%80 · CI ≥%95) | `@.ai/AGENTS.md` §16 | qa-engineer | hedef var · ölçüm yok |
| 3 | Vault LINT + 16 Hard Guardrail | `@.ai/CLAUDE.md` §6 (işaret: kök AGENTS.md §24.2) | MO | işaretçi |
| 4 | Domain sınırı (dosya tipi → sorumlu agent) | `@.ai/AGENTS.md` §5 Domain Boundaries | MO | kural var |
| 5 | Ekran/uygulama erişilebilirliği (WCAG 2.2 AA · touch ≥24px · focus 3px) | kök AGENTS.md §5 UI/UX | ui-designer | kural var · denetim yok |
| 6 | **KVKK veri koruma politikası** — m.4 işleme ilkeleri (hukuka uygunluk, amaçla sınırlılık, ölçülülük) · m.6 özel nitelikli veri · m.12 teknik+idari tedbir · m.12/5 ihlal bildirimi ≤72 saat · m.16 VERBİS · m.18 yaptırım (15.000–1.000.000 TL) | 6698 sayılı Kanun — kvkk.gov.tr (2026-10-17 doğrulandı) | Vault Steward + security-engineer | kapsam kararlı · denetim yok |
| 7 | **ISO/IEC 27001:2022 ISMS politikası** — Annex A 93 kontrol/4 tema · risk tedavisi · SoA (Statement of Applicability) zorunlu | ISO 27001:2022 (standart metni ücretli — secureframe/drata özeti 2026-10-17) | Vault Steward | çerçeve benimsendi · SoA yazılmadı |
| 8 | GDPR (koşullu) — yalnız AB yerleşik kullanıcısı/kişisi varsa tam kapsam; CoreMusic hedef kitlesinde tespit edilmedi → **şartlı etkin** | AB 2016/679 — koşul: AB veri öznesi bulunursa | Vault Steward | `⚠️ VERIFY REQUIRED` (AB veri öznesi tespiti yok) |

> **Not (zero-hallucination):** #6 ve #7 satırları **kapsam kararı** taşır (kullanıcı
> onayı 2026-10-17); uygulama denetimi **yapılmadı** → "uyumlu" cümlesi yoktur.
> ISO sertifika iddiası **yoktur**; SoA yazılmadan ISO kontrolü `denetlenmedi` sayılır.

## Kapsam kararı kaydı (2026-10-17)

| Karar | Seçenek | Kaynak |
|---|---|---|
| Uyum kapsamı | **KVKK + ISO 27001** (GDPR koşullu) | kullanıcı onayı |
| Disk/ID | Klasör `K019-comilance` adıyla kalır; disk kanıtı esas | kullanıcı onayı |
| Kod örnekleri | 11 dil → `controls/kod-ornekleri.md` | kullanıcı onayı |

## Durum

- `node_type: subdomain` · `children: []` (v2'de alt liste yok — politika MD'leri üretilmedi; uydurma politika metni yazılmaz).
- KVKK/ISO politika metinleri **kayıt satırıdır**; tam politika belgesi istenirse ayrı ADR + onay gerekir.

## İlişkili kaynaklar

| Kaynak | Ne verir |
|---|---|
| `[[../index]]` | Klasör künyesi |
| `[[../compliance-master]]` | Uyum alanları + denetim döngüsü |
| `[[../controls/index]]` | OWASP A01–A10 + ISO + K000–K013 haritası |
| `[[../controls/kod-ornekleri]]` | Politikaların kod gövdesi (11 dil) |
| `[[../verification/index]]` | Denetim turu kayıtları |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Politika kayıt kataloğu (8 satır) | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward
