---
id: electroncis/guc-ve-koruma/guc-ve-koruma-anayasa
title: "Güç ve Koruma Anayasası"
node_type: module
parent_id: electroncis/guc-ve-koruma
level: 4
group: mimari
status: draft
updated: 2026-10-13
---

# guc-ve-koruma-anayasa

> `electroncis/guc-ve-koruma` alt-kapsamının anayasası: güç mimarisi (AC→DC→regulator), koruma katmanları, sıralama (inrush/soft-start), verimlilik ve komşu sınırlar (K001 termal, K005 firmware). Scope Q1 (`⚠️ VERIFICATION REQUIRED`) geçerli; sartname hedefleri (SNR >100 dB, THD+N <0.01% @1 kHz) PLANNED ve `⚠️ VERIFICATION REQUIRED`. Repo'da devre seviyesi kod YOK — kaynak: .ai/electronic/** (60 dosya).

## §0 · Kapsam

| Konu | Bu modül | Komşu |
|---|---|---|
| Güç mimarisi şeması | evet | — |
| Koruma katmanları | evet | — |
| Sıralama / soft-start | evet | — |
| Verimlilik | evet | — |
| Topoloji seçimi ayrıntısı | sınır | guc-kaynaklari-ve-regulatorler |
| Eşik hesabı ayrıntısı | sınır | koruma-devreleri-ve-esikler |
| Termal soğutma | sınır | K001 |
| Firmware koruma eşiği | sınır | K005 |

## §1 · Güç Mimarisi (AC→DC→Regulator) — Katmanlar

| Katman | İş | Tipik eleman | Örnek |
|---|---|---|---|
| AC giriş | Şebekeden ham AC | priz, sigorta, EMI filtresi | 220 V AC |
| EMC filtresi | Giriş emisyonu bastırma | X/Y kapasitör, common-mode choke | — |
| Transformatör / izolasyon | İndirme + izolasyon | toroidal / PCB trafosu | 230→24 V AC |
| Rectifier | AC→DC | köprü diyot (schottky/FAST) | — |
| Bulk kapasitör | Ripple deposu | elektrolitik | C_bulk |
| Ön regulator (ham DC) | Yüksek→düşük | anahtarlama (buck/boost) | 24→12 V |
| Lineer regulator (temiz DC) | Son regülasyon | LDO / lineer | 12→5 V, 3.3 V |
| Dağıtım | Rail'ler | PCB iz, star-ground | +V, -V, AVDD, DVDD |
| Koruma rölesi | DC offset çıkış kesme | röle + kontrol | >0.5 V kesme |

## §1b · Rail Mimarisi (Ses Cihazı — Örnek, Ölçülmemiş)

| Rail | Görev | Tipik regülatör | Hassasiyet |
|---|---|---|---|
| AVDD (analog +) | DAC/amp analog | LDO (temiz) | yüksek gürültü |
| AVSS (analog −) | negatif analog | inverting / −V | yüksek |
| DVDD (dijital) | MCU/logic | anahtarlama | orta |
| VREF | referans | izole / LDO | kritik |
| HP_OUT | kulaklık güç | lineer | yüksek |
| Relay_V | röle bobin | ayrı | orta |

## §1c · AC→DC Geçiş Gerekçeleri

| Gerekçe | Açıklama |
|---|---|
| İzolasyon | kullanıcı emniyeti (Class II) |
| Seviye indirme | 220→güvenli DC |
| EMC | giriş emisyonu sınırlama |
| Ripple azaltma | bulk + regüle |
| Rail ayrımı | analog/dijital ayrımı gürültü için |

## §2 · Koruma Katmanları

| Katman | Tehdit | Koruma | Hız |
|---|---|---|---|
| Giriş sigorta | Aşırı akım / kısa devre | sigorta / PTC | anlık |
| EMI filtresi | Gürültü / emisyon | LC / common-mode | sürekli |
| Akım sınırı | Kısa devre / yük | eFuse / limit | hızlı |
| Gerilim sınırı | Aşırı gerilim / tepe | TVS / AKŞ (crowbar) | hızlı |
| Termal koruma | Aşırı ısınma | termal kapanma (OTP) | orta |
| ESD koruma | ESD deşarj | TVS diodo / ESD array | çok hızlı |
| DC offset rölesi | Hoparlör hasarı | DC algılama + röle | yavaş (ms) |
| Yumuşak başlat (soft-start) | İnrush akımı | rampa kontrol | açılış |

## §2b · ESD / Surge / Transient Koruması (Giriş-Arayüz)

| Olay | Standart atfı | Koruma elemanı | Durum |
|---|---|---|---|
| ESD deşarj | IEEE 61000-4-2 | TVS / ESD array | atıf `⚠️ VERIFICATION REQUIRED` |
| EFT / surge | IEC (yayın 2724) | MOV / TVS / filtre | atıf `⚠️ VERIFICATION REQUIRED` |
| Dalgıç (surge) | IEEE 1547 (ilgili) | arrester / TVS | atıf `⚠️ VERIFICATION REQUIRED` |
| EMI / iletken gürültü | — | LC filtre | — |

## §3 · Sıralama (Inrush / Soft-Start)

| Sıralama | Amaç | Yöntem |
|---|---|---|
| 1 | Girişi sınırla | inrush NTC / pre-charge |
| 2 | Bulk'ı yavaş doldur | soft-start / pre-charge direnç |
| 3 | Rail'leri kademeli aç | sıralı enable |
| 4 | Koruma eşiklerini hazırlama | comparator ready |
| 5 | Röleyi geç bağla | DC stabil → röle |
| 6 | Yük aç | rampa / step |

## §3b · Soft-Start Sırası Adım Adım

| Adım | Olay | Eşik/durum | Sonuç |
|---|---|---|---|
| A | Güç gelir | VIN hazır | inrush sınır |
| B | Bulk doldu | bulk ≥ eşik | DC rail hazır |
| C | Enable | soft-start rampa | rail yukarı |
| D | Rail stabil | DC within tol | — |
| E | DC algılama hazır | offset <0.5 V | röle KAPALI |
| F | Röle kapat | hoparlör bağlı | ses çıkışı |
| G | Anormallik | offset/akım/ısı | röle AÇIK |

## §4 · Verimlilik

| Topoloji | Tipik verim | Gürültü | Kullanım |
|---|---|---|---|
| Lineer (LDO) | düşük-orta (%30–70) | çok düşük | temiz rail |
| Buck | yüksek (%80–95) | orta | ön regulator |
| Boost | yüksek | orta | yüksek rail |
| Flyback | orta-yüksek | yüksek | izole |
| Charge-pump | orta | düşük-orta | küçük yük |

## §4b · Verimlilik–Gürültü Trade-Off

| Seçim | Kazanım | Kayıp |
|---|---|---|
| Anahtarlama ön-regülatör | verim | ripple/EMI |
| LDO son-regülatör | temiz | ısınma |
| Daha yüksek anahtarlama frekansı | küçük L/C | EMI |
| Daha düşük anahtarlama frekansı | düşük EMI | büyük L/C |

## §5 · Komşu Sınırı (Farklılaştırma)

| Konu | Komşu | Ayrım |
|---|---|---|
| Soğutma/termal tasarım | K001 | burada koruma eşiği; orada soğutma |
| Firmware koruma eşiği | K005 | burada donanım; orada yazılım eşiği |
| Devre topoloji tasarımı | devre-tasarimi | orada sim; burada güç-koruma mimari |
| Simülasyon doğrulama | devre-tasarimi | orada sim-ölçüm |

## §5b · Kapsam Dışı (Bu Modülde YOK)

| Konu | Neden-yok | Gidilen yer |
|---|---|---|
| Soğutma soğutucu hesabı | K001 | K001 |
| Firmware koruma eşik kodu | K005 (repo'da yok) | K005 (UNKNOWN) |
| Simülasyon netlist | devre-tasarimi | devre-tasarimi |
| DSP algoritması | Neva/DSP | embedded-engineer |

## §6 · Terimler (Power/Protection)

| Terim | Anlam |
|---|---|
| LDO | Lineer düşük-damlama regülatör |
| Buck | Düşürücü anahtarlama topolojisi |
| Boost | Yükseltici topoloji |
| Buck-boost | Her iki yöne |
| Flyback | İzole anahtarlama |
| eFuse | Elektronik sigorta (akım limiti) |
| AKŞ (crowbar) | Aşırı-gerilim kısa-devre kesici |
| TVS | Tepe-bastırma diyodu |
| Inrush | Açılış ani akımı |
| Soft-start | Yumuşak açılış rampası |
| Ripple | DC üzerinde AC dalgalanma |
| PSRR | Güç kaynak redd oranı |
| OTP | Aşırı-ısı kapanması |
| Hot-swap | Sıcak takma koruması |

## §1d · Güç Bütçesi (Başlangıç — Ölçülmemiş, Örnek)

> Rail bazlı yük tahmini; gerçek değerler ölçümle (`⚠️ VERIFICATION REQUIRED`).

| Rail | Gerilim | Tahmini yük | Güç | Koruma |
|---|---|---|---|---|
| AVDD | +V (UNKNOWN) | UNKNOWN | UNKNOWN | LDO + akım sınır |
| AVSS | −V (UNKNOWN) | UNKNOWN | UNKNOWN | inverting + sınır |
| DVDD | 3.3 V | düşük | düşük | buck + LDO |
| VREF | referans | çok düşük | çok düşük | izole LDO |
| HP_OUT | ±/tek | orta | orta | lineer + röle |
| Relay_V | bobin | düşük-pik | pik | ayrı sigorta |

## §1e · AC Giriş / EMC Ayrıntıları

| Bileşen | İş | Not |
|---|---|---|
| Sigorta | Aşırı akım | hızlı eriyen |
| X kapasitör | diferansiyel gürültü | L-N |
| Y kapasitör | common-mode gürültü | L/N-GND |
| Common-mode choke | CM bastırma | — |
| MOV | Surge | arrester |
| Köprü diyot | Rectifier | FAST/Schottky |
| Bulk kapasitör | Ripple deposu | elektrolitik |
| Bleeder direnç | Boşaltma | emniyet |

## §1f · Rail Ayrımı — Gürültü Mantığı

| Ayrım | Neden |
|---|---|
| Analog / dijital ayrı rail | dijital gürültü izolasyonu |
| LDO son katman | ripple/EMI azaltma |
| Yıldız (star) toprak | ortak-impedans gürültüsü önleme |
| Ayrı güç izi | yüksek akım ayrımı |
| VREF temiz LDO | referans gürültüsü → SNR |

## §2c · Koruma Elemanı Seçim Matrisi

| Koruma | Eleman | Hız | Eşik | Durum |
|---|---|---|---|---|
| Kısa devre | eFuse / sigorta | hızlı | akım limit | — |
| Aşırı gerilim | TVS | hızlı | Vrwm | — |
| Crowbar (AKŞ) | SCR + TVS | hızlı | aşırı V | — |
| Aşırı ısınma | termistör + OTP | orta | sıcaklık | — |
| ESD | ESD array / TVS | çok hızlı | ±kV | IEEE 61000-4-2 |
| İnrush | NTC / pre-charge | yavaş | akım | soft-start |
| DC offset | algılama + röle | ms | 0.5 V | kural |

## §2d · Koruma Senaryoları (Ne Zaman Kesen)

| Senaryo | Algılama | Aksiyon | Hız |
|---|---|---|---|
| Çıkış kısa devre | akım > limit | eFuse kesme | hızlı |
| Hoparlör DC offset | DC >0.5 V | röle aç | ms |
| Aşırı ısınma | sıcaklık > OTP | kapanma | orta |
| ESD deşarj | tepe | TVS toprağa | çok hızlı |
| Giriş surge | tepe | MOV/TVS | hızlı |
| Açılış inrush | akım | soft-start | açılış |

## §2e · DC-Offset / Röle Koruma Mantığı

| Adım | İş | Eşik |
|---|---|---|
| 1 | Çıkış DC ölç | her örnek/blok |
| 2 | |DC| hesap | >0.5 V kontrol |
| 3 | Eşik aşımı | röle komut |
| 4 | Röle açma | hoparlör ayır |
| 5 | Normale dön | gecikme sonra kapat |

## §3c · İnrush / Soft-Start Mantığı

| Kavram | Açıklama | Eşik |
|---|---|---|
| İnrush | Açılış ani akımı | sınırlı |
| Pre-charge | Bulk'ı yavaş doldur | direnç/NTC |
| Soft-start rampa | Rail yukarı yavaş | süre |
| Kademeli enable | Rail sırası | sıralı |
| Röle gecikme | Sonra bağla | stabil sonrası |

## §4c · Verim / Kayıp Mantığı

| Kayıp | Kaynak | Azaltma |
|---|---|---|
| Anahtarlama kaybı | MOSFET geçiş | doğru boyut |
| İletim kaybı | Rds(on) | düşük Rds |
| İndüktör kaybı | DCR + çekirdek | kaliteli L |
| Kapasitör kaybı | ESR | düşük ESR |
| Lineer kayıp | (VIN−VOUT)·I | LDO gürültü-öncelik |

## §4d · Verim-Ölçüm Mantığı

| Ölçüm | Yöntem |
|---|---|
| Giriş gücü | VIN·IIN |
| Çıkış gücü | VOUT·IOUT |
| Verim | POUT/PIN |
| Isı kaybı | (PIN−POUT) |
| Termal | ΔT ölçümü |

## §5c · Bağımlılık Matrisi

| Bağımlılık | Yön | Etki |
|---|---|---|
| Termal (K001) | dış | soğutma sınırları |
| Firmware (K005) | dış | yazılım eşiği |
| Devre-tasarimi | iç-eş | topoloji |
| Koruma-esik | iç | eşik hesabı |
| Regulatorler | iç | seçim |

## §6b · Terimler — Devam

| Terim | Anlam |
|---|---|
| PSRR | Güç reddi (dB) |
| Quiescent | Boşta akım |
| DCR | Direnç (indüktör) |
| ESR | Eşdeğer seri direnç (kapasitör) |
| Creepage / clearance | İzolasyon boşluğu |
| Class II | Çift izolasyon |
| Röle contact | Kontak direnci |

## §2f · ESD/TVS Koruma — Ayrıntı Matrisi

| Konu | Değer/Not | Durum |
|---|---|---|
| ESD kategorisi | insan/model (HBM) / machines | `⚠️ VERIFICATION REQUIRED` |
| TVS çalışma gerilimi | Vrwm rail'e uygun | tasarım |
| TVS kapasitör | düşük-kap (sinyal) | tasarım |
| Düzlem / sıralı yerleşim | ESD akım yolu | PCB |
| Topraklama | kısa ESD yolu | PCB |
| Test standardı | IEEE 61000-4-2 | atıf `⚠️ VERIFICATION REQUIRED` |

## §2g · Surge/EFT — Filtre Ayrıntı

| Konu | Not | Durum |
|---|---|---|
| Surge enerjisi | MOV boyutu | `⚠️ VERIFICATION REQUIRED` |
| Common-mode | Y-kap + choke | tasarım |
| Diferansiyel | X-kap | tasarım |
| Filtre sonrası | TVS son katman | tasarım |
| Test | IEC (yayın 2724) | atıf `⚠️ VERIFICATION REQUIRED` |

## §3d · Soft-Start Süre / Akım Mantığı

| Parametre | Etki | Not |
|---|---|---|
| Soft-start süresi | İnrush ↓ | yavaş açılış |
| Pre-charge direnç | Bulk yavaş | güç kaybı |
| NTC soğuk direnç | yüksek soğuk | ısınca ↓ |
| Enable gecikme | Sıra | rail seq |
| Röle gecikme | DC stabil sonra | güvenli |

## §4e · Verim vs Gürültü — Karar Matrisi

| Öncelik | Seçim | Gerekçe |
|---|---|---|
| Gürültü kritik | LDO (son) | temiz rail |
| Verim kritik | Buck/boost | verimli |
| İkisi | Buck ön + LDO son | dengeli |
| Düşük EMI | Düşük fs / LC | EMI |
| İzole | Flyback | güvenlik |

## §5d · Kapsam-Dışı — Devam

| Konu | Gidilen |
|---|---|
| Termal soğutucu hesabı | K001 |
| Soğutma hava akışı | K001 |
| Firmware OTP kodu | K005 |
| Watchdog | K005 |
| Yazılım eşik log | K005 |

## §6c · Ölçü / Birim Tablosu

| Büyüklük | Birim | Not |
|---|---|---|
| Gerilim | V | rail |
| Akım | A | yük / limit |
| Güç | W | bütçe |
| Sıcaklık | °C | termal |
| Frekans | kHz | fs |
| Ripple | mV | temizlik |
| PSRR | dB | redd |

## §1g · Güç Topoloji Seçim Kararı (Başlangıç)

| Girdi→Çıktı | Topoloji | Gerekçe |
|---|---|---|
| Yüksek→düşük, verimli | Buck | verim |
| Düşük→yüksek | Boost | yükseltme |
| İki yöne | Buck-boost | esnek |
| İzole | Flyback | güvenlik |
| Temiz son | LDO | gürültü |

## §2h · Koruma — Karar Özet Matrisi

| Tehdit | Birincil koruma | İkincil |
|---|---|---|
| Kısa devre | eFuse | sigorta |
| Aşırı gerilim | TVS | crowbar |
| Aşırı ısınma | OTP | termal röle |
| ESD | ESD array | TVS |
| İnrush | NTC/soft-start | pre-charge |
| DC offset | röle | algılama |

## §1h · Rail Sıralama (Power Sequencing) Tablosu

| Sıra | Rail | Beklenen | Durum |
|---|---|---|---|
| 1 | Ham DC | VIN stabil | — |
| 2 | DVDD | logic hazır | — |
| 3 | AVDD/AVSS | analog hazır | — |
| 4 | VREF | referans stabil | — |
| 5 | HP_OUT | çıkış hazır | — |
| 6 | Röle | DC algılamadan sonra | kural |

## §1i · Gürültü-Bütçe Rail İlişkisi

| Rail | Gürültü etkisi | SNR katkısı |
|---|---|---|
| AVDD | doğrudan | yüksek |
| VREF | doğrudan | kritik |
| DVDD | sınırlı (izole) | orta |
| HP_OUT | hoparlör | yüksek |

## §2i · Eşik Matrisi (Örnek — Hepsi `⚠️ VERIFICATION REQUIRED`)

| Koruma | Eşik | Not |
|---|---|---|
| DC offset | 0.5 V | kural |
| Akım limit | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |
| Gerilim sınır | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |
| OTP | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |
| ESD | IEEE 61000-4-2 | atıf `⚠️ VERIFICATION REQUIRED` |

## §5e · Komşu Sınır — Özet

| Konu | Burada | Komşuda |
|---|---|---|
| Termal | koruma eşiği | K001 soğutma |
| Firmware | donanım eşiği | K005 yazılım |
| Sim | — | devre-tasarimi |
| Topoloji | mimari | regulatorler |

## §8b · Açık Sorular — Devam

| Soru | Durum |
|---|---|
| İzolasyon sınıfı (Class II mi) | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| Giriş AC aralığı (220 V mu) | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| Röle tipi/kontak akımı | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| ESD test seviyesi | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| Surge enerji sınıfı | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |

## §4f · Verimlilik-Ölçüm Kabul Matrisi

| Parametre | Kabul | Durum |
|---|---|---|
| Buck verim | hedef (UNKNOWN) | `⚠️ VERIFICATION REQUIRED` |
| LDO dropout | datasheet | `⚠️ VERIFICATION REQUIRED` |
| Isı kaybı | termal ΔT | `⚠️ VERIFICATION REQUIRED` |
| Quiescent | düşük | `⚠️ VERIFICATION REQUIRED` |

## §3e · Sıralama-Senaryo Tablosu

| Senaryo | Sıra sonucu |
|---|---|
| Normal açılış | soft-start → röle |
| Ani güç kesme | röle aç → boşaltma |
| Yeniden başlat | yeniden soft-start |
| Anomali | koruma → röle aç |

## §6d · Terim — Ek

| Terim | Anlam |
|---|---|
| Pre-charge | Ön şarj |
| Sequencing | Rail sıralama |
| Brown-out | Düşük gerilim |
| Derating | Güç düşürme |

## §2j · Koruma Hız Sıralaması

| Koruma | Hız | Öncelik |
|---|---|---|
| ESD/TVS | çok hızlı | ilk |
| Akım (eFuse) | hızlı | ikinci |
| Crowbar | hızlı | ikinci |
| Termal (OTP) | orta | üçüncü |
| DC röle | yavaş (ms) | son |

## §1j · Rail-Güç Tablosu (Örnek)

| Rail | Tipik güç | Öncelik |
|---|---|---|
| AVDD | düşük | yüksek |
| DVDD | düşük | orta |
| HP_OUT | orta-pik | yüksek |
| Relay_V | pik | orta |

## §0b · Kapsam Özet

| Var | YOK |
|---|---|
| Güç mimarisi | Soğutma (K001) |
| Koruma katmanları | Firmware (K005) |
| Verimlilik | DSP (embedded) |

## §7 · Bilgi İddia Matrisi (Web Araştırma · 2026-10-13)

> Bu satırlar gerçek ve envanter-dışıdır (2.102 URL envanteriyle kesişim 0). Atıf/spec tek tek HTTP 200 ile "varlık" desteklenir; özellik `⚠️ VERIFICATION REQUIRED`.

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| TPS5430 buck converter ürün sayfası | https://www.ti.com/product/TPS5430 | 2026-10-13 | HTTP 200 doğrulandı · envanter-dışı · özellik `⚠️ VERIFICATION REQUIRED` |
| LM5164 buck ürün sayfası | https://www.ti.com/product/LM5164 | 2026-10-13 | HTTP 200 doğrulandı · envanter-dışı · özellik `⚠️ VERIFICATION REQUIRED` |
| SLAA868 uygulama notu (literatür-no) | https://www.ti.com/lit/an/slaa868/slaa868.pdf | 2026-10-13 | HTTP 200 doğrulandı · envanter-dışı · başlık `⚠️ VERIFICATION REQUIRED` |
| IEEE 1547 (ilgili standart) | https://standards.ieee.org/ieee/1547/4407/ | 2026-10-13 | HTTP 200 doğrulandı · envanter-dışı · atıf `⚠️ VERIFICATION REQUIRED` |

## §8 · Açık Sorular (CEVAP BEKLİYOR)

| Soru | Neden açık | Durum |
|---|---|---|
| Ana güç topolojisi (buck-boost mu) | netleşmedi | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| DC-DC giriş aralığı | belirsiz | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| Rail gerilimleri (±V, AVDD/DVDD) | sartname PLANNED | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| Koruma eşiği kesin (akım/gerilim/ısı) | netleşmedi | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| Termal tasarım (K001) bağlı mı | bağımlılık | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| Firmware koruma eşiği (K005) | repo'da yok | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| Sartname SNR/THD hedefi (ölçülmemiş) | PLANNED | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |

## §9 · Authority · Kapanış

- Bu modül `electroncis/guc-ve-koruma` yetkisi altındadır; scope Q1 (`⚠️ VERIFICATION REQUIRED`) nedeniyle `status: draft`.
- Sartname hedefleri (SNR >100 dB, THD+N <0.01% @1 kHz, 48 kHz, PCM3168A) PLANNED; koruma rölesi (DC offset >0.5 V) kural olarak kayıtlı; hiçbiri ölçülmüş `verified` değildir.
- Dış iddialar (§7) tek tek HTTP 200 doğrulanmış, envanter-dışıdır; özellik/atıf `⚠️ VERIFICATION REQUIRED` işaretlidir.
- Bu dosyanın yazımı ve doğrulanması: bu görev oturumunda (2026-10-13).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
