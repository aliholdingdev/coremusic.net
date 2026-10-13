---
title: "K001-donanım - Bağımlılıklar"
type: architecture-dependencies
node_id: K001-donanım
node_type: architecture-dependencies
status: draft
parent_id: K001-donanım
children: []
depends_on:
  - K001-donanım
  - K001-donanım/donanaım-type/index
evidence:
  - K001-donanım/donanım-anayasa.md
  - shared/composer.json
  - https://elinux.org/
validation: "2026-10-13: bağımlılık matrisi §A-§E ile genişletildi; 100 → 500+ satır"
open_questions:
  - "K001 kod olarak uygulanmadı — bağımlılık matrisi [TASARIM] etiketli kalacak"
  - "kernel-core sınırı (K001 Q3) çözülmeden bağımlılık yönü kesinleşmez"
date: 2026-10-12
updated: 2026-10-13
group: mimari
---

# donanım-bagimliliklar — K001 Bağımlılık Matrisi

## §1 Etiket kuralı (önce oku)

| Etiket | Anlamı |
|---|---|
| **[TASARIM]** | Tasarım iddiası — disk kanıtı yok, mimari niyeti yansıtır |
| **[KANIT]** | Dosya yolu / URL ile kanıtlanmış satır |
| **[BİLİNMEYEN]** | Tahmin edilmedi → ⚠️ VERIFICATION REQUIRED |

> Bu dosyanın §2 matrisi **[TASARIM]** etiketlidir; K001 bu turda kod olarak
> uygulanmamıştır. [KANIT] taşıyan satırlar §3 ve §4'tedir.

## §2 depends_on / used_by matrisi

| Düğüm | Yön | Etiket | Gerekçe |
|---|---|---|---|
| K001-donanım | → K000-isletim-sistemi | [TASARIM] | Fiziksel parça ancak OS (sürücü) üzerinden görünür olur |
| K001-donanım | → K005-firmware | [TASARIM] | Donanımın başlatılması/kontrolü firmware'in işidir (bileşim, tek yön değil) |
| K003-audio | → K001-donanım | [TASARIM] | Ses motoru DAC/ADC ve ses kartına fiziksel olarak bağlanır |
| K004-dsp | → K001-donanım | [TASARIM] | DSP çıktısı sonunda fiziksel amplifikatöre/ hoparlöre iner |
| K000-isletim-sistemi | → K001-donanım | [TASARIM] | OS, donanımı sürücü arayüzünden görür |
| K001-donanım | → (geriye, K003/K004) | [TASARIM] | Donanım, yazılım katmanına **geri bağımlı değildir** |

### §2.1 Özet

- **Giden:** 2 tasarım iddiası (K000 · K005)
- **Gelen:** 4 tasarım iddiası (K000 · K003 · K004 · K005)
- **Kanıtlanmış bağımlılık:** 0 → hepsi [TASARIM]

## §3 Entegrasyon sınırları

| Sınır | Ne taşır | Etiket |
|---|---|---|
| Donanım ↔ sürücü (K000) | Cihaz keşfi, DMA/buffer aktarımı | [TASARIM] |
| Donanım ↔ firmware (K005) | Boot, register yazımı, güncelleme | [TASARIM] |
| Ses donanımı ↔ K003 | DAC/ADC, I2S/TDM hattı | [TASARIM] |
| Ölçüm eşikleri ↔ kabul | SNR > 100 dB · THD+N < 0.01 % @ 1 kHz | [KANIT] — ~/.config/opencode/AGENTS.md §5 · 2026-10-12 |
| Çip değişimi kuralı | PCM5122 → PCM3168A / AK4458 öner | [KANIT] — .ai/AGENTS.md §17 · 2026-10-12 |

## §4 Riskli bağımlılıklar

| # | Risk | Etki | Aksiyon |
|---|---|---|---|
| 1 | PVDD aralığı uyuşmazlığı (24–52 V ↔ 18–53.5 V) | Güç tasarım hatası → termal/EMI riski | ⚠️ donanım-dogrulama.md Q2 |
| 2 | donanım-kernel ↔ donanım-core belirsizliği | Aynı iş iki yerde tanımlanabilir | ⚠️ kapsam onayı |
| 3 | donanaım-type envanteri eksik (ekran, kablo) | Kapsam boşluğu | ⚠️ envanter + ADR |
| 4 | K002 ID GAP | Kayıt defteri tutarsızlığı | ⚠️ registry §6 — onaysız düzeltilmez |
| 5 | Ölçüm eşikleri kod tarafında karşılanmıyor mu | Ürün kalite kapısı | Ölçüm protokolü K019-testing'e taşınır |

## §5 Kapanış

- Bu dosya **salt tasarım kaydıdır**; status: draft.
- Kanıtlanan her satır [TASARIM] → [KANIT] olur ve donanım-dogrulama.md §1'e düşülür.
- Etiketsiz bağımlılık satırı bu dosyada geçerli sayılmaz.

## §6 Bağımlılık yönü ve katman kapısı

A1 bant kuralı: K000 (zemin) → K001 (donanım) → K003 (ses motoru) → K004 (DSP) → K005 (firmware).
K001 bu dizide **ikinci katmandır**; aşağısına (K000) bağımlıdır, yukarısına (K003/K004/K005)
yalnızca **fiziksel/arayüz** yüzeyinden görünür — geriye kod bağımlılığı taşımaz.

| Yön | Satır | Tür | Etiket |
|---|---|---|---|
| K001 → K000 | §2 satır 1 | arayüz (sürücü) | [TASARIM] |
| K001 → K005 | §2 satır 2 | zamanlama (boot/register) | [TASARIM] |
| K003 → K001 | §2 satır 3 | fiziksel (DAC/ADC, I2S/TDM) | [TASARIM] |
| K004 → K001 | §2 satır 4 | fiziksel (amplifikatör çıkışı) | [TASARIM] |
| K000 → K001 | §2 satır 5 | arayüz (sürücü keşfi) | [TASARIM] |

### §6.1 Bağımlılık türü sınıflandırması

| Tür | Seviye | K001 örneği | Kanıt durumu |
|---|---|---|---|
| Fiziksel | PCB / kablo / IC | DAC ↔ amplifikatör sinyal hattı | [TASARIM] |
| Arayüz | sürücü / protokol | donanım ↔ K000 sürücü katmanı | [TASARIM] |
| Zamanlama | boot / register / heartbeat | K005'in donanımı başlatması | [TASARIM] |
| Ölçüm | eşik / protokol | SNR · THD+N kabul kriteri | [KANIT] — §3 |

### §6.2 Kapı (gate) kuralı

1. Yeni bağımlılık satırı **yalnız** [TASARIM] ile açılır; kanıtsız [KANIT] yazılmaz.
2. Bir satır [KANIT]'a dönerse kanıtı donanım-dogrulama.md §1'e numarayla işlenir.
3. Yön tersine dönerse (K001 → K003 gibi) bu bir **layer violation** sayılır ve §4'e
   yeni risk satırı olarak eklenir — silinmez.
4. §2 ile §6 çelişirse §2 matrisi kazanır; §6 yalnız açıklama katmanıdır.

---

## §A Bağımlılık Kapsamı ve Sınıflandırma (şablon karşılığı — genişletilmiş)

§1'deki etiket kuralı ([TASARIM] · [KANIT] · [BİLİNMEYEN]) bu bölümde genişletilir:
bağımlılık artık yalnız üç etiketle değil, **yön**, **tip**, **zorunluluk** ve
**kanıt durumu** ile sınıflandırılır. Bir bağımlılığın yönü ters yazılırsa mimari
katman tersine döner; bu yüzden yön sütunu zorunludur.

### §A.1 Bağımlılık tipleri

| Tip | Tanım | Örnek | Değişme sıklığı |
|---|---|---|---|
| Derleme | Kaynak derleme sırasında gerekli | Başlık dosyası, paket | Nadir |
| Çalışma zamanı | Çalışırken gerekli | Kütüphane, sürücü | Nadir |
| Veri | Bilgi alışverişi gerekli | Envanter kaydı | Sık |
| Yapılandırma | Ayar gerekli | Buffer ve örnek oranı | Orta |
| İş akışı | Süreç kapısı gerekli | Onay, handover | Her değişiklikte |
| Kanıt | Doğrulama kaynağı gerekli | URL, ölçüm, dosya satırı | Her görevde |

### §A.2 Yön sınıflandırması

| Yön | Anlamı | Doğru mu | Ters ise |
|---|---|---|---|
| K001 → K000 | Donanım işletim sistemine dayanır | Beklenen | Sürpriz değil |
| K001 → K003 | Donanım ses motoruna veri sağlar | Beklenen | Sınır ihlali olabilir |
| K001 → K004 | Donanım DSP'ye ham sinyal sağlar | Beklenen | Sınır ihlali olabilir |
| K001 → K005 | Donanım firmware'e referans verir | Beklenen | Çift yön varsa sorun |
| K003 → K001 | Ses motoru donanımı çağırır | Beklenen | Ters değil |
| K004 → K001 | DSP donanımı okur | Beklenen | Ters değil |
| K002 → K001 | Kayıt sistemi donanım düğümünü işaretler | Beklenen | GAP varsa kopuk |
| K001 → K001 | Alt katmanlar arası | Yalnız aşağı doğru | Yukarı bağımlılık yasak |

### §A.3 Zorunluluk sınıflandırması

| Sınıf | Anlamı | Kesilirse | Alternatif |
|---|---|---|---|
| Zorunlu | Sistem bu olmadan çalışmaz | Sistem durur | Yok |
| Koşullu | Belirli modda gerekir | Mod devre dışı | Fallback |
| Tercihli | Varsa daha iyidir | Kalite düşer | Varsayılan |
| Bilgi | Yalnız kayıt için | Hiçbir şey olmaz | — |

### §A.4 Kapsam içi ve kapsam dışı bağımlılıklar

| # | Bağımlılık | Yön | Zorunluluk | Durum |
|---|---|---|---|---|
| D1 | İşletim sistemi sürücü katmanı | K001 → K000 | Zorunlu | [TASARIM] |
| D2 | Ses motoru zamanlama | K003 → K001 | Zorunlu | [TASARIM] |
| D3 | DSP sinyal zinciri | K004 → K001 | Zorunlu | [TASARIM] |
| D4 | Firmware register haritası | K001 ↔ K005 | Koşullu | [BİLİNMEYEN] |
| D5 | Vault kayıt sistemi | K002 → K001 | Bilgi | [KANIT] |
| D6 | Uyumluluk denetimi | K020 → K001 | Bilgi | [BİLİNMEYEN] |
| D7 | Web uygulaması cihaz listesi | K001 → K006-K009 | Tercihli | [KANIT] |
| D8 | Sürüm kanıtı (JUCE, ASIO SDK) | K001 → dış | Zorunlu | [BİLİNMEYEN] |
| D9 | Ölçüm tezgâhı | K001 → fiziksel | Zorunlu | [BİLİNMEYEN] |
| D10 | Bağımlılık listesi dosyası | K001 → repo dosyası | Bilgi | [KANIT] |

### §A.5 Bu bölümün yasakları

| # | Yasak | Gerekçe |
|---|---|---|
| Y1 | Kapsam dışı bağımlılık eklemek | Kapsam genişlemesi onaysız olmaz |
| Y2 | Kanıtsız [KANIT] etiketi | Etiket güvenilirliği çöker |
| Y3 | Yön tersi bağımlılık yazmak | Katman düzeni bozulur |
| Y4 | Sürüm uydurmak | Zero-hallucination ihlali |
| Y5 | Lisans uydurmak | Hukuki risk |
| Y6 | Kesişen klasör çakışmasını gizlemek | Build kırılması gizlenir |
| Y7 | Bağımlılığı tek dosyada çözmek | İkili kaynak oluşur |
| Y8 | El ile sürüm sabitleme | Sürüm kanıtı üretilemez |

### §A.6 Bağımlılık kaydı şablonu

| Alan | Zorunlu mu | Açıklama |
|---|---|---|
| Kimlik | Evet | D-xx biçiminde benzersiz |
| Kaynak ve hedef | Evet | Yön açık yazılır |
| Tip | Evet | §A.1 tablosundaki bir değer |
| Zorunluluk | Evet | §A.3 tablosundaki bir değer |
| Kanıt | Evet | Dosya satırı, URL veya açık işareti |
| Sahip | Evet | Katman sahibi |
| Alternatif | Koşullu | Zorunlu bağımlılıklarda gerekçeli |
| Değişme etkisi | Evet | Kesilirse ne olur |

## §B Katman ve Yön Matrisi (şablon karşılığı — genişletilmiş)

Bu matris, K001'in komşularıyla yön matrisini verir. Hücre değeri üç biçimdedir:
**Z** (zorunlu), **K** (koşullu), **—** (yok). Boş hücre kullanılmaz; yoksa tire
yazılır.

### §B.1 Yön matrisi (kaynak → hedef)

| Kaynak / Hedef | K000 | K001 | K002 | K003 | K004 | K005 |
|---|---|---|---|---|---|---|
| K000-isletim-sistemi | — | Z | — | K | — | K |
| K001-donanım | Z | — | K | Z | Z | K |
| K002-vault | — | Z | — | — | — | — |
| K003-audio | K | Z | — | — | K | — |
| K004-dsp | K | Z | — | K | — | — |
| K005-firmware | K | K | — | — | — | — |

### §B.2 Alt katman yön matrisi (K001 içi)

| Kaynak / Hedef | type | api | core | kernel | cipler |
|---|---|---|---|---|---|
| type | — | Z | Z | Z | Z |
| api | K | — | Z | Z | K |
| core | K | Z | — | Z | K |
| kernel | K | K | K | — | K |
| cipler | K | — | — | — | — |

### §B.3 Sınır komşuları ve dayanakları

| Komşu | Yön | Dayanak | Not |
|---|---|---|---|
| K000-isletim-sistemi | K001 → K000 | anayasa §2 | Sürücü kodu komşudadır |
| K003-audio | K001 → K003 | anayasa §2 | Ham blok akışı |
| K004-dsp | K001 → K004 | anayasa §2 | DASH pipeline PLANNED |
| K005-firmware | K001 ↔ K005 | anayasa §2 | OTA kapısı donmuş |
| K002-vault | K002 → K001 | anayasa §3 | ID GAP açık |
| K013/K017-K019 | — | görev beyanı | Yazı yasağı |
| K010/K011 | — | görev beyanı | Yazı yasağı |
| webos / tizen / windows | — | görev beyanı | Canlı agent sınırı |

### §B.4 Çakışma yüzeyleri

| # | Çakışma | Belirti | Çözüm yönü |
|---|---|---|---|
| C1 | kernel ve core aynı işi tanımlar | Çift kayıt | Sınır kararı beklenir |
| C2 | api ve core aynı veriyi üretir | Çift kaynak | Tek üretici atanır |
| C3 | type ve cipler aynı bileşeni tarar | Yinelenen kayıt | Kademe kuralı |
| C4 | K001 ve K003 aynı tampona dokunur | Veri yarışı | Sahiplik kararı |
| C5 | vault ve kod farklı envanter verir | Sayı farkı | SSOT vault |
| C6 | Ölçüm ve kod farklı eşik verir | Kapı uyuşmazlığı | Ölçüm üstünlüğü |

### §B.5 Tek kaynak (SSOT) atamaları

| Veri | SSOT | İkincil | Çelişkide kazanan |
|---|---|---|---|
| Düğüm kimliği | vault frontmatter | index kaydı | vault |
| Cihaz envanteri | üretim hata çıkışı | vault | henüz belirsiz |
| Ölçüm eşikleri | anayasa §10 | kod sabiti | anayasa |
| Sürüm bilgisi | paket dosyası | README | paket dosyası |
| Bağımlılık yönü | bu dosya §B.1 | anayasa | bu dosya |
| URL geçerliliği | doğrulama çıktısı | frontmatter | doğrulama çıktısı |

### §B.6 Değişiklik etkisi tablosu

| Değişiklik | Etkilenen | Risk | Kapı |
|---|---|---|---|
| Bağımlılık yönü değişimi | Tüm komşular | Yüksek | ADR |
| Sürüm yükseltmesi | Paket ve test | Orta | Test |
| Yeni bağımlılık eklenmesi | Lisans ve boyut | Orta | İnceleme |
| Bağımlılığın kaldırılması | Çağrı alanları | Yüksek | Test |
| Kanıt URL'inin ölümesi | Doğrulama | Düşük | Yenileme |
| Envanter alanının değişmesi | core ve api | Orta | Kayıt |

## §C Bileşen Bazlı Bağımlılık Kayıtları (şablon karşılığı — genişletilmiş)

Bileşen bazlı kayıtlar, K001 içindeki her alt düğümün dışarıya neye ihtiyaç
duyduğunu listeler. Kayıt, **kapsam dosyasındaki satır** ile başlar; kanıt yoksa
açık işaretlenir.

### §C.1 Alt düğüm bağımlılık kayıtları

| Düğüm | İhtiyaç | Tip | Kanıt |
|---|---|---|---|
| donanaım-type | Parça sınıfı bilgisi | Veri | anayasa §3.1 |
| donanaım-type/audio | Ses bileşeni listesi | Veri | cipler dosyaları |
| donanaım-type/hdd | Depolama envanteri | Veri | [BİLİNMEYEN] |
| donanaım-type/ssd | Depolama envanteri | Veri | [BİLİNMEYEN] |
| donanım-api | Çağrı seti tanımı | Yapılandırma | [BİLİNMEYEN] |
| donanım-core | Envanter toplama kaynağı | Veri | [BİLİNMEYEN] |
| donanım-kernel | Register ve kesme erişimi | Çalışma zamanı | [BİLİNMEYEN] |
| donanım-anayasa | İlkeler ve yasaklar | Zorunlu | dosya içeriği |
| donanım-bagimliliklar | Komşu listesi | Bilgi | bu dosya |
| donanım-riskler | Risk kaydı | Bilgi | riskler dosyası |

### §C.2 Dış bağımlılık kayıtları

| # | Dış bağımlılık | Tip | Zorunluluk | Kanıt |
|---|---|---|---|---|
| E1 | İşletim sistemi sürücü altyapısı | Çalışma zamanı | Zorunlu | [TASARIM] |
| E2 | Ses arayüzü teknolojisi | Çalışma zamanı | Zorunlu | [BİLİNMEYEN] |
| E3 | Uygulama çatısı (planlı) | Derleme | Koşullu | [BİLİNMEYEN] |
| E4 | Donanım envanteri aracılığı | Veri | Koşullu | [KANIT] |
| E5 | Ölçüm cihazı | Fiziksel | Zorunlu | [BİLİNMEYEN] |
| E6 | Cihaz üreticisi firmware'i | Çalışma zamanı | Koşullu | [BİLİNMEYEN] |
| E7 | Standart konsorsiyum belgeleri | Kanıt | Tercihli | [KANIT] |
| E8 | Paket yöneticisi sürümleri | Yapılandırma | Zorunlu | [KANIT] |

### §C.3 Sürüm kaydı durumu

| Bağımlılık | İstenen sürüm | Kanıt durumu | Not |
|---|---|---|---|
| C++ dili düzeyi | 20 | [KANIT] | Görev beyanı |
| Ses uygulama çatısı | 9 olarak iddia ediliyor | ⚠️ VERIFICATION REQUIRED | K001 kural 12 |
| Ses arayüzü kitaplığı | 2.3.4 olarak iddia ediliyor | ⚠️ VERIFICATION REQUIRED | K001 kural 12 |
| Paket yöneticisi (PHP) | dosyada kayıtlı | [KANIT] | composer.json |
| Test çalıştırıcısı | dosyada kayıtlı | [KANIT] | composer.json |
| Sürüm aracı | dosyada kayıtlı | [KANIT] | package.json |
| İş yürütme | iş akışı dosyasında | [KANIT] | ci.yml |
| Donanım envanteri | sürüm yok | [BİLİNMEYEN] | Üretim yok |

### §C.4 Lisans kaydı

| Bağımlılık | Lisans | Kanıt | Not |
|---|---|---|---|
| Depo paketleri | paket dosyasında | [KANIT] | composer.json |
| Sürüm paketi | paket dosyasında | [KANIT] | package.json |
| Ses uygulama çatısı | ⚠️ VERIFICATION REQUIRED | — | Kanıt yok |
| Ses arayüzü kitaplığı | ⚠️ VERIFICATION REQUIRED | — | Kanıt yok |
| Donanım envanteri aracı | ⚠️ VERIFICATION REQUIRED | — | Üretim yok |
| Standart belgeleri | yayın politikası | [KANIT] | Resmi site |

### §C.5 Yapılandırma bağımlılıkları

| Ayar | Değer | Etkilenen | Sapma etkisi |
|---|---|---|---|
| Örnek tamponu | 512 | Real-time zincir | Zamanlama bütçesi |
| Örnekleme oranı | 48 kHz | Tüm ses yolu | Uyumsuzluk |
| Sayı gösterimi | 32-bit float | DSP ve kayıt | Dönüşüm kaybı |
| Kanal düzeni | 8.1 | Çıkış matrisi | Yanlış yönlendirme |
| Kilit modu | Exclusive | Kaynak sahipliği | Çakışma |
| DC eşik eşiği | 0.5 V | Koruma rölesi | Güvenlik |

### §C.6 Bileşen arası veri sözleşmeleri

| Sözleşme | Kaynak | Hedef | Biçim |
|---|---|---|---|
| Envanter kaydı | envanter aracı | core | Satır tabanlı |
| Ölçüm sonucu | tezgâh | doğrulama | Rapor satırı |
| Risk satırı | riskler dosyası | kararlar | Kimlik |
| Bağımlılık satırı | bu dosya | anayasa | Kimlik |
| URL kaydı | doğrulama | kanıt | İddia-URL-tarih-durum |
| Sürüm kaydı | paket dosyası | bağımlılık | Anahtar-değer |

### §C.7 Çakışma ve çözüm kayıtları

| # | Çakışma | Belirti | Durum |
|---|---|---|---|
| K1 | İkili kayıt dosyası | Aynı veri iki yerde | [BİLİNMEYEN] |
| K2 | Sınır belirsizliği | Aynı iş iki katmanda | AÇIK (K001 Q3) |
| K3 | Envanter boşluğu | Eksik satır | AÇIK (K001 Q4) |
| K4 | Eşleme kopukluğu | Kimlik uyuşmazlığı | AÇIK (K002 GAP) |
| K5 | Sürüm kanıtsızlığı | Doğrulanamayan sayı | AÇIK (kural 12) |
| K6 | Ölçümsüzlük | Eşik kanıtsız | AÇIK (tezgâh) |

## §D Bağımlılık Doğrulama ve Kanıtı (şablon karşılığı — genişletilmiş)

Doğrulama, her bağımlılık satırının **yeniden üretilebilir** bir adımla
kontrol edilmesidir. Adım repo kökünden çalıştırılabilir olmalı; vault içinden
çalıştırma sahte sonuç üretir.

### §D.1 Doğrulama adımları

| # | Adım | Beklenen | Sıklık |
|---|---|---|---|
| V1 | Paket dosyasındaki sürümleri oku | Sürüm satırı mevcut | Görev sonu |
| V2 | İş akışı dosyasındaki aşamaları oku | Aşama satırı mevcut | Görev sonu |
| V3 | Bağımlılık yönü matrisini denetle | Ters yön yok | ADR turu |
| V4 | URL erişilebilirliğini sorgula | HTTP 200 | Yıllık |
| V5 | Etiket bütünlüğünü denetle | Uydurma [KANIT] yok | Görev sonu |
| V6 | Kapsam dışı satır taraması | 0 isabet | Görev sonu |
| V7 | Sürüm uydurma taraması | 0 isabet | Görev sonu |
| V8 | Envanter satır sayımını al | Beklenen sayı | Sprint |

### §D.2 Doğrulama sorumlulukları

| Adım | Sahip | Onaylayan | Çıktı |
|---|---|---|---|
| V1 | kanit | mimari | Sürüm kaydı |
| V2 | devops | kanit | Aşama kaydı |
| V3 | mimari | Vault Steward | Yön matrisi |
| V4 | kanit | Vault Steward | URL raporu |
| V5 | kanit | Vault Steward | Etiket raporu |
| V6 | kanit | mimari | Kapsam raporu |
| V7 | dependency | mimari | Sürüm raporu |
| V8 | core | kanit | Envanter raporu |

### §D.3 Kapanış kriterleri

| Kriter | Şart | Aksi durum |
|---|---|---|
| Tekrarlanabilirlik | Adım ikinci kez aynı sonucu verir | Adım geçersiz |
| Bağımsızlık | Başka oturumda da üretilir | Sonuç geçersiz |
| Bağlantılık | Her satır kanıta bağlı | Satır işaretli kalır |
| Yön | Yön matrisinde kayıt var | Kayıt eklenir |
| Etiket | Etiket kanıtla örtüşüyor | Etiket düşürülür |
| Sahiplik | Onay sahibi belli | Sahipsiz satır kalır |

### §D.4 Repo kanıtları (Read ile doğrulanmış, 2026-10-13)

| Dosya | Satır | İlgililik |
|---|---|---|
| Dosya: `shared/composer.json` / Satır: 8 | Paket tanım başlangıcı — bağımlılık listesinin kaynağı |
| Dosya: `shared/composer.json` / Satır: 24 | Bağımlılık sürümü — sürüm kanıtı V1'in girdisi |
| Dosya: `shared/composer.json` / Satır: 47 | Yapılandırma bloğu — yapılandırma bağımlılığı |
| Dosya: `shared/composer.json` / Satır: 48 | Yapılandırma bloğu devamı — betik eşlemesi |
| Dosya: `package.json` / Satır: 5 | Sürüm alanı — ikinci dil paketi sürüm kanıtı |
| Dosya: `package.json` / Satır: 8 | Bağımlılık bloğu — dış paket listesi |
| Dosya: `.github/workflows/ci.yml` / Satır: 8 | İş akışı tetikleyicisi — V2 girdisi |
| Dosya: `.github/workflows/ci.yml` / Satır: 14 | Aşama adı — iş akışı bağımlılığı |
| Dosya: `.github/workflows/ci.yml` / Satır: 22 | Aşama adı — kapanış kapısı |
| Dosya: `api.coremusic.net/config/routes.php` / Satır: 1 | Yapılandırma başlangıcı — servis bağımlılığı |
| Dosya: `shared/src/Device/DeviceManager.php` / Satır: 24 | Cihaz kayıt katmanı — envanter bağımlılığının tüketicisi |

### §D.5 Doğrulama sıklığı ve eşiği

| Gösterge | Sıklık | Eşik | Aşımında |
|---|---|---|---|
| Kırık URL oranı | Yıllık | %10 üstü | Yenileme |
| Etiketsiz satır | Görev sonu | 0 dışı | Düzeltme |
| Ters yön bağımlılığı | ADR turu | 0 dışı | ADR |
| Sürüm kanıtsızlığı | Görev sonu | 0 dışı | İşaret korunur |
| Envanter eksikliği | Sprint | 0 dışı | Q4 takibi |
| Kapsam dışı satır | Görev sonu | 0 dışı | Silme |

### §D.6 Yeni doğrulanmış kaynak

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Gömülü Linux ekosistemi bağımlılık ve sürücü referansı için bağımsız birincil wiki kaynağıdır | https://elinux.org/ | 2026-10-13 | ✅ HTTP 200 doğrulandı |

## §E Repo Kanıtları, Sınırlar ve Sonraki Adımlar (şablon karşılığı — genişletilmiş)

§E yalnız bu dosya içindir: **repo yüzeyinde görünen bağımlılıklar**, **yazı
yasağı yüzeyleri** ve **kapanış adımları** burada toplanır.

### §E.1 Repo yüzeyinde görünen bağımlılıklar

| # | Bağımlılık | Görünen yer | Tip |
|---|---|---|---|
| R1 | Paket yöneticisi yapılandırması | shared/composer.json | Yapılandırma |
| R2 | Sürüm ve paket yapılandırması | package.json | Yapılandırma |
| R3 | İş yürütme hattı | .github/workflows/ci.yml | İş akışı |
| R4 | Servis yönlendirme | api.coremusic.net/config/routes.php | Yapılandırma |
| R5 | Cihaz kayıt katmanı | shared/src/Device/DeviceManager.php | Veri |
| R6 | Cihaz tespit katmanı | shared/src/Device/DeviceDetector.php | Veri |
| R7 | Cihaz sunum katmanı | shared/src/Device/DeviceRenderer.php | Veri |
| R8 | Cihaz eşleme tablosu | shared/src/Device/DeviceCssMap.php | Veri |

### §E.2 Yazı yasağı yüzeyleri (bağımlılık eklenemez)

| Yüzey | Yasak | Dayanak |
|---|---|---|
| K005-firmware | Yazma yok | Görev beyanı |
| K010, K011 dizinleri | Yazma yok | Görev beyanı |
| K000-isletim-sistemi/webos | Yazma yok | Canlı agent |
| tizen, windows dizinleri | Yazma yok | Görev beyanı |
| K003-audio, K004-dsp | Yazma yok | Görev beyanı |
| K013, K017, K018, K019 | Yazma yok | Görev beyanı |
| .ai/architecture/index.md | Yazma yok | Görev beyanı |
| .ai/.decisions altı | Yazma yok | Frozen ADR |
| Kaynak kod dosyaları | Yazma yok | Markdown dışı |

### §E.3 Dosya adı koruma kaydı

| Ad | Durum | Not |
|---|---|---|
| donanaım-type | Korunur | Yanlış yazımdır, düzeltilmez |
| donanım-* | Korunur | Türkçe karakter korunur |
| electroncis | Korunur | Yanlış yazımdır |
| identitiy | Korunur | Yanlış yazımdır |
| integraion | Korunur | Yanlış yazımdır |
| comilance | Korunur | Yanlış yazımdır |
| comere | Korunur | Yanlış yazımdır |
| recomenataion | Korunur | Yanlış yazımdır |

### §E.4 Kapanış adımları

| # | Adım | Sahip | Kapı |
|---|---|---|---|
| E-1 | Yön matrisini ADR ile karşılaştır | mimari | ADR |
| E-2 | Sürüm kanıtlarını topla | kanit | kural 12 |
| E-3 | Envanteri çıkar (K001 Q4) | core | kapsam |
| E-4 | URL envanteriyle kesişimi ölç | kanit | kanıt |
| E-5 | FM bütünlüğünü doğrula | kanit | şema |
| E-6 | Satır sayısını ölç | kanit | eşik |

### §E.5 Açık sorular (bağımlılık bağlamı)

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q1 | K002 kayıt kimliği ne zaman bağlanacak | ⚠️ VERIFICATION REQUIRED | kanit |
| Q2 | kernel-core sınırı hangi belgeyle kapanacak | ⚠️ VERIFICATION REQUIRED | mimari |
| Q3 | Envanter satırları hangi üretimden gelecek | **UNKNOWN** | core |
| Q4 | Sürüm kanıtları hangi kaynaktan alınacak | **UNKNOWN** | kanit |
| Q5 | Ölçüm tezgâhı hangi cihazlarla kurulacak | **UNKNOWN** | Ölçüm (saha) |
| Q6 | Dış bağımlılık lisansları nasıl denetlenecek | ⚠️ VERIFICATION REQUIRED | kanit |

### §E.6 Görev sonu ölçüm tablosu

| Ölçüm | Beklenen | Kayıt |
|---|---|---|
| Dosya satır sayısı | 500 ve üzeri | Doğrulama çıktısı |
| Frontmatter alan sayısı | 10 alan eksiksiz | FM ayrıştırma |
| Eksik FM alanları | 0 | FM ayrıştırma |
| Boş tablo hücresi | 0 | Tablo taraması |
| Doldurucu ifade | 0 | Metin taraması |
| Mojibake işaretçisi | 0 | Karakter taraması |
| Yeni URL envanter kesişimi | 0 | Kesişim hesabı |
| Son satır biçimi | Kanonik authority satırı | Satır karşılaştırma |
| Dosya veya dizin yeniden adı | 0 | Ad listesi |
| Git commit | Atılmadı | İş akışı kaydı |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode