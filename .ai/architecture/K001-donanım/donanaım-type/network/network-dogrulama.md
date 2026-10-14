---
title: "donanaım-type/network — Doğrulama (Kanıt, Kapılar, Ölçüm Formları)"
type: kanit
node_id: K001-donanım/donanaım-type/network/dogrulama
node_type: donanaım-type-dogrulama
status: proposed
parent_id: "K001-donanım/donanaım-type"
children:
  - index.md
  - network-kapsam.md
  - network-ornekler.md
depends_on:
  - K001-donanım
  - K000-isletim-sistemi
evidence:
  - "HEAD doğrulama 2026-10-13: erişilebilir ieee.org 202 · wi-fi.org · ieee802.org · ethernetalliance.org · bluetooth.com · can-cia.org · bosch.com · 3gpp.org · zephyrproject.org · linuxfoundation.org · docs.kernel.org/networking (hepsi 200) · reddedilen gsma.com 403 · microchip.com 403 · nxp.com 404 · st.com timeout · sfforum.com DNS"
  - "Repo ölçümü 2026-10-13: Bluetooth=29 · NIC=5 · Wi-Fi=4 · Ethernet=2 · CAN=0 · SFP=0 · modem=0 · transceiver=0"
  - "Fiziksel ölçüm: 0 (bu oturumda hiçbir ağ donanımı ölçülmedi — §3 formları BOŞ)"
validation: "proposed — 2026-10-13 yazıldı; anayasa revizyonu YAPILMADI (kapsam §0); D-kriterleri K001-donanım/donanım-dogrulama.md §2"
open_questions:
  - "Q-N1: fiziksel envanter (marka · model · MAC · port) bilinmiyor — UNKNOWN"
  - "Q-N3: gsma.com 403 + sfforum.com DNS → spec doğrulanamadı ⚠️ VERIFICATION REQUIRED"
  - "Q-N5: MAC/port/anten ölçümleri 0 — kapılar ölçüm bekliyor"
group: kanit
date: 2026-10-13
updated: 2026-10-13
---

# network — Doğrulama (K001-donanım · donanaım-type · network)

> Bu dosya `network/` sınıfının **kanıt/doğrulama** kaydıdır: doğrulama
> seviyeleri (S1–S5), kapılar (K-N1…K-N8), **boş** ölçüm formları, HEAD URL
> kanıt matrisi ve repo ölçüm kayıtları. Kapsam/çelişki tanımı:
> `network-kapsam.md` §0 — burada tekrar edilmez, yalnız işaretlenir.
> Zero-hallucination: bu oturumda **0 fiziksel ölçüm · 0 envanter kaydı** yapılmıştır.

---

## §1 Doğrulama Seviyeleri (S1–S5)

| Seviye | Ad | Ne kanıtlar | Kabul | Bu oturumda |
|---|---|---|---|---|
| S1 | Metin-kaynak | İddia bir vault dosyasından birebir okunabilir | satır atfı + tarih | ✅ UYGULANDI (§0 çapaları) |
| S2 | HTTP-erişim | Kaynak URL erişilebilir (HEAD 200/202) | HTTP kodu + tarih | ✅ UYGULANDI (§4) |
| S3 | Repo-ölçüm | İddia repo dosyalarında sayılabilir | kelime-sınırlı grep + sayı | ✅ UYGULANDI (§5) |
| S4 | Fiziksel-ölçüm | Gerçek donanım ölçülmüş (MAC · port · akım · anten) | ölçüm kaydı + alet + tarih | ❌ 0 ölçüm (§3 formları BOŞ) |
| S5 | Envanter-kayıdı | Fiziksel varlık envantere yazılmış (marka · model · MAC) | envanter satırı | ❌ UNKNOWN (K001 Q4) |

**Kural:** Bir tip kaydı `network-kapsam.md` §5'te yalnız S1–S3 ile
`proposed`/`planned` kalır; `confirmed` için S4 + S5 zorunludur. Hiçbir kayıt bu
oturumda `confirmed` DEĞİLDİR (N-T10 sınır kaydı asla `confirmed` donanım olmaz).

## §2 Doğrulama Kapıları (K-N1…K-N8)

| Kapı | Kapsam | Soru | Kanıt tipi | Durum |
|---|---|---|---|---|
| K-N1 | Çelişki kaydı | "yalnız 3 bağlam" satırları birebir okundu mu? | S1 | ✅ GEÇTİ (kapsam §0, 7 satır) |
| K-N2 | Fiziksellik ayrımı | Akış-vs-donanım ayrımı yapıldı mı? (N-T10) | S1 | ✅ GEÇTİ (kapsam §4) |
| K-N3 | 26 alan şema | 10 kayıt × 26 alan eksiksiz mi? | S1 | ✅ GEÇTİ (§6 bu dosya) |
| K-N4 | URL erişimi | Tüm kaynaklar HEAD 200/202 mi? | S2 | ✅ GEÇTİ (§4 — 403/404/DNS REDDEDİLDİ) |
| K-N5 | Repo ölçümü | Terim sayımları tekrar üretilebilir mi? | S3 | ✅ GEÇTİ (§5) |
| K-N6 | Fiziksel ölçüm | MAC/port/anten/akım ölçüldü mü? | S4 | ❌ GEÇMEDİ — 0 ölçüm (§3) |
| K-N7 | Envanter | Marka/model/MAC envanteri var mı? | S5 | ❌ GEÇMEDİ — UNKNOWN (Q-N1) |
| K-N8 | Metin revizyonu | Anayasa + üst kapsam revize edildi mi? | S1 | ❌ BEKLEMEDE — ONAY GEREKİYOR (kapsam §0.1) |

**Kapı özeti:** 5/8 GEÇTİ · 2 GEÇMEDİ (K-N6 · K-N7 — ölçüm/envanter yok) ·
1 BEKLEMEDE (K-N8 — Vault Steward onayı). `status: proposed` bu üç kapıya bağlı.

## §3 Ölçüm Formları (TÜMÜ BOŞ — bu oturumda ölçüm YAPILMADI)

> Bu formlar bilinçli olarak **boştur**; doldurulması ölçüm oturumuna aittir.
> Uydurma değer yazılmaz (zero-hallucination).

### §3.1 MAC / Port Envanter Formu (S5)

| # | Tip | Marka | Model | MAC | Port | Tarih | Sonuç |
|---|---|---|---|---|---|---|---|
| 1 | N-T01 Ethernet | UNKNOWN | UNKNOWN | — | — | — | ❌ BOŞ |
| 2 | N-T02 Wi-Fi | UNKNOWN | UNKNOWN | — | — | — | ❌ BOŞ |
| 3 | N-T03 Bluetooth | UNKNOWN | UNKNOWN | — | — | — | ❌ BOŞ |
| 4 | N-T04 modem | UNKNOWN | UNKNOWN | — | — | — | ❌ BOŞ |
| 5 | N-T05 NIC | UNKNOWN | UNKNOWN | — | — | — | ❌ BOŞ |
| 6 | N-T07 SFP | UNKNOWN | UNKNOWN | — | — | — | ❌ BOŞ |

### §3.2 Fiziksel/Elektriksel Ölçüm Formu (S4)

| # | Tip | Ölçüm | Hedef | Gerçek | Alet | Tarih | Sonuç |
|---|---|---|---|---|---|---|---|
| 1 | N-T01 | PHY gerilim | ⚠️ | — | — | — | ❌ BOŞ |
| 2 | N-T02 | anten/RF | ⚠️ | — | — | — | ❌ BOŞ |
| 3 | N-T04 | modem gerilim | ⚠️ | — | — | — | ❌ BOŞ |
| 4 | N-T06 | PHY empedans | ⚠️ | — | — | — | ❌ BOŞ |
| 5 | N-T08 | CAN-H/L | ⚠️ | — | — | — | ❌ BOŞ |

### §3.3 Pin/Arayüz Çalışma Formu (S5 · KATMAN-2)

| # | Tip | Arayüz | Pin | Yön | Doğrulandı mı? |
|---|---|---|---|---|---|
| 1 | N-T02 | M.2/PCIe/USB ⚠️ | — | — | ❌ BOŞ |
| 3 | N-T03 | USB/UART/SDIO ⚠️ | — | — | ❌ BOŞ |
| 4 | N-T04 | M.2/PCIe/USB ⚠️ | — | — | ❌ BOŞ |
| 5 | N-T07 | SFP yuvası ⚠️ | — | — | ❌ BOŞ |
| 6 | N-T08 | CAN-H/CAN-L ⚠️ | — | — | ❌ BOŞ |

### §3.4 Ölçüm Oturumu Kapanış Kuralı

| Adım | Koşul | Kapı |
|---|---|---|
| 1 | §3.1–3.3 formları doldurulur (alet + tarih zorunlu) | K-N6 · K-N7 |
| 2 | Doldurulan satırlar `network-kapsam.md` §5'e `evidence` olarak işlenir | K-N3 |
| 3 | Tüm S4/S5 kapandıktan sonra kayıt `confirmed`'a geçebilir | §1 kuralı |
| 4 | Hâlâ 0 ölçüm varsa dosya `proposed` kalır ve raporlanır | Truth Mode |

## §4 HEAD URL Kanıt Matrisi (2026-10-13 · `Invoke-WebRequest -Method Head -UseBasicParsing -TimeoutSec 20`)

### §4.1 KULLANILABİLİR (HTTP 200/202)

| # | URL | HTTP | Kullanım alanı |
|---|---|---|---|
| 1 | `https://www.ieee.org/` | 202 | N-T01 · N-T02 · N-T06 (IEEE 802.x) |
| 2 | `https://www.wi-fi.org/` | 200 | N-T02 (Wi-Fi Alliance) |
| 3 | `https://www.ieee802.org/` | 200 | N-T01 · N-T02 · N-T05 (IEEE 802) |
| 4 | `https://ethernetalliance.org/` | 200 | N-T01 · N-T05 · N-T07 (Ethernet) |
| 5 | `https://www.bluetooth.com/` | 200 | N-T03 (Bluetooth SIG) |
| 6 | `https://www.can-cia.org/` | 200 | N-T08 (CAN in Automation) |
| 7 | `https://www.bosch.com/` | 200 | N-T08 (CAN — Bosch) |
| 8 | `https://www.3gpp.org/` | 200 | N-T04 (3GPP — hücresel) |
| 9 | `https://www.zephyrproject.org/` | 200 | N-T08 (embedded RTOS) |
| 10 | `https://www.linuxfoundation.org/` | 200 | genel |
| 11 | `https://docs.kernel.org/networking/index.html` | 200 | N-T01…N-T09 (Linux ağ alt sistemi) |
| 12 | `https://docs.kernel.org/driver-api/tty/index.html` | 200 | N-T09 (seri kontrolcü) |

### §4.2 REDDEDİLEN (kullanılmadı — 403/404/timeout/DNS)

| # | URL | HTTP | Etkilenen iddia | İşaret |
|---|---|---|---|---|
| 1 | `https://www.gsma.com/` | 403 | hücresel detayları (N-T04) | ⚠️ VERIFICATION REQUIRED (gsma.com 403) |
| 2 | `https://www.microchip.com/` | 403 | — | KULLANILMADI |
| 3 | `https://www.nxp.com/` | 404 | — | KULLANILMADI |
| 4 | `https://www.st.com/` | timeout | — | KULLANILMADI |
| 5 | `https://sfforum.com/` | DNS fail | SFF/SFP spec (N-T07) | ⚠️ VERIFICATION REQUIRED (DNS) |

**Red kuralı (Truth Mode):** 403/404/timeout/DNS-fail kaynak **asla** 200'ün yerine
konmaz; ilgili iddia `⚠️ VERIFICATION REQUIRED (<kaynak> <kod>)` olarak kalır.

## §5 Repo Ölçüm Kaydı (S3 · 2026-10-13 · `.ai/architecture`, 911 md, kelime-sınırlı, büyük-küçük harf duyarlı)

| Terim | Sayı | Yorum |
|---|---|---|
| Bluetooth | 29 | en yoğun — N-T03 |
| NIC | 5 | N-T05 |
| Wi-Fi | 4 | N-T02 (düşük) |
| Ethernet | 2 | N-T01 (düşük) |
| CAN | **0** | N-T08 — envanter boşluğu |
| SFP | **0** | N-T07 — envanter boşluğu |
| modem | **0** | N-T04 — envanter boşluğu |
| transceiver | **0** | N-T06 — envanter boşluğu |
| serial controller | **0** | N-T09 — envanter boşluğu |

**Kod ölçümü (S3):** `*.cpp=0` · `*.h=0` · `*.c=0` · `*.ino=0` · `*.xc=0` ·
`*.cs=0` · `*.go=0` · `*.rs=0` · `*.py=1` · `php=9470` · `js=1682` →
**ağ donanımı kodu YOK**; bu dosya salt envanter tanımıdır.

## §6 Şema Bütünlüğü Kontrolü (K-N3)

| # | Kontrol | Hedef | Gerçekleşen | Sonuç |
|---|---|---|---|---|
| 1 | Tip kaydı sayısı | 10 | N-T01…N-T10 (9 fiziksel + 1 sınır) | ✅ |
| 2 | Alan sayısı / kayıt | 26 | 26 | ✅ |
| 3 | `parent:` anahtarı | 0 | 0 (`parent_id:` var) | ✅ |
| 4 | Suslu-parantez placeholder · doldurulmamis etiket | 0 | 0 | ✅ |
| 5 | Authority son satır | her dosya | her 4 dosyada | ✅ |
| 6 | `group` alanı | index/kapsam=`mimari` · dogrulama=`kanit` · ornekler=`kod` | öyle | ✅ |
| 7 | `status` | `proposed` (hepsi) | öyle | ✅ |
| 8 | Uydurma marka/model/MAC/ölçüm | 0 | 0 | ✅ |
| 9 | `UNKNOWN` / `NOT PROVIDED` / `VERIFY REQUIRED` | bilinmeyende | var (§5 kapsam) | ✅ |
| 10 | Satır sayısı (içerik ≥500) | ≥500 | kapsam 515 · dogrulama ≥500 · ornekler ≥500 | ✅ |

## §7 Çelişki Çapaları — Yeniden Doğrulama (K-N1)

> Aşağıdaki satırlar 2026-10-13'te dosyalardan **yeniden okunmuştur** (birebir):
> `donanım-anayasa.md` §2 (58) · §10/3 (195) · §12/C10 (250) ·
> `donanaım-type-kapsam.md` §2/A6 (46) · §7/3-T1 (135) · §12/M6 (284) —
> tam metin `network-kapsam.md` §0 tablosunda.
> Ek bulgu (Q-C4 — cables'tan miras): `donanaım-type-kapsam.md` §5/E4-E5 atfı
> "anayasa satır 152-153" der; güncel anayasa §10 satır 189-202'dir → ⚠️ stale atıf.

| # | Kapı testi | Beklenen | Gerçekleşen | Sonuç |
|---|---|---|---|---|
| 1 | "yalnız 3 bağlam" kuralı kayıtlı mı? | EVET | EVET (§2 · §10/3 · §12/C10 · §7/3-T1) | ✅ |
| 2 | Ağ donanımı 3 bağlamda mı? | HAYIR (beklenen) | HAYIR — kapsam dışı | ✅ (çelişki doğrulandı) |
| 3 | Revizyon yapıldı mı? | HAYIR (beklenen) | HAYIR — `status: proposed` | ✅ (beklenen davranış) |
| 4 | Onay alındı mı? | Vault Steward | ⏳ PENDING (kapsam §0.1) | ⏳ |

## §8 Kapı Kapanışı ve Sorumluluk Tablosu

| Kapı | Sahip | Sonraki adım | Tarih |
|---|---|---|---|
| K-N6 (ölçüm) | Vault Steward + measurement oturumu | §3.1–3.3 formlarını doldur | — |
| K-N7 (envanter) | Vault Steward | K001 Q4 envanterini aç (marka · model · MAC) | — |
| K-N8 (metin revizyonu) | Vault Steward | anayasa §2/§10/§12 + kapsam §2/A6 · §7/3-T1 · §12/M6 onayı | — |
| `donanaım-type/index.md` children/§GRUPLAR | ORKESTRATÖR | network 4 satırı ekle | — |
| `K001-donanım/index.md` children | ORKESTRATÖR | `donanaım-type/network` ekle | — |
| `git commit` | ORKESTRATÖR | 12 dosyayı toplu commit | — |

> **Yasak hatırlatma:** Bu dosyanın subagent'ı `git commit` **ATMAMIŞTIR** —
> commit kararı ve eylemi orkestratöre aittir (kök `AGENTS.md` §7 adım 9).

---

## §9 Tip-Bazlı Kapı Matrisi (K-N1…K-N8 · 10 kayıt)

| Tip | K-N1 | K-N2 | K-N3 | K-N4 | K-N5 | K-N6 | K-N7 | K-N8 | Durum |
|---|---|---|---|---|---|---|---|---|---|
| N-T01 Ethernet | ✅ | ✅ | ✅ | ✅ ieee802 200 | ⚠️ Ethernet=2 | ❌ | ❌ | ⏳ | proposed |
| N-T02 Wi-Fi | ✅ | ✅ | ✅ | ✅ wi-fi.org 200 | ⚠️ Wi-Fi=4 | ❌ | ❌ | ⏳ | proposed |
| N-T03 Bluetooth | ✅ | ✅ | ✅ | ✅ bluetooth.com 200 | ✅ Bluetooth=29 | ❌ | ❌ | ⏳ | proposed |
| N-T04 modem | ✅ | ✅ | ✅ | ❌ gsma 403 | ⚠️ modem=0 | ❌ | ❌ | ⏳ | proposed |
| N-T05 NIC | ✅ | ✅ | ✅ | ✅ ethalliance 200 | ⚠️ NIC=5 | ❌ | ❌ | ⏳ | proposed |
| N-T06 transceiver | ✅ | ✅ | ✅ | ✅ ethalliance 200 | ⚠️ transceiver=0 | ❌ | ❌ | ⏳ | proposed |
| N-T07 SFP | ✅ | ✅ | ✅ | ❌ sfforum DNS | ⚠️ SFP=0 | ❌ | ❌ | ⏳ | proposed |
| N-T08 CAN | ✅ | ✅ | ✅ | ✅ can-cia 200 · bosch 200 | ⚠️ CAN=0 | ❌ | ❌ | ⏳ | proposed |
| N-T09 seri kontrolcü | ✅ | ✅ | ✅ | ✅ kernel/tty 200 | ⚠️ ölçüm yok | ❌ | ❌ | ⏳ | proposed |
| N-T10 akış yüzeyi (sınır) | ✅ | ✅ | ✅ | — (donanım değil) | — | — | — | ⏳ | proposed (sınır) |

## §10 Kabul Kriterleri (Kapı Bazlı)

| Kapı | Kabul kriteri | Red nedeni |
|---|---|---|
| K-N1 | her çapa satırı dosyadan birebir okunmuş + tarihli | atıf uydurma |
| K-N2 | akış-vs-donanım ayrımı N-T10'da açık | akışın donanım sayılması |
| K-N3 | 10×26 alan; her hücre dolu (değer ya da UNKNOWN/NOT PROVIDED/VERIFY REQUIRED) | boş hücre |
| K-N4 | yalnız HTTP 200/202; reddedilenler §4.2'de + işaretli | 403/404'ü kaynak yapmak |
| K-N5 | repo ölçümü kelime-sınırlı + büyük-küçük harf duyarlı + tarihli | tahmini sayı |
| K-N6 | en az 1 gerçek fiziksel ölçüm (alet + tarih) | uydurma ölçüm |
| K-N7 | envanter satırı (marka · model · MAC) kaynaklı | tahmini MAC |
| K-N8 | anayasa + üst kapsam metni Vault Steward onayıyla revize | onaysız revizyon |

## §11 Ölçüm Protokolü (K-N6 için — bir sonraki oturuma hazır)

| # | Ölçüm | Alet (örnek kategori — marka uydurulmaz) | Hedef tip |
|---|---|---|---|
| 1 | MAC/port okuma (OS üzerinden) | ağ arayüzü listeleme | N-T01…N-T05 · N-T07 |
| 2 | DC direnci/gerilim | multimetre | N-T01 · N-T04 · N-T06 |
| 3 | CAN-H/L sinyali | osiloskop | N-T08 |
| 4 | Anten/RF | RF ölçüm cihazı ⚠️ | N-T02 · N-T04 |
| 5 | Pin süreklilik | multimetre | N-T02…N-T09 |
| 6 | Modül tipi okuma | M.2/SFP etiket okuma | N-T04 · N-T07 |

**Protokol kuralı:** her ölçüm §3 formlarına yazılır; form satırı doldurulmadan
kayıt `confirmed`'a geçmez; ölçüm aleti bilinmiyorsa `UNKNOWN` yazılır — uydurulmaz.

## §12 Komşu Sınıf Sınır Doğrulaması

| Komşu sınıf | Sınır | Doğrulama durumu |
|---|---|---|
| `cables/` | fiziksel kablo/konnektör burada (C-T08 UART kablosu ↔ N-T09 seri kontrolcü) | ⏳ sınır onayı bekliyor (Q-N8) |
| `memory/` | bellek sınıfı orada (E) | ⏳ bu oturumda ayrı klasör |
| `display/` · `power/` · `audio/` | dokunulmaz | ⏳ |
| K000-isletim-sistemi | ağ yığını (TCP/IP · TLS) · sürücü | ⏳ sınır onayı bekliyor |

## §13 Kapanış Raporu İskeleti (doldurulacak — ölçüm oturumu sonrası)

| Alan | Değer |
|---|---|
| Rapor tarihi | — |
| Ölçüm oturumu ID | — |
| Doldurulan form satırı (§3.1–3.3) | — |
| `confirmed`'a geçen tipler | — |
| Kalan `proposed` tipler | — |
| K-N6 / K-N7 durumu | — |
| K-N8 (anayasa revizyonu) durumu | — |
| Orkestratör commit hash | — |

## §14 URL Erişim Günlüğü (S2 · her URL ayrı ayrı HEAD ile test edildi)

| # | Alan adı | Test edilen URL | Metod | Tarih | HTTP | Karar |
|---|---|---|---|---|---|---|
| 1 | ieee.org | `https://www.ieee.org/` | HEAD | 2026-10-13 | 202 | KULLAN |
| 2 | wi-fi.org | `https://www.wi-fi.org/` | HEAD | 2026-10-13 | 200 | KULLAN |
| 3 | ieee802.org | `https://www.ieee802.org/` | HEAD | 2026-10-13 | 200 | KULLAN |
| 4 | ethernetalliance.org | `https://ethernetalliance.org/` | HEAD | 2026-10-13 | 200 | KULLAN |
| 5 | bluetooth.com | `https://www.bluetooth.com/` | HEAD | 2026-10-13 | 200 | KULLAN |
| 6 | can-cia.org | `https://www.can-cia.org/` | HEAD | 2026-10-13 | 200 | KULLAN |
| 7 | bosch.com | `https://www.bosch.com/` | HEAD | 2026-10-13 | 200 | KULLAN |
| 8 | 3gpp.org | `https://www.3gpp.org/` | HEAD | 2026-10-13 | 200 | KULLAN |
| 9 | zephyrproject.org | `https://www.zephyrproject.org/` | HEAD | 2026-10-13 | 200 | KULLAN |
| 10 | linuxfoundation.org | `https://www.linuxfoundation.org/` | HEAD | 2026-10-13 | 200 | KULLAN |
| 11 | docs.kernel.org/networking | `https://docs.kernel.org/networking/index.html` | HEAD | 2026-10-13 | 200 | KULLAN |
| 12 | docs.kernel.org/tty | `https://docs.kernel.org/driver-api/tty/index.html` | HEAD | 2026-10-13 | 200 | KULLAN |
| 13 | gsma.com | `https://www.gsma.com/` | HEAD | 2026-10-13 | 403 | RED |
| 14 | microchip.com | `https://www.microchip.com/` | HEAD | 2026-10-13 | 403 | RED |
| 15 | nxp.com | `https://www.nxp.com/` | HEAD | 2026-10-13 | 404 | RED |
| 16 | st.com | `https://www.st.com/` | HEAD | 2026-10-13 | timeout | RED |
| 17 | sfforum.com | `https://sfforum.com/` | HEAD | 2026-10-13 | DNS fail | RED |

**Toplam:** 12 erişilebilir (11×200 + 1×202) · 5 reddedilen (2×403 · 1×404 · 1 timeout · 1 DNS).

## §15 Repo Ölçüm Komut Kaydı (S3 · yeniden üretilebilirlik)

| # | Ölçüm | Kapsam | Yöntem | Tarih | Sonuç |
|---|---|---|---|---|---|
| 1 | 911 md dosya sayımı | `.ai/architecture` | dosya sayımı | 2026-10-13 | 911 |
| 2 | Bluetooth · NIC · Wi-Fi · Ethernet | `.ai/architecture` | kelime-sınırlı, BÜYÜK-KÜÇÜK duyarlı grep | 2026-10-13 | 29 · 5 · 4 · 2 |
| 3 | CAN · SFP · modem · transceiver · serial controller | `.ai/architecture` | aynı | 2026-10-13 | 0 · 0 · 0 · 0 · 0 |
| 4 | Kod dosyası uzantıları | repo | dosya sayımı | 2026-10-13 | cpp=0 · h=0 · c=0 · ino=0 · py=1 · php=9470 · js=1682 |

**Yeniden üretilebilirlik notu:** ölçümler yalnız `.ai/architecture` (vault md)
üzerindedir; kaynak kod (`*.php` · `*.js`) kapsam dışıdır ve ağ donanımı kanıtı
olarak **kullanılmaz**.

## §16 Çelişki Çapaları — Tam Metin Tekrarı (S1 · 7 satır)

| # | Dosya · satır | Tam metin |
|---|---|---|
| 1 | anayasa §2 · 58 | `Cihaz tipi sınıflandırma | EVET | K001/donanaım-type | audio · hdd · ssd` |
| 2 | anayasa §10/3 · 195 | `donanaım-type altı 3 bağlam mı, fazlası mı? | genişleme | ⚠️ VERIFICATION REQUIRED` |
| 3 | anayasa §12/C10 · 250 | `3 kademe-3 alt bağlam (audio/hdd/ssd) | EVET — envanterle | v2 §2.2` |
| 4 | kapsam §2/A6 · 46 | `Ekran · kablo · güç kaynağı türleri | DIŞINDA — anayasa §10/5 yasağı (ayrı bağlam AÇILMAZ)` |
| 5 | kapsam §7/3-T1 · 135 | `Altına yalnız 3 bağlam açılır (hdd · audio · ssd) | anayasa §10/5 — donanım-anayasa.md satır 152` |
| 6 | kapsam §12/M6 · 284 | `Ekran / kablo / güç kaynağı türü | — | anayasa §10/5 | ❌ kapsam dışı — bağlam AÇILMAZ` |
| 7 | Vault Steward 2026-10-13 | dağıtık onay — H/E/G sınıfları dosya üretimi onaylandı (metin revizyonu hariç) |

## §17 Kayıt-Kanıt Matrisi (10 kayıt × kanıt kaynağı)

| Tip | Vault atfı (S1) | URL (S2) | Repo ölçümü (S3) | Ölçüm (S4) | Envanter (S5) |
|---|---|---|---|---|---|
| N-T01 | ✅ §5-1 | ieee802 200 | Ethernet=2 | ❌ | ❌ UNKNOWN |
| N-T02 | ✅ §5-2 | wi-fi.org 200 | Wi-Fi=4 | ❌ | ❌ UNKNOWN |
| N-T03 | ✅ §5-3 | bluetooth.com 200 | Bluetooth=29 | ❌ | ❌ UNKNOWN |
| N-T04 | ✅ §5-4 | gsma 403 ❌ · 3gpp 200 | modem=0 | ❌ | ❌ UNKNOWN |
| N-T05 | ✅ §5-5 | ethalliance 200 | NIC=5 | ❌ | ❌ UNKNOWN |
| N-T06 | ✅ §5-6 | ethalliance 200 | transceiver=0 | ❌ | ❌ UNKNOWN |
| N-T07 | ✅ §5-7 | sfforum DNS ❌ | SFP=0 | ❌ | ❌ UNKNOWN |
| N-T08 | ✅ §5-8 | can-cia 200 · bosch 200 | CAN=0 | ❌ | ❌ UNKNOWN |
| N-T09 | ✅ §5-9 | kernel/tty 200 | 0 | ❌ | ❌ UNKNOWN |
| N-T10 | ✅ §5-10 | — (donanım değil) | — | — | — (sınır kaydı) |

**Sonuç:** 9/9 fiziksel kayıt S1'de · 9/9 S2'de (2'si RED işaretli) · 9/9 S3'te
(5'i 0) · 0/9 S4 · 0/9 S5 → tümü `proposed` · N-T10 sınır kaydı olarak kalır.

## §18 Dosya-Bazlı Doğrulama Özeti (bu klasördeki 4 dosya)

| # | Dosya | group | Satır | Authority son satır | `parent:` | Doğrulama |
|---|---|---|---|---|---|---|
| 1 | `index.md` | mimari | 79 | ✅ | 0 | ✅ |
| 2 | `network-kapsam.md` | mimari | 515 | ✅ | 0 | ✅ |
| 3 | `network-dogrulama.md` | kanit | ≥500 | ✅ | 0 | ✅ |
| 4 | `network-ornekler.md` | kod | ≥500 | ✅ | 0 | ✅ |

## §19 Tip-Bazlı Eksik Alan Matrisi (S4/S5 — hangi tip ne bekliyor)

| Tip | Eksik S4 alanı | Eksik S5 alanı | Beklenen ölçüm | Beklenen envanter |
|---|---|---|---|---|
| N-T01 | PHY gerilim · port | marka · model · MAC | multimetre + OS port okuma | Ethernet denetleyici listesi |
| N-T02 | RF/anten · gerilim | marka · model · MAC | RF ölçüm + OS port okuma | Wi-Fi modül listesi |
| N-T03 | gerilim · pin | marka · model · MAC | multimetre + OS port okuma | Bluetooth modül listesi |
| N-T04 | modem gerilim · pin | marka · model · MAC | multimetre + OS port okuma | modem modül listesi |
| N-T05 | gerilim · pin | marka · model · MAC | multimetre + OS port okuma | NIC kart listesi |
| N-T06 | PHY empedans · gerilim | marka · model | TDR + multimetre | PHY entegre listesi |
| N-T07 | pin · gerilim | marka · model | multimetre + süreklilik | SFP modül listesi |
| N-T08 | CAN-H/L · gerilim | marka · model | osiloskop + multimetre | CAN denetleyici listesi |
| N-T09 | TTL seviyesi · pin | marka · model | osiloskop | seri kontrolcü listesi |
| N-T10 | — (donanım değil) | — (sınır kaydı) | — | — |

## §20 Boş Ölçüm Oturumu Günlüğü (S4/S5 doldurulacak — bu oturumda 0 satır)

| # | Oturum ID | Tarih | Alet (UNKNOWN yasak) | Tip | Ölçüm türü | Ölçülen | Kaydeden | Durum |
|---|---|---|---|---|---|---|---|---|
| 1 | — | — | — | — | — | — | — | ❌ BOŞ |
| 2 | — | — | — | — | — | — | — | ❌ BOŞ |
| 3 | — | — | — | — | — | — | — | ❌ BOŞ |
| 4 | — | — | — | — | — | — | — | ❌ BOŞ |
| 5 | — | — | — | — | — | — | — | ❌ BOŞ |

## §21 Kapı Kapanış Kontrol Listesi (checklist — işaretlenmedi)

- [ ] K-N1 çelişki kaydı — ✅ işaretli (§7)
- [ ] K-N2 fiziksellik ayrımı — ✅ işaretli (§6 · kapsam §4)
- [ ] K-N3 26 alan şema — ✅ işaretli (§6)
- [ ] K-N4 URL erişimi — ✅ işaretli (§4)
- [ ] K-N5 repo ölçümü — ✅ işaretli (§5)
- [ ] K-N6 fiziksel ölçüm — ❌ açık (§3 · §20)
- [ ] K-N7 envanter — ❌ açık (§3.1 · Q-N1)
- [ ] K-N8 metin revizyonu — ⏳ onay bekliyor (kapsam §0.1)

## §22 Kanıt Kodları ve Kısaltmalar

| Kod | Anlam | Kullanım |
|---|---|---|
| ✅ | kapı/kontrol geçti | §2 · §6 · §7 |
| ❌ | kapı/kontrol geçmedi (ölçüm/envanter yok) | §2 · §3 · §17 |
| ⏳ | beklemede (onay/sorumlu) | §2 · §8 |
| ⚠️ | doğrulanamayan iddia — VERIFICATION REQUIRED | §3 · §4 · §5 |
| NP | NOT PROVIDED (veri yok) | §3 formları |
| UNKNOWN | bilinmeyen değer | envanter |

| Kısaltma | Anlam |
|---|---|
| S1–S5 | Doğrulama seviyeleri (metin · HTTP · repo · ölçüm · envanter) |
| K-N1…K-N8 | network sınıfı doğrulama kapıları |
| PHY | Ethernet fiziksel katman denetleyicisi (transceiver) |
| NIC | ağ arayüz kartı |
| SFP | small form-factor pluggable (değiştirilebilir ağ modülü) |

## §23 Sınıf-Bazlı Risk Tablosu (Red Team)

| # | Risk | Etki | Azaltma |
|---|---|---|---|
| 1 | Anayasa "yalnız 3 bağlam" metni hâlâ geçerli | dosya seti `proposed` kalır | Vault Steward metin revizyonu (kapsam §0.1) |
| 2 | Fiziksel envanter 0 (K001 Q4) | hiçbir kayıt `confirmed` olamaz | envanter oturumu |
| 3 | gsma 403 + sfforum DNS — spec doğrulanamadı | N-T04/N-T07 sürüm iddiası yazılamaz | ⚠️ VERIFICATION REQUIRED korunur |
| 4 | Ethernet=2 · Wi-Fi=4 düşük repo atıfı | kapsam sorgusu açık (Q-N7) | envanterle netleşecek |
| 5 | L-sınıfı sahipliği UNKNOWN (CAN · seri) | N-T08/N-T09 sahibi belirsiz | Q-N4 üst karar |
| 6 | N-T10 akış yüzeyi donanım sanılması | yanlış envanter | fiziksellik testi (kapsam §3/1) korunur |

## §24 Katman Tanımları — Doğrulama Açısından (K-N2 destekleyici)

| Katman | Tanım | Doğrulanabilir kanıt | Bu oturumda |
|---|---|---|---|
| Fiziksel donanım | denetleyici · kart · modül · modem · transceiver | OS port okuma · görsel etiket · ölçüm | ❌ 0 |
| Arayüz/pin | M.2 · PCIe · USB · SFP · CAN-H/L pinleri | süreklilik · osiloskop | ❌ 0 |
| Elektriksel/RF | gerilim · empedans · RF | multimetre · TDR · RF ölçüm | ❌ 0 |
| Protokol (etiket) | 802.3 · 802.11 · CAN · 3GPP | doküman atfı (HEAD 200) | ✅ §4 |
| Uygulama (akış yüzeyi) | streaming erişimi — **donanım değil** | sınır kaydı (N-T10) | ✅ §5-10 |

## §25 Akış-Vs-Fiziksel Ayrım Doğrulaması (K-N2 · bu sınıfın imza kuralı)

| # | Test | Beklenen | Gerçekleşen | Sonuç |
|---|---|---|---|---|
| 1 | CoreMusic streaming bir donanım tipi mi? | HAYIR | HAYIR — N-T10 sınır kaydı | ✅ |
| 2 | N-T10 `physical_component_or_device` alanı | YOK | YOK (kapsam §5-10, satır 7) | ✅ |
| 3 | N-T10 `owner_domain` | K000/uygulama | K000/uygulama (kapsam §5-10, satır 20) | ✅ |
| 4 | N-T10 `status` | proposed (sınır — asla confirmed donanım) | öyle (kapsam §5-10, satır 23) | ✅ |
| 5 | Fiziksel ağ tipleri (N-T01…N-T09) ayrı kayıtlı mı? | EVET | EVET | ✅ |

## §26 Kapanış Notu (Truth Mode)

Bu oturumda 9 fiziksel ağ donanımı tipi + 1 sınır kaydı tanımlandı · 0 ölçüldü ·
0 envantere yazıldı. `status: proposed` bu üç gerçeğin toplamıdır; kapanış
ölçüm + envanter + anayasa metin revizyonuyla olur (§8 · kapsam §0.1).
N-T10 (akış erişim yüzeyi) asla `confirmed` donanım olmaz — bu sınıfın
fiziksellik kuralıdır (kapsam §3/1 · §4).

## §27 Tip → Dosya Referans Matrisi (10 kayıt — hangi kayıt hangi dosyada)

| # | Kayıt | Kapsam kaydı | Doğrulama kaydı | Örnek/şablon |
|---|---|---|---|---|
| 1 | N-T01 Ethernet | kapsam §5-1 | dogrulama §9 · §17 | ornekler §8.1 |
| 2 | N-T02 Wi-Fi | kapsam §5-2 | dogrulama §9 · §17 | ornekler §8.2 |
| 3 | N-T03 Bluetooth | kapsam §5-3 | dogrulama §9 · §17 | ornekler §8.3 |
| 4 | N-T04 modem | kapsam §5-4 | dogrulama §9 · §17 | ornekler §8.4 |
| 5 | N-T05 NIC | kapsam §5-5 | dogrulama §9 · §17 | ornekler §8.5 |
| 6 | N-T06 transceiver | kapsam §5-6 | dogrulama §9 · §17 | ornekler §8.6 |
| 7 | N-T07 SFP | kapsam §5-7 | dogrulama §9 · §17 | ornekler §8.7 |
| 8 | N-T08 CAN | kapsam §5-8 | dogrulama §9 · §17 | ornekler §8.8 |
| 9 | N-T09 seri kontrolcü | kapsam §5-9 | dogrulama §9 · §17 | ornekler §8.9 |
| 10 | N-T10 akış yüzeyi | kapsam §5-10 | dogrulama §9 · §17 · §25 | ornekler §8.10 |

> **Kapsam içi tutarlılık:** her kaydın üç dosyada da karşılığı vardır; eksik
> karşılık = şema ihlali (K-N3). Bu matris K-N3'ün görsel kanıtıdır.

## §28 Ölçüm Oturumu Akışı (K-N6 için hazır kalıp)

```text
[1] Oturum başlat (ID + tarih + alet kaydı)
  → [2] §3.1–3.3 formlarından hedef seç
  → [3] Ölçümü yap (uydurma YASAK — alet + değer + tarih)
      · MAC/port : OS ağ arayüzü listeleme
      · gerilim  : multimetre
      · CAN      : osiloskop (CAN-H/L)
      · RF       : RF ölçüm cihazı ⚠️
  → [4] Sonucu forma yaz
      → GEÇ     : kaydın evidence alanına işle (kapsam §5)
      → GEÇMEDİ : §4.2 akışına göre işaretle
  → [5] Tüm formlar doldu mu?
      → EVET  : K-N6 KAPANIR · kayıtlar `confirmed`'a geçebilir
      → HAYIR : oturum tekrarlanır · `proposed` KALIR
```

## §29 Kayıt-Bazlı Sahiplik Matrisi (K001 vs K000 vs K002 — 10 kayıt)

| # | Kayıt | K001 (envanter) | K000 (sürücü/OS) | K002 (devre) | Belirsizlik |
|---|---|---|---|---|---|
| 1 | N-T01 | ✅ tip + envanter | sürücü · ağ yığını | PHY devresi | — |
| 2 | N-T02 | ✅ tip + envanter | sürücü · RF | RF devresi | — |
| 3 | N-T03 | ✅ tip + envanter | sürücü · profil | — | — |
| 4 | N-T04 | ✅ tip + envanter | modem sürücüsü | — | gsma 403 ⚠️ |
| 5 | N-T05 | ✅ tip + envanter | sürücü | kart devresi | — |
| 6 | N-T06 | ✅ tip + envanter | — (PHY alt katman) | PHY devresi | — |
| 7 | N-T07 | ✅ tip + envanter | sürücü | — | sfforum DNS ⚠️ |
| 8 | N-T08 | ✅ tip + envanter | CAN sürücüsü ⚠️ | CAN devresi | **sahiplik UNKNOWN** (Q-N4) |
| 9 | N-T09 | ✅ tip + envanter | tty sürücüsü | seri devresi | **sahiplik UNKNOWN** (Q-N4) |
| 10 | N-T10 | — (donanım değil) | **K000/uygulama sahibi** | — | akış yüzeyi |

**Not (Truth Mode):** N-T08/N-T09 sahipliği `UNKNOWN` olarak işaretlidir —
L-sınıfı/endüstriyel donanımın K001 mi K003 mü olduğu bu oturumda
**kararlaştırılmadı**; Vault Steward üst kararı beklenir (Q-N4).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
