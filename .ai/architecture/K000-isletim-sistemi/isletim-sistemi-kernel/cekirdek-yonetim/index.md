---
node_id: K000-isletim-sistemi/isletim-sistemi-kernel/cekirdek-yonetim
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-kernel
children:
  - K000-isletim-sistemi/isletim-sistemi-kernel/cekirdek-yonetim/cekirdek-yonetim-anayasa
  - K000-isletim-sistemi/isletim-sistemi-kernel/cekirdek-yonetim/cekirdek-yonetim-surucu
  - K000-isletim-sistemi/isletim-sistemi-kernel/cekirdek-yonetim/cekirdek-yonetim-ornekler
depends_on: []
evidence:
  - https://docs.kernel.org/scheduler/index.html
  - https://docs.kernel.org/mm/index.html
  - https://man7.org/linux/man-pages/man2/fork.2.html
  - https://man7.org/linux/man-pages/man2/mmap.2.html
  - https://learn.microsoft.com/en-us/windows/win32/memory/memory-management
validation:
  - "2026-10-13 · 5 resmi URL HEAD 200 · envanter kesişimi 0 · dosya envanteri disk glob ile sayıldı"
open_questions:
  - "core ↔ kernel sınırı (Q2) → ⚠️ VERIFICATION REQUIRED — kavram sınırı `-core` tarafında"
  - "Hedef çekirdek (Q4) → UNKNOWN — CI yalnız ubuntu-latest gösterir"
group: mimari
---

# cekirdek-yonetim — Alt-Dal İndeksi (Kademe-4)

**ELI15:** Çekirdek yönetimi, işletim sisteminin **belleği ve süreçleri** nasıl
paylaştığıdır — sayfa tablosu belleği böler, zamanlayıcı CPU'yu süreçlere dağıtır,
bağlam değişimi (context switch) hangi sürecin çalıştığını değiştirir.

| Öğe | Değer |
|---|---|
| parent_id | `K000-isletim-sistemi/isletim-sistemi-kernel` |
| Kademe | 4 · alt-dal |
| Durum | `draft` (Q2 · Q4 açık) |
| Ana işlev | 2 (zamanlar) + 3 (bellek ayırır) — kök `index.md` §1 |

## §1 Kanıtlanmış çerçeve

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Zamanlama alt sistemi dokümanı (EEVDF · CFS) | https://docs.kernel.org/scheduler/index.html | 2026-10-13 | ✅ HEAD 200 |
| Bellek yönetimi (mm) alt sistem dokümanı | https://docs.kernel.org/mm/index.html | 2026-10-13 | ✅ HEAD 200 |
| `fork(2)` süreç yaratma | https://man7.org/linux/man-pages/man2/fork.2.html | 2026-10-13 | ✅ HEAD 200 |
| `mmap(2)` dosya/bellek eşlemesi | https://man7.org/linux/man-pages/man2/mmap.2.html | 2026-10-13 | ✅ HEAD 200 |
| Windows bellek yönetimi ayrı resmi konudur | https://learn.microsoft.com/en-us/windows/win32/memory/memory-management | 2026-10-13 | ✅ HEAD 200 |

## §2 Bu alt-dalın dosya envanteri (disk · 2026-10-13)

| # | Dosya | İçerik yüzeyi (bölüm omurgası) | group | Satır |
|---|---|---|---|---|
| 1 | `index.md` (bu dosya) | alt-dal indeksi · komşu sınır matrisi | mimari | 40-100 |
| 2 | `cekirdek-yonetim-anayasa.md` | §0-§4: ELI15 · Ring 0/3 · bellek (sayfa/TLB/sanallaştırma) · modlar · monolitik/mikro/hibrit | mimari | ≥500 |
| 3 | `cekirdek-yonetim-surucu.md` | §5-§9: sistem durumu/context switch · ölçüm · repo kanıtı · riskler · kaynakça | mimari | ≥500 |
| 4 | `cekirdek-yonetim-ornekler.md` | §1-§6: örnek kataloğu · senaryolar · repo örnekleri | kod | ≥500 |

> Kural: bu 4 dosyanın yüzeyi bu alt-dalın **kendi beyazlistesidir**; kök
> `isletim-sistemi-kernel/*.md` ve kardeş alt-dallara bu turda **dokunulmadı**.

## §3 Komşu sınır matrisi (bu alt-dal ↔ diğerleri)

| Dışarıda olan | Nereye gider | Neden |
|---|---|---|
| Kavramsal `core` tanımı (bellek/süreç kavramı) | `isletim-sistemi-core` | Q2 — kavram sınırı `-core` tarafında tanımlı |
| Zamanlama/jitter ölçüm protokolü | `isletim-sistemi-plan` | kesme-zamanlama `-plan`'ın alanı |
| Fork/exec yüzeyinin uygulama kullanımı | `isletim-sistemi-api` | sürücü↔uygulama sınırı `-api` |
| Bellek güvenlik politikası (ASLR · NX) | `isletim-sistemi-security` | güvenlik `-security` |
| Sayfa-tablosu ölçüm sayacı · telemetry | `isletim-sistemi-observability` | gözlem `-observability` |
| Zamanlama testi · determinizm testi | `isletim-sistemi-testing` | test `-testing` |
| OS türü (monolitik/mikro/hibrit sınıflandırma) | `isletim-sistemi-turu` | tür `-turu` |

> Bu alt-dal **yalnız** bellek + süreç/zamanlama mekanizmasını işler; kavram,
> ölçüm protokolü, güvenlik politikası ve test **komşu daldadır** — burada
> tekrar edilmez (çakışma = 0).

## §4 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| C1 | `core` ↔ `kernel` sınırı (zamanlama/bellek hangi dalda)? | ⚠️ VERIFICATION REQUIRED (Q2) |
| C2 | Hedef çekirdek (Linux/Windows/macOS)? | UNKNOWN (Q4) |
| C3 | Ölçüm hedefi (512 örnek · 48 kHz) bu çekirdekte geçerli mi? | ⚠️ VERIFICATION REQUIRED |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Alt-dal künyesi ve çocuk kataloğu | Kademe-4 |
| mimari | `cekirdek-yonetim-anayasa.md` | Anasözleşme ve bağlayıcı mimari kurallar | Kademe-4 |
| mimari | `cekirdek-yonetim-surucu.md` | Sürüm/ölçüm/risk omurgası | Kademe-4 |
| kod | `cekirdek-yonetim-ornekler.md` | Örnek senaryolar ve repo örnekleri | Kademe-4 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
