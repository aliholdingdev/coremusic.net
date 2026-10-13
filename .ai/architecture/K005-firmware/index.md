---
node_id: K005-firmware/index.md
node_type: domain
status: draft
parent_id: null
children:
  - boot
  - runtime
  - device-control
  - audio-control
  - power-control
  - diagnostics
  - update
  - security
  - testing


depends_on:
  - K000-isletim-sistemi
  - K001-donanım
  - K003-audio
  - K004-dsp
evidence:
  - "coremusic-katmanli-mimari-agaci-v2.md §2.5 — 9 çocuk + firmware-master.md — okundu 2026-10-12"
  - ".ai/architecture/index.md §8.1 — iki-ekseni gruplama — okundu 2026-10-12"
  - "ADR-097 §5.5 — grup şeması — okundu 2026-10-12"
  - "disk ölçümü — 12 kök MD · 2026-10-13"
validation:
  - "2026-10-13 — §GRUPLAR tablosu 12/12 satır ile klasör içeriği birebir karşılaştırıldı"
  - "2026-10-13 — çocuk kataloğu 9/9 ad — dosya adlarıyla eşleşti"
open_questions:
  - "dalga-3 (diagnostics · security · testing) — ⚠️ VERIFICATION REQUIRED"
  - "v2 §6 ID CONFLICT: `K005-veri-yonetimi` ↔ `K005-firmware` — onaysız yeniden adlandırma yasak"
  - "çocuk rol satırları v2'de yok, adlarından türetildi — ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K005-firmware — Firmware Alanı (kademe-1 künyesi)

> **ELI15:** Donanımın kendi küçük yazılımı — cihaz açılırken çalışan "iç işletim sistemi".
> `boot` açılır · `runtime` döner · `device-control`/`audio-control` parçaya komut yollar ·
> `power-control` gücü yönetir · `diagnostics` sınar · `update` günceller · `security` kilitler ·
> `testing` test eder. **Ne DEĞİL:** donanımın fiziksel yüzeyi (K001) · ses algoritması (K004) ·
> sesin taşınması (K003) · host işletim sistemi (K000).

## §1 Kademe-2 çocuk kataloğu (v2 §2.5 — 9 çocuk, sıra korunur — KURAL 1: ad değiştirilemez)

| # | Çocuk | Rol (1 satır) | Dalga | Durum |
|---|---|---|---|---|
| 1 | boot | Reset → kendini tanıma → yükleme | dalga-2 | 6 MD yazıldı |
| 2 | runtime | Kontrol döngüsü · scheduler · watchdog | dalga-2 | 6 MD yazıldı |
| 3 | device-control | Parçaya komut (aç/kapat/ayarla · bus · driver) | dalga-2 | 6 MD yazıldı |
| 4 | audio-control | Ses donanımı ayarı (register · clock · mute) | dalga-2 | 6 MD yazıldı |
| 5 | power-control | Uyku, kapanma, akım ve termal kontrol | dalga-2 | 6 MD yazıldı |
| 6 | diagnostics | Self-test · hata raporu · log | dalga-3 | yalnız eski index.md |
| 7 | update | OTA · A/B · rollback | dalga-2 | 6 MD yazıldı |
| 8 | security | İmza, secure boot, erişim, koruma | dalga-3 | yalnız eski index.md |
| 9 | testing | Firmware testleri ve kabul kriterleri (unit · HIL) | dalga-3 | yalnız eski index.md |

> **KURAL 1 (v2 §3):** 9 çocuk adı ve `firmware-master.md` adı değiştirilmez; rol satırları
> adından türetildi → `⚠️ VERIFICATION REQUIRED`. Rol satırları `⚠️` ile birlikte okunur.
> **G4 (`teknoloji`) notu:** yalnız kademe-3 derin-dal dosyalarını kapsar; K005 kökünde
> G4 kullanılmaz — `firmware-evrensel-dil.md` G1 `mimari`'ye yazılır (sözlük = mimari).

**Grup dağılımı (bu üretim):** mimari 7 · kod 2 · kanit 3 = **kök 12** · kademe-2 36 (6 çocuk × 6) ·
toplam 48 (mimari 25 · kod 8 · kanit 15). Doğrulama kapıları D1–D10: `firmware-master.md` §9 ·
tam döküm `firmware-dogrulama.md` · kademe-3 teknoloji klasörleri (G4) `⚠️ RESERVED — ADR`.

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan group**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi, çocuk kataloğu, grup haritası | Kademe-1 |
| mimari | `firmware-master.md` | Anayasa — kapsam omurgası, iddialar, §9 doğrulama | Kademe-1 |
| mimari | `firmware-kapsam.md` | Kapsam sınırı: içinde / dışında / komşu | Kademe-1 |
| mimari | `firmware-bilesenler.md` | Bileşen kataloğu + kademe-3 planı | Kademe-1 |
| mimari | `firmware-bagimliliklar.md` | Bağımlılık haritası + mimari↔kod bağları | Kademe-1 |
| mimari | `firmware-kararlar.md` | Karar kaydı (ADR bağlantılı) | Kademe-1 |
| mimari | `firmware-evrensel-dil.md` | Kavram→terim sözlüğü + adlandırma kuralları | Kademe-1 |
| kod | `firmware-kod-ornekleri.md` | Gerçek repo kod örnekleri — `Dosya: … / Satır: …` | Kademe-1 |
| kod | `firmware-test-stratejisi.md` | Test piramidi · HIL · gate'ler | Kademe-1 |
| kanit | `firmware-dogrulama.md` | Kriterler (D1…) + doğrulama tabloları | Kademe-1 |
| kanit | `firmware-web-arastirma.md` | Web araştırması — iddia/URL/tarih kayıtları | Kademe-1 |
| kanit | `firmware-riskler.md` | Risk matrisi + azaltma planı | Kademe-1 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
