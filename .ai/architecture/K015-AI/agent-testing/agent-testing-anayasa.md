---
node_id: agent-testing-anayasa
node_type: module
status: active
parent_id: agent-testing
children: []
depends_on: [agent-testing-index]
evidence:
  - ".ai/AGENTS.md §16 + §6 + §11 + §7.6 (test standartları + routing + health, okundu 2026-10-17)"
  - "root AGENTS.md §6 + §6.1 (swarm + persona hiyerarşisi — behavior test kaynağı)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı"
open_questions: []
group: mimari
---

# agent-testing-anayasa - Ajan Test Kuralları

> **Bu dosya ne değildir:** PHPUnit/Vitest **kod testi altyapısını**, LLM **eval skorlarını** ve
> **benchmark karşılaştırmalarını** üretmez; burada **ajanın kendisinin sınanması anayasası** vardır —
> bir görev isteği geldiğinde doğru ajan mı seçiliyor, handover/escalation protokolü doğru mu
> işletiliyor, vault senkronu bozulmadan mı dönüyor — ve bunlar **kapıdan** (PASS/FAIL) geçiyor mu.

## §1 Rol (ELI15)

Ajan testi, "sistem doğru ajanı seçti mi?" ve "ajan doğru protokolü işletti mi?" sorularının
**tekrarlanabilir, kapı bağlı** (gate-bound) biçimde cevaplanmasıdır. Kod testi bir `if` bloğunun
doğruluğunu; ajan testi ise **routing/handover/escalation kararının** doğruluğunu sınar. Ölçüm kaynağı
`.ai/AGENTS.md` §6 (9 keyword grubu) + §11 (health) + §16 (QA standartları) + §7.6 (verification)
olup, hepsi 2026-10-17'de okunmuştur.

| Soru | Cevap | Kanıt |
|------|-------|-------|
| Ne sınanır? | Karar (routing, handover, escalation) — değil, metin üretimi | `.ai/AGENTS.md` §6 · §9 · §10 |
| Neyle ölçülür? | Çıktı → kapı → PASS/FAIL | `.ai/AGENTS.md` §7.6 |
| Hedef ne? | ≥80% kapsam, %0 flaky | `.ai/AGENTS.md` §16 (QA satırı) |
| Kim sahibi? | QA Engineer (cross-cutting) | `.ai/AGENTS.md` §4 (#7) |

> **eli15:** Bu dosya, "ajan testi"nin bir **ruh hali değil, tablo + kapı** işi olduğunu söyler.
> > **eli10:** Kod testi "bu fonksiyon doğru mu" der; ajan testi "bu görev doğru ajana mı gitti" der.

## §2 Kapsam (in / out)

| Kapsam | Kapsam Dışı | Kanıt |
|--------|-------------|-------|
| Routing doğruluğu — 9 keyword grubu → doğru ajan | Ajanın ürettiği **kodun** doğruluğu (PHPUnit alanı) | `.ai/AGENTS.md` §6 |
| Handover mesajı 8 alan doğruluğu + onay akışı | LLM model kalitesi / token maliyeti eval'ı | `.ai/AGENTS.md` §9.1 |
| Escalation L1→L2→L3→İnsan zinciri | Benchmark (MMLU benzeri karşılaştırma) — kapsam dışı | `.ai/AGENTS.md` §10 |
| Vault senkronu + boot + context lock davranış testi | Model eğitimi / fine-tune (ayrı ADR) | root `AGENTS.md` §2 |
| Test güvenliği: secret sızmaz, test production'a yazmaz | OWASP kod denetimi (Security Engineer) | root `AGENTS.md` §16.4 |
| Health check davranışı (retry 3 · timeout 30s · heartbeat 10s) | Üretim izleme/monitoring kurulumu | `.ai/AGENTS.md` §11.1 |
| Persona hiyerarşisi karar kuralı (3 tur debate) | Ajanların **ürettiği** içeriğin editoryal denetimi | root `AGENTS.md` §6.1 |

**Ön koşul:** bu anayasa yalnız `.ai/architecture/K015-AI/agent-testing/` altındadır; genel test
altyapısı (PHPUnit, CI) K019'da kalır — sınır `agent-testing/index.md:14` açık sorusudur
(`⚠️ VERIFICATION REQUIRED`, §25/1).

**Yazım yasağı:** bu anayasa kendi kendini **kod** olarak uygulamaz; uygulama yüzeyi (betik/test)
ayrı görevde üretilir ve §18 kapılarından geçmeden "bitti" sayılmaz. Ölçülmeyen iddia yazılmaz.

## §3 Dört Kavram Ayrımı (ajan testi ≠ kod testi ≠ eval ≠ benchmark)

| Kavram | Ne ölçer | Girdi | Çıktı | Karar tipi |
|--------|----------|-------|-------|------------|
| **Ajan testi** | routing/handover/escalation **kararı** | fixture prompt + beklenen ajan | PASS/FAIL (kapı) | bağlayıcı (gate) |
| **Kod testi** | fonksiyon/unite **doğruluğu** | girdi → beklenen değer | assert geçer/geçmez | bağlayıcı (gate) |
| **Eval** | çıktı **kalitesi** (bulanık skor) | örnek kümesi + rubrik | 0-1 arası skor | göreceli (rapor) |
| **Benchmark** | sistemlerin **karşılaştırması** | standart veri seti | sıralama/karşılaştırma | göreceli (rapor) |

Ayrım kuralı: **skor görecelidir, kapı bağlayıcıdır** — LangSmith dokümanı bu ayrımı açıkça kurar
("Testing asserts correctness … Evaluation measures performance", kaynak W2 §24/2). CoreMusic'te
**kapı olan tek şey PASS/FAIL'dir**; eval skoru kapıya girmez.

| Karışıklık | Doğru olan | Yanlış olan |
|------------|-----------|-------------|
| "LLM skoru 0.9 çıktı, test geçti mi?" | Hayır — skor rapordur, kapı ayrı | skoru kapı saymak |
| "Benchmark'ta 1. sıradayız, kalite tamam" | Hayır — göreli sıralama | benchmark'ı gate yapmak |
| "Kod testi geçti, ajan doğru mu seçti?" | Hayır — farklı katman | kod testini ajan testi saymak |

> **eli10:** Aynı soruyu iki kez soruyorsan biri koddur, biri ajandır; ikisini aynı tabloda
> **karıştırmak ölçüyü bozar**. Ölçüm birimi: kod testi = assert, ajan testi = karar, eval = skor.

## §4 Ajan Test Pyramid (5 katman)

| # | Katman | Ne yapar | Hız / maliyet | Zorunluluk |
|---|--------|----------|----------------|------------|
| 1 | **Prompt fixture** | Tek satırlık istek + beklenen ajan (ground truth) | anlık / 0 | her test |
| 2 | **Ajan çalıştırma** | Routing kararını (veya mock ajanı) çalıştır | hızlı / düşük | her test |
| 3 | **Çıktı yakalama** | Seçilen ajan adı, handover nesnesi, log satırı | anlık / 0 | her test |
| 4 | **Kapı ölçümü** | Assertion: beklenen == gözlenen | anlık / 0 | her test |
| 5 | **Değerlendirme** | Kapsam raporu (≥80%) + flaky raporu (%0) | toplu / orta | her suite sonu |

Piramit kuralı: katman 1-4 **her testte** çalışır; katman 5 **suite sonunda** bir kez çalışır ve
§14 hedefiyle karşılaştırılır. Katman 4'te kapı **yoksa** test sayılmaz (assertion'sız test = FAIL).

| Piramit dışı iş | Neden yasak | Kanıt |
|-----------------|-------------|-------|
| Kapısız "gözlem notu" | sayılmaz, kapsamı şişirir | §18/4 |
| Elle yazılmış beklenti | ground truth diskten gelmeli | §12/2 |
| Üretim verisi kopyası | secret/PII riski | §15/1 |

> **eli15:** Piramidin 1. katmanı en ucuz, en sık koşulandır; 5. katman nadiren ama **her suite
> sonunda** çalışır. Katman atlamak = kapsam açığı, kapsam açığı = §10.1'deki "coverage %80 altı"
> eskalasyonunu tetikler (`.ai/AGENTS.md` §10.1 satır 5).

## §5 Test Türleri Tablosu

| # | Test türü | Ne doğrular | Kaynak § | Tipik kapı |
|---|-----------|-------------|----------|------------|
| 1 | **Routing testi** | 9 keyword grubu → doğru birincil ajan | `.ai/AGENTS.md` §6 | 9/9 eşleşme |
| 2 | **Handover testi** | 8 alanlık mesaj + onay/red akışı | `.ai/AGENTS.md` §9.1-9.2 | alan eksiksiz |
| 3 | **Escalation testi** | L1→L2→L3→İnsan + timeout değerleri | `.ai/AGENTS.md` §10.1-10.2 | seviye sırası |
| 4 | **Vault senkron testi** | wiki-link/indeks satırı diskte var mı | `.ai/RAG.md` §3.2 kural 1 | hayalet satır 0 |
| 5 | **Boot testi** | 14 kanonik dosya okunabilir + sayı sabit | `.ai/AGENTS.md` §24.2 | 14/14 |
| 6 | **Context lock testi** | kilit 30s, priority override, queue reset | `.ai/AGENTS.md` §12.1 | deadlock 0 |
| 7 | **Health state testi** | 5 durum (200/301/408/500/503) tetiklenir | `.ai/AGENTS.md` §11.2 | 5/5 |
| 8 | **Zero-hallucination testi** | uydurma dosya/URL/sayı → FAIL | `.ai/AGENTS.md` (Zero-Hallucination) | 0 uydurma |

Test türü **ananayasa değildir**; her tür §4 piramidinin 5 katmanından geçer ve §18 kapılarından
birine bağlanır. Yeni tür ekleneceği zaman önce §19 senaryo matrisine satır eklenir, sonra kod yazılır.

| Sıra | Aksiyon | Zorunlu çıktı |
|------|---------|----------------|
| 1 | §19 matrisine fixture satırı | satır + beklenen + kapı |
| 2 | Bekleneni diskten referansla | `Dosya:` / `Satır:` künyesi |
| 3 | Testi çalıştır + raporla | PASS/FAIL + sayı |

## §6 Routing Testi — 9 Keyword Grubu (zorunlu kapsam)

| # | Keyword grubu (§6) | Fixture prompt (örnek) | Beklenen birincil |
|---|--------------------|------------------------|-------------------|
| 1 | API, endpoint, routing, middleware, PHP | "API endpoint'e middleware ekle" | Backend Architect |
| 2 | CSS, UI, responsive, ITCSS, BEM, token | "ITSS token'ını responsive yap" | UI Designer |
| 3 | CSRF, CSP, XSS, OWASP, auth | "CSRF token doğrulaması ekle" | Security Engineer |
| 4 | database, SQL, BCNF, migration, MySQL | "tabloyu 3NF'ten BCNF'e taşı" | Data Engineer |
| 5 | C++, ASIO, JUCE, audio, DSP | "ASIO ring buffer'ı optimize et" | Embedded Engineer |
| 6 | test, coverage, PHPUnit, E2E | "coverage ölçümünü %80'e çıkar" | QA Engineer |
| 7 | CI/CD, GitHub Actions, deploy | "pipeline'a secret scan ekle" | DevOps Engineer |
| 8 | vault, documentation, ADR, wiki-link | "ADR indeksini güncelle" | MO (vault-updater) |
| 9 | template, şablon, .templates | "şablon kalıbını uygula" | Tüm ajanlar (guardrail #16) |

**Kapsam hedefi:** 9 satırın **tamamı** test edilir (9/9 = %100); §16'daki ≥80% eşiği bu tabloya
**üst sınır** olarak uygulanır, tekil bir routing satırı test edilmeden suite geçemez.

## §7 Handover Testi (§9.1 8 alan → 8 assertion)

| # | Alan | Beklenen tip / değer | Assertion tipi |
|---|------|----------------------|----------------|
| 1 | Konu | boş olmayan kısa metin | `contains` / `is-not-empty` |
| 2 | Kaynak Agent | registry adı (§4 tablosu) | `equals` (enum) |
| 3 | Hedef Agent | registry adı (§4 tablosu) | `equals` (enum) |
| 4 | Öncelik | CRITICAL / HIGH / MEDIUM / LOW | `equals` (enum) |
| 5 | Etkilenen Dosyalar | diskte var olan yol listesi | `Test-Path` / `file-exists` |
| 6 | İstek | boş olmayan metin | `is-not-empty` |
| 7 | Onay Durumu | PENDING → APPROVED/REJECTED | sıralı geçiş (state) |
| 8 | Timestamp | `YYYY-MM-DD HH:MM:SS` (UTC) | `regex` |

Ek kapılar (§9.2): onay zorunlu · timeout 30s · max retry 3 · reddedilince MO devreye girer ·
tüm handover `log.md`'ye yazılır. Handover testi **hem alanları hem akışı** sınar; yalnız alan
varlığı testi eksiktir.

## §8 Escalation Testi (§10.1 9 senaryo → zincir doğruluğu)

| # | Senaryo | Beklenen zincir | Timeout | Kapı |
|---|---------|-----------------|---------|------|
| 1 | Aynı dosya değiştirilemiyor | L1 → L2 | 30s | seviye artışı |
| 2 | BCNF çelişkisi | L1 (Data) → L2 | 30s | doğru başlangıç |
| 3 | CSRF/CSP uyumsuzluğu | L1 (Security) → L2 | 15s | süre ≤15s |
| 4 | ASIO cihaz kaybı | L1 (Embedded) → L2 | 30s | fallback kaydı |
| 5 | Coverage %80 altı | L1 (QA) → L2 | 60s | §16 ihlali tetikliyor |
| 6 | Deployment başarısız | L1 (DevOps) → L2 | 30s | retry ≤3 |
| 7 | Mimari çelişki (ADR) | L2 → L3 | 60s | Uzlaşma/ADR kapısı |
| 8 | Güvenlik açığı | L2 → L3 | 15s | hızlandırılmış |
| 9 | Sistem durması | L2 → **İnsan** | anlık | son çare |

Test kuralı: her senaryo için **başlangıç seviyesi + hedef seviye + timeout** üçlüsü assertion
edilir; retry 3 aşıldıysa FAIL (§10.2 "max retry 3 her seviyede").

## §9 Vault Senkron Testi

| # | Kontrol | Yöntem | İhlal sonucu |
|---|---------|--------|--------------|
| 1 | İndeks satırındaki hedef diskte var mı | `Test-Path` / glob | hayalet satır → FAIL |
| 2 | Wiki-link hedefi diskte mi | `.ai/scripts/wiki-link-check.ps1` (kökten) | kırık link raporu |
| 3 | Frontmatter 7 alan dolu mu | gözden geçirme / betik | FM eksik → FAIL |
| 4 | Placeholder `{{` yok mu | grep | placeholder → FAIL |
| 5 | Yeni `.md` şablonla (Kalıp A-D) üretildi mi | Guardrail #16 okuma kaydı | şablonsuz → DUR |
| 6 | `log.md` append-only korundu mu | son satır değişmedi, yalnız eklendi | geçmişe dokunma → FAIL |
| 7 | Boot listesi 14 kanonik sabit mi | `.ai/CLAUDE.md` §16 ile karşılaştırma | sayı değiştiyse → FAIL |

Senkron testi **salt-okunurdur**: test asla vault'a yazmaz, yalnız okur ve sayar. Betikler yalnız
**repo kökünden** çalıştırılır (`.ai/` içinden çalıştırma → sahte sonuç, `.ai/AGENTS.md` §13.6).

| Sahte sonuç kaynağı | Belirti | Önlem |
|---------------------|---------|-------|
| `.ai/` içinden çalışma | 19 adet sahte "PNG yok" | kökten çalıştır (§13.6) |
| Bilinen 6 sahte kırık link | raporda 6 kırık | düzeltme YASAK, 6'da kalır |
| Sayım tazeliği eski | sayı ≠ disk | ölçü tarihi + yeniden sayım |

## §10 Boot Testi + Context Lock Testi

| # | Test | Beklenen | Kapı |
|---|------|----------|------|
| 1 | Boot dosyaları okunabilir mi | §24.2 listesindeki 14 dosya `Test-Path` true | 14/14 |
| 2 | Boot sayısı değişmedi mi | 14 kanonik + FULL boot 17 öğe | sayı == beklenti |
| 3 | Kilit bekleme süresi | max 30 saniye | ≤30s |
| 4 | Deadlock prevention | MO en eski kilidi kırar / priority override | kilit kırma kaydı var |
| 5 | Öncelik sırası | CRITICAL > HIGH > MEDIUM > LOW | sıralama assertion |
| 6 | Logging | lock acquire/release `log.md`'ye yazıldı | log satırı var |

Boot testi **kayıt (reproduce) testidir**: boot listesi bilerek sabittir, "tümünü oku" davranışı
değil, "liste değişmedi" davranışı sınanır. Context lock testi iki ajanın eşzamanlı erişimini
**simüle** eder (mock ajan — §13).

## §11 Persona Hiyerarşisi Behavior Testi (3 tur debate)

| # | Kural | Test edilen davranış | Kapı |
|---|-------|----------------------|------|
| 1 | Expert > Senior > Junior | kanıt gücü eşitse seviye sırası | sıra doğru mu |
| 2 | Oy çokluğu YOK | 10 Junior = 1 Expert'e yenilmez | çoğunluk kararı → FAIL |
| 3 | Her öneri gerekçe + dosya/satır taşır | kaynaksız öneri sayılmaz | kaynak yok → FAIL |
| 4 | Tur 1 = kendi önerisi | her persona kendi önerisini yazdı mı | 20/20 persona |
| 5 | Tur 2 = çapraz eleştiri | en az 1 karşı öneri var mı | karşı öneri ≥1 |
| 6 | Tur 3 = uzlaşma + ADR | uzlaşma yoksa `VERIFICATION REQUIRED` | etiket zorunlu |
| 7 | Karar bağlayıcılığı | en az 1 Expert + 1 Senior + 1 Junior görüşü | 3 seviye zorunlu |

Test tipi: **behavior test** — 3 tur kaydedilir (round1/round2/round3 çıktıları) ve her turun
kapısı ayrı ayrı ölçülür. Uzlaşmasız turda doğru davranış **durdurmak**tır (üst karar ister).

## §12 Test Verisi Kuralları (fixture · gerçek disk · sahte veri · mock)

| # | Kural | Ne yapılır | Ne YAPILMAZ |
|---|-------|-----------|-------------|
| 1 | **Fixture prompt** sabittir | tek satır istek + `expected_agent` alanı | test içinde üretilen prompt |
| 2 | **Beklenen değer diskten** gelir | routing beklentisi §6 tablosundan okunur | elle yazılmış "büyük olasılıkla" |
| 3 | **Gerçek disk verisi** okuma testi | `Test-Path`, gerçek satır sayısı | uydurma dosya yolu |
| 4 | **Sahte veri** yalnız simülasyon | lock/timeout/escalation için mock | üretim verisi kopyası |
| 5 | **Mock ajan** yalnız determinizm için | sabit yanıt döner, model çağırmaz | geçerlilik (validity) kanıtı |

Kilit ayrım: **gerçek disk verisi = doğrulama (verification)**, **sahte veri = simülasyon
(simulation)**. Sahte veriyle "dosya var" iddiası **kanıt sayılmaz** (zero-hallucination, §17).

| Veri kaynağı | Kullanım yeri | Kanıt statüsü |
|--------------|---------------|---------------|
| `.ai/AGENTS.md` §6 tablosu (gerçek) | routing beklentisi | ✅ kanıt |
| `shared/tests/` dosya varlığı (gerçek) | PHP kalıp alıntısı | ✅ kanıt |
| Simüle edilmiş lock/timeout (sahte) | davranış zinciri | ⚠️ simülasyon |
| Uydurma dosya yolu / URL | hiçbir yerde | ❌ yasak |

## §13 Determinizm & Flaky Kuralları (%0 flaky)

| # | Flaky kaynağı | Önlem | Kapı |
|---|---------------|-------|------|
| 1 | LLM çıktısının deterministik olmaması | mock ajan + sabit seed / deterministik assertion | aynı fixture 2× aynı sonuç |
| 2 | Saat/zaman damgası | sabit `timestamp` (regex format kontrolü) | format == `YYYY-MM-DD HH:MM:SS` |
| 3 | Dosya sistemi sırası | sıralama (`Sort-Object`) sonra karşılaştırma | sıra bağımsız |
| 4 | Ağa/bağımlılığa bağlılık | test offline çalışır | ağ çağrısı yasak |
| 5 | Paralel koşu → yarış | yazma yok (salt-okunur test, §15) | lock çakışması 0 |
| 6 | Ölçüm yeri yanlış (`.ai/` içi) | yalnız repo kökünden çalıştır | sahte sonuç üretilmez |
| 7 | Rastgele/UUID üretimini | sabit seed / format assertion | biçim değişmez |

**Tekrar kuralı:** bir test 3 kez üst üste aynı sonucu vermiyorsa **flaky'dir** ve suite'i düşürür
— §16 "flaky test %0" hedefi ajan testinde de bağlayıcıdır.

| Flaky kontrolü | Sıklık | Sonuç |
|----------------|--------|-------|
| Aynı fixture 3× koş | her PR | 3/3 aynı → PASS |
| Beklenen diskten yeniden oku | her suite | elle yazılmış beklenti yok |

## §14 Kapsam Hedefi (§16 QA standartlarının ajan testine karşılığı)

| # | §16 QA standardı | Ajan testinde karşılığı | Hedef |
|---|------------------|-------------------------|-------|
| 1 | Test coverage ≥%80 | routing satırları kapsandı | 9/9 = %100 (≥%80 zorunlu) |
| 2 | Flaky test %0 | deterministik fixture + mock | %0 |
| 3 | — | handover 8 alan tamamı | 8/8 |
| 4 | — | escalation 9 senaryo tamamı | 9/9 |
| 5 | — | boot 14 dosya + lock 6 kontrol | 14/14 · 6/6 |
| 6 | — | persona 3 tur × kapısı | 3/3 tur |

**Hesaplama (kapalı):** kapsanan satır / toplam satır ≥ 0.80; routing için bu oran 9/9 = 1.00'dür.
%80'in altında kalan satır sayısı `(0.80 × toplam) − kapsanan` formülüyle raporlanır ve §10.1'deki
"Test coverage %80 altı → L1 (QA) → L2" senaryosu tetiklenir.

## §15 Test Güvenliği (secret sızmaz · production'a yazmaz)

| # | Kural | Uygulama | İhlal sonucu |
|---|-------|----------|--------------|
| 1 | Testte **secret** bulunmaz | fixture'ta `[REDACTED]` maskesi | FAIL + kayıt |
| 2 | `.env` / `.env.figma` okunmaz | test kapsamı dışında (yalnız §6 tablosu) | erişim yasak |
| 3 | Test **production'a yazmaz** | salt-okunur test: `Test-Path`/`grep`/`ReadAllLines` | yazma → FAIL |
| 4 | Test yalnız bu dosyanın dışına **yazmaz** | tek çıktı = test raporu (stdout) | dosya üretimi yasak |
| 5 | Log'a sızan hassas veri | `[REDACTED]` (§17 edge case 3) | redaction assertion |
| 6 | `git commit` subagent'ta | test çalıştırması commit üretmez | commit yasak |
| 7 | Test dosyası üretimi | tek çıktı = stdout raporu | bu dosya dışına yazım yasak |
| 8 | `.ai/*.md` / `shared/**` yazma | salt-okunur çalışma | yazma denemesi → FAIL |

Test ortamı ile üretim ortamı **aynı dosyaya dokunamaz**: en güçlü koruma, testin **hiç yazmaması**dır
— yazmayan testin production'a zarar verme yüzeyi sıfırdır.

| Yasak yüzey | Testte davranışı |
|-------------|------------------|
| `.env`, `.env.figma` | açılmaz, okunmaz (§13.9/3) |
| `.ai/.png/**` (salt-okunur SSOT) | yalnız okuma |
| `*.php` / `*.js` / `*.css` / `*.sql` | yalnız okuma, değiştirme yok |
| `git commit` | subagent **atmaz** — orkestratöre ait |

## §16 Health Check Testi (§11 parametreleri)

| # | Parametre | Beklenen | Test tipi |
|---|-----------|----------|-----------|
| 1 | Timeout | 30 saniye | sabit değer assertion |
| 2 | Max Retry | 3 | sayaç == 3'te dur |
| 3 | Check Interval | her görev başında | çağrı sayacı ≥ görev sayısı |
| 4 | Heartbeat | 10 saniye | aralık ≤10s |
| 5 | Degraded | 408/301 eşlemesi | durum kodu assertion |
| 6 | Failed → Dead | 3 retry sonrası queue reset + escalation | zincir assertion |

Sağlık durumu matrisi (§11.2): `Healthy 200 · Degraded 301 · Retry 408 · Failed 500 · Dead 503` —
test bu **5 durumun tamamını** tetikler; eksik durum = kapsam açığı (§14).

| Senaryo | Enjekte edilen hata | Beklenen davranış |
|---------|---------------------|-------------------|
| Yanıt gecikmesi >15s | uyku/timeout taklidi | `Degraded 301` + devam |
| Timeout (30s) | yanıt yok | `408` + retry 1→2→3 |
| 3 retry da başarısız | sabit hata | `500` + queue reset |
| Yanıt hiç yok | kanal kapalı | `503` + escalation |

**Dikkat:** bu testlerde bekleme **simüle edilir** (uyku süresi kısaltılır) — gerçek 30s bekleyen test
flaky üretir ve §13/%0 flaky hedefini ihlal eder.

## §17 Zero-Hallucination Testi (uydurma iddia → FAIL)

| # | İddia tipi | Doğrulama yöntemi | Sonuç |
|---|-----------|-------------------|-------|
| 1 | Dosya/dizin varlığı | `Test-Path` / glob | yoksa → FAIL |
| 2 | URL / API / sınıf / metot | disk veya resmi doküman | kanıt yoksa `⚠️ VERIFICATION REQUIRED` |
| 3 | Sayı iddiası (96 md, 227 link) | yeniden sayım | sayı ≠ ölçüm → FAIL |
| 4 | Versiyon / sürüm | `composer.json` / dosya künyesi | doğrulanamayan → `UNKNOWN` |
| 5 | Commit hash | `git log` (salt-okunur) | uydurma hash → FAIL |
| 6 | Mimari kural | `.ai/` vault künyesi | vault'suz iddia → FAIL |

Kapı: testin beklediği **tek dürüst etiket** `IMPLEMENTED / PLANNED / UNKNOWN` üçlüsüdür; test
kendisi de aynı disipline uyar (bu dosyadaki her sayı bir okuma künyesi taşır).

| Etiket | Testte kullanımı | Örnek |
|--------|------------------|-------|
| `IMPLEMENTED` | disk kanıtı Test-Path ile geldi | `shared/tests/**` glob = 45 dosya (2026-10-17) |
| `PLANNED` | kod yok, yalnız ADR/plan | ajan eval altyapısı (§25/2) |
| `UNKNOWN` | ölçüm yapılmadı | kapsam oranı kaynağı |
| `⚠️ VERIFICATION REQUIRED` | iddia kanıtlanamadı | W3 tam metni (§24/3) |

## §18 Doğrulama Kapıları (gate listesi)

| # | Kapı | Kriter | Çalıştırma yeri |
|---|------|--------|-----------------|
| 1 | FM 10 alan | frontmatter eksiksiz + sıra sabit | gözden geçirme |
| 2 | H1 + ≥25 § | her § 15-25 satır + tablo | sayım |
| 3 | Satır ≥500 | `[IO.File]::ReadAllLines(..., UTF8).Count` | repo kökü |
| 4 | Routing 9/9 | §6 tablosu tamamı assertion | test çalıştırma |
| 5 | Kanıt künyesi | `Dosya:` / `Satır:` her PHP alıntısında | gözden geçirme |
| 6 | İllüstratif etiket | Python/TS/Go bloğu üstünde ⚠️ satırı | grep |
| 7 | Authority son satır | birebir son satır | grep |
| 8 | Wiki-link | hedef diskte | `wiki-link-check.ps1` (kökten) |

Kapılar **sırayla** çalışır; 3 numaralı kapı (satır ≥500) başarısızsa dosya üretilmiş sayılmaz ve
ölçüm tekrarlanır (500-520 bandı).

## §19 Senaryo Matrisi (fixture → beklenti → kapı → sonuç)

| # | Fixture prompt | Beklenen | Kapı | Sonuç |
|---|----------------|----------|------|-------|
| 1 | "CSRF token doğrulaması ekle" | Security Engineer | routing eq | PASS/FAIL |
| 2 | "coverage %80 altı" | L1(QA)→L2, 60s | escalation zincir | PASS/FAIL |
| 3 | "DB schema değişikliği" handover | Backend → Data, HIGH | 8 alan + onay | PASS/FAIL |
| 4 | Vault'a yeni `.md`, indeks satırı | §3 satırı yeniden ölçülür | Test-Path | PASS/FAIL |
| 5 | İki ajan aynı dosya | lock ≤30s, sonra queue | deadlock 0 | PASS/FAIL |
| 6 | 3 tur debate 20 persona | Expert>Senior>Junior | 3 tur kapısı | PASS/FAIL |
| 7 | Uydurma dosya yolu iddiası | `VERIFICATION REQUIRED` | hallucination gate | PASS/FAIL |

Bu matris **en küçük geçerli test kümesidir**; yeni test, önce bu tabloya bir satır olarak yazılır
(önce veri, sonra kod — spec-before-code).

## §20 PowerShell — Routing Eşleşme Doğrulama Betiği (kalıp)

> PowerShell doğrulama betiği kalıbı — repo'da bu betik **YOK**; `.ai/scripts/*.ps1` betikleriyle
> aynı kalıp ve aynı "yalnız repo kökünden çalıştır" kuralı.

```powershell
# agent-routing-check.ps1 — kalıp: .ai/scripts/*.ps1 ile aynı işleyiş (repo köküden çalıştır)
$ag   = '.ai/AGENTS.md'
$line = Select-String -Path $ag -Pattern '^\| CSRF, CSP, XSS' | Select-Object -First 1
if (-not $line) { Write-Output 'FAIL: routing satiri bulunamadi'; exit 1 }

$expect = 'Security Engineer'
$ok     = $line.Line -match [regex]::Escape($expect)
Write-Output ("{0} -> {1}", $(if ($ok) { 'PASS' } else { 'FAIL' }), $expect)
if (-not $ok) { exit 1 }
```

| # | Betik adımı | Kapı (PASS/FAIL) |
|---|-------------|------------------|
| 1 | `Select-String` ile §6 routing satırı bulunur | satır yok → `exit 1` |
| 2 | Beklenen ajan adı satırda eşleşir | eşleşmedi → `exit 1` |
| 3 | `Write-Output` ile PASS/FAIL raporu yazılır | rapor satırı yok → FAIL |

Betik **salt-okunurdur** (yalnız `Select-String`): dosyaya yazmaz, secret okumaz; `exit 1` = FAIL (§15 · §13).

## §21 PHP — Repo Kanıtı (routing/eşleşme assertion'ı gerçek testte)

Repo'da `shared/tests/Api/GatewayMethodRoutingTest.php` adlı gerçek bir **yönlendirme assertion
testi** vardır; bu anayasadaki "routing eşleşme" kapısının PHP karşılığıdır (künyeli alıntı):

```php
// Dosya: shared/tests/Api/GatewayMethodRoutingTest.php · Satır: 33-39
private function apiRouteTable(): RouteTable
{
    $path = dirname(__DIR__, 3) . '/api.coremusic.net/config/routes.php';
    $this->assertFileExists($path);
    return RouteTable::fromArray(require $path);
}
```

| # | Öğrenilen kalıp (PHP) | Ajan testindeki karşılığı |
|---|------------------------|---------------------------|
| 1 | Beklenen route tablosu **diskten** okunur | routing beklentisi `.ai/AGENTS.md` §6 |
| 2 | `assertFileExists` → varlık kapısı | `Test-Path` / glob kapısı |
| 3 | `dispatch()` edilir, sonuç karşılaştırılır | beklenen ajan == seçilen ajan |
| 4 | `setUp`/`tearDown` `$_SERVER` yedeğini geri alır | test, durumu üretimden ayırır |

Ders: **beklenen diskten gelir → varlık kapısına bağlanır → çalıştır → `equals` ile karşılaştır.**
Ajan routing testi de tam bu dört adımı izler; beklenti asla testin içine elle yazılmaz.

## §22 İllüstratif Örnek — Python (fixture runner + kapı)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
# ajan routing fixture runner — upstream docs (pytest kalıbı) referansı; repo'da karşılığı YOK
CASES = [
    {"prompt": "CSRF token doğrulaması ekle", "expected": "Security Engineer"},
    {"prompt": "coverage %80 altı",           "expected": "QA Engineer"},
]

def test_routing() -> None:
    fails = [c for c in CASES if pick_primary_agent(c["prompt"]) != c["expected"]]
    assert not fails, f"FAIL: {len(fails)}/{len(CASES)} satir"   # kapı: hepsi ya da hiçbiri
```

Kapı: tek satır bile yanlışsa **suite FAIL** — §14'teki "9/9 = %100" all-or-nothing assertion'ı.

| # | Öğe | Kaynağı |
|---|-----|---------|
| 1 | `CASES` (fixture) | §6 routing tablosu — diskten |
| 2 | `pick_primary_agent()` | mock ajan (§13) — deterministik |
| 3 | `assert not fails` | kapı: all-or-nothing (§4 katman 4) |

## §23 İllüstratif Örnek — TypeScript (assertion + coverage sayacı)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```ts
// routing assertion + kapsam sayacı — upstream docs (promptfoo/Vitest kalıbı) referansı
type Row = { keywords: string[]; expected: string };

const ROUTING: Row[] = [
  { keywords: ["API", "endpoint", "PHP"], expected: "Backend Architect" },
  { keywords: ["CSRF", "OWASP", "auth"],  expected: "Security Engineer" },
];
export function grade(rows: Row[], hit: (p: string) => string) {
  const ok = rows.filter((r) => hit(r.keywords[0]) === r.expected).length;
  return { pass: ok === rows.length, cov: ok / rows.length };  // §14: >= 0.80
}
```

Not: `cov ≥ 0.80` eşiği §16 QA standardından gelir; routing'de pratik hedef `ok === rows.length` (9/9).

| # | Dönüş değeri | Karşılığı |
|---|--------------|-----------|
| 1 | `pass` | kapı sonucu (all-or-nothing) |
| 2 | `cov` | §14 kapsam oranı (≥%80) — mock ajan `hit()` ile üretilir |

## §24 Doğrulanmış Web Kaynakları (3)

| # | Kaynak | URL | Doğrulama | Kullanım |
|---|--------|-----|-----------|----------|
| W1 | promptfoo — Assertions & metrics | `https://www.promptfoo.dev/docs/configuration/expected-outputs/` | **fetch edildi 2026-10-17** | kapı/assertion tipleri, `threshold`, trajectory assertion |
| W2 | LangSmith — Evaluation concepts | `https://docs.langchain.com/langsmith/evaluation-concepts` | **fetch edildi 2026-10-17** | offline/online ayrımı, reference output, "test vs eval" |
| W3 | Arize — Agent evaluation guide | `https://arize.com/ai-agents/agent-evaluation/` | ⚠️ **sadece arama sonucu (başlık+özet) — tam metin OKUNMADI** | temsilî veri seti + trace fikri (kullanım: yalnız genel çerçeve) |

W1'den alınan somut kural: her assertion'ın `type` + `threshold` alanı vardır ve test sonucu
ağırlıklı skorla **PASS/FAIL**'e iner — bu anayasadaki "kapı" kavramının literatür karşılığı.
W3 tam metni okunmadığı için onun adına iddia **üretilmez** (yalnız çerçeve düzeyinde anılır).

| Kaynak | Doğrulama yöntemi | Uydurma riski |
|--------|-------------------|---------------|
| W1 | sayfa `webfetch` ile indi, assertion tipleri okundu | yok — metin elimizde |
| W2 | sayfa `webfetch` ile indi, offline/online + "test vs eval" okundu | yok — metin elimizde |
| W3 | yalnız `websearch` sonucu (başlık + özet) | var → `⚠️ VERIFICATION REQUIRED` |

**Kural:** bir kaynak yalnız **okunduysa** "doğrulandı" sayılır; arama özetindeki iddia, kaynağın
kendisine atfedilmez. URL uydurmak yasaktır — listedeki 3 URL de bu oturumda gerçek sorgularla
döndürülmüştür (2 fetch + 1 search, 2026-10-17).

## §25 Açık Sorular

| # | Soru | Öncelik | Durum / Kanıt |
|---|------|---------|----------------|
| 1 | `agent-testing/` vs `K019-testing/` sınırı kesinleşti mi? | HIGH | `⚠️ VERIFICATION REQUIRED` (`agent-testing/index.md:14`) |
| 2 | Ajan routing testi hangi altyapıda koşacak (PHPUnit / betik / harici eval aracı)? | HIGH | `UNKNOWN` — karar yok |
| 3 | Mock ajan kimliği sabit mi, model seçimine göre mi? | MEDIUM | `UNKNOWN` — tanımsız |
| 4 | §14 kapsam oranı hangi sayacı kullanıyor (satır mı, senaryo mu)? | MEDIUM | `⚠️ VERIFICATION REQUIRED` — ölçüm yok |
| 5 | 3 tur debate testi otomatik mi, insan denetimli mi? | LOW | `UNKNOWN` — root `AGENTS.md` §6.1 mekanizmayı tarif eder, testini değil |
| 6 | `shared/tests/` dosya sayısı: §25.2 "22 dosya" kaydı ile 2026-10-17 glob ölçümü (45) neden farklı? | MEDIUM | `⚠️ VERIFICATION REQUIRED` — sayım kriteri (üst dizin/dahil etme) yazılmamış |
| 7 | Ajan testi suite'i CI'a (`ci.yml`) bağlanacak mı? | MEDIUM | `UNKNOWN` — `.github/workflows/` 2 dosya (ölçüm 2026-09-27, §25.2) |
| 8 | §19 senaryo matrisi kaça genişletilecek (bugün 7 satır)? | LOW | `⚠️ VERIFICATION REQUIRED` — hedef sayı yok |

Her açık soru **ölçümle** kapanır; tahminle kapatılan soru `UNKNOWN` etiketini korur (§17).
Açık soru, çözümü için **önce** §19'a fixture satırı olarak yazılır; kod bu sıradan sonra gelir.

## §26 Kaynaklar (vault)

| # | Kaynak | Yol | Kullanım |
|---|--------|-----|----------|
| 1 | Agent registry SSOT | `.ai/AGENTS.md` (v22.2.0, okundu 2026-10-17) | §6 routing · §7.6 · §9-§12 · §16 |
| 2 | Master agent rules | `AGENTS.md` (kök, okundu 2026-10-17) | §6 swarm · §6.1 persona hiyerarşisi |
| 3 | Bu alt-dümğüm künyesi | `.ai/architecture/K015-AI/agent-testing/index.md` (48 satır) | §2 sınır · §25/1 |
| 4 | Anayasa şablonu (yapı referansı) | `.ai/architecture/K015-AI/rag/rag-anayasa.md` | yapı: H1 + § + tablo |
| 5 | PHP repo kanıtı | `shared/tests/Api/GatewayMethodRoutingTest.php` | §21 |
| 6 | Doğrulama betikleri | `.ai/scripts/*.ps1` (6 dosya, kökten çalıştırılır) | §10 · §18/8 |
| 7 | Retrieval/test eşlemesi | `.ai/RAG.md` §3 (satır 77 — routing/persona hedefi) | §9 |
| 8 | QA kalite standardı satırı | `.ai/AGENTS.md` §16 (coverage ≥%80 · flaky %0) | §14 |
| 9 | Verification adımı (adım 6) | `.ai/AGENTS.md` §7.6 (4 kontrol) | §4 katman 4 |
| 10 | Sağlık parametreleri | `.ai/AGENTS.md` §11.1-11.3 (retry/timeout/heartbeat) | §16 |
| 11 | Domain/rol sınırı | `shared/AGENTS.md` §3-§4 (QA Engineer test ekleme) | §1/§14 |
| 12 | Doğrulama kapıları (yer) | `.ai/AGENTS.md` §13.6 — betikler **yalnız repo kökünden** | §9 · §18 |
| 13 | Anayasa iskeleti (şablon) | `.ai/architecture/K015-AI/rag/rag-anayasa.md` (30 §) | yapı referansı |

Okuma sırası: bu anayasa **on-demand** referanstır; boot listesine eklenmez (boot = `.ai/CLAUDE.md`
§16 kanonik paket). Çelişkide `.ai/AGENTS.md` (registry SSOT) ve kök `AGENTS.md` kazanır.

## §27 Hızlı Referans + Ölçüm Gate

| # | Soru | Cevap |
|---|------|-------|
| 1 | Ajan testi neyi sınar? | Kararı (routing/handover/escalation) — metni değil |
| 2 | En küçük geçerli test nedir? | Fixture → koşu → kapı → PASS/FAIL (§4, 5 katman) |
| 3 | Kapsam hedefi? | §16: ≥%80 kapsam, %0 flaky; routing'de 9/9 = %100 |
| 4 | Test secret/production okur mu? | Hayır — salt-okunur, yazma yok (§15) |
| 5 | Ölçüm kapısı? | `[IO.File]::ReadAllLines($hedef,[Text.Encoding]::UTF8).Count` ≥ 500 |
| 6 | Kaynaklanamayan iddia? | `⚠️ VERIFICATION REQUIRED` — uydurma URL/kanıt yasak |
| 7 | Beklenen nereden gelir? | Diskten (§6/§9 tabloları) — elle yazılmaz (§12) |
| 8 | Test kimi sınar? | Ajanı, kodu değil (§3 dört kavram ayrımı) |

Kapı zinciri tek satırda: **fixture (diskten) → koşu → assertion → PASS/FAIL → kapsam raporu** —
biri eksikse test tamamlanmamış sayılır (§4). Ölçüm 2026-10-17 üretim oturumunda yapılır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
