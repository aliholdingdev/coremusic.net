---
node_id: K000-isletim-sistemi/isletim-sistemi-core/surec-ve-zamanlama-kavramlari
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-core
children:
  - K000-isletim-sistemi/isletim-sistemi-core/surec-ve-zamanlama-kavramlari/surec-ve-zamanlama-kavramlari-anayasa
  - K000-isletim-sistemi/isletim-sistemi-core/surec-ve-zamanlama-kavramlari/surec-ve-zamanlama-kavramlari-surucu
  - K000-isletim-sistemi/isletim-sistemi-core/surec-ve-zamanlama-kavramlari/surec-ve-zamanlama-kavramlari-ornekler
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-core
evidence:
  - shared/src/Middleware/MiddlewarePipeline.php:42
  - shared/src/AI/AIWorkflow.php:51
  - shared/tests/Repository/ApiKeyCreateCommandTest.php:253
validation: "2026-10-13 · 2 yeni URL HTTP 200 (envanter kesişimi 0) · 3 içerik dosyası disk kanıtlı (her biri ≥500 satır) · repo satırları ReadAllLines ile birebir"
open_questions:
  - "Süreç ↔ iplik ayrımı bu dalda mı, `-plan` dalında mı? (Q2) → ⚠️ VERIFICATION REQUIRED"
  - "Hedef platform zamanlayıcısı (Q4) → UNKNOWN"
group: mimari
---

# surec-ve-zamanlama-kavramlari — Deep-Dal İndeksi (Kademe-4)

## §0 ELI15

"Bilgisayar aynı anda birden fazla işi nasıl yetiştiriyor?" sorusunun cevabı: işletim
sistemi **süreçleri başlatır, sıraya koyar, zaman dilimleri ayırır ve geçiş yapar**. Bu
dal bu kavramların tanımını (anayasa), mekanizmasını (sürücü) ve repo'daki sargılarını
(örnekler) taşır.

## §1 Amaç

| # | Amaç | Gittiği dosya |
|---|---|---|
| 1 | Süreç/zamanlama terimlerini bağlayıcı kurallara bağlamak | `-anayasa` §3-§4 |
| 2 | Mekanizma (çekirdek) ile politika (kuyruk) ayrımını belgelemek | `-surucu` §1-§3 |
| 3 | CoreMusic kodundaki süreç/zamanlama sargılarını satır kanıtıyla göstermek | `-ornekler` §2-§3 |

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| `personality(2)`, sürecinexecution-domain bayraklarını okur/ayarlar (Linux) | https://www.man7.org/linux/man-pages/man2/personality.2.html | 2026-10-13 | ✓ HTTP 200 |
| `DispatchQueue`, Apple'de işleri iplikler üzerinde planlayan kuyruk soyutlamasıdır | https://developer.apple.com/documentation/dispatch/dispatchqueue | 2026-10-13 | ✓ HTTP 200 |

## §2 Kademe ve rol

| # | Kademe | Dosya | Rol |
|---|---|---|---|
| 1 | Kademe-4 | `surec-ve-zamanlama-kavramlari/index.md` | bu dalın künyesi |
| 2 | Kademe-5 | `-anayasa.md` | bağlayıcı tanımlar ve ilkeler (§0-§10) |
| 3 | Kademe-5 | `-surucu.md` | mekanizma envanteri ve hata modları (§1-§7) |
| 4 | Kademe-5 | `-ornekler.md` | repo örnekleri ve test senaryoları (§1-§5) |

> Kademe etiketi bu dal için `index.md` H1'indeki "Kademe-4" tanımıyla sabittir; içerik
> dosyaları bir alt kademedir (registry §8.1 gruplama ekseniyle çakışmaz).

## §3 Envanter

| # | Dosya | Kapsadığı kavramlar |
|---|---|---|
| 1 | `-anayasa.md` | süreç · iplik · kuyruk · zaman dilimi · geçiş · mono saat · gerçek saat |
| 2 | `-surucu.md` | fork/exec · zamanlayıcı · bekleyiş · kilit · sinyal · eşzamanlılık |
| 3 | `-ornekler.md` | `proc_open` · `hrtime` · `microtime` · oturum · bootstrap · exit kodu |

## §4 §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `surec-ve-zamanlama-kavramlari-anayasa.md` | Bağlayıcı tanım ve ilke seti | Kademe-4 |
| mimari | `surec-ve-zamanlama-kavramlari-surucu.md` | Mekanizma envanteri | Kademe-4 |
| kod | `surec-ve-zamanlama-kavramlari-ornekler.md` | Repo örnekleri ve testler | Kademe-4 |

## §5 Komşu sınırı

| Konu | Gittiği dal | Bu daldaki karşılığı |
|---|---|---|
| process/thread/zamanlama yürütmesi (preemption, kuyruk politikası) | `isletim-sistemi-plan` | yalnız kavram + repo sargısı |
| bellek/segmentasyon (çekirdek içi veri yapıları) | `isletim-sistemi-kernel` | süreç adres alanı kavramı kalır |
| dosya sistemi syscall yüzeyi | `isletim-sistemi-api` | `proc_open` gibi sargılar sınır komşusu |
| izin/ACL denetim uygulaması | `isletim-sistemi-security` | kimlik değiştirme (`setuid`) işlenmez |
| gözlem (ftrace/ETW) | `isletim-sistemi-observability` | süre ölçümü sargısı örnek olarak kalır |
| test (kselftest/KUnit) | `isletim-sistemi-testing` | senaryolar doküman düzeyi |
| OS türü karşılaştırması | `isletim-sistemi-turu` | Linux/Windows/macOS karşılaştırması örnek düzeyinde |

## §6 depends_on

`depends_on: [K000-isletim-sistemi/isletim-sistemi-core]` — üst düğümün §1 kavram #1'i
(süreç + zaman paylaşımı) bu dalın konusudur; master referans `os-master.md` §3 satır 1.

## §7 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q-S1 | `core` ↔ `kernel` sınırında "zamanlama politikası" hangi tarafta? (Q2) | ⚠️ VERIFICATION REQUIRED |
| Q-S2 | `isletim-sistemi-plan` ile bu dalın ayrımı v2'de tanımsız | ⚠️ VERIFICATION REQUIRED |
| Q-S3 | Hedef platform zamanlayıcı/timer API seti (Q4) | UNKNOWN |
| Q-S4 | PHP ZTS iplik modeli hangi dalda işlenecek? | UNKNOWN |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
