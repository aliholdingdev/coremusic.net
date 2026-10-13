---
node_id: K000-isletim-sistemi/isletim-sistemi-testing/uygulama-testi
node_type: subdomain
status: active
parent_id: K000-isletim-sistemi/isletim-sistemi-testing
children:
  - K000-isletim-sistemi/isletim-sistemi-testing/uygulama-testi/uygulama-testi-anayasa.md
  - K000-isletim-sistemi/isletim-sistemi-testing/uygulama-testi/uygulama-testi-surucu.md
  - K000-isletim-sistemi/isletim-sistemi-testing/uygulama-testi/uygulama-testi-ornekler.md
depends_on: []
evidence: []
validation: []
open_questions:
  - T2 [UNKNOWN] — uygulama-testi için coverage eşiği yok; coverage ağacı `K019-testing` dalındadır, burada esas alınmaz.
  - T3 [UNKNOWN] — `auth.coremusic.net` test paketi mi DEĞİL mi; `composer.json` test scripti YOK (komşu dal kanıtı).
group: mimari
---

# Uygulama-Testi — POSIX Kullanıcı Uzayı Uygulama Testi

> **Kapsam:** Bu alt-dal PHP 8.x uygulama testidir (unit/integration/contract),
> POSIX davranışının uygulama katmanındaki yansımasıdır. Çekirdek/QEMU testi →
> `cekirdek-test/`; CI kalite kapısı → `ci-ve-kalite-kapisi/`.

## §1 Dosyalar (Kademe-3)

| Dosya | Tür | `group` | Ne verir |
|---|---|---|---|
| `uygulama-testi-anayasa.md` | modül | `mimari` | Test piramidi, PHPUnit 11.5 konfigürasyonu, mock/fake stratejisi, boundary doğrulama, N+1 izolasyonu |
| `uygulama-testi-surucu.md` | modül | `mimari` | `phpunit.xml` sürücüsü, testsuite matrisi, DB izolasyonu, `Select-Object -Index` kanıt protokolü |
| `uygulama-testi-ornekler.md` | modül | `kod` | Gerçek repo test örnekleri, assert kalıpları, anti-pattern tablosu |

## §2 Konu haritası (issue → dosya)

| Issue (kapsam.md §C) | Dosya |
|---|---|
| D-01 test piramidi | anayasa §1 |
| D-02 PHPUnit 11.5 | anayasa §2 + surucu §1 |
| D-03 mock/fake | anayasa §3 + ornekler §5 |
| D-04 boundary doğrulama | anayasa §4 + ornekler §6 |
| D-05 gerçek repo kanıtı | ornekler §1 |
| D-06 ölçüm/coverage | anayasa §6 |

## §3 Sorumluluk sınırı (komşu dallar)

| Konu | Sınır | Bu alt-dal ne YAPAR |
|---|---|---|
| Kernel/QEMU testi | → `isletim-sistemi-testing/cekirdek-test` | Yalnız uygulama katmanı |
| CI pipeline kalite kapısı | → `isletim-sistemi-testing/ci-ve-kalite-kapisi` | Kapıda çalışan test komutunun sahibi |
| Coverage ağacı | → `K019-testing` | Erişim YOK (0 satır); eşik uydurulmaz |
| Uygulama mimarisi | → `isletim-sistemi-api` | Test stratejisi; endpoint tasarımı DEĞİL |
| Ses/isletim uygulaması | → `isletim-sistemi-audio` | Genel uygulama testi; ses DSP testi DEĞİL |
| Güvenlik testi (auth/CSRF) | → `isletim-sistemi-security` | Davranış testi; güvenlik politikası DEĞİL |
| CI/CD altyapısı | → `K018-devops` | Test adımı; pipeline sahipliği DEĞİL |
| Ölçüm telemetrisi | → `isletim-sistemi-observability` | Test metrikleri; canlı telemetri DEĞİL |
| Sürücü/donanım arayüzü | → `isletim-sistemi-kernel` | Uygulama testi sınırı; kod DEĞİL |
| Süreç/örtü bölgesi modeli | → `isletim-sistemi-core` | Uygulama davranışı; model DEĞİL |
| Zamanlama/gecikme ölçümü | → `isletim-sistemi-plan` | Test metrikleri; planlayıcı DEĞİL |
| Platform matrisi | → `isletim-sistemi-turu` | OS satırları; kurulum DEĞİL |

## §4 Kanıt ve validation durumu

| Alan | Değer |
|---|---|
| `evidence` | `[]` — doğrulanmış iddia YOK (repo disk kanıtı kapsam kanıtı olarak sayılmaz) |
| `validation` | `[]` |
| Disk kanıtı | Bu alt-dalda ÜRETİLEN gerçek repo kanıtı: `shared\phpunit.xml` (39 satır, 8 testsuite) |
| `open_questions` | T2 eşik + T3 auth paketi → `[UNKNOWN]` |

## §5 Bilinen sınırlar

1. `composer.json` → `test: unit` scripti YOK; `unit:` variant PHPStan.
2. `auth.coremusic.net/composer.json` test scripti YOK.
3. Test dosyaları salt-okunur; `*.php` yazma yasağı bağlayıcı.
4. Kod örneklerindeki satır numaraları `Select-Object -Index` ile doğrulanmıştır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
