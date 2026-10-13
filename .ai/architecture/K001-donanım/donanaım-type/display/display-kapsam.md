---
title: "display-kapsam — Ekran Görüntüleme Sınıfı Kapsamı"
type: architecture
node_id: display-kapsam
node_type: module
status: proposed
parent_id: K001-donanım/donanaım-type/display
children: []
depends_on:
  - K001-donanım
  - K001-donanım/donanaım-type
  - K001-donanım/donanaım-type/display/index
evidence:
  - "Vault Steward 2026-10-13 dağıtık onayı — klasör açımı YAPILDI; anayasa revizyonu YOK"
  - "donanım-anayasa.md §2 (Cihaz tipi sınıflandırma = K001/donanaım-type · Fiziksel PCB/BOM = HAYIR)"
  - "donanım-anayasa.md §10/3 · §12/C10 · §12.1 (Yapısal sınıf — ADR-097 onayı ister)"
  - "donanaım-type-kapsam.md §2/A6 · §7/3-T1 · §12/M6 · §7.3/X3 (mevcut metinde kapsam DIŞINDA)"
  - "https://vesa.org/ (HTTP 200, 2026-10-13, HEAD)"
  - "https://www.hdmi.org/ (HTTP 200, 2026-10-13, HEAD)"
  - "https://www.displayport.org/ (HTTP 200, 2026-10-13, HEAD)"
  - "https://www.displayhdr.org/ (HTTP 200, 2026-10-13, HEAD)"
  - "https://www.cta.tech/ (HTTP 200, 2026-10-13, HEAD)"
  - "https://www.khronos.org/ (HTTP 200, 2026-10-13, HEAD)"
  - "Repo ölçümü 2026-10-13: 700 kaynak dosyada EDID=0 · DDC=0 · HDMI=0 · DisplayPort=0 · VESA=0 · touchscreen/dokunmatik=19"
validation: "proposed — 2026-10-13 yazıldı · ≥500 satır ölçümü yazım sonrası · fiziksel envanter ve ölçüm YOK (tüm S1 kapıları açık) · anayasa revizyonu YAPILMADI"
open_questions:
  - "Q-DK1: Ekran envanteri (panel tipi · boyut · çözünürlük · arayüz) bilinmiyor — UNKNOWN (K001 Q4)"
  - "Q-DK2: anayasa §10/3 + §12/C10 revizyonu (3→5 bağlam) onayı alınmadı — APPROVAL REQUIRED"
  - "Q-DK3: GPU görüntü çıkışı donanımının sahip olduğu K düğümü (K001 mi, başka düğüm mü) eşleştirilmedi — ⚠️ VERIFICATION REQUIRED"
group: mimari
date: 2026-10-13
updated: 2026-10-13
---

# display-kapsam — Ekran Görüntüleme Kapsamı (K001 Donanım Türü Envanteri)

> **Yazım notu:** `donanaım-type` yazımı v2 kaynağına sadıktır — düzeltme yasak
> (v2 §3 KURAL 1; `donanaım-type/index.md` yazım notu). Bu dosya `display/`
> alt-konunun **kapsam** belgesidir: ekran donanımının K001 tür envanterindeki
> yeri, tip kayıtları, panel ↔ arayüz ayrımı ve komşu düğümlerle sınır.

## §0 SINIR ÇELİŞKİSİ KAYDI (zorunlu — gizleme YASAK)

Bu klasör, vault'un mevcut anayasası ve üst kapsam metni ile **çelişir**. Çelişki
bu dosyada ve kardeş `power/power-kapsam.md` girişinde tablo halinde kayıtlıdır;
**gizlenmez, yumuşatılmaz**. Çözüm yolu Vault Steward 2026-10-13 açık onayı ile
klasör açımıdır; metin revizyonu = **APPROVAL REQUIRED (henüz yapılmadı)**.

| # | Çelişkili kaynak | Metindeki ifade (olduğu gibi) | Bu klasörle ilişkisi |
|---|---|---|---|
| 1 | `donanım-anayasa.md` §10 Açık Sorular /3 | "donanaım-type altı 3 bağlam mı, fazlası mı? — ⚠️ VERIFICATION REQUIRED" | 5. bağlam açılması bu soruyu *yanıtlar* ama anayasa metninde **hâlâ 3 yazar** |
| 2 | `donanım-anayasa.md` §12/C10 (değişmez kural) | "3 kademe-3 alt bağlam (audio/hdd/ssd) — EVET, envanterle (değişebilir)" | Değişiklik yapısal sınıf (§12.1) → **ADR-097 onayı ister** — alınmadı |
| 3 | `donanım-anayasa.md` §2 / §5 | "Cihaz tipi sınıflandırma: audio · hdd · ssd" | Ekran satırı yok — **eksik kayıt**, revize EDİLMEDİ |
| 4 | `donanaım-type-kapsam.md` §2/A6 | "Ekran · kablo · güç kaynağı türleri — **DIŞINDA** — anayasa §10/5 yasağı (ayrı bağlam AÇILMAZ)" | `display/` klasörü bu satırı **ihlal eder** |
| 5 | `donanaım-type-kapsam.md` §7/3-T1 | "Altına yalnız 3 bağlam açılır (`hdd` · `audio` · `ssd`)" | Aynı ihlal |
| 6 | `donanaım-type-kapsam.md` §12/M6 | "Ekran / kablo / güç kaynağı türü — ❌ kapsam dışı — bağlam AÇILMAZ" | Aynı ihlal |
| 7 | `donanaım-type-kapsam.md` §7.3/X3 | "Ekran/kablo/güç kaynağı için bağlam açmak — anayasa yasağı" | Aynı ihlal |

**Durum etiketi (bağlayıcı):** Yukarıdaki 7 satırın tamamı için bu dosya seti
`PROPOSED — APPROVAL REQUIRED` etiketini taşır. Açık soru **Q-T2**
("hdd/ssd dışı türler nereye gidiyor?" — `donanaım-type-kapsam.md` §6) bu
dosyalarla **yanıtlanmış sayılır, kapatılmaz**; kapanış yalnız anayasa §10/3,
§12/C10 ve üst kapsam §2/A6 · §7/3-T1 · §12/M6 metinlerinin revizyonuyla olur.

| # | Kapanış adımı | Sahip | Durum |
|---|---|---|---|
| 1 | Klasör açımı (`display/` · `power/`) | Vault Steward (2026-10-13 açık onay) | ✅ YAPILDI |
| 2 | `donanım-anayasa.md` §10/3 + §12/C10 + §2/§5 satır revizyonu | Vault Steward + ADR-097 onayı | ❌ YAPILMADI — APPROVAL REQUIRED |
| 3 | `donanaım-type-kapsam.md` §2/A6 · §7/3-T1 · §12/M6 revizyonu | Vault Steward | ❌ YAPILMADI — APPROVAL REQUIRED |
| 4 | `donanaım-type/index.md` + `K001-donanım/index.md` children güncellenmesi | bu tur (append) | ✅ YAPILDI (2026-10-13) |
| 5 | Q-T2 kapanışı | Vault Steward | ❌ açık |

## §1 ELI15

Ekran donanımı, bilgisayarın ürettiği sayısal görüntüyü **görülebilir ışığa**
çeviren fiziksel parçalar bütünüdür: bir **panel** (ışığı/pikseli üreten yüzey),
bir **aydınlatma katmanı** (LCD'de arkadan aydınlatma; OLED'de yok — her piksel
kendi ışığını üretir), bir **ekran kontrolcüsü** (zamanlama/scalar — dijital
görüntüyü panelin satır-sütun sürücüsüne çeviren çip), bir **görüntü çıkışı**
(GPU'nun fiziksel çıkış bloğu) ve bir **arayüz** (DisplayPort/HDMI sınıfı
bağlantı protokolü). Üzerine dokunma bilgisini veren **dokunmatik katman**
ayrı bir bileşendir. Bu dosya bu parçaların **sınıf/envanter** listesini
çıkarır, rollerini ayırır ve sahiplik sorusunu yanıtlar.

> ⚠️ VERIFICATION REQUIRED: Bu alt-konu v2'de ad olarak geçmez; tanımı mevcut
> anayasa ile **çelişir** (§0). Envanter (hangi panelin fiziksel olarak elde
> olduğu) bilinmiyor (Q-DK1 → UNKNOWN). Bu dosyada geçen **hiçbir sayısal
> performans değeri ölçüm değildir**; ölçüm `display-dogrulama.md` kapılarında
> tanımlıdır. Uydurma rakam yazılmaz.

## §2 Kapsam — İÇİNDE NE var

| # | Kapsam İÇİ | Durum |
|---|---|---|
| A1 | Ekran sınıfının K001 tür envanterindeki yeri (tip listesi) | ✅ mimari tanım (§6) |
| A2 | 8 tip kaydı — prompt §6 26-alan şeması ile | ✅ §6 |
| A3 | Panel tipi ↔ arayüz protokolü **ayrı modelleme** (I2S benzeri ayrım) | ✅ §4 |
| A4 | Fiziksel bileşen ↔ tamamlanmış cihaz ayrımı | ✅ §5 |
| A5 | Etiket disiplini (IMPLEMENTED…UNKNOWN) | ✅ kural tablosu (§7) |
| A6 | K001 ↔ komşu düğümler sınırı (K000 · K002 · UI katmanı) | ✅ mimari sınır (§8) |
| A7 | Ölçüm/kabul kapılarının **tanımı** (uygulaması `display-dogrulama.md`) | ✅ tanım (§9) |
| A8 | Çelişki kaydı ve kapanış adımları | ✅ §0 |
| A9 | Riskler ve uygulama sırası | ✅ plan (§10–§11) |
| A10 | Web kaynaklarının HEAD doğrulama kaydı | ✅ §14 |

## §3 Kapsam — DIŞINDA NE var (sınır komşuları)

| # | Dışında | Nereye gider | Dayanak |
|---|---|---|---|
| B1 | Ekranın **sürülmesi** (sürücü, framebuffer, DDC/EDID okuma kodu) | K000-isletim-sistemi | `donanım-anayasa.md` §2 (işletim sistemi donanımı sürer → HAYIR) |
| B2 | GPU'nun render/çizim davranışı, grafik API kullanımı | K002-electroncis (dijital-ve-mikroislemci) + sürücü katmanı | anayasa §2 · K002 `dijital-ve-mikroislemci/` (4 dosya) |
| B3 | Ekran/panel **PCB, şema, BOM, hesap** | K002-electroncis + ADR-063/064 | anayasa §2 "Fiziksel PCB / BOM → HAYIR" · ADR-063 · ADR-064 (dosya varlıkları doğrulandı) |
| B4 | UI layout, tasarım token'ı, ekran tasarımı | `.ai/ui-design/**` | T17-monitor-22fhd (2 md) ≠ ekran donanımı |
| B5 | Ses çıkışı / ses donanımı | `../audio/` | kardeş alt-bağlam |
| B6 | Depolama | `../hdd/` · `../ssd/` | kardeş alt-bağlam |
| B7 | Güç kaynağı / koruma devresi sınıfları | `../power/` | kardeş alt-bağlam (bu tur açıldı) |
| B8 | Güç devresi tasarımı (regülatör, koruma eşiği) | K000-isletim-sistemi/electroncis/guc-ve-koruma (4 dosya) · K002-electroncis/guc-yonetimi (2 dosya) | disk sayımı 2026-10-13 |
| B9 | Firmware/MCU yüklemesi | K005-firmware | anayasa §2 |
| B10 | Ekran envanteri kaydı (marka/model) | K001 envanter (Q4) + owner onayı | bu dosya karar üretmez |

## §4 Panel Tipi ↔ Arayüz Protokolü — AYRI MODEL

> **Ayrım mantığı I2S'e benzer:** `audio/` bağlamında *teknik sınıf* (DAC/codec)
> ile *arayüz protokolü* (I2S/TDM) ayrı modellenir; burada da **panel teknolojisi**
> (LCD · LED-aydınlatmalı LCD · OLED) ile **görüntü arayüzü protokolü**
> (DisplayPort · HDMI) birbirinden bağımsız eksenlerdir. Bir panel, birden çok
> arayüz üzerinden sürülebilir; bir arayüz, birden çok panel teknolojisini
> taşıyabilir. **Eksenleri karıştırmak = sınıflandırma hatası.**

| # | Eksen | Soru | Ayırma çizgisi | Bu eksendeki dallar |
|---|---|---|---|---|
| X1 | Panel teknolojisi | Piksel ışığı nasıl üretilir? | sıvı kristal + arkadan aydınlatma ↔ kendiliğinden ışıklı | LCD · LED-aydınlatmalı LCD · OLED |
| X2 | Görüntü arayüzü protokolü | Paket/seri nasıl taşınır? | paket tabanlı ↔ saat/veri tabanlı | DisplayPort · HDMI (her ikisi de paket tabanlı — ayrıntı ⚠️) |
| X3 | Çıkış kaynağı | Görüntü kimden çıkar? | GPU bloğu ↔ denetleyici çıkışı | GPU görüntü çıkışı |
| X4 | Dokunma katmanı | Dokunma nasıl okunur? | ekrandan bağımsız bir sensör katmanı | dokunmatik ekran |
| X5 | Kontrol/zamanlama | Panelin satır-sütun sürümünü kim yapar? | scalar / timing controller | ekran kontrolcüsü |

**Eksen eşleme kuralı:** Bir tip kaydı **tek eksene** aittir; iki eksene birden
yazan kayıt bölünür. Örn. "HDMI'lı OLED ekran" = 3 kayıt (OLED panel · HDMI
arabirimi · tamamlanmış cihaz) — tek kayıtta birleştirilmez (§5).

| # | Karıştırma | Sonucu | Doğrusu |
|---|---|---|---|
| 1 | "OLED = bir arayüz" sanmak | Protokol envanteri şişer | OLED **panel teknolojisidir** (X1) |
| 2 | "HDMI = bir panel" sanmak | Panel envanteri şişer | HDMI **arayüz protokolüdür** (X2) |
| 3 | GPU çıkışını panel saymak | Çift sayım | GPU çıkışı **kaynak**tır (X3) |
| 4 | Dokunmatik katmanı panelin içine gömmek | Dokunma envanteri kaybolur | ayrı kayıt (X4) |

## §5 Fiziksel Bileşen ↔ Tamamlanmış Cihaz Ayrımı

> **Kural:** K001 tür envanteri **fiziksel bileşen** sınıfını kaydeder; satın
> alınabilir/teslim alınabilir **tamamlanmış cihaz** ayrı satırdır. Bileşen ↔
> cihaz eşlemesi envantere gelmeden yapılamaz (Q-DK1).

| # | Fiziksel bileşen (K001 türü) | Tamamlanmış cihaz (dışarıdan bakış) | Fark |
|---|---|---|---|
| 1 | Panel (LCD/LED-ayd./OLED) | Ekran modülü / monitör | Panel bileşendir; monitör cihazdır |
| 2 | Ekran kontrolcüsü (scalar/T-CON) | Ekran kartı (display controller board) | Kontrolcü çiptir; kart taşıyıcıdır |
| 3 | GPU görüntü çıkışı bloğu | Ekran kartı (add-in) veya tümleşik GPU | Çıkış **blok**tur; kart cihazdır |
| 4 | DisplayPort arabirim bloğu/konnektör | Kablo/set | Arabirim bileşendir; kablo aksesuardır (kablo türü kapsam dışı — §0) |
| 5 | HDMI arabirim bloğu/konnektör | Kablo/set | Aynı |
| 6 | Dokunmatik katman (sensor + controller) | Dokunmatik ekran modülü | Katman bileşendir; modül cihazdır |
| 7 | Arkadan aydınlatma katmanı (LCD'de) | — (panelin parçası) | OLED'de **yoktur** |

**Ayrım kuralı:** "elde var mı?" sorusu **her iki satır için ayrı** sorulur;
bileşen elde ise cihaz elde **değildir** (ve tersi). Her iki satır da `UNKNOWN`
iken satın alma kararı verilemez (§9 K-D1).

## §6 Tür Kayıtları (prompt §6 — 26 alan şeması)

> Her kayıt 26 alanla yazılır. Bilinmeyen alan `UNKNOWN` / `NOT PROVIDED` /
> `VERIFY REQUIRED` ile işaretlenir; marka/model **yalnız kanıtlanırsa** yazılır
> (bu turda **kanıtlanan marka/model YOKTUR** — envanter UNKNOWN). Alan 21
> (`existing_document`) yalnız vault disk dosyası gösterir; alan 22 (`evidence`)
> yalnız bu oturumda doğrulanan kanıt.

### §6.1 D-T01 — LCD panel

| # | Alan | Değer |
|---|---|---|
| 1 | type_id | D-T01 |
| 2 | name_tr | Sıvı kristal ekran paneli (arkadan aydınlatmalı) |
| 3 | name_en | LCD panel (backlit) |
| 4 | category | display |
| 5 | subcategory | panel |
| 6 | description | Sıvı kristal katmanının ışık geçişini kontrol ettiği, arkadan aydınlatma ile çalışan panel sınıfı |
| 7 | physical_component_or_device | fiziksel bileşen (panel) — cihaz ayrı (§5/1) |
| 8 | primary_function | Sayısal görüntüyü görünür ışığa çevirme |
| 9 | interfaces | Panel arayüzü (ekran kontrolcüsüne) — türü UNKNOWN |
| 10 | protocols | Dijital arayüz protokolü ayrı eksendedir (§4/X2) — bu kayıt panel teknolojisidir |
| 11 | power_requirements | UNKNOWN (panel datasheet'i yok) |
| 12 | operating_environment | UNKNOWN (kullanım ortamı kararı yok) |
| 13 | os_dependencies | HAYIR — panel OS'e doğrudan bağlı değildir (sürücü K000'de) |
| 14 | driver_requirements | Panel sınıfı için ayrı sürücü değil; kontrolcü/çıkış sürücüsü K000'de |
| 15 | firmware_dependency | Kontrolcü firmware'i varsa K005 — varlığı UNKNOWN |
| 16 | hardware_abstraction_requirement | EVET — K001 paneli envanter kaydı olarak sunar, ham zamanlama yazmaz |
| 17 | audio_relevance | YOK (ses sinyal yolu ile ilişkisi yok) |
| 18 | coremusic_relevance | Arayüz/görsel doğrulama ekranları — K003/UI ekranları bu sınıf üzerinden **görüntülenir** (bağlantı düzeyi) |
| 19 | parent_domain | K001-donanım/donanaım-type |
| 20 | owner_domain | K001-donanım (tür) · tasarım K002-electroncis + ADR-063/064 |
| 21 | existing_document | `display-kapsam.md` (bu dosya) — ayrı panel dosyası YOK |
| 22 | evidence | Vault Steward 2026-10-13 onayı · vesa.org HEAD 200 |
| 23 | status | PROPOSED (sınıf kaydı) · envanter UNKNOWN |
| 24 | confidence | LOW (envanter yok; yalnız sınıf tanımı) |
| 25 | open_questions | Q-DK1 (envanter) · panel tipi/boyut/çözünürlük UNKNOWN |
| 26 | proposed_path | `display/display-kapsam.md` §6.1 · kapı `display-dogrulama.md` K-D1 |

### §6.2 D-T02 — LED aydınlatmalı LCD panel

| # | Alan | Değer |
|---|---|---|
| 1 | type_id | D-T02 |
| 2 | name_tr | LED aydınlatmalı sıvı kristal panel |
| 3 | name_en | LED-backlit LCD panel |
| 4 | category | display |
| 5 | subcategory | panel |
| 6 | description | Arkadan aydınlatması LED dizisi/şeridi ile yapılan LCD panel sınıfı; "LED ekran" ifadesi çoğu zaman bu sınıfı kasteder (pazarlama dili ≠ teknik sınıf) |
| 7 | physical_component_or_device | fiziksel bileşen (panel + LED aydınlatma katmanı) |
| 8 | primary_function | Aydınlık katmanı + likit kristal ile görüntü üretimi |
| 9 | interfaces | Panel arayüzü (kontrolcüye) — türü UNKNOWN |
| 10 | protocols | Arayüz ekseni ayrı (§4/X2) |
| 11 | power_requirements | UNKNOWN |
| 12 | operating_environment | UNKNOWN |
| 13 | os_dependencies | HAYIR (sürücü K000) |
| 14 | driver_requirements | Arkadan aydınlatma parlaklık kontrolü kontrolcü/OS tarafında — sahiplik K000 ⚠️ |
| 15 | firmware_dependency | UNKNOWN |
| 16 | hardware_abstraction_requirement | EVET (envanter kaydı) |
| 17 | audio_relevance | YOK |
| 18 | coremusic_relevance | Görsel doğrulama ekranları — bağlantı düzeyi |
| 19 | parent_domain | K001-donanım/donanaım-type |
| 20 | owner_domain | K001-donanım (tür) · tasarım K002 + ADR-063/064 |
| 21 | existing_document | `display/display-kapsam.md` (bu dosya) |
| 22 | evidence | Vault Steward 2026-10-13 onayı · displayhdr.org HEAD 200 (kurumsal) |
| 23 | status | PROPOSED · envanter UNKNOWN |
| 24 | confidence | LOW |
| 25 | open_questions | D-T01 ile aynı (Q-DK1) |
| 26 | proposed_path | `display/display-kapsam.md` §6.2 |

### §6.3 D-T03 — OLED panel

| # | Alan | Değer |
|---|---|---|
| 1 | type_id | D-T03 |
| 2 | name_tr | Kendiliğinden ışık yayan diyot paneli |
| 3 | name_en | OLED panel |
| 4 | category | display |
| 5 | subcategory | panel |
| 6 | description | Her pikselin kendi ışığını ürettiği panel sınıfı; arkadan aydınlatma katmanı **yoktur** (§5/7) |
| 7 | physical_component_or_device | fiziksel bileşen (panel) |
| 8 | primary_function | Piksel bazında ışık üretimi |
| 9 | interfaces | Panel arayüzü — türü UNKNOWN |
| 10 | protocols | Arayüz ekseni ayrı (§4/X2) |
| 11 | power_requirements | UNKNOWN |
| 12 | operating_environment | UNKNOWN (yakma riski sınıflandırması ⚠️ — envanter yok) |
| 13 | os_dependencies | HAYIR |
| 14 | driver_requirements | K000 (sürücü-üstü) |
| 15 | firmware_dependency | UNKNOWN |
| 16 | hardware_abstraction_requirement | EVET |
| 17 | audio_relevance | YOK |
| 18 | coremusic_relevance | Bağlantı düzeyi |
| 19 | parent_domain | K001-donanım/donanaım-type |
| 20 | owner_domain | K001-donanım · tasarım K002 + ADR-063/064 |
| 21 | existing_document | `display/display-kapsam.md` (bu dosya) |
| 22 | evidence | Vault Steward 2026-10-13 onayı · vesa.org HEAD 200 |
| 23 | status | PROPOSED · envanter UNKNOWN |
| 24 | confidence | LOW |
| 25 | open_questions | Q-DK1 |
| 26 | proposed_path | `display/display-kapsam.md` §6.3 |

### §6.4 D-T04 — GPU görüntü çıkışı

| # | Alan | Değer |
|---|---|---|
| 1 | type_id | D-T04 |
| 2 | name_tr | GPU görüntü çıkışı (görüntü üreten donanım bloğu) |
| 3 | name_en | GPU display output |
| 4 | category | display |
| 5 | subcategory | kaynak (source) |
| 6 | description | Dijital görüntüyü üreten ve arayüz katmanına veren donanım bloğu (tümleşik veya ayrı birim) |
| 7 | physical_component_or_device | fiziksel bileşen (blok); kart/çip cihazdır (§5/3) |
| 8 | primary_function | Görüntü üretimi ve arayüz protokolüne aktarım |
| 9 | interfaces | DisplayPort / HDMI sınıfı arayüzler (§4/X2) |
| 10 | protocols | Arayüz protokolleri ayrı kayıtlarda (D-T05 · D-T06) |
| 11 | power_requirements | UNKNOWN (envanter yok) |
| 12 | operating_environment | UNKNOWN |
| 13 | os_dependencies | EVET — sürücü K000-isletim-sistemi'nde (anayasa §2) |
| 14 | driver_requirements | Ekran sürücüsü (K000) — sınıf adı/ürün UNKNOWN |
| 15 | firmware_dependency | VBIOS/Display firmware sınıfı — varlığı UNKNOWN |
| 16 | hardware_abstraction_requirement | EVET — K001 envanteri ham register yazmaz |
| 17 | audio_relevance | DÖNÜŞÜMLÜ — HDMI/DP üzerinden ses taşıma **arayüz** yeteneği olabilir; ses zinciri K003'ün (`../audio/` §3/B10) |
| 18 | coremusic_relevance | Görsel doğrulama/monitör ekranlarının kaynağı |
| 19 | parent_domain | K001-donanım/donanaım-type |
| 20 | owner_domain | K001 (tür kaydı) · GPU tasarımı K002-electroncis + ADR-063/064 |
| 21 | existing_document | `donanım-kararlar.md` satır 90-91 (AMD/NVIDIA satırları — **yalnız bu dosyadaki kayıt**, tekrar edilmez) |
| 22 | evidence | `donanım-kararlar.md` (disk) · repo ölçümü HDMI=0/DisplayPort=0 |
| 23 | status | DOCUMENTED (vault satırı) · PROPOSED (bu tür) · envanter UNKNOWN |
| 24 | confidence | LOW-ORTA (vault satırı var, donanım envanteri yok) |
| 25 | open_questions | Q-DK1 · Q-DK3 (sahip K düğümü) |
| 26 | proposed_path | `display/display-kapsam.md` §6.4 |

### §6.5 D-T05 — DisplayPort arabirimi

| # | Alan | Değer |
|---|---|---|
| 1 | type_id | D-T05 |
| 2 | name_tr | DisplayPort görüntü arayüzü |
| 3 | name_en | DisplayPort interface |
| 4 | category | display |
| 5 | subcategory | arayüz |
| 6 | description | Paket tabanlı görüntü taşıma arayüzü sınıfı; yayın standardı VESA tarafından yayımlanır (organizasyon düzeyi — içerik okunmadı) |
| 7 | physical_component_or_device | fiziksel bileşen (arayüz bloğu + konnektör) |
| 8 | primary_function | GPU ↔ ekran arasında görüntü verisi taşıma |
| 9 | interfaces | GPU çıkışına (D-T04) bağlı |
| 10 | protocols | DisplayPort protokolü — sürüm/detay **⚠️ VERIFICATION REQUIRED** (spesifikasyon bu oturumda okunmadı) |
| 11 | power_requirements | UNKNOWN |
| 12 | operating_environment | UNKNOWN |
| 13 | os_dependencies | EVET (sürücü/EDID okuma K000) |
| 14 | driver_requirements | K000 — sınıf düzeyi |
| 15 | firmware_dependency | UNKNOWN |
| 16 | hardware_abstraction_requirement | EVET |
| 17 | audio_relevance | Taşıma yeteneği olabilir → ses zinciri K003 |
| 18 | coremusic_relevance | Monitör/ekran bağlantısı — bağlantı düzeyi |
| 19 | parent_domain | K001-donanım/donanaım-type |
| 20 | owner_domain | K001 (tür) · tasarım K002 + ADR-063/064 |
| 21 | existing_document | `display/display-dogrulama.md` §4 (HEAD kaydı) |
| 22 | evidence | `https://www.displayport.org/` HEAD 200 (2026-10-13) · `https://vesa.org/` HEAD 200 |
| 23 | status | PROPOSED · envanter UNKNOWN |
| 24 | confidence | LOW (yalnız kurumsal kaynak doğrulandı) |
| 25 | open_questions | Sürüm/ek özellik envanteri UNKNOWN (Q-DK1) |
| 26 | proposed_path | `display/display-kapsam.md` §6.5 |

### §6.6 D-T06 — HDMI arabirimi

| # | Alan | Değer |
|---|---|---|
| 1 | type_id | D-T06 |
| 2 | name_tr | HDMI görüntü arayüzü |
| 3 | name_en | HDMI interface |
| 4 | category | display |
| 5 | subcategory | arayüz |
| 6 | description | Paket tabanlı görüntü (+opsiyonel ses) taşıma arayüzü sınıfı; yayın standardı HDMI lisans programı tarafından yürütülür (organizasyon düzeyi — içerik okunmadı) |
| 7 | physical_component_or_device | fiziksel bileşen (arayüz bloğu + konnektör) |
| 8 | primary_function | GPU ↔ ekran之间 görüntü verisi taşıma |
| 9 | interfaces | GPU çıkışına (D-T04) bağlı |
| 10 | protocols | HDMI protokolü — sürüm/detay **⚠️ VERIFICATION REQUIRED** |
| 11 | power_requirements | UNKNOWN |
| 12 | operating_environment | UNKNOWN |
| 13 | os_dependencies | EVET (sürücü/EDID okuma K000) |
| 14 | driver_requirements | K000 |
| 15 | firmware_dependency | UNKNOWN |
| 16 | hardware_abstraction_requirement | EVET |
| 17 | audio_relevance | HDMI ses taşıma yeteneği var → ses zinciri sahibi K003 (`../audio/` §3/B10) |
| 18 | coremusic_relevance | Bağlantı düzeyi |
| 19 | parent_domain | K001-donanım/donanaım-type |
| 20 | owner_domain | K001 (tür) · tasarım K002 + ADR-063/064 |
| 21 | existing_document | `display/display-dogrulama.md` §4 |
| 22 | evidence | `https://www.hdmi.org/` HEAD 200 (2026-10-13) |
| 23 | status | PROPOSED · envanter UNKNOWN |
| 24 | confidence | LOW |
| 25 | open_questions | Sürüm/ek özellik UNKNOWN (Q-DK1) |
| 26 | proposed_path | `display/display-kapsam.md` §6.6 |

### §6.7 D-T07 — Dokunmatik ekran katmanı

| # | Alan | Değer |
|---|---|---|
| 1 | type_id | D-T07 |
| 2 | name_tr | Dokunmatik ekran katmanı |
| 3 | name_en | Touch screen layer |
| 4 | category | display |
| 5 | subcategory | giriş katmanı (görüntüyle ilişkili) |
| 6 | description | Dokunma konumunu algılayan sensör katmanı + onu okuyan denetleyici; **ekrandan bağımsız ayrı bileşendir** (§4/X4) |
| 7 | physical_component_or_device | fiziksel bileşen (katman + denetleyici) |
| 8 | primary_function | Dokunma koordinatı üretimi |
| 9 | interfaces | Denetleyici arayüzü (USB/I2C sınıfı) — türü UNKNOWN |
| 10 | protocols | Dokunma protokolü sınıfı — UNKNOWN (donanım envanteri yok) |
| 11 | power_requirements | UNKNOWN |
| 12 | operating_environment | UNKNOWN |
| 13 | os_dependencies | EVET — girdi alt sistemi K000 |
| 14 | driver_requirements | K000 (girdi sürücüsü) |
| 15 | firmware_dependency | Denetleyici firmware'i varsa K005 — UNKNOWN |
| 16 | hardware_abstraction_requirement | EVET |
| 17 | audio_relevance | YOK |
| 18 | coremusic_relevance | UI etkileşimi (dokunma) — **donanım katı** bu kayıt; UI davranışı ayrı katman |
| 19 | parent_domain | K001-donanım/donanaım-type |
| 20 | owner_domain | K001 (tür) · tasarım K002 + ADR-063/064 |
| 21 | existing_document | Repo kaynak kodu: `assets.coremusic.net/js/managers/ScaleManager.js` · `assets.coremusic.net/Css/08_Devices/d-embedded.css` · `shared/tests/Unit/Device/DeviceDetectorTest.php` (19/700 kaynak dosyada `touchscreen|dokunmatik`) |
| 22 | evidence | Repo ölçümü 2026-10-13 (§14) · usb.org HEAD 200 **kullanılmadı** (başka dosyada kayıtlı) |
| 23 | status | DOCUMENTED (UI kodu) · PLANNED (donanım envanteri yok) |
| 24 | confidence | ORTA (kod kanıtı var, donanım envanteri yok) |
| 25 | open_questions | Donanım katmanı marka/tip UNKNOWN (Q-DK1) |
| 26 | proposed_path | `display/display-ornekler.md` §5 |

### §6.8 D-T08 — Ekran kontrolcüsü

| # | Alan | Değer |
|---|---|---|
| 1 | type_id | D-T08 |
| 2 | name_tr | Ekran kontrolcüsü (scalar / zamanlama denetleyicisi) |
| 3 | name_en | Display controller (scalar / timing controller) |
| 4 | category | display |
| 5 | subcategory | kontrolcü |
| 6 | description | Dijital görüntüyü panelin satır/sütun sürücüsüne çeviren, zamanlama üreten çip sınıfı |
| 7 | physical_component_or_device | fiziksel bileşen (çip) — kart cihazdır (§5/2) |
| 8 | primary_function | Zamanlama üretimi + panel arayüzü sürme |
| 9 | interfaces | Arayüz girişi (D-T05/D-T06) + panel arayüzü çıkışı |
| 10 | protocols | Her iki uçta da protokol ayrı eksende (§4) |
| 11 | power_requirements | UNKNOWN (çip datasheet'i yok) |
| 12 | operating_environment | UNKNOWN |
| 13 | os_dependencies | HAYIR (donanım içi; OS yalnız üst arayüzden görür) |
| 14 | driver_requirements | Üst katman sürücüsü K000 |
| 15 | firmware_dependency | Kontrolcü firmware'i varsa K005 — UNKNOWN |
| 16 | hardware_abstraction_requirement | EVET |
| 17 | audio_relevance | YOK |
| 18 | coremusic_relevance | Bağlantı düzeyi |
| 19 | parent_domain | K001-donanım/donanaım-type |
| 20 | owner_domain | K001 (tür) · tasarım K002 + ADR-063/064 |
| 21 | existing_document | `display/display-kapsam.md` (bu dosya) |
| 22 | evidence | Vault Steward 2026-10-13 onayı · khronos.org HEAD 200 (grafik API standart kurumu — içerik okunmadı ⚠️) |
| 23 | status | PROPOSED · envanter UNKNOWN |
| 24 | confidence | LOW |
| 25 | open_questions | Marka/model UNKNOWN (Q-DK1) |
| 26 | proposed_path | `display/display-dogrulama.md` K-D2 |

**§6 uyarısı (bağlayıcı):** 8 kaydın **hiçbiri** "elde var" demez; hepsi sınıf
kayıtıdır. `status` sütunu karışık durumda **en düşük** etiketi taşır (§7).

## §7 Etiket Disiplini (belgede geçen ≠ üretilmiş)

| # | Etiket | Anlamı | Bu konuda geçerli örnek |
|---|---|---|---|
| 1 | IMPLEMENTED | Kod/fiziksel kanıt diskte vardır | Vault `*.md` dosyaları (sayım kanıtlı) — donanım için YOK |
| 2 | DOCUMENTED | Belge/ADR/kayıt okunmuştur; üretim yok | `donanım-kararlar.md` AMD/NVIDIA satırları · UI dokunma kodu |
| 3 | PLANNED | Karar/spec var, kod/prototip yok | D-T07 donanım katmanı |
| 4 | PROPOSED | Öneri/envanter kaydı; karar verilmedi | D-T01…D-T06 · D-T08 · bu klasörün kendisi |
| 5 | UNKNOWN | Veri hiç yok | Ekran envanteri (Q-DK1) · güç gereksinimleri |

**Kapı kuralı:** Bir satırda tek etiket; karışık durumda en düşük etiket yazılır.
"Üretildi/sahte" iddiası yalnız IMPLEMENTED ile yazılır ve dosya yolu + sayı ister
(`display-dogrulama.md` §7).

## §8 K001 ↔ Komşu Düğüm Sınırı (çakışma yazma kuralı)

| # | Soru | Sahip | K001'in (display) yazabileceği | YAZAMAYACAĞI |
|---|---|---|---|---|
| 1 | Hangi panel sınıfı envanterde? | K001 | tip + sınıf özeti | panelin çalışma davranışı/tasarımı |
| 2 | Görüntü kimden çıkıyor? | K001 (tip) · K000 (sürücü) | kaynak sınıfı (D-T04) | sürücü kodu/registry |
| 3 | EDID okuma kodu nerede? | K000 | yalnız atıf (repo EDID=0) | kod tasarımı |
| 4 | Render/çizim davranışı | K002-electroncis + sürücü | — | hiçbir iddia |
| 5 | UI layout/token | `.ai/ui-design/**` | — | tasarım iddiası |
| 6 | Ses taşıma (HDMI/DP) | K003-audio (`../audio/` §3/B10) | arayüzün yeteneği (sınıf) | ses zinciri davranışı |
| 7 | Ekran PCB/BOM/hesap | K002 + ADR-063/064 | atıf | tasarım |
| 8 | Güç gereksinimi | `../power/` (sınıf) + envanter | tip satırı | rail tasarımı |
| 9 | Dokunma girdisinin UI davranışı | UI katmanı | donanım katmanı kaydı (D-T07) | etkileşim davranışı |
| 10 | Firmware/kontrolcü yükleme | K005-firmware | — | — |

**Tespit (disk):** `.ai/ui-design/screens/T17-monitor-22fhd/` altında **2 dosya**
(`home-dashboard.md` · `welcome-popup.md`) vardır — bunlar **ekran tasarımıdır,
ekran donanımı değildir**; bu dosya onlara içerik üretmez.

## §9 Kabul Kapıları (tanım — uygulama `display-dogrulama.md`)

| # | Kapı | Hedef (kaynak) | Şu anki durum |
|---|---|---|---|
| 1 | K-D1 Envanter | Panel/çıkış/arayüz/dokunma listesi (marka/tip) | ❌ UNKNOWN |
| 2 | K-D2 Tip kaydı tutarlılığı | 26 alan × 8 kayıt, eksiksiz | ✅ bu dosyada uygulandı |
| 3 | K-D3 Etiket kapısı | Her satırda tek etiket | ✅ uygulandı |
| 4 | K-D4 Web kaynak kapısı | HEAD 200 + içerik okunma | ❌ kısmi (kurumsal 200, içerik okunmadı) |
| 5 | K-D5 Çelişki kapısı | Anayasa revizyonu | ❌ APPROVAL REQUIRED (§0) |
| 6 | K-D6 Ölçüm kapısı | Renk/parlaklık/gecikme ölçümü | ❌ açık (S1 yok) |
| 7 | K-D7 Kod kapısı | EDID/DDC/ekran kodu repo'da | ❌ repo ölçümü 0 (§14) |

## §10 Riskler

| # | Risk | Etki | Olasılık | Azaltma |
|---|---|---|---|---|
| 1 | Çelişkili dosya setinin "onaylı" sanılması | kritik | yüksek | §0 + `status: proposed` her dosyada |
| 2 | Panel ↔ arayüz eksenlerinin karışması | orta | yüksek | §4 eşleme kuralı |
| 3 | Bileşen ↔ cihaz karışması (çift sayım) | orta | orta | §5 |
| 4 | Envanter yokken satın alma | kritik | orta | K-D1 |
| 5 | "Belgede var" → "üretildi" sanılması | yüksek | yüksek | §7 etiket disiplini |
| 6 | Q-T2'nin kapatılmış sanılması | yüksek | orta | §0 kapanış adımları — kapanış yalnız anayasa revizyonuyla |
| 7 | HDMI/DP sürüm iddiası uydurmak | yüksek | orta | alan 10 → ⚠️ |
| 8 | Ölçüm kapılarının "geçti" sanılması | kritik | yüksek | S1 kanıtsız "GEÇTİ" yazılmaz |

## §11 Uygulama Sırası (checklist)

| # | Adım | Çıktı | Kapı |
|---|---|---|---|
| 1 | Fiziksel ekran envanteri (panel/çıkış/arayüz/dokunma) | liste | K-D1 |
| 2 | Anayasa + üst kapsam metin revizyonu (onayla) | revizyon kaydı | K-D5 |
| 3 | Q-T2'yi kapat | anayasa satırı | §0/5 |
| 4 | Tip kayıtlarını envantere bağla (marka/model kanıtıyla) | güncellenmiş §6 | K-D2 |
| 5 | Web içeriklerini oku (yalnız HEAD 200 olanlar) | içerik kanıtı | K-D4 |
| 6 | Ölçüm tezgâhını kur (renk/parlaklık/gecikme) | tezgâh | `display-dogrulama.md` §5 |
| 7 | Ölçümleri çalıştır | rapor | `display-dogrulama.md` §3 |
| 8 | Şablonları örnek defterine işle | örnek | `display-ornekler.md` |
| 9 | Etiketleri güncelle (kanıt geldikten sonra) | etiket sütunu | §7 |
| 10 | Sahiplik kararı → owner + ADR | karar kaydı | ADR-097 / §12.1 |

## §12 Sık Yapılan Hatalar

| # | Hata | Belirti | Doğrusu |
|---|---|---|---|
| 1 | Çelişkiyi gizlemek | "anayasa zaten izin veriyor" | §0 — metin hâlâ yasak der |
| 2 | Q-T2'yi kapatmak | Açık soru listesinden silmek | Kapanış yalnız anayasa revizyonuyla |
| 3 | "OLED ekran = HDMI" tek kayıt | Eksen karışması | §4 — ayrı eksenler |
| 4 | Paneli cihaz saymak | Çift sayım | §5 |
| 5 | Sürüm/ölçü uydurmak | "HDMI 2.x — 48 Gbps" vb. | alan 10 → ⚠️ VERIFICATION REQUIRED |
| 6 | UI tasarımını donanım sanmak | T17-monitor referansı | §8/5 |
| 7 | `parent:` anahtarı yazmak | Frontmatter hatası | `parent_id:` |
| 8 | `{...}` / `TBD` bırakmak | Okunamaz tablo | `UNKNOWN` / `⚠️` |
| 9 | Yazımı düzeltmek ("donanım-type") | wiki-link kırılır | yazım korunur |

## §13 Terim Sözlüğü

| # | Terim | Tanım |
|---|---|---|
| 1 | LCD | Sıvı kristal ile ışık geçişini kontrol eden panel sınıfı |
| 2 | LED-aydınlatmalı LCD | Arkadan aydınlatması LED ile yapılan LCD (pazarlama dili "LED ekran") |
| 3 | OLED | Her pikselin kendi ışığını ürettiği panel sınıfı (arkadan aydınlatma yok) |
| 4 | Panel | Görüntüyü üreten fiziksel yüzey (bileşen) |
| 5 | Ekran kontrolcüsü | Zamanlama üreten ve paneli süren çip (scalar / timing controller) |
| 6 | GPU görüntü çıkışı | Görüntüyü üreten donanım bloğu (kaynak) |
| 7 | DisplayPort | Paket tabanlı görüntü arayüzü sınıfı (standart VESA — içerik okunmadı) |
| 8 | HDMI | Paket tabanlı görüntü (+opsiyonel ses) arayüzü sınıfı |
| 9 | EDID | Ekranın kimlik/özellik verisini taşıyan veri yapısı — **okuma kodu repo'da 0** |
| 10 | DDC | Ekranla çift yönlü iletişim kanalı sınıfı — repo kodu 0 |
| 11 | Dokunmatik katman | Dokunmayı algılayan ayrı sensör katmanı |
| 12 | Tamamlanmış cihaz | Dışarıdan satın alınabilir/teslim alınabilir birim (monitör, ekran kartı) |
| 13 | Fiziksel bileşen | Cihazın içindeki parça sınıfı (panel, kontrolcü, arayüz bloğu) |
| 14 | Eksen | Sınıflandırma ekseni (§4/X1–X5) |
| 15 | Tip kaydı | prompt §6 26-alanlı envanter satırı |
| 16 | IMPLEMENTED / DOCUMENTED / PLANNED / PROPOSED / UNKNOWN | §7 etiketleri |
| 17 | S1–S5 | Kanıt seviyeleri (`display-dogrulama.md` §2) |
| 18 | HEAD isteği | Sunucu durumunu içerik indirmeden sorgulama |
| 19 | APPROVAL REQUIRED | Onay bekleyen metin revizyonu durumu |
| 20 | ADR-097 | Yapısal sınıf değişikliği onayı (anayasa §12.1) |

## §14 Kaynakça (bu dosyanın kaynakları — HEAD sonuçları)

| # | Kaynak | Tarih | Durum |
|---|---|---|---|
| 1 | `donanım-anayasa.md` §2 · §10/3 · §12/C10 · §12.1 (511 satır) | 2026-10-13 okundu | ✅ disk kanıtı |
| 2 | `donanaım-type-kapsam.md` §2/A6 · §6/Q-T2 · §7/3-T1 · §12/M6 · §7.3/X3 (512 satır) | 2026-10-13 okundu | ✅ disk kanıtı |
| 3 | `donanım-kararlar.md` satır 90-91 (AMD · NVIDIA satırları) | 2026-10-13 okundu | ✅ disk kanıtı |
| 4 | `.ai/ui-design/screens/T17-monitor-22fhd/` (2 dosya) | 2026-10-13 sayıldı | ✅ ölçüm |
| 5 | K000-isletim-sistemi/electroncis/guc-ve-koruma/ (4 dosya) · K002-electroncis/ (16 dosya) | 2026-10-13 sayıldı | ✅ ölçüm |
| 6 | Repo ölçümü: 700 kaynak dosyada EDID=0 · DDC=0 · HDMI=0 · DisplayPort=0 · VESA=0 · touchscreen/dokunmatik=19 · *.cpp/*.h/*.c/*.xc/*.ino = 0 | 2026-10-13 | ✅ ölçüm (negatif kanıt) |
| 7 | https://vesa.org/ | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| 8 | https://www.hdmi.org/ | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| 9 | https://www.displayport.org/ | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| 10 | https://www.displayhdr.org/ | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| 11 | https://www.cta.tech/ | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| 12 | https://www.khronos.org/ | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| 13 | https://www.jedec.org/ | 2026-10-13 | ❌ HTTP 403 → **kullanılmadı** |
| 14 | https://www.80plus.org/ · https://www.energystar.gov/ · https://www.iec.ch/ | 2026-10-13 | ✅ 200/200/202 — bu dosyada **kullanılmadı** (power/ dosyasına ayrıldı) |
| 15 | https://www.usb.org/ · https://www.amd.com/ · https://www.nvidia.com/ · https://www.arm.com/ | 2026-10-13 HEAD 200 | ⚠️ KULLANILMADI — G6 URL benzersizliği: başka K001/K005 dosyalarında kayıtlı |

**Uydurma kaynak yok:** 6 web URL'si bu oturumda HEAD ile 200 döndürülerek
doğrulanmıştır; 403 döndüren URL ve G6 çakışması olan URL'ler **kullanılmamıştır**.
6 satırın tamamı **kurumsal/organizasyon** kaynağıdır — hiçbirinin içeriği
bu oturumda okunmamıştır (⚠️). **Yeni envanter kesişimi: 0.**

## §15 Çapraz Referanslar

| # | Hedef | İlişki |
|---|---|---|
| 1 | `[[display/index]]` | Bu konunun künyesi |
| 2 | `[[donanaım-type/index]]` | Üst künye · children + §GRUPLAR (2026-10-13 eklendi) |
| 3 | `[[donanaım-type-kapsam]]` | Üst kapsam — çelişkili metin (§0) |
| 4 | `donanım-anayasa.md` | Anayasa — çelişkili metin (§0) |
| 5 | `[[power-kapsam]]` | Kardeş sınıf (güç) — aynı çelişki kaydı |
| 6 | `[[audio-kapsam]]` · `[[hdd-kapsam]]` · `[[ssd-kapsam]]` | Kardeş sınıflar |
| 7 | `display-dogrulama.md` | Ölçüm kapıları K-D1…K-D7 |
| 8 | `display-ornekler.md` | Uygulama şablonları |
| 9 | ADR-063 · ADR-064 (accepted) | Tasarım standartları — **yalnız varlık iddiası** |
| 10 | K002-electroncis/ · K000-isletim-sistemi/electroncis/ | Devre tasarımı sahibi (sınır) |

---

## §16 Dürüstlük Bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez. Bu dosya 6 adet HEAD-200 web URL'si (§14),
4 adet okunmuş disk dosyası ve 3 adet repo ölçümü üzerine kuruludur; 403 döndüren
URL ve G6 çakışması olan URL'ler kullanılmamıştır. **Hiçbir fiziksel ölçüm
YAPILMAMIŞTIR**; ekran envanteri UNKNOWN'dur. 8 tip kaydının **hiçbiri** "elde
var" iddiası taşımaz. Çelişki (§0) gizlenmemiş, kapanış adımları listelenmiştir;
anayasa ve üst kapsam metni **revize edilmemiştir** → `PROPOSED — APPROVAL
REQUIRED`. Datasheet/ölçü değeri uydurulmamış; bilinmeyenler `UNKNOWN` /
`⚠️ VERIFICATION REQUIRED` ile işaretlenmiştir. `{...}` ve `TBD` yer tutucusu
kullanılmamıştır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
