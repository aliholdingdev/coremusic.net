---
title: power — Kapsam (Çatışma Kaydı, Sınır, P-T01…P-T13 Tip Kayıtları)
type: mimari-kapsam
node_id: K001-donanım/donanaım-type/power/power-kapsam
node_type: architecture-scope
status: proposed
parent_id: K001-donanım/donanaım-type
children:
  - "§0 çatışma kaydı (anayasa §2 vs bu klasör)"
  - "§sınır K002-electroncis + ADR-063/064"
  - "§6 P-T01…P-T13 (26 alan şema)"
depends_on: K001-donanım
evidence:
  - "K001-donanım/donanım-anayasa.md §2 'ekran/kablo/güç kaynağı bağlamı AÇILMAZ' + §10/3 + §12/C10 — okundu"
  - "K001-donanım/donanaım-type/donanaım-type-kapsam.md §2/A6, §7/3-T1, §12/M6 — okundu"
  - "kendi repo sayımım (2026-10-13): 700 kaynak dosyada PSU=0 VRM=0 LDO=0 BMS=0 fuse/sigorta=0 power supply=0"
  - ".ai/.decisions/accepted/ADR-063-hardware-design-standards.md, ADR-064-electronics-platform-architecture.md — varlık (16 md K002-electroncis ile birlikte)"
validation: "⚠️ VERIFICATION REQUIRED — çatışma kaydı yazıldı; anayasa/kapsam metni revizesi ONAY BEKLİYOR; S1 ölçüm = 0"
open_questions:
  - "Q-T2: yanıtlandı (power/ açıldı) ama KAPANDI DEĞİL"
  - "VD-02: power tipsiz mi — 700 dosyada 0 eşleşme"
  - "P-T01…P-T13 kayıtlarında power_requirements alanlarının çoğu NOT PROVIDED — ölçüm/inceleme gerekir"
date: 2026-10-13
updated: 2026-10-13
group: mimari
---

# power — Kapsam

> **Kapsam**: power/ alt tipinin kapsamı, anayasa ile çatışma kaydı, K002 sınırı ve
> 13 tip kaydı (P-T01…P-T13, 26 alan şema).
> **Geri çekilme taahhüdü**: bu dosya, anayasa §2 ile açıkça çeliştiği yerde kendi durumunu
> `proposed` + `PROPOSED — APPROVAL REQUIRED` etiketiyle işaretler; metin revizesi **bu dosyada
> yapılmaz** (anayasa/kapsam dokunulmaz, onay Vault Steward'ın).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

## §0 Çatışma kaydı (BU DOSYANIN ZORUNLU KAYDI)

> Aynı tablo `display/display-kapsam.md §0`'da da vardır (ikiz kayıt). Bu klasörün açılması ile
> anayasa metni arasındaki gerilim **gizlenmez, tabloya bağlanır.**

| Kaynak | Bölüm | İddia (metin özeti) | power/ ile gerilim | Durum |
|---|---|---|---|---|
| `donanım-anayasa.md` | §2 | "yalnız 3 bağlam (hdd·audio·ssd)" | power 4. bağlam olur | gerilim KAYITLI |
| `donanım-anayasa.md` | §2 | "ekran/kablo/**güç kaynağı** bağlamı AÇILMAZ" | power klasörü açıldı | gerilim KAYITLI |
| `donanaım-type-kapsam.md` | §2/A6 | tip kapsamı 3 bağlamla sınırlı | +power | gerilim KAYITLI |
| `donanaım-type-kapsam.md` | §7/3-T1 | 3-T1 oturum notu (kapsam genişletme yok) | +power | gerilim KAYITLI |
| `donanaım-type-kapsam.md` | §12/M6 | M6 açık madde (kapsam değişikliği) | +power | gerilim KAYITLI |

**Çözüm (kayıt anı 2026-10-13):**

| Adım | Ne | Sahibi | Status |
|---|---|---|---|
| 1 | power/ klasörünün açılması (8 dosya) | Vault Steward onayı, 2026-10-13 | **TAMAM** |
| 2 | Çatışma kaydının iki -kapsam dosyasına yazılması | bu görev | **TAMAM** |
| 3 | display/ + power/ index satırlarının iki dizine eklenmesi | bu görev | **TAMAM** |
| 4 | anayasa §2/§10 + donanaım-type-kapsam §2/A6, §7/3-T1, §12/M6 **metin revizesi** | Vault Steward onayı | **APPROVAL REQUIRED — YAPILMADI** |
| 5 | Q-T2'nin KAPANDI sayılması | Vault Steward | **AÇIK** — yanıtlandı ≠ kapandı |

> **Etiket**: `PROPOSED — APPROVAL REQUIRED` · `status: proposed` · adımlar 1–3 tamam, 4–5 onay bekliyor.
> Anayasa metni bu dosyada **değiştirilmedi** (dokunulmaz yüzey).

---

## §1 power ne DEĞİL (kapsam dışı — disiplin ayrımı)

| # | Değil | Nereye | Kaynak |
|---|---|---|---|
| 1 | Devre tasarımı (topoloji, hesap) | K002-electroncis | ADR-063/064 (varlık) |
| 2 | PCB tasarımı / BOM | K002-electroncis | anayasa §2 "Fiziksel PCB / BOM → HAYIR" |
| 3 | bileşen seçimi / datasheet hesabı | K002-electroncis | ADR-063 |
| 4 | yazılım kontrolü (PWM/ADC kodu) | K003-yazılım | katman ayrımı |
| 5 | ısıl tasarımın hesabı (CFD/thermal sim) | K002-electronsis (tasarım) | sınır notu §sınır |
| 6 | tip/enventar sınıflandırma (envanter, arayüz, bağlam) | **K001 power (BU KLASÖR)** | bu dosya §6 |

**power/ yalnız şudur**: güç bileşen/tiplerinin **sınıflandırılması, arayüz ve bağlamının
kaydı, koruma özelliklerinin enventarlanması** — tasarım/değil, ölçüm/ölçüm kapıları ayrı.

---

## §2 Kapsam içi (BU KLASÖR)

| # | İçerik | Tip kaydı |
|---|---|---|
| 1 | ATX PSU, DC adaptör, AC/DC dönüştürücü | P-T01, P-T02, P-T03 |
| 2 | DC/DC, LDO, VRM | P-T04, P-T05, P-T06 |
| 3 | batarya, BMS | P-T07, P-T08 |
| 4 | güç dağıtımı, sigorta, aşırı akım/aşırı gerilim | P-T09, P-T10, P-T11 |
| 5 | ESD koruma, ısıl koruma | P-T12, P-T13 |

> 13 kaydın tamamı §6'da 26 alan şemayla; bilinmeyen alan `UNKNOWN` / `NOT PROVIDED` /
> `VERIFY REQUIRED` yazımına uygundur; hiçbir kayıtta uydurma voltaj/akım/ölçüm yoktur.

---

## §3 Terminoloji (kısa, bağlamsal değil)

| Terim | Bu dosyadaki anlamı |
|---|---|
| güç kaynağı | enerji sağlayan bileşen/cihaz (ağ AC'den veya bataryadan) |
| dönüştürücü | AC/DC ya da DC/DC çevrim yapan modül/modül sınıfı |
| regülatör | sabitleyen aşama (LDO = düşük gürültülü doğrusal; VRM = işlemci besleme) |
| koruma | aşırı akım/gerilim/ESD/ısı için devre **özellik** sınıfı (tasarım değil) |
| dağıtım | güçten besleme raylarına/kollektör planına aktarım (yollar, sigorta noktaları) |
| BMS | batarya denetleyici (şarj/dşarj/ölçüm/balance — sınıf kaydı) |

---

## §4 Fiziksel bileşen ↔ bitmiş cihaz ayrımı (anayasa §12.1 benzeri ayrım)

| Kavram | power örneği | Kural |
|---|---|---|
| Bileşen | sigorta, LDO, DC/DC modülü, MOSFET koruma | envantere bileşen olarak girer |
| Alt sistem | VRM bölgesi, güç dağıtımı rayı | bileşenler topluluğu |
| Bitmiş cihaz | masaüstü PSU (ATX), duvar adaptörü | kullanıcıya satılan ürün |
| İlişki | cihazın **içinde** bileşenler var | "PSU cihaz" ile "sigorta bileşen" aynı satırda birleştirilmez |

---

## §5 Tip ↔ arayüz ayrımı (I2S benzeri ayrım — burada güç için)

| Eksen | Ne modellenir | Ayırıcı |
|---|---|---|
| Tip sınıfı | bu bir PSU mu adaptör mü LDO mu | sınıf = ne olduğu |
| Arayüz | ATX 24-pin · barrel jack · Molex/SATA güç · USB-C PD · terminal ray | bağlanma = nasıl bağlanır |
| Protokol/seviye | ATX spesifikasyonu · PD kontratı · I²C telemetri (varsa) | konuşma/kontrol düzeyi |
| Koruma özelliği | sigorta, OVP/OCP, ESD, ısıl kapanış | özellik ≠ tip |

**Kural**: "ATX PSU" tek satırda hem tip hem arayüz hem protokol **karıştırılmaz** —
`interfaces` ve `protocols` alanları ayrı doldurulur.

---

## §6 Tip kayıtları (26 alan şema — P-T01…P-T13)

> **Şema (prompt §6)**: type_id · name_tr · name_en · category · subcategory · description ·
> physical_component_or_device · primary_function · interfaces · protocols · power_requirements ·
> operating_environment · os_dependencies · driver_requirements · firmware_dependency ·
> hardware_abstraction_requirement · audio_relevance · coremusic_relevance · parent_domain ·
> owner_domain · existing_document · evidence · status · confidence · open_questions · proposed_path
> **Kural**: bilinmeyen `UNKNOWN` / `NOT PROVIDED` / `VERIFY REQUIRED`; ölçüm iddiası yok
> (S1=0); status etiketleri IMPLEMENTED/DOCUMENTED/PLANNED/PROPOSED/UNKNOWN/VERIFY REQUIRED.

### P-T01 · ATX PSU

| Alan | Değer |
|---|---|
| type_id | P-T01 |
| name_tr | ATX güç kaynağı |
| name_en | ATX power supply unit |
| category | power |
| subcategory | system-psu |
| description | Masaüstü sistem için şebeke AC'yi DC'ye çeviren, ATX form faktöründe güç kaynağı |
| physical_component_or_device | device (bitmiş ürün; içinde bileşenler: traço, kapasitör, sigorta, fan) |
| primary_function | Sistemi beslemek, gerilim raylarını üretmek ve korumak |
| interfaces | ATX 24-pin ana konektör · EPS/PCIe güç konektörleri (sıra ve pinlemesi NOT PROVIDED) |
| protocols | ATX spesifikasyonu (sürüm NOT PROVIDED — VERIFY REQUIRED) |
| power_requirements | Giriş: şebeke AC (aralık NOT PROVIDED) · Çıkış: DC rayları (değerler NOT PROVIDED) |
| operating_environment | Kapalı gövde, iç mekân (sıcaklık aralığı NOT PROVIDED) |
| os_dependencies | yok (donanım katmanı) — OS yalnız güç düğmesi/yönetim sinyalleri görür |
| driver_requirements | özel sürücü Genelde yok · ACPI güç yönetimi (OS düzeyi) |
| firmware_dependency | ATX/EC davranışına bağımlı olabilir — VERIFY REQUIRED |
| hardware_abstraction_requirement | OS güç yönetimi katmanı (ACPI) |
| audio_relevance | düşük — demirleme/ground loop potansiyeli (tasarım konusu K002) |
| coremusic_relevance | orta — stüdyo/masaüstü sisteminin beslemesi (envanter) |
| parent_domain | K001-donanım/donanaım-type/power |
| owner_domain | K001-donanım (sınıflandırma) · tasarım K002-electroncis |
| existing_document | K002-electroncis/ (16 md) · ADR-063, ADR-064 (varlık) |
| evidence | repo sayımı PSU=0 (S2) · anayasa §2 çatışma kaydı (§0) |
| status | UNKNOWN (repo'da karşılığı yok) |
| confidence | LOW (sınıf tanımı literatür, cihaz kanıtı yok) |
| open_questions | VD-02 · hangi PSU envanterde · ölçülen çıkış sapmaları (S1=0) |
| proposed_path | K001-donanım/donanaım-type/power/power-kapsam.md (bu dosya) |

### P-T02 · DC adaptör (harici)

| Alan | Değer |
|---|---|
| type_id | P-T02 |
| name_tr | DC harici adaptör |
| name_en | external DC power adapter |
| category | power |
| subcategory | external-adapter |
| description | Şebeke AC'yi cihaz için DC'ye çeviren, kablo ile gelen harici adaptör (brick) |
| physical_component_or_device | device (bitmiş ürün) |
| primary_function | Harici cihazı beslemek |
| interfaces | AC girişi (fiş tipi NOT PROVIDED) · DC çıkışı (barrel/USB-C — VERIFY REQUIRED) |
| protocols | USB-C PD varsa kontrat (durum: VERIFY REQUIRED) |
| power_requirements | Giriş AC · Çıkış DC (voltaj/akım NOT PROVIDED) |
| operating_environment | Kapalı gövde, masa üstü |
| os_dependencies | yok |
| driver_requirements | yok (düz DC) · USB-C PD ise OS POWER_DELIVERY görüşme katmanı |
| firmware_dependency | PD kontratlı modüllerde muhtemel — VERIFY REQUIRED |
| hardware_abstraction_requirement | OS güç/şarj yönetimi (USB-C PD durumunda) |
| audio_relevance | düşük-orta (şebekeden gelen gürültü, tasarım K002) |
| coremusic_relevance | orta — pedal/hoparlör/ekran gibi aksesuarların tipik beslemesi |
| parent_domain | K001-donanım/donanaım-type/power |
| owner_domain | K001-donanım (sınıflandırma) |
| existing_document | — (K001'de ayrı dosya yok) |
| evidence | repo sayımı power supply=0 (S2) |
| status | UNKNOWN |
| confidence | LOW |
| open_questions | envanterde hangi adaptörler · DC jack ölçümü (S1=0) |
| proposed_path | K001-donanım/donanaım-type/power/power-kapsam.md |

### P-T03 · AC/DC dönüştürücü

| Alan | Değer |
|---|---|
| type_id | P-T03 |
| name_tr | AC/DC dönüştürücü |
| name_en | AC/DC converter |
| category | power |
| subcategory | conversion |
| description | Alternatif doğru akımı sabit doğru akıma çeviren güç dönüşüm aşaması |
| physical_component_or_device | component veya module (iç bileşen olabilir) |
| primary_function | Şebeke enerjisini DC'ye dönüştürmek |
| interfaces | AC girişi · DC çıkışı (bağlantı tipi NOT PROVIDED) |
| protocols | güç dönüşümü için protokol yok · kontrol varsa I²C/PMBus (VERIFY REQUIRED) |
| power_requirements | Giriş AC · Çıkış DC (NOT PROVIDED) |
| operating_environment | Gövde içi / modül |
| os_dependencies | yok |
| driver_requirements | yok (düz çıkış) |
| firmware_dependency | dijital kontrollü modüllerde olabilir — VERIFY REQUIRED |
| hardware_abstraction_requirement | yok (doğrudan elektriksel) |
| audio_relevance | orta — anahtarlama gürültüsü audio zincirini etkiler (tasarım K002) |
| coremusic_relevance | orta — tüm cihazların arkasındaki sınıf |
| parent_domain | K001-donanım/donanaım-type/power |
| owner_domain | K001 (sınıflandırma) · tasarım K002-electroncis |
| existing_document | K002-electroncis/ · ADR-064 (varlık) |
| evidence | repo sayımı PSU=0 (S2) |
| status | UNKNOWN |
| confidence | LOW |
| open_questions | sınıfın repo karşılığı · ölçüm (S1=0) |
| proposed_path | K001-donanım/donanaım-type/power/power-kapsam.md |

### P-T04 · DC/DC dönüştürücü

| Alan | Değer |
|---|---|
| type_id | P-T04 |
| name_tr | DC/DC dönüştürücü |
| name_en | DC/DC converter |
| category | power |
| subcategory | conversion |
| description | Bir DC seviyesini başka DC seviyesine çeviren dönüştürücü sınıfı (buck/boost vb.) |
| physical_component_or_device | component/module |
| primary_function | DC seviyesi dönüştürmek |
| interfaces | DC girişi · DC çıkışı · enable/control (varsa) |
| protocols | dijital kontrol varsa I²C/PMBus (VERIFY REQUIRED) |
| power_requirements | Giriş/çıkış gerilim-akım NOT PROVIDED |
| operating_environment | Gövde içi |
| os_dependencies | yok · OS yalnız dolaylı görür (sensör/driver üzerinden) |
| driver_requirements | özel driver yalnız dijital telemetri varsa — VERIFY REQUIRED |
| firmware_dependency | kontrol firmware'si varsa — VERIFY REQUIRED |
| hardware_abstraction_requirement | varsa telemetri katmanı (OS) |
| audio_relevance | orta — anahtarlama gürültüsü |
| coremusic_relevance | orta — taşınabilir/çiçek besleme zinciri |
| parent_domain | K001-donanım/donanaım-type/power |
| owner_domain | K001 (sınıflandırma) · K002 (tasarım) |
| existing_document | K002-electroncis/ |
| evidence | repo sayımı VRM=0 (S2) |
| status | UNKNOWN |
| confidence | LOW |
| open_questions | hangi rail'ler · verim ölçümü (S1=0) |
| proposed_path | K001-donanım/donanaım-type/power/power-kapsam.md |

### P-T05 · LDO

| Alan | Değer |
|---|---|
| type_id | P-T05 |
| name_tr | LDO regülatör |
| name_en | low-dropout regulator |
| category | power |
| subcategory | regulation |
| description | Düşük dropout'lı doğrusal gerilim regülatörü — düşük gürültü, ısıl dağılım |
| physical_component_or_device | component |
| primary_function | Sabit, temiz DC gerilim sağlamak |
| interfaces | VIN · VOUT · GND (pin dizilimi NOT PROVIDED) |
| protocols | protokol yok · enable/feedback pinleri (varsa) |
| power_requirements | Giriş-çıkış NOT PROVIDED · dropout NOT PROVIDED |
| operating_environment | Gövde içi, sıcaklığa duyarlı |
| os_dependencies | yok |
| driver_requirements | yok |
| firmware_dependency | yok (düz regülatör) |
| hardware_abstraction_requirement | yok |
| audio_relevance | YÜKSEK — ses yolundaki regülatör gürültüsü doğrudan işler (sınıf kaydı) |
| coremusic_relevance | yüksek — audio ekipmanında tipik sınıf |
| parent_domain | K001-donanım/donanaım-type/power |
| owner_domain | K001 (sınıflandırma) · K002 (tasarım/hesap) |
| existing_document | K002-electroncis/ |
| evidence | repo sayımı LDO=0 (S2) |
| status | UNKNOWN |
| confidence | MEDIUM (sınıf tanımı yaygın) |
| open_questions | hangi rail'de LDO · gürültü ölçümü (S1=0) |
| proposed_path | K001-donanım/donanaım-type/power/power-kapsam.md |

### P-T06 · VRM

| Alan | Değer |
|---|---|
| type_id | P-T06 |
| name_tr | VRM (besleme bölgesi) |
| name_en | voltage regulator module |
| category | power |
| subcategory | regulation |
| description | Yük (işlemci vb.) için yüksek akımlı, çok fazlı DC/DC besleme bölgesi |
| physical_component_or_device | module/region (bileşen topluluğu) |
| primary_function | Yükü yüksek akımla, düşük gerilimde beslemek |
| interfaces | Girişi ana DC ray · çıkışı yük · kontrol PWM/VID (NOT PROVIDED) |
| protocols | yük kontrol protokolleri (ör. dijital VR protokolleri) — VERIFY REQUIRED |
| power_requirements | Giriş/çıkış NOT PROVIDED |
| operating_environment | Gövde içi, ısı yoğun bölge |
| os_dependencies | yok (OS güç politikası dolaylı) |
| driver_requirements | özel telemetri driver'ı olabilir — VERIFY REQUIRED |
| firmware_dependency | dijital VRM'de firmware olabilir — VERIFY REQUIRED |
| hardware_abstraction_requirement | varsa telemetri/driver katmanı |
| audio_relevance | düşük — ancak sistem toprak/ısı tasarımına dolaylı |
| coremusic_relevance | düşük-orta (sistem bileşeni, envanter) |
| parent_domain | K001-donanım/donanaım-type/power |
| owner_domain | K001 (sınıflandırma) · K002 (tasarım) |
| existing_document | K002-electroncis/ |
| evidence | repo sayımı VRM=0 (S2) |
| status | UNKNOWN |
| confidence | MEDIUM |
| open_questions | sistemde VRM bölgesi var mı · telemetri (S1=0) |
| proposed_path | K001-donanım/donanaım-type/power/power-kapsam.md |

### P-T07 · Batarya

| Alan | Değer |
|---|---|
| type_id | P-T07 |
| name_tr | batarya |
| name_en | battery |
| category | power |
| subcategory | storage |
| description | Kimyasal enerji depolayan güç kaynağı sınıfı (hücre/paket) |
| physical_component_or_device | device/component (hücre veya paket) |
| primary_function | Sistem taşınabilirliğinde enerji depolamak |
| interfaces | Besleme uçları · şarj/veri (varsa) — NOT PROVIDED |
| protocols | şarj protokolleri (varsa) — VERIFY REQUIRED |
| power_requirements | hücre gerilimi/akım/sıcaklık NOT PROVIDED |
| operating_environment | sıcaklık sınırlarına duyarlı — aralık NOT PROVIDED |
| os_dependencies | OS pil göstergesi/uyku politikası (varsa) |
| driver_requirements | OS pil sürücüsü (varsa) — VERIFY REQUIRED |
| firmware_dependency | paket içinde koruma/BMS firmware'si olabilir |
| hardware_abstraction_requirement | OS pil API'si |
| audio_relevance | düşük (taşınabilir setup'ta orta) |
| coremusic_relevance | düşük-orta — taşınabilir stüdyo/kulaklık sınıfı |
| parent_domain | K001-donanım/donanaım-type/power |
| owner_domain | K001 (sınıflandırma) |
| existing_document | — |
| evidence | repo sayımı (batarya ölçümü yapılmadı — sayım: güç kavramları 0, S2) |
| status | UNKNOWN |
| confidence | MEDIUM |
| open_questions | envanterde batarya var mı · kapasite/ölçüm (S1=0) |
| proposed_path | K001-donanım/donanaım-type/power/power-kapsam.md |

### P-T08 · BMS

| Alan | Değer |
|---|---|
| type_id | P-T08 |
| name_tr | batarya yönetim sistemi |
| name_en | battery management system |
| category | power |
| subcategory | protection-control |
| description | Bataryayı ölçen, şarj/dşarjı yöneten, koruyan denetleyici sınıfı |
| physical_component_or_device | module (bileşen topluluğu) |
| primary_function | Güvenli şarj/dşarj, ölçüm, dengeleme, koruma |
| interfaces | hücre uçları · şarj kontrolü · haberleşme (varsa) — NOT PROVIDED |
| protocols | hücre telemetrisi haberleşme (I²C/SMBus vb.) — VERIFY REQUIRED |
| power_requirements | ölçülen hücre değerleri NOT PROVIDED |
| operating_environment | batarya paketi içinde |
| os_dependencies | yok (OS yalnız rapor görürse) |
| driver_requirements | telemetri varsa OS driver'ı — VERIFY REQUIRED |
| firmware_dependency | EVET — BMS firmware'si (sürüm NOT PROVIDED) |
| hardware_abstraction_requirement | telemetri raporlama katmanı (varsa) |
| audio_relevance | yok-sınırda |
| coremusic_relevance | düşük (envanter) |
| parent_domain | K001-donanım/donanaım-type/power |
| owner_domain | K001 (sınıflandırma) · tasarım K002 |
| existing_document | — |
| evidence | repo sayımı BMS=0 (S2) |
| status | UNKNOWN |
| confidence | MEDIUM |
| open_questions | sistemde BMS var mı (batarya yoksa sınıf da yok) |
| proposed_path | K001-donanım/donanaım-type/power/power-kapsam.md |

### P-T09 · Güç dağıtımı

| Alan | Değer |
|---|---|
| type_id | P-T09 |
| name_tr | güç dağıtımı |
| name_en | power distribution |
| category | power |
| subcategory | distribution |
| description | Kaynaktan tüketicilere gücü taşıyan ray/yol/toprak düzeni (alt sistem) |
| physical_component_or_device | subsystem (yollar, konnektörler, sigorta noktaları) |
| primary_function | Gücü dağıtmak ve dallanmayı korumak |
| interfaces | konnektörler/kuplörler (NOT PROVIDED) |
| protocols | protokol yok · izleme için telemetri (varsa) — VERIFY REQUIRED |
| power_requirements | taşıdığı yükler NOT PROVIDED |
| operating_environment | gövde içi |
| os_dependencies | yok |
| driver_requirements | yok |
| firmware_dependency | yok (düz dağıtım) |
| hardware_abstraction_requirement | yok |
| audio_relevance | yüksek (toprak düzeni / ground planı, tasarım K002) |
| coremusic_relevance | yüksek — stüdyo masası/ekipman besleme düzeni |
| parent_domain | K001-donanım/donanaım-type/power |
| owner_domain | K001 (sınıflandırma) · tasarım K002 |
| existing_document | — |
| evidence | repo sayımı PSU=0 (S2) |
| status | UNKNOWN |
| confidence | MEDIUM |
| open_questions | mevcut dağıtım düzeni belgeli mi · şema kanıtı yok |
| proposed_path | K001-donanım/donanaım-type/power/power-kapsam.md |

### P-T10 · Sigorta

| Alan | Değer |
|---|---|
| type_id | P-T10 |
| name_tr | sigorta |
| name_en | fuse |
| category | power |
| subcategory | protection |
| description | Aşırı akımda devreyi açan tek kullanımlık/yenilenir koruma bileşeni |
| physical_component_or_device | component |
| primary_function | Aşırı akım koruması |
| interfaces | seri bağlantı uçları (tip/boyut NOT PROVIDED) |
| protocols | protokol yok |
| power_requirements | akım derecesi/voltaj derecesi NOT PROVIDED |
| operating_environment | gövde içi |
| os_dependencies | yok |
| driver_requirements | yok |
| firmware_dependency | yok |
| hardware_abstraction_requirement | yok (mekanik/elektriksel) |
| audio_relevance | düşük-orta (koruma zinciri) |
| coremusic_relevance | orta — ekipman koruması envanteri |
| parent_domain | K001-donanım/donanaım-type/power |
| owner_domain | K001 (sınıflandırma) · tasarım K002 |
| existing_document | — |
| evidence | repo sayımı fuse/sigorta=0 (S2) |
| status | UNKNOWN |
| confidence | MEDIUM |
| open_questions | sistemde sigorta var mı · dereceler (S1=0) |
| proposed_path | K001-donanım/donanaım-type/power/power-kapsam.md |

### P-T11 · Aşırı akım / aşırı gerilim koruması

| Alan | Değer |
|---|---|
| type_id | P-T11 |
| name_tr | aşırı akım/aşırı gerilim koruması |
| name_en | overcurrent / overvoltage protection |
| category | power |
| subcategory | protection |
| description | OCP/OCP eşiklerinde kesen/limitleyen koruma sınıfı (aktif/pasif) |
| physical_component_or_device | component/module |
| primary_function | Yükü aşırı akım/gerilimden korumak |
| interfaces | seri/paralel bağlantı · algılama uçları (NOT PROVIDED) |
| protocols | protokol yok · kilitlenme/rapor sinyali (varsa) — VERIFY REQUIRED |
| power_requirements | eşikler NOT PROVIDED |
| operating_environment | gövde içi |
| os_dependencies | yok (kapanış donanımsal) |
| driver_requirements | yok |
| firmware_dependency | akıllı korumalarda olabilir — VERIFY REQUIRED |
| hardware_abstraction_requirement | alarm varsa OS raporlama katmanı |
| audio_relevance | düşük-orta |
| coremusic_relevance | orta |
| parent_domain | K001-donanım/donanaım-type/power |
| owner_domain | K001 (sınıflandırma) · tasarım K002 |
| existing_document | — |
| evidence | repo sayımı (koruma kavramı) güç kavramları 0 (S2) |
| status | UNKNOWN |
| confidence | MEDIUM |
| open_questions | mevcut koruma şeması · eşik ölçümü (S1=0) |
| proposed_path | K001-donanım/donanaım-type/power/power-kapsam.md |

### P-T12 · ESD koruması

| Alan | Değer |
|---|---|
| type_id | P-T12 |
| name_tr | ESD koruması |
| name_en | ESD protection |
| category | power |
| subcategory | protection |
| description | Elektrostatik deşarja karşı arayüz koruması sınıfı (TVS vb.) |
| physical_component_or_device | component |
| primary_function | Arayüz pinlerini statik deşarja karşı korumak |
| interfaces | korunan arayüz pinleri (hangi arayüz — NOT PROVIDED) |
| protocols | protokol değil · koruduğu protokol arayüzün kendi (Power-over-USB vb.) |
| power_requirements | çalışma gerilim aralığı NOT PROVIDED |
| operating_environment | arayüz konnektörü yakını |
| os_dependencies | yok |
| driver_requirements | yok |
| firmware_dependency | yok |
| hardware_abstraction_requirement | yok |
| audio_relevance | düşük (giriş uçlarında orta) |
| coremusic_relevance | orta — tak-çıkar yapılan USB/girişler |
| parent_domain | K001-donanım/donanaım-type/power |
| owner_domain | K001 (sınıflandırma) · tasarım K002 (A/ADR-063) |
| existing_document | ADR-063-hardware-design-standards.md (varlık) |
| evidence | repo sayımı 0 (S2) · ADR-063 var (S3) |
| status | UNKNOWN |
| confidence | MEDIUM |
| open_questions | hangi arayüzlerde ESD var · test raporu (S1=0) |
| proposed_path | K001-donanım/donanaım-type/power/power-kapsam.md |

### P-T13 · Isıl koruma

| Alan | Değer |
|---|---|
| type_id | P-T13 |
| name_tr | ısıl koruma |
| name_en | thermal protection |
| category | power |
| subcategory | protection |
| description | Aşırı sıcaklıkta kapanan/limitleyen koruma sınıfı (termistor, eşik, fan kontrolü) |
| physical_component_or_device | component/module (sensör + eşik + eyleyici) |
| primary_function | Isıyı izleyip aşırı durumda gücü kesmek/azaltmak |
| interfaces | sıcaklık sensörü bağlantısı · kontrol çıkışı (NOT PROVIDED) |
| protocols | sensör haberleşmesi (varsa) — VERIFY REQUIRED |
| power_requirements | eşik sıcaklığı NOT PROVIDED |
| operating_environment | ısı kaynağına yakın |
| os_dependencies | OS termal rapor/politika (varsa) |
| driver_requirements | sensör driver'ı (varsa) — VERIFY REQUIRED |
| firmware_dependency | kontrol firmware'si olabilir — VERIFY REQUIRED |
| hardware_abstraction_requirement | termal izleme katmanı (OS/motherboard) |
| audio_relevance | düşük-orta (fan gürültüsü doğrudan işler — stüdyo) |
| coremusic_relevance | orta-yüksek (sessiz çalışma gereksinimi) |
| parent_domain | K001-donanım/donanaım-type/power |
| owner_domain | K001 (sınıflandırma) · tasarım K002 |
| existing_document | ADR-064-electronics-platform-architecture.md (varlık) |
| evidence | repo sayımı 0 (S2) · ADR-064 var (S3) |
| status | UNKNOWN |
| confidence | MEDIUM |
| open_questions | mevcut termal eşikler · ölçüm (S1=0 — sıcaklık ölçümü yapılmadı) |
| proposed_path | K001-donanım/donanaım-type/power/power-kapsam.md |

**Kayıt özeti**: 13 kayıt · status = UNKNOWN (hepsi) · confidence LOW×3, MEDIUM×10 ·
S1 ölçüm 0 · `NOT PROVIDED`/`VERIFY REQUIRED` alanları tahmin edilmedi.

---

## §sınır · power ↔ K002-electroncis sınırı (bağlamsal değil, kural)

| Konu | K001 power (bu klasör) | K002-electroncis (+ ADR-063/064) |
|---|---|---|
| Tip/enventar sınıflandırma | EVET (P-T01..P-T13) | — |
| Bağlam, arayüz, koruma **özellik** kaydı | EVET | — |
| Devre tasarımı (topoloji, feedback, hesap) | HAYIR | EVET |
| PCB düzeni, BOM, bileşen seçimi | HAYIR | EVET (anayasa §2 "Fiziksel PCB / BOM → HAYIR") |
| ısıl/EMI hesap ve simülasyon | HAYIR | EVET |
| Ölçüm kapıları (ne ölçülür) | EVET (power-dogrulama K-P4) | ölçüm sonuçları iki tarafta da ayrı kayıt |

> K002-electroncis 16 md + ADR-063/064 (`.ai/.decisions/accepted/`) — **varlık kanıtı**;
> içerikleri bu görevde okunmadı (yalnız dosya varlığı + adlar kullanıldı).
> anayasa §2: "Fiziksel PCB / BOM → HAYIR" (okundu, S3) — bu sınırın dayanağı.

---

## § Kaynaklar (HEAD 2026-10-13, kendi çalıştırığım)

| # | Kaynak | HEAD | Kullanım | İçerik |
|---|---|---|---|---|
| 1 | `https://www.80plus.org/` | **200** | PSU verimlilik sınıfı referansı (P-T01) | ⚠️ okunmadı |
| 2 | `https://www.energystar.gov/` | **200** | adaptör/verim programı referansı (P-T02) | ⚠️ okunmadı |
| 3 | `https://www.iec.ch/` | **202** | güvenlik/standart kuruluşu (koruma sınıfı) | ⚠️ okunmadı |

> `jedec.org` (403), `analog.com`/`littelfuse.com`/`formfactors.org` (ERR) erişilemedi —
> **kullanılmadı**. 200/202 = erişildi; içerik okunmadı ⇒ ilgili iddialar `⚠️ VERIFICATION REQUIRED`.
> G6 unique URL: bu 3 URL yalnız power dosyalarında geçer (display 6'sı display dosyalarında).

---

## § Kapanış

1. power/ 8 dosya açıldı (Vault Steward onayı, 2026-10-13) — TAMAM.
2. Çatışma kaydı §0: 5 satır + 5 adımlı çözüm tablosu — adım 1–3 TAMAM, 4–5 APPROVAL REQUIRED.
3. Sınır §sınır: K001 ≠ K002 (tasarım/değil) — kural olarak yazıldı.
4. 13 tip kaydı (§6): bilinmeyen alanlar uydurulmadı; status UNKNOWN; S1=0.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
