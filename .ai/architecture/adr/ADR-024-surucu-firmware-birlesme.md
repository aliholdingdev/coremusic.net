---
title: "ADR-024 — Sürücü-Firmware Birleşmesi (k-surucu → k2-surucu, firmware → K1.f)"
type: adr
category: architecture
date: 2026-09-24
updated: 2026-09-24
tarih: 2026-09-24
durum: önerildi
status: proposed
version: 1.0.0
kaynak: "3 turlu agent tartışması 20 persona"
authority: "Karar metninin kendisi (Guardrail #16 adr-template iskeleti)"
governance: Red Team · Human Mode · Truth Mode
---

# ADR-024 — Sürücü-Firmware Birleşmesi

**Durum:** önerildi · **Tarih:** 2026-09-24
**Karar Veren:** 3 turlu agent tartışması 20 persona (uzlaşma)
**İlgili ADR'ler:** [[ADR-023-hibrit-derinlik]] · [[ADR-025-k8-2-k15-siniri]] · [[ADR-026-sayim-birimi-5000]]
**Konum:** .ai/architecture/adr/ADR-024-surucu-firmware-birlesme.md (ayrı seri — numara notu: .ai/.decisions kayıtlarındaki ADR-024-ecosystem-modular-docs ile aynı numara, farklı slug; bu dosya .ai/architecture/adr/ serisindedir)

---

## §1 Başlık

**Sürücü-Firmware Birleşmesi:** k-surucu/ klasörünün 12 dosyası hiçbir dosya silinmeden k2-surucu/ klasörüne taşınır; k-surucu/ klasörü boşaltılarak referans dışı bırakılır. firmware/ klasörü olduğu gibi KALIR ve K1 Donanım katmanının alt katmanı **K1.f** olarak adlandırılır. Taşımadan önce ve sonra wiki-link taraması zorunludur.

---

## §2 Durum

| Alan | Değer |
|------|-------|
| Durum | **önerildi** (proposed) |
| Tarih | 2026-09-24 |
| Kaynak | 3 turlu agent tartışması 20 persona |
| Bağlayıcılık | Evet — klasör yapısı ve K1.f adlandırması bu karara bağlıdır |
| Disk gözlemi (2026-09-24) | Taşıma uygulanmış görünüyor: k-surucu/ = 0 dosya, k2-surucu/ = 14 dosya (12 taşınan + README + CLAUDE), firmware/ = 8 dosya — üçü de §5.1 beklenenleriyle birebir |
| Onay akışı | Vault Steward ✅ → Tech Lead ⏳ → Arch Lead ⏳ |

---

## §3 Bağlam (Context)

### §3.1 Mevcut Durum

Vault'ta sürücü içeriği iki klasöre bölünmüştü; firmware ise üçüncü bir yerde duruyordu:

| Klasör | Dosya Sayısı (2026-09-24 glob) | İçerik | Sorun |
|--------|-------------------------------:|--------|-------|
| k-surucu/ | 0 | (eski hali: 12 sürücü dosyası — asio, wasapi, alsa, pipewire, core-audio, usb-audio-class, network, bluetooth, driver-stack, buffer, latency, index) | Ayrık klasör; K2 ile ilişkisi yalnız isim benzerliği |
| k2-surucu/ | 14 | README.md, CLAUDE.md + 12 taşınan sürücü dosyası | İki kabuk (README/CLAUDE) vs içerik ayrımı |
| firmware/ | 8 | bootloader, dsp-firmware, gpio-control, i2s-driver, mcu-support, usb-audio-firmware, xmos-firmware, index | Kapsamsız: hangi K'ye ait olduğu yazılı değil |

**Tartışma anındaki (karar öncesi) durum:** k2-surucu/ 2 dosyalık boş kabuk (README, CLAUDE); içerik k-surucu/ tarafında 12 dosya; firmware/ 8 dosya bağımsız.

### §3.2 Sorun Tanımı

Üç soru tartışmaya girmiştir:

1. İki sürücü klasörü (k-surucu, k2-surucu) neden vardır ve hangisi kalıcıdır?
2. İçerik mi taşınır, kabuk mu? (Hangi yönde birleşim?)
3. firmware/ hangi katmanın parçasıdır — ayrı bir K mi, alt katman mı?

### §3.3 Kısıtlar

| # | Kısıt | Açıklama | Kaynak |
|---|-------|----------|--------|
| 1 | 21 katman sabit | firmware için yeni ana katman (örn. K21) AÇILAMAZ | 3 tur uzlaşma / [[adlandirma-kurali]] |
| 2 | Silme yasağı | Vault içinden dosya silmek revert + CRITICAL tetikler | In-Place Refactoring #4 |
| 3 | Link bütünlüğü | Taşıma sonrası kırık wiki-link kalmaz | [[CLAUDE.md]] guardrail |
| 4 | Dependency Rule | firmware ↔ K1 dosya düzeyinde bağımlılık kurmaz; K2 sürücüleri firmware'e dokunmaz | [[katman-baglilik-matrisi]] |
| 5 | Sayım bütünlüğü | K1.f, [[katman-sayim-rehberi]] K1 satırında sayılır (ayrı satır değil) | [[ADR-026-sayim-birimi-5000]] |
| 6 | Kanıt zorunluluğu | K1.f harfli segmenti yalnız firmware/ disk kanıtına dayanır (kanıt türü i) | [[adlandirma-kurali]] §3.3 |

### §3.4 Web Araştırması Raporu

| Alan | Değer |
|------|-------|
| Web Search Query | driver documentation folder structure firmware separate module naming convention |
| Web Search Konusu | Sürücü ve firmware dokümantasyonunun katmanlı vault'ta yerleştirilmesi |
| Web Search Bağlamı | Donanım-yakın yazılım (driver/firmware) ile donanım (K1) arasındaki sınır |
| Web Search Kısa Açıklama | Firmware, donanımın çalıştırılabilir uzantısıdır; sürücü ise işletim sistemi tarafı |
| Web Search Uzun Açıklama | IA'da benzer sorumluluk (sürücü) tek çatıda toplanır; farklı yaşam döngüsü olan (firmware) alt-katman olarak ayrılır |
| Web Search Paragraf | CoreMusic: sürücü → K2 (tek klasör), firmware → K1.f (donanımın alt katmanı); bu ayrım firmware'in cihazın parçası niteliğini korur |
| Web Search Sonucu | İki klasörü birleştir + firmware'i K1'e bağla |
| Web Search Alınan Karar | k-surucu/* → k2-surucu/; firmware/ → K1.f |
| Web Search Sonuç | 21 katman sabit korunur, içerik kaybı yok |

*⚠️ VERIFICATION REQUIRED: Web araştırması canlı oturum kaydı .ai/log.md append ile güncellenmelidir.*

---

## §4 Karar (Decision)

| # | Karar Maddesi | Ayırıcı Söz |
|---|---------------|-------------|
| 1 | k-surucu/ içindeki **12 dosya** k2-surucu/ içine **taşınır; hiçbir dosya silinmez** | "Taşı, silme" |
| 2 | k2-surucu/ kabuğu (README.md, CLAUDE.md) yerinde korunur; Dosya Haritası satır edit ile güncellenir | "Kabuk korunur" |
| 3 | k-surucu/ klasörü boşaltılır ve yeni referans almaz; eski yollara gelen linkler k2-surucu/README.md'ye yönlendirilir | "Eski yol = ölü yol" |
| 4 | firmware/ klasörü OLDUĞU GİBİ KALIR (taşınmaz, birleştirilmez, silinmez) | "Firmware yerinde" |
| 5 | firmware/, K1 Donanım katmanının alt katmanı olarak **K1.f** adlandırılır (harfli segment) | "K1.f tek istisna" |
| 6 | K1.f'in harfli segmenti kanıtla sınırlıdır: yalnız firmware/ disk varlığı (kanıt türü i); K2.f, K9.f gibi yeni harfli segmentler uydurulamaz | "Harf uydurmak yasak" |
| 7 | Taşımadan ÖNCE ve SONRA wiki-link taraması zorunludur (baseline + düzeltme + sıfır kalıntı) | "Link taraması kalemidir" |
| 8 | firmware ↔ K1: dosya düzeyinde bağımlılık YOK; K2 sürücüleri firmware'e dokunmaz (K2 → K1 yönü korunur) | "Yön K2 → K1" |

### §4.1 Neden Bu Seçenek? (Rationale)

| Gerekçe | Açıklama |
|---------|----------|
| Tek sürücü klasörü | İki sürücü klasörü (isim benzerliğiyle) kafa karıştırıcıydı; tek K2 klasörü K matrisiyle birebir örtüşür |
| İçerik taşındı, kabuk korundu | 12 dosyalık gerçek içerik silinemezdi (In-Place #4); README/CLAUDE zaten K2'nin kendi kabuğu |
| firmware K1.f | Firmware, cihazın (donanımın) çalıştırılabilir parçasıdır; K1'e alt katman olarak bağlanır ve 21 katman sabitliği korunur (yeni K açılmaz) |
| Harfli segment kanıtla | K1.f tek istisnadır; genelleme yasağı sonraki K*.f uydurmalarını kapatır |
| Link taraması zorunlu | Taşıma en sık kırık wiki-link üretir; girişi ve çıkışı kanıt üretir |

### §4.2 Teknik Detaylar

**Taşıma tablosu (12 dosya — silme yok):**

| # | Kaynak (k-surucu/) | Hedef (k2-surucu/) | İşlem |
|---|---------------------|---------------------|-------|
| 1 | index.md | k2-surucu/index.md | move |
| 2 | asio-drivers.md | k2-surucu/asio-drivers.md | move |
| 3 | wasapi-exclusive.md | k2-surucu/wasapi-exclusive.md | move |
| 4 | alsa-native.md | k2-surucu/alsa-native.md | move |
| 5 | pipewire-modern.md | k2-surucu/pipewire-modern.md | move |
| 6 | core-audio-macos.md | k2-surucu/core-audio-macos.md | move |
| 7 | usb-audio-class.md | k2-surucu/usb-audio-class.md | move |
| 8 | network-audio-drivers.md | k2-surucu/network-audio-drivers.md | move |
| 9 | bluetooth-a2dp.md | k2-surucu/bluetooth-a2dp.md | move |
| 10 | driver-stack-mimari.md | k2-surucu/driver-stack-mimari.md | move |
| 11 | buffer-management.md | k2-surucu/buffer-management.md | move |
| 12 | latency-optimization.md | k2-surucu/latency-optimization.md | move |

**K1.f (firmware — 8 dosya, kalıcı):**

| # | Dosya | Not |
|---|-------|-----|
| 1 | bootloader.md | K1.f |
| 2 | dsp-firmware.md | K1.f |
| 3 | gpio-control.md | K1.f |
| 4 | i2s-driver.md | K1.f (firmware tarafı; K2'deki sürücüden farklı katman) |
| 5 | mcu-support.md | K1.f |
| 6 | usb-audio-firmware.md | K1.f |
| 7 | xmos-firmware.md | K1.f |
| 8 | index.md | K1.f indeksi |

---

## §5 Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| A | **Hiçbirleşme:** k-surucu/ ve k2-surucu/ yan yana kalsın; firmware ayrı kalsın | Hiçbir iş yok; sıfır risk | İki sürücü klasörü kafa karıştırıcı; firmware'in K'sı belirsiz; sayım ikiye bölünür | **RED:** Kalıcı ikilik; K matrisinde tek K2 karşılığı yok; link denetimi iki klasörü ayrı izlemek zorunda |
| B | **Ters birleşim:** k2-surucu/ kabuğunu k-surucu/ içine al (14 → 12+2) | Sürücü klasörü k-surucu adında kalır | K2 matris karşılığı zayıf (klasör adı K2 değil); index.md ve github-referanslari zaten k2-surucu'yu hedef gösterir | **RED:** K matrisi k2-surucu'yu K2 karşılığı olarak tanımlar; ters yön link ve referans düzeltmesini iki kat büyütür |
| C | **firmware'i K1'in içine al (k1-donanim/ altına taşı)** | Tek dosya ağacı | 8 dosya silinmeden taşınır ama firmware/ disk kanıtı (kanıt türü i) K1.f için tek dayanak; klasör yok edilirse K1.f kanıtsız kalır | **RED (ikincil):** Taşıma teknik olarak mümkün, ancak K1.f'in kanıt dayanağı firmware/ klasörünün varlığıdır; ayrıca In-Place #4 gereği ek link taraması yükü |
| D | **firmware için yeni ana katman (K21 veya K2.0)** | İsim net | 21 katman sabitliği bozulur; sayım iskeleti, matris ve tüm tablolar yeniden yazılır | **RED:** Bağlayıcı kısıt (§3.3 madde 1) — 20 persona tek sesle reddetti |
| E | **Birleşim (SEÇİLEN):** içerik k2-surucu/'ya + firmware → K1.f | Tek sürücü klasörü, 21 sabit, silme yok, kanıt korunur | Taşıma + link taraması işi gerekir | — **SEÇİLDİ** — tüm kısıtları karşılar |

### §5.1 Seçenek Karşılaştırma Matrisi

| Kriter | A Hiçbirleşme | B Ters | C firmware→K1 içi | D K21 | **E Birleşim** |
|--------|--------------|--------|------------------|-------|----------------|
| 21 katman sabit | ✅ | ✅ | ✅ | ❌ | ✅ |
| Silme yok | ✅ | ✅ | ✅ | kısmi | ✅ |
| Tek sürücü klasörü | ❌ | ✅ | ❌ | ❌ | ✅ |
| K1.f kanıtı korunur | kısmi | kısmi | ❌ | ❌ | ✅ |
| Link taraması yükü | düşük | yüksek | yüksek | çok yüksek | **orta** |
| 20 persona uzlaşması | red | red | red | red | **kabul** |

---

## §6 Sonuçlar (Consequences)

### §6.1 Olumlu Sonuçlar

- Tek sürücü klasörü: k2-surucu/ = K2 matris karşılığı (2026-09-24: 14 dosya).
- 12 dosya kaybolmadı (move); k-surucu/ = 0 dosya — üç bağımsız ölçümle doğrulandı (glob, read, link taraması).
- firmware/ yerinde: 8 dosya → K1.f; 21 katman sabitliği korundu.
- K1.f sayımı K1 satırına bindi: [[katman-sayim-rehberi]] K1 (a=12) satırında başlık sütunu k1-donanim + firmware.
- Link taraması hem giriş (baseline) hem çıkış (sıfır kalıntı) kanıtı üretti.

### §6.2 Olumsuz Sonuçlar

- Eski k-surucu yollu wiki-link'i yazan yazar hata yapar; düzeltme k2-surucu/README.md'ye yönlendirilir — geçiş dönemi kafa karışıklığı.
- K1.f harfli segmenti genel şablondan sapar (K{n}.a.b dışında tek istisna) — yeni harf talepleri her seferinde ADR ister.
- firmware ↔ K1 dosya düzeyinde bağımlılık yok kuralı yorum payı bırakır (klasör K1'e bağlı ama dosyalar değil) — denetim kuralı elle uygulanır.
- Numara uzayı notu: .ai/.decisions/index.md'deki ADR-024-ecosystem-modular-docs ile numara paylaşımı; iki seri birleştirilmedi. ⚠️ VERIFICATION REQUIRED: birleştirme istenirse üst karar gerekir.
- README/index'teki gelecek adım satırları (Dosya Haritası, link notları) bu ADR'yi yazan görevce DEĞİŞTİRİLMEDİ — güncelleme ayrı, satır-edit tabanlı görevdir (In-Place #4).

### §6.3 Riskler

| # | Risk | Olasılık | Etki | Mitigasyon |
|---|------|---------|------|------------|
| 1 | Taşıma sırasında dosya kaybı | Düşük | Kritik | 12 dosya move listesi (§4.2) + git diff ile adet denetimi (14 = 12+2) |
| 2 | Kırık wiki-link kalması | Orta | Yüksek | Zorunlu iki aşamalı tarama (§7.1); kabul: k-surucu referansı kalıntısı = 0 |
| 3 | K1.f yerine K2.f/K9.f uydurma | Orta | Yüksek | Genelleme yasağı (§4 madde 6) + adlandırma regex denetimi |
| 4 | firmware/ taşınması için baskı | Düşük | Orta | §5 alternatif C red gerekçesi; kanıt (i) firmware/ klasörüne bağlı |
| 5 | i2s-driver.md katman tartışması (firmware mi, K2 sürücü mü) | Orta | Düşük | Bu dosya firmware/ içinde K1.f'te sayılır; K2'deki sürücü dokümanlarıyla çakışma yok (farklı dosyalar) |
| 6 | link taraması atlanırsa | Orta | Yüksek | Betik kontrolü + kabul kriteri §7.1; taramasız taşıma = denetim başarısız |

---

## §7 Uygulama (Implementation)

### §7.1 Adımlar

| # | Adım | Sorumlu | Durum |
|---|------|---------|-------|
| 1 | Baseline kırık-link raporu (taşıma ÖNCESİ) | Vault Steward | ✅ (mevcut rapor) |
| 2 | 12 dosyanın k-surucu/ → k2-surucu/ move işlemi | Vault Steward | ✅ 2026-09-24 (disk gözlemi §2) |
| 3 | k2-surucu/README.md Dosya Haritası satır edit | Vault Steward | ✅ (index.md §5 kaydı) |
| 4 | Link taraması SONRASI: k-surucu referanslı kalıntı = 0 | MO (vault-updater) | ✅ (index.md §5: tek referans düzeltildi) |
| 5 | firmware/ → K1.f adlandırmasının adlandirma/katman-sayim dosyalarına işlenmesi | Architect agent | ✅ (bu görev) |
| 6 | index.md + github-referanslari.md + katman-baglilik-matrisi.md ADR-024 notları | Vault Steward | ✅ (gözlemlendi) |
| 7 | scripts/katman-sayim.ps1 ile firmware=8 / k-surucu=0 regresyon kalemi | DevOps | ⏳ |
| 8 | Onay akışı: Tech Lead → Arch Lead | Vault Steward | ⏳ |

### §7.2 Geri Dönüş Planı

Birleşim sakat bulunursa: (1) taşınan 12 dosya k2-surucu/ → k-surucu/ yönünde move edilir (yine silinmez), (2) k2-surucu/README.md Dosya Haritası geri alınır, (3) link taramasıyla eski yollar yeniden bağlanır, (4) yeni bir ADR ile alternatif A veya B yeniden değerlendirilir, (5) log.md append ile revert kaydedilir. firmware/ için geri dönüş yoktur — K1.f kararı başka bir ADR ile supersede edilmedikçe geçerlidir.

### §7.3 Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward (yazar) | Bayram Ali | 2026-09-24 | ✅ |
| Tech Lead | — | — | ⏳ |
| Arch Lead | — | — | ⏳ |

---

## §8 İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[adlandirma-kurali]] | §6: k-surucu/k2-surucu/firmware klasör kuralı + K1.f |
| [[katman-sayim-rehberi]] | §7 disk ölçümü (k-surucu=0, firmware=8) + betik kalemleri |
| [[ADR-023-hibrit-derinlik]] | K1.f harfli segmentinin derinlik/sınır bağlamı |
| [[ADR-025-k8-2-k15-siniri]] | Benzer sınır kararı (servis vs boru hattı sahibi) |
| [[ADR-026-sayim-birimi-5000]] | Sayım birimi; firmware'in K1 satırında sayılması |
| [[katman-baglilik-matrisi]] | K2 satırı (k2-surucu 14 dosya) + K2→K1 yönü |
| [[index]] | §5 ADR-024 Birleşim Kaydı |
| [[github-referanslari]] | ADR-024 notları (k-surucu = 0) |

---

## §9 Salt Okunur Doğrulama

| # | Kontrol | Beklenen | Uygunsuzsa |
|---|---------|----------|------------|
| 1 | k-surucu/ dosya sayısı | 0 | Taşıma yapılmamış / geri alınmış |
| 2 | k2-surucu/ dosya sayısı | 14 (12 + README + CLAUDE) | Eksik taşıma |
| 3 | firmware/ dosya sayısı | 8 | K1.f kanıtı zedelenmiş |
| 4 | k-surucu referanslı wiki-link taraması | 0 eşleşme | Kırık link — §7.1 adım 4 |
| 5 | K2.f / K9.f gibi uydurma harfli segment | 0 eşleşme | Genelleme yasağı ihlali |

---

## §10 Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 1.0.0 | 2026-09-24 | İlk yazım — 3 tur / 20 persona uzlaşması: k-surucu→k2-surucu birleşimi + firmware→K1.f + link taraması zorunluluğu |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-24
**Mode:** Red Team · Human Mode · Truth Mode
