---
title: "CoreMusic — .ai/ Vault PRD (Belge Sistemi Gereksinim Belgesi)"
type: prd
category: vault
version: 1.0.1
status: active
authority: "Derived — SSOT: [[AGENTS.md]] (registry) + [[CLAUDE.md]] (anayasa) + [[../PRD.md]] (aile indeksi)"
updated: 2026-10-23
---

# CoreMusic — .ai/ Vault PRD

**docType:** prd · **Klasör:** `.ai/` · **Sorumlu:** MO (vault-updater) + product-owner rolü (kullanıcı)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[CONTEXT.md]] · [[../PRD.md]] · [[index.md]] · [[keys.md]]

---

## 1. Amaç

Bu PRD, `.ai/` vault'unun (ikinci beyin) **belge sistemini** — hangi dokümanın ne işe yaradığını, kimin
ürettiğini ve hangi kalite kapısından geçtiğini — tanımlar. Vault bir dosya deposu değil, **karar ve
bağlam üretimidir**: anayasa, süreç, karar (ADR), envanter, şablon, SQL, ekran görseli ve oturum kaydı
tek hiyerarşide yaşar; tüm alt alan adlarının tek doğruluk kaynağıdır.

> **eli10 (basit):** Bu dosya, `.ai/` klasöründe ne tür dosyalar bulunacağını ve kurallarını söyler.
> **eli15 (detay):** Ayrı yazıldı çünkü vault, kodun üstünde bir "ne hakkında" katmanıdır; PRD'si yoksa
> doküman üretimi kuralsızleşir. Okunması, yeni `.md` üretirken şablon/otorite kuralını verir. Yazılması,
> üretim ile denetimi ayırır. Olmazsa her oturum kendi dosya düzenini icat eder ve retrieval kırılır.

---

## 2. Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `.ai/**/*.md` doküman türleri ve şablonları | Alt alan adı gereksinimleri → ilgili alt PRD (bkz `[[../PRD.md]]` §5) |
| Otorite sırası (`.ai/CLAUDE.md` §2.1) | Kod yüzeyi (`*.php/js/css/sql` — yazım yasak) |
| Üretim kalite kapıları (7 alanlı frontmatter, Kalıp A–D) | Mimari karar metinleri → ADR'ler (frozen) |
| Oturum yaşam döngüsü (SESSIONS · log.md append) | Ürün tanımı metni → `.ai/PROJECTS.md` (PRD'si ayrıca) |

- **Kullananlar:** MO (üretim/denetim), 11 agent (on-demand `@` okuma), kullanıcı (onay kapıları).
- **Ön koşul:** Anayasa `.ai/CLAUDE.md` v5.x; süreç `.ai/WORKFLOW.md`; agent registry `.ai/AGENTS.md` —
  bu PRD onları **ezmez, belge katmanını tanımlar**.
- **Dokunulmaz:** frozen ADR metinleri · `.ai/.png/**` (salt-okunur SSOT) · `T07-embedded/` dizin adı.

> **eli10 (basit):** Kapsam yalnızca `.ai/` içindeki dosya sistemi; kod ve karar metinleri dışarıdadır.
> **eli15 (detay):** Sınır, PRD'nin ikinci bir anayasaya dönüşmesini engeller. Okunması, hangi kuralın
> nereden geldiğini gösterir. Sınır yoksa otorite sırası çakışır.

---

## 3. Doküman Sistemi (envanter → tür)

Vault, doküman türlerine göre yaşar (envanter sayıları `[[CONTEXT.md]]` — disk ölçümü, bu PRD'de
**sayım tekrarlanmaz**):

| Tür | Dosya(lar) | Rol | Sahip |
|-----|-----------|-----|-------|
| Anayasa | `CLAUDE.md` | Guardrail, LINT, otorite sırası | MO |
| Ana kurallar | `AGENTS.md` (vault) | Agent registry SSOT (11 agent) | MO |
| Süreç | `WORKFLOW.md` | Faz, hard gate, persona turları | MO |
| Envantér bağlamı | `CONTEXT.md` | Klasör envanteri (ölçüm) | MO |
| **PRD (yeni)** | `PRD.md` | Vault belge sistemi gereksinimleri | MO |
| Ürün tanımı | `PROJECTS.md` | Vizyon, özellik, kitle, katalog | product |
| Retrieval | `RAG.md` · `keys.md` · `index.md` | Konu→dosya, keyword, katalog | MO |
| Kararlar | `.decisions/index.md` + ADR'ler | Karar SSOT (frozen metin) | MO |
| Şablonlar | `.templates/**` | UI/UX + frontend kalıpları | ui |
| Veri | `.sql/**` | Şema şema kasası (salt-okunur) | data |
| Ekran | `ui-design/**` | Görsel spec + PNG referans (Kalıp A–D) | ui |
| Oturum | `SESSIONS/*.md` · `log.md` | Kapanış kaydı · append-only denetim izi | MO |

> **eli10 (basit):** Her dosya türünün bir sahibi ve bir işi vardır; karışmaz.
> **eli15 (detay):** Tablo ayrı çünkü tip-rol-sahip eşlemesi üretim sırasında karar vericidir. Okunması,
> doğru şablonu doğru dosyaya seçtirir. Eşleşme yoksa dosya yanlış kasaya düşer ve retrieval kaybolur.

---

## 4. Gereksinimler (PRD-VLT-001..010)

| ID | Gereksinim | Kabul Kriteri |
|----|-----------|----------------|
| PRD-VLT-001 | Yeni `.md` 7 alanlı frontmatter taşır | `title·type·category·version·status·authority·updated` — eksik alan yok |
| PRD-VLT-002 | UI-Design dosyaları Kalıp A–D'ye uyar | `.templates/ui-design/` şablonu okunmadan `ui-design/**`'a yazım YOK |
| PRD-VLT-003 | Otorite sırası korunur | `.ai/CLAUDE.md` §2.1 — çelişkide anayasa kazanır |
| PRD-VLT-004 | `.ai/` gizli dizindir | Aramalarda `hidden: true` (aksi: sahte 0 sonuç) |
| PRD-VLT-005 | `log.md` append-only'dir | Eski satır silinmez/düzenlenmez; yalnız ekleme |
| PRD-VLT-006 | Her oturum `SESSIONS/{id}.md` ile kapanır | Özet · değişiklik · açık kalan · sonraki adım |
| PRD-VLT-007 | Retrieval iki katmanlıdır | `RAG.md` §3 indeks → `keys.md` keyword → `index.md` katalog |
| PRD-VLT-008 | Ölü atıf üretilmez | Referans verilen dosya diskte yoksa yazılmaz, yok sayılır |
| PRD-VLT-009 | Secret `.md`'ye girmez | `.env` / env substitution — `.md`/`.json`/`.log` yasak |
| PRD-VLT-010 | PRD ailesi indeksi kökte yaşar | `[[../PRD.md]]` §5 — vault PRD'si indeksi kopyalamaz |

> **eli10 (basit):** Bunlar, `.ai/` içinde dosya üretirken uyulacak 10 kuraldır.
> **eli15 (detay):** Kimlikler ayrı çünkü kural ihlali denetlenebilir olmalıdır. Okunması, üretim sırasında
> öz-kontrol verir. Kimliksiz kural "genel tavsiye"ye döner ve denetlenemez.

---

## 5. Kabul Kriteri (vault kapısı)

| # | Kapı | Ölçüt |
|---|------|-------|
| 1 | Frontmatter | PRD-VLT-001 — 7 alan eksiksiz |
| 2 | Şablon | PRD-VLT-002 — Kalıp A–D (yalnız `ui-design/**`) |
| 3 | Otorite | PRD-VLT-003 — anayasa > AGENTS > WORKFLOW > brain > index > templates |
| 4 | Kanıt | Zero-hallucination — bilinmeyen `UNKNOWN` / `⚠️ VERIFICATION REQUIRED` |
| 5 | Link | Wiki-link `[[...]]`; diskte olmayan hedef üretilmez (PRD-VLT-008) |
| 6 | Güvenlik | PRD-VLT-009 — secret yok |
| 7 | Kapanış | PRD-VLT-006 — `SESSIONS/` + `log.md` append |

> **eli10 (basit):** Yeni vault dosyası bu 7 kapıdan geçmeden "bitti" sayılmaz.
> **eli15 (detay):** Kapılar üretimden ayrıdır; denetim üretime karışmaz. Okunması, G8 final raporu
> checklist'ini verir. Kapı yoksa "bitti" iddiası kanıtsız olur.

---

## 6. ADR Bağları

| Karar Alanı | ADR | Bağ |
|-------------|-----|-----|
| Retrieval pipeline | ADR-030 (PLANNED — `.ai/RAG.md` §4) | VLT-007 |
| UI şablon otoritesi | Guardrail #16 (`.ai/CLAUDE.md`) | VLT-002 |
| Yeni ADR numarası | `.ai/.decisions/index.md` — next: **111** | VLT-010 · PLT-012 |
| BFF/AI decommission (vault etkisi) | ADR-030 · ADR-084 | `[[CONTEXT.md]]` — AI/Bff satırı |

> **eli10 (basit):** Vault kuralları büyük kararlara bağlıdır; ADR'ye bağ verilir, tekrar edilmez.
> **eli15 (detay):** Bağ, ADR metninin frozen olduğunu hatırlatır; PRD karar metnini kopyalasa iki sürüm doğar.

---

## 7. Risk

| # | Risk | Etki | Azaltma |
|---|------|------|---------|
| 1 | Şablonsuz dosya üretimi (Kalıp A–D atlanırsa) | Yapısal tutarsızlık | §5 kapı 2 · üretim öncesi şablon okuma |
| 2 | `log.md`'de silme/düzenleme | Denetim izi kaybı | PRD-VLT-005 — append-only |
| 3 | Gizli dizin aramasında `hidden:false` | Sahte "0 sonuç" → ölü atıf | PRD-VLT-004 |
| 4 | PRD ailesi indeksinin vault'a kopyalanması | İki SSOT | PRD-VLT-010 · §5 kapı 5 |
| 5 | Legacy boot dosyalarıyla (`wiki/`, `raw/`, `.rules/`) çelişki | Yanlış referans | PRD-VLT-008 — diskte olmayan yazılmaz |

> **eli10 (basit):** En büyük tehlike: eski/olmayan dosyalara referans vermek ve şablonu atlamak.
> **eli15 (detay):** Risklerin azaltması farklı kapıya bağlıdır; kapılar devre dışı bırakılırsa risk geri döner.

---

## 8. Sonraki Adım

1. G2–G7: `[[../PRD.md]]` §5'teki 33 alt PRD üretilir (vault yüzeyleri: #25 `.sql` · #26 `ui-design` ·
   #27 `.decisions` bu PRD'ye bağlanır · #29–#34 araç/yüzey PRD'leri 2026-10-23'te üretildi —
   sayım 27 → 33, aile indeksi 28 → 34 yüzey, kök `PRD.md` v1.1.0 → v1.2.0).
2. G8 kapanış: `.ai/index.md` katalog satırı · `keys.md` keyword · `log.md` append · `SESSIONS/` kaydı ·
   `wiki-link-check` + final rapor.
3. Sonraki vault sürüm revizyonunda bu PRD'ye `updated` + `version` artışı yazılır (yalnız revizyon).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
