---
title: "CoreMusic — Katman Adlandırma Kuralı (K{n}.a.b.c)"
type: architecture-rule
category: architecture
date: 2026-09-24
updated: 2026-09-24
tarih: 2026-09-24
durum: önerildi
status: proposed
version: 1.0.0
kaynak: "3 turlu agent tartışması 20 persona"
authority: "SSOT — mimari adlandırma kuralı (.ai/CLAUDE.md §5 hiyerarşisine bağlı)"
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Katman Adlandırma Kuralı

**Durum:** önerildi · **Tarih:** 2026-09-24 · **Kaynak:** 3 turlu agent tartışması 20 persona
**Konum:** .ai/architecture/adlandirma-kurali.md · **İlgili:** [[katman-sayim-rehberi]] · [[ADR-023-hibrit-derinlik]]

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index]] · [[brain.md]] · [[katman-baglilik-matrisi]] · [[frontend-restructuring-plan]] · [[.templates/index]]

---

## §1 Amaç ve Kapsam

Bu dosya, CoreMusic mimarisinin 21 ana katmanı (K0-K20) altındaki tüm düğümler için **tek geçerli adlandırma dilini** tanımlar. Üç turlu tartışma (20 persona) sonucunda bağlayıcı hale gelen kurallar burada toplanır; başka bir vault dosyası adlandırma kuralı ilan edemez (SSOT).

| Kapsam | Kapsam Dışı |
|--------|-------------|
| K{n}, K{n}.a, K{n}.a.b, K{n}.a.b.c düğüm adları | Bileşen içi sınıf/fonksiyon adlandırması (→ backend şablonları) |
| Bileşen kimlikleri (K0-01 … K{n}-NN) ↔ düğüm eşlemesi | Katman bağımlılık izinleri (→ [[katman-baglilik-matrisi]]) |
| L → K dönüşüm kuralı (eski L-şemasından) | A0-A5 gruplarının içerik sahipliği |
| k-surucu / k2-surucu / firmware klasör kuralları | Düğüm sayımı ve tavan aralığı (→ [[katman-sayim-rehberi]]) |
| İhlal denetimi kuralları ve sonuçları | ADR metinleri (→ adr/ altındaki ADR-023…026) |

### §1.1 Bağlayıcı Karar Kaynağı

| Karar | Kaynak | Durum |
|-------|--------|-------|
| K{n}.a.b.c şablonu zorunlu, 4. seviye kanıt koşullu | 3 tur / 20 persona uzlaşma | bağlayıcı |
| K7.0.x ara numara reddi | 3 tur / 20 persona uzlaşma | bağlayıcı |
| k-surucu/* → k2-surucu/ birleşimi (12 dosya taşınır, silinmez) | 3 tur / 20 persona uzlaşma | bağlayıcı |
| firmware/ klasörü kalır → K1.f alt katmanı | 3 tur / 20 persona uzlaşma | bağlayıcı |
| K8.2 = servis ucu, K15 = boru hattı sahibi | 3 tur / 20 persona uzlaşma | bağlayıcı (ADR-025) |
| 21 ana katman sabit, K16-K20 bağımsız | 3 tur / 20 persona uzlaşma | bağlayıcı |
| Sayım birimi = DÜĞÜM, hedef 5000+ | 3 tur / 20 persona uzlaşma | bağlayıcı (ADR-026) |

---

## §2 Adlandırma Şablonu — K{n}.a.b.c

### §2.1 Genel Gramer

~~~
K{n}[.a][.b][.c]
  │    │   │   └─ 4. seviye: kanıt KOŞULLU (yalnız §3.3 kanıtlarından biri ile)
  │    │   └───── 3. seviye: b (alt bileşen grubu)
  │    └───────── 2. seviye: a (alt katman)
  └────────────── 1. seviye: n = 0…20 (21 ana katman, SABİT)
~~~

| Seviye | Biçim | Anlamı | Zorunluluk | Sayıma dahil mi? |
|--------|-------|--------|------------|------------------|
| 1 | K{n} | Ana katman (kök) | 21 tane, sabit | Evet (kök 21) |
| 2 | K{n}.a | Alt katman (birim sorumluluk alanı) | Evet | Evet |
| 3 | K{n}.a.b | Alt bileşen grubu | Evet | Evet |
| 4 | K{n}.a.b.c | Kanıtlı alt bileşen | **Yalnız kanıtla** | Evet (kanıtlıysa) |
| 5+ | K{n}.a.b.c.d | — | **YASAK** (4 seviye tavan) | Hayır — denetim ihlali |

### §2.2 Biçimsel Doğrulama (regex)

Geçerli düğüm adı şu düzenli ifadeyle eşleşmelidir:

~~~text
^K([0-9]|1[0-9]|20)(\.[1-9][0-9]*){0,3}$
~~~

| Kural | Açıklama | İhlali |
|-------|----------|--------|
| Önek | Yalnız büyük harf K | Geçersiz |
| n aralığı | 0-20 (21 katman sabit) | K21+ reddedilir |
| Segment sıfır yasağı | Hiçbir ara segment 0 olamaz (\.[1-9][0-9]*) | §4 K7.0.x red gerekçesi |
| Maksimum derinlik | 3 nokta = 4 seviye | 5. seviye reddedilir |
| Segment yazımı | Ondalık, süresiz genişleyebilir (a=1…99) | Kısaltma/eksi写 yasak |

### §2.3 Geçerli ve Geçersiz Örnekler

| # | Örnek | Sonuç | Gerekçe |
|---|-------|-------|---------|
| 1 | K11 | ✅ Geçerli | Kök katman |
| 2 | K11.4 | ✅ Geçerli | 2. seviye alt katman |
| 3 | K11.1.4 | ✅ Geçerli | 3. seviye (04_Components) |
| 4 | K11.1.4.13 | ✅ Geçerli | 4. seviye + kanıt: [[frontend-restructuring-plan]] §2.2 satırı (c-player.css) |
| 5 | K7.0.1 | ❌ Reddedildi | Ara segment 0 — §4 |
| 6 | K7.0.x | ❌ Reddedildi | Ara numara + joker karakter — §4 |
| 7 | K11.1.4.13.2 | ❌ Reddedildi | 5. seviye yasak |
| 8 | k11.1.4 (küçük k) | ❌ Reddedildi | Önek büyük harf K |
| 9 | K-11.1 | ❌ Reddedildi | Tire yasak |
| 10 | K21.1 | ❌ Reddedildi | 21 katman sabit (K0-K20) |
| 11 | K11.01.4 | ❌ Reddedildi | Sıfır dolgusu yasak (01 → 1) |
| 12 | K8.2 | ✅ Geçerli | Servis ucu alt katmanı (ADR-025) |
| 13 | K1.f | ✅ Geçerli | firmware alt katmanı — harfli segment kanıtlı istisna (§6.3) |
| 14 | L11.1.4.13 | ⚠️ Dönüşüm gerekli | L şeması → §7 kuralı ile K11.1.4.13 |

### §2.4 Katman Bazlı Kök Adları (21 sabit)

| Kök | Ad | Kök | Ad | Kök | Ad |
|-----|----|-----|----|-----|-----|
| K0 | İşletim Sistemi | K7 | Middleware | K14 | Ağ & İletişim |
| K1 | Donanım | K8 | Servis | K15 | Medya & Streaming |
| K2 | Sürücü | K9 | API & Routing | K16 | Class AB Amplifikatör |
| K3 | Ses İşleme Motoru | K10 | Uygulama | K17 | Güç Kaynağı ±35V |
| K4 | Yapay Zeka | K11 | Kullanıcı Deneyimi | K18 | Termal Tasarım |
| K5 | Veri Yönetimi | K12 | İzleme & Log | K19 | PCB Tasarım |
| K6 | Güvenlik | K13 | CI/CD & Deploy | K20 | BOM & Üretim |

**K16-K20 bağımsızlık:** K16-K20, K0-K15 hiyerarşisine tabi değildir; tek arayüz bağımlılıkları K1'e ↕ yönlüdür ([[katman-baglilik-matrisi]] §2.2). Adlandırma onlarda da aynı K{n}.a.b.c şablonunu kullanır.

### §2.5 Klasör Adı ↔ Düğüm Adı Uyumu

| Düğüm | Klasör (disk) | Kural |
|-------|---------------|-------|
| K{n} | .ai/architecture/k{n}-<slug>/ | Kök klasör slug'ı Türkçe/İngilizce olabilir; düğüm adı her zaman K{n} |
| K{n}.a | k{n}-<slug>/ içindeki alt başlık veya dosya grubu | Aynı klasörde yaşar |
| K{n}.a.b | k{n}-<slug>/<konu>.md | Dosya = 3. seviye düğüm |
| K{n}.a.b.c | README/index bileşen tablosu satırı veya plan satırı | Disk dosyası zorunlu değil; kanıt zorunlu |
| K1.f | .ai/architecture/firmware/ | firmware/ klasörü K1'e bağlı kalır (§6.3) |
| K2.* | .ai/architecture/k2-surucu/ | k-surucu/ içeriği buraya taşınır (§6.1) |

---

## §3 Seviye Kuralları

### §3.1 1.-3. Seviye (Kanıtsız Kurulabilir)

| Seviye | Kurulma koşulu | Düşürme (demote) koşulu |
|--------|----------------|--------------------------|
| K{n} | 21 katman listesinde olmak (K0-K20) | Yapısal değişiklik = yeni ADR |
| K{n}.a | Bir sorumluluk alanı olduğunu README'de yazmak | Kapsam birleşmesi (örn. §6 k-surucu birleşimi) |
| K{n}.a.b | En az bir içerik dosyası veya tablo satırı | Dosya silinirse düğüm de silinir (In-Place #4 ihlali yoksa) |

### §3.2 Sayısal Sınırlar (Taban Kapasite)

| Seviye | Üst sınır | Kaynak |
|--------|-----------|--------|
| K{n} | 21 | Bağlayıcı karar — 21 ana katman sabit |
| K{n}.a | Katman sayım tablosundaki "alt" değeri | [[katman-sayim-rehberi]] §4 |
| K{n}.a.b | Katman sayım tablosundaki "orta" değeri (her a başına) | [[katman-sayim-rehberi]] §4 |
| K{n}.a.b.c | Katman sayım tablosundaki "tavan" değeri + kanıt zorunluluğu | [[ADR-023-hibrit-derinlik]] |

### §3.3 4. Seviye Kanıt Üçlüsü (Zorunlu)

Bir K{n}.a.b.c düğümü **yalnızca** aşağıdaki kanıtlardan **en az biri** varsa kurulabilir. Kanıt yoksa düğüm yazılmaz; yazılmışsa 3. seviyeye düşürülür.

| # | Kanıt Türü | Geçerli Konum | Geçerlilik Testi |
|---|-----------|---------------|------------------|
| (i) | Diskte katman MD dosyası | .ai/architecture/k{n}-*/*.md | Glob ile dosya var mı? |
| (ii) | README/index bileşen tablosu satırı | k{n}-*/README.md veya index.md bileşen haritası | Tablo satırı var mı? (örn. k2-surucu README §2 K2-01…K2-12) |
| (iii) | Plan satırı | [[frontend-restructuring-plan]] §2.1-§2.2 | Satır numarası gösterilebiliyor mu? (örn. §2.2 L11.1.4.13 = c-player.css) |

**Kanıt kuralları:**

1. Kanıt (i) diskte ise düğüm kalıcıdır; README güncellenmemiş olsa da (i) tek başına yeterlidir.
2. Kanıt yalnız (iii) ise (plan satırı) düğüm **PLANNED** etiketiyle işaretlenir; dosya üretilince (i) kazanılır.
3. Üç kanıt da yoksa 4. seviye **yazım yasağı** — sayım scripti bu düğümü saymaz.
4. Kanıt, kanıt tarihiyle birlikte sayım raporunda listelenir (şeffaflık → [[ADR-026-sayim-birimi-5000]]).
5. Kanıt silinirse (dosya/README/plan satırı) düğüm bir sonraki sayım turunda düşer; sessizce tutulamaz.

### §3.4 Derinlik Denetimi

| Denetim | Yöntem | İhlal Sonucu |
|---------|--------|--------------|
| 5. seviye tespiti | Regex: ikinci noktadan sonra segment sayısı > 3 | Düğümü 4. seviyeye indir + log |
| Kanıtsız 4. seviye | §3.3 üç kanıtı da negatif | Düğümü sil + CRITICAL log |
| Sıfırlı ara segment | Regex: \.0\. geçişi | §4 gereği reddet |
| 21+ katman | K21, K21a vb. | Reddet + mimari ADR iste |

---

## §4 K7.0.x Ara Numara Reddi

### §4.1 Karar

**K7.0, K7.0.1, K7.0.x gibi ara numaralandırma KESİNLİKLE REDDEDİLMİŞTİR.** Hiçbir katmanda ara ( boşluk-doldurucu ) numara kullanılamaz; numaralandırma her zaman 1'den başlar ve atlanarak ilerler (boşluk = henüz kurulmamış düğüm, 0 ile doldurulmaz).

### §4.2 Gerekçeler

| # | Gerekçe | Açıklama |
|---|---------|----------|
| 1 | 0 dolgusu yapay şişirmedir | Kullanılmayan slotu doldurmak, sayımı şişirir (ADR-023 Aday A reddi ile aynı mantık) |
| 2 | Regex ihlali | \.[1-9][0-9]* kuralı sıfırlı segmenti zaten reddeder; istisna açmak kuralı çökertir |
| 3 | Kararsız eşleme | K7.0.x şeması okuyucuya "x" jokeri bırakır; karşılığı diskte yoktur |
| 4 | Katman dizini tutarsızlığı | k7-middleware/ dosyaları (14 dosya) 1'den numaralanır; 0 numaralı dosya yoktur |
| 5 | Sayım belirsizliği | Ara numara sayılır mı sayılmaz mı belirsiz → ADR-026 kanıt şeffaflığı ihlali |

### §4.3 Red Edilen Alternatifler

| Alternatif | Ne idi? | Neden Red |
|------------|---------|-----------|
| K7.0.x ara katman | "Henüz atanmamış yer tutucu" | Yer tutucu = kanıtsız düğüm; sayıma girip girmeyeceği belirsiz |
| K7.0 → K7.1, K7.2… (0'dan başlatma) | 0. alt katman fikri | 2. seviye 1'den başlar (regex) |
| K7.a, K7.b harfli ara | Harfli ara numara | Yalnız K1.f (§6.3) kanıtlı istisna var; genelleştirilemez |
| Uçlarda nokta (K7.) | Kısaltma yazımı | Regex ihlali, kırık link üretir |

### §4.4 Geçerli K7 Kullanımı

| ✅ Doğru | ❌ Yanlış |
|----------|-----------|
| K7 (kök) | K7.0 |
| K7.1 (OriginCheck) | K7.0.1 |
| K7.1.3 | K7.0.x |
| K7.10 (Validation — §2.2'deki 10. middleware) | K7.1.03 (sıfır dolgusu) |

**İhlal sonucu:** K7.0.x deseni bir dosyada tespit edilirse düğüm adı 1'den başlayacak şekilde yeniden adlandırılır, kırık wiki-linkler tarama ile bulunur, log.md'ye CRITICAL girilir.

---

## §5 Bileşen Kimlikleri ↔ Düğüm Eşlemesi (K0-01 … K0-20)

### §5.1 Genel Kural

README/index bileşen haritasındaki **K{n}-NN** kimlikleri tek başına a.b'yi vermez; eşleme README tablosunda saklanır. İstisnasız kural: her K{n}-NN satırında karşılık gelen K{n}.a.b düğümü yazılır.

~~~text
K{n}-NN  ↔  K{n}.a.b   (eşleme README bileşen tablosunda, tek satırda)
~~~

### §5.2 K0-01 … K0-20 Zorunlu Eşleme Tablosu (K0 örneği kanonik)

Kapsam kanıtları: [[index]] §1 K0 satırı (Windows, Linux, macOS, RPi5, ReactOS, Docker, Cross-Platform API, IPC, Memory, Threading) + [[frontend-restructuring-plan]] §2.1 L0.1-L0.5 satırları.

| Kimlik | Bileşen | Kapsam Kanıtı | Karşılık gelen düğüm | Kanıt türü |
|--------|---------|---------------|----------------------|------------|
| K0-01 | Windows platformu (WASAPI/ASIO ev sahibi) | plan §2.1 L0.1 | K0.1.1 | (iii) |
| K0-02 | Linux platformu (ALSA, PipeWire, D-Bus ev sahibi) | plan §2.1 L0.2 | K0.2.1 | (iii) |
| K0-03 | macOS platformu (CoreAudio, AVFoundation ev sahibi) | plan §2.1 L0.3 | K0.3.1 | (iii) |
| K0-04 | Raspberry Pi 5 (ARM64, I2S, GPIO ev sahibi) | plan §2.1 L0.4 | K0.4.1 | (iii) |
| K0-05 | ReactOS (experimental tier) | plan §2.1 L0.5 | K0.5.1 | (iii) |
| K0-06 | Docker | [[index]] §1 K0 satırı | K0.6.1 | (ii) |
| K0-07 | Container runtime ortak katmanı | [[index]] §1 K0 satırı (Docker) | K0.6.2 | (ii) |
| K0-08 | Cross-Platform API | [[index]] §1 K0 satırı | K0.7.1 | (ii) |
| K0-09 | Sistem çağrı soyutlaması | Cross-Platform API kapsamı | K0.7.2 | (ii) |
| K0-10 | Çekirdek servis erişim API'si | [[CLAUDE.md]] §5 K0 satırı | K0.7.3 | (ii) |
| K0-11 | IPC (process arası iletişim) | [[index]] §1 K0 satırı | K0.8.1 | (ii) |
| K0-12 | Donanım keşfi (OS seviyesi enumerasyon) | [[CLAUDE.md]] §5 K0 satırı | K0.8.2 | (ii) |
| K0-13 | Performans sayaçları (OS sayaçları) | [[CLAUDE.md]] §5 K0 satırı | K0.8.3 | (ii) |
| K0-14 | Bellek yönetimi | [[index]] §1 K0 satırı (Memory) | K0.9.1 | (ii) |
| K0-15 | Bellek havuzu / allocator | Memory kapsamı türevi | K0.9.2 | (ii) |
| K0-16 | Zamanlayıcı ve saat servisi | Çekirdek servis kapsamı | K0.9.3 | (ii) |
| K0-17 | İş parçacığı yönetimi | [[index]] §1 K0 satırı (Threading) | K0.9.4 | (ii) |
| K0-18 | Süreç yönetimi | Çekirdek servis kapsamı | K0.10.1 | (ii) |
| K0-19 | Sanal dosya sistemi (VFS) katmanı | Çekirdek servis kapsamı | K0.10.2 | (ii) |
| K0-20 | Servis başlatıcı (init benzeri) | Çekirdek servis kapsamı | K0.10.3 | (ii) |

**Uyum kontrolü:** K0 taban kapasitesi [[katman-sayim-rehberi]] §4'e göre 10 alt (a=1…10) × 7 orta (b=1…7) = 70 3. seviye yuvasıdır; tabloda a∈[1..10], b∈[1..7] ihlali yoktur.

### §5.3 Diğer Katmanlar İçin Şablon

| Adım | İşlem |
|------|-------|
| 1 | k{n}-*/README.md bileşen haritası satırlarını oku (K{n}-NN sütunu) |
| 2 | Her NN için karşılık gelen K{n}.a.b düğümünü satıra ekle (§5.1 kuralı) |
| 3 | README'de karşılığı olmayan NN için VERIFICATION REQUIRED yaz, uydurma düğüm kurma |
| 4 | 20 kimlikli K0 tablosu kanonik örnektir; diğer katmanlar aynı sütun düzenini kullanır |

---

## §6 k-surucu / k2-surucu / firmware Kuralı

### §6.1 Birleşme Kararı: k-surucu/* → k2-surucu/

**12 dosya taşınır, HİÇBİR dosya silinmez.** k2-surucu/ bugün 2 dosyalık boş kabuktur (README.md, CLAUDE.md); içerik k-surucu/ tarafındadır.

| # | Taşınacak dosya (k-surucu/) | Hedef (k2-surucu/) | İşlem |
|---|-----------------------------|---------------------|-------|
| 1 | index.md | k2-surucu/index.md | move (silme yok) |
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

**Korunan kabuk:** k2-surucu/README.md ve k2-surucu/CLAUDE.md yerinde kalır (In-Place Refactoring #4); taşınan 12 dosyadan sonra README'deki Dosya Haritası güncellenir (satır edit, silme yok).

### §6.2 Link Taraması Zorunluluğu

Taşımadan **önce ve sonra** wiki-link taraması zorunludur (bkz. [[ADR-024-surucu-firmware-birlesme]]):

| Aşama | Tarama | Kabul |
|-------|--------|-------|
| Önce | kırık link raporu baseline (reports/broken-links-report.md) | Mevcut kırıklar kayıtlı |
| Sonra | k-surucu referanslı tüm vault dosyaları | Kırık link = k2-surucu/ ile düzeltilir |
| Sonra | wiki-link [[k-surucu/...]] kalıntısı | 0 kalmalı |

### §6.3 firmware/ Kuralı → K1.f

| Kural | Değer |
|-------|-------|
| Klasör | .ai/architecture/firmware/ **KALIR** (taşınmaz, silinmez) |
| Düğüm | K1.f — K1 Donanım katmanının alt katmanıdır |
| Dosya sayısı | 8 (bootloader, dsp-firmware, gpio-control, i2s-driver, mcu-support, usb-audio-firmware, xmos-firmware, index) |
| Segment türü | Harfli segment (f) — **kanıtlı tek istisna**; kanıt: firmware/ klasörünün diskte varlığı (kanıt türü (i)) + klasör adı |
| Genelleme yasağı | Başka harfli segment (K1.x vb.) ancak §3.3 kanıtıyla ve yeni ADR ile açılabilir |
| Sınır | firmware ↔ K1: dosya düzeyinde bağımlılık yok; K2 sürücüleri firmware'e dokunmaz (K2 → K1 yönü korunur) |

### §6.4 Yasaklar

| # | Yasak | Sonuç |
|---|-------|-------|
| 1 | k-surucu/ veya k2-surucu/ içinden dosya silmek | Revert + CRITICAL (In-Place #4) |
| 2 | Taşıma sonrası kırık link bırakmak | Denetim başarısız, taşıma geri alınır |
| 3 | firmware/ dosyalarını k2-surucu/ veya k1-donanim/ içine almak | İhlal — firmware K1.f'te kalır |
| 4 | K1.f yerine K2.f / K9.f gibi kod uydurmak | §2.2 regex + §3.3 kanıt ihlali |
| 5 | Taşıma commit'inde log.md'ye yazmamak | Audit trail boşluğu |

---

## §7 L → K Dönüşüm Tablosu

### §7.1 Dönüşüm Kuralı

~~~text
L{n}(.x(.y(.z)))   →   K{n}(.x(.y(.z)))
~~~

Yalnızca önek değişir (L → K), sayısal segmentler aynen korunur. Örnek: **L11.1.4.13 → K11.1.4.13** (kanıt: [[frontend-restructuring-plan]] §2.2, L11.1.4.13 = c-player.css (YENİ)).

### §7.2 Katman Dönüşümü (L0-L20 → K0-K20)

Kanıt sütunu: [[frontend-restructuring-plan]] §2.1 satırları (114-279).

| L | K | L katman adı (plan §2.1) | K katman adı |
|---|---|--------------------------|--------------|
| L0 | K0 | İşletim Sistemi Katmanı | İşletim Sistemi |
| L1 | K1 | Donanım Altyapısı Katmanı | Donanım |
| L2 | K2 | Sürücü Katmanı (K2) | Sürücü |
| L3 | K3 | Ses İşleme Motoru Katmanı (K3) | Ses İşleme Motoru |
| L4 | K4 | Yapay Zeka Katmanı (K4) | Yapay Zeka |
| L5 | K5 | Veri Yönetimi Katmanı (K5) | Veri Yönetimi |
| L6 | K6 | Güvenlik Katmanı (K6) | Güvenlik |
| L7 | K7 | Middleware Katmanı (K7) | Middleware |
| L8 | K8 | Servis Katmanı (K8) | Servis |
| L9 | K9 | API & Routing Katmanı (K9) | API & Routing |
| L10 | K10 | Uygulama Katmanı (K10) | Uygulama |
| L11 | K11 | Kullanıcı Deneyimi Katmanı (K11) | Kullanıcı Deneyimi |
| L12 | K12 | İzleme & Log Katmanı (K12) | İzleme & Log |
| L13 | K13 | CI/CD & Deploy Katmanı (K13) | CI/CD & Deploy |
| L14 | K14 | Ağ & İletişim Katmanı (K14) | Ağ & İletişim |
| L15 | K15 | Medya & Streaming Katmanı (K15) | Medya & Streaming |
| L16 | K16 | Class AB Amplifikatör | Class AB Amplifikatör |
| L17 | K17 | ±35V Güç Kaynağı (LM5122) | Güç Kaynağı ±35V |
| L18 | K18 | Termal Tasarım | Termal Tasarım |
| L19 | K19 | PCB Tasarımı (6-layer) | PCB Tasarım |
| L20 | K20 | BOM & Üretim | BOM & Üretim |

### §7.3 Uyarı — İkinci "L" İsim Uzayı (Otomatik Dönüşüm YASAK)

[[CLAUDE.md]] §32'deki **4 katmanlı basitleştirilmiş L0-L3** şeması bu tabloya dahil DEĞİLDİR. O uzayda otomatik L→K dönüşümü yasaktır.

| Basitleştirilmiş şema (CLAUDE §32) | Karşılığı (yalnızca referans) | Dönüşüm |
|------------------------------------|-------------------------------|---------|
| L3 Uygulama Katmanı | K10 + K11 | Manuel, ADR'li |
| L2 Servis Katmanı | K8 + K9 | Manuel, ADR'li |
| L1 Güvenlik Katmanı | K6 + K7 | Manuel, ADR'li |
| L0 Altyapı Katmanı | K0 + K5 (+ K13) | Manuel, ADR'li |

**Kural:** L→K dönüşümü yalnızca plan §2.1-§2.2 bağlamında geçerlidir; §5.1 Layer Dependency Matrix'teki L0-L6 okları (A0 grubu okuma biçimi) için dönüşüm yapılmaz, oklar yerinde kalır.

---

## §8 A0-A5 ↔ K Eşlemesi

| Grup | Katmanlar | Kapsam | Raporlama adı |
|------|-----------|--------|---------------|
| A0 | K0-K5 | Altyapı (OS, donanım, sürücü, ses motoru, AI, veri) | A0 altyapı |
| A1 | K6-K7 | Güvenlik ve middleware | A1 güvenlik |
| A2 | K8-K9 | Hizmet ve yönlendirme | A2 hizmet |
| A3 | K10-K11 | Sunum (uygulama + deneyim) | A3 sunum |
| A4 | K12-K15 | Operasyon (izleme, CI/CD, ağ, medya) | A4 operasyon |
| A5 | K16-K20 | Fiziksel üretim (amfi, güç, termal, PCB, BOM) | A5 üretim |

### §8.1 Eşleme Kuralları

1. A0-A5 **yalnızca raporlama/gruplama içindir**; bağımlılık izni A grubundan değil, K matrisinden okunur.
2. Her düğüm K{n}.a.b.c → en yakın A grubuna aittir (K n'e göre, a/b/c'ye göre değil).
3. **Katman ihlali denetimi K matrisine göre yapılır** ([[katman-baglilik-matrisi]] §2.1-§2.2); A grubu denetimde kullanılamaz.
4. Gruplar arası sınır ihlali (örn. A3'ten A0'a doğrudan atlamak) ancak K düzeyinde ihlalse ihlaldir.
5. A5 (K16-K20) bağımsızdır: A0-A4 ile tek ilişki K1'e ↕ arayüz bağımlılığıdır.

### §8.2 Örnek Denetim

| İddia | Denetim | Sonuç |
|-------|---------|-------|
| "A4'ten A0'a geçiş yasak" | İncelenen gerçek ok: K15 → K0? | Matriste yok → ihlal (K15'in tek hedefi K14) |
| "A2 → A4 müsaitsiz" | Gerçek ok: K8.2 → K15 (çağrı, üst→alt) | İzinli — ADR-025 |
| "A3 → A4 müsaitsiz" | Gerçek ok: K11 → K12 (gösterim) | İzinli — §9.3 |

---

## §9 İhlal Denetimi Kuralları

### §9.1 Üç Ok Türü

| Ok türü | Anlamı | Nereden okunur | Örnek |
|---------|--------|----------------|-------|
| **Bağımlılık** (kanonik) | "Kayan katman, hedefin ARAYÜZÜNE bağımlıdır" | [[katman-baglilik-matrisi]] §2.1-§2.2 | K15 → K14 |
| **Çağrı** (üst→alt) | Üst katman alt katmanı arayüzünden çağırır; matrise bağımlılık eklemez | ADR-025 / katman README'leri | K8.2 → K15 |
| **Gösterim** (üst→alt) | Dokümantasyon akış çizgisi | Katman README'leri | K11 → K12 |

### §9.2 Bağımlılık Oku Kuralları (Matris Kanonik)

| # | Kural | İhlal |
|---|-------|-------|
| 1 | Ok = kayan katman → hedefin arayüzüne bağımlılık | Yön tersyüzse ihlal |
| 2 | K15'in **tek** hedefi K14'tür | K15 → K16-20 izni **YOK**; K15 → başka katman = ihlal |
| 3 | K12'nin **tek** bağımlılığı K8'dir | K12 → K0/K13 vb. = ihlal |
| 4 | K16-K20 ↔ K1 çift yönlü dışında ikili çift yönlü yok | Diğer ↔ = ihlal |
| 5 | Tanımsız bağımlılık = hata (matris §7.1) | Yeni ok için önce matris satırı |
| 6 | Matris ile bu dosya çelişirse matris kazanır, bu dosya güncellenir | SSOT |

### §9.3 İzinli Çağrı ve Gösterim Okları (İstisnalar)

| Ok | Tür | Kural |
|----|-----|-------|
| K8.2 → K15 | Çağrı | Servis ucu, boru hattını çağırır (iş emri / akış URL'si / kitaplık CRUD) — ADR-025 |
| K11 → K12 | Gösterim | Üst→alt akış çizgisi; bağımlılık satırı eklemez |
| K12 → K8 | Bağımlılık | Matristeki tek ve kanonik hedefi |
| K15 → K14 | Bağımlılık | K15'in tek hedefi |

### §9.4 Denetim Checklist'i

| # | Kontrol | Beklenen | Kaynak |
|---|---------|----------|--------|
| 1 | Tüm düğüm adları regex'e uyar mı? | 0 ihlal | §2.2 |
| 2 | 5. seviye var mı? | 0 | §3.4 |
| 3 | Kanıtsız 4. seviye var mı? | 0 | §3.3 |
| 4 | 0 ara segment (K7.0.x dahil) var mı? | 0 | §4 |
| 5 | k-surucu/ dosya sayısı | 0 (12'si taşındı) | §6.1 |
| 6 | k2-surucu/ dosya sayısı | 14 (2 kabuk + 12 taşınan) | §6.1 |
| 7 | firmware/ dosya sayısı | 8 (K1.f) | §6.3 |
| 8 | L-önekli düğüm referansı | 0 (dönüştürüldü) | §7.1 |
| 9 | K15 → K16-20 oku | 0 | §9.2 |
| 10 | K12 → K8 dışı bağımlılık | 0 | §9.2 |
| 11 | Kırık wiki-link | 0 | §6.2 |
| 12 | Sayım scripti sonucu | ≥5000 | [[katman-sayim-rehberi]] §8 |

### §9.5 İhlal Sonuçları

| Şiddet | Durum | Aksiyon |
|--------|-------|---------|
| CRITICAL | 5. seviye, kanıtsız 4. seviye, K15→K16-20, dosya silme | Derhal revert + log.md CRITICAL |
| HIGH | K7.0.x ara numara, L-önek kalıntısı, kırık link | Düzelt + log |
| MEDIUM | README eşleme satırı eksik (K{n}-NN ↔ a.b) | Tamamla + log |
| LOW | Raporlama/gruplama (A0-A5) tutarsızlığı | Düzelt, revert gerekmez |

---

## §10 Doğrulama Komutları (salt okunur)

~~~powershell
# 1) 5. seviye ve sıfırlı ara segment taraması (0 sonuç beklenir)
Get-ChildItem .ai\architecture -Recurse -Filter *.md |
  Select-String -Pattern 'K\d+(\.\d+){4,}' |

  Select-String -Pattern 'K7\.0'

# 2) k-surucu / k2-surucu / firmware dosya sayıları (0 / 14 / 8 beklenir)
(Get-ChildItem .ai\architecture\k-surucu -Filter *.md -ErrorAction SilentlyContinue).Count
(Get-ChildItem .ai\architecture\k2-surucu -Filter *.md).Count
(Get-ChildItem .ai\architecture\firmware -Filter *.md).Count

# 3) L-önek kalıntısı (0 beklenir; CLAUDE §32 alıntıları hariç)
Get-ChildItem .ai\architecture -Recurse -Filter *.md |
  Select-String -Pattern 'L\d+\.\d+\.\d+'

# 4) Kırık link taraması
Get-ChildItem .ai -Recurse -Filter *.md | Select-String -Pattern '\[\[k-surucu/'
~~~

---

## §11 İlgili Dokümanlar ve Değişiklik Geçmişi

### §11.1 İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[CLAUDE.md]] | §5 katman tablosu, §5.1 Layer Dependency Matrix, Guardrail #4/#16 |
| [[AGENTS.md]] | Domain boundaries, §25.3 frozen/silme kuralları |
| [[katman-baglilik-matrisi]] | Kanonik bağımlılık okları (§9.2 kaynağı) |
| [[index]] | Katman kök adları ve bileşen sayıları |
| [[frontend-restructuring-plan]] | §2.1-§2.2 — L şeması + 4. seviye kanıt (iii) |
| [[katman-sayim-rehberi]] | Sayım birimi, 21 katman tablosu, script |
| [[ADR-023-hibrit-derinlik]] | Derinlik ve tavan kararı (Aday C) |
| [[ADR-024-surucu-firmware-birlesme]] | k-surucu→k2-surucu + K1.f ayrıntısı |
| [[ADR-025-k8-2-k15-siniri]] | K8.2 → K15 çağrı okunun gerekçesi |
| [[ADR-026-sayim-birimi-5000]] | Düğüm birimi ve kanıt şeffaflığı |

### §11.2 Değişiklik Geçmişi

| Tarih | Sürüm | Değişiklik | Kaynak |
|-------|-------|------------|--------|
| 2026-09-24 | 1.0.0 | İlk oluşturma — 3 turlu agent tartışması 20 persona bağlayıcı kararları | Bağlayıcı karar seti |

---

**REFACTOR REPORT:** FILE: adlandirma-kurali.md · PURPOSE: K{n}.a.b.c adlandırma dili, K7.0.x red, K0-01…K0-20 eşlemesi, L→K dönüşümü, A0-A5, ihlal denetimi · VALIDATION: yeni dosya, mevcut dosya değiştirilmedi · RELATED: [[katman-sayim-rehberi]] · [[ADR-023-hibrit-derinlik]] · [[ADR-024-surucu-firmware-birlesme]] · [[ADR-025-k8-2-k15-siniri]] · [[ADR-026-sayim-birimi-5000]]

*CoreMusic Katman Adlandırma Kuralı v1.0.0 — Durum: önerildi · Tarih: 2026-09-24 · Kaynak: 3 turlu agent tartışması 20 persona*
*Mode: Red Team · Human Mode · Truth Mode*
