---
title: "CoreMusic — ADR-090: Kanal Varyant Ürün Ailesi (mono · 2 · 2+1 · 4 · 5 · 6 · 7 · 8 · 7+1 · 8+1)"
type: "architecture-decision"
category: "electronics"
date: "2026-09-26"
updated: "2026-09-26"
version: "1.0.0"
status: "accepted"
authority: "SSOT — kanal varyant matrisi, maliyet kademesi ve SKU politikası; K16-K20 amfi katmanları ve PCM3168A çekirdeği bu karara bağlanır"
kaynak: "Kullanıcı onaylı karar kapsamı (ADR-038 §5.3 yönlendirmesinin gerçek hedefi) + disk kanıtı taraması + exa web araştırması"
governance: "Red Team · Human Mode · Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-090: Kanal Varyant Ürün Ailesi (Channel Variant Product Family)

> **Durum:** ✅ **ACCEPTED** (kullanıcı onaylı kapsam) · **Tarih:** 2026-09-26 · **Debate:** ✅ **TAMAMLANDI** (3 tur / 20 persona — 18 kabul / 2 çekimser / 0 red → **KABUL**, §7.1) · **Tech Lead:** ✅ · **Arch Lead:** ⏳
> **Karar serisi:** `.ai/.decisions/accepted/` · **Slug:** `ADR-090-channel-variant-product-family`
> **İlgili kararlar:** [[ADR-038-8-1-sound-card-chip-selection]] (DAC/USB çekirdeği — §5.3 bu karara yönlendirmişti) · [[ADR-089-classab-24v]] (amfi temeli) · [[../../brain.md]] · [[../index.md]]
> **Numara gerekçesi:** brain.md `:1021-1027` 083-089 slotlarını rezerve konularla doldurur (083 SPA Router … 089 Class AB); **090 ilk boş numaradır** ve kullanıcı tarafından onaylanmıştır. Frozen **001-037'ye dokunulmamıştır**.

---

## §1 Bağlam (Context)

### §1.1 Mevcut Durum (disk + kod kanıtı — dürüst etiket)

**A) Vault kanıtları (IMPLEMENTED = karar/doküman olarak):**

| Kaynak | Kanıt | Etiket |
|---|---|---|
| [[../../brain.md]] `:284` | Class AB Amp — 50W @ 8Ω, THD+N <0.01%, SNR >100dB, ±35V DC, MJL21194/MJL21193 **(ADR-089)** | IMPLEMENTED (karar) |
| [[../../brain.md]] `:907` | **K16-K18**: 50W/kanal, 8 kanal, MJL21194/MJL21193, ±35V boost, **800W** (ADR-089) | IMPLEMENTED (karar) |
| [[../../brain.md]] `:292` | §9 **8.1 Surround** — 8 kanal + 1 LFE subwoofer, Linkwitz-Riley 4. nesil, crossover 80Hz | IMPLEMENTED (karar) |
| [[../../brain.md]] `:1021-1027` | ADR-083…089 slotları rezerve (SPA Router, API Gateway, Shared Lib, Event Driven, Master Plan, OAuth, Class AB) → **090 ilk boş slot** | envanter |
| [[ADR-038-8-1-sound-card-chip-selection]] §5.3 / §7.1 | "Kanal varyant ürün ailesi (mono/2/2+1/4-8/7+1/8+1) ayrı karara yönlendirildi" — **ADR-083 yazıyordu**; ADR-083 slotu brain'de SPA Router'a ayrılmış (**numara çakışması**) → bu ADR o çakışmayı kapatır | IMPLEMENTED (karar) |
| [[ADR-089-classab-24v]] §2 kalem 4 | **1 / 2 / 4 / 6 / 8 kanal — modüler, her kanal bağımsız PCB** (PROJECTS L306) | IMPLEMENTED (karar) |
| [[../../architecture/k16-class-ab/CLAUDE.md]] | Class AB zorunlu, ±35V, 6-layer, THD+N <0.005%, 50W/kanal (25-75W aralık), BOM hedefi **<$430** | IMPLEMENTED (doküman) |
| [[../../architecture/k16-class-ab/8-channel-design.md]] | 8 × **100W** @ 8Ω, toplam 800W, dual-mono PSU, kanal başına ayrı PCB, liste **$882** | IMPLEMENTED (doküman) — **ADR-089 (50W) ile güç çelişkisi, §1.1-C** |
| [[../../architecture/k17-guc-kaynagi/CLAUDE.md]] | K17 güç kaynağı katmanı (LM5122 dual boost, ±35V) | IMPLEMENTED (doküman) |
| [[../../architecture/k18-termal/CLAUDE.md]] | K18 termal katmanı (Fischer SK53-100-SA, KSD301 72°C, 80mm PWM) | IMPLEMENTED (doküman) |
| [[../../CLAUDE.md]] L116-L120 / L126-L127 | K16-K20 katman tanımı + H1 (Class AB 8×50W 7.1) + H2 (±40V/12-24V boost) | IMPLEMENTED (doküman) |
| [[../../ecosystem/donanım-devre-referanslari.md]] `:315` | "rp2040 red kapsamı: yalnız K17 için mi, yoksa **düşük maliyetli SKU** (varsa) için de düşünülür mü — **ürün kararına bağlı**" | vault'ta SKU'ya tek atıf — bu ADR o kararı verir |

**B) KOD — kanal varyant kodu 0 (dürüst bulgu):**

Tarama: `channel | kanal | subwoofer | LFE | SKU | variant` → `shared/` (php/js/sql/cpp/h):

| Eşleşme | İçerik | Dürüst etiket |
|---|---|---|
| `shared/src/AI/AIEngine.php:111,:304,:315` | `'channels' => $metadata['channels'] ?? 2` — dosya metadata'sı | varyant değil; **ürün kanal kodu YOK** |
| `shared/src/OAuth/Provider/YouTubeOAuth.php:90-107` | YouTube API `channels` ucu | alakasız |
| `.ai/` geneli `variant\|SKU` | yalnız `product_variants` **şablon örneği** (`.ai/.templates/adr/adr-database-template.md`) + UI token/`variant` usage | tasarım örneği — ürün kodu değil |

→ **Sonuç: kanal varyant/SKU için repoda 0 implementasyon; bu karar tamamı PLANNED'dir (karar seviyesi IMPLEMENTED).**

**C) Spec çelişkileri (açıkça işaretlenir):**

| # | Çelişki | Durum |
|---|---|---|
| C1 | `8-channel-design.md` **100W/kanal** der; ADR-089 + brain `:284/:907` **50W/kanal** der | **SSOT = ADR-089 (50W, 25-75W aralık)**; 100W satırı düzeltme §5.1 adım 5 |
| C2 | ADR-089 kanal listesi **1/2/4/6/8**; bu matriste **5 ve 7** var | Kombinasyonla karşılanır (4+1, 6+1) — §2.2-b |
| C3 | **8.1 = 8 kanal + 1 LFE = 9 çıkış**, PCM3168A **8-out** (ADR-038) | 9. çıkış için ikinci DAC/TDM genişleme gerekir → **§4.3 R2, `⚠️ VERIFICATION REQUIRED`** |
| C4 | ADR-038 bu konuyu **ADR-083**'e yönlendirmişti; brain `:1021` = SPA Router | Çakışma bu ADR ile kapanır (§6) |

**D) K16-K18 dokümanları ne diyor (özet):** K16 = tek kanal bağımsız PCB, enable pinli, Darlington MJL21194/93, 50W @ 8Ω, THD+N <0.005%, BOM <$430; K17 = 12-24V DC → LM5122 dual boost → ±35V, OR-ing (6S LiPo / DC adaptör), UVP/OVP/OCP/OTP; K18 = Fischer SK53-100-SA + Noctua NF-A8 PWM + KSD301 72°C, 41W/kanal ısı yönetimi. **Hiçbiri kanal sayısını/sku'yu sabitlemez — modülerlik bu ADR'nin omurgasıdır.**

### §1.2 Sorun Tanımı (Problem)

1. CoreMusic donanım ailesi tek bir **8 kanal / 8.1** hedefiyle yazılı; pazarın mono'dan 8+1'e kadar uzanan bir ürün ailesi için **hangi kanal sayısının hangi DAC/amfi/maliyet kademesiyle** üretileceği kararı yok.
2. ADR-089 amfiyi **modüler** kurdu ama **varyant × maliyet × SKU** matrisini vermedi; ADR-038 DAC çekirdeğini verdi ama düşük kademe DAC alternatiflerini bilinçli olarak kapsam dışı bıraktı.
3. Karar ertelendiği için `donanım-devre-referanslari.md:315`'teki "düşük maliyetli SKU" sorusu ve ADR-038'in ADR-083 yönlendirmesi **askıda** kaldı; numara çakışması (ADR-083) çözüm bekliyordu.

### §1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (v7.2.0 — resmi/üretici kaynağı önce, her ana iddia ≥2 çapraz kaynak, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`).
> **Araç notu:** `websearch` kanalı 2 sorguda **boş sonuç** döndü (0 kaynak) → araştırma **exa** ile tamamlandı (toplam 10 exa sorgusu; aşağıdaki kaynak listesi dosyaya giren **doğrudan görülen 10 kaynaktır**).

| Alan | Değer |
|------|-------|
| Web Search **Query** | `1)` multichannel audio interface product family channel count variants 2.0/2.1/5.1/7.1/7.1.4 platform shared PCB cost down · `2)` audio DAC channel scaling one multichannel codec vs multiple stereo DACs cost tradeoff · `3)` amplifier channel count trade-off class AB multi-channel modular design product SKU · `4)` product platform architecture SKU proliferation cost common core board variants design to cost · `5)` PCM3168A alternative multichannel audio codec 8 out DAC · `6)` multi-channel power amplifier modular channel count cost thermal trade off AVR 5.1 7.1 variants · `7)` audio hardware design to cost common platform BOM reduction stereo vs multichannel variant (exa, 10 çağrı) |
| Web Search **Konusu** | Kanal varyantlı ses ürün ailesi (arayüz/amfi), ortak platform-BOM mimarisi, DAC kanal ölçekleme, SKU yayılımı maliyeti, modüler amplifikatör ailesi fiyat/konfigürasyon örnekleri |
| Web Search **Bağlam** | ADR-090 kapsamı: 10 varyantlık matris, 4 kademeli maliyet stratejisi, ADR-089 (amfi) + ADR-038 (DAC) hizası; her varyant için DAC kademesi/amfi kanalı/maliyet bandı gerekçelendirilmeli |
| Web Search **Kısa Açıklama** | Üreticiler aynı platformdan kanal sayısına göre varyant çıkarıyor (Apogee 2x12/8x8/8x16; Emotiva XPA tek-kanal modül platformu 5/7 kanal; Grace m701 modüler slot); stereo→çok-kanallı geçişte DAC tek çip (PCM3168A sınıfı) maliyet/karmaşıklık avantajı sağlıyor; literatür ortak platform + modüler ailenin iç çeşitlilik maliyetini düşürduğunu, ama SKU arttıkça test/stok karmaşıklığının arttığını söylüyor |
| Web Search **Uzun Açıklama** | **(1)** Apogee Symphony Studio aynı seride **2x12 ($2.199), 8x8 ($2.999), 8x16 ($3.999)** satar; 2x12 → 7.1.4 immersive, 8x8 → 7.1, üst kademe 16 çıkış (DB25 ile 1U) — yani **kanal sayısı doğrudan fiyat kademesi**. **(2)** ESI Prodigy 7.1 HiFi tek kartta **2, 4, 6 (5.1), 8 (7.1)** kanal desteği verir (Wolfson WM8776/8766, 4 katmanlı PCB) → aynı PCB ailesinden kademe. **(3)** Grace Design m701 **8 slota 8'er kanallı ADC/DAC modülleri** takar; müşteri istediği I/O kombinasyonunu seçer → **modüler slot = varyant üretimi**. **(4)** MOTU 848 tek cihazda 60 eşzamanlı kanal, 5.1/7.1.4 monitor grubunu tek fader ile kumanda eder. **(5)** Emotiva **XPA Gen3**: tek kanal Class A/B modülleri → XPA-5 ($2.249, 5 kanal, 2 slot boş) ve XPA-7 ($2.749, 7 kanal) aynı platform; siparişe göre konfigüre edilir ("built to order", ileri ekleme ile genişleme) → **aynı amfi modülünden SKU türetme** ADR-089'un modüler kanal PCB'siyle birebir aynı fikir. **(6)** XMOS XR-USB-AUDIO-2.0-MC referansı 6-in/8-out + 18in/8out mikser (ADR-038'in XU316/PCM3168A çiftinin referans ailesi). **(7)** Akademik: Ripperda & Krause *Cost Effects of Modular Product Family Structures* — modüler aile iç çeşitlilik (çeşit başı maliyet) düşürür ama ölçülmesi gerekir; Bortolini vd. (Springer 2023) platform = "ortak alt sistemlerden türeyen varyantlar", MTS stok + MTO/ertelenmiş farklılaştırma ile hem maliyet hem teslim süresi iyileşir; Martin & Ishii *Design for Variety* = ürün yayılımının (proliferation) maliyetini ölçmenin yolu (genial variety/coupling indeksleri). |
| Web Search **Paragraf Veri Uzun** | Üretici kanıtları üç şeyi birlikte gösteriyor: (i) **kanal sayısı fiyat kademesini doğrudan belirler** (Apogee $2.199 → $3.999 aynı seride 2x12 → 8x16; Emotiva XPA-5 $2.249 → XPA-7 $2.749); (ii) **aynı PCB/platformdan kademe üretmek** mümkündür ve yaygındır (ESI tek kartta 2/4/6/8 kanal; Emotiva tek kanal modülden 5-7 kanal SKU; Grace m701 8 slot × 8 kanal modül); (iii) **modülerlik + ertelenmiş farklılaştırma** literatürde maliyet/stok duyarlılığı birlikte iyileştirir (Bortolini platform MTS+MTO; Ripperda & Krause modüler aile iç çeşitlilik maliyeti düşürür). Bunların CoreMusic'e transferi: ortak çekirdek PCB + kanal başına modül (K16 zaten bu), DAC'da tek çip (PCM3168A) kademesi ve her varyantın ayrı SKU'su. Sayısal CoreMusic maliyet bantları **dış kaynaklı değildir** — vault BOM rakamlarından türetilmiştir (§1.4 kısıt, §4.3 R4). |
| Web Search **Sonucu** | 10 exa sorgusu; **9 doğrudan üretici/akademik kaynak + 1 vault doğrulanmış datasheet çaprazı** dosyaya alındı (aşağıda); kanal→fiyat kademesi, ortak platform ve modüler SKU üç iddiası **≥2 bağımsız kaynakla** çaprazlandı; **kanıtsız kalan tek alan** = CoreMusic'e özel %/TL maliyet bantları → `⚠️ VERIFICATION REQUIRED` (§2.3) |
| Web Search **Alınan Karar** | **Tek ortak çekirdek şemadan (aynı PCB ailesi) türeyen 10 kanal varyantı ayrı SKU olarak piyasaya sürülür**; DAC kademesi 8-kanal PCM3168A'da sabit kalır (düşük varyantta kullanılmayan kanallar disable; stereo/küçük çip geçişi ayrı ADR ister — PCM5122 H001 yasağı nedeniyle dışlanır); amfi kademesi ADR-089'un modüler kanal PCB'sinden **kanal sayısı kadar** modül adediyle ölçeklenir; maliyet 4 kademede (ortak çekirdek → DAC → amfi → SKU) kurgulanır |
| Web Search **Sonuç** | Dış kaynaklar **platform + modüler SKU** stratejisini **destekliyor**; kanal sayısının fiyat/termal/test yükü üzerindeki bedeli de kaynaklarda görünür (Emotiva 7 kanal tüm kanallar sürülürken 200W/8Ω; Apogee üst kademe farkı) → §4.1/§4.2/§4.3'te pozitif+negatif+risik olarak işlendi |

**Kaynak listesi (10):** 1) apogeedigital.com/symphony-studio-series (kanal kademe fiyatları) · 2) esi-audio.com/products/prodigy71hifi (tek kart 2/4/6/8 kanal) · 3) gracedesign.com/products/m701-audio-interface (modüler 8-slot, 8-kanal modüller) · 4) motu.com/en-us/products/848 (60 kanal, 5.1/7.1.4 monitor) · 5) emotiva.com/products/xpa-7-gen3 (7 kanal, modüler tek kanal platform, Class A/B) · 6) emotiva.com/products/xpa-5-gen3 ($2.249, 5 kanal + 2 boş slot) · 7) resources.ampheo.com — XMOS XR-USB-AUDIO-2.0-MC datasheet (6in/8out) · 8) Ripperda & Krause, *Cost Effects of Modular Product Family Structures* (exa library) · 9) Bortolini vd., *A two-step methodology for product platform design…* (Springer, 2023) · 10) TI PCM3168A datasheet (ADR-038 §1.3 kaynak 1-2 ile **vault'ta çapraz doğrulanmış**) · ayrıca Martin & Ishii *Design for Variety* ve Simpson vd. *Product Family and Product Platform Design* (kaynak 8-9'un atıfları — çapraz bağlam).

### §1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| **H001 — PCM5122 yasağı** | 2 kanal olduğu için yasaklı; mono/2 kademesinde PCM5122 tabanlı stereo DAC **kullanılamaz** (brain `:878`, `k1-donanim/CLAUDE.md:20`) — alternatif stereo çip = ayrı ADR |
| **ADR-089 hizası zorunlu** | Her varyantın amfi kademesi Class AB (MJL21194/MJL21193, ±35V, modüler PCB) olmak zorunda; topoloji değişikliği yeni ADR ister |
| **ADR-038 çekirdeği değişmez** | DAC/USB çekirdeği PCM3168A + XU316; varyantlar bu çekirdeğin **kanal kademesi**dir, alternatifi değil |
| **Frozen 001-037 immutabel** | Yalnız atıf; düzeltme yapılmaz |
| **Kod 0 → PLANNED disiplini** | SKU/kanal varyant kodu repoda 0 (§1.1-B); tüm uygulama adımları PLANNED etiketlidir |
| **Maliyet rakamları tahmindir** | Vault BOM'undan (k16 <$430, K20 ~$682, 8-channel-design $882) türetilmiştir; dış kaynak değildir → `⚠️ VERIFICATION REQUIRED` |
| **REDACTED** | Tedarikçi teklif gizliliği, sır/credential yazılmaz |
| **Tek yazma kanalı** | Tüm vault yazımı `.ai/scripts/vault-utf8-writer.mjs`; `log.md` yalnız append |

---

## §2 Karar (Decision)

**CoreMusic ses donanımı, tek ortak çekirdek şemadan türeyen 10 kanal varyantlı bir ürün ailesi olarak — her varyant AYRI SKU ile — piyasaya sürülür. Amfi kademesi ADR-089'un modüler Class AB çekirdeğinden kanal sayısı kadar ölçeklenir; DAC kademesi ADR-038'in PCM3168A + XU316 çekirdeğinde sabit kalır.**

### §2.1 Kanal Varyant Matrisi (tam — kullanıcı onaylı)

| # | Varyant | Hedef pazar / kullanım | DAC kademesi | Amfi kanalı (ADR-089 hizası) | Beklenen maliyet bandı (TAHMİN ⚠️) |
|---|---------|------------------------|--------------|------------------------------|-------------------------------------|
| 1 | **mono** | Tek hoparlör ofis/stüdyo monitörü, seslendirme, DJ yakında dinleme | PCM3168A (1 kanal aktif; kalan kanallar disable) | **1 kanal** Class AB (ADR-089 1/2/4/6/8 listesinde) | ~**$180-260** (k16 <$430'un ~%45-60'ı) |
| 2 | **2** | Stereo hi-fi / bookshelf, iki monitörlü stüdyo | PCM3168A (2 aktif kanal) | **2 kanal** | ~**$240-320** |
| 3 | **2+1** | 2.0 + subwoofer (bass management, 80Hz LR4) | PCM3168A (3 aktif kanal) | **2 kanal + 1 sub kanalı** (ADR-089 2 kanal + modüler genişleme) | ~**$300-380** |
| 4 | **4** | Quad / 3.1 stüdyo, monitör + surround çifti | PCM3168A (4 aktif) | **4 kanal** (ADR-089 listesinde) | ~**$360-460** |
| 5 | **5** | 5.0 / 5.1 (LFE dahil) ev sineması girişi | PCM3168A (5 aktif) | **4+1 kombinasyon** (ADR-089 listesinde 5 yok → §2.2-b) | ~**$420-520** |
| 6 | **6** | 5.1 / 6 kanal stüdyo monitörleme | PCM3168A (6 aktif) | **6 kanal** (ADR-089 listesinde) | ~**$470-570** |
| 7 | **7** | 6.1 / 7.0 surround, Atmos "bed" kanalları | PCM3168A (7 aktif) | **6+1 kombinasyon** (§2.2-b) | ~**$520-620** |
| 8 | **8** | 7.0 / 7.1 temel kanallar, üst segment | PCM3168A tam 8-out (ADR-038 çekirdeği) | **8 kanal** (ADR-089 çekirdeği, 8×50W) | ~**$570-700** (K20 ~$682 referans) |
| 9 | **7+1** | 7.1 surround (7 bed + LFE) | PCM3168A 8-out **+ 9. çıkış için genişleme** (§1.1-C3) | **7+1 = 8 kanal** modül | ~**$620-760** |
| 10 | **8+1** | 8.1 surround (brain §9: 8 kanal + 1 LFE) | PCM3168A 8-out **+ ikinci DAC / TDM genişleme** (§1.1-C3, R2) | **8+1 = 9 kanal** (8 çekirdek + 1 sub modülü) | ~**$680-820** |

**Maliyet bandı okuması (uyarı):** Bantlar vault BOM rakamlarından **oransal** türetilmiştir (k16 sınıfı amplifikatör BOM hedefi **<$430** [[../../architecture/k16-class-ab/CLAUDE.md]]; K20 sistem **~$682** [[../../CLAUDE.md]] L120; `8-channel-design.md` **$882** listesi 100W revizyonudur ve SSOT değildir — §1.1-C1). Gerçek bantlar tedarikçi teklifiyle doğrulanır (**§4.3 R4**).

### §2.2 Maliyet Kademesi (4 kademe — kullanıcı onaylı)

| Kademe | Strateji | Uygulama | Kısıt |
|--------|----------|----------|-------|
| **(a) Ortak çekirdek şema** | **Aynı PCB ailesi**; kanal sayısı azaldıkça modül adedi/boş slot azalır | K16 kanal başına bağımsız PCB (enable pinli) zaten bu mimaridir — varyantlar aynı çekirdek revizyonunu paylaşır, front panel/konnektör kademe belirler | K19 6-layer/ENIG/star ground sabit; farklı varyant için ayrı stackup YOK |
| **(b) DAC ölçekleme** | PCM3168A **8-kanal tek çip** birincil kademe; düşük varyantta kullanılmayan kanallar **disable** edilir (çip değişmez → tek BOM, tek firmware) | Alternatif kademe (6-in/8-out altı): **AK4458** (brain §8 "opsiyonel", DAC-only) veya stereo/4-kanal çip adayları — her biri **ayrı ADR** ister; **PCM5122 yasak** (H001) | ADC tarafı 6 kanal (ADR-038); 9. çıkış (8+1) için ikinci DAC/TDM — R2 |
| **(c) Amfi kanalı azaltma** | ADR-089 **8×50W çekirdek** → varyant başına kanal modülü adedi düşer (1/2/4/6/8 + kombinasyonlar) | Modül PCB'si aynı; K17 (±35V boost) ve K18 (termal) varyant yüküne göre **yeniden boyutlanır** (tek kanal mono'da boost kapasitesi ve heatsink küçülür) | Topoloji/çıkış transistörleri **değişmez** (ADR-089) |
| **(d) Ayrı SKU** | Her varyant **bağımsız SKU** olarak pazara sürülür (stok, test, ambalaj, fiyat ayrı) | SKU kodu ailesi `CM-<kanal kademesi>` — **⚠️ VERIFICATION REQUIRED:** SKU sözdizimi/verification kuralı vault'ta tanımlı değil, üretim öncesi sabitlenir | SKU patlaması riski → R1 |

#### §2.2-b ADR-089 kanal listesi ile matris uyumu (hileli noktalar)

- ADR-089 **1/2/4/6/8** modüler kanal listelerir; matristeki **5** ve **7** bu listede yoktur → **4+1** ve **6+1** kombinasyonu olarak üretilir (aynı modül, ayrı slot/enable planı). Topoloji değişmez, yalnız modül adedi değişir.
- **2+1 / 7+1 / 8+1** subwoofer kanalları ADR-089'un kanal modülüyle aynı **bağımsız PCB + enable** kuralına bağlanır; LFE için ayrı "subwoofer amfi" topolojisi **yoktur** (aynı Class AB modülü, düşük frekans yükü).
- K17 güç kaynağı ve K18 termal her varyantta **aynı mimarinin küçültülmüş hâli**dir; yeni devre topolojisi değil, **boyutlandırma** değişir.

### §2.3 Neden Bu Seçenek? (Rationale)

1. **Tek çekirdek, çok SKU:** Ortak PCB/BOM, iç çeşitlilik maliyetini düşürür (Ripperda & Krause; Bortolini vd. — §1.3 kaynak 8-9); her varyant için sıfırdan tasarım yok.
2. **Ölçeklenebilir omurga zaten var:** K16 modüler kanal PCB'si + ADR-089 "1/2/4/6/8" kararı bu matrisin uygulanmasını **yeni topoloji olmadan** mümkün kılar (Emotiva XPA mantığı — §1.3 kaynak 5-6).
3. **DAC'da tek çip = tek firmware:** PCM3168A sabit kalınca ADR-038'in saat/jitter/ölçüm planı (M1-M6) **tüm varyantlarda** geçerlidir; DAC çipini varyantlara göre bölmek test matrisini katlar.
4. **Pazar kapsaması tam:** mono → 8.1 arası 10 nokta, Apogee/Emotiva gibi rakiplerin kanal-kademe-fiyat modeliyle uyumlu (§1.3).
5. **Geriye dönük risk düşük:** Karar PLANNED (kod 0); şema ve BOM henüz çizilmediği için vazgeçme maliyeti şema seviyesindedir.

### §2.4 Teknik Detaylar

#### (a) Varyant başına taslak şema kapsamı (araştırma + şema)

| Şema kalemi | Kapsam (tüm varyantlarda ortak) | Varyant bazında fark |
|---|---|---|
| **S1 — Giriş/USB** | XU316 + UAC2.0, I2C init (ADR-038 §2.2-b) | yok — ortak çekirdek |
| **S2 — DAC/ADC** | PCM3168A 8-out/6-in, tek saat kaynağı, I2S/TDM | aktif kanal maskesi; **8+1/7+1** için 2. DAC/TDM genişleme şeması (R2) |
| **S3 — Amfi modülü** | K16 tek kanal: diff pair → VAS → Vbe mult → MJL21194/93, NFB, DC offset/overcurrent/termal koruma | modül adedi 1…9; enable hatları MCU'ya (ADR-089 §2.1) |
| **S4 — Güç** | K17 12-24V → LM5122 dual boost → ±35V, OR-ing, UVP/OVP/OCP/OTP | boost kapasitesi kanal yüküne göre ölçeklenir (mono ~1 kanal → 8+1 ~9 kanal) |
| **S5 — Termal** | K18 Fischer SK53-100-SA + PWM fan + KSD301 72°C | heatsink/fan adedi kanal yüküne göre (41W/kanal → toplam ısıl yük) |
| **S6 — PCB/mekanik** | K19 6-layer 200×100mm, ENIG, 2oz, star ground | front/rear panel konnektör kademesi + boş slot tapajı |
| **S7 — SKU/BOM** | K20 ortak BOM ağacı, Mouser/Digikey | varyant başına modül/konnektör satırı farklı |

#### (b) Faz planı

| Faz | Kapsam | Durum |
|-----|--------|-------|
| **F1 — Matris dondurma** | 10 varyant + DAC/amfi/maliyet kademesi (bu ADR) → debate 3 tur / 20 persona | ✅ Debate TAMAMLANDI (18/2/0 KABUL — §7.1, 3 şart §5.4) |
| **F2 — Şema paketi** | S1-S7 taslak şemaları (mono, 2+1, 8+1 seçilen 3 pilot varyantla başlanır) | ⏳ PLANNED |
| **F3 — BOM/SKU** | Ortak BOM ağacı + varyant BOM delta'ları + SKU sözdizimi | ⏳ PLANNED |
| **F4 — Ölçüm** | AES17/IEC 61606 M1-M6 ölçümü pilot varyantta (ADR-038 §2.2-d) → tüm varyantlara yayılır | ⏳ PLANNED |
| **F5 — Seri üretim/entegrasyon** | Yazılım tarafı (ADR-019 IAudioBackend, ADR-025 EQ, ADR-032 IPC) kanal sayısını yapılandırmalı | ⏳ PLANNED |

> **Faz planı ↔ Master Plan bağı:** `brain.md:906` **ADR-087 "Master Implementation Plan (5 faz, 40 gün, 22 bölüm)"** olarak kayıtlıdır; **⚠️ VERIFICATION REQUIRED: `ADR-087` dosyası diskte YOK** (`.ai/.decisions/` içinde `ADR-08*.md` yalnız ADR-081 ve ADR-089) → bu ADR **wiki-link kurmaz**, düz metin referans verir. ADR-087 yazıldığında F1-F5 oraya bağlanır.

---

## §3 Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Her varyant için ayrı tasarım (varyant başına özgün şema/PCB)** | Her segmente özel optimizasyon | 10 kat tasarım + 10 kat test + 10 kat sertifikasyon maliyeti; parça çeşitliliği patlar | İç çeşitlilik maliyeti literatürde modüler ailenin tam tersidir (§1.3 kaynak 8-9); K16 modülerliği heba olur |
| 2 | **Tek model — her zaman 8 kanal (sadece 8+1 satılır)** | Tek SKU, tek test, en basit | Mono/2/2+1 pazarına fiyatla girilemez; Amfi/BOM gereksiz yük, termal/fiyat dezavantajı (Apogee/Emotiva kademe modeli çelişir — §1.3) | Kullanıcı onayı 10 varyantlı aileyi şart koşar; tek SKU pazarın %x'ini dışlar |
| 3 | **Tamamen satın alınmış hazır modül (satın alınan çok-kanallı kart + amfi)** | Sıfır PCB riski, hızlı | Vizyon K0-K5 kendi donanımını üretir; Class AB/±35V/DC-only mimarisi uygulanamaz; marj kaybı | ADR-038 §3 alt.5 ile aynı gerekçe; AGENTS §5 domain boundary (üretim tarafı) |
| 4 | **DAC'yı varyantlara göre bölmek (stereo/4/6/8 farklı DAC çipleri)** | Düşük varyantta çip maliyeti düşebilir | Her bölünme ayrı saat/jitter/ölçüm matrisi; **PCM5122 yasak**; firmware/BOM çeşitlenir | Ortak çekirdek (a) kademesi daha ucuz ve ADR-038 M1-M6'yı korur; gerçek tasarruf ölçülmeden kabul edilmez → ayrı ADR'ye bırakılır (fallback §4.4) |
| 5 | **Amfiyi tüm varyantlarda sabit 8 kanal bırakmak (yazılımsal kapatma)** | Tek amfi BOM'u | Mono/2 SKU'sunda 6-7 modül + K17/K18 fazla maliyet ve ısıl yük; fiyatı tutmaz | §2.2-c kademesi (kullanıcı onayı) ile çelişir; maliyet bandı karşılanmaz |

---

## §4 Sonuçlar (Consequences)

### §4.1 Olumlu Sonuçlar

- **Tek şema ailesi → 10 SKU:** Tasarım, test ve ölçüm altyapısı tekrarlanır (AES17 M1-M6 bir kez kurulur, varyantlara uygulanır).
- **ADR-089 modülerliği tam kullanılır:** Kanal modülü + enable + bağımsız PCB, varyant üretiminin doğal mekanizmasıdır; ek topoloji gerekmez.
- **Pazar kapsaması:** mono → 8.1 arası 10 fiyat kademesi; rakip modeller (Apogee 2x12/8x8/8x16, Emotiva XPA-5/7) ile aynı fiyat-kademe mantığı (§1.3).
- **Geri çekilebilirlik:** Karar PLANNED (kod/şema 0) → vazgeçmek yalnız şema işini iptal eder.
- **Yazılım tarafı uyumu:** ADR-019 IAudioBackend + ADR-025 EQ + ADR-032 IPC kanal sayısını yapılandırma olarak alır; varyant çoğalması firmware'yi yeniden yazdırmaz.

### §4.2 Olumsuz Sonuçlar

- **SKU patlaması:** 10 varyant = 10 stok kalemi, 10 test prosedürü, 10 ambalaj/fiyat kaydı.
- **Test yükü:** Ölçüm matrisi (M1-M6) varyant başına tekrarlanır; 9 kanal modülüne kadar termal doğrulama gerekir.
- **Parça çeşitliliği:** Boş slot tapajı, farklı panel/konnektör setleri ortak BOM'u genişletir.
- **Maliyet sapması:** Bantlar tahmin (§1.4); darbe/termal yükler mono'da ve 8+1'de beklenenden sapabilir.
- **9. çıkış boşluğu:** 8+1/7+1 için PCM3168A 8-out yetersizdir (§1.1-C3) → ek çip/karmaşıklık.

### §4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **R1 SKU patlaması — stok/test/karmaşıklık ürer** | Yüksek (>%70) | Orta | Ortak BOM ağacı + varyant delta listesi (§5.1 adım 3); SKU sayısı 10 ile sabitlenir, "ara varyant" talebi yeni ADR ister |
| **R2 8+1 / 7+1'de 9. çıkış (LFE) — PCM3168A 8-out yetersiz** | Yüksek (>%70) | Yüksek | İkinci DAC veya TDM genişleme şeması F2'de doğrulanır; doğrulanamazsa 8+1/7+1 varyantları **beklemeye alınır** (§4.4) |
| **R3 Test yükü — 10 varyant × ölçüm matrisi** | Orta (%40-70) | Orta | Pilot varyant (mono, 2+1, 8+1) ile F4'te ölçüm kurulur, diğerleri **benzerlik/küme** ile kabul edilir (ölçüm protokolü ADR-038 M1-M6) |
| **R4 Maliyet bandı tutmaz (tahmin ↔ teklif sapması)** | Orta (%40-70) | Orta | Bantlar `⚠️ VERIFICATION REQUIRED`; F3'te tedarikçi teklifiyle kapatılır; sapma >%20 ise matris revize edilir (yeni ADR gerekmez, `updated` + yeniden onay) |
| **R5 Parça çeşitliliği — panel/konnektör/boş slot SKU'su** | Orta (%40-70) | Düşük-Orta | Ortak panel ailesi + tapaj parçası; tedarikçi 2'nci kaynak F3'te eklenir |
| **R6 5/7 kanal kombinasyonu ADR-089 listesiyle okunur çelişir** | Orta (%40-70) | Düşük | §2.2-b'de açık kayıt: kombinasyon = aynı modül, farklı adet; ADR-089 metni **değiştirilmez** |

### §4.4 Fallback

1. **Platform mimarisi korunur:** Herhangi bir varyant vazgeçilirse ortak çekirdek (S1-S3) değişmez; yalnız o SKU listeden çıkar ve bu ADR `updated` alanıyla yeniden onaylanır.
2. **Ortak BOM:** Maliyet tutmazsa (R4) önce (c) amfi kanalı kademesi, sonra (b) DAC kademesi gözden geçirilir; **topoloji (Class AB) ve çekirdek (PCM3168A+XU316) fallback değildir** — değişimi = yeni ADR.
3. **8+1/7+1 ertelenirse:** aile 8 varyantla (mono…8) piyasaya sürülür, LFE genişlemesi ayrı ADR ile eklenir.
4. **DAC bölme (alternatif 4)** yalnız ölçümle kanıtlanan gerçek bir tasarruf varsa yeni ADR ile açılır (ADR-038 `superseded-by` zinciri değil, yeni karar).

---

## §5 Uygulama (Implementation)

### §5.1 Adımlar

| # | Adım | Sorumlu | Süre | Durum |
|---|------|---------|------|-------|
| 1 | Bu ADR'yi şablondan üret + künye/§1-§7 dolu (Guardrail #16) | Vault Steward | 2 dk | ✅ UYGULANDI (2026-09-26) |
| 2 | `.ai/log.md`'ye 1 satır append (`ADR-090 yazıldı (debate PENDING)`) | Vault Steward | 1 dk | ✅ UYGULANDI (2026-09-26) |
| 3 | **Debate 3 tur / 20 persona** (matris + 4 kademe) + Tech Lead onayı | MO + Tech Lead | 2 gün | ✅ UYGULANDI (2026-09-26 — 18/2/0 KABUL, §7.1; 3 şart §5.4) |
| 4 | F2 şema paketi S1-S7 (pilot: mono, 2+1, 8+1) + 9. çıkış doğrulaması (R2) | Audio HW Engineer | 5 gün | ⏳ PLANNED |
| 5 | C1 düzeltmesi: `k16-class-ab/8-channel-design.md` "100W" → ADR-089 SSOT (50W) hizası + log | Audio HW Engineer | 1 saat | ⏳ PLANNED |
| 6 | F3 ortak BOM ağacı + varyant delta + SKU sözdizimi sabitleme | Audio HW + Data Engineer | 3 gün | ⏳ PLANNED |
| 7 | F4 AES17/IEC 61606 ölçümü pilot varyantta (ADR-038 M1-M6) | QA + Audio HW | 2 gün | ⏳ PLANNED |
| 8 | `.ai/.decisions/index.md` kayıt satırı + brain.md ADR-090 özeti (vault sync — ayrı işlem) | MO (vault-updater) | 1 dk | ⏳ PLANNED |
| 9 | Yazılım tarafı kanal yapılandırması: ADR-019/025/032 kanal sayısını okur | Embedded + Backend | 3 gün | ⏳ PLANNED (kod 0) |

### 5.2 Geri Dönüş Planı

1. **Karar seviyesi:** `status: accepted` ama **frozen değil** → vazgeçiş/yeni karar = yeni ADR (NNN+1) ve bu dosya `superseded-by` ile bağlanır; metin **silinmez/değiştirmez**.
2. **Şema seviyesi:** F2 şemaları henüz çizilmedi (kod/şema 0) → iptal maliyeti yalnız taslak işidir; çizilen şemalar varsa yalnız o paket geri alınır.
3. **SKU seviyesi:** SKU üretimi başlamadan önce geri dönüş = matristen varyant çıkarmaktır (§4.4-3); başlamışsa stok/test kayıtları `log.md`'ye yeni satır olarak işlenir.
4. **Log/dizin seviyesi:** `log.md` append-only → geri dönüş de **yeni satır**; `index.md` kaydı geri alınırsa satır `—` olarak işaretlenir (silinmez).
5. **Bozulma durumunda:** `node .ai/scripts/vault-utf8-writer.mjs repair --file <dosya>` (yedek alır) → gerekirse `git checkout` (AGENTS §18 #5).

### 5.3 Debate Notu

**Debate: ✅ TAMAMLANDI** — 3 tur / 20 persona (ADR-038/ADR-089 formatı): **18 kabul / 2 çekimser / 0 red → KABUL**; tam kayıt §7.1. 3 bağlayıcı şart §5.4'e bağlandı (bağlayıcıdır).

### 5.4 Debate Şartları (bağlayıcı — 3 şart)

> Debate sonucu: **3 tur / 20 persona · 18 kabul / 2 çekimser / 0 red → KABUL** (§7.1). Aşağıdaki şartlar KABUL'ün koşuludur; her biri ilgili maddeye bağlıdır.

| # | Şart | Debateden kaynak | İlgili madde | Durum |
|---|------|------------------|--------------|-------|
| **1a** | **9. çıkış (C3) çözümü netleştirilir:** 8.1 = 8 kanal + 1 LFE = **9 çıkış** ↔ PCM3168A 8-out çelişkisinde **sub kanalı** çözümü F2 şemasında sabitlenir — **dahili mixer/amfi fazlalık** **veya** **7+1** (ikinci DAC/TDM genişleme) seçeneklerinden biri seçilir; seçenek seçilmeden 8+1/7+1 varyantları F2'ye alınmaz | Tur 2-1 (Perf/Critic: 9. çıkış C3) | §1.1-C3, §2.1 (satır 9-10), §4.3 R2 | ⏳ PLANNED (§5.1 adım 4) |
| **1b** | **C1 düzeltmesi:** `k16-class-ab/8-channel-design.md` "100W/kanal" satırı → **ADR-089 SSOT (50W, 25-75W aralık)** hizasına çekilir; `8-channel-design.md` güç rakamı **SSOT değildir** + log | Tur 2-2 (Critic: C1 şart) | §1.1-C1, §2.1 (maliyet bandı uyarısı), §6 | ⏳ PLANNED (§5.1 adım 5) |
| **2** | **Araştırma genişletme:** §1.3'e **DAC scaling** (tek çok-kanallı çip ↔ stereo/çoklu DAC maliyet ödünleşimi) + **SKU maliyet/proliferation** alanlarında ek kaynak — doğrudan kaynak toplamı **≥8** korunur ve derinleştirilir (mevcut 10 kaynak; websearch 2 boş sorgu exa ile telafi edilir) | Tur 2-3 (Critic: kaynak derinliği — serinin en düşüğü 10) | §1.3 | ⏳ PLANNED |
| **3** | **SKU test matrisi:** 10 varyant × M1-M6 ölçüm matrisi + **ortak BOM denetimi** (varyant delta satırları) F3/F4'te kurulur; R1 SKU patlaması mitigasyonunun somut hâli | Tur 2-4 (QA: SKU test matrisi) | §4.2, §4.3 R1, §5.1 adım 6-7 | ⏳ PLANNED |

---

## §6 İlgili Dokümanlar (İlişki tablosu — tüm hedefler diskte doğrulandı: 17/17)

| Dosya (wiki-link) | İlişki |
|-------|--------|
| [[../../CLAUDE.md]] | Ana sözleşme; K16-K20 / H1-H2 katman tanımı (L113-L127) |
| [[../../brain.md]] | §8 `:284`, §9 `:292`, §23 `:907`, slot listesi `:1021-1027` |
| [[../index.md]] | Karar dizini — ADR-090 kayıt satırı (§5.1 adım 8) |
| [[ADR-038-8-1-sound-card-chip-selection]] | DAC/USB çekirdeği (PCM3168A + XU316) + §5.3 bu karara yönlendirme |
| [[ADR-089-classab-24v]] | Amfi temeli: modüler 1/2/4/6/8 kanal, Class AB, ±35V |
| [[ADR-017-dsp-hardware-mode]] | XMOS DSP katmanı + hard-RT kısıtları (firmware beslemesi) — frozen, okunur |
| [[ADR-019-per-os-neva-player]] | IAudioBackend — varyant kanal sayısını tüketen yazılım sınırı |
| [[ADR-025-professional-eq-system]] | EQ zinciri — kanal başına işleme |
| [[ADR-032-ipc-contract-versioning]] | firmware↔host IPC sözleşmesi (kanal yapılandırması) |
| [[../../architecture/k16-class-ab/CLAUDE.md]] | K16 kısıtları + BOM <$430 hedefi |
| [[../../architecture/k16-class-ab/8-channel-design.md]] | 8 kanal şema detayı — **C1 güç çelişkisi (100W vs 50W)** |
| [[../../architecture/k17-guc-kaynagi/CLAUDE.md]] | K17 boost/±35V — varyant yüküne göre boyutlandırma |
| [[../../architecture/k18-termal/CLAUDE.md]] | K18 termal — varyant ısıl yükü |
| [[../../architecture/k1-donanim/pcm3168a-dac-adc.md]] | PCM3168A donanım dokümanı |
| [[../../ecosystem/donanım-devre-referanslari.md]] | `:315` düşük maliyetli SKU sorusu → bu ADR ile cevaplanır |
| [[../../glossary.md]] | LFE / surround terimleri (`:74`, `:246` — LFE `DOĞRULAMA GEREKLİ` işaretli) |
| [[../../PROJECTS.md]] | L305-L311 kanal 1-8 modüler durum kaydı |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | §1.3 web araştırma protokolü (v7.2.0) — wiki-link DEĞİL (`.ai/` dışında, düz yol) |
| Debate kaydı | §7.1 (3 tur / 20 persona, 18/2/0 KABUL — Tech Lead ✅) + §5.4 (3 bağlayıcı şart: 9. çıkış/C1 düzeltmesi, araştırma genişletme, SKU test matrisi) |
| **ADR-087 (Master Implementation Plan)** | ⚠️ **Diskte dosya YOK** → **wiki-link kurulmadı**; düz metin referans (§2.4-b) |

---

## §7 Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali / Vault Steward | 2026-09-26 | ✅ |
| Tech Lead | Tech Lead (debate 3/20 onayı) | 2026-09-26 | ✅ |
| Arch Lead | — | — | ⏳ |

### §7.1 Tartışma Kaydı (Debate)

| Alan | Değer |
|---|---|
| **Durum** | ✅ **TAMAMLANDI** — 3 tur / 20 persona · **18 kabul / 2 çekimser / 0 red → KABUL** |
| **Karar kapsamı kaynağı** | Kullanıcı onaylı: 10 varyantlık kanal matrisi + 4 kademeli maliyet stratejisi + ayrı SKU politikası |
| **Kapsam notu** | ADR-038 §5.3 bu konuyu "ADR-083"e yönlendirmişti; ADR-083 slotu brain `:1021`'de SPA Router'a ayrılmış → **numara çakışması bu ADR ile kapatıldı** (090 = ilk boş slot) |
| **Bekleyenler** | Arch Lead ✅, 3 şart uygulaması (§5.4: 1a, 1b, 2, 3), R2 (9. çıkış) doğrulaması, R4 (maliyet teklifi) |

#### §7.1.1 Tur Tur Kayıt (3 tur / 20 persona)

| Tur | Kapsam | Sonuç |
|-----|--------|-------|
| **1** | 20 persona — bulgu: `ADR-089-classab-24v.md` diskte var (amfi temeli wiki-link ✓); **K16 modüler kanal PCB / K17 boost ±35V / K18 termal = omurga**; kanal varyant kodu **0 → PLANNED**; çelişki **C1** (8-channel-design.md 100W ↔ ADR-089 50W, **SSOT = 089**), **C3** (8.1 = 9 çıkış ↔ PCM3168A 8-out); ADR-087 diskte yok → düz metin + `⚠️ VERIFICATION REQUIRED`; kaynak **10** (websearch 2 boş, exa 10) = serinin en düşüğü; maliyet bantları vault türevi `⚠️ VERIFICATION REQUIRED` | **15 kabul/neutral · 4 uyarı** (QA: SKU test matrisi; Perf: 9. çıkış C3; Critic: kaynak derinliği + C1 + maliyet V.R. şart) |
| **2** | İtiraz → çözüm: (1) 8.1 = 9 çıkış ↔ DAC 8-out (C3) → **sub kanalı** çözümü netleştirilir (dahili mixer/amfi fazlalık **veya** 7+1) → **şart 1a**; (2) C1 100W ↔ 50W → `8-channel-design.md` **SSOT = ADR-089** düzeltmesi → **şart 1b**; (3) kaynak 10 → **§1.3 genişletme** (DAC scaling + SKU maliyet ≥8 kaynak) → **şart 2**; (4) 10 varyant test → **SKU test matrisi** + ortak BOM denetimi → **şart 3** | 3 bağlayıcı şart → **§5.4** |
| **3** | Oy | **18 kabul / 2 çekimser / 0 red → KABUL** |

**Statü özeti:** `status: accepted` (kullanıcı onaylı kapsam) · debate **✅ TAMAMLANDI (3 tur / 20 persona — 18/2/0 KABUL, §7.1)** · **Tech Lead ✅** · **Arch Lead ⏳** · **3 şart** bağlayıcı (§5.4) · **frozen YOK** (ADR-001-037 dokunulmaz; bu dosya 090 = Active aralığı) · uygulama adımlarının tamamı **PLANNED** (kod/şema 0).

---

*ADR-090 v1.0.0 — CoreMusic Architecture Decision Record*
*Authority: ADR-090 Karar Metni (SSOT)*
*Last Updated: 2026-09-26*
*Mode: Red Team · Human Mode · Truth Mode*
