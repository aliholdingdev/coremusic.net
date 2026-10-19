---
node_id: K001-donanım
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - donanım-kernel
  - donanım-core
  - donanım-api
  - donanaım-type
  - donanım-devices
depends_on:
  - K000-isletim-sistemi
evidence:
  - "v2 §2.2 mini-ağaç — coremusic-katmanli-mimari-agaci-v2.md satır 170-186"
  - "https://www.ti.com/lit/gpn/TPA3255 (erişim 2026-10-12)"
  - "https://github.com/EliMattingly22/TPA3255_ClassD_PBTL (erişim 2026-10-12)"
  - ".ai/AGENTS.md §4 (Audio Hardware Engineer: PCM3168A · AK4458 · Class AB)"
validation:
  - "Çocuk listesi v2 §2.2 ile birebir karşılaştırıldı — 4/4 eşleşme (2026-10-12)"
  - "donanaım-type altında 3 bounded-context (hdd · audio · ssd) ayrı ayrı açıldı (2026-10-12)"
  - "2026-10-13 genişleme (Vault Steward onayı): donanım-devices 5. çocuk + donanaım-type'a 7 tür (display · power · cables · network · memory · cpu · gpu) — anayasa §2/§10 metin revizyonu PROPOSED (onay bekliyor)"
open_questions:
  - "v2 §3.1: K001 ile K003 arasında K002 yok (ID GAP) → ⚠️ VERIFICATION REQUIRED"
  - "TPA3255 PVDD aralığı: görev iddiası 24–52 V, TI datasheet 18–53.5 V → ⚠️ VERIFICATION REQUIRED"
  - "donanım-kernel ile donanım-core sınırı v2'de tanımsız → ⚠️ VERIFICATION REQUIRED"
  - "donanaım-type yazımı kaynaktan birebir korunmuştur (düzeltme YASAK) — bilgi notudur"
group: mimari
---
# K001-donanım — Kayıt

## §GENEL BAKIŞ

| Alan | Değer |
|---|---|
| **K Kodu** | K001-donanım |
| **K Adı** | Donanım (Hardware) |
| **Kapsam** | Kasadaki fiziksel parçaların vault karşılığı — disk, ses kartı, ekran, kablolar ve 10 tür alt bağlam (hdd · audio · ssd · display · power · cables · network · memory · cpu · gpu). DAC/ADC çipleri (PCM3168A · AK4458) ve Class AB amplifikatör tarafının mimari evidir; kart/PCB tasarımı K002-electroncis'e aittir (sınır KAPANDI 2026-10-15). 5 kademe-2 çocuk + donanaım-type altında 10 bounded-context. |
| **Kritik Bağımlılıklar** | K000-isletim-sistemi · K005-firmware (kanıt: FM depends_on · donanım-bagimliliklar.md §2 "Giden: K000 · K005") |
| **Durum** | draft (5 kademe-2 çocuk + 10 alt bağlam) |
| **Son Güncelleme** | 2026-10-14 (§1.5 İlişkili Proje Kaydı — Neva Engine K1 donanım eşlemesi) |
## §1.5 İlişkili Proje Kaydı (2026-10-14)

**Neva Engine K1 donanım eşlemesi** — `.ai/projects/NevaEngine/00-sdk-lib-envanter.md` + `01-gelistirme-plani.md`. Yazılım tarafı: ASIO/WASAPI/ALSA/CoreAudio backends (Faz 1) → PCM3168A (6-in/8-out, SNR 112dB) / AK4458 (8-kanal 32-bit DAC) çıkışları. Loopback THD+N/SNR ölçüm kapısı (02-dsp-zincir-mimari §4): THD+N <0.005% · SNR >105dB · gerçek DAC'ta AES17 ölçümü (ADR-038). donanaım-type/audio/cipler/ (ak4458-dac · pcm3168a-codec) ile birebir eşleşme.

**ELI15 (v2 §2.2, birebir):** Kasadaki fiziksel parçalar — disk, ses kartı, ekran, kablolar.
> **Alan notu:** depends_on satırı **tasarım iddiasıdır** [TASARIM] — gerekçesi ve
> etiketli tam matris donanım-bagimliliklar.md içindedir.

## Çocuk kataloğu (kademe-2)

| # | Çocuk | Ne işe yarar (1 satır) | Kanıt |
|---|---|---|---|
| 1 | donanım-kernel | Donanıma en yakın donanım katmanı (çekirdek/alt donanım) | ⚠️ yok |
| 2 | donanım-core | Donanım tarafının merkez bileşenleri | ⚠️ yok |
| 3 | donanım-api | Donanıma erişim arayüzü (sürücü/cihaz API'si) | ⚠️ yok |
| 4 | donanaım-type | Donanım türü ayrılan dal — altında 10 alt bağlam (2026-10-13 genişleme) | ✔ v2 mini-ağaç + Vault Steward onayı |
| 5 | donanım-devices | Donanım sınıflandırma/envanter — cihaz tespiti ve kayıt | ✔ IMPLEMENTED: `DeviceDetector.php` · `DeviceManager.php` |

> 1 satırlıklar **ad kökeninden** türetilmiştir; v2 §2.2 tek tek tanım yazmaz → içerik
> VERIFICATION REQUIRED'dır. yazım (donanaım-type) **dokunulmaz** (v2 §3 KURAL 1).

### Kademe-3 (donanaım-type altı — bounded-context)

| # | Alt bağlam | Ne işe yarar (1 satır) | Kanıt |
|---|---|---|---|
| 1 | hdd | Disk depolama donanımı | ⚠️ yok |
| 2 | audio | Ses kartı / ses donanımı | ✔ §4 (PCM3168A · AK4458) |
| 3 | ssd | Katı hal depolama donanımı | ⚠️ yok |
| 4 | display | Ekran/panel/görüntü arayüzü donanımı sınıfı (2026-10-13, Vault Steward onayı) | ⚠️ anayasa §2 çatışma kaydı — power-kapsam/display-kapsam §0 |
| 5 | power | Güç kaynağı/koruma donanımı sınıfı — sınır: tasarım K002 (2026-10-13, Vault Steward onayı) | ⚠️ anayasa §2 çatışma kaydı — power-kapsam §0 |
| 6 | cables | Kablo/konnektör arayüzü donanımı sınıfı (2026-10-13, Vault Steward onayı) | ⚠️ anayasa §10 çatışma kaydı — cables-kapsam §0 |
| 7 | network | Ağ arayüzü donanımı sınıfı (NIC/Wi-Fi) (2026-10-13, Vault Steward onayı) | ⚠️ anayasa §10 çatışma kaydı — network-kapsam §0 |
| 8 | memory | Bellek donanımı sınıfı (DDR/ECC) (2026-10-13, Vault Steward onayı) | ⚠️ anayasa §10 çatışma kaydı — memory-kapsam §0 |
| 9 | cpu | İşlemci/SoC donanımı sınıfı (2026-10-13, Vault Steward onayı) | ⚠️ anayasa §10 çatışma kaydı — cpu-kapsam §0 |
| 10 | gpu | Graphics/compute donanımı sınıfı (2026-10-13, Vault Steward onayı) | ⚠️ anayasa §10 çatışma kaydı — gpu-kapsam §0 |

## Durum

| Öğe | Değer |
|---|---|
| status | draft |
| Kademe | 2 · domain (5 alt klasör + 10 alt bağlam) |
| Master dosyası | donanım-anayasa.md (v2'de yoktu — ADR-097 K2 istisnası) |
| Ek dosyalar | donanım-bagimliliklar.md · donanım-dogrulama.md |
| Bloklayan açık soru | PVDD aralığı · core/kernel sınırı · K002 GAP (hepsi ⚠️) |

**Doğrulama kapısı:** donanım-dogrulama.md §2 ölçütlerinin tamamı → status: verified.

---

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `donanım-anayasa.md` | Anasözleşme ve bağlayıcı mimari kurallar | Kademe-1 |
| mimari | `donanım-bagimliliklar.md` | Katman bağı ve used_by haritası | Kademe-1 |
| mimari | `donanım-evrensel-dil.md` | TR↔EN terim karşılık sözlüğü | Kademe-1 |
| mimari | `donanım-kararlar.md` | Karar, alternatif ve gerekçe kaydı | Kademe-1 |
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-1 |
| kod | `donanım-kod-ornekleri.md` | Gerçek repo kodu, dosya ve satır | Kademe-1 |
| kod | `donanım-platformlararasi-kod-ornekleri.md` | Dil gruplı Windows/macOS/Linux çapraz-platform kod şablonları (§G1–§G8) | Kademe-1 |
| kod | `donanım-test-stratejisi.md` | Test kapsamı ve kabul kriterleri | Kademe-1 |
| kanit | `donanım-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-1 |
| kanit | `donanım-riskler.md` | Risk, etki, olasılık ve azaltım planı | Kademe-1 |
| kanit | `donanım-web-arastirma.md` | Sistemli resmi kaynak tarama kaydı | Kademe-1 |
## §BAĞLANTILAR

> **Yön işareti = kanıt yönü, sözleşme değil:** `→` bu K'nın kök içerik dosyaları hedefe referans veriyor · `←` hedef K'nın kök dosyaları bu K'ya referans veriyor · `↔` ikisi de · `⚠️` kök taramada kanıt yok.
> **Yöntem (2026-10-18):** her K'nın kök .md'leri (index.md hariç) taranıp hedef K adının toplam geçişi sayıldı (zero-hallucination; uydurma bağımlılık yok). Zenginleşik 4 tablo (K003/K008/K011/K012) korunmuştur.

| Hedef K | Wiki-link | Göreli dosya yolu | Yön | İlişki | Kanıt |
|---|---|---|---|---|---|
| K001-donanım | [[K001-donanım]] | ./K001-donanım/index.md | ○ | bu K (kendi kökü) | registry §5 + disk |
| K000-isletim-sistemi | [[K000-isletim-sistemi]] | ../K000-isletim-sistemi/index.md | ↔ | bağımlı (depends_on) | M→19 · M←11 (kök .md, 2026-10-18) |
| K002-electroncis | [[K002-electroncis]] | ../K002-electroncis/index.md | ↔ | iki yönlü güçlü referans | M→8 · M←25 (kök .md, 2026-10-18) |
| K003-audio | [[K003-audio]] | ../K003-audio/index.md | ↔ | iki yönlü güçlü referans | M→12 · M←10 (kök .md, 2026-10-18) |
| K004-dsp | [[K004-dsp]] | ../K004-dsp/index.md | ↔ | iki yönlü güçlü referans | M→12 · M←13 (kök .md, 2026-10-18) |
| K005-firmware | [[K005-firmware]] | ../K005-firmware/index.md | ↔ | iki yönlü güçlü referans | M→15 · M←27 (kök .md, 2026-10-18) |
| K006-yapay-zeka | [[K006-yapay-zeka]] | ../K006-yapay-zeka/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K007-data | [[K007-data]] | ../K007-data/index.md | ↔ | iki yönlü atıf | M→1 · M←2 (kök .md, 2026-10-18) |
| K008-domain | [[K008-domain]] | ../K008-domain/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K009-security | [[K009-security]] | ../K009-security/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K010-network | [[K010-network]] | ../K010-network/index.md | ← | karşı taraf güçlü referanslıyor | M→0 · M←20 (kök .md, 2026-10-18) |
| K011-application | [[K011-application]] | ../K011-application/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K012-services | [[K012-services]] | ../K012-services/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K013-integraion | [[K013-integraion]] | ../K013-integraion/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K014-media | [[K014-media]] | ../K014-media/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K015-AI | [[K015-AI]] | ../K015-AI/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K016-observability | [[K016-observability]] | ../K016-observability/index.md | ← | karşı taraf referanslıyor | M→0 · M←2 (kök .md, 2026-10-18) |
| K019-comilance | [[K019-comilance]] | ../K019-comilance/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K020-discovery | [[K020-discovery]] | ../K020-discovery/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K021-recomenataion | [[K021-recomenataion]] | ../K021-recomenataion/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K17-devops | [[K17-devops]] | ../K17-devops/index.md | ← | karşı taraf referanslıyor | M→0 · M←1 (kök .md, 2026-10-18) |
| K18-testing | [[K18-testing]] | ../K18-testing/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |

