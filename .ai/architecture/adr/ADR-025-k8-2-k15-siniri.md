---
title: "ADR-025 — K8.2 / K15 Sınırı (Servis Ucu vs Boru Hattı Sahibi)"
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

# ADR-025 — K8.2 / K15 Sınırı (Servis Ucu vs Boru Hattı Sahibi)

**Durum:** önerildi · **Tarih:** 2026-09-24
**Karar Veren:** 3 turlu agent tartışması 20 persona (uzlaşma)
**İlgili ADR'ler:** [[ADR-023-hibrit-derinlik]] · [[ADR-024-surucu-firmware-birlesme]] · [[ADR-026-sayim-birimi-5000]]
**Konum:** .ai/architecture/adr/ADR-025-k8-2-k15-siniri.md (ayrı seri — numara notu: .ai/.decisions kayıtlarındaki ADR-025-professional-eq-system ile aynı numara, farklı slug)

---

## §1 Başlık

**K8.2 / K15 Sınırı:** FFmpeg medya boru hattı ile medya servis ucunun sahipliği ayrılır. **K15 Medya & Streaming**, FFmpeg, transcode, HLS/DASH boru hattının ve protokolünün SAHİBİDİR; **K8.2 Media Servis**, iş emri (job), akış URL'si ve kitaplık CRUD uçlarını sunar. K8.2 yalnızca K15'in arayüzünü çağırır — FFmpeg komut satırını, parametrelerini veya boru hattı akışını asla kendisi kurmaz.

**Bağlayıcı sınır cümlesi (iki README'de BİREBİR aynı, gelecek adımda yazılacak):**

> **K8.2 Media Servis iş emri/akış uçlarını sunar; FFmpeg boru hattının sahibi K15'tir. K8.2 yalnız K15 arayüzünü çağırır.**

---

## §2 Durum

| Alan | Değer |
|------|-------|
| Durum | **önerildi** (proposed) |
| Tarih | 2026-09-24 |
| Kaynak | 3 turlu agent tartışması 20 persona |
| Bağlayıcılık | Evet — K8.2 ve K15 sorumluluk sınırı bu karara bağlıdır |
| Sınır cümlesi | §1'deki cümle k8-servis/README.md ve k15-medya-streaming/README.md içinde birebir aynı yer alacaktır (gelecek adım — henüz mevcut dosyalara yazılmadı) |
| Onay akışı | Vault Steward ✅ → Tech Lead ⏳ → Arch Lead ⏳ |

---

## §3 Bağlam (Context)

### §3.1 Mevcut Durum

FFmpeg ilgisi **iki ayrı yerde** tekrar ediyor (2026-09-24 glob kanıtı):

| Dosya | Katman | İçerik | Sorun |
|-------|--------|--------|-------|
| .ai/architecture/k8-servis/media-service.md | K8 (K8.2 Media Servis) | Medya servisi — transcode akışı anlatılıyor | Boru hattı detayı servis katmanına sızmış |
| .ai/architecture/k15-medya-streaming/ffmpeg-pipeline.md | K15 Medya & Streaming | FFmpeg boru hattı — transcode akışı anlatılıyor | Asıl sahip burası |

k8-servis/README.md (14 dosya) ile k15-medya-streaming/README.md (16 dosya)各自 indekslerinde medya sorumluluklarını listeler; iki indeks arasındaki sınır yazılı değildi.

### §3.2 Sorun Tanımı

**Soru:** Transcode/HLS/DASH işi kime ait — K8.2 mi, K15 mi?

Belirsizlik üç sorun üretir:

1. **Sorumluluk belirsizliği:** Aynı FFmpeg komutu iki katmanda da tanımlanırsa iki SSOT doğar (guardrail ihlali).
2. **Bağımlılık yönü belirsizliği:** K8.2 → K15 mi, K15 → K8.2 mi, yoksa ikisi de FFmpeg'e mi bağlı? Yanlış yön Dependency Rule'u çiğner.
3. **Değişiklik patlaması:** FFmpeg sürüm/parametre değişikliği iki katmanı birden etkiler.

### §3.3 Kısıtlar

| # | Kısıt | Açıklama | Kaynak |
|---|-------|----------|--------|
| 1 | Dependency Rule | Alt katman üst katmana bakmaz; K15, K8.2'yi tanımaz | [[katman-baglilik-matrisi]] |
| 2 | Tek SSOT | Bir konu tek dosyada anlatılır | [[CLAUDE.md]] guardrail |
| 3 | K matrisi izni | K8.2 → K15 çağrısı matristeki izinli oklardan biri olmalı | [[katman-baglilik-matrisi]] §2.1-§2.2 |
| 4 | K8.2 düğüm tanımı | K8.2 = servis ucu (iş emri, akış URL'si, kitaplık CRUD) — bağlayıcı | 3 tur uzlaşma |
| 5 | K15 sahipliği | K15 = boru hattı/protokol sahibi (FFmpeg, transcode, HLS/DASH) — bağlayıcı | 3 tur uzlaşma |
| 6 | Mevcut dosya değişikliği yasak | Bu görev mevcut README'leri değiştirmez; cümle gelecek adımda yazılır | Görev direktifi |
| 7 | Birebir aynı cümle | İki README'deki sınır cümlesi karakter karakter aynı olmalı | 3 tur uzlaşma |

### §3.4 Web Araştırması Raporu

| Alan | Değer |
|------|-------|
| Web Search Query | service layer vs media pipeline ownership FFmpeg microservice boundary design |
| Web Search Konusu | Medya işleme boru hattının servis katmanından ayrılması |
| Web Search Bağlamı | Katmanlı mimaride FFmpeg gibi ağır işleyicinin sahipliği |
| Web Search Kısa Açıklama | Servis katmanı iş emri ve URL üretir; transcode/mux/segment işi uzmanlaşmış pipeline katmanında kalır |
| Web Search Uzun Açıklama | Pipeline sahipliği (codec seçimi, segmentleme, protokol) teknik kararları barındırır; servis katmanı bunları bilerse katmanlar arası kopukluk (coupling) artar ve her servis kendi FFmpeg sürümünü/parametresini yazar |
| Web Search Paragraf | CoreMusic: K8.2 iş emri + akış URL'si + kitaplık CRUD verir; K15 FFmpeg'i, transcode'u, HLS/DASH segmentlemesini yürütür. K8.2 → K15 arayüzü tek yön |
| Web Search Sonucu | Sınır: servis ucu ≠ boru hattı sahibi |
| Web Search Alınan Karar | K8.2 yalnız K15 arayüzünü çağırır |
| Web Search Sonuç | Tek FFmpeg SSOT'u: k15-medya-streaming/ffmpeg-pipeline.md |

*⚠️ VERIFICATION REQUIRED: Web araştırması canlı oturum kaydı .ai/log.md append ile güncellenmelidir.*

---

## §4 Karar (Decision)

| # | Karar Maddesi | Ayırıcı Söz |
|---|---------------|-------------|
| 1 | **K8.2 = servis ucu:** iş emri (transcode job), akış URL'si, kitaplık CRUD uçlarını sunar | "Uçlar K8.2'de" |
| 2 | **K15 = boru hattı/protokol sahibi:** FFmpeg, transcode, HLS/DASH — teknik kararlar ve yürütme K15'te | "Boru hattı K15'te" |
| 3 | **Yön: K8.2 → K15.** K8.2 yalnız K15'in **arayüzünü** çağırır; FFmpeg komut satırı, codec seçimi, segmentleme parametresi K8.2'de bulunamaz | "Tek yön, tek kapı" |
| 4 | **Tek FFmpeg SSOT'u:** FFmpeg'in anlatıldığı tek dosya k15-medya-streaming/ffmpeg-pipeline.md'dir; k8-servis/media-service.md FFmpeg ayrıntısına girmez | "Tek SSOT" |
| 5 | **Birebir aynı sınır cümlesi:** §1'deki cümle k8-servis/README.md ve k15-medya-streaming/README.md içinde aynen yer alır (gelecek adım) | "Aynı cümle, iki README" |
| 6 | K15, K8.2'yi asla tanımaz (Dependency Rule); K15'in K8.2'ye bağımlılığı YOKTUR | "Geri bakış yok" |
| 7 | Uçların altında kalan "işlem" FFmpeg çağrısı ise, o çağıran K15 arayüzünün implementasyonudur — K8.2'de değil | "İşlem değil, çağrı" |

### §4.1 Neden Bu Seçenek? (Rationale)

| Gerekçe | Açıklama |
|---------|----------|
| Katman semantiği | K8 servis = kullanıcıya/yüzeylere uç sunar; K15 medya = medya bilgisini işler. Transcode medya bilgisidir |
| SSOT tekliği | İki dosyada FFmpeg anlatımı bugüne dek çakışma riski taşıyordu (§3.1) |
| Bağımlılık yönü | K8.2 → K15 izinli ok; tersi (K15 → K8.2) servis katmanına bağımlılık yaratır ve Dependency Rule'u çiğner |
| Değişiklik izolasyonu | FFmpeg sürüm atlaması yalnız K15'i (ve onun dokümanını) etkiler; K8.2 aynı arayüzü kullanmaya devam eder |
| Test edilebilirlik | K15 boru hattı tek başına (URL'siz) test edilebilir; K8.2 ucu sahte (mock) K15 ile test edilebilir |

### §4.2 Teknik Detaylar

**Sorumluluk matrisi:**

| Konu | K8.2 (Servis Ucu) | K15 (Boru Hattı Sahibi) |
|------|-------------------|--------------------------|
| İş emri (job) oluşturmak / durumu / kuyruk | ✅ Sahip | ❌ |
| Akış URL'si üretmek (HLS master playlist URL'si dahil) | ✅ Sahip | ⚠️ Playlist içeriğini üretir, URL'yi servis verir |
| Kitaplık CRUD (parça, albüm, meta kaydı uçları) | ✅ Sahip | ❌ |
| FFmpeg komut satırı kurulumu | ❌ YASAK | ✅ Sahip |
| Transcode parametreleri (codec, bitrate, segment süresi) | ❌ YASAK | ✅ Sahip |
| HLS / DASH segmentleme ve protokol ayrıntısı | ❌ YASAK | ✅ Sahip |
| transcode-job başlatma API'si | ⚠️ Arayüz çağrı (K15) | ✅ Implementasyon |
| medya-metadata okuma | ⚠️ Arayüz çağrı | ✅ Sahip |

**Çağrı akışı:**

~~~text
İstemci → K8.2 Media Servis (iş emri ucu, akış URL'si, kitaplık CRUD)
              │
              │  arayüz çağrısı (tek yön: K8.2 → K15)
              ▼
         K15 Medya & Streaming (FFmpeg · transcode · HLS/DASH)
~~~

**Sınır cümlesi yerleşim planı (gelecek adım — henüz yazılmadı):**

| Dosya | Yerleşim | Durum |
|-------|----------|-------|
| k8-servis/README.md | K8.2 Media Servis satırının hemen altında, tırnaklı olarak | ⏳ gelecek adım |
| k15-medya-streaming/README.md | Kapsam/Sorumluluk bölümünde, tırnaklı olarak birebir aynı | ⏳ gelecek adım |
| k8-servis/media-service.md | FFmpeg ayrıntısı çıkarılır / ssot notu eklenir (satır edit) | ⏳ gelecek adım |
| k15-medya-streaming/ffmpeg-pipeline.md | Tek FFmpeg SSOT'u olduğu beyan edilir | ⏳ gelecek adım |

---

## §5 Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| A | **K8.2 sahip olsun:** FFmpeg boru hattı servis katmanında (media-service.md) tanımlansın, K15 yalnız akış sunumu yapsın | Servis her şeyi kontrol eder; tek dosyada görünür | Transcode teknik kararları servis katmanına sızar; K15 medya bilgisiz kalır; codec/segment kararları kullanıcıya en yakın katmanda | **RED:** Katman semantiği ters (medya bilgisi K15'te kalmalı); K15'ın sorumluluk alanı boşalır; FFmpeg sürüm değişimi servisi kırar |
| B | **İkisi de bağımsız:** Her iki katman kendi FFmpeg tanımsını yazsın (paylaşımlı değil) | Sıfır görüşme yükü | İki SSOT — guardrail ihlali; parametre çakışması; iki katman da FFmpeg sürümüne bağlı olur | **RED:** Tek SSOT kuralı ve §3.1'deki tespit edilmiş tekrar doğrudan bunu yasaklar |
| C | **K15 → K8.2 (ters yön):** Boru hattı servis ucunu çağırsın (durum/URL geri bildirimi için) | Ters bildirim akışı doğal görünür | Dependency Rule ihlali: alt katman (K15) üst katmanı (K8.2) tanır; matriste izin yok | **RED:** Bağımlılık yönü yasak; geri bildirim arayüz (event/callback) ile K8.2 tarafında çözülür |
| D | **Ayrı bir K21 Pipeline katmanı:** FFmpeg'i yeni ana katmana taşı | İsim çok net | 21 katman sabitliğini bozar (ADR-024 §5 D ile aynı red) | **RED:** Bağlayıcı kısıt — 21 sabit |
| E | **Sınır kararı (SEÇİLEN):** K8.2 servis ucu, K15 boru hattı sahibi, K8.2 → K15 tek yön | Katman semantiği doğru; tek SSOT; izinli ok; değişiklik izolasyonu | Sınır cümlesinin iki README'ye birebir yazılması gerekir (gelecek adım) | — **SEÇİLDİ** — tüm kısıtları karşılar |

### §5.1 Seçenek Karşılaştırma Matrisi

| Kriter | A K8.2 sahip | B İki SSOT | C Ters yön | D K21 | **E Sınır** |
|--------|-------------|-----------|-----------|-------|-------------|
| Katman semantiği | ❌ | ❌ | ❌ | ✅ | ✅ |
| Tek SSOT | ✅ | ❌ | ✅ | ✅ | ✅ |
| Dependency Rule | ✅ | ✅ | ❌ | ✅ | ✅ |
| 21 katman sabit | ✅ | ✅ | ✅ | ❌ | ✅ |
| 20 persona uzlaşması | red | red | red | red | **kabul** |

---

## §6 Sonuçlar (Consequences)

### §6.1 Olumlu Sonuçlar

- FFmpeg'in tek SSOT'u: k15-medya-streaming/ffmpeg-pipeline.md — k8-servis/media-service.md'deki tekrar (§3.1) kapatılır.
- K8.2 → K15 izinli ok; katman denetimi matriste tek satırla izlenir.
- K8.2 sahip olduğu uçlar netleşir: iş emri, akış URL'si, kitaplık CRUD — sayımı [[katman-sayim-rehberi]] K8 satırında K8.2 düğümü olarak geçer.
- Birebir aynı cümle iki README'de görünürce tutarlılığı sağlar (birebir denetlenebilir).
- FFmpeg sürüm atlamaları yalnız K15'i etkiler.

### §6.2 Olumsuz Sonuçlar

- Uç ile iş arasında uzun çağrı zinciri (K8.2 → K15 arayüzü) — hata ayıklama iki katmanı kapsar; log korelasyonu gerekir.
- K15, akış URL'si üretmezken playlist içeriğini ürettiği için URL/playlist sınırı yorum payı taşır — §4.2 matrisi yorumu kapatır.
- Sınır cümlesi henüz yazılmadı (görev direktifi: mevcut dosya değişikliği yasak); iki README arasında geçici dönemde tutarsızlık sürebilir → gelecek adım (§7.1).
- Numara uzayı notu: .ai/.decisions kayıtlarındaki ADR-025-professional-eq-system ile numara paylaşımı; iki seri birleştirilmedi. ⚠️ VERIFICATION REQUIRED: birleştirme istenirse üst karar gerekir.

### §6.3 Riskler

| # | Risk | Olasılık | Etki | Mitigasyon |
|---|------|---------|------|------------|
| 1 | K8.2 içine sızmış FFmpeg ayrıntısı temizlenmez | Orta | Yüksek | §7.1 adım 2 — media-service.md satır edit; grep ile FFmpeg taraması |
| 2 | İki README'deki cümle farklı yazılır | Orta | Orta | §1'deki cümle kopyala-yapıştır; birebir diff denetimi |
| 3 | Yeni bir servis K15'ı atlayıp FFmpeg çağırmak ister | Orta | Yüksek | §4 madde 3+7; katman denetimi K matrisine göre (AGENTS §5) |
| 4 | K15 → K8.2 geri çağırmak (durum bildirimi) | Orta | Yüksek | Dependency Rule; durum K8.2'nin kuyruğundan okunur (poll/event) |
| 5 | Sınır cümlesi unutulup ayrı içerik yazılır | Düşük | Orta | §5.1 birebir denetimi; kabul = iki cümle identical |

---

## §7 Uygulama (Implementation)

### §7.1 Adımlar

| # | Adım | Sorumlu | Durum |
|---|------|---------|-------|
| 1 | Bu ADR'nin yazılması (bağlayıcı sınır + cümle) | Architect agent | ✅ 2026-09-24 |
| 2 | k8-servis/README.md + k15-medya-streaming/README.md'ye sınır cümlesinin birebir yazılması | Vault Steward | ⏳ gelecek adım |
| 3 | k8-servis/media-service.md'den FFmpeg ayrıntısının temizlenmesi + K15 SSOT notu (satır edit) | Backend Architect | ⏳ gelecek adım |
| 4 | k15-medya-streaming/ffmpeg-pipeline.md'ye "tek FFmpeg SSOT'u" beyanı | Embedded / Media | ⏳ gelecek adım |
| 5 | FFmpeg taraması: k8-servis/ içinde FFmpeg=0 (README sınır cümlesi hariç) | MO (vault-updater) | ⏳ |
| 6 | Onay akışı: Tech Lead → Arch Lead | Vault Steward | ⏳ |

### §7.2 Geri Dönüş Planı

Sınır uygulanamazsa (örn. K15 arayüzü iş emrini taşıyamıyorsa): (1) cümleler README'lerden satır edit ile kaldırılır, (2) yeni bir ADR ile alternatif A (K8.2 sahipliği) yeniden değerlendirilir, (3) FFmpeg SSOT'u tek dosyaya taşınır, (4) log.md append ile revert kaydedilir. Mevcut hiçbir dosya silinmez (In-Place #4).

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
| [[adlandirma-kurali]] | §1.1: K8.2 = servis ucu, K15 = boru hattı sahibi (bağlayıcı) |
| [[katman-baglilik-matrisi]] | K8.2 → K15 izinli oku; K15 → K16-20 izni YOK (tek hedef K14) |
| [[katman-sayim-rehberi]] | K8 (a=10) ve K15 (a=9) satırlarında K8.2 düğümü |
| [[ADR-023-hibrit-derinlik]] | Sınır kararının derinlik/kanıt bağlamı |
| [[ADR-024-surucu-firmware-birlesme]] | Benzer sınır/sahiplik kararı (firmware → K1.f) |
| [[ADR-026-sayim-birimi-5000]] | K8.2 düğümünün sayım birimiyle eşlenmesi |
| k8-servis/README.md | Sınır cümlesi hedefi (gelecek adım) |
| k15-medya-streaming/README.md | Sınır cümlesi hedefi (gelecek adım) |
| k15-medya-streaming/ffmpeg-pipeline.md | Tek FFmpeg SSOT'u |

---

## §9 Salt Okunur Doğrulama

| # | Kontrol | Beklenen | Uygunsuzsa |
|---|---------|----------|------------|
| 1 | Sınır cümlesi iki README'de var mı ve birebir mi | 2 kopya, identical | Gelecek adım henüz yapılmamış (§7.1 adım 2) |
| 2 | k8-servis/ içinde FFmpeg ayrıntısı | 0 (cümle hariç) | Sınır ihlali |
| 3 | k15-medya-streaming/ içinde FFmpeg | ≥1 (SSOT) | SSOT kayıp |
| 4 | K15 → K8.2 bağımlılık referansı | 0 | Dependency Rule ihlali |
| 5 | Yön oku: K8.2 → K15 | matriste izinli | K matrisi ihlali |

---

## §10 Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 1.0.0 | 2026-09-24 | İlk yazım — 3 tur / 20 persona uzlaşması: K8.2 servis ucu + K15 boru hattı sahibi + birebir sınır cümlesi |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-24
**Mode:** Red Team · Human Mode · Truth Mode
