---
node_id: K000-isletim-sistemi/isletim-sistemi-testing/cekirdek-test
node_type: subdomain
status: active
parent_id: K000-isletim-sistemi/isletim-sistemi-testing
children:
  - K000-isletim-sistemi/isletim-sistemi-testing/cekirdek-test/cekirdek-test-anayasa.md
  - K000-isletim-sistemi/isletim-sistemi-testing/cekirdek-test/cekirdek-test-surucu.md
  - K000-isletim-sistemi/isletim-sistemi-testing/cekirdek-test/cekirdek-test-ornekler.md
depends_on: []
evidence: []
validation: []
open_questions:
  - T1 [UNKNOWN] — CEKİRDEK-TEST kapsamına POSIX kullanıcı uzayı uygulama testi dahil mi, yoksa yalnız kernel + QEMU katmanı mı? (kapsam.md §G.1)
  - T2 [UNKNOWN] — kunit/KASAN/lockdep metrikleri için eşik yok; parantez içi rakamlar requirements.md dokümanından gelir, ölçüm değeri değildir. (kapsam.md §G.2)
group: mimari
---

# Cekirdek-Test — Çekirdek, Sürücü ve Sanal Donanım Testi

> **Kapsam:** Bu alt-dal yalnız çekirdek (kernel) testi, sürücü testi ve sanal donanım
> testidir. POSIX kullanıcı uzayı uygulama testi → `uygulama-testi/`; CI kalite kapısı →
> `ci-ve-kalite-kapisi/`. Komşu sınır çizgisi bu dosyanın §SINIR bölümündedir.

## §1 Dosyalar (Kademe-3)

| Dosya | Tür | `group` | Ne verir |
|---|---|---|---|
| `cekirdek-test-anayasa.md` | modül | `mimari` | Test seviyeleri (unit/integration/system), kunit + Zephyr framework, QEMU/KVM, emülatör/sandbox, sanal donanım, ölçüm |
| `cekirdek-test-surucu.md` | modül | `mimari` | KUnit harness, QEMU boot zinciri, KASAN/lockdep/KFENCE, sürücü hata enjeksiyonu, ölçüm protokolü |
| `cekirdek-test-ornekler.md` | modül | `kod` | Gerçek kkernel test kalıpları, crosstest harness, QEMU komut blokları, anti-pattern tablosu |

## §2 Konu haritası (issue → dosya)

| Issue (kapsam.md §C) | Dosya |
|---|---|
| C-01 test seviyeleri | anayasa §1 |
| C-02 kunit/Zephyr framework | anayasa §2 + surucu §1 |
| C-03 QEMU/KVM | anayasa §3 + surucu §2 |
| C-04 emülatör/sandbox | anayasa §4 |
| C-05 sanal donanım | anayasa §5 + surucu §3 |
| C-06 ölçüm | anayasa §6 + surucu §5 + ornekler §7 |
| C-07 gerçek repo kanıtı | ornekler §1 |

## §3 Sorumluluk sınırı (komşu dallar)

| Konu | Sınır | Bu alt-dal ne YAPAR |
|---|---|---|
| Uygulama (PHP/JS) testi | → `isletim-sistemi-testing/uygulama-testi` | Yalnız QEMU misafirinde POSIX davranışı testi |
| CI pipeline / kalite kapısı | → `isletim-sistemi-testing/ci-ve-kalite-kapisi` | CI'da çalıştırılacak çekirdek test komutunu tanımlar |
| Sürücü/donanım arayüzü | → `isletim-sistemi-kernel` | Test stratejisi; sürücü implementasyonu DEĞİL |
| Süreç/örtü bölgesi modeli | → `isletim-sistemi-core` | Test edilen invariant; modelin sahibi DEĞİL |
| Zamanlama/gecikme ölçümü | → `isletim-sistemi-plan` | Ölçümün test tarafı; planlayıcı analizi DEĞİL |
| Güvenlik denetimi | → `isletim-sistemi-security` | KASAN/syzkaller testi; güvenlik politikası DEĞİL |
| Telemetri/trace | → `isletim-sistemi-observability` | ftrace ölçümü; telemetri altyapısı DEĞİL |
| Platform dağılımı | → `isletim-sistemi-turu` | Test matrisindeki satırlar; kurulum DEĞİL |
| Test ağaçları/coverage | → `K019-testing` | Faz-A teslimi; 0 satır yazım |
| CI altyapısı sahipliği | → `K018-devops` | Test komutu; pipeline DEĞİL |

## §4 Kanıt ve validation durumu

| Alan | Değer |
|---|---|
| `evidence` | `[]` — 2026-10-13 çalışma anında bu alt-dalda kanıtlanan iddia YOKTUR |
| `validation` | `[]` — doğrulanmış iddia YOKTUR |
| Disk kanıtı | Yok (bkz. kök `index.md` §DURUM). Üretilen tüm test kalıpları kurgusaldır |
| `open_questions` | T1 kapsam + T2 eşik → `[UNKNOWN]` |

## §5 Bilinen sınırlar

1. `tsc.c:__rdtsc()` — ölçüm kaynağı seçimi `plan` dalının konusudur (root kapsam §D.3).
2. `virtio-gpu` pointer trap — `cross` dalı sınırı (root kapsam §D.4).
3. K019-testing erişimi yok; 0 satır.
4. `phpunit.xml` → `uygulama-testi` ve `ci-ve-kalite-kapisi`; burada değil.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
