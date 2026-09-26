---
title: "CoreMusic — Persona: İlayda Erdem"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/kiz-cocuk/ilayda-erdem"
updated: 2026-09-26
group: kiz-cocuk
age: 7
mood: Enerjik
persona_id: CM-IE-07-ENE-DNZ
veli_onayı_gerekli: true
---

# CoreMusic — Persona: İlayda Erdem

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **kiz-cocuk (4-11 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 7 yaşındaki bir çocuğun gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Enerjik / Lider), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | kiz-cocuk (4-11) |
| Birincil mood | Enerjik ([[personas/index]] §6.3 ataması — eski dosyada Lider idi, §7.2) |
| Test odağı | Hızlı navigasyon + skip performansı, anlık geri bildirim, playlist oluşturucu, sıralama kontrolü (Lider ikincil), ebeveyn kilidi, erişilebilirlik denetimi |
| Veli onayı | `veli_onayı_gerekli: true` (16 yaş altı — ⚠️ DERIVED, [[research-bank]] P6.4) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Çocuk modu ana sayfa / keşif | Level 2 + 3 | Enerjik: hızlı navigasyon, anlık geri bildirim, sık skip (ADR-023 satır 12) |
| Playlist oluşturucu (amaçlı listeler) | Level 2 + 3 | "Odak/Ödev/Strateji" listeleri; Lider ikincil: sıralama/önceliklendirme kontrolü |
| Sesli arama (çocuk sesi) | Level 2 + 3 | 7 yaş telaffuzu; kısa sorgu; anlık geri bildirim (ADR-023 satır 18) |
| Ebeveyn kilidi & rol/izin akışı | Level 2 + 3 | Lider test etkisi: "ebeveyn ≠ çocuk" rol ayrımı (mood-taxonomy §3.3 satır 12) |
| Erişilebilirlik denetimi (kontrast / hedef boyu) | Level 2 | 2.5.8 ≥24×24 px, 1.4.3 ≥4.5:1 denetimi |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P3-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| ≥10 test adımı (Adım / Beklenen / Doğrulama yöntemi) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: eski vault'taki 500+ satırlık fiziksel/psikolojik detay bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5). Eski dosyadaki "sosyal medya (Instagram)" satırı 13 yaş altı için taşınmadı (P1/COPPA) → §3.11 `⚠️ VERIFICATION REQUIRED`.

**Okuma sırası (bu dosyayı kullanan agent için):**

| Sıra | Dosya | Ne için |
|------|-------|---------|
| 1 | [[personas/persona-template]] | Zorunlu iskelet (§3.3 blok, §3.5 alan havuzu) |
| 2 | [[research-bank]] P3-P8 | Tek gerçek-dünya veri kaynağı + etiket sözlüğü (§4.1) |
| 3 | [[mood-taxonomy]] | Küme adı + tanım alıntısı (yalnız §3 listesi) |
| 4 | [[personas/index]] §6.3 | Yaş/mood/filename ataması (SSOT) |
| 5 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | İlayda Erdem | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 7 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3 — eski dosyada 11 idi, §7.2) |
| **Doğum Tarihi** | 12 Şubat 2019 (yaş 7 ile tutarlılık için türetildi — eski dosyada 25 Aralık 2015 idi, §7.2) | Kaynak: kurgusal (persona verisi) |
| **Cinsiyet** | Kız | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Denizli / Pamukkale / Karşıyaka Mahallesi | Kaynak: kurgusal (persona verisi; eski dosyadan taşındı) |
| **Okul / Sınıf** | Denizli Özel Pamukkale İlkokulu, 2. sınıf | Kaynak: kurgusal (persona verisi; yaş 7 ile tutarlılık için türetildi — eski dosyada Ortaokulu 6. sınıf idi, §7.2) |
| **Kardeş** | 2 (bir abla Dilara 14, bir erkek kardeş Ege 5) — ortanca çocuk | Kaynak: kurgusal (persona verisi; Ege'nin yaşı yaş 7 ile tutarlılık için 7→5 türetildi, §7.2) |
| **Online Rumuz** | Yok (aile hesabında alt profil) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-IE-07-ENE-DNZ` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `IE` | Ad + soyad ilk harfleri (İlayda Erdem → İ, E) |
| `YY` | `07` | Yaş 2 hane (7 → `07`; şablon örnek aralığı 10-99'dur, 10 altı yaşlarda sıfırla — ⚠️ DERIVED format notu) |
| `ZZZ` | `ENE` | Mood tür kodu (Enerjik → ENE) |
| `XXX` | `DNZ` | Şehir kodu (Denizli → DNZ) |

> **KVKK / yaş sınırı notu (zorunlu — 16 yaş altı):** Bu persona 7 yaşındadır → frontmatter'de `veli_onayı_gerekli: true`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) → `Kaynak: [k1] + [k2]` (research-bank P6.4 VERIFIED). "16 yaş altı → veli onayı" CoreMusic politika sonucudur → `⚠️ DERIVED` (P6.4 son satır). Persona tamamen kurgusaldır; gerçek çocuk verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki 500+ satırlık ayrıntı (ayakkabı numarası, kan grubu, burç, postür, persentil yüzdeleri…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır. Persentil/WHO istatistikleri doğrulanamadığı için TAŞINMADI → `⚠️ VERIFICATION REQUIRED` (§3.11).

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 125 cm (yaşıtlarına göre normal bant — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 24 kg, atletik/hareketli | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Koyu kumral düz saç (omuz hizası), koyu kahverengi göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok (görme/işitme taramaları normal — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Preppy/klasik-şık; okul forması, lacivert-gri-beyaz; profil görseli: Pamukkale beyazları temalı | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | İnce motor gelişiyor; hızlı hareket eder, aceleci dokunur → hedefler ≥24×24 px + tolere edilen tıklama alanı | Kaynak: kurgusal (persona verisi); WCAG 2.5.8 (≥24×24 CSS px) kriteri: `Kaynak: [k1] + [k2]` |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 80 | Sınıfında fikirlerini yüksek sesle savunur, oyun kurallarını kendisi koyar; topluluk önünde konuşmaktan çekinmez (eski dosya: münazara lideri — yaş 7'ye uyarlandı, §7.2). | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 70 | Diplomatiktir: "Argümanla ikna et, güçle değil" ilkesini evde ablasıyla tartışırken uygular; farklı görüşlere saygılı. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 100 | Renkli ajandası/plan panosu vardır; ödevini sıraya koyar, "bitirmeden oynamam" der; hiç ödev kaçırmaz. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 20 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 20 = çok dengeli taraf; tartışma öncesi "stres" yaşamaz, soğukkanlıdır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 90 | Yeni argümanlara, yeni oyunlara, yeni müzik türlerine açık; "neden?" sorusunu sorar, meraklıdır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir**; eski dosyadaki 10'luk puanlar 0-100'e ölçeklendi (§7.2) |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). Şablon satırındaki O/C/E/A harf eşlemesi bu tam adlarla çelişir → P7.5 kuralı ("kod esastır") uygulanır, çelişki `⚠️ VERIFICATION REQUIRED` olarak §3.11'e kayıtlıdır. Mood-taxonomy §3.2.2'deki "Yüksek Dışadönüklük (O), yüksek Deneyime Açıklık (A)" harf kullanımı da P7.3'e **ters**tir — kod esastır: E=Dışadönüklük, O=Açıklık. |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Enerjik ([[personas/index]] §6.3 — SSOT) |
| **İkincil küme** | Lider (eski salt-okunur dosyada birincil idi; ad [[mood-taxonomy]] §3.3 satır 12 ile birebir — çelişki §7.2) |
| **Küme tanımı (alıntı)** | "Yüksek enerjili, sık skip eden, yeni şarkı keşfeden ve playlist oluşturan dinleyici" ([[mood-taxonomy]] §3.2.2) |
| **İkincil küme tanımı (alıntı)** | "Öncü, yönlendiren; listeleri ve sıralamayı kontrol eder" ([[mood-taxonomy]] §3.3 satır 12) |
| **Tetikleyici durumlar** | Sabah/okul sonrası hareketli an (Enerjik); önerilen içeriğin tekrar etmesi (sıkılma → skip); sıra/plan değişikliği (Lider: kontrol ihtiyacı); ödev/odak zamanı (eski dosya: işlevsel dinleme) |
| **UI etkisi** | Enerjik: hızlı navigasyon, anlık geri bildirim, skip performansı, canlı renkler (mood-taxonomy §3.2.2 "Test etkisi"); Lider ikincil: sıralama/önceliklendirme kontrolleri, rol/izin akışı "ebeveyn ≠ çocuk" (§3.3 satır 12) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Enerjik); ikincil = eski salt-okunur persona kaynağı (Lider) — çelişki kaydı §7.2'de |
| Test etkisi | ADR-023 satır 12 (hızlı navigasyon + geri tuşu) — [[mood-taxonomy]] §3.2.2; skip/INP performansı bu satırın alt kırılımıdır |
| BPM çelişkisi | §3.2.2'deki "120-160 BPM ⚠️" taksonomi düzeyinde işaretli önermedir; test eşikleri P4.4 tür aralıkları kullanılır (§7.2) |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Sık skip (Enerjik) | Skip sonrası anlık geri bildirim; INP ≤ **200 ms** | Performance trace (P8.1 VERIFIED; ADR-023 satır 12) |
| 2 | Yeni şarkı keşfi (Enerjik) | Her gün ≥1 yeni öneri; "Keşfet" bloğu her oturumda tazedir | Manuel + DOM assertion |
| 3 | Playlist oluşturma (Enerjik) | Amaçlı liste oluşturucu ("Ödev", "Oyun") akışı çalışır; adlandırma + sıralama | Manuel + form assertion |
| 4 | Hızlı navigasyon (Enerjik) | Geri tuşu her ekranda görünür ve kaybolmaz (2.4.11) | DOM assertion + a11y audit |
| 5 | Sıralama/rol kontrolü (Lider ikincil) | Liste sıralama kontrolü görünür; "ebeveyn ≠ çocuk" rol/izin ayrımı korunur | Manuel + form assertion (mood-taxonomy §3.3 satır 12) |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Klasik Müzik (Barok) 2) Lo-Fi / Chillhop 3) Caz 4) Klasik Rock 5) Türk Sanat Müziği (enstrümantal) | Kaynak: kurgusal (persona verisi — eski dosyadan taşındı) |
| **Favori sanatçılar (eserlerle)** | Bach, Vivaldi, Queen, Beatles, Nina Simone | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4.2/P4.3 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **En sevdiği eserler (kurgusal)** | "We Are the Champions", "Dört Mevsim", "Air on the G String", "Bohemian Rhapsody", "Feeling Good" | Kaynak: kurgusal (persona verisi); eser-icra eşleşmesi gerçek-dünya bilgisi → `⚠️ VERIFICATION REQUIRED` |
| **BPM aralığı (tür)** | İlayda'nın türleri (klasik, lo-fi, caz, klasik rock, TSM) **P4.4'te YOK**. Bankada karşılığı olan referans türler: Pop **80-120** · Dance **110-135** · Electro **90-130** (P4.4) | Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (research-bank P4.4 `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)`) + `⚠️ VERIFICATION REQUIRED` (karşılığı olmayan türler) |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı; eski dosyadaki "65-110 / odak 65-85 / motivasyon 100-110" kişisel aralığı X-2 nedeniyle olgu olarak TAŞINMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | Amaçlı dinleme: ödev/odak için yavaş-tempo, oyun/enerji için hareketli tempo (kurgusal tercih, ölçüm değil) | Kaynak: kurgusal (persona verisi) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 16:30-17:00 (ödev), 18:00-18:30 (oyun/enerji), 20:30-21:00 (uyku öncesi sakin piyano) · Hafta sonu 10:00-10:30 (kahvaltı caz), 15:00-16:00 (serbest keşif) | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (Çocuk Modu) + aile TV'si + ortak hoparlör | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Kendi keşfetti (verimli ödev müziği ararken); hızlı skip eder, her gün yeni bir şey dener, playlist'leri amaçlı kurar | Kaynak: kurgusal (persona verisi) + mood: [[mood-taxonomy]] §3.2.2 |
| **CoreMusic davranışı** | Anne hesabıyla 15 organize playlist ("Odaklanma", "Oyun", "Strateji"…), ~120 favori (kriter: işe yarıyor mu) | Kaynak: kurgusal (persona verisi — eski dosyadan taşındı; sayılar kurgusaldır) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Klasik Müzik (Barok) | Zihinsel düzen + odaklanma; ödev öncesi "beyin hazırlığı" ritüeli |
| 2 | Lo-Fi / Chillhop | Ödev sırasında arka plan; söz dağıtmaz, tempoyu korur |
| 3 | Caz | Babasıyla ortak zevk; sofrada/kitap okurken dinlenir |
| 4 | Klasik Rock | Zafer marşı hissi; "kazandım" enerjisi verir |
| 5 | TSM (enstrümantal) | Aile büyükleriyle bağ; yatıştırıcı |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 07:30-08:00 | Kahvaltı & hazırlık | Hareketli pop/dans (Enerjik sabah) |
| Hafta içi | 16:30-17:00 | Ödev/okul sonrası | Lo-Fi / Barok (odak) |
| Hafta içi | 18:00-18:30 | Oyun, koşu, enerji boşalımı | Klasik rock (enerji) |
| Hafta içi | 19:30-20:00 | Aile zamanı | Caz (ortak) |
| Hafta içi | 20:30-21:00 | Uyku öncesi | Sakin piyano (yavaş) |
| Hafta sonu | 10:00-10:30 | Kahvaltı | Caz vokal |
| Hafta sonu | 15:00-16:00 | Serbest keşif | Karışık (kendi seçimi) |

*Not: BPM satırları yalnızca bankada bulunan tür aralığı düzeyindedir (P4.4); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). Mood-taxonomy §3.2.2 Enerjik'in "120-160 BPM" önermesi taksonomide `⚠️` işaretlidir; test hedefi olarak P4.4 aralıkları kullanılır (§7.2).*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Samsung Galaxy A34 5G | Kaynak: GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) + Samsung resmi (samsung.com — "Resolution (Main Display) 1080 x 2340") |
| **Ekran çözünürlüğü → viewport** | 1080 × 2340 px → `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: çözünürlük ÷ DPR (research-bank P3.2); üretilmiş değer, 2. bağımsız kaynak **değildir** |
| **OS** | Android 13 (A34 için 4 büyük OS güncellemesi) | Kaynak: GSMArena + Samsung resmi / en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G (P3.3 `VERIFIED (3 kaynak)`) |
| **Tarayıcı** | Chrome (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | 75 Mbps ev fiberi (kurgusal) | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Tema** | light (çocuk modu renkli varyantla; Enerjik → canlı renkler) | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.2.2 |
| **Ekran süresi / kullanım** | Hafta içi 45-60 dk/gün, hafta sonu 60-90 dk/gün (ebeveyn limiti — limit değeri kurgu) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 5/10 — oyun/çizim uygulamaları, video izler, sunum yapmaya yeni başlıyor; sosyal medya YOK (13 yaş altı) | Kaynak: kurgusal (persona verisi); yaş sınırı: research-bank P1 (COPPA 13) |
| **Diğer cihazlar (eski dosyadan)** | MacBook Air M2, iPhone 13, iPad, AirPods Pro — spec'leri bu turda DOĞRULANMADI; "iPhone kendine ait" ibaresi yaş 7 ile tutarsız → taşınmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır; `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmedi).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | yok (kurgu) | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) — canlı renkli Enerjik temada kontrast denetimi kritik | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | normal | İşitsel geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | ince motor gelişiyor; aceleci/hızlı dokunur | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · sürükleme zorunlu değil (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | 2. sınıf okuma gelişiyor; odak 15-20 dk (ilgi alanında) | Erişilebilir kimlik doğrulama min. (3.3.8) — ebeveyn kilidi dostane · odak görünürlüğü 2.4.7 + 2.4.11 | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | "Okuma-yazma gelişimi / dikkat süresi" ifadeleri 6698 m.6 kapsamında algı verisi sayılabilir → persona'da **kurgusal olması zorunlu** (research-bank P6.5) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| Büyük buton dp hedefleri (88×88 vb.) | Bankada yok → bu dosyada YAZILMADI; asgari güvenli eşik = 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Enerjik: yerinde duramaz, coşkuyla anlatır, sık skip eder; her hareketin anlık karşılığını ister — sabırsız ama mutludur.
- Lider (ikincil): listeleri ve sıralamayı kendisi kontrol etmek ister; "sıra bizim" oyunu kurar; ebeveyn ≠ çocuk rol/izin ayrımını hisseder ama test eder.
- Planlı/ajandacı (C=100): renkli plan panosu, "önce ödev sonra oyun" düzeni; sıra bozulursa itiraz eder.
- İkna etmeyi sever: "İtiraz ediyorum!" cümlesi evdeki favorisidir; ablasıyla yapıcı rekabet (kurgu).
- Müziği işlevsel kullanır: ödev için odak, oyun için enerji, uyku için sakin — her anın müziği vardır.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Ebeveyn onaylı — İlayda "ister", anne "eğitim yatırımı" olarak almıştır (eski dosya) | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | 2-3 sn'de sıkılır; yükleme spinner'ına ~6 sn dayanır (Enerjik — sabırsız) | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.2.2 |
| **Hata tepkisi** | "Ama neden?!" diye itiraz eder, hemen tekrar dener; teknik metin görürse anne'ye yönelir — dostça dil şart | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 2/10 — 5. saniyede "anne, geçelim mi?" | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Hareket + tekrar + oyunlaştırma; sesli komut ve ikonlu arama güçlü girdidir, uzun metin zayıf | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Keşfettiğini hemen anlatır, listesini arkadaşına dayatır; sırayı paylaşır ama sırayı o kurar | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | 21:00 sonrası enerji düşer ama zihin "bir tane daha" der; sakinleştirme akışı beklenir | Kaynak: kurgusal (persona verisi) |
| **Kontrol tetikleyicisi** | Sıralama/izin bozulursa (Lider) veya gecikme olursa (Enerjik) hayal kırıklığı; anlık geri bildirim + görünür sıra → güven | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.2.2 + §3.3 satır 12 |

*Erişilebilirlik etkisi (özet):* 2. sınıf okuma evresi + aceleci dokunma → dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3), kimlik doğrulama dostane (3.3.8) — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen İlayda Erdem'sin. 7 yaşındasın, Denizli/Pamukkale'de yaşıyorsun, Denizli Özel Pamukkale İlkokulu 2. sınıf öğrencisisin.
Enerjik bir kişiliğin var; ikincil olarak Lider'sin — hızlı hareket edersin, sık sık şarkıları değiştirirsin (skip), yeni şarkılar keşfeder ve playlist'leri kendin kurarsın; listeleri ve sıralamayı sen kontrol etmek istersin.
Müzik zevkin: barok klasik (Bach, Vivaldi), lo-fi, caz, klasik rock (Queen, Beatles), enstrümantal Türk sanat müziği (Nina Simone). Müziği işlevsel kullanırsın: ödev için odak, oyun için enerji, uyku için sakin.
Samsung Galaxy A34 5G kullanıyorsun, light temada, evde internet 75 Mbps (kurgusal).
Görme / işitme / hareket kısıtların yok; ince motorun gelişiyor ve aceleci dokunuyorsun — büyük hedefler, anlık geri bildirim ve görünür "geri" düğmesi beklersin.
Sabır eşiğin 2-3 sn, spinner'a ~6 sn dayanırsın; satın alma tamamen annenin onayına bağlıdır; rol/izin ayrımı (ebeveyn ≠ çocuk) senin için belirgindir.
Test edilecek akışlar: çocuk modu ana sayfa, keşfet/öneri bloğu, hızlı navigasyon + skip, playlist oluşturucu + sıralama, sesli arama, ebeveyn kilidi & rol/izin akışı, hata ekranları, oryantasyon değişimi, arka plan müzik, erişilebilirlik denetimi.
Davranış notları:
- Aynı şarkıyı 3'ten fazla dinlemezsin; her gün yeni bir keşif beklersin, tekrar eden öneride hemen skip edersin.
- Her ekranda "geri" ve "sıradaki" hızlı erişilebilir olmalı; gecikmeden sıkılırsın, anlık geri bildirim beklersin.
- Listeleri ve sıralamayı senin kontrol etmen gerekir; ebeveyn ayrıcalığı ("annemin listesi") net ayrılmalıdır.
- Türkçe telaffuzun net ama çocuksudur; arayüzde "sen" dili, kısa metinler ve büyük ikonlar beklersin.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, kısa cümleler — 7 yaş persona'sı enerjik/acelecidir; uzun komut dağıtmaz |
| Sürpriz yasak | Enerjik keşif ihtiyacı nedeniyle tekrarlayan/kilitli öneri döngüsü yok; her adımda geri tuşu ve "başka tür" çıkışı açık bırakılır |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| # | Adım | Beklenen | Doğrulama yöntemi |
|---|------|----------|-------------------|
| 1 | Ana sayfa yükleme (çocuk modu) | LCP ≤ **2500 ms**, karşılama + keşif bloğu üstte, CLS ≤ **0.1** | Performance trace + screenshot (eşikler: research-bank P8.1 `VERIFIED`) |
| 2 | Keşfet / öneri bloğu (Enerjik) | Her oturumda taze öneri; "yeni" etiketi görünür; tekrar eden öneri hemen değiştirilebilir | Manuel + DOM assertion |
| 3 | Hızlı skip navigasyon (ADR-023 satır 12) | INP ≤ **200 ms**; skip sonrası anlık geri bildirim; geri tuşu kaybolmaz (2.4.11) | Performance trace + DOM assertion |
| 4 | Kategori gezinme (hızlı) | Metin bağımlı olmayan navigasyon; dokunma hedefi ≥ **24×24 CSS px** (2.5.8) | Erişilebilirlik audit (axe / Lighthouse) |
| 5 | Şarkı çalma (play / duraklat) | İlk ses < 1 sn; büyük oynat kontrolü; odak görünür (2.4.7) | Trace + console log + a11y audit |
| 6 | Playlist oluşturucu (amaçlı liste) | Adlandırma ("Ödev"/"Oyun") + oluşturma tamamlanır; durum kalıcı | Form assertion + LocalStorage kontrolü |
| 7 | Liste sıralama/önceliklendirme (Lider ikincil) | Sürükleyerek/tıklayarak sıralama çalışır; sıra geri alınabilir (undo) | Manuel + DOM assertion (mood-taxonomy §3.3 satır 12) |
| 8 | Favori (kalp) ekleme | Görsel + sesli geri bildirim; durum localStorage'da kalıcı | DOM assertion + LocalStorage kontrolü |
| 9 | Ebeveyn kilidi & rol/izin akışı | Kilit devrede; "ebeveyn ≠ çocuk" ayrımı; ses %65'te sınırlı; mesaj dostça | Form assertion + 3.3.8 denetimi |
| 10 | Oryantasyon değişimi (yatay/dikey) | Geçiş < 400 ms; içerik kaybı yok; müzik kesintisiz | Trace + Manuel |
| 11 | SPA navigasyon (sayfalar arası) | Müzik kesintisiz; hissedilir gecikme yok; CLS ≤ 0.1 | Trace + console log |
| 12 | Bağlantı yavaşlatma | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace (P8.3 `VERIFIED`) |
| 13 | Hata sayfaları (404 / kopma) | Türkçe, dostça metin; büyük "Tekrar Dene" hedefi; offline keşif önerisi | Manuel + screenshot |
| 14 | Erişilebilirlik denetimi | Lighthouse A11y + axe; 1.4.3 / 1.4.11 / 2.4.7 / 2.5.8 ihlali yok; canlı tema kontrastı dahil | Lighthouse / axe |
| 15 | Kırılma noktası kontrolü | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) görünümlerinde düzen bozulmaz | Playwright `setViewportSize` + screenshot (`⚠️ DERIVED` viewport) |
| 16 | Türkçe karakter içeriği (ç, ğ, ı, İ, ö, ş, ü) | Menü ve şarkı adlarında karakter bozulmaz (mojibake yok) | `vault-utf8-writer verify` + ekran görüntüsü |

*Asgari 10 satır (şablon §3.5.10) — 16 satır yazıldı. Her satırda 3 sütun zorunlu: `Adım` · `Beklenen` · `Doğrulama yöntemi`. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir).*

**Ek doğrulama notları (test koşusundan önce — research-bank P8 `VERIFIED`):**

- **TBT:** > 50 ms ise uzun görev (long task) main thread'i bloklar; TBT, INP'nin laboratuvar proxy'sidir. `Kaynak: [k1] + [k2]` (web.dev/articles/vitals + web.dev/optimize-vitals-lighthouse — research-bank P8.6 `VERIFIED`)
- **Lighthouse v8 performans skor ağırlıkları:** LCP 25 · TBT 30 · CLS 15 · FCP 10 · Speed Index 10 · TTI 10 — skor yorumu sürüm duyarlıdır (v8 baz). `Kaynak: [k1] + [k2]` (github.com/GoogleChrome/lighthouse docs/v8-perf-faq.md + googlechrome.github.io/lighthouse/scorecalc — research-bank P8.6 `VERIFIED`)
- **Test kırılım noktaları (P8.5 — 9 adet):** 320 × 568 · 375 × 667 · 375 × 812 · 414 × 896 · 768 × 1024 · 1280 × 720 · 1600 × 1200 · 1920 × 1080 · 2560 × 1440 — persona viewport'u (`360 × 780` / `412 × 915`, `⚠️ DERIVED` — §3) bu setle çapraz kontrol edilir. `Kaynak: [k1] + [k2]` (playwright.dev/docs/emulation + mintlify mobile-emulation — research-bank P8.5 `VERIFIED`)
- **Eşiklerin yorumu:** LCP/INP/CLS/FCP eşikleri 75. yüzdelik alanına göredir; Test Adımları tablosundaki eşikler research-bank P8'den alınır, persona'ya özel metrik hedefi bu dosyaya yazılmaz. `Kaynak: [k1] + [k2]` (web.dev/articles/vitals + web.dev/articles/defining-core-web-vitals-thresholds — research-bank P8.1/P8.6 `VERIFIED`)

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, doğum tarihi, semt, okul, kardeş, aile detayları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (7) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | `veli_onayı_gerekli: true` (16 yaş altı politikası) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 4 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 6 | Eski dosyadaki persentil yüzdeleri (%65/%60) | Doğrulanamadı | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 7 | Big Five puanları (5×0-100) | Kurgusal persona verisi (eski 10'luk puanlardan ölçeklendi) | — | — | `Kaynak: kurgusal (persona verisi)` |
| 8 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 9 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 10 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 11 | Boyut kodu eşlemesi + mood harf kullanımı (P7.3'e ters) | Vault içi çelişki | persona-template §3.5.3 + mood-taxonomy §3.2.2/§3.3 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 12 | Mood küme adları (Enerjik / Lider) | Vault referanslı atama | [[mood-taxonomy]] §3.2.2 + §3.3 satır 12 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); adlar mood-taxonomy §3 listesinden` |
| 13 | Mood küme tanımı alıntıları | Vault verisi | [[mood-taxonomy]] §3.2.2 + §3.3 satır 12 | — | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 14 | Bankada karşılığı olan tür BPM aralıkları (Pop 80-120 · Dance 110-135 · Electro 90-130) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 15 | İlayda'nın türleri için BPM (klasik, lo-fi, caz, klasik rock, TSM) | Doğrulanmadı (P4.4'te yok) | research-bank P4.4 (17 tür listesi) | — | `⚠️ VERIFICATION REQUIRED` |
| 16 | Mood BPM önermesi "120-160" (§3.2.2) | Taksonomi önermesi, işaretli | mood-taxonomy §3.2.2 (`⚠️`) | research-bank P4.4 | `⚠️ VERIFICATION REQUIRED` — testte P4.4 kullanılır (§7.2) |
| 17 | Şarkı bazlı BPM / eski "65-110" kişisel aralığı | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 18 | Favori sanatçı/eser adları (Bach, Vivaldi, Queen, Beatles, Nina Simone) | Persona tercihi (kurgusal); realitesi P4 kapsamında değil | research-bank P4.2/P4.3 (bu isimler listede YOK) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 19 | Cihaz spec'i: A34 5G — 6.6" AMOLED, 1080×2340, 19.5:9, ~390 ppi, 120 Hz, 1000 nit, GG5, IP67, Android 13, BT 5.3 | Gerçek-dünya | GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) | Samsung resmi + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 20 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş | Türetme: 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 21 | Diğer cihazlar (MacBook Air M2, iPhone 13, iPad, AirPods) spec'leri + "iPhone kendine ait" | Doğrulanmadı (EXCLUDED); yaş 7'ye tutarsız | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı, sahiplik iddiası silindi |
| 22 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 23 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 24 | Persona renk kombinasyonu kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 25 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 26 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 27 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 28 | Playwright emülasyon alanları / breakpoint listesi | Gerçek-dünya | playwright.dev/docs/emulation | github.com/microsoft/playwright (docs/src/emulation.md) | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 29 | Eski dosyadaki istatistik/aile verileri (aile geliri, persentil, sosyal medya Instagram) | Bankada YOK / yaşa uygun değil | research-bank P1-P8 kapsamı + P1 (COPPA 13) | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 30 | Eski cihaz/okul/yaş/mood değerleri (§7.2 çakışma tablosu) | Vault içi çelişki | eski salt-okunur persona dosyası | [[personas/index]] §6.3 | Karar: index kazanır; çelişki §7.2'de saklı |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`ilayda-erdem.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 16 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Galaxy A34 viewport = 360×780` (türetilmiş ama türetilmişsiz) | `viewport ≈ 360×780 (DPR 3) — ⚠️ DERIVED` |
| `Bohemian Rhapsody 110 BPM` | Tür/eşleme (Dance 110-135, VERIFIED ikincil + ⚠️ DERIVED eşleme) + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.2.2 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
| 3 | Eski salt-okunur persona kaynağını incele (yalnız kimlik/müzik/rutin; etiketsiz iddia taşınmaz) | Kurgusal içerik |
| 4 | §3.5 11/11 alanı doldur → her satıra §4.5 etiketi yaz | Dolu §3 |
| 5 | Test Adımları ≥10 → §6.1 kontrol listesi → `vault-utf8-writer verify` → `log.md` append | Doğrulanmış dosya |

**Adım ayrıntıları:**

| Adım | Ayrıntı | Sıklık |
|------|---------|--------|
| 1 | `.ai/.templates/personas/persona-template.md` §3.3 + §3.5; `.ai/.templates/docs-md-template.md` | Her üretimde |
| 2 | research-bank P3 (cihaz), P4 (müzik/BPM), P5 (WCAG), P6 (KVKK), P7 (Big Five), P8 (test eşikleri) | Her üretimde |
| 3 | Eski dosya: `.ai/personas/kiz-cocuk/` salt-okunur; yalnız kurgusal kimlik/müzik taşınır | Yalnız ilk üretimde |
| 4 | 11 alt bölüm tek tek doldurulur; boş bırakılan alan `yok` olarak işaretlenir | Her üretimde |
| 5 | `node .ai/scripts/vault-utf8-writer.mjs verify --file <dosya>` → `lines ≥ 500`, `mojibake = 0` | Yazım sonrası zorunlu |

```text
ŞABLONU OKU → BANKA/TAKSONOMİ/INDEX OKU → ESKİ KAYNAKTAN KURGU TAŞI → 11/11 ALAN → ETİKETLE → TEST ADIMLARI ≥10 → §6.1 → VERIFY → LOG
```

### §5.2 Hata Modları

| Hata | Belirti | Düzeltme |
|------|---------|----------|
| Etiketsiz satır | §6.1 "kaynak" maddesi düşer | §4.5 formatıyla etiketle |
| Şarkı BPM'i / uydurma 2. kaynak | ADR-005 ihlali | Sil + `⚠️ VERIFICATION REQUIRED` |
| Eksik alan havuzu | 11/11 düşer | §3.5'e dön; boş alan `yok` diye işaretle |
| Mojibake | `verify.mojibake > 0` | `vault-utf8-writer repair` |
| Çift `{{` | §4 bloğu dışındaki yer tutucu | Yer tutucuyu doldur veya sil |
| 500 satır altı | `verify.lines < 500` | Açıklama satırı/tablo ekle, yeniden verify |
| Persona-özel eşik uydurma | "İlayda için INP 300 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`) + ek alanlar (`group`, `age`, `mood`, `persona_id`, `veli_onayı_gerekli`)
- [ ] Tek H1 + Zorunlu Bağlantılar satırı (≥ 1 wiki-link)
- [ ] §1-§7 başlıkları eksiksiz; §3 altında 11 alt bölüm (Kimlik → Kaynak & Doğrulama) dolu
- [ ] §4'ün ilk maddesi = §3.3 Şablon-Önce bloğu birebir (5 madde + Durum/Aksiyon tablosu)
- [ ] Dosya derinliği ≥ 500 satır (`verify` → `lines`)
- [ ] Her alanda/satırda kaynak etiketi var (`kurgusal` · `[k1] + [k2]` · `⚠️ VERIFICATION REQUIRED` · `⚠️ DERIVED`)
- [ ] Big Five 5/5 boyut dolu, 0-100 aralığında, her birinde 1 satır gerekçe + ters kodlama notu
- [ ] Test Adımları tablosu ≥ 10 satır ve 3 sütun (Adım / Beklenen / Doğrulama yöntemi)
- [ ] AI Rol Kartı `Sen ...'sin` paragrafıyla başlıyor, `{{...}}` kalmamış (yalnız §4.3 bloğundaki `{{VARIABLE}}` kural metni)
- [ ] Wiki-link'ler `[[...]]` formatında; hedefi olmayanlar §7.1'de `📋 planlanan`
- [ ] 16 yaş altı: `veli_onayı_gerekli: true` + KVKK notu (TMK 18 / COPPA 13 / GDPR 16; "KVKK 16" YANLIŞ)
- [ ] Cihaz: yalnız A34 spec'i VERIFIED; viewport `⚠️ DERIVED`; diğer cihazlar `⚠️ VERIFICATION REQUIRED`
- [ ] WCAG kodları yalnız P5 listesinden; persona renkleri `⚠️ DERIVED`
- [ ] BPM yalnız tür aralığı (P4.4/P4.5); şarkı BPM'i yok
- [ ] Mojibake yok (`verify` → mojibake 0, BOM false)
- [ ] §7.2 Çakışma Kayıtları + §7.3 Değişiklik Geçmişi (append-only) + Authority footer mevcut

### §6.2 Quality Report

| Metrik | Değer |
|--------|-------|
| Versiyon | 1.0.0 |
| Durum | Red Team · Human Mode · Truth Mode verified |
| Bölüm | 7 (H1 + §1-§7) + frontmatter |
| Alan havuzu | 11/11 alt bölüm |
| Test adımı | 16 (asgari 10) |
| Kaynak & Doğrulama satırı | 30 |
| Frontmatter alan | 7 zorunlu + 5 ek |
| Dil | Türkçe (mojibake yasak) |
| Derinlik | ≥ 500 satır (`verify` → `lines`) |
| Kayıt | `log.md` append-only |

---

## §7 Referanslar

### §7.1 Wiki-link Referansları

| Wiki-link | İlişki | Durum / Gerçek hedef |
|-----------|--------|----------------------|
| `[[personas/index]]` | Persona kataloğu — yaş/mood atamasının sahibi | ✅ `.ai/.personas/index.md` |
| `[[personas/persona-template]]` | Persona şablonu — bu dosyanın iskeleti | ✅ `.ai/.templates/personas/persona-template.md` |
| `[[research-bank]]` | Araştırma paketleri P1-P8 — tek gerçek-dünya veri kaynağı | ✅ `.ai/.personas/research-bank.md` |
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.2.2 Enerjik, §3.3 satır 12 Lider) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (bu turda başka agent sorumluluğunda; diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 11 | 7 | [[personas/index]] §6.3 kazanır |
| Mood | Lider (birincil) | Enerjik (birincil) / Lider (ikincil) | [[personas/index]] §6.3 kazanır; ikincil ad [[mood-taxonomy]] §3.3 satır 12 ile birebir |
| Doğum tarihi | 25 Aralık 2015 | 12 Şubat 2019 | Yaş 7 ile tutarlılık için kurgusal türetme |
| Okul / sınıf | Denizli Özel Pamukkale Ortaokulu, 6. sınıf | Denizli Özel Pamukkale İlkokulu, 2. sınıf | Yaş 7'ye uygun kurgusal güncelleme |
| Kardeş Ege yaşı | 7 | 5 | İlayda (7) ortanca kalması için kurgusal türetme |
| Karakter olgunluğu | "11 yaşında avukat gibi düşünür", münazara kulübü başkanı, B1+ İngilizce, Swift Playgrounds | 7 yaşa uyarlama: sınıfında fikir savunur, oyun kurallarını koyar, İngilizce dersi, oyun/çizim uygulamaları | Yaş 7 tutarlılığı; eski davranış çekirdeği (ikna, plan, liderlik) korundu |
| Birincil cihaz | MacBook Air M2 + iPhone 13 ("kendine ait") + iPad + AirPods Pro | Samsung Galaxy A34 5G (P3 VERIFIED); "kendine ait telefon" silindi | [[research-bank]] P3 + yaş 7 (P1/COPPA) |
| Ebeveyn kontrolü | "Yok (güvenilir statüsünde)" | `veli_onayı_gerekli: true` + ebeveyn kilidi devrede | P6.4 + ADR-020 (7 yaş → veli onayı zorunlu) |
| Sosyal medya | Instagram (özel, sınırlı) | yazılmadı | 13 yaş altı (COPPA 13) → taşınmadı |
| Mood BPM önermesi | Enerjik §3.2.2 "120-160 BPM ⚠️" | testte P4.4 tür aralıkları (Pop 80-120, Dance 110-135, Electro 90-130) | Taksonomi `⚠️` işaretli; research-bank VERIFIED kazanır |
| Şarkı BPM'i | "65-110 BPM / odak 65-85 / motivasyon 100-110" | yazılmadı | X-2 (şarkı BPM'i yasak); kişisel tempo kurgusal tercih olarak kaldı |
| Etki alanındaki istatistikler (aile geliri, persentil yüzdeleri) | Kaynaksız değerler | yazılmadı | §4.5 kural 4 (bankada yok → yazma) |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı, yaş/mood index §6.3'e göre düzeltildi (11→7, Lider→Enerjik, Lider ikincil kaldı); karakter 7 yaşa uyarlandı; sosyal medya/ebeveyn kontrolü çelişkileri P1/ADR-020 ile çözüldü; veli_onayı + KVKK notu eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/kiz-cocuk/ilayda-erdem
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
