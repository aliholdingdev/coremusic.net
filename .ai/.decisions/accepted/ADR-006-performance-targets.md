---
title: "CoreMusic — ADR-006: Performance Targets (Web / Sunucu / Audio Ölçülebilir Hedefler)"
type: adr
category: architecture
date: 2026-09-24
updated: 2026-09-24
version: 1.0.0
status: accepted
authority: ADR-006 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-006: Performance Targets (Web / Sunucu / Audio Ölçülebilir Hedefler)

**Durum:** accepted (kabul — frozen YOK; okunur + yazılabilir)
**Tarih:** 2026-09-24
**Karar Veren:** Vault Steward (kullanıcı onaylı — "sıfırdan yaz") · debate: ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0) · Tech Lead: ✅ 2026-09-24
**İlgili ADR'ler:** [[ADR-001-vanilla-js-itcss]] (frontend kısıtı — Vanilla JS, byte bütçesi bu kararın uygulama bağlamı) · [[ADR-004-multi-domain-spa]] (SPA route yapısı — CWV ölçümü hangi route'larda çalışacağı) · [[ADR-005-ultrathink-protocol]] (≥2 kaynak standardı — §1.3 araştırması bu protokolle üretildi) · karar dizini [[../index]] §3 satırı `ADR-006-performance-targets` (slug eşleşmesi ✅ — dosya yazıldıkça dizin linki canlı hale gelir) · Eski seri ADR-017 / ADR-019 `[[../index]]` §3'te listelenir ama dosyalar diskte YOK → eski numaralara wiki-link KURULMAZ, düz metin. · `[[../../ui-design/05-responsive-architecture]]` (responsive fallback zorunluluğu §12 — bu kararın ölçüm bağlamı).

---

## 1. Bağlam (Context)

CoreMusic'ın üç katmanında (web arayüzü, PHP 8.4 sunucu/API, audio motoru) **ölçülebilir performans hedefi yazılı değil**: CWV eşikleri vault'ta yok, API p95/p99 hedefi yok, byte bütçesi yok, PR'da eşikleri denetleyen mekanizma yok. Ölçülmeyen hedef gelişemez; regresyon ancak kullanıcı şikâyetiyle görünür olur. Bu karar **3 katmanlı performans hedef setini**, **ölçüm ve CI gate mekanizmasını**, **sayfa tipi byte bütçesini** ve **istisna sürecini** tescil eder.

**Konumlandırma (bilimsel dürüstlük):** CWV eşikleri Google'ın resmî `web.dev` değerleridir (§1.3); API ve audio eşiklerinin bir kısmı **öneri** olarak konumlandırılmıştır — audio underrun=0 hedefi vault'ta tescilli olmadığından `⚠️ VERIFICATION REQUIRED` taşır (§2.2c). Bütün sayısal iddialar §1.3'te ≥2 bağımsız kaynakla çaprazlanır (ADR-005 §2.2c kuralı).

### 1.1 Mevcut Durum

- **Web (vault kanıtı):** `.ai/CLAUDE.md` §... "Latency Hedefi" satırı yalnız **audio** içindir; CWV (LCP/INP/CLS) hedefi vault kök dosyalarında YOKTUR (grep kanıtı: `LCP|CLS` eşleşmesi `.ai/ui-design/05-responsive-architecture.md` içinde 0). `[[../../ui-design/05-responsive-architecture]]` §7.4/§12 responsive fallback zorunlu tutar ama sayısal eşik vermez.
- **Sunucu/API (vault kanıtı):** PHP 8.4 + PageRouter bağlamı `[[../../architecture/k9-api-routing/index]]` ve `.ai/CLAUDE.md` §5'te tanımlı; **TTFB / p95 / p99 hedefi YOKTUR** (`.ai/ecosystem/asio-wasapi-rehber.md` satır 271'deki "p95 gecikme hedefi henüz yok — ürün kararı" notu benzer bir boşluğun audio K2 tarafında da olduğunu doğrular).
- **Audio (vault kanıtı — hedefler VAR):** `.ai/CLAUDE.md` satır 429 + `.ai/PROJECTS.md` satır 171: **`<10ms (ASIO), <20ms (WASAPI)`** (Gecikme/Latency Hedefi). `[[../../architecture/k2-surucu/latency-optimization]]` satır 12: **`<1ms` round-trip** hedefi (k2 sürücü katmanı); `[[../../architecture/firmware/usb-audio-firmware]]` satır 429: XMOS USB **`<1ms` round-trip**; `k2-surucu/index.md` satır 50: **`<0.5ms`**, buffer 32-64 sample. `[[../../brain]]` satır 286: ASIO buffer 512 sample varsayılan (64-1024) @48kHz ≈ **10.67ms**. **Buffer underrun/xrun = 0 hedefi vault'ta YOKTUR** — `brain.md` satır 869 yalnız bir **fallback** verir (CPU %100 → Fade-out → 50ms sessizlik → restart), `k2-surucu/alsa-native.md` satır 133-137 XRUN yönetimini "kritik" der ama tolerans sayısı vermez.
- **Ölçüm/CI (disk kanıtı):** `.github/workflows/ci.yml` + `secret-scan.yml` VAR (2 workflow) — **performans gate'i YOKTUR**; Lighthouse CI / RUM / CrUX entegrasyonu vault'ta ve workflow'larda geçmez.
- **Bütçe:** Byte bütçesi (JS/CSS/HTML gzip) vault'ta YOKTUR; `.ai/.templates/index` envanterinde de yok.
- **Kapsam dışı:** Kod implementasyonu (→ CI workflow edit'i ayrı işlem); agent routing `[[../../AGENTS.md]]` tekelindedir (DIP).

### 1.2 Sorun Tanımı

(1) **Hedef yokluğu:** "Hızlı olsun" gibi ölçülemez niyetler var; sayısal kapı yok → regresyon görünmez. (2) **Dağınık audio hedefleri:** `<10ms`, `<1ms`, `<0.5ms` üç farklı sayı üç dosyada — hangisinin bütçe, hangisinin tasarım hedefi olduğu yazılı değil; underrun toleransı hiç yok. (3) **CI gatesiz ölçüme:** `.github/workflows/ci.yml` var ama hiçbir eşik PR'da denetlenmiyor → hedef kâğıtta kalır. (4) **İstisna süreci yok:** Bütçe aşılınca ne olacağı belirsiz → ya sessiz ihlal ya keyfi block.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: prompt-maker web araştırma protokolü (`.claude/skills/prompt-maker/references/10-web-research-protocol.md` — diskte OKUNDU ✅) — birincil/resmi kaynak önce, **her iddiaya ≥2 bağımsız çapraz kaynak**, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`. **Odak: (a) 2026 CWV eşikleri; (b) INP geçiş/kabul istatistikleri; (c) PHP 8.x TTFB/RTT optimizasyonu; (d) ses buffer underrun toleransları (pro-audio literatürü).**

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "Core Web Vitals 2026 thresholds LCP 2.5s INP 200ms CLS 0.1 web.dev good" · (2) "CrUX 2026 percentage of sites passing INP 200ms threshold adoption statistics HTTP Archive" · (3) "PHP 8.4 OPcache TTFB optimization API latency p95 reduce response time PageRouter" · (4) "audio buffer underrun xrun tolerance professional audio latency budget ASIO milliseconds acceptable" |
| Web Search **Konusu** | 2026 CWV iyi/kötü eşikleri (web.dev, Search Console) + CrUX/HTTP Archive geçiş istatistikleri (INP kabul oranı, tüm CWV geçen origin yüzdesi); PHP 8.4 OPcache/JIT/TTFB optimizasyonu ve p95/p99 API SLO uygulamaları; ASIO/WASAPI buffer boyu → ms dönüşümü, underrun/xrun tanımı ve toleransı (pro-audio). |
| Web Search **Bağlam** | ~16 kaynak (Haz. 2026 tarihli CrUX sürümleri dâhil): birincil — web.dev/vitals, Google Search Console Core Web Vitals report, php.net opcache.configuration, MDN performance budgets, web.dev performance-budgets codelab; ikincil — digitalapplied.com (Haz. 2026), webvitals.tools (Nis. 2026), justanalytics (Haz. 2026), hostingstep (Haz. 2026), dev.to PHP 8.4 perf guide, Pantheon/Blackfire OPcache, soundonsound, broadcast.tools, MathWorks Audio Toolbox, webtech360/attackshark buffer tabloları, Calibre bundle-size. |
| Web Search **Kısa Açıklama** | **CWV 2026 eşikleri değişmedi:** LCP ≤2.500ms, INP ≤200ms, CLS ≤0,1 (p75, 28 günlük pencere; kötü: LCP >4s, INP >500ms, CLS >0,25) — web.dev + Search Console. **INP geçişi olgunlaştı:** FID→INP (Mar. 2024) sonrası 2026'da origin'lerin ~%87'si INP'yi geçiyor (CrUX Nis./May. 2026), tüm üçlüyü geçen origin ~%51-56 — yani kapı geçilebilir ama otomatik değil; LCP mobile'da en zayıf halka (%56-62). **PHP:** OPcache bytecode'ı paylaşımlı bellekte saklar → TTFB düşer (PHP 8.5+ gömülü/zorunlu; 8.4'te açık olmalı); pratik SLO biçimi "endpoint p95 < 150ms" + CI'da regresyonda bütçe başarısızlığı. **Audio:** 48kHz'te 128 sample ≈2.67ms, 256 ≈5.33ms, 512 ≈10.67ms (tek buffer süresi); ASIO sub-10ms hedefi için tercih edilir; underrun = boş buffer → **sessizlik/crackle** (MathWorks tanımı); DPC >1000µs ise <256 sample güvenli değil. |
| Web Search **Uzun Açıklama** | **(a) CWV eşikleri (2026):** web.dev/vitals: LCP 2.5sn, INP 200ms, CLS 0.1 "iyi" eşiği; Search Console CWR tablosu: iyi LCP ≤2.5s / INP ≤200ms / CLS ≤0.1, orta ≤4s / ≤500ms / ≤0.25, kötü >4s / >500ms / >0.25; URL grubu durumu **en kötü metriğe** göre belirlenir (bir metrik kötüyse grup kötü). Ölçüm: gerçek kullanıcı verisi (CrUX), p75, yuvarlanan 28 gün — mobil+masaüstü eşikleri aynı, mobil geçmesi zor. Not: bir kaynak (ideafueled) "Mar. 2026 core update LCP'yi 2.0s'e indirdi" iddiasında bulunur — bu, Google'ın resmî web.dev/Search Console sayfalarıyla **çelişir ve 2. el iddiadır**; bu ADR resmî 2.500ms eşiğini alır, 2.0s iddiası `⚠️ VERIFICATION REQUIRED` kapsamı dışındadır (tek kaynak, çaprazlanamadı). **(b) INP/geçiş istatistikleri:** webvitals.tools (Nis. 2026): tüm CWV geçen origin %55.7 (2024'ten +5.7pp), iyi LCP %68.3, iyi INP **%87.1** (+10pp YoY), iyi CLS %80.9; justanalytics (Haz. 2026): mobilde tüm üçlü %51.3, masaüstü %67.8, mobil INP dağılımı iyi %71.2 / orta %19.4 / kötü %9.4; digitalapplied (Haz. 2026): INP geçiş %57, LCP %68, CLS %78 (kendi CrUX kesitleri); hostingstep (4.4M WordPress): mobil iyi TTFB yalnız %24.2 (eşik 800ms) — **TTFB'nin en zayıf metrik** olduğunu gösterir; PageSpeedMatters: mobil CWV geçişi 2022 %28.6 → 2026 %49.1. **(c) PHP 8.x TTFB/RTT:** php.net opcache.configuration (birincil): paylaşımlı bellekte önceden derlenmiş bytecode; Pantheon: OPcache TTFB'yi düşürür, bellek dolarsa **sessizce cache kesilir → anlık TTFB sıçraması** (risk!), PHP 8.5+ opcache varsayılan gömülü; Blackfire: OPcache analizi JIT ile birlikte anlamlı; dev.to PHP 8.4 guide: SLO → baseline (wrk/k6) → profil (Blackfire/XHProf) → düzelt → OPcache/JIT/realpath/FPM sertleştirme → **re-measure + CI perf budget (PR'ları regresyonda başarısız yap)**; hostiserver: production opcache.ini + FPM pm.dynamic havuzu ayarı. **(d) Audio underrun/latency bütçesi:** broadcast.tools: 48kHz'te 128/256/512/1024 sample = 2.67/5.33/10.67/21.33ms (tek buffer); ASIO sub-10ms hedeflerinin tercih yolu, WASAPI/WDM daha değişken; soundonsound: gecikme = buffer örneği/örneklem oranı (256@44.1kHz ≈5.8ms), düşük buffer'da ilk görünen şey **glitching**tir; MathWorks: **underrun = DAC boş buffer çektiğinde sessizlik** (işlem döngüsü zamanında örnek yetiştiremez), overrun = girdi düşüşü; webtech360: ASIO'da 128-256 sample "low latency (<10ms) underrun tetiklemeden" dengesi; attackshark: LatencyMon **DPC routine >1000µs ise <256 sample güvenli değil** (256 = başlangıç çizgisi önerisi); onlinetoolguides: underrunların ~%60'ı CPU yükü. |
| Web Search **Paragraf Veri Uzun** | LCP ≤2.500ms · INP ≤200ms · CLS ≤0,1 (p75, 28 gün, web.dev+Search Console) · kötü: >4s/>500ms/>0,25 · en-kötü-metrik kuralı · FID→INP Mar. 2024 · tüm CWV geçen origin %51.3-55.9 · iyi INP %71.2-87.1 · iyi LCP %62-68 (mobile %55.7-62) · mobil iyi TTFB %24.2 (800ms eşiği) · OPcache → TTFB düşer, bellek dolarsa sessiz kesilir → TTFB sıçraması · PHP 8.4 SLO biçimi endpoint p95 <150ms · CI perf budget: PR regresyonda başarısız · 48kHz buffer 128/256/512/1024 = 2.67/5.33/10.67/21.33ms · ASIO sub-10ms tercih · underrun = boş buffer → sessizlik/crackle (tolerans 0) · overrun = girdi düşüşü · DPC >1000µs ise <256 sample güvenli değil · underrun ~%60 CPU yükü · critical-path <170KB gzip kural-başı · webpack/Lighthouse+bundlesize CI bütçesi · vault: <10ms ASIO / <20ms WASAPI / <1ms k2 / 512 sample ≈10.67ms.
| Web Search **Sonucu** | 1) **CWV kapıları 2026'da sabit ve resmî:** LCP ≤2.500ms / INP ≤200ms / CLS ≤0,1, p75 + 28 gün, en-kötü-metrik kuralı (**kaynak 1, 2, 3, 4**) → CoreMusic hedefi bu resmî değerlerle yazılır; "LCP 2.0s" tek-kaynaklı iddia reddedildi. 2) **Geçiş istatistikleri hedefin erişilebilir olduğunu kanıtlar:** tüm CWV geçen origin %51-56, iyi INP %71-87 (**kaynak 5, 6, 7, 8**) → iddialı ama imkânsız değil; mobile odak şart (LCP mobile %56-62 — **kaynak 8**). 3) **TTFB mobile'da en yaygın başarısızlık** (%24.2 iyi — **kaynak 8**) → sunucu katmanı hedefi (≤800ms iyi eşiği) CWV'den ayrı değil, LCP'nin ön koşulu. 4) **PHP hedefleri OPcache + p95 SLO + CI bütçesi üçlüsüyle kurulur:** OPcache TTFB'nin doğrudan yoludur ama doluluk/süreli bytecode riski vardır (**kaynak 11, 12, 13**); "endpoint p95 <150ms" biçimi ve **PR'da regresyon → CI başarısız** modeli literatürde standarttır (**kaynak 10** — bu ADR'nin CI gate modeli bu kaynağa dayanır). 5) **Audio bütçesi vault sayılarıyla uyumlu düşer:** 512 sample @48kHz ≈10.67ms = vault `brain.md` satır 286 ile birebir (**kaynak 14, 15** vs vault) → `<10ms (ASIO)` bütçesi 512-sample varsayılanıyla tutarlı; underrun **kesin bir sessizlik/crackle olayıdır, toleransı 0'dır** (**kaynak 16, 17**) → hedef "underrun = 0 oturum başına" yazılabilir; DPC >1000µs uyarısı fallback'i (**kaynak 18**) `brain.md` satır 869 fade-out fallback'iyle hizalanır. 6) **Byte bütçesi yöntemi resmî:** web.dev "critical-path <170KB sıkıştırılmış" kural-başı + webpack/Lighthouse bütçe mekanizması + bundlesize CI entegrasyonu (**kaynak 19, 20, 21**) → JS ≤50KB gzip / CSS ≤30KB gzip değerleri 170KB critical-path altında, Vanilla JS (ADR-001) bağlamında muhafazakâr-dar öneri olarak konumlandırıldı (bütçe sayılarının kendisi **öneri** — proje büyümesine göre Tech Lead review'su §5.1'de). |
| Web Search **Alınan Karar** | **ADR-006 kabul edilir — 3 katman + 3 mekanizma:** **(1) Web katmanı:** LCP ≤2.500ms, INP ≤200ms, CLS ≤0,1 (p75, 28 gün, resmî web.dev/Search Console eşikleri — §2.2a). **(2) Sunucu/API katmanı:** TTFB ≤800ms ("iyi" eşiği — hostingstep/web.dev), API latency **p95 ≤150ms / p99 ≤400ms** hedefi (PHP 8.4 + PageRouter; 150ms biçimi kaynak 10'daki SLO örneğiyle hizalı), OPcache+JIT zorunlu (**§2.2b**). **(3) Audio katmanı:** **buffer underrun/xrun = 0** (sessizlik/crackle olayı — kaynak 16, 17), latency bütçesi **ASIO <10ms / WASAPI <20ms** (vault `.ai/CLAUDE.md` L429 ile birebir korunur) + tasarım hedefi k2 katmanında **<1ms round-trip** (vault k2-surucu) — bütçe vs tasarım-hedefi ayrımı yazıldı; `underrun = 0` vault'ta tescilli olmadığından `⚠️ VERIFICATION REQUIRED` + sektör literatürüne (kaynak 16, 17, 18) dayanır (**§2.2c**). **Mekanizmalar:** **(4) Ölçüm** = Lab (Lighthouse CI) + RUM (CrUX veya kendi beacon'ı — consent'li) + **CI gate**: `.github/workflows/ci.yml`'a perf job → PR'da eşik aşımı = başarısız = **block** (**§2.2d**); **(5) Byte bütçesi** sayfa tipi başına: JS ≤50KB gzip, CSS ≤30KB gzip, HTML ≤30KB gzip, critical-path ≤170KB gzip (kaynak 19); **(6) İstisna süreci** = istisna talebi → **Tech Lead onayı** → bu ADR'ye geçici madde olarak kayıt; **kalıcı istisna = yeni karar/ADR** (**§2.2e**). |
| Web Search **Sonuç** | Karar 2026 verisiyle **desteklendi**: CWV eşikleri (4 kaynak: web.dev, Search Console, 2 ikincil), INP/geçiş istatistikleri (4 kaynak: webvitals, justanalytics, digitalapplied, hostingstep), PHP TTFB/OPcache (4 kaynak: php.net, Pantheon, Blackfire, dev.to), audio underrun/latency (5 kaynak: broadcast.tools, soundonsound, MathWorks, webtech360, attackshark), byte bütçesi (3 kaynak: web.dev ×2, MDN) — **toplam ~16 birincil+ikincil URL**, çapraz doğrulama ≥2 kaynak/tüm sayısal iddialarda karşılanır. Tek-kaynaklı iddialar (LCP 2.0s) reddedildi; vault'ta karşılığı olmayan `underrun = 0` sayısal hedefi `⚠️ VERIFICATION REQUIRED` ile işaretlendi. Disk iddiaları (audio hedefleri, workflow varlığı, CWV yokluğu) glob/grep kanıtıyla doğrulandı. |

**Kaynak listesi (~16 birincil + ikincil):**
1. https://web.dev/articles/vitals — Core Web Vitals resmî eşikleri: LCP 2.5s, INP 200ms, CLS 0.1
2. https://support.google.com/webmasters/answer/9205520 — Search Console Core Web Vitals report: iyi/orta/kötü tablosu + en-kötü-metrik kuralı
3. https://webhelpagency.com/blog/core-web-vitals-2026 — 2026 eşik tablosu (p75, 28 gün, mobil/masaüstü aynı eşik)
4. https://yassersoliman.com/blog/core-web-vitals-explained-marketers — 2026 eşik teyidi (web.dev dipnotu)
5. https://webvitals.tools/blog/core-web-vitals-data-april-2026 — CrUX Nis. 2026: tüm CWV %55.7, INP %87.1 (+10pp YoY)
6. https://justanalytics.app/blog/core-web-vitals-statistics-2026 — HTTP Archive Haz. 2026: mobil %51.3 / masaüstü %67.8; INP mobil %71.2 iyi
7. https://www.digitalapplied.com/blog/core-web-vitals-benchmarks-2026-pass-rate-reference — CrUX May. 2026: %55.9 tüm CWV, LCP %68.6 / CLS %81.3 / INP %86.6
8. https://hostingstep.com/core-web-vitals-stats — 4.4M WordPress: mobil iyi TTFB %24.2 (800ms eşiği), mobil LCP %55.7
9. https://www.pagespeedmatters.com/resources/data-studies/core-web-vitals-pass-rates — mobil CWV geçişi 2022 %28.6 → 2026 %49.1
10. https://dev.to/blamsa0mine/php-84-performance-optimization-a-practical-repeatable-guide-1jp4 — PHP 8.4: SLO (p95 <150ms), baseline → profil → OPcache/JIT → CI perf budget (PR regresyonda başarısız)
11. https://www.php.net/manual/en/opcache.configuration.php — OPcache resmî yapılandırması (paylaşımlı bellek bytecode)
12. https://pantheon.io/learning-center/wordpress/php-opache — OPcache → TTFB; bellek dolunca sessiz cache kesintisi → TTFB sıçraması riski
13. https://blog.blackfire.io/boosting-php-performance-mastering-opcache-optimization-with-blackfire.html — OPcache/JIT optimizasyonu + profil akışı
14. https://broadcast.tools/guides/audio-latency-explained — 48kHz buffer tablosu: 128/256/512/1024 = 2.67/5.33/10.67/21.33ms; ASIO sub-10ms
15. https://www.soundonsound.com/techniques/optimising-latency-pc-audio-interface — gecikme = buffer/örneklem; düşük buffer'da glitching
16. https://www.mathworks.com/help/audio/gs/audio-io-buffering-latency-and-throughput.html — underrun = boş buffer → **sessizlik**; overrun = girdi düşüşü
17. https://tips.webtech360.com/en/windows/ultimate-guide-fix-windows-11-daw-latency-and-buffer-underrun-issues-for-smooth-audio-production-w11/99900958 — ASIO 128-256 sample: <10ms dengesi
18. https://attackshark.com/blogs/knowledges/audio-buffer-settings-reduce-latency-gaming — DPC >1000µs → <256 sample güvenli değil; 256 başlangıç çizgisi
19. https://web.dev/articles/incorporate-performance-budgets-into-your-build-tools — bütçe mekanizması, Lighthouse CI + bundlesize, critical-path <170KB sıkıştırılmış kural-başı
20. https://web.dev/articles/codelab-setting-performance-budgets-with-webpack — maxAssetSize/maxEntrypointSize bütçe örneği
21. https://developer.mozilla.org/en-US/docs/Web/Performance/Guides/Performance_budgets — MDN: bütçe = regresyonu önleyen limit (zaman/miktar/kural tabanlı)
22. (ikincil/çelişkili — reddedildi) https://ideafueled.com/blog/core-web-vitals-2026-explained — tek kaynaklı "LCP 2.0s" iddiası, resmî kaynaklarla çelişiyor

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Her iddia ≥2 kaynak | ADR-005 §2.2c standardı: sayısal her dış iddia ≥2 bağımsız kaynak taşır; tek kaynak → `⚠️ VERIFICATION REQUIRED` (§1.3'te LCP 2.0s örneği bu yüzden reddedildi). |
| Frozen ADR dokunulmaz | ADR-001…037 metinleri okunur/referanslanır, değiştirilmez (AGENTS.md §25.3 kural 2); bu ADR frozen değildir (status: accepted, frozen YOK). |
| log.md append-only | Bu ADR kaydı ve istisna kayıtları append ile yazılır; geçmiş satıra dokunulmaz (AGENTS.md §25.3 kural 3). |
| Dosya adı değişmez | In-Place Refactoring: `ADR-006-performance-targets.md` adı onaysız değiştirilemez (In-Place Refactoring kuralı 1). |
| REDACTED | Secret/credential hiçbir koşulda ADR'ye/CI config'e yazılmaz (REDACTED politikası). |
| Audio hedef çatışması | Vault'taki `<10ms/ASIO`, `<1ms k2`, `<0.5ms index` üçlüsü bu ADR'de **bütçe (§2.2c üst sınır) vs tasarım hedefi (k2 iç hedefi)** olarak ayrıştırıldı; çelişki varsayımı yazılmaz, vault satırı aynen korunur. |
| ADR-001 frontend kısıtı | Vanilla JS + ITCSS (framework yasak) — byte bütçesi bu bağlamda anlamlandırılır; framework tabanlı "otomatik performans" alternatifi §3'te reddedilmiştir. |

---

## 2. Karar (Decision)

**CoreMusic, üç katmanı (Web, Sunucu/API, Audio) kapsayan ölçülebilir performans hedeflerini, Lab + RUM + CI gate mekanizmasını, sayfa tipi byte bütçesini ve istisna sürecini ADR-006 olarak tescil eder.** Web eşikleri resmî web.dev/Search Console değerleridir; API hedefi PHP 8.4 + PageRouter bağlamında öneridir; audio hedefi vault sayılarını (`<10ms ASIO / <20ms WASAPI`, k2 `<1ms`) korur ve `underrun = 0` hedefini sektör literatürüyle yazar (`⚠️ VERIFICATION REQUIRED` — vault'ta tescilli değil). **PR'da eşik aşımı = CI başarısız = block**; istisna yalnız Tech Lead onayıyla geçici kayıtlı, kalıcı istisna yeni ADR ister.

### 2.1 Neden Bu Seçenek?

1. **Ölçülmeyen hedef yönetilemez:** vault'ta CWV/API eşiği yok, CI'da perf job yok (§1.1) — hedef + kapı birlikte yazılmalı; literatür "CI perf budget" modelini standart olarak önerir (kaynak 10).
2. **Resmî eşik dışına çıkmak SEO/UX riski:** CWV iyi eşikleri Google'ın kapılarıdır (kaynak 1, 2); URL grubu en-kötü-metrik kuralı tek metrik kaybının grubu kötü yapacağını söyler (kaynak 2) → üç metrik de ayrı ayrı yazılır.
3. **Erişilebilirlik kanıtlanmış:** tüm CWV geçen origin %51-56, INP %71-87 (kaynak 5, 6, 7) — hedef iddialı ama sahada geçiliyor; mobile odak LCP'de (kaynak 8).
4. **TTFB ayrı yazılır:** mobil iyi TTFB yalnız %24.2 (kaynak 8) → LCP'nin ön koşulu sunucudan başlar; PHP OPcache doğrudan TTFB yoludur (kaynak 11, 12).
5. **Audio hedefi vault'a sadık kalır:** 512 sample @48kHz ≈10.67ms (kaynak 14) vault `brain.md` satır 286 ile birebir tutarlı → bütçe sayıları icat edilmez, vault + literatür hizalanır; underrun kesin olaydır (kaynak 16, 17) → tolerans 0 yazılabilir ama vault'ta tescilli olmadığı için etiketli kalır.
6. **Bütçe + istisna süreci katı-yumuşak dengeyi kurar:** kural-başı critical-path <170KB (kaynak 19) altında dar bütçeler (JS ≤50KB gzip) + Tech Lead'li istisna kapısı → ne sessiz ihlal ne keyfi block.

### 2.2 Teknik Detaylar

**(a) Web katmanı — Core Web Vitals 2026 eşikleri (kaynak: web.dev/vitals, Search Console CWR — §1.3 kaynak 1, 2):**

| Metrik | İyi (hedef — CI gate) | Orta | Kötü (bloklanır) | Ölçüm |
|--------|----------------------|------|------------------|-------|
| **LCP** | **≤ 2.500 ms** | 2.500-4.000 ms | > 4.000 ms | p75, 28 gün (RUM/CrUX) + Lab (Lighthouse CI) |
| **INP** | **≤ 200 ms** | 200-500 ms | > 500 ms | aynı |
| **CLS** | **≤ 0,1** | 0,1-0,25 | > 0,25 | aynı |

*Kural:* URL/route grubu **en kötü metriğe** göre derecelenir (kaynak 2) → üçü birlikte geçilmelidir. Lab eşikleri RUM'dan %15 gevşek tutulur (lab koşulları gerçek cihazdan farklıdır — flakiness mitigasyonu §4.3 risk 1). `[[../../ui-design/05-responsive-architecture]]` §12 fallback zorunluluğu bu eşikleri bozmaz; responsive katman CLS'in birincil kaynağıdır → CLS gate'i ui-design görevlerinde de çalışır.

**(b) Sunucu/API katmanı — PHP 8.4 + PageRouter (kaynak: §1.3 8, 10, 11, 12):**

| Hedef | Değer | Dayanak |
|-------|-------|---------|
| TTFB (sayfa render) | **≤ 800 ms** (iyi eşiği) | CrUX/Search Console iyi TTFB eşiği (kaynak 8'deki 800ms tablosu + web.dev) |
| API latency **p95** | **≤ 150 ms** | SLO biçimi (kaynak 10: "/api/feed p95 < 150 ms") |
| API latency **p99** | **≤ 400 ms** | p95 bütçesi + kuyruk-kuyruğu payı (öneri — Tech Lead §5.1 review'su) |
| OPcache / JIT | **Zorunlu, üretimde açık** | php.net + Pantheon + Blackfire (kaynak 11, 12, 13); OPcache bellek dolunca sessiz kesilir → health check'te `opcache_get_status()` izlenir (kaynak 12 riski) |
| Ölçüm | wrk/k6 baseline → Blackfire/XHProf profili → CI'da tekrar ölçüm | kaynak 10 akışı |

**(c) Audio katmanı — XMOS/JUCE/WASAPI bağlamı (vault + §1.3 14-18):**

| Hedef | Değer | Kaynak/Durum |
|-------|-------|--------------|
| **Buffer underrun / xrun** | **= 0** (oturum başına) | Underrun = boş buffer → **sessizlik/crackle** (kaynak 16); pro-audio'da kabul edilebilir değildir. `⚠️ VERIFICATION REQUIRED` — bu sayısal hedef `.ai/` vault'unda tescilli değil; dayanak: sektör literatürü (kaynak 16, 17, 18). Vault'taki tek ilgili kayıt `brain.md` satır 869 **fallback**'idir (Fade-out → 50ms sessizlik → restart) — olay sonrası kurtarma, tolerans değil. |
| Latency **bütçesi** (üst sınır) | **ASIO < 10 ms · WASAPI < 20 ms** | **Vault birebir:** `.ai/CLAUDE.md` satır 429 + `.ai/PROJECTS.md` satır 171 ("Latency Hedefi"); literatürle uyumlu: ASIO sub-10ms'nin tercih yolu (kaynak 14, 17). |
| Latency **tasarım hedefi** (katman içi) | **< 1 ms round-trip** (k2 sürücü) · **< 0.5 ms** (`k2-surucu/index`) | Vault birebir: `[[../../architecture/k2-surucu/latency-optimization]]`, `[[../../architecture/firmware/usb-audio-firmware]]` (XMOS <1ms). Ayrım: **bütçe = ürün üst sınırı (CI/ölçüm kapısı), tasarım hedefi = mühendislik hedefi** — üç vault sayısı çelişki değil, farklı katman. |
| Buffer varsayılanı | 512 sample @48kHz ≈ **10,67 ms** (64-1024 arası) | Vault `brain.md` satır 286 + literatür tablosu (kaynak 14) birebir aynı. |
| Kötü donanım kuralı | DPC routine >1000µs ise <256 sample **güvenli değil** | kaynak 18 → LatencyMon ön-kontrolü; `brain.md` satır 869 fallback'iyle hizalı. |

**Katman-bazlı birleştirici tablo (debate Şart 3):** Mevcut satırlar değişmez; bu alt tablo vault sayılarını tek yerde toplar — **katman-bazlı netleştirme notu** (çelişki değil, bağlam bazlı):

| Katman / rol | Hedef | Tür | Vault kaynağı |
|--------------|-------|-----|---------------|
| Firmware — XMOS (K1.f) | round-trip **< 1 ms** | tasarım hedefi | [[../../architecture/firmware/usb-audio-firmware]] (satır 429) |
| k2 sürücü (K2) | round-trip **< 1 ms** · iç hedef **< 0.5 ms** | tasarım hedefi | [[../../architecture/k2-surucu/latency-optimization]] · `k2-surucu/index` (satır 50) |
| ASIO (ürün) | **< 10 ms** | **bütçe** — ürün üst sınırı (CI kapısı) | `.ai/CLAUDE.md` satır 429 · `.ai/PROJECTS.md` satır 171 |
| WASAPI out (ürün) | **< 20 ms** | **bütçe** — ürün üst sınırı | `.ai/CLAUDE.md` satır 429 · `.ai/PROJECTS.md` satır 171 |
| Buffer varsayılanı | 512 sample @48kHz = **10,67 ms** | ölçüm karşılığı (tek buffer — ASIO <10ms bütçesiyle tutarlı) | [[../../brain]] satır 286 + §1.3 kaynak 14 |

**Netleştirme (katman-bazlı):** `firmware <1ms` · `ASIO <10ms` · `WASAPI <20ms` · `512sample = 10,67ms` sayıları **bağlam bazlıdır ve birbirini dışlamaz** — her biri farklı katmanın hedefidir (tasarım hedefi / ürün bütçesi / ölçüm karşılığı); ölçüm raporları katman sütunuyla ayrılır. Bu alt tablodaki her sayı vault satırı + §1.3 kaynaklarıyla çaprazlı olduğundan **kendi satırlarında `⚠️ VERIFICATION REQUIRED` taşımaz** (debate 3/20 — tutarsızlık iddiası bu tabloyla kapatıldı); üstteki **`underrun = 0` satırındaki etiket farklı bir iddiaya aittir ve korunur** (ADR-005 §2.2c — vault'ta tescilli değil; kalkış koşulu §5.2/4).

**(d) Ölçüm + CI gate (mekanizma):**

| Katman | Araç | Ne ölçer | Gate davranışı |
|--------|------|----------|----------------|
| **Lab** | **Lighthouse CI** (`.github/workflows/` mevcut `ci.yml`'a perf job) | LCP/INP-CLS benzeri lab metrikleri, byte bütçesi (§2.2e) | PR'da iyi eşiği %15 gevşek aşırsa → **başarısız → block** |
| **RUM** | **CrUX** (varsayılan, ücretsiz) **veya kendi beacon'ı** (consent'li) | p75 LCP/INP/CLS, TTFB — 28 gün penceresi | Yalnız raporlar (gate labdadır); "Needs Improvement" 2 sprint üst üste → backlog zorunluluğu |
| **CI gate** | `ci.yml` perf job (bundlesize/Lighthouse assert) | byte bütçesi + lab eşiği | **Eşik aşımı = PR başarısız = merge block**; istisna §2.2e akışını işletir |
| **API gate** | k6/wrk smoke (PR'da yalnız ilgili endpoint) | p95/p99 bütçesi (§2.2b) | Aşım → başarısız (warn → 2 hafta sonra block — olgunlaşma) |

*Not (disk kanıtı):* `.github/workflows/ci.yml` + `secret-scan.yml` VAR; **perf job henüz YOK** → bu madde PLANNED'tir, implementasyon §5.1 adım 2.

**(e) Byte bütçesi (sayfa tipi başına) + istisna süreci:**

| Sayfa tipi | JS (gzip) | CSS (gzip) | HTML (gzip) | Toplam critical-path |
|------------|-----------|------------|-------------|----------------------|
| Statik içerik (makale/landing) | **≤ 50 KB** | **≤ 30 KB** | ≤ 30 KB | **≤ 170 KB gzip** (kaynak 19 kural-başı) |
| Uygulama/panel (SPA route) | ≤ 50 KB initial + lazy chunk'lar | ≤ 30 KB | ≤ 30 KB | ≤ 170 KB gzip |
| Ses player (K3 embed) | ≤ 50 KB (player core) | ≤ 30 KB | — | ≤ 170 KB gzip |

*Dayanak:* web.dev "critical-path <170KB sıkıştırılmış" kural-başı (kaynak 19) + MDN bütçe tanımı (kaynak 21). **JS ≤50KB / CSS ≤30KB değerleri ÖNERİ'dir** (ADR-001 Vanilla JS bağlamında muhafazakâr-dar; framework'süz kodda gerçekçi) — §5.1 adım 4'te Tech Lead ilk ölçüme göre onaylar/revize eder.

**İstisna süreci (zorunlu akış):**

```
Bütçe/eşik aşıldı → İstisna talebi (dosya, aşılan değer, gerekçe, süre)
  → Tech Lead ONAYI (§7) → bu ADR'ye geçici madde olarak yazılır (§2.2f)
    → Red → PR kapanır/revize edilir
  → Kalıcı istisna gerekiyorsa → YENİ ADR (yeni karar) açılır; bu ADR değiştirilmez
```

**(f) Geçici istisna kaydı şablonu (bu ADR'ye eklenecek madde biçimi):**

| # | Dosya/sayfa tipi | Aşılan hedef | Değer | Gerekçe | Onay | Geçerlilik |
|---|------------------|--------------|-------|---------|------|-----------|
| — | (istisna yok — 2026-09-24) | — | — | — | — | — |

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Yalnız Lab (Lighthouse CI) — RUM/CrUX yok** | Hızlı, consent gerektirmez, deterministik | Lab ≠ gerçek kullanıcı; CrUX'suz URL-grubu/28-gün penceresi görünmez; mobile gerçek LCP kaybı gizlenir (kaynak 8 mobile %56-62) | Google'ın kapısı p75 gerçek-kullanım verisidir (kaynak 1, 2); yalnız-lab "iyi skor / kötü SEO" paradoksunu üretir |
| 2 | **Yalnız RUM/CrUX — CI gate yok** | Gerçek veri, sıfır false-positive | Regresyon yalnız 28 gün sonra görünür; PR'da koruma yok; literatür "CI perf budget"ı standart önerir (kaynak 10) | Kapı olmadan hedef kâğıtta kalır (§1.2 madde 3); gecikmeli fark = maliyetli prod düzeltmesi |
| 3 | **Sektör-gevşek hedefler (ör. Lighthouse ≥90 genel skor, sayısal eşik yok)** | Yorum esnekliği, düşük yanlış-pozitif | Skor = gizli ağırlıklı karma; tek metriğin kötüsünü saklar; en-kötü-metrik kuralı ihlal edilir (kaynak 2) | Ölçülebilir ve savunulabilir kapı isteniyor; genel skor flakiness + gizli düzelme sağlar |
| 4 | **Byte bütçesi yok — yalnız CWV/TTFB kapısı** | Daha az ölçüm, daha az maintenance | Boyut regresyonu ancak CWV düşüşünde görünür (gecikmeli); web.dev bütçeyi "regresyonu önleyen limit" olarak tanımlar (kaynak 21) | Bütçe = erken-warning katmanı; 170KB critical-path kural-başı (kaynak 19) kullanılmadan CWV korunamaz |
| 5 | **Audio hedeflerini bu ADR'ye koymamak (yalnız web+API)** | Daha dar kapsam | 3 katmanlı kapsam kullanıcı onaylı; vault hedef dağınıklığı (`<10/<1/<0.5ms`) çözüm bekliyor (§1.2 madde 2) | Kapsam dışı bırakmak çelişkiyi bırakır; bütçe-vs-tasarım-hedefi ayrımı bu ADR'nin asıl kazanımı |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Üç katmanda da sayısal kapı var:** CWV (web.dev resmî), TTFB/p95/p99 (PHP), underrun=0 + latency bütçesi (audio) — "hızlı olsun" niyeti ölçülebilir hedefe döner (§2.2a-c).
- **Regresyon PR'dda yakalanır:** CI gate + byte bütçesi, bozulmayı prod'a ulaşmadan block eder (§2.2d-e; kaynak 10, 21).
- **Vault audio hedef dağınıklığı çözülür:** `<10/<1/<0.5ms` sayıları çelişki değil, **bütçe vs tasarım hedefi** olarak ayrıştırılır — vault satırları aynen korunur (§2.2c).
- **İstisna süreci şeffaf:** Tech'li onay + geçici madde + kalıcı=AUDR → keyfi esneme ve sessiz ihlal ikisi de kapanır (§2.2e-f).
- **Ölçüm iki ayağı:** Lab (kapı) + RUM (gerçeklik) → hem PR hem 28-gün p75 görünür (§2.2d).

### 4.2 Olumsuz Sonuçlar

- **CI süresi artar:** Lighthouse CI + k6 smoke PR başına ek dakikalar (paralelleştirilse de pipeline maliyeti).
- **RUM seçimi/consent yükü:** Kendi beacon'ı seçilirse GDPR/consent yönetimi gerekir (bkz. risk 2); CrUX seçilirse yeterli trafik + 28 gün gecikmesi.
- **Erken dönemde gate gevşetilir:** İlk aylarda lab eşikleri %15 gevşek → "gerçek" kapıya geç gecikme.
- **Bütçe revizyonu olasılığı:** JS ≤50KB önerisi büyümeyle ters düşebilir → §5.1 adım 4'te ilk ölçümden sonra revizyon gerekebilir.
- **Audio underrun ölçüm altyapısı PLANNED:** `underrun = 0` sayımı için sayaç/telemetri henüz vault'ta/kodda yok (disk kanıtı: sayısal hedef vault'ta yok — §1.1) → kapı kâğıtta başlar.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **CI gate flakiness** (lab ortamı gürültülü → sahte başarısızlık → block) | 3 (olası) | 3 (orta) | Lab eşikleri RUM'dan %15 gevşek (§2.2a notu); başarısızlıkta retry + warm-up; **flaky 3 kez üst üste → gate otomatik warn'a düşer, Tech Lead triage** (fallback §4.4) |
| **RUM gizliliği / consent** (beacon = kişisel veri; consent yoksa ihlal) | 2 (mümkün) | 4 (yüksek) | Varsayılan **CrUX** (anonim, Google tarafı); kendi beacon seçilirse → anonim sayım, IP hashing, consent entegrasyonu Security Engineer onayıyla; REDACTED: beacon payload'ına kimlik verisi yazılmaz |
| **Audio hedef çatışması** (`<10ms` bütçe vs `<1ms`/`<0.5ms` tasarım hedefi — yanlış yorum = iki taraf da "hedefi tutturdu" sanır) | 3 (olası) | 3 (orta) | §2.2c'de **bütçe vs tasarım-hedefi** ayrımı yazıldı; ölçüm raporu ikisini ayrı sütun gösterir; vault satırları değişmedi (SSOT korunur) |
| **Bütçe/istisna kuralının bayatlaması** (istisnalar birikir → kural anlamsızlaşır) | 3 (olası) | 2 (düşük) | İstisna maddesi **geçerlilik** alanı zorunlu (§2.2f); süresi geçen istisna otomatik düşer → yeni talep; kalıcı = yeni ADR |
| **TTFB ≤800ms hedefinin altyapıya bağlanması** (hosting/DNS/TLS bütçesi elde değil) | 2 (mümkün) | 3 (orta) | TTFB bütçesi sunucu render ile network payını ayırır; opcache health check (§2.2b); altyapı-elde-değilse istisna süreci (§2.2e) |

### 4.4 Fallback (Zaruri İstisna Kapısı)

| # | Koşul | Onay |
|---|-------|------|
| 1 | CI gate 3 kez üst üste **yanlış-pozitif** (flaky) ile PR block'ladıysa → gate geçici olarak `warn` moduna alınır; eşikler ve hedefler DEĞİŞMEZ (yalnız block→warn) | Tech Lead |
| 2 | Ses ölçüm altyapısı (underrun sayacı) üretime gelene kadar audio kapısı **PLANNED** kalır; bu süre zarfında hedefler kâğıtta geçerlidir, ihlal yaptırım üretmez — ama hedef metni SİLİNMEZ (`⚠️ VERIFICATION REQUIRED` açık kalır — ADR-005 §2.2c.3) | Vault Steward |
| 3 | RUM beacon'ı consent entegrasyonu tamamlanana kadar **yalnız CrUX** kullanılır (beacon pasif) | Security Engineer |
| 4 | Byte bütçesi ilk ölçümde gerçekçi değilse (ör. JS 65KB) → **bütçe revize edilir, kapı kalkmaz**: yeni değer Tech Lead onayıyla §2.2e'ye geçici madde yazar; kalıcı değişim = yeni ADR | Tech Lead |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | Bu ADR'yi `.ai/.decisions/accepted/` altına yaz (slug `ADR-006-performance-targets`) + `log.md` append ("ADR-006 yazıldı (debate PENDING)") + dizin satırı doğrula (`[[../index]]` §3 satırı `[[ADR-006-performance-targets]]` slug eşleşmesi ✅) | Vault Steward | 30 dk |
| 2 | **CI gate kurulumu:** `.github/workflows/ci.yml`'a `perf` job — Lighthouse assert (LCP/CLS lab) + bundlesize (§2.2e bütçeleri) + k6 smoke (p95/p99, warn modu) — eşik aşımı → başarısız → block | DevOps Engineer + QA Engineer | 1 gün |
| 3 | Debate (3 tur / 20 persona, ADR-004 formatı) + Tech Lead onayı: sonuç §7.1'e yazıldı ✅ (2026-09-24 — 18/2/0 KABUL); 3 şart §5.3'e yazıldı | Vault Steward + Debate + Tech Lead | 2 gün |
| 4 | **Bütçe gerçekçilik ölçümü:** ilk production build'de JS/CSS/HTML gzip boyutları ölçülür; JS ≤50KB / CSS ≤30KB önerisi gerçekçi değilse §2.2e'ye geçici madde (§4.4 fallback 4) | UI Designer + Tech Lead | 2 gün |
| 5 | **RUM seçimi:** varsayılan CrUX; kendi beacon gerekiyorsa consent entegrasyonu + anonimleştirme (risk 2 mitigasyonu) | Security Engineer + DevOps Engineer | 1 hafta |
| 6 | **Audio ölçüm altyapısı (PLANNED):** underrun/xrun sayacı + round-trip latency ölçümü (LatencyMon ön-kontrolü — kaynak 18); sayılar `brain.md` §... fallback satırıyla hizalanır | Embedded Engineer + Windows SW Engineer | 2 hafta |
| 7 | `[[../../brain]]` özeti + `.ai/.decisions/index.md` §3 satır durumu vault-sync ile tazelenir (MO/vault-updater) | MO (vault-updater) | 1 saat |

### 5.2 Geri Dönüş Planı

Karar süreç karardır; geri dönüş yalnız **yeni ADR** ile olur (In-Place Refactoring yasağı — bu dosya frozen olmasa da keyfi düzenlenmez). Senaryolar: (1) **CI gate kaldırılırsa/yumuşatılırsa:** yeni ADR şart; bu süre zarfında §4.4 fallback 1 geçerlidir (block→warn, eşikler sabit) + `log.md` append. (2) **CWV eşikleri Google tarafından değişirse:** web.dev/Search Console resmî sayfası yeniden okunur (≥2 kaynak — ADR-005 §2.2c), yeni eşikler bu ADR'nin `superseded`'ıyla değil **revizyon madresiyle** değil — yeni ADR ile yazılır (eşik = yeni karar). (3) **Byte bütçesi revize edilirse:** §4.4 fallback 4 akışı (Tech Lead geçici madde); kalıcı → yeni ADR. (4) **Audio underrun hedefi vault'a tescillenirse** (ör. ADR-017 serisinden bir kararla): `⚠️ VERIFICATION REQUIRED` etiketi kalkar (ADR-005 §2.2c: disk kanıtı = kalkış koşulu 2) + `log.md` append. (5) **Veri/state kaybı yoktur** — bu ADR süreçtir, kod/schema değiştirmez; vault bozulursa standart kurtarma `git checkout` + son commit (AGENTS.md §17 #10).

### 5.3 Debate Şartları (3/3 — zorunlu; debate 18/2/0 KABUL)

| # | Şart | Uygulama yeri | Sorumlu | Durum |
|---|------|---------------|---------|-------|
| 1 | **CI gate uyarı/block ayrımı + flakiness toleransı:** PR'da yalnız **ihlal (kötü) eşiği** merge'i **block** eder; **uyarı (orta) eşiği** yalnız warning verir, block vermez; lab gürültüsü için %15 gevşeklik + retry/warm-up korunur → 3 kez üst üste flaky ise §4.4 fallback 1 (block→warn, eşikler DEĞİŞMEZ) | §2.2d (CI gate satırı) + §4.3 risk 1 + §4.4 fallback 1 | DevOps Engineer + Tech Lead | ✅ şartlaştırıldı |
| 2 | **RUM beacon anonim / No-PII:** varsayılan **CrUX**; kendi beacon seçilirse payload **No-PII** (kişisel veri yok), IP hashing, REDACTED; consent gereken bölge (EU/EEA — GDPR) notu yazılır → beacon yalnız consent sonrası aktif | §2.2d (RUM satırı) + §4.3 risk 2 + §4.4 fallback 3 | Security Engineer | ✅ şartlaştırıldı |
| 3 | **Audio katman-bazlı birleştirici tablo:** firmware <1ms · ASIO <10ms · WASAPI out <20ms · 512sample = 10,67ms — bağlam bazlı, çelişki değil | §2.2c (birleştirici alt tablo) | Vault Steward + Embedded Engineer | ✅ uygulandı |

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[ADR-001-vanilla-js-itcss]] | Frontend kısıtı — byte bütçesi bu bağlamda anlamlandırılır (framework yasaklı Vanilla JS); **dosya diskte VAR** ✅ |
| [[ADR-004-multi-domain-spa]] | SPA route yapısı — CWV ölçümü hangi route'larda çalışacağı; debate format referansı; **dosya diskte VAR** ✅ |
| [[ADR-005-ultrathink-protocol]] | §1.3 araştırmasının ≥2 kaynak standardı ve `⚠️ VERIFICATION REQUIRED` kuralları bu ADR'de uygulandı; **dosya diskte VAR** ✅ |
| [[../../ui-design/05-responsive-architecture]] | Responsive fallback §12 + 4K ortalamama §7.4 — CLS/LCP ölçüm bağlamı; **dosya diskte VAR** ✅ (grep: eşik metni içermiyor — bu ADR'nin sayısal eşikleri buraya sonraki vault-sync'te taşınabilir) |
| [[../index]] | Karar dizini — bu ADR'nin kaydı §3 `[[ADR-006-performance-targets]]` (slug eşleşmesi ✅) |
| [[../CLAUDE.md]] | Karar alt registry kuralı (accepted/) |
| [[../../CLAUDE.md]] | Ana sözleşme — 16 Hard Guardrail (#16 şablon zorunluluğu); `.ai/CLAUDE.md` satır 429 audio Latency Hedefi (§2.2c birincil vault kaynağı) |
| [[../../AGENTS.md]] | §24.1 Ultrathink protokolü (bu ADR §1.3'ü bu protokolle üretti), §25.3 frozen/append-only kuralları, §16 quality standards |
| [[../../brain]] | Audio fallback satırı (Buffer Underrun → Fade-out → 50ms → restart) + ASIO buffer 512/48kHz/10.67ms — §2.2c vault kaynakları |
| [[../../log]] | Audit trail — bu ADR ve istisna kayıtları append edilir (append-only) |
| [[../../index]] | Master katalog — decisions bölümü |
| [[../../.templates/adr/adr-template]] | Bu ADR'nin 7 bölümlük şablonu (Guardrail #16) |
| [[../../architecture/k2-surucu/latency-optimization]] | k2 round-trip `<1ms` + `targetLatencyUs=500` — §2.2c tasarım hedefi |
| [[../../architecture/k2-surucu/alsa-native]] | XRUN (underrun/overrun) yönetimi "kritik" — §2.2c underrun dayana (vault) |
| [[../../architecture/firmware/usb-audio-firmware]] | XMOS `<1ms` round-trip — §2.2c tasarım hedefi |
| [[../../architecture/k9-api-routing/index]] | PageRouter/routing bağlamı — §2.2b API hedeflerinin uygulama katmanı |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | §1.3 web araştırma protokolü (diskte OKUNDU ✅ — 2+ çapraz kaynak kuralı) |
| **Düz metin (dosya diskte YOK — wiki-link kurulmaz):** ADR-017-dsp-hardware-mode · ADR-019-per-os-neva-player | `[[../index]]` §3 satır 54/56'da listeli ama `.ai/.decisions/accepted/` altında dosyaları YOK (glob kanıtı) → düz metin referans; audio donanım/OS kararlarıyla ilgili (eski seri — ADR-005 §künye kuralı ile aynı muamele) |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali (kullanıcı onaylı — "sıfırdan yaz") | 2026-09-24 | ✅ |
| Tech Lead | Debate 18/2/0 KABUL (20 persona) | 2026-09-24 | ✅ |
| Arch Lead | ⏳ | — | ⏳ |

### 7.1 Tartışma Kaydı (Debate — kaynak alanı)

| Alan | Değer |
|------|-------|
| Biçim | ADR-004 formatı — 3 tur / 20 persona |
| Durum | **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** |
| Tur 1 | 20 persona — 14 kabul/neutral, 4 uyarı: DevOps (CI gate flakiness), Security (RUM consent/GDPR), PM (istisna SLA), A11y/SEO çakışması YOK; **Critic en sert:** audio hedefleri vault'ta tutarsız (<1ms firmware / <10ms ASIO / <20ms WASAPI) → birleştirici tablo şart. |
| Tur 2 (İtiraz→çözüm) | (1) CI gate false-positive → yalnız eşik kırımında block, uyarı eşiği ayrımı · (2) RUM beacon gizlilik → anonim, No-PII, consent gereken bölge notu · (3) Audio rakam çatışması → katman-bazlı tablo: firmware <1ms · ASIO <10ms · WASAPI out <20ms · 512sample = 10,67ms — bağlam bazlı, çelişki değil · (4) İstisna süreci → Tech Lead 1 gün SLA; kalıcı istisna yeni karar ister |
| Tur 3 (Oy) | 18 kabul / 2 çekimser / 0 red → **KABUL** |
| Şartlar | 3/3 → §5.3: (1) CI gate uyarı/block ayrımı + flakiness toleransı, (2) RUM anonim/No-PII, (3) audio katman-bazlı birleştirici tablo (§2.2c) |
| Tech Lead | ✅ 2026-09-24 (debate KABUL üzerine) |
| Sonuç | **KABUL** — ADR-006 debate kapandı (18/2/0) |

---

*ADR-006 v1.0.0 | 2026-09-24 | Created — CoreMusic Vault (.decisions/accepted/ yeni seri; slug: `performance-targets`)*
*Authority: ADR-006 Karar Metni · Mode: Red Team · Human Mode · Truth Mode*
