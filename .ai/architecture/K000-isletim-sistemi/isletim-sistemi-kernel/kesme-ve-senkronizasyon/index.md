---
node_id: K000-isletim-sistemi/isletim-sistemi-kernel/kesme-ve-senkronizasyon
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-kernel
children:
  - K000-isletim-sistemi/isletim-sistemi-kernel/kesme-ve-senkronizasyon/kesme-ve-senkronizasyon-anayasa
  - K000-isletim-sistemi/isletim-sistemi-kernel/kesme-ve-senkronizasyon/kesme-ve-senkronizasyon-surucu
  - K000-isletim-sistemi/isletim-sistemi-kernel/kesme-ve-senkronizasyon/kesme-ve-senkronizasyon-ornekler
depends_on: []
evidence:
  - https://docs.kernel.org/locking/lockdep-design.html
  - https://docs.kernel.org/locking/mutex-design.html
  - https://docs.kernel.org/locking/locktypes.html
  - https://learn.microsoft.com/en-us/windows/win32/sync/mutex-objects
  - https://man7.org/linux/man-pages/man7/futex.7.html
validation:
  - "2026-10-13 · 5 resmi URL HEAD 200 · envanter kesişimi 0 · dosya envanteri disk glob ile sayıldı"
open_questions:
  - "core ↔ kernel sınırı (Q2) → ⚠️ VERIFICATION REQUIRED"
  - "Hedef çekirdek (Q4) → UNKNOWN"
group: mimari
---

# kesme-ve-senkronizasyon — Alt-Dal İndeksi (Kademe-4)

**ELI15:** Kesme (interrupt), donanımın "çabuk bak" diyerek çekireği aniden
uyarmasıdır; senkronizasyon ise birden çok çekirdek/izleyici aynı veriye
dokunduğunda **çakışmayı** önleyen kilit yapılarıdır — ikisi birlikte
"eşzamanlılık" problemini çözer.

| Öğe | Değer |
|---|---|
| parent_id | `K000-isletim-sistemi/isletim-sistemi-kernel` |
| Kademe | 4 · alt-dal |
| Durum | `draft` (Q2 · Q4 açık) |
| Ana işlev | 1 (sistem çağrılarını karşılar — trap/kesme yolu) |

## §1 Kanıtlanmış çerçeve

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Lockdep kilit tasarım dokümanı (deadlock tespiti) | https://docs.kernel.org/locking/lockdep-design.html | 2026-10-13 | ✅ HEAD 200 |
| Mutex tasarım dokümanı (uyuyan/uyandırılan kilit) | https://docs.kernel.org/locking/mutex-design.html | 2026-10-13 | ✅ HEAD 200 |
| Kilit türleri karşılaştırma (spin vs mutex vs rwsem) | https://docs.kernel.org/locking/locktypes.html | 2026-10-13 | ✅ HEAD 200 |
| Windows mutex nesneleri (senkronizasyon primitive) | https://learn.microsoft.com/en-us/windows/win32/sync/mutex-objects | 2026-10-13 | ✅ HEAD 200 |
| `futex(7)` hızlı kullanıcı-uzayı kilidi | https://man7.org/linux/man-pages/man7/futex.7.html | 2026-10-13 | ✅ HEAD 200 |

## §2 Bu alt-dalın dosya envanteri (disk · 2026-10-13)

| # | Dosya | İçerik yüzeyi (bölüm omurgası) | group | Satır |
|---|---|---|---|---|
| 1 | `index.md` (bu dosya) | alt-dal indeksi · komşu sınır matrisi | mimari | 40-100 |
| 2 | `kesme-ve-senkronizasyon-anayasa.md` | §0-§3: ELI15 · kesme (IRQ/NMI/vektör) · kilit türleri · deadlock/starvation | mimari | ≥500 |
| 3 | `kesme-ve-senkronizasyon-surucu.md` | §4-§6: SMP/tutarlılık · ölçüm (jitter/lock contention) · referans | mimari | ≥500 |
| 4 | `kesme-ve-senkronizasyon-ornekler.md` | §1-§6: örnek kataloğu · senaryolar · repo örnekleri | kod | ≥500 |

> Kural: bu 4 dosyanın yüzeyi bu alt-dalın **kendi beyazlistesidir**; kök
> `isletim-sistemi-kernel/*.md` ve kardeş alt-dallara bu turda **dokunulmadı**.

## §3 Komşu sınır matrisi (bu alt-dal ↔ diğerleri)

| Dışarıda olan | Nereye gider | Neden |
|---|---|---|
| Zamanlama/tick kesmesi (timer interrupt) | `isletim-sistemi-plan` | kesme-zamanlama `-plan`'ın alanı |
| Trap çağrısının uygulama yüzeyi | `isletim-sistemi-api` | sürücü↔uygulama sınırı `-api` |
| Kilitlerin güvenlik kullanımı (kilit bypass) | `isletim-sistemi-security` | güvenlik `-security` |
| Kesme sayacı · lock contention telemetry | `isletim-sistemi-observability` | gözlem `-observability` |
| Kilit determinizm testi · deadlock testi | `isletim-sistemi-testing` | test `-testing` |
| Bellek/dosya kilitleri (VFS `flock`) | `cekirdek-yonetim` | bellek-kilit kardeş dalda |
| OS türü (kesme modeli sınıflandırma) | `isletim-sistemi-turu` | tür `-turu` |

> Bu alt-dal **yalnız** kesme yolu + kilit/senkronizasyon mekanizmasını işler;
> zamanlama protokolü, güvenlik politikası ve gözlem **komşu daldadır**.

## §4 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| S1 | `core` ↔ `kernel` sınırı (kesme/kilit hangi dalda)? | ⚠️ VERIFICATION REQUIRED (Q2) |
| S2 | Hedef çekirdek (Linux/Windows/macOS)? | UNKNOWN (Q4) |
| S3 | Ölçüm hedefi (512 örnek · 48 kHz · lock-free callback) bu çekirdekte geçerli mi? | ⚠️ VERIFICATION REQUIRED |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Alt-dal künyesi ve çocuk kataloğu | Kademe-4 |
| mimari | `kesme-ve-senkronizasyon-anayasa.md` | Anasözleşme ve bağlayıcı mimari kurallar | Kademe-4 |
| mimari | `kesme-ve-senkronizasyon-surucu.md` | SMP/ölçüm/risk omurgası | Kademe-4 |
| kod | `kesme-ve-senkronizasyon-ornekler.md` | Örnek senaryolar ve repo örnekleri | Kademe-4 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
