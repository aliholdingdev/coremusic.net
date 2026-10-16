---
node_id: K000-isletim-sistemi/isletim-sistemi-core
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi
children:
  - K000-isletim-sistemi/isletim-sistemi-core/surec-ve-zamanlama-kavramlari
  - K000-isletim-sistemi/isletim-sistemi-core/bellek-kavramlari
  - K000-isletim-sistemi/isletim-sistemi-core/dosya-sistemi-kavramlari
  - K000-isletim-sistemi/isletim-sistemi-core/izin-ve-guvenlik-kavramlari
depends_on: []
evidence: ["https://www.man7.org/linux/man-pages/", "https://www.man7.org/linux/man-pages/man3/strerror.3.html"]
validation: "2026-10-13 · 4 resmi URL HTTP 200 · 4 alt-dal disk kanıtlı AÇILDI (kök 4 md + 16 yeni md) · §GRUPLAR 4 satır Kademe-3 · children 4 tam yol | 2026-10-14 · isletim-sistemi-core-kod-seti.md yazıldı (994 satır · 23 kod bloğu · 29 resmi URL HTTP 200 · spot-check: orkestratör)"
open_questions:
  - "core ↔ kernel ayrım tanımı yok → ⚠️ VERIFICATION REQUIRED (Q2 / Q-C1)"
  - "Çekirdek kavramlarının tam seti v2'de listelenmemiş → UNKNOWN (Q-C2)"
  - "Hedef platform seti (Q4) → UNKNOWN (Q-C3)"
group: mimari
---

# isletim-sistemi-core — Kademe-2 Kayıt (subdomain · `status: draft`)

## §0 ELI15

İşletim sisteminin **kavramsal çekirdeği**: bellek, süreç, dosya, izin — "bir işletim sistemi ne demektir" sorusunun cevabı. Kod değil, **kavram haritası**. Master: `os-master.md` §2-§3.

## §1 Amaç

| # | Amaç | Karşılığı |
|---|---|---|
| 1 | 1 | 8 çekirdek kavramın 4'ünü derin-dal dosyalarına ayırmak | `-kapsam.md` §C |
| 2 | Her kavramı "kavram → upstream kaynak → repo sargısı" üçlüsüyle belgelemek | alt-dal `-anayasa` · `-surucu` · `-ornekler` |
| 3 | İddiaları test/doğrulama dosyasına bağlamak | `-dogrulama.md` §A-§I |

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Linux man-pages projesi, bölüm 1-9 sayfalarının resmi dizinini yayınlar | https://www.man7.org/linux/man-pages/ | 2026-10-13 | ✓ HTTP 200 |
| `strerror(3)`, `errno` değerlerini insan-okur metne çevirme sözleşmesidir | https://www.man7.org/linux/man-pages/man3/strerror.3.html | 2026-10-13 | ✓ HTTP 200 |

## §2 Kademe ve rol

| # | Kademe | Dosya/klasör | Rol |
|---|---|---|---|
| 1 | Kademe-2 | bu `index.md` | subdomain künyesi · çocuk kataloğu |
| 2 | Kademe-3 | 4 alt-dal klasörü (+ her birinin `index.md`) | kavram bazlı deep-dal |
| 3 | Kademe-4 | alt-dallarda 3'er içerik dosyası | anayasa · sürücü · örnekler |

Kök içerik dosyaları (Kademe-2, bu düğümde kalır): `-kapsam.md` · `-dogrulama.md` · `-ornekler.md`.

## §3 Envanter

| # | Alt-dal (Kademe-3) | İçerik (Kademe-4) | Kavram |
|---|---|---|---|
| 1 | `surec-ve-zamanlama-kavramlari/` | `-anayasa` · `-surucu` · `-ornekler` | süreç + zaman paylaşımı + zamanlama |
| 2 | `bellek-kavramlari/` | `-anayasa` · `-surucu` · `-ornekler` | adres alanı + tahsis + paylaşımlı bellek |
| 3 | `dosya-sistemi-kavramlari/` | `-anayasa` · `-surucu` · `-ornekler` | VFS · yol · dosya tanıtıcısı · kilit |
| 4 | `izin-ve-guvenlik-kavramlari/` | `-anayasa` · `-surucu` · `-ornekler` | kimlik · mod · ACL · capability |

8 kavramdan kalan 4'ü (`syscall` → `-api` · gözlem → `-observability` · test → `-testing` · ses → `-audio`) bu dalda değildir.

## §4 §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın FM'inde 9. alan `group`. Tablo Kademe-3 çocuklarını listeler; kök kod-seti dosyası son satırda işaretlidir.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `surec-ve-zamanlama-kavramlari/index.md` | Süreç & zamanlama alt-dalı künyesi | Kademe-3 |
| mimari | `bellek-kavramlari/index.md` | Bellek alt-dalı künyesi | Kademe-3 |
| mimari | `dosya-sistemi-kavramlari/index.md` | Dosya sistemi alt-dalı künyesi | Kademe-3 |
| mimari | `izin-ve-guvenlik-kavramlari/index.md` | İzin & güvenlik alt-dalı künyesi | Kademe-3 |
| kod | `isletim-sistemi-core-kod-seti.md` | Cross-platform bellek ölçümü / dosya meta / temp dizin kod seti — 994 satır · 23 kod bloğu · 29 resmi URL | Kademe-2 (kök) |

## §5 Komşu sınırı

| Konu | Gittiği dal |
|---|---|
| process/thread/zamanlama (yürütme, preemption) | `isletim-sistemi-plan` |
| bellek/segmentasyon (çekirdek içi veri yapıları) | `isletim-sistemi-kernel` |
| dosya sistemi syscall yüzeyi | `isletim-sistemi-api` |
| izin/ACL denetim uygulaması | `isletim-sistemi-security` |
| gözlem (ftrace/ETW) | `isletim-sistemi-observability` |
| test (kselftest/KUnit) | `isletim-sistemi-testing` |
| OS türü karşılaştırması | `isletim-sistemi-turu` |

`core` ↔ `kernel` sınır tanımı yok (Q2): iki dal da kapsam genişletmez — `-kapsam.md` §A.4.

## §6 depends_on

`depends_on: []` — başka K-node'a bağlı değildir; dayandığı dosyalar `evidence` alanındadır (`os-master.md` §2-§3 · `os-bagimliliklar.md` §1 B13).

## §7 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q-C1 | `core` ↔ `kernel` sınırı (Q2)? | ⚠️ VERIFICATION REQUIRED |
| Q-C2 | 8 kavramın tamamı bu dalın mı, parçaları child'lara mı dağıtıldı? | ⚠️ VERIFICATION REQUIRED |
| Q-C3 | Hedef platform çekirdeği (Q4)? | UNKNOWN |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
