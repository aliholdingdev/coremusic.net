---
title: "CoreMusic — Persona: Ada Çelik"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/kiz-cocuk/ada-celik"
updated: 2026-09-26
group: kiz-cocuk
age: 6
mood: Lider
persona_id: CM-AC-06-LID-GZT
veli_onayı_gerekli: true
---

# CoreMusic — Persona: Ada Çelik

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **kiz-cocuk (4-11 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 6 yaşındaki bir çocuğun gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Lider / Enerjik), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | kiz-cocuk (4-11) |
| Birincil mood | Lider ([[personas/index]] §6.3 ataması) |
| Test odağı | Liste sıralama/önceliklendirme, rol-izin akışı (ebeveyn ≠ çocuk), büyük dokunma hedefleri, hızlı skip |
| Veli onayı | `veli_onayı_gerekli: true` (16 yaş altı — ⚠️ DERIVED, [[research-bank]] P6.4) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Çocuk modu ana sayfa / keşif | Level 2 + 3 | Lider sıralama davranışı, Enerjik hızlı skip |
| Ebeveyn kilidi & rol/izin akışı | Level 2 | 3.3.8 erişilebilir kimlik doğrulama; çocuk ≠ yetişkin rol sınırı |
| Liste sıralama / önceliklendirme | Level 2 + 3 | Sürükleyip bırakma yerine tek dokunuşlu sıralama (2.5.7) |
| Hata ekranları & yavaş ağ | Level 2 + 3 | 8 sn spinner sabrı, dostça hata dili |
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
Kapsam dışı istisnalar: eski vault'taki 500+ satırlık fiziksel/psikolojik detay bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5).

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
| **Ad Soyad** | Ada Çelik | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 6 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3) |
| **Doğum Tarihi** | 19 Mayıs 2020 | Kaynak: kurgusal (persona verisi; yaş 6 ile tutarlılık için türetildi) |
| **Cinsiyet** | Kız | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Gaziantep / Şehitkamil / Mücahitler Mahallesi | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | Özel Gaziantep Koleji, 1. sınıf | Kaynak: kurgusal (persona verisi; yaş 6 ile tutarlılık için türetildi) |
| **Ulaşım** | Okul servisi (ev-okul ~3 km, ~20 dk) + aile aracı | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | Yok (aile hesabında alt profil) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-AC-06-LID-GZT` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `AC` | Ad + soyad ilk harfleri (Ada Çelik → A, C) |
| `YY` | `06` | Yaş 2 hane (6 → `06`; şablon örnek aralığı 10-99'dur, 10 altı yaşlarda sıfırla — ⚠️ DERIVED format notu) |
| `ZZZ` | `LID` | Mood tür kodu (Lider → LID) |
| `XXX` | `GZT` | Şehir kodu (Gaziantep → GZT; Türkçe karakter katlamı: İ→I — bu kodda Türkçe karakter yok) |

> **KVKK / yaş sınırı notu (zorunlu — 16 yaş altı):** Bu persona 6 yaşındadır → frontmatter'de `veli_onayı_gerekli: true`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) → `Kaynak: [k1] + [k2]` (research-bank P6.4 VERIFIED). "16 yaş altı → veli onayı" CoreMusic politika sonucudur → `⚠️ DERIVED` (P6.4 son satır). Persona tamamen kurgusaldır; gerçek çocuk verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki 500+ satırlık ayrıntı (ayakkabı numarası, kan grubu, burç, postür…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 121 cm (yaşıtlarına göre normal-üstü bant — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 22 kg, atletik-iri | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Koyu kumral sıkı at kuyruğu, koyu kahverengi göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok (sağlık taramaları normal — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Spor/eşofman ağırlıklı; sarı-kırmızı-turuncu enerji renkleri; profil fotoğrafında numaralı forma (kontrast: koyu forma × açık zemin — renk eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Kaba motor çok iyi, ince motor gelişiyor → hassas sürükleme yerine tek dokunuş; terli parmakla hızlı dokunma | Kaynak: kurgusal (persona verisi); WCAG 2.5.8 (≥24×24 CSS px) + 2.5.7 (sürükleme) kriterleri: `Kaynak: [k1] + [k2]` |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 88 | Takım arkadaşlarını motive eder, bayrak yarışında koçluk yapar ("hadi kızlar, yapabiliriz!"); bireysel antrenmanı da kendi başına yapabilir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 72 | Fair-play duygusu yüksektir: rakibe saygı duyar, yenilgiyi kabullenir ama bir daha yenilmemek için çalışır; sırada paylaşmayı öğreniyor. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 60 | Antrenman/oyun rutinlerine uyar (dış disiplin); okul ödevleri ve eşya toplama için hâlâ hatırlatma ister. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 32 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 32 = dengeli taraf; yarışı kaybedince birkaç dakika üzülür, enerjisini atamazsa huzursuzlaşır, sonra toparlanır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 80 | Yeni spor, yeni rota, yeni oyun hep "önce ben" der; yemek konusunda tutucudur (Anadolu mutfağı dışına şüpheyle bakar). | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). Şablon satırındaki O/C/E/A harf eşlemesi bu tam adlarla çelişir → P7.5 kuralı ("kod esastır") uygulanır, çelişki `⚠️ VERIFICATION REQUIRED` olarak §3.5.11'e kayıtlıdır. |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Lider |
| **İkincil küme** | Enerjik |
| **Küme tanımı (alıntı)** | "Öncü, yönlendiren; listeleri ve sıralamayı kontrol eder" ([[mood-taxonomy]] §3.3 satır 12) |
| **Tetikleyici durumlar** | Sıralanabilir liste görünmesi (kimin önce olacağına karar verme); takım/pist ortamı ve yarış davetiyeleri (Enerjik tetikleyicisi); sırası değiştirilemeyen/kilitli ekran (öfke — Lider kontrol kaybı) |
| **UI etkisi** | Lider: sıralama/önceliklendirme kontrolleri, rol/izin akışı (ebeveyn ≠ çocuk) ([[mood-taxonomy]] §3.3 satır 12 "Test etkisi"); Enerjik ikincilken canlı renkler, hızlı animasyon, anlık geri bildirim + kolay geri tuşu ([[mood-taxonomy]] §3.2.2) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Lider); ikincil = eski salt-okunur persona kaynağı (Enerjik) — çelişki kaydı §7.2'de |
| Test etkisi | ADR-023 satır 9 (kız çocuk grubu temsilcisi: içerik filtresi + ebeveyn kontrolü) — [[mood-taxonomy]] §3.6; Lider'e özgü rol/izin akışı bu satırın alt kırılımıdır |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Liste sıralama isteği (Lider) | Tek dokunuşla sıralama; sürükleme zorunlu değil (2.5.7) | Manuel + DOM assertion |
| 2 | Sık skip (Enerjik) | Skip sonrası anlık geri bildirim, INP ≤ **200 ms** | Performance trace (P8.1 VERIFIED) |
| 3 | Rol/izin farkı (çocuk ≠ yetişkin) | Çocuk rolünde yetki aşımında dostça engel + ebeveyn kilidi | Form assertion + 3.3.8 denetimi |
| 4 | Geri tuşu beklentisi (ADR-023) | Geri tuşu her adımda görünür, kaybolmaz (2.4.11) | DOM assertion |
| 5 | Kontrol kaybı (kilitli sıra) | Kilit ikonunda açıklama; alternatif tek dokunuş sıralama sunulur | Manuel + screenshot |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Rock / Hard Rock 2) Hip-Hop / Rap 3) Elektronik / EDM 4) Türk Halk Müziği (hareketli — halay/oyun havası) 5) Epik spor filmi müzikleri | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Duman, maNga, Ceza, Şebnem Ferah, Haluk Levent | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **Yabancı favoriler (kurgusal)** | AC/DC, Queen, Eminem, Survivor (grup) — antrenman ritüel parçaları | Kaynak: kurgusal (persona verisi); gerçek-dünya sanatçı bilgisi `⚠️ VERIFICATION REQUIRED` |
| **BPM aralığı (tür)** | Hip Hop **80-130** · Pop **80-120** · Electro **90-130** · Dance **110-135** · Techno **130-140** | Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (research-bank P4.4 `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)`) |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | Yarışma/antrenman öncesi 130-160 (yüksek enerji), ısınma 110-130, soğuma 70-90 | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 07:00-08:30 (antrenman), 16:00-17:30 (ikinci antrenman), 20:15-20:30 (sakinleşme) · Hafta sonu 07:30-09:00 (ailece koşu), 19:00-22:00 (düğün/aile) | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (Çocuk Modu) + aile TV hoparlörü | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Enerjik: BPM/enerji etiketine bakar, ilk saniyede dener, beğenmezse atlar; kaynak = antrenör/abiler önerileri + algoritma | Kaynak: kurgusal (persona verisi) |
| **Premium olasılığı** | %95 (baba "sporcu yatırımı" olarak onaylar) | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Rock / Hard Rock | Güçlü davul ve gitar riffleri adımlara uyar; antrenman playlist'inin belkemiği |
| 2 | Hip-Hop / Rap | Ritmik yapısı koşu kadansına uyar; hızlı vokal sprint motivasyonu verir |
| 3 | Elektronik / EDM | Yarışma öncesi enerji yükseltme; bass drop adrenalin hissi |
| 4 | Türk Halk Müziği (hareketli) | Antep halay/oyun havaları — düğünlerde "sosyal kardiyo", aile bağını da kurar |
| 5 | Epik spor filmi müzikleri | Yarışma sabahı ritüeli: görsel-işitsel motivasyon bloğu |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 07:00-08:30 | Atletizm antrenmanı | Rock / EDM (yüksek tempo) |
| Hafta içi | 09:00-14:30 | Okul (telefon/tablet yok) | — |
| Hafta içi | 16:00-17:30 | İkinci antrenman / oyun | Hip-hop / motivasyon mix |
| Hafta içi | 20:15-20:30 | Sakinleşme / uyku öncesi | Sakin enstrümantal (tempo düşer) |
| Hafta sonu | 07:30-09:00 | Ailece koşu | Rock klasikleri |
| Hafta sonu | 19:00-22:00 | Düğün / aile buluşması | Yöresel halay / oyun havası |

*Not: BPM satırları yalnızca tür aralığı düzeyindedir (P4.4/P4.5); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). Mood-taxonomy §3.3'te Lider müziği "Dans/pop, kararlı ritim" olarak önerilir — o da kurgusal önermedir (§1.2); test hedefi olarak P4.4 tür aralıkları kullanılır.*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Samsung Galaxy A34 5G | Kaynak: GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) + Samsung resmi (samsung.com — "Resolution (Main Display) 1080 x 2340") |
| **Ekran çözünürlüğü → viewport** | 1080 × 2340 px → `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: çözünürlük ÷ DPR (research-bank P3.2); üretilmiş değer, 2. bağımsız kaynak **değildir** |
| **OS** | Android 13 (A34 için 4 büyük OS güncellemesi) | Kaynak: GSMArena + Samsung resmi / en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G (P3.3 `VERIFIED (3 kaynak)`) |
| **Tarayıcı** | Chrome (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | 50 Mbps (ev, kurgusal) / mobil veri kurgusal | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Tema** | light (çocuk modu renkli varyantla) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Hafta içi 60 dk/gün (tüm cihazlar, spor içeriği hariç), hafta sonu 90 dk/gün (ebeveyn limiti) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 4/10 — ikon-bazlı gezinme, hızlı dokunma, temel arama; metin girişi güç | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | iPhone SE (annenin eski telefonu), Samsung Galaxy Tab S6 Lite, Garmin çocuk spor saati, Vestel Smart TV — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır; `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmedi).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | yok (kurgu) | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) — antrenman ekranında güneş altında okunabilirlik kritik | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | normal; yüksek ses eğilimi (aile sınırı koyar) | İşitsel geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | kaba motor çok iyi, ince motor gelişiyor; hızlı ama hassas değil | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · sıralama için sürükleme yerine tek dokunuş seçeneği (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | 1. sınıf okuma-yazma gelişiyor; odak 15-20 dk (antrenmanda uzun) | Erişilebilir kimlik doğrulama min. (3.3.8) — ebeveyn kilidi yetişkin için tasarlı, çocuk kilit ekranı dostane · odak görünürlüğü 2.4.7 + 2.4.11 | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | "İnce motor gelişim / okuma-yazma gelişimi" ifadesi 6698 m.6 kapsamında sağlık verisi sayılabilir → persona'da **kurgusal olması zorunlu** (research-bank P6.5) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| Büyük buton dp hedefleri (88×88 vb.) | Bankada yok → bu dosyada YAZILMADI; asgari güvenli eşik = 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Lider: takım arkadaşlarını yönlendirir, sırayı/sıralamayı kendisi kurmak ister ("önce ben, sonra sen"); kimin ne zaman dinleyeceğine o karar verir.
- Enerjik ikincil küme: antrenman bitse de "bir tur daha" der; sabah zıplayarak uyanır, yorulmak nedir bilmez.
- Rekabetçi: her şeyi yarışa çevirir; kaybedince birkaç dakika üzülür, sonra "bir dahaki sefere" moduna geçer.
- Hızlı karar verir: listeyi baştan sona okumaz, ilk görende karar kılar — arayüzde geri tuşu ve iptal çok görünür olmalıdır.
- Aile/abla etkisi güçlü: abilerinin playlist'lerini taklit eder, küçük kardeşine "büyüyünce sen de dinlersin" der.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Ebeveyn onaylı — Ada "ister", baba "sporcu yatırımı" diye karar verir | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | 3 sn'de sıkılır; yükleme spinner'ına en fazla 8 sn dayanır | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | "Neden çalışmıyor?" diye bağırır; teknik hata metnini görürse bırakır — dostça dil şart | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 2/10 — antrenmanın ortasında reklam gelirse tableti babasına uzatır | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Görsel + yaparak-öğrenme; ikon ve animasyonla çözer, uzun metin okumaz | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Sıralamayı kendi kurar; arkadaşına "bunu dinle" diye dayatır ama sırayı paylaşmayı da öğrenir | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | 20:30 sonrası dikkat süresi 5 dk'ya düşer; karmaşık akışlar reddedilir | Kaynak: kurgusal (persona verisi) |
| **Kontrol tetikleyicisi** | Sırası değiştirilemeyen/kilitli ekranlarda öfke; kontrol hissi veren sıralama/kalp ikonu → güven | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] Lider birincil küme |

*Erişilebilirlik etkisi (özet):* ince motor gelişimi + 1. sınıf okuma-yazma evresi → dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3), sıralama sürükleme zorunlu değil / tek dokunuş (2.5.7) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Ada Çelik'sin. 6 yaşında, Gaziantep/Şehitkamil-Mücahitler'de yaşıyorsun, Özel Gaziantep Koleji 1. sınıf öğrencisisin.
Lider bir kişiliğin var; ikincil olarak Enerjik'sin — takım arkadaşlarını yönlendirir, sıralamayı kendin kurmak istersin, antrenman bitse de "bir tur daha" dersin.
Müzik zevkin: rock/hard rock, hip-hop/rap, elektronik, hareketli halk müziği (halay), epik spor filmi müzikleri. En sevdiklerin: Duman, maNga, Ceza, Şebnem Ferah, Haluk Levent.
Samsung Galaxy A34 5G kullanıyorsun, light temada, internetin evde 50 Mbps (kurgusal).
Görme / işitme / hareket kısıtların yok; kaba motorun çok iyi, ince motorun gelişiyor — büyük dokunma hedefleri ve tek dokunuşlu sıralama beklersin.
Sabır eşiğin 3 sn, spinner'a 8 sn dayanırsın; satın alma tamamen babanın onayına bağlıdır.
Test edilecek akışlar: çocuk modu ana sayfa, kategori gezinme, BPM/enerji filtresi, liste sıralama (sürükleme zorunlu değil), play/duraklat, hızlı skip navigasyon + geri tuşu, favori (kalp), ebeveyn kilidi & rol/izin akışı, hata ekranları, erişilebilirlik denetimi.
Davranış notları:
- Sırayı kendin kurmak istersin; kilitli/sabit listelerde sinirlenirsin, alternatif tek dokunuş sıralama beklersin.
- Yeni öneriyi ilk saniyede dener, beğenmezsen hızla atlarsın (sık skip); yazı yazamazsın, ikonlu arama beklersin.
- Reklamı antrenmanın ortasında görmeye tahammülün yoktur; dostça "devam et" seçenekleri beklersin.
- Türkçe telaffuzun çocuksudur; arayüzde "sen" dili, kısa metinler ve büyük ikonlar beklersin.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, kısa cümleler — 6 yaş persona'sı karmaşık komut kabul etmez |
| Sürpriz yasak | Lider kontrol ihtiyacı nedeniyle prompt'ta kontrolsüz/kilitli senaryo yok; her adımda geri tuşu açık bırakılır |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| # | Adım | Beklenen | Doğrulama yöntemi |
|---|------|----------|-------------------|
| 1 | Ana sayfa yükleme (çocuk modu) | LCP ≤ **2500 ms**, karşılama + büyük ikonlar üstte, CLS ≤ **0.1** | Performance trace + screenshot (eşikler: research-bank P8.1 `VERIFIED`) |
| 2 | Ebeveyn kilidi (kayıt/ayar geçişi) | Kilit devrede; çocuk modu dışına çıkış yok; 3.3.8 (erişilebilir kimlik doğrulama) gözetilir | Manuel + form assertion; WCAG: w3.org (P5.4) |
| 3 | Kategori gezinme (Spor & Egzersiz) | Metin bağımlı olmayan navigasyon; dokunma hedefi ≥ **24×24 CSS px** (2.5.8) | Erişilebilirlik audit (axe / Lighthouse) |
| 4 | Enerji/BPM filtresi (tür aralığı) | Filtre anında uygulanır; boş sonuçta dostça "en yakın sonuçlar" mesajı | DOM assertion + timing |
| 5 | Şarkı çalma (play / duraklat) | İlk ses < 1 sn; büyük oynat kontrolü; odak görünür (2.4.7) | Trace + console log + a11y audit |
| 6 | Hızlı skip navigasyon (Enerjik davranış) | INP ≤ **200 ms**; skip sonrası anlık geri bildirim; geri tuşu kaybolmaz (2.4.11) | Performance trace + DOM assertion |
| 7 | Liste sıralama / önceliklendirme (Lider) | Sürükleme zorunlu değil; tek dokunuşlu sıralama (2.5.7); sıra tercihi oturumda kalıcı | Manuel + LocalStorage kontrolü |
| 8 | SPA navigasyon (sayfalar arası) | Müzik kesintisiz; hissedilir gecikme yok; çakışma yok (CLS ≤ 0.1) | Trace + console log |
| 9 | Favori (kalp) ekleme | Görsel + sesli geri bildirim; durum localStorage'da kalıcı | DOM assertion + LocalStorage kontrolü |
| 10 | Rol/izin akışı (çocuk ≠ yetişkin) | Yetki aşımında dostça engel mesajı; yetişkin ayarı ebeveyn kilidine gider | Form assertion + 3.3.8 denetimi |
| 11 | Bağlantı yavaşlatma | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace (P8.3 `VERIFIED`) |
| 12 | Hata sayfaları (404 / kopma) | Türkçe, dostça metin; büyük "Tekrar Dene" hedefi | Manuel + screenshot |
| 13 | Erişilebilirlik denetimi | Lighthouse A11y + axe; 1.4.3 / 1.4.11 / 2.5.7 / 2.5.8 ihlali yok | Lighthouse / axe |
| 14 | Kırılma noktası kontrolü | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) görünümlerinde düzen bozulmaz | Playwright `setViewportSize` + screenshot (`⚠️ DERIVED` viewport) |
| 15 | Çevrimdışı / yavaş ağ davranışı | Yavaş ağda yaşa uygun yükleme animasyonu; teknik hata metni görünmez | CDP `Network.emulateNetworkConditions` + manuel |
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
| 1 | Ad, doğum tarihi, semt, okul, ulaşım, kardeşler, aile detayları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (6) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | `veli_onayı_gerekli: true` (16 yaş altı politikası) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 4 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 6 | Big Five puanları (5×0-100) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 8 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 9 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 10 | Boyut kodu eşlemesi (şablon O/C/E/A ↔ P7.3 tam adları) | Vault içi çelişki | persona-template §3.5.3 + mood-taxonomy §3.3 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 11 | Mood küme adları (Lider / Enerjik) | Vault referanslı atama | [[mood-taxonomy]] §3 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); adlar mood-taxonomy §3 listesinden` |
| 12 | Mood küme tanımı alıntısı | Vault verisi | [[mood-taxonomy]] §3.3 (satır 12) + §3.2.2 | — | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 13 | Tür BPM aralıkları (Hip Hop 80-130 · Pop 80-120 · Electro 90-130 · Dance 110-135 · Techno 130-140) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 14 | Rap/Trap BPM (gerekirse: boom-bap 85-95 · trap 70-110 · modern rap ~140) | Gerçek-dünya | nevamuzik.com.tr | vocuno.com/tr/bpm-algilayici | `Kaynak: [k1] + [k2]` (P4.5 `VERIFIED`) |
| 15 | Şarkı bazlı BPM / kişisel "kadans = BPM" iddiası | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 16 | Favori sanatçı adları (Duman, maNga, Ceza, Şebnem Ferah, Haluk Levent + yabancılar) | Persona tercihi (kurgusal); sanatçı realitesi P4 kapsamında değil | research-bank P4.2 (yalnız Duman satırı VERIFIED) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 17 | Cihaz spec'i: A34 5G — 6.6" AMOLED, 1080×2340, 19.5:9, ~390 ppi, 120 Hz, 1000 nit, GG5, IP67, Android 13, BT 5.3 | Gerçek-dünya | GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) | Samsung resmi + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 18 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş | Türetme: 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 19 | Diğer cihazlar (iPhone SE, Galaxy Tab S6 Lite, spor saati, Smart TV) spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 20 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 21 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 22 | Persona renk kombinasyonu kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 23 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 24 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 25 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 26 | Playwright emülasyon alanları / breakpoint listesi | Gerçek-dünya | playwright.dev/docs/emulation | github.com/microsoft/playwright (docs/src/emulation.md) | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 27 | Eski dosyadaki istatistik iddiaları (TÜİK/WHO/Spotify/Garmin/Gençlik ve Spor Bakanlığı/IAAF yüzdeleri) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 28 | Eski cihaz/okul/yaş/mood değerleri (§7.2 çakışma tablosu) | Vault içi çelişki | eski salt-okunur persona dosyası | [[personas/index]] §6.3 | Karar: index kazanır; çelişki §7.2'de saklı |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`ada-celik.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 16 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Galaxy A34 viewport = 360×780` (türetilmiş ama türetilmişsiz) | `viewport ≈ 360×780 (DPR 3) — ⚠️ DERIVED` |
| `Antrenman şarkısı 145 BPM` | Tür aralığı (Techno 130-140, VERIFIED ikincil) + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.3 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
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
| Persona-özel eşik uydurma | "Ada için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

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
| Kaynak & Doğrulama satırı | 28 |
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
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.3 satır 12, §3.2.2) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (bu turda başka agent sorumluluğunda; diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 7 | 6 | [[personas/index]] §6.3 kazanır |
| Mood | Enerjik (birincil) / Lider (ikincil) | Lider (birincil) / Enerjik (ikincil) | [[personas/index]] + [[mood-taxonomy]] adları |
| Doğum tarihi | 19 Mayıs 2019 | 19 Mayıs 2020 | Yaş 6 ile tutarlılık için kurgusal türetme |
| Okul / sınıf | Özel Gaziantep Koleji, 2. sınıf | Özel Gaziantep Koleji, 1. sınıf | Yaş 6'ya uygun kurgusal güncelleme |
| Birincil cihaz | iPhone SE + Galaxy Tab S6 Lite (EXCLUDED) | Samsung Galaxy A34 5G (P3 VERIFIED) | [[research-bank]] P3 |
| Etki alanındaki istatistikler (TÜİK, WHO, Spotify, Garmin, GSB, IAAF yüzdeleri) | Kaynaksız yüzdeler | yazılmadı | §4.5 kural 4 (bankada yok → yazma) |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı, yaş/mood index §6.3'e göre düzeltildi; veli_onayı + KVKK notu eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/kiz-cocuk/ada-celik
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
