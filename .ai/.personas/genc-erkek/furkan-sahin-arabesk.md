---
title: "CoreMusic — Persona: Furkan Şahin"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/genc-erkek/furkan-sahin-arabesk"
updated: 2026-09-26
group: genc-erkek
age: 17
mood: Arabesk Melankolik
persona_id: CM-FS-17-ARB-GZT
veli_onayı_gerekli: false
---

# CoreMusic — Persona: Furkan Şahin

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **genc-erkek (12-17 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 17 yaşındaki gece dinleyen bir genç için arayüzü arabesk/melankolik gözden deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Arabesk Melankolik), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | genc-erkek (12-17) |
| Birincil mood | Arabesk Melankolik ([[personas/index]] §6.3 ataması) |
| Test odağı | Karanlık tema + gece modu, slow geçişler, gece uzun dinleme, Türkçe karakter arama, minimal player, kesintisiz çalma |
| Veli onayı | `veli_onayı_gerekli: false` (17 yaş — 16 CoreMusic eşiğinin üstünde; dayanak §3.5.1) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Ana sayfa / hızlı başlatma | Level 2 + 3 | Karanlık tema varsayılan; arabesk kategorisi üst sıralarda ([[mood-taxonomy]] §3.4 satır 24 test etkisi) |
| Gece modu & slow geçişler | Level 1 + 2 | "Karanlık tema, slow geçişler, gece modu" beklentisi; geçişte CLS ≤ 0.1 |
| Arama (Türkçe karakter + eski kayıt) | Level 2 | mojibake yok; sonuç < 2 sn; ASCII yazım toleransı |
| Uzun gece dinleme (kesintisiz) | Level 2 + 3 | Arka plan çalma sürer; INP ≤ 200 ms; hata anında kurtarma |
| BPM/tür filtresi | Level 2 | Yalnız tür aralığı (P4.4); arabesk şarkı BPM'i → `⚠️ VERIFICATION REQUIRED` |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P3-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| ≥10 test adımı (Adım / Beklenen / Doğrulama yöntemi) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: eski salt-okunur dosyadaki 100+ satırlık fiziksel/aile detayı bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5); kaynaksız yüzdeler/istatistikler (yüzde, saat, takipçi) taşınmaz.

**Okuma sırası (bu dosyayı kullanan agent için):**

| Sıra | Dosya | Ne için |
|------|-------|---------|
| 1 | [[personas/persona-template]] | Zorunlu iskelet (§3.3 blok, §3.5 alan havuzu) |
| 2 | [[research-bank]] P3-P8 | Tek gerçek-dünya veri kaynağı + etiket sözlüğü (§4.1) |
| 3 | [[mood-taxonomy]] | Küme adı + tanım alıntısı (yalnız §3 listesi) |
| 4 | [[personas/index]] §6.3 | Yaş/mood/filename/persona_id ataması (SSOT) |
| 5 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | Furkan Şahin | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 17 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3) |
| **Doğum Tarihi** | 3 Aralık 2008 | Kaynak: kurgusal (persona verisi; 2026-09-26 itibarıyla yaş 17 ile tutarlılık için türetildi) |
| **Cinsiyet** | Erkek | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Gaziantep / Şahinbey / Karataş | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | Lise 11. sınıf; meslek lisesi elektrik-elektronik bölümü | Kaynak: kurgusal (persona verisi) |
| **Ulaşım** | Yürüyüş (okul yakın); bazen aile motoru | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | `furkan.arabesk.gece` | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-FS-17-ARB-GZT` | Kaynak: kurgusal (atama: [[personas/index]] §6.3) |

**`CoreMusic Kullanıcı ID` parça dökümü** (index §6.3 atanan format — persona-template §3.5.1'in `CM-XX-YY-ZZZ-XXX` şablonuyla uyumludur; 2026-09-26 düzeltme):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `FS` | `FS` | Ad soyad baş harfleri (Furkan Şahin) |
| `17` | `17` | Yaş (index §6.3) |
| `ARB` | `ARB` | Mood kodu (Arabesk Melankolik) |
| `GZT` | `GZT` | Şehir kodu (Gaziantep) |

> **KVKK / yaş sınırı notu (zorunlu — 16+):** Bu persona 17 yaşındadır → frontmatter'de `veli_onayı_gerekli: false`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; CoreMusic politikası **16 yaş altı** için veli onayı ister (`⚠️ DERIVED`, [[research-bank]] P6.4 son satır) ve 17 bu eşiğin üzerindedir. Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) → `Kaynak: [k1] + [k2]` (research-bank P6.4 VERIFIED). Persona tamamen kurgusaldır; gerçek genç verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`).

| Düzenleme / Politika | Yaş sınırı | Anlamı | Etiket |
|----------------------|-----------|--------|--------|
| TMK m.11 (Türkiye) | 18 | Erginlik = hukuki ehliyet (evlenme, dava, imza) — veli onayının genel sınırı | `Kaynak: [k1] + [k2]` (P6.4 VERIFIED) |
| GDPR m.8 (AB) | 16 | Bilgi topluma sunma hizmetlerinde rıza verme yaşı | `Kaynak: [k1] + [k2]` (P6.4 VERIFIED) |
| COPPA (ABD) | 13 | 13 altı için ebeveyn izni zorunlu | `Kaynak: [k1] + [k2]` (P6.4 VERIFIED) |
| CoreMusic politikası | 16 | <16 → `veli_onayı_gerekli: true`; ≥16 → `false` | `⚠️ DERIVED` (P6.4 son satır) |

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski salt-okunur dosyadaki 100+ satırlık ayrıntı (ayakkabı numarası, cilt bakımı, tıbbi ölçüm…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 178 cm (orta yapı — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 70 kg, orta yapı, hafif kilo fazlası | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Siyah kısa saç, koyu kahverengi göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok; kulaklıkla dinler (model §3.5.6) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Sade ve koyu giyim (siyah/gri); profil fotoğrafında koyu zemin (kontrast: açık metin × koyu zemin — renk eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Gece / düşük ışıkta tek elle telefon; göz yormayan koyu arayüz ve büyük play kontrolü bekler | Kaynak: kurgusal (persona verisi); kontrast eşikleri: `Kaynak: [k1] + [k2]` (P5.4 VERIFIED) |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 32 | Kalabalığı sevmez, okulda arka sırada oturur; yalnız yakın arkadaşla sessiz muhabbet eder, geri kalan zaman tek başına dinler. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 55 | Dışarıdan sessiz ve uyumlu görünür; haksızlığa uğradığında ise içine kapanır, günlerce konuşmaz. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 48 | Meslek lisesinde işi idare eder; zorunlu işleri tamamlar ama motive değildir, ödevi son güne bırakır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 72 | **Yüksek puan = düşük duygusal denge (ters kodlama).** Melankolik ve karamsar; gece duygusal dalgalanma zirvede, arabesk tam da bu yüzden iyi gelir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 28 | Değişimi ve yeniyi sevmez ("eski köye yeni adet olmaz"); keşifte tanıdık eski kayıtlara döner, yeni tür denemez. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

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
| **Birincil küme** | Arabesk Melankolik |
| **İkincil küme** | Yok (bu turda ikincil atama yapılmadı — [[personas/index]] §6.3 tekil küme atar) |
| **Küme tanımı (alıntı)** | "Gece arabesk, duygusal derinlik" ([[mood-taxonomy]] §3.4 satır 24) |
| **Big Five eğilimi (alıntı)** | "Yüksek N, düşük O" ([[mood-taxonomy]] §3.4 satır 24 "Big Five eğilimi (kurgusal)") — Big Five puanlarıyla (N 72 · O 28) **tutarlı**; bağ: `⚠️ DERIVED` |
| **Tetikleyici durumlar** | Gece 21:00 sonrası odaya çekilme (gece modu beklentisi); yavaş/kaba geçiş ve ani parlak tema (gözü alır); değişiklik/"yeni tasarım" görürse ("eskisi daha iyiydi" tepkisi); hata metni teknik yığın görünürse güveni düşer |
| **UI etkisi** | "Karanlık tema, slow geçişler, gece modu" ([[mood-taxonomy]] §3.4 satır 24 "Test etkisi") |
| **Mood müziği (not)** | §3.4 satır 24'ün "Arabesk, slow · **60-90 BPM** ⚠️" hücresi taksonomi kendisi ⚠️ ile işaretler → birincil ölçüm değildir, testte kullanılmaz (`⚠️ VERIFICATION REQUIRED`, §3.5.11 satır 16) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Arabesk Melankolik); ikincil bu turda yok — eski salt-okunur dosyada "ikincil Moody" yazar, §7.2'de |
| Test etkisi | "Karanlık tema, slow geçişler, gece modu" (§3.4 satır 24) — geçerli kontrast eşikleri P5.4 (≥4.5:1 / ≥3:1); gece modu IDDAIASI değil beklentidir, `⚠️ DERIVED` test senaryosu |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Gece arabesk / duygusal derinlik | Varsayılan koyu tema; arabesk kategorisi ilk ekranda | DOM assertion + screenshot |
| 2 | Gece modu beklentisi | Beyaz flaş/anı parlama yok; tema tüm sayfalarda tutarlı (kontrast ≥ **4.5:1** · ≥ **3:1**) | A11y audit (P5.4 VERIFIED) |
| 3 | Slow geçişler | Sayfa/parça geçişinde sıçrama yok (CLS ≤ **0.1**), ani animasyon yok | Trace + console log |
| 4 | Değişime kapalı (düşük O) | A/B değişiminde eski akış korunur/geri alınabilir; "eski" geri bildirimi kaydedilir | Manuel + LocalStorage kontrolü |
| 5 | Yüksek N (hata/gecikme anı) | Teknik yığın izi görünmez; sakin, kısa hata metni + Tekrar Dene | Manuel + screenshot |
| 6 | Gece uzun dinleme | Kesintisiz çalma; INP ≤ **200 ms**; odak görünür (2.4.7), geri tuşu kaybolmaz (2.4.11) | Performance trace + a11y audit |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Arabesk (slow/hüzünlü) 2) Türk Halk Müziği (ağıt/uzun hava) 3) Fantezi 4) Türk Sanat Müziği (seçme) 5) Özgün Müzik | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Müslüm Gürses, İbrahim Tatlıses, Orhan Gencebay, Ferdi Tayfur, Ahmet Kaya | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); P4.2 kapsamındaki gerçek-dünya bilgiler aşağıdaki tabloda VERIFIED, kapsam dışılar `⚠️ VERIFICATION REQUIRED` |
| **BPM aralığı (tür)** | arabesk için bankada tür-BPM aralığı YOK → **yok**; komşu türler: Pop **80-120** · House **110-128** · Reggae **65-80** (P4.4) | `⚠️ VERIFICATION REQUIRED` (arabesk — P4.7 / EXCLUDED X-2) · komşu türler: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (P4.4) → `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)` |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | Ağır/slow his; "arabeskte önemli olan duygu, tempo değil" (persona tutumu) | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi: okul yolu 07:45, teneffüs, öğle arası, dönüş yolu, 21:00-02:00 gece seansı · Hafta sonu: geç uyanış, gece yarısı uzun dinleme | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (eski/nadir arabesk kayıtlar + söz + kişisel listeler) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Keşiften çok "bildiğini bulma": eski albüm/kayıt arar; yeni tür denemez; listeler mood bazlı ve minimalist isimli | Kaynak: kurgusal (persona verisi; düşük O ile tutarlı) |
| **Premium olasılığı** | Düşük — ücretsiz kullanıcı, reklamı tolere eder, "para olsa alırım" | Kaynak: kurgusal (persona verisi; yüzdeler yazılmadı) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Arabesk (slow) | Gecenin yoldaşı; duyguyu anlatır, karamsarlıkla örtüşür |
| 2 | Türk Halk Müziği | Memleketin sesi; ağıt/uzun hava köklere bağlar |
| 3 | Fantezi | Daha melodik; gündüz arka plan için hafif seçen |
| 4 | Türk Sanat Müziği (seçme) | Eski plaklardan gelen "kalite" algısı |
| 5 | Özgün Müzik | Duygusal/içerik tarafı; sadece melodi ve söz |

**Doğrulanmış sanatçı realitesi (research-bank P4.2 — VERIFIED):**

| Sanatçı | Bilgi | Kaynak |
|---------|-------|--------|
| **Müslüm Gürses** (1953 Halfeti – 2013 İstanbul) | "Müslüm Baba", "Arabeskin Babası", dünyada "Father of Arabesque"; 2002 Teoman'ın "Paramparça"sını seslendirdi | tr.wikipedia + ilimvemedeniyet.com → `Kaynak: [k1] + [k2]` |
| **Orhan Gencebay** | Lakabı "Kral" | tr.wikipedia + ilimvemedeniyet.com → `Kaynak: [k1] + [k2]` |
| **Ferdi Tayfur** | Lakabı "Abi" | tr.wikipedia + ilimvemedeniyet.com → `Kaynak: [k1] + [k2]` |
| **İbrahim Tatlıses** | Arabesk icracısı olarak listelenir | tr.wikipedia + ilimvemedeniyet.com → `Kaynak: [k1] + [k2]` |
| **Bergen** (1959-1989) | "Acıların Kadını" (1986); 80'lerin önde gelen arabesk kadını | ntv.com.tr + tr.wikipedia → `Kaynak: [k1] + [k2]` |
| **Ahmet Kaya** | P4.2'de YOK | `⚠️ VERIFICATION REQUIRED` (yalnızca persona tercihi olarak kullanıldı) |

*Arabesk tanımı (P4.1 VERIFIED):* "Türkiye'ye özgü duygusal halk müziği türü; teması karamsarlık, umutsuz aşk, günlük dertler; etimoloji Fransızca 'Arap tarzı'; Arap ezgi/usullerinden esinlenen **Türk müziği** türü (Arap müziği değildir)" → `Kaynak: tr.wikipedia.org/wiki/Arabesk_müzik + ilimvemedeniyet.com`.

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 07:45 | Okula yürüyüş | Arabesk (kısa, yolu kısa) |
| Hafta içi | 15:30 | Okul dönüşü | Arabesk (derin dinleme) |
| Hafta içi | 21:00-02:00 | Gece seansı | En ağır arabesk + halk müziği |
| Hafta sonu | 23:00-02:00 | Eski konser/kayıt arayışı | Arabesk katalog taraması |

*Not: BPM satırları yalnız tür aralığı düzeyindedir (P4.4); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). [[mood-taxonomy]] §3.4 satır 24'ün "60-90 BPM" notu da birincil ölçüm değildir → `⚠️ VERIFICATION REQUIRED`; test hedefi olarak banka tür aralıkları kullanılır.*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Xiaomi Redmi Note 12 (eski salt-okunur dosyadan) | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları YAZILMADI; bankada VERIFIED cihaz yalnız Samsung Galaxy A34 5G |
| **Ekran çözünürlüğü → viewport** | Cihaza özel viewport YOK (spec doğrulanmadı); test için banka viewport'u: `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: A34 çözünürlüğü ÷ DPR (research-bank P3.2); persona cihazına **uygulanmaz**, platform test girdisidir |
| **OS** | Android sürümü doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / X-1) — sürüm yazılmadı |
| **Tarayıcı** | Chrome (mobil — kurgusal; P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | Ev/mobil karma; hız kurgusal (evde sabit, dışarıda sınırlı mobil paket) | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Kulaklık** | Kablosuz kulaklık + yedek kablolu (eski dosyada modeller) — modeller spec'siz | `⚠️ VERIFICATION REQUIRED` (X-1) — model/spec yazılmadı |
| **Tema** | dark / gece (mood: karanlık tema, gece modu) | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.4 satır 24 |
| **Ekran süresi / kullanım** | Yoğun gece kullanımı (kurgusal — sayısal saat yazılmadı) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 4/10 — telefonu iyi kullanır, bilgisayarda temel işler; "telefon iş görsün yeter" | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | Eski masaüstü PC (ders için) — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır (platform test varsayımı, `⚠️ DERIVED`); persona'nın kendi cihaz viewport'u **yoktur** (spec doğrulanmadı — X-1). `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmedi).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | yok (kurgu); gece düşük ışıkta uzun bakış | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) — dark temada da geçerli | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | normal; kulaklıkla düşük-orta ses | Sesli geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | kısıt yok; gece tek elle kullanım (yatakta) | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · tek dokunuş yerine sürükleme zorunlu değil (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | gece yorgun; uzun metin okumaz, az adım bekler | Odak görünürlüğü 2.4.7 + 2.4.11; erişilebilir kimlik doğrulama min. (3.3.8) — kısa akış bekler | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | Persona verisi (okul/aile/ruh hâli bağlamı) gerçek kişiye ait değil, tamamı kurgu (6698 m.5/1 — `Kaynak: [k1] + [k2]`, P6.5) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| ≥48 px dokunma hedefi | Eski dosyada "butonlar en az 48×48px" yazar; research-bank P5'te 48 px EŞİĞİ YOK → `⚠️ VERIFICATION REQUIRED`; asgari güvenli eşik 2.5.8 (≥24×24 CSS px, VERIFIED) |

### Kişilik & Davranış

- İçe dönük ve sessizdir: okulda arka sırada, az konuşur; konu arabesk olunca gözleri parlar ve açılır.
- Melankolik/gece insandır: gece en derin halidir; müzik onun için terapi ve sırdaştır.
- Değişime dirençlidir: "Eskisi daha iyiydi" en sık cümlesidir; yeni tasarım/A-B değişimi görürse tepki gösterir.
- Sadık ve az ama derin bağ kurar: tek-iki yakın arkadaş, aileye (özellikle anneye) bağlılık güçlü.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | 17 yaş → kendi kararı (CoreMusic 16 eşiği üstü); ücretsiz kullanıcı, premium talebi düşük | Kaynak: kurgusal (persona verisi); politika: `⚠️ DERIVED` (P6.4) |
| **Sabır eşiği** | ~5 sn; yavaş yüklemede uygulamayı kapatır, tanıdık kaynağına döner | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Sessizce çıkar/tekrar dener; teknik hata metni görmezden gelinir — hızlı kurtarma şart | Kaynak: kurgusal (persona verisi); yüksek N ile tutarlı |
| **Reklam toleransı** | Orta — ücretsiz kullanıcısında alışmış; yine de gece akışını bölmesinden hoşlanmaz | Kaynak: kurgusal (persona verisi; yüzdeler yazılmadı) |
| **Öğrenme biçimi** | Görsel ve az metin; kılavuz okumaz, deneyerek bulur | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Neredeyse hiç paylaşmaz; ayda bir-iki kez tek yakın arkadaşa gönderir | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | Gece yarısı sonrası tek dokunuşlu "devam et" akışı bekler; karmaşık menü reddedilir | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Tanıdık arabesk listesi + kesintisiz çalma + karanlık tema → uygulamaya bağlılık artar | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.4 satır 24 |

*Erişilebilirlik etkisi (özet):* gece tek elle, yorgun gözle kullanım → dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3) dark temada, geri tuşu/skip kaybolmaz (2.4.11) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED); ≥48 px hedefi `⚠️ VERIFICATION REQUIRED`.

### AI Rol Kartı

Sen Furkan Şahin'sin. 17 yaşında, Gaziantep/Şahinbey-Karataş'ta yaşıyorsun, meslek lisesinde elektrik-elektronik bölümünde 11. sınıfta okuyorsun.
Arabesk Melankolik bir kişiliğin var: içe dönüksün, sessizsin, gece daha derinsin; "gece arabesk, duygusal derinlik" senin tanımındır.
Müzik zevkin: arabesk (slow/hüzünlü), Türk Halk Müziği, fantezi, az da olsa sanat müziği ve özgün müzik. En sevdiklerin: Müslüm Gürses, İbrahim Tatlıses, Orhan Gencebay, Ferdi Tayfur, Ahmet Kaya.
Telefonun (model spec'i doğrulanmadı) ve eski bir masaüstü bilgisayarın var; kablosuz kulaklıkla, çoğunlukla karanlık temada dinlersin.
Görme / işitme / hareket kısıtların yok; ama gece düşük ışıkta tek elle kullanırsın — büyük oynat kontrolü, az adım ve göz yormayan koyu arayüz beklersin.
Sabır eşiğin ~5 sn; hata metninde teknik yığın görürsen güvenin düşer; "Eskisi daha iyiydi" en sık cümledir.
Test edilecek akışlar: ana sayfa (karanlık tema, arabesk üstte), gece modu & slow geçişler, arama (Türkçe karakter + eski kayıt), playlist oluşturma (mood bazlı, minimalist), player (basit, büyük kontrol), kesintisiz gece dinleme, BPM/tür filtresi, hata kurtarma, çoklu cihaz session, erişilebilirlik denetimi.
Davranış notları:
- Gece 21:00 sonrası odana çekilirsin; en ağır arabesk o saatte başlar.
- Tanıdık listeye dönersin; yeni tür keşfetmezsin, yeni tasarım görürsen direnirsin.
- Pop/enerjik öneri görürsen "beni tanımıyor musun" dersin — öneriler arabesk ağırlıklı olmalı.
- Şarkı sözünü okumayı/ezberlemeyi seversin; Türkçe karakter bozukluğu güveni düşürür.
- Reklam/kesinti gece akışını bölerse uygulamayı kapatırsın; premium talebin düşük, "para olsa alırım" dersin.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, kısa ve içe dönük ton — uzun açıklama ve heyecanlı hitap kabul etmez |
| Gerçek kişi verisi | Prompt'ta gerçek kişi/aile/okul verisi yok — hepsi kurgu (P6.5); sanatçı bilgileri P4.2 VERIFIED kadro ile sınırlı |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |

### Test Adımları

| # | Adım | Beklenen | Doğrulama yöntemi |
|---|------|----------|-------------------|
| 1 | Ana sayfa yükleme (gece) | LCP ≤ **2500 ms**, koyu tema üstte, CLS ≤ **0.1** | Performance trace + screenshot (eşikler: research-bank P8.1 `VERIFIED`) |
| 2 | Karanlık tema varsayılan | Tüm sayfalarda tutarlı koyu tema; beyaz flaş yok; kontrast ≥ **4.5:1** / ≥ **3:1** | A11y audit (axe / Lighthouse; P5.4) |
| 3 | Gece modu / slow geçiş | Ani animasyon yok; geçişte sıçrama yok (CLS ≤ 0.1) | Trace + console log |
| 4 | Arama (Türkçe karakter: ç, ğ, ı, İ, ö, ş, ü) | Türkçe karakter kabulü; ASCII yazım ("Muslum Gurses") da sonuç verir; sonuç < 2 sn; mojibake yok | DOM assertion + timing + `vault-utf8-writer verify` |
| 5 | Eski albüm/kayıt listesi | Kronolojik/eksiksiz liste; kapaklar görünür; "benzer sanatçı" yanlış tür önermez | DOM assertion + screenshot |
| 6 | Playlist oluşturma (mood bazlı) | Oluşturma < 2 sn; isim/kapak minimalist-koyu; gizlilik varsayılanı kişisel | DOM assertion + LocalStorage kontrolü |
| 7 | Player (play / duraklat / skip) | İlk ses < 1 sn; büyük oynat kontrolü (≥ **24×24 CSS px**, 2.5.8); odak görünür (2.4.7) | Trace + console log + a11y audit |
| 8 | Uzun gece dinleme (kesintisiz) | INP ≤ **200 ms**; parça geçişinde kesinti/kayıp yok; ekran kapalıyken çalma sürer | Performance trace + manuel |
| 9 | BPM/tür filtresi | Filtre çalışır; sonuçlar tür aralığı (P4.4) içinde; **arabesk/şarkı BPM iddiası yok** | DOM assertion + filtre testi |
| 10 | SPA navigasyon (sayfalar arası) | Müzik kesintisiz; hissedilir gecikme yok; çakışma yok (CLS ≤ 0.1) | Trace + console log |
| 11 | Bağlantı yavaşlatma | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok, akış korunur | Network throttling + trace (P8.3 `VERIFIED`) |
| 12 | Çevrimdışı / veri tasarrufu | İndirilen/önbellek liste erişilebilir; durum göstergesi doğru | CDP `Network.emulateNetworkConditions` + manuel |
| 13 | Hata sayfaları (404 / kopma) | Hızlı kurtarma (Tekrar Dene); teknik yığın izi görünmez | Manuel + screenshot |
| 14 | Erişilebilirlik denetimi | Lighthouse A11y + axe; 1.4.3 / 1.4.11 / 2.4.7 / 2.4.11 / 2.5.7 / 2.5.8 / 3.3.8 ihlali yok (48 px iddiası test edilmez — VR) | Lighthouse / axe |
| 15 | Kırılma noktası kontrolü | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) görünümlerinde düzen bozulmaz | Playwright `setViewportSize` + screenshot (`⚠️ DERIVED` viewport) |
| 16 | Odak sırası (klavye/gezinme) | Odak sırası mantıklı; odak görünür (2.4.7); gizli odak yok | Klavye navigasyon testi + DOM assertion |

*Asgari 10 satır (şablon §3.5.10) — 16 satır yazıldı. Her satırda 3 sütun zorunlu: `Adım` · `Beklenen` · `Doğrulama yöntemi`. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir).*

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, semt, okul, bölüm, ulaşım, rumuz, aile detayları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (17) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | Doğum tarihi (3 Aralık 2008) | Türetilmiş kurgu (yaşla tutarlılık) | Yaş 17 + güncel tarih 2026-09-26 | — | `Kaynak: kurgusal (türetildi)` |
| 4 | `veli_onayı_gerekli: false` (16+ politikası) | Politika sonucu (türetilmiş) | research-bank P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 5 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 6 | Persona verisi kurgu; gerçek genç verisi yok | Gerçek-dünya (hukuki) | research-bank P6.5 | 6698 m.5/1 | `Kaynak: [k1] + [k2]` |
| 7 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 8 | Big Five puanları (5×0-100) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 9 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 10 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 11 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 12 | Boyut kodu eşlemesi (şablon O/C/E/A ↔ P7.3 tam adları) | Vault içi çelişki | persona-template §3.5.3 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 13 | Mood küme adı (Arabesk Melankolik) | Vault referanslı atama | [[mood-taxonomy]] §3.4 satır 24 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); ad mood-taxonomy §3 listesinden` |
| 14 | Mood küme tanımı + test etkisi alıntısı ("Gece arabesk, duygusal derinlik" · "Karanlık tema, slow geçişler, gece modu") | Vault verisi | [[mood-taxonomy]] §3.4 satır 24 | — | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 15 | Mood eğilimi ("Yüksek N, düşük O") | Vault verisi (kurgusal eğilim) | [[mood-taxonomy]] §3.4 satır 24 | — | `Kaynak: [[mood-taxonomy]]` + bağ: `⚠️ DERIVED` |
| 16 | Mood BPM notu (60-90 ⚠️) | Doğrulanmadı (taksonomi notu) | mood-taxonomy §3.4 (⚠️ işaretli) | research-bank P4 (arabesk aralığı YOK) | `⚠️ VERIFICATION REQUIRED` — testte kullanılmadı |
| 17 | Arabesk tanımı (P4.1) | Gerçek-dünya | tr.wikipedia.org/wiki/Arabesk_müzik | ilimvemedeniyet.com | `Kaynak: [k1] + [k2]` (P4.6 `VERIFIED`) |
| 18 | Müslüm Gürses (1953-2013, "Müslüm Baba" …) | Gerçek-dünya | tr.wikipedia.org/wiki/Müslüm_Gürses | ilimvemedeniyet.com + tr.wikipedia Arabesk müzik | `Kaynak: [k1] + [k2]` (P4.6 `VERIFIED`) |
| 19 | Orhan Gencebay "Kral" · Ferdi Tayfur "Abi" · İbrahim Tatlıses · Bergen (1959-1989, "Acıların Kadını" 1986) | Gerçek-dünya | tr.wikipedia Arabesk müzik + ntv.com.tr | ilimvemedeniyet.com | `Kaynak: [k1] + [k2]` (P4.6 `VERIFIED`) |
| 20 | Ahmet Kaya (favori sanatçı) realitesi | Doğrulanmadı (P4.2 dışı) | research-bank P4.2 (kapsam dışı) | — | `⚠️ VERIFICATION REQUIRED` — yalnız persona tercihi |
| 21 | Tür BPM aralıkları (Pop 80-120 · House 110-128 · Reggae 65-80) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 22 | Arabesk tür-BPM aralığı + şarkı bazlı BPM | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 23 | Cihaz: Xiaomi Redmi Note 12 (spec'siz) | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 24 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş (platform test girdisi) | Türetme: A34 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 25 | Diğer cihazlar (eski PC, kulaklık) spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 26 | Tarayıcı sürümü, internet hızı, OS sürümü | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 27 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 28 | Persona renk kombinasyonu kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 29 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 30 | Dokunma hedefi ≥48 px (eski dosya iddiası) | Doğrulanmadı | eski salt-okunur dosya | research-bank P5 (48 px EŞİK YOK) | `⚠️ VERIFICATION REQUIRED` — asgari 2.5.8 (≥24×24 px) kullanıldı |
| 31 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 32 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 33 | Eski dosyadaki istatistik/yüzde iddiaları (dinleme saatleri, keşif yüzdeleri, premium yüzdesi, takipçi) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`furkan-sahin-arabesk.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 16 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Redmi Note 12 viewport = 2400×1080` (spec doğrulanmadı) | `spec yok — ⚠️ VERIFICATION REQUIRED (X-1); test viewport = banka türevi ⚠️ DERIVED` |
| `Bu arabesk şarkı 76 BPM` | Tür aralığı (P4.4, VERIFIED ikincil) + arabesk/şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |
| `Gece dinleme %70'i gece yapar` / `premium %10` | Yüzdeler bankada yok → nitel ("düşük/orta") + `Kaynak: kurgusal (persona verisi)` |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.4 satır 24 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood/persona_id |
| 3 | Eski salt-okunur persona kaynağını incele (yalnız kimlik/müzik/rutin; etiketsiz iddia/istatistik taşınmaz) | Kurgusal içerik |
| 4 | §3.5 11/11 alanı doldur → her satıra §4.5 etiketi yaz | Dolu §3 |
| 5 | Test Adımları ≥10 → §6.1 kontrol listesi → `vault-utf8-writer verify` → `log.md` append | Doğrulanmış dosya |

**Adım ayrıntıları:**

| Adım | Ayrıntı | Sıklık |
|------|---------|--------|
| 1 | `.ai/.templates/personas/persona-template.md` §3.3 + §3.5; `.ai/.templates/docs-md-template.md` | Her üretimde |
| 2 | research-bank P3 (cihaz), P4 (müzik/BPM), P5 (WCAG), P6 (KVKK/veli), P7 (Big Five), P8 (test eşikleri) | Her üretimde |
| 3 | Eski dosya: salt-okunur kaynak; yalnız kurgusal kimlik/müzik/rutin taşınır; yüzde/saat/takipçi istatistikleri taşınmaz | Yalnız ilk üretimde |
| 4 | 11 alt bölüm tek tek doldurulur; boş bırakılan alan `yok` olarak işaretlenir | Her üretimde |
| 5 | `node .ai/scripts/vault-utf8-writer.mjs verify --file <dosya>` → `lines ≥ 500`, `mojibake = 0` | Yazım sonrası zorunlu |

```text
ŞABLONU OKU → BANKA/TAKSONOMİ/INDEX OKU → ESKİ KAYNAKTAN KURGU TAŞI (İSTATİSTİK YOK) → 11/11 ALAN → ETİKETLE → TEST ADIMLARI ≥10 → §6.1 → VERIFY → LOG
```

### §5.2 Hata Modları

| Hata | Belirti | Düzeltme |
|------|---------|----------|
| Etiketsiz satır | §6.1 "kaynak" maddesi düşer | §4.5 formatıyla etiketle |
| Şarkı/arabesk BPM'i / uydurma 2. kaynak | ADR-005 ihlali | Sil + `⚠️ VERIFICATION REQUIRED` |
| Eksik alan havuzu | 11/11 düşer | §3.5'e dön; boş alan `yok` diye işaretle |
| Kaynaksız yüzde/istatistik (eski dosyadan) | X-10 / §4.5 ihlali | Sil; nitel ifadeye çevir + `Kaynak: kurgusal` |
| Mojibake | `verify.mojibake > 0` | `vault-utf8-writer repair` |
| 500 satır altı | `verify.lines < 500` | Açıklama satırı/tablo ekle, yeniden verify |
| Persona-özel eşik uydurma | "Furkan için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`) + ek alanlar (`group`, `age`, `mood`, `persona_id`, `veli_onayı_gerekli`)
- [ ] Tek H1 + Zorunlu Bağlantılar satırı (≥ 1 wiki-link)
- [ ] §1-§7 başlıkları eksiksiz; §3 altında 11 alt bölüm (Kimlik → Kaynak & Doğrulama) dolu
- [ ] §4'ün ilk maddesi = §3.3 Şablon-Önce bloğu birebir (5 madde + Durum/Aksiyon tablosu)
- [ ] Dosya derinliği ≥ 500 satır (`verify` → `lines`)
- [ ] Her alanda/satırda kaynak etiketi var (`kurgusal` · `[k1] + [k2]` · `⚠️ VERIFICATION REQUIRED` · `⚠️ DERIVED`)
- [ ] Big Five 5/5 boyut dolu, 0-100 aralığında, her birinde 1 satır gerekçe + ters kodlama notu + mood eğilimi (Yüksek N, düşük O) uyumu
- [ ] Test Adımları tablosu ≥ 10 satır ve 3 sütun (Adım / Beklenen / Doğrulama yöntemi)
- [ ] AI Rol Kartı `Sen ...'sin` paragrafıyla başlıyor, `{{...}}` kalmamış (yalnız §4.3 bloğundaki `{{VARIABLE}}` kural metni)
- [ ] Wiki-link'ler `[[...]]` formatında; hedefi olmayanlar §7.1'de `📋 planlanan`
- [ ] 16+: `veli_onayı_gerekli: false` + KVKK/GDPR/COPPA karşılaştırma tablosu ("KVKK 16" YANLIŞ)
- [ ] Cihaz: yalnız A34 spec'i VERIFIED; viewport `⚠️ DERIVED`; Redmi Note 12 `⚠️ VERIFICATION REQUIRED`
- [ ] WCAG kodları yalnız P5 listesinden; persona renkleri `⚠️ DERIVED`
- [ ] BPM yalnız tür aralığı (P4.4); arabesk/şarkı BPM'i yok
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
| Kaynak & Doğrulama satırı | 33 |
| Frontmatter alan | 7 zorunlu + 5 ek |
| Dil | Türkçe (mojibake yasak) |
| Derinlik | ≥ 500 satır (`verify` → `lines`) |
| Kayıt | `log.md` append-only |

---

## §7 Referanslar

### §7.1 Wiki-link Referansları

| Wiki-link | İlişki | Durum / Gerçek hedef |
|-----------|--------|----------------------|
| `[[personas/index]]` | Persona kataloğu — yaş/mood/persona_id atamasının sahibi | ✅ `.ai/.personas/index.md` |
| `[[personas/persona-template]]` | Persona şablonu — bu dosyanın iskeleti | ✅ `.ai/.templates/personas/persona-template.md` |
| `[[research-bank]]` | Araştırma paketleri P1-P8 — tek gerçek-dünya veri kaynağı | ✅ `.ai/.personas/research-bank.md` |
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.4 satır 24) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (bu turda başka agent sorumluluğunda; diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş / doğum tarihi | 17 · 3 Aralık 2009 (2026-09-26'da 16 yapar — yaşla tutarsız) | 17 · 3 Aralık 2008 (yaş 17 ile tutarlı) | Yaş SSOT: [[personas/index]] §6.3; tarih kurgu olarak yaşa göre türetildi |
| Persona ID | `CM-FS-17-ARB-GZT` / `GE-FS-17-ARB-GZT` | `CM-FS-17-ARB-GZT` | [[personas/index]] §6.3 kazanır; şablon §3.5.1 `CM-XX-YY-ZZZ-XXX` ile uyumlu (2026-09-26 düzeltme) |
| Birincil cihaz | Xiaomi Redmi Note 12 (2400×1080 spec'li) | Test viewport: banka A34 türevi (`⚠️ DERIVED`); spec yazılmadı | [[research-bank]] P3 — cihaz spec'i yazılmadı (X-1) |
| BPM aralığı | "70-90 BPM (arabesk slow)" + taksonomi "60-90 ⚠️" + şarkı bazlı okumalar | arabesk tür aralığı YOK → `⚠️ VERIFICATION REQUIRED`; testte P4.4 tür aralıkları | §4.5 + X-2 |
| Dokunma hedefi | "Butonlar en az 48×48px" | ≥24×24 CSS px (2.5.8, P5.4 VERIFIED); 48 px `⚠️ VERIFICATION REQUIRED` | research-bank P5 kazanır |
| Big Five ölçeği | 0-10 puanlar (3/10, 2/10 …) | 0-100 normalize (`⚠️ DERIVED`) | P7.4; eski 0-10 değerleri puan olarak taşınmadı |
| İkincil mood | "İkincil mod tipi: Moody" | ikincil bu turda yok | [[personas/index]] §6.3 tekil küme atar |
| Yüzdeler/istatistikler (dinleme saati, keşif %, premium %10, aile geliri, harçlık) | Kaynaksız sayılar | yazılmadı (nitel ifadeye çevrildi) | §4.5 kuralı 4 (bankada yok → yazma) |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kurgusal kimlik/müzik/rutin taşındı, istatistikler atlandı; persona_id/index ataması uygulandı; veli_onayı false + KVKK/GDPR/COPPA karşılaştırma tablosu eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/genc-erkek/furkan-sahin-arabesk
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
