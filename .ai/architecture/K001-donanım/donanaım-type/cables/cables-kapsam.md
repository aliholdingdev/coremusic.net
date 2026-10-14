---
title: "donanaım-type/cables — Kablo, Konnektör ve Arayüz Kapsamı"
type: architecture
node_id: K001-donanım/donanaım-type/cables/kapsam
node_type: donanaım-type-kapsam
status: proposed
parent_id: "K001-donanım/donanaım-type"
children:
  - cables/index.md
  - cables-dogrulama.md
  - cables-ornekler.md
depends_on:
  - K001-donanım
  - K000-isletim-sistemi
evidence:
  - "Çelişki çapaları 2026-10-13 dosyadan birebir okundu: donanım-anayasa.md §2 (58) · §10/3 (195) · §12/C10 (250) · donanaım-type-kapsam.md §2/A6 (46) · §7/3-T1 (135) · §7/3-T2 (136) · §12/M6 (284) · §7.3/X3 (428)"
  - "Repo ölçümü 2026-10-13 (.ai/architecture, 911 md, kelime-sınırlı, büyük-küçük harf duyarlı): USB=258 · I2S=183 · I2C=147 · GPIO=109 · SPI=99 · HDMI=67 · DisplayPort=27 · UART=30 · SATA=38 · PCIe=23 · USB Type-C=0"
  - "HEAD 2026-10-13 erişilebilir (200): usb.org · sata-io.org · displayport.org · hdmi.org · docs.kernel.org (i2c · spi · tty · gpio · usb · PCI · sound) · ieee.org (202) · wi-fi.org · bluetooth.com · ethernetalliance.org · mipi.org"
  - "HEAD 2026-10-13 REDDEDİLEN ve kullanılmayanlar: jedec.org 403 · pcisig.com 403 · snia.org 403 · aes.org 403 · microchip.com 403 · raspberrypi.com 403 · intel.com 403 · nxp.com 404 · st.com timeout · sfforum.com DNS · formfactors.org DNS"
  - "Repo kod ölçümü 2026-10-13: *.cpp=0 · *.h=0 · *.c=0 · *.ino=0 · *.py=1 · php=9470 · js=1682 → kablo/konnektör kodu YOK"
validation: "proposed — 2026-10-13 yazıldı; anayasa ve üst kapsam metni revize EDİLMEDİ (§0 PROPOSED — APPROVAL REQUIRED); 0 fiziksel ölçüm; kapılar cables-dogrulama.md §3"
open_questions:
  - "Q-C1: fiziksel kablo/konnektör envanteri (marka · kesit · uzunluk · pin sayısı) bilinmiyor — UNKNOWN (K001 Q4)"
  - "Q-C2: anayasa §2/§10/§12 ve kapsam §2/A6 · §7/3-T1-T2 · §12/M6 · §7.3/X3 metin revizyonu onayı alınmadı — status: proposed kalır"
  - "Q-C3: pcisig.com (PCIe) ve jedec.org (RAM) 403 — spec sürümü doğrulanamadı ⚠️ VERIFICATION REQUIRED"
  - "Q-C4: donanaım-type-kapsam.md §5/E4-E5 atfı 'anayasa satır 152-153' der; güncel anayasa §10 satır 189-202'dedir (152-153 = §7 hata tablosu) — atıf uyuşmazlığı ⚠️"
group: mimari
date: 2026-10-13
updated: 2026-10-13
---

# cables — Kapsam (K001-donanım · donanaım-type · 6. alt bağlam)

> Bu dosya `cables/` klasörünün **kapsam** kaydıdır: sınır çelişkisi, 4 katmanlı
> model kuralı, 17 tip kaydı (26 alan şema), sınırlar, açık sorular, kaynaklar.
> Kanıt/doğrulama ayrı dosyadır: `cables-dogrulama.md` · örnekler: `cables-ornekler.md`.
> Yazım notu: `donanaım-type` yazımı v2 kaynağına sadıktır — **düzeltme yasak**.

---

## §0 SINIR ÇELİŞKİSİ KAYDI — PROPOSED — APPROVAL REQUIRED

Bu klasör, anayasanın açık yasağıyla **doğrudan çelişir**: yasağın metninde
**"kablo"** kelimesi kendisi geçer. Vault Steward 2026-10-13 tarihli dağıtık
onayı yalnızca **klasör açımını ve dosya üretimini** kapsar; anayasa/üst-kapsam
**metin revizyonu YAPILMAMIŞTIR**. Aşağıdaki satırlar dosyalardan 2026-10-13'te
birebir okunmuştur (uydurma yok):

| # | Kaynak dosya · bölüm · satır | Birebir metin | Çelişki yönü |
|---|---|---|---|
| 1 | `donanım-anayasa.md` §2 · satır 58 | `Cihaz tipi sınıflandırma | EVET | K001/donanaım-type | audio · hdd · ssd` | Sınıflandırma yalnız 3 bağlamı sayar — kablo yok |
| 2 | `donanım-anayasa.md` §10/3 · satır 195 | `donanaım-type altı 3 bağlam mı, fazlası mı? | genişleme | ⚠️ VERIFICATION REQUIRED` | Genişleme zaten açık soru olarak işaretli |
| 3 | `donanım-anayasa.md` §12/C10 · satır 250 | `3 kademe-3 alt bağlam (audio/hdd/ssd) | EVET — envanterle | v2 §2.2` | Değişmezlik sınıfı olarak 3 bağlamı kilitler |
| 4 | `donanaım-type-kapsam.md` §2/A6 · satır 46 | `Ekran · kablo · güç kaynağı türleri | **DIŞINDA** — anayasa §10/5 yasağı (ayrı bağlam AÇILMAZ)` | **Yasağın adında "kablo" geçer — en güçlü çelişki** |
| 5 | `donanaım-type-kapsam.md` §7/3-T1 · satır 135 | `Altına yalnız 3 bağlam açılır (hdd · audio · ssd) | anayasa §10/5 — donanım-anayasa.md satır 152` | 3 bağlam kuralı (eskimiş satır atfı — bkz. satır 9) |
| 6 | `donanaım-type-kapsam.md` §7/3-T2 · satır 136 | `Ekran · kablo · güç kaynağı türü bağlamı AÇILMAZ | anayasa §10/5 — satır 153 (§2/A6)` | Açık yasak |
| 7 | `donanaım-type-kapsam.md` §12/M6 · satır 284 | `Ekran / kablo / güç kaynağı türü | — | anayasa §10/5 | ❌ kapsam dışı — bağlam AÇILMAZ` | Matriste kapsam dışı işaretli |
| 8 | `donanaım-type-kapsam.md` §7.3/X3 · satır 428 | `Ekran/kablo/güç kaynağı için bağlam açmak | anayasa yasağı | §2/A6 · §7/3-T2` | Yasak-gaté ihlali listesinde |
| 9 | `donanaım-type-kapsam.md` §5/E4-E5 · atıf | Atıf "anayasa satır 152-153" der; güncel `donanım-anayasa.md` §10 satır 189-202'dedir, 152-153 satırları §7 hata tablosuna aittir | ⚠️ **Atıf uyuşmazlığı (stale citation)** — atıf sayılmaz, yasağın varlığı §2/A6 · §7/3-T2 ile ayakta |
| 10 | Vault Steward 2026-10-13 dağıtık onayı | K001-donanım tip genişlemesi: H (kablo) · E (bellek) · G (ağ) sınıfları için dosya üretimi onaylandı | Yeni ihtiyaç: envanterde kablo/ağ/bellek tipleri sınıfsız kalıyordu |

**Yorum (Red Team · Truth Mode):** Yasağı kaldırmak bizim yetkimizde değildir.
Bu dosya seti yalnızca **PROPOSED** kayıt üretir; kapanış iki koşula bağlıdır:
(1) `donanım-anayasa.md` §2 · §10/3 · §12/C10 revizyonu, (2)
`donanaım-type-kapsam.md` §2/A6 · §7/3-T1 · §7/3-T2 · §12/M6 · §7.3/X3 revizyonu.
Bu iki dosya bu oturumda **salt okunur** tutulmuştur.

### §0.1 Kapanış Adımları (PENDING — ORKESTRATÖR)

| # | Adım | Hedef dosya | Sorumlu | Durum |
|---|---|---|---|---|
| 1 | `children:` satırına `cables` ekle | `donanaım-type/index.md` | ORKESTRATÖR | ⏳ PENDING (subagent DOKUNMADI) |
| 2 | `§GRUPLAR` tablosuna cables 4 satırı ekle | `donanaım-type/index.md` | ORKESTRATÖR | ⏳ PENDING |
| 3 | `children:` satırına `donanaım-type/cables` ekle | `K001-donanım/index.md` | ORKESTRATÖR | ⏳ PENDING |
| 4 | §2/§10/§12 "yalnız 3 bağlam" → genişletilmiş bağlam listesi | `donanım-anayasa.md` | Vault Steward onayı | ⏳ ONAY BEKLİYOR |
| 5 | §2/A6 · §7/3-T1-T2 · §12/M6 · §7.3/X3 yasağı → güncelleme | `donanaım-type-kapsam.md` | Vault Steward onayı | ⏳ ONAY BEKLİYOR |
| 6 | §5/E4-E5 satır atfı düzeltmesi (152-153 → güncel §10) | `donanaım-type-kapsam.md` | Vault Steward onayı | ⏳ ONAY BEKLİYOR |
| 7 | `git commit` | — | ORKESTRATÖR | ⏳ PENDING (subagent ATILMADI) |

---

## §1 Tip Tanımları

**Kablo** = iki nokta arasında sinyal/gücü fiziksel olarak taşıyan montaj
(iletken + kılıf + sonlandırma). **Konnektör** = kablo ile cihazın/mezradan
geçiş yaptığı mekanik-elektriksel arayüz parçası. **Elektriksel arayüz** =
hat üzerindeki gerilim/seviye/empedans rejimi (diferansiyel mi, tek uçlu mu,
kaç volt). **Protokol** = hat üzerindeki paketleme/komut dili — **katman-4'tür ve
kablo tipi değildir**; protokol sahibi bileşen K000 (sürücü) / donanım-api
kapsamındadır, burada yalnız *etiketlenir*.

| Terim | Katman | K001'de mi? | Not |
|---|---|---|---|
| Fiziksel kablo (montaj) | KATMAN-1 | EVET — envanter | kesit/uzunluk UNKNOWN |
| Konnektör (uç/yuva) | KATMAN-2 | EVET — envanter | pin sayısı envanterle |
| Elektriksel arayüz (seviye/empedans) | KATMAN-3 | EVET — sınıf | ölçüm bu turda 0 |
| Protokol (USB 2.0 · PCIe · I2C · S/PDIF…) | KATMAN-4 | HAYIR — yalnız etiket | sahibi K000 / donanım-api |
| Sürücü/OS katmanı | — | HAYIR | `K000-isletim-sistemi` |
| Devre/PCB/BOM | — | HAYIR | `K002-electroncis` (ADR-063/064) |

## §2 Tipler (7 H-alt-sınıf · 17 tip)

| # | Alt sınıf | Kapsadığı tipler | Katman ağırlığı |
|---|---|---|---|
| H1 | `veri-baglanti` | C-T01 USB-A · C-T02 USB-C · C-T03 PCIe · C-T04 SATA | 1 + 2 |
| H2 | `kart-arasi-dijital` | C-T05 I2S/TDM · C-T06 SPI · C-T07 I2C · C-T08 UART · C-T09 GPIO | 1 + 3 |
| H3 | `ekran-kablo` | C-T10 DisplayPort · C-T11 HDMI | 1 + 2 |
| H4 | `analog-kesit` | C-T12 koaksiyel (S/PDIF coax) · C-T13 dengeli (XLR) · C-T14 dengesiz (RCA) | 1 + 3 |
| H5 | `guc-baglanti` | C-T15 güç konnektörleri (IEC C13/C14 · ATX · Molex · SATA-power · barrel) | 1 + 2 |
| H6 | `konnektor-sinifi` | C-T16 konektör sınıfları (pin-header · FPC/FFC · card-edge · M.2) | 2 |
| H7 | `donusturucu` | C-T17 adapter/dönüştürücü (katman-geçişli uç-uyumsuzluğu köprüler) | 1 + 2 |

> E (bellek) ve G (ağ) sınıfları bu dosyanın **kapsamı dışındadır** — ayrı
> klasörlerde (`memory/` · `network/`) üretilir. `display/` · `power/` klasörleri
> bu turda **dokunulmaz** (mevcut, başka ajanın).

## §3 Sınıflandırma Kriterleri

1. **Katman testi:** Bir kayıt yalnız bir katmanın iddiasını taşır; "USB-C kablosu
   USB 3.2 taşır" cümlesi KATMAN-1 + KATMAN-4 karışımıdır → kayıttaki `protocols`
   alanı yalnız *etiket* olarak taşınır, tip tanımı protokolle kurulmaz.
2. **Envanter testi:** Fiziksel varlık (kablo/konnektör) envanterde
   **UNKNOWN** ise tip yine de tanımlanır ama `confidence: low` + `open_questions`
   alanıyla işaretlenir; uydurma marka/ölçü yazılmaz.
3. **Sahiplik testi:** Sürücü/OS davranışı → K000 · devre/PCB/BOM → K002 ·
   sınıflandırma/envanter → K001 (bu dosya). L-sınıfı (endüstriyel/embedded)
   donanım sahipliği **UNKNOWN** + açık soru (Q-C8).
4. **Ölçüm testi:** Empedans/kesit/uzunluk/akım değerleri bu oturumda **0 ölçüm** —
   her ölçüm alanı `NOT PROVIDED` veya `VERIFY REQUIRED` taşır.
5. **Yasak testi:** Her kayıt §0'daki yasağı ihlal eden bir iddia içeremez:
   kayıt *envanter* olarak tanımlıdır, anayasa revizyonu iddiası taşımaz.

## §4 Dört Katmanlı Model — Katman Matrisi

> Kural: her satır bir katman-1/2/3/4 bileşenini ayrı etiketler. Protokol
> (KATMAN-4) hiçbir satırın *tip tanımı* değildir.

| Arayüz | KATMAN-1 kablo | KATMAN-2 konektör | KATMAN-3 elektriksel arayüz | KATMAN-4 protokol (etiket) |
|---|---|---|---|---|
| USB-A | data+kılıf montajı | USB-A erkek/kadın | tek uçlu 3.3 V mantık · GND · VBUS 5 V (tipik) | USB 2.0/3.x ⚠️ sürüm VERIFY REQUIRED |
| USB-C | Type-C montajı | Type-C 24-pin | VBUS 5-20 V (PD) ⚠️ ölçüm yok · CC pinleri | USB 2.0/3.x · Power Delivery ⚠️ |
| PCIe | riser/kart-içi montaj ⚠️ | PCIe x1…x16 yuva | **diferansiyel çift** ⚠️ empedans VERIFY REQUIRED | PCI Express ⚠️ pcisig.com 403 |
| SATA | veri + güç montajı | SATA 7+15-pin | diferansiyel ⚠️ VERIFY REQUIRED | AHCI/NVM ⚠️ (protokol K000) |
| I2S/TDM | düz kablo/FPC | pin-header | tek uçlu 3.3 V ⚠️ | I2S / TDM ⚠️ |
| SPI | düz kablo/FPC | pin-header | tek uçlu 3.3 V ⚠️ | SPI ⚠️ |
| I2C | düz kablo/FPC | pin-header | open-drain ⚠️ | I2C ⚠️ |
| UART | düz kablo | pin-header | TTL seviyesi ⚠️ | seri ⚠️ |
| GPIO | düz kablo | pin-header | tek uçlu ⚠️ | — (dijital I/O) |
| DisplayPort | DP montajı | DP 20-pin | **diferansiyel çift** ⚠️ | DisplayPort ⚠️ |
| HDMI | HDMI montajı | HDMI 19-pin | **diferansiyel çift (TMDS)** ⚠️ | HDMI ⚠️ |
| Analog koaksiyel | 75 Ω koaksiyel ⚠️ ölçüm yok | RCA / BNC | tek uçlu · koaksiyel ⚠️ | S/PDIF ⚠️ |
| Dengeli (XLR) | twist-pair + kalkan | XLR 3-pin | **diferansiyel (dengeli)** ⚠️ | analog ses (protokol yok) |
| Dengesiz (RCA) | koaksiyel tek uçlu | RCA 2-pin | tek uçlu ⚠️ | analog ses (protokol yok) |
| Güç (IEC C13/C14) | gücü taşıyan montaj | IEC 60320 ⚠️ | AC şebeke ⚠️ | — (protokol yok) |
| Güç (ATX/Molex/barrel) | DC güç montajı | ATX/Molex/barrel | DC ⚠️ | — (protokol yok) |

**Katman-karışımı yasağı örneği:** "USB-C kablosu USB 3.2 Gen 2 10 Gbps taşır" →
bu bir **tip tanımı değil**, KATMAN-1 montajının KATMAN-4 etiketidir. Doğru kayıt:
C-T02 (montaj, KATMAN-1/2) + protokol alanı `USB 2.0/3.x ⚠️ etiket`.

---

## §5 Tip Kayıtları — 26 Alanlı Şema

> Şema (26 alan): `type_id` · `name_tr` · `name_en` · `category` · `subcategory` ·
> `description` · `physical_component_or_device` · `primary_function` · `interfaces` ·
> `protocols` · `power_requirements` · `operating_environment` · `os_dependencies` ·
> `driver_requirements` · `firmware_dependency` · `hardware_abstraction_requirement` ·
> `audio_relevance` · `coremusic_relevance` · `parent_domain` · `owner_domain` ·
> `existing_document` · `evidence` · `status` · `confidence` · `open_questions` ·
> `proposed_path`. Bilinmeyen → `UNKNOWN` · veri yok → `NOT PROVIDED` ·
> doğrulanamayan → `VERIFY REQUIRED`. Marka/model/URL/ölçüm **uydurulmaz**.

### C-T01 · `veri-baglanti` · USB-A Kablo Montajı

| # | Alan | Değer | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `type_id` | C-T01 | bu dosya §2/H1 | planned |
| 2 | `name_tr` | USB-A kablo montajı | — | planned |
| 3 | `name_en` | USB-A cable assembly | — | planned |
| 4 | `category` | cables | Vault Steward 2026-10-13 | proposed |
| 5 | `subcategory` | veri-baglanti | bu dosya §2/H1 | planned |
| 6 | `description` | USB-A uçlu fiziksel data kablosu; katman-1/2. Protokol etiketleri katman-4. | usb.org 200 (HEAD 2026-10-13) | planned |
| 7 | `physical_component_or_device` | kablo montajı + USB-A konektörler | — | planned |
| 8 | `primary_function` | konak↔çevre birimi arası fiziksel taşıma | — | planned |
| 9 | `interfaces` | USB-A erkek/yuva | usb.org 200 | planned |
| 10 | `protocols` | USB 2.0/3.x — **yalnız etiket** (katman-4) | usb.org 200 | planned |
| 11 | `power_requirements` | VBUS tipik 5 V ⚠️ kesin değer VERIFY REQUIRED | NOT PROVIDED | ⚠️ |
| 12 | `operating_environment` | indoor masaüstü ⚠️ | NOT PROVIDED | ⚠️ |
| 13 | `os_dependencies` | K001'de değil — K000 | domain kuralı §6 | planned |
| 14 | `driver_requirements` | K001'de değil — K000 | domain kuralı §6 | planned |
| 15 | `firmware_dependency` | K001'de değil — K000 | domain kuralı §6 | planned |
| 16 | `hardware_abstraction_requirement` | tip/envanter kaydı — soyutlama K000 | bu dosya §6 | planned |
| 17 | `audio_relevance` | USB ses arabirimi için taşıyıcı | docs.kernel.org/sound 200 | planned |
| 18 | `coremusic_relevance` | envanter tipi (6. alt bağlam) | — | planned |
| 19 | `parent_domain` | K001-donanım | — | planned |
| 20 | `owner_domain` | K001 (envanter) · sürücü K000 | domain kuralı §6 | planned |
| 21 | `existing_document` | `donanaım-type-kapsam.md` §2/A6 (yasağın adı) | satır 46 | planned |
| 22 | `evidence` | repo: USB=258 · usb.org 200 | ölçüm 2026-10-13 | planned |
| 23 | `status` | proposed | — | proposed |
| 24 | `confidence` | medium | repo yoğunluğu yüksek | planned |
| 25 | `open_questions` | Q-C1 envanter · Q-C3 spec sürümü | — | planned |
| 26 | `proposed_path` | `donanaım-type/cables/` (bu klasör) | — | planned |

### C-T02 · `veri-baglanti` · USB Type-C Kablo Montajı

| # | Alan | Değer | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `type_id` | C-T02 | bu dosya §2/H1 | planned |
| 2 | `name_tr` | USB Type-C kablo montajı | — | planned |
| 3 | `name_en` | USB Type-C cable assembly | — | planned |
| 4 | `category` | cables | Vault Steward 2026-10-13 | proposed |
| 5 | `subcategory` | veri-baglanti | bu dosya §2/H1 | planned |
| 6 | `description` | Type-C uçlu montaj; 24-pin, yönlenebilir. Katman-1/2. | usb.org 200 | planned |
| 7 | `physical_component_or_device` | kablo montajı + Type-C konektörler | — | planned |
| 8 | `primary_function` | konak↔çevre fiziksel taşıma + güç | — | planned |
| 9 | `interfaces` | USB Type-C | usb.org 200 | planned |
| 10 | `protocols` | USB 2.0/3.x · Power Delivery — **etiket** (katman-4) | usb.org 200 | planned |
| 11 | `power_requirements` | VBUS PD ⚠️ kesin değer VERIFY REQUIRED | NOT PROVIDED | ⚠️ |
| 12 | `operating_environment` | indoor ⚠️ | NOT PROVIDED | ⚠️ |
| 13 | `os_dependencies` | K000 | domain kuralı §6 | planned |
| 14 | `driver_requirements` | K000 | domain kuralı §6 | planned |
| 15 | `firmware_dependency` | K000 (PD/alt-yonga) ⚠️ | domain kuralı §6 | planned |
| 16 | `hardware_abstraction_requirement` | envanter — soyutlama K000 | bu dosya §6 | planned |
| 17 | `audio_relevance` | USB ses taşıyıcısı | docs.kernel.org/sound 200 | planned |
| 18 | `coremusic_relevance` | envanter tipi | — | planned |
| 19 | `parent_domain` | K001-donanım | — | planned |
| 20 | `owner_domain` | K001 (envanter) · sürücü K000 | domain kuralı §6 | planned |
| 21 | `existing_document` | `donanaım-type-kapsam.md` §2/A6 | satır 46 | planned |
| 22 | `evidence` | repo: USB Type-C=0 ⚠️ envanter boşluğu · usb.org 200 | ölçüm 2026-10-13 | planned |
| 23 | `status` | proposed | — | proposed |
| 24 | `confidence` | low | repo'da 0 atıf | planned |
| 25 | `open_questions` | Q-C1 · Q-C3 · repo'da Type-C 0 — kapsamda mı? | — | planned |
| 26 | `proposed_path` | `donanaım-type/cables/` | — | planned |

### C-T03 · `veri-baglanti` · PCIe Montaj / Riser

| # | Alan | Değer | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `type_id` | C-T03 | bu dosya §2/H1 | planned |
| 2 | `name_tr` | PCIe fiziksel montaj / riser | — | planned |
| 3 | `name_en` | PCIe physical assembly / riser | — | planned |
| 4 | `category` | cables | Vault Steward 2026-10-13 | proposed |
| 5 | `subcategory` | veri-baglanti | bu dosya §2/H1 | planned |
| 6 | `description` | PCIe yuva-kart bağlantısının fiziksel katmanı (riser/örtük yol). | docs.kernel.org/PCI 200 | planned |
| 7 | `physical_component_or_device` | riser kartı / kart-içi iz | — | planned |
| 8 | `primary_function` | mezra/kart arası diferansiyel taşıma | — | planned |
| 9 | `interfaces` | PCIe x1…x16 yuva | docs.kernel.org/PCI 200 | planned |
| 10 | `protocols` | PCI Express — **etiket** (katman-4) | pcisig.com 403 → ⚠️ | ⚠️ |
| 11 | `power_requirements` | yuva kart besleme ⚠️ | NOT PROVIDED | ⚠️ |
| 12 | `operating_environment` | kart-içi/kasa içi ⚠️ | NOT PROVIDED | ⚠️ |
| 13 | `os_dependencies` | K000 | domain kuralı §6 | planned |
| 14 | `driver_requirements` | K000 | domain kuralı §6 | planned |
| 15 | `firmware_dependency` | K000 (BIOS/enumerasyon) ⚠️ | domain kuralı §6 | planned |
| 16 | `hardware_abstraction_requirement` | envanter — soyutlama K000 | bu dosya §6 | planned |
| 17 | `audio_relevance` | PCIe ses kartları taşıyıcısı | docs.kernel.org/PCI 200 | planned |
| 18 | `coremusic_relevance` | envanter tipi | — | planned |
| 19 | `parent_domain` | K001-donanım | — | planned |
| 20 | `owner_domain` | K001 (envanter) · sürücü K000 | domain kuralı §6 | planned |
| 21 | `existing_document` | `donanaım-type-kapsam.md` §2/A6 | satır 46 | planned |
| 22 | `evidence` | repo: PCIe=23 · pcisig.com 403 | ölçüm 2026-10-13 | planned |
| 23 | `status` | proposed | — | proposed |
| 24 | `confidence` | low | pcisig 403 → spec doğrulanamadı | planned |
| 25 | `open_questions` | Q-C3 pcisig 403 — spec sürümü ⚠️ VERIFICATION REQUIRED | — | ⚠️ |
| 26 | `proposed_path` | `donanaım-type/cables/` | — | planned |

### C-T04 · `veri-baglanti` · SATA Veri + Güç Kablo

| # | Alan | Değer | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `type_id` | C-T04 | bu dosya §2/H1 | planned |
| 2 | `name_tr` | SATA veri ve güç kabloları | — | planned |
| 3 | `name_en` | SATA data and power cables | — | planned |
| 4 | `category` | cables | Vault Steward 2026-10-13 | proposed |
| 5 | `subcategory` | veri-baglanti | bu dosya §2/H1 | planned |
| 6 | `description` | SATA veri (7-pin) + güç (15-pin) fiziksel montajları. | sata-io.org 200 | planned |
| 7 | `physical_component_or_device` | kablo montajları + SATA konektörler | — | planned |
| 8 | `primary_function` | depolama birimi↔mezra taşıma + güç | — | planned |
| 9 | `interfaces` | SATA 7-pin data · 15-pin power | sata-io.org 200 | planned |
| 10 | `protocols` | AHCI/NVM — **etiket** (katman-4) | sata-io.org 200 | planned |
| 11 | `power_requirements` | SATA güç hattı ⚠️ kesin değer VERIFY REQUIRED | NOT PROVIDED | ⚠️ |
| 12 | `operating_environment` | kasa içi ⚠️ | NOT PROVIDED | ⚠️ |
| 13 | `os_dependencies` | K000 | domain kuralı §6 | planned |
| 14 | `driver_requirements` | K000 | domain kuralı §6 | planned |
| 15 | `firmware_dependency` | K000 ⚠️ | domain kuralı §6 | planned |
| 16 | `hardware_abstraction_requirement` | envanter — soyutlama K000 | bu dosya §6 | planned |
| 17 | `audio_relevance` | düşük (depolama taşıyıcısı) | — | planned |
| 18 | `coremusic_relevance` | hdd/ssd sınıflarının fiziksel bağlantısı | — | planned |
| 19 | `parent_domain` | K001-donanım | — | planned |
| 20 | `owner_domain` | K001 (envanter) · sürücü K000 | domain kuralı §6 | planned |
| 21 | `existing_document` | `hdd/index.md` · `ssd/index.md` (komşu sınıflar) | — | planned |
| 22 | `evidence` | repo: SATA=38 · sata-io.org 200 | ölçüm 2026-10-13 | planned |
| 23 | `status` | proposed | — | proposed |
| 24 | `confidence` | medium | sata-io 200 + repo 38 | planned |
| 25 | `open_questions` | Q-C1 envanter | — | planned |
| 26 | `proposed_path` | `donanaım-type/cables/` | — | planned |

### C-T05 · `kart-arasi-dijital` · I2S / TDM Düz Kablo

| # | Alan | Değer | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `type_id` | C-T05 | bu dosya §2/H2 | planned |
| 2 | `name_tr` | I2S / TDM düz kablo (kart-içi/kart-arası) | — | planned |
| 3 | `name_en` | I2S / TDM direct wiring | — | planned |
| 4 | `category` | cables | Vault Steward 2026-10-13 | proposed |
| 5 | `subcategory` | kart-arasi-dijital | bu dosya §2/H2 | planned |
| 6 | `description` | Ses veri hattının fiziksel taşıyıcısı (BCLK/LRCLK/DATA). Katman-1/3. | repo: I2S=183 | planned |
| 7 | `physical_component_or_device` | düz kablo/FPC/iz | — | planned |
| 8 | `primary_function` | çip↔çip ses veri taşıma | — | planned |
| 9 | `interfaces` | pin-header / FPC | NOT PROVIDED | ⚠️ |
| 10 | `protocols` | I2S · TDM — **etiket** (katman-4) | repo 183 | planned |
| 11 | `power_requirements` | mantık seviyesi ⚠️ VERIFY REQUIRED | NOT PROVIDED | ⚠️ |
| 12 | `operating_environment` | kart-içi ⚠️ | NOT PROVIDED | ⚠️ |
| 13 | `os_dependencies` | K000 (ses alt sistemi) | docs.kernel.org/sound 200 | planned |
| 14 | `driver_requirements` | K000 | domain kuralı §6 | planned |
| 15 | `firmware_dependency` | K001-dışında ⚠️ (ses motoru firmware'i ayrı) | — | ⚠️ |
| 16 | `hardware_abstraction_requirement` | envanter — soyutlama K000/ASIO | domain kuralı §6 | planned |
| 17 | `audio_relevance` | **YÜKSEK** — ses motorunun fiziksel hattı | repo 183 | planned |
| 18 | `coremusic_relevance` | Neva Engine DSP yolu ⚠️ (K002/K003 sınırı) | — | ⚠️ |
| 19 | `parent_domain` | K001-donanım | — | planned |
| 20 | `owner_domain` | K001 (envanter) · devre K002 | ADR-063/064 | planned |
| 21 | `existing_document` | `audio/*` (komşu sınıf) | — | planned |
| 22 | `evidence` | repo: I2S=183 (2. en yoğun terim) | ölçüm 2026-10-13 | planned |
| 23 | `status` | proposed | — | proposed |
| 24 | `confidence` | high | repo yoğunluğu 183 | planned |
| 25 | `open_questions` | Q-C1 pin/şema envanteri · Q-C8 K002 sınırı | — | planned |
| 26 | `proposed_path` | `donanaım-type/cables/` | — | planned |

### C-T06 · `kart-arasi-dijital` · SPI Düz Kablo

| # | Alan | Değer | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `type_id` | C-T06 | bu dosya §2/H2 | planned |
| 2 | `name_tr` | SPI düz kablo / kart-içi iz | — | planned |
| 3 | `name_en` | SPI direct wiring | — | planned |
| 4 | `category` | cables | Vault Steward 2026-10-13 | proposed |
| 5 | `subcategory` | kart-arasi-dijital | bu dosya §2/H2 | planned |
| 6 | `description` | SPI hattının fiziksel taşıyıcısı (SCLK/MOSI/MISO/CS). Katman-1/3. | docs.kernel.org/spi 200 | planned |
| 7 | `physical_component_or_device` | düz kablo/FPC/iz | — | planned |
| 8 | `primary_function` | çip↔çip seri taşıma | — | planned |
| 9 | `interfaces` | pin-header / FPC | NOT PROVIDED | ⚠️ |
| 10 | `protocols` | SPI — **etiket** (katman-4) | docs.kernel.org/spi 200 | planned |
| 11 | `power_requirements` | mantık seviyesi ⚠️ | NOT PROVIDED | ⚠️ |
| 12 | `operating_environment` | kart-içi ⚠️ | NOT PROVIDED | ⚠️ |
| 13 | `os_dependencies` | K000 | docs.kernel.org/spi 200 | planned |
| 14 | `driver_requirements` | K000 | domain kuralı §6 | planned |
| 15 | `firmware_dependency` | K001-dışında ⚠️ | — | ⚠️ |
| 16 | `hardware_abstraction_requirement` | envanter — soyutlama K000 | bu dosya §6 | planned |
| 17 | `audio_relevance` | DAC/codec register erişimi (dolaylı) | — | planned |
| 18 | `coremusic_relevance` | envanter tipi | — | planned |
| 19 | `parent_domain` | K001-donanım | — | planned |
| 20 | `owner_domain` | K001 (envanter) · devre K002 | ADR-063/064 | planned |
| 21 | `existing_document` | `donanaım-type-kapsam.md` §2/A6 | satır 46 | planned |
| 22 | `evidence` | repo: SPI=99 · docs.kernel.org/spi 200 | ölçüm 2026-10-13 | planned |
| 23 | `status` | proposed | — | proposed |
| 24 | `confidence` | medium | repo 99 + kernel doc 200 | planned |
| 25 | `open_questions` | Q-C1 pin envanteri | — | planned |
| 26 | `proposed_path` | `donanaım-type/cables/` | — | planned |

### C-T07 · `kart-arasi-dijital` · I2C Düz Kablo

| # | Alan | Değer | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `type_id` | C-T07 | bu dosya §2/H2 | planned |
| 2 | `name_tr` | I2C düz kablo / kart-içi iz | — | planned |
| 3 | `name_en` | I2C direct wiring | — | planned |
| 4 | `category` | cables | Vault Steward 2026-10-13 | proposed |
| 5 | `subcategory` | kart-arasi-dijital | bu dosya §2/H2 | planned |
| 6 | `description` | I2C hattının fiziksel taşıyıcısı (SDA/SCL + pull-up). Katman-1/3. | docs.kernel.org/i2c 200 | planned |
| 7 | `physical_component_or_device` | düz kablo/FPC/iz | — | planned |
| 8 | `primary_function` | çip↔çip kontrol taşıma | — | planned |
| 9 | `interfaces` | pin-header / FPC | NOT PROVIDED | ⚠️ |
| 10 | `protocols` | I2C — **etiket** (katman-4) | docs.kernel.org/i2c 200 | planned |
| 11 | `power_requirements` | open-drain + pull-up ⚠️ | NOT PROVIDED | ⚠️ |
| 12 | `operating_environment` | kart-içi ⚠️ | NOT PROVIDED | ⚠️ |
| 13 | `os_dependencies` | K000 | docs.kernel.org/i2c 200 | planned |
| 14 | `driver_requirements` | K000 | domain kuralı §6 | planned |
| 15 | `firmware_dependency` | K001-dışında ⚠️ | — | ⚠️ |
| 16 | `hardware_abstraction_requirement` | envanter — soyutlama K000 | bu dosya §6 | planned |
| 17 | `audio_relevance` | codec/register kontrol yolu | — | planned |
| 18 | `coremusic_relevance` | envanter tipi | — | planned |
| 19 | `parent_domain` | K001-donanım | — | planned |
| 20 | `owner_domain` | K001 (envanter) · devre K002 | ADR-063/064 | planned |
| 21 | `existing_document` | `donanaım-type-kapsam.md` §2/A6 | satır 46 | planned |
| 22 | `evidence` | repo: I2C=147 · docs.kernel.org/i2c 200 | ölçüm 2026-10-13 | planned |
| 23 | `status` | proposed | — | proposed |
| 24 | `confidence` | medium | repo 147 + kernel doc 200 | planned |
| 25 | `open_questions` | Q-C1 pin envanteri | — | planned |
| 26 | `proposed_path` | `donanaım-type/cables/` | — | planned |

### C-T08 · `kart-arasi-dijital` · UART Düz Kablo

| # | Alan | Değer | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `type_id` | C-T08 | bu dosya §2/H2 | planned |
| 2 | `name_tr` | UART düz kablo / pin-header | — | planned |
| 3 | `name_en` | UART direct wiring | — | planned |
| 4 | `category` | cables | Vault Steward 2026-10-13 | proposed |
| 5 | `subcategory` | kart-arasi-dijital | bu dosya §2/H2 | planned |
| 6 | `description` | UART hattının fiziksel taşıyıcısı (TX/RX/GND). Katman-1/3. | docs.kernel.org/tty 200 | planned |
| 7 | `physical_component_or_device` | düz kablo / pin-header | — | planned |
| 8 | `primary_function` | konsol/seri hattı fiziksel taşıma | — | planned |
| 9 | `interfaces` | pin-header | NOT PROVIDED | ⚠️ |
| 10 | `protocols` | seri (UART) — **etiket** (katman-4) | docs.kernel.org/tty 200 | planned |
| 11 | `power_requirements` | TTL seviyesi ⚠️ | NOT PROVIDED | ⚠️ |
| 12 | `operating_environment` | kart-içi/dış ⚠️ | NOT PROVIDED | ⚠️ |
| 13 | `os_dependencies` | K000 (tty alt sistemi) | docs.kernel.org/tty 200 | planned |
| 14 | `driver_requirements` | K000 | domain kuralı §6 | planned |
| 15 | `firmware_dependency` | K001-dışında ⚠️ | — | ⚠️ |
| 16 | `hardware_abstraction_requirement` | envanter — soyutlama K000 | bu dosya §6 | planned |
| 17 | `audio_relevance` | düşük (konsol hattı) | — | planned |
| 18 | `coremusic_relevance` | envanter tipi | — | planned |
| 19 | `parent_domain` | K001-donanım | — | planned |
| 20 | `owner_domain` | K001 (envanter) · sürücü K000 | domain kuralı §6 | planned |
| 21 | `existing_document` | `donanaım-type-kapsam.md` §2/A6 | satır 46 | planned |
| 22 | `evidence` | repo: UART=30 · docs.kernel.org/tty 200 | ölçüm 2026-10-13 | planned |
| 23 | `status` | proposed | — | proposed |
| 24 | `confidence` | medium | repo 30 + kernel doc 200 | planned |
| 25 | `open_questions` | Q-C1 pin envanteri | — | planned |
| 26 | `proposed_path` | `donanaım-type/cables/` | — | planned |

### C-T09 · `kart-arasi-dijital` · GPIO Pin-Header Kabloları

| # | Alan | Değer | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `type_id` | C-T09 | bu dosya §2/H2 | planned |
| 2 | `name_tr` | GPIO pin-header kabloları | — | planned |
| 3 | `name_en` | GPIO pin-header jumpers | — | planned |
| 4 | `category` | cables | Vault Steward 2026-10-13 | proposed |
| 5 | `subcategory` | kart-arasi-dijital | bu dosya §2/H2 | planned |
| 6 | `description` | GPIO uçlarının fiziksel jumper/kablo taşıyıcısı. Katman-1/2. | docs.kernel.org/gpio 200 | planned |
| 7 | `physical_component_or_device` | jumper kablo / duplo kablo | — | planned |
| 8 | `primary_function` | dijital I/O taşıma | — | planned |
| 9 | `interfaces` | pin-header | NOT PROVIDED | ⚠️ |
| 10 | `protocols` | yok (ham dijital I/O) | — | planned |
| 11 | `power_requirements` | mantık seviyesi ⚠️ | NOT PROVIDED | ⚠️ |
| 12 | `operating_environment` | prototip/kart-içi ⚠️ | NOT PROVIDED | ⚠️ |
| 13 | `os_dependencies` | K000 | docs.kernel.org/gpio 200 | planned |
| 14 | `driver_requirements` | K000 | domain kuralı §6 | planned |
| 15 | `firmware_dependency` | K001-dışında ⚠️ | — | ⚠️ |
| 16 | `hardware_abstraction_requirement` | envanter — soyutlama K000 | bu dosya §6 | planned |
| 17 | `audio_relevance` | düşük (kontrol/LED) | — | planned |
| 18 | `coremusic_relevance` | envanter tipi | — | planned |
| 19 | `parent_domain` | K001-donanım | — | planned |
| 20 | `owner_domain` | K001 (envanter) · devre K002 | ADR-063/064 | planned |
| 21 | `existing_document` | `donanaım-type-kapsam.md` §2/A6 | satır 46 | planned |
| 22 | `evidence` | repo: GPIO=109 · docs.kernel.org/gpio 200 | ölçüm 2026-10-13 | planned |
| 23 | `status` | proposed | — | proposed |
| 24 | `confidence` | medium | repo 109 + kernel doc 200 | planned |
| 25 | `open_questions` | Q-C1 pin envanteri | — | planned |
| 26 | `proposed_path` | `donanaım-type/cables/` | — | planned |

### C-T10 · `ekran-kablo` · DisplayPort Kablo

| # | Alan | Değer | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `type_id` | C-T10 | bu dosya §2/H3 | planned |
| 2 | `name_tr` | DisplayPort kablo montajı | — | planned |
| 3 | `name_en` | DisplayPort cable assembly | — | planned |
| 4 | `category` | cables | Vault Steward 2026-10-13 | proposed |
| 5 | `subcategory` | ekran-kablo | bu dosya §2/H3 | planned |
| 6 | `description` | DisplayPort uçlu fiziksel montaj. Katman-1/2. display/ sınıfıyla KOMŞU. | displayport.org 200 · vesa.org 200 | planned |
| 7 | `physical_component_or_device` | kablo montajı + DP konektör | — | planned |
| 8 | `primary_function` | ekran/görüntü fiziksel taşıma | — | planned |
| 9 | `interfaces` | DisplayPort 20-pin ⚠️ pin sayısı VERIFY REQUIRED | displayport.org 200 | ⚠️ |
| 10 | `protocols` | DisplayPort — **etiket** (katman-4) | displayport.org 200 | planned |
| 11 | `power_requirements` | AUX/DP_PWR ⚠️ | NOT PROVIDED | ⚠️ |
| 12 | `operating_environment` | masaüstü ⚠️ | NOT PROVIDED | ⚠️ |
| 13 | `os_dependencies` | K000 | domain kuralı §6 | planned |
| 14 | `driver_requirements` | K000 | domain kuralı §6 | planned |
| 15 | `firmware_dependency` | K000 ⚠️ | domain kuralı §6 | planned |
| 16 | `hardware_abstraction_requirement` | envanter — soyutlama K000 | bu dosya §6 | planned |
| 17 | `audio_relevance` | düşük (görüntü taşıyıcısı; ses dolaylı) | — | planned |
| 18 | `coremusic_relevance` | display/ sınıfının kablo katmanı | display/index.md | planned |
| 19 | `parent_domain` | K001-donanım | — | planned |
| 20 | `owner_domain` | K001 (envanter) · sürücü K000 | domain kuralı §6 | planned |
| 21 | `existing_document` | `display/*` (komşu sınıf — dokunulmaz) | — | planned |
| 22 | `evidence` | repo: DisplayPort=27 · displayport.org 200 | ölçüm 2026-10-13 | planned |
| 23 | `status` | proposed | — | proposed |
| 24 | `confidence` | medium | displayport.org 200 + repo 27 | planned |
| 25 | `open_questions` | Q-C1 envanter · display/ ile sınır (Q-C9) | — | planned |
| 26 | `proposed_path` | `donanaım-type/cables/` | — | planned |

### C-T11 · `ekran-kablo` · HDMI Kablo

| # | Alan | Değer | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `type_id` | C-T11 | bu dosya §2/H3 | planned |
| 2 | `name_tr` | HDMI kablo montajı | — | planned |
| 3 | `name_en` | HDMI cable assembly | — | planned |
| 4 | `category` | cables | Vault Steward 2026-10-13 | proposed |
| 5 | `subcategory` | ekran-kablo | bu dosya §2/H3 | planned |
| 6 | `description` | HDMI uçlu fiziksel montaj. Katman-1/2. display/ sınıfıyla KOMŞU. | hdmi.org 200 | planned |
| 7 | `physical_component_or_device` | kablo montajı + HDMI konektör | — | planned |
| 8 | `primary_function` | ekran/görüntü fiziksel taşıma | — | planned |
| 9 | `interfaces` | HDMI 19-pin ⚠️ pin sayısı VERIFY REQUIRED | hdmi.org 200 | ⚠️ |
| 10 | `protocols` | HDMI (TMDS) — **etiket** (katman-4) | hdmi.org 200 | planned |
| 11 | `power_requirements` | +5V/power ⚠️ | NOT PROVIDED | ⚠️ |
| 12 | `operating_environment` | masaüstü ⚠️ | NOT PROVIDED | ⚠️ |
| 13 | `os_dependencies` | K000 | domain kuralı §6 | planned |
| 14 | `driver_requirements` | K000 | domain kuralı §6 | planned |
| 15 | `firmware_dependency` | K000 (HDCP) ⚠️ | domain kuralı §6 | planned |
| 16 | `hardware_abstraction_requirement` | envanter — soyutlama K000 | bu dosya §6 | planned |
| 17 | `audio_relevance` | düşük (ses destekli görüntü hattı) | — | planned |
| 18 | `coremusic_relevance` | display/ sınıfının kablo katmanı | display/index.md | planned |
| 19 | `parent_domain` | K001-donanım | — | planned |
| 20 | `owner_domain` | K001 (envanter) · sürücü K000 | domain kuralı §6 | planned |
| 21 | `existing_document` | `display/*` (dokunulmaz) | — | planned |
| 22 | `evidence` | repo: HDMI=67 · hdmi.org 200 | ölçüm 2026-10-13 | planned |
| 23 | `status` | proposed | — | proposed |
| 24 | `confidence` | medium | hdmi.org 200 + repo 67 | planned |
| 25 | `open_questions` | Q-C1 envanter · display/ sınırı (Q-C9) | — | planned |
| 26 | `proposed_path` | `donanaım-type/cables/` | — | planned |

### C-T12 · `analog-kesit` · Analog Koaksiyel (S/PDIF Coax)

| # | Alan | Değer | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `type_id` | C-T12 | bu dosya §2/H4 | planned |
| 2 | `name_tr` | Analog koaksiyel ses kablosu (RCA/BNC) | — | planned |
| 3 | `name_en` | Analog coaxial audio cable | — | planned |
| 4 | `category` | cables | Vault Steward 2026-10-13 | proposed |
| 5 | `subcategory` | analog-kesit | bu dosya §2/H4 | planned |
| 6 | `description` | Koaksiyel kesitli tek-uçlu ses taşıyıcısı (dijital S/PDIF veya analog). Katman-1/3. | docs.kernel.org/sound 200 | planned |
| 7 | `physical_component_or_device` | koaksiyel kablo + RCA/BNC konektör | — | planned |
| 8 | `primary_function` | tek- uçlu ses sinyali taşıma | — | planned |
| 9 | `interfaces` | RCA · BNC ⚠️ | NOT PROVIDED | ⚠️ |
| 10 | `protocols` | S/PDIF (dijital varyant) — **etiket** | NOT PROVIDED | ⚠️ |
| 11 | `power_requirements` | pasif — yok | — | planned |
| 12 | `operating_environment` | stüdyo/ev ⚠️ | NOT PROVIDED | ⚠️ |
| 13 | `os_dependencies` | K000 (ses alt sistemi) | docs.kernel.org/sound 200 | planned |
| 14 | `driver_requirements` | K000 | domain kuralı §6 | planned |
| 15 | `firmware_dependency` | yok (pasif) | — | planned |
| 16 | `hardware_abstraction_requirement` | envanter — soyutlama K000/ASIO | domain kuralı §6 | planned |
| 17 | `audio_relevance` | **YÜKSEK** — ses zinciri fiziksel katmanı | — | planned |
| 18 | `coremusic_relevance` | envanter tipi (ses donanımı komşu sınıf) | — | planned |
| 19 | `parent_domain` | K001-donanım | — | planned |
| 20 | `owner_domain` | K001 (envanter) · devre K002 | ADR-063/064 | planned |
| 21 | `existing_document` | `audio/*` (komşu sınıf) | — | planned |
| 22 | `evidence` | aes.org 403 → standard doğrulanamadı ⚠️ | HEAD 2026-10-13 | planned |
| 23 | `status` | proposed | — | proposed |
| 24 | `confidence` | low | aes.org 403 · ölçüm yok | planned |
| 25 | `open_questions` | Q-C1 kesit/uzunluk · 75 Ω empedans VERIFY REQUIRED | — | ⚠️ |
| 26 | `proposed_path` | `donanaım-type/cables/` | — | planned |

### C-T13 · `analog-kesit` · Dengeli (Balanced) Ses Kablosu

| # | Alan | Değer | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `type_id` | C-T13 | bu dosya §2/H4 | planned |
| 2 | `name_tr` | Dengeli (balanced) ses kablosu (XLR) | — | planned |
| 3 | `name_en` | Balanced audio cable (XLR) | — | planned |
| 4 | `category` | cables | Vault Steward 2026-10-13 | proposed |
| 5 | `subcategory` | analog-kesit | bu dosya §2/H4 | planned |
| 6 | `description` | Diferansiyel (dengeli) twist-pair + kalkanlı ses taşıyıcısı. Katman-1/3. | docs.kernel.org/sound 200 | planned |
| 7 | `physical_component_or_device` | twist-pair kablo + XLR konektör | — | planned |
| 8 | `primary_function` | diferansiyel ses sinyali taşıma | — | planned |
| 9 | `interfaces` | XLR 3-pin ⚠️ | NOT PROVIDED | ⚠️ |
| 10 | `protocols` | yok (analog) | — | planned |
| 11 | `power_requirements` | pasif — yok | — | planned |
| 12 | `operating_environment` | stüdyo/ev ⚠️ | NOT PROVIDED | ⚠️ |
| 13 | `os_dependencies` | K000 | docs.kernel.org/sound 200 | planned |
| 14 | `driver_requirements` | K000 | domain kuralı §6 | planned |
| 15 | `firmware_dependency` | yok (pasif) | — | planned |
| 16 | `hardware_abstraction_requirement` | envanter — soyutlama K000/ASIO | domain kuralı §6 | planned |
| 17 | `audio_relevance` | **YÜKSEK** — dengeli ses zinciri | — | planned |
| 18 | `coremusic_relevance` | envanter tipi | — | planned |
| 19 | `parent_domain` | K001-donanım | — | planned |
| 20 | `owner_domain` | K001 (envanter) · devre K002 | ADR-063/064 | planned |
| 21 | `existing_document` | `audio/*` (komşu sınıf) | — | planned |
| 22 | `evidence` | aes.org 403 → standard doğrulanamadı ⚠️ | HEAD 2026-10-13 | planned |
| 23 | `status` | proposed | — | proposed |
| 24 | `confidence` | low | aes.org 403 · ölçüm yok | planned |
| 25 | `open_questions` | Q-C1 kesit/uzunluk · empedans VERIFY REQUIRED | — | ⚠️ |
| 26 | `proposed_path` | `donanaım-type/cables/` | — | planned |

### C-T14 · `analog-kesit` · Dengesiz (Unbalanced) Ses Kablosu

| # | Alan | Değer | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `type_id` | C-T14 | bu dosya §2/H4 | planned |
| 2 | `name_tr` | Dengesiz (unbalanced) ses kablosu (RCA) | — | planned |
| 3 | `name_en` | Unbalanced audio cable (RCA) | — | planned |
| 4 | `category` | cables | Vault Steward 2026-10-13 | proposed |
| 5 | `subcategory` | analog-kesit | bu dosya §2/H4 | planned |
| 6 | `description` | Tek- uçlu (dengesiz) ses taşıyıcısı. Katman-1/3. | docs.kernel.org/sound 200 | planned |
| 7 | `physical_component_or_device` | tek- iletken + kalkan kablo + RCA konektör | — | planned |
| 8 | `primary_function` | tek- uçlu ses sinyali taşıma | — | planned |
| 9 | `interfaces` | RCA 2-pin ⚠️ | NOT PROVIDED | ⚠️ |
| 10 | `protocols` | yok (analog) | — | planned |
| 11 | `power_requirements` | pasif — yok | — | planned |
| 12 | `operating_environment` | ev ⚠️ | NOT PROVIDED | ⚠️ |
| 13 | `os_dependencies` | K000 | docs.kernel.org/sound 200 | planned |
| 14 | `driver_requirements` | K000 | domain kuralı §6 | planned |
| 15 | `firmware_dependency` | yok (pasif) | — | planned |
| 16 | `hardware_abstraction_requirement` | envanter — soyutlama K000/ASIO | domain kuralı §6 | planned |
| 17 | `audio_relevance` | **YÜKSEK** — dengesiz ses zinciri | — | planned |
| 18 | `coremusic_relevance` | envanter tipi | — | planned |
| 19 | `parent_domain` | K001-donanım | — | planned |
| 20 | `owner_domain` | K001 (envanter) · devre K002 | ADR-063/064 | planned |
| 21 | `existing_document` | `audio/*` (komşu sınıf) | — | planned |
| 22 | `evidence` | aes.org 403 → standard doğrulanamadı ⚠️ | HEAD 2026-10-13 | planned |
| 23 | `status` | proposed | — | proposed |
| 24 | `confidence` | low | aes.org 403 · ölçüm yok | planned |
| 25 | `open_questions` | Q-C1 kesit/uzunluk · empedans VERIFY REQUIRED | — | ⚠️ |
| 26 | `proposed_path` | `donanaım-type/cables/` | — | planned |

### C-T15 · `guc-baglanti` · Güç Konnektörleri ve Kabloları

| # | Alan | Değer | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `type_id` | C-T15 | bu dosya §2/H5 | planned |
| 2 | `name_tr` | Güç konnektörleri ve güç kabloları | — | planned |
| 3 | `name_en` | Power connectors and power cables | — | planned |
| 4 | `category` | cables | Vault Steward 2026-10-13 | proposed |
| 5 | `subcategory` | guc-baglanti | bu dosya §2/H5 | planned |
| 6 | `description` | AC/DC güç taşıyan montajlar ve konnektörler. Katman-1/2. power/ sınıfıyla KOMŞU. | NOT PROVIDED | ⚠️ |
| 7 | `physical_component_or_device` | güç kablosu + konnektörler | — | planned |
| 8 | `primary_function` | cihaza güç taşıma | — | planned |
| 9 | `interfaces` | IEC 60320 C13/C14 · ATX · Molex · SATA-power · barrel ⚠️ | NOT PROVIDED | ⚠️ |
| 10 | `protocols` | yok (güç taşıyıcı) | — | planned |
| 11 | `power_requirements` | AC şebeke / DC ⚠️ kesin değer VERIFY REQUIRED | NOT PROVIDED | ⚠️ |
| 12 | `operating_environment` | stüdyo/ev/masaüstü ⚠️ | NOT PROVIDED | ⚠️ |
| 13 | `os_dependencies` | yok (donanım katmanı) | — | planned |
| 14 | `driver_requirements` | yok (pasif/besleme) | — | planned |
| 15 | `firmware_dependency` | ATX PSU ⚠️ (smart PSU var) | NOT PROVIDED | ⚠️ |
| 16 | `hardware_abstraction_requirement` | envanter — güç izleme K001 API | bu dosya §6 | planned |
| 17 | `audio_relevance` | **YÜKSEK** — amfi/besleme (Class AB) zinciri | kök CLAUDE.md Sabitler | planned |
| 18 | `coremusic_relevance` | envanter tipi | — | planned |
| 19 | `parent_domain` | K001-donanım | — | planned |
| 20 | `owner_domain` | K001 (envanter) · devre K002 | ADR-063/064 | planned |
| 21 | `existing_document` | `power/*` (komşu sınıf — dokunulmaz) | — | planned |
| 22 | `evidence` | repo: güç konnektörü ölçümü yok · K001 Q4 | ölçüm 2026-10-13 | planned |
| 23 | `status` | proposed | — | proposed |
| 24 | `confidence` | low | envanter yok · power/ komşu | planned |
| 25 | `open_questions` | Q-C1 envanter · power/ sınırı (Q-C9) | — | planned |
| 26 | `proposed_path` | `donanaım-type/cables/` | — | planned |

### C-T16 · `konnektor-sinifi` · Konnektör Sınıfları (Pin-Header / FPC / Card-Edge / M.2)

| # | Alan | Değer | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `type_id` | C-T16 | bu dosya §2/H6 | planned |
| 2 | `name_tr` | Konnektör sınıfları | — | planned |
| 3 | `name_en` | Connector classes | — | planned |
| 4 | `category` | cables | Vault Steward 2026-10-13 | proposed |
| 5 | `subcategory` | konnektor-sinifi | bu dosya §2/H6 | planned |
| 6 | `description` | Kablo↔mezra/çip geçişinin mekanik-elektriksel parçaları. Katman-2. | NOT PROVIDED | ⚠️ |
| 7 | `physical_component_or_device` | pin-header · FPC/FFC · card-edge · M.2 ⚠️ | NOT PROVIDED | ⚠️ |
| 8 | `primary_function` | kablo↔yuva geçişi | — | planned |
| 9 | `interfaces` | pin-header ⚠️ · FPC ⚠️ · M.2 ⚠️ | NOT PROVIDED | ⚠️ |
| 10 | `protocols` | yok (fiziksel geçiş) | — | planned |
| 11 | `power_requirements` | konnektör akım kapasitesi ⚠️ VERIFY REQUIRED | NOT PROVIDED | ⚠️ |
| 12 | `operating_environment` | kart-içi/kasa-içi ⚠️ | NOT PROVIDED | ⚠️ |
| 13 | `os_dependencies` | yok (fiziksel) | — | planned |
| 14 | `driver_requirements` | yok | — | planned |
| 15 | `firmware_dependency` | yok | — | planned |
| 16 | `hardware_abstraction_requirement` | envanter — pin eşlemesi K001 | bu dosya §6 | planned |
| 17 | `audio_relevance` | ses kartı/çip geçişleri | — | planned |
| 18 | `coremusic_relevance` | envanter tipi | — | planned |
| 19 | `parent_domain` | K001-donanım | — | planned |
| 20 | `owner_domain` | K001 (envanter) · devre K002 | ADR-063/064 | planned |
| 21 | `existing_document` | `donanaım-type-kapsam.md` §2/A6 | satır 46 | planned |
| 22 | `evidence` | repo: konektör ölçümü yok · K001 Q4 | ölçüm 2026-10-13 | planned |
| 23 | `status` | proposed | — | proposed |
| 24 | `confidence` | low | envanter yok | planned |
| 25 | `open_questions` | Q-C1 pin sayısı/marka · formfactors.org DNS fail | — | ⚠️ |
| 26 | `proposed_path` | `donanaım-type/cables/` | — | planned |

### C-T17 · `donusturucu` · Adapter / Dönüştürücü

| # | Alan | Değer | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `type_id` | C-T17 | bu dosya §2/H7 | planned |
| 2 | `name_tr` | Adapter / dönüştürücü (katman-geçişli uç) | — | planned |
| 3 | `name_en` | Adapter / converter | — | planned |
| 4 | `category` | cables | Vault Steward 2026-10-13 | proposed |
| 5 | `subcategory` | donusturucu | bu dosya §2/H7 | planned |
| 6 | `description` | Uç-uyumsuzluğunu köprüleyen adaptör (USB-C↔A, DP↔HDMI). Katman-1/2/3. | usb.org 200 · hdmi.org 200 | planned |
| 7 | `physical_component_or_device` | adaptör gövdesi + iki uç konektör | — | planned |
| 8 | `primary_function` | iki farklı fiziksel arayüzü köprüleme | — | planned |
| 9 | `interfaces` | USB-C ↔ USB-A ⚠️ · DP ↔ HDMI ⚠️ | NOT PROVIDED | ⚠️ |
| 10 | `protocols` | protokol dönüştürme (aktif adaptör) ⚠️ — etiket | NOT PROVIDED | ⚠️ |
| 11 | `power_requirements` | ⚠️ (pasif/aktif) | NOT PROVIDED | ⚠️ |
| 12 | `operating_environment` | taşınabilir ⚠️ | NOT PROVIDED | ⚠️ |
| 13 | `os_dependencies` | aktif adaptör ⚠️ | NOT PROVIDED | ⚠️ |
| 14 | `driver_requirements` | aktif adaptör ⚠️ | NOT PROVIDED | ⚠️ |
| 15 | `firmware_dependency` | aktif adaptör ⚠️ | NOT PROVIDED | ⚠️ |
| 16 | `hardware_abstraction_requirement` | envanter — köprüleme K001 | bu dosya §6 | planned |
| 17 | `audio_relevance` | düşük (taşınabilir bağlantı) | — | planned |
| 18 | `coremusic_relevance` | envanter tipi | — | planned |
| 19 | `parent_domain` | K001-donanım | — | planned |
| 20 | `owner_domain` | K001 (envanter) · sürücü K000 (aktif) | domain kuralı §6 | planned |
| 21 | `existing_document` | `donanaım-type-kapsam.md` §2/A6 | satır 46 | planned |
| 22 | `evidence` | repo: adapter ölçümü yok | ölçüm 2026-10-13 | planned |
| 23 | `status` | proposed | — | proposed |
| 24 | `confidence` | low | envanter yok | planned |
| 25 | `open_questions` | Q-C1 · pasif/aktif ayrımı envanterle | — | planned |
| 26 | `proposed_path` | `donanaım-type/cables/` | — | planned |

---

## §6 Sınırlar (Domain Sahipliği)

| Yüzey | Sahibi | cables/ ne yapar | cables/ ne YAPMAZ |
|---|---|---|---|
| Tip/envanter sınıflandırma | K001 (bu dosya) | 17 tip tanımı + 26 alan kaydı | — |
| Sürücü / OS / protokol yığını | `K000-isletim-sistemi` | yalnız etiket | sürücü/OS kodu |
| Devre / PCB / BOM / kesit hesabı | `K002-electroncis` (ADR-063/064) | yalnız envanter | devre tasarımı, BOM |
| Endüstriyel / embedded donanım (CAN, SFP, modem) | **UNKNOWN** + Q-C8 | — (kapsam dışı, network/ sınıfı) | — |
| Ekran / güç / ses sınıfları | `display/` · `power/` · `audio/` (dokunulmaz) | komşu referans | sınıfların içeriğini |

---

## §7 Açık Sorular

| # | Soru | Etki | Etiket |
|---|---|---|---|
| Q-C1 | Fiziksel kablo/konnektör envanteri (marka · kesit · uzunluk · pin sayısı) — nerede tutulacak? | envanter | ⚠️ VERIFICATION REQUIRED |
| Q-C2 | Anayasa §2/§10/§12 + kapsam §2/A6/§7-T1-T2/§12-M6/§7.3-X3 revizyonu onayı | sınır | ⚠️ VERIFICATION REQUIRED |
| Q-C3 | pcisig.com (PCIe) + jedec.org (RAM) 403 — spec sürümü doğrulanamadı | kanıt | ⚠️ VERIFICATION REQUIRED |
| Q-C4 | donanaım-type-kapsam.md §5/E4-E5 atıf uyuşmazlığı (152-153 → güncel §10 189-202) | kanıt | ⚠️ VERIFICATION REQUIRED |
| Q-C5 | Empedans/kesit/uzunluk ölçümleri bu oturumda 0 — hangi kapıda toplanacak? | ölçüm | NOT PROVIDED |
| Q-C6 | 75 Ω koaksiyel, dengeli empedans — hangi standarttan doğrulanacak? | kanıt | ⚠️ VERIFICATION REQUIRED (aes.org 403) |
| Q-C7 | I2S/TDM pin-şeması envanteri K001'de mi K002'de mi? | sınır | ⚠️ VERIFICATION REQUIRED |
| Q-C8 | Endüstriyel/embedded donanım (CAN, SFP, modem) sahipliği | sınır | ⚠️ VERIFICATION REQUIRED |
| Q-C9 | display/ · power/ komşu sınıflarıyla kablo katmanı sınırı | sınır | ⚠️ VERIFICATION REQUIRED |
| Q-C10 | USB Type-C repo'da 0 atıf — kapsamda mı, yoksa envanter boşluğu mu? | envanter | ⚠️ VERIFICATION REQUIRED |

---

## §8 Kaynaklar (HEAD 2026-10-13 · erişilebilir 200/202)

| # | URL | HTTP | Durum | Not |
|---|---|---|---|---|
| 1 | `https://www.usb.org/` | 200 | KULLANILDI | USB-IF — Type-C/A referans |
| 2 | `https://www.sata-io.org/` | 200 | KULLANILDI | SATA konektör referans |
| 3 | `https://www.displayport.org/` | 200 | KULLANILDI | DisplayPort referans |
| 4 | `https://www.hdmi.org/` | 200 | KULLANILDI | HDMI referans |
| 5 | `https://docs.kernel.org/i2c/index.html` | 200 | KULLANILDI | I2C kernel docs |
| 6 | `https://docs.kernel.org/spi/spi-summary.html` | 200 | KULLANILDI | SPI kernel docs |
| 7 | `https://docs.kernel.org/driver-api/tty/index.html` | 200 | KULLANILDI | UART/tty kernel docs |
| 8 | `https://docs.kernel.org/driver-api/gpio/index.html` | 200 | KULLANILDI | GPIO kernel docs |
| 9 | `https://docs.kernel.org/PCI/index.html` | 200 | KULLANILDI | PCI kernel docs |
| 10 | `https://docs.kernel.org/sound/index.html` | 200 | KULLANILDI | Ses alt sistemi kernel docs |
| 11 | `https://www.vesa.org/` | 200 | KULLANILDI | VESA (DisplayPort sahibi) |
| 12 | `https://www.jedec.org/` | 403 | **REDDEDİLDİ** | bellek standartları → ⚠️ VERIFICATION REQUIRED |
| 13 | `https://pcisig.com/` | 403 | **REDDEDİLDİ** | PCIe spec sürümü → ⚠️ VERIFICATION REQUIRED |
| 14 | `https://www.snia.org/` | 403 | **REDDEDİLDİ** | SATA/depolama |
| 15 | `https://www.aes.org/` | 403 | **REDDEDİLDİ** | S/PDIF/dengeli ses standartları → ⚠️ VERIFICATION REQUIRED |
| 16 | `https://www.microchip.com/` | 403 | **REDDEDİLDİ** | — |
| 17 | `https://www.raspberrypi.com/` | 403 | **REDDEDİLDİ** | — |
| 18 | `https://www.intel.com/` | 403 | **REDDEDİLDİ** | — |
| 19 | `https://www.nxp.com/` | 404 | **REDDEDİLDİ** | — |
| 20 | `https://www.st.com/` | timeout | **REDDEDİLDİ** | — |
| 21 | `https://sfforum.com/` | DNS fail | **REDDEDİLDİ** | — |
| 22 | `https://www.formfactors.org/` | DNS fail | **REDDEDİLDİ** | M.2 form faktörü |

> **Red kuralı:** 403/404/timeout/DNS-fail kaynaklar **kullanılmaz**; iddia bu
> durumda `⚠️ VERIFICATION REQUIRED (<domain> <kod>)` olarak işaretlenir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
