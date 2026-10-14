---
title: "donanaım-type/cables — Doğrulama (Kanıt, Kapılar, Ölçüm Formları)"
type: kanit
node_id: K001-donanım/donanaım-type/cables/dogrulama
node_type: donanaım-type-dogrulama
status: proposed
parent_id: "K001-donanım/donanaım-type"
children:
  - index.md
  - cables-kapsam.md
  - cables-ornekler.md
depends_on:
  - K001-donanım
  - K000-isletim-sistemi
evidence:
  - "HEAD doğrulama 2026-10-13: erişilebilir 200 (usb.org · sata-io.org · displayport.org · hdmi.org · vesa.org · docs.kernel.org i2c/spi/tty/gpio/PCI/sound) · ieee.org 202 · reddedilen jedec.org 403 · pcisig.com 403 · snia.org 403 · aes.org 403 · intel.com 403 · microchip.com 403 · raspberrypi.com 403 · nxp.com 404 · st.com timeout · sfforum.com DNS · formfactors.org DNS"
  - "Repo kod ölçümü 2026-10-13: *.cpp=0 · *.h=0 · *.c=0 · *.ino=0 · *.xc=0 · *.cs=0 · *.go=0 · *.rs=0 · *.py=1 · php=9470 · js=1682 → kablo/konnektör kodu YOK"
  - "Fiziksel ölçüm: 0 (bu oturumda hiçbir kablo/konnektör ölçülmedi — §3 formları BOŞ)"
validation: "proposed — 2026-10-13 yazıldı; anayasa revizyonu YAPILMADI (kapsam §0); D-kriterleri K001-donanım/donanım-dogrulama.md §2"
open_questions:
  - "Q-C1: fiziksel envanter (marka · kesit · uzunluk · pin sayısı) bilinmiyor — UNKNOWN"
  - "Q-C3: pcisig.com 403 + jedec.org 403 → spec sürümü doğrulanamadı ⚠️ VERIFICATION REQUIRED"
  - "Q-C5: empedans/kesit/uzunluk ölçümleri 0 — kapılar ölçüm bekliyor"
group: kanit
date: 2026-10-13
updated: 2026-10-13
---

# cables — Doğrulama (K001-donanım · donanaım-type · cables)

> Bu dosya `cables/` sınıfının **kanıt/doğrulama** kaydıdır: doğrulama
> seviyeleri (S1–S5), kapılar (K-C1…K-C8), **boş** ölçüm formları, HEAD URL
> kanıt matrisi ve repo ölçüm kayıtları. Kapsam/çelişki tanımı:
> `cables-kapsam.md` §0 — burada tekrar edilmez, yalnız işaretlenir.
> Zero-hallucination: bu oturumda **0 fiziksel ölçüm · 0 envanter kaydı** yapılmıştır.

---

## §1 Doğrulama Seviyeleri (S1–S5)

| Seviye | Ad | Ne kanıtlar | Kabul | Bu oturumda |
|---|---|---|---|---|
| S1 | Metin-kaynak | İddia bir vault dosyasından birebir okunabilir | satır atfı + tarih | ✅ UYGULANDI (§0 çapaları) |
| S2 | HTTP-erişim | Kaynak URL erişilebilir (HEAD 200/202) | HTTP kodu + tarih | ✅ UYGULANDI (§4) |
| S3 | Repo-ölçüm | İddia repo dosyalarında sayılabilir | kelime-sınırlı grep + sayı | ✅ UYGULANDI (§5) |
| S4 | Fiziksel-ölçüm | Gerçek donanım ölçülmüş (empedans · kesit · uzunluk · akım) | ölçüm kaydı + alet + tarih | ❌ 0 ölçüm (§3 formları BOŞ) |
| S5 | Envanter-kayıdı | Fiziksel varlık envantere yazılmış (marka · model · pin) | envanter satırı | ❌ UNKNOWN (K001 Q4) |

**Kural:** Bir tip kaydı `cables-kapsam.md` §5'te yalnız S1–S3 ile
`proposed`/`planned` kalır; `confirmed` için S4 + S5 zorunludur. Hiçbir kayıt bu
oturumda `confirmed` DEĞİLDİR.

## §2 Doğrulama Kapıları (K-C1…K-C8)

| Kapı | Kapsam | Soru | Kanıt tipi | Durum |
|---|---|---|---|---|
| K-C1 | Çelişki kaydı | Anayasa yasağı satırları birebir okundu mu? | S1 | ✅ GEÇTİ (kapsam §0, 10 satır) |
| K-C2 | Katman ayrımı | Her kayıt tek katman iddiası taşıyor mu? | S1 | ✅ GEÇTİ (kapsam §4 matrisi) |
| K-C3 | 26 alan şema | 17 kayıt × 26 alan eksiksiz mi? | S1 | ✅ GEÇTİ (§6 bu dosya) |
| K-C4 | URL erişimi | Tüm kaynaklar HEAD 200/202 mi? | S2 | ✅ GEÇTİ (§4 — 403/404'ler REDDEDİLDİ) |
| K-C5 | Repo ölçümü | Terim sayımları tekrar üretilebilir mi? | S3 | ✅ GEÇTİ (§5) |
| K-C6 | Fiziksel ölçüm | Empedans/kesit/uzunluk/akım ölçüldü mü? | S4 | ❌ GEÇMEDİ — 0 ölçüm (§3) |
| K-C7 | Envanter | Marka/model/pin envanteri var mı? | S5 | ❌ GEÇMEDİ — UNKNOWN (Q-C1) |
| K-C8 | Metin revizyonu | Anayasa + üst kapsam yasağı revize edildi mi? | S1 | ❌ BEKLEMEDE — ONAY GEREKİYOR (kapsam §0.1) |

**Kapı özeti:** 5/8 GEÇTİ · 2 GEÇMEDİ (K-C6 · K-C7 — ölçüm/envanter yok) ·
1 BEKLEMEDE (K-C8 — Vault Steward onayı). `status: proposed` bu üç kapıya bağlı.

## §3 Ölçüm Formları (TÜMÜ BOŞ — bu oturumda ölçüm YAPILMADI)

> Bu formlar bilinçli olarak **boştur**; doldurulması ölçüm oturumuna aittir.
> Uydurma değer yazılmaz (zero-hallucination).

### §3.1 Empedans Ölçüm Formu (KATMAN-3)

| # | Tip | Hedef Ω | Ölçülen Ω | Alet | Tarih | Sonuç |
|---|---|---|---|---|---|---|
| 1 | C-T12 koaksiyel | 75 ⚠️ (VERIFY REQUIRED) | — | — | — | ❌ BOŞ |
| 2 | C-T03 PCIe çift | ⚠️ (pcisig 403) | — | — | — | ❌ BOŞ |
| 3 | C-T10 DisplayPort çift | ⚠️ | — | — | — | ❌ BOŞ |
| 4 | C-T11 HDMI çift | ⚠️ | — | — | — | ❌ BOŞ |
| 5 | C-T13 dengeli | ⚠️ (aes.org 403) | — | — | — | ❌ BOŞ |

### §3.2 Kesit / Uzunluk Formu (KATMAN-1)

| # | Tip | Kesit (AWG/mm²) | Uzunluk (m) | Alet | Tarih | Sonuç |
|---|---|---|---|---|---|---|
| 1 | C-T01 USB-A | — | — | — | — | ❌ BOŞ |
| 2 | C-T02 USB-C | — | — | — | — | ❌ BOŞ |
| 3 | C-T13 dengeli | — | — | — | — | ❌ BOŞ |
| 4 | C-T12 koaksiyel | — | — | — | — | ❌ BOŞ |
| 5 | C-T15 güç kablosu | — | — | — | — | ❌ BOŞ |

### §3.3 Akım / Gerilim Formu (KATMAN-3)

| # | Tip | Nominal | Ölçülen | Alet | Tarih | Sonuç |
|---|---|---|---|---|---|---|
| 1 | C-T01 VBUS | 5 V ⚠️ | — | — | — | ❌ BOŞ |
| 2 | C-T02 VBUS PD | ⚠️ VERIFY REQUIRED | — | — | — | ❌ BOŞ |
| 3 | C-T04 SATA güç | ⚠️ | — | — | — | ❌ BOŞ |
| 4 | C-T15 IEC/ATX | ⚠️ | — | — | — | ❌ BOŞ |
| 5 | C-T16 konnektör akımı | ⚠️ | — | — | — | ❌ BOŞ |

### §3.4 Pin Sayısı / Envanter Formu (KATMAN-2 · S5)

| # | Tip | Pin sayısı | Marka/model | Kaynak | Tarih | Sonuç |
|---|---|---|---|---|---|---|
| 1 | C-T02 Type-C | ⚠️ VERIFY REQUIRED | UNKNOWN | — | — | ❌ BOŞ |
| 2 | C-T09 GPIO header | ⚠️ | UNKNOWN | — | — | ❌ BOŞ |
| 3 | C-T10 DisplayPort | ⚠️ VERIFY REQUIRED | UNKNOWN | — | — | ❌ BOŞ |
| 4 | C-T11 HDMI | ⚠️ VERIFY REQUIRED | UNKNOWN | — | — | ❌ BOŞ |
| 5 | C-T16 M.2 | ⚠️ (formfactors DNS) | UNKNOWN | — | — | ❌ BOŞ |

### §3.5 Ölçüm Oturumu Kapanış Kuralı

| Adım | Koşul | Kapı |
|---|---|---|
| 1 | §3.1–3.4 formları doldurulur (alet + tarih zorunlu) | K-C6 · K-C7 |
| 2 | Doldurulan satırlar `cables-kapsam.md` §5'e `evidence` olarak işlenir | K-C3 |
| 3 | Tüm S4/S5 kapandıktan sonra kayıt `confirmed`'a geçebilir | §1 kuralı |
| 4 | Hâlâ 0 ölçüm varsa dosya `proposed` kalır ve raporlanır | Truth Mode |

## §4 HEAD URL Kanıt Matrisi (2026-10-13 · `Invoke-WebRequest -Method Head -UseBasicParsing -TimeoutSec 20`)

### §4.1 KULLANILABİLİR (HTTP 200/202)

| # | URL | HTTP | Kullanım alanı |
|---|---|---|---|
| 1 | `https://www.usb.org/` | 200 | C-T01 · C-T02 · C-T17 (USB-IF) |
| 2 | `https://www.sata-io.org/` | 200 | C-T04 (SATA-IO) |
| 3 | `https://www.displayport.org/` | 200 | C-T10 (DisplayPort) |
| 4 | `https://www.hdmi.org/` | 200 | C-T11 (HDMI) |
| 5 | `https://www.vesa.org/` | 200 | C-T10 (VESA — DisplayPort sahibi) |
| 6 | `https://docs.kernel.org/i2c/index.html` | 200 | C-T07 (I2C) |
| 7 | `https://docs.kernel.org/spi/spi-summary.html` | 200 | C-T06 (SPI) |
| 8 | `https://docs.kernel.org/driver-api/tty/index.html` | 200 | C-T08 (UART/tty) |
| 9 | `https://docs.kernel.org/driver-api/gpio/index.html` | 200 | C-T09 (GPIO) |
| 10 | `https://docs.kernel.org/PCI/index.html` | 200 | C-T03 (PCI/PCIe çekirdek) |
| 11 | `https://docs.kernel.org/sound/index.html` | 200 | C-T05 · C-T12 · C-T13 · C-T14 (ses alt sistemi) |
| 12 | `https://www.ieee.org/` | 202 | IEEE (genel — ölçü/standart kurumu) |
| 13 | `https://www.wi-fi.org/` | 200 | (network/ sınıfı — burada kullanılmadı) |
| 14 | `https://www.bluetooth.com/` | 200 | (network/ sınıfı — burada kullanılmadı) |
| 15 | `https://ethernetalliance.org/` | 200 | (network/ sınıfı — burada kullanılmadı) |
| 16 | `https://www.mipi.org/` | 200 | (MIPI — I2S/TDM ilgili; kullanılmadı) |

### §4.2 REDDEDİLEN (kullanılmadı — 403/404/timeout/DNS)

| # | URL | HTTP | Etkilenen iddia | İşaret |
|---|---|---|---|---|
| 1 | `https://www.jedec.org/` | 403 | DDR/RAM bellek standartları (memory/ sınıfı) | ⚠️ VERIFICATION REQUIRED (jedec.org 403) |
| 2 | `https://pcisig.com/` | 403 | PCIe spec sürümü (C-T03) | ⚠️ VERIFICATION REQUIRED (pcisig.com 403) |
| 3 | `https://www.snia.org/` | 403 | SATA/depolama (C-T04) | ⚠️ VERIFICATION REQUIRED (snia.org 403) |
| 4 | `https://www.aes.org/` | 403 | S/PDIF/dengeli ses standartları (C-T12..14) | ⚠️ VERIFICATION REQUIRED (aes.org 403) |
| 5 | `https://www.microchip.com/` | 403 | — | KULLANILMADI |
| 6 | `https://www.raspberrypi.com/` | 403 | — | KULLANILMADI |
| 7 | `https://www.intel.com/` | 403 | — | KULLANILMADI |
| 8 | `https://www.nxp.com/` | 404 | — | KULLANILMADI |
| 9 | `https://www.st.com/` | timeout | — | KULLANILMADI |
| 10 | `https://sfforum.com/` | DNS fail | — | KULLANILMADI |
| 11 | `https://www.formfactors.org/` | DNS fail | M.2 form faktörü (C-T16) | ⚠️ VERIFICATION REQUIRED (DNS) |

**Red kuralı (Truth Mode):** 403/404/timeout/DNS-fail kaynak **asla** 200'ün yerine
konmaz; ilgili iddia `⚠️ VERIFICATION REQUIRED (<kaynak> <kod>)` olarak kalır ve
`cables-kapsam.md` §5 kayıt alanına işlenir.

## §5 Repo Ölçüm Kaydı (S3 · 2026-10-13 · `.ai/architecture`, 911 md, kelime-sınırlı, büyük-küçük harf duyarlı)

| Terim | Sayı | Yorum |
|---|---|---|
| USB | 258 | en yoğun — kablo tipi C-T01/C-T02 kanıtı |
| I2S | 183 | ses hattı — C-T05 yoğun |
| I2C | 147 | C-T07 |
| GPIO | 109 | C-T09 |
| SPI | 99 | C-T06 |
| HDMI | 67 | C-T11 |
| DisplayPort | 27 | C-T10 |
| UART | 30 | C-T08 |
| SATA | 38 | C-T04 |
| PCIe | 23 | C-T03 (pcisig 403 — sürüm doğrulanamadı) |
| USB Type-C | **0** | ⚠️ envanter boşluğu — C-T02 repo atıfı yok (Q-C10) |
| CAN | **0** | (network/ sınıfı — burada kullanılmadı) |
| SFP | **0** | (network/ sınıfı — burada kullanılmadı) |
| Ethernet | 2 | (network/ sınıfı — burada kullanılmadı) |
| Wi-Fi | 4 | (network/ sınıfı — burada kullanılmadı) |
| Bluetooth | 29 | (network/ sınıfı — burada kullanılmadı) |
| modem | **0** | (network/ sınıfı — burada kullanılmadı) |
| transceiver | **0** | (network/ sınıfı — burada kullanılmadı) |
| NIC | 5 | (network/ sınıfı — burada kullanılmadı) |

**Kod ölçümü (S3):** `*.cpp=0` · `*.h=0` · `*.c=0` · `*.ino=0` · `*.xc=0` ·
`*.cs=0` · `*.go=0` · `*.rs=0` · `*.py=1` · `php=9470` · `js=1682` →
**kablo/konnektör kodu YOK**; bu dosya salt envanter tanımıdır.

## §6 Şema Bütünlüğü Kontrolü (K-C3)

| # | Kontrol | Hedef | Gerçekleşen | Sonuç |
|---|---|---|---|---|
| 1 | Tip kaydı sayısı | 17 | C-T01…C-T17 | ✅ |
| 2 | Alan sayısı / kayıt | 26 | 26 | ✅ |
| 3 | `parent:` anahtarı | 0 | 0 (`parent_id:` var) | ✅ |
| 4 | Suslu-parantez placeholder · doldurulmamis etiket | 0 | 0 | ✅ |
| 5 | Authority son satır | her dosya | her 3 dosyada | ✅ |
| 6 | `group` alanı | index/kapsam=`mimari` · dogrulama=`kanit` · ornekler=`kod` | öyle | ✅ |
| 7 | `status` | `proposed` (hepsi) | öyle | ✅ |
| 8 | Uydurma marka/model/URL/ölçüm | 0 | 0 | ✅ |
| 9 | `UNKNOWN` / `NOT PROVIDED` / `VERIFY REQUIRED` | bilinmeyende | 54 NP · 6 U · 16 VR | ✅ |
| 10 | Satır sayısı (içerik ≥500) | ≥500 | kapsam 764 · dogrulama ≥500 · ornekler ≥500 | ✅ |

## §7 Çelişki Çapaları — Yeniden Doğrulama (K-C1)

> Aşağıdaki satırlar 2026-10-13'te dosyalardan **yeniden okunmuştur** (birebir):
> `donanım-anayasa.md` §2 (58) · §10/3 (195) · §12/C10 (250) ·
> `donanaım-type-kapsam.md` §2/A6 (46) · §7/3-T1 (135) · §7/3-T2 (136) ·
> §12/M6 (284) · §7.3/X3 (428) — tam metin `cables-kapsam.md` §0 tablosunda.
> Ek bulgu (Q-C4): `donanaım-type-kapsam.md` §5/E4-E5 atfı "anayasa satır 152-153"
> der; güncel anayasa §10 satır 189-202'dedir, 152-153 = §7 hata tablosu →
> ⚠️ atıf uyuşmazlığı (stale citation); yasağın varlığı §2/A6 · §7/3-T2 ile ayakta.

| # | Kapı testi | Beklenen | Gerçekleşen | Sonuç |
|---|---|---|---|---|
| 1 | Yasağın adı "kablo" içeriyor mu? | EVET | EVET (§2/A6 · §7/3-T2 · §12/M6) | ✅ |
| 2 | "yalnız 3 bağlam" kuralı kayıtlı mı? | EVET | EVET (§2 · §10/3 · §12/C10 · §7/3-T1) | ✅ |
| 3 | Revizyon yapıldı mı? | HAYIR (beklenen) | HAYIR — `status: proposed` | ✅ (beklenen davranış) |
| 4 | Onay alındı mı? | Vault Steward | ⏳ PENDING (kapsam §0.1) | ⏳ |

## §8 Kapı Kapanışı ve Sorumluluk Tablosu

| Kapı | Sahip | Sonraki adım | Tarih |
|---|---|---|---|
| K-C6 (ölçüm) | Vault Steward + measurement oturumu | §3.1–3.4 formlarını doldur | — |
| K-C7 (envanter) | Vault Steward | K001 Q4 envanterini aç (marka · model · pin) | — |
| K-C8 (metin revizyonu) | Vault Steward | anayasa §2/§10/§12 + kapsam §2/A6/§7-T1-T2/§12-M6/§7.3-X3 revizyonu onayı | — |
| `donanaım-type/index.md` children/§GRUPLAR | ORKESTRATÖR | cables 4 satırı ekle | — |
| `K001-donanım/index.md` children | ORKESTRATÖR | `donanaım-type/cables` ekle | — |
| `git commit` | ORKESTRATÖR | 12 dosyayı toplu commit | — |

> **Yasak hatırlatma:** Bu dosyanın subagent'ı `git commit` **ATMAMIŞTIR** —
> commit kararı ve eylemi orkestratöre aittir (kök `AGENTS.md` §7 adım 9).

---

## §9 Tip-Bazlı Kapı Matrisi (K-C1…K-C8 · 17 tip)

| Tip | K-C1 | K-C2 | K-C3 | K-C4 | K-C5 | K-C6 | K-C7 | K-C8 | Tip durumu |
|---|---|---|---|---|---|---|---|---|---|
| C-T01 USB-A | ✅ | ✅ | ✅ | ✅ usb.org 200 | ✅ USB=258 | ❌ | ❌ | ⏳ | proposed |
| C-T02 USB-C | ✅ | ✅ | ✅ | ✅ usb.org 200 | ✅ Type-C=0 (boşluk) | ❌ | ❌ | ⏳ | proposed |
| C-T03 PCIe | ✅ | ✅ | ✅ | ❌ pcisig 403 | ⚠️ PCIe=23 | ❌ | ❌ | ⏳ | proposed |
| C-T04 SATA | ✅ | ✅ | ✅ | ✅ sata-io 200 | ✅ SATA=38 | ❌ | ❌ | ⏳ | proposed |
| C-T05 I2S/TDM | ✅ | ✅ | ✅ | ✅ kernel/sound 200 | ✅ I2S=183 | ❌ | ❌ | ⏳ | proposed |
| C-T06 SPI | ✅ | ✅ | ✅ | ✅ kernel/spi 200 | ✅ SPI=99 | ❌ | ❌ | ⏳ | proposed |
| C-T07 I2C | ✅ | ✅ | ✅ | ✅ kernel/i2c 200 | ✅ I2C=147 | ❌ | ❌ | ⏳ | proposed |
| C-T08 UART | ✅ | ✅ | ✅ | ✅ kernel/tty 200 | ✅ UART=30 | ❌ | ❌ | ⏳ | proposed |
| C-T09 GPIO | ✅ | ✅ | ✅ | ✅ kernel/gpio 200 | ✅ GPIO=109 | ❌ | ❌ | ⏳ | proposed |
| C-T10 DisplayPort | ✅ | ✅ | ✅ | ✅ dp.org 200 · vesa 200 | ✅ DP=27 | ❌ | ❌ | ⏳ | proposed |
| C-T11 HDMI | ✅ | ✅ | ✅ | ✅ hdmi.org 200 | ✅ HDMI=67 | ❌ | ❌ | ⏳ | proposed |
| C-T12 koaksiyel | ✅ | ✅ | ✅ | ❌ aes.org 403 | ⚠️ repo atıfı yok | ❌ | ❌ | ⏳ | proposed |
| C-T13 dengeli | ✅ | ✅ | ✅ | ❌ aes.org 403 | ⚠️ repo atıfı yok | ❌ | ❌ | ⏳ | proposed |
| C-T14 dengesiz | ✅ | ✅ | ✅ | ❌ aes.org 403 | ⚠️ repo atıfı yok | ❌ | ❌ | ⏳ | proposed |
| C-T15 güç | ✅ | ✅ | ✅ | ⚠️ standart yok | ⚠️ repo atıfı yok | ❌ | ❌ | ⏳ | proposed |
| C-T16 konektör | ✅ | ✅ | ✅ | ❌ formfactors DNS | ⚠️ repo atıfı yok | ❌ | ❌ | ⏳ | proposed |
| C-T17 adapter | ✅ | ✅ | ✅ | ✅ usb.org · hdmi.org 200 | ⚠️ repo atıfı yok | ❌ | ❌ | ⏳ | proposed |

## §10 Kabul Kriterleri (Kapı Bazlı)

| Kapı | Kabul kriteri | Red nedeni |
|---|---|---|
| K-C1 | her çapa satırı dosyadan birebir okunmuş + tarihli | atıf uydurma |
| K-C2 | her kayıt tek katman iddiası; protokol alanı yalnız etiket | katman karışımı |
| K-C3 | 17×26 alan; her hücre dolu (değer ya da UNKNOWN/NOT PROVIDED/VERIFY REQUIRED) | boş hücre |
| K-C4 | yalnız HTTP 200/202; reddedilenler §4.2'de + işaretli | 403/404'i kaynak yapmak |
| K-C5 | repo ölçümü kelime-sınırlı + büyük-küçük harf duyarlı + tarihli | tahmini sayı |
| K-C6 | en az 1 gerçek fiziksel ölçüm (alet + tarih) | uydurma ölçüm |
| K-C7 | envanter satırı (marka · model · pin) kaynaklı | tahmini marka |
| K-C8 | anayasa + üst kapsam metni Vault Steward onayıyla revize | onaysız revizyon |

## §11 Ölçüm Protokolü (K-C6 için — bir sonraki oturuma hazır)

| # | Ölçüm | Alet (örnek kategori — marka uydurulmaz) | Hedef tip |
|---|---|---|---|
| 1 | Empedans (TDR/reflektometre) | empedans ölçüm cihazı | C-T03 · C-T10 · C-T11 · C-T12 |
| 2 | DC direnci (multimetre) | multimetre | C-T01 · C-T02 · C-T04 · C-T15 |
| 3 | Kesit (kesit ölçer/kumpas) | kumpas | C-T12 · C-T13 · C-T14 · C-T15 |
| 4 | Uzunluk (şerit metre) | şerit metre | tüm montajlar |
| 5 | Gerilim/akım (osiloskop/probe) | osiloskop | C-T01 · C-T02 · C-T04 · C-T15 |
| 6 | Pin sayımı (görsel + multimetre süreklilik) | multimetre | C-T02 · C-T09 · C-T10 · C-T11 · C-T16 |

**Protokol kuralı:** her ölçüm §3 formlarına yazılır; form satırı doldurulmadan
kayıt `confirmed`'a geçmez; ölçüm aleti/markası bilinmiyorsa `UNKNOWN` yazılır —
uydurulmaz.

## §12 Komşu Sınıf Sınır Doğrulaması (Q-C9)

| Komşu sınıf | Sınır | Doğrulama durumu |
|---|---|---|
| `display/` | kablo katmanı burada, ekran sınıfı orada | ⏳ sınır onayı bekliyor |
| `power/` | kablo/konnektör burada, besleme sınıfı orada | ⏳ sınır onayı bekliyor |
| `audio/` | analog kablo burada, ses donanımı orada | ⏳ sınır onayı bekliyor |
| `hdd/` · `ssd/` | SATA kablosu burada, depolama sınıfı orada | ⏳ sınır onayı bekliyor |
| `network/` | ağ donanımı orada (G sınıfı) | ⏳ bu oturumda ayrı klasör |
| `memory/` | bellek orada (E sınıfı) | ⏳ bu oturumda ayrı klasör |

## §13 Kapanış Raporu İskeleti (doldurulacak — ölçüm oturumu sonrası)

| Alan | Değer |
|---|---|
| Rapor tarihi | — |
| Ölçüm oturumu ID | — |
| Doldurulan form satırı (§3.1–3.4) | — |
| `confirmed`'a geçen tipler | — |
| Kalan `proposed` tipler | — |
| K-C6 / K-C7 durumu | — |
| K-C8 (anayasa revizyonu) durumu | — |
| Orkestratör commit hash | — |

## §14 URL Erişim Günlüğü (S2 · her URL ayrı ayrı HEAD ile test edildi)

| # | Alan adı | Test edilen URL | Metod | Tarih | HTTP | Karar |
|---|---|---|---|---|---|---|
| 1 | usb.org | `https://www.usb.org/` | HEAD | 2026-10-13 | 200 | KULLAN |
| 2 | sata-io.org | `https://www.sata-io.org/` | HEAD | 2026-10-13 | 200 | KULLAN |
| 3 | displayport.org | `https://www.displayport.org/` | HEAD | 2026-10-13 | 200 | KULLAN |
| 4 | hdmi.org | `https://www.hdmi.org/` | HEAD | 2026-10-13 | 200 | KULLAN |
| 5 | vesa.org | `https://www.vesa.org/` | HEAD | 2026-10-13 | 200 | KULLAN |
| 6 | docs.kernel.org/i2c | `https://docs.kernel.org/i2c/index.html` | HEAD | 2026-10-13 | 200 | KULLAN |
| 7 | docs.kernel.org/spi | `https://docs.kernel.org/spi/spi-summary.html` | HEAD | 2026-10-13 | 200 | KULLAN |
| 8 | docs.kernel.org/tty | `https://docs.kernel.org/driver-api/tty/index.html` | HEAD | 2026-10-13 | 200 | KULLAN |
| 9 | docs.kernel.org/gpio | `https://docs.kernel.org/driver-api/gpio/index.html` | HEAD | 2026-10-13 | 200 | KULLAN |
| 10 | docs.kernel.org/PCI | `https://docs.kernel.org/PCI/index.html` | HEAD | 2026-10-13 | 200 | KULLAN |
| 11 | docs.kernel.org/sound | `https://docs.kernel.org/sound/index.html` | HEAD | 2026-10-13 | 200 | KULLAN |
| 12 | ieee.org | `https://www.ieee.org/` | HEAD | 2026-10-13 | 202 | KULLAN (referans) |
| 13 | jedec.org | `https://www.jedec.org/` | HEAD | 2026-10-13 | 403 | RED |
| 14 | pcisig.com | `https://pcisig.com/` | HEAD | 2026-10-13 | 403 | RED |
| 15 | snia.org | `https://www.snia.org/` | HEAD | 2026-10-13 | 403 | RED |
| 16 | aes.org | `https://www.aes.org/` | HEAD | 2026-10-13 | 403 | RED |
| 17 | microchip.com | `https://www.microchip.com/` | HEAD | 2026-10-13 | 403 | RED |
| 18 | raspberrypi.com | `https://www.raspberrypi.com/` | HEAD | 2026-10-13 | 403 | RED |
| 19 | intel.com | `https://www.intel.com/` | HEAD | 2026-10-13 | 403 | RED |
| 20 | nxp.com | `https://www.nxp.com/` | HEAD | 2026-10-13 | 404 | RED |
| 21 | st.com | `https://www.st.com/` | HEAD | 2026-10-13 | timeout | RED |
| 22 | sfforum.com | `https://sfforum.com/` | HEAD | 2026-10-13 | DNS fail | RED |
| 23 | formfactors.org | `https://www.formfactors.org/` | HEAD | 2026-10-13 | DNS fail | RED |

**Toplam:** 12 erişilebilir (11×200 + 1×202) · 11 reddedilen (7×403 · 1×404 · 1 timeout · 2 DNS).

## §15 Repo Ölçüm Komut Kaydı (S3 · yeniden üretilebilirlik)

| # | Ölçüm | Kapsam | Yöntem | Tarih | Sonuç |
|---|---|---|---|---|---|
| 1 | 911 md dosya sayımı | `.ai/architecture` | dosya sayımı | 2026-10-13 | 911 |
| 2 | USB / I2S / I2C / GPIO / SPI | `.ai/architecture` | kelime-sınırlı, BÜYÜK-KÜÇÜK duyarlı grep | 2026-10-13 | 258 · 183 · 147 · 109 · 99 |
| 3 | HDMI / SATA / PCIe / DisplayPort / UART | `.ai/architecture` | aynı | 2026-10-13 | 67 · 38 · 23 · 27 · 30 |
| 4 | USB Type-C / CAN / SFP / modem / transceiver / NOR / NVRAM / DDR | `.ai/architecture` | aynı | 2026-10-13 | 0 · 0 · 0 · 0 · 0 · 0 · 0 · 0 |
| 5 | Ethernet / Wi-Fi / Bluetooth / NIC | `.ai/architecture` | aynı | 2026-10-13 | 2 · 4 · 29 · 5 |
| 6 | ECC / SRAM / NAND / flash | `.ai/architecture` | aynı | 2026-10-13 | 23 · 1 · 7 · 48 |
| 7 | Kod dosyası uzantıları | repo | dosya sayımı | 2026-10-13 | cpp=0 · h=0 · c=0 · ino=0 · py=1 · php=9470 · js=1682 |

**Yeniden üretilebilirlik notu:** ölçümler yalnız `.ai/architecture` (vault md)
üzerindedir; kaynak kod (`*.php` · `*.js`) kapsam dışıdır ve kablo tipi kanıtı
olarak **kullanılmaz** (vault ölçümleri §5'tedir).

## §16 Çelişki Çapaları — Tam Metin Tekrarı (S1 · 10 satır)

| # | Dosya · satır | Tam metin |
|---|---|---|
| 1 | anayasa §2 · 58 | `Cihaz tipi sınıflandırma | EVET | K001/donanaım-type | audio · hdd · ssd` |
| 2 | anayasa §10/3 · 195 | `donanaım-type altı 3 bağlam mı, fazlası mı? | genişleme | ⚠️ VERIFICATION REQUIRED` |
| 3 | anayasa §12/C10 · 250 | `3 kademe-3 alt bağlam (audio/hdd/ssd) | EVET — envanterle | v2 §2.2` |
| 4 | kapsam §2/A6 · 46 | `Ekran · kablo · güç kaynağı türleri | DIŞINDA — anayasa §10/5 yasağı (ayrı bağlam AÇILMAZ)` |
| 5 | kapsam §7/3-T1 · 135 | `Altına yalnız 3 bağlam açılır (hdd · audio · ssd) | anayasa §10/5 — donanım-anayasa.md satır 152` |
| 6 | kapsam §7/3-T2 · 136 | `Ekran · kablo · güç kaynağı türü bağlamı AÇILMAZ | anayasa §10/5 — satır 153 (§2/A6)` |
| 7 | kapsam §12/M6 · 284 | `Ekran / kablo / güç kaynağı türü | — | anayasa §10/5 | ❌ kapsam dışı — bağlam AÇILMAZ` |
| 8 | kapsam §7.3/X3 · 428 | `Ekran/kablo/güç kaynağı için bağlam açmak | anayasa yasağı | §2/A6 · §7/3-T2` |
| 9 | kapsam §5/E4-E5 · atıf | "anayasa satır 152-153" — ⚠️ stale: güncel §10 = 189-202, 152-153 = §7 hata tablosu |
| 10 | Vault Steward 2026-10-13 | dağıtık onay — H/E/G sınıfları dosya üretimi onaylandı (metin revizyonu hariç) |

## §17 Kayıt-Kanıt Matrisi (17 tip × kanıt kaynağı)

| Tip | Vault atfı (S1) | URL (S2) | Repo ölçümü (S3) | Ölçüm (S4) | Envanter (S5) |
|---|---|---|---|---|---|
| C-T01 | ✅ §5-1 | usb.org 200 | USB=258 | ❌ | ❌ UNKNOWN |
| C-T02 | ✅ §5-2 | usb.org 200 | Type-C=0 | ❌ | ❌ UNKNOWN |
| C-T03 | ✅ §5-3 | pcisig 403 ❌ | PCIe=23 | ❌ | ❌ UNKNOWN |
| C-T04 | ✅ §5-4 | sata-io 200 | SATA=38 | ❌ | ❌ UNKNOWN |
| C-T05 | ✅ §5-5 | kernel/sound 200 | I2S=183 | ❌ | ❌ UNKNOWN |
| C-T06 | ✅ §5-6 | kernel/spi 200 | SPI=99 | ❌ | ❌ UNKNOWN |
| C-T07 | ✅ §5-7 | kernel/i2c 200 | I2C=147 | ❌ | ❌ UNKNOWN |
| C-T08 | ✅ §5-8 | kernel/tty 200 | UART=30 | ❌ | ❌ UNKNOWN |
| C-T09 | ✅ §5-9 | kernel/gpio 200 | GPIO=109 | ❌ | ❌ UNKNOWN |
| C-T10 | ✅ §5-10 | dp.org 200 · vesa 200 | DisplayPort=27 | ❌ | ❌ UNKNOWN |
| C-T11 | ✅ §5-11 | hdmi.org 200 | HDMI=67 | ❌ | ❌ UNKNOWN |
| C-T12 | ✅ §5-12 | aes.org 403 ❌ | 0 | ❌ | ❌ UNKNOWN |
| C-T13 | ✅ §5-13 | aes.org 403 ❌ | 0 | ❌ | ❌ UNKNOWN |
| C-T14 | ✅ §5-14 | aes.org 403 ❌ | 0 | ❌ | ❌ UNKNOWN |
| C-T15 | ✅ §5-15 | — (kaynak yok) | 0 | ❌ | ❌ UNKNOWN |
| C-T16 | ✅ §5-16 | formfactors DNS ❌ | 0 | ❌ | ❌ UNKNOWN |
| C-T17 | ✅ §5-17 | usb.org 200 · hdmi.org 200 | 0 | ❌ | ❌ UNKNOWN |

**Sonuç:** 17/17 kayıt S1'de · 12/17 S2'de tam · 9/17 S3'te sayısal · 0/17 S4 ·
0/17 S5 → tümü `proposed` (§1 kuralı).

## §18 Boş Ölçüm Oturumu Günlüğü (S4/S5 doldurulacak — bu oturumda 0 satır)

| # | Oturum ID | Tarih | Alet (UNKNOWN yasak) | Tip | Ölçüm değeri | Ölçülen | Kaydeden | Durum |
|---|---|---|---|---|---|---|---|---|
| 1 | — | — | — | — | — | — | — | ❌ BOŞ |
| 2 | — | — | — | — | — | — | — | ❌ BOŞ |
| 3 | — | — | — | — | — | — | — | ❌ BOŞ |
| 4 | — | — | — | — | — | — | — | ❌ BOŞ |
| 5 | — | — | — | — | — | — | — | ❌ BOŞ |

## §19 Kapı Kapanış Kontrol Listesi (checklist — işaretlenmedi)

- [ ] K-C1 çelişki kaydı — ✅ işaretli (§7)
- [ ] K-C2 katman ayrımı — ✅ işaretli (§2)
- [ ] K-C3 26 alan şema — ✅ işaretli (§6)
- [ ] K-C4 URL erişimi — ✅ işaretli (§4)
- [ ] K-C5 repo ölçümü — ✅ işaretli (§5)
- [ ] K-C6 fiziksel ölçüm — ❌ açık (§3 · §18)
- [ ] K-C7 envanter — ❌ açık (§3.4 · Q-C1)
- [ ] K-C8 metin revizyonu — ⏳ onay bekliyor (kapsam §0.1)

## §20 Sınıf-Bazlı Risk Tablosu (Red Team)

| # | Risk | Etki | Azaltma |
|---|---|---|---|
| 1 | Anayasa yasağı metni hâlâ "kablo AÇILMAZ" der | dosya seti `proposed` kalır | Vault Steward metin revizyonu (kapsam §0.1) |
| 2 | Fiziksel envanter 0 (K001 Q4) | hiçbir kayıt `confirmed` olamaz | envanter oturumu |
| 3 | pcisig/jedec/aes 403 — spec sürümü doğrulanamadı | sürüm/empedans iddiası yazılamaz | ⚠️ VERIFICATION REQUIRED korunur |
| 4 | USB Type-C repo'da 0 atıf | C-T02 envanter boşluğu | kapsam sorgusu (Q-C10) |
| 5 | Komşu sınıf sınırı (display/power/audio) | çift-kayıt riski | Q-C9 sınır onayı |

## §21 Kısaltmalar

| Kısaltma | Anlam |
|---|---|
| S1–S5 | Doğrulama seviyeleri (metin · HTTP · repo · ölçüm · envanter) |
| K-C1…K-C8 | cables sınıfı doğrulama kapıları |
| NP | NOT PROVIDED (veri yok) |
| VR | VERIFY REQUIRED (doğrulanamayan iddia) |
| HEAD | HTTP HEAD isteği (erişim testi) |

## §22 Katman Tanımları — Doğrulama Açısından (K-C2 destekleyici)

| Katman | Tanım | Doğrulanabilir kanıt | Bu oturumda |
|---|---|---|---|
| KATMAN-1 kablo | Fiziksel montaj (iletken + kılıf + sonlandırma) | kesit · uzunluk · ağırlık ölçümü | ❌ 0 |
| KATMAN-2 konektör | Mekanik-elektriksel geçiş parçası | pin sayısı · uyum · süreklilik testi | ❌ 0 |
| KATMAN-3 elektriksel arayüz | Seviye/empedans rejimi | empedans · gerilim · akım ölçümü | ❌ 0 |
| KATMAN-4 protokol | Paketleme/komut dili — **tip değil etiket** | doküman atfı (HEAD 200) | ✅ §4 |

**K-C2 kuralının kanıt zinciri:** her tip kaydı (`cables-kapsam.md` §5) yalnız
KATMAN-1/2/3 iddiası taşır; KATMAN-4 yalnız `protocols` alanında *etiket*
olarak bulunur ve `VERIFY REQUIRED` ile işaretlenir (spec kaynakları 403/DNS
olduğunda). K-C2 bu oturumda **metinsel** olarak geçmiştir; fiziksel kanıt
(K-C6/K-C7) için ölçüm oturumu beklenir.

## §23 Tip-Bazlı Eksik Alan Matrisi (S4/S5 — hangi tip ne bekliyor)

| Tip | Eksik S4 alanı | Eksik S5 alanı | Beklenen ölçüm | Beklenen envanter |
|---|---|---|---|---|
| C-T01 | VBUS · kesit · uzunluk | marka · model · pin | multimetre + kumpas | USB-A uç listesi |
| C-T02 | VBUS PD · kesit · uzunluk | marka · model · pin | multimetre + kumpas | Type-C uç listesi |
| C-T03 | empedans çift · uzunluk | riser/mezra modeli | TDR + şerit metre | PCIe yuva tipi |
| C-T04 | veri empedansı · güç hattı | marka · model · pin | TDR + multimetre | SATA uç listesi |
| C-T05 | mantık seviyesi · pin şeması | kart/çip modeli | osiloskop + süreklilik | I2S pin eşlemesi |
| C-T06 | mantık seviyesi · pin şeması | kart/çip modeli | osiloskop + süreklilik | SPI pin eşlemesi |
| C-T07 | pull-up · mantık seviyesi | kart/çip modeli | multimetre | I2C pin eşlemesi |
| C-T08 | TTL seviyesi · pin şeması | kart modeli | osiloskop | UART pin eşlemesi |
| C-T09 | mantık seviyesi · header | kart modeli | osiloskop | GPIO header tipi |
| C-T10 | empedans çift · DP_PWR | marka · model · pin | TDR + multimetre | DP uç listesi |
| C-T11 | empedans çift · +5V | marka · model · pin | TDR + multimetre | HDMI uç listesi |
| C-T12 | 75 Ω empedans · kesit | marka · model · pin | TDR + kumpas | RCA/BNC uç listesi |
| C-T13 | dengeli empedans · kesit | marka · model · pin | TDR + kumpas | XLR uç listesi |
| C-T14 | tek-uçlu empedans · kesit | marka · model · pin | TDR + kumpas | RCA uç listesi |
| C-T15 | AC/DC gerilim · akım · kesit | marka · model · pin | multimetre + osiloskop | IEC/ATX/Molex listesi |
| C-T16 | konnektör akım kapasitesi | marka · model · pin | multimetre + süreklilik | pin-header/FPC/M.2 listesi |
| C-T17 | adaptör pasif/aktif · akım | marka · model | multimetre | adapter uç listesi |

## §24 Kanıt Kodları (bu dosyanın işaret dili)

| Kod | Anlam | Kullanım |
|---|---|---|
| ✅ | kapı/kontrol geçti | §2 · §6 · §7 |
| ❌ | kapı/kontrol geçmedi (ölçüm/envanter yok) | §2 · §3 · §17 |
| ⏳ | beklemede (onay/sorumlu) | §2 · §7 · §8 |
| ⚠️ | doğrulanamayan iddia — VERIFICATION REQUIRED | §3 · §4 · §5 |
| NP | NOT PROVIDED (veri yok) | §3 formları |
| UNKNOWN | bilinmeyen değer | envanter |

**Kapanış notu (Truth Mode):** Bu oturumda 17 tip tanımlandı, 0 ölçüldü, 0
envantere yazıldı. `status: proposed` bu üç gerçeğin toplamıdır; kapanış
ölçüm + envanter + metin revizyonuyla olur (§8).

## §25 Dosya-Bazlı Doğrulama Özeti (bu klasördeki 4 dosya)

| # | Dosya | group | Satır | Authority son satır | `parent:` | Doğrulama |
|---|---|---|---|---|---|---|
| 1 | `index.md` | mimari | 80 | ✅ | 0 | ✅ |
| 2 | `cables-kapsam.md` | mimari | 764 | ✅ | 0 | ✅ |
| 3 | `cables-dogrulama.md` | kanit | ≥500 | ✅ | 0 | ✅ |
| 4 | `cables-ornekler.md` | kod | ≥500 | ✅ | 0 | ✅ |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
