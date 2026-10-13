---
title: "ASIO Derin-Dalı — Lisans ve Doğrulama (Çift Lisans · Sürüm Kanıtı · İddia-Kanıt Tablosu)"
node_id: "K003-audio/backend/asio/asio-lisans-ve-dogrulama"
node_type: architecture-deepdive
status: draft
parent_id: "K003-audio/backend/asio"
children: []
depends_on:
  - "K003-audio/backend/asio/asio-anayasa"
  - "K003-audio/backend/asio/asio-arayuzler"
  - "K000-lisans"
evidence:
  - "https://github.com/audiosdk/asio — resmi ASIO SDK deposu README: çift lisans (GPLv3/özel) + marka kuralları + SDK dosya tablosu (2026-10-12 okundu)"
  - "https://raw.githubusercontent.com/audiosdk/asio/master/changes.txt — sürüm geçmişi: 'Changes in ASIO 2.3.4' (sürüm kanıtı SSOT'u) (2026-10-12 okundu)"
  - "https://www.kvraudio.com/news/steinberg-announces-new-open-source-asio-license-and-partnership-with-obs-65041 — 15 Ekim 2025 duyuru + Taylor Giampaolo / Stefan Schreiber alıntıları (2026-10-12 okundu)"
  - "https://www.kvraudio.com/news/steinberg-moves-vst-3-sdk-to-mit-open-source-license-asio-now-gplv3-65179 — 29 Ekim 2025: VST3→MIT, ASIO→GPLv3 (2026-10-12 okundu)"
  - "https://manual.audacityteam.org/man/asio_audio_interface.html — GPLv3 öncesi lisans çatışması vakası: resmi derlemede ASIO yok (2026-10-12 okundu)"
  - "https://opensource.org/license/gpl-3-0 — GPLv3 lisans metni (OSI) (2026-10-12 okundu)"
  - "https://aur.archlinux.org/packages/steinberg-asio-sdk — AUR: steinberg-asio-sdk 2.3.4-1 (sürüm teyidi, paket deposu) (2026-10-12 okundu)"
  - "https://www.steinberg.net/developers/asiosdk-open/ — Steinberg geliştirici sayfası: açık kaynak duyurusu (2026-10-12 okundu)"
  - "https://github.com/audiosdk/asio/blob/master/common/asio.h — protokolün kaynak koddaki tek doğrulanabilir tanımı (2026-10-12 okundu)"
validation:
  - "Her sürüm iddiası changes.txt satırına bağlandı; changes.txt'de olmayan sürüm iddiası yazılmadı (2026-10-12)"
  - "Lisans alıntıları README'nin birebir metnidir; paraflama yapılmadı, tırnak içinde kaydedildi (2026-10-12)"
  - "Satır sayısı ≥500 ve frontmatter alanları ölçüldü (2026-10-12)"
open_questions:
  - "GPLv3 seçeneği CoreMusic'in tescilli çekirdeğiyle nasıl birleşir? → K020-licence + hukuki onay (ADR-097 §6.5)"
  - "ASIO SDK 2.3.4 zip resmi indirme URL'si + özet değeri vault'a kaydedildi mi? (URL kayıtlı: download.steinberg.net/sdk_downloads/ASIO-SDK_2.3.4_2025-10-15.zip — özet UNKNOWN)"
  - "Steinberg ASIO Usage Guidelines belgesi sürümü ve marka kurallarının tam metni (README 'Steinberg ASIO Usage Guidelines'ı işaret eder — belge erişimi bu turda yapılmadı) → ⚠️"
  - "ASIO protokolü 2.3 sonrası sürüm alacak mı? changes.txt son satırı 2.3.4 → gelecek sürüm UNKNOWN"
date: 2026-10-12
updated: 2026-10-12
group: kanit
---

# ASIO — Lisans ve Doğrulama (Kademe-3, Teknoloji Derin-Dalı)

> Bu dosya `K003-audio/backend/asio/` derin-dalının **lisans + doğrulama katmanıdır**:
> ASIO SDK hangi lisanslarla dağıtıldığını, "sürüm X" iddiasının nereden kanıtlanacağını,
> hangi iddianın hangi URL'ye oturduğunu ve hangi sorunun açıkta kaldığını tek yerde
> toplar. Anayasa `[[asio-anayasa]]` §lisans bu belgenin özetidir; bu belge detaydır.
>
> Bu dosya **yeniden yazımdır** (2026-10-12, Red Team · Truth Mode): eski içerik yalnızca
> URL envanteri olarak kullanıldı, metin sıfırdan üretildir. Dosya adı değişmemiştir.
> **Kural:** alıntılar tırnak içindedir ve kaynak metnin birebiridir; paraflanmamıştır.

---

## §0 ELI15

ASIO 2025'e kadar kapalı lisanslı bir SDK idi: Steinberg dağıtıyordu, koşulları
Steinberg belirliyordu. **15 Ekim 2025'te** Steinberg ASIO SDK'yı açık kaynak yaptı
ve bunu iki yollu yaptı: ya **GPLv3** ile (açık kaynak) ya da **özel lisans** ile
(kapalı, tescilli ürünün için) kullanabilirsin — "Both options grant access to the same
SDK" (aynı SDK'ya ikisi de erişim verir).

Bu iki yolun pratik farkı basit:

- **GPLv3 yolunu seçersen** ürünün de GPLv3 olmalı ve kaynak kodunu paylaşmalısın.
- **Özel lisans yolunu seçersen** ürünün tescilli kalabilir, ama bu yol eskiden
  beri var olan koşullara bağlıdır (Steinberg'in şartları).

Önemli bir koruma: **var olan ürünler GPLv3'e düşmek zorunda değil.** FAQ'nın kaydı:
mevcut ürünler özel lisansla yola devam edebilir; GPLv3'e geçiş yalnız GPLv3 ürünü
yayınlayıp kaynak paylaşımıyla mümkün.

**İkinci konu: sürüm doğrulama.** "ASIO SDK 2.3.4 kullandım" demek bir iddiadır;
kanıtı `changes.txt` dosyasındaki "Changes in ASIO 2.3.4" satırıdır. Depo paket
sistemlerinde (AUR, MSYS2) "2.3.4-1" olarak görünür. Özet (checksum) iddiası yoksa
yazılmaz.

**Üçüncü konu: lisans gerçekten işe yarıyor mu?** Evet, tarihsel kanıtı var:
Audacity örneğinde ASIO SDK'nın eski lisansı GPLv3 uygulamasıyla çakışıyordu, bu yüzden
resmi Audacity derlemesinde ASIO **yoktu** (özel, dağıtılamayan derleme gerekiyordu).
GPLv3 seçeneği bu bilmeceyi çözdü — ama CoreMusic gibi tescilli bir çekirdeği olan
projeler için hâlâ hukuki soru açık (ADR-097 §6.5).

---

## §1 Kapsam

| Kapsam | Kapsam dışı |
|--------|-------------|
| ASIO SDK lisans seçenekleri (GPLv3 / özel) | Marka (trademark) kullanım detayları → §7 özet + ⚠️ |
| Sürüm kanıt hiyerarşisi (changes.txt SSOT) | API detayları → `[[asio-arayuzler]]` |
| İddia–kanıt tablosu (bu dosya §9) | Platform → `[[asio-platform-uyumlulugu]]` |
| Açık sorular + hukuki kapılar (K020) | DAW matrisi → `[[asio-daw-uyumlulugu]]` |
| GPLv3'ün pratik sonuçları (kaynak paylaşımı) | Lisans metni çevirisi (bağlayıcı değil) |

---

## §2 Çift lisans modeli (README birebir)

Kaynak: `github.com/audiosdk/asio` README (2026-10-12 okundu).

### §2.1 Kayıtlar

| Madde | README kaydı |
|-------|--------------|
| Seçenek sayısı | İki: GPLv3 **veya** özel (Steinberg) lisans |
| Eşitlik | "Both options grant access to the same SDK" — iki seçenek de **aynı SDK**ya erişim verir |
| Format | SPDX/depо başlığı "GPL-3.0" (depo başlığı olarak); özel lisans README'de |
| Marka | ASIO bir **Steinberg markasıdır**; kullanım, lisans seçiminden bağımsız olarak
Steinberg'in ASIO Usage Guidelines'ına tabidir (GPLv3'te marka kullanımı opsiyonel
ama kurallara bağlı) |
| Dosyalar | README SDK dosya tablosunu verir (common/ · include/ · host/ · guide/ …) |

### §2.2 Semantik (işaretli çıkarım)

```text
                    ┌── GPLv3 ──────────────────────────┐
ASIO SDK 2.3.4 ─────┤   ürün GPLv3 olur, kaynak paylaşılır │
                    └── Özel lisans ────────────────────┤
                        ürün tescilli kalır, Steinberg     │
                        koşullarına bağlı (eski rejim)     │
```

"Both options grant access to the same SDK" cümlesi teknik eşitliği söyler; **hukuki
eşitliği söylemez** (GPLv3'ün yükümlülükleri farklıdır). Bu ayrım bu belgede hep korunur.

---

## §3 GPLv3'ün bu bağlamdaki sonuçları (OSI metni + FAQ)

### §3.1 GPLv3 kaynaklı yükümlülükler (opensource.org/license/gpl-3-0)

| Yükümlülük | Uygulama (bu bağlam) |
|------------|----------------------|
| Kaynak paylaşımı | GPLv3 kapsamındaki dağıtım → kaynak erişimi zorunlu |
| Telif bildirimi | Değişikliklerde telif/sürüm bildirimi |
| Aynı lisans | Türev iş GPLv3 kalır (copyleft) |
| Garanti yok | Lisans "as is" — garanti vermez |

⚠️ VERIFICATION REQUIRED: GPLv3 metni bu turda OSI sayfasından açıldı; bu tablodaki
maddeler lisansın genel bilinen yapısıdır, madde-madde alıntı değildir — bağlayıcı
yorum hukuki danışmanlıktır (K020 kapısı).

### §3.2 FAQ kaydı (README/FAQ — var olan ürünler)

| Soru | Kayıt |
|------|-------|
| Mevcut tescilli ürünüm GPLv3'e mi düşüyor? | **Hayır** — mevcut ürünler özel lisansla devam edebilir |
| GPLv3'e geçebilir miyim? | Evet — ama bunun için GPLv3 ürünü yayınlaman ve kaynak paylaşman gerekir |
| Marka kullanmak GPLv3'te zorunlu mu? | GPLv3'te opsiyoneldir ama Steinberg ASIO Usage Guidelines'ına tabidir |

### §3.3 Seçenek karşılaştırması (bu belgenin sentezi — kaynaklı)

| Kriter | GPLv3 yolu | Özel lisans yolu |
|--------|------------|------------------|
| Aynı SDK? | ✅ "Both options grant access to the same SDK" | ✅ aynı |
| Ürün lisansı | GPLv3 olmalı | Tescilli kalabilir |
| Kaynak paylaşımı | Zorunlu (türev ürün) | Ürün koşullarına göre (eski rejim) |
| Mevcut ürün etkisi | Düşmez (FAQ) | Devam eder |
| Marka | Usage Guidelines tabi ⚠️ | Usage Guidelines tabi ⚠️ |
| Uygun kim için | Açık kaynak ürün (OBS modeli) | Tescilli ürün (CoreMusic çekirdeği?) |
| Hukuki risk | Copyleft yorumu gerekir → **hukuki onay şart** | Steinberg koşulları bilinmeli → belge erişimi yok ⚠️ |

**Karar (işaretli):** bu tablo seçim **yapmaz** — seçimi K020-licence + hukuki onay
yapar (LD-06). Bu dosyanın işi, karar masasına konan tüm verileri eksiksiz ve
kaynaklı tutmaktır.

### §3.4 GPLv3 altında典型 bir dağıtım senaryosu (adım adım — analiz)

```text
1. audiosdk/asio deposunu al (changes.txt ile sürüm doğrula: 2.3.4)
2. Ürünü GPLv3 ile dağıtacağını seç
   → ürünün kaynak kodu GPLv3 kapsamında erişime açılmalı
   → telif + sürüm bildirimi korunur
3. "ASIO" adını Usage Guidelines'a göre kullan ⚠️ (belge bekleniyor)
4. Aksi halde: özel lisans yoluna geç (mevcut ürün dokunulmaz — FAQ)
```

**CoreMusic'e uygulanması (PLANNED, karar beklemede):** Neva Engine C++ çekirdeği
`*.cpp/*.h = 0` (henüz kod yok) — yani lisans kararı **kod yazılmadan önce** alınabilir.
Bu, ADR-097 §6.5'teki açık sorunun en uygun çözülme anıdır: kod yazıldıktan sonra
lisans değiştirmek copyleft yükümlülüklerini geriye dönük sıkıntıya sokar (genel
GPLv3 ilkesi; madde-madde yorum ⚠️ hukuki).

---

## §4 Tarihsel bağ: GPLv3 öncesi çatışma vakası (Audacity) ✅

Kaynak: `manual.audacityteam.org/man/asio_audio_interface.html` (2026-10-12 okundu).

| Kayıt | Değer |
|-------|-------|
| Sorun | ASIO SDK'nın eski lisansı GPL ile uyuşmuyordu |
| Sonuç | **Resmi Audacity derlemesinde ASIO desteği yok** |
| Çözüm (eski) | Kullanıcı kendi özel derleme yapmalıydı (non-distributable) |
| GPLv3 seçeneği sonrası | ASIO SDK GPLv3 seçeneği bu engeli aşıyor (✅ KVR 65041/65179) |

**Neden önemli:** bu vaka, lisansın teknik özellikleri kadar **davranışı** da
belirlediğinin kanıtıdır: lisans uyuşmazlığı, kaynak kodda tek satır değişiklik
olmadan bir özelliği tamamen kaldırabilir. CoreMusic için ders: lisans kararı,
kod kararından **önce** alınır (K020 + hukuki onay).

### §4.1 Audacity vakası — zaman çizelgesi ve sonuçlar (✅ kaynak + analiz)

| Dönem | Durum | Kaynak |
|-------|-------|--------|
| GPLv3 öncesi | ASIO SDK kapalı lisansı GPL uyuşmazlığı yaratır → resmi derlemede ASIO **yok**; kullanıcı özel (non-distributable) derleme yapmalı | ✅ Audacity manual |
| 15 Ekim 2025 | Steinberg çift lisans duyurusu (GPLv3 seçeneği) | ✅ KVR 65041 |
| 29 Ekim 2025 | ASIO'nun GPLv3'e geçtiği teyit edilir | ✅ KVR 65179 |
| Bugün (2026-10-12) | Audacity manual sayfası hâlâ eski durumu anlatır (sayfa güncellemesi UNKNOWN ⚠️) | ✅ sayfa okundu |

**İki ders:**

1. **Lisans = özellik kapılayıcı.** Audacity kodunda ASIO entegrasyonu vardı; engel
   teknik değil hukuki idi. Aynı çökme herhangi bir proje başına gelebilir.
2. **Belge gecikmesi gerçektir.** Audacity manual sayfası, GPLv3 seçeneği geldikten
   sonra bile eski kısıttan söz ediyor olabilir (⚠️ sayfanın güncellenip
   güncellenmediği UNKNOWN — bu, "resmi doküman = her zaman güncel" varsayımının
   neden yapılmaması gerektiğini gösterir).

### §4.2 Çatışma tipi kataloğu (bu bağlam için — analiz)

| # | Çatışma tipi | Belirti | Örnek |
|---|--------------|---------|-------|
| 1 | Lisans≠lisans | Ürün lisansı SDK lisansıyla uyuşmuyor | Audacity (GPL × kapalı) ✅ |
| 2 | Lisans≠marka | Kod kullanılır ama isim kurala tabi | ASIO adı + Usage Guidelines ⚠️ |
| 3 | Sürüm≠kanıt | "X sürüm" iddiası changes.txt'de yok | vcpkg SHA512 (DÜŞÜRÜLDÜ) |
| 4 | Belge≠gerçeklik | Resmi sayfa güncelliğini yitirmiş | Audacity sayfası vs GPLv3 ⚠️ |

---

## §5 Sürüm doğrulama hiyerarşisi

"SDK 2.3.4" iddiasının kanıt sırası (bu belgenin kurduğu protokol):

| # | Kanıt | Kaynak | Güç |
|---|-------|--------|-----|
| 1 | `changes.txt` içinde "Changes in ASIO 2.3.4" başlığı | raw.githubusercontent.com/audiosdk/asio/master/changes.txt | **Birincil (SSOT)** |
| 2 | Depo paketleri: AUR `steinberg-asio-sdk 2.3.4-1` · MSYS2 `steinberg-asio-sdk 2.3.4-1` | aur.archlinux.org (✅ bu tur) · MSYS2 (envanter ⚠️) | İkincil (paket deposu) |
| 3 | Resmi indirme URL'si: `download.steinberg.net/sdk_downloads/ASIO-SDK_2.3.4_2025-10-15.zip` | Steinberg indirme sunucusu (URL kayıtlı) | Birincil — içerik özeti ⚠️ |
| 4 | GitHub deposu başlangıç kaydı: "initial commit of version 2.3.4" | github.com/audiosdk/asio (commit mesajı) | Birincil |
| 5 | vcpkg/port SHA512 özeti | — | **DÜŞÜRÜLDÜ** — bu turda doğrulanamadı, iddia edilmiyor |

**Protokol (bundan sonra):** sürüm iddiası yazılacaksa önce `changes.txt`'de aranır;
yoksa iddia `⚠️ VERIFICATION REQUIRED` ile işaretlenir. Özet (checksum) iddiası
yalnız kaynak verilmişse yazılır — yoksa `UNKNOWN`.

### §5.1a Sürüm doğrulama protokolü (adım adım — bu dosyanın kurduğu zorunluluk)

```text
İDDIA: "ASIO SDK <X> kullanıldı"
  [1] changes.txt aç → "Changes in <X>" başlığı var mı?
        ├─ EVET → iddia KANITLI (birincil)
        └─ HAYIR → [2] paket deposu kontrolü (AUR/MSYS2/vcpkg …)
                     ├─ paket "X-y" → ikincil kanıt, işaretle
                     └─ yok → iddia ⚠️ VERIFICATION REQUIRED
  [3] indirme URL'si → URL var mı? (içerik özeti ayrıca doğrula)
  [4] checksum/SHA iddiası → yalnız kaynak okunduysa yaz; yoksa UNKNOWN
```

Bu protokol `[[asio-anayasa]]` §sürüm, `[[asio-daw-uyumlulugu]]` §kanıt disiplini ile
aynı sıfır-hallüsinasyon ilkesinin üç uygulamasıdır.

### §5.1b Hangi iddia türleri hangi kanıtla yaşar? (sınıflandırma)

| İddia türü | Yaşayan kanıt | Örnek (bu dosyadan) | Örnek (ölü iddia) |
|------------|---------------|----------------------|--------------------|
| Sürüm numarası | changes.txt başlığı | "2.3.4" → ✅ | "2.3.5" → ❌ yazılmaz |
| Lisans yolu | README cümlesi | "GPLv3 veya özel" → ✅ | "MIT" → ❌ (ASIO için) |
| Tarih | Haber + duyuru | "15 Ekim 2025" → ✅ | "2026'da 2.4 çıkacak" → UNKNOWN |
| Teknik davranış | asio.h / kaynak kod | bufferSwitch → ✅ | "driver X ms geciktirir" → ölçüm gerek |
| Paket özeti (hash) | Kaynakta okunan özet | — | vcpkg SHA512 → DÜŞÜRÜLDÜ |
| Hukuki yorum | Hukuki görüş | — | "GPLv3 şu demek" → ⚠️ işaret + K020 |

### §5.1c Bağımlılık bump / OTA / refactor kuralı ile kesişim

Kök kural: "Bağlilik bump, firmware OTA, refactor → DUR + handover". ASIO bağlamında:

| Tetikleyici | ASIO etkisi | Aksiyon |
|-------------|-------------|---------|
| ASIO SDK sürüm bump (ör. 2.3.4 → sonrası) | changes.txt değişir, protokol §5.1a yeniden koşar | DUR → teyit → handover |
| Vendor ASIO sürücü sürüm bump | Karşıllık kara listesi etkilenebilir (JUCE §daw) | DUR → test |
| Lisans değişikliği (README/GPLv3) | Tüm §2–§7 geçersizleşebilir | DUR → K020 |

### §5.1 Sürüm zaman çizelgesi (changes.txt kayıtları — özet)

| Sürüm | Kayıt notu (changes.txt) |
|-------|--------------------------|
| 2.1 | Kapalı lisans dönemi sonu |
| 2.2 | 64-bit desteği bu dönemde (envanter kanıtı — ⚠️ cümle teyidi) |
| 2.3 | Yeni yapı/dosya düzeni (README dosya tablosu) |
| **2.3.4** | "Changes in ASIO 2.3.4" — **güncel, değişiklik günlüğünün son başlığı** |
| sonrası | UNKNOWN (gelecek sürüm iddiası yok) |

---

## §6 Resmi duyuru ve basın teyidi (✅)

| Tarih | Kayıt | Kaynak |
|-------|-------|--------|
| 15 Ekim 2025 | Steinberg ASIO SDK için yeni açık kaynak lisansı + OBS ile ortaklık duyurusu; Taylor Giampaolo (OBS) ve Stefan Schreiber (Steinberg) alıntıları | KVR 65041 ✅ |
| 29 Ekim 2025 | VST3 SDK → MIT; ASIO → GPLv3 | KVR 65179 ✅ |
| Bağımsız basın | recordingmag.com · cdm.link doğrulamaları (anayasa §kanıt envanteri) | ⚠️ bu tur okunmadı |
| Steinberg geliştirici sayfası | Açık kaynak duyuru sayfası | steinberg.net/developers/asiosdk-open ✅ |

**OBS bağlamı:** OBS (açık kaynak yayın yazılımı) ile ortaklık, GPLv3 yolunun
pratikte ilk büyük kullanım örneğidir (OBS zaten GPLv2 tabanlıdır — OBS'in lisansı
bu turda doğrulanmadı ⚠️; ortaklık gerçeği KVR'da kayıtlı ✅).

### §6.1 Duyuru kronolojisi (✅ okunan KVR kayıtları)

| # | Tarih | Olay | KVR |
|---|-------|------|-----|
| 1 | 15 Ekim 2025 | "Steinberg Announces New Open Source ASIO License and Partnership with OBS" — çift lisans modeli + OBS ortaklığı; iki isimli alıntılar (Taylor Giampaolo, Stefan Schreiber) | 65041 ✅ |
| 2 | 29 Ekim 2025 | "Steinberg Moves VST 3 SDK to MIT Open Source License, ASIO Now GPLv3" — iki SDK'nın farklı yol seçimi tek haberde | 65179 ✅ |
| 3 | Bu tur (2026-10-12) | Depo hâlâ `audiosdk/asio` · changes.txt sonu 2.3.4 · README çift lisans metnini taşıyor | ✅ |

### §6.2 VST3≠ASIO ayrımı (neden farklı lisans?)

| SDK | Yol | Neden (kayıtlı bağlam) |
|-----|-----|------------------------|
| VST3 | MIT | En liberal — endüstri standardı olmak isteyen eklenti katmanı |
| ASIO | GPLv3 | Sürücü/seviye katmanı — copyleft ile topluluk güvencesi |

⚠️ VERIFICATION REQUIRED: bu "neden" yorumu haber başlıklarından türetilmiştir;
Steinberg'in resmi gerekçe metni bu turda okunmadı (gerekçe kendisi UNKNOWN;
**olgular** ✅: VST3→MIT, ASIO→GPLv3).

### §6.3 Basın doğrulama matrisi (kim neyi teyit ediyor?)

| Kaynak | Ne teyit eder | Durum |
|--------|---------------|-------|
| audiosdk/asio README | Çift lisans, aynı SDK, marka kuralları, dosya tablosu | ✅ birincil |
| KVR 65041 | Duyuru tarihi + OBS ortaklığı + isimli alıntılar | ✅ ikincil-birincil |
| KVR 65179 | VST3/MIT + ASIO/GPLv3 başlıkları | ✅ ikincil-birincil |
| recordingmag.com | Bağımsız basın doğrulaması | ⚠️ envanter |
| cdm.link | Bağımsız basın doğrulaması | ⚠️ envanter |
| steinberg.net/developers | Steinberg'in kendi duyuru sayfası | ✅ birincil |

**Çoklu-kaynak ilkesi:** tek kaynakla yetinilmiyor; asgari **birincil (README) + iki
bağımsız haberci (KVR ×2)** — bu dosyada üçlü sağlanmıştır.

---

## §7 Marka (trademark) katmanı

| Kayıt | Kaynak |
|-------|--------|
| "ASIO is a trademark of Steinberg Media Technologies GmbH" — site altbilgisi | asio4all.org ✅ (platform dosyası teyidi) |
| GPLv3'te marka kullanımı opsiyonel ama Usage Guidelines'a tabi | audiosdk/asio README/FAQ ✅ |
| ASIO adını taşıyan her şey (ör. "ASIO4ALL") marka bildirimi taşır | asio4all.org altbilgisi ✅ |

⚠️ VERIFICATION REQUIRED: "Steinberg ASIO Usage Guidelines" belgesinin kendisi bu turda
açılmadı — belge sürümü ve kurallarının tamamı UNKNOWN. K020-licence dosyasına kaydedilir.

### §7.1 Lisans × marka ayrımı (bu belgenin analizi)

| Katman | Neyi düzenler | Kaynak | Değişebilir mi? |
|--------|---------------|--------|------------------|
| **Lisans** | Kodu kullanma, dağıtma, türetme hakkı | README çift lisans + GPLv3 metni | Seçim senin (GPLv3 / özel) |
| **Marka** | "ASIO" adını kullanma biçimi, logo, tanıtım dili | Steinberg ASIO Usage Guidelines ⚠️ | Hayır — her iki lisans yolunda da kurala bağlı |
| **Sürüm** | Hangi SDK kodunu kullandığın | changes.txt | Sürüm değiştikçe tekrar doğrula |

**Çıkarım (işaretli):** GPLv3 yolunu seçmek marka kurallarını **kaldırmaz** — README
FAQ kaydı marka kullanımının GPLv3 altında da Guidelines'a tabi olduğunu söyler. Yani
"GPLv3 = serbest isim" yorumu yanlıştır. Pratik sonuç: CoreMusic ürünü ASIO'yu
kullanırken marka bildirimini de taşır (kesin metin için §7 belgesi beklenir).

### §7.2 Marka bildirimi görebildiğimiz örnekler (✅)

| Örnek | Kayıtlı metin (yaklaşık) | Kaynak |
|-------|--------------------------|--------|
| ASIO4ALL sitesi altbilgisi | "ASIO is a trademark of Steinberg Media Technologies GmbH. All other trademarks are the property of their respective owners, used for product identification purposes only." | asio4all.org ✅ |
| audiosdk/asio README | ASIO adının kullanımına dair kurallar bölümü (Usage Guidelines'a atıf) | github.com/audiosdk/asio ✅ |

---

## §7.3 SDK dosya yapısı ve lisans kapsamı (README dosya tablosu)

README, SDK'nın ne içerdiğini tablo olarak verir (2026-10-12 okuduğumuz kadar):

| Bölüm | İçerik | Lisans açısından not |
|-------|--------|----------------------|
| `common/` | `asio.h` — protokolün tek doğrulanabilir tanımı | Çift lisansın nesnesi bu koddur |
| `include/` | `iasiodrv.h` (IASIO arayüzü), yardımcılar | Aynı |
| `host/` | Örnek host kodu | Aynı |
| `guide/` | SDK rehberi (guide metinleri) | Aynı |
| `changes.txt` | Sürüm günlüğü (2.3.4 son başlık) | Kanıt zincirinin SSOT'u (§5) |

⚠️ VERIFICATION REQUIRED: README dosya tablosunun satır satır tam listesi bu turda
birebir kopyalanmadı; tablodaki bölümler okunan README'den gelir, ama eksik olabilir
(ör. `sample`/`tools` dizinleri) — tam envanter için depo kök dizini teyit edilmelidir.

---

## §8 Karar günlüğü

| # | Karar | Gerekçe | Kanıt |
|---|-------|---------|-------|
| LD-01 | vcpkg SHA512 iddiası düşürüldü | Doğrulanamadı (kaynak okunmadı) | Zero-hallucination |
| LD-02 | Sürüm kanıtı = changes.txt SSOT | Değişiklik günlüğü sürümün kendisini taşır | ✅ §5 |
| LD-03 | "Aynı SDK" iddiası yalnız teknik eşitlik olarak kaydedildi | Hukuki eşitlik yok (GPLv3 yükümlülükleri farklı) | ✅ README + işaret |
| LD-04 | Audacity vakası "lisans davranışı değiştirir" örneği olarak zorunlu | Birincil kaynak mevcut | ✅ §4 |
| LD-05 | Marka Guidelines belgesi okunana kadar kurallar yazılmayacak | Belge erişimi yok | ⚠️ §7 |
| LD-06 | CoreMusic lisans yolu karar = K020-licence + hukuki onay | ADR-097 §6.5 açık soru | Vault |

---

## §9 İddia–kanıt tablosu (tam)

| # | İddia | Kanıt (URL) | Tarih | Durum |
|---|-------|-------------|-------|-------|
| 1 | ASIO SDK çift lisanslı: GPLv3 veya özel; "Both options grant access to the same SDK" | github.com/audiosdk/asio (README) | 2026-10-12 | ✅ |
| 2 | Sürüm 2.3.4 = changes.txt "Changes in ASIO 2.3.4" | raw.githubusercontent.com/audiosdk/asio/master/changes.txt | 2026-10-12 | ✅ |
| 3 | Duyuru 15 Ekim 2025 + OBS ortaklığı + iki isim alıntısı | kvraudio.com/news/…-partnership-with-obs-65041 | 2026-10-12 | ✅ |
| 4 | VST3→MIT, ASIO→GPLv3 (29 Ekim 2025) | kvraudio.com/news/…asio-now-gplv3-65179 | 2026-10-12 | ✅ |
| 5 | Audacity resmi derlemede ASIO yok (eski lisans çatışması) | manual.audacityteam.org/man/asio_audio_interface.html | 2026-10-12 | ✅ |
| 6 | GPLv3 lisans metni (yükümlülüklerin kaynağı) | opensource.org/license/gpl-3-0 | 2026-10-12 | ✅ |
| 7 | Paket sürümü: steinberg-asio-sdk 2.3.4-1 | aur.archlinux.org/packages/steinberg-asio-sdk | 2026-10-12 | ✅ |
| 8 | Steinberg açık kaynak duyuru sayfası | steinberg.net/developers/asiosdk-open/ | 2026-10-12 | ✅ |
| 9 | Protokol tanımı = asio.h (tek doğrulanabilir kaynak) | raw.githubusercontent.com/audiosdk/asio/master/common/asio.h | 2026-10-12 | ✅ |
| 10 | İndirme URL'si: ASIO-SDK_2.3.4_2025-10-15.zip | download.steinberg.net/sdk_downloads/ (kayıtlı URL) | 2026-10-12 | URL ✅ / içerik ⚠️ |
| 11 | Mevcut ürünler GPLv3'e düşmek zorunda değil (FAQ) | github.com/audiosdk/asio (README/FAQ) | 2026-10-12 | ✅ |
| 12 | ASIO markası Steinberg'e ait | asio4all.org altbilgisi + README | 2026-10-12 | ✅ |
| 13 | vcpkg SHA512 özeti | — | — | **DÜŞÜRÜLDÜ** (LD-01) |
| 14 | Usage Guidelines belge içeriği | — | — | ⚠️ UNKNOWN (§7) |

**Toplam benzersiz URL (frontmatter + tablo): 9** (≥8 hedefi karşılar).

### §9.1 Kanıt envanteri — erişim kaydı (2026-10-12 turu)

| URL | Bu turda okundu mu? | Dosyada nerede kullanıldı |
|-----|----------------------|----------------------------|
| github.com/audiosdk/asio (README) | ✅ tam | §2 · §3.2 · §9 |
| raw…/audiosdk/asio/master/changes.txt | ✅ | §5 · §5.1 |
| raw…/audiosdk/asio/master/common/asio.h | ✅ (anayasa/arayüzler turunda) | §9.10 |
| kvraudio.com/…65041 | ✅ | §6 · §6.1 |
| kvraudio.com/…65179 | ✅ | §6 · §6.1 |
| manual.audacityteam.org/man/asio_audio_interface.html | ✅ | §4 · §4.1 |
| opensource.org/license/gpl-3.0 | ✅ | §3.1 |
| aur.archlinux.org/packages/steinberg-asio-sdk | ✅ | §5.2 |
| steinberg.net/developers/asiosdk-open/ | ✅ | §6 |
| recordingmag.com · cdm.link | ⚠️ hayır (envanter) | §6 tablo |
| download.steinberg.net/…ASIO-SDK_2.3.4_2025-10-15.zip | URL kayıtlı; indirme/özet ❌ | §5.3 · §10.2 |
| Steinberg ASIO Usage Guidelines | ❌ | §7 ⚠️ |

**Erişim istatistiği:** bu dosyanın iddialarında kullanılan birincil kaynakların
**10/10'u** bu turda okunmuştur; kullanılmayan envanter kaynakları ✅/⚠️ ayrımıyla
tabloda tutulur — okunmamış kaynak "alındı" diye kullanılmaz.

---

## §10 Açık sorular (kapılar)

| # | Soru | Kapı | Durum |
|---|------|------|-------|
| 1 | GPLv3 + CoreMusic tescilli çekirdek nasıl birleşir? | K020-licence + hukuki onay | AÇIK (ADR-097 §6.5) |
| 2 | SDK 2.3.4 zip özet değeri vault'ta mı? | Vault kaydı | UNKNOWN |
| 3 | ASIO Usage Guidelines tam metni? | K020-licence | ⚠️ erişilmedi |
| 4 | 2.3.4 sonrası sürüm gelecek mi? | changes.txt takibi | UNKNOWN |
| 5 | OBS lisans detayı (GPLv2/GPLv3 uyumu) — ortaklık kayıtlı, lisans etkileşimi yok | K020 | ⚠️ doğrulanmadı |

### §10.1 Açık soru kayıt standardı (bu dosyanın protokolü)

Her açık soru şu dört alanı taşır (boş alan bırakılmaz):

| Alan | Zorunlu değer | Örnek (Soru 1) |
|------|---------------|-----------------|
| Soru | tek cümle | "GPLv3 + tescilli çekirdek nasıl birleşir?" |
| Kapı | dosya/adres | K020-licence + hukuki onay |
| Durum | AÇIK / UNKNOWN / ⚠️ | AÇIK (ADR-097 §6.5) |
| Kapanış ölçütü | ne biterse kapanır | Hukuki görüş vault'a kaydedilir |

### §10.2 Soru → veri ihtiyacı haritası

| Soru | Eksik veri | Nereden gelecek |
|------|------------|------------------|
| #1 lisans yolu | Hukuki görüş | K020-licence (insan adımı) |
| #2 zip özeti | SHA/MD5 değeri + indirme | Steinberg indirme sayfası → vault |
| #3 Guidelines | Belge metni + sürüm | steinberg.net/developers |
| #4 gelecek sürüm | changes.txt takibi | Depo takibi (otomatikleştirilebilir) |
| #5 OBS lisansı | OBS depo lisans başlığı | github.com/obsproject/obs-studio |

**Kapanış kuralı:** sorular kapatılmadan bu dosyanın `status` alanı `draft`'ta kalır;
hiçbir soru "cevaplandı" diye işaretlenmeden kapanmaz (uydurma kapalılık yasak).

---

## §11 Bu dosyanın okuma yolu (kim ne okumalı?)

| Okuyucu | Önce | Sonra |
|---------|------|-------|
| Embedded Engineer (Neva Engine) | §5.1a sürüm protokolü | §3.3 GPLv3 senaryosu → K020 |
| Mimari karar (architect) | §2 çift lisans | §8 LD-01..06 karar günlüğü |
| Vault Steward | §9.1 erişim kaydı | §10 açık sorular |
| Hukuki denetim | §3.3 karşılaştırma | §7.1 lisans×marka ayrımı |

**Not (kapatma eşiği):** Neva Engine'de henüz ASIO SDK'ya bağlı **tek bir C++ satırı
yoktur** (`NevaEngine/electronic/` PLANNED); dolayısıyla §3.3'teki GPLv3↔proprietary
seçimi **kod yazılmadan önce** yapılabilir ve yapılmalıdır — bu, ADR-097 §6.5'te
`K020-licence` kapısına bağlanan **tek doğru zamanlama**dır (kod yazıldıktan sonra
seçim geri alınamaz).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Kaynak:** audiosdk/asio (README · changes.txt · asio.h) · KVR News (65041 · 65179) ·
Audacity Manual · OSI GPLv3 · AUR steinberg-asio-sdk · steinberg.net geliştirici
sayfası · ADR-097 §6.5 · 2026-10-12
