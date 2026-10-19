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
  - "disk ölçümü — 64 MD (kök 12 + kademe-2 52) · 2026-10-14 — 29 eksik modül tamamlandı"
validation:
## §GRUPLAR

> İki-ekseni gruplama (registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**. `Kademe` ataması şeffaf rol kuralıdır (index/master/kapsam → Kademe-1; destek → Kademe-2) — diskte `kademe` alanı yok, uydurma yok.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi | Kademe-1 |
| mimari | `firmware-master.md` | Firmware anayasa | Kademe-1 |
| mimari | `firmware-kapsam.md` | Kapsam sınırı | Kademe-1 |
| mimari | `firmware-bilesenler.md` | Bileşen kataloğu | Kademe-2 |
| mimari | `firmware-bagimliliklar.md` | Bağımlılık haritası | Kademe-2 |
| mimari | `firmware-kararlar.md` | Mimari karar kaydı | Kademe-2 |
| kod | `firmware-kod-ornekleri.md` | Gerçek repo kod örnekleri | Kademe-2 |
| kanit | `firmware-riskler.md` | Risk matrisi | Kademe-2 |
| kod | `firmware-test-stratejisi.md` | Test stratejisi | Kademe-2 |
| kanit | `firmware-web-arastirma.md` | İddia/URL kayıtları | Kademe-2 |
| mimari | `firmware-evrensel-dil.md` | TR domain / EN teknik sözlük | Kademe-2 |
| kanit | `firmware-dogrulama.md` | Doğrulama protokolü | Kademe-2 |


## §BAĞLANTILAR

> **Yön işareti = kanıt yönü, sözleşme değil:** `→` bu K'nın kök içerik dosyaları hedefe referans veriyor · `←` hedef K'nın kök dosyaları bu K'ya referans veriyor · `↔` ikisi de · `⚠️` kök taramada kanıt yok.
> **Yöntem (2026-10-18):** her K'nın kök .md'leri (index.md hariç) taranıp hedef K adının toplam geçişi sayıldı (zero-hallucination; uydurma bağımlılık yok). Zenginleşik 4 tablo (K003/K008/K011/K012) korunmuştur.

| Hedef K | Wiki-link | Göreli dosya yolu | Yön | İlişki | Kanıt |
|---|---|---|---|---|---|
| K005-firmware | [[K005-firmware]] | ./K005-firmware/index.md | ○ | bu K (kendi kökü) | registry §5 + disk |
| K000-isletim-sistemi | [[K000-isletim-sistemi]] | ../K000-isletim-sistemi/index.md | ↔ | bağımlı (depends_on) | M→22 · M←9 (kök .md, 2026-10-18) |
| K001-donanım | [[K001-donanım]] | ../K001-donanım/index.md | ↔ | bağımlı (depends_on) | M→27 · M←15 (kök .md, 2026-10-18) |
| K002-electroncis | [[K002-electroncis]] | ../K002-electroncis/index.md | ← | karşı taraf güçlü referanslıyor | M→0 · M←44 (kök .md, 2026-10-18) |
| K003-audio | [[K003-audio]] | ../K003-audio/index.md | ↔ | bağımlı (depends_on) | M→21 · M←8 (kök .md, 2026-10-18) |
| K004-dsp | [[K004-dsp]] | ../K004-dsp/index.md | ↔ | bağımlı (depends_on) | M→30 · M←15 (kök .md, 2026-10-18) |
| K006-yapay-zeka | [[K006-yapay-zeka]] | ../K006-yapay-zeka/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K007-data | [[K007-data]] | ../K007-data/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K008-domain | [[K008-domain]] | ../K008-domain/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K009-security | [[K009-security]] | ../K009-security/index.md | ↔ | iki yönlü atıf | M→3 · M←1 (kök .md, 2026-10-18) |
| K010-network | [[K010-network]] | ../K010-network/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K011-application | [[K011-application]] | ../K011-application/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K012-services | [[K012-services]] | ../K012-services/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K013-integraion | [[K013-integraion]] | ../K013-integraion/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K014-media | [[K014-media]] | ../K014-media/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K015-AI | [[K015-AI]] | ../K015-AI/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K016-observability | [[K016-observability]] | ../K016-observability/index.md | ← | karşı taraf referanslıyor | M→0 · M←2 (kök .md, 2026-10-18) |
| K019-comilance | [[K019-comilance]] | ../K019-comilance/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K020-discovery | [[K020-discovery]] | ../K020-discovery/index.md | ← | karşı taraf referanslıyor | M→0 · M←1 (kök .md, 2026-10-18) |
| K021-recomenataion | [[K021-recomenataion]] | ../K021-recomenataion/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K17-devops | [[K17-devops]] | ../K17-devops/index.md | ← | karşı taraf referanslıyor | M→0 · M←2 (kök .md, 2026-10-18) |
| K18-testing | [[K18-testing]] | ../K18-testing/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |


## §GENEL BAKIŞ

| Alan | Değer |
|---|---|
| **K Kodu** | K005-firmware |
| **K Adı** | Firmware |
| **Kapsam** | Donanımın kendi küçük yazılımı — cihaz açılırken çalışan "iç işletim sistemi". boot açılır · runtime döner · device-control/audio-control parçaya komut yollar · power-control gücü yönetir · diagnostics sınar · update günceller · security kilitler · testing test eder. Donanımın fiziksel yüzeyi (K001), ses algoritması (K004), sesin taşınması (K003) ve host işletim sistemi (K000) kapsam dışıdır. 9 kademe-2 çocuk. |
| **Kritik Bağımlılıklar** | K000-isletim-sistemi · K001-donanım · K003-audio · K004-dsp (kanıt: FM depends_on · firmware-bagimliliklar.md E1–E4) |
| **Durum** | draft (9 kademe-2 çocuk · 64 MD) |
| **Son Güncelleme** | 2026-10-14 (9 çocuk §GRUPLAR disk ile birebir · 29 eksik modül tamamlandı) |

> **ELI15:** Donanımın kendi küçük yazılımı — cihaz açılırken çalışan "iç işletim sistemi".
> `boot` açılır · `runtime` döner · `device-control`/`audio-control` parçaya komut yollar ·
> `power-control` gücü yönetir · `diagnostics` sınar · `update` günceller · `security` kilitler ·
> `testing` test eder. **Ne DEĞİL:** donanımın fiziksel yüzeyi (K001) · ses algoritması (K004) ·
> sesin taşınması (K003) · host işletim sistemi (K000).

## §1 Kademe-2 çocuk kataloğu (v2 §2.5 — 9 çocuk, sıra korunur — KURAL 1: ad değiştirilemez)

| # | Çocuk | Rol (1 satır) | Dalga | Durum |
|---|---|---|---|---|
| 1 | boot | Reset → kendini tanıma → yükleme | dalga-2 | 6 MD yazıldı |
| 2 | runtime | Kontrol döngüsü · scheduler · watchdog | dalga-2 | 4 MD (kapsam/dogrulama yok) |
| 3 | device-control | Parçaya komut (aç/kapat/ayarla · bus · driver) | dalga-2 | 6 MD yazıldı |
| 4 | audio-control | Ses donanımı ayarı (register · clock · mute) | dalga-2 | 6 MD yazıldı |
| 5 | power-control | Uyku, kapanma, akım ve termal kontrol | dalga-2 | 6 MD yazıldı |
| 6 | diagnostics | Self-test · hata raporu · log | dalga-3 | 6 MD (2026-10-14) |
| 7 | update | OTA · A/B · rollback | dalga-2 | 6 MD yazıldı |
| 8 | security | İmza, secure boot, erişim, koruma | dalga-3 | 6 MD (2026-10-14) |
| 9 | testing | Firmware testleri ve kabul kriterleri (unit · HIL) | dalga-3 | 6 MD (2026-10-14) |

> **KURAL 1 (v2 §3):** 9 çocuk adı ve `firmware-master.md` adı değiştirilmez; rol satırları
> adından türetildi → `⚠️ VERIFICATION REQUIRED`. Rol satırları `⚠️` ile birlikte okunur.
> **G4 (`teknoloji`) notu:** yalnız kademe-3 derin-dal dosyalarını kapsar; K005 kökünde
> G4 kullanılmaz — `firmware-evrensel-dil.md` G1 `mimari`'ye yazılır (sözlük = mimari).

**Grup dağılımı (2026-10-14 sayımı):** mimari 7 · kod 2 · kanit 3 = **kök 12** · kademe-2 52 (9 çocuk: 8×6 · runtime 4) ·
toplam 64 (mimari 41 · kod 12 · kanit 11). Doğrulama kapıları D1–D10: `firmware-master.md` §9 ·
tam döküm `firmware-dogrulama.md` · kademe-3 teknoloji klasörleri (G4) `⚠️ RESERVED — ADR`.

**İlişkili vault projesi (2026-10-15):** **Neva Engine** — `.ai/projects/NevaEngine/` (11 md). `audio-control` (çocuk #4: register → clock → mute) ile sınır komşuluğu: Neva host-side ses motoru DAC ayarlarını firmware katmanına bırakır (XMOS XU316 firmware'i bu domaindedir); Neva Engine bu domainin yerine geçmez, K003-audio üzerinden beslenir.

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
