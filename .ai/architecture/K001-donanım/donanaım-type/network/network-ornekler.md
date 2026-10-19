---
title: "donanaım-type/network — Örnekler (Ayrım Örnekleri, Çalışma Sayfaları, Şablonlar)"
type: kod
node_id: K001-donanım/donanaım-type/network/ornekler
node_type: donanaım-type-ornekler
status: proposed
parent_id: "K001-donanım/donanaım-type"
children:
  - index.md
  - network-kapsam.md
  - network-dogrulama.md
depends_on:
  - K001-donanım
  - K000-isletim-sistemi
evidence:
  - "Repo kod ölçümü 2026-10-13: *.cpp=0 · *.h=0 · *.c=0 · *.ino=0 · *.xc=0 · *.cs=0 · *.go=0 · *.rs=0 · *.py=1 → ağ donanımı GERÇEK KODU YOK"
  - "Bu dosya yalnızca ŞABLON ve ÇALIŞMA SAYFASI içerir — gerçek üretim kodu değil, doldurulacak formlar"
validation: "proposed — 2026-10-13 yazıldı; anayasa revizyonu YAPILMADI (kapsam §0); 0 gerçek kod"
open_questions:
  - "Q-N9: ağ donanımı kod yüzeyi (envanter scripti, port okuma) ne zaman yazılacak? — bu turda YOK"
group: kod
date: 2026-10-13
updated: 2026-10-13
---

# network — Örnekler (K001-donanım · donanaım-type · network)

> Bu dosya `network/` sınıfının **örnek/şablon** kaydıdır. **Önemli (Truth Mode):**
> bu klasörde gerçek ağ donanımı **kod dosyası YOKTUR** (repo ölçümü 2026-10-13:
> `*.cpp=0 · *.h=0 · *.c=0 · *.ino=0 · *.py=1`); bu dosyadaki her şey
> **doldurulacak şablon / çalışma sayfası**dır — üretim kodu değil, uydurma kod değil.
> Kapsam/çelişki: `network-kapsam.md` §0 · kapılar: `network-dogrulama.md` §2.

---

## §1 Gerçek Repo Kodu — YOK (Kanıtla)

| # | Dosya tipi | Repo sayısı | Yorum |
|---|---|---|---|
| 1 | `*.cpp` | 0 | ağ donanımı kodu yok |
| 2 | `*.h` | 0 | — |
| 3 | `*.c` | 0 | — |
| 4 | `*.ino` | 0 | — |
| 5 | `*.xc` | 0 | — |
| 6 | `*.cs` | 0 | — |
| 7 | `*.go` | 0 | — |
| 8 | `*.rs` | 0 | — |
| 9 | `*.py` | 1 | alakasız (vault scripti) |
| 10 | `*.php` | 9470 | web uygulaması — ağ donanımı kodu DEĞİL |
| 11 | `*.js` | 1682 | frontend — ağ donanımı kodu DEĞİL |

**Sonuç:** `network/` sınıfının kod yüzeyi **boştur**; bu dosya yalnızca
gelecekteki kod/ölçüm oturumları için **şablon** sağlar. Uydurma üretim kodu yazılmaz.

## §2 Akış-Vs-Fiziksel Ayrım Örneği — Doğru/Yanlış Kayıt

> Bu bölüm `network-kapsam.md` §4 ayrım kuralının **uygulama örneğidir**.

### §2.1 Yanlış (akış donanım sanıldı — K-N2 RED)

```text
YANLIŞ KAYIT:
  type     : "CoreMusic streaming erişimi"
  category : network
  neden    : Streaming erişimi FİZİKSEL donanım DEĞİL — yazılım erişim katmanıdır.
  sonuç    : K-N2 RED — fiziksellik testi (kapsam §3/1) ihlali.
```

### §2.2 Doğru (ayrım korundu — K-N2 GEÇ)

```text
DOĞRU KAYITLAR (ikisi ayrı):
  [a] N-T05 NIC (fiziksel donanım):
       physical_component_or_device : PCIe/USB NIC kartı
       owner_domain                 : K001 (envanter) · sürücü K000
       status                       : proposed
  [b] N-T10 akış erişim yüzeyi (SINIR KAYDI — donanım değil):
       physical_component_or_device : YOK — fiziksel donanım değildir
       owner_domain                 : K000 / uygulama
       status                       : proposed (sınır — asla confirmed donanım olmaz)
```

### §2.3 Fiziksellik Etiketleme Şablonu (her kayıt için)

```text
KAYIT ŞABLONU (26 alan — doldurulacak):
  [ 1] type_id                      : N-T__
  [ 2] name_tr                      : 
  [ 3] name_en                      : 
  [ 4] category                     : network
  [ 5] subcategory                  : ethernet | kablosuz | hucresel | nic-kart | endustriyel | sinir-kaydi
  [ 6] description                  : 
  [ 7] physical_component_or_device :  (fiziksel DEĞİLse: YOK — sınır kaydı)
  [ 8] primary_function             : 
  [ 9] interfaces                   : 
  [10] protocols                    :  (yalnız etiket — katman-4)
  [11] power_requirements           : 
  [12] operating_environment        : 
  [13] os_dependencies              :  (K000)
  [14] driver_requirements          :  (K000)
  [15] firmware_dependency          :  (K000)
  [16] hardware_abstraction_requirement : 
  [17] audio_relevance              : 
  [18] coremusic_relevance          : 
  [19] parent_domain                : K001-donanım
  [20] owner_domain                 :  (K001 envanter · K000 sürücü · K002 devre · UNKNOWN?)
  [21] existing_document            : 
  [22] evidence                     : 
  [23] status                       : proposed
  [24] confidence                   : low | medium | high
  [25] open_questions               : 
  [26] proposed_path                : donanaım-type/network/
```

## §3 Pin/Arayüz Çalışma Sayfaları (BOŞ — doldurulacak)

### §3.1 Arayüz/Pin Eşleme Sayfası (S5 · KATMAN-2)

| # | Kayıt | Arayüz | Pin | Yön (in/out/bidir) | Karşılık | Doğrulandı mı? |
|---|---|---|---|---|---|---|
| 1 | N-T01 | RJ45 / PHY ⚠️ | — | — | — | ❌ BOŞ |
| 2 | N-T02 | M.2 / PCIe / USB ⚠️ | — | — | — | ❌ BOŞ |
| 3 | N-T03 | USB / UART / SDIO ⚠️ | — | — | — | ❌ BOŞ |
| 4 | N-T04 | M.2 / PCIe / USB ⚠️ | — | — | — | ❌ BOŞ |
| 5 | N-T05 | PCIe / USB ⚠️ | — | — | — | ❌ BOŞ |
| 6 | N-T06 | MDI / MDI-X ⚠️ | — | — | — | ❌ BOŞ |
| 7 | N-T07 | SFP yuvası ⚠️ | — | — | — | ❌ BOŞ |
| 8 | N-T08 | CAN-H / CAN-L ⚠️ | — | — | — | ❌ BOŞ |
| 9 | N-T09 | TX / RX ⚠️ | — | — | — | ❌ BOŞ |
| 10 | N-T10 | — (donanım değil) | — | — | — | — (sınır) |

### §3.2 Elektriksel/RF Ölçüm Sayfası (S4 · KATMAN-3)

| # | Kayıt | Ölçüm | Hedef | Gerçek | Alet | Tarih |
|---|---|---|---|---|---|---|
| 1 | N-T01 | PHY gerilim | ⚠️ | — | — | — |
| 2 | N-T02 | RF/anten | ⚠️ | — | — | — |
| 3 | N-T04 | modem gerilim | ⚠️ | — | — | — |
| 4 | N-T06 | PHY empedans | ⚠️ | — | — | — |
| 5 | N-T08 | CAN-H/L sinyali | ⚠️ | — | — | — |
| 6 | N-T09 | TTL seviyesi | ⚠️ | — | — | — |

### §3.3 Envanter Çalışma Sayfası (S5)

| # | Kayıt | Marka | Model | MAC/Port | Tarih | Kaynak |
|---|---|---|---|---|---|---|
| 1 | N-T01 | UNKNOWN | UNKNOWN | — | — | — |
| 2 | N-T02 | UNKNOWN | UNKNOWN | — | — | — |
| 3 | N-T03 | UNKNOWN | UNKNOWN | — | — | — |
| 4 | N-T04 | UNKNOWN | UNKNOWN | — | — | — |
| 5 | N-T05 | UNKNOWN | UNKNOWN | — | — | — |
| 6 | N-T07 | UNKNOWN | UNKNOWN | — | — | — |
| 7 | N-T08 | UNKNOWN | UNKNOWN | — | — | — |
| 8 | N-T09 | UNKNOWN | UNKNOWN | — | — | — |

<!-- PART2 -->

## §4 NIC / Ethernet Senaryoları (§2 ayrımının derinleştirilmesi)

> Bu bölüm `network-kapsam.md` §4 (akış-fiziksel ayrımı) ve §5 (N-T01/N-T05/N-T06)
> kayıtlarının **uygulama derinliğidir**. Her satır **şablondur** — doldurulacak
> çalışmadır; **hiçbiri bu oturumda ölçülmüştür** (ölçüm = 0, §9).

### §4.1 NIC tür → fiziksellik karar matrisi (kapsam §3/1 testi)

| # | Öğe | Fiziksel mi? | Karar | Kanıt durumu |
|---|---|---|---|---|
| 1 | Anakart üstü entegre NIC (PHY + RJ45) | ✅ evet | bu dal (N-T01) | envanter UNKNOWN (Q-N1) |
| 2 | PCIe NIC kartı | ✅ evet | bu dal (N-T05) | envanter UNKNOWN |
| 3 | USB Ethernet adaptörü | ✅ evet | bu dal (N-T05) | envanter UNKNOWN |
| 4 | M.2 / kartüstü Wi-Fi adapter | ✅ evet | bu dal (N-T03) | envanter UNKNOWN |
| 5 | SFP / SFP+ modülü | ✅ evet | bu dal (N-T07) | ölçüm: `SFP` = **0** → envanter yok |
| 6 | Switch portu (altyapı) | ✅ evet ama **proje dışı** | sınır — proje envanteri değil | UNKNOWN (dış envanter) |
| 7 | VLAN etiketi (802.1Q) | ❌ hayır (yapılandırma) | **K000 / ağ altyapısı** | — |
| 8 | QoS kuyruğu / politika | ❌ hayır (yazılım) | **K000** | — |
| 9 | MTU değeri | ❌ hayır (parametre) | **K000** (donanım *kabiliyeti* bu dalda) | — |
| 10 | "Streaming erişimi" | ❌ hayır | sınır kaydı (N-T10) — asla confirmed değil | §2.1 (K-N2 RED) |

**Okuma kuralı**: 1–5 satırları **donanım envanteri** satırıdır (M-↔ kapı/N kapısı);
6–10 satırları **sınır kaydıdır** — bunlar bu dalda envanter olarak **sayılmaz**.

### §4.2 Duplex / half-duplex — doğru/yanlış kayıt (K-N2 provası)

```text
YANLIŞ KAYIT:
  type     : "Gigabit ethernet half-duplex bağlantı"
  category : network
  neden    : "half-duplex" BİR BAĞLANTI DURUMUDUR (anlık müzakere sonucu) —
             donanım tipi değildir; tip ile durum tek satırda birleşti.
  sonuç    : K-N2 RED — akış/durum, fiziksel tip olamaz.
```

```text
DOĞRU KAYITLAR (üç ayrı satır):
  [a] N-T01 NIC (fiziksel donanım — sabit):
        physical_component_or_device : PCIe/ anakart NIC + PHY
        status                       : proposed
  [b] Duplex durumu (anlık gözlem — ölçülebilir, tip DEĞİL):
        observed_state               : unknown (bu oturumda ölçüm YOK)
        measurement_ref              : §9 (PLANNED)
  [c] Otomatik müzakere kabiliyeti (donanımın yeteneği):
        capability                   : ⚠️ VERIFICATION REQUIRED (donanım dokümanı çekilmedi)
```

### §4.3 Duplex otomatik müzakere çalışma sayfası (BOŞ — doldurulacak)

| # | Port | Reklam edilen hız | Duplex (auto/100M/1G) | Sonuç durum | Kaynak | Tarih |
|---|---|---|---|---|---|---|
| 1 | UNKNOWN | — | — | ❌ BOŞ | — | — |
| 2 | UNKNOWN | — | — | ❌ BOŞ | — | — |
| 3 | UNKNOWN | — | — | ❌ BOŞ | — | — |

> **Kural (Truth Mode)**: "half-duplex kaldı" gibi bir cümle **yalnız** bu sayfada
> ölçülmüş satır varsa yazılabilir; boş sayfa = cümle **yasak** (S1 → UNKNOWN).

### §4.4 Duplex ile ilgili klasik hata kayıtları (şablon — Red Team)

| # | Hata kaydı | Neden yanlış | Doğru karşılık |
|---|---|---|---|
| H-D1 | "NIC half-duplex" tip olarak yazıldı | durum ≠ tip | N-T01 + §4.3 durum satırı |
| H-D2 | "1 Gbps" donanım kapasitesi sanıldı | reklam ≠ ölçülen | §9 ölçüm + §4.3 |
| H-D3 | Switch portu proje envanterine yazıldı | proje dışı altyapı | §4.1/6 sınır satırı |
| H-D4 | Kablo uzunluğu PHY performansına yazıldı | fizik ≠ ölçüm | §3.2 ölçüm sayfası |
| H-D5 | Duplex uyuşmazlığı "donanım arızası" ilan edildi | kanıtsız teşhis | ölçüm → M/N kapısı |

---

## §5 Wi-Fi Adapter — tip kaydı derinleştirmesi (N-T03)

### §5.1 Wi-Fi adapter için 26 alanın kritik 10 satırı (doldurulacak)

| # | Alan | Wi-Fi için beklenen içerik | Durum |
|---|---|---|---|
| 1 | `type_id` | N-T03 | ✅ şablon |
| 2 | `subcategory` | `kablosuz` | ✅ şablon |
| 3 | `physical_component_or_device` | M.2/PCIe/USB kablosuz adaptör (marka/model: **UNKNOWN**) | ❌ BOŞ |
| 4 | `interfaces` | M.2 / PCIe / USB ⚠️ | ❌ BOŞ |
| 5 | `protocols` | Wi-Fi nesli — **yalnız etiket** (katman-4) ⚠️ | etiket sınırı |
| 6 | `power_requirements` | ⚠️ VERIFICATION REQUIRED | ❌ BOŞ |
| 7 | `os_dependencies` | K000 (sürücü/tarama) | K000 |
| 8 | `driver_requirements` | K000 (§8) | K000 |
| 9 | `owner_domain` | K001 envanter · sürücü K000 | ✅ şablon |
| 10 | `status` | proposed | ✅ şablon |

### §5.2 RF / kanal çalışma sayfası (BOŞ — ölçüm yok)

| # | Öğe | Değer | Alet | Tarih |
|---|---|---|---|---|
| 1 | Bant (2.4 / 5 / 6 GHz) ⚠️ | — | — | — |
| 2 | Kanal no ⚠️ | — | — | — |
| 3 | SSID (gözlem) | — | — | — |
| 4 | Sinyal gücü (dBm) ⚠️ | — | — | — |
| 5 | Anten tipi (dahili/dış) | — | — | — |
| 6 | RF ölçüm cihazı | **kayıt yok** | — | — |

> **RF sınırı**: bu sayfadaki **hiçbir** hücre bu oturumda doldurulmadı;
> RF gerilim/güç iddiaları **ölçümsüzdür** → ⚠️ VERIFICATION REQUIRED.

### §5.3 Wi-Fi ile ölçüm gürültüsü (repo ölçümü — 2026-10-13)

| Terim | Eşleşme | Yorum |
|---|---|---|
| `Wi-Fi` | 4 | metin/doküman → **donanım envanteri DEĞİL** |
| `Bluetooth` | 29 | metin → envanter değil |
| `modem` · `transceiver` | 0 · 0 | kayıt yok |

---

## §6 VLAN / QoS — konfigürasyon mu donanım mı? (sınır kasası)

### §6.1 Sınır karar tablosu

| # | Öğe | Fiziksel? | Karar | Gerekçe |
|---|---|---|---|---|
| 1 | VLAN ID (802.1Q etiketi) | ❌ | K000 / ağ altyapısı | etiket = yapılandırma |
| 2 | VLAN'lı port (switch/NIC portu) | ⚠️ kısmi | port **donanım** → envanter; VLAN ayarı → K000 | ikili kayıt |
| 3 | QoS politikası (kuyruk/sınıflandırma) | ❌ | K000 | yazılım kuralı |
| 4 | QoS donanım kuyruk donanımı (NIC TX kuyruk) | ✅ | bu dal (donanım yeteneği) | çip içi blok |
| 5 | Trafik şekillendirme (shaping) | ❌ | K000 | algoritma |
| 6 | tracert / route tablosu | ❌ | K000 | OS çıktısı |
| 7 | Port mirroring (SPAN) | ❌ (altyapı) | dış altyapı — sınır | proje envanteri değil |
| 8 | MTU / jumbo frame ayarı | ❌ | K000 (kabiliyet bu dalda) | §7 |
| 9 | `ip`/`ifconfig` çıktısı | ❌ | K000 kanıtı | OS tespiti (K000) |
| 10 | Ethernet prizi / patch panel | ✅ | bu dal (altyapı envanteri) | fiziksel |

### §6.2 Doğru kayıt örneği (VLAN — sınır kaydı)

```text
DOĞRU KAYIT:
  [a] N-T01 NIC portu (fiziksel):
        physical_component_or_device : RJ45 portu (NIC parçası)
        observed_vlan                : — (gözlem yok)
        status                       : proposed
  [b] VLAN 100 (konfigürasyon — SINIR):
        physical_component_or_device : YOK
        owner_domain                 : K000 / ağ altyapısı
        status                       : proposed (sınır — asla donanım olmaz)
```

---

## §7 MTU — etiket kaydı + PLANNED ölçüm

### §7.1 MTU kayıt şablonu

| # | Alan | İçerik | Durum |
|---|---|---|---|
| 1 | `type_id` | N-T01 / N-T05 (MTU donanımın **değil**, arayüzün özelliğidir) | şablon |
| 2 | MTU kabiliyeti (min/max) | ⚠️ VERIFICATION REQUIRED | ❌ BOŞ |
| 3 | Aktif MTU | ❌ ölçülmedi | §7.2 |
| 4 | Jumbo frame (1500 üstü) iddiası | **yazılmaz** — ölçülmeden | yasak |
| 5 | Sahibi | ayar → K000 · donanım kabiliyeti → bu dal | sınır |

### §7.2 PLANNED: MTU keşif ölçümü (şablon — BU OTURUMDA ÇALIŞTIRILMADI)

```text
[PLANNED — çalıştırılmadı, çıktı yok]
  hedef   : <IP/alan adı — UNKNOWN>
  yöntem  : artan paket boyutu + fragment bayrağı (ICMP) ile keşif
  kayıt   : sonuç → §9.2 formuna (ölçüm = 0 bugün)
  kural   : ölçüm yapılmadan "MTU 1500" / "jumbo 9000" gibi sayı YAZILMAZ
```

---

## §8 Sürücü Bağlama (driver-binding) Senaryoları — K000 SINIRI

### §8.1 Senaryo tablosu (bu dal neyi kaydeder, neyi devreder)

| # | Senaryo | Bu dalın kaydı | Devredilen (K000) |
|---|---|---|---|
| 1 | NIC tak/çıkar → sürücü yüklenir | donanım **varlığı** (N-T05) | sürücü yüklemesi |
| 2 | Wi-Fi adapter sürücü sürümü | — (sürüm K000) | sürüm/ürün kimliği |
| 3 | `bind`/`unbind` (sürücü bağlama) | yok | tamamen K000 |
| 4 | Sürücü hatası / cihaz yönetici durumu | yok | K000 |
| 5 | USB NIC'in yeniden tanımlanması | fiziksel aygıt kaydı | yeniden tanıma K000 |
| 6 | PHY/MAC adresi okuma | donanım **özelliği** (envanter) | okuma aracı K000 |
| 7 | VLAN etiketleme sürücüsü | yok (yazılım) | K000 |
| 8 | Poweryield / Wake-on-LAN | donanım **kabiliyeti** ⚠️ | etkinleştirme K000 |
| 9 | Kanal/RF gücü seçimi | donanım **kabiliyeti** ⚠️ | seçim K000 |
| 10 | Firmware güncelleme | donanım yüzeyi (tetikleyici firmware) | süreç K000 |

### §8.2 Sınır özeti

| Soru | Cevap | Sahip |
|---|---|---|
| "Hangi sürücü bağlı?" | **K000 os-donanim-tespiti** — bu dalda soru bile sorulmaz | K000 |
| "Bu NIC var mı?" | envanter satırı → §3.3 / M-ekt | **bu dal** |
| "Sürücü sürümü nedir?" | `UNKNOWN` (bu dalda ölçülmedi) | K000 |
| "Sürücü bağlama nasıl yapılır?" | bu dosyada **yazılmaz** (kod üretim yasağı) | K000 |

---

## §9 PLANNED Ölçüm — ping / jitter / packet-loss [KANIT]

> **[KANIT] işareti**: bu bölümdeki **tüm** eşik adayları **kanıt bekliyor**
> (Vault Steward onayı + gerçek ölçüm). **Ölçüm = 0** (bu oturumda **hiçbir**
> ağ ölçümü yapılmadı). Eşikler **kaynak değildir**, **adaydır** →
> ⚠️ VERIFICATION REQUIRED.

### §9.1 Aday eşik tablosu [KANIT] — ⚠️ VERIFICATION REQUIRED

| # | Metrik | Aday eşik (kaynak DEĞİL) | İşaret | Ölçüm |
|---|---|---|---|---|
| 1 | Paket kaybı (packet-loss) | %0 hedefi ⚠️ | [KANIT] | **0** |
| 2 | Gecikme (ping RTT) — LAN | ≤1 ms adayı ⚠️ | [KANIT] | **0** |
| 3 | Gecikme (ping RTT) — WAN | ≤50 ms adayı ⚠️ | [KANIT] | **0** |
| 4 | Jitter (RTT değişkenliği) | ≤5 ms adayı ⚠️ | [KANIT] | **0** |
| 5 | Tekrar (loss burst) | 0 adayı ⚠️ | [KANIT] | **0** |
| 6 | Ses için ağ gecikme bütçesi | ⚠️ (K004 ses zinciriyle eşleştirilmeli) | [KANIT] | **0** |

**Kural**: bu tablodaki hiçbir sayı **rapora/ADR'ye kaynak olarak**
kullanılamaz; yalnız **ölçüm sonrası** (§9.2 dolduğunda) `S3`'e çıkar ve
`network-dogrulama.md` kapı kaydına işlenir.

### §9.2 Ölçüm kayıt formu (BOŞ — measurement = 0)

| # | Hedef | Metrik | Değer | Paket | Tarih | Alet |
|---|---|---|---|---|---|---|
| 1 | — | RTT (ms) | `MEASURE` | — | `YYYY-MM-DD` | — |
| 2 | — | jitter (ms) | `MEASURE` | — | `YYYY-MM-DD` | — |
| 3 | — | packet-loss (%) | `MEASURE` | — | `YYYY-MM-DD` | — |
| 4 | — | MTU keşif (bayt) | `MEASURE` | — | `YYYY-MM-DD` | — |
| 5 | — | duplex (auto/100M/1G) | `MEASURE` | — | `YYYY-MM-DD` | — |

### §9.3 Ölçüm protokolü (adım adım — çalıştırılmadı)

```text
[1] Ortam kaydı: OS · ağ arayüzü · saat · tekrar sayısı
  → [2] Temel RTT ölçümü (10 paket, medyan yaz)
  → [3] Jitter hesabı (RTT değişkenliği)
  → [4] Kayıp ölçümü (30+ paket, oran)
  → [5] MTU keşif (artan boyut — §7.2)
  → [6] Duplex/kanal durumu okuma (§4.3)
  → [7] Sonuçları §9.2 formuna yaz (uydurma YASAK)
  → [8] Eşiklerle karşılaştır (§9.1 adayları — [KANIT])
  → [9] Aşım varsa: yalnız OLAY kaydı — suçlu atfı YOK
  → [10] `network-dogrulama.md` kapı satırını güncelle (satır silme)
  → [11] Dürüstlük: ölçüm yapılmayan hücre `MEASURE` kalır
  → [12] 3 başarısız turda DUR + üst karar
```

---

## §10 URL / Kaynak Defteri (HEAD — 2026-10-13)

| # | URL | HTTP | Karar | Kullanım |
|---|---|---|---|---|
| 1 | `https://docs.kernel.org/` | **200** | KULLANILDI | Linux sürücü/altyapı dokümantasyonu — §8 sınırı (K000 sahipliği) |
| 2 | `https://kernel.org/doc/html/latest/` | **200** | KULLANILDI | çekirdek dokümantasyon kökü — §8/§6 |
| 3 | `https://uefi.org/` | **403** | **KULLANILMADI** | erişilemedi → bu dosyada **atıf yazılmaz** |

**Kural**: bu dosyada **yalnız** HTTP 200/202 olan URL kaynak yazılabilir;
403/404/timeout URL **hiçbir** satırda atıf olamaz (403 için: yalnız
"KULLANILMADI" kaydı). Yöntem: `Invoke-WebRequest -Method Head -UseBasicParsing
-Uri $u -TimeoutSec 20` · erişim tarihi **2026-10-13**.

---

## §11 İddia Kayıt Defteri (network-ornekler)

| # | İddia | Durum | Dayanak |
|---|---|---|---|
| N-OR01 | "Repo'da ağ donanımı kodu yok" | **ÖLÇÜLDÜ (S3)** | §1: cpp/h/c/ino/xc/cs/go/rs = 0 · py = 1 |
| N-OR02 | "NIC/Wi-Fi envanteri depoda yok" | **ÖLÇÜLDÜ (S3)** | §5.3 + §1 (metin = envanter değil) |
| N-OR03 | "Ağ ölçümü yapıldı" | **REDDEDİLDİ** | §9: ölçüm = 0 |
| N-OR04 | "Paket kaybı < %1" (benzeri eşik iddiası) | **YASAK** | §9.1 [KANIT] — ölçüm yok |
| N-OR05 | "VLAN donanım özelliğidir" | **REDDEDİLDİ** | §6.1: etiket = yapılandırma |
| N-OR06 | "half-duplex bir NIC tipidir" | **REDDEDİLDİ** | §4.2 (K-N2) |
| N-OR07 | "Sürücü bağlama bu dalda anlatılır" | **REDDEDİLDİ** | §8: K000 |
| N-OR08 | "jedec.org kaynak kullanılabilir" | **KULLANILMADI** | 403 (§10 benzeri HEAD kuralı) |
| N-OR09 | "docs.kernel.org erişilebilir" | **S5** | §10/1 — HEAD 200 · 2026-10-13 |

---

## §12 Kullanım Kuralı ve Dürüstlük

| Kural | Ayrıntı |
|---|---|
| Bu dosya üretim kodu değildir | yalnız şablon/çalışma sayfası — gerçek kod §1'de 0 |
| Uydurma ölçüm/marka/URL yazmaz | boş hücre `—` · bilinmeyen `UNKNOWN` · iddia `⚠️ VERIFICATION REQUIRED` |
| Ölçüm = 0 | §9 formları **boş** — hiçbir eşik kullanılamaz |
| Doldurma sahibi | Vault Steward ölçüm oturumu (kapılar `network-dogrulama.md` §2) |
| Authority | dosyanın **son satırında tek** Authority satırı |

### §12.1 Dürüstlük bloğu (QUANTIFIED)

- **Kod**: ağ donanımı kodu **0** (§1 ölçüm 2026-10-13).
- **Ölçüm**: ping/jitter/packet-loss/MTU **0** — §9 formları boş; eşikler [KANIT].
- **URL**: 2 × HTTP 200 (docs.kernel.org · kernel.org/doc/html/latest),
  1 × HTTP 403 (uefi.org) **kullanılmadı** · erişim 2026-10-13.
- **Sınır**: VLAN/QoS/MTU/sürücü → **K000**; NIC/Wi-Fi/SFP varlığı → bu dal (envanter UNKNOWN).
- **Yer tutucu**: `parent:` yok · yer tutucu deseni yok.

---

## §13 Çapraz Referanslar

| Konu | Dosya |
|---|---|
| Kapsam/sınır | `network-kapsam.md` §4–§5 |
| Kapılar/ölçüm | `network-dogrulama.md` §2 · §5 (ortak ölçüm defteri) |
| Dal indeksi | `network/index.md` |
| OS tespiti / sürücü | `K000-isletim-sistemi` (os-donanim-tespiti sahipliği) |
| Kardeş şablon | `cables/cables-ornekler.md` · `memory/memory-ornekler.md` |

## §14 Tip → Senaryo Eşleme Matrisi (N-T01…N-T10 — derinlik provası)

| # | Tip | İlgili § | Beklenen ölçüm | Durum |
|---|---|---|---|---|
| 1 | N-T01 Ethernet NIC | §4 · §4.3 · §9.2 | duplex + RTT | ❌ BOŞ |
| 2 | N-T02 PCIe/USB ağ kartı | §4.1 · §8 | varlık okuması | ❌ BOŞ |
| 3 | N-T03 Wi-Fi adapter | §5 · §5.2 | RF/kanal | ❌ BOŞ |
| 4 | N-T04 hücresel modem | §4.1 · §8 | envanter | ❌ BOŞ |
| 5 | N-T05 NIC kart (genel) | §4.2 · §8.1 | sürücü sınırı | ❌ BOŞ |
| 6 | N-T06 PHY/MDI-X | §4.3 | müzakere | ❌ BOŞ |
| 7 | N-T07 SFP/SFP+ | §4.1/5 | envanter (SFP=0) | ❌ BOŞ |
| 8 | N-T08 CAN (endüstriyel) | §9 (paket/metrik) | ölçüm yok | ❌ BOŞ |
| 9 | N-T09 seri/USB-serial | §9.2 | ölçüm yok | ❌ BOŞ |
| 10 | N-T10 akış yüzeyi (sınır) | §2.1 · §6 | — (donanım değil) | sınır |

### §14.1 Doldurulmuş örnek kayıt provası (N-T01 — yalnız şablon değerler)

| # | Alan | Değer |
|---|---|---|
| 1 | `type_id` | N-T01 |
| 2 | `name_tr` | Ethernet NIC (ağa bağlanma arayüzü) |
| 3 | `name_en` | ethernet network interface card |
| 4 | `category` | network |
| 5 | `subcategory` | `nic-kart` |
| 6 | `physical_component_or_device` | PCIe/anakart NIC + PHY + RJ45 (marka/model: **UNKNOWN**) |
| 7 | `primary_function` | fiziksel katman-1/2 erişimi |
| 8 | `interfaces` | RJ45 / PCIe / USB ⚠️ |
| 9 | `protocols` | Ethernet/IP — **yalnız etiket** (katman-4) |
| 10 | `power_requirements` | ⚠️ VERIFICATION REQUIRED |
| 11 | `os_dependencies` | K000 (§8.2) |
| 12 | `driver_requirements` | K000 (§8) |
| 13 | `firmware_dependency` | K000 (NIC firmware yüzeyi) |
| 14 | `owner_domain` | K001 envanter · sürücü K000 |
| 15 | `evidence` | repo: NIC=5 · Ethernet=2 (metin — envanter değil) |
| 16 | `status` | proposed |
| 17 | `confidence` | low |
| 18 | `open_questions` | Q-N9 (kod yüzeyi) · envanter sorusu |
| 19 | `proposed_path` | `donanaım-type/network/` |

> **Not**: 26 alanın kalanı `network-kapsam.md` §5/§2.3 şablonundan doldurulur;
> **hiçbir** hücre bu oturumda gerçek envanter verisiyle **doldurulmadı**.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
