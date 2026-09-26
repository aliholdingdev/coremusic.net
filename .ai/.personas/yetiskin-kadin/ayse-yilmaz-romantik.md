---
title: "CoreMusic — Persona: Ayşe Yılmaz"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/yetiskin-kadin/ayse-yilmaz-romantik"
updated: 2026-09-26
group: yetiskin-kadin
age: 32
mood: Romantik
persona_id: CM-AY-32-ROM-IST
veli_onayı_gerekli: false
---

# CoreMusic — Persona: Ayşe Yılmaz

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **yetiskin-kadin** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 32 yaşındaki bir öğretmenin — romantik/duygusal dinleyicinin — gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Romantik), veri → [[research-bank]] (P1-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | yetiskin-kadin (25-45) |
| Birincil mood | Romantik ([[personas/index]] §6.3 ataması) |
| Test odağı | Duygusal UI, renk paleti, **slow geçiş animasyonları** ([[mood-taxonomy]] §3.2.1); profil + ödeme formu (ADR-023 satır 13 — **ödeme PLANNED ⚠️**); nostalji ikinciliyle arama kalitesi |
| Veli onayı | `veli_onayı_gerekli: false` (yetişkin — 16 yaş altı politikası uygulanmaz) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Mood-geçiş & duygusal UI (ADR-023 satır 16) | Level 2 + 3 | Romantik arayüz geçişleri; geçersiz mood değeri hata (error) üretir; slow animasyon beklentisi |
| Profil & ödeme formu (ADR-023 satır 13) | Level 2 | Yetişkin kadın test temsilcisi — **ödeme akışı PLANNED ⚠️** |
| Arama (Türkçe karakter + nostalji) | Level 1 + 2 | Nostaljik ikincil küme → yıl/on yıl arama, throwback (§3.2.8) |
| Çalma listesi & favori (kişisel alan) | Level 2 + 3 | Playlist paylaşmaz; kalıcılık ve geri bildirim testi |
| Çocuk profili & içerik filtresi | Level 2 + 3 | Kurgusal çocuk hesabı (P6.5); ebeveyn kontrolü |
| Tema geçişi & kontrast denetimi | Level 2 | Gündüz light / akşam dark; 1.4.3 · 1.4.11 eşikleri |
| Yavaş ağ & hata ekranları | Level 2 + 3 | Sabır orta; suçlayıcı/uzun metin yasak; Slow 4G koşulu |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P1-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| ≥10 test adımı (Adım / Eylem / Beklenen / Doğrulama / Metrik / WCAG) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: eski vault'taki 100+ satırlık fiziksel/aile/maaş detayı bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5); üniversite/bölüm adı (X-6), maaş/gelir ve sağlık verisi (6698 m.6) yazılmaz.

**Okuma sırası (bu dosyayı kullanan agent için):**

| Sıra | Dosya | Ne için |
|------|-------|---------|
| 1 | [[personas/persona-template]] | Zorunlu iskelet (§3.3 blok, §3.5 alan havuzu) |
| 2 | [[research-bank]] P1-P8 | Tek gerçek-dünya veri kaynağı + etiket sözlüğü (§4.1) |
| 3 | [[mood-taxonomy]] §3.2.1 (+ §3.2.8) | Küme adı + tanım alıntısı (Romantik / Nostaljik) |
| 4 | [[personas/index]] §6.3 | Yaş/mood/filename ataması (SSOT) |
| 5 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | Ayşe Yılmaz | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 32 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3) |
| **Doğum Tarihi** | 14 Şubat 1994 | Kaynak: kurgusal (persona verisi; yaş 32 ile tutarlılık için türetildi) |
| **Cinsiyet** | Kadın | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | İstanbul / Kadıköy / Moda | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | Öğretmen — Türk Dili ve Edebiyatı (ortaöğretim) | Kaynak: kurgusal (persona verisi); lisans üniversitesi/bölüm resmi adı P2 kapsamında YOK → `⚠️ VERIFICATION REQUIRED` (research-bank P2.8 / EXCLUDED X-6) |
| **Ulaşım** | Toplu taşıma (okula) + aile ortak aracı (akşam) | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | Yok (kişisel hesapta gerçek ad) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-AY-32-ROM-IST` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `AY` | Ad + soyad ilk harfleri (Ayşe Yılmaz → A, Y — ASCII katlama: Ay → `AY`) |
| `YY` | `32` | Yaş 2 hane (32 → `32`) |
| `ZZZ` | `ROM` | Mood tür kodu (Romantik → ROM) |
| `XXX` | `IST` | Şehir kodu (İstanbul → IST) |

*Not (yetişkin persona):* `veli_onayı_gerekli: false` — KVKK/yaş sınırı notu 16 yaş altı persona'lar içindir; bu persona için uygulanmaz. Dayanak çerçevesi yine de doğrudur: TMK m.11 erginlik **18**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir; research-bank P6.3/P6.4). Bu dosya tamamen kurgusaldır: gerçek kişi verisi yazılmaz (6698 m.5/1 → `Kaynak: [k1] + [k2]`). Kurgusal çocuk verisi (Defne, 3) **her zaman kurgusal** kalır (P6.5).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki 100+ satırlık ayrıntı (ten rengi, uyku düzeni, beslenme, sağlık tanısı, reçete numarası…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 165 cm (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | Orta yapı, normal siluet (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Koyu kumral dalgalı omuz hizası saç, ela göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşithe cihazı** | Kısıt yok (kurgu); gözlük/lens **numarası ve reçete YAZILMADI** — 6698 m.6 sağlık verisidir | Kaynak: kurgusal (persona verisi); kısıt verisi: yazılmadı (P6.5) |
| **Giyim / temsil notu** | İşte düz kesim pantolon-bluz-hırka, günlükte kot/basic; profil fotoğrafında sıcak/sade tonlar (renk eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Uzun okuma/sınav kağıdı eforu + okulda tek elle hızlı kullanım → net tipografi, büyük hedef; görsel yorgunlukta kolay okunur metin | Kaynak: kurgusal (persona verisi); WCAG 2.5.8 (≥24×24 CSS px) kriteri: `Kaynak: [k1] + [k2]` |

*Sağlık verisi notu:* Eski dosyadaki sağlık ayrıntıları (kronik rahatsızlık, ilaç/reçete, uyku-beslenme tanısı vb.) 6698 m.6 kapsamında **sağlık verisidir** → bu dosyada **yazılmadı**; persona'da herhangi bir sağlık kısıtı varsa dahi **kurgusal olması zorunludur** (research-bank P6.5).

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 60 | Sınıfta öğrencileriyle rahat ve enerjiktir; tanımadığı kalabalık ortamlarda içine kapanır, yakın çevre dardır — akşam evde sessizliği tercih eder. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 80 | Empatisi yüksektir; öğrencilerinin duygusal durumunu hemen algılar, çatışmadan kaçınır ve arabulucu rolünü üstlenir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 70 | Ders planları, sınav kağıtları, veli toplantıları zamanında ve düzenlidir; evde düzeni küçük çocuk nedeniyle kısmen koruyabilir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 50 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 50 = orta nokta; müzik/şiir/film gibi sanatsal tetikleyiciler ruh halini hızlı değiştirir, stresli dönemlerde hafif dalgalanma yaşar ama kriz anında soğukkanlıdır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 70 | Edebiyat öğretmeni — entelektüel merak yüksektir; yeni kitap/müzik/kültür çeker; büyük değişikliklerde temkinlidir, rutin içindeki küçük yeniliklere açıktır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; eski `/10` puanları ×10 ile ölçeklendi (6/10 → 60, 8/10 → 80, 7/10 → 70, 5/10 → 50, 7/10 → 70); normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). Şablon satırındaki O/C/E/A harf eşlemesi bu tam adlarla çelişir → P7.5 kuralı ("kod esastır") uygulanır, çelişki `⚠️ VERIFICATION REQUIRED` olarak §3.5.11'e kayıtlıdır. |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Romantik |
| **İkincil küme** | Nostaljik ([[mood-taxonomy]] §3.2.8 — 68 persona içinde **atamasız** küme; eski dosyadan ikincil olarak taşındı, silinmez §4.5/§3.4 kuralı) |
| **Küme tanımı (alıntı)** | "Aşk/duygu odaklı dinleyici; şarkıya duygusal bağ kurar, az skip eder, tekrar dinler" ([[mood-taxonomy]] §3.2.1) |
| **Tetikleyici durumlar** | Eski şarkı/anı tetikleyicisi (nostalji); akşam-gece yalnız zaman; yorgunluk sonrası sakinleştirici müzik; sınıfta/okulda hızlı, kesintisiz erişim |
| **UI etkisi** | "Yumuşak renkler, yavaş animasyonlar, sıcak tonlar" + test etkisi: "Duygusal UI, renk paleti, **slow geçiş animasyonları**; ADR-023 satır 16 (mood-geçiş)" ([[mood-taxonomy]] §3.2.1) — ADR-023 satır 13: "Profil + ödeme formu (**ödeme PLANNED ⚠️**)" |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Romantik); ikincil = Nostaljik (eski dosya; §3.2.8'de atamasızdır) — §7.2'de kayıtlı |
| Test etkisi | ADR-023 satır 13 (profil + ödeme formu) + satır 16 (mood-geçiş) + §3.2.1 slow animasyon |
| Taksonomi BPM notu | §3.2.1 "60-100 BPM ⚠️" işareti eski taksonomi verisidir → `⚠️ VERIFICATION REQUIRED` (bkz. §3.5.5) |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Duygusal/slow UI (§3.2.1) | Geçişler yavaş ve yumuşak; müziği kesmez; ani parlaklık/keskin animasyon yok | Manuel + trace + screenshot |
| 2 | Mood-geçiş (ADR-023 satır 16) | Geçerli mood → arayüz uyarlanır; geçersiz mood değeri hata (error) üretir | Manuel + hata akışı testi |
| 3 | Nostalji (§3.2.8) | Yıl/on yıl filtresi ve "eski şarkılar" araması doğru sonuç verir | DOM assertion + içerik denetimi |
| 4 | Az skip, tekrar dinleme (§3.2.1) | "Devam et" listesi son dinleneni korur; sayaç kaybı yok | LocalStorage + sayaç kontrolü |
| 5 | Kişisel alan (playlist paylaşmaz) | Playlist varsayılan gizli; paylaşım açık onay ister | Form assertion + manuel |
| 6 | Profil & ödeme formu (ADR-023 satır 13) | Form adım adım ve erişilebilir (3.3.7 / 3.3.8); **ödeme akışı PLANNED ⚠️** | Form assertion + a11y audit |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı** | 1) Türkçe pop (90'lar & 2000'ler) 2) Slow / akustik 3) Türk sanat müziği 4) Jazz / blues 5) Lo-fi / chill (sözsüz) | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Candan Erçetin, Mabel Matiz, Zeki Müren, Sezen Aksu, Sertab Erener | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4 kapsamında değil → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **BPM aralığı (tür)** | Pop **80-120** BPM (Türkçe pop köprüsü için; P4.4) | Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (research-bank P4.4 `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)`) |
| **BPM (mood / romantik)** | **60-100 BPM** — eski taksonomi verisi | `⚠️ VERIFICATION REQUIRED` ([[mood-taxonomy]] §3.2.1 + §1.2 toplu etiket; research-bank P4.5'te romantik aralık YOK) |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | Yavaş-orta tempo; duygusal/sakin parça ağırlığı (kurgusal tercih) | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 07:15-07:45 (yola çıkış), 10:00-10:15 (teneffüs, kulaklık), 17:00-17:30 (dönüş), 21:00-22:00 (Defne uyuduktan sonra — ana ritüel) · Hafta sonu 09:00-11:00 (ev işi, enerjik pop) | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (eski Spotify alışkanlığından geçiş — kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Pasif kaşif: algoritmanın "Senin için" listesine güvenir; arkadaş önerilerini dener, özel çaba harcamaz | Kaynak: kurgusal (persona verisi) |
| **Premium olasılığı** | %75 (aile planı + reklamsız/offline alışkanlığı güçlü — kurgusal) | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Türkçe pop (90'lar & 2000'ler) | Lise/ Walkman dönemi; ilk aşk/acı dediğinde o dönemin sesi gelir |
| 2 | Slow / akustik | Üniversite kütüphanesi + kitap okurken fon; melankolik ama huzurlu |
| 3 | Türk sanat müziği | Babasının evde çaldığı çocukluk sesi; akşamlarda güvence hissi |
| 4 | Jazz / blues | Eşle tanışma anının müziği (ilk buluşma mekânı); kişisel ritüel |
| 5 | Lo-fi / chill | Sözsüz olduğu için sınav kağıdı okurken/ev işinde odak sağlar |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 07:15-07:45 | Yola çıkış (toplu taşıma/araç) | Hafif Türkçe pop |
| Hafta içi | 10:00-10:15 | Teneffüs (kulaklık, 10 dk) | Slow / jazz |
| Hafta içi | 17:00-17:30 | Okul dönüşü | Slow Türkçe pop |
| Hafta içi | 21:00-22:00 | Defne uyuduktan sonra — kitap/sınav kağıdı | Jazz / blues + lo-fi |
| Hafta sonu | 09:00-11:00 | Ev işi, aile | Enerjik pop / TSM |
| Hafta sonu | 21:30 | Eşle baş başa (ritüel) | Slow / jazz (kişisel alan) |

**CoreMusic kullanım alışkanlıkları (kurgusal — eski salt-okunur kaynaktan taşındı):**

| Alan | Değer |
|------|-------|
| Kullanım sıklığı | Günde ~2-3 saat, haftada 5-6 gün |
| En çok kullandığı 5 özellik | Kişiselleştirilmiş öneriler · Çalma listeleri · Sanatçı sayfaları · Arama · Çocuk modu |
| En az kullandığı özellik | Sosyal paylaşım, canlı radyo, podcast |
| Playlist alışkanlığı | ~8 tematik liste; ayda 1 günceller; **kimseyle paylaşmaz** |
| Offline ihtiyacı | Yüksek (yolda/metroda); çevrimdışı erişim beklentisi |
| Sosyal paylaşım | Neredeyse yok — müziği kişisel alan olarak görür |

*Not: BPM satırları yalnız tür aralığı düzeyindedir (P4.4, VERIFIED ikincil); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). Romantik mood aralığı (60-100) ve Nostaljik mood aralığı (70-120) eski taksonomi verisidir → `⚠️ VERIFICATION REQUIRED` (mood-taxonomy §1.2).*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | iPhone 14 (eski salt-okunur dosyadan) | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları YAZILMADI; bankada VERIFIED cihaz yalnız Samsung Galaxy A34 5G |
| **Ekran çözünürlüğü → viewport** | Persona cihazına özel viewport YOK (spec doğrulanmadı); platform test girdisi: A34 **1080 × 2340** (VERIFIED) → `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | Çözünürlük: `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`); viewport: `⚠️ DERIVED` — türetme: çözünürlük ÷ DPR (P3.2) — persona cihazına **uygulanmaz** |
| **OS** | iOS sürümü doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / X-1) — sürüm yazılmadı |
| **Tarayıcı** | Safari (mobil/masaüstü — sürüm kurgusal; P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | Ev fiber (hız kurgusal) / mobil veri kurgusal | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Kulaklık** | Kablosuz kulaklık (dışarıda) + evde over-ear — model spec'siz | `⚠️ VERIFICATION REQUIRED` (X-1) — model/spec yazılmadı |
| **Tema** | light (gündüz) / dark (akşam) — tema geçişi testi girdisi | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Günde 6-8 saat (iş + kişisel — kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 7/10 — temel kullanımda rahat, sorun çözmede eşine danışır; eğitim teknolojilerine aşina | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | Dizüstü bilgisayar, tablet, akşam hoparlör (mutfak), ev televizyonu, araç multimedya — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır (platform test varsayımı, `⚠️ DERIVED`); persona'nın kendi cihaz viewport'u **yoktur** (spec doğrulanmadı — X-1). `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmiyor).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | kısıt yok (kurgu); uzun süreli okuma/sınav kağıdı eforu | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11); hover/focus içeriği kuralı (1.4.13) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | kısıt yok (kurgu); okul/ev ortamı gürültülü, kulaklıkla dinleme yoğun | Sesli/işitsel geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | kısıt yok; ince motor iyi, tek elle hızlı kullanım (okul koridoru/taşıma) | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · sürüklemesiz tek dokunuş (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | dikkat orta-yüksek; evde küçük çocuk varken düşer; adım adım rehber ister | Erişilebilir kimlik doğrulama min. (3.3.8) · tekrar bilgi girişi yok (3.3.7) · tutarlı yardım (3.2.6) · odak görünürlüğü 2.4.7 + 2.4.11 | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | Kurgusal çocuk hesabı verisi 6698 m.6 kapsamında hassastır → persona ve çocuk verisi **kurgusal olmak zorunda** (research-bank P6.5) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| 72×72 / 64×64 dp gibi tasarım hedefleri | Bankada yok → bu dosyada YAZILMADI; asgari güvenli eşik = 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Romantik ve nostaljiktir: eski şarkılar/anılar onu güçlü tetikler; az skip eder, aynı parçayı tekrar dinler.
- Empatiktir: karşısındakinin duygusunu hemen anlar; çatışmadan kaçınır, arabulucudur.
- Planlı ama esnektir: ders ve ev düzenini yürütür; küçük aksaklıkları tolere eder, tekrar eden hatada vazgeçer.
- Müziği kişisel alan olarak görür: playlist'lerini paylaşmaz, sosyal özellikleri kullanmaz.
- Yeni platforma temkinli başlar: önce araştırır, rehberli tur ister, alışınca sadık kalır.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Araştırmacı: büyük alışverişte yorum/öneri okur; kitapta impulsif olabilir; aile planına yatkın | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | Orta (~5 sn); yavaş yükleme en çok rahatsız ettiği şey ("çocuk uyurken beklemek istemem") | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Küçük hataları görmezden gelir; tekrar eden hata/uzun teknik metin uygulamadan soğutur | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 3/10 — reklamsızlık alışkanlığı güçlü; atlanamayan reklamda bıkkınlık | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Rehberli tur + adım adım yönlendirme; uzun doküman okumaz, gösterilmesini ister | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Sosyal paylaşım minimal; eşine tek tıkla link gönderir | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | 22:00 sonrası sade, tek dokunuşlu akış; karmaşık menü reddedilir | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Sade Türkçe arayüz + tutarlı yardım (3.2.6) + şeffaf fiyat → güvenir; belirsizlikte alternatif arar | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.2.1 |

*Erişilebilirlik etkisi (özet):* uzun okuma eforu + tekrar eden görev yorgunluğu → kontrast ≥ **4.5:1** (1.4.3), dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kimlik doğrulama zihinsel yükü düşük (3.3.8) ve tekrar bilgi girişi olmaması (3.3.7), geri tuşu/odak kaybolmaz (2.4.11) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Ayşe Yılmaz'sın. 32 yaşındasın, İstanbul/Kadıköy-Moda'da yaşıyorsun, ortaöğretimde Türk Dili ve Edebiyatı öğretmenisin; evlisin, 3 yaşındaki kızın Defne ile ilgileniyorsun.
Romantik bir kişiliğin var: şarkıya duygusal bağ kurar, az skip eder, tekrar dinlersin; ikincil olarak Nostaljik'sin — eski şarkılar ve anılar seni güçlü tetikler.
Müzik zevkin: Türkçe pop (90'lar/2000'ler), slow/akustik, Türk sanat müziği, jazz/blues, lo-fi. En sevdiklerin: Candan Erçetin, Mabel Matiz, Zeki Müren, Sezen Aksu, Sertab Erener.
Telefonun (model spec'i doğrulanmadı); dizüstü, tablet, mutfak hoparlörü ve araç multimedyan var ama hepsinin spec'i doğrulanmadı; teknoloji seviyen 7/10, gündüz light akşam dark temadasın.
Görme / işitme / hareket kısıtların yok; uzun okuma/sınav kağıdı eforun var — net tipografi, büyük hedefler ve yavaş, yumuşak geçişler beklersin.
Sabır eşiğin ~5 sn; yavaş yükleme ve tekrar eden hata seni soğutur, adımların kısa ve rehberli olmasını istersin.
Test edilecek akışlar: mood-geçiş (duygusal UI), profil & ödeme formu (ödeme PLANNED ⚠️), arama (Türkçe karakter + nostalji), çalma listesi/favori, çocuk profili, tema geçişi, hata kurtarma, erişilebilirlik denetimi.
Davranış notları:
- Playlist'lerin kişisel alanıdır; paylaşım varsayılan kapalı olmalı, açık onay istenmeli.
- "Senin için" önerileri seni tanımalı; nostalji filtreleri (yıl/on yıl) aradığın şeydir.
- Adım adım rehberli akış ve tutarlı yardım (3.2.6) seni rahatlatır; belirsiz form adımlarında takılırsın.
- Türkçe karakterli içerikte bozulma (mojibake) görürsen güvenin düşer.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; çift süslü parantezli yer tutucu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, sıcak ama saygılı ton — romantik persona duygusal bağ bekler |
| Çocuk verisi yasak | Prompt'ta gerçek çocuk verisi yok; kızın Defne kurgudur (P6.5) |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| Adım | Eylem | Beklenen | Doğrulama | Metrik (research-bank P8 değeri) | WCAG kriteri |
|------|-------|----------|-----------|----------------------------------|--------------|
| 1 | Ana sayfa yükleme (kişisel profil) | LCP ≤ **2500 ms**, kişiselleştirilmiş selamlama + öneriler görünür, CLS ≤ **0.1** | Performance trace + screenshot | LCP ≤ **2500 ms** · CLS ≤ **0.1** (P8.1 `VERIFIED`) | 1.4.3 (`VERIFIED`) |
| 2 | Mood-geçiş (Romantik arayüz) | Geçerli mood → yumuşak renk/yavaş geçiş; geçersiz mood değeri hata (error) üretir (ADR-023 satır 16) | Manuel + hata akışı testi | — (P8 dışı) | 2.4.7 (`VERIFIED`) |
| 3 | Arama (Türkçe karakter: ç, ğ, ı, İ, ö, ş, ü) | Spesifik sorgu (sanatçı + parça) en üstte; sonuçlar INP eşiği içinde görünür; mojibake yok | DOM assertion + timing + `vault-utf8-writer verify` | INP ≤ **200 ms** (P8.1 `VERIFIED`) | 2.5.8 (`VERIFIED`) |
| 4 | SPA navigasyon (sayfalar arası) | Müzik kesintisiz; geri tuşu çalışır; CLS ≤ **0.1** | Trace + console log | CLS ≤ **0.1** (P8.1 `VERIFIED`) | 2.4.11 (`VERIFIED`) |
| 5 | Player kontrol (play / duraklat / ileri-geri / söz) | İlk içerik < **1 sn** (FCP); INP ≤ **200 ms**; sürükleme yerine erişilebilir alternatif | Trace + console log + a11y audit | FCP ≤ **1 sn** · INP ≤ **200 ms** (P8.1 `VERIFIED`) | 2.5.7 (`VERIFIED`) |
| 6 | Çalma listesi oluştur / düzenle / sil | CRUD anında yansır; liste kalıcı; Türkçe ad kabul | DOM assertion + LocalStorage kontrolü | INP ≤ **200 ms** (P8.1 `VERIFIED`) | 2.5.8 (`VERIFIED`) |
| 7 | Favori (kalp) ekleme | Anlık görsel geri bildirim; durum localStorage'da kalıcı | DOM assertion + LocalStorage kontrolü | INP ≤ **200 ms** (P8.1 `VERIFIED`) | 2.5.8 (`VERIFIED`) |
| 8 | Çoklu cihaz devam (telefon → masaüstü) | Aynı parça aynı yerden devam; playlist senkron | Level 3 E2E + manuel | — (P8 dışı) | — |
| 9 | Tema geçişi (light / dark) | Geçiş müziği kesmez; iki temada da metin ≥ **4.5:1**, UI ≥ **3:1** | Manuel + ekran görüntüsü + kontrast denetimi | — (P8 dışı; eşik P5) | 1.4.3 · 1.4.11 (`VERIFIED`) |
| 10 | Kırılma noktası kontrolü | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) görünümlerinde düzen bozulmaz, yatay kaydırma yok | Playwright `setViewportSize` + screenshot (`⚠️ DERIVED` viewport) | FCP ≤ **1 sn** (P8.1 `VERIFIED`) | 2.5.8 (`VERIFIED`) |
| 11 | Bağlantı yavaşlatma | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok, geri bildirim net | Network throttling + trace | Slow 4G profili: **150 ms · 1.6 Mbps ↓ / 750 Kbps ↑** (P8.3 `VERIFIED`) | — |
| 12 | Hata sayfaları (404 / kopma) | Türkçe, kısa, yol gösteren metin; yığın izni görünmez | Manuel + screenshot | — (P8 dışı) | 3.3.8 (`VERIFIED`) |
| 13 | Giriş / kimlik doğrulama akışı | Kimlik doğrulama zihinsel yükü düşük; formda tekrar bilgi girişi yok | Form assertion + a11y audit | — (P8 dışı; eşik P5) | 3.3.7 · 3.3.8 (`VERIFIED`) |
| 14 | Tutarlı yardım erişimi | Yardım/SSS bağlantısı her sayfada aynı yerde erişilebilir | DOM assertion + a11y audit | — (P8 dışı; eşik P5) | 3.2.6 (`VERIFIED`) |
| 15 | Odak sırası (klavye/gezinme) | Odak sırası mantıklı; odak görünür; gizli/örtülü odak yok | Klavye navigasyon testi + DOM assertion | — (P8 dışı) | 2.4.7 · 2.4.11 (`VERIFIED`) |
| 16 | Erişilebilirlik denetimi | Lighthouse A11y + axe; 1.4.3 / 1.4.11 / 1.4.13 / 2.5.7 / 2.5.8 ihlali yok | Lighthouse / axe | — (P8 dışı; eşik P5) | 1.4.3 · 1.4.11 · 1.4.13 · 2.5.7 · 2.5.8 (`VERIFIED`) |
| 17 | Çocuk profili & içerik filtresi (Defne — kurgu) | Çocuk profili ayrı; yaşa uygun içerik filtresi çalışır; ebeveyn kontrolü erişilebilir (3.3.8) | Level 3 E2E + manuel içerik denetimi | — (P8 dışı) | 3.3.8 (`VERIFIED`) |

*Asgari 10 satır (şablon §3.5.10) — 17 satır yazıldı. Her satırda 6 sütun zorunlu: `Adım` · `Eylem` · `Beklenen` · `Doğrulama` · `Metrik (research-bank P8 değeri)` · `WCAG kriteri`. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); WCAG kodları yalnız P5.2/P5.4 listesinden (P5'te geçmeyen kod kullanılmadı); persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir).*

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, doğum tarihi, semt, medeni durum, kurgusal kız (Defne, 3), ulaşım, rumuz | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (32) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | `veli_onayı_gerekli: false` (yetişkin) | Kural sonucu | research-bank P6.3/P6.4 (16 yaş altı politikası bu persona için geçersiz) | — | `Kaynak: kurgusal + kural (P6.4 uygulaması)` |
| 4 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Fiziksel özet satırları (boy, yapı, saç/göz, giyim) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 6 | Gözlük/lens numarası, sağlık tanısı, uyku/beslenme detayı yazılmadı (m.6 hassasiyeti) | Kural uygulaması | research-bank P6.5 | — | `Kaynak: [k1] + [k2]` (P6 `VERIFIED`) + uygulama: yazılmadı |
| 7 | Üniversite/bölüm resmi adı (eski dosyada adı geçiyor) | Doğrulanmadı (EXCLUDED) | research-bank P2.8 / X-6 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı; yalnız meslek kurgu |
| 8 | Big Five puanları (60/80/70/50/70 — 0-100) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 9 | `/10` → 0-100 normalize yöntemi (×10) | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 10 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 11 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 12 | Boyut kodu eşlemesi (şablon O/C/E/A ↔ P7.3 tam adları) | Vault içi çelişki | persona-template §3.5.3 + mood-taxonomy §3.2.1 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 13 | Mood küme adı (Romantik) | Vault referanslı atama | [[mood-taxonomy]] §3.2.1 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); ad mood-taxonomy §3 listesinden` |
| 14 | Mood küme tanımı alıntısı + UI/test etkisi (slow animasyon) | Vault verisi | [[mood-taxonomy]] §3.2.1 | [[ADR-023-persona-driven-testing]] (satır 13/16) | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 15 | İkincil mood (Nostaljik) | Vault listesinde var — atamasız küme | eski salt-okunur persona dosyası | mood-taxonomy §3.2.8 (atamasız, korunur) | `Kaynak: kurgusal (atama)` + `⚠️ VERIFICATION REQUIRED` (atama vs. §7.2 notu) |
| 16 | Romantik mood BPM aralığı (60-100) | Doğrulanmadı (eski taksonomi) | mood-taxonomy §3.2.1 + §1.2 | — | `⚠️ VERIFICATION REQUIRED` |
| 17 | Eski dosya BPM aralığı (70-110) | Vault içi çelişki + doğrulanmamış | eski salt-okunur persona dosyası | mood-taxonomy §3.2.1 (60-100) | `CONFLICT ⚠️` — iki değer de yazıldı + `⚠️ VERIFICATION REQUIRED` (tek değer indirgenmedi) |
| 18 | Tür BPM aralığı (Pop 80-120) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 19 | Şarkı bazlı BPM | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 20 | Favori sanatçı adları (Candan Erçetin, Mabel Matiz, Zeki Müren, Sezen Aksu, Sertab Erener) | Persona tercihi (kurgusal); sanatçı realitesi P4 dışında | research-bank P4.2 (kapsam dışı) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 21 | Cihaz: iPhone 14 (spec'siz) | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 22 | Platform test cihazı A34: 6.6" AMOLED, 1080×2340, 19.5:9, ~390 ppi, 120 Hz, Android 13 | Gerçek-dünya | GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) | Samsung resmi + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 23 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş (platform test girdisi) | Türetme: 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 24 | Diğer cihazlar (dizüstü, tablet, hoparlör, TV, araç multimedya) spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 25 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 26 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 1.4.13 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.2.6 · 3.3.7 · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 27 | Persona renk kombinasyonu (light/dark tema) kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 28 | P5 kapsamında olmayan AA kriterleri (1.4.5, 1.4.10, 2.4.3, 3.3.x dışı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 29 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 30 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 31 | Eski dosyadaki istatistik/maaş/sağlık/üniversite satırları (yüzdeler, gelir aralığı, sağlık tanısı, üniversite adı) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 32 | §6.1/§6.2 sayaçları (test adımı, kaynak satırı, derinlik) | İç tutarlılık | Bu dosyanın §3.10/§3.11 tabloları | `vault-utf8-writer verify` | `Kaynak: kurgusal (dosya içi sayım; verify ile doğrulanır)` |

---

## §4 Kurallar

### ZORUNLU: ŞABLON ÖNCE (Template-First) — Persona

**Bu persona dosyasını yazmadan ÖNCE `.ai/.templates/` dizinindeki ilgili şablonu oku:**

1. Şablonu `[[../.templates/index]]` §7.1 tablolarından seç → persona için `personas/persona-template.md`.
2. Şablonu oku; `{{VARIABLE}}` alanlarını doldur, gereksiz anlatım bloklarını kaldır (§3.5 alan havuzu KALIR).
3. **Şablon varsa ona göre yaz.**
4. **Şablon yoksa** bu belgedeki 8-bölüm formatına göre yaz ve `⚠️ VERIFICATION REQUIRED` notuyla `log.md`'ye şablon eksiğini bildir.
5. Şablon okunmadan yazılan persona dosyası **geçersizdir** (Guardrail #16): revert edilir, `log.md`'ye ERROR girilir.

| Durum | Aksiyon |
|-------|---------|
| `.ai/.templates/personas/persona-template.md` VAR | Şablonu oku → ona göre yaz |
| Şablon YOK | 8-bölüm formatına göre yaz + `log.md`'ye "şablon eksiği" kaydı |
| Şablon okundu ama çelişiyor | DUR → `[[../CLAUDE.md]]` §2.1 SSOT öncelik sırası |
| Vault erişilemiyor | `⚠️ VERIFICATION REQUIRED` → yazım durdurulur, kullanıcıya sor |

*(Bu blok persona-template §3.3'ten birebir kopyalanmıştır; §4'ün ilk maddesidir, silinemez.)*

### §4.1 Bağlayıcı Kurallar

| # | Kural | Uygulama | İhlal Sonucu |
|---|-------|----------|--------------|
| 1 | Template Mandatory (Guardrail #16) | Bu dosya persona-template'den üretildi | Dosya geçersiz, revert |
| 2 | Şablon Önce | Yazmadan `.ai/.templates/` okundu | ERROR log, yazıma devam yok |
| 3 | SSOT | Persona kataloğu [[personas/index]]; araştırma verisi [[research-bank]] | İçerik silinir |
| 4 | Zero Hallucination (ADR-005) | Her satır §4.5 etiketi taşır; bankada olmayan iddia yazılmaz | Etiketsiz iddia silinir |
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`ayse-yilmaz-romantik.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; kurgusal çocuk verisi P6.5'e tabidir | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `iPhone 14 viewport = 390×844` (spec doğrulanmadı) | `spec yok — ⚠️ VERIFICATION REQUIRED (X-1); test viewport = banka A34 türevi ⚠️ DERIVED` |
| `Şarkı 92 BPM` / `romantik 60-100 BPM (doğrulanmış)` | Tür aralığı (Pop 80-120, VERIFIED ikincil) + mood BPM: `⚠️ VERIFICATION REQUIRED` + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `üniversite/bölüm adı (eski dosya)` / `maaş aralığı (eski dosya)` | `üniversite/bölüm adı: ⚠️ VERIFICATION REQUIRED (X-6)` + `maaş/gelir: bankada yok → yazılmadı (ADR-005)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P1-P8 + [[mood-taxonomy]] §3.2.1/§3.2.8 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
| 3 | Eski salt-okunur persona kaynağını incele (yalnız kimlik/müzik/rutin; etiketsiz/m.addField istatistik iddia taşınmaz) | Kurgusal içerik |
| 4 | §3.5 11/11 alanı doldur → her satıra §4.5 etiketi yaz | Dolu §3 |
| 5 | Test Adımları ≥10 (6 sütun) → §6.1 kontrol listesi → `vault-utf8-writer verify` → `log.md` append | Doğrulanmış dosya |

**Adım ayrıntıları:**

| Adım | Ayrıntı | Sıklık |
|------|---------|--------|
| 1 | `.ai/.templates/personas/persona-template.md` §3.3 + §3.5; `.ai/.templates/docs-md-template.md` | Her üretimde |
| 2 | research-bank P1 (demografi), P2 (okul/meslek), P3 (cihaz), P4 (müzik/BPM), P5 (WCAG), P6 (KVKK), P7 (Big Five), P8 (test eşikleri) | Her üretimde |
| 3 | Eski dosya: `.ai/personas/yetiskin-kadin/` salt-okunur; yalnız kurgusal kimlik/müzik/rutin taşınır (maaş/istatistik/sağlık/bölüm adı asla birebir kopyalanmaz) | Yalnız ilk üretimde |
| 4 | 11 alt bölüm tek tek doldurulur; boş bırakılan alan `yok` olarak işaretlenir | Her üretimde |
| 5 | `node .ai/scripts/vault-utf8-writer.mjs verify --file <dosya>` → `lines ≥ 506`, `mojibake = 0` | Yazım sonrası zorunlu |

```text
ŞABLONU OKU → BANKA/TAKSONOMİ/INDEX OKU → ESKİ KAYNAKTAN KURGU TAŞI → 11/11 ALAN → ETİKETLE → TEST ADIMLARI ≥10 (6 SÜTUN) → §6.1 → VERIFY → LOG
```

### §5.2 Hata Modları

| Hata | Belirti | Düzeltme |
|------|---------|----------|
| Etiketsiz satır | §6.1 "kaynak" maddesi düşer | §4.5 formatıyla etiketle |
| Şarkı BPM'i / uydurma 2. kaynak | ADR-005 ihlali | Sil + `⚠️ VERIFICATION REQUIRED` |
| Eksik alan havuzu | 11/11 düşer | §3.5'e dön; boş alan `yok` diye işaretle |
| Mojibake | `verify.mojibake > 0` | `vault-utf8-writer repair` |
| Çift süslü parantez | §4 Template-First bloğu dışındaki yer tutucu | Yer tutucuyu doldur veya sil |
| 506 satır altı | `verify.lines < 506` | Açıklama satırı/tablo ekle, yeniden verify |
| Sağlık/maaş/bölüm adı sızıntısı | 6698 m.6 / X-6 ihlali | Sil → `⚠️ VERIFICATION REQUIRED` + §7.2'ye kayıt |
| Persona-özel eşik uydurma | "Ayşe için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`) + ek alanlar (`group`, `age`, `mood`, `persona_id`, `veli_onayı_gerekli`) → toplam 12 alan
- [ ] Tek H1 + Zorunlu Bağlantılar satırı (7 wiki-link, emre satır 18 birebir sıra)
- [ ] §1-§7 başlıkları eksiksiz; §3 altında 11 alt bölüm (Kimlik → Kaynak & Doğrulama) dolu
- [ ] §4'ün ilk maddesi = §3.3 Şablon-Önce bloğu birebir (5 madde + Durum/Aksiyon tablosu)
- [ ] Dosya derinliği ≥ 506 satır (`verify` → `lines`)
- [ ] Her alanda/satırda kaynak etiketi var (`kurgusal` · `[k1] + [k2]` · `⚠️ VERIFICATION REQUIRED` · `⚠️ DERIVED`)
- [ ] Big Five 5/5 boyut dolu, 0-100 aralığında, her birinde 1 satır gerekçe + ters kodlama notu (N)
- [ ] Test Adımları tablosu ≥ 10 satır ve 6 sütun (Adım / Eylem / Beklenen / Doğrulama / Metrik (research-bank P8 değeri) / WCAG kriteri)
- [ ] AI Rol Kartı `Sen ...'sin` paragrafıyla başlıyor, yer tutucu (çift süslü parantez) kalmamış — §4 Template-First bloğundaki `VARIABLE` kural metni hariç
- [ ] Wiki-link'ler `[[...]]` formatında; hedefi olmayanlar §7.1'de `📋 planlanan`
- [ ] Yetişkin persona: `veli_onayı_gerekli: false`; "KVKK 16" YANLIŞ notu var; 16 yaş altı veli akışı yok
- [ ] Cihaz: yalnız A34 spec'i VERIFIED (platform test girdisi); persona cihazı `⚠️ VERIFICATION REQUIRED`; viewport `⚠️ DERIVED`
- [ ] WCAG kodları yalnız P5 listesinden (1.4.3, 1.4.11, 1.4.13, 2.4.7, 2.4.11, 2.5.7, 2.5.8, 3.2.6, 3.3.7, 3.3.8); persona renkleri `⚠️ DERIVED`
- [ ] BPM yalnız tür aralığı (P4.4); mood BPM `⚠️ VERIFICATION REQUIRED`; şarkı BPM'i yok (X-2)
- [ ] Maaş/gelir/sağlık/üniversite-bölüm adı YOK (6698 m.6 / X-6)
- [ ] Mojibake yok (`verify` → mojibake 0, BOM false)
- [ ] §7.2 Çakışma Kayıtları + §7.3 Değişiklik Geçmişi (append-only) + Authority footer mevcut

### §6.2 Quality Report

| Metrik | Değer |
|--------|-------|
| Versiyon | 1.0.0 |
| Durum | Red Team · Human Mode · Truth Mode verified |
| Bölüm | 7 (H1 + §1-§7) + frontmatter |
| Frontmatter alan | 12 (7 zorunlu + 5 ek) |
| Alan havuzu | 11/11 alt bölüm |
| Test adımı | 17 (asgari 10) — 6 sütun |
| Kaynak & Doğrulama satırı | 32 |
| Dil | Türkçe (mojibake yasak) |
| Derinlik | ≥ 506 satır (`verify` → `lines`) |
| Kayıt | `log.md` append-only |

---

## §7 Referanslar

### §7.1 Wiki-link Referansları

| Wiki-link | İlişki | Durum / Gerçek hedef |
|-----------|--------|----------------------|
| `[[personas/index]]` | Persona kataloğu — yaş/mood atamasının sahibi | ✅ `.ai/.personas/index.md` |
| `[[personas/persona-template]]` | Persona şablonu — bu dosyanın iskeleti | ✅ `.ai/.templates/personas/persona-template.md` |
| `[[research-bank]]` | Araştırma paketleri P1-P8 — tek gerçek-dünya veri kaynağı | ✅ `.ai/.personas/research-bank.md` |
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.2.1 Romantik / §3.2.8 Nostaljik) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (bu turda başka agent sorumluluğunda; diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 32 | 32 | Tutarlı — [[personas/index]] §6.3 ile birebir; çelişki yok |
| Doğum tarihi | 14 Şubat 1994 | 14 Şubat 1994 | Yaş 32 ile tutarlı (2026 − 1994 = 32); kurgusal korundu |
| İkincil mood | Nostaljik | Nostaljik (§3.2.8 — **atamasız** küme) | Mood-taxonomy §3 listesinde adı VAR → taşındı; "atamasız/kullanılmayan" notu §3.5.4 ve §3.5.11'de saklı, silinmedi |
| BPM aralığı | 70-110 BPM (eski persona) | 60-100 BPM (mood-taxonomy §3.2.1) | `CONFLICT ⚠️` — **iki değer de doğrulanmamış** → tek değere indirgenmedi; ikisi de `⚠️ VERIFICATION REQUIRED` (§3.5.5 + §3.5.11 satır 17) |
| Birincil cihaz | iPhone 14 (spec'li sunum) | Test viewport: banka A34 türevi (`⚠️ DERIVED`); iPhone 14 spec'siz `⚠️ VERIFICATION REQUIRED` | [[research-bank]] P3 — X-1 |
| Üniversite / bölüm adı | Eski dosyada üniversite adı + lisans bölümü adı (X-6 gereği yazılmaz) | yazılmadı (yalnız meslek kurgu) | P2.8 / EXCLUDED X-6 — üniversite/bölüm adları bu pakette YOK |
| Maaş / aile geliri | Eski dosyada maaş ve aile geliri aralıkları (TL/ay — bankada yok) | yazılmadı | §4.5 kuralı 4 (bankada yok → yazma; ADR-005) |
| Sağlık detayları (m.6) | Eski dosyada tanı, gözlük numarası ve uyku/beslenme detayları | yazılmadı | P6.5 — sağlık verisi kurgusal olmak zorunda; numara/reçete de yazılmadı |
| Etki alanındaki istatistikler (eski "Kaynaklar" bölümü) | Spotify TR %38, TÜİK %87, BTK 92 milyon vb. yüzdeler | yazılmadı | §4.5 kuralı 4 — bankada olmayan istatistik üretilmez |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P1-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik/rutin taşındı (maaş/istatistik/sağlık/bölüm adı kırpıldı), yaş index §6.3 ile doğrulandı (32 = 32), Big Five /10 → 0-100 ölçeklendi (⚠️ DERIVED), 6 sütunlu 17 satırlık test tablosu + 32 satırlık kaynak tablosu, ikincil mood Nostaljik ve BPM çelişkileri §7.2'ye kaydedildi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/yetiskin-kadin/ayse-yilmaz-romantik
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
