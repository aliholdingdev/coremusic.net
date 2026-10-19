---
title: "donanaım-type/network — Ağ Donanımı Kapsamı"
type: architecture
node_id: K001-donanım/donanaım-type/network/kapsam
node_type: donanaım-type-kapsam
status: proposed
parent_id: "K001-donanım/donanaım-type"
children:
  - index.md
  - network-dogrulama.md
  - network-ornekler.md
depends_on:
  - K001-donanım
  - K000-isletim-sistemi
evidence:
  - "Çelişki çapaları 2026-10-13 dosyadan birebir okundu: donanım-anayasa.md §2 (58) · §10/3 (195) · §12/C10 (250) · donanaım-type-kapsam.md §2/A6 (46) · §7/3-T1 (135) · §12/M6 (284)"
  - "Repo ölçümü 2026-10-13 (.ai/architecture, 911 md, kelime-sınırlı, büyük-küçük harf duyarlı): Bluetooth=29 · NIC=5 · Wi-Fi=4 · Ethernet=2 · CAN=0 · SFP=0 · modem=0 · transceiver=0"
  - "HEAD 2026-10-13 erişilebilir (202/200): ieee.org 202 · wi-fi.org · ieee802.org · ethernetalliance.org · bluetooth.com · can-cia.org · bosch.com · 3gpp.org · zephyrproject.org · linuxfoundation.org · docs.kernel.org/networking"
  - "HEAD 2026-10-13 REDDEDİLEN ve kullanılmayanlar: gsma.com 403 · microchip.com 403 · nxp.com 404 · st.com timeout · sfforum.com DNS"
  - "Repo kod ölçümü 2026-10-13: *.cpp=0 · *.h=0 · *.c=0 · *.ino=0 · *.py=1 · php=9470 · js=1682 → ağ donanımı kodu YOK"
validation: "proposed — 2026-10-13 yazıldı; anayasa ve üst kapsam metni revize EDİLMEDİ (§0 PROPOSED — APPROVAL REQUIRED); 0 fiziksel ölçüm; kapılar network-dogrulama.md §3"
open_questions:
  - "Q-N1: fiziksel ağ donanımı envanteri (marka · model · MAC · port) bilinmiyor — UNKNOWN (K001 Q4)"
  - "Q-N2: anayasa §2/§10/§12 ve kapsam §2/A6 · §7/3-T1 · §12/M6 metin revizyonu onayı alınmadı — status: proposed kalır"
  - "Q-N3: gsma.com 403 + sfforum.com DNS — hücresel/SFF detayları doğrulanamadı ⚠️ VERIFICATION REQUIRED"
  - "Q-N4: L-sınıfı (CAN · seri kontrolcü · endüstriyel) sahipliği UNKNOWN — K001 mi K003 mü?"
group: mimari
date: 2026-10-13
updated: 2026-10-13
---

# network — Kapsam (K001-donanım · donanaım-type · 7. alt bağlam)

> Bu dosya `network/` klasörünün **kapsam** kaydıdır: sınır çelişkisi, akış-vs-fiziksel
> ayrımı, katman sınırı, 10 tip kaydı (26 alan şema), sınırlar, açık sorular, kaynaklar.
> Kanıt/doğrulama ayrı dosyadır: `network-dogrulama.md` · örnekler: `network-ornekler.md`.
> Yazım notu: `donanaım-type` yazımı v2 kaynağına sadıktır — **düzeltme yasak**.

---

## §0 SINIR ÇELİŞKİSİ KAYDI — PROPOSED — APPROVAL REQUIRED

`network/` klasörü, anayasanın "yalnız 3 bağlam" kuralıyla çelişir. `cables/`'ın
aksine yasağın metninde **"ağ"** kelimesi geçmez (yasağın adı "ekran · kablo ·
güç kaynağı" der) — çelişki **dolaylıdır**: "yalnız 3 bağlam (audio · hdd · ssd)"
kuralı ağ donanımını kapsam dışı bırakır. Satırlar 2026-10-13'te birebir okundu:

| # | Kaynak dosya · bölüm · satır | Birebir metin | Çelişki yönü |
|---|---|---|---|
| 1 | `donanım-anayasa.md` §2 · satır 58 | `Cihaz tipi sınıflandırma | EVET | K001/donanaım-type | audio · hdd · ssd` | Sınıflandırma yalnız 3 bağlamı sayar — ağ yok |
| 2 | `donanım-anayasa.md` §10/3 · satır 195 | `donanaım-type altı 3 bağlam mı, fazlası mı? | genişleme | ⚠️ VERIFICATION REQUIRED` | Genişleme zaten açık soru |
| 3 | `donanım-anayasa.md` §12/C10 · satır 250 | `3 kademe-3 alt bağlam (audio/hdd/ssd) | EVET — envanterle | v2 §2.2` | Değişmezlik sınıfı 3 bağlamı kilitler |
| 4 | `donanaım-type-kapsam.md` §2/A6 · satır 46 | `Ekran · kablo · güç kaynağı türleri | DIŞINDA — anayasa §10/5 yasağı (ayrı bağlam AÇILMAZ)` | Ağ listelenmiyor — "yalnız 3 bağlam" kapsam dışı |
| 5 | `donanaım-type-kapsam.md` §7/3-T1 · satır 135 | `Altına yalnız 3 bağlam açılır (hdd · audio · ssd) | anayasa §10/5 — donanım-anayasa.md satır 152` | 3 bağlam kuralı (eskimiş atıf — Q-C4) |
| 6 | `donanaım-type-kapsam.md` §12/M6 · satır 284 | `Ekran / kablo / güç kaynağı türü | — | anayasa §10/5 | ❌ kapsam dışı — bağlam AÇILMAZ` | Ağ matriste de yok |
| 7 | Vault Steward 2026-10-13 dağıtık onayı | K001-donanım tip genişlemesi: H (kablo) · E (bellek) · G (ağ) sınıfları için dosya üretimi onaylandı | Yeni ihtiyaç: ağ tipleri sınıfsız kalıyordu |

**Fark (Red Team):** `cables/` yasağın **adında** geçer (doğrudan çelişki);
`network/` yasağın **kapsamı dışında** kalır (dolaylı çelişki — "yalnız 3 bağlam"
kuralı ihlal edilir). Her iki durumda da anayasa metni bu oturumda
**DEĞİŞTİRİLMEDİ** → `status: proposed`.

### §0.1 Kapanış Adımları (PENDING — ORKESTRATÖR)

| # | Adım | Hedef dosya | Sorumlu | Durum |
|---|---|---|---|---|
| 1 | `children:` satırına `network` ekle | `donanaım-type/index.md` | ORKESTRATÖR | ⏳ PENDING (subagent DOKUNMADI) |
| 2 | `§GRUPLAR` tablosuna network 4 satırı ekle | `donanaım-type/index.md` | ORKESTRATÖR | ⏳ PENDING |
| 3 | `children:` satırına `donanaım-type/network` ekle | `K001-donanım/index.md` | ORKESTRATÖR | ⏳ PENDING |
| 4 | §2/§10/§12 "yalnız 3 bağlam" → genişletilmiş bağlam listesi | `donanım-anayasa.md` | Vault Steward onayı | ⏳ ONAY BEKLİYOR |
| 5 | §2/A6 · §7/3-T1 · §12/M6 kapsamlı güncelleme | `donanaım-type-kapsam.md` | Vault Steward onayı | ⏳ ONAY BEKLİYOR |
| 6 | `git commit` | — | ORKESTRATÖR | ⏳ PENDING (subagent ATILMADI) |

---

## §1 Tip Tanımları

**Ağ donanımı** = bilgisayarın fiziksel ağ erişimini sağlayan donanım parçası
(entegre denetleyici · kart · modül · modem · transceiver). **Akış servisi erişimi**
(CoreMusic streaming) = **donanım tipi DEĞİL** — ağ donanımı üzerinden çalışan bir
yazılım erişim katmanıdır; bu dosyada yalnız **sınır kaydı** olarak taşınır (N-T10).

| Terim | K001'de mi? | Not |
|---|---|---|
| Fiziksel ağ donanımı (denetleyici · kart · modül) | EVET — envanter | 9 tip (N-T01…N-T09) |
| Ağ erişim-yüzeyi (akış servisi) | HAYIR — sınır kaydı (N-T10) | fiziksel değil |
| Sürücü/OS/yığın (TCP/IP · TLS) | HAYIR | `K000-isletim-sistemi` |
| Ağ protokolleri (802.3 · 802.11 · CAN · 3GPP) | HAYIR — yalnız etiket | katman-4 |
| Devre/PCB/BOM | HAYIR | `K002-electroncis` (ADR-063/064) |

## §2 Tipler (9 fiziksel + 1 sınır kaydı)

| # | Alt sınıf | Kapsadığı tipler | Not |
|---|---|---|---|
| G1 | `ethernet` | N-T01 Ethernet denetleyicisi · N-T06 transceiver (PHY) | kablolu |
| G2 | `kablosuz` | N-T02 Wi-Fi · N-T03 Bluetooth | RF |
| G3 | `hucresel` | N-T04 hücresel modem | mobil |
| G4 | `nic-kart` | N-T05 NIC (ağ arayüz kartı) · N-T07 SFP/SFP+ modülü | kart/modül |
| G5 | `endustriyel` | N-T08 CAN/indüstriyel · N-T09 seri kontrolcü | L-sınıfı — Q-N4 |
| G6 | `sinir-kaydi` | N-T10 akış erişim yüzeyi | **fiziksel DEĞİL** |

> E (bellek) · H (kablo) sınıfları bu dosyanın **kapsamı dışındadır** — ayrı
> klasörlerde (`memory/` · `cables/`). `display/` · `power/` · `audio/` klasörleri
> bu turda **dokunulmaz**.

## §3 Sınıflandırma Kriterleri

1. **Fiziksellik testi:** Kayıt yalnız fiziksel bir ağ parçası ise `status:
   proposed` alır; fiziksel olmayan (akış servisi) yalnız sınır kaydı olabilir
   (N-T10) ve `physical_component_or_device: YOK` taşır.
2. **Katman testi:** Ağ protokolleri (802.3 · 802.11 · CAN · 3GPP) yalnız
   `protocols` alanında etikettir — tip tanımı protokolle kurulmaz (cables §4
   kuralının ağ karşılığı).
3. **Sahiplik testi:** Sürücü/OS/yığın (TCP/IP · TLS) → K000 · devre → K002 ·
   sınıflandırma → K001. L-sınıfı (CAN · seri kontrolcü) sahipliği **UNKNOWN** (Q-N4).
4. **Ölçüm testi:** MAC/port/anten/akım ölçümleri bu oturumda **0** — her ölçüm
   alanı `NOT PROVIDED`/`VERIFY REQUIRED` taşır.
5. **Yasak testi:** Kayıt anayasa revizyonu iddiası taşımaz; yalnız PROPOSED envanterdir.

## §4 Katman Sınırı — Ağ Yığını Matrisi

> Kural: K001 yalnız **donanım katmanını** (1-2) envanterler; üst katmanlar
> yalnız etikettir ve sahibi K000'dir.

| Ağ katmanı | K001 kapsamı | network/ ne taşır | network/ ne YAPMAZ |
|---|---|---|---|
| Fiziksel (donanım · modül · anten · port) | EVET | tip + envanter (N-T01…N09) | — |
| Veri-bağlantı (MAC · Ethernet/Wi-Fi/CAN çerçeve) | HAYIR — etiket | yalnız `protocols` alanı | çerçeve tasarımı |
| Ağ (IP · yönlendirme) | HAYIR | yalnız etiket | TCP/IP kodu |
| Uygulama (akış servisi · TLS) | HAYIR — sınır kaydı (N-T10) | yalnız sınır notu | streaming kodu |
| Sürücü/OS | HAYIR | — | K000 |

**Akış-vs-fiziksel ayrımı (bu sınıfın ana kuralı):**

```text
CoreMusic streaming erişimi  →  YAZILIM/YÖNLENDİRME katmanı  →  K000 / uygulama
Ağ donanımı (NIC · Wi-Fi…)  →  FİZİKSEL donanım katmanı     →  K001 (bu sınıf)
Bu ikisi AYRIKayıtlardır; akış erişimi asla donanım tipi sayılmaz (N-T10 sınır kaydı).
```

---

## §5 Tip Kayıtları — 26 Alanlı Şema

> Şema (26 alan) `cables-kapsam.md` §5 ile aynıdır. Bilinmeyen → `UNKNOWN` ·
> veri yok → `NOT PROVIDED` · doğrulanamayan → `VERIFY REQUIRED`. Marka/model/
> MAC/ölçüm **uydurulmaz**.

### N-T01 · `ethernet` · Ethernet Denetleyicisi

| # | Alan | Değer | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `type_id` | N-T01 | bu dosya §2/G1 | planned |
| 2 | `name_tr` | Ethernet denetleyicisi | — | planned |
| 3 | `name_en` | Ethernet controller | — | planned |
| 4 | `category` | network | Vault Steward 2026-10-13 | proposed |
| 5 | `subcategory` | ethernet | bu dosya §2/G1 | planned |
| 6 | `description` | Ethernet MAC/PHY donanım denetleyicisi — fiziksel ağ erişim yüzeyi. | ethernetalliance.org 200 · ieee802.org 200 | planned |
| 7 | `physical_component_or_device` | denetleyici entegresi / entegre NIC | — | planned |
| 8 | `primary_function` | kablolu ağ erişimi | — | planned |
| 9 | `interfaces` | RJ45 / PHY arayüzü ⚠️ | NOT PROVIDED | ⚠️ |
| 10 | `protocols` | Ethernet (IEEE 802.3) — **etiket** | ieee802.org 200 | planned |
| 11 | `power_requirements` | ⚠️ | NOT PROVIDED | ⚠️ |
| 12 | `operating_environment` | indoor ⚠️ | NOT PROVIDED | ⚠️ |
| 13 | `os_dependencies` | K000 (ağ yığını) | docs.kernel.org/networking 200 | planned |
| 14 | `driver_requirements` | K000 | domain kuralı §6 | planned |
| 15 | `firmware_dependency` | K000 ⚠️ | domain kuralı §6 | planned |
| 16 | `hardware_abstraction_requirement` | envanter — soyutlama K000 | bu dosya §6 | planned |
| 17 | `audio_relevance` | düşük (akış taşıyıcısı — akış ≠ donanım, §4) | — | planned |
| 18 | `coremusic_relevance` | ağ erişim donanımı envanteri | — | planned |
| 19 | `parent_domain` | K001-donanım | — | planned |
| 20 | `owner_domain` | K001 (envanter) · sürücü K000 | domain kuralı §6 | planned |
| 21 | `existing_document` | `donanaım-type-kapsam.md` §2/A6 | satır 46 | planned |
| 22 | `evidence` | repo: Ethernet=2 ⚠️ düşük · ethernetalliance.org 200 | ölçüm 2026-10-13 | planned |
| 23 | `status` | proposed | — | proposed |
| 24 | `confidence` | low | repo 2 (düşük) | planned |
| 25 | `open_questions` | Q-N1 envanter · Q-N2 revizyon | — | planned |
| 26 | `proposed_path` | `donanaım-type/network/` (bu klasör) | — | planned |

### N-T02 · `kablosuz` · Wi-Fi Denetleyicisi / Modülü

| # | Alan | Değer | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `type_id` | N-T02 | bu dosya §2/G2 | planned |
| 2 | `name_tr` | Wi-Fi denetleyicisi / modülü | — | planned |
| 3 | `name_en` | Wi-Fi controller / module | — | planned |
| 4 | `category` | network | Vault Steward 2026-10-13 | proposed |
| 5 | `subcategory` | kablosuz | bu dosya §2/G2 | planned |
| 6 | `description` | Kablosuz ağ erişim donanımı (modül/entegre). Fiziksel katman. | wi-fi.org 200 · ieee802.org 200 | planned |
| 7 | `physical_component_or_device` | Wi-Fi modülü / entegre denetleyici + anten | — | planned |
| 8 | `primary_function` | kablosuz ağ erişimi | — | planned |
| 9 | `interfaces` | M.2 / PCIe / USB ⚠️ | NOT PROVIDED | ⚠️ |
| 10 | `protocols` | IEEE 802.11 — **etiket** | ieee802.org 200 | planned |
| 11 | `power_requirements` | ⚠️ | NOT PROVIDED | ⚠️ |
| 12 | `operating_environment` | indoor ⚠️ | NOT PROVIDED | ⚠️ |
| 13 | `os_dependencies` | K000 (ağ yığını) | docs.kernel.org/networking 200 | planned |
| 14 | `driver_requirements` | K000 | domain kuralı §6 | planned |
| 15 | `firmware_dependency` | K000 ⚠️ (firmware blob) | domain kuralı §6 | planned |
| 16 | `hardware_abstraction_requirement` | envanter — soyutlama K000 | bu dosya §6 | planned |
| 17 | `audio_relevance` | düşük (akış taşıyıcısı) | — | planned |
| 18 | `coremusic_relevance` | ağ erişim donanımı envanteri | — | planned |
| 19 | `parent_domain` | K001-donanım | — | planned |
| 20 | `owner_domain` | K001 (envanter) · sürücü K000 | domain kuralı §6 | planned |
| 21 | `existing_document` | `donanaım-type-kapsam.md` §2/A6 | satır 46 | planned |
| 22 | `evidence` | repo: Wi-Fi=4 ⚠️ düşük · wi-fi.org 200 | ölçüm 2026-10-13 | planned |
| 23 | `status` | proposed | — | proposed |
| 24 | `confidence` | low | repo 4 (düşük) | planned |
| 25 | `open_questions` | Q-N1 envanter · anten tipi UNKNOWN | — | planned |
| 26 | `proposed_path` | `donanaım-type/network/` | — | planned |

### N-T03 · `kablosuz` · Bluetooth Denetleyicisi / Modülü

| # | Alan | Değer | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `type_id` | N-T03 | bu dosya §2/G2 | planned |
| 2 | `name_tr` | Bluetooth denetleyicisi / modülü | — | planned |
| 3 | `name_en` | Bluetooth controller / module | — | planned |
| 4 | `category` | network | Vault Steward 2026-10-13 | proposed |
| 5 | `subcategory` | kablosuz | bu dosya §2/G2 | planned |
| 6 | `description` | Bluetooth erişim donanımı (modül/entegre). Fiziksel katman. | bluetooth.com 200 | planned |
| 7 | `physical_component_or_device` | Bluetooth modülü / entegre denetleyici + anten | — | planned |
| 8 | `primary_function` | Bluetooth erişimi (kısa mesafe) | — | planned |
| 9 | `interfaces` | USB / UART / SDIO ⚠️ | NOT PROVIDED | ⚠️ |
| 10 | `protocols` | Bluetooth (IEEE 802.15.1) — **etiket** | bluetooth.com 200 | planned |
| 11 | `power_requirements` | ⚠️ | NOT PROVIDED | ⚠️ |
| 12 | `operating_environment` | indoor ⚠️ | NOT PROVIDED | ⚠️ |
| 13 | `os_dependencies` | K000 | docs.kernel.org/networking 200 | planned |
| 14 | `driver_requirements` | K000 | domain kuralı §6 | planned |
| 15 | `firmware_dependency` | K000 ⚠️ (firmware blob) | domain kuralı §6 | planned |
| 16 | `hardware_abstraction_requirement` | envanter — soyutlama K000 | bu dosya §6 | planned |
| 17 | `audio_relevance` | orta (Bluetooth ses profilleri ⚠️ etiket) | bluetooth.com 200 | planned |
| 18 | `coremusic_relevance` | ağ erişim donanımı envanteri | — | planned |
| 19 | `parent_domain` | K001-donanım | — | planned |
| 20 | `owner_domain` | K001 (envanter) · sürücü K000 | domain kuralı §6 | planned |
| 21 | `existing_document` | `donanaım-type-kapsam.md` §2/A6 | satır 46 | planned |
| 22 | `evidence` | repo: Bluetooth=29 · bluetooth.com 200 | ölçüm 2026-10-13 | planned |
| 23 | `status` | proposed | — | proposed |
| 24 | `confidence` | medium | repo 29 + bluetooth.com 200 | planned |
| 25 | `open_questions` | Q-N1 envanter · sürüm/versiyon UNKNOWN | — | planned |
| 26 | `proposed_path` | `donanaım-type/network/` | — | planned |

### N-T04 · `hucresel` · Hücresel Modem

| # | Alan | Değer | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `type_id` | N-T04 | bu dosya §2/G3 | planned |
| 2 | `name_tr` | Hücresel modem | — | planned |
| 3 | `name_en` | Cellular modem | — | planned |
| 4 | `category` | network | Vault Steward 2026-10-13 | proposed |
| 5 | `subcategory` | hucresel | bu dosya §2/G3 | planned |
| 6 | `description` | Hücresel ağ erişim donanımı (mobil şebeke). Fiziksel katman. | 3gpp.org 200 | planned |
| 7 | `physical_component_or_device` | modem modülü / kart + anten | — | planned |
| 8 | `primary_function` | hücresel şebeke erişimi | — | planned |
| 9 | `interfaces` | M.2 / PCIe / USB ⚠️ | NOT PROVIDED | ⚠️ |
| 10 | `protocols` | 3GPP (LTE/5G ⚠️ sürüm) — **etiket** | 3gpp.org 200 | planned |
| 11 | `power_requirements` | ⚠️ | NOT PROVIDED | ⚠️ |
| 12 | `operating_environment` | mobil/hareketli ⚠️ | NOT PROVIDED | ⚠️ |
| 13 | `os_dependencies` | K000 | docs.kernel.org/networking 200 | planned |
| 14 | `driver_requirements` | K000 | domain kuralı §6 | planned |
| 15 | `firmware_dependency` | K000 ⚠️ (modem firmware) | domain kuralı §6 | planned |
| 16 | `hardware_abstraction_requirement` | envanter — soyutlama K000 | bu dosya §6 | planned |
| 17 | `audio_relevance` | düşük (taşınabilir erişim) | — | planned |
| 18 | `coremusic_relevance` | ağ erişim donanımı envanteri | — | planned |
| 19 | `parent_domain` | K001-donanım | — | planned |
| 20 | `owner_domain` | K001 (envanter) · sürücü K000 | domain kuralı §6 | planned |
| 21 | `existing_document` | `donanaım-type-kapsam.md` §2/A6 | satır 46 | planned |
| 22 | `evidence` | repo: modem=0 ⚠️ envanter boşluğu · 3gpp.org 200 · gsma.com 403 | ölçüm 2026-10-13 | planned |
| 23 | `status` | proposed | — | proposed |
| 24 | `confidence` | low | repo 0 · gsma 403 | planned |
| 25 | `open_questions` | Q-N1 · Q-N3 gsma 403 — sürüm ⚠️ VERIFICATION REQUIRED | — | ⚠️ |
| 26 | `proposed_path` | `donanaım-type/network/` | — | planned |

### N-T05 · `nic-kart` · NIC (Ağ Arayüz Kartı)

| # | Alan | Değer | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `type_id` | N-T05 | bu dosya §2/G4 | planned |
| 2 | `name_tr` | NIC (ağ arayüz kartı) | — | planned |
| 3 | `name_en` | Network interface card | — | planned |
| 4 | `category` | network | Vault Steward 2026-10-13 | proposed |
| 5 | `subcategory` | nic-kart | bu dosya §2/G4 | planned |
| 6 | `description` | Fiziksel ağ arayüz kartı (bağımsız donanım). Fiziksel katman. | ethernetalliance.org 200 | planned |
| 7 | `physical_component_or_device` | PCIe/USB NIC kartı | — | planned |
| 8 | `primary_function` | ağ arayüzü sağlama | — | planned |
| 9 | `interfaces` | PCIe / USB ⚠️ | NOT PROVIDED | ⚠️ |
| 10 | `protocols` | Ethernet — **etiket** | ieee802.org 200 | planned |
| 11 | `power_requirements` | ⚠️ | NOT PROVIDED | ⚠️ |
| 12 | `operating_environment` | kasa içi ⚠️ | NOT PROVIDED | ⚠️ |
| 13 | `os_dependencies` | K000 | docs.kernel.org/networking 200 | planned |
| 14 | `driver_requirements` | K000 | domain kuralı §6 | planned |
| 15 | `firmware_dependency` | K000 ⚠️ | domain kuralı §6 | planned |
| 16 | `hardware_abstraction_requirement` | envanter — soyutlama K000 | bu dosya §6 | planned |
| 17 | `audio_relevance` | düşük (akış taşıyıcısı) | — | planned |
| 18 | `coremusic_relevance` | ağ erişim donanımı envanteri | — | planned |
| 19 | `parent_domain` | K001-donanım | — | planned |
| 20 | `owner_domain` | K001 (envanter) · sürücü K000 | domain kuralı §6 | planned |
| 21 | `existing_document` | `donanaım-type-kapsam.md` §2/A6 | satır 46 | planned |
| 22 | `evidence` | repo: NIC=5 · ethernetalliance.org 200 | ölçüm 2026-10-13 | planned |
| 23 | `status` | proposed | — | proposed |
| 24 | `confidence` | low | repo 5 (düşük) | planned |
| 25 | `open_questions` | Q-N1 envanter (marka · model · MAC) | — | planned |
| 26 | `proposed_path` | `donanaım-type/network/` | — | planned |

### N-T06 · `ethernet` · Transceiver (PHY)

| # | Alan | Değer | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `type_id` | N-T06 | bu dosya §2/G1 | planned |
| 2 | `name_tr` | Transceiver (PHY) | — | planned |
| 3 | `name_en` | Transceiver (PHY) | — | planned |
| 4 | `category` | network | Vault Steward 2026-10-13 | proposed |
| 5 | `subcategory` | ethernet | bu dosya §2/G1 | planned |
| 6 | `description` | Ethernet PHY transceiver — fiziksel katma (medya dönüşümü). | ethernetalliance.org 200 | planned |
| 7 | `physical_component_or_device` | PHY entegresi | — | planned |
| 8 | `primary_function` | dijital↔medya sinyal dönüşümü | — | planned |
| 9 | `interfaces` | MDI / MDI-X ⚠️ | NOT PROVIDED | ⚠️ |
| 10 | `protocols` | Ethernet PHY — **etiket** | ieee802.org 200 | planned |
| 11 | `power_requirements` | ⚠️ | NOT PROVIDED | ⚠️ |
| 12 | `operating_environment` | kart-içi ⚠️ | NOT PROVIDED | ⚠️ |
| 13 | `os_dependencies` | K000 | docs.kernel.org/networking 200 | planned |
| 14 | `driver_requirements` | K000 | domain kuralı §6 | planned |
| 15 | `firmware_dependency` | K001-dışında ⚠️ | — | ⚠️ |
| 16 | `hardware_abstraction_requirement` | envanter — soyutlama K000 | bu dosya §6 | planned |
| 17 | `audio_relevance` | düşük | — | planned |
| 18 | `coremusic_relevance` | ağ erişim donanımı envanteri | — | planned |
| 19 | `parent_domain` | K001-donanım | — | planned |
| 20 | `owner_domain` | K001 (envanter) · devre K002 | ADR-063/064 | planned |
| 21 | `existing_document` | `donanaım-type-kapsam.md` §2/A6 | satır 46 | planned |
| 22 | `evidence` | repo: transceiver=0 ⚠️ envanter boşluğu | ölçüm 2026-10-13 | planned |
| 23 | `status` | proposed | — | proposed |
| 24 | `confidence` | low | repo 0 | planned |
| 25 | `open_questions` | Q-N1 envanter | — | planned |
| 26 | `proposed_path` | `donanaım-type/network/` | — | planned |

### N-T07 · `nic-kart` · SFP / SFP+ Modülü

| # | Alan | Değer | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `type_id` | N-T07 | bu dosya §2/G4 | planned |
| 2 | `name_tr` | SFP / SFP+ modülü | — | planned |
| 3 | `name_en` | SFP / SFP+ module | — | planned |
| 4 | `category` | network | Vault Steward 2026-10-13 | proposed |
| 5 | `subcategory` | nic-kart | bu dosya §2/G4 | planned |
| 6 | `description` | Değiştirilebilir ağ modülü (small form-factor). Fiziksel katman. | ethernetalliance.org 200 | planned |
| 7 | `physical_component_or_device` | SFP modülü (fiber/bakır) | — | planned |
| 8 | `primary_function` | ağ medya-arayüzü modülü | — | planned |
| 9 | `interfaces` | SFP yuvası ⚠️ | NOT PROVIDED | ⚠️ |
| 10 | `protocols` | Ethernet — **etiket** | ieee802.org 200 | planned |
| 11 | `power_requirements` | ⚠️ | NOT PROVIDED | ⚠️ |
| 12 | `operating_environment` | kasa içi ⚠️ | NOT PROVIDED | ⚠️ |
| 13 | `os_dependencies` | K000 | docs.kernel.org/networking 200 | planned |
| 14 | `driver_requirements` | K000 | domain kuralı §6 | planned |
| 15 | `firmware_dependency` | K000 ⚠️ | domain kuralı §6 | planned |
| 16 | `hardware_abstraction_requirement` | envanter — soyutlama K000 | bu dosya §6 | planned |
| 17 | `audio_relevance` | düşük | — | planned |
| 18 | `coremusic_relevance` | ağ erişim donanımı envanteri | — | planned |
| 19 | `parent_domain` | K001-donanım | — | planned |
| 20 | `owner_domain` | K001 (envanter) · sürücü K000 | domain kuralı §6 | planned |
| 21 | `existing_document` | `donanaım-type-kapsam.md` §2/A6 | satır 46 | planned |
| 22 | `evidence` | repo: SFP=0 ⚠️ · sfforum.com DNS fail → spec doğrulanamadı | ölçüm 2026-10-13 | planned |
| 23 | `status` | proposed | — | proposed |
| 24 | `confidence` | low | repo 0 · spec kaynağı erişilemedi | planned |
| 25 | `open_questions` | Q-N1 · Q-N3 sfforum DNS — SFF spec ⚠️ VERIFICATION REQUIRED | — | ⚠️ |
| 26 | `proposed_path` | `donanaım-type/network/` | — | planned |

### N-T08 · `endustriyel` · CAN / Endüstriyel Haberleşme Donanımı

| # | Alan | Değer | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `type_id` | N-T08 | bu dosya §2/G5 | planned |
| 2 | `name_tr` | CAN / endüstriyel haberleşme donanımı | — | planned |
| 3 | `name_en` | CAN / industrial communication hardware | — | planned |
| 4 | `category` | network | Vault Steward 2026-10-13 | proposed |
| 5 | `subcategory` | endustriyel | bu dosya §2/G5 | planned |
| 6 | `description` | CAN/endüstriyel haberleşme denetleyicisi — L-sınıfı/embedded donanım. | can-cia.org 200 · bosch.com 200 | planned |
| 7 | `physical_component_or_device` | CAN denetleyicisi / transceiver ⚠️ | NOT PROVIDED | ⚠️ |
| 8 | `primary_function` | endüstriyel haberleşme | — | planned |
| 9 | `interfaces` | CAN-H/CAN-L ⚠️ | NOT PROVIDED | ⚠️ |
| 10 | `protocols` | CAN / CAN-FD ⚠️ — **etiket** | can-cia.org 200 | planned |
| 11 | `power_requirements` | ⚠️ | NOT PROVIDED | ⚠️ |
| 12 | `operating_environment` | endüstriyel/otomotiv ⚠️ | NOT PROVIDED | ⚠️ |
| 13 | `os_dependencies` | K000 ⚠️ (embedded OS) | zephyrproject.org 200 | planned |
| 14 | `driver_requirements` | K000 ⚠️ | domain kuralı §6 | planned |
| 15 | `firmware_dependency` | K001-dışında ⚠️ | — | ⚠️ |
| 16 | `hardware_abstraction_requirement` | envanter — soyutlama K000/Zephyr | bu dosya §6 | planned |
| 17 | `audio_relevance` | düşük | — | planned |
| 18 | `coremusic_relevance` | envanter tipi (L-sınıfı — Q-N4) | — | ⚠️ |
| 19 | `parent_domain` | K001-donanım | — | planned |
| 20 | `owner_domain` | **UNKNOWN** (K001 mi K003 mü?) | Q-N4 | ⚠️ |
| 21 | `existing_document` | `donanaım-type-kapsam.md` §2/A6 | satır 46 | planned |
| 22 | `evidence` | repo: CAN=0 ⚠️ · can-cia.org 200 · bosch.com 200 | ölçüm 2026-10-13 | planned |
| 23 | `status` | proposed | — | proposed |
| 24 | `confidence` | low | repo 0 · sahiplik UNKNOWN | planned |
| 25 | `open_questions` | Q-N1 · Q-N4 sahiplik (K001 vs K003) ⚠️ | — | ⚠️ |
| 26 | `proposed_path` | `donanaım-type/network/` | — | planned |

### N-T09 · `endustriyel` · Seri Kontrolcü (Serial Controller)

| # | Alan | Değer | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `type_id` | N-T09 | bu dosya §2/G5 | planned |
| 2 | `name_tr` | Seri kontrolcü (serial controller) | — | planned |
| 3 | `name_en` | Serial controller | — | planned |
| 4 | `category` | network | Vault Steward 2026-10-13 | proposed |
| 5 | `subcategory` | endustriyel | bu dosya §2/G5 | planned |
| 6 | `description` | Seri haberleşme denetleyicisi (UART-tabanlı endüstriyel). Fiziksel katman. | docs.kernel.org/tty 200 | planned |
| 7 | `physical_component_or_device` | seri denetleyici entegresi | — | planned |
| 8 | `primary_function` | seri haberleşme | — | planned |
| 9 | `interfaces` | TX/RX ⚠️ (kablo ayrı: C-T08) | NOT PROVIDED | ⚠️ |
| 10 | `protocols` | seri (UART) — **etiket** | docs.kernel.org/tty 200 | planned |
| 11 | `power_requirements` | ⚠️ | NOT PROVIDED | ⚠️ |
| 12 | `operating_environment` | kart-içi/endüstriyel ⚠️ | NOT PROVIDED | ⚠️ |
| 13 | `os_dependencies` | K000 (tty alt sistemi) | docs.kernel.org/tty 200 | planned |
| 14 | `driver_requirements` | K000 | domain kuralı §6 | planned |
| 15 | `firmware_dependency` | K001-dışında ⚠️ | — | ⚠️ |
| 16 | `hardware_abstraction_requirement` | envanter — soyutlama K000 | bu dosya §6 | planned |
| 17 | `audio_relevance` | düşük | — | planned |
| 18 | `coremusic_relevance` | envanter tipi (L-sınıfı — Q-N4) | — | ⚠️ |
| 19 | `parent_domain` | K001-donanım | — | planned |
| 20 | `owner_domain` | **UNKNOWN** (K001 mi K003 mü?) | Q-N4 | ⚠️ |
| 21 | `existing_document` | `donanaım-type-kapsam.md` §2/A6 · cables C-T08 | — | planned |
| 22 | `evidence` | repo: seri kontrolcü ölçümü yok · kernel/tty 200 | ölçüm 2026-10-13 | planned |
| 23 | `status` | proposed | — | proposed |
| 24 | `confidence` | low | repo ölçümü yok · sahiplik UNKNOWN | planned |
| 25 | `open_questions` | Q-N1 · Q-N4 sahiplik ⚠️ · cables C-T08 sınırı | — | ⚠️ |
| 26 | `proposed_path` | `donanaım-type/network/` | — | planned |

### N-T10 · `sinir-kaydi` · CoreMusic Akış Erişim Yüzeyi (FİZİKSEL DEĞİL)

| # | Alan | Değer | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `type_id` | N-T10 | bu dosya §2/G6 | planned |
| 2 | `name_tr` | CoreMusic akış erişim yüzeyi (sınır kaydı) | — | planned |
| 3 | `name_en` | CoreMusic streaming access surface (boundary record) | — | planned |
| 4 | `category` | network | Vault Steward 2026-10-13 | proposed |
| 5 | `subcategory` | sinir-kaydi | bu dosya §2/G6 | planned |
| 6 | `description` | Streaming erişimi bir donanım tipi DEĞİL — fiziksel ağ donanımı üzerinde çalışan yazılım erişim katmanıdır. Sınır kaydı. | bu dosya §4 | planned |
| 7 | `physical_component_or_device` | **YOK** — fiziksel donanım değildir | — | planned |
| 8 | `primary_function` | akış servisine erişim (yazılım) | — | planned |
| 9 | `interfaces` | — (donanım arayüzü yok) | — | planned |
| 10 | `protocols` | HTTPS/TLS ⚠️ — uygulama katmanı, K000 | NOT PROVIDED | ⚠️ |
| 11 | `power_requirements` | — (donanım değil) | — | planned |
| 12 | `operating_environment` | uygulama katmanı | — | planned |
| 13 | `os_dependencies` | K000 (ağ yığını + TLS) | — | planned |
| 14 | `driver_requirements` | K000 | — | planned |
| 15 | `firmware_dependency` | — (donanım değil) | — | planned |
| 16 | `hardware_abstraction_requirement` | **SINIR KAYDI** — fiziksel donanıma bağlanır, onun yerine geçmez | bu dosya §4 | planned |
| 17 | `audio_relevance` | orta (akış = CoreMusic işlevi, donanım değil) | — | planned |
| 18 | `coremusic_relevance` | streaming erişiminin donanımdan AYRI olduğunu belgeler | — | planned |
| 19 | `parent_domain` | K001-donanım | — | planned |
| 20 | `owner_domain` | **K000 / uygulama** (donanım değil) | bu dosya §4 | planned |
| 21 | `existing_document` | bu dosya §4 (akış-vs-fiziksel kuralı) | — | planned |
| 22 | `evidence` | ayrım kuralı — fiziksellik testi (§3/1) | — | planned |
| 23 | `status` | proposed (sınır kaydı — asla `confirmed` donanım olmaz) | — | proposed |
| 24 | `confidence` | high (ayrım kuralı net) | — | planned |
| 25 | `open_questions` | akış erişimi hangi vault dosyasında yaşayacak? (K000/uç-kullanım) | — | planned |
| 26 | `proposed_path` | `donanaım-type/network/` | — | planned |

---

## §6 Sınırlar (Domain Sahipliği)

| Yüzey | Sahibi | network/ ne yapar | network/ ne YAPMAZ |
|---|---|---|---|
| Tip/envanter sınıflandırma | K001 (bu dosya) | 10 kayıt (9 fiziksel + 1 sınır) | — |
| Sürücü/OS/ağ yığını (TCP/IP · TLS) | `K000-isletim-sistemi` | yalnız etiket | ağ yığını kodu |
| Devre/PCB/BOM | `K002-electroncis` (ADR-063/064) | yalnız envanter | devre tasarımı |
| Streaming servis yazılımı | K000 / uygulama | sınır kaydı (N-T10) | streaming kodu |
| L-sınıfı (CAN · seri) sahipliği | **UNKNOWN** (Q-N4) | — | — |
| Kablo/konnektör | `cables/` (dokunulmaz) | komşu referans | kablo içeriği |

---

## §7 Açık Sorular

| # | Soru | Etki | Etiket |
|---|---|---|---|
| Q-N1 | Ağ donanımı envanteri (marka · model · MAC · port) — nerede tutulacak? | envanter | ⚠️ VERIFICATION REQUIRED |
| Q-N2 | Anayasa §2/§10/§12 + kapsam §2/A6 · §7/3-T1 · §12/M6 revizyonu onayı | sınır | ⚠️ VERIFICATION REQUIRED |
| Q-N3 | gsma.com 403 + sfforum.com DNS — hücresel/SFF detayları doğrulanamadı | kanıt | ⚠️ VERIFICATION REQUIRED |
| Q-N4 | L-sınıfı (CAN · seri kontrolcü) sahipliği — K001 mi K003 mü? | sınır | ⚠️ VERIFICATION REQUIRED |
| Q-N5 | MAC/port/anten ölçümleri 0 — hangi kapıda toplanacak? | ölçüm | NOT PROVIDED |
| Q-N6 | N-T10 akış erişimi hangi vault dosyasında yaşayacak? | tasarım | ⚠️ VERIFICATION REQUIRED |
| Q-N7 | Ethernet=2 · Wi-Fi=4 repo atıfı düşük — kapsam sorgusu | envanter | ⚠️ VERIFICATION REQUIRED |
| Q-N8 | `cables/` C-T08 (UART kablosu) ↔ N-T09 (seri kontrolcü) sınırı | sınır | ⚠️ VERIFICATION REQUIRED |

---

## §8 Kaynaklar (HEAD 2026-10-13 · erişilebilir 202/200)

| # | URL | HTTP | Durum | Not |
|---|---|---|---|---|
| 1 | `https://www.ieee.org/` | 202 | KULLANILDI | IEEE — 802.x standartları |
| 2 | `https://www.wi-fi.org/` | 200 | KULLANILDI | Wi-Fi Alliance |
| 3 | `https://www.ieee802.org/` | 200 | KULLANILDI | IEEE 802 LAN/MAN |
| 4 | `https://ethernetalliance.org/` | 200 | KULLANILDI | Ethernet Alliance |
| 5 | `https://www.bluetooth.com/` | 200 | KULLANILDI | Bluetooth SIG |
| 6 | `https://www.can-cia.org/` | 200 | KULLANILDI | CAN in Automation |
| 7 | `https://www.bosch.com/` | 200 | KULLANILDI | CAN (Bosch — CAN kaynağı) |
| 8 | `https://www.3gpp.org/` | 200 | KULLANILDI | 3GPP — hücresel |
| 9 | `https://www.zephyrproject.org/` | 200 | KULLANILDI | Zephyr RTOS (embedded) |
| 10 | `https://www.linuxfoundation.org/` | 200 | KULLANILDI | Linux Foundation |
| 11 | `https://docs.kernel.org/networking/index.html` | 200 | KULLANILDI | Linux ağ alt sistemi |
| 12 | `https://www.gsma.com/` | 403 | **REDDEDİLDİ** | hücresel — ⚠️ VERIFICATION REQUIRED |
| 13 | `https://www.microchip.com/` | 403 | **REDDEDİLDİ** | — |
| 14 | `https://www.nxp.com/` | 404 | **REDDEDİLDİ** | — |
| 15 | `https://www.st.com/` | timeout | **REDDEDİLDİ** | — |
| 16 | `https://sfforum.com/` | DNS fail | **REDDEDİLDİ** | SFF — SFP spec ⚠️ |

> **Red kuralı:** 403/404/timeout/DNS-fail kaynaklar **kullanılmaz**; ilgili iddia
> `⚠️ VERIFICATION REQUIRED (<kaynak> <kod>)` olarak işaretlenir.

---

## §9 OS × DİL ERİŞİM MATRİSİ (ağ donanımı)

> Bu bölüm `donanım-platformlararasi-kod-ornekleri.md` §PLATFORM MATRİSİ (P1–P6)
> üzerine kuruludur; **yeni URL eklenmemiştir**. Ağ erişimi fiziksel NIC/Wi-Fi/
> modem donanımının OS üzerinden listelenmesidir; TCP/IP yığını K000'dir (§4).

| Dil | Windows | macOS (Darwin) | Linux |
|---|---|---|---|
| JavaScript (Node.js) | `GetAdaptersAddresses` (native addon) ⚠️ | `getifaddrs` ⚠️ (404) | `/proc/net` + netlink ⚠️ |
| C | `GetAdaptersAddresses` | `getifaddrs` ⚠️ | `/proc/net` + netlink |
| Python | `GetAdaptersAddresses` (ctypes) ⚠️ | `getifaddrs` ⚠️ | `/proc/net` + netlink |
| Go | `GetAdaptersAddresses` ⚠️ (net stdlib) | `getifaddrs` ⚠️ | `/proc/net` + netlink ⚠️ |
| Rust | `GetAdaptersAddresses` ⚠️ (crate adı yok) | `getifaddrs` ⚠️ | `/proc/net` + netlink ⚠️ |
| C# (.NET) | `NetworkInterface` (BCL) | `getifaddrs` ⚠️ P/Invoke | `/proc/net` okuma |
| PHP | `GetAdaptersAddresses` ⚠️ (sarmalayıcı yok) | `getifaddrs` ⚠️ | `/proc/net` okuma |
| Bash | — | `ifconfig`/`netstat` ⚠️ | `ip link` · `/proc/net` |
| PowerShell | `Get-NetAdapter` ⚠️ | — | — |
| Java | JNA/FFM ⚠️ | JNA/FFM ⚠️ | JNA/FFM ⚠️ |
| Swift | — | `getifaddrs` ⚠️ | — |
| Kotlin | JNA/FFM ⚠️ | JNA/FFM ⚠️ | JNA/FFM ⚠️ |
| C++ | `GetAdaptersAddresses` | `getifaddrs` ⚠️ | `/proc/net` + netlink |

**Öneri (repo dili PHP/JS):** JS/Node ve PHP Linux dalları `/proc/net` +
`netifaces`/dosya okuma ile başlar (`docs.kernel.org/filesystems/proc.html` 200);
Windows `GetAdaptersAddresses` (learn.microsoft.com iphlpapi 200, §K3/22),
macOS `getifaddrs` **şablon/PROPOSED** kalır (R8 404 → `⚠️`).

**Risk/uyumluluk:**
- macOS `getifaddrs` `⚠️ VERIFICATION REQUIRED` (Apple archive 404 — §K4/R8);
  `__APPLE__` URL'siz (R4).
- netlink (Linux) düşük seviye socket çağrısıdır — erişim K000/kernel sınırındadır;
  bu bölüm yalnız **statik arayüz listeleme** şablonu satar.
- MAC adresi/anten gücü **envanter verisi** değildir (Q-N1); okuma sonucu
  donanım kanıtı sayılmaz, yalnız erişim yolu şablonudur.
- Bluetooth/Wi-Fi modül tespiti (N-T02/N-T03) OS registry/sysfs ile farklılaşır —
  ayrı ölçüm kapıları `network-dogrulama.md` §3'tedir.

Kapsamlı şablonlar: `donanım-platformlararasi-kod-ornekleri.md` §G1–§G11 · tam matris: `raporlar/K001-donanım-os-dil-matrisi.md`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
