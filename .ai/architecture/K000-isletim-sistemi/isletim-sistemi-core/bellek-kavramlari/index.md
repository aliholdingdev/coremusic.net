---
node_id: K000-isletim-sistemi/isletim-sistemi-core/bellek-kavramlari
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-core
children:
  - K000-isletim-sistemi/isletim-sistemi-core/bellek-kavramlari/bellek-kavramlari-anayasa
  - K000-isletim-sistemi/isletim-sistemi-core/bellek-kavramlari/bellek-kavramlari-surucu
  - K000-isletim-sistemi/isletim-sistemi-core/bellek-kavramlari/bellek-kavramlari-ornekler
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-core
evidence:
  - https://www.php.net/manual/en/function.memory-get-usage.php
  - https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-globalmemorystatusex
  - shared/src/Cache/MemoryAdapter.php:8
  - shared/src/Cache/ApcuAdapter.php:13
validation: "2026-10-13 · 2 yeni URL HTTP HEAD 200 (envanter kesişimi 0) · 3 içerik dosyası ≥500 satır disk kanıtlı · repo satırları ReadAllLines ile birebir"
open_questions:
  - "Bellek koruma politikası (ASLR/NX) bu dalda mı, `-security` dalında mı? → ⚠️ VERIFICATION REQUIRED (Q-B1)"
  - "Hedef platform bellek modeli (48-bit/5 seviye sayfalama?) → UNKNOWN (Q-B2)"
  - "Üretim bellek eşiği (limit/RSZ hedefi) → UNKNOWN — ölçüm PLANNED (Q-B3)"
group: mimari
---

# bellek-kavramlari — Deep-Dal İndeksi (Kademe-4)

## §0 ELI15

"Bilgisayar belleği nasıl bölüşülüyor?" sorusunun cevabı: işletim sistemi her sürece **sanal bir adres uzayı** verir, bu uzayı **sayfalara** böler, sayfaları **sayfa tablosuyla** gerektiğinde **swap'e** yollar ve yetkisiz erişimi **koruma bitleriyle** engeller. Bu dal üç katmanı birlikte taşır: tanım (`-anayasa`), mekanizma (`-surucu`) ve repo'daki gerçek bellek yüzeyi (`-ornekler`).

## §1 Amaç

| # | Amaç | Gittiği dosya |
|---|---|---|
| 1 | Bellek kavramlarını (adresleme · sayfalama · koruma · swap · metrik) bağlayıcı tanıma bağlamak | `-anayasa` §2–§4 |
| 2 | Sayfa hatası, swap ve koruma ihlali akışlarını adım adım belgelemek | `-surucu` §2–§3 |
| 3 | Repo'daki bellek yüzeyini (dizi deposu · APCu paylaşımlı bellek · ölçüm eksikliği) satır kanıtıyla göstermek | `-ornekler` §1–§3 |

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| PHP `memory_get_usage()`/`memory_get_peak_usage()` süreç içi bellek ölçümüdür | https://www.php.net/manual/en/function/memory-get-usage.php | 2026-10-13 | ✓ HTTP 200 |
| Windows `GlobalMemoryStatusEx` sistem bellek durumu sorgusudur | https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-globalmemorystatusex | 2026-10-13 | ✓ HTTP 200 |

## §2 Kademe ve rol

| # | Kademe | Dosya | Rol |
|---|---|---|---|
| 1 | Kademe-4 | `bellek-kavramlari/index.md` | bu dalın künyesi |
| 2 | Kademe-5 | `-anayasa.md` | bağlayıcı tanımlar, ilke ve eşikler (§0–§9) |
| 3 | Kademe-5 | `-surucu.md` | uygulama katmanı, akış, hata modu, ölçüm, repo kanıtı (§1–§7) |
| 4 | Kademe-5 | `-ornekler.md` | çalışan yapılandırmalar, kod örnekleri, anti-pattern (§1–§5) |

> Kademe etiketi bu dal için H1'deki "Kademe-4" ile sabittir (registry §8.1).

## §3 Envanter

| # | Dosya | Kapsadığı kavramlar |
|---|---|---|
| 1 | `-anayasa.md` | sanal adresleme · sayfalama/sayfa tablosu · TLB · koruma bitleri (R/W/X) · swap · bellek metrikleri |
| 2 | `-surucu.md` | adres çevirisi akışı · sayfa hatası · koruma ihlali · swap baskısı · `/proc` ve Windows sayaçları |
| 3 | `-ornekler.md` | PHP dizi deposu · APCu paylaşımlı bellek · `str_repeat` maskeleme · ölçüm eksikliğinin olumsuz kanıtı |

## §4 §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `bellek-kavramlari-anayasa.md` | Bağlayıcı tanım ve ilke seti | Kademe-4 |
| mimari | `bellek-kavramlari-surucu.md` | Mekanizma envanteri ve hata modları | Kademe-4 |
| kod | `bellek-kavramlari-ornekler.md` | Repo örnekleri ve test senaryoları | Kademe-4 |

## §5 Komşu sınırı

| Konu | Gittiği dal | Bu daldaki karşılığı |
|---|---|---|
| Çekirdek içi bellek veri yapıları (sayfa tablosu yerleşimi, buddy/SLUB) | `isletim-sistemi-kernel` | yalnız kavram + akış |
| Bellek güvenlik politikası (ASLR · NX · W^X uygulaması) | `isletim-sistemi-security` | koruma **bitleri** kalır, politika gitmez |
| `/proc` ve performans sayacı okuma araçları | `isletim-sistemi-observability` | metrik **tanımı** kalır, toplama gitmez |
| Bellek limiti testi / sızıntı testi stratejisi | `isletim-sistemi-testing` | senaryolar doküman düzeyi |
| Bellek ölçüm API yüzeyi (`memory_get_usage` sargısı) | `isletim-sistemi-api` | yalnız kavram + repo sargısı |
| OS türü bellek modeli karşılaştırması | `isletim-sistemi-turu` | karşılaştırma örnek düzeyinde |
| Ses tamponu bellek planı (512 örnek) | `isletim-sistemi-audio` | yalnız genel sayfalama kavramı |

## §6 depends_on

`depends_on: [K000-isletim-sistemi/isletim-sistemi-core]` — üst düğümün §1 kavram maddelerinden "bellek" bu dalın konusudur (kök `index.md` §1 · `os-master.md` §3).

## §7 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q-B1 | Koruma politikası (ASLR/NX) `-security` dalında mı işlenecek? | ⚠️ VERIFICATION REQUIRED |
| Q-B2 | Hedef platform bellek modeli (sayfa boyu / seviye sayısı) | UNKNOWN |
| Q-B3 | Üretim bellek eşiği ve ölçüm periyodu | UNKNOWN — ölçüm PLANNED |
| Q-B4 | Neva Engine (C++20) tampon bellek planı bu dala girer mi? | ⚠️ VERIFICATION REQUIRED (ADR-019 sınırı) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
