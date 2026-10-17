---
node_id: agent-testing-surucu
node_type: module
status: active
parent_id: agent-testing
children: []
depends_on: [agent-testing-anayasa]
evidence:
  - ".ai/AGENTS.md §6 + §7.6 (routing test senaryoları, okundu 2026-10-17)"
  - "shared/tests/ envanteri + .ai/scripts/wiki-link-check.ps1 (gerçek kanıt)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı"
open_questions: []
group: mimari
---

# agent-testing-surucu - Ajan Test Sürucusu

> **Kapsam:** Bir ajan davranışının (özellikle routing) **nasıl sıvandığı** — test senaryosu →
> fixture → çalıştırma → ölçüm → PASS/FAIL → retry → rapor zinciri, 9 keyword grubunun
> tek tek test senaryoları, vault/registry değişince davranışın korunup korunmadığı regresyon
> kontrolü, sonuçların nereye yazıldığı ve test hata modları. **Kural koyar değil, tarif eder:**
> bağlayıcı kurallar `Dosya: .ai/AGENTS.md · Satır: 232-242` (§6 Routing) ve
> `Dosya: .ai/AGENTS.md · Satır: 300-305` (§7.6 Verification) içindedir. Bu dosya o kuralların
> **test kılavuzudur**.
>
> **Ölçüm tarihi:** 2026-10-17 (envanter sayıları ve satır aralıkları bugün diskten
> `[IO.File]::ReadAllLines(...UTF8)` / `Get-ChildItem` ile ölçülmüştür — §17, §19).

## §1. ELI15 — Bu sürücü ne işe yarar

Ajan testi, "model iyi cevap veriyor mu?" sorusu **değildir**. CoreMusic'te test edilen şey
dar ve ölçülebilirdir: *"Bu cümle geldiğinde registry §6 hangi ajanı söylüyor, ajan o ajanı
mı seçti?"* — yani routing **sınıflandırma** testidir. Sürücü bu işi yedi adımda (§3 A-G)
yapar: önce senaryo yazılır, sonra girdiyi besleyen fixture hazırlanır, ajan çalıştırılır,
çıktı ölçülür, ölçüm bir eşikle PASS/FAIL'e çevrilir, başarısızlıkta en fazla 3 kez yeniden
denenir, sonuç raporlanır.

> **eli10:** "Test geçti" demek, tek bir cümlede karşılığı bulunan ajanın registry'deki
> birincil ajanla **birebir aynı** olması demektir. Farklıysa FAIL — süre/kalite yorumu
> sonradan, **eşleşme hatası anında**.
>
> **eli15:** Test bir *yazılım* testi gibi çalışır ama girdisi **prompt**'tur, çıktısı
> **ajan adıdır**. Bu yüzden `$this->assertSame(200, $result['status'])` yerine
> `$this->assertSame('Security Engineer', $routed)` yazılır; altyapı aynı (PHPUnit,
> fixture, retry), sadece assert hedefi farklıdır.

## §2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Test akışı A-G: senaryo → fixture → çalıştırma → ölçüm → PASS/FAIL → retry → rapor (§3-§10) | Ajanın **iç** düşünme zinciri (chain-of-thought) denetimi |
| Routing testi: §6 9 keyword grubunun her biri için gerçek senaryo (§11-§13) | Kod testi yazımı (o iş QA Engineer'ın `shared/tests/` alanıdır) |
| Regresyon: vault/registry değişince routing davranışı korunuyor mu (§14-§16) | PHPUnit test altyapısının kurulması (zaten kurulu — §17) |
| Hata modları: flaky · sahte PASS · production'a dokunma · mojibake (§26) | Model kalitesi benchmark'ı (LLM-as-a-judge — §33/3 notu) |
| Raporlama yeri + token bütçesi + doğrulama kapıları (§10, §27-§30) | `.ai/scripts/**`, `shared/**`, `.ai/*.md` yazımı (**salt-okunur**) |

- **Kullananlar:** routing testini yürüten subagent · MO (regresyon kapısı) · QA Engineer (rapor).
- **Ön koşul:** `.ai/AGENTS.md` §6 tablosu diskte mevcut ve 9 satırdır (`Satır: 234-242`).
- **Kanıt standardı:** her iddia `Dosya:` + `Satır:` künyelidir; web iddiası §33'te URL'li.

## §3. Test Akışı A-G (genel bakış)

```text
[A] Test senaryosu   → keyword seti + beklenen ajan + beklenen ikincil yazılır
[B] Fixture          → girdi metni, boot durumu, vault snapshot'ı hazırlanır (salt-okunur)
[C] Ajan çalıştırma  → routing kararı alınır (§6 tablosu + §7.1 analiz adımı)
[D] Çıktı ölçümü     → üretilen ajan adı + ikincil + verilen künye sayısal/metrikse çevrilir
[E] PASS/FAIL        → eşik tablosu (§8) uygulanır: birebir eşleşme mi?
[F] Retry            → FAIL ise max 3 yeniden deneme (§9); 3'te de başarısız → escalation
[G] Rapor            → sonuç nereye yazılır (§10) + kapanış listesi (§30)
```

| Adım | Çıktı | Karar ağacı | Kanıt |
|---|---|---|---|
| **A** | `TestCase[]` (id, girdi, beklenen_birincil, beklenen_ikincil) | girdi var mı → yoksa senaryo üretilir (§4) | `Dosya: .ai/AGENTS.md · Satır: 234-242` |
| **B** | fixture dosyası (prompt metni + boot durumu) | boot'ta okunan dosya var mı → tekrar okuma (§5) | `Dosya: kök AGENTS.md · Satır: 273-282` |
| **C** | `{routed_primary, routed_secondary, evidence}` | §6 tablosuna birebir satır eşleşmesi (§6) | `Dosya: .ai/AGENTS.md · Satır: 232-243` |
| **D** | skor: `match_primary` · `match_secondary` · `cited` · `latency` | metrik üretimi — bellekten sayı yok (§7) | `Dosya: .ai/RAG.md · Satır: 105` |
| **E** | `PASS` / `FAIL` | eşik tablosu (§8) — eşik aşılmazsa FAIL | `Dosya: .ai/AGENTS.md · Satır: 302-305` |
| **F** | `retry_count ≤ 3` | 3 FAIL → **DUR** + escalation (§9) | `Dosya: .ai/AGENTS.md · Satır: 201, 321` |
| **G** | rapor satırı (§10 şablonu) | sonucun yazılacağı yer seçilir | `Dosya: .ai/AGENTS.md · Satır: 309-312` |

**Adım [F] kanıtı (retry sınırı):** `Dosya: .ai/AGENTS.md · Satır: 201` → "Ajan timeout (30s+)
→ Max 3 retry, sonra queue reset" · `Satır: 321` → "Önceki görev başarısız mı? → Retry /
escalation → Max 3 retry". Test tarafı da aynı 3 sayısını kullanır — **test, üretim
retry sayısını taklit eder** (farklı sınır koymak flaky yaratır, §26/1).

**Adım [E] kanıtı (5 satırlık verification tablosu):** `Dosya: .ai/AGENTS.md · Satır: 300-305` —
başlık `Satır: 300`, ayraç `Satır: 301`, 4 kontrol satırı `Satır: 302-305`:
`Çıktı formatı` · `Domain uyumluluğu` · `Cross-reference` · `Security`.

## §4. A · Test Senaryosu Üretimi (karar ağacı)

| # | Durum | Ne yazılır | Çıktı |
|---|---|---|---|
| A1 | Keyword seti verilmiş (ör. "CSRF + middleware") | §6 3. satırdan beklenen birincil + ikincil türetilir | 1 test case |
| A2 | Yalnızca doğal cümle verilmiş ("şu sayfada login bozuk") | 3-5 keyword çıkarılır → §6'da eşleşen **ilk** satır | 1 test case |
| A3 | Birden çok gruba giren cümle (hem `test` hem `CI/CD`) | 2 beklenen aday yazılır; birincil = satır sırası **üstteki** | 1 + 1 adaylı test |
| A4 | §6'da hiç eşlenmeyen keyword | Beklenen = `MO` (dağıtıcı) — **uydurulan ajan yazılmaz** | negatif test |
| A5 | Şablon/`template` keyword'ü | Beklenen = "Tüm ajanlar (guardrail #16)" + MO koordinasyon | özel test |
| A6 | Boş/çok kısa girdi (`"a"`) | Beklenen = `⚠️ VERIFICATION REQUIRED` / tek soru — FAIL değil **DUR** | sınır testi |

**Senaryo yazım kuralı (A-satırı):** her test case dört alanlıdır ve **bir** §6 satırına
gider; iki satıra birden giden senaryo yazılır ama `A3` gibi **adaylı** işaretlenir —
aksi hâlde regresyon koşusunda belirsiz FAIL oluşur (§26/4).

**Kaynak:** `Dosya: .ai/AGENTS.md · Satır: 232-242` (9 satır) · `Dosya: .ai/AGENTS.md · Satır: 263`
("Öncelik belirleme" → `Dosya: .ai/AGENTS.md · Satır: 259-267` §7.1 Analysis tablosu:
"Keyword çıkarma → Routing tablosuna başvur → Bu dosya §6").

## §5. B · Fixture Hazırlığı

Fixture = testin girdisi + **o anki vault durumu**. Routing testi bozulmasının en yaygın
nedeni fixture'ın bayat olmasıdır.

| # | Fixture parçası | Ne korunur | Kaynak |
|---|---|---|---|
| B1 | Test metni (prompt) | keyword'ler tam ve doğru yazım (mojibake'siz) | §26/4 |
| B2 | Boot durumu | boot'ta okunmuş dosyalar **yeniden okunmaz** (token israfı yok) | `Dosya: kök AGENTS.md · Satır: 282` |
| B3 | Vault snapshot'ı | `.ai/AGENTS.md` §6 satır sayısı **9** ve sıra korunur | `Satır: 234-242` |
| B4 | Kırık-link istisnası | 6 bilinen sahte kırık link **"onarılmaz"** (fixture'a dahil) | `Dosya: .ai/AGENTS.md · Satır: 145` |
| B5 | Ortam | repo **kökünden** çalıştırılır (`C:\www\coremusic.net`) | `Dosya: .ai/AGENTS.md · Satır: 144` |
| B6 | Yazma izni | fixture üretimi **salt-okunur** kaynaklardan okur | `Dosya: .ai/AGENTS.md · Satır: 171` |

**B5 kritik:** `.ai/scripts/*.ps1` doğrulama betikleri `.ai/` içinden çalıştırılırsa
`screens-frontmatter-check.ps1` göreli yolları çözemez ve **19 adet sahte "PNG yok"** üretir
(`Dosya: .ai/AGENTS.md · Satır: 144`). Test harness'ı CWD'yi repo köküne sabitler; CWD
farklıysa koşu **geçersiz sayılır** (§26/5).

**B4 kritik:** `flow/auth/01-login.md [[C]]` gibi 6 hedef şablon değişkenidir; fixture bunu
"gerçek kırık link" sayıp düzeltmeye kalkarsa **test, kaynağı bozar** — bu, §26/3
(production'a dokunma) sınıfının en sinsi biçimidir.

## §6. C · Ajan Çalıştırma

| # | Adım | Ne yapılır | Çıktı |
|---|---|---|---|
| C1 | Keyword çıkarma | girdiden §6'daki anahtar kelimeler taranır (exact/substring) | `hits[]` |
| C2 | Satır eşleşmesi | en üst eşleşen satır = birincil beklenti | `expected_primary` |
| C3 | İkincil beklenti | §6 3. sütun okunur (`—` ise ikincil yok) | `expected_secondary` |
| C4 | Gerçek routing | orkestratör/kurallar motoru ajanı seçer | `actual_primary` |
| C5 | Süreç kaydı | hangi adımda hangi ajan seçildi + hangi künye | `trace` |

**C1-C3 sabittir** (registry'den okunur); **C4 testin özüdür** (sistemin verdiği karar).
Test, C4'ü **C2 ile karşılaştırır** — bu, "gold label" (ground truth) karşılaştırmasıdır
(`Dosya: .ai/AGENTS.md · Satır: 263` → "Ajan seçimi | Birincil + ikincil ajan | Bu dosya §6`).

> **Önemli ayrım:** §6 tablosu **salt-okunur kaynaktır** (testin altın cevabı). Sistem
> çıktısı ise **değişkendir**. İkisini karıştıran test, kendini doğrular — sahte PASS'ın
> 1 numaralı kaynağıdır (§26/2).

## §7. D · Çıktı Ölçümü

| Metrik | Tanım | Tür | Hedef |
|---|---|---|---|
| `match_primary` | birincil ajan birebir eşleşti mi | **bool** (deterministik) | `true` |
| `match_secondary` | ikincil ajan eşleşti mi (`—` ise N/A) | bool | `true` / N/A |
| `cited` | çıktı `Dosya:` + `Satır:` künyesi taşıdı mı | bool | `true` |
| `flagged` | bilinmeyen için `⚠️ VERIFICATION REQUIRED` yazdı mı | bool | `true` (yalnız negatif testte) |
| `latency_ms` | karar süresi | sayı | `≤ eşik` (§8) |
| `tokens_in` | girdi token'ı | sayı | bütçe §27 |
| `route_from` | boot'tan mı yoksa taramadan mı karar verildi | kategori | izlenir, FAIL değil |

**Ölçüm kuralı:** bool metrikler **kodlayıcıdır** (code evaluator) — LLM-judge'a
**gerek yoktur**; routing eşleşmesi exact match olduğu için deterministik kontrol en ucuz ve
en güvenilir yöntemdir. Sayısal metrikler (latency/token) yalnız ölçümle yazılır,
bellekten sayı yazılmaz (`Dosya: .ai/RAG.md · Satır: 105`).

**Eşik yerleştirme:** `.ai/AGENTS.md §16` QA standardı "Test coverage ≥80%, **flaky test %0**"
(`Satır: 580`) — yani routing testlerinde **tek bir flaky bile kabul edilmez**.

## §8. E · PASS/FAIL Karar Tablosu

| # | Koşul | Sonuç | Not |
|---|---|---|---|
| E1 | `match_primary = true` **ve** `cited = true` | **PASS** | tam kapıdır |
| E2 | `match_primary = true`, `cited = false` | **FAIL** (künye eksik) | `Dosya: .ai/AGENTS.md · Satır: 304` (Cross-reference) |
| E3 | `match_primary = false` | **FAIL** (yanlış ajan) | `Satır: 303` (Domain uyumluluğu) |
| E4 | İkincil `—` iken ikincil üretildi | **FAIL** (kapsam aşımı) | §6 3. sütun `—` |
| E5 | Girdi A4 sınıfı (§6'da yok) → ajan uydurdu | **FAIL** (hallüsinasyon) | `Satır: 219` — `VERIFICATION REQUIRED` etiketi |
| E6 | Girdi A6 (boş/çok kısa) → `⚠️ VERIFICATION REQUIRED` | **PASS** | dürüst durum davranışı |
| E7 | `latency_ms > eşik` ama eşleşme doğru | **WARN** (FAIL değil) | `Satır: 419` Degraded 301 |
| E8 | Çıktıda hassas veri (`[REDACTED]` değil) | **FAIL** (güvenlik) | `Satır: 305` (Security) |

**§7.6 Verification tablosunun test karşılığı** (`Dosya: .ai/AGENTS.md · Satır: 302-305`):

| # | Registry kontrolü (Satır) | Test karşılığı | Sonuç alanı |
|---|---|---|---|
| 1 | `Çıktı formatı` (302) | çıktı şablonu + `Dosya:/Satır:` künyesi | E2 |
| 2 | `Domain uyumluluğu` (303) | birincil ajan eşleşmesi | E3 |
| 3 | `Cross-reference` (304) | wiki-link hedefi diskte (`wiki-link-check`) | E2 |
| 4 | `Security` (305) | hassas veri redaction | E8 |

## §9. F · Retry (max 3) — karar ağacı

| Durum | Aksiyon | Sınır | Sonuç |
|---|---|---|---|
| İlk FAIL (E2/E3/E4/E8) | aynı senaryo **yeniden** çalıştırılır | 1 | tekrar ölç |
| 2. FAIL | fixture bayat mı kontrol (B3 snapshot) | 2 | fixture tazelenir, yeniden |
| 3. FAIL | **DUR** — şüpheli varsayım yazılır, üst karar istenir | 3 | escalation |
| Aynı hata 3 tur tekrar | test **FLAKY değil BROKEN** sayılır | — | regresyon kaydı (§10) |
| Timeout (30s+) | `408 Retry` → `500 Failed` → `503 Dead` | §11.2 | `Satır: 416-422` |

**Sınır kanıtı:** `Dosya: .ai/AGENTS.md · Satır: 201` ("Max 3 retry, sonra queue reset") ·
`Satır: 396` ("Max retry | 3 her seviyede") · `Satır: 217` ("Agent timeout 30s+ → Max 3 retry,
sonra escalation"). **Test retry'sı da 3'tür** — 3'ten fazla denemek veri gömer.

**Yasak:** retry sırasında beklenti (`expected_primary`) **değiştirilemez**. Beklentiyi
FAIL sonrası güncellemek = testi sonuca uydurmak = **sahte PASS** (§26/2).

## §10. G · Test Sonrası Raporlama (sonuç nereye yazılır?)

| Sonuç sınıfı | Nereye yazılır | Format | Yazmaz |
|---|---|---|---|
| **PASS** (hepsi) | subagent çıktısı — §34 gate satırı | `routing PASS => 9/9` | `log.md` (gürültü) |
| **FAIL — yeni bulgu** | **`open_questions`** (bu dosya FM) + orkestratöre rapor | `Q# · soru · ⚠️ VERIFICATION REQUIRED` | `MEMORY.md` (detay) |
| **FAIL — bilinen/istisna** | §26 hata modu satırı (istisna listesi) | `# · semptom · davranış` | yeni dosya |
| **Regresyon** (vault değişince bozuldu) | orkestratöre **DUR** raporu + üst karar | `eski → yeni` çift kanıt | sessiz düzeltme |
| **Session özeti** | `.ai/SESSIONS/{id}.md` + `MEMORY.md` (session state) | özet satırı | tam test dökümü |
| **Audit trail** | `log.md` — yalnız **append-only** giriş | tek satır | geçmiş satıra dokunma |

**Nerede yazılmaz (yasağı):** `shared/**` (test production kodudur, QA Engineer alanı) ·
`.ai/scripts/**` (salt-okunur betikler) · `.ai/*.md` kök dosyalar (MO alanı) · bu dosya
**dışına** yeni `.md` (kapsam dışı yazım ihlali).

**Kaynak:** `Dosya: .ai/AGENTS.md · Satır: 309-312` (§7.7 Completion: "Görev tamamlanır ·
`log.md`'ye giriş eklenir · MEMORY.md session state güncellenir · Gerekirse vault-sync yapılır").

## §11. Routing Testi Akışı — 9 keyword grubu (her biri için gerçek senaryo)

> **Kaynak tablo (bağlayıcı):** `Dosya: .ai/AGENTS.md · Satır: 232-242` — başlık `Satır: 230`,
> ayraç `Satır: 231`, **9 veri satırı `Satır: 232-242`** (§6 `Keyword → Agent Routing`).
> Aşağıdaki "Beklenen birincil/ikincil" sütunları bu satırların **birebir kopyasıdır**;
> "Test senaryosu" sütunu ise **bu sürücünün önerdiği girdidir** (kanıt değil, senaryo).

### §11.1 — Grup 1: API / endpoint / routing / middleware / PHP

| Alan | Değer |
|---|---|
| Keyword (Satır: 234) | `API, endpoint, routing, middleware, PHP, controller, repository` |
| Test senaryosu (girdi) | "Yeni bir API endpoint ekle, middleware pipeline'a kaydet" |
| Beklenen birincil | **Backend Architect** |
| Beklenen ikincil | **Security Engineer** |
| Doğrulama | `match_primary` + ikincil `Security` beklenir; `Dosya: .ai/AGENTS.md · Satır: 234` |

### §11.2 — Grup 2: CSS / UI / responsive / ITCSS / token

| Alan | Değer |
|---|---|
| Keyword (Satır: 235) | `CSS, UI, responsive, accessibility, ITCSS, BEM, frontend, design, JS, mockup, ui-design, c01-c16, 45-tier, device-matrix, screen-spec, token, glassmorphism` |
| Test senaryosu (girdi) | "1024 breakpoint'te widget gridini 2×2 / 1×5 / 1×5 yap, ITCSS tokenlarını güncelle" |
| Beklenen birincil | **UI Designer** |
| Beklenen ikincil | **QA Engineer** |
| Doğrulama | çok keyword'lü en uzun satır — **substring eşleşmesi** zorunlu; `Satır: 235` |

### §11.3 — Grup 3: CSRF / CSP / XSS / OWASP / auth

| Alan | Değer |
|---|---|
| Keyword (Satır: 236) | `CSRF, CSP, XSS, OWASP, auth, encryption, security, session, rate limit` |
| Test senaryosu (girdi) | "CSRF token doğrulamasını `hash_equals` ile yap, CSP header'ını sıkılaştır" |
| Beklenen birincil | **Security Engineer** |
| Beklenen ikincil | **Backend Architect** |
| Doğrulama | negatif aday: `auth` aynı zamanda §6/1'de `endpoint` ile birlikte geçmez → bu satır kazanır; `Satır: 236` |

### §11.4 — Grup 4: database / SQL / BCNF / migration

| Alan | Değer |
|---|---|
| Keyword (Satır: 237) | `database, SQL, BCNF, migration, query, schema, MySQL, PDO, index` |
| Test senaryosu (girdi) | "`oauth_connections` tablosunu 3NF'ten BCNF'ye taşı, migration yaz" |
| Beklenen birincil | **Data Engineer** |
| Beklenen ikincil | **Backend Architect** |
| Doğrulama | `Satır: 237` · kaçak test: `index` kelimesi §6/2 `screen-spec` satırında yok → bu satır |

### §11.5 — Grup 5: C++ / ASIO / JUCE / audio / DSP

| Alan | Değer |
|---|---|
| Keyword (Satır: 238) | `C++, ASIO, JUCE, audio, DSP, Neva Engine, ring buffer, WASAPI, hardware` |
| Test senaryosu (girdi) | "Neva Engine ring buffer'ında ASIO cihaz kaybını WASAPI fallback ile karşıla" |
| Beklenen birincil | **Embedded Engineer** |
| Beklenen ikincil | **DevOps Engineer** |
| Doğrulama | `Satır: 238` · ikincil `DevOps` — QA **değil** (§11.6 ile karıştırılmamalı) |

### §11.6 — Grup 6: test / coverage / PHPUnit / Vitest / Playwright

| Alan | Değer |
|---|---|
| Keyword (Satır: 239) | `test, coverage, PHPUnit, Vitest, Playwright, E2E, unit test, integration` |
| Test senaryosu (girdi) | "`shared/tests/` coverage oranını ölç, eksik testleri tamamla" |
| Beklenen birincil | **QA Engineer** |
| Beklenen ikincil | **—** (yok) |
| Doğrulama | ikincil `—` iken ikincil ajan üretildiyse **FAIL (E4)**; `Satır: 239` |

### §11.7 — Grup 7: CI/CD / GitHub Actions / deploy / pipeline

| Alan | Değer |
|---|---|
| Keyword (Satır: 240) | `CI/CD, GitHub Actions, deploy, infrastructure, pipeline, monitoring, GitLeaks` |
| Test senaryosu (girdi) | "`ci.yml`'a PHPUnit adımı ekle, secret-scan gate'ini koru" |
| Beklenen birincil | **DevOps Engineer** |
| Beklenen ikincil | **QA Engineer** |
| Doğrulama | `Satır: 240` · repo kanıtı `.github/workflows/` = 2 dosya (`Satır: 630`) |

### §11.8 — Grup 8: vault / documentation / ADR / wiki-link

| Alan | Değer |
|---|---|
| Keyword (Satır: 241) | `vault, documentation, ADR, wiki-link, index, keys, brain` |
| Test senaryosu (girdi) | "Yeni vault dosyasını `index.md`'ye ekle, wiki-link kontrolünü çalıştır" |
| Beklenen birincil | **MO (vault-updater)** |
| Beklenen ikincil | **—** (yok) |
| Doğrulama | `Satır: 241` · **dikkat:** `index`/`keys` kelimeleri ayrıca retrieval testidir (ayrı sürücü) |

### §11.9 — Grup 9: template / şablon / .templates (guardrail #16)

| Alan | Değer |
|---|---|
| Keyword (Satır: 242) | `template, şablon, şablon, template usage, .templates` |
| Test senaryosu (girdi) | "Bu ekran için Kalıp D screen-spec şablonunu kullan" |
| Beklenen birincil | **Tüm ajanlar (guardrail #16)** |
| Beklenen ikincil | **MO (koordinasyon)** |
| Doğrulama | tek "tüm ajanlar" satırı — `match_primary` **string containment** ile kontrol edilir (birebir eşitlik değil); `Satır: 242` |

### §11.10 — Özet: 9/9 koşu tablosu

| # | Grup | Beklenen birincil | İkincil | Senaryo var | Kapı |
|---|---|---|---|---|---|
| 1 | API/PHP | Backend Architect | Security Engineer | §11.1 | PASS/FAIL |
| 2 | CSS/UI | UI Designer | QA Engineer | §11.2 | PASS/FAIL |
| 3 | Security | Security Engineer | Backend Architect | §11.3 | PASS/FAIL |
| 4 | Data | Data Engineer | Backend Architect | §11.4 | PASS/FAIL |
| 5 | Embedded | Embedded Engineer | DevOps Engineer | §11.5 | PASS/FAIL |
| 6 | QA | QA Engineer | — | §11.6 | PASS/FAIL |
| 7 | DevOps | DevOps Engineer | QA Engineer | §11.7 | PASS/FAIL |
| 8 | Vault | MO (vault-updater) | — | §11.8 | PASS/FAIL |
| 9 | Template | Tüm ajanlar (guardrail #16) | MO (koordinasyon) | §11.9 | PASS/FAIL |

**Kapı:** `9/9 PASS` olmadan tamamlanmış sayılmaz; `8/9` regresyon demektir (§28 kapı 5).

## §12. Routing Testi — Doğrulama Adımları (§7.6 + §7.1)

| # | Doğrulama | Yöntem | Kaynak künye |
|---|---|---|---|
| 1 | Testin beklediği satır §6'da gerçekten var mı | `Select-String` / satır indeksi | `Dosya: .ai/AGENTS.md · Satır: 234-242` |
| 2 | Satır sayısı hâlâ **9** mu (satır eklenip silinmedi mi) | `ReadAllLines` + section tarama (§19/B) | `Satır: 232-242` |
| 3 | Keyword yazımı mojibake'siz mi (`şablon` doğru karakterlerle) | bayt/karakter denetimi | §26/4 |
| 4 | İkincil sütun `—` mü, ajan mı | `Satır: 239, 241` | §11.6, §11.8 |
| 5 | Çıktı `Dosya:`/`Satır:` künyeli mi | regex `Dosya: .+ · Satır: \d+` | `Dosya: .ai/AGENTS.md · Satır: 302-305` |

**§7.1 Analiz tablosu** (`Dosya: .ai/AGENTS.md · Satır: 263`) testin de yöntemidir:
`Keyword çıkarma | Routing tablosuna başvur | Bu dosya §6`.

## §13. Routing — Negatif ve Çakışma Senaryoları

| # | Senaryo | Beklenen | Neden |
|---|---|---|---|
| N1 | §6'da olmayan keyword ("kuantum") | `MO` dağıtımı **veya** `⚠️ VERIFICATION REQUIRED` | uydurma ajan yasak |
| N2 | Aynı cümlede `test` + `deploy` | satır sırası: **QA (Satır: 239)** önce gelir → adaylı test | A3 kuralı |
| N3 | `template` + `CSS` birlikte | Guardrail #16 satırı **önce** okunmalı mı? → **belirsiz = adaylı** | §26/4 |
| N4 | İkincil `—` iken ikincil üretim | **FAIL E4** | kapsam aşımı |
| N5 | Boş girdi | `⚠️ VERIFICATION REQUIRED` → **PASS E6** | dürüstlük |
| N6 | Mojibake girdi (`ÅŸablon`) | **FAIL (fixture hatası)** — test değil, fixture düzeltilir | §26/4 |
| N7 | Çıktı künyesiz | **FAIL E2** | `Satır: 304` |

## §14. Regresyon Testi — Vault/Registry değişince davranış korunuyor mu?

Regresyon testi, **kod değil vault** değiştiğinde çalışır. Soru şudru: `.ai/AGENTS.md`
(registry) veya boot dosyaları editlendikten sonra, §11'deki 9 senaryonun beklenen cevabı
**hâlâ doğru mu**?

| # | Değişiklik sınıfı | Regresyon riski | Test |
|---|---|---|---|
| R1 | §6'ya satır eklendi/çıkarıldı | 9 sayısı bozulur → tüm senaryolar kayar | §19/B satır sayacı |
| R2 | §6 satır **sırası** değişti | N2/N3 çakışma kararları değişir | sıra hash'i |
| R3 | Bir ajan **adı** değişti (ör. `MO (vault-updater)` → `vault-updater`) | birebir eşleşme kırılır | §19/C isim taraması |
| R4 | Boot listesi değişti (§25.4 farkı) | "boot'ta zaten okundu" adımı yanlış çalışır | §15 |
| R5 | `.ai/index.md` / `keys.md` taşındı | wiki-link kırılır → E2 FAIL | `wiki-link-check.ps1` |
| R6 | `shared/tests/` dosya sayısı değişti | envanter iddiası bayatlar (§17) | yeniden sayım |
| R7 | Mojibake temizliği (vault geneli 745 düzeltme) | keyword karakterleri değişir | §26/4 |

**Temel prensip:** regresyon testi **eski beklentiyi saklar** (baseline snapshot) ve
yeni vault ile karşılaştırır. Beklenti, vault ile **eşzamanlı güncellenirse** test hiçbir
zaman regresyonu yakalayamaz — bu, §26/2 sahte PASS'inin vault versiyonudur.

## §15. Regresyon — §25.4 Boot Farkı Örneği (gerçek, diskteki bilinen çelişki)

`Dosya: .ai/AGENTS.md · Satır: 642` (§25.4 Boot List Compatibility) şunu **açıkça bilinen
durum** olarak kaydeder:

| Liste | Sayı | Kaynak |
|---|---|---|
| §24.2 kök boot listesi | **14 dosya** | `Dosya: .ai/AGENTS.md · Satır: 503-517` |
| `MEMORY.md §5` genişletilmiş okuma | **20 adım** | `Satır: 642` (fark "bilinen durumdur") |
| FULL boot | **17 öğe** | `Satır: 642` (root CLAUDE + root WORKFLOW + root README + 14 .ai kök + 3 dizin) |
| `.ai/CLAUDE.md §16` kanonik | **13 = 14 − 1** | `Dosya: .ai/CLAUDE.md · Satır: 630, 648` |

**Regresyon testi bu farkı "onarmamalıdır":**

| # | Yanlış davranış | Doğru davranış |
|---|---|---|
| 1 | Test, 14 ≠ 20'yi **FAIL** sayıp boot listesini "düzeltmeye" çalışır | Test bu farkı **bilinen istisna** olarak işaretler (baseline = 14/20/17) |
| 2 | Test, 13 = 14 − 1 sayım zincirini bozar | Zincir bozulursa **DUR** (`Dosya: .ai/CLAUDE.md · Satır: 648`) |
| 3 | Vault'ta satır sayıları değişince test sessizce yeni sayıyı kabul eder | Yeni sayı **kanıtla** (ReadAllLines) ve fark **raporlanır**, sessizce yutulmaz |
| 4 | Boot listesine testin/kodun okuduğu dosya **eklenir** | Boot listesi bu sürümle **değiştirilemez** (`Dosya: .ai/RAG.md · Satır: 136`) |

**Sonuç:** regresyon = "eski davranış korunuyor mu" sorusunun **baseline ile** sorulması;
vault değiştiyse baseline **bilinçli** güncellenir ve bu güncelleme bir kayıttır
(§10 raporlama), sessiz bir edit değil.

## §16. Regresyon — Vault Değişikliği Sınıflandırması

| Sınıf | Örnek | Test etkisi | Eylem |
|---|---|---|---|
| **Güvenli** | yorum/typo düzeltmesi | 0 | koş |
| **Routing etkili** | §6 satırı eklendi | 9 → 10 | senaryo ekle + baseline güncelle |
| **İsim etkili** | ajan kod adı değişti | birebir eşleşme kırılır | §11 tablosunu güncelle (raporla) |
| **Sıra etkili** | §6 satırları yeniden sıralandı | çakışma kararları değişir | §13 adaylı testleri yeniden değerlendir |
| **Bozucu** | §6 satırı silindi / tablo başlığı yok | koşulamaz | **DUR** + üst karar |
| **Görünmez** | `.ai/` gizli dizin araması `hidden:false` | sahte "0 sonuç" | `hidden: true` ile tekrar (§26/5) |

## §17. `shared/tests/` Envanteri (gerçek ölçüm — 2026-10-17)

> ✅ **Ölçüm:** `Get-ChildItem -Recurse -File` · **toplam 57 dosya** · **41 `*Test.php`** ·
> **19 dizin** · uzantı: `.php` **45** · `.md` **8** · `.pem` **4**.
> Üst düzey **9 dizin**: `Api · Component · Events · Fixtures · Middleware · OAuth ·
> Repository · Security · Unit`.

| Üst dizin | `*Test.php` | Not |
|---|---|---|
| `Unit/` | **16** | alt: PageRouter 6 · Device 4 · Config 1 · Log 2 · Security 2 · Theme 1 · Middleware 0 |
| `Middleware/` | **9** | CSRF, Auth, RateLimiter, Origin, Pipeline, BypassAuth (×2), Session, Validation |
| `Api/` | **8** | RouteTable, GatewayMethodRouting, Versioning, Dto, ApiResponse, ... |
| `Events/` | **2** | EventDispatcher, DomainEvent |
| `Repository/` | **2** | ApiKeyRepository, ApiKeyCreateCommand (+ 2 `SqliteDatabase*` yardımcı sınıfı) |
| `Security/` | **2** | SessionKeys, ReturnUrlPolicyEdgeCase |
| `Component/` | **1** | TemplateEngine |
| `OAuth/` | **1** | OAuthManager |
| `Fixtures/` | **0** | 4 `.pem` + 2 `chrome/*.php` (fixture, test değil) |
| **Toplam** | **41** | 9 dizin / 57 dosya |

**Test türü:** hepsi **PHPUnit unit testi** (`shared/phpunit.xml` → 8 testsuite:
`Unit · Api · Repository · Events · Security · Middleware · Component · OAuth`;
`bootstrap="vendor/autoload.php"`; `source/include = src`). `composer.json` →
`"phpunit/phpunit": "^10.5"` · script `"test": "phpunit"` (`Dosya: shared/composer.json · Satır: 24, 47`).

**Çelişki kaydı (kapatılmaz, işaretlenir):** registry `Dosya: .ai/AGENTS.md · Satır: 629`
"`shared/tests/` **22 dosya**" der; bugün diskte **57 dosya / 41 test** vardır. İki sayı da
**gerçektir ama farklı tarihlidir** → eski sayım, güncel ölçümün yerine geçmez;
`⚠️ VERIFICATION REQUIRED` ile işaretlenir ve §31/Q1'e konur.

**Kapsam notu:** `tests/` dizini **QA Engineer** alanıdır
(`Dosya: .ai/AGENTS.md · Satır: 103` — `tests/**/*.php` → QA Engineer). Bu sürucü o dosyalara
**yazmaz**; yalnız okur (§29).

## §18. PowerShell A — `wiki-link-check.ps1` (gerçek betik, künyeli alıntı)

> ✅ **Gerçek betik alıntısı** — `Dosya: .ai/scripts/wiki-link-check.ps1 · Satır: 1-43` (43 satır,
> tamamı okundu 2026-10-17). Aşağıdaki bloklar dosyadan **birebir** alınmıştır.
> **Yalnızca repo kökünden** çalıştırılır (`Dosya: .ai/AGENTS.md · Satır: 144`).

```powershell
#Requires -Version 5.1                                       # Satır 1
$ErrorActionPreference='Stop'                                # Satır 3
[Console]::OutputEncoding=[Text.Encoding]::UTF8             # Satır 4
$ROOT='C:\www\coremusic.net\.ai\ui-design'                   # Satır 5
```

**İndeks kurulumu — "dosya var mı" testinin temeli** (`Satır: 9-16`):

```powershell
$index=@{}                                                   # Satır 9
Get-ChildItem -Path 'C:\www\coremusic.net\.ai' -Recurse -Filter *.md -File | ForEach-Object {
  $base=$_.BaseName.ToLowerInvariant()                       # Satır 11
  $rel=$_.FullName.Substring(0,$_.FullName.Length-3)         # Satır 12
  if(-not $index.ContainsKey($base)){ $index[$base]=$_.FullName }  # Satır 13
  $index[$_.FullName.ToLowerInvariant()]=$_.FullName         # Satır 14
  $index[$rel.ToLowerInvariant()]=$_.FullName                # Satır 15
}
```

**Üç kademeli eşleşme + kırık raporu** (`Satır: 24-38`, `Satır: 41-43`):

```powershell
foreach($m in [regex]::Matches($text,'\[\[([^\]|#]+)(?:[#|][^\]]*)?\]\]')){   # Satır 24
    $t=$m.Groups[1].Value.Trim()                            # Satır 25
    if(-not $t){ continue }                                 # Satır 26
    $checked++                                              # Satır 27
    $k=$t.ToLowerInvariant()                                # Satır 29
    if($index.ContainsKey($k)){ continue }                  # Satır 30  <- 1) dosya anahtarı
    $cand=[System.IO.Path]::GetFullPath((Join-Path $dir ($t -replace '/','\')))  # Satır 32
    if(Test-Path $cand){ continue }                         # Satır 33  <- 2) göreli yol
    if(Test-Path ($cand+'.md')){ continue }                 # Satır 34
    $leaf=[System.IO.Path]::GetFileNameWithoutExtension(($t -replace '/','\'))  # Satır 36
    if($index.ContainsKey($leaf.ToLowerInvariant())){ continue }  # Satır 37 <- 3) dosya adı
    $broken.Add("$(Split-Path $src -Leaf) -> [[$t]]")       # Satır 38
}
"kontrol edilen link: $checked"                             # Satır 41
"kirik link: "+$broken.Count                                # Satır 42
```

**Test açısından anlamı (üç satır):**

| Betik davranışı | Test karşılığı | Künye |
|---|---|---|
| `$index` kurulumu (9-16) | **fixture**: vault dosya indeksi | `Satır: 9-16` |
| 3 kademeli `continue` zinciri (30/33/37) | **fallback sırası** — tek kademe isabetsizse FAIL değil, sonraki kademe | `Satır: 28-37` |
| `$broken` listesi + `40` limit (43) | **FAIL listesi** (rapor, §10) | `Satır: 38, 42-43` |

**Bilinen sonuç (2026-09-29 kök ölçümü):** `227 link / 6 raporlanan` = **gerçek kırık 0**
(6'sı `Dosya: .ai/AGENTS.md · Satır: 145` §13.7 sahte kırık link) — yani testin **6 istisnası
vardır ve bu 6 "onarılmaz"** (§5/B4).

## §19. PowerShell B — Routing Test Kalıbı (gerçek çalıştırılabilir)

> ✅ **Çalıştırılabilir PowerShell betiği** — repo kökünden (`C:\www\coremusic.net`) çalışır.
> Beklenen çıktılar 2026-10-17 §6 tablosuna göredir: `satir => 9` · `routing PASS => 9/9` ·
> `verification satir => 5`.

```powershell
# B) Routing testi: .ai/AGENTS.md §6 — 9 keyword grubu -> beklenen ajan eşleşmesi
$p = Join-Path (Get-Location) '.ai\AGENTS.md'
$lines = [IO.File]::ReadAllLines($p, [Text.Encoding]::UTF8)

# §6 bölümünü başlıktan bul (satır numarası sabitlenmez — başlık anahtardır)
$hdr = [Array]::IndexOf($lines, '### §6 Keyword → Agent Routing')
if ($hdr -lt 0) { throw 'FAIL: §6 basligi bulunamadi' }

$rows = @()
for ($i = $hdr + 1; $i -lt $lines.Count; $i++) {
    if ($lines[$i] -notmatch '^\|') { break }   # tablo bitince dur
    $rows += $lines[$i]
}
# ilk 2 satir baslik + ayiric -> geri kalan = veri satiri
$rows = $rows | Select-Object -Skip 2
"satir => {0}   (kapı: 9 => {1})" -f $rows.Count, ($rows.Count -eq 9)

# §6'daki TÜM ajan adları (testin altın listesi - registry'den türetildi)
$expect = @(
  'Backend Architect', 'UI Designer', 'Security Engineer', 'Data Engineer',
  'Embedded Engineer', 'QA Engineer', 'DevOps Engineer',
  'MO (vault-updater)', 'Tüm ajanlar (guardrail #16)'
)
$pass = 0
for ($i = 0; $i -lt $expect.Count; $i++) {
    if ($i -lt $rows.Count -and $rows[$i] -match [regex]::Escape($expect[$i])) { $pass++ }
    else { "FAIL satir {0}: beklenen -> {1}" -f ($i+1), $expect[$i] }
}
"routing PASS => {0}/9   (kapı: 9/9)" -f $pass
```

```powershell
# C) §7.6 Verification tablosu = 5 satır (baslik + ayiric + 4 kontrol)
$hdr2 = [Array]::IndexOf($lines, '#### §7.6 Step 6: Verification')
$v = @()
for ($i = $hdr2 + 1; $i -lt $lines.Count; $i++) {
    if ($lines[$i] -notmatch '^\|') { break }
    $v += $lines[$i]
}
"verification satir => {0}   (kapı: 5 => {1})" -f $v.Count, ($v.Count -eq 5)
$v | ForEach-Object { "  " + $_ }

# D) Mojibake false-negative taraması (§26/4): §6 satirinda bozuk karakter yok mu?
$s6 = $lines[($hdr+1)..($hdr+11)] -join "`n"
"mojibake hit => {0}   (kapı: 0)" -f ([regex]::Matches($s6, '[ÃÂ]').Count)
```

```powershell
# E) Regresyon baseline'i: §6 satir metninin hash'i (dizi degisti mi?)
$sec = ($rows -join "`n")
$hash = [BitConverter]::ToString(
          [Security.Cryptography.SHA256]::Create().ComputeHash(
            [Text.Encoding]::UTF8.GetBytes($sec))).Replace('-','')
"routing baseline sha256 => {0}" -f $hash
"  (onceki kosuyla ayni mi? -> aynisa R1/R2/R3 regression YOK)" 
```

**Çalıştırma sırası:** `B → C → D → E`. Herhangi biri `kapı` false verirse koşu
**FAIL** sayılır ve §9 retry'a girer (3 tur).

## §20. PowerShell C — Üretim Kapısı (bu dosyanın kendi ölçümü)

```powershell
# F) Bu dosyanın kapisi: >=500 satir + Authority son satir + FM 10 alan
$p  = '.ai\architecture\K015-AI\agent-testing\agent-testing-surucu.md'
$ln = [IO.File]::ReadAllLines((Join-Path (Get-Location) $p), [Text.Encoding]::UTF8)
"satir    => {0}   (kapı >=500: {1})" -f $ln.Count, ($ln.Count -ge 500)
$auth = '**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode'
"authority=> {0}" -f ($ln[$ln.Count - 1] -eq $auth)
$pat = '^(node_id|node_type|status|parent_id|children|depends_on|evidence|validation|open_questions|group):'
"fm 10    => {0}" -f ((Select-String -Path $p -Pattern $pat).Count -eq 10)

# G) Kapsam sayaci: bu uretimde yazilan dosya TEK olmali
$changed = git status --porcelain -- '.ai/architecture/K015-AI/agent-testing/'
"degisen  => {0}   (kapı: 1)" -f $changed.Count
$changed | ForEach-Object { "  {0}" -f $_ }
```

## §21. PHP A — Repo Test Kanıtı (`shared/tests/` gerçek alıntılar)

> ✅ **Repo kanıtı** — aşağıdaki bloklar `shared/tests/` içinden **birebir** alınmıştır;
> satır sonu yorumları künyedir. Uydurma yok.

```php
<?php declare(strict_types=1);                                      // Satır 1
namespace CoreMusic\Test\Middleware;                                 // Satır 3
use PHPUnit\Framework\TestCase;                                     // Satır 5
use CoreMusic\Middleware\CsrfMiddleware;                             // Satır 6

final class CsrfMiddlewareTest extends TestCase                      // Satır 16
{
    protected function setUp(): void                                 // Satır 18
    { $_SESSION = []; }                                              // Satır 20
```

**Assert kalıbı — routing testinin PHP karşılığı** (`Dosya: shared/tests/Middleware/CsrfMiddlewareTest.php · Satır: 32-47`):

```php
    public function testGetRequestBypassesCsrf(): void               // Satır 32
    {
        $middleware = new CsrfMiddleware();                          // Satır 34
        $coreCalled = false;                                         // Satır 35

        $result = $middleware->handle(
            ['method' => 'GET', 'uri' => '/home'],                   // Satır 38
            function (array $req) use (&$coreCalled) {
                $coreCalled = true;
                return ['status' => 200];                            // Satır 41
            }
        );

        $this->assertTrue($coreCalled, 'GET requests should bypass CSRF');  // Satır 45
        $this->assertSame(200, $result['status']);                   // Satır 46
    }                                                                // Satır 47
```

**Routing'e en yakın gerçek test** (`Dosya: shared/tests/Api/GatewayMethodRoutingTest.php · Satır: 12-18, 33-39`):

```php
/**
 * Gateway + route tablosu entegrasyonu (Faz 1a):                     // Satır 13
 *   - `_route` pipeline array'ine aktarılır (Faz 2 notu #2)          // Satır 14
 *   - 405 (+ Allow) vs 404 ayrımı                                    // Satır 15
 *   - GET davranışları korunur                                       // Satır 16
 */                                                                    // Satır 17
final class GatewayMethodRoutingTest extends TestCase                 // Satır 18

    private function apiRouteTable(): RouteTable                      // Satır 33
    {
        $path = dirname(__DIR__, 3) . '/api.coremusic.net/config/routes.php';  // Satır 35
        $this->assertFileExists($path);                               // Satır 36
        return RouteTable::fromArray(require $path);                  // Satır 38
    }
```

**Önemli ayrım:** bunlar **HTTP route** testidir (`RouteTable`, `405/404`). Ajan routing'i
(§6 keyword → agent) **başka** bir katmandır ve aşağıda §22'de durumu vardır.

**Eşleme tablosu:**

| Repo davranışı | Sürümdeki karşılığı | Künye |
|---|---|---|
| `setUp/tearDown` ile ortam sıfırlama | §5 fixture hazırlığı (B-satırı) | `CsrfMiddlewareTest.php · Satır: 18-26` |
| `assertFileExists` (kaynak var mı) | §12/1 "satır §6'da gerçekten var mı" | `GatewayMethodRoutingTest.php · Satır: 36` |
| `assertSame(expected, actual)` | §8 `match_primary` | `CsrfMiddlewareTest.php · Satır: 46` |
| `assertTrue(..., 'açıklama')` | FAIL mesajı (§10 rapor satırı) | `CsrfMiddlewareTest.php · Satır: 45` |

## §22. PHP B — Ajan Routing: Repo Durumu (kod VAR · test YOK)

| İddia | Tarama | Sonuç |
|---|---|---|
| "`shared/tests/` içinde **ajan routing** (keyword → agent) testi var mı?" | `grep 'agent\|Keyword\|routing table'` (büyük/küçük harf **duyarlı**) → `shared/tests/**` | **0 isabet** |
| Aynı tarama, **duyarlılık kapalı** (`-i`) | `grep -i 'agent\|keyword'` → `shared/tests/**` | **22 isabet — 22'si de ilgisiz**: `HTTP_USER_AGENT` / `$rpiAgent` (DeviceDetectorTest) · `'TestAgent'` / `'Agent'` (EventDispatcherTest, DomainEventTest) · `.ai/AGENTS.md §17` yorumu (LoggerRedactionTest). **Hiçbiri keyword → ajan routing testi değil** → **Repo'da bu test kodu YOK** |
| "Repo'da ajan routing **motoru** kodu var mı?" | `grep -i 'agent'` → `shared/src/` | **VAR**: `shared/src/AI/AIOrchestrator.php` — `AGENT_MAP` (`Satır: 26-37`, **11 ajan**) + `TASK_TYPES` (`Satır: 40-48`) + `routeToAgent()` (`Satır: 125-133`) |
| "`routeToAgent()` için test var mı?" | `grep 'routeToAgent\|AGENT_MAP'` → `shared/tests/**` | **0 isabet — testi YOK** |
| Bilinmeyen ajan davranışı | kod okuma | `'error' => "Unknown agent: {$agentId}"` — `Satır: 131` · ayrıca `ToolCalling.php · Satır: 152` aynı mesajı üretir |

**Repo'da gerçekten var olan routing kodu** (`Dosya: shared/src/AI/AIOrchestrator.php · Satır: 26-37, 125-133`):

```php
    private const AGENT_MAP = [                                  // Satır 26
        'mo'         => ['domain' => 'coordination', 'layer' => 'coordination'],  // Satır 27
        'backend'    => ['domain' => 'php-api',     'layer' => 'l2-routing'],     // Satır 28
        'ui'         => ['domain' => 'frontend',    'layer' => 'l3-presentation'],// Satır 29
        'security'   => ['domain' => 'security',    'layer' => 'l1-security'],    // Satır 30
        'data'       => ['domain' => 'database',    'layer' => 'l0-infrastructure'], // Satır 31
        'embedded'   => ['domain' => 'audio-dsp',   'layer' => 'l0-electronics'], // Satır 32
        'qa'         => ['domain' => 'testing',     'layer' => 'cross-cutting'],  // Satır 33
        'devops'     => ['domain' => 'cicd',        'layer' => 'ci-cd'],           // Satır 34
        'audio-hw'   => ['domain' => 'hardware',    'layer' => 'hw'],              // Satır 35
        'dsp-fw'     => ['domain' => 'firmware',    'layer' => 'fw'],              // Satır 36
        'win-sw'     => ['domain' => 'platform',    'layer' => 'plat'],            // Satır 37

    public function routeToAgent(string $agentId, string $action, array $params): array  // Satır 125
    {
        if (!isset(self::AGENT_MAP[$agentId])) {                 // Satır 127
            return [                                              // Satır 128
                'success' => false,
                'result'  => null,
                'error'   => "Unknown agent: {$agentId}",         // Satır 131
            ];                                                    // Satır 132
        }
```

**Regresyon testi için hazır iki iddia (kanıtlanabilir, bugün koşulmamış):**

| # | Aday test | Beklenen | Kaynak |
|---|---|---|---|
| P1 | `AGENT_MAP` anahtar sayısı ve adları | **11 ajan** = registry §4 kod adları (`mo · backend · ui · security · data · embedded · qa · devops · audio-hw · dsp-fw · win-sw`) | `Satır: 26-37` ↔ `Dosya: .ai/AGENTS.md · Satır: 79-89` |
| P2 | Bilinmeyen ajan çağrısı | `success=false` + `Unknown agent:` (sessiz yok sayma yok) | `Satır: 127-132` |
| P3 | `TASK_TYPES['security-audit']` | `agent=security`, `priority=critical` | `Satır: 46` |
| P4 | `TASK_TYPES['testing']` | `agent=qa` (§6 grup 6 ile uyum) | `Satır: 48` |

> **Dürüst ayrım:** `routeToAgent()` **id ile** çalışır (`'security'`), §6 routing'i ise
> **keyword ile** (`CSRF, CSP, ...` → `Security Engineer`). İkisi **aynı katman değildir**;
> P1-P4 id-bazlı regresyondur, §11 senaryoları keyword-bazlıdır. İkisini birleştiren
> köprü (keyword → id) repo'da **görülmedi** → `⚠️ VERIFICATION REQUIRED` (§31/Q5).

**Sonuç cümlesi (dürüst):** *Repo'da ajan-routing **motoru** kodu var (`AIOrchestrator`),
ama **bu motorun testi ve keyword → ajan köprüsü YOK**.* §11'deki 9 senaryo bugün **elle**
(vault §6 tablosuna bakılarak) koşulur; PHPUnit'a taşınması §31/Q2'de açık sorudur.
Uydurma test dosyası üretilmez, varmış gibi yazılmaz.

## §23. İllüstratif — Python Ajan Fixture Runner

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
# §6 routing tablosunun satirlarini okuyup 9 senaryoyu kosturen mini runner
from dataclasses import dataclass

@dataclass
class Case:
    cid: str
    prompt: str
    expect_primary: str
    expect_secondary: str | None

def parse_routing(lines: list[str], header: str = "### §6 Keyword") -> list[list[str]]:
    i = lines.index(header)
    rows = []
    for line in lines[i + 1:]:
        if not line.startswith("|"):
            break
        rows.append([c.strip() for c in line.strip("|").split("|")])
    return rows[2:]                       # baslik + ayrici atlanir -> 9 veri satiri

def run(case: Case, route) -> str:        # route(prompt) -> (primary, secondary, evidence)
    primary, secondary, evidence = route(case.prompt)
    if primary != case.expect_primary:
        return f"FAIL {case.cid}: {primary!r} != {case.expect_primary!r}"
    if case.expect_secondary is None and secondary:
        return f"FAIL {case.cid}: ikincil '{secondary}' olmamaliydi"
    if "Dosya:" not in evidence:
        return f"FAIL {case.cid}: kunye yok"
    return f"PASS {case.cid}"
```

## §24. İllüstratif — Go Routing Assertion

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
package routingtest

import "strings"

// Expect, §6 tablosunun bir satirindan turetilen altın cevaptır.
type Expect struct {
    Row       int    // §6 satir numarasi (1..9)
    Primary   string
    Secondary string // bos ise "—" demek
}

// AssertPrimary: birebir eslesme (grup 9 icin containment - §11.9).
func AssertPrimary(c Case, e Expect) string {
    got := strings.TrimSpace(c.RoutedPrimary)
    want := strings.TrimSpace(e.Primary)
    if e.Row == 9 { // "Tüm ajanlar (guardrail #16)" satiri
        if !strings.Contains(want, got) && !strings.Contains(got, want) {
            return "FAIL " + c.ID + ": grup 9 containment saglanmadi"
        }
        return "PASS " + c.ID
    }
    if got != want {
        return "FAIL " + c.ID + ": got=" + got + " want=" + want
    }
    return "PASS " + c.ID
}
```

## §25. İllüstratif — TypeScript Regresyon Diff

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```typescript
type Snapshot = { section: string; sha256: string; rowCount: number };

/** Vault degisince routing davranisi korunuyor mu? (§14 R1-R3) */
export function regress(
  baseline: Snapshot,      // eski kosudan saklanan
  current: Snapshot,       // yeni okuma
): 'OK' | 'REGRESSED' | 'BASELINE_STALE' {
  if (current.rowCount !== baseline.rowCount) return 'REGRESSED';      // R1
  if (current.sha256 !== baseline.sha256) return 'REGRESSED';          // R2/R3
  // satir sayisi ayni ama metin farkli -> eski baseline bayat, guncelle + raporla
  return 'OK';
}
```

**Not:** bu üç blok **illüstratiftir** (repo'da karşılığı yok — §22); iskeletleri
§6 tablo yapısından (`Satır: 232-242`) ve §9 retry kuralından (`Satır: 201`) türetilmiştir.

## §26. Test Hata Modları (tespit → davranış)

| # | Hata modu | Semptom | **Tespit** | **Davranış** |
|---|---|---|---|---|
| 1 | **Flaky test** | aynı girdi bazen PASS bazen FAIL | 3 koşuda sonuç değişiyor (§9 tur sayacı) | Beklenti sabitlenir; değişkenlik **girdide** ise fixture tazelenir; hâlâ flaky ise test **kaldırılmaz, BROKEN işaretlenir** — `flaky test %0` hedefi (`Satır: 580`) |
| 2 | **Sahte PASS** | test geçiyor ama sistem yanlış | `expected` ile `§6` satırı **aynı anda** üretiliyor (test kaynağı üretim kaynağıyla aynı) | Golden değer **bağımsız** kaynaktan (vault snapshot) okunur; FAIL sonrası beklenti **değiştirilmez** (§9) |
| 3 | **Test production'a dokunma** | koşu `git status`'ta beklenmedik dosya gösteriyor | `git status --porcelain` → whitelist dışı dosya (§20/G) | Koşu **durur**, değişiklik raporlanır; `.ai/scripts/**`, `shared/**`, `.ai/*.md` **salt-okunur**; `git commit` **YASAK** (orkestratöre ait) |
| 4 | **Mojibake false-negative** | `ÅŸablon` gibi bozuk keyword → eşleşme yok → haksız FAIL | `[regex]::Matches($s6, '[ÃÂ]')` > 0 (§19/D) | Önce **fixture** düzeltilir; test **FAIL sayılmaz** — kaynak bozuksa `DUR` + rapor (§10) |
| 5 | **Gizli dizin unutulması** | `.ai/**` araması sahte "0 sonuç" | `hidden: false` kullanıldı mı? | Tarama `hidden: true` / `Force` ile tekrar (`Dosya: kök AGENTS.md · §7 adım 2`) |
| 6 | **Yanlış CWD** | `.ai/` içinden betik → 19 sahte "PNG yok" | CWD ≠ repo kökü | Koşu **geçersiz** sayılır, kökten tekrar (`Satır: 144`) |
| 7 | **Sahte kırık link** | `wiki-link-check` 6 raporluyor | §13.7 listesiyle eşleşme | **Onarılmaz** — 6'da kalır, kusur sayılmaz (`Satır: 145`) |
| 8 | **Token overflow** | koşu bütçe aşıyor | `tokens_in` > §27 bütçesi | Görev başarısız sayılır (`Satır: 220`) — test kapsamı daraltılır |
| 9 | **Bellekten sayı** | envanter iddiası ölçüm tarihsiz | `ReadAllLines` çalıştırılmamış | İddia `UNKNOWN` sayılır (`Dosya: .ai/RAG.md · Satır: 105`) |
| 10 | **3 başarısız düzeltme turu** | aynı hata tekrar | tur sayacı (§9) | **DUR** — şüpheli varsayım yazılır, üst karar istenir |

## §27. Token Ekonomisi (test bütçesi)

| # | Kural | Bütçe/sonuç | Kanıt |
|---|---|---|---|
| 1 | Test koşusu boot'taki dosyaları **yeniden okumaz** | çift israf yok | `Dosya: kök AGENTS.md · Satır: 282` |
| 2 | §6 tablosu **tek sefer** okunur, 9 senaryo için 9 kez değil | ~11 satır / koşu | `Satır: 232-242` |
| 3 | Hedef dosya `@` ile **yalnız ihtiyaç anında** okunur | en pahalı adım en sonda | `Satır: 282` |
| 4 | Rastgele okuma yasak — keys/routing kullan | asimetrik tarama yok | `Dosya: .ai/keys.md · Satır: 579` |
| 5 | Retry max 3 → en fazla 3× maliyet | sonsuz döngü yok | `Satır: 201` |
| 6 | Bool metrikler kodla ölçülür (LLM-judge yok) | judge token maliyeti **0** | §7 (code evaluator) |
| 7 | Tam koşu = 9 senaryo + 3 kapı (§19) | tek oturumda tamamlanır | §11.10, §20 |
| 8 | Token overflow → görev başarısız | bütçe aşımı sessiz geçilmez | `Satır: 220` |

## §28. Doğrulama Kapıları

| # | Kapı | Kriter | Yöntem / komut |
|---|---|---|---|
| 1 | Frontmatter | 10 alan eksiksiz | `Select-String` sayacı (§20/F) |
| 2 | Satır sayısı | `>= 500` | `[IO.File]::ReadAllLines(...).Count` (§20/F) |
| 3 | Authority son satır | birebir | `§20/F` eşitlik kontrolü |
| 4 | §6 satır sayısı | **9** | §19/B |
| 5 | Routing koşusu | **9/9 PASS** | §19/B (kapı 4+5 birlikte) |
| 6 | §7.6 tablosu | **5 satır** | §19/C |
| 7 | Mojibake | §6 içinde `[ÃÂ]` = **0** | §19/D |
| 8 | Envanter iddiası | `57 dosya / 41 test` yeniden sayım tutarlı | §17 |
| 9 | Wiki-link | `wiki-link-check` kökten, gerçek kırık **0** (6 istisna) | §18 + `Satır: 145` |
| 10 | Kapsam dışı yazım | **0** dosya (yalnız bu dosya) | §20/G |
| 11 | `git commit` | **0** (subagent yasağı) | `Satır: 172` |
| 12 | İllüstratif etiket | Python/Go/TS bloklarının üstünde uyarı var | §23-§25 |

> Kapı 1-3 bu dosyanın üretim kapısıdır; kapı 4-12 test sürümünün **koşu** kapılarıdır.

## §29. Yasaklar ve Sınırlar

| # | Yasak | Neden |
|---|---|---|
| 1 | `git commit` atmak | **Orkestratöre aittir** (subagent yasak — `Satır: 172`) |
| 2 | `.ai/*.md`, `.ai/scripts/**`, `shared/**` dosyalarına yazmak | salt-okunur (§10) |
| 3 | Bu dosya **dışına** yeni `.md` üretmek | kapsam dışı yazım = ihlal (§20/G) |
| 4 | FAIL sonrası bekentiyi değiştirmek | sahte PASS (§26/2) |
| 5 | `expected`'i üretim kaynağından **aynı anda** üretmek | test kendini doğrular (§6) |
| 6 | `.env` / credential'ı fixture'a almak | sızıntı (`Dosya: .ai/RAG.md · Satır: 137`) |
| 7 | 6 sahte kırık linki "onarmak" | şablon bozulur (`Satır: 145`) |
| 8 | `.ai/` içinden gate betiği çalıştırmak | 19 sahte sonuç (`Satır: 144`) |
| 9 | Ölçülmeyen sayıyı yazmak | Zero-Hallucination (`Dosya: .ai/RAG.md · Satır: 105`) |
| 10 | Uydurma test dosyası/varmış gibi test kodu üretmek | §22 negatif kanıt |

## §30. Kapanış Kontrol Listesi (her test koşusu sonunda)

| # | Soru | Zorunlu cevap |
|---|---|---|
| 1 | A-G zincirinin 7 adımı da koşuldu mu? | evet |
| 2 | 9 keyword grubunun **hepsi** koşuldu mu (§11.10)? | evet — `9/9` |
| 3 | Golden beklenti bağımsız kaynaktan (vault snapshot) mı okundu? | evet |
| 4 | Retry sayısı ≤ 3 mü? | evet |
| 5 | FAIL varsa sonucu §10'daki doğru yere yazıldı mı? | evet |
| 6 | Regresyon koşusu baseline ile yapıldı mı (§14-§15)? | evet / gerekçesi |
| 7 | Ölçüm yapıldı mı (§19, §20 betikleri)? | evet |
| 8 | Kapsam dışı dosyaya yazıldı mı? | **hayır (0)** |
| 9 | `git commit` atıldı mı? | **hayır (orkestratöre ait)** |
| 10 | Uydurma kanıt / uydurma sayı var mı? | **hayır** |

## §31. Açık Sorular

| # | Soru | Durum |
|---|---|---|
| Q1 | Registry "`shared/tests/` 22 dosya" (`Satır: 629`) ↔ diskte **57 dosya / 41 test** (§17) — hangi tarih geçerli, satır güncellenecek mi? | `⚠️ VERIFICATION REQUIRED` (orkestratör) |
| Q2 | §11'deki 9 senaryo PHPUnit'e taşınacak mı (`shared/tests/` alanı QA Engineer'ındır — §22 "test kodu YOK")? | QA Engineer kararı |
| Q3 | `depends_on: [agent-testing-anayasa]` — kapanış ölçümünde (2026-10-17) `agent-testing/` altında **4 dosya** vardı: `index.md` 48 satır · `agent-testing-anayasa.md` 542 · `agent-testing-ornekler.md` 565 · `agent-testing-surucu.md` 904 (bu dosya). Bağımlılık **karşılanıyor mu** (anayasa ile çelişki var mı)? | bağımlılık mevcut — **çelişki kontrolü `⚠️ VERIFICATION REQUIRED`** |
| Q4 | Ebeveyn `agent-testing/index.md` `children: []` ve "Kademe-3 bu fazda AÇILMADI" diyor (`Satır: 6`) — modül eklendikten sonra `children` güncellenecek mi? | orkestratör kararı |
| Q5 | Repo'da **id-bazlı** routing var (`AIOrchestrator::routeToAgent`, `Satır: 125`) ama §6'nın **keyword-bazlı** routing'ine giden köprü (keyword → ajan id) kodda **görülmedi**. Köprü nerede ve hangi dosyada? | `⚠️ VERIFICATION REQUIRED` — köprü yoksa §6/C4'ün otomatik karşılığı `UNKNOWN`'dır |
| Q6 | N2/N3 çakışma kuralı ("hangi satır kazanır") registry'de **yazılı değil** — test, satır sırasını mı esas alacak? | `⚠️ VERIFICATION REQUIRED` (§13) |
| Q7 | Ajan-routing testi için LLM-judge gerekir mi? (routing exact-match olduğunun kararı) | bu sürüm: **hayır** (§7); tekrar değerlendirilir |

## §32. Kaynaklar (vault içi)

| Kaynak | Yol | Kullanım amacı | Ölçüm |
|---|---|---|---|
| Agent registry (SSOT) | `.ai/AGENTS.md` | §6 routing (Satır: 232-242) · §7.1 (259-267) · §7.6 (300-305) · §7.7 (309-312) · §8 retry (321) · §16 (580) · §17/§18 (197-224) · §25.2 (629) · §25.4 (642) | 775 satır |
| Doğrulama betiği | `.ai/scripts/wiki-link-check.ps1` | 3 kademeli eşleşme + kırık raporu (§18) | **43 satır** (tamamı okundu) |
| Gate betikleri (kökten) | `.ai/scripts/*.ps1` (6 dosya) | `screens-frontmatter-check` · `kalip-abc-check` · `figma-tokens` · `device-matrix-catid` · `figma-extract` | `Satır: 144` |
| Test envanteri | `shared/tests/` | §17 sayımı | 57 dosya / 41 test / 19 dizin |
| Test yapılandırması | `shared/phpunit.xml` | 8 testsuite + `source/include=src` | 39 satır |
| Paket/test scripti | `shared/composer.json` | `phpunit ^10.5` · `test: phpunit` | Satır: 24, 47 |
| PHP test kanıtı | `shared/tests/Middleware/CsrfMiddlewareTest.php` | setUp/tearDown + assert kalıbı (§21) | Satır: 1-47 |
| PHP routing kanıtı | `shared/tests/Api/GatewayMethodRoutingTest.php` | HTTP route testi (§21) | Satır: 12-18, 33-39 |
| Ajan routing motoru (repo) | `shared/src/AI/AIOrchestrator.php` | `AGENT_MAP` 11 ajan (26-37) · `TASK_TYPES` (40-48) · `routeToAgent` (125-133) | 210 satır |
| Boot zinciri | `AGENTS.md` (kök) §14 | fixture boot durumu (Satır: 273-282) | 285 satır |
| Boot protokolü | `.ai/CLAUDE.md` §16 | 13 = 14 − 1 (Satır: 630, 648) | 987 satır |
| Retrieval kuralları | `.ai/RAG.md` §3.2/4 (Satır: 105) · §5 (136) | ölçüm disiplini + boot değişmezliği | 177 satır |
| Ebeveyn düğüm | `.ai/architecture/K015-AI/agent-testing/index.md` | kapsam sınırı + `children` durumu (Satır: 6, 14, 27) | 48 satır |
| Yapı referansı | `.ai/architecture/K015-AI/rag/rag-surucu.md` | H1 + § + karar ağacı + kapı iskeleti | 779 satır |

## §33. Web Doğrulama Kaynakları (1-3 resmi)

| # | Kaynak | URL | Doğrulanan iddia | Doğrulama |
|---|---|---|---|---|
| 1 | **Langfuse — LLM Evaluation: Methods, Best Practices, and a Practical Roadmap** | `https://langfuse.com/blog/2025-11-12-evals` | (a) offline eval = **regresyonu** bilinen test örneklerinde yakalar; online eval drift'i yakalar. (b) **code evaluator** = deterministik kontrol (exact match, regex, tool-call assertion), maliyeti "near zero". (c) CI kapısı: skor eşiğin altına düşerse job **fail** olur (`RegressionError`). (d) Ajan değerlendirmesi son çıktıya değil **trajectory**'ye bakar (doğru araç/argüman seçimi). | ✅ `webfetch` — 2026-10-17 sayfa metni okundu |
| 2 | **Arize — The definitive guide to LLM evaluations** | `https://arize.com/resources/llm-evaluation` | (a) değerlendirme kapsamı **routing, tool calls, guardrails, latency, cost** dahildir. (b) **regression suite** = her değişiklikte CI'da koşan sabit set. (c) release kararı tek skora değil **tekrarlı koşuya ve güven aralığına** dayanır (flaky uyarı kaynağı). (d) "eval'ler sigorta değildir; her eval bir **karar** korumalıdır" (smoke vs deep ayrımı). | ✅ `webfetch` — 2026-10-17 sayfa metni okundu |
| 3 | *Araştırma düzeyi (fetch yok)* — Evaluation and Benchmarking of LLM Agents: A Survey | `https://arxiv.org/html/2507.21504v1` | Ajan benchmark'larının otomatik metrikler (AST correctness, Win Rate) ve standart test setleri kullandığı (arama özeti) | ⚠️ **VERIFICATION REQUIRED** — sayfa tam okunmadı, yalnız arama sonucu |

**Vault ile uyum:** Langfuse "code evaluator + exact match" önerisi bu sürümün §7'sindeki
"deterministik bool metrik, LLM-judge yok" kararıyla **aynı**dır; Arize'ın "regression suite
her değişiklikte CI'da" önerisi §14-§15 regresyon bölümüyle örtüşür. **Çelişki yok.**

## §34. Gate (özet)

§1-§34 tamam: ELI15 · kapsam · A-G akışı (7 adım) · senaryo üretim karar ağacı (6 satır) ·
fixture 6 parça · çalıştırma 5 adım · 7 metrikli ölçüm · 8 satırlık PASS/FAIL + §7.6
karşılığı · retry 5 satır · raporlama 6 sınıf · **9 keyword grubu 9 senaryo tablosu** +
9/9 özet · 5 adımlı doğrulama · 7 negatif senaryo · regresyon 7 sınıf + **§25.4 boot farkı
örneği** (14/20/17/13) + 6 sınıflı değişim tablosu · `shared/tests/` envanteri (57/41/19) ·
PowerShell **7 blok** (wiki-link-check gerçek alıntı 3 · routing kalıbı B-E 3 · üretim kapısı F-G 1) ·
PHP **4 blok** (gerçek alıntı, künyeli — 3 test + `AGENT_MAP`/`routeToAgent` kanıtı) ·
illüstratif **3 blok** (Python/Go/TS) · hata modu **10** ·
token kuralı **8** · kapı **12** · yasak **10** · kapanış **10** · açık soru **7** ·
kaynak **14** · web kaynak **3** (2 doğrulanmış + 1 ⚠️).

**Kapılar:** satır `>= 500` · FM **10 alan** · Authority son satır birebir · kapsam dışı
yazım **0** · `git commit` **0** · routing koşusu **9/9**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
